<?php

namespace App\Http\Controllers;

use App\Models\ActionArea;
use App\Models\Attendance;
use App\Models\PujaCategorie;
use App\Models\PujaCommittee;
use App\Models\PujaCommitteeRepo;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class PujaController extends Controller
{
    public function data()
    {
        $out = DataTables::of(PujaCommittee::query())
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

    public function get($id)
    {
        $puja = PujaCommittee::find($id);
        if (!$puja) return $this->err("No Such Puja");
        return $this->ok("Puja Detail", ["data" => $puja]);
    }

    public function add(Request $request)
    {
		//Log::info("aaa",["data"=>$request->all()]);
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
		if ($request->in_newtown) {
			if (strtolower($request->input('puja_committee_name')) === 'other') {
				$nm = $request->input('puja_committee_name_other');

                // ✅ Insert into Repo if not exists already
                $actionArea = ActionArea::where('name', $request->action_area)->first();
                $category   = PujaCategorie::where('name', $request->category)->first();
                if ($actionArea && $category) {
                    // Insert into Repo if not exists
                    $exists = PujaCommitteeRepo::where('name', $nm)->first();
                    if (!$exists) {
                        PujaCommitteeRepo::create([
                            'action_area_id'   => $actionArea->id,
                            'puja_category_id' => $category->id,
                            'name'             => $nm,
                            'puja_address'     => $request->puja_committee_address,
                        ]);
                    }
                }

			} else {
				$nm = $request->input('puja_committee_name');
			}
		} else {
			$nm = $request->input('puja_committee_name_text');
		}
		$request->merge([
			'puja_committee_name' => $nm
		]);
		//Log::info("xxx",["data"=>$request->all()]);
        $err = $this->validate($request->all(), $rules);
        if ($err) return $err;

        $pujaData = [
            'action_area'           => $request->in_newtown ? $request->action_area : null,
            'category'              => $request->in_newtown ? $request->category : null,
            'puja_committee_name'   => $request->puja_committee_name,
            'puja_committee_address' => $request->puja_committee_address,
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

        $puja = PujaCommittee::create($pujaData);
        return $this->ok('Registration Successful',["data"=>$puja->token]);
    }

    public function update(Request $request, $id)
    {
        $puja = PujaCommittee::find($id);
        if (!$puja) return $this->err("No Such Puja");
		
		if ($request->in_newtown) {
			if (strtolower($request->input('puja_committee_name')) === 'other') {
				$nm = $request->input('puja_committee_name_other');
			} else {
				$nm = $request->input('puja_committee_name');
			}
		} else {
			$nm = $request->input('puja_committee_name_text');
		}
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
        $puja->puja_committee_address = $request->puja_committee_address;
        $puja->secretary_name         = $request->secretary_name;
        $puja->secretary_mobile       = $request->secretary_mobile;
        $puja->chairman_name          = $request->chairman_name;
        $puja->chairman_mobile        = $request->chairman_mobile;
        $puja->proposed_immersion_date = $request->proposed_immersion_date;
        $puja->proposed_immersion_time = $request->proposed_immersion_time;
        $puja->vehicle_no             = $request->vehicle_no;
        $puja->team_members           = $request->dhunuchi ? $request->team_members : null;
        $puja->save();

        $actionArea = $request->in_newtown
            ? ActionArea::where('name', $request->action_area)->first()
            : null;

        $category = $request->in_newtown
            ? PujaCategorie::where('name', $request->category)->first()
            : null;
            
        if ($request->in_newtown && $actionArea && $category) {
            $repo = PujaCommitteeRepo::firstOrNew([
                'name'             => $nm,
                'action_area_id'   => $actionArea->id,
                'puja_category_id' => $category->id,
            ]);
            // always update address if given
            if ($request->filled('puja_committee_address')) {
                $repo->puja_address = $request->puja_committee_address;
            }
            $repo->save();
        }

        return $this->ok('Puja Saved Successfully');
    }

	public function thanks($token) 
	{
        $puja = PujaCommittee::where('token', $token)->first();
        if (!$puja) abort(404, 'INVALID puja');
		return view("thanks",compact("puja"));
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

    public function gpass($token)
    {
        $puja = PujaCommittee::where('token', $token)->first();
        if (!$puja) abort(404, 'INVALID puja');
        $path = public_path('qrs');
        if (!file_exists($path)) mkdir($path, 0755, true);
        $file = $path . '/' . $puja->token . '.png';
        $this->qrGen($file, $puja->secretary_mobile);
        return view('puja.gatepass', compact('puja'));
    }

    public function downloadPdf($token)
    {
        $puja = PujaCommittee::where('token', $token)->first();
        if (!$puja) abort(404, 'Puja not found');
        $file = public_path("qrs/{$puja->token}.png");
        $this->qrGen($file, $puja->secretary_mobile);
        $pdf = Pdf::loadView('puja.gatepass-pdf', compact('puja', 'file'))
            ->setPaper([0,0,297,420],'landscape');// A4 quarter = A6
        return $pdf->download("{$puja->secretary_mobile}.pdf");
    }

    public function has_entryslip($id) 
    {
        $puja = PujaCommittee::find($id);
        if (!$puja) return $this->err("No Such Puja");
        $repoAtt = Attendance::where('puja_committee_id', $puja->id)
            ->where('typ', 'in')->first();
        if (!$repoAtt) return $this->err("Not scanned by operator post yet");
        return $this->ok("Ok",["data"=>$puja->secretary_mobile,"repoAtt"=>$repoAtt]);
    }

    public function entryslip($id) 
    {
        $puja = PujaCommittee::where('secretary_mobile', $id)->first();
        if (!$puja) abort(404, 'Puja not found');
        $repoAtt = Attendance::where('puja_committee_id', $puja->id)
            ->where('typ', 'in')->first();
        //if (!$repoAtt) abort(404, 'Yet not reported');
        $file = public_path("qrs/{$puja->id}.png");
        $this->qrGen($file, $puja->secretary_mobile);
        return view("puja.entryslip", compact('puja', 'file', 'repoAtt'));
    }

    public function scan()
    {
        $puja = $this->getUserObj();
        return view('puja.scan', compact('puja'));
    }

    public function save_settings(Request $request)
    {
        Log::info("save_settings", $request->all());
        foreach ($request->except(['_token', '_method']) as $key => $val) {
            set_setting($key, $val);
        }
        return $this->ok("Saved Successfully");
    }


}
