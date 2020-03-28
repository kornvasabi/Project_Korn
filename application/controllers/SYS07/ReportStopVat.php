<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/********************************************************
             ______@28/02/2020______
			 Pasakorn Boonded

********************************************************/
class ReportStopVat extends MY_Controller {
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
				<div class='col-sm-12 col-xs-12' style='height:100%;overflow:auto;font-size:11pt;'>					
					<div class='row' style='height:90%;'>
						<div class='col-sm-12 col-xs-12' style='background-color:#2e86c1;border:5px solid white;height:75px;text-align:center;font-size:12pt;color:white;font-weight:bold;'>	
							<br>รายงานลูกหนี้หยุด Vat<br>
						</div>
						<div class='col-sm-10 col-xs-10 col-sm-offset-1 text-primary'>	
							<br>
							<div class='col-sm-4'>	
								<div class='form-group'>
									รหัสสาขา
									<select id='LOCAT' class='form-control input-sm'></select>
								</div>
							</div>
							<div class='col-sm-4'>	
								<div class='form-group'>
									จากเลขที่สาขา
									<select id='' class='form-control input-sm'></select>
								</div>
							</div>
							<div class='col-sm-4'>	
								<div class='form-group'>
									ถึงเลขที่สาขา
									<select id='' class='form-control input-sm'></select>
								</div>
							</div>
							<div class='col-sm-4'>	
								<div class='form-group'>
									BillColl
									<select id='' class='form-control input-sm'></select>
								</div>
							</div>
							<div class='col-sm-4'>	
								<div class='form-group'>
									จากวันที่หยุด Vat
									<input type='text' id='STOPDT' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' value='".$this->today('today')."' styl='font-size:10.5pt;'>
								</div>
							</div>
							<div class='col-sm-4'>	
								<div class='form-group'>
									ถึงวันที่หยุด Vat
									<input type='text' id='STOPDT' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' value='".$this->today('today')."' styl='font-size:10.5pt;'>
								</div>
							</div>
						</div>
						<div class='col-sm-10 col-xs-10 col-sm-offset-1'><br>	
							<div class='col-sm-12 col-xs-12'>	
								<div class='form-group'>
									เรียงลำดับข้อมูล<br>
									<div class='col-sm-12 col-xs-12' style='border:0.1px dotted #d6d6d6;'>	
										<div class='col-sm-12 col-sm-offset-1'>
											<div class='col-sm-3'>
												<div class='form-group'>
													<br>
													<label>
														<input type= 'radio' id='or1' name='order' checked> เลขที่สัญญา
													</label>
												</div>
											</div>
											<div class='col-sm-3'>
												<div class='form-group'>
													<br>
													<label>
														<input type= 'radio' id='or2' name='order'> BillColl
													</label>
												</div>
											</div>
											<div class='col-sm-3'>
												<div class='form-group'>
													<br>
													<label>
														<input type= 'radio' id='or3' name='order'> วันที่หยุด Vat
													</label>
												</div>
											</div>
											<div class='col-sm-3'>
												<div class='form-group'>
													<br>
													<label>
														<input type= 'radio' id='or4' name='order'> เลขตัวถัง
													</label>
												</div>
											</div>
										</div>	
									</div>
								</div>
								<div class='col-sm-12 col-xs-12'>
									<br>
									<button id='btnBateDebt' type='button' class='btn btn-info btn-outline btn-block' style='width:100%'><span class='fa fa-folder-open'><b>แสดง</b></span></button>
								</div><br>
							</div>
						</div>
					</div>
				</div>
			</div>
		";
		$html .="<script src='".base_url('public/js/SYS07/ReportStopVat.js')."'></script>";
		echo $html;
	}
}