<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\PujaCommittee;
use App\Models\ImmersionDate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;

class ScanController extends Controller
{

    
    public function getcomm_bydt(Request $request) {
        $date = $request->input('date');
        $typ  = $request->input('typ', "1");
        //Log::info("xxx",["data"=>$request->all()]);
        if (!$date) return $this->err("Date is required");
        try {
            // immersion window = 3 AM → next day 3 AM
            $start = Carbon::parse($date)->addHours(3);
            $end   = Carbon::parse($date)->addDay()->addHours(3);

            // adjust if date is today but before 3 AM
            if (now()->lt($start)) {
                $start = Carbon::parse($date)->subDay()->addHours(3);
                $end   = Carbon::parse($date)->addHours(3);
            }

            if ($typ == "1") {
                // all committees registered that day
                $query = PujaCommittee::whereDate('proposed_immersion_date', $date)
                    ->orderBy('proposed_immersion_date');
            } elseif ($typ == "2") {
                // only immersed committees (attendance out)
                $query = PujaCommittee::select('puja_committees.*', 'a.scan_datetime as immersion_time')
                    ->join('attendance as a', 'puja_committees.id', '=', 'a.puja_committee_id')
                    ->where('a.typ', 'in')
                    ->whereBetween('a.scan_datetime', [$start, $end])
                    ->distinct()
                    ->orderBy('a.scan_datetime');
            } else {
                return $this->err("Incorrect Type");
            }

            $data = $query->get();
            return $this->ok("Pujas",["data"=>$data]);
        } catch (\Exception $e) {
            return $this->err("Server Error");
        }
    }

    public function scanstat_bydt(Request $request) {
        $date = $request->input('date');
        if (!$date) {
            return $this->err("Date is required");
        }

        try {
            // Start of the immersion day = selected date at 3 AM
            $start = Carbon::parse($date)->addHours(3);
            $end   = Carbon::parse($date)->addDay()->addHours(3);

            // if selected date is today and now < 3 AM, shift to yesterday
            if (now()->lt($start)) {
                $start = Carbon::parse($date)->subDay()->addHours(3);
                $end   = Carbon::parse($date)->addHours(3);
            }

            // Registered committees (by proposed immersion date falling in this day-window)
			$registered = DB::table('puja_committees')
				->whereDate('proposed_immersion_date', $date)
				->count();

            // Immersed committees (attendance "out" between 3AM→3AM)
            $immersed = DB::table('attendance')
                ->where('typ', 'in')
                ->whereBetween('scan_datetime', [$start, $end])
                ->distinct('puja_committee_id')
                ->count('puja_committee_id');

            return $this->ok("ok", [
                'registered' => $registered,
                'immersed'   => $immersed,
                'date'       => $start->format('d-M-Y') . " to " . $end->format('d-M-Y'),
            ]);
        } catch (\Exception $e) {
            return $this->err("Error fetching stats: " . $e->getMessage());
        }
        
    }

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
		//$oCount = (clone $scans)->where('typ', 'out')->count();
            
        $immersionData = DB::table('attendance')
            ->select(
                'puja_committee_id',
                DB::raw('MIN(scan_datetime) as first_scan'),
                DB::raw('MAX(scan_datetime) as last_scan')
            )
            ->whereBetween('scan_datetime', [$start, $end])
            ->groupBy('puja_committee_id')
            ->get();

        $totalDuration = 0;
        $committeeCount = 0;

        foreach ($immersionData as $row) {
            $first = Carbon::parse($row->first_scan);
            $last  = Carbon::parse($row->last_scan);
            $diffMinutes = $first->diffInMinutes($last);

            if ($diffMinutes > 0) { // ignore 0-diff cases
                $totalDuration += $diffMinutes;
                $committeeCount++;
            }
        }
		
        $avgImmersionTime = 0;

        if ($committeeCount > 0) {
            $avgMinutes = round($totalDuration / $committeeCount); // average in minutes
            $hours = floor($avgMinutes / 60);
            $minutes = $avgMinutes % 60;
            $avgImmersionTime = sprintf("%02d:%02d", $hours, $minutes); // HH:MM format
        } else {
            $avgImmersionTime = sprintf("%02d:%02d", 0, 0); // HH:MM format
		}
        
		$totalOut = DB::table('attendance')->where('typ', 'in')->count();

		$stats = [ $qCount, $iCount, 0, /*$oCount,*/ $qCount + $iCount /*+ $oCount*/, $avgImmersionTime, $totalOut ];

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
		// define immersion day window
		/*$start = Carbon::today()->addHours(3);      // today 3 AM
		$end   = Carbon::tomorrow()->addHours(3);   // tomorrow 3 AM
		if (now()->lt($start)) {
			$start = Carbon::yesterday()->addHours(3);
			$end   = Carbon::today()->addHours(3);
		}
		$allowed = ImmersionDate::whereBetween('idate', [$start, $end])->exists();
		if (!$allowed) {
			return $this->err("Today is Not immersion date");
		}*/

        $cuser = $this->getUserObj();
        $request->validate(['token'    => 'required|string',]);
        $puja = PujaCommittee::where('secretary_mobile', $request->token)->first();
        if (!$puja) return $this->err("GatePass not found");
        $today = Carbon::today();
		/*if (!$puja->proposed_immersion_date || !Carbon::parse($puja->proposed_immersion_date)->isSameDay($today)) {
			return $this->err("GatePass not valid for today");
		}*/
        $lastAtt = Attendance::where('puja_committee_id', $puja->id)
            //->whereDate('scan_datetime', $today)
            ->orderBy('scan_datetime', 'desc')
            ->first();
        if(!$lastAtt) { // FIRST SWIPE
            if($cuser->role != "s") { // MUST BE SCANNER POST
                return $this->err("You are not in queue.");
            } else $typ = 'queue';
        } else {
            if($lastAtt->typ === 'queue') { // 2ND SWIPE
                if(!hasRole("ao")) { // MUST BE SCANNER POST
                    //return $this->err("Counter post required");
                    return $this->err("Already verified and in the Queue.");
                } else $typ = 'in';
            } /*else if($lastAtt->typ === 'in') { // 3RD SWIPE
                if($cuser->role != "s") { // MUST BE SCANNER POST
                    return $this->err("Scanner post required.");
                } else $typ = 'out';
            }*/ else {
                //return $this->err("All scan completed.");
                return $this->err("Immersion already done.");
            }
        } 
        Attendance::create([
            'scan_datetime' => now(),
            'scan_by'       => $cuser->id,
            'puja_committee_id'       => $puja->id,
            'typ'           => $typ,
        ]);
        $typNm = attDict()[$typ];
        //return $this->ok("Marked <b>" . $typNm . "</b> for " . $puja->secretary_mobile);
        if($cuser->role == "s") {
            return $this->ok("Digital Pass successfully verified");
        } else {
            $pujacontroller = new PujaController;
            $pujacontroller->sendSmsToPuja($puja,"98656");
            return $this->ok("Digital Pass successfully verified for Immersion");
        }
    }

    public function mark_by_mob(Request $request)
    {
		// define immersion day window
		/*$start = Carbon::today()->addHours(3);      // today 3 AM
		$end   = Carbon::tomorrow()->addHours(3);   // tomorrow 3 AM
		if (now()->lt($start)) {
			$start = Carbon::yesterday()->addHours(3);
			$end   = Carbon::today()->addHours(3);
		}
		$allowed = ImmersionDate::whereBetween('idate', [$start, $end])->exists();
		if (!$allowed) {
			return $this->err("Today is Not immersion date");
		}*/
		
		$today = Carbon::today();

        $cuser = $this->getUserObj();
		//if($cuser->role != "s") return $this->err("You are not in queue.");
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
				'proposed_immersion_date'=> $today,
			];
			$puja = PujaCommittee::create($pujaData);
		} else {
            //return $this->err("Can't register. Already registered mobile.");
			/*if ($puja->proposed_immersion_date && !Carbon::parse($puja->proposed_immersion_date)->isSameDay($today)) {
				return $this->err("GatePass not valid for today");
			}*/
		}
        $lastAtt = Attendance::where('puja_committee_id', $puja->id)
            //->whereDate('scan_datetime', $today)
            ->orderBy('scan_datetime', 'desc')
            ->first();
        if(!$lastAtt) { // FIRST SWIPE
            if($cuser->role != "s") { // MUST BE SCANNER POST
                return $this->err("You are not in queue.");
            } else $typ = 'queue';
        } else {
            if($lastAtt->typ === 'queue') { // 2ND SWIPE
                if(!hasRole("ao")) { // MUST BE SCANNER POST
                    //return $this->err("Counter post required");
                    return $this->err("Already verified and in the Queue.");
                } else $typ = 'in';
            } /*else if($lastAtt->typ === 'in') { // 3RD SWIPE
                if($cuser->role != "s") { // MUST BE SCANNER POST
                    return $this->err("Scanner post required.");
                } else $typ = 'out';
            }*/ else {
                //return $this->err("All scan completed.");
                return $this->err("Immersion already done.");
            }
        } 
        Attendance::create([
            'scan_datetime' => now(),
            'scan_by'       => $cuser->id,
            'puja_committee_id'       => $puja->id,
            'typ'           => $typ,
        ]);
        $typNm = attDict()[$typ];
        return $this->ok("Digital Pass successfully verified");
    }

}
