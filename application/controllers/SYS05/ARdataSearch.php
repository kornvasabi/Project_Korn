<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//BEE+
class ARdataSearch extends MY_Controller {
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
			<div class='b_tab1' name='home' locat='{$this->sess['branch']}' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}' today='".$this->today('today')."' style='height:calc(100vh - 132px);overflow:auto;'>
				<div class='col-sm-12 col-xs-12' style='float:left;height:100%;overflow:auto;'>
					<div id='wizard-leasing' class='wizard-wrapper'>    
						<div class='wizard'>
							<form id='demo-form2' action='' class='lobi-form' novalidate='novalidate'>
								<ul class='wizard-tabs nav-justified nav nav-pills'>
									<li class='active' style='background-color:#fef7cd;border:3px solid #fef7cd;'>
										<a href='#tab11' prev='#' data-toggle='tab' aria-expanded='true'>
											<span class='step'></span>
											<span class='title'><b>ยอดค้างชำระ</b></span>
										</a>
									</li>
									<li style='background-color:#fef7cd;border:3px solid #fef7cd;'>
										<a href='#tab22' prev='#tab11' data-toggle='tab'>
											<span class='step'></span>
											<span class='title'><b>อุปกรณ์เสริม</b></span>
										</a>
									</li>
									<li style='background-color:#fef7cd;border:3px solid #fef7cd;'>
										<a href='#tab33' prev='#tab22' data-toggle='tab'>
											<span class='step'></span>
											<span class='title'><b>รายละเอียดสัญญา</b></span>
										</a>
									</li>
									<li style='background-color:#fef7cd;border:3px solid #fef7cd;'>
										<a href='#tab44' prev='#tab33' data-toggle='tab'>
											<span class='step'></span>
											<span class='title'><b>ลูกหนี้อื่น</b></span>
										</a>
									</li>							
								</ul>
								<div class='tab-content bg-white'>
									".$this->getfromLeasingTab11()."
									".$this->getfromLeasingTab22()."
									".$this->getfromLeasingTab33()."
									".$this->getfromLeasingTab44()."
								</div>
							</form>
						</div>
					</div>				
				</div>
			</div>
		";
		
		$html.= "<script src='".base_url('public/js/SYS05/ARdataSearch.js')."'></script>";
		echo $html;
	}
	
	private function getfromLeasingTab11(){//style='border:2px dotted white;background-color:#dfe9ec;'
		$html = "
			<div class='tab-pane active' name='tab11' style='height:calc(100vh - 220px);overflow:auto;'>
				<fieldset style='height:100%'>
					<div style='float:left;height:100%;' class='col-sm-12 col-xs-12'>
						<div class='row'>
							<div class=' col-sm-10 col-xs-10 col-sm-offset-1'>
								<div class=' col-sm-4 col-xs-4 col-sm-offset-1'>	
									<div class='form-group text-primary'>
										<b>ลูกค้า</b>
										<select id='CUSCOD1' class='form-control input-sm' data-placeholder='ลูกค้า'></select>
									</div>
								</div>
								<div class=' col-sm-4 col-xs-4'>	
									<div class='form-group text-primary'>
										<b>เลขที่สัญญา</b>
										<select id='CONTNO1' class='form-control input-sm' data-placeholder='เลขที่สัญญา'></select>
									</div>
								</div>
								<div class=' col-sm-2 col-xs-2'>	
									<div class='form-group'>
										<br>
										<button id='btnt1search' class='btn btn-primary btn-sm' style='width:100%'><span class='glyphicon glyphicon-search'><b> สอบถาม</b></span></button>
									</div>
								</div>
							</div>
						</div>
						<br>
						<div class='row' style='height:25%;border:0.1px solid #bdbdbd;background-color:#eee;'>
							<div class='col-sm-12 col-xs-12' style='height:100%;'>
								<div id='dataTable-fixed-cusdata' class='dataTables_wrapper dt-bootstrap4 table-responsive' style='height:100%;width:100%;overflow:auto;'>
									<table id='dataTables-cusdata' class='table table-bordered dataTable table-hover' stat='' aria-describedby='dataTable_info' cellspacing='0' width='calc(100% - 1px)'>
										<thead>
											<tr role='row' style='height:30px;font-size:8pt;background-color:#666666;color:white;'>
												<th width='12.5%' style='vertical-align:middle;'>เลขที่สัญญา</th>
												<th width='12.5%' style='vertical-align:middle;'>สาขา</th>
												<th width='12.5%' style='vertical-align:middle;'>ประเภทการขาย</th>
												<th width='12.5%' style='text-align:right;vertical-align:middle;'>ราคาขาย</th>
												<th width='12.5%' style='text-align:right;vertical-align:middle;'>ชำระแล้ว</th>
												<th width='12.5%' style='text-align:right;vertical-align:middle;'>ลูกหนี้คงเหลือ</th>
												<th width='12.5%' style='text-align:right;vertical-align:middle;'>เช็ครอเรียกเก็บ</th>
												<th width='12.5%' style='text-align:right;vertical-align:middle;'>ยอดคงเหลือหักเช็ค</th>
											</tr>
										</thead>
										<tbody style='white-space:nowrap;background-color:white;font-size:9pt;'></tbody>
									</table>
								</div>
							</div>
						</div>
						<div class='row' style='border:0.1px solid #bdbdbd;background-color:#dedede;'>
							<div class='col-sm-12 col-xs-12' style='height:100%;'>	
								<div id='fix-sumcusdata' style='width:100%;overflow:auto;'>
									<table id='sumcusdata' width='calc(100% - 1px)'>
										<tr style='font-size:9pt;'>
											<th colspan='3' class='text-primary' style='text-align:center;'><b>รวม</b></th>
											<th width='12.5%' style='padding:2px;'><input type='text' id='1_SUMTOTPRC' class='text-primary' style='text-align:right;' readonly></th>
											<th width='12.5%' style='padding:2px;'><input type='text' id='1_SUMSMPAY' 	class='text-primary' style='text-align:right;' readonly></th>
											<th width='12.5%' style='padding:2px;'><input type='text' id='1_SUMBALANC' class='text-primary' style='text-align:right;' readonly></th>
											<th width='12.5%' style='padding:2px;'><input type='text' id='1_SUMSMCHQ' 	class='text-primary' style='text-align:right;' readonly></th>
											<th width='12.5%' style='padding:2px;'><input type='text' id='1_SUMTOTAL' 	class='text-primary' style='text-align:right;' readonly></th>
										</tr>
									</table>
								</div>
							</div>
						</div>
						<div class='row' id='CONTDETAIL'>
							<br><b>รายละเอียดสินค้า</b>
							<div class='col-sm-12 col-xs-12' style='border:0.1px dotted #999;'>
								<div class='col-sm-4 col-xs-4'>	
									<div class='form-group'>
										เลขตัวถัง
										<input type='text' id='1_STRNO' class='form-control input-sm' disabled>
									</div>
								</div>
								<div class='col-sm-4 col-xs-4'>	
									<div class='form-group'>
										เลขเครื่อง
										<input type='text' id='1_ENGNO' class='form-control input-sm' disabled>
									</div>
								</div>
								<div class='col-sm-4 col-xs-4'>	
									<div class='form-group'>
										เลขทะเบียน
										<input type='text' id='1_REGNO' class='form-control input-sm' disabled>
									</div>
								</div>
								<div class='col-sm-4 col-xs-4'>	
									<div class='form-group'>
										ยี่ห้อ
										<input type='text' id='1_TYPE' class='form-control input-sm' disabled>
									</div>
								</div>
								<div class='col-sm-4 col-xs-4'>	
									<div class='form-group'>
										รุ่น
										<input type='text' id='1_MODEL' class='form-control input-sm' disabled>
									</div>
								</div>
								<div class='col-sm-4 col-xs-4'>	
									<div class='form-group'>
										แบบ
										<input type='text' id='1_BAAB' class='form-control input-sm' disabled>
									</div>
								</div>
								<div class='col-sm-4 col-xs-4'>	
									<div class='form-group'>
										สี
										<input type='text' id='1_COLOR' class='form-control input-sm' disabled>
									</div>
								</div>
								<div class='col-sm-2 col-xs-2'>	
									<div class='form-group'>
										ขนาด
										<input type='text' id='1_CC' class='form-control input-sm' disabled>
									</div>
								</div>
								<div class='col-sm-2 col-xs-2'>	
									<div class='form-group'>
										สภาพ
										<input type='text' id='1_STAT' class='form-control input-sm' disabled>
									</div>
								</div>
								<div class='col-sm-4 col-xs-4'>	
									<div class='form-group'>
										วันเดือนปีที่ขาย
										<input type='text' id='1_SDATE' class='form-control input-sm' disabled>
									</div>
								</div>
							</div>
						</div>
						<div class='row' id='CONTDETAIL_N' style='height:8%;display:none;'>
							<table style='width:100%;height:100%;'>
								<tr><td style='vertical-align:bottom;'><b>รายละเอียดสัญญา</b></td></tr>
							</table>
						</div>
						<div class='row' id='CONTDETAIL_A' style='height:40%;border:0.1px solid #bdbdbd;background-color:#eee;display:none;'>
							<div class='col-sm-12 col-xs-12' style='height:100%;'>
								<div id='dataTable-fixed-detail' class='dataTables_wrapper dt-bootstrap4 table-responsive' style='height:100%;width:100%;overflow:auto;'>
									<table id='dataTables-detail' class='table table-bordered dataTable table-hover' stat='' aria-describedby='dataTable_info' cellspacing='0' width='calc(100% - 1px)'>
										<thead>
											<tr role='row' style='height:30px;font-size:8pt;background-color:#666666;color:white;'>
												<th width='12.5%' style='vertical-align:middle;'>เลขตัวถัง</th>
												<th width='12.5%' style='vertical-align:middle;'>เลขเครื่อง</th>
												<th width='12.5%' style='vertical-align:middle;'>ยี่ห้อ</th>
												<th width='12.5%' style='vertical-align:middle;'>รุ่น</th>
												<th width='12.5%' style='vertical-align:middle;'>แบบ</th>
												<th width='12.5%' style='vertical-align:middle;'>สี</th>
												<th width='6.25%' style='vertical-align:middle;'>ขนาด</th>
												<th width='6.25%' style='vertical-align:middle;'>สถาพ</th>
												<th width='12.5%' style='vertical-align:middle;'>วันเดือนปีที่ขาย</th>
											</tr>
										</thead>
										<tbody style='white-space:nowrap;background-color:white;font-size:9pt;'></tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</fieldset>
			</div>
		";
		return $html;
	}
	
	private function getfromLeasingTab22(){
		$html = "
			<div class='tab-pane' name='tab22' style='height:calc(100vh - 220px);overflow:auto;'>
				<fieldset style='height:100%;'>
					<div style='float:left;height:100%;' class='col-sm-12 col-xs-12'>						
						<div class='row' style='height:92%;border:0.1px solid #bdbdbd;background-color:#eee;'>
							<div class='col-sm-12 col-xs-12' style='height:100%;'>
								<div id='dataTable-fixed-options' class='dataTables_wrapper dt-bootstrap4 table-responsive' style='height:100%;width:100%;overflow:auto;'>
									<table id='dataTables-options' class='table table-bordered dataTable table-hover' stat='' aria-describedby='dataTable_info' cellspacing='0' width='calc(100% - 1px)'>
										<thead>
											<tr role='row' style='height:30px;font-size:8pt;background-color:#666666;color:white;'>
												<th style='vertical-align:middle;'>เลขที่สัญญา</th>
												<th style='vertical-align:middle;'>สาขา</th>
												<th style='vertical-align:middle;'>ประเภทการขาย</th>
												<th style='vertical-align:middle;'>รหัสอุปกรณ์</th>
												<th style='vertical-align:middle;'>ชื่ออุปกรณ์</th>
												<th width='12.5%' style='vertical-align:middle;text-align:right;'>ราคาต่อหน่วย</th>
												<th width='12.5%' style='vertical-align:middle;text-align:right;'>จำนวน</th>
												<th width='12.5%' style='vertical-align:middle;text-align:right;'>ราคารวม</th>
											</tr>
										</thead>
										<tbody style='white-space:nowrap;background-color:white;font-size:9pt;'></tbody>
									</table>
								</div>
							</div>
						</div>
						<div class='row' style='border:0.1px solid #bdbdbd;background-color:#dedede;'>
							<div class='col-sm-12 col-xs-12' style='height:100%;'>	
								<div id='fix-sumoptions' style='width:100%;overflow:auto;'>
									<table id='sumoptions' width='calc(100% - 1px)'>
										<tr style='font-size:9pt;'>
											<th colspan='5' class='text-primary' style='text-align:center;'><b>รวม</b></th>
											<th width='12.5%' style='padding:2px;'><input type='text' id='2_SUMPRCICE' class='text-primary'  style='text-align:right;' readonly></th>
											<th width='12.5%' style='padding:2px;'><input type='text' id='2_SUMQTY' class='text-primary' style='text-align:right;' readonly></th>
											<th width='12.5%' style='padding:2px;'><input type='text' id='2_SUMTOTAL' class='text-primary' style='text-align:right;' readonly></th>
										</tr>
									</table>
								</div>
							</div>
						</div>
					</div>
				</fieldset>
			</div>
		";
		return $html;
	}
	
	private function getfromLeasingTab33(){
		$html = "
			<div class='tab-pane' name='tab33' style='height:calc(100vh - 220px);overflow:auto;'>
				<fieldset style='height:100%'>
					<div style='float:left;border:0.1px dotted #999;' class='col-sm-12 col-xs-12'>						
						<div class='row'>
							<div class='col-sm-12 col-xs-12'>
								<div class='col-sm-3 col-xs-3'>	
									<div class='form-group'>
										เลขที่สัญญา
										<input type='text' id='3_CONTNO' class='form-control input-sm' disabled>
									</div>
								</div>
								<div class='col-sm-3 col-xs-3'>	
									<div class='form-group'>
										ชื่อ-สกุล ลูกค้า
										<input type='text' id='3_CUSTOMER' class='form-control input-sm' disabled>
									</div>
								</div>
								<div class='col-sm-3 col-xs-3'>	
									<div class='form-group'>
										เลขตัวถัง
										<input type='text' id='3_STRNO' class='form-control input-sm' disabled>
									</div>
								</div>
								<div class='col-sm-3 col-xs-3'>	
									<div class='form-group'>
										วันที่ขาย
										<input type='text' id='3_SDATE' class='form-control input-sm' disabled>
									</div>
								</div>
								<div class='col-sm-3 col-xs-3'>	
									<div class='form-group'>
										ราคาขาย
										<input type='text' id='3_TOTPRC' class='form-control input-sm' disabled>
									</div>
								</div>
								<div class='col-sm-3 col-xs-3'>	
									<div class='form-group'>
										ชำระแล้ว
										<input type='text' id='3_SMPAY' class='form-control input-sm' disabled>
									</div>
								</div>
								<div class='col-sm-3 col-xs-3'>	
									<div class='form-group'>
										ลูกหนี้คงเหลือ
										<input type='text' id='3_ARBALAC' class='form-control input-sm' disabled>
									</div>
								</div>
								<div class='col-sm-3 col-xs-3'>	
									<div class='form-group'>
										เช็ครอเรียกเก็บ
										<input type='text' id='3_SMCHQ' class='form-control input-sm' disabled>
									</div>
								</div>
								<div class='col-sm-3 col-xs-3'>	
									<div class='form-group'>
										ลูกหนี้คงเหลือหักเช็ค
										<input type='text' id='3_TOTBALANC' class='form-control input-sm' disabled>
									</div>
								</div>
								<div class='col-sm-3 col-xs-3'>	
									<div class='form-group'>
										วันชำระล่าสุด
										<input type='text' id='3_DATELP' class='form-control input-sm' disabled>
									</div>
								</div>
								<div class='col-sm-3 col-xs-3'>	
									<div class='form-group'>
										จำนวนเงินชำระล่าสุด
										<input type='text' id='3_TOTLP' class='form-control input-sm' disabled>
									</div>
								</div>
								<div class='col-sm-1 col-xs-1'>	
									<div class='form-group'>
										จน งวดค้าง
										<input type='text' id='3_EXPPRD' class='form-control input-sm' disabled>
									</div>
								</div>
								<div class='col-sm-1 col-xs-1'>	
									<div class='form-group'>
										ค้างงวดที่
										<input type='text' id='3_EXPFRM' class='form-control input-sm' disabled>
									</div>
								</div>
								<div class='col-sm-1 col-xs-1'>	
									<div class='form-group'>
										ถึง
										<input type='text' id='3_EXPTO' class='form-control input-sm' disabled>
									</div>
								</div>
								<div class='col-sm-3 col-xs-3'>	
									<div class='form-group'>
										จำนวนเงินที่ค้าง
										<input type='text' id='3_EXPAMT' class='form-control input-sm' disabled>
									</div>
								</div>
								<div class='col-sm-3 col-xs-3'>	
									<div class='form-group'>
										ชำระล่าช้าได้
										<input type='text' id='3_DAYLATE' class='form-control input-sm' disabled>
									</div>
								</div>
								<div class='col-sm-3 col-xs-3'>	
									<div class='form-group'>
										คิดเบี้ยปรับแบบที่
										<input type='text' id='3_CANINT' class='form-control input-sm' disabled>
									</div>
								</div>
								<div class='col-sm-3 col-xs-3'>	
									<div class='form-group'>
										สอบถามเบี้ยปรับและตัดสด ณ วันที่
										<input type='text' id='3_DATESEARCH' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' value='".$this->today('today')."'>
									</div>
								</div>
							</div>
						</div>
					</div>	
					<div style='float:left;height:22%;padding-right:15px;' class='col-sm-6 col-xs-6'><br>						
						<div class='row' style='height:100%;background-color:#96ded1;'><center><font color='#004957'>รายการผู้ค้ำประกัน</font></center>
							<div class='col-sm-12 col-xs-12' style='height:100%;border:0.1px solid #bdbdbd;background-color:#eee;'>
								<div id='dataTable-fixed-armgar' class='dataTables_wrapper dt-bootstrap4 table-responsive' style='height:100%;width:100%;overflow:auto;'>
									<table id='dataTables-armgar' class='table table-bordered dataTable table-hover' stat='' aria-describedby='dataTable_info' width='calc(100% - 1px)'>
										<thead>
											<tr role='row' style='height:30px;font-size:8pt;background-color:#666666;color:white;'>
												<th style='vertical-align:middle;'>รหัสผู้ค้ำประกัน</th>
												<th style='vertical-align:middle;'>ชื่อ</th>
												<th style='vertical-align:middle;'>สกุล</th>
												<th style='vertical-align:middle;'>ความสัมพันธ์</th>
												<th style='vertical-align:middle;'>ผู้ค้ำคนที่</th>
											</tr>
										</thead>
										<tbody style='white-space:nowrap;background-color:white;'></tbody>
									</table>
								</div>
							</div>
						</div>
					</div>	
					<div style='float:left;height:22%;padding-left:15px;' class='col-sm-6 col-xs-6'><br>				
						<div class='row' style='height:100%;background-color:#96ded1;'><center><font color='#004957'>หลักทรัพย์ค้ำประกัน</font></center>
							<div class='col-sm-12 col-xs-12' style='height:100%;border:0.1px solid #bdbdbd;background-color:#eee;'>
								<div id='dataTable-fixed-optarmgar' class='dataTables_wrapper dt-bootstrap4 table-responsive' style='height:100%;width:100%;overflow:auto;'>
									<table id='dataTables-optarmgar' class='table table-bordered dataTable table-hover' stat='' aria-describedby='dataTable_info' width='calc(100% - 1px)'>
										<thead>
											<tr role='row' style='height:30px;font-size:8pt;background-color:#666666;color:white;'>
												<th style='vertical-align:middle;'>รหัสหลักทรัพย์</th>
												<th style='vertical-align:middle;'>รายการ</th>
												<th style='vertical-align:middle;'>หมายเลขประจำตัวหลักทรัพย์</th>
												<th style='vertical-align:middle;'>รายการที่</th>
											</tr>
										</thead>
										<tbody style='white-space:nowrap;background-color:white;'></tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
					<div class='col-sm-12 col-xs-12'>
						<div class=' col-sm-6 col-xs-6'>	
							<br><br><br><br>
							<button id='btnpenalty' class='btn btn-primary btn-outline btn-block' style='font-size:10pt'><span class='glyphicon glyphicon-calendar'><b> เบี้ยปรับ</b></span></button>
						</div>
						<div class=' col-sm-6 col-xs-6'>	
							<br><br><br><br>
							<button id='btndiscount' class='btn btn-primary btn-outline btn-block' style='font-size:10pt'><span class='glyphicon glyphicon-list-alt'><b> ส่วนลดตัดสด</b></span></button>
						</div>
					</div>
				</fieldset>
			</div>
		";
		return $html;
	}
	
	private function getfromLeasingTab44(){//style='border:2px dotted white;background-color:#eee;'
		$html = "
			<div class='tab-pane' name='tab44' style='height:calc(100vh - 220px);overflow:auto;'>
				<fieldset style='height:100%'>
					<div style='float:left;height:100%;' class='col-sm-12 col-xs-12'>	
						<!--div class='row'>เงื่อนไข</div--!>
						<div class='row'>
							<div class=' col-sm-10 col-xs-10 col-sm-offset-1'>
								<div class=' col-sm-4 col-xs-4 col-sm-offset-1'>	
									<div class='form-group text-primary'>
										<b>ลูกค้า</b>
										<select id='CUSCOD2' class='form-control input-sm' data-placeholder='ลูกค้า'></select>
									</div>
								</div>
								<div class=' col-sm-4 col-xs-4'>	
									<div class='form-group text-primary'>
										<b>เลขที่สัญญาลูกหนี้อื่น</b>
										<input type='text' id='4_ARCONT' class='form-control input-sm'>
									</div>
								</div>
								<div class=' col-sm-2 col-xs-2'>	
									<div class='form-group'>
										<br>
										<button id='btntsearcharothr' class='btn btn-primary btn-sm' style='width:100%'><span class='glyphicon glyphicon-search'><b> สอบถาม</b></span></button>
									</div>
								</div>
							</div>
						</div>
						<br>
						<div class='row' style='height:72%;border:0.1px solid #bdbdbd;background-color:#eee;'>
							<div class='col-sm-12 col-xs-12' style='height:100%;'>
								<div id='dataTable-fixed-arothers' class='dataTables_wrapper dt-bootstrap4 table-responsive' style='height:100%;width:100%;overflow:auto;'>
									<table id='dataTables-arothers' class='table table-bordered dataTable table-hover' stat='' aria-describedby='dataTable_info' cellspacing='0' width='calc(100% - 1px)'>
										<thead>
											<tr role='row' style='height:30px;font-size:8pt;background-color:#666666;color:white;'>
												<th style='vertical-align:middle;'>เลขที่สัญญาลูกหนี้อื่น</th>
												<th style='vertical-align:middle;'>เลขที่สัญญา</th>
												<th style='vertical-align:middle;'>สาขา</th>
												<th style='vertical-align:middle;'>ค้างชำระค่า</th>
												<th width='11%' style='text-align:right;vertical-align:middle;'>ยอดตั้งลูกหนี้</th>
												<th width='11%' style='text-align:right;vertical-align:middle;'>ชำระแล้ว</th>
												<th width='11%' style='text-align:right;vertical-align:middle;'>ลูกหนี้คงเหลือ</th>
												<th width='11%' style='text-align:right;vertical-align:middle;'>เช็ครอเรียกเก็บ</th>
												<th width='11%' style='text-align:right;vertical-align:middle;'>ยอดคงเหลือหักเช็ค</th>
											</tr>
										</thead>
										<tbody style='white-space:nowrap;background-color:white;'></tbody>
									</table>
								</div>
								
							</div>
						</div>
						<div class='row' style='border:0.1px solid #bdbdbd;background-color:#dedede;'>
							<div class='col-sm-12 col-xs-12' style='height:100%;'>	
								<div id='fix-sumarothers' style='width:100%;overflow:auto;'>
									<table id='sumarothers' width='calc(100% - 1px)'>
										<tr style='font-size:9pt;'>
											<th colspan='4' class='text-primary' style='text-align:center;'><b>รวม</b></th>
											<th width='11%' style='padding:2px;'><input type='text' id='4_SUMTOTPRC' 	class='text-primary' style='text-align:right;' readonly></th>
											<th width='11%' style='padding:2px;'><input type='text' id='4_SUMSMPAY' 	class='text-primary' style='text-align:right;' readonly></th>
											<th width='11%' style='padding:2px;'><input type='text' id='4_SUMBALANC' 	class='text-primary' style='text-align:right;' readonly></th>
											<th width='11%' style='padding:2px;'><input type='text' id='4_SUMSMCHQ' 	class='text-primary' style='text-align:right;' readonly></th>
											<th width='11%' style='padding:2px;'><input type='text' id='4_SUMTOTAL' 	class='text-primary' style='text-align:right;' readonly></th>
										</tr>
									</table>
								</div>
							</div>
						</div>
					</div>
				</fieldset>
			</div>
		";
		return $html;
	}
	
	function getfromPenalty(){
		//$level	= $_REQUEST["level"];

		$html = "
			<div class='b_Penalty' style='width:800px;height:480px;overflow:auto;background-color:white;'>
				<fieldset style='height:100%'>
					<div style='float:left;height:100%;' class='col-sm-12 col-xs-12'>	
						<div class='row'>
							<div class='col-sm-12 col-xs-12' style='background-color:#c8373c;border:5px solid white;height:65px;text-align:center;font-size:12pt;color:white;font-weight:bold;'>	
								<br>เบี้ยปรับล่าช้า<br>
							</div>
						</div>
						<div class='row' style='height:40%;border:5px solid white;'>
							<div class='col-sm-12 col-xs-12' style='height:100%;border:0.1px solid #bdbdbd;background-color:#eee;'>
								<div id='dataTable-fixed-penalty' class='dataTables_wrapper dt-bootstrap4 table-responsive' style='height:100%;width:100%;overflow:auto;'>
									<table id='dataTables-penalty' class='table table-bordered dataTable table-hover' stat='' aria-describedby='dataTable_info' cellspacing='0' width='calc(100% - 1px)'>
										<thead>
											<tr role='row' style='background-color:#666666;color:white;font-size:8pt;text-align:center;'>
												<th style='text-align:center;'>งวดที่</th>
												<th style='text-align:center;'>วันดิว</th>
												<th style='text-align:center;'>ค่างวด</th>
												<th style='text-align:center;'>วันชำระ</th>
												<th style='text-align:center;'>ชำระแล้ว</th>
												<th style='text-align:center;'>วันล่าช้า</th>
												<th style='text-align:center;'>ดอกเบี้ย</th>
												<th style='text-align:center;'>เกรด</th>
											</tr>
										</thead>
										<tbody style='white-space:nowrap;font-size:8pt;background-color:white;'></tbody>
									</table>
								</div>
							</div>
						</div>
						<div class='row' style='border:5px solid white;'>
							<div class='col-sm-12 col-xs-12' style='border:1px dotted #c2c2c2;'>
								<div class='col-sm-10 col-xs-10 col-sm-offset-1'>
									<div class='col-sm-6 col-xs-6'>	
										<div class='form-group'>
											เบี้ยปรับ
											<input type='text' id='P_PENALTY' class='form-control input-sm' disabled>
										</div>
									</div>
									<div class='col-sm-6 col-xs-6'>	
										<div class='form-group'>
											ชำระแล้ว
											<input type='text' id='P_SMPAY' class='form-control input-sm' disabled>
										</div>
									</div>
									<div class='col-sm-6 col-xs-6'>	
										<div class='form-group'>
											ส่วนลด
											<input type='text' id='P_DISCOUNT' class='form-control input-sm' disabled>
										</div>
									</div>
									<div class='col-sm-6 col-xs-6'>	
										<div class='form-group'>
											ยอดคงเหลือ
											<input type='text' id='P_BALANC' class='form-control input-sm' disabled>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class='row'>
							<div class=' col-sm-4 col-sm-offset-4'>	
								<div class='form-group'>
									<br>
									<button id='btnprint_penalty' class='btn btn-info btn-sm' style='width:100%;font-size:10pt'>
									<span><img id='table-ARlost-excel' src='../public/images/print-icon.png' style='width:20px;height:20px;'> <b>พิมพ์</b></span>
									</button>
								</div>
							</div>
						</div>
					</div>
				</fieldset>
			</div>
		";
	
		$response = array("html"=>$html);
		echo json_encode($response);
	}
	
	function getfromDiscount(){
		//$level	= $_REQUEST["level"];

		$html = "
			<div class='b_Discount' style='width:800px;height:480px;overflow:auto;background-color:white;'>
				<fieldset style='height:100%'>
					<div style='float:left;height:100%;' class='col-sm-12 col-xs-12'>	
						<div class='row'>
							<div class='col-sm-12 col-xs-12' style='background-color:#3d9690;border:5px solid white;height:65px;text-align:center;font-size:12pt;color:white;font-weight:bold;'>	
								<br>ส่วนลดตัดสด<br>
							</div>
						</div>
						<div class='row' style='border:5px solid white;'>
							<div class='col-sm-12 col-xs-12' style='border:1px dotted #c2c2c2;'>
								<div class='col-sm-10 col-xs-10 col-sm-offset-1'>
									<div class='col-sm-6 col-xs-6'>	
										<div class='form-group'>
											ค่างวดคงเหลือ
											<input type='text' id='D_PAYMENT' class='form-control input-sm' disabled>
										</div>
									</div>
									<div class='col-sm-6 col-xs-6'>	
										<div class='form-group'>
											ส่วนลดตัดสด
											<input type='text' id='D_DISCOUNT' class='form-control input-sm' disabled>
										</div>
									</div>
									<div class='col-sm-6 col-xs-6'>	
										<div class='form-group'>
											ต้องชำระค่างวด
											<input type='text' id='D_BALANC' class='form-control input-sm' disabled>
										</div>
									</div>
									<div class='col-sm-6 col-xs-6'>	
										<div class='form-group'>
											+ เบี้ยปรับค้างชำระ
											<input type='text' id='D_PENALTY' class='form-control input-sm' disabled>
										</div>
									</div>
									<div class='col-sm-6 col-xs-6'>	
										<div class='form-group'>
											ค่าดำเนินการ
											<input type='text' id='D_OPERATE' class='form-control input-sm' disabled>
										</div>
									</div>
									<div class='col-sm-6 col-xs-6'>	
										<div class='form-group'>
											รวมยอดตัดสด
											<input type='text' id='D_TOTAL' class='form-control input-sm' disabled>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class='row' style='border:5px solid white;'>
							<div class='col-sm-12 col-xs-12' style='border:1px dotted #c2c2c2;'>
								<div class='col-sm-10 col-xs-10 col-sm-offset-1'>
									<div class='col-sm-12 col-xs-12'>	
										<div class='form-group'>
											<center><font color='#009966'><b>ส่วนลดที่ให้ลูกค้าต้องไม่ต่ำกว่า 50% ของดอกผลคงเหลือ</b></font></center>
										</div>
									</div>
									<div class='col-sm-6 col-xs-6'>	
										<div class='form-group'>
											ดอกผลคงเหลือ
											<input type='text' id='D_NPROFIT' class='form-control input-sm' disabled>
										</div>
									</div>
									<div class='col-sm-6 col-xs-6'>	
										<div class='form-group'>
											50% ของกำไรคงเหลือ
											<input type='text' id='D_PPROFI' class='form-control input-sm' disabled>
										</div>
									</div>
								</div>
							</div>
						</div>
						<br>
						<div class='row' style='border:5px solid white;'>
							<div class='col-sm-12 col-xs-12'>
								<div class='col-sm-10 col-xs-10 col-sm-offset-1'>
									<div class='col-sm-6 col-xs-6'>	
										<div class='form-group'>
											<button id='btnprint_account' class='btn btn-info btn-sm' style='width:100%;font-size:10pt'>
											<span><img id='table-ARlost-excel' src='../public/images/print-icon.png' style='width:20px;height:20px;'> <b>บัญชี</b></span>
											</button>
										</div>
									</div>
									<div class='col-sm-6 col-xs-6'>	
										<div class='form-group'>
											<button id='btnprint_customer' class='btn btn-info btn-sm' style='width:100%;font-size:10pt'>
											<span><img id='table-ARlost-excel' src='../public/images/print-icon.png' style='width:20px;height:20px;'> <b>ลูกค้า</b></span>
											</button>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</fieldset>
			</div>
		";
	
		$response = array("html"=>$html);
		echo json_encode($response);
	}
	
	function search(){
		$CUSCOD1	= $_REQUEST["CUSCOD1"]; //'ฌHI-00005374';
		$CONTNO1	= $_REQUEST["CONTNO1"]; //'';
		$response = array();
		
		$sql = "
				IF OBJECT_ID('tempdb..#CUSDATA') IS NOT NULL DROP TABLE #CUSDATA
				select CONTNO, LOCAT, TOTPRC , SMPAY, BALANC, SMCHQ, TKANG, TYPE, MODEL, BAAB, CC, STAT, COLOR, REGNO, ENGNO, STRNO, TSALE, SDATE, CUSCOD,
				LPAYD, LPAYA, EXP_PRD, EXP_FRM, EXP_TO, EXP_AMT, DLDAY, CALINT
				into #CUSDATA
				from(
					select A.CONTNO, A.LOCAT, A.TOTPRC , A.SMPAY, (A.TOTPRC-A.SMPAY) AS BALANC, A.SMCHQ, (A.TOTPRC - (A.SMPAY+A.SMCHQ)) AS TKANG, 
					B.TYPE, B.MODEL, B.BAAB, B.CC, B.STAT, B.COLOR, B.REGNO, B.ENGNO, B.STRNO, A.TSALE, convert(nvarchar,A.SDATE,103) as SDATE, A.CUSCOD,
					convert(nvarchar,A.LPAYD,103) as LPAYD, LPAYA, EXP_PRD, EXP_FRM, EXP_TO, EXP_AMT, DLDAY, CALINT
					from {$this->MAuth->getdb('ARMAST')} A
					left join {$this->MAuth->getdb('INVTRAN')} B on A.STRNO = B.STRNO AND A.CONTNO = B.CONTNO AND A.TSALE = B.TSALE
					where A.TOTPRC > 0 and A.CUSCOD like '%".$CUSCOD1."%' and A.CONTNO like '%".$CONTNO1."%'
					union
					select A.CONTNO, A.LOCAT, A.TOTPRC ,A.SMPAY, (A.TOTPRC-A.SMPAY) AS BALANC, A.SMCHQ, (A.TOTPRC - (A.SMPAY+A.SMCHQ)) AS TKANG, 
					B.TYPE, B.MODEL, B.BAAB, B.CC, B.STAT, B.COLOR, B.REGNO, B.ENGNO, B.STRNO, A.TSALE, convert(nvarchar,A.SDATE,103) as SDATE, A.CUSCOD,
					convert(nvarchar,A.LPAYDT,103) as LPAYDT, SMPAY as LPAYA, 0 EXP_PRD, 0 EXP_FRM, 0 EXP_TO, 0 EXP_AMT, 0 DLDAY, '' CALINT
					from {$this->MAuth->getdb('ARCRED')} A
					left join {$this->MAuth->getdb('INVTRAN')} B on A.STRNO = B.STRNO AND A.CONTNO = B.CONTNO AND A.TSALE = B.TSALE
					where A.TOTPRC > 0 and A.CUSCOD like '%".$CUSCOD1."%' and A.CONTNO like '%".$CONTNO1."%'
					union
					select A.CONTNO, A.LOCAT, A.TOTPRC ,A.SMPAY, (A.TOTPRC-A.SMPAY) AS BALANC, A.SMCHQ, (A.TOTPRC - (A.SMPAY+A.SMCHQ)) AS TKANG, 
					B.TYPE, B.MODEL, B.BAAB, B.CC, B.STAT, B.COLOR, B.REGNO, B.ENGNO, B.STRNO, A.TSALE, convert(nvarchar,A.SDATE,103) as SDATE, A.CUSCOD,
					convert(nvarchar,A.LPAYD,103) as LPAYD, LPAYA, 0 EXP_PRD,  0 EXP_FRM, 0 EXP_TO, 0 EXP_AMT, 0 DLDAY, '' CALINT
					from {$this->MAuth->getdb('ARFINC')} A
					left join {$this->MAuth->getdb('INVTRAN')} B on A.STRNO = B.STRNO AND A.CONTNO = B.CONTNO AND A.TSALE = B.TSALE
					where A.TOTPRC > 0 and A.CUSCOD like '%".$CUSCOD1."%' and A.CONTNO like '%".$CONTNO1."%'
					union
					select A.CONTNO, A.LOCAT, A.OPTPTOT AS TOTPRC, A.SMPAY, (A.OPTPTOT -A.SMPAY) AS BALANC, A.SMCHQ, (A.OPTPTOT  -(A.SMPAY+A.SMCHQ)) AS TKANG,
					'' AS TYPE, '' AS MODEL, '' AS BAAB, 0 AS CC, '' AS STAT, '' AS COLOR, '' AS REGNO, '' AS ENGNO, '' AS STRNO, A.TSALE,
					convert(nvarchar,A.SDATE,103) as SDATE, A.CUSCOD, convert(nvarchar,A.LPAYDT,103) as LPAYDT, 0 LPAYA, 0 EXP_PRD, 0 EXP_FRM, 0 EXP_TO, 0 EXP_AMT,
					0 DLDAY, '' CALINT 
					from {$this->MAuth->getdb('AROPTMST')} A  
					where A.OPTPTOT > 0 and A.CUSCOD like '%".$CUSCOD1."%' and A.CONTNO like '%".$CONTNO1."%'
					union
					select A.CONTNO, A.LOCAT, A.TOTPRC, A.SMPAY, (A.TOTPRC-A.SMPAY) AS BALANC, A.SMCHQ, (A.TOTPRC -(A.SMPAY+A.SMCHQ)) AS TKANG,'' AS TYPE,'' AS MODEL,
					'' AS BAAB, 0 AS CC, '' AS STAT, '' AS COLOR, '' AS REGNO, '' AS ENGNO, '' AS STRNO, A.TSALE, convert(nvarchar,A.SDATE,103) as SDATE, A.CUSCOD,
					convert(nvarchar,A.LPAYDT,103) as LPAYDT, SMPAY as LPAYA, 0 EXP_PRD, 0 EXP_FRM, 0 EXP_TO, 0 EXP_AMT, 0 DLDAY, '' CALINT 
					from  {$this->MAuth->getdb('AR_INVOI')} A  
					WHERE  A.TOTPRC > 0 and A.CUSCOD like '%".$CUSCOD1."%' and A.CONTNO like '%".$CONTNO1."%'
				)CUSDATA
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$sql = "
				select * from #CUSDATA order by CUSCOD ,CONTNO, TSALE
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);

		$custdata = "";
		$i = 0;
		if($query->row()){
			foreach($query->result() as $row){$i++;
				//print_r($row->DESC1);
				$custdata .= "
					<tr class='trow' seq='old'>
						<td class='getit' style='cursor:pointer;color:blue;'
						CONTNO 		= '".$row->CONTNO."' 
						CUSCOD 		= '".$row->CUSCOD."' 
						TSALE 		= '".$row->TSALE."'
						>".$row->CONTNO."</td>
						<td>".$row->LOCAT."</td>
						<td>".$row->TSALE."</td>
						<td align='right'>".number_format($row->TOTPRC,2)."</td>
						<td align='right'>".number_format($row->SMPAY,2)."</td>
						<td align='right'>".number_format($row->BALANC,2)."</td>
						<td align='right'>".number_format($row->SMCHQ,2)."</td>
						<td align='right'>".number_format($row->TKANG,2)."</td>
					</tr>
				";	
			}
		}else{
			$custdata = "";
		}
	
		if($i>0){
			$response["numrow"] = $i;
			
			$stylesheet = "
				<style>
					tr.highlighted td{background:#cce8ff;}
				</style>
			";
			$custdata = $custdata.$stylesheet;
			$response["custdata"] = $custdata;
			
			$cond = "
				select top 1 CONTNO, CUSCOD from #CUSDATA order by CUSCOD ,CONTNO, TSALE
			";
			$querycond = $this->db->query($cond);
			$rows = $querycond->row();
			$contno = $rows->CONTNO;
			$cuscod = $rows->CUSCOD;
		
			$sql2 = "
					select sum(TOTPRC) as sumTOTPRC, sum(SMPAY) as sumSMPAY, sum(BALANC) as sumBALANC, sum(SMCHQ) as sumSMCHQ, 
					sum(TKANG) as sumTKANG from #CUSDATA
			";
			//echo $sql2; exit;
			$query2 = $this->db->query($sql2);
			
			if($query2->row()){
				foreach($query2->result() as $row){
					$response["sumTOTPRC_1"] 	= number_format($row->sumTOTPRC,2);
					$response["sumSMPAY_1"] 	= number_format($row->sumSMPAY,2);
					$response["sumBALANC_1"] 	= number_format($row->sumBALANC,2);
					$response["sumSMCHQ_1"] 	= number_format($row->sumSMCHQ,2);
					$response["sumTKANG_1"] 	= number_format($row->sumTKANG,2);
				}
			}
			
			$sql3 = "
					select CONTNO, LOCAT, TYPE, MODEL, BAAB, CC, case when STAT = 'N' then 'ใหม่' else 'เก่า' end as STATS,  COLOR, REGNO, ENGNO, 
					TOTPRC , SMPAY, BALANC, SMCHQ, TKANG, STRNO, TSALE, SDATE, a.CUSCOD, LPAYD, LPAYA, EXP_PRD, EXP_FRM, EXP_TO, EXP_AMT, DLDAY, 
					CALINT, b.SNAM+b.NAME1+' '+b.NAME2 as CUSNAME
					from #CUSDATA a
					left join {$this->MAuth->getdb('CUSTMAST')} b on a.CUSCOD = b.CUSCOD
					where CONTNO = '".$contno."' and a.CUSCOD = '".$cuscod."'
					order by CUSCOD ,CONTNO, TSALE
			";
			//echo $sql2; exit;
			$query3 = $this->db->query($sql3);
			
			if($query3->row()){
				foreach($query3->result() as $row){
					$response["TYPE"] 	= str_replace(chr(0),'',$row->TYPE);
					$response["MODEL"] 	= str_replace(chr(0),'',$row->MODEL);
					$response["BAAB"] 	= str_replace(chr(0),'',$row->BAAB);
					$response["CC"] 	= number_format($row->CC);
					$response["STAT"] 	= $row->STATS;
					$response["COLOR"] 	= str_replace(chr(0),'',$row->COLOR);
					$response["REGNO"] 	= str_replace(chr(0),'',$row->REGNO);
					$response["ENGNO"] 	= str_replace(chr(0),'',$row->ENGNO);
					$response["STRNO"] 	= str_replace(chr(0),'',$row->STRNO);
					$response["TSALE"] 	= str_replace(chr(0),'',$row->TSALE);
					$response["SDATE"] 	= $row->SDATE;
					$response["CONTNO"] = $row->CONTNO;
					$response["CUSCOD"] = $row->CUSCOD;
					$response["CUSNAME"]= $row->CUSNAME;
					$response["LPAYD"] 	= $row->LPAYD;
					$response["TOTPRC"] = number_format($row->TOTPRC,2);
					$response["SMPAY"]	= number_format($row->SMPAY,2);
					$response["BALANC"]	= number_format($row->BALANC,2);
					$response["SMCHQ"] 	= number_format($row->SMCHQ,2);
					$response["TKANG"]	= number_format($row->TKANG,2);
					$response["LPAYA"] 	= number_format($row->LPAYA,2);
					$response["EXP_PRD"]= number_format($row->EXP_PRD);
					$response["EXP_FRM"]= number_format($row->EXP_FRM);
					$response["EXP_TO"] = number_format($row->EXP_TO);
					$response["EXP_AMT"]= number_format($row->EXP_AMT,2);
					$response["DLDAY"] 	= number_format($row->DLDAY);
					$response["CALINT"] = $row->CALINT;

				}
			}
			
			$sql4 = "
					select A.CONTNO, A.LOCAT, A.TSALE, A.OPTCODE, B.OPTNAME, A.UPRICE, A.QTY, A.TOTPRC 
					from {$this->MAuth->getdb('ARINOPT')} A
					left join {$this->MAuth->getdb('OPTMAST')} B on A.OPTCODE = B.OPTCODE AND A.LOCAT = B.LOCAT
					where A.CONTNO = '".$contno."'
			";
			//echo $sql4; exit;
			$query4 = $this->db->query($sql4);
			
			$optmast = "";
			if($query4->row()){
				foreach($query4->result() as $row){
					//print_r($row->DESC1);
					$optmast .= "
						<tr class='trow' seq='old'>
							<td>".$row->CONTNO."</td>
							<td>".$row->LOCAT."</td>
							<td>".$row->TSALE."</td>
							<td>".$row->OPTCODE."</td>
							<td>".$row->OPTNAME."</td>
							<td align='right'>".number_format($row->UPRICE,2)."</td>
							<td align='right'>".number_format($row->QTY,2)."</td>
							<td align='right'>".number_format($row->TOTPRC,2)."</td>
						</tr>
					";	
				}
			}else{
				$optmast .= "<tr class='trow'><td colspan='8'>ไม่มี</td></tr>";	
			}
			$response["optmast"] = $optmast;
			
			$sql5 = "
					select sum(UPRICE) as sumUPRICE, sum(QTY) as sumQTY, sum(TOTPRC) as sumTOTPRC 
					from {$this->MAuth->getdb('ARINOPT')} 
					where CONTNO = '".$contno."'
			";
			//echo $sql5; exit;
			$query5 = $this->db->query($sql5);
			
			if($query5->row()){
				foreach($query5->result() as $row){
					$response["sumUPRICE_2"] 	= number_format($row->sumUPRICE,2);
					$response["sumQTY_2"] 		= number_format($row->sumQTY,2);
					$response["sumTOTPRC_2"] 	= number_format($row->sumTOTPRC,2);
				}
			}
			
			$sql6 = "
					select a.CUSCOD, b.NAME1, b.NAME2, a.RELATN, a.GARNO 
					from {$this->MAuth->getdb('ARMGAR')} a
					left join {$this->MAuth->getdb('CUSTMAST')} b  on a.CUSCOD = b.CUSCOD
					where CONTNO = '".$contno."'
			";
			//echo $sql; exit;
			$query6 = $this->db->query($sql6);
			
			$armgars = "";
			if($query6->row()){
				foreach($query6->result() as $row){
					//print_r($row->DESC1);
					$armgars .= "
						<tr class='trow' seq='old'>
							<td>".$row->CUSCOD."</td>
							<td>".$row->NAME1."</td>
							<td>".$row->NAME2."</td>
							<td>".$row->RELATN."</td>
							<td>".$row->GARNO."</td>
						</tr>
					";	
				}
			}else{
				$armgars .= "<tr class='trow'><td colspan='5'>ไม่มี</td></tr>";
			}
			$response["armgars"] = $armgars;
			
			$sql7 = "
					select A.CONTNO,A.GARNO,A.GARCODE,A.REFFNO ,B.GARDESC 
					from {$this->MAuth->getdb('AROTHGAR')} A
					left join {$this->MAuth->getdb('SETARGAR')} B on A.GARCODE = B.GARCODE
					where A.CONTNO = '".$contno."'
			";
			//echo $sql; exit;
			$query7 = $this->db->query($sql7);
			
			$optarmgar = "";
			if($query7->row()){
				foreach($query7->result() as $row){
					//print_r($row->DESC1);
					$optarmgar .= "
						<tr class='trow'>
							<td>".$row->GARCODE."</td>
							<td>".$row->GARDESC."</td>
							<td>".$row->REFFNO."</td>
							<td>".$row->GARNO."</td>
						</tr>
					";	
				}
			}else{
				$optarmgar .= "<tr class='trow'><td colspan='4'>ไม่มี</td></tr>";
			}
			$response["optarmgar"] = $optarmgar;
			
			$sql8 = "
					select ARCONT, CONTNO, LOCAT, FORDESC, PAYAMT, SMPAY, PAYAMT-SMPAY as BALANCE, SMCHQ, PAYAMT-SMPAY-SMCHQ as TOTAL
					from {$this->MAuth->getdb('AROTHR')} a
					left join {$this->MAuth->getdb('PAYFOR')} b on a.PAYFOR = b.FORCODE
					where a.CUSCOD = '".$cuscod."'
			";
			//echo $sql8; exit;
			$query8 = $this->db->query($sql8);
			
			$arothers = "";
			if($query8->row()){
				foreach($query8->result() as $row){
					//print_r($row->DESC1);
					$arothers .= "
						<tr class='trow'>
							<td>".$row->ARCONT."</td>
							<td>".$row->CONTNO."</td>
							<td>".$row->LOCAT."</td>
							<td>".$row->FORDESC."</td>
							<td align='right'>".number_format($row->PAYAMT,2)."</td>
							<td align='right'>".number_format($row->SMPAY,2)."</td>
							<td align='right'>".number_format($row->BALANCE,2)."</td>
							<td align='right'>".number_format($row->SMCHQ,2)."</td>
							<td align='right'>".number_format($row->TOTAL,2)."</td>
						</tr>
					";	
				}
			}else{
				$arothers .= "<tr class='trow'><td colspan='9'>ไม่มี</td></tr>";
			}
			$response["arothers"] = $arothers;
			
			$sql9 = "
					select isnull(sum(PAYAMT),0) as sumPAYAMT, isnull(sum(SMPAY),0) as sumSMPAY, isnull(sum(PAYAMT-SMPAY),0) as sumBALANCE, isnull(sum(SMCHQ),0) as sumSMCHQ, isnull(sum(PAYAMT-SMPAY-SMCHQ),0) as sumTOTAL
					from {$this->MAuth->getdb('AROTHR')} 
					where CUSCOD = '".$cuscod."'
			";
			//echo $sql5; exit;
			$query9 = $this->db->query($sql9);
			
			if($query9->row()){
				foreach($query9->result() as $row){
					$response["sumPAYAMT_4"] 	= number_format($row->sumPAYAMT,2);
					$response["sumSMPAY_4"] 	= number_format($row->sumSMPAY,2);
					$response["sumBALANCE_4"] 	= number_format($row->sumBALANCE,2);
					$response["sumSMCHQ_4"] 	= number_format($row->sumSMCHQ,2);
					$response["sumTOTAL_4"] 	= number_format($row->sumTOTAL,2);
				}
			}
			
			$sql10 = "
					select CONTNO, LOCAT, convert(nvarchar,STARTDT,112) as STARTDT, convert(nvarchar,ENDDT,112) as ENDDT, MEMO1, USERID
					from {$this->MAuth->getdb('ALERTMSG')} 
					where CONTNO = '".$contno."' and GETDATE() between STARTDT and ENDDT
			";
			//echo $sql5; exit;
			$query10 = $this->db->query($sql10);
			
			if($query10->row()){
				foreach($query10->result() as $row){
					$response["MSGLOCAT"] 	= $row->LOCAT;
					$response["STARTDT"] 	= $row->STARTDT;
					$response["ENDDT"] 		= $row->ENDDT;
					$response["MSGMEMO"] 	= $row->MEMO1;
					$response["USERID"] 	= $row->USERID;
				}
			}else{
				$response["MSGMEMO"] 	= "none";
			}
			
			$sql11 = "
					select STRNO, ENGNO, TYPE, MODEL, BAAB, COLOR, CC, STAT, convert(nvarchar,SDATE,112) as SDATE
					from {$this->MAuth->getdb('INVTRAN')} 
					where CONTNO = '".$contno."' and TSALE = 'A'
					order by SDATE, MODEL, STRNO
			";
			//echo $sql5; exit;
			$query11 = $this->db->query($sql11);
			$detail = "";
			if($query11->row()){
				foreach($query11->result() as $row){
					//print_r($row->DESC1);
					$detail .= "
						<tr class='trow'>
							<td>".$row->STRNO."</td>
							<td>".$row->ENGNO."</td>
							<td>".$row->TYPE."</td>
							<td>".$row->MODEL."</td>
							<td>".$row->BAAB."</td>
							<td>".$row->COLOR."</td>
							<td>".number_format($row->CC)."</td>
							<td>".$row->STAT."</td>
							<td>".$this->Convertdate(2,$row->SDATE)."</td>
							
						</tr>
					";	
				}
			}else{
				$detail .= "<tr class='trow'><td colspan='9'>ไม่มี</td></tr>";
			}
			$response["detail"] = $detail;
			
		}else{
			$custdata = "
				<tr class='trow'><td colspan='8' style='color:red;'>ไม่พบข้อมูล</td></tr>
			";	
			$response["custdata"] = $custdata;
		}

		echo json_encode($response);
	}
	
	function changedata(){
		$CONTNOS	= $_REQUEST["CONTNOS"]; 
		$CUSCODS	= $_REQUEST["CUSCODS"];
		$TSALES		= $_REQUEST["TSALES"];
		$response = array();
		
		$sql3 = "
				select CONTNO, LOCAT, TYPE, MODEL, BAAB, CC, case when STAT = 'N' then 'ใหม่' else 'เก่า' end as STATS,  COLOR, REGNO, ENGNO, 
				TOTPRC , SMPAY, BALANC, SMCHQ, TKANG, STRNO, TSALE, SDATE, a.CUSCOD, LPAYD, LPAYA, EXP_PRD, EXP_FRM, EXP_TO, EXP_AMT, DLDAY, 
				CALINT, b.SNAM+b.NAME1+' '+b.NAME2 as CUSNAME
				from(
					select A.CONTNO, A.LOCAT, A.TOTPRC , A.SMPAY, (A.TOTPRC-A.SMPAY) AS BALANC, A.SMCHQ, (A.TOTPRC - (A.SMPAY+A.SMCHQ)) AS TKANG, 
					B.TYPE, B.MODEL, B.BAAB, B.CC, B.STAT, B.COLOR, B.REGNO, B.ENGNO, B.STRNO, A.TSALE, convert(nvarchar,A.SDATE,103) as SDATE, A.CUSCOD,
					convert(nvarchar,A.LPAYD,103) as LPAYD, LPAYA, EXP_PRD, EXP_FRM, EXP_TO, EXP_AMT, DLDAY, CALINT
					from {$this->MAuth->getdb('ARMAST')} A
					left join {$this->MAuth->getdb('INVTRAN')} B on A.STRNO = B.STRNO AND A.CONTNO = B.CONTNO AND A.TSALE = B.TSALE
					where A.TOTPRC > 0 and A.CUSCOD like '%".$CUSCODS."%' and A.CONTNO like '%".$CONTNOS."%'
					union
					select A.CONTNO, A.LOCAT, A.TOTPRC ,A.SMPAY, (A.TOTPRC-A.SMPAY) AS BALANC, A.SMCHQ, (A.TOTPRC - (A.SMPAY+A.SMCHQ)) AS TKANG, 
					B.TYPE, B.MODEL, B.BAAB, B.CC, B.STAT, B.COLOR, B.REGNO, B.ENGNO, B.STRNO, A.TSALE, convert(nvarchar,A.SDATE,103) as SDATE, A.CUSCOD,
					convert(nvarchar,A.LPAYDT,103) as LPAYDT, SMPAY as LPAYA, 0 EXP_PRD, 0 EXP_FRM, 0 EXP_TO, 0 EXP_AMT, 0 DLDAY, '' CALINT
					from {$this->MAuth->getdb('ARCRED')} A
					left join {$this->MAuth->getdb('INVTRAN')} B on A.STRNO = B.STRNO AND A.CONTNO = B.CONTNO AND A.TSALE = B.TSALE
					where A.TOTPRC > 0 and A.CUSCOD like '%".$CUSCODS."%' and A.CONTNO like '%".$CONTNOS."%'
					union
					select A.CONTNO, A.LOCAT, A.TOTPRC ,A.SMPAY, (A.TOTPRC-A.SMPAY) AS BALANC, A.SMCHQ, (A.TOTPRC - (A.SMPAY+A.SMCHQ)) AS TKANG, 
					B.TYPE, B.MODEL, B.BAAB, B.CC, B.STAT, B.COLOR, B.REGNO, B.ENGNO, B.STRNO, A.TSALE, convert(nvarchar,A.SDATE,103) as SDATE, A.CUSCOD,
					convert(nvarchar,A.LPAYD,103) as LPAYD, LPAYA, 0 EXP_PRD,  0 EXP_FRM, 0 EXP_TO, 0 EXP_AMT, 0 DLDAY, '' CALINT
					from {$this->MAuth->getdb('ARFINC')} A
					left join {$this->MAuth->getdb('INVTRAN')} B on A.STRNO = B.STRNO AND A.CONTNO = B.CONTNO AND A.TSALE = B.TSALE
					where A.TOTPRC > 0 and A.CUSCOD like '%".$CUSCODS."%' and A.CONTNO like '%".$CONTNOS."%'
					union
					select A.CONTNO, A.LOCAT, A.OPTPTOT AS TOTPRC, A.SMPAY, (A.OPTPTOT -A.SMPAY) AS BALANC, A.SMCHQ, (A.OPTPTOT  -(A.SMPAY+A.SMCHQ)) AS TKANG,
					'' AS TYPE, '' AS MODEL, '' AS BAAB, 0 AS CC, '' AS STAT, '' AS COLOR, '' AS REGNO, '' AS ENGNO, '' AS STRNO, A.TSALE,
					convert(nvarchar,A.SDATE,103) as SDATE, A.CUSCOD, convert(nvarchar,A.LPAYDT,103) as LPAYDT, 0 LPAYA, 0 EXP_PRD, 0 EXP_FRM, 0 EXP_TO, 0 EXP_AMT,
					0 DLDAY, '' CALINT 
					from {$this->MAuth->getdb('AROPTMST')} A  
					where A.OPTPTOT > 0 and A.CUSCOD like '%".$CUSCODS."%' and A.CONTNO like '%".$CONTNOS."%'
					union
					select A.CONTNO, A.LOCAT, A.TOTPRC, A.SMPAY, (A.TOTPRC-A.SMPAY) AS BALANC, A.SMCHQ, (A.TOTPRC -(A.SMPAY+A.SMCHQ)) AS TKANG,'' AS TYPE,'' AS MODEL,
					'' AS BAAB, 0 AS CC, '' AS STAT, '' AS COLOR, '' AS REGNO, '' AS ENGNO, '' AS STRNO, A.TSALE, convert(nvarchar,A.SDATE,103) as SDATE, A.CUSCOD,
					convert(nvarchar,A.LPAYDT,103) as LPAYDT, SMPAY as LPAYA, 0 EXP_PRD, 0 EXP_FRM, 0 EXP_TO, 0 EXP_AMT, 0 DLDAY, '' CALINT 
					from  {$this->MAuth->getdb('AR_INVOI')} A  
					WHERE  A.TOTPRC > 0 and A.CUSCOD like '%".$CUSCODS."%' and A.CONTNO like '%".$CONTNOS."%'
				)a
				left join CUSTMAST b on a.CUSCOD = b.CUSCOD
				order by CUSCOD ,CONTNO, TSALE
		";
		//echo $sql3; exit;
		$query3 = $this->db->query($sql3);
		
		if($query3->row()){
			foreach($query3->result() as $row){
				$response["TYPE"] 	= str_replace(chr(0),'',$row->TYPE);
				$response["MODEL"] 	= str_replace(chr(0),'',$row->MODEL);
				$response["BAAB"] 	= str_replace(chr(0),'',$row->BAAB);
				$response["CC"] 	= number_format($row->CC);
				$response["STAT"] 	= $row->STATS;
				$response["COLOR"] 	= str_replace(chr(0),'',$row->COLOR);
				$response["REGNO"] 	= str_replace(chr(0),'',$row->REGNO);
				$response["ENGNO"] 	= str_replace(chr(0),'',$row->ENGNO);
				$response["STRNO"] 	= str_replace(chr(0),'',$row->STRNO);
				$response["TSALE"] 	= str_replace(chr(0),'',$row->TSALE);
				$response["SDATE"] 	= $row->SDATE;
				$response["CONTNO"] = $row->CONTNO;
				$response["CUSCOD"] = $row->CUSCOD;
				$response["CUSNAME"]= $row->CUSNAME;
				$response["LPAYD"] 	= $row->LPAYD;
				$response["TOTPRC"] = number_format($row->TOTPRC,2);
				$response["SMPAY"]	= number_format($row->SMPAY,2);
				$response["BALANC"]	= number_format($row->BALANC,2);
				$response["SMCHQ"] 	= number_format($row->SMCHQ,2);
				$response["TKANG"]	= number_format($row->TKANG,2);
				$response["LPAYA"] 	= number_format($row->LPAYA,2);
				$response["EXP_PRD"]= number_format($row->EXP_PRD);
				$response["EXP_FRM"]= number_format($row->EXP_FRM);
				$response["EXP_TO"] = number_format($row->EXP_TO);
				$response["EXP_AMT"]= number_format($row->EXP_AMT,2);
				$response["DLDAY"] 	= number_format($row->DLDAY);
				$response["CALINT"] = $row->CALINT;

			}
		}
		
		$sql4 = "
				select A.CONTNO, A.LOCAT, A.TSALE, A.OPTCODE, B.OPTNAME, A.UPRICE, A.QTY, A.TOTPRC 
				from {$this->MAuth->getdb('ARINOPT')} A
				left join {$this->MAuth->getdb('OPTMAST')} B on A.OPTCODE = B.OPTCODE AND A.LOCAT = B.LOCAT
				where A.CONTNO = '".$CONTNOS."'
		";
		//echo $sql4; exit;
		$query4 = $this->db->query($sql4);
		
		$optmast = "";
		if($query4->row()){
			foreach($query4->result() as $row){
				//print_r($row->DESC1);
				$optmast .= "
					<tr class='trow' seq='old'>
						<td>".$row->CONTNO."</td>
						<td>".$row->LOCAT."</td>
						<td>".$row->TSALE."</td>
						<td>".$row->OPTCODE."</td>
						<td>".$row->OPTNAME."</td>
						<td align='right'>".number_format($row->UPRICE,2)."</td>
						<td align='right'>".number_format($row->QTY,2)."</td>
						<td align='right'>".number_format($row->TOTPRC,2)."</td>
					</tr>
				";	
			}
		}else{
			$optmast .= "<tr class='trow'><td colspan='8'>ไม่มี</td></tr>";	
		}
		$response["optmast"] = $optmast;
		
		$sql5 = "
				select isnull(sum(UPRICE),0) as sumUPRICE, isnull(sum(QTY),0) as sumQTY, isnull(sum(TOTPRC),0) as sumTOTPRC 
				from {$this->MAuth->getdb('ARINOPT')} 
				where CONTNO = '".$CONTNOS."'
		";
		//echo $sql5; exit;
		$query5 = $this->db->query($sql5);
		
		if($query5->row()){
			foreach($query5->result() as $row){
				$response["sumUPRICE_2"] 	= number_format($row->sumUPRICE,2);
				$response["sumQTY_2"] 		= number_format($row->sumQTY,2);
				$response["sumTOTPRC_2"] 	= number_format($row->sumTOTPRC,2);
			}
		}
		
		$sql6 = "
				select a.CUSCOD, b.NAME1, b.NAME2, a.RELATN, a.GARNO 
				from {$this->MAuth->getdb('ARMGAR')} a
				left join CUSTMAST b  on a.CUSCOD = b.CUSCOD
				where CONTNO = '".$CONTNOS."'
		";
		//echo $sql6; exit;
		$query6 = $this->db->query($sql6);
		
		$armgars = "";
		if($query6->row()){
			foreach($query6->result() as $row){
				//print_r($row->DESC1);
				$armgars .= "
					<tr class='trow' seq='old'>
						<td>".$row->CUSCOD."</td>
						<td>".$row->NAME1."</td>
						<td>".$row->NAME2."</td>
						<td>".$row->RELATN."</td>
						<td>".$row->GARNO."</td>
					</tr>
				";	
			}
		}else{
			$armgars .= "<tr class='trow'><td colspan='5'>ไม่มี</td></tr>";
		}
		$response["armgars"] = $armgars;
		
		$sql7 = "
				select A.CONTNO,A.GARNO,A.GARCODE,A.REFFNO ,B.GARDESC 
				from {$this->MAuth->getdb('AROTHGAR')} A
				left join {$this->MAuth->getdb('SETARGAR')} B on A.GARCODE = B.GARCODE
				where A.CONTNO = '".$CONTNOS."'
		";
		//echo $sql; exit;
		$query7 = $this->db->query($sql7);
		
		$optarmgar = "";
		if($query7->row()){
			foreach($query7->result() as $row){
				//print_r($row->DESC1);
				$optarmgar .= "
					<tr class='trow'>
						<td>".$row->GARCODE."</td>
						<td>".$row->GARDESC."</td>
						<td>".$row->REFFNO."</td>
						<td>".$row->GARNO."</td>
					</tr>
				";	
			}
		}else{
			$optarmgar .= "<tr class='trow'><td colspan='4'>ไม่มี</td></tr>";
		}
		$response["optarmgar"] = $optarmgar;
		
		$sql8 = "
				select CONTNO, LOCAT, convert(nvarchar,STARTDT,112) as STARTDT, convert(nvarchar,ENDDT,112) as ENDDT, MEMO1, USERID
				from {$this->MAuth->getdb('ALERTMSG')} 
				where CONTNO = '".$CONTNOS."' and GETDATE() between STARTDT and ENDDT
		";
		//echo $sql5; exit;
		$query8 = $this->db->query($sql8);
		
		if($query8->row()){
			foreach($query8->result() as $row){
				$response["MSGLOCAT"] 	= $row->LOCAT;
				$response["STARTDT"] 	= $row->STARTDT;
				$response["ENDDT"] 		= $row->ENDDT;
				$response["MSGMEMO"] 	= $row->MEMO1;
				$response["USERID"] 	= $row->USERID;
			}
		}else{
			$response["MSGMEMO"] 	= "none";
		}
		
		$sql9 = "
				select STRNO, ENGNO, TYPE, MODEL, BAAB, COLOR, CC, STAT, convert(nvarchar,SDATE,112) as SDATE
				from {$this->MAuth->getdb('INVTRAN')} 
				where CONTNO = '".$CONTNOS."' and TSALE = 'A'
				order by SDATE, MODEL, STRNO
		";
		//echo $sql5; exit;
		$query9 = $this->db->query($sql9);
		$detail = "";
		if($query9->row()){
			foreach($query9->result() as $row){
				//print_r($row->DESC1);
				$detail .= "
					<tr class='trow'>
						<td>".$row->STRNO."</td>
						<td>".$row->ENGNO."</td>
						<td>".$row->TYPE."</td>
						<td>".$row->MODEL."</td>
						<td>".$row->BAAB."</td>
						<td>".$row->COLOR."</td>
						<td>".number_format($row->CC)."</td>
						<td>".$row->STAT."</td>
						<td>".$this->Convertdate(2,$row->SDATE)."</td>
						
					</tr>
				";	
			}
		}else{
			$detail .= "<tr class='trow'><td colspan='9'>ไม่มี</td></tr>";
		}
		$response["detail"] = $detail;

		echo json_encode($response);
	}
	
	function searcharothr(){
		$CUSCOD2	= $_REQUEST["CUSCOD2"];
		$AROTHR		= $_REQUEST["AROTHR"];
		$cond		= "";
		
		if($CUSCOD2 != ""){
			$cond .= " and a.CUSCOD = '".$CUSCOD2."'";
		}
		
		if($AROTHR != ""){
			$cond .= " and a.ARCONT like '%".$AROTHR."%' collate thai_cs_as";
		}
		
		$sql = "
				select ARCONT, CONTNO, LOCAT, FORDESC, PAYAMT, SMPAY, PAYAMT-SMPAY as BALANCE, SMCHQ, PAYAMT-SMPAY-SMCHQ as TOTAL
				from {$this->MAuth->getdb('AROTHR')} a
				left join {$this->MAuth->getdb('PAYFOR')} b on a.PAYFOR = b.FORCODE
				where 1=1 ".$cond."
		";
		//echo $sql8; exit;
		$query = $this->db->query($sql);
		
		$arothers = "";
		if($query->row()){
			foreach($query->result() as $row){
				//print_r($row->DESC1);
				$arothers .= "
					<tr class='trow'>
						<td>".$row->ARCONT."</td>
						<td>".$row->CONTNO."</td>
						<td>".$row->LOCAT."</td>
						<td>".$row->FORDESC."</td>
						<td align='right'>".number_format($row->PAYAMT,2)."</td>
						<td align='right'>".number_format($row->SMPAY,2)."</td>
						<td align='right'>".number_format($row->BALANCE,2)."</td>
						<td align='right'>".number_format($row->SMCHQ,2)."</td>
						<td align='right'>".number_format($row->TOTAL,2)."</td>
					</tr>
				";	
			}
		}else{
			$arothers .= "<tr class='trow'><td colspan='9'>ไม่มี</td></tr>";
		}
		$response["sercharoth"] = $arothers;
		
		$sql2 = "
				select isnull(sum(PAYAMT),0) as sumPAYAMT, isnull(sum(SMPAY),0) as sumSMPAY, isnull(sum(PAYAMT-SMPAY),0) as sumBALANCE, isnull(sum(SMCHQ),0) as sumSMCHQ, isnull(sum(PAYAMT-SMPAY-SMCHQ),0) as sumTOTAL
				from {$this->MAuth->getdb('AROTHR')} a
				where 1=1 ".$cond."
		";
		//echo $sql2; exit;
		$query2 = $this->db->query($sql2);
		
		if($query2->row()){
			foreach($query2->result() as $row){
				$response["sumPAYAMT_S"] 	= number_format($row->sumPAYAMT,2);
				$response["sumSMPAY_S"] 	= number_format($row->sumSMPAY,2);
				$response["sumBALANCE_S"] 	= number_format($row->sumBALANCE,2);
				$response["sumSMCHQ_S"] 	= number_format($row->sumSMCHQ,2);
				$response["sumTOTAL_S"] 	= number_format($row->sumTOTAL,2);
			}
		}else{
				$response["sumPAYAMT_S"] 	= "0.00";
				$response["sumSMPAY_S"] 	= "0.00";
				$response["sumBALANCE_S"] 	= "0.00";
				$response["sumSMCHQ_S"] 	= "0.00";
				$response["sumTOTAL_S"] 	= "0.00";
		}
		
		echo json_encode($response);
	}
	
	function getfromAlertMessage(){
		$TYPALERT	= $_REQUEST["TYPALERT"];
		$hcolor = "";
		if($TYPALERT == 'XX'){
			$hcolor = "background-color:#4169e1;";
		}else{
			$hcolor = "background-color:#d11226;";
		}//echo $hcolor; exit;
		$html = "
			<div class='b_HoldtoOldcar' style='width:600px;height:350px;overflow:auto;background-color:white;'>
				<div style='float:left;overflow:auto;' class='col-sm-12 col-xs-12'>
					<div class='row'>
						<div class='col-sm-12 col-xs-12' style='".$hcolor." border:5px solid white;height:75px;text-align:center;font-size:12pt;color:white;font-weight:bold;'>	
							<br>แสดงข้อความเตือน<br>
						</div>
					</div>
					<div class='row'>
						<div class='form-group' style='border:5px solid white;'>
							<textarea type='text' id='MSGMEMO' rows='8' cols='20' class='form-control input-sm' style='font-size:10.5pt'></textarea>
						</div>
					</div>
					<div class='row'>
						<div class=' col-sm-2'>	
							<div class='form-group'>
								<input type='checkbox' id='savemsg' class='form-check-input' style='cursor:pointer;max-width:20px;max-height:10px;' checked> บันทึก
							</div>
						</div>
						<div class=' col-sm-3 col-sm-offset-7'>	
							<div class='form-group'>
								<button id='btnclose' class='btn btn-default btn-sm text-red' style='width:100%;font-size:10pt;'><span class='glyphicon glyphicon-remove-sign'><b> ปิด</b></span></button>
							</div>
						</div>
					</div>
				</div>
			</div>
		";
	
		$response = array("html"=>$html);
		echo json_encode($response);
	}
	
	function updatemessage(){
		$CONTNO	= $_REQUEST["CONTNO"];
		$STARTDT = $_REQUEST["STARTDT"];
		$ENDDT = $_REQUEST["ENDDT"];
		$MEMOold = $_REQUEST["MSGOLD"];
		$MEMO = $_REQUEST["MSGNEW"];
		
		$sql = "
			if OBJECT_ID('tempdb..#updatemessage') is not null drop table #updatemessage;
			create table #updatemessage (id varchar(20),contno varchar(20),msg varchar(max));
			
			begin tran updatemessage
			begin try
				
				declare @CONTNO varchar(max) = '".$CONTNO."';
				
				update {$this->MAuth->getdb('ALERTMSG')}
				set MEMO1 = '".$MEMO."'
				where CONTNO = @CONTNO collate thai_cs_as and STARTDT = '".$STARTDT."' 
				and ENDDT = '".$ENDDT."' and MEMO1 like '".$MEMOold."%'
				
				insert into #updatemessage select 'S',@CONTNO,'แก้ไขข้อความแจ้งเตือนเรียบร้อย';
				
				commit tran updatemessage;
			end try
			begin catch
				rollback tran updatemessage;
				insert into #updatemessage select 'E','',ERROR_MESSAGE();
			end catch
		";
		//echo $sql; exit;
		
		$this->db->query($sql);
		$sql = "select * from #updatemessage";
		$query = $this->db->query($sql);
	  
		if($query->row()){
			foreach($query->result() as $row){
				$response["status"] = $row->id;
				$response["contno"] = $row->contno;
				$response["msg"] = $row->msg;
			}
		}else{
			$response["status"] = false;
			$response["contno"] = '';
			$response["msg"] = 'ผิดพลาดไม่สามารถแก้ไขข้อความแจ้งเตือน';
		}
		
		echo json_encode($response);
	}
	
	function updateINTAMT(){
		$CONTNO		= $_REQUEST["CONTNO"];
		$DATESEARCH	= $this->Convertdate(1,$_REQUEST["DATESEARCH"]);
		
		$sql = "
			if OBJECT_ID('tempdb..#updateintamt') is not null drop table #updateintamt;
			create table #updateintamt (id varchar(20),contno varchar(20),msg varchar(max));
			
			begin tran updateintamt
			begin try
	
				declare @contno varchar(12) = '".$CONTNO."'
				declare @delayrate decimal(6,4) = (select DELYRT*0.01 from {$this->MAuth->getdb('ARMAST')} where CONTNO = @contno)
				declare @delayday decimal(6,0) = (select DLDAY from {$this->MAuth->getdb('ARMAST')} where CONTNO = @contno)
				declare @locat varchar(10) = (select LOCAT from {$this->MAuth->getdb('ARMAST')} where CONTNO = @contno)
				declare @day varchar(10) = '".$DATESEARCH."'

				update {$this->MAuth->getdb('ARPAY')}
				set DELAY	=	DATEDIFF(DAY,DDATE,@day),
					INTAMT	=	case when DATEDIFF(DAY,DDATE,@day) > @delayday	then round((((DAMT-PAYMENT)*@delayrate)/30)*(DATEDIFF(DAY,DDATE,@day)),0)
								when DATEDIFF(DAY,DDATE,@day) <= @delayday	then 0 end
				where CONTNO = @contno and LOCAT = @locat and DDATE < @day and PAYMENT < DAMT 
				
				insert into #updateintamt select 'S',@CONTNO,'อัพเดทวันล้าช้าและเบี้ยปรับล่าช้าแล้ว';
				
				commit tran updateintamt;
			end try
			begin catch
				rollback tran updateintamt;
				insert into #updateintamt select 'E','',ERROR_MESSAGE();
			end catch
		";
		//echo $sql; exit;
		
		$this->db->query($sql);
		$sql = "select * from #updateintamt";
		$query = $this->db->query($sql);
	  
		if($query->row()){
			foreach($query->result() as $row){
				$response["status"] = $row->id;
				$response["contno"] = $row->contno;
				$response["msg"] = $row->msg;
			}
		}else{
			$response["status"] = false;
			$response["contno"] = '';
			$response["msg"] = 'ผิดพลาดไม่สามารถแก้ไขข้อความแจ้งเตือน';
		}
		
		echo json_encode($response);
	}
	
	function searchpenalty(){
		$CONTNO	= $_REQUEST["CONTNO"];

		$sql = "
				declare @contno varchar(12) = '".$CONTNO."'
				declare @locat varchar(10) = (select LOCAT from {$this->MAuth->getdb('ARMAST')} where CONTNO = @contno)

				select NOPAY, convert(nvarchar,DDATE,103) as DDATE, DAMT, convert(nvarchar,DATE1,103) as DATE1, PAYMENT, DELAY, INTAMT, GRDCOD
				from {$this->MAuth->getdb('ARPAY')}
				where CONTNO = @contno and LOCAT = @locat
				order by NOPAY
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$payment = "";
		if($query->row()){
			foreach($query->result() as $row){
				$bgcolor="";
				//print_r($row->DESC1);
				$payment .= "
					<tr class='trow'>
						<td align='center'>".$row->NOPAY."</td>
						<td align='center'>".$row->DDATE."</td>
						<td align='right'>".number_format($row->DAMT,2)."</td>
						<td align='center'>".$row->DATE1."</td>
						<td align='right'>".number_format($row->PAYMENT,2)."</td>
						<td align='center'>".$row->DELAY."</td>
						<td align='right'>".number_format($row->INTAMT,2)."</td>
						<td align='center'>".$row->GRDCOD."</td>
					</tr>
				";	
			}
		}
		
		$sql2 = "
				declare @contno varchar(12) = '".$CONTNO."'
				declare @locat varchar(10) = (select LOCAT from {$this->MAuth->getdb('ARMAST')} where CONTNO = @contno)

				select isnull(sum(INTAMT),0) as sumINTAMT
				from {$this->MAuth->getdb('ARPAY')}
				where CONTNO = @contno and LOCAT = @locat
		";
		//echo $sql; exit;
		$query2 = $this->db->query($sql2);
		$row2 = $query2->row();
		$sumINTAMT = $row2->sumINTAMT;
		
		$sql3 = "
				declare @contno varchar(12) = '".$CONTNO."'
				declare @locat varchar(10) = (select LOCAT from {$this->MAuth->getdb('ARMAST')} where CONTNO = @contno)

				select isnull(SUM(PAYINT),0) AS PAID, isnull(SUM(DSCINT),0) AS DSCINT 
				from {$this->MAuth->getdb('CHQTRAN')} 
				where CONTNO = @contno and LOCATPAY = @locat and (PAYFOR='006' OR PAYFOR='007') and FLAG !='C' and (PAYDT IS NOT NULL)
		";
		//echo $sql; exit;
		$query3 = $this->db->query($sql3);
		$row3 = $query3->row();
		$PAID = $row3->PAID;
		$DSCINT = $row3->DSCINT;
		$penalty = $sumINTAMT-$PAID;
		
		$response["payment"] 	= $payment;
		$response["sumINTAMT"] 	= number_format($sumINTAMT,2);
		$response["PAID"] 		= number_format($PAID,2);
		$response["DSCINT"] 	= number_format($DSCINT,2);
		$response["penalty"] 	= number_format($penalty,2);
		
		echo json_encode($response);
	}
	
	function searchdiscount(){
		$CONTNO		= $_REQUEST["CONTNO"];
		$DATESEARCH	= $this->Convertdate(1,$_REQUEST["DATESEARCH"]);
		$sql = "
				declare @locat varchar(10)	= (select LOCAT from {$this->MAuth->getdb('ARMAST')} where CONTNO = '".$CONTNO."')
				declare @DISPAY decimal(8,2) = (select isnull(MIN(NOPAY),0) as DISPAY from {$this->MAuth->getdb('ARPAY')} where CONTNO = '".$CONTNO."' and LOCAT = @locat and DDATE >= '".$DATESEARCH."')
				declare @NPROF decimal(8,2)	= ( select sum(NPROF) as NPROF
				from(
					select case when PAYMENT > 0 then (NPROF/DAMT)*PAYMENT else NPROF end as  NPROF  
					from {$this->MAuth->getdb('ARPAY')}  
					where CONTNO = '".$CONTNO."' and LOCAT = @locat and PAYMENT < DAMT and DDATE >= '".$DATESEARCH."'
				)A)
				declare @AROTH decimal(8,2)= (select isnull(SUM(PAYAMT-(SMPAY+SMCHQ)),0) as AROTH  from {$this->MAuth->getdb('AROTHR')}  where CONTNO = '".$CONTNO."' and LOCAT = @locat )
				declare @INTAMT decimal(8,2) = (select sum(INTAMT) from {$this->MAuth->getdb('ARPAY')} where CONTNO = '".$CONTNO."' and LOCAT = @locat)
				declare @PAID decimal(8,2) = ( select isnull(SUM(PAYINT),0) from {$this->MAuth->getdb('CHQTRAN')} where CONTNO = '".$CONTNO."' and LOCATPAY = @locat and 
				(PAYFOR='006' OR PAYFOR='007') and FLAG !='C' and (PAYDT IS NOT NULL) )
				declare @NPROF2 decimal(8,2)	= (select top 1 NPROF from {$this->MAuth->getdb('ARPAY')}  where CONTNO = '".$CONTNO."' and LOCAT = @locat order by NOPAY desc)
				
				select TOTPRC-SMPAY-SMCHQ as TOTAR, case when @DISPAY > 0 then @NPROF*0.3 else 0 end as PERC30, 
				case when @DISPAY > 0 then (TOTPRC-SMPAY-SMCHQ)-(@NPROF*0.3) else TOTPRC-SMPAY-SMCHQ end as TOTPAY,
				@INTAMT-@PAID as INTAMT, 0 as OPERT, case when @DISPAY > 0 then (TOTPRC-SMPAY-SMCHQ)-(@NPROF*0.3)+(@INTAMT-@PAID)+@AROTH 
				else TOTPRC-SMPAY-SMCHQ+(@INTAMT-@PAID)+@AROTH end as NETPAY, @NPROF as NPROF, @NPROF*0.5 as PERC50,
				case when isnull(@NPROF,0) = 0 then @NPROF2 else @NPROF end as NPROF, case when isnull(@NPROF,0) = 0 then @NPROF2*0.5 else @NPROF*0.5 end as PERC50
				from {$this->MAuth->getdb('ARMAST')} 
				where CONTNO = '".$CONTNO."' and LOCAT = @locat
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		if($query->row()){
			foreach($query->result() as $row){
				$response["BALANC"] 	= number_format($row->TOTAR,2);
				$response["DISCOUNT"] 	= number_format($row->PERC30,2);
				$response["NDAMT"] 		= number_format($row->TOTPAY,2);
				$response["NINTAMT"] 	= number_format($row->INTAMT,2);
				$response["OPERT"] 		= number_format($row->OPERT,2);
				$response["TOTAL"] 		= number_format($row->NETPAY,2);
				$response["NPROF"] 		= number_format($row->NPROF,2);
				$response["PRENPROF"] 	= number_format($row->PERC50,2);
			}
		}
		
		echo json_encode($response);
	}
	
	function printpenaltypdf(){
		$mpdf = new \Mpdf\Mpdf([
			'mode' => 'utf-8', 
			'format' => 'A4',
			'margin_top' => 15, 	//default = 16
			'margin_left' => 10, 	//default = 15
			'margin_right' => 10, 	//default = 15
			'margin_bottom' => 15, 	//default = 16
			'margin_header' => 9, 	//default = 9
			'margin_footer' => 9, 	//default = 9
		]);
		
		$tx = explode("||",$_REQUEST['cond']);
		$CONTNO = $tx[0];
		$DATESEARCH = $this->Convertdate(1,$tx[1]);
		
		$sql = " select CUSCOD, LOCAT from {$this->MAuth->getdb('ARMAST')} where CONTNO = '".$CONTNO."' ";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		$row = $query->row();
		$CUSCOD = $row->CUSCOD;
		$LOCAT = $row->LOCAT;
		
		$data = array();
		
		$sql = " select COMP_NM from CONDPAY ";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		$row = $query->row();
		$data[0] = $row->COMP_NM;
		
		$sql = " select  SNAM+NAME1+' '+NAME2 as CUSNAME from {$this->MAuth->getdb('CUSTMAST')} where CUSCOD = '".$CUSCOD."' ";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		$row = $query->row();
		$data[1] = $row->CUSNAME;
		
		$sql = "
				select isnull(ADDR1,'-')+' ถ.'+isnull(ADDR2,'-')+' ต.'+isnull(TUMB,'-')+' อ.'+isnull(b.AUMPDES,'-')+' จ.'+isnull(PROVDES,'-') as CUSADD
				from {$this->MAuth->getdb('CUSTADDR')} a	
				left join {$this->MAuth->getdb('SETAUMP')} b on a.AUMPCOD = b.AUMPCOD 
				left join {$this->MAuth->getdb('SETPROV')} c on b.PROVCOD = c.PROVCOD
				where ADDRNO = '1' and  CUSCOD = '".$CUSCOD."'
		";
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$data[2] = $row->CUSADD;
			}
		}else{
			$data[2] = "";
		}
		
		$sql = "
				select isnull(ADDR1,'-')+' ถ.'+isnull(ADDR2,'-')+' ต.'+isnull(TUMB,'-')+' อ.'+isnull(b.AUMPDES,'-')+' จ.'+isnull(PROVDES,'-') as CUSOFF
				from {$this->MAuth->getdb('CUSTADDR')} a	
				left join {$this->MAuth->getdb('SETAUMP')} b on a.AUMPCOD = b.AUMPCOD 
				left join {$this->MAuth->getdb('SETPROV')} c on b.PROVCOD = c.PROVCOD
				where ADDRNO = '2' and  CUSCOD = '".$CUSCOD."'
		";  
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$data[3] = $row->CUSOFF;
			}
		}else{
			$data[3] = "";
		}
		
		$sql = "
				declare @armgra1 varchar(12)= (select CUSCOD from {$this->MAuth->getdb('ARMGAR')} where CONTNO = '".$CONTNO."' and GARNO = '1')
				select SNAM+NAME1+' '+NAME2 as GRANAME1 from {$this->MAuth->getdb('CUSTMAST')} where CUSCOD = @armgra1
		";
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$data[4] = $row->GRANAME1;
			}
		}else{
			$data[4] = "";
		}
		
		$sql = "
				declare @armgra1 varchar(12)= (select CUSCOD from {$this->MAuth->getdb('ARMGAR')} where CONTNO = '".$CONTNO."' and GARNO = '1')
				select isnull(ADDR1,'-')+' ถ.'+isnull(ADDR2,'-')+' ต.'+isnull(TUMB,'-')+' อ.'+isnull(b.AUMPDES,'-')+' จ.'+isnull(PROVDES,'-') as GARADD1
				from {$this->MAuth->getdb('CUSTADDR')} a 
				left join {$this->MAuth->getdb('SETAUMP')} b on a.AUMPCOD = b.AUMPCOD 
				left join {$this->MAuth->getdb('SETPROV')} c on b.PROVCOD = c.PROVCOD where ADDRNO = '1' and CUSCOD = @armgra1 
		";//echo $sql; exit;
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$data[5] = $row->GARADD1;
			}
		}else{
			$data[5] = "";
		}
		
		$sql = "
				declare @armgra1 varchar(12)= (select CUSCOD from {$this->MAuth->getdb('ARMGAR')} where CONTNO = '".$CONTNO."' and GARNO = '1')
				select isnull(ADDR1,'-')+' ถ.'+isnull(ADDR2,'-')+' ต.'+isnull(TUMB,'-')+' อ.'+isnull(b.AUMPDES,'-')+' จ.'+isnull(PROVDES,'-') as GAROFF1
				from {$this->MAuth->getdb('CUSTADDR')} a	
				left join {$this->MAuth->getdb('SETAUMP')} b on a.AUMPCOD = b.AUMPCOD 
				left join {$this->MAuth->getdb('SETPROV')} c on b.PROVCOD = c.PROVCOD
				where ADDRNO = '2' and  CUSCOD = @armgra1
		";
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$data[6] = $row->GAROFF1;
			}
		}else{
			$data[6] = "";
		}
		
		$sql = "
				declare @armgra2 varchar(12)= (select CUSCOD from {$this->MAuth->getdb('ARMGAR')} where CONTNO = '".$CONTNO."' and GARNO = '2')
				select SNAM+NAME1+' '+NAME2 as GRANAME2 from {$this->MAuth->getdb('CUSTMAST')} where CUSCOD = @armgra2
		";
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$data[7] = $row->GRANAME2;
			}
		}else{
			$data[7] = "";
		}
		
		$sql = "
				declare @armgra2 varchar(12)= (select CUSCOD from {$this->MAuth->getdb('ARMGAR')} where CONTNO = '".$CONTNO."' and GARNO = '2')
				select isnull(ADDR1,'-')+' ถ.'+isnull(ADDR2,'-')+' ต.'+isnull(TUMB,'-')+' อ.'+isnull(b.AUMPDES,'-')+' จ.'+isnull(PROVDES,'-') as GARADD2
				from {$this->MAuth->getdb('CUSTADDR')} a 
				left join {$this->MAuth->getdb('SETAUMP')} b on a.AUMPCOD = b.AUMPCOD 
				left join {$this->MAuth->getdb('SETPROV')} c on b.PROVCOD = c.PROVCOD where ADDRNO = '1' and CUSCOD = @armgra2 
		";//echo $sql; exit;
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$data[8] = $row->GARADD2;
			}
		}else{
			$data[8] = "";
		}
		
		$sql = "
				declare @armgra2 varchar(12)= (select CUSCOD from {$this->MAuth->getdb('ARMGAR')} where CONTNO = '".$CONTNO."' and GARNO = '2')
				select isnull(ADDR1,'-')+' ถ.'+isnull(ADDR2,'-')+' ต.'+isnull(TUMB,'-')+' อ.'+isnull(b.AUMPDES,'-')+' จ.'+isnull(PROVDES,'-') as GAROFF2
				from {$this->MAuth->getdb('CUSTADDR')} a 
				left join {$this->MAuth->getdb('SETAUMP')} b on a.AUMPCOD = b.AUMPCOD 
				left join {$this->MAuth->getdb('SETPROV')} c on b.PROVCOD = c.PROVCOD where ADDRNO = '1' and CUSCOD = @armgra2 
		";//echo $sql; exit;
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$data[9] = $row->GAROFF2;
			}
		}else{
			$data[9] = "";
		}
		
		$sql = "
				select a.TYPE, a.MODEL, a.COLOR, a.STRNO, a.REGNO, b.REGEXP
				from {$this->MAuth->getdb('INVTRAN')} a
				left join {$this->MAuth->getdb('REGTAB')} b on a.STRNO = b.STRNO
				where a.CONTNO = '".$CONTNO."'
		";
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$data[10] = $row->TYPE;
				$data[11] = $row->MODEL;
				$data[12] = $row->COLOR;
				$data[13] = $row->STRNO;
				$data[14] = $row->REGNO;
				$data[15] = $row->REGEXP;
			}
		}
		
		$sql = "
				declare @INTAMT decimal(8,2) = (select isnull(sum(INTAMT),0) from {$this->MAuth->getdb('ARPAY')} where CONTNO = '".$CONTNO."' and LOCAT = '".$LOCAT."' and DDATE <= '".$DATESEARCH."') 
				declare @DAMT decimal(8,2) = (select isnull(sum(DAMT-PAYMENT),0) from {$this->MAuth->getdb('ARPAY')} where CONTNO = '".$CONTNO."' and LOCAT = '".$LOCAT."' and DDATE <= '".$DATESEARCH."') 
				declare @PAID decimal(8,2) = ( select isnull(sum(PAYINT),0) from {$this->MAuth->getdb('CHQTRAN')} where CONTNO = '".$CONTNO."' and LOCATPAY = '".$LOCAT."' 
				and (PAYFOR='006' OR PAYFOR='007') and FLAG !='C' and (PAYDT <= '".$DATESEARCH."') ) 
				select CONTNO, LOCAT, convert(nvarchar,FDATE,112) as FDATE, convert(nvarchar,LDATE,112) as LDATE, VATPRC, NPRICE, 
				TOTPRC, TOTDWN, TOTDWN-PAYDWN as PAYDWN, SMPAY, TOTPRC-SMPAY as BALANC, convert(nvarchar,EXP_FRM)+'-'+convert(nvarchar,EXP_TO) as EXP_FT, 
				EXP_PRD, @DAMT as EXP_AMT, convert(nvarchar,LPAYD,112) as LPAYD, LPAYA, @INTAMT-@PAID as TOTINTAMT, @DAMT+(TOTDWN-PAYDWN)+(@INTAMT-@PAID) as TOTAR,
				T_NOPAY, MEMO1 
				from {$this->MAuth->getdb('ARMAST')} 
				where CONTNO = '".$CONTNO."' 
		";//echo $sql; exit;
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$data[16] = $this->Convertdate(2,$row->FDATE);
				$data[17] = $this->Convertdate(2,$row->LDATE);
				$data[18] = number_format($row->T_NOPAY,2);
				$data[19] = number_format($row->TOTPRC,2);
				$data[20] = number_format($row->NPRICE,2);
				$data[21] = number_format($row->VATPRC,2);
				$data[22] = number_format($row->TOTDWN,2);
				$data[23] = number_format($row->PAYDWN,2);
				$data[24] = number_format($row->SMPAY,2);
				$data[25] = number_format($row->BALANC,2);
				$data[26] = $row->EXP_FT;
				$data[27] = number_format($row->EXP_AMT,2);
				$data[28] = number_format($row->TOTINTAMT,2);
				$data[29] = number_format($row->EXP_PRD);
				$data[30] = number_format($row->TOTAR,2);
				$data[31] = $this->Convertdate(2,$row->LPAYD);
				$data[32] = number_format($row->LPAYA,2);
				$data[33] = str_replace("[explode]"," ",$row->MEMO1);
			}
		}
		
		$sql = "
				select convert(nvarchar,ARDATE,112) as ARDATE, PAYFOR, FORDESC, PAYAMT, SMPAY, PAYAMT-SMPAY as ARBALANC
				from {$this->MAuth->getdb('AROTHR')} a
				left join {$this->MAuth->getdb('PAYFOR')} b on a.PAYFOR = b.FORCODE
				where TSALE = 'X' and CONTNO = '".$CONTNO."'
		";//echo $sql; exit;
		$query = $this->db->query($sql);
		$No = 1;
		$tdaroth = "";
		if($query->row()){
			foreach($query->result() as $row){
				$tdaroth .= "
							<tr>
								<td style='height:25px;'>".$No++."</td>
								<td style='height:25px;'>".$this->Convertdate(2,$row->ARDATE)."</td>
								<td style='height:25px;'>".$row->FORDESC."</td>
								<td style='text-align:right;height:25px;'>".number_format($row->PAYAMT,2)."</td>
								<td style='text-align:right;height:25px;'>".number_format($row->SMPAY,2)."</td>
								<td style='text-align:right;height:25px;'>".number_format($row->ARBALANC,2)."</td>
								<td style='text-align:right;height:25px;'></td>
							</tr>
				";
			}
		}else{
			$tdaroth = "";
		}
		
		$TOTAR = (double)str_replace(',','',$data[30]);
		$sumARBALANC = 0;
		$sql = " select isnull(SUM(PAYAMT-SMPAY),0) as sumARBALANC from {$this->MAuth->getdb('AROTHR')} where TSALE = 'X' and CONTNO = '".$CONTNO."' ";//echo $sql; exit;
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$sumARBALANC = $row->sumARBALANC;
			}
		}
		
		$tdaroth .= "
					<tr>
						<td colspan='7' style='height:25px;'></td>
					</tr>
					<tr>
						<td colspan='4' style='height:25px;'></td>
						<td style='height:25px;'>รวมลูกหนี้คงเหลือดค้างชำระ</td>
						<td style='text-align:right;height:25px;'><u>".number_format($sumARBALANC,2)."</u></td>
						<td style='height:25px;'>&nbsp;บาท</td>
					</tr>
					<tr>
						<td colspan='4' style='height:25px;'></td>
						<td style='height:25px;'><b>ยอดรวมที่ต้องชำระ</b></td>
						<td style='text-align:right;height:25px;'><b><u>".number_format(($sumARBALANC+$TOTAR),2)."</u></b></td>
						<td style='height:25px;'>&nbsp;<b>บาท</b></td>
					</tr>
		";

		$content = "
			<div style='width:100%;text-align:center;font-size:9.5pt;'><b>ใบแจ้งเบี้ยปรับ</b></div><br>
			<div>
				<table width='100%'>
					<tr class='wm'> 
						<td colspan='4'></td>
						<td class='wf pd'>วันที่พิมพ์</td>
						<td class='wf pd'> ".date('d/m/').(date('Y')+543)."</td>
					</tr>
					<tr class='wm'> 
						<td class='wf pd' colspan='4'>{$data[0]}</td>
						<td class='wf pd'>เลขที่บัญชี</td>
						<td class='wf pd'>".$CONTNO."</td>
					</tr>
					<tr class='wm'> 
						<td class='wf pd'>ชื่อ-สกุล ผู้ชื้อ</td>
						<td class='wf pd' colspan='3'>{$data[1]}</td>
						<td class='wf pd'>สาขา</td>
						<td class='wf pd'>".$LOCAT."</td>
					</tr>
					<tr class='wm'>
						<td class='wf pd'>ที่อยู่ ผู้ซื้อ</td>
						<td class='wf pd' colspan='5'>{$data[2]}</td>
					</tr>
					<tr class='wm'>
						<td class='wf pd'>ที่อยู่ ที่ทำงาน</td>
						<td class='wf pd' colspan='5'>{$data[3]}</td>
					</tr>
					<tr class='wm'> 
						<td class='wf pd'>ชื่อ-สกุล ผู้ค้ำ 1</td>
						<td class='wf pd' colspan='5'>{$data[4]}</td>
					</tr>
					<tr class='wm'>
						<td class='wf pd'>ที่อยู่ ผู้ค้ำ 1</td>
						<td class='wf pd' colspan='5'>{$data[5]}</td>
					</tr>
					<tr class='wm'>
						<td class='wf pd'>ที่อยู่ ที่ทำงาน</td>
						<td class='wf pd' colspan='5'>{$data[6]}</td>
					</tr>
					<tr class='wm'> 
						<td class='wf pd'>ชื่อ-สกุล ผู้ค้ำ 2</td>
						<td class='wf pd' colspan='5'>{$data[7]}</td>
					</tr>
					<tr class='wm'>
						<td class='wf pd'>ที่อยู่ ผู้ค้ำ 2</td>
						<td class='wf pd' colspan='5'>{$data[8]}</td>
					</tr>
					<tr class='wm'>
						<td class='wf pd'>ที่อยู่ ที่ทำงาน</td>
						<td class='wf pd' colspan='5'>{$data[9]}</td>
					</tr>
					<tr class='wm'> 
						<td class='wf pd'>ยี่ห้อ</td>
						<td class='wf pd'>{$data[10]}</td>
						<td class='wf pd'>รุ่น</td>
						<td class='wf pd'>{$data[11]}</td>
						<td class='wf pd'>สี</td>
						<td class='wf pd'>{$data[12]}</td>
					</tr>
					<tr class='wm'> 
						<td class='wf pd'>เลขตัวถัง</td>
						<td class='wf pd'>{$data[13]}</td>
						<td class='wf pd'>เลขทะเบียน</td>
						<td class='wf pd'>{$data[14]}</td>
						<td class='wf pd'>ทะเบียนหมดอายุ</td>
						<td class='wf pd'>{$data[15]}</td>
					</tr>
					<tr class='wm'> 
						<td class='wf pd'>วันดิวงวดแรก</td>
						<td class='wf pd'>{$data[16]}</td>
						<td class='wf pd'>วันครบกำหนด</td>
						<td class='wf pd'>{$data[17]}</td>
						<td class='wf pd'>งวดที่</td>
						<td class='wf pd'>{$data[18]}</td>
					</tr>
					<tr class='wm'> 
						<td class='wf pd'>ราคาเช่าซื้อ</td>
						<td class='wf pd tr'>{$data[19]}  บาท</td>
						<td class='wf pd'>มูลค่าสินค้า</td>
						<td class='wf pd tr'>{$data[20]}  บาท</td>
						<td class='wf pd'>ภาษี</td>
						<td class='wf pd tr'>{$data[21]}  บาท</td>
					</tr>
					<tr class='wm'> 
						<td class='wf pd'>เงินดาวน์</td>
						<td class='wf pd tr'>{$data[22]}  บาท</td>
						<td class='wf pd'>ค้างดาวน์</td>
						<td class='wf pd tr'>{$data[23]}  บาท</td>
						<td class='wf pd'></td>
						<td class='wf pd'></td>
					</tr>
					<tr class='wm'> 
						<td class='wf pd'>ชำระเงินแล้ว</td>
						<td class='wf pd tr'>{$data[24]}  บาท</td>
						<td class='wf pd'>ลูกหนี้คงเหลือ</td>
						<td class='wf pd tr'>{$data[25]}  บาท</td>
						<td class='wf pd'></td>
						<td class='wf pd'></td>
					</tr>
					<tr class='wm'> 
						<td class='wf pd'>ค้างชำระงวดที่</td>
						<td class='wf pd'>{$data[26]}</td>
						<td class='wf pd'>เป็นเงิน</td>
						<td class='wf pd tr'>{$data[27]}  บาท</td>
						<td class='wf pd'>ค้างเบี้ยปรับ</td>
						<td class='wf pd tr'>{$data[28]}  บาท</td>
					</tr>
					<tr class='wm'> 
						<td class='wf pd'>จำนวนงวดที่ขาด</td>
						<td class='wf pd'>{$data[29]}  งวด</td>
						<td class='wf pd'></td>
						<td class='wf pd'></td>
						<td class='wf pd'>ค้างค่างวดและเบี้ยปรับ</td>
						<td class='wf pd tr'><u>{$data[30]}</u>  บาท</td>
					</tr>
					<tr class='wm'> 
						<td class='wf pd'>วันที่ชำระครั้งสุดท้าย</td>
						<td class='wf pd'>{$data[31]}</td>
						<td class='wf pd'>จำนวนเงิน</td>
						<td class='wf pd tr'>{$data[32]}  บาท</td>
						<td class='wf pd'></td>
						<td class='wf pd'></td>
					</tr>
				</table>
			</div>
			<div style='width:66%;padding:5px;'>หมายเหตุ : {$data[33]}</div>
			<div style='text-align:center;'><b>ลูกหนี้ค้างชำระค่าอื่นๆ</b></div>
			<div>
				<table style='width:100%;' cellspacing='0'>
					<tr>
						<td class='tt' style='width:5%;'>No.</td>
						<td class='tt' style='width:12%;'>วันที่ค้าง</td>
						<td class='tt' style='width:21%;'>ค้างค่า</td>
						<td class='tt' style='width:19%;text-align:right;'>จำนวนเงิน</td>
						<td class='tt' style='width:19%;text-align:right;'>ชำระแล้ว</td>
						<td class='tt' style='width:19%;text-align:right;'>ยอดคงเหลือ</td>
						<td class='tt' style='width:5%;'></td>
					</tr>
					".$tdaroth."
				</table>
			</div>
		";
		
		$stylesheet = "
			<style>
				body { font-family: garuda;font-size:8pt; }
				.wf { width:16.5%;}
				.wm { width:100%;}
				.pd { padding:5px; }
				.tc { text-align:center; }
				.tr { text-align:right; }
				.tt { border-bottom:0.1px solid black;border-top:0.1px solid black;height:25px; }
				.pf { position:fixed; }
				.bor { border:0.1px solid black; }
				.bor2 { border:0.1px dotted black; }
				.data { background-color:#fff;font-size:9pt; }
			</style>
		";
		
		$mpdf->WriteHTML($content.$stylesheet);
		$mpdf->SetHTMLFooter("<div class='wf pf' style='top:1060;left:0;font-size:6pt;width:720px;text-align:right;'>{$this->sess["name"]} ออกเอกสาร ณ วันที่ ".date('d/m/').(date('Y')+543)." ".date('H:i')."</div>");
		$mpdf->fontdata['qanela'] = array('R' => "QanelasSoft-Regular.ttf",'B' => "QanelasSoft-Bold.ttf",); //แก้ปริ้นแล้วอ่านไม่ออก
		$mpdf->Output();
	}
	
	function printaccountpdf(){
		$mpdf = new \Mpdf\Mpdf([
			'mode' => 'utf-8', 
			'format' => 'A4',
			'margin_top' => 15, 	//default = 16
			'margin_left' => 10, 	//default = 15
			'margin_right' => 10, 	//default = 15
			'margin_bottom' => 15, 	//default = 16
			'margin_header' => 9, 	//default = 9
			'margin_footer' => 9, 	//default = 9
		]);
		
		$tx = explode("||",$_REQUEST['cond']);
		$CONTNO = $tx[0];
		$DATEBILL =  $tx[1];
		$DATESEARCH = $this->Convertdate(1,$tx[1]);
		
		$sql = " select CUSCOD, LOCAT from {$this->MAuth->getdb('ARMAST')} where CONTNO = '".$CONTNO."' ";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		$row = $query->row();
		$CUSCOD = $row->CUSCOD;
		$LOCAT = $row->LOCAT;
		
		$data = array();
		
		$sql = " select COMP_NM from {$this->MAuth->getdb('CONDPAY')} ";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		$row = $query->row();
		$data[0] = $row->COMP_NM;
		
		$sql = " select  SNAM+NAME1+' '+NAME2 as CUSNAME from {$this->MAuth->getdb('CUSTMAST')} where CUSCOD = '".$CUSCOD."' ";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		$row = $query->row();
		$data[1] = $row->CUSNAME;
		
		$sql = "
				select TYPE, MODEL, COLOR, STRNO, ENGNO, REGNO
				from {$this->MAuth->getdb('INVTRAN')}
				where CONTNO = '".$CONTNO."'
		";
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$data[2] = $row->TYPE;
				$data[3] = $row->MODEL;
				$data[4] = $row->COLOR;
				$data[5] = $row->STRNO;
				$data[6] = $row->ENGNO;
				$data[7] = $row->REGNO;
			}
		}
		
		$sql = "
				declare @NOPAYED int = (select COUNT(NOPAY) as NOPAY from {$this->MAuth->getdb('ARPAY')} where CONTNO = '".$CONTNO."' and LOCAT = '".$LOCAT."' and PAYMENT > 0)
				declare @DISPAY decimal(8,2) = (select isnull(MIN(NOPAY),0) as DISPAY from {$this->MAuth->getdb('ARPAY')} where CONTNO = '".$CONTNO."' and LOCAT = '".$LOCAT."' and DDATE >= '".$DATESEARCH."')
				declare @NPROF decimal(8,2)	= ( select sum(NPROF) as NPROF
				from(
					select case when PAYMENT > 0 then (NPROF/DAMT)*PAYMENT else NPROF end as  NPROF  
					from {$this->MAuth->getdb('ARPAY')}  
					where CONTNO = '".$CONTNO."' and LOCAT = '".$LOCAT."' and PAYMENT < DAMT and DDATE >= '".$DATESEARCH."'
				)A)
				declare @AROTH decimal(8,2)= (select isnull(SUM(PAYAMT-(SMPAY+SMCHQ)),0) as AROTH  from {$this->MAuth->getdb('AROTHR')}  where CONTNO = '".$CONTNO."' and LOCAT = '".$LOCAT."' )
				declare @INTAMT decimal(8,2) = (select sum(INTAMT) from {$this->MAuth->getdb('ARPAY')} where CONTNO = '".$CONTNO."' and LOCAT = '".$LOCAT."')
				declare @PAID decimal(8,2) = ( select isnull(SUM(PAYINT),0) from {$this->MAuth->getdb('CHQTRAN')} where CONTNO = '".$CONTNO."' and LOCATPAY = '".$LOCAT."' and 
				(PAYFOR='006' OR PAYFOR='007') and FLAG !='C' and (PAYDT IS NOT NULL) )
				declare @NPROF2 decimal(8,2)	= (select top 1 NPROF from {$this->MAuth->getdb('ARPAY')}  where CONTNO = '".$CONTNO."' and LOCAT = '".$LOCAT."' order by NOPAY desc)

				select T_NOPAY, @NOPAYED as NOPAYED, @DISPAY as DISPAY, case when @DISPAY > 0 then 30 else 0 end as PERCDIS,
				case when @DISPAY > 0 then @NPROF*0.3 else 0 end as PERC30, NPRICE, VATPRC, TOTPRC, NCSHPRC, VCSHPRC, TCSHPRC, TOTDWN, INTRT, NPAYRES, VATPRES, SMPAY, 
				NPRICE-NPAYRES as ARBALANC, VATPRC-VATPRES as VATBALANC, TOTPRC-SMPAY-SMCHQ as TOTAR,  case when @DISPAY > 0 then (TOTPRC-SMPAY-SMCHQ)-(@NPROF*0.3) 
				else TOTPRC-SMPAY-SMCHQ end as TOTPAY, @INTAMT-@PAID as INTAMT, @AROTH as AROTH, case when @DISPAY > 0 then (TOTPRC-SMPAY-SMCHQ)-(@NPROF*0.3)+(@INTAMT-@PAID)+@AROTH 
				else TOTPRC-SMPAY-SMCHQ+(@INTAMT-@PAID)+@AROTH  end as NETPAY, convert(nvarchar,SDATE,112) as SDATE,
				case when isnull(@NPROF,0) = 0 then @NPROF2 else @NPROF end as NPROF, case when isnull(@NPROF,0) = 0 then @NPROF2*0.5 else @NPROF*0.5 end as PERC50
				from {$this->MAuth->getdb('ARMAST')}
				where CONTNO = '".$CONTNO."' and LOCAT = '".$LOCAT."'
		";//echo $sql; exit;
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$data[8]  = $this->Convertdate(2,$row->SDATE);
				$data[9]  = number_format($row->T_NOPAY);
				$data[10] = number_format($row->NOPAYED);
				$data[11] = number_format($row->DISPAY);
				$data[12] = number_format($row->NPROF,2);
				$data[13] = number_format($row->PERC50,2);
				$data[14] = number_format($row->PERCDIS,2);
				$data[15] = number_format($row->PERC30,2);
				$data[16] = number_format($row->NPRICE,2);
				$data[17] = number_format($row->VATPRC,2);
				$data[18] = number_format($row->TOTPRC,2);
				$data[19] = number_format($row->NCSHPRC,2);
				$data[20] = number_format($row->VCSHPRC,2);
				$data[21] = number_format($row->TCSHPRC,2);
				$data[22] = number_format($row->TOTDWN,2);
				$data[23] = number_format($row->INTRT,2);
				$data[24] = number_format($row->NPAYRES,2);
				$data[25] = number_format($row->VATPRES,2);
				$data[26] = number_format($row->SMPAY,2);
				$data[27] = number_format($row->ARBALANC,2);
				$data[28] = number_format($row->VATBALANC,2);
				$data[29] = number_format($row->TOTAR,2);
				$data[30] = number_format($row->TOTPAY,2);
				$data[31] = number_format($row->INTAMT,2);
				$data[32] = number_format($row->AROTH,2);
				$data[33] = number_format($row->NETPAY,2);
			}
		}
		
		$sql = "
				select a.ARCONT, a.ARDATE, a.PAYFOR, b.FORDESC, a.PAYAMT, a.SMPAY, (a.PAYAMT-a.SMPAY) as AROTHBALANC
				from {$this->MAuth->getdb('AROTHR')} a
				left join {$this->MAuth->getdb('PAYFOR')} b on a.PAYFOR = b.FORCODE
				where a.PAYAMT > a.SMPAY and a.CONTNO = '".$CONTNO."' and a.LOCAT = '".$LOCAT."'
				order by a.ARCONT
		";//echo $sql; exit;
		$query = $this->db->query($sql);
		$No = 1;
		$tdaroth = "";
		if($query->row()){
			foreach($query->result() as $row){
				$tdaroth .= "
							<tr>
								<td style='height:25px;'>".$ARCONT."</td>
								<td style='height:25px;'>".$this->Convertdate(2,$row->ARDATE)."</td>
								<td style='height:25px;'>".$row->FORDESC."</td>
								<td style='text-align:right;height:25px;'>".number_format($row->PAYAMT,2)."</td>
								<td style='text-align:right;height:25px;'>".number_format($row->SMPAY,2)."</td>
								<td style='text-align:right;height:25px;'>".number_format($row->AROTHBALANC,2)."</td>
								<td style='text-align:right;height:25px;'></td>
							</tr>
				";
			}
		}else{
			$tdaroth .= "
							<tr>
								<td style='height:25px;'></td>
								<td style='height:25px;'></td>
								<td style='height:25px;'></td>
								<td style='text-align:right;height:25px;'>0.00</td>
								<td style='text-align:right;height:25px;'>0.00</td>
								<td style='text-align:right;height:25px;'>0.00</td>
								<td style='text-align:right;height:25px;'></td>
							</tr>
			";
		}
		
		$TOTAR = (double)str_replace(',','',$data[30]);
		$sumARBALANC = 0;
		$sql = " select SUM(PAYAMT-SMPAY) as sumARBALANC from {$this->MAuth->getdb('AROTHR')} where PAYAMT > SMPAY and CONTNO = '".$CONTNO."' and LOCAT = '".$LOCAT."'";//echo $sql; exit;
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$sumARBALANC = $row->sumARBALANC;
			}
		}
		
		$tdaroth .= "
				<tr>
					<td class='tt' colspan='4' style='height:25px;'></td>
					<td class='tt' style='height:25px;'>รวมลูกหนี้คงเหลือดค้างชำระ</td>
					<td class='tt' style='text-align:right;height:25px;'><u>".number_format($sumARBALANC,2)."</u></td>
					<td class='tt' style='height:25px;'>&nbsp;บาท</td>
				</tr>
		";

		$content = "
			<div style='width:100%;text-align:center;font-size:9.5pt;'><b>ใบตัดสด</b></div><br>
			<div style='width:100%;text-align:right;font-size:8pt;'>[แผนกบัญชี]</div>
			<div>
				<table width='100%' cellspacing='0'>
					<tr class='wm'>
						<td class='wf pd' style='height:1px;border-top:0.1px solid black;' colspan='6'></td>
					</tr>
					<tr class='wm'> 
						<td class='wf pd' colspan='5'>{$data[0]}</td>
						<td class='wf pd tr'>PrnBill40,41</td>
					</tr>
					<tr class='wm'> 
						<td class='wf pd' colspan='6'>ตัดสด ณ วันที่ ".$DATEBILL."</td>
					</tr>
					<tr class='wm'>
						<td class='wf pd'>เลขที่บัญชี</td>
						<td class='wf pd'>".$CONTNO."</td>
						<td class='wf pd'>สาขา</td>
						<td class='wf pd'>".$LOCAT."</td>
						<td class='wf pd'>วันที่ทำสัญญา</td>
						<td class='wf pd'>{$data[8]}</td>
					</tr>
					<tr class='wm'>
						<td class='wf pd'>ชื่อ-สกุล</td>
						<td class='wf pd'>{$data[1]}</td>
						<td class='wf pd'>รหัสลูกค้า</td>
						<td class='wf pd'>".$CUSCOD."</td>
						<td class='wf pd'></td>
						<td class='wf pd'></td>
					</tr>
					<tr class='wm'> 
						<td class='wf pd'>ยี่ห้อ</td>
						<td class='wf pd'>{$data[2]}</td>
						<td class='wf pd'>รุ่น</td>
						<td class='wf pd'>{$data[3]}</td>
						<td class='wf pd'>สี</td>
						<td class='wf pd'>{$data[4]}</td>
					</tr>
					<tr class='wm'>
						<td class='wf pd'>เลขถัง</td>
						<td class='wf pd'>{$data[5]}</td>
						<td class='wf pd'>เลขเครื่อง</td>
						<td class='wf pd'>{$data[6]}</td>
						<td class='wf pd'>เลขทะเบียน</td>
						<td class='wf pd'>{$data[7]}</td>
					</tr>
					<tr class='wm'>
						<td class='wf pd' style='height:1px;border-bottom:0.1px solid black;' colspan='6'></td>
					</tr>
					<tr class='wm'>
						<td class='wf pd' style='height:1px;' colspan='6'></td>
					</tr>
					<tr class='wm'> 
						<td class='wf pd'>จำนวนงวดทั้งหมด</td>
						<td class='wf pd tr'>{$data[9]} งวด</td>
						<td class='wf pd'>จำนวนงวดที่ผ่อนมาแล้ว</td>
						<td class='wf pd tr'>{$data[10]} งวด</td>
						<td class='wf pd'>ตัดสดงวดที่</td>
						<td class='wf pd tr'>{$data[11]}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
					</tr>
					<tr class='wm'>
						<td class='wf pd'>ดอกผลคงเหลือ</td>
						<td class='wf pd tr'>{$data[12]} บาท</td>
						<td class='wf pd'>ส่วนลด</td>
						<td class='wf pd tr'>50%&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
						<td class='wf pd'>เงินส่วนลด</td>
						<td class='wf pd tr'>{$data[13]} บาท</td>
					</tr>
					<tr class='wm'>
						<td class='wf pd'></td>
						<td class='wf pd tr'></td>
						<td class='wf pd'>ส่วนลด</td>
						<td class='wf pd tr'>{$data[14]}%&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
						<td class='wf pd'>เงินส่วนลด</td>
						<td class='wf pd tr'>{$data[15]} บาท</td>
					</tr>
					<tr class='wm'> 
						<td class='wf pd'>มูลค่าราคาเช่าซื้อ</td>
						<td class='wf pd tr'>{$data[16]} บาท</td>
						<td class='wf pd'>ภาษีราคาเช่าซื้อ</td>
						<td class='wf pd tr'>{$data[17]} บาท</td>
						<td class='wf pd'>ราคาเช่าซื้อ</td>
						<td class='wf pd tr'>{$data[18]} บาท</td>
					</tr>
					<tr class='wm'> 
						<td class='wf pd'>มูลค่าราคาขายสด</td>
						<td class='wf pd tr'>{$data[19]} บาท</td>
						<td class='wf pd'>ภาษีราคาขายสด</td>
						<td class='wf pd tr'>{$data[20]} บาท</td>
						<td class='wf pd'>ราคาขายสด</td>
						<td class='wf pd tr'>{$data[21]} บาท</td>
					</tr>
					<tr class='wm'> 
						<td class='wf pd'>มูลค่าเงินดาวน์</td>
						<td class='wf pd tr'>{$data[22]} บาท</td>
						<td class='wf pd'>อัตราดอกเบี้ย</td>
						<td class='wf pd tr'>{$data[23]} บาท</td>
						<td class='wf pd'></td>
						<td class='wf pd tr'></td>
					</tr>
					<tr class='wm'> 
						<td class='wf pd'>มูลค่าชำระแล้ว</td>
						<td class='wf pd tr'>{$data[24]} บาท</td>
						<td class='wf pd'>ภาษีชำระแล้ว</td>
						<td class='wf pd tr'>{$data[25]} บาท</td>
						<td class='wf pd'>ชำระเงินแล้ว</td>
						<td class='wf pd tr'>{$data[26]} บาท</td>
					</tr>
					<tr class='wm'> 
						<td class='wf pd'>มูลค่าลุกหนี้คงเหลือ</td>
						<td class='wf pd tr'>{$data[27]} บาท</td>
						<td class='wf pd'>ภาษีคงเหลือ</td>
						<td class='wf pd tr'>{$data[28]} บาท</td>
						<td class='wf pd'>ลูกหนี้คงเหลือรวม</td>
						<td class='wf pd tr'>{$data[29]} บาท</td>
					</tr>
					<tr class='wm'> 
						<td class='wf pd'></td>
						<td class='wf pd tr'></td>
						<td class='wf pd'></td>
						<td class='wf pd tr'></td>
						<td class='wf pd'>ยอดต้องชำระ</td>
						<td class='wf pd tr'>{$data[30]} บาท</td>
					</tr>
					<tr class='wm'> 
						<td class='wf pd'></td>
						<td class='wf pd tr'></td>
						<td class='wf pd'></td>
						<td class='wf pd tr'></td>
						<td class='wf pd'>เบี้ยปรับ</td>
						<td class='wf pd tr'>{$data[31]} บาท</td>
					</tr>
					<tr class='wm'> 
						<td class='wf pd'></td>
						<td class='wf pd tr'></td>
						<td class='wf pd'></td>
						<td class='wf pd tr'></td>
						<td class='wf pd'>ลูกหนี้อื่นๆ</td>
						<td class='wf pd tr'>{$data[32]} บาท</td>
					</tr>
					<tr class='wm'> 
						<td class='wf pd'></td>
						<td class='wf pd tr'></td>
						<td class='wf pd'></td>
						<td class='wf pd tr'></td>
						<td class='wf pd'>รวมต้องชำระ</td>
						<td class='wf pd tr'><u>{$data[33]}</u>  บาท</td>
					</tr>
				</table>
			</div>
			<br>
			<div style='text-align:center;'><b>ลูกหนี้ค้างชำระค่าอื่นๆ</b></div>
			<div>
				<table style='width:100%;' cellspacing='0'>
					<tr>
						<td class='tt' style='background-color:#eee;width:13%;'>เลขที่ลูกหนี้</td>
						<td class='tt' style='background-color:#eee;width:10%;'>วันที่ค้าง</td>
						<td class='tt' style='background-color:#eee;width:15%;'>ค้างค่า</td>
						<td class='tt' style='background-color:#eee;width:19%;text-align:right;'>จำนวนเงิน</td>
						<td class='tt' style='background-color:#eee;width:19%;text-align:right;'>ชำระแล้ว</td>
						<td class='tt' style='background-color:#eee;width:19%;text-align:right;'>ยอดคงเหลือ</td>
						<td class='tt' style='background-color:#eee;width:5%;'></td>
					</tr>
					".$tdaroth."
				</table>
			</div>
		";
		
		$stylesheet = "
			<style>
				body { font-family: garuda;font-size:8pt; }
				.wf { width:16.5%;}
				.wm { width:100%;}
				.pd { padding:5px; }
				.tc { text-align:center; }
				.tr { text-align:right; }
				.tt { border-bottom:0.1px solid black;border-top:0.1px solid black;height:25px; }
				.pf { position:fixed; }
				.bor { border:0.1px solid black; }
				.bor2 { border:0.1px dotted black; }
				.data { background-color:#fff;font-size:9pt; }
			</style>
		";
		
		$mpdf->WriteHTML($content.$stylesheet);
		$mpdf->SetHTMLFooter("<div class='wf pf' style='top:1060;left:0;font-size:6pt;width:720px;text-align:right;'>{$this->sess["name"]} ออกเอกสาร ณ วันที่ ".date('d/m/').(date('Y')+543)." ".date('H:i')."</div>");
		$mpdf->fontdata['qanela'] = array('R' => "QanelasSoft-Regular.ttf",'B' => "QanelasSoft-Bold.ttf",); //แก้ปริ้นแล้วอ่านไม่ออก
		$mpdf->Output();
	}
	
	function printcustomerpdf(){
		$mpdf = new \Mpdf\Mpdf([
			'mode' => 'utf-8', 
			'format' => 'A4',
			'margin_top' => 15, 	//default = 16
			'margin_left' => 10, 	//default = 15
			'margin_right' => 10, 	//default = 15
			'margin_bottom' => 15, 	//default = 16
			'margin_header' => 9, 	//default = 9
			'margin_footer' => 9, 	//default = 9
		]);
		
		$tx = explode("||",$_REQUEST['cond']);
		$CONTNO = $tx[0];
		$DATEBILL =  $tx[1];
		$DATESEARCH = $this->Convertdate(1,$tx[1]);
		
		$sql = " select CUSCOD, LOCAT from {$this->MAuth->getdb('ARMAST')} where CONTNO = '".$CONTNO."' ";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		$row = $query->row();
		$CUSCOD = $row->CUSCOD;
		$LOCAT = $row->LOCAT;
		
		$data = array();
		
		$sql = " select COMP_NM from {$this->MAuth->getdb('CONDPAY')} ";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		$row = $query->row();
		$data[0] = $row->COMP_NM;
		
		$sql = " select  SNAM+NAME1+' '+NAME2 as CUSNAME from {$this->MAuth->getdb('CUSTMAST')} where CUSCOD = '".$CUSCOD."' ";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		$row = $query->row();
		$data[1] = $row->CUSNAME;
		
		$sql = "
				select TYPE, MODEL, COLOR, STRNO, ENGNO, REGNO
				from {$this->MAuth->getdb('INVTRAN')}
				where CONTNO = '".$CONTNO."'
		";
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$data[2] = $row->TYPE;
				$data[3] = $row->MODEL;
				$data[4] = $row->COLOR;
				$data[5] = $row->STRNO;
				$data[6] = $row->ENGNO;
				$data[7] = $row->REGNO;
			}
		}
		
		$sql = "
				declare @NOPAYED int = (select COUNT(NOPAY) as NOPAY from {$this->MAuth->getdb('ARPAY')} where CONTNO = '".$CONTNO."' and LOCAT = '".$LOCAT."' and PAYMENT > 0)
				declare @DISPAY decimal(8,2) = (select isnull(MIN(NOPAY),0) as DISPAY from {$this->MAuth->getdb('ARPAY')} where CONTNO = '".$CONTNO."' and LOCAT = '".$LOCAT."' and DDATE >= '".$DATESEARCH."')
				declare @NPROF decimal(8,2)	= ( select sum(NPROF) as NPROF
				from(
					select case when PAYMENT > 0 then (NPROF/DAMT)*PAYMENT else NPROF end as  NPROF  
					from {$this->MAuth->getdb('ARPAY')}  
					where CONTNO = '".$CONTNO."' and LOCAT = '".$LOCAT."' and PAYMENT < DAMT and DDATE >= '".$DATESEARCH."'
				)A)
				declare @AROTH decimal(8,2)= (select isnull(SUM(PAYAMT-(SMPAY+SMCHQ)),0) as AROTH  from {$this->MAuth->getdb('AROTHR')}  where CONTNO = '".$CONTNO."' and LOCAT = '".$LOCAT."' )
				declare @INTAMT decimal(8,2) = (select sum(INTAMT) from {$this->MAuth->getdb('ARPAY')} where CONTNO = '".$CONTNO."' and LOCAT = '".$LOCAT."')
				declare @PAID decimal(8,2) = ( select isnull(SUM(PAYINT),0) from {$this->MAuth->getdb('CHQTRAN')} where CONTNO = '".$CONTNO."' and LOCATPAY = '".$LOCAT."' and 
				(PAYFOR='006' OR PAYFOR='007') and FLAG !='C' and (PAYDT IS NOT NULL) )
			
				select T_NOPAY, @NOPAYED as NOPAYED, @DISPAY as DISPAY, TCSHPRC, TOTPRC, SMPAY, TOTPRC-SMPAY-SMCHQ as TOTAR, @INTAMT-@PAID as INTAMT,
				@AROTH as AROTH, TOTPRC-SMPAY-SMCHQ+(@INTAMT-@PAID)+@AROTH as TOTPAY, case when @DISPAY > 0 then @NPROF*0.3 else 0 end as PERC30, 
				case when @DISPAY > 0 then (TOTPRC-SMPAY-SMCHQ)-(@NPROF*0.3)+(@INTAMT-@PAID)+@AROTH else TOTPRC-SMPAY-SMCHQ+(@INTAMT-@PAID)+@AROTH end
				as NETPAY, convert(nvarchar,SDATE,112) as SDATE
				from {$this->MAuth->getdb('ARMAST')} 
				where CONTNO = '".$CONTNO."' and LOCAT = '".$LOCAT."'
		";//echo $sql; exit;
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$data[8]  = $this->Convertdate(2,$row->SDATE);
				$data[9]  = number_format($row->T_NOPAY);
				$data[10] = number_format($row->NOPAYED);
				$data[11] = number_format($row->DISPAY);
				$data[12] = number_format($row->TOTPRC,2);
				$data[13] = number_format($row->TCSHPRC,2);
				$data[14] = number_format($row->SMPAY,2);
				$data[15] = number_format($row->TOTAR,2);
				$data[16] = number_format($row->INTAMT,2);
				$data[17] = number_format($row->AROTH,2);
				$data[18] = number_format($row->TOTPAY,2);
				$data[19] = number_format($row->PERC30,2);
				$data[20] = number_format($row->NETPAY,2);
			}
		}
		
		$sql = "
				select a.ARCONT, a.ARDATE, a.PAYFOR, b.FORDESC, a.PAYAMT, a.SMPAY, (a.PAYAMT-a.SMPAY) as AROTHBALANC
				from {$this->MAuth->getdb('AROTHR')} a
				left join {$this->MAuth->getdb('PAYFOR')} b on a.PAYFOR = b.FORCODE
				where a.PAYAMT > a.SMPAY and a.CONTNO = '".$CONTNO."' and a.LOCAT = '".$LOCAT."'
				order by a.ARCONT
		";//echo $sql; exit;
		$query = $this->db->query($sql);
		$No = 1;
		$tdaroth = "";
		if($query->row()){
			foreach($query->result() as $row){
				$tdaroth .= "
							<tr>
								<td style='height:25px;'>".$ARCONT."</td>
								<td style='height:25px;'>".$this->Convertdate(2,$row->ARDATE)."</td>
								<td style='height:25px;'>".$row->FORDESC."</td>
								<td style='text-align:right;height:25px;'>".number_format($row->PAYAMT,2)."</td>
								<td style='text-align:right;height:25px;'>".number_format($row->SMPAY,2)."</td>
								<td style='text-align:right;height:25px;'>".number_format($row->AROTHBALANC,2)."</td>
								<td style='text-align:right;height:25px;'></td>
							</tr>
				";
			}
		}else{
			$tdaroth .= "
							<tr>
								<td style='height:25px;'></td>
								<td style='height:25px;'></td>
								<td style='height:25px;'></td>
								<td style='text-align:right;height:25px;'>0.00</td>
								<td style='text-align:right;height:25px;'>0.00</td>
								<td style='text-align:right;height:25px;'>0.00</td>
								<td style='text-align:right;height:25px;'></td>
							</tr>
			";
		}
		
		$TOTAR = (double)str_replace(',','',$data[30]);
		$sumARBALANC = 0;
		$sql = " select SUM(PAYAMT-SMPAY) as sumARBALANC from {$this->MAuth->getdb('AROTHR')} where PAYAMT > SMPAY and CONTNO = '".$CONTNO."' and LOCAT = '".$LOCAT."'";//echo $sql; exit;
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$sumARBALANC = $row->sumARBALANC;
			}
		}
		
		$tdaroth .= "
				<tr>
					<td class='tt' colspan='4' style='height:25px;'></td>
					<td class='tt' style='height:25px;'>รวมลูกหนี้คงเหลือดค้างชำระ</td>
					<td class='tt' style='text-align:right;height:25px;'><u>".number_format($sumARBALANC,2)."</u></td>
					<td class='tt' style='height:25px;'>&nbsp;บาท</td>
				</tr>
		";

		$content = "
			<div style='width:100%;text-align:center;font-size:9.5pt;'><b>ใบตัดสด</b></div>
			<div style='width:100%;text-align:right;font-size:8pt;'>[สำหรับลูกค้า]</div>
			<div>
				<table width='100%' cellspacing='0'>
					<tr class='wm'>
						<td class='wf pd' style='height:1px;border-top:0.1px solid black;' colspan='6'></td>
					</tr>
					<tr class='wm'> 
						<td class='wf pd' colspan='2'>{$data[0]}</td>
						<td class='wf pd'>ตัดสด ณ วันที่</td>
						<td class='wf pd'>".$DATEBILL."</td>
						<td class='wf pd'></td>
						<td class='wf pd'>PrnBill50,51</td>
					</tr>
					<tr class='wm'>
						<td class='wf pd'>เลขที่บัญชี</td>
						<td class='wf pd'>".$CONTNO."</td>
						<td class='wf pd'>สาขา</td>
						<td class='wf pd'>".$LOCAT."</td>
						<td class='wf pd'></td>
						<td class='wf pd'></td>
					</tr>
					<tr class='wm'>
						<td class='wf pd'>ชื่อ-สกุล</td>
						<td class='wf pd'>{$data[1]}</td>
						<td class='wf pd'>รหัสลูกค้า</td>
						<td class='wf pd'>".$CUSCOD."</td>
						<td class='wf pd'>วันที่ทำสัญญา</td>
						<td class='wf pd'>{$data[8]}</td>
					</tr>
					<tr class='wm'> 
						<td class='wf pd'>ยี่ห้อ</td>
						<td class='wf pd'>{$data[2]}</td>
						<td class='wf pd'>รุ่น</td>
						<td class='wf pd'>{$data[3]}</td>
						<td class='wf pd'>สี</td>
						<td class='wf pd'>{$data[4]}</td>
					</tr>
					<tr class='wm'>
						<td class='wf pd'>เลขถัง</td>
						<td class='wf pd'>{$data[5]}</td>
						<td class='wf pd'>เลขเครื่อง</td>
						<td class='wf pd'>{$data[6]}</td>
						<td class='wf pd'>เลขทะเบียน</td>
						<td class='wf pd'>{$data[7]}</td>
					</tr>
					<tr class='wm'>
						<td class='wf pd' style='height:1px;border-bottom:0.1px solid black;' colspan='6'></td>
					</tr>
					<tr class='wm'>
						<td class='wf pd' style='height:1px;' colspan='6'></td>
					</tr>
					<tr class='wm'> 
						<td class='wf pd'>ราคาขายสด</td>
						<td class='wf pd tr'>{$data[13]} บาท</td>
						<td class='wf pd'>จำนวนงวดที่ผ่อนมาแล้ว</td>
						<td class='wf pd tr'>{$data[10]} งวด</td>
						<td class='wf pd'></td>
						<td class='wf pd tr'></td>
					</tr>
					<tr class='wm'>
						<td class='wf pd'>ราคาเช่าซื้อ</td>
						<td class='wf pd tr'>{$data[12]} บาท</td>
						<td class='wf pd'>จำนวนงวดทั้งหมด</td>
						<td class='wf pd tr'>{$data[9]} งวด</td>
						<td class='wf pd'></td>
						<td class='wf pd tr'></td>
					</tr>
					<tr class='wm'>
						<td class='wf pd'>ยอดชำระ</td>
						<td class='wf pd tr'>{$data[14]} บาท</td>
						<td class='wf pd'>ตัดสด งวดที่</td>
						<td class='wf pd tr'>{$data[11]} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
						<td class='wf pd'></td>
						<td class='wf pd tr'></td>
					</tr>
					<tr class='wm'> 
						<td class='wf pd'>ลูกหนี้คงเหลือรวม</td>
						<td class='wf pd tr'>{$data[15]} บาท</td>
						<td class='wf pd'></td>
						<td class='wf pd'></td>
						<td class='wf pd'></td>
						<td class='wf pd'></td>
					</tr>
					<tr class='wm'> 
						<td class='wf pd'>เบี้ยปรับ</td>
						<td class='wf pd tr'>{$data[16]} บาท</td>
						<td class='wf pd'></td>
						<td class='wf pd'></td>
						<td class='wf pd'></td>
						<td class='wf pd'></td>
					</tr>
					<tr class='wm'> 
						<td class='wf pd'>ลูกหนี้อื่นๆ</td>
						<td class='wf pd tr'>{$data[17]} บาท</td>
						<td class='wf pd'></td>
						<td class='wf pd'></td>
						<td class='wf pd'></td>
						<td class='wf pd'></td>
					</tr>
					<tr class='wm'> 
						<td class='wf pd'>รวมยอดค้าง</td>
						<td class='wf pd tr'><u>{$data[18]}</u> บาท</td>
						<td class='wf pd'></td>
						<td class='wf pd'></td>
						<td class='wf pd'></td>
						<td class='wf pd'></td>
					</tr>
					<tr class='wm'> 
						<td class='wf pd'>เงินส่วนลด</td>
						<td class='wf pd tr'><u>{$data[19]}</u> บาท</td>
						<td class='wf pd'></td>
						<td class='wf pd'></td>
						<td class='wf pd'></td>
						<td class='wf pd'></td>
					</tr>
					<tr class='wm'> 
						<td class='wf pd'>รวมต้องชำระ</td>
						<td class='wf pd tr'><u>{$data[20]}</u> บาท</td>
						<td class='wf pd'></td>
						<td class='wf pd'></td>
						<td class='wf pd'></td>
						<td class='wf pd'></td>
					</tr>
				</table>
			</div>
			<br>
			<div style='text-align:center;'><b>ลูกหนี้ค้างชำระค่าอื่นๆ</b></div>
			<div>
				<table style='width:100%;' cellspacing='0'>
					<tr>
						<td class='tt' style='background-color:#eee;width:13%;'>เลขที่ลูกหนี้</td>
						<td class='tt' style='background-color:#eee;width:10%;'>วันที่ค้าง</td>
						<td class='tt' style='background-color:#eee;width:15%;'>ค้างค่า</td>
						<td class='tt' style='background-color:#eee;width:19%;text-align:right;'>จำนวนเงิน</td>
						<td class='tt' style='background-color:#eee;width:19%;text-align:right;'>ชำระแล้ว</td>
						<td class='tt' style='background-color:#eee;width:19%;text-align:right;'>ยอดคงเหลือ</td>
						<td class='tt' style='background-color:#eee;width:5%;'></td>
					</tr>
					".$tdaroth."
				</table>
			</div>
		";
		
		$stylesheet = "
			<style>
				body { font-family: garuda;font-size:8pt; }
				.wf { width:16.5%;}
				.wm { width:100%;}
				.pd { padding:5px; }
				.tc { text-align:center; }
				.tr { text-align:right; }
				.tt { border-bottom:0.1px solid black;border-top:0.1px solid black;height:25px; }
				.pf { position:fixed; }
				.bor { border:0.1px solid black; }
				.bor2 { border:0.1px dotted black; }
				.data { background-color:#fff;font-size:9pt; }
			</style>
		";
		//$mpdf->AddPage();
		$mpdf->WriteHTML($content.$stylesheet);
		$mpdf->SetHTMLFooter("<div class='wf pf' style='top:1060;left:0;font-size:6pt;width:720px;text-align:right;'>{$this->sess["name"]} ออกเอกสาร ณ วันที่ ".date('d/m/').(date('Y')+543)." ".date('H:i')."</div>");
		$mpdf->fontdata['qanela'] = array('R' => "QanelasSoft-Regular.ttf",'B' => "QanelasSoft-Bold.ttf",); //แก้ปริ้นแล้วอ่านไม่ออก
		$mpdf->Output();
	}
}