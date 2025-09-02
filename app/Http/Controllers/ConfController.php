<?php

namespace App\Http\Controllers;

use App\Models\ActionArea;
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
        return DataTables::of(ActionArea::query()->orderBy('view_order', 'asc'))->make(true);
    }
    public function get_action($id)
    {
        return $this->ok("Record",["data"=>ActionArea::find($id)]);
    }
    public function updateorder_action(Request $request)
    {
        foreach ($request->order as $item) {
            ActionArea::where('id', $item['id'])
            ->update(['view_order' => $item['position']]);
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
            'name' => strip_tags($request->name),
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
        $rec->name = strip_tags($request->name);
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
        foreach ($request->order as $item) {
            PujaCategorie::where('id', $item['id'])
            ->update(['view_order' => $item['position']]);
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
            'name' => strip_tags($request->name),
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
        $rec->name = strip_tags($request->name);
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
    public function data_committee()
    {
        return DataTables::of(PujaCommitteeRepo::query()->orderBy('view_order', 'asc'))->make(true);
    }
    public function get_committee($id)
    {
        return $this->ok("Record",["data"=>PujaCommitteeRepo::find($id)]);
    }
    public function updateorder_committee(Request $request)
    {
        foreach ($request->order as $item) {
            PujaCommitteeRepo::where('id', $item['id'])
            ->update(['view_order' => $item['position']]);
        }
        return $this->ok('Record ordered successfully');
    }
    public function add_committee(Request $request)
    {
        $err = $this->validate($request->all(), [
            'name' => 'required|string|max:50|unique:puja_committies_repo,name',
        ]);
        if ($err) return $err;
        PujaCommitteeRepo::create([
            'name' => strip_tags($request->name),
        ]);
        return $this->ok('Record Added Successfully');
    } 
    public function edit_committee(Request $request, $id)
    {
        $rec = PujaCommitteeRepo::find($id);
        if (!$rec) return $this->err("No Such Record");
        $err = $this->validate($request->all(), [
            'name' => 'required|string|max:50|unique:puja_committies_repo,name,'.$id,
        ]);
        if ($err) return $err;
        $rec->name = strip_tags($request->name);
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
                ImmersionDate::query()->orderBy('idate', 'asc')
            )
            ->editColumn('idate', function ($row) {
                return $row->idate?->format('d-m-Y');
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
            'name' => strip_tags($request->name),
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
        $rec->name = strip_tags($request->name);
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
}
