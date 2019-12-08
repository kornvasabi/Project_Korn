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
						ใบรับชั่วคราว
						<input type='text' id='XXXXXX' class='form-control input-sm' value='' style='font-size:12pt;' readonly>
					</div>
				</div>
				<div class='col-sm-2'>	
					<div class='form-group'>
						วันที่รับเงิน
						<input type='text' id='XXXXXX' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' value='' maxlength=10>
					</div>
				</div>
				
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
						ชำระโดย
						<select id='locat' class='form-control input-sm chosen-select' data-placeholder='สาขา'>
							<option value=''></option>
						</select>
					</div>
				</div>
				<div class='col-sm-4'>	
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
				
				
				<div class='col-sm-2'>	
					<div class='form-group'>
						เลขที่อ้างอิง
						<input type='text' id='XXXXXX' class='form-control input-sm' value='' style='font-size:12pt;'>
					</div>
				</div>
				<div class='col-sm-2'>	
					<div class='form-group'>
						เลขที่เช็ค
						<input type='text' id='XXXXXX' class='form-control input-sm' value='' style='font-size:12pt;'>
					</div>
				</div>
				<div class='col-sm-2'>	
					<div class='form-group'>
						วันที่เช็ค
						<input type='text' id='XXXXXX' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' value='' maxlength=10>
					</div>
				</div>
				<div class='col-sm-2'>	
					<div class='form-group'>
						จำนวนเงิน
						<input type='text' id='XXXXXX' class='form-control input-sm' value='' style='font-size:12pt;'>
					</div>
				</div>
				<div class='col-sm-2'>	
					<div class='form-group'>
						ธนาคาร
						<select id='locat' class='form-control input-sm chosen-select' data-placeholder='สาขา'>
							<option value=''></option>
						</select>
					</div>
				</div>
				<div class='col-sm-2'>	
					<div class='form-group'>
						สาขาธนาคาร
						<input type='text' id='XXXXXX' class='form-control input-sm' value='' style='font-size:12pt;'>
					</div>
				</div>
				
				<div class='col-sm-2 col-sm-offset-4'>	
					<div class='form-group'>
						เลขที่ใบเสร็จ
						<input type='text' id='XXXXXX' class='form-control input-sm' value='' style='font-size:12pt;'>
					</div>
				</div>
				<div class='col-sm-2'>	
					<div class='form-group'>
						วันที่ใบเสร็จ
						<input type='text' id='XXXXXX' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' value='' maxlength=10>
					</div>
				</div>
				
				<div class='col-sm-12'>	
					<div class='row' style='width:100%;height:100%;padding-left:30px;background-color:#269da1;'>
						<div class='form-group col-sm-12' style='height:100%;'>
							<span style='color:#efff14;'>ชำระค่า</span>
							<div id='dataTable_fixed_ARMGAR' class='dataTables_wrapper dt-bootstrap4 table-responsive' style='max-height:calc(100% - 50px);height:calc(100% - 30px);overflow:auto;border:1px dotted black;background-color:white;'>
								<table id='dataTable_ARMGAR' class='table table-bordered dataTable table-hover table-secondary' id='dataTables_ARMGAR' stat='' role='grid' aria-describedby='dataTable_info' style='width:100%;line-height:10px;white-space:nowrap;text-overflow:ellipsis;overflow:hidden;' cellspacing='0'>
									<thead class='thead-dark' style='width:100%;'>
										<tr role='row'>
											<th style='width:40px'>
												<i id='add_payment' class='btn btn-xs btn-success glyphicon glyphicon-plus' style='cursor:pointer;'> เพิ่ม  </i>
											</th>
											<th>ชำระค่า</th>
											<th>เลขที่สัญญา</th>
											<th>จำนวนชำระ</th>
											<th>ส่วนลด</th>
											<th>ค่าเบี้ยปรับ</th>
											<th>ส่วนลดเบี้ยปรับ</th>
											<th>ยอดรับสุทธิ</th>
										</tr>
									</thead>
									<tbody style='white-space: nowrap;'></tbody>
								</table>
							</div>
						</div>
						
						<div class='col-sm-2 col-sm-offset-10'>	
							<div class='form-group'>
								<span style='color:#efff14;'>รวม</span>
								<input type='text' id='XXXXXX' class='form-control input-sm' value='' style='font-size:12pt;' readonly>
							</div>
						</div>
					</div>
				</div>
			</div>
		";
		
		$response = array("html"=>$html);
		echo json_encode($response);
	}
	
}




















