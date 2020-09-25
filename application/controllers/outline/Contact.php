<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/********************************************************
             _______________________
            / / _ _   _ _     __ 
           / // __ \ / __ \ / __ \
       _ _/ // /_/ // / / // /_/ /
     /_ _ _/ \_ _ //_/ /_/ \__  /
                          _ _/ /
                         /___ /
********************************************************/
class Contact extends MY_Controller {
	private $sess = array();
	
	function __construct(){
		parent::__construct();
	}
	
	/*@ ให้โปรแกรมงานฝากส่งข้อมูลรูปภาพมาอัพขึ้นระบบ เนื่องจากโปรแกรมเงินฝากตอนแนบรูปจะไม่สมบูรณ์ น่าจะเป็นเพราะ PHP5.3 */
	function index(){
		//print_r(get_headers(base_url())); exit;
		//header('Access-Control-Allow-Origin: http://localhost:98');		
		header('Access-Control-Allow-Origin: http://192.168.0.2:89'); // อนุญาติให้ server .2 เข้าถึงเมนู
		header('Access-Control-Allow-Methods: GET');
		header("Access-Control-Allow-Headers: X-Requested-With");
		header('Access-Control-Max-Age: 1000');
		header('Content-Type: application/json');
	
		$this->uploadRevenueSlip();
	}
	
	function uploadRevenueSlip(){
		$sql = "
			select ftpserver, ftpuser, ftppass, ftpfolder, filePath from YTKManagement.dbo.config_fileupload
			where refno='serviceweb' and ftpstatus='Y' and ftpfolder='Finance/slipDepositBranch'
		";
		$query = $this->db->query($sql);
		
		$seq = @$_POST["seq"];
		$ftp_server 	= "";
		$ftp_user_name 	= "";
		$ftp_user_pass 	= "";
		$ex = explode(".",@$_POST["imageName"]);
		$picture_name   = md5($seq).".".$ex[sizeof($ex)-1];
		
		$arrsResult =  array();
		if($query->row()){
			foreach($query->result() as $row){
				foreach($row as $key => $val){
					$arrsResult[$key] = $val;
				}
			}
		}
		
		$ftp_server 	= $arrsResult['ftpserver'];
		$ftp_user_name 	= $arrsResult['ftpuser'];
		$ftp_user_pass 	= $arrsResult['ftppass'];
		
		$conn_id 		= ftp_connect($ftp_server);		
		$login_result 	= ftp_login($conn_id, $ftp_user_name, $ftp_user_pass); 
		ftp_chdir($conn_id,$arrsResult['ftpfolder']);
		
		if ((!$conn_id) || (!$login_result)) {
			$response["error"] = true;
			$response["msg"][] = "FTP connection has failed!<br/>Attempted to connect to server for user {$ftp_user_name}";
			echo json_encode($response); exit;
		}
		
		$img  = @$_POST["imageSlip"];
		$img  = str_replace('data:image/tmp;base64,', '', $img);
		$img  = str_replace(' ', '+', $img);
		$data = base64_decode($img);			
		//echo $img; exit;
		if (strpos(@$_POST["imageSlip"], "http://".$ftp_server."/".$arrsResult['ftpfolder']."/") !== false) {
			$sql = "
				update serviceweb.dbo.fn_depositLogs
				set Filename='".@$_POST["imageName"]."'
				where seq='{$seq}'
			";
			$this->db->query($sql);
			$error = false;
		}else{
			// Initializing new session 
			$ch = curl_init("http://".$ftp_server.'/'.$arrsResult['ftpfolder'].'/'.$picture_name); 
			// Request method is set 
			curl_setopt($ch, CURLOPT_NOBODY, true); 
			// Executing cURL session 
			curl_exec($ch); 
			// Getting information about HTTP Code 
			$retcode = curl_getinfo($ch, CURLINFO_HTTP_CODE); 				  
			// Testing for 200
			if($retcode == 200) {
				ftp_delete($conn_id, '/'.$arrsResult['ftpfolder'].'/'.$picture_name);
			}
			
			$error = true;
			if(file_put_contents('ftp://'.$ftp_user_name.':'.$ftp_user_pass.'@'.$ftp_server.'/'.$arrsResult['ftpfolder'].'/'.$picture_name, $data)){
				$sql = "
					update serviceweb.dbo.fn_depositLogs
					set Filename='{$picture_name}'
					where seq='{$seq}'
				";
				$this->db->query($sql);
				$error = false;
			}
			
			ftp_close($conn_id);
		}
		
		
		$response = array("error"=>$error);
		echo json_encode($response);
	}
}




















