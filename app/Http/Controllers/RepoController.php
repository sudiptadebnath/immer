<?php

namespace App\Http\Controllers;

use App\Models\ActionArea;
use App\Models\ImmersionDate;
use App\Models\PujaCategorie;
use App\Models\PujaCommittee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;

class RepoController extends Controller
{
    public function regsdata()
    {
        $query = PujaCommittee::orderBy('proposed_immersion_date');
        $data = $query->get()->map(function ($row) {
            $row['proposed_immersion_date'] = $row->proposed_immersion_date 
                ? Carbon::parse($row->proposed_immersion_date)->format('d/m/Y') 
                : null;
            $row['proposed_immersion_time'] = $row->proposed_immersion_time 
                ? Carbon::parse($row->proposed_immersion_time)->format('h:i A') 
                : null;
            $row['stat'] = statDict()[$row->stat] ?? $row->stat;
            return $row;
        });
        return DataTables::of($data)->make(true);
    }
	
    public function immerdata(Request $request)
    {
        $query = PujaCommittee::orderBy('proposed_immersion_date');
		if ($request->filled('dt')) {
			$query->whereDate('proposed_immersion_date', $request->dt);
		}
        $data = $query->get()->map(function ($row) {
            $row['proposed_immersion_date'] = $row->proposed_immersion_date 
                ? Carbon::parse($row->proposed_immersion_date)->format('d/m/Y') 
                : null;
            $row['proposed_immersion_time'] = $row->proposed_immersion_time 
                ? Carbon::parse($row->proposed_immersion_time)->format('h:i A') 
                : null;
            $row['stat'] = statDict()[$row->stat] ?? $row->stat;
            return $row;
        });
        return DataTables::of($data)->make(true);
    }
	
    public function dhundata(Request $request)
    {
        $query = PujaCommittee::
			whereNotNull('team_members')
			->orderBy('proposed_immersion_date');
		if ($request->filled('dt')) {
			$query->whereDate('proposed_immersion_date', $request->dt);
		}
        $data = $query->get()->map(function ($row) {
            $row['proposed_immersion_date'] = $row->proposed_immersion_date 
                ? Carbon::parse($row->proposed_immersion_date)->format('d/m/Y') 
                : null;
            $row['proposed_immersion_time'] = $row->proposed_immersion_time 
                ? Carbon::parse($row->proposed_immersion_time)->format('h:i A') 
                : null;
            $row['stat'] = statDict()[$row->stat] ?? $row->stat;
            return $row;
        });
        return DataTables::of($data)->make(true);
    }

}
