<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/********************************************************
             _____15/08/2563______
            / / _ _   _ _     __ 
           / // __ \ / __ \ / __ \
       _ _/ // /_/ // / / // /_/ /
     /_ _ _/ \_ _ //_/ /_/ \__  /
                          _ _/ /
                         /___ /						 
********************************************************/
class DealerServices extends MY_Controller {
	private $sess = array();
	
	function __construct(){
		parent::__construct();
		/*Additional code which you want to run automatically in every function call */
		$sess = $this->session->userdata('cbjsess001');
		if(!$sess){ redirect(base_url("welcome/"),"_parent"); }else{
			foreach ($sess as $key => $value) {
                $this->sess[$key] = $value;
            }
		}
	}
	
	function index(){		
		$claim = $this->MLogin->getclaim(uri_string());
		if($claim['m_access'] != "T"){ echo "<div align='center' style='color:red;font-size:16pt;width:100%;'>ขออภัย คุณยังไม่มีสิทธิเข้าใช้งานหน้านี้ครับ</div>"; exit; }
		
		$html = "
			<div class='tab1' name='home' locat='{$this->sess['branch']}' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}'>
				<br>
				<div class='row col-sm-6 col-sm-offset-3'>
					<input type='text' id='STRNO' class='form-control input-sm' placeholder='เลขตัวถัง/เลขเครื่อง' maxlength=30>
					<button id='btnCheck' class='btn btn-warning btn-block'><span class='glyphicon glyphicon-pencil'> ตรวจสอบ</span></button>
				</div>
				<div class='row col-sm-6 col-sm-offset-3'>
					<span style='font-size:7pt;color:red;'>เฉพาะรถยี่ห้อ HONDA ที่ผลิตตั้งแต่ปี 2004-ปัจจุบัน เท่านั้น</span>
				</div>
				
				<div id='result' class='row col-sm-6 col-sm-offset-3'><div>
			</div>
		";
		
		$html.= "<script src='".base_url('public/js/SYS02/DealerServices.js')."'></script>";
		echo $html;
	}
	
	function getInfo(){
		$now = new DateTime();
		$strno = $_POST["strno"];
		
		$sql = "
			declare @strno varchar(30) = replace('".$strno."',' ','');
			select case when left(right(@strno,2),1) = 'F' and isnumeric(right(@strno,1)) = 1
				then substring(@strno,0,len(@strno)-1)
				else @strno 
				end as STRNO
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		$query = $query->row();
		$strno = $query->STRNO;
		
		$homepage = iconv('TIS-620','UTF-8',$this->getInfoSTRNO($strno));
		$ex = explode("|",$homepage);
		
		$vinno = (isset($ex[17]) ? $ex[17]:'');
		$strno = (isset($ex[2]) ? $ex[2]:'').(isset($ex[3]) ? $ex[3]:'');
		$engno = (isset($ex[0]) ? $ex[0]:'').(isset($ex[1]) ? $ex[1]:'');
		$yr    = (isset($ex[16]) ? $ex[16]:'');
		$yrb   = "";
		if($yr != ""){
			$yrex  = explode("/",$yr);
			$yrb   = $yrex[2]."/".($yrex[2]+543);
		}
		
		$model = (isset($ex[13]) ? $ex[13]:'');
		$baab  = (isset($ex[14]) ? $ex[14]:'');
		$color = (isset($ex[15]) ? $ex[15]:'');
		
		$html = "
			<table class='table table-dotted'>			
				<!-- tr><th>vin no </th><td>".$vinno."</td></tr>
				<tr><th>เลขตัวถัง </th><td>".$strno."</td></tr -->
				<tr><th>เลขตัวถัง</th><td>".$vinno."</td></tr>
				<tr><th>เลขเครื่อง </th><td>".$engno."</td></tr>
				<tr><th>รุ่น</th><td>".$model."</td></tr>
				<tr><th>แบบ</th><td>".$baab."</td></tr>
				<tr><th>สี  </th><td>".$color."</td></tr>
				<tr><th>วันที่ผลิต  </th><td>".$yr."</td></tr>
				<tr><th>ปีผลิต (ค.ศ./พ.ศ) </th><td>".$yrb."</td></tr>
			</table>
		";
		
		$then = new DateTime();
		$diff = $now->diff($then);
		$alert = "";
		if($diff->format('%h') != 0){ $alert .= $diff->format('%h')." ชั่วโมง"; }
		if($diff->format('%i') != 0){ $alert .= $diff->format('%i')." นาที"; }
		$alert .= $diff->format('%s')." วินาที";
		
		$html .= "<span style='font-size:7pt;color:red;'>ระยะเวลาค้นหา  :: ".$alert."</span>";		
		$this->response["html"] =  $homepage;
		$this->response["html"] =  $html;
		echo json_encode($this->response);
	}
}




















