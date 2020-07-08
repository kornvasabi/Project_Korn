<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//BEE+
class ReportVatdue extends MY_Controller {
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
	
	//หน้าแรก
	function index(){
		$claim = $this->MLogin->getclaim(uri_string());
		if($claim['m_access'] != "T"){ echo "<div align='center' style='color:red;font-size:16pt;width:100%;'>ขออภัย คุณยังไม่มีสิทธิเข้าใช้งานหน้านี้ครับ</div>"; exit; }

		$html = "
			<div class='b_tab1' name='home' locat='{$this->sess['branch']}' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}' today='".$this->today('today')."' style='height:calc(100vh - 132px);overflow:auto;background-color:white;'>
				<div class='col-sm-12 col-xs-12' style='height:100%;overflow:auto;font-size:10.5pt;'>					
					<div class='row' style='height:90%;'>
						<div class='col-sm-12 col-xs-12 bg-info' style='border:5px solid white;height:75px;text-align:center;font-size:12pt;color:white;font-weight:bold;'>	
							<br>รายงานภาษีครบกำหนด<br>
						</div>
						<div class='col-sm-8 col-xs-8 col-sm-offset-2'>
							<br>
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group'>
									สาขา
									<select id='LOCAT1' class='form-control input-sm' data-placeholder='สาขา'></select>
								</div>
							</div>
						</div>
						<div class='col-sm-8 col-xs-8 col-sm-offset-2'>
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group'>
									จากวันที่
									<input type='text' id='FRMDATE' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' placeholder='จากวันที่' value='".$this->today('today')."' style='font-size:10.5pt'>
								</div>
							</div>
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group'>
									ถึงวันที่
									<input type='text' id='TODATE' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' placeholder='ถึงวันที่' value='".$this->today('today')."' style='font-size:10.5pt'>
								</div>
							</div>
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group' >
									เลขที่สัญญา
									<select id='CONTNO1' class='form-control input-sm' data-placeholder='เลขที่สัญญา'></select>
								</div>
							</div>
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group' >
									รหัสพนักงานเก็บเงิน
									<select id='BILLCOL1' class='form-control input-sm' data-placeholder='รหัสพนักงานเก็บเงิน'></select>
								</div>
							</div>
						</div>
						<div class='col-sm-8 col-xs-8 col-sm-offset-2'>
							<br>
							<div class='col-sm-6 col-xs-6'>	
								รูปแบบการพิมพ์
								<div class='col-sm-12 col-xs-12' style='border:0.1px dotted #d6d6d6;'>	
									<div class='col-sm-12 col-xs-12'>
										<div class='form-group'>
											<br>
											<input type= 'radio' id='inv_null' name='invoice'> เฉพาะที่ยังไม่ออกใบกำกับ
											<br><br>
											<input type= 'radio' id='inv_all'  name='invoice' checked> ทั้งหมด
											<br>
										</div>
									</div>
								</div>
							</div>
							<div class='col-sm-6 col-xs-6'>	
								เรียงลำดับข้อมูล
								<div class='col-sm-12 col-xs-12' style='border:0.1px dotted #d6d6d6;'>	
									<div class='col-sm-12 col-xs-12'>
										<div class='form-group'>
											<br>
											<input type= 'radio' id='normal' name='stat' checked> ปกติ
											<br>
											<input type= 'radio' id='ystat' name='stat'> รถยึดรอไถ่ถอน
											<br>
											<input type= 'radio' id='statall' name='stat'> ทั้งหมด
											<br>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class='row' style='height:10%;'>
						<div class='col-sm-12 col-xs-12'><br>	
							<button id='btnt1search' class='btn btn-info btn-sm' style='width:100%;font-size:10.5pt;'><span class='glyphicon glyphicon-search'> แสดง</span></button>
						</div>
					</div>
				</div>
			</div>
		";
		
		$html.= "<script src='".base_url('public/js/SYS07/ReportVatdue.js')."'></script>";
		echo $html;
	}
	
	function search(){
		$LOCAT1		= $_REQUEST["LOCAT1"];
		$CONTNO1 	= str_replace(chr(0),'',$_REQUEST["CONTNO1"]);
		$BILLCOL1 	= str_replace(chr(0),'',$_REQUEST["BILLCOL1"]);
		$FRMDATE 	= $this->Convertdate(1,$_REQUEST["FRMDATE"]);
		$TODATE 	= $this->Convertdate(1,$_REQUEST["TODATE"]);
		$invoice 	= $_REQUEST["invoice"];
		$stat 		= $_REQUEST["stat"];
		
		$cond = ""; $rpcond = "";
		
		if($LOCAT1 != ""){
			$cond .= " AND (A.LOCAT LIKE '".$LOCAT1."%')";
			$rpcond .= "  สาขา ".$LOCAT1;
		}
		
		if($CONTNO1 != ""){
			$cond .= " AND (A.CONTNO LIKE '".$CONTNO1."%') ";
			$rpcond .= "  เลขที่สัญญา ".$CONTNO1;
		}
		
		if($BILLCOL1 != ""){
			$cond .= " AND (B.BILLCOLL LIKE '".$BILLCOL1."%') ";
			$rpcond .= "  รหัสพนักงานเก็บเงิน ".$BILLCOL1;
		}

		if($invoice == "null"){
			$cond .= " AND (A.TAXINV = '' or A.TAXINV is null)";
		}
		
		if($stat == "normal"){
			$cond .= " AND B.YSTAT = 'N'";
		}else if($stat == "ystat"){
			$cond .= " AND B.YSTAT = 'Y'";
		}
		
		$sql = "
				IF OBJECT_ID('tempdb..#main') IS NOT NULL DROP TABLE #main
				select *
				into #main
				from(	
					SELECT A.LOCAT, A.CONTNO, B.SDATE, (SELECT SUM(DAMT) FROM {$this->MAuth->getdb('ARPAY')} WHERE CONTNO=A.CONTNO)-(SELECT CASE WHEN SUM(PAYMENT) IS NOT NULL 
					THEN  SUM(PAYMENT) ELSE 0 END FROM {$this->MAuth->getdb('ARPAY')} WHERE DDATE<=GETDATE() AND CONTNO=A.CONTNO) AS DAMT, (SELECT MAX(NOPAY) FROM {$this->MAuth->getdb('ARPAY')} 
					WHERE CONTNO=A.CONTNO )AS MNOPAY, A.N_DAMT, A.V_DAMT, A.NOPAY, A.DDATE, B.TOTPRC, B.VATPRC, A.TAXINV, A.TAXDT
					FROM {$this->MAuth->getdb('ARPAY')} A   
					LEFT OUTER JOIN {$this->MAuth->getdb('ARMAST')} B ON A.CONTNO=B.CONTNO  
					WHERE A.DDATE BETWEEN '".$FRMDATE."' AND '".$TODATE."' ".$cond."
				)main
		";//echo $sql; 
		$query = $this->db->query($sql);
		
		$sql = "
				select LOCAT, CONTNO, convert(char,SDATE,112) as SDATE, DAMT, MNOPAY, N_DAMT, V_DAMT, NOPAY, convert(char,DDATE,112) as DDATE, 
				TOTPRC, VATPRC, TAXINV, convert(char,TAXDT,112) as TAXDT
				from #main
				ORDER BY DDATE, CONTNO
		";//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$sql = "
				select 'รวมทั้งสิ้น '+convert(nvarchar(7),COUNT(CONTNO))+' รายการ' as Total, sum(DAMT) as sumDAMT, sum(N_DAMT) as sumN_DAMT, 
				sum(V_DAMT) as sumV_DAMT, sum(TOTPRC) as sumTOTPRC, sum(VATPRC) as sumVATPRC
				from #main
		";//echo $sql; exit;
		$query2 = $this->db->query($sql);
		
		$head = ""; $html = ""; $head2 = "";  $report = ""; $sumreport = ""; $sumreport2 = ""; $i = 0; 
		
		$head = "<tr style='height:25px;background-color:#D3ECDC;'>
					<th style='vertical-align:middle;taxt-align:left;display:none;'>#</th>
					<th style='vertical-align:middle;taxt-align:left;'>สาขา</th>
					<th style='vertical-align:middle;taxt-align:left;'>เลขที่สัญญา</th>
					<th style='vertical-align:middle;taxt-align:center;'>วันที่ขาย</th>
					<th style='vertical-align:middle;taxt-align:right;'>ยอดลูกหนี้คงเหลือ</th>
					<th style='vertical-align:middle;taxt-align:center;'>จน.งวดทั้งหมด</th>
					<th style='vertical-align:middle;taxt-align:right;'>มูลค่าค่างวด</th>
					<th style='vertical-align:middle;taxt-align:right;'>ภาษีค่างวด</th>
					<th style='vertical-align:middle;taxt-align:center;'>งวดที่</th>
					<th style='vertical-align:middle;taxt-align:center;'>วันดิว</th>
					<th style='vertical-align:middle;taxt-align:right;'>ราคาขาย</th>
					<th style='vertical-align:middle;taxt-align:right;'>ภาษีขาย</th>
					<th style='vertical-align:middle;taxt-align:center;'>เลขที่ใบกำกับ</th>
					<th style='vertical-align:middle;taxt-align:center;'>วันที่ใบกำกับ</th>
				</tr>
		";
		
		$head2 = "<tr>
					<th style='vertical-align:middle;taxt-align:left;'>#</th>
					<th style='vertical-align:middle;taxt-align:left;'>สาขา</th>
					<th style='vertical-align:middle;taxt-align:left;'>เลขที่สัญญา</th>
					<th style='vertical-align:middle;taxt-align:center;'>วันที่ขาย</th>
					<th style='vertical-align:middle;taxt-align:right;'>ยอดลูกหนี้คงเหลือ</th>
					<th style='vertical-align:middle;taxt-align:center;'>จน.งวดทั้งหมด</th>
					<th style='vertical-align:middle;taxt-align:right;'>มูลค่าค่างวด</th>
					<th style='vertical-align:middle;taxt-align:right;'>ภาษีค่างวด</th>
					<th style='vertical-align:middle;taxt-align:center;'>งวดที่</th>
					<th style='vertical-align:middle;taxt-align:center;'>วันดิว</th>
					<th style='vertical-align:middle;taxt-align:right;'>ราคาขาย</th>
					<th style='vertical-align:middle;taxt-align:right;'>ภาษีขาย</th>
					<th style='vertical-align:middle;taxt-align:center;'>เลขที่ใบกำกับ</th>
					<th style='vertical-align:middle;taxt-align:center;'>วันที่ใบกำกับ</th>
				</tr>
		";
		
		$NRow = 1;
		if($query->row()){
			foreach($query->result() as $row){$i++;
				$html .= "
					<tr class='trow' seq=".$NRow.">
						<td seq=".$NRow." style='display:none;'>".$NRow++."</td>
						<td>".$row->LOCAT."</td>
						<td>".$row->CONTNO."</td>
						<td align='center'>".$this->Convertdate(2,$row->SDATE)."</td>
						<td align='right'>".number_format($row->DAMT,2)."</td>
						<td align='center'>".$row->MNOPAY."</td>
						<td align='right'>".number_format($row->N_DAMT)."</td>
						<td align='right'>".number_format($row->V_DAMT,2)."</td>
						<td align='center'>".$row->NOPAY."</td>
						<td align='center'>".$this->Convertdate(2,$row->DDATE)."</td>
						<td align='right'>".number_format($row->TOTPRC,2)."</td>
						<td align='right'>".number_format($row->VATPRC,2)."</td>
						<td align='center'>".$row->TAXINV."</td>
						<td align='center'>".$this->Convertdate(2,$row->TAXDT)."</td>
					</tr>
				";	
			}
		}
		
		
		$No = 1;
		if($query->row()){
			foreach($query->result() as $row){
				$report .= "
					<tr class='trow'>
						<td style='mso-number-format:\"\@\";'>".$No++."</td>
						<td style='mso-number-format:\"\@\";'>".$row->LOCAT."</td>
						<td style='mso-number-format:\"\@\";'>".$row->CONTNO."</td>
						<td style='mso-number-format:\"\@\";taxt-align:center;'>".$this->Convertdate(2,$row->SDATE)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->DAMT,2)."</td>
						<td style='mso-number-format:\"\@\";taxt-align:center;'>".$row->MNOPAY."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->N_DAMT)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->V_DAMT,2)."</td>
						<td style='mso-number-format:\"\@\";taxt-align:center;'>".$row->NOPAY."</td>
						<td style='mso-number-format:\"\@\";taxt-align:center;'>".$this->Convertdate(2,$row->DDATE)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->TOTPRC,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->VATPRC,2)."</td>
						<td style='mso-number-format:\"\@\";taxt-align:center;'>".$row->TAXINV."</td>
						<td style='mso-number-format:\"\@\";taxt-align:center;'>".$this->Convertdate(2,$row->TAXDT)."</td>
					</tr>
				";	
			}
		}
		
		if($query2->row()){
			foreach($query2->result() as $row){
				$sumreport .= "
					<tr class='trow' style='height:25px;background-color:#D3ECDC;'>
						<th style='border:0px;vertical-align:middle;text-align:left;' colspan='3'>".$row->Total."</th>
						<th style='border:0px;vertical-align:middle;text-align:right;'>".number_format($row->sumDAMT,2)."</th>
						<th style='border:0px;vertical-align:middle;text-align:left;'></th>
						<th style='border:0px;vertical-align:middle;text-align:right;'>".number_format($row->sumN_DAMT,2)."</th>
						<th style='border:0px;vertical-align:middle;text-align:right;'>".number_format($row->sumV_DAMT,2)."</th>
						<th style='border:0px;vertical-align:middle;text-align:right;' colspan='2'></th>
						<th style='border:0px;vertical-align:middle;text-align:right;'>".number_format($row->sumTOTPRC,2)."</th>
						<th style='border:0px;vertical-align:middle;text-align:right;'>".number_format($row->sumVATPRC,2)."</th>
						<th style='border:0px;vertical-align:middle;text-align:right;' colspan='2'></th>
					</tr>
				";	
			}
		}
		
		if($query2->row()){
			foreach($query2->result() as $row){
				$sumreport2 .= "
					<tr class='trow'>
						<th style='mso-number-format:\"\@\";text-align:left;' colspan='4'>".$row->Total."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumDAMT,2)."</th>
						<th ></th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumN_DAMT,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumV_DAMT,2)."</th>
						<th colspan='2'></th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumTOTPRC,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumVATPRC,2)."</th>
						<th colspan='2'></th>
					</tr>
				";	
			}
		}
		
		if($i>0){
			$html = "
				<div id='table-fixed-ReportVatdue' class='col-sm-12' style='height:100%;width:100%;overflow:auto;font-size:9pt;'>
					<table id='table-ReportVatdue' style='background-color:white;' class='col-sm-12 display table table-bordered' cellspacing='0' width='calc(100% - 1px)'>
						<thead>
						<tr style='height:40px;'>
							<th colspan='13' style='font-size:12pt;border:0px;vertical-align;middle;text-align:center;'>รายงานภาษีครบกำหนด</th>
						</tr>
						<tr style='height:25px;'>
							<td colspan='13' style='border-bottom:1px solid #ddd;vertical-align;middle;text-align:center;'>จากวันที่ ".$_REQUEST["FRMDATE"]." - ".$_REQUEST["TODATE"]." ".$rpcond."   ออกรายงาน ณ วันที่ ".$this->today('today')."</td>
						</tr>
						".$head."
						</thead>	
						<tbody style='height: 10px !important; overflow: scroll;'>
						".$html."
						</tbody>	
						<tfoot>
						".$sumreport."
						</tfoot>
					</table>
				</div>
			";
		}else{
			$html="<font style='color:red;'>ไม่มีข้อมูล</font>";
		}
		
		$report = "
			<div id='table-fixed-ReportVatdue2' class='col-sm-12' style='height:100%;width:100%;overflow:auto;'>
				<table id='table-ReportVatdue2' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='100%'>
					<thead>
						<tr>
							<th colspan='14' style='font-size:12pt;border:0px;text-align:center;'>รายงานภาษีครบกำหนด</th>
						</tr>
						<tr>
							<td colspan='14' style='border:0px;text-align:center;'>จากวันที่ ".$_REQUEST["FRMDATE"]." - ".$_REQUEST["TODATE"]." ".$rpcond."  ออกรายงาน ณ วันที่ ".$this->today('today')."</td>
						</tr>
						".$head2."
					</thead>	
					<tbody>
						".$report."
						".$sumreport2."
					</tbody>
				</table>
			</div>
		";
		
		$response = array("html"=>$html, "report"=>$report, "reporttoday"=>str_replace('/','-',$this->today('today')));
		echo json_encode($response);
	}
	
	function conditiontopdf(){
		$data 	= 	array();
		$data[] = 	urlencode(
						$_REQUEST["LOCAT1"].'||'.
						$_REQUEST["CONTNO1"].'||'.
						$_REQUEST["BILLCOL1"].'||'.
						$_REQUEST["FRMDATE"].'||'.
						$_REQUEST["TODATE"].'||'.
						$_REQUEST["invoice"].'||'.
						$_REQUEST["stat"]
					);
		echo json_encode($this->generateData($data,"encode"));
	}
	
	function pdf(){
		ini_set("memory_limit","-1");
		ini_set("pcre.backtrack_limit", "100000000");
		
		$data 	= array();
		$data[] = $_GET["condpdf"];
		$arrs 	= $this->generateData($data,"decode");
		$arrs[0]= urldecode($arrs[0]);
		$tx 	= explode("||",$arrs[0]);
		$LOCAT1		= $tx[0];
		$CONTNO1 	= str_replace(chr(0),'',$tx[1]);
		$BILLCOL1 	= str_replace(chr(0),'',$tx[2]);
		$FRMDATE 	= $this->Convertdate(1,$tx[3]);
		$TODATE 	= $this->Convertdate(1,$tx[4]);
		$invoice 	= $tx[5];
		$stat 		= $tx[6];
		
		$layout = "A4";
		
		$cond = ""; $rpcond = "";
		
		if($LOCAT1 != ""){
			$cond .= " AND (A.LOCAT LIKE '".$LOCAT1."%')";
			$rpcond .= "  สาขา ".$LOCAT1;
		}
		
		if($CONTNO1 != ""){
			$cond .= " AND (A.CONTNO LIKE '".$CONTNO1."%') ";
			$rpcond .= "  เลขที่สัญญา ".$CONTNO1;
		}
		
		if($BILLCOL1 != ""){
			$cond .= " AND (B.BILLCOLL LIKE '".$BILLCOL1."%') ";
			$rpcond .= "  รหัสพนักงานเก็บเงิน ".$BILLCOL1;
		}

		if($invoice == "null"){
			$cond .= " AND (A.TAXINV = '' or A.TAXINV is null)";
		}
		
		if($stat == "normal"){
			$cond .= " AND B.YSTAT = 'N'";
		}else if($stat == "ystat"){
			$cond .= " AND B.YSTAT = 'Y'";
		}
		
		$sql = "
				IF OBJECT_ID('tempdb..#main') IS NOT NULL DROP TABLE #main
				select *
				into #main
				from(	
					SELECT A.LOCAT, A.CONTNO, B.SDATE, (SELECT SUM(DAMT) FROM {$this->MAuth->getdb('ARPAY')} WHERE CONTNO=A.CONTNO)-(SELECT CASE WHEN SUM(PAYMENT) IS NOT NULL 
					THEN  SUM(PAYMENT) ELSE 0 END FROM {$this->MAuth->getdb('ARPAY')} WHERE DDATE<=GETDATE() AND CONTNO=A.CONTNO) AS DAMT, (SELECT MAX(NOPAY) FROM {$this->MAuth->getdb('ARPAY')} 
					WHERE CONTNO=A.CONTNO )AS MNOPAY, A.N_DAMT, A.V_DAMT, A.NOPAY, A.DDATE, B.TOTPRC, B.VATPRC, A.TAXINV, A.TAXDT
					FROM {$this->MAuth->getdb('ARPAY')} A   
					LEFT OUTER JOIN {$this->MAuth->getdb('ARMAST')} B ON A.CONTNO=B.CONTNO  
					WHERE A.DDATE BETWEEN '".$FRMDATE."' AND '".$TODATE."' ".$cond."
				)main
		";//echo $sql; 
		$query = $this->db->query($sql);
		
		$sql = "
				select LOCAT, CONTNO, convert(char,SDATE,112) as SDATE, DAMT, MNOPAY, N_DAMT, V_DAMT, NOPAY, convert(char,DDATE,112) as DDATE, 
				TOTPRC, VATPRC, TAXINV, convert(char,TAXDT,112) as TAXDT
				from #main
				ORDER BY DDATE, CONTNO
		";//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$sql = "
				select 'รวมทั้งสิ้น '+convert(nvarchar(7),COUNT(CONTNO))+' รายการ' as Total, sum(DAMT) as sumDAMT, sum(N_DAMT) as sumN_DAMT, 
				sum(V_DAMT) as sumV_DAMT, sum(TOTPRC) as sumTOTPRC, sum(VATPRC) as sumVATPRC
				from #main
		";//echo $sql; exit;
		$query2 = $this->db->query($sql);
		
		$sql = "
				SELECT COMP_NM, COMP_ADR1, COMP_ADR2, TELP, TAXID FROM {$this->MAuth->getdb('CONDPAY')}
		";//echo $sql; exit;
		$query3 = $this->db->query($sql);
		
		$row1		= $query3->row();
		$COMP_NM 	= $row1->COMP_NM;
		$COMP_ADR1 	= $row1->COMP_ADR1;
		$COMP_ADR2 	= $row1->COMP_ADR2;
		$TELP 		= $row1->TELP;
		$TAXID 		= $row1->TAXID;
		
		$LOCATCD = ""; $LOCATNM = ""; $LOCADDR1 = ""; $LOCADDR2 = "";
		if($LOCAT1 != ""){
			$sql = "
					SELECT LOCATCD,LOCATNM,LOCADDR1,LOCADDR2 from {$this->MAuth->getdb('INVLOCAT')} where LOCATCD = '".$LOCAT1."'
			";//echo $sql; exit;
			$query4 = $this->db->query($sql);
			
			$row2 		= $query4->row();
			$LOCATCD 	= $row2->LOCATCD;
			$LOCATNM 	= $row2->LOCATNM;
			$LOCADDR1 	= $row2->LOCADDR1;
			$LOCADDR2 	= $row2->LOCADDR2;
		}else{
			$LOCATNM 	= "";
			$LOCADDR1 	= $COMP_ADR1;
			$LOCADDR2 	= $COMP_ADR2;
		}
		
		$head = ""; $html = ""; $i=0; 
		
		$head = "
				<tr>
					<th width='50px' 	align='left' 	style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;'>สาขา</th>
					<th width='90px' 	align='left' 	style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;'>เลขที่สัญญา</th>
					<th width='70px' 	align='left' 	style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;'>วันที่ขาย</th>
					<th width='90px' 	align='right' 	style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;'>ลูกหนี้คงเหลือ</th>
					<th width='50px' 	align='center' 	style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;'>จน.งวด</th>
					<th width='90px' 	align='right' 	style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;'>มูลค่าค่างวด</th>
					<th width='90px' 	align='right' 	style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;'>ภาษีค่างวด</th>
					<th width='50px' 	align='center' 	style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;'>งวดที่</th>
					<th width='70px' 	align='center' 	style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;'>วันดิว</th>
					<th width='90px' 	align='right' 	style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;'>ราคาขาย</th>
					<th width='90px' 	align='right' 	style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;'>ภาษีขาย</th>
					<th width='90px' 	align='center' 	style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;'>เลขที่ใบกำกับ</th>
					<th width='80px' 	align='center' 	style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;'>วันที่ใบกำกับ</th>
				</tr>
		";
		
		$No = 1;
		if($query->row()){
			foreach($query->result() as $row){	
				$html .= "
					<tr class='trow' seq=".$No.">
						<td width='50px' 	height='25px'	align='left'>".$row->LOCAT."</td>
						<td width='90px' 	height='25px'	align='left'>".$row->CONTNO."</td>
						<td width='70px' 	height='25px'	align='left'>".$this->Convertdate(2,$row->SDATE)."</td>
						<td width='90px' 	height='25px'	align='right'>".number_format($row->DAMT,2)."</td>
						<td width='50px' 	height='25px'	align='center'>".$row->MNOPAY."</td>
						<td width='90px' 	height='25px'	align='right'>".number_format($row->N_DAMT)."</td>
						<td width='90px' 	height='25px'	align='right'>".number_format($row->V_DAMT,2)."</td>
						<td width='50px' 	height='25px'	align='center'>".$row->NOPAY."</td>
						<td width='70px' 	height='25px'	align='center'>".$this->Convertdate(2,$row->DDATE)."</td>
						<td width='90px' 	height='25px'	align='right'>".number_format($row->TOTPRC,2)."</td>
						<td width='90px' 	height='25px'	align='right'>".number_format($row->VATPRC,2)."</td>
						<td width='90px' 	height='25px'	align='center'>".$row->TAXINV."</td>
						<td width='80px' 	height='25px'	align='center'>".$this->Convertdate(2,$row->TAXDT)."</td>
					</tr>
				";	
			}
		}
		
		if($query2->row()){
			foreach($query2->result() as $row){	
				$html .= "
					<tr class='trow' style='background-color:#ebebeb;'>
						<td style='border-top:0.1px solid black;border-bottom:0.1px solid black;border-left:0.1px solid black;vertical-align:middle;text-align:left;' colspan='3'>".$row->Total."</td>
						<td style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:middle;text-align:right;'>".number_format($row->sumDAMT,2)."</td>
						<td style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:middle;text-align:left;'></td>
						<td style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:middle;text-align:right;'>".number_format($row->sumN_DAMT,2)."</td>
						<td style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:middle;text-align:right;'>".number_format($row->sumV_DAMT,2)."</td>
						<td style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:middle;text-align:right;' colspan='2'></td>
						<td style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:middle;text-align:right;'>".number_format($row->sumTOTPRC,2)."</td>
						<td style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:middle;text-align:right;'>".number_format($row->sumVATPRC,2)."</td>
						<td style='border-top:0.1px solid black;border-bottom:0.1px solid black;border-right:0.1px solid black;vertical-align:middle;text-align:right;' colspan='2'></td>
					</tr>
					
				";	
			}
		}
		$body = "<table class='wf fs8' cellspacing='0'>".$html."</table>";
		
		$mpdf = new \Mpdf\Mpdf([
			'mode' => 'utf-8', 
			'format' => $layout,
			'margin_top' => ($layout == "A4-L" ? "70" : "50"), 	//default = 16
			'margin_left' => 10, 	//default = 15
			'margin_right' => 10, 	//default = 15
			'margin_bottom' => 9, 	//default = 16
			'margin_header' => 9, 	//default = 9
			'margin_footer' => 9, 	//default = 9
		]);
		
		$stylesheet = "
			<style>
				body { font-family: garuda;font-size:9pt; }
				.wf { width:100%; }
				.fs8 { font-size:8.5pt; }
				.fs9 { font-size:9pt; }
				.h30 { height:30px; }
				.bor { border-top:0.1px solid black;border-bottom:0.1px solid black;}
			</style>
		";
		
		$header = "
			<table class='wf fs9' cellspacing='0' style='border-collapse:collapse;line-height:23px;overflow:wrap;vertical-align:text-top;'>
				<tr>
					<th colspan='13' style='font-size:11pt;'>".$COMP_NM."<br>รายงานภาษีครบกำหนด</th>
				</tr>
				<tr>
					<td colspan='13' style='height:35px;text-align:center;'>".$rpcond." จากวันที่ วันที่ ".$tx[3]." ถึงวันที่ ".$tx[4]."</td>
				</tr>
				<tr>
					<td colspan='2' align='left'>ชื่อผู้ประกอบการ</td>
					<td colspan='11' align='left'>".$COMP_NM."</td>
				</tr>
				<tr>
					<td colspan='2' align='left'>ชื่อสถานที่ประกอบการ</td>
					<td colspan='11' align='left'>".$LOCADDR1." ".$LOCADDR2."</td>
				</tr>
				<tr>
					<td colspan='2' align='left'>เลขประจำตัวผู้เสียภาษี</td>
					<td colspan='6' align='left'>".$TAXID."</td>
					<td colspan='5' align='right'>เรียงรายงาน: ".$ordername."</td>
				</tr>
				<tr>
					<td colspan='2' align='left'>วันที่พิมพ์รายงาน</td>
					<td colspan='6' align='left'>".date('d/m/').(date('Y')+543)." ".date('H:i')."</td>
					<td colspan='5' align='right'>หน้าที่ : {PAGENO} / {nb} &emsp;&emsp;</td>
					
				</tr>
				".$head."
			</table>
		";		
		$mpdf->SetHTMLHeader($header);
		$mpdf->WriteHTML($body.$stylesheet);
		$mpdf->fontdata['qanela'] = array('R' => "QanelasSoft-Regular.ttf",'B' => "QanelasSoft-Bold.ttf",); //แก้ปริ้นแล้วอ่านไม่ออก
		$mpdf->Output();
		
	}
}