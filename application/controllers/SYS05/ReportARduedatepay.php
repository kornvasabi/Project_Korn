<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//BEE+
class ReportARduedatepay extends MY_Controller {
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
							<br>รายงานลูกหนี้ครบกำหนดชำระค่างวด<br>
						</div>
						<div class='col-sm-8 col-xs-8 col-sm-offset-2'>
							<br>
							<div class='col-sm-4 col-xs-4'>	
								<div class='form-group'>
									สาขา
									<select id='LOCAT1' class='form-control input-sm' data-placeholder='สาขา'></select>
								</div>
							</div>
							<div class='col-sm-4 col-xs-4'>	
								<div class='form-group' >
									เลขที่สัญญา
									<select id='CONTNO1' class='form-control input-sm' data-placeholder='เลขที่สัญญา'></select>
								</div>
							</div>
							<div class='col-sm-4 col-xs-4'>	
								<div class='form-group' >
									BillColl
									<select id='BILLCOL1' class='form-control input-sm' data-placeholder='BillColl'></select>
								</div>
							</div>
							<div class='col-sm-4 col-xs-4'>	
								<div class='form-group'>
									จากวันที่ดิว
									<input type='text' id='FRMDATE' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' placeholder='จากวันที่ดิว' value='".$this->today('today')."' style='font-size:10.5pt'>
								</div>
							</div>
							<div class='col-sm-4 col-xs-4'>	
								<div class='form-group'>
									ถึงวันที่ดิว
									<input type='text' id='TODATE' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' placeholder='ถึงวันที่' value='".$this->today('today')."' style='font-size:10.5pt'>
								</div>
							</div>
							<div class='col-sm-4 col-xs-4'>	
								<div class='form-group'>
									ตำบล
									<input type='text' id='TUMBON1' class='form-control input-sm' style='font-size:10.5pt' value=''>
								</div>
							</div>
							<div class='col-sm-4 col-xs-4'>	
								<div class='form-group' >
									อำเภอ
									<select id='AMPHUR1' class='form-control input-sm AUMP' data-placeholder='อำเภอ'></select>
								</div>
							</div>
							<div class='col-sm-8 col-xs-8'>	
								<div class='form-group' >
									จังหวัด
									<select id='PROVINCE1' class='form-control input-sm AUMP' data-placeholder='จังหวัด'></select>
								</div>
							</div>
						</div>
						<div class='col-sm-8 col-xs-8 col-sm-offset-2'>
							<br>
							<div class='col-sm-4 col-xs-4'>
								<div class='form-group'>
									รูปแบบการพิมพ์
									<div class='col-sm-12 col-xs-12' style='border:0.1px dotted #d6d6d6;'>	
										<div class='col-sm-12 col-xs-12'>
											<div class='form-group'>
												<br>
												<input type= 'radio' id='hor' name='layout' checked> แนวนอน
												<br><br><br><br>
												<input type= 'radio' id='ver' name='layout'> แนวตั้ง
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class='col-sm-4 col-xs-4'>	
								<div class='form-group'>
									เรียงลำดับข้อมูล
									<div class='col-sm-12 col-xs-12' style='border:0.1px dotted #d6d6d6;'>	
										<div class='col-sm-12 col-xs-12'>
											<div class='form-group'>
												<br>
												<input type= 'radio' id='contno' name='orderby'> เลขที่สัญญา
												<br><br>
												<input type= 'radio' id='billcoll' name='orderby' checked> พนักงานเก็บเงิน
												<br><br>
												<input type= 'radio' id='duedate' name='orderby'> วันดิว
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class='col-sm-4 col-xs-4'>	
								<div class='form-group'>
									แสดงผล
									<div class='col-sm-12 col-xs-12' style='border:0.1px dotted #d6d6d6;'>	
										<div class='col-sm-12 col-xs-12'>
											<div class='form-group'>
												<br>
												<input type= 'radio' id='detail' name='report' checked> แสดงรายการ
												<br><br><br><br>
												<input type= 'radio' id='summary' name='report'> สรุป
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
		
		$html.= "<script src='".base_url('public/js/SYS05/ReportARduedatepay.js')."'></script>";
		echo $html;
	}
	
	function search(){
		$LOCAT1		= $_REQUEST["LOCAT1"];
		$CONTNO1 	= str_replace(chr(0),'',$_REQUEST["CONTNO1"]);
		$BILLCOL1 	= str_replace(chr(0),'',$_REQUEST["BILLCOL1"]);
		$AMPHUR1 	= str_replace(chr(0),'',$_REQUEST["AMPHUR1"]);
		$PROVINCE1 	= str_replace(chr(0),'',$_REQUEST["PROVINCE1"]);
		$FRMDATE 	= $this->Convertdate(1,$_REQUEST["FRMDATE"]);
		$TODATE 	= $this->Convertdate(1,$_REQUEST["TODATE"]);
		$TUMBON1 	= $_REQUEST["TUMBON1"];
		$report 	= $_REQUEST["report"];
		$orderby 	= $_REQUEST["orderby"];
		
		$cond = ""; $rpcond = "";
		
		if($LOCAT1 != ""){
			$cond .= " AND (A.LOCAT LIKE '%".$LOCAT1."%')";
			$rpcond .= "  สาขา ".$LOCAT1;
		}
		
		if($CONTNO1 != ""){
			$cond .= " AND (A.CONTNO LIKE '%".$CONTNO1."%')";
			$rpcond .= "  เลขที่สัญญา ".$CONTNO1;
		}
		
		if($BILLCOL1 != ""){
			$cond .= " AND (A.BILLCOLL LIKE '%".$BILLCOL1."%' )";
			$rpcond .= "  พนักงานเก็บเงิน ".$BILLCOL1;
		}else{
			$cond .= " AND (A.BILLCOLL LIKE '%%' OR A.BILLCOLL IS NULL)";
		}
		
		if($AMPHUR1 != ""){
			$cond .= " AND (D.AUMPCOD LIKE '%".$AMPHUR1."%' )";
		}else{
			$cond .= " AND (D.AUMPCOD LIKE '%%' OR D.AUMPCOD IS NULL)";
		}
		
		if($PROVINCE1 != ""){
			$cond .= " AND (D.PROVCOD LIKE '%".$PROVINCE1."%' )";
		}else{
			$cond .= " AND (D.PROVCOD LIKE '%%' OR D.PROVCOD IS NULL)";
		}
		
		if($TUMBON1 != ""){
			$cond .= " AND D.TUMB LIKE '%".$TUMBON1."%'";
		}
		
		$sql = "

		";//echo $sql; 
		$query = $this->db->query($sql);
		
		$sql = "

		";//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$sql = "

		";//echo $sql; exit;
		$query2 = $this->db->query($sql);
		
		$head = ""; $html = ""; $head2 = "";  $report = ""; $sumreport = ""; $sumreport2 = ""; $i = 0; 
		
		$head = "<tr>
				<th style='display:none;'>#</th>
				<th style='vertical-align:top;'>สาขา</th>
				<th style='vertical-align:top;'>เลขที่สัญญา<br>เลขทะเบียน</th>
				<th style='vertical-align:top;'>รหัสลูกค้า<br>เลขตัวถัง</th>
				<th style='vertical-align:top;'>ชื่อ - นามสกุล<br>ที่อยู่</th>
				<th style='vertical-align:top;'>วันที่ขาย<br>กิจกรรมการขาย</th>
				<th style='vertical-align:top;text-align:right;'>ราคาขาย</th> 
				<th style='vertical-align:top;text-align:right;'>เงินดาวน์ </th>
				<th style='vertical-align:top;text-align:right;'>ค้างดาวน์</th>
				<th style='vertical-align:top;text-align:right;'>ลูกหนี้คงเหลือจริง<br>งวดทั้งหมด</th>
				<th style='vertical-align:top;text-align:right;'>ค้างงวด<br>งวดที่ค้าง</th>
				<th style='vertical-align:top;text-align:right;'>ค่างวดๆละ<br>จากงวดที่</th>
				<th style='vertical-align:top;text-align:right;'>ชำระล่าสุด<br>ค้างเบี้ยปรับ</th>
				<th style='vertical-align:top;text-align:right;'>Billcoll<br>ขาดการติดต่อ</th>
				</tr>
		";
		
		$head2 = "<tr>
					<th style='vertical-align:middle;'>#</th>
					<th style='vertical-align:top;'>สาขา</th>
					<th style='vertical-align:top;'>เลขที่สัญญา</th>
					<th style='vertical-align:top;'>รหัสลูกค้า</th>
					<th style='vertical-align:top;'>ชื่อ - นามสกุล</th>
					<th style='vertical-align:top;'>ที่อยู่</th>
					<th style='vertical-align:top;'>เลขทะเบียน</th>
					<th style='vertical-align:top;'>เลขตัวถัง</th>
					<th style='vertical-align:top;'>วันที่ขาย</th>
					<th style='vertical-align:top;text-align:right;'>ราคาขาย</th> 
					<th style='vertical-align:top;text-align:right;'>เงินดาวน์ </th>
					<th style='vertical-align:top;text-align:right;'>ค้างดาวน์</th>
					<th style='vertical-align:top;text-align:right;'>ลูกหนี้คงเหลือจริง</th>
					<th style='vertical-align:top;text-align:right;'>ค้างงวด</th>
					<th style='vertical-align:top;text-align:right;'>ค่างวดๆละ</th>
					<th style='vertical-align:top;text-align:center;'>ชำระล่าสุด</th>
					<th style='vertical-align:top;text-align:center;'>ค้างเบี้ยปรับ</th>
					<th style='vertical-align:top;text-align:center;'>งวดทั้งหมด</th>
					<th style='vertical-align:top;text-align:center;'>งวดที่ค้าง</th>
					<th style='vertical-align:top;text-align:center;'>จากงวดที่</th>
					<th style='vertical-align:top;text-align:center;'>ขาดการติดต่อ</th>
					<th style='vertical-align:top;text-align:center;'>กิจกรรมการขาย</th>
					<th style='vertical-align:top;text-align:center;'>Billcoll</th>
				</tr>
		";
		
		$NRow = 1;
		if($query->row()){
			foreach($query->result() as $row){$i++;
				$html .= "
					<tr class='trow' seq=".$NRow.">
						<td seq=".$NRow++." style='display:none;'></td>
						<td>".$row->LOCAT."</td>
						<td>".$row->CONTNO."<br>".$row->REGNO."</td>
						<td>".$row->CUSCOD."<br>".$row->STRNO."</td>
						<td>".$row->CUSNAME."<br>".$row->CUSADD."</td>
						<td>".$this->Convertdate(2,$row->SDATE)."<br>".$row->ACTICOD."</td>
						<td align='right'>".number_format($row->TOTPRC,2)."</td>
						<td align='right'>".number_format($row->TOTDWN,2)."</td>
						<td align='right'>".number_format($row->KANGDWN,2)."</td>
						<td align='right'>".number_format($row->TOTAR,2)."<br>".number_format($row->T_NOPAY)."</td>
						<td align='right'>".number_format($row->EXP_AMT,2)."<br>".number_format($row->EXP_PRD)."</td>
						<td align='right'>".number_format($row->TOT_UPAY)."<br>".number_format($row->EXP_FRM).'-'.number_format($row->EXP_TO)."</td>
						<td align='right'>".$this->Convertdate(2,$row->LPAYDS)."<br>".number_format($row->KANGINT,2)."</td>
						<td align='right'>".$row->BILLCOLL."<br>".number_format($row->LATED).' วัน'."</td>
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
						<td style='mso-number-format:\"\@\";'>".$row->CUSADD."</td>
						<td style='mso-number-format:\"\@\";'>".$row->REGNO."</td>
						<td style='mso-number-format:\"\@\";'>".$row->STRNO."</td>
						<td style='mso-number-format:\"\@\";text-align:center;'>".$this->Convertdate(2,$row->SDATE)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->TOTPRC,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->TOTDWN,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->KANGDWN,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->TOTAR,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->EXP_AMT,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->TOT_UPAY,2)."</td>
						<td style='mso-number-format:\"\@\";text-align:center;'>".$this->Convertdate(2,$row->LPAYDS)."</td>
						<td style='mso-number-format:\"\@\";text-align:center;'>".number_format($row->KANGINT,2)."</td>
						<td style='mso-number-format:\"\@\";text-align:center;'>".number_format($row->T_NOPAY)."</td>
						<td style='mso-number-format:\"\@\";text-align:center;'>".number_format($row->EXP_PRD)."</td>
						<td style='mso-number-format:\"\@\";text-align:center;'>".number_format($row->EXP_FRM).'-'.number_format($row->EXP_TO)."</td>
						<td style='mso-number-format:\"\@\";text-align:center;'>".$row->LATED."</td>
						<td style='mso-number-format:\"\@\";text-align:center;'>".$row->ACTICOD."</td>
						<td style='mso-number-format:\"\@\";text-align:center;'>".$row->BILLCOLL."</td>
					</tr>
				";	
			}
		}
		
		if($query2->row()){
			foreach($query2->result() as $row){
				$sumreport = "
					<tr>
						<th colspan='5' style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;text-align:center;'>".$row->Total."</th>
						<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;text-align:right;'>".number_format($row->sumTOTPRC)."</th>
						<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;text-align:right;'>".number_format($row->sumTOTDWN,2)."</th>
						<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;text-align:right;'>".number_format($row->sumKANGDWN,2)."</th>
						<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;text-align:right;'>".number_format($row->sumTOTAR,2)."</th>
						<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;text-align:right;'>".number_format($row->sumEXP_AMT,2)."</th>
						<th colspan='3' style='border:0px;text-align:right;'></th>
					</tr>
				";	
			}
		}
		
		if($query2->row()){
			foreach($query2->result() as $row){
				$sumreport2 = "
					<tr class='trow'>
						<th style='mso-number-format:\"\@\";text-align:center;' colspan='9'>".$row->Total."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumTOTPRC,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumTOTDWN,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumKANGDWN,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumTOTAR,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumEXP_AMT,2)."</th>
						<th style='mso-number-format:\"\@\";text-align:center;' colspan='9'></th>
					</tr>
				";	
			}
		}
		
		if($i>0){
			$html = "
				<div id='table-fixed-ReportARduedatepay' class='col-sm-12' style='height:100%;width:100%;overflow:auto;font-size:8pt;'>
					<table id='table-ReportARduedatepay' style='background-color:white;' class='col-sm-12 display table table-bordered' cellspacing='0' width='calc(100% - 1px)'>
						<thead>
						<tr>
							<th colspan='13' style='font-size:12pt;border:0px;text-align:center;'>รายงานลูกหนี้ครบกำหนดชำระค่างวด</th>
						</tr>
						<tr>
							<td colspan='13' style='border-bottom:1px solid #ddd;text-align:center;'>วันที่ขาย ".$_REQUEST["FRMDATE"]." - ".$_REQUEST["TODATE"]." ".$rpcond."  ออกรายงาน ณ วันที่ ".$this->today('today')."</td>
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
			<div id='table-fixed-ReportARduedatepay2' class='col-sm-12' style='height:100%;width:100%;overflow:auto;'>
				<table id='table-ReportARduedatepay2' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='100%'>
					<thead>
						<tr>
							<th colspan='23' style='font-size:12pt;border:0px;text-align:center;'>รายงานลูกหนี้ครบกำหนดชำระค่างวด</th>
						</tr>
						<tr>
							<td colspan='23' style='border:0px;text-align:center;'>วันที่ขาย ".$_REQUEST["FRMDATE"]." - ".$_REQUEST["TODATE"]." ".$rpcond." ออกรายงาน ณ วันที่ ".$this->today('today')."</td>
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
		$data[] = 	urlencode($_REQUEST["LOCAT1"].'||'.$_REQUEST["CONTNO1"].'||'.$_REQUEST["BILLCOL1"].'||'.$_REQUEST["AMPHUR1"]
					.'||'.$_REQUEST["PROVINCE1"].'||'.$_REQUEST["FRMDATE"].'||'.$_REQUEST["TODATE"].'||'.$_REQUEST["TUMBON1"]
					.'||'.$_REQUEST["report"].'||'.$_REQUEST["orderby"].'||'.$_REQUEST["layout"]);
		echo json_encode($this->generateData($data,"encode"));
	}
	
	function pdf(){
		$data 	= array();
		$data[] = $_GET["condpdf"];
		$arrs 	= $this->generateData($data,"decode");
		$arrs[0]= urldecode($arrs[0]);
		$tx 	= explode("||",$arrs[0]);
		$LOCAT1		= $tx[0];
		$CONTNO1 	= str_replace(chr(0),'',$tx[1]);
		$BILLCOL1 	= str_replace(chr(0),'',$tx[2]);
		$AMPHUR1 	= str_replace(chr(0),'',$tx[3]);
		$PROVINCE1 	= str_replace(chr(0),'',$tx[4]);
		$FRMDATE 	= $this->Convertdate(1,$tx[5]);
		$TODATE 	= $this->Convertdate(1,$tx[6]);
		$TUMBON1 	= $tx[7];
		$report 	= $tx[8];
		$orderby 	= $tx[9];
		$layout 	= $tx[10];
		
		$cond = ""; $rpcond = "";
		
		if($LOCAT1 != ""){
			$cond .= " AND (A.LOCAT LIKE '%".$LOCAT1."%')";
			$rpcond .= "  สาขา ".$LOCAT1;
		}
		
		if($CONTNO1 != ""){
			$cond .= " AND (A.CONTNO LIKE '%".$CONTNO1."%')";
			$rpcond .= "  เลขที่สัญญา ".$CONTNO1;
		}
		
		if($BILLCOL1 != ""){
			$cond .= " AND (A.BILLCOLL LIKE '%".$BILLCOL1."%' )";
			$rpcond .= "  พนักงานเก็บเงิน ".$BILLCOL1;
		}else{
			$cond .= " AND (A.BILLCOLL LIKE '%%' OR A.BILLCOLL IS NULL)";
		}
		
		if($AMPHUR1 != ""){
			$cond .= " AND (D.AUMPCOD LIKE '%".$AMPHUR1."%' )";
		}else{
			$cond .= " AND (D.AUMPCOD LIKE '%%' OR D.AUMPCOD IS NULL)";
		}
		
		if($PROVINCE1 != ""){
			$cond .= " AND (D.PROVCOD LIKE '%".$PROVINCE1."%' )";
		}else{
			$cond .= " AND (D.PROVCOD LIKE '%%' OR D.PROVCOD IS NULL)";
		}
		
		if($TUMBON1 != ""){
			$cond .= " AND D.TUMB LIKE '%".$TUMBON1."%'";
		}
		
		$sql = "
				
		";//echo $sql; 
		$query = $this->db->query($sql);
		
		$sql = "
				
		";//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$sql = "

		";//echo $sql; exit;
		$query2 = $this->db->query($sql);
		
		$head = ""; $html = ""; $i=0; 

		$head = "
				<tr>
					<th style='border-bottom:0.1px solid black;text-align:left;'>#</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>สาขา</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>เลขที่สัญญา<br>เลขทะเบียน</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>รหัสลูกค้า<br>เลขตัวถัง</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>ชื่อ - นามสกุล<br>ที่อยู่</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>วันที่ขาย<br>กิจกรรมการขาย</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>ราคาขาย</th> 
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>เงินดาวน์ </th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>ค้างดาวน์</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>ลูกหนี้คงเหลือ<br>งวดทั้งหมด</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>ค้างงวด<br>งวดที่ค้าง</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>ค่างวดๆละ<br>จากงวดที่</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:center;'>ชำระล่าสุด<br>ค้างเบี้ยปรับ</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:center;'>Billcoll<br>ขาดการติดต่อ</th>
				</tr>
		";
		
		$No = 1;
		if($query->row()){
			foreach($query->result() as $row){	
				$html .= "
					<tr class='trow' seq=".$No.">
						<td style='width:25px;'>".$No++."</td>
						<td style='width:40px;'>".$row->LOCAT."</td>
						<td style='width:75px;'>".$row->CONTNO."<br>".$row->REGNO."</td>
						<td style='width:120px;'>".$row->CUSCOD."<br>".$row->STRNO."</td>
						<td style='width:200px;' >".$row->CUSNAME."<br>".$row->CUSADD."</td>
						<td style='width:80px;'>".$this->Convertdate(2,$row->SDATE)."<br>".$row->ACTICOD."</td>
						<td style='width:70px;' align='right'>".number_format($row->TOTPRC,2)."</td>
						<td style='width:70px;' align='right'>".number_format($row->TOTDWN,2)."</td>
						<td style='width:70px;' align='right'>".number_format($row->KANGDWN,2)."</td>
						<td style='width:70px;' align='right'>".number_format($row->TOTAR,2)."<br>".number_format($row->T_NOPAY)."</td>
						<td style='width:70px;' align='right'>".number_format($row->EXP_AMT,2)."<br>".number_format($row->EXP_PRD)."</td>
						<td style='width:60px;' align='right'>".number_format($row->TOT_UPAY)."<br>".number_format($row->EXP_FRM).'-'.number_format($row->EXP_TO)."</td>
						<td style='width:70px;' align='center'>".$this->Convertdate(2,$row->LPAYDS)."<br>".number_format($row->KANGINT,2)."</td>
						<td style='width:70px;' align='center'>".$row->BILLCOLL."<br>".number_format($row->LATED)."</td>
						
					</tr>
				";	
			}
		}
		
		if($query2->row()){
			foreach($query2->result() as $row){	
				$html .= "
					<tr class='trow bor' style='background-color:#ebebeb;'>
						<td colspan='6' style='text-align:center;vertical-align:middle;'>".$row->Total."</td>
						<td align='right'>".number_format($row->sumTOTPRC)."</td>
						<td align='right'>".number_format($row->sumTOTDWN,2)."</td>
						<td align='right'>".number_format($row->sumKANGDWN,2)."</td>
						<td align='right'>".number_format($row->sumTOTAR,2)."</td>
						<td align='right'>".number_format($row->sumEXP_AMT,2)."</td>
						<td colspan='3' style='text-align:center;vertical-align:middle;'></td>
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
			<table class='wf' style='font-size:7.5pt;height:700px;border-collapse:collapse;line-height:23px;overflow:wrap;vertical-align:text-top;'>
				<tbody>
					<tr>
						<th colspan='14' style='font-size:10pt;'>รายงานลูกหนี้ครบกำหนดชำระค่างวด</th>
					</tr>
					<tr>
						<td colspan='14' style='font-size:8pt;height:35px;border-bottom:0.1px solid black;text-align:center;'>".$rpcond." วันที่ขาย ".$tx[8]." - ".$tx[9]."</td>
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