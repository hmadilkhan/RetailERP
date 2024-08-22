<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PfFund extends Model
{
	public function getFunds($status)
	{
		return DB::select("SELECT * FROM `hr_pf_fund` where company_id = ? and status = ?",[session("company_id"),$status]);
	}
	
}