<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class adminBranch extends Model
{
    public function get_branches($companyid){
    	$result = DB::select('SELECT a.branch_id, a.branch_name, c.country_name,  d.city_name, a.branch_mobile, a.branch_email, a.branch_address, a.branch_logo FROM branch a
        INNER JOIN company b ON b.company_id = a.company_id
        INNER JOIN country c ON c.country_id = a.country_id
        INNER JOIN city d on d.city_id = a.city_id
        WHERE a.status_id = 1 AND a.company_id = ?',[$companyid]);
		return $result;
    }
}
