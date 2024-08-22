<?php
class Crud{

     public $dbstatus;
	 public $data = array();
	 public $error;
	 public $country = array();
	 public $city = array();
	 public $state = array();
	 public $login_id;
	 public $login_name;
	 public  $login_status;

//constructor
public function __construct(){


	     try{

	         $this->dbstatus = new PDO("mysql:host=localhost;dbname=admin_xero9","admin_retail","H80Mvum67UulKJukuF4p");
             $this->dbstatus->exec("set names utf8");

		 }catch(PDOException $e){

			die("[Connection Error] : [Server is not Connected.]");

		 }




}
//Run Query
public function runQuery($query){
	try{

	      if(isset($query)){

		        $stmt = $this->dbstatus->prepare($query);
                $stmt->execute();

			   if($stmt->rowCount() > 0){

				  return $stmt->fetchAll();

			    }else {

				  return 0;


				}

            }


		 }catch(PDOException $e){

			 $this->error = "[Error] : [".$e->getMessage()."]";



		 }


}




//row column count method
public function count_Row($columnCount,$condition,$table){

              if($condition != ""){

				  $stmt = $this->dbstatus->prepare("Select ".$columnCount." From ".$table." WHERE ".$condition."");

			 $stmt->execute();

			 if($stmt->rowCount() > 0){

			              $get = $stmt->fetchObject();

				 return $get->$columnCount;

			 }else{

				 return 0;

			 }


		}else{

			 $stmt = $this->dbstatus->prepare("Select ".$columnCount." From ".$table."");

			 $stmt->execute();

			 if($stmt->rowCount() > 0){

				     $get = $stmt->fetchObject();

				 return $get->$columnCount;

			 }else{

				 return 0;

			 }

		}

}

//Row Count Method
public function Row_Action($columnCount,$condition,$table){

			 if($condition != ""){

				  $stmt = $this->dbstatus->prepare("Select ".$columnCount." From ".$table." WHERE ".$condition."");

			 $stmt->execute();

			 if($stmt->rowCount() > 0){

				 return $stmt->rowCount();

			 }else{

				 return 0;

			 }


		}else{

			 $stmt = $this->dbstatus->prepare("Select ".$columnCount." From ".$table."");

			 $stmt->execute();

			 if($stmt->rowCount() > 0){

				 return $stmt->rowCount();

			 }else{

				 return 0;

			 }

		}


}



//Select Method
public function select_mode($column,$table,$condition){

   try{



	       if($condition != ""){



		    $select_query = "SELECT ".$column." FROM ".$table." WHERE ".$condition."";

	    $stmt = $this->dbstatus->prepare($select_query);

		$stmt->execute();

		  if($stmt->rowCount()){

			   return $stmt->fetchAll();

		  }else {

			   return 0;

			  }

		   }else {



						    $select_query = "SELECT ".$column." FROM ".$table."";

				    $stmt = $this->dbstatus->prepare($select_query);

					$stmt->execute();

					  if($stmt->rowCount() > 0){
			              return $stmt->fetchAll();

					  }else {

						 return 0;

						  }


	    }



     }catch(Throwable $exception){

		 return 'Error Message : '.$exception->getMessage();

	 }

}

//Insert Method
public function insert_mode($fixcolum,$column,$table,$state){

   try{


	  $insert_query = "INSERT INTO ".$table." ".$fixcolum." VALUES ".$column."";

	   $stmt = $this->dbstatus->prepare($insert_query);

	   $stmt->execute();

	        if($stmt->rowCount() > 0){

			    return ($state) ?  $this->dbstatus->lastInsertId() : 1;

			}else{

				return 0;

			}


   }catch(Throwable $exception){

	   return 'Error Message : '.$exception->getMessage();
   }

}




//Modify Method
public function modify_mode($column,$table,$condition){

   try{

	$modify_query = "UPDATE ".$table." SET ".$column." WHERE ".$condition;

	   $stmt = $this->dbstatus->prepare($modify_query);

	    $stmt->execute();

	        if($stmt->rowCount() > 0){

			 return 1;

			}else{

				return 0;

				}


   }catch(Throwable $exception){

	   return 'Error Message : '.$exception->getMessage();
   }




}

//Remove or Delete Method
public function remove_mode($table,$condition){


 try{

	$remove_query = "Delete From ".$table." WHERE ".$condition;

	   $stmt = $this->dbstatus->prepare($remove_query);

	   $stmt->execute();




	        if($stmt->rowCount() > 0){

			 return 1;

			}else{

				return 0;

				}


   }catch(Throwable $exception){

	   return 'Error Message : '.$exception->getMessage();
   }

}



	//country method
	public function country(){


	        try{

				$query = "select * from countries";

				  $stmt = $this->dbstatus->prepare($query);

		$stmt->execute();

		      if($stmt->rowCount() > 0){



			      while($row = $stmt->fetchObject()){

					      $this->country[] = $row;

			           }
			  }else {

				  $this->country[] = "";

			  }

				}catch(Throwable $exception){

	             return 'Error Message : '.$exception->getMessage();

                 }
	}


	public function city($select){


	        try{


				$get_id = $this->runQuery("select * from countries where name = '$select'");


				if($get_id != "0"){

				        $query = "select * from cities where state_id = $get_id->id";
				        $stmt = $this->dbstatus->prepare($query);
		                $stmt->execute();

		                    if($stmt->rowCount() > 0){

			                     while($row = $stmt->fetchObject()){

					                      $this->city[] = $row;

			                       }

			                }else {

				                 $this->country[] = "";

			                }
				}else {

					$this->country[] = "none";
				}


		    }catch(Throwable $exception){

	            return 'Error Message : '.$exception->getMessage();

            }
	}


		public function state($select){


	        try{

			   if($select != "all"){
				$get_id = $this->runQuery("select id from cities where name = '$select' and state_id = 166");


				   if($get_id != "0"){

				         $getstate = $this->select_json_mode("state_name","state","city_id = $get_id->id");
				         return $getstate;
				     }else{

				         return "";
				     }



			  }else {

			            $getstate = $this->select_json_mode("a.state_name","state a Inner join cities b on b.id = a.city_id INNER Join countries c on c.id = b.state_id","c.id = 166");

			            return $getstate;

	           }


		    }catch(Throwable $exception){

	            return 'Error Message : '.$exception->getMessage();

            }
	}





}


