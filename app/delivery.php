<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\ServiceProviderPaymentType;

class delivery extends Model
{
    public function getbranches()
    {
        $branch = DB::table('branch')->where('company_id',session('company_id'))->get();
        return $branch;
    }

    public function insert($table,$items){

        $result = DB::table($table)->insertGetId($items);
        return $result;
    }



    public function getcharges($statusid){
        $result = DB::select('SELECT * FROM delivery_charges a INNER JOIN branch b ON b.branch_id = a.branch_id INNER JOIN accessibility_mode c on c.status_id = a.status_id WHERE a.status_id = ? AND b.branch_id = ?',[$statusid,session('branch')]);
        return $result;
    }

    public function update_qty($id, $qty){
        $result = DB::table('delivery_bottles')->where('id', $id)->update(['qty'=>$qty]);
        return $result;
    }

    public function delete_load($id)
    {
        if (DB::table('delivery_bottles')->where('id',$id)->delete()) {
            return 1;
        }
        else{
            return 0;
        }

    }

    public function exsist_chk($areaname,$branch){
        $result = DB::select('SELECT COUNT(id) AS counts FROM delivery_charges WHERE area_name = ? AND branch_id = ?',[$areaname,$branch]);
        return $result;
    }

    public function exsist_chk_category($category){
        $result = DB::select('SELECT COUNT(category_id) AS counts FROM service_provider_category WHERE category = ?',[$category]);
        return $result;
    }

    public function exsist_chk_percentage($per){
        $result = DB::select('SELECT COUNT(percentage_id) AS counts FROM service_agreement_percentages WHERE percentage = ?',[$per]);
        return $result;
    }


    public function exsist_chk_provider($provider,$branchid){
        $result = DB::select('SELECT COUNT(id) AS counts FROM service_provider_details WHERE provider_name = ? AND branch_id = ?',[$provider,$branchid]);
        return $result;
    }

    public function getcategory()
    {
        $category = DB::table('service_provider_category')->get();
        return $category;
    }

    public function getpercentages()
    {
        $per = DB::table('service_agreement_percentages')->get();
        return $per;
    }


    public function getserviceproviders($statusid){
		if(session("roleId") == 2){
			$result = DB::select('SELECT a.*, b.category, c.status_name, d.branch_name, e.type,a.payment_value FROM service_provider_details a INNER JOIN service_provider_category b ON b.category_id = a.categor_id INNER JOIN accessibility_mode c ON c.status_id = a.status_id INNER JOIN branch d ON d.branch_id = a.branch_id INNER JOIN service_provider_payment_type e ON e.id = a.payment_type_id WHERE a.status_id = ? AND a.branch_id IN (Select branch_id from branch where company_id = ?)',[$statusid,session('company_id')]);
			return $result;
		}else{
			$result = DB::select('SELECT a.*, b.category, c.status_name, d.branch_name, e.type,a.payment_value FROM service_provider_details a INNER JOIN service_provider_category b ON b.category_id = a.categor_id INNER JOIN accessibility_mode c ON c.status_id = a.status_id INNER JOIN branch d ON d.branch_id = a.branch_id INNER JOIN service_provider_payment_type e ON e.id = a.payment_type_id WHERE a.status_id = ? AND a.branch_id = ?',[$statusid,session('branch')]);
			return $result;
		}
        
    }
	
	public function getServiceProviderPaymentInfo()
	{
		return ServiceProviderPaymentType::all();
	}

    public function getledger($providerid){
        $result = DB::select('SELECT * FROM service_provider_details a LEFT JOIN service_provider_ladger b ON b.provider_id = a.id WHERE a.id = ? order by b.date DESC ' ,[$providerid]);
        return $result;
    }

    public function getpreviousbalance($providerid){
        $result = DB::select('SELECT balance FROM service_provider_ladger WHERE provider_id = ? AND ladger_id = (SELECT MAX(ladger_id) FROM service_provider_ladger)',[$providerid]);
        return $result;
    }




    public function load_details(){
        $result = DB::select('SELECT a.driver_name, b.name, a.date, (SELECT COUNT(product_id) FROM delivery_bottles  WHERE customer_id = a.customer_id AND driver_name = a.driver_name AND date = a.date) AS products,
		(SELECT SUM(qty) FROM delivery_bottles  WHERE customer_id = a.customer_id AND driver_name = a.driver_name AND date = a.date) AS load_qty FROM delivery_bottles a
		INNER JOIN customers b ON b.id = a.customer_id
		GROUP BY a.date');
        return $result;
    }

    public function delivery_details($date){
        $result = DB::select('SELECT b.name, a.driver_name, a.date, c.image, c.item_code, c.product_name, a.qty FROM delivery_bottles a
		INNER JOIN customers b ON b.id = a.customer_id
		INNER JOIN inventory_general c ON c.id = a.product_id
		WHERE a.date = ?',[$date]);
        return $result;
    }

    public function update_charges($chargesid, $items){
        $result = DB::table('delivery_charges')->where('id', $chargesid)->update($items);
        return $result;
    }

    public function update_provider($providerid, $items){
        $result = DB::table('service_provider_details')->where('id', $providerid)->update($items);
        return $result;
    }


    public function getdetails($providerid){
        $result = DB::select('SELECT a.*, b.category, c.status_name, d.branch_name, e.type,a.payment_value FROM service_provider_details a INNER JOIN service_provider_category b ON b.category_id = a.categor_id INNER JOIN accessibility_mode c ON c.status_id = a.status_id INNER JOIN branch d ON d.branch_id = a.branch_id INNER JOIN service_provider_payment_type e ON e.id = a.payment_type_id WHERE a.id = ?',[$providerid]);
        return $result;
    }

     public function getAdditionalCharges($provider_id){
        $result = DB::table('service_provide_additional_charges')->where('provider_id', $provider_id)->get();
        return $result;
    }

    public function getServiceProviderInfo($providerid,$from,$to){
        $result = DB::select('SELECT a.*, b.category, c.status_name, d.branch_name, e.percentage FROM service_provider_details a INNER JOIN service_provider_category b ON b.category_id = a.categor_id INNER JOIN accessibility_mode c ON c.status_id = a.status_id INNER JOIN branch d ON d.branch_id = a.branch_id INNER JOIN service_agreement_percentages e ON e.percentage_id = a.percentage_id WHERE a.id = ?',[$providerid]);

        $result[0]->additional_charge = DB::select('SELECT * FROM service_provide_additional_charges WHERE provider_id = '.$providerid.'  ');

        return $result;
    }

    public function closedToServiceProviderLedger($providerid,$from,$to){
          $result = DB::table('service_provider_ladger')->where('provider_id', $providerid)->where(DB::raw('DATE_FORMAT(`date`,"%Y-%m-%d")'),'>=', $from)->where(DB::raw('DATE_FORMAT(`date`,"%Y-%m-%d")'),'<=', $to)->update(array('closed'=>0));
        return $result;
    }

}