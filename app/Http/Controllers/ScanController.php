<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\PujaCommittee;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ScanController extends Controller
{
    public function scanStat(Request $request)
    {
        $today = Carbon::today();
        $scans = Attendance::where('post', 'post1')
            ->whereDate('scan_datetime', $today);
        $qCount  = (clone $scans)->where('typ', 'queue')->count();
        $iCount  = (clone $scans)->where('typ', 'in')->count();
        $oCount  = (clone $scans)->where('typ', 'out')->count();
        $stats = [
            ['name' => 'QUEUE', 'count' => $qCount, 'color' => 'danger'],
            ['name' => 'IN',    'count' => $iCount, 'color' => 'success'],
            ['name' => 'OUT',   'count' => $oCount, 'color' => 'primary'],
        ];
        return $this->ok("ok", ["data" => $stats]);
    }

    public function scanview()
    {
        $user = $this->getUserObj();
        return view('scan.scan', compact('user'));
    }

    public function mark_by_qr(Request $request)
    {
        $cuser = $this->getUserObj();
        $request->validate([
            'token'    => 'required|string',          // scanned QR token or UID
            'post'     => 'required|string|max:30',
            'typ'      => 'required|string|max:10',
            'location' => 'nullable|string|max:255',
        ]);

        // Find the user by token
        $user = User::where('token', $request->token)->first();

        if (!$user) return $this->err("GatePass not exists");

        $today = Carbon::today();
        $exists = Attendance
            ::where('scan_by', $cuser->id)
            ->where('user_id', $user->id)
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
            'user_id'       => $user->id,
            'post'          => $request->post ?? 'post1',
            'typ'           => $request->typ ?? 'att',
            'location'      => $request->location,
        ]);

        return $this->ok("Marked <b>" . $typNm . "</b> for " . $user->secretary_mobile);
    }

    public function mark_by_mob(Request $request)
    {
        $err = $this->validate($request->all(), [
            'mobile'      => 'required|string|min:8|max:20|unique:puja_committees,secretary_mobile',
            'typ'      => 'required|string|min:1|max:10',
        ],[
            'mobile.unique' => "Duplicate mobile",
            'mobile.*' => "Duplicate mobile",
            'typ.*' => "Incorrect attandance type",
        ]);
        if ($err) return $err;
        $cuser = $this->getUserObj();
        $mob = $request->input('mobile');
        $typNm = attDict()[$request->typ];
        $pujaData = [
            'secretary_mobile'      => $mob,
        ];
        $puja = PujaCommittee::create($pujaData);
        Attendance::create([
            'scan_datetime' => now(),
            'scan_by'       => $cuser->id,
            'user_id'       => $puja->id,
            'post'          => $request->post ?? 'post1',
            'typ'           => $request->typ ?? 'att',
            'location'      => $request->location,
        ]);
        return $this->ok("Mobile verified and <br>Marked <b>" . $typNm . "</b> for " . $mob);
    }

}
