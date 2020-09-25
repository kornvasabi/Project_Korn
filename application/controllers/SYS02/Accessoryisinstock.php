<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/********************************************************
             ______@22/07/2020______
			 Pasakorn Boonded

********************************************************/
class Accessoryisinstock extends MY_Controller {
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
			<div class='k_tab1' name='home' locat='{$this->sess['branch']}' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}' today='".$this->today('today')."' style='height:calc(100vh - 132px);overflow:auto;background-color:white;'>
				<div class='col-sm-12'>
					<div class='col-sm-3'>	
						<div class='form-group'>
							สาขา
							<select id='LOCAT' class='form-control input-sm'></select>
						</div>
					</div>
					<div class='col-sm-3'>	
						<div class='form-group'>
							รหัสอุปกรณ์
							<select id='F_OTPCODE' class='form-control input-sm'></select>
						</div>
					</div>
					<div class='col-sm-3'>	
						<div class='form-group'>
							ถึง
							<select id='T_OTPCODE' class='form-control input-sm'></select>
						</div>
					</div>
					<div class=' col-sm-3'>	
						<div class='form-group'>
							รวม
							<div class='input-group'>
								<input id='C_ASC' class='form-control input-sm'>
								<span class='input-group-addon'>รายการ</span>
							</div>
						</div>
					</div>
					<div class=' col-sm-12'>	
						<div class='form-group'>
							<br>
							<button id='btnsearch' class='btn btn-cyan btn-block'><span class='glyphicon glyphicon-search'> ค้นหา</span></button>
						</div>
					</div>
				</div>
				<div class='col-sm-12'>
					<div class='col-sm-12'>
						<div id='result'></div>
					</div>
				</div>
			</div>
		";
		$html .="<script src='".base_url('public/js/SYS02/Accessoryisinstock.js')."'></script>";
		echo $html;
	}
	function Search(){
		$LOCAT     = $_REQUEST['LOCAT'];
		$F_OTPCODE = $_REQUEST['F_OTPCODE'];
		$T_OTPCODE = $_REQUEST['T_OTPCODE'];
		$html = ""; $cond = "";
		
		if($LOCAT !== ""){
			$cond .=" and LOCAT like '".$LOCAT."%'";
		}
		if($F_OTPCODE !== "" and $T_OTPCODE !== ""){
			$cond .=" and (OPTCODE between '".$F_OTPCODE."' and '".$T_OTPCODE."')";
		}else if($F_OTPCODE == "" and $T_OTPCODE !== ""){
			$cond .=" and (OPTCODE like '".$T_OTPCODE."%')";
		}else if($F_OTPCODE !== "" and $T_OTPCODE == ""){
			$cond .=" and (OPTCODE like '".$F_OTPCODE."%')";
		}
		
		$sql = "
			select LOCAT,OPTCODE,OPTNAME,ONHAND,UNITCST,UNITPRC,ONHAND * UNITPRC as TOT 
			from {$this->MAuth->getdb('OPTMAST')} 
			where ONHAND > 0 ".$cond."
			order by LOCAT,OPTCODE 
		";
		//echo $sql; exit;
		$i = 0;
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){$i++;
				$html .="
					<tr>
						<td style='vertical-align:middle;'>".$row->LOCAT."</td>
						<td style='vertical-align:middle;'>".$row->OPTCODE."</td>
						<td style='vertical-align:middle;'>".$row->OPTNAME."</td>
						<td style='vertical-align:middle;'>".number_format($row->ONHAND,2)."</td>
						<td style='vertical-align:middle;'>".number_format($row->UNITCST,2)."</td>
						<td style='vertical-align:middle;'>".number_format($row->UNITPRC,2)."</td>
						<td style='vertical-align:middle;'>".number_format($row->TOT,2)."</td>
					</tr>
				";
			}
		}
		$html = "
			<table id='table-accessorystock' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='99.99%' border=1 style='font-size:8pt;'>
				<thead style='background: rgba(0, 0, 0, 0) url(&#39;../public/lobiadmin-master/version/1.0/ajax/img/bg/bg6.png&#39;) repeat scroll 0% 0%;'>
					<tr style='line-height:20px;'>
						<td style='vertical-align:middle;text-align:center;font-size:8pt;' colspan='20'>
							เงื่อนไข :: 
						</td>
					</tr>
					<tr>
						<th style='vertical-align:middle;'>สาขา</th>
						<th style='vertical-align:middle;'>รหัสอุปกรณ์</th>
						<th style='vertical-align:middle;'>ชื่ออุปกรณ์</th>
						<th style='vertical-align:middle;'>ยอดคงเหลือ</th>
						<th style='vertical-align:middle;'>ราคาทุน</th>
						<th style='vertical-align:middle;'>ราคาขาย</th>
						<th style='vertical-align:middle;'>ยอดรวม</th>
					</tr>
				</thead>	
				<tbody>						
					".$html."
				</tbody>
			</table>
		";
		
		$response = array("html"=>$html, "status"=>true);
		$response['C_ASC'] = $i;
		echo json_encode($response);
	}
}