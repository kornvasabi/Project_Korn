<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/********************************************************
             ______@31/02/2020______
			 Pasakorn Boonded

********************************************************/
class ReportStopVat_Pay extends MY_Controller {
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
							<br>รายงานลูกหนี้หยุด Vat ที่มาชำระต่อ<br>
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
									BillColl
									<select id='BILLCOLL' class='form-control input-sm'></select>
								</div>
							</div>
						</div>
						<div class='col-sm-10 col-xs-10 col-sm-offset-1'><br>	
							<div class='col-sm-12 col-xs-12'>	
								<div class='form-group'>
									เรียงลำดับข้อมูล<br>
									<div class='col-sm-12 col-xs-12' style='border:0.1px dotted #d6d6d6;'>	
										<div class='col-sm-12 col-sm-offset-1'>
											<div class='col-sm-3'>
												<div class='form-group'>
													<br>
													<label>
														<input type= 'radio' id='or1' name='order' checked> เลขที่สัญญา
													</label>
												</div>
											</div>
											<div class='col-sm-3'>
												<div class='form-group'>
													<br>
													<label>
														<input type= 'radio' id='or2' name='order'> BillColl
													</label>
												</div>
											</div>
											<div class='col-sm-3'>
												<div class='form-group'>
													<br>
													<label>
														<input type= 'radio' id='or3' name='order'> วันที่หยุด Vat
													</label>
												</div>
											</div>
											<div class='col-sm-3'>
												<div class='form-group'>
													<br>
													<label>
														<input type= 'radio' id='or4' name='order'> เลขตัวถัง
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
		$html .="<script src='".base_url('public/js/SYS07/ReportStopVat_Pay.js')."'></script>";
		echo $html;
	}
	function conditiontopdf(){
		$data = array();
		$data[] = urlencode($_REQUEST['LOCAT'].'||'.$_REQUEST['BILLCOLL'].'||'.$_REQUEST['order']);
		echo json_encode($this->generateData($data,"encode"));
	}
	function pdf(){
		$data = array();
		$data[] = $_REQUEST["condpdf"];
		$arrs = $this->generateData($data,"decode");
		$arrs[0] = urldecode($arrs[0]);
		$tx = explode('||',$arrs[0]);
		$LOCAT       = $tx[0];
		$BILLCOLL    = $tx[1];
		$order       = $tx[2];
		$tocontno = "";
		
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
			IF OBJECT_ID('tempdb..#RST') IS NOT NULL DROP TABLE #RST
			select LOCAT,CONTNO,CUSCOD,CUSNAM+'('+CUSCOD+')' as CUSNAM,SDATE,TOTPRC
				,LPAYA,EXP_PRD,DTSTOPV,STRNO,LPAYD,VAR2,BILLCOLL
			into #RST
			FROM(
				select A.LOCAT,A.CONTNO,A.CUSCOD,C.SNAM+C.NAME1+' '+C.NAME2 as CUSNAM,CONVERT(varchar(8),A.SDATE,112) as SDATE,A.TOTPRC
				,A.LPAYA,A.EXP_PRD,convert(varchar(8),A.DTSTOPV,112) as DTSTOPV,A.STRNO,CONVERT(varchar(8),A.LPAYD,112) as LPAYD
				,A.TOTPRC-A.SMPAY-A.SMCHQ AS VAR2,A.BILLCOLL from {$this->MAuth->getdb('ARMAST')} A
				left join {$this->MAuth->getdb('CUSTMAST')} C on A.CUSCOD = C.CUSCOD
				where A.LOCAT like '%".$LOCAT."%' and A.BILLCOLL like '%".$BILLCOLL."%' and A.FLSTOPV = 'S' and A.DTSTOPV <= A.LPAYD
			)RST
		";
		//echo $sql; exit;
		$this->db->query($sql);
		
		$sql = "
			select * from #RST  order by ".$order."
		";
		$query = $this->db->query($sql);
		
		$sql2 = "
			select sum(TOTPRC) as sumTOTPRC,sum(VAR2) as sumVAR2 
			,sum(LPAYA) as sumLPAYA from #RST 
		";
		$query2 = $this->db->query($sql2);
		
		$head = ""; $html = ""; $i = 0;
		$head = "
			<tr class='wm'>
				<td class='wf' style='height:1px;border-top:0.1px solid black;' colspan='13'></td>
			</tr>
			<tr>
				<th style='border-bottom:0.1px solid black;text-align:left;'>สาขา</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>เลขที่สัญญา</th>
				<th style='border-bottom:0.1px solid black;text-align:left;' colspan='2'>ชื่อ - นามสกุล</th>
				<th style='border-bottom:0.1px solid black;text-align:left;' colspan='2'>เลขถัง</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>วันที่ขาย</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ราคาขาย</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ยอดคงเหลือ</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>วันที่หยุด Vat</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>วันชำระล่าสุด</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>จำนวนเงินที่ชำระ</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>จำนวนงวดที่ค้าง</th>
			</tr>
			<tr>
				<td class='wf' style='height:1px;border-top:0.1px solid black;' colspan='13'></td>
			</tr>
		";
		
		if($query->row()){
			foreach($query->result() as $row){$i++;
				$html .="
					<tr>
						<td style='width:70px;text-align:left;'>".$row->LOCAT."</td>
						<td style='width:90px;text-align:left;'>".$row->CONTNO."</td>
						<td style='width:200px;text-align:left;' colspan='2'>".$row->CUSNAM."</td>
						<td style='width:130px;text-align:left;' colspan='2'>".$row->STRNO."</td>
						<td style='width:80px;text-align:left;'>".$this->Convertdate(2,$row->SDATE)."</td>
						<td style='width:80px;text-align:right;'>".$row->TOTPRC."</td>
						<td style='width:80px;text-align:right;'>".number_format($row->VAR2,2)."</td>
						<td style='width:80px;text-align:right;'>".$this->Convertdate(2,$row->DTSTOPV)."</td>
						<td style='width:80px;text-align:right;'>".$this->Convertdate(2,$row->LPAYD)."</td>
						<td style='width:100px;text-align:right;'>".number_format($row->LPAYA,2)."</td>
						<td style='width:100px;text-align:right;'>".$row->EXP_PRD."</td>
					</tr>
				";
			}
		}
		if($query2->row()){
			foreach($query2->result() as $row2){
				$html .="
					<tr class='wm'>
						<td class='wf' style='height:1px;border-top:0.1px solid black;' colspan='13'></td>
					</tr>
					<tr>
						<td style='width:70px;text-align:center;' colspan='2'><b>รวมทั้งสิ้น</b></td>
						<td style='width:70px;text-align:center;'>".$i."</td>
						<td style='width:70px;text-align:right;'><b>รายการ</b></td>
						<td style='width:70px;text-align:right;' colspan='2'>เป็นเงิน -   -   -   -   -   -   -   -   -   -   ></td>
						<td style='width:70px;text-align:right;' colspan='2'>".$row2->sumTOTPRC."</td>
						<td style='width:70px;text-align:right;' colspan=''>".$row2->sumVAR2."</td>
						<td style='width:70px;text-align:right;' colspan='3'>".$row2->sumLPAYA."</td>
					</tr>
					<tr class='wm'>
						<td class='wf' style='height:1px;border-top:0.1px solid black;' colspan='13'></td>
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
							<th colspan='13' style='font-size:10pt;'>บริษัท ตั้งใจพัฒนายานยนต์ จำกัด</th>
						</tr>
						<tr>
							<th colspan='13' style='font-size:9pt;'>รายงานลูกหนี้หยุด Vat ที่มาชำระต่อ</th>
						</tr>
						<tr>
							<td style='text-align:center;' colspan='13'>
								<b>สาขา</b> &nbsp;&nbsp;".$locatnm."&nbsp;&nbsp;
								<b>BILLCOLL</b> &nbsp;&nbsp;".$BILLCOLL."&nbsp;&nbsp;
							</td>
						</tr>
						<tr>
							<td style='text-align:right;' colspan='13'>RpStpV21</td>
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
}