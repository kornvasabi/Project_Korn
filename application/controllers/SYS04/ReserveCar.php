<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/********************************************************
             ______@08/03/2019______
            / / _ _   _ _     __ 
           / // __ \ / __ \ / __ \
       _ _/ // /_/ // / / // /_/ /
     /_ _ _/ \_ _ //_/ /_/ \__  /
                          _ _/ /
                         /___ /
********************************************************/
class ReserveCar extends MY_Controller {
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
			<div class='tab1' name='home' locat='{$this->sess['branch']}' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}' style='height:calc(100vh - 132px);overflow:auto;background-color:white;'>
				<div class='col-sm-12' style='overflow:auto;'>					
					<div class='row'>
						<div class='col-xs-2 col-sm-2'>	
							<div class='form-group'>
								เลขที่ใบจอง
								<input type='text' id='RESVNO' class='form-control input-sm' placeholder='เลขที่สัญญา' >
							</div>
						</div>
						<div class='col-xs-2 col-sm-2'>	
							<div class='form-group'>
								วันที่จอง
								<input type='text' id='SRESVDT' class='form-control input-sm' placeholder='จาก' data-provide='datepicker' data-date-language='th-th' value='".$this->today('startofmonth')."'>
							</div>
						</div>	
						<div class='col-xs-2 col-sm-2'>	
							<div class='form-group'>
								ถึง
								<input type='text' id='ERESVDT' class='form-control input-sm' placeholder='ถึง' data-provide='datepicker' data-date-language='th-th' value='".$this->today('endofmonth')."'>
							</div>
						</div>	
						<div class='col-xs-2 col-sm-3'>	
							<div class='form-group'>
								เลขตัวถัง
								<input type='text' id='STRNO' class='form-control input-sm' placeholder='เลขตัวถัง' >
							</div>
						</div>
						<div class='col-xs-2 col-sm-3'>	
							<div class='form-group'>
								ชื่อ-สกุล ผู้จอง
								<select id='CUSCOD' class='form-control input-sm chosen-select' data-placeholder='ชื่อ-สกุล ผู้จอง'></select>
							</div>
						</div>
					</div>
					<div class='row'>
						<div class='col-xs-2 col-sm-6'>	
							<div class='form-group'>
								<input type='button' id='btnt1reserve' class='btn btn-cyan btn-sm' value='จอง' style='width:100%'>
							</div>
						</div>
						<div class='col-xs-2 col-sm-6'>	
							<div class='form-group'>
								<input type='button' id='btnt1search' class='btn btn-primary btn-sm' value='แสดง' style='width:100%'>
							</div>
						</div>
					</div>		
				</div>
			</div>
		";
		
		$html.= "<script src='".base_url('public/js/SYS04/ReserveCar.js')."'></script>";
		echo $html;
	}
	
	function search(){
		$arrs = array();
		$arrs['RESVNO']	= $_REQUEST['RESVNO'];
		$arrs['SRESVDT'] = $this->Convertdate(1,$_REQUEST['SRESVDT']);
		$arrs['ERESVDT'] = $this->Convertdate(1,$_REQUEST['ERESVDT']);
		$arrs['STRNO'] 	= $_REQUEST['STRNO'];
		$arrs['CUSCOD'] = $_REQUEST['CUSCOD'];
		
		$cond = "";
		if($arrs['RESVNO'] != ""){
			$cond .= " and A.RESVNO like '".$arrs['RESVNO']."%'";
		}
		
		if($arrs['SRESVDT'] != "" and $arrs['ERESVDT'] != ""){
			$cond .= " and convert(varchar(8),A.RESVDT,112) between '".$arrs['SRESVDT']."' and '".$arrs['ERESVDT']."' ";
		}else if($arrs['SRESVDT'] != "" and $arrs['ERESVDT'] == ""){
			$cond .= " and convert(varchar(8),A.RESVDT,112) = '".$arrs['SRESVDT']."'";
		}else if($arrs['SRESVDT'] == "" and $arrs['ERESVDT'] != ""){
			$cond .= " and convert(varchar(8),A.RESVDT,112) = '".$arrs['ERESVDT']."'";
		}
		
		if($arrs['STRNO'] != ""){
			$cond .= " and A.STRNO like '".$arrs['STRNO']."%'";
		}
		
		if($arrs['CUSCOD'] != ""){
			$cond .= " and A.CUSCOD like '".$arrs['CUSCOD']."'";
		}
		
		$sql = "
			SELECT ".($cond == "" ? "top 20":"")." A.RESVNO,convert(varchar(8),A.RESVDT,112) as RESVDT
				,A.CUSCOD,B.SNAM+B.NAME1+' '+B.NAME2 as NAME,A.STRNO
			FROM {$this->MAuth->getdb('ARRESV')} A
			left join {$this->MAuth->getdb('CUSTMAST')} B on A.CUSCOD=B.CUSCOD
			where 1=1 ".$cond."
			order by A.RESVNO
		";
		//echo $sql; exit;		
		$query = $this->db->query($sql);
		
		$html = "";
		$NRow = 1;
		if($query->row()){
			foreach($query->result() as $row){
				$html .= "
					<tr>
						<td class='resvnoClick'>".$row->RESVNO."</td>
						<td>".$this->Convertdate(2,$row->RESVDT)."</td>
						<td>".$row->CUSCOD."</td>
						<td>".$row->NAME."</td>
						<td>".$row->STRNO."</td>
					</tr>
				";
				
				$NRow++;
			}
		}
		
		$html = "
			<div id='table-fixed-ReserveCar' class='col-sm-12' style='height:calc(100% - 30px);width:100%;overflow:auto;font-size:8pt;'>
				<table id='table-ReserveCar' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='100%' border=1>
					<thead>						
						<tr style='line-height:20px;'>
							<td style='vertical-align:middle;background-color:#ccc;text-align:center;font-size:8pt;' colspan='15'>
								เงื่อนไข
							</td>
						</tr>
						<tr>
							<th style='vertical-align:middle;background-color:#ccc;'>เลขที่บิลจอง</th>
							<th style='vertical-align:middle;background-color:#ccc;'>วันที่จอง</th>
							<th style='vertical-align:middle;background-color:#ccc;'>รหัสลูกค้า</th>
							<th style='vertical-align:middle;background-color:#ccc;'>ชื่อ-สกุล</th>
							<th style='vertical-align:middle;background-color:#ccc;'>เลขตัวถัง</th>
						</tr>
					</thead>	
					<tbody>						
						".$html."
					</tbody>
				</table>
			</div>
			<div>
				<img src='".base_url("/public/images/excel.png")."'  onclick=\"tableToExcel('table-CReport011', 'exporttoexcell');\" style='width:25px;height:25px;cursor:pointer;'/>
			</div>
		";
		
		$response = array("html"=>$html,"status"=>true);
		echo json_encode($response);
	}
}




















