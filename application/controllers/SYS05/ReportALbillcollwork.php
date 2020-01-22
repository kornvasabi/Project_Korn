<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//BEE+
class ReportALbillcollwork extends MY_Controller {
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
							<br>รายงานผลงานพนักงานเก็บเงิน<br>
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
									รหัสพนักงาน
									<select id='BILLCOLL1' class='form-control input-sm' data-placeholder='รหัสพนักงาน'></select>
								</div>
							</div>
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group'>
									วัดผลงานจากวันที่
									<input type='text' id='FRMDATE' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' placeholder='วัดผลงานจากวันที่' value='".$this->today('today')."' style='font-size:10.5pt'>
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
							<div class='col-sm-12 col-xs-12'>	
								<div class='form-group'>
									ข้อมูลรายงาน
									<div class='col-sm-12 col-xs-12' style='border:0.1px dotted #d6d6d6;'>	
										<div class='col-sm-6 col-xs-6'>
											<div class='form-group'><br>
												<input type= 'radio' id='y_no' name='sumy' checked> ไม่รวมรถยึด
											</div>
										</div>
										<div class='col-sm-6 col-xs-6'>
											<div class='form-group'><br>
												<input type= 'radio' id='y_yes' name='sumy'> รวมรถยึด
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
												<input type= 'radio' id='billcoll' name='orderby' checked> รหัสพนักงาน
												<br><br>
												<input type= 'radio' id='locat' name='orderby'> รหัสสาขา
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
		
		$html.= "<script src='".base_url('public/js/SYS05/ReportALbillcollwork.js')."'></script>";
		echo $html;
	}
	
	function search(){
		$LOCAT1		= $_REQUEST["LOCAT1"];
		$BILLCOLL1 	= str_replace(chr(0),'',$_REQUEST["BILLCOLL1"]);
		$FRMDATE 	= $this->Convertdate(1,$_REQUEST["FRMDATE"]);
		$TODATE 	= $this->Convertdate(1,$_REQUEST["TODATE"]);
		$ystat 		= $_REQUEST["ystat"];
		$orderby 	= $_REQUEST["orderby"];
		
		$cond = ""; $rpcond = "";
		
		if($LOCAT1 != ""){
			$cond .= " AND (A.LOCAT LIKE '%".$LOCAT1."%')";
			$rpcond .= "  สาขา ".$LOCAT1;
		}
		
		if($BILLCOLL1 != ""){
			$cond .= " AND (A.BILLCOLL = '".$BILLCOLL1."')";
			$rpcond .= "  พนักงานเก็บเงิน ".$BILLCOLL1;
		}
		
		if($ystat == 'NO'){
			$cond .= " AND (YDATE IS NULL OR YDATE >= '".$TODATE."')";
			$rpcond .= "  ไม่รวมรถยึด";
		}else{
			$rpcond .= "  รวมรถยึด";
		}
		
		$sql = "
				IF OBJECT_ID('tempdb..#main') IS NOT NULL DROP TABLE #main
				select *
				into #main
				from(
					select A.BILLCOLL, B.NAME, A.LOCAT, A.CONTNO
					from {$this->MAuth->getdb('ARMAST')} A   
					LEFT OUTER JOIN {$this->MAuth->getdb('OFFICER')} B ON A.BILLCOLL=B.CODE     
					where TOTPRC > ISNULL((SELECT SUM(PAYAMT) FROM {$this->MAuth->getdb('CHQTRAN')} WHERE FLAG<>'C' AND CONTNO=A.CONTNO 
					AND LOCATPAY = A.LOCAT AND PAYDT < '".$FRMDATE."'),0) 
					".$cond."
					GROUP BY A.BILLCOLL, B.NAME, A.LOCAT, A.CONTNO 
					HAVING COUNT(A.CONTNO) > 0  
					UNION  
					select A.BILLCOLL, B.NAME, A.LOCAT, A.CONTNO  
					from {$this->MAuth->getdb('HARMAST')} A   
					LEFT OUTER JOIN {$this->MAuth->getdb('OFFICER')} B ON A.BILLCOLL = B.CODE    
					where TOTPRC > ISNULL((SELECT SUM(PAYAMT) FROM {$this->MAuth->getdb('CHQTRAN')} WHERE FLAG <> 'C' AND CONTNO=A.CONTNO 
					AND LOCATPAY=A.LOCAT AND PAYDT < '".$FRMDATE."'),0)  AND A.CONTNO NOT IN (
					SELECT CONTNO FROM {$this->MAuth->getdb('ARHOLD')} WHERE YDATE <= '".$FRMDATE."'  UNION  SELECT CONTNO FROM {$this->MAuth->getdb('ARLOST')} WHERE LOSTDT<='".$FRMDATE."'  
					UNION SELECT CONTNO FROM {$this->MAuth->getdb('ARCHAG')} WHERE YDATE <= '".$FRMDATE."'  UNION SELECT CONTNO FROM {$this->MAuth->getdb('ARCLOSE')} WHERE CLDATE <= '".$FRMDATE."')
					".$cond."  
					GROUP BY A.BILLCOLL, B.NAME, A.LOCAT, A.CONTNO 
					HAVING COUNT(A.CONTNO) > 0  
				)main
		";//echo $sql; 
		$query = $this->db->query($sql);
		
		$sql = "
				IF OBJECT_ID('tempdb..#main2') IS NOT NULL DROP TABLE #main2
				select *
				into #main2
				from(
					select A.CONTNO, LOCAT, TOTDAMT, B_DAMT, DAMT, isnull(B_PAYDATM,0) as B_PAYDATM, isnull(PAYDATM,0) as PAYDATM,
					case when TOTDAMT-isnull(PAYDATM,0)-isnull(B_PAYDATM,0) > 0 then 1 else 0 end as QTYARRVC,
					case when PAYDATM > 0 then 1 else 0 end as QTYARPAY
					from(
						select CONTNO, LOCAT,
						sum(CASE WHEN (DDATE BETWEEN '".$FRMDATE."' AND '".$TODATE."' ) THEN  DAMT ELSE 0 END) AS DAMT,
						sum(CASE WHEN (DDATE < '".$FRMDATE."' ) THEN  DAMT  ELSE 0 END) AS B_DAMT,
						sum(DAMT) AS TOTDAMT  
						from {$this->MAuth->getdb('ARPAY')} 
						where CONTNO in (select CONTNO from #main)  
						GROUP BY CONTNO, LOCAT
						UNION   
						select CONTNO, LOCAT,
						sum(CASE WHEN (DDATE BETWEEN '".$FRMDATE."' AND '".$TODATE."' ) THEN  DAMT ELSE 0 END) AS DAMT,
						sum(CASE WHEN (DDATE < '".$FRMDATE."' ) THEN  DAMT  ELSE 0 END) AS B_DAMT,
						sum(DAMT) AS TOTDAMT 
						from {$this->MAuth->getdb('HARPAY')} 
						where CONTNO in (select CONTNO from #main)  
						GROUP BY CONTNO, LOCAT  
					)A
					left join(
						select A.CONTNO,
						sum(CASE WHEN ((A.PAYDT BETWEEN '".$FRMDATE."' AND '".$TODATE."') AND A.FLAG <>'C' AND ((A.PAYFOR = '006') OR (A.PAYFOR = '007')) ) 
						THEN  A.PAYAMT ELSE 0 END) AS PAYDATM,
						sum(CASE WHEN (A.PAYDT < '".$FRMDATE."' AND A.FLAG <>'C' AND ((A.PAYFOR = '006') OR (A.PAYFOR = '007'))) 
						THEN  A.PAYAMT ELSE 0 END) AS B_PAYDATM 
						FROM {$this->MAuth->getdb('CHQTRAN')} A 
						WHERE   A.FLAG <>'C' AND A.LOCATPAY+A.CONTNO in (select LOCAT+CONTNO from #main) 
						GROUP BY A.CONTNO 
					)B on A.CONTNO = B.CONTNO
				)main2
		";//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$sql = "
				IF OBJECT_ID('tempdb..#main3') IS NOT NULL DROP TABLE #main3
				select *
				into #main3
				from(
					select BILLCOLL, NAME, A.LOCAT, COUNT(A.CONTNO) AS CONT, SUM(QTYARRVC) as QTYARRVC , SUM(QTYARPAY) as QTYARPAY,
					sum((isnull(B_DAMT,0)+isnull(DAMT,0))-isnull(B_PAYDATM,0)) as EXPECT, sum(isnull(PAYDATM,0)) as PAYDATM
					from #main a
					left join #main2 b on a.CONTNO = b.CONTNO and a.LOCAT = b.LOCAT
					group by BILLCOLL, NAME, A.LOCAT
				)main3
		";//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$sql = "
				select BILLCOLL, NAME, LOCAT, CONT, QTYARRVC, QTYARPAY, (QTYARPAY*100)/nullif(QTYARRVC,0) as PERCEN1, EXPECT, PAYDATM,
				round((PAYDATM*100)/nullif(EXPECT,0),2) as PERCEN2
				from #main3
				order by ".$orderby."
		";//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$sql = "
				select 'รวมทั้งหมด' as Total, sum(CONT) as sumCONT, sum(QTYARRVC) as sumQTYARRVC, sum(QTYARPAY) as sumQTYARPAY, 
				(sum(QTYARPAY)*100)/nullif(sum(QTYARRVC),0) as sumPERCEN1, sum(EXPECT) as sumEXPECT, sum(PAYDATM) as sumPAYDATM,
				round((sum(PAYDATM)*100)/nullif(sum(EXPECT),0),2) as sumPERCEN2
				from #main3
		";//echo $sql; exit;
		$query2 = $this->db->query($sql);
		
		$head = ""; $html = ""; $head2 = "";  $report = ""; $sumreport = ""; $sumreport2 = ""; $i = 0; 
		
		$head = "<tr style='height:25px;'>
				<th style='display:none;'>#</th>
				<th style='vertical-align:top;'>สาขา</th>
				<th style='vertical-align:top;'>รหัสพนักงานเก็บเงิน</th>
				<th style='vertical-align:top;'>ชื่อ - สกุล</th>
				<th style='vertical-align:top;text-align:right;'>จน.บัญชีที่ถือทั้งหมด</th> 
				<th style='vertical-align:top;text-align:right;'>จน.บัญชีที่ต้องเก็บเงิน</th>
				<th style='vertical-align:top;text-align:right;'>จน.บัญชีที่เก็บได้</th>
				<th style='vertical-align:top;text-align:right;'>คิดเป็นเปอร์เซ็นต์</th>
				<th style='vertical-align:top;text-align:right;'>จน.เงินที่คาดหวัง</th>
				<th style='vertical-align:top;text-align:right;'>จน.เงินที่เก็บได้</th>
				<th style='vertical-align:top;text-align:right;'>คิดเป็นเปอร์เซ็นต์</th>
				</tr>
		";
		
		$head2 = "<tr>
					<th style='vertical-align:middle;'>#</th>
					<th style='vertical-align:top;'>สาขา</th>
					<th style='vertical-align:top;'>รหัสพนักงานเก็บเงิน</th>
					<th style='vertical-align:top;'>ชื่อ - สกุล</th>
					<th style='vertical-align:top;text-align:right;'>จน.บัญชีที่ถือทั้งหมด</th> 
					<th style='vertical-align:top;text-align:right;'>จน.บัญชีที่ต้องเก็บเงิน</th>
					<th style='vertical-align:top;text-align:right;'>จน.บัญชีที่เก็บได้</th>
					<th style='vertical-align:top;text-align:right;'>คิดเป็นเปอร์เซ็นต์</th>
					<th style='vertical-align:top;text-align:right;'>จน.เงินที่คาดหวัง</th>
					<th style='vertical-align:top;text-align:right;'>จน.เงินที่เก็บได้</th>
					<th style='vertical-align:top;text-align:right;'>คิดเป็นเปอร์เซ็นต์</th>
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
						<td align='right'>".number_format($row->CONT)."</td>
						<td align='right'>".number_format($row->QTYARRVC)."</td>
						<td align='right'>".number_format($row->QTYARPAY)."</td>
						<td align='right'>".number_format($row->PERCEN1,2)."%</td>
						<td align='right'>".number_format($row->EXPECT,2)."</td>
						<td align='right'>".number_format($row->PAYDATM,2)."</td>
						<td align='right'>".number_format($row->PERCEN2,2)."%</td>
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
						<td style='mso-number-format:\"\#\,\#\#0\";text-align:right;'>".number_format($row->CONT)."</td>
						<td style='mso-number-format:\"\#\,\#\#0\";text-align:right;'>".number_format($row->QTYARRVC)."</td>
						<td style='mso-number-format:\"\#\,\#\#0\";text-align:right;'>".number_format($row->QTYARPAY)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->PERCEN1,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->EXPECT,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->PAYDATM,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->PERCEN2,2)."</td>
					</tr>
				";	
			}
		}
		
		if($query2->row()){
			foreach($query2->result() as $row){
				$sumreport = "
					<tr style='height:25px;'>
						<th colspan='3' style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;vertical-align:middle;text-align:center;'>".$row->Total."</th>
						<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;vertical-align:middle;text-align:right;'>".number_format($row->sumCONT)."</th>
						<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;vertical-align:middle;text-align:right;'>".number_format($row->sumQTYARRVC)."</th>
						<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;vertical-align:middle;text-align:right;'>".number_format($row->sumQTYARPAY)."</th>
						<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;vertical-align:middle;text-align:right;'>".number_format($row->sumPERCEN1,2)."%</th>
						<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;vertical-align:middle;text-align:right;'>".number_format($row->sumEXPECT,2)."</th>
						<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;vertical-align:middle;text-align:right;'>".number_format($row->sumPAYDATM,2)."</th>
						<th style='border:0px;text-align:right;'>".number_format($row->sumPERCEN2,2)."%</th>
					</tr>
				";	
			}
		}
		
		if($query2->row()){
			foreach($query2->result() as $row){
				$sumreport2 = "
					<tr class='trow'>
						<th style='mso-number-format:\"\@\";text-align:center;' colspan='4'>".$row->Total."</th>
						<td style='mso-number-format:\"\#\,\#\#0\";text-align:right;'>".number_format($row->sumCONT)."</td>
						<td style='mso-number-format:\"\#\,\#\#0\";text-align:right;'>".number_format($row->sumQTYARRVC)."</td>
						<td style='mso-number-format:\"\#\,\#\#0\";text-align:right;'>".number_format($row->sumQTYARPAY)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumPERCEN1,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumEXPECT,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumPAYDATM,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumPERCEN2,2)."</td>
					</tr>
				";	
			}
		}
		
		if($i>0){
			$html = "
				<div id='table-fixed-ReportALbillcollwork' class='col-sm-12' style='height:100%;width:100%;overflow:auto;font-size:9pt;'>
					<table id='table-ReportALbillcollwork' style='background-color:white;' class='col-sm-12 display table table-bordered' cellspacing='0' width='calc(100% - 1px)'>
						<thead>
						<tr style='height:40px;'>
							<th colspan='10' style='font-size:12pt;border:0px;vertical-align;middle;text-align:center;'>รายงานผลงานพนักงานเก็บเงิน</th>
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
			<div id='table-fixed-ReportALbillcollwork2' class='col-sm-12' style='height:100%;width:100%;overflow:auto;'>
				<table id='table-ReportALbillcollwork2' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='100%'>
					<thead>
						<tr>
							<th colspan='11' style='font-size:12pt;border:0px;text-align:center;'>รายงานผลงานพนักงานเก็บเงิน</th>
						</tr>
						<tr>
							<td colspan='11' style='border:0px;text-align:center;'>จากวันที่ ".$_REQUEST["FRMDATE"]." - ".$_REQUEST["TODATE"]." ".$rpcond." ออกรายงาน ณ วันที่ ".$this->today('today')."</td>
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
		$data[] = 	urlencode($_REQUEST["LOCAT1"].'||'.$_REQUEST["BILLCOLL1"].'||'.$_REQUEST["FRMDATE"].'||'.$_REQUEST["TODATE"]
					.'||'.$_REQUEST["ystat"].'||'.$_REQUEST["orderby"].'||'.$_REQUEST["layout"]);
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
		$BILLCOLL1 	= str_replace(chr(0),'',$tx[1]);
		$FRMDATE 	= $this->Convertdate(1,$tx[2]);
		$TODATE 	= $this->Convertdate(1,$tx[3]);
		$ystat 		= $tx[4];
		$orderby 	= $tx[5];
		$layout 	= $tx[6];
		
		$cond = ""; $rpcond = "";
		
		if($LOCAT1 != ""){
			$cond .= " AND (A.LOCAT LIKE '%".$LOCAT1."%')";
			$rpcond .= "  สาขา ".$LOCAT1;
		}
		
		if($BILLCOLL1 != ""){
			$cond .= " AND (A.BILLCOLL = '".$BILLCOLL1."')";
			$rpcond .= "  พนักงานเก็บเงิน ".$BILLCOLL1;
		}
		
		if($ystat == 'NO'){
			$cond .= " AND (YDATE IS NULL OR YDATE >= '".$TODATE."')";
			$rpcond .= "  ไม่รวมรถยึด";
		}else{
			$rpcond .= "  รวมรถยึด";
		}
		
		$sql = "
				IF OBJECT_ID('tempdb..#main') IS NOT NULL DROP TABLE #main
				select *
				into #main
				from(
					select A.BILLCOLL, B.NAME, A.LOCAT, A.CONTNO
					from {$this->MAuth->getdb('ARMAST')} A   
					LEFT OUTER JOIN {$this->MAuth->getdb('OFFICER')} B ON A.BILLCOLL=B.CODE     
					where TOTPRC > ISNULL((SELECT SUM(PAYAMT) FROM {$this->MAuth->getdb('CHQTRAN')} WHERE FLAG<>'C' AND CONTNO=A.CONTNO 
					AND LOCATPAY = A.LOCAT AND PAYDT < '".$FRMDATE."'),0) 
					".$cond."
					GROUP BY A.BILLCOLL, B.NAME, A.LOCAT, A.CONTNO 
					HAVING COUNT(A.CONTNO) > 0  
					UNION  
					select A.BILLCOLL, B.NAME, A.LOCAT, A.CONTNO  
					from {$this->MAuth->getdb('HARMAST')} A   
					LEFT OUTER JOIN {$this->MAuth->getdb('OFFICER')} B ON A.BILLCOLL = B.CODE    
					where TOTPRC > ISNULL((SELECT SUM(PAYAMT) FROM {$this->MAuth->getdb('CHQTRAN')} WHERE FLAG <> 'C' AND CONTNO=A.CONTNO 
					AND LOCATPAY=A.LOCAT AND PAYDT < '".$FRMDATE."'),0)  AND A.CONTNO NOT IN (
					SELECT CONTNO FROM {$this->MAuth->getdb('ARHOLD')} WHERE YDATE <= '".$FRMDATE."'  UNION  SELECT CONTNO FROM {$this->MAuth->getdb('ARLOST')} WHERE LOSTDT<='".$FRMDATE."'  
					UNION SELECT CONTNO FROM {$this->MAuth->getdb('ARCHAG')} WHERE YDATE <= '".$FRMDATE."'  UNION SELECT CONTNO FROM {$this->MAuth->getdb('ARCLOSE')} WHERE CLDATE <= '".$FRMDATE."')
					".$cond."  
					GROUP BY A.BILLCOLL, B.NAME, A.LOCAT, A.CONTNO 
					HAVING COUNT(A.CONTNO) > 0  
				)main
		";//echo $sql; 
		$query = $this->db->query($sql);
		
		$sql = "
				IF OBJECT_ID('tempdb..#main2') IS NOT NULL DROP TABLE #main2
				select *
				into #main2
				from(
					select A.CONTNO, LOCAT, TOTDAMT, B_DAMT, DAMT, isnull(B_PAYDATM,0) as B_PAYDATM, isnull(PAYDATM,0) as PAYDATM,
					case when TOTDAMT-isnull(PAYDATM,0)-isnull(B_PAYDATM,0) > 0 then 1 else 0 end as QTYARRVC,
					case when PAYDATM > 0 then 1 else 0 end as QTYARPAY
					from(
						select CONTNO, LOCAT,
						sum(CASE WHEN (DDATE BETWEEN '".$FRMDATE."' AND '".$TODATE."' ) THEN  DAMT ELSE 0 END) AS DAMT,
						sum(CASE WHEN (DDATE < '".$FRMDATE."' ) THEN  DAMT  ELSE 0 END) AS B_DAMT,
						sum(DAMT) AS TOTDAMT  
						from {$this->MAuth->getdb('ARPAY')} 
						where CONTNO in (select CONTNO from #main)  
						GROUP BY CONTNO, LOCAT
						UNION   
						select CONTNO, LOCAT,
						sum(CASE WHEN (DDATE BETWEEN '".$FRMDATE."' AND '".$TODATE."' ) THEN  DAMT ELSE 0 END) AS DAMT,
						sum(CASE WHEN (DDATE < '".$FRMDATE."' ) THEN  DAMT  ELSE 0 END) AS B_DAMT,
						sum(DAMT) AS TOTDAMT 
						from {$this->MAuth->getdb('HARPAY')} 
						where CONTNO in (select CONTNO from #main)  
						GROUP BY CONTNO, LOCAT  
					)A
					left join(
						select A.CONTNO,
						sum(CASE WHEN ((A.PAYDT BETWEEN '".$FRMDATE."' AND '".$TODATE."') AND A.FLAG <>'C' AND ((A.PAYFOR = '006') OR (A.PAYFOR = '007')) ) 
						THEN  A.PAYAMT ELSE 0 END) AS PAYDATM,
						sum(CASE WHEN (A.PAYDT < '".$FRMDATE."' AND A.FLAG <>'C' AND ((A.PAYFOR = '006') OR (A.PAYFOR = '007'))) 
						THEN  A.PAYAMT ELSE 0 END) AS B_PAYDATM 
						FROM {$this->MAuth->getdb('CHQTRAN')} A 
						WHERE   A.FLAG <>'C' AND A.LOCATPAY+A.CONTNO in (select LOCAT+CONTNO from #main) 
						GROUP BY A.CONTNO 
					)B on A.CONTNO = B.CONTNO
				)main2
		";//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$sql = "
				IF OBJECT_ID('tempdb..#main3') IS NOT NULL DROP TABLE #main3
				select *
				into #main3
				from(
					select BILLCOLL, NAME, A.LOCAT, COUNT(A.CONTNO) AS CONT, SUM(QTYARRVC) as QTYARRVC , SUM(QTYARPAY) as QTYARPAY,
					sum((isnull(B_DAMT,0)+isnull(DAMT,0))-isnull(B_PAYDATM,0)) as EXPECT, sum(isnull(PAYDATM,0)) as PAYDATM
					from #main a
					left join #main2 b on a.CONTNO = b.CONTNO and a.LOCAT = b.LOCAT
					group by BILLCOLL, NAME, A.LOCAT
				)main3
		";//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$sql = "
				select BILLCOLL, NAME, LOCAT, CONT, QTYARRVC, QTYARPAY, (QTYARPAY*100)/nullif(QTYARRVC,0) as PERCEN1, EXPECT, PAYDATM,
				round((PAYDATM*100)/nullif(EXPECT,0),2) as PERCEN2
				from #main3
				order by ".$orderby."
		";//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$sql = "
				select 'รวมทั้งหมด' as Total, sum(CONT) as sumCONT, sum(QTYARRVC) as sumQTYARRVC, sum(QTYARPAY) as sumQTYARPAY, 
				(sum(QTYARPAY)*100)/nullif(sum(QTYARRVC),0) as sumPERCEN1, sum(EXPECT) as sumEXPECT, sum(PAYDATM) as sumPAYDATM,
				round((sum(PAYDATM)*100)/nullif(sum(EXPECT),0),2) as sumPERCEN2
				from #main3
		";//echo $sql; exit;
		$query2 = $this->db->query($sql);
		
		$head = ""; $html = ""; $i=0; 

		$head = "
				<tr>
					<th style='border-bottom:0.1px solid black;text-align:left;'>#</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>สาขา</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>รหัสพนักงาน</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>ชื่อ - สกุล</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>จน.บัญชีที่ถือทั้งหมด</th> 
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>จน.บัญชีที่ต้องเก็บเงิน</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>จน.บัญชีที่เก็บได้</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>คิดเป็นเปอร์เซ็นต์</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>จน.เงินที่คาดหวัง</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>จน.เงินที่เก็บได้</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>คิดเป็นเปอร์เซ็นต์</th>
				</tr>
		";
		
		$No = 1;
		if($query->row()){
			foreach($query->result() as $row){	
				$html .= "
					<tr class='trow' seq=".$No.">
						<td style='width:30px;'>".$No++."</td>
						<td style='width:50px;'>".$row->LOCAT."</td>
						<td style='width:80px;'>".$row->BILLCOLL."</td>
						<td style='width:150px;'>".$row->NAME."</td>
						<td style='width:110px;' align='right'>".number_format($row->CONT)."</td>
						<td style='width:110px;' align='right'>".number_format($row->QTYARRVC)."</td>
						<td style='width:95px;' align='right'>".number_format($row->QTYARPAY)."</td>
						<td style='width:95px;' align='right'>".number_format($row->PERCEN1,2)."%</td>
						<td style='width:100px;' align='right'>".number_format($row->EXPECT,2)."</td>
						<td style='width:95px;' align='right'>".number_format($row->PAYDATM,2)."</td>
						<td style='width:95px;' align='right'>".number_format($row->PERCEN2,2)."%</td>
						
					</tr>
				";	
			}
		}
		
		if($query2->row()){
			foreach($query2->result() as $row){	
				$html .= "
					<tr class='trow bor' style='background-color:#ebebeb;'>
						<th colspan='4' style='text-align:center;vertical-align:middle;'>".$row->Total."</th>
						<td align='right'>".number_format($row->sumCONT)."</td>
						<td align='right'>".number_format($row->sumQTYARRVC)."</td>
						<td align='right'>".number_format($row->sumQTYARPAY)."</td>
						<td align='right'>".number_format($row->sumPERCEN1,2)."%</td>
						<td align='right'>".number_format($row->sumEXPECT,2)."</td>
						<td align='right'>".number_format($row->sumPAYDATM,2)."</td>
						<td align='right'>".number_format($row->sumPERCEN2,2)."%</td>
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
						<th colspan='11' style='font-size:10pt;'>รายงานผลงานพนักงานเก็บเงิน</th>
					</tr>
					<tr>
						<td colspan='11' style='font-size:9pt;height:35px;border-bottom:0.1px solid black;text-align:center;'>จากวันที่ ".$tx[2]." - ".$tx[3]." ".$rpcond."</td>
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