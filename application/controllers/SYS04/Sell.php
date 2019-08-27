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
class Sell extends MY_Controller {
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
								ชื่อ-สกุล ผู้จอง
								<select id='CUSCOD' class='form-control input-sm' data-placeholder='ชื่อ-สกุล ผู้จอง'></select>
							</div>
						</div>
					</div>
					<div class='row'>
						<div class=' col-sm-6'>	
							<div class='form-group'>
								<input type='button' id='btnt1sell' class='btn btn-cyan btn-sm' value='ขายสด' style='width:100%'>
							</div>
						</div>
						<div class=' col-sm-6'>	
							<div class='form-group'>
								<input type='button' id='btnt1search' class='btn btn-primary btn-sm' value='แสดง' style='width:100%'>
							</div>
						</div>
					</div>		
				</div>
			</div>
		";
		
		$html.= "<script src='".base_url('public/js/SYS04/Sell.js')."'></script>";
		echo $html;
	}
	
	function search(){
		$arrs = array();
		$arrs['contno']	= $_REQUEST['contno'];
		$arrs['sdatefrm'] = $this->Convertdate(1,$_REQUEST['sdatefrm']);
		$arrs['sdateto'] = $this->Convertdate(1,$_REQUEST['sdateto']);
		$arrs['locat'] 	= $_REQUEST['locat'];
		$arrs['strno'] 	= $_REQUEST['strno'];
		$arrs['cuscod'] = $_REQUEST['cuscod'];
		
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
		
		if($arrs['cuscod'] != ""){
			$condDesc .= " ลูกค้า ".$arrs['cuscod'];
			$cond .= " and A.cuscod like '".$arrs['cuscod']."'";
		}
		
		$sql = "
			SELECT ".($cond == "" ? "top 20":"")." A.CONTNO,A.LOCAT,convert(varchar(8),A.SDATE,112) as SDATE
				,A.CUSCOD,B.SNAM+B.NAME1+' '+B.NAME2 as NAME,A.STRNO,A.RESVNO
			FROM {$this->MAuth->getdb('ARCRED')} A
			left join {$this->MAuth->getdb('CUSTMAST')} B on A.CUSCOD=B.CUSCOD
			where 1=1 ".$cond."
			order by A.CONTNO
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
							<i class='sellDetails btn btn-xs btn-success glyphicon glyphicon-zoom-in' contno='".$row->CONTNO."' style='cursor:pointer;'> รายละเอียด  </i>
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
			<div id='table-fixed-sellCar' class='col-sm-12' style='height:calc(100% - 30px);width:100%;overflow:auto;font-size:8pt;'>
				<table id='table-sellCar' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='100%' border=1>
					<thead>						
						<tr style='line-height:20px;'>
							<td style='vertical-align:middle;background-color:#ccc;text-align:center;font-size:8pt;' colspan='8'>
								เงื่อนไข :: {$condDesc}
							</td>
						</tr>
						<tr>
							<th style='vertical-align:middle;background-color:#ccc;'>#</th>
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
			<div>
				<img src='".base_url("/public/images/excel.png")."'  onclick=\"tableToExcel('table-sellCar', 'exporttoexcell');\" style='width:25px;height:25px;cursor:pointer;'/>
			</div>
		";
		
		/*
		$html = "
			<div class='panel panel-default'>
				<div class='panel-heading'>
					<div class='panel-title'>
						<h4>Panel title</h4>
					</div>
				</div>
				<div class='panel-body'>
					".$html."
				</div>
			</div>
		";
		*/
		
		$response = array("html"=>$html,"status"=>true);
		echo json_encode($response);
	}
	
	function loadSell(){
		$contno = $_REQUEST['contno'];
		
		$sql = "
			select CONTNO,LOCAT,convert(varchar(8),SDATE,112) SDATE,RESVNO,APPVNO
				,CUSCOD,(select SNAM+NAME1+' '+NAME2+' ('+CUSCOD+')'+'-'+GRADE from {$this->MAuth->getdb('CUSTMAST')} where CUSCOD=a.CUSCOD) as CUSNAME
				,INCLVAT,VATRT
				,ADDRNO,(select '('+ADDRNO+') '+ADDR1+' '+ADDR2+' '+TUMB from {$this->MAuth->getdb('CUSTADDR')} where CUSCOD=a.CUSCOD and ADDRNO=a.ADDRNO) as ADDRDetail
				,STRNO,PAYTYP,(select '('+PAYCODE+') '+PAYDESC from {$this->MAuth->getdb('PAYDUE')} where PAYCODE=a.PAYTYP) as PAYDESC
				,OPTCTOT,OPTPTOT,STDPRC,KEYIN,TAXNO,convert(varchar(8),TAXDT,112) TAXDT,CREDTM,convert(varchar(8),DUEDT,112) DUEDT
				,SALCOD	,(select USERNAME+' ('+USERID+')' from {$this->MAuth->getdb('PASSWRD')} where USERID=a.SALCOD) as SALNAME
				,COMITN,ISSUNO,convert(varchar(8),ISSUDT,112) ISSUDT,RECOMCOD
				,(select SNAM+NAME1+' '+NAME2+' ('+CUSCOD+')'+'-'+GRADE from {$this->MAuth->getdb('CUSTMAST')} where CUSCOD=a.RECOMCOD) as RECOMNAME
				,ACTICOD,(select '('+ACTICOD+') '+ACTIDES from HIC2SHORTL.dbo.SETACTI where 1=1 and ACTICOD=a.ACTICOD) as ACTINAME
				,COMEXT,COMOPT,COMOTH
				,CRDTXNO,CRDAMT,MEMO1
			from {$this->MAuth->getdb('ARCRED')} a
			where a.CONTNO='".$contno."'
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$response = array();
		if($query->row()){
			foreach($query->result() as $row){
				$response["CONTNO"] 	= $row->CONTNO;
				$response["LOCAT"] 		= $row->LOCAT;
				$response["SDATE"] 		= $this->Convertdate(2,$row->SDATE);
				$response["RESVNO"] 	= $row->RESVNO;
				$response["APPVNO"] 	= $row->APPVNO;
				$response["CUSCOD"] 	= $row->CUSCOD;
				$response["CUSNAME"] 	= $row->CUSNAME;
				$response["INCLVAT"] 	= $row->INCLVAT;
				$response["VATRT"] 		= number_format($row->VATRT,2);
				$response["ADDRNO"] 	= $row->ADDRNO;
				$response["ADDRDetail"] = $row->ADDRDetail;
				$response["STRNO"] 		= $row->STRNO;
				$response["PAYTYP"] 	= $row->PAYTYP;
				$response["PAYDESC"] 	= $row->PAYDESC;
				
				$response["OPTCTOT"] 	= number_format($row->OPTCTOT,2);
				$response["OPTPTOT"] 	= number_format($row->OPTPTOT,2);
				$response["STDPRC"] 	= number_format($row->STDPRC,2);
				$response["KEYIN"] 		= number_format($row->KEYIN,2);
				$response["TAXNO"] 		= $row->TAXNO;
				$response["TAXDT"] 		= $this->Convertdate(2,$row->TAXDT);
				$response["TAXNO"] 		= $row->TAXNO;
				$response["CREDTM"] 	= $row->CREDTM;
				$response["DUEDT"] 		= $this->Convertdate(2,$row->DUEDT);
				$response["SALCOD"] 	= $row->SALCOD;
				$response["SALNAME"] 	= $row->SALNAME;
				$response["COMITN"] 	= number_format($row->COMITN,2);
				$response["ISSUNO"] 	= $row->ISSUNO;
				$response["ISSUDT"] 	= $this->Convertdate(2,$row->ISSUDT);
				
				$response["RECOMCOD"] 	= $row->RECOMCOD;
				$response["RECOMNAME"] 	= $row->RECOMNAME;
				$response["ACTICOD"] 	= $row->ACTICOD;
				$response["ACTINAME"] 	= $row->ACTINAME;
				$response["COMEXT"] 	= number_format($row->COMEXT,2);
				$response["COMOPT"] 	= number_format($row->COMOPT,2);
				$response["COMOTH"] 	= number_format($row->COMOTH,2);
				$response["CRDTXNO"] 	= $row->CRDTXNO;
				$response["CRDAMT"] 	= number_format($row->CRDAMT,2);
				
				$MEMO = explode('[explode]',$row->MEMO1);
				if(sizeof($MEMO) == 2){
					$billDAS = explode(',',$MEMO[0]);
					for($i=0;$i<sizeof($billDAS);$i++){
						$response["billDAS"][$i] = $billDAS[$i];
					}
					$response["MEMO1"] 	= $MEMO[1];
				}else{
					$response["MEMO1"] 	= $row->MEMO1;
				}
			}
		}
		
		$sql = "
			select a.OPTCODE,a.UPRICE,a.UCOST,a.QTY,a.TOTPRC,a.TOTVAT,a.NPRICE,a.OPTCST,a.OPTCVT,a.OPTCTOT 
				,'('+a.OPTCODE+') '+b.OPTNAME as OPTNAME
			from {$this->MAuth->getdb('ARINOPT')} a 
			left join {$this->MAuth->getdb('OPTMAST')} b on a.OPTCODE=b.OPTCODE
			where a.CONTNO='".$contno."' and b.LOCAT=(select LOCAT from {$this->MAuth->getdb('ARCRED')} where CONTNO='".$contno."')
		";
		$query = $this->db->query($sql);
		
		$option = "";
		if($query->row()){
			foreach($query->result() as $row){
				$option .= "
					<tr seq='old'>
						<td align='center'>
							<i class='inoptTab2 btn btn-xs btn-danger glyphicon glyphicon-minus' disabled
								opCode='".$row->OPTCODE."' total1='".$row->TOTPRC."' total2='".$row->OPTCTOT."' 
								price1='".$row->NPRICE."' price2='".$row->OPTCST."' vat1='".$row->TOTVAT."' 
								vat2='".$row->OPTCVT."' qty='".$row->QTY."' uprice='".$row->UPRICE."' 
								style='cursor:pointer;'> ลบ   
							</i>
						</td>
						<td>".$row->OPTNAME."</td>
						<td class='text-right'>".number_format($row->UPRICE,2)."</td>
						<td class='text-right'>".number_format($row->QTY,2)."</td>
						<td class='text-right'>".number_format($row->NPRICE,2)."</td>
						<td class='text-right'>".number_format($row->TOTVAT,2)."</td>
						<td class='text-right'>".number_format($row->TOTPRC,2)."</td>
						<td class='text-right'>".number_format($row->OPTCST,2)."</td>
						<td class='text-right'>".number_format($row->OPTCVT,2)."</td>
						<td class='text-right'>".number_format($row->OPTCTOT,2)."</td>
					</tr>
				";
			}
		}
		$response["option"] = $option;
		
		echo json_encode($response);
	}
	
	function loadARPAY(){
		$contno = $_REQUEST['contno'];
		$sql = "
			select NOPAY,convert(varchar(8),DDATE,112) as DDATE,DAMT,V_DAMT,N_DAMT,PAYMENT
				,V_PAYMENT,TAXINV,convert(varchar(8),TAXDT,112) as TAXDT,GRDCOD 
			from {$this->MAuth->getdb('ARPAY')}
			where CONTNO='".$contno."'
			order by NOPAY
		";
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$html .= "
					<tr>
						<td style='max-width:40px;'>".$row->NOPAY."</td>
						<td>".$this->Convertdate(2,$row->DDATE)."</td>
						<td class='text-right'>".number_format($row->DAMT,2)."</td>
						<td class='text-right'>".number_format($row->V_DAMT,2)."</td>
						<td class='text-right text-blue'>".number_format($row->N_DAMT,2)."</td>
						<td class='text-right text-red'>".number_format($row->V_PAYMENT,2)."</td>
						<td class='text-right'>".number_format($row->PAYMENT,2)."</td>
						<td>".$row->TAXINV."</td>
						<td>".$this->Convertdate(2,$row->TAXDT)."</td>
						<td>".$row->GRDCOD."</td>
					</tr>
				";
			}
		}
		
		$html = "
			<div id='dataTable-fixed-arpay' class='dataTables_wrapper dt-bootstrap4 table-responsive' style='max-height:calc(100%);height:calc(100%);overflow:auto;border:1px dotted black;background-color:#eee;'>
				<table id='dataTables-arpay' class='table table-bordered dataTable table-hover' style='width:100%;line-height:10px;white-space:nowrap;text-overflow:ellipsis;overflow:hidden;' cellspacing='0'>
					<thead>
						<tr role='row'>
							<th style='max-width:40px;'>งวดที่</th>
							<th>ดิวเดท</th>
							<th>ค่างวดรวม</th>
							<th>ภาษี</th>
							<th>มูลค่างวดนี้</th>
							<th>ภาษีงวดนี้</th>
							<th>ชำระแล้ว</th>
							<th>ใบกำกับภาษี</th>
							<th>วันที่ใบกำกับ</th>
							<th>เกรด</th>
						</tr>
					</thead>
					<tbody style='white-space: nowrap;'>
						".$html."
					</tbody>
				</table>
			</div>
		";
		
		echo json_encode(array("html"=>$html));
	}
		
	function getfromSell(){
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
			<div id='wizard-sell' class='wizard-wrapper'>    
				<div class='wizard'>
					<form id='demo-form2' action='' class='lobi-form' novalidate='novalidate'>
						<ul class='wizard-tabs wizard-tab-balls nav-justified nav nav-pills'>
							<li class='active'>
								<a href='#tab11' prev='#' data-toggle='tab' aria-expanded='true'>
									<span class='step'>1</span>
									<span class='title'>ผู้ซื้อและสินค้า</span>
								</a>
							</li>
							<li>
								<a href='#tab22' prev='#tab11' data-toggle='tab'>
									<span class='step'>2</span>
									<span class='title'>ราคาสินค้า</span>
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
							".$this->getfromLeasingTab11($data)."
							".$this->getfromLeasingTab22($data)."
							".$this->getfromLeasingTab33($data)."							
							
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
					<input type='button' id='btnTax' class='btn btn-xs btn-info' style='width:100px;' value='ใบกำกับ' disabled>
					<input type='button' id='btnSend' class='btn btn-xs btn-info' style='width:100px;' value='ใบส่งมอบ' disabled>
					<input type='button' id='btnApproveSell' class='btn btn-xs btn-info' style='width:100px;' value='ใบอนุมัติขาย' disabled>
					<br>
				</div>
				<div class='col-sm-6 text-right'>
					<input type='button' id='add_save' class='btn btn-xs btn-primary right' style='width:100px;' value='บันทึก' >
					<br>
					<input type='button' id='add_delete' class='btn btn-xs btn-danger right' style='width:100px;' value='ลบ' >
				</div>
			</div>
		";
		
		$response = array('html'=>$html,'status'=>true);
		echo json_encode($response);
	}
	
	private function getfromLeasingTab11($data){
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
										<select id='add_cuscod' class='form-control input-sm' data-placeholder='ลูกค้า'></select>
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
								<div class='col-sm-4'>	
									<div class='form-group'>
										วิธีชำระค่างวด
										<select id='add_paydue' class='form-control input-sm' data-placeholder='วิธีชำระค่างวด'></select>
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
	
	private function getfromLeasingTab22($data){
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
													<input type='text' id='add2_optcost' class='form-control input-sm text-right' value='0.00' disabled>
													<span id='error_add2_optcost' class='error text-danger jzError'></span>		
												</div>
												
												<div class='form-group col-sm-4'>
													<label class='jzfs10' for='add2_optsell' style='color:#34dfb5;'>ราคาขาย</label>
													<input type='text' id='add2_optsell' class='form-control input-sm text-right' value='0.00' disabled>
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
									ราคาขายหน้าร้าน
									<input type='text' id='add_stdprc' class='form-control input-sm' placeholder='ราคาขายหน้าร้าน' value='0.00'>
								</div>
							</div>
							<div class='col-sm-12 col-lg-6'>	
								<div class='form-group'>
									ราคาขายจริงรวมอุปกรณ์
									<div class='form-group'>
										<label class='input'>
											<span id='add_inprcCal' class='input-icon input-icon-append glyphicon glyphicon-info-sign'></span>
											<input type='text' id='add_inprc' class='form-control input-sm' placeholder='ราคาขายจริงรวมอุปกรณ์' value='0.00'>
										</label>
									</div>
								</div>
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
								เครดิต
								<div class='input-group'>
									<input type='text' id='add_credtm' class='form-control input-sm' placeholder='เครดิต' value='0'>
									<span class='input-group-addon'>วัน</span>
								</div>
							</div>
							<div class='col-sm-12 col-lg-6'>	
								วันครบกำหนดชำระ
								<input type='text' id='add_duedt' class='form-control input-sm' placeholder='วันครบกำหนดชำระ' data-provide='datepicker' data-date-language='th-th' value='".$this->today('today')."'>
							</div>
							
							<div class='col-sm-12 col-lg-12'>	
								<div class='form-group'>
									รหัสพนักงานขาย
									<select id='add_salcod' class='form-control input-sm' data-placeholder='รหัสพนักงานขาย'>
										<option value='".$this->sess["USERID"]."'>".$this->sess["name"]." (".$this->sess["USERID"].")</option>
									</select>
								</div>
							</div>
							<div class='col-sm-12 col-lg-6'>	
								<div class='form-group'>
									ค่าคอมพนักงานขาย
									<input type='text' id='add_comitn' class='form-control input-sm' placeholder='ค่าคอมพนักงานขาย' value='0.00'>
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
	
	private function getfromLeasingTab33($data){
		$html = "
			<div class='tab-pane' name='tab33' style='height:calc(100vh - 260px);overflow:auto;'>
				<fieldset style='height:100%'>
					<div style='float:left;' class='col-sm-8 '>
						<div class='row'>
							<div class=' col-sm-6'>	
								<div class='form-group'>
									ผู้แนะนำการซื้อ
									<select id='add_recomcod' class='form-control input-sm' data-placeholder='ผู้แนะนำการซื้อ'></select>
								</div>
							</div>
							
							<div class=' col-sm-6'>	
								<div class='form-group'>
									กิจกรรมการขาย
									<select id='add_acticod' class='form-control input-sm' data-placeholder='กิจกรรมการขาย'></select>
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
							<div class='col-sm-4'>	
								<div class='form-group'>
									จำนวนเงินที่ลดหนี้
									<input type='text' id='add_crdamt' class='form-control input-sm' placeholder='จำนวนเงินที่ลดหนี้' value='0.00'>
								</div>
							</div>
							
							
							<div class='2 col-sm-12'>	
								<div class='form-group'>
									หมายเหตุ
									<textarea type='text' id='add_memo1' class='form-control input-sm' placeholder='หมายเหตุ' ></textarea>
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
	
	function strnoChanged(){
		$strno = $_REQUEST['strno'];
		$sql = "select STDPRC from {$this->MAuth->getdb('INVTRAN')} where STRNO='".$strno."'";
		$query = $this->db->query($sql);
		
		$response = array();
		if($query->row()){
			foreach($query->result() as $row){
				$response["STDPRC"]  = number_format($row->STDPRC,2);
			}
		}else{
			$response["STDPRC"]  = '0.00';
		}
		
		echo json_encode($response);
	}
	
	function resvnoChanged(){
		$resvno = $_REQUEST['resvno'];
		
		$sql = "
			select a.RESVNO,a.LOCAT
				,a.CUSCOD
				,b.SNAM+b.NAME1+' '+b.NAME2+' ('+b.CUSCOD+')'+'-'+b.GRADE CUSNAME
				,b.GRADE,a.STRNO
				,a.SMCHQ
				,a.RESPAY
				,a.SMPAY
			from {$this->MAuth->getdb('ARRESV')} a
			left join {$this->MAuth->getdb('CUSTMAST')} b on a.CUSCOD=b.CUSCOD
			left join {$this->MAuth->getdb('INVTRAN')} c on a.STRNO=c.STRNO and c.FLAG='D'
			where 1=1 and a.RESVNO='".$resvno."' and c.STRNO is not null
		";
		$query = $this->db->query($sql);
		
		$response = array();
		if($query->row()){
			foreach($query->result() as $row){
				$response["RESVNO"]  = $row->RESVNO;
				$response["LOCAT"]   = $row->LOCAT;
				$response["CUSCOD"]  = $row->CUSCOD;
				$response["CUSNAME"] = $row->CUSNAME;
				$response["GRADE"]   = $row->GRADE;
				$response["STRNO"]   = $row->STRNO;
				$response["SMCHQ"]   = str_replace(",","",number_format($row->SMCHQ,2));
				$response["msg"]   	 = ($row->SMCHQ > 0 ? "เช็คเงินจองยังไม่ผ่าน": ($row->RESPAY == $row->SMPAY ? "" : "เลขที่บิลจอง ".$row->RESVNO." ยังชำระเงินจองไม่ครบครับ"));
			}
		}else{
			$response["RESVNO"]  = "";
			$response["LOCAT"]   = "";
			$response["CUSCOD"]  = "";
			$response["CUSNAME"] = "";
			$response["GRADE"] 	 = "";
			$response["STRNO"] 	 = "";
			$response["SMCHQ"]   = "";
			$response["msg"]   	 = "";
		}
		
		echo json_encode($response);
	}
	
	function checkCustomer(){
		$cuscod = $_REQUEST['cuscod'];
		$sql = "
			select GRADE from {$this->MAuth->getdb('CUSTMAST')}
			where CUSCOD='".$cuscod."'
		";
		$query = $this->db->query($sql);
		
		$response = array();
		if($query->row()){
			foreach($query->result() as $row){
				$response["GRADE"] = $row->GRADE;
			}
		}else{
			$response["GRADE"] = "";
		}
		
		$sql = "
			select RESVNO,CONVERT(varchar(8),RESVDT,112) as RESVDT
				,STRNO,MODEL,BAAB,COLOR,LOCAT
			from {$this->MAuth->getdb('ARRESV')}
			where CUSCOD='".$cuscod."' and SDATE is null and STRNO <> ''
		";
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$html .= "
					<div class='row' style='border:1px dotted red;'>
						<div class='col-sm-4 text-right'>
							เลขที่บิลจอง
						</div>
						<div class='col-sm-8'>
							".$row->RESVNO."
						</div>
						
						<div class='col-sm-4 text-right'>
							วันที่จอง
						</div>
						<div class='col-sm-8'>
							".$this->Convertdate(2,$row->RESVDT)."
						</div>
						
						<div class='col-sm-4 text-right'>
							จองที่สาขา
						</div>
						<div class='col-sm-8'>
							".$row->LOCAT."
						</div>
						
						
						<div class='col-sm-4 text-right'>
							เลขตัวถัง
						</div>
						<div class='col-sm-8'>
							".$row->STRNO."
						</div>
						
						<div class='col-sm-4 text-right'>
							รุ่น
						</div>
						<div class='col-sm-8'>
							".$row->MODEL."
						</div>
						
						<div class='col-sm-4 text-right'>
							แบบ
						</div>
						<div class='col-sm-8'>
							".$row->BAAB."
						</div>
						
						<div class='col-sm-4 text-right'>
							สี
						</div>
						<div class='col-sm-8'>
							".$row->COLOR."
						</div>
						
						<div class='col-sm-12'>
							<input type='button' class='cusinresv btn btn-primary btn-block' resvno='".$row->RESVNO."' value='ใช้เลขที่บิลจอง ".$row->RESVNO."'>
						</div>
					</div>					
				";
			}
		}
		
		$response["ARRESV"] = $html;
		echo json_encode($response);
	}
	
	function save(){
		$arrs = array();
		$arrs["contno"] 	= $_REQUEST["contno"];
		$arrs["locat"] 		= $_REQUEST["locat"];
		$arrs["resvno"] 	= $_REQUEST["resvno"];
		$arrs["cuscod"] 	= $_REQUEST["cuscod"];
		$arrs["strno"] 		= $_REQUEST["strno"];
		$arrs["inclvat"] 	= $_REQUEST["inclvat"];
		$arrs["sdate"] 		= $this->Convertdate(1,$_REQUEST["sdate"]);
		$arrs["vatrt"] 		= $_REQUEST["vatrt"];
		
		$arrs["stdprc"] 	= str_replace(',','',$_REQUEST["stdprc"]);
		$arrs["dscprc"] 	= 0;
		
		$arrs["keyin"] 		= str_replace(',','',$_REQUEST["inprc"]);
		if($arrs["inclvat"] == 'Y'){
			$arrs["nkeyin"] 	= str_replace(',','',number_format($arrs["keyin"] / ((100 + $arrs["vatrt"]) / 100),2));
			$arrs["vkeyin"] 	= str_replace(',','',number_format($arrs["keyin"] - $arrs["nkeyin"],2));
			$arrs["tkeyin"] 	= $arrs["keyin"];
		}else{
			$arrs["nkeyin"] 	= $arrs["keyin"];
			$arrs["vkeyin"] 	= str_replace(',','',number_format($arrs["keyin"] * ($arrs["vatrt"] / 100),2));
			$arrs["tkeyin"] 	= str_replace(',','',number_format($arrs["nkeyin"] + $arrs["vkeyin"],2));
		}
		
		$arrs["nprice"] 	= $arrs["nkeyin"];
		$arrs["vatprc"] 	= $arrs["vkeyin"];
		$arrs["totprc"] 	= $arrs["tkeyin"];
		$arrs["npayres"]    = 0;
		$arrs["vatpres"] 	= 0;
		$arrs["totpres"]	= 0;
		$arrs["smpay"] 		= 0;
		$arrs["smchq"] 		= 0;
		$arrs["nkang"] 		= 0;
		$arrs["vkang"] 		= 0;
		$arrs["tkang"] 		= 0;
		$arrs["ncarcst"] 	= 0;
		$arrs["vcarcst"] 	= 0;
		$arrs["tcarcst"] 	= 0;
		$arrs["crdtxno"] 	= $_REQUEST["crdtxno"];
		$arrs["crdamt"] 	= str_replace(',','',$_REQUEST["crdamt"]);
		$arrs["optcst"]  	= 0;
		$arrs["optcvt"]  	= 0;
		$arrs["optctot"] 	= 0;		
		$arrs["optprc"]  	= 0;
		$arrs["optpvt"]  	= 0;
		$arrs["optptot"] 	= 0;
		$arrs["credtm"] 	= str_replace(',','',$_REQUEST["credtm"]);
		$arrs["duedt"] 		= $this->Convertdate(1,$_REQUEST["duedt"]);
		$arrs["salcod"] 	= $_REQUEST["salcod"];
		$arrs["comitn"] 	= str_replace(',','',$_REQUEST["comitn"]);
		$arrs["issuno"] 	= $_REQUEST["issuno"];
		$arrs["issudt"] 	= $this->Convertdate(1,$_REQUEST["issudt"]);
		$arrs["tsale"]	 	= 'C';
		$arrs["memo1"] 		= $_REQUEST["memo1"];
		$arrs["userid"]  	= $this->sess["USERID"];
		$arrs["inpdt"] 	 	= 'getdate()';
		$arrs["delid"]	 	= '';
		$arrs["flcancl"] 	= '';
		$arrs["appvno"] 	= $_REQUEST["approve"];
		$arrs["paytyp"] 	= $_REQUEST["paydue"];
		$arrs["addrno"] 	= $_REQUEST["addrno"];
		$arrs["comext"] 	= str_replace(',','',($_REQUEST["commission"] == "" ? 0 : $_REQUEST["commission"]));
		$arrs["comopt"] 	= str_replace(',','',($_REQUEST["free"] == "" ? 0 : $_REQUEST["free"]));
		$arrs["comoth"] 	= str_replace(',','',($_REQUEST["payother"] == "" ? 0 : $_REQUEST["payother"]));
		$arrs["recomcod"] 	= $_REQUEST["recomcod"];
		$arrs["acticod"] 	= $_REQUEST["acticod"];
		
		$arrs["updateARRESV"] = "";
		if($arrs["resvno"] != ''){
			$sql = "
				select * from {$this->MAuth->getdb('ARRESV')}
				where RESVNO='".$arrs["resvno"]."' --and RESPAY <= SMPAY
			";
			$query = $this->db->query($sql);
			
			if($query->row()){
				foreach($query->result() as $row){
					$arrs["npayres"]    = str_replace(',','',number_format($row->RESPAY / ((100 + $row->VATRT) / 100),2));
					$arrs["vatpres"] 	= str_replace(',','',number_format($row->RESPAY - $arrs["npayres"],2));
					$arrs["totpres"] 	= $row->RESPAY;
					
					$arrs["smpay"] = $row->RESPAY;
					$arrs["smchq"] = 0;
				}
			}
			
			$arrs["updateARRESV"] = "
				if(( select count(*) from {$this->MAuth->getdb('ARRESV')} where RESVNO='".$arrs["resvno"]."' and isnull(RECVDT,'')='' and isnull(SDATE,'')='') > 0)
				begin 
					update {$this->MAuth->getdb('ARRESV')}
					set ISSUNO	= '".$arrs["issuno"]."',
						RECVDT	= GETDATE(),
						SDATE	= GETDATE()
					where RESVNO='".$arrs["resvno"]."'
				end
				else 
				begin
					rollback tran sellTran;
					insert into #sellTemp select 'E' as id,'','บันทึกไม่สำเร็จ ไม่สามารถใช้เลขที่ใบจอง ".$arrs["resvno"]." ได้ โปรดตรอจสอบใหม่อีกครั้ง' as msg;
					return;
				end;
			";
		}
		
		$arrs["nkang"] = $arrs["nkeyin"]-$arrs["npayres"];
		$arrs["vkang"] = $arrs["vkeyin"]-$arrs["vatpres"];
		$arrs["tkang"] = $arrs["tkeyin"]-$arrs["totpres"];
		
		$sql = "
			select * from {$this->MAuth->getdb('INVTRAN')}
			where STRNO='".$arrs["strno"]."'
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		$invtrn = array("STDPRC"=>0);
		if($query->row()){
			foreach($query->result() as $row){
				$arrs["ncarcst"] 	= $row->NETCOST;
				$arrs["vcarcst"] 	= $row->CRVAT;
				$arrs["tcarcst"] 	= $row->TOTCOST;
			}
		}
		
		$arrs["inopt"] 		= (isset($_REQUEST["inopt"]) ? $_REQUEST["inopt"] : array());
		$arrs['insertOpt'] 	= "";
		for($i = 0;$i < sizeof($arrs["inopt"]);$i++){
			$arrs["optprc"]  = $arrs["inopt"][$i][3];
			$arrs["optpvt"]  = $arrs["inopt"][$i][4];
			$arrs["optptot"] = $arrs["inopt"][$i][5];
			$arrs["optcst"]  = $arrs["inopt"][$i][6];
			$arrs["optcvt"]  = $arrs["inopt"][$i][7];
			$arrs["optctot"] = $arrs["inopt"][$i][8];
			
			$arrs['insertOpt'] .= "
				insert into {$this->MAuth->getdb('ARINOPT')} (
					[TSALE],[CONTNO],[LOCAT],[OPTCODE],[UPRICE]
					,[UCOST],[QTY],[TOTPRC],[TOTVAT],[NPRICE]
					,[OPTCST],[OPTCVT],[OPTCTOT],[CONFIR]
					,[USERID],[INPDT],[POSTDT],[SDATE],[RTNFLAG]
				) values (
					'".$arrs["tsale"]."',@contno,'".$arrs["locat"]."','".$arrs["inopt"][$i][0]."',".$arrs["inopt"][$i][1]."
					,0.00,".$arrs["inopt"][$i][2].",".$arrs["inopt"][$i][5].",".$arrs["inopt"][$i][4].",".$arrs["inopt"][$i][3]."
					,".$arrs["inopt"][$i][6].",".$arrs["inopt"][$i][7].",".$arrs["inopt"][$i][8].",''
					,'".$this->sess["USERID"]."',getdate(),null,'".$arrs["sdate"]."',''
				);
				
				update {$this->MAuth->getdb('OPTMAST')}
				set ONHAND=ONHAND-".$arrs["inopt"][$i][2]."
				where OPTCODE='".$arrs["inopt"][$i][0]."' and LOCAT='".$arrs["locat"]."';
			";
		}
		
		$arrs["reg"] 		= $_REQUEST["reg"];
		$arrs["dwninv"] 	= $_REQUEST["dwninv"];
		$arrs["dwninvDt"] 	= $this->Convertdate(1,$_REQUEST["dwninvDt"]);
		
		$data = "";
		if($arrs["acticod"] 	== ""){ $data = "กิจกรรมการขาย"; }
		if($arrs["issudt"] 		== ""){ $data = "วันที่ปล่อยรถ"; }
		if($arrs["salcod"] 		== ""){ $data = "รหัสพนักงานขาย"; }
		if($arrs["keyin"] 		== ""){ $data = "ราคาขายจริงรวมอุปกรณ์"; }
		if($arrs["stdprc"] 		== ""){ $data = "ราคาขายหน้าร้าน"; }
		if($arrs["strno"] 		== ""){	$data = "เลขตัวถัง"; }
		if($arrs["vatrt"] 		== ""){ $data = "อัตราภาษี"; }
		if($arrs["addrno"] 		== ""){ $data = "ที่อยู่ในการพิมพ์สัญญา"; }
		if($arrs["cuscod"] 		== ""){ $data = "รหัสลูกค้า"; }
		if($arrs["sdate"] 		== ""){ $data = "วันที่ขาย"; }
		
		if($data != ""){ 
			$response["status"] = 'W';
			$response["msg"] = "ไม่พบ{$data} โปรดระบุ{$data}ก่อนครับ";
			echo json_encode($response); exit; 
		}
		
		$arrs["billdas"] = $_REQUEST["billdas"];
		$dasSize = sizeof($arrs["billdas"]);
		$mapMEMO1 = "";
		for($i=0;$i<$dasSize;$i++){
			if($mapMEMO1 != ""){ $mapMEMO1 .= ","; }
			$mapMEMO1 .= $arrs["billdas"][$i];	
		}
		$arrs["memo1"] = $mapMEMO1."[explode]".$arrs["memo1"];		
		
		if($arrs["contno"] == 'Auto Genarate'){
			$this->saveinsell($arrs);
		}else{
			$this->updateinleasing($arrs);
		}
	}
	
	private function saveinsell($arrs){
		$sql = "
			if OBJECT_ID('tempdb..#sellTemp') is not null drop table #sellTemp;
			create table #sellTemp (id varchar(20),contno varchar(20),msg varchar(max));
			
			begin tran sellTran
			begin try
			
				/* @symbol = สัญลักษณ์แทนประเภทของเลขที่ นั้นๆ */
				declare @symbol varchar(10) = (select H_CASHNO from {$this->MAuth->getdb('CONDPAY')});
				/* @rec = รหัสพื้นฐาน */
				declare @rec varchar(10) = (select SHORTL+@symbol+'-'+right(left(convert(varchar(8),GETDATE(),112),6),4) from {$this->MAuth->getdb('INVLOCAT')} where LOCATCD='".$arrs['locat']."');
				/* @RESVNO = รหัสที่จะใช้ */
				declare @CONTNO varchar(12) = isnull((select MAX(CONTNO) from {$this->MAuth->getdb('ARCRED')} where CONTNO like ''+@rec+'%' collate thai_cs_as),@rec+'0000');
				set @CONTNO = left(@CONTNO,8)+right(right(@CONTNO,4)+10001,4);
				
				set @symbol = (select H_TXMAST from {$this->MAuth->getdb('CONDPAY')});
				set @rec = (select SHORTL+@symbol+'-'+right(left(convert(varchar(8),GETDATE(),112),6),4) from {$this->MAuth->getdb('INVLOCAT')} where LOCATCD='".$arrs["locat"]."');
				
				declare @TAXNO varchar(12) = isnull((select MAX(TAXNO) from {$this->MAuth->getdb('TAXTRAN')} where TAXNO like ''+@rec+'%' collate thai_cs_as),@rec+'0000');
				declare @TAXDT datetime = (select convert(varchar(8),getdate(),112));
				set @TAXNO = left(@TAXNO ,8)+right(right(@TAXNO ,4)+10001,4);
				
				if(".$arrs["vatrt"]." = '0.00')
				BEGIN
					set @TAXNO = null;
					set @TAXDT = null;
				END
				
				if ((select count(*) from {$this->MAuth->getdb('INVTRAN')}
					 where STRNO = '".$arrs["strno"]."' and FLAG='D' and SDATE is null and isnull(CONTNO,'')=''
						and ((CURSTAT = 'R' and RESVNO='".$arrs["resvno"]."') or (CURSTAT is null and RESVNO is null))
					) > 0 )
				begin
					insert into {$this->MAuth->getdb('ARCRED')} (
						CONTNO  ,LOCAT    ,RESVNO  ,CUSCOD  ,STRNO   ,
						INCLVAT ,SDATE    ,VATRT   ,STDPRC  ,DSCPRC  ,
						KEYIN   ,NKEYIN   ,VKEYIN  ,TKEYIN  ,NPRICE  ,
						VATPRC  ,TOTPRC   ,NPAYRES ,VATPRES ,TOTPRES ,
						SMPAY   ,SMCHQ    ,NKANG   ,VKANG   ,TKANG   ,
						NCARCST ,VCARCST  ,TCARCST ,TAXNO   ,TAXDT   ,
						CRDTXNO ,CRDAMT   ,OPTCST  ,OPTCVT  ,OPTCTOT ,
						OPTPRC  ,OPTPVT   ,OPTPTOT ,CREDTM  ,DUEDT   ,
						SALCOD  ,COMITN   ,ISSUNO  ,ISSUDT  ,TSALE   ,
						MEMO1   ,USERID   ,INPDT   ,DELID   ,FLCANCL ,
						APPVNO  ,PAYTYP   ,ADDRNO  ,COMEXT  ,COMOPT  ,
						COMOTH  ,RECOMCOD ,ACTICOD
					) values (
						@CONTNO				   ,'".$arrs["locat"]."'  	,'".$arrs["resvno"]."'	,'".$arrs["cuscod"]."'	,'".$arrs["strno"]."'	,
						'".$arrs["inclvat"]."' ,'".$arrs["sdate"]."'  	,'".$arrs["vatrt"]."'	,'".$arrs["stdprc"]."'	,'".$arrs["dscprc"]."'  ,
						'".$arrs["keyin"]."'   ,'".$arrs["nkeyin"]."' 	,'".$arrs["vkeyin"]."'	,'".$arrs["tkeyin"]."'	,'".$arrs["nprice"]."'  ,
						'".$arrs["vatprc"]."'  ,'".$arrs["totprc"]."' 	,'".$arrs["npayres"]."'	,'".$arrs["vatpres"]."'	,'".$arrs["totpres"]."' ,
						'".$arrs["smpay"]."'   ,'".$arrs["smchq"]."'  	,'".$arrs["nkang"]."'	,'".$arrs["vkang"]."'	,'".$arrs["tkang"]."'   ,
						'".$arrs["ncarcst"]."' ,'".$arrs["vcarcst"]."'	,'".$arrs["tcarcst"]."'	,@TAXNO					,@TAXDT				    ,
						'".$arrs["crdtxno"]."' ,'".$arrs["crdamt"]."'	,'".$arrs["optcst"]."'	,'".$arrs["optcvt"]."'	,'".$arrs["optctot"]."' ,
						'".$arrs["optprc"]."'  ,'".$arrs["optpvt"]."'	,'".$arrs["optptot"]."'	,'".$arrs["credtm"]."'	,'".$arrs["duedt"]."'   ,				
						'".$arrs["salcod"]."'  ,'".$arrs["comitn"]."'	,'".$arrs["issuno"]."'	,'".$arrs["issudt"]."'	,'".$arrs["tsale"]."'   ,
						'".$arrs["memo1"]."'   ,'".$arrs["userid"]."'	,".$arrs["inpdt"]."  	,'".$arrs["delid"]."'	,'".$arrs["flcancl"]."' ,
						'".$arrs["appvno"]."'  ,'".$arrs["paytyp"]."'	,'".$arrs["addrno"]."'	,'".$arrs["comext"]."'	,'".$arrs["comopt"]."'  ,
						'".$arrs["comoth"]."'  ,'".$arrs["recomcod"]."'	,'".$arrs["acticod"]."'
					);
				end 
				else 
				begin 
					rollback tran sellTran;
					insert into #sellTemp select 'E' as id,'','บันทึกไม่สำเร็จ ไม่สามารถใช้เลขที่ตัวถัง ".$arrs["strno"]." ได้  อาจมีการขายไปแล้ว หรือมีการจองแล้วครับ โปรดตรอจสอบใหม่อีกครั้ง' as msg;
					return;
				end;
				
				/* INVTRAN */
				if ((select count(*) from {$this->MAuth->getdb('INVTRAN')}
					 where STRNO = '".$arrs["strno"]."' and FLAG='D' and SDATE is null and isnull(CONTNO,'')=''
						and ((CURSTAT = 'R' and RESVNO='".$arrs["resvno"]."') or (CURSTAT is null and RESVNO is null))
					) > 0 )
				begin 
					update {$this->MAuth->getdb('INVTRAN')}
					set FLAG 	= 'C',
						SDATE 	= '".$arrs["sdate"]."',
						PRICE 	= ".$arrs["keyin"].",
						CONTNO 	= @CONTNO,
						TSALE 	= 'C'
					where STRNO = '".$arrs["strno"]."' and CRLOCAT = '".$arrs["locat"]."' and FLAG = 'D' and isnull(CONTNO,'')=''
				end 
				else 
				begin 
					rollback tran sellTran;
					insert into #sellTemp select 'E' as id,'','บันทึกไม่สำเร็จ ไม่สามารถใช้เลขที่ตัวถัง ".$arrs["strno"]." ได้  อาจมีการขายไปแล้ว หรือมีการจองแล้วครับ โปรดตรอจสอบใหม่อีกครั้ง' as msg;
					return;
				end;
				
				
				/* TAXTRAN */
				if(@TAXNO is not null)
				BEGIN
					insert into {$this->MAuth->getdb('TAXTRAN')}(
						[LOCAT],[TAXNO],[TAXDT],[TSALE],[CONTNO],
						[CUSCOD],[SNAM],[NAME1],[NAME2],[STRNO],
						[REFNO],[REFDT],[VATRT],[NETAMT],[VATAMT],
						[TOTAMT],[DESCP],[FPAR],[FPAY],[LPAR],
						[LPAY],[INPDT],[FLAG],[CANDT],[TAXTYP],
						[TAXFLG],[USERID],[FLCANCL],[TMBILL],[RTNSTK],
						[FINCOD],[DOSTAX],[PAYFOR],[RESONCD],[INPTIME]
					) values (
						'".$arrs["locat"]."',@TAXNO,@TAXDT,'".$arrs["tsale"]."',@CONTNO,
						'".$arrs["cuscod"]."','','','','".$arrs["strno"]."',
						'',null,'".$arrs["vatrt"]."',".str_replace(",","",number_format($arrs["nprice"],2)).",".str_replace(",","",number_format($arrs["vatprc"],2)).",
						".str_replace(",","",number_format($arrs["totprc"],2)).",'ใบกำกับขายสด/เชื่อ','',0,'',
						0,getdate(),'',null,'S',
						'N','".$arrs["userid"]."','','','',
						'','','','',null
					);
				END;				
				
				/* ใบจอง  ARRESV */
					".$arrs["updateARRESV"]."
					
				/*อุปกรณ์เสริมรวมราคารถ ARINOPT */
					DISABLE Trigger ALL ON {$this->MAuth->getdb('ARINOPT')};
					".$arrs['insertOpt']."
					ENABLE Trigger ALL ON {$this->MAuth->getdb('ARINOPT')};
				
				insert into {$this->MAuth->getdb('hp_UserOperationLog')} (userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
				values ('".$this->sess["IDNo"]."','SYS04::บันทึกขายสดแล้ว',@CONTNO+' ".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');

				insert into #sellTemp select 'S',@CONTNO,'บันทึกรายการขายสด เลขที่สัญญา '+@CONTNO+' แล้วครับ';
				commit tran sellTran;
			end try
			begin catch
				rollback tran sellTran;
				insert into #sellTemp select 'E','',ERROR_MESSAGE();
			end catch
		";
		//echo $sql; exit;
		
		$this->db->query($sql);
		$sql = "select * from #sellTemp";
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
			$response["msg"] = 'ผิดพลาดไม่สามารถบันทึกการขายได้ โปรดติดต่อฝ่ายไอที';
		}
		
		echo json_encode($response);
	}
	
	private function updateinleasing($arrs){
		if($arrs["contno"] == ''){
			$response["status"] = false;
			$response["msg"] = 'ผิดพลาดไม่สามารถแก้ไขการขายได้ เนื่องจากไม่พบเลขที่สัญญาครับ';
			echo json_encode($response); exit;
		}
		
		$sql = "
			if OBJECT_ID('tempdb..#leasingTemp') is not null drop table #leasingTemp;
			create table #leasingTemp (id varchar(20),contno varchar(20),msg varchar(max));
			
			begin tran leasingTran
			begin try
				declare @CONTNO varchar(20) = '".$arrs["contno"]."';
				
				update {$this->MAuth->getdb('ARMAST')}
				set PAYTYP='".$arrs["paydue"]."'
					,BILLCOLL='".$arrs["billcoll"]."'
					,CHECKER='".$arrs["checker"]."'
					,INTRT='".$arrs["intrt"]."'
					,DELYRT='".$arrs["delyrt"]."'
					,DLDAY='".$arrs["dlday"]."'
					,COMITN='".$arrs["comitn"]."'
					,ACTICOD='".$arrs["acticod"]."'
					,RECOMCOD='".$arrs["recomcod"]."'
					,COMEXT='".$arrs["comext"]."'
					,COMOPT='".$arrs["comopt"]."'
					,COMOTH='".$arrs["comoth"]."'
					,CALINT='".$arrs["calint"]."'
					,CALDSC='".$arrs["discfm"]."'
					,MEMO1='".$arrs["comments"]."'
				where CONTNO=@CONTNO
				
				/* คนค้ำประกัน  ARMGAR */
					delete {$this->MAuth->getdb('ARMGAR')} where CONTNO=@CONTNO
					".$arrs["insertARMGAR"]."
					
				/* หลักทรัพย์ประกัน  AROTHGAR */
					delete {$this->MAuth->getdb('AROTHGAR')} where CONTNO=@CONTNO
					".$arrs["insertAROTHGAR"]."	
				
				insert into {$this->MAuth->getdb('hp_UserOperationLog')} (userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
				values ('".$this->sess["IDNo"]."','SYS04::บันทึกขายผ่อน (แก้ไข)',' ".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');

				insert into #leasingTemp select 'S',@CONTNO,'บันทึกรายการขายสำเร็จ เลขที่สัญญา '+@CONTNO;
				commit tran leasingTran;
			end try
			begin catch
				rollback tran leasingTran;
				insert into #leasingTemp select 'E','',ERROR_MESSAGE();
			end catch
		";
		
		$this->db->query($sql);
		$sql = "select * from #leasingTemp";
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
			$response["msg"] = 'ผิดพลาดไม่สามารถบันทึกการขายได้ โปรดติดต่อฝ่ายไอที';
		}
		
		echo json_encode($response);
	}
	
	function deleteContno(){
		$contno = $_REQUEST['contno'];
		
		$sql = "
			if OBJECT_ID('tempdb..#leasingTemp') is not null drop table #leasingTemp;
			create table #leasingTemp (id varchar(20),contno varchar(20),msg varchar(max));
			
			begin tran leasingTran
			begin try
				declare @CONTNO varchar(20) = '".$contno."';
				
				update {$this->MAuth->getdb('INVTRAN')} 
				set SDATE=null,PRICE=null,CONTNO=null,FLAG='D'
				where CONTNO=@CONTNO
				
				delete from {$this->MAuth->getdb('AROTHGAR')}  
				where CONTNO=@CONTNO
				
				delete from {$this->MAuth->getdb('ARMGAR')}  
				where CONTNO=@CONTNO
				
				delete from {$this->MAuth->getdb('ARINOPT')}  
				where CONTNO=@CONTNO
				
				delete from {$this->MAuth->getdb('TAXTRAN')}  
				where TAXNO=(select TAXNO from {$this->MAuth->getdb('ARMAST')} where CONTNO=@CONTNO)
				
				delete from {$this->MAuth->getdb('ARPAY')} where CONTNO=@CONTNO
				
				update {$this->MAuth->getdb('ARRESV')}
				set ISSUNO	= '',
					RECVDT	= null,
					SDATE	= null
				where RESVNO=(select RESVNO from {$this->MAuth->getdb('ARMAST')} where CONTNO=@CONTNO)
				
				delete from {$this->MAuth->getdb('ARMAST')}
				where CONTNO=@CONTNO
				
				insert into {$this->MAuth->getdb('hp_UserOperationLog')} (userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
				values ('".$this->sess["IDNo"]."','SYS04::ลบการขายผ่อน',' ".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');

				insert into #leasingTemp select 'S',@CONTNO,'ลบรายการขาย เลขที่สัญญา '+@CONTNO+' แล้ว';
				commit tran leasingTran;
			end try
			begin catch
				rollback tran leasingTran;
				insert into #leasingTemp select 'E','',ERROR_MESSAGE();
			end catch
		";
		//echo $sql; exit;
		$this->db->query($sql);
		$sql = "select * from #leasingTemp";
		$query = $this->db->query($sql);
	  
		if($query->row()){
			foreach($query->result() as $row){
				$response["status"] = $row->id;
				$response["contno"] = $row->contno;
				$response["msg"] = $row->msg;
			}
		}else{
			$response["status"] = 'E';
			$response["contno"] = '';
			$response["msg"] = 'ผิดพลาดไม่สามารถลบการขายได้ โปรดติดต่อฝ่ายไอที';
		}
		
		echo json_encode($response);
	}
	
	function approvepdf(){
		echo 'อยู่ระหว่างการพัฒนาโปรแกรม'; exit;
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
		
		/*
		$data = array(
			 0=>"อ้างอิงถึงใบสั่งจองรถเลขที่"	,1=>"สาขา"					,2=>"ประเภทการขาย"
			,3=>"วันที่รับรถ"				,4=>"วันเริ่มต้นสัญญา"				,5=>"เลขที่สัญญา"
			,6=>"วันที่สิ้นสุดสัญญา"		,7=>"ชื่อลูกค้า"					,8=>"ที่อยู่"
			,9=>"หมายเหตุ"				,10=>"ผู้ค้ำประกันคนที่ 1"			,11=>"ที่อยู่"
			,12=>"ผู้ค้ำประกันคนที่ 2"		,13=>"ที่อยู่"					,14=>"ราคาขายสดหน้าร้าน"
			,15=>"ยี่ห้อ"				,16=>""						,17=>"รุ่น"
			,18=>"ราคาขายเงินสดสุทธิ"		,19=>"แบบ"					,20=>"เงินดาวน์"
			,21=>"สี"					,22=>"ราคาขายสดสุทธิหักเงินดาวน์"	,23=>"หมายเลขทะเบียน"
			,24=>"อัตราดอกเบี้ยเช่าซื้อ"		,25=>"หมายเลขตัวถัง"				,26=>"อัตราดอกเบี้ยเช่าซื้อจริง"
			,27=>"หมายเลขเครื่อง"			,28=>"ราคาขายรวมภาษี"			,29=>"พนักงานขาย"
			,30=>"ผ่อนชำระงวดละ"			,31=>"ยอดจัดกรณีเข้าไฟแนนซ์"		,32=>"กรณีเงินสด/เงินเชื่อ"
			,33=>"กรณีเช่าซื้อ/ขายผ่อน"		,34=>"กรณีขายไฟแนนซ์"			,35=>"หักเงินมัดจำ"
			,36=>"ต้องชำระทั้งสิ้น"			,37=>"ชำระจริง"					,38=>"คงค้าง"
			,39=>"ผ่อนจำนวน"			,40=>"ยอดตั้งลูกหนี้"
		);
		*/
		
		$sql = "
			select a.RESVNO 
				,(select LOCATNM from INVLOCAT where LOCATCD=a.LOCAT) as LOCAT
				,case when a.TSALE = 'H' then 'ขายผ่อน' else a.TSALE end TSALE
				,convert(varchar(8),b.RECVDT,112) as RECVDT
				,convert(varchar(8),a.SDATE,112) as SDATE
				,convert(varchar(8),a.DUEDT,112) as LDATE
				,a.CONTNO
				,a.CUSCOD
				,(select 'คุณ'+NAME1+' '+NAME2 from {$this->MAuth->getdb('CUSTMAST')} where CUSCOD=a.CUSCOD) as CUSNAME
				,(select ADDR1+' '+ADDR2+' '+TUMB
					+' '+(select AUMPDES from {$this->MAuth->getdb('SETAUMP')} where AUMPCOD=ca.AUMPCOD and PROVCOD=ca.PROVCOD)
					+' '+(select PROVDES from {$this->MAuth->getdb('SETPROV')} where PROVCOD=ca.PROVCOD)
					+' '+ZIP
				  from {$this->MAuth->getdb('CUSTADDR')} ca
				  where CUSCOD=a.CUSCOD and ADDRNO=(select ADDRNO from {$this->MAuth->getdb('CUSTMAST')} where CUSCOD=a.CUSCOD)
				 ) CUSADDR
				,a.MEMO1
				
				,a.STDPRC
				,a.DSCPRC
				,a.KEYIN
				,0 TOTDWN
				,a.TOTPRC - a.SMPAY as DTPD
				,0 INTRT
				,0 EFRATE
				,a.TOTPRC
				,0 T_FUPAY
				,0 as fn
				
				,b.TYPE
				,b.MODEL
				,b.BAAB
				,b.COLOR
				,b.REGNO
				,b.STRNO
				,b.ENGNO
				,(select USERNAME from {$this->MAuth->getdb('PASSWRD')} where USERID = a.SALCOD) as SALCOD
				
				,0 as CRED
				,0 as HP
				,0 as FN
				,a.TOTPRES
				,0 as TOTDR
				,0 as PAYDWN
				,0 as REV
				,0 as T_NOPAY
				,a.TKANG
				
			from {$this->MAuth->getdb('ARCRED')} a
			left join {$this->MAuth->getdb('INVTRAN')} b on a.STRNO=b.STRNO
			where a.CONTNO='".$_REQUEST['contno']."'
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$data = array();
		if($query->row()){
			foreach($query->result() as $row){
				$data[0] = $row->RESVNO;
				$data[1] = $row->LOCAT;
				$data[2] = $row->TSALE;
				$data[3] = $this->Convertdate(2,$row->RECVDT);
				$data[4] = $this->Convertdate(2,$row->SDATE);
				$data[5] = $row->CONTNO;
				$data[6] = $this->Convertdate(2,$row->LDATE);
				$data[7] = $row->CUSNAME;
				$data[8] = $row->CUSADDR;
				$data[9] = str_replace("[explode]"," ",$row->MEMO1);
				
				$data[14] = number_format($row->STDPRC,2);
				$data[16] = number_format(0,2);
				$data[18] = number_format($row->KEYIN,2);
				$data[20] = number_format($row->TOTDWN,2);
				$data[22] = number_format($row->DTPD,2);
				$data[24] = $row->INTRT;
				$data[26] = $row->EFRATE;
				$data[28] = number_format($row->TOTPRC,2);
				$data[30] = number_format($row->T_FUPAY,2);
				$data[31] = number_format($row->fn,2);
				
				$data[15] = $row->TYPE;
				$data[17] = $row->MODEL;
				$data[19] = $row->BAAB;
				$data[21] = $row->COLOR;
				$data[23] = $row->REGNO;
				$data[25] = $row->STRNO;
				$data[27] = $row->ENGNO;
				$data[29] = $row->SALCOD;				
				
				$data[32] = number_format($row->CRED,2);
				$data[33] = number_format($row->HP,2);
				$data[34] = number_format($row->FN,2);
				$data[35] = number_format($row->TOTPRES,2);
				$data[36] = number_format($row->TOTDR,2);
				$data[37] = number_format($row->PAYDWN,2);
				$data[38] = number_format($row->REV,2);
				$data[39] = number_format($row->T_NOPAY,0);
				$data[40] = number_format($row->TKANG,2);
			}
		}
		
		$sql = "
			select top 2 GARNO
				,(select 'คุณ'+NAME1+' '+NAME2 from {$this->MAuth->getdb('CUSTMAST')} where CUSCOD=a.CUSCOD) as CUSNAME
				,(select ADDR1+' '+ADDR2+' '+TUMB
					+' '+(select AUMPDES from {$this->MAuth->getdb('SETAUMP')} where AUMPCOD=ca.AUMPCOD and PROVCOD=ca.PROVCOD)
					+' '+(select PROVDES from {$this->MAuth->getdb('SETPROV')} where PROVCOD=ca.PROVCOD)
					+' '+ZIP
				  from {$this->MAuth->getdb('CUSTADDR')} ca
				  where CUSCOD=a.CUSCOD and ADDRNO=(select ADDRNO from {$this->MAuth->getdb('CUSTMAST')} where CUSCOD=a.CUSCOD)
				 ) CUSADDR 
			from {$this->MAuth->getdb('ARMGAR')} a
			left join {$this->MAuth->getdb('CUSTMAST')} b on a.CUSCOD=b.CUSCOD
			where CONTNO='".$_REQUEST['contno']."'
			order by GARNO
		";
		$query = $this->db->query($sql);
		
		if($query->row()){
			$size = 1;
			foreach($query->result() as $row){
				if($size == 1){
					$data[10] = $row->CUSNAME;
					$data[11] = $row->CUSADDR;
					$size++;
				}else{
					$data[12] = $row->CUSNAME;
					$data[13] = $row->CUSADDR;
				}
			}
		}
		
		$sql = "
			select b.FORDESC,a.PAYAMT from {$this->MAuth->getdb('AROTHR')} a
			left join {$this->MAuth->getdb('PAYFOR')} b on a.PAYFOR=b.FORCODE
			where CONTNO='".$_REQUEST['contno']."'
		";
		
		$query = $this->db->query($sql);
		
		$other = "";
		$top = 715;
		$sum = 0;
		if($query->row()){
			foreach($query->result() as $row){
				$other .= "<div class='wf pf' style='top:".$top.";left:0;font-size:10pt;'>".$row->FORDESC."</div>";
				$other .= "<div class='wf pf data' style='top:".$top.";left:200;font-size:10pt;width:140px;text-align:right;'>".number_format($row->PAYAMT,2)."</div>";
				$other .= "<div class='wf pf' style='top:".$top.";left:360;font-size:10pt;'>บาท</div>";
				$sum = $sum + $row->PAYAMT;
				$top += 30;
			}
		}
		
		if($top != 715){
			$top = ($top < 955 ? 955 : $top+30);
			$other .= "<div class='wf pf' style='top:".$top.";left:0;font-size:10pt;'>รวม</div>";
			$other .= "<div class='wf pf data' style='top:".$top.";left:200;font-size:10pt;width:140px;text-align:right;'>".number_format($sum,2)."</div>";
			$other .= "<div class='wf pf' style='top:".$top.";left:360;font-size:10pt;'>บาท</div>";			
		}
		
		for($x=0;$x <= 40;$x++){
			$data[$x] = (!isset($data[$x]) ? "" : $data[$x]);
		}
		
		$content = "
			<div class='wf pf' style='top:45;left:290;font-size:12pt;'><b><u>ใบอนุมัติการขาย</u></b></div>
			<div class='wf pf' style='top:85;left:0;font-size:10pt;'>อ้างอิงถึงใบสั่งจองรถเลขที่</div>
			<div class='wf pf data' style='top:85;left:150;width:235px;'>{$data[0]}</div>
			
			<div class='wf pf' style='top:115;left:0;font-size:10pt;'>สาขา</div>
			<div class='wf pf data' style='top:115;left:35;width:350px;'>{$data[1]}</div>
			<div class='wf pf' style='top:115;left:480;font-size:10pt;'>ประเภทการขาย</div>
			<div class='wf pf data' style='top:115;left:610;width:80px;'>{$data[2]}</div>
			
			<div class='wf pf' style='top:145;left:0;font-size:10pt;'>วันที่รับรถ</div>
			<div class='wf pf data' style='top:145;left:65;width:320px;'>{$data[3]}</div>
			<div class='wf pf' style='top:145;left:480;font-size:10pt;'>วันเริ่มต้นสัญญา</div>
			<div class='wf pf data' style='top:145;left:610;width:80px;'>{$data[4]}</div>
			
			<div class='wf pf' style='top:175;left:0;font-size:10pt;'>เลขที่สัญญา</div>
			<div class='wf pf data' style='top:175;left:75;width:310px;'>{$data[5]}</div>
			<div class='wf pf' style='top:175;left:480;font-size:10pt;'>วันที่สิ้นสุดสัญญา</div>
			<div class='wf pf data' style='top:175;left:610;width:80px;'>{$data[6]}</div>
			
			<div class='wf pf' style='top:205;left:0;font-size:10pt;'>ชื่อลูกค้า</div>
			<div class='wf pf data' style='top:205;left:55;width:635px;'>{$data[7]}</div>
			
			<div class='wf pf' style='top:235;left:0;font-size:10pt;'>ที่อยู่</div>
			<div class='wf pf data' style='top:235;left:30;width:355px;height:55px;'>{$data[8]}</div>
			<div class='wf pf' style='top:235;left:400;font-size:10pt;'>หมายเหตุ</div>
			<div class='wf pf data' style='top:235;left:460;width:230px;height:145px;'>{$data[9]}</div>
			
			<div class='wf pf' style='top:295;left:0;font-size:10pt;'>ผู้ค้ำประกันคนที่ 1</div>
			<div class='wf pf data' style='top:295;left:110;width:275px;'>{$data[10]}</div>
			<div class='wf pf' style='top:325;left:0;font-size:10pt;'>ที่อยู่</div>
			<div class='wf pf data' style='top:325;left:30;width:355px;'>{$data[11]}</div>
			
			<div class='wf pf' style='top:355;left:0;font-size:10pt;'>ผู้ค้ำประกันคนที่ 2</div>
			<div class='wf pf data' style='top:355;left:110;width:275px;'>{$data[12]}</div>
			<div class='wf pf' style='top:385;left:0;font-size:10pt;'>ที่อยู่</div>
			<div class='wf pf data' style='top:385;left:30;width:355px;'>{$data[13]}</div>
			
			<div class='wf pf' style='top:415;left:0;font-size:10pt;'>ราคาขายสดหน้าร้าน</div>
			<div class='wf pf data' style='top:415;left:200;width:140px;text-align:right;'>{$data[14]}</div>
			<div class='wf pf' style='top:415;left:360;font-size:10pt;'>บาท</div>
			<div class='wf pf' style='top:415;left:520;font-size:10pt;'><b><u>รายละเอียดรถ</u></b></div>
			<div class='wf pf' style='top:445;left:420;font-size:10pt;width:100px;text-align:left;'>ยี่ห้อ</div>
			<div class='wf pf data' style='top:445;left:530;width:160px;'>{$data[15]}</div>
			
			<div class='wf pf data' style='top:445;left:200;width:140px;text-align:right;'>{$data[16]}</div>
			<div class='wf pf' style='top:445;left:360;font-size:10pt;'>บาท</div>
			<div class='wf pf' style='top:475;left:420;font-size:10pt;width:100px;text-align:left;'>รุ่น</div>
			<div class='wf pf data' style='top:475;left:530;width:160px;'>{$data[17]}</div>
			
			<div class='wf pf' style='top:475;left:0;font-size:10pt;'>ราคาขายเงินสดสุทธิ</div>
			<div class='wf pf data' style='top:475;left:200;width:140px;text-align:right;'>{$data[18]}</div>
			<div class='wf pf' style='top:475;left:360;font-size:10pt;'>บาท</div>
			<div class='wf pf' style='top:505;left:420;font-size:10pt;width:100px;text-align:left;'>แบบ</div>
			<div class='wf pf data' style='top:505;left:530;width:160px;'>{$data[19]}</div>
			
			<div class='wf pf' style='top:505;left:0;font-size:10pt;'>เงินดาวน์</div>
			<div class='wf pf data' style='top:505;left:200;width:140px;text-align:right;'>{$data[20]}</div>
			<div class='wf pf' style='top:505;left:360;font-size:10pt;'>บาท</div>
			<div class='wf pf' style='top:535;left:420;font-size:10pt;width:100px;text-align:left;'>สี</div>
			<div class='wf pf data' style='top:535;left:530;width:160px;'>{$data[21]}</div>
			
			<div class='wf pf' style='top:535;left:0;font-size:10pt;'>ราคาขายสดสุทธิหักเงินดาวน์</div>
			<div class='wf pf data' style='top:535;left:200;width:140px;text-align:right;'>{$data[22]}</div>
			<div class='wf pf' style='top:535;left:360;font-size:10pt;'>บาท</div>
			<div class='wf pf' style='top:565;left:420;font-size:10pt;width:100px;text-align:left;'>หมายเลขทะเบียน</div>
			<div class='wf pf data' style='top:565;left:530;width:160px;'>{$data[23]}</div>
			
			<div class='wf pf' style='top:565;left:0;font-size:10pt;'>อัตราดอกเบี้ยเช่าซื้อ</div>
			<div class='wf pf data' style='top:565;left:200;width:140px;text-align:right;'>{$data[24]}</div>
			<div class='wf pf' style='top:565;left:360;font-size:10pt;'>%</div>
			<div class='wf pf' style='top:595;left:420;font-size:10pt;width:100px;text-align:left;'>หมายเลขตัวถัง</div>
			<div class='wf pf data' style='top:595;left:530;width:160px;'>{$data[25]}</div>
			
			<div class='wf pf' style='top:595;left:0;font-size:10pt;'>อัตราดอกเบี้ยเช่าซื้อจริง</div>
			<div class='wf pf data' style='top:595;left:200;width:140px;text-align:right;'>{$data[26]}</div>
			<div class='wf pf' style='top:595;left:360;font-size:10pt;'>%</div>
			<div class='wf pf' style='top:625;left:420;font-size:10pt;width:100px;text-align:left;'>หมายเลขเครื่อง</div>
			<div class='wf pf data' style='top:625;left:530;width:160px;'>{$data[27]}</div>
			
			<div class='wf pf' style='top:625;left:0;font-size:10pt;'>ราคาขายรวมภาษี</div>
			<div class='wf pf data' style='top:625;left:200;width:140px;text-align:right;'>{$data[28]}</div>
			<div class='wf pf' style='top:625;left:360;font-size:10pt;'>บาท</div>
			<div class='wf pf' style='top:655;left:420;font-size:10pt;width:100px;text-align:left;'>พนักงานขาย</div>
			<div class='wf pf data' style='top:655;left:530;width:160px;'>{$data[29]}</div>
			
			<div class='wf pf' style='top:655;left:0;font-size:10pt;'>ผ่อนชำระงวดละ</div>
			<div class='wf pf data' style='top:655;left:200;width:140px;text-align:right;'>{$data[30]}</div>
			<div class='wf pf' style='top:655;left:360;font-size:10pt;'>บาท</div>
			
			<div class='wf pf' style='top:685;left:0;font-size:10pt;'>ยอดจัดกรณีเข้าไฟแนนซ์</div>
			<div class='wf pf data' style='top:685;left:200;width:140px;text-align:right;'>{$data[31]}</div>
			<div class='wf pf' style='top:685;left:360;font-size:10pt;'>บาท</div>
			<div class='wf pf' style='top:715;left:470;font-size:10pt;'><u>จำนวนเงินที่ต้องชำระในวันรับรถ</u></div>
			
			<div class='wf pf' style='top:745;left:400;font-size:10pt;'>กรณีเงินสด/เงินเชื่อ</div>
			<div class='wf pf data' style='top:745;left:520;width:135px;text-align:right;'>{$data[32]}</div>
			<div class='wf pf' style='top:745;left:670;font-size:10pt;'>บาท</div>
			
			<div class='wf pf' style='top:775;left:400;font-size:10pt;'>กรณีเช่าซื้อ/ขายผ่อน</div>
			<div class='wf pf data' style='top:775;left:520;width:135px;text-align:right;'>{$data[33]}</div>
			<div class='wf pf' style='top:775;left:670;font-size:10pt;'>บาท</div>
			
			<div class='wf pf' style='top:805;left:400;font-size:10pt;'>กรณีขายไฟแนนซ์</div>
			<div class='wf pf data' style='top:805;left:520;width:135px;text-align:right;'>{$data[34]}</div>
			<div class='wf pf' style='top:805;left:670;font-size:10pt;'>บาท</div>
			
			<div class='wf pf' style='top:835;left:400;font-size:10pt;'>หักเงินมัดจำ</div>
			<div class='wf pf data' style='top:835;left:520;width:135px;text-align:right;'>{$data[35]}</div>
			<div class='wf pf' style='top:835;left:670;font-size:10pt;'>บาท</div>
			
			<div class='wf pf' style='top:865;left:400;font-size:10pt;'>ต้องชำระทั้งสิ้น</div>
			<div class='wf pf data' style='top:865;left:520;width:135px;text-align:right;'>{$data[36]}</div>
			<div class='wf pf' style='top:865;left:670;font-size:10pt;'>บาท</div>
			
			<div class='wf pf' style='top:895;left:420;font-size:10pt;'>ชำระจริง</div>
			<div class='wf pf data' style='top:895;left:520;width:135px;text-align:right;'>{$data[37]}</div>
			<div class='wf pf' style='top:895;left:670;font-size:10pt;'>บาท</div>
			
			<div class='wf pf' style='top:925;left:420;font-size:10pt;'>คงค้าง</div>
			<div class='wf pf data' style='top:925;left:520;width:135px;text-align:right;'>{$data[38]}</div>
			<div class='wf pf' style='top:925;left:670;font-size:10pt;'>บาท</div>
			
			<div class='wf pf' style='top:955;left:400;font-size:10pt;'>ผ่อนจำนวน</div>
			<div class='wf pf data' style='top:955;left:520;width:135px;text-align:right;'>{$data[39]}</div>
			<div class='wf pf' style='top:955;left:670;font-size:10pt;'>งวด</div>
			
			<div class='wf pf' style='top:985;left:420;font-size:10pt;'>ยอดตั้งลูกหนี้</div>
			<div class='wf pf data' style='top:985;left:520;width:135px;text-align:right;'>{$data[40]}</div>
			<div class='wf pf' style='top:985;left:670;font-size:10pt;'>บาท</div>
		";
		
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




















