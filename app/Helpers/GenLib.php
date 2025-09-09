<?php

use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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
            "1" => "Ghat 1",
        ];
        return $typ == 1 ? array_flip($dict) : $dict;
    }
}

if (!function_exists('attDict')) {
    function attDict($typ = 0)
    {
        $dict = [
            "queue" => "Queued",
            "in" => "Reported",
            "out" => "Immersion Done",
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

if (!function_exists('setting')) {
    function setting(string|array $key, $def = "")
    {
        $dbkey = is_array($key) ? array_shift($key) : $key;
        $val = Setting::where('key', $dbkey)->value('val');
        if ($val == null) return $def;
        if (!is_array($key) || empty($key)) return $val;
        $valjson = json_decode($val, true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($valjson)) {
            return $def;
        }
        $result = $valjson;
        foreach ($key as $pathKey) {
            if (is_array($result) && array_key_exists($pathKey, $result)) {
                $result = $result[$pathKey];
            } else {
                return $def;
            }
        }
        return $result;
    }
}

if (!function_exists('set_setting')) {
    function set_setting(string $key, $val)
    {
        if (is_array($val) || is_object($val)) {
            $val = json_encode($val, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }
        return Setting::updateOrCreate(
            ['key' => $key],
            ['val' => $val]
        );
    }
}

if (!function_exists('stoa')) {
    function stoa(string $s, $del="~~")
    {
        $out=[];
        foreach(explode($del, $s) as $itm) $out[$itm] = $itm;
        return $out;
    }
}


if (!function_exists('dbVals')) {
    function dbVals(string $tbl, string|array $fld = "name", string $orderBy = "", string $direction = "asc"): array
    {
        $out = [];

        if (is_array($fld)) {
            $query = DB::table($tbl)->select($fld);

            if ($orderBy) {
                $query->orderBy($orderBy, $direction);
            }

            $rows = $query->get();

            foreach ($rows as $row) {
                $key = $row->{$fld[0]};
                $vals = [];
                foreach (array_slice($fld, 1) as $f) {
                    $vals[] = $row->$f;
                }
                $out[$key] = implode(" - ", $vals);
            }
        } else {
            $query = DB::table($tbl)->select($fld);

            if ($orderBy) {
                $query->orderBy($orderBy, $direction);
            }

            $rows = $query->pluck($fld)->toArray();

            foreach ($rows as $val) {
                $out[$val] = $val;
            }
        }

        return $out;
    }
}
