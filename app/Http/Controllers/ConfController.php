<?php

namespace App\Http\Controllers;

use App\Models\ActionArea;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;

class ConfController extends Controller
{
    public function action_data()
    {
        return DataTables::of(ActionArea::query())->make(true);
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
