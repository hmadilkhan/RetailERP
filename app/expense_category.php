<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class expense_category extends Model
{
  protected $fillable = [
    'branch_id',
    'expense_category',
  ];

  // save record //
  public function insert($data)
  {

    return DB::table("expense_categories")->insert($data);
  }


  // update record //
  public function modify($data, $id)
  {

    return DB::table("expense_categories")->where('exp_cat_id', $id)->update($data);
  }

  // remove record //
  public function remove($id)
  {

    return '';
  }

  // get record //
  public static function get()
  {
    if (session('company_id') == 7) {
      return DB::table('expense_categories')->where('branch_id', session('branch'))->where("platform_type", 1)->orderBy('exp_cat_id', 'DESC')->get();
    } else {
      return DB::table('expense_categories')->where('branch_id', session('branch'))->orderBy('exp_cat_id', 'DESC')->get();
    }
  }

  public static function getAllCategories()
  {
    return DB::table('expense_categories')->where('branch_id', session('branch'))->orderBy('exp_cat_id', 'DESC')->get();
  }

  public static function get_edit($id)
  {
    return DB::table("expense_categories")->select('exp_cat_id as id', 'expense_category as category')->where(['exp_cat_id' => $id, 'branch_id' => session('branch')])->get();
  }

  // check record exists // 
  public function check($name)
  {
    return count(DB::table("expense_categories")->where(['expense_category' => $name, 'branch_id' => session('branch')])->get()) > 0 ? true : false;
  }
}
