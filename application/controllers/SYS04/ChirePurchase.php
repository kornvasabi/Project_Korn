<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ChirePurchase extends MY_Controller {
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
			<div class='tab1' name='home' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}' style='height:calc(100vh - 132px);overflow:auto;background-color:white;'>
				<div style='height:65px;overflow:auto;'>
					<div class='col-sm-2'>	
						<div class='form-group'>
							เลขที่สัญญา
							<input type='text' id='inpCONTNO' class='form-control input-sm' placeholder='เลขที่สัญญา'>
						</div>
					</div>
					<div class='col-sm-2'>
						<div class='form-group'>
							วันที่ทำสัญญา
							<input class='form-control input-sm' type='text' data-provide='datepicker' data-date-language='th-th' placeholder='วันที่ทำสัญญา'>
						</div>
					</div>
					<div class='col-sm-2'>	
						<div class='form-group'>
							เลขตัวถัง
							<input type='text' id='inpCONTNO' class='form-control input-sm' placeholder='เลขตัวถัง'>
						</div>
					</div>
					
					<div class='col-sm-2'>	
						<div class='form-group'>
							รหัสลูกค้า
							<input type='text' id='inpCONTNO' class='form-control input-sm' placeholder='รหัสลูกค้า'>
						</div>
					</div>
					
					<div class='col-sm-2'>	
						<div class='form-group'>
							ชื่อ-สกุล ลูกค้า
							<input type='text' id='inpCONTNO' class='form-control input-sm' placeholder='ชื่อ-สกุล ลูกค้า'>
						</div>
					</div>
					
					<div class='col-sm-1'>	
						<div class='form-group'>
							<br>
							<input type='button' id='search_TypeCar' class='btn btn-primary btn-sm' value='แสดง' style='width:100%'>
						</div>
					</div>
					<div class='col-sm-1'>	
						<div class='form-group'>
							<br>
							<input type='button' id='search_TypeCar' class='btn btn-primary btn-sm' value='ขาย' style='width:100%'>
						</div>
					</div>
				</div>
				<div id='' style='height:calc(100% - 65px);overflow:auto;background-color:red;'></div>				
			</div>
			<div class='tab2' style='height:calc(100vh - 132px);overflow:auto;background-color:white;'>
				<div id='demo-wizard2' class='wizard-wrapper'>    
					<div class='wizard'>
						<form id='demo-form2' action='' class='lobi-form' novalidate='novalidate'>
							<ul class='wizard-tabs wizard-tab-balls nav-justified nav nav-pills'>
								<li class='active'>
									<a href='#tab21' data-toggle='tab' aria-expanded='true'>
										<span class='step'>1</span>
										<span class='title'>ผู้เช่าซื้อ/รถ</span>
									</a>
								</li>
								<li>
									<a href='#tab22' data-toggle='tab'>
										<span class='step'>2</span>
										<span class='title'>อุปกรณ์เสริม</span>
									</a>
								</li>
								<li>
									<a href='#tab23' data-toggle='tab'>
										<span class='step'>3</span>
										<span class='title'>เงื่อนไขการเงิน</span>
									</a>
								</li>
								<li>
									<a href='#tab24' data-toggle='tab'>
										<span class='step'>4</span>
										<span class='title'>รายละเอียดสัญญา</span>
									</a>
								</li>
								<li>
									<a href='#tab25' data-toggle='tab'>
										<span class='step'>5</span>
										<span class='title'>การค้ำประกัน</span>
									</a>
								</li>
							</ul>
							<div class='tab-content bg-white'>
								<div class='tab-pane active' id='tab21' style='height:calc(100vh - 330px);overflow:auto;'>
									<fieldset>
										<div class='row'>
											<div class='col-sm-2 col-sm-offset-3'>	
												<div class='form-group'>
													เลขที่สัญญา
													<input type='text' id='inpCONTNO' class='form-control input-sm' placeholder='เลขที่สัญญา'>
												</div>
											</div>
											<div class='col-sm-2'>	
												<div class='form-group'>
													วันที่ทำสัญญา
													<input class='form-control input-sm' type='text' data-provide='datepicker' data-date-language='th-th' placeholder='วันที่ทำสัญญา'>
												</div>
											</div>
											<div class='col-sm-2'>	
												<div class='form-group'>
													สาขา
													<input type='text' id='inpCONTNO' class='form-control input-sm' placeholder='สาขา'>
												</div>
											</div>
										</div>	
											
										<div class='row'>
											<div class='col-sm-2 col-sm-offset-4'>	
												<div class='form-group'>
													เลขที่ใบจอง
													<input type='text' id='inpCONTNO' class='form-control input-sm' placeholder='เลขที่ใบจอง'>
												</div>
											</div>
											<div class='col-sm-2'>	
												<div class='form-group'>
													เลขที่ใบอนุมัติ
													<input type='text' id='inpCONTNO' class='form-control input-sm' placeholder='เลขที่ใบอนุมัติ'>
												</div>
											</div>
										</div>	
											
										<div class='row'>
											<div class='col-sm-2 col-sm-offset-3'>
												รหัสลูกค้า
												<div class='input-group'>
													<input type='text' id='tab21_CUSCOD' class='form-control input-sm' placeholder='รหัสลูกค้า' readonly>
													<div class='input-group-btn'>
														<button type='button' id='tab21_CUSCODAction' class='btn btn-info dropdown-toggle' data-toggle='dropdown' aria-expanded='false'><i class='fa fa-list-ul'></i></button>
														<ul class='dropdown-menu dropdown-menu-right' role='menu'>
															<li><a href='#'><div id='tab21_CUSCODSearch'>ค้นหา</div></a></li>
															<li><a href='#'><div id='tab21_CUSCODClear'>เคลียร์</div></a></li>
														</ul>
													</div>
												</div>
											</div>
											<div class='col-sm-2'>	
												<div class='form-group'>
													ชื่อ-สกุลลูกค้า
													<input type='text' id='tab21_CUSNAME' class='form-control input-sm' placeholder='ชื่อ-สกุล ลูกค้า' readonly>
												</div>
											</div>
											<div class='col-sm-2'>	
												<div class='form-group'>
													ที่อยู่ในการพิมพ์สัญญา
													<select id='inpCONTNO' class='form-control' style='font-size:10pt;'>
														<option value='1' selected>1</option>
														<option value='2'>2</option>
													</select>
												</div>
											</div>
										</div>
										
										<div class='row'>
											<div class='col-sm-2 col-sm-offset-4'>	
												<div class='form-group'>
													ต้องการป้อนจำนวนเงินแบบ
													<select id='inpCONTNO' class='form-control' style='font-size:10pt;'>
														<option value='VAT' selected>รวม VAT</option>
														<option value='NVAT'>แยก VAT</option>
													</select>
												</div>
											</div>
											<div class='col-sm-2'>	
												<div class='form-group'>
													อัตราภาษี
													<input type='text' id='inpCONTNO' class='form-control input-sm' placeholder='อัตราภาษี'>
												</div>
											</div>
										</div>
										
										<div class='row'>
											<div class='col-sm-2 col-sm-offset-3'>
												เลขตัวถัง
												<div class='input-group'>
													<input type='text' id='tab21_CUSCOD' class='form-control input-sm' placeholder='เลขตัวถัง' readonly>
													<div class='input-group-btn'>
														<button type='button' id='tab21_CUSCODAction' class='btn btn-info dropdown-toggle' data-toggle='dropdown' aria-expanded='false'><i class='fa fa-list-ul'></i></button>
														<ul class='dropdown-menu dropdown-menu-right' role='menu'>
															<li><a href='#'><div id='tab21_CUSCODSearch'>ค้นหา</div></a></li>
															<li><a href='#'><div id='tab21_CUSCODClear'>เคลียร์</div></a></li>
														</ul>
													</div>
												</div>
											</div>
											<div class='col-sm-2'>
												<div class='form-group'>
													ทะเบียน
													<input type='text' id='tab21_CUSNAME' class='form-control input-sm' placeholder='ทะเบียน' readonly>
												</div>
											</div>
											<div class='col-sm-2'>
												<div class='form-group'>
													วิธีชำระค่างวด
													<select id='inpCONTNO' class='form-control' style='font-size:10pt;'>
														<option value='1' selected>1</option>
														<option value='2'>2</option>
													</select>
												</div>
											</div>
											
										</div>
									</fieldset>
								</div>
							
								<div class='tab-pane' id='tab22' style='height:calc(100vh - 330px);overflow:auto;'>
									<fieldset>
										<header>ข้อมูลอุปกรณ์เสริม</header>
										<div class='col-sm-12'>	
											<div id='table-fixed-option' class='col-sm-12' style='max-height:calc(100vh - 630px);width:100%;overflow:auto;'>
												<table id='table-option' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='100%'>
													<thead>
														<tr>
															<th>#</th>
															<th>รหัสอุปกรณ์</th>
															<th>ราคา/หน่วย</th>
															<th>จำนวน</th>
															<th>มูลค่าสินค้า</th>
															<th>ภาษี</th>
															<th>มูลค่ารวมภาษี</th>
															<th>มูลค่าทุน</th>
															<th>ภาษีทุน</th>
															<th>ทุนรวมภาษี</th>
														</tr>
													</thead>
													<tbody>
														<tr>
															<th class='optionDel' nrow=1 align='center' style='color:red;cursor:pointer;'>ลบ</th>
															<th>ประกันภัย</th>
															<th>1000</th>
															<th>5</th>
															
															<th>5000</th>
															<th>0</th>
															<th>5000</th>
															
															<th>4000</th>
															<th>0</th>
															<th>4000</th>
														</tr>
													</tbody>
												</table>
											</div>	
										</div>
										
										<div class='row'>	
											<div class='col-sm-2'>	
												<div class='form-group'>
													<br>
													<button type='button' id='optionAdd' class='btn btn-primary btn-sm' style='width:100%'>เพิ่ม</button>
												</div>
											</div>
											<div class='col-sm-2 col-sm-offset-6'>	
												<div class='form-group'>
													ต้นทุนรวม
													<input type='text' id='inpCONTNO' class='form-control input-sm' placeholder='ต้นทุนรวม' readonly>
												</div>
											</div>
											<div class='col-sm-2'>	
												<div class='form-group'>
													ราคาขาย
													<input type='text' id='inpCONTNO' class='form-control input-sm' placeholder='ราคาขาย' readonly>
												</div>
											</div>
										</div>
										
									</fieldset>
							
									<fieldset>
										<header>ข้อมูลราคา</header>
										<div class='row'>
											<div class='col-sm-2'>
												ราคาขายผ่อน
												<div class='input-group'>
													<input type='text' id='tab21_CUSCOD' class='form-control input-sm' placeholder='ราคาขายผ่อน' readonly>
													<div class='input-group-btn'>
														<button type='button' id='tab21_CUSCODAction' class='btn btn-info dropdown-toggle' data-toggle='dropdown' aria-expanded='false'><i class='fa fa-list-ul'></i></button>
														<ul class='dropdown-menu dropdown-menu-right' role='menu'>
															<li><a href='#'><div id='tab21_CUSCODSearch'>คำนวณ</div></a></li>
															<li><a href='#'><div id='tab21_CUSCODClear'>เคลียร์</div></a></li>
														</ul>
													</div>
												</div>
											</div>
											
											<div class='col-sm-2'>	
												<div class='form-group'>
													เงินดาวน์
													<input type='text' id='inpCONTNO' class='form-control input-sm' placeholder='เงินดาวน์'>
												</div>
											</div>
											<div class='col-sm-2'>	
												<div class='form-group'>
													ใบกำกับเงินดาวน์
													<input type='text' id='inpCONTNO' class='form-control input-sm' placeholder='ใบกำกับเงินดาวน์'>
												</div>
											</div>
											<div class='col-sm-2'>	
												<div class='form-group'>
													วันที่ใบกำกับ
													<input type='text' id='inpCONTNO' class='form-control input-sm' placeholder='วันที่ใบกำกับ'>
												</div>
											</div>
											<div class='col-sm-2'>	
												<div class='form-group'>
													จำนวนงวดที่ผ่อน
													<input type='text' id='inpCONTNO' class='form-control input-sm' placeholder='จำนวนงวดที่ผ่อน'>
												</div>
											</div>
											<div class='col-sm-2'>	
												<div class='form-group'>
													ผ่อนชำระ เดือน/งวด
													<input type='text' id='inpCONTNO' class='form-control input-sm' placeholder='ผ่อนชำระ'>
												</div>
											</div>
											
											
										</div>
									</fieldset>
								</div>
							
								<div class='tab-pane' id='tab23' style='height:calc(100vh - 330px);overflow:auto;'>
									<fieldset>
										<div class='row'>
											<div class='col-sm-2 col-sm-offset-3'>	
												<div class='form-group'>
													ค่างวดแรก
													<input type='text' id='inpCONTNO' class='form-control input-sm' placeholder='ค่างวดแรก'>
												</div>
											</div>
											<div class='col-sm-2'>	
												<div class='form-group'>
													ค่างวดถัดไป
													<input type='text' id='inpCONTNO' class='form-control input-sm' placeholder='ค่างวดถัดไป'>
												</div>
											</div>
											<div class='col-sm-2'>	
												<div class='form-group'>
													ค่างวดสุดท้าย + ภาษี
													<input type='text' id='inpCONTNO' class='form-control input-sm' placeholder='ค่างวดสุดท้าย + ภาษี'>
												</div>
											</div>
										</div>
										
										<div class='row'>
											<div class='col-sm-2 col-sm-offset-3'>	
												<div class='form-group'>
													ราคาขายหน้าร้าน
													<input type='text' id='inpCONTNO' class='form-control input-sm' placeholder='ราคาขายหน้าร้าน'>
												</div>
											</div>
											<div class='col-sm-2'>	
												<div class='form-group'>
													ราคาขายสดสุทธิ
													<input type='text' id='inpCONTNO' class='form-control input-sm' placeholder='ราคาขายสดสุทธิ'>
												</div>
											</div>
											<div class='col-sm-2'>	
												<div class='form-group'>
													ดอกผลเช่าซื้อ
													<input type='text' id='inpCONTNO' class='form-control input-sm' placeholder='ดอกผลเช่าซื้อ'>
												</div>
											</div>
											<div class='col-sm-1'>	
												<div class='form-group'>
													<br>
													<button type='button' id='optionAdd' class='btn btn-primary btn-sm' style='width:100%'>รายละเอียด</button>
												</div>
											</div>
										</div>
										
									</fieldset>
									
									<fieldset>
										<div class='row'>
											<div class='col-sm-2 col-sm-offset-2'>	
												<div class='form-group'>
													วันดิวงวดแรก
													<input type='text' id='inpCONTNO' class='form-control input-sm' placeholder='วันดิวงวดแรก'>
												</div>
											</div>
											<div class='col-sm-2'>	
												<div class='form-group'>
													วันดิวงวดสุดท้าย
													<input type='text' id='inpCONTNO' class='form-control input-sm' placeholder='วันดิวงวดสุดท้าย'>
												</div>
											</div>
											<div class='col-sm-2'>	
												<div class='form-group'>
													เลขที่ใบปล่อยรถ
													<input type='text' id='inpCONTNO' class='form-control input-sm' placeholder='เลขที่ใบปล่อยรถ'>
												</div>
											</div>
											<div class='col-sm-2'>	
												<div class='form-group'>
													วันที่ปล่อยรถ
													<input type='text' id='inpCONTNO' class='form-control input-sm' placeholder='วันที่ปล่อยรถ'>
												</div>
											</div>
										</div>
										
										<div class='row'>
											<div class='col-sm-2 col-sm-offset-3'>	
												<div class='form-group'>
													รหัสพนักงานเก็บเงิน
													<input type='text' id='inpCONTNO' class='form-control input-sm' placeholder='รหัสพนักงานเก็บเงิน'>
												</div>
											</div>
											<div class='col-sm-2'>	
												<div class='form-group'>
													รหัสผู้ตรวจสอบ
													<input type='text' id='inpCONTNO' class='form-control input-sm' placeholder='รหัสผู้ตรวจสอบ'>
												</div>
											</div>
											<div class='col-sm-2'>	
												<div class='form-group'>
													รหัสพนักงานขาย
													<input type='text' id='inpCONTNO' class='form-control input-sm' placeholder='รหัสพนักงานขาย'>
												</div>
											</div>
										</div>	
										
										<div class='row'>
											<div class='col-sm-2 col-sm-offset-2'>	
												<div class='form-group'>
													อัตราเบี้ยปรับล่าช้า
													<input type='text' id='inpCONTNO' class='form-control input-sm' placeholder='อัตราเบี้ยปรับล่าช้า'>
												</div>
											</div>
											<div class='col-sm-2'>	
												<div class='form-group'>
													ชำระล่าช้าได้ไม่เกิน
													<input type='text' id='inpCONTNO' class='form-control input-sm' placeholder='ชำระล่าช้าได้ไม่เกิน'>
												</div>
											</div>
											<div class='col-sm-2'>	
												<div class='form-group'>
													อัตราดอกเบี้ยทำเช่าซื้อ
													<input type='text' id='inpCONTNO' class='form-control input-sm' placeholder='อัตราดอกเบี้ยทำเช่าซื้อ'>
												</div>
											</div>
											<div class='col-sm-2'>	
												<div class='form-group'>
													อัตราดอกเบี้ยเช่าซื้อจริง
													<input type='text' id='inpCONTNO' class='form-control input-sm' placeholder='อัตราดอกเบี้ยเช่าซื้อจริง'>
												</div>
											</div>
										</div>
										
										<div class='row'>
											<div class='col-sm-2 col-sm-offset-3'>	
												<div class='form-group'>
													ค่านายหน้าขาย
													<input type='text' id='inpCONTNO' class='form-control input-sm' placeholder='ค่านายหน้าขาย'>
												</div>
											</div>
											<div class='col-sm-2 col-sm-offset-0'>	
												<div class='form-group'>
													กิจกรรมการขาย
													<input type='text' id='inpCONTNO' class='form-control input-sm' placeholder='กิจกรรมการขาย'>
												</div>
											</div>
											<div class='col-sm-2'>	
												<div class='form-group'>
													กำหนดวันดิว
													<select id='add3_setdue' class='form-control jform-control-sm jRME jRME selectpicker' data-live-search='false' data-live-search-placeholder='ค้นหา' title='เลือก' data-size='8' data-actions-box='false'>
														<option value='N' selected>กำหนดให้วันดิว งวดถัดไปวันเดียวกับงวดแรก</option>
														<option value='Y'>กำหนดให้วันดิว งวดถัดไปเป็นสิ้นเดือน</option>
													</select>
												</div>
											</div>
										</div>
									</fieldset>
									
								</div>
							
								<div class='tab-pane' id='tab24' style='height:calc(100vh - 330px);overflow:auto;'>
									<fieldset>
										<div class='row'>
											<div class='col-sm-2 col-sm-offset-3'>
												รหัส ผู้แนะนำการซื้อ
												<div class='input-group'>
													<input type='text' id='#' class='form-control input-sm' placeholder='รหัส ผู้แนะนำการซื้อ' readonly>
													<div class='input-group-btn'>
														<button type='button' id='#' class='btn btn-info dropdown-toggle' data-toggle='dropdown' aria-expanded='false'><i class='fa fa-list-ul'></i></button>
														<ul class='dropdown-menu dropdown-menu-right' role='menu'>
															<li><a href='#'><div id='#'>ค้นหา</div></a></li>
															<li><a href='#'><div id='#'>เคลียร์</div></a></li>
														</ul>
													</div>
												</div>
											</div>
											<div class='col-sm-4'>	
												<div class='form-group'>
													ชื่อ-สกุล ผู้แนะนำการซื้อ
													<input type='text' id='#' class='form-control input-sm' placeholder='ชื่อ-สกุล ผู้แนะนำการซื้อ' readonly>
												</div>
											</div>
										</div>
										
										<div class='row'>
											<div class='col-sm-4 col-sm-offset-3'>
												<div class='form-group'>
													วิธีคำนวณส่วนลดตัดสด คำนวณจาก
													<div>
														<label class='radio lobiradio lobiradio-lg'>
															<input type='radio' name='nameRedio2' checked=''> 
															<i></i> % ส่วนลดของดอกเบี้ยคงเหลือ(สคบ.)
														</label>
														<label class='radio lobiradio lobiradio-lg'>
															<input type='radio' name='nameRedio2'> 
															<i></i> % ส่วนลดของดอกเบี้ยทั้งหมด
														</label>
														<label class='radio lobiradio lobiradio-lg'>
															<input type='radio' name='nameRedio2'> 
															<i></i> % ส่วนลดต่อเดือน(HP DOS)
														</label>
													</div>
												</div>	
											</div>
											<div class='col-sm-4'>
												<div class='form-group'>
													วิธีคำนวณเบี้ยปรับ
													<div>
														<label class='radio lobiradio lobiradio-lg'>
															<input type='radio' name='nameRedio'> 
															<i></i> ตามอัตรา MRR+ค่าคงที่
														</label>
														<label class='radio lobiradio lobiradio-lg'>
															<input type='radio' name='nameRedio' checked=''> 
															<i></i> ตามอัตราเบี้ยปรับต่อเดือน
														</label>
													</div>
												</div>	
											</div>
										</div>
										<div class='row'>
											<div class='col-sm-2 col-sm-offset-2'>	
												<div class='form-group'>
													ชำระเงินดาวน์แล้ว
													<input type='text' id='#' class='form-control input-sm' placeholder='ชำระเงินดาวน์แล้ว'>
												</div>
											</div>
											<div class='col-sm-2'>	
												<div class='form-group'>
													รับชำระเงินแล้วทั้งหมด
													<input type='text' id='#' class='form-control input-sm' placeholder='รับชำระเงินแล้วทั้งหมด'>
												</div>
											</div>

											<div class='col-sm-2'>	
												<div class='form-group'>
													ค่าคอมบุคคลนอก
													<input type='text' id='#' class='form-control input-sm' placeholder='ค่าคอมบุคคลนอก'>
												</div>
											</div>
											<div class='col-sm-2'>	
												<div class='form-group'>
													ค่าของแถม
													<input type='text' id='#' class='form-control input-sm' placeholder='ค่าของแถม'>
												</div>
											</div>
										</div>
										<div class='row'>
											<div class='col-sm-2 col-sm-offset-2'>	
												<div class='form-group'>
													ค่าใช้จ่ายอื่นๆ
													<input type='text' id='#' class='form-control input-sm' placeholder='ค่าใช้จ่ายอื่นๆ'>
												</div>
											</div>
											<div class='col-sm-2'>	
												<div class='form-group'>
													เลขที่บิล das 1
													<input type='text' id='#' class='form-control input-sm' placeholder='เลขที่บิล das 1'>
												</div>
											</div>

											<div class='col-sm-2'>	
												<div class='form-group'>
													เลขที่บิล das 2
													<input type='text' id='#' class='form-control input-sm' placeholder='เลขที่บิล das 2'>
												</div>
											</div>
											<div class='col-sm-2'>	
												<div class='form-group'>
													เลขที่บิล das 3
													<input type='text' id='#' class='form-control input-sm' placeholder='เลขที่บิล das 3'>
												</div>
											</div>
										</div>
										<div class='row'>
											<div class='col-sm-8 col-sm-offset-2'>	
												หมายเหตุ
												<textarea id='add4_memo1' class='form-control' rows='4'></textarea>
											</div>
										</div>
									</fieldset>
								</div>
						
								<div class='tab-pane' id='tab25' style='height:calc(100vh - 330px);overflow:auto;'>
									<fieldset>
										<div class='col-sm-2'>	
											<div class='form-group'>
												ชื่อ-สกุล ลูกค้า
												<input type='text' id='inpCONTNO' class='form-control input-sm' placeholder='ชื่อ-สกุล ลูกค้า'>
											</div>
										</div>
									</fieldset>
								</div>
								
								<ul class='pager'>
									<li class='previous first disabled' style='display:block;'><a href='javascript:void(0)'>First</a></li>
									<li class='previous disabled'><a href='javascript:void(0)'>Previous</a></li>
									<li class='next last' style='display:none;'><a href='javascript:void(0)'>Last</a></li>
									<li class='next'><a href='javascript:void(0)'>Next</a></li>
								</ul>
							</div>
						</form>
					</div>
				</div>
			</div>
		";
		
		$html.= "<script src='".base_url('public/js/SYS04/ChirePurchase.js')."'></script>";
		echo $html;
	}
	
	function getCustomersForm(){
		$html = "		
			<div style='width:100%;height:60px;background-color:white;'>
				<div class='row'>	
					<div class='col-sm-2'>	
						<div class='form-group'>
							รหัสลูกค้า
							<input type='text' id='CUSCOD' class='form-control input-sm' placeholder='รหัสลูกค้า'>
						</div>
					</div>
					<div class='col-sm-5'>
						<div class='form-group'>
							ชื่อ-สกุล ลูกค้า
							<input type='text' id='CUSNAME' class='form-control input-sm' placeholder='ชื่อ-สกุล ลูกค้า'>
						</div>
					</div>
					<div class='col-sm-3'>	
						<div class='form-group'>
							เลข ปปช.
							<input type='text' id='CUSCOD' class='form-control input-sm' placeholder='เลข ปปช.'>
						</div>
					</div>
					<div class='col-sm-2'>
						<div class='form-group'>
							<br>
							<input type='button' id='customerSearch' class='btn btn-primary btn-sm' value='แสดง' style='width:100%'>
						</div>
					</div>
				</div>
			</div>
			<div id='resultCustomers' style='width:100%;height:calc(100% - 60px);background-color:white;'></div>
		";
		
		$response = array("html"=>$html);
		echo json_encode($response);
	}
	
	function getCustomersSearch(){
		$arrs = array();
		$arrs['CUSCOD'] = $_REQUEST['CUSCOD'];
		$arrs['CUSNAME'] = $_REQUEST['CUSNAME'];
		
		$tr = "";
		for($i=1;$i<100;$i++){
			$tr .= "
				<tr class='trow' seq=".$i.">
					<td class='getit' seq=".$i." CUSCOD='๑HI-1801001".($i < 10 ? '0'.$i:$i)."' CUSNAME='นายทด".$i." สกุลสอบ'  style='width:50px;cursor:pointer;text-align:center;'>เลือก</td>
					<td>๑HI-1801001".($i < 10 ? '0'.$i:$i)."</td>
					<td>นายทด".$i." สกุลสอบ</td>
					<td>Fกป</td>
					<td>A</td>
				</tr>
			";
		}
		$html = "
			<div id='table-fixed-getCustomersSearch' class='col-sm-12' style='height:100%;width:100%;overflow:auto;'>
				<table id='data-table-example2' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='100%'>
					<thead>
						<tr>
							<th>#</th>
							<th>รหัสลูกค้า</th>
							<th>ชื่อ-สกุล ลูกค้า</th>
							<th>สาขา</th>
							<th>กลุ่ม</th>
						</tr>
					</thead>	
					<tbody>
						".$tr."
					</tbody>
				</table>
			</div>
		";
		
		$response = array("html"=>$html);
		echo json_encode($response);
	}
}




















