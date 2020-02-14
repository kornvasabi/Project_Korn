<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//BEE+
class ReportOptionaudit extends MY_Controller {
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
						<div class='col-sm-12 col-xs-12' style='background-color:#0067a5;border:5px solid white;height:75px;text-align:center;font-size:12pt;color:white;font-weight:bold;'>	
							<br>รายงานตรวจสอบการรับอุปกรณ์<br>
						</div>
						<div class='col-sm-8 col-xs-8 col-sm-offset-2'>
							<br>
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group'>
									รหัสสาขา
									<select id='LOCAT1' class='form-control input-sm' data-placeholder='รหัสสาขา'></select>
								</div>
							</div>
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group' >
									เจ้าหนี้
									<select id='APCODE1' class='form-control input-sm' data-placeholder='เจ้าหนี้'></select>
								</div>
							</div>
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
						<div class='col-sm-8 col-xs-8 col-sm-offset-2'>
							<br>
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group'>
									เรียงลำดับข้อมูล
									<div class='col-sm-12 col-xs-12' style='border:0.1px dotted #d6d6d6;'>	
										<div class='form-group'>
											<br>
											<input type= 'radio' id='rcvdate' name='order' checked> ตามวันที่รับสินค้า
											<br><br>
											<input type= 'radio' id='invno' name='order'> ตามเลขที่ Invoi
										</div>
									</div>
								</div>
							</div>
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group'>
									รูปแบบการพิมพ์
									<div class='col-sm-12 col-xs-12' style='border:0.1px dotted #d6d6d6;'>	
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
					</div>
					<div class='row' style='height:10%;'>
						<div class='col-sm-12 col-xs-12'><br>	
							<button id='btnt1search' class='btn btn-primary btn-sm' style='width:100%;font-size:10.5pt;'><span class='glyphicon glyphicon-search'> แสดง</span></button>
						</div>
					</div>
				</div>
			</div>
		";
		
		$html.= "<script src='".base_url('public/js/SYS02/ReportOptionaudit.js')."'></script>";
		echo $html;
	}
	
	function search(){
		$LOCAT1		= $_REQUEST["LOCAT1"];
		$APCODE1 	= str_replace(chr(0),'',$_REQUEST["APCODE1"]);
		$FRMDATE 	= $this->Convertdate(1,$_REQUEST["FRMDATE"]);
		$TODATE 	= $this->Convertdate(1,$_REQUEST["TODATE"]);
		$orderby 	= $_REQUEST["orderby"];
		
		$cond = ""; $rpcond = "";
		
		if($LOCAT1 != ""){
			$cond .= " AND (I.RVLOCAT LIKE '".$LOCAT1."%')";
			$rpcond .= "  สถานที่รับรถ ".$LOCAT1;
		}
		
		if($APCODE1 != ""){
			$cond .= " AND (I.APCODE LIKE '".$APCODE1."%') ";
			$rpcond .= "  รหัสเจ้าหนี้ ".$APCODE1;
		}
		
		$sql = "
				IF OBJECT_ID('tempdb..#MAIN') IS NOT NULL DROP TABLE #MAIN
				SELECT *
				INTO #MAIN
				FROM(
					SELECT I.RVLOCAT, I.RECVNO, I.RECVDT, convert(char,I.RECVDT,112) as RECVDTS, I.INVNO, convert(char,I.INVDT,112) as INVDT, 
					I.TAXNO, convert(char,I.TAXDT,112) as TAXDT, I.APCODE, A.APNAME, T.OPTCODE, T.OPTNAME, T.QTY, T.UNITCST, 
					T.UNITCST*T.QTY AS TOT1, T.DSCAMT, (T.UNITCST*T.QTY)-T.DSCAMT AS TOT2
					FROM  {$this->MAuth->getdb('OPTINV')} I 
					LEFT JOIN {$this->MAuth->getdb('OPTTRAN')} T ON I.RECVNO = T.RECVNO
					LEFT JOIN {$this->MAuth->getdb('APMAST')} A ON I.APCODE = A.APCODE
					WHERE (I.RECVDT BETWEEN '".$FRMDATE."' AND '".$TODATE."') 
				)MAIN
		";//echo $sql; 
		$query = $this->db->query($sql);
		
		$sql = "
				SELECT RVLOCAT, RECVNO, RECVDT, RECVDTS, INVNO, INVDT, TAXNO, TAXDT, APCODE, APNAME, OPTCODE, OPTNAME, 
				QTY, UNITCST, TOT1, DSCAMT, TOT2
				FROM #MAIN
				ORDER BY ".$orderby.", OPTCODE
		";//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$sql = "
				SELECT 'รวม '+CONVERT(NVARCHAR,COUNT(RECVNO+RVLOCAT))+' รายการ' as Total, sum(TOT2) as sumTOT2
				FROM (
					SELECT RECVNO, RVLOCAT, sum(isnull(TOT2,0)) as TOT2 FROM #MAIN GROUP BY RECVNO, RVLOCAT
				)A
		";//echo $sql; exit;
		$query2 = $this->db->query($sql);
		
		$head = ""; $html = ""; $head2 = "";  $report = ""; $sumreport = ""; $sumreport2 = ""; $i = 0; 
		
		$head = "<tr style='height:25px;'>
				<th style='display:none;'>#</th>
				<th style='vertical-align:top;text-align:left;'>รับจากสาขา</th>
				<th style='vertical-align:top;text-align:left;'>เลขที่ใบรับ</th>
				<th style='vertical-align:top;text-align:left;'>วันที่รับ</th>
				<th style='vertical-align:top;text-align:left;'>เลขที่ใบส่ง<br>วันที่ส่ง</th>
				<th style='vertical-align:top;text-align:left;'>เลขใบกำกับ<br>วันที่ใบกำกับ</th>
				<th style='vertical-align:top;text-align:left;'>รหัสเจ้าหนี้<br>ชื่อบริษัทเจ้าหนี้</th>
				<th style='vertical-align:top;text-align:left;'>รหัสอุปกรณ์<br>ชื่ออุปกรณ์</th>
				<th style='vertical-align:top;text-align:right;'>จำนวน</th>
				<th style='vertical-align:top;text-align:right;'>ราคา/หน่วย</th>
				<th style='vertical-align:top;text-align:right;'>เป็นเงิน</th>
				<th style='vertical-align:top;text-align:right;'>ส่วนลด</th>
				<th style='vertical-align:top;text-align:right;'>รวมเงินสุทธิ</th>
				</tr>
		";
		
		$NRow = 1;
		if($query->row()){
			foreach($query->result() as $row){$i++;
				$html .= "
					<tr class='trow' seq=".$NRow.">
						<td seq=".$NRow++." style='display:none;'></td>
						<td>".$row->RVLOCAT."</td>
						<td>".$row->RECVNO."</td>
						<td>".$this->Convertdate(2,$row->RECVDTS)."</td>
						<td>".$row->INVNO."<br>".$this->Convertdate(2,$row->INVDT)."</td>
						<td>".$row->TAXNO."<br>".$this->Convertdate(2,$row->TAXDT)."</td>
						<td>".$row->APCODE."<br>".$row->APNAME."</td>
						<td>".$row->OPTCODE."<br>".$row->OPTNAME."</td>
						<td align='right'>".number_format($row->QTY)."</td>
						<td align='right'>".number_format($row->UNITCST,2)."</td>
						<td align='right'>".number_format($row->TOT1,2)."</td>
						<td align='right'>".number_format($row->DSCAMT,2)."</td>
						<td align='right'>".number_format($row->TOT2,2)."</td>
					</tr>
				";	
			}
		}
		
		$head2 = "<tr>
					<th style='vertical-align:top;text-align:left;'>#</th>
					<th style='vertical-align:top;text-align:left;'>รับจากสาขา</th>
					<th style='vertical-align:top;text-align:left;'>เลขที่ใบรับ</th>
					<th style='vertical-align:top;text-align:left;'>วันที่รับ</th>
					<th style='vertical-align:top;text-align:left;'>เลขที่ใบส่ง</th>
					<th style='vertical-align:top;text-align:left;'>วันที่ส่ง</th>
					<th style='vertical-align:top;text-align:left;'>เลขใบกำกับ</th>
					<th style='vertical-align:top;text-align:left;'>วันที่ใบกำกับ</th>
					<th style='vertical-align:top;text-align:left;'>รหัสเจ้าหนี้</th>
					<th style='vertical-align:top;text-align:left;'>ชื่อบริษัทเจ้าหนี้</th>
					<th style='vertical-align:top;text-align:left;'>รหัสอุปกรณ์</th>
					<th style='vertical-align:top;text-align:left;'>ชื่ออุปกรณ์</th>
					<th style='vertical-align:top;text-align:right;'>จำนวน</th>
					<th style='vertical-align:top;text-align:right;'>ราคา/หน่วย</th>
					<th style='vertical-align:top;text-align:right;'>เป็นเงิน</th>
					<th style='vertical-align:top;text-align:right;'>ส่วนลด</th>
					<th style='vertical-align:top;text-align:right;'>รวมเงินสุทธิ</th>
				</tr>
		";

		$No = 1;
		if($query->row()){
			foreach($query->result() as $row){
				$report .= "
					<tr class='trow'>
						<td style='mso-number-format:\"\@\";'>".$No++."</td>
						<td style='mso-number-format:\"\@\";'>".$row->RVLOCAT."</td>
						<td style='mso-number-format:\"\@\";'>".$row->RECVNO."</td>
						<td style='mso-number-format:\"\@\";'>".$this->Convertdate(2,$row->RECVDTS)."</td>
						<td style='mso-number-format:\"\@\";'>".$row->INVNO."</td>
						<td style='mso-number-format:\"\@\";'>".$this->Convertdate(2,$row->INVDT)."</td>
						<td style='mso-number-format:\"\@\";'>".$row->TAXNO."</td>
						<td style='mso-number-format:\"\@\";'>".$this->Convertdate(2,$row->TAXDT)."</td>
						<td style='mso-number-format:\"\@\";'>".$row->APCODE."</td>
						<td style='mso-number-format:\"\@\";'>".$row->APNAME."</td>
						<td style='mso-number-format:\"\@\";'>".$row->OPTCODE."</td>
						<td style='mso-number-format:\"\@\";'>".$row->OPTNAME."</td>
						<td style='mso-number-format:\"\#\,\#\#0\";text-align:right;'>".number_format($row->QTY)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->UNITCST,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->TOT1,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->DSCAMT,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->TOT2,2)."</td>
					</tr>
				";	
			}
		}
		
		if($query2->row()){
			foreach($query2->result() as $row){
				$sumreport = "
					<tr style='height:25px;'>
						<th colspan='11' style='border-left:0px;border-bottom:0px;border-top:0px;vertical-align:middle;text-align:left;'>".$row->Total."</th>
						<th style='border:0px;text-align:right;'>".number_format($row->sumTOT2,2)."</th>
					</tr>
				";	
			}
		}
		
		if($query2->row()){
			foreach($query2->result() as $row){
				$sumreport2 = "
					<tr class='trow'>
						<th style='mso-number-format:\"\@\";text-align:left;' colspan='16'>".$row->Total."</th>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumTOT2,2)."</td>
					</tr>
				";	
			}
		}
		
		if($i>0){
			$html = "
				<div id='table-fixed-ReportOptionaudit' class='col-sm-12' style='height:100%;width:100%;overflow:auto;font-size:9pt;'>
					<table id='table-ReportOptionaudit' style='background-color:white;' class='col-sm-12 display table table-bordered' cellspacing='0' width='calc(100% - 1px)'>
						<thead>
						<tr style='height:40px;'>
							<th colspan='12' style='font-size:12pt;border:0px;vertical-align;middle;text-align:center;'>รายงานตรวจสอบการรับอุปกรณ์</th>
						</tr>
						<tr style='height:25px;'>
							<td colspan='12' style='border-bottom:1px solid #ddd;vertical-align;middle;text-align:center;'>จากวันที่ ".$_REQUEST["FRMDATE"]." - ".$_REQUEST["TODATE"]." ".$rpcond."  ออกรายงาน ณ วันที่ ".$this->today('today')."</td>
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
			<div id='table-fixed-ReportOptionaudit2' class='col-sm-12' style='height:100%;width:100%;overflow:auto;'>
				<table id='table-ReportOptionaudit2' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='100%'>
					<thead>
						<tr>
							<th colspan='17' style='font-size:12pt;border:0px;text-align:center;'>รายงานตรวจสอบการรับอุปกรณ์</th>
						</tr>
						<tr>
							<td colspan='17' style='border:0px;text-align:center;'>จากวันที่ ".$_REQUEST["FRMDATE"]." - ".$_REQUEST["TODATE"]." ".$rpcond." ออกรายงาน ณ วันที่ ".$this->today('today')."</td>
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
						$_REQUEST["APCODE1"].'||'.
						$_REQUEST["FRMDATE"].'||'.
						$_REQUEST["TODATE"].'||'.
						$_REQUEST["orderby"].'||'.
						$_REQUEST["layout"]
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
		$APCODE1 	= str_replace(chr(0),'',$tx[1]);
		$FRMDATE 	= $this->Convertdate(1,$tx[2]);
		$TODATE 	= $this->Convertdate(1,$tx[3]);
		$orderby 	= $tx[4];
		$layout 	= $tx[5];
		
		$cond = ""; $rpcond = "";
		
		if($LOCAT1 != ""){
			$cond .= " AND (I.RVLOCAT LIKE '".$LOCAT1."%')";
			$rpcond .= "  สถานที่รับรถ ".$LOCAT1;
		}
		
		if($APCODE1 != ""){
			$cond .= " AND (I.APCODE LIKE '".$APCODE1."%') ";
			$rpcond .= "  รหัสเจ้าหนี้ ".$APCODE1;
		}
		
		$sql = "
				IF OBJECT_ID('tempdb..#MAIN') IS NOT NULL DROP TABLE #MAIN
				SELECT *
				INTO #MAIN
				FROM(
					SELECT I.RVLOCAT, I.RECVNO, I.RECVDT, convert(char,I.RECVDT,112) as RECVDTS, I.INVNO, convert(char,I.INVDT,112) as INVDT, 
					I.TAXNO, convert(char,I.TAXDT,112) as TAXDT, I.APCODE, A.APNAME, T.OPTCODE, T.OPTNAME, T.QTY, T.UNITCST, 
					T.UNITCST*T.QTY AS TOT1, T.DSCAMT, (T.UNITCST*T.QTY)-T.DSCAMT AS TOT2
					FROM  {$this->MAuth->getdb('OPTINV')} I 
					LEFT JOIN {$this->MAuth->getdb('OPTTRAN')} T ON I.RECVNO = T.RECVNO
					LEFT JOIN {$this->MAuth->getdb('APMAST')} A ON I.APCODE = A.APCODE
					WHERE (I.RECVDT BETWEEN '".$FRMDATE."' AND '".$TODATE."') 
				)MAIN
		";//echo $sql; 
		$query = $this->db->query($sql);
		
		$sql = "
				SELECT RVLOCAT, RECVNO, RECVDT, RECVDTS, INVNO, INVDT, TAXNO, TAXDT, APCODE, APNAME, OPTCODE, OPTNAME, 
				QTY, UNITCST, TOT1, DSCAMT, TOT2
				FROM #MAIN
				ORDER BY ".$orderby.", OPTCODE
		";//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$sql = "
				SELECT 'รวม '+CONVERT(NVARCHAR,COUNT(RECVNO+RVLOCAT))+' รายการ' as Total, sum(TOT2) as sumTOT2
				FROM (
					SELECT RECVNO, RVLOCAT, sum(isnull(TOT2,0)) as TOT2 FROM #MAIN GROUP BY RECVNO, RVLOCAT
				)A
		";//echo $sql; exit;
		$query2 = $this->db->query($sql);
		
		$head = ""; $html = ""; $i=0; 

		$head = "
				<tr>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;display:none;'>#</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>รับจากสาขา</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>เลขที่ใบรับ</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>วันที่รับ</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>เลขที่ใบส่ง<br>วันที่ส่ง</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>เลขใบกำกับ<br>วันที่ใบกำกับ</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>รหัสเจ้าหนี้<br>ชื่อบ.เจ้าหนี้</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>รหัสอุปกรณ์<br>ชื่ออุปกรณ์</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>จำนวน</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>ราคา/หน่วย</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>เป็นเงิน</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>ส่วนลด</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>รวมเงินสุทธิ</th>
				</tr>
		";
		
		$No = 1;
		if($query->row()){
			foreach($query->result() as $row){	
				$html .= "
					<tr class='trow' seq=".$No.">
						<td style='width:30px;display:none;'>".$No++."</td>
						<td style='width:60px;'>".$row->RVLOCAT."</td>
						<td style='width:90px;'>".$row->RECVNO."</td>
						<td style='width:75px;'>".$this->Convertdate(2,$row->RECVDTS)."</td>
						<td style='width:90px;'>".$row->INVNO."<br>".$this->Convertdate(2,$row->INVDT)."</td>
						<td style='width:90px;'>".$row->TAXNO."<br>".$this->Convertdate(2,$row->TAXDT)."</td>
						<td style='width:120px;'>".$row->APCODE."<br>".$row->APNAME."</td>
						<td style='width:120px;'>".$row->OPTCODE."<br>".$row->OPTNAME."</td>
						<td style='width:60px;' align='right'>".number_format($row->QTY)."</td>
						<td style='width:70px;' align='right'>".number_format($row->UNITCST,2)."</td>
						<td style='width:70px;' align='right'>".number_format($row->TOT1,2)."</td>
						<td style='width:70px;' align='right'>".number_format($row->DSCAMT,2)."</td>
						<td style='width:70px;' align='right'>".number_format($row->TOT2,2)."</td>
					</tr>
				";	
			}
		}
		
		if($query2->row()){
			foreach($query2->result() as $row){	
				$html .= "
					<tr class='trow bor' style='background-color:#ebebeb;'>
						<th colspan='11' style='text-align:left;vertical-align:middle;'>".$row->Total."</th>
						<th style='text-align:right;vertical-align:middle;'>".number_format($row->sumTOT2,2)."</th>
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
			<table class='wf' style='font-size:8pt;height:700px;width:100%;border-collapse:collapse;line-height:23px;overflow:wrap;vertical-align:text-top;'>
				<tbody>
					<tr>
						<th colspan='12' style='font-size:10pt;'>รายงานตรวจสอบการรับอุปกรณ์</th>
					</tr>
					<tr>
						<td colspan='12' style='font-size:9pt;height:35px;border-bottom:0.1px solid black;text-align:center;'>จากวันที่ ".$tx[2]." - ".$tx[3]." ".$rpcond."</td>
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