<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PujaCommitteeRepo;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;
use App\Services\SmsService;

class UserController extends Controller
{
    public function data()
    {
        $cuser = $this->getUserObj();
        $query = User::where('id', '!=', $cuser->id)
            ->where('role','!=','a');
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

    public function login(Request $request/*,SmsService $sms*/)
    {
		//$sms->send("9830371026","98656");
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
		/*$committees = [
			['name' => 'New Town Animikha Abasan Sharodotsav Committee', 'puja_address' => 'Animikha Abasan', 'action_area' => 1, 'category' => 1],
			['name' => 'New Town (1CA to 1CD Blocks) Residents Welfare Association', 'puja_address' => '1CA to 1CD Blocks', 'action_area' => 1, 'category' => 2],
			['name' => 'DB BLOCK SARBOJANIN DURGOTSOV', 'puja_address' => 'DB Block', 'action_area' => 1, 'category' => 1],
			['name' => 'STARLIT HOUSING OWNERS ASSOCIATION', 'puja_address' => 'Starlit Housing', 'action_area' => 2, 'category' => 1],
			['name' => 'GREENWOOD PARK CULTURAL ASSOCIATION', 'puja_address' => 'Greenwood Park', 'action_area' => 1, 'category' => 1],
			['name' => 'NEW TOWN AC-AD & AA BLOCK PUJA COMMITTEE', 'puja_address' => 'AC-AD & AA Block', 'action_area' => 1, 'category' => 2],
			['name' => 'EASTERN GROVE PUJA COMMITTEE', 'puja_address' => 'Eastern Grove', 'action_area' => 1, 'category' => 1],
			['name' => 'NEWTOWN-BC BLOCK CULTURAL ASSOCIATION', 'puja_address' => 'BC Block', 'action_area' => 1, 'category' => 2],
			['name' => 'SHREE ABASAN SHARADOTSAV COMMITTEE', 'puja_address' => 'Shree Abasan', 'action_area' => 1, 'category' => 1],
			['name' => 'NEWTOWN AA - 1B SARBOJANIN DURGOTSAV COMMITTEE', 'puja_address' => 'AA-1B Block', 'action_area' => 1, 'category' => 2],
			['name' => 'New Town BA Block Cultural & Social Association', 'puja_address' => 'BA Block', 'action_area' => 1, 'category' => 2],
			['name' => 'CE BLOCK CULTURAL ASSOCIATION DURGA PUJA COMMITTEE', 'puja_address' => 'CE Block', 'action_area' => 1, 'category' => 2],
			['name' => 'Millennium club durga puja committee', 'puja_address' => 'Millennium Club', 'action_area' => 1, 'category' => 1],
			['name' => 'A.A. 1D. Sarbojanin Durga Puja Committee', 'puja_address' => 'AA-1D Block', 'action_area' => 1, 'category' => 2],
			['name' => 'NEW TOWN BE BLOCK WELFARE ASSOCIATION', 'puja_address' => 'BE Block', 'action_area' => 1, 'category' => 2],
			['name' => 'Balaka Abasan Puja Committee', 'puja_address' => 'Balaka Abasan', 'action_area' => 1, 'category' => 1],
			['name' => 'Newtown Sarbojanin Durgotsab Samiti', 'puja_address' => 'Newtown', 'action_area' => 1, 'category' => 2],
			['name' => 'JATRAGACHI R.R SIDE SARBO JANIN DURGE UTSAV PUJA COMMITTEE', 'puja_address' => 'Jatragachi R.R Side', 'action_area' => 1, 'category' => 2],
			['name' => 'DA Block Sarbojanin Durgotsab Committee', 'puja_address' => 'DA Block', 'action_area' => 1, 'category' => 2],
		];

		foreach ($committees as $index => $data) {
			PujaCommitteeRepo::create([
				'action_area_id'   => $data['action_area'],   // I = 1, II = 2
				'puja_category_id' => $data['category'],      // Housing = 1, Block = 2
				'name'             => $data['name'],
				'puja_address'     => $data['puja_address'],
				'view_order'       => $index + 1,
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
            'phone'                  => 'nullable|string|min:10|max:100',
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
            'phone'                  => 'nullable|string|min:10|max:100',
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
