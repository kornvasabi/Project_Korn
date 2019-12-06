<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/********************************************************
             ______@12/11/2019______
            / / _ _   _ _     __ 
           / // __ \ / __ \ / __ \
       _ _/ // /_/ // / / // /_/ /
     /_ _ _/ \_ _ //_/ /_/ \__  /
                          _ _/ /
                         /___ /
********************************************************/
class RevPayment extends MY_Controller {
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
		//print_r($claim); exit;
		if($claim['m_access'] != "T"){ echo "<div align='center' style='color:red;font-size:16pt;width:100%;'>ขออภัย คุณยังไม่มีสิทธิเข้าใช้งานหน้านี้ครับ</div>"; exit; }
		
		$html = "
			<div class='tab1' name='home' locat='{$this->sess['branch']}' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}'>
				<div>
					<div class='row'>
						<div class='col-sm-2'>	
							<div class='form-group'>
								สาขา
								<select id='locat' class='form-control input-sm chosen-select' data-placeholder='สาขา'>
									<option value='{$this->sess['branch']}'>{$this->sess['branch']}</option>
								</select>
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								วันที่รับ จาก
								<input type='text' id='SDATE' class='form-control input-sm' placeholder='จาก' data-provide='datepicker' data-date-language='th-th' value='".$this->today('startofmonthB1')."'>
							</div>
						</div>	
						<div class='col-sm-2'>	
							<div class='form-group'>
								วันที่รับ ถึง
								<input type='text' id='EDATE' class='form-control input-sm' placeholder='ถึง' data-provide='datepicker' data-date-language='th-th' value='".$this->today('endofmonthB1')."'>
							</div>
						</div>	
						
						<div class='col-sm-2'>	
							<div class='form-group'>
								ยี่ห้อ
								<select id='TYPE' class='form-control input-sm chosen-select' data-placeholder='ยี่ห้อ'></select>
							</div>
						</div>
						
						<div class='col-sm-2'>	
							<div class='form-group'>
								รุ่น
								<select id='MODEL' class='form-control input-sm chosen-select' data-placeholder='รุ่น'></select>
							</div>
						</div>
						
						<div class='col-sm-2'>	
							<div class='form-group'>
								สถานะรถ
								<select id='STAT' class='form-control input-sm chosen-select' data-placeholder='สถานะรถ'>
									<option value='A'>ทั้งหมด</option>
									<option value='N'>รถใหม่</option>
									<option value='O'>รถเก่า</option>
								</select>
							</div>
						</div>						
					</div>
					<!-- div class='row'>
						<div class='col-sm-2 col-sm-offset-4'>
							<b>รายงาน</b>
							<div class='radio'><label><input type='radio' class='sort' name='REPORT' value='1' checked=''>วันที่รับ</label></div>
							<div class='radio'><label><input type='radio' class='sort' name='REPORT' value='2'>วันที่ใบกำกับภาษี</label></div>
						</div>
						
						<div class='col-sm-2'>
							<b>สินค้าและวัตถุดิบ</b>
							<div class='radio'><label><input type='radio' class='sort' name='turnover' value='Y' checked=''>มีการเคลื่อนไหว</label></div>
							<div class='radio'><label><input type='radio' class='sort' name='turnover' value='N'>ทั้งหมด</label></div>
						</div>
					</div -->
					<div class='row'>
						<div class='col-sm-6'>	
							<div class='form-group'>
								<button id='btnt1revpayment' class='btn btn-cyan btn-block'>
									<span class='glyphicon glyphicon-pencil'> รับชำระ</span>
								</button>
							</div>
						</div>
						<div class='col-sm-6'>	
							<div class='form-group'>
								<button id='btnt1search' class='btn btn-primary btn-block'>
									<span class='glyphicon glyphicon-search'> แสดง</span>
								</button>
							</div>
						</div>
					</div>
				</div>
				<div id='result'></div>
			</div>
		";
		
		$html.= "<script src='".base_url('public/js/SYS06/RevPayment.js')."'></script>";
		echo $html;
	}
	
	
	
	function get_form_received(){
		$html = "
			<div>
				<div class='col-sm-2'>	
					<div class='form-group'>
						สาขา
						<select id='locat' class='form-control input-sm chosen-select' data-placeholder='สาขา'>
							<option value='{$this->sess['branch']}'>{$this->sess['branch']}</option>
						</select>
					</div>
				</div>
				<div class='col-sm-3'>	
					<div class='form-group'>
						ชื่อสกุล-ลูกค้า
						<div class='input-group'>
						   <input type='text' id='fCUSCOD' CUSCOD='' class='form-control input-sm' placeholder='ลูกค้า'  value=''>
						   <span class='input-group-btn'>
						   <button id='fCUSCOD_removed' class='btn btn-danger btn-sm' type='button'>
								<span class='glyphicon glyphicon-remove' aria-hidden='true'></span></button>
						   </span>
						</div>
					</div>
				</div>
			</div>
		";
		
		$response = array("html"=>$html);
		echo json_encode($response);
	}
	
}




















