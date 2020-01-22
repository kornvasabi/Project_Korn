<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/********************************************************
             ______@14/01/2020______
			 Pasakorn Boonded

********************************************************/
class ReportReceivedCC extends MY_Controller {
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
							<br>รายงานการรับชำระเงินตามรหัสลูกค้า<br>
						</div>
						<div class='col-sm-10 col-xs-10 col-sm-offset-2 text-primary'>	
							<br>
							<div class='col-sm-3'>	
								<div class='form-group'>
									รหัสลูกค้า
									<div class='input-group'>
										<input type='text' id='add_cuscod' class='form-control input-sm' placeholder='รหัสลูกค้า'  disabled>
										<span class='input-group-btn'>
											<button id='btnaddcus' class='btn btn-info btn-sm' type='button'>
												<span class='glyphicon glyphicon-hand-up' aria-hidden='true'></span>
											</button>
										</span>
									</div>
								</div>
							</div>
							<div class='col-sm-6'>	
								<div class='form-group'>
									ชื่อ - นามสกุล
									<input type='text' id='cusname' class='form-control input-sm' disabled>
								</div>
							</div>
						</div>
						<div class='col-sm-10 col-xs-10 col-sm-offset-2'><br>	
							<div class='col-sm-10 col-xs-10'>	
								<div class='form-group'>
									การเรียงลำดับของข้อมูล<br>
									<div class='col-sm-11 col-xs-11' style='border:0.1px dotted #d6d6d6;'>	
										<div class='col-sm-6'>
											<div class='form-group'>
												<br>
												<label>
													<input type= 'radio' id='OR1' name='CC' checked> เลขที่ใบรับ
												</label>
											</div>
										</div>
										<div class='col-sm-6'>
											<div class='form-group'>
												<br>
												<label>
													<input type= 'radio' id='OR2' name='CC'> เลขที่สัญญา
												</label>
											</div>
										</div>
										<div class='col-sm-6'>
											<div class='form-group'>
												<br>
												<label>
													<input type= 'radio' id='OR3' name='CC'> วันที่รับชำระ
												</label>
											</div>
										</div>
										<div class='col-sm-6'>
											<div class='form-group'>
												<br>
												<label>
													<input type= 'radio' id='OR4' name='CC'> วันที่เช็ค
												</label>
											</div>
										</div>
									</div>
									<div class='col-sm-11 col-xs-11'>
										<br>
										<button id='btnreportCC' type='button' class='btn btn-info btn-outline btn-block' style='width:100%'><span class='fa fa-folder-open'><b>แสดง</b></span></button>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		";
		$html .="<script src='".base_url('public/js/SYS06/ReportReceivedCC.js')."'></script>";
		echo $html;
	}
	function conditiontopdf(){
		$data = array();
		$data[] = urlencode($_REQUEST["CUSCOD"].'||'.$_REQUEST["CUSNAME"].'||'.$_REQUEST["order"]);
		echo json_encode($this->generateData($data,"encode"));
	}
	function pdf(){
		$data = array();
		$data[] = $_GET["condpdf"];
		$arrs = $this->generateData($data,"decode");
		$arrs[0] = urldecode($arrs[0]);
		
		$tx = explode("||",$arrs[0]);
		$CUSCOD 	= $tx[0];
		$CUSNAME 	= $tx[1];
		$order 	    = $tx[2];
		
		$scrt = "";
		if($order == "TMBILL"){
			$scrt = "เลขที่ใบรับ";
		}else if($order == "CONTNO"){
			$scrt = "เลขที่สัญญา";
		}else if($order == "TMBILDT"){
			$scrt = "วันที่รับชำระ";
		}else if($order == "CHQDT"){
			$scrt = "วันที่เช็ค";
		}
		$sql = "
			IF OBJECT_ID('tempdb..#ONE') IS NOT NULL DROP TABLE #RCUS
			select CUSCOD,TMBILL,LOCATRECV,TMBILDT,BILLNO,BILLDT,PAYTYP,CHQNO
				,CHQDT,CHQAMT,CHQTMP,FLAG,PAYDT,REFNO
			into #RCUS
			FROM(
				select CUSCOD,TMBILL,LOCATRECV,convert(varchar(8),TMBILDT,112) as TMBILDT,BILLNO
				,convert(varchar(8),BILLDT,112) as BILLDT,PAYTYP,CHQNO,convert(varchar(8),CHQDT,112) as CHQDT
				,CHQAMT,CHQTMP,FLAG,convert(varchar(8),PAYDT,112) as PAYDT,REFNO from {$this->MAuth->getdb('CHQMAS')} 
				where CUSCOD = '".$CUSCOD."' and TMBILL in (select TMBILL from {$this->MAuth->getdb('CHQTRAN')}) 
			)RCUS
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		$sql = "
			select * from #RCUS order by ".$order."
		";
		$query2 = $this->db->query($sql);
		
		$sql = "
			IF OBJECT_ID('temp..#RCC') IS NOT NULL DROP TABLE #RCC
			select TMBILL,PAYFOR,CONTNO,PAYAMT,DISCT,PAYINT,DSCINT,NETPAY,FLAG,FORDESC
			into #RCC
			from(
				select T.TMBILL,T.PAYFOR,T.CONTNO,T.PAYAMT,T.DISCT,T.PAYINT,T.DSCINT,T.NETPAY,T.FLAG,P.FORDESC 
				from {$this->MAuth->getdb('CHQTRAN')} T left join {$this->MAuth->getdb('PAYFOR')} P on T.PAYFOR = P.FORCODE  
				where CUSCOD = '".$CUSCOD."'	
			)RCC
		";
		$query3 = $this->db->query($sql);
		$sql = "
			select COUNT(PAYAMT) as countPAYAMT,SUM(PAYAMT) as PAYAMT,SUM(DISCT) as DISCT,SUM(PAYINT) as PAYINT,SUM(DSCINT) as DSCINT
			,SUM(NETPAY) as NETPAY from  #RCC
		";
		$query4 = $this->db->query($sql);
		$sql = "
			select SUM(PAYAMT) as PAYAMT,SUM(DISCT) as DISCT,SUM(PAYINT) as PAYINT,SUM(DSCINT) as DSCINT
			,SUM(NETPAY) as NETPAY from  #RCC where FLAG = 'C'
		";
		$query5 = $this->db->query($sql);
		$sql = "
			declare @A1 decimal(12,2) = (select coalesce(SUM(PAYAMT),0) from #RCC); 
			declare @B1 decimal(12,2) = (select coalesce(SUM(PAYAMT),0) from #RCC where FLAG = 'C'); 
			declare @A2 decimal(12,2) = (select coalesce(SUM(DISCT),0)  from #RCC); 
			declare @B2 decimal(12,2) = (select coalesce(SUM(DISCT),0)  from #RCC where FLAG = 'C'); 
			declare @A3 decimal(12,2) = (select coalesce(SUM(PAYINT),0) from #RCC); 
			declare @B3 decimal(12,2) = (select coalesce(SUM(PAYINT),0) from #RCC where FLAG = 'C'); 
			declare @A4 decimal(12,2) = (select coalesce(SUM(DSCINT),0) from #RCC); 
			declare @B4 decimal(12,2) = (select coalesce(SUM(DSCINT),0) from #RCC where FLAG = 'C');
			declare @A5 decimal(12,2) = (select coalesce(SUM(NETPAY),0) from #RCC); 
			declare @B5 decimal(12,2) = (select coalesce(SUM(NETPAY),0) from #RCC where FLAG = 'C'); 

			select @A1-@B1 as PAYAMT,@A2-@B2 as DISCT,@A3-@B3 as PAYINT,@A4-@B4 as DSCINT,@A5-@B5 as NETPAY
		";
		$query6 = $this->db->query($sql);
		$head = ""; $html = ""; 
		$head = "
			<tr class='wm'>
				<td class='wf pd' style='height:1px;border-top:0.1px solid black;' colspan='12'></td>
			</tr>
			<tr>
				<th style='border-bottom:0.1px solid black;text-align:left;'>สาขาที่รับ<br>เลขที่สัญญา</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>เลขใบรับเงินชั่วคราว</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>วันที่รับเงิน<br>ชำระค่า</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>เลขที่ใบเสร็จรับเงิน</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>วันที่ใบเสร็จรับเงิน<br>เลขที่อ้างอิง</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>ชำระโดย</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>เลขที่เช็ค<br>ยอดหักลูกหนี้</th> 
				<th style='border-bottom:0.1px solid black;text-align:right;'>วันที่เช็ค<br>ส่วนลด</th> 
				<th style='border-bottom:0.1px solid black;text-align:right;'>วันที่รับเงิน<br>เบี้ยปรับ</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ยอดเงินหน้าเช็ค<br>ส่วนลดเบี้ยปรับ</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ยอดชำระสุทธิ<br>ยอดรับสุทธิ</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>status</th>
			</tr>
			<tr class='wm'>
				<td class='wf pd' style='height:1px;border-top:0.1px solid black;' colspan='12'></td>
			</tr>
		";
		$status = "";
		if($query2->row()){
			foreach($query2->result() as $row){
				$tmbill = array();
				$tmbill['TMBILL'] = $row->TMBILL;
				$sql3 = "
					select T.TMBILL,T.PAYFOR,T.CONTNO,T.PAYAMT,T.DISCT,T.PAYINT,T.DSCINT,T.NETPAY,T.FLAG,P.FORDESC 
					from {$this->MAuth->getdb('CHQTRAN')} T left join {$this->MAuth->getdb('PAYFOR')} P on T.PAYFOR = P.FORCODE 
					where CUSCOD = '".$CUSCOD."' and TMBILL = '".$tmbill['TMBILL']."'
				";
				$chq = "";
				$query3 = $this->db->query($sql3);
				if($query3->row()){
					foreach($query3->result() as $row3){
						$chq .="
							<tr class='trow'>
								<td style='width:70px;text-align:left;' colspan='2'>".$row3->CONTNO."</td>
								<td style='width:70px;text-align:left;' colspan='2'><i>".$row3->FORDESC."</i></td>
								<td style='width:70px;text-align:right;' colspan='3'>".number_format($row3->PAYAMT,2)."</td>
								<td style='width:70px;text-align:right;'>".number_format($row3->DISCT,2)."</td>
								<td style='width:70px;text-align:right;'>".number_format($row3->PAYINT,2)."</td>
								<td style='width:70px;text-align:right;'>".number_format($row3->DSCINT,2)."</td>
								<td style='width:70px;text-align:right;'>".number_format($row3->NETPAY,2)."</td>
							</tr>
						";
					}
				}
				if($row->FLAG == 'C'){
					$status = "*ยกเลิก*";
				}else{
					$status = "";
				}
				$html .="
					<tr class='trow'>
						<td style='width:70px;text-align:left;'>".$row->LOCATRECV."</td>
						<td style='width:70px;text-align:left;'>".$row->TMBILL."</td>
						<td style='width:70px;text-align:left;'>".$this->Convertdate(2,$row->TMBILDT)."</td>
						<td style='width:100px;text-align:left;'>".$row->BILLNO."</td>
						<td style='width:70px;text-align:left;'>".$this->Convertdate(2,$row->BILLDT)."</td>
						<td style='width:70px;text-align:left;'>".$row->PAYTYP."</td>
						<td style='width:70px;text-align:right;'>".$row->CHQNO."</td>
						<td style='width:70px;text-align:right;'>".$this->Convertdate(2,$row->CHQDT)."</td>
						<td style='width:70px;text-align:right;'>".$this->Convertdate(2,$row->PAYDT)."</td>
						<td style='width:70px;text-align:right;'>".number_format($row->CHQAMT,2)."</td>
						<td style='width:70px;text-align:right;'>".number_format($row->CHQTMP,2)."</td>
						<td style='width:70px;text-align:right;'>".$status."</td>
					</tr>
					".$chq."
				";
			}
		}
		if($query4->row()){
			foreach($query4->result() as $row){
				$html .="
					<tr class='wm'>
						<td class='wf pd' style='height:1px;border-top:0.1px solid black;' colspan='12'></td>
					</tr>
					<tr class='trow'>
						<th style='width:70px;text-align:left;'>รวมทั้งสิ้น</th>
						<td style='width:70px;text-align:center;'>".$row->countPAYAMT."</td>
						<th style='width:70px;text-align:left;'>รายการ</th>
						<th style='width:70px;text-align:right;' colspan='3'>ยอดรวมทั้งสิ้น</th>
						<td style='width:70px;text-align:right;'>".number_format($row->PAYAMT,2)."</td>
						<td style='width:70px;text-align:right;'>".number_format($row->DISCT,2)."</td>
						<td style='width:70px;text-align:right;'>".number_format($row->PAYINT,2)."</td>
						<td style='width:70px;text-align:right;'>".number_format($row->DSCINT,2)."</td>
						<td style='width:70px;text-align:right;'>".number_format($row->NETPAY,2)."</td>
					</tr>
				";
			}
		}
		if($query5->row()){
			foreach($query5->result() as $row){
				$html .="
					<tr class='trow'>
						<td style='width:70px;text-align:left;'></td>
						<td style='width:70px;text-align:center;'></td>
						<td style='width:70px;text-align:left;'></td>
						<th style='width:70px;text-align:right;' colspan='3'>ยอดรวมรายการยกเลิก</th>
						<td style='width:70px;text-align:right;'>".number_format($row->PAYAMT,2)."</td>
						<td style='width:70px;text-align:right;'>".number_format($row->DISCT,2)."</td>
						<td style='width:70px;text-align:right;'>".number_format($row->PAYINT,2)."</td>
						<td style='width:70px;text-align:right;'>".number_format($row->DSCINT,2)."</td>
						<td style='width:70px;text-align:right;'>".number_format($row->NETPAY,2)."</td>
					</tr>
				";
			}
		}
		if($query6->row()){
			foreach($query6->result() as $row){
				$html .="
					<tr class='trow'>
						<td style='width:70px;text-align:left;'></td>
						<td style='width:70px;text-align:center;'></td>
						<td style='width:70px;text-align:left;'></td>
						<th style='width:70px;text-align:right;' colspan='3'>ยอดรวมสุทธิ</th>
						<td style='width:70px;text-align:right;'>".number_format($row->PAYAMT,2)."</td>
						<td style='width:70px;text-align:right;'>".number_format($row->DISCT,2)."</td>
						<td style='width:70px;text-align:right;'>".number_format($row->PAYINT,2)."</td>
						<td style='width:70px;text-align:right;'>".number_format($row->DSCINT,2)."</td>
						<td style='width:70px;text-align:right;'>".number_format($row->NETPAY,2)."</td>
					</tr>
					<tr class='wm'>
						<td class='wf pd' style='height:1px;border-top:0.1px solid black;' colspan='12'></td>
					</tr>
				";
			}
		}
		$mpdf = new \Mpdf\Mpdf([
			'mode' => 'utf-8', 
			'format' =>'A4-L',
			'margin_top' => 10, 	//default = 16
			'margin_left' => 10, 	//default = 15
			'margin_right' => 10, 	//default = 15
			'margin_bottom' => 10, 	//default = 16
			'margin_header' => 9, 	//default = 9
			'margin_footer' => 9, 	//default = 9
		]);
		$content = "
			<table class='wf' style='font-size:7.5pt;height:700px;border-collapse:collapse;line-height:23px;overflow:wrap;vertical-align:text-top;'>
				<tbody>
					<tr>
						<th colspan='12' style='font-size:10pt;'>บริษัท ตั้งใจพัฒนายานยนต์ จำกัด</th>
					</tr>
					<tr>
						<th colspan='12' style='font-size:9pt;'>รายงานการรับชำระตามรหัสลูกค้า</th>
					</tr>
					<tr>
						<td  colspan='12' style='font-size:9pt;text-align:center;'>
							<b>รหัสลูกค้า</b>&nbsp;&nbsp;".$CUSCOD."&nbsp;&nbsp;&nbsp;&nbsp;<b>ชื่อ - นามสกุล</b> ".$CUSNAME."&nbsp;&nbsp;
						</td>
					</tr>
					<tr>
						<td style='text-align:left;' colspan='6'><b>Scrt By :</b>".$scrt."</td>
						<td style='text-align:right;' colspan='6'>RpRec41</td>
					</tr>
					".$head."
					".$html."
				</tbody>
			</table>
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
			</style>
		";
		$content = $content.$stylesheet;
		
		$head = "
			<div class='wf pf' style='top:1060;left:600;top:715;left:880; font-size:6pt;'>วันที่พิมพ์รายงาน : ".date('d/m/').(date('Y')+543)." ".date('H:i')." หน้า {PAGENO} / {nbpg}</div>
		";
		$mpdf->SetHTMLHeader($head);	
		$mpdf->WriteHTML($content);	
		$mpdf->Output();
	}
}