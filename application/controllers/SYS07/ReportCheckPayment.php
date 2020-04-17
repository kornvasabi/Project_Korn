<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/********************************************************
             ______@31/02/2020______
			 Pasakorn Boonded

********************************************************/
class ReportCheckPayment extends MY_Controller {
	private $sess = array(); 
	
	function __construct(){
		parent::__construct();
		//Additional code which you want to run automatically in every function call 
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
				<div class='col-sm-12 col-xs-12' style='height:100%;overflow:auto;font-size:11pt;'>					
					<div class='row' style='height:90%;'>
						<div class='col-sm-12 col-xs-12' style='background-color:#45b39d;border:5px solid white;height:75px;text-align:center;font-size:12pt;color:white;font-weight:bold;'>	
							<br>รายงานการตรวจสอบค่างวดที่ยังไม่ออกใบกำกับภาษี<br>
						</div>
						<div class='col-sm-10 col-xs-10 col-sm-offset-1 text-primary'>	
							<br>
							<div class='col-sm-6'>	
								<div class='form-group'>
									รหัสสาขา
									<select id='LOCAT' class='form-control input-sm'></select>
								</div>
							</div>
							<div class='col-sm-6'>	
								<div class='form-group'>
									รหัสลูกค้า
									<select id='CUSCOD' class='form-control input-sm'></select>
								</div>
							</div>
							<div class='col-sm-6'>	
								<div class='form-group'>
									เลขที่สัญญา
									<select id='CONTNO' class='form-control input-sm'></select>
								</div>
							</div>
							<div class='col-sm-6'>	
								<div class='form-group'>
									ลน. ครบกำหนดก่อนวันที่
									<input type='text' id='DATE' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' value='".$this->today('today')."' styl='font-size:10.5pt;'>
								</div>
							</div>
						</div>
						<div class='col-sm-10 col-xs-10 col-sm-offset-1'><br>	
							<div class='col-sm-12 col-xs-12'>	
								<div class='form-group'>
									เรียงลำดับข้อมูล<br>
									<div class='col-sm-12 col-xs-12' style='border:0.1px dotted #d6d6d6;'>	
										<div class='col-sm-12 col-sm-offset-1'>
											<div class='col-sm-6'>
												<div class='form-group'>
													<br>
													<label>
														<input type= 'radio' id='or1' name='order' checked> ตามรหัสลูกค้า
													</label>
												</div>
											</div>
											<div class='col-sm-6'>
												<div class='form-group'>
													<br>
													<label>
														<input type= 'radio' id='or2' name='order'> ตามเลขที่สัญญา
													</label>
												</div>
											</div>
										</div>	
									</div>
								</div>
								<div class='col-sm-12 col-xs-12'>
									<br>
									<button id='btnReportVat' type='button' class='btn btn-info btn-outline btn-block' style='width:100%'><span class='fa fa-folder-open'><b>แสดง</b></span></button>
								</div><br>
							</div>
						</div>
					</div>
				</div>
			</div>
		";
		$html .="<script src='".base_url('public/js/SYS07/ReportCheckPayment.js')."'></script>";
		echo $html;
	}
	function conditiontopdf(){
		$data = array();
		$data[] = urlencode($_REQUEST['LOCAT'].'||'.$_REQUEST['CUSCOD'].'||'.$_REQUEST['CONTNO']
		.'||'.$_REQUEST['DATE'].'||'.$_REQUEST['order']);
		echo json_encode($this->generateData($data,"encode"));
	}
	function pdf(){
		$data = array();
		$data[] = $_REQUEST["condpdf"];
		$arrs = $this->generateData($data,"decode");
		$arrs[0] = urldecode($arrs[0]);
		$tx = explode('||',$arrs[0]);
		//print_r ($tx); exit;
		$LOCAT       = $tx[0];
		$CUSCOD 	 = $tx[1];
		$CONTNO  	 = $tx[2];
		$DATE        = $this->Convertdate(1,$tx[3]);
		$order       = $tx[4];
	
		$sql = "
			select LOCATCD,LOCATNM from {$this->MAuth->getdb('INVLOCAT')} where LOCATCD = '".$LOCAT."'
		";
		$query = $this->db->query($sql);
		$locatnm = "";
		if($query->row()){
			foreach($query->result() as $row){
				$locatnm = $row->LOCATNM;
			}
		}
		$sql = "
			IF OBJECT_ID('tempdb..#RCP') IS NOT NULL DROP TABLE #RCP
			select LOCAT,CONTNO,NOPAY,DAMT,V_DAMT,TAXAMT,K_DAMT,VATRT,DDATE,LPAYD
				,CUSCOD,CUSNAME,TAXDT,TAXNO
			into #RCP
			FROM(
				select A.LOCAT,A.CONTNO,A.NOPAY,A.DAMT,A.V_DAMT,A.TAXAMT,A.V_DAMT-A.TAXAMT as K_DAMT,A.VATRT,
				CONVERT(varchar(8),A.DDATE,112) as DDATE,CONVERT(varchar(8),B.LPAYD,112) as LPAYD
				,B.CUSCOD,rtrim(C.SNAM)+rtrim(C.NAME1)+ '  '+rtrim(C.NAME2) as CUSNAME
				,CONVERT(varchar(8),(select max(TAXDT) as LTXDT from {$this->MAuth->getdb('TAXTRAN')} where CONTNO=B.CONTNO and LOCAT=B.LOCAT and CANDT is null),112) as TAXDT
				,(select max(TAXNO) as LTXNO from {$this->MAuth->getdb('TAXTRAN')} where CONTNO=B.CONTNO and LOCAT=B.LOCAT and CANDT IS null) as TAXNO 
				from {$this->MAuth->getdb('ARPAY')} A
				left join {$this->MAuth->getdb('ARMAST')} B on A.CONTNO = B.CONTNO
				left join {$this->MAuth->getdb('CUSTMAST')} C  on C.CUSCOD = B.CUSCOD where  A.LOCAT = B.LOCAT
				and (A.TAXAMT < A.V_DAMT or A.TAXAMT is null) and B.LOCAT like '%".$LOCAT."%' and B.CONTNO like '%".$CONTNO."%'  
				and B.CUSCOD like '%".$CUSCOD."%' and A.DDATE <= '".$DATE."' and (B.FLSTOPV <> 'S' or B.DTSTOPV > '".$DATE."') 
				--order by A.CONTNO,A.LOCAT,A.NOPAY,B.CUSCOD	
			)RCP
		";
		//echo $sql; exit;
		$this->db->query($sql);
		
		$sql = "
			select * from #RCP  order by ".$order."
		";
		$query = $this->db->query($sql);
		$sql2 = "
			select sum(DAMT) as sumDAMT,sum(V_DAMT) as sumV_DAMT,sum(TAXAMT) as sumTAXAMT,sum(K_DAMT) as sumK_DAMT from #RCP 
		";
		$query2 = $this->db->query($sql2);
		$head = ""; $html = ""; $i = 0;
		
		$head = "
			<tr class='wm'>
				<td class='wf' style='height:1px;border-top:0.1px solid black;' colspan='15'></td>
			</tr>
			<tr>
				<th style='border-bottom:0.1px solid black;text-align:left;'>No</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>สาขา</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>เลขที่สัญญา</th>
				<th style='border-bottom:0.1px solid black;text-align:left;' colspan='2'>ชื่อ - นามสกุล</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>งวด</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>อัตราภาษี</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>จำนวนเงิน</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ยอดภาษี</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ยอดใบกำกับ</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ยอดค้างออกใบกำกับ</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>วันดิว</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>วันที่ชำระล่าสุด</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>วันที่ใบกำกับล่าสุด</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>วันที่ใบกำกับล่าสุด</th>
			</tr>
			<tr>
				<td class='wf' style='height:1px;border-top:0.1px solid black;' colspan='15'></td>
			</tr>
		";
		if($query->row()){
			foreach($query->result() as $row){$i++;
				$html .="
					<tr>
						<td style='width:50px; text-align:left;'>".$i."</td>
						<td style='width:50px; text-align:left;'>".$row->LOCAT."</td>
						<td style='width:500px; text-align:left;'>".$row->CONTNO."</td>
						<td style='width:50px; text-align:left;' colspan='2'>".$row->CUSNAME."</td>
						<td style='width:50px; text-align:left;'>".$row->NOPAY."</td>
						<td style='width:50px; text-align:left;'>".number_format($row->VATRT,2)."</td>
						<td style='width:50px; text-align:right;'>".number_format($row->DAMT,2)."</td>
						<td style='width:50px; text-align:right;'>".number_format($row->V_DAMT,2)."</td>
						<td style='width:50px; text-align:right;'>".number_format($row->TAXAMT,2)."</td>
						<td style='width:50px; text-align:right;'>".number_format($row->K_DAMT,2)."</td>
						<td style='width:50px; text-align:right;'>".$this->Convertdate(2,$row->DDATE)."</td>
						<td style='width:50px; text-align:right;'>".$this->Convertdate(2,$row->LPAYD)."</td>
						<td style='width:50px; text-align:right;'>".$this->Convertdate(2,$row->TAXDT)."</td>
						<td style='width:100px; text-align:right;'>".$row->TAXNO."</td>
					</tr>
				";
			}
		}
		if($query2->row()){
			foreach($query2->result() as $row2){
				$html .="
					<tr class='wm'>
						<td class='wf' style='height:1px;border-top:0.1px solid black;' colspan='15'></td>
					</tr>
					<tr>
						<td style='width:70px;text-align:center;' colspan='2'><b>รวมทั้งสิ้น</b></td>
						<td style='width:70px;text-align:center;'>".$i."</td>
						<td style='width:70px;text-align:right;'><b>รายการ</b></td>
						<td style='width:70px;text-align:right;' colspan='3'>เป็นเงิน -   -   -   -   -   -   -   -   -   -   ></td>
						<td style='width:70px;text-align:right;' colspan=''>".number_format($row2->sumDAMT,2)."</td>
						<td style='width:70px;text-align:right;' colspan=''>".number_format($row2->sumV_DAMT,2)."</td>
						<td style='width:70px;text-align:right;' colspan=''>".number_format($row2->sumTAXAMT,2)."</td>
						<td style='width:70px;text-align:right;' colspan=''>".number_format($row2->sumK_DAMT,2)."</td>
					</tr>
					<tr class='wm'>
						<td class='wf' style='height:1px;border-top:0.1px solid black;' colspan='15'></td>
					</tr>
				";
			}
		}
		$mpdf = new \Mpdf\Mpdf([
			'mode' => 'utf-8', 
			'format' => 'A4-L',
			'margin_top' => 10, 	//default = 16
			'margin_left' => 10, 	//default = 15
			'margin_right' => 10, 	//default = 15
			'margin_bottom' => 10, 	//default = 16
			'margin_header' => 9, 	//default = 9
			'margin_footer' => 9, 	//default = 9
		]);
		if($i > 0){
			$content = "
				<table class='wf' style='font-size:8pt;height:700px;border-collapse:collapse;line-height:23px;overflow:wrap;vertical-align:text-top;'>
					<tbody>
						<tr>
							<th colspan='15' style='font-size:10pt;'>บริษัท ตั้งใจพัฒนายานยนต์ จำกัด</th>
						</tr>
						<tr>
							<th colspan='15' style='font-size:9pt;'>รายงานการตรวจสอบค่างวดที่ยังไม่ออกใบกำกับภาษี</th>
						</tr>
						<tr>
							<td style='text-align:center;' colspan='17'>
								<b>สาขา</b> &nbsp;&nbsp;".$locatnm."&nbsp;&nbsp;
								<b>รหัสลูกค้า</b> &nbsp;&nbsp;".$CUSCOD."&nbsp;&nbsp;
								<b>เลขที่สัญญา</b>&nbsp;&nbsp;".$CONTNO."&nbsp;&nbsp;
								<b>ลน. ครบกำหนดก่อนวันที่</b>&nbsp;&nbsp;".$this->Convertdate(2,$DATE)."&nbsp;&nbsp;
							</td>
						</tr>
						<tr>
							<td style='text-align:right;' colspan='15'>RCHK_TAXS</td>
						</tr>
						<br>
						".$head."
						".$html."
					</tbody>
				</table>
			";
			$head = "
				<div class='wf pf' style='top:1060;left:600;top:715;left:880; font-size:6pt;'>วันที่พิมพ์รายงาน : ".date('d/m/').(date('Y')+543)." ".date('H:i')." หน้า {PAGENO} / {nbpg}</div>
			";
		}else{
			$content = "<div style='color:red;'>ไม่พบข้อมูลตามเงื่อนไข</div>";
			$head = "
				<div class='wf pf' style='top:1060;left:600;top:715;left:880; font-size:6pt;'></div>
			";
		}
		$stylesheet = "
			<style>
				body { font-family: garuda;font-size:10pt; }
				.wf { width:100%; }
				.h10 { height:10px; }
				.tc { text-align:center; }
				.pf { position:fixed; }
				.bor { border:0.1px solid black; }
				.bor2 { border:0.1px dotted black; }
			</style>
		";
		$content = $content.$stylesheet;
		$mpdf->SetHTMLHeader($head);	
		$mpdf->WriteHTML($content);	
		$mpdf->Output();
	}
	/*
	function pdf(){
		$data = array();
		$data[] = $_REQUEST["condpdf"];
		$arrs = $this->generateData($data,"decode");
		$arrs[0] = urldecode($arrs[0]);
		$tx = explode('||',$arrs[0]);
		//print_r ($tx); exit;
		$LOCAT       = $tx[0];
		$CUSCOD 	 = $tx[1];
		$CONTNO  	 = $tx[2];
		$DATE        = $this->Convertdate(1,$tx[3]);
		$order       = $tx[4];
	
		$sql = "
			select LOCATCD,LOCATNM from {$this->MAuth->getdb('INVLOCAT')} where LOCATCD = '".$LOCAT."'
		";
		$query = $this->db->query($sql);
		$locatnm = "";
		if($query->row()){
			foreach($query->result() as $row){
				$locatnm = $row->LOCATNM;
			}
		}
		$sql = "
			IF OBJECT_ID('tempdb..#RCP') IS NOT NULL DROP TABLE #RCP
			select LOCAT,CONTNO,NOPAY,DAMT,V_DAMT,TAXAMT,K_DAMT,VATRT,DDATE,LPAYD
				,CUSCOD,CUSNAME,TAXDT,TAXNO
			into #RCP
			FROM(
				select A.LOCAT,A.CONTNO,A.NOPAY,A.DAMT,A.V_DAMT,A.TAXAMT,A.V_DAMT-A.TAXAMT as K_DAMT,A.VATRT,
				CONVERT(varchar(8),A.DDATE,112) as DDATE,CONVERT(varchar(8),B.LPAYD,112) as LPAYD
				,B.CUSCOD,rtrim(C.SNAM)+rtrim(C.NAME1)+ '  '+rtrim(C.NAME2) as CUSNAME
				,CONVERT(varchar(8),(select max(TAXDT) as LTXDT from {$this->MAuth->getdb('TAXTRAN')} where CONTNO=B.CONTNO and LOCAT=B.LOCAT and CANDT is null),112) as TAXDT
				,(select max(TAXNO) as LTXNO from {$this->MAuth->getdb('TAXTRAN')} where CONTNO=B.CONTNO and LOCAT=B.LOCAT and CANDT IS null) as TAXNO 
				from {$this->MAuth->getdb('ARPAY')} A
				left join {$this->MAuth->getdb('ARMAST')} B on A.CONTNO = B.CONTNO
				left join {$this->MAuth->getdb('CUSTMAST')} C  on C.CUSCOD = B.CUSCOD where  A.LOCAT = B.LOCAT
				and (A.TAXAMT < A.V_DAMT or A.TAXAMT is null) and B.LOCAT like '%".$LOCAT."%' and B.CONTNO like '%".$CONTNO."%'  
				and B.CUSCOD like '%".$CUSCOD."%' and A.DDATE <= '".$DATE."' and (B.FLSTOPV <> 'S' or B.DTSTOPV > '".$DATE."') 
				--order by A.CONTNO,A.LOCAT,A.NOPAY,B.CUSCOD	
			)RCP
		";
		//echo $sql; exit;
		$this->db->query($sql);
		
		$sql = "
			select * from #RCP  order by ".$order."
		";
		$query = $this->db->query($sql);
		$sql2 = "
			select sum(TOTPRC) as sumTOTPRC,sum(SMPAY) as sumSMPAY,sum(VAR2) as sumVAR2 
			,sum(EXP_AMT) as sumEXP_AMT from #RST 
		";
		$query2 = $this->db->query($sql2);
		$head = ""; $html = ""; $i = 0;
		
		$head = "
			<tr class='wm'>
				<td class='wf' style='height:1px;border-top:0.1px solid black;' colspan='15'></td>
			</tr>
			<tr>
				<th style='border-bottom:0.1px solid black;text-align:left;'>No</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>สาขา</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>เลขที่สัญญา</th>
				<th style='border-bottom:0.1px solid black;text-align:left;' colspan='2'>ชื่อ - นามสกุล</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>งวด</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>อัตราภาษี</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>จำนวนเงิน</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ยอดภาษี</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ยอดใบกำกับ</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ยอดค้างออกใบกำกับ</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>วันดิว</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>วันที่ชำระล่าสุด</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>วันที่ใบกำกับล่าสุด</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>วันที่ใบกำกับล่าสุด</th>
			</tr>
			<tr>
				<td class='wf' style='height:1px;border-top:0.1px solid black;' colspan='15'></td>
			</tr>
		";
		if($query->row()){
			foreach($query->result() as $row){$i++;
				$html .="
					<tr>
						<td style='width:70px;text-align:left;'>".$i."</td>
						<td style='width:70px;text-align:left;'>".$row->LOCAT."</td>
						<td style='width:70px;text-align:left;'>".$row->CONTNO."</td>
						<td style='width:130px;text-align:left;' colspan='2'>".$row->CUSNAME."</td>
						<td style='width:130px;text-align:left;'>".$row->NOPAY."</td>
						<td style='width:70px;text-align:left;'>".number_format($row->VATRT,2)."</td>
						<td style='width:70px;text-align:right;'>".number_format($row->DAMT,2)."</td>
						<td style='width:70px;text-align:right;'>".number_format($row->V_DAMT,2)."</td>
						<td style='width:70px;text-align:right;'>".number_format($row->TAXAMT,2)."</td>
						<td style='width:70px;text-align:right;'>".number_format($row->K_DAMT,2)."</td>
						<td style='width:70px;text-align:right;'>".$this->Convertdate(2,$row->DDATE)."</td>
						<td style='width:70px;text-align:right;'>".$this->Convertdate(2,$row->LPAYD)."</td>
						<td style='width:70px;text-align:right;'>".$this->Convertdate(2,$row->TAXDT)."</td>
						<td style='width:70px;text-align:right;'>".$row->TAXNO."</td>
					</tr>
				";
			}
		}
		if($query2->row()){
			foreach($query2->result() as $row2){
				$html .="
					<tr class='wm'>
						<td class='wf' style='height:1px;border-top:0.1px solid black;' colspan='15'></td>
					</tr>
					<tr>
						<td style='width:70px;text-align:center;' colspan='3'><b>รวมทั้งสิ้น</b></td>
						<td style='width:70px;text-align:center;'>".$i."</td>
						<td style='width:70px;text-align:right;'><b>รายการ</b></td>
						<td style='width:70px;text-align:right;' colspan='2'>เป็นเงิน -   -   -   -   -   -   -   -   -   -   ></td>
						<td style='width:70px;text-align:right;' colspan='2'>".$row2->sumTOTPRC."</td>
						<td style='width:70px;text-align:right;' colspan='3'>".$row2->sumSMPAY."</td>
						<td style='width:70px;text-align:right;' colspan='2'>".$row2->sumVAR2."</td>
						<td style='width:70px;text-align:right;' colspan='2'>".$row2->sumEXP_AMT."</td>
					</tr>
					<tr class='wm'>
						<td class='wf' style='height:1px;border-top:0.1px solid black;' colspan='15'></td>
					</tr>
				";
			}
		}
		$mpdf = new \Mpdf\Mpdf([
			'mode' => 'utf-8', 
			'format' => 'A4-L',
			'margin_top' => 10, 	//default = 16
			'margin_left' => 10, 	//default = 15
			'margin_right' => 10, 	//default = 15
			'margin_bottom' => 10, 	//default = 16
			'margin_header' => 9, 	//default = 9
			'margin_footer' => 9, 	//default = 9
		]);
		if($i > 0){
			$content = "
				<table class='wf' style='font-size:8pt;height:700px;border-collapse:collapse;line-height:23px;overflow:wrap;vertical-align:text-top;'>
					<tbody>
						".$html."
					</tbody>
				</table>
			";
			$head ="
				<div>
					<table class='wf' style='font-size:8pt;height:700px;border-collapse:collapse;line-height:23px;overflow:wrap;vertical-align:text-top;'>
						<tbody>
							<tr>
								<th colspan='15' style='font-size:10pt;'>บริษัท ตั้งใจพัฒนายานยนต์ จำกัด</th>
							</tr>
							<tr>
								<th colspan='15' style='font-size:9pt;'>รายงานการตรวจสอบค่างวดที่ยังไม่ออกใบกำกับภาษี</th>
							</tr>
							<tr>
								<td style='text-align:center;' colspan='17'>
									<b>สาขา</b> &nbsp;&nbsp;".$locatnm."&nbsp;&nbsp;
									<b>รหัสลูกค้า</b> &nbsp;&nbsp;".$CUSCOD."&nbsp;&nbsp;
									<b>เลขที่สัญญา</b>&nbsp;&nbsp;".$CONTNO."&nbsp;&nbsp;
									<b>ลน. ครบกำหนดก่อนวันที่</b>&nbsp;&nbsp;".$this->Convertdate(2,$DATE)."&nbsp;&nbsp;
								</td>
							</tr>
							<tr>
								<td style='text-align:right;' colspan='15'>RCHK_TAXS</td>
							</tr>
							<br>
							".$head."
						</tbody>
					</table>
				<div>	
			";
			$head .= "
				<div class='wf pf' style='top:1060;left:600;top:715;left:880; font-size:6pt;'>วันที่พิมพ์รายงาน : ".date('d/m/').(date('Y')+543)." ".date('H:i')." หน้า {PAGENO} / {nbpg}</div>
			";
		}else{
			$content = "<div style='color:red;'>ไม่พบข้อมูลตามเงื่อนไข</div>";
			$head = "
				<div class='wf pf' style='top:1060;left:600;top:715;left:880; font-size:6pt;'></div>
			";
		}
		$stylesheet = "
			<style>
				body { font-family: garuda;font-size:10pt; }
				.wf { width:100%; }
				.h10 { height:10px; }
				.tc { text-align:center; }
				.pf { position:fixed; }
				.bor { border:0.1px solid black; }
				.bor2 { border:0.1px dotted black; }
			</style>
		";
		$content = $content.$stylesheet;
		$mpdf->SetHTMLHeader($head);	
		$mpdf->WriteHTML($content);	
		$mpdf->Output();
	}
	*/
}