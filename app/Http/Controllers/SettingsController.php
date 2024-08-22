<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\settings;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(settings $settings)
    {
        $pages = $settings->getpages();
        $company = $settings->getCompany();
//        $details = $settings->getdetails();
        $companies = $settings->getcompany_name();
        $modulesdetails = $settings->getmodulesdetails();

        return view('settings.modules-permissions', compact('pages','company','companies','modulesdetails'));
    }

    public function store(settings $settings, Request $request){
            $array =[];
            array_push($array, $request->pages);
            $result = $settings->getparentid($request->pages);
            if ($result[0]->parent_id == 0)
            {
                //insert into table

            }
            else{
                array_push($array, $result[0]->parent_id);
                $child = $settings->getparentid($result[0]->parent_id);
                if ($child[0]->parent_id == 0)
                {
                    //insert here

                }
                else{
                    array_push($array, $child[0]->parent_id);
                    $grandchild = $settings->getparentid($child[0]->parent_id);
                    if ($grandchild[0]->parent_id == 0)
                    {
                        //insert into table
                    }
                    else{
                        array_push($array, $grandchild[0]->parent_id);
                        $grandgrandchild = $settings->getparentid($grandchild[0]->parent_id);
                        if ($grandgrandchild[0]->parent_id == 0)
                        {
                            //insert into table
                        }
                        else{
                            //nothing
                        }
                    }
                }

            }

            foreach (array_reverse($array) as $pages)
            {
                $counts = $settings->exsist_chk_modules($request->company,$pages);
                if ($counts[0]->counts == 0)
                {
                    $items = [
                        'company_id' => $request->company,
                        'page_id' => $pages,
                        'status_id' => 1,
                    ];
                    $result = $settings->insert('module_permissions_details',$items);
                }

            }
            return 1;

    }

    public  function getbycompanyid(settings $settings, Request $request){
        $result = $settings->getbycompanyid($request->companyid);
        return $result;
    }

    public  function deletemodules(settings $settings, Request $request){
        $result = $settings->delete_module($request->id);
        return $result;
    }
}