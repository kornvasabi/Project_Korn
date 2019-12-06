<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//BEE+
class ReportARsummaryinstalment extends MY_Controller {
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
							<br>รายงานสรุปลูกหนี้เช่าซื้อตามสาขา<br>
						</div>
						<div class='col-sm-4 col-xs-4 col-sm-offset-4'>
							<br>
							<div class='col-sm-12 col-xs-12'>	
								<div class='form-group'>
									สาขา
									<select id='LOCAT1' class='form-control input-sm' data-placeholder='สาขา'></select>
								</div>
							</div>
							<div class='col-sm-12 col-xs-12'>	
								<div class='form-group'>
									ลูกหนี้ ณ วันที่
									<input type='text' id='ARDATE' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' placeholder='ลูกหนี้ ณ วันที่' value='".$this->today('today')."' style='font-size:10.5pt'>
								</div>
							</div>
							<div class='col-sm-12 col-xs-12'>	
								<div class='form-group' >
									BillColl
									<select id='BILLCOL1' class='form-control input-sm' data-placeholder='BillColl'></select>
								</div>
							</div>	
							<div class='col-sm-12 col-xs-12'>	
								<div class='form-group' >
									กลุ่มสินค้า
									<select id='GCOCE1' class='form-control input-sm' data-placeholder='กลุ่มสินค้า'></select>
								</div>
							</div>	
						</div>
						<div class='col-sm-4 col-xs-4 col-sm-offset-4'><br>	
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
		
		$html.= "<script src='".base_url('public/js/SYS05/ReportARsummaryinstalment.js')."'></script>";
		echo $html;
	}
	
	function search(){
		$LOCAT1		= $_REQUEST["LOCAT1"];
		$BILLCOL1 	= str_replace(chr(0),'',$_REQUEST["BILLCOL1"]);
		$GCOCE1 	= str_replace(chr(0),'',$_REQUEST["GCOCE1"]);
		$ARDATE 	= $_REQUEST["ARDATE"];
		$ARDATES 	= $this->Convertdate(1,$_REQUEST["ARDATE"]);
		
		
		$cond = ""; $rpcond = "";
		
		if($LOCAT1 != ""){
			$cond .= " AND (A.LOCAT LIKE '%".$LOCAT1."%')";
			$rpcond .= "  สาขา ".$LOCAT1;
		}
		
		if($BILLCOL1 != ""){
			$cond .= " AND (A.BILLCOLL LIKE '%".$BILLCOL1."%' )";
			$rpcond .= "  พนักงานเก็บเงิน ".$BILLCOL1;
		}else{
			$cond .= " AND (A.BILLCOLL LIKE '%%' OR A.BILLCOLL IS NULL)";
		}
		
		if($GCOCE1 != ""){
			$cond .= " AND (D.GCODE LIKE '%".$GCOCE1."%' )";
			$rpcond .= "  กลุ่มสินค้า ".$GCOCE1;
		}else{
			$cond .= " AND (D.GCODE LIKE '%%' OR D.GCODE IS NULL)";
		}
		
		$sql = "
				IF OBJECT_ID('tempdb..#main') IS NOT NULL DROP TABLE #main
				select *
				into #main
				from(
					select LOCAT, CONTNO, BILLCOLL, NPRICE, VATPRC, TOTPRC, (d.snetp+d.snetp1) as NPAY, totprc-(totpres+snetp1+snetp) as NARBALANCE
					from ( 
						select DISTINCT A.LOCAT, A.CONTNO, A.BILLCOLL, A.SDATE, A.NPRICE, A.VATPRC, A.TOTPRC, A.TOTPRES,
						case when f.snetp is null then 0 else f.snetp end as snetp,
						case when f.snetp1 is null then 0 else f.snetp1 end as snetp1
						from {$this->MAuth->getdb('ARMAST')} A
						left outer join (
							select strno, gcode, contno from {$this->MAuth->getdb('INVTRAN')}  
							union 
							select strno, gcode, contno from {$this->MAuth->getdb('HINVTRAN')}
						) as D on a.strno=d.strno and a.contno=d.contno   
						left outer join (
							select contno, locatpay,
							sum(CASE WHEN (PAYDT <='".$ARDATES."' AND ((PAYFOR = '006') OR (PAYFOR = '007'))  AND FLAG <>'C' ) THEN  PAYAMT ELSE 0 END) AS SNETP,
							sum(CASE WHEN (PAYDT <='".$ARDATES."' AND (PAYFOR = '002')  AND FLAG <>'C' ) THEN  PAYAMT ELSE 0 END) AS SNETP1,
							sum(CASE WHEN (PAYDT <='".$ARDATES."' AND ((PAYFOR = '006') OR (PAYFOR = '007')) AND FLAG <>'C' ) THEN  PAYAMT_V ELSE 0 END) AS VPAY,
							sum(CASE WHEN (PAYDT <='".$ARDATES."' AND (PAYFOR = '002')  AND FLAG <>'C' ) THEN  PAYAMT_V ELSE 0 END) AS VDN  
							from {$this->MAuth->getdb('CHQTRAN')} 
							group by contno,locatpay
						) as f on a.contno = f.contno and a.locat = f.locatpay 
						where (A.SDATE <= '".$ARDATES."') AND A.TOTPRC > 0 AND (A.TOTPRC > A.SMPAY OR (A.TOTPRC = A.SMPAY AND A.LPAYD > '".$ARDATES."')) 
						".$cond."

						union 

						select DISTINCT A.LOCAT, A.CONTNO, A.BILLCOLL, A.SDATE, A.NPRICE, A.VATPRC, A.TOTPRC, A.TOTPRES,
						case when f.snetp is null then 0 else f.snetp end as snetp,
						case when f.snetp1 is null then 0 else f.snetp1 end as snetp1
						from {$this->MAuth->getdb('HARMAST')} A 
						left outer join {$this->MAuth->getdb('chgar_view')} B on a.contno = b.contno and a.locat = b.locat  
						left outer join (
							select strno, gcode, type, model, stat, contno from {$this->MAuth->getdb('INVTRAN')}  
							union 
							select strno, gcode, type, model, stat, contno from {$this->MAuth->getdb('HINVTRAN')}
						) as D on a.strno=d.strno and a.contno=d.contno  
						left outer join (
							select contno, locatpay,
							sum(CASE WHEN (PAYDT <='".$ARDATES."' AND ((PAYFOR = '006') OR (PAYFOR = '007')) AND FLAG <>'C' ) THEN  PAYAMT ELSE 0 END) AS SNETP,
							sum(CASE WHEN (PAYDT <='".$ARDATES."' AND (PAYFOR = '002')  AND FLAG <>'C') THEN  PAYAMT ELSE 0 END) AS SNETP1,
							sum(CASE WHEN (PAYDT <='".$ARDATES."' AND ((PAYFOR = '006') OR (PAYFOR = '007')) AND FLAG <>'C' ) THEN  PAYAMT_V ELSE 0 END) AS VPAY,
							sum(CASE WHEN (PAYDT <='".$ARDATES."' AND (PAYFOR = '002') AND FLAG <>'C' ) THEN  PAYAMT_V ELSE 0 END) AS VDN  
							from {$this->MAuth->getdb('CHQTRAN')} 
							group by contno,locatpay
						) as f on a.contno = f.contno and a.locat = f.locatpay 
						where (A.SDATE <= '".$ARDATES."') AND A.TOTPRC > 0 AND (A.TOTPRC > A.SMPAY OR (A.TOTPRC = A.SMPAY AND A.LPAYD > '".$ARDATES."')) AND b.date1 > '".$ARDATES."'   
						".$cond."
					 ) as d 
				)main
		";//echo $sql; 
		$query = $this->db->query($sql);
		
		$sql = "
				select LOCAT, BILLCOLL, NAME, count(CONTNO) as CONTNO, sum(NPRICE) as NPRICE, sum(VATPRC) as VATPRC, 
				sum(TOTPRC) as TOTPRC, sum(NPAY) as NPAY, sum(NARBALANCE) as NARBALANCE 
				from #main a
				left join OFFICER b on a.BILLCOLL = b.CODE
				group by LOCAT, BILLCOLL, NAME
				order by LOCAT, BILLCOLL
		";//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$sql = "
				select 'รวมทั้งหมด' as Total, sum(CONTNO) as sumCONTNO, sum(NPRICE) as sumNPRICE, sum(VATPRC) as sumVATPRC, sum(TOTPRC) as sumTOTPRC, 
				sum(NPAY) as sumNPAY, sum(NARBALANCE) as sumNARBALANCE
				from(
					select LOCAT, BILLCOLL, NAME, count(CONTNO) as CONTNO, sum(NPRICE) as NPRICE, sum(VATPRC) as VATPRC, 
					sum(TOTPRC) as TOTPRC, sum(NPAY) as NPAY, sum(NARBALANCE) as NARBALANCE 
					from #main a
					left join OFFICER b on a.BILLCOLL = b.CODE
					group by LOCAT, BILLCOLL, NAME
				)A
		";
		//echo $sql; exit;
		$query2 = $this->db->query($sql);
		
		
		$head = ""; $html = ""; $head2 = "";  $report = ""; $sumreport = ""; $sumreport2 = ""; $i = 0; 
		
		$head = "<tr>
				<th style='display:none;'>#</th>
				<th style='vertical-align:top;'>สาขา</th>
				<th style='vertical-align:top;'>รหัสBillcoll</th>
				<th style='vertical-align:top;'>ชื่อ - นามสกุล</th>
				<th style='vertical-align:top;text-align:right;'>จำนวนสัญญา</th> 
				<th style='vertical-align:top;text-align:right;'>ราคาขายก่อน Vat</th>
				<th style='vertical-align:top;text-align:right;'>Vatราคาขาย</th>
				<th style='vertical-align:top;text-align:right;'>ราคาขายรวมVat</th>
				<th style='vertical-align:top;text-align:right;'>ยอดชำระเงิน</th>
				<th style='vertical-align:top;text-align:right;'>ลูกหนี้คงเหลือ</th>
				</tr>
		";
		
		$head2 = "<tr>
					<th style='vertical-align:middle;'>#</th>
					<th style='vertical-align:top;'>สาขา</th>
					<th style='vertical-align:top;'>รหัสBillcoll</th>
					<th style='vertical-align:top;'>ชื่อ - นามสกุล</th>
					<th style='vertical-align:top;text-align:right;'>จำนวนสัญญา</th> 
					<th style='vertical-align:top;text-align:right;'>ราคาขายก่อน Vat</th>
					<th style='vertical-align:top;text-align:right;'>Vatราคาขาย</th>
					<th style='vertical-align:top;text-align:right;'>ราคาขายรวมVat</th>
					<th style='vertical-align:top;text-align:right;'>ยอดชำระเงิน</th>
					<th style='vertical-align:top;text-align:right;'>ลูกหนี้คงเหลือ</th>
				</tr>
		";
		
		$NRow = 1;
		if($query->row()){
			foreach($query->result() as $row){$i++;
				$html .= "
					<tr class='trow' seq=".$NRow.">
						<td seq=".$NRow++." style='display:none;'></td>
						<td>".$row->LOCAT."</td>
						<td>".$row->BILLCOLL."</td>
						<td>".$row->NAME."</td>
						<td align='right'>".number_format($row->CONTNO)."</td>
						<td align='right'>".number_format($row->NPRICE,2)."</td>
						<td align='right'>".number_format($row->VATPRC,2)."</td>
						<td align='right'>".number_format($row->TOTPRC,2)."</td>
						<td align='right'>".number_format($row->NPAY,2)."</td>
						<td align='right'>".number_format($row->NARBALANCE,2)."</td>
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
						<td style='mso-number-format:\"\@\";'>".$row->BILLCOLL."</td>
						<td style='mso-number-format:\"\@\";'>".$row->NAME."</td>
						<td style='mso-number-format:\"\#\,\#\#0\";text-align:right;'>".number_format($row->CONTNO)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->NPRICE,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->VATPRC,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->TOTPRC,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->NPAY,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->NARBALANCE,2)."</td>
					</tr>
				";	
			}
		}
		
		if($query2->row()){
			foreach($query2->result() as $row){
				$sumreport = "
					<tr>
						<th colspan='3' style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;text-align:center;'>".$row->Total."</th>
						<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;text-align:right;'>".number_format($row->sumCONTNO)."</th>
						<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;text-align:right;'>".number_format($row->sumNPRICE,2)."</th>
						<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;text-align:right;'>".number_format($row->sumVATPRC,2)."</th>
						<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;text-align:right;'>".number_format($row->sumTOTPRC,2)."</th>
						<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;text-align:right;'>".number_format($row->sumNPAY,2)."</th>
						<th style='border:0px;text-align:right;'>".number_format($row->sumNARBALANCE,2)."</th>
					</tr>
				";	
			}
		}
		
		if($query2->row()){
			foreach($query2->result() as $row){
				$sumreport2 = "
					<tr class='trow'>
						<th style='mso-number-format:\"\@\";text-align:center;' colspan='4'>".$row->Total."</th>
						<th style='mso-number-format:\"\#\,\#\#0\";text-align:right;'>".number_format($row->sumCONTNO)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumNPRICE,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumVATPRC,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumTOTPRC,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumNPAY,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumNARBALANCE,2)."</th>
					</tr>
				";	
			}
		}
		
		if($i>0){
			$html = "
				<div id='table-fixed-ReportARsummaryinstalment' class='col-sm-12' style='height:100%;width:100%;overflow:auto;font-size:8pt;'>
					<table id='table-ReportARsummaryinstalment' style='background-color:white;' class='col-sm-12 display table table-bordered' cellspacing='0' width='calc(100% - 1px)'>
						<thead>
						<tr>
							<th colspan='9' style='font-size:12pt;border:0px;text-align:center;'>รายงานสรุปลูกหนี้เช่าซื้อตามสาขา</th>
						</tr>
						<tr>
							<td colspan='9' style='border-bottom:1px solid #ddd;text-align:center;'>ลูกหนี้ ณ วันที่ ".$ARDATE." ".$rpcond."  ออกรายงาน ณ วันที่ ".$this->today('today')."</td>
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
			<div id='table-fixed-ReportARsummaryinstalment2' class='col-sm-12' style='height:100%;width:100%;overflow:auto;'>
				<table id='table-ReportARsummaryinstalment2' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='100%'>
					<thead>
						<tr>
							<th colspan='10' style='font-size:12pt;border:0px;text-align:center;'>รายงานสรุปลูกหนี้เช่าซื้อตามสาขา</th>
						</tr>
						<tr>
							<td colspan='10' style='border:0px;text-align:center;'>ลูกหนี้ ณ วันที่ ".$ARDATE." ".$rpcond." ออกรายงาน ณ วันที่ ".$this->today('today')."</td>
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
		$data[] = 	urlencode($_REQUEST["LOCAT1"].'||'.$_REQUEST["BILLCOL1"].'||'.$_REQUEST["GCOCE1"].'||'.$_REQUEST["ARDATE"].'||'.$_REQUEST["layout"]);
		echo json_encode($this->generateData($data,"encode"));
	}
	
	function pdf(){
		$data 	= array();
		$data[] = $_GET["condpdf"];
		$arrs 	= $this->generateData($data,"decode");
		$arrs[0]= urldecode($arrs[0]);
		$tx 	= explode("||",$arrs[0]);
		$LOCAT1 	= $tx[0];
		$BILLCOL1 	= str_replace(chr(0),'',$tx[1]);
		$GCOCE1 	= str_replace(chr(0),'',$tx[2]);
		$ARDATE 	= $tx[3];
		$ARDATES 	= $this->Convertdate(1,$tx[3]);
		$layout 	= $tx[4];
		
		$cond = ""; $rpcond = "";
		
		if($LOCAT1 != ""){
			$cond .= " AND (A.LOCAT LIKE '%".$LOCAT1."%')";
			$rpcond .= "  สาขา ".$LOCAT1;
		}
		
		if($BILLCOL1 != ""){
			$cond .= " AND (A.BILLCOLL LIKE '%".$BILLCOL1."%' )";
			$rpcond .= "  พนักงานเก็บเงิน ".$BILLCOL1;
		}else{
			$cond .= " AND (A.BILLCOLL LIKE '%%' OR A.BILLCOLL IS NULL)";
		}
		
		if($GCOCE1 != ""){
			$cond .= " AND (D.GCODE LIKE '%".$GCOCE1."%' )";
			$rpcond .= "  กลุ่มสินค้า ".$GCOCE1;
		}else{
			$cond .= " AND (D.GCODE LIKE '%%' OR D.GCODE IS NULL)";
		}
		
		$sql = "
				IF OBJECT_ID('tempdb..#main') IS NOT NULL DROP TABLE #main
				select *
				into #main
				from(
					select LOCAT, CONTNO, BILLCOLL, NPRICE, VATPRC, TOTPRC, (d.snetp+d.snetp1) as NPAY, totprc-(totpres+snetp1+snetp) as NARBALANCE
					from ( 
						select DISTINCT A.LOCAT, A.CONTNO, A.BILLCOLL, A.SDATE, A.NPRICE, A.VATPRC, A.TOTPRC, A.TOTPRES,
						case when f.snetp is null then 0 else f.snetp end as snetp,
						case when f.snetp1 is null then 0 else f.snetp1 end as snetp1
						from   {$this->MAuth->getdb('ARMAST')} A
						left outer join (
							select strno, gcode, contno from {$this->MAuth->getdb('INVTRAN')}  
							union 
							select strno, gcode, contno from {$this->MAuth->getdb('HINVTRAN')}
						) as D on a.strno=d.strno and a.contno=d.contno   
						left outer join (
							select contno, locatpay,
							sum(CASE WHEN (PAYDT <='".$ARDATES."' AND ((PAYFOR = '006') OR (PAYFOR = '007'))  AND FLAG <>'C' ) THEN  PAYAMT ELSE 0 END) AS SNETP,
							sum(CASE WHEN (PAYDT <='".$ARDATES."' AND (PAYFOR = '002')  AND FLAG <>'C' ) THEN  PAYAMT ELSE 0 END) AS SNETP1,
							sum(CASE WHEN (PAYDT <='".$ARDATES."' AND ((PAYFOR = '006') OR (PAYFOR = '007')) AND FLAG <>'C' ) THEN  PAYAMT_V ELSE 0 END) AS VPAY,
							sum(CASE WHEN (PAYDT <='".$ARDATES."' AND (PAYFOR = '002')  AND FLAG <>'C' ) THEN  PAYAMT_V ELSE 0 END) AS VDN  
							from {$this->MAuth->getdb('CHQTRAN')} 
							group by contno,locatpay
						) as f on a.contno = f.contno and a.locat = f.locatpay 
						where (A.SDATE <= '".$ARDATES."') AND A.TOTPRC > 0 AND (A.TOTPRC > A.SMPAY OR (A.TOTPRC = A.SMPAY AND A.LPAYD > '".$ARDATES."')) 
						".$cond."

						union 

						select DISTINCT A.LOCAT, A.CONTNO, A.BILLCOLL, A.SDATE, A.NPRICE, A.VATPRC, A.TOTPRC, A.TOTPRES,
						case when f.snetp is null then 0 else f.snetp end as snetp,
						case when f.snetp1 is null then 0 else f.snetp1 end as snetp1
						from {$this->MAuth->getdb('HARMAST')} A 
						left outer join {$this->MAuth->getdb('chgar_view')} B on a.contno = b.contno and a.locat = b.locat  
						left outer join (
							select strno, gcode, type, model, stat, contno from {$this->MAuth->getdb('INVTRAN')}  
							union 
							select strno, gcode, type, model, stat, contno from {$this->MAuth->getdb('HINVTRAN')}
						) as D on a.strno=d.strno and a.contno=d.contno  
						left outer join (
							select contno, locatpay,
							sum(CASE WHEN (PAYDT <='".$ARDATES."' AND ((PAYFOR = '006') OR (PAYFOR = '007')) AND FLAG <>'C' ) THEN  PAYAMT ELSE 0 END) AS SNETP,
							sum(CASE WHEN (PAYDT <='".$ARDATES."' AND (PAYFOR = '002')  AND FLAG <>'C') THEN  PAYAMT ELSE 0 END) AS SNETP1,
							sum(CASE WHEN (PAYDT <='".$ARDATES."' AND ((PAYFOR = '006') OR (PAYFOR = '007')) AND FLAG <>'C' ) THEN  PAYAMT_V ELSE 0 END) AS VPAY,
							sum(CASE WHEN (PAYDT <='".$ARDATES."' AND (PAYFOR = '002') AND FLAG <>'C' ) THEN  PAYAMT_V ELSE 0 END) AS VDN  
							from {$this->MAuth->getdb('CHQTRAN')} 
							group by contno,locatpay
						) as f on a.contno = f.contno and a.locat = f.locatpay 
						where (A.SDATE <= '".$ARDATES."') AND A.TOTPRC > 0 AND (A.TOTPRC > A.SMPAY OR (A.TOTPRC = A.SMPAY AND A.LPAYD > '".$ARDATES."')) AND b.date1 > '".$ARDATES."'   
						".$cond."
					 ) as d 
				)main
		";//echo $sql; 
		$query = $this->db->query($sql);
		
		$sql = "
				select LOCAT, BILLCOLL, NAME, count(CONTNO) as CONTNO, sum(NPRICE) as NPRICE, sum(VATPRC) as VATPRC, 
				sum(TOTPRC) as TOTPRC, sum(NPAY) as NPAY, sum(NARBALANCE) as NARBALANCE 
				from #main a
				left join OFFICER b on a.BILLCOLL = b.CODE
				group by LOCAT, BILLCOLL, NAME
				order by LOCAT, BILLCOLL
		";//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$sql = "
				select 'รวมทั้งหมด' as Total, sum(CONTNO) as sumCONTNO, sum(NPRICE) as sumNPRICE, sum(VATPRC) as sumVATPRC, sum(TOTPRC) as sumTOTPRC, 
				sum(NPAY) as sumNPAY, sum(NARBALANCE) as sumNARBALANCE
				from(
					select LOCAT, BILLCOLL, NAME, count(CONTNO) as CONTNO, sum(NPRICE) as NPRICE, sum(VATPRC) as VATPRC, 
					sum(TOTPRC) as TOTPRC, sum(NPAY) as NPAY, sum(NARBALANCE) as NARBALANCE 
					from #main a
					left join OFFICER b on a.BILLCOLL = b.CODE
					group by LOCAT, BILLCOLL, NAME
				)A
		";
		//echo $sql; exit;
		$query2 = $this->db->query($sql);
		
		$head = ""; $html = ""; $i=0; 

		$head = "
				<tr>
					<th style='border-bottom:0.1px solid black;text-align:left;'>#</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>สาขา</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>รหัสBillcoll</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>ชื่อ - นามสกุล</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>จำนวนสัญญา</th> 
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>ราคาขายก่อน Vat</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>Vatราคาขาย</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>ราคาขายรวมVat</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>ยอดชำระเงิน</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>ลูกหนี้คงเหลือ</th>
				</tr>
		";
		
		$No = 1;
		if($query->row()){
			foreach($query->result() as $row){	
				$html .= "
					<tr class='trow' seq=".$No.">
						<td style='width:25px;'>".$No++."</td>
						<td style='width:50px;'>".$row->LOCAT."</td>
						<td style='width:70px;'>".$row->BILLCOLL."</td>
						<td style='width:200px;'>".$row->NAME."</td>
						<td style='width:80px;' align='right'>".number_format($row->CONTNO)."</td>
						<td style='width:80px;' align='right'>".number_format($row->NPRICE,2)."</td>
						<td style='width:80px;' align='right'>".number_format($row->VATPRC,2)."</td>
						<td style='width:80px;' align='right'>".number_format($row->TOTPRC,2)."</td>
						<td style='width:80px;' align='right'>".number_format($row->NPAY,2)."</td>
						<td style='width:80px;' align='right'>".number_format($row->NARBALANCE,2)."</td>
					</tr>
				";	
			}
		}
		
		if($query2->row()){
			foreach($query2->result() as $row){	
				$html .= "
					<tr class='trow bor' style='background-color:#ebebeb;'>
						<td colspan='4' style='text-align:center;vertical-align:middle;'>".$row->Total."</td>
						<td align='right'>".number_format($row->sumCONTNO)."</td>
						<td align='right'>".number_format($row->sumNPRICE,2)."</td>
						<td align='right'>".number_format($row->sumVATPRC,2)."</td>
						<td align='right'>".number_format($row->sumTOTPRC,2)."</td>
						<td align='right'>".number_format($row->sumNPAY,2)."</td>
						<td align='right'>".number_format($row->sumNARBALANCE,2)."</td>
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
						<th colspan='10' style='font-size:10pt;'>รายงานสรุปลูกหนี้เช่าซื้อตามสาขา</th>
					</tr>
					<tr>
						<td colspan='10' style='font-size:8pt;height:35px;border-bottom:0.1px solid black;text-align:center;'>ลูกหนี้ ณ วันที่ ".$ARDATE." ".$rpcond."</td>
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