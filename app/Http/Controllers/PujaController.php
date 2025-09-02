<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\PujaCommittee;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

class PujaController extends Controller
{
    public function data()
    {
        $query = PujaCommittee::orderBy('created_at', 'desc');
        $data = $query->get()->map(function ($row) {
            $row['stat'] = statDict()[$row->stat] ?? $row->stat;
            return $row;
        });
        return DataTables::of($data)->make(true);
    }

    public function get($id)
    {
        $puja = PujaCommittee::find($id);
        if (!$puja) return $this->err("No Such Puja");
        return $this->ok("Puja Detail", ["data" => $puja]);
    }

    public function add(Request $request)
    {
        $rules = [
            'puja_committee_name'   => 'required|string|min:3|max:100|unique:puja_committees,puja_committee_name',
            'puja_committee_address' => 'nullable|string|min:3|max:200',
            'secretary_name'        => 'required|string|min:3|max:100',
            'secretary_mobile'      => 'required|string|min:8|max:20|unique:puja_committees,secretary_mobile',
            'chairman_name'         => 'required|string|min:3|max:100',
            'chairman_mobile'       => 'required|string|min:8|max:20|unique:puja_committees,chairman_mobile',
            'proposed_immersion_date' => 'required|date',
            'proposed_immersion_time' => 'required|string',
            'vehicle_no'            => 'nullable|string|min:3|max:50',
            'team_members'          => 'nullable|integer|min:1|max:100',
        ];
        $nm = $request->input('puja_committee_name') 
            ?? $request->input('puja_committee_name_other') 
            ?? $request->input('puja_committee_name_text');
        $request->merge([
            'puja_committee_name' => $nm
        ]);

        $err = $this->validate($request->all(), $rules);
        if ($err) return $err;

        $pujaData = [
            'action_area'           => $request->in_newtown ? $request->action_area : null,
            'category'              => $request->in_newtown ? $request->category : null,
            'puja_committee_name'   => $request->puja_committee_name,
            'puja_committee_address' => $request->in_newtown ? null : $request->puja_committee_address,
            'secretary_name'        => $request->secretary_name,
            'secretary_mobile'      => $request->secretary_mobile,
            'chairman_name'         => $request->chairman_name,
            'chairman_mobile'       => $request->chairman_mobile,
            'proposed_immersion_date' => $request->proposed_immersion_date,
            'proposed_immersion_time' => $request->proposed_immersion_time,
            'vehicle_no'            => $request->vehicle_no,
            'team_members'          => $request->dhunuchi ? $request->team_members : null,
            'stat'                  => 'a', 
        ];

        PujaCommittee::create($pujaData);
        return $this->ok('Registration Successful');
    }

    public function update(Request $request, $id)
    {
        $puja = PujaCommittee::find($id);
        if (!$puja) return $this->err("No Such Puja");
        $nm = $request->input('puja_committee_name') 
            ?? $request->input('puja_committee_name_other') 
            ?? $request->input('puja_committee_name_text');
        $request->merge([
            'puja_committee_name' => $nm
        ]);
        $err = $this->validate($request->all(), [
            'puja_committee_name'   => 'required|string|min:3|max:100|unique:puja_committees,puja_committee_name,' . $id,
            'puja_committee_address' => 'nullable|string|min:3|max:200',
            'secretary_name'        => 'required|string|min:3|max:100',
            'secretary_mobile'      => 'required|string|min:8|max:20|unique:puja_committees,secretary_mobile,' . $id,
            'chairman_name'         => 'required|string|min:3|max:100',
            'chairman_mobile'       => 'required|string|min:8|max:20|unique:puja_committees,chairman_mobile,' . $id,
            'proposed_immersion_date' => 'required|date',
            'proposed_immersion_time' => 'required|string',
            'vehicle_no'            => 'nullable|string|min:3|max:50',
            'team_members'          => 'nullable|integer|min:1|max:100',
        ]);
        if ($err) return $err;

        $puja->action_area            = $request->in_newtown ? $request->action_area : null;
        $puja->category               = $request->in_newtown ? $request->category : null;
        $puja->puja_committee_name    = $request->puja_committee_name;
        $puja->puja_committee_address = $request->in_newtown ? null : $request->puja_committee_address;
        $puja->secretary_name         = $request->secretary_name;
        $puja->secretary_mobile       = $request->secretary_mobile;
        $puja->chairman_name          = $request->chairman_name;
        $puja->chairman_mobile        = $request->chairman_mobile;
        $puja->proposed_immersion_date = $request->proposed_immersion_date;
        $puja->proposed_immersion_time = $request->proposed_immersion_time;
        $puja->vehicle_no             = $request->vehicle_no;
        $puja->team_members           = $request->dhunuchi ? $request->team_members : null;
        $puja->save();
        return $this->ok('Puja Saved Successfully');
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

    public function gpass($id)
    {
        $puja = PujaCommittee::find($id);
        if (!$puja) abort(404, 'INVALID puja');
        $path = public_path('qrs');
        if (!file_exists($path)) mkdir($path, 0755, true);
        $file = $path . '/' . $puja->id . '.png';
        $this->qrGen($file, $puja->secretary_mobile);
        return view('puja.gatepass', compact('puja'));
    }

    public function downloadPdf($id)
    {
        $puja = PujaCommittee::find($id);
        if (!$puja) abort(404, 'Puja not found');
        $file = public_path("qrs/{$puja->id}.png");
        $this->qrGen($file, $puja->secretary_mobile);
        $pdf = Pdf::loadView('puja.gatepass-pdf', compact('puja', 'file'));
        return $pdf->download("{$puja->secretary_mobile}.pdf");
    }

    public function scan()
    {
        $puja = $this->getUserObj();
        return view('puja.scan', compact('puja'));
    }

    public function attendance(Request $request)
    {
        $cuser = $this->getUserObj();
        $request->validate([
            'token'    => 'required|string',          // scanned QR token or UID
            'post'     => 'required|string|max:30',
            'typ'      => 'required|string|max:10',
            'location' => 'nullable|string|max:255',
        ]);

        // Find the user by token
        $puja = PujaCommittee::where('token', $request->token)->first();

        if (!$puja) return $this->err("GatePass not exists");

        $today = Carbon::today();
        $exists = Attendance
            ::where('scan_by', $cuser->id)
            ->where('user_id', $puja->id)
            ->whereDate('scan_datetime', $today)
            ->where('post', $request->post ?? 'post1')
            ->where('typ', $request->typ ?? 'att')
            ->exists();
        if ($exists) {
            return $this->err('Duplicate Entry not allowed');
        }

        $typNm = attDict()[$request->typ];
        Attendance::create([
            'scan_datetime' => now(),
            'scan_by'       => $cuser->id,
            'user_id'       => $puja->id,
            'post'          => $request->post ?? 'post1',
            'typ'           => $request->typ ?? 'att',
            'location'      => $request->location,
        ]);

        return $this->ok("Marked <b>" . $typNm . "</b> for " . $puja->secretary_mobile);
    }

    public function scanStat(Request $request)
    {
        $gateId = $request->get('gate_id');
        if (!$gateId) return $this->ok("ok", ["data" => []]);
        $today = Carbon::today();
        $scans = Attendance::where('post', $gateId)
            ->whereDate('scan_datetime', $today);
        $qCount  = (clone $scans)->where('typ', 'QUEUE')->count();
        $iCount  = (clone $scans)->where('typ', 'IN')->count();
        $oCount  = (clone $scans)->where('typ', 'OUT')->count();
        $stats = [
            ['name' => 'QUEUE', 'count' => $qCount, 'color' => 'danger'],
            ['name' => 'IN',    'count' => $iCount, 'color' => 'success'],
            ['name' => 'OUT',   'count' => $oCount, 'color' => 'primary'],
        ];
        return $this->ok("ok", ["data" => $stats]);
    }

    public function save_settings(Request $request)
    {
        Log::info("save_settings", $request->all());
        foreach ($request->except(['_token', '_method']) as $key => $val) {
            set_setting($key, $val);
        }
        return $this->ok("Saved Successfully");
    }

    public function send_otp(Request $request)
    {
        $err = $this->validate($request->all(), [
            'mobile'      => 'required|string|min:8|max:20|unique:puja_committees,secretary_mobile',
        ]);
        if ($err) return $err;
        $mob = $request->mobile;
        $otpData = Session::get('otp_sessions', []);
        if (isset($otpData[$mob])) {
            $data = $otpData[$mob];
            if ($data['attempts'] >= 3 && Carbon::now()->diffInMinutes($data['created_at']) < 3) {
                return $this->err('Maximum OTP attempts reached. Try after some time');
            }
        }
        $otp = rand(100000, 999999);
        $otpData[$mob] = [
            'otp' => $otp,
            'created_at' => Carbon::now(),
            'attempts' => isset($otpData[$mob]) ? $otpData[$mob]['attempts'] + 1 : 1
        ];
        Session::put('otp_sessions', $otpData);
        // Example: SmsService::send($mobile, "Your OTP is $otp");
        return $this->ok("OTP : $otp sent to $mob");
    }

    public function verify_otp(Request $request)
    {
        $cuser = $this->getUserObj();
        $mob = $request->input('mobile');
        $otp = $request->input('otp');
        $otpData = Session::get('otp_sessions', []);
        if (!isset($otpData[$mob])) {
            return $this->err('Invalid mobile.');
        }
        $data = $otpData[$mob];
        if (Carbon::now()->diffInMinutes($data['created_at']) > 5) {
            unset($otpData[$mob]);
            Session::put('otp_sessions', $otpData);
            return $this->err('OTP expired. Please request again.');
        }
        if ($otp != $data['otp']) {
            return $this->err('Invalid OTP.');
        }
        unset($otpData[$mob]);
        Session::put('otp_sessions', $otpData);

        $pass = Str::random(6);
        $pujaData = [
            'secretary_mobile'      => $mob,
            'password'              => $pass,
            'role'                  => $request->role ?? 'u', 
            'stat'                  => $request->stat ?? 'a', 
        ];
        $puja = User::create($pujaData);
        // Example: SmsService::send($mob, "Your password is $pass");
        $typNm = attDict()[$request->typ];
        Attendance::create([
            'scan_datetime' => now(),
            'scan_by'       => $cuser->id,
            'user_id'       => $puja->id,
            'post'          => $request->post ?? 'post1',
            'typ'           => $request->typ ?? 'att',
            'location'      => $request->location,
        ]);

        return $this->ok("OTP verified successfully and <br>Marked <b>" . $typNm . "</b> for " . $puja->secretary_mobile);
    }

}
