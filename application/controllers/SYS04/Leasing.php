<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/********************************************************
             ______@08/03/2019______
            / / _ _   _ _     __ 
           / // __ \ / __ \ / __ \
       _ _/ // /_/ // / / // /_/ /
     /_ _ _/ \_ _ //_/ /_/ \__  /
                          _ _/ /
                         /___ /
********************************************************/
class Leasing extends MY_Controller {
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
			<div class='tab1' name='home' locat='{$this->sess['branch']}' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}'>
				<div class='col-sm-12'>
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
								<input type='text' id='SDATEFRM' class='form-control input-sm' placeholder='จาก' data-provide='datepicker' data-date-language='th-th' value='".$this->today('startofmonthB1')."'>
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
								<button id='btnt1leasing' class='btn btn-cyan btn-block'><span class='glyphicon glyphicon-pencil'> ทำรายการขายผ่อน</span></button>
							</div>
						</div>
						<div class=' col-sm-6'>	
							<div class='form-group'>
								<button id='btnt1search' class='btn btn-primary btn-block'><span class='glyphicon glyphicon-search'> ค้นหา</span></button>
							</div>
						</div>
					</div>
					<div class='row'>	
						<div id='jd_result' class='col-sm-12'></div>
					</div>				
				</div>
			</div>
		";
		
		$html.= "<script src='".base_url('public/js/SYS04/Leasing.js')."'></script>";
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
			FROM {$this->MAuth->getdb('ARMAST')} A
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
							<i class='leasingDetails btn btn-xs btn-success glyphicon glyphicon-zoom-in' contno='".$row->CONTNO."' style='cursor:pointer;'> รายละเอียด  </i>
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
			<div id='table-fixed-LeasingCar' class='col-sm-12' style='height:calc(100% - 30px);width:100%;overflow:auto;font-size:8pt;'>
				<table id='table-LeasingCar' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='100%' border=1>
					<thead style='background: rgba(0, 0, 0, 0) url(&#39;../public/lobiadmin-master/version/1.0/ajax/img/bg/bg6.png&#39;) repeat scroll 0% 0%;'>						
						<tr style='line-height:20px;'>
							<td style='vertical-align:middle;text-align:center;font-size:8pt;' colspan='8'>
								เงื่อนไข :: {$condDesc}
							</td>
						</tr>
						<tr>
							<th style='vertical-align:middle;'>#</th>
							<th style='vertical-align:middle;'>เลขที่สัญญา</th>
							<th style='vertical-align:middle;'>สาขา</th>
							<th style='vertical-align:middle;'>วันที่ขาย</th>
							<th style='vertical-align:middle;'>รหัสลูกค้า</th>
							<th style='vertical-align:middle;'>ชื่อ-สกุล</th>
							<th style='vertical-align:middle;'>เลขตัวถัง</th>
							<th style='vertical-align:middle;'>เลขที่ใบจอง</th>
						</tr>
					</thead>
					<tbody>
						".$html."
					</tbody>
				</table>
			</div>
			<!-- div>
				<img src='".base_url("/public/images/excel.png")."'  onclick=\"tableToExcel('table-LeasingCar', 'exporttoexcell');\" style='width:25px;height:25px;cursor:pointer;'/>
			</div -->
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
	
	function loadLeasing(){
		$contno = $_REQUEST['contno'];
		
		$sql = "
			select a.CONTNO,a.LOCAT,convert(varchar(8),a.SDATE,112) as SDATE ,a.RESVNO
				,a.APPVNO,a.CUSCOD,b.SNAM+b.NAME1+' '+b.NAME2+' ('+b.CUSCOD+')'+'-'+b.GRADE as CUSNAME
				,a.INCLVAT,a.VATRT,a.ADDRNO,(select '('+ADDRNO+') '+ADDR1+' '+ADDR2+' '+TUMB from {$this->MAuth->getdb('CUSTADDR')} where CUSCOD=a.CUSCOD and ADDRNO=a.ADDRNO) as ADDRDetail
				,a.STRNO,a.PAYTYP,'('+c.PAYCODE+') '+c.PAYDESC PAYDESC
				,a.OPTCTOT,a.OPTPTOT
				,a.KEYINPRC,a.KEYINDWN,a.TAXNO,convert(varchar(8),a.TAXDT,112) as TAXDT,a.T_NOPAY,a.T_UPAY
				,a.KEYINFUPAY,a.KEYINUPAY,a.T_LUPAY,a.STDPRC,a.KEYINCSHPRC,a.NPROFIT
				,convert(varchar(8),a.FDATE,112) as FDATE
				,convert(varchar(8),a.LDATE,112) as LDATE
				,a.ISSUNO,convert(varchar(8),a.ISSUDT,112) as ISSUDT
				,a.BILLCOLL,a.CHECKER,a.SALCOD,a.DELYRT,a.DLDAY,a.INTRT,a.EFRATE,a.COMITN
				,a.ACTICOD,(select '('+ACTICOD+') '+ACTIDES from {$this->MAuth->getdb('SETACTI')} where 1=1 and ACTICOD=a.ACTICOD) as ACTINAME
				,(select USERNAME+' ('+USERID+')' from {$this->MAuth->getdb('PASSWRD')} where USERID=a.BILLCOLL) as BILLNAME
				,(select USERNAME+' ('+USERID+')' from {$this->MAuth->getdb('PASSWRD')} where USERID=a.CHECKER) as CHECKNAME
				,(select USERNAME+' ('+USERID+')' from {$this->MAuth->getdb('PASSWRD')} where USERID=a.SALCOD) as SALNAME
				,a.RECOMCOD,(select SNAM+NAME1+' '+NAME2+' ('+CUSCOD+')'+'-'+GRADE from {$this->MAuth->getdb('CUSTMAST')} where CUSCOD=a.RECOMCOD) as RECOMNAME
				,a.PAYDWN,a.SMPAY,a.COMEXT,a.COMOPT,a.COMOTH,a.CALINT,a.CALDSC,a.MEMO1
			from {$this->MAuth->getdb('ARMAST')} a
			left join {$this->MAuth->getdb('CUSTMAST')} b on a.CUSCOD=b.CUSCOD 
			left join {$this->MAuth->getdb('PAYDUE')} c on a.PAYTYP=c.PAYCODE
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
				$response["KEYINPRC"] 	= number_format($row->KEYINPRC,2);
				$response["KEYINDWN"] 	= number_format($row->KEYINDWN,2);
				$response["TAXNO"] 		= $row->TAXNO;
				$response["TAXDT"] 		= $this->Convertdate(2,$row->TAXDT);
				$response["T_NOPAY"] 	= number_format($row->T_NOPAY,0);
				$response["T_UPAY"] 	= number_format($row->T_UPAY,0);
				$response["KEYINFUPAY"] = number_format($row->KEYINFUPAY,2);
				$response["KEYINUPAY"] 	= number_format($row->KEYINUPAY,2);
				$response["T_LUPAY"] 	= number_format($row->T_LUPAY,2);
				$response["STDPRC"] 	= number_format($row->STDPRC,2);
				$response["KEYINCSHPRC"] = number_format($row->KEYINCSHPRC,2);
				$response["NPROFIT"] 	= number_format($row->NPROFIT,2);
				
				$response["FDATE"] 		= $this->Convertdate(2,$row->FDATE);
				$response["LDATE"] 		= $this->Convertdate(2,$row->LDATE);
				$response["ISSUNO"] 	= $row->ISSUNO;
				$response["ISSUDT"] 	= $this->Convertdate(2,$row->ISSUDT);
				$response["BILLCOLL"] 	= $row->BILLCOLL;
				$response["BILLNAME"] 	= $row->BILLNAME;
				$response["CHECKER"] 	= $row->CHECKER;
				$response["CHECKNAME"] 	= $row->CHECKNAME;
				$response["DELYRT"] 	= $row->DELYRT;
				$response["DLDAY"] 		= $row->DLDAY;
				$response["INTRT"] 		= $row->INTRT;
				$response["EFRATE"] 	= $row->EFRATE;
				$response["SALCOD"] 	= $row->SALCOD;
				$response["SALNAME"] 	= $row->SALNAME;
				$response["COMITN"] 	= number_format($row->COMITN,2);
				$response["ACTICOD"] 	= $row->ACTICOD;
				$response["ACTINAME"] 	= $row->ACTINAME;
				
				$response["RECOMCOD"] 	= $row->RECOMCOD;
				$response["RECOMNAME"] 	= $row->RECOMNAME;
				$response["PAYDWN"] 	= number_format($row->PAYDWN,2);
				$response["SMPAY"] 		= number_format($row->SMPAY,2);
				$response["COMEXT"] 	= number_format($row->COMEXT,2);
				$response["COMOPT"] 	= number_format($row->COMOPT,2);
				$response["COMOTH"] 	= number_format($row->COMOTH,2);
				$response["CALINT"] 	= $row->CALINT;
				$response["CALDSC"] 	= $row->CALDSC;
				
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
			where a.CONTNO='".$contno."' and b.LOCAT=(select LOCAT from {$this->MAuth->getdb('ARMAST')} where CONTNO='".$contno."')
		";
		$query = $this->db->query($sql);
		
		$option = "";
		if($query->row()){
			foreach($query->result() as $row){
				$option .= "
					<tr seq='old'>
						<td align='center'>
							<i class='inoptTab2 btn btn-xs btn-danger glyphicon glyphicon-minus' 
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
		
		/*คนค้ำประกัน*/
		$sql = "
			select a.GARNO,a.CUSCOD,b.SNAM+b.NAME1+' '+b.NAME2+' ('+b.CUSCOD+')'+'-'+b.GRADE as CUSNAME
				,a.ADDRNO,a.RELATN
			from {$this->MAuth->getdb('ARMGAR')} a 
			left join {$this->MAuth->getdb('CUSTMAST')} b on a.CUSCOD=b.CUSCOD
			where a.CONTNO='".$contno."' and a.LOCAT=(select LOCAT from {$this->MAuth->getdb('ARMAST')} where CONTNO='".$contno."')
			order by a.GARNO
		";
		$query = $this->db->query($sql);
		
		$mgar = "";
		if($query->row()){
			foreach($query->result() as $row){
				$mgar .= "
					<tr seq='old'>
						<td align='center'>
							<i class='mgarTab5 btn btn-xs btn-danger glyphicon glyphicon-minus'
								position='".$row->GARNO."' cuscod='".$row->CUSCOD."' cusval='".$row->CUSNAME."' relation='".$row->RELATN."'
								style='cursor:pointer;'> ลบ   
							</i>
						</td>
						<td>".$row->GARNO."</td>
						<td>".$row->CUSNAME."</td>
						<td>".$row->RELATN."</td>
					</tr>
				";
			}
		}else{
			$mgar = "";
		}
		$response["mgar"] = $mgar;
		
		/*หลักทรัพย์ค้ำประกัน*/
		$sql = "
			select a.GARNO,a.GARCODE,'('+a.GARCODE+') '+GARDESC as GARDESC
				,a.REFFNO
			from {$this->MAuth->getdb('AROTHGAR')} a 
			left join {$this->MAuth->getdb('SETARGAR')} b on a.GARCODE=b.GARCODE
			where a.CONTNO='".$contno."' and a.LOCAT=(select LOCAT from {$this->MAuth->getdb('ARMAST')} where CONTNO='".$contno."')
			order by a.GARNO
		";
		$query = $this->db->query($sql);
		
		$othmgar = "";
		if($query->row()){
			foreach($query->result() as $row){
				$othmgar .= "
					<tr seq='old'>
						<td align='center'>
							<i class='othmgarTab5 btn btn-xs btn-danger glyphicon glyphicon-minus'
								position='".$row->GARNO."' garcod='".$row->GARCODE."' garval='".$row->GARDESC."' refno='".$row->REFFNO."'
								style='cursor:pointer;'> ลบ   
							</i>
						</td>
						<td>".$row->GARNO."</td>
						<td>".$row->GARDESC."</td>
						<td>".$row->REFFNO."</td>
					</tr>
				";
			}
		}else{
			$othmgar = "";
		}
		$response["othmgar"] = $othmgar;
		
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
		
	function getfromLeasing(){
		$data = array();
		
		$sql = "
			select * from {$this->MAuth->getdb('VATMAST')}
			where getdate() between FRMDATE and TODATE
		";
		$query = $this->db->query($sql);
		$row = $query->row();
		$data["vatrt"] = number_format($row->VATRT,2);
		
		$sql = "select CALINT,DISC_FM from {$this->MAuth->getdb('CONDPAY')}";
		$query = $this->db->query($sql);
		$row = $query->row();
		$data["CALINT"] = str_replace(chr(0),'',$row->CALINT);
		$data["DISC_FM"] = str_replace(chr(0),'',$row->DISC_FM);
		
		$sql = "select top 1 PAYCODE,'('+PAYCODE+') '+PAYDESC PAYDESC from {$this->MAuth->getdb('PAYDUE')} order by PAYCODE";
		$query = $this->db->query($sql);
		$row = $query->row();
		$data["PAYCODE"] = str_replace(chr(0),'',$row->PAYCODE);
		$data["PAYDESC"] = str_replace(chr(0),'',$row->PAYDESC);
		
		$sql = "select top 1 ACTICOD,'('+ACTICOD+') '+ACTIDES as ACTIDES from {$this->MAuth->getdb('SETACTI')} order by ACTICOD";
		$query = $this->db->query($sql);
		$row = $query->row();
		$data["ACTICOD"] = str_replace(chr(0),'',$row->ACTICOD);
		$data["ACTIDES"] = str_replace(chr(0),'',$row->ACTIDES);
			
		$html = "
			<div id='wizard-leasing' class='wizard-wrapper'>    
				<div class='wizard'>
					<form id='demo-form2' action='' class='lobi-form' novalidate='novalidate'>
						<ul class='wizard-tabs wizard-tab-balls nav-justified nav nav-pills'>
							<li class='active'>
								<a href='#tab11' prev='#' data-toggle='tab' aria-expanded='true'>
									<span class='step'>1</span>
									<span class='title'>ผู้เช่าซื้อ/รถ</span>
								</a>
							</li>
							<li>
								<a href='#tab22' prev='#tab11' data-toggle='tab'>
									<span class='step'>2</span>
									<span class='title'>อุปกรณ์เสริม</span>
								</a>
							</li>
							<li>
								<a href='#tab33' prev='#tab22' data-toggle='tab'>
									<span class='step'>3</span>
									<span class='title'>เงื่อนไขการเงิน</span>
								</a>
							</li>
							
							<li>
								<a href='#tab44' prev='#tab33' data-toggle='tab'>
									<span class='step'>4</span>
									<span class='title'>รายละเอียดสัญญา</span>
								</a>
							</li>							
							<li>
								<a href='#tab55' prev='#tab44' data-toggle='tab'>
									<span class='step'>5</span>
									<span class='title'>การค้ำประกัน</span>
								</a>
							</li>
						</ul>
						<div class='tab-content bg-white'>
							".$this->getfromLeasingTab11($data)."
							".$this->getfromLeasingTab22($data)."
							".$this->getfromLeasingTab33($data)."
							".$this->getfromLeasingTab44($data)."
							".$this->getfromLeasingTab55($data)."
							
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
					<input type='button' id='btnArpay' class='btn btn-xs btn-info' style='width:100px;' value='ตาราง' disabled>
					<input type='button' id='btnSend' class='btn btn-xs btn-info' style='width:100px;' value='ใบส่งมอบ' disabled>
					<input type='button' id='btnTax' class='btn btn-xs btn-info' style='width:100px;' value='ใบกำกับ' disabled>
					<br>
					<input type='button' id='btnApproveSell' class='btn btn-xs btn-info' style='width:100px;' value='ใบอนุมัติขาย' disabled>
					<input type='button' id='btnContno' class='btn btn-xs btn-info' style='width:100px;' value='สัญญา' disabled>
					<input type='button' id='btnLock' class='btn btn-xs btn-info' style='width:100px;' value='Lock สัญญา' disabled>
				</div>
				<div class='col-sm-6 text-right'>
					<button id='add_save' class='btn btn-xs btn-primary' style='width:100px;'><span class='glyphicon glyphicon-floppy-disk'> บันทึก</span></button>
					<br>
					<button id='add_delete' class='btn btn-xs btn-danger' style='width:100px;'><span class='glyphicon glyphicon-trash'> ลบ</span></button>
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
										<!-- input type='text' id='add_approve' class='form-control input-sm' placeholder='เลขที่ใบอนุมัติ'  -->
										<select id='add_approve' class='form-control input-sm' data-placeholder='เลขที่ใบอนุมัติ'></select>
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
										<select id='add_paydue' class='form-control input-sm' data-placeholder='วิธีชำระค่างวด'>
											<option value='".$data["PAYCODE"]."' selected>".$data["PAYDESC"]."</option>
										</select>
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
									ราคาขายผ่อน
									<div class='form-group'>
										<label class='input'>
											<span id='add_inprcCal' class='input-icon input-icon-append glyphicon glyphicon-info-sign'></span>
											<input type='text' id='add_inprc' class='form-control input-sm' placeholder='ราคาขายผ่อน' >
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
								จำนวนงวดที่ผ่อน
								<div class='input-group'>
									<input type='text' id='add_nopay' class='form-control input-sm' placeholder='จำนวนงวดที่ผ่อน' >
									<span class='input-group-addon'>งวด</span>
								</div>
							</div>
							<div class='col-sm-12 col-lg-6'>	
								ผ่อนชำระ
								<div class='input-group'>
									<input type='text' id='add_upay' class='form-control input-sm' placeholder='ผ่อนชำระ' >
									<span class='input-group-addon'>ด./งวด</span>
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
					<div style='float:left;background-color:#269da1;color:#efff14;' class='col-sm-8 col-sm-offset-2'>
						<div class='col-sm-4'>	
							<div class='form-group'>
								ค่างวดแรก
								<input type='text' id='add_payfirst' class='form-control input-sm' placeholder='ค่างวดแรก' >
							</div>
						</div>
						<div class='col-sm-4'>	
							<div class='form-group'>
								ค่างวดถัดไป
								<input type='text' id='add_paynext' class='form-control input-sm' placeholder='ค่างวดถัดไป' >
							</div>
						</div>
						<div class='col-sm-4'>	
							<div class='form-group'>
								ค่างวดสุดท้าย + ภาษี
								<input type='text' id='add_paylast' class='form-control input-sm' placeholder='ค่างวดสุดท้าย + ภาษี' >
							</div>
						</div>
						
						<div class='col-sm-4'>	
							<div class='form-group'>
								ราคาขายหน้าร้าน
								<input type='text' id='add_sell' class='form-control input-sm' placeholder='ราคาขายหน้าร้าน' >
							</div>
						</div>
						<div class='col-sm-4'>	
							<div class='form-group'>
								ราคาขายสดสุทธิ
								<input type='text' id='add_totalSell' class='form-control input-sm' placeholder='ราคาขายสดสุทธิ' >
							</div>
						</div>
						<div class='col-sm-4'>	
							<div class='form-group'>
								ดอกผลเช่าซื้อ
								<input type='text' id='add_interest' class='form-control input-sm' placeholder='ดอกผลเช่าซื้อ' >
							</div>
						</div>
						
						<div class='col-sm-4 col-sm-offset-8'>	
							<button id='add_detailsCond' class='btn-sm btn-inverse btn-block'>รายละเอียด</button>
						</div>
					</div>
					
					<div style='float:left;' class='col-sm-8 col-sm-offset-2'>
						<div class='row'>
							<div class=' col-sm-3'>	
								<div class='form-group'>
									วันดิวงวดแรก
									<input type='text' id='add_duefirst' class='form-control input-sm' placeholder='วันดิวงวดแรก' data-provide='datepicker' data-date-language='th-th' value='".$this->today('todaynextmonth')."'>
								</div>
							</div>
							<div class=' col-sm-3'>	
								<div class='form-group'>
									วันดิวงวดสุดท้าย
									<input type='text' id='add_duelast' class='form-control input-sm' placeholder='วันดิวงวดสุดท้าย' data-provide='datepicker' data-date-language='th-th' disabled>
								</div>
							</div>
							<div class=' col-sm-3'>	
								<div class='form-group'>
									เลขที่ปล่อยรถ
									<input type='text' id='add_release' class='form-control input-sm' placeholder='เลขที่ปล่อยรถ' >
								</div>
							</div>
							<div class=' col-sm-3'>	
								<div class='form-group'>
									วันที่ปล่อยรถ
									<input type='text' id='add_released' class='form-control input-sm' placeholder='วันที่ปล่อยรถ' data-provide='datepicker' data-date-language='th-th' value='".$this->today('today')."'>
								</div>
							</div>
							
							<div class=' col-sm-6'>	
								<div class='form-group'>
									รหัสพนักงานเก็บเงิน
									<select id='add_emp' class='form-control input-sm' data-placeholder='รหัสพนักงานเก็บเงิน'>
										<option value='".$this->sess["USERID"]."'>".$this->sess["name"]." (".$this->sess["USERID"].")</option>
									</select>
								</div>
							</div>
							<div class=' col-sm-6'>	
								<div class='form-group'>
									รหัสผู้ตรวจสอบ
									<select id='add_audit' class='form-control input-sm' data-placeholder='รหัสผู้ตรวจสอบ'>
										<option value='".$this->sess["USERID"]."'>".$this->sess["name"]." (".$this->sess["USERID"].")</option>
									</select>
								</div>
							</div>
							
							<div class=' col-sm-3'>	
								<div class='form-group'>
									อัตราเบี้ยปรับล่าช้า
									<input type='text' id='add_intRate' class='form-control input-sm' placeholder='อัตราเบี้ยปรับล่าช้า' >
								</div>
							</div>
							<div class=' col-sm-3'>	
								<div class='form-group'>
									ชำระล่าช้าได้ไม่เกิน
									<input type='text' id='add_delay' class='form-control input-sm' placeholder='ชำระล่าช้าได้ไม่เกิน' >
								</div>
							</div>
							<div class=' col-sm-3'>	
								<div class='form-group'>
									อัตราดอกเบี้ยทำเช่าซื้อ
									<input type='text' id='add_interestRate' class='form-control input-sm' placeholder='อัตราดอกเบี้ยทำเช่าซื้อ' >
								</div>
							</div>
							<div class=' col-sm-3'>	
								<div class='form-group'>
									อัตราดอกเบี้ยเช่าซื้อจริง
									<input type='text' id='add_interestRateReal' class='form-control input-sm' placeholder='อัตราดอกเบี้ยเช่าซื้อจริง' >
								</div>
							</div>
							
							<div class=' col-sm-6'>	
								<div class='form-group'>
									รหัสพนักงานขาย
									<select id='add_empSell' class='form-control input-sm' data-placeholder='รหัสพนักงานขาย'>
										<option value='".$this->sess["USERID"]."'>".$this->sess["name"]." (".$this->sess["USERID"].")</option>
									</select>
								</div>
							</div>
							<div class=' col-sm-3'>	
								<div class='form-group'>
									ค่านายหน้าขาย
									<input type='text' id='add_agent' class='form-control input-sm' placeholder='ค่านายหน้าขาย' >
								</div>
							</div>
							<div class=' col-sm-3'>	
								<div class='form-group'>
									กิจกรรมการขาย
									<select id='add_acticod' class='form-control input-sm' data-placeholder='กิจกรรมการขาย'>
										<option value='".$data["ACTICOD"]."' selected>".$data["ACTIDES"]."</option>
									</select>
								</div>
							</div>
							<div class='add_nextlastmonth' class=' col-sm-6'>	
								<div class='form-group'>
									<input id='add_nextlastmonth' class='form-check-input' style='cursor:pointer;max-width:20px;max-height:10px;' type='checkbox' value='ต้องการให้งวดถัดไปเป็นสิ้นเดือน'  >
									<label class='form-check-label' style='cursor:pointer;' for='add_nextlastmonth'>ต้องการให้งวดถัดไปเป็นสิ้นเดือน</label>
								</div>
							</div>
						</div>
					</div>									
				</fieldset>
			</div>
		";
		return $html;
	}
	
	private function getfromLeasingTab44($data){
		$html = "
			<div class='tab-pane' name='tab44' style='height:calc(100vh - 260px);overflow:auto;'>
				<fieldset style='height:100%'>
					<div style='float:left;' class='col-sm-8 '>
						<div class='row'>
							<div class=' col-sm-6'>	
								<div class='form-group'>
									ผู้แนะนำการซื้อ
									<select id='add_advisor' class='form-control input-sm' data-placeholder='ผู้แนะนำการซื้อ'></select>
								</div>
							</div>
							
							<div class=' col-sm-3'>	
								<div class='form-group'>
									ชำระเงินดาวน์แล้ว
									<input type='text' id='add_paydown' class='form-control input-sm' placeholder='ชำระเงินดาวน์แล้ว' disabled>
								</div>
							</div>
							
							<div class=' col-sm-3'>	
								<div class='form-group'>
									รับชำระเงินแล้วทั้งหมด
									<input type='text' id='add_payall' class='form-control input-sm' placeholder='รับชำระเงินแล้วทั้งหมด' disabled>
								</div>
							</div>
							
							<div class='col-sm-4'>	
								<div class='form-group'>
									ค่าคอมบุคคลนอก
									<input type='text' id='add_commission' class='form-control input-sm' placeholder='ค่าคอมบุคคลนอก' >
								</div>
							</div>
							
							<div class='col-sm-4'>	
								<div class='form-group'>
									ค่าของแถม
									<input type='text' id='add_free' class='form-control input-sm' placeholder='ค่าของแถม' >
								</div>
							</div>
							
							<div class='col-sm-4'>	
								<div class='form-group'>
									ค่าใช้จ่ายอื่นๆ
									<input type='text' id='add_payother' class='form-control input-sm' placeholder='ค่าใช้จ่ายอื่นๆ' >
								</div>
							</div>
							
							<div class=' col-sm-6'>	
								วิธีคำนวนเบี้ยปรับ
								<div class='col-sm-12'>
									<label class='radio lobiradio lobiradio-info'>
										<input type='radio' name='CALINT' value='1' ".($data["CALINT"] == 1 ? "checked":"")."> 
										<i></i> ตามอัตรา MRR+ค่าคงที่
									</label>
								</div>
								<div class='col-sm-12'>
									<label class='radio lobiradio lobiradio-info'>
										<input type='radio' name='CALINT' value='2' ".($data["CALINT"] == 2 ? "checked":"").">
										<i></i> ตามอัตราเบี้ยปรับต่อเดือน
									</label>
								</div>
							</div>
							<div class=' col-sm-6'>	
								วิธีคำนวนส่วนลดตัดสด
								<div class='col-sm-12'>
									<label class='radio lobiradio lobiradio-info'>
										<input type='radio' name='DISC_FM' value='1' ".($data["DISC_FM"] == 1 ? "checked":"")."> 
										<i></i> % ส่วนลดของดอกเบี้ยคงเหลือ(สคบ.)
									</label>									
								</div>
								<div class='col-sm-12'>
									<label class='radio lobiradio lobiradio-info'>
										<input type='radio' name='DISC_FM' value='2' ".($data["DISC_FM"] == 2 ? "checked":"")."> 
										<i></i> % ส่วนลดของดอกเบี้ยทั้งหมด
									</label>
								</div>
								<div class='col-sm-12'>
									<label class='radio lobiradio lobiradio-info'>
										<input type='radio' name='DISC_FM' value='3' ".($data["DISC_FM"] == 3 ? "checked":"").">  
										<i></i> % ส่วนลดต่อเดือน(HP DOS)
									</label>
								</div>
							</div>							
							
							<div class='2 col-sm-12'>	
								<div class='form-group'>
									หมายเหตุ
									<textarea type='text' id='add_comments' class='form-control input-sm' placeholder='หมายเหตุ'  rows=4 style='resize:vertical;'></textarea>
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
	
	private function getfromLeasingTab55($data){
		$html = "
			<div class='tab-pane' name='tab55' style='height:calc(100vh - 260px);overflow:auto;'>
				<fieldset style='height:100%;'>
					<div style='float:left;height:100%;' class='col-sm-6'>						
						<div class='row' style='width:100%;height:100%;padding-left:30px;background-color:#269da1;'>
							<div class='form-group col-sm-12' style='height:100%;'>
								<span style='color:#efff14;'>ผู้ค้ำประกัน</span>
								<div id='dataTable_fixed_ARMGAR' class='dataTables_wrapper dt-bootstrap4 table-responsive' style='max-height:calc(100% - 50px);height:calc(100% - 30px);overflow:auto;border:1px dotted black;background-color:white;'>
									<table id='dataTable_ARMGAR' class='table table-bordered dataTable table-hover table-secondary' id='dataTables_ARMGAR' stat='' role='grid' aria-describedby='dataTable_info' style='width:100%;line-height:10px;white-space:nowrap;text-overflow:ellipsis;overflow:hidden;' cellspacing='0'>
										<thead class='thead-dark' style='width:100%;'>
											<tr role='row'>
												<th style='width:40px'>
													<i id='add_mgar' class='btn btn-xs btn-success glyphicon glyphicon-plus' style='cursor:pointer;'> เพิ่ม  </i>
												</th>
												<th>ลำดับที่</th>
												<th>รหัสผู้ค้ำ</th>
												<th>ความสัมพันธ์</th>
											</tr>
										</thead>
										<tbody style='white-space: nowrap;'></tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
					<div style='float:left;height:100%;' class='col-sm-6'>						
						<div class='row' style='width:100%;height:100%;padding-left:30px;background-color:#269da1;'>
							<div class='form-group col-sm-12' style='height:100%;'>
								<!-- span style='color:#34dfb5;'>รายการอุปกรณ์เสริม</span> &emsp;&emsp; <span style='color:#efff14;'>บันทึกอุปกรณ์เสริมเพื่อขายรวมกับตัวรถ</span -->
								<span style='color:#efff14;'>หลักทรัพย์ค้ำประกัน</span>
								<div id='dataTable_fixed_AROTHGAR' class='dataTables_wrapper dt-bootstrap4 table-responsive' style='max-height:calc(100% - 50px);height:calc(100% - 30px);overflow:auto;border:1px dotted black;background-color:white;'>
									<table id='dataTable_AROTHGAR' class='table table-bordered dataTable table-hover table-secondary' id='dataTables_AROTHGAR' stat='' role='grid' aria-describedby='dataTable_info' style='width:100%;line-height:10px;white-space:nowrap;text-overflow:ellipsis;overflow:hidden;' cellspacing='0'>
										<thead class='thead-dark' style='width:100%;'>
											<tr role='row'>
												<th style='width:40px'>
													<i id='add_othmgar' class='btn btn-xs btn-success glyphicon glyphicon-plus' style='cursor:pointer;'> เพิ่ม  </i>
												</th>
												<th>ลำดับที่</th>
												<th>รหัสหลักทรัพย์</th>
												<th>เลขที่อ้างอิง</th>
											</tr>
										</thead>
										<tbody style='white-space: nowrap;'></tbody>
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
	
	function getFormStd(){
		$arrs = array();
		$arrs["locat"] = $_REQUEST["locat"];
		$arrs["strno"] = $_REQUEST["strno"];
		
		$sql = "
			select * from {$this->MAuth->getdb('INVTRAN')} 
			where CRLOCAT='".$arrs["locat"]."' and STRNO='".$arrs["strno"]."'
		";
		$query = $this->db->query($sql);
		
		$data = array();
		if($query->row()){
			foreach($query->result() as $row){
				$data["STRNO"]	= $row->STRNO;
				$data["MODEL"]	= $row->MODEL;
				$data["BAAB"] 	= $row->BAAB;
				$data["COLOR"] 	= $row->COLOR;
			}
		}
		
		$sql = "select top 1 ACTICOD,'('+ACTICOD+') '+ACTIDES as ACTIDES from {$this->MAuth->getdb('SETACTI')} order by ACTICOD";
		$query = $this->db->query($sql);
		$row = $query->row();
		$data["ACTICOD"] = str_replace(chr(0),"",$row->ACTICOD);
		$data["ACTIDES"] = str_replace(chr(0),"",$row->ACTIDES);
		
		$html = "
			<div id='lobiwin_std' class='row'>
				<div class='col-sm-4 col-sm-offset-2' style='border:1px dotted black;padding-bottom:30px;'> 
					<p>ข้อมูลรถ</p>
					<p class='text-primary'>เลขตัวถัง</p> <p class='text-red' style='font-size:8pt;'>".$data["STRNO"]."</p>
					<p class='text-primary'>รุ่น</p> <p class='text-red' style='font-size:8pt;'>".$data["MODEL"]."</p>
					<p class='text-primary'>แบบ</p> <p class='text-red' style='font-size:8pt;'>".$data["BAAB"]."</p>
					<p class='text-primary'>สี</p> <p class='text-red' style='font-size:8pt;'>".$data["COLOR"]."</p>
				</div>
				<div class='col-sm-4' style='border:1px dotted black;padding-bottom:30px;'> 
					<p>เงื่อนไข</p>			
					<div class='col-sm-12'>
						<span class='text-info' style='font-size:8pt;'>กิจกรรมการขาย</span>
						<select id='std_acticod' class='form-control input-sm' data-placeholder='กิจกรรมการขาย'>
							<option value='".$data["ACTICOD"]."' selected>".$data["ACTIDES"]."</option>
						</select>
					</div>
					<div class='col-sm-12'>
						<span class='text-info' style='font-size:8pt;'>จำนวนเงินดาวน์</span>
						<div class='input-group'>
							<input type='text' id='std_dwn' class='form-control input-sm' >
							<span class='input-group-addon'>บาท</span>
						</div>
					</div>
					<div class='col-sm-12'>
						<span class='text-info' style='font-size:8pt;'>จำนวนผ่อน</span>
						<div class='input-group'>
							<input type='text' id='std_nopay' class='form-control input-sm' >
							<span class='input-group-addon'>งวด</span>
						</div>
					</div>
					
					<p class='col-sm-12 text-right'>&emsp;</p>
					<p class='col-sm-12 text-right'>
						<icon id='btnStdSearch' 
							STRNO='".$data["STRNO"]."' 
							MODEL='".$data["MODEL"]."'
							BAAB='".$data["BAAB"]."'
							COLOR='".$data["COLOR"]."'
							class='btn btn-xs btn-primary btn-block'>ค้นหา</icon>
					</p>
				</div>
			</div>
			<div class='row'>
				<div id='stdResult' class='col-sm-8 col-sm-offset-2' style='border:1px dotted black;padding-bottom:30px;'></div>
			</div>
		";
		
		$response = array("html"=>$html);		
		echo json_encode($response);
	}
	
	function getFormStdSearch(){
		$arrs = array();
		$arrs["locat"] 	 = $_REQUEST["locat"];
		$arrs["sdate"] 	 = $this->Convertdate(1,$_REQUEST["sdate"]);
		$arrs["model"] 	 = $_REQUEST["model"];
		$arrs["baab"]  	 = $_REQUEST["baab"];
		$arrs["color"]	 = $_REQUEST["color"];
		$arrs["acticod"] = $_REQUEST["acticod"];
		$arrs["dwn"] 	 = $_REQUEST["dwn"];
		$arrs["nopay"] 	 = $_REQUEST["nopay"];
		//print_r($arrs); exit;
		
		if($arrs["dwn"] > 0){}else{
			$response["status"] = "W";
			$response["msg"] = "คุณยังไม่ได้ระบุจำนวนเงินดาวน์";
			echo json_encode($response); exit;
		}
		
		$sql = "
			declare @locat varchar(20)		= '".$arrs["locat"]."';
			declare @sdate varchar(20)		= '".$arrs["sdate"]."';
			declare @model varchar(30)		= '".$arrs["model"]."';
			declare @baab varchar(30)		= '".$arrs["baab"]."';
			declare @color varchar(30)		= '".$arrs["color"]."';
			declare @acticod varchar(30)	= '".$arrs["acticod"]."';
			declare @down decimal(18,2)		= '".$arrs["dwn"]."';
			declare @nopay int				= '".$arrs["nopay"]."';

			if ((
				select COUNT(*) from {$this->MAuth->getdb('std_vehicles')} a 
				left join {$this->MAuth->getdb('std_pricelist')} b on a.id=b.id
				left join {$this->MAuth->getdb('std_pricelist_locat')} c on b.id=c.id and b.plrank=c.plrank
				where a.model=@model and a.baab=@baab and a.color=@color and b.ACTICOD=@acticod
					and @sdate between b.event_s and isnull(b.event_e,@sdate) and c.locat=@locat
			) > 0)
			begin 
				select a.id,a.model,a.baab,a.color,b.plrank
					,b.price,b.pricespecial,b.ACTICOD
					,convert(varchar(8),b.event_s,112) as event_s
					,convert(varchar(8),b.event_e,112) as event_e
					,c.locat,d.*
					,(case when @down between dwnrate_s and dwnrate_e then 1 else 0 end) as checked
				from {$this->MAuth->getdb('std_vehicles')} a 
				left join {$this->MAuth->getdb('std_pricelist')} b on a.id=b.id
				left join {$this->MAuth->getdb('std_pricelist_locat')} c on b.id=c.id and b.plrank=c.plrank
				left join {$this->MAuth->getdb('std_down')} d on b.id=d.id and b.plrank=d.plrank
				where a.model=@model and a.baab=@baab and a.color=@color and b.ACTICOD=@acticod
					and @sdate between b.event_s and isnull(b.event_e,@sdate) and c.locat=@locat
			end 
			else if ((
				select COUNT(*) from {$this->MAuth->getdb('std_vehicles')} a 
				left join {$this->MAuth->getdb('std_pricelist')} b on a.id=b.id
				left join {$this->MAuth->getdb('std_pricelist_locat')} c on b.id=c.id and b.plrank=c.plrank
				where a.model=@model and a.baab=@baab and a.color=@color and b.ACTICOD=@acticod
					and @sdate between b.event_s and isnull(b.event_e,@sdate) and c.locat='all'
			) > 0)
			begin 
				select a.id,a.model,a.baab,a.color,b.plrank
					,b.price,b.pricespecial,b.ACTICOD
					,convert(varchar(8),b.event_s,112) as event_s
					,convert(varchar(8),b.event_e,112) as event_e
					,c.locat,d.*
					,(case when @down between dwnrate_s and dwnrate_e then 1 else 0 end) as checked
				from {$this->MAuth->getdb('std_vehicles')} a 
				left join {$this->MAuth->getdb('std_pricelist')} b on a.id=b.id
				left join {$this->MAuth->getdb('std_pricelist_locat')} c on b.id=c.id and b.plrank=c.plrank
				left join {$this->MAuth->getdb('std_down')} d on b.id=d.id and b.plrank=d.plrank
				where a.model=@model and a.baab=@baab and a.color=@color and b.ACTICOD=@acticod
					and @sdate between b.event_s and isnull(b.event_e,@sdate) and c.locat='all'
			end
			else if ((
				select COUNT(*) from {$this->MAuth->getdb('std_vehicles')} a 
				left join {$this->MAuth->getdb('std_pricelist')} b on a.id=b.id
				left join {$this->MAuth->getdb('std_pricelist_locat')} c on b.id=c.id and b.plrank=c.plrank
				where a.model=@model and a.baab=@baab and a.color=@color and b.ACTICOD='all'
					and @sdate between b.event_s and isnull(b.event_e,@sdate) and c.locat='all'
			) > 0)
			begin 
				select a.id,a.model,a.baab,a.color,b.plrank
					,b.price,b.pricespecial,b.ACTICOD
					,convert(varchar(8),b.event_s,112) as event_s
					,convert(varchar(8),b.event_e,112) as event_e
					,c.locat,d.*
					,(case when @down between dwnrate_s and dwnrate_e then 1 else 0 end) as checked
				from {$this->MAuth->getdb('std_vehicles')} a 
				left join {$this->MAuth->getdb('std_pricelist')} b on a.id=b.id
				left join {$this->MAuth->getdb('std_pricelist_locat')} c on b.id=c.id and b.plrank=c.plrank
				left join {$this->MAuth->getdb('std_down')} d on b.id=d.id and b.plrank=d.plrank
				where a.model=@model and a.baab=@baab and a.color=@color and b.ACTICOD='all'
					and @sdate between b.event_s and isnull(b.event_e,@sdate) and c.locat='all'
			end
			else if ((
				select COUNT(*) from {$this->MAuth->getdb('std_vehicles')} a 
				left join {$this->MAuth->getdb('std_pricelist')} b on a.id=b.id
				left join {$this->MAuth->getdb('std_pricelist_locat')} c on b.id=c.id and b.plrank=c.plrank
				where a.model=@model and a.baab=@baab and a.color='all' and b.ACTICOD='all'
					and @sdate between b.event_s and isnull(b.event_e,@sdate) and c.locat='all'
			) > 0)
			begin 
				select a.id,a.model,a.baab,a.color,b.plrank
					,b.price,b.pricespecial,b.ACTICOD
					,convert(varchar(8),b.event_s,112) as event_s
					,convert(varchar(8),b.event_e,112) as event_e
					,c.locat,d.*
					,(case when @down between dwnrate_s and isnull(dwnrate_e,@down) then 1 else 0 end) as checked
				from {$this->MAuth->getdb('std_vehicles')} a 
				left join {$this->MAuth->getdb('std_pricelist')} b on a.id=b.id
				left join {$this->MAuth->getdb('std_pricelist_locat')} c on b.id=c.id and b.plrank=c.plrank
				left join {$this->MAuth->getdb('std_down')} d on b.id=d.id and b.plrank=d.plrank
				where a.model=@model and a.baab=@baab and a.color='all' and b.ACTICOD='all'
					and @sdate between b.event_s and isnull(b.event_e,@sdate) and c.locat='all'		
			end
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$response = array();
		$attrReceipt = "";
		$correct = false;
		if($query->row()){
			foreach($query->result() as $row){
				$response["htmlHead"] = "
					<div class='col-lg-12 text-blue'>รหัส std. :: ".$row->id."</div>
					<div class='col-lg-4'>รุ่น :: ".$row->model."</div>
					<div class='col-lg-4'>แบบ :: ".$row->baab."</div>
					<div class='col-lg-4'>สี :: ".$row->color."</div>
					<div class='col-lg-4'>ลำดับ :: ".$row->plrank."</div>
					<div class='col-lg-4'>ราคาสด/ผ่อน :: ".number_format($row->price,2)."</div>
					<div class='col-lg-4'>ราคาผลัด :: ".number_format($row->pricespecial,2)."</div>
					<div class='col-lg-8'>กิจกรรมขาย :: ".$row->ACTICOD."</div>
					<div class='col-lg-4'>สาขา :: ".$row->locat."</div>
					<div class='col-lg-6'>เริ่มใช้ std ตั้งแต่วันที่ :: ".$this->Convertdate(2,$row->event_s)."</div>
					<div class='col-lg-6'>วันที่สิ้นสุด std :: ".$this->Convertdate(2,$row->event_e)."</div>
				";
				if(!isset($response["htmlBody"])){$response["htmlBody"]='';}
				
				$text = "text-muted";
				if($row->checked == 1){ 
					$correct = true;
					$text = "bg-success text-blue"; 
					$attrReceipt .= " stdid='".$row->id."'";
					$attrReceipt .= " plrank='".$row->plrank."'";	
					$attrReceipt .= " price='".str_replace(",","",number_format($row->price,2))."'";	
					$attrReceipt .= " interest_rate='".str_replace(",","",number_format(($row->interest_rate * 12),2))."'";	
					$attrReceipt .= " interest_rate2='".str_replace(",","",number_format(($row->interest_rate2 * 12),2))."'";	
					$attrReceipt .= " insurance='".str_replace(",","",number_format($row->insurance,2))."'";	
					$attrReceipt .= " transfers='".str_replace(",","",number_format($row->transfers,2))."'";	
					$attrReceipt .= " regist='".str_replace(",","",number_format($row->regist,2))."'";
					$attrReceipt .= " act='".str_replace(",","",number_format($row->act,2))."'";
					$attrReceipt .= " coupon='".str_replace(",","",number_format($row->coupon,2))."'";
					
					$optionTotal = $row->insurance + $row->transfers + $row->regist + $row->act + $row->coupon;
					$attrReceipt .= " optionTotal='".str_replace(",","",number_format($optionTotal,2))."'";
					$attrReceipt .= " down='".str_replace(",","",number_format($arrs["dwn"],2))."'";
					$attrReceipt .= " nopay='".$arrs["nopay"]."'";
				}
				
				$response["htmlBody"] .= "
					<tr>
						<td class='{$text}'><icon class='glyphicon glyphicon-".($text == 'text-muted'?'remove':'ok')."'></icon> ".$row->level_r."</td>
						<td class='text-right {$text}'>".($row->dwnrate_s == "" ? "" : number_format($row->dwnrate_s,2))."</td>
						<td class='text-right {$text}'>".($row->dwnrate_e == "" ? "" : number_format($row->dwnrate_e,2))."</td>
						<td class='text-right {$text}'>".($row->interest_rate == "" ? "" : number_format($row->interest_rate,2))."</td>
						<td class='text-right {$text}'>".($row->interest_rate2 == "" ? "" : number_format($row->interest_rate2,2))."</td>
						<td class='text-right {$text}'>".($row->insurance == "" ? "" : number_format($row->insurance,2))."</td>
						<td class='text-right {$text}'>".($row->transfers == "" ? "" : number_format($row->transfers,2))."</td>
						<td class='text-right {$text}'>".($row->regist == "" ? "" : number_format($row->regist,2))."</td>
						<td class='text-right {$text}'>".($row->act == "" ? "" : number_format($row->act,2))."</td>
						<td class='text-right {$text}'>".($row->coupon == "" ? "" : number_format($row->coupon,2))."</td>
					</tr>
				";
			}
		}else{
			$response["status"] = "W";
			$response["msg"] = "ผิดพลาด รถรุ่นนี้ยังไม่ได้กำหนด std. ทีครับ";
			echo json_encode($response); exit;
		}
		
		$response["htmlBody"] = "
			<div id='dataTable-fixed-inopt' class='dataTables_wrapper dt-bootstrap4 table-responsive' style='max-height:calc(100% - 130px);height:calc(100% - 130px);overflow:auto;border:1px dotted black;background-color:#eee;'>
				<table id='dataTables-inopt' class='table table-bordered table-dark' stat='' aria-describedby='dataTable_info' style='width:100%;line-height:10px;white-space:nowrap;text-overflow:ellipsis;overflow:hidden;' cellspacing='0'>
					<thead>
						<tr>
							<th class='col-lg-1 text-center'>ลำดับ</th>
							<th class='col-lg-4 text-center' colspan='2'>ช่วงการดาวน์</th>
							<th class='col-lg-1 text-center'>ดอกเบี้ย/ด.<br>ทั่วไป</th>
							<th class='col-lg-1 text-center'>ดอกเบี้ย/ด.<br>ราชการ</th>
							<th class='col-lg-1 text-center'>ประกัน</th>
							<th class='col-lg-1 text-center'>โอน</th>
							<th class='col-lg-1 text-center'>ทะเบียน</th>
							<th class='col-lg-1 text-center'>พรบ.</th>
							<th class='col-lg-2 text-center'>คูปองชิงโชค</th>
						</tr>
					</thead>
					<tbody>
						".$response["htmlBody"]."
					</tbody>
				</table>
			</div>	
			<div class='col-sm-12'><br>
				<div class='row'>
					<div class='col-sm-3 col-sm-offset-3'>
						<input class='form-check-input' style='cursor:pointer;max-width:20px;max-height:10px;' type='checkbox' id='stdCond1' value='เป็นข้าราชการ'  ".$attrReceipt." ".($correct ? "enabled":"disabled").">
						<label class='form-check-label' style='cursor:pointer;' for='stdCond1'>เป็นข้าราชการ</label>
						<br>
						<input class='form-check-input' style='cursor:pointer;max-width:20px;max-height:10px;' type='checkbox' id='stdCond2' value='รวมประกัน'  checked ".$attrReceipt." ".($correct ? "enabled":"disabled").">
						<label class='form-check-label' style='cursor:pointer;' for='stdCond2'>รวมประกัน</label>
						<br>
						<input class='form-check-input' style='cursor:pointer;max-width:20px;max-height:10px;' type='checkbox' id='stdCond3' value='รวมโอน'  checked ".$attrReceipt." ".($correct ? "enabled":"disabled").">
						<label class='form-check-label' style='cursor:pointer;' for='stdCond3'>รวมโอน</label>
					</div>
					<div class='col-sm-3'>					
						<input class='form-check-input' style='cursor:pointer;max-width:20px;max-height:10px;' type='checkbox' id='stdCond4' value='รวมทะเบียน'  checked ".$attrReceipt." ".($correct ? "enabled":"disabled").">
						<label class='form-check-label' style='cursor:pointer;' for='stdCond4'>รวมทะเบียน</label>
						<br>
						<input class='form-check-input' style='cursor:pointer;max-width:20px;max-height:10px;' type='checkbox' id='stdCond5' value='รวมพ.ร.บ.'  checked ".$attrReceipt." ".($correct ? "enabled":"disabled").">
						<label class='form-check-label' style='cursor:pointer;' for='stdCond5'>รวมพ.ร.บ.</label>
						<br>
						<input class='form-check-input' style='cursor:pointer;max-width:20px;max-height:10px;' type='checkbox' id='stdCond6' value='รวมโอน'  checked ".$attrReceipt." ".($correct ? "enabled":"disabled").">
						<label class='form-check-label' style='cursor:pointer;' for='stdCond6'>คูปองชิงโชค</label>
					</div>
				</div>
			</div>
		";
		
		$response["htmlBody"] .= "
			<div class='col-sm-6 col-sm-offset-3'><br>
				<icon id='btnStdReceipt' ".$attrReceipt." ".($correct ? "enabled":"disabled")." class='btn btn-xs btn-primary btn-block'>รับค่า</icon>
			</div>
			<br><br><br><br><br><br><br><br><br>
		";
		
		$response["status"] = "S";
		$response["msg"] = $response["htmlHead"].$response["htmlBody"];
		echo json_encode($response); 
	}
	
	function getStdReceipt(){
		$arrs = array();
		$arrs["stdid"] 	 		= $_REQUEST["stdid"];
		$arrs["plrank"] 	 	= $_REQUEST["plrank"];
		$arrs["price"] 	 		= str_replace(",","",$_REQUEST["price"]);
		$arrs["interest_rate"]  = str_replace(",","",$_REQUEST["interest_rate"]);
		$arrs["insurance"]	 	= str_replace(",","",$_REQUEST["insurance"]);
		$arrs["transfers"] 		= str_replace(",","",$_REQUEST["transfers"]);
		$arrs["regist"] 	 	= str_replace(",","",$_REQUEST["regist"]);
		$arrs["act"] 	 		= str_replace(",","",$_REQUEST["act"]);
		$arrs["coupon"] 	 	= str_replace(",","",$_REQUEST["coupon"]);
		$arrs["down"] 	 		= str_replace(",","",$_REQUEST["down"]);
		$arrs["nopay"] 	 		= str_replace(",","",$_REQUEST["nopay"]);
		$arrs["inclvat"] 		= $_REQUEST["inclvat"];
		$arrs["vatrt"] 	 		= str_replace(",","",$_REQUEST["vatrt"]);
		//$arrs["duefirst"] 	 	= str_replace(",","",$this->Convertdate(1,$_REQUEST["duefirst"]);
		
		$response = array();
		$response["status"] = true;
		
		if($arrs["price"] < $arrs["down"]){
			$response["status"] = false;
			$response["msg"] = "ราคาขายต้องมากกว่าเงินดาวน์ครับ"; 
			echo json_encode($response); exit;
		}
		
		echo json_encode($response);
	}
	
	function getStdReceived(){
		$arrs = array();
		$arrs["stdCond1"] 	= $_REQUEST["stdCond1"];
		$arrs["stdCond2"] 	= $_REQUEST["stdCond2"];
		$arrs["stdCond3"] 	= $_REQUEST["stdCond3"];
		$arrs["stdCond4"] 	= $_REQUEST["stdCond4"];
		$arrs["stdCond5"] 	= $_REQUEST["stdCond5"];
		$arrs["stdCond6"] 	= $_REQUEST["stdCond6"];
		$arrs["stdid"] 		= $_REQUEST["stdid"];
		$arrs["plrank"]		= $_REQUEST["plrank"];
		$arrs["price"] 		= $_REQUEST["price"];
		$arrs["interest_rate"] 	= $_REQUEST["interest_rate"];
		$arrs["interest_rate2"]	= $_REQUEST["interest_rate2"];
		$arrs["insurance"] 	= $_REQUEST["insurance"];
		$arrs["transfers"] 	= $_REQUEST["transfers"];
		$arrs["regist"]		= $_REQUEST["regist"];
		$arrs["act"]		= $_REQUEST["act"];
		$arrs["coupon"] 	= $_REQUEST["coupon"];
		$arrs["optionTotal"]= $_REQUEST["optionTotal"];
		$arrs["down"] 		= $_REQUEST["down"];
		$arrs["nopay"]		= $_REQUEST["nopay"];
		
		$response = array();
		$response["price"] 		= number_format($arrs["price"],2);
		$response["down"] 		= number_format($arrs["down"],2);
		$response["nopay"] 		= $arrs["nopay"];
		$response["interestY"] 	= ($arrs["stdCond1"] ? $arrs["interest_rate"] : ($arrs["interest_rate2"] == 0 ? $arrs["interest_rate"] : $arrs["interest_rate2"]) );
		
		$response["priceOpt"] 	=  ($arrs["stdCond2"] == 'T' ? $arrs["insurance"] : 0);
		$response["priceOpt"] 	+= ($arrs["stdCond3"] == 'T' ? $arrs["transfers"] : 0);
		$response["priceOpt"] 	+= ($arrs["stdCond4"] == 'T' ? $arrs["regist"] : 0);
		$response["priceOpt"] 	+= ($arrs["stdCond5"] == 'T' ? $arrs["act"] : 0);
		$response["priceOpt"] 	+= ($arrs["stdCond6"] == 'T' ? $arrs["coupon"] : 0);
		$response["priceOpt"] 	= number_format($response["priceOpt"],2);
		
		echo json_encode($response);
	}
	
	function resvnoChanged(){
		$resvno = $_REQUEST['resvno'];
		$locat 	= $_REQUEST['locat'];
		
		$sql = "
			select a.RESVNO,a.LOCAT
				,a.CUSCOD
				,b.SNAM+b.NAME1+' '+b.NAME2+' ('+b.CUSCOD+')'+'-'+b.GRADE CUSNAME
				,b.GRADE,a.STRNO
				,a.SMCHQ
				,a.RESPAY
				,a.SMPAY
				,c.CRLOCAT
				,1 as ADDRNO
				,(
					select '('+aa.ADDRNO+') '+aa.ADDR1+' '+aa.ADDR2+' ต.'+aa.TUMB
						+' อ.'+bb.AUMPDES+' จ.'+cc.PROVDES+' '+aa.ZIP as ADDRNODetails 			
					from {$this->MAuth->getdb('CUSTADDR')} aa
					left join {$this->MAuth->getdb('SETAUMP')} bb on aa.AUMPCOD=bb.AUMPCOD
					left join {$this->MAuth->getdb('SETPROV')} cc on bb.PROVCOD=cc.PROVCOD
					where aa.CUSCOD=a.CUSCOD and aa.ADDRNO=1
				) as ADDRDES
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
				$response["ADDRNO"]  = $row->ADDRNO;
				$response["ADDRDES"] = $row->ADDRDES;
				$response["GRADE"]   = $row->GRADE;
				$response["STRNO"]   = $row->STRNO;
				$response["SMCHQ"]   = str_replace(",","",number_format($row->SMCHQ,2));
				$response["msg"]   	 = ($row->CRLOCAT == $locat ? ($row->SMCHQ > 0 ? "เช็คเงินจองยังไม่ผ่าน": ($row->RESPAY == $row->SMPAY ? "" : "เลขที่บิลจอง ".$row->RESVNO." ยังชำระเงินจองไม่ครบครับ")) : "ผิดพลาด รถที่จองไม่ได้อยู่ในสาขาที่ทำรายการคีย์ขายครับ");
			}
		}else{
			$response["RESVNO"]  = "";
			$response["LOCAT"]   = "";
			$response["CUSCOD"]  = "";
			$response["CUSNAME"] = "";
			$response["ADDRNO"]  = "";
			$response["ADDRDES"] = "";
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
			select top 1 ADDRNO,'('+ADDRNO+') '+ADDR1+' '+ADDR2+' '+TUMB as ADDRDT from {$this->MAuth->getdb('CUSTADDR')}
			where CUSCOD='".$cuscod."'
			order by ADDRNO
		";
		$query = $this->db->query($sql);
		
		if($query->row()){
			foreach($query->result() as $row){
				$response["ADDRNO"] = $row->ADDRNO;
				$response["ADDRDT"] = $row->ADDRDT;
			}
		}else{
			$response["ADDRNO"] = "";
			$response["ADDRDT"] = "";
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
	
	function getFormInopt(){
		$html = "
			<div id='inoptform' style='height:100%;'>
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
					<i id='receipt_inopt' class='btn btn-xs btn-primary btn-block glyphicon glyphicon-ok' style='cursor:pointer;'> รับค่า  </i>
				</div>
			</div>
		";
		
		echo json_encode($html);		
	}
	
	function calbilldas(){
		$saleno = $_REQUEST['saleno'];
		$locat	= $_REQUEST['locat'];
		$size 	= sizeof($saleno);
		
		$sql = "
			select free from serviceweb.dbo.fn_branchMaps
			where senior='".$locat."'
		";
		$query = $this->db->query($sql);
		$row = $query->row();
		$locatDas = $row->free;
		
		$cond = "";
		for($i=0;$i<$size;$i++){
			if($cond != ""){ $cond .= ","; }
			$cond .= "'".$saleno[$i]."'";
		}
		
		$response = array();
		if($cond != ''){
			$cond = " and SaleNo in (".$cond.")";
			
			$sql = "
				select sum(TotalAmt) as TotalAmt from DBFREE.dbo.SPSale
				where 1=1 and BranchNo='".$locatDas."' ".$cond."
			";
			//echo $sql; exit;
			$DAS = $this->load->database('DAS',true);
			$query = $DAS->query($sql);
			$row = $query->row();
			
			$response["TotalAmt"] = number_format($row->TotalAmt,2);
			
			$sql = "
				select PartName+' '+cast(cast(SaleQTY as decimal(7,0)) as varchar)+' '+UM as item from DBFREE.dbo.SPSaleDetail
				where 1=1 and BranchNo='".$locatDas."' ".$cond."
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
	
	function calculate_inopt(){
		$arrs = array();
		$response = array();
		
		$qty 	 = $_REQUEST['qty'];
		$uprice  = $_REQUEST['uprice'];
		$cvt 	 = $_REQUEST['cvt'];
		$inclvat = $_REQUEST['inclvat'];
		$vatrt   = $_REQUEST['vatrt'];
		$opCode  = $_REQUEST['opCode'];
		$opText  = $_REQUEST['opText'];
				
		if($opCode == ""){
			$response["status"]	= false;
			$response["html"]	= "ผิดพลาด ยังไม่ระบุอุปกรณ์เสริมทีครับ"; 
			echo json_encode($response); exit;
		}
			
		if($uprice == ""){
			$response["status"]	= false;
			$response["html"]	= "ผิดพลาด ยังไม่ระบุราคาต่อหน่วย"; 
			echo json_encode($response); exit;
		}
		
		if($cvt == ""){
			$response["status"]	= false;
			$response["html"]	= "ผิดพลาด ยังไม่ระบุราคาทุน"; 
			echo json_encode($response); exit;
		}
		
		if($qty == ""){
			$response["status"]	= false;
			$response["html"]	= "ผิดพลาด ยังไม่ระบุจำนวน"; 
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
	
	function getFormCalNopay(){
		$response = array();
		$strno    = $_REQUEST['strno'];
		$inclvat  = $_REQUEST['inclvat'];
		$vatrt	  = ($_REQUEST['vatrt'] == "" ? 0 : $_REQUEST['vatrt']);
		
		if($strno == ""){
			$response["status"] = false;
			$response["msg"] = "ผิดพลาด ไม่พบเลขตัวถัง";
			echo json_encode($response); exit;
		}
		
		$sql = "
			select STDPRC from {$this->MAuth->getdb('INVTRAN')}
			where STRNO='".$strno."'
		";
		$query = $this->db->query($sql);
		$row = $query->row();
		
		$data = array();
		if($inclvat == 'Y'){
			$data["calc_npricev"]	= number_format($row->STDPRC,2);
			$data["calc_nprice"] 	= number_format($row->STDPRC / ((100 + $vatrt)/100),2);
		}else{
			$data["calc_npricev"]	= number_format($row->STDPRC + ($row->STDPRC * ($vatrt/100)),2);
			$data["calc_nprice"] 	= number_format($row->STDPRC,2);
		}
		
		$html = "
			<div id='inoptform' style='height:100%;'>				
					<div class='col-sm-5' style='border:1px dotted black;padding-bottom:30px;'>
						<p class='text-warning col-xs-6'>คำนวนราคาขาย</p>
						<p class='col-xs-6 text-right'><icon id='btnStd' class='btn btn-xs btn-primary btn-block'>ดึงราคาจาก std.</icon></p>
						<div class='row'>
							<div class='col-xs-6'>
								<span id='span_npricev' class='".($inclvat == 'Y' ? 'text-info':'')."' style='font-size:8pt;'>ราคาขายสดรวม VAT</span>
								<div class='input-group'>
									<input type='text' id='calc_npricev' class='form-control input-sm' value='".$data["calc_npricev"]."' ".($inclvat == 'Y' ? '':'disabled').">
									<span class='input-group-addon'>บาท</span>
								</div>
							</div>
							<div class='col-xs-6'>
								<span id='span_nprice' class='".($inclvat == 'Y' ? '':'text-info')."' style='font-size:8pt;'>ราคาขายสดก่อน VAT</span>
								<div class='input-group'>
									<input type='text' id='calc_nprice' class='form-control input-sm' value='".$data["calc_nprice"]."' ".($inclvat == 'Y' ? 'disabled':'').">
									<span class='input-group-addon'>บาท</span>
								</div>
							</div>
							<div class='col-xs-6'>
								<span id='span_ndownv' class='".($inclvat == 'Y' ? 'text-info':'')."' style='font-size:8pt;'>เงินดาวน์รวม VAT</span>
								<div class='input-group'>
									<input type='text' id='calc_ndownv' class='form-control input-sm' ".($inclvat == 'Y' ? '':'disabled').">
									<span class='input-group-addon'>บาท</span>
								</div>
							</div>
							<div class='col-xs-6'>
								<span id='span_ndown' class='".($inclvat == 'Y' ? '':'text-info')."' style='font-size:8pt;'>เงินดาวน์ก่อน VAT</span>
								<div class='input-group'>
									<input type='text' id='calc_ndown' class='form-control input-sm' ".($inclvat == 'Y' ? 'disabled':'').">
									<span class='input-group-addon'>บาท</span>
								</div>
							</div>
							
							<div class='col-xs-6'>
								<span style='font-size:8pt;'>ยอดตั้งลูกหนี้รวม VAT</span>
								<div class='input-group'>
									<input type='text' id='calc_debtorv' class='form-control input-sm' disabled>
									<span class='input-group-addon'>บาท</span>
								</div>
							</div>
							<div class='col-xs-6'>
								<span style='font-size:8pt;'>ยอดตั้งลูกหนี้ก่อน VAT</span>
								<div class='input-group'>
									<input type='text' id='calc_debtor' class='form-control input-sm' disabled>
									<span class='input-group-addon'>บาท</span>
								</div>
							</div>
							
							<div class='col-xs-6'>
								<span id='span_nopay' class='text-info' style='font-size:8pt;'>ผ่อนจำนวน</span>
								<div class='input-group'>
									<input type='text' id='calc_nopay' class='form-control input-sm' >
									<span class='input-group-addon'>เดือน</span>
								</div>
							</div>
							<div class='col-xs-6'>
								<span id='span_nopays' class='text-info' style='font-size:8pt;'>ผ่อนจำนวน</span>
								<div class='input-group'>
									<input type='text' id='calc_nopays' class='form-control input-sm' >
									<span class='input-group-addon'>งวด</span>
								</div>
							</div>
							<div class='col-xs-6'>
								<span id='span_vatyear' class='text-info' style='font-size:8pt;'>อัตราดอกเบี้ยร้อยละ</span>
								<div class='input-group'>
									<input type='text' id='calc_vatyear' class='form-control input-sm' >
									<span class='input-group-addon'>ต่อปี</span>
								</div>
							</div>
							<div class='col-xs-6'>
								<span id='span_vatmonth' style='font-size:8pt;'>อัตราดอกเบี้ยร้อยละ</span>
								<div class='input-group'>
									<input type='text' id='calc_vatmonth' class='form-control input-sm' disabled>
									<span class='input-group-addon'>ต่อเดือน</span>
								</div>
							</div>
						
							<div class='col-xs-6 col-xs-offset-6'>
								<span style='font-size:8pt;'>ดอกเบี้ยรวม</span>
								<div class='input-group'>
									<input type='text' id='calc_vatall' class='form-control input-sm' disabled>
									<span class='input-group-addon'>บาท</span>
								</div>
							</div>
							<div class='col-xs-6'>
								<span style='font-size:8pt;'>ราคาขายผ่อนก่อน VAT</span>
								<div class='input-group'>
									<input type='text' id='calc_sellBvat' class='form-control input-sm' disabled>
									<span class='input-group-addon'>บาท</span>
								</div>
							</div>
							<div class='col-xs-6'>
								<span style='font-size:8pt;'>ผ่อนงวดละ</span>
								<div class='input-group'>
									<input type='text' id='calc_installmentn' class='form-control input-sm' disabled>
									<span class='input-group-addon'>บาท</span>
								</div>
							</div>
							
							<div class='col-xs-6'>
								<span style='font-size:8pt;'>ราคาขายผ่อนรวม VAT</span>
								<div class='input-group'>
									<input type='text' id='calc_sellvat' class='form-control input-sm' disabled>
									<span class='input-group-addon'>บาท</span>
								</div>
							</div>
							<div class='col-xs-6'>
								<span style='font-size:8pt;'>ผ่อนงวดละรวม VAT</span>
								<div class='input-group'>
									<input type='text' id='calc_installmentv' class='form-control input-sm' disabled>
									<span class='input-group-addon'>บาท</span>
								</div>
							</div>
							<div class='col-xs-6'>
								<span style='font-size:8pt;'>ราคาขายผ่อนสุทธิรวม VAT</span>
								<div class='input-group'>
									<input type='text' id='calc_sellvatLast' class='form-control input-sm' disabled>
									<span class='input-group-addon'>บาท</span>
								</div>
							</div>
							<div class='col-xs-6'>
								<span style='font-size:8pt;'>ผ่อนงวดละรวม VAT</span>
								<div class='input-group'>
									<input type='text' id='calc_installmentvLast' class='form-control input-sm' disabled>
									<span class='input-group-addon'>บาท</span>
								</div>
							</div>
						</div>
					</div>
					
					<div class='col-sm-5' style='border:1px dotted black;padding-bottom:30px;'>
						<p class='text-warning'>คำนวณราคาผ่อนเช่าซื้ออุปกรณ์เสริม</p>
						<div class='row'>
							<div class='col-xs-6'>
								<span id='span_npricevOpt' class='".($inclvat == 'Y' ? 'text-info':'')."' style='font-size:8pt;'>ราคาขายสดรวม VAT</span>
								<div class='input-group'>
									<input type='text' id='calc_npricevOpt' class='form-control input-sm' ".($inclvat == 'Y' ? '':'disabled').">
									<span class='input-group-addon'>บาท</span>
								</div>
							</div>
							<div class='col-xs-6'>
								<span id='span_npriceOpt' class='".($inclvat == 'Y' ? '':'text-info')."' style='font-size:8pt;'>ราคาขายสดก่อน VAT</span>
								<div class='input-group'>
									<input type='text' id='calc_npriceOpt' class='form-control input-sm' ".($inclvat == 'Y' ? 'disabled':'').">
									<span class='input-group-addon'>บาท</span>
								</div>
							</div>
							<div class='col-xs-6'>
								<span style='font-size:8pt;'>เงินดาวน์รวม VAT</span>
								<div class='input-group'>
									<input type='text' id='calc_ndownvOpt' class='form-control input-sm' disabled>
									<span class='input-group-addon'>บาท</span>
								</div>
							</div>
							<div class='col-xs-6'>
								<span style='font-size:8pt;'>เงินดาวน์ก่อน VAT</span>
								<div class='input-group'>
									<input type='text' id='calc_ndownOpt' class='form-control input-sm' disabled>
									<span class='input-group-addon'>บาท</span>
								</div>
							</div>
							
							<div class='col-xs-6'>
								<span style='font-size:8pt;'>ยอดตั้งลูกหนี้รวม VAT</span>
								<div class='input-group'>
									<input type='text' id='calc_debtorvOpt' class='form-control input-sm' disabled>
									<span class='input-group-addon'>บาท</span>
								</div>
							</div>
							
							<div class='col-xs-6'>
								<span style='font-size:8pt;'>ยอดตั้งลูกหนี้ก่อน VAT</span>
								<div class='input-group'>
									<input type='text' id='calc_debtorOpt' class='form-control input-sm' disabled>
									<span class='input-group-addon'>บาท</span>
								</div>
							</div>
							<div class='col-xs-6'>
								<span style='font-size:8pt;'>ผ่อนจำนวน</span>
								<div class='input-group'>
									<input type='text' id='calc_nopayOpt' class='form-control input-sm' disabled>
									<span class='input-group-addon'>เดือน</span>
								</div>
							</div>
							<div class='col-xs-6'>
								<span style='font-size:8pt;'>ผ่อนจำนวน</span>
								<div class='input-group'>
									<input type='text' id='calc_nopaysOpt' class='form-control input-sm' disabled>
									<span class='input-group-addon'>งวด</span>
								</div>
							</div>
							<div class='col-xs-6'>
								<span style='font-size:8pt;'>อัตราดอกเบี้ยร้อยละ</span>
								<div class='input-group'>
									<input type='text' id='calc_vatyearOpt' class='form-control input-sm' disabled>
									<span class='input-group-addon'>ต่อปี</span>
								</div>
							</div>
							<div class='col-xs-6'>
								<span style='font-size:8pt;'>อัตราดอกเบี้ยร้อยละ</span>
								<div class='input-group'>
									<input type='text' id='calc_vatmonthOpt' class='form-control input-sm' disabled>
									<span class='input-group-addon'>ต่อเดือน</span>
								</div>
							</div>
						
							<div class='col-xs-6 col-xs-offset-6'>
								<span style='font-size:8pt;'>ดอกเบี้ยรวม</span>
								<div class='input-group'>
									<input type='text' id='calc_vatallOpt' class='form-control input-sm' disabled>
									<span class='input-group-addon'>บาท</span>
								</div>
							</div>
							<div class='col-xs-6'>
								<span style='font-size:8pt;'>ราคาขายผ่อนก่อน VAT</span>
								<div class='input-group'>
									<input type='text' id='calc_sellBvatOpt' class='form-control input-sm' disabled>
									<span class='input-group-addon'>บาท</span>
								</div>
							</div>
							<div class='col-xs-6'>
								<span style='font-size:8pt;'>ผ่อนงวดละ</span>
								<div class='input-group'>
									<input type='text' id='calc_installmentnOpt' class='form-control input-sm' disabled>
									<span class='input-group-addon'>บาท</span>
								</div>
							</div>
							
							<div class='col-xs-6'>
								<span style='font-size:8pt;'>ราคาขายผ่อนรวม VAT</span>
								<div class='input-group'>
									<input type='text' id='calc_sellvatOpt' class='form-control input-sm' disabled>
									<span class='input-group-addon'>บาท</span>
								</div>
							</div>
							<div class='col-xs-6'>
								<span style='font-size:8pt;'>ผ่อนงวดละรวม VAT</span>
								<div class='input-group'>
									<input type='text' id='calc_installmentvOpt' class='form-control input-sm' disabled>
									<span class='input-group-addon'>บาท</span>
								</div>
							</div>
							<div class='col-xs-6'>
								<span style='font-size:8pt;'>ราคาขายผ่อนสุทธิรวม VAT</span>
								<div class='input-group'>
									<input type='text' id='calc_sellvatLastOpt' class='form-control input-sm' disabled>
									<span class='input-group-addon'>บาท</span>
								</div>
							</div>
							<div class='col-xs-6'>
								<span style='font-size:8pt;'>ผ่อนงวดละรวม VAT</span>
								<div class='input-group'>
									<input type='text' id='calc_installmentvLastOpt' class='form-control input-sm' disabled>
									<span class='input-group-addon'>บาท</span>
								</div>
							</div>
						</div>
					</div>
					
					<div class='win2 col-sm-2' style='border:1px dotted black;padding-bottom:30px;background-color:#ccc;'>
						<p class='text-warning'>เงื่อนไข</p>
						<div class='row'>
							<div class='col-sm-7'>
								<div class='form-group'>
									<span style='font-size:8pt;'>ภาษี</span>
									<select id='calc_incvat' class='form-control input-sm' data-placeholder=''></select>
								</div>
							</div>
							<div class='col-sm-5'>
								<br>
								<div class='input-group'>
									<input type='text' id='calc_vatrt' class='form-control input-sm' disabled>
									<span class='input-group-addon'>%</span>
								</div>
							</div>
							<div class='col-sm-12'>
								<div class='form-group'>
									<span style='font-size:8pt;'>อัตราดอกเบี้ย</span>
									<select id='calc_installment' class='form-control input-sm' data-placeholder=''>
										<option value='M'>ต่อเดือน</option>
										<option value='Y' selected>ต่อปี</option>										
									</select>
								</div>
							</div>
							<div class='col-sm-12'>
								<div class='form-group'>
									<span style='font-size:8pt;'>ปัดทศนิยม</span>
									<select id='calc_decimal' class='form-control input-sm' data-placeholder=''>
										<option value='0'>ไม่ปัด</option>
										<option value='1'>ปัด 1 บาท</option>
										<option value='5' selected>ปัด 5 บาท</option>
										<option value='10'>ปัด 10 บาท</option>
									</select>
								</div>
							</div>
						</div>
						<br>
						<icon id='btnCalculate' class='btn btn-sm btn-inverse btn-block'>คำนวน</icon>
						<span class='col-lg-12' style='padding-bottom:22px;'></span>
						<p class='text-warning p20'>ผลลัพธ์</p>
						<div class='row'>
							<div class='col-sm-12'>
								<div class='form-group'>
									<span style='font-size:8pt;'>ราคาขายผ่อน+อุปกรณ์รวม VAT</span>
									<div class='input-group'>
										<input type='text' id='calc_totalSell' class='form-control input-sm' >
										<span class='input-group-addon'>บาท</span>
									</div>
								</div>
							</div>
							<div class='col-sm-12'>
								<div class='form-group'>
									<span style='font-size:8pt;'>ผ่อนงวดละ+อุปกรณ์รวม VAT</span>
									<div class='input-group'>
										<input type='text' id='calc_totalInstallment' class='form-control input-sm' >
										<span class='input-group-addon'>บาท</span>
									</div>
								</div>
							</div>							
						</div>						
						
						<icon id='btnReceived' class='btn btn-sm btn-primary btn-block'>รับค่า</icon>
					</div>
			
			</div>
		";
		
		$response["status"] = true;
		$response["msg"] 	= $html;
		echo json_encode($response); 
	}
	
	function getFormCalNopayCalculate(){
		$arrs = array();
		$arrs["npricev"] 	 = ($_REQUEST["npricev"] == "" ? 0 : str_replace(",","",$_REQUEST["npricev"]));
		$arrs["nprice"] 	 = ($_REQUEST["nprice"] == "" ? 0 : str_replace(",","",$_REQUEST["nprice"]));
		$arrs["ndownv"] 	 = ($_REQUEST["ndownv"] == "" ? 0 : str_replace(",","",$_REQUEST["ndownv"]));
		$arrs["ndown"] 		 = ($_REQUEST["ndown"] == "" ? 0 : str_replace(",","",$_REQUEST["ndown"]));
		$arrs["nopay"] 		 = $_REQUEST["nopay"];
		$arrs["nopays"] 	 = $_REQUEST["nopays"];
		$arrs["vatyear"] 	 = $_REQUEST["vatyear"];
		$arrs["vatmonth"] 	 = $_REQUEST["vatmonth"];
		$arrs["npricevOpt"]  = ($_REQUEST["npricevOpt"] == "" ? 0 : str_replace(",","",$_REQUEST["npricevOpt"]));
		$arrs["npriceOpt"] 	 = ($_REQUEST["npriceOpt"] == "" ? 0 : str_replace(",","",$_REQUEST["npriceOpt"]));
		$arrs["incvat"] 	 = $_REQUEST["incvat"];
		$arrs["vatrt"] 		 = $_REQUEST["vatrt"];
		$arrs["installment"] = $_REQUEST["installment"];
		$arrs["decimal"] 	 = $_REQUEST["decimal"];
		$arrs["resvno"] 	 = $_REQUEST["resvno"];
		
		$response = array();
		$response["status"] = true;
		if($arrs["incvat"]=='Y'){
			//data sell
			if($arrs["npricev"] < $arrs["ndownv"]){
				$response["status"] = false;
				$response["msg"] = "ราคาขายต้องมากกว่าเงินดาวน์ครับ"; 
				echo json_encode($response); exit;
			}
			$response["ds1"] = number_format($arrs["npricev"],2);
			$response["ds2"] = number_format($arrs["npricev"] / ((100 + $arrs["vatrt"])/100),2);
			$response["ds3"] = number_format($arrs["ndownv"],2);
			$response["ds4"] = number_format($arrs["ndownv"] / ((100 + $arrs["vatrt"])/100),2);
			$response["ds5"] = number_format(($arrs["npricev"] - $arrs["ndownv"]) / ((100 + $arrs["vatrt"])/100),2);
			$response["ds6"] = number_format($arrs["npricev"] - $arrs["ndownv"],2);
			$response["ds7"] = $arrs["nopay"];
			$response["ds8"] = $arrs["nopays"];
			$response["ds9"] = ($arrs["installment"] == 'Y' ? $arrs["vatyear"] : number_format($arrs["vatmonth"] * 12,2));
			$response["ds10"] = ($arrs["installment"] == 'M' ? $arrs["vatmonth"] : number_format($arrs["vatyear"] / 12,2));
			$response["ds11"] = number_format(str_replace(",","",$response["ds5"]) * (($response["ds10"] / 100) * $response["ds7"]),2);
			$response["ds12"] = number_format(str_replace(",","",$response["ds2"]) + str_replace(",","",$response["ds11"]),2);
			$response["ds13"] = number_format(((str_replace(",","",$response["ds12"]) - str_replace(",","",$response["ds4"])) / $response["ds7"]),2);
			$response["ds14"] = number_format(str_replace(",","",$response["ds1"]) + (str_replace(",","",$response["ds11"]) * (100 + $arrs["vatrt"])/100),2);
			$response["ds15"] = number_format(((str_replace(",","",$response["ds14"]) - str_replace(",","",$response["ds3"])) / $response["ds7"]),2); 
			$response["ds16"] = 0;
			$response["ds17"] = 0;
			//data option
			
			$response["do1"] = number_format($arrs["npricevOpt"],2);
			$response["do2"] = number_format($arrs["npricevOpt"] / ((100 + $arrs["vatrt"])/100),2);
			$response["do3"] = 0;
			$response["do4"] = 0;
			$response["do5"] = number_format($arrs["npricevOpt"],2);
			$response["do6"] = number_format($arrs["npricevOpt"] / ((100 + $arrs["vatrt"])/100),2);
			$response["do7"] = $arrs["nopay"];
			$response["do8"] = $arrs["nopays"];
			$response["do9"] = 0;
			$response["do10"] = 0;
			$response["do11"] = 0;
			$response["do12"] = number_format((str_replace(",","",$response["do6"])),2);
			$response["do13"] = number_format((str_replace(",","",$response["do6"]) / $response["do7"]),2);
			$response["do14"] = number_format((str_replace(",","",$response["do5"])),2);
			$response["do15"] = number_format((str_replace(",","",$response["do5"]) / $response["do7"]),2);
			$response["do16"] = 0;
			$response["do17"] = 0;
			
			switch($arrs["decimal"]){
				case 0: 
					$response["ds16"] = number_format((str_replace(",","",$response["ds15"]) * $response["ds7"]) + str_replace(",","",$response["ds3"]),2);
					$response["ds17"] = $response["ds15"];
					
					$response["do16"] = (($response["do1"] == $response["do2"] and $response["do1"]==0 ) ? '0.00' : (str_replace(",","",$response["do15"]) * $response["do7"]) + $response["do3"]);
					$response["do17"] = (($response["do1"] == $response["do2"] and $response["do1"]==0 ) ? '0.00' : $response["do15"]);
					break;
				case 1: 
					$ism = str_replace(",","",$response["ds15"]);
					$ism = floor($ism + 1);
					$response["ds16"] = number_format((($ism * $arrs["nopay"])+str_replace(",","",$response["ds3"])),2);
					$response["ds17"] = number_format($ism,2);
					
					$ism = str_replace(",","",$response["do15"]);
					$ism = floor($ism + 1);
					$response["do16"] = (($response["do1"] == $response["do2"] and $response["do1"]==0 ) ? '0.00' : number_format((($ism * $arrs["nopay"])+str_replace(",","",$response["do3"])),2));
					$response["do17"] = (($response["do1"] == $response["do2"] and $response["do1"]==0 ) ? '0.00' : number_format($ism,2));
					break;
				case 5: 
					$ism = str_replace(",","",$response["ds15"]);
					$ism = floor($ism + (5-($ism % 5)));
					$response["ds16"] = number_format((($ism * $arrs["nopay"])+str_replace(",","",$response["ds3"])),2);
					$response["ds17"] = number_format($ism,2);
					
					$ism = str_replace(",","",$response["do15"]);
					$ism = floor($ism + (5-($ism % 5)));
					$response["do16"] = (($response["do1"] == $response["do2"] and $response["do1"]==0 ) ? '0.00' : number_format((($ism * $arrs["nopay"])+str_replace(",","",$response["do3"])),2));
					$response["do17"] = (($response["do1"] == $response["do2"] and $response["do1"]==0 ) ? '0.00' : number_format($ism,2));
					break;
				case 10: 
					$ism = str_replace(",","",$response["ds15"]);
					$ism = floor($ism + (10-($ism % 10)));
					$response["ds16"] = number_format((($ism * $arrs["nopay"])+str_replace(",","",$response["ds3"])),2);
					$response["ds17"] = number_format($ism,2);
					
					$ism = str_replace(",","",$response["do15"]);
					$ism = floor($ism + (10-($ism % 10)));
					$response["do16"] = (($response["do1"] == $response["do2"] and $response["do1"]==0 ) ? '0.00' : number_format((($ism * $arrs["nopay"])+str_replace(",","",$response["do3"])),2));
					$response["do17"] = (($response["do1"] == $response["do2"] and $response["do1"]==0 ) ? '0.00' : number_format($ism,2));
					break;
			}
			
			$response["tt01"] = number_format(str_replace(",","",$response["ds16"]) + str_replace(",","",$response["do16"]),2);
			$response["tt02"] = number_format(str_replace(",","",$response["ds17"]) + str_replace(",","",$response["do17"]),2);
		}else if($arrs["incvat"]=='N'){
			if($arrs["nprice"] < $arrs["ndown"]){
				$response["status"] = false;
				$response["msg"] = "ราคาขายต้องมากกว่าเงินดาวน์ครับ"; 
				echo json_encode($response); exit;
			}
			$response["ds1"] = number_format($arrs["nprice"] * ((100 + $arrs["vatrt"])/100));
			$response["ds2"] = number_format($arrs["nprice"],2);
			$response["ds3"] = number_format($arrs["ndown"] * ((100 + $arrs["vatrt"])/100),2);
			$response["ds4"] = number_format($arrs["ndown"],2);
			$response["ds5"] = number_format(str_replace(",","",$response["ds2"]) - str_replace(",","",$response["ds4"]),2);
			$response["ds6"] = number_format(str_replace(",","",$response["ds1"]) - str_replace(",","",$response["ds3"]),2);
			$response["ds7"] = $arrs["nopay"];
			$response["ds8"] = $arrs["nopays"];
			$response["ds9"] = ($arrs["installment"] == 'Y' ? $arrs["vatyear"] : $arrs["vatmonth"] * 12 );
			$response["ds10"] = ($arrs["installment"] == 'M' ? $arrs["vatmonth"] : $arrs["vatyear"] / 12 );
			$response["ds11"] = number_format(str_replace(",","",$response["ds5"]) * (($response["ds10"] / 100) * $response["ds7"]),2);
			$response["ds12"] = number_format((str_replace(",","",$response["ds2"]) + str_replace(",","",$response["ds11"])),2);
			$response["ds13"] = number_format(((str_replace(",","",$response["ds12"]) - str_replace(",","",$response["ds4"])) / $response["ds7"]),2);
			$response["ds14"] = number_format(str_replace(",","",$response["ds1"]) + (str_replace(",","",$response["ds11"]) * ((100 + $arrs["vatrt"])/100)),2);
			$response["ds15"] = number_format(((str_replace(",","",$response["ds14"]) - str_replace(",","",$response["ds3"])) / $response["ds7"]),2); 
			$response["ds16"] = 0;
			$response["ds17"] = 0;
			
			$response["do1"] = number_format($arrs["npriceOpt"] * ((100 + $arrs["vatrt"])/100),2);
			$response["do2"] = number_format($arrs["npriceOpt"],2);
			$response["do3"] = 0;
			$response["do4"] = 0;
			$response["do5"] = number_format($arrs["npriceOpt"] * ((100 + $arrs["vatrt"])/100),2);
			$response["do6"] = number_format($arrs["npriceOpt"],2);
			$response["do7"] = $arrs["nopay"];
			$response["do8"] = $arrs["nopays"];
			$response["do9"] = 0;
			$response["do10"] = 0;
			$response["do11"] = 0;
			$response["do12"] = number_format((str_replace(",","",$response["do6"])),2);
			$response["do13"] = number_format((str_replace(",","",$response["do6"]) / $response["do7"]),2);
			$response["do14"] = number_format((str_replace(",","",$response["do5"])),2);
			$response["do15"] = number_format((str_replace(",","",$response["do5"]) / $response["do7"]),2);
			$response["do16"] = 0;
			$response["do17"] = 0;
			
			switch($arrs["decimal"]){
				case 0: 
					$response["ds16"] = (str_replace(",","",$response["ds15"]) * $response["ds7"]) + str_replace(",","",$response["ds3"]);
					$response["ds17"] = $response["ds15"];
					
					$response["do16"] = (($response["do1"] == $response["do2"] and $response["do1"]==0 ) ? '0.00' : (str_replace(",","",$response["do15"]) * $response["do7"]) + $response["do3"]);
					$response["do17"] = (($response["do1"] == $response["do2"] and $response["do1"]==0 ) ? '0.00' : $response["do15"]);
					break;
				case 1: 
					$ism = str_replace(",","",$response["ds15"]);
					$ism = floor($ism + 1);
					$response["ds16"] = number_format((($ism * $arrs["nopay"])+str_replace(",","",$response["ds3"])),2);
					$response["ds17"] = number_format($ism,2);
					
					$ism = str_replace(",","",$response["do15"]);
					$ism = floor($ism + 1);
					$response["do16"] = (($response["do1"] == $response["do2"] and $response["do1"]==0 ) ? '0.00' : number_format((($ism * $arrs["nopay"])+str_replace(",","",$response["do3"])),2));
					$response["do17"] = (($response["do1"] == $response["do2"] and $response["do1"]==0 ) ? '0.00' : number_format($ism,2));
					break;
				case 5: 
					$ism = str_replace(",","",$response["ds15"]);
					$ism = floor($ism + (5-($ism % 5)));
					$response["ds16"] = number_format((($ism * $arrs["nopay"])+str_replace(",","",$response["ds3"])),2);
					$response["ds17"] = number_format($ism,2);
					
					$ism = str_replace(",","",$response["do15"]);
					$ism = floor($ism + (5-($ism % 5)));
					$response["do16"] = (($response["do1"] == $response["do2"] and $response["do1"]==0 ) ? '0.00' : number_format((($ism * $arrs["nopay"])+str_replace(",","",$response["do3"])),2));
					$response["do17"] = (($response["do1"] == $response["do2"] and $response["do1"]==0 ) ? '0.00' : number_format($ism,2));
					break;
				case 10: 
					$ism = str_replace(",","",$response["ds15"]);
					$ism = floor($ism + (10-($ism % 10)));
					$response["ds16"] = number_format((($ism * $arrs["nopay"])+str_replace(",","",$response["ds3"])),2);
					$response["ds17"] = number_format($ism,2);
					
					$ism = str_replace(",","",$response["do15"]);
					$ism = floor($ism + (10-($ism % 10)));
					$response["do16"] = (($response["do1"] == $response["do2"] and $response["do1"]==0 ) ? '0.00' : number_format((($ism * $arrs["nopay"])+str_replace(",","",$response["do3"])),2));
					$response["do17"] = (($response["do1"] == $response["do2"] and $response["do1"]==0 ) ? '0.00' : number_format($ism,2));
					break;
			}
			
			$response["tt01"] = number_format(str_replace(",","",$response["ds16"]) + str_replace(",","",$response["do16"]),2);
			$response["tt02"] = number_format(str_replace(",","",$response["ds17"]) + str_replace(",","",$response["do17"]),2);			
		}
		
		if($arrs["resvno"] != ''){
			$sql = "select * from {$this->MAuth->getdb('ARRESV')} where RESVNO='".$arrs["resvno"]."'";
			$query = $this->db->query($sql);
			
			if($query->row()){
				foreach($query->result() as $row){
					if($row->RESPAY > str_replace(',','',$response["ds3"])){
						$response["status"] = false;
						$response["msg"] = "เงินจองต้องไม่มากกว่าเงินดาวน์ครับ"; 
						echo json_encode($response); exit;
					}
				}
			}
		}
		
		echo json_encode($response);
	}
	
	function getReceivedCAL(){
		$arrs = array();
		$response = array();
		
		$arrs["aincvat"] = $_REQUEST["aincvat"];
		$arrs["avatrt"]  = $_REQUEST["avatrt"];
		$arrs["cincvat"] = $_REQUEST["cincvat"];
		$arrs["cvatrt"]  = $_REQUEST["cvatrt"];
		
		$arrs["npricev"] = str_replace(",","",$_REQUEST["npricev"]);
		$arrs["nprice"]  = str_replace(",","",$_REQUEST["nprice"]);
		$arrs["ndownv"]  = str_replace(",","",$_REQUEST["ndownv"]);
		$arrs["ndown"] 	 = str_replace(",","",$_REQUEST["ndown"]);
		$arrs["debtorv"] = str_replace(",","",$_REQUEST["debtorv"]);
		$arrs["debtor"]  = str_replace(",","",$_REQUEST["debtor"]);
		
		$arrs["nopay"]    = $_REQUEST["nopay"];
		$arrs["nopays"]   = $_REQUEST["nopays"];
		$arrs["vatyear"]  = $_REQUEST["vatyear"];
		$arrs["vatmonth"] = $_REQUEST["vatmonth"];
		
		$arrs["vatall"] 			 = str_replace(",","",$_REQUEST["vatall"]);
		$arrs["sellBvat"] 			 = str_replace(",","",$_REQUEST["sellBvat"]);
		$arrs["installmentn"] 		 = str_replace(",","",$_REQUEST["installmentn"]);
		$arrs["sellvatLast"] 		 = str_replace(",","",$_REQUEST["sellvatLast"]);
		$arrs["installmentvLast"] 	 = str_replace(",","",$_REQUEST["installmentvLast"]);
		$arrs["sellBvatOpt"] 		 = str_replace(",","",$_REQUEST["sellBvatOpt"]);
		$arrs["installmentnOpt"] 	 = str_replace(",","",$_REQUEST["installmentnOpt"]);
		$arrs["sellvatLastOpt"] 	 = str_replace(",","",$_REQUEST["sellvatLastOpt"]);
		$arrs["installmentvLastOpt"] = str_replace(",","",$_REQUEST["installmentvLastOpt"]);
		$arrs["totalSell"] 			 = str_replace(",","",$_REQUEST["totalSell"]);
		$arrs["totalInstallment"] 	 = str_replace(",","",$_REQUEST["totalInstallment"]);
		
		$arrs["strno"] = $_REQUEST["strno"];
		$arrs["duefirst"] = $this->Convertdate(1,$_REQUEST["duefirst"]);
		
		$arrs["npricevOpt"] = str_replace(",","",$_REQUEST["npricevOpt"]);
		$arrs["npriceOpt"] 	= str_replace(",","",$_REQUEST["npriceOpt"]);
		
		$arrs["vatCal"] = ((100 + $arrs["avatrt"])/100);
		
		$sql = "
			select * from {$this->MAuth->getdb('INVTRAN')}
			where STRNO='".$arrs["strno"]."'
		";
		$query = $this->db->query($sql);
		$row = $query->row();
		$response["STDPRC"] = number_format($row->STDPRC,2);
		
		$sql = "
			select *,convert(varchar(8),dateadd(month,(".$arrs["nopays"]."-1),'".$arrs["duefirst"]."'),112) as duelast
			from {$this->MAuth->getdb('CONDPAY')}
		";
		$query = $this->db->query($sql);
		$row = $query->row();
		
		$response["DELAY_DAY"] = $row->DELAY_DAY;
		$response["INT_RATE"]  = $row->INT_RATE;
		$response["DISC_FM"]   = $row->DISC_FM;
		$response["CALINT"]    = $row->CALINT;
		$response["duelast"]   = $this->Convertdate(2,$row->duelast);
		
		switch($arrs["aincvat"]){
			case 'Y':
				$response["sell"] 		 = number_format($arrs["totalSell"],2);
				$response["down"] 		 = number_format($arrs["ndownv"],2);
				$response["nopay"] 		 = $arrs["nopays"];
				$response["upay"] 		 = $arrs["nopays"] / $arrs["nopay"];
				$response["pay1"] 		 = number_format($arrs["totalInstallment"],2);
				$response["pay2"] 		 = number_format($arrs["totalInstallment"],2);
				$response["pay3"] 		 = number_format($arrs["totalInstallment"],2);
				$response["sellrv"]  	 = number_format($arrs["npricev"],2);
				$response["sellFresh"] 	 = number_format($arrs["npricev"]+$arrs["npricevOpt"],2);
				$response["interate"] 	 = number_format(($arrs["totalSell"] - ($arrs["npricev"]+$arrs["npricevOpt"])) / $arrs["vatCal"],2);  //number_format((($arrs["totalSell"] / $arrs["vatCal"])  - ($arrs["npricev"]/$arrs["vatCal"])),2);
				$response["vatyear"] 	 = number_format($arrs["vatyear"],2);
				$response["vatyearReal"] = number_format((((( str_replace(",","",$response["sell"]) - ($arrs["npricev"]+$arrs["npricevOpt"])) / $response["nopay"]) * (100 / (($arrs["npricev"]+$arrs["npricevOpt"]) - $arrs["ndownv"]))) * 12),2);
			break;
			case 'N':
				$response["sell"] 		 = number_format(($arrs["sellBvat"]+$arrs["sellBvatOpt"]),2);
				$response["down"] 		 = number_format($arrs["ndown"],2);
				$response["nopay"] 		 = $arrs["nopays"];
				$response["upay"]		 = $arrs["nopays"] / $arrs["nopay"];
				$response["pay1"] 		 = number_format(($arrs["installmentn"]+$arrs["installmentnOpt"]),2);
				$response["pay2"] 		 = number_format(($arrs["installmentn"]+$arrs["installmentnOpt"]),2);
				$response["pay3"] 		 = number_format((($arrs["installmentn"]+$arrs["installmentnOpt"]) * $arrs["vatCal"]),2);
				$response["sellrv"]  	 = number_format($arrs["nprice"],2);
				$response["sellFresh"] 	 = number_format($arrs["nprice"]+$arrs["npriceOpt"],2);
				$response["interate"] 	 = number_format((($arrs["sellBvat"]+$arrs["sellBvatOpt"]) - ($arrs["nprice"]+$arrs["npriceOpt"])) / $arrs["vatCal"],2); //number_format(($arrs["vatall"] + $arrs["sellBvatOpt"]),2);
				$response["vatyear"] 	 = number_format($arrs["vatyear"],2);
				$response["vatyearReal"] = number_format((((( str_replace(",","",$response["sell"]) - ($arrs["nprice"]+$arrs["npriceOpt"])) / $response["nopay"]) * (100 / (($arrs["nprice"]+$arrs["npriceOpt"]) - $arrs["ndown"]))) * 12),2);
			break;
		}
		
		echo json_encode($response);
	}
	
	public function getDetailsCond(){
		$arrs = array();
		$response = array("status"=>true,"msg"=>"");
		
		$arrs["add_vattype"] = $_REQUEST["aincvat"];
		$arrs["add_vatrt1"] = ($_REQUEST["avatrt"])/100;
		$arrs["add_vatrt2"] = ($_REQUEST["avatrt"]+100)/100;
		
		$arrs["add2_hp"] = str_replace(",","",$_REQUEST["ainprc"]);
		$arrs["add2_down"] = str_replace(",","",$_REQUEST["aindwn"]);
		$arrs["add_resvno"] = $_REQUEST["aresvno"];
		$arrs["add3_payfirst"] = str_replace(",","",$_REQUEST["apayfirst"]);
		$arrs["add3_paynext"] = str_replace(",","",$_REQUEST["apaynext"]);
		$arrs["add3_paylast"] = str_replace(",","",$_REQUEST["apaylast"]);
		$arrs["add3_sell"] = str_replace(",","",$_REQUEST["asell"]);
		$arrs["add3_sellv"] = str_replace(",","",$_REQUEST["atotalSell"]);
		$arrs["add3_interest"] = $_REQUEST["ainterest"];
		
		if($arrs["add2_hp"] == "" or $arrs["add2_hp"] == 0){
			$response["status"] = false;
			$response["msg"] = "คุณยังไม่ได้คำนวณราคาขาย";
		}else{
			$c_arrs = $this->calDetails($arrs);
			
			$response["html"] = "
				<div class='form-control jzbgbluelow text-dark'>
					<div style='width:100%;overflow:auto;'>	
						<table class='table table-sm table-striped' style='width:100%;white-space:nowrap;border:0.1px solid;'>
							<thead style='background-color:#269da1;color:#efff14;text-align:center;'>
								<tr>
									<th></th>
									<th>ยอดเงินรวมภาษี</th>
									<th>ภาษีมูลค่าเพิ่ม</th>
									<th>มูลค่าสินค้า</th>
								</tr>
							</thead>						
							<tbody>
								<tr>
									<td class='text-right' style='background-color:#269da1;color:#efff14;'>ราคาขาย</td>
									<td class='text-right'>".number_format($c_arrs["add2_hp1"],2)."</td>
									<td class='text-right'>".number_format($c_arrs["add2_hp2"],2)."</td>
									<td class='text-right'>".number_format($c_arrs["add2_hp3"],2)."</td>
								</tr>
								<tr>
									<td class='text-right' style='background-color:#269da1;color:#efff14;'>เงินจอง</td>
									<td class='text-right'>".number_format($c_arrs["add2_resv1"],2)."</td>
									<td class='text-right'>".number_format($c_arrs["add2_resv2"],2)."</td>
									<td class='text-right'>".number_format($c_arrs["add2_resv3"],2)."</td>
								</tr>
								<tr>
									<td class='text-right' style='background-color:#269da1;color:#efff14;'>ยอดคงเหลือ</td>
									<td class='text-right'>".number_format(($c_arrs["add2_hp1"]-$c_arrs["add2_resv1"]),2)."</td>
									<td class='text-right'>".number_format(($c_arrs["add2_hp2"]-$c_arrs["add2_resv2"]),2)."</td>
									<td class='text-right'>".number_format(($c_arrs["add2_hp3"]-$c_arrs["add2_resv3"]),2)."</td>
								</tr>
								<tr>
									<td class='text-right' style='background-color:#269da1;color:#efff14;'>เงินดาวน์</td>
									<td class='text-right'>".number_format($c_arrs["add2_down1"],2)."</td>
									<td class='text-right'>".number_format($c_arrs["add2_down2"],2)."</td>
									<td class='text-right'>".number_format($c_arrs["add2_down3"],2)."</td>
								</tr>
								<tr>
									<td class='text-right' style='background-color:#269da1;color:#efff14;'>ยอดตั้งลูกหนี้</td>
									<td class='text-right'>".number_format($c_arrs["add2_hp1"]-$c_arrs["add2_down1"],2)."</td>
									<td class='text-right'>".number_format($c_arrs["add2_hp2"]-$c_arrs["add2_down2"],2)."</td>
									<td class='text-right'>".number_format($c_arrs["add2_hp3"]-$c_arrs["add2_down3"],2)."</td>
								</tr>
								
							</tbody>
							
							<thead style='background-color:#269da1;color:#efff14;text-align:center;'>
								<tr>
									<th></th>
									<th>ยอดเงินรวมภาษี</th>
									<th>ภาษีมูลค่าเพิ่ม</th>
									<th>มูลค่าสินค้า</th>
								</tr>
							</thead>						
							<tbody>
								<tr>
									<td class='text-right' style='background-color:#269da1;color:#efff14;'>ค่างวดงวดแรก</td>
									<td class='text-right'>".number_format($c_arrs["add3_payfirst1"],2)."</td>
									<td class='text-right'>".number_format($c_arrs["add3_payfirst2"],2)."</td>
									<td class='text-right'>".number_format($c_arrs["add3_payfirst3"],2)."</td>
								</tr>
								<tr>
									<td class='text-right' style='background-color:#269da1;color:#efff14;'>ค่างวดงวดถัดไป</td>
									<td class='text-right'>".number_format($c_arrs["add3_paynext1"],2)."</td>
									<td class='text-right'>".number_format($c_arrs["add3_paynext2"],2)."</td>
									<td class='text-right'>".number_format($c_arrs["add3_paynext3"],2)."</td>
								</tr>
								<tr>
									<td class='text-right' style='background-color:#269da1;color:#efff14;'>ค่างวดงวดสุดท้าย</td>
									<td class='text-right'>".number_format($c_arrs["add3_paylast1"],2)."</td>
									<td class='text-right'>".number_format($c_arrs["add3_paylast2"],2)."</td>
									<td class='text-right'>".number_format($c_arrs["add3_paylast3"],2)."</td>
								</tr>
								<tr>
									<td class='text-right' style='background-color:#269da1;color:#efff14;'>ราคาขายเงินสด</td>
									<td class='text-right'>".number_format($c_arrs["add3_sellv1"],2)."</td>
									<td class='text-right'>".number_format($c_arrs["add3_sellv2"],2)."</td>
									<td class='text-right'>".number_format($c_arrs["add3_sellv3"],2)."</td>
								</tr>
							</tbody>
						</table>
					</div>	
				</div>
			";
		}
		
		echo json_encode($response);
	}
	
	function getFormMGAR(){
		$html = "
			<div id='mgarform' style='height:100%;'>
				<div class='row'>
					<div class='col-sm-12'>
						<div class='form-group'>
							รหัส/ชื่อ ผู้ค้ำประกัน
							<select id='mgar_cuscod' class='form-control input-xs' data-placeholder='รหัส/ชื่อ ผู้ค้ำประกัน'></select>
						</div>					
					<div>
				</div>
				<div class='row'>
					<div class='col-sm-12'>
						<div class='form-group'>
							ที่อยู่คนค้ำ
							<select id='mgar_addrno' class='form-control input-xs' data-placeholder='ที่อยู่คนค้ำ'></select>
						</div>					
					<div>
				</div>
				<div class='row'>
					<div class='col-sm-12'>	
						<div class='form-group'>
							ความสัมพันธ์
							<input type='text' id='mgar_relation' class='form-control input-sm' placeholder='ความสัมพันธ์' >
						</div>
					</div>
				</div>	
				<div class='row'>
					<div class='col-sm-12'>	
						<div id='mgar_results'></div>
					</div>
				</div>
				<div class='row'>
					<div class='col-sm-12'>	
						<i id='mgar_receipt' class='btn btn-xs btn-primary btn-block glyphicon glyphicon-ok' style='cursor:pointer;'> รับค่า  </i>
					</div>
				</div>				
			</div>
		";
		
		echo json_encode($html);
	}
	
	function getFormOTHMGAR(){
		$sql = "select GARCODE,'('+GARCODE+') '+GARDESC as GARDESC from {$this->MAuth->getdb('SETARGAR')}";
		$query = $this->db->query($sql);
		
		$argar = "";
		if($query->row()){
			foreach($query->result() as $row){
				$argar .= "<option value='".str_replace(chr(0),'',$row->GARCODE)."'>".$row->GARDESC."</option>";
			}
		}
		
		$html = "
			<div id='othmgarform' style='height:100%;'>
				<div class='row'>
					<div class='col-sm-12'>
						<div class='form-group'>
							รหัสหลักทรัพย์
							<select id='othmgar_garcod' class='form-control input-xs' data-placeholder='รหัสหลักทรัพย์'>".$argar."</select>
						</div>					
					<div>
				</div>
				<div class='row'>
					<div class='col-sm-12'>	
						<div class='form-group'>
							เลขที่อ้างอิง
							<input type='text' id='othmgar_refno' class='form-control input-sm' placeholder='เลขที่อ้างอิง' >
						</div>
					</div>
				</div>	
				<div class='row'>
					<div class='col-sm-12'>	
						<div id='othmgar_results'></div>
					</div>
				</div>
				<div class='row'>
					<div class='col-sm-12'>	
						<i id='othmgar_receipt' class='btn btn-xs btn-primary btn-block glyphicon glyphicon-ok' style='cursor:pointer;'> รับค่า  </i>
					</div>
				</div>				
			</div>
		";
		
		echo json_encode($html);
	}
	
	private function calDetails($arrs){
		$arrs["RESPAY"] = 0;
		$arrs["RESVAT"] = 0;
		$arrs["RESPAYVAT"] = 0;
		
		$arrs["add2_resv1"] = 0;	
		$arrs["add2_resv2"] = 0;	
		$arrs["add2_resv3"] = 0;
		
		if($arrs["add_resvno"] != ""){
			$sql = "
				select * from {$this->MAuth->getdb('ARRESV')}
				where RESVNO='".$arrs["add_resvno"]."'
			";
			$query = $this->db->query($sql);
			
			if($query->row()){
				foreach($query->result() as $row){
					$arrs["add2_resv1"] = str_replace(",","",number_format($row->RESPAY / ((100+$row->VATRT) / 100),2));	
					$arrs["add2_resv2"] = str_replace(",","",number_format($row->RESPAY - $arrs["add2_resv1"],2));	
					$arrs["add2_resv3"] = str_replace(",","",number_format($row->RESPAY,2));
				}
			}
		}
		
		if($arrs["add_vattype"] == "Y"){
			$arrs["add2_hp1"] = $arrs["add2_hp"];
			$arrs["add2_hp2"] = $arrs["add2_hp"] - ($arrs["add2_hp"] / $arrs["add_vatrt2"]);
			$arrs["add2_hp3"] = $arrs["add2_hp"] / $arrs["add_vatrt2"];
			
			$arrs["add2_down1"] = $arrs["add2_down"];
			$arrs["add2_down2"] = $arrs["add2_down"] - ($arrs["add2_down"] / $arrs["add_vatrt2"]);
			$arrs["add2_down3"] = $arrs["add2_down"] / $arrs["add_vatrt2"];
			
			$arrs["add3_payfirst1"] = $arrs["add3_payfirst"];
			$arrs["add3_payfirst2"] = $arrs["add3_payfirst"] - ($arrs["add3_payfirst"] / $arrs["add_vatrt2"]);
			$arrs["add3_payfirst3"] = $arrs["add3_payfirst"] / $arrs["add_vatrt2"];
			
			$arrs["add3_paynext1"] = $arrs["add3_paynext"];
			$arrs["add3_paynext2"] = $arrs["add3_paynext"] - ($arrs["add3_paynext"] / $arrs["add_vatrt2"]);
			$arrs["add3_paynext3"] = $arrs["add3_paynext"] / $arrs["add_vatrt2"];	

			$arrs["add3_paylast1"] = $arrs["add3_paylast"];
			$arrs["add3_paylast2"] = $arrs["add3_paylast"] - ($arrs["add3_paylast"] / $arrs["add_vatrt2"]);
			$arrs["add3_paylast3"] = $arrs["add3_paylast"] / $arrs["add_vatrt2"];
			
			$arrs["add3_sellv1"] = $arrs["add3_sellv"];
			$arrs["add3_sellv2"] = $arrs["add3_sellv"] - ($arrs["add3_sellv"] / $arrs["add_vatrt2"]);
			$arrs["add3_sellv3"] = $arrs["add3_sellv"] / $arrs["add_vatrt2"];
			
		}else{
			$arrs["add2_hp1"] = $arrs["add2_hp"] * $arrs["add_vatrt2"];
			$arrs["add2_hp2"] = $arrs["add2_hp"] * $arrs["add_vatrt1"];
			$arrs["add2_hp3"] = $arrs["add2_hp"];
			
			$arrs["add2_down1"] = $arrs["add2_down"] * $arrs["add_vatrt2"];
			$arrs["add2_down2"] = $arrs["add2_down"] * $arrs["add_vatrt1"];
			$arrs["add2_down3"] = $arrs["add2_down"];
			
			$arrs["add3_payfirst1"] = $arrs["add3_payfirst"] * $arrs["add_vatrt2"];
			$arrs["add3_payfirst2"] = $arrs["add3_payfirst"] * $arrs["add_vatrt1"];
			$arrs["add3_payfirst3"] = $arrs["add3_payfirst"];
			
			$arrs["add3_paynext1"] = $arrs["add3_paynext"] * $arrs["add_vatrt2"];
			$arrs["add3_paynext2"] = $arrs["add3_paynext"] * $arrs["add_vatrt1"];
			$arrs["add3_paynext3"] = $arrs["add3_paynext"];
			
			$arrs["add3_paylast1"] = $arrs["add3_paylast"];
			$arrs["add3_paylast2"] = $arrs["add3_paylast"] - ($arrs["add3_paylast"] / $arrs["add_vatrt2"]);
			$arrs["add3_paylast3"] = $arrs["add3_paylast"] / $arrs["add_vatrt2"];
			
			$arrs["add3_sellv1"] = $arrs["add3_sellv"] * $arrs["add_vatrt2"];
			$arrs["add3_sellv2"] = $arrs["add3_sellv"] * $arrs["add_vatrt1"];
			$arrs["add3_sellv3"] = $arrs["add3_sellv"];
		}
		
		return $arrs;
	}
	
	function manualCal(){
		$arrs = array();
		$arrs["inclvat"] 	= $_REQUEST["inclvat"];
		$arrs["vatrt"] 		= $_REQUEST["vatrt"];
		$arrs["inprc"] 		= str_replace(",","",$_REQUEST["inprc"]);
		$arrs["indwn"] 		= str_replace(",","",$_REQUEST["indwn"]);
		$arrs["nopay"] 		= str_replace(",","",$_REQUEST["nopay"]);
		$arrs["payfirst"] 	= str_replace(",","",$_REQUEST["payfirst"]);
		$arrs["paynext"] 	= str_replace(",","",$_REQUEST["paynext"]);
		$arrs["paylast"] 	= str_replace(",","",$_REQUEST["paylast"]);
		$arrs["sell"] 		= str_replace(",","",$_REQUEST["sell"]);
		$arrs["totalSell"] 	= str_replace(",","",$_REQUEST["totalSell"]);
		$arrs["interest"] 	= str_replace(",","",$_REQUEST["interest"]);
		
		$arrs["vatCal"] 	= str_replace(",","",number_format((100 + $_REQUEST["vatrt"] / 100),2));
		
		if($arrs["inclvat"] == "Y"){
			
		}else{
			
		}		
	}
	
	function save(){
		$arrs = array();
		$arrs["contno"] 	= $_REQUEST["contno"];
		$arrs["locat"] 		= $_REQUEST["locat"];
		$arrs["sdate"] 		= $this->Convertdate(1,$_REQUEST["sdate"]);
		$arrs["resvno"] 	= $_REQUEST["resvno"];
		$arrs["approve"] 	= $_REQUEST["approve"];
		$arrs["cuscod"] 	= $_REQUEST["cuscod"];
		$arrs["inclvat"] 	= $_REQUEST["inclvat"];
		$arrs["vatrt"] 		= $_REQUEST["vatrt"];
		$arrs["addrno"] 	= $_REQUEST["addrno"];
		$arrs["strno"] 		= $_REQUEST["strno"];
		$arrs["reg"] 		= $_REQUEST["reg"];
		$arrs["paydue"] 	= $_REQUEST["paydue"];
		$arrs["inopt"] 		= (isset($_REQUEST["inopt"]) ? $_REQUEST["inopt"] : array());
		$arrs["inprc"] 		= str_replace(',','',$_REQUEST["inprc"]);
		$arrs["indwn"] 		= str_replace(',','',$_REQUEST["indwn"]);		
		$arrs["dwninv"] 	= str_replace(',','',$_REQUEST["dwninv"]);
		$arrs["dwninvDt"] 	= $_REQUEST["dwninvDt"];
		$arrs["nopay"] 		= $_REQUEST["nopay"];
		$arrs["upay"] 		= $_REQUEST["upay"];
		$arrs["payfirst"] 	= str_replace(',','',$_REQUEST["payfirst"]);
		$arrs["paynext"] 	= str_replace(',','',$_REQUEST["paynext"]);
		$arrs["paylast"] 	= str_replace(',','',$_REQUEST["paylast"]);
		$arrs["sell"] 		= str_replace(',','',$_REQUEST["sell"]);
		$arrs["totalSell"] 	= str_replace(',','',$_REQUEST["totalSell"]);
		$arrs["interest"] 	= str_replace(',','',$_REQUEST["interest"]);
		$arrs["duefirst"] 	= $this->Convertdate(1,$_REQUEST["duefirst"]);
		$arrs["duelast"] 	= $this->Convertdate(1,$_REQUEST["duelast"]);
		$arrs["release"] 	= $_REQUEST["release"];
		$arrs["released"] 	= $this->Convertdate(1,$_REQUEST["released"]);
		$arrs["billcoll"] 	= $_REQUEST["emp"];
		$arrs["checker"] 	= $_REQUEST["audit"];
		$arrs["delyrt"] 	= $_REQUEST["intRate"];
		$arrs["dlday"] 		= $_REQUEST["delay"];
		$arrs["intrt"] 		= $_REQUEST["interestRate"];
		$arrs["efrate"] 	= $_REQUEST["interestRateReal"];
		$arrs["empSell"] 	= $_REQUEST["empSell"];
		$arrs["agent"] 		= str_replace(',','',($_REQUEST["agent"] == '' ? 0 : $_REQUEST["agent"]));
		$arrs["acticod"] 	= $_REQUEST["acticod"];
		$arrs["nextlastmonth"] = $_REQUEST["nextlastmonth"];
		$arrs["recomcod"] 	= $_REQUEST["advisor"];
		$arrs["paydown"] 	= str_replace(',','',($_REQUEST["paydown"] == "" ? 0 : $_REQUEST["paydown"]));
		$arrs["payall"] 	= str_replace(',','',($_REQUEST["payall"] == "" ? 0 : $_REQUEST["payall"]));
		$arrs["comext"] 	= str_replace(',','',($_REQUEST["commission"] == "" ? 0 : $_REQUEST["commission"]));
		$arrs["comopt"] 	= str_replace(',','',($_REQUEST["free"] == "" ? 0 : $_REQUEST["free"]));
		$arrs["comoth"] 	= str_replace(',','',($_REQUEST["payother"] == "" ? 0 : $_REQUEST["payother"]));
		$arrs["calint"] 	= $_REQUEST["calint"];
		$arrs["discfm"] 	= $_REQUEST["discfm"];
		$arrs["comments"] 	= $_REQUEST["comments"];
		$arrs["billdas"] 	= (isset($_REQUEST["billdas"]) ? $_REQUEST["billdas"] : array());
		$arrs["mgar"] 		= (isset($_REQUEST["mgar"]) ? $_REQUEST["mgar"] : array());
		$arrs["othmgar"] 	= (isset($_REQUEST["othmgar"]) ? $_REQUEST["othmgar"] : array());
		$arrs["cal"] 		= $_REQUEST["cal"];
		
		$data = "";
		if($arrs["interest"] 	== ""){ $data = "ดอกผลเช่าซื้อ"; }
		if($arrs["totalSell"] 	== ""){ $data = "ราคาขายสดสุทธิ"; }
		if($arrs["sell"] 		== ""){ $data = "ราคาขายหน้าร้าน"; }
		if($arrs["paylast"] 	== ""){ $data = "ค่างวดสุดท้าย + ภาษี"; }
		if($arrs["paynext"] 	== ""){ $data = "ค่างวดถัดไป"; }
		if($arrs["payfirst"] 	== ""){ $data = "ค่างวดแรก"; }
		if($arrs["upay"] 		== ""){ $data = "ผ่อนชำระ ด./งวด"; }
		if($arrs["nopay"] 		== ""){ $data = "จำนวนงวดที่ผ่อน"; }
		if($arrs["indwn"] 		== ""){ $data = "เงินดาวน์"; }
		if($arrs["inprc"] 		== ""){ $data = "ราคาขายผ่อน"; }
		if($arrs["paydue"] 		== ""){ $data = "วิธีชำระค่างวด"; }
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
				
		$dasSize = sizeof($arrs["billdas"]);
		$mapMEMO1 = "";
		for($i=0;$i<$dasSize;$i++){
			if($mapMEMO1 != ""){ $mapMEMO1 .= ","; }
			$mapMEMO1 .= $arrs["billdas"][$i];	
		}
		$arrs["comments"] = $mapMEMO1."[explode]".$arrs["comments"];		
		
		$arrs["insertARMGAR"] = "";
		$mgarSize = sizeof($arrs["mgar"]);
		for($i=0;$i<$mgarSize;$i++){
			$arrs["insertARMGAR"] .= "
				insert into {$this->MAuth->getdb('ARMGAR')} (
					[TSALE],[CONTNO],[LOCAT],[GARNO],[CUSCOD],[ADDRNO],[RELATN]
				) values (
					'H',@CONTNO,'".$arrs["locat"]."','".$arrs["mgar"][$i][0]."','".$arrs["mgar"][$i][1]."','".$arrs["mgar"][$i][2]."','".$arrs["mgar"][$i][3]."'
				);
			";
		}
			
		$arrs["insertAROTHGAR"] = "";
		$mgarSize = sizeof($arrs["othmgar"]);
		for($i=0;$i<$mgarSize;$i++){
			$arrs["insertAROTHGAR"] .= "
				insert into {$this->MAuth->getdb('AROTHGAR')} (
					[TSALE],[CONTNO],[LOCAT],[GARNO],[GARCODE],[REFFNO]
				) values (
					'H',@CONTNO,'".$arrs["locat"]."','".$arrs["othmgar"][$i][0]."','".$arrs["othmgar"][$i][1]."','".$arrs["othmgar"][$i][2]."'
				);
			";
		}
		
		$arrs["updateARRESV"] = "";
		if($arrs["resvno"] != ''){
			$sql = "
				select * from {$this->MAuth->getdb('ARRESV')}
				where RESVNO='".$arrs["resvno"]."'
			";
			$query = $this->db->query($sql);
			
			if($query->row()){
				foreach($query->result() as $row){
					$arrs["resv_smpay"] = $row->RESPAY;
					$arrs["resv_smchq"] = 0;
					
					$arrs["npayres"]    = str_replace(',','',number_format($row->RESPAY / ((100 + $row->VATRT) / 100),2));
					$arrs["vatpres"] 	= str_replace(',','',number_format($row->RESPAY - $arrs["npayres"],2));
					$arrs["totpres"] 	= $row->RESPAY;
				}
			}
			
			$arrs["updateARRESV"] = "
				if(( select count(*) from {$this->MAuth->getdb('ARRESV')} where RESVNO='".$arrs["resvno"]."' and isnull(RECVDT,'')='' and isnull(SDATE,'')='') > 0)
				begin 
					update {$this->MAuth->getdb('ARRESV')}
					set ISSUNO	= '".$arrs["release"]."',
						RECVDT	= GETDATE(),
						SDATE	= GETDATE()
					where RESVNO='".$arrs["resvno"]."'
				end
				else 
				begin
					rollback tran leasingTran;
					insert into #leasingTemp select 'E' as id,'','บันทึกไม่สำเร็จ ไม่สามารถใช้เลขที่ใบจอง ".$arrs["resvno"]." ได้ โปรดตรอจสอบใหม่อีกครั้ง' as msg;
					return;
				end;
			";
			
			if($arrs["resv_smpay"] > $arrs["indwn"]){
				$response["status"] = 'E';
				$response["msg"] = "เงินจองต้องไม่มากกว่าเงินดาวน์ครับ";
				echo json_encode($response); exit; 
			}
		}else{
			$arrs["resv_smpay"] = 0;
			$arrs["resv_smchq"] = 0;
			$arrs["npayres"]    = 0;
			$arrs["vatpres"] 	= 0;
			$arrs["totpres"]	= 0;
		}
		
		$arrs["stdprc"] 	= $arrs["sell"];
		$arrs["dscprc"] 	= 0;
		$arrs["keyinprc"] 	= $arrs["inprc"];
		if($arrs["inclvat"] == 'Y'){
			$arrs["nprice"] 	= str_replace(',','',number_format($arrs["inprc"] / ((100 + $arrs["vatrt"]) / 100),2));
			$arrs["vatprc"] 	= str_replace(',','',number_format($arrs["inprc"] - $arrs["nprice"],2));
			$arrs["totprc"] 	= $arrs["inprc"];
			$arrs["keyindwn"] 	= $arrs["indwn"];
			$arrs["ndawn"] 		= str_replace(',','',number_format($arrs["indwn"] / ((100 + $arrs["vatrt"]) / 100),2));
			$arrs["vatdwn"] 	= str_replace(',','',number_format($arrs["indwn"] - $arrs["ndawn"],2));
			$arrs["totdwn"] 	= $arrs["ndawn"] + $arrs["vatdwn"];
			$arrs["keyinfupay"] = $arrs["payfirst"];
			$arrs["n_fupay"] 	= str_replace(',','',number_format($arrs["payfirst"] / ((100 + $arrs["vatrt"]) / 100),2));		
			$arrs["v_fupay"] 	= str_replace(',','',number_format($arrs["payfirst"] - $arrs["n_fupay"],2));
			$arrs["t_fupay"] 	= $arrs["payfirst"];			
			$arrs["keyinupay"] 	= $arrs["paynext"];
			$arrs["n_upay"] 	= str_replace(',','',number_format($arrs["paynext"] / ((100 + $arrs["vatrt"]) / 100),2));			
			$arrs["v_upay"] 	= str_replace(',','',number_format($arrs["paynext"] - $arrs["n_upay"],2));
			$arrs["tot_upay"] 	= $arrs["paynext"];			
			$arrs["lpaytot"] 	= 0;
			$arrs["n_lupay"] 	= str_replace(',','',number_format($arrs["paylast"] / ((100 + $arrs["vatrt"]) / 100),2));		
			$arrs["v_lupay"] 	= str_replace(',','',number_format($arrs["paylast"] - $arrs["n_lupay"],2));
			$arrs["t_lupay"] 	= $arrs["paylast"];
			
			$arrs["keyincshprc"]= $arrs["totalSell"];
			$arrs["ncshprc"] 	= str_replace(',','',number_format($arrs["totalSell"] / ((100 + $arrs["vatrt"]) / 100),2));		
			$arrs["vcshprc"] 	= str_replace(',','',number_format($arrs["totalSell"] - $arrs["ncshprc"],2));
			$arrs["tcshprc"] 	= $arrs["totalSell"];
		}else{
			$arrs["nprice"] 	= $arrs["inprc"];
			$arrs["vatprc"] 	= str_replace(',','',number_format($arrs["inprc"] * ($arrs["vatrt"] / 100),2));
			$arrs["totprc"] 	= str_replace(',','',number_format($arrs["nprice"] + $arrs["vatprc"],2));
			$arrs["keyindwn"] 	= $arrs["indwn"];
			$arrs["ndawn"] 		= $arrs["indwn"];
			$arrs["vatdwn"] 	= str_replace(',','',number_format($arrs["indwn"] * ($arrs["vatrt"] / 100),2));
			$arrs["totdwn"] 	= $arrs["ndawn"] + $arrs["vatdwn"];
			$arrs["keyinfupay"] = $arrs["payfirst"];
			$arrs["n_fupay"] 	= $arrs["payfirst"];
			$arrs["v_fupay"] 	= str_replace(',','',number_format($arrs["payfirst"] * ($arrs["vatrt"] / 100),2));
			$arrs["t_fupay"] 	= str_replace(',','',number_format($arrs["n_fupay"] + $arrs["v_fupay"],2));
			$arrs["keyinupay"] 	= $arrs["paynext"];
			$arrs["n_upay"] 	= $arrs["paynext"];
			$arrs["v_upay"] 	= str_replace(',','',number_format($arrs["paynext"] * ($arrs["vatrt"] / 100),2));
			$arrs["tot_upay"] 	= str_replace(',','',number_format($arrs["n_upay"] + $arrs["v_upay"],2));
			$arrs["lpaytot"] 	= 0;
			$arrs["n_lupay"] 	= str_replace(',','',number_format($arrs["paylast"] / ((100 + $arrs["vatrt"]) / 100),2));		
			$arrs["v_lupay"] 	= str_replace(',','',number_format($arrs["paylast"] - $arrs["n_lupay"],2));
			$arrs["t_lupay"] 	= $arrs["paylast"];
			
			$arrs["keyincshprc"]= $arrs["totalSell"];
			$arrs["ncshprc"] 	= $arrs["totalSell"];
			$arrs["vcshprc"] 	= str_replace(',','',number_format($arrs["totalSell"] * ($arrs["vatrt"] / 100),2));
			$arrs["tcshprc"] 	= str_replace(',','',number_format($arrs["ncshprc"] + $arrs["vcshprc"],2));
		}
		
		$arrs["balanc"] 	= $arrs["totprc"] - $arrs["totpres"];
		$arrs["nkang"] 		= $arrs["nprice"] - $arrs["ndawn"];
		$arrs["vkang"] 		= $arrs["vatprc"] - $arrs["vatdwn"];
		$arrs["tkang"] 		= $arrs["totprc"] - $arrs["totdwn"];
		$arrs["paydwn"] 	= $arrs["resv_smpay"]; //เอาเงินจองไปเป็นเงินดาวน์
		$arrs["smpay"] 		= $arrs["resv_smpay"];
		$arrs["smchq"] 		= $arrs["resv_smchq"];
		$arrs["salcod"] 	= $arrs["empSell"];
		$arrs["comitn"] 	= $arrs["agent"];
		
		$arrs["insertARPAY"] = "";		
		$STRPROF = ($arrs["interest"] / $arrs["nopay"]);
		for($i=1;$i<=$arrs["nopay"];$i++){
			$setdue = "";
			if($arrs["nextlastmonth"] == "Y"){			
				$setdue = "dateadd(day,-1,dateadd(month,".$i.",(left('".$arrs["duefirst"]."',6)+'01')))";
			}else{
				$setdue = "dateadd(month,".($i - 1).",convert(datetime,'".$arrs["duefirst"]."'))";
			}
			
			//เช็คว่าดิวเกินอัตราภาษีที่เซ้ตไว้ใน VATMAST หรือไม่
			$sql = "
				select count(*) cs,convert(varchar(8),".$setdue.",112) as duefail from {$this->MAuth->getdb('VATMAST')} 
				where ".$setdue." between FRMDATE and TODATE
			";
			$q = $this->db->query($sql);
			$r = $q->row();
			
			if($r->cs == 0){
				$response["status"] = 'E';
				$response["msg"] = "อัตรา VAT ที่เซ็ตไว้ ไม่ครอบคลุมถึงดิว ".$this->Convertdate(2,$r->duefail);
				echo json_encode($response); exit; 
			}
			
			if($arrs["insertARPAY"] != ""){ $arrs["insertARPAY"] .= "union all"; }
			$arrs["insertARPAY"] .= "
			select @CONTNO,'".$arrs["locat"]."',".$i.",".$setdue.",".str_replace(",","",number_format($arrs["t_fupay"],2)).",".str_replace(",","",number_format($arrs["v_fupay"],2)).",".str_replace(",","",number_format($arrs["n_fupay"],2)).",0,0,0,0,0,".$arrs["vatrt"].",0,0,0,0,'','".$this->sess["USERID"]."',0,0,'',0,".str_replace(",","",number_format($STRPROF,2))." ";
		}
		
		if($arrs["insertARPAY"] != ""){
			$arrs["insertARPAY"] = "
				insert into {$this->MAuth->getdb('ARPAY')}( CONTNO, LOCAT, NOPAY, DDATE, DAMT, V_DAMT, N_DAMT, NINSTAL, NPROF, PAYMENT, V_PAYMENT, N_PAYMENT, VATRT, DELAY, ADVDUE, DISCT, INTERT, TAXINV, USERID, INTAMT,TAXAMT, GRDCOD, TAXPAY, STRPROF )
				".$arrs["insertARPAY"].";
			";
		}
		
		$arrs['insertOpt'] = "";
		$arrs["optprc"]  = 0;
		$arrs["optpvt"]  = 0;
		$arrs["optptot"] = 0;
		$arrs["optcst"]  = 0;
		$arrs["optcvt"]  = 0;
		$arrs["optctot"] = 0;
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
					'H',@contno,'".$arrs["locat"]."','".$arrs["inopt"][$i][0]."',".$arrs["inopt"][$i][1]."
					,0.00,".$arrs["inopt"][$i][2].",".$arrs["inopt"][$i][5].",".$arrs["inopt"][$i][4].",".$arrs["inopt"][$i][3]."
					,".$arrs["inopt"][$i][6].",".$arrs["inopt"][$i][7].",".$arrs["inopt"][$i][8].",''
					,'".$this->sess["USERID"]."',getdate(),null,'".$arrs["sdate"]."',''
				);
				
				update {$this->MAuth->getdb('OPTMAST')}
				set ONHAND=ONHAND-".$arrs["inopt"][$i][2]."
				where OPTCODE='".$arrs["inopt"][$i][0]."' and LOCAT='".$arrs["locat"]."';
			";
		}
		
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
		
		if($arrs["contno"] == 'Auto Genarate'){
			$this->saveinleasing($arrs);
		}else{
			$this->updateinleasing($arrs);
		}
	}
	
	private function saveinleasing($arrs){
		$sql = "
			if OBJECT_ID('tempdb..#leasingTemp') is not null drop table #leasingTemp;
			create table #leasingTemp (id varchar(20),contno varchar(20),msg varchar(max));
			
			begin tran leasingTran
			begin try
			
				/* @symbol = สัญลักษณ์แทนประเภทของเลขที่ นั้นๆ */
				declare @symbol varchar(10) = (select H_MASTNO from {$this->MAuth->getdb('CONDPAY')});
				/* @rec = รหัสพื้นฐาน */
				declare @rec varchar(10) = (select SHORTL+@symbol+'-'+right(left(convert(varchar(8),GETDATE(),112),6),4) from {$this->MAuth->getdb('INVLOCAT')} where LOCATCD='".$arrs['locat']."');
				/* @RESVNO = รหัสที่จะใช้ */
				declare @CONTNO varchar(12) = isnull((select MAX(CONTNO) from {$this->MAuth->getdb('ARMAST')} where CONTNO like ''+@rec+'%' collate thai_cs_as),@rec+'0000');
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
				
				insert into {$this->MAuth->getdb('ARMAST')} (
					[CONTNO],[LOCAT],[CUSCOD],[ADDRNO],[RESVNO]
					,[TSALE],[STRNO],[CONTSTAT],[INCLVAT],[VATRT]
					,[SDATE],[STDPRC],[DSCPRC],[KEYINPRC],[NPRICE]
					,[VATPRC],[TOTPRC],[NPAYRES],[VATPRES],[TOTPRES]
					,[BALANC],[KEYINDWN],[NDAWN],[VATDWN],[TOTDWN]
					,[NKANG],[VKANG],[TKANG],[PAYDWN],[SMPAY]
					,[SMCHQ],[SALCOD],[COMITN],[OPTPRC],[OPTPVT]
					,[OPTPTOT],[OPTCST],[OPTCVT],[OPTCTOT],[NCARCST]
					,[VCARCST],[TCARCST],[TAXNO],[TAXDT],[EXP_PRD]
					,[EXP_AMT],[EXP_FRM],[EXP_TO],[LPAYD],[LPAYA]
					,[YSTAT],[YDATE],[T_NOPAY],[T_UPAY],[KEYINFUPAY]
					,[N_FUPAY],[V_FUPAY],[T_FUPAY],[KEYINUPAY],[N_UPAY]
					,[V_UPAY],[TOT_UPAY],[LPAYTOT],[N_LUPAY],[V_LUPAY]
					,[T_LUPAY],[FDATE],[LDATE],[CLOSAR],[ISSUNO]
					,[ISSUDT],[KEYINCSHPRC],[NCSHPRC],[VCSHPRC],[TCSHPRC]
					,[NPROFIT],[CHECKER],[BILLCOLL],[HLDNO],[INTRT]
					,[EFRATE],[DELYRT],[DLDAY],[FLRATE],[FLSTOPV]
					,[DTSTOPV],[CONFIR],[CONFIRID],[CONFIRDT],[MEMO1]
					,[FLCANCL],[USERID],[INPDT],[DELID],[DELDT]
					,[POSTDT],[PAYTYP],[CALINT],[CALDSC],[APPVNO]
					,[LUPDINT],[COMEXT],[COMOPT],[COMOTH],[GRDCOD]
					,[RECOMCOD],[ACTICOD],[CLOSDT],[EXP_DAY],[PROF_METHOD]
					,[EFFRT_AFADJ],[STOPPROF_DT],[NETFREE],[VATFREE],[TOTFREE]
					,[ITEMNPRC],[ITEMVPRC],[ITEMTPRC],[LIMITF],[INSUR]
					,[ACT],[CAMPCODE]
				) values (
					@CONTNO,'".$arrs['locat']."','".$arrs['cuscod']."','".$arrs['addrno']."','".$arrs['resvno']."'
					,'H','".$arrs['strno']."','ป','".$arrs['inclvat']."',".$arrs['vatrt']."
					,'".$arrs['sdate']."',".$arrs['stdprc'].",".$arrs["dscprc"].",".$arrs['keyinprc'].",".$arrs['nprice']."
					,".$arrs["vatprc"].",".$arrs["totprc"].",".$arrs["npayres"].",".$arrs["vatpres"].",".$arrs["totpres"]."
					,".$arrs["balanc"].",".$arrs["keyindwn"].",".$arrs["ndawn"].",".$arrs["vatdwn"].",".$arrs["totdwn"]."
					,".$arrs["nkang"].",".$arrs["vkang"].",".$arrs["tkang"].",".$arrs["paydwn"].",".$arrs["smpay"]."
					,".$arrs["smchq"].",'".$arrs["salcod"]."',".$arrs["comitn"].",".$arrs["optprc"].",".$arrs["optpvt"]."
					,".$arrs["optptot"].",".$arrs["optcst"].",".$arrs["optcvt"].",".$arrs["optctot"].",".$arrs["ncarcst"]."
					,".$arrs["vcarcst"].",".$arrs["tcarcst"].",@TAXNO,@TAXDT,0
					,0,0,0,'".$arrs['sdate']."',0
					,'N',null,".$arrs['nopay'].",".$arrs['upay'].",".$arrs["keyinfupay"]."
					,".$arrs["n_fupay"].",".$arrs["v_fupay"].",".$arrs["t_fupay"].",".$arrs["keyinupay"].",".$arrs["n_upay"]."
					,".$arrs["v_upay"].",".$arrs["tot_upay"].",".$arrs["lpaytot"].",".$arrs["n_lupay"].",".$arrs["v_lupay"]."
					,".$arrs["t_lupay"].",'".$arrs["duefirst"]."','".$arrs["duelast"]."','','".$arrs["release"]."'
					,'".$arrs['released']."',".$arrs["keyincshprc"].",".$arrs["ncshprc"].",".$arrs["vcshprc"].",".$arrs["tcshprc"]."
					,".$arrs["interest"].",'".$arrs['checker']."','".$arrs['billcoll']."',0,".$arrs["intrt"]."
					,".$arrs["efrate"].",".$arrs["delyrt"].",".$arrs["dlday"].",0.0000,''
					,null,'','',null,'".$arrs["comments"]."'
					,'','".$this->sess["USERID"]."',getdate(),'',null
					,null,'".$arrs["paydue"]."',".$arrs["calint"].",".$arrs["discfm"].",'".$arrs["approve"]."'
					,null,".$arrs["comext"].",".$arrs["comopt"].",".$arrs["comoth"].",''
					,'".$arrs["recomcod"]."','".$arrs["acticod"]."',null,0,''
					,'0.000000',null,0.00,0.00,0.00
					,0.00,0.00,0.00,0.00,'N'
					,'N',''
				);
				
				/* ตารางผ่อน  ARPAY */
					".$arrs["insertARPAY"]."
				
				/* INVTRAN */
				if ((select count(*) from {$this->MAuth->getdb('INVTRAN')}
					 where STRNO = '".$arrs["strno"]."' and FLAG='D' and SDATE is null and isnull(CONTNO,'')=''
						and ((CURSTAT = 'R' and RESVNO='".$arrs["resvno"]."') or (isnull(CURSTAT,'')='' and isnull(RESVNO,'')=''))
					) > 0 )
				begin 
					update {$this->MAuth->getdb('INVTRAN')}
					set FLAG 	= 'C',
						SDATE 	= '".$arrs["sdate"]."',
						PRICE 	= ".$arrs["keyinprc"].",
						CONTNO 	= @CONTNO,
						TSALE 	= 'H'
					where STRNO = '".$arrs["strno"]."' and CRLOCAT = '".$arrs["locat"]."' and FLAG = 'D' and isnull(CONTNO,'')=''
				end 
				else 
				begin 
					rollback tran leasingTran;
					insert into #leasingTemp select 'E' as id,'','บันทึกไม่สำเร็จ ไม่สามารถใช้เลขที่ตัวถัง ".$arrs["strno"]." ได้  อาจมีการขายไปแล้ว หรือมีการจองแล้วครับ โปรดตรอจสอบใหม่อีกครั้ง' as msg;
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
						'".$arrs["locat"]."',@TAXNO,@TAXDT,'H',@CONTNO,
						'".$arrs["cuscod"]."','','','','".$arrs["strno"]."',
						'',null,'".$arrs["vatrt"]."',".str_replace(",","",number_format($arrs["ndawn"],2)).",".str_replace(",","",number_format($arrs["vatdwn"],2)).",
						".str_replace(",","",number_format($arrs["totdwn"],2)).",'ใบกำกับเงินดาวน์ขายผ่อน','',0,'',
						0,getdate(),'',null,'S',
						'N','".$this->sess["USERID"]."','','','',
						'','','','',null
					);
				END;
				
				
				/* ใบจอง  ARRESV */
					".$arrs["updateARRESV"]."
					
				/*อุปกรณ์เสริมรวมราคารถ ARINOPT */
					DISABLE Trigger ALL ON {$this->MAuth->getdb('ARINOPT')};
					".$arrs['insertOpt']."
					ENABLE Trigger ALL ON {$this->MAuth->getdb('ARINOPT')};
				
				/* คนค้ำประกัน  ARMGAR */
					".$arrs["insertARMGAR"]."
					
				/* หลักทรัพย์ประกัน  AROTHGAR */
					".$arrs["insertAROTHGAR"]."	
					
				insert into {$this->MAuth->getdb('hp_UserOperationLog')} (userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
				values ('".$this->sess["IDNo"]."','SYS04::บันทึกขายผ่อนแล้ว',@CONTNO+' ".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');

				insert into #leasingTemp select 'S',@CONTNO,'บันทึกรายการขาย เลขที่สัญญา '+@CONTNO+' แล้วครับ';
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
				
				if('".$arrs["cal"]."' = 'y' and (select count(*) from {$this->MAuth->getdb('CHQTRAN')}
					where CONTNO=@CONTNO and TSALE='H' and CANDT is null and CANTIME is null and isnull(CANID,'') = '') > 0)
				begin
					declare @FORDESC varchar(250) = (
						select top 1 b.FORDESC from {$this->MAuth->getdb('CHQTRAN')} a
						left join {$this->MAuth->getdb('PAYFOR')} b on a.PAYFOR=b.FORCODE
						where CONTNO=@CONTNO and TSALE='H' and CANDT is null and CANTIME is null and isnull(CANID,'') = ''
						order by INPDT asc
					);
					
					rollback tran leasingTran;
					insert into #leasingTemp select 'E' as id,'','บันทึกไม่สำเร็จ เนื่องจากเลขที่สัญญา '+@CONTNO+' มีการบันทึก '+@FORDESC+' แล้ว กรณีมีการคำนวนค่างวดใหม่จะต้องไม่มีการรับชำระค่าใดๆ ทั้งสิ้น' as msg;
					return;
				end
				else
				begin 
					update {$this->MAuth->getdb('ARMAST')}
					set 
					where CONTNO=@CONTNO
					
					insert into {$this->MAuth->getdb('ARMAST')} (
					
					[STDPRC],[DSCPRC],[KEYINPRC],[NPRICE]
					,[VATPRC],[TOTPRC],[NPAYRES],[VATPRES],[TOTPRES]
					,[BALANC],[KEYINDWN],[NDAWN],[VATDWN],[TOTDWN]
					,[NKANG],[VKANG],[TKANG],[PAYDWN],[SMPAY]
					,[SMCHQ],[NCARCST]
					
					,[VCARCST],[TCARCST],[TAXNO],[TAXDT],[EXP_PRD]
					,[EXP_AMT],[EXP_FRM],[EXP_TO],[LPAYD],[LPAYA]
					,[YSTAT],[YDATE],[T_NOPAY],[T_UPAY],[KEYINFUPAY]
					,[N_FUPAY],[V_FUPAY],[T_FUPAY],[KEYINUPAY],[N_UPAY]
					,[V_UPAY],[TOT_UPAY],[LPAYTOT],[N_LUPAY],[V_LUPAY]
					,[T_LUPAY],[FDATE],[LDATE],[CLOSAR],[ISSUNO]
					,[ISSUDT],[KEYINCSHPRC],[NCSHPRC],[VCSHPRC],[TCSHPRC]
					,[NPROFIT],[CHECKER],[BILLCOLL],[HLDNO],[INTRT]
					,[EFRATE],[DELYRT],[DLDAY],[FLRATE],[FLSTOPV]
					,[DTSTOPV],[CONFIR],[CONFIRID],[CONFIRDT],[MEMO1]
					,[FLCANCL],[USERID],[INPDT],[DELID],[DELDT]
					,[POSTDT],[PAYTYP],[CALINT],[CALDSC],[APPVNO]
					,[LUPDINT],[COMEXT],[COMOPT],[COMOTH],[GRDCOD]
					,[RECOMCOD],[ACTICOD],[CLOSDT],[EXP_DAY],[PROF_METHOD]
					,[EFFRT_AFADJ],[STOPPROF_DT],[NETFREE],[VATFREE],[TOTFREE]
					,[ITEMNPRC],[ITEMVPRC],[ITEMTPRC],[LIMITF],[INSUR]
					,[ACT],[CAMPCODE]
				) values (
					
					
					".$arrs['stdprc'].",".$arrs["dscprc"].",".$arrs['keyinprc'].",".$arrs['nprice']."
					,".$arrs["vatprc"].",".$arrs["totprc"].",".$arrs["npayres"].",".$arrs["vatpres"].",".$arrs["totpres"]."
					,".$arrs["balanc"].",".$arrs["keyindwn"].",".$arrs["ndawn"].",".$arrs["vatdwn"].",".$arrs["totdwn"]."
					,".$arrs["nkang"].",".$arrs["vkang"].",".$arrs["tkang"].",".$arrs["paydwn"].",".$arrs["smpay"]."
					,".$arrs["smchq"].",".$arrs["ncarcst"]."
					
					,".$arrs["vcarcst"].",".$arrs["tcarcst"].",@TAXNO,@TAXDT,0
					,0,0,0,'".$arrs['sdate']."',0
					,'N',null,".$arrs['nopay'].",".$arrs['upay'].",".$arrs["keyinfupay"]."
					,".$arrs["n_fupay"].",".$arrs["v_fupay"].",".$arrs["t_fupay"].",".$arrs["keyinupay"].",".$arrs["n_upay"]."
					,".$arrs["v_upay"].",".$arrs["tot_upay"].",".$arrs["lpaytot"].",".$arrs["n_lupay"].",".$arrs["v_lupay"]."
					,".$arrs["t_lupay"].",'".$arrs["duefirst"]."','".$arrs["duelast"]."','','".$arrs["release"]."'
					,'".$arrs['released']."',".$arrs["keyincshprc"].",".$arrs["ncshprc"].",".$arrs["vcshprc"].",".$arrs["tcshprc"]."
					,".$arrs["interest"].",'".$arrs['checker']."','".$arrs['billcoll']."',0,".$arrs["intrt"]."
					,".$arrs["efrate"].",".$arrs["delyrt"].",".$arrs["dlday"].",0.0000,''
					,null,'','',null,'".$arrs["comments"]."'
					,'','".$this->sess["USERID"]."',getdate(),'',null
					,null,'".$arrs["paydue"]."',".$arrs["calint"].",".$arrs["discfm"].",'".$arrs["approve"]."'
					,null,".$arrs["comext"].",".$arrs["comopt"].",".$arrs["comoth"].",''
					,'".$arrs["recomcod"]."','".$arrs["acticod"]."',null,0,''
					,'0.000000',null,0.00,0.00,0.00
					,0.00,0.00,0.00,0.00,'N'
					,'N',''
				);
					
					/* ตารางผ่อน  ARPAY */
					delete from {$this->MAuth->getdb('ARPAY')} where CONTNO=@CONTNO
					".$arrs["insertARPAY"]."
				end
				
				
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
					delete {$this->MAuth->getdb('ARMGAR')} where CONTNO=@CONTNO;
					".$arrs["insertARMGAR"]."
					
				/* หลักทรัพย์ประกัน  AROTHGAR */
					delete {$this->MAuth->getdb('AROTHGAR')} where CONTNO=@CONTNO;
					".$arrs["insertAROTHGAR"]."	
					
					
				/*อุปกรณ์เสริมรวมราคารถ ARINOPT */
					DISABLE Trigger ALL ON {$this->MAuth->getdb('ARINOPT')};
					update b 
					set b.ONHAND = b.ONHAND+a.QTY
					from {$this->MAuth->getdb('ARINOPT')} a
					left join {$this->MAuth->getdb('OPTMAST')} b on a.LOCAT=b.LOCAT and a.OPTCODE=b.OPTCODE
					where CONTNO=@CONTNO
					
					delete from {$this->MAuth->getdb('ARINOPT')} where CONTNO=@CONTNO;
					
					".$arrs['insertOpt']."					
					ENABLE Trigger ALL ON {$this->MAuth->getdb('ARINOPT')};
					
				
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
				
				if((select count(*) from {$this->MAuth->getdb('CHQTRAN')}
					where CONTNO=@CONTNO and TSALE='H' and CANDT is null and CANTIME is null and isnull(CANID,'') = '') > 0)
				begin
					declare @FORDESC varchar(250) = (
						select top 1 b.FORDESC from {$this->MAuth->getdb('CHQTRAN')} a
						left join {$this->MAuth->getdb('PAYFOR')} b on a.PAYFOR=b.FORCODE
						where CONTNO=@CONTNO and TSALE='H' and CANDT is null and CANTIME is null and isnull(CANID,'') = ''
						order by INPDT asc
					);
					
					rollback tran leasingTran;
					insert into #leasingTemp select 'E' as id,'','ลบสัญญาไม่สำเร็จ เนื่องจากเลขที่สัญญา '+@CONTNO+' มีการบันทึก '+@FORDESC+' แล้ว' as msg;
					return;
				end				
				
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
				,convert(varchar(8),a.LDATE,112) as LDATE
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
				,a.KEYINCSHPRC
				,a.DSCPRC
				,a.TOTDWN
				,a.KEYINCSHPRC-a.TOTDWN as DTPD
				,a.INTRT
				,a.EFRATE
				,a.TOTPRC
				,a.T_FUPAY
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
				,a.TOTDWN as HP
				,0 as FN
				,a.TOTPRES
				,a.TOTDWN - a.TOTPRES as TOTDR
				,a.PAYDWN - a.TOTPRES as PAYDWN
				,a.TOTDWN - a.PAYDWN as REV
				,a.T_NOPAY
				,a.TKANG
				
				,a.OPTPTOT
			from {$this->MAuth->getdb('ARMAST')} a
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
				$data[16] = number_format($row->OPTPTOT,2);
				$data[18] = number_format($row->KEYINCSHPRC,2);
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
	
	// 20191009 created
	function getDataANALYZE(){
		$ANID = $_POST["ANID"];
		
		$sql = "
			declare @INT_RATE decimal(7,2) = (select INT_RATE from {$this->MAuth->getdb('CONDPAY')});
			declare @DELAY_DAY decimal(7,2) = (select DELAY_DAY from {$this->MAuth->getdb('CONDPAY')});
			
			select a.ID,isnull(a.RESVNO,'') as RESVNO,a.DWN,a.NOPAY,1 as NOPAYPerMonth
				,a.STRNO,a.MODEL,a.BAAB,a.COLOR
				,a.PRICE
				,a.INTEREST_RT
				,b.CUSCOD
				,c.SNAM+c.NAME1+' '+c.NAME2+' ('+c.CUSCOD+')'+'-'+c.GRADE as CUSNAME
				,b.ADDRDOCNO
				,'('+d.ADDRNO+') '+d.ADDR1+' '+d.ADDR2+' ต.'+d.TUMB+' อ.'+e.AUMPDES+' จ.'+f.PROVDES+' '+d.ZIP as ADDRNODetails 	
				--,a.STDID,a.STDPLRANK
				,@INT_RATE as INT_RATE
				,@DELAY_DAY as DELAY_DAY
				
				,(isnull(g.insurance,0)+isnull(g.transfers,0)+isnull(g.regist,0)+isnull(g.act,0)+isnull(g.coupon,0))
					- isnull(a.DWN_INSURANCE,0) as STD_OPT_TOTAL
				,isnull(a.DWN_INSURANCE,0) as DWN_INSURANCE
			from {$this->MAuth->getdb('ARANALYZE')} a
			left join {$this->MAuth->getdb('ARANALYZEREF')} b on a.ID=b.ID and b.CUSTYPE=0
			left join {$this->MAuth->getdb('CUSTMAST')} c on b.CUSCOD=c.CUSCOD collate thai_cs_as
			left join {$this->MAuth->getdb('CUSTADDR')} d on b.CUSCOD=d.CUSCOD collate thai_cs_as and b.ADDRDOCNO=d.ADDRNO collate thai_cs_as
			left join {$this->MAuth->getdb('SETAUMP')} e on d.AUMPCOD=e.AUMPCOD
			left join {$this->MAuth->getdb('SETPROV')} f on d.PROVCOD=f.PROVCOD
			left join {$this->MAuth->getdb('std_down')} g on a.STDID=g.id and a.STDPLRANK=g.plrank and a.DWN between g.dwnrate_s and g.dwnrate_e
			where a.ID='".$ANID."'
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$data = array();
		if($query->row()){
			foreach($query->result() as $row){
				foreach($row  as $key => $val){
					switch($key){
						case 'DWN': 
							$data[$key] = number_format($val,2);
							$data['ORI_DWN'] = $val;
							break;
						default:
							$data[$key] = str_replace(chr(0),'',$val);
							break;
					}
				}
				
				/*
				$sql = "
					select price,pricespecial 
						,interest_rate,interest_rate2
						,insurance,transfers,regist,act,coupon 
						,isnull(insurance,0)+isnull(transfers,0)+isnull(regist,0)+isnull(act,0)+isnull(coupon,0) as total
					from {$this->MAuth->getdb('std_vehicles')} a
					left join {$this->MAuth->getdb('std_pricelist')} b on a.id=b.id
					left join {$this->MAuth->getdb('std_down')} c on b.id=c.id and b.plrank=c.plrank
					where a.id='".$data["STDID"]."' and b.plrank='".$data["STDPLRANK"]."' and '".$data["ORI_DWN"]."' between c.dwnrate_s and c.dwnrate_e
				";
				//echo $sql; exit;
				$query_std = $this->db->query($sql);

				$data["std_price"] 			= 0;
				$data["std_pricespecial"] 	= 0;
				$data["std_interest_rate"] 	= 0;
				$data["std_interest_rate2"] = 0;
				$data["std_opt_total"]		= 0;
				if($query_std->row()){
					foreach($query_std->result() as $row_std){
						$data["std_price"] 			= $row_std->price;
						$data["std_pricespecial"] 	= $row_std->pricespecial;
						$data["std_interest_rate"] 	= $row_std->interest_rate;
						$data["std_interest_rate2"] = $row_std->interest_rate2;
						$data["std_opt_total"]		= $row_std->total - $data["DWN_INSURANCE"];
					}
				}
				*/
				
				$sql = "
					select * from {$this->MAuth->getdb('fn_jd_calPriceForSale')}(
						'".$data["PRICE"]."',
						'".$data["ORI_DWN"]."',
						(select VATRT from {$this->MAuth->getdb('VATMAST')} where getdate() between FRMDATE and TODATE),
						'".$data["STD_OPT_TOTAL"]."',
						'".$data["INTEREST_RT"]."',
						'".$data["NOPAY"]."',
						'5'
					)
				";
				//echo $sql; exit;
				$q = $this->db->query($sql);
				
				if($q->row()){
					foreach($q->result() as $row_q){
						foreach($row_q  as $key_q => $val_q){
							switch($key_q){
								default:
									$data[$key_q] = $val_q;
									break;
							}
						}
					}
				}
				
				$sql = "
					select a.CUSTYPE,a.CUSCOD,b.SNAM+b.NAME1+' '+b.NAME2+' ('+b.CUSCOD+')-'+b.GRADE as REFNAME 
					from {$this->MAuth->getdb('ARANALYZEREF')} a
					left join {$this->MAuth->getdb('CUSTMAST')} b on a.CUSCOD=b.CUSCOD collate thai_cs_as
					where a.CUSTYPE<>0 and a.ID='".$data["ID"]."'
				";
				$qref = $this->db->query($sql);
				
				if($qref->row()){
					foreach($qref->result() as $row_qref){
						$data['REF'][$row_qref->CUSTYPE]['rank'] = $row_qref->CUSTYPE;
						$data['REF'][$row_qref->CUSTYPE]['cuscod'] = $row_qref->CUSCOD;
						$data['REF'][$row_qref->CUSTYPE]['refname'] = $row_qref->REFNAME;
						$data['REF'][$row_qref->CUSTYPE]['relation'] = 'xxx';
					}
				}
			}
		}
		
		echo json_encode($data);
	}
	
	function UIEdit(){
		$claim = $this->MLogin->getclaim(uri_string());
		if($claim['m_access'] != "T"){ echo "<div align='center' style='color:red;font-size:16pt;width:100%;'>ขออภัย คุณยังไม่มีสิทธิเข้าใช้งานหน้านี้ครับ</div>"; exit; }
		
		$html = "
			<div class='tab1' name='home' locat='{$this->sess['branch']}' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}'>
				<div class='col-sm-12'>
					<div class='row'>
						<div class=' col-sm-2'>	
							<div class='form-group'>
								เลขที่สัญญา
								<input type='text' id='CONTNO' class='form-control input-sm' placeholder='เลขที่สัญญา' >
							</div>
						</div>
						<div class=' col-sm-2'>	
							<div class='form-group'>
								รหัสลูกค้า
								<input type='text' id='CUSCOD' class='form-control input-sm' placeholder='รหัสลูกค้า' >
							</div>
						</div>	
						<div class=' col-sm-2'>	
							<div class='form-group'>
								ชื่อ-สกุล ลูกค้า
								<input type='text' id='CUSNAME' class='form-control input-sm' placeholder='ชื่อ-สกุล' >
							</div>
						</div>	
						<div class=' col-sm-2'>	
							<div class='form-group'>
								สาขา
								<select id='LOCAT' class='form-control input-sm' data-placeholder='สาขา'>
									<option value='".$this->sess["branch"]."'>".$this->sess["branch"]."</option>
								</select>
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
								เลขที่บิลจอง
								<select id='RESVNO' class='form-control input-sm' data-placeholder='เลขที่บิลจอง'></select>
							</div>
						</div>
					</div>
					<div class='row'>
						<div class='col-sm-12'>	
							<div class='form-group'>
								<button id='btnt1search' class='btn btn-primary btn-block'><span class='glyphicon glyphicon-search'> ค้นหา</span></button>
							</div>
						</div>
					</div>
					<div class='row'>	
						<div id='jd_result' class='col-sm-12'></div>
					</div>				
				</div>
			</div>
		";
		
		$html.= "<script src='".base_url('public/js/SYS04/LeasingEdit.js')."'></script>";
		echo $html;
	}
	
	function searchEdit(){
		$html = "";
		$arrs = array();
		$arrs["CONTNO"]  = $_POST["CONTNO"];
		$arrs["CUSCOD"]  = $_POST["CUSCOD"];
		$arrs["CUSNAME"] = $_POST["CUSNAME"];
		$arrs["LOCAT"] 	 = $_POST["LOCAT"];
		$arrs["STRNO"] 	 = $_POST["STRNO"];
		$arrs["RESVNO"]  = $_POST["RESVNO"];
		
		$cond = "";
		$condDesc = "";
		if($arrs["CONTNO"] != ""){
			$condDesc .= " เลขที่สัญญา ".$arrs["CONTNO"];
			$cond .= " and A.CONTNO like '".$arrs["CONTNO"]."'";
		}
		if($arrs["CUSCOD"] != ""){
			$condDesc .= " รหัสลูกค้า ".$arrs["CUSCOD"];
			$cond .= " and C.CUSCOD like '".$arrs["CUSCOD"]."'";
		}
		if($arrs["CUSNAME"] != ""){
			$condDesc .= " ชื่อ-สกุลลูกค้า ".$arrs["CUSNAME"];
			$cond .= " and C.NAME1+' '+C.NAME2 like '".$arrs["CUSNAME"]."'";
		}		
		if($arrs["LOCAT"] != ""){
			$condDesc .= " สาขา ".$arrs["LOCAT"];
			$cond .= " and A.LOCAT like '".$arrs["LOCAT"]."'";
		}
		if($arrs["STRNO"] != ""){
			$condDesc .= " เลขตัวถัง ".$arrs["STRNO"];
			$cond .= " and A.STRNO like '".$arrs["STRNO"]."'";
		}
		if($arrs["RESVNO"] != ""){
			$condDesc .= " เลขที่บิลจอง ".$arrs["RESVNO"];
			$cond .= " and A.RESVNO like '".$arrs["RESVNO"]."'";
		}
		
		
		$sql = "
			SELECT top 1000 A.LOCAT,A.CONTNO,A.CUSCOD,C.SNAM+C.NAME1+' '+C.NAME2 as CUSNAME
				,A.STRNO,A.RESVNO,I.REGNO,I.CURSTAT,A.BILLCOLL 
			FROM {$this->MAuth->getdb('ARMAST')} A
			left join {$this->MAuth->getdb('CUSTMAST')} C on A.CUSCOD=C.CUSCOD AND A.CUSCOD=C.CUSCOD 
			left join {$this->MAuth->getdb('INVTRAN')} I on A.STRNO=I.STRNO   
			WHERE 1=1 ".$cond."
			ORDER BY A.CONTNO 
		";
		//echo $sql;  exit;
		$query = $this->db->query($sql);
				
		if($query->row()){
			foreach($query->result() as $row){
				$html .= "
					<tr>
						<td style='width:40px'>
							<i class='leasingEdit btn btn-xs btn-warning glyphicon glyphicon-edit' contno='".$row->CONTNO."' style='cursor:pointer;'> แก้ไข  </i>
						</td>
						<td style='vertical-align:middle;'>".$row->CONTNO."</td>
						<td style='vertical-align:middle;'>".$row->LOCAT."</td>
						<td style='vertical-align:middle;'>".$row->CUSCOD."</td>
						<td style='vertical-align:middle;'>".$row->CUSNAME."</td>
						<td style='vertical-align:middle;'>".$row->STRNO."</td>
						<td style='vertical-align:middle;'>".$row->RESVNO."</td>
					</tr>
				";
			}
		}
		
		$html = "
			<div id='table-fixed-LE' class='col-sm-12' style='height:calc(100% - 30px);width:100%;overflow:auto;font-size:8pt;'>
				<table id='table-LE' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='100%' border=1>
					<thead style='background: rgba(0, 0, 0, 0) url(&#39;../public/lobiadmin-master/version/1.0/ajax/img/bg/bg6.png&#39;) repeat scroll 0% 0%;'>						
						<tr style='line-height:20px;'>
							<td style='vertical-align:middle;text-align:center;font-size:8pt;' colspan='8'>
								เงื่อนไข :: {$condDesc}
							</td>
						</tr>
						<tr>
							<th style='vertical-align:middle;'>#</th>
							<th style='vertical-align:middle;'>เลขที่สัญญา</th>
							<th style='vertical-align:middle;'>สาขา</th>
							<th style='vertical-align:middle;'>รหัสลูกค้า</th>
							<th style='vertical-align:middle;'>ชื่อ-สกุลลูกค้า</th>
							<th style='vertical-align:middle;'>เลขตัวถัง</th>
							<th style='vertical-align:middle;'>เลขที่ใบจอง</th>
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
	
	function getFormEdit(){
		$contno = $_POST["contno"];
		
		$sql = "
			select a.CONTNO ,a.LOCAT,a.STRNO,a.CUSCOD,b.SNAM+b.NAME1+' '+b.NAME2 as CUSNAME
				,a.RESVNO,convert(varchar(8),a.SDATE,112) as SDATE,a.SMPAY,a.TOTPRC,a.STDPRC
				,a.CONTSTAT
				,(select ff.CONTDESC from {$this->MAuth->getdb('TYPCONT')} ff where ff.CONTTYP=a.CONTSTAT) as CONTDESC
				,a.CHECKER,(select aa.USERNAME from {$this->MAuth->getdb('PASSWRD')} aa where aa.USERID=a.CHECKER collate thai_ci_as) as CHECKERNAME
				,a.ACTICOD
				,(select bb.ACTIDES from {$this->MAuth->getdb('SETACTI')} bb where bb.ACTICOD=a.ACTICOD) as ACTIDES
				,a.BILLCOLL
				,(select cc.USERNAME from {$this->MAuth->getdb('PASSWRD')} cc where cc.USERID=a.BILLCOLL collate thai_ci_as) as BILLCOLLNAME
				,a.PAYTYP as PAYCODE
				,(select dd.PAYDESC from {$this->MAuth->getdb('PAYDUE')} dd where dd.PAYCODE=a.PAYTYP) as PAYDESC				
				,a.SALCOD
				,(select ee.USERNAME from {$this->MAuth->getdb('PASSWRD')} ee where ee.USERID=a.SALCOD collate thai_ci_as) as SALNAME				
				,a.DELYRT,a.DLDAY,a.CALINT,a.CALDSC,a.MEMO1
			from {$this->MAuth->getdb('ARMAST')} a
			left join {$this->MAuth->getdb('CUSTMAST')} b on a.CUSCOD=b.CUSCOD
			where a.CONTNO = '".$contno."'
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$data = array();
		if($query->row()){
			foreach($query->result() as $row){
				foreach($row as $key => $val){
					switch($key){
						case 'SDATE': $data[$key] = $this->Convertdate(2,$val); break;
						case 'SMPAY': $data[$key] = number_format($val,2); break;
						case 'TOTPRC': $data[$key] = number_format($val,2); break;
						case 'STDPRC': $data[$key] = number_format($val,2); break;
						case 'MEMO1':
							$MEMO = explode('[explode]',$val);
							if(sizeof($MEMO) == 2){
								$data["das"] = $MEMO[0]."[explode]";
								$data[$key]  = $MEMO[1];
							}else{
								$data[$key] = $val;
							}
							break;
						default: $data[$key] = $val; break;
					}
				}
			}
		}
		
		$sql = "
			select ARCONT,convert(varchar(8),ARDATE,112) as ARDATE
				,CONTNO,CUSCOD,PAYFOR,PAYAMT,PAYAMT-SMPAY-SMCHQ AS KANG
			from {$this->MAuth->getdb('AROTHR')} 
			where PAYAMT-SMPAY-SMCHQ>0 and CONTNO='".$contno."'
		";
		$query = $this->db->query($sql);
		
		$data["other"] = "";
		if($query->row()){
			foreach($query->result() as $row){
				$data["other"] .= "
					<tr>
						<td>".$row->ARCONT."</td>
						<td>".$this->Convertdate(2,$row->ARDATE)."</td>
						<td>".$row->CONTNO."</td>
						<td>".$row->CUSCOD."</td>
						<td>".$row->PAYFOR."</td>
						<td>".$row->PAYAMT."</td>						
						<td>".$row->KANG."</td>
					</tr>
				";
			}
		}
		
		
		$html = "
			<div id='MAINUIE' class='col-sm-12' style='height:calc(100% - 40px);overflow:auto;'>
				<div class='row' style='background: rgba(0, 0, 0, 0) url(&#39;../public/lobiadmin-master/version/1.0/ajax/img/bg/bg6.png&#39;) repeat scroll 0% 0%;'>
					<div class=' col-sm-2'>	
						<div class='form-group'>
							เลขที่สัญญา
							<input type='text' id='uiesCONTNO' value='".$data["CONTNO"]."' class='form-control input-sm' placeholder='เลขที่สัญญา' readonly>
						</div>
					</div>
					<div class=' col-sm-2'>
						<div class='form-group'>
							สาขา
							<input type='text' id='uiesLOCAT' value='".$data["LOCAT"]."' class='form-control input-sm' placeholder='สาขา' readonly>
						</div>
					</div>
					<div class=' col-sm-2'>
						<div class='form-group'>
							เลขตัวถัง
							<input type='text' id='uiesSTRNO' value='".$data["STRNO"]."' class='form-control input-sm' placeholder='เลขตัวถัง' readonly>
						</div>
					</div>
					<div class=' col-sm-2'>
						<div class='form-group'>
							รหัสลูกค้า
							<input type='text' id='uiesCUSCOD' value='".$data["CUSCOD"]."' class='form-control input-sm' placeholder='รหัสลูกค้า' readonly>
						</div>
					</div>
					<div class=' col-sm-4'>
						<div class='form-group'>
							ชื่อ-สกุล ลูกค้า
							<input type='text' id='uiesCUSNAME' value='".$data["CUSNAME"]."' class='form-control input-sm' placeholder='ชื่อ-สกุล ลูกค้า' readonly>
						</div>
					</div>
					
					<div class='col-sm-2 col-sm-offset-1'>	
						<div class='form-group'>
							เลขที่ใบจอง
							<input type='text' id='uiesRESVNO' value='".$data["RESVNO"]."' class='form-control input-sm' placeholder='เลขที่ใบจอง' readonly>
						</div>
					</div>
					<div class=' col-sm-2'>	
						<div class='form-group'>
							วันที่ทำสัญญา
							<input type='text' id='uiesSDATE' value='".$data["SDATE"]."' class='form-control input-sm' placeholder='วันที่ทำสัญญา' readonly>
						</div>
					</div>
					<div class=' col-sm-2'>	
						<div class='form-group'>
							ชำระเงินแล้ว
							<input type='text' id='uiesSMPAY' value='".$data["SMPAY"]."' class='form-control input-sm' placeholder='ชำระเงินแล้ว' readonly>
						</div>
					</div>
					<div class=' col-sm-2'>	
						<div class='form-group'>
							ราคาขาย
							<input type='text' id='uiesTOTPRC' value='".$data["TOTPRC"]."' class='form-control input-sm' placeholder='ราคาขาย' readonly>
						</div>
					</div>
					<div class=' col-sm-2'>	
						<div class='form-group'>
							ราคาขายหน้าร้าน
							<input type='text' id='uiesSTDPRC' value='".$data["STDPRC"]."' class='form-control input-sm' placeholder='ราคาขายหน้าร้าน' readonly>
						</div>
					</div>
				</div>
				<div class='row col-sm-6'>
					<div class='col-sm-4'>	
						<div class='form-group'>
							สถานะสัญญา
							<select id='uieCONTSTAT' class='form-control input-sm'>
								<option value='".$data["CONTSTAT"]."'>(".$data["CONTSTAT"].") ".$data["CONTDESC"]."</option>
							</select>
						</div>
					</div>
					<div class='col-sm-4'>	
						<div class='form-group'>
							Checker
							<select id='uieCHECKER' class='form-control input-sm'>
								<option value='".$data["CHECKER"]."'>".$data["CHECKERNAME"]." (".$data["CHECKER"].")</option>
							</select>
						</div>
					</div>
					<div class='col-sm-4'>	
						<div class='form-group'>
							กิจกรรมการขาย
							<select id='uieACTICOD' class='form-control input-sm'>
								<option value='".$data["ACTICOD"]."'>(".$data["ACTICOD"].") ".$data["ACTIDES"]."</option>
							</select>
						</div>
					</div>
					
					<div class='col-sm-4'>	
						<div class='form-group'>
							พนักงานเก็บเงิน
							<select id='uieBILLCOLL' class='form-control input-sm'>
								<option value='".$data["BILLCOLL"]."'>".$data["BILLCOLLNAME"]." (".$data["BILLCOLL"].")</option>
							</select>
							
						</div>
					</div>
					<div class='col-sm-4'>	
						<div class='form-group'>
							วิธีชำระค่างวด
							<select id='uiePAYCODE' class='form-control input-sm'>
								<option value='".$data["PAYCODE"]."'>(".$data["PAYCODE"].") ".$data["PAYDESC"]."</option>
							</select>
						</div>
					</div>
					<div class='col-sm-4'>	
						<div class='form-group'>
							พนักงานขาย
							<select id='uieSALCOD' class='form-control input-sm'>
								<option value='".$data["SALCOD"]."'>".$data["SALNAME"]." (".$data["SALCOD"].")</option>
							</select>
						</div>
					</div>
					
					<div class='col-sm-4'>	
						<div class='form-group'>
							อัตราเบี้ยปรับล่าช้า / เดือน
							<input type='text' id='uieDELYRT' value='".$data["DELYRT"]."' class='form-control input-sm' placeholder='อัตราเบี้ยปรับล่าช้า' >
						</div>
					</div>
					<div class='col-sm-4'>	
						<div class='form-group'>
							ชำระล่าช้าได้ไม่เกิน
							<input type='text' id='uieDLDAY' value='".$data["DLDAY"]."' class='form-control input-sm' placeholder='ชำระล่าช้าได้ไม่เกิน' >
						</div>
					</div>
					<div class='col-sm-4'>	
						<div class='form-group'>
							ลูกค้า
							<select id='uieCUSCOD' class='form-control input-sm'>
								<option value='".$data["CUSCOD"]."'>".$data["CUSNAME"]." (".$data["CUSCOD"].")</option>
							</select>
						</div>
					</div>
					
					<div class=' col-sm-6'>	
						วิธีคำนวนเบี้ยปรับ
						<div class='col-sm-12'>
							<label class='radio lobiradio lobiradio-info'>
								<input type='radio' name='uieCALINT' value='1' ".($data["CALINT"] == 1 ? "checked":"")."> 
								<i></i> ตามอัตรา MRR+ค่าคงที่
							</label>
						</div>
						<div class='col-sm-12'>
							<label class='radio lobiradio lobiradio-info'>
								<input type='radio' name='uieCALINT' value='2' ".($data["CALINT"] == 2 ? "checked":"").">
								<i></i> ตามอัตราเบี้ยปรับต่อเดือน
							</label>
						</div>
					</div>
					<div class=' col-sm-6'>	
						วิธีคำนวนส่วนลดตัดสด
						<div class='col-sm-12'>
							<label class='radio lobiradio lobiradio-info'>
								<input type='radio' name='uieCALDSC' value='1' ".($data["CALDSC"] == 1 ? "checked":"")."> 
								<i></i> % ส่วนลดของดอกเบี้ยคงเหลือ(สคบ.)
							</label>									
						</div>
						<div class='col-sm-12'>
							<label class='radio lobiradio lobiradio-info'>
								<input type='radio' name='uieCALDSC' value='2' ".($data["CALDSC"] == 2 ? "checked":"")."> 
								<i></i> % ส่วนลดของดอกเบี้ยทั้งหมด
							</label>
						</div>
						<div class='col-sm-12'>
							<label class='radio lobiradio lobiradio-info'>
								<input type='radio' name='uieCALDSC' value='3' ".($data["CALDSC"] == 3 ? "checked":"").">  
								<i></i> % ส่วนลดต่อเดือน(HP DOS)
							</label>
						</div>
					</div>			
					
					<div class='col-sm-12'>	
						<div class='form-group'>
							หมายเหตุ
							<textarea id='uieMEMO1' das='".$data["das"]."' class='form-control input-sm' rows='3' style='resize:vertical;'>".$data["MEMO1"]."</textarea>
						</div>
					</div>
				</div>
				
				<div class='row col-sm-6' style='font-size:8pt;'>
					<table id='table-aroth' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='100%' border=1>
						<thead style='background: rgba(0, 0, 0, 0) url(&#39;../public/lobiadmin-master/version/1.0/ajax/img/bg/bg6.png&#39;) repeat scroll 0% 0%;'>
							<tr>
								<th colspan='7' class='text-primary'>ลูกหนี้ค้างชำระ ที่จะต้องโอนให้ลูกค้าใหม่</th>
							</tr>
							<tr>
								<th>เลขที่ตั้งหนี้</th>
								<th>วันที่ตั้งหนี้</th>
								<th>เลขที่สัญญา</th>
								<th>รหัสลูกค้า</th>
								<th>ชำระค่า</th>
								<th>จำนวนเงิน</th>
								<th>ค้างชำระ</th>
							</tr>
						</thead>
						<tbody>
							".$data["other"]."
						</tbody>
					</table>
				</div>
			</div>
			<div class='row' style='background: rgba(0, 0, 0, 0) url(&#39;../public/lobiadmin-master/version/1.0/ajax/img/bg/bg4.png&#39;) repeat scroll 0% 0%;'>
				<div class='col-sm-12' style='height:40px;position:fixed;top:calc(100% - 42px);padding-right:20px;'>
					<div class='col-sm-2 col-sm-offset-10'>
						<button id='UIESave' class='btn btn-primary btn-block'><span class='glyphicon glyphicon-floppy-disk'> บันทึก</span></button>
					</div>
				</div>
			</div>
		";
		
		$response = array("html"=>$html);
		echo json_encode($response);
	}
	
	function EditCONTNO(){
		$CONTNO 	= $_POST["CONTNO"];
		$CONTSTAT 	= $_POST["CONTSTAT"];
		$CHECKER 	= $_POST["CHECKER"];
		$ACTICOD 	= $_POST["ACTICOD"];
		$BILLCOLL 	= $_POST["BILLCOLL"];
		$PAYCODE 	= $_POST["PAYCODE"];
		$SALCOD 	= $_POST["SALCOD"];
		
		$DELYRT 	= $_POST["DELYRT"];
		$DLDAY 		= $_POST["DLDAY"];
		$CUSCOD 	= $_POST["CUSCOD"];
		$CALINT 	= $_POST["CALINT"];
		$CALDSC 	= $_POST["CALDSC"];
		$MEMO1	 	= $_POST["DAS"].$_POST["MEMO1"];
		
		$sql = "
			if OBJECT_ID('tempdb..#UIETemp') is not null drop table #UIETemp;
			create table #UIETemp (id varchar(20),contno varchar(20),msg varchar(max));
			
			begin tran UIEleasingTran
			begin try
				if ((select CUSCOD from {$this->MAuth->getdb('ARMAST')} where CONTNO='".$CONTNO."') <> '".$CUSCOD."')
				begin
					update {$this->MAuth->getdb('AROTHR')} 
					set CUSCOD='".$CUSCOD."'
					where PAYAMT-SMPAY-SMCHQ>0 and CONTNO='".$CONTNO."'
				end
				
				if exists(select * from {$this->MAuth->getdb('ARMAST')} where CONTNO='".$CONTNO."')
				begin
					update {$this->MAuth->getdb('ARMAST')} 
					set CONTSTAT='".$CONTSTAT."',
						CHECKER='".$CHECKER."',
						ACTICOD='".$ACTICOD."',
						BILLCOLL='".$BILLCOLL."',
						PAYTYP='".$PAYCODE."',
						SALCOD='".$SALCOD."',
						DELYRT='".$DELYRT."',
						DLDAY='".$DLDAY."',
						CUSCOD='".$CUSCOD."',
						CALINT='".$CALINT."',
						CALDSC='".$CALDSC."',
						MEMO1='".$MEMO1."'
					where CONTNO='".$CONTNO."'
				end 
				
				insert into {$this->MAuth->getdb('hp_UserOperationLog')} (userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
				values ('".$this->sess["IDNo"]."','SYS04::แก้ไขข้อมูลสัญญาเช่าซื้อแล้ว','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');

				insert into #UIETemp select 'S','".$CONTNO."','แก้ไขข้อมูลสัญญาเช่าซื้อ เลขที่สัญญา ".$CONTNO." แล้วครับ';
				commit tran UIEleasingTran;
			end try
			begin catch
				rollback tran UIEleasingTran;
				insert into #UIETemp select 'E','',ERROR_MESSAGE();
			end catch
		";
		//echo $sql; exit;
		
		$this->db->query($sql);
		$sql = "select * from #UIETemp";
		$query = $this->db->query($sql);
	  
		if($query->row()){
			foreach($query->result() as $row){
				$response["error"]  = ($row->id == "E" ?true:false);
				$response["contno"] = $row->contno;
				$response["msg"] = $row->msg;
			}
		}else{
			$response["error"]  = true;
			$response["contno"] = '';
			$response["msg"] = 'ผิดพลาดไม่สามารถบันทึกการขายได้ โปรดติดต่อฝ่ายไอที';
		}
		
		echo json_encode($response);
	}
	
}




















