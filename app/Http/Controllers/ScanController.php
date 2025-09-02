<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\PujaCommittee;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScanController extends Controller
{
    public function scanStat()
    {
        // $today = Carbon::today();
        // $scans = Attendance::whereDate('scan_datetime', $today);
        // $scans = Attendance::query();
        $scans = DB::table('attendance as s')
            ->select('s.puja_committee_id', 's.typ')
            ->join(DB::raw('(SELECT puja_committee_id, MAX(scan_datetime) as max_dt 
                            FROM attendance 
                            GROUP BY puja_committee_id) as latest'),
                function ($join) {
                    $join->on('s.puja_committee_id', '=', 'latest.puja_committee_id')
                        ->on('s.scan_datetime', '=', 'latest.max_dt');
                })
            ->get();
        $qCount  = (clone $scans)->where('typ', 'queue')->count();
        $iCount  = (clone $scans)->where('typ', 'in')->count();
        $oCount  = (clone $scans)->where('typ', 'out')->count();
        $stats = [
            ['name' => 'Reported', 'count' => $qCount, 'color' => 'danger'],
            ['name' => 'In',    'count' => $iCount, 'color' => 'success'],
            ['name' => 'Immersion Done',   'count' => $oCount, 'color' => 'primary'],
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
        $request->validate(['token'    => 'required|string',]);
        $puja = PujaCommittee::where('secretary_mobile', $request->token)->first();
        if (!$puja) return $this->err("GatePass not found");
        $today = Carbon::today();
        $lastAtt = Attendance::where('puja_committee_id', $puja->id)
            //->whereDate('scan_datetime', $today)
            ->orderBy('scan_datetime', 'desc')
            ->first();
        if (!$lastAtt && $cuser->role=="s") $typ = 'queue';
        elseif ($lastAtt->typ === 'queue' && $cuser->role=="o")  $typ = 'in';
        elseif ($lastAtt->typ === 'in' && $cuser->role=="s") $typ = 'out';
        else return $this->err("Unaccepted pass");
        Attendance::create([
            'scan_datetime' => now(),
            'scan_by'       => $cuser->id,
            'puja_committee_id'       => $puja->id,
            'typ'           => $typ,
        ]);
        $typNm = attDict()[$typ];
        return $this->ok("Marked <b>" . $typNm . "</b> for " . $puja->secretary_mobile);
    }

    public function mark_by_mob(Request $request)
    {
        $err = $this->validate($request->all(), [
            'mobile'      => 'required|string|min:8|max:20|unique:puja_committees,secretary_mobile',
        ],[
            'mobile.unique' => "Duplicate mobile",
            'mobile.*' => "Duplicate mobile",
        ]);
        if ($err) return $err;
        $cuser = $this->getUserObj();
        $mob = $request->mobile;
        $pujaData = [
            'secretary_mobile'      => $mob,
        ];
        $puja = PujaCommittee::create($pujaData);
        Attendance::create([
            'scan_datetime' => now(),
            'scan_by'       => $cuser->id,
            'puja_committee_id'       => $puja->id,
            'typ'           => 'queue',
            'location'      => $request->location,
        ]);
        return $this->ok("Mobile verified and <br>Marked <b>Reported</b> for " . $mob);
    }

}
