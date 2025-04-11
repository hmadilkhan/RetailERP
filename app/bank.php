<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class bank extends Model
{
	public function exsists_bank($bankname){
		$result = DB::select('SELECT COUNT(bank_id) AS counter FROM banks WHERE bank_name = ?',
			[$bankname]);
		return $result;
	}
	public function exsists_branch($branchname){
		$result = DB::select('SELECT COUNT(branch_id) AS counter FROM bank_branches WHERE branch_name = ?',
			[$branchname]);
		return $result;
	}
	public function exsists_account($accountnumber){
		$result = DB::select('SELECT COUNT(bank_account_id) AS counter FROM bank_account_generaldetails
		WHERE account_no = ?',[$accountnumber]);
		return $result;
	}

	public function getOneBank($id)
    {
//        $result = DB::table("banks")->where("bank_id",$id)->get();
        $result = DB::select("Select * from banks where bank_id = ?",[$id]);
        return $result;
    }

    public function update_bank($id,$items){
        $result = DB::table('banks')->where('bank_id', $id)->update($items);
        return $result;

    }



	public function insert_bankdetails($table,$items){
		$result = DB::table($table)->insert($items);
       return $result;
	}
	public function get_banks(){
		$result = DB::table('banks')->get();
		return $result;
	}
	public function get_branches(){
		$result = DB::table('bank_branches')->get();
		return $result;
	}

	public function get_accounts(){
		$result = DB::table('bank_account_generaldetails as a')
                        ->select(
                            'a.*',
                            'b.bank_name',
                            'c.branch_name',
                            'e.id as website_id',
                            'e.name as website_name'
                        )
                        ->join('banks as b', 'b.bank_id', '=', 'a.bank_id')
                        ->join('bank_branches as c', 'c.branch_id', '=', 'a.branch_id')
                        ->leftJoin('website_banks as d', 'd.bank_id', '=', 'a.bank_account_id')
                        ->leftJoin('website_details as e', 'e.id', '=', 'd.website_id')
                        ->where('a.branch_id_company', 17)
                        ->where('d.status', 1)
                        ->get();
        //$result = DB::select('SELECT a.*, b.bank_name, c.branch_name FROM bank_account_generaldetails a INNER JOIN banks b ON b.bank_id = a.bank_id INNER JOIN bank_branches c ON c.branch_id = a.branch_id WHERE a.branch_id_company = ?',[session('branch')]);
		return $result;

	}

	public function get_details($accountid){
		$result = DB::select('SELECT a.bank_account_id, a.account_title, a.account_type, a.account_no, b.bank_name, c.branch_name,a.image  FROM bank_account_generaldetails a INNER JOIN banks b ON b.bank_id = a.bank_id INNER JOIN bank_branches c ON c.branch_id = a.branch_id
			WHERE a.bank_account_id = ?',[$accountid]);
		return $result;
	}

	public function update_accounts($id,$items){
		 $result = DB::table('bank_account_generaldetails')->where('bank_account_id', $id)->update($items);
    	return $result;

	}

	public function getLedger($id)
	{
		$result = DB::table('bank_deposit_details')->where('bank_account_id',$id) ->orderBy('bank_account_id', 'desc')->get();
		return $result;
	}

	public function getLastBalance($id)
	{
		$result = DB::select('SELECT balance from bank_deposit_details where bank_deposit_id = (SELECT MAX(bank_deposit_id) from bank_deposit_details where bank_account_id = ?)',[$id]);
		return $result;
	}

    public function getchequedetails(){
        $result = DB::select('SELECT * FROM bank_cheque_general a INNER JOIN bank_cheque_details b ON b.cheque_id = a.cheque_id INNER JOIN cheque_status c on c.id = b.cheque_status_id INNER JOIN bank_cheque_customer d ON d.cheque_id = a.cheque_id INNER JOIN customers e ON e.id = d.customer_id WHERE b.status_id = 1');
        return $result;
    }

    public function insert($table,$items){
        $result = DB::table($table)->insertGetId($items);
        return $result;
    }

    public function exsit_cheque($chequenumber)
    {
        $result = DB::select('SELECT COUNT(cheque_id) AS counts FROM bank_cheque_general WHERE cheque_number = ?',[$chequenumber]);
        return $result;
    }

    public function exist_status($statusname)
    {
        $result = DB::select('SELECT COUNT(id) AS counts FROM cheque_status WHERE status = ?',[$statusname]);
        return $result;
    }

    public function cheque_status()
    {
        $status = DB::table('cheque_status')->get();
        return $status;
    }

    public function getid($chequeid)
    {
        $result = DB::select('SELECT * FROM bank_cheque_details WHERE cheque_id = ? AND status_id = 1',[$chequeid]);
        return $result;
    }

    public function update_cheque_details($id,$items){
        $result = DB::table('bank_cheque_details')->where('id', $id)->update($items);
        return $result;

    }

    public function getdetailsbychequeid($chequeid)
    {
        $result = DB::select('SELECT * FROM bank_cheque_general a INNER JOIN bank_cheque_details b ON b.cheque_id = a.cheque_id INNER JOIN cheque_status c on c.id = b.cheque_status_id WHERE a.cheque_id = ?',[$chequeid]);

        return $result;
    }

    public function getcustomers(){
	    $result = DB::select('SELECT * FROM customers WHERE user_id IN (SELECT user_id FROM user_authorization WHERE company_id = ?)',[session('company_id')]);
	    return $result;
    }

    public function getcheques_bydate($date){
	    $result = DB::select('SELECT * FROM bank_cheque_general a INNER JOIN bank_cheque_details b ON b.cheque_id = a.cheque_id INNER JOIN cheque_status c on c.id = b.cheque_status_id INNER JOIN bank_cheque_customer d ON d.cheque_id = a.cheque_id INNER JOIN customers e ON e.id = d.customer_id WHERE a.cheque_date = ? AND b.status_id = 1 AND b.cheque_status_id = 1 GROUP BY a.cheque_id',[$date]);
	    return $result;
    }

    public function getcheques_filter($fromdate,$todate,$chequeStatus,$chequeType,$customer){
        $clause = "";

        if ($fromdate != ""){
//            if ($clause == ""){
//                $clause .= "WHERE a.cheque_date BETWEEN '".$fromdate."' AND '".$todate."'";
//            }else{
//                $clause .= " AND a.cheque_date BETWEEN '".$fromdate."' AND '".$todate."'";
//            }
            $clause .= " AND a.cheque_date BETWEEN '".$fromdate."' AND '".$todate."'";

        }
        //cheque status
        if ($chequeStatus != ""){
//            if ($clause == ""){
//                $clause .= "WHERE b.cheque_status_id = ".$chequeStatus;
//            }else{
//                $clause .= " AND b.cheque_status_id = ".$chequeStatus;
//            }
            $clause .= " AND b.cheque_status_id = ".$chequeStatus;
        }
        // cheque type
        if ($chequeType != "")
        {
//            if ($clause == ""){
//                $clause .= "WHERE a.payment_mode = '".$chequeType."'";
//            }else{
//                $clause .= " AND a.payment_mode = '".$chequeType."'";
//            }
            $clause .= " AND a.payment_mode = '".$chequeType."'";
        }
        //customer
        if ($customer != ""){
//            if ($clause == ""){
//                $clause .= "WHERE d.customer_id = ".$customer;
//            }else{
//                $clause .= " AND d.customer_id = ".$customer;
//            }
            $clause .= " AND d.customer_id = ".$customer;
        }
        $result = DB::select('SELECT * FROM bank_cheque_general a INNER JOIN bank_cheque_details b ON b.cheque_id = a.cheque_id INNER JOIN cheque_status c on c.id = b.cheque_status_id INNER JOIN bank_cheque_customer d ON d.cheque_id = a.cheque_id INNER JOIN customers e ON e.id = d.customer_id WHERE b.status_id = 1 '.$clause.' GROUP BY a.cheque_id');
        return $result;
    }

    public function getbankAccounts(){
	    $result = DB::select('SELECT * FROM bank_account_generaldetails a INNER JOIN banks b ON b.bank_id = a.bank_id INNER JOIN bank_branches c ON c.branch_id = a.branch_id WHERE a.branch_id_company = ?',[session('branch')]);
	    return $result;
    }

    public  function cheque_exsits($chequenumber){
	    $result = DB::select('SELECT COUNT(bank_deposit_id) AS counts FROM bank_deposit_details WHERE cheque_number = ?',[$chequenumber]);
	    return $result;
    }

    public function getCashDetails()
    {
        $result = DB::table("cash_ledger")->where('branch_id',session('branch'))->orderByDesc('id')->get();
        return $result;
    }

    public function getLastCashBalance()
    {
        $result = DB::select('SELECT balance FROM `cash_ledger` where id = (Select MAX(id) from cash_ledger where branch_id = ?) ',[session('branch')]);
        return $result;
    }

    public function update_ledger_narration($id,$items){
        $result = DB::table('cash_ledger')->where('id', $id)->update($items);
        return $result;

    }
    public function update_bank_narration($id,$items){
        $result = DB::table('bank_deposit_details')->where('bank_deposit_id', $id)->update($items);
        return $result;

    }

    public function getcashledgerbalance()
    {
        $result = DB::select('SELECT balance FROM cash_ledger WHERE id = (SELECT MAX(id) FROM cash_ledger WHERE branch_id IN(SELECT branch_id FROM branch WHERE company_id = ?))',[session('company_id')]);
        return $result;
    }

    public function getaccountdetails_byid($id){
	    $result = DB::table('bank_account_generaldetails')->where('bank_account_id',$id)->get();
	    return $result;
    }

    public function getbankledgerbalance($id)
    {
        $result = DB::select('SELECT balance FROM bank_deposit_details WHERE bank_deposit_id = (SELECT MAX(bank_deposit_id) FROM bank_deposit_details WHERE bank_account_id = ?)',[$id]);
        return $result;
    }





}
