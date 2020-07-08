<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/********************************************************
             ______@25/06/2020______
			 Pasakorn Boonded

********************************************************/
class Accessory extends MY_Controller {
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
				<div class='col-sm-12'>
					<div class=' col-sm-3'>	
						<div class='form-group'>
							เลขที่สัญญา
							<input type='text' id='CONTNO' class='form-control input-sm' placeholder='เลขที่สัญญา' >
						</div>
					</div>
					<div class=' col-sm-3'>	
						<div class='form-group'>
							วันที่ทำสัญญา
							<input type='text' id='SDATEFRM' class='form-control input-sm' placeholder='จาก' data-provide='datepicker' data-date-language='th-th' value='".$this->today('startofmonthB1')."'>
						</div>
					</div>	
					<div class=' col-sm-3'>	
						<div class='form-group'>
							ถึง
							<input type='text' id='SDATETO' class='form-control input-sm' placeholder='ถึง' data-provide='datepicker' data-date-language='th-th' value='".$this->today('endofmonth')."'>
						</div>
					</div>	
					<div class=' col-sm-3'>	
						<div class='form-group'>
							สาขา
							<input type='text' id='LOCAT' class='form-control input-sm' placeholder='สาขา'  value='".$this->sess['branch']."'>
						</div>
					</div>						
					<div class=' col-sm-6'>	
						<div class='form-group'>
							<button id='btnaddform' class='btn btn-cyan btn-block'><span class='glyphicon glyphicon-pencil'> ทำรายการขายอุปกรณ์</span></button>
						</div>
					</div>
					<div class=' col-sm-6'>	
						<div class='form-group'>
							<button id='btnsearchlist' class='btn btn-primary btn-block'><span class='glyphicon glyphicon-search'> ค้นหา</span></button>
						</div>
					</div>
				</div>
				<div class='col-sm-12'>
					<div id='result'></div>
				</div>
			</div>
		";
		$html .="<script src='".base_url('public/js/SYS04/Accessory.js')."'></script>";
		echo $html;
	}
	function Search(){
		$CONTNO   = $_REQUEST['CONTNO'];
		$SDATEFRM = $this->Convertdate(1,$_REQUEST['SDATEFRM']);
		$SDATETO  = $this->Convertdate(1,$_REQUEST['SDATETO']);
		$LOCAT = $_REQUEST['LOCAT'];
		$html = "";
		
		$cond = "";
		if($CONTNO !== ""){
			$cond .="and A.CONTNO like '".$CONTNO."%'";
		}
		if($SDATEFRM !== "" and $SDATETO !== ""){
			$cond .= "and A.SDATE between '".$SDATEFRM."' and '".$SDATETO."'";
		}else if($SDATEFRM !== "" and $SDATETO = ""){
			$cond .="and A.SDATE = '".$SDATEFRM."'";
		}else if($SDATEFRM = "" and $SDATETO !== ""){
			$cond .="and A.SDATE '".$SDATETO."'";
		}
		if($LOCAT !== ""){
			$cond .= "and A.LOCAT = '".$LOCAT."'";
		}
		
		$sql = "	
			select 
				A.CONTNO,A.LOCAT,CONVERT(varchar(8),A.SDATE,112) as SDATE,A.CUSCOD
				,C.SNAM+C.NAME1+' '+C.NAME2 as CUSNAME,A.TAXNO,CONVERT(varchar(8),A.TAXDT,112) as TAXDT
			from {$this->MAuth->getdb('AROPTMST')} A 
			left join {$this->MAuth->getdb('CUSTMAST')} C on A.CUSCOD = C.CUSCOD 
			where 1=1 ".$cond." order by A.CONTNO
		";
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$html .="
					<tr>
						<td style='width:40px;'>
							<i class='ACSDetails btn btn-xs btn-success glyphicon glyphicon-zoom-in' contno='".$row->CONTNO."' style='cursor:pointer;'> รายละเอียด  </i>
						</td>
						<td style='vertical-align:middle;'>".$row->CONTNO."</td>
						<td style='vertical-align:middle;'>".$row->LOCAT."</td>
						<td style='vertical-align:middle;'>".$this->Convertdate(2,$row->SDATE)."</td>
						<td style='vertical-align:middle;'>".$row->CUSCOD."</td>
						<td style='vertical-align:middle;'>".$row->CUSNAME."</td>
						<td style='vertical-align:middle;'>".$row->TAXNO."</td>
						<td style='vertical-align:middle;'>".$this->Convertdate(2,$row->TAXDT)."</td>
					</tr>
				";
			}
		}
		$html = "
			<table id='table-accessory' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='99%' border=1 style='font-size:8pt;'>
				<thead style='background: rgba(0, 0, 0, 0) url(&#39;../public/lobiadmin-master/version/1.0/ajax/img/bg/bg6.png&#39;) repeat scroll 0% 0%;'>
					<tr style='line-height:20px;'>
						<td style='vertical-align:middle;text-align:center;font-size:8pt;' colspan='8'>
							เงื่อนไข :: 
						</td>
					</tr>
					<tr>
						<th style='vertical-align:middle;'>#</th>
						<th style='vertical-align:middle;'>เลขที่สัญญา</th>
						<th style='vertical-align:middle;'>สาขา</th>
						<th style='vertical-align:middle;'>วันที่ขาย</th>
						<th style='vertical-align:middle;'>รหัสลูกค้า</th>
						<th style='vertical-align:middle;'>ชื่อ-สกุล</th>
						<th style='vertical-align:middle;'>เลขที่ใบกำกับ</th>
						<th style='vertical-align:middle;'>วันที่ใบกำกับ</th>
					</tr>
				</thead>	
				<tbody>						
					".$html."
				</tbody>
			</table>
		";
		
		$response = array("html"=>$html,"status"=>true);
		echo json_encode($response);
	}
	function loadACS(){
		$contno = $_REQUEST['contno'];
		$sql = "
			select 
				A.LOCAT,A.CONTNO,CONVERT(varchar(8),A.SDATE,112) as SDATE,A.CUSCOD
				,C.SNAM+C.NAME1+' '+C.NAME2+' ('+A.CUSCOD+')' as CUSNAME
				,A.INCLVAT,A.VATRT,A.CREDTM,convert(varchar(8),A.DUEDT,112) as DUEDT
				,A.SALCOD,O.NAME+'('+A.SALCOD+')' as TAXSAL
				,A.COMITN,A.TAXNO,convert(varchar(8),A.TAXDT,112) as TAXDT,A.MEMO1
				,A.OPTPTOT,A.OPTPRC,A.OPTPVT,A.SMPAY
			from {$this->MAuth->getdb('AROPTMST')} A 
			left join {$this->MAuth->getdb('CUSTMAST')} C on A.CUSCOD = C.CUSCOD
			left join {$this->MAuth->getdb('OFFICER')} O on A.SALCOD = O.CODE
			where CONTNO = '".$contno."'
		";
		$query = $this->db->query($sql);
		$response = array();
		if($query->row()){
			foreach($query->result() as $row){
				$response['LOCAT']   = $row->LOCAT;
				$response['CONTNO']  = $row->CONTNO;
				$response['SDATE']   = $this->Convertdate(2,$row->SDATE);
				$response['CUSCOD']  = $row->CUSCOD;
				$response['CUSNAME'] = $row->CUSNAME;
				$response['INCLVAT'] = $row->INCLVAT;
				$response['VATRT']   = $row->VATRT;
				$response['CREDTM']  = $row->CREDTM;
				$response['DUEDT']   = $this->Convertdate(2,$row->DUEDT);
				$response['SALCOD']  = $row->SALCOD;
				$response['TAXSAL']  = $row->TAXSAL;
				$response['COMITN']  = number_format($row->COMITN,2);
				$response['TAXNO']   = $row->TAXNO;
				$response['TAXDT']   = $this->Convertdate(2,$row->TAXDT);
				$response['OPTPTOT'] = $row->OPTPTOT;
				$response['OPTPRC']  = $row->OPTPRC;
				$response['OPTPVT']  = $row->OPTPVT;
				$response['SMPAY']   = $row->SMPAY;
				$response['MEMO1']   = $row->MEMO1;
			}
		}
		$sql = "
			select OPTCODE,UPRICE,QTY,NPRICE,TOTVAT,TOTPRC,OPTCST,OPTCVT,OPTCTOT 
			from {$this->MAuth->getdb('ARINOPT')} where CONTNO = '".$contno."'
		";
		$query = $this->db->query($sql);
		$accslist = "";
		if($query->row()){
			foreach($query->result() as $row){
				$accslist .="
					<tr>
						<td align='center'>
							<i class='accslist btn btn-xs btn-danger glyphicon glyphicon-minus'
								optcode = '".$row->OPTCODE."' optptot = '".$row->UPRICE."' count_acs = '".$row->QTY."'
								optprc = '".$row->NPRICE."' vatrt ='".$row->TOTVAT."' t_optptot ='".$row->TOTPRC."' 
								optcst = '".$row->OPTCST."' optcvt = '".$row->OPTCVT."'
								optctot= '".$row->OPTCTOT."'
								style='cursor:pointer;'> ลบ   
							</i>
						</td>
						<td class='text-right'>".$row->OPTCODE."</td>
						<td class='text-right'>".$row->UPRICE."</td>
						<td class='text-right'>".$row->QTY."</td>
						<td class='text-right'>".$row->NPRICE."</td>
						<td class='text-right'>".$row->TOTVAT."</td>
						<td class='text-right'>".$row->TOTPRC."</td>
						<td class='text-right'>".$row->OPTCST."</td>
						<td class='text-right'>".$row->OPTCVT."</td>
						<td class='text-right'>".$row->OPTCTOT."</td>
					</tr>
				";
			}
		}
		$response['accslist'] = $accslist;
		echo json_encode($response);
	}
	function getformAccessory(){
		$data = array();
		
		$sql = "
			select * from {$this->MAuth->getdb('VATMAST')}
			where getdate() between FRMDATE and TODATE
		";
		$query = $this->db->query($sql);
		$row = $query->row();
		$data['vatrt'] = number_format($row->VATRT,2);
		
		$html = "
			<div id='wizard-leasing' class='wizard-wrapper'>    
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
							".$this->getformAccessoryTab11($data)."
							".$this->getformAccessoryTab22($data)."
							".$this->getformAccessoryTab33($data)."							
							
							<!--ul class='pager'>
								<li class='previous first disabled' style='display:none;'><a href='javascript:void(0)'>First</a></li>
								<li class='previous disabled'><a href='javascript:void(0)'>ย้อนกลับ</a></li>
								<li class='next last' style='display:none;'><a href='javascript:void(0)'>Last</a></li>
								<li class='next'><a href='javascript:void(0)'>ถัดไป</a></li>
							</ul-->
						</div>
					</form>
				</div>
			</div>
			<div>
				<div class='col-sm-6 text-left'>
					<br>
					<div class='btn-group btn-group-xs dropup'>
						<button type='button' id='btnDocument' class='btn btn-xs btn-info'>
							เอกสาร
						</button>
						<button type='button' id='btnDocumentOption' class='btn btn-xs btn-info dropdown-toggle' data-toggle='dropdown' aria-expanded='false'>
							<i class='fa fa-cog'></i>
							<span class='sr-only'>Toggle Dropdown</span>
						</button>
						<ul class='dropdown-menu'>
							<span id='btnDOSend' style='text-align:left;' class='btn btn-info btn-xs btn-block text-left'>1.ใบกำกับภาษีอย่างย่อ</span>
						</ul>
					</div>
				</div>
				<div class='col-sm-6 text-right'>
					<br>
					<button id='btn_delete' class='btn btn-xs btn-danger' style='width:100px;'><span class='glyphicon glyphicon-trash'> ลบ</span></button>
					<button id='btn_save' class='btn btn-xs btn-primary' style='width:100px;'><span class='glyphicon glyphicon-floppy-disk'> บันทึก</span></button>
				</div>
			</div>
		";
		$response = array('html'=>$html,'status'=>true);
		echo json_encode($response);
	}
	function getformAccessoryTab11($data){
		$html = "
			<div class='tab-pane active' name='tab11' style='height:calc(100vh - 260px);overflow:auto;'>
				<fieldset style='height:100%'>
					<div class='row' style='height:100%'>
						<div style='float:left;height:100%;overflow:auto;' class='col-sm-8 col-sm-offset-2'>
							<div class='row'>
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
										เลขที่สัญญา
										<input type='text' id='add_contno' class='form-control input-sm' placeholder='เลขที่สัญญา'  value=''>
									</div>
								</div>
								<div class='col-sm-4'>	
									<div class='form-group'>
										วันที่ทำสัญญา
										<input type='text' id='add_sdate' class='form-control input-sm' placeholder='วันที่ทำสัญญา' data-provide='datepicker' data-date-language='th-th' value='".$this->today('today')."'>
									</div>
								</div>
								<div class='col-sm-8'>	
									<div class='form-group'>
										รหัสลูกค้า
										<div class='input-group'>
											<input type='text' id='add_cuscod' CUSCOD='' class='form-control input-sm' placeholder='ลูกค้า' >
											<span class='input-group-btn'>
												<button id='btnaddcuscod' class='btn btn-info btn-sm' type='button'>
													<span class='glyphicon glyphicon-hand-up' aria-hidden='true'></span>
												</button>
												<button id='add_cuscod_removed' class='btn btn-danger btn-sm' type='button'>
													<span class='glyphicon glyphicon-remove' aria-hidden='true'></span>
												</button>
											</span>
										</div>
									</div>
								</div>
								<div class='col-sm-2'>	
									<div class='form-group'>
										<br>
										<button id='btn_DetailHistrory' class='btn btn-cyan btn-block'><span class='fa fa-file-text'>รายละเอียด</span></button>
									</div>
								</div>
								<div class='col-sm-2'>	
									<div class='form-group'>
										<br>
										<button id='btn_linkaddcus' class='btn btn-primary btn-block'>
											<span class=''>link</span>
										</button>
									</div>
								</div>
								<div class='col-sm-4'>	
									<div class='form-group'>
										ต้องการป้อนจำนวนเงินแบบ
										<select id='add_inclvat' class='form-control input-sm' data-placeholder='ต้องการป้อนจำนวนเงินแบบ'>
											<option value='Y' selected>รวม VAT</option>
											<option value='N'>แยก VAT</option>
										</select>
									</div>
								</div>
								<div class='col-sm-4 col-sm-offset-4'>	
									<div class='form-group'>
										อัตราภาษี
										<div class='input-group'>
											<input  style='text-align:right;' type='text' id='add_vatrt' class='form-control input-sm' value='".$data['vatrt']."'>
											<span class='input-group-addon'><b>%</b></span>	
										</div>
									</div>
								</div>
								<div class='col-sm-3'>	
									<div class='form-group'>
										เครดิต
										<div class='input-group'>
											<input type='text' id='add_credtm' class='form-control input-sm' value='0'>
											<span class='input-group-addon'><b>วัน</b></span>	
										</div>
									</div>
								</div>
								<div class='col-sm-3'>	
									<div class='form-group'>
										วันครบกำหนดชำระ
										<input type='text' id='add_duedt' class='form-control input-sm' placeholder='วันที่ทำสัญญา' data-provide='datepicker' data-date-language='th-th' value='".$this->today('today')."'>
									</div>
								</div>
								<div class='col-sm-3'>	
									<div class='form-group'>
										รหัสพนักงานขาย
										<select id='add_salecod' class='form-control input-sm' data-placeholder='รหัสพนักงานขาย'></select>
									</div>
								</div>
								<div class='col-sm-3'>	
									<div class='form-group'>
										ค่านายหน้า
										<input type='text' id='add_comitn' class='form-control input-sm' value='0'>
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
	function getformAccessoryTab22($data){
		$html = "
			<div class='tab-pane' name='tab22' style='height:calc(100vh - 260px);overflow:auto;'>
				<fieldset style='height:100%'>
					<div class='row' style='height:100%'>
						<div style='float:left;height:100%;overflow:auto;' class='col-sm-12'>
							<div class='row' style='width:100%;height:100%;padding-left:30px;background-color:#269da1;'>
								<div class='form-group col-sm-12' style='height:100%;'>
									<span style='color:#efff14;'>บันทึกอุปกรณ์เสริมและราคาขาย</span>
									<div id='dataTable-fixed-acce' class='dataTables_wrapper dt-bootstrap4 table-responsive' style='max-height:calc(100% - 130px);height:calc(100% - 130px);overflow:auto;border:1px dotted black;background-color:#eee;'>
										<table id='dataTables-acce' class='table table-bordered dataTable table-hover' stat='' aria-describedby='dataTable_info' style='width:100%;line-height:10px;white-space:nowrap;text-overflow:ellipsis;overflow:hidden;' cellspacing='0'>
											<thead>
												<tr role='row'>
													<th style='width:40px'>
														<i id='add_optcod' class='btn btn-xs btn-success glyphicon glyphicon-plus' style='cursor:pointer;'> เพิ่ม  </i>
													</th>
													<th>รหัสอุปกรณ์</th>
													<th>ราคาขาย/หน่วย</th>
													<th>จำนวน</th>
													<th>มูลค่าสินค้า</th>
													<th>ภาษี</th>
													<th>ราคาขาย</th>
													<th>มูลค่าทุน</th>
													<th>ภาษีทุน</th>
													<th>ทุนรวมภาษี</th>
												</tr>
											</thead>
											<tbody style='white-space: nowrap;'></tbody>
										</table>
									</div>
									<div class='col-sm-2'>	
										<div class='form-group'>
											เลขที่ใบกำกับ
											<input type='text' id='get_taxno' class='form-control input-sm' placeholder='เลขที่ใบกำกับ'  disabled>
										</div>
									</div>
									<div class='col-sm-2'>	
										<div class='form-group'>
											วันที่ใบกำกับ
											<input type='text' id='get_taxdt' class='form-control input-sm' placeholder='วันที่ใบกำกับ'  disabled>
										</div>
									</div>
									<div class='col-sm-2'>	
										<div class='form-group'>
											รวมราคาขาย
											<input type='text' id='sum_optptot' style='text-align: right;' class='form-control input-sm' placeholder='0.00'  disabled>
										</div>
									</div>
									<div class='col-sm-2'>	
										<div class='form-group'>
											มูลค่าสินค้า
											<input type='text' id='sum_optprc' style='text-align: right;' class='form-control input-sm' placeholder='0.00'  disabled>
										</div>
									</div>
									<div class='col-sm-2'>	
										<div class='form-group'>
											ภาษีมูลค่าเพิ่ม
											<input type='text' id='sum_vatrt' style='text-align: right;' class='form-control input-sm' placeholder='0.00'  disabled>
										</div>
									</div>
									<div class='col-sm-2'>	
										<div class='form-group'>
											ชำระแล้ว
											<input type='text' id='add_smpay' style='text-align: right;' class='form-control input-sm' placeholder='0.00'  disabled>
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
	function getformAccessoryTab33($data){
		$html = "
			<div class='tab-pane' name='tab33' style='height:calc(100vh - 260px);overflow:auto;'>
				<fieldset style='height:100%'>
					<div class='row' style='height:100%'>
						<div style='float:left;height:100%;overflow:auto;' class='col-sm-8 col-sm-offset-2'>
							<div class='2 col-sm-12'>	
								<div class='form-group'>
									<textarea type='text' id='add_memo1' class='form-control input-sm' placeholder='บันทึกเพิ่มเติม' rows=20 style='resize:vertical;' ></textarea>
								</div>
							</div>
						</div>
					</div>
				</fieldset>
			</div>
		";
		return $html;
	}
	function getFormOPTCODE(){
		$inclvat = $_REQUEST['inclvat'];
		$vatrt   = $_REQUEST['vatrt'];
		$html = "
			<div class='row'>
				<div class='col-sm-12'>
					รหัสอุปกรณ์
					<select id='fm_optcode' class='form-control input-sm'>
					</select>
				</div>
				<div class='col-sm-5'>
					ราคาขาย/หน่วย
					<input type='text' class='form-control input-sm' id='fm_optptot' style='text-align:right;' value=''>
				</div>
				<div class='col-sm-2'>
					จำนวน
					<input type='text' class='form-control input-sm' id='fm_count' style='text-align:right;' value='1'>
				</div>
				<div class='col-sm-5'>
					มูลค่าสินค้า
					<input type='text' class='form-control input-sm' id='fm_optprc' style='text-align:right;' value=''>
				</div>
				<div class='col-sm-6'>
					ภาษี
					<input type='text' class='form-control input-sm' id='fm_vatrt' style='text-align:right;' value=''>
				</div>
				<div class='col-sm-6'>
					ราคาขายรวม
					<input type='text' class='form-control input-sm' id='fm_t_optptot' style='text-align:right;' value=''>
				</div>
				<div style='height:200px;'></div>
				<div class='col-sm-6'>
					มูลค่าทุน
					<input type='text' class='form-control input-sm' id='fm_optcst' style='text-align:right;' value=''>
				</div>
				<div class='col-sm-6'>
					ภาษีทุน
					<input type='text' class='form-control input-sm' id='fm_optcvt' style='text-align:right;' value=''>
				</div>
				<div class='col-sm-6'>
					ทุนรวมภาษี
					<input type='text' class='form-control input-sm' id='fm_optctot' style='text-align:right;' value=''>
				</div>
				<div class='col-sm-12'>
					<br>
					<button id='btn_addlistopt' class='btn btn-primary btn-block'><span class=''> เพิ่ม</span></button>
				</div>
			</div>
		";
		$response = array("html"=>$html);
		echo json_encode($response);
	}
	function fnCalPrice(){
		$optcode   = $_REQUEST['optcode'];
		$inclvat   = $_REQUEST['inclvat'];
		$vatrt     = $_REQUEST['vatrt'];
		$optptot   = str_replace(",","",$_REQUEST['optptot']);
		$count_acs = $_REQUEST['count_acs'];
		$cpt_value = ($_REQUEST['capitalvalue'] == "" ? 0:str_replace(",","",$_REQUEST['capitalvalue']));
		//$cpt_value = str_replace(",","",$_REQUEST['capitalvalue']);
		if($optcode == ""){
			$response["error"] = true;
			$response["msg"] = "กรุณาเลือกรหัสอุปกรณ์เสริมก่อนนะครับ";
			echo json_encode($response); exit;
		}
		if($optptot == ""){
			$response["error"] = true;
			$response["msg"] = "กรุณาระบุราคาขายต่อหน่อยก่อนนะครับ";
			echo json_encode($response); exit;
		}
		if($count_acs == ""){
			$response["error"] = true;
			$response["msg"] = "กรุณาระบุจำนวนก่อนนะครับ";
			echo json_encode($response); exit;
		}
		if($cpt_value == ""){
			$response["error"] = true;
			$response["msg"] = "กรุณาระบุมูลค่าทุนก่อนนะครับ";
			echo json_encode($response); exit;
		}
		$data = array();
		if($inclvat == 'Y'){
			$data['optptot'] = $optptot; //ราคาขาย/หน่วย
			$data['optcst']  = $cpt_value; //มูลค่าทุน
			
			$data['t_optptot'] = $optptot * $count_acs; //รวมราคาขาย
			
			$data['vatrt']  = $data['t_optptot'] - ($data['t_optptot'] / ((100 + $vatrt) / 100)); //ภาษี
			$data['optprc'] = (($optptot * $count_acs) - $data['vatrt']); //มูลค่าสินค้า
			
			$data['optcvt'] = ($cpt_value * $vatrt) / 100; //ภาษีทุน
			$data['optctot']= $cpt_value + $data['optcvt']; //ทุนรวมภาษี
		}else{
			$data['optptot']= $optptot;
			$data['optcst'] = $cpt_value;
			
			$data['vatrt']  = (($optptot * $count_acs) * $vatrt) / 100;
			$data['optprc'] = ($optptot * $count_acs);
			$data['t_optptot'] = ($optptot * $count_acs) + $data['vatrt']; 
			
			$data['optcvt'] = ($cpt_value * $vatrt) / 100; //ภาษีทุน
			$data['optctot']= $cpt_value + $data['optcvt']; //ทุนรวมภาษี
		}
		foreach($data as $key => $val){
			$data[$key] = number_format($val,2);
		}
		echo json_encode($data);
	}
	function Save(){
		$arrs = array();
		$arrs['contno']     = $_REQUEST['contno'];
		$arrs['locat']  	= $_REQUEST['locat'];
		$arrs['sdate']  	= $this->Convertdate(1,$_REQUEST['sdate']);
		$arrs['cuscod']  	= $_REQUEST['cuscod'];
		$arrs['inclvat']  	= $_REQUEST['inclvat'];
		$arrs['vatrt']  	= $_REQUEST['vatrt'];
		$arrs['credtm']  	= ($_REQUEST['credtm'] == "" ? 0:str_replace(',','',$_REQUEST['credtm']));
		$arrs['duedt']      = $this->Convertdate(1,$_REQUEST['duedt']);
		$arrs['salecod']    = $_REQUEST['salecod'];
		$arrs['comitn']     = ($_REQUEST['comitn'] == "" ? 0:str_replace(',','',$_REQUEST['comitn']));
		$arrs['listacs']    = $_REQUEST['listacs']; //รายการอุปกรณ์เสริม
		
		$arrs['taxno']      = $_REQUEST['taxno'];
		
		$arrs['tt_optptot'] = str_replace(',','',$_REQUEST['tt_optptot']);
		$arrs['tt_optprc']  = str_replace(',','',$_REQUEST['tt_optprc']);
		$arrs['tt_vatrt']   = str_replace(',','',$_REQUEST['tt_vatrt']);
		$arrs['memo1']      = $_REQUEST['memo1'];
		
		$msg = "";
		if($arrs['cuscod']  == ""){$msg = "รหัสลูกค้า";}
		if($arrs['salecod'] == ""){$msg = "รหัสพนักงานขาย";}
		if($arrs['salecod'] == ""){$msg = "รหัสพนักงานขาย";}
		if($arrs['listacs'] == "no listacs"){$msg = "รายการอุปกรณ์เสริม";}
		
		$response = array("error"=>false,"msg"=>"");
		if($msg !== ""){
			$response["error"] = true;
			$response["msg"]   = "ไม่พบ{$msg} โปรดเลือกหรือระบุ{$msg}ก่อนครับ";
			echo json_encode($response); exit;
		}
		$sql = "
			if object_id('tempdb..#tempOPTCODE') is not null drop table #tempOPTCODE;
			select TSALE,CONTNO,LOCAT,OPTCODE,UPRICE,UCOST,QTY,TOTPRC,TOTVAT,NPRICE
				,OPTCST,OPTCVT,OPTCTOT,CONFIR,USERID,INPDT,POSTDT,SDATE,RTNFLAG
			into #tempOPTCODE
			from {$this->MAuth->getdb('ARINOPT')} 
			where 1=2
		";
		//echo $sql;
		$this->db->query($sql);
		
		$arrs['insertOPTCODE'] = ""; $arrs['trigger_insert'] = ""; $arrs['updateACS'] = "";
		$arrs['optcode'] = "";
		$codesize = sizeof($arrs['listacs']);
		for($i=0;$i<$codesize;$i++){
			$arrs['insertOPTCODE'] = "
				insert into #tempOPTCODE
				select 'O','createnos','".$arrs['locat']."','".$arrs['listacs'][$i][0]."'
				,".$arrs['listacs'][$i][1].",0,".$arrs['listacs'][$i][2].",".$arrs['listacs'][$i][5]."
				,".$arrs['listacs'][$i][4].",".$arrs['listacs'][$i][3].",".$arrs['listacs'][$i][6]."
				,".$arrs['listacs'][$i][7].",".$arrs['listacs'][$i][8].",null,'".$this->sess["USERID"]."'
				,getdate(),null,'".$arrs['sdate']."',null
			";
			//echo $arrs['insertOPTCODE'];
			$this->db->query($arrs['insertOPTCODE']);
			
			//trigger insert ARINOPT -->update OPTMAST 
			$arrs['trigger_insert'] .="
				update {$this->MAuth->getdb('OPTMAST')} set ONHAND = ONHAND - ".$arrs['listacs'][$i][2]." 
				where LOCAT = '".$arrs['locat']."' and OPTCODE = '".$arrs['listacs'][$i][0]."'
			";
			$arrs['updateACS'] .="
				if exists(
					select * from {$this->MAuth->getdb('ARINOPT')}
					where CONTNO = '".$arrs['contno']."' and LOCAT = '".$arrs['locat']."'
					and OPTCODE = '".$arrs['listacs'][$i][0]."'
				)
				begin
					update {$this->MAuth->getdb('ARINOPT')} 
					set UPRICE = ".$arrs['listacs'][$i][1].",QTY = ".$arrs['listacs'][$i][2]."
					,TOTPRC = ".$arrs['listacs'][$i][5].",TOTVAT = ".$arrs['listacs'][$i][4]."
					,NPRICE = ".$arrs['listacs'][$i][3]."
					,OPTCST = ".$arrs['listacs'][$i][6].",OPTCVT = ".$arrs['listacs'][$i][7]."
					,OPTCTOT = ".$arrs['listacs'][$i][8]." 
					where CONTNO = '".$arrs['contno']."' and LOCAT = '".$arrs['locat']."' 
					and OPTCODE = '".$arrs['listacs'][$i][0]."' 
				end
				else if not exists(
					select * from {$this->MAuth->getdb('ARINOPT')}
					where CONTNO = '".$arrs['contno']."' and LOCAT = '".$arrs['locat']."'
					and OPTCODE = '".$arrs['listacs'][$i][0]."'
				)
				begin
					ALTER TABLE {$this->MAuth->getdb('ARINOPT')} DISABLE TRIGGER AFTINS_ARINOPT
					
					insert into {$this->MAuth->getdb('ARINOPT')} (
						TSALE,CONTNO,LOCAT,OPTCODE,UPRICE,UCOST,QTY
						,TOTPRC,TOTVAT,NPRICE,OPTCST,OPTCVT,OPTCTOT
						,CONFIR,USERID,INPDT,POSTDT,SDATE,RTNFLAG
					)values(
						'O','".$arrs['contno']."','".$arrs['locat']."','".$arrs['listacs'][$i][0]."'
						,".$arrs['listacs'][$i][1].",0,".$arrs['listacs'][$i][2].",".$arrs['listacs'][$i][5]."
						,".$arrs['listacs'][$i][4].",".$arrs['listacs'][$i][3].",".$arrs['listacs'][$i][6]."
						,".$arrs['listacs'][$i][7].",".$arrs['listacs'][$i][8].",null,'".$this->sess["USERID"]."'
						,getdate(),null,'".$arrs['sdate']."',null
					)
					update {$this->MAuth->getdb('OPTMAST')} set ONHAND = ONHAND - ".$arrs['listacs'][$i][2]." 
					where LOCAT = '".$arrs['locat']."' and OPTCODE = '".$arrs['listacs'][$i][0]."'
					
					ALTER TABLE {$this->MAuth->getdb('ARINOPT')} ENABLE TRIGGER AFTINS_ARINOPT
				end
			";
			
			if($arrs['optcode'] !== ""){
				$arrs['optcode'] .= "','";
			}
			$arrs['optcode'] .= $arrs['listacs'][$i][0]; //ลบรายการอุปกรณ์เสริม
			
			$arrs['optcst'][]  = $arrs['listacs'][$i][6];
			$arrs['optcvt'][]  = $arrs['listacs'][$i][7];
			$arrs['optctot'][] = $arrs['listacs'][$i][8];
			//echo $arrs['insertOPTCODE'];
		}
		//echo $arrs['optcode']; exit;
		
		$arrs['tt_optcst']  = array_sum($arrs['optcst']);
		$arrs['tt_optcvt']  = array_sum($arrs['optcvt']);
		$arrs['tt_optctot'] = array_sum($arrs['optctot']);
		
		$sql = "
			select OPTCODE,QTY from {$this->MAuth->getdb('ARINOPT')} 
			where CONTNO = '".$arrs['contno']."' and LOCAT = '".$arrs['locat']."' 
			and OPTCODE not in('".$arrs['optcode']."')
		";
		$query = $this->db->query($sql);
		$arrs['trigger_update'] = ""; //ติ๊กเกอร์ UPDATE ลบรายการอุปกรณ์
		if($query->row()){
			foreach($query->result() as $row){
				$arrs['trigger_update'] .= "
					update {$this->MAuth->getdb('OPTMAST')} set ONHAND = ONHAND + ".$row->QTY." 
					where LOCAT = '".$arrs['locat']."' and OPTCODE = '".$row->OPTCODE."'
				";
			}
		}
		//echo $arrs['trigger_update']; exit;
		//print_r($arrs['trigger_update']); exit;
		
		if($arrs['contno'] == "Auto Genarate"){
			$this->SaveAcs($arrs);
		}else{
			$this->UpdateAcs($arrs);
		}
	}
	function SaveAcs($arrs){
		$sql = "
			if OBJECT_ID('tempdb..#tempSaveacs') is not null drop table #tempSaveacs;
			create table #tempSaveacs (id varchar(1),msg varchar(max));
			begin tran SaveAsc
			begin try
				ALTER TABLE {$this->MAuth->getdb('ARINOPT')} DISABLE TRIGGER AFTINS_ARINOPT 
				
				declare @year varchar(4) = (select left(CONVERT(varchar(8),'".$arrs['sdate']."',112),4))
				declare @month varchar(2) = (select right(left(CONVERT(varchar(8),'".$arrs['sdate']."',112),6),2))
				declare @lastno int = (
					select COUNT(*) from {$this->MAuth->getdb('LASTNO')} 
					where LOCAT = '".$arrs['locat']."' and CR_YEAR = @year and CR_MONTH = @month
				)
				--เลขที่สัญญา
				declare @h_optcno varchar(5) = ( 
					select H_OPTCNO from {$this->MAuth->getdb('CONDPAY')}	
				);	
				declare @createcont varchar(8) = (
					select SHORTL+@h_optcno+'-'+right(left(convert(varchar(8),'".$arrs['sdate']."',112),6),4) 
					from {$this->MAuth->getdb('INVLOCAT')} where LOCATCD = '".$arrs['locat']."'
				);
				declare @CONTNO varchar(12) = (
					select @createcont+ISNULL(
						right('0000' + cast(max(cast(coalesce(L_OPTCNO,0) as int) + 1) as varchar(4)), 4)  ,'0001'
					)
					from {$this->MAuth->getdb('LASTNO')} where LOCAT = '".$arrs['locat']."' 
					and CR_YEAR = @year and CR_MONTH = @month
				);
				--เลขที่ใบกำกับ
				declare @h_txopt varchar(5) = ( 
					select H_TXOPT from {$this->MAuth->getdb('CONDPAY')}	
				);
				declare @createtax varchar(8) = (
					select SHORTL+@h_txopt+'-'+right(left(convert(varchar(8),'".$arrs['sdate']."',112),6),4) 
					from {$this->MAuth->getdb('INVLOCAT')} where LOCATCD = '".$arrs['locat']."'
				);
				declare @TAXNO varchar(12) = (
					select @createtax+ISNULL(
						right('0000' + cast(max(cast(coalesce(L_TXOPT,0) as int) + 1) as varchar(4)), 4)  ,'0001')
					from {$this->MAuth->getdb('LASTNO')} where LOCAT = '".$arrs['locat']."' 
					and CR_YEAR = @year and CR_MONTH = @month
				);
				declare @TAXDT datetime = (select convert(varchar(8),getdate(),112));
				--select @contno,@taxno
				
				if exists(select * from #tempOPTCODE)
				begin
					update #tempOPTCODE set CONTNO = @CONTNO
				end
				else
				begin
					rollback tran SaveAsc;
					insert into #tempSaveacs select 'N' as id,'บันทึกข้อมูลไม่สำเร็จ ไม่พบรหัสอุปกรณ์เสริม : กรุณาตรวจสอบข้อมูลอีกครั้ง' as msg;
					return;
				end
				
				if(@lastno = 1)
					update {$this->MAuth->getdb('LASTNO')} set L_OPTCNO = L_OPTCNO+1,L_TXOPT = L_TXOPT+1 
					where LOCAT = '".$arrs['locat']."' and CR_YEAR = @year and CR_MONTH = @month
				else
					insert into {$this->MAuth->getdb('LASTNO')} (
						LOCAT,CR_YEAR,CR_MONTH,L_OPTCNO,L_TXOPT
					)values(
						'".$arrs['locat']."',@year,@month,1,1
					)
					
				insert into {$this->MAuth->getdb('AROPTMST')} (
					CONTNO,LOCAT,CUSCOD,INCLVAT,VATRT,SDATE,SMPAY,SMCHQ,KANG,COST,TAXNO,TAXDT
					,OPTCST,OPTCVT,OPTCTOT
					,OPTPRC,OPTPVT,OPTPTOT
					,CREDTM,DUEDT,SALCOD,COMITN,LPAYDT,TSALE,MEMO1
					,USERID,INPDT,DELID,DELDT,POSTDT,CRDTXNO,CRDAMT 
				)values(@CONTNO,'".$arrs['locat']."','".$arrs['cuscod']."','".$arrs['inclvat']."'
					,".$arrs['vatrt'].",'".$arrs['sdate']."',0,0,0,0,@TAXNO,@TAXDT
					
					,".$arrs['tt_optcst'].",".$arrs['tt_optcvt'].",".$arrs['tt_optctot']."
					,".$arrs['tt_optprc'].",".$arrs['tt_vatrt'].",".$arrs['tt_optptot']."
					
					,".$arrs['credtm'].",'".$arrs['duedt']."','".$arrs['salecod']."',".$arrs['comitn']."
					,null,'O','".$arrs['memo1']."','".$this->sess["USERID"]."',getdate(),'',null,null,null,null
				)
				insert into {$this->MAuth->getdb('ARINOPT')} (
					TSALE,CONTNO,LOCAT,OPTCODE,UPRICE,UCOST,QTY
					,TOTPRC,TOTVAT,NPRICE,OPTCST,OPTCVT,OPTCTOT
					,CONFIR,USERID,INPDT,POSTDT,SDATE,RTNFLAG
				) select * from #tempOPTCODE
				
				insert into {$this->MAuth->getdb('TAXTRAN')} (
					LOCAT,TAXNO,TAXDT,TSALE,CONTNO,CUSCOD,SNAM,NAME1,NAME2,STRNO,REFNO,REFDT,VATRT
					,NETAMT,VATAMT,TOTAMT,DESCP,FPAR,FPAY,LPAR,LPAY,INPDT,FLAG,CANDT,TAXTYP
					,TAXFLG,USERID,FLCANCL,TMBILL,RTNSTK,FINCOD,DOSTAX,PAYFOR,RESONCD,INPTIME
				)select 
					'".$arrs['locat']."',@TAXNO,@TAXDT,'O',@CONTNO,'".$arrs['cuscod']."'
					,SNAM,NAME1,NAME2,null,null,null,".$arrs['vatrt'].",".$arrs['tt_optprc']."
					,".$arrs['tt_vatrt'].",".$arrs['tt_optptot'].",'ใบกำกับขายอุปกรณ์เสริม',null,0,null,0,getdate()
					,null,null,'S','N','".$this->sess["USERID"]."',null,null,null,null,null,null,null
					,null
				from {$this->MAuth->getdb('CUSTMAST')} where CUSCOD = '".$arrs['cuscod']."'
				
				insert into {$this->MAuth->getdb('hp_UserOperationLog')} (
					userId,descriptions,postReq,dateTimeTried,ipAddress,functionName
				)values (
					'".$this->sess["IDNo"]."','SYS04::บันทึกอุปกรณ์เสริม'
					,@CONTNO+' ".str_replace("'","",var_export($_REQUEST, true))."'
					,getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."'
				);
				
				--trigger insert ARINOPT -->update OPTMAST
				".$arrs['trigger_insert']."
				
				ALTER TABLE {$this->MAuth->getdb('ARINOPT')} ENABLE TRIGGER AFTINS_ARINOPT 
				
				insert into #tempSaveacs select 'Y' as id,'สำเร็จ บันทึกข้อมูลรายการขายอุปกรณ์เสริมเลขที่ :: '+@CONTNO+' เรียบร้อยแล้ว' as msg;
				commit tran SaveAsc;
			end try
			begin catch
				rollback tran SaveAsc;
				insert into #tempSaveacs select 'N' as id,'บันทึกข้อมูลไม่สำเร็จ : กรุณาติดต่อฝ่ายไอที' as msg;
				return;
			end catch
		";
		//echo $sql; exit;
		$this->db->query($sql);
		$sql = "
			select * from #tempSaveacs
		";
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$response["error"] = ($row->id == "Y" ? false:true);
				$response["msg"]   = $row->msg;
			}
		}else{
			$response["error"] = true;
			$response["msg"]   = "ผิดพลาด กรุณาติดต่อฝ่ายไอที";
		}
		echo json_encode($response);
	}
	function UpdateAcs($arrs){
		$sql = "
			if OBJECT_ID('tempdb..#tempUpdateacs') is not null drop table #tempUpdateacs;
			create table #tempUpdateacs (id varchar(1),msg varchar(max));
			begin tran UpdateAsc
			begin try
				declare @arin int = isnull((
						select case when COUNT(*) = 0 then 0 else 1 end from {$this->MAuth->getdb('ARINOPT')} 
						where CONTNO = '".$arrs['contno']."' and LOCAT = '".$arrs['locat']."'
					)
				,0)
				declare @arop int = isnull((
						select COUNT(*) from {$this->MAuth->getdb('AROPTMST')} 
						where CONTNO = '".$arrs['contno']."' and CUSCOD = '".$arrs['cuscod']."' 
						and LOCAT = '".$arrs['locat']."' and TAXNO = '".$arrs['taxno']."'
					)
				,0)
				declare @taxt int = isnull((
						select COUNT(*) from {$this->MAuth->getdb('TAXTRAN')} 
						where CONTNO = '".$arrs['contno']."' and CUSCOD = '".$arrs['cuscod']."' 
						and LOCAT = '".$arrs['locat']."' and TAXNO = '".$arrs['taxno']."'
					)
				,0)
				if(@arin = 1 and @arop = 1 and @taxt = 1)
					begin
						ALTER TABLE {$this->MAuth->getdb('ARINOPT')} DISABLE TRIGGER AFTDEL_ARINOPT
						
						update {$this->MAuth->getdb('AROPTMST')} set LOCAT = '".$arrs['locat']."'
						,OPTCST = ".$arrs['tt_optcst'].",OPTCVT = ".$arrs['tt_optcvt'].",OPTCTOT = ".$arrs['tt_optctot']."
						,OPTPRC = ".$arrs['tt_optprc'].",OPTPVT = ".$arrs['tt_vatrt'].",OPTPTOT = ".$arrs['tt_optptot']."
						,CREDTM = ".$arrs['credtm'].",COMITN = ".$arrs['comitn'].",SALCOD = '".$arrs['salecod']."'
						,MEMO1 = '".$arrs['memo1']."' where CONTNO = '".$arrs['contno']."' 
						and TAXNO = '".$arrs['taxno']."' and LOCAT = '".$arrs['locat']."'
						
						update {$this->MAuth->getdb('TAXTRAN')} set LOCAT = '".$arrs['locat']."'
						,NETAMT = ".$arrs['tt_optprc'].",VATAMT = ".$arrs['tt_vatrt'].",TOTAMT = ".$arrs['tt_optptot']."
						where CONTNO = '".$arrs['contno']."' and CUSCOD = '".$arrs['cuscod']."' 
						and TAXNO = '".$arrs['taxno']."'
						
						".$arrs['updateACS']."
						
						delete from {$this->MAuth->getdb('ARINOPT')} 
						where CONTNO = '".$arrs['contno']."' and LOCAT = '".$arrs['locat']."'
						and OPTCODE not in('".$arrs['optcode']."')
						
						/*trigger update delete list*/
						".$arrs['trigger_update']."
						
						insert into {$this->MAuth->getdb('hp_UserOperationLog')} (
							userId,descriptions,postReq,dateTimeTried,ipAddress,functionName
						)values (
							'".$this->sess["IDNo"]."','SYS04::แก้ไขอุปกรณ์เสริม'
							,'".$arrs['contno']."'+' ".str_replace("'","",var_export($_REQUEST, true))."'
							,getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."'
						);
						
						ALTER TABLE {$this->MAuth->getdb('ARINOPT')} ENABLE TRIGGER AFTDEL_ARINOPT
					end
				else
					begin
						rollback tran UpdateAsc;
						insert into #tempUpdateacs select 'N' as id,'บันทึกข้อมูลไม่สำเร็จ : กรุณาตรวจสอบข้อมูลอีกครั้ง' as msg;
						return;
					end
				insert into #tempUpdateacs select 'Y' as id,'สำเร็จ บันทึกข้อมูลรายการขายอุปกรณ์เสริมเลขที่ :: ".$arrs['contno']." เรียบร้อยแล้ว' as msg;
				commit tran UpdateAsc;
			end try
			begin catch
				rollback tran UpdateAsc;
				insert into #tempUpdateacs select 'N' as id,'บันทึกข้อมูลไม่สำเร็จ : กรุณาติดต่อฝ่ายไอที' as msg;
				return;
			end catch
		";
		//echo $sql; exit;
		$this->db->query($sql);
		$sql = "
			select * from #tempUpdateacs
		";
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$response["error"] = ($row->id == "Y" ? false:true);
				$response["msg"]   = $row->msg;
			}
		}else{
			$response["error"] = true;
			$response["msg"] = "ผิดพลาด  กรุณาติดต่อฝ่ายไอที";
		}
		echo json_encode($response);
	}
	function DeleteAcs(){
		$arrs = array();
		$arrs['contno'] = $_REQUEST['contno'];
		$arrs['locat']  = $_REQUEST['locat'];
		$arrs['sdate']  = $this->Convertdate(1,$_REQUEST['sdate']);
		$arrs['cuscod'] = $_REQUEST['cuscod'];
		$arrs['taxno']  = $_REQUEST['taxno'];
		
		$arrs['listacs']= $_REQUEST['listacs'];
		$codesize = sizeof($arrs['listacs']);
		$arrs['trigger_delete'] = "";
		for($i=0;$i<$codesize;$i++){
			$arrs['trigger_delete'] .="
				update {$this->MAuth->getdb('OPTMAST')} set ONHAND = ONHAND + ".$arrs['listacs'][$i][2]." 
				where LOCAT = '".$arrs['locat']."' and OPTCODE = '".$arrs['listacs'][$i][0]."'
			";	
		}
		$sql = "
			if OBJECT_ID('tempdb..#tempDelacs') is not null drop table #tempDelacs;
			create table #tempDelacs (id varchar(1),msg varchar(max));
			begin tran DelAsc
			begin try
				declare @arin int = isnull((
						select case when COUNT(*) = 0 then 0 else 1 end from {$this->MAuth->getdb('ARINOPT')} 
						where CONTNO = '".$arrs['contno']."' and LOCAT = '".$arrs['locat']."'
					)
				,0)
				declare @arop int = isnull((
						select COUNT(*) from {$this->MAuth->getdb('AROPTMST')} 
						where CONTNO = '".$arrs['contno']."' and CUSCOD = '".$arrs['cuscod']."' 
						and LOCAT = '".$arrs['locat']."' and TAXNO = '".$arrs['taxno']."'
					)
				,0)
				declare @taxt int = isnull((
						select COUNT(*) from {$this->MAuth->getdb('TAXTRAN')} 
						where CONTNO = '".$arrs['contno']."' and CUSCOD = '".$arrs['cuscod']."' 
						and LOCAT = '".$arrs['locat']."' and TAXNO = '".$arrs['taxno']."'
					)
				,0)
				if(@arin = 1 and @arop = 1 and @taxt = 1)
					begin
						ALTER TABLE {$this->MAuth->getdb('AROPTMST')} DISABLE TRIGGER AFTDEL_AROPMST
						
						ALTER TABLE {$this->MAuth->getdb('ARINOPT')} DISABLE TRIGGER AFTDEL_ARINOPT
						
						--trigger --AROPTMST --insert CANOPMST
						insert into {$this->MAuth->getdb('CANOPMST')} (
							LOCAT,CONTNO,CUSCOD,INCLVAT,SDATE,
							VATRT,TOTPRC,SMPAY,SMCHQ,TAXDT,TAXNO,
							SALCOD,TSALE,USERID,INPDT,DELID,DELDT,POSTDT
						)select 
							LOCAT,CONTNO,CUSCOD,INCLVAT,SDATE,
							VATRT,OPTPTOT,SMPAY,SMCHQ,TAXDT,TAXNO,
							SALCOD,TSALE,USERID,INPDT,'".$this->sess["USERID"]."',getdate(),POSTDT 
						from {$this->MAuth->getdb('AROPTMST')} 
						where CONTNO = '".$arrs['contno']."' and CUSCOD = '".$arrs['cuscod']."' 
						and LOCAT = '".$arrs['locat']."' and TAXNO = '".$arrs['taxno']."'
						
						delete from {$this->MAuth->getdb('AROPTMST')}  
						where CONTNO = '".$arrs['contno']."' and CUSCOD = '".$arrs['cuscod']."' 
						and LOCAT = '".$arrs['locat']."' and TAXNO = '".$arrs['taxno']."'
						
						delete from {$this->MAuth->getdb('ARINOPT')} 
						where CONTNO = '".$arrs['contno']."' and LOCAT = '".$arrs['locat']."'
						
						update {$this->MAuth->getdb('TAXTRAN')} set FLAG = 'C',CANDT = GETDATE(),FLCANCL = '".$this->sess["USERID"]."' 
						where CONTNO = '".$arrs['contno']."' and CUSCOD = '".$arrs['cuscod']."' 
						and LOCAT = '".$arrs['locat']."' and TAXNO = '".$arrs['taxno']."'
						
						/*trigger update OPTMAST delete ARINOPT*/
						".$arrs['trigger_delete']."
						
						insert into {$this->MAuth->getdb('hp_UserOperationLog')} (
							userId,descriptions,postReq,dateTimeTried,ipAddress,functionName
						)values (
							'".$this->sess["IDNo"]."','SYS04::ลบอุปกรณ์เสริม'
							,'".$arrs['contno']."'+' ".str_replace("'","",var_export($_REQUEST, true))."'
							,getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."'
						);
						
						ALTER TABLE {$this->MAuth->getdb('ARINOPT')} ENABLE TRIGGER AFTDEL_ARINOPT
						
						ALTER TABLE {$this->MAuth->getdb('AROPTMST')} ENABLE TRIGGER AFTDEL_AROPMST
					end
				else
					begin
						rollback tran DelAsc;
						insert into #tempDelacs select 'N' as id,'บันทึกข้อมูลไม่สำเร็จ : กรุณตรวจสอบข้อมูลอีกครั้งครับ' as msg;
						return;
					end
				insert into #tempDelacs select 'Y' as id,'สำเร็จ ลบข้อมูลรายการขายอุปกรณ์เสริมเลขที่ :: ".$arrs['contno']." เรียบร้อยแล้ว' as msg;
				commit tran DelAsc;	
			end try
			begin catch
				rollback tran DelAsc;
				insert into #tempDelacs select 'N' as id,'บันทึกข้อมูลไม่สำเร็จ : กรุณาติดต่อฝ่ายไอที' as msg;
				return;
			end catch
		";
		//echo $sql; exit;
		$this->db->query($sql);
		
		$sql = "
			select * from #tempDelacs
		";
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$response["error"] = ($row->id == "Y" ? false:true);
				$response["msg"]   = $row->msg;
			}
		}else{
			$response["error"] = true;
			$response["msg"]   = "บันทึกข้อมูลไม่สำเร็จ : กรุณาติดต่อฝ่ายไอที";
		}
		echo json_encode($response);
	}
}