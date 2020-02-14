<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/********************************************************
             ______@30/01/2020______
			 Pasakorn Boonded

********************************************************/
class ReportCancelPY extends MY_Controller {
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
							<br>รายงานยกเลิกการรับชำระ<br>
						</div>
						<div class='col-sm-10 col-xs-10 col-sm-offset-1 text-primary'>	
							<br>
							<div class='col-sm-3'>	
								<div class='form-group'>
									รหัสสาขารับชำระ
									<select id='LOCATRECV' class='form-control input-sm' data-placeholder='สาขา'></select>
								</div>
							</div>
							<div class='col-sm-3'>	
								<div class='form-group'>
									Userid
									<input type='text' id='USERID' class='form-control input-sm'>
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
						</div>
						<div class='col-sm-10 col-xs-10 col-sm-offset-1'><br>	
							<div class='col-sm-12 col-xs-12'>	
								<div class='form-group'>
									เรียงลำดับข้อมูลตาม <br>
									<div class='col-sm-12 col-xs-12' style='border:0.1px dotted #d6d6d6;'>	
										<div class='col-sm-12 col-sm-offset-1'>
											<div class='col-sm-6'>
												<div class='form-group'>
													<br>
													<label>
														<input type= 'radio' id='OR1' name='order' checked> รหัสสาขา
													</label>
												</div>
											</div>
											<div class='col-sm-6'>
												<div class='form-group'>
													<br>
													<label>
														<input type= 'radio' id='OR2' name='order'> วันที่บันทึก
													</label>
												</div>
											</div>
											<div class='col-sm-6'>
												<div class='form-group'>
													<br>
													<label>
														<input type= 'radio' id='OR3' name='order'> เลขที่เช็ค
													</label>
												</div>
											</div>
											<div class='col-sm-6'>
												<div class='form-group'>
													<br>
													<label>
														<input type= 'radio' id='OR4' name='order'> วันที่รับชำระ
													</label>
												</div>
											</div>
										</div>	
									</div>
								</div>
								<div class='col-sm-12 col-xs-12'>
									<br>
									<button id='btnreportPY' type='button' class='btn btn-info btn-outline btn-block' style='width:100%'><span class='fa fa-folder-open'><b>แสดง</b></span></button>
								</div><br>
							</div>
						</div>
					</div>
				</div>
			</div>
		";
		$html .="<script src='".base_url('public/js/SYS06/ReportCancelPY.js')."'></script>";
		echo $html;
	}
	function conditiontopdf(){
		$data = array();
		$data[] = urlencode($_REQUEST['LOCATRECV'].'||'.$_REQUEST['USERID'].'||'.$_REQUEST['DATE1'].'||'.$_REQUEST['DATE2']
		.'||'.$_REQUEST['order']);
		echo json_encode($this->generateData($data,"encode"));
	}
	function pdf(){
		$data = array();
		$data[] = $_REQUEST["condpdf"];
		$arrs = $this->generateData($data,"decode");
		$arrs[0] = urldecode($arrs[0]);
		
		$tx = explode('||',$arrs[0]);
		$LOCATRECV = $tx[0];
		$USERID    = $tx[1];
		$DATE1     = $this->Convertdate(1,$tx[2]);
		$DATE2     = $this->Convertdate(1,$tx[3]);
		$order     = $tx[4];
		
		$sql = "
			select LOCATCD,LOCATNM from INVLOCAT where LOCATCD = '".$LOCATRECV."'
		";
		$query1 = $this->db->query($sql);
		$locatnm = "";
		if($query1->row()){
			foreach($query1->result() as $row){
				$locatnm = $row->LOCATNM;
			}
		}
		$sql = "
			IF OBJECT_ID('tempdb..#RPY') IS NOT NULL DROP TABLE #RPY
			select LOCATRECV,TMBILL,TMBILDT,PAYTYP,BILLNO,BILLDT,CHQNO
				,CHQBK,CHQDT,CHQAMT,INPDT,CANDT,CANID,CANTIME,CUSCOD,CUSNAME
			into #RPY
			FROM(
				select A.LOCATRECV,A.TMBILL,convert(varchar(8),A.TMBILDT,112) as TMBILDT
				,A.PAYTYP,A.BILLNO,convert(varchar(8),A.BILLDT,112) as BILLDT,A.CHQNO
				,A.CHQBK,convert(varchar(8),A.CHQDT,112) as CHQDT,A.CHQAMT
				,convert(varchar(8),A.INPDT,112) as INPDT,convert(varchar(8),A.CANDT,112) as CANDT
				,A.CANID,convert(varchar(8),A.CANTIME,114) as CANTIME,A.CUSCOD
				,B.SNAM+B.NAME1+' '+B.NAME2 as CUSNAME from {$this->MAuth->getdb('CHQMAS')} A 
				left join {$this->MAuth->getdb('CUSTMAST')} B on A.CUSCOD = B.CUSCOD 
				where A.FLAG = 'C' and A.CANDT BETWEEN '".$DATE1."' and '".$DATE2."'
				and A.LOCATRECV like '%".$LOCATRECV."%' and A.CANID like '%".$USERID."%' 
			)RPY
		";
		//echo $sql; exit;
		$query2 = $this->db->query($sql);
		$sql = "
			select * from #RPY order by ".$order."
		";
		$query3 = $this->db->query($sql);
		$sql = "
			select COUNT(TMBILL) as countTMBILL,SUM(PAYAMT) as sumPAYAMT,SUM(DISCT) as sumDISCT
			,SUM(PAYINT) as sumPAYINT,SUM(DSCINT) as sumDSCINT,SUM(NETPAY) as sumNETPAY
			from CHQTRAN where TMBILL in (select TMBILL from #RPY)
		";
		$query4 = $this->db->query($sql);
		
		$head = ""; $html = ""; $i = 0;
		$head = "
			<tr class='wm'>
				<td class='wf' style='height:1px;border-top:0.1px solid black;' colspan='17'></td>
			</tr>
			<tr>
				<th style='border-bottom:0.1px solid black;text-align:left;'>ลำดับที่</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>สาขาที่ได้รับ</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>เลขที่ใบรับ</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>วันที่ใบรับเงิน<br>ชำระค่า</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>เลขที่ใบเสร็จ<br>เลขที่สัญญา</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>วันที่ใบเสร็จ<br>บัญชีสาขา</th>
				<th style='border-bottom:0.1px solid black;text-align:left;' colspan='2'>ชื่อ - สกุล ลูกค้า</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ชำระโดย</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>เลขที่เช็ค</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>เช็คธนาคาร<br>จำนวนเงิน</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>เช็คลงวันที่<br>ส่วนลด</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>จำนวนเงิน<br>เบี้ยปรับ</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>วันที่ลงบันทึก<br>ส่วนเบี้ยปรับ</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'colspan='2'>วันที่ยกเลิก<br>รับสุทธิ</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>รหัสผู้ยกเลิก</th>
			</tr>
			<tr>
				<td class='wf' style='height:1px;border-top:0.1px solid black;' colspan='17'></td>
			</tr>
		";
		if($query3->row()){
			foreach($query3->result() as $row){$i++;
				$sql1 = "
					select TMBILL,PAYFOR,CONTNO,LOCATPAY,PAYAMT
					,DISCT,PAYINT,DSCINT,NETPAY,USERID from {$this->MAuth->getdb('CHQTRAN')} 
					where TMBILL ='{$row->TMBILL}'
				";
				$query1 = $this->db->query($sql1);
				$html2 = "";
				if($query1->row()){
					foreach($query1->result() as $row1){
						$html2 .="
							<tr>
								<td style='width:20px;text-align:left;'></td>
								<td style='width:50px;text-align:left;'></td>
								<td style='width:80px;text-align:left;'></td>
								<td style='width:70px;text-align:left;'>".$row1->PAYFOR."</td>
								<td style='width:80px;text-align:left;'>".$row1->CONTNO."</td>
								<td style='width:50px;text-align:left;'>".$row1->LOCATPAY."</td>
								<td style='width:160px;text-align:left;' colspan='2'></td>
								<td style='width:50px;text-align:right;'></td>
								<td style='width:70px;text-align:right;'></td>
								<td style='width:50px;text-align:right;'>".number_format($row1->PAYAMT,2)."</td>
								<td style='width:60px;text-align:right;'>".number_format($row1->DISCT,2)."</td>
								<td style='width:60px;text-align:right;'>".number_format($row1->PAYINT,2)."</td>
								<td style='width:50px;text-align:right;'>".number_format($row1->DSCINT,2)."</td>
								<td style='width:140px;text-align:right;' colspan='2'>".number_format($row1->NETPAY,2)."</td>
								<td style='width:50px;text-align:right;'></td>
							</tr>
						";
					}
				}
				$html .="
					<tr>
						<td style='width:70px;text-align:left;'>".$i."</td>
						<td style='width:70px;text-align:left;'>".$row->LOCATRECV."</td>
						<td style='width:120px;text-align:left;'>".$row->TMBILL."</td>
						<td style='width:70px;text-align:left;'>".$this->Convertdate(2,$row->TMBILDT)."</td>
						<td style='width:70px;text-align:left;'>".$row->BILLNO."</td>
						<td style='width:70px;text-align:left;'>".$this->Convertdate(2,$row->BILLDT)."</td>
						<td style='width:70px;text-align:left;' colspan='2'>".$row->CUSNAME."</td>
						<td style='width:70px;text-align:right;'>".$row->PAYTYP."</td>
						<td style='width:70px;text-align:right;'>".$row->CHQNO."</td>
						<td style='width:70px;text-align:right;'>".$row->CHQBK."</td>
						<td style='width:70px;text-align:right;'>".$this->Convertdate(2,$row->CHQDT)."</td>
						<td style='width:70px;text-align:right;'>".number_format($row->CHQAMT,2)."</td>
						<td style='width:70px;text-align:right;'>".$this->Convertdate(2,$row->INPDT)."</td>
						<td style='width:160px;text-align:right;'colspan='2'>".$this->Convertdate(2,$row->CANDT)." ".$row->CANTIME."</td>
						<td style='width:70px;text-align:right;'>".$row->CANID."</td>
					</tr>
					".$html2."
				";
			}
		}
		if($query4->row()){
			foreach($query4->result() as $row){
				$html .="
					<tr class='wm'>
						<td class='wf' style='height:1px;border-top:0.1px solid black;' colspan='17'></td>
					</tr>
					<tr>
						<td style='width:70px;text-align:center;' colspan='4'><b>รวมทั้งสิ้น</b></td>
						<td style='width:70px;text-align:center;'>".$row->countTMBILL."</td>
						<td style='width:70px;text-align:right;'><b>รายการ</b></td>
						<td style='width:70px;text-align:right;' colspan='5'>".number_format($row->sumPAYAMT,2)."</td>
						<td style='width:70px;text-align:right;'>".number_format($row->sumDISCT,2)."</td>
						<td style='width:70px;text-align:right;'>".number_format($row->sumPAYINT,2)."</td>
						<td style='width:70px;text-align:right;'>".number_format($row->sumDSCINT,2)."</td>
						<td style='width:70px;text-align:right;'>".number_format($row->sumNETPAY,2)."</td>
					</tr>
					<tr class='wm'>
						<td class='wf' style='height:1px;border-top:0.1px solid black;' colspan='17'></td>
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
							<th colspan='17' style='font-size:10pt;'>บริษัท ตั้งใจพัฒนายานยนต์ จำกัด</th>
						</tr>
						<tr>
							<th colspan='17' style='font-size:9pt;'>รายงานการยกเลิกการรับชำระ</th>
						</tr>
						<tr>
							<td style='text-align:center;' colspan='17'>
								<b>สาขา</b> &nbsp;&nbsp;".$locatnm."&nbsp;&nbsp;
								<b>ระหว่างวันที่</b>&nbsp;&nbsp;".$this->Convertdate(2,$DATE1)."&nbsp;&nbsp;
								<b>ถึงวันที่</b>&nbsp;&nbsp;".$this->Convertdate(2,$DATE2)."&nbsp;&nbsp;
							</td>
						</tr>
						<tr>
							<td style='text-align:right;' colspan='17'>RepCan 10,11</td>
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