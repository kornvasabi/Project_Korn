<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/********************************************************
             ______@26/12/2019______
            / / _ _   _ _     __ 
           / // __ \ / __ \ / __ \
       _ _/ // /_/ // / / // /_/ /
     /_ _ _/ \_ _ //_/ /_/ \__  /
                          _ _/ /
                         /___ /
********************************************************/
class StandardSHC extends MY_Controller {
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
		
		$this->load->model('MMAIN');
	}
	
	function index(){
		$claim = $this->MLogin->getclaim(uri_string());
		if($claim['m_access'] != "T"){ echo "<div align='center' style='color:red;font-size:16pt;width:100%;'>ขออภัย คุณยังไม่มีสิทธิเข้าใช้งานหน้านี้ครับ</div>"; exit; }
		
		$html = "
			<div class='tab1' name='home' locat='{$this->sess['branch']}' is_mobile='{$this->sess['is_mobile']}' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}' style='height:calc(100vh - 132px);overflow:auto;background-color:white;'>
				<div class='col-sm-12' >
					<div class='row'>
						<div class='col-sm-2'>	
							<div class='form-group'>
								รุ่น
								<input type='text' id='SMODEL' class='form-control input-sm' placeholder='รุ่น' >
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								แบบ
								<input type='text' id='SBAAB' class='form-control input-sm' placeholder='แบบ' >
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								สี
								<input type='text' id='SCOLOR' class='form-control input-sm' placeholder='สี' >
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								ปีรถ
								<input type='text' id='SEVENTS' class='form-control input-sm' placeholder='ปีรถ' value=''>
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								กลุ่มรถ
								<input type='text' id='SEVENTE' class='form-control input-sm' placeholder='กลุ่มรถ' value=''>
							</div>
						</div>
						
						<div class='col-sm-6'>	
							<div class='form-group'>
								สาขา
								<select id='Search_LOCAT' class='form-control JD-BSSELECT' title='เลือก'  multiple data-actions-box='true' data-size='8' data-live-search='true'></select>
							</div>
						</div>
					</div>
					<div class='row'>
						<div class='col-sm-6'>	
							<div class='form-group'>
								<button id='btnt1createStd' class='btn btn-cyan btn-block'><span class='glyphicon glyphicon-pencil'> สร้าง</span></button>
							</div>
						</div>
						<div class='col-sm-6'>	
							<div class='form-group'>
								<button id='btnt1search' class='btn btn-primary btn-block'><span class='glyphicon glyphicon-search'> ค้นหา</span></button>
							</div>
						</div>
					</div>
				</div>
			</div>
		";
		
		$html.= "<script src='".base_url('public/js/SYS04/StandardSHC.js')."'></script>";
		echo $html;
	}
	
	function loadform(){
		$html = "
			<div class='col-sm-12 col-md-12 col-lg-8 col-lg-offset-2'>
				<div class='row' style='height:calc(100vh - 115px);border:0px solid red;overflow:auto;'>
						<div class='col-md-3 col-sm-4 col-xs-6'>
							<div class='form-group'>
								รุ่น
								<select class='form-control'></select>
							</div>
						</div>
						<div class='col-md-3 col-md-offset-6 col-sm-4 col-sm-offset-4 col-xs-6'>
							<div class='form-group'>
								แบบ
								<select class='form-control'></select>
							</div>
						</div>
						<div class='col-xs-12 col-sm-6'>
							<div class='form-group'>
								สี
								<select id='COLOR' multiple='multiple' size='10' name='duallistbox_demo1[]'></select>
							</div>
						</div>			
						<div class='col-xs-12 col-sm-6'>
							<div class='form-group'>
								สาขา
								<select id='LOCAT' multiple='multiple' size='10' name='duallistbox_demo1[]'></select>
							</div>
						</div>
						<div class='col-sm-3'>
							<div class='form-group'>
								ปี
								<input type='text' class='form-control'>
							</div>
						</div>
						<div class='col-sm-3'>
							<div class='form-group'>
								กลุ่มรถ
								<select class='form-control'></select>
							</div>
						</div>
						<div class='col-sm-3'>
							<div class='form-group'>
								ราคารถใหม่
								<input type='text' class='form-control'>
							</div>
						</div>
						<div class='col-sm-3'>
							<div class='form-group'>
								ราคามือสอง
								<input type='text' class='form-control'>
							</div>
						</div>
						
				</div>
				<div class='row'>
					<div class='col-sm-2 col-sm-offset-10'>	
						<div class='form-group'>
							<button id='btnt1search' class='btn btn-xs btn-primary btn-block' ><span class='glyphicon glyphicon-floppy-disk'> บันทึก</span></button>
						</div>
					</div>
				</div>
			</div>
		";
		
		$response = array('html'=>$html,'status'=>true);
		echo json_encode($response);
	}	
	
}

































