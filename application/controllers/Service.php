<?php
defined('BASEPATH') OR exit('No direct script access allowed');
define('LINE_API',"https://notify-api.line.me/api/notify");
/********************************************************
             ______@27/08/2019______
            / / _ _   _ _     __ 
           / // __ \ / __ \ / __ \
       _ _/ // /_/ // / / // /_/ /
     /_ _ _/ \_ _ //_/ /_/ \__  /
                          _ _/ /
                         /___ /
********************************************************/
class Service extends MY_Controller {
	function __construct(){
		parent::__construct();
	}
	
	function index(){
		header('Access-Control-Allow-Origin: *'); 
		header('Content-Type: application/json');
		
		/*
		POST 	เพิ่ม
		GET 	ดึง
		PUT 	แก้ไข
		DELETE 	ลบ
		*/		
		
		$arrs = array();
		for($i=0;$i<10;$i++){
			for($j=1;$j<3;$j++){
				$arrs[$i][$j] = rand(1000,9999);
			}
		}
		
		echo json_encode($arrs);
	}
	
	function select(){
		$service = file_get_contents("http://192.168.1.30:92/YTKMini/service/");
		$obj 	 = json_decode($service,true); 
		
		echo "<style> body { margin:0px; } </style>";
		echo $obj[9][2];
	}
	
	function report_analyze_everyday(){
		$sql = "
			select LOCAT,count(*) as total from {$this->MAuth->getdb('ARANALYZE')}
			where ANSTAT in ('P','PP')
			group by LOCAT
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$lineNotify = "";
		if($query->row()){
			$lineNotify .= "รายงานประจำชั่วโมง\nมีคำร้องอนุมัติวิเคราะห์สินเชื่อ รออนุมัติดังนี้\n";
			foreach($query->result() as $row){
				$lineNotify .= "\nสาขา :: ".$row->LOCAT." (".$row->total." คำร้อง)";
			}
		}else{
			$lineNotify .= "รายงานประจำชั่วโมง\nไม่พบคำร้องรออนุมัติของใบวิเคราะห์สินเชื่อครับ";
		}
		
		#แจ้งเตือนไปกลุ่ม Line 
		$token = "vOaP9LwtP38FNLvh6VIA942P5qoBcDhTIAOpJSxDEu2";
		$line_msg = $lineNotify;
		
		//$imagePath0240 = "https://stardate.org/sites/default/files/styles/medium/public/images/gallery/cas_a.jpg?itok=_aaVQvEQ";
		//$imagePath1024 = "https://www.noao.edu/image_gallery/images/d4/androy.jpg";
		
		$data = array(
			"message" => $line_msg,
			// "imageThumbnail"=> $imagePath0240,
			// "imageFullsize"=> $imagePath1024,
			// "stickerPackageId"=>2,
			// "stickerId"=>43,
		);
		$this->MMAIN->send_notify_line($token,$data);
	}
}




















