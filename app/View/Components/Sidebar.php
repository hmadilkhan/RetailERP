<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Support\Facades\DB;

class Sidebar extends Component
{
    public function __construct()
    {
        //
    }

    public function render()
    {
        $roleId = session('roleId');

        // Impersonate ke waqt impersonated user ka actual role DB se lo
        if (app('impersonate')->isImpersonating()) {
            $roleId = DB::table('user_authorization')
                ->where('user_id', auth()->id())
                ->value('role_id') ?? $roleId;
        }

        $checkPackage = DB::table("company")->where("company_id", session("company_id"))->whereNotNull("package_id")->get();
        if (count($checkPackage) > 0) {
            $pageid = DB::select('SELECT page_id from role_settings WHERE role_id = ? and page_id IN (SELECT page_id FROM `package_module_permissions` where package_id = ?) ORDER BY page_id;', [$roleId, $checkPackage[0]->package_id]);
        } else {
            $pageid = DB::select('SELECT page_id from role_settings WHERE role_id = ? ORDER BY page_id', [$roleId]);
        }

        $array = [];
        foreach ($pageid as $value) {
            array_push($array, $value->page_id);
        }

        $result = DB::table('pages_details')->whereIN('id', $array)->get();

        return view('components.sidebar', compact('result'));
    }
}
