<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\sideBar;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class SideBarController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(sideBar $sideBar){
        $details = $sideBar->getdetails();
        return view('SideBarPages.Pages', compact('details'));
    }

    public function store(sideBar $sideBar, Request $request){
        $iconarrow = "";
        if ($request->iconarrow == "on")
        {
            $iconarrow = 1;
        }
        else{
            $iconarrow = 0;
        }


        $chk = $sideBar->exsist_chk($request->pagename, $request->pageurl);
        if ($chk[0]->counts == 0) {

            $items = [
                'page_name' => $request->pagename,
                'page_url' => $request->pageurl,
                'navclass' => $request->navclass,
                'icofont' => $request->icofont,
                'parent_id' => $request->parent,
                'page_mode' => $request->pagemode,
                'icofont_arrow' => $iconarrow,
            ];

            $result = $sideBar->insert('pages_details',$items);
            return redirect('/pages')->with('success',"Submitted Successfully!!");
        }
        else{
            return redirect('/pages')->with('success',"Already Exsist!!");
        }
    }

    public function remove(sideBar $sideBar, Request $request){
        $result = $sideBar->delete_page($request->id);
        return $result;
    }

    public function update(sideBar $sideBar, Request $request){
        $iconarrow = "";
        if ($request->updateiconarrow == "on")
        {
            $iconarrow = 1;
        }
        else{
            $iconarrow = 0;
        }
            $items = [
                'page_name' => $request->updatepagename,
                'page_url' => $request->updatepageurl,
                'navclass' => $request->updatenavclass,
                'icofont' => $request->updateicofont,
                'parent_id' => $request->updateparent,
                'page_mode' => $request->updatepagemode,
                'icofont_arrow' => $iconarrow,
            ];

            $result = $sideBar->update_page($request->pageid,$items);
            return redirect('/pages')->with('success',"Updated Successfully!!");

    }

    public function getpages(sideBar $sideBar){
        $details = $sideBar->getdetails();

        return view('partials.side-bar-nav', compact('details'));
    }

    public function role_index(sideBar $sideBar){
        $details = $sideBar->getparents();
        $pages = $sideBar->getpages();

        $getroles = $sideBar->getroles();
        $roledetails = $sideBar->getroledetails();
        $roles = $sideBar->getroles_name();

//        foreach ($roledetails as $roles)
//        {
//            $array[] = implode(',',(array)$roles->page_name);
//            $valuearray=[
//              'rolename' =>  $roles->role,
//                'pagename' => $array
//            ];
//        }

        return view('SideBarPages.Roles', compact('details','getroles','roledetails','roles','pages'));
    }

    public function insertRole(sideBar $sideBar, Request $request){

        $array =[];

            array_push($array, $request->pages);

            $result = $sideBar->getparentid($request->pages);

            if ($result[0]->parent_id == 0)
            {
                //insert into table

            }
            else{
                array_push($array, $result[0]->parent_id);
                $child = $sideBar->getparentid($result[0]->parent_id);
                if ($child[0]->parent_id == 0)
                {
                    //insert here

                }
                else{
                    array_push($array, $child[0]->parent_id);
                    $grandchild = $sideBar->getparentid($child[0]->parent_id);
                    if ($grandchild[0]->parent_id == 0)
                    {
                        //insert into table
                    }
                    else{
                        array_push($array, $grandchild[0]->parent_id);
                        $grandgrandchild = $sideBar->getparentid($grandchild[0]->parent_id);
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


//        dd(array_reverse($array));


//        if ($request->mode == 1)
//        {
//            foreach ($request->pagename as $pages)
//            {
//                $counts = $sideBar->exsist_chk_roles($request->addmoreRoleid,$pages);
//                if ($counts[0]->counts == 0)
//                {
//                    $items = [
//                        'role_id' => $request->addmoreRoleid,
//                        'page_id' => $pages,
//                    ];
//                    $result = $sideBar->insert('role_settings',$items);
//                }
//                else{
//                    return redirect('/roles')->with('warning',"Already Exsist!!");
//                }
//
//            }
//            return redirect('/roles')->with('success',"Submitted Successfully!!");
//
//
//        }
//        else{
            foreach (array_reverse($array) as $pages)
            {
                $counts = $sideBar->exsist_chk_roles($request->role,$pages);
                if ($counts[0]->counts == 0)
                {
                    $items = [
                        'role_id' => $request->role,
                        'page_id' => $pages,
                    ];
                    $result = $sideBar->insert('role_settings',$items);
                }
//                else{
//                    return 0;
//                }
            }
            return 1;
//        }

    }

    public  function getbyroleid(sideBar $sideBar, Request $request){
        $result = $sideBar->getbyroleid($request->roleid);
        return $result;
    }
    public  function deletepagesetting(sideBar $sideBar, Request $request){
        $result = $sideBar->delete_rolepage($request->id);
        return $result;
    }

    public  function getpageschild(sideBar $sideBar, Request $request){

        $result = $sideBar->getchilds($request->parentid);
        return $result;
    }





}