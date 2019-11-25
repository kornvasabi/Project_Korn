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
class Agent extends MY_Controller {
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
								<button id='btnt1agent' class='btn btn-cyan btn-block'><span class='glyphicon glyphicon-pencil'> ขายส่งเอเย่นต์</span></button>
							</div>
						</div>
						<div class=' col-sm-6'>	
							<div class='form-group'>
								<button id='btnt1search' class='btn btn-primary btn-block'><span class='glyphicon glyphicon-search'> ค้นหา</span></button>
							</div>
						</div>
					</div>		
				</div>
				<div id='result'></div>
			</div>
		";
		
		$html.= "<script src='".base_url('public/js/SYS04/Agent.js')."'></script>";
		echo $html;
	}
	
	function search(){
		$arrs = array();
		$arrs['contno']	= $_POST['contno'];
		$arrs['sdatefrm'] = $this->Convertdate(1,$_POST['sdatefrm']);
		$arrs['sdateto'] = $this->Convertdate(1,$_POST['sdateto']);
		$arrs['locat'] 	= $_POST['locat'];
		$arrs['strno'] 	= $_POST['strno'];
		$arrs['cuscod'] = $_POST['cuscod'];
		
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
			$condDesc .= " วันที่ ".$_POST['sdatefrm']." - ".$_POST['sdateto'];
			$cond .= " and convert(varchar(8),A.SDATE,112) between '".$arrs['sdatefrm']."' and '".$arrs['sdateto']."' ";
		}else if($arrs['sdatefrm'] != "" and $arrs['sdateto'] == ""){
			$condDesc .= " วันที่ ".$_POST['sdatefrm'];
			$cond .= " and convert(varchar(8),A.SDATE,112) = '".$arrs['sdatefrm']."'";
		}else if($arrs['sdatefrm'] == "" and $arrs['sdateto'] != ""){
			$condDesc .= " วันที่ ".$_POST['sdateto'];
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
				,A.CUSCOD,B.SNAM+B.NAME1+' '+B.NAME2 as NAME,'' as STRNO,'' as RESVNO
			FROM {$this->MAuth->getdb('AR_INVOI')} A
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
							<i class='agentDetails btn btn-xs btn-success glyphicon glyphicon-zoom-in' contno='".$row->CONTNO."' style='cursor:pointer;'> รายละเอียด  </i>
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
			<table id='table-agent' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='100%' border=1 style='font-size:8pt;'>
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
		";
		
		$response = array("html"=>$html,"status"=>true);
		echo json_encode($response);
	}
	
	function loadAgent(){
		$contno = $_POST['contno'];
		
		$sql = "
			select a.CONTNO,a.LOCAT,convert(varchar(8),a.SDATE,112) as SDATE 
				,a.APPVNO,a.CUSCOD,b.SNAM+b.NAME1+' '+b.NAME2+' ('+b.CUSCOD+')'+'-'+b.GRADE as CUSNAME
				,a.INCLVAT,a.VATRT,a.ADDRNO,(select '('+ADDRNO+') '+ADDR1+' '+ADDR2+' '+TUMB from {$this->MAuth->getdb('CUSTADDR')} where CUSCOD=a.CUSCOD and ADDRNO=a.ADDRNO) as ADDRDetail
				,a.PAYTYP,'('+c.PAYCODE+') '+c.PAYDESC PAYDESC
				,a.CREDTM,convert(varchar(8),a.DUEDT,112) as DUEDT
				,a.ISSUNO,convert(varchar(8),a.ISSUDT,112) as ISSUDT
				,a.TAXNO,convert(varchar(8),a.TAXDT,112) as TAXDT 
				,a.TKEYIN,a.NKEYIN,a.VKEYIN,a.SMPAY
				,a.COMITN,a.CRDAMT,a.ACTICOD
				,(select '('+ACTICOD+') '+ACTIDES from {$this->MAuth->getdb('SETACTI')} where 1=1 and ACTICOD=a.ACTICOD) as ACTINAME
				,a.SALCOD,(select USERNAME+' ('+USERID+')' from {$this->MAuth->getdb('PASSWRD')} where USERID=a.SALCOD) as SALNAME
				,a.RECOMCOD,(select SNAM+NAME1+' '+NAME2+' ('+CUSCOD+')'+'-'+GRADE from {$this->MAuth->getdb('CUSTMAST')} where CUSCOD=a.RECOMCOD) as RECOMNAME
				,a.MEMO1
			from {$this->MAuth->getdb('AR_INVOI')} a
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
				$response["APPVNO"] 	= $row->APPVNO;
				$response["CUSCOD"] 	= $row->CUSCOD;
				$response["CUSNAME"] 	= $row->CUSNAME;
				$response["INCLVAT"] 	= $row->INCLVAT;
				$response["VATRT"] 		= number_format($row->VATRT,2);
				$response["ADDRNO"] 	= $row->ADDRNO;
				$response["ADDRDetail"] = $row->ADDRDetail;
				$response["PAYTYP"] 	= str_replace(chr(0),'',$row->PAYTYP);
				$response["PAYDESC"] 	= str_replace(chr(0),'',$row->PAYDESC);
				$response["CREDTM"] 	= $row->CREDTM;
				$response["DUEDT"]	 	= $this->Convertdate(2,$row->DUEDT);
				$response["SALCOD"] 	= $row->SALCOD;
				$response["SALNAME"] 	= $row->SALNAME;
				$response["COMITN"] 	= number_format($row->COMITN,2);
				$response["ISSUNO"] 	= $row->ISSUNO;
				$response["ISSUDT"] 	= $this->Convertdate(2,$row->ISSUDT);
				
				$response["TAXNO"] 		= $row->TAXNO;
				$response["TAXDT"] 		= $this->Convertdate(2,$row->TAXDT);
				$response["TKEYIN"] 	= number_format($row->TKEYIN,2);
				$response["NKEYIN"] 	= number_format($row->NKEYIN,2);
				$response["VKEYIN"] 	= number_format($row->VKEYIN,2);
				$response["SMPAY"] 		= number_format($row->SMPAY,2);
				
				$response["CRDAMT"] 		= number_format($row->CRDAMT,2);
				$response["ACTICOD"] 	= str_replace(chr(0),'',$row->ACTICOD);
				$response["ACTINAME"] 	= str_replace(chr(0),'',$row->ACTINAME);
				$response["RECOMCOD"] 	= $row->RECOMCOD;
				$response["RECOMNAME"] 	= $row->RECOMNAME;
				
				
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
			select STRNO,NKEYIN,VKEYIN,TKEYIN,VATRT,MEMO1,ISSUNO
			from {$this->MAuth->getdb('AR_TRANS')}
			where CONTNO='".$contno."' 
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
	
		$strnolist = "";
		if($query->row()){
			foreach($query->result() as $row){
				$strnolist .= "
					<tr seq='old'>
						<td align='center'>
							<i class='strnolist btn btn-xs btn-danger glyphicon glyphicon-minus' disabled
								strno='".$row->STRNO."' nkeyin='".$row->NKEYIN."' vkeyin='".$row->VKEYIN."' 
								tkeyin='".$row->TKEYIN."' vatrt='".$row->VATRT."' memo1='".$row->MEMO1."' 
								issuno='".$row->ISSUNO."'
								style='cursor:pointer;'> ลบ   
							</i>
						</td>
						<td>".$row->STRNO."</td>
						<td class='text-right'>".number_format($row->NKEYIN,2)."</td>
						<td class='text-right'>".number_format($row->VKEYIN,2)."</td>
						<td class='text-right'>".number_format($row->TKEYIN,2)."</td>
						<td class='text-right'>".$row->VATRT."</td>
						<td class='text-right'>".$row->MEMO1."</td>
						<td class='text-right'>".$row->ISSUNO."</td>
					</tr>
				";
			}
		}
		$response["strnolist"] = $strnolist;
				
		echo json_encode($response);
	}	
	
	function getfromAgent(){
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
		
		$sql = "select top 1 PAYCODE,'('+PAYCODE+') '+PAYDESC PAYDESC from {$this->MAuth->getdb('PAYDUE')} order by PAYCODE";
		$query = $this->db->query($sql);
		$row = $query->row();
		$data["PAYCODE"] = str_replace(chr(0),'',$row->PAYCODE);
		$data["PAYDESC"] = str_replace(chr(0),'',$row->PAYDESC);
		
		$sql = "select top 1 ACTICOD,'('+ACTICOD+') '+ACTIDES as ACTIDES from {$this->MAuth->getdb('SETACTI')} where ACTICOD='04' order by ACTICOD";
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
							".$this->getfromAgentTab11($data)."
							".$this->getfromAgentTab22($data)."
							".$this->getfromAgentTab33($data)."							
							
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
					<div class='btn-group btn-group-xs dropup'>
						<button type='button' id='btnDocument' class='btn btn-xs btn-info'>
							เอกสาร
						</button>
						<button type='button' id='btnDocumentOption' class='btn btn-xs btn-info dropdown-toggle' data-toggle='dropdown' aria-expanded='false'>
							<i class='fa fa-cog'></i>
							<span class='sr-only'>Toggle Dropdown</span>
						</button>
						<ul class='dropdown-menu'>
							<span id='btnDOSend' style='text-align:left;' class='btn btn-info btn-xs btn-block text-left'>1.ใบส่งมอบสินค้า</span>
							<span id='btnDOSendTax' style='text-align:left;' class='btn btn-info btn-xs btn-block text-left'>2.ใบส่งของ / ใบกำกับภาษี</span>
							<span id='btnDOPrice' style='text-align:left;' class='btn btn-info btn-xs btn-block text-left'>3.ใบเสร็จรับเงิน</span>
							<span id='btnDOPriceTax' style='text-align:left;' class='btn btn-info btn-xs btn-block text-left'>4.ใบเสร็จรับเงิน / ใบกำกับภาษี</span>
							<span id='btnDOTax' style='text-align:left;' class='btn btn-info btn-xs btn-block text-left'>5.ใบกำกับภาษี</span>
						</ul>
					</div>
				</div>
				<div class='col-sm-6 text-right'>
					<br>
					<button id='add_delete' class='btn btn-xs btn-danger' style='width:100px;'><span class='glyphicon glyphicon-trash'> ลบ</span></button>
					<button id='add_save' class='btn btn-xs btn-primary' style='width:100px;'><span class='glyphicon glyphicon-floppy-disk'> บันทึก</span></button>
				</div>
			</div>
		";
		
		$response = array('html'=>$html,'status'=>true);
		echo json_encode($response);
	}
	
	private function getfromAgentTab11($data){
		$html = "
			<div class='tab-pane active' name='tab11' style='height:calc(100vh - 260px);overflow:auto;'>
				<fieldset style='height:100%'>
					<div class='row' style='height:100%'>
						<div style='float:left;height:100%;overflow:auto;' class='col-sm-8 col-sm-offset-2'>
							<div class='row'>
								<div class='col-sm-4'>	
									<div class='form-group'>
										เลขที่สัญญา
										<input type='text' id='add_contno' class='form-control input-sm' placeholder='เลขที่สัญญา'  value='".@$_POST["xxx"]."'>
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
							
								<div class='col-sm-4'>	
									<div class='form-group'>
										เลขที่ใบอนุมัติ
										<input type='text' id='add_approve' class='form-control input-sm' placeholder='เลขที่ใบอนุมัติ' value='".@$_POST["xxx"]."'>
									</div>
								</div>
								<div class='col-sm-4 col-sm-offset-4'>	
									<div class='form-group'>
										ลูกค้า
										<div class='input-group'>
											<input type='text' id='add_cuscod' CUSCOD='' class='form-control input-sm' placeholder='ลูกค้า' >
											<span class='input-group-btn'>
											<button id='add_cuscod_removed' class='btn btn-danger btn-sm' type='button'>
												<span class='glyphicon glyphicon-remove' aria-hidden='true'></span></button>
											</span>
										</div>
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
								<div class='col-sm-4'>	
									<div class='form-group'>
										อัตราภาษี
										<div class='input-group'>
											<input type='text' id='add_vatrt' class='form-control input-sm' placeholder='อัตราภาษี' value='".$data["vatrt"]."'>
											<span class='input-group-addon'>%</span>
										</div>
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
							
							<div class='row'>	
								<div class='col-sm-4'>	
									<div class='form-group'>
										เครดิต
										<div class='input-group'>
											<input type='text' id='add_credtm' class='form-control input-sm' placeholder='เครดิต' value=''>
											<span class='input-group-addon'>วัน</span>
										</div>	
									</div>
								</div>
								<div class='col-sm-4'>	
									<div class='form-group'>
										วันครบกำหนดชำระ
										<input type='text' id='add_duedt' class='form-control input-sm' placeholder='วันที่ทำสัญญา' data-provide='datepicker' data-date-language='th-th' value='".$this->today('today')."'>
									</div>
								</div>
								<div class='col-sm-4'>	
									<div class='form-group'>
										พนักงานขาย
										<select id='add_salcod' class='form-control input-sm' data-placeholder='พนักงานขาย'></select>
									</div>
								</div>
							</div>
							
							<div class='row'>		
								<div class='col-sm-4'>	
									<div class='form-group'>
										ค่านายหน้าขาย
										<input type='text' id='add_comitn' class='form-control input-sm' placeholder='ค่านายหน้าขาย' value=''>
									</div>
								</div>
								<div class='col-sm-4'>	
									<div class='form-group'>
										เลขที่ใบปล่อยรถ
										<input type='text' id='add_issuno' class='form-control input-sm' placeholder='ค่านายหน้าขาย' value='' maxlength=12>
									</div>
								</div>
								<div class='col-sm-4'>	
									<div class='form-group'>
										วันที่ปล่อยรถ
										<input type='text' id='add_issudt' class='form-control input-sm' placeholder='วันที่ทำสัญญา' data-provide='datepicker' data-date-language='th-th' value='".$this->today('today')."'>
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
	
	private function getfromAgentTab22($data){
		$html = "
			<div class='tab-pane' name='tab22' style='height:calc(100vh - 260px);overflow:auto;'>
				<fieldset style='height:100%'>
					<div class='row' style='height:100%'>
						<div style='float:left;height:100%;overflow:none;' class='col-sm-12'>
							<div class='row' style='width:100%;height:100%;padding-left:30px;background-color:#269da1;'>
								<div class='form-group col-sm-12' style='height:100%;'>
									<span style='color:#efff14;'>บันทึกเลขตัวถังและราคาขาย</span>
									<div id='dataTable-fixed-strno' class='dataTables_wrapper dt-bootstrap4 table-responsive' style='max-height:calc(100% - 130px);height:calc(100% - 130px);overflow:auto;border:1px dotted black;background-color:#eee;'>
										<table id='dataTables-strno' class='table table-bordered dataTable table-hover' stat='' aria-describedby='dataTable_info' style='width:100%;line-height:10px;white-space:nowrap;text-overflow:ellipsis;overflow:hidden;' cellspacing='0'>
											<thead>
												<tr role='row'>
													<th style='width:40px'>
														<i id='add_strno' class='btn btn-xs btn-success glyphicon glyphicon-plus' style='cursor:pointer;'> เพิ่ม  </i>
													</th>
													<th>เลขตัวถัง</th>
													<th>ราคาขาย</th>
													<th>ภาษีมูลค่าเพิ่ม</th>
													<th>ราคาขายรวมภาษี</th>
													<th>อัตราภาษี</th>
													<th>หมายเหตุ</th>
													<th>ใบลดหนี้</th>
												</tr>
											</thead>
											<tbody style='white-space: nowrap;'></tbody>
										</table>
										
									</div>
										<div class='row' style='width:100%;background-color:#269da1;'>
											<div class='col-sm-2'>	
												<div class='form-group'>
													ใบกำกับเงินดาวน์
													<input type='text' id='add_taxno' class='form-control input-sm' placeholder='ใบกำกับเงินดาวน์'  disabled>
												</div>
											</div>
											
											<div class='col-sm-2'>	
												<div class='form-group'>
													วันที่ใบกำกับ
													<input type='text' id='add_taxdt' class='form-control input-sm' placeholder='วันที่ใบกำกับ' data-provide='datepicker' data-date-language='th-th' disabled>
												</div>
											</div>
											<div class='col-sm-2'>	
												<div class='form-group'>
													มูลค่าสินค้า
													<input type='text' id='add_nkeyinall' class='form-control input-sm' placeholder='มูลค่าสินค้า' disabled>
												</div>
											</div>
											<div class='col-sm-2'>	
												<div class='form-group'>
													ภาษีมูลค่าเพิ่ม
													<input type='text' id='add_vkeyinall' class='form-control input-sm' placeholder='ภาษีมูลค่าเพิ่ม' disabled>
												</div>
											</div>
											<div class='col-sm-2'>	
												<div class='form-group'>
													รวมราคาขาย
													<input type='text' id='add_tkeyinall' class='form-control input-sm' placeholder='รวมราคาขาย'  disabled>
												</div>
											</div>
											<div class='col-sm-2'>	
												<div class='form-group'>
													ชำระแล้ว
													<input type='text' id='add_smpay' class='form-control input-sm' placeholder='ชำระแล้ว' disabled>
												</div>
											</div>
											
											<!-- div style='float:left;height:100%;overflow:auto;' class='col-sm-8 col-sm-offset-2'>
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
											</div -->
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
	
	private function getfromAgentTab33($data){
		$html = "
			<div class='tab-pane' name='tab33' style='height:calc(100vh - 260px);overflow:auto;'>
				<fieldset style='height:100%'>
					<div style='float:left;' class='col-sm-8 col-sm-offset-2'>
						<div class='col-sm-4'>	
							<div class='form-group'>
								ยอดเงินตามใบลดหนี้
								<input type='text' id='add_crdamt' class='form-control input-sm' placeholder='ยอดเงินตามใบลดหนี้' >
							</div>
						</div>
						<div class=' col-sm-4'>	
							<div class='form-group'>
								กิจกรรมการขาย
								<select id='add_acticod' class='form-control input-sm' data-placeholder='กิจกรรมการขาย'>
									<option value='".$data["ACTICOD"]."'>".$data["ACTIDES"]."</option>
								</select>
							</div>
						</div>
						<div class=' col-sm-4'>	
							<div class='form-group'>
								ผู้แนะนำการซื้อ
								<div class='input-group'>
									<input type='text' id='add_recomcod' CUSCOD='' class='form-control input-sm' placeholder='ผู้แนะนำการซื้อ' >
									<span class='input-group-btn'>
									<button id='add_recomcod_removed' class='btn btn-danger btn-sm' type='button'>
										<span class='glyphicon glyphicon-remove' aria-hidden='true'></span></button>
									</span>
								</div>
							</div>
						</div>
							
							
						<div class='2 col-sm-12'>	
							<div class='form-group'>
								หมายเหตุ
								<textarea type='text' id='add_memo1' class='form-control input-sm' placeholder='หมายเหตุ' rows=6 style='resize:vertical;' ></textarea>
							</div>
						</div>
					</div>					
				</fieldset>
			</div>
		";
		return $html;
	}
	
	function checkCustomer(){
		$cuscod = $_POST['cuscod'];
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
		
		$response["ARRESV"] = $html.$html;		
		echo json_encode($response);
	}
	
	function save(){
		$arrs = array();
		$arrs["contno"] 	= $_POST["contno"];
		$arrs["locat"] 		= $_POST["locat"];
		$arrs["sdate"] 		= $this->Convertdate(1,$_POST["sdate"]);
		$arrs["approve"] 	= $_POST["approve"];
		$arrs["cuscod"] 	= $_POST["cuscod"];
		$arrs["inclvat"] 	= $_POST["inclvat"];
		$arrs["vatrt"] 		= $_POST["vatrt"];
		$arrs["paydue"] 	= $_POST["paydue"];
		$arrs["credtm"] 	= ($_POST["credtm"] == "" ? 0:str_replace(',','',$_POST["credtm"]));
		$arrs["duedt"] 		= $this->Convertdate(1,$_POST["duedt"]);
		$arrs["salcod"] 	= $_POST["salcod"];
		$arrs["comitn"] 	= ($_POST["comitn"] == "" ? 0:str_replace(',','',$_POST["comitn"]));
		$arrs["issuno"] 	= $_POST["issuno"];
		$arrs["issudt"] 	= $this->Convertdate(1,$_POST["issudt"]);
		$arrs["strnolist"] 	= $_POST["strnolist"];
		$arrs["nkeyinall"] 	= str_replace(',','',$_POST["nkeyinall"]);
		$arrs["tkeyinall"] 	= str_replace(',','',$_POST["tkeyinall"]);
		$arrs["vkeyinall"] 	= str_replace(',','',$_POST["vkeyinall"]);
		$arrs["crdamt"] 	= ($_POST["crdamt"] == "" ? 0:str_replace(',','',$_POST["crdamt"]));
		$arrs["acticod"] 	= $_POST["acticod"];
		$arrs["recomcod"] 	= $_POST["recomcod"];
		$arrs["memo1"] 		= $_POST["memo1"];
		
		$arrs["tsale"] 		= 'A';
		
		$data = "";
		if($arrs["acticod"] 	== ""){ $data = "กิจกรรมการขาย"; }
		if($arrs["strnolist"] 	== "no record"){ $data = "รายการรถ"; }
		if($arrs["paydue"] 		== ""){ $data = "วิธีชำระค่างวด"; }
		if($arrs["duedt"] 		== ""){	$data = "วันครบกำหนดชำระ"; }
		if($arrs["vatrt"] 		== ""){ $data = "อัตราภาษี"; }
		if($arrs["salcod"] 		== ""){ $data = "พนักงานขาย"; }
		if($arrs["cuscod"] 		== ""){ $data = "ลูกค้า"; }
		if($arrs["locat"] 		== ""){ $data = "สาขา"; }
		if($arrs["sdate"] 		== ""){ $data = "วันที่ทำสัญญา"; }
		
		$response = array("error"=>false,"msg"=>"");
		if($data != ""){ 
			$response["error"] = true;
			$response["msg"] = "ไม่พบ{$data} โปรดระบุ{$data}ก่อนครับ";
			echo json_encode($response); exit; 
		}	
			
		$strnosize = sizeof($arrs["strnolist"]);
		$arrs["insertSTRNO"] = "";
		
		$sql = "
			if object_id('tempdb..#tempSTRNOList') is not null drop table #tempSTRNOList;
			select CONTNO ,LOCAT ,STRNO ,VATRT ,KEYIN ,NKEYIN ,VKEYIN ,TKEYIN ,NPRICE ,VATPRC ,TOTPRC 
				,SMPAY ,SMCHQ ,TSALE ,CRDAMT ,USERID ,INPDT ,NCARCST into #tempSTRNOList 
			from {$this->MAuth->getdb('AR_TRANS')} 
			where 1=2
		";
		//echo $sql; 
		$this->db->query($sql);
		
		for($i=0;$i<$strnosize;$i++){
			$arrs["insertSTRNO"] = "
				insert into #tempSTRNOList
				select null,'".$arrs["locat"]."','".$arrs["strnolist"][$i]["strno"]."',".$arrs["strnolist"][$i]["vatrt"].",".$arrs["strnolist"][$i]["tkeyin"].",".$arrs["strnolist"][$i]["nkeyin"].",".$arrs["strnolist"][$i]["vkeyin"].",".$arrs["strnolist"][$i]["tkeyin"].",".$arrs["strnolist"][$i]["nkeyin"].",".$arrs["strnolist"][$i]["vkeyin"].",".$arrs["strnolist"][$i]["tkeyin"].",0,0,'".$arrs["tsale"]."',".$arrs["crdamt"].",'".$this->sess["USERID"]."',getdate(),NULL
			";
			//echo $arrs["insertSTRNO"]; 
			$this->db->query($arrs["insertSTRNO"]);
		}
		//exit;
		
		if($arrs["contno"] == 'Auto Genarate'){
			$this->saveinagent($arrs);
		}else{
			$this->updateinagent($arrs);
		}
	}
	
	private function saveinagent($arrs){
		$sql = "
			if OBJECT_ID('tempdb..#agentTemp') is not null drop table #agentTemp;
			create table #agentTemp (id varchar(20),contno varchar(20),msg varchar(max));
			
			begin tran agentTran
			begin try
				/* @symbol = สัญลักษณ์แทนประเภทของเลขที่ นั้นๆ */
				declare @symbol varchar(10) = (select H_AGENNO from {$this->MAuth->getdb('CONDPAY')});
				/* @rec = รหัสพื้นฐาน */
				declare @rec varchar(10) = (select SHORTL+@symbol+'-'+right(left(convert(varchar(8),GETDATE(),112),6),4) from {$this->MAuth->getdb('INVLOCAT')} where LOCATCD='".$arrs['locat']."');
				/* @RESVNO = รหัสที่จะใช้ */
				declare @CONTNO varchar(12) = isnull((select MAX(CONTNO) from {$this->MAuth->getdb('AR_INVOI')} where CONTNO like ''+@rec+'%' collate thai_cs_as),@rec+'0000');
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
				
				if not exists(select * from #tempSTRNOList)
				begin 
					rollback tran agentTran;
					insert into #agentTemp select 'E' as id,'','บันทึกไม่สำเร็จ ไม่พบเลขตัวถัง <br>โปรดตรอจสอบใหม่อีกครั้ง' as msg;
					return;
				end 
				else 
				begin
					update #tempSTRNOList 
					set CONTNO=@CONTNO
				end
				
				if((
					select count(*) from {$this->MAuth->getdb('INVTRAN')} a
					left join #tempSTRNOList b on a.STRNO=b.STRNO collate thai_cs_as
					where a.FLAG='D' and a.SDATE is null and isnull(a.CONTNO,'')='' and isnull(a.CURSTAT,'')='' and isnull(a.RESVNO,'')='' and b.STRNO is not null
				) <> (select count(*) from #tempSTRNOList))
				begin
					declare @str varchar(max) = '';
					select @str= replace(isnull(@str,'') + QUOTENAME(STRNO),'][','],[') from #tempSTRNOList
					where STRNO not in (
						select a.STRNO from {$this->MAuth->getdb('INVTRAN')} a
						left join #tempSTRNOList b on a.STRNO=b.STRNO collate thai_cs_as
						where a.FLAG='D' and a.SDATE is null and isnull(a.CONTNO,'')='' and isnull(a.CURSTAT,'')='' and isnull(a.RESVNO,'')='' and b.STRNO is not null
					)
					
					rollback tran agentTran;
					insert into #agentTemp select 'E' as id,'','บันทึกไม่สำเร็จ รถบางคัน ไม่สามารถคีย์ขายได้<br>'+@str+'<br>โปรดตรวจสอบข้อมูลใหม่อีกครั้ง' as msg;
					return;
				end 
				else 
				begin
					update a 
					set CONTNO=@CONTNO
						,FLAG='C'
						,SDATE=GETDATE()
						,PRICE=b.TOTPRC
						,TSALE='".$arrs["tsale"]."'
					from {$this->MAuth->getdb('INVTRAN')} a
					left join #tempSTRNOList b on a.STRNO=b.STRNO collate thai_cs_as
					where a.FLAG='D' and a.SDATE is null and isnull(a.CONTNO,'')='' and isnull(a.CURSTAT,'')='' and isnull(a.RESVNO,'')='' and b.STRNO is not null
					
					INSERT INTO {$this->MAuth->getdb('AR_INVOI')} (
						CONTNO ,LOCAT ,CUSCOD ,INCLVAT ,SDATE ,VATRT ,KEYIN ,NKEYIN ,VKEYIN ,TKEYIN 
						,NPRICE ,VATPRC ,TOTPRC ,SMPAY ,SMCHQ ,TAXNO ,TAXDT ,CRDTXNO ,CRDAMT ,CREDTM ,DUEDT 
						,SALCOD ,COMITN ,ISSUNO ,ISSUDT ,LPAYDT ,TSALE ,MEMO1 ,USERID ,INPDT ,DELID 
						,FLCANCL ,APPVNO ,PAYTYP ,ADDRNO ,RECOMCOD ,ACTICOD 
					)  VALUES (
						@CONTNO,'".$arrs["locat"]."','".$arrs["cuscod"]."','".$arrs["inclvat"]."'
						,'".$arrs["sdate"]."',".$arrs["vatrt"].",".$arrs["tkeyinall"].",".$arrs["nkeyinall"].",".$arrs["vkeyinall"]."
						,".$arrs["tkeyinall"].",".$arrs["nkeyinall"].",".$arrs["vkeyinall"].",".$arrs["tkeyinall"].",0,0
						,@TAXNO,@TAXDT,'',".$arrs["crdamt"].",".$arrs["credtm"].",'".$arrs["duedt"]."'
						,'".$arrs["salcod"]."',".$arrs["comitn"].",'".$arrs["issuno"]."','".$arrs["issudt"]."'
						,NULL,'".$arrs["tsale"]."','".$arrs["memo1"]."','".$this->sess["USERID"]."',getdate()
						,'','','".$arrs["approve"]."','".$arrs["paydue"]."'
						,(select top 1 ADDRNO from {$this->MAuth->getdb('CUSTMAST')} where CUSCOD='".$arrs["cuscod"]."')
						,'".$arrs["recomcod"]."','".$arrs["acticod"]."'
					);
					
					insert into {$this->MAuth->getdb('AR_TRANS')} (CONTNO ,LOCAT ,STRNO ,VATRT ,KEYIN ,NKEYIN ,VKEYIN ,TKEYIN ,NPRICE ,VATPRC ,TOTPRC ,SMPAY ,SMCHQ ,TSALE ,CRDAMT ,USERID ,INPDT ,NCARCST )
					select * from #tempSTRNOList
				end
				
				/* TAXTRAN */
				if(@TAXNO is not null)
				BEGIN
					insert into {$this->MAuth->getdb('TAXTRAN')}(
						LOCAT ,TAXNO ,TAXDT ,TSALE ,CONTNO ,CUSCOD ,
						SNAM ,NAME1 ,NAME2 ,VATRT ,NETAMT ,VATAMT ,TOTAMT ,
						DESCP ,FPAY ,LPAY ,INPDT ,TAXTYP ,TAXFLG ,USERID 
					)					
					select '".$arrs["locat"]."',@TAXNO,@TAXDT,'".$arrs["tsale"]."',@CONTNO
						,CUSCOD,SNAM,NAME1,NAME2,".$arrs["vatrt"]."
						,".$arrs["nkeyinall"].",".$arrs["vkeyinall"].",".$arrs["tkeyinall"]."
						,'ใบกำกับขายส่งเอเยนต์',0,0,getdate(),'S','N','".$this->sess["USERID"]."'
					from {$this->MAuth->getdb('CUSTMAST')}
					where CUSCOD='".$arrs["cuscod"]."'
				END;
				
				insert into {$this->MAuth->getdb('hp_UserOperationLog')} (userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
				values ('".$this->sess["IDNo"]."','SYS04::บันทึกขายส่งเอเย่นต์แล้ว',@CONTNO+' ".str_replace("'","",var_export($_POST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');

				insert into #agentTemp select 'S',@CONTNO,'บันทึกขายส่งเอเย่นต์ เลขที่สัญญา '+@CONTNO+' แล้วครับ';
				commit tran agentTran;
			end try
			begin catch
				rollback tran agentTran;
				insert into #agentTemp select 'E','',ERROR_MESSAGE();
			end catch
		";
		//echo $sql; exit;
		$this->db->query($sql);
		$sql = "select * from #agentTemp";
		$query = $this->db->query($sql);
	  
		if($query->row()){
			foreach($query->result() as $row){
				$response["error"] = ($row->id == "S" ? false:true);
				$response["contno"] = $row->contno;
				$response["msg"] = $row->msg;
			}
		}else{
			$response["error"] = true;
			$response["contno"] = '';
			$response["msg"] = 'ผิดพลาดไม่สามารถบันทึกการขายได้ โปรดติดต่อฝ่ายไอที';
		}
		
		echo json_encode($response);
	}
	
	private function updateinagent($arrs){
		if($arrs["contno"] == ''){
			$response["status"] = false;
			$response["msg"] = 'ผิดพลาดไม่สามารถแก้ไขการขายได้ เนื่องจากไม่พบเลขที่สัญญาครับ';
			echo json_encode($response); exit;
		}
		
		$sql = "
			if OBJECT_ID('tempdb..#agentTemp') is not null drop table #agentTemp;
			create table #agentTemp (id varchar(20),contno varchar(20),msg varchar(max));
			
			begin tran agentTran
			begin try
				declare @CONTNO varchar(20) = '".$arrs["contno"]."';
				
				update {$this->MAuth->getdb('AR_INVOI')}
				set ISSUNO='".$arrs["issuno"]."'
					,ISSUDT='".$arrs["issudt"]."'
					,ACTICOD='".$arrs["acticod"]."'
					,RECOMCOD='".$arrs["recomcod"]."'
					,MEMO1='".$arrs["memo1"]."'
				where CONTNO=@CONTNO
								
				insert into {$this->MAuth->getdb('hp_UserOperationLog')} (userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
				values ('".$this->sess["IDNo"]."','SYS04::บันทึกขายส่งเอเย่นต์ (แก้ไข)',' ".str_replace("'","",var_export($_POST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');

				insert into #agentTemp select 'S',@CONTNO,'บันทึกรายการขายสำเร็จ เลขที่สัญญา '+@CONTNO;
				commit tran agentTran;
			end try
			begin catch
				rollback tran agentTran;
				insert into #agentTemp select 'E','',ERROR_MESSAGE();
			end catch
		";
		
		$this->db->query($sql);
		$sql = "select * from #agentTemp";
		$query = $this->db->query($sql);
	  
		if($query->row()){
			foreach($query->result() as $row){
				$response["error"] = ($row->id == "S" ? false:true);
				$response["contno"] = $row->contno;
				$response["msg"] = $row->msg;
			}
		}else{
			$response["status"] = true;
			$response["contno"] = '';
			$response["msg"] = 'ผิดพลาดไม่สามารถบันทึกการขายได้ โปรดติดต่อฝ่ายไอที';
		}
		
		echo json_encode($response);
	}
	
	function deleteContno(){
		$contno = $_POST['contno'];
		
		$sql = "
			if OBJECT_ID('tempdb..#agentTemp') is not null drop table #agentTemp;
			create table #agentTemp (id varchar(20),contno varchar(20),msg varchar(max));
			
			begin tran agentTran
			begin try
				declare @CONTNO varchar(20) = '".$contno."';

				if exists(select * from {$this->MAuth->getdb('AR_INVOI')} where CONTNO=@CONTNO and CRDAMT>0)
				begin
					rollback tran agentTran;
					insert into #agentTemp select 'E' as id,'','ผิดพลาด มีการออกใบลดหนี้แล้ว' as msg;
					return;
				end
				
				if exists(
					select * from {$this->MAuth->getdb('CHQTRAN')} 
					where PAYFOR='009' and CONTNO='๙SH-17010008' and FLAG<>'C' and CANDT is null
				)
				begin
					rollback tran agentTran;
					insert into #agentTemp select 'E' as id,'','ผิดพลาด มีการรับชำระแล้วครับ' as msg;
					return;
				end
				
				INSERT INTO {$this->MAuth->getdb('CANINVO')}  (
					CONTNO,LOCAT,CUSCOD,INCLVAT,SDATE,VATRT,DSCPRC,KEYIN,TOTPRC,SMPAY,SMCHQ
					,TAXNO,TAXDT,SALCOD,TSALE,USERID,INPDT,DELID,DELDT,POSTDT
				)
				select CONTNO,LOCAT,CUSCOD,INCLVAT,SDATE,VATRT,CRDAMT as DSCPRC,KEYIN,TOTPRC,SMPAY,SMCHQ
					,TAXNO,TAXDT,SALCOD,TSALE,USERID,INPDT,DELID,DELDT,POSTDT 
				from {$this->MAuth->getdb('AR_INVOI')} 
				where CONTNO=@CONTNO
				
				update {$this->MAuth->getdb('INVTRAN')} 
				set SDATE=null,PRICE=null,CONTNO=null,FLAG='D',TSALE=null
				where CONTNO=@CONTNO
				
				delete from {$this->MAuth->getdb('AR_INVOI')} where CONTNO=@CONTNO
				delete from {$this->MAuth->getdb('AR_TRANS')} where CONTNO=@CONTNO
				
				/*
				delete from {$this->MAuth->getdb('TAXTRAN')}  
				where TAXNO=(select TAXNO from {$this->MAuth->getdb('ARMAST')} where CONTNO=@CONTNO)
				*/
				update {$this->MAuth->getdb('TAXTRAN')}  
				set FLAG='C'
					,CANDT=getdate()
					,FLCANCL='".$this->sess["USERID"]."'
				where TAXNO=(select TAXNO from {$this->MAuth->getdb('AR_INVOI')} where CONTNO=@CONTNO)
				
				insert into {$this->MAuth->getdb('hp_UserOperationLog')} (userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
				values ('".$this->sess["IDNo"]."','SYS04::ลบการขายส่งเอเย่นต์',' ".str_replace("'","",var_export($_POST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');

				insert into #agentTemp select 'S',@CONTNO,'ลบรายการขาย เลขที่สัญญา '+@CONTNO+' แล้ว';
				commit tran agentTran;
			end try
			begin catch
				rollback tran agentTran;
				insert into #agentTemp select 'E','',ERROR_MESSAGE();
			end catch
		";
		//echo $sql; exit;
		$this->db->query($sql);
		$sql = "select * from #agentTemp";
		$query = $this->db->query($sql);
	  
		if($query->row()){
			foreach($query->result() as $row){
				$response["error"] = ($row->id == "S" ? false:true);
				$response["contno"] = $row->contno;
				$response["msg"] = $row->msg;
			}
		}else{
			$response["status"] = true;
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
				,a.DSCPRC
				,a.TOTDWN
				,a.STDPRC-a.TOTDWN as DTPD
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
				
			from {$this->MAuth->getdb('ARMAST')} a
			left join {$this->MAuth->getdb('INVTRAN')} b on a.STRNO=b.STRNO
			where a.CONTNO='".$_POST['contno']."'
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
				
				$data[14] = number_format(0,2);
				$data[16] = number_format(0,2);
				$data[18] = number_format($row->STDPRC,2);
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
			where CONTNO='".$_POST['contno']."'
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
			where CONTNO='".$_POST['contno']."'
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
	
	function sendpdf(){
		$data = array();
		$contno = $_GET['contno'];
		$document = $_GET['document'];
		
		$sql = "
			select COMP_NM,COMP_ADR1+' '+COMP_ADR2+' โทร '+TELP as COMADDR,TAXID 
			from {$this->MAuth->getdb('CONDPAY')}
		";
		$query = $this->db->query($sql);
		
		if($query->row()){
			foreach($query->result() as $row){
				$data["company"] = $row->COMP_NM;
				$data["comaddr"] = $row->COMADDR;
				$data["comtaxid"] = $row->TAXID;
			}
		}
		
		
		$sql = "
			SELECT A.CONTNO AS เลขที่สัญญา, A.LOCAT AS รหัสสาขา, A.CUSCOD AS รหัสลูกค้า, '{$document}' AS ประเภทเอกสาร
				,(CASE WHEN A.TSALE='A' THEN 'ขายส่งเอเย่นซ์' 
					WHEN A.TSALE='O' THEN 'ขายอุปกรณ์'    
					WHEN A.TSALE='F' THEN 'ขายส่งไฟแนนช์' 
					WHEN A.TSALE='C' THEN 'ขายสด / เครดิต'   
					WHEN A.TSALE='H' THEN 'ข่ายผ่อนเช่าซื้อ' ELSE '' END) AS ประเภทการขาย
				,convert(char(8),A.SDATE,112) AS วันที่ใบส่งสินค้า,'' AS เลขที่ใบส่งสินค้า, A.NPRICE AS ราคาสุทธิ
				,A.VATPRC AS ภาษีมูลค่าเพิ่ม, A.TOTPRC AS ราคารวมภาษี,{$this->MAuth->getdb('FN_JD_CONVERT_NUM2BATH')}(A.TOTPRC) as ราคารวมภาษีอักษร
				,A.SALCOD AS รหัสพนักงานขาย
				,A.VATRT AS อัตราภาษี, A.TAXNO AS เลขที่ใบกำกับภาษี, A.TAXDT AS วันที่ใบกำกับภาษี
				,A.FINCOD AS รหัสบริษัทไฟแนนช์
				,CAST(('ข้าพเจ้าได้ตรวจสอบดูแล้ว เห็นว่ารถคันนี้พร้อมด้วยเครื่องยนต์และอุปกรณ์ต่างๆอยู่ในสภาพเรียบร้อยทุกประการ ในกรณีที่รถคันนี้เกิดเสียหายด้วยเหตุใดๆก็ตามภายหลังจากการมอบรถไปแล้วข้าพเจ้าขอรับผิดชอบทั้งสิ้น เพื่อเป็นหลักฐานในการนี้ ข้าพเจ้าจึงลงนามไว้เป็นหลักฐาน') AS VARCHAR(1024)) AS หมายเหตุหน้าเรียก
				,CAST((A.MEMO1) AS VARCHAR(1024)) AS หมายเหตุหน้าขาย
				,A.TYPE AS ยี่ห้อหรือรายการ,A.MODEL AS รุ่นรถ,A.COLOR AS สีรถ,  A.STRNO AS เลขตัวถังรถ
				,A.ENGNO AS เลขเครื่องรถ,A.OPTCODE AS รหัสอุปกรณ์,A.QTY AS จำนวน,   A.T_UPRICE AS ราคาต่อหน่วย
				,A.T_TOTPRC AS รวมสุทธิ,  B.LOCATNM AS ชื่อสาขา,B.LOCADDR1 AS ที่อยู่1_สาขา,B.LOCADDR2 AS ที่อยู่2_สาขา
				,H.CUSNAME AS ชื่อลู้กค้า,H.CUSADDR1 AS ที่อยู่1_ลูกค้า,H.CUSADDR2 AS ที่อยู่2_ลูกค้า,  H.CUSTEL AS เบอร์โทรศัพท์_ลูกค้า 
			FROM (          
				SELECT A.CONTNO, A.LOCAT, A.CUSCOD, A.ADDRNO, A.TSALE
					,A.SDATE, A.NPRICE, A.VATPRC, A.TOTPRC, A.SALCOD
					,A.VATRT, A.TAXNO, A.TAXDT, '' as FINCOD, A.MEMO1
					,B.TYPE,B.MODEL,B.COLOR, B.STRNO,B.ENGNO,'' AS OPTCODE
					,1 AS QTY,A.TOTPRC AS T_UPRICE ,A.TOTPRC AS T_TOTPRC          
				FROM {$this->MAuth->getdb('ARMAST')} A
				LEFT OUTER JOIN {$this->MAuth->getdb('INVTRAN')} B ON  A.CONTNO=B.CONTNO                  
				
				UNION ALL          
				SELECT A.CONTNO, A.LOCAT,A.CUSCOD, A.ADDRNO, A.TSALE
					,A.SDATE, A.NPRICE, A.VATPRC, A.TOTPRC, A.SALCOD
					,A.VATRT, A.TAXNO, A.TAXDT, '' as FINCOD, A.MEMO1
					,B.TYPE,B.MODEL,B.COLOR, B.STRNO,B.ENGNO,'' AS OPTCODE,1 AS QTY
					,A.TOTPRC AS T_UPRICE ,A.TOTPRC AS T_TOTPRC          
				FROM {$this->MAuth->getdb('ARCRED')} A          
				LEFT OUTER JOIN {$this->MAuth->getdb('INVTRAN')} B ON  A.CONTNO=B.CONTNO                   
				
				UNION ALL         
				SELECT A.CONTNO, A.LOCAT, A.CUSCOD, A.ADDRNO,A.TSALE
					,A.SDATE, A.NPRICE, A.VATPRC, A.TOTPRC, A.SALCOD
					,A.VATRT, A.TAXNO, A.TAXDT, '' as FINCOD, A.MEMO1
					,C.TYPE,C.MODEL,C.COLOR, B.STRNO,C.ENGNO,'' AS OPTCODE,1 AS QTY
					,(B.TOTPRC) AS T_UPRICE ,(B.TOTPRC) AS T_TOTPRC          
				FROM {$this->MAuth->getdb('AR_INVOI')}  A          
				LEFT OUTER JOIN   (                                
					SELECT CONTNO,STRNO,NPRICE,VATPRC,TOTPRC FROM {$this->MAuth->getdb('AR_TRANS')}
				) AS B ON  B.CONTNO=A.CONTNO           
				LEFT OUTER JOIN {$this->MAuth->getdb('INVTRAN')} C ON  B.STRNO=C.STRNO                   
				
				UNION ALL           
				SELECT A.CONTNO, A.LOCAT, A.CUSCOD, A.ADDRNO, A.TSALE
					,A.SDATE, A.NPRICE, A.VATPRC,A.TOTPRC, A.SALCOD
					,A.VATRT, A.TAXNO, A.TAXDT, A.FINCOD, A.MEMO1
					,B.TYPE,B.MODEL,B.COLOR, B.STRNO,B.ENGNO,'' AS OPTCODE,1 AS QTY
					,A.TOTPRC AS T_UPRICE ,A.TOTPRC AS T_TOTPRC         
				FROM {$this->MAuth->getdb('ARFINC')} A          
				LEFT OUTER JOIN {$this->MAuth->getdb('INVTRAN')} B ON  A.CONTNO=B.CONTNO                  
				
				UNION ALL          
				SELECT A.CONTNO, A.LOCAT, A.CUSCOD, '1' as ADDRNO, A.TSALE
					,A.SDATE, A.OPTPRC as NPRICE, A.OPTPVT as VATPRC
					,A.OPTPTOT as TOTPRC, A.SALCOD
					,A.VATRT, A.TAXNO, A.TAXDT, '' as FINCOD, A.MEMO1
					,(B.OPTCODE)+': '+RTRIM(C.OPTNAME) AS TYPE,'' AS MODEL,'' AS COLOR
					,'' AS STRNO,'' AS ENGNO,B.OPTCODE,B.QTY,B.UPRICE AS T_UPRICE,B.TOTPRC AS T_TOTPRC         
				FROM {$this->MAuth->getdb('AROPTMST')} A          
				LEFT OUTER JOIN {$this->MAuth->getdb('ARINOPT')} B ON A.CONTNO=B.CONTNO           
				LEFT OUTER JOIN {$this->MAuth->getdb('OPTMAST')} C ON (B.optcode=C.optcode) and (B.locat=C.locat)        
			) as A     
			LEFT OUTER JOIN {$this->MAuth->getdb('INVLOCAT')}  B on B.LOCATCD = A.LOCAT   
			LEFT OUTER JOIN ( 
				SELECT  E.CUSCOD,E.NAME AS CUSNAME, RTRIM(E.บ้านเลขที่) + ' ' +   RTRIM(E.หมู่บ้าน) + ' ' +RTRIM(E.ซอย) + ' ' +  RTRIM(E.ถนน) AS CUSADDR1  
					,RTRIM(E.TUMB) + ' ' +RTRIM(E.AUMP)+ ' ' +RTRIM(E.PROV)+ ' ' +    RTRIM(CAST((E.ZIP) AS CHAR(8))) AS CUSADDR2  ,E.TELP AS CUSTEL    
				FROM (   
					SELECT D.*   ,CASE WHEN (substring(D.PROVDES,1,7)='กรุงเทพฯ' OR substring(D.PROVDES,1,3)='กทม.') AND TUMBDES<>''   
							THEN 'แขวง' + D.TUMBDES WHEN D.TUMBDES <>' ' THEN 'ต.' + D.TUMBDES ELSE '' END AS TUMB   
						,CASE WHEN (substring(D.PROVDES,1,7)='กรุงเทพฯ' OR substring(D.PROVDES,1,3)='กทม.') AND AUMPDES<>''   
							THEN 'เขต' + D.AUMPDES WHEN D.AUMPDES <>' ' THEN 'อ.' + D.AUMPDES ELSE '' END AS AUMP   
						,CASE WHEN (substring(D.PROVDES,1,7)='กรุุงเทพฯ' OR substring(D.PROVDES,1,3)='กทม.') AND PROVDES<>''   
							THEN D.PROVDES WHEN D.PROVDES<>' ' THEN 'จ.' + D.PROVDES ELSE '' END AS PROV    
					FROM (   
						SELECT c.CUSCOD,c.MOBILENO   ,RTRIM(c.SNAM) +''+ RTRIM(c.NAME1) +' '+ RTRIM(c.NAME2) AS name ,B.ADDR1 AS บ้านเลขที่   
							,CASE WHEN B.ADDR2<>' ' THEN 'ถ.' + B.ADDR2 ELSE ' ' END AS ถนน   
							,CASE WHEN B.MOOBAN<>' ' THEN 'หมู่บ้าน' + B.MOOBAN ELSE ' ' END AS หมู่บ้าน   
							,CASE WHEN B.SOI<>' ' THEN ' ซ.' + B.SOI ELSE ' ' END AS ซอย   
							,B.TUMB AS TUMBDES,B.AUMPCOD,B.PROVCOD,B.ZIP,B.TELP    
							,(select aumpdes from {$this->MAuth->getdb('SETAUMP')} where aumpcod = b.aumpcod) as aumpdes  
							,(select provdes from {$this->MAuth->getdb('SETPROV')} where provcod = b.provcod) as provdes  
						FROM {$this->MAuth->getdb('CUSTMAST')} c  
						LEFT OUTER JOIN {$this->MAuth->getdb('CUSTADDR')} b  ON c.CUSCOD=B.CUSCOD AND c.ADDRNO=B.ADDRNO  
					) AS D  
				) AS E  
			) AS H ON  A.CUSCOD=H.CUSCOD   
			WHERE (A.CONTNO = '".$contno."') --AND (A.TSALE='A')
		";
		//echo md5(base64_encode($sql)); exit;
		$query = $this->db->query($sql);
		
		$top = 305;
		$i = 1;
		$data["car"] = array();
		$data["carsize"] =  1;
		if($query->row()){
			foreach($query->result() as $row){
				$data["locatnm"] = $row->ชื่อสาขา;
				$data["document"] = $row->ประเภทเอกสาร;
				$data["contno"] = $row->เลขที่สัญญา;
				$data["sendno"] = $row->เลขที่ใบส่งสินค้า;
				$data["cusname"] = $row->ชื่อลู้กค้า;
				$data["senddt"] = $this->Convertdate(2,$row->วันที่ใบส่งสินค้า);
				$data["cusaddr1"] = $row->ที่อยู่1_ลูกค้า;
				$data["cusaddr2"] = $row->ที่อยู่2_ลูกค้า;
				$data["taxno"] = $row->เลขที่ใบกำกับภาษี;
				$data["comment"] = $row->หมายเหตุหน้าเรียก;
				
				if(($i % 21) == 0){ 
					// ครบ 20 คัน ให้ขึ้นหน้าใหม่
					$top = 305;
					$data["carsize"] += 1; 
				}
				if(!isset($data["car"][$data["carsize"]])){ $data["car"][$data["carsize"]] = "";  }
				
				$top += 25; 
				$data["car"][$data["carsize"]] .= "
					<div class='wf pf data' style='top:{$top};left:0;width:30px;'>{$i}</div>
					<div class='wf pf data' style='top:{$top};left:30;width:100px;'>{$row->ยี่ห้อหรือรายการ}</div>
					<div class='wf pf data' style='top:{$top};left:130;width:100px;'>{$row->รุ่นรถ}</div>
					<div class='wf pf data' style='top:{$top};left:230;width:100px;'>{$row->สีรถ}</div>
					<div class='wf pf data' style='top:{$top};left:330;width:150px;'>{$row->เลขตัวถังรถ}</div>
					<div class='wf pf tc data' style='top:{$top};left:480;width:50px;'>{$row->จำนวน}</div>
					<div class='wf pf tr data' style='top:{$top};left:540;width:90px;'>".number_format($row->ราคาต่อหน่วย,2)."</div>
					<div class='wf pf tr data' style='top:{$top};left:635;width:80px;'>".number_format($row->รวมสุทธิ,2)."</div>
				";
				$i++;
				
				$data["vatrt"] = number_format($row->อัตราภาษี,2);				
				$data["total"] = number_format($row->ราคาสุทธิ,2);
				$data["totalvat"] = number_format($row->ภาษีมูลค่าเพิ่ม,2);
				$data["totalprice"] = number_format($row->ราคารวมภาษี,2);
				$data["totalpriceTH"] = $row->ราคารวมภาษีอักษร;
			}
			
			$sql = "
				select a.optcode as optcode,a.qty as qty,a.uprice as uprice
					,a.nprice as nprice, a.totvat as totvat,a.totprc as totprc
					,b.optname as optname  
				from {$this->MAuth->getdb('ARINOPT')} a
				left join {$this->MAuth->getdb('OPTMAST')} b on a.optcode=b.optcode and a.locat=B.locat
				where a.contno='".$data["contno"]."' 
			";
			$query = $this->db->query($sql);
			
			if($query->row()){
				foreach($query->result() as $row){
					if(($i % 21) == 0){ 
						// ครบ 20 คัน ให้ขึ้นหน้าใหม่
						$top = 305;
						$data["carsize"] += 1; 
					}
					if(!isset($data["car"][$data["carsize"]])){ $data["car"][$data["carsize"]] = "";  }
					
					$top += 25; 
					$data["car"][$data["carsize"]] .= "
						<div class='wf pf data' style='top:{$top};left:0;width:30px;'>{$i}</div>
						<div class='wf pf data' colspan='4' style='top:{$top};left:30;width:300px;'>{$row->optname}</div>
						<div class='wf pf tc data' style='top:{$top};left:480;width:50px;'>{$row->qty}</div>
						<div class='wf pf tr data' style='top:{$top};left:540;width:90px;'>".number_format($row->nprice,2)."</div>
						<div class='wf pf tr data' style='top:{$top};left:635;width:80px;'>".number_format($row->totprc,2)."</div>
					";
					$i++;
				}
			}
		}
		
		$this->sendpdfForm($data);
	}
	
	function sendpdfForm($data){
		$mpdf = new \Mpdf\Mpdf([
			'mode' => 'utf-8', 
			'format' => 'A4',
			'margin_top' => 0, 	//default = 16
			'margin_left' => 10, 	//default = 15
			'margin_right' => 10, 	//default = 15
			'margin_bottom' => 16, 	//default = 16
			'margin_header' => 9, 	//default = 9
			'margin_footer' => 9, 	//default = 9
		]);
		
		for($i=1;$i<=$data["carsize"];$i++){
			if($i != 1){ $mpdf->AddPage(); }
			$content = "
				<div class='wf pf tc' style='top:40;left:0;font-size:12pt;'><b>{$data["company"]}</b></div>
				<div class='wf pf tc data' style='top:70;left:0;'>{$data["comaddr"]}</div>
				<div class='wf pf tc data' style='top:95;left:0;'>เลขที่ประจำตัวผู้เสียภาษี {$data["comtaxid"]}</div>
				
				<div class='wf pf data' style='top:140;left:0;'><b>สาขาที่ออกใบกำกับภาษี</b> {$data["locatnm"]}</div>
				<div class='wf pf data' style='top:140;left:490;'><b>{$data["document"]}</b></div>
				
				<div class='wf pf data' style='top:165;left:0;'><b>เลขที่สัญญา</b> {$data["contno"]}</div>
				<div class='wf pf data' style='top:165;left:490;'><b>เลขที่ใบส่งสินค้า</b> {$data["sendno"]}</div>
				
				<div class='wf pf data' style='top:190;left:0;'><b>ชื่อ - สกุล</b> {$data["cusname"]}</div>
				<div class='wf pf data' style='top:190;left:490;'><b>วันที่ส่งสินค้า</b> {$data["senddt"]}</div>
				
				<div class='wf pf data' style='top:215;left:0;'><b>ที่อยู่</b> {$data["cusaddr1"]}</div>
				<div class='wf pf data' style='top:240;left:30;'>{$data["cusaddr2"]}</div>
				<div class='wf pf data' style='top:215;left:490;'><b>เลขที่ใบกำกับภาษี</b> {$data["taxno"]}</div>
				
				<div class='wf pf data' style='top:265;left:0;'><hr size='20'></div>
				<div class='wf pf data' style='top:315;left:0;'><hr size='20'></div>
				
				<div class='wf pf data' style='top:295;left:0;'><b>No.</b></div>
				<div class='wf pf data' style='top:295;left:490;'><b>จำนวน</b></div>
				<div class='wf pf data' style='top:295;left:560;'><b>ราคา/หน่วย</b></div>
				<div class='wf pf data' style='top:295;left:650;'><b>จำนวนเงิน</b></div>
				<div class='wf pf tc data' style='top:290;left:30;width:450px;'><b>-----------------------------------------------------------------------------</b></div>
				<div class='wf pf tc data' style='top:280;left:30;width:450px;'><b>รายการ</b></div>
				<div class='wf pf tc data' style='top:305;left:30;width:100px;'><b>ยี่ห้อ</b></div>
				<div class='wf pf tc data' style='top:305;left:130;width:100px;'><b>รุ่น</b></div>
				<div class='wf pf tc data' style='top:305;left:230;width:100px;'><b>สี</b></div>
				<div class='wf pf tc data' style='top:305;left:330;width:150px;'><b>เลขตัวถัง</b></div>
				
				{$data["car"][$i]}
				
				<div class='wf pf data' style='top:860;left:0;height:53px;'>
					{$data["comment"]}
				</div>
				
				<div class='wf pf data' style='top:915;left:0;'><hr size='20'></div>
				<div class='wf pf data' style='top:940;left:490;'><b>ราคารวมสุทธิ</b></div>
				<div class='wf pf tr data' style='top:940;left:590;width:120px;'>{$data["total"]}</div>
				<div class='wf pf data' style='top:965;left:490;'><b>ภาษีมูลค่าเพิ่ม {$data["vatrt"]} %</b></div>
				<div class='wf pf tr data' style='top:965;left:620;width:90px;'>{$data["totalvat"]}</div>
				
				<div class='wf pf data' style='top:990;left:0;'><b>รวมเงิน ({$data["totalpriceTH"]})</b></div>
				<div class='wf pf data' style='top:990;left:490;'><b>ราคารวมภาษี</b></div>
				<div class='wf pf tr data' style='top:990;left:590;width:120px;'>{$data["totalprice"]}</div>
				
				<div class='wf pf data' style='top:1040;left:80;'>.....................................................</div>
				<div class='wf pf data' style='top:1040;left:400;'>.....................................................</div>
				<div class='wf pf tc data' style='top:1065;left:80;width:180px;'><b>ผู้รับสินค้า</b></div>
				<div class='wf pf tc data' style='top:1065;left:400;width:180px;'><b>ผู้รับมอบอำนาจ</b></div>
			";
			
			$stylesheet = "
				<style>
					body { font-family: garuda;font-size:10pt; }
					//.wf { width:100%;border:1px solid red; }
					.wf { width:100%; }
					.h10 { height:10px; }
					.tc { text-align:center; }
					.tr { text-align:right; }
					.pf { position:fixed; }
					.bor { border:0.1px solid black; }
					.bor2 { border:0.1px dotted black; }
					.data { background-color:#fff;font-size:9pt; }
				</style>
			";
			$mpdf->WriteHTML($content.$stylesheet);
			$mpdf->SetHTMLFooter("<div class='wf pf' style='top:1060;left:0;font-size:6pt;width:720px;text-align:right;'>{$this->sess["name"]} ออกเอกสาร ณ วันที่ ".date('d/m/').(date('Y')+543)." ".date('H:i')."</div>");
			$mpdf->fontdata['qanela'] = array('R' => "QanelasSoft-Regular.ttf",'B' => "QanelasSoft-Bold.ttf",); //แก้ปริ้นแล้วอ่านไม่ออก
		}
		
		$mpdf->Output();
	}
	
	
	function getFormSTRNO(){
		$html =  "
			<div class='row' id='gfmain'>
				<div class='col-sm-12'>
					<div class='form-group'>
						เลขตัวถัง
						<select id='gf_strno' class='form-control input-xs' data-placeholder='เลขตัวถัง'></select>
					</div>
				</div>
				<div class='col-sm-6'>
					<div class='form-group'>
						ราคาขาย
						<input type='text' id='gf_nkeyin' class='form-control input-xs' ".($_POST["inclvat"] == "Y" ? "disabled":"").">
					</div>
				</div>
				<div class='col-sm-6'>
					<div class='form-group'>
						ภาษีมูลค่าเพิ่ม
						<input type='text' id='gf_vkeyin' class='form-control input-xs' disabled>
					</div>
				</div>
				<div class='col-sm-6'>
					<div class='form-group'>
						ราคาขายรวมภาษี
						<input type='text' id='gf_tkeyin' class='form-control input-xs' ".($_POST["inclvat"] == "Y" ? "":"disabled").">
					</div>
				</div>
				<div class='col-sm-6'>
					<div class='form-group'>
						อัตราภาษี
						<div class='input-group'>
							<input type='text' id='gf_vatrt' class='form-control input-xs' inclvat='".$_POST["inclvat"]."' placeholder='อัตราภาษี' value='{$_POST["vatrt"]}' readonly>
							<span class='input-xs input-group-addon'>%</span>
						</div>
					</div>
				</div>
				<div class='col-sm-12'>
					<div class='form-group'>
						หมายเหตุ
						<textarea type='text' id='gf_memo1' class='form-control input-sm' placeholder='หมายเหตุ' rows=4 style='resize:vertical;' ></textarea>
					</div>
				</div>
				<div class='col-sm-12'>
					<div class='form-group'>
						ใบลดหนี้
						<input type='text' id='gf_issuno' class='form-control input-xs' maxlength='12'>
					</div>
				</div>
				
				<div class='col-sm-12'>
					<button id='btngf_receipt' class='btn-xs btn-primary btn-block'>
						<span class='glyphicon glyphicon-ok'> รับค่า</span>
					</button>
				</div>
			</div>
		";
		
		$response = array("html"=>$html);
		echo json_encode($response);
	}
	
	function fnCalPrice(){
		$nkeyin   = str_replace(",","",$_POST["nkeyin"]);
		$tkeyin  = str_replace(",","",$_POST["tkeyin"]);
		$inclvat = $_POST["inclvat"];
		$vatrt   = $_POST["vatrt"];
		
		$data = array();
		if($inclvat == "Y"){
			$data['nkeyin']  = ($tkeyin / (($vatrt+ 100) / 100));
			$data['vatin']  = ($tkeyin - $data['nkeyin']);
			$data['tkeyin'] = ($tkeyin);
		}else{
			$data['nkeyin']  = ($nkeyin);
			$data['vatin']  = ($nkeyin * ((($vatrt+ 100) / 100) - 1));
			$data['tkeyin'] = ($nkeyin * (($vatrt+ 100) / 100));
		}
		
		foreach($data as $key => $val){
			$data[$key] = number_format($val,2);
		}
		
		echo json_encode($data);
	}
}




















