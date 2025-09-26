<?php

namespace App\Http\Controllers;

use App\Models\PujaCommittee;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class RepoController extends Controller
{
    public function regsdata(Request $request)
    {
        $query = PujaCommittee::query();//orderBy('proposed_immersion_date');

        $typ = $request->typ ?? '';
        if($typ) {
            if($typ==='nt') {
                $query->whereNotNull('action_area')
                ->where('action_area', '!=', '');
            }
            if($typ==='ont') {
                $query->whereNull('action_area')
                ->orWhere('action_area', '');
            }
        }

        $out = DataTables::of($query)
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
	
	public function immerdata(Request $request)
	{
		[$start, $end] = getStEnDt($request->dt);

		$query = PujaCommittee::whereExists(function ($q) use ($start, $end, $request) {
			$q->select(DB::raw(1))
				->from('attendance as a')
				->whereColumn('a.puja_committee_id', 'puja_committees.id')
				->whereBetween('a.scan_datetime', [
					$start->copy()->setTimezone('UTC'),
					$end->copy()->setTimezone('UTC'),
				])
				->whereRaw('a.scan_datetime = (
					SELECT MAX(scan_datetime)
					FROM attendance
					WHERE puja_committee_id = a.puja_committee_id
					  AND scan_datetime BETWEEN ? AND ?
				)', [
					$start->copy()->setTimezone('UTC'),
					$end->copy()->setTimezone('UTC'),
				]);

			if ($request->istat) {
				$q->where('a.typ', $request->istat);
			}
		});
		
        $typ = $request->typ ?? '';
        if($typ) {
            if($typ==='nt') {
                $query->whereNotNull('action_area')
                ->where('action_area', '!=', '');
            }
            if($typ==='ont') {
                $query->whereNull('action_area')
                ->orWhere('action_area', '');
            }
        }


		if ($request->filled('dstat')) {
			if($request->dstat == "1") $query->whereNotNull('team_members');
			if($request->dstat == "0") $query->whereNull('team_members');
		}
		
		return DataTables::of($query)
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
			->addColumn('attendance', function ($row) use ($start, $end) {
				$att = DB::table('attendance')
					->where('puja_committee_id', $row->id)
					->whereBetween('scan_datetime', [
						$start->copy()->setTimezone('UTC'),
						$end->copy()->setTimezone('UTC'),
					])
					->orderByDesc('scan_datetime') // latest first
					->get()
					->map(function ($att) {
						return [
							'typ'  => attDict()[$att->typ],
							'time' => Carbon::parse($att->scan_datetime, 'UTC')
								->setTimezone('Asia/Kolkata')
								->format('d M h:i A'),
						];
					});
				return $att->toArray(); 
			})
			->addColumn('latest_attendance_typ', function ($row) use ($start, $end) {
				$latest = DB::table('attendance')
					->where('puja_committee_id', $row->id)
					->whereBetween('scan_datetime', [
						$start->copy()->setTimezone('UTC'),
						$end->copy()->setTimezone('UTC'),
					])
					->orderByDesc('scan_datetime')
					->first();

				return $latest ? $latest->typ : "";
			})			
			->orderColumn('latest_attendance_typ', function ($query, $order) use ($start, $end) {
				$query->orderByRaw("(SELECT a.typ FROM attendance a
				 WHERE a.puja_committee_id = puja_committees.id
				   AND a.scan_datetime BETWEEN ? AND ?
				 ORDER BY a.scan_datetime DESC
				 LIMIT 1) {$order}", [
					$start->copy()->setTimezone('UTC'),
					$end->copy()->setTimezone('UTC'),
				 ]);
			})
			->make(true);
	}

	
    public function dhundata(Request $request)
    {
        $query = PujaCommittee::whereNotNull('team_members');
           // ->orderBy('proposed_immersion_date');

        if ($request->filled('dt')) {
            $query->whereDate('proposed_immersion_date', $request->dt);
        }

        return DataTables::of($query)
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
    }

}
