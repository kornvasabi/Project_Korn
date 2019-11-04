<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//BEE+
class ReportRedemption extends MY_Controller {
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
				<div class='col-sm-12 col-xs-12' style='overflow:auto;font-size:10.5pt;'>					
					<div class='row'>
						<div class='col-sm-12 col-xs-12' style='background-color:#637b9a;border:5px solid white;height:75px;text-align:center;font-size:12pt;color:white;font-weight:bold;'>	
							<br>รายงานการไถ่ถอนรถยึด<br>
						</div>
					</div>
					<br>
					<div class='row'>
						<div class='col-sm-6 col-xs-6 col-sm-offset-3'>	
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group'>
									สาขา
									<select id='LOCAT1' class='form-control input-sm' data-placeholder='สาขา'></select>
								</div>
							</div>
						</div>
					</div>
					<div class='row'>
						<div class='col-sm-6 col-xs-6 col-sm-offset-3'>	
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group' >
									จากวันที่
									<input type='text' id='FROMDATE' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' placeholder='จากวันที่' value='".$this->today('today')."' style='font-size:10.5pt'>
								</div>
							</div>
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group'>
									ถึงวันที่
									<input type='text' id='TODATE' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' placeholder='ถึงวันที่' value='".$this->today('today')."' style='font-size:10.5pt'>
								</div>
							</div>
						</div>
					</div>
					<div class='row'>
						<div class='col-sm-6 col-xs-6 col-sm-offset-3'><br>	
							<div class='col-sm-12 col-xs-12'>	
								<div class='form-group'>
									รูปแบบการพิมพ์
									<div class='col-sm-12 col-xs-12' style='border:0.1px dotted #d6d6d6;'>	
										<div class='col-sm-6 col-xs-6'>
											<div>
												<input type= 'radio' id='ver' name='layout' style='margin:10px;' checked> แนวตั้ง
											</div>
										</div>
										<div class='col-sm-6 col-xs-6'>
											<div >
												<input type= 'radio' id='hor' name='layout' style='margin:10px;'> แนวนอน
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class='row'>
						<div class='col-sm-6 col-xs-6 col-sm-offset-3'><br>	
							<div class='col-sm-12 col-xs-12'>	
								<div class='form-group'>
									เรียงลำดับข้อมูล
									<div class='col-sm-12 col-xs-12' style='border:0.1px dotted #d6d6d6;'>	
										<div class='col-sm-6 col-xs-6'>
											<div>
												<input type= 'radio' id='contno' name='orderby' style='margin:10px;' checked> เลขที่สัญญา
											</div>
										</div>
										<div class='col-sm-6 col-xs-6'>
											<div >
												<input type= 'radio' id='redate' name='orderby' style='margin:10px;'> วันที่ไถ่ถอน
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class='row'>
						<div class='col-sm-12 col-xs-12'><br>		
							<div class='form-group'>
								<br><br>
								<button id='btnt1search' class='btn btn-primary btn-sm' style='width:100%'><span class='glyphicon glyphicon-search'> แสดง</span></button>
							</div>
						</div>
					</div>
				</div>
			</div>
		";
		
		$html.= "<script src='".base_url('public/js/SYS05/ReportRedemption.js')."'></script>";
		echo $html;
	}
	
	function search(){
		$LOCAT1			= $_REQUEST["LOCAT1"];
		$FROMDATE 		= $_REQUEST["FROMDATE"];
		$TODATE 		= $_REQUEST["TODATE"];
		$orderby 		= $_REQUEST["orderby"];
		
		$cond = "";
		$rpcond = "";
		if($LOCAT1 != ""){
			$cond .= " and b.LOCATRECV = '".$LOCAT1."'";
			$rpcond .= "  สาขา ".$LOCAT1;
		}
		
		if($FROMDATE != ""){
			$cond .= " and b.TMBILDT >= '".$this->Convertdate(1,$FROMDATE)."'";
			$rpcond .= "  จากวันที่ ".$FROMDATE;
		}
		
		if($TODATE != ""){
			$cond .= " and b.TMBILDT <= '".$this->Convertdate(1,$TODATE)."'";
			$rpcond .= "  ถึงวันที่ ".$TODATE;
		}
		
		$sql = "
				IF OBJECT_ID('tempdb..#REDEEM') IS NOT NULL DROP TABLE #REDEEM
				select *
				into #REDEEM
				from(
					select b.LOCATPAY, a.CUSCOD, a.CONTNO, c.SNAM+c.NAME1+' '+c.NAME2 as CUSNAME, convert(nvarchar,DATEADD(year,543,b.TMBILDT),103) as TMBILDTS, 
					b.PAYAMT, b.USERID, a.STRNO,convert(nvarchar,DATEADD(year,543,a.SDATE),103) as SDATE, a.TOTPRC, a.SMPAY, a.EXP_AMT, a.EXP_PRD, a.BILLCOLL, b.TMBILL, 
					convert(nvarchar,DATEADD(year,543,b.INPTIME),103)+' '+convert(nvarchar,b.INPTIME,108) as INPTIME, b.LOCATRECV, b.TMBILDT
					from {$this->MAuth->getdb('ARMAST')} a
					left join {$this->MAuth->getdb('CHQTRAN')} b on a.CONTNO = b.CONTNO and a.LOCAT = b.LOCATPAY and a.TSALE = b.TSALE
					left join {$this->MAuth->getdb('CUSTMAST')} c on a.CUSCOD = c.CUSCOD
					where b.FLAG != 'C' and b.YFLAG = 'Y' ".$cond."
					union
					select b.LOCATPAY, a.CUSCOD, a.CONTNO, c.SNAM+c.NAME1+' '+c.NAME2 as CUSNAME, convert(nvarchar,DATEADD(year,543,b.TMBILDT),103) as TMBILDTS, 
					b.PAYAMT, b.USERID, a.STRNO,convert(nvarchar,DATEADD(year,543,a.SDATE),103) as SDATE, a.TOTPRC, a.SMPAY, a.EXP_AMT, a.EXP_PRD, a.BILLCOLL, b.TMBILL, 
					convert(nvarchar,DATEADD(year,543,b.INPTIME),103)+' '+convert(nvarchar,b.INPTIME,108) as INPTIME, b.LOCATRECV, b.TMBILDT
					from {$this->MAuth->getdb('HARMAST')} a
					left join {$this->MAuth->getdb('CHQTRAN')} b on a.CONTNO = b.CONTNO and a.LOCAT = b.LOCATPAY and a.TSALE = b.TSALE
					left join {$this->MAuth->getdb('CUSTMAST')} c on a.CUSCOD = c.CUSCOD
					where b.FLAG != 'C' and b.YFLAG = 'Y' ".$cond."
				)REDEEM	
		";
		//echo $sql; 
		$query = $this->db->query($sql);
		
		$sql = "
				select * from #REDEEM order by ".$orderby."
		";
		//echo $sql; 
		$query = $this->db->query($sql);
		
		$sql2 = "
				select 'รวมทั้งสิน' as Total, sum(TOTPRC) as sumTOTPRC, sum(SMPAY) as sumSMPAY, sum(EXP_AMT) as sumEXP_AMT
				from #REDEEM	
		";
		//echo $sql2; exit;
		$query2 = $this->db->query($sql2);
		
		$head = ""; $html = ""; $head2 = "";  $report = ""; $sumreport = ""; $i=0; 
	
		$head = "<tr>
				<th style='display:none;'>#</th>
				<th style='vertical-align:top;'>สาขา</th>
				<th style='vertical-align:top;'>เลขที่สัญญา</th>
				<th style='vertical-align:top;'>รหัสลูกค้า</th>
				<th style='vertical-align:top;'>ชื่อ - นามสกุล</th>
				<th style='vertical-align:top;'>เลขตัวถัง</th>
				<th style='vertical-align:top;'>วันที่ทำสัญญา</th>
				<th style='vertical-align:top;'>ราคาขาย</th>
				<th style='vertical-align:top;'>ชำระแล้ว</th>
				<th style='vertical-align:top;'>ค้างชำระ<br>(บาท)</th>
				<th style='vertical-align:top;'>ค้างชำระ<br>(งวด)</th>
				<th style='vertical-align:top;'>เลขที่ใบรับ</th>
				<th style='vertical-align:top;'>วันที่ชำระ</th>
				<th style='vertical-align:top;'>วันเวลาที่บันทึก</th>
				<th style='vertical-align:top;'>Billcoll</th>
				</tr>
		";
		
		$head2 = "<tr>
				<th style='vertical-align:middle;'>#</th>
				<th style='vertical-align:middle;'>สาขา</th>
				<th style='vertical-align:middle;'>เลขที่สัญญา</th>
				<th style='vertical-align:middle;'>รหัสลูกค้า</th>
				<th style='vertical-align:middle;'>ชื่อ - นามสกุล</th>
				<th style='vertical-align:middle;'>เลขตัวถัง</th>
				<th style='vertical-align:middle;'>วันที่ทำสัญญา</th>
				<th style='vertical-align:middle;'>ราคาขาย</th>
				<th style='vertical-align:middle;'>ชำระแล้ว</th>
				<th style='vertical-align:middle;'>ค้างชำระ<br>(บาท)</th>
				<th style='vertical-align:middle;'>ค้างชำระ<br>(งวด)</th>
				<th style='vertical-align:middle;'>เลขที่ใบรับ</th>
				<th style='vertical-align:middle;'>วันที่ชำระ</th>
				<th style='vertical-align:middle;'>วันเวลาที่บันทึก</th>
				<th style='vertical-align:middle;'>Billcoll</th>
				</tr>
		";
		
		$NRow = 1;
		if($query->row()){
			foreach($query->result() as $row){$i++;
				$html .= "
					<tr class='trow' seq=".$NRow.">
						<td seq=".$NRow++." style='display:none;'></td>
						<td>".$row->LOCATPAY."</td>
						<td>".$row->CONTNO."</td>
						<td>".$row->CUSCOD."</td>
						<td>".$row->CUSNAME."</td>
						<td>".$row->STRNO."</td>
						<td>".$row->SDATE."</td>
						<td align='right'>".number_format($row->TOTPRC,2)."</td>
						<td align='right'>".number_format($row->SMPAY,2)."</td>
						<td align='right'>".number_format($row->EXP_AMT,2)."</td>
						<td align='right'>".number_format($row->EXP_PRD)."</td>
						<td>".$row->TMBILL."</td>
						<td>".$row->TMBILDTS."</td>
						<td>".$row->INPTIME."</td>
						<td>".$row->BILLCOLL."</td>
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
						<td style='mso-number-format:\"\@\";'>".$row->LOCATPAY."</td>
						<td style='mso-number-format:\"\@\";'>".$row->CONTNO."</td>
						<td style='mso-number-format:\"\@\";'>".$row->CUSCOD."</td>
						<td style='mso-number-format:\"\@\";'>".$row->CUSNAME."</td>
						<td style='mso-number-format:\"\@\";'>".$row->STRNO."</td>
						<td style='mso-number-format:\"\@\";'>".$row->SDATE."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->TOTPRC,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->SMPAY,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->EXP_AMT,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0\";text-align:right;'>".number_format($row->EXP_PRD)."</td>
						<td style='mso-number-format:\"\@\";'>".$row->TMBILL."</td>
						<td style='mso-number-format:\"\@\";'>".$row->TMBILDTS."</td>
						<td style='mso-number-format:\"\@\";'>".$row->INPTIME."</td>
						<td style='mso-number-format:\"\@\";'>".$row->BILLCOLL."</td>
					</tr>
				";	
			}
		}
		
		if($query2->row()){
			foreach($query2->result() as $row){
				$sumreport .= "
					<tr class='trow'>
						<th colspan='7' align='center'>".$row->Total."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumTOTPRC,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumSMPAY,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumEXP_AMT,2)."</th>
						<th colspan='5'></th>
				";	
			}
		}
		
		if($i>0){
			$html = "
				<div id='table-fixed-ReportRedemption' class='col-sm-12' style='height:100%;width:100%;overflow:auto;font-size:8pt;'>
					<table id='table-ReportRedemption' style='background-color:white;' class='col-sm-12 display table table-bordered' cellspacing='0' width='calc(100% - 1px)'>
						<thead>
						<tr>
							<th colspan='15' style='font-size:12pt;border:0px;text-align:center;'>รายงานการไถ่ถอนรถยึด</th>
						</tr>
						<tr>
							<td colspan='15' style='border-bottom:1px solid #ddd;text-align:center;'>".$rpcond."  ออกรายงาน ณ วันที่ ".$this->today('today')."</td>
						</tr>
						".$head."
						</thead>	
						<tbody style='height: 10px !important; overflow: scroll;'>
						".$html."
						</tbody>	
					</table>
				</div>
			";
		}else{
			$html="<font style='color:red;'>ไม่มีข้อมูล</font>";
		}
		
		$report = "
			<div id='table-fixed-ReportRedemption2' class='col-sm-12' style='height:100%;width:100%;overflow:auto;'>
				<table id='table-ReportRedemption2' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='100%'>
					<thead>
						<tr>
							<th colspan='15' style='font-size:12pt;border:0px;text-align:center;'>รายงานการไถ่ถอนรถยึด</th>
						</tr>
						<tr>
							<td colspan='15' style='border:0px;text-align:center;'>".$rpcond." ออกรายงาน ณ วันที่ ".$this->today('today')."</td>
						</tr>
						".$head2."
					</thead>	
					<tbody>
						".$report."
						".$sumreport."
					</tbody>
				</table>
			</div>
		";
		
		$response = array("html"=>$html, "report"=>$report, "reporttoday"=>str_replace('/','-',$this->today('today')));
		echo json_encode($response);
	}
	
	function conditiontopdf(){
		$data = array();
		$data[] = urlencode($_REQUEST["LOCAT1"].'||'.$_REQUEST["FROMDATE"].'||'.$_REQUEST["TODATE"].'||'.$_REQUEST["orderby"].'||'.$_REQUEST["layout"]);
		echo json_encode($this->generateData($data,"encode"));
	}
	
	function pdf(){
		$data = array();
		$data[] = $_GET["condpdf"];
		$arrs = $this->generateData($data,"decode");
		$arrs[0] = urldecode($arrs[0]);

		$tx = explode("||",$arrs[0]);
		$locat 		= $tx[0];
		$fromdate 	= $tx[1];
		$todate 	= $tx[2];
		$orderby 	= $tx[3];
		$layout 	= $tx[4];

		$cond = "";
		$rpcond = "";
		if($locat != ""){
			$cond .= " and b.LOCATRECV = '".$locat."'";
			$rpcond .= "  สาขา ".$locat;
		}
		
		if($fromdate != ""){
			$cond .= " and b.TMBILDT >= '".$this->Convertdate(1,$fromdate)."'";
			$rpcond .= "  จากวันที่ ".$fromdate;
		}
		
		if($todate != ""){
			$cond .= " and b.TMBILDT <= '".$this->Convertdate(1,$todate)."'";
			$rpcond .= "  ถึงวันที่ ".$todate;
		}
		
		$sql = "
				IF OBJECT_ID('tempdb..#REDEEM') IS NOT NULL DROP TABLE #REDEEM
				select *
				into #REDEEM
				from(
					select b.LOCATPAY, a.CUSCOD, a.CONTNO, c.SNAM+c.NAME1+' '+c.NAME2 as CUSNAME, convert(nvarchar,DATEADD(year,543,b.TMBILDT),103) as TMBILDTS, 
					b.PAYAMT, b.USERID, a.STRNO,convert(nvarchar,DATEADD(year,543,a.SDATE),103) as SDATE, a.TOTPRC, a.SMPAY, a.EXP_AMT, a.EXP_PRD, a.BILLCOLL, b.TMBILL, 
					convert(nvarchar,DATEADD(year,543,b.INPTIME),103)+' '+convert(nvarchar,b.INPTIME,108) as INPTIME, b.LOCATRECV, b.TMBILDT
					from {$this->MAuth->getdb('ARMAST')} a
					left join {$this->MAuth->getdb('CHQTRAN')} b on a.CONTNO = b.CONTNO and a.LOCAT = b.LOCATPAY and a.TSALE = b.TSALE
					left join {$this->MAuth->getdb('CUSTMAST')} c on a.CUSCOD = c.CUSCOD
					where b.FLAG != 'C' and b.YFLAG = 'Y' ".$cond."
					union
					select b.LOCATPAY, a.CUSCOD, a.CONTNO, c.SNAM+c.NAME1+' '+c.NAME2 as CUSNAME, convert(nvarchar,DATEADD(year,543,b.TMBILDT),103) as TMBILDTS, 
					b.PAYAMT, b.USERID, a.STRNO,convert(nvarchar,DATEADD(year,543,a.SDATE),103) as SDATE, a.TOTPRC, a.SMPAY, a.EXP_AMT, a.EXP_PRD, a.BILLCOLL, b.TMBILL, 
					convert(nvarchar,DATEADD(year,543,b.INPTIME),103)+' '+convert(nvarchar,b.INPTIME,108) as INPTIME, b.LOCATRECV, b.TMBILDT
					from {$this->MAuth->getdb('HARMAST')} a
					left join {$this->MAuth->getdb('CHQTRAN')} b on a.CONTNO = b.CONTNO and a.LOCAT = b.LOCATPAY and a.TSALE = b.TSALE
					left join {$this->MAuth->getdb('CUSTMAST')} c on a.CUSCOD = c.CUSCOD
					where b.FLAG != 'C' and b.YFLAG = 'Y' ".$cond."
				)REDEEM	
		";
		//echo $sql; 
		$query = $this->db->query($sql);
		
		$sql = "
				select * from #REDEEM order by ".$orderby."
		";
		//echo $sql; 
		$query = $this->db->query($sql);
		
		$sql2 = "
				select 'รวมทั้งสิน' as Total, sum(TOTPRC) as sumTOTPRC, sum(SMPAY) as sumSMPAY, sum(EXP_AMT) as sumEXP_AMT
				from #REDEEM	
		";
		//echo $sql2; exit;
		$query2 = $this->db->query($sql2);
		
		$head = ""; $html = ""; $sumreport = ""; $i=0; 
	
		$head = "<tr>
				<th style='border-bottom:0.1px solid black;'>#</th>
				<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>สาขา</th>
				<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>เลขที่สัญญา</th>
				<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>รหัสลูกค้า<br>ชื่อ - นามสกุล</th>
				<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>เลขตัวถัง</th>
				<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>วันที่ทำสัญญา</th>
				<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>ราคาขาย</th>
				<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>ชำระแล้ว</th>
				<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>ค้างชำระ<br>(บาท)</th>
				<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:center;'>ค้างชำระ<br>(งวด)</th>
				<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>เลขที่ใบรับ</th>
				<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>วันที่ชำระ</th>
				<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>วันเวลาที่บันทึก</th>
				<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>Billcoll</th>
				</tr>
		";
		
		$No = 1;
		if($query->row()){
			foreach($query->result() as $row){	
				$html .= "
					<tr class='trow' seq=".$No.">
						<td style='width:25px;'>".$No++."</td>
						<td style='width:40px;'>".$row->LOCATPAY."</td>
						<td style='width:75px;'>".$row->CONTNO."</td>
						<td style='width:125px;'>".$row->CUSCOD."<br>".$row->CUSNAME."</td>
						<td style='width:125px;'>".$row->STRNO."</td>
						<td style='width:70px;'>".$row->SDATE."</td>
						<td style='width:70px;' align='right'>".number_format($row->TOTPRC,2)."</td>
						<td style='width:70px;' align='right'>".number_format($row->SMPAY,2)."</td>
						<td style='width:70px;' align='right'>".number_format($row->EXP_AMT,2)."</td>
						<td style='width:70px;' align='center'>".number_format($row->EXP_PRD)."</td>
						<td style='width:75px;'>".$row->TMBILL."</td>
						<td style='width:70px;'>".$row->TMBILDTS."</td>
						<td style='width:80px;'>".$row->INPTIME."</td>
						<td style='width:50px;'>".$row->BILLCOLL."</td>
					</tr>
				";	
			}
		}
		
		if($query2->row()){
			foreach($query2->result() as $row){
				$sumreport .= "
					<tr class='trow bor' style='background-color:#ebebeb;'>
						<td colspan='6' align='center'>".$row->Total."</td>
						<td style='width:70px;text-align:right;'>".number_format($row->sumTOTPRC,2)."</td>
						<td style='width:70px;text-align:right;'>".number_format($row->sumSMPAY,2)."</td>
						<td style='width:70px;text-align:right;'>".number_format($row->sumEXP_AMT,2)."</td>
						<td colspan='5'></td>
					</tr>
				";	
			}
		}
		
		$mpdf = new \Mpdf\Mpdf([
			'mode' => 'utf-8', 
			'format' => $layout,
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
						<th colspan='14' style='font-size:10pt;'>รายงานการไถ่ถอนรถยึด </th>
					</tr>
					<tr>
						<td colspan='14' style='font-size:8pt;height:35px;border-bottom:0.1px solid black;text-align:center;'>".$rpcond."</td>
					</tr>
					".$head."
					".$html."
					".$sumreport."
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