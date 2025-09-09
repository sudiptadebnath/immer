<?php

namespace App\Http\Controllers;

use App\Models\ActionArea;
use App\Models\ImmersionDate;
use App\Models\PujaCategorie;
use App\Models\PujaCommittee;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
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
