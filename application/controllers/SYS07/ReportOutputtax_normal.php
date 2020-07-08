<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//BEE+
class ReportOutputtax_normal extends MY_Controller {
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
					<div class='row' style='height:93%;'>
						<div class='col-sm-12 col-xs-12 bg-info' style='border:5px solid white;height:75px;text-align:center;font-size:12pt;color:white;font-weight:bold;'>	
							<br>รายงานภาษีขาย(ยื่นปกติ)<br>
						</div>
						<div class='col-sm-8 col-xs-8 col-sm-offset-2'>
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group'>
									สาขา
									<select id='LOCAT1' class='form-control input-sm' data-placeholder='สถานที่รับรถ'></select>
								</div>
							</div>
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group' >
									ประเภทการขาย
									<select id='TSALE1' class='form-control input-sm' data-placeholder='ประเภทการขาย'></select>
								</div>
							</div>
							<div class='col-sm-6 col-xs-6'>	
								จากวันที่
								<input type='text' id='FRMDATE' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' placeholder='จากวันที่' value='".$this->today('today')."' style='font-size:10.5pt'>
							</div>
							<div class='col-sm-6 col-xs-6'>	
								ถึงวันที่
								<input type='text' id='TODATE' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' placeholder='ถึงวันที่' value='".$this->today('today')."' style='font-size:10.5pt'>
							</div>
						</div>
						<div class='col-sm-8 col-xs-8 col-sm-offset-2'>
							<br>
							<div class='col-sm-12 col-xs-12'>	
								ประเภทรายงาน
								<div class='col-sm-12 col-xs-12' style='border:0.1px dotted #d6d6d6;'>	
									<div class='col-sm-6 col-xs-6'>
										<div class='form-group'>
											<br>
											<input type= 'radio' id='vatA' name='report' checked> ภาษีขายทั้งหมด
											<br>
											<input type= 'radio' id='vatB' name='report'> ภาษีค่างวดเช่าซื้อ
											<br>
											<input type= 'radio' id='vatC' name='report'> ใบลดหนี้ทั้งหมด
											<br>
										</div>
									</div>
									<div class='col-sm-6 col-xs-6'>
										<div class='form-group'>
											<br>
											<input type= 'radio' id='vatD' name='report'> ภาษีเปิดการขาย
											<br>
											<input type= 'radio' id='vatE' name='report'> ภาษีเงินจองและอื่นๆ
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class='col-sm-8 col-xs-8 col-sm-offset-2'>
							<br>
							<div class='col-sm-12 col-xs-12'>	
								เรียงลำดับข้อมูล
								<div class='col-sm-12 col-xs-12' style='border:0.1px dotted #d6d6d6;'>	
									<div class='col-sm-6 col-xs-6'>
										<div class='form-group'>
											<br>
											<input type= 'radio' id='taxdt' name='orderby'> วันที่ใบกำกับ
											<br>
											<input type= 'radio' id='contno' name='orderby' checked> เลขที่สัญญา
											<br>
										</div>
									</div>
									<div class='col-sm-6 col-xs-6'>
										<div class='form-group'>
											<br>
											<input type= 'radio' id='taxno' name='orderby'> เลขที่ใบกำกับ
											<br>
											<input type= 'radio' id='inputdt' name='orderby'> วันที่บันทึก
											<br>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class='col-sm-8 col-xs-8 col-sm-offset-2'>
							<br>
							<div class='col-sm-12 col-xs-12'>	
								รูปแบบการพิมพ์
								<div class='col-sm-12 col-xs-12' style='border:0.1px dotted #d6d6d6;'>	
									<div class='col-sm-6 col-xs-6'>
										<div class='form-group'>
											<br>
											<input type= 'radio' id='hor' name='layout' checked> แนวนอน
											<br>
										</div>
									</div>
									<div class='col-sm-6 col-xs-6'>
										<div class='form-group'>
											<br>
											<input type= 'radio' id='ver' name='layout'> แนวตั้ง
											<br>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class='row' style='height:7%;'>
						<div class='col-sm-12 col-xs-12'>	
							<button id='btnt1search' class='btn btn-info btn-sm' style='width:100%;font-size:10.5pt;'><span class='glyphicon glyphicon-search'> แสดง</span></button>
						</div>
					</div>
				</div>
			</div>
		";
		
		$html.= "<script src='".base_url('public/js/SYS07/ReportOutputtax_normal.js')."'></script>";
		echo $html;
	}
	
	function search(){
		$LOCAT1		= $_REQUEST["LOCAT1"];
		$TSALE1 	= str_replace(chr(0),'',$_REQUEST["TSALE1"]);
		$FRMDATE 	= $this->Convertdate(1,$_REQUEST["FRMDATE"]);
		$TODATE 	= $this->Convertdate(1,$_REQUEST["TODATE"]);
		$report 	= $_REQUEST["report"];
		$order 		= $_REQUEST["order"];
		
		$cond = ""; $rpcond = "";
		
		if($LOCAT1 != ""){
			$cond .= " AND (LOCAT LIKE '".$LOCAT1."%')";
			$rpcond .= "  สาขา ".$LOCAT1;
		}
		
		if($TSALE1 != ""){
			$cond .= " AND (TSALE LIKE '".$TSALE1."%') ";
			$rpcond .= "  ประเภทการขาย ".$TSALE1;
		}
		
		if($report == "vatB"){ //ค่างวดเช่าซื้อ
			$cond .= " AND TAXTYP in ('D','B')";
		}else if($report == "vatC"){ //ใบลดหนี้
			$cond .= " AND TAXTYP in ('2','4','5','6','7','8','9')";
		}else if($report == "vatD"){ //เปิดการขาย
			$cond .= " AND TAXTYP in ('S')";
		}else if($report == "vatE"){ // อื่นๆ
			$cond .= " AND TAXTYP in ('R','O','1','3')";
		}

		$orderby = "";
		if($order == "TAXDT"){
			$orderby = "order by TAXDT, TAXNO";
		}else if($order == "CONTNO"){
			$orderby = "order by CONTNO"; 
		}else if($order == "TAXNO"){
			$orderby = "order by TAXNO"; 
		}else if($order == "INPUTDT"){
			$orderby = "order by INPUTDT, TAXNO"; 
		}
		
		$sql = "
				IF OBJECT_ID('tempdb..#txsale') IS NOT NULL DROP TABLE #txsale
				select *
				into #txsale
				from(
					select convert(char,TAXDT,112) as TAXDT, TAXNO, SNAM+NAME1+' '+NAME2 as CUSNAME, CONTNO, STRNO, DESCP, VATRT, 
					NETAMT, VATAMT, TOTAMT, case when FLAG = 'C' then 'ยกเลิก' else '' end as CANCEL, INPDT, FLAG, TAXTYP,
					case 
					when TSALE = 'C' then 'ขายสด'
					when TSALE = 'H' and TAXTYP = 'B' then 'ค่างวดก่อนดิว'
					when TSALE = 'H' and TAXTYP = 'D' then 'ค่างวดตามดิว'
					when TSALE = 'H' and TAXTYP = 'S' then 'เงินดาวน์'
					when TSALE = 'A' then 'ส่งเอเย่นต์'
					when TAXTYP in ('2','4','5','6','7','8','9') then 'ลดหนี้'
					when TAXTYP in ('R','O','1','3') then 'เงินจองและอื่นๆ'
					else '' end as DESCP2
					from {$this->MAuth->getdb('TAXTRAN')} 
					where TAXDT between '".$FRMDATE."' and '".$TODATE."' and TAXFLG = 'N' ".$cond."
				)txsale
		";//echo $sql; 
		$query = $this->db->query($sql);
		
		$sql = "
				select TAXDT, TAXNO, CUSNAME, CONTNO, STRNO, DESCP2, VATRT, NETAMT, VATAMT, TOTAMT, CANCEL
				from #txsale
				".$orderby."
		";//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$sql = "
				select N, Total, sumQTY, sumNETAMT, sumVATAMT, sumTOTAMT
				from(
					select 1 as N, 'ยอดรวมที่ออกใบกำกับภาษีซื้อ' as Total, 'รวมทั้งสิ้น '+convert(nvarchar(7),COUNT(TAXNO))+' รายการ' as sumQTY, 
					sum(NETAMT) as sumNETAMT, sum(VATAMT) as sumVATAMT, sum(TOTAMT) as sumTOTAMT
					from #txsale
					union all
					select 2 as N,'หัก ยอดรวมที่ออกใบลดหนี้' as Total, NULL, sum(NETAMT) as sumNETAMT, sum(VATAMT) as sumVATAMT, sum(TOTAMT) as sumTOTAMT
					from #txsale
					where TAXTYP in ('2','4','5','6','7','8','9')
					union all
					select 3 as N,'หัก ยอดรวมรายการยกเลิก' as Total, NULL, sum(NETAMT) as sumNETAMT, sum(VATAMT) as sumVATAMT, sum(TOTAMT) as sumTOTAMT
					from #txsale
					where FLAG = 'C'
					union all
					select 4 as N,'ยอดรวมภาษีซื้อที่ต้องนำส่ง' as Total, NULL, sum(NETAMT) as sumNETAMT, sum(VATAMT) as sumVATAMT, sum(TOTAMT) as sumTOTAMT
					from #txsale
					where FLAG != 'C' and TAXTYP not in ('2','4','5','6','7','8','9')
				)B
				order by N
		";//echo $sql; exit;
		$query2 = $this->db->query($sql);
		
		$head = ""; $html = ""; $head2 = "";  $report = ""; $sumreport = ""; $sumreport2 = ""; $i = 0; 
		
		$head = "<tr style='height:25px;background-color:#D3ECDC;'>
					<th style='vertical-align:middle;taxt-align:left;'>#</th>
					<th style='vertical-align:middle;taxt-align:left;'>วันที่ใบกำกับ</th>
					<th style='vertical-align:middle;taxt-align:left;'>เลขที่ใบกำกับ</th>
					<th style='vertical-align:middle;taxt-align:left;'>ชื่อ-นามสกุล</th>
					<th style='vertical-align:middle;taxt-align:left;'>เลขที่สัญญา</th>
					<th style='vertical-align:middle;taxt-align:left;'>เลขตัวถัง</th>
					<th style='vertical-align:middle;taxt-align:left;'>รายการ</th>
					<th style='vertical-align:middle;taxt-align:center;'>อัตราภาษี</th>
					<th style='vertical-align:middle;taxt-align:right;'>มูลค่าสินค้า</th>
					<th style='vertical-align:middle;taxt-align:right;'>ภาษี</th>
					<th style='vertical-align:middle;taxt-align:right;'>มูลค่ารวม</th>
					<th style='vertical-align:middle;taxt-align:center;'></th>
				</tr>
		";
		
		$head2 = "<tr>
					<th style='vertical-align:middle;taxt-align:left;'>#</th>
					<th style='vertical-align:middle;taxt-align:left;'>วันที่ใบกำกับ</th>
					<th style='vertical-align:middle;taxt-align:left;'>เลขที่ใบกำกับ</th>
					<th style='vertical-align:middle;taxt-align:left;'>ชื่อ-นามสกุล</th>
					<th style='vertical-align:middle;taxt-align:left;'>เลขที่สัญญา</th>
					<th style='vertical-align:middle;taxt-align:left;'>เลขตัวถัง</th>
					<th style='vertical-align:middle;taxt-align:left;'>รายการ</th>
					<th style='vertical-align:middle;taxt-align:center;'>อัตราภาษี</th>
					<th style='vertical-align:middle;taxt-align:right;'>มูลค่าสินค้า</th>
					<th style='vertical-align:middle;taxt-align:right;'>ภาษี</th>
					<th style='vertical-align:middle;taxt-align:right;'>มูลค่ารวม</th>
					<th style='vertical-align:middle;taxt-align:center;'></th>
				</tr>
		";
		
		$NRow = 1;
		if($query->row()){
			foreach($query->result() as $row){$i++;
				$html .= "
					<tr class='trow' seq=".$NRow.">
						<td seq=".$NRow.">".$NRow++."</td>
						<td>".$this->Convertdate(2,$row->TAXDT)."</td>
						<td>".$row->TAXNO."</td>
						<td>".$row->CUSNAME."</td>
						<td>".$row->CONTNO."</td>
						<td>".$row->STRNO."</td>
						<td>".$row->DESCP2."</td>
						<td align='center'>".number_format($row->VATRT)."</td>
						<td align='right'>".number_format($row->NETAMT,2)."</td>
						<td align='right'>".number_format($row->VATAMT,2)."</td>
						<td align='right'>".number_format($row->TOTAMT,2)."</td>
						<td align='center'>".$row->CANCEL."</td>
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
						<td style='mso-number-format:\"\@\";'>".$this->Convertdate(2,$row->TAXDT)."</td>
						<td style='mso-number-format:\"\@\";'>".$row->TAXNO."</td>
						<td style='mso-number-format:\"\@\";'>".$row->CUSNAME."</td>
						<td style='mso-number-format:\"\@\";'>".$row->CONTNO."</td>
						<td style='mso-number-format:\"\@\";'>".$row->STRNO."</td>
						<td style='mso-number-format:\"\@\";'>".$row->DESCP2."</td>
						<td style='mso-number-format:\"\#\,\#\#0\";text-align:right;'>".number_format($row->VATRT)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->NETAMT,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->VATAMT,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->TOTAMT,2)."</td>
						<td style='mso-number-format:\"\@\";taxt-align:center;'>".$row->CANCEL."</td>
					</tr>
				";	
			}
		}
		
		if($query2->row()){
			foreach($query2->result() as $row){
				$sumreport .= "
					<tr class='trow' style='height:25px;background-color:#D3ECDC;'>
						<th style='border:0px;vertical-align:middle;text-align:right;' colspan='4'></th>
						<th style='border:0px;vertical-align:middle;text-align:left;' colspan='2'>".$row->Total."</th>
						<th style='border:0px;vertical-align:middle;text-align:left;' colspan='2'>".$row->sumQTY."</th>
						<th style='border:0px;vertical-align:middle;text-align:right;'>".number_format($row->sumNETAMT,2)."</th>
						<th style='border:0px;vertical-align:middle;text-align:right;'>".number_format($row->sumVATAMT,2)."</th>
						<th style='border:0px;vertical-align:middle;text-align:right;'>".number_format($row->sumTOTAMT,2)."</th>
						<th style='border:0px;vertical-align:middle;text-align:right;'></th>
					</tr>
				";	
			}
		}
		
		if($query2->row()){
			foreach($query2->result() as $row){
				$sumreport2 .= "
					<tr class='trow'>
						<th colspan='4'></th>
						<th style='mso-number-format:\"\@\";text-align:left;' colspan='2'>".$row->Total."</th>
						<th style='mso-number-format:\"\@\";text-align:left;' colspan='2'>".$row->sumQTY."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumNETAMT,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumVATAMT,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumTOTAMT,2)."</th>
						<th></th>
					</tr>
				";	
			}
		}
		
		if($i>0){
			$html = "
				<div id='table-fixed-ReportOutputtax_normal' class='col-sm-12' style='height:100%;width:100%;overflow:auto;font-size:9pt;'>
					<table id='table-ReportOutputtax_normal' style='background-color:white;' class='col-sm-12 display table table-bordered' cellspacing='0' width='calc(100% - 1px)'>
						<thead>
						<tr style='height:40px;'>
							<th colspan='12' style='font-size:12pt;border:0px;vertical-align;middle;text-align:center;'>รายงานภาษีขาย (ยื่นปกติ)</th>
						</tr>
						<tr style='height:25px;'>
							<td colspan='12' style='border-bottom:1px solid #ddd;vertical-align;middle;text-align:center;'>จากวันที่ ".$_REQUEST["FRMDATE"]." - ".$_REQUEST["TODATE"]." ".$rpcond."   ออกรายงาน ณ วันที่ ".$this->today('today')."</td>
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
			<div id='table-fixed-ReportOutputtax_normal2' class='col-sm-12' style='height:100%;width:100%;overflow:auto;'>
				<table id='table-ReportOutputtax_normal2' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='100%'>
					<thead>
						<tr>
							<th colspan='12' style='font-size:12pt;border:0px;text-align:center;'>รายงานภาษีขาย(ยื่นปกติ)</th>
						</tr>
						<tr>
							<td colspan='12' style='border:0px;text-align:center;'>จากวันที่ ".$_REQUEST["FRMDATE"]." - ".$_REQUEST["TODATE"]." ".$rpcond."  ออกรายงาน ณ วันที่ ".$this->today('today')."</td>
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
						$_REQUEST["TSALE1"].'||'.
						$_REQUEST["FRMDATE"].'||'.
						$_REQUEST["TODATE"].'||'.
						$_REQUEST["report"].'||'.
						$_REQUEST["order"].'||'.
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
		$TSALE1 	= str_replace(chr(0),'',$tx[1]);
		$FRMDATE 	= $this->Convertdate(1,$tx[2]);
		$TODATE 	= $this->Convertdate(1,$tx[3]);
		$report 	= $tx[4];
		$order 		= $tx[5];
		$layout 	= $tx[6];
		
		$cond = ""; $rpcond = "";
		
		if($LOCAT1 != ""){
			$cond .= " AND (LOCAT LIKE '".$LOCAT1."%')";
			$rpcond .= "  สาขา ".$LOCAT1;
		}
		
		if($TSALE1 != ""){
			$cond .= " AND (TSALE LIKE '".$TSALE1."%') ";
			$rpcond .= "  ประเภทการขาย ".$TSALE1;
		}
		
		
		if($report == "vatB"){ //ค่างวดเช่าซื้อ
			$cond .= " AND TAXTYP in ('D','B')";
		}else if($report == "vatC"){ //ใบลดหนี้
			$cond .= " AND TAXTYP in ('2','4','5','6','7','8','9')";
		}else if($report == "vatD"){ //เปิดการขาย
			$cond .= " AND TAXTYP in ('S')";
		}else if($report == "vatE"){ // อื่นๆ
			$cond .= " AND TAXTYP in ('R','O','1','3')";
		}
		
		$orderby = ""; $ordername = "";
		if($order == "TAXDT"){
			$orderby = "order by TAXDT, TAXNO";
			$ordername = "วันที่ใบกำกับ";
		}else if($order == "CONTNO"){
			$orderby = "order by CONTNO"; 
			$ordername = "เลขที่สัญญา";
		}else if($order == "TAXNO"){
			$orderby = "order by TAXNO"; 
			$ordername = "เลขที่ใบกำกับ";
		}else if($order == "INPUTDT"){
			$orderby = "order by INPUTDT, TAXNO"; 
			$ordername = "วันที่บันทึก";
		}
		
		$sql = "
				IF OBJECT_ID('tempdb..#txsale') IS NOT NULL DROP TABLE #txsale
				select *
				into #txsale
				from(
					select convert(char,TAXDT,112) as TAXDT, TAXNO, SNAM+NAME1+' '+NAME2 as CUSNAME, CONTNO, STRNO, DESCP, VATRT, 
					NETAMT, VATAMT, TOTAMT, case when FLAG = 'C' then 'ยกเลิก' else '' end as CANCEL, INPDT, FLAG, TAXTYP,
					case 
					when TSALE = 'C' then 'ขายสด'
					when TSALE = 'H' and TAXTYP = 'B' then 'ค่างวดก่อนดิว'
					when TSALE = 'H' and TAXTYP = 'D' then 'ค่างวดตามดิว'
					when TSALE = 'H' and TAXTYP = 'S' then 'เงินดาวน์'
					when TSALE = 'A' then 'ส่งเอเย่นต์'
					when TAXTYP in ('2','4','5','6','7','8','9') then 'ลดหนี้'
					when TAXTYP in ('R','O','1','3') then 'เงินจองและอื่นๆ'
					else '' end as DESCP2
					from {$this->MAuth->getdb('TAXTRAN')} 
					where TAXDT between '".$FRMDATE."' and '".$TODATE."' and TAXFLG = 'N' ".$cond."
				)txsale
		";//echo $sql; 
		$query = $this->db->query($sql);
		
		$sql = "
				select TAXDT, TAXNO, CUSNAME, CONTNO, STRNO, DESCP2, VATRT, NETAMT, VATAMT, TOTAMT, CANCEL
				from #txsale
				order by CONTNO
		";//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$sql = "
				select N, Total, sumQTY, sumNETAMT, sumVATAMT, sumTOTAMT
				from(
					select 1 as N, 'ยอดรวมที่ออกใบกำกับภาษีซื้อ' as Total, 'รวมทั้งสิ้น '+convert(nvarchar(7),COUNT(TAXNO))+' รายการ' as sumQTY, 
					sum(NETAMT) as sumNETAMT, sum(VATAMT) as sumVATAMT, sum(TOTAMT) as sumTOTAMT
					from #txsale
					union all
					select 2 as N,'หัก ยอดรวมที่ออกใบลดหนี้' as Total, NULL, sum(NETAMT) as sumNETAMT, sum(VATAMT) as sumVATAMT, sum(TOTAMT) as sumTOTAMT
					from #txsale
					where TAXTYP in ('2','4','5','6','7','8','9')
					union all
					select 3 as N,'หัก ยอดรวมรายการยกเลิก' as Total, NULL, sum(NETAMT) as sumNETAMT, sum(VATAMT) as sumVATAMT, sum(TOTAMT) as sumTOTAMT
					from #txsale
					where FLAG = 'C'
					union all
					select 4 as N,'ยอดรวมภาษีซื้อที่ต้องนำส่ง' as Total, NULL, sum(NETAMT) as sumNETAMT, sum(VATAMT) as sumVATAMT, sum(TOTAMT) as sumTOTAMT
					from #txsale
					where FLAG != 'C' and TAXTYP not in ('2','4','5','6','7','8','9')
				)B
				order by N
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
					<th width='40px' 	style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>#</th>
					<th width='80px' 	style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>วันที่ใบกำกับ</th>
					<th width='80px' 	style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>เลขที่ใบกำกับ</th>
					<th width='230px' 	style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>ชื่อ-นามสกุล<br>เลขที่สัญญา</th>
					<th width='120px' 	style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>เลขตัวถัง</th>
					<th width='80px' 	style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>รายการ</th>
					<th width='70px' 	style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;text-align:center;'>อัตราภาษี%</th>
					<th width='100px' 	style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>มูลค่าสินค้า</th>
					<th width='100px' 	style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>ภาษี</th>
					<th width='100px' 	style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>มูลค่ารวม</th>
					<th width='50px'	style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;text-align:center;'></th>
				</tr>
		";
		
		$No = 1;
		if($query->row()){
			foreach($query->result() as $row){	
				$html .= "
					<tr class='trow' seq=".$No.">
						<td width='40px' 	height='40px'	align='left' style='vertical-align:top;'>".$No++."</td>
						<td width='80px' 	height='40px'	align='left' style='vertical-align:top;'>".$this->Convertdate(2,$row->TAXDT)."</td>
						<td width='80px' 	height='40px'	align='left' style='vertical-align:top;'>".$row->TAXNO."</td>
						<td width='230px' 	height='40px'	align='left' style='vertical-align:top;'>".$row->CUSNAME."<br>".$row->CONTNO."</td>
						<td width='120px' 	height='40px'	align='left' style='vertical-align:top;'>".$row->STRNO."<br></td>
						<td width='80px' 	height='40px'	align='left' style='vertical-align:top;'>".$row->DESCP2."</td>
						<td width='70px' 	height='40px'	align='center' style='vertical-align:top;'>".number_format($row->VATRT)."</td>
						<td width='100px' 	height='40px'	align='right' style='vertical-align:top;'>".number_format($row->NETAMT,2)."</td>
						<td width='100px' 	height='40px'	align='right' style='vertical-align:top;'>".number_format($row->VATAMT,2)."</td>
						<td width='100px' 	height='40px'	align='right' style='vertical-align:top;'>".number_format($row->TOTAMT,2)."</td>
						<td width='50px'	height='40px'	align='center' style='vertical-align:top;'>".$row->CANCEL."</td>
					</tr>
				";	
			}
		}
		
		if($query2->row()){
			foreach($query2->result() as $row){	
				$bordertop = ""; $borderbottom = "";
				if($row->N == "1"){
					$bordertop = "border-top:0.1px solid black;";
				}
				if($row->N == "4"){
					$borderbottom = "border-bottom:0.1px solid black;";
				}
				$html .= "
					<tr class='trow' style='background-color:#ebebeb;'>
						<th height='25px'	style='".$bordertop."".$borderbottom."border-left:0.1px solid black;text-align:left;vertical-align:middle;' colspan='3'></th>
						<th height='25px'	style='".$bordertop."".$borderbottom."text-align:left;vertical-align:middle;' colspan='2'>".$row->Total."</th>
						<th height='25px'	style='".$bordertop."".$borderbottom."text-align:left;vertical-align:middle;' colspan='2'>".$row->sumQTY."</th>
						<th height='25px'	style='".$bordertop."".$borderbottom."text-align:right;vertical-align:middle;'>".number_format($row->sumNETAMT,2)."</th>
						<th height='25px'	style='".$bordertop."".$borderbottom."text-align:right;vertical-align:middle;'>".number_format($row->sumVATAMT,2)."</th>
						<th height='25px'	style='".$bordertop."".$borderbottom."text-align:right;vertical-align:middle;'>".number_format($row->sumTOTAMT,2)."</th>
						<th height='25px'	style='".$bordertop."".$borderbottom."border-right:0.1px solid black;'></th>
					</tr>
					
				";	
			}
		}
		$body = "<table class='wf fs8' cellspacing='0'>".$html."</table>";
		
		$mpdf = new \Mpdf\Mpdf([
			'mode' => 'utf-8', 
			'format' => $layout,
			'margin_top' => ($layout == "A4-L" ? "72" : "54"), 	//default = 16
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
				.fs8 { font-size:8pt; }
				.fs9 { font-size:9pt; }
				.h30 { height:30px; }
				.bor { border-top:0.1px solid black;border-bottom:0.1px solid black;}
			</style>
		";
		
		$header = "
			<table class='wf fs9' cellspacing='0' style='border-collapse:collapse;line-height:23px;overflow:wrap;vertical-align:text-top;'>
				<tr>
					<th colspan='11' style='font-size:11pt;'>".$COMP_NM."<br>รายงานภาษีขาย(ยื่นปกติ) </th>
				</tr>
				<tr>
					<td colspan='11' style='height:35px;text-align:center;'>".$LOCATNM." จากวันที่ วันที่ ".$tx[2]." ถึงวันที่ ".$tx[3]."</td>
				</tr>
				<tr>
					<td colspan='2' align='left'>ชื่อผู้ประกอบการ</td>
					<td colspan='9' align='left'>".$COMP_NM."</td>
				</tr>
				<tr>
					<td colspan='2' align='left'>ชื่อสถานที่ประกอบการ</td>
					<td colspan='9' align='left'>".$LOCADDR1." ".$LOCADDR2."</td>
				</tr>
				<tr>
					<td colspan='2' align='left'>เลขประจำตัวผู้เสียภาษี</td>
					<td colspan='4' align='left'>".$TAXID."</td>
					<td colspan='5' align='right'>เรียงรายงาน: ".$ordername."</td>
				</tr>
				<tr>
					<td colspan='2' align='left'>วันที่พิมพ์รายงาน</td>
					<td colspan='4' align='left'>".date('d/m/').(date('Y')+543)." ".date('H:i')."</td>
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