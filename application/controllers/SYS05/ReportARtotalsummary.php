<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//BEE+
class ReportARtotalsummary extends MY_Controller {
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
							<br>รายงานลูกหนี้คงเหลือตามสาขา<br>
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
		
		$html.= "<script src='".base_url('public/js/SYS05/ReportARtotalsummary.js')."'></script>";
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
			$rpcond .= "  สาขา ".$LOCAT1;
			$LOCAT1 = " AND (A.LOCAT LIKE '%".$LOCAT1."%')";
		}
		
		if($BILLCOL1 != ""){
			$rpcond .= "  พนักงานเก็บเงิน ".$BILLCOL1;
			$BILLCOL1 = " AND (A.BILLCOLL = '".$BILLCOL1."' )";
			
		}else{
			$BILLCOL1 = " AND (A.BILLCOLL LIKE '%%' OR A.BILLCOLL IS NULL)";
		}
		
		if($GCOCE1 != ""){
			$rpcond .= "  กลุ่มสินค้า ".$GCOCE1;
			$GCOCE1 = " AND (C.GCODE LIKE '%".$GCOCE1."%' )";
		}else{
			$GCOCE1 = " AND (C.GCODE LIKE '%%' OR C.GCODE IS NULL)";
		}
		
		$sql = "
				IF OBJECT_ID('tempdb..#main') IS NOT NULL DROP TABLE #main
				select *
				into #main
				from(
					select A.LOCAT, 'SALE_C' as TSALE, COUNT(CONTNO) as QTY, SUM(TOTPRC) as TOTPRC, SUM(TOTAR) as TOTAR
					from (
						select A.LOCAT, A.TSALE, A.CONTNO, A.TOTPRC, A.TOTPRC-A.SMPAY as TOTAR 
						from {$this->MAuth->getdb('ARCRED')} A
						left join {$this->MAuth->getdb('INVTRAN')} C on A.STRNO = C.STRNO
						where (A.SDATE <= '".$ARDATES."')  AND A.TOTPRC > 0 AND (A.TOTPRC > A.SMPAY OR (A.TOTPRC = A.SMPAY AND A.LPAYDT > '".$ARDATES."')) 
						".$LOCAT1." ".$GCOCE1." 
					)A
					group by A.LOCAT
					union all
					select A.LOCAT, 'SALE_F' as TSALE, COUNT(CONTNO) as QTY, SUM(TOTPRC) as TOTPRC, SUM(TOTAR) as TOTAR
					from (
						select A.LOCAT, A.TSALE, A.CONTNO, A.TOTPRC, A.TOTPRC-A.SMPAY as TOTAR 
						from {$this->MAuth->getdb('ARFINC')} A
						left join {$this->MAuth->getdb('INVTRAN')} C on A.STRNO = C.STRNO
						where (A.SDATE <= '".$ARDATES."')  AND A.TOTPRC > 0 AND (A.TOTPRC > A.SMPAY OR (A.TOTPRC = A.SMPAY AND A.LPAYD > '".$ARDATES."')) 
						".$LOCAT1." ".$GCOCE1."
					)A
					group by A.LOCAT
					union all
					select A.LOCAT, 'SALE_H' as TSALE, COUNT(CONTNO) as QTY, SUM(TOTPRC) as TOTPRC, SUM(TOTAR) as TOTAR
					from (
						select A.LOCAT, A.TSALE, A.CONTNO, A.TOTPRC, A.TOTPRC-A.SMPAY as TOTAR 
						from {$this->MAuth->getdb('ARMAST')} A
						left join {$this->MAuth->getdb('INVTRAN')} C on A.STRNO = C.STRNO
						where (A.SDATE <= '".$ARDATES."')  AND A.TOTPRC > 0 AND (A.TOTPRC > A.SMPAY OR (A.TOTPRC = A.SMPAY AND A.LPAYD > '".$ARDATES."')) 
						".$LOCAT1." ".$GCOCE1." ".$BILLCOL1."
					)A
					group by A.LOCAT
					union all
					select A.LOCAT, 'SALE_A' as TSALE, COUNT(CONTNO) as QTY, SUM(TOTPRC) as TOTPRC, SUM(TOTAR) as TOTAR
					from (
						select A.LOCAT, A.TSALE, A.CONTNO, A.TOTPRC, A.TOTPRC-A.SMPAY as TOTAR 
						from {$this->MAuth->getdb('AR_INVOI')} A
						where (A.SDATE <= '".$ARDATES."')  AND A.TOTPRC > 0 AND (A.TOTPRC > A.SMPAY OR (A.TOTPRC = A.SMPAY AND A.LPAYDT > '".$ARDATES."')) 
						".$LOCAT1."  
					)A
					group by A.LOCAT
					union all
					select A.LOCAT, 'SALE_O' as TSALE, COUNT(CONTNO) as QTY, SUM(TOTPRC) as TOTPRC, SUM(TOTAR) as TOTAR
					from (
						select A.LOCAT, A.TSALE, A.CONTNO, A.OPTPTOT as TOTPRC, A.OPTPTOT-A.SMPAY as TOTAR 
						from {$this->MAuth->getdb('AROPTMST')} A
						where (A.SDATE <= '".$ARDATES."')  AND A.OPTPTOT > 0 AND (A.OPTPTOT > A.SMPAY OR (A.OPTPTOT = A.SMPAY AND A.LPAYDT > '".$ARDATES."')) 
						".$LOCAT1." 
					)A
					group by A.LOCAT
				)main
		";//echo $sql; 
		$query = $this->db->query($sql);
		
		$sql = "
				select LOCAT, isnull(LOCATNM,'') as LOCATNM, isnull([SALE_C],'0|0|0') as SALE_C, isnull([SALE_F],'0|0|0') as SALE_F, 
				isnull([SALE_H],'0|0|0') as SALE_H, isnull([SALE_A],'0|0|0') as SALE_A, isnull([SALE_O],'0|0|0') as SALE_O
				from(
					select LOCAT, LOCATNM, TSALE, convert(nvarchar,QTY)+'|'+convert(nvarchar,TOTPRC)+'|'+convert(nvarchar,TOTAR) as QTY
					from #main a
					left join INVLOCAT b on a.LOCAT = b.LOCATCD
				)A
				pivot(
					max(QTY) for TSALE in ([SALE_C],[SALE_F],[SALE_H],[SALE_A],[SALE_O])
				)as data
				order by LOCAT
		";//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$sql = "
				select Total, isnull([SALE_C],'0|0|0') as SALE_C, isnull([SALE_F],'0|0|0') as SALE_F, 
				isnull([SALE_H],'0|0|0') as SALE_H, isnull([SALE_A],'0|0|0') as SALE_A, isnull([SALE_O],'0|0|0') as SALE_O
				from(
					select 'รวมทั้งหมด' as Total, TSALE, convert(nvarchar,sum(QTY))+'|'+convert(nvarchar,sum(TOTPRC))+'|'+convert(nvarchar,sum(TOTAR)) as QTY
					from #main
					group by TSALE
				)A
				pivot(
					max(QTY) for TSALE in ([SALE_C],[SALE_F],[SALE_H],[SALE_A],[SALE_O])
				)as data
		";//echo $sql; exit;
		$query2 = $this->db->query($sql);
		
		
		$head = ""; $html = ""; $head2 = ""; $tbRow = ""; $report = ""; $sumreport = ""; $sumreport2 = ""; $i = 0; 
		
		$head = "
				<tr style='height:30px;'>
					<th rowspan='2' style='display:none;'>#</th>
					<th rowspan='2' style='vertical-align:middle;text-align:center;'>สาขา</th>
					<th rowspan='2' style='vertical-align:middle;text-align:center;'>ชื่อสาขา</th>
					<th colspan='3' style='vertical-align:top;text-align:center;'>ขายสด</th>
					<th colspan='3' style='vertical-align:top;text-align:center;'>ขายส่งไฟแนนซ์</th>
					<th colspan='3' style='vertical-align:top;text-align:center;'>ขายผ่อน</th>
					<th colspan='3' style='vertical-align:top;text-align:center;'>ขายส่งเอเย่นต์</th>
					<th colspan='3' style='vertical-align:top;text-align:center;'>ขายอุปกรณ์เสริม</th>
				</tr>
				<tr style='height:30px;'>
					<th style='vertical-align:top;text-align:right;'>จน สัญญา</th> 
					<th style='vertical-align:top;text-align:right;'>รวมราคาขาย</th>
					<th style='vertical-align:top;text-align:right;'>ลูกหนี้คงเหลือ</th>
					<th style='vertical-align:top;text-align:right;'>จน สัญญา</th> 
					<th style='vertical-align:top;text-align:right;'>รวมราคาขาย</th>
					<th style='vertical-align:top;text-align:right;'>ลูกหนี้คงเหลือ</th>
					<th style='vertical-align:top;text-align:right;'>จน สัญญา</th> 
					<th style='vertical-align:top;text-align:right;'>รวมราคาขาย</th>
					<th style='vertical-align:top;text-align:right;'>ลูกหนี้คงเหลือ</th>
					<th style='vertical-align:top;text-align:right;'>จน สัญญา</th> 
					<th style='vertical-align:top;text-align:right;'>รวมราคาขาย</th>
					<th style='vertical-align:top;text-align:right;'>ลูกหนี้คงเหลือ</th>
					<th style='vertical-align:top;text-align:right;'>จน สัญญา</th> 
					<th style='vertical-align:top;text-align:right;'>รวมราคาขาย</th>
					<th style='vertical-align:top;text-align:right;'>ลูกหนี้คงเหลือ</th>
				</tr>
		";
		
		$head2 = "<tr>
					<th rowspan='2' style='vertical-align:middle;'>#</th>
					<th rowspan='2' style='vertical-align:middle;text-align:center;'>สาขา</th>
					<th rowspan='2' style='vertical-align:middle;text-align:center;'>ชื่อสาขา</th>
					<th colspan='3' style='vertical-align:top;text-align:center;'>ขายสด</th>
					<th colspan='3' style='vertical-align:top;text-align:center;'>ขายส่งไฟแนนซ์</th>
					<th colspan='3' style='vertical-align:top;text-align:center;'>ขายผ่อน</th>
					<th colspan='3' style='vertical-align:top;text-align:center;'>ขายส่งเอเย่นต์</th>
					<th colspan='3' style='vertical-align:top;text-align:center;'>ขายอุปกรณ์เสริม</th>
				</tr>
				<tr>
					<th style='vertical-align:top;text-align:right;'>จน สัญญา</th> 
					<th style='vertical-align:top;text-align:right;'>รวมราคาขาย</th>
					<th style='vertical-align:top;text-align:right;'>ลูกหนี้คงเหลือ</th>
					<th style='vertical-align:top;text-align:right;'>จน สัญญา</th> 
					<th style='vertical-align:top;text-align:right;'>รวมราคาขาย</th>
					<th style='vertical-align:top;text-align:right;'>ลูกหนี้คงเหลือ</th>
					<th style='vertical-align:top;text-align:right;'>จน สัญญา</th> 
					<th style='vertical-align:top;text-align:right;'>รวมราคาขาย</th>
					<th style='vertical-align:top;text-align:right;'>ลูกหนี้คงเหลือ</th>
					<th style='vertical-align:top;text-align:right;'>จน สัญญา</th> 
					<th style='vertical-align:top;text-align:right;'>รวมราคาขาย</th>
					<th style='vertical-align:top;text-align:right;'>ลูกหนี้คงเหลือ</th>
					<th style='vertical-align:top;text-align:right;'>จน สัญญา</th> 
					<th style='vertical-align:top;text-align:right;'>รวมราคาขาย</th>
					<th style='vertical-align:top;text-align:right;'>ลูกหนี้คงเหลือ</th>
				</tr>
		";
		
		$NRow = 1;
		if($query->row()){
			foreach($query->result() as $row){
				$i++;
				$rowArray = (array) $row;
				$html.="<tr class='trow' seq=".$NRow."><td seq=".$NRow++." style='display:none;'></td>";
				foreach($rowArray as $key=>$val){
					switch($key){
						case 'LOCAT':
							$html.="<td>".$val."</td>"; break;
						case 'LOCATNM':
							$html.="<td>".$val."</td>"; break;
						default :
							$setostyles = ""; $setostyles2 = ""; $setostyles3 = ""; 
							$convint = "";$convint2 = ""; $convint3 = "";
							$ex = explode("|",$val);
							$convint = number_format($ex[0]);
							$convint2 = number_format($ex[1],2);
							$convint3 = number_format($ex[2],2);
							
							$html.="
								<td align='right'>".$convint."</td>
								<td align='right'>".$convint2."</td>
								<td align='right'>".$convint3."</td>
							";
						break;
					}
				}
				$html.="</tr>";
			}
		}
		
		$No = 1;
		if($query->row()){
			foreach($query->result() as $row){
				$rowArray = (array) $row;
				$report.="<tr><td>".$No++."</td>";
				foreach($rowArray as $key=>$val){
					switch($key){
						case 'LOCAT':
							$report.="<td style='mso-number-format:\"\@\";'>".$val."</td>"; break;
						case 'LOCATNM':
							$report.="<td style='mso-number-format:\"\@\";'>".$val."</td>"; break;
						default :
							$setostyles = ""; $setostyles2 = ""; $setostyles3 = ""; 
							$convint = "";$convint2 = ""; $convint3 = "";
							$ex = explode("|",$val);
							$convint = number_format($ex[0]);
							$convint2 = number_format($ex[1],2);
							$convint3 = number_format($ex[2],2);
							
							$report.="
								<td style='mso-number-format:\"\#\,\#\#0\";text-align:right;''>".$convint."</td>
								<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".$convint2."</td>
								<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".$convint3."</td>
							";
						break;
					}
				}
				$report.="</tr>";
			}
		}
		
		
		if($query2->row()){
			foreach($query2->result() as $row){
				$i++;
				$rowArray = (array) $row;
				$sumreport.="<tr>";
				foreach($rowArray as $key=>$val){
					switch($key){
						case 'Total':
							$sumreport.="<th colspan='2' style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;text-align:center;'>".$val."</th>"; break;
						default :
							$setostyles = ""; $setostyles2 = ""; $setostyles3 = ""; 
							$convint = "";$convint2 = ""; $convint3 = "";
							$ex = explode("|",$val);
							$convint = number_format($ex[0]);
							$convint2 = number_format($ex[1],2);
							$convint3 = number_format($ex[2],2);
							
							$sumreport.="
								<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;text-align:right;'>".$convint."</th>
								<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;text-align:right;'>".$convint2."</th>
								<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;text-align:right;'>".$convint3."</th>
							";
						break;
					}
				}
				$sumreport.="</tr>";
			}
		}
		
		if($query2->row()){
			foreach($query2->result() as $row){
				$i++;
				$rowArray = (array) $row;
				$sumreport2.="<tr>";
				foreach($rowArray as $key=>$val){
					switch($key){
						case 'Total':
							$sumreport2.="<th colspan='3' style='mso-number-format:\"\@\";text-align:center;'>".$val."</th>"; break;
						default :
							$setostyles = ""; $setostyles2 = ""; $setostyles3 = ""; 
							$convint = "";$convint2 = ""; $convint3 = "";
							$ex = explode("|",$val);
							$convint = number_format($ex[0]);
							$convint2 = number_format($ex[1],2);
							$convint3 = number_format($ex[2],2);
							
							$sumreport2.="
								<th style='mso-number-format:\"\#\,\#\#0\";text-align:right;'>".$convint."</th>
								<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".$convint2."</th>
								<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".$convint3."</th>
							";
						break;
					}
				}
				$sumreport2.="</tr>";
			}
		}
		
		if($i>0){
			$html = "
				<div id='table-fixed-ReportARtotalsummary' class='col-sm-12' style='height:100%;width:100%;overflow:auto;font-size:8pt;'>
					<table id='table-ReportARtotalsummary' style='background-color:white;' class='col-sm-12 display table table-bordered' cellspacing='0' width='calc(100% - 1px)'>
						<thead>
						<tr style='height:40px;'>
							<th colspan='17' style='font-size:12pt;border:0px;text-align:center;'>รายงานลูกหนี้คงเหลือตามสาขา</th>
						</tr>
						<tr style='height:25px;'>
							<td colspan='17' style='border-bottom:1px solid #ddd;text-align:center;'>ลูกหนี้ ณ วันที่ ".$ARDATE." ".$rpcond."  ออกรายงาน ณ วันที่ ".$this->today('today')."</td>
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
			<div id='table-fixed-ReportARtotalsummary2' class='col-sm-12' style='height:100%;width:100%;overflow:auto;'>
				<table id='table-ReportARtotalsummary2' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='100%'>
					<thead>
						<tr>
							<th colspan='18' style='font-size:12pt;border:0px;text-align:center;'>รายงานลูกหนี้คงเหลือตามสาขา</th>
						</tr>
						<tr>
							<td colspan='18' style='border:0px;text-align:center;'>ลูกหนี้ ณ วันที่ ".$ARDATE." ".$rpcond." ออกรายงาน ณ วันที่ ".$this->today('today')."</td>
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
			$rpcond .= "  สาขา ".$LOCAT1;
			$LOCAT1 = " AND (A.LOCAT LIKE '%".$LOCAT1."%')";
		}
		
		if($BILLCOL1 != ""){
			$rpcond .= "  พนักงานเก็บเงิน ".$BILLCOL1;
			$BILLCOL1 = " AND (A.BILLCOLL = '".$BILLCOL1."' )";
			
		}else{
			$BILLCOL1 = " AND (A.BILLCOLL LIKE '%%' OR A.BILLCOLL IS NULL)";
		}
		
		if($GCOCE1 != ""){
			$rpcond .= "  กลุ่มสินค้า ".$GCOCE1;
			$GCOCE1 = " AND (C.GCODE LIKE '%".$GCOCE1."%' )";
		}else{
			$GCOCE1 = " AND (C.GCODE LIKE '%%' OR C.GCODE IS NULL)";
		}
		
		$sql = "
				IF OBJECT_ID('tempdb..#main') IS NOT NULL DROP TABLE #main
				select *
				into #main
				from(
					select A.LOCAT, 'SALE_C' as TSALE, COUNT(CONTNO) as QTY, SUM(TOTPRC) as TOTPRC, SUM(TOTAR) as TOTAR
					from (
						select A.LOCAT, A.TSALE, A.CONTNO, A.TOTPRC, A.TOTPRC-A.SMPAY as TOTAR 
						from {$this->MAuth->getdb('ARCRED')} A
						left join {$this->MAuth->getdb('INVTRAN')} C on A.STRNO = C.STRNO
						where (A.SDATE <= '".$ARDATES."')  AND A.TOTPRC > 0 AND (A.TOTPRC > A.SMPAY OR (A.TOTPRC = A.SMPAY AND A.LPAYDT > '".$ARDATES."')) 
						".$LOCAT1." ".$GCOCE1." 
					)A
					group by A.LOCAT
					union all
					select A.LOCAT, 'SALE_F' as TSALE, COUNT(CONTNO) as QTY, SUM(TOTPRC) as TOTPRC, SUM(TOTAR) as TOTAR
					from (
						select A.LOCAT, A.TSALE, A.CONTNO, A.TOTPRC, A.TOTPRC-A.SMPAY as TOTAR 
						from {$this->MAuth->getdb('ARFINC')} A
						left join {$this->MAuth->getdb('INVTRAN')} C on A.STRNO = C.STRNO
						where (A.SDATE <= '".$ARDATES."')  AND A.TOTPRC > 0 AND (A.TOTPRC > A.SMPAY OR (A.TOTPRC = A.SMPAY AND A.LPAYD > '".$ARDATES."')) 
						".$LOCAT1." ".$GCOCE1."
					)A
					group by A.LOCAT
					union all
					select A.LOCAT, 'SALE_H' as TSALE, COUNT(CONTNO) as QTY, SUM(TOTPRC) as TOTPRC, SUM(TOTAR) as TOTAR
					from (
						select A.LOCAT, A.TSALE, A.CONTNO, A.TOTPRC, A.TOTPRC-A.SMPAY as TOTAR 
						from {$this->MAuth->getdb('ARMAST')} A
						left join {$this->MAuth->getdb('INVTRAN')} C on A.STRNO = C.STRNO
						where (A.SDATE <= '".$ARDATES."')  AND A.TOTPRC > 0 AND (A.TOTPRC > A.SMPAY OR (A.TOTPRC = A.SMPAY AND A.LPAYD > '".$ARDATES."')) 
						".$LOCAT1." ".$GCOCE1." ".$BILLCOL1."
					)A
					group by A.LOCAT
					union all
					select A.LOCAT, 'SALE_A' as TSALE, COUNT(CONTNO) as QTY, SUM(TOTPRC) as TOTPRC, SUM(TOTAR) as TOTAR
					from (
						select A.LOCAT, A.TSALE, A.CONTNO, A.TOTPRC, A.TOTPRC-A.SMPAY as TOTAR 
						from {$this->MAuth->getdb('AR_INVOI')} A
						where (A.SDATE <= '".$ARDATES."')  AND A.TOTPRC > 0 AND (A.TOTPRC > A.SMPAY OR (A.TOTPRC = A.SMPAY AND A.LPAYDT > '".$ARDATES."')) 
						".$LOCAT1."  
					)A
					group by A.LOCAT
					union all
					select A.LOCAT, 'SALE_O' as TSALE, COUNT(CONTNO) as QTY, SUM(TOTPRC) as TOTPRC, SUM(TOTAR) as TOTAR
					from (
						select A.LOCAT, A.TSALE, A.CONTNO, A.OPTPTOT as TOTPRC, A.OPTPTOT-A.SMPAY as TOTAR 
						from {$this->MAuth->getdb('AROPTMST')} A
						where (A.SDATE <= '".$ARDATES."')  AND A.OPTPTOT > 0 AND (A.OPTPTOT > A.SMPAY OR (A.OPTPTOT = A.SMPAY AND A.LPAYDT > '".$ARDATES."')) 
						".$LOCAT1." 
					)A
					group by A.LOCAT
				)main
		";//echo $sql; 
		$query = $this->db->query($sql);
		
		$sql = "
				select LOCAT, isnull(LOCATNM,'') as LOCATNM, isnull([SALE_C],'0|0|0') as SALE_C, isnull([SALE_F],'0|0|0') as SALE_F, 
				isnull([SALE_H],'0|0|0') as SALE_H, isnull([SALE_A],'0|0|0') as SALE_A, isnull([SALE_O],'0|0|0') as SALE_O
				from(
					select LOCAT, LOCATNM, TSALE, convert(nvarchar,QTY)+'|'+convert(nvarchar,TOTPRC)+'|'+convert(nvarchar,TOTAR) as QTY
					from #main a
					left join INVLOCAT b on a.LOCAT = b.LOCATCD
				)A
				pivot(
					max(QTY) for TSALE in ([SALE_C],[SALE_F],[SALE_H],[SALE_A],[SALE_O])
				)as data
				order by LOCAT
		";//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$sql = "
				select Total, isnull([SALE_C],'0|0|0') as SALE_C, isnull([SALE_F],'0|0|0') as SALE_F, 
				isnull([SALE_H],'0|0|0') as SALE_H, isnull([SALE_A],'0|0|0') as SALE_A, isnull([SALE_O],'0|0|0') as SALE_O
				from(
					select 'รวมทั้งหมด' as Total, TSALE, convert(nvarchar,sum(QTY))+'|'+convert(nvarchar,sum(TOTPRC))+'|'+convert(nvarchar,sum(TOTAR)) as QTY
					from #main
					group by TSALE
				)A
				pivot(
					max(QTY) for TSALE in ([SALE_C],[SALE_F],[SALE_H],[SALE_A],[SALE_O])
				)as data
		";//echo $sql; exit;
		$query2 = $this->db->query($sql);
		
		$head = ""; $html = ""; $i=0; 

		$head = "
				<tr>
					<th rowspan='2' style='border-bottom:0.1px solid black;vertical-align:middle;text-align:left;'>#</th>
					<th rowspan='2' style='border-bottom:0.1px solid black;vertical-align:middle;text-align:left;'>สาขา</th>
					<th colspan='3' style='border-bottom:0.1px solid black;vertical-align:top;text-align:center;'>ขายสด</th>
					<th colspan='3' style='border-bottom:0.1px solid black;vertical-align:top;text-align:center;'>ขายส่งไฟแนนซ์</th>
					<th colspan='3' style='border-bottom:0.1px solid black;vertical-align:top;text-align:center;'>ขายผ่อน</th>
					<th colspan='3' style='border-bottom:0.1px solid black;vertical-align:top;text-align:center;'>ขายส่งเอเย่นต์</th>
					<th colspan='3' style='border-bottom:0.1px solid black;vertical-align:top;text-align:center;'>ขายอุปกรณ์เสริม</th>
				</tr>
				<tr>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>จำนวน</th> 
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>รวมราคาขาย</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>ลูกหนี้คงเหลือ</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>จำนวน</th> 
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>รวมราคาขาย</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>ลูกหนี้คงเหลือ</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>จำนวน</th> 
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>รวมราคาขาย</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>ลูกหนี้คงเหลือ</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>จำนวน</th> 
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>รวมราคาขาย</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>ลูกหนี้คงเหลือ</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>จำนวน</th> 
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>รวมราคาขาย</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>ลูกหนี้คงเหลือ</th>
				</tr>
		";//<th rowspan='2' style='border-bottom:0.1px solid black;vertical-align:middle;text-align:center;'>ชื่อสาขา</th>
		
		$No = 1;
		if($query->row()){
			foreach($query->result() as $row){	
				$rowArray = (array) $row;
				$html.="<tr class='trow' seq=".$No."><td style='width:30px;'>".$No++."</td>";
				foreach($rowArray as $key=>$val){
					switch($key){
						case 'LOCAT':
							$html.="<td style='width:50px;'>".$val."</td>"; break;
						case 'LOCATNM': continue;
							//$html.="<td style='width:200px;'>".$val."</td>"; break;
						default :
							$setostyles = ""; $setostyles2 = ""; $setostyles3 = ""; 
							$convint = "";$convint2 = ""; $convint3 = "";
							$ex = explode("|",$val);
							$convint = number_format($ex[0]);
							$convint2 = number_format($ex[1],2);
							$convint3 = number_format($ex[2],2);
							
							$html.="
								<td style='width:50px;' align='right'>".$convint."</td>
								<td style='width:90px;' align='right'>".$convint2."</td>
								<td style='width:90px;' align='right'>".$convint3."</td>
							";
						break;
					}
				}
				$html.="</tr>";
			}
		}
		
		if($query2->row()){
			foreach($query2->result() as $row){	
				$rowArray = (array) $row;
				$html.="<tr class='trow bor' style='background-color:#ebebeb;'>";
				foreach($rowArray as $key=>$val){
					switch($key){
						case 'Total':
							$html.="<td colspan='2' style='text-align:center;vertical-align:middle;'>".$val."</td>"; break;
						default :
							$setostyles = ""; $setostyles2 = ""; $setostyles3 = ""; 
							$convint = "";$convint2 = ""; $convint3 = "";
							$ex = explode("|",$val);
							$convint = number_format($ex[0]);
							$convint2 = number_format($ex[1],2);
							$convint3 = number_format($ex[2],2);
							
							$html.="
								<td align='right'>".$convint."</td>
								<td align='right'>".$convint2."</td>
								<td align='right'>".$convint3."</td>
							";
						break;
					}
				}
				$html.="</tr>";
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
						<th colspan='17' style='font-size:10pt;'>รายงานลูกหนี้คงเหลือตามสาขา</th>
					</tr>
					<tr>
						<td colspan='17' style='font-size:8pt;height:35px;border-bottom:0.1px solid black;text-align:center;'>ลูกหนี้ ณ วันที่ ".$ARDATE." ".$rpcond."</td>
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