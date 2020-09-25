<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/********************************************************
             ______@27/08/2020______
			 Pasakorn Boonded

********************************************************/
class ReceiveOPTINV extends MY_Controller {
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
							เลขที่ใบรับ
							<input type='text' id='RECVNO' class='form-control input-sm' placeholder='เลขที่ใบรับ' >
						</div>
					</div>
					<div class='col-sm-3'>	
						<div class='form-group'>
							วันที่ใบรับ
							<input type='text' id='RECVDT' class='form-control input-sm' placeholder='วันที่ใบรับ' data-provide='datepicker' data-date-language='th-th' value='{$this->today('today')}'>
						</div>
					</div>
					<div class='col-sm-3'>	
						<div class='form-group'>
							สาขา
							<input type='text' id='RVLOCAT' class='form-control input-sm' placeholder='สาขา' value='{$this->sess['branch']}' >
						</div>
					</div>	
					<div class='col-sm-3'>	
						<div class='form-group'>
							เลขที่ใบส่งสินค้า
							<input type='text' id='INVNO' class='form-control input-sm' placeholder='รหัสเจ้าหนี้' >
						</div>
					</div>	
					<div class='col-sm-6'>
						<div class='form-group'>
							<button id='btnaddoptinv' class='btn btn-cyan btn-block'><span class='glyphicon glyphicon-pencil'>ทำรายการรับอุปกรณ์</span></button>
						</div>
					</div>
					<div class='col-sm-6'>
						<div class='form-group'>
							<button id='btnsearchlist' class='btn btn-primary btn-block'><span class='glyphicon glyphicon-search'>ค้นหารายการรับอุปกรณ์</span></button>
						</div>
					</div>
				</div>
				<div class='col-sm-12'>
					<div class='col-sm-12'>
						<div id='resultOptinv'></div>
					</div>
				</div>
			</div>
		";
		$html .="<script src='".base_url('public/js/SYS02/ReceiveOPTINV.js')."'></script>";
		echo $html;
	}
	function Search(){
		$RECVNO   = $_REQUEST['RECVNO'];
		$RECVDT   = $this->Convertdate(1,$_REQUEST['RECVDT']);
		$RVLOCAT  = $_REQUEST['RVLOCAT'];
		$INVNO    =  $_REQUEST['INVNO'];
		$html = ""; $cond = "";
		if($RECVNO != ""){
			$cond .= " and RECVNO like '".$RECVNO."%'";
		}
		if($RECVDT != ""){
			$cond .= " and RECVDT = '".$RECVDT."'";
		}
		if($RVLOCAT != ""){
			$cond .= " and RVLOCAT like '".$RVLOCAT."%'";
		}
		if($INVNO != ""){
			$cond .= " and INVNO like '".$INVNO."%'";
		}
		$sql = "
			select ".($cond == "" ? "top 20":"")." RECVNO,convert(varchar(8),RECVDT,112) as RECVDT
				,RVLOCAT,APCODE,INVNO,TAXNO 
			from {$this->MAuth->getdb('OPTINV')} where 1=1 ".$cond."
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		$NRow = 1;
		if($query->row()){
			foreach($query->result() as $row){
				$html .="
					<tr class='trow' style='height:30px;' seq='".$NRow."'>
						<td align='center' class='getit' seq='".$NRow++."' RECVNO='".str_replace(chr(0),'',$row->RECVNO)."' style='cursor:pointer;'>เลือก</td>
						<td>".$row->RECVNO."</td>
						<td>".$this->Convertdate(2,$row->RECVDT)."</td>
						<td>".$row->RVLOCAT."</td>
						<td>".$row->APCODE."</td>
						<td>".$row->INVNO."</td>
						<td>".$row->TAXNO."</td>
					</tr>
				";
			}
		}
		$html = "
			<div id='table-fixed-Receiveoptinv' class='col-sm-12' style='height:100%;width:100%;overflow:auto;'>
				<table id='table-Receiveoptinv' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='99%'>
					<thead>
						<tr style='height:50px;'>
							<th style='vertical-align:middle;'>#</th>
							<th style='vertical-align:middle;'>เลขที่ใบรับ</th>
							<th style='vertical-align:middle;'>วันที่ใบรับ</th>
							<th style='vertical-align:middle;'>สาขา</th>
							<th style='vertical-align:middle;'>รหัสเจ้าหนี้</th>
							<th style='vertical-align:middle;'>เลขที่ใบส่งสินค้า</th>
							<th style='vertical-align:middle;'>เลขที่ใบกำกับสินค้า</th>
						</tr>
					</thead>
					<tbody>
						".$html."
					</tbody>
				</table>
			</div>
		";
		$response = array("html"=>$html);
		echo json_encode($response);
	}
	function FromAddStockASC(){
		$data = array();
		
		$sql = "
			select * from {$this->MAuth->getdb('VATMAST')}
			where getdate() between FRMDATE and TODATE
		";
		$query = $this->db->query($sql);
		$row = $query->row();
		$data['vatrt'] = number_format($row->VATRT,2);
		$html = "
			<div id='wizard-optinv' class='wizard-wrapper'>
				<div class='wizard'>
					<form id='demo-form2' action='' class='lobi-form' novalidate='novalidate'>
						<ul class='wizard-tabs wizard-tab-balls nav-justified nav nav-pills'>
							<li class='active'>
								<a href='#tab11' prev='#' data-toggle='tab' aria-expanded='true'>
									<span class='step'>1</span>
									<span class='title' style='font-size:12pt;'>ใบรับอุปกรณ์เสริม</span>
								</a>
							</li>
							<li class=''>
								<a href='#tab22' prev='#tab11' data-toggle='tab'>
									<span class='step'>2</span>
									<span class='title' style='font-size:12pt;'>รายการอุปกรณ์เสริม</span>
								</a>
							</li>
						</ul>
						<div class='tab-content bg-white'>
							".$this->getformaddTab11($data)."
							".$this->getformaddTab22($data)."
						</div>
					</form>
				</div>
			</div>
			<div class='col-sm-12 text-right'>
				<div class='row'>
					<button id='btn_del' class='btn btn-xs btn-danger' style='width:100px;'><span class='glyphicon glyphicon-trash'> ลบ</span></button>
					<button id='btn_save' class='btn btn-xs btn-primary' style='width:100px;'><span class='glyphicon glyphicon-floppy-disk'> บันทึก</span></button>	
				</div>
			</div>
		";
		$response = array("html"=>$html);
		echo json_encode($response);
	}
	function getformaddTab11($data){
		$html = "
			<div class='tab-pane active' name='tab11' style='height:calc(100vh - 230px);overflow:auto;'>
				<fieldset style='height:100%;'>
					<div class='row' style='height:100%'>
						<div style='float:left;height:100%;' class='col-sm-8 col-sm-offset-2'>
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
										เลขที่ใบรับสินค้า
										<input type='text' id='add_recvno' class='form-control input-sm' placeholder='เลขที่สัญญา'  value=''>
									</div>
								</div>
								<div class='col-sm-4'>	
									<div class='form-group'>
										วันที่รับสินค้า
										<input type='text' id='add_recvdt' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' value='".$this->today('today')."'>
									</div>
								</div>	
								<div class='col-sm-4'>	
									<div class='form-group'>
										<span class='text-red'>*</span>
										รหัสเจ้าหนี้
										<select id='add_apcode' class='form-control input-sm'></select>
									</div>
								</div>	
								<div class='col-sm-4'>	
									<div class='form-group'>
										เครดิต(วัน)
										<input type='text' id='add_credit' class='form-control input-sm' placeholder='เครดิต(วัน)'  value='0'>
									</div>
								</div>
								<div class='col-sm-4'>	
									<div class='form-group'>
										อัตราภาษี
										<div class='input-group'>
											<input style='text-align:right;' type='text' id='add_vatrt' class='form-control input-sm' value='".$data['vatrt']."' readonly>
											<span class='input-group-addon'><b>%</b></span>	
										</div>
									</div>
								</div>
								<div class='col-sm-4'>	
									<div class='form-group'>
										<span class='text-red'>*</span>
										เลขที่ใบส่งสินค้า
										<input type='text' id='add_invno' class='form-control input-sm' placeholder='เลขที่ใบส่งสินค้า'  value=''>
									</div>
								</div>
								<div class='col-sm-4'>	
									<div class='form-group'>
										<span class='text-red'>*</span>
										วันที่ส่งสินค้า
										<input type='text' id='add_invdt' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' placeholder='วันที่ใบส่งสินค้า'  value=''>
									</div>
								</div>	
								<div class='col-sm-4'>	
									<div class='form-group'>
										เลขที่ใบกำกับ
										<input type='text' id='add_taxno' class='form-control input-sm' placeholder='เลขที่ใบกำกับ'  value=''>
									</div>
								</div>
								<div class='col-sm-4'>	
									<div class='form-group'>
										วันที่ใบกำกับ
										<input type='text' id='add_taxdt' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' placeholder='วันที่ใบกำกับ' value=''>
									</div>
								</div>	
								<div class='col-sm-4'>	
									<div class='form-group'>
										<span class='text-red'>*</span>
										รหัสผู้รับสินค้า
										<select id='add_rvcode' class='form-control input-sm'></select>
									</div>
								</div>	
								<div class='col-sm-4'>	
									<div class='form-group'>
										วันที่ครบดิว
										<input type='text' id='add_duedt' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' value='".$this->today('today')."'>
									</div>
								</div>
								<div class='col-sm-4'>	
									<div class='form-group'>
										<span class='text-red'>*</span>
										แบบใบกำกับ
										<select id='add_fltax' class='form-control input-sm'>
											<option value=''></option>
											<option value='A'>ยื่นเพิ่มเติม</option>
											<option value='N'>ไม่ยืนเพิ่มเติม</option>
										</select>
									</div>
								</div>								
								<div class='col-sm-8'>	
									<div class='form-group'>
										คำอธิบาย
										<textarea type='text' id='add_descp' style='height:35px;' class='form-control input-sm'></textarea>
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
	function getformaddTab22($data){
		$html = "
			<div class='tab-pane' name='tab22' style='height:calc(100vh - 260px);overflow:auto;'>
				<fieldset style='height:100%'>
					<div class='row' style='height:100%'>
						<div style='float:left;height:100%;overflow:auto;' class='col-sm-12'>
							<div class='row' style='width:100%;height:100%;padding-left:30px;background-color:#269da1;'>
								<div class='form-group col-sm-12' style='height:100%;'>
									<span style='color:#efff14;'>บันทึกอุปกรณ์เสริมเข้าสต๊อก</span>
									<div id='dataTable-fixed-asc' class='dataTables_wrapper dt-bootstrap4 table-responsive' style='max-height:calc(100% - 130px);height:calc(100% - 130px);overflow:auto;border:1px dotted black;background-color:#eee;'>
										<table id='dataTables-asc' class='table table-bordered dataTable table-hover' stat='' aria-describedby='dataTable_info' style='width:100%;line-height:10px;white-space:nowrap;text-overflow:ellipsis;overflow:hidden;' cellspacing='0'>
											<thead>
												<tr role='row'>
													<th style='width:40px'>
														<i id='btn_optcode' class='btn btn-xs btn-success glyphicon glyphicon-plus' style='cursor:pointer;'> เพิ่ม  </i>
													</th>
													<th>รหัสอุปกรณ์</th>
													<th>ชื่ออุปกรณ์</th>
													<th>จำนวนรับ</th>
													<th>ราคา/หน่วย</th>
													<th>จำนวนเงิน</th>
													<th>ส่วนลด(บาท)</th>
													<th>ยอดสุทธิ</th>
												</tr>
											</thead>
											<tbody style='white-space: nowrap;'></tbody>
										</table>
									</div><br>
									<div class='col-sm-2 col-sm-offset-6'>	
										<div class='form-group'>
											มูลค่ารวม
											<input type='text' id='get_netcst' style='text-align: right;' class='form-control input-sm' placeholder='0.00'  disabled>
										</div>
									</div>
									<div class='col-sm-2'>	
										<div class='form-group'>
											ภาษีมูลค่าเพิ่ม
											<input type='text' id='get_netvat' style='text-align: right;' class='form-control input-sm' placeholder='0.00'  disabled>
										</div>
									</div>
									<div class='col-sm-2'>	
										<div class='form-group'>
											รวมทั้งสิ้น
											<input type='text' id='get_nettot' style='text-align: right;' class='form-control input-sm' placeholder='0.00'  disabled>
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
	function loadFormOPT(){
		$response = array();
		$RECVNO = $_REQUEST["recvno"];
		$sql = "
			select  O.RVLOCAT,O.RECVNO,CONVERT(varchar(8),O.RECVDT,112) as RECVDT,O.APCODE,O.CREDIT,O.VATRT
				,O.INVNO,convert(varchar(8),O.INVDT,112) as INVDT,O.TAXNO,CONVERT(varchar(8),O.TAXDT,112) as TAXDT
				,O.RVCODE,CONVERT(varchar(8),O.DUEDT,112) as DUEDT,O.FLTAX,O.DESCP,O.NETCST,O.NETVAT
				,O.NETTOT,AP.APNAME,US.NAME as RVNAME
			from {$this->MAuth->getdb('OPTINV')} O 
			left join {$this->MAuth->getdb('APMAST')} AP on O.APCODE = AP.APCODE 
			left join {$this->MAuth->getdb('OFFICER')} US on O.RVCODE = US.CODE
			where O.RECVNO = '".$RECVNO."'
		";
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$response["RVLOCAT"] = $row->RVLOCAT;
				$response["RECVNO"]  = $row->RECVNO;
				$response["RECVDT"]  = $this->Convertdate(2,$row->RECVDT);
				$response["APCODE"]  = $row->APCODE;
				$response["APNAME"]  = $row->APNAME;
				$response["CREDIT"]  = $row->CREDIT;
				$response["VATRT"]   = $row->VATRT;
				$response["INVNO"]   = $row->INVNO;
				$response["INVDT"]   = $this->Convertdate(2,$row->INVDT);
				$response["TAXNO"]   = $row->TAXNO;
				$response["TAXDT"]   = $this->Convertdate(2,$row->TAXDT);
				$response["RVCODE"]  = $row->RVCODE;
				$response["RVNAME"]  = $row->RVNAME;
				$response["DUEDT"]   = $this->Convertdate(2,$row->DUEDT);
				$response["FLTAX"]   = $row->FLTAX;
				$response["FLTAXNM"] = ($row->FLTAX == "A" ? "ยื่นเพิ่มเติม":"ไม่ยื่นเพิ่มเติม");
				$response["DESCP"]   = $row->DESCP;
				$response["NETCST"]  = $row->NETCST;
				$response["NETVAT"]  = $row->NETVAT;
				$response["NETTOT"]  = $row->NETTOT;
			}
		}
		$sql = "
			select 
				OPTCODE,OPTNAME,QTY,UNITCST,TOTCST,DSCAMT,NETCST,VATRT 
			from {$this->MAuth->getdb('OPTTRAN')} 
			where RECVNO = '".$RECVNO."'
		";
		$query = $this->db->query($sql);
		$listopt = "";
		if($query->row()){
			foreach($query->result() as $row){
				$listopt .="
					<tr>
						<td align='center'>
							<i class='acslistdel btn btn-xs btn-danger glyphicon glyphicon-minus'
								OPTCODE = '".$row->OPTCODE."' OPTNAME = '".$row->OPTNAME."' QTY = '".$row->QTY."' 
								UNITCST = '".$row->UNITCST."' TOTCST = '".$row->TOTCST."' DSCAMT = '".$row->DSCAMT."' 
								NETCST = '".$row->NETCST."' VATRT='".$row->VATRT."'
							>ลบ</i> 
						</td>
						<td>".$row->OPTCODE."</td>
						<td>".$row->OPTNAME."</td>
						<td align='right'>".$row->QTY."</td>
						<td align='right'>".number_format($row->UNITCST,2)."</td>
						<td align='right'>".number_format($row->TOTCST,2)."</td>
						<td align='right'>".number_format($row->DSCAMT,2)."</td>
						<td align='right'>".number_format($row->NETCST,2)."</td>
					</tr>
				";
			}
		}	
		$response["listopt"] = $listopt;
		echo json_encode($response);
	}
	function Addlistdetail_opt(){
		$optcode   = $_REQUEST["optcode"];
		$qty       = $_REQUEST["qty"];
		$unitcst   = $_REQUEST["unitcst"];
		$totcst    = $_REQUEST["totcst"];
		$dscamt    = $_REQUEST["dscamt"];
		$netcst    = $_REQUEST["netcst"];
		$add_vatrt = $_REQUEST["add_vatrt"];
		$locat     = $_REQUEST["locat"];
		
		$response = array();
		if($optcode == ""){
			$response["error"] = true;
			$response["msg"]   = "กรุณาเลือกรหัสอุปกรณ์เสริมก่อนครับ";	
			echo json_encode($response); exit;
		}
		if($qty == ""){
			$response["error"] = true;
			$response["msg"]   = "กรุณากรอกจำนวนที่รับอุปกรณ์เสริมก่อนครับ";	
			echo json_encode($response); exit;
		}
		if($unitcst == ""){
			$response["error"] = true;
			$response["msg"]   = "กรุณากรอกราคาต่อ/หน่วย ของอุปกรณ์เสริมก่อนครับ";	
			echo json_encode($response); exit;
		}
		if($dscamt == ""){
			$response["error"] = true;
			$response["msg"]   = "กรุณากรอกส่วนลดทั้งหมด ของอุปกรณ์เสริมก่อนครับ";	
			echo json_encode($response); exit;
		}
		
		
		$sql = "
			select OPTCODE,OPTNAME from {$this->MAuth->getdb('OPTMAST')}
			where LOCAT = '".$locat."' and OPTCODE = '".$optcode."'
		";
		$query = $this->db->query($sql);
		$optn  = $query->row();
		$optname = $optn->OPTNAME;
		
		$html = "
			<tr>
				<td align='center'>
					<i class='acslistdel btn btn-xs btn-danger glyphicon glyphicon-minus'
						OPTCODE = '".$optcode."' OPTNAME = '".str_replace(chr(0),"",$optname)."' QTY = '".$qty."' 
						UNITCST = '".$unitcst."' TOTCST = '".$totcst."' DSCAMT = '".$dscamt."' 
						NETCST = '".$netcst."' 
					>ลบ</i> 
				</td>
				<td>".$optcode."</td>
				<td>".$optname."</td>
				<td align='right'>".$qty."</td>
				<td align='right'>".number_format($unitcst,2)."</td>
				<td align='right'>".number_format($totcst,2)."</td>
				<td align='right'>".number_format($dscamt,2)."</td>
				<td align='right'>".number_format($netcst,2)."</td>
			</tr>
		";
		
		$response["vatrt"]  = $add_vatrt;
		$response["html"]   = $html;
		echo json_encode($response); exit;
	}
	function Checksave($arrs){
		$response = array();
		$msg = "";
		if($arrs["APCODE"]  == ""){ $msg = "รหัสเจ้าหนี้";    }
		if($arrs["INVNO"]   == ""){ $msg = "เลขที่ใบส่งสินค้า"; }
		if($arrs["RVCODE"]  == ""){ $msg = "วันที่ส่งสิค้า";    }
		if($arrs["RVCODE"]  == ""){ $msg = "รหัสผู้รับสินค้า";  }
		if($arrs["FLTAX"]   == ""){ $msg = "แบบใบกำกับ";   }
		if($arrs["listopt"] == "nolist"){  $msg = "รายการอุุปกรณ์เสริม"; }
		if($msg != ""){
			$response["error"] = "N";
			$response["msg"]   = "กรุณากรอกหรือระบุ{$msg}ก่อนครับ";
			echo json_encode($response); exit;
		}
	}
	function Save(){
		$arrs = array();
		$arrs["LOCAT"]   = $_REQUEST["LOCAT"];
		$arrs["RECVNO"]  = $_REQUEST["RECVNO"];
		$arrs["RECVDT"]  = $this->Convertdate(1,$_REQUEST["RECVDT"]);
		$arrs["APCODE"]  = $_REQUEST["APCODE"];
		$arrs["CREDIT"]  = $_REQUEST["CREDIT"];
		$arrs["VATRT"]   = $_REQUEST["VATRT"];
		$arrs["INVNO"]   = $_REQUEST["INVNO"];
		$arrs["INVDT"]   = $this->Convertdate(1,$_REQUEST["INVDT"]);
		$arrs["TAXNO"]   = $_REQUEST["TAXNO"];
		$arrs["TAXDT"]   = $this->Convertdate(1,$_REQUEST["TAXDT"]);
		$arrs["RVCODE"]  = $_REQUEST["RVCODE"];
		$arrs["DUEDT"]   = $this->Convertdate(1,$_REQUEST["DUEDT"]);
		$arrs["FLTAX"]   = $_REQUEST["FLTAX"];	
		$arrs["DESCP"]   = $_REQUEST["DESCP"];
		$arrs["listopt"] = $_REQUEST["listopt"];
		
		$arrs["NETCST"]  = str_replace(",","",$_REQUEST["NETCST"]);
		$arrs["NETVAT"]  = str_replace(",","",$_REQUEST["NETVAT"]);
		$arrs["NETTOT"]  = str_replace(",","",$_REQUEST["NETTOT"]);
		$arrs["optdel"]  = "";
		$arrs["listdel"] = "";
		
		$this->Checksave($arrs);	
		$listopt         = $arrs["listopt"];
		$sizeopt = sizeof($listopt);
		$sql_opt = "";
		for($i=0;$i<$sizeopt;$i++){
			if($arrs["RECVNO"] == "Auto Genarate"){
				$sql_opt .="
					insert into {$this->MAuth->getdb('OPTTRAN')}(
						[RECVNO],[RVLOCAT],[OPTCODE],[OPTNAME],[QTY],[AVGCST],[UNITCST]
						,[TOTCST],[DSCAMT],[NETCST],[NETVAT],[VATRT],[INPDT],[USERID]
						,[RECVDT]
					)values(
						@RECVNO,'".$arrs["LOCAT"]."','".$listopt[$i][0]."','".$listopt[$i][1]."'
						,".$listopt[$i][2].",null,".$listopt[$i][3].",".$listopt[$i][4].",".$listopt[$i][5]."
						,".$listopt[$i][6].",null,".$arrs["VATRT"].",getdate(),'".$this->sess["USERID"]."'
						,'".$arrs["RECVDT"]."'
					)
					--insert tb OPTTRAN trigger update tb OPTMAST 
					update {$this->MAuth->getdb('OPTMAST')} set ONHAND = ONHAND + ".$listopt[$i][2]." 
					where OPTCODE = '".$listopt[$i][0]."' and LOCAT = '".$arrs["LOCAT"]."'
				";	
			}else{
				$sql_opt .="
					if not exists(
						select * from {$this->MAuth->getdb('OPTTRAN')} 
						where RECVNO = '".$arrs["RECVNO"]."' and OPTCODE = '".$listopt[$i][0]."'
					)
					begin 
						insert into {$this->MAuth->getdb('OPTTRAN')}(
							[RECVNO],[RVLOCAT],[OPTCODE],[OPTNAME],[QTY],[AVGCST],[UNITCST]
							,[TOTCST],[DSCAMT],[NETCST],[NETVAT],[VATRT],[INPDT],[USERID]
							,[RECVDT]
						)values(
							@RECVNO,'".$arrs["LOCAT"]."','".$listopt[$i][0]."','".$listopt[$i][1]."'
							,".$listopt[$i][2].",null,".$listopt[$i][3].",".$listopt[$i][4].",".$listopt[$i][5]."
							,".$listopt[$i][6].",null,".$arrs["VATRT"].",getdate(),'".$this->sess["USERID"]."'
							,'".$arrs["RECVDT"]."'
						)
						--insert tb OPTTRAN trigger update tb OPTMAST 
						update {$this->MAuth->getdb('OPTMAST')} set ONHAND = ONHAND + ".$listopt[$i][2]." 
						where OPTCODE = '".$listopt[$i][0]."' and LOCAT = '".$arrs["LOCAT"]."'
					end
				";
				if($arrs['optdel'] != ""){
					$arrs['optdel'] .= "','";
				}
				$arrs['optdel'] .= $listopt[$i][0];
			}
		}
		//echo $sql_opt; exit;
		
		if($arrs["RECVNO"] == "Auto Genarate"){
			$sql = "
				if OBJECT_ID('tempdb..#tempsaveopt') is not null drop table #tempsaveopt;
				create table #tempsaveopt (id varchar(1),msg varchar(max));
				begin tran saveopt
				begin try
					--เลขที่ใบรับสินค้า
					declare @h_recvop varchar(10) = (select H_RECVOP from {$this->MAuth->getdb('CONDPAY')});
					declare @rec varchar(10) = (
						select SHORTL+@h_recvop+'-'+right(left(convert(varchar(8),'".$arrs["RECVDT"]."',112),6),4) 
						from {$this->MAuth->getdb('INVLOCAT')} where LOCATCD = '".$arrs["LOCAT"]."'
					);
					declare @RECVNO varchar(12) = isnull((
						select MAX(RECVNO) from {$this->MAuth->getdb('OPTINV')}
						where RECVNO like ''+@rec+'%' collate thai_cs_as)
						,@rec+'0000'
					);
					set @RECVNO = left(@RECVNO,8)+right(right(@RECVNO,4)+10001,4);

					if not exists(
						select * from {$this->MAuth->getdb('APINVOI')} where INVNO = '".$arrs["INVNO"]."'
					)
					begin
						ALTER TABLE {$this->MAuth->getdb('OPTINV')} DISABLE TRIGGER AFTINS_OPTINV
						
						insert into {$this->MAuth->getdb('OPTINV')} (
							RECVNO,RVLOCAT,RECVDT,INVNO,INVDT,APCODE,VATRT,CREDIT,DUEDT,TAXNO,TAXDT 
							,FLTAX,DESCP,RVCODE,TOTCST,DSCRAT,DSCAMT,NETCST,NETVAT,NETTOT
							,SPAYMT,SDISC,FLAG,LPAYDT,INPDT,USERID,UPD 
						)values(
							@RECVNO,'".$arrs["LOCAT"]."','".$arrs["RECVDT"]."','".$arrs["INVNO"]."','".$arrs["INVDT"]."'
							,'".$arrs["APCODE"]."',".$arrs["VATRT"].",".$arrs["CREDIT"].",'".$arrs["DUEDT"]."'
							,case when '".$arrs["TAXNO"]."' <> '' then '".$arrs["TAXNO"]."' else null end
							,case when '".$arrs["TAXDT"]."' <> '' then '".$arrs["TAXDT"]."' else null end,'".$arrs["FLTAX"]."'
							,'".$arrs["DESCP"]."','".$arrs["RVCODE"]."',0,0,0,".$arrs["NETCST"].",".$arrs["NETVAT"].",".$arrs["NETTOT"]."
							,0,0,null,null,getdate(),'".$this->sess["USERID"]."',null
						)
						--insert tb OPTIVN trigger -> insert tb APINVOI
						insert into {$this->MAuth->getdb('APINVOI')}(
							INVNO,LOCAT,APCODE,RECVDT,RECVNO,CREDTM,INVDUE,TAXDATE,
							INVDATE,DESCRP,VATTYPE,TOTAL,DISCT,DISCAMT,BALANCE,VATRT,VATAMT,NETTOTAL,
							TAXNO,SMPAY,SMCHQ,KANG,TNOPAY,TUPAY,FLAG
						)
						select INVNO,RVLOCAT,APCODE,RECVDT,RECVNO,CREDIT,DUEDT,TAXDT,
							INVDT,DESCP,'2',NETCST,0,0,NETCST,VATRT,NETVAT,NETTOT,TAXNO,0,0,
							NETTOT,1,1,'1'  
						from {$this->MAuth->getdb('OPTINV')} where INVNO = '".$arrs["INVNO"]."' 
						
						ALTER TABLE {$this->MAuth->getdb('OPTINV')} ENABLE TRIGGER AFTINS_OPTINV
						
						
						ALTER TABLE {$this->MAuth->getdb('OPTTRAN')} DISABLE TRIGGER AFTINS_OPTRN
						".$sql_opt."
						ALTER TABLE {$this->MAuth->getdb('OPTTRAN')} ENABLE TRIGGER AFTINS_OPTRN
						
						insert into {$this->MAuth->getdb('hp_UserOperationLog')} (
							userId,descriptions,postReq,dateTimeTried,ipAddress,functionName
						)values (
							'".$this->sess["IDNo"]."','SYS02::บันทึกอุปกรณ์เสริมเข้าสต๊อก'
							,@RECVNO+' ".str_replace("'","",var_export($_REQUEST, true))."'
							,getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."'
						);
						
						insert into #tempsaveopt select 'Y' as id,'สำเร็จ บันทึกข้อมูลรายการขายอุปกรณ์เสริมใบรับสินค้าเลขที่ :: '+@RECVNO+' เรียบร้อยแล้ว' as msg;
						commit tran saveopt;
					end
					else
					begin
						rollback tran saveopt;
						insert into #tempsaveopt select 'N' as id,'บันทึกข้อมูลไม่สำเร็จ : เลขที่สินค้าซ้ำในระบบเจ้าหนี้' as msg;
						return;
					end
				end try
				begin catch
					rollback tran saveopt;
					insert into #tempsaveopt select 'E' as id,'บันทึกข้อมูลไม่สำเร็จ : กรุณาติดต่อฝ่ายไอที' as msg;
					return;
				end catch
			";
		}else{
			$sql = "
				select OPTCODE,QTY from {$this->MAuth->getdb('OPTTRAN')} 
				where RECVNO = '".$arrs["RECVNO"]."' and OPTCODE not in ('".$arrs['optdel']."')
			";
			$query = $this->db->query($sql);
			if($query->row()){
				foreach($query->result() as $row){
					$arrs["listdel"] .="
						delete from {$this->MAuth->getdb('OPTTRAN')} 
						where RECVNO = '".$arrs["RECVNO"]."' and OPTCODE not in ('".$arrs['optdel']."')
						
						--trigger del list update OPTMAST
						update {$this->MAuth->getdb('OPTMAST')} set ONHAND = ONHAND - ".$row->QTY."
						where LOCAT = '".$arrs["LOCAT"]."' and OPTCODE = '".$row->OPTCODE."'
					";
				}
			}
			$sql = "
				if OBJECT_ID('tempdb..#tempsaveopt') is not null drop table #tempsaveopt;
				create table #tempsaveopt (id varchar(1),msg varchar(max));
				begin tran saveopt
				begin try
					declare @RECVNO varchar(20) = ('".$arrs["RECVNO"]."');
					declare @OPTINV int = (select count(*) from {$this->MAuth->getdb('OPTINV')} where RECVNO = @RECVNO);
					declare @INVNO  varchar(20) = ('".$arrs["INVNO"]."');
					declare @APINVOI varchar(20) = (
						select INVNO from {$this->MAuth->getdb('APINVOI')} where RECVNO = @RECVNO 
					);
					
					if (@OPTINV = 1)
					begin
						if (@INVNO = @APINVOI)
						begin
							update {$this->MAuth->getdb('OPTINV')} 
								set APCODE = '".$arrs["APCODE"]."',CREDIT = ".$arrs["CREDIT"].",INVNO = '".$arrs["INVNO"]."'
								,INVDT = '".$arrs["INVDT"]."',RVCODE = '".$arrs["RVCODE"]."',FLTAX = '".$arrs["FLTAX"]."'
								,DESCP = '".$arrs["DESCP"]."',NETCST = ".$arrs["NETCST"].",NETVAT = ".$arrs["NETVAT"]."
								,NETTOT = ".$arrs["NETTOT"]."
							where RECVNO = '".$arrs["RECVNO"]."'
							
							ALTER TABLE {$this->MAuth->getdb('OPTTRAN')} DISABLE TRIGGER AFTDEL_OPTRN
							".$arrs["listdel"]."
							ALTER TABLE {$this->MAuth->getdb('OPTTRAN')} ENABLE TRIGGER AFTDEL_OPTRN
							
							ALTER TABLE {$this->MAuth->getdb('OPTTRAN')} DISABLE TRIGGER AFTINS_OPTRN
							".$sql_opt."
							ALTER TABLE {$this->MAuth->getdb('OPTTRAN')} ENABLE TRIGGER AFTINS_OPTRN
							
							insert into {$this->MAuth->getdb('hp_UserOperationLog')} (
								userId,descriptions,postReq,dateTimeTried,ipAddress,functionName
							)values (
								'".$this->sess["IDNo"]."','SYS02::แก้ไขอุปกรณ์เสริมที่อยู่สต๊อก'
								,@RECVNO+' ".str_replace("'","",var_export($_REQUEST, true))."'
								,getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."'
							);
							
							insert into #tempsaveopt select 'Y' as id,'สำเร็จ แก้ไขข้อมูลรายการขายอุปกรณ์เสริมใบรับสินค้าเลขที่ :: '+@RECVNO+' เรียบร้อยแล้ว' as msg;
							commit tran saveopt;
						end
						else
						begin
							if not exists(
								select * from {$this->MAuth->getdb('APINVOI')} where INVNO = '".$arrs["INVNO"]."'
							)
							begin
								update {$this->MAuth->getdb('OPTINV')} 
									set APCODE = '".$arrs["APCODE"]."',CREDIT = ".$arrs["CREDIT"].",INVNO = '".$arrs["INVNO"]."'
									,INVDT = '".$arrs["INVDT"]."',RVCODE = '".$arrs["RVCODE"]."',FLTAX = '".$arrs["FLTAX"]."'
									,DESCP = '".$arrs["DESCP"]."',NETCST = ".$arrs["NETCST"].",NETVAT = ".$arrs["NETVAT"]."
									,NETTOT = ".$arrs["NETTOT"]."
								where RECVNO = '".$arrs["RECVNO"]."'
								
								update {$this->MAuth->getdb('APINVOI')} set INVNO = '".$arrs["INVNO"]."'
								where RECVNO = '".$arrs["RECVNO"]."'
								
								ALTER TABLE {$this->MAuth->getdb('OPTTRAN')} DISABLE TRIGGER AFTDEL_OPTRN
								".$arrs["listdel"]."
								ALTER TABLE {$this->MAuth->getdb('OPTTRAN')} ENABLE TRIGGER AFTDEL_OPTRN
								
								ALTER TABLE {$this->MAuth->getdb('OPTTRAN')} DISABLE TRIGGER AFTINS_OPTRN
								".$sql_opt."
								ALTER TABLE {$this->MAuth->getdb('OPTTRAN')} ENABLE TRIGGER AFTINS_OPTRN
								
								insert into {$this->MAuth->getdb('hp_UserOperationLog')} (
									userId,descriptions,postReq,dateTimeTried,ipAddress,functionName
								)values (
									'".$this->sess["IDNo"]."','SYS02::แก้ไขอุปกรณ์เสริมที่อยู่สต๊อก'
									,@RECVNO+' ".str_replace("'","",var_export($_REQUEST, true))."'
									,getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."'
								);
								
								insert into #tempsaveopt select 'Y' as id,'สำเร็จ แก้ไขข้อมูลรายการขายอุปกรณ์เสริมใบรับสินค้าเลขที่ :: '+@RECVNO+' เรียบร้อยแล้ว' as msg;
								commit tran saveopt;
							end
							else
							begin
								rollback tran saveopt;
								insert into #tempsaveopt select 'N' as id,'บันทึกข้อมูลไม่สำเร็จ : เลขที่ใบส่งสินค้าซ้ำในระบบเจ้าหนี้' as msg;
								return;
							end
						end
					end
					else
					begin
						rollback tran saveopt;
						insert into #tempsaveopt select 'N' as id,'บันทึกข้อมูลไม่สำเร็จ : กรุณาตรวจสอบข้อมูลอีกครั้ง' as msg;
						return;
					end	
				end try
				begin catch
					rollback tran saveopt;
					insert into #tempsaveopt select 'E' as id,'บันทึกข้อมูลไม่สำเร็จ : กรุณาติดต่อฝ่ายไอที' as msg;
					return;
				end catch
			";
		}
		//echo $sql; exit;
		$this->db->query($sql);
		
		$sql = "select * from #tempsaveopt";
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$response["error"] = $row->id;
				$response["msg"]   = $row->msg;
			}
		}else{
			$response["error"] = "error";
			$response["msg"]   = "ผิดพลาด กรุณาติดต่อฝ่ายไอที";
		}
		echo json_encode($response); 
	}
	function Delopt(){
		$arrs = array();
		$arrs["LOCAT"]  = $_REQUEST["LOCAT"];
		$arrs["RECVNO"] = $_REQUEST["RECVNO"];
		$arrs["INVNO"]  = $_REQUEST["INVNO"];
		
		$listopt        = $_REQUEST["listopt"]; 
		$sizeopt = sizeof($listopt);
		$sql_opt = "";
		for($i=0;$i<$sizeopt;$i++){
			$sql_opt .="
				delete from {$this->MAuth->getdb('OPTTRAN')} 
				where RECVNO = @RECVNO and RVLOCAT = @LOCAT and OPTCODE = '".$listopt[$i][0]."'
				--trigger delete OPTTRAN -->uptdate OPTMAST
				update {$this->MAuth->getdb('OPTMAST')} set ONHAND = ONHAND - ".$listopt[$i][1]." 
				where LOCAT = @LOCAT and OPTCODE = '".$listopt[$i][0]."'
			";
		}
		
		$sql = "
			if OBJECT_ID('tempdb..#tempdelopt') is not null drop table #tempdelopt;
			create table #tempdelopt (id varchar(1),msg varchar(max));
			begin tran delopt
			begin try
				declare @RECVNO varchar(20) = ('".$arrs["RECVNO"]."');
				declare @OPTINV int = (select count(*) from {$this->MAuth->getdb('OPTINV')} where RECVNO = @RECVNO);
				declare @INVNO  varchar(20) = ('".$arrs["INVNO"]."');
				declare @LOCAT  varchar(20) = ('".$arrs["LOCAT"]."');
				
				if (@OPTINV = 1)
				begin
					ALTER TABLE {$this->MAuth->getdb('OPTINV')} DISABLE TRIGGER AFTDEL_OPTINV
					
					delete from {$this->MAuth->getdb('OPTINV')} where RECVNO = @RECVNO and RVLOCAT = @LOCAT	
					--trigger delete OPTINV --> delete APINVOI
					delete from {$this->MAuth->getdb('APINVOI')} where RECVNO = @RECVNO and INVNO = @INVNO
											
					ALTER TABLE {$this->MAuth->getdb('OPTINV')} ENABLE TRIGGER AFTDEL_OPTINV
					
					ALTER TABLE {$this->MAuth->getdb('OPTTRAN')} DISABLE TRIGGER AFTDEL_OPTRN
				
					".$sql_opt."
				
					ALTER TABLE {$this->MAuth->getdb('OPTTRAN')} ENABLE TRIGGER AFTDEL_OPTRN
					
					insert into {$this->MAuth->getdb('hp_UserOperationLog')} (
						userId,descriptions,postReq,dateTimeTried,ipAddress,functionName
					)values (
						'".$this->sess["IDNo"]."','SYS02::ลบอุปกรณ์เสริมที่อยู่ในสต๊อก'
						,@RECVNO+' ".str_replace("'","",var_export($_REQUEST, true))."'
						,getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."'
					);
					
					insert into #tempdelopt select 'Y' as id,'สำเร็จ ลบข้อมูลรายการขายอุปกรณ์เสริมใบรับสินค้าเลขที่ :: '+@RECVNO+' เรียบร้อยแล้ว' as msg;
					commit tran delopt;
				end
				else
				begin
					rollback tran delopt;
					insert into #tempdelopt select 'N' as id,'บันทึกข้อมูลไม่สำเร็จ : กรุณาตรวจสอบข้อมูลอีกครั้ง' as msg;
					return;
				end	
			end try
			begin catch
				rollback tran delopt;
				insert into #tempdelopt select 'E' as id,'บันทึกข้อมูลไม่สำเร็จ : กรุณาติดต่อฝ่ายไอที' as msg;
				return;
			end catch
		";
		//echo $sql; exit;
		$this->db->query($sql);
		
		$sql = "select * from #tempdelopt";
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$response["error"] = $row->id;
				$response["msg"]   = $row->msg;
			}
		}else{
			$response["error"] = "error";
			$response["msg"]   = "ผิดพลาด กรุณาติดต่อฝ่ายไอที";
		}
		echo json_encode($response);
	}
}