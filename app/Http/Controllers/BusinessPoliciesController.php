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
    	return view('v2.business-policy.view-tax', compact('details'));	
    }

      public function tax_create()
    {
    	return view('v2.business-policy.create-tax');
    }

    public function insert_tax(businessPolicies $policy, Request $request)
    {
        $request->validate([
            'taxname' => 'required|string|max:100',
            'taxpercentage' => 'required|numeric|min:0|max:100',
        ], [
            'taxname.required' => 'Tax head name is required.',
            'taxpercentage.required' => 'Tax percentage is required.',
            'taxpercentage.numeric' => 'Tax percentage must be a number.',
            'taxpercentage.min' => 'Tax percentage must be between 0 and 100.',
            'taxpercentage.max' => 'Tax percentage must be between 0 and 100.',
        ]);

        $taxName = trim($request->taxname);
        $exsist = $policy->tax_esists($taxName, session('company_id'));

        if ($exsist[0]->counter > 0) {
            return back()->withInput()->withErrors(['taxname' => 'A tax with this name already exists.']);
        }

        $items = [
            'name' => $taxName,
            'value' => $request->taxpercentage,
            'show_in_purchase' => ($request->purchase == "on" ? 1 : 0),
            'show_in_pos' => ($request->pos == "on" ? 1 : 0),
            'company_id' => session('company_id'),
            'status_id' => 1,
        ];
        $policy->insert_tax($items);

        return redirect('/BusinessPolicy')->with('success', 'Tax rule created successfully.');
    }

    public function delete_tax(businessPolicies $policy, Request $request)
    {
        // Only the logged-in company's own tax can be made inactive.
        $result = $policy->tax_update($request->id, session('company_id'));
        return $result ? 1 : 0;
    }

    public function show_tax(businessPolicies $policy, Request $request)
    {
        $details = $policy->get_tax_rules_id(Crypt::decrypt($request->id), session('company_id'));
        abort_if(count($details) === 0, 404);
        return view('v2.business-policy.edit-tax', compact('details'));
    }

    public function update_tax(businessPolicies $policy, Request $request)
    {
        $request->validate([
            'taxid' => 'required|integer',
            'taxname' => 'required|string|max:100',
            'taxpercentage' => 'required|numeric|min:0|max:100',
        ], [
            'taxname.required' => 'Tax head name is required.',
            'taxpercentage.required' => 'Tax percentage is required.',
            'taxpercentage.numeric' => 'Tax percentage must be a number.',
            'taxpercentage.min' => 'Tax percentage must be between 0 and 100.',
            'taxpercentage.max' => 'Tax percentage must be between 0 and 100.',
        ]);

        // Only the company's own active tax can be edited.
        abort_if(count($policy->get_tax_rules_id($request->taxid, session('company_id'))) === 0, 404);

        $taxName = trim($request->taxname);
        $exsist = $policy->tax_esists($taxName, session('company_id'), $request->taxid);

        if ($exsist[0]->counter > 0) {
            return back()->withInput()->withErrors(['taxname' => 'A tax with this name already exists.']);
        }

        // Existing behaviour: the old rule is made inactive and a new rule is created.
        $policy->tax_update($request->taxid);

        $policy->insert_tax([
            'name' => $taxName,
            'value' => $request->taxpercentage,
            'show_in_purchase' => ($request->purchase == "on" ? 1 : 0),
            'show_in_pos' => ($request->pos == "on" ? 1 : 0),
            'company_id' => session('company_id'),
            'status_id' => 1,
        ]);

        return redirect('/BusinessPolicy')->with('success', 'Tax rule updated successfully.');
    }


     public function show_taxslabs(businessPolicies $policy, Request $request)
    {
        $slabs = $policy->get_tax_slabs(1);
        return view('Business_Policy.view-tax-slabs', compact('slabs'));    
    }

     public function createtaxslabs()
    {
        return view('Business_Policy.create-tax-slabs');  
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
