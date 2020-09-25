<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/********************************************************
             ______@17/04/2019______
            / / _ _   _ _     __ 
           / // __ \ / __ \ / __ \
       _ _/ // /_/ // / / // /_/ /
     /_ _ _/ \_ _ //_/ /_/ \__  /
                          _ _/ /
                         /___ /
********************************************************/
class Finance extends MY_Controller {
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
			<div class='tab1' name='home' locat='{$this->sess['branch']}' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}' style='height:calc(100vh - 132px);overflow:auto;background-color:white;'>
				<div class='col-sm-12' style='overflow:auto;'>					
					<div class='row'>
						<div class=' col-sm-2'>	
							<div class='form-group'>
								เลขที่สัญญา
								<input type='text' id='CONTNO' class='form-control input-sm' placeholder='เลขที่สัญญา' >
							</div>
						</div>
						<div class=' col-sm-2'>	
							<div class='form-group'>
								วันที่ทำสัญญา
								<input type='text' id='SDATEFRM' class='form-control input-sm' placeholder='จาก' data-provide='datepicker' data-date-language='th-th' value='".$this->today('startofmonth')."'>
							</div>
						</div>	
						<div class=' col-sm-2'>	
							<div class='form-group'>
								ถึง
								<input type='text' id='SDATETO' class='form-control input-sm' placeholder='ถึง' data-provide='datepicker' data-date-language='th-th' value='".$this->today('endofmonth')."'>
							</div>
						</div>	
						<div class=' col-sm-2'>	
							<div class='form-group'>
								สาขา
								<input type='text' id='LOCAT' class='form-control input-sm' placeholder='สาขา'  value='".$this->sess['branch']."'>
							</div>
						</div>
						<div class=' col-sm-2'>	
							<div class='form-group'>
								เลขตัวถัง
								<input type='text' id='STRNO' class='form-control input-sm' placeholder='เลขตัวถัง' >
							</div>
						</div>
						<div class=' col-sm-2'>	
							<div class='form-group'>
								ชื่อ - สกุล ลูกค้า
								<input type='text' id='NAME' class='form-control input-sm' placeholder='ชื่อ - สกุล ลูกค้า' >
							</div>
						</div>
					</div>
					<div class='row'>
						<div class=' col-sm-6'>	
							<div class='form-group'>
								<button id='btnt1finance' class='btn btn-cyan btn-block'><span class='glyphicon glyphicon-pencil'> ขายส่งไฟแนนซ์</span></button>
							</div>
						</div>
						<div class=' col-sm-6'>	
							<div class='form-group'>
								<button id='btnt1search' class='btn btn-primary btn-block'><span class='glyphicon glyphicon-search'> ค้นหา</span></button>
							</div>
						</div>
					</div>
					<div class='row'>
						<div id='searchresult'></div>
					</div>	
				</div>
			</div>
		";
		
		$html.= "<script src='".base_url('public/js/SYS04/Finance.js')."'></script>";
		echo $html;
	}
	
	function search(){
		$arrs = array();
		$arrs['contno']	  = $_REQUEST['contno'];
		$arrs['sdatefrm'] = $this->Convertdate(1,$_REQUEST['sdatefrm']);
		$arrs['sdateto']  = $this->Convertdate(1,$_REQUEST['sdateto']);
		$arrs['locat'] 	  = $_REQUEST['locat'];
		$arrs['strno'] 	  = $_REQUEST['strno'];
		$arrs['name']     = $_REQUEST['name'];
		
		$cond = "";
		$condDesc = "";
		if($arrs['contno'] != ""){
			$condDesc .= " เลขที่สัญญา ".$arrs['contno'];
			$cond .= " and A.CONTNO like '".$arrs['contno']."%'";
		}
		
		if($arrs['locat'] != ""){
			$condDesc .= " สาขา ".$arrs['locat'];
			$cond .= " and A.LOCAT like '".$arrs['locat']."%'";
		}
		
		if($arrs['sdatefrm'] != "" and $arrs['sdateto'] != ""){
			$condDesc .= " วันที่ ".$_REQUEST['sdatefrm']." - ".$_REQUEST['sdateto'];
			$cond .= " and convert(varchar(8),A.SDATE,112) between '".$arrs['sdatefrm']."' and '".$arrs['sdateto']."' ";
		}else if($arrs['sdatefrm'] != "" and $arrs['sdateto'] == ""){
			$condDesc .= " วันที่ ".$_REQUEST['sdatefrm'];
			$cond .= " and convert(varchar(8),A.SDATE,112) = '".$arrs['sdatefrm']."'";
		}else if($arrs['sdatefrm'] == "" and $arrs['sdateto'] != ""){
			$condDesc .= " วันที่ ".$_REQUEST['sdateto'];
			$cond .= " and convert(varchar(8),A.SDATE,112) = '".$arrs['sdateto']."'";
		}
		
		if($arrs['strno'] != ""){
			$condDesc .= " เลขตัวถัง ".$arrs['strno'];
			$cond .= " and A.STRNO like '".$arrs['strno']."%'";
		}
		if($arrs['name'] != ""){
			$condDesc .= " ลูกค้า ".$arrs['name'];
			$cond .= " and (C.NAME1 like '".$arrs['name']."%' or C.NAME2 like '".$arrs['name']."%')" ;
		}
		$sql = "
			select 
				".($cond == "" ? "top 20":"")." A.CONTNO,A.LOCAT,convert(varchar(8),A.SDATE,112) as SDATE
				,A.CUSCOD,C.SNAM+C.NAME1+' '+C.NAME2 as NAME,A.STRNO,A.RESVNO
			from {$this->MAuth->getdb('ARFINC')} A,{$this->MAuth->getdb('CUSTMAST')} C
			,{$this->MAuth->getdb('INVTRAN')} I 
			where A.CUSCOD=C.CUSCOD and A.STRNO=I.STRNO ".$cond."
		";
		//echo $sql; exit;		
		$query = $this->db->query($sql);
		
		$html = "";
		$NRow = 1;
		if($query->row()){
			foreach($query->result() as $row){
				$html .= "
					<tr>
						<td style='width:40px'>
							<i class='financeDetails btn btn-xs btn-success glyphicon glyphicon-zoom-in' contno='".$row->CONTNO."' style='cursor:pointer;'> รายละเอียด  </i>
						</td>
						<td style='vertical-align:middle;'>".$row->CONTNO."</td>
						<td style='vertical-align:middle;'>".$row->LOCAT."</td>
						<td style='vertical-align:middle;'>".$this->Convertdate(2,$row->SDATE)."</td>
						<td style='vertical-align:middle;'>".$row->CUSCOD."</td>
						<td style='vertical-align:middle;'>".$row->NAME."</td>
						<td style='vertical-align:middle;'>".$row->STRNO."</td>
						<td style='vertical-align:middle;'>".$row->RESVNO."</td>
					</tr>
				";
				$NRow++;
			}
		}
		
		$html = "
			<div id='table-fixed-Finance' class='col-sm-12' style='height:calc(100% - 30px);width:100%;overflow:auto;font-size:8pt;'>
				<table id='table-Finance' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='100%' border=1>
					<thead>						
						<tr style='line-height:20px;'>
							<td style='vertical-align:middle;background-color:#ccc;text-align:center;font-size:8pt;' colspan='8'>
								เงื่อนไข :: {$condDesc}
							</td>
						</tr>
						<tr>
							<th width='20px;' style='vertical-align:middle;background-color:#ccc;'>#</th>
							<th style='vertical-align:middle;background-color:#ccc;'>เลขที่สัญญา</th>
							<th style='vertical-align:middle;background-color:#ccc;'>สาขา</th>
							<th style='vertical-align:middle;background-color:#ccc;'>วันที่ขาย</th>
							<th style='vertical-align:middle;background-color:#ccc;'>รหัสลูกค้า</th>
							<th style='vertical-align:middle;background-color:#ccc;'>ชื่อ-สกุล</th>
							<th style='vertical-align:middle;background-color:#ccc;'>เลขตัวถัง</th>
							<th style='vertical-align:middle;background-color:#ccc;'>เลขที่ใบจอง</th>
						</tr>
					</thead>	
					<tbody>						
						".$html."
					</tbody>
				</table>
			</div>
		";
		
		$response = array("html"=>$html,"status"=>true);
		echo json_encode($response);
	}	
	//function getfromAgent(){
	function getfromFinance(){	
		$data = array();
		
		$sql = "
			select * from {$this->MAuth->getdb('VATMAST')}
			where getdate() between FRMDATE and TODATE
		";
		$query = $this->db->query($sql);
		$row = $query->row();
		$data["vatrt"] = number_format($row->VATRT,2);
		
		$sql = "
			select CALINT,DISC_FM from {$this->MAuth->getdb('CONDPAY')}			
		";
		$query = $this->db->query($sql);
		$row = $query->row();
		$data["CALINT"] = $row->CALINT;
		$data["DISC_FM"] = $row->DISC_FM;
		
		$html = "
			<div id='wizard-finance' class='wizard-wrapper'>    
				<div class='wizard'>
					<form id='demo-form2' action='' class='lobi-form' novalidate='novalidate'>
						<ul class='wizard-tabs wizard-tab-balls nav-justified nav nav-pills'>
							<li class='active'>
								<a href='#tab11' prev='#' data-toggle='tab' aria-expanded='true'>
									<span class='step'>1</span>
									<span class='title'>ผู้ซื้อสินค้า</span>
								</a>
							</li>
							<li>
								<a href='#tab22' prev='#tab11' data-toggle='tab'>
									<span class='step'>2</span>
									<span class='title'>รายการสินค้า</span>
								</a>
							</li>
							<li>
								<a href='#tab33' prev='#tab22' data-toggle='tab'>
									<span class='step'>3</span>
									<span class='title'>บันทึกเพิ่มเติม</span>
								</a>
							</li>
							
						</ul>
						<div class='tab-content bg-white'>
							".$this->getfromFinanceTab11($data)."
							".$this->getfromFinanceTab22($data)."
							".$this->getfromFinanceTab33($data)."							
							
							<!-- ul class='pager'>
								<li class='previous first disabled' style='display:none;'><a href='javascript:void(0)'>First</a></li>
								<li class='previous disabled'><a href='javascript:void(0)'>ย้อนกลับ</a></li>
								<li class='next last' style='display:none;'><a href='javascript:void(0)'>Last</a></li>
								<li class='next'><a href='javascript:void(0)'>ถัดไป</a></li>
							</ul -->
						</div>
					</form>
				</div>
			</div>
			<div>
				<div class='col-sm-6 text-left'>
					<br>
					<input type='button' id='btnTax' class='btn btn-xs btn-info' style='width:100px;' value='ใบกำกับ' disabled>
					<input type='button' id='btnSend' class='btn btn-xs btn-info' style='width:100px;' value='ใบส่งมอบ' disabled>
					<input type='button' id='btnApproveSell' class='btn btn-xs btn-info' style='width:100px;' value='ใบอนุมัติขาย' disabled>
				</div>
				<div class='col-sm-6 text-right'>
					<input type='button' id='add_save' class='btn btn-xs btn-primary right' style='width:100px;' value='บันทึก' >
					
					<input type='button' id='add_delete' class='btn btn-xs btn-danger right' style='width:100px;' value='ลบ' >
				</div>
			</div>
		";
		
		$response = array('html'=>$html,'status'=>true);
		echo json_encode($response);
	}
	
	private function getfromFinanceTab11($data){
		$html = "
			<div class='tab-pane active' name='tab11' style='height:calc(100vh - 260px);overflow:auto;'>
				<fieldset style='height:100%'>
					<div class='row' style='height:100%'>
						<div style='float:left;height:100%;overflow:auto;' class='col-sm-8 col-sm-offset-2'>
							<div class='row'>
								<div class='col-sm-4'>	
									<div class='form-group'>
										เลขที่สัญญา
										<input type='text' id='add_contno' class='form-control input-sm' placeholder='เลขที่สัญญา' >
									</div>
								</div>
								<div class='col-sm-4'>	
									<div class='form-group'>
										ทำสัญญาขายที่สาขา
										<select id='add_locat' class='form-control input-sm' data-placeholder='ทำสัญญาขายที่สาขา'>
											<option value='".$this->sess['branch']."'>".$this->sess['branch']."</option>
										</select>
									</div>
								</div>
								<div class='col-sm-4'>	
									<div class='form-group'>
										วันที่ทำสัญญา
										<input type='text' id='add_sdate' class='form-control input-sm' placeholder='วันที่ทำสัญญา' data-provide='datepicker' data-date-language='th-th' value='".$this->today('today')."'>
									</div>
								</div>
							</div>
							<div class='row'>
								<div class='col-sm-4'>	
									<div class='form-group'>
										เลขที่ใบจอง
										<select id='add_resvno' class='form-control input-sm' data-placeholder='เลขที่ใบจอง'></select>
									</div>
								</div>
								<div class='col-sm-4'>	
									<div class='form-group'>
										เลขที่ใบอนุมัติ
										<input type='text' id='add_approve' class='form-control input-sm' placeholder='เลขที่ใบอนุมัติ' >
									</div>
								</div>
								<div class='col-sm-4'>	
									<div class='form-group'>
										ลูกค้า
										<div class='input-group'>
										   <input type='text' id='add_cuscod' CUSCOD='' class='form-control input-sm' placeholder='ลูกค้า'  value=''>
										   <span class='input-group-btn'>
										   <button id='add_cuscod_removed' class='btn btn-danger btn-sm' type='button'>
												<span class='glyphicon glyphicon-remove' aria-hidden='true'></span></button>
										   </span>
										</div>
									</div>
								</div>
							</div>
							<div class='row'>
								<div class='col-sm-4'>	
									<div class='form-group'>
										ต้องการป้อนจำนวนเงินแบบ
										<select id='add_inclvat' class='form-control input-sm' data-placeholder='ต้องการป้อนจำนวนเงินแบบ'>
											<option value='Y' selected>รวม VAT</option>
											<option value='N'>แยก VAT</option>
										</select>
									</div>
								</div>
								<div class='col-sm-4'>	
									<div class='form-group'>
										อัตราภาษี
										<input type='text' id='add_vatrt' class='form-control input-sm' placeholder='อัตราภาษี' value='".$data["vatrt"]."'>
									</div>
								</div>
								<div class='col-sm-4'>	
									<div class='form-group'>
										ที่อยู่ในการพิมพ์สัญญา
										<select id='add_addrno' class='form-control input-sm' data-placeholder='ที่อยู่ในการพิมพ์สัญญา'></select>
									</div>
								</div>
							</div>
							<div class='row'>
								<div class='col-sm-4'>	
									<div class='form-group'>
										เลขตัวถัง
										<select id='add_strno' class='form-control input-sm' data-placeholder='เลขตัวถัง'></select>
									</div>
								</div>
								<div class='col-sm-4'>	
									<div class='form-group'>
										ทะเบียน
										<input type='text' id='add_reg' class='form-control input-sm' placeholder='ทะเบียน' >
									</div>
								</div>
								<div class=' col-sm-4'>	
									<div class='form-group'>
										กิจกรรมการขาย
										<select id='add_acticod' class='form-control input-sm' data-placeholder='กิจกรรมการขาย'></select>
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
	
	private function getfromFinanceTab22($data){
		$html = "
			<div class='tab-pane' name='tab22' style='height:calc(100vh - 260px);overflow:auto;'>
				<fieldset style='height:100%'>
					<div class='row' style='height:100%'>
						<div style='float:left;height:100%;overflow:none;' class='col-sm-9'>
							<div class='row' style='width:100%;height:100%;padding-left:30px;background-color:#269da1;'>
								<div class='form-group col-sm-12' style='height:100%;'>
									<span style='color:#34dfb5;'>รายการอุปกรณ์เสริม</span> &emsp;&emsp; <span style='color:#efff14;'>บันทึกอุปกรณ์เสริมเพื่อขายรวมกับตัวรถ</span>
									<div id='dataTable-fixed-inopt' class='dataTables_wrapper dt-bootstrap4 table-responsive' style='max-height:calc(100% - 130px);height:calc(100% - 130px);overflow:auto;border:1px dotted black;background-color:#eee;'>
										<table id='dataTables-inopt' class='table table-bordered dataTable table-hover' stat='' aria-describedby='dataTable_info' style='width:100%;line-height:10px;white-space:nowrap;text-overflow:ellipsis;overflow:hidden;' cellspacing='0'>
											<thead>
												<tr role='row'>
													<th style='width:40px'>
														<i id='add_inopt' class='btn btn-xs btn-success glyphicon glyphicon-plus' style='cursor:pointer;'> เพิ่ม  </i>
													</th>
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
											<tbody style='white-space: nowrap;'></tbody>
										</table>
										
									</div>
									<div class='row' style='width:100%;padding-left:30px;background-color:#269da1;'>
										<div style='float:left;height:100%;overflow:auto;' class='col-sm-8 col-sm-offset-2'>
											<div class='form-group col-sm-4 col-sm-offset-2'>
												<label class='jzfs10' for='add2_optcost' style='color:#34dfb5;'>ต้นทุนรวม</label>
												<input type='text' id='add2_optcost' class='form-control input-sm text-right' value='' disabled>
												<span id='error_add2_optcost' class='error text-danger jzError'></span>		
											</div>
											
											<div class='form-group col-sm-4'>
												<label class='jzfs10' for='add2_optsell' style='color:#34dfb5;'>ราคาขาย</label>
												<input type='text' id='add2_optsell' class='form-control input-sm text-right' value='' disabled>
												<span id='error_add2_optsell' class='error text-danger jzError'></span>		
											</div>												
										</div>
									</div>
								</div>
							</div>							
						</div>
						
						<div style='float:left;background-color:white;';' class='col-sm-3'>
							<div class='col-sm-12 col-lg-6'>	
								<div class='form-group'>
									ราคาขาย
									<div class='form-group'>
										<label class='input'>
											<span id='add_inprcCal' class='input-icon input-icon-append glyphicon glyphicon-info-sign'></span>
											<input type='text' id='add_inprc' class='form-control input-sm' placeholder='ราคาขาย' >
										</label>
									</div>
								</div>
							</div>
							<div class='col-sm-12 col-lg-6'>	
								<div class='form-group'>
									เงินดาวน์
									<input type='text' id='add_indwn' class='form-control input-sm' placeholder='เงินดาวน์' >
								</div>
							</div>
							<div class='col-sm-12 col-lg-6'>	
								<div class='form-group'>
									ราคาขายหน้าร้าน
									<input type='text' id='add_dwninv' class='form-control input-sm' placeholder='ราคาขายหน้าร้าน' >
								</div>
							</div>
							
							<div class='col-sm-12 col-lg-6'>	
								<div class='form-group'>
									ส่วนลด
									<input type='text' id='add_dwninvDt' class='form-control input-sm' placeholder='ส่วนลด'>
								</div>
							</div>
							
							<div class='col-sm-12 col-lg-6'>	
								รหัสบริษัทไฟแนนท์
								<select id='add_fincode' class='form-control input-sm' placeholder='พนักงานขาย'></select>
							</div>
							<div class='col-sm-12 col-lg-6'>	
								ค่าคอมไฟแนนท์
								<input type='text' id='add_commission' class='form-control input-sm' placeholder='ค่าคอมไฟแนนท์'>
							</div>
							
							<div class='col-sm-12 col-lg-6'>	
								พนักงานขาย
								<!-- input type='text' id='add_salcod' class='form-control input-sm' placeholder='พนักงานขาย' -->
								<select id='add_salcod' class='form-control input-sm' placeholder='พนักงานขาย'></select>
							</div>
							<div class='col-sm-12 col-lg-6'>	
								ค่านายหน้าขาย
								<input type='text' id='add_facesale' class='form-control input-sm' placeholder='ค่านายหน้าขาย'>
							</div>
							
							<div class='col-sm-12 col-lg-6'>	
								<div class='form-group'>
									ใบกำกับเงินดาวน์
									<input type='text' id='add_dwninv' class='form-control input-sm' placeholder='ใบกำกับเงินดาวน์'  disabled>
								</div>
							</div>
							
							<div class='col-sm-12 col-lg-6'>	
								<div class='form-group'>
									วันที่ใบกำกับ
									<input type='text' id='add_dwninvDt' class='form-control input-sm' placeholder='วันที่ใบกำกับ' data-provide='datepicker' data-date-language='th-th' disabled>
								</div>
							</div>
							
							<div class='col-sm-12 col-lg-6'>	
								<div class='form-group'>
									เลขที่ปล่อยรถ
									<input type='text' id='add_issuno' class='form-control input-sm' placeholder='เลขที่ปล่อยรถ' >
								</div>
							</div>
							<div class='col-sm-12 col-lg-6'>	
								<div class='form-group'>
									วันที่ปล่อยรถ
									<input type='text' id='add_issudt' class='form-control input-sm' placeholder='วันที่ปล่อยรถ' data-provide='datepicker' data-date-language='th-th' value='".$this->today('today')."'>
								</div>
							</div>
						</div>
					</div>
				</fieldset>
			</div>
		";
		return $html;
	}
	
	private function getfromFinanceTab33($data){
		$html = "
			<div class='tab-pane' name='tab33' style='height:calc(100vh - 260px);overflow:auto;'>
				<fieldset style='height:100%'>
					<div style='float:left;' class='col-sm-8 '>
						<div class='row'>
							<div class=' col-sm-4'>	
								<div class='form-group'>
									ผู้แนะนำการซื้อ
									<div class='input-group'>
									   <input type='text' id='add_recomcod' CUSCOD='' class='form-control input-sm' placeholder='ผู้แนะนำการซื้อ'  value=''>
									   <span class='input-group-btn'>
									   <button id='add_recomcod_removed' class='btn btn-danger btn-sm' type='button'>
											<span class='glyphicon glyphicon-remove' aria-hidden='true'></span></button>
									   </span>
									</div>
								</div>
							</div>
							
							<div class='col-sm-4'>	
								<div class='form-group'>
									ชำระเงินดาวน์แล้ว
									<input type='text' id='add_paydown' class='form-control input-sm' placeholder='ชำระเงินดาวน์แล้ว' disabled>
								</div>
							</div>
							
							<div class='col-sm-4'>	
								<div class='form-group'>
									รับชำระเงินแล้วทั้งหมด
									<input type='text' id='add_payall' class='form-control input-sm' placeholder='รับชำระเงินแล้วทั้งหมด' disabled>
								</div>
							</div>
							
							<div class='col-sm-4'>	
								<div class='form-group'>
									ค่าคอมบุคคลนอก
									<input type='text' id='add_commission' class='form-control input-sm' placeholder='ค่าคอมบุคคลนอก' value='0.00'>
								</div>
							</div>
							
							<div class='col-sm-4'>	
								<div class='form-group'>
									ค่าของแถม
									<input type='text' id='add_free' class='form-control input-sm' placeholder='ค่าของแถม' value='0.00'>
								</div>
							</div>
							
							<div class='col-sm-4'>	
								<div class='form-group'>
									ค่าใช้จ่ายอื่นๆ
									<input type='text' id='add_payother' class='form-control input-sm' placeholder='ค่าใช้จ่ายอื่นๆ' value='0.00'>
								</div>
							</div>
							
							<div class='col-sm-4'>	
								<div class='form-group'>
									เลขที่ใบลดหนี้
									<input type='text' id='add_crdtxno' class='form-control input-sm' placeholder='เลขที่ใบลดหนี้' >
								</div>
							</div>
							<div class='col-sm-4 col-sm-offset-4'>	
								<div class='form-group'>
									จำนวนเงินที่ลดหนี้
									<input type='text' id='add_crdamt' class='form-control input-sm' placeholder='จำนวนเงินที่ลดหนี้' value='0.00'>
								</div>
							</div>
							
							
							<div class='2 col-sm-12'>	
								<div class='form-group'>
									หมายเหตุ
									<textarea type='text' id='add_memo1' class='form-control input-sm' placeholder='หมายเหตุ' rows=4 style='resize:vertical;'></textarea>
								</div>
							</div>
						</div>
					</div>
					<div style='float:left;border:1px dotted red;' class='col-sm-4'>
						<div class='row'>
							<div class='2 col-sm-12'>	
								<div id='formBillDas' class='form-group'>
									<span id='btn_addBillDas' class='glyphicon glyphicon-plus btn btn-xs btn-block btn-info'> บิลจาก DASI(FREE)</span>
									<!-- select class='add_billdas form-control input-sm' use=false data-placeholder='เลขที่บิล'></select -->
								</div>
							</div>
						</div>
					</div>
				</fieldset>
			</div>
		";
		return $html;
	}
	function getFormInopt(){
		$html = "
			<div id='inoptform' class='inoptform' style='height:100%;'>
				<div class='row'>
					<div class='col-sm-12'>
						<div class='form-group'>
							อุปกรณ์เสริม
							<select id='op_code' class='form-control input-sm' data-placeholder='อุปกรณ์เสริม'></select>
						</div>
					<div>
				<div>
				<div class='row'>	
					<div class='col-sm-4'>	
						<div class='form-group'>
							ราคา/หน่วย
							<input type='text' id='op_uprice' class='form-control input-xs' placeholder='ราคา/หน่วย' >
						</div>
					</div>
					
					<div class='col-sm-4'>	
						<div class='form-group'>
							ราคาทุนรวม
							<input type='text' id='op_cvt' class='form-control input-xs' placeholder='ราคา/หน่วย' >
						</div>
					</div>
					<div class='col-sm-4'>	
						<div class='form-group'>
							จำนวน
							<input type='text' id='op_qty' class='form-control input-xs' placeholder='จำนวน' >
						</div>
					</div>
				<div>

				<div class='col-sm-12'>	
					<i id='cal_inopt' class='btn btn-xs btn-info btn-block glyphicon glyphicon-refresh' style='cursor:pointer;'> คำนวน  </i>
				</div>

				<div class='col-sm-12'>	
					<div id='inopt_results'></div>
				</div>			
			
				<div class='col-sm-12'>	
					<i id='getvalue_inopt' class='btn btn-xs btn-primary btn-block glyphicon glyphicon-ok' style='cursor:pointer;'> รับค่า  </i>
				</div>
			</div>
		";
		echo json_encode($html);		
	}
	function calculate_inopt(){
		$response = array();
		
		$inclvat = $_REQUEST['inclvat'];
		$vatrt   = $_REQUEST['vatrt'];
		$opCode  = $_REQUEST['opCode'];
		$opText  = $_REQUEST['opText'];
		$uprice  = $_REQUEST['uprice'];
		$cvt 	 = $_REQUEST['cvt'];
		$qty 	 = $_REQUEST['qty'];
		
		if($opCode == ""){
			$response["status"]	= false;
			$response["msg"]	= "ผิดพลาด ยังไม่ระบุอุปกรณ์เสริมทีครับ"; 
			echo json_encode($response); exit;
		}
		if($uprice == ""){
			$response["status"]	= false;
			$response["msg"]	= "ผิดพลาด ยังไม่ระบุราคาต่อหน่วย"; 
			echo json_encode($response); exit;
		}
		if($cvt == ""){
			$response["status"]	= false;
			$response["msg"]	= "ผิดพลาด ยังไม่ระบุราคาทุน"; 
			echo json_encode($response); exit;
		}
		if($qty == ""){
			$response["status"]	= false;
			$response["msg"]	= "ผิดพลาด ยังไม่ระบุจำนวน"; 
			echo json_encode($response); exit;
		}
		$response["qty"] = $qty; 
		$response["uprice"] = $uprice;
		if($inclvat == "Y"){
			if($vatrt > 0){
				$response["1price"] = number_format(($uprice * $qty) / ((100 + $vatrt)/100),2);
				$response["1vat"] 	= number_format(($uprice * $qty) - (($uprice * $qty) / ((100 + $vatrt)/100)),2);
				$response["1total"] = number_format(($uprice * $qty),2);
				
				$response["2price"] = number_format($cvt / ((100 + $vatrt)/100),2);
				$response["2vat"] 	= number_format($cvt - ($cvt / ((100 + $vatrt)/100)),2);
				$response["2total"] = number_format($cvt,2);
			}else{
				$response["1price"] = number_format(($uprice * $qty),2);
				$response["1vat"] 	= number_format(0,2);
				$response["1total"] = number_format(($uprice * $qty),2);
				
				$response["2price"] = number_format($cvt,2);
				$response["2vat"] 	= number_format(0,2);
				$response["2total"] = number_format($cvt,2);
			}
		}else{
			if($vatrt > 0){
				$response["1price"] = number_format(($uprice * $qty),2);
				$response["1vat"] 	= number_format(($uprice * $qty) * ($vatrt/100),2);
				$response["1total"] = number_format(($uprice * $qty) * ((100 + $vatrt)/100),2);
				
				$response["2price"] = number_format($cvt,2);
				$response["2vat"] 	= number_format($cvt * ($vatrt/100),2);
				$response["2total"] = number_format($cvt * ((100 + $vatrt)/100),2);
			}else{
				$response["1price"] = number_format(($uprice * $qty),2);
				$response["1vat"] 	= number_format(0,2);
				$response["1total"] = number_format(($uprice * $qty),2);
				
				$response["2price"] = number_format($cvt,2);
				$response["2vat"] 	= number_format(0,2);
				$response["2total"] = number_format($cvt,2);
			}
		}
		
		$html = "
			<div class='row'>
				<div class='col-lg-12' align='center'>
					".$opText."
				</div>
				<div class='col-lg-4' align='right'>
					ราคา/หน่วย
				</div>
				<div class='col-lg-6 col-lg-offset-2' align='right'>
					".number_format($uprice,2)."
				</div>
				
				<div class='col-lg-4' align='right'>
					ราคาทุนรวม
				</div>
				<div class='col-lg-6 col-lg-offset-2' align='right'>
					".number_format($cvt,2)."
				</div>
				
				<div class='col-lg-4' align='right'>
					จำนวน
				</div>
				<div class='col-lg-6 col-lg-offset-2' align='right'>
					".number_format($qty,2)."
				</div>
				
				<div class='col-lg-4' align='right'>
					มูลค่าทุน
				</div>
				<div class='col-lg-6 col-lg-offset-2' align='right'>
					".$response["2price"]."
				</div>
				
				<div class='col-lg-4' align='right'>
					ภาษีทุน
				</div>
				<div class='col-lg-6 col-lg-offset-2' align='right'>
					".$response["2vat"]."
				</div>
				
				<div class='col-lg-4' align='right'>
					ทุนรวมภาษี
				</div>
				<div class='col-lg-6 col-lg-offset-2' align='right'>
					".$response["2total"]."
				</div>
				
				<div class='col-lg-4' align='right'>
					มูลค่าสินค้า
				</div>
				<div class='col-lg-6 col-lg-offset-2' align='right'>
					".$response["1price"]."
				</div>
				
				<div class='col-lg-4' align='right'>
					ภาษี
				</div>
				<div class='col-lg-6 col-lg-offset-2' align='right'>
					".$response["1vat"]."
				</div>
				
				<div class='col-lg-4' align='right'>
					ยอดเงินรวมภาษี
				</div>
				<div class='col-lg-6 col-lg-offset-2' align='right'>
					".$response["1total"]."
				</div>
			</div>
		";
		$response["html"] = $html;
		$response["status"]	= true;
		echo json_encode($response);		
	}
}




















