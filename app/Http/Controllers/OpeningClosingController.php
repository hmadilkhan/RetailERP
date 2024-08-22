<?php

namespace App\Http\Controllers;

use App\Models\SalesOpening;

class OpeningClosingController extends Controller
{
	public function index()
	{
		$list = SalesOpening::with("branch","terminal")->where("user_id",auth()->user()->branch_id)->get();
		// return $list;
		return view("openingclosing.index",compact("list"));
	}
}