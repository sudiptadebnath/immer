<?php

namespace App\Http\Controllers;

use App\Models\ActionArea;
use App\Models\ImmersionDate;
use App\Models\PujaCategorie;
use App\Models\PujaCommittee;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;

class RepoController extends Controller
{
    public function regsdata()
    {
        $out = DataTables::of(PujaCommittee::orderBy('proposed_immersion_date'))
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
        $query = PujaCommittee::orderBy('proposed_immersion_date');

        $date = $request->filled('dt') ? Carbon::parse($request->dt) : now();

        // Immersion window based on given date
        $start = $date->copy()->startOfDay()->addHours(3);
        $end   = $start->copy()->addDay();

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
            ->addColumn('attendance', function ($row) use ($start, $end) {
                $att = DB::table('attendance')
                    ->where('puja_committee_id', $row->id)
                    ->whereBetween('scan_datetime', [$start, $end])
                    ->orderByDesc('scan_datetime') // <- latest first
                    ->get()
                    ->map(function ($att) {
                        return [
                            'typ'  => $att->typ,
                            'time' => Carbon::parse($att->scan_datetime)->format('d/m/Y h:i A'),
                        ];
                    });
                return $att->toArray(); 
            })          
            ->make(true);
    }
	
    public function dhundata(Request $request)
    {
        $query = PujaCommittee::whereNotNull('team_members')
            ->orderBy('proposed_immersion_date');

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
