<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Support\Facades\DB;

class Sidebar extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        $checkPackage = DB::table("company")->where("company_id", session("company_id"))->whereNotNull("package_id")->get();
        if (count($checkPackage) > 0) {
            $pageid = DB::select('SELECT page_id from role_settings WHERE role_id = ? and page_id IN (SELECT page_id FROM `package_module_permissions` where package_id = ?) ORDER BY page_id;', [session("roleId"), $checkPackage[0]->package_id]);
            // $pageid = DB::select('SELECT page_id from role_settings WHERE role_id = ? and page_id IN (SELECT page_id FROM `module_permissions_details` where company_id = ?) ORDER BY page_id;', [session("roleId"), session("company_id")]);
        } else {
            $pageid = DB::select('SELECT page_id from role_settings WHERE role_id = ? ORDER BY page_id', [session("roleId")]);
        }
        // $pageid = DB::select('SELECT page_id from role_settings WHERE role_id = ? and page_id IN (SELECT page_id FROM `module_permissions_details` where company_id = ?) ORDER BY page_id;',[session("roleId"),session("company_id")]);
        $array = [];

        foreach ($pageid as $value) {
            array_push($array, $value->page_id);
        }

        $result = DB::table('pages_details')->whereIN('id', $array)->get();

        return view('components.sidebar', compact('result'));
    }
}
