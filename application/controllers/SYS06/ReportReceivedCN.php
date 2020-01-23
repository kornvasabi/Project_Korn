<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/********************************************************
             ______@11/01/2020______
			 Pasakorn Boonded

********************************************************/
class ReportReceivedCN extends MY_Controller {
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
							<br>รายงานการรับชำระเงินตามเลขที่สัญญา<br>
						</div>
						<div class='col-sm-10 col-xs-10 col-sm-offset-1 text-primary'>	
							<br>
							<div class='col-sm-3'>	
								<div class='form-group'>
									เลขที่สัญญา
									<div class='input-group'>
										<input type='text' id='add_contno' class='form-control input-sm' placeholder='เลขที่สัญญา' disabled>
										<span class='input-group-btn'>
											<button id='btnaddcont' class='btn btn-info btn-sm' type='button'>
												<span class='glyphicon glyphicon-hand-up' aria-hidden='true'></span>
											</button>
										</span>
									</div>
								</div>
							</div>
							<div class='col-sm-3'>	
								<div class='form-group' >
									ทำสัญญาที่สาขา
									<input type='text' id='locat' class='form-control input-sm' disabled>
								</div>
							</div>
							<div class='col-sm-6'>	
								<div class='form-group'>
									ชื่อ - นามสกุล
									<input type='text' id='cusname' class='form-control input-sm' disabled>
								</div>
							</div>
						</div>
						<div class='col-sm-10 col-xs-10 col-sm-offset-1'><br>	
							<div class='col-sm-5 col-xs-5'>	
								<div class='form-group'>
									เรียงลำดับข้อมูล
									<div class='col-sm-12 col-xs-12' style='border:0.1px dotted #d6d6d6;'>	
										<div class='col-sm-12'>
											<div class='form-group'>
												<br>
												<label>
													<input type= 'radio' id='OR1' name='order' checked> เลขที่ใบรับ
												</label>
												<br><br>
												<label>
													<input type= 'radio' id='OR2' name='order'> วันที่รับชำระ
												</label>
												<br><br>
												<label>
													<input type= 'radio' id='OR3' name='order'> เลขที่เช็ค
												</label>
												<br><br>
												<label>
													<input type= 'radio' id='OR4' name='order'> วันที่เช็ค
												</label>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class='col-sm-7 col-xs-7'>	
								<div class='form-group'>
									ประเภทการขาย<br>
									<div class='col-sm-12 col-xs-12' style='border:0.1px dotted #d6d6d6;'>	
										<div class='col-sm-6'>
											<div class='form-group'>
												<br>
												<label>
													<input type= 'radio' id='P1' name='CN'> ขายสด/เครดิต
												</label>
												<br><br>
												<label>
													<input type= 'radio' id='P2' name='CN' checked> ขายผ่อนเช่าซื้อ
												</label>
												<br><br>
												<label>
													<input type= 'radio' id='P3' name='CN'> ขายไฟแนนซ์
												</label>
											</div>
										</div>
										<div class='col-sm-6'>
											<div class='form-group'>
												<br>
												<label>
													<input type= 'radio' id='P4' name='CN'> ขายส่งเอเยนต์
												</label>
												<br><br>
												<label>
													<input type= 'radio' id='P5' name='CN'> ขายอุปกรณ์เสริม
												</label>
												<br><br>
												<label>
													<input type= 'radio' id='P6' name='CN'> ลูกหนี้อื่น
												</label>
											</div>
										</div>
									</div>
									<div class='col-sm-12 col-xs-12'>
										<br>
										<button id='btnreportCN' type='button' class='btn btn-info btn-outline btn-block' style='width:100%'><span class='fa fa-folder-open'><b>แสดง</b></span></button>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		";
		$html .="<script src='".base_url('public/js/SYS06/ReportReceivedCN.js')."'></script>";
		echo $html;
	}
	function conditiontopdf(){
		$data = array();
		$data[] = urlencode($_REQUEST["CONTNO"].'||'.$_REQUEST["LOCAT"].'||'.$_REQUEST["CUSNAME"].'||'.$_REQUEST["order"]
		.'||'.$_REQUEST["price"]);
		echo json_encode($this->generateData($data,"encode"));
	}
	function pdf(){
		$data = array();
		$data[] = $_GET["condpdf"];
		$arrs = $this->generateData($data,"decode");
		$arrs[0] = urldecode($arrs[0]);
		
		$tx = explode("||",$arrs[0]);
		$CONTNO 	= $tx[0];
		$LOCAT 	    = $tx[1];
		$CUSNAME	= $tx[2];
		$order 	    = $tx[3];
		$price      = $tx[4];
		
		$orderth = "";
		if($order == "TMBILL"){
			$orderth = "เลขที่ใบรับ";
		}else if($order == "TMBILDT"){
			$orderth = "วันที่รับชำระ";
		}else if($order == "CHQNO"){
			$orderth = "เลขที่เช็ค";
		}else if($order == "CHQDT"){
			$orderth = "วันที่เช็ค";
		}
		$sql = "
			select LOCATCD,LOCATNM from {$this->MAuth->getdb('INVLOCAT')} where LOCATCD = '".$LOCAT."'
		";
		$locatnm = "";
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$locatnm = $row->LOCATNM;
			}
		}
		//แก้ radio ประเภทการขาย
		$kind = "";
		if($price == "P1"){
			$sql = "
				select CONTNO,LOCAT,TSALE from {$this->MAuth->getdb('ARCRED')} 
				where CONTNO = '".$CONTNO."' and LOCAT = '".$LOCAT."'
			";
		}else if($price == "P2"){
			$sql = "
				select CONTNO,LOCAT,TSALE from {$this->MAuth->getdb('ARMAST')} 
				where CONTNO = '".$CONTNO."' and LOCAT = '".$LOCAT."'
			";
		}else if($price == "P3"){
			$sql = "
				select CONTNO,LOCAT,TSALE from {$this->MAuth->getdb('ARFINC')} 
				where CONTNO = '".$CONTNO."' and LOCAT = '".$LOCAT."'
			";
		}else if($price == "P4"){
			$sql = "
				select CONTNO,LOCAT,TSALE from {$this->MAuth->getdb('AR_INVOI')} 
				where CONTNO = '".$CONTNO."' and LOCAT = '".$LOCAT."'
			";
		}else if($price == "P5"){
			$sql = "
				select CONTNO,LOCAT,TSALE from {$this->MAuth->getdb('AROPTMST')} 
				where CONTNO = '".$CONTNO."' and LOCAT = '".$LOCAT."'
			";
		}else if($price == "P6"){
			$sql = "
				select CONTNO,LOCAT,TSALE from {$this->MAuth->getdb('AROTHR')} 
				where CONTNO = '".$CONTNO."' and LOCAT = '".$LOCAT."'
			";
		}
		$query1 = $this->db->query($sql);
		$TSALE = "";
		if($query1->row()){
			foreach($query1->result() as $row){
				$TSALE = $row->TSALE;
			}
		}
		
		$sql = "
			IF OBJECT_ID('tempdb..#RCN') IS NOT NULL DROP TABLE #RCN
			select LOCATPAY,TMBILL,PAYAMT,DISCT,PAYINT,DSCINT,NETPAY,PAYFOR
				,TMBILDT,PAYTYP,CHQNO,CHQDT,CHQAMT,FLAG,BILLNO,BILLDT,PAYDT
			into #RCN
			from (
				select T.LOCATPAY,T.TMBILL,T.PAYAMT,T.DISCT,T.PAYINT,T.DSCINT,T.NETPAY,T.PAYFOR
				,convert(varchar(8),M.TMBILDT,112) as TMBILDT,M.PAYTYP,M.CHQNO,convert(varchar(8),M.CHQDT,112) as CHQDT,M.CHQAMT
				,M.FLAG,M.BILLNO,convert(varchar(8),M.BILLDT,112) as BILLDT,convert(varchar(8),M.PAYDT,112) as PAYDT 
				from {$this->MAuth->getdb('CHQTRAN')} T
				left join {$this->MAuth->getdb('CHQMAS')} M on T.TMBILL = M.TMBILL 
				where (T.LOCATRECV = M.LOCATRECV) and (T.CONTNO = '".$CONTNO."' or T.CONTNO = '') 
				and (T.LOCATPAY = '".$LOCAT."') and (T.TSALE = '".$TSALE."' or T.TSALE = 'R') --order by T.TMBILL
			)RCN
		";
		//echo $sql; exit;
		$query2 = $this->db->query($sql);
		
		$sql = "
			select * from #RCN order by ".$order."
		";
		$query3 = $this->db->query($sql);
		$sql = "
			select COUNT(TMBILL) as countTMBILL,sum(CHQAMT) as CHQAMT,SUM(PAYAMT) as PAYAMT,SUM(DISCT) as DISCT
			,SUM(PAYINT) as PAYINT,SUM(DSCINT) as DSCINT,SUM(NETPAY) as NETPAY from #RCN
		";
		$query4 = $this->db->query($sql);
		$sql = "
			select sum(CHQAMT) as CHQAMT,SUM(PAYAMT) as PAYAMT,SUM(DISCT) as DISCT
			,SUM(PAYINT) as PAYINT,SUM(DSCINT) as DSCINT,SUM(NETPAY) as NETPAY from #RCN where FLAG = 'C'
		";
		$query5 = $this->db->query($sql);
		$sql = "
			declare @A1 decimal(12) = (select coalesce(SUM(CHQAMT),0) from #RCN); 
			declare @B1 decimal(12) = (select coalesce(SUM(CHQAMT),0) from #RCN where FLAG = 'C'); 
			declare @A2 decimal(12) = (select coalesce(SUM(PAYAMT),0) from #RCN); 
			declare @B2 decimal(12) = (select coalesce(SUM(PAYAMT),0) from #RCN where FLAG = 'C'); 
			declare @A3 decimal(12) = (select coalesce(SUM(DISCT),0)  from #RCN); 
			declare @B3 decimal(12) = (select coalesce(SUM(DISCT),0)  from #RCN where FLAG = 'C'); 
			declare @A4 decimal(12) = (select coalesce(SUM(PAYINT),0) from #RCN); 
			declare @B4 decimal(12) = (select coalesce(SUM(PAYINT),0) from #RCN where FLAG = 'C'); 
			declare @A5 decimal(12) = (select coalesce(SUM(DSCINT),0) from #RCN); 
			declare @B5 decimal(12) = (select coalesce(SUM(DSCINT),0) from #RCN where FLAG = 'C'); 
			declare @A6 decimal(12) = (select coalesce(SUM(NETPAY),0) from #RCN); 
			declare @B6 decimal(12) = (select coalesce(SUM(NETPAY),0) from #RCN where FLAG = 'C'); 

			select @A1-@B1 as CHQAMT,@A2-@B2 as PAYAMT,@A3-@B3 as DISCT,@A4-@B4 as PAYINT,@A5-@B5 as DSCINT,@A6-@B6 as NETPAY
		";
		//echo $sql; exit;
		$query6 = $this->db->query($sql);
		$head = ""; $html = ""; 
		$head = "
			<tr class='wm'>
				<td class='wf pd' style='height:1px;border-top:0.1px solid black;' colspan='17'></td>
			</tr>
			<tr>
				<th style='border-bottom:0.1px solid black;text-align:center;'>สาขา</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>เลขใบรับเงิน</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>วันที่ใบรับ</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>เลขที่ใบเสร็จ</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>วันที่ใบเสร็จ</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>ชำระค่า</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>ชำระโดย</th> 
				<th style='border-bottom:0.1px solid black;text-align:left;'>เลขที่เช็ค</th> 
				<th style='border-bottom:0.1px solid black;text-align:left;'>วันที่เช็ค</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>วันที่ได้เงิน</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ยอดเงินหน้าเช็ค</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ยอดหักลูกหนี้</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ส่วนลด</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>เบี้ยปรับ</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ส่วนลดเบี้ยปรับ</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ยอดรับสุทธิ</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'></th>
			</tr>
			<tr class='wm'>
				<td class='wf pd' style='height:1px;border-top:0.1px solid black;' colspan='17'></td>
			</tr>
		";
		$status = "";
		if($query3->row()){
			foreach($query3->result() as $row){
				if($row->FLAG == 'C'){
					$status = "*ยกเลิก*";
				}else{
					$status = "";
				}
				$html .="
					<tr class='trow'>
						<td style='width:50px;text-align:center;'>".$row->LOCATPAY."</td>
						<td style='width:150px;text-align:left;'>".$row->TMBILL."</td>
						<td style='width:70px;text-align:left;'>".$this->Convertdate(2,$row->TMBILDT)."</td>
						<td style='width:100px;text-align:left;'>".$row->BILLNO."</td>
						<td style='width:50px;text-align:left;'>".$this->Convertdate(2,$row->BILLDT)."</td>
						<td style='width:50px;text-align:left;'>".$row->PAYFOR."</td>
						<td style='width:50px;text-align:left;'>".$row->PAYTYP."</td>
						<td style='width:50px;text-align:left;'>".$row->CHQNO."</td>
						<td style='width:70px;text-align:left;'>".$this->Convertdate(2,$row->CHQDT)."</td>
						<td style='width:70px;text-align:left;'>".$this->Convertdate(2,$row->PAYDT)."</td>
						<td style='width:50px;text-align:right;'>".number_format($row->CHQAMT,2)."</td>
						<td style='width:50px;text-align:right;'>".number_format($row->PAYAMT,2)."</td>
						<td style='width:50px;text-align:right;'>".number_format($row->DISCT,2)."</td>
						<td style='width:50px;text-align:right;'>".number_format($row->PAYINT,2)."</td>
						<td style='width:50px;text-align:right;'>".number_format($row->DSCINT,2)."</td>
						<td style='width:50px;text-align:right;'>".number_format($row->NETPAY,2)."</td>
						<td style='width:50px;text-align:right;'>".$status."</td>
					</tr>
				";
			}
		}
		if($query4->row()){
			foreach($query4->result() as $row){
				$html .="
					<tr class='wm'>
						<td class='wf pd' style='height:1px;border-top:0.1px solid black;' colspan='17'></td>
					</tr>
					<tr class='trow'>
						<th style='width:50px;text-align:center;'colspan='2'>รวมทั้งสิ้น</th>
						<td style='width:150px;text-align:left;'>".$row->countTMBILL."</td>
						<th style='width:70px;text-align:left;'>รายการ</th>
						<th style='width:100px;text-align:right;'colspan='3'>ยอดรวมทั้งสิ้น</th>
						<td style='width:50px;text-align:right;'colspan='4'>".number_format($row->CHQAMT,2)."</td>
						<td style='width:50px;text-align:right;'>".number_format($row->PAYAMT,2)."</td>
						<td style='width:50px;text-align:right;'>".number_format($row->DISCT,2)."</td>
						<td style='width:50px;text-align:right;'>".number_format($row->PAYINT,2)."</td>
						<td style='width:50px;text-align:right;'>".number_format($row->DSCINT,2)."</td>
						<td style='width:50px;text-align:right;'>".number_format($row->NETPAY,2)."</td>
					</tr>
				";
			}
		}
		if($query5->row()){
			foreach($query5->result() as $row){
				$html .="
					<tr class='trow'>
						<th style='width:50px;text-align:center;'colspan='2'></th>
						<td style='width:150px;text-align:left;'></td>
						<th style='width:70px;text-align:left;'></th>
						<th style='width:100px;text-align:right;'colspan='3'>ยอดรวมรายการยกเลิก</th>
						<td style='width:50px;text-align:right;'colspan='4'>".number_format($row->CHQAMT,2)."</td>
						<td style='width:50px;text-align:right;'>".number_format($row->PAYAMT,2)."</td>
						<td style='width:50px;text-align:right;'>".number_format($row->DISCT,2)."</td>
						<td style='width:50px;text-align:right;'>".number_format($row->PAYINT,2)."</td>
						<td style='width:50px;text-align:right;'>".number_format($row->DSCINT,2)."</td>
						<td style='width:50px;text-align:right;'>".number_format($row->NETPAY,2)."</td>
					</tr>
				";
			}
		}
		if($query6->row()){
			foreach($query6->result() as $row){
				$html .="
					<tr class='trow'>
						<th style='width:50px;text-align:center;'colspan='2'></th>
						<td style='width:150px;text-align:left;'></td>
						<th style='width:70px;text-align:left;'></th>
						<th style='width:100px;text-align:right;'colspan='3'>ยอดรวมสุทธิ</th>
						<td style='width:50px;text-align:right;'colspan='4'>".number_format($row->CHQAMT,2)."</td>
						<td style='width:50px;text-align:right;'>".number_format($row->PAYAMT,2)."</td>
						<td style='width:50px;text-align:right;'>".number_format($row->DISCT,2)."</td>
						<td style='width:50px;text-align:right;'>".number_format($row->PAYINT,2)."</td>
						<td style='width:50px;text-align:right;'>".number_format($row->DSCINT,2)."</td>
						<td style='width:50px;text-align:right;'>".number_format($row->NETPAY,2)."</td>
					</tr>
					<tr class='wm'>
						<td class='wf pd' style='height:1px;border-top:0.1px solid black;' colspan='17'></td>
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
						<th colspan='17' style='font-size:10pt;'>บริษัท ตั้งใจพัฒนายานยนต์ จำกัด</th>
					</tr>
					<tr>
						<th colspan='17' style='font-size:9pt;'>รายงานการรับชำระตามเลขที่สัญญา</th>
					</tr>
					<tr>
						<td  colspan='17' style='font-size:9pt;text-align:center;'>
							<b>เลขที่สัญญา</b>&nbsp;&nbsp;".$CONTNO."&nbsp;&nbsp;&nbsp;&nbsp;<b>ชื่อ - นามสกุล</b> ".$CUSNAME."&nbsp;&nbsp;
						</td>
					</tr>
					<tr>
						<td style='text-align:left;font-size:8pt;' colspan='2'><b>Scrt By :</b>&nbsp;&nbsp;".$orderth."</td>
						<td style='text-align:center;font-size:9pt;' colspan='13'><b>ทำสัญญาที่สาขา</b>&nbsp;&nbsp;".$locatnm."</td>
						<td style='text-align:right;font-size:8pt;' colspan='2'>RpRec31</td>
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