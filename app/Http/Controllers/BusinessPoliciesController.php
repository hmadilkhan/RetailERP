<?php

namespace App\Http\Controllers;

use App\businessPolicies;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class BusinessPoliciesController extends Controller
{
	  public function __construct()
    {
        $this->middleware('auth');
    } 

      public function index(businessPolicies $policy, Request $request)
    {
    	$details = $policy->get_tax_rules();
    	return view('Business_Policy.view-tax', compact('details'));	
    }

      public function tax_create()
    {
    	return view('Business_Policy.create-tax', compact(0));
    }

    public function insert_tax(businessPolicies $policy, Request $request)
    {

    	 $rules = [
                'taxname' => 'required',
                'taxpercentage' => 'required',

            ];
             $this->validate($request, $rules);
             $exsist = $policy->tax_esists($request->taxname);

             if ($exsist[0]->counter == 0) {
             
    	$items = [
    		'name' => $request->taxname,
    		'value' => $request->taxpercentage,
            'show_in_purchase' => ($request->purchase == "on" ? 1 : 0),
            'show_in_pos' => ($request->pos == "on" ? 1 : 0),
    		'company_id' => session('company_id'),
    		'status_id' => 1,
    	];
    	 $result = $policy->insert_tax($items);
    	 return redirect('/BusinessPolicy');
    	}
    	else
    	{
    	return 0;	
    	}

    }

    public function delete_tax(businessPolicies $policy, Request $request)
    {
    	$result = $policy->tax_update($request->id);
    	return 1;
    	
    }

       public function show_tax(businessPolicies $policy, Request $request)
    {
    	$details = $policy->get_tax_rules_id(Crypt::decrypt($request->id));
    	return view('Business_Policy.edit-tax', compact('details'));	
    }

    public function update_tax(businessPolicies $policy, Request $request)
    {
        $result = $policy->tax_update($request->taxid);

        $items = [
            'name' => $request->taxname,
            'value' => $request->taxpercentage,
            'show_in_purchase' => ($request->purchase == "on" ? 1 : 0),
            'show_in_pos' => ($request->pos == "on" ? 1 : 0),
            'company_id' => session('company_id'),
            'status_id' => 1,
        ];
        $result = $policy->insert_tax($items);

//    	$items = [
//    		'name' => $request->taxname,
//    		'value' => $request->taxpercentage,
//    		'company_id' => session('company_id'),
//    		'status_id' => 1,
//    	];
//    	$result = $policy->tax_edit($request->taxid, $items);
    	 return redirect('/BusinessPolicy');
    }


     public function show_taxslabs(businessPolicies $policy, Request $request)
    {
        $slabs = $policy->get_tax_slabs(1);
        return view('Business_Policy.view-tax-slabs', compact('slabs'));    
    }

     public function createtaxslabs()
    {
        return view('Business_Policy.create-tax-slabs', compact(0));  
    }

      public function store_taxslabs(businessPolicies $policy, Request $request)
    {
         $rules = [
                'slabmin' => 'required',
                'slabmax' => 'required',
                'taxpercentage' => 'required',
                'year' => 'required',
            ];
             $this->validate($request, $rules);

       $chk = $policy->slab_exsists(session('company_id'),$request->slabmin,$request->slabmax);
             if ($chk[0]->counts == 0) {
        $items = [
            'slab_min' => $request->slabmin,
            'slab_max' => $request->slabmax, 
            'percentage' => $request->taxpercentage, 
            'company_id' => session('company_id'),
            'year' => $request->year,
            'status_id' => 1,
        ];
        $result = $policy->insert('tax_slabs', $items);
         return redirect('/showtaxslabs-active');
             }
             else{
                return 0;
             }

    }

 public function show_taxslabsinactive(businessPolicies $policy, Request $request)
    {
        $slabs = $policy->get_tax_slabs(2);
        return $slabs;
    }


 public function inactivetaxslab(businessPolicies $policy, Request $request)
    {
       $items = [
            'status_id' => 2,
        ];
        $result = $policy->update_taxslabs($request->taxid, $items);
         return $result;
    }

    public function reactivetaxslab(businessPolicies $policy, Request $request)
    {
       $items = [
            'status_id' => 1,
        ];
        $result = $policy->update_taxslabs($request->taxid, $items);
         return $result;
    }


      public function update_taxslabs(businessPolicies $policy, Request $request)
    {

        $items = [
            'slab_min' => $request->slabmin,
            'slab_max' => $request->slabmax, 
            'percentage' => $request->taxpercentage, 
            'company_id' => session('company_id'),
            'year' => $request->year,
            'status_id' => 1,
        ];
        $result = $policy->update_taxslabs($request->taxid, $items);
         return 1;

    }



}
