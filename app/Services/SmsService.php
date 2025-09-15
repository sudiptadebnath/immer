<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    protected $apiUrl;
    protected $apiKey;
    protected $sender;

    public function __construct()
    {
        $this->apiUrl = config('services.sms.url');
        $this->apiKey = config('services.sms.key');
        $this->sender = config('services.sms.sender');
    }

	public function send($to, $message)
	{
		$recipients = is_array($to) ? $to : [$to];
		
		Log::info('SMS >>', [
			'to'      => $recipients,
			'message' => $message,
		]);

		/* Example real call:
		$response = Http::post($this->apiUrl, [
			'api_key' => $this->apiKey,
			'sender'  => $this->sender,
			'to'      => implode(',', $recipients), // if provider expects comma-separated
			'message' => $message,
		]);
		return $response->json();
		*/

		// Fake response for now
		return [
			'success'  => true,
			'to'      => $recipients,
			'message' => $message,
			'id'      => uniqid('sms_'),
			'debug'   => true,
		];
	}
}
