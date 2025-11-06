<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class expense extends Model
{
   protected $fillable = [
        'branch_id', 'exp_cat_id','tax_id', 'expense_details','tax_amount','amount','net_amount','date','platform_type'
    ];
	
	public function expense_report_filter($cat,$first,$second)
    {
    	$filter = "";
    	if($cat != "")
    	{
    		if($filter == "")
    		{
    			$filter .= " and a.exp_cat_id = ".$cat;
    		}
    		else
    		{
    			$filter .= " and a.exp_cat_id = ".$cat;
    		}
    	}
    	if($first != "")
    	{
    		if($filter == "")
    		{
    			// $filter .= " and date(a.created_at) BETWEEN '".$first."' and '".$second."' ";
    			$filter .= " and date BETWEEN '".$first."' and '".$second."' ";
    		}
    		else
    		{
    			// $filter .= " and date(a.created_at) BETWEEN '".$first."' and '".$second."' ";
    			$filter .= " and date BETWEEN '".$first."' and '".$second."' ";
    		}
    	}
    	
    	$result = DB::select('SELECT a.exp_id,a.date,b.expense_category,a.expense_details,a.net_amount FROM expenses a INNER JOIN expense_categories b on b.exp_cat_id = a.exp_cat_id WHERE a.branch_id = ? '.$filter,[session('branch')]);
    	return $result;
    }

    public function expense_report($cat,$first,$second)
    {
    	$filter = "";
    	if($cat != "")
    	{
    		if($filter == "")
    		{
    			$filter .= " and a.exp_cat_id = ".$cat;
    		}
    		else
    		{
    			$filter .= " and a.exp_cat_id = ".$cat;
    		}
    		
    	}
    	if($first != "")
    	{
    		if($filter == "")
    		{
    			$filter .= " and a.date BETWEEN '".$first."' and '".$second."' ";
    		}
    		else
    		{
    			$filter .= " and a.date BETWEEN '".$first."' and '".$second."' ";
    		}
    		
    	}
    	
    	$result = DB::select('SELECT a.exp_id,a.date,b.expense_category,a.expense_details,SUM(a.net_amount) as balance,a.platform_type FROM expenses a INNER JOIN expense_categories b on b.exp_cat_id = a.exp_cat_id WHERE a.branch_id = ? '.$filter.' GROUP BY a.date,b.expense_category',[session('branch')]);
    	return $result;
    }

    public function company($id)
    {
        $result = DB::table('company')->where('company_id',$id)->get();
        return $result;
    }


    public  function expense_voucher($expid)
    {
        $result = DB::select('SELECT a.exp_id, a.amount, a.date, a.expense_details, b.expense_category, a.tax_amount, a.net_amount FROM expenses a INNER JOIN expense_categories b ON b.exp_cat_id = a.exp_cat_id WHERE a.branch_id = ? AND a.exp_id  = ?',[session('branch'),$expid]);
        return $result;
    }
}
