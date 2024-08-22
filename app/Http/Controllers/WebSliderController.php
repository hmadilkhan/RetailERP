<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\WebsiteDetail;
use App\inventory_department;
use Session,Image;

class WebSliderController extends Controller
{
    public function index(Request $request)
	{

          return view("websites.sliders.index");
	}  


   public function create(){

		return view("websites.sliders.create",[
			"websites"   => WebsiteDetail::all(),
			"departments" => inventory_department::getdepartment('')
		]);

   }


   public function store(Request $request){
       
       $rules = [
                  'website'    => 'requried',
                  'department' => 'required',
                  'slide'      => 'required|mimes:jpg,jpeg,png'
                ];

        $this->validate($request,$rules);   
   }

   public function show(Request $request){


   }


}