<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function data()
    {
        $cuser = $this->getUserObj();
        $query = User::where('id', '!=', $cuser->id)
            ->where('role','!=','a')
            ->orderBy('created_at', 'desc');
        return DataTables::of($query)
            ->editColumn('role', function ($row) {
                return roleDict()[$row->role] ?? $row->role;
            })
            // allow partial search for role
            ->filterColumn('role', function ($query, $keyword) {
                $map = roleDict();
                $matchedKeys = [];
                foreach ($map as $key => $val) {
                    if (stripos($val, $keyword) !== false) {
                        $matchedKeys[] = $key;
                    }
                }
                if ($matchedKeys) {
                    $query->whereIn('role', $matchedKeys);
                }
            })
            // show stat as text
            ->editColumn('stat', function ($row) {
                return statDict()[$row->stat] ?? $row->stat;
            })
            // allow partial search for stat
            ->filterColumn('stat', function ($query, $keyword) {
                $map = statDict();
                $matchedKeys = [];
                foreach ($map as $key => $val) {
                    if (stripos($val, $keyword) !== false) {
                        $matchedKeys[] = $key;
                    }
                }
                if ($matchedKeys) {
                    $query->whereIn('stat', $matchedKeys);
                }
            })
            // format logged_at nicely
            ->editColumn('logged_at', function ($row) {
                return $row->logged_at
                    ? Carbon::parse($row->logged_at)->format('d/m/Y H:i')
                    : '';
            })
            // allow partial search for logged_at
            ->filterColumn('logged_at', function ($query, $keyword) {
                $query->whereRaw("DATE_FORMAT(logged_at, '%d/%m/%Y %H:%i') LIKE ?", ["%{$keyword}%"]);
            })
            ->make(true);
    }

    public function get($id)
    {
        $user = User::find($id);
        if (!$user) return $this->err("No Such User");
        return $this->ok("User Detail", ["data" => $user]);
    }

    public function login(Request $request)
    {

        /*for ($i = 10; $i <= 20; $i++) {
            User::create([
                'name'     => "Aprator{$i}",
                'email'    => "Aprator{$i}@mail.com",
                'phone'    => "92" . str_pad($i, 8, '0', STR_PAD_LEFT),
                'password' => 'abc123&',
                'role'     => 'o',
            ]);

            User::create([
                'name'     => "Janner{$i}",
                'email'    => "Janner{$i}@mail.com",
                'phone'    => "93" . str_pad($i, 8, '0', STR_PAD_LEFT),
                'password' => 'abc123&',
                'role'     => 's',
            ]);
        }*/

        $err = $this->validate($request->all(), [
            'email' => 'required|email',
            'password' => [
                'required',
                'min:2',
                //'min:6',
                //'regex:/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*?&]).+$/'
            ],
        ], [
            'password.*' => 'Password must contain at least 1 letter, 1 number, and 1 special character',
        ]);

        if ($err) return $err;
        $user = User::where('email', $request->email)->first();
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

    public function add(Request $request)
    {
        $rules = [
            'email'                 => 'required|email|unique:users,email',
            'name'                  => 'required|string|min:2|max:20|unique:users,name',
            'phone'                  => 'nullable|string|min:10|max:20|unique:users,phone',
            'password'              => 'required|string|min:6',
            'password2'             => 'required|same:password',
            'role'                  => 'required|string',
            'stat'                  => 'required|string',
        ];
        $err = $this->validate($request->all(), $rules);
        if ($err) return $err;
        $userData = [
            'email'     => $request->email,
            'name'      => $request->name,
            'phone'      => $request->phone,
            'password'  => $request->password,
            'role'      => $request->role, 
            'stat'      => $request->stat, 
        ];
        User::create($userData);
        return $this->ok('User Added Successfully');
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) return $this->err("No Such User");
        $err = $this->validate($request->all(), [
            'email'                 => 'required|email|unique:users,email,' . $id,
            'name'                  => 'required|string|min:2|max:20|unique:users,name,' . $id,
            'phone'                  => 'nullable|string|min:10|max:20|unique:users,phone,' . $id,
            'password'              => 'nullable|string|min:6',
            'password2'             => 'nullable|same:password',
        ]);
        if ($err) return $err;

        $user->email            = $request->email;
        $user->name            = $request->name;
        $user->phone            = $request->phone;
        if (!empty($request->password)) $user->password = $request->password;
        if (!empty($request->role)) $user->role = $request->role;
        if (!empty($request->stat)) $user->stat = $request->stat;
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


}
