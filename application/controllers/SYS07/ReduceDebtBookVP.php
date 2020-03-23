<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/********************************************************
             ______@16/03/2020______
			 Pasakorn Boonded

********************************************************/
class ReduceDebtBookVP extends MY_Controller {
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
						<div class='col-sm-10 col-xs-10 col-sm-offset-1 text-primary'>	
							<br>
							<div class='col-sm-3'>	
								<div class='form-group'>
									ออกใบลดหนี้ที่สาขา
									<select id='LOCAT' class='form-control input-sm' data-placeholder='สาขา'></select>
								</div>
							</div>
							<div class='col-sm-3'>	
								<div class='form-group'>
									ประเภทใบลดหนี้
									<select id='TAXTYP' value='9' class='form-control input-sm' disabled><option>9</option></select>
								</div>
							</div>
							<div class='col-sm-3'>	
								<div class='form-group'>
									อ้างถึงใบกำกับเลขที่
									<select id='TAXNO' class='form-control input-sm' data-placeholder='เลือก'></select>
								</div>
							</div>
							<div class='col-sm-3'>	
								<div class='form-group'>
									เลขตัวถัง
									<input type='text' id='STRNO' class='form-control input-sm' disabled>
								</div>
							</div>
							<div class='col-sm-3'>	
								<div class='form-group'>
									เลขที่ใบลดหนี้
									<input type='text' id='TAXNO2' class='form-control input-sm'>
								</div>
							</div>
							<div class='col-sm-3'>	
								<div class='form-group'>
									วันที่ออก
									<input type='text' id='TAXDT' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' style='font-size:10.5pt'>
								</div>
							</div>
							<div class='col-sm-3'>	
								<div class='form-group'>
									ลงวันที่
									<input type='text' id='REFDT' class='form-control input-sm' disabled>
								</div>
							</div>
							<div class='col-sm-3'>	
								<div class='form-group'>
									เลขที่สัญญา
									<input type='text' id='CONTNO' class='form-control input-sm' disabled>
								</div>
							</div>
							
							<div class='col-sm-4'>	
								<div class='form-group'>
									รหัสลูกค้า
									<input type='text' id='CUSCOD' class='form-control input-sm' disabled>
								</div>
							</div>
							<div class='col-sm-2'>	
								<div class='form-group'>
									<br>
									<input type='text' id='SNAM' class='form-control input-sm' disabled>
								</div>
							</div>
							<div class='col-sm-3'>	
								<div class='form-group'>
									<br>
									<input type='text' id='NAME1' class='form-control input-sm' disabled>
								</div>
							</div>
							<div class='col-sm-3'>	
								<div class='form-group'>
									<br>
									<input type='text' id='NAME2' class='form-control input-sm' disabled>
								</div>
							</div>
							<div class='col-sm-2'>	
								<div class='form-group'>
									ประเภทการขาย
									<input type='text' id='TSALE' class='form-control input-sm' disabled>
								</div>
							</div>
							<div class='col-sm-3'>	
								<div class='form-group'>
									รายการ
									<input type='text' id='DESCP' class='form-control input-sm' disabled>
								</div>
							</div>
							<div class='col-sm-3'>	
								<div class='form-group'>
									สาเหตุที่ออกใบลดหนี้
									<select id='RESONCD' class='form-control input-sm'></select>
								</div>
							</div>
							<div class='col-sm-4'>	
								<div class='form-group'>
									<br>
									<input type='text' id='RESNDES' class='form-control input-sm' disabled>
								</div>
							</div>
						</div>
						<div class='col-sm-10 col-xs-10 col-sm-offset-1'><br>	
							<div class='col-sm-12 col-xs-12'>	
								<div class='form-group'>
									<div class='col-sm-12 col-xs-12' style='border:0.1px dotted #d6d6d6;background-color:#148f77;'>	
										<div class='col-sm-12 col-xs-12'>
											<div class='col-sm-7' style='text-align:right;'>
											</div>
											<div class='col-sm-4' style='text-align:left;color:#f5cc28;'>	
												<div class='form-group'>
													<b>มูลค่าสินค้า</b>
													<div class='input-group'>
														<!--input id='NETAMT' value='0.00' style='text-align:right;' class='form-control input-sm jzAllowNumber' type='text' onkeyup='format(this)' onchange='format(this)' onblur='if(this.value.indexOf('.')==-1)this.value=this.value+'.00''--> 
														<input type='text' id='NETAMT' class='form-control input-sm jzAllowNumber' style='text-align:right;' value='0.00' readonly>
														<span class='input-group-addon'>บาท</span>
													</div>
												</div>
											</div>
										</div>
										<div class='col-sm-12 col-xs-12'>
											<div class='col-sm-7' style='text-align:center;'>
												<br>
												<input id='stana' style='text-align:center;font-size:20px;' type='text' value='***ยกเลิก***' id='VATAMT' class='form-control input-sm text-danger bg-warning' readonly>
											</div>
											<div class='col-sm-4' style='text-align:left;color:#f5cc28;'>	
												<div class='form-group'>
													<b>ภาษีมูลค่าเพิ่ม</b>
													<div class='input-group'>
														<input type='text' id='VATAMT' class='form-control input-sm jzAllowNumber text-primary' style='text-align:right;' value='0.00' readonly>
														<span class='input-group-addon'>บาท</span>
													</div>
												</div>
											</div>
										</div>
										<div class='col-sm-12 col-xs-12'>
											<div class='col-sm-7' style='text-align:right;'>
											</div>
											<div class='col-sm-4' style='text-align:left;color:#f5cc28;'>	
												<div class='form-group'>
													<b>ยอดรวมสุทธิ</b>
													<div class='input-group'>
														<input type='text' id='TOTAMT' class='form-control input-sm jzAllowNumber text-danger' style='text-align:right;' value='0.00' readonly>
														<span class='input-group-addon'>บาท</span>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class='col-sm-2 col-sm-2'></div>
								<div class='col-sm-2 col-sm-2'>
									<br>
									<button id='btnaddRD' type='button' class='btn btn-info'style='width:100%;'><span class='fa fa-plus-square'><b>เพิ่ม</b></span></button>
								</div>
								<div class='col-sm-2 col-sm-2'>
									<br>
									<button id='btnsaveRD' type='button' class='btn btn-primary' style='width:100%;'><span class='fa fa-floppy-o'><b>บันทึก</b></span></button>
								</div>
								<div class='col-sm-2 col-sm-2'>
									<br>
									<button id='btndelRD' type='button' class='btn btn-info btn btn-danger'style='width:100%;'><span class='glyphicon glyphicon-trash'><b>ลบ</b></span></button>
								</div><br><div class='col-sm-2 col-sm-2'>
									<br>
									<button id='btnshowRD' type='button' class='btn btn-info btn btn-cyan'style='width:100%;'><span class='fa fa-folder-open'><b>สอบถาม</b></span></button>
								</div><br><div class='col-sm-2 col-sm-2'>
									<br>
									<button id='btnclearRD' type='button' class='btn btn-info btn btn-light'style='width:100%;'><span class='' style='color:blue;'><b>Clear</b></span></button>
								</div><br>
							</div>
						</div>
					</div>
				</div>
			</div>
		";
		$html .="<script src='".base_url('public/js/SYS07/ReduceDebtBookVP.js')."'></script>";
		echo $html;
	}
}