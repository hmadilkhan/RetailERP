<?php

namespace App\Http\Controllers;

use App\company;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class CompanyController extends Controller
{
  public function __construct()
    {
        $this->middleware('auth');
    } 

    public function show(company $company){
        $search = request('search', '');
        $company = $company->get_company_paginated($search);
        return view('v2.company.list', compact('company', 'search'));
    }

     public function index(company $company){
    	$country = $company->getcountry();
        $city = $company->getcity();
    	return view('v2.company.create', compact('country','city'));	
    }

    public function create(Request $request,company $company)
    {
        $rules = [
            'companyname' => 'required',
            'country' => 'required',
            'city' => 'required',
            'company_email' => 'required',
            'company_mobile' => 'required',
            'company_ptcl' => 'required',
            'company_address' => 'required',
        ];
         $this->validate($request, $rules);

        $items=[
            'status_id' => 1,
            'country_id' => $request->country,
            'city_id' => $request->city,
            'name' => $request->companyname,
            'address' => $request->company_address,
            'email' => $request->company_email,
            'ptcl_contact' => $request->company_ptcl,
            'mobile_contact' => $request->company_mobile,
            'latitude' => null,
            'longitude' => null,
            'logo' => $imageName,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $result = $company->insert();
    }
}
