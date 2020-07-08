<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//BEE+
class ReportInputtax extends MY_Controller {
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
							<br>รายงานภาษีซื้อ<br>
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
									รูปแบบการพิมพ์
									<div class='col-sm-12 col-xs-12' style='border:0.1px dotted #d6d6d6;'>	
										<div class='col-sm-12 col-xs-12'>
											<div class='form-group'>
												<br>
												<input type= 'radio' id='hor' name='layout' checked> แนวนอน
												<br>
												<input type= 'radio' id='ver' name='layout'> แนวตั้ง
												<br>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group'>
									ประเภทใบกำกับภาษี
									<div class='col-sm-12 col-xs-12' style='border:0.1px dotted #d6d6d6;'>	
										<div class='col-sm-12 col-xs-12'>
											<div class='form-group'>
												<br>
												<input type= 'radio' id='normal' name='taxtype' checked> ยื่นปกติ
												<br>
												<input type= 'radio' id='more' name='taxtype'> ยื่นเพิ่มเติม
												<br>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class='col-sm-8 col-xs-8 col-sm-offset-2'>
							<br>
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group'>
									ประเภทรายงาน
									<div class='col-sm-12 col-xs-12' style='border:0.1px dotted #d6d6d6;'>	
										<div class='col-sm-12 col-xs-12'>
											<div class='form-group'>
												<br>
												<input type= 'radio' id='inputtax' name='report' checked> ภาษีซื้อทั้งหมด
												<br>
												<input type= 'radio' id='creditttax' name='report'> ใบลดหนี้
												<br>
												<input type= 'radio' id='taxothr' name='report'> ภาษีซื้ออื่นๆ
												<br>
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
												<input type= 'radio' id='taxdt' name='orderby'> วันที่ใบกำกับ
												<br>
												<input type= 'radio' id='apcode' name='orderby' checked> รหัสเจ้าหนี้
												<br>
												<input type= 'radio' id='taxno' name='orderby'> เลขที่ใบกำกับ
												<br>
											</div>
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
		
		$html.= "<script src='".base_url('public/js/SYS07/ReportInputtax.js')."'></script>";
		echo $html;
	}
	
	function search(){
		$LOCAT1		= $_REQUEST["LOCAT1"];
		$APCODE1 	= str_replace(chr(0),'',$_REQUEST["APCODE1"]);
		$FRMDATE 	= $this->Convertdate(1,$_REQUEST["FRMDATE"]);
		$TODATE 	= $this->Convertdate(1,$_REQUEST["TODATE"]);
		$taxtype 	= $_REQUEST["taxtype"];
		$report 	= $_REQUEST["report"];
		$order 	= $_REQUEST["orderby"];
		
		$cond = ""; $rpcond = "";
		
		if($LOCAT1 != ""){
			$cond .= " AND (LOCAT LIKE '".$LOCAT1."%')";
		}
		
		if($APCODE1 != ""){
			$cond .= " AND (CUSCOD LIKE '".$APCODE1."%')";
		}
		
		if($report == "creditttax"){
			$cond .= " AND (TAXTYP = '1' or TAXTYP = '2')";
		}else if($report == "taxothr"){
			$cond .= " AND TAXNO in( select TAXNO from {$this->MAuth->getdb('APINVOI')} where FLAG = 'E' )";
		}

		$orderby = ""; $orderrow = "";
		if($order == "TAXDT"){
			$orderby = "order by B, C, A, TAXDT desc"; 
			$orderrow = "TAXDT, TAXNO, CUSCOD";
		}else if($order == "APCODE"){
			$orderby = "order by B, C, A, TAXDT desc"; 
			$orderrow = "CUSCOD, TAXDT, TAXNO";
		}else if($order == "TAXNO"){
			$orderby = "order by A, C, B, TAXDT desc"; 
			$orderrow = "TAXNO, TAXDT, CUSCOD";
		}
		
		$sql = "
				IF OBJECT_ID('tempdb..#txbuy') IS NOT NULL DROP TABLE #txbuy
				select *
				into #txbuy
				from(
					select TAXDT, TAXNO, CUSCOD, SNAM, NAME1, NAME2, DESCP, VATRT, NETAMT, VATAMT, TOTAMT, FLAG, CANDT, TAXTYP, STRNO  
					from {$this->MAuth->getdb('TAXBUY')} 
					where ".($taxtype == "normal" ? "TAXDT" : "INPDT")." between '".$FRMDATE."' and '".$TODATE."' 
					and (TAXFLG = '".($taxtype == "normal" ? "N" : "A")."') ".$cond."
				)txbuy
		";//echo $sql;  
		$query = $this->db->query($sql);
		
		$sql = "
				select NUM, convert(char,TAXDT,112) as TAXDT, TAXNO, CUSNAME, CUSCOD, DESCP, VATRT, NETAMT, VATAMT, TOTAMT
				from(
					select TAXNO as A, CUSCOD as B, TAXDT as C, TAXDT, ROW_NUMBER () OVER(ORDER BY ".$orderrow.") as NUM,
					TAXNO, isnull(SNAM,'')+isnull(NAME1,'')+' '+isnull(NAME2,'') as CUSNAME, CUSCOD, 
					DESCP, VATRT, NETAMT, VATAMT, TOTAMT
					from #txbuy 
					
					union all
					
					select a.TAXNO as A, a.CUSCOD as B, a.TAXDT as C, NULL as NUM,
					NULL TAXDT, b.STRNO as TAXNO, b.ENGNO as CUSNAME, NULL CUSCOD, 
					NULL DESCP, NULL VATRT, NULL NETAMT, NULL VATAMT, NULL TOTAMT
					from #txbuy a
					left join (
						select v.TAXNO, v.APCODE, i.STRNO, i.ENGNO
						from {$this->MAuth->getdb('INVTRAN')} i
						left join {$this->MAuth->getdb('INVINVO')} v on v.RECVNO = i.RECVNO
						union
						select v.TAXNO, v.APCODE, i.STRNO, i.ENGNO
						from {$this->MAuth->getdb('HINVTRAN')} i
						left join {$this->MAuth->getdb('INVINVO')} v on v.RECVNO = i.RECVNO
					)b on a.TAXNO = b.TAXNO and a.CUSCOD = b.APCODE
				)A
				".$orderby."
		";//echo $sql;  exit;
		$query = $this->db->query($sql);
		
		$sql = "
				select Total, sumQTY, sumNETAMT, sumVATAMT, sumTOTAMT
				from(
					select 1 as N, 'ยอดรวมที่ออกใบกำกับภาษีซื้อ' as Total, 'รวมทั้งสิ้น '+convert(nvarchar(7),COUNT(TAXNO))+' รายการ' as sumQTY, 
					sum(NETAMT) as sumNETAMT, sum(VATAMT) as sumVATAMT, sum(TOTAMT) as sumTOTAMT
					from #txbuy
					union all
					select 2 as N,'หัก ยอดรวมที่ออกใบลดหนี้' as Total, NULL, sum(NETAMT) as sumNETAMT, sum(VATAMT) as sumVATAMT, sum(TOTAMT) as sumTOTAMT
					from #txbuy
					where TAXTYP in ('1','2')
					union all
					select 3 as N,'หัก ยอดรวมรายการยกเลิก' as Total, NULL, sum(NETAMT) as sumNETAMT, sum(VATAMT) as sumVATAMT, sum(TOTAMT) as sumTOTAMT
					from #txbuy
					where FLAG = 'C'
					union all
					select 4 as N,'ยอดรวมภาษีซื้อที่ต้องนำส่ง' as Total, NULL, sum(NETAMT) as sumNETAMT, sum(VATAMT) as sumVATAMT, sum(TOTAMT) as sumTOTAMT
					from #txbuy
					where FLAG != 'C' and TAXTYP not in ('1','2')
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
			$LOCATNM == "";
			$LOCADDR1 == $COMP_ADR1;
			$COMP_ADR2 == $COMP_ADR2;
		}
		
		$head = ""; $html = ""; $head2 = "";  $report = ""; $sumreport = ""; $sumreport2 = ""; $i = 0; 
		
		$head = "<tr style='height:25px;background-color:#D3ECDC;'>
				<th style='display:none;'>#</th>
				<th style='vertical-align:top;text-align:center;'>ลำดับ</th>
				<th style='vertical-align:top;text-align:left;'>วันที่ใบกำกับ<br></th>
				<th style='vertical-align:top;text-align:left;'>เลขที่ใบกำกับ<br>เลขตัวถัง</th>
				<th style='vertical-align:top;text-align:left;'>ชื่อ-นามสกุล เจ้าหนี้<br>เลขเครื่อง</th>
				<th style='vertical-align:top;text-align:left;'>รหัสเจ้าหนี้</th>
				<th style='vertical-align:top;text-align:left;'>รายการ</th>
				<th style='vertical-align:top;text-align:center;'>อัตราภาษี%</th>
				<th style='vertical-align:top;text-align:right;'>มูลค่าสินค้า</th>
				<th style='vertical-align:top;text-align:right;'>ภาษี</th>
				<th style='vertical-align:top;text-align:right;'>มูลค่ารวม</th>
				</tr>
		";
		
		$NRow = 1;
		if($query->row()){
			foreach($query->result() as $row){$i++;
				$html .= "
					<tr class='trow' seq=".$NRow.">
						<td seq=".$NRow++." style='display:none;'></td>
						<td align='center'>".($row->NUM == '' ? '': $row->NUM)."</td>
						<td >".($row->TAXDT == '' ? '': $this->Convertdate(2,$row->TAXDT))."</td>
						<td>".$row->TAXNO."</td>
						<td>".$row->CUSNAME."</td>
						<td>".($row->CUSCOD == '' ? '': $row->CUSCOD)."</td>
						<td>".($row->DESCP == '' ? '': $row->DESCP)."</td>
						<td align='center'>".($row->VATRT == '' ? '': number_format($row->VATRT))."</td>
						<td align='right'>".($row->NETAMT == '' ? '': number_format($row->NETAMT,2))."</td>
						<td align='right'>".($row->VATAMT == '' ? '': number_format($row->VATAMT,2))."</td>
						<td align='right'>".($row->TOTAMT == '' ? '': number_format($row->TOTAMT,2))."</td>
					</tr>
				";	
			}
		}
		
		$head2 = "<tr>
					<th style='vertical-align:top;text-align:center;'>ลำดับ</th>
					<th style='vertical-align:top;text-align:left;'>วันที่ใบกำกับ<br></th>
					<th style='vertical-align:top;text-align:left;'>เลขที่ใบกำกับ<br>เลขตัวถัง</th>
					<th style='vertical-align:top;text-align:left;'>ชื่อ-นามสกุล เจ้าหนี้<br>เลขเครื่อง</th>
					<th style='vertical-align:top;text-align:left;'>รหัสเจ้าหนี้</th>
					<th style='vertical-align:top;text-align:left;'>รายการ</th>
					<th style='vertical-align:top;text-align:center;'>อัตราภาษี%</th>
					<th style='vertical-align:top;text-align:right;'>มูลค่าสินค้า</th>
					<th style='vertical-align:top;text-align:right;'>ภาษี</th>
					<th style='vertical-align:top;text-align:right;'>มูลค่ารวม</th>
				</tr>
		";
		
		$No = 1;
		if($query->row()){
			foreach($query->result() as $row){
				$report .= "
					<tr class='trow' >
						<td style='mso-number-format:\"\@\";text-align:center;'>".($row->NUM == '' ? '': $row->NUM)."</td>
						<td style='mso-number-format:\"\@\";text-align:left;'>".($row->TAXDT == '' ? '': $this->Convertdate(2,$row->TAXDT))."</td>
						<td style='mso-number-format:\"\@\";text-align:left;'>".$row->TAXNO."</td>
						<td style='mso-number-format:\"\@\";text-align:left;'>".$row->CUSNAME."</td>
						<td style='mso-number-format:\"\@\";text-align:left;'>".($row->CUSCOD == '' ? '': $row->CUSCOD)."</td>
						<td style='mso-number-format:\"\@\";text-align:left;'>".($row->DESCP == '' ? '': $row->DESCP)."</td>
						<td style='mso-number-format:\"\#\,\#\#0\";text-align:center;'>".($row->VATRT == '' ? '': number_format($row->VATRT))."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".($row->NETAMT == '' ? '': number_format($row->NETAMT,2))."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".($row->VATAMT == '' ? '': number_format($row->VATAMT,2))."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".($row->TOTAMT == '' ? '': number_format($row->TOTAMT,2))."</td>
					</tr>
				";	
			}
		}
		
		if($query2->row()){
			foreach($query2->result() as $row){
				$sumreport .= "
					<tr style='height:25px;background-color:#D3ECDC;'>
						<th colspan='5'></th>
						<th style='vertical-align:middle;text-align:left;'>".$row->Total."</th>
						<th style='vertical-align:middle;text-align:center;'>".$row->sumQTY."</th>
						<th style='vertical-align:middle;text-align:right;'>".number_format($row->sumNETAMT,2)."</th>
						<th style='vertical-align:middle;text-align:right;'>".number_format($row->sumVATAMT,2)."</th>
						<th style='vertical-align:middle;text-align:right;'>".number_format($row->sumTOTAMT,2)."</th>
					</tr>
				";	
			}
		}
		
		if($query2->row()){
			foreach($query2->result() as $row){
				$sumreport2 .= "
					<tr class='trow'>
						<th colspan='5'></th>
						<th style='mso-number-format:\"\@\";text-align:left;'>".$row->Total."</th>
						<th style='mso-number-format:\"\@\";text-align:center;'>".$row->sumQTY."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumNETAMT,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumVATAMT,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumTOTAMT,2)."</th>
					</tr>
				";	
			}
		}
		
		if($i>0){
			$html = "
				<div id='table-fixed-ReportInputtax' class='col-sm-12' style='height:100%;width:100%;overflow:auto;font-size:9pt;'>
					<table id='table-ReportInputtax' style='background-color:white;' class='col-sm-12 display' cellspacing='0' width='calc(100% - 1px)'>
						<thead>
						<tr style='background-color:white;'>
							<th colspan='10' id='H_ReportInputtax' style='padding:10px;'></th>
						</tr>
						<tr style='background-color:white;height:40px;'>
							<th colspan='10' style='font-size:12pt;border:0px;vertical-align;middle;text-align:center;'>รายงานภาษีซื้อ</th>
						</tr>
						<tr style='background-color:white;height:25px;'>
							<td colspan='10' style='border-bottom:1px solid #ddd;vertical-align;middle;text-align:center;'>".$LOCATNM." จากวันที่  ".$_REQUEST["FRMDATE"]." ถึงวันที่  ".$_REQUEST["TODATE"]." ออกรายงาน ณ วันที่ ".$this->today('today')."</td>
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
			<div id='table-fixed-ReportInputtax2' class='col-sm-12' style='height:100%;width:100%;overflow:auto;'>
				<table id='table-ReportInputtax2' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='100%'>
					<thead>
						<tr>
							<th colspan='10' style='font-size:12pt;border:0px;text-align:center;'>รายงานภาษีซื้อ</th>
						</tr>
						<tr>
							<td colspan='10' style='border:0px;text-align:center;'>".$LOCATNM." จากวันที่  ".$_REQUEST["FRMDATE"]." ถึงวันที่  ".$_REQUEST["TODATE"]."  ออกรายงาน ณ วันที่ ".$this->today('today')."</td>
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
						$_REQUEST["taxtype"].'||'.
						$_REQUEST["report"].'||'.
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
		$taxtype 	= $tx[4];
		$report 	= $tx[5];
		$order 		= $tx[6];
		$layout 	= $tx[7];
		
		$cond = ""; $rpcond = "";
		
		if($LOCAT1 != ""){
			$cond .= " AND (LOCAT LIKE '".$LOCAT1."%')";
		}
		
		if($APCODE1 != ""){
			$cond .= " AND (CUSCOD LIKE '".$APCODE1."%')";
		}
		
		if($report == "creditttax"){
			$cond .= " AND (TAXTYP = '1' or TAXTYP = '2')";
		}else if($report == "taxothr"){
			$cond .= " AND TAXNO in( select TAXNO from {$this->MAuth->getdb('APINVOI')} where FLAG = 'E' )";
		}

		$orderby = ""; $orderrow = ""; $ordername = "";
		if($order == "TAXDT"){
			$orderby = "order by B, C, A, TAXDT desc"; 
			$orderrow = "TAXDT, TAXNO, CUSCOD";
			$ordername = "วันที่ใบกำกับ";
		}else if($order == "APCODE"){
			$orderby = "order by B, C, A, TAXDT desc"; 
			$orderrow = "CUSCOD, TAXDT, TAXNO";
			$ordername = "เจ้าหนี้";
		}else if($order == "TAXNO"){
			$orderby = "order by A, C, B, TAXDT desc"; 
			$orderrow = "TAXNO, TAXDT, CUSCOD";
			$ordername = "เลขที่ใบกำกับ";
		}
		
		$sql = "
				IF OBJECT_ID('tempdb..#txbuy') IS NOT NULL DROP TABLE #txbuy
				select *
				into #txbuy
				from(
					select TAXDT, TAXNO, CUSCOD, SNAM, NAME1, NAME2, DESCP, VATRT, NETAMT, VATAMT, TOTAMT, FLAG, CANDT, TAXTYP, STRNO  
					from {$this->MAuth->getdb('TAXBUY')} 
					where ".($taxtype == "normal" ? "TAXDT" : "INPDT")." between '".$FRMDATE."' and '".$TODATE."' 
					and (TAXFLG = '".($taxtype == "normal" ? "N" : "A")."') ".$cond."
				)txbuy
		";//echo $sql;  
		$query = $this->db->query($sql);
		
		$sql = "
				select NUM, convert(char,TAXDT,112) as TAXDT, TAXNO, CUSNAME, CUSCOD, DESCP, VATRT, NETAMT, VATAMT, TOTAMT
				from(
					select TAXNO as A, CUSCOD as B, TAXDT as C, TAXDT, ROW_NUMBER () OVER(ORDER BY ".$orderrow.") as NUM,
					TAXNO, isnull(SNAM,'')+isnull(NAME1,'')+' '+isnull(NAME2,'') as CUSNAME, CUSCOD, 
					DESCP, VATRT, NETAMT, VATAMT, TOTAMT
					from #txbuy 
					
					union all
					
					select a.TAXNO as A, a.CUSCOD as B, a.TAXDT as C, NULL as NUM,
					NULL TAXDT, b.STRNO as TAXNO, b.ENGNO as CUSNAME, NULL CUSCOD, 
					NULL DESCP, NULL VATRT, NULL NETAMT, NULL VATAMT, NULL TOTAMT
					from #txbuy a
					left join (
						select v.TAXNO, v.APCODE, i.STRNO, i.ENGNO
						from {$this->MAuth->getdb('INVTRAN')} i
						left join {$this->MAuth->getdb('INVINVO')} v on v.RECVNO = i.RECVNO
						union
						select v.TAXNO, v.APCODE, i.STRNO, i.ENGNO
						from {$this->MAuth->getdb('HINVTRAN')} i
						left join {$this->MAuth->getdb('INVINVO')} v on v.RECVNO = i.RECVNO
					)b on a.TAXNO = b.TAXNO and a.CUSCOD = b.APCODE
				)A
				".$orderby."
		";//echo $sql;  exit;
		$query = $this->db->query($sql);
		
		$sql = "
				select N, Total, sumQTY, sumNETAMT, sumVATAMT, sumTOTAMT
				from(
					select 1 as N, 'ยอดรวมที่ออกใบกำกับภาษีซื้อ' as Total, 'รวมทั้งสิ้น '+convert(nvarchar(7),COUNT(TAXNO))+' รายการ' as sumQTY, 
					sum(NETAMT) as sumNETAMT, sum(VATAMT) as sumVATAMT, sum(TOTAMT) as sumTOTAMT
					from #txbuy
					union all
					select 2 as N,'หัก ยอดรวมที่ออกใบลดหนี้' as Total, NULL, sum(NETAMT) as sumNETAMT, sum(VATAMT) as sumVATAMT, sum(TOTAMT) as sumTOTAMT
					from #txbuy
					where TAXTYP in ('1','2')
					union all
					select 3 as N,'หัก ยอดรวมรายการยกเลิก' as Total, NULL, sum(NETAMT) as sumNETAMT, sum(VATAMT) as sumVATAMT, sum(TOTAMT) as sumTOTAMT
					from #txbuy
					where FLAG = 'C'
					union all
					select 4 as N,'ยอดรวมภาษีซื้อที่ต้องนำส่ง' as Total, NULL, sum(NETAMT) as sumNETAMT, sum(VATAMT) as sumVATAMT, sum(TOTAMT) as sumTOTAMT
					from #txbuy
					where FLAG != 'C' and TAXTYP not in ('1','2')
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
			$LOCATNM = "";
			$LOCADDR1 = $COMP_ADR1;
			$LOCADDR2 = $COMP_ADR2;
		}
		
		$head = ""; $html = ""; $i=0; 
		
		$head = "
				<tr>
					<th width='50px' 	style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;text-align:center;'>ลำดับ</th>
					<th width='80px' 	style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>วันที่ใบกำกับ<br></th>
					<th width='140px' 	style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>เลขที่ใบกำกับ<br>เลขตัวถัง</th>
					<th width='200px' 	style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>ชื่อ-นามสกุล เจ้าหนี้<br>เลขเครื่อง</th>
					<th width='70px' 	style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>รหัสเจ้าหนี้</th>
					<th width='140px' 	style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;text-align:center;'>รายการ</th>
					<th width='70px' 	style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;text-align:center;'>อัตราภาษี%</th>
					<th width='110px' 	style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>มูลค่าสินค้า</th>
					<th width='110px' 	style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>ภาษี</th>
					<th width='110px' 	style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>มูลค่ารวม</th>
				</tr>
		";
		
		$No = 1;
		if($query->row()){
			foreach($query->result() as $row){	
				$html .= "
					<tr class='trow' seq=".$No.">
						<td width='50px' 	height='25px'	align='center'>".($row->NUM == '' ? '': $row->NUM)."</td>
						<td width='80px' 	height='25px'	align='left'>".($row->TAXDT == '' ? '': $this->Convertdate(2,$row->TAXDT))."</td>
						<td width='140px' 	height='25px'	align='left'>".$row->TAXNO."</td>
						<td width='200px' 	height='25px'	align='left'>".$row->CUSNAME."</td>
						<td width='70px' 	height='25px'	align='center'>".($row->CUSCOD == '' ? '': $row->CUSCOD)."</td>
						<td width='140px' 	height='25px'	align='left'>".($row->DESCP == '' ? '': $row->DESCP)."</td>
						<td width='70px' 	height='25px'	align='center'>".($row->VATRT == '' ? '': number_format($row->VATRT))."</td>
						<td width='110px' 	height='25px'	align='right'>".($row->NETAMT == '' ? '': number_format($row->NETAMT,2))."</td>
						<td width='110px' 	height='25px'	align='right'>".($row->VATAMT == '' ? '': number_format($row->VATAMT,2))."</td>
						<td width='110px' 	height='25px'	align='right'>".($row->TOTAMT == '' ? '': number_format($row->TOTAMT,2))."</td>
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
						<th height='25px'	style='".$bordertop."".$borderbottom."border-right:0.1px solid black;text-align:right;vertical-align:middle;'>".number_format($row->sumTOTAMT,2)."</th>
					</tr>
					
				";	
			}
		}
		$body = "<table class='fs9' cellspacing='0'>".$html."</table>";
		
		$mpdf = new \Mpdf\Mpdf([
			'mode' => 'utf-8', 
			'format' => $layout,
			'margin_top' => ($layout == "A4-L" ? "70" : "52"), 	//default = 16
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
				.fs9 { font-size:9pt; }
				.h30 { height:30px; }
				.bor { border-top:0.1px solid black;border-bottom:0.1px solid black;}
			</style>
		";
		
		$header = "
			<table class='wf fs9' cellspacing='0' style='border-collapse:collapse;line-height:23px;overflow:wrap;vertical-align:text-top;'>
				<tr>
					<th colspan='10' style='font-size:11pt;'>".$COMP_NM."<br>รายงานภาษีซื้อ</th>
				</tr>
				<tr>
					<td colspan='10' style='height:35px;text-align:center;'>".$LOCATNM." จากวันที่ วันที่ ".$tx[2]." ถึงวันที่ ".$tx[3]."</td>
				</tr>
				<tr>
					<td colspan='2' align='left'>ชื่อผู้ประกอบการ</td>
					<td colspan='8' align='left'>".$COMP_NM."</td>
				</tr>
				<tr>
					<td colspan='2' align='left'>ชื่อสถานที่ประกอบการ</td>
					<td colspan='8' align='left'>".$LOCADDR1." ".$LOCADDR2."</td>
				</tr>
				<tr>
					<td colspan='2' align='left'>เลขประจำตัวผู้เสียภาษี</td>
					<td colspan='4' align='left'>".$TAXID."</td>
					<td colspan='4' align='right'>เรียงรายงาน: ".$ordername."</td>
				</tr>
				<tr>
					<td colspan='2' align='left'>วันที่พิมพ์รายงาน</td>
					<td colspan='4' align='left'>".date('d/m/').(date('Y')+543)." ".date('H:i')."</td>
					<td colspan='4' align='right'>หน้าที่ : {PAGENO} / {nb} &emsp;&emsp;</td>
					
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