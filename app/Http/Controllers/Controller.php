<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

abstract class Controller
{
    protected function ok($msg = "Ok", $opt = [], $status = 200)
    {
        $ans = ["success" => true, "msg" => $msg];
        if ($opt) $ans = array_merge($ans, $opt);
        return response()->json($ans, $status);
    }

    protected function err($msg, $opt = [], $status = 200)
    {
        $ans = ["success" => false, "msg" => "SERVER: $msg"];
        if ($opt) $ans = array_merge($ans, $opt);
        return response()->json($ans, $status);
    }

    protected function raw($data)
    {
        return response()->json($data);
    }

    public function hello()
    {
        return $this->ok("hello");
    }

    protected function validate($itms, $ruls)
    {
        $validator = Validator::make($itms, $ruls);

        if ($validator->fails()) {
            return $this->err(implode("<br>", $validator->errors()->all()));
        }

        return false;
    }

    protected function setUser($user)
    {
        $data = $user->toArray();
        $data['token'] = $user->token;
        Session::put('user', $data);
    }
    protected function getUser()
    {
        return Session::get('user', []);
    }
    protected function getUserObj()
    {
        $user = Session::get('user');

        if ($user && isset($user['id'])) {
            return User::find($user['id']);
        }

        return null;
    }
    //
}
