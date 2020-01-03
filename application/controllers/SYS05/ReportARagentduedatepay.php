<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//BEE+
class ReportARagentduedatepay extends MY_Controller {
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
							<br>รายงานลูกหนี้ขายส่งครบกำหนดชำระ<br>
						</div>
						<div class='col-sm-8 col-xs-8 col-sm-offset-2'>
							<br>
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group'>
									สาขา
									<select id='LOCAT1' class='form-control input-sm' data-placeholder='สาขา'></select>
								</div>
							</div>
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group' >
									เลขที่สัญญา
									<select id='CONTNO1' class='form-control input-sm' data-placeholder='เลขที่สัญญา'></select>
								</div>
							</div>
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group'>
									จากวันที่ดิว
									<input type='text' id='FRMDATE' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' placeholder='จากวันที่ดิว' value='".$this->today('today')."' style='font-size:10.5pt'>
								</div>
							</div>
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group'>
									ถึงวันที่ดิว
									<input type='text' id='TODATE' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' placeholder='ถึงวันที่' value='".$this->today('today')."' style='font-size:10.5pt'>
								</div>
							</div>
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group'>
									ตำบล
									<input type='text' id='TUMBON1' class='form-control input-sm' style='font-size:10.5pt' value=''>
								</div>
							</div>
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group' >
									อำเภอ
									<select id='AMPHUR1' class='form-control input-sm AUMP' data-placeholder='อำเภอ'></select>
								</div>
							</div>
							<div class='col-sm-12 col-xs-12'>	
								<div class='form-group' >
									จังหวัด
									<select id='PROVINCE1' class='form-control input-sm AUMP' data-placeholder='จังหวัด'></select>
								</div>
							</div>
						</div>
						<div class='col-sm-8 col-xs-8 col-sm-offset-2'>
							<br>
							<div class='col-sm-6 col-xs-6'>
								<div class='form-group'>
									รูปแบบการพิมพ์
									<div class='col-sm-12 col-xs-12' style='border:0.1px dotted #d6d6d6;'>	
										<div class='col-sm-12 col-xs-12'>
											<div class='form-group'>
												<br>
												<input type= 'radio' id='hor' name='layout' checked> แนวนอน
												<br><br>
												<input type= 'radio' id='ver' name='layout'> แนวตั้ง
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group'>
									เรียงลำดับข้อมูล
									<div class='col-sm-12 col-xs-12' style='border:0.1px dotted #d6d6d6;'>	
										<div class='col-sm-12 col-xs-12'>
											<div class='form-group'>
												<br>
												<input type= 'radio' id='ldate' name='orderby'> วันที่ครบกำหนด
												<br><br>
												<input type= 'radio' id='contno' name='orderby' checked> เลขที่สัญญา
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
		
		$html.= "<script src='".base_url('public/js/SYS05/ReportARagentduedatepay.js')."'></script>";
		echo $html;
	}
	
	function search(){
		$LOCAT1		= $_REQUEST["LOCAT1"];
		$CONTNO1 	= str_replace(chr(0),'',$_REQUEST["CONTNO1"]);
		$AMPHUR1 	= str_replace(chr(0),'',$_REQUEST["AMPHUR1"]);
		$PROVINCE1 	= str_replace(chr(0),'',$_REQUEST["PROVINCE1"]);
		$FRMDATE 	= $this->Convertdate(1,$_REQUEST["FRMDATE"]);
		$TODATE 	= $this->Convertdate(1,$_REQUEST["TODATE"]);
		$TUMBON1 	= $_REQUEST["TUMBON1"];
		$orderby 	= $_REQUEST["orderby"];
		
		$cond = ""; $rpcond = "";
		
		if($LOCAT1 != ""){
			$cond .= " AND (m.LOCAT LIKE '%".$LOCAT1."%')";
			$rpcond .= "  สาขา ".$LOCAT1;
		}
		
		if($CONTNO1 != ""){
			$cond .= " AND (m.CONTNO LIKE '%".$CONTNO1."%')";
			$rpcond .= "  เลขที่สัญญา ".$CONTNO1;
		}
		
		if($AMPHUR1 != ""){
			$cond .= " AND (a.AUMPCOD LIKE '%".$AMPHUR1."%' )";
		}else{
			$cond .= " AND (a.AUMPCOD LIKE '%%' OR a.AUMPCOD IS NULL)";
		}
		
		if($PROVINCE1 != ""){
			$cond .= " AND (a.PROVCOD LIKE '%".$PROVINCE1."%' )";
		}else{
			$cond .= " AND (a.PROVCOD LIKE '%%' OR a.PROVCOD IS NULL)";
		}
		
		if($TUMBON1 != ""){
			$cond .= " AND a.TUMB LIKE '%".$TUMBON1."%'";
		}
		
		$sql = "
				IF OBJECT_ID('tempdb..#main') IS NOT NULL DROP TABLE #main
				select *
				into #main
				from(
					select m.CONTNO, m.LOCAT, m.CUSCOD, c.SNAM+c.NAME1+' '+c.NAME2 as CUSNAME, convert(nvarchar,m.SDATE,112) as SDATE, 
					m.TOTPRC, m.SMPAY, m.TOTPRC- m.SMPAY as TOTAR, m.SMCHQ, m.DUEDT, convert(nvarchar,m.DUEDT,112) as DUEDTS
					from {$this->MAuth->getdb('AR_INVOI')} m
					left join {$this->MAuth->getdb('CUSTMAST')} c on c.CUSCOD = m.CUSCOD
					left join {$this->MAuth->getdb('CUSTADDR')} a  on c.CUSCOD = a.CUSCOD and c.ADDRNO = a.ADDRNO 
					where (m.duedt between '".$FRMDATE."' and '".$TODATE."') 
					".$cond."
				)main
		";//echo $sql; 
		$query = $this->db->query($sql);
		
		$sql = "
				select  CONTNO, LOCAT, CUSCOD, CUSNAME, SDATE, TOTPRC, SMPAY, TOTAR, SMCHQ, DUEDT, DUEDTS
				from #main
				order by ".$orderby."
		";//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$sql = "
				select 'รวมทั้งหมด' as Total, sum(TOTPRC) as sumTOTPRC, sum(SMPAY) as sumSMPAY, sum(TOTAR) as sumTOTAR, sum(SMCHQ) as sumSMCHQ
				from #main
		";//echo $sql; exit;
		$query2 = $this->db->query($sql);
		
		$head = ""; $html = ""; $head2 = "";  $report = ""; $sumreport = ""; $sumreport2 = ""; $i = 0; 
		
		$head = "<tr style='height:25px;'>
				<th style='display:none;'>#</th>
				<th style='vertical-align:top;'>สาขา</th>
				<th style='vertical-align:top;'>เลขที่สัญญา</th>
				<th style='vertical-align:top;'>รหัสลูกค้า</th>
				<th style='vertical-align:top;'>ชื่อ - นามสกุล</th>
				<th style='vertical-align:top;text-align:center;'>วันที่ขาย</th>
				<th style='vertical-align:top;text-align:right;'>ราคาขาย</th> 
				<th style='vertical-align:top;text-align:right;'>ชำระเงินแล้ว</th>
				<th style='vertical-align:top;text-align:right;'>ลูกหนี้คงเหลือ</th>
				<th style='vertical-align:top;text-align:right;'>เช็ครอเรียกเก็บ</th>
				<th style='vertical-align:top;text-align:center;'>วันที่ขาย</th>
				</tr>
		";
		
		$head2 = "<tr>
					<th style='vertical-align:middle;'>#</th>
					<th style='vertical-align:top;'>สาขา</th>
					<th style='vertical-align:top;'>เลขที่สัญญา</th>
					<th style='vertical-align:top;'>รหัสลูกค้า</th>
					<th style='vertical-align:top;'>ชื่อ - นามสกุล</th>
					<th style='vertical-align:top;text-align:center;'>วันที่ขาย</th>
					<th style='vertical-align:top;text-align:right;'>ราคาขาย</th> 
					<th style='vertical-align:top;text-align:right;'>ชำระเงินแล้ว </th>
					<th style='vertical-align:top;text-align:right;'>ลูกหนี้คงเหลือ</th>
					<th style='vertical-align:top;text-align:right;'>เช็ครอเรียกเก็บ</th>
					<th style='vertical-align:top;text-align:center;'>วันที่ขาย</th>
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
						<td align='center'>".$this->Convertdate(2,$row->SDATE)."</td>
						<td align='right'>".number_format($row->TOTPRC,2)."</td>
						<td align='right'>".number_format($row->SMPAY,2)."</td>
						<td align='right'>".number_format($row->TOTAR,2)."</td>
						<td align='right'>".number_format($row->SMCHQ,2)."</td>
						<td align='center'>".$this->Convertdate(2,$row->DUEDTS)."</td>
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
						<td style='mso-number-format:\"\@\";text-align:center;'>".$this->Convertdate(2,$row->SDATE)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->TOTPRC,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->SMPAY,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->TOTAR,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->SMCHQ,2)."</td>
						<td style='mso-number-format:\"\@\";text-align:center;'>".$this->Convertdate(2,$row->DUEDTS)."</td>
					</tr>
				";	
			}
		}
		
		if($query2->row()){
			foreach($query2->result() as $row){
				$sumreport = "
					<tr style='height:25px;'>
						<th colspan='5' style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;vertical-align:middle;text-align:center;'>".$row->Total."</th>
						<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;vertical-align:middle;text-align:right;'>".number_format($row->sumTOTPRC,2)."</th>
						<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;vertical-align:middle;text-align:right;'>".number_format($row->sumSMPAY,2)."</th>
						<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;vertical-align:middle;text-align:right;'>".number_format($row->sumTOTAR,2)."</th>
						<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;vertical-align:middle;text-align:right;'>".number_format($row->sumSMCHQ,2)."</th>
						<th style='border:0px;text-align:right;'></th>
					</tr>
				";	
			}
		}
		
		if($query2->row()){
			foreach($query2->result() as $row){
				$sumreport2 = "
					<tr class='trow'>
						<th style='mso-number-format:\"\@\";text-align:center;' colspan='6'>".$row->Total."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumTOTPRC,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumSMPAY,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumTOTAR,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumSMCHQ,2)."</th>
						<th style='mso-number-format:\"\@\";text-align:center;'></th>
					</tr>
				";	
			}
		}
		
		if($i>0){
			$html = "
				<div id='table-fixed-ReportARagentduedatepay' class='col-sm-12' style='height:100%;width:100%;overflow:auto;font-size:9pt;'>
					<table id='table-ReportARagentduedatepay' style='background-color:white;' class='col-sm-12 display table table-bordered' cellspacing='0' width='calc(100% - 1px)'>
						<thead>
						<tr style='height:40px;'>
							<th colspan='10' style='font-size:12pt;border:0px;vertical-align;middle;text-align:center;'>รายงานลูกหนี้ขายส่งครบกำหนดชำระ</th>
						</tr>
						<tr style='height:25px;'>
							<td colspan='10' style='border-bottom:1px solid #ddd;vertical-align;middle;text-align:center;'>จากวันที่ดิว ".$_REQUEST["FRMDATE"]." - ".$_REQUEST["TODATE"]." ".$rpcond."  ออกรายงาน ณ วันที่ ".$this->today('today')."</td>
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
			<div id='table-fixed-ReportARagentduedatepay2' class='col-sm-12' style='height:100%;width:100%;overflow:auto;'>
				<table id='table-ReportARagentduedatepay2' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='100%'>
					<thead>
						<tr>
							<th colspan='11' style='font-size:12pt;border:0px;text-align:center;'>รายงานลูกหนี้ขายส่งครบกำหนดชำระ</th>
						</tr>
						<tr>
							<td colspan='11' style='border:0px;text-align:center;'>จากวันที่ดิว ".$_REQUEST["FRMDATE"]." - ".$_REQUEST["TODATE"]." ".$rpcond." ออกรายงาน ณ วันที่ ".$this->today('today')."</td>
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
		$data[] = 	urlencode($_REQUEST["LOCAT1"].'||'.$_REQUEST["CONTNO1"].'||'.$_REQUEST["AMPHUR1"].'||'.$_REQUEST["PROVINCE1"].'||'.$_REQUEST["FRMDATE"]
					.'||'.$_REQUEST["TODATE"].'||'.$_REQUEST["TUMBON1"].'||'.$_REQUEST["orderby"].'||'.$_REQUEST["layout"]);
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
		$AMPHUR1 	= str_replace(chr(0),'',$tx[2]);
		$PROVINCE1 	= str_replace(chr(0),'',$tx[3]);
		$FRMDATE 	= $this->Convertdate(1,$tx[4]);
		$TODATE 	= $this->Convertdate(1,$tx[5]);
		$TUMBON1 	= $tx[6];
		$orderby 	= $tx[7];
		$layout 	= $tx[8];
		
		$cond = ""; $rpcond = "";
		
		$sql = "
				IF OBJECT_ID('tempdb..#main') IS NOT NULL DROP TABLE #main
				select *
				into #main
				from(
					select m.CONTNO, m.LOCAT, m.CUSCOD, c.SNAM+c.NAME1+' '+c.NAME2 as CUSNAME, convert(nvarchar,m.SDATE,112) as SDATE, 
					m.TOTPRC, m.SMPAY, m.TOTPRC- m.SMPAY as TOTAR, m.SMCHQ, m.DUEDT, convert(nvarchar,m.DUEDT,112) as DUEDTS
					from {$this->MAuth->getdb('AR_INVOI')} m
					left join {$this->MAuth->getdb('CUSTMAST')} c on c.CUSCOD = m.CUSCOD
					left join {$this->MAuth->getdb('CUSTADDR')} a  on c.CUSCOD = a.CUSCOD and c.ADDRNO = a.ADDRNO 
					where (m.duedt between '".$FRMDATE."' and '".$TODATE."') 
					".$cond."
				)main
		";//echo $sql; 
		$query = $this->db->query($sql);
		
		$sql = "
				select  CONTNO, LOCAT, CUSCOD, CUSNAME, SDATE, TOTPRC, SMPAY, TOTAR, SMCHQ, DUEDT, DUEDTS
				from #main
				order by ".$orderby."
		";//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$sql = "
				select 'รวมทั้งหมด' as Total, sum(TOTPRC) as sumTOTPRC, sum(SMPAY) as sumSMPAY, sum(TOTAR) as sumTOTAR, sum(SMCHQ) as sumSMCHQ
				from #main
		";//echo $sql; exit;
		$query2 = $this->db->query($sql);
		
		$head = ""; $html = ""; $i=0; 

		$head = "
				<tr>
					<th style='border-bottom:0.1px solid black;text-align:left;'>#</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>สาขา</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>เลขที่สัญญา</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>รหัสลูกค้า</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>ชื่อ - นามสกุล</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:center;'>วันที่ขาย</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>ราคาขาย</th> 
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>ชำระเงินแล้ว </th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>ลูกหนี้คงเหลือ</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>เช็ครอเรียกเก็บ</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:center;'>วันที่ขาย</th>
				</tr>
		";
		
		$No = 1;
		if($query->row()){
			foreach($query->result() as $row){	
				$html .= "
					<tr class='trow' seq=".$No.">
						<td style='width:30px;'>".$No++."</td>
						<td style='width:50px;'>".$row->LOCAT."</td>
						<td style='width:100px;'>".$row->CONTNO."</td>
						<td style='width:100px;'>".$row->CUSCOD."</td>
						<td style='width:250px;'>".$row->CUSNAME."</td>
						<td style='width:150px;' align='center'>".$this->Convertdate(2,$row->SDATE)."</td>
						<td style='width:110px;' align='right'>".number_format($row->TOTPRC,2)."</td>
						<td style='width:110px;' align='right'>".number_format($row->SMPAY,2)."</td>
						<td style='width:110px;' align='right'>".number_format($row->TOTAR,2)."</td>
						<td style='width:110px;' align='right'>".number_format($row->SMCHQ,2)."</td>
						<td style='width:150px;' align='center'>".$this->Convertdate(2,$row->DUEDTS)."</td>
						
					</tr>
				";	
			}
		}
		
		if($query2->row()){
			foreach($query2->result() as $row){	
				$html .= "
					<tr class='trow bor' style='background-color:#ebebeb;'>
						<th colspan='6' style='text-align:center;vertical-align:middle;'>".$row->Total."</th>
						<th align='right'>".number_format($row->sumTOTPRC,2)."</th>
						<th align='right'>".number_format($row->sumSMPAY,2)."</th>
						<th align='right'>".number_format($row->sumTOTAR,2)."</th>
						<th align='right'>".number_format($row->sumSMCHQ,2)."</th>
						<th style='text-align:center;vertical-align:middle;'></th>
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
			<table class='wf' style='font-size:9pt;height:700px;border-collapse:collapse;line-height:23px;overflow:wrap;vertical-align:text-top;'>
				<tbody>
					<tr>
						<th colspan='11' style='font-size:10pt;'>รายงานลูกหนี้ขายส่งครบกำหนดชำระ</th>
					</tr>
					<tr>
						<td colspan='11' style='font-size:9pt;height:35px;border-bottom:0.1px solid black;text-align:center;'>".$rpcond." วันที่ขาย ".$tx[4]." - ".$tx[5]."</td>
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