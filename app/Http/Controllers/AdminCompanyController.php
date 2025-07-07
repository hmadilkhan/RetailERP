<?php

namespace App\Http\Controllers;

use App\adminCompany;
use App\branch;
use App\Traits\ActivityLoggerTrait;
use App\Traits\MediaTrait;
use Illuminate\Http\Request;
use Image;
use Illuminate\Support\Facades\DB;
use \stdClass;
use App\Http\Requests\CompanyStoreRequest;
use App\Http\Requests\CompanyUpdateRequest;
use App\Services\CompanyService;
use App\Services\BranchService;

class AdminCompanyController extends Controller
{
    use MediaTrait, ActivityLoggerTrait;
    protected $companyService;
    protected $branchService;

    public function __construct(CompanyService $companyService, BranchService $branchService)
    {
        $this->middleware('auth');
        $this->companyService = $companyService;
        $this->branchService = $branchService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $companies = $this->companyService->getAll();
        return view('Admin.Company.list', compact('companies'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = $this->companyService->getFormData();
        return view('Admin.Company.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CompanyStoreRequest $request)
    {
        try {
            $data = $request->validated();

            // Handle file uploads
            if ($request->hasFile('vdimg')) {
                $file = $this->uploads($request->file('vdimg'), 'images/company/');
                $data['vdimg'] = $file['fileName'];
            } else {
                $data['vdimg'] = '';
            }

            if ($request->hasFile('posbgimg')) {
                $bgFile = $this->uploads($request->file('posbgimg'), 'images/pos-background/');
                $data['posbgimg'] = $bgFile['fileName'];
            } else {
                $data['posbgimg'] = '';
            }

            if ($request->hasFile('ordercallingbgimg')) {
                $orderFile = $this->uploads($request->file('ordercallingbgimg'), 'images/order-calling/');
                $data['ordercallingbgimg'] = $orderFile['fileName'];
            } else {
                $data['ordercallingbgimg'] = '';
            }

            $companyId = $this->companyService->create($data);
            $this->branchService->createHeadOffice($companyId, $data);
            return redirect()->route('company.index')->with('success', 'Company created successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\adminCompany  $adminCompany
     * @return \Illuminate\Http\Response
     */
    public function show(adminCompany $adminCompany) {}

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\adminCompany  $adminCompany
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = $this->companyService->getEditFormData($id);
        return view('Admin.Company.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\adminCompany  $adminCompany
     * @return \Illuminate\Http\Response
     */
    public function update(CompanyUpdateRequest $request, $id)
    {
        try {
            $data = $request->validated();

            // Handle file uploads
            if ($request->hasFile('vdimg')) {
                $file = $this->uploads($request->file('vdimg'), 'images/company/', $request->input('prev_logo'));
                $data['vdimg'] = $file['fileName'];
            } else {
                $data['vdimg'] = $request->input('prev_logo');
            }

            if ($request->hasFile('posbgimg')) {
                $bgFile = $this->uploads($request->file('posbgimg'), 'images/pos-background/', $request->input('pos_bg_logo'));
                $data['posbgimg'] = $bgFile['fileName'];
            } else {
                $data['posbgimg'] = $request->input('pos_bg_logo');
            }

            if ($request->hasFile('ordercallingbgimg')) {
                $orderFile = $this->uploads($request->file('ordercallingbgimg'), 'images/order-calling/', $request->input('prev_order_calling_display'));
                $data['ordercallingbgimg'] = $orderFile['fileName'];
            } else {
                $data['ordercallingbgimg'] = $request->input('prev_order_calling_display');
            }

            $this->companyService->update($id, $data);
            return redirect()->route('company.index')->with('success', 'Company updated successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\adminCompany  $adminCompany
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $this->companyService->delete($id);
            return redirect()->route('company.index')->with('success', 'Company deleted successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
