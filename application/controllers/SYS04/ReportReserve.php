<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/********************************************************
             ______@11/11/2019______
            / / _ _   _ _     __ 
           / // __ \ / __ \ / __ \
       _ _/ // /_/ // / / // /_/ /
     /_ _ _/ \_ _ //_/ /_/ \__  /
                          _ _/ /
                         /___ /
********************************************************/
class ReportReserve extends MY_Controller {
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
				<div>
					<div class='row'>
						<div class='col-sm-2'>	
							<div class='form-group'>
								รายงาน
								<select id='REPORT' class='form-control input-sm chosen-select' data-placeholder='สถานะ'>
									<option value='1'>รายงานการจองรถ</option>
									<option value='2'>รถจองไม่ระบุเลขตัวถัง</option>
									<option value='3'>รถจองที่ยังไม่ได้ขาย</option>
									<option value='4'>การขายรถจอง</option>
								</select>
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								จองสาขา
								<select id='locat' class='form-control input-sm chosen-select' data-placeholder='จองสาขา'>
									<option value='{$this->sess['branch']}'>{$this->sess['branch']}</option>
								</select>
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								จาก
								<input type='text' id='sRESVDT' class='form-control input-sm' placeholder='ถึง' data-provide='datepicker' data-date-language='th-th' value='".$this->today('startinyear')."'>
							</div>
						</div>	
						<div class='col-sm-2'>	
							<div class='form-group'>
								ถึง
								<input type='text' id='eRESVDT' class='form-control input-sm' placeholder='ถึง' data-provide='datepicker' data-date-language='th-th' value='".$this->today('endofmonth')."'>
							</div>
						</div>	
						<div class='col-sm-2'>	
							<div class='form-group'>
								ชื่อ-สกุล ผู้จอง
								<select id='CUSCOD' class='form-control input-sm chosen-select' data-placeholder='ชื่อ-สกุล ผู้จอง'></select>
							</div>
						</div>
						
						<div class='col-sm-2'>	
							<div class='form-group'>
								กลุ่มรถ
								<select id='GCODE' class='form-control input-sm chosen-select' data-placeholder='กลุ่มรถ'></select>
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								รุ่น
								<select id='MODEL' class='form-control input-sm chosen-select' data-placeholder='รุ่น'></select>
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								แบบ
								<select id='BAAB' class='form-control input-sm chosen-select' data-placeholder='แบบ'></select>
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								สี
								<select id='COLOR' class='form-control input-sm chosen-select' data-placeholder='สี'></select>
							</div>
						</div>						
					</div>
					<div class='row'>
						<div class='col-sm-12'>
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
		
		$html.= "<script src='".base_url('public/js/SYS04/ReportReserve.js')."'></script>";
		echo $html;
	}
	
	function search(){
		$arrs = array();
		$arrs['locat']	= $_REQUEST['locat'];
		$arrs['sRESVDT'] = $this->Convertdate(1,$_REQUEST['sRESVDT']);
		$arrs['eRESVDT'] = $this->Convertdate(1,$_REQUEST['eRESVDT']);
		$arrs['CUSCOD']	= $_REQUEST['CUSCOD'];
		$arrs['MODEL'] 	= $_REQUEST['MODEL'];
		$arrs['BAAB'] 	= $_REQUEST['BAAB'];
		$arrs['COLOR'] 	= $_REQUEST['COLOR'];
		$arrs['GCODE'] 	= $_REQUEST['GCODE'];
		$arrs['REPORT'] = $_REQUEST['REPORT'];
		
		$cond = "";
		$condDesc = "";
		if($arrs['locat'] != ""){
			$cond .= " and A.LOCAT like '".$arrs['locat']."%'";
			$condDesc .= "สาขา ".$arrs['locat'];
		}
		
		if($arrs['sRESVDT'] != "" and $arrs['eRESVDT'] != ""){
			$cond .= " and convert(varchar(8),A.RESVDT,112) between '".$arrs['sRESVDT']."' and '".$arrs['eRESVDT']."' ";
			$condDesc .= " วันที่จอง จากวันที่  ".$_REQUEST['sRESVDT']." - ".$_REQUEST['eRESVDT'];
		}else if($arrs['sRESVDT'] != "" and $arrs['eRESVDT'] == ""){
			$cond .= " and convert(varchar(8),A.RESVDT,112) = '".$arrs['sRESVDT']."'";
			$condDesc .= " วันที่จอง  ".$_REQUEST['sRESVDT'];
		}else if($arrs['sRESVDT'] == "" and $arrs['eRESVDT'] != ""){
			$cond .= " and convert(varchar(8),A.RESVDT,112) = '".$arrs['eRESVDT']."'";
			$condDesc .= " วันที่จอง  ".$_REQUEST['eRESVDT'];
		}
		
		if($arrs['MODEL'] != ""){
			$cond .= " and A.MODEL like '".$arrs['MODEL']."%'";
			$condDesc .= " รุ่น  ".$_REQUEST['MODEL'];
		}
		
		if($arrs['BAAB'] != ""){
			$cond .= " and A.BAAB like '".$arrs['BAAB']."%'";
			$condDesc .= " แบบ ".$_REQUEST['BAAB'];
		}
		
		if($arrs['COLOR'] != ""){
			$cond .= " and A.COLOR like '".$arrs['COLOR']."%'";
			$condDesc .= " สี ".$_REQUEST['COLOR'];
		}
		
		if($arrs['GCODE'] != ""){
			$cond .= " and A.GRPCOD like '".$arrs['GCODE']."%'";
			$condDesc .= " กลุ่มรถ ".$_REQUEST['GCODE'];
		}
		
		if($arrs['CUSCOD'] != ""){
			$cond .= " and A.CUSCOD like '".$arrs['CUSCOD']."'";
			$condDesc .= " รหัสลูกค้า ".$_REQUEST['CUSCOD'];
		}
		
		if($arrs['REPORT'] == 1){
			$condDesc = " รายงานการจองรถ :: ".$condDesc;
		}else if($arrs['REPORT'] == 2){
			$cond .= " and isnull(A.STRNO,'') = ''";
			$condDesc = " รายงานการจองรถไม่ระบุเลขถัง :: ".$condDesc;
		}else if($arrs['REPORT'] == 3){
			$cond .= " and A.SDATE is null";
			$condDesc = " รายงานการจองรถยังไม่ได้ขาย :: ".$condDesc;
		}else if($arrs['REPORT'] == 4){
			$cond .= " and A.SDATE is not null";
			$condDesc = " รายงานการขายรถจอง :: ".$condDesc;
		}
		
		$sql = "select COMP_NM from {$this->MAuth->getdb('CONDPAY')}";
		$query = $this->db->query($sql);
		$company = "";
		if($query->row()){
			foreach($query->result() as $row){
				$company = $row->COMP_NM;
			}
		}
		
		$sql = "
			SELECT A.CUSCOD,(select SNAM+NAME1+' '+NAME2 from {$this->MAuth->getdb('CUSTMAST')} CM where CM.CUSCOD=A.CUSCOD) as CUSNAME
				,A.RESVNO,A.LOCAT,convert(varchar(8),A.RESVDT,112) as RESVDT
				,convert(varchar(8),A.RECVDUE,112) as RECVDUE
				,A.GRPCOD,A.TYPE,A.BAAB,A.MODEL,A.COLOR,A.CC,A.STAT
				,A.SALCOD,A.VATRT,A.PRICE,A.RESPAY,A.BALANCE,A.SMPAY,A.SMCHQ,A.STRNO,A.ISSUNO
				,A.RECVDT,A.RECVCD,A.SDATE,A.TAXNO,A.TAXDT,A.MEMO1,A.REQNO,A.REQLOCAT,A.POSTDT,A.INPDT,A.USERID
				,A.GRPCOD
			FROM {$this->MAuth->getdb('ARRESV')} A
			WHERE 1=1 ".$cond."
			ORDER BY A.RESVNO
		";
		$query = $this->db->query($sql);
		
		$html = "";
		$NRow = 1;
		if($query->row()){
			foreach($query->result() as $row){
				$html .= "
					<tr>
						<td style='vertical-align:middle;'>".$row->LOCAT."</td>
						<td>
							".$row->RESVNO."
							<br>".$this->Convertdate(2,$row->RESVDT)."
							<br>".$this->Convertdate(2,$row->RECVDUE)."
						</td>
						<td>
							".$row->CUSCOD."
							<br>".$row->CUSNAME."
						</td>
						<td style='mso-number-format:\"\@\";'>
							".$row->STRNO."
							<br>".$row->GRPCOD."
						</td>
						<td>
							".$row->MODEL."
							<br>".$row->COLOR."
						</td>
						<td>
							".$row->BAAB."
							<br>".$row->CC."
						</td>
						<td align='right'>
							".number_format($row->PRICE,2)."
							<br>".number_format($row->RESPAY,2)."
							<br>".number_format($row->BALANCE,2)."
						</td>
						<td style='vertical-align:middle;'>".$row->SALCOD."</td>
					</tr>
				";
				
				$NRow++;
			}
		}
		
		$html = "
			<div id='table-fixed-RPReserveCar' class='col-sm-12' style='height:calc(100% - 30px);width:100%;overflow:auto;font-size:8pt;'>
				<table id='table-RPReserveCar' class='table table-bordered' cellspacing='0' width='calc(100% - 1px)'>
					<thead style='background: rgba(0, 0, 0, 0) url(&#39;../public/lobiadmin-master/version/1.0/ajax/img/bg/bg6.png&#39;) repeat scroll 0% 0%;'>
						<tr style='line-height:20px;'>
							<th style='vertical-align:middle;text-align:center;font-size:12pt;' colspan='9'>
								{$company}
							</th>
						</tr>
						<tr style='line-height:20px;'> 
							<th style='vertical-align:middle;text-align:center;font-size:8pt;' colspan='9'>
								เงื่อนไข {$condDesc}
							</th>
						</tr>
						<tr>
							<th style='vertical-align:middle;'>สาขา</th>
							<th style='vertical-align:middle;'>เลขที่บิลจอง<br>วันที่จอง<br>วันที่นัดรับรถ</th>
							<th style='vertical-align:middle;'>รหัสลูกค้า<br>ชื่อ-สกุล</th>
							<th style='vertical-align:middle;'>เลขตัวถัง<br>กลุ่มรถ</th>							
							<th style='vertical-align:middle;'>รุ่น<br>สี</th>
							<th style='vertical-align:middle;'>แบบ<br>ขนาด</th>
							<th style='vertical-align:middle;'>ราคารถ<br>จอง<br>คงเหลือ</th>
							<th style='vertical-align:middle;'>พนักงานขาย</th>
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
	
	function loadding(){
		$html = "
			<div align='center' style='width:100%;'>
				<input type='image' src='".base_url("public/images/loading-icon.gif")."'>			
			</div>
		";
		echo $html;
	}
	
	function pdf(){
		$arrs = array();
		$arrs['locat']	= $_REQUEST['locat'];
		$arrs['sRESVDT'] = $this->Convertdate(1,$_REQUEST['sRESVDT']);
		$arrs['eRESVDT'] = $this->Convertdate(1,$_REQUEST['eRESVDT']);
		$arrs['CUSCOD']	= $_REQUEST['CUSCOD'];
		$arrs['MODEL'] 	= $_REQUEST['MODEL'];
		$arrs['BAAB'] 	= $_REQUEST['BAAB'];
		$arrs['COLOR'] 	= $_REQUEST['COLOR'];
		$arrs['GCODE'] 	= $_REQUEST['GCODE'];
		$arrs['REPORT'] = $_REQUEST['REPORT'];
		$cond = "";
		$condDesc = "";
		if($arrs['locat'] != ""){
			$cond .= " and A.LOCAT like '".$arrs['locat']."%'";
			$condDesc .= "สาขา ".$arrs['locat'];
		}
		
		if($arrs['sRESVDT'] != "" and $arrs['eRESVDT'] != ""){
			$cond .= " and convert(varchar(8),A.RESVDT,112) between '".$arrs['sRESVDT']."' and '".$arrs['eRESVDT']."' ";
			$condDesc .= " วันที่จอง จากวันที่  ".$_REQUEST['sRESVDT']." - ".$_REQUEST['eRESVDT'];
		}else if($arrs['sRESVDT'] != "" and $arrs['eRESVDT'] == ""){
			$cond .= " and convert(varchar(8),A.RESVDT,112) = '".$arrs['sRESVDT']."'";
			$condDesc .= " วันที่จอง  ".$_REQUEST['sRESVDT'];
		}else if($arrs['sRESVDT'] == "" and $arrs['eRESVDT'] != ""){
			$cond .= " and convert(varchar(8),A.RESVDT,112) = '".$arrs['eRESVDT']."'";
			$condDesc .= " วันที่จอง  ".$_REQUEST['eRESVDT'];
		}
		
		if($arrs['MODEL'] != ""){
			$cond .= " and A.MODEL like '".$arrs['MODEL']."%'";
			$condDesc .= " รุ่น  ".$_REQUEST['MODEL'];
		}
		
		if($arrs['BAAB'] != ""){
			$cond .= " and A.BAAB like '".$arrs['BAAB']."%'";
			$condDesc .= " แบบ ".$_REQUEST['BAAB'];
		}
		
		if($arrs['COLOR'] != ""){
			$cond .= " and A.COLOR like '".$arrs['COLOR']."%'";
			$condDesc .= " สี ".$_REQUEST['COLOR'];
		}
		
		if($arrs['GCODE'] != ""){
			$cond .= " and A.GRPCOD like '".$arrs['GCODE']."%'";
			$condDesc .= " กลุ่มรถ ".$_REQUEST['GCODE'];
		}
		
		if($arrs['CUSCOD'] != ""){
			$cond .= " and A.CUSCOD like '".$arrs['CUSCOD']."'";
			$condDesc .= " รหัสลูกค้า ".$_REQUEST['CUSCOD'];
		}
		
		if($arrs['REPORT'] == 1){
			$condDesc = " รายงานการจองรถ :: ".$condDesc;
		}else if($arrs['REPORT'] == 2){
			$cond .= " and isnull(A.STRNO,'') = ''";
			$condDesc = " รายงานการจองรถไม่ระบุเลขถัง :: ".$condDesc;
		}else if($arrs['REPORT'] == 3){
			$cond .= " and A.SDATE is null";
			$condDesc = " รายงานการจองรถยังไม่ได้ขาย :: ".$condDesc;
		}else if($arrs['REPORT'] == 4){
			$cond .= " and A.SDATE is not null";
			$condDesc = " รายงานการขายรถจอง :: ".$condDesc;
		}
		
		$sql = "select COMP_NM from {$this->MAuth->getdb('CONDPAY')}";
		$query = $this->db->query($sql);
		$company = "";
		if($query->row()){
			foreach($query->result() as $row){
				$company = $row->COMP_NM;
			}
		}
		
		$sql = "
			SELECT A.CUSCOD,(select SNAM+NAME1+' '+NAME2 from {$this->MAuth->getdb('CUSTMAST')} CM where CM.CUSCOD=A.CUSCOD) as CUSNAME
				,A.RESVNO,A.LOCAT,convert(varchar(8),A.RESVDT,112) as RESVDT
				,convert(varchar(8),A.RECVDUE,112) as RECVDUE
				,A.GRPCOD,A.TYPE,A.BAAB,A.MODEL,A.COLOR,A.CC,A.STAT
				,A.SALCOD,A.VATRT,A.PRICE,A.RESPAY,A.BALANCE,A.SMPAY,A.SMCHQ,A.STRNO,A.ISSUNO
				,A.RECVDT,A.RECVCD,A.SDATE,A.TAXNO,A.TAXDT,A.MEMO1,A.REQNO,A.REQLOCAT,A.POSTDT,A.INPDT,A.USERID
				,A.GRPCOD
			FROM {$this->MAuth->getdb('ARRESV')} A
			WHERE 1=1 ".$cond."
			ORDER BY A.RESVNO
		";
		$query = $this->db->query($sql);
		
		$html = "";
		$NRow = 1;		
		if($query->row()){
			foreach($query->result() as $row){
				$html .= "
					<div style='line-height:22px;font-size:8pt;border-bottom:1px dotted black;'>
						<div class='tl' style='width:50px;line-height:20px;float:left;'><br>".$row->LOCAT."<br></div>
						<div class='tl' style='width:100px;line-height:20px;float:left;'>".$row->RESVNO."<br>".$this->Convertdate(2,$row->RESVDT)."<br>".$this->Convertdate(2,$row->RECVDUE)."</div>
						<div class='tl' style='width:200px;line-height:20px;float:left;'>".$row->CUSCOD."<br>".$row->CUSNAME."</div>
						<div class='tl' style='width:170px;line-height:20px;float:left;'>".$row->STRNO."<br>".$row->GRPCOD."</div>
						<div class='tl' style='width:150px;line-height:20px;float:left;'>".$row->MODEL."<br>".$row->COLOR."</div>
						<div class='tl' style='width:80px;line-height:20px;float:left;'>".$row->BAAB."<br>".$row->CC."</div>
						<div class='tr' style='width:110px;line-height:20px;float:left;'>".number_format($row->PRICE,2)."<br>".number_format($row->RESPAY,2)."<br>".number_format($row->BALANCE,2)."</div>
						<div class='tc' style='width:160px;line-height:20px;float:left;'><br>".$row->SALCOD."<br></div>
						
					</div>
				";
				$NRow++;
			}
		}
		
		$head = "
			<div style='line-height:24px;'>
				<div class='wf tc f14'><b>{$company}</b></div>
				<div class='wf tc'><b>เงื่อนไข {$condDesc}</b></div>
				<div class='wf'><hr></div>
				<div class='tc' style='width:50px;line-height:20px;float:left;'><b><br>สาขา<br></b></div>
				<div class='tc' style='width:100px;line-height:20px;float:left;'><b>เลขที่บิลจอง<br>วันที่จอง<br>วันที่นัดรับรถ</b></div>
				<div class='tc' style='width:200px;line-height:20px;float:left;'><b>รหัสลูกค้า<br>ชื่อ-สกุล</b></div>
				<div class='tc' style='width:170px;line-height:20px;float:left;'><b>เลขตัวถัง<br>กลุ่มรถ</b></div>
				<div class='tc' style='width:150px;line-height:20px;float:left;'><b>รุ่น<br>สี</b></div>
				<div class='tc' style='width:80px;line-height:20px;float:left;'><b>แบบ<br>ขนาด</b></div>
				<div class='tc' style='width:110px;line-height:20px;float:left;'><b>ราคารถ<br>จอง<br>คงเหลือ</b></div>
				<div class='tc' style='width:160px;line-height:20px;float:left;'><b><br>พนักงานขาย<br></b></div>
				<div class='wf'><hr></div>
			</div>
		";
		
		try {
			$mpdf = new \Mpdf\Mpdf([
				'mode' => 'utf-8',
				'format' => 'A4-L',
				'margin_top' => 48, 	//default = 16
				'margin_left' => 10, 	//default = 15
				'margin_right' => 10, 	//default = 15
				'margin_bottom' => 16, 	//default = 16
				'margin_header' => 9, 	//default = 9
				'margin_footer' => 2, 	//default = 9
			]);

			$stylesheet = "
				<style>
					body { font-family: garuda;font-size:10pt; }
					.wf { width:100%; }
					.f14 { font-size:14pt; }
					.h10 { height:10px; }
					.tc { text-align:center; }
					.tl { text-align:left; }
					.tr { text-align:right; }
					.pf { position:fixed; }
					.bor { border:0.5px solid black; }
					.bor2 { border:0.1px dotted black; }
				</style>
			";
			$content = $html.$stylesheet;
			
			$mpdf->SetHTMLHeader($head);
			$mpdf->SetHTMLFooter("
				<div class='wf pf' style='top:720;font-size:6pt;text-align:right;'>พิมพ์โดย :: ".$this->sess["name"]." ".date('d/m/').(date('Y')+543)." ".date('H:i')." หน้า {PAGENO} / {nbpg}</div>
			");
			$mpdf->WriteHTML($content);
				
			$mpdf->Output();
		} catch (Exception $e) {
			die('Error loading file "' . pathinfo($file, PATHINFO_BASENAME). '": ' . $e->getMessage());
		}
	}
	
}




















