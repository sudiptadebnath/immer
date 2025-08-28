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
                $row['address'] = $row->address ?? '';
                $row['stat'] = statDict()[$row->stat] ?? $row->role;
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
            'email' => ['required', function ($attribute, $value, $fail) {
                if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
                } elseif (!preg_match('/^[a-zA-Z0-9@._-]+$/', $value)) {
                    $fail('User ID must be alphanumeric and may include @ . _ - characters.');
                }
            }],
            'password' => 'required',
        ]);
        if ($err) return $err;

        $user = User
            ::where('email', $request->email)
            ->orWhere('uid', $request->email)
            ->first();

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
            'uid' => 'required|string|min:4|max:20|regex:/^[a-zA-Z0-9._-]+$/|unique:users,uid',
            'name' => 'required|string|min:3|max:150|unique:users,name',
            'address' => 'required|string|min:3|max:250',
            'email' => 'required|email|unique:users,email',
            'mob' => 'required|string|min:8|max:20|unique:users,mob',
            'password' => 'required|string|min:6',
            'password2' => 'required|same:password',
        ];

        if ($isAdminAdd) {
            $rules['name'] = 'nullable|string|min:3|max:150|unique:users,name';
            $rules['address'] = 'nullable|string|min:3|max:250';
            $rules['email'] = 'nullable|email|unique:users,email';
            $rules['mob'] = 'nullable|string|min:8|max:20|unique:users,mob';
            $rules['role'] = 'required';
        }

        $err = $this->validate($request->all(), $rules);
        if ($err) return $err;

        $userData = [
            'uid' => $request->uid,
            'name' => $request->name,
            'address' => $request->address,
            'email' => $request->mail,
            'mob' => $request->mob,
            'password' => $request->password, // auto-hashed by model mutator
            'logged_at' => now(),
        ];

        if ($isAdminAdd) {
            $userData['role'] = $request->role;
        }

        $user = User::create($userData);

        // Auto login only if self-registered
        if (!$isAdminAdd) {
            $this->setUser($user);
        }

        return $this->ok($isAdminAdd ? 'User Saved Successfully' : 'Registration Successful');
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) return $this->err("No Such User");

        $err = $this->validate($request->all(), [
            'uid' => 'required|string|min:4|max:20|regex:/^[a-zA-Z0-9._-]+$/|unique:users,uid,' . $id,
            'name' => 'required|string|min:3|max:150|unique:users,name,' . $id,
            'address' => 'required|string|min:3|max:250',
            'email' => 'required|email|unique:users,email,' . $id,
            'mob' => 'required|string|min:8|max:20|unique:users,mob,' . $id,
        ]);
        if ($err) return $err;

        // Update fields
        $user->uid = $request->uid;
        $user->name = $request->name;
        $user->address = $request->address;
        $user->email = $request->email;
        $user->mob = $request->mob;
        $user->role = $request->role;
        $user->stat = $request->stat;

        // Only update password if it's provided
        if (!empty($request->password)) {
            $user->password = $request->password; // auto-hashed
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
        $inCount  = (clone $scans)->where('typ', 'IN')->count();
        $outCount = (clone $scans)->where('typ', 'OUT')->count();
        $insideCount = $inCount - $outCount;
        $stats = [
            ['name' => 'IN',     'count' => $inCount,    'color' => 'success'],
            ['name' => 'OUT',    'count' => $outCount,   'color' => 'danger'],
            ['name' => 'Inside',   'count' => $insideCount, 'color' => 'primary'],
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
