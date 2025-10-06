<?php

namespace App\Http\Controllers;

use App\Models\PujaCommittee;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Log;

class RepoController extends Controller
{
    /*public function regsdata(Request $request)
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
	*/
	
/*
	public function regsdata(Request $request)
	{
		[$start, $end] = getStEnDt($request->dt); 

		$query = PujaCommittee::query();

		$typ = $request->typ ?? '';
		if ($typ) {
			if ($typ === 'nt') {
				$query->whereNotNull('action_area')
					  ->where('action_area', '!=', '');
			}
			if ($typ === 'ont') {
				$query->where(function ($q) {
					$q->whereNull('action_area')
					  ->orWhere('action_area', '');
				});
			}
		}
		
		$dt = $request->dt ?? null;
		if ($dt) {
			$query->whereDate('proposed_immersion_date', $dt);
		}
	
		if ($request->filled('istat')) {
			$istat = $request->istat;

			if ($istat === 'not_done') {
				$query->whereNotExists(function ($q) use ($start, $end) {
					$q->select(DB::raw(1))
					  ->from('attendance as a')
					  ->whereColumn('a.puja_committee_id', 'puja_committees.id')
					  ->whereBetween('a.scan_datetime', [$start, $end])
					  ->where('a.typ', 'in');
				});
			} else {
				$query->whereExists(function ($q) use ($start, $end) {
					$q->select(DB::raw(1))
					  ->from('attendance as a')
					  ->whereColumn('a.puja_committee_id', 'puja_committees.id')
					  ->whereBetween('a.scan_datetime', [$start, $end])
					  ->where('a.typ', 'in');
				});
			}
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
			->addColumn('last_attendance_status', function ($row) use ($start, $end) {
				$latest = DB::table('attendance')
					->where('puja_committee_id', $row->id)
					->whereBetween('scan_datetime', [$start, $end])
					->orderByDesc('scan_datetime')
					->first();

				if (!$latest) {
					return 'Not Done';
				}

				return 'Done';
			})
			->filterColumn('last_attendance_status', function ($query, $keyword) use ($start, $end) {
				$keyword = strtolower($keyword);

				if ($keyword === 'done') {
					$query->whereExists(function ($q) use ($start, $end) {
						$q->select(DB::raw(1))
						  ->from('attendance as a')
						  ->whereColumn('a.puja_committee_id', 'puja_committees.id')
						  ->whereBetween('a.scan_datetime', [$start, $end]);
					});
				} elseif ($keyword === 'not done') {
					$query->whereNotExists(function ($q) use ($start, $end) {
						$q->select(DB::raw(1))
						  ->from('attendance as a')
						  ->whereColumn('a.puja_committee_id', 'puja_committees.id')
						  ->whereBetween('a.scan_datetime', [$start, $end]);
					});
				}
			})
			->orderColumn('last_attendance_status', function ($query, $order) use ($start, $end) {
				$query->orderByRaw("
					CASE 
					  WHEN EXISTS (
						SELECT 1 FROM attendance a
						WHERE a.puja_committee_id = puja_committees.id
						  AND a.scan_datetime BETWEEN ? AND ?
					  ) THEN 1 ELSE 0 END {$order}
				", [$start, $end]);
			})
			->make(true);
	}
*/

	public function regsdata(Request $request)
	{
		[$start, $end] = getStEnDt($request->dt); 
		$query = PujaCommittee::query();

		// --- Filter by typ ---
		$typ = $request->typ ?? '';
		if ($typ) {
			if ($typ === 'nt') {
				$query->whereNotNull('action_area')
					  ->where('action_area', '!=', '');
			}
			if ($typ === 'ont') {
				$query->where(function ($q) {
					$q->whereNull('action_area')
					  ->orWhere('action_area', '');
				});
			}
		}

		// --- Date filter for proposed immersion ---
		$dt = $request->dt ?? null;
		if ($dt) {
			$query->whereDate('proposed_immersion_date', $dt);
		}

		// --- Filter by attendance status ---
		if ($request->filled('istat')) {
			$istat = $request->istat;

			if ($istat === 'not_done') {
				$query->whereNotExists(function ($q) use ($start, $end, $dt) {
					$q->select(DB::raw(1))
					  ->from('attendance as a')
					  ->whereColumn('a.puja_committee_id', 'puja_committees.id')
					  ->where('a.typ', 'in');
					
					if ($dt) {
						$q->whereBetween('a.scan_datetime', [$start, $end]);
					}
				});
			} else {
				$query->whereExists(function ($q) use ($start, $end, $dt) {
					$q->select(DB::raw(1))
					  ->from('attendance as a')
					  ->whereColumn('a.puja_committee_id', 'puja_committees.id')
					  ->where('a.typ', 'in');

					if ($dt) {
						$q->whereBetween('a.scan_datetime', [$start, $end]);
					}
				});
			}
		}

		// --- DataTables Output ---
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
			->addColumn('last_attendance_status', function ($row) use ($start, $end, $dt) {
				$latest = DB::table('attendance')
					->where('puja_committee_id', $row->id)
					->where('typ', 'in');

				if ($dt) {
					$latest->whereBetween('scan_datetime', [$start, $end]);
				}

				$latest->orderByDesc('scan_datetime');
				$record = $latest->first();

				return $record ? 'Done' : 'Not Done';
			})
			->filterColumn('last_attendance_status', function ($query, $keyword) use ($start, $end, $dt) {
				$keyword = strtolower($keyword);

				if ($keyword === 'done') {
					$query->whereExists(function ($q) use ($start, $end, $dt) {
						$q->select(DB::raw(1))
						  ->from('attendance as a')
						  ->whereColumn('a.puja_committee_id', 'puja_committees.id')
						  ->where('a.typ', 'in');
						
						if ($dt) {
							$q->whereBetween('a.scan_datetime', [$start, $end]);
						}
					});
				} elseif ($keyword === 'not done') {
					$query->whereNotExists(function ($q) use ($start, $end, $dt) {
						$q->select(DB::raw(1))
						  ->from('attendance as a')
						  ->whereColumn('a.puja_committee_id', 'puja_committees.id')
						  ->where('a.typ', 'in');
						
						if ($dt) {
							$q->whereBetween('a.scan_datetime', [$start, $end]);
						}
					});
				}
			})
			->orderColumn('last_attendance_status', function ($query, $order) use ($start, $end, $dt) {
				if ($dt) {
					$query->orderByRaw("
						CASE 
						  WHEN EXISTS (
							SELECT 1 FROM attendance a
							WHERE a.puja_committee_id = puja_committees.id
							  AND a.scan_datetime BETWEEN ? AND ?
							  AND a.typ = 'in'
						  ) THEN 1 ELSE 0 END {$order}
					", [$start, $end]);
				} else {
					$query->orderByRaw("
						CASE 
						  WHEN EXISTS (
							SELECT 1 FROM attendance a
							WHERE a.puja_committee_id = puja_committees.id
							AND a.typ = 'in'
						  ) THEN 1 ELSE 0 END {$order}
					");
				}
			})
			->make(true);
	}
	
	
	public function immerdata2(Request $request)
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
				$query->where(function ($q) {
					$q->whereNull('action_area')
					  ->orWhere('action_area', '');
				});
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


public function immerdata(Request $request)
{
    [$start, $end] = getStEnDt($request->dt);
    $dt = $request->dt ?? null;
	
    $query = PujaCommittee::whereExists(function ($q) use ($start, $end, $request, $dt) {
        $q->select(DB::raw(1))
            ->from('attendance as a')
            ->whereColumn('a.puja_committee_id', 'puja_committees.id');

        // Use date filter only if dt provided
        if ($dt) {
            $q->whereBetween('a.scan_datetime', [
                $start->copy()->setTimezone('UTC'),
                $end->copy()->setTimezone('UTC'),
            ]);
        }

        // Use MAX(scan_datetime) only if dt is given
        $q->whereRaw('a.scan_datetime = (
            SELECT MAX(scan_datetime)
            FROM attendance
            WHERE puja_committee_id = a.puja_committee_id'
            . ($dt ? ' AND scan_datetime BETWEEN ? AND ?' : '') . '
        )', $dt ? [
            $start->copy()->setTimezone('UTC'),
            $end->copy()->setTimezone('UTC'),
        ] : []);

        if ($request->istat) {
            $q->where('a.typ', $request->istat);
        }
    });

    // --- typ filter ---
    $typ = $request->typ ?? '';
    if ($typ) {
        if ($typ === 'nt') {
            $query->whereNotNull('action_area')
                  ->where('action_area', '!=', '');
        }
        if ($typ === 'ont') {
            $query->where(function ($q) {
                $q->whereNull('action_area')
                  ->orWhere('action_area', '');
            });
        }
    }

    // --- dstat filter ---
    if ($request->filled('dstat')) {
        if ($request->dstat == "1") $query->whereNotNull('team_members');
        if ($request->dstat == "0") $query->whereNull('team_members');
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
        ->addColumn('attendance', function ($row) use ($start, $end, $dt) {
            $att = DB::table('attendance')
                ->where('puja_committee_id', $row->id);

            if ($dt) {
                $att->whereBetween('scan_datetime', [
                    $start->copy()->setTimezone('UTC'),
                    $end->copy()->setTimezone('UTC'),
                ]);
            }

            $att->orderByDesc('scan_datetime');

            return $att->get()->map(function ($att) {
                return [
                    'typ'  => attDict()[$att->typ],
                    'time' => Carbon::parse($att->scan_datetime, 'UTC')
                        ->setTimezone('Asia/Kolkata')
                        ->format('d M h:i A'),
                ];
            })->toArray();
        })
        ->addColumn('latest_attendance_typ', function ($row) use ($start, $end, $dt) {
            $latest = DB::table('attendance')
                ->where('puja_committee_id', $row->id);

            if ($dt) {
                $latest->whereBetween('scan_datetime', [
                    $start->copy()->setTimezone('UTC'),
                    $end->copy()->setTimezone('UTC'),
                ]);
            }

            $latest = $latest->orderByDesc('scan_datetime')->first();
            return $latest ? $latest->typ : "";
        })
        ->orderColumn('latest_attendance_typ', function ($query, $order) use ($start, $end, $dt) {
            if ($dt) {
                $query->orderByRaw("(SELECT a.typ FROM attendance a
                    WHERE a.puja_committee_id = puja_committees.id
                      AND a.scan_datetime BETWEEN ? AND ?
                    ORDER BY a.scan_datetime DESC
                    LIMIT 1) {$order}", [
                    $start->copy()->setTimezone('UTC'),
                    $end->copy()->setTimezone('UTC'),
                ]);
            } else {
                $query->orderByRaw("(SELECT a.typ FROM attendance a
                    WHERE a.puja_committee_id = puja_committees.id
                    ORDER BY a.scan_datetime DESC
                    LIMIT 1) {$order}");
            }
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
