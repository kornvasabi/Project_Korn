<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//BEE+
class ReportReceivebydatereceipt extends MY_Controller {
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
							<br>รายงานตรวจสอบการรับสินค้า<br>
						</div>
						<div class='col-sm-8 col-xs-8 col-sm-offset-2'>
							<br>
							<div class='col-sm-4 col-xs-4'>	
								<div class='form-group'>
									สถานที่รับรถ
									<select id='LOCAT1' class='form-control input-sm' data-placeholder='สถานที่รับรถ'></select>
								</div>
							</div>
						</div>
						<div class='col-sm-8 col-xs-8 col-sm-offset-2'>
							<div class='col-sm-4 col-xs-4'>	
								<div class='form-group'>
									จากวันที่
									<input type='text' id='FRMDATE' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' placeholder='จากวันที่' value='".$this->today('today')."' style='font-size:10.5pt'>
								</div>
							</div>
							<div class='col-sm-4 col-xs-4'>	
								<div class='form-group'>
									ถึงวันที่
									<input type='text' id='TODATE' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' placeholder='ถึงวันที่' value='".$this->today('today')."' style='font-size:10.5pt'>
								</div>
							</div>
							<div class='col-sm-4 col-xs-4'>	
								<div class='form-group' >
									เจ้าหนี้
									<select id='APCODE1' class='form-control input-sm' data-placeholder='เจ้าหนี้'></select>
								</div>
							</div>
							<div class='col-sm-4 col-xs-4'>	
								<div class='form-group' >
									ยี่ห้อ
									<select id='TYPE1' class='form-control input-sm' data-placeholder='ยี่ห้อ'></select>
								</div>
							</div>
							<div class='col-sm-4 col-xs-4'>	
								<div class='form-group' >
									กลุ่มสินค้า
									<select id='GCOCE1' class='form-control input-sm' data-placeholder='กลุ่มสินค้า'></select>
								</div>
							</div>
							<div class='col-sm-4 col-xs-4'>	
								<div class='form-group' >
									แบบ
									<select id='BAAB1' class='form-control input-sm' data-placeholder='แบบ'></select>
								</div>
							</div>
							<div class='col-sm-4 col-xs-4'>	
								<div class='form-group' >
									รุ่น
									<select id='MODEL1' class='form-control input-sm' data-placeholder='รุ่น'></select>
								</div>
							</div>
							<div class='col-sm-4 col-xs-4'>	
								<div class='form-group' >
									ขนาด>=
									<select id='CC1' class='form-control input-sm' data-placeholder='ขนาด'></select>
								</div>
							</div>
							<div class='col-sm-4 col-xs-4'>	
								<div class='form-group' >
									สี
									<select id='COLOR1' class='form-control input-sm' data-placeholder='สี'></select>
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
												<br>
												<input type= 'radio' id='ver' name='layout'> แนวตั้ง
												<br><br><br>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class='col-sm-4 col-xs-4'>	
								<div class='form-group'>
									สถานะสินค้า
									<div class='col-sm-12 col-xs-12' style='border:0.1px dotted #d6d6d6;'>	
										<div class='col-sm-12 col-xs-12'>
											<div class='form-group'>
												<br>
												<input type= 'radio' id='new' name='stat'> ใหม่
												<br>
												<input type= 'radio' id='old' name='stat'> เก่า
												<br>
												<input type= 'radio' id='all' name='stat' checked> ทั้งหมด
												<br><br>
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
												<input type= 'radio' id='rcvno' name='orderby' checked> ตามใบรับ, เลขตัวถัง
												<br>
												<input type= 'radio' id='rcvdate' name='orderby'> ตามวันที่ใบรับ
												<br>
												<input type= 'radio' id='strno' name='orderby'> ตามเลขตัวถัง
												<br>
												<input type= 'radio' id='types' name='orderby'> ตามยี่ห้อ รุ่น แบบ สี
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
		
		$html.= "<script src='".base_url('public/js/SYS02/ReportReceivebydatereceipt.js')."'></script>";
		echo $html;
	}
	
	function search(){
		$LOCAT1		= $_REQUEST["LOCAT1"];
		$APCODE1 	= str_replace(chr(0),'',$_REQUEST["APCODE1"]);
		$TYPE1 		= str_replace(chr(0),'',$_REQUEST["TYPE1"]);
		$GCOCE1 	= str_replace(chr(0),'',$_REQUEST["GCOCE1"]);
		$BAAB1 		= str_replace(chr(0),'',$_REQUEST["BAAB1"]);
		$MODEL1 	= str_replace(chr(0),'',$_REQUEST["MODEL1"]);
		$CC1 		= str_replace(chr(0),'',$_REQUEST["CC1"]);
		$COLOR1 	= str_replace(chr(0),'',$_REQUEST["COLOR1"]);
		$FRMDATE 	= $this->Convertdate(1,$_REQUEST["FRMDATE"]);
		$TODATE 	= $this->Convertdate(1,$_REQUEST["TODATE"]);
		$stat 		= $_REQUEST["stat"];
		$orderby 	= $_REQUEST["orderby"];
		
		$cond = ""; $rpcond = "";
		
		if($LOCAT1 != ""){
			$cond .= " AND (T.RVLOCAT LIKE '".$LOCAT1."%')";
			$rpcond .= "  สถานที่รับรถ ".$LOCAT1;
		}
		
		if($APCODE1 != ""){
			$cond .= " AND (V.APCODE LIKE '".$APCODE1."%') ";
			$rpcond .= "  รหัสเจ้าหนี้ ".$APCODE1;
		}
		
		if($TYPE1 != ""){
			$cond .= " AND (T.TYPE LIKE '".$TYPE1."%')";
		}
		
		if($GCOCE1 != ""){
			$cond .= " AND ( T.GCODE LIKE '".$GCOCE1."%') ";
		}
		
		if($BAAB1 != ""){
			$cond .= " AND (T.BAAB LIKE '".$BAAB1."%')";
		}
		
		if($MODEL1 != ""){
			$cond .= " AND (T.MODEL LIKE '".$MODEL1."%') ";
		}
		
		if($CC1 != ""){
			$cond .= " AND (T.CC >= ".$CC1." ) ";
		}else{
			$cond .= " AND (T.CC >= 0 ) ";
		}
		
		if($COLOR1 != ""){
			$cond .= " AND (T.COLOR LIKE '".$COLOR1."%') ";
		}
		
		if($stat != ""){
			$cond .= " AND (T.STAT LIKE '".$stat."%')";
		}
		
		$sql = "
				IF OBJECT_ID('tempdb..#main') IS NOT NULL DROP TABLE #main
				select *
				into #main
				from(
					SELECT V.RECVNO, V.RECVDT, CONVERT(char,V.RECVDT,112) as RECVDTS, V.INVNO, CONVERT(char,V.INVDT,112) as INVDT, 
					V.TAXNO, CONVERT(char,V.TAXDT,112) as TAXDT, V.LOCAT, V.APCODE, T.RVCODE, T.STAT, T.GCODE, T.TYPE, T.MODEL, 
					T.BAAB, T.COLOR, T.CC, T.STRNO, T.ENGNO, T.MILERT
					FROM {$this->MAuth->getdb('INVINVO')} V
					LEFT JOIN {$this->MAuth->getdb('INVTRAN')} T ON V.RECVNO=T.RECVNO AND V.LOCAT=T.RVLOCAT
					WHERE (V.RECVDT BETWEEN '".$FRMDATE."' AND '".$TODATE."') ".$cond."
				)main 
		";//echo $sql; 
		$query = $this->db->query($sql);
		
		$sql = "
				SELECT  RECVNO, RECVDTS, INVNO, INVDT, TAXNO, TAXDT, LOCAT, APCODE, RVCODE, STAT, GCODE, TYPE, MODEL, 
				BAAB, COLOR, CC, STRNO, ENGNO, MILERT
				FROM #main
				ORDER BY ".$orderby."
		";//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$sql = "
				SELECT 'รวมทั้งหมด  '+convert(nvarchar,COUNT(RECVNO+STRNO+GCODE))+' คัน' as Total FROM #main
		";//echo $sql; exit;
		$query2 = $this->db->query($sql);
		
		$head = ""; $html = ""; $head2 = "";  $report = ""; $sumreport = ""; $sumreport2 = ""; $i = 0; 
		
		$head = "<tr style='height:25px;'>
				<th style='display:none;'>#</th>
				<th style='vertical-align:top;'>เลขที่ใบรับ</th>
				<th style='vertical-align:top;'>วันที่รับ<br>สถานที่รับรถ</th>
				<th style='vertical-align:top;'>เลขที่ใบส่ง<br>วันที่ส่ง</th>
				<th style='vertical-align:top;'>เลขที่ใบกำกับ<br>วันที่ใบกำกับ</th> 
				<th style='vertical-align:top;'>รหัสเจ้าหนี้<br>รหัสผู้รับ</th>
				<th style='vertical-align:top;'>สถานะ<br>กลุ่มสินค้า</th>
				<th style='vertical-align:top;'>ยี่ห้อ<br>แบบ</th>
				<th style='vertical-align:top;'>รุ่น<br>สี</th>
				<th style='vertical-align:top;'>เลขตัวถัง<br>ขนาด</th>
				<th style='vertical-align:top;'>เลขเครื่อง<br>เลขไมล์</th>
				</tr>
		";
		
		$NRow = 1;
		if($query->row()){
			foreach($query->result() as $row){$i++;
				$html .= "
					<tr class='trow' seq=".$NRow.">
						<td seq=".$NRow++." style='display:none;'></td>
						<td>".$row->RECVNO."</td>
						<td>".$this->Convertdate(2,$row->RECVDTS)."<br>".$row->LOCAT."</td>
						<td>".$row->INVNO."<br>".$this->Convertdate(2,$row->INVDT)."</td>
						<td>".$row->TAXNO."<br>".$this->Convertdate(2,$row->TAXDT)."</td>
						<td>".$row->APCODE."<br>".$row->RVCODE."</td>
						<td>".$row->STAT."<br>".$row->GCODE."</td>
						<td>".$row->TYPE."<br>".$row->BAAB."</td>
						<td>".$row->MODEL."<br>".$row->COLOR."</td>
						<td>".$row->STRNO."<br>".number_format($row->CC)."</td>
						<td>".$row->ENGNO."<br>".number_format($row->MILERT)."</td>
					</tr>
				";	
			}
		}
		
		$head2 = "<tr>
					<th style='vertical-align:middle;'>#</th>
					<th style='vertical-align:top;'>เลขที่ใบรับ</th>
					<th style='vertical-align:top;'>วันที่รับ</th>
					<th style='vertical-align:top;'>สถานที่รับรถ</th>
					<th style='vertical-align:top;'>เลขที่ใบส่ง</th>
					<th style='vertical-align:top;'>วันที่ส่ง</th>
					<th style='vertical-align:top;'>เลขที่ใบกำกับ</th> 
					<th style='vertical-align:top;'>วันที่ใบกำกับ</th> 
					<th style='vertical-align:top;'>รหัสเจ้าหนี้</th>
					<th style='vertical-align:top;'>รหัสผู้รับ</th>
					<th style='vertical-align:top;'>สถานะ</th>
					<th style='vertical-align:top;'>กลุ่มสินค้า</th>
					<th style='vertical-align:top;'>ยี่ห้อ</th>
					<th style='vertical-align:top;'>รุ่น</th>
					<th style='vertical-align:top;'>แบบ</th>
					<th style='vertical-align:top;'>สี</th>
					<th style='vertical-align:top;'>ขนาด</th>
					<th style='vertical-align:top;'>เลขตัวถัง</th>
					<th style='vertical-align:top;'>เลขเครื่อง</th>
					<th style='vertical-align:top;'>เลขไมล์</th>
				</tr>
		";
		
		$No = 1;
		if($query->row()){
			foreach($query->result() as $row){
				$report .= "
					<tr class='trow'>
						<td style='mso-number-format:\"\@\";'>".$No++."</td>
						<td style='mso-number-format:\"\@\";'>".$row->RECVNO."</td>
						<td style='mso-number-format:\"\@\";'>".$this->Convertdate(2,$row->RECVDTS)."</td>
						<td style='mso-number-format:\"\@\";'>".$row->LOCAT."</td>
						<td style='mso-number-format:\"\@\";'>".$row->INVNO."</td>
						<td style='mso-number-format:\"\@\";'>".$this->Convertdate(2,$row->INVDT)."</td>
						<td style='mso-number-format:\"\@\";'>".$row->TAXNO."</td>
						<td style='mso-number-format:\"\@\";'>".$this->Convertdate(2,$row->TAXDT)."</td>
						<td style='mso-number-format:\"\@\";'>".$row->APCODE."</td>
						<td style='mso-number-format:\"\@\";'>".$row->RVCODE."</td>
						<td style='mso-number-format:\"\@\";'>".$row->STAT."</td>
						<td style='mso-number-format:\"\@\";'>".$row->GCODE."</td>
						<td style='mso-number-format:\"\@\";'>".$row->TYPE."</td>
						<td style='mso-number-format:\"\@\";'>".$row->MODEL."</td>
						<td style='mso-number-format:\"\@\";'>".$row->BAAB."</td>
						<td style='mso-number-format:\"\@\";'>".$row->COLOR."</td>
						<td style='mso-number-format:\"\#\,\#\#0\";'>".number_format($row->CC)."</td>
						<td style='mso-number-format:\"\@\";'>".$row->STRNO."</td>
						<td style='mso-number-format:\"\@\";'>".$row->ENGNO."</td>
						<td style='mso-number-format:\"\#\,\#\#0\";'>".number_format($row->MILERT)."</td>
					</tr>
				";	
			}
		}
		
		if($query2->row()){
			foreach($query2->result() as $row){
				$sumreport = "
					<tr style='height:25px;'>
						<th colspan='10' style='border-left:0px;border-bottom:0px;border-top:0px;vertical-align:middle;text-align:left;'>".$row->Total."</th>
					</tr>
				";	
			}
		}
		
		if($query2->row()){
			foreach($query2->result() as $row){
				$sumreport2 = "
					<tr class='trow'>
						<th style='mso-number-format:\"\@\";text-align:left;' colspan='20'>".$row->Total."</th>
					</tr>
				";	
			}
		}
		
		if($i>0){
			$html = "
				<div id='table-fixed-ReportReceivebydatereceipt' class='col-sm-12' style='height:100%;width:100%;overflow:auto;font-size:9pt;'>
					<table id='table-ReportReceivebydatereceipt' style='background-color:white;' class='col-sm-12 display table table-bordered' cellspacing='0' width='calc(100% - 1px)'>
						<thead>
						<tr style='height:40px;'>
							<th colspan='10' style='font-size:12pt;border:0px;vertical-align;middle;text-align:center;'>รายงานตรวจสอบการรับสินค้า</th>
						</tr>
						<tr style='height:25px;'>
							<td colspan='10' style='border-bottom:1px solid #ddd;vertical-align;middle;text-align:center;'>จากวันที่ ".$_REQUEST["FRMDATE"]." - ".$_REQUEST["TODATE"]." ".$rpcond."  ออกรายงาน ณ วันที่ ".$this->today('today')."</td>
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
			<div id='table-fixed-ReportReceivebydatereceipt2' class='col-sm-12' style='height:100%;width:100%;overflow:auto;'>
				<table id='table-ReportReceivebydatereceipt2' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='100%'>
					<thead>
						<tr>
							<th colspan='20' style='font-size:12pt;border:0px;text-align:center;'>รายงานตรวจสอบการรับสินค้า</th>
						</tr>
						<tr>
							<td colspan='20' style='border:0px;text-align:center;'>จากวันที่ ".$_REQUEST["FRMDATE"]." - ".$_REQUEST["TODATE"]." ".$rpcond." ออกรายงาน ณ วันที่ ".$this->today('today')."</td>
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
						$_REQUEST["TYPE1"].'||'.
						$_REQUEST["GCOCE1"].'||'.
						$_REQUEST["BAAB1"].'||'.
						$_REQUEST["MODEL1"].'||'.
						$_REQUEST["CC1"].'||'.
						$_REQUEST["COLOR1"].'||'.
						$_REQUEST["FRMDATE"].'||'.
						$_REQUEST["TODATE"].'||'.
						$_REQUEST["stat"].'||'.
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
		$TYPE1 		= str_replace(chr(0),'',$tx[2]);
		$GCOCE1 	= str_replace(chr(0),'',$tx[3]);
		$BAAB1 		= str_replace(chr(0),'',$tx[4]);
		$MODEL1 	= str_replace(chr(0),'',$tx[5]);
		$CC1 		= str_replace(chr(0),'',$tx[6]);
		$COLOR1 	= str_replace(chr(0),'',$tx[7]);
		$FRMDATE 	= $this->Convertdate(1,$tx[8]);
		$TODATE 	= $this->Convertdate(1,$tx[9]);
		$stat 		= $tx[10];
		$orderby 	= $tx[11];
		$layout 	= $tx[12];
		
		$cond = ""; $rpcond = "";
		
		if($LOCAT1 != ""){
			$cond .= " AND (T.RVLOCAT LIKE '".$LOCAT1."%')";
			$rpcond .= "  สถานที่รับรถ ".$LOCAT1;
		}
		
		if($APCODE1 != ""){
			$cond .= " AND (V.APCODE LIKE '".$APCODE1."%') ";
			$rpcond .= "  รหัสเจ้าหนี้ ".$APCODE1;
		}
		
		if($TYPE1 != ""){
			$cond .= " AND (T.TYPE LIKE '".$TYPE1."%')";
		}
		
		if($GCOCE1 != ""){
			$cond .= " AND ( T.GCODE LIKE '".$GCOCE1."%') ";
		}
		
		if($BAAB1 != ""){
			$cond .= " AND (T.BAAB LIKE '".$BAAB1."%')";
		}
		
		if($MODEL1 != ""){
			$cond .= " AND (T.MODEL LIKE '".$MODEL1."%') ";
		}
		
		if($CC1 != ""){
			$cond .= " AND (T.CC >= ".$CC1." ) ";
		}else{
			$cond .= " AND (T.CC >= 0 ) ";
		}
		
		if($COLOR1 != ""){
			$cond .= " AND (T.COLOR LIKE '".$COLOR1."%') ";
		}
		
		if($stat != ""){
			$cond .= " AND (T.STAT LIKE '".$stat."%')";
		}
		
		$sql = "
				IF OBJECT_ID('tempdb..#main') IS NOT NULL DROP TABLE #main
				select *
				into #main
				from(
					SELECT V.RECVNO, V.RECVDT, CONVERT(char,V.RECVDT,112) as RECVDTS, V.INVNO, CONVERT(char,V.INVDT,112) as INVDT, 
					V.TAXNO, CONVERT(char,V.TAXDT,112) as TAXDT, V.LOCAT, V.APCODE, T.RVCODE, T.STAT, T.GCODE, T.TYPE, T.MODEL, 
					T.BAAB, T.COLOR, T.CC, T.STRNO, T.ENGNO, T.MILERT
					FROM {$this->MAuth->getdb('INVINVO')} V
					LEFT JOIN {$this->MAuth->getdb('INVTRAN')} T ON V.RECVNO=T.RECVNO AND V.LOCAT=T.RVLOCAT
					WHERE (V.RECVDT BETWEEN '".$FRMDATE."' AND '".$TODATE."') ".$cond."
				)main 
		";//echo $sql; 
		$query = $this->db->query($sql);
		
		$sql = "
				SELECT  RECVNO, RECVDTS, INVNO, INVDT, TAXNO, TAXDT, LOCAT, APCODE, RVCODE, STAT, GCODE, TYPE, MODEL, 
				BAAB, COLOR, CC, STRNO, ENGNO, MILERT
				FROM #main
				ORDER BY ".$orderby."
		";//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$sql = "
				SELECT 'รวมทั้งหมด  '+convert(nvarchar,COUNT(RECVNO+STRNO+GCODE))+' คัน' as Total FROM #main
		";//echo $sql; exit;
		$query2 = $this->db->query($sql);
		
		$head = ""; $html = ""; $i=0; 

		$head = "
				<tr>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>#</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>เลขที่ใบรับ</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>วันที่รับ<br>สถานที่รับรถ</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>เลขที่ใบส่ง<br>วันที่ส่ง</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>เลขที่ใบกำกับ<br>วันที่ใบกำกับ</th> 
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>รหัสเจ้าหนี้<br>รหัสผู้รับ</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>สถานะ<br>กลุ่มสินค้า</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>ยี่ห้อ<br>แบบ</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>รุ่น<br>สี</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>เลขตัวถัง<br>ขนาด</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>เลขเครื่อง<br>เลขไมล์</th>
				</tr>
		";
		
		$No = 1;
		if($query->row()){
			foreach($query->result() as $row){	
				$html .= "
					<tr class='trow' seq=".$No.">
						<td style='width:30px;'>".$No++."</td>
						<td style='width:80px;'>".$row->RECVNO."</td>
						<td style='width:80px;'>".$this->Convertdate(2,$row->RECVDTS)."<br>".$row->LOCAT."</td>
						<td style='width:80px;'>".$row->INVNO."<br>".$this->Convertdate(2,$row->INVDT)."</td>
						<td style='width:80px;'>".$row->TAXNO."<br>".$this->Convertdate(2,$row->TAXDT)."</td>
						<td style='width:70px;'>".$row->APCODE."<br>".$row->RVCODE."</td>
						<td style='width:70px;'>".$row->STAT."<br>".$row->GCODE."</td>
						<td style='width:120px;'>".$row->TYPE."<br>".$row->BAAB."</td>
						<td style='width:120px;'>".$row->MODEL."<br>".$row->COLOR."</td>
						<td style='width:130px;'>".$row->STRNO."<br>".number_format($row->CC)."</td>
						<td style='width:130px;'>".$row->ENGNO."<br>".number_format($row->MILERT)."</td>
					</tr>
				";	
			}
		}
		
		if($query2->row()){
			foreach($query2->result() as $row){	
				$html .= "
					<tr class='trow bor' style='background-color:#ebebeb;'>
						<th colspan='11' style='text-align:left;vertical-align:middle;'>".$row->Total."</th>
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
						<th colspan='11' style='font-size:10pt;'>รายงานตรวจสอบการรับสินค้า</th>
					</tr>
					<tr>
						<td colspan='11' style='font-size:9pt;height:35px;border-bottom:0.1px solid black;text-align:center;'>จากวันที่ ".$tx[8]." - ".$tx[9]." ".$rpcond."</td>
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