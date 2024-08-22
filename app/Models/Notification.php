<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Notification extends Model
{
	public static function sendNotification($terminal,$message,$title,$date,$time)
	{
		$tokens = array();
		$body = ucwords($terminal->branch->company->name)." (".ucwords($terminal->branch->branch_name).") (".date("d/M/Y",strtotime($date))." - ".date("H:i a",strtotime($time)).")";
        $firebaseToken = DB::table("terminal_details")->where("branch_id",session("branch"))->where("terminal_id",$terminal->terminal_id)->whereNotNull("device_token")->get("device_token");
		//["cZIiT3EPTAKce8s8lPHTkZ:APA91bH0a0zModJDvMjwLmeMIqHNfyLriX1m2EWV9BI157KY6DtxsfWPDo-mYjl-Qh92dyfjU0Q0BM_HeXykZp6xy3LxoOxZmeLIxyBTimnfsCVIOuM0PBE8j53EV-_AWi6CVMOJIDMH"];//User::whereNotNull('device_token')->pluck('device_token')->all();
		
		if(count($firebaseToken) > 0){
		
			foreach($firebaseToken as $token){
				array_push($tokens,$token->device_token);
			}

			$SERVER_API_KEY = 'AAAATXdhnIk:APA91bHFZZbCubOgnG3dihDVsqFbwGGQaBpC6f7BPFMnvpntpOOY88ysAEVAT2puQvdng3Xkd8j4HNVWFp1FQ2rHEe9g3Cv6nSZ7oeMsQtSh2GrJYNIxGHeogmen7TSPqRWHJxrG4QF_';

			$server_api_key_mobile = 'AAAA2dlOr6s:APA91bHGDpYDSZWI0LotnIYZUTpOTA9lLS56jsyB-2hq6Fsq6l0OPBoMYFqePTAbteVFawWzdyZOfMowMf-j8LBL8xJefdnpb_pZRVQHzu5rXykkdLBfPJgcr8gmPhPBDlXMWJy_-uv2';   
			   
			$data = [
				"registration_ids" => $tokens,
				"notification" => [
					"title" => "Shift ".$title,
					"body" => $body,
					"icon" => "https://sabsoft.com.pk/Retail/public/assets/images/Sabify72.png",
					"content_available" => true,
					"priority" => "high",
				],
				"data" =>[
					"par1" => $message,
				],
			]; 
			
			$dataString = json_encode($data);

			$headers = [
				'Authorization: key=' . $SERVER_API_KEY,
				'Content-Type: application/json',
			];

			$ch = curl_init();

			curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);

			$headers1 = [
				'Authorization: key=' . $server_api_key_mobile,
				'Content-Type: application/json',
			];

			$chs = curl_init();

			curl_setopt($chs, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
			curl_setopt($chs, CURLOPT_POST, true);
			curl_setopt($chs, CURLOPT_HTTPHEADER, $headers1);
			curl_setopt($chs, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($chs, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($chs, CURLOPT_POSTFIELDS, $dataString);

			curl_exec($chs);
			$response = curl_exec($ch);
			return json_encode($response);
		
		}

	}
}