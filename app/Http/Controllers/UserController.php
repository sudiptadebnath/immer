<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function data(Request $request)
    {
        $query = User::orderBy('created_at', 'desc')->get()
            ->map(function ($row) {
                $row['stat'] = statDict()[$row->stat] ?? $row->stat;
                $row['role'] = roleDict()[$row->role] ?? $row->role;
                return $row;
            });

        return DataTables::of($query)->make(true);
    }

    public function get($id)
    {
        $user = User::find($id);
        if (!$user) return $this->err("No Such User");
        return $this->ok("User Detail", ["data" => $user]);
    }

    public function login(Request $request)
    {
        $err = $this->validate($request->all(), [
            'mob' => ['required', 'regex:/^[6-9]\d{9}$/'],
            'password' => 'required|min:4',
        ]);
        if ($err) return $err;
        $user = User::where('secretary_mobile', $request->mob)->first();
        if ($user && Hash::check($request->password, $user->password)) {
            if ($user->stat == "i") {
                return $this->err("Account is inactive");
            }
            $user->logged_at = now();
            $user->save();
            $this->setUser($user);
            return $this->ok('Login Successful');
        }
        return $this->err("Invalid Login Attempt");
    }

    public function register(Request $request)
    {
        $isAdminAdd = $request->filled('role');

        $rules = [
            'puja_committee_name'   => 'required|string|min:3|max:100|unique:users,puja_committee_name',
            'puja_committee_address' => 'nullable|string|min:3|max:200',
            'secretary_name'        => 'required|string|min:3|max:100',
            'secretary_mobile'      => 'required|string|min:8|max:20|unique:users,secretary_mobile',
            'chairman_name'         => 'required|string|min:3|max:100',
            'chairman_mobile'       => 'required|string|min:8|max:20|unique:users,chairman_mobile',
            'proposed_immersion_date' => 'required|date',
            'proposed_immersion_time' => 'required|string',
            'vehicle_no'            => 'nullable|string|min:3|max:50',
            'team_members'          => 'nullable|integer|min:1|max:100',
            'password'              => 'required|string|min:6',
            'password2'             => 'required|same:password',
        ];

        if ($isAdminAdd) {
            $rules['role'] = 'required|string';
        }

        $err = $this->validate($request->all(), $rules);
        if ($err) return $err;

        $userData = [
            'newtown'               => $request->in_newtown,
            'action_area'           => $request->action_area,
            'category'              => $request->category,
            'puja_committee_name'   => $request->puja_committee_name,
            'puja_committee_address' => $request->puja_committee_address,
            'secretary_name'        => $request->secretary_name,
            'secretary_mobile'      => $request->secretary_mobile,
            'chairman_name'         => $request->chairman_name,
            'chairman_mobile'       => $request->chairman_mobile,
            'proposed_immersion_date' => $request->proposed_immersion_date,
            'proposed_immersion_time' => $request->proposed_immersion_time,
            'vehicle_no'            => $request->vehicle_no,
            'team_members'          => $request->team_members,
            'password'              => $request->password, // auto-hashed in model
            'logged_at'             => now(),
        ];

        if ($isAdminAdd) {
            $userData['role'] = $request->role;
        }

        $user = User::create($userData);

        if (!$isAdminAdd) {
            $this->setUser($user);
        }

        return $this->ok($isAdminAdd ? 'Registration Successful' : 'Registration Successful');
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) return $this->err("No Such User");

        $err = $this->validate($request->all(), [
            'puja_committee_name'   => 'required|string|min:3|max:100|unique:users,puja_committee_name,' . $id,
            'puja_committee_address' => 'nullable|string|min:3|max:200',
            'secretary_name'        => 'required|string|min:3|max:100',
            'secretary_mobile'      => 'required|string|min:8|max:20|unique:users,secretary_mobile,' . $id,
            'chairman_name'         => 'required|string|min:3|max:100',
            'chairman_mobile'       => 'required|string|min:8|max:20|unique:users,chairman_mobile,' . $id,
            'proposed_immersion_date' => 'required|date',
            'proposed_immersion_time' => 'required|string',
            'vehicle_no'            => 'nullable|string|min:3|max:50',
            'team_members'          => 'nullable|integer|min:1|max:100',
            'role'                  => 'required|string',
            'stat'                  => 'required|string',
        ]);
        if ($err) return $err;

        $user->newtown                = $request->in_newtown;
        $user->action_area            = $request->action_area;
        $user->category               = $request->category;
        $user->puja_committee_name    = $request->puja_committee_name;
        $user->puja_committee_address = $request->puja_committee_address;
        $user->secretary_name         = $request->secretary_name;
        $user->secretary_mobile       = $request->secretary_mobile;
        $user->chairman_name          = $request->chairman_name;
        $user->chairman_mobile        = $request->chairman_mobile;
        $user->proposed_immersion_date = $request->proposed_immersion_date;
        $user->proposed_immersion_time = $request->proposed_immersion_time;
        $user->vehicle_no             = $request->vehicle_no;
        $user->team_members           = $request->team_members;
        $user->role                   = $request->role;
        $user->stat                   = $request->stat;

        if (!empty($request->password)) {
            $user->password = $request->password; // auto-hashed by model
        }

        $user->save();
        return $this->ok('User Saved Successfully');
    }

    public function delete($id)
    {
        $user = User::find($id);
        if (!$user) return $this->err("No Such User");
        $user->delete();
        return $this->ok('User deleted successfully');
    }

    private function qrGen($file, $tok)
    {
        Log::info($file, ["tok" => $tok]);
        if (file_exists($file)) return;
        $qrCode = new QrCode($tok);
        $writer = new PngWriter();
        $result = $writer->write($qrCode);
        $result->saveToFile($file);
    }

    public function gpass($id)
    {
        $user = User::find($id);
        if (!$user) abort(404, 'INVALID user');
        $path = public_path('qrs');
        if (!file_exists($path)) mkdir($path, 0755, true);
        $file = $path . '/' . $user->id . '.png';
        $this->qrGen($file, $user->token);
        return view('user.gatepass', compact('user'));
    }

    public function downloadPdf($id)
    {
        $user = User::find($id);
        if (!$user) abort(404, 'User not found');
        $file = public_path("qrs/{$user->id}.png");
        $this->qrGen($file, $user->token);
        $pdf = Pdf::loadView('user.gatepass-pdf', compact('user', 'file'));
        return $pdf->download("GatePass-{$user->uid}.pdf");
    }

    public function scan()
    {
        $user = $this->getUserObj();
        return view('user.scan', compact('user'));
    }

    public function attendance(Request $request)
    {
        $cuser = $this->getUserObj();
        $request->validate([
            'token'    => 'required|string',          // scanned QR token or UID
            'post'     => 'required|string|max:30',
            'typ'      => 'required|string|max:10',
            'location' => 'nullable|string|max:255',
        ]);

        // Find the user by token
        $user = User::where('token', $request->token)->first();

        if (!$user) return $this->err("GatePass not exists");

        $today = Carbon::today();
        $exists = Attendance
            ::where('scan_by', $cuser->id)
            ->where('user_id', $user->id)
            ->whereDate('scan_datetime', $today)
            ->where('post', $request->post ?? 'post1')
            ->where('typ', $request->typ ?? 'att')
            ->exists();
        if ($exists) {
            return $this->err('Duplicate Entry not allowed');
        }

        $typNm = attDict()[$request->typ];
        Attendance::create([
            'scan_datetime' => now(),
            'scan_by'       => $cuser->id,
            'user_id'       => $user->id,
            'post'          => $request->post ?? 'post1',
            'typ'           => $request->typ ?? 'att',
            'location'      => $request->location,
        ]);

        return $this->ok("Marked <b>" . $typNm . "</b> for " . $user->uid);
    }

    public function scanStat(Request $request)
    {
        $gateId = $request->get('gate_id');
        if (!$gateId) return $this->ok("ok", ["data" => []]);
        $today = Carbon::today();
        $scans = Attendance::where('post', $gateId)
            ->whereDate('scan_datetime', $today);
        $qCount  = (clone $scans)->where('typ', 'QUEUE')->count();
        $iCount  = (clone $scans)->where('typ', 'IN')->count();
        $oCount  = (clone $scans)->where('typ', 'OUT')->count();
        $stats = [
            ['name' => 'QUEUE', 'count' => $qCount, 'color' => 'danger'],
            ['name' => 'IN',    'count' => $iCount, 'color' => 'success'],
            ['name' => 'OUT',   'count' => $oCount, 'color' => 'primary'],
        ];
        return $this->ok("ok", ["data" => $stats]);
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
