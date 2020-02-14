<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/********************************************************
             ______@31/01/2020______
			 Pasakorn Boonded

********************************************************/
class ReportCheckST extends MY_Controller {
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
				<div class='col-sm-12 col-xs-12' style='height:100%;overflow:auto;font-size:11pt;'>					
					<div class='row' style='height:90%;'>
						<div class='col-sm-12 col-xs-12' style='background-color:#808b96;border:5px solid white;height:75px;text-align:center;font-size:12pt;color:white;font-weight:bold;'>	
							<br>รายงานเช็ค<br>
						</div>
						<div class='col-sm-10 col-xs-10 col-sm-offset-1 text-primary'>	
							<br>
							<div class='col-sm-2'>	
								<div class='form-group'>
									รหัสสาขารับชำระ
									<select id='LOCATRECV' class='form-control input-sm' data-placeholder='สาขา'></select>
								</div>
							</div>
							<div class='col-sm-4'>	
								<div class='form-group'>
									เลขที่บัญชีนำฝาก
									<select id='BKCODE' class='form-control input-sm' data-placeholder='เลขที่บัญชี'></select>
								</div>
							</div>
							<div class='col-sm-3'>	
								<div class='form-group'>
									จากวันที่ยกเลิก
									<input type='text' id='DATE1' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' value='".$this->today('today')."' style='font-size:10.5pt'>
								</div>
							</div>
							<div class='col-sm-3'>	
								<div class='form-group'>
									ถึงวันที่
									<input type='text' id='DATE2' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' value='".$this->today('today')."' style='font-size:10.5pt'>
								</div>
							</div>
							<div class='col-sm-3'>	
								<div class='form-group'>
									Userid
									<input type='text' id='USERID' class='form-control input-sm'>
								</div>
							</div>
						</div>
						<div class='col-sm-10 col-xs-10 col-sm-offset-1'><br>	
							<div class='col-sm-8 col-xs-8'>	
								<div class='form-group'>
									ข้อมูลรายงาน
									<div class='col-sm-12 col-xs-12' style='border:0.1px dotted #d6d6d6;'>	
										<div class='col-sm-4'>
											<div class='form-group'>
												<br>
												<label>
													<input type= 'radio' id='DR1' name='data'> เช็ค On Hand
												</label>
											</div>
										</div>
										<div class='col-sm-4'>
											<div class='form-group'>
												<br>
												<label>
													<input type= 'radio' id='DR2' name='data'> เช็คผ่านแล้ว
												</label>
											</div>
										</div>
										<div class='col-sm-4'>
											<div class='form-group'>
												<br>
												<label>
													<input type= 'radio' id='DR3' name='data' checked> ทั้งหมด
												</label>
											</div>
										</div>
										<div class='col-sm-4'>
											<div class='form-group'>
												<br>
												<label>
													<input type= 'radio' id='DR4' name='data'> เช็คนำฝาก
												</label>
											</div>
										</div>
										<div class='col-sm-4'>
											<div class='form-group'>
												<br>
												<label>
													<input type= 'radio' id='DR5' name='data'> เช็คคืน
												</label>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class='col-sm-4 col-xs-4'>	
								<div class='form-group'>
									การเรียงในรายงาน
									<div class='col-sm-12 col-xs-12' style='border:0.1px dotted #d6d6d6;'>	
										<div class='col-sm-12'>
											<div class='form-group'>
												<br>
												<label>
													<input type= 'radio' id='D1' name='date' checked> วันที่รับ
												</label>
											</div>
										</div>
										<div class='col-sm-12'>
											<div class='form-group'>
												<br>
												<label>
													<input type= 'radio' id='D2' name='date'> วันที่เช็ค
												</label>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class='col-sm-12 col-xs-12'>
								<br>
								<button id='btnreportST' type='button' class='btn btn-info btn-outline btn-block' style='width:100%'><span class='fa fa-folder-open'><b>แสดง</b></span></button>
							</div><br>
						</div>
					</div>
				</div>
			</div>
		";
		$html .="<script src='".base_url('public/js/SYS06/ReportCheckST.js')."'></script>";
		echo $html;
	}
	function conditiontopdf(){
		$data = array();
		$data[] = urlencode($_REQUEST['LOCATRECV'].'||'.$_REQUEST['BKCODE'].'||'.$_REQUEST['DATE1'].'||'.$_REQUEST['DATE2']
		.'||'.$_REQUEST['USERID'].'||'.$_REQUEST['datareport'].'||'.$_REQUEST['order']);
		echo json_encode($this->generateData($data,"encode"));
	}
	function pdf(){
		$data = array();
		$data[] = $_REQUEST["condpdf"];
		$arrs = $this->generateData($data,"decode");
		$arrs[0] = urldecode($arrs[0]);
		
		$tx = explode('||',$arrs[0]);
		$LOCATRECV = $tx[0];
		$BKCODE    = $tx[1];
		$DATE1     = $this->Convertdate(1,$tx[2]);
		$DATE2     = $this->Convertdate(1,$tx[3]);
		$USERID    = $tx[4];
		$datareport= $tx[5];
		$order     = $tx[6];
		
		$sql = "
			select LOCATCD,LOCATNM from INVLOCAT where LOCATCD = '".$LOCATRECV."'
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
			select CHQBR,CHQBK,TMBILL,LOCATRECV,TMBILDT,CHQNO,CHQDT,CHQAMT
				,PAYINDT,PAYDT,RCHQDT,FLAG,USERID,PAYINACC
			into #RST
			FROM(
				select CHQBR,CHQBK,TMBILL,LOCATRECV,CONVERT(varchar(8),TMBILDT,112) as TMBILDT,CHQNO
				,CONVERT(varchar(8),CHQDT,112) as CHQDT,CHQAMT,CONVERT(varchar(8),PAYINDT,112) as PAYINDT
				,CONVERT(varchar(8),PAYDT,112) as PAYDT,RCHQDT,FLAG,USERID,PAYINACC 
				from {$this->MAuth->getdb('CHQMAS')} where PAYTYP = '02' and FLAG <> 'C' 
				and LOCATRECV like '%".$LOCATRECV."%' and CHQDT between '".$DATE1."' and '".$DATE2."' 
				and PAYINACC like '%".$BKCODE."%' AND USERID like '%".$USERID."%'  
				and FLAG like '%".$datareport."%' 
			)RST
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		$sql = "
			select * from #RST order by ".$order."
		";
		$query1 = $this->db->query($sql);
		$sql = "
			select count(TMBILL) as countTMBILL,sum(CHQAMT) as sumCHQAMT from #RST 
		";
		$query2 = $this->db->query($sql);
		$head = ""; $html = ""; $i = 0;
		$head = "
			<tr class='wm'>
				<td class='wf' style='height:1px;border-top:0.1px solid black;' colspan='13'></td>
			</tr>
			<tr>
				<th style='border-bottom:0.1px solid black;text-align:left;'>No.</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>สาขา</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>วันที่รับชำระ</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>เลขที่ใบรับ</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>เลขที่เช็ค</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>วันที่เช็ค</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>เช็คธนาคาร</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>จำนวนเงิน</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>พนักงานเก็บเงิน</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>วันที่ PAYIN</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>วันที่เช็คผ่าน</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>วันที่เช็คคืน</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>สถานะเช็ค</th>
			</tr>
			<tr>
				<td class='wf' style='height:1px;border-top:0.1px solid black;' colspan='13'></td>
			</tr>
		";
		if($query1->row()){
			foreach($query1->result() as $row){$i++;
				$html .="
					<tr>
						<td style='width:70px;text-align:left;'>".$i."</td>
						<td style='width:70px;text-align:left;'>".$row->LOCATRECV."</td>
						<td style='width:70px;text-align:left;'>".$this->Convertdate(2,$row->TMBILDT)."</td>
						<td style='width:70px;text-align:left;'>".$row->TMBILL."</td>
						<td style='width:70px;text-align:left;'>".$row->CHQNO."</td>
						<td style='width:70px;text-align:left;'>".$this->Convertdate(2,$row->CHQDT)."</td>
						<td style='width:70px;text-align:left;'>".$row->CHQBK."".$row->CHQBR."</td>
						<td style='width:70px;text-align:right;'>".number_format($row->CHQAMT,2)."</td>
						<td style='width:70px;text-align:right;'>".$row->USERID."</td>
						<td style='width:70px;text-align:right;'>".$this->Convertdate(2,$row->PAYINDT)."</td>
						<td style='width:70px;text-align:right;'>".$this->Convertdate(2,$row->PAYDT)."</td>
						<td style='width:70px;text-align:right;'>".$row->RCHQDT."</td>
						<td style='width:70px;text-align:right;'>".$row->FLAG."</td>
					</tr>
				";
			}
		}
		if($query2->row()){
			foreach($query2->result() as $row){
				$html .="
					<tr class='wm'>
						<td class='wf' style='height:1px;border-top:0.1px solid black;' colspan='13'></td>
					</tr>
					<tr>
						<td style='width:70px;text-align:center;' colspan='3'><b>รวมทั้งสิ้น</b></td>
						<td style='width:70px;text-align:center;'>".$row->countTMBILL."</td>
						<td style='width:70px;text-align:right;'><b>รายการ</b></td>
						<td style='width:70px;text-align:right;' colspan='3'>".number_format($row->sumCHQAMT,2)."</td>
					</tr>
					<tr class='wm'>
						<td class='wf' style='height:1px;border-top:0.1px solid black;' colspan='13'></td>
					</tr>
					<tr class='wm'>
						<td style='width:70px;text-align:left;' colspan='13'><b>สถานะเช็ค : </b>&nbsp;&nbsp; H : เช็ค OnHard , B : เช็คนำฝาก , P : เช็คผ่าน , R : เช็คคืน</td>
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
							<th colspan='13' style='font-size:9pt;'>รายงานเช็ค</th>
						</tr>
						<tr>
							<td style='text-align:center;' colspan='13'>
								<b>สาขา</b> &nbsp;&nbsp;".$locatnm."&nbsp;&nbsp;
								<b>ระหว่างวันที่</b>&nbsp;&nbsp;".$DATE1."&nbsp;&nbsp;
								<b>ถึงวันที่</b>&nbsp;&nbsp;".$DATE2."&nbsp;&nbsp;
								<b>เลขบัญชีที่นำฝาก</b>&nbsp;&nbsp;".$BKCODE."&nbsp;&nbsp;
								<b>พนักงานเก็บเงิน</b>&nbsp;&nbsp;".$USERID."&nbsp;&nbsp;
							</td>
						</tr>
						<tr>
							<td style='text-align:right;' colspan='13'>RepChg 10,11</td>
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