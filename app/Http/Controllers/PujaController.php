<?php

namespace App\Http\Controllers;

use App\Models\ActionArea;
use App\Models\Attendance;
use App\Models\PujaCategorie;
use App\Models\PujaCommittee;
use App\Models\PujaCommitteeRepo;
use App\Services\SmsService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class PujaController extends Controller
{
    public function data()
    {
        $out = DataTables::of(PujaCommittee::query())
            ->editColumn('proposed_immersion_date', function ($row) {
                return $row->proposed_immersion_date
                    ? Carbon::parse($row->proposed_immersion_date)->format('d/m/Y')
                    : null;
            })
            ->editColumn('proposed_immersion_time', function ($row) {
                return $row->proposed_immersion_time
                    ? Carbon::parse($row->proposed_immersion_time)->format('h:i A')
                    : null;
            })
            ->editColumn('stat', function ($row) {
                return statDict()[$row->stat] ?? $row->stat;
            })
            ->make(true);
        return $out;
    }

    public function get($id)
    {
        $puja = PujaCommittee::find($id);
        if (!$puja) return $this->err("No Such Puja");
        return $this->ok("Puja Detail", ["data" => $puja]);
    }
	
	public function form_validate(Request $request) {
		if($request->query('puja_committee_name')) {
			$nm = $request->query('puja_committee_name');
			if($nm != "Other") {
				$exists = PujaCommittee::where('puja_committee_name', $nm)->first();
				if($exists) return response()->json("❌ This puja committee is already registered");
			}
		}
		else if($request->query('puja_committee_name_other')) {
			$nm = $request->query('puja_committee_name_other');
			if($nm) {
				$exists = PujaCommittee::where('puja_committee_name', $nm)->first();
				if($exists) return response()->json("❌ This puja committee is already registered");
			}
		}
		else if($request->query('puja_committee_name_text')) {
			$nm = $request->query('puja_committee_name_text');
			if($nm) {
				$exists = PujaCommittee::where('puja_committee_name', $nm)->first();
				if($exists) return response()->json("❌ This puja committee is already registered");
			}
		}
		else if($request->query('secretary_mobile')) {
			$nm = $request->query('secretary_mobile');
			if($nm) {
				$exists = PujaCommittee::where('secretary_mobile', $nm)->first();
				if($exists) return response()->json("❌ This Secretary Mobile is already taken");
			}
		}
		else if($request->query('chairman_mobile')) {
			$nm = $request->query('chairman_mobile');
			if($nm) {
				$exists = PujaCommittee::where('chairman_mobile', $nm)->first();
				if($exists) return response()->json("❌ This Chairman Mobile is already taken");
			}
		}
		return response("true");
	}

    public function add(Request $request,SmsService $sms)
    {
		//Log::info("aaa",["data"=>$request->all()]);
        $rules = [
            'puja_committee_name'   => 'required|string|min:3|max:100|unique:puja_committees,puja_committee_name',
            'puja_committee_address' => 'nullable|string|min:3|max:200',
            'secretary_name'        => 'required|string|min:3|max:100',
            'secretary_mobile'      => 'required|string|min:8|max:20|unique:puja_committees,secretary_mobile',
            'chairman_name'         => 'required|string|min:3|max:100',
            'chairman_mobile'       => 'required|string|min:8|max:20|unique:puja_committees,chairman_mobile',
            'proposed_immersion_date' => 'required|date',
            'proposed_immersion_time' => 'required|string',
            'no_of_vehicles'            => 'nullable|integer|min:1|max:3',
            'vehicle_no'            => 'nullable|string|min:3|max:50',
            'team_members'          => 'nullable|integer|min:1|max:100',
			'secretary_mobile_otp'     => 'required|string|size:6',
			'chairman_mobile_otp'      => 'required|string|size:6',
        ];
		if ($request->in_newtown) {
			if (strtolower($request->input('puja_committee_name')) === 'other') {
				$nm = $request->input('puja_committee_name_other');
			} else {
				$nm = $request->input('puja_committee_name');
			}
		} else {
			$nm = $request->input('puja_committee_name_text');
		}
		$request->merge([
			'puja_committee_name' => $nm
		]);
		//Log::info("xxx",["data"=>$request->all()]);
        $err = $this->validate($request->all(), $rules);
        if ($err) return $err;

		// ✅ Validate secretary OTP
		$secretaryOtpSession = session("secretary_mobile_otp");
		if (!$secretaryOtpSession) {
			return $this->err("Secretary OTP not verified. Please send and verify OTP first.");
		}
		if ($secretaryOtpSession['mobile'] != $request->secretary_mobile) {
			return $this->err("Secretary mobile number does not match.");
		}
		if ($request->secretary_mobile_otp != $secretaryOtpSession['otp']) {
			return $this->err("Invalid Secretary OTP.");
		}
		if ($secretaryOtpSession['time'] && now()->diffInMinutes($secretaryOtpSession['time']) > 5) {
			return $this->err("Secretary OTP expired. Please resend OTP.");
		}

		// ✅ Validate chairman OTP
		$chairmanOtpSession = session("chairman_mobile_otp");
		if (!$chairmanOtpSession) {
			return $this->err("Chairman OTP not verified. Please send and verify OTP first.");
		}
		if ($chairmanOtpSession['mobile'] != $request->chairman_mobile) {
			return $this->err("Chairman mobile number does not match.");
		}
		if ($request->chairman_mobile_otp != $chairmanOtpSession['otp']) {
			return $this->err("Invalid Chairman OTP.");
		}
		if ($chairmanOtpSession['time'] && now()->diffInMinutes($chairmanOtpSession['time']) > 5) {
			return $this->err("Chairman OTP expired. Please resend OTP.");
		}

        $pujaData = [
            'action_area'           => $request->in_newtown ? $request->action_area : null,
            'category'              => $request->in_newtown ? $request->category : null,
            'puja_committee_name'   => $request->puja_committee_name,
            'puja_committee_address' => $request->puja_committee_address,
            'secretary_name'        => $request->secretary_name,
            'secretary_mobile'      => $request->secretary_mobile,
            'chairman_name'         => $request->chairman_name,
            'chairman_mobile'       => $request->chairman_mobile,
            'proposed_immersion_date' => $request->proposed_immersion_date,
            'proposed_immersion_time' => $request->proposed_immersion_time,
            'no_of_vehicles'        => $request->no_of_vehicles,
            'vehicle_no'            => $request->vehicle_no,
            'team_members'          => $request->dhunuchi ? $request->team_members : null,
            'stat'                  => 'a', 
        ];

        $puja = PujaCommittee::create($pujaData);
		$this->smsLink($puja->token);
        if(setting('NKDA_MOBS')) {
            $sms->send(explode("~~",setting('NKDA_MOBS')),"New puja committee ($nm) registered.");
        }
		
        $actionArea = ActionArea::where('name', $request->action_area)->first();
        $category   = PujaCategorie::where('name', $request->category)->first();
		if ($request->in_newtown) {
			if (strtolower($request->input('puja_committee_name')) === 'other') {
                // ✅ Insert into Repo if not exists already
                if ($actionArea && $category) {
                    // Insert into Repo if not exists
                    $exists = PujaCommitteeRepo::where('name', $nm)->first();
                    if (!$exists) {
                        PujaCommitteeRepo::create([
                            'action_area_id'   => $actionArea->id,
                            'puja_category_id' => $category->id,
                            'name'             => $nm,
                            'puja_address'     => $request->puja_committee_address,
                        ]);
                    }
                }
			} else {
                // always update address if given
                if ($request->filled('puja_committee_address')) {
                    $repo = PujaCommitteeRepo::firstOrNew([
                        'name'             => $nm,
                        'action_area_id'   => $actionArea->id,
                        'puja_category_id' => $category->id,
                    ]);
                    $repo->puja_address = $request->puja_committee_address;
                }
                $repo->save();

            }
		} 
		
		session()->forget(['secretary_mobile_otp', 'chairman_mobile_otp']);
        return $this->ok('Registration Successful',["data"=>$puja->token]);
    }

    
    public function addadmin(Request $request)
    {
		//Log::info("aaa",["data"=>$request->all()]);
        $rules = [
            'puja_committee_name'   => 'required|string|min:3|max:100|unique:puja_committees,puja_committee_name',
            'puja_committee_address' => 'nullable|string|min:3|max:200',
            'secretary_name'        => 'nullable|string|min:3|max:100',
            'secretary_mobile'      => 'required|string|min:8|max:20|unique:puja_committees,secretary_mobile',
            'chairman_name'         => 'nullable|string|min:3|max:100',
            'chairman_mobile'       => 'nullable|string|min:8|max:20|unique:puja_committees,chairman_mobile',
            'proposed_immersion_date' => 'required|date',
            'proposed_immersion_time' => 'nullable|string',
            'no_of_vehicles'            => 'nullable|integer|min:1|max:3',
            'vehicle_no'            => 'nullable|string|min:3|max:50',
            'team_members'          => 'nullable|integer|min:1|max:100',
			'secretary_mobile_otp'     => 'required|string|size:6',
			'chairman_mobile_otp'      => 'nullable|string|size:6',
        ];
		if ($request->in_newtown) {
			if (strtolower($request->input('puja_committee_name')) === 'other') {
				$nm = $request->input('puja_committee_name_other');
			} else {
				$nm = $request->input('puja_committee_name');
			}
		} else {
			$nm = $request->input('puja_committee_name_text');
		}
		$request->merge([
			'puja_committee_name' => $nm
		]);
		//Log::info("xxx",["data"=>$request->all()]);
        $err = $this->validate($request->all(), $rules);
        if ($err) return $err;

		// ✅ Validate secretary OTP
		$secretaryOtpSession = session("secretary_mobile_otp");
		if (!$secretaryOtpSession) {
			return $this->err("Secretary OTP not verified. Please send and verify OTP first.");
		}
		if ($secretaryOtpSession['mobile'] != $request->secretary_mobile) {
			return $this->err("Secretary mobile number does not match.");
		}
		if ($request->secretary_mobile_otp != $secretaryOtpSession['otp']) {
			return $this->err("Invalid Secretary OTP.");
		}
		if ($secretaryOtpSession['time'] && now()->diffInMinutes($secretaryOtpSession['time']) > 5) {
			return $this->err("Secretary OTP expired. Please resend OTP.");
		}

		if($request->chairman_mobile) {
			// ✅ Validate chairman OTP
			$chairmanOtpSession = session("chairman_mobile_otp");
			if (!$chairmanOtpSession) {
				return $this->err("Chairman OTP not verified. Please send and verify OTP first.");
			}
			if ($chairmanOtpSession['mobile'] != $request->chairman_mobile) {
				return $this->err("Chairman mobile number does not match.");
			}
			if ($request->chairman_mobile_otp != $chairmanOtpSession['otp']) {
				return $this->err("Invalid Chairman OTP.");
			}
			if ($chairmanOtpSession['time'] && now()->diffInMinutes($chairmanOtpSession['time']) > 5) {
				return $this->err("Chairman OTP expired. Please resend OTP.");
			}
		}

        $pujaData = [
            'action_area'           => $request->in_newtown ? $request->action_area : null,
            'category'              => $request->in_newtown ? $request->category : null,
            'puja_committee_name'   => $request->puja_committee_name,
            'puja_committee_address' => $request->puja_committee_address,
            'secretary_name'        => $request->secretary_name,
            'secretary_mobile'      => $request->secretary_mobile,
            'chairman_name'         => $request->chairman_name,
            'chairman_mobile'       => $request->chairman_mobile,
            'proposed_immersion_date' => $request->proposed_immersion_date,
            'proposed_immersion_time' => $request->proposed_immersion_time,
            'no_of_vehicles'        => $request->no_of_vehicles,
            'vehicle_no'            => $request->vehicle_no,
            'team_members'          => $request->dhunuchi ? $request->team_members : null,
            'stat'                  => 'a', 
        ];

        $puja = PujaCommittee::create($pujaData);
		$this->smsLink($puja->token);
		
        $actionArea = ActionArea::where('name', $request->action_area)->first();
        $category   = PujaCategorie::where('name', $request->category)->first();
		if ($request->in_newtown) {
			if (strtolower($request->input('puja_committee_name')) === 'other') {
                // ✅ Insert into Repo if not exists already
                if ($actionArea && $category) {
                    // Insert into Repo if not exists
                    $exists = PujaCommitteeRepo::where('name', $nm)->first();
                    if (!$exists) {
                        PujaCommitteeRepo::create([
                            'action_area_id'   => $actionArea->id,
                            'puja_category_id' => $category->id,
                            'name'             => $nm,
                            'puja_address'     => $request->puja_committee_address,
                        ]);
                    }
                }
			} else {
                // always update address if given
                if ($request->filled('puja_committee_address')) {
                    $repo = PujaCommitteeRepo::firstOrNew([
                        'name'             => $nm,
                        'action_area_id'   => $actionArea->id,
                        'puja_category_id' => $category->id,
                    ]);
                    $repo->puja_address = $request->puja_committee_address;
                }
                $repo->save();

            }
		} 
		
        return $this->ok('Registration Successful',["data"=>$puja->token]);
    }


    public function updateadmin(Request $request, $id)
    {
        //Log::info("ccc",["data"=>$request->all()]);
        $puja = PujaCommittee::find($id);
        if (!$puja) return $this->err("No Such Puja");
		
		if ($request->in_newtown) {
			if (strtolower($request->input('puja_committee_name')) === 'other') {
				$nm = $request->input('puja_committee_name_other');
			} else {
				$nm = $request->input('puja_committee_name');
			}
		} else {
			$nm = $request->input('puja_committee_name_text');
		}
		$request->merge([
			'puja_committee_name' => $nm
		]);

        $err = $this->validate($request->all(), [
            'puja_committee_name'   => 'required|string|min:3|max:100|unique:puja_committees,puja_committee_name,' . $id,
            'puja_committee_address' => 'nullable|string|min:3|max:200',
            'secretary_name'        => 'nullable|string|min:3|max:100',
            'secretary_mobile'      => 'required|string|min:8|max:20|unique:puja_committees,secretary_mobile,' . $id,
            'chairman_name'         => 'nullable|string|min:3|max:100',
            'chairman_mobile'       => 'nullable|string|min:8|max:20|unique:puja_committees,chairman_mobile,' . $id,
            'proposed_immersion_date' => 'required|date',
            'proposed_immersion_time' => 'nullable|string',
            'no_of_vehicles'            => 'nullable|integer|min:1|max:3',
            'vehicle_no'            => 'nullable|string|min:3|max:50',
            'team_members'          => 'nullable|integer|min:1|max:100',
			'secretary_mobile_otp'     => 'nullable|string|size:6',
			'chairman_mobile_otp'      => 'nullable|string|size:6',
        ]);
        if ($err) return $err;

		// ✅ Validate secretary OTP
		if($request->secretary_mobile && $puja->secretary_mobile !=$request->secretary_mobile) {
			$secretaryOtpSession = session("secretary_mobile_otp");
			if (!$secretaryOtpSession) {
				return $this->err("Secretary OTP not verified. Please send and verify OTP first.");
			}
			if ($secretaryOtpSession['mobile'] != $request->secretary_mobile) {
				return $this->err("Secretary mobile number does not match.");
			}
			if ($request->secretary_mobile_otp != $secretaryOtpSession['otp']) {
				return $this->err("Invalid Secretary OTP.");
			}
			if ($secretaryOtpSession['time'] && now()->diffInMinutes($secretaryOtpSession['time']) > 5) {
				return $this->err("Secretary OTP expired. Please resend OTP.");
			}
		}

		if($request->chairman_mobile && $puja->chairman_mobile !=$request->chairman_mobile) {
			// ✅ Validate chairman OTP
			$chairmanOtpSession = session("chairman_mobile_otp");
			if (!$chairmanOtpSession) {
				return $this->err("Chairman OTP not verified. Please send and verify OTP first.");
			}
			if ($chairmanOtpSession['mobile'] != $request->chairman_mobile) {
				return $this->err("Chairman mobile number does not match.");
			}
			if ($request->chairman_mobile_otp != $chairmanOtpSession['otp']) {
				return $this->err("Invalid Chairman OTP.");
			}
			if ($chairmanOtpSession['time'] && now()->diffInMinutes($chairmanOtpSession['time']) > 5) {
				return $this->err("Chairman OTP expired. Please resend OTP.");
			}
		}

		$oldSecretaryMobile = $puja->secretary_mobile;
		$oldChairmanMobile  = $puja->chairman_mobile;

        $puja->action_area            = $request->in_newtown ? $request->action_area : null;
        $puja->category               = $request->in_newtown ? $request->category : null;
        $puja->puja_committee_name    = $request->puja_committee_name;
        $puja->puja_committee_address = $request->puja_committee_address;
        $puja->secretary_name         = $request->secretary_name;
        $puja->secretary_mobile       = $request->secretary_mobile;
        $puja->chairman_name          = $request->chairman_name;
        $puja->chairman_mobile        = $request->chairman_mobile;
        $puja->proposed_immersion_date = $request->proposed_immersion_date;
        $puja->proposed_immersion_time = $request->proposed_immersion_time;
        $puja->no_of_vehicles          = $request->no_of_vehicles;
        $puja->vehicle_no             = $request->vehicle_no;
        $puja->team_members           = $request->dhunuchi ? $request->team_members : null;
        $puja->save();

		if (($oldSecretaryMobile !== $request->secretary_mobile) 
		|| ($oldChairmanMobile !== $request->chairman_mobile)) 
			$this->smsLink($puja->token);

        $actionArea = $request->in_newtown
            ? ActionArea::where('name', $request->action_area)->first()
            : null;

        $category = $request->in_newtown
            ? PujaCategorie::where('name', $request->category)->first()
            : null;
            
        if ($request->in_newtown && $actionArea && $category) {
            $repo = PujaCommitteeRepo::firstOrNew([
                'name'             => $nm,
                'action_area_id'   => $actionArea->id,
                'puja_category_id' => $category->id,
            ]);
            // always update address if given
            if ($request->filled('puja_committee_address')) {
                $repo->puja_address = $request->puja_committee_address;
            }
            $repo->save();
        }

        return $this->ok('Puja Saved Successfully');
    }

	public function thanks($token) 
	{
        $puja = PujaCommittee::where('token', $token)->first();
        if (!$puja) abort(404, 'INVALID puja');
		return view("thanks",compact("puja"));
	}
	
    public function delete($id)
    {
        $puja = PujaCommittee::find($id);
        if (!$puja) return $this->err("No Such Puja");
        $puja->delete();
        return $this->ok('Puja deleted successfully');
    }

    private function qrGen($file, $tok)
    {
        //if (file_exists($file)) return;
        $qrCode = new QrCode($tok);
        $writer = new PngWriter();
        $result = $writer->write($qrCode);
        $result->saveToFile($file);
    }

    public function gpass($token)
    {
        $puja = PujaCommittee::where('token', $token)->first();
        if (!$puja) abort(404, 'INVALID puja');
        $path = public_path('qrs');
        if (!file_exists($path)) mkdir($path, 0755, true);
        $file = $path . '/' . $puja->token . '.png';
        $this->qrGen($file, $puja->secretary_mobile);
        return view('puja.gatepass', compact('puja'));
    }

    public function downloadPdf($token)
    {
        $puja = PujaCommittee::where('token', $token)->first();
        if (!$puja) abort(404, 'Puja not found');
        $file = public_path("qrs/{$puja->token}.png");
        $this->qrGen($file, $puja->secretary_mobile);
        $pdf = Pdf::loadView('puja.gatepass-pdf', compact('puja', 'file'))
            ->setPaper([0,0,297,420],'landscape');// A4 quarter = A6
        return $pdf->download("{$puja->secretary_mobile}.pdf");
    }
	
	public function sendSmsToPuja($puja,$msg) 
	{
		$sms = new SmsService;
		$mob = [];
		if($puja->secretary_mobile) $mob[] = $puja->secretary_mobile;
		if($puja->chairman_mobile) $mob[] = $puja->chairman_mobile;
		$ans = $sms->send($mob,$msg);
		return [$ans, $mob];
	}
	
	public function smsLink($token)
	{
        $puja = PujaCommittee::where('token', $token)->first();
        if (!$puja) return $this->err("No Such Puja");
		$link = route('puja.gpass.pdf', ['token' => $puja->token]);
		list($ans,$mob) = $this->sendSmsToPuja(
			$puja, "Please Download the Digital Pass from $link."
		);
		if($ans['success']) {
			return $this->ok("Sent download link to ". implode(", ",$mob) ." through SMS.");
		} else {
			return $this->err("Failed to send SMS: ".$ans['message']);
		}
	}
	
	public function send_otp(Request $request,SmsService $sms)
	{
        $err = $this->validate($request->all(), [
			'nm'     => ['required','string'],
			'mobile' => ['required','regex:/^[6-9]\d{9}$/'],
        ], [
			'nm.required'     => 'Field identifier is missing.',
			'mobile.required' => 'Mobile number is required.',
			'mobile.regex'    => 'Please enter a valid 10-digit Indian mobile number.',
		]);
        if ($err) return $err;
		
		$nm  = $request->nm;
		$mobile = $request->mobile;
		$countKey = "{$nm}_{$mobile}_otp_count";
		$count    = session($countKey, 0);
		if ($count >= 3) {
			return $this->err("You have reached the maximum limit of 3 OTP requests.");
		}
		
		$otp = rand(100000, 999999);

		try {
			$sms->send($mobile, "Your OTP is $otp");
			session()->put("{$nm}_otp", [
				'mobile' => $mobile,
				'otp'    => $otp,
				'time'   => now(),
			]);
			session()->put($countKey, $count + 1);
			return $this->ok("An OTP $otp has been sent to your mobile number ending with " 
			. substr($mobile, -4) . ".");
		} catch (\Exception $e) {
			return $this->err("Failed to send OTP right now");
		}
	}

    public function has_entryslip($id) 
    {
        $puja = PujaCommittee::find($id);
        if (!$puja) return $this->err("No Such Puja");
        $repoAtt = Attendance::where('puja_committee_id', $puja->id)
            ->where('typ', 'in')->first();
        if (!$repoAtt) return $this->err("Not scanned by operator post yet");
        return $this->ok("Ok",["data"=>$puja->secretary_mobile,"repoAtt"=>$repoAtt]);
    }

    public function entryslip($id) 
    {
        $puja = PujaCommittee::where('secretary_mobile', $id)->first();
        if (!$puja) abort(404, 'Puja not found');
        $repoAtt = Attendance::where('puja_committee_id', $puja->id)
            ->where('typ', 'in')->first();
        if (!$repoAtt) abort(404, 'Not scanned by operator post yet');
        $file = public_path("qrs/{$puja->id}.png");
        $this->qrGen($file, $puja->secretary_mobile);
        return view("puja.entryslip", compact('puja', 'file', 'repoAtt'));
    }

    public function scan()
    {
        $puja = $this->getUserObj();
        return view('puja.scan', compact('puja'));
    }

	/*
	Following methods should not rely on $request, session, or middleware, 
	because this is scheduler run which is outside of HTTP context.
	*/
	public function sendPujaReminders1()
	{
        $sms = new SmsService;
        $today = Carbon::today()->toDateString();

        $committees = PujaCommittee::whereDate('proposed_immersion_date', $today)
            ->where(function ($q) {
                $q->whereNotNull('secretary_mobile')
                  ->orWhereNotNull('chairman_mobile');
            })
            ->where('reminder_typ', 0)
            ->where('reminder_cnt', '<', 3)
            ->get();

        foreach ($committees as $c) {
            try {
                // Collect mobiles
                $mobiles = array_filter([
                    $c->secretary_mobile,
                    $c->chairman_mobile,
                ]);

                // ---- Send SMS here ----
                $message = "Dear {$c->puja_committee_name}, "
                    ."please remember your immersion today at {$c->proposed_immersion_time}. "
                    ."Wishing you a safe and happy event.";
                
                $ok = $sms->send($mobiles, $message);

                // If sent successfully
                if ($ok['success']) {
                    $c->update([
                        'reminder_typ' => 1,  // mark as second reminder done
                        'reminder_cnt' => 0,
                    ]);
                } else {
                    // Retry count will increase anyway
                    $c->increment('reminder_cnt');
                    Log::warning("Reminder1 SMS failed for {$c->puja_committee_name}");
                }
            } catch (\Exception $e) {
                $c->increment('reminder_cnt');
                Log::error("Reminder1 SMS error for {$c->puja_committee_name}: ".$e->getMessage());
            }
        }        
	}

    public function sendPujaReminders2()
    {
        $sms = new SmsService;
        $now = Carbon::now('Asia/Kolkata');
        $from = $now;                       // current time
        $to   = $now->copy()->addHours(2);  // next 2 hours

        $committees = PujaCommittee::whereDate('proposed_immersion_date', Carbon::today())
            ->where(function ($q) {
                $q->whereNotNull('secretary_mobile')
                ->orWhereNotNull('chairman_mobile');
            })
            ->where('reminder_typ', 1)
            ->where('reminder_cnt', '<', 3)
            ->whereBetween('proposed_immersion_time', [$from->toTimeString(), $to->toTimeString()])
            ->get();

        foreach ($committees as $c) {
            try {
                $mobiles = array_filter([
                    $c->secretary_mobile,
                    $c->chairman_mobile,
                ]);

                $message = "Dear {$c->puja_committee_name}, "
                    ."your idol immersion is scheduled at {$c->proposed_immersion_time} today. "
                    ."This is a gentle reminder from NKDA. "
                    ."Wishing you a safe and happy event.";

                $ok = $sms->send($mobiles, $message);

                if ($ok['success']) {
                    $c->update([
                        'reminder_typ' => 2,  // mark as second reminder done
                        'reminder_cnt' => 0,
                    ]);
                } else {
                    $c->increment('reminder_cnt');
                    Log::warning("Reminder2 SMS failed for {$c->puja_committee_name}");
                }
            } catch (\Exception $e) {
                $c->increment('reminder_cnt');
                Log::error("Reminder2 SMS error for {$c->puja_committee_name}: ".$e->getMessage());
            }
        }
    }


}
