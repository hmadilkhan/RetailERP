<?php
require "crud.php";
date_default_timezone_set("Asia/Karachi");
$crud = new Crud();
$productarray = array();


$ids = $_POST['inventory'];
// $ids = implode(',', $idArray);
// echo 1;
// print_r($idArray);
// exit();

$result = $GLOBALS['crud']->runQuery("SELECT id,item_code,product_name,(Select retail_price from inventory_price where product_id = inventory_general.id and status_id = 1) as price,(Select name from inventory_uom where uom_id = inventory_general.uom_id) as unit  FROM `inventory_general` where id IN (".$ids.")");

foreach($result as $value){
	$item = [
		"id" => $value["id"],
		"name" => $value["product_name"],
		"bar_code" => $value["item_code"],
		"unit" => $value["unit"],
		"price" => $value["price"],
	];
	array_push($productarray,$item);
}
$productarray = json_encode($productarray);
print_r($productarray);
exit();

?>
<script src="https://cdn.jsdelivr.net/npm/md5-js-tools@1.0.2/lib/md5.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script>
	function sendtosunmi(){
	<?php echo "<script type='text/javascript'>console.log('working')</script>";?>
	const characters ='ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

	function generateString(length) {
		let result = '';
		const charactersLength = characters.length;
		for ( let i = 0; i < length; i++ ) {
			result += characters.charAt(Math.floor(Math.random() * charactersLength));
		}

		return result;
	}
	function getCurrentUnixTimestamp() {
		return Math.floor(Date.now() / 1000);
	}
	
	let random = generateString(7);
	console.log("RANDOM:",random);

	let timestamp = getCurrentUnixTimestamp();
	console.log("TIMESTAMP:",timestamp);
	
	let productList = "<?=$productarray;?>";
	let string = 'app_id=KV1LI73MXVBAQ&product_list='+productList+'&random='+random+'&shop_id=1&timestamp='+timestamp+'&key=0XsVp45yO0vJlEbWsPPQ';

	// console.log("string",string);
	var hash = MD5.generate(string);
	let sign = hash.toUpperCase();
	// console.log(hash.toUpperCase());
	
	$.ajax({
	  url: "https://store.sunmi.com/openapi/product/update",
	  method : "POST",
	  data:{shop_id : 1,product_list:productList,app_id:'KV1LI73MXVBAQ',random:random,timestamp:timestamp,sign:sign},
	  cache: false,
	  success: function(response){
		console.log(response)
	  }
	});
	}
	
</script>
<?
?>