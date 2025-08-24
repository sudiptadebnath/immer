<?php

use Carbon\Carbon;

if (!function_exists('hasRole')) {
    function hasRole($roles)
    {
        $user = session('user');
        return $user && isset($user['role']) && str_contains($roles, $user['role']);
    }
}

if (!function_exists('userLogged')) {
    function userLogged()
    {
        return !empty(session('user'));
    }
}

if (!function_exists('getUsrProp')) {
    function getUsrProp($ky)
    {
        return (session('user') ?? [])[$ky] ?? "";
    }
}

if (!function_exists('roleDict')) {
    function roleDict($typ = 0)
    {
        $dict = [
            "a" => "Admin",
            "o" => "Operator",
            "s" => "Scanner",
            "u" => "User",
        ];
        return $typ == 1 ? array_flip($dict) : $dict;
    }
}

if (!function_exists('statDict')) {
    function statDict($typ = 0)
    {
        $dict = [
            "a" => "Active",
            "i" => "Inactive",
        ];
        return $typ == 1 ? array_flip($dict) : $dict;
    }
}

if (!function_exists('postDict')) {
    function postDict($typ = 0)
    {
        $dict = [
            "1" => "Post 1",
            "2" => "Post 2",
            "3" => "Post 3",
            "4" => "Post 4",
        ];
        return $typ == 1 ? array_flip($dict) : $dict;
    }
}

if (!function_exists('attDict')) {
    function attDict($typ = 0)
    {
        $dict = [
            "in" => "In",
            "out" => "Out",
            "tempin" => "Temp In",
            "tempout" => "Temp Out",
            "att" => "Attendance",
        ];
        return $typ == 1 ? array_flip($dict) : $dict;
    }
}

if (!function_exists('dtfmt')) {
    function dtfmt($typ = 0)
    {
        return ["dd-MM-yyyy", "DD-MM-YYYY"][$typ] ?? "dd-MM-yyyy";
    }
}

if (!function_exists('dttmfmt')) {
    function dttmfmt($typ = 0)
    {
        return ["dd-MM-yyyy HH:mm", "DD-MM-YYYY HH:mm"][$typ] ?? "dd-MM-yyyy HH:mm";
    }
}

if (!function_exists('dtsql')) {
    function dtsql($vl, $typ = 0)
    {
        $fmt = ["d-m-Y"][$typ] ?? "d-m-Y";
        try {
            return Carbon::createFromFormat($fmt, $vl)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }
}

if (!function_exists('dttmsql')) {
    function dttmsql($vl, $typ = 0)
    {
        $fmt = ["d-m-Y H:i"][$typ] ?? "d-m-Y H:i";
        try {
            return Carbon::createFromFormat($fmt, $vl)->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            return null;
        }
    }
}

