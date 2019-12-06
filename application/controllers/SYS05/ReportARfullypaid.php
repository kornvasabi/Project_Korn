<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//BEE+
class ReportARfullypaid extends MY_Controller {
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
						<div class='col-sm-12 col-xs-12' style='background-color:#4479aa;border:5px solid white;height:75px;text-align:center;font-size:12pt;color:white;font-weight:bold;'>	
							<br>รายงานเช่าซื้อชำระเงินครบแล้ว<br>
						</div>
						<div class='col-sm-6 col-xs-6 col-sm-offset-3'>
							<br>
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group'>
									สาขา
									<select id='LOCAT1' class='form-control input-sm' data-placeholder='สาขา'></select>
								</div>
							</div>
						</div>
						<div class='col-sm-6 col-xs-6 col-sm-offset-3'>	
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
						</div>
						<div class='col-sm-6 col-xs-6 col-sm-offset-3'>	
							<div class='col-sm-12 col-xs-12'>	
								<div class='form-group'>
									รูปแบบการพิมพ์
									<div class='col-sm-12 col-xs-12' style='border:0.1px dotted #d6d6d6;'>	
										<div class='col-sm-6 col-xs-6'>
											<div class='form-group'>
												<br>
												<input type= 'radio' id='hor' name='layout' checked> แนวนอน
											</div>
										</div>
										<div class='col-sm-6 col-xs-6'>
											<div class='form-group'>
												<br>
												<input type= 'radio' id='ver' name='layout'> แนวตั้ง
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class='col-sm-12 col-xs-12'>	
								<br>
								<div class='form-group'>
									เงื่อนไขวันที่
									<div class='col-sm-12 col-xs-12' style='border:0.1px dotted #d6d6d6;'>	
										<div class='col-sm-6 col-xs-6'>
											<div class='form-group'>
												<br>
												<input type= 'radio' id='condlpayd' name='conddate' checked> ตามวันชำระ
											</div>
										</div>
										<div class='col-sm-6 col-xs-6'>
											<div class='form-group'>
												<br>
												<input type= 'radio' id='condsdate' name='conddate'> ตามวันที่ขาย
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class='col-sm-12 col-xs-12'>	
								<br>
								<div class='form-group'>
									เรียงลำดับข้อมูล
									<div class='col-sm-12 col-xs-12' style='border:0.1px dotted #d6d6d6;'>	
										<div class='col-sm-6 col-xs-6'>
											<div class='form-group'>
												<br>
												<input type= 'radio' id='contno' name='orderby' checked> เลขที่สัญญา
												<br>
												<input type= 'radio' id='cuscod' name='orderby'> ตามรหัสลูกค้า
												<br>
												<input type= 'radio' id='lpayd' name='orderby'> วันชำระล่าสุด
											</div>
										</div>
										<div class='col-sm-6 col-xs-6'>
											<div class='form-group'>
												<br>
												<input type= 'radio' id='contstat' name='orderby'> สถานะบัญชี
												<br>
												<input type= 'radio' id='sdate' name='orderby'> วันที่ขาย
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class='row' style='height:10%;'>
						<div class='col-sm-12 col-xs-12'><br>	
							<button id='btnt1search' class='btn btn-primary btn-sm' style='width:100%;font-size:10.5pt;'><span class='glyphicon glyphicon-search'> แสดง</span></button>
						</div>
					</div>
				</div>
			</div>
		";
		
		$html.= "<script src='".base_url('public/js/SYS05/ReportARfullypaid.js')."'></script>";
		echo $html;
	}
	
	function search(){
		$LOCAT1		= $_REQUEST["LOCAT1"];
		$FRMDATE 	= $this->Convertdate(1,$_REQUEST["FRMDATE"]);
		$TODATE 	= $this->Convertdate(1,$_REQUEST["TODATE"]);
		$conddate 	= $_REQUEST["conddate"];
		$orderby 	= $_REQUEST["orderby"];
	
		$cond = ""; $rpcond = ""; $datecond = "";
		
		if($LOCAT1 != ""){
			$cond .= " AND (A.LOCAT LIKE '%".$LOCAT1."%')";
			$rpcond .= "  สาขา ".$LOCAT1;
		}
		
		if($conddate == 'A.LPAYD'){
			$datecond = "ชำระงวดสุดท้ายเมื่อวันที่";
		}else{
			$datecond = "จากวันที่ขาย";
		}
		
		$sql = "
				IF OBJECT_ID('tempdb..#main') IS NOT NULL DROP TABLE #main
				select *
				into #main
				from(
					select A.LOCAT, A.CONTNO, A.CUSCOD, B.SNAM+B.NAME1+' '+B.NAME2 as CUSNAME, A.CONTSTAT, A.SDATE, convert(nvarchar,A.SDATE,112) as SDATES,
					A.TOTPRC, convert(nvarchar,A.LDATE,112) as LDATE, A.LPAYD, convert(nvarchar,A.LPAYD,112) as LPAYDS, A.LPAYA, A.BILLCOLL, A.CHECKER,
					(select MAX(E.BILLNO) from {$this->MAuth->getdb('CHQMAS')} E, {$this->MAuth->getdb('CHQTRAN')} D where D.TMBILL=E.TMBILL AND A.LPAYD=E.BILLDT) AS BILLNO, 
					(select MAX(DISCT) from {$this->MAuth->getdb('CHQTRAN')} where contno=A.contno)AS DISCT 
					from {$this->MAuth->getdb('ARMAST')} A
					left join {$this->MAuth->getdb('CUSTMAST')} B on A.CUSCOD = B.CUSCOD
					left join {$this->MAuth->getdb('INVTRAN')} C on A.STRNO = C.STRNO
					where (".$conddate." BETWEEN '".$FRMDATE."' and '".$TODATE."') AND A.TOTPRC > 0 AND (A.TOTPRC = A.SMPAY )  
					".$cond."   
				)main
		";//echo $sql; 
		$query = $this->db->query($sql);
		
		$sql = "
				select LOCAT, CONTNO, CUSCOD, CUSNAME, CONTSTAT, SDATE, SDATES, TOTPRC, LDATE, LPAYD, LPAYDS, LPAYA, BILLCOLL, CHECKER,
				BILLNO, DISCT 
				from #main
				order by ".$orderby."
		";//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$sql = "
				select 'รวมทั้งหมด' as Total, sum(TOTPRC) as sumTOTPRC, sum(LPAYA) as sumLPAYA
				from #main
		";
		//echo $sql; exit;
		$query2 = $this->db->query($sql);
		
		
		$head = ""; $html = ""; $head2 = "";  $report = ""; $sumreport = ""; $sumreport2 = ""; $i = 0; 
		
		$head = "<tr>
				<th style='display:none;'>#</th>
				<th style='vertical-align:top;'>สาขา</th>
				<th style='vertical-align:top;'>เลขที่สัญญา</th>
				<th style='vertical-align:top;'>รหัสลูกค้า</th>
				<th style='vertical-align:top;'>ชื่อ - นามสกุล</th>
				<th style='vertical-align:top;'>สถานะบัญชี</th>
				<th style='vertical-align:top;'>วันที่ขาย</th>
				<th style='vertical-align:top;text-align:right;'>ราคาขาย</th> 
				<th style='vertical-align:top;text-align:right;'>วันดิวงวดสุดท้าย</th>
				<th style='vertical-align:top;text-align:right;'>วันที่ชำระงวดสุดท้าย</th>
				<th style='vertical-align:top;text-align:right;'>จำนวนเงินชำระล่าสุด</th>
				<th style='vertical-align:top;'>พนง.เก็บเงิน</th>
				<th style='vertical-align:top;'>Checker</th>
				<th style='vertical-align:top;'>เลขที่ใบเสร็จ</th>
				<th style='vertical-align:top;text-align:right;'>ส่วนลด</th>
				</tr>
		";
		
		$head2 = "<tr>
					<th style='vertical-align:middle;'>#</th>
					<th style='vertical-align:top;'>สาขา</th>
					<th style='vertical-align:top;'>เลขที่สัญญา</th>
					<th style='vertical-align:top;'>รหัสลูกค้า</th>
					<th style='vertical-align:top;'>ชื่อ - นามสกุล</th>
					<th style='vertical-align:top;'>สถานะบัญชี</th>
					<th style='vertical-align:top;'>วันที่ขาย</th>
					<th style='vertical-align:top;text-align:right;'>ราคาขาย</th> 
					<th style='vertical-align:top;text-align:right;'>วันดิวงวดสุดท้าย</th>
					<th style='vertical-align:top;text-align:right;'>วันที่ชำระงวดสุดท้าย</th>
					<th style='vertical-align:top;text-align:right;'>จำนวนเงินชำระล่าสุด</th>
					<th style='vertical-align:top;'>พนง.เก็บเงิน</th>
					<th style='vertical-align:top;'>Checker</th>
					<th style='vertical-align:top;'>เลขที่ใบเสร็จ</th>
					<th style='vertical-align:top;text-align:right;'>ส่วนลด</th>
				</tr>
		";
		
		$NRow = 1;
		if($query->row()){
			foreach($query->result() as $row){$i++;
				$html .= "
					<tr class='trow' seq=".$NRow.">
						<td seq=".$NRow++." style='display:none;'></td>
						<td>".$row->LOCAT."</td>
						<td>".$row->CONTNO."</td>
						<td>".$row->CUSCOD."</td>
						<td>".$row->CUSNAME."</td>
						<td align='center'>".$row->CONTSTAT."</td>
						<td>".$this->Convertdate(2,$row->SDATES)."</td>
						<td align='right'>".number_format($row->TOTPRC,2)."</td>
						<td align='right'>".$this->Convertdate(2,$row->LDATE)."</td>
						<td align='right'>".$this->Convertdate(2,$row->LPAYDS)."</td>
						<td align='right'>".number_format($row->LPAYA,2)."</td>
						<td>".$row->BILLCOLL."</td>
						<td>".$row->CHECKER."</td>
						<td>".$row->BILLNO."</td>
						<td align='right'>".number_format($row->DISCT,2)."</td>
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
						<td style='mso-number-format:\"\@\";'>".$row->CUSCOD."</td>
						<td style='mso-number-format:\"\@\";'>".$row->CUSNAME."</td>
						<td style='mso-number-format:\"\@\";text-align:center;'>".$row->CONTSTAT."</td>
						<td style='mso-number-format:\"\@\";text-align:center;'>".$this->Convertdate(2,$row->SDATES)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->TOTPRC,2)."</td>
						<td style='mso-number-format:\"\@\";text-align:right;'>".$this->Convertdate(2,$row->LDATE)."</td>
						<td style='mso-number-format:\"\@\";text-align:right;'>".$this->Convertdate(2,$row->LPAYDS)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->LPAYA,2)."</td>
						<td style='mso-number-format:\"\@\";'>".$row->BILLCOLL."</td>
						<td style='mso-number-format:\"\@\";'>".$row->CHECKER."</td>
						<td style='mso-number-format:\"\@\";'>".$row->BILLNO."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->DISCT,2)."</td>
					</tr>
				";	
			}
		}
		
		if($query2->row()){
			foreach($query2->result() as $row){
				$sumreport = "
					<tr>
						<th colspan='6' style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;text-align:center;'>".$row->Total."</th>
						<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;text-align:right;'>".number_format($row->sumTOTPRC)."</th>
						<th colspan='2' style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;text-align:right;'></th>
						<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;text-align:right;'>".number_format($row->sumLPAYA,2)."</th>
						<th colspan='4' style='border:0px;text-align:right;'></th>
					</tr>
				";	
			}
		}
		
		if($query2->row()){
			foreach($query2->result() as $row){
				$sumreport2 = "
					<tr class='trow'>
						<th style='mso-number-format:\"\@\";text-align:center;' colspan='7'>".$row->Total."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumTOTPRC,2)."</th>
						<th style='mso-number-format:\"\@\";text-align:center;' colspan='2'></th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumLPAYA,2)."</th>
						<th style='mso-number-format:\"\@\";text-align:center;' colspan='4'></th>
					</tr>
				";	
			}
		}
			
		if($i>0){
			$html = "
				<div id='table-fixed-ReportARfullypaid' class='col-sm-12' style='height:100%;width:100%;overflow:auto;font-size:8pt;'>
					<table id='table-ReportARfullypaid' style='background-color:white;' class='col-sm-12 display table table-bordered' cellspacing='0' width='calc(100% - 1px)'>
						<thead>
						<tr>
							<th colspan='14' style='font-size:12pt;border:0px;text-align:center;'>รายงานเช่าซื้อชำระเงินครบแล้ว</th>
						</tr>
						<tr>
							<td colspan='14' style='border-bottom:1px solid #ddd;text-align:center;'>".$datecond." ".$_REQUEST["FRMDATE"]." - ".$_REQUEST["TODATE"]." ".$rpcond."  ออกรายงาน ณ วันที่ ".$this->today('today')."</td>
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
			<div id='table-fixed-ReportARfullypaid2' class='col-sm-12' style='height:100%;width:100%;overflow:auto;'>
				<table id='table-ReportARfullypaid2' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='100%'>
					<thead>
						<tr>
							<th colspan='15' style='font-size:12pt;border:0px;text-align:center;'>รายงานเช่าซื้อชำระเงินครบแล้ว</th>
						</tr>
						<tr>
							<td colspan='15' style='border:0px;text-align:center;'>".$datecond." ".$_REQUEST["FRMDATE"]." - ".$_REQUEST["TODATE"]." ".$rpcond." ออกรายงาน ณ วันที่ ".$this->today('today')."</td>
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
		$data[] = 	urlencode($_REQUEST["LOCAT1"].'||'.$_REQUEST["FRMDATE"].'||'.$_REQUEST["TODATE"].'||'.$_REQUEST["conddate"].'||'.$_REQUEST["orderby"].'||'.$_REQUEST["layout"]);
		echo json_encode($this->generateData($data,"encode"));
	}

	function pdf(){
		$data 	= array();
		$data[] = $_GET["condpdf"];
		$arrs 	= $this->generateData($data,"decode");
		$arrs[0]= urldecode($arrs[0]);
		$tx 	= explode("||",$arrs[0]);
		$LOCAT1		= $tx[0];
		$FRMDATE 	= $this->Convertdate(1,$tx[1]);
		$TODATE 	= $this->Convertdate(1,$tx[2]);
		$conddate 	= $tx[3];
		$orderby 	= $tx[4];
		$layout 	= $tx[5];
		
		$cond = ""; $rpcond = ""; $datecond = "";
		
		if($LOCAT1 != ""){
			$cond .= " AND (A.LOCAT LIKE '%".$LOCAT1."%')";
			$rpcond .= "  สาขา ".$LOCAT1;
		}
		
		if($conddate == 'A.LPAYD'){
			$datecond = "ชำระงวดสุดท้ายเมื่อวันที่";
		}else{
			$datecond = "จากวันที่ขาย";
		}
		
		$sql = "
				IF OBJECT_ID('tempdb..#main') IS NOT NULL DROP TABLE #main
				select *
				into #main
				from(
					select A.LOCAT, A.CONTNO, A.CUSCOD, B.SNAM+B.NAME1+' '+B.NAME2 as CUSNAME, A.CONTSTAT, A.SDATE, convert(nvarchar,A.SDATE,112) as SDATES,
					A.TOTPRC, convert(nvarchar,A.LDATE,112) as LDATE, A.LPAYD, convert(nvarchar,A.LPAYD,112) as LPAYDS, A.LPAYA, A.BILLCOLL, A.CHECKER,
					(select MAX(E.BILLNO) from {$this->MAuth->getdb('CHQMAS')} E, {$this->MAuth->getdb('CHQTRAN')} D where D.TMBILL=E.TMBILL AND A.LPAYD=E.BILLDT) AS BILLNO, 
					(select MAX(DISCT) from {$this->MAuth->getdb('CHQTRAN')} where contno=A.contno)AS DISCT 
					from {$this->MAuth->getdb('ARMAST')} A
					left join {$this->MAuth->getdb('CUSTMAST')} B on A.CUSCOD = B.CUSCOD
					left join {$this->MAuth->getdb('INVTRAN')} C on A.STRNO = C.STRNO
					where (".$conddate." BETWEEN '".$FRMDATE."' and '".$TODATE."') AND A.TOTPRC > 0 AND (A.TOTPRC = A.SMPAY )  
					".$cond."   
				)main
		";//echo $sql; 
		$query = $this->db->query($sql);
		
		$sql = "
				select LOCAT, CONTNO, CUSCOD, CUSNAME, CONTSTAT, SDATE, SDATES, TOTPRC, LDATE, LPAYD, LPAYDS, LPAYA, BILLCOLL, CHECKER,
				BILLNO, DISCT 
				from #main
				order by ".$orderby."
		";//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$sql = "
				select 'รวมทั้งหมด' as Total, sum(TOTPRC) as sumTOTPRC, sum(LPAYA) as sumLPAYA
				from #main
		";
		//echo $sql; exit;
		$query2 = $this->db->query($sql);
		
		$head = ""; $html = ""; $i=0; 

		$head = "
				<tr>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>#</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>สาขา</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>เลขที่สัญญา</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>รหัสลูกค้า</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>ชื่อ - นามสกุล</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>สถานะบัญชี</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>วันที่ขาย</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>ราคาขาย</th> 
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>วันดิวงวดสุดท้าย</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>วันที่ชำระงวดสุดท้าย</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>จน.เงินชำระล่าสุด</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>พนง.เก็บเงิน</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>Checker</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>เลขที่ใบเสร็จ</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>ส่วนลด</th>
				</tr>
		";
		
		$No = 1;
		if($query->row()){
			foreach($query->result() as $row){	
				$html .= "
					<tr class='trow' seq=".$No.">
						<td style='width:25px;'>".$No++."</td>
						<td style='width:50px;'>".$row->LOCAT."</td>
						<td style='width:80px;'>".$row->CONTNO."</td>
						<td style='width:80px;'>".$row->CUSCOD."</td>
						<td style='width:150px;'>".$row->CUSNAME."</td>
						<td style='width:70px;' align='center'>".$row->CONTSTAT."</td>
						<td style='width:65px;'>".$this->Convertdate(2,$row->SDATES)."</td>
						<td style='width:80px;' align='right'>".number_format($row->TOTPRC,2)."</td>
						<td style='width:85px;' align='right'>".$this->Convertdate(2,$row->LDATE)."</td>
						<td style='width:110px;' align='right'>".$this->Convertdate(2,$row->LPAYDS)."</td>
						<td style='width:100px;' align='right'>".number_format($row->LPAYA,2)."</td>
						<td style='width:75px;'>&nbsp;&nbsp;".$row->BILLCOLL."</td>
						<td style='width:50px;'>".$row->CHECKER."</td>
						<td style='width:80px;'>".$row->BILLNO."</td>
						<td style='width:60px;' align='right'>".number_format($row->DISCT,2)."</td>
					</tr>
				";	
			}
		}
		
		if($query2->row()){
			foreach($query2->result() as $row){	
				$html .= "
					<tr class='trow bor' style='background-color:#ebebeb;'>
						<td colspan='7' style='text-align:center;vertical-align:middle;'>".$row->Total."</td>
						<td align='right'>".number_format($row->sumTOTPRC,2)."</td>
						<td colspan='2'></td>
						<td align='right'>".number_format($row->sumLPAYA,2)."</td>
						<td colspan='4'></td>
					</tr>
				";	
			}
		}
		
		$mpdf = new \Mpdf\Mpdf([
			'mode' => 'utf-8', 
			'format' => $layout,
			'margin_top' => 10, 	//default = 16
			'margin_left' => 8, 	//default = 15
			'margin_right' => 8, 	//default = 15
			'margin_bottom' => 10, 	//default = 16
			'margin_header' => 9, 	//default = 9
			'margin_footer' => 9, 	//default = 9
		]);
		
		$content = "
			<table class='wf' style='font-size:8pt;height:700px;border-collapse:collapse;line-height:23px;overflow:wrap;vertical-align:text-top;'>
				<tbody>
					<tr>
						<th colspan='15' style='font-size:10pt;'>รายงานเช่าซื้อชำระเงินครบแล้ว</th>
					</tr>
					<tr>
						<td colspan='15' style='font-size:8pt;height:35px;border-bottom:0.1px solid black;text-align:center;'>".$rpcond." ".$datecond." ".$tx[1]." - ".$tx[2]."</td>
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
			<div class='wf pf' style='".($layout == 'A4' ? 'top:1060;left:600;':'top:715;left:960;')." font-size:6pt;'>".date('d/m/').(date('Y')+543)." ".date('H:i')." หน้า {PAGENO} / {nbpg}</div>
		";
		//<div class='wf pf' style='top:1050;left:580;'>{DATE j-m-Y H:s}  {PAGENO} / {nbpg}</div>
		//$mpdf->AddPage('L');	
		$mpdf->SetHTMLHeader($head);	
		$mpdf->WriteHTML($content);	
		$mpdf->Output();
		
	}
}