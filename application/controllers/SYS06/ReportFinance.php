<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/********************************************************
             ______@26/11/2019______
			 Pasakorn Boonded

********************************************************/
class ReportFinance extends MY_Controller {
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
		//jong add $cuscod
		$cuscod = (!isset($_GET["cc"])?'':$_GET["cc"]);
		
		$claim = $this->MLogin->getclaim(uri_string());
		if($claim['m_access'] != "T"){ echo "<div align='center' style='color:red;font-size:16pt;width:100%;'>ขออภัย คุณยังไม่มีสิทธิเข้าใช้งานหน้านี้ครับ</div>"; exit; }
		$html = "
			<div class='k_tab1' name='home' locat='{$this->sess['branch']}' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}' today='".$this->today('today')."' style='height:calc(100vh - 132px);overflow:auto;'>
				<div class='col-sm-12 col-xs-12' style='float:left;height:100%;overflow:auto;'>
					<div id='wizard-financedetail' class='wizard-wrapper'>    
						<div class='wizard'>
							<form id='demo-form2' action='' class='lobi-form' novalidate='novalidate'>
								<ul class='wizard-tabs wizard-tab-balls nav-justified nav nav-pills'>
									<li class='active' style='background-color:#83f0d6; solid #83f0d6;'>
										<a href='#tab11' prev='#' data-toggle='tab' aria-expanded='true'>
											<span class='step'>1</span>
											<span class='title'><b>ยอดค้างชำระ</b></span>
										</a>
									</li>
									<li style='background-color:#83f0d6; solid #83f0d6;'>
										<a href='#tab22' prev='#tab11' data-toggle='tab'>
											<span class='step'>2</span>
											<span class='title'><b>รายละเอียดสินค้า</b></span>
										</a>
									</li>
									<li style='background-color:#83f0d6; solid #83f0d6;'>
										<a href='#tab33' prev='#tab22' data-toggle='tab'>
											<span class='step'>3</span>
											<span class='title'><b>รายละเอียดสัญญา</b></span>
										</a>
									</li>
									<li style='background-color:#83f0d6; solid #83f0d6;'>
										<a href='#tab44' prev='#tab33' data-toggle='tab'>
											<span class='step'>4</span>
											<span class='title'><b>รายการรับชำระเงิน</b></span>
										</a>
									</li>
									<li style='background-color:#83f0d6; solid #83f0d6;'>
										<a href='#tab55' prev='#tab44' data-toggle='tab'>
											<span class='step'>5</span>
											<span class='title'><b>รายละเอียดผู้ซื้อ</b></span>
										</a>
									</li>	
									<li style='background-color:#83f0d6; solid #83f0d6;'>
										<a href='#tab66' prev='#tab55' data-toggle='tab'>
											<span class='step'>6</span>
											<span class='title'><b>รายละเอียดผู้ค้ำ</b></span>
										</a>
									</li>
									<li style='background-color:#83f0d6; solid #83f0d6;'>
										<a href='#tab77' prev='#tab66' data-toggle='tab'>
											<span class='step'>7</span>
											<span class='title'><b>เปลี่ยนสถานะสัญญา</b></span>
										</a>
									</li>
									<li style='background-color:#83f0d6; solid #83f0d6;'>
										<a href='#tab88' prev='#tab77' data-toggle='tab'>
											<span class='step'>8</span>
											<span class='title'><b>ลูกหนี้อื่น</b></span>
										</a>
									</li>
								</ul>
								<div class='tab-content bg-white'>
									".$this->getfromFinanceTab11($cuscod)."
									".$this->getfromFinanceTab22()."
									".$this->getfromFinanceTab33()."
									".$this->getfromFinanceTab44()."
									".$this->getfromFinanceTab55()."
									".$this->getfromFinanceTab66()."
									".$this->getfromFinanceTab77()."
									".$this->getfromFinanceTab88()."
								</div>
							</form>
						</div>
					</div>				
				</div>
			</div>
		";
		
		$html .="<script src='".base_url('public/js/SYS06/ReportFinance.js')."'></script>";
		echo ($html);
	}
	function getfromFinanceTab11($cuscod){
		$html = "
			<div class='tab-pane active' name='tab11' style='height:calc(100vh - 260px);overflow:auto;'>
				<fieldset style='height:100%'>
					<div style='float:left;height:100%;' class='col-sm-12 col-xs-12'>
						<b>เงื่อนไข</b>
						<div class='row' style='border:1px dotted #aaa;'>
							<div class='row'>
								<div class='col-sm-10 col-xs-10 col-sm-offset-2' style='height:100%;'>	
									<div class='row'>
										<div class='col-sm-3 text-primary'>
											<b>รหัสลูกค้า</b>
											<select id='CUSCOD1' class='form-control input-sm' data-placeholder='รหัสลูกค้า'>
												<option value='".$cuscod."'>".$cuscod."</option>
											</select>
										</div>
										<div class='col-sm-3 text-primary'>
											<b>เลขที่สัญญา</b>
											<select id='CONTNO1' class='form-control input-sm' data-placeholder='เลขที่สัญญา'></select>
										</div>
										<div class='col-sm-4 text-danger'>
											<br>
											<input class='form-check-input' style='cursor:pointer;max-width:20px;max-height:10px;' type='checkbox' id='tab11C' value='*' checked>
											<label class='form-check-label' style='cursor:pointer;' for='tab11C'>(*) แสดงหนี้สูญ แลกเปลี่ยน รถยึดเปลี่ยนสภาพ</label>
										</div>
									</div>
								</div>
							</div><br>
							<div class='row'>
								<div class='col-sm-10 col-xs-10 col-sm-offset-2' style='height:100%;'>	
									<div class='row'>
										<div class='col-sm-3 text-primary'>
											<b>เลขตัวถัง</b>
											<select id='STRNO1' class='form-control input-sm' data-placeholder='เลขตัวถัง'></select>
										</div>
										<div class='col-sm-3 text-primary'>
											<b>เลขทะเบียน</b>
											<select id='REGNO1' class='form-control input-sm' data-placeholder='เลขทะเบียน'></select>
										</div>
										<div class='col-sm-2'>
											<br>
											<button id='btnsearch' class='btn btn-primary btn-sm' style='width:100%'><span class='glyphicon glyphicon-search'><b>สอบถาม</b></span></button>
										</div>
										<div class='col-sm-2'>
											<br>
											<button id='btnmsg' class='btn btn-warning btn-sm' style='width:100%'><span class='glyphicon glyphicon-list-alt'><b>ข้อความเตือน</b></span></button>
										</div>
									</div>
								</div>
							</div><br>
						</div>
						<br>
						<b>การซื้อ</b>
						<div class='row' style='border:0.1px solid #bdbdbd;background-color:#dedede;'>
							<div class='col-sm-10 col-xs-12' style='height:100%;'>	
								<div class='row'>
									<div class='col-sm-2 text-primary' style='text-align:center;'>
										ชื่อ-สกุล
									</div>
									<div class='col-sm-2'>
										<input type='text' class='form-control input-sm checkvalue' id='1_SNAM' value='' readonly>
									</div>
									<div class='col-sm-3'>
										<input type='text' class='form-control input-sm checkvalue' id='1_NAME1' value='' readonly>
									</div>
									<div class='col-sm-3'>
										<input type='text' class='form-control input-sm checkvalue' id='1_NAME2' value='' readonly>
									</div>
								</div>
							</div>
						</div>
						<div class='row' style='height:30%;border:0.1px solid #bdbdbd;background-color:#eee;'>
							<div class='col-sm-12 col-xs-12' style='height:100%;'>
								<div id='cusbuy' class='dataTables_wrapper dt-bootstrap4 table-responsive' style='height:100%;width:100%;overflow:auto;'>
									<table id='dataTables-cusbuy' class='table table-bordered dataTable table-hover' stat='' aria-describedby='dataTable_info' cellspacing='0' width='calc(100% - 1px)'>
										<thead>
											<tr role='row' style='font-size:8pt;background-color:#666666;color:white;'>
												<th width='12.5%'>เลขที่สัญญา</th>
												<th width='12.5%'>รหัสลูกค้า</th>
												<th width='12.5%'>ประเภท ล/น</th>
												<th width='12.5%'>สาขา</th>
												<th width='12.5%'>วันขาย</th>
												<th width='12.5%'>ราคาขาย</th>
												<th width='12.5%'>ชำระแล้ว</th>
												<th width='12.5%'>ล/น คงเหลือ</th>
												<th width='6%'>*</th>
											</tr>
										</thead>
										<tbody style='white-space:nowrap;background-color:white;font-size:9pt;'></tbody>
									</table>
								</div>
							</div>
						</div>
						<div class='row' style='border:0.1px solid #bdbdbd;background-color:#dedede;'>
							<div class='col-sm-12 col-xs-12' style='height:100%;'>	
								<div id='fix-cusbuy' style='width:100%;overflow:auto;'>
									<table id='cusbuy' width='calc(100% - 1px)'>
										<tr style='font-size:9pt;'>
											<th colspan='1' class='text-primary' style='text-align:center;'><b>รวม</b></th>
											<th width='14%' style='padding:2px;'><input type='text' id='1_COUNTCONTNO' value='0' class='text-primary' style='text-align:right;' readonly></th>
											<th colspan='2' class='text-primary' style='text-align:left;'><b>สัญญา</b></th>
											<th width='12.5%' style='padding:2px;'><input type='text' id='1_SUMTOTPRC' value='0.00' class='text-primary' style='text-align:right;' readonly></th>
											<th width='12.5%' style='padding:2px;'><input type='text' id='1_SUMSMPAY' value='0.00' class='text-primary' style='text-align:right;' readonly></th>
											<th width='12.5%' style='padding:2px;'><input type='text' id='1_SUMBALANCE' value='0.00' class='text-primary' style='text-align:right;' readonly></th>
										</tr>
									</table>
								</div>
							</div>
						</div>
						
						<br>
						<b>การค้ำประกัน</b>
						<div class='row' style='height:30%;border:0.1px solid #bdbdbd;background-color:#eee;'>
							<div class='col-sm-12 col-xs-12' style='height:100%;'>
								<div id='insurance' class='dataTables_wrapper dt-bootstrap4 table-responsive' style='height:100%;width:100%;overflow:auto;'>
									<table id='dataTables-insurance' class='table table-bordered dataTable table-hover' stat='' aria-describedby='dataTable_info' cellspacing='0' width='calc(100% - 1px)'>
										<thead>
											<tr role='row' style='font-size:8pt;background-color:#666666;color:white;'>
												<th width='12.5%'>เลขที่สัญญา</th>
												<th width='12.5%'>รหัสผู้ซื้อ</th>
												<th width='12.5%'>ชื่อผู้ซื้อ</th>
												<th width='12.5%'>สกุลผู้ซื้อ</th>
												<th width='12.5%'>เลขตัวถัง</th>
												<th width='12.5%'>วันขาย</th>
												<th width='12.5%'>ล/น คงเหลือ</th>
											</tr>
										</thead>
										<tbody style='white-space:nowrap;background-color:white;font-size:9pt;'></tbody>
									</table>
								</div>
							</div>
						</div>
						<div class='row' style='border:0.1px solid #bdbdbd;background-color:#dedede;'>
							<div class='col-sm-12 col-xs-12' style='height:100%;'>	
								<div id='fix-insurance' style='width:100%;overflow:auto;'>
									<table id='insurance' width='calc(100% - 1px)'>
										<tr style='font-size:9pt;'>
											<th colspan='1' class='text-primary' style='text-align:center;'><b>รวม</b></th>
											<th width='14%' style='padding:2px;'><input type='text' id='1_COUNTCUSCOD_ISR' value='0' class='text-primary' style='text-align:right;' readonly></th>
											<th colspan='2' class='text-primary' style='text-align:left;'><b>สัญญา</b></th>
											<th width='12.5%' style='padding:2px;'><input type='text' id='1_SUMBALANCE_ISR' value='0.00' class='text-primary' style='text-align:right;' readonly></th>
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
	function getfromFinanceTab22(){
		$html ="
			<div class='tab-pane' name='tab22' style='height:calc(100vh - 260px);overflow:auto;'>
				<fieldset style='height:100%'>
					<div style='float:left;height:100%;' class='col-sm-12 col-xs-12'>
						<div class='row' style='border:0.1px dotted #999;'><br>
							<div class='row'>
								<div class='col-sm-10 col-xs-10 col-sm-offset-2' style='height:100%;'>	
									<div class='row'>
										<div class='col-sm-3'>
											<b>เลขที่สัญญา</b>
											<input type='text' id='2_CONTNO' class='text-primary' readonly>
										</div>
										<div class='col-sm-3'>
											<b>สาขา</b>
											<input type='text' id='2_LOCAT' class='text-primary' readonly>
										</div>
									</div>
								</div>
							</div><br>
						</div>
						<br>
						<b>รายละเอียดรถ</b>
						<div class='row' style='border:0.1px solid #bdbdbd;background-color:#dedede;'>
							<div class='row'>
								<div class='col-sm-12 col-xs-12' style='height:100%;'>
									<div class='row text-primary'>
										<div class='col-sm-3'>	
											เลขตัวถัง
											<input type='text' id='2_STRNO' class='form-control input-sm' readonly>
										</div>
										<div class='col-sm-3'>	
											เลขเครื่อง
											<input type='text' id='2_ENGNO' class='form-control input-sm' readonly>
										</div>
										<div class='col-sm-3'>
											ค่าทำทะเบียน
											<input type='text' id='2_NADDCOST' class='form-control input-sm' readonly>
										</div>
										<div class='col-sm-3'>
											เลขทะเบียน
											<input type='text' id='2_REGNO' class='form-control input-sm' readonly>
										</div>
										<div class='col-sm-3'>	
											วันจดทะเบียน
											<input type='text' id='2_REGYEAR' class='form-control input-sm' readonly>
										</div>
										<div class='col-sm-3'>	
											วันหมดอายุ
											<input type='text' id='2_REGEXP' class='form-control input-sm' readonly>
										</div>
										<div class='col-sm-3'>
											เลขประกันชั้น
											<input type='text' id='2_REGTYP' class='form-control input-sm' readonly>
										</div>
										<div class='col-sm-3'>	
											วันคุ้มครอง
											<input type='text' id='2_GARFRM' class='form-control input-sm' readonly>									
										</div>
										<div class='col-sm-3'>
											วันหมดอายุ
											<input type='text' id='2_GAREXP' class='form-control input-sm' readonly>
										</div>
										<div class='col-sm-3'>	
											เลข พ.ร.บ
											<input type='text' id='2_GARNO3' class='form-control input-sm' readonly>
										</div>
										<div class='col-sm-3'>	
											วันคุ้มครอง
											<input type='text' id='2_GAR3FRM' class='form-control input-sm' readonly>
										</div>
										<div class='col-sm-3'>	
											วันหมดอายุ
											<input type='text' id='2_GAR3EXP' class='form-control input-sm' readonly>
										</div>
										<div class='col-sm-3'>	
											ยี่ห้อ
											<input type='text' id='2_TYPE' class='form-control input-sm' readonly>
										</div>
										<div class='col-sm-3'>	
											รุ่น
											<input type='text' id='2_MODEL' class='form-control input-sm' readonly>
										</div>
										<div class='col-sm-3'>	
											แบบ
											<input type='text' id='2_BAAB' class='form-control input-sm' readonly>
										</div>
										<div class='col-sm-3'>	
											สี
											<input type='text' id='2_COLOR' class='form-control input-sm' readonly>
										</div>
										<div class='col-sm-3'>	
											ขนาด
											<input type='text' id='2_CC' class='form-control input-sm' readonly>
										</div>
										<div class='col-sm-3'>	
											สภาพ
											<input type='text' id='2_STAT' class='form-control input-sm text-danger' readonly>
										</div>
										<div class='col-sm-3'>	
											ปีที่ผลิต
											<input type='text' id='2_MANUYR' class='form-control input-sm' readonly>
										</div>
										<div class='col-sm-3'>	
											บริษัทเจ้าหนี้
											<input type='text' id='2_APNAME' class='form-control input-sm' readonly>
										</div>
										<div class='col-sm-3'>	
											ประเภทสินค้า
											<input type='text' id='2_GDESC' class='form-control input-sm' readonly>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class='row' id='SPECSCAR' style='height:80%;border:0.1px'>
							<br><b>อุปกรณ์เสริม</b>
							<div class='row' style='height:30%;border:0.1px solid #bdbdbd;background-color:#eee;'>
								<div class='col-sm-12 col-xs-12' style='height:100%;'>
									<div id='dataTable-fixed-accessory' class='dataTables_wrapper dt-bootstrap4 table-responsive' style='height:100%;width:100%;overflow:auto;'>
										<table id='dataTables-accessory' class='table table-bordered dataTable table-hover' stat='' aria-describedby='dataTable_info' cellspacing='0' width='calc(100% - 1px)'>
											<thead>
												<tr role='row' style='font-size:8pt;background-color:#666666;color:white;'>
													<th width='12.5%'>รหัสอุปกรณ์</th>
													<th width='12.5%'>ชื่ออุปกรณ์</th>
													<th width='12.5%'>ราคาต่อหน่วย</th>
													<th width='12.5%'>จำนวน</th>
													<th width='12.5%'>ราคารวม</th>
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
										<div class='col-sm-1' style='text-align:center;'>	
											<b>รวม</b>
										</div>
										<div class='col-sm-3'>	
											<div class='input-group'>
												<b><input type='text' id='2_COUNTOPTCODE' value='0' class='text-primary' style='text-align:right;' readonly></b>
												<span class='input-group-addon'><b>รายการ</b></span>
											</div>
										</div>
										<div class='col-sm-2 col-sm-offset-6' style='text-align:left;'>	
											<b><input type='text' id='2_SUMTOTPRC' value='0.00' class='text-primary' style='text-align:right;' readonly></b>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class='row' id='SPECSCAR_A' style='height:80%;border:0.1px'>
							<br><b>รายการรถ</b>
							<div class='row' style='height:30%;border:0.1px solid #bdbdbd;background-color:#eee;'>
								<div class='col-sm-12 col-xs-12' style='height:100%;'>
									<div id='dataTable-fixed-listcar' class='dataTables_wrapper dt-bootstrap4 table-responsive' style='height:100%;width:100%;overflow:auto;'>
										<table id='dataTables-listcar' class='table table-bordered dataTable table-hover' stat='' aria-describedby='dataTable_info' cellspacing='0' width='calc(100% - 1px)'>
											<thead>
												<tr role='row' style='font-size:8pt;background-color:#666666;color:white;'>
													<th width='12.5%'>เลขถัง</th>
													<th width='12.5%'>มูลค่าสินค้า</th>
													<th width='12.5%'>ภาษีมูลค่าเพิ่ม</th>
													<th width='12.5%'>ราคาขายรวมภาษี</th>
													<th width='12.5%'>หมายเหตุ</th>
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
										<div class='col-sm-1' style='text-align:center;'>	
											<b>รวม</b>
										</div>
										<div class='col-sm-3'>	
											<div class='input-group'>
												<input type='text' id='2_COUNTSTRNO' value='0' class='text-primary' style='text-align:right;' readonly>
												<span class='input-group-addon'><b>รายการ</b></span>
											</div>
										</div>
										<div class='col-sm-2 col-sm-offset-6' style='text-align:left;'>	
											<b><input type='text' id='2_SUMNPRICE' value='0.00' class='text-primary' style='text-align:right;' readonly></b>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</fieldset>
			</div>
		";
		return $html;
	}
	function getfromFinanceTab33(){
		$html ="
			<div class='tab-pane' name='tab33' style='height:calc(100vh - 260px);overflow:auto;'>
				<fieldset style='height:100%'>
					<div style='float:left;height:100%;' class='col-sm-12 col-xs-12'>
						<div class='row' style='border:0.1px dotted #999;'><br>
							<div class='row'>
								<div class='col-sm-10 col-xs-10 col-sm-offset-2' style='height:100%;'>	
									<div class='row'>
										<div class='col-sm-4'>
											<b>ณ วันที่</b>
											<input type='text' id='3_DATESEARCH' data-provide='datepicker' data-date-language='th-th' value='".$this->today('today')."'>
										</div>
									</div>
								</div>
							</div><br>
						</div>
						<br>
						<b>รายละเอียดสัญญา</b>
						<div class='row text-primary' style='border:0.1px solid #bdbdbd;background-color:#dedede;'>
							<div class='col-sm-12 col-xs-12'>
								<div class='col-sm-3'>	
									<div class='form-group'>
										เลขที่สัญญา
										<input type='text' id='3_CONTNO' class='form-control input-sm' readonly>
									</div>
								</div>
								<div class='col-sm-3'>	
									<div class='form-group'>
										สาขา
										<input type='text' id='3_LOCAT' class='form-control input-sm' readonly>
									</div>
								</div>
								<div class='col-sm-3'>	
									<div class='form-group'>
										วันทำสัญญา
										<input type='text' id='3_SDATE' class='form-control input-sm' readonly>
									</div>
								</div>
								<div class='col-sm-3'>	
									<div class='form-group'>
										ราคาขาย
										<input type='text' id='3_TOTPRC' class='form-control input-sm' readonly>
									</div>
								</div>
								<div class='col-sm-3'>	
									<div class='form-group'>
										ชำระแล้ว
										<input type='text' id='3_SMPAY' class='form-control input-sm' readonly>
									</div>
								</div>
								<div class='col-sm-3'>	
									<div class='form-group'>
										ล/น คงเหลือ
										<input type='text' id='3_REMAIN' class='form-control input-sm' readonly>
									</div>
								</div>
								<div class='col-sm-3'>	
									<div class='form-group'>
										ราคาจัดเช่าซื้อ
										<input type='text' id='3_NCSHPRC' class='form-control input-sm' readonly>
									</div>
								</div>
								<div class='col-sm-3'>	
									<div class='form-group'>
										ราคาเช่าซื้อ
										<input type='text' id='3_NPRICE' class='form-control input-sm' readonly>
									</div>
								</div>
								<div class='col-sm-3'>	
									<div class='form-group'>
										อัตราดอกเบี้ย
										<input type='text' id='3_INTRT' class='form-control input-sm' readonly>
									</div>
								</div>
								<div class='col-sm-3'>	
									<div class='form-group'>
										เช็ครอเรียกเก็บ
										<input type='text' id='3_SMCHQ' class='form-control input-sm' readonly>
									</div>
								</div>
								<div class='col-sm-3'>	
									<div class='form-group'>
										วันชำระล่าสุด
										<input type='text' id='3_LPAYD' class='form-control input-sm' readonly>
									</div>
								</div>
								<div class='col-sm-3'>	
									<div class='form-group'>
										จ.น วันชำระล่าสุด
										<input type='text' id='3_LPAYA' class='form-control input-sm' readonly>
									</div>
								</div>
								<div class='col-sm-3'>	
									<div class='form-group'>
										เงินดาวน์
										<input type='text' id='3_TOTDWN' class='form-control input-sm' readonly>
									</div>
								</div>
								<div class='col-sm-3'>	
									<div class='form-group'>
										ชำระดาวน์แล้ว
										<input type='text' id='3_PAYDWN' class='form-control input-sm' readonly>
									</div>
								</div>
								<div class='col-sm-3'>	
									<div class='form-group'>
										ค้างชำระดาวน์
										<input type='text' id='3_KDWN' class='form-control input-sm' readonly>
									</div>
								</div>
								<div class='col-sm-3'>	
									<div class='form-group'>
										ค้างค่างวด
										<b><input type='text' id='3_EXP_AMT' class='form-control input-sm text-danger' readonly></b>
									</div>
								</div>
								<div class='col-sm-2'>	
									<div class='form-group'>
										ค่างวดที่
										<input type='text' id='3_EXP_FRM' class='form-control input-sm' readonly>
									</div>
								</div>
								<div class='col-sm-2'>	
									<div class='form-group'>
										ถึง
										<input type='text' id='3_EXP_TO' class='form-control input-sm' readonly>
									</div>
								</div>
								<div class='col-sm-2'>	
									<div class='form-group'>
										ค้างค่างวด
										<b><input type='text' id='3_EXP_PRD' class='form-control input-sm text-danger' readonly></b>
									</div>
								</div>
								<div class='col-sm-2'>	
									<div class='form-group'>
										ถึงดิวงวดที่
										<input type='text' id='3_' class='form-control input-sm' readonly>
									</div>
								</div>
								<div class='col-sm-2'>	
									<div class='form-group'>
										วันครบกำหนด
										<input type='text' id='3_' class='form-control input-sm' readonly>
									</div>
								</div>
								<div class='col-sm-2'>	
									<div class='form-group'>
										จ.น เงินค่างวด
										<input type='text' id='3_' class='form-control input-sm' readonly>
									</div>
								</div>
								<div class='col-sm-3'>	
									<div class='form-group'>
										รายงาน ณ วันที่
										<input type='text' id='' class='form-control input-sm' value='".$this->today('today')."' readonly>
									</div>
								</div>
								<div class='col-sm-3'>	
									<div class='form-group'>
										สถานะสัญญา
										<input type='text' id='3_CONTSTAT' class='form-control input-sm' readonly>
									</div>
								</div>
								<div class='col-sm-3'>	
									<div class='form-group'>
										ค้างค่าอื่นๆ
										<input type='text' id='3_OTHR' class='form-control input-sm' readonly>
									</div>
								</div>
								<div class='col-sm-3'>	
									<div class='form-group'>
										วิธีชำระค่างวด
										<input type='text' id='3_PAYTYP' class='form-control input-sm' readonly>
									</div>
								</div>
								<div class='col-sm-3'>	
									<div class='form-group'>
										จ.น. งวดทั้งหมด
										<input type='text' id='3_T_NOPAY' class='form-control input-sm' readonly>
									</div>
								</div>
								
								<div class='col-sm-3'>	
									<div class='form-group'>
										วิธีคำนวณเบี้ยปรับ
										<input type='text' id='3_CALINT' class='form-control input-sm' readonly>
									</div>
								</div>
								<div class='col-sm-3'>	
									<div class='form-group'>
										วิธีคำนวณส่วนลดตัดสด
										<input type='text' id='3_CALDSC' class='form-control input-sm' readonly>
									</div>
								</div>
								<div class='col-sm-3'>	
									<div class='form-group'>
										อัตราเบี้ยปรับล่าช้า
										<div class='input-group'>
											<input type='text' id='3_DELYRT' class='form-control input-sm' readonly>
											<span class='input-group-addon'>%</span>
										</div>
									</div>
								</div>
								<div class='col-sm-3'>	
									<div class='form-group'>
										ล่าช้าได้ไม่เกิน
										<div class='input-group'>
											<input type='text' id='3_DLDAY' class='form-control input-sm' readonly>
											<span class='input-group-addon'>วัน</span>
										</div>
									</div>
								</div>
								<div class='col-sm-3'>	
									<div class='form-group'>
										ที่อยู่ที่ใช้พิมพ์สัญญา
										<input type='text' id='3_ADDRNO' class='form-control input-sm' readonly>
									</div>
								</div>
								<div class='col-sm-3'>	
									<div class='form-group'>
										พนักงานสินเชื่อ
										<input type='text' id='3_CHECKER_USE' class='form-control input-sm' readonly>
									</div>
								</div>
								<div class='col-sm-3'>	
									<div class='form-group'>
										Billcoll
										<input type='text' id='3_BILLCOLL' class='form-control input-sm' readonly>
									</div>
								</div>
								<div class='col-sm-3'>	
									<div class='form-group'>
										Checker
										<input type='text' id='3_CHECKER' class='form-control input-sm' readonly>
									</div>
								</div>
								<div class='col-sm-3'>	
									<div class='form-group'>
										เกรดสัญญา
										<b><input type='text' id='3_GRDCOD' class='form-control input-sm text-danger' readonly></b>
									</div>
								</div>
								
								<div class='col-sm-6 col-xs-6'>	
									<div class='form-group'>
										<br>
										<p id='statuscar' style='text-align:center;color:red;' class='text-justify'><b></b></p>
									</div>
								</div>
								
								<div class='col-sm-10'>	
									<div class='form-group'>
										หมายเหตุ
										<input type='text' id='3_MEMO1' class='form-control input-sm' readonly>
									</div>
								</div>
							</div>
						</div>
						<div class='col-sm-12 col-xs-12'>
							<div class=' col-sm-6 col-xs-6'>
								<br><br>
								<button id='btncostcar' class='btn btn-primary btn-outline btn-block' style='font-size:10pt'><span class='glyphicon glyphicon-calendar'><b>ตารางค่างวด</b></span></button>
							</div>
							<div class=' col-sm-6 col-xs-6'>
								<br><br>
								<button id='btndiscount' class='btn btn-primary btn-outline btn-block' style='font-size:10pt'><span class='glyphicon glyphicon-list-alt'><b>ส่วนลดตัดสด</b></span></button>
							</div>
						</div>
					</div>
				</fieldset>
			</div>
		";
		return $html;
	}
	function getfromFinanceTab44(){
		$html ="
			<div class='tab-pane' name='tab44' style='height:calc(100vh - 260px);overflow:auto;'>
				<fieldset style='height:100%'>
					<div style='float:left;height:100%;' class='col-sm-12 col-xs-12'>
						<b>รายการรับชำระเงิน</b>
						<div class='row' style='border:0.1px dotted #999;'><br>
							<div class='row'>
								<div class='col-sm-10 col-xs-10 col-sm-offset-2' style='height:100%;'>	
									<div class='row'>
										<div class='col-sm-3'>
											<b>เลขที่สัญญา</b>
											<input type='text' id='4_CONTNO' 	class='text-primary' style='text-align:right;' readonly>
										</div>
										<div class='col-sm-2'>
											<b>สาขา</b>
											<input type='text' id='4_LOCAT' 	class='text-primary' style='text-align:right;' readonly>
										</div>
									</div>
								</div>
							</div><br>
						</div><br>
						<div class='row' style='height:30%;border:0.1px solid #bdbdbd;background-color:#eee;'>
							<div class='col-sm-12 col-xs-12' style='height:100%;'>
								<div id='payment' class='dataTables_wrapper dt-bootstrap4 table-responsive' style='height:100%;width:100%;overflow:auto;'>
									<table id='dataTables-payment' class='table table-bordered dataTable table-hover' stat='' aria-describedby='dataTable_info' cellspacing='0' width='calc(100% - 1px)'>
										<thead>
											<tr role='row' style='font-size:8pt;background-color:#666666;color:white;'>
												<th width='12.5%'>เลขที่ใบรับ</th>
												<th width='12.5%'>วันที่รับ</th>
												<th width='12.5%'>ชำระโดย</th>
												<th width='12.5%'>ชำระค่า</th>
												<th width='12.5%'>เลขที่เช็ค</th>
												<th width='12.5%'>วันที่เช็ค</th>
												<th width='12.5%'>ยอดหักลูกหนี้</th>
												<th width='12.5%'>ส่วนลด</th>
												<th width='12.5%'>ชำระเบี้ยปรับ</th>
												<th width='12.5%'>ส่วนลดเบี้ยปรับ</th>
												<th width='12.5%'>ยอดรับสุทธิ</th>
												<th width='12.5%'>เลขที่ใบเสร็จ</th>
												<th width='12.5%'>วันที่ใบเสร็จ</th>
												<th width='12.5%'>วันที่ตัด ล/น</th>
												<th width='12.5%'>สถานะเอกสาร</th>
												<th width='12.5%'>ชำระงวดที่</th>
												<th width='12.5%'>ถึงงวดที่</th>
												<th width='12.5%'>วันบันทึกรายการ</th>
												<th width='12.5%'>รหัสลูกค้า</th>
												<th width='12.5%'>เลขที่สัญญา</th>
											</tr>
										</thead>
										<tbody style='white-space:nowrap;background-color:white;font-size:9pt;'></tbody>
									</table>
								</div>
							</div>
						</div>
						<div class='row' id=''>
							<br><b>Summary</b>
							<b class='text-primary'><div class='col-sm-12 col-xs-12' style='height:30%;border:0.1px solid #bdbdbd;background-color:#eee;'>
								<div class='col-sm-2 text-primary'>
									รวม
									<div class='input-group'>
										<input type='text' id='4_COUNTTMBILL' value='0' class='form-control input-sm text-primary' style='text-align:right;' readonly>
										<span class='input-group-addon'>รายการ</span>
									</div>
								</div>
								<div class='col-sm-2'>	
									<div class='form-group'>
										ราคาขาย
										<input type='text' id='4_TOTPRC' value='0.00' class='form-control input-sm text-primary' style='text-align:right;' readonly>
									</div>
								</div>
								<div class='col-sm-2'>	
									<div class='form-group'>
										ยอดลูกหนี้คงเหลือ
										<input type='text' id='4_REMAIN' value='0.00' class='form-control input-sm text-primary' style='text-align:right;' readonly>
									</div>
								</div>
								<div class='col-sm-2'>	
									<div class='form-group'>
										ยอดคงเหลือหักเช็ค
										<input type='text' id='4_REMAIN_1' value='0.00' class='form-control input-sm text-primary' style='text-align:right;' readonly>
									</div>
								</div>
								<div class='col-sm-2'>	
									<div class='form-group'>
										ยอดชำระ
										<input type='text' id='4_SMPAY' value='0.00' class='form-control input-sm text-primary' style='text-align:right;' readonly>
									</div>
								</div>
								<div class='col-sm-2'>	
									<div class='form-group'>
										ยอดเช็ครอเรียกเก็บ
										<input type='text' id='4_SMCHQ' value='0.00' class='form-control input-sm text-primary' style='text-align:right;' value='20000000' readonly>
									</div>
								</div>
							</b></div>
						</div>
					</div>
				</fieldset>
			</div>
		";
		return $html;
	}
	function getfromFinanceTab55(){
		$html ="
			<div class='tab-pane' name='tab55' style='height:calc(100vh - 260px);overflow:auto;'>
				<fieldset style='height:100%'>
					<div style='float:left;height:100%;' class='col-sm-12 col-xs-12'>
						<b>ผู้ซื้อ</b>
						<div class='row' style='border:0.1px solid #bdbdbd;background-color:#dedede;'>
							<div class='col-sm-12 col-xs-12'>
								<div class='col-sm-4'>	
									<div class='form-group text-primary'>
										รหัสลูกค้า
										<input type='text' id='5_CUSCOD' class='form-control input-sm' readonly>
									</div>
								</div>
								<div class='col-sm-2'>	
									<div class='form-group text-primary'>
										กลุ่ม
										<input type='text' id='5_GROUP1' class='form-control input-sm' readonly>
									</div>
								</div>
								<div class='col-sm-2'>	
									<div class='form-group text-primary'>
										เกรด
										<input type='text' id='5_GRADE' class='form-control input-sm' readonly>
									</div>
								</div>
								<div class='col-sm-4'>	
									<div class='form-group text-primary'>
										วันเกิด
										<input type='text' id='5_BIRTHDT' class='form-control input-sm' readonly>
									</div>
								</div>
								<div class='col-sm-4'>	
									<div class='form-group text-primary'>
										ชื่อเล่น
										<input type='text' id='5_NICKNM' class='form-control input-sm' readonly>
									</div>
								</div>
								<div class='col-sm-4'>	
									<div class='form-group text-primary'>
										ออกโดย
										<input type='text' id='5_ISSUBY' class='form-control input-sm' readonly>
									</div>
								</div>
								<div class='col-sm-4'>	
									<div class='form-group text-primary'>
										วันที่ออกบัตร
										<input type='text' id='5_ISSUDT' class='form-control input-sm' readonly>
									</div>
								</div>
								<div class='col-sm-4'>	
									<div class='form-group text-primary'>
										วันหมดอายุ
										<input type='text' id='5_EXPDT' class='form-control input-sm' readonly>
									</div>
								</div>
								<div class='col-sm-4'>	
									<div class='form-group text-primary'>
										อายุ
										<input type='text' id='5_AGE' class='form-control input-sm' readonly>
									</div>
								</div>
								<div class='col-sm-4'>	
									<div class='form-group text-primary'>
										อาชีพ
										<input type='text' id='5_OCCUP' class='form-control input-sm' readonly>
									</div>
								</div>
								<div class='col-sm-4'>	
									<div class='form-group text-primary'>
										ที่ทำงาน
										<input type='text' id='5_OFFIC' class='form-control input-sm' readonly>
									</div>
								</div>
								<div class='col-sm-4'>	
									<div class='form-group text-primary'>
										วงเงินเครดิต
										<input type='text' id='5_MAXCRED' class='form-control input-sm' readonly>
									</div>
								</div>
								<div class='col-sm-4'>	
									<div class='form-group text-primary'>
										เลขที่สมาชิก
										<input type='text' id='5_YINCOME' class='form-control input-sm' readonly>
									</div>
								</div>
								<div class='col-sm-4'>	
									<div class='form-group text-primary'>
										รายได้ต่อเดือน
										<input type='text' id='5_MREVENU' class='form-control input-sm' readonly>
									</div>
								</div>
								<div class='col-sm-4'>	
									<div class='form-group text-primary'>
										รายได้ต่อปี
										<input type='text' id='5_YREVENU' class='form-control input-sm' readonly>
									</div>
								</div>
								<div class='col-sm-4'>	
									<div class='form-group text-primary'>
										ที่อยู่ที่ใช้ส่งเอกสาร
										<input type='text' id='5_ADDRNO3' class='form-control input-sm' readonly>
									</div>
								</div>
								<div class='col-sm-10'>	
									<div class='form-group text-primary'>
										หมายเหตุ
										<input type='text' id='5_MEMO1' class='form-control input-sm' readonly>
									</div>
								</div>
							</div>
						</div><br>
						<b>ที่อยู่ผู้ซื้อ</b>
						<div class='row' style='height:30%;border:0.1px solid #bdbdbd;background-color:#eee;'>
							<div class='col-sm-12 col-xs-12' style='height:100%;'>
								<div id='dataTable-fixed-addrprice' class='dataTables_wrapper dt-bootstrap4 table-responsive' style='height:100%;width:100%;overflow:auto;'>
									<table id='dataTables-addrprice' class='table table-bordered dataTable table-hover' stat='' aria-describedby='dataTable_info' cellspacing='0' width='calc(100% - 1px)'>
										<thead>
											<tr role='row' style='font-size:8pt;background-color:#666666;color:white;'>
												<th width='12.5%'>ลำดับที่อยู่</th>
												<th width='12.5%'>บ้านเลขที่</th>
												<th width='12.5%'>ถนน</th>
												<th width='12.5%'>ตำบล</th>
												<th width='12.5%'>อำเภอ</th>
												<th width='12.5%'>จังหวัด</th>
												<th width='12.5%'>รหัสไปรษณีย์</th>
												<th width='12.5%'>โทรศัพท์</th>
											</tr>
										</thead>
										<tbody style='white-space:nowrap;background-color:white;font-size:9pt;'></tbody>
									</table>
									<div class='col-sm-12'>	
										<div class='form-group text-primary'>
											หมายเหตุ
											<input type='text' id='5_MEMO1ADR' class='form-control input-sm' readonly>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</fieldset>
			</div>
		";
		return $html;
	}
	function getfromFinanceTab66(){
		$html ="
			<div class='tab-pane' name='tab66' style='height:calc(100vh - 260px);overflow:auto;'>
				<fieldset style='height:100%'>
					<div style='float:left;height:100%;' class='col-sm-12 col-xs-12'>
						<b>รายการรับชำระเงิน</b>
						<div class='row' style='border:0.1px dotted #999;'><br>
							<div class='row'>
								<div class='col-sm-10 col-xs-10 col-sm-offset-2' style='height:100%;'>	
									<div class='row'>
										<div class='col-sm-3'>
											<b>เลขที่สัญญา</b>
											<input type='text' id='6_CONTNO' class='text-primary' style='text-align:right;' readonly>
										</div>
										<div class='col-sm-2'>
											<b>สาขา</b>
											<input type='text' id='6_LOCAT' class='text-primary' style='text-align:right;' readonly>
										</div>
									</div>
								</div>
							</div><br>
						</div><br>
						<b>รายชื่อผู้ค้ำประกัน</b>
						<div class='row' style='height:30%;border:0.1px solid #bdbdbd;background-color:#eee;'>
							<div class='col-sm-12 col-xs-12' style='height:100%;'>
								<div id='dataTable-fixed-supporter' class='dataTables_wrapper dt-bootstrap4 table-responsive' style='height:100%;width:100%;overflow:auto;'>
									<table id='dataTables-supporter' class='table table-bordered dataTable table-hover' stat='' aria-describedby='dataTable_info' cellspacing='0' width='calc(100% - 1px)'>
										<thead>
											<tr role='row' style='font-size:8pt;background-color:#666666;color:white;'>
												<th width='12.5%'>ผู้ค้ำคนที่</th>
												<th width='12.5%'>รหัสลูกค้า</th>
												<th width='12.5%'>ชื่อ</th>
												<th width='12.5%'>นามสกุล</th>
												<th width='12.5%'>ความสัมพันธ์</th>
											</tr>
										</thead>
										<tbody style='white-space:nowrap;background-color:white;font-size:9pt;'></tbody>
									</table>
								</div>
							</div>
						</div><br>
						<b>ผู้ค้ำประกัน</b>
						<div class='row' style='border:0.1px solid #bdbdbd;background-color:#dedede;'>
							<div class='col-sm-12 col-xs-12'>
								<div class='col-sm-1'>	
									<div class='form-group text-primary'>
										กลุ่ม
										<input type='text' id='6_GROUP1' class='form-control input-sm' readonly>
									</div>
								</div>
								<div class='col-sm-1'>	
									<div class='form-group text-primary'>
										เกรด
										<input type='text' id='6_GRADE' class='form-control input-sm' readonly>
									</div>
								</div>
								<div class='col-sm-2'>	
									<div class='form-group text-primary'>
										วันเกิด
										<input type='text' id='6_BIRTHDT' class='form-control input-sm' readonly>
									</div>
								</div>
								<div class='col-sm-2'>	
									<div class='form-group text-primary'>
										ชื่อเล่น
										<input type='text' id='6_NICKNM' class='form-control input-sm' readonly>
									</div>
								</div>
								<div class='col-sm-2'>	
									<div class='form-group text-primary'>
										บัตรประจำตัว
										<input type='text' id='6_IDCARD' class='form-control input-sm' readonly>
									</div>
								</div>
								<div class='col-sm-2'>	
									<div class='form-group text-primary'>
										เลขที่
										<input type='text' id='6_IDNO' class='form-control input-sm' readonly>
									</div>
								</div>
								<div class='col-sm-2'>	
									<div class='form-group text-primary'>
										ออกโดย
										<input type='text' id='6_ISSUBY' class='form-control input-sm' readonly>
									</div>
								</div>
								<div class='col-sm-2'>	
									<div class='form-group text-primary'>
										วันออกบัตร
										<input type='text' id='6_ISSUDT' class='form-control input-sm' readonly>
									</div>
								</div>
								<div class='col-sm-2'>	
									<div class='form-group text-primary'>
										วันหมดอายุ
										<input type='text' id='6_EXPDT' class='form-control input-sm' readonly>
									</div>
								</div>
								<div class='col-sm-2'>	
									<div class='form-group text-primary'>
										อายุ
										<input type='text' id='6_AGE' class='form-control input-sm' readonly>
									</div>
								</div>
								<div class='col-sm-2'>	
									<div class='form-group text-primary'>
										อาชีพ
										<input type='text' id='6_OCCUP' class='form-control input-sm' readonly>
									</div>
								</div>
								<div class='col-sm-2'>	
									<div class='form-group text-primary'>
										ที่ทำงาน
										<input type='text' id='6_OFFIC' class='form-control input-sm' readonly>
									</div>
								</div>
								<div class='col-sm-2'>	
									<div class='form-group text-primary'>
										วงเงินเครดิต
										<input type='text' id='6_MAXCRED' class='form-control input-sm' readonly>
									</div>
								</div>
								<div class='col-sm-2'>	
									<div class='form-group text-primary'>
										เลขที่สมาชิก
										<input type='text' id='6_YINCOME' class='form-control input-sm' readonly>
									</div>
								</div>
								<div class='col-sm-2'>	
									<div class='form-group text-primary'>
										รายได้ต่อเดือน
										<input type='text' id='6_MREVENU' class='form-control input-sm' readonly>
									</div>
								</div>
								<div class='col-sm-2'>	
									<div class='form-group text-primary'>
										รายได้พิเศษต่อปี
										<input type='text' id='6_YREVENU' class='form-control input-sm' readonly>
									</div>
								</div>
								<div class='col-sm-2'>	
									<div class='form-group text-primary'>
										ที่อยู่ที่ส่งเอกสาร
										<input type='text' id='6_ADDRNO3' class='form-control input-sm' readonly>
									</div>
								</div>
								<div class='col-sm-4'>	
									<div class='form-group text-primary'>
										หมายเหตุ
										<input type='text' id='6_MEMO1' class='form-control input-sm' readonly>
									</div>
								</div>
							</div>
						</div><br>
						<b>ที่อยู่ผู้ค้ำ</b>
						<div class='row' style='height:30%;border:0.1px solid #bdbdbd;background-color:#eee;'>
							<div class='col-sm-12 col-xs-12' style='height:100%;'>
								<div id='dataTable-fixed-addrspt' class='dataTables_wrapper dt-bootstrap4 table-responsive' style='height:100%;width:100%;overflow:auto;'>
									<table id='dataTables-addrspt' class='table table-bordered dataTable table-hover' stat='' aria-describedby='dataTable_info' cellspacing='0' width='calc(100% - 1px)'>
										<thead>
											<tr role='row' style='font-size:8pt;background-color:#666666;color:white;'>
												<th width='12.5%'>ลำดับที่อยู่</th>
												<th width='12.5%'>บ้านเลขที่</th>
												<th width='12.5%'>ถนน</th>
												<th width='12.5%'>ตำบล</th>
												<th width='12.5%'>อำเภอ</th>
												<th width='12.5%'>จังหวัด</th>
												<th width='12.5%'>รหัสไปรษณีย์</th>
												<th width='12.5%'>โทรศัพท์</th>
											</tr>
										</thead>
										<tbody style='white-space:nowrap;background-color:white;font-size:9pt;'></tbody>
									</table>
									<div class='col-sm-12'>	
										<div class='form-group text-primary'>
											หมายเหตุ
											<input type='text' id='6_MEMO1ADR' class='form-control input-sm' readonly>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</fieldset>
			</div>
		";
		return $html;
	}
	function getfromFinanceTab77(){
		$html ="
			<div class='tab-pane' name='tab77' style='height:calc(100vh - 260px);overflow:auto;'>
				<fieldset style='height:100%'>
					<div style='float:left;height:100%;' class='col-sm-12 col-xs-12'>
						<div class='row' style='border:0.1px dotted #999;'><br>
							<div class='row'>
								<div class='col-sm-10 col-xs-10 col-sm-offset-2' style='height:100%;'>	
									<div class='row'>
										<div class='col-sm-3'>
											<b>เลขที่สัญญา</b>
											<input type='text' id='7_CONTNO' class='text-primary' style='text-align:right;' readonly>
										</div>
										<div class='col-sm-2'>
											<b>สาขา</b>
											<input type='text' id='7_LOCAT' class='text-primary' style='text-align:right;' readonly>
										</div>
									</div>
								</div>
							</div><br>
						</div><br>
						<div class='row' style='height:40%;border:0.1px solid #bdbdbd;background-color:#eee;'>
							<div class='col-sm-12 col-xs-12' style='height:100%;'>
								<div id='dataTable-fixed-compact' class='dataTables_wrapper dt-bootstrap4 table-responsive' style='height:100%;width:100%;overflow:auto;'>
									<table id='dataTables-compact' class='table table-bordered dataTable table-hover' stat='' aria-describedby='dataTable_info' cellspacing='0' width='calc(100% - 1px)'>
										<thead>
											<tr role='row' style='font-size:8pt;background-color:#666666;color:white;'>
												<th width='12.5%'>วันที่เปลี่ยนสถานะ</th>
												<th width='12.5%'>จากสถานะ</th>
												<th width='12.5%'>เป็นสถานะ</th>
												<th width='12.5%'>จากพนักงานเก็บเงิน</th>
												<th width='12.5%'>เป็นพนักงานเก็บเงิน</th>
												<th width='12.5%'>ผู้เปลี่ยน</th>
											</tr>
										</thead>
										<tbody style='white-space:nowrap;background-color:white;font-size:9pt;'></tbody>
									</table>
									<div class='col-sm-12'>	
										<div class='form-group text-primary'>
											หมายเหตุ
											<textarea readonly></textarea>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class='col-sm-12 col-xs-12'>
							<div class=' col-sm-12 col-xs-12'>
								<br><br>
								<button id='btnprintT7' class='btn btn-primary btn-outline btn-block' style='font-size:10pt'><span class='glyphicon glyphicon-print'><b>พิมพ์</b></span></button>
							</div>
						</div>
					</div>
				</fieldset>
			</div>
		";
		return $html;
	}
	function getfromFinanceTab88(){
		$html ="
			<div class='tab-pane' name='tab88' style='height:calc(100vh - 260px);overflow:auto;'>
				<fieldset style='height:100%'>
					<div style='float:left;height:100%;' class='col-sm-12 col-xs-12'>
						<div class='row' style='border:0.1px dotted #999;'><br>
							<div class='row'>
								<div class='col-sm-10 col-xs-10 col-sm-offset-2' style='height:100%;'>	
									<div class='row'>
										<div class='col-sm-3'>
											<b>เลขที่สัญญา</b>
											<input type='text' id='8_CONTNO' class='text-primary' style='text-align:right;' readonly>
										</div>
									</div>
								</div>
							</div><br>
						</div><br>
						<b>การซื้อ</b>
						<div class='row' style='height:30%;border:0.1px solid #bdbdbd;background-color:#eee;'>
							<div class='col-sm-12 col-xs-12' style='height:100%;'>
								<div id='dataTable-fixed-debtors' class='dataTables_wrapper dt-bootstrap4 table-responsive' style='height:100%;width:100%;overflow:auto;'>
									<table id='dataTables-debtors' class='table table-bordered dataTable table-hover' stat='' aria-describedby='dataTable_info' cellspacing='0' width='calc(100% - 1px)'>
										<thead>
											<tr role='row' style='font-size:8pt;background-color:#666666;color:white;'>
												<th width='12.5%'>เลขสัญญาลูกหนี้อื่น</th>
												<th width='12.5%'>เลขที่สัญญา</th>
												<th width='12.5%'>สาขา</th>
												<th width='12.5%'>ยอดตั้งลูกหนี้</th>
												<th width='12.5%'>ชำระแล้ว</th>
												<th width='12.5%'>ล/น คงเหลือ</th>
												<th width='12.5%'>เช็ครอเรียกเก็บ</th>
												<th width='12.5%'>ยอดคงเหลือหักเช็ค</th>
												<th width='12.5%'>ค้างชำระค่า</th>
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
									<div class='col-sm-1' style='text-align:center;'>	
										<b>รวม</b>
									</div>
									<div class='col-sm-2'>	
										<b><input type='text' id='8_SUMTOTPRC' value='0.00' class='text-primary' style='text-align:right;' readonly></b>
									</div>
									<div class='col-sm-2 style='text-align:left;'>	
										<b><input type='text' id='8_SUMSMPAY' value='0.00' class='text-primary' style='text-align:right;' readonly></b>
									</div>
									<div class='col-sm-2 style='text-align:left;'>	
										<b><input type='text' id='8_SUMBALANC' value='0.00' class='text-primary' style='text-align:right;' readonly></b>
									</div>
									<div class='col-sm-2 style='text-align:left;'>	
										<b><input type='text' id='8_SUMSMCHQ' value='0.00' class='text-primary' style='text-align:right;' readonly></b>
									</div>
									<div class='col-sm-2 style='text-align:left;'>	
										<b><input type='text' id='8_SUMTKANG' value='0.00' class='text-primary' style='text-align:right;' readonly></b>
									</div>
								</div>
							</div>
						</div>
					</div>
				</fieldset>
			</div>
		";
		return $html;
	}
	function getfromSaveMessage(){
		$userid = !isset($_POST['userid']) ? '' : $_POST['userid'];
		$html = "
			<div class='k_Message' style='width:800px;height:530px;overflow:auto;background-color:white;'>
				<fieldset style='height:100%'>
					<div style='float:left;height:100%;' class='col-sm-12 col-xs-12'>
						<div id='col1' class='row'>
							<div class='col-sm-12 col-xs-12' style='background-color:#2745ef; border:5px solid white:50px;height:65px;text-align:center;font-size:14pt;color:white;font-weight:bold;'>	
								<br>บันทึกข้อความเตือน<br>
							</div>
						</div>
						<div id='col2' class='row'>
							<div class='col-sm-12 col-xs-12' style='background-color:#f5301c; border:5px solid white:50px;height:65px;text-align:center;font-size:14pt;color:white;font-weight:bold;'>	
								<br>บันทึกข้อความเตือน<br>
							</div>
						</div><br>
						<div class='row'>
							<div class='col-sm-12 col-xs-12' align='right'>	
								<img id='DISCRIPTION' src='../public/images/manual-icon.png' style='width:30px;height:30px;cursor:pointer;filter: contrast(100%);'>
							</div>
						</div>
						<br>
						<div class='row col-sm-10 col-xs-10 col-sm-offset-1' style='border:1px dotted #c2c2c2;'><br>
							<div class='row'>
								<div class='col-sm-10 col-xs-10 col-sm-offset-2' style='height:100%;'>	
									<div class='row'>
										<div class='col-sm-5 text-primary'>
											เลขที่สัญญา
											<div class='input-group'>
												<input type='text' id='add_contno' class='form-control input-sm' placeholder='เลขที่สัญญา' >
												<span class='input-group-btn'>
													<button id='add_contno_removed' class='btn btn-danger btn-sm' type='button'>
														<span class='glyphicon glyphicon-remove' aria-hidden='true'></span>
													</button>
												</span>
											</div>	
										</div>
										<div class='col-sm-5 text-primary'>
											วันที่บันทึก
											<input type='text' id='DATESAVE' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' value=''>
										</div>
									</div>
								</div>
							</div>
							<div class='row'>
								<div class='col-sm-10 col-xs-10 col-sm-offset-2' style='height:100%;'>	
									<div class='row'>
										<div class='col-sm-5 text-primary'>
											วันที่เริ่มเตือน
											<input type='text' id='DATESTART' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' value=''>
										</div>
										<div class='col-sm-5 text-primary'>
											วันที่สิ้นสุดการเตือน
											<input type='text' id='DATEENG' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' value=''>
										</div>
									</div>
								</div>
							</div><br>
						</div><br>
						
						<div  class='row col-sm-10 col-xs-10 col-sm-offset-1'><br>
							<div class='row'>
								<div class='col-sm-10 col-xs-10 col-sm-offset-2' style='height:100%;'>	
									<div class='row'>
										<div class='col-sm-10 text-primary'>
											บันทึกข้อความที่จะแสดงเตือน
											<textarea type='text' id='MSGMEMO' rows='9' cols='10' class='form-control input-sm' style='white:100px;height:100px; font-size:10pt'></textarea>
										</div>
									</div>
								</div>
							</div><br>
						</div>
						
						<div class='row col-sm-10 col-xs-10 col-sm-offset-1'>
							<div class='row'>
								<div class='col-sm-12 col-xs-12 col-sm-offset-1' style='height:100%;'>	
									<div class='row'>
										<div class='col-sm-5'>
											<div class='radio-inline  text-danger' style='margin-top:-5px'>
												<label>
													<input id='N_text' type='radio' class='choice' name='edit' value='editno'>ผู้อ่านแก้ไขข้อความไม่ได้ (สีแดง)						
												</label>
											</div>
										</div>
										<div class='col-sm-5 text-primary'>
											<div class='radio-inline text-primary' style='margin-top:-5px'>
												<label>
													<input id='Y_text' type='radio' class='choice' name='edit' value='edityes' checked>ผู้อ่านแก้ไขข้อความได้ (สีน้ำเงิน)	
												</label>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class='row col-sm-10 col-xs-10 col-sm-offset-2'><br>
							<div class='row'>
								<div class='col-sm-10 col-xs-10 col-sm-offset-1' style='height:100%;'>	
									<div class='row'>
										<div class='col-sm-2 text-primary'>
											<br>
											<button id='btnmsgI' class='btn btn-info btn-sm' style='width:100%'><span class=''><b>เพิ่ม</b></span></button>
										</div>
										<div class='col-sm-2 text-primary'>
											<br>
											<button id='btnmsgD' class='btn btn-danger btn-sm' style='width:100%'><span class=''><b>ลบ</b></span></button>
										</div>
										<div class='col-sm-2 text-primary'>
											<br>
											<button id='btnmsgQ' class='btn btn-primary btn-sm' style='width:100%'><span class=''><b>สอบถาม</b></span></button>
										</div>
										<div class='col-sm-2 text-primary'>
											<br>
											<button id='btnmsgS' class='btn btn-success btn-sm' style='width:100%'><span class=''><b>บันทึก</b></span></button>
										</div>
										<div class='col-sm-2 text-primary'>
											<br>
											<button id='btnmsgC' class='btn btn-warning btn-sm' style='width:100%'><span class=''><b>ยกเลิก</b></span></button>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div><br>
				</fieldset>
			</div>
		";
		$response = array("html"=>$html);
		$response['DATESAVE']  = $this->today('today');
		$response['DATESTART'] = $this->today('today');
		$response['DATEENG']   = $this->today('endofmonth');
		$response['userid'] = $userid;
		echo json_encode($response);
	}
	function msgSave(){
		$arrs = array();
		$arrs['CONTNO']    = $_POST['CONTNO'];
		$arrs['DATESAVE']  = $this->Convertdate(1,$_POST['DATESAVE']);
		$arrs['DATESTART'] = $this->Convertdate(1,$_POST['DATESTART']);
		$arrs['DATEENG']   = $this->Convertdate(1,$_POST['DATEENG']);
		$arrs['MSGMEMO']   = $_POST['MSGMEMO'];
		$arrs['choice']    = $_POST['choice'];
		//echo $choice; exit;
		$response = array();
		if($arrs['CONTNO'] == ""){
			$response["error"] = true;
			$response["msg"]   = 'กรุณาระบุเลขที่สัญญาก่อนครับ';
			echo json_encode($response); exit;
		}
		if($arrs['MSGMEMO'] == ""){
			$response["error"] = true;
			$response["msg"]   = 'กรุณากรอกข้อความที่จะแสดงแจ้งเตือนก่อนครับ';
			echo json_encode($response); exit;
		}
		$sql = "
			select LOCAT,INPDT,USERID from {$this->MAuth->getdb('ARMAST')} 
			where CONTNO = '".$arrs['CONTNO']."' 
		";
		$querym = $this->db->query($sql);
		$rowm = $querym->row();
		$arrs['LOCAT']  = $rowm->LOCAT;
		$arrs['INPDT']  = $rowm->INPDT;
		$arrs['USERID'] = $rowm->USERID;
		
		if($arrs['choice'] == 'edityes'){
			$arrs['radio'] = "XX";
		}else{
			$arrs['radio'] = $arrs['USERID'];
		}
		//echo $arrs['radio']; exit;
		
		$data = "";
		$data .="
			declare @isval int = isnull((
				select count(*) from {$this->MAuth->getdb('ALERTMSG')} 
				where CONTNO='".$arrs['CONTNO']."'
			),0);
			if(@isval = 0)
			begin 
				insert into {$this->MAuth->getdb('ALERTMSG')} (
					[CONTNO],[LOCAT],[CREATEDT],[STARTDT],[ENDDT],[MEMO1],[INPDT],[USERID]
				)values(
					'".$arrs['CONTNO']."','".$arrs['LOCAT']."','".$arrs['DATESAVE']."'
					,'".$arrs['DATESTART']."','".$arrs['DATEENG']."','".$arrs['MSGMEMO']."'
					,'".$arrs['INPDT']."','".$arrs['radio']."'
				)
				insert into {$this->MAuth->getdb('hp_UserOperationLog')}(userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
				values ('".$this->sess["IDNo"]."','ข้อความแจ้งเตือน เพิ่ม','".$arrs['CONTNO']."'+'".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
			end
			else
			begin 
				rollback tran alertmsg;
				insert into #tempmsg select 'N' as id,'ไม่บันทึก : มีการบันทึกแจ้งเตือนรหัสกลุ่ม ".$arrs['CONTNO']." อยู่แล้ว' as msg;
				return;
			end
		";
		$sql = "
			if object_id('tempdb..#tempmsg') is not null drop table #tempmsg;
			create table #tempmsg (id varchar(1),msg varchar(max));
			
			begin tran alertmsg
			begin try			
				".$data."
				insert into #tempmsg select 'Y' as id,'สำเร็จ บันทึกข้อมูลเรียบร้อยแล้ว' as msg;
				commit tran alertmsg;
			end try
			begin catch
				rollback tran alertmsg;
				insert into #tempmsg select 'N' as id,'บันทึกข้อมูลไม่สำเร็จ : กรุณาติดต่อฝ่ายไอที' as msg;
			end catch
		";
		$this->db->query($sql);	
		$sql = "select * from #tempmsg";
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$response['stat'] = ($row->id == 'Y' ? true:false);
				$response['msg'] = $row->msg;
			}
		}else{
			$response['stat'] = false;
			$response['msg'] = 'ผิดพลาด';
		}
		echo json_encode($response);
	}
	function msgDelete(){
		$CONTNO = $_POST['CONTNO'];
		$response = array();
		$sql = "
			if object_id('tempdb..#tempmsg') is not null drop table #tempmsg;
			create table #tempmsg (id varchar(1),msg varchar(max));
			begin tran alertmsg
			
			begin try
				begin
					delete from {$this->MAuth->getdb('ALERTMSG')} where CONTNO = '".$CONTNO."'
					
					insert into {$this->MAuth->getdb('hp_UserOperationLog')}(userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
					values ('".$this->sess["IDNo"]."','ข้อความแจ้งเตือน ลบ','".$CONTNO."'+'".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
				end
				insert into #tempmsg select 'Y' as id,'สำเร็จ บันทึกข้อมูลเรียบร้อยแล้ว' as msg;
				commit tran alertmsg;
			end try
			begin catch
				rollback tran alertmsg;
				insert into #tempmsg select 'N' as id,'บันทึกข้อมูลไม่สำเร็จ : กรุณาติดต่อฝ่ายไอที ' as msg;
			end catch
		";
		//echo $sql; exit;
		$this->db->query($sql);	
		$sql = "select * from #tempmsg";
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$response['stat'] = ($row->id == 'Y' ? true:false);
				$response['msg'] = $row->msg;
			}
		}else{
			$response['stat'] = false;
			$response['msg'] = 'ผิดพลาด';
		}
		echo json_encode($response);
	}
	function getfromPayment(){
		$CONTNO     = $_REQUEST['CONTNO'];
		$LOCAT  	= $_REQUEST['LOCAT'];
		$DATESEARCH = $this->Convertdate(1,$_REQUEST['DATESEARCH']);
		
		$arrs = array();
		$sql = "
			select CONVERT(varchar(8),GETDATE(),112) as GDATE
		";
		$query = $this->db->query($sql);
		$gdate = "";
		if($query->row()){
			foreach($query->result() as $row){
				$gdate = $row->GDATE;
			}
		}
		$datesr = "";
		if($DATESEARCH == $gdate){
			$datesr = $DATESEARCH;
		}else{
			$datesr = null;
		}
		$sql = "
			exec [dbo].[FN_JD_LatePenalty] @contno ='".$CONTNO."',@dt = '".$datesr."'
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		$sql = "
			select NOPAY,convert(varchar(8),DDATE,112) as DDATE,DAMT,convert(varchar(8),DATE1,112) as DATE1
			,PAYMENT,DELAY,ADVDUE,GRDCOD,INTAMT from {$this->MAuth->getdb('ARPAY')} 
			where CONTNO = '".$CONTNO."' and LOCAT = '".$LOCAT."' 
			union
			select NOPAY,convert(varchar(8),DDATE,112) as DDATE,DAMT,convert(varchar(8),DATE1,112) as DATE1
			,PAYMENT,DELAY,ADVDUE,GRDCOD,INTAMT from {$this->MAuth->getdb('HARPAY')} 
			where CONTNO = '".$CONTNO."' and LOCAT = '".$LOCAT."' 
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		$i = 0;
		$listpayment = "";
		if($query->row()){
			foreach($query->result() as $row){$i++;
				$listpayment .="
					<tr class='trow' seq='old'>
						<td align='center'>".str_replace(chr(0),'',$row->NOPAY)."</td>
						<td align='center'>".$this->Convertdate(2,$row->DDATE)."</td>
						<td align='right'>".number_format($row->DAMT,2)."</td>
						<td align='center'>".$this->Convertdate(2,$row->DATE1)."</td>
						<td align='right'>".number_format($row->PAYMENT,2)."</td>
						<td align='center'>".str_replace(chr(0),'',$row->DELAY)."</td>
						<td align='right'>".number_format($row->INTAMT,2)."</td>
						<td align='center'>".str_replace(chr(0),'',$row->GRDCOD)."</td>
					</tr>
				";
			}
		}
		$arrs['listpayment'] = $listpayment;
		
		$sql2 = "
			select SUM(PAYINT) as sumPAID,SUM(DSCINT) as sumDSCINT from {$this->MAuth->getdb('CHQTRAN')} 
			where CONTNO = '".$CONTNO."' and LOCATPAY = '".$LOCAT."'
			and (PAYFOR='006' or PAYFOR = '007') and FLAG <> 'C' and (PAYDT IS NOT NULL)
		";
		//echo $sql2; exit;
		$query2 = $this->db->query($sql2);
		$sumPAID   = "";
		$sumDSCINT = "";
		if($query2->row()){
			foreach($query2->result() as $row){
				$sumPAID   = $row->sumPAID;
				$sumDSCINT = $row->sumDSCINT;				
			}
		}
		$sql3 = "
			select SUM(INTAMT) as sumINTAMT from {$this->MAuth->getdb('ARPAY')} 
			where CONTNO = '".$CONTNO."' and LOCAT = '".$LOCAT."'
		";
		//echo $sql3; exit;
		$query3 = $this->db->query($sql3);
		$sumINTAMT = "";
		$penalty   = "";
		if($query3->row()){
			foreach($query3->result() as $row){
				$sumINTAMT   = $row->sumINTAMT;
			}
		}
		$penalty     = $sumINTAMT-$sumPAID;
		$arrs['sumINTAMT'] = number_format($sumINTAMT,2);
		$arrs['sumPAID']   = number_format($sumPAID,2);
		$arrs['sumDSCINT'] = number_format($sumDSCINT,2);
		$arrs['penalty']   = number_format($penalty,2);
		//echo $arrs['sumINTAMT']; exit;
		
		if($i < 1){
			$response["error"] = true;
			$response["msg"] = "ไม่มีข้อมูลครับ";
			echo json_encode ($response); exit;
		}
		$html = "
			<div class='k_Penalty' style='width:800px;height:480px;overflow:auto;background-color:white;'>
				<fieldset style='height:100%'>
					<div style='float:left;height:100%;' class='col-sm-12 col-xs-12'>	
						<div class='row'>
							<div class='col-sm-12 col-xs-12' style='background-color:#309206;border:5px solid white;height:65px;text-align:center;font-size:12pt;color:white;font-weight:bold;'>	
								<br>***เบี้ยปรับล่าช้า***<br>
							</div>
						</div>
						<div class='row' style='height:40%;border:5px solid white;'>
							<div class='col-sm-12 col-xs-12' style='height:100%;border:0.1px solid #bdbdbd;background-color:#eee;'>
								<div id='dataTable-fixed-listpayment' class='dataTables_wrapper dt-bootstrap4 table-responsive' style='height:100%;width:100%;overflow:auto;'>
									<table id='dataTables-listpayment' class='table table-bordered dataTable table-hover' stat='' aria-describedby='dataTable_info' cellspacing='0' width='calc(100% - 1px)'>
										<thead>
											<tr role='row' style='background-color:#149f71;color:white;font-size:8pt;text-align:center;'>
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
										<tbody style='white-space:nowrap;font-size:8pt;background-color:white;'>
											".$arrs['listpayment']."
										</tbody>
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
											<input type='text' id='P_sumINTAMT' class='form-control input-sm' value='".$arrs['sumINTAMT']."' disabled>
										</div>
									</div>
									<div class='col-sm-6 col-xs-6'>	
										<div class='form-group'>
											ชำระแล้ว
											<input type='text' id='P_sumPAID' class='form-control input-sm' value='".$arrs['sumPAID']."' disabled>
										</div>
									</div>
									<div class='col-sm-6 col-xs-6'>	
										<div class='form-group'>
											ส่วนลด
											<input type='text' id='P_sumDSCINT' class='form-control input-sm' value='".$arrs['sumDSCINT']."' disabled>
										</div>
									</div>
									<div class='col-sm-6 col-xs-6'>	
										<div class='form-group'>
											ยอดคงเหลือ
											<input type='text' id='P_penalty' class='form-control input-sm' value='".$arrs['penalty']."' disabled>
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
		$CONTNO     = $_REQUEST['CONTNO'];
		$LOCAT      = $_REQUEST['LOCAT'];
		$DATESEARCH = $this->Convertdate(1,$_REQUEST['DATESEARCH']);
		$arrs = array();
		
		$sql = "
			select COUNT(*) checknull from {$this->MAuth->getdb('ARMAST')} 
			where CONTNO = '".$CONTNO."'
		";
		$query = $this->db->query($sql);
		$row = $query->row();
		$arrs['checknull'] = $row->checknull;
		if($arrs['checknull'] == 0){
			$response['error'] = true;
			$response['msg']   = "ไม่มีข้อมูลส่วนลดตัดสดครับ";
			echo json_encode($response); exit;
		}
		$sql = "
			select CONVERT(varchar(8),GETDATE(),112) as GDATE
		";
		$query = $this->db->query($sql);
		$gdate = "";
		if($query->row()){
			foreach($query->result() as $row){
				$gdate = $row->GDATE;
			}
		}
		$datesr = "";
		if($DATESEARCH == $gdate){
			$datesr = $DATESEARCH;
		}else{
			$datesr = null;
		}
		//function sql update เบี้ยปรับล่าช้า
		$sql = "
			exec [dbo].[FN_JD_LatePenalty] @contno ='".$CONTNO."',@dt = '".$datesr."'
		";
		$this->db->query($sql);
		
		$sql = "
			--จำนวนงวด
			declare @T_NOPAY int =(
				select T_NOPAY from {$this->MAuth->getdb('ARMAST')} 
				where CONTNO = '".$CONTNO."' and LOCAT = '".$LOCAT."'
			);
			declare @num_PERD varchar(max) = (
				select
				case
					when @T_NOPAY <= 10 then '10'
					when @T_NOPAY <= 12 then '12'
					when @T_NOPAY <= 18 then '18'
					when @T_NOPAY <= 24 then '24'
					when @T_NOPAY <= 30 then '30'
					when @T_NOPAY <= 36 then '36'
					when @T_NOPAY <= 42 then '42'
					when @T_NOPAY <= 48 then '48'
					when @T_NOPAY <= 54 then '54'
					when @T_NOPAY <= 60 then '60'
					else '60' 
				end
			);
			
			--ตาราง setup
			declare @CALDSC varchar(max) = (
				select CALDSC from {$this->MAuth->getdb('ARMAST')} 
				where CONTNO = '".$CONTNO."' and LOCAT = '".$LOCAT."'
			);
			select @num_PERD as N_PERD,@CALDSC as CALDSC,@T_NOPAY as T_NOPAY
		";
		//echo $sql;
		$query = $this->db->query($sql);
		$row = $query->row();
		$num_perd = $row->N_PERD;
		
		$caldsc3 = $row->CALDSC;
		$caldsc   = "TABLE".($row->CALDSC == 3 ? 1:$row->CALDSC);
		//echo $caldsc; exit;
		
		$sql = "
			declare @DISPAY decimal(8,2) = (
				select isnull(MIN(NOPAY),0) as DISPAY from {$this->MAuth->getdb('ARPAY')} 
				where CONTNO = '".$CONTNO."' and LOCAT = '".$LOCAT."' and DDATE >= '".$DATESEARCH."'
			)
			/*
			declare @NPROF decimal(8,2)	= (
				select sum(NPROF) as NPROF from(
					select case when PAYMENT > 0 then (NPROF/DAMT) * PAYMENT else NPROF end as  NPROF  
					from {$this->MAuth->getdb('ARPAY')}  
					where CONTNO = '".$CONTNO."' and LOCAT = '".$LOCAT."' and PAYMENT < DAMT and DDATE > '".$DATESEARCH."'
				)A
			)
			*/
			declare @NPROF decimal(8,2)	= (
				select sum(NPROF) as NPROF from(
					select NPROF from {$this->MAuth->getdb('ARPAY')} 
					where CONTNO = '".$CONTNO."' and LOCAT = '".$LOCAT."' 
					and DDATE > '".$DATESEARCH."'
				)A
			)
			declare @AROTH decimal(8,2)= (
				select isnull(SUM(PAYAMT-(SMPAY+SMCHQ)),0) as AROTH  from {$this->MAuth->getdb('AROTHR')}  
				where CONTNO = '".$CONTNO."' and LOCAT = '".$LOCAT."'
			)
			declare @INTAMT decimal(8,2) = (
				select sum(INTAMT) from {$this->MAuth->getdb('ARPAY')} 
				where CONTNO = '".$CONTNO."' and LOCAT = '".$LOCAT."'
			)
			declare @PAID decimal(8,2) = (
				select isnull(SUM(PAYINT),0) from {$this->MAuth->getdb('CHQTRAN')} 
				where CONTNO = '".$CONTNO."' and LOCATPAY = '".$LOCAT."' and 
				(PAYFOR='006' OR PAYFOR='007') and FLAG !='C' and (PAYDT IS NOT NULL) 
			)
			declare @NPROF2 decimal(8,2)	= (
				select top 1 NPROF from {$this->MAuth->getdb('ARPAY')}  
				where CONTNO = '".$CONTNO."' and LOCAT = '".$LOCAT."' order by NOPAY desc
			)
			declare @PERD decimal(18,1) = (
				select case when ".$caldsc3." = 3 then 0 else PERD".$num_perd." / 100 end
				from ".$this->MAuth->getdb($caldsc)." 
				where NOPAY = ISNULL(@DISPAY,0)		
			);
			select TOTPRC-SMPAY-SMCHQ as TOTAR
				,case when @DISPAY > 0 then @NPROF * @PERD else 0 end as PERC30
				,case when @DISPAY > 0 then (TOTPRC-SMPAY-SMCHQ)-(@NPROF * @PERD) else TOTPRC-SMPAY-SMCHQ end as TOTPAY
				,@INTAMT-@PAID as INTAMT
				,0 as OPERT
				,case when @DISPAY > 0 then (TOTPRC-SMPAY-SMCHQ)-(@NPROF * @PERD)+(@INTAMT-@PAID)+@AROTH 
				else TOTPRC-SMPAY-SMCHQ+(@INTAMT-@PAID)+@AROTH end as NETPAY
				,@NPROF as NPROF, @NPROF*0.5 as PERC50
				,case when isnull(@NPROF,0) = 0 then @NPROF2 else @NPROF end as NPROF
				,case when isnull(@NPROF,0) = 0 then @NPROF2 * 0.5 else @NPROF*0.5 end as PERC50
			from {$this->MAuth->getdb('ARMAST')} 
			where CONTNO = '".$CONTNO."' and LOCAT = '".$LOCAT."'
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		$i = 0;
		if($query->row()){
			foreach($query->result() as $row){$i++;
				$arrs['TOTAR']   = number_format($row->TOTAR,2);
				$arrs['PERC30']  = number_format($row->PERC30,2);
				$arrs['TOTPAY']  = number_format($row->TOTPAY,2);
				$arrs['INTAMT']  = number_format($row->INTAMT,2);
				$arrs['OPERT']   = number_format($row->OPERT,2);
				$arrs['NETPAY']  = number_format($row->NETPAY,2);
				$arrs['NPROF']   = number_format($row->NPROF,2);
				$arrs['PERC50']  = number_format($row->PERC50,2);
			}
		}else{
			$arrs['TOTAR']   = '0.00';
			$arrs['PERC30']  = '0.00';
			$arrs['TOTPAY']  = '0.00';
			$arrs['INTAMT']  = '0.00';
			$arrs['OPERT']   = '0.00';
			$arrs['NETPAY']  = '0.00';
			$arrs['NPROF']   = '0.00';
			$arrs['PERC50']  = '0.00';
		}
		//echo $i; exit;
		$html = "
			<div class='k_Discount' style='width:800px;height:480px;overflow:auto;background-color:white;'>
				<fieldset style='height:100%  background-color:#6aa705'>
					<div style='float:left;height:100%;' class='col-sm-12 col-xs-12'>	
						<div class='row'>
							<div class='col-sm-12 col-xs-12' style='background-color:#e74c3c  ;border:5px solid white;height:65px;text-align:center;font-size:12pt;color:white;font-weight:bold;'>	
								<br>ส่วนลดตัดสด<br>
							</div>
						</div>
						<div class='row' style='border:5px solid white;'>
							<div class='col-sm-12 col-xs-12' style='border:1px dotted #c2c2c2;'>
								<div class='col-sm-10 col-xs-10 col-sm-offset-1'>
									<div class='col-sm-6 col-xs-6'>	
										<div class='form-group'>
											ค่างวดคงเหลือ
											<input type='text' id='D_TOTAR' class='form-control input-sm' value='".$arrs['TOTAR']."' disabled>
										</div>
									</div>
									<div class='col-sm-6 col-xs-6'>	
										<div class='form-group'>
											ส่วนลดตัดสด
											<input type='text' id='D_PERC30' class='form-control input-sm' value='".$arrs['PERC30']."' disabled>
										</div>
									</div>
									<div class='col-sm-6 col-xs-6'>	
										<div class='form-group'>
											ต้องชำระค่างวด
											<input type='text' id='D_TOTPAY' class='form-control input-sm' value='".$arrs['TOTPAY']."' disabled>
										</div>
									</div>
									<div class='col-sm-6 col-xs-6'>	
										<div class='form-group'>
											+ เบี้ยปรับค้างชำระ
											<input type='text' id='D_INTAMT' class='form-control input-sm' value='".$arrs['INTAMT']."' disabled>
										</div>
									</div>
									<div class='col-sm-6 col-xs-6'>	
										<div class='form-group'>
											ค่าดำเนินการ
											<input type='text' id='D_OPERT' class='form-control input-sm' value='".$arrs['OPERT']."' disabled>
										</div>
									</div>
									<div class='col-sm-6 col-xs-6'>	
										<div class='form-group'>
											รวมยอดตัดสด
											<input type='text' id='D_NETPAY' class='form-control input-sm' value='".$arrs['NETPAY']."' disabled>
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
											<center><font color='#e74c3c'><b>ส่วนลดที่ให้ลูกค้าต้องไม่ต่ำกว่า 50% ของดอกผลคงเหลือ</b></font></center>
										</div>
									</div>
									<div class='col-sm-6 col-xs-6'>	
										<div class='form-group'>
											ดอกผลคงเหลือ
											<input type='text' id='D_NPROF' class='form-control input-sm' value='".$arrs['NPROF']."' disabled>
										</div>
									</div>
									<div class='col-sm-6 col-xs-6'>	
										<div class='form-group'>
											50% ของกำไรคงเหลือ
											<input type='text' id='D_PERC50' class='form-control input-sm' value='".$arrs['PERC50']."' disabled>
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
		$CUSCOD1 = $_REQUEST['CUSCOD1'];
		$CONTNO1 = $_REQUEST['CONTNO1'];
		$STRNO1  = $_REQUEST['STRNO1'];
		$tab11C  = $_REQUEST['tab11C'];
		$response = array(); 
		//tab11
		$sql = "
			IF OBJECT_ID('tempdb..#RPFN') IS NOT NULL DROP TABLE #RPFN
			select CONTNO,CUSCOD,TYPESALE,LOCAT,convert(varchar(8),SDATE,112) as SDATE,TOTPRC,SMPAY,BALANCE
			,SMCHQ,TKANG,STRNO,RESVNO,TSALE,FL
			into #RPFN
			FROM(
				select A. * from (
					select CONTNO,CUSCOD,'ขายผ่อน' as TYPESALE,LOCAT,SDATE,TOTPRC,SMPAY,(TOTPRC-SMPAY) as BALANCE
					,SMCHQ,TKANG,STRNO,RESVNO,TSALE,'' as FL from {$this->MAuth->getdb('ARMAST')} where DELDT is null and CUSCOD like '".$CUSCOD1."%' and 
					CONTNO like '".$CONTNO1."%' and STRNO like '".$STRNO1."%'
					union
					select CONTNO,CUSCOD,'ขายผ่อน' as TYPESALE,LOCAT,SDATE,TOTPRC,SMPAY,(TOTPRC-SMPAY) as BALANCE
					,SMCHQ,TKANG,STRNO,RESVNO,TSALE,'*' as FL from {$this->MAuth->getdb('HARMAST')} where DELDT is null and CUSCOD like '".$CUSCOD1."%' and 
					CONTNO like '".$CONTNO1."%' and STRNO like '".$STRNO1."%'
					union 
					select CONTNO,CUSCOD,'ขายสด' as TYPESALE,LOCAT,SDATE,TOTPRC,SMPAY,(TOTPRC-SMPAY) as BALANCE
					,SMCHQ,TKANG,STRNO,RESVNO,TSALE,'' as FL from {$this->MAuth->getdb('ARCRED')} where DELDT is null and CUSCOD like '".$CUSCOD1."%' and 
					CONTNO like '".$CONTNO1."%' and STRNO like '".$STRNO1."%'
					union 
					select CONTNO,CUSCOD,'ขายสด' as TYPESALE,LOCAT,SDATE,TOTPRC,SMPAY,(TOTPRC-SMPAY) as BALANCE
					,SMCHQ,TKANG,STRNO,RESVNO,TSALE,'*' as FL from {$this->MAuth->getdb('HARCRED')} where DELDT is null and CUSCOD like '".$CUSCOD1."%' and 
					CONTNO like '".$CONTNO1."%' and STRNO like '".$STRNO1."%'
					union 
					select CONTNO,CUSCOD,'ขายไปแนนซ์' as TYPESALE,LOCAT,SDATE,TOTPRC,SMPAY,(TOTPRC-SMPAY) as BALANCE
					,SMCHQ,TKANG,STRNO,RESVNO,TSALE,'' as FL from {$this->MAuth->getdb('ARFINC')} where DELDT is null and CUSCOD like '".$CUSCOD1."%' and 
					CONTNO like '".$CONTNO1."%' and STRNO like '".$STRNO1."%'
					union 
					select CONTNO,CUSCOD,'ขายไปแนนซ์' as TYPESALE,LOCAT,SDATE,TOTPRC,SMPAY,(TOTPRC-SMPAY) as BALANCE
					,SMCHQ,TKANG,STRNO,RESVNO,TSALE,'*' as FL from {$this->MAuth->getdb('HARFINC')} where DELDT is null and CUSCOD like '".$CUSCOD1."%' and 
					CONTNO like '".$CONTNO1."%' and STRNO like '".$STRNO1."%'
					union 
					select A.CONTNO,A.CUSCOD,'ขายส่งเอเยนต์' as TYPESALE,A.LOCAT,A.SDATE,A.TOTPRC,A.SMPAY,(A.TOTPRC-A.SMPAY)
					as BALANCE,A.SMCHQ,0 as TKANG,B.STRNO,'' as RESVNO,A.TSALE,'' as FL from {$this->MAuth->getdb('AR_INVOI')} A,{$this->MAuth->getdb('INVTRAN')} B
					where A.DELDT is null and A.CONTNO = B.CONTNO and A.CUSCOD like '".$CUSCOD1."%' and A.CONTNO like '".$CONTNO1."%' and B.STRNO like '".$STRNO1."%'
					union
					select A.CONTNO,A.CUSCOD,'ขายส่งเอเยนต์' as TYPESALE,A.LOCAT,A.SDATE,A.TOTPRC,A.SMPAY,(A.TOTPRC-A.SMPAY)
					as BALANCE,A.SMCHQ,0 as TKANG,B.STRNO,'*' as RESVNO,A.TSALE,'' as FL from {$this->MAuth->getdb('HAR_INVO')} A,{$this->MAuth->getdb('HINVTRAN')} B
					where A.DELDT is null and A.CONTNO = B.CONTNO and A.CUSCOD like '".$CUSCOD1."%' and A.CONTNO like '".$CONTNO1."%' and B.STRNO like '".$STRNO1."%'
					union 	
					select A.ARCONT as CONTNO,A.CUSCOD,'ลูกหนี้อื่น' as TYPESALE,A.LOCAT,A.ARDATE as SDATE,A.PAYAMT
					as TOTPRC,A.SMPAY,(A.PAYAMT-A.SMPAY) as BALANCE,A.SMCHQ,0 as TKANG,'' as STRNO,'' as RESVNO,A.TSALE,'' as FL from {$this->MAuth->getdb('AROTHR')} A
					where CUSCOD like '".$CUSCOD1."%' and CONTNO like '".$CONTNO1."%'
				) AS A 
				left join {$this->MAuth->getdb('REGTAB')} B ON A.STRNO = B.STRNO  
				where (B.REGNO like '%' or (B.REGNO is null) ) and A.STRNO like '".$STRNO1."%' 
			)RPFN
		";
		//echo $sql; exit;
		$this->db->query($sql);
		
		if($tab11C[0] == "true"){
			$sql = "select * from #RPFN";
		}else{
			$sql = "select * from #RPFN where FL != '*'";
		}
		$query = $this->db->query($sql);
		$cusbuy = ""; $i = 0; $TSALE = "";
		if($query->row()){
			foreach($query->result() as $row){$i++;
				$css = "color:black";
				$css2 = "color:blue";
				if($row->FL == "*"){
					$css = "color:red";
					$css2 = "color:red";
				}
				$cusbuy .= "
					<tr style='{$css}'>
						<td class='getit' style='cursor:pointer;{$css2}'
							CONTNO = '".str_replace(chr(0),'',$row->CONTNO)."' 
							CUSCOD = '".str_replace(chr(0),'',$row->CUSCOD)."'
							STRNO  = '".str_replace(chr(0),'',$row->STRNO)."'
							LOCAT  = '".str_replace(chr(0),'',$row->LOCAT)."'
							TSALE  = '".str_replace(chr(0),'',$row->TSALE)."'
						>".$row->CONTNO."</td>
						<td>".$row->CUSCOD."</td>
						<td>".$row->TYPESALE."</td>
						<td>".$row->LOCAT."</td>
						<td>".$this->Convertdate(2,$row->SDATE)."</td>
						<td align='right'>".number_format($row->TOTPRC,2)."</td>
						<td align='right'>".number_format($row->SMPAY,2)."</td>
						<td align='right'>".number_format($row->BALANCE,2)."</td>
						<td align='center' style='color:red;'>".$row->FL."</td>
					</tr>
				";	
				$TSALE = str_replace(chr(0),'',$row->TSALE);
			}
		}else{
			$cusbuy .= "<tr class='trow'><td colspan='9' style='color:red;'>ไม่มีข้อมูล</td></tr>";
		}
		$response['TSALE'] = $TSALE;
		
		$response["cusbuy"] = $cusbuy;
		$response["numrow"] = $i;
		/*
		$stylesheet = "
			<style>
				tr.highlighted td{background:#cce8ff;}
			</style>
		";
		$cusbuy = $cusbuy.$stylesheet;
		*/
		/*
		if($i < 1){
			//การซื้อ
			$sql12 = "
				IF OBJECT_ID('tempdb..#ISR') IS NOT NULL DROP TABLE #ISR
				select CONTNO,CUSCOD,NAME1,NAME2,STRNO,convert(varchar(8),SDATE,112) as SDATE, BALANCE
				into #ISR
				FROM(
					select A.CONTNO,B.CUSCOD,C.NAME1,C.NAME2,B.STRNO,B.SDATE,(B.TOTPRC-B.SMPAY) as BALANCE 
					from {$this->MAuth->getdb('ARMGAR')} A
					left join {$this->MAuth->getdb('ARMAST')} B on A.CONTNO = B.CONTNO and A.LOCAT = B.LOCAT
					left join {$this->MAuth->getdb('CUSTMAST')} C on B.CUSCOD = C.CUSCOD where A.CUSCOD = '".$CUSCOD1."'

					union all
					select A.CONTNO,B.CUSCOD,C.NAME1,C.NAME2,B.STRNO,B.SDATE,(B.TOTPRC-B.SMPAY) as BALANCE 
					from {$this->MAuth->getdb('HARMGAR')} A
					left join {$this->MAuth->getdb('HARMAST')} B on A.CONTNO = B.CONTNO and A.LOCAT = B.LOCAT
					left join {$this->MAuth->getdb('CUSTMAST')} C on B.CUSCOD = C.CUSCOD where A.CUSCOD = '".$CUSCOD1."'
				)ISR
			";
			//echo $sql12; exit;
			$query12 = $this->db->query($sql12);
			$sql13 = "
				select * from #ISR
			";
			$query13 = $this->db->query($sql13);
			$insurance = "";
			if($query13->row()){
				foreach($query13->result() as $row){
					$insurance .= "
						<tr class='trow' seq='old'>
							<td class=''>".$row->CONTNO."</td>
							<td>".$row->CUSCOD."</td>
							<td>".$row->NAME1."</td>
							<td>".$row->NAME2."</td>
							<td>".$row->STRNO."</td>
							<td>".$this->Convertdate(2,$row->SDATE)."</td>
							<td align='right'>".number_format($row->BALANCE,2)."</td>
						</tr>
					";	
				}
			}else{
				$insurance .= "<tr class='trow'><td colspan='9' style='color:red;'>ไม่มีข้อมูล</td></tr>";
			}
			
			$response["insurance"] = $insurance;
			$sql14 = "
				select count(CONTNO) as countCUSCOD,sum(BALANCE) as sumBALANCE from #ISR
			";
			$query14 = $this->db->query($sql14);
			if($query14->row()){
				foreach($query14->result() as $row){
					$response['COUNTCUSCOD_ISR']    = $row->countCUSCOD;
					$response['SUMBALANCE_ISR']		= number_format($row->sumBALANCE,2);
				}
			}
			$response["MSGMEMO"] = "none";
		}
		*/
		if($i > 0){
			//KeySearchFirst --> sql top 1
			$sql = "
				select top 1 CONTNO,CUSCOD,STRNO,LOCAT from #RPFN order by CONTNO DESC
			";
			$query = $this->db->query($sql);
			$contno = ""; $cuscod = ""; $strno = ""; $locat = "";
			if($query->row()){
				foreach($query->result() as $row){
					$contno = str_replace(chr(0),'',$row->CONTNO);
					$cuscod = str_replace(chr(0),'',$row->CUSCOD);
					$strno  = str_replace(chr(0),'',$row->STRNO);
					$locat  = str_replace(chr(0),'',$row->LOCAT);	
				}
			}			
			//echo $locat; exit;
			$sql = "
				select SNAM,NAME1,NAME2 from {$this->MAuth->getdb('CUSTMAST')} 
				where CUSCOD = '".$cuscod."'
			";
			$query = $this->db->query($sql);
			if($query->row()){
				foreach($query->result() as $row){
					$response['SNAM']		 = $row->SNAM;
					$response['NAME1']		 = $row->NAME1;
					$response['NAME2']		 = $row->NAME2;
				}
			}
			if($tab11C[0] == "true"){
				$sql11 = "
					select COUNT(CONTNO) as countCONTNO,SUM(TOTPRC) as sumTOTPRC,SUM(SMPAY) as sumSMPAY
					,SUM(BALANCE) as sumBALANCE from #RPFN
				";
			}else{
				$sql11 = "
					select COUNT(CONTNO) as countCONTNO,SUM(TOTPRC) as sumTOTPRC,SUM(SMPAY) as sumSMPAY
					,SUM(BALANCE) as sumBALANCE from #RPFN where FL != '*'
				";
			}
			$query11 = $this->db->query($sql11);
			//echo $sql11; exit;
			if($query11->row()){
				foreach($query11->result() as $row){
					$response['COUNTCONTNO'] = $row->countCONTNO;
					$response['SUMTOTPRC']   = number_format($row->sumTOTPRC,2);
					$response['SUMSMPAY']    = number_format($row->sumSMPAY,2);
					$response['SUMBALANCE']  = number_format($row->sumBALANCE,2);
				}
			}
			$sql12 = "
				IF OBJECT_ID('tempdb..#ISR') IS NOT NULL DROP TABLE #ISR
				select CONTNO,CUSCOD,NAME1,NAME2,STRNO,convert(varchar(8),SDATE,112) as SDATE, BALANCE
				into #ISR
				FROM(
					select A.CONTNO,B.CUSCOD,C.NAME1,C.NAME2,B.STRNO,B.SDATE,(B.TOTPRC-B.SMPAY) as BALANCE 
					from {$this->MAuth->getdb('ARMGAR')} A
					left join {$this->MAuth->getdb('ARMAST')} B on A.CONTNO = B.CONTNO and A.LOCAT = B.LOCAT
					left join {$this->MAuth->getdb('CUSTMAST')} C on B.CUSCOD = C.CUSCOD where A.CUSCOD = '".$cuscod."'

					union all
					select A.CONTNO,B.CUSCOD,C.NAME1,C.NAME2,B.STRNO,B.SDATE,(B.TOTPRC-B.SMPAY) as BALANCE 
					from {$this->MAuth->getdb('HARMGAR')} A
					left join {$this->MAuth->getdb('HARMAST')} B on A.CONTNO = B.CONTNO and A.LOCAT = B.LOCAT
					left join {$this->MAuth->getdb('CUSTMAST')} C on B.CUSCOD = C.CUSCOD where A.CUSCOD = '".$cuscod."'
				)ISR
			";
			//echo $sql12; exit;
			$this->db->query($sql12);
			$sql13 = "
				select * from #ISR
			";
			$query13 = $this->db->query($sql13);
			$insurance = "";
			if($query13->row()){
				foreach($query13->result() as $row){
					$insurance .= "
						<tr class='trow' seq='old'>
							<td class=''>".$row->CONTNO."</td>
							<td>".$row->CUSCOD."</td>
							<td>".$row->NAME1."</td>
							<td>".$row->NAME2."</td>
							<td>".$row->STRNO."</td>
							<td>".$this->Convertdate(2,$row->SDATE)."</td>
							<td align='right'>".number_format($row->BALANCE,2)."</td>
						</tr>
					";	
				}
			}else{
				$insurance .= "<tr class='trow'><td colspan='9' style='color:red;'>ไม่มีข้อมูล</td></tr>";
			}
			$response["insurance"] = $insurance;
			$sql14 = "
				select count(CONTNO) as countCUSCOD,sum(BALANCE) as sumBALANCE from #ISR
			";
			$query14 = $this->db->query($sql14);
			if($query14->row()){
				foreach($query14->result() as $row){
					$response['COUNTCUSCOD_ISR']    = $row->countCUSCOD;
					$response['SUMBALANCE_ISR']		= number_format($row->sumBALANCE,2);
				}
			}
/*---------------------------//ข้อความแจ้งเตือน-----------------------------------------*/
			$sql15 = "
				select CONTNO, LOCAT, convert(nvarchar,STARTDT,112) as STARTDT, convert(nvarchar,ENDDT,112) as ENDDT, MEMO1, USERID
				from {$this->MAuth->getdb('ALERTMSG')} 
				where CONTNO = '".$contno."' and GETDATE() between STARTDT and ENDDT
			";
			//echo $sql5; exit;
			$query15 = $this->db->query($sql15);
			if($query15->row()){
				foreach($query15->result() as $row){
					$response["CONTNO"]     = $row->CONTNO;
					$response["MSGLOCAT"] 	= $row->LOCAT;
					$response["STARTDT"] 	= $row->STARTDT;
					$response["ENDDT"] 		= $row->ENDDT;
					$response["MSGMEMO"] 	= $row->MEMO1;
					$response["USERID"] 	= $row->USERID;
				}
			}else{
				$response["MSGMEMO"] = "none";
			}
/*---------------------------//ข้อความแจ้งเตือน-----------------------------------------*/
			//tab2
			$sql21 = "
				select CONTNO,LOCAT from #RPFN where CONTNO = '".$contno."'
			";
			$query21 = $this->db->query($sql21);
			if($query21->row()){
				foreach($query21->result() as $row){
					$response['CONTNO_2']   = str_replace(chr(0),'',$row->CONTNO);
					$response['LOCAT_2']  	= str_replace(chr(0),'',$row->LOCAT);
					$response['CONTNO_3'] 	= str_replace(chr(0),'',$row->CONTNO);
					$response['LOCAT_3']  	= str_replace(chr(0),'',$row->LOCAT);
					$response['CONTNO_4']   = str_replace(chr(0),'',$row->CONTNO);
					$response['LOCAT_4']  	= str_replace(chr(0),'',$row->LOCAT);
					$response['CONTNO_6']   = str_replace(chr(0),'',$row->CONTNO);
					$response['LOCAT_6']  	= str_replace(chr(0),'',$row->LOCAT);
					$response['CONTNO_7']   = str_replace(chr(0),'',$row->CONTNO);
					$response['LOCAT_7']  	= str_replace(chr(0),'',$row->LOCAT);
					$response['CONTNO_8']  	= str_replace(chr(0),'',$row->CONTNO);
				}
			}
			$sql22 = "
				IF OBJECT_ID('tempdb..#CDT') IS NOT NULL DROP TABLE #CDT
				select CONTNO,RVLOCAT,STRNO,ENGNO,NADDCOST,TYPE,MODEL,BAAB,COLOR,CC,STAT,MANUYR,YSTAT,RECVNO,GCODE
				into #CDT
				FROM(
					select CONTNO,RVLOCAT,STRNO,ENGNO,NADDCOST,TYPE,MODEL,BAAB,COLOR,CC,STAT,MANUYR,YSTAT
					,RECVNO,GCODE from {$this->MAuth->getdb('INVTRAN')} where STRNO = '".$strno."'
					union
					select CONTNO,RVLOCAT,STRNO,ENGNO,NADDCOST,TYPE,MODEL,BAAB,COLOR,CC,STAT,MANUYR,YSTAT
					,RECVNO,GCODE from {$this->MAuth->getdb('HINVTRAN')} where STRNO = '".$strno."'
				)CDT
			";
			//echo $sql22; exit;
			$this->db->query($sql22);
			
			$sql23 = "
				select CONTNO,RVLOCAT,STRNO,ENGNO,NADDCOST,TYPE,MODEL
				,BAAB,COLOR,CC,STAT,MANUYR,YSTAT,RECVNO,GCODE from #CDT
			";	
			$query23 = $this->db->query($sql23);
			$recvno = ""; $gcode  = ""; $stat = ""; $ystat = "";
			if($query23->row()){
				foreach($query23->result() as $row){
					$response['STRNO_2']    = str_replace(chr(0),'',$row->STRNO);
					$response['ENGNO_2']    = str_replace(chr(0),'',$row->ENGNO);
					$response['NADDCOST_2'] = number_format($row->NADDCOST,2);
					$response['TYPE_2']     = str_replace(chr(0),'',$row->TYPE);
					$response['MODEL_2']    = str_replace(chr(0),'',$row->MODEL);
					$response['BAAB_2']     = str_replace(chr(0),'',$row->BAAB);
					$response['COLOR_2']    = str_replace(chr(0),'',$row->COLOR);
					$response['CC_2']       = str_replace(chr(0),'',$row->CC);
					$stat					= str_replace(chr(0),'',$row->STAT);
					$ystat    				= str_replace(chr(0),'',$row->YSTAT);
					$response['MANUYR_2']   = str_replace(chr(0),'',$row->MANUYR);
					
					$recvno					= str_replace(chr(0),'',$row->RECVNO);
					$gcode					= str_replace(chr(0),'',$row->GCODE);
				}
			}
			if($stat == 'O' && $ystat !== 'Y'){
				$response['STAT_2'] = "เก่า";
			}else if($stat == 'N' && $ystat !== 'Y'){
				$response['STAT_2'] = "ใหม่";
			}else if($stat == 'O' && $ystat == 'Y'){
				$response['STAT_2'] = "เก่า : เป็นรถยึด";
			}else if($stat == 'N' && $ystat == 'Y'){
				$response['STAT_2'] = "ใหม่ : เป็นรถยึด";
			}
			$sql24 = "
				select APNAME from {$this->MAuth->getdb('INVINVO')} A,{$this->MAuth->getdb('APMAST')} B 
				where RECVNO = '".$recvno."' and A.APCODE = B.APCODE 
			";
			$query24 = $this->db->query($sql24);
			if($query24->row()){
				foreach($query24->result() as $row){
					$response['APNAME_2']    = str_replace(chr(0),'',$row->APNAME);
				}
			}
			$sql25 = "
				select GDESC from {$this->MAuth->getdb('SETGROUP')} where GCODE = '".$gcode."' 
			";
			$query25 = $this->db->query($sql25);
			if($query25->row()){
				foreach($query25->result() as $row){
					$response['GDESC_2']    = str_replace(chr(0),'',$row->GDESC);
				}
			}
			$sql26 = "
				select REGNO,REGPAY,convert(varchar(8),REGYEAR,112) as REGYEAR,convert(varchar(8),REGEXP,112) as REGEXP
				,REGTYP,convert(varchar(8),GARFRM,112) as GARFRM,convert(varchar(8),GAREXP,112) as GAREXP
				,GARNO3,convert(varchar(8),GAR3FRM,112) as GAR3FRM,convert(varchar(8),GAR3EXP,112) as GAR3EXP
				from {$this->MAuth->getdb('REGTAB')} where STRNO = '".$strno."' 
			";
			//echo $sql2_4; exit;
			$query26 = $this->db->query($sql26);
			if($query26->row()){
				foreach($query26->result() as $row){
					$response['REGNO_2']    = str_replace(chr(0),'',$row->REGNO); 
					$response['REGYEAR_2']  = $this->Convertdate(2,$row->REGYEAR); 
					$response['REGEXP_2']   = $this->Convertdate(2,$row->REGEXP); 
					$response['REGTYP_2']   = str_replace(chr(0),'',$row->REGTYP); 
					$response['GARFRM_2']   = $this->Convertdate(2,$row->GARFRM); 
					$response['GAREXP_2']   = $this->Convertdate(2,$row->GAREXP); 
					$response['GARNO3_2']   = str_replace(chr(0),'',$row->GARNO3); 
					$response['GAR3FRM_2']  = $this->Convertdate(2,$row->GAR3FRM);
					$response['GAR3EXP_2']  = $this->Convertdate(2,$row->GAR3EXP);
				}
			}
			//อุปกรณ์เสริม
			$sql27 = "
				IF OBJECT_ID('tempdb..#ACCESSORY') IS NOT NULL DROP TABLE #ACCESSORY
				select OPTCODE,OPTNAME,UPRICE,QTY,TOTPRC,PRCPU
				into #ACCESSORY
				from (
					select A.OPTCODE,B.OPTNAME,A.UPRICE,A.QTY,A.TOTPRC,(A.TOTPRC/A.QTY) as PRCPU 
					from {$this->MAuth->getdb('ARINOPT')} A
					left join {$this->MAuth->getdb('OPTMAST')} B on A.OPTCODE = B.OPTCODE 
					where A.CONTNO like '".$contno."%' and B.LOCAT like '".$locat."%'
				)ACCESSORY
			";
			//echo $sql3; exit;
			$query27 = $this->db->query($sql27);
			$sql28 = "
				select OPTCODE,OPTNAME,UPRICE,QTY,TOTPRC from #ACCESSORY
			";
			//echo $sql4; exit;
			$query28 = $this->db->query($sql28);
			$acce = "";
			if($query28->row()){
				foreach($query28->result() as $row){
					$acce .= "
						<tr class='trow' seq='old'>
							<td>".$row->OPTCODE."</td>
							<td>".$row->OPTNAME."</td>
							<td align ='right'>".number_format($row->UPRICE,2)."</td>
							<td align ='right'>".number_format($row->QTY,2)."</td>
							<td align ='right'>".number_format($row->TOTPRC,2)."</td>
						</tr>
					";	
				}
			}
			$response["acce"] = $acce;
			$sql29 = "
				select count(OPTCODE) as countOPTCODE,sum(TOTPRC) as sumTOTPRC from #ACCESSORY
			";
			$query29 = $this->db->query($sql29);
			if($query29->row()){
				foreach($query29->result() as $row){
					$response['COUNTOPTCODE_2']   = $row->countOPTCODE;
					$response['SUMTOTPRC_2']	  = number_format($row->sumTOTPRC,2);
				}
			}
			//รายการรถ
			$sql2A = "
				IF OBJECT_ID('tempdb..#LISTCAR') IS NOT NULL DROP TABLE #LISTCAR
				select STRNO,NPRICE,VATPRC,TOTPRC
				into #LISTCAR
				FROM(
					select STRNO,NPRICE,VATPRC,TOTPRC from {$this->MAuth->getdb('AR_TRANS')} 
					where CONTNO = '".$contno."' and LOCAT = '".$locat."'
				)LISTCAR
			";
			//echo $sql3; exit;
			$query2A = $this->db->query($sql2A);
			
			$sql2B = "
				select STRNO,NPRICE,VATPRC,TOTPRC from #LISTCAR
			";
			//echo $sql4; exit;
			$query2B = $this->db->query($sql2B);
			$list = "";
			$l = 0;
			if($query2B->row()){
				foreach($query2B->result() as $row){$l++;
					$list .= "
						<tr class='trow' seq='old'>
							<td class='getstrno' style='cursor:pointer;color:blue;'
								STRNO = '".str_replace(chr(0),'',$row->STRNO)."'
							>".str_replace(chr(0),'',$row->STRNO)."</td>
							<td align ='right'>".number_format($row->NPRICE,2)."</td>
							<td align ='right'>".number_format($row->VATPRC,2)."</td>
							<td align ='right'>".number_format($row->TOTPRC,2)."</td>
							<td></td>
						</tr>
					";	
				}
			}
			$response["list"] = $list;
			$response['numrow1'] = $l;
			
			$sql2C = "select count(STRNO) as countSTRNO,sum(NPRICE) as sumNPRICE from #LISTCAR";
			$query2C = $this->db->query($sql2C);
			if($query2C->row()){
				foreach($query2C->result() as $row){
					$response['COUNTSTRNO_2']   = $row->countSTRNO;
					$response['SUMNPRICE_2']	= number_format($row->sumNPRICE,2);
				}
			}
			//tab3
			$sql31 = "
				IF OBJECT_ID('tempdb..#CONTRAC') IS NOT NULL DROP TABLE #CONTRAC
				select CONTNO,LOCAT,YSTAT,TSALE,CONVERT(varchar(8),SDATE,112) as SDATE,TOTPRC,SMPAY,REMAIN,SMCHQ,CONVERT(varchar(8),LPAYD,112) 
				as LPAYD,LPAYA,TOTDWN,PAYDWN,KDWN,EXP_AMT,EXP_FRM,EXP_TO,EXP_PRD,CONTSTAT,TKANG,BILLCOLL,CHECKER,PAYTYP
				,T_NOPAY,CALINT,CALDSC,DELYRT,DLDAY,ADDRNO
				into #CONTRAC
				FROM(
					select CONTNO,LOCAT,'' as YSTAT,TSALE,SDATE,TOTPRC,SMPAY,(TOTPRC-SMPAY) as REMAIN
					,SMCHQ,LPAYDT as LPAYD,0 as LPAYA,0 as TOTDWN,0 as PAYDWN,0 as KDWN,0 as EXP_AMT,0 as EXP_FRM,0 as EXP_TO
					,0 as EXP_PRD,'' as CONTSTAT,TKANG,'' as BILLCOLL,'' as CHECKER,'' as PAYTYP,0 as T_NOPAY,'' as CALINT
					,'' as CALDSC,0 as DELYRT,0 as DLDAY,ADDRNO from {$this->MAuth->getdb('ARCRED')} where CONTNO = '".$contno."' and LOCAT ='".$locat."'
					union 
					select CONTNO,LOCAT,'' as YSTAT,TSALE,SDATE,TOTPRC,SMPAY,(TOTPRC-SMPAY) as REMAIN
					,SMCHQ,LPAYDT as LPAYD,0 as LPAYA,0 as TOTDWN,0 as PAYDWN,0 as KDWN,0 as EXP_AMT,0 as EXP_FRM,0 as EXP_TO
					,0 as EXP_PRD,'' as CONTSTAT,TKANG,'' as BILLCOLL,'' as CHECKER,'' as PAYTYP,0 as T_NOPAY,'' as CALINT
					,'' as CALDSC,0 as DELYRT,0 as DLDAY,ADDRNO from {$this->MAuth->getdb('HARCRED')} where CONTNO = '".$contno."' and LOCAT ='".$locat."'
					union
					select CONTNO,LOCAT,'' as YSTAT,TSALE,SDATE,TOTPRC,SMPAY,(TOTPRC-SMPAY) as REMAIN
					,SMCHQ,LPAYD as LPAYD,0 as LPAYA,0 as TOTDWN,0 as PAYDWN,0 as KDWN,0 as EXP_AMT,0 as EXP_FRM,0 as EXP_TO
					,0 as EXP_PRD,'' as CONTSTAT,TKANG,'' as BILLCOLL,'' as CHECKER,'' as PAYTYP,0 as T_NOPAY,'' as CALINT
					,'' as CALDSC,0 as DELYRT,0 as DLDAY,ADDRNO from {$this->MAuth->getdb('ARFINC')} where CONTNO = '".$contno."' and LOCAT ='".$locat."'
					union
					select CONTNO,LOCAT,'' as YSTAT,TSALE,SDATE,TOTPRC,SMPAY,(TOTPRC-SMPAY) as REMAIN
					,SMCHQ,LPAYD as LPAYD,0 as LPAYA,0 as TOTDWN,0 as PAYDWN,0 as KDWN,0 as EXP_AMT,0 as EXP_FRM,0 as EXP_TO
					,0 as EXP_PRD,'' as CONTSTAT,TKANG,'' as BILLCOLL,'' as CHECKER,'' as PAYTYP,0 as T_NOPAY,'' as CALINT
					,'' as CALDSC,0 as DELYRT,0 as DLDAY,ADDRNO from {$this->MAuth->getdb('HARFINC')} where CONTNO = '".$contno."' and LOCAT ='".$locat."'
					union
					select CONTNO,LOCAT,'' as YSTAT,TSALE,SDATE,TOTPRC,SMPAY,(TOTPRC-SMPAY) as REMAIN
					,SMCHQ,LPAYDT as LPAYD,0 as LPAYA,0 as TOTDWN,0 as PAYDWN,0 as KDWN,0 as EXP_AMT,0 as EXP_FRM,0 as EXP_TO
					,0 as EXP_PRD,'' as CONTSTAT,0 as TKANG,'' as BILLCOLL,'' as CHECKER,'' as PAYTYP,0 as T_NOPAY,'' as CALINT
					,'' as CALDSC,0 as DELYRT,0 as DLDAY,ADDRNO from {$this->MAuth->getdb('AR_INVOI')} where CONTNO = '".$contno."' and LOCAT ='".$locat."'
					union
					select CONTNO,LOCAT,'' as YSTAT,TSALE,SDATE,TOTPRC,SMPAY,(TOTPRC-SMPAY) as REMAIN
					,SMCHQ,LPAYDT as LPAYD,0 as LPAYA,0 as TOTDWN,0 as PAYDWN,0 as KDWN,0 as EXP_AMT,0 as EXP_FRM,0 as EXP_TO
					,0 as EXP_PRD,'' as CONTSTAT,0 as TKANG,'' as BILLCOLL,'' as CHECKER,'' as PAYTYP,0 as T_NOPAY,'' as CALINT
					,'' as CALDSC,0 as DELYRT,0 as DLDAY,ADDRNO from {$this->MAuth->getdb('HAR_INVO')} where CONTNO = '".$contno."' and LOCAT ='".$locat."'
					union  
					select CONTNO,LOCAT,YSTAT,TSALE,SDATE,TOTPRC,SMPAY,(TOTPRC-SMPAY) as REMAIN,SMCHQ,LPAYD,LPAYA,TOTDWN,PAYDWN,(TOTDWN-PAYDWN) as KDWN
					,EXP_AMT,EXP_FRM,EXP_TO,EXP_PRD,CONTSTAT,TKANG,BILLCOLL,CHECKER,PAYTYP,T_NOPAY,CALINT,CALDSC,DELYRT,DLDAY,ADDRNO from {$this->MAuth->getdb('ARMAST')}
					where CONTNO = '".$contno."' and LOCAT ='".$locat."'
					union 
					select CONTNO,LOCAT,(case when YSTAT = 'Y' then 'H' else YSTAT end) as YSTAT,TSALE,SDATE,TOTPRC,SMPAY,(TOTPRC-SMPAY) as REMAIN
					,SMCHQ,LPAYD,LPAYA,TOTDWN,PAYDWN,(TOTDWN-PAYDWN) as KDWN,EXP_AMT,EXP_FRM,EXP_TO,EXP_PRD,CONTSTAT,TKANG,BILLCOLL
					,CHECKER,PAYTYP,T_NOPAY,CALINT,CALDSC,DELYRT,DLDAY,ADDRNO from {$this->MAuth->getdb('HARMAST')} where CONTNO = '".$contno."' and LOCAT ='".$locat."'
				)CONTRAC
			";
			//echo $sql31; exit;
			$this->db->query($sql31);
			
			$sql32 = "
				select '['+PAYCODE+']'+PAYDESC as PAY,* from #CONTRAC A 
				left join {$this->MAuth->getdb('PAYDUE')} B on A.PAYTYP = B.PAYCODE
			";
			//echo $sql32; exit;
			$query32 = $this->db->query($sql32);
			if($query32->row()){
				foreach($query32->result() as $row){
					$response['SDATE_3']  	 = $this->Convertdate(2,$row->SDATE);
					$response['TOTPRC_3'] 	 = number_format($row->TOTPRC,2);
					$response['SMPAY_3']  	 = number_format($row->SMPAY,2);
					$response['REMAIN_3'] 	 = number_format($row->REMAIN,2);
					$response['SMCHQ_3']  	 = number_format($row->SMCHQ,2);
					$response['LPAYD_3']  	 = $this->Convertdate(2,$row->LPAYD);
					$response['LPAYA_3']  	 = number_format($row->LPAYA,2);
					$response['TOTDWN_3']  	 = number_format($row->TOTDWN,2);
					$response['PAYDWN_3'] 	 = number_format($row->PAYDWN,2);
					$response['KDWN_3']  	 = number_format($row->KDWN,2);
					$response['EXP_AMT_3']   = number_format($row->EXP_AMT,2);
					$response['EXP_FRM_3']   = number_format($row->EXP_FRM);
					$response['EXP_TO_3']  	 = number_format($row->EXP_TO);
					$response['EXP_PRD_3']   = number_format($row->EXP_PRD);
					
					$response['CONTSTAT_3']  = $row->CONTSTAT;
					$response['PAYTYP_3']  	 = str_replace(chr(0),'',$row->PAY);
					$response['T_NOPAY_3']   = $row->T_NOPAY;
					$response['CALINT_3']    = $row->CALINT;
					$response['CALDSC_3']    = $row->CALDSC;
					$response['DELYRT_3']    = number_format($row->DELYRT,2);
					$response['DLDAY_3']     = $row->DLDAY;
					$response['ADDRNO_3']    = $row->ADDRNO;
				}
			}
			$sql33 = "
				select SUM(PAYAMT-SMPAY) as OTHR from {$this->MAuth->getdb('AROTHR')} 
				where CONTNO = '".$contno."' and LOCAT ='".$locat."'
			";
			//echo $sql33; exit;
			$query33 = $this->db->query($sql33);
			if($query33->row()){
				foreach($query33->result() as $row){
					$response['OTHR_3']  = number_format($row->OTHR,2);
				}
			}
			$sql34 = "
				select SALCOD,NCSHPRC,NPRICE,INTRT,GRDCOD from {$this->MAuth->getdb('ARMAST')} 
				where CONTNO = '".$contno."' and LOCAT ='".$locat."'
				union all
				select SALCOD,NCSHPRC,NPRICE,INTRT,GRDCOD from {$this->MAuth->getdb('HARMAST')} 
				where CONTNO = '".$contno."' and LOCAT ='".$locat."'
			";
			$query34 = $this->db->query($sql34);
			if($query34->row()){
				foreach($query34->result() as $row){
					$response['NCSHPRC_3']  = number_format($row->NCSHPRC,2);
					$response['NPRICE_3']   = number_format($row->NPRICE,2);
					$response['INTRT_3']    = number_format($row->INTRT,2);
					$response['GRDCOD_3']   = str_replace(chr(0),'',$row->GRDCOD);
				}
			}
			$sql35 = "
				select (select RTRIM(name) + '[' + RTRIM(code) + ']' 
				from {$this->MAuth->getdb('OFFICER')} B where B.CODE = A.BILLCOLL) as BILLCOLL 
				from #CONTRAC A left join {$this->MAuth->getdb('OFFICER')} B on A.BILLCOLL = B.CODE
			";
			$query35 = $this->db->query($sql35);
			if($query35->row()){
				foreach($query35->result() as $row){
					$response['bill'] = str_replace(chr(0),'',$row->BILLCOLL);
				}
			}
			$sql36 = "
				select (select RTRIM(name) + '[' + RTRIM(code) + ']' from {$this->MAuth->getdb('OFFICER')} B where B.CODE = A.CHECKER) as CHECKER 
				from #CONTRAC A left join {$this->MAuth->getdb('OFFICER')} B on A.CHECKER = B.CODE
			";
			$query36 = $this->db->query($sql36);
			if($query36->row()){
				foreach($query36->result() as $row){
					$response['check'] = str_replace(chr(0),'',$row->CHECKER);
				}
			}
			$sql37 = "
				select MEMO1 from {$this->MAuth->getdb('ARMAST')} 
				where CONTNO = '".$contno."' and LOCAT ='".$locat."'
			";
			$query37 = $this->db->query($sql37);
			if($query37->row()){
				foreach($query37->result() as $row){
					$response['MEMO1_3']   = $row->MEMO1;
				}
			}
			//สถานะรถ
			$sttcar = "
				select YSTAT from #CONTRAC
			";
			$querystt = $this->db->query($sttcar);
			if($querystt->row()){
				foreach($querystt->result() as $rows){
					$response['YSTAT'] = $rows->YSTAT;
				}
			}
			//tab4
			$sql41 = "
				IF OBJECT_ID('tempdb..#PAYMENT') IS NOT NULL DROP TABLE #PAYMENT
				select LOCATRECV,TMBILL,TMBILDT,PAYTYP,PAYFOR,PAYAMT,CUSCOD,DISCT,PAYINT,DSCINT
				,NETPAY,PAYDT,FLAG,CONTNO,CHQNO,BILLNO,BILLDT,F_PAY,L_PAY,INPDT
				into #PAYMENT
				FROM(
					select  B.LOCATRECV,B.TMBILL,CONVERT(varchar(8),B.TMBILDT,112) as TMBILDT,B.PAYTYP,B.PAYFOR
					,B.PAYAMT,B.CUSCOD,B.DISCT,B.PAYINT,B.DSCINT,B.NETPAY,CONVERT(varchar(8),B.PAYDT,112) as PAYDT
					,B.FLAG,B.CONTNO,B.CHQNO,A.BILLNO,CONVERT(varchar(8),A.BILLDT,112) as BILLDT,B.F_PAY,B.L_PAY
					,CONVERT(varchar(8),B.INPDT,112) as INPDT from  {$this->MAuth->getdb('CHQMAS')} A
					,{$this->MAuth->getdb('CHQTRAN')} B where  A.TMBILL =  B.TMBILL AND A.LOCATRECV = B.LOCATRECV 
					AND (B.CONTNO = '".$contno."' OR B.CONTNO = '".$contno."') AND  B.LOCATPAY ='".$locat."'
				)PAYMENT
			";	
			//echo $sql41; exit;
			$this->db->query($sql41);
			
			$sql42 = "
				select LOCATRECV,TMBILL,TMBILDT,PAYTYP,PAYFOR,PAYAMT,CUSCOD,DISCT,PAYINT,DSCINT
				,NETPAY,PAYDT,FLAG,CONTNO,CHQNO,BILLNO,BILLDT,F_PAY,L_PAY,INPDT from #PAYMENT 
				order by TMBILDT desc
			";
			$query42 = $this->db->query($sql42);
			$payment = "";
			if($query42->row()){
				foreach($query42->result() as $row){
					$payment .= "
						<tr class='trow' seq='old'>
							<td>".$row->TMBILL."</td>
							<td>".$this->Convertdate(2,$row->TMBILDT)."</td>
							<td>".$row->PAYTYP."</td>
							<td>".$row->PAYFOR."</td>
							<td></td>
							<td></td>
							<td align='right'>".number_format($row->PAYAMT,2)."</td>
							<td align='right'>".number_format($row->DISCT,2)."</td>
							<td align='right'>".number_format($row->PAYINT,2)."</td>
							<td align='right'>".number_format($row->DSCINT,2)."</td>
							<td align='right'>".number_format($row->NETPAY,2)."</td>
							<td>".$row->TMBILL."</td>
							<td>".$this->Convertdate(2,$row->PAYDT)."</td>
							<td>".$this->Convertdate(2,$row->BILLDT)."</td>
							<td>".$row->FLAG."</td>
							<td>".$row->F_PAY."</td>
							<td>".$row->L_PAY."</td>
							<td>".$this->Convertdate(2,$row->INPDT)."</td>
							<td>".$row->CUSCOD."</td>
							<td>".$row->CONTNO."</td>
						</tr>
						
					";	
				}
			}
			$response["payment"] = $payment;
			$sql43 = "
				select count(TMBILL) as countTMBILL from #PAYMENT
			";
			$query43 = $this->db->query($sql43);
			if($query43->row()){
				foreach($query43->result() as $row){
					$response['COUNTTMBILL'] = $row->countTMBILL;
				}
			}else{
				$response['COUNTTMBILL'] = '0';
			}
			$sql44 = "
				select TOTPRC,REMAIN,SMPAY,SMCHQ from #CONTRAC
			";
			$query44 = $this->db->query($sql44);
			if($query44->row()){
				foreach($query44->result() as $row){
					$response['TOTPRC_4'] = number_format($row->TOTPRC,2);
					$response['REMAIN_4'] = number_format($row->REMAIN,2);
					$response['SMPAY_4']  = number_format($row->SMPAY,2);
					$response['SMCHQ_4']  = number_format($row->SMCHQ,2);
				}
			}else{
				$response['TOTPRC_4'] = '0.00';
				$response['REMAIN_4'] = '0.00';
				$response['SMPAY_4']  = '0.00';
				$response['SMCHQ_4']  = '0.00';
			}
			//tab5
			$sql51 = "
				select CUSCOD,SNAM+NAME1+' '+NAME2+' ('+CUSCOD+')' as CUSNAME,CUSCOD,GROUP1,GRADE
				,convert(varchar(8),BIRTHDT,112) as BIRTHDT,NICKNM,ISSUBY
				,convert(varchar(8),ISSUDT,112) as ISSUDT,convert(varchar(8),EXPDT,112) as EXPDT,AGE
				,OCCUP,OFFIC,MAXCRED,YINCOME,MREVENU,YREVENU,ADDRNO3,MEMO1 from {$this->MAuth->getdb('CUSTMAST')} 
				where CUSCOD = '".$cuscod."'
			";
			$query51 = $this->db->query($sql51);
			if($query51->row()){
				foreach($query51->result() as $row){
					$response['CUSCOD_5'] 	= str_replace(chr(0),'',$row->CUSNAME);
					$response['GROUP1_5'] 	= str_replace(chr(0),'',$row->GROUP1);
					$response['GRADE_5'] 	= str_replace(chr(0),'',$row->GRADE);
					$response['BIRTHDT_5'] 	= $this->Convertdate(2,$row->BIRTHDT);
					$response['NICKNM_5'] 	= str_replace(chr(0),'',$row->NICKNM);
					$response['ISSUBY_5'] 	= str_replace(chr(0),'',$row->ISSUBY);
					$response['ISSUDT_5'] 	= $this->Convertdate(2,$row->ISSUDT);
					$response['EXPDT_5'] 	= $this->Convertdate(2,$row->EXPDT);
					$response['AGE_5'] 		= str_replace(chr(0),'',$row->AGE);
					$response['OCCUP_5'] 	= str_replace(chr(0),'',$row->OCCUP);
					$response['OFFIC_5'] 	= str_replace(chr(0),'',$row->OFFIC);
					$response['MAXCRED_5'] 	= number_format($row->MAXCRED,2);
					//$response['YINCOME_5'] 	= $row->YINCOME;
					$response['MREVENU_5'] 	= number_format($row->MREVENU,2);
					$response['YREVENU_5'] 	= number_format($row->YREVENU,2);
					$response['ADDRNO3_5'] 	= str_replace(chr(0),'',$row->ADDRNO3);
					$response['MEMO1_5'] 	= str_replace(chr(0),'',$row->MEMO1);
				}
			}	
			$sql52 = "
				select A.ADDRNO,A.ADDR1,A.ADDR2,A.TUMB,B.AUMPDES,C.PROVDES,A.ZIP,A.TELP,A.MEMO1
				from {$this->MAuth->getdb('CUSTADDR')} A left join {$this->MAuth->getdb('SETAUMP')} B on A.AUMPCOD=B.AUMPCOD 
				left join {$this->MAuth->getdb('SETPROV')} C on B.PROVCOD=C.PROVCOD where A.CUSCOD ='".$cuscod."'
				order by A.ADDRNO
			";
			$query52 = $this->db->query($sql52);
			$addrprice = "";
			if($query52->row()){
				foreach($query52->result() as $row){
					$addrprice .= "
						<tr class='trow' seq='old'>
							<td>".str_replace(chr(0),'',$row->ADDRNO)."</td>
							<td>".str_replace(chr(0),'',$row->ADDR1)."</td>
							<td>".str_replace(chr(0),'',$row->ADDR2)."</td>
							<td>".str_replace(chr(0),'',$row->TUMB)."</td>
							<td>".str_replace(chr(0),'',$row->AUMPDES)."</td>
							<td>".str_replace(chr(0),'',$row->PROVDES)."</td>
							<td>".str_replace(chr(0),'',$row->ZIP)."</td>
							<td>".str_replace(chr(0),'',$row->TELP)."</td>
						</tr>
					";
					$response['MEMO1ADR'] = $row->MEMO1;
				}
			}
			$response['addrprice'] = $addrprice;
			
			//tab6
			$sql61 = "
				IF OBJECT_ID('tempdb..#SUPPORTER') IS NOT NULL DROP TABLE #SUPPORTER
				select CONTNO,GARNO,CUSCOD,NAME1,NAME2,RELATN,GROUP1,GRADE,BIRTHDT,NICKNM,IDCARD
				,IDNO,ISSUBY,ISSUDT,EXPDT,AGE,OCCUP,OFFIC,MAXCRED,YINCOME,MREVENU,YREVENU
				,ADDRNO3,MEMO1
				into #SUPPORTER
				FROM(
					select A.CONTNO,A.GARNO,A.CUSCOD,B.NAME1, B.NAME2,A.RELATN 
					,B.GROUP1,B.GRADE,B.BIRTHDT,B.NICKNM,B.IDCARD,B.IDNO,B.ISSUBY
					,B.ISSUDT,B.EXPDT,B.AGE,B.OCCUP,B.OFFIC,B.MAXCRED,B.YINCOME
					,B.MREVENU,B.YREVENU,B.ADDRNO3,B.MEMO1
					from {$this->MAuth->getdb('ARMGAR')} A 
					left join {$this->MAuth->getdb('CUSTMAST')} B  on A.CUSCOD = B.CUSCOD
					where CONTNO = '".$contno."'
					union all
					select A.CONTNO,A.GARNO,A.CUSCOD,B.NAME1, B.NAME2,A.RELATN 
					,B.GROUP1,B.GRADE,B.BIRTHDT,B.NICKNM,B.IDCARD,B.IDNO,B.ISSUBY
					,B.ISSUDT,B.EXPDT,B.AGE,B.OCCUP,B.OFFIC,B.MAXCRED,B.YINCOME
					,B.MREVENU,B.YREVENU,B.ADDRNO3,B.MEMO1
					from {$this->MAuth->getdb('HARMGAR')} A 
					left join {$this->MAuth->getdb('CUSTMAST')} B  on A.CUSCOD = B.CUSCOD
					where CONTNO = '".$contno."'
				)SUPPORTER
			";
			//echo $sql20; exit;
			$this->db->query($sql61);
			
			$sql62 = "select * from #SUPPORTER";
			$query62 = $this->db->query($sql62);
			$supporter = "";
			$s = 0;
			if($query62->row()){
				foreach($query62->result() as $row){$s++;
					$supporter .= "
						<tr class='trow' seq='old'>
							<td>".$row->GARNO."</td>
							<td class='getspt' style='cursor:pointer;color:blue;'
								CUSCODSPT = '".str_replace(chr(0),'',$row->CUSCOD)."'
							>".$row->CUSCOD."</td>
							<td>".$row->NAME1."</td>
							<td>".$row->NAME2."</td>
							<td>".$row->RELATN."</td>
						</tr>
					";
				}
			}
			$response['supporter'] = $supporter;
			$response['numrow2'] = $s;
			$cond2 = "
				select top 1 CUSCOD from #SUPPORTER 
			";
			$cuscod2 = "";
			$querycond2 = $this->db->query($cond2);
			if($querycond2->row()){
				foreach($querycond2->result() as $row){
					$cuscod2 = $row->CUSCOD;
				}
			}
			$sql63 = "
				select GROUP1,GRADE,convert(varchar(8),BIRTHDT,112) as BIRTHDT,NICKNM,IDCARD,IDNO,ISSUBY
				,convert(varchar(8),ISSUDT,112) as ISSUDT,convert(varchar(8),EXPDT,112) as EXPDT,AGE 
				,OCCUP,OFFIC,MAXCRED,YINCOME,MREVENU,YREVENU,ADDRNO3,MEMO1
				from {$this->MAuth->getdb('CUSTMAST')} where CUSCOD = '".$cuscod2."' 
			";
			$query63 = $this->db->query($sql63);
			if($query63->row()){
				foreach($query63->result() as $row){
					$response['GROUP1_6'] 	= str_replace(chr(0),'',$row->GROUP1);
					$response['GRADE_6'] 	= str_replace(chr(0),'',$row->GRADE);
					$response['BIRTHDT_6'] 	= $this->Convertdate(2,$row->BIRTHDT);
					$response['NICKNM_6'] 	= str_replace(chr(0),'',$row->NICKNM);
					$response['IDCARD_6'] 	= str_replace(chr(0),'',$row->IDCARD);
					$response['IDNO_6'] 	= str_replace(chr(0),'',$row->IDNO);
					$response['ISSUBY_6'] 	= str_replace(chr(0),'',$row->ISSUBY);
					$response['ISSUDT_6'] 	= $this->Convertdate(2,$row->ISSUDT);
					$response['EXPDT_6'] 	= $this->Convertdate(2,$row->EXPDT);
					$response['AGE_6'] 		= str_replace(chr(0),'',$row->AGE);
					$response['OCCUP_6'] 	= str_replace(chr(0),'',$row->OCCUP);
					$response['OFFIC_6'] 	= str_replace(chr(0),'',$row->OFFIC);
					$response['MAXCRED_6'] 	= number_format($row->MAXCRED,2);
					$response['YINCOME_6']	= number_format($row->YINCOME,2);
					$response['MREVENU_6'] 	= number_format($row->MREVENU,2);
					$response['YREVENU_6'] 	= number_format($row->YREVENU,2);
					$response['ADDRNO3_6'] 	= str_replace(chr(0),'',$row->ADDRNO3);
					$response['MEMO1_6'] 	= str_replace(chr(0),'',$row->MEMO1);
				}
			}
			$sql64 = "
				select A.ADDRNO,A.ADDR1,A.ADDR2,A.TUMB,B.AUMPDES,C.PROVDES,A.ZIP,A.TELP,A.MEMO1
				from {$this->MAuth->getdb('CUSTADDR')} A left join {$this->MAuth->getdb('SETAUMP')} B on A.AUMPCOD=B.AUMPCOD 
				left join {$this->MAuth->getdb('SETPROV')} C on B.PROVCOD=C.PROVCOD where A.CUSCOD ='".$cuscod2."'
				order by A.ADDRNO
			";
			$query64 = $this->db->query($sql64);
			$addrspt = "";
			if($query64->row()){
				foreach($query64->result() as $row){
					$addrspt .= "
						<tr class='trow' seq='old'>
							<td>".str_replace(chr(0),'',$row->ADDRNO)."</td>
							<td>".str_replace(chr(0),'',$row->ADDR1)."</td>
							<td>".str_replace(chr(0),'',$row->ADDR2)."</td>
							<td>".str_replace(chr(0),'',$row->TUMB)."</td>
							<td>".str_replace(chr(0),'',$row->AUMPDES)."</td>
							<td>".str_replace(chr(0),'',$row->PROVDES)."</td>
							<td>".str_replace(chr(0),'',$row->ZIP)."</td>
							<td>".str_replace(chr(0),'',$row->TELP)."</td>
						</tr>
					";
					$response['MEMO1ADR_6'] = str_replace(chr(0),'',$row->MEMO1);
				}
			}
			$response['addrspt'] = $addrspt;
			//tab7
			$sql71 = "
				select CONVERT(varchar(8),A.CHGDATE,112) as CHGDATE,A.STATFRM,A.STATTO
				,(select RTRIM(name) + '[' + RTRIM(code) + ']' from {$this->MAuth->getdb('OFFICER')} C where C.CODE = A.FRMBILL) as BILLFRM
				,(select RTRIM(name) + '[' + RTRIM(code) + ']' from {$this->MAuth->getdb('OFFICER')} C where C.CODE = A.TOBILL) as BILLTO
				,A.MEMO1 ,RTRIM(B.USERNAME) + '[' + RTRIM(B.CUSCOD) +']' as USERNAME from {$this->MAuth->getdb('STATTRAN')} A
				,{$this->MAuth->getdb('PASSWRD')} B where A.AUTHCOD = B.USERID and A.CONTNO = '".$contno."' 
				and A.LOCAT = '".$locat."' order by A.CHGDATE
			";
			$compact = "";
			$query71 = $this->db->query($sql71);
			if($query71->row()){
				foreach($query71->result() as $row){
					$compact .="
						<tr class='trow' seq='old'>
							<td>".$this->Convertdate(2,$row->CHGDATE)."</td>
							<td>".$row->STATFRM."</td>
							<td>".$row->STATTO."</td>
							<td>".$row->BILLFRM."</td>
							<td>".$row->BILLTO."</td>
							<td>".$row->USERNAME."</td>
						</tr>
					";
				}
			}
			$response['compact'] = $compact;
			
			//tab8
			$sql81 = "
				IF OBJECT_ID('tempdb..#DEBTORS') IS NOT NULL DROP TABLE #DEBTORS
				select CONTNO,ARCONT,LOCAT,TOTPRC,SMPAY,BALANC,SMCHQ,TKANG,TSALE,CUSCOD,PAYFOR
				into #DEBTORS
				FROM(
					select A.CONTNO,A.ARCONT,A.LOCAT,A.PAYAMT AS TOTPRC,A.SMPAY,(A.PAYAMT - A.SMPAY) AS BALANC
					,A.SMCHQ,(A.PAYAMT - (A.SMPAY + A.SMCHQ)) AS TKANG, A.TSALE,A.CUSCOD,A.PAYFOR  
					from  {$this->MAuth->getdb('AROTHR')} A 
					WHERE A.CONTNO = '".$contno."' 
				)DEBTORS
			";
			$this->db->query($sql81);
			
			$sql82 = "select * from #DEBTORS order by CONTNO";
			$query82 = $this->db->query($sql82);
			$debtors = "";
			if($query82->row()){
				foreach($query82->result() as $row){
					$debtors .= "
						<tr class='trow' seq='old'>
							<td>".$row->CONTNO."</td>
							<td>".$row->ARCONT."</td>
							<td>".$row->LOCAT."</td>
							<td align='right'>".number_format($row->TOTPRC,2)."</td>
							<td align='right'>".number_format($row->SMPAY,2)."</td>
							<td align='right'>".number_format($row->BALANC,2)."</td>
							<td align='right'>".number_format($row->SMCHQ,2)."</td>
							<td align='right'>".number_format($row->TKANG,2)."</td>
							<td>".$row->PAYFOR."</td>
						</tr>
					";
				}
			}
			$response['debtors'] = $debtors;
			
			$sql83 = "
				select SUM(TOTPRC) as SUMTOTPRC,SUM(SMPAY) as SUMSMPAY,SUM(BALANC) as SUMBALANC
				,SUM(SMCHQ) as SUMSMCHQ,SUM(TKANG) as SUMTKANG from #DEBTORS
			";
			$query83 = $this->db->query($sql83);
			if($query83->row()){
				foreach($query83->result() as $row){
					$response['SUMTOTPRC_8']  = number_format($row->SUMTOTPRC,2);
					$response['SUMSMPAY_8']   = number_format($row->SUMSMPAY,2);
					$response['SUMBALANC_8']  = number_format($row->SUMBALANC,2);
					$response['SUMSMCHQ_8']   = number_format($row->SUMSMCHQ,2);
					$response['SUMTKANG_8']   = number_format($row->SUMTKANG,2);
				}
			}
		}else{
			$insurance = "";
			$insurance .= "<tr class='trow'><td colspan='9' style='color:red;'>ไม่มีข้อมูล</td></tr>";
		}
		$response["insurance"] = $insurance;
		echo json_encode ($response);
	}
	function changedata(){
		$CUSCODS = $_REQUEST['CUSCODS'];
		$CONTNOS = $_REQUEST['CONTNOS'];
		$STRNOS  = $_REQUEST['STRNOS'];
		$LOCATS  = $_REQUEST['LOCATS'];
		$TSALES  = $_REQUEST['TSALES'];
		$response = array();
		$sql = "
			IF OBJECT_ID('tempdb..#RPFN1') IS NOT NULL DROP TABLE #RPFN1
			select CONTNO,CUSCOD,TYPESALE,LOCAT,convert(varchar(8),SDATE,112) as SDATE,TOTPRC,SMPAY,BALANCE,SMCHQ,TKANG,STRNO,RESVNO,TSALE,FL
			into #RPFN1
			FROM(
				select A. * from (
					select CONTNO,CUSCOD,'ขายผ่อน' as TYPESALE,LOCAT,SDATE,TOTPRC,SMPAY,(TOTPRC-SMPAY) as BALANCE
					,SMCHQ,TKANG,STRNO,RESVNO,TSALE,'' as FL from {$this->MAuth->getdb('ARMAST')} where DELDT is null and CUSCOD like '".$CUSCODS."%' and 
					CONTNO like '".$CONTNOS."%' and STRNO like '".$STRNOS."%'
					union
					select CONTNO,CUSCOD,'ขายผ่อน' as TYPESALE,LOCAT,SDATE,TOTPRC,SMPAY,(TOTPRC-SMPAY) as BALANCE
					,SMCHQ,TKANG,STRNO,RESVNO,TSALE,'*' as FL from {$this->MAuth->getdb('HARMAST')} where DELDT is null and CUSCOD like '".$CUSCODS."%' and 
					CONTNO like '".$CONTNOS."%' and STRNO like '".$STRNOS."%'
					union 
					select CONTNO,CUSCOD,'ขายสด' as TYPESALE,LOCAT,SDATE,TOTPRC,SMPAY,(TOTPRC-SMPAY) as BALANCE
					,SMCHQ,TKANG,STRNO,RESVNO,TSALE,'' as FL from {$this->MAuth->getdb('ARCRED')} where DELDT is null and CUSCOD like '".$CUSCODS."%' and 
					CONTNO like '".$CONTNOS."%' and STRNO like '".$STRNOS."%'
					union 
					select CONTNO,CUSCOD,'ขายสด' as TYPESALE,LOCAT,SDATE,TOTPRC,SMPAY,(TOTPRC-SMPAY) as BALANCE
					,SMCHQ,TKANG,STRNO,RESVNO,TSALE,'*' as FL from {$this->MAuth->getdb('HARCRED')} where DELDT is null and CUSCOD like '".$CUSCODS."%' and 
					CONTNO like '".$CONTNOS."%' and STRNO like '".$STRNOS."%'
					union 
					select CONTNO,CUSCOD,'ขายไปแนนซ์' as TYPESALE,LOCAT,SDATE,TOTPRC,SMPAY,(TOTPRC-SMPAY) as BALANCE
					,SMCHQ,TKANG,STRNO,RESVNO,TSALE,'' as FL from {$this->MAuth->getdb('ARFINC')} where DELDT is null and CUSCOD like '".$CUSCODS."%' and 
					CONTNO like '".$CONTNOS."%' and STRNO like '".$STRNOS."%'
					union 
					select CONTNO,CUSCOD,'ขายไปแนนซ์' as TYPESALE,LOCAT,SDATE,TOTPRC,SMPAY,(TOTPRC-SMPAY) as BALANCE
					,SMCHQ,TKANG,STRNO,RESVNO,TSALE,'*' as FL from {$this->MAuth->getdb('HARFINC')} where DELDT is null and CUSCOD like '".$CUSCODS."%' and 
					CONTNO like '".$CONTNOS."%' and STRNO like '".$STRNOS."%'
					union 
					select A.CONTNO,A.CUSCOD,'ขายส่งเอเยนต์' as TYPESALE,A.LOCAT,A.SDATE,A.TOTPRC,A.SMPAY,(A.TOTPRC-A.SMPAY)
					as BALANCE,A.SMCHQ,0 as TKANG,B.STRNO,'' as RESVNO,A.TSALE,'' as FL from {$this->MAuth->getdb('AR_INVOI')} A,{$this->MAuth->getdb('INVTRAN')} B
					where A.DELDT is null and A.CONTNO = B.CONTNO and A.CUSCOD like '".$CUSCODS."%' and A.CONTNO like '".$CONTNOS."%' and B.STRNO like '".$STRNOS."%'
					union
					select A.CONTNO,A.CUSCOD,'ขายส่งเอเยนต์' as TYPESALE,A.LOCAT,A.SDATE,A.TOTPRC,A.SMPAY,(A.TOTPRC-A.SMPAY)
					as BALANCE,A.SMCHQ,0 as TKANG,B.STRNO,'*' as RESVNO,A.TSALE,'' as FL from {$this->MAuth->getdb('HAR_INVO')} A,{$this->MAuth->getdb('HINVTRAN')} B
					where A.DELDT is null and A.CONTNO = B.CONTNO and A.CUSCOD like '".$CUSCODS."%' and A.CONTNO like '".$CONTNOS."%' and B.STRNO like '".$STRNOS."%'
					union 	
					select A.ARCONT as CONTNO,A.CUSCOD,'ลูกหนี้อื่น' as TYPESALE,A.LOCAT,A.ARDATE as SDATE,A.PAYAMT
					as TOTPRC,A.SMPAY,(A.PAYAMT-A.SMPAY) as BALANCE,A.SMCHQ,0 as TKANG,'' as STRNO,'' as RESVNO,A.TSALE,'' as FL from {$this->MAuth->getdb('AROTHR')} A
					where CUSCOD like '".$CUSCODS."%' and CONTNO like '".$CONTNOS."%'
				) AS A 
				left join {$this->MAuth->getdb('REGTAB')} B ON A.STRNO=B.STRNO  
				Where (B.REGNO like '%' or (B.REGNO is null) ) and A.STRNO like '".$STRNOS."%' 
			)RPFN1
		";
		//echo $sql; exit;
		$this->db->query($sql);
		
		$response['CONTNO_2']   = $CONTNOS;
		$response['LOCAT_2']  	= $LOCATS;
		$response['CONTNO_3'] 	= $CONTNOS;
		$response['LOCAT_3']  	= $LOCATS;
		$response['CONTNO_4']   = $CONTNOS;
		$response['LOCAT_4']  	= $LOCATS;
		$response['CONTNO_6']   = $CONTNOS;
		$response['LOCAT_6']  	= $LOCATS;
		$response['CONTNO_7']   = $CONTNOS;
		$response['LOCAT_7']  	= $LOCATS;
		$response['CONTNO_8']  	= $CONTNOS;
		
		$response['TSALES']     = $TSALES;
		
		$sql = "
			select SNAM,NAME1,NAME2 from {$this->MAuth->getdb('CUSTMAST')} 
			where CUSCOD = '".$CUSCODS."'
		";
		$queryname = $this->db->query($sql);
		$rown = $queryname->row();
		$response['SNAM']		 = $rown->SNAM;
		$response['NAME1']		 = $rown->NAME1;
		$response['NAME2']		 = $rown->NAME2;
			
		$sql = "
			select CONTNO, LOCAT, convert(nvarchar,STARTDT,112) as STARTDT, convert(nvarchar,ENDDT,112) as ENDDT, MEMO1, USERID
			from {$this->MAuth->getdb('ALERTMSG')} 
			where CONTNO = '".$CONTNOS."' and GETDATE() between STARTDT and ENDDT
		";
		//echo $sql5; exit;
		//ข้อความแจ้งเตือน
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$response["CONTNO"]     = $row->CONTNO;
				$response["MSGLOCAT"] 	= $row->LOCAT;
				$response["STARTDT"] 	= $row->STARTDT;
				$response["ENDDT"] 		= $row->ENDDT;
				$response["MSGMEMO"] 	= $row->MEMO1;
				$response["USERID"] 	= $row->USERID;
			}
		}else{
			$response["MSGMEMO"] = "none";
		}
		//tab2
		$sql2 = "
			IF OBJECT_ID('tempdb..#CDT') IS NOT NULL DROP TABLE #CDT
			select CONTNO,RVLOCAT,STRNO,ENGNO,NADDCOST,TYPE,MODEL,BAAB,COLOR,CC,STAT,MANUYR,YSTAT,RECVNO,GCODE
			into #CDT
			FROM(
				select CONTNO,RVLOCAT,STRNO,ENGNO,NADDCOST,TYPE,MODEL,BAAB,COLOR,CC,STAT,MANUYR,YSTAT
				,RECVNO,GCODE from {$this->MAuth->getdb('INVTRAN')} where STRNO = '".$STRNOS."'
				union
				select CONTNO,RVLOCAT,STRNO,ENGNO,NADDCOST,TYPE,MODEL,BAAB,COLOR,CC,STAT,MANUYR,YSTAT
				,RECVNO,GCODE from {$this->MAuth->getdb('HINVTRAN')} where STRNO = '".$STRNOS."'
			)CDT
		";
		//echo $sql2; exit;
		$this->db->query($sql2);
		
		$sql2_1 = "select * from #CDT";
		$recvno = ""; $gcode  = ""; $stat = ""; $ystat = "";
		$query2_1 = $this->db->query($sql2_1);
		if($query2_1->row()){
			foreach($query2_1->result() as $row){
				$response['STRNO_2']    = str_replace(chr(0),'',$row->STRNO);
				$response['ENGNO_2']    = str_replace(chr(0),'',$row->ENGNO);
				$response['NADDCOST_2'] = number_format($row->NADDCOST,2);
				$response['TYPE_2']     = str_replace(chr(0),'',$row->TYPE);
				$response['MODEL_2']    = str_replace(chr(0),'',$row->MODEL);
				$response['BAAB_2']     = str_replace(chr(0),'',$row->BAAB);
				$response['COLOR_2']    = str_replace(chr(0),'',$row->COLOR);
				$response['CC_2']       = str_replace(chr(0),'',$row->CC);
				$stat					= str_replace(chr(0),'',$row->STAT);
				$ystat   				= str_replace(chr(0),'',$row->YSTAT);
				$response['MANUYR_2']   = str_replace(chr(0),'',$row->MANUYR);
				
				$recvno					= str_replace(chr(0),'',$row->RECVNO);
				$gcode					= str_replace(chr(0),'',$row->GCODE);
			}
		}
		if($stat == 'O' && $ystat !== 'Y'){
			$response['STAT_2'] = "เก่า";
		}else if($stat == 'N' && $ystat !== 'Y'){
			$response['STAT_2'] = "ใหม่";
		}else if($stat == 'O' && $ystat == 'Y'){
			$response['STAT_2'] = "เก่า : เป็นรถยึด";
		}else if($stat == 'N' && $ystat == 'Y'){
			$response['STAT_2'] = "ใหม่ : เป็นรถยึด";
		}
		$sql2_2 = "
			select APNAME from {$this->MAuth->getdb('INVINVO')} A,{$this->MAuth->getdb('APMAST')} B 
			where RECVNO = '".$recvno."' and A.APCODE = B.APCODE 
		";
		$query2_2 = $this->db->query($sql2_2);
		if($query2_2->row()){
			foreach($query2_2->result() as $row){
				$response['APNAME_2']    = str_replace(chr(0),'',$row->APNAME);
			}
		}
		$sql2_3 = "
			select GDESC from {$this->MAuth->getdb('SETGROUP')} where GCODE = '".$gcode."' 
		";
		$query2_3 = $this->db->query($sql2_3);
		if($query2_3->row()){
			foreach($query2_3->result() as $row){
				$response['GDESC_2']    = str_replace(chr(0),'',$row->GDESC);
			}
		}
		$sql2_4 = "
			select REGNO,REGPAY,convert(varchar(8),REGYEAR,112) as REGYEAR,convert(varchar(8),REGEXP,112) as REGEXP
			,REGTYP,convert(varchar(8),GARFRM,112) as GARFRM,convert(varchar(8),GAREXP,112) as GAREXP
			,GARNO3,convert(varchar(8),GAR3FRM,112) as GAR3FRM,convert(varchar(8),GAR3EXP,112) as GAR3EXP
			from {$this->MAuth->getdb('REGTAB')} where STRNO = '".$STRNOS."' 
		";
		//echo $sql2_4; exit;
		$query2_4 = $this->db->query($sql2_4);
		if($query2_4->row()){
			foreach($query2_4->result() as $row){
				$response['REGNO_2']    = str_replace(chr(0),'',$row->REGNO); 
				$response['REGYEAR_2']  = $this->Convertdate(2,$row->REGYEAR); 
				$response['REGEXP_2']   = $this->Convertdate(2,$row->REGEXP); 
				$response['REGTYP_2']   = str_replace(chr(0),'',$row->REGTYP); 
				$response['GARFRM_2']   = $this->Convertdate(2,$row->GARFRM); 
				$response['GAREXP_2']   = $this->Convertdate(2,$row->GAREXP); 
				$response['GARNO3_2']   = str_replace(chr(0),'',$row->GARNO3); 
				$response['GAR3FRM_2']  = $this->Convertdate(2,$row->GAR3FRM);
				$response['GAR3EXP_2']  = $this->Convertdate(2,$row->GAR3EXP);
			}
		}
		$sql2_5 = "
			IF OBJECT_ID('tempdb..#ACCESSORY') IS NOT NULL DROP TABLE #ACCESSORY
			select OPTCODE,OPTNAME,UPRICE,QTY,TOTPRC,PRCPU
			into #ACCESSORY
			from (
				select A.OPTCODE,B.OPTNAME,A.UPRICE,A.QTY,A.TOTPRC,(A.TOTPRC/A.QTY) as PRCPU from {$this->MAuth->getdb('ARINOPT')} A
				left join {$this->MAuth->getdb('OPTMAST')} B on A.OPTCODE = B.OPTCODE 
				where A.CONTNO like '".$CONTNOS."%' and B.LOCAT like '".$LOCATS."%'
			)ACCESSORY
		";
		//echo $sql3; exit;
		$this->db->query($sql2_5);
		
		$sql2_6 = "
			select OPTCODE,OPTNAME,UPRICE,QTY,TOTPRC from #ACCESSORY
		";
		//echo $sql4; exit;
		$query2_6 = $this->db->query($sql2_6);
		$acce = "";
		if($query2_6->row()){
			foreach($query2_6->result() as $row){
				$acce .= "
					<tr class='trow' seq='old'>
						<td>".$row->OPTCODE."</td>
						<td>".$row->OPTNAME."</td>
						<td align ='right'>".number_format($row->UPRICE,2)."</td>
						<td align ='right'>".number_format($row->QTY,2)."</td>
						<td align ='right'>".number_format($row->TOTPRC,2)."</td>
					</tr>
				";	
			}
		}
		$response["acce"] = $acce;
		$sql2_7 = "select count(OPTCODE) as countOPTCODE,sum(TOTPRC) as sumTOTPRC from #ACCESSORY";
		$query2_7 = $this->db->query($sql2_7);
		if($query2_7->row()){
			foreach($query2_7->result() as $row){
				$response['COUNTOPTCODE_2']   = $row->countOPTCODE;
				$response['SUMTOTPRC_2']	  = number_format($row->sumTOTPRC,2);
			}
		}
		//tab3
		$sql3_1 = "
			IF OBJECT_ID('tempdb..#CONTRAC') IS NOT NULL DROP TABLE #CONTRAC
			select CONTNO,LOCAT,YSTAT,TSALE,CONVERT(varchar(8),SDATE,112) as SDATE,TOTPRC,SMPAY,REMAIN,SMCHQ,CONVERT(varchar(8),LPAYD,112) 
			as LPAYD,LPAYA,TOTDWN,PAYDWN,KDWN,EXP_AMT,EXP_FRM,EXP_TO,EXP_PRD,CONTSTAT,TKANG,BILLCOLL,CHECKER,PAYTYP
			,T_NOPAY,CALINT,CALDSC,DELYRT,DLDAY,ADDRNO
			into #CONTRAC
			FROM(
				select CONTNO,LOCAT,'' as YSTAT,TSALE,SDATE,TOTPRC,SMPAY,(TOTPRC-SMPAY) as REMAIN
				,SMCHQ,LPAYDT as LPAYD,0 as LPAYA,0 as TOTDWN,0 as PAYDWN,0 as KDWN,0 as EXP_AMT,0 as EXP_FRM,0 as EXP_TO
				,0 as EXP_PRD,'' as CONTSTAT,TKANG,'' as BILLCOLL,'' as CHECKER,'' as PAYTYP,0 as T_NOPAY,'' as CALINT
				,'' as CALDSC,0 as DELYRT,0 as DLDAY,ADDRNO from {$this->MAuth->getdb('ARCRED')} where CONTNO = '".$CONTNOS."' and LOCAT ='".$LOCATS."'
				union 
				select CONTNO,LOCAT,'' as YSTAT,TSALE,SDATE,TOTPRC,SMPAY,(TOTPRC-SMPAY) as REMAIN
				,SMCHQ,LPAYDT as LPAYD,0 as LPAYA,0 as TOTDWN,0 as PAYDWN,0 as KDWN,0 as EXP_AMT,0 as EXP_FRM,0 as EXP_TO
				,0 as EXP_PRD,'' as CONTSTAT,TKANG,'' as BILLCOLL,'' as CHECKER,'' as PAYTYP,0 as T_NOPAY,'' as CALINT
				,'' as CALDSC,0 as DELYRT,0 as DLDAY,ADDRNO from {$this->MAuth->getdb('HARCRED')} where CONTNO = '".$CONTNOS."' and LOCAT ='".$LOCATS."'
				union
				select CONTNO,LOCAT,'' as YSTAT,TSALE,SDATE,TOTPRC,SMPAY,(TOTPRC-SMPAY) as REMAIN
				,SMCHQ,LPAYD as LPAYD,0 as LPAYA,0 as TOTDWN,0 as PAYDWN,0 as KDWN,0 as EXP_AMT,0 as EXP_FRM,0 as EXP_TO
				,0 as EXP_PRD,'' as CONTSTAT,TKANG,'' as BILLCOLL,'' as CHECKER,'' as PAYTYP,0 as T_NOPAY,'' as CALINT
				,'' as CALDSC,0 as DELYRT,0 as DLDAY,ADDRNO from {$this->MAuth->getdb('ARFINC')} where CONTNO = '".$CONTNOS."' and LOCAT ='".$LOCATS."'
				union
				select CONTNO,LOCAT,'' as YSTAT,TSALE,SDATE,TOTPRC,SMPAY,(TOTPRC-SMPAY) as REMAIN
				,SMCHQ,LPAYD as LPAYD,0 as LPAYA,0 as TOTDWN,0 as PAYDWN,0 as KDWN,0 as EXP_AMT,0 as EXP_FRM,0 as EXP_TO
				,0 as EXP_PRD,'' as CONTSTAT,TKANG,'' as BILLCOLL,'' as CHECKER,'' as PAYTYP,0 as T_NOPAY,'' as CALINT
				,'' as CALDSC,0 as DELYRT,0 as DLDAY,ADDRNO from {$this->MAuth->getdb('HARFINC')} where CONTNO = '".$CONTNOS."' and LOCAT ='".$LOCATS."'
				union
				select CONTNO,LOCAT,'' as YSTAT,TSALE,SDATE,TOTPRC,SMPAY,(TOTPRC-SMPAY) as REMAIN
				,SMCHQ,LPAYDT as LPAYD,0 as LPAYA,0 as TOTDWN,0 as PAYDWN,0 as KDWN,0 as EXP_AMT,0 as EXP_FRM,0 as EXP_TO
				,0 as EXP_PRD,'' as CONTSTAT,0 as TKANG,'' as BILLCOLL,'' as CHECKER,'' as PAYTYP,0 as T_NOPAY,'' as CALINT
				,'' as CALDSC,0 as DELYRT,0 as DLDAY,ADDRNO from {$this->MAuth->getdb('AR_INVOI')} where CONTNO = '".$CONTNOS."' and LOCAT ='".$LOCATS."'
				union
				select CONTNO,LOCAT,'' as YSTAT,TSALE,SDATE,TOTPRC,SMPAY,(TOTPRC-SMPAY) as REMAIN
				,SMCHQ,LPAYDT as LPAYD,0 as LPAYA,0 as TOTDWN,0 as PAYDWN,0 as KDWN,0 as EXP_AMT,0 as EXP_FRM,0 as EXP_TO
				,0 as EXP_PRD,'' as CONTSTAT,0 as TKANG,'' as BILLCOLL,'' as CHECKER,'' as PAYTYP,0 as T_NOPAY,'' as CALINT
				,'' as CALDSC,0 as DELYRT,0 as DLDAY,ADDRNO from {$this->MAuth->getdb('HAR_INVO')} where CONTNO = '".$CONTNOS."' and LOCAT ='".$LOCATS."'
				union  
				select CONTNO,LOCAT,YSTAT,TSALE,SDATE,TOTPRC,SMPAY,(TOTPRC-SMPAY) as REMAIN,SMCHQ,LPAYD,LPAYA,TOTDWN,PAYDWN,(TOTDWN-PAYDWN) as KDWN
				,EXP_AMT,EXP_FRM,EXP_TO,EXP_PRD,CONTSTAT,TKANG,BILLCOLL,CHECKER,PAYTYP,T_NOPAY,CALINT,CALDSC,DELYRT,DLDAY,ADDRNO from {$this->MAuth->getdb('ARMAST')}
				where CONTNO = '".$CONTNOS."' and LOCAT ='".$LOCATS."'
				union 
				select CONTNO,LOCAT,(case when YSTAT = 'Y' then 'H' else YSTAT end) as YSTAT,TSALE,SDATE,TOTPRC,SMPAY,(TOTPRC-SMPAY) as REMAIN
				,SMCHQ,LPAYD,LPAYA,TOTDWN,PAYDWN,(TOTDWN-PAYDWN) as KDWN,EXP_AMT,EXP_FRM,EXP_TO,EXP_PRD,CONTSTAT,TKANG,BILLCOLL
				,CHECKER,PAYTYP,T_NOPAY,CALINT,CALDSC,DELYRT,DLDAY,ADDRNO from {$this->MAuth->getdb('HARMAST')} where CONTNO = '".$CONTNOS."' and LOCAT ='".$LOCATS."'
			)CONTRAC
		";
		//echo $sql3_1; exit;
		$this->db->query($sql3_1);
		
		$sql3_2 = "
			select '['+PAYCODE+']'+PAYDESC as PAY,* from #CONTRAC A 
			left join {$this->MAuth->getdb('PAYDUE')} B on A.PAYTYP = B.PAYCODE
		";
		$query3_2 = $this->db->query($sql3_2);
		if($query3_2->row()){
			foreach($query3_2->result() as $row){
				$response['SDATE_3']  	 = $this->Convertdate(2,$row->SDATE);
				$response['TOTPRC_3'] 	 = number_format($row->TOTPRC,2);
				$response['SMPAY_3']  	 = number_format($row->SMPAY,2);
				$response['REMAIN_3'] 	 = number_format($row->REMAIN,2);
				$response['SMCHQ_3']  	 = number_format($row->SMCHQ,2);
				$response['LPAYD_3']  	 = $this->Convertdate(2,$row->LPAYD);
				$response['LPAYA_3']  	 = number_format($row->LPAYA,2);
				$response['TOTDWN_3']  	 = number_format($row->TOTDWN,2);
				$response['PAYDWN_3'] 	 = number_format($row->PAYDWN,2);
				$response['KDWN_3']  	 = number_format($row->KDWN,2);
				$response['EXP_AMT_3']   = number_format($row->EXP_AMT,2);
				$response['EXP_FRM_3']   = number_format($row->EXP_FRM);
				$response['EXP_TO_3']  	 = number_format($row->EXP_TO);
				$response['EXP_PRD_3']   = number_format($row->EXP_PRD);
				
				$response['CONTSTAT_3']  = $row->CONTSTAT;
				$response['PAYTYP_3']  	 = str_replace(chr(0),'',$row->PAY);
				$response['T_NOPAY_3']   = $row->T_NOPAY;
				$response['CALINT_3']    = $row->CALINT;
				$response['CALDSC_3']    = $row->CALDSC;
				$response['DELYRT_3']    = number_format($row->DELYRT,2);
				$response['DLDAY_3']     = $row->DLDAY;
				$response['ADDRNO_3']    = $row->ADDRNO;
			}
		}
		$sql3_3 = "
			select SUM(PAYAMT-SMPAY) as OTHR from {$this->MAuth->getdb('AROTHR')}
			where CONTNO = '".$CONTNOS."' and LOCAT ='".$LOCATS."'
		";
		$query3_3 = $this->db->query($sql3_3);
		if($query3_3->row()){
			foreach($query3_3->result() as $row){
				$response['OTHR_3']  = number_format($row->OTHR,2);
			}
		}
		$sql3_4 = "
			select SALCOD,NCSHPRC,NPRICE,INTRT,GRDCOD from {$this->MAuth->getdb('ARMAST')} 
			where CONTNO = '".$CONTNOS."' and LOCAT ='".$LOCATS."'
			union all
			select SALCOD,NCSHPRC,NPRICE,INTRT,GRDCOD from {$this->MAuth->getdb('HARMAST')} 
			where CONTNO = '".$CONTNOS."' and LOCAT ='".$LOCATS."'
		";
		$query3_4 = $this->db->query($sql3_4);
		if($query3_4->row()){
			foreach($query3_4->result() as $row){
				$response['NCSHPRC_3']  = number_format($row->NCSHPRC,2);
				$response['NPRICE_3']   = number_format($row->NPRICE,2);
				$response['INTRT_3']    = $row->INTRT;
				$response['GRDCOD_3']   = $row->GRDCOD;
			}
		}
		$sql3_5 = "
			select (select RTRIM(name) + '[' + RTRIM(code) + ']' from {$this->MAuth->getdb('OFFICER')} B 
			where B.CODE = A.BILLCOLL) as BILLCOLL 
			from #CONTRAC A left join {$this->MAuth->getdb('OFFICER')} B on A.BILLCOLL = B.CODE
		";
		$query3_5 = $this->db->query($sql3_5);
		if($query3_5->row()){
			foreach($query3_5->result() as $row){
				$response['bill'] = str_replace(chr(0),'',$row->BILLCOLL);
			}
		}
		$sql3_6 = "
			select (select RTRIM(name) + '[' + RTRIM(code) + ']' from {$this->MAuth->getdb('OFFICER')} B 
			where B.CODE = A.CHECKER) as CHECKER 
			from #CONTRAC A left join {$this->MAuth->getdb('OFFICER')} B on A.CHECKER = B.CODE
		";
		$query3_6 = $this->db->query($sql3_6);
		if($query3_6->row()){
			foreach($query3_6->result() as $row){
				$response['check'] = str_replace(chr(0),'',$row->CHECKER);
			}
		}
		$sql3_6 = "
			select MEMO1 from {$this->MAuth->getdb('ARMAST')} where CONTNO = '".$CONTNOS."' and LOCAT ='".$LOCATS."'
		";
		$query3_6 = $this->db->query($sql3_6);
		if($query3_6->row()){
			foreach($query3_6->result() as $row){
				$response['MEMO1_3']   = $row->MEMO1;
			}
		}
		//สถานะรถ
		$sql3_7 = "
			select YSTAT from #CONTRAC
		";
		$query3_7 = $this->db->query($sql3_7);
		if($query3_7->row()){
			foreach($query3_7->result() as $row){
				$response['YSTAT'] = $row->YSTAT;
			}
		}
		//tab4
		$sql4_1 = "
			IF OBJECT_ID('tempdb..#PAYMENT') IS NOT NULL DROP TABLE #PAYMENT
			select LOCATRECV,TMBILL,TMBILDT,PAYTYP,PAYFOR,PAYAMT,CUSCOD,DISCT,PAYINT,DSCINT
			,NETPAY,PAYDT,FLAG,CONTNO,CHQNO,BILLNO,BILLDT,F_PAY,L_PAY,INPDT
			into #PAYMENT
			FROM(
				select  B.LOCATRECV,B.TMBILL,CONVERT(varchar(8),B.TMBILDT,112) as TMBILDT,B.PAYTYP,B.PAYFOR
				,B.PAYAMT,B.CUSCOD,B.DISCT,B.PAYINT,B.DSCINT,B.NETPAY,CONVERT(varchar(8),B.PAYDT,112) as PAYDT
				,B.FLAG,B.CONTNO,B.CHQNO,A.BILLNO,CONVERT(varchar(8),A.BILLDT,112) as BILLDT,B.F_PAY,B.L_PAY
				,CONVERT(varchar(8),B.INPDT,112) as INPDT from  {$this->MAuth->getdb('CHQMAS')} A,{$this->MAuth->getdb('CHQTRAN')} B where  A.TMBILL =  B.TMBILL 
				AND A.LOCATRECV = B.LOCATRECV AND (B.CONTNO = '".$CONTNOS."' OR B.CONTNO = '".$CONTNOS."') AND  B.LOCATPAY ='".$LOCATS."'
			)PAYMENT
		";
		$this->db->query($sql4_1);
		
		$sql4_2 = "
			select LOCATRECV,TMBILL,TMBILDT,PAYTYP,PAYFOR,PAYAMT,CUSCOD,DISCT,PAYINT,DSCINT
			,NETPAY,PAYDT,FLAG,CONTNO,CHQNO,BILLNO,BILLDT,F_PAY,L_PAY,INPDT from #PAYMENT  order by TMBILDT desc
		";
		$query4_2 = $this->db->query($sql4_2);
		$payment = "";
		if($query4_2->row()){
			foreach($query4_2->result() as $row){
				$payment .= "
					<tr class='trow' seq='old'>
						<td>".$row->TMBILL."</td>
						<td>".$this->Convertdate(2,$row->TMBILDT)."</td>
						<td>".$row->PAYTYP."</td>
						<td>".$row->PAYFOR."</td>
						<td></td>
						<td></td>
						<td align='right'>".number_format($row->PAYAMT,2)."</td>
						<td align='right'>".number_format($row->DISCT,2)."</td>
						<td align='right'>".number_format($row->PAYINT,2)."</td>
						<td align='right'>".number_format($row->DSCINT,2)."</td>
						<td align='right'>".number_format($row->NETPAY,2)."</td>
						<td>".$row->TMBILL."</td>
						<td>".$this->Convertdate(2,$row->PAYDT)."</td>
						<td>".$this->Convertdate(2,$row->BILLDT)."</td>
						<td>".$row->FLAG."</td>
						<td>".$row->F_PAY."</td>
						<td>".$row->L_PAY."</td>
						<td>".$this->Convertdate(2,$row->INPDT)."</td>
						<td>".$row->CUSCOD."</td>
						<td>".$row->CONTNO."</td>
					</tr>
				";	
			}
		}
		$response["payment"] = $payment;
		$sql4_3 = "
			select count(TMBILL) as countTMBILL from #PAYMENT
		";
		$query4_3 = $this->db->query($sql4_3);
		if($query4_3->row()){
			foreach($query4_3->result() as $row){
				$response['COUNTTMBILL'] = $row->countTMBILL;
			}
		}else{
			$response['COUNTTMBILL'] = '0';
		}
		$sql4_4 = "
			select TOTPRC,REMAIN,SMPAY,SMCHQ from #CONTRAC
		";
		$query4_4 = $this->db->query($sql4_4);
		if($query4_4->row()){
			foreach($query4_4->result() as $row){
				$response['TOTPRC_4'] = number_format($row->TOTPRC,2);
				$response['REMAIN_4'] = number_format($row->REMAIN,2);
				$response['SMPAY_4']  = number_format($row->SMPAY,2);
				$response['SMCHQ_4']  = number_format($row->SMCHQ,2);
			}
		}else{
			$response['TOTPRC_4'] = '0.00';
			$response['REMAIN_4'] = '0.00';
			$response['SMPAY_4']  = '0.00';
			$response['SMCHQ_4']  = '0.00';
		}
		//tab5
		$sql51 = "
			select CUSCOD,SNAM+NAME1+' '+NAME2+' ('+CUSCOD+')' as CUSNAME,CUSCOD,GROUP1,GRADE
			,convert(varchar(8),BIRTHDT,112) as BIRTHDT,NICKNM,ISSUBY
			,convert(varchar(8),ISSUDT,112) as ISSUDT,convert(varchar(8),EXPDT,112) as EXPDT,AGE
			,OCCUP,OFFIC,MAXCRED,YINCOME,MREVENU,YREVENU,ADDRNO3,MEMO1 from {$this->MAuth->getdb('CUSTMAST')} 
			where CUSCOD = '".$CUSCODS."'
		";
		$query51 = $this->db->query($sql51);
		if($query51->row()){
			foreach($query51->result() as $row){
				$response['CUSCOD_5'] 	= str_replace(chr(0),'',$row->CUSNAME);
				$response['GROUP1_5'] 	= str_replace(chr(0),'',$row->GROUP1);
				$response['GRADE_5'] 	= str_replace(chr(0),'',$row->GRADE);
				$response['BIRTHDT_5'] 	= $this->Convertdate(2,$row->BIRTHDT);
				$response['NICKNM_5'] 	= str_replace(chr(0),'',$row->NICKNM);
				$response['ISSUBY_5'] 	= str_replace(chr(0),'',$row->ISSUBY);
				$response['ISSUDT_5'] 	= $this->Convertdate(2,$row->ISSUDT);
				$response['EXPDT_5'] 	= $this->Convertdate(2,$row->EXPDT);
				$response['AGE_5'] 		= str_replace(chr(0),'',$row->AGE);
				$response['OCCUP_5'] 	= str_replace(chr(0),'',$row->OCCUP);
				$response['OFFIC_5'] 	= str_replace(chr(0),'',$row->OFFIC);
				$response['MAXCRED_5'] 	= number_format($row->MAXCRED,2);
				//$response['YINCOME_5'] 	= $row->YINCOME;
				$response['MREVENU_5'] 	= number_format($row->MREVENU,2);
				$response['YREVENU_5'] 	= number_format($row->YREVENU,2);
				$response['ADDRNO3_5'] 	= str_replace(chr(0),'',$row->ADDRNO3);
				$response['MEMO1_5'] 	= str_replace(chr(0),'',$row->MEMO1);
			}
		}	
		$sql52 = "
			select A.ADDRNO,A.ADDR1,A.ADDR2,A.TUMB,B.AUMPDES,C.PROVDES,A.ZIP,A.TELP,A.MEMO1
			from {$this->MAuth->getdb('CUSTADDR')} A left join {$this->MAuth->getdb('SETAUMP')} B on A.AUMPCOD=B.AUMPCOD 
			left join {$this->MAuth->getdb('SETPROV')} C on B.PROVCOD=C.PROVCOD where A.CUSCOD ='".$CUSCODS."'
			order by A.ADDRNO
		";
		$query52 = $this->db->query($sql52);
		$addrprice = "";
		if($query52->row()){
			foreach($query52->result() as $row){
				$addrprice .= "
					<tr class='trow' seq='old'>
						<td>".str_replace(chr(0),'',$row->ADDRNO)."</td>
						<td>".str_replace(chr(0),'',$row->ADDR1)."</td>
						<td>".str_replace(chr(0),'',$row->ADDR2)."</td>
						<td>".str_replace(chr(0),'',$row->TUMB)."</td>
						<td>".str_replace(chr(0),'',$row->AUMPDES)."</td>
						<td>".str_replace(chr(0),'',$row->PROVDES)."</td>
						<td>".str_replace(chr(0),'',$row->ZIP)."</td>
						<td>".str_replace(chr(0),'',$row->TELP)."</td>
					</tr>
				";
				$response['MEMO1ADR'] = $row->MEMO1;
			}
		}
		$response['addrprice'] = $addrprice;
		//tab6
		$sql6_1 = "
			IF OBJECT_ID('tempdb..#SUPPORTER') IS NOT NULL DROP TABLE #SUPPORTER
			select CONTNO,GARNO,CUSCOD,NAME1,NAME2,RELATN,GROUP1,GRADE,BIRTHDT,NICKNM,IDCARD
			,IDNO,ISSUBY,ISSUDT,EXPDT,AGE,OCCUP,OFFIC,MAXCRED,YINCOME,MREVENU,YREVENU
			,ADDRNO3,MEMO1
			into #SUPPORTER
			FROM(
				select A.CONTNO,A.GARNO,A.CUSCOD,B.NAME1, B.NAME2,A.RELATN 
				,B.GROUP1,B.GRADE,B.BIRTHDT,B.NICKNM,B.IDCARD,B.IDNO,B.ISSUBY
				,B.ISSUDT,B.EXPDT,B.AGE,B.OCCUP,B.OFFIC,B.MAXCRED,B.YINCOME
				,B.MREVENU,B.YREVENU,B.ADDRNO3,B.MEMO1
				from {$this->MAuth->getdb('ARMGAR')} A 
				left join {$this->MAuth->getdb('CUSTMAST')} B  on A.CUSCOD = B.CUSCOD
				where CONTNO = '".$CONTNOS."'
				union all
				select A.CONTNO,A.GARNO,A.CUSCOD,B.NAME1, B.NAME2,A.RELATN 
				,B.GROUP1,B.GRADE,B.BIRTHDT,B.NICKNM,B.IDCARD,B.IDNO,B.ISSUBY
				,B.ISSUDT,B.EXPDT,B.AGE,B.OCCUP,B.OFFIC,B.MAXCRED,B.YINCOME
				,B.MREVENU,B.YREVENU,B.ADDRNO3,B.MEMO1
				from {$this->MAuth->getdb('HARMGAR')} A 
				left join {$this->MAuth->getdb('CUSTMAST')} B  on A.CUSCOD = B.CUSCOD
				where CONTNO = '".$CONTNOS."'
			)SUPPORTER
		";
		//echo $sql6_1; exit;
		$this->db->query($sql6_1);
		
		$sql6_2 = "select * from #SUPPORTER";
		$query6_2 = $this->db->query($sql6_2);
		$supporter = "";
		if($query6_2->row()){
			foreach($query6_2->result() as $row){
				$supporter .= "
					<tr class='trow' seq='old'>
						<td>".$row->GARNO."</td>
						<td>".$row->CUSCOD."</td>
						<td>".$row->NAME1."</td>
						<td>".$row->NAME2."</td>
						<td>".$row->RELATN."</td>
					</tr>
				";
			}
		}
		$response['supporter'] = $supporter;
		
		$cond2 = "
			select top 1 CUSCOD from #SUPPORTER 
		";
		$cuscod2 = "";
		$querycond2 = $this->db->query($cond2);
		if($querycond2->row()){
			foreach($querycond2->result() as $row){
				$cuscod2 = $row->CUSCOD;
			}
		}
		$sql6_3 = "
			select GROUP1,GRADE,convert(varchar(8),BIRTHDT,112) as BIRTHDT,NICKNM,IDCARD,IDNO,ISSUBY
			,convert(varchar(8),ISSUDT,112) as ISSUDT,convert(varchar(8),EXPDT,112) as EXPDT,AGE 
			,OCCUP,OFFIC,MAXCRED,YINCOME,MREVENU,YREVENU,ADDRNO3,MEMO1
			from {$this->MAuth->getdb('CUSTMAST')} where CUSCOD = '".$cuscod2."' 
		";
		$query6_3 = $this->db->query($sql6_3);
		if($query6_3->row()){
			foreach($query6_3->result() as $row){
				$response['GROUP1_6'] 	= $row->GROUP1;
				$response['GRADE_6'] 	= $row->GRADE;
				$response['BIRTHDT_6'] 	= $this->Convertdate(2,$row->BIRTHDT);
				$response['NICKNM_6'] 	= $row->NICKNM;
				$response['IDCARD_6'] 	= $row->IDCARD;
				$response['IDNO_6'] 	= $row->IDNO;
				$response['ISSUBY_6'] 	= $row->ISSUBY;
				$response['ISSUDT_6'] 	= $this->Convertdate(2,$row->ISSUDT);
				$response['EXPDT_6'] 	= $this->Convertdate(2,$row->EXPDT);
				$response['AGE_6'] 		= $row->AGE;
				$response['OCCUP_6'] 	= $row->OCCUP;
				$response['OFFIC_6'] 	= $row->OFFIC;
				$response['MAXCRED_6'] 	= number_format($row->MAXCRED,2);
				$response['YINCOME_6']	= number_format($row->YINCOME,2);
				$response['MREVENU_6'] 	= number_format($row->MREVENU,2);
				$response['YREVENU_6'] 	= number_format($row->YREVENU,2);
				$response['ADDRNO3_6'] 	= $row->ADDRNO3;
				$response['MEMO1_6'] 	= $row->MEMO1;
			}
		}
		$sql3_4 = "
			select A.ADDRNO,A.ADDR1,A.ADDR2,A.TUMB,B.AUMPDES,C.PROVDES,A.ZIP,A.TELP,A.MEMO1
			from {$this->MAuth->getdb('CUSTADDR')} A left join {$this->MAuth->getdb('SETAUMP')} B on A.AUMPCOD=B.AUMPCOD 
			left join {$this->MAuth->getdb('SETPROV')} C on B.PROVCOD=C.PROVCOD where A.CUSCOD ='".$cuscod2."'
			order by A.ADDRNO
		";
		$query3_4 = $this->db->query($sql3_4);
		$addrspt = "";
		if($query3_4->row()){
			foreach($query3_4->result() as $row){
				$addrspt .= "
					<tr class='trow' seq='old'>
						<td>".$row->ADDRNO."</td>
						<td>".$row->ADDR1."</td>
						<td>".$row->ADDR2."</td>
						<td>".$row->TUMB."</td>
						<td>".$row->AUMPDES."</td>
						<td>".$row->PROVDES."</td>
						<td>".$row->ZIP."</td>
						<td>".$row->TELP."</td>
					</tr>
				";
				$response['MEMO1ADR_6'] = $row->MEMO1;
			}
		}
		$response['addrspt'] = $addrspt;
		
		//tab7
		$sql7_1 = "
			select CONVERT(varchar(8),A.CHGDATE,112) as CHGDATE,A.STATFRM,A.STATTO
			,(select RTRIM(name) + '[' + RTRIM(code) + ']' from {$this->MAuth->getdb('OFFICER')} C where C.CODE = A.FRMBILL) as BILLFRM
			,(select RTRIM(name) + '[' + RTRIM(code) + ']' from {$this->MAuth->getdb('OFFICER')} C where C.CODE = A.TOBILL) as BILLTO
			,A.MEMO1 ,RTRIM(B.USERNAME) + '[' + RTRIM(B.CUSCOD) +']' as USERNAME from {$this->MAuth->getdb('STATTRAN')} A
			,{$this->MAuth->getdb('PASSWRD')} B where A.AUTHCOD = B.USERID and A.CONTNO = '".$CONTNOS."' 
			and A.LOCAT = '".$LOCATS."' order by A.CHGDATE
		";
		$compact = "";
		$query7_1 = $this->db->query($sql7_1);
		if($query7_1->row()){
			foreach($query7_1->result() as $row){
				$compact .="
					<tr class='trow' seq='old'>
						<td>".$this->Convertdate(2,$row->CHGDATE)."</td>
						<td>".$row->STATFRM."</td>
						<td>".$row->STATTO."</td>
						<td>".$row->BILLFRM."</td>
						<td>".$row->BILLTO."</td>
						<td>".$row->USERNAME."</td>
					</tr>
				";
			}
		}
		$response['compact'] = $compact;
		
		//tab8
		$sql8_1 = "
			IF OBJECT_ID('tempdb..#DEBTORS') IS NOT NULL DROP TABLE #DEBTORS
			select CONTNO,ARCONT,LOCAT,TOTPRC,SMPAY,BALANC,SMCHQ,TKANG,TSALE,CUSCOD,PAYFOR
			into #DEBTORS
			FROM(
				select A.CONTNO,A.ARCONT,A.LOCAT,A.PAYAMT AS TOTPRC,A.SMPAY,(A.PAYAMT - A.SMPAY) AS BALANC
				,A.SMCHQ,(A.PAYAMT - (A.SMPAY + A.SMCHQ)) AS TKANG, A.TSALE,A.CUSCOD,A.PAYFOR  
				from  {$this->MAuth->getdb('AROTHR')} A WHERE A.CONTNO = '".$CONTNOS."' 
			)DEBTORS
		";
		$this->db->query($sql8_1);
		
		$sql8_2 = "
			select CONTNO,ARCONT,LOCAT,TOTPRC,SMPAY
			,BALANC,SMCHQ,TKANG,TSALE,CUSCOD,PAYFOR from #DEBTORS order by CONTNO
		";
		$query8_2 = $this->db->query($sql8_2);
		$debtors = "";
		if($query8_2->row()){
			foreach($query8_2->result() as $row){
				$debtors .= "
					<tr class='trow' seq='old'>
						<td>".$row->CONTNO."</td>
						<td>".$row->ARCONT."</td>
						<td>".$row->LOCAT."</td>
						<td>".number_format($row->TOTPRC,2)."</td>
						<td>".number_format($row->SMPAY,2)."</td>
						<td>".number_format($row->BALANC,2)."</td>
						<td>".number_format($row->SMCHQ,2)."</td>
						<td>".number_format($row->TKANG,2)."</td>
						<td>".$row->PAYFOR."</td>
					</tr>
				";
			}
		}
		$response['debtors'] = $debtors;
		
		$sql8_3 = "
			select SUM(TOTPRC) as SUMTOTPRC,SUM(SMPAY) as SUMSMPAY,SUM(BALANC) as SUMBALANC
			,SUM(SMCHQ) as SUMSMCHQ,SUM(TKANG) as SUMTKANG from #DEBTORS
		";
		$query8_3 = $this->db->query($sql8_3);
		if($query8_3->row()){
			foreach($query8_3->result() as $row){
				$response['SUMTOTPRC_8']  = number_format($row->SUMTOTPRC,2);
				$response['SUMSMPAY_8']   = number_format($row->SUMSMPAY,2);
				$response['SUMBALANC_8']  = number_format($row->SUMBALANC,2);
				$response['SUMSMCHQ_8']   = number_format($row->SUMSMCHQ,2);
				$response['SUMTKANG_8']   = number_format($row->SUMTKANG,2);
			}
		}
		echo json_encode($response);
	}
	function changedatacar(){
		$STRNOS = $_REQUEST['STRNOS'];
		$response = array();
		$sql2 = "
			IF OBJECT_ID('tempdb..#CDT') IS NOT NULL DROP TABLE #CDT
			select CONTNO,RVLOCAT,STRNO,ENGNO,NADDCOST,TYPE,MODEL,BAAB,COLOR,CC,STAT,MANUYR,YSTAT,RECVNO,GCODE
			into #CDT
			FROM(
				select CONTNO,RVLOCAT,STRNO,ENGNO,NADDCOST,TYPE,MODEL,BAAB,COLOR,CC,STAT,MANUYR,YSTAT
				,RECVNO,GCODE from {$this->MAuth->getdb('INVTRAN')} where STRNO = '".$STRNOS."'
				union
				select CONTNO,RVLOCAT,STRNO,ENGNO,NADDCOST,TYPE,MODEL,BAAB,COLOR,CC,STAT,MANUYR,YSTAT
				,RECVNO,GCODE from {$this->MAuth->getdb('HINVTRAN')} where STRNO = '".$STRNOS."'
			)CDT
		";
		$this->db->query($sql2);
		
		$sql2_1 = "select * from #CDT";
		$recvno = ""; $gcode  = ""; $stat = ""; $ystat = "";
		$query2_1 = $this->db->query($sql2_1);
		if($query2_1->row()){
			foreach($query2_1->result() as $row){
				$response['STRNO_2']    = str_replace(chr(0),'',$row->STRNO);
				$response['ENGNO_2']    = str_replace(chr(0),'',$row->ENGNO);
				$response['NADDCOST_2'] = number_format($row->NADDCOST,2);
				$response['TYPE_2']     = str_replace(chr(0),'',$row->TYPE);
				$response['MODEL_2']    = str_replace(chr(0),'',$row->MODEL);
				$response['BAAB_2']     = str_replace(chr(0),'',$row->BAAB);
				$response['COLOR_2']    = str_replace(chr(0),'',$row->COLOR);
				$response['CC_2']       = str_replace(chr(0),'',$row->CC);
				$stat					= str_replace(chr(0),'',$row->STAT);
				$ystat					= str_replace(chr(0),'',$row->YSTAT);
				$response['MANUYR_2']   = str_replace(chr(0),'',$row->MANUYR);
				
				$recvno					= str_replace(chr(0),'',$row->RECVNO);
				$gcode					= str_replace(chr(0),'',$row->GCODE);
			}
		}
		if($stat == 'O' && $ystat !== 'Y'){
			$response['STAT_2'] = "เก่า";
		}else if($stat == 'N' && $ystat !== 'Y'){
			$response['STAT_2'] = "ใหม่";
		}else if($stat == 'O' && $ystat == 'Y'){
			$response['STAT_2'] = "เก่า : เป็นรถยึด";
		}else if($stat == 'N' && $ystat == 'Y'){
			$response['STAT_2'] = "ใหม่ : เป็นรถยึด";
		}
		$sql2_2 = "
			select APNAME from {$this->MAuth->getdb('INVINVO')} A,{$this->MAuth->getdb('APMAST')} B where RECVNO = '".$recvno."' and A.APCODE = B.APCODE 
		";
		$query2_2 = $this->db->query($sql2_2);
		if($query2_2->row()){
			foreach($query2_2->result() as $row){
				$response['APNAME_2']    = str_replace(chr(0),'',$row->APNAME);
			}
		}
		$sql2_3 = "
			select GDESC from {$this->MAuth->getdb('SETGROUP')} where GCODE = '".$gcode."' 
		";
		$query2_3 = $this->db->query($sql2_3);
		if($query2_3->row()){
			foreach($query2_3->result() as $row){
				$response['GDESC_2']    = str_replace(chr(0),'',$row->GDESC);
			}
		}
		$sql2_4 = "
			select REGNO,REGPAY,convert(varchar(8),REGYEAR,112) as REGYEAR,convert(varchar(8),REGEXP,112) as REGEXP
			,REGTYP,convert(varchar(8),GARFRM,112) as GARFRM,convert(varchar(8),GAREXP,112) as GAREXP
			,GARNO3,convert(varchar(8),GAR3FRM,112) as GAR3FRM,convert(varchar(8),GAR3EXP,112) as GAR3EXP
			from {$this->MAuth->getdb('REGTAB')} where STRNO = '".$STRNOS."' 
		";
		//echo $sql2_4; exit;
		$query2_4 = $this->db->query($sql2_4);
		if($query2_4->row()){
			foreach($query2_4->result() as $row){
				$response['REGNO_2']    = str_replace(chr(0),'',$row->REGNO); 
				$response['REGYEAR_2']  = $this->Convertdate(2,$row->REGYEAR); 
				$response['REGEXP_2']   = $this->Convertdate(2,$row->REGEXP); 
				$response['REGTYP_2']   = str_replace(chr(0),'',$row->REGTYP); 
				$response['GARFRM_2']   = $this->Convertdate(2,$row->GARFRM); 
				$response['GAREXP_2']   = $this->Convertdate(2,$row->GAREXP); 
				$response['GARNO3_2']   = str_replace(chr(0),'',$row->GARNO3); 
				$response['GAR3FRM_2']  = $this->Convertdate(2,$row->GAR3FRM);
				$response['GAR3EXP_2']  = $this->Convertdate(2,$row->GAR3EXP);
			}
		}
		echo json_encode($response);
	}
	function changedataspt(){
		$CUSCODSPT = $_REQUEST['CUSCODSPT'];
		$response = array();
		$sql20 = "
			select GROUP1,GRADE,convert(varchar(8),BIRTHDT,112) as BIRTHDT,NICKNM,IDCARD,IDNO,ISSUBY
			,convert(varchar(8),ISSUDT,112) as ISSUDT,convert(varchar(8),EXPDT,112) as EXPDT,AGE 
			,OCCUP,OFFIC,MAXCRED,YINCOME,MREVENU,YREVENU,ADDRNO3,MEMO1
			from {$this->MAuth->getdb('CUSTMAST')} where CUSCOD = '".$CUSCODSPT."' 
		";
		$query20 = $this->db->query($sql20);
		if($query20->row()){
			foreach($query20->result() as $row){
				$response['GROUP1_6'] 	= str_replace(chr(0),'',$row->GROUP1);
				$response['GRADE_6'] 	= str_replace(chr(0),'',$row->GRADE);
				$response['BIRTHDT_6'] 	= $this->Convertdate(2,$row->BIRTHDT);
				$response['NICKNM_6'] 	= str_replace(chr(0),'',$row->NICKNM);
				$response['IDCARD_6'] 	= str_replace(chr(0),'',$row->IDCARD);
				$response['IDNO_6'] 	= str_replace(chr(0),'',$row->IDNO);
				$response['ISSUBY_6'] 	= str_replace(chr(0),'',$row->ISSUBY);
				$response['ISSUDT_6'] 	= $this->Convertdate(2,$row->ISSUDT);
				$response['EXPDT_6'] 	= $this->Convertdate(2,$row->EXPDT);
				$response['AGE_6'] 		= str_replace(chr(0),'',$row->AGE);
				$response['OCCUP_6'] 	= str_replace(chr(0),'',$row->OCCUP);
				$response['OFFIC_6'] 	= str_replace(chr(0),'',$row->OFFIC);
				$response['MAXCRED_6'] 	= number_format($row->MAXCRED,2);
				$response['YINCOME_6']	= number_format($row->YINCOME,2);
				$response['MREVENU_6'] 	= number_format($row->MREVENU,2);
				$response['YREVENU_6'] 	= number_format($row->YREVENU,2);
				$response['ADDRNO3_6'] 	= str_replace(chr(0),'',$row->ADDRNO3);
				$response['MEMO1_6'] 	= str_replace(chr(0),'',$row->MEMO1);
			}
		}
		$sql21 = "
			select A.ADDRNO,A.ADDR1,A.ADDR2,A.TUMB,B.AUMPDES,C.PROVDES,A.ZIP,A.TELP,A.MEMO1
			from {$this->MAuth->getdb('CUSTADDR')} A left join {$this->MAuth->getdb('SETAUMP')} B on A.AUMPCOD=B.AUMPCOD 
			left join {$this->MAuth->getdb('SETPROV')} C on B.PROVCOD=C.PROVCOD where A.CUSCOD ='".$CUSCODSPT."'
			order by A.ADDRNO
		";
		$query21 = $this->db->query($sql21);
		$addrspt = "";
		if($query21->row()){
			foreach($query21->result() as $row){
				$addrspt .= "
					<tr class='trow' seq='old'>
						<td>".str_replace(chr(0),'',$row->ADDRNO)."</td>
						<td>".str_replace(chr(0),'',$row->ADDR1)."</td>
						<td>".str_replace(chr(0),'',$row->ADDR2)."</td>
						<td>".str_replace(chr(0),'',$row->TUMB)."</td>
						<td>".str_replace(chr(0),'',$row->AUMPDES)."</td>
						<td>".str_replace(chr(0),'',$row->PROVDES)."</td>
						<td>".str_replace(chr(0),'',$row->ZIP)."</td>
						<td>".str_replace(chr(0),'',$row->TELP)."</td>
					</tr>
				";
				$response['MEMO1ADR_6'] = str_replace(chr(0),'',$row->MEMO1);
			}
		}
		$response['addrspt'] = $addrspt;
		echo json_encode($response);
	}
	function getfromAlertMessage(){
		$TYPALERT = $_REQUEST['TYPALERT'];
		$hcolor = "";
		if($TYPALERT == 'XX'){
			$hcolor = "background-color:#4169e1;";
		}else{
			$hcolor = "background-color:#d11226;";
		}
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
		$CONTNO  = $_REQUEST['CONTNO'];
		$STARTDT = $_REQUEST['STARTDT'];
		$ENDDT   = $_REQUEST['ENDDT'];
		$MSGOLD  = $_REQUEST['MSGOLD'];
		$MSGNEW  = $_REQUEST['MSGNEW'];
		//exit;
		$sql = "
			if OBJECT_ID('tempdb..#updatemessage') is not null drop table #updatemessage;
			create table #updatemessage (id varchar(20),contno varchar(20),msg varchar(max));
			
			begin tran updatemessage
			begin try
			
				update {$this->MAuth->getdb('ALERTMSG')}
				set MEMO1 = '".$MSGNEW."'
				where CONTNO = '".$CONTNO."' collate thai_cs_as and STARTDT = '".$STARTDT."' 
				and ENDDT = '".$ENDDT."' and MEMO1 like '".$MSGOLD."%'
				
				insert into #updatemessage select 'S','".$CONTNO."','แก้ไขข้อความแจ้งเตือนเรียบร้อย';
				
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
		if($tx[1] == 'undefined'){ $tx[1] = $this->today('today'); }
		$CONTNO = $tx[0];
		$DATESEARCH = $this->Convertdate(1,$tx[1]);
		
		$sql = "
			select CUSCOD, LOCAT from {$this->MAuth->getdb('ARMAST')} 
			where CONTNO = '".$CONTNO."' 
		";
		//echo $sql; exit;
		$CUSCOD = ""; $LOCAT = ""; $i = 0;
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){$i++;
				$CUSCOD = $row->CUSCOD;
				$LOCAT = $row->LOCAT;
			}
		}
		$data = array();
		
		$sql = "select COMP_NM from {$this->MAuth->getdb('CONDPAY')}";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		$row = $query->row();
		$data[0] = $row->COMP_NM;
		
		$sql = "
			select isnull(M.SNAM,'-') + isnull(M.NAME1,'-') +' '+ isnull(M.NAME2,'-') as CUSNAME,M.OFFIC, 
			isnull(A.ADDR1,'-')+' ถ.'+isnull(A.ADDR2,'-')+' ต.'+isnull(A.TUMB,'-')+' อ.'+isnull(SA.AUMPDES,'-')+' จ.'+isnull(SP.PROVDES,'-') 
			as CUSADD from {$this->MAuth->getdb('CUSTMAST')} M 
			left join {$this->MAuth->getdb('CUSTADDR')} A on A.CUSCOD = M.CUSCOD and A.ADDRNO = M.ADDRNO 
			left join {$this->MAuth->getdb('SETAUMP')} SA on SA.AUMPCOD = A.AUMPCOD
			left join {$this->MAuth->getdb('SETPROV')} SP on SP.PROVCOD = A.PROVCOD where M.CUSCOD = '".$CUSCOD."'
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$data[1] = $row->CUSNAME;
				$data[2] = $row->CUSADD;
				$data[3] = $row->OFFIC;
			}
		}else{
			$data[1] = '';
			$data[2] = '';
			$data[3] = '';
		}
		$sql = "
			select isnull(M.SNAM,'-') + isnull(M.NAME1,'-') +' '+ isnull(M.NAME2,'-') as CUSNAME,M.OFFIC, 
			isnull(A.ADDR1,'-')+' ถ.'+isnull(A.ADDR2,'-')+' ต.'+isnull(A.TUMB,'-')+' อ.'+isnull(SA.AUMPDES,'-')+' จ.'+isnull(SP.PROVDES,'-') 
			as CUSADD from {$this->MAuth->getdb('ARMGAR')} G 
			left join {$this->MAuth->getdb('CUSTMAST')} M on M.CUSCOD = G.CUSCOD 
			left join {$this->MAuth->getdb('CUSTADDR')} A on A.CUSCOD = M.CUSCOD and A.ADDRNO = M.ADDRNO
			left join {$this->MAuth->getdb('SETAUMP')} SA on SA.AUMPCOD = A.AUMPCOD 
			left join {$this->MAuth->getdb('SETPROV')} SP on SP.PROVCOD = A.PROVCOD 
			where G.CONTNO = '".$CONTNO."' and G.LOCAT = '".$LOCAT."' and GARNO = '1'
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$data[4] = $row->CUSNAME;
				$data[5] = $row->CUSADD;
				$data[6] = $row->OFFIC;
			}
		}else{
			$data[4] = '';
			$data[5] = '';
			$data[6] = '';
		}
		$sql = "
			select isnull(M.SNAM,'-') + isnull(M.NAME1,'-') +' '+ isnull(M.NAME2,'-') as CUSNAME,M.OFFIC, 
			isnull(A.ADDR1,'-')+' ถ.'+isnull(A.ADDR2,'-')+' ต.'+isnull(A.TUMB,'-')+' อ.'+isnull(SA.AUMPDES,'-')+' จ.'+isnull(SP.PROVDES,'-') 
			as CUSADD from {$this->MAuth->getdb('ARMGAR')} G 
			left join {$this->MAuth->getdb('CUSTMAST')} M on M.CUSCOD = G.CUSCOD 
			left join {$this->MAuth->getdb('CUSTADDR')} A on A.CUSCOD = M.CUSCOD and A.ADDRNO = M.ADDRNO
			left join {$this->MAuth->getdb('SETAUMP')} SA on SA.AUMPCOD = A.AUMPCOD 
			left join {$this->MAuth->getdb('SETPROV')} SP on SP.PROVCOD = A.PROVCOD 
			where G.CONTNO = '".$CONTNO."' and G.LOCAT = '".$LOCAT."' and GARNO = '2'
		";
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$data[7] = $row->CUSNAME;
				$data[8] = $row->CUSADD;
				$data[9] = $row->OFFIC;
			}
		}else{
			$data[7] = '';
			$data[8] = '';
			$data[9] = '';
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
			declare @PAID decimal(8,2) = (
				select isnull(sum(PAYINT),0) from {$this->MAuth->getdb('CHQTRAN')} where CONTNO = '".$CONTNO."' and LOCATPAY = '".$LOCAT."' 
				and (PAYFOR='006' OR PAYFOR='007') and FLAG !='C' and (PAYDT <= '".$DATESEARCH."') 
			) 
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
		$sql = "
			select isnull(SUM(PAYAMT-SMPAY),0) as sumARBALANC from {$this->MAuth->getdb('AROTHR')} 
			where TSALE = 'X' and CONTNO = '".$CONTNO."' 
		";//echo $sql; exit;
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
		if($i > 0){
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
		}else{
			$content = "<div style='color:red;'>ไม่พบข้อมูล</div>";
		}
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
		if($tx[1] == 'undefined'){ $tx[1] = $this->today('today'); }
		$CONTNO = $tx[0];
		$DATEBILL =  $tx[1];		
		$DATESEARCH = $this->Convertdate(1,$tx[1]);
		
		$sql = "
			select CUSCOD, LOCAT from {$this->MAuth->getdb('ARMAST')} 
			where CONTNO = '".$CONTNO."' 
		";
		//echo $sql; exit;
		$CUSCOD = ""; $LOCAT = ""; $i = 0;
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){$i++;
				$CUSCOD = $row->CUSCOD;
				$LOCAT = $row->LOCAT;
			}
		}
		
		$sql = " select COMP_NM from {$this->MAuth->getdb('CONDPAY')} ";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		$row = $query->row();
		$data[0] = $row->COMP_NM;
		$sql = " 
			select ISNULL(SNAM,'-')+''+ISNULL(NAME1,'-')+'  '+ISNULL(NAME2,'-') as CUSNAME 
			from {$this->MAuth->getdb('CUSTMAST')} where CUSCOD = '".$CUSCOD."'
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		$row = $query->row();
		$data[1] = $row->CUSNAME;
		$sql = "
			select TYPE,MODEL,COLOR,STRNO,ENGNO,REGNO
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
			--จำนวนงวด
			declare @T_NOPAY int =(
				select T_NOPAY from {$this->MAuth->getdb('ARMAST')} 
				where CONTNO = '".$CONTNO."' and LOCAT = '".$LOCAT."'
			);
			declare @num_PERD varchar(max) = (
				select
				case
					when @T_NOPAY <= 10 then '10'
					when @T_NOPAY <= 12 then '12'
					when @T_NOPAY <= 18 then '18'
					when @T_NOPAY <= 24 then '24'
					when @T_NOPAY <= 30 then '30'
					when @T_NOPAY <= 36 then '36'
					when @T_NOPAY <= 42 then '42'
					when @T_NOPAY <= 48 then '48'
					when @T_NOPAY <= 54 then '54'
					when @T_NOPAY <= 60 then '60'
					else '60' 
				end
			);
			
			--ตาราง setup
			declare @CALDSC varchar(max) = (
				select CALDSC from {$this->MAuth->getdb('ARMAST')} 
				where CONTNO = '".$CONTNO."' and LOCAT = '".$LOCAT."'
			);
			select @num_PERD as N_PERD,@CALDSC as CALDSC
		";
		//echo $sql;
		$query = $this->db->query($sql);
		$row = $query->row();
		$num_perd = $row->N_PERD;
		$caldsc3   = $row->CALDSC;
		$caldsc   = "TABLE".($row->CALDSC == 3 ? 1:$row->CALDSC);
		
		$sql = "
			declare @NOPAYED int = (
				select COUNT(NOPAY) as NOPAY 
				from {$this->MAuth->getdb('ARPAY')} where CONTNO = '".$CONTNO."' 
				and LOCAT = '".$LOCAT."' and PAYMENT > 0
			)
			declare @DISPAY decimal(8,2) = (
				select isnull(MIN(NOPAY),0) as DISPAY 
				from {$this->MAuth->getdb('ARPAY')} 
				where CONTNO = '".$CONTNO."' and LOCAT = '".$LOCAT."' and DDATE >= '".$DATESEARCH."'
			)
			/*
			declare @NPROF decimal(8,2)	= ( 
				select sum(NPROF) as NPROF
				from(
					select case when PAYMENT > 0 then (NPROF/DAMT)*PAYMENT else NPROF end as  NPROF  
					from {$this->MAuth->getdb('ARPAY')}  
					where CONTNO = '".$CONTNO."' and LOCAT = '".$LOCAT."' 
					and PAYMENT < DAMT and DDATE > '".$DATESEARCH."'
				)a
			)
			*/
			declare @NPROF decimal(8,2)	= (
				select sum(NPROF) as NPROF from(
					select NPROF from {$this->MAuth->getdb('ARPAY')} 
					where CONTNO = '".$CONTNO."' and LOCAT = '".$LOCAT."' 
					and DDATE > '".$DATESEARCH."'
				)A
			)
			
			declare @AROTH decimal(8,2)= (
				select isnull(SUM(PAYAMT-(SMPAY+SMCHQ)),0) as AROTH  
				from {$this->MAuth->getdb('AROTHR')}  
				where CONTNO = '".$CONTNO."' and LOCAT = '".$LOCAT."'
			)
			declare @INTAMT decimal(8,2) = (
				select sum(INTAMT) from {$this->MAuth->getdb('ARPAY')} 
				where CONTNO = '".$CONTNO."' and LOCAT = '".$LOCAT."'
			)
			
			declare @PAID decimal(8,2) = ( 
				select isnull(SUM(PAYINT),0) from {$this->MAuth->getdb('CHQTRAN')} 
				where CONTNO = '".$CONTNO."' and LOCATPAY = '".$LOCAT."' and 
				(PAYFOR='006' OR PAYFOR='007') and FLAG !='C' and (PAYDT IS NOT NULL) 
			)
			declare @NPROF2 decimal(8,2) = (
				select top 1 NPROF from {$this->MAuth->getdb('ARPAY')}  
				where CONTNO = '".$CONTNO."' and LOCAT = '".$LOCAT."' order by NOPAY desc
			)
			declare @PERD decimal(18,1) = (
				select case when ".$caldsc3." = 3 then 0 else PERD".$num_perd." / 100 end 
				from ".$this->MAuth->getdb($caldsc)." 
				where NOPAY = ISNULL(@DISPAY,0)		
			);
			
			--มูลค่าชำระแล้ว
			declare @NPAYRES decimal(8,2) = ( 
				select SUM(PAYAMT) from {$this->MAuth->getdb('CHQTRAN')} 
				where CONTNO = '".$CONTNO."' and PAYFOR in ('002','006','007') 
				and FLAG <> 'C' and LOCATPAY = '".$LOCAT."' 
			)
			--ภาษีชำระแล้ว 
			declare @VATPRES decimal(8,2) = ( 
				select SUM(PAYAMT_V) from {$this->MAuth->getdb('CHQTRAN')} 
				where CONTNO = '".$CONTNO."' and PAYFOR in ('002','006','007') 
				and FLAG <> 'C' and LOCATPAY = '".$LOCAT."' 
			)
			
			select T_NOPAY,@NOPAYED as NOPAYED,@DISPAY as DISPAY
				,case when @DISPAY > 0 then @PERD * 100 else 0 end as PERCDIS,
				case when @DISPAY > 0 then @NPROF * @PERD else 0 end as PERC30
				,NPRICE,VATPRC,TOTPRC,NCSHPRC,VCSHPRC,TCSHPRC,TOTDWN,INTRT
				,@NPAYRES - @VATPRES as NPAYRES,@VATPRES as VATPRES,SMPAY
				,NPRICE - (@NPAYRES - @VATPRES) as ARBALANC
				,VATPRC - @VATPRES as VATBALANC
				,TOTPRC-SMPAY-SMCHQ as TOTAR
				,case when @DISPAY > 0 then (TOTPRC-SMPAY-SMCHQ)-(@NPROF * @PERD) 
				else TOTPRC-SMPAY-SMCHQ end as TOTPAY, @INTAMT-@PAID as INTAMT
				,@AROTH as AROTH
				
				,case when @DISPAY > 0 then (TOTPRC-SMPAY-SMCHQ)-(@NPROF * @PERD)+(@INTAMT-@PAID)+@AROTH 
				else TOTPRC-SMPAY-SMCHQ+(@INTAMT-@PAID)+@AROTH end as NETPAY
				,convert(nvarchar,SDATE,112) as SDATE
				,case when isnull(@NPROF,0) = 0 then @NPROF2 else @NPROF end as NPROF
				,case when isnull(@NPROF,0) = 0 then @NPROF2 * 0.5 else @NPROF * 0.5 end as PERC50
			from {$this->MAuth->getdb('ARMAST')}
			where CONTNO = '".$CONTNO."' and LOCAT = '".$LOCAT."'
		";
		//echo $sql; exit;
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
		$sql = "
			select SUM(PAYAMT-SMPAY) as sumARBALANC from {$this->MAuth->getdb('AROTHR')} 
			where PAYAMT > SMPAY and CONTNO = '".$CONTNO."' and LOCAT = '".$LOCAT."'
		";//echo $sql; exit;
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
		if($i > 0){
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
							<td class='wf pd tr'>50.00%&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
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
		}else{
			$content = "<div style='color:red;'>ไม่พบข้อมูล</div>";
		}
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
		if($tx[1] == 'undefined'){ $tx[1] = $this->today('today'); }
		$CONTNO = $tx[0];
		$DATEBILL =  $tx[1];
		$DATESEARCH = $this->Convertdate(1,$tx[1]);
		
		$sql = "
			select CUSCOD, LOCAT from {$this->MAuth->getdb('ARMAST')} 
			where CONTNO = '".$CONTNO."' 
		";
		//echo $sql; exit;
		$CUSCOD = ""; $LOCAT = ""; $i = 0;
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){$i++;
				$CUSCOD = $row->CUSCOD;
				$LOCAT = $row->LOCAT;
			}
		}
		$data = array();
		
		$sql = " select COMP_NM from {$this->MAuth->getdb('CONDPAY')} ";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		$row = $query->row();
		$data[0] = $row->COMP_NM;
		
		$sql = " 
			select ISNULL(SNAM,'-')+''+ISNULL(NAME1,'-')+'  '+ISNULL(NAME2,'-') as CUSNAME 
			from {$this->MAuth->getdb('CUSTMAST')} where CUSCOD = '".$CUSCOD."'
		";
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
			--จำนวนงวด
			declare @T_NOPAY int =(
				select T_NOPAY from {$this->MAuth->getdb('ARMAST')} 
				where CONTNO = '".$CONTNO."' and LOCAT = '".$LOCAT."'
			);
			declare @num_PERD varchar(max) = (
				select
				case
					when @T_NOPAY <= 10 then '10'
					when @T_NOPAY <= 12 then '12'
					when @T_NOPAY <= 18 then '18'
					when @T_NOPAY <= 24 then '24'
					when @T_NOPAY <= 30 then '30'
					when @T_NOPAY <= 36 then '36'
					when @T_NOPAY <= 42 then '42'
					when @T_NOPAY <= 48 then '48'
					when @T_NOPAY <= 54 then '54'
					when @T_NOPAY <= 60 then '60'
					else '60' 
				end
			);
			--ตาราง setup
			declare @CALDSC varchar(max) = (
				select CALDSC from {$this->MAuth->getdb('ARMAST')} 
				where CONTNO = '".$CONTNO."' and LOCAT = '".$LOCAT."'
			);
			select @num_PERD as N_PERD,@CALDSC as CALDSC
		";
		//echo $sql;
		$query = $this->db->query($sql);
		$row = $query->row();
		$num_perd = $row->N_PERD;
		
		$caldsc3  = $row->CALDSC;
		$caldsc   = "TABLE".($row->CALDSC == 3 ? 1:$row->CALDSC);
		
		$sql = "
			declare @NOPAYED int = (select COUNT(NOPAY) as NOPAY from {$this->MAuth->getdb('ARPAY')} where CONTNO = '".$CONTNO."' and LOCAT = '".$LOCAT."' and PAYMENT > 0)
			declare @DISPAY decimal(8,2) = (select isnull(MIN(NOPAY),0) as DISPAY from {$this->MAuth->getdb('ARPAY')} where CONTNO = '".$CONTNO."' and LOCAT = '".$LOCAT."' and DDATE >= '".$DATESEARCH."')
			/*
			declare @NPROF decimal(8,2)	= ( 
				select sum(NPROF) as NPROF from(
					select case when PAYMENT > 0 then (NPROF/DAMT)*PAYMENT else NPROF end as  NPROF  
					from {$this->MAuth->getdb('ARPAY')}  
					where CONTNO = '".$CONTNO."' and LOCAT = '".$LOCAT."' 
					and PAYMENT < DAMT and DDATE > '".$DATESEARCH."'
				)A
			)
			*/
			declare @NPROF decimal(8,2)	= (
				select sum(NPROF) as NPROF from(
					select NPROF from {$this->MAuth->getdb('ARPAY')} 
					where CONTNO = '".$CONTNO."' and LOCAT = '".$LOCAT."' 
					and DDATE > '".$DATESEARCH."'
				)A
			)
			declare @AROTH decimal(8,2)= (select isnull(SUM(PAYAMT-(SMPAY+SMCHQ)),0) as AROTH  from {$this->MAuth->getdb('AROTHR')}  where CONTNO = '".$CONTNO."' and LOCAT = '".$LOCAT."' )
			declare @INTAMT decimal(8,2) = (select sum(INTAMT) from {$this->MAuth->getdb('ARPAY')} where CONTNO = '".$CONTNO."' and LOCAT = '".$LOCAT."')
			declare @PAID decimal(8,2) = ( select isnull(SUM(PAYINT),0) from {$this->MAuth->getdb('CHQTRAN')} where CONTNO = '".$CONTNO."' and LOCATPAY = '".$LOCAT."' and 
			(PAYFOR='006' OR PAYFOR='007') and FLAG !='C' and (PAYDT IS NOT NULL) )
			
			declare @PERD decimal(18,1) = (
				select case when ".$caldsc3." = 3 then 0 else PERD".$num_perd." / 100 end 
				from ".$this->MAuth->getdb($caldsc)." 
				where NOPAY = ISNULL(@DISPAY,0)		
			);
		
			select T_NOPAY,@NOPAYED as NOPAYED,@DISPAY as DISPAY,TCSHPRC,TOTPRC,SMPAY
				,TOTPRC-SMPAY-SMCHQ as TOTAR, @INTAMT-@PAID as INTAMT,@AROTH as AROTH
				,TOTPRC-SMPAY-SMCHQ+(@INTAMT-@PAID)+@AROTH as TOTPAY
				,case when @DISPAY > 0 then @NPROF * @PERD else 0 end as PERC30
				,case when @DISPAY > 0 then (TOTPRC-SMPAY-SMCHQ)-(@NPROF * @PERD)+(@INTAMT-@PAID)+@AROTH 
				else TOTPRC-SMPAY-SMCHQ+(@INTAMT-@PAID)+@AROTH end
			as NETPAY, convert(nvarchar,SDATE,112) as SDATE
			from {$this->MAuth->getdb('ARMAST')} 
			where CONTNO = '".$CONTNO."' and LOCAT = '".$LOCAT."'
		";
		//echo $sql; exit;
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
			select A.ARCONT,A.ARDATE,A.PAYFOR,B.FORDESC,A.PAYAMT,A.SMPAY,(A.PAYAMT-A.SMPAY) as AROTHBALANC
			from {$this->MAuth->getdb('AROTHR')} A
			left join {$this->MAuth->getdb('PAYFOR')} B on A.PAYFOR = B.FORCODE
			where A.PAYAMT > A.SMPAY and A.CONTNO = '".$CONTNO."' and A.LOCAT = '".$LOCAT."'
			order by A.ARCONT
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
		$sql = "
			select SUM(PAYAMT-SMPAY) as sumARBALANC from {$this->MAuth->getdb('AROTHR')} 
			where PAYAMT > SMPAY and CONTNO = '".$CONTNO."' and LOCAT = '".$LOCAT."'
		";//echo $sql; exit;
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
		if($i > 0){
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
		}else{
			$content = "<div style='color:red;'>ไม่พบข้อมูล</div>";
		}
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
	function printstatuscontentpdf(){
		$tx = explode("||",$_REQUEST['cond']);
		$CONTNO = $tx[0];
		$LOCAT =  $tx[1];
		$sql = "
			IF OBJECT_ID('tempdb..#RCT') IS NOT NULL DROP TABLE #RCT
			select LOCAT,CONTNO,CUSNAME,SDATE,TOTPRC,LPAYD
				,BALANCE,CHGDATE,STATFRM,STATTO,FRMBILL,TOBILL,MEMO1 
			into #RCT
			FROM(
				select A.LOCAT,A.CONTNO,C.SNAM+C.NAME1+' '+C.NAME2 as CUSNAME,convert(varchar(8),B.SDATE,112) as SDATE
				,B.TOTPRC,convert(varchar(8),B.LPAYD,112) as LPAYD,(B.TOTPRC-B.SMPAY) as BALANCE
				,convert(varchar(8),A.CHGDATE,112) as CHGDATE,A.STATFRM,A.STATTO,A.FRMBILL,A.TOBILL,A.MEMO1 
				from {$this->MAuth->getdb('STATTRAN')} A
				left join {$this->MAuth->getdb('ARMAST')} B on A.CONTNO = B.CONTNO
				left join {$this->MAuth->getdb('CUSTMAST')} C on B.CUSCOD = C.CUSCOD
				where A.CONTNO = '".$CONTNO."' and A.LOCAT = '".$LOCAT."'
			)RCT
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$sql = "select * from #RCT";
		$query1 = $this->db->query($sql);
		
		$sql2 = "select count(CONTNO) as CONTNO from #RCT";
		$query2 = $this->db->query($sql2);
		
		$head = ""; $html = ""; $i = 0;
		$head = "
			<tr class='wm'>
				<td class='wf pd' style='height:1px;border-top:0.1px solid black;' colspan='13'></td>
			</tr>
			<tr>
				<th style='border-bottom:0.1px solid black;text-align:left;'>สาขา</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>เลขที่สัญญา</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>ชื่อ - สกุล</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>วันขาย</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>ราคาขาย</th>			
				<th style='border-bottom:0.1px solid black;text-align:left;'>วันชำระล่าสุด</th> 
				<th style='border-bottom:0.1px solid black;text-align:left;'>ลูกหนี้คงเหลือ</th> 
				<th style='border-bottom:0.1px solid black;text-align:right;'>วันที่เปลี่ยนสถานะ</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>จากสถานะ</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>เป็นสถานะ</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>จากพนักงานเก็บเงิน</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>เป็นพนักงานเก็บเงิน</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>หมายเหตุ</th>
			</tr>
			<tr class='wm'>
				<td class='wf pd' style='height:1px;border-top:0.1px solid black;' colspan='13'></td>
			</tr>
		";
		if($query1->row()){
			foreach($query1->result() as $row){$i++;
				$html .="
					<tr class='trow'>
						<td style='width:70px;text-align:left;'>".$row->LOCAT."</td>
						<td style='width:90px;text-align:left;'>".$row->CONTNO."</td>
						<td style='width:300px;text-align:left;'>".$row->CUSNAME."</td>
						<td style='width:70spx;text-align:left;'>".$this->Convertdate(2,$row->SDATE)."</td>
						<td style='width:70px;text-align:right;'>".number_format($row->TOTPRC,2)."</td>
						<td style='width:70px;text-align:right;'>".$this->Convertdate(2,$row->LPAYD)."</td>
						<td style='width:70px;text-align:right;'>".number_format($row->BALANCE,2)."</td>
						<td style='width:70px;text-align:right;'>".$this->Convertdate(2,$row->CHGDATE)."</td>
						<td style='width:70px;text-align:right;'>".$row->STATFRM."</td>
						<td style='width:70px;text-align:right;'>".$row->STATTO."</td>
						<td style='width:70px;text-align:right;'>".$row->FRMBILL."</td>
						<td style='width:70px;text-align:right;'>".$row->TOBILL."</td>
						<td style='width:70px;text-align:right;'>".$row->MEMO1."</td>
					</tr>
				";
			}
		}
		if($query2->row()){
			foreach($query2->result() as $row){;
				$html .="
					<tr class='wm'>
						<td class='wf pd' style='height:1px;border-top:0.1px solid black;' colspan='13'></td>
					</tr>
					<tr class='trow'>
						<td style='width:70px;text-align:left;'>รวมทั้งสิ้น</td>
						<td style='width:70px;text-align:left;'>".$row->CONTNO."</td>
						<td style='width:70px;text-align:left;'>รายการ</td>
					</tr>
					<tr class='wm'>
						<td class='wf pd' style='height:1px;border-top:0.1px solid black;' colspan='13'></td>
					</tr>
				";
			}
		}
		$mpdf = new \Mpdf\Mpdf([
			'mode' => 'utf-8', 
			'format' =>'A4-L',
			'margin_top' => 10, 	//default = 16
			'margin_left' => 10, 	//default = 15
			'margin_right' => 10, 	//default = 15
			'margin_bottom' => 10, 	//default = 16
			'margin_header' => 9, 	//default = 9
			'margin_footer' => 9, 	//default = 9
		]);
		if($i > 0){
			$content = "
				<table class='wf' style='font-size:7.5pt;height:700px;border-collapse:collapse;line-height:23px;overflow:wrap;vertical-align:text-top;'>
					<tbody>
						<tr>
							<th colspan='13' style='font-size:10pt;'>บริษัท ตั้งใจพัฒนายานยนต์ จำกัด</th>
						</tr>
						<tr>
							<th colspan='13' style='font-size:9pt;'>รายงานการเปลี่ยนสถานะสัญญา</th>
						</tr>
						<tr>
							<td style='text-align:center;' colspan='13'>
								<b>จากวันที่</b> &nbsp;&nbsp; &nbsp;&nbsp;
								<b>ถึงวันที่</b>&nbsp;&nbsp; &nbsp;&nbsp;
								<b>เลขที่สัญญา</b>&nbsp;&nbsp;".$CONTNO."&nbsp;&nbsp;
								<b>สาขา</b>&nbsp;&nbsp;".$LOCAT."&nbsp;&nbsp;
							</td>
						</tr>
						".$head."
						".$html."
					</tbody>
				</table>
			";
			$head = "
				<div class='wf pf' style='top:1060;left:600;top:715;left:880; font-size:6pt;'>วันที่พิมพ์รายงาน : ".date('d/m/').(date('Y')+543)." ".date('H:i')." หน้า {PAGENO} / {nbpg}</div>
			";
		}else{
			$content = "<font style='color:red;'>ไม่พบข้อมูล</font>";
			$head = "
				<div class='wf pf' style='top:1060;left:600;top:715;left:880; font-size:6pt;'></div>
			";
		}
		$stylesheet = "
			<style>
				body { font-family: garuda;font-size:10pt; }
				.wf { width:100%; }
				.h10 { height:10px; }
				.tc { text-align:center; }
				.pf { position:fixed; }
				.bor { border:0.1px solid black; }
				.bor2 { border:0.1px dotted black; }
			</style>
		";
		$content = $content.$stylesheet;
		$mpdf->SetHTMLHeader($head);	
		$mpdf->WriteHTML($content);	
		$mpdf->Output();
	}
}

