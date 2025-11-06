<?php

namespace App\Http\Controllers;

use App\expense_category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExpenseCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {
        $this->middleware('auth');
    }


    public function index()
    {
        $category = expense_category::getAllCategories();
        return view('ExpenseCategory.lists', compact('category'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {}

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, expense_category $expense_category)
    {
        $data = [
            'branch_id' => session('branch'),
            'expense_category' => $request->get('category'),
            'platform_type' => $request->get('platform_type'),
        ];

        if ($expense_category->check($request->get('category'))) {
            return response()->json(array("state" => 2, "msg" => 'This category already exists.', "contrl" => 'category'));
        } else {
            if ($expense_category->insert($data)) {
                return response()->json(array("state" => 0, "msg" => '', "contrl" => ''));
            } else {
                return response()->json(array("state" => 1, "msg" => 'Not saved :(', "contrl" => ''));
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\expense_category  $expense_category
     * @return \Illuminate\Http\Response
     */
    public function show(expense_category $expense_category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\expense_category  $expense_category
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, expense_category $expense_category)
    {
        $get = $expense_category->get_edit($request->id);
        if ($get) {
            return response()->json($get);
        } else {
            return response()->json(array("state" => 0, "msg" => "Oops! Sorry Can't edit record"));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\expense_category  $expense_category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, expense_category $expense_category)
    {

        $result = $expense_category->modify(['expense_category' => $request->cat], $request->id);
        if ($result) {
            return response()->json(array("state" => 1, "msg" => "saved changes."));
        } else {
            return response()->json(array("state" => 0, "msg" => "Oops! Sorry Can't update expense category"));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\expense_category  $expense_category
     * @return \Illuminate\Http\Response
     */
    public function destroy(expense_category $expense_category)
    {
        //
    }
}
