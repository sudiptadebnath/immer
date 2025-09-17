<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class SmsService
{
    protected $apiUrl;

	private $templateMessages = [
		"98656" => "Your Puja Immersion has been successfully completed. Wishing you a joyous Shubho Bijoya. – NKDAWB",
		"98657" => "An OTP F1 has been sent to your mobile number ending with F2 . NKDAWB",
		"98658" => "Number of Puja Committee Registrations: F1 . NKDAWB",
	];

    public function __construct()
    {
        $this->apiUrl = config('services.sms.url');
    }

	public function send($numbers, $TempId, ...$fields)
	{
		$numbers = is_array($numbers) ? $numbers : [$numbers];
		$request = [
			'TempId'      => $TempId,
			'phonenumber' => $numbers
		];
		foreach ($fields as $index => $value) {
			$request['F' . ($index + 1)] = $value;
		}
		if($this->templateMessages[$TempId] ?? "") {
			$params = http_build_query($request);
			$fullUrl = $this->apiUrl . '&' . $params;
			$raw_response = file_get_contents($fullUrl);
			$response = [
				"success"=>stripos($raw_response, 'ok') !== false,
				"message"=>$raw_response,
			];
			$request["message"]=$this->templateMessages[$TempId];
		} else {
			$request["message"]=$TempId;
			$response = [
				"success"=>true,
				"message"=>"Template not supported",
			];
		}
		app_log('system.SMS',json_encode($request),json_encode($response));
		Log::info('SMS >>', [
			'request' => $request,
			'response' => $response,
		]);
		return $response;
	}
}
