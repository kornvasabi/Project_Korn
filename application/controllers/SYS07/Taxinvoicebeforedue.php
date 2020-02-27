<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//BEE+
class Taxinvoicebeforedue extends MY_Controller {
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
	
	//หน้าแรก
	function index(){
		$claim = $this->MLogin->getclaim(uri_string());
		if($claim['m_access'] != "T"){ echo "<div align='center' style='color:red;font-size:16pt;width:100%;'>ขออภัย คุณยังไม่มีสิทธิเข้าใช้งานหน้านี้ครับ</div>"; exit; }

		$html = "
			<div class='b_tab1' name='home' locat='{$this->sess['branch']}' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}' today='".$this->today('today')."' style='height:calc(100vh - 132px);overflow:auto;background-color:#f6fefa;'>
				<div class='col-sm-12 col-xs-12' style='height:100%;overflow:auto;font-size:10.5pt;'>					
					<div class='row' style='height:90%;'><br>
						<div class='col-sm-6 col-xs-6 col-sm-offset-3' style='border:0.1px dotted #d6d6d6;'>
							<div class='col-sm-6 col-xs-6'>	
								<label class='radio lobiradio lobiradio-info'>
									<input type='radio' id='normal' name='vat' value='normal' checked><i></i> ยื่นภาษีปกติ
								</label>
							</div>
							<div class='col-sm-6 col-xs-6'>	
								<label class='radio lobiradio lobiradio-info'>
									<input type='radio' id='more' value='more' name='vat'><i></i> ยื่นเพิ่มเติม
								</label>
							</div>
						</div>
						<div class='col-sm-6 col-xs-6 col-sm-offset-3'>
							<br>
							<div class='col-sm-12 col-xs-12'>	
								<div class='form-group'>
									วันที่ออกใบกำกับภาษียื่นเพิ่มเติม
									<input type='text' id='VATDATE' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' value='' style='font-size:10.5pt' disabled>
								</div>
							</div>
						</div>
						<div class='col-sm-6 col-xs-6 col-sm-offset-3'>
							<div class='col-sm-12 col-xs-12'>	
								<div class='form-group'>
									รหัสสาขา
									<select id='LOCAT1' class='form-control input-sm' data-placeholder='รหัสสาขา'></select>
								</div>
							</div>
							<div class='col-sm-12 col-xs-12'>	
								<div class='form-group'>
									จากวันที่ดิว
									<input type='text' id='FRMDATE' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' placeholder='จากวันที่' value='".$this->today('today')."' style='font-size:10.5pt'>
								</div>
							</div>
							<div class='col-sm-12 col-xs-12'>	
								<div class='form-group'>
									ถึงวันที่
									<input type='text' id='TODATE' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' placeholder='ถึงวันที่' value='".$this->today('today')."' style='font-size:10.5pt'>
								</div>
							</div>
							<div class='col-sm-12 col-xs-12'>	
								<div class='form-group' >
									เฉพาะเลขที่สัญญา
									<select id='CONTNO1' class='form-control input-sm' data-placeholder='เลขที่สัญญา'></select>
								</div>
							</div>
							<div class='col-sm-12 col-xs-12'>	
								<div class='form-group'>
									<button id='btnt1search' class='btn btn-info btn-sm' style='width:100%;font-size:10.5pt;'><span class='glyphicon glyphicon-search'> สอบถามการ Run ล่าสุด</span></button>
								</div>
							</div>
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group '>
									วันที่ RUN ใบกำกับล่าสุด
									<input type='text' id='LATEDAY1' class='form-control input-sm' style='font-size:10.5pt' disabled>
								</div>
							</div>
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group'>
									ใบกำกับภาษีเลขที่ล่าสุด
									<input type='text' id='LATEDAY1' class='form-control input-sm' style='font-size:10.5pt' disabled>
								</div>
							</div>
						</div>
					</div>
					<div class='row' style='height:10%;'>
						<div class='col-sm-6 col-xs-6 col-sm-offset-3'>
							<div class='col-sm-6 col-xs-6'>	
								<button id='btntprint' class='btn btn-primary btn-sm' style='width:100%;font-size:10.5pt;'><span class='glyphicon glyphicon-print'> Print</span></button>
							</div>
							<div class='col-sm-6 col-xs-6'>	
								<button id='btntrunno' class='btn btn-primary btn-sm' style='width:100%;font-size:10.5pt;'><span class='glyphicon glyphicon-ok'> RunNo</span></button>
							</div>
						</div>
						<div class='col-sm-12 col-xs-12 text-red' style='font-size:8pt;text-align:center;'>	
							<br>
							** ก่อนออกใบกำกับภาษีก่อนดิว ต้องตรวจสอบการรับชำระให้ถูกต้องก่อน หากต้องการยกเลิกการรับชำระรายการที่ออกใบกำกับภาษี อาจทำให้ภาษีไม่ถูกต้อง **
						</div>
					</div>
				</div>
			</div>
		";
		
		$html.= "<script src='".base_url('public/js/SYS07/Taxinvoicebeforedue.js')."'></script>";
		echo $html;
	}
}