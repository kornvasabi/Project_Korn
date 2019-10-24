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
class ReserveCar extends MY_Controller {
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
		//print_r($claim); exit;
		if($claim['m_access'] != "T"){ echo "<div align='center' style='color:red;font-size:16pt;width:100%;'>ขออภัย คุณยังไม่มีสิทธิเข้าใช้งานหน้านี้ครับ</div>"; exit; }
		//style='height:calc(100vh - 132px);overflow:auto;background-color:white;'
		$html = "
			<div class='tab1' name='home' locat='{$this->sess['branch']}' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}'>
				<div class='divcondition col-sm-12' style='overflow:auto;'>					
					<div class='row'>
						<div class='col-xs-2 col-sm-2'>	
							<div class='form-group'>
								เลขที่ใบจอง
								<input type='text' id='RESVNO' class='form-control input-sm' placeholder='เลขที่สัญญา' >
							</div>
						</div>
						<div class='col-xs-2 col-sm-2'>	
							<div class='form-group'>
								วันที่จอง
								<input type='text' id='SRESVDT' class='form-control input-sm' placeholder='จาก' data-provide='datepicker' data-date-language='th-th' value='".$this->today('startofmonth')."'>
							</div>
						</div>	
						<div class='col-xs-2 col-sm-2'>	
							<div class='form-group'>
								ถึง
								<input type='text' id='ERESVDT' class='form-control input-sm' placeholder='ถึง' data-provide='datepicker' data-date-language='th-th' value='".$this->today('endofmonth')."'>
							</div>
						</div>	
						<div class='col-xs-2 col-sm-3'>	
							<div class='form-group'>
								เลขตัวถัง
								<input type='text' id='STRNO' class='form-control input-sm' placeholder='เลขตัวถัง' >
							</div>
						</div>
						<div class='col-xs-2 col-sm-3'>	
							<div class='form-group'>
								ชื่อ-สกุล ผู้จอง
								<select id='CUSCOD' class='form-control input-sm chosen-select' data-placeholder='ชื่อ-สกุล ผู้จอง'></select>
							</div>
						</div>
					</div>
					<div class='row'>
						<div class='col-sm-6'>	
							<div class='form-group'>
								<button id='btnt1reserve' class='btn btn-cyan btn-block'>
									<span class='glyphicon glyphicon-pencil'> จอง</span>
								</button>
							</div>
						</div>
						<div class='col-sm-6'>	
							<div class='form-group'>
								<button id='btnt1search' class='btn btn-primary btn-block'>
									<span class='glyphicon glyphicon-search'> แสดง</span>
								</button>
							</div>
						</div>
					</div>
				</div>
				<div id='result'></div>
			</div>
		";
		
		$html.= "<script src='".base_url('public/js/SYS04/ReserveCar.js')."'></script>";
		echo $html;
	}
	
	function search(){
		$arrs = array();
		$arrs['RESVNO']	= $_REQUEST['RESVNO'];
		$arrs['SRESVDT'] = $this->Convertdate(1,$_REQUEST['SRESVDT']);
		$arrs['ERESVDT'] = $this->Convertdate(1,$_REQUEST['ERESVDT']);
		$arrs['STRNO'] 	= $_REQUEST['STRNO'];
		$arrs['CUSCOD'] = $_REQUEST['CUSCOD'];
		
		$cond = "";
		if($arrs['RESVNO'] != ""){
			$cond .= " and A.RESVNO like '".$arrs['RESVNO']."%'";
		}
		
		if($arrs['SRESVDT'] != "" and $arrs['ERESVDT'] != ""){
			$cond .= " and convert(varchar(8),A.RESVDT,112) between '".$arrs['SRESVDT']."' and '".$arrs['ERESVDT']."' ";
		}else if($arrs['SRESVDT'] != "" and $arrs['ERESVDT'] == ""){
			$cond .= " and convert(varchar(8),A.RESVDT,112) = '".$arrs['SRESVDT']."'";
		}else if($arrs['SRESVDT'] == "" and $arrs['ERESVDT'] != ""){
			$cond .= " and convert(varchar(8),A.RESVDT,112) = '".$arrs['ERESVDT']."'";
		}
		
		if($arrs['STRNO'] != ""){
			$cond .= " and A.STRNO like '".$arrs['STRNO']."%'";
		}
		
		if($arrs['CUSCOD'] != ""){
			$cond .= " and A.CUSCOD like '".$arrs['CUSCOD']."'";
		}
		
		$sql = "
			SELECT ".($cond == "" ? "top 20":"")." A.RESVNO,convert(varchar(8),A.RESVDT,112) as RESVDT
				,A.CUSCOD,B.SNAM+B.NAME1+' '+B.NAME2 as NAME,A.STRNO
				,A.RESPAY
				,A.RESPAY - (A.SMPAY + A.SMCHQ) as CWO
				,CONVERT(varchar(8),A.INPDT,112) as INPDT
				,CONVERT(varchar(5),A.INPDT,108) as INPTM
			FROM {$this->MAuth->getdb('ARRESV')} A
			left join {$this->MAuth->getdb('CUSTMAST')} B on A.CUSCOD=B.CUSCOD
			where 1=1 ".$cond."
			order by A.RESVNO
		";
		//echo $sql; exit;		
		$query = $this->db->query($sql);
		
		$html = "";
		$NRow = 1;
		if($query->row()){
			foreach($query->result() as $row){
				$html .= "
					<tr>
						<td>
							<i class='resvnoClick btn btn-xs btn-success glyphicon glyphicon-zoom-in' RESVNO='".$row->RESVNO."' style='cursor:pointer;'> รายละเอียด  </i>
						</td>
						<td>".$row->RESVNO."</td>
						<td>".$row->STRNO."</td>
						<td>".$this->Convertdate(2,$row->RESVDT)."</td>
						<td>".$row->NAME."</td>
						<td>".number_format($row->RESPAY,2)."</td>
						<td>".number_format($row->CWO,2)."</td>
						<td>".$this->Convertdate(2,$row->INPDT)." ".$row->INPTM."</td>
					</tr>
				";
				
				$NRow++;
			}
		}
		
		$html = "
			<div id='table-fixed-ReserveCar' class='col-sm-12' style='height:calc(100% - 30px);width:100%;overflow:auto;font-size:8pt;'>
				<table id='table-ReserveCar' class='table table-bordered' cellspacing='0' width='calc(100% - 1px)'>
					<thead style='background: rgba(0, 0, 0, 0) url(&#39;../public/lobiadmin-master/version/1.0/ajax/img/bg/bg6.png&#39;) repeat scroll 0% 0%;'>
						<tr style='line-height:20px;'>
							<td style='vertical-align:middle;text-align:center;font-size:8pt;' colspan='8'>
								เงื่อนไข
							</td>
						</tr>
						<tr>
							<th style='vertical-align:middle;'>#</th>
							<th style='vertical-align:middle;'>เลขที่บิลจอง</th>
							<th style='vertical-align:middle;'>เลขตัวถัง</th>
							<th style='vertical-align:middle;'>วันที่จอง</th>
							<th style='vertical-align:middle;'>ชื่อ-สกุล</th>
							<th style='vertical-align:middle;'>เงินจอง</th>
							<th style='vertical-align:middle;'>ค้างชำระ</th>
							<th style='vertical-align:middle;'>วันที่ทำรายการ</th>
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
	
	function getfromReserve(){
		$RESVNO = $_POST["RESVNO"];
		$EVENT  = $_POST["EVENT"];
		
		$arrs = array();
		$arrs["fRESVNO"] 	= "Auto Genarate";
		$arrs["fRESVDT"] 	= $this->today('today');
		$arrs["fLOCAT"] 	= "<option value='".$this->sess['branch']."'>".$this->sess['branch']."</option>";
		$arrs["fCUSCOD"] 	= "";
		$arrs["fRECVCD"] 	= "<option value='".$this->sess["USERID"]."'>".$this->sess["name"]." (".$this->sess["USERID"].")</option>";
		$arrs["fSALCOD"] 	= "<option value='".$this->sess["USERID"]."'>".$this->sess["name"]." (".$this->sess["USERID"].")</option>";
		$arrs["fVATRT"] 	= "";
		$arrs["fTAXNO"] 	= "";
		$arrs["fTAXDT"] 	= "";
		$arrs["fSTRNO"] 	= "";
		$arrs["fACTICOD"] 	= "";
		$arrs["fGRPCOD"]	= "";
		$arrs["fTYPE"] 		= "<option value='HONDA'>HONDA</option>";
		$arrs["fMODEL"] 	= "";
		$arrs["fBAAB"] 		= "";
		$arrs["fCOLOR"] 	= "";
		$arrs["fCC"] 		= $this->opt('CC','');
		$arrs["fSTAT"] 		= $this->opt('STAT','');
		$arrs["fPRICE"] 	= "";
		$arrs["fSTDID"] 	= "";
		$arrs["fSTDPLRANK"] = "";
		$arrs["fRESPAY"] 	= "";
		$arrs["fBALANCE"] 	= "";
		$arrs["fRECVDUE"] 	= "";
		$arrs["fRECVDT"] 	= "";
		$arrs["fSMPAY"] 	= "";
		$arrs["fSMOWE"] 	= "";
		$arrs["fMEMO1"] 	= "";
		
		$sql = "
			select a.RESVNO ,convert(varchar(8),a.RESVDT,112) as RESVDT,a.LOCAT ,a.CUSCOD 
				,b.SNAM+b.NAME1+' '+b.NAME2+' ('+a.CUSCOD+')-'+b.GRADE as CUSNAME,a.RECVCD,a.SALCOD
				,(select aa.USERNAME+' ('+aa.USERID+')' from {$this->MAuth->getdb('PASSWRD')} aa where aa.USERID=a.RECVCD ) as RECVCDNAME
				,(select bb.USERNAME+' ('+bb.USERID+')' from {$this->MAuth->getdb('PASSWRD')} bb where bb.USERID=a.SALCOD ) as SALCODNAME
				,a.VATRT ,a.TAXNO ,convert(varchar(8),a.TAXDT,112) as TAXDT
				,a.STRNO ,d.ACTICOD ,(
					select '('+d.ACTICOD+') '+cc.ACTIDES collate thai_cs_as from {$this->MAuth->getdb('SETACTI')} cc 
					where cc.ACTICOD=d.ACTICOD collate thai_cs_as
				 ) as ACTIDES
				,a.GRPCOD ,'('+a.GRPCOD+') '+c.GDESC as GRPDESC
				,a.TYPE ,a.MODEL ,a.BAAB ,a.COLOR ,a.CC ,a.STAT 
				,a.PRICE ,d.STDID ,d.STDPLRANK,a.RESPAY ,a.BALANCE 
				,convert(varchar(8),a.RECVDUE,112) as RECVDUE 
				,convert(varchar(8),a.RECVDT,112) as RECVDT 
				,a.SMPAY ,a.SMCHQ ,a.MEMO1
				,isnull(a.RESPAY,0) - (isnull(a.SMPAY,0) + isnull(a.SMCHQ,0)) as SMOWE
			from {$this->MAuth->getdb('ARRESV')} a 
			left join {$this->MAuth->getdb('CUSTMAST')} b on a.CUSCOD=b.CUSCOD
			left join {$this->MAuth->getdb('SETGROUP')} c on a.GRPCOD=c.GCODE
			left join {$this->MAuth->getdb('ARRESVOTH')} d on a.RESVNO=d.RESVNO collate thai_cs_as and d.REF='".strtoupper($this->sess["db"])."'
			where a.RESVNO='{$RESVNO}'
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		if($query->row()){
			foreach($query->result() as $row){
				$arrs["fRESVNO"] 	= $row->RESVNO;
				$arrs["fRESVDT"] 	= $this->Convertdate(2,$row->RESVDT);
				$arrs["fLOCAT"]  	= "<option value='".$row->LOCAT."'>".$row->LOCAT."</option>";
				$arrs["fCUSCOD"] 	= "<option value='".$row->CUSCOD."'>".$row->CUSNAME."</option>";
				$arrs["fRECVCD"] 	= "<option value='".$row->RECVCD."'>".$row->RECVCDNAME."</option>";
				$arrs["fSALCOD"] 	= "<option value='".$row->SALCOD."'>".$row->SALCODNAME."</option>";
				$arrs["fVATRT"]  	= number_format($row->VATRT,2);
				$arrs["fTAXNO"]  	= $row->TAXNO;
				$arrs["fTAXDT"]  	= $this->Convertdate(2,$row->TAXDT);
				$arrs["fSTRNO"] 	= "<option value='".$row->STRNO."'>".$row->STRNO."</option>";
				$arrs["fACTICOD"] 	= "<option value='".$row->ACTICOD."'>".$row->ACTIDES."</option>";
				$arrs["fGRPCOD"] 	= "<option value='".$row->GRPCOD."'>".$row->GRPDESC."</option>";
				$arrs["fTYPE"] 		= "<option value='".$row->TYPE."'>".$row->TYPE."</option>";
				$arrs["fMODEL"] 	= "<option value='".$row->MODEL."'>".$row->MODEL."</option>";
				$arrs["fBAAB"] 		= "<option value='".$row->BAAB."'>".$row->BAAB."</option>";
				$arrs["fCOLOR"] 	= "<option value='".$row->COLOR."'>".$row->COLOR."</option>";
				$arrs["fCC"] 		= $this->opt('CC',$row->CC);
				$arrs["fSTAT"] 		= $this->opt('STAT',$row->STAT);
				$arrs["fPRICE"]  	= number_format($row->PRICE,2);
				$arrs["fSTDID"]		= $row->STDID;
				$arrs["fSTDPLRANK"]	= $row->STDPLRANK;
				$arrs["fRESPAY"]  	= number_format($row->RESPAY,2);
				$arrs["fBALANCE"]  	= number_format($row->BALANCE,2);
				$arrs["fRECVDUE"]  	= $this->Convertdate(2,$row->RECVDUE);
				$arrs["fRECVDT"]  	= $this->Convertdate(2,$row->RECVDT);
				$arrs["fSMPAY"]  	= number_format($row->SMPAY,2);
				$arrs["fSMOWE"]  	= number_format($row->SMOWE,2);
				$arrs["fMEMO1"]  	= $row->MEMO1;				
			}
		}
			
		$html = "
			<h3 class='text-primary'>ผู้เช่าซื้อ</h3>
			<div class='row col-sm-12' style='border:1px dotted #aaa;'>
				<div class='col-sm-3'>	
					<div class='form-group'>
						เลขที่บิลจอง
						<input type='text' id='fRESVNO' class='form-control input-sm' value='{$arrs["fRESVNO"]}' style='font-size:12pt;' readonly>
					</div>
				</div>
				<div class='col-sm-3'>	
					<div class='form-group'>
						วันที่จอง
						<input type='text' id='fRESVDT' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' value='{$arrs["fRESVDT"]}' maxlength=10>
					</div>
				</div>
				<div class='col-sm-3'>	
					<div class='form-group'>
						สาขา
						<select id='fLOCAT' class='form-control input-sm'>
							{$arrs["fLOCAT"]}
						</select>
					</div>
				</div>
				<div class='col-sm-3'>	
					<div class='form-group'>
						ชื่อสกุล-ลูกค้า
						<select id='fCUSCOD' class='form-control input-sm'>
							{$arrs["fCUSCOD"]}
						</select>
					</div>
				</div>
				<div class='col-sm-3'>	
					<div class='form-group'>
						รหัสผู้รับจอง
						<select id='fRECVCD' class='form-control input-sm'>
							{$arrs["fRECVCD"]}
						</select>
					</div>
				</div>
				<div class='col-sm-3'>	
					<div class='form-group'>
						รหัสพนักงานขาย
						<select id='fSALCOD' class='form-control input-sm'>
							{$arrs["fSALCOD"]}
						</select>
					</div>
				</div>
				<div class='col-sm-2'>	
					<div class='form-group'>
						อัตราภาษี(%)
						<input type='text' id='fVATRT' class='form-control input-sm' value='{$arrs["fVATRT"]}' disabled>
					</div>
				</div>
				<div class='col-sm-2'>	
					<div class='form-group'>
						เลขที่ใบกำกับ
						<input type='text' id='fTAXNO' class='form-control input-sm' value='{$arrs["fTAXNO"]}' disabled>
					</div>
				</div>
				<div class='col-sm-2'>	
					<div class='form-group'>
						วันที่ใบกำกับ
						<input type='text' id='fTAXDT' class='form-control input-sm' value='{$arrs["fTAXDT"]}' disabled>
					</div>
				</div>
			</div>
			
			
			<h3 class='text-primary'>ข้อมูลรถ</h3>
			<div id='datepkposition' class='row col-sm-12' style='border:1px dotted #aaa;'>
				<div class='col-sm-3'>	
					<div class='form-group'>
						เลขตัวถัง
						<select id='fSTRNO' class='form-control input-sm'>
							{$arrs["fSTRNO"]}
						</select>
					</div>
				</div>
				<div class='col-sm-3'>	
					<div class='form-group'>
						กิจกรรมการขาย
						<select id='fACTICOD' class='form-control input-sm'>
							{$arrs["fACTICOD"]}
						</select>
					</div>
				</div>
				<div class='col-sm-3'>	
					<div class='form-group'>
						ประเภทสินค้า
						<select id='fGRPCOD' class='form-control input-sm'>
							{$arrs["fGRPCOD"]}
						</select>
					</div>
				</div>
				<div class='col-sm-3'>	
					<div class='form-group'>
						ยี่ห้อ
						<select id='fTYPE' class='form-control input-sm'>
							{$arrs["fTYPE"]}
						</select>
					</div>
				</div>
				<div class='col-sm-3'>	
					<div class='form-group'>
						รุ่น
						<select id='fMODEL' class='form-control input-sm'>
							{$arrs["fMODEL"]}
						</select>
					</div>
				</div>
				<div class='col-sm-3'>	
					<div class='form-group'>
						แบบ
						<select id='fBAAB' class='form-control input-sm'>
							{$arrs["fBAAB"]}
						</select>
					</div>
				</div>
				<div class='col-sm-3'>	
					<div class='form-group'>
						สี
						<select id='fCOLOR' class='form-control input-sm'>
							{$arrs["fCOLOR"]}
						</select>
					</div>
				</div>
				<div class='col-sm-3'>	
					<div class='form-group'>
						ขนาด
						<select id='fCC' class='form-control input-sm'>
							{$arrs["fCC"]}
						</select>
					</div>
				</div>
				<div class='col-sm-3'>	
					<div class='form-group'>
						สถานะ
						<select id='fSTAT' class='form-control input-sm'>
							{$arrs["fSTAT"]}
						</select>
					</div>
				</div>
				<div class='col-sm-3'>	
					<div class='form-group'>
						ราคาขายรวมภาษี
						<input type='text' id='fPRICE' class='form-control input-sm jzAllowNumber' value='{$arrs["fPRICE"]}'
							stdid='".$arrs["fSTDID"]."' stdplrank='".$arrs["fSTDPLRANK"]."'>
					</div>
				</div>
				<div class='col-sm-3'>	
					<div class='form-group'>
						เงินจองรวมภาษี
						<input type='text' id='fRESPAY' class='form-control input-sm jzAllowNumber' value='{$arrs["fRESPAY"]}'>
					</div>
				</div>
				<div class='col-sm-3'>	
					<div class='form-group'>
						ยอดคงเหลือ
						<input type='text' id='fBALANCE' class='form-control input-sm jzAllowNumber' value='{$arrs["fBALANCE"]}' disabled>
					</div>
				</div>
				<div class='col-sm-3'>	
					<div class='form-group'>
						วันนัดรับรถ
						<input type='text' id='fRECVDUE' class='form-control input-sm' value='{$arrs["fRECVDUE"]}' data-provide='datepicker' data-date-language='th-th' value='' maxlength=10>
					</div>
				</div>
				<div class='col-sm-3'>	
					<div class='form-group'>
						วันที่รับรถจริง
						<input type='text' id='fRECVDT' class='form-control input-sm' value='{$arrs["fRECVDT"]}' data-provide='datepicker' data-date-language='th-th' value='' maxlength=10 disabled>
					</div>
				</div>
				<div class='col-sm-3'>	
					<div class='form-group'>
						ชำระเงินจองแล้ว
						<input type='text' id='fSMPAY' class='form-control input-sm jzAllowNumber' value='{$arrs["fSMPAY"]}' disabled>
					</div>
				</div>
				<div class='col-sm-3'>	
					<div class='form-group'>
						ค้างชำระเงินจอง
						<input type='text' id='fSMOWE' class='form-control input-sm jzAllowNumber' value='{$arrs["fSMOWE"]}' disabled>
					</div>
				</div>
				<div class='col-sm-6'>	
					<div class='form-group'>
						หมายเหตุ
						<textarea id='fMEMO1' class='form-control input-sm' rows='3' style='resize:vertical;'>{$arrs["fMEMO1"]}</textarea>
					</div>
				</div>
			</div>
			
			<div class='col-sm-2 col-sm-offset-8'>
				<br/><br/>
				<button id='btnDelete' class='btn btn-danger btn-block'>
					<span class='glyphicon glyphicon-trash'> ลบ</span>
				</button>
				<br/>
			</div>
			<div class='col-sm-2'>
				<br/><br/>
				<button id='btnSave' class='btn btn-primary btn-block'>
					<span class='glyphicon glyphicon-floppy-disk'> บันทึก</span>
				</button>
				<br/>
			</div>
		";
		
		$response = array('html'=>$html,'EVENT'=>$EVENT,'status'=>true);
		echo json_encode($response);
	}
	
	function getStandart(){
		$response = array("error"=>false,"msg"=>"");
		
		$arrs["RESVDT"]  = $this->Convertdate(1,$_POST["RESVDT"]);
		$arrs["ACTICOD"] = $_POST["ACTICOD"];
		$arrs["ACTIDES"] = $_POST["ACTIDES"];
		$arrs["MODEL"] 	 = $_POST["MODEL"];
		$arrs["BAAB"] 	 = $_POST["BAAB"];
		$arrs["COLOR"] 	 = $_POST["COLOR"];
		
		if($arrs["RESVDT"] == ""){
			$response["error"] = true;
			$response["msg"] = "ผิดพลาด คุณยังไม่ได้ระบุวันที่จองรถ";
			echo json_encode($response); exit;
		}
		
		if($arrs["ACTICOD"] == ""){
			$response["error"] = true;
			$response["msg"] = "ผิดพลาด คุณยังไม่ได้ระบุกิจกรรมการขาย";
			echo json_encode($response); exit;
		}
		
		if($arrs["MODEL"] == ""){
			$response["error"] = true;
			$response["msg"] = "ผิดพลาด คุณยังไม่ได้ระบุรุ่น";
			echo json_encode($response); exit;
		}
		
		if($arrs["BAAB"] == ""){
			$response["error"] = true;
			$response["msg"] = "ผิดพลาด คุณยังไม่ได้ระบุแบบ";
			echo json_encode($response); exit;
		}
		
		if($arrs["COLOR"] == ""){
			$response["error"] = true;
			$response["msg"] = "ผิดพลาด คุณยังไม่ได้ระบุสี";
			echo json_encode($response); exit;
		}
		
		$sql = "
			if exists(
				select * from {$this->MAuth->getdb('std_vehicles')} a
				left join {$this->MAuth->getdb('std_pricelist')} b on a.id=b.id and '".$arrs["RESVDT"]."' between event_s and isnull(event_e,GETDATE())
				where a.model='{$arrs["MODEL"]}' and a.baab='{$arrs["BAAB"]}' and a.color='{$arrs["COLOR"]}' and b.ACTICOD='{$arrs["ACTICOD"]}'
			)
			begin 
				select * from {$this->MAuth->getdb('std_vehicles')} a
				left join {$this->MAuth->getdb('std_pricelist')} b on a.id=b.id and '".$arrs["RESVDT"]."' between event_s and isnull(event_e,GETDATE())
				where a.model='{$arrs["MODEL"]}' and a.baab='{$arrs["BAAB"]}' and a.color='{$arrs["COLOR"]}' and b.ACTICOD='{$arrs["ACTICOD"]}'
			end 
			else if exists(
				select * from {$this->MAuth->getdb('std_vehicles')} a
				left join {$this->MAuth->getdb('std_pricelist')} b on a.id=b.id and '".$arrs["RESVDT"]."' between event_s and isnull(event_e,GETDATE())
				where a.model='{$arrs["MODEL"]}' and a.baab='{$arrs["BAAB"]}' and a.color='{$arrs["COLOR"]}' and b.ACTICOD='ALL'
			)
			begin 
				select * from {$this->MAuth->getdb('std_vehicles')} a
				left join {$this->MAuth->getdb('std_pricelist')} b on a.id=b.id and '".$arrs["RESVDT"]."' between event_s and isnull(event_e,GETDATE())
				where a.model='{$arrs["MODEL"]}' and a.baab='{$arrs["BAAB"]}' and a.color='{$arrs["COLOR"]}' and b.ACTICOD='ALL'
			end 
			else if exists(
				select * from {$this->MAuth->getdb('std_vehicles')} a
				left join {$this->MAuth->getdb('std_pricelist')} b on a.id=b.id and '".$arrs["RESVDT"]."' between event_s and isnull(event_e,GETDATE())
				where a.model='{$arrs["MODEL"]}' and a.baab='{$arrs["BAAB"]}' and a.color='ALL' and b.ACTICOD='{$arrs["ACTICOD"]}'
			)
			begin 
				select * from {$this->MAuth->getdb('std_vehicles')} a
				left join {$this->MAuth->getdb('std_pricelist')} b on a.id=b.id and '".$arrs["RESVDT"]."' between event_s and isnull(event_e,GETDATE())
				where a.model='{$arrs["MODEL"]}' and a.baab='{$arrs["BAAB"]}' and a.color='ALL' and b.ACTICOD='{$arrs["ACTICOD"]}'
			end 
			else if exists(
				select * from {$this->MAuth->getdb('std_vehicles')} a
				left join {$this->MAuth->getdb('std_pricelist')} b on a.id=b.id and '".$arrs["RESVDT"]."' between event_s and isnull(event_e,GETDATE())
				where a.model='{$arrs["MODEL"]}' and a.baab='{$arrs["BAAB"]}' and a.color='ALL' and b.ACTICOD='ALL'
			)
			begin 
				select * from {$this->MAuth->getdb('std_vehicles')} a
				left join {$this->MAuth->getdb('std_pricelist')} b on a.id=b.id and '".$arrs["RESVDT"]."' between event_s and isnull(event_e,GETDATE())
				where a.model='{$arrs["MODEL"]}' and a.baab='{$arrs["BAAB"]}' and a.color='ALL' and b.ACTICOD='ALL'
			end 
			else if exists(
				select * from {$this->MAuth->getdb('std_vehicles')} a
				left join {$this->MAuth->getdb('std_pricelist')} b on a.id=b.id and '".$arrs["RESVDT"]."' between event_s and isnull(event_e,GETDATE())
				where a.model='{$arrs["MODEL"]}' and a.baab='ALL' and a.color='ALL' and b.ACTICOD='{$arrs["ACTICOD"]}'
			)
			begin 
				select * from {$this->MAuth->getdb('std_vehicles')} a
				left join {$this->MAuth->getdb('std_pricelist')} b on a.id=b.id and '".$arrs["RESVDT"]."' between event_s and isnull(event_e,GETDATE())
				where a.model='{$arrs["MODEL"]}' and a.baab='ALL' and a.color='ALL' and b.ACTICOD='{$arrs["ACTICOD"]}'
			end
			else if exists(
				select * from {$this->MAuth->getdb('std_vehicles')} a
				left join {$this->MAuth->getdb('std_pricelist')} b on a.id=b.id and '".$arrs["RESVDT"]."' between event_s and isnull(event_e,GETDATE())
				where a.model='{$arrs["MODEL"]}' and a.baab='ALL' and a.color='ALL' and b.ACTICOD='ALL'
			)
			begin 
				select * from {$this->MAuth->getdb('std_vehicles')} a
				left join {$this->MAuth->getdb('std_pricelist')} b on a.id=b.id and '".$arrs["RESVDT"]."' between event_s and isnull(event_e,GETDATE())
				where a.model='{$arrs["MODEL"]}' and a.baab='ALL' and a.color='ALL' and b.ACTICOD='ALL'
			end
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
			
		if($query->row()){
			foreach($query->result() as $row){
				$response["price"] = $row->price;
				$response["stdid"] = $row->id;
				$response["stdplrank"] = $row->plrank;
			}
		}else{
			$response["error"] = true;
			$response["msg"] = "
				ผิดพลาด ไม่พบราคาขายรถใหม่ โปรดติดต่อฝ่ายเช่าซื้อ/ฝ่ายวิเคราะห์ เพื่อกำหนดราคาขายก่อนครับ<br><br>
				รุ่น :: ".$arrs["MODEL"]."<br>
				แบบ :: ".$arrs["BAAB"]."<br>
				สี :: ".$arrs["COLOR"]."<br>
				กิจกรรมการขาย :: ".$arrs["ACTIDES"]."
			";
		}
		
		echo json_encode($response);
	}
	
	function getSTRNOSelect(){
		$arrs["STRNO"] = $_POST["STRNO"];
		
		$sql = "
			select a.STRNO,a.GCODE,'('+a.GCODE+') '+b.GDESC as GDESC
				,a.TYPE,a.MODEL,a.BAAB,a.COLOR,a.CC,a.STAT 
			from {$this->MAuth->getdb('INVTRAN')} a
			left join {$this->MAuth->getdb('SETGROUP')} b on a.GCODE=b.GCODE
			where STRNO='".$arrs["STRNO"]."'
		";
		$query = $this->db->query($sql);
		
		$data = array();
		if($query->row()){
			foreach($query->result() as $row){
				foreach($row as $key => $val){
					$data[$key] = str_replace(chr(0),"",$val);
				}
			}
		}
		
		echo json_encode($data);
	}
	
	function setBalance(){
		$arrs 	 = array();
		$arrs["PRICE"]   = str_replace(",","",$_POST["PRICE"]);
		$arrs["RESPAY"]  = str_replace(",","",$_POST["RESPAY"]);
		$arrs["BALANCE"] = ($arrs["PRICE"] == "" ? 0 : $arrs["PRICE"]) - ($arrs["RESPAY"] == "" ? 0 : $arrs["RESPAY"]);
		
		foreach($arrs as $key => $val){
			$arrs[$key] = ($val == "" ? "":number_format($val,2));
		}
		
		echo json_encode($arrs);
	}
	
	function SaveRESV(){
		$response = array('error'=>false,'msg'=>'');
		$arrs = array();
		$arrs["RESVNO"] 	= $_POST["RESVNO"];
		$arrs["RESVDT"] 	= $this->Convertdate(1,$_POST["RESVDT"]);
		$arrs["LOCAT"] 		= $_POST["LOCAT"];
		$arrs["CUSCOD"] 	= $_POST["CUSCOD"];
		$arrs["RECVCD"] 	= $_POST["RECVCD"];
		$arrs["SALCOD"] 	= $_POST["SALCOD"];
		$arrs["VATRT"] 		= $_POST["VATRT"];
		$arrs["TAXNO"] 		= $_POST["TAXNO"];
		$arrs["TAXDT"] 		= $_POST["TAXDT"];
		$arrs["STRNO"] 		= $_POST["STRNO"];
		$arrs["ACTICOD"] 	= $_POST["ACTICOD"];
		$arrs["GCODE"] 		= $_POST["GCODE"];
		$arrs["TYPE"] 		= $_POST["TYPE"];
		$arrs["MODEL"] 		= $_POST["MODEL"];
		$arrs["BAAB"] 		= $_POST["BAAB"];
		$arrs["COLOR"] 		= $_POST["COLOR"];
		$arrs["CC"] 		= $_POST["CC"];
		$arrs["STAT"] 		= $_POST["STAT"];
		$arrs["PRICE"] 		= $_POST["PRICE"];
		$arrs["STDID"] 		= $_POST["STDID"];
		$arrs["STDPLRANK"] 	= $_POST["STDPLRANK"];
		$arrs["RESPAY"] 	= $_POST["RESPAY"];
		$arrs["BALANCE"] 	= $_POST["BALANCE"];
		$arrs["RECVDUE"] 	= $this->Convertdate(1,$_POST["RECVDUE"]);
		$arrs["RECVDT"] 	= $this->Convertdate(1,$_POST["RECVDT"]);
		$arrs["SMPAY"] 		= $_POST["SMPAY"];
		$arrs["SMCHQ"] 		= $_POST["SMCHQ"];
		$arrs["MEMO1"] 		= $_POST["MEMO1"];
		
		if($arrs["CUSCOD"] == ""){
			$response["error"] = true;
			$response["msg"] = 'คุณยังไม่ได้ระบุชื่อลูกค้าเลย เลือกลูกค้าก่อนนะครับ';
			echo json_encode($response); exit;
		}
		
		if($arrs["ACTICOD"] == "" and $arrs["RESVNO"] == "Auto Genarate"){
			$response["error"] = true;
			$response["msg"] = 'คุณยังไม่ได้ระบุกิจกรรมการขายเลย เลือกกิจกรรมการขายก่อนนะครับ';
			echo json_encode($response); exit;
		}
		
		if($arrs["TYPE"] == ""){
			$response["error"] = true;
			$response["msg"] = 'คุณยังไม่ได้ระบุยี่ห้อเลย เลือกยี่ห้อก่อนนะครับ';
			echo json_encode($response); exit;
		}
		
		if($arrs["MODEL"] == ""){
			$response["error"] = true;
			$response["msg"] = 'คุณยังไม่ได้ระบุรุ่นเลย เลือกรุ่นก่อนนะครับ';
			echo json_encode($response); exit;
		}
		
		if($arrs["BAAB"] == ""){
			$response["error"] = true;
			$response["msg"] = 'คุณยังไม่ได้ระบุแบบเลย เลือกแบบก่อนนะครับ';
			echo json_encode($response); exit;
		}
		
		if($arrs["COLOR"] == ""){
			$response["error"] = true;
			$response["msg"] = 'คุณยังไม่ได้ระบุสีเลย เลือกสีก่อนนะครับ';
			echo json_encode($response); exit;
		}
		
		if($arrs["STAT"] == ""){
			$response["error"] = true;
			$response["msg"] = 'คุณยังไม่ได้ระบุสถานะรถเลย เลือกสถานะรถก่อนนะครับ';
			echo json_encode($response); exit;
		}
		
		if($arrs["PRICE"] == ""){
			$response["error"] = true;
			$response["msg"] = 'คุณยังไม่ได้ระบุราคาขายรวมภาษีเลย ระบุราคาขายรวมภาษีรถก่อนนะครับ';
			echo json_encode($response); exit;
		}
		
		if($arrs["RESPAY"] == ""){
			$response["error"] = true;
			$response["msg"] = 'คุณยังไม่ได้ระบุเงินจองรวมภาษีเลย ระบุเงินจองรถก่อนนะครับ';
			echo json_encode($response); exit;
		}
		
		if($arrs["RECVDUE"] == ""){
			$response["error"] = true;
			$response["msg"] = 'คุณยังไม่ได้ระบุวันนัดรับรถเลย ระบุวันนัดรับรถก่อนนะครับ';
			echo json_encode($response); exit;
		}
		
		if($arrs["VATRT"] == ""){ $arrs["VATRT"] = "0.00"; }
		if($arrs["CC"] == ""){ $arrs["CC"] = "0.00"; }
		if($arrs["PRICE"] == ""){ $arrs["PRICE"] = "0.00"; }else{ $arrs["PRICE"] = str_replace(",","",$arrs["PRICE"]); }
		if($arrs["RESPAY"] == ""){ $arrs["RESPAY"] = "0.00"; }else{ $arrs["RESPAY"] = str_replace(",","",$arrs["RESPAY"]); }
		if($arrs["BALANCE"] == ""){ $arrs["BALANCE"] = "0.00"; }else{ $arrs["BALANCE"] = str_replace(",","",$arrs["BALANCE"]); }
		if($arrs["SMPAY"] == ""){ $arrs["SMPAY"] = "0.00"; }else{ $arrs["SMPAY"] = str_replace(",","",$arrs["SMPAY"]); }
		if($arrs["SMCHQ"] == ""){ $arrs["SMCHQ"] = "0.00"; }else{ $arrs["SMCHQ"] = str_replace(",","",$arrs["SMCHQ"]); }
		
		if($arrs["RESVNO"] == "Auto Genarate"){
			// บันทึกบิลจอง
			$sql = "
				if OBJECT_ID('tempdb..#transaction') is not null drop table #transaction;
				create table #transaction (error varchar(1),resvno varchar(12),msg varchar(max));

				begin tran tst
				begin try 
					/* @symbol = สัญลักษณ์แทนประเภทของเลขที่ นั้นๆ */
					declare @symbol varchar(10) = (select H_RESV from {$this->MAuth->getdb('CONDPAY')});
					/* @rec = รหัสพื้นฐาน */
					declare @rec varchar(10) = (select SHORTL+@symbol+'-'+right(left(convert(varchar(8),GETDATE(),112),6),4) from {$this->MAuth->getdb('INVLOCAT')} where LOCATCD='".$arrs["LOCAT"]."');
					/* @RESVNO = รหัสที่จะใช้ */
					declare @RESVNO varchar(12) = isnull((select MAX(RESVNO) from {$this->MAuth->getdb('ARRESV')} where RESVNO like ''+@rec+'%'),@rec+'0000');
					set @RESVNO = left(@RESVNO ,8)+right(right(@RESVNO ,4)+10001,4);
					
					if exists (select * from {$this->MAuth->getdb('ARRESV')} where RESVNO=@RESVNO)
					begin
						rollback tran tst;
						insert into #transaction select 'Y' as id,'','ผิดพลาด เลขที่ใบจองถูกใช้ไปแล้ว โปรดทำรายการใหม่อีกครั้ง' as msg;
						return;
					end
					
					if('".$arrs["STRNO"]."' != '')
					begin
						if not exists (
							select * from {$this->MAuth->getdb('INVTRAN')}
							where STRNO='".$arrs["STRNO"]."' and isnull(RESVNO,'')='' and isnull(RESVDT,'')='' and FLAG='D'
						)
						begin 
							rollback tran tst;
							insert into #transaction select 'Y' as id,'','ผิดพลาด เลขตัวถังนี้ถูกจองไปแล้ว โปรดตรวจสอบรายการใหม่อีกครั้ง' as msg;
							return;
						end
					end
					
					if('".$arrs["STAT"]."' = 'N' and ('".$arrs["STDID"]."' = '' or '".$arrs["STDPLRANK"]."' = ''))
					begin 
						rollback tran tst;
						insert into #transaction select 'Y' as id,'','ผิดพลาด จองรถใหม่ ไม่ได้ดึงราคา std. มาใช้งาน โปรดทำรายการใหม่อีกครั้ง' as msg;
						return;
					end	
					
					begin
						declare @SMCHQ decimal(18,2) = '0.00';
						insert into {$this->MAuth->getdb('ARRESV')} ( 
							RESVNO, LOCAT, RESVDT, CUSCOD, GRPCOD, TYPE, 
							BAAB, MODEL, COLOR, CC, STAT, SALCOD, VATRT, RECVDUE ,
							PRICE, RESPAY, BALANCE, SMPAY, SMCHQ, STRNO, ISSUNO, 
							RECVCD, TAXNO, TAXDT, REQNO, REQLOCAT, MEMO1, 
							INPDT, USERID
						) values (
							@RESVNO,'".$arrs["LOCAT"]."','".$arrs["RESVDT"]."','".$arrs["CUSCOD"]."','".$arrs["GCODE"]."','".$arrs["TYPE"]."',
							'".$arrs["BAAB"]."','".$arrs["MODEL"]."','".$arrs["COLOR"]."','".$arrs["CC"]."','".$arrs["STAT"]."','".$arrs["SALCOD"]."','".$arrs["VATRT"]."',".($arrs["RECVDUE"] == "" ? "null" : "'".$arrs["RECVDUE"]."'").",
							'".$arrs["PRICE"]."','".$arrs["RESPAY"]."','".$arrs["BALANCE"]."','".$arrs["SMPAY"]."',@SMCHQ,'".$arrs["STRNO"]."',null,
							'".$arrs["RECVCD"]."','".$arrs["TAXNO"]."',".($arrs["TAXDT"] == "" ? "null" : "'".$arrs["TAXDT"]."'").",null,null,'".$arrs["MEMO1"]."',
							getdate(),'".$this->sess["USERID"]."'
						)
						
						insert into {$this->MAuth->getdb('ARRESVOTH')} ( 
							RESVNO ,ACTICOD ,STDID ,STDPLRANK ,REF ,INSBY ,INSDT 
						) values (
							@RESVNO,'".$arrs["ACTICOD"]."','".$arrs["STDID"]."','".$arrs["STDPLRANK"]."','".strtoupper($this->sess["db"])."','".$this->sess["IDNo"]."',getdate()
						)
						
						if ('".$arrs["STRNO"]."' != '')
						begin 
							update {$this->MAuth->getdb('INVTRAN')}
							set RESVNO=@RESVNO,
								RESVDT='".$arrs["RESVDT"]."',
								CURSTAT='R'
							where STRNO='".$arrs["STRNO"]."' and isnull(RESVDT,'') = '' and isnull(RESVNO,'') = '' and isnull(CURSTAT,'') = ''
						end
					end
					
					insert into {$this->MAuth->getdb('hp_UserOperationLog')} (userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
					values ('".$this->sess["IDNo"]."','SYS04::บันทึกบิลจองแล้ว',@RESVNO+' :: ".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
					
					insert into #transaction select 'N',@RESVNO,'บันทึกรายการจองรถ เลขที่บิลจอง :: '+@RESVNO+' เรียบร้อยแล้ว';
					commit tran tst;
				end try
				begin catch
					rollback tran tst;
					insert into #transaction select 'Y','',ERROR_MESSAGE();
				end catch
			";
		}else{
			// แก้ไขบิลจอง
			$sql = "
				if OBJECT_ID('tempdb..#transaction') is not null drop table #transaction;
				create table #transaction (error varchar(1),resvno varchar(12),msg varchar(max));
				
				begin tran tst
				begin try 
					if exists (
						select * from {$this->MAuth->getdb('ARANALYZE')}
						where RESVNO='".$arrs["RESVNO"]."'
					)
					begin
						rollback tran tst;
						insert into #transaction select 'Y' as id,'','ผิดพลาด เลขที่บิลจองถูกนำไปใช้แล้ว ไม่สามารถแก้ไขบิลจองได้อีก' as msg;
						return;
					end
				
					if ('".$arrs["STRNO"]."' = '')
					begin
						declare @STRNO varchar(50) = (select STRNO from {$this->MAuth->getdb('ARRESV')} where RESVNO='".$arrs["RESVNO"]."');
						
						if isnull(@STRNO,'') <> ''
						begin
							update {$this->MAuth->getdb('INVTRAN')}
							set RESVNO	= NULL,
								RESVDT	= NULL,
								CURSTAT	= ''
							where STRNO=@STRNO and RESVNO='".$arrs["RESVNO"]."'
						end
					end
					else 
					begin						
						update {$this->MAuth->getdb('INVTRAN')}
						set RESVNO	= '".$arrs["RESVNO"]."',
							RESVDT	= '".$arrs["RESVDT"]."',
							CURSTAT	= 'R'
						where STRNO = '".$arrs["STRNO"]."'
					end
					
					update {$this->MAuth->getdb('ARRESV')}
					set STRNO	 = '".$arrs["STRNO"]."'
						,RECVDUE = ".($arrs["RECVDUE"] == "" ? "NULL" : "'".$arrs["RECVDUE"]."'")."
						,CC		 = '".$arrs["CC"]."'
						,MEMO1 	 = '".$arrs["MEMO1"]."'
					where RESVNO='".$arrs["RESVNO"]."'
					
					insert into {$this->MAuth->getdb('hp_UserOperationLog')} (userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
					values ('".$this->sess["IDNo"]."','SYS04::บันทึกบิลจองแล้ว','".$arrs["RESVNO"]." :: ".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
					
					insert into #transaction select 'N','".$arrs["RESVNO"]."','แก้ไขบิลจอง เลขที่บิลจอง :: ".$arrs["RESVNO"]." เรียบร้อยแล้ว';
					commit tran tst;
				end try
				begin catch
					rollback tran tst;
					insert into #transaction select 'Y','',ERROR_MESSAGE();
				end catch				
			";
		}
		
		//echo $sql; exit;
		$this->db->query($sql);
		$sql = "select * from #transaction";
		$query = $this->db->query($sql);
		
		if($query->row()){
			foreach($query->result() as $row){
				$response["error"] = ($row->error == 'Y' ? true:false);
				$response["msg"] = $row->msg;
			}
		}else{
			$response["error"] = true;
			$response["msg"] = "ผิดพลาด ไม่สามารถบันทึกข้อมูลได้ในขณะนี้";
		}
		
		echo json_encode($response);
	}
	
	function DeletedRESV(){
		$RESVNO = $_POST["RESVNO"];
		
		$sql = "
			if OBJECT_ID('tempdb..#transaction') is not null drop table #transaction;
			create table #transaction (error varchar(1),resvno varchar(12),msg varchar(max));
			
			begin tran tst
			begin try 
				if exists (select * from {$this->MAuth->getdb('CHQTRAN')} where PAYFOR='008' and CONTNO='".$RESVNO."' and FLAG <> 'C')
				begin
					rollback tran tst;
					insert into #transaction select 'Y' as id,'','ผิดพลาด เลขที่บิลจองมีการรับชำระเงินแล้ว ไม่สามารถลบบิลจองได้' as msg;
					return;
				end
				
				if exists (select * from {$this->MAuth->getdb('ARANALYZE')} where RESVNO='".$RESVNO."')
				begin
					rollback tran tst;
					insert into #transaction select 'Y' as id,'','ผิดพลาด เลขที่บิลจองถูกนำไปใช้แล้ว ไม่สามารถลบบิลจองได้' as msg;
					return;
				end
				
				declare @STRNO varchar(50) = (select STRNO from {$this->MAuth->getdb('ARRESV')} where RESVNO='".$RESVNO."');
				if isnull(@STRNO,'') <> ''
				begin
					update {$this->MAuth->getdb('INVTRAN')}
					set RESVNO	= NULL,
						RESVDT	= NULL,
						CURSTAT	= ''
					where STRNO=@STRNO and RESVNO='".$RESVNO."'
				end
				
				delete {$this->MAuth->getdb('ARRESVOTH')} where RESVNO='".$RESVNO."'
				delete {$this->MAuth->getdb('ARRESV')} where RESVNO='".$RESVNO."'
				
				insert into {$this->MAuth->getdb('hp_UserOperationLog')} (userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
				values ('".$this->sess["IDNo"]."','SYS04::ลบบิลจองแล้ว','".$RESVNO." :: ".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
				
				insert into #transaction select 'N','".$RESVNO."','ลบบิลจอง เลขที่บิลจอง :: ".$RESVNO." เรียบร้อยแล้ว';
				commit tran tst;
			end try
			begin catch
				rollback tran tst;
				insert into #transaction select 'Y','',ERROR_MESSAGE();
			end catch	
		";
		$this->db->query($sql);
		$sql = "select * from #transaction";
		$query = $this->db->query($sql);
		
		if($query->row()){
			foreach($query->result() as $row){
				$response["error"] = ($row->error == 'Y' ? true:false);
				$response["msg"] = $row->msg;
			}
		}else{
			$response["error"] = true;
			$response["msg"] = "ผิดพลาด ไม่สามารถทำรายการได้ในขณะนี้";
		}
		
		echo json_encode($response);
	}
	
}




















