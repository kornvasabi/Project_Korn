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
		//echo "เมนูยังไม่เสร็จ กำลังพัฒนาต่อครับ"; exit;
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
	function loadDetails(){
		$response = array();
		$CONTNO  = $_REQUEST["CONTNO"];
		$sql = "
			select a.CONTNO,a.LOCAT,convert(varchar(8),a.SDATE,112) as SDATE
				,a.RESVNO,a.APPVNO,a.CUSCOD,c.SNAM+c.NAME1+' '+c.NAME2+'('+c.CUSCOD+')'+'-'+c.GRADE as CUSNAME
				,a.INCLVAT,a.VATRT,a.ADDRNO
				,(select '('+ADDRNO+')'+ADDR1 from {$this->MAuth->getdb('CUSTADDR')} where CUSCOD = a.CUSCOD and ADDRNO = a.ADDRNO) as ADDR
				,a.STRNO,a.ACTICOD
				,(select '('+ACTICOD+')'+ACTIDES from {$this->MAuth->getdb('SETACTI')} where ACTICOD = a.ACTICOD) as ACTIDES
				,a.KEYIN,a.KEYINDWN,a.STDPRC,a.DSCPRC,a.FINCOD,f.FINNAME,a.FINCOM
				,a.SALCOD,p.USERNAME+'('+p.USERID+')' as USERNAME,a.COMITN,a.TAXNO
				,CONVERT(varchar(8),a.TAXDT,112) as TAXDT,a.ISSUNO,CONVERT(varchar(8),a.ISSUDT,112) as ISSUDT
				,a.OPTCTOT,a.OPTPTOT,a.RECOMCOD
				,(select SNAM+NAME1+' '+NAME2+'('+CUSCOD+')'+'-'+GRADE from {$this->MAuth->getdb('CUSTMAST')} where CUSCOD = a.RECOMCOD) as RECOMNAME
				,a.PAYDWN,a.PAYFIN,a.COMEXT,a.COMOPT,a.COMOTH,a.CRDTXNO,a.CRDAMT,a.MEMO1
			from {$this->MAuth->getdb('ARFINC')} a
			left join {$this->MAuth->getdb('CUSTMAST')} c on a.CUSCOD = c.CUSCOD
			left join {$this->MAuth->getdb('FINMAST')} f on a.FINCOD = f.FINCODE
			left join {$this->MAuth->getdb('PASSWRD')} p on a.SALCOD = p.USERID
			where a.CONTNO = '".$CONTNO."'
		";
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$response["CONTNO"]     = $row->CONTNO;
				$response["LOCAT"]      = $row->LOCAT;
				$response["SDATE"]      = $this->Convertdate(2,$row->SDATE);
				$response["RESVNO"]     = $row->RESVNO;
				$response["APPVNO"]     = $row->APPVNO;
				$response["CUSCOD"]     = $row->CUSCOD;
				$response["CUSNAME"]    = $row->CUSNAME;
				$response["INCLVAT"]    = $row->INCLVAT;
				$response["VATRT"]      = number_format($row->VATRT,2);
				$response["ADDRNO"]     = $row->ADDRNO;
				$response["ADDR"]       = $row->ADDR;
				$response["STRNO"]      = $row->STRNO;
				$response["ACTICOD"]    = $row->ACTICOD;
				$response["ACTIDES"]    = str_replace(chr(0),"",$row->ACTIDES);
				$response["KEYIN"]      = number_format($row->KEYIN,2);
				$response["KEYINDWN"]   = number_format($row->KEYINDWN,2);
				$response["STDPRC"]     = number_format($row->STDPRC,2);
				$response["DSCPRC"]     = number_format($row->DSCPRC,2);
				$response["FINCOD"]     = $row->FINCOD;
				$response["FINNAME"]    = str_replace(chr(0),"",$row->FINNAME);
				$response["FINCOM"]     = number_format($row->FINCOM,2);
				$response["SALCOD"]     = $row->SALCOD;
				$response["USERNAME"]   = $row->USERNAME;
				$response["COMITN"]     = number_format($row->COMITN,2);
				$response["TAXNO"]      = $row->TAXNO;
				$response["TAXDT"]      = $this->Convertdate(2,$row->TAXDT);
				$response["ISSUNO"]     = $row->ISSUNO;
				$response["ISSUDT"]     = $this->Convertdate(2,$row->ISSUDT);
				$response["OPTCTOT"]    = number_format($row->OPTCTOT,2);
				$response["OPTPTOT"]    = number_format($row->OPTPTOT,2);
				$response["RECOMCOD"]   = $row->RECOMCOD;
				$response["RECOMNAME"]  = $row->RECOMNAME;
				$response["PAYDWN"]     = number_format($row->PAYDWN,2);
				$response["PAYFIN"]     = number_format($row->PAYFIN,2);
				$response["COMEXT"]     = number_format($row->COMEXT,2);
				$response["COMOPT"]     = number_format($row->COMOPT,2);
				$response["COMOTH"]     = number_format($row->COMOTH,2);
				$response["CRDTXNO"]    = $row->CRDTXNO;
				$response["CRDAMT"]     = number_format($row->CRDAMT,2);
				$response["MEMO1"]      = $row->MEMO1;
			}
		}
		$sql = "
			select 
				o.OPTCODE,o.OPTCODE+'('+o.OPTNAME+')' as OPTNAME,a.UPRICE,a.QTY,a.NPRICE
				,a.TOTVAT,a.TOTPRC,a.OPTCST,a.OPTCVT,a.OPTCTOT
			from {$this->MAuth->getdb('ARINOPT')} a
			left join {$this->MAuth->getdb('OPTMAST')} o on a.OPTCODE = o.OPTCODE  
			where a.CONTNO = '".$CONTNO."' and o.LOCAT = '".$response["LOCAT"]."'
		";
		$response["listopt"] = "";
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$response["listopt"] .="
					<tr seq='new'>
						<td align='center'>
							<i class='inoptTab2 btn btn-xs btn-danger glyphicon glyphicon-minus'
								opCode = '".str_replace(chr(0),"",$row->OPTCODE)."' opText='".str_replace(chr(0),"",$row->OPTNAME)."' 
								total1 = '".$row->TOTPRC."' total2 = '".$row->OPTCTOT."' price1 = '".$row->NPRICE."'
								price2 = '".$row->OPTCST."' vat1 = '".$row->TOTVAT."' vat2 = '".$row->OPTCVT."'
								qty = '".$row->QTY."' uprice = '".$row->UPRICE."'
							>ลบ</i> 
						</td>
						<td>".$row->OPTNAME."</td>
						<td align='right'>".$row->UPRICE."</td>
						<td align='right'>".$row->QTY."</td>
						<td align='right'>".$row->NPRICE."</td>
						<td align='right'>".$row->TOTVAT."</td>
						<td align='right'>".$row->TOTPRC."</td>
						<td align='right'>".$row->OPTCST."</td>
						<td align='right'>".$row->OPTCVT."</td>
						<td align='right'>".$row->OPTCTOT."</td>
					</tr>
				";
			}
		}
		echo json_encode($response);
	}
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
					<input type='button' id='btnTax' class='btn btn-xs btn-info' style='width:100px;' value='ใบกำกับ'>
					<input type='button' id='btnSend' class='btn btn-xs btn-info' style='width:100px;' value='ใบส่งมอบ'>
					<input type='button' id='btnApproveSell' class='btn btn-xs btn-info' style='width:100px;' value='ใบอนุมัติขาย'>
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
										<input type='text' id='add_appvno' class='form-control input-sm' placeholder='เลขที่ใบอนุมัติ' >
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
												<label class='jzfs10' for='add2_optctot' style='color:#34dfb5;'>ต้นทุนรวม</label>
												<input type='text' id='add2_optctot' class='form-control input-sm text-right' value='0.00' disabled>
												<span id='error_add2_optcost' class='error text-danger jzError'></span>		
											</div>
											
											<div class='form-group col-sm-4'>
												<label class='jzfs10' for='add2_optptot' style='color:#34dfb5;'>ราคาขาย</label>
												<input type='text' id='add2_optptot' class='form-control input-sm text-right' value='0.00' disabled>
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
											<input type='text' id='add_inprc' class='form-control input-sm jzAllowNumber' placeholder='ราคาขาย' >
										</label>
									</div>
								</div>
							</div>
							<div class='col-sm-12 col-lg-6'>	
								<div class='form-group'>
									เงินดาวน์
									<input type='text' id='add_indwn' class='form-control input-sm jzAllowNumber' placeholder='เงินดาวน์' >
								</div>
							</div>
							<div class='col-sm-12 col-lg-6'>	
								<div class='form-group'>
									ราคาขายหน้าร้าน
									<input type='text' id='add_stdprc' class='form-control input-sm jzAllowNumber' placeholder='ราคาขายหน้าร้าน' >
								</div>
							</div>
							
							<div class='col-sm-12 col-lg-6'>	
								<div class='form-group'>
									ส่วนลด
									<input type='text' id='add_dscprc' class='form-control input-sm jzAllowNumber' placeholder='ส่วนลด'>
								</div>
							</div>
							
							<div class='col-sm-12 col-lg-6'>	
								รหัสบริษัทไฟแนนท์
								<select id='add_fincode' class='form-control input-sm' placeholder='พนักงานขาย'></select>
							</div>
							<div class='col-sm-12 col-lg-6'>	
								ค่าคอมไฟแนนท์
								<input type='text' id='add_fincom' class='form-control input-sm' placeholder='ค่าคอมไฟแนนท์'>
							</div>
							
							<div class='col-sm-12 col-lg-6'>	
								พนักงานขาย
								<!-- input type='text' id='add_salcod' class='form-control input-sm' placeholder='พนักงานขาย' -->
								<select id='add_salcod' class='form-control input-sm' placeholder='พนักงานขาย'></select>
							</div>
							<div class='col-sm-12 col-lg-6'>	
								ค่านายหน้าขาย
								<input type='text' id='add_comitn' class='form-control input-sm' placeholder='ค่านายหน้าขาย'>
							</div>
							
							<div class='col-sm-12 col-lg-6'>	
								<div class='form-group'>
									เลขที่ใบกำกับภาษี
									<input type='text' id='add_taxno' class='form-control input-sm' placeholder='ใบกำกับเงินดาวน์' value='Auto Genarate'  disabled>
								</div>
							</div>
							
							<div class='col-sm-12 col-lg-6'>	
								<div class='form-group'>
									วันที่ใบกำกับ
									<input type='text' id='add_taxdt' class='form-control input-sm' placeholder='วันที่ใบกำกับ' data-provide='datepicker' data-date-language='th-th' value='".$this->today('today')."' disabled>
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
									<input type='text' id='add_paydwn' class='form-control input-sm' placeholder='ชำระเงินดาวน์แล้ว' disabled>
								</div>
							</div>
							
							<div class='col-sm-4'>	
								<div class='form-group'>
									รับเงินจากไฟแนนซ์
									<input type='text' id='add_payfin' class='form-control input-sm' placeholder='รับชำระเงินแล้วทั้งหมด' disabled>
								</div>
							</div>
							
							<div class='col-sm-4'>	
								<div class='form-group'>
									ค่าคอมบุคคลนอก
									<input type='text' id='add_comext' class='form-control input-sm' placeholder='ค่าคอมบุคคลนอก' value='0.00'>
								</div>
							</div>
							
							<div class='col-sm-4'>	
								<div class='form-group'>
									ค่าของแถม
									<input type='text' id='add_comopt' class='form-control input-sm' placeholder='ค่าของแถม' value=''>
								</div>
							</div>
							
							<div class='col-sm-4'>	
								<div class='form-group'>
									ค่าใช้จ่ายอื่นๆ
									<input type='text' id='add_comoth' class='form-control input-sm' placeholder='ค่าใช้จ่ายอื่นๆ' value='0.00'>
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
	function change_resvno(){
		$resvno  = $_REQUEST["resvno"];
		$locat   = $_REQUEST["locat"];
		$response = array();
		
		$sql = "
			select a.RESVNO,a.LOCAT,a.CUSCOD
				,b.SNAM+b.NAME1+' '+b.NAME2+' ('+b.CUSCOD+')'+'-'+b.GRADE CUSNAME
				,b.GRADE,a.STRNO,a.SMCHQ,a.RESPAY,a.SMPAY,c.CRLOCAT,1 as ADDRNO
				,(
					select '('+aa.ADDRNO+') '+aa.ADDR1+' '+aa.ADDR2+' ต.'+aa.TUMB
						+' อ.'+bb.AUMPDES+' จ.'+cc.PROVDES+' '+aa.ZIP as ADDRNODetails 			
					from {$this->MAuth->getdb('CUSTADDR')} aa
					left join {$this->MAuth->getdb('SETAUMP')} bb on aa.AUMPCOD=bb.AUMPCOD
					left join {$this->MAuth->getdb('SETPROV')} cc on bb.PROVCOD=cc.PROVCOD
					where aa.CUSCOD=a.CUSCOD and aa.ADDRNO=1
				) as ADDRDES
				,aa.ACTICOD
				,(select '('+sa.ACTICOD+') '+sa.ACTIDES from {$this->MAuth->getdb('SETACTI')} sa 
					where sa.ACTICOD=aa.ACTICOD collate thai_cs_as) as ACTIDES
			from {$this->MAuth->getdb('ARRESV')} a
			left join {$this->MAuth->getdb('ARRESVOTH')} aa on a.RESVNO=aa.RESVNO collate thai_cs_as
			left join {$this->MAuth->getdb('CUSTMAST')} b on a.CUSCOD=b.CUSCOD
			left join {$this->MAuth->getdb('INVTRAN')} c on a.STRNO=c.STRNO and c.FLAG='D'
			where 1=1 and a.RESVNO='".$resvno."' and c.STRNO is not null
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				if($row->CRLOCAT != $locat){
					$response["error"] = true;
					$response["msg"]   = "ผิดพลาด รถที่จองไม่ได้อยู่ในสาขาที่ทำรายการคีย์ขายครับ";
					echo json_encode($response); exit;
 				}
				if($row->SMCHQ > 0){
					$response["error"] = true;
					$response["msg"]   = "เช็คเงินจองยังไม่ผ่าน";
					echo json_encode($response); exit;
				}
				if($row->RESPAY != $row->SMPAY){
					$response["error"] = true;
					$response["msg"]   = "เลขที่บิลจอง ".$row->RESVNO." ยังชำระเงินจองไม่ครบครับ";
					echo json_encode($response); exit;
				}
				$response["RESVNO"]  = $row->RESVNO;
				$response["LOCAT"]   = $row->LOCAT;
				$response["CUSCOD"]  = $row->CUSCOD;
				$response["CUSNAME"] = $row->CUSNAME;
				$response["STRNO"]   = $row->STRNO;
				$response["CRLOCAT"] = $row->CRLOCAT;
				$response["ADDRNO"]  = $row->ADDRNO;
				$response["ADDRDES"] = $row->ADDRDES;
				$response["ACTICOD"] = $row->ACTICOD;
				$response["ACTIDES"] = $row->ACTIDES;
			}
		}else{
			$response["RESVNO"]  = "";
			$response["LOCAT"]   = "";
			$response["CUSCOD"]  = "";
			$response["CUSNAME"] = "";
			$response["STRNO"]   = "";
			$response["CRLOCAT"] = "";
			$response["ADDRNO"]  = "";
			$response["ADDRDES"] = "";
			$response["ACTICOD"] = "";
			$response["ACTIDES"] = "";
		}
		echo json_encode($response);
	}
	function get_strPrice(){
		$STRNO  = $_REQUEST["STRNO"];
		$sql = "
			select 
				ISNULL(STDPRC,0) as STDPRC,ISNULL(NETCOST,0)+ISNULL(NADDCOST,0) as NCARCST
				,ISNULL(CRVAT,0)+ISNULL(VADDCOST,0) as VCARCST
				,ISNULL(TOTCOST,0)+ISNULL(TADDCOST,0) as TCARCST
			from {$this->MAuth->getdb('INVTRAN')}
			where STRNO = '".$STRNO."'
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$response["STDPRC"]  = number_format($row->STDPRC,2);
				$response["NCARCST"] = number_format($row->NCARCST,2);
				$response["VCARCST"] = number_format($row->VCARCST,2);
				$response["TCARCST"] = number_format($row->TCARCST,2);
				$response["DSCPRC"]  = "0.00";
			}
		}else{
			$response["STDPRC"]  = "";
			$response["NCARCST"] = "";
			$response["VCARCST"] = "";
			$response["TCARCST"] = "";
			$response["DSCPRC"]  = "";
		}
		echo json_encode($response);
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
	//ดึกบิลของแถมเหมือนกับเมนูขายผ่อนเช่าซื้อ
	function calbilldas(){
		$saleno = $_REQUEST['saleno'];
		$locat	= $_REQUEST['locat'];
		$size 	= sizeof($saleno);
		
		$sql = "
			select senior,free,spss from serviceweb.dbo.fn_branchMaps
			where senior='".$locat."'
		";
		$query = $this->db->query($sql);
		$row = $query->row();
		$locatDasFREE = $row->free;
		$locatDasSPS = $row->spss;
		
		$condFREE = "";
		$condSPS = "";
		for($i=0;$i<$size;$i++){
			
			if(substr($saleno[$i],0,1) == "F"){
				//if($condFREE != ""){ $condFREE .= ","; }
				$condFREE .= ",";
				$condFREE .= "'".substr($saleno[$i],1,strlen($saleno[$i]))."'";
			}else if(substr($saleno[$i],0,1) == "S"){
				//if($condSPS != ""){ $condSPS .= ","; }
				$condSPS .= ",";
				$condSPS .= "'".substr($saleno[$i],1,strlen($saleno[$i]))."'";
			}
		}
		
		$response = array();
		if($condFREE != '' || $condSPS != ''){
			$condFREE = " and SaleNo in (''".$condFREE.")";
			$condSPS = " and SaleNo in (''".$condSPS.")";
			
			$sql = "
				select sum(TotalAmt) as TotalAmt from (
					select Discount as TotalAmt from DBFREE.dbo.SPSale
					where 1=1 and BranchNo='".$locatDasFREE."' ".$condFREE."
					
					union all
					select Discount as TotalAmt from DBSPS.dbo.SPSale
					where 1=1 and BranchNo='".$locatDasSPS."' ".$condSPS."
				) as data
			";
			//echo $sql; exit;
			$DAS = $this->load->database('DAS',true);
			$query = $DAS->query($sql);
			$row = $query->row();
			
			$response["TotalAmt"] = number_format($row->TotalAmt,2);
			
			$sql = "
				select PartName+' '+cast(cast(SaleQTY as decimal(7,0)) as varchar)+' '+UM as item from DBFREE.dbo.SPSaleDetail
				where 1=1 and BranchNo='".$locatDasFREE."' ".$condFREE."
				
				union all
				select PartName+' '+cast(cast(SaleQTY as decimal(7,0)) as varchar)+' '+UM as item from DBSPS.dbo.SPSaleDetail
				where 1=1 and BranchNo='".$locatDasSPS."' ".$condSPS."
			";
			$query = $DAS->query($sql);
			
			$details = "";
			foreach($query->result() as $row){
				if($details != ""){ $details .= ","; }
				$details .= $row->item;
			}
			$response["Details"] = $details;
		}else{
			$response["TotalAmt"] = 0;
			$response["Details"]  = "";
		}
		
		echo json_encode($response);
	}
	function checkSave($arrs){
		$response = array();
		$msg = "";
		if($arrs["CUSCOD"]  == ""){ $msg = "รหัสลูกค้า"; }
		if($arrs["STRNO"]   == ""){ $msg = "เลขที่ตัวถัง"; }
		if($arrs["ACTICOD"] == ""){ $msg = "กิจกรรมการขาย"; }
		if($arrs["INPRC"]   == 0){ $msg = "ราคาขาย"; }
		if($arrs["INDWN"]   == 0){ $msg = "เงินดาวน์"; }
		if($arrs["FINCOD"]  == ""){ $msg = "รหัสบริษัทไฟแนนซ์"; }
		if($arrs["SALCOD"]  == ""){ $msg = "พนักงานขาย"; }
		
		if($msg != ""){
			$response["error"] = "N";
			$response["msg"]   = "กรุณาเลือกหรือระบุ{$msg}ก่อนครับ";
			echo json_encode($response); exit;
		}
	}
	function Save(){
		$arrs = array();
		$arrs["CONTNO"]   = $_REQUEST["CONTNO"];
		$arrs["LOCAT"]    = $_REQUEST["LOCAT"];
		$arrs["SDATE"]    = $this->Convertdate(1,$_REQUEST["SDATE"]);
		$arrs["RESVNO"]   = $_REQUEST["RESVNO"];
		$arrs["APPVNO"]   = $_REQUEST["APPVNO"];
		$arrs["CUSCOD"]   = $_REQUEST["CUSCOD"];
		$arrs["INCLVAT"]  = $_REQUEST["INCLVAT"];
		$arrs["VATRT"]    = $_REQUEST["VATRT"];
		$arrs["ADDRNO"]   = $_REQUEST["ADDRNO"];
		$arrs["STRNO"]    = $_REQUEST["STRNO"];
		$arrs["REGNO"]    = $_REQUEST["REGNO"];
		$arrs["ACTICOD"]  = $_REQUEST["ACTICOD"];
		
		$arrs["listopt"]  = $_REQUEST["listopt"]; //รายการอุปกรณ์เสริม
		
		//print_r($arrs["listopt"]); exit;
		
		$arrs["INPRC"]    = str_replace(",","",($_REQUEST["INPRC"]   == "" ? 0:$_REQUEST["INPRC"])); //ราคาคีย์ขาย
		$arrs["INDWN"]    = str_replace(",","",($_REQUEST["INDWN"]   == "" ? 0:$_REQUEST["INDWN"]));
		$arrs["STDPRC"]   = str_replace(",","",($_REQUEST["STDPRC"]  == "" ? 0:$_REQUEST["STDPRC"]));
		$arrs["DSCPRC"]   = str_replace(",","",($_REQUEST["DSCPRC"]  == "" ? 0:$_REQUEST["DSCPRC"]));
		
		$arrs["NCARCST"]  = str_replace(",","",(isset($_REQUEST["NCARCST"]) ? $_REQUEST["NCARCST"]:0));
		$arrs["VCARCST"]  = str_replace(",","",(isset($_REQUEST["VCARCST"]) ? $_REQUEST["VCARCST"]:0));
		$arrs["TCARCST"]  = str_replace(",","",(isset($_REQUEST["TCARCST"]) ? $_REQUEST["TCARCST"]:0));
		
		$arrs["FINCOD"]   = $_REQUEST["FINCOD"];
		$arrs["FINCOM"]   = str_replace(",","",($_REQUEST["FINCOM"] == "" ? 0:$_REQUEST["FINCOM"]));
		$arrs["SALCOD"]   = str_replace(",","",($_REQUEST["SALCOD"] == "" ? "":$_REQUEST["SALCOD"]));
		
		$arrs["COMITN"]   = str_replace(",","",number_format(($_REQUEST["COMITN"] == "" ? 0:$_REQUEST["COMITN"]),2));
		
		$arrs["TAXDT"]    = $this->Convertdate(1,$_REQUEST["TAXDT"]);
		$arrs["ISSUNO"]   = $_REQUEST["ISSUNO"];
		$arrs["ISSUDT"]   = $this->Convertdate(1,$_REQUEST["ISSUDT"]);
		$arrs["RECOMCODE"]= $_REQUEST["RECOMCODE"];
		$arrs["PAYDWN"]   = str_replace(",","",($_REQUEST["PAYDWN"]  == "" ? 0:$_REQUEST["PAYDWN"]));
		$arrs["PAYFIN"]   = str_replace(",","",($_REQUEST["PAYFIN"]  == "" ? 0:$_REQUEST["PAYFIN"]));
		
		$arrs["COMEXT"]   = str_replace(",","",($_REQUEST["COMEXT"]  == "" ? 0:$_REQUEST["COMEXT"]));
		$arrs["COMOPT"]   = str_replace(",","",($_REQUEST["COMOPT"]  == "" ? 0:$_REQUEST["COMOPT"]));
		$arrs["COMOTH"]   = str_replace(",","",($_REQUEST["COMOTH"]  == "" ? 0:$_REQUEST["COMOTH"]));
		$arrs["MEMO1"]    = $_REQUEST["MEMO1"];
		
		$this->checkSave($arrs); //exit;
		
		if($arrs["INCLVAT"] == "Y"){
			//ต้นทุนรวม
			$arrs["OPTCTOT"]  = str_replace(",","",($_REQUEST["OPTCTOT"]   == "" ? 0:$_REQUEST["OPTCTOT"]));
			$arrs["OPTCVT"]   = str_replace(",","",number_format($arrs["OPTCTOT"] - ($arrs["OPTCTOT"] / ((100 + $arrs["VATRT"]) / 100)),2));
			$arrs["OPTCST"]   = str_replace(",","",number_format($arrs["OPTCTOT"] / ((100 + $arrs["VATRT"]) / 100),2));
			//ราคาขายรวม
			$arrs["OPTPTOT"]  = str_replace(",","",($_REQUEST["OPTPTOT"]   == "" ? 0:$_REQUEST["OPTPTOT"]));
			$arrs["OPTPVT"]   = str_replace(",","",number_format($arrs["OPTPTOT"] - ($arrs["OPTPTOT"] / ((100 + $arrs["VATRT"]) / 100)),2));
			$arrs["OPTPRC"]   = str_replace(",","",number_format($arrs["OPTPTOT"] / ((100 + $arrs["VATRT"]) / 100),2));
			
			$arrs["KEYIN"]    = $arrs["INPRC"];
			$arrs["NKEYIN"]   = str_replace(",","",(number_format($arrs["INPRC"] / ((100 + $arrs["VATRT"]) / 100),2)));
			$arrs["VKEYIN"]   = str_replace(",","",number_format($arrs["INPRC"] - ($arrs["INPRC"] / ((100 + $arrs["VATRT"]) / 100)),2));
			
			$arrs["TKEYIN"]   = $arrs["INPRC"];
			$arrs["NPRICE"]	  = str_replace(",","",(number_format($arrs["INPRC"] / ((100 + $arrs["VATRT"]) / 100),2)));
			$arrs["VATPRC"]   = str_replace(",","",number_format($arrs["INPRC"] - ($arrs["INPRC"] / ((100 + $arrs["VATRT"]) / 100)),2));
			
			$arrs["TOTPRC"]   = $arrs["INPRC"];
			
			$arrs["KEYINDWN"] = $arrs["INDWN"];
			$arrs["NDAWN"]    = str_replace(",","",(number_format($arrs["INDWN"] / ((100 + $arrs["VATRT"]) / 100),2)));
			$arrs["VATDWN"]   = str_replace(",","",number_format($arrs["INDWN"] - ($arrs["INDWN"] / ((100 + $arrs["VATRT"]) / 100)),2));
			$arrs["TOTDWN"]   = $arrs["INDWN"];
			
			$arrs["NKANG"]    = str_replace(",","",(number_format($arrs["INPRC"] / ((100 + $arrs["VATRT"]) / 100),2)));
			$arrs["VKANG"]    = str_replace(",","",number_format($arrs["INPRC"] - ($arrs["INPRC"] / ((100 + $arrs["VATRT"]) / 100)),2));
			$arrs["TKANG"]    = $arrs["INPRC"];
			
			$arrs["TOTFIN"]   = str_replace(",","",number_format($arrs["INPRC"] - $arrs["INDWN"]));
			$arrs["NFINAN"]   = str_replace(",","",(number_format($arrs["TOTFIN"] / ((100 + $arrs["VATRT"]) / 100),2)));
			$arrs["VATFIN"]   = str_replace(",","",number_format($arrs["TOTFIN"] - ($arrs["TOTFIN"] / ((100 + $arrs["VATRT"]) / 100)),2));
			
			$arrs["NETAMT"]   = str_replace(",","",(number_format($arrs["INPRC"] / ((100 + $arrs["VATRT"]) / 100),2)));
			$arrs["VATAMT"]   = str_replace(",","",number_format($arrs["INPRC"] - ($arrs["INPRC"] / ((100 + $arrs["VATRT"]) / 100)),2));
			$arrs["TOTAMT"]   = $arrs["INPRC"];
		}else{
			$arrs["KEYIN"]    = str_replace(",","",$arrs["INPRC"]);
			$arrs["NKEYIN"]   = $arrs["KEYIN"];
			$arrs["VKEYIN"]   = str_replace(",","",number_format(($arrs["KEYIN"] * $arrs["VATRT"]) / 100,2));
			$arrs["TKEYIN"]   = str_replace(",","",number_format($arrs["KEYIN"] + $arrs["VKEYIN"],2));
			
			$arrs["NPRICE"]	  = str_replace(",","",$arrs["INPRC"]);
			$arrs["VATPRC"]   = str_replace(",","",number_format(($arrs["NPRICE"] * $arrs["VATRT"]) / 100,2));
			$arrs["TOTPRC"]   = str_replace(",","",number_format($arrs["NPRICE"] + $arrs["VATPRC"],2));
		
			$arrs["KEYINDWN"] = $arrs["INDWN"];
			$arrs["NDAWN"]    = $arrs["KEYINDWN"];
			$arrs["VATDWN"]   = str_replace(",","",number_format(($arrs["NDAWN"] * $arrs["VATRT"]) / 100,2));
			$arrs["TOTDWN"]   = str_replace(",","",number_format($arrs["NDAWN"] + $arrs["VATDWN"],2));
			
			$arrs["NKANG"]    = str_replace(",","",$arrs["INPRC"]);
			$arrs["VKANG"]    = str_replace(",","",number_format(($arrs["NKANG"] * $arrs["VATRT"]) / 100,2));
			$arrs["TKANG"]    = str_replace(",","",number_format($arrs["NKANG"] + $arrs["VATPRC"],2));
			
			$arrs["TOTFIN"]   = str_replace(",","",number_format($arrs["INPRC"] - $arrs["INDWN"]));
			$arrs["VATFIN"]   = str_replace(",","",number_format(($arrs["TOTFIN"] * $arrs["VATRT"]) / 100,2));
			$arrs["NFINAN"]   = str_replace(",","",(number_format($arrs["TOTFIN"] + $arrs["VATFIN"],2)));
			
			
			$arrs["NETAMT"]   = $arrs["INPRC"];
			$arrs["VATAMT"]   = str_replace(",","",number_format(($arrs["INPRC"] * $arrs["VATRT"]) / 100,2));
			$arrs["TOTAMT"]   = str_replace(",","",(number_format($arrs["NETAMT"] + $arrs["VATAMT"],2)));
			
			//ต้นทุนรวม
			$arrs["OPTCTOT"]  = str_replace(",","",$_REQUEST["OPTCTOT"]);
			$arrs["OPTCVT"]   = str_replace(",","",number_format(($arrs["OPTCTOT"] * $arrs["VATRT"]) / 100,2));
			$arrs["OPTCST"]   = str_replace(",","",number_format($arrs["OPTCTOT"] + $arrs["OPTCVT"],2));
			
			//ราคาขายรวม
			$arrs["OPTPTOT"]  = str_replace(",","",($_REQUEST["OPTPTOT"]   == "" ? 0:$_REQUEST["OPTPTOT"]));
			$arrs["OPTPVT"]   = str_replace(",","",number_format(($arrs["OPTPTOT"] * $arrs["VATRT"]) / 100,2));
			$arrs["OPTPRC"]   = str_replace(",","",number_format($arrs["OPTPTOT"] + $arrs["OPTPVT"],2));
		}
		
		$sql = "
			select SNAM,NAME1,NAME2 from {$this->MAuth->getdb('CUSTMAST')}
			where CUSCOD = '".$arrs["CUSCOD"]."'
		";
		$query = $this->db->query($sql);
		$row   = $query->row();
		$arrs["SNAM"]  = $row->SNAM;
		$arrs["NAME1"] = $row->NAME1;
		$arrs["NAME2"] = $row->NAME2;
		
		$arrs["optdel"] = "";
		
		$insertopt = ""; $updateopt = ""; $arrs['trigger_update'] = "";
		if($arrs["listopt"] != "noopt"){
			$countlist = sizeof($arrs["listopt"]);
			for($i=0;$i<$countlist;$i++){
				$insertopt .="
					insert into {$this->MAuth->getdb('ARINOPT')}(
						[TSALE],[CONTNO],[LOCAT]
						
						,[OPTCODE],[UPRICE],[UCOST],[QTY],[TOTPRC]
						,[TOTVAT],[NPRICE],[OPTCST],[OPTCVT],[OPTCTOT]
						
						,[CONFIR],[USERID]
						,[INPDT],[POSTDT],[SDATE],[RTNFLAG]
					)values(
						'F',@CONTNO,'".$arrs["LOCAT"]."','".$arrs["listopt"][$i][0]."',".$arrs["listopt"][$i][3]."
						,0,".$arrs["listopt"][$i][2].",".$arrs["listopt"][$i][6].",".$arrs["listopt"][$i][5]."
						,".$arrs["listopt"][$i][4].",".$arrs["listopt"][$i][9].",".$arrs["listopt"][$i][8]."
						,".$arrs["listopt"][$i][7].",null,'{$this->sess['USERID']}',getdate(),null,'".$arrs["SDATE"]."'
						,null
					)
					update {$this->MAuth->getdb('OPTMAST')} 
					set ONHAND = ONHAND - ".$arrs["listopt"][$i][2]." 
					where LOCAT = '".$arrs['LOCAT']."' and OPTCODE = '".$arrs["listopt"][$i][0]."'
				";
				$updateopt .="
					if not exists(
						select * from {$this->MAuth->getdb('ARINOPT')}
						where CONTNO = '".$arrs['CONTNO']."' and LOCAT = '".$arrs['LOCAT']."'
						and OPTCODE = '".$arrs['listopt'][$i][0]."'
					)
					begin
						insert into {$this->MAuth->getdb('ARINOPT')}(
							[TSALE],[CONTNO],[LOCAT]
							
							,[OPTCODE],[UPRICE],[UCOST],[QTY],[TOTPRC]
							,[TOTVAT],[NPRICE],[OPTCST],[OPTCVT],[OPTCTOT]
							
							,[CONFIR],[USERID]
							,[INPDT],[POSTDT],[SDATE],[RTNFLAG]
						)values(
							'F',@CONTNO,'".$arrs["LOCAT"]."','".$arrs["listopt"][$i][0]."',".$arrs["listopt"][$i][3]."
							,0,".$arrs["listopt"][$i][2].",".$arrs["listopt"][$i][6].",".$arrs["listopt"][$i][5]."
							,".$arrs["listopt"][$i][4].",".$arrs["listopt"][$i][9].",".$arrs["listopt"][$i][8]."
							,".$arrs["listopt"][$i][7].",null,'{$this->sess['USERID']}',getdate(),null,'".$arrs["SDATE"]."'
							,null
						)
						
						update {$this->MAuth->getdb('OPTMAST')} 
						set ONHAND = ONHAND - ".$arrs["listopt"][$i][2]." 
						where LOCAT = '".$arrs['LOCAT']."' and OPTCODE = '".$arrs["listopt"][$i][0]."'
					end
				";
				if($arrs["optdel"] != ""){
					$arrs["optdel"] .="','";
				}
				$arrs["optdel"] .= $arrs["listopt"][$i][0];
			}
			//echo $arrs["optdel"]; exit;
			
			$sql = "
				select OPTCODE,QTY from {$this->MAuth->getdb('ARINOPT')} 
				where CONTNO = '".$arrs['CONTNO']."' and LOCAT = '".$arrs['LOCAT']."' 
				and OPTCODE not in('".$arrs['optdel']."')
			";
			$query = $this->db->query($sql); 
			if($query->row()){
				foreach($query->result() as $row){
					$arrs['trigger_update'] .= "
						--trigger_update
						update {$this->MAuth->getdb('OPTMAST')} set ONHAND = ONHAND + ".$row->QTY." 
						where LOCAT = '".$arrs['LOCAT']."' and OPTCODE = '".$row->OPTCODE."'
					";
				}
			}
			//echo $insertopt; exit;
		}
		if($arrs["CONTNO"] == "Auto Genarate"){
			$sql = "
				if OBJECT_ID('tempdb..#tempsavefinance') is not null drop table #tempsavefinance;
				create table #tempsavefinance (id varchar(1),msg varchar(max));
				begin tran savefinance
				begin try
					begin
						--เลขที่สัญญา
						declare @h_fincno varchar(10) = (select H_FINCNO from {$this->MAuth->getdb('CONDPAY')});
						declare @cno varchar(10) = (
							select SHORTL+@h_fincno+'-'+right(left(convert(varchar(8),'".$arrs["SDATE"]."',112),6),4) 
							from {$this->MAuth->getdb('INVLOCAT')} where LOCATCD = '".$arrs["LOCAT"]."'
						);
						declare @CONTNO varchar(12) = isnull((
							select MAX(CONTNO) from {$this->MAuth->getdb('ARFINC')}
							where CONTNO like ''+@cno+'%' collate thai_cs_as)
							,@cno+'0000'
						);
						set @CONTNO = left(@CONTNO,8)+right(right(@CONTNO,4)+10001,4);
						
						--เลขที่ใบกำกับภาษี
						declare @h_txfinc varchar(10) = (select H_TXFINC from {$this->MAuth->getdb('CONDPAY')});
						declare @tno varchar(10) = (
							select SHORTL+@h_txfinc+'-'+right(left(convert(varchar(8),'".$arrs["SDATE"]."',112),6),4) 
							from {$this->MAuth->getdb('INVLOCAT')} where LOCATCD = '".$arrs["LOCAT"]."'
						);
						declare @TAXNO varchar(12) = isnull((
							select MAX(TAXNO) from {$this->MAuth->getdb('TAXTRAN')}
							where TAXNO like ''+@tno+'%' collate thai_cs_as)
							,@tno+'0000'
						);
						set @TAXNO = left(@TAXNO,8)+right(right(@TAXNO,4)+10001,4);
						
						
						declare @recvno int = (
							select COUNT(*) from {$this->MAuth->getdb('INVTRAN')} 
							where STRNO = '".$arrs["STRNO"]."'
						);
						
						if (@recvno = 1)
						begin
							insert into {$this->MAuth->getdb('ARFINC')} (
								[CONTNO],[LOCAT],[RESVNO],[CUSCOD],[STRNO],[INCLVAT],[VATRT],[STDPRC],[DSCPRC],[SDATE]
								,[KEYIN],[NKEYIN],[VKEYIN],[TKEYIN],[NPRICE],[VATPRC],[TOTPRC],[NPAYRES],[VATPRES]
								,[TOTPRES],[KEYINDWN],[NDAWN],[VATDWN],[TOTDWN],[NKANG],[VKANG],[TKANG],[NFINAN]
								,[VATFIN],[TOTFIN],[PAYDWN],[PAYFIN],[SMPAY],[SMCHQ],[TAXNO],[TAXDT],[TAXCRD]
								,[CRDTXNO],[CRDAMT],[NCARCST],[VCARCST],[TCARCST],[OPTCST],[OPTCVT],[OPTCTOT],[OPTPRC]
								,[OPTPVT],[OPTPTOT],[FINCOM],[FINCOD],[SALCOD],[COMITN],[LPAYD],[LPAYA],[ISSUNO]
								,[ISSUDT],[TSALE],[CONFIR],[CONFIRID],[CONFIRDT],[MEMO1],[USERID],[INPDT],[DELID]
								,[DELDT],[POSTDT],[FLCANCL],[APPVNO],[PAYTYP],[ADDRNO],[COMEXT],[COMOPT],[COMOTH]
								,[RECOMCOD],[ACTICOD]
							)values(
								@CONTNO,'".$arrs["LOCAT"]."','".$arrs["RESVNO"]."','".$arrs["CUSCOD"]."','".$arrs["STRNO"]."'
								,'".$arrs["INCLVAT"]."',".$arrs["VATRT"].",".$arrs["STDPRC"].",".$arrs["DSCPRC"].",'".$arrs["SDATE"]."'
								,".$arrs["KEYIN"].",".$arrs["NKEYIN"].",".$arrs["VKEYIN"].",".$arrs["TKEYIN"].",".$arrs["NPRICE"]."
								,".$arrs["VATPRC"].",".$arrs["TOTPRC"].",0,0,0,".$arrs["KEYINDWN"].",".$arrs["NDAWN"]."
								,".$arrs["VATDWN"].",".$arrs["TOTDWN"].",".$arrs["NKANG"].",".$arrs["VKANG"].",".$arrs["TKANG"]."
								,".$arrs["NFINAN"].",".$arrs["VATFIN"].",".$arrs["TOTFIN"].",0,0,0,0,@TAXNO,'".$arrs["TAXDT"]."'
								,'','',0,".$arrs["NCARCST"].",".$arrs["VCARCST"].",".$arrs["TCARCST"].",".$arrs["OPTCST"].",".$arrs["OPTCVT"]."
								,".$arrs["OPTCTOT"].",".$arrs["OPTPRC"].",".$arrs["OPTPVT"].",".$arrs["OPTPTOT"].",".$arrs["FINCOM"]."
								,'".$arrs["FINCOD"]."','".$arrs["SALCOD"]."',".$arrs["COMITN"].",null,0,'".$arrs["ISSUNO"]."'
								,'".$arrs["ISSUDT"]."','F','','',null,'".$arrs["MEMO1"]."','{$this->sess["USERID"]}',getdate(),''
								,null,null,'','".$arrs["APPVNO"]."','','".$arrs["ADDRNO"]."',".$arrs["COMEXT"].",".$arrs["COMOPT"]."
								,".$arrs["COMOTH"].",'".$arrs["RECOMCODE"]."','".$arrs["ACTICOD"]."'
							)
							
							update {$this->MAuth->getdb('INVTRAN')} 
								set SDATE = '".$arrs["SDATE"]."',PRICE = ".$arrs["INPRC"]."
								,TSALE = 'F',CONTNO = @CONTNO,FLAG = 'C'
							where STRNO = '".$arrs["STRNO"]."'
							
							insert into {$this->MAuth->getdb('TAXTRAN')} (
								[LOCAT],[TAXNO],[TAXDT],[TSALE],[CONTNO],[CUSCOD],[SNAM],[NAME1],[NAME2]
								,[STRNO],[REFNO],[REFDT],[VATRT],[NETAMT],[VATAMT],[TOTAMT],[DESCP]
								,[FPAR],[FPAY],[LPAR],[LPAY],[INPDT],[FLAG],[CANDT],[TAXTYP],[TAXFLG]
								,[USERID],[FLCANCL],[TMBILL],[RTNSTK],[FINCOD],[DOSTAX],[PAYFOR]
								,[RESONCD],[INPTIME]
							)values(
								'".$arrs["LOCAT"]."',@TAXNO,'".$arrs["TAXDT"]."','F',@CONTNO,'".$arrs["CUSCOD"]."'
								,'".$arrs["SNAM"]."','".$arrs["NAME1"]."','".$arrs["NAME2"]."','".$arrs["STRNO"]."'
								,null,null,".$arrs["VATRT"].",".$arrs["NETAMT"].",".$arrs["VATAMT"].",".$arrs["TOTAMT"]."
								,'ใบกำกับภาษีขายส่งไฟแนนซ์',null,0,null,0,getdate(),null,null,'S','N','".$this->sess["USERID"]."'
								,null,null,null,'".$arrs["FINCOD"]."',null,null,null,null
							)
							
							ALTER TABLE {$this->MAuth->getdb('ARINOPT')} DISABLE TRIGGER AFTINS_ARINOPT
							".$insertopt."
							ALTER TABLE {$this->MAuth->getdb('ARINOPT')} ENABLE TRIGGER AFTINS_ARINOPT
							
							insert into {$this->MAuth->getdb('hp_UserOperationLog')} (
								userId,descriptions,postReq,dateTimeTried,ipAddress,functionName
							)values (
								'".$this->sess["IDNo"]."','SYS04::บันทึกขายส่งไฟแนนซ์'
								,@CONTNO+' ".str_replace("'","",var_export($_REQUEST, true))."'
								,getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."'
							);
							
							insert into #tempsavefinance select 'Y' as id,'สำเร็จ บันทึกข้อมูลรายการขายส่งไฟแนนซ์เลขที่สัญญา :: '+@CONTNO+' เรียบร้อยแล้ว' as msg;
							commit tran savefinance;	
						end
						else
						begin
							rollback tran savefinance;
							insert into #tempsavefinance select 'E' as id,'บันทึกข้อมูลไม่สำเร็จ : กรุณาตรวจสอบข้อมูลอีกครั้ง' as msg;
							return;
						end
					end
				end try
				begin catch
					rollback tran savefinance;
					insert into #tempsavefinance select 'E' as id,'บันทึกข้อมูลไม่สำเร็จ : กรุณาติดต่อฝ่ายไอที' as msg;
					return;
				end catch
			";
		}else{
			$sql = "
				if OBJECT_ID('tempdb..#tempsavefinance') is not null drop table #tempsavefinance;
				create table #tempsavefinance (id varchar(1),msg varchar(max));
				begin tran savefinance
				begin try
					begin
						declare @invtran int = (
							select COUNT(*) from {$this->MAuth->getdb('INVTRAN')} 
							where STRNO = '".$arrs["STRNO"]."'
						);
						declare @arfinc int = (
							select COUNT(*) from {$this->MAuth->getdb('ARFINC')} 
							where STRNO = '".$arrs["STRNO"]."' and CONTNO = '".$arrs["CONTNO"]."'
						);
						declare @CONTNO varchar(20) = '".$arrs["CONTNO"]."'
						
						if (@invtran = 1 and @arfinc = 1)
						begin
							update {$this->MAuth->getdb('ARFINC')}
							set ADDRNO = '".$arrs["ADDRNO"]."'
								,ACTICOD = '".$arrs["ACTICOD"]."',OPTCST = ".$arrs["OPTCST"]."
								,OPTCVT = ".$arrs["OPTCVT"].",OPTCTOT = ".$arrs["OPTCTOT"]."
								,OPTPRC = ".$arrs["OPTPRC"].",OPTPVT = ".$arrs["OPTPVT"]."
								,OPTPTOT = ".$arrs["OPTPTOT"].",STDPRC = ".$arrs["STDPRC"]."
								,DSCPRC = ".$arrs["DSCPRC"].",FINCOD = '".$arrs["FINCOD"]."'
								,FINCOM = ".$arrs["FINCOM"].",SALCOD = '".$arrs["SALCOD"]."'
								,COMITN = ".$arrs["COMITN"].",ISSUNO = '".$arrs["ISSUNO"]."'
								,ISSUDT = '".$arrs["ISSUDT"]."',COMEXT = ".$arrs["COMEXT"]."
								,COMOPT = ".$arrs["COMOPT"].",COMOTH = ".$arrs["COMOTH"]."
								,MEMO1 = '".$arrs["MEMO1"]."'
							where CONTNO = '".$arrs["CONTNO"]."' and STRNO = '".$arrs["STRNO"]."'
							
							ALTER TABLE {$this->MAuth->getdb('ARINOPT')} DISABLE TRIGGER AFTINS_ARINOPT
							".$updateopt."
							ALTER TABLE {$this->MAuth->getdb('ARINOPT')} ENABLE TRIGGER AFTINS_ARINOPT
							
							ALTER TABLE {$this->MAuth->getdb('ARINOPT')} DISABLE TRIGGER AFTDEL_ARINOPT
							--trigger_delopt
							".$arrs['trigger_update']."
							
							delete from {$this->MAuth->getdb('ARINOPT')} 
							where CONTNO = '".$arrs['CONTNO']."' and LOCAT = '".$arrs['LOCAT']."'
							and OPTCODE not in('".$arrs['optdel']."')
							ALTER TABLE {$this->MAuth->getdb('ARINOPT')} ENABLE TRIGGER AFTDEL_ARINOPT
							
							insert into {$this->MAuth->getdb('hp_UserOperationLog')} (
								userId,descriptions,postReq,dateTimeTried,ipAddress,functionName
							)values (
								'".$this->sess["IDNo"]."','SYS04::แก้ไขขายส่งไฟแนนซ์'
								,@CONTNO+' ".str_replace("'","",var_export($_REQUEST, true))."'
								,getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."'
							);
							
							insert into #tempsavefinance select 'Y' as id,'สำเร็จ แก้ไขข้อมูลรายการขายส่งไฟแนนซ์เลขที่สัญญา :: '+@CONTNO+' เรียบร้อยแล้ว' as msg;
							commit tran savefinance;	
						end
						else
						begin
							rollback tran savefinance;
							insert into #tempsavefinance select 'E' as id,'บันทึกข้อมูลไม่สำเร็จ : กรุณาตรวจสอบข้อมูลอีกครั้ง' as msg;
							return;
						end
					end
				end try
				begin catch
					rollback tran savefinance;
					insert into #tempsavefinance select 'E' as id,'บันทึกข้อมูลไม่สำเร็จ : กรุณาติดต่อฝ่ายไอที' as msg;
					return;
				end catch
			";
		}
		//echo $sql; exit;
		$this->db->query($sql);
		
		$sql = "select * from #tempsavefinance";
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
	function fnDel(){
		$arrs = array();
		$arrs["CONTNO"]  = $_REQUEST["CONTNO"];
		$arrs["LOCAT"]   = $_REQUEST["LOCAT"];
		$listopt         = $_REQUEST["listopt"];		
		$arrs['trigger_deloptmast'] = "";
		
		for($i=0;$i<sizeof($listopt);$i++){
			$arrs['trigger_deloptmast'] .= "
				update {$this->MAuth->getdb('OPTMAST')} set ONHAND = ONHAND + ".$listopt[$i][2]." 
				where LOCAT = '".$arrs['LOCAT']."' and OPTCODE = '".$listopt[$i][0]."'
			";
		}
		$sql = "
			if OBJECT_ID('tempdb..#tempdelfinance') is not null drop table #tempdelfinance;
			create table #tempdelfinance (id varchar(1),msg varchar(max));
			begin tran delfinance
			begin try
				begin
					declare @invtran int = (
						select COUNT(*) from {$this->MAuth->getdb('INVTRAN')} 
						where CONTNO = '".$arrs["CONTNO"]."'
					);
					declare @arfinc int = (
						select COUNT(*) from {$this->MAuth->getdb('ARFINC')} 
						where CONTNO = '".$arrs["CONTNO"]."'
					);
					declare @taxtran int = (
						select COUNT(*) from {$this->MAuth->getdb('TAXTRAN')} 
						where CONTNO = '".$arrs["CONTNO"]."'
					);
					declare @CONTNO varchar(20) = '".$arrs["CONTNO"]."'
					
					if (@invtran = 1 and @arfinc = 1 and @taxtran = 1)
					begin
						ALTER TABLE {$this->MAuth->getdb('ARINOPT')} DISABLE TRIGGER AFTDEL_ARINOPT
						
						insert into {$this->MAuth->getdb('CANFINC')} (LOCAT,CONTNO,RESVNO,CUSCOD,STRNO,INCLVAT,
							SDATE, VATRT,STDPRC,DSCPRC,KEYIN,TOTPRC,TOTPRES,SMPAY,SMCHQ,
							TAXDT,TAXNO,SALCOD,TSALE,USERID,INPDT,DELID,DELDT,POSTDT)
						select O.LOCAT,O.CONTNO,O.RESVNO,O.CUSCOD,O.STRNO,O.INCLVAT,
							O.SDATE,O.VATRT,O.STDPRC,O.DSCPRC,O.KEYIN,O.TOTPRC,O.TOTPRES,
							O.SMPAY,O.SMCHQ,O.TAXDT,O.TAXNO,O.SALCOD,O.TSALE,O.USERID,
							O.INPDT,O.DELID,O.DELDT,O.POSTDT 
						from {$this->MAuth->getdb('ARFINC')} O
						where CONTNO = @CONTNO
						
						delete from {$this->MAuth->getdb('ARFINC')} where CONTNO = @CONTNO
						
						delete from {$this->MAuth->getdb('ARINOPT')} where CONTNO = @CONTNO
						
						update {$this->MAuth->getdb('TAXTRAN')} 
						set FLAG = 'C'
							,CANDT = getdate(),FLCANCL = '".$this->sess["USERID"]."'
						where CONTNO = @CONTNO
						
						update {$this->MAuth->getdb('INVTRAN')} 
						set SDATE = null, 
							PRICE = 0,TSALE = '',CONTNO = '',FLAG = 'D'
						where CONTNO = @CONTNO	
						
						".$arrs['trigger_deloptmast']."
						
						insert into {$this->MAuth->getdb('hp_UserOperationLog')} (
							userId,descriptions,postReq,dateTimeTried,ipAddress,functionName
						)values (
							'".$this->sess["IDNo"]."','SYS04::ลบขายส่งไฟแนนซ์'
							,@CONTNO+' ".str_replace("'","",var_export($_REQUEST, true))."'
							,getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."'
						);
						
						ALTER TABLE {$this->MAuth->getdb('ARINOPT')} ENABLE TRIGGER AFTDEL_ARINOPT
						
						insert into #tempdelfinance select 'Y' as id,'สำเร็จ ลบข้อมูลรายการขายส่งไฟแนนซ์เลขที่สัญญา :: '+@CONTNO+' เรียบร้อยแล้ว' as msg;
						commit tran delfinance;	
					end
					else
					begin
						rollback tran delfinance;
						insert into #tempdelfinance select 'E' as id,'บันทึกข้อมูลไม่สำเร็จ : กรุณาตรวจสอบข้อมูลอีกครั้ง' as msg;
						return;
					end
				end
			end try
			begin catch
				rollback tran delfinance;
				insert into #tempdelfinance select 'E' as id,'บันทึกข้อมูลไม่สำเร็จ : กรุณาติดต่อฝ่ายไอที' as msg;
				return;
			end catch
		";
		//echo $sql; exit;
		$this->db->query($sql);
		
		$sql = "select * from #tempdelfinance";
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
	function conditiontopdf(){
		$data = array();
		if($_REQUEST["param"] == "TAX" or $_REQUEST["param"] == "SELL"){
			$data[] = urlencode(
				$_REQUEST["contno"].'||'.$_REQUEST["locat"]
			);
		}else{
			$data[] = urlencode(
				$_REQUEST["contno"].'||'.$_REQUEST["locat"].'||'.$_REQUEST["tfrom"].'||'.$_REQUEST["memo"]
			);
		}
		echo json_encode($this->generateData($data,"encode"));
	}
	function pdftax(){
		$data = array();
		$data[] = $_REQUEST["condpdf"];
		$arrs   = $this->generateData($data,"decode");
		$arrs[0]= urldecode($arrs[0]);
		//print_r($arrs);
		
		$tx = explode('||',$arrs[0]);
		$contno  = $tx[0];
		$locat   = $tx[1];
		
		$sql = "
			select 
				a.TAXNO,a.CONTNO,CONVERT(varchar(10),a.SDATE,121) as SDATE
				,f.FINNAME,f.FINADDR1,f.FINADDR2,'('+l.LOCATCD+') '+l.LOCATNM as LOCATNM,t.TYPE,t.MODEL,t.COLOR,t.STRNO,t.ENGNO
				,a.NPRICE,a.VATRT,a.VKEYIN,a.KEYIN,a.MEMO1
			from {$this->MAuth->getdb('ARFINC')} a
			left join {$this->MAuth->getdb('FINMAST')} f on a.FINCOD = f.FINCODE
			left join {$this->MAuth->getdb('INVLOCAT')} l on a.LOCAT = l.LOCATCD
			left join {$this->MAuth->getdb('INVTRAN')} t on a.STRNO = t.STRNO 
			where a.CONTNO = '".$contno."' and a.LOCAT = '".$locat."'
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$data[0] = $row->CONTNO;
				$data[1] = $this->DateThai($row->SDATE);
				$data[2] = $row->FINNAME;
				$data[3] = $row->FINADDR1;
				$data[4] = $row->FINADDR2;
				$data[5] = $row->LOCATNM;
				$data[6] = $row->TYPE;
				$data[7] = $row->MODEL;
				$data[8] = $row->COLOR;
				$data[9]  = $row->STRNO;
				$data[10] = $row->ENGNO;
				$data[11] = number_format($row->VATRT,2);
				$data[12] = number_format($row->NPRICE,2);
				$data[13] = number_format($row->VKEYIN,2);
				$data[14] = number_format($row->KEYIN,2);
				$data[15] = $row->MEMO1;
				$data[16] = $row->TAXNO;
			}
		}
		$mpdf = new \Mpdf\Mpdf([
			'mode' => 'utf-8', 
			'format' => 'A4-L',
			'margin_top' => 0, 	//default = 16
			'margin_left' => 15, 	//default = 15
			'margin_right' => 15, 	//default = 15
			'margin_bottom' => 16, 	//default = 16
			'margin_header' => 9, 	//default = 9
			'margin_footer' => 9, 	//default = 9
		]);
		
		$content = "
			<div class='wf pf data' style='top:45;left:520;width:200px;font-size:12pt;text-align:right;'><b>เลขที่ใบกำกับภาษี :</b></div>
			<div class='wf pf data' style='top:45;left:750;font-size:12pt;'>{$data[16]}</div>
			<div class='wf pf data' style='top:90;left:520;width:200px;font-size:12pt;text-align:right;'><b>วันที่ทำสัญญา :</b></div>
			<div class='wf pf data' style='top:90;left:750;font-size:12pt;'>{$data[1]}</div>
			
			<div class='wf pf data' style='top:140;left:520;width:200px;font-size:12pt;text-align:right;'><b>วันที่ทำสัญญา :</b></div>
			<div class='wf pf data' style='top:140;left:750;font-size:12pt;'>{$data[0]}</div>
			
			<div class='wf pf data' style='top:200;left:100;font-size:12pt;'>{$data[2]}</div>
			<div class='wf pf data' style='top:250;left:100;font-size:12pt;'>{$data[3]}</div>
			<div class='wf pf data' style='top:300;left:100;font-size:12pt;'>{$data[4]}</div>
			
			<div class='wf pf data' style='top:350;left:100;font-size:12pt;'><b>สาขา :<b></div>
			<div class='wf pf data' style='top:350;left:150;font-size:12pt;'>{$data[5]}</div>
			
			<div class='wf pf data' style='top:400;left:100;width:50px;font-size:12pt;text-align:left;'><b>ยี่ห้อ :<b></div>
			<div class='wf pf data' style='top:400;left:200;width:100px;font-size:12pt;text-align:left;'>{$data[6]}</div>
			<div class='wf pf data' style='top:400;left:400;width:50px;font-size:12pt;text-align:right;'><b>รุ่น :<b></div>
			<div class='wf pf data' style='top:400;left:500;width:100px;font-size:12pt;text-align:left;'>{$data[7]}</div>
			<div class='wf pf data' style='top:400;left:700;width:50px;font-size:12pt;text-align:right;'><b>สี :<b></div>
			<div class='wf pf data' style='top:400;left:800;width:100px;font-size:12pt;text-align:left;'>{$data[8]}</div>
			
			<div class='wf pf data' style='top:450;left:100;width:60px;font-size:12pt;text-align:left;'><b>เลขถัง :<b></div>
			<div class='wf pf data' style='top:450;left:200;width:300px;font-size:12pt;text-align:left;'>{$data[9]}</div>
			<div class='wf pf data' style='top:450;left:400;width:120px;font-size:12pt;text-align:right;'><b>เลขเครื่อง :<b></div>
			<div class='wf pf data' style='top:450;left:550;width:320px;font-size:12pt;text-align:left;'>{$data[10]}</div>
			
			<div class='wf pf data' style='top:500;left:100;width:100px;font-size:12pt;text-align:left;'><b>อัตราภาษี :<b></div>
			<div class='wf pf data' style='top:500;left:250;width:300px;font-size:12pt;text-align:left;'>{$data[11]}</div>
			<div class='wf pf data' style='top:500;left:400;width:120px;font-size:12pt;text-align:right;'><b>หมายเหตุ :<b></div>
			<div class='wf pf data' style='top:500;left:550;width:320px;font-size:12pt;text-align:left;'>{$data[15]}</div>
			
			<div class='wf pf data' style='top:600;left:400;width:300px;font-size:12pt;text-align:right;'><b>ราคาสินค้าสุทธิ :<b></div>
			<div class='wf pf data' style='top:600;left:700;width:200px;font-size:12pt;text-align:right;'>{$data[12]} บาท</div>
			
			<div class='wf pf data' style='top:650;left:400;width:300px;font-size:12pt;text-align:right;'><b>จำนวนภาษี :<b></div>
			<div class='wf pf data' style='top:650;left:700;width:200px;font-size:12pt;text-align:right;'>{$data[13]} บาท</div>
			
			<div class='wf pf data' style='top:700;left:100;width:300px;font-size:12pt;text-align:left;'><b>(".$this->ConvertText($data[14]).")<b></div>
			<div class='wf pf data' style='top:700;left:400;width:300px;font-size:12pt;text-align:right;'><b>ราคาขายรวมภาษี :<b></div>
			<div class='wf pf data' style='top:700;left:700;width:200px;font-size:12pt;text-align:right;'>{$data[14]} บาท</div>
		";
		
		$other = "";
		$stylesheet = "
			<style>
				body { font-family: garuda;font-size:10pt; }
				.wf { width:100%; }
				.h10 { height:10px; }
				.tc { text-align:center; }
				.pf { position:fixed; }
				.bor { border:0.1px solid black; }
				.bor2 { border:0.1px dotted black; }
				.data { background-color:#fff;font-size:9pt; }
			</style>
		";
		
		$mpdf->WriteHTML($content.$other.$stylesheet);
		$mpdf->SetHTMLFooter("<div class='wf pf' style='top:1060;left:0;font-size:6pt;width:720px;text-align:right;'>{$this->sess["name"]} ออกเอกสาร ณ วันที่ ".date('d/m/').(date('Y')+543)." ".date('H:i')."</div>");
		$mpdf->fontdata['qanela'] = array('R' => "QanelasSoft-Regular.ttf",'B' => "QanelasSoft-Bold.ttf",); //แก้ปริ้นแล้วอ่านไม่ออก
		$mpdf->Output();
	}
	function pdfsend(){
		$data = array();
		$data[] = $_REQUEST["condpdf"];
		$arrs   = $this->generateData($data,"decode");
		$arrs[0]= urldecode($arrs[0]);
		//print_r($arrs);
		
		$tx = explode('||',$arrs[0]);
		$contno  = $tx[0];
		$locat   = $tx[1];
		$tfrom   = $tx[2];
		$memo    = $tx[3];
		
		$sql = "
			select (select COMP_NM from {$this->MAuth->getdb('CONDPAY')}) as COMP_NM
				,(select COMP_ADR1+' '+COMP_ADR2+' '+TELP from {$this->MAuth->getdb('CONDPAY')}) as COMP_ADDR 
				,l.LOCATNM,a.CONTNO,convert(varchar(8),a.ISSUDT,112) as ISSUDT
				,f.FINNAME,f.FINADDR1,f.FINADDR2,a.TAXNO
				,a.NPRICE,a.VKEYIN,a.KEYIN
			from {$this->MAuth->getdb('ARFINC')} a
			left join {$this->MAuth->getdb('FINMAST')} f on a.FINCOD = f.FINCODE
			left join {$this->MAuth->getdb('INVLOCAT')} l on a.LOCAT = l.LOCATCD
			where a.CONTNO = '".$contno."' and a.LOCAT = '".$locat."'
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$data[0] = $row->COMP_NM;
				$data[1] = $row->COMP_ADDR;
				$data[2] = $row->LOCATNM;
				$data[3] = $row->CONTNO;
				$data[4] = $this->Convertdate(2,$row->ISSUDT);
				$data[5] = $row->FINNAME;
				$data[6] = $row->FINADDR1;
				$data[7] = $row->FINADDR2;
				$data[8] = $row->TAXNO;
				$data[9]  = number_format($row->NPRICE,2);
				$data[10] = number_format($row->VKEYIN,2);
				$data[11] = number_format($row->KEYIN,2);
			}
		}
		$sql = "
			select 
				t.TYPE,t.MODEL,t.COLOR,t.STRNO,COUNT(*) as COUNTCONT,a.TOTPRC 
			from {$this->MAuth->getdb('ARFINC')} a
			left join {$this->MAuth->getdb('INVTRAN')} t on a.CONTNO = t.CONTNO
			where a.CONTNO = '".$contno."' and a.LOCAT = '".$locat."'
			group by t.TYPE,t.MODEL,t.COLOR,t.STRNO,a.TOTPRC
		";
		$query = $this->db->query($sql);
		$listcar = ""; $i = 0;
		if($query->row()){
			foreach($query->result() as $row){$i++;
				$listcar .="
					<tr>
						<td style='width:6%;text-align:left;'>".$i."</td>
						<td style='width:15%;text-align:left;'>".$row->TYPE."</td>
						<td style='width:15%;text-align:left;'>".$row->MODEL."</td>
						<td style='width:15%;text-align:left;'>".$row->COLOR."</td>
						<td style='width:15%;text-align:left;'>".$row->STRNO."</td>
						<td style='width:12%;text-align:right;'>".$row->COUNTCONT."</td>
						<td style='width:12%;text-align:right;'>".number_format($row->TOTPRC,2)."</td>
						<td style='width:10%;text-align:right;'>".number_format($row->TOTPRC,2)."</td>
					</tr>
				";	
			}
		}
		$mpdf = new \Mpdf\Mpdf([
			'mode' => 'utf-8', 
			'format' => 'A4',
			'margin_top' => 0, 	//default = 16
			'margin_left' => 15, 	//default = 15
			'margin_right' => 15, 	//default = 15
			'margin_bottom' => 16, 	//default = 16
			'margin_header' => 9, 	//default = 9
			'margin_footer' => 9, 	//default = 9
		]);
		
		$content = "
			<div class='wf pf' style='top:30;left:0;width:100%;font-size:12pt;text-align:center;'><b>{$data[0]}</b></div>
			<div class='wf pf' style='top:60;left:0;width:100%;font-size:10pt;text-align:center;'>{$data[1]}</div>
			<div class='wf pf' style='top:90;left:0;width:100%;font-size:10pt;text-align:center;'>เลขประจำตัวผู้เสียภาษี</div>
			
			<div class='wf pf data' style='top:140;left:0;width:180;text-align:left;'><b>สาขาที่ออกใบกำกับภาษีคือ</b></div>
			<div class='wf pf data' style='top:140;left:180px;width:300;text-align:left;'>{$data[2]}</div>
			<div class='wf pf data' style='top:140;left:430px;width:200;text-align:left;'><b>{$tfrom}</b></div>
			
			<div class='wf pf data' style='top:170;left:0;width:180;text-align:left;'><b>เลขที่สัญญา</b></div>
			<div class='wf pf data' style='top:170;left:180px;width:300;text-align:left;'>{$data[3]}</div>
			<div class='wf pf data' style='top:170;left:430px;width:100;text-align:left;'><b>เลขที่ใบส่งสินค้า</b></div>
			<div class='wf pf data' style='top:170;left:550px;width:200;text-align:left;'>{$data[3]}</div>
			
			<div class='wf pf data' style='top:200;left:0;width:100;text-align:left;'><b>ชื่อ - นามสกุล</b></div>
			<div class='wf pf data' style='top:200;left:100px;width:300;text-align:left;'>{$data[5]}</div>
			<div class='wf pf data' style='top:200;left:430px;width:100;text-align:left;'><b>วันที่ส่งสินค้า</b></div>
			<div class='wf pf data' style='top:200;left:550px;width:200;text-align:left;'>{$data[4]}</div>
			
			<div class='wf pf data' style='top:230;left:0;width:100;text-align:left;'><b>ที่อยู่</b></div>
			<div class='wf pf data' style='top:230;left:100px;width:300;text-align:left;'>{$data[6]}</div>
			<div class='wf pf data' style='top:230;left:430px;width:120;text-align:left;'><b>เลขที่ใบกำกับภาษี</b></div>
			<div class='wf pf data' style='top:230;left:550px;width:200;text-align:left;'>{$data[8]}</div>
			
			<div class='wf pf data' style='top:270;left:100px;width:300;text-align:left;'>{$data[7]}</div>
			<div class='wf pf' style='top:300;left:0;font-size:10pt;height:1px;border-top:0.1px;background-color:#000000;'></div>
			
			<div class='wf pf' style='top:310;left:0;'>
				<table class='wf pf'>
					<thead>
						<tr>
							<td style='width:6%;text-align:left;'>No.</td>
							<td style='width:15%;text-align:left;'>รายการ</td>
							<td style='width:15%;text-align:left;'></td>
							<td style='width:15%;text-align:left;'></td>
							<td style='width:15%;text-align:left;'></td>
							<td style='width:12%;text-align:right;'>จำนวน</td>
							<td style='width:12%;text-align:right;'>ราคา/หน่วย</td>
							<td style='width:10%;text-align:right;'>จำนวนเงิน</td>
						</tr>
						<tr>
							<td style='width:6%;text-align:left;'></td>
							<td style='width:15%;border-bottom:#000000 1px dotted' colspan='4'></td>
							<td style='width:0%;text-align:right;' colspan='3'></td>
						</tr>
						<tr>
							<td style='width:6%;text-align:left;'></td>
							<td style='width:15%;text-align:left;'>ยี่ห้อ</td>
							<td style='width:15%;text-align:left;'>รุ่น</td>
							<td style='width:15%;text-align:left;'>สี</td>
							<td style='width:15%;text-align:left;'>เลขถัง</td>
							<td style='width:12%;text-align:right;'></td>
							<td style='width:12%;text-align:right;'></td>
							<td style='width:10%;text-align:right;'></td>
						</tr>
					</thead>
				</table>
			</div>
			<div class='wf pf' style='top:370;left:0;font-size:10pt;height:1px;border-top:0.1px;background-color:#000000;'></div>
			<div class='wf pf' style='top:390;left:0;'>
				<table class='wf pf'>
					<tbody>
						".$listcar."
					</tbody>
				</table>
			</div>
			
			<div class='wf pf data' style='top:780;left:50;width:600px;text-align:left;'>{$memo}</div>
			
			<div class='wf pf' style='top:850;left:0;font-size:10pt;height:1px;border-top:0.1px;background-color:#000000;'></div>
			
			<div class='wf pf data' style='top:900;left:450;width:100;text-align:right;'><b>ราคารวมสุทธิ</b></div>
			<div class='wf pf data' style='top:900;left:550px;width:100;text-align:right;'>{$data[9]}</div>
			<div class='wf pf data' style='top:930;left:450;width:100;text-align:right;'><b>ภาษีมูลค่าเพิ่ม</b></div>
			<div class='wf pf data' style='top:930;left:550px;width:100;text-align:right;'>{$data[10]}</div>
			
			<div class='wf pf data' style='top:960;left:0;width:300;text-align:left;'><b>รวม(".$this->ConvertText($data[11]).")</b></div>
			<div class='wf pf data' style='top:960;left:450;width:100;text-align:right;'><b>ราคารวมภาษี</b></div>
			<div class='wf pf data' style='top:960;left:550px;width:100;text-align:right;'>{$data[11]}</div>
			
			<div class='wf pf data' style='top:1050;left:100;width:150;text-align:center;border-bottom:#000000 1px dotted'></div>
			<div class='wf pf data' style='top:1050;left:350;width:150;text-align:center;border-bottom:#000000 1px dotted'></div>
			
			<div class='wf pf data' style='top:1070;left:100;width:150;text-align:center;'><b>ผู้รับสินค้า</b></div>
			<div class='wf pf data' style='top:1070;left:350;width:150;text-align:center;'><b>ผู้มอบอำนาจ</b></div>
		";
		
		$other = "";
		$stylesheet = "
			<style>
				body { font-family: garuda;font-size:10pt; }
				.wf { width:100%; }
				.h10 { height:10px; }
				.tc { text-align:center; }
				.pf { position:fixed; }
				.bor { border:0.1px solid black; }
				.bor2 { border:0.1px dotted black; }
				.data { background-color:#fff;font-size:10pt; }
			</style>
		";
		
		$mpdf->WriteHTML($content.$other.$stylesheet);
		$mpdf->SetHTMLFooter("<div class='wf pf' style='top:1060;left:0;font-size:6pt;width:720px;text-align:right;'>{$this->sess["name"]} ออกเอกสาร ณ วันที่ ".date('d/m/').(date('Y')+543)." ".date('H:i')."</div>");
		$mpdf->fontdata['qanela'] = array('R' => "QanelasSoft-Regular.ttf",'B' => "QanelasSoft-Bold.ttf",); //แก้ปริ้นแล้วอ่านไม่ออก
		$mpdf->Output();
	}
	function pdfsell(){
		$data = array();
		$data[] = $_REQUEST["condpdf"];
		$arrs   = $this->generateData($data,"decode");
		$arrs[0]= urldecode($arrs[0]);
		//print_r($arrs);
		
		$tx = explode('||',$arrs[0]);
		$contno  = $tx[0];
		$locat   = $tx[1];
		
		$sql = "
			select l.LOCATNM+'('+l.LOCATCD+')' as LOCATNM
				,convert(varchar(8),a.ISSUDT,112) as ISSUDT,CONVERT(varchar(8),a.SDATE,112) as SDATE
				,a.CONTNO,c.SNAM+c.NAME1+' '+NAME2 as CUSTNAME,a.MEMO1
				,r.ADDR1+' หมู่บ้าน'+r.MOOBAN+' ซ.'+r.SOI+' ถ.'+r.ADDR2+' ต.'+r.TUMB+' อ.'+p.AUMPDES+' จ.'+v.PROVDES+''+p.AUMPCOD+' โทร.'+r.TELP as ADDR
				,a.DSCPRC,a.TOTPRC,a.TOTFIN,a.TOTDWN
				,t.TYPE,t.MODEL,t.BAAB,t.COLOR,t.STRNO,t.ENGNO,u.USERNAME+'['+u.USERID+']' as USERSALE
				,f.FINNAME
			from {$this->MAuth->getdb('ARFINC')} a
			left join {$this->MAuth->getdb('FINMAST')} f on a.FINCOD = f.FINCODE
			left join {$this->MAuth->getdb('INVLOCAT')} l on a.LOCAT = l.LOCATCD
			left join {$this->MAuth->getdb('INVTRAN')} t on a.STRNO = t.STRNO 
			left join {$this->MAuth->getdb('CUSTMAST')} c on a.CUSCOD = c.CUSCOD
			left join {$this->MAuth->getdb('CUSTADDR')} r on c.CUSCOD = r.CUSCOD and a.ADDRNO = r.ADDRNO
			left join {$this->MAuth->getdb('SETPROV')} v on r.PROVCOD = v.PROVCOD
			left join {$this->MAuth->getdb('SETAUMP')} p on r.AUMPCOD = p.AUMPCOD
			left join {$this->MAuth->getdb('PASSWRD')} u on a.SALCOD = u.USERID
			where a.CONTNO = '".$contno."' and a.LOCAT = '".$locat."'
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$data[0] = $row->LOCATNM;
				$data[1] = $this->Convertdate(2,$row->ISSUDT);
				$data[2] = $this->Convertdate(2,$row->SDATE);
				$data[3] = $row->CONTNO;
				$data[4] = $row->CUSTNAME;
				$data[5] = $row->MEMO1;
				$data[6] = $row->ADDR;
				$data[7] = number_format($row->DSCPRC,2);
				$data[8] = number_format($row->TOTPRC,2);
				$data[9]  = number_format($row->TOTFIN,2);
				$data[10] = number_format($row->TOTDWN,2);
				$data[11] = $row->TYPE;
				$data[12] = $row->MODEL;
				$data[13] = $row->BAAB;
				$data[14] = $row->COLOR;
				$data[15] = $row->STRNO;
				$data[16] = $row->ENGNO;
				$data[17] = $row->USERSALE;
				$data[18] = $row->FINNAME;
			}
		}
		$mpdf = new \Mpdf\Mpdf([
			'mode' => 'utf-8', 
			'format' => 'A4',
			'margin_top' => 0, 	//default = 16
			'margin_left' => 15, 	//default = 15
			'margin_right' => 15, 	//default = 15
			'margin_bottom' => 16, 	//default = 16
			'margin_header' => 9, 	//default = 9
			'margin_footer' => 9, 	//default = 9
		]);
		
		$content = "
			<div class='wf pf' style='top:45;left:0;font-size:12pt;text-align:center;'><u><b>ใบอนุมัติการขาย</b></u></div>
			
			<div class='wf pf' style='top:90;left:0;width:150px;text-align:left;'>อ้างถึงใบสั่งจองรถเลขที่ : </div>
			<div class='wf pf' style='top:90;left:150;width:200px;text-align:left;'></div>
			
			<div class='wf pf' style='top:120;left:0;width:50px;text-align:left;'>สาขา : </div>
			<div class='wf pf' style='top:120;left:50;width:400px;text-align:left;'>{$data[0]}</div>
			<div class='wf pf' style='top:120;left:450;width:250px;text-align:left;'>ประเภทการขาย</div>
			<div class='wf pf' style='top:120;left:550;width:200px;text-align:left;'>ขายส่งไฟแนนซ์</div>
			
			<div class='wf pf' style='top:150;left:0;width:80px;text-align:left;'>วันที่รับรถ : </div>
			<div class='wf pf' style='top:150;left:80;width:400px;text-align:left;'>{$data[1]}</div>
			<div class='wf pf' style='top:150;left:450;width:250px;text-align:left;'>วันที่เริ่มต้นสัญญา : </div>
			<div class='wf pf' style='top:150;left:600;width:200px;text-align:left;'>{$data[2]}</div>
			
			<div class='wf pf' style='top:180;left:0;width:80px;text-align:left;'>เลขที่สัญญา : </div>
			<div class='wf pf' style='top:180;left:80;width:400px;text-align:left;'>{$data[3]}</div>
			<div class='wf pf' style='top:180;left:450;width:250px;text-align:left;'>วันที่สิ้นสุดสัญญา : </div>
			<div class='wf pf' style='top:180;left:600;width:200px;text-align:left;'>{$data[2]}</div>
			
			<div class='wf pf' style='top:210;left:0;text-align:left;'>ชื่อลูกค้า : </div>
			<div class='wf pf' style='top:210;left:80;text-align:left;'>{$data[4]}</div>
			<div class='wf pf' style='top:240;left:0;text-align:left;'>หมายเหตุ : </div>
			<div class='wf pf' style='top:240;left:80;text-align:left;width:600px;'>{$data[5]}</div>
			
			<div class='wf pf' style='top:290;left:0;text-align:left;'>ที่อยู่ : </div>
			<div class='wf pf' style='top:290;left:80;text-align:left;'>{$data[6]}</div>
			
			<div class='wf pf' style='top:360;left:0;text-align:left;'>ราคาขายสดหน้าร้าน</div>
			<div class='wf pf' style='top:360;left:180;width:100px;text-align:right;'>0.00</div>
			<div class='wf pf' style='top:360;left:300;text-align:left;'>บาท</div>
			
			<div class='wf pf' style='top:390;left:0;text-align:left;'></div>
			<div class='wf pf' style='top:390;left:180;width:100px;text-align:right;'>{$data[7]}</div>
			<div class='wf pf' style='top:390;left:300;text-align:left;'>บาท</div>
			
			<div class='wf pf' style='top:420;left:0;text-align:left;'>ราคาขายเงินสดสุทธิ</div>
			<div class='wf pf' style='top:420;left:180;width:100px;text-align:right;'>{$data[8]}</div>
			<div class='wf pf' style='top:420;left:300;text-align:left;'>บาท</div>
			
			<div class='wf pf' style='top:450;left:0;text-align:left;'>เงินดาวน์</div>
			<div class='wf pf' style='top:450;left:180;width:100px;text-align:right;'>{$data[10]}</div>
			<div class='wf pf' style='top:450;left:300;text-align:left;'>บาท</div>
			
			<div class='wf pf' style='top:480;left:0;text-align:left;'>ราคาขายสดสุทธิหักเงินดาวน์</div>
			<div class='wf pf' style='top:480;left:180;width:100px;text-align:right;'>{$data[9]}</div>
			<div class='wf pf' style='top:480;left:300;text-align:left;'>บาท</div>
			
			<div class='wf pf' style='top:510;left:0;text-align:left;'>อัตราดอกเบี้ยเช่าซื้อ</div>
			<div class='wf pf' style='top:510;left:180;width:100px;text-align:right;'>0.00</div>
			<div class='wf pf' style='top:510;left:300;text-align:left;'>บาท</div>
			
			<div class='wf pf' style='top:540;left:0;text-align:left;'>ราคาขายรวมภาษี</div>
			<div class='wf pf' style='top:540;left:180;width:100px;text-align:right;'>{$data[8]}</div>
			<div class='wf pf' style='top:540;left:300;text-align:left;'>บาท</div>
			
			<div class='wf pf' style='top:570;left:0;text-align:left;'>ผ่อนชำระงวดละ</div>
			<div class='wf pf' style='top:570;left:180;width:100px;text-align:right;'>0.00</div>
			<div class='wf pf' style='top:570;left:300;text-align:left;'>บาท</div>
			
			<div class='wf pf' style='top:600;left:0;text-align:left;'>ยอดจัดกรณีเข้าไฟแนนซ์</div>
			<div class='wf pf' style='top:600;left:180;width:100px;text-align:right;'>{$data[9]}</div>
			<div class='wf pf' style='top:600;left:300;text-align:left;'>บาท</div>
			
			<div class='wf pf' style='top:330;left:450;text-align:left;font-size:10pt;'><u><b>รายละเอียดรถ</b></u></div>
			<div class='wf pf' style='top:360;left:400;text-align:left;'>ยี่ห้อ</div>
			<div class='wf pf' style='top:360;left:500;text-align:left;'>{$data[10]}</div>
			
			<div class='wf pf' style='top:390;left:400;text-align:left;'>รุ่น</div>
			<div class='wf pf' style='top:390;left:500;text-align:left;'>{$data[11]}</div>
			<div class='wf pf' style='top:420;left:400;text-align:left;'>แบบ</div>
			<div class='wf pf' style='top:420;left:500;text-align:left;'>{$data[12]}</div>
			<div class='wf pf' style='top:450;left:400;text-align:left;'>สี</div>
			<div class='wf pf' style='top:450;left:500;text-align:left;'>{$data[13]}</div>
			<div class='wf pf' style='top:480;left:400;text-align:left;'>เลขทะเบียน</div>
			<div class='wf pf' style='top:480;left:500;text-align:left;'></div>
			<div class='wf pf' style='top:510;left:400;text-align:left;'>หมายเลขตัวถัง</div>
			<div class='wf pf' style='top:510;left:500;text-align:left;'>{$data[14]}</div>
			<div class='wf pf' style='top:540;left:400;text-align:left;'>หมายเลขเครื่อง</div>
			<div class='wf pf' style='top:540;left:500;text-align:left;'>{$data[15]}</div>
			<div class='wf pf' style='top:570;left:400;text-align:left;'>บริษัทไฟแนนซ์</div>
			<div class='wf pf' style='top:570;left:500;text-align:left;'>{$data[16]}</div>
			
			<div class='wf pf' style='top:600;left:400;text-align:left;'><u>จำนวนเงินที่ต้องชำระในวันรับรถ</u></div>
			
			<div class='wf pf' style='top:630;left:300;text-align:left;'>กรณีเงินสด / เงินเชื่อ</div>
			<div class='wf pf' style='top:630;left:500;width:100px;text-align:right;'>0.00</div>
			<div class='wf pf' style='top:630;left:620;text-align:left;'>บาท</div>
			<div class='wf pf' style='top:660;left:300;text-align:left;'>กรณีเงินเช่าซื้อ / ขายผ่อน</div>
			<div class='wf pf' style='top:660;left:500;width:100px;text-align:right;'>0.00</div>
			<div class='wf pf' style='top:660;left:620;text-align:left;'>บาท</div>
			<div class='wf pf' style='top:690;left:300;text-align:left;'>กรณีขายไฟแนนซ์</div>
			<div class='wf pf' style='top:690;left:500;width:100px;text-align:right;'>{$data[10]}</div>
			<div class='wf pf' style='top:690;left:620;text-align:left;'>บาท</div>
			<div class='wf pf' style='top:720;left:300;text-align:left;'>หักเงินมัดจำ</div>
			<div class='wf pf' style='top:720;left:500;width:100px;text-align:right;'>0.00</div>
			<div class='wf pf' style='top:720;left:620;text-align:left;'>บาท</div>
			<div class='wf pf' style='top:750;left:300;text-align:left;'>ต้องชำระทั้งสิ้น</div>
			<div class='wf pf' style='top:750;left:500;width:100px;text-align:right;'>{$data[10]}</div>
			<div class='wf pf' style='top:750;left:620;text-align:left;'>บาท</div>
			<div class='wf pf' style='top:780;left:300;text-align:left;'>ชำระจริง</div>
			<div class='wf pf' style='top:780;left:500;width:100px;text-align:right;'>0.00</div>
			<div class='wf pf' style='top:780;left:620;text-align:left;'>บาท</div>
			<div class='wf pf' style='top:810;left:300;text-align:left;'>คงค้าง</div>
			<div class='wf pf' style='top:810;left:500;width:100px;text-align:right;'>{$data[10]}</div>
			<div class='wf pf' style='top:810;left:620;text-align:left;'>บาท</div>
			
			<div class='wf pf' style='top:840;left:300;text-align:left;'>ยอดตั้งลูกหนี้</div>
			<div class='wf pf' style='top:840;left:500;width:100px;text-align:right;'>{$data[9]}</div>
			<div class='wf pf' style='top:840;left:620;text-align:left;'>บาท</div>
			
			
		";
		
		$other = "";
		$stylesheet = "
			<style>
				body { font-family: garuda;font-size:10pt; }
				.wf { width:100%; }
				.h10 { height:10px; }
				.tc { text-align:center; }
				.pf { position:fixed; }
				.bor { border:0.1px solid black; }
				.bor2 { border:0.1px dotted black; }
				.data { background-color:#fff;font-size:9pt; }
			</style>
		";
		
		$mpdf->WriteHTML($content.$other.$stylesheet);
		$mpdf->SetHTMLFooter("<div class='wf pf' style='top:1060;left:0;font-size:6pt;width:720px;text-align:right;'>{$this->sess["name"]} ออกเอกสาร ณ วันที่ ".date('d/m/').(date('Y')+543)." ".date('H:i')."</div>");
		$mpdf->fontdata['qanela'] = array('R' => "QanelasSoft-Regular.ttf",'B' => "QanelasSoft-Bold.ttf",); //แก้ปริ้นแล้วอ่านไม่ออก
		$mpdf->Output();
	}
}




















