<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/********************************************************
             ______@27/05/2020______
			 Pasakorn Boonded

********************************************************/
class SYDProfitMoney extends MY_Controller {
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
							<br>รายงานกำไรตามการรับชำระ(SYD)<br>
						</div>
						<div class='col-sm-12'>
							<div class='col-sm-3'>
								<div class='form-group'>
									รหัสสาขา
									<select id='LOCAT' class='form-control input-sm'></select>
								</div>
							</div>
							<div class='col-sm-3'>
								<div class='form-group'>
									เลขที่สัญญา
									<div class='input-group'>
										<input type='text' id='CONTNO' class='form-control input-sm' placeholder='เลขที่สัญญา' >
										<span class='input-group-btn'>
											<button id='btnaddcont' class='btn btn-info btn-sm' type='button'>
												<span class='glyphicon glyphicon-hand-up' aria-hidden='true'></span>
											</button>
										</span>
									</div>
								</div>
							</div>
							<div class='col-sm-3'>
								<div class='form-group'>
									จากวันชำระ
									<input type='text' id='F_DATE' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' value='".$this->today('today')."' placeholder='ถึงวันที่ขาย' >
								</div>
							</div>
							<div class='col-sm-3'>
								<div class='form-group'>
									ถึงวันที่
									<input type='text' id='T_DATE' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' value='".$this->today('today')."' placeholder='ถึงวันที่ขาย' >
								</div>
							</div>
							<div class='col-sm-3'>
								<div class='form-group'>
									พนักงานเก็บเงิน
									<select id='OFFICER' class='form-control input-sm'></select>
								</div>
							</div>
						</div>
						<div class='col-sm-12'>
							<div class='col-sm-10'>
								<b>การเรียงในรายงาน</b>
								<div class='col-sm-12' style='height:100%;width:100%;border:1px dotted #aaa;'>
									<div class='col-sm-3'>
										<div class='form-group'>
											<br>
											<label>
												<input type= 'radio' id='OR1' name='order' checked> รหัสสาขา
											</label>
										</div>
									</div>
									<div class='col-sm-3'>
										<div class='form-group'>
											<br>
											<label>
												<input type= 'radio' id='OR2' name='order'> เลขที่สัญญา
											</label>
										</div>
									</div>
									<div class='col-sm-3'>
										<div class='form-group'>
											<br>
											<label>
												<input type= 'radio' id='OR3' name='order'> รหัสลูกค้า
											</label>
										</div>
									</div>
									<div class='col-sm-3'>
										<div class='form-group'>
											<br>
											<label>
												<input type= 'radio' id='OR4' name='order'> วันที่ใบเสร็จ
											</label>
										</div>
									</div>
								</div>	
							</div>
							<div class='col-sm-2'>
								<br>
								<button id='btnreport' style='width:100%;height:60px;' class='btn btn-cyan btn-sm'><span class='fa fa-file-text'>SCREEN</span></button>
							</div>
						</div>
						<div class='col-sm-12' style='height:100px;'></div>
						<!--div class='col-sm-4'>
							<button id='updateprofit' style='width:100%;' class='btn btn-primary btn-sm'><span class='fa fa-refresh'>ปรับปรุงดอกผล</span></button>
						</div-->
					</div>	
				</div>
			</div>
		";
		$html .="<script src='".base_url('public/js/SYS06/SYDProfitMoney.js')."'></script>";
		echo $html;
	}
	/*
	function getLocat(){
		$CONTNO = $_REQUEST['CONTNO'];
		$response = array();
		$sql = "
			select top 100 A.CONTNO,A.LOCAT,C.SNAM+C.NAME1+' '+C.NAME2+' ('+A.CONTNO+')' as CUSNAME 
			from {$this->MAuth->getdb('ARMAST')} A
			left join {$this->MAuth->getdb('CUSTMAST')} C on A.CUSCOD = C.CUSCOD
			left join {$this->MAuth->getdb('INVTRAN')} I on A.STRNO = I.STRNO 
			where A.CONTNO = '".$CONTNO."' order by A.CONTNO
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$response['LOCAT'] = $row->LOCAT;
			}
		}else{
			$response['LOCAT'] = "";
		}
		echo json_encode($response);
	}
	*/
	function conditiontopdf(){
		$data = array();
		$data[] = urlencode($_REQUEST['LOCAT'].'||'.$_REQUEST['CONTNO'].'||'.$_REQUEST['F_DATE']
		.'||'.$_REQUEST['T_DATE'].'||'.$_REQUEST['OFFICER'].'||'.$_REQUEST['order']);
		echo json_encode($this->generateData($data,"encode"));
	}
	function pdf(){
		$data = array();
		$data[] = $_REQUEST["condpdf"];
		$arrs = $this->generateData($data,"decode");
		$arrs[0] = urldecode($arrs[0]);
		
		$tx = explode('||',$arrs[0]);
		$LOCAT     = $tx[0];
		$CONTNO    = $tx[1];
		$F_DATE    = $this->Convertdate(1,$tx[2]);
		$T_DATE    = $this->Convertdate(1,$tx[3]);
		$OFFICER   = $tx[4];
		$order     = $tx[5];
		$head = ""; $html = ""; $i = 0;
		$head = "
			<tr class='wm'>
				<td class='wf' style='height:1px;border-top:0.1px solid black;' colspan='16'></td>
			</tr>
			<tr>
				<th style='border-bottom:0.1px solid black;text-align:left;'>No.</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>สาขาที่รับ</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>วันที่ใบรับ</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>เลขที่ใบรับ</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>เลขที่สัญญา</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>ชื่อ - สกุล</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ชำระโดย</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>จน. งวด</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ดอกผลทั้งหมด</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ดอกผลสะสม<br>งวดก่อน</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ส่วนลดสะสม</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ชำระงวดที่</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>มูลค่ายอด<br>หักลูกหนี้</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ส่วนลด</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ดอกผลงวดนี้</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ดอกผลคงเหลือ</th>	
			</tr>
			<tr>
				<td class='wf' style='height:1px;border-top:0.1px solid black;' colspan='16'></td>
			</tr>
		";
		$sql = "
			IF OBJECT_ID('tempdb..#SYDPM') IS NOT NULL DROP TABLE #SYDPM
			select * 
			into #SYDPM
			FROM(
				select A.TMBILL,convert(varchar(8),A.TMBILDT,112) as TMBILDT,A.CONTNO,A.LOCATPAY,A.LOCATRECV,A.PAYAMT
					,A.PAYAMT_N,A.PAYDT,A.PAYTYP,A.PROFSYD,B.BILLDT,RTRIM(D.NAME1)+'  '+RTRIM(D.NAME2) as NAME
					, B.BILLNO,A.DISCT,C.T_NOPAY,C.NPROFIT,C.CUSCOD,A.F_PAR,A.F_PAY,A.L_PAY,A.L_PAR
					,(
						select SUM(PROFSYD) as BPROFSYD from {$this->MAuth->getdb('CHQTRAN')} 
						where CONTNO = A.CONTNO and (NOPAY < A.NOPAY)
					) AS BPROFSYD
					,(
						select SUM(DISCT) AS BDISCT 
						from {$this->MAuth->getdb('CHQTRAN')} where CONTNO = A.CONTNO and (NOPAY < A.NOPAY)
					)AS BDISCT  
				from {$this->MAuth->getdb('CHQTRAN')} A 
				left join {$this->MAuth->getdb('CHQMAS')} B ON (A.LOCATRECV = B.LOCATRECV) and A.TMBILL = B.TMBILL 
				left join (
					select LOCAT,CONTNO,CUSCOD,T_NOPAY,NPROFIT,TOTPRC,BILLCOLL 
					from {$this->MAuth->getdb('ARMAST')} 
					union  
					select LOCAT,CONTNO,CUSCOD,T_NOPAY,NPROFIT,TOTPRC,BILLCOLL 
					from {$this->MAuth->getdb('HARMAST')}
				) C ON  A.CONTNO = C.CONTNO and A.LOCATPAY = C.LOCAT  
				left join {$this->MAuth->getdb('CUSTMAST')} D on D.CUSCOD = C.CUSCOD 
				where (A.PAYFOR = '006' or A.PAYFOR = '007') 
				and (A.FLAG <> 'C') and A.LOCATPAY like '".$LOCAT."%' 
				and A.CONTNO like '".$CONTNO."%' and (A.PAYAMT_N > 0 ) 
				and C.TOTPRC > 0 and C.BILLCOLL like '".$OFFICER."%' and C.NPROFIT > 0 
				and (A.TMBILDT between '".$F_DATE."' and '".$T_DATE."')
			)SYDPM
		";
		//echo $sql; exit;
		$this->db->query($sql);
		$sql = "
			select * from #SYDPM
		";
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){$i++;
				$nopay = "";
				$nopay = $row->F_PAR.$row->F_PAY."-".$row->L_PAY.$row->L_PAR;
				$html .="
					<tr>
						<td style='width:2%;text-align:left;'>".$i."</td>
						<td style='width:5%;text-align:left;'>".$row->LOCATRECV."</td>
						<td style='width:7%;text-align:left;'>".$this->Convertdate(2,$row->TMBILDT)."</td>
						<td style='width:8%;text-align:left;'>".$row->TMBILL."</td>
						<td style='width:8%;text-align:left;'>".$row->CONTNO."</td>
						<td style='width:14%;text-align:left;'>".$row->NAME."</td>
						<td style='width:5%;text-align:right;'>".$row->PAYTYP."</td>
						<td style='width:5%;text-align:right;'>".$row->T_NOPAY."</td>
						<td style='width:6%;text-align:right;'>".number_format($row->NPROFIT,2)."</td>
						<td style='width:6%;text-align:right;'>".number_format($row->BPROFSYD,2)."</td>
						<td style='width:5%;text-align:right;'>".number_format($row->BDISCT,2)."</td>
						<td style='width:5%;text-align:right;'>".$nopay."</td>
						<td style='width:5%;text-align:right;'>".number_format($row->PAYAMT_N,2)."</td>
						<td style='width:5%;text-align:right;'>".number_format($row->DISCT,2)."</td>
						<td style='width:6%;text-align:right;'>".number_format($row->PROFSYD,2)."</td>
						<td style='width:7%;text-align:right;'>".number_format($row->NPROFIT,2)."</td>
					</tr>
				";
			}
		}
		$sql = "
			select count(CONTNO) as countCONTNO,sum(PAYAMT_N) as sumPAYAMT_N,sum(DISCT) as sumDISCT
			,sum(PROFSYD) as sumPROFSYD from #SYDPM 
		";
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$html .="
					<tr>
						<td class='wf' style='height:1px;border-top:0.1px solid black;' colspan='16'></td>
					</tr>
					<tr>
						<td style='width:%;text-align:left;' colspan='3'><b>รวมทั้งสิน</b></td>
						<td style='width:%;text-align:left;' colspan='2'>".$row->countCONTNO."</td>
						<td style='width:%;text-align:left;' colspan='3'><b>รายการ</b></td>
						<td style='width:%;text-align:right;' colspan='5'>".number_format($row->sumPAYAMT_N,2)."</td>
						<td style='width:%;text-align:right;'>".number_format($row->sumDISCT,2)."</td>
						<td style='width:%;text-align:right;'>".number_format($row->sumPROFSYD,2)."</td>
					</tr>
					<tr>
						<td class='wf' style='height:1px;border-top:0.1px solid black;' colspan='16'></td>
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
				<table class='wf' style='font-size:7.5pt;height:700px;border-collapse:collapse;line-height:23px;overflow:wrap;vertical-align:text-top;'>
					<tbody>
						<tr>
							<th colspan='16' style='font-size:10pt;'>บริษัท ตั้งใจพัฒนายานยนต์ จำกัด</th>
						</tr>
						<tr>
							<th colspan='16' style='font-size:9pt;'>รายงานกำไรตามวันรับชำระ (SYD)</th>
						</tr>
						<tr>
							<td style='text-align:center;' colspan='16'>
								<b>รหัสสาขา</b> &nbsp;&nbsp;".$LOCAT."&nbsp;&nbsp;
							</td>
						</tr>
						<tr>
							<td style='text-align:right;' colspan='16'>Rpsyd 10,11</td>
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
	function formupdateprofit(){
		$F_DATE  = $_REQUEST['F_DATE'];
		$T_DATE  = $_REQUEST['T_DATE'];
		
		$html = "
			<div class='col-sm-12 col-xs-12' style='height:100%;overflow:auto;font-size:11pt;'>					
				<div class='row' style='height:90%;'>
					<div class='col-sm-12 col-xs-12' style='background-color:#808b96;border:5px solid white;height:75px;text-align:center;font-size:12pt;color:white;font-weight:bold;'>	
						<br>การปรับปรุงดอกผลเช่าซื้อ<br>
					</div>
					<div class='col-sm-12'>
						<div class='col-sm-3'>
							<div class='form-group'>
								รหัสสาขา
								<select id='LOCAT' class='form-control input-sm'></select>
							</div>
						</div>
						<div class='col-sm-3'>
							<div class='form-group'>
								เลขที่สัญญา
								<select id='CONTNO' class='form-control input-sm'></select>
							</div>
						</div>
						<div class='col-sm-3'>
							<div class='form-group'>
								จากวันที่ใบรับเงิน
								<input type='text' id='F_DATE' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' value='".$this->today('today')."' placeholder='ถึงวันที่ขาย' >
							</div>
						</div>
						<div class='col-sm-3'>
							<div class='form-group'>
								ถึงวันที่
								<input type='text' id='T_DATE' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' value='".$this->today('today')."' placeholder='ถึงวันที่ขาย' >
							</div>
						</div>
						<div class='col-sm-3'>
							<div class='form-group'>
								เลขที่ใบเสร็จ
								<input type='text' id='F_DATE' class='form-control input-sm' disabled>
							</div>
						</div>
					</div>
					<div class='col-sm-12'>
						<div class='col-sm-12' style='height:100%;width:100%;border:1px dotted #aaa;'>
							<div class='col-sm-5 col-sm-offset-2'>
								<div class='form-group'>
									<br>
									<label>
										<input type='radio' id='noup' name='update' checked> เฉพาะที่ยังไม่เคยปรับ
									</label>
								</div>
							</div>
							<div class='col-sm-5'>
								<div class='form-group'>
									<br>
									<label>
										<input type='radio' id='allup' name='update'> ปรับใหม่ทั้งหมด
									</label>
								</div>
							</div>
						</div>	
					</div>
					<div class='col-sm-12'>
						<div class='col-sm-6'>
							<br>
							<button id='btnreport' style='width:100%;' class='btn btn-cyan btn-sm'><span class='fa fa-refresh'>ปรับปรุงดอกผล STR ในตารางค่างวด</span></button>
						</div>
						<div class='col-sm-6'>
							<br>
							<button id='btnreport' style='width:100%;' class='btn btn-cyan btn-sm'><span class='fa fa-refresh'>ปรับปรุงดอกผลตามใบรับ</span></button>
						</div>
					</div>
				</div>	
			</div>
		";
		$response = array("html"=>$html);
		echo json_encode($response);
	}
}