<?php

namespace App\Http\Controllers;

use App\Models\ActionArea;
use App\Models\AppLog;
use App\Models\ImmersionDate;
use App\Models\PujaCategorie;
use App\Models\PujaCommitteeRepo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;

class ConfController extends Controller
{

    /* ==================== SERVICES FOR ACTION AREA ====================================*/
    public function data_action()
    {
        return DataTables::of(ActionArea::query())->make(true);
    }
    public function get_action($id)
    {
        return $this->ok("Record",["data"=>ActionArea::find($id)]);
    }
    public function updateorder_action(Request $request)
    {
		if (is_array($request->order)) {
			foreach ($request->order as $item) {
				ActionArea::where('id', $item['id'])
				->update(['view_order' => $item['position']]);
			}
		}
        return $this->ok('Record ordered successfully');
    }
    public function add_action(Request $request)
    {
        $err = $this->validate($request->all(), [
            'name' => 'required|string|max:50|unique:action_areas,name',
        ]);
        if ($err) return $err;
        ActionArea::create([
            'name' => $request->name,
        ]);
        return $this->ok('Record Added Successfully');
    } 
    public function edit_action(Request $request, $id)
    {
        $rec = ActionArea::find($id);
        if (!$rec) return $this->err("No Such Record");
        $err = $this->validate($request->all(), [
            'name' => 'required|string|max:50|unique:action_areas,name,'.$id,
        ]);
        if ($err) return $err;
        $rec->name = $request->name;
        $rec->save();
        return $this->ok('Record Saved Successfully');
    } 
    public function del_action($id)
    {
        $rec = ActionArea::find($id);
        if (!$rec) return $this->err("No Such Record");
        $rec->delete();
        return $this->ok('Record Deleted Successfully');
    } 


    /* ==================== SERVICES FOR category ====================================*/
    public function data_category()
    {
        return DataTables::of(PujaCategorie::query()->orderBy('view_order', 'asc'))->make(true);
    }
    public function get_category($id)
    {
        return $this->ok("Record",["data"=>PujaCategorie::find($id)]);
    }
    public function updateorder_category(Request $request)
    {
		if (is_array($request->order)) {
			foreach ($request->order as $item) {
				PujaCategorie::where('id', $item['id'])
				->update(['view_order' => $item['position']]);
			}
		}
        return $this->ok('Record ordered successfully');
    }
    public function add_category(Request $request)
    {
        $err = $this->validate($request->all(), [
            'name' => 'required|string|max:50|unique:puja_categories,name',
        ]);
        if ($err) return $err;
        PujaCategorie::create([
            'name' => $request->name,
        ]);
        return $this->ok('Record Added Successfully');
    } 
    public function edit_category(Request $request, $id)
    {
        $rec = PujaCategorie::find($id);
        if (!$rec) return $this->err("No Such Record");
        $err = $this->validate($request->all(), [
            'name' => 'required|string|max:50|unique:puja_categories,name,'.$id,
        ]);
        if ($err) return $err;
        $rec->name = $request->name;
        $rec->save();
        return $this->ok('Record Saved Successfully');
    } 
    public function del_category($id)
    {
        $rec = PujaCategorie::find($id);
        if (!$rec) return $this->err("No Such Record");
        $rec->delete();
        return $this->ok('Record Deleted Successfully');
    } 


    /* ==================== SERVICES FOR committee ====================================*/
    public function data_committee(Request $request)
    {
        $query = PujaCommitteeRepo::select(
                'puja_committies_repo.*',
                'action_areas.name as action_area',
                'puja_categories.name as category'
            )
            ->leftJoin('action_areas', 'action_areas.id', '=', 'puja_committies_repo.action_area_id')
            ->leftJoin('puja_categories', 'puja_categories.id', '=', 'puja_committies_repo.puja_category_id');
        if(!$request->has('order')) $query->orderBy("view_order");
        return DataTables::of($query)->make(true);
    }

    public function get_committee($id)
    {
        return $this->ok("Record",["data"=>
		PujaCommitteeRepo::with(['actionArea', 'pujaCategory'])->find($id)]);
    }
    public function get_committees(Request $request)
    {
		$q = PujaCommitteeRepo::query();
		if ($request->action_area) {
			$q->whereHas('actionArea', function ($sub) use ($request) {
				$sub->where('name', $request->action_area);
			});
		}
		if ($request->category) {
			$q->whereHas('pujaCategory', function ($sub) use ($request) {
				$sub->where('name', $request->category);
			});
		}
		$committees = $q->orderBy('view_order', 'asc')->get();
		return $this->ok("Records",["data"=>$committees]);
    }
    public function updateorder_committee(Request $request)
    {
		if (is_array($request->order)) {
			foreach ($request->order as $item) {
				PujaCommitteeRepo::where('id', $item['id'])
				->update(['view_order' => $item['position']]);
			}
		}
        return $this->ok('Record ordered successfully');
    }
    public function add_committee(Request $request)
    {
        $err = $this->validate($request->all(), [
			'action_area' => 'required|exists:action_areas,id',
			'category' => 'required|exists:puja_categories,id',
			'name' => 'required|string|max:200|unique:puja_committies_repo,name',
			'puja_committee_address' => 'nullable|string|max:300',
        ], [
			'action_area.required' => 'Please select an Action Area.',
			'action_area.exists' => 'Selected Action Area is invalid.',
			'category.required' => 'Please select a Category.',
			'category.exists' => 'Selected Category is invalid.',
			'name.required' => 'Committee name is required.',
			'name.string' => 'Committee name must be text.',
			'name.max' => 'Committee name may not exceed 200 characters.',
			'name.unique' => 'This committee name already exists.',
			'puja_committee_address.string' => 'Address must be text.',
			'puja_committee_address.max' => 'Address may not exceed 300 characters.',
		]);
        if ($err) return $err;
        PujaCommitteeRepo::create([
			'action_area_id' => $request->action_area,
			'puja_category_id' => $request->category,
			'name' => $request->name,
			'puja_address' => $request->puja_committee_address,
        ]);
        return $this->ok('Record Added Successfully');
    } 
    public function edit_committee(Request $request, $id)
    {
        $rec = PujaCommitteeRepo::find($id);
        if (!$rec) return $this->err("No Such Record");
        $err = $this->validate($request->all(),[
			'action_area' => 'required|exists:action_areas,id',
			'category' => 'required|exists:puja_categories,id',
			'name' => 'required|string|max:200|unique:puja_committies_repo,name,' . $id,
			'puja_committee_address' => 'nullable|string|max:300',
			'view_order' => 'nullable|integer|min:0',
		], [
			'action_area.required' => 'Please select an Action Area.',
			'action_area.exists' => 'Selected Action Area is invalid.',

			'category.required' => 'Please select a Category.',
			'category.exists' => 'Selected Category is invalid.',

			'name.required' => 'Committee name is required.',
			'name.string' => 'Committee name must be text.',
			'name.max' => 'Committee name may not exceed 200 characters.',
			'name.unique' => 'This committee name already exists.',

			'puja_committee_address.string' => 'Address must be text.',
			'puja_committee_address.max' => 'Address may not exceed 300 characters.',

			'view_order.integer' => 'View order must be a number.',
			'view_order.min' => 'View order cannot be negative.',
		]);
        if ($err) return $err;
		$rec->action_area_id = $request->action_area;
		$rec->puja_category_id = $request->category;
		$rec->name = $request->name;
		$rec->puja_address = $request->puja_committee_address;
		$rec->save();
        return $this->ok('Record Saved Successfully');
    } 
    public function del_committee($id)
    {
        $rec = PujaCommitteeRepo::find($id);
        if (!$rec) return $this->err("No Such Record");
        $rec->delete();
        return $this->ok('Record Deleted Successfully');
    } 


    /* ==================== SERVICES FOR immerdt ====================================*/
    public function data_immerdt()
    {
        return DataTables::of(
                ImmersionDate::query()
            )
            ->editColumn('idate', function ($row) {
                return $row->idate?->format('d-m-Y');
            })
            ->filterColumn('idate', function ($query, $keyword) {
                $query->whereRaw("DATE_FORMAT(idate, '%d-%m-%Y') like ?", ["%{$keyword}%"]);
            })
            ->make(true);
    }
    public function get_immerdt($id)
    {
        $data = ImmersionDate::find($id);
        return $this->ok("Record", [
            "data" => [
                "id"    => $data->id,
                "name"  => $data->name,
                "idate" => $data->idate?->format('d-m-Y'),
            ]
        ]);
    }
    public function add_immerdt(Request $request)
    {
        $err = $this->validate($request->all(), [
            'name' => 'required|string|max:50|unique:puja_immersion_dates,name',
            'idate' => 'required|date|unique:puja_immersion_dates,idate',
        ]);
        if ($err) return $err;
        ImmersionDate::create([
            'name' => $request->name,
            'idate' => $request->idate,
        ]);
        return $this->ok('Record Added Successfully');
    } 
    public function edit_immerdt(Request $request, $id)
    {
        $rec = ImmersionDate::find($id);
        if (!$rec) return $this->err("No Such Record");
        $err = $this->validate($request->all(), [
            'name' => 'required|string|max:50|unique:puja_immersion_dates,name,'.$id,
            'idate' => 'required|date|unique:puja_immersion_dates,idate,'.$id,
        ]);
        if ($err) return $err;
        $rec->name = $request->name;
        $rec->idate = $request->idate;
        $rec->save();
        return $this->ok('Record Saved Successfully');
    } 
    public function del_immerdt($id)
    {
        $rec = ImmersionDate::find($id);
        if (!$rec) return $this->err("No Such Record");
        $rec->delete();
        return $this->ok('Record Deleted Successfully');
    } 


    public function save_settings(Request $request)
    {
        Log::info("save_settings", $request->all());
        foreach ($request->except(['_token', '_method']) as $key => $val) {
            set_setting($key, $val);
        }
        return $this->ok("Saved Successfully");
    }

    public function data_logs()
    {
        return DataTables::of(AppLog::query())
        ->rawColumns(['context'])
        ->editColumn('created_at', function ($row) {
            return $row->created_at?->timezone('Asia/Kolkata')->format('d-m-Y H:i:s');
        })
        ->make(true);
    }

    public function del_logs(Request $request)
    {
        $search = $request->input('search', '');
        $query = AppLog::query();
        $query->where(function($q) use ($search) {
            $q->where('ip', 'like', "%$search%")
            ->orWhere('user', 'like', "%$search%")
            ->orWhere('name', 'like', "%$search%")
            ->orWhere('action', 'like', "%$search%")
            ->orWhere('reaction', 'like', "%$search%")
            ->orWhere('context', 'like', "%$search%");
        });

        $deleted = $query->delete();
        return $this->ok("$deleted Record Purged Successfully");
    }
}
