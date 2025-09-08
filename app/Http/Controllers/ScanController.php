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
		$start = Carbon::today()->addHours(3);      // 03:00 today
		$end   = Carbon::tomorrow()->addHours(3);   // 03:00 tomorrow

		if (now()->lt($start)) {
			$start = Carbon::yesterday()->addHours(3);
			$end   = Carbon::today()->addHours(3);
		}

		$scans = DB::table('attendance as s')
			->select('s.puja_committee_id', 's.typ')
			->join(
				DB::raw('(SELECT puja_committee_id, MAX(scan_datetime) as max_dt 
						  FROM attendance 
						  WHERE scan_datetime >= "'.$start.'" 
							AND scan_datetime < "'.$end.'" 
						  GROUP BY puja_committee_id) as latest'),
				function ($join) {
					$join->on('s.puja_committee_id', '=', 'latest.puja_committee_id')
						 ->on('s.scan_datetime', '=', 'latest.max_dt');
				}
			)
			->whereBetween('s.scan_datetime', [$start, $end])
			->get();

		$qCount = (clone $scans)->where('typ', 'queue')->count();
		$iCount = (clone $scans)->where('typ', 'in')->count();
		$oCount = (clone $scans)->where('typ', 'out')->count();
		
		$totalOut = DB::table('attendance')->where('typ', 'out')->count();

		$stats = [ $qCount, $iCount, $oCount, $qCount + $iCount + $oCount, $totalOut ];

		return $this->ok("ok", [
			"data" => $stats, 
			"dt" => $start->format('d-M-Y')
		]);
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
		if (!$puja->proposed_immersion_date || !\Carbon\Carbon::parse($puja->proposed_immersion_date)->isSameDay($today)) {
			return $this->err("GatePass not valid for today");
		}
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
        $cuser = $this->getUserObj();
		if($cuser->role != "s") return $this->err("Not a scanner post");
		$request->validate([
			'mobile' => [
				'required',
				'regex:/^[6-9]\d{9}$/',
			],
		], [
			'mobile.regex' => 'Enter a valid 10-digit Indian mobile number starting with 6–9',
		]);
        $puja = PujaCommittee::where('secretary_mobile', $request->mobile)->first();
        if (!$puja) {
			$mob = $request->mobile;
			$pujaData = [
				'secretary_mobile'      => $mob,
			];
			$puja = PujaCommittee::create($pujaData);
		} else {
			if (!$puja->proposed_immersion_date || !\Carbon\Carbon::parse($puja->proposed_immersion_date)->isSameDay($today)) {
				return $this->err("GatePass not valid for today");
			}
		}
        $today = Carbon::today();
        $lastAtt = Attendance::where('puja_committee_id', $puja->id)
            //->whereDate('scan_datetime', $today)
            ->orderBy('scan_datetime', 'desc')
            ->first();
        if (!$lastAtt && $cuser->role=="s") $typ = 'queue';
        else return $this->err("Unaccepted pass");
        Attendance::create([
            'scan_datetime' => now(),
            'scan_by'       => $cuser->id,
            'puja_committee_id'       => $puja->id,
            'typ'           => $typ,
        ]);
        $typNm = attDict()[$typ];
        return $this->ok("Mobile verified and <br>Marked <b>Reported</b> for " . $mob);
    }

}
