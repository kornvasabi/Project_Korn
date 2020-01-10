<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/********************************************************
             ______@09/01/2020______
			 Pasakorn Boonded

********************************************************/
class ReportReceivedSV extends MY_Controller {
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
						<div class='col-sm-12 col-xs-12' style='background-color:#808b96;border:5px solid white;height:75px;text-align:center;font-size:12pt;color:white;font-weight:bold;'>	
							<br>รายงานการรับชำระเงินตามวันที่บันทึก<br>
						</div>
						<div class='col-sm-10 col-xs-10 col-sm-offset-1 text-primary'>	
							<br>
							<div class='col-sm-3'>	
								<div class='form-group'>
									รับชำระที่สาขา
									<select id='LOCATRECV' class='form-control input-sm' data-placeholder='สาขา'></select>
								</div>
							</div>
							<div class='col-sm-3'>	
								<div class='form-group' >
									เพื่อ บ/ช ของสาขา
									<select id='LOCATPAY' class='form-control input-sm' data-placeholder='สาขา'></select>
								</div>
							</div>
							<div class='col-sm-3'>	
								<div class='form-group'>
									รับชำระจากวันที่
									<input type='text' id='DATE1' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' value='".$this->today('today')."' style='font-size:10.5pt'>
								</div>
							</div>
							<div class='col-sm-3'>	
								<div class='form-group'>
									ถึงวันที่
									<input type='text' id='DATE2' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' value='".$this->today('today')."' style='font-size:10.5pt'>
								</div>
							</div>
							<div class='col-sm-3'>	
								<div class='form-group'>
									ชำระโดย
									<select id='PAYTYP' class='form-control input-sm' data-placeholder='ชำระโดย'></select>
								</div>
							</div>
							<div class='col-sm-3'>	
								<div class='form-group'>
									ชำระค่า
									<select id='PAYFOR' class='form-control input-sm' data-placeholder='ชำระค่า'></select>
								</div>
							</div>
							<div class='col-sm-3'>	
								<div class='form-group'>
									พนักงานที่บันทึก
									<select id='USERID' class='form-control input-sm' data-placeholder='พนักงานที่บันทึก'></select>
								</div>
							</div>
							<div class='col-sm-3'>	
								<div class='form-group'>
									กลุ่มลูกค้า
									<select id='GROUP1' class='form-control input-sm' data-placeholder='กลุ่มลูกค้า'></select>
								</div>
							</div>
							<div class='col-sm-3'>	
								<div class='form-group'>
									รหัสพนักงานเก็บเงิน
									<select id='CODE' class='form-control input-sm' data-placeholder='รหัสพนักงานเก็บเงิน'></select>
								</div>
							</div>
						</div>
						<div class='col-sm-10 col-xs-10 col-sm-offset-1'><br>	
							<div class='col-sm-12 col-xs-12'>	
								<div class='form-group'>
									รูปแบบรายงาน
									<div class='col-sm-12 col-xs-12' style='border:0.1px dotted #d6d6d6;'>	
										<div class='col-sm-6'>
											<div class='form-group'>
												<br>
												<label>
													<input type= 'radio' id='all' name='report' checked> แสดงรายการทั้งหมด
												</label>
											</div>
										</div>
										<div class='col-sm-6'>
											<div class='form-group'>
												<br>
												<label>
													<input type= 'radio' id='pay' name='report'> สรุปตามการรับชำระ
												</label>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>	
						<div class='col-sm-10 col-xs-10 col-sm-offset-1'><br>	
							<div class='col-sm-12 col-xs-12'>	
								<div class='form-group'>
									เรียงลำดับข้อมูลตาม <br>
									<div class='col-sm-12 col-xs-12' style='border:0.1px dotted #d6d6d6;'>	
										<div class='col-sm-4'>
											<div class='form-group'>
												<br>
												<label>
													<input type= 'radio' id='set1' name='Sort' checked> วันที่,เลขที่ใบรับเงิน
												</label>
											</div>
										</div>
										<div class='col-sm-4'>
											<div class='form-group'>
												<br>
												<label>
													<input type= 'radio' id='set2' name='Sort'> วันที่เช็ค
												</label>
											</div>
										</div>
										<div class='col-sm-4'>
											<div class='form-group'>
												<br>
												<label>
													<input type= 'radio' id='set3' name='Sort'> เลขที่สัญญา
												</label>
											</div>
										</div>
										<div class='col-sm-4'>
											<div class='form-group'>
												<br>
												<label>
													<input type= 'radio' id='set4' name='Sort'> เลขที่ใบเสร็จรับเงิน
												</label>
											</div>
										</div>
										<div class='col-sm-4'>
											<div class='form-group'>
												<br>
												<label>
													<input type= 'radio' id='set5' name='Sort'> วันที่นำฝาก
												</label>
											</div>
										</div>
										<div class='col-sm-4'>
											<div class='form-group'>
												<br>
												<label>
													<input type= 'radio' id='set6' name='Sort'> เลขที่ใบอ้างอิง
												</label>
											</div>
										</div>
										<div class='col-sm-4'>
											<div class='form-group'>
												<br>
												<label>
													<input type= 'radio' id='set7' name='Sort'> สาขาที่ได้รับ
												</label>
											</div>
										</div>
									</div>
								</div>
								<div class='col-sm-12 col-xs-12'>
									<br>
									<button id='btnreport' type='button' class='btn btn-info btn-outline btn-block' style='width:100%'><span class='fa fa-folder-open'><b>แสดง</b></span></button>
								</div><br>
							</div>
						</div>
					</div>
				</div>
			</div>
		";
		$html .="<script src='".base_url('public/js/SYS06/ReportReceivedSV.js')."'></script>";
		echo $html;
	}
	function conditiontopdf(){
		$data = array();
		$data[] = urlencode($_REQUEST["LOCATRECV"].'||'.$_REQUEST["LOCATPAY"].'||'.$_REQUEST["DATE1"]
		.'||'.$_REQUEST["DATE2"].'||'.$_REQUEST["PAYTYP"].'||'.$_REQUEST["PAYFOR"].'||'.$_REQUEST["USERID"]
		.'||'.$_REQUEST["GROUP1"].'||'.$_REQUEST["CODE"].'||'.$_REQUEST["sort"]);
		echo json_encode($this->generateData($data,"encode"));
	}
	function pdfall(){
		
	}
	function pdfpay(){
		
	}
}