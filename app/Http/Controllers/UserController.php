<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
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
        $data = $query->get()->map(function ($row) {
            $row['stat'] = statDict()[$row->stat] ?? $row->stat;
            $row['role'] = roleDict()[$row->role] ?? $row->role;
            return $row;
        });
        return DataTables::of($data)->make(true);
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
