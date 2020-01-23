<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//BEE+
class ReportALar_endofmonth extends MY_Controller {
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
		//เมนูเก่า คือ รายงานวิเคราะห์สภาพลูกหนี้ ณ ปัจจุบัน
		$html = "
			<div class='b_tab1' name='home' locat='{$this->sess['branch']}' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}' today='".$this->today('today')."' style='height:calc(100vh - 132px);overflow:auto;background-color:white;'>
				<div class='col-sm-12 col-xs-12' style='height:100%;overflow:auto;font-size:10.5pt;'>					
					<div class='row' style='height:90%;'>
						<div class='col-sm-12 col-xs-12' style='background-color:#0067a5;border:5px solid white;height:75px;text-align:center;font-size:12pt;color:white;font-weight:bold;'>	
							<br>รายงานวิเคราะห์สภาพลูกหนี้ ณ ปัจจุบัน<br>
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
									ลูกหนี้จากวันที่ขาย
									<input type='text' id='FRMDATE' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' placeholder='สภาพลูกหนี้จากวันที่' value='".$this->today('today')."' style='font-size:10.5pt'>
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
		
		$html.= "<script src='".base_url('public/js/SYS05/ReportALar_endofmonth.js')."'></script>";
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
			$cond .= " AND (a.LOCAT LIKE '%".$LOCAT1."%')";
			$rpcond .= "  สาขา ".$LOCAT1;
		}
		
		if($BILLCOLL1 != ""){
			$cond .= " AND (a.BILLCOLL = '".$CONTNO1."')";
			$rpcond .= "  พนักงานเก็บเงิน ".$BILLCOLL1;
		}
		
		$sql = "
				IF OBJECT_ID('tempdb..#main') IS NOT NULL DROP TABLE #main
				select *
				into #main
				from(
					select LOCAT, BILLCOLL, CONTNO, NAME
					from {$this->MAuth->getdb('ARMAST')} a 
					left join {$this->MAuth->getdb('OFFICER')} b on a.BILLCOLL = b.CODE
					where TOTPRC > SMPAY
					and SDATE between '".$FRMDATE."' and '".$TODATE."'
					".$cond."
				)main
		";//echo $sql; 
		$query = $this->db->query($sql);
		
		$sql = "
				IF OBJECT_ID('tempdb..#main2') IS NOT NULL DROP TABLE #main2
				select *
				into #main2
				from(
					select CONTNO, LOCAT, datediff(day,MIN(DDATE),GETDATE()) as LATEDATE, SUM(DAMT - PAYMENT) as KANG
					from {$this->MAuth->getdb('ARPAY')}
					where CONTNO in (select CONTNO from #main) and DDATE < GETDATE() 
					group by CONTNO, LOCAT 
				)main2
		";//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$sql = "
				IF OBJECT_ID('tempdb..#main3') IS NOT NULL DROP TABLE #main3
				select *
				into #main3
				from(
					select LOCAT, BILLCOLL, NAME, isnull(Q_no,0) as Q_no, isnull(Q_0,0) as Q_0, isnull(Q1_60,0) as Q1_60, isnull(K1_60,0) as K1_60, isnull(Q61_90,0) as Q61_90, 
					isnull(K61_90,0) as K61_90, isnull(Q_91,0) as Q_91, isnull(K_91,0) as K_91, isnull(Q1_60,0)+isnull(Q61_90,0)+isnull(Q_91,0) as Q_TOT,
					isnull(K1_60,0)+isnull(K61_90,0)+isnull(K_91,0) as K_TOT
					from(
						select LOCAT, BILLCOLL, NAME, QQ, sum(QTY) as QTY
						from(
							select a.LOCAT, a.CONTNO, a.BILLCOLL, a.NAME,
							case	when b.CONTNO is null then 'Q_no'  
									when b.KANG = 0 then 'Q_0'
									when b.KANG > 0 and b.LATEDATE between 1 and 60 then 'Q1_60'
									when b.KANG > 0 and b.LATEDATE between 61 and 90 then 'Q61_90'
									when b.KANG > 0 and b.LATEDATE > 90 then 'Q_91'
							end as QQ, 1 as QTY
							from #main a
							left join #main2 b on a.CONTNO = b.CONTNO collate thai_cs_as
							union all
							select a.LOCAT, a.CONTNO, a.BILLCOLL, a.NAME, 
							case	when b.CONTNO is null then 'K_no'  
									when b.KANG = 0 then 'K_0'  
									when b.KANG > 0 and b.LATEDATE between 1 and 60 then 'K1_60'
									when b.KANG > 0 and b.LATEDATE between 61 and 90 then 'K61_90'
									when b.KANG > 0 and b.LATEDATE > 90 then 'K_91'
							end as QQ, case	when b.CONTNO is null then 0 else b.KANG end as QTY
							from #main a
							left join #main2 b on a.CONTNO = b.CONTNO collate thai_cs_as
						)A
						group by LOCAT, BILLCOLL, NAME, QQ
						
					)B
					pivot(
						max(QTY) for QQ in(Q_no,Q_0,Q1_60,Q61_90,Q_91,K_no,K_0,K1_60,K61_90,K_91)
					)as data
				)main3
		";//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$sql = "
				select LOCAT, BILLCOLL, NAME, Q_no, Q_0, Q1_60, K1_60, Q61_90, K61_90, Q_91, K_91, Q_TOT, K_TOT
				from #main3
				".$orderby."
		";//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$sql = "
				select 'รวมทั้งหมด' as Total, sum(Q_no) as sumQ_no, sum(Q_0) as sumQ_0, sum(Q1_60) as sumQ1_60, sum(K1_60) as sumK1_60, sum(Q61_90) as sumQ61_90, 
				sum(K61_90) as sumK61_90, sum(Q_91) as sumQ_91, sum(K_91) as sumK_91, sum(Q_TOT) as sumQ_TOT, sum(K_TOT) as sumK_TOT
				from #main3
		";//echo $sql; exit;
		$query2 = $this->db->query($sql);
		
		$head = ""; $html = ""; $head2 = "";  $report = ""; $sumreport = ""; $sumreport2 = ""; $i = 0; 
		
		$head = "<tr style='height:25px;'>
				<th rowspan='2' style='display:none;'>#</th>
				<th rowspan='2' style='vertical-align:top;'>สาขา</th>
				<th rowspan='2' style='vertical-align:top;'>รหัสพนักงาน</th>
				<th rowspan='2' style='vertical-align:top;'>ชื่อพนักงาน</th>
				<th style='vertical-align:top;text-align:right;'>ยังไม่ถึงกำหนด</th>
				<th style='vertical-align:top;text-align:right;'>ชำระปกติ</th> 
				<th colspan='2' style='vertical-align:top;text-align:center;'>ค้างชำระ 1-60 วัน</th> 
				<th colspan='2' style='vertical-align:top;text-align:center;'>ค้างชำระ 61-90 วัน</th> 
				<th colspan='2' style='vertical-align:top;text-align:center;'>ค้างชำระมากกว่า 90 วัน</th> 
				<th colspan='2' style='vertical-align:top;text-align:center;'>รวมค้างชำระ</th> 
				</tr>
				<tr>
				<th style='vertical-align:top;text-align:right;'>จำนวน</th>
				<th style='vertical-align:top;text-align:right;'>จำนวน</th>
				<th style='vertical-align:top;text-align:right;'>จำนวน</th>
				<th style='vertical-align:top;text-align:right;'>เป็นเงิน</th>
				<th style='vertical-align:top;text-align:right;'>จำนวน</th>
				<th style='vertical-align:top;text-align:right;'>เป็นเงิน</th>
				<th style='vertical-align:top;text-align:right;'>จำนวน</th> 
				<th style='vertical-align:top;text-align:right;'>เป็นเงิน</th>
				<th style='vertical-align:top;text-align:right;'>จำนวน</th>
				<th style='vertical-align:top;text-align:right;'>เป็นเงิน</th>
				</tr>
		";
		
		$head2 = "<tr>
					<th rowspan='2' style='vertical-align:middle;'>#</th>
					<th rowspan='2' style='vertical-align:top;'>สาขา</th>
					<th rowspan='2' style='vertical-align:top;'>รหัสพนักงาน</th>
					<th rowspan='2' style='vertical-align:top;'>ชื่อพนักงาน</th>
					<th rowspan='2' style='vertical-align:top;text-align:right;'>ยังไม่ถึงกำหนด<br>จำนวน</th>
					<th rowspan='2' style='vertical-align:top;text-align:right;'>ชำระปกติ<br>จำนวน</th> 
					<th colspan='2' style='vertical-align:top;text-align:center;'>ค้างชำระ 1-60 วัน</th> 
					<th colspan='2' style='vertical-align:top;text-align:center;'>ค้างชำระ 61-90 วัน</th> 
					<th colspan='2' style='vertical-align:top;text-align:center;'>ค้างชำระมากกว่า 90 วัน</th> 
					<th colspan='2' style='vertical-align:top;text-align:center;'>รวมค้างชำระ</th> 
				</tr>
				<tr>
					<th style='vertical-align:top;text-align:right;'>จำนวน</th>
					<th style='vertical-align:top;text-align:right;'>เป็นเงิน</th>
					<th style='vertical-align:top;text-align:right;'>จำนวน</th>
					<th style='vertical-align:top;text-align:right;'>เป็นเงิน</th>
					<th style='vertical-align:top;text-align:right;'>จำนวน</th> 
					<th style='vertical-align:top;text-align:right;'>เป็นเงิน</th>
					<th style='vertical-align:top;text-align:right;'>จำนวน</th>
					<th style='vertical-align:top;text-align:right;'>เป็นเงิน</th>
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
						<td align='right'>".number_format($row->Q_no)."</td>
						<td align='right'>".number_format($row->Q_0)."</td>
						<td align='right'>".number_format($row->Q1_60)."</td>
						<td align='right'>".number_format($row->K1_60,2)."</td>
						<td align='right'>".number_format($row->Q61_90)."</td>
						<td align='right'>".number_format($row->K61_90,2)."</td>
						<td align='right'>".number_format($row->Q_91)."</td>
						<td align='right'>".number_format($row->K_91,2)."</td>
						<td align='right'>".number_format($row->Q_TOT)."</td>
						<td align='right'>".number_format($row->K_TOT,2)."</td>
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
						<td style='mso-number-format:\"\#\,\#\#0\";text-align:right;'>".number_format($row->Q_no)."</td>
						<td style='mso-number-format:\"\#\,\#\#0\";text-align:right;'>".number_format($row->Q_0)."</td>
						<td style='mso-number-format:\"\#\,\#\#0\";text-align:right;'>".number_format($row->Q1_60)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->K1_60,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0\";text-align:right;'>".number_format($row->Q61_90)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->K61_90,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0\";text-align:right;'>".number_format($row->Q_91)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->K_91,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0\";text-align:right;'>".number_format($row->Q_TOT)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->K_TOT,2)."</td>
					</tr>
				";	
			}
		}
		
		if($query2->row()){
			foreach($query2->result() as $row){
				$sumreport = "
					<tr style='height:25px;'>
						<th colspan='3' style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;vertical-align:middle;text-align:center;'>".$row->Total."</th>
						<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;vertical-align:middle;text-align:right;'>".number_format($row->sumQ_no)."</th>
						<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;vertical-align:middle;text-align:right;'>".number_format($row->sumQ_0)."</th>
						<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;vertical-align:middle;text-align:right;'>".number_format($row->sumQ1_60)."</th>
						<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;vertical-align:middle;text-align:right;'>".number_format($row->sumK1_60,2)."</th>
						<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;vertical-align:middle;text-align:right;'>".number_format($row->sumQ61_90)."</th>
						<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;vertical-align:middle;text-align:right;'>".number_format($row->sumK61_90,2)."</th>
						<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;vertical-align:middle;text-align:right;'>".number_format($row->sumQ_91)."</th>
						<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;vertical-align:middle;text-align:right;'>".number_format($row->sumK_91,2)."</th>
						<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;vertical-align:middle;text-align:right;'>".number_format($row->sumQ_TOT)."</th>
						<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;vertical-align:middle;text-align:right;'>".number_format($row->sumK_TOT,2)."</th>
					</tr>
				";	
			}
		}
		
		if($query2->row()){
			foreach($query2->result() as $row){
				$sumreport2 = "
					<tr class='trow'>
						<th style='mso-number-format:\"\@\";text-align:center;' colspan='4'>".$row->Total."</th>
						<th style='mso-number-format:\"\#\,\#\#0\";text-align:right;'>".number_format($row->sumQ_no,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0\";text-align:right;'>".number_format($row->sumQ_0,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0\";text-align:right;'>".number_format($row->sumQ1_60,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumK1_60,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0\";text-align:right;'>".number_format($row->sumQ61_90,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumK61_90,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0\";text-align:right;'>".number_format($row->sumQ_91,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumK_91,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0\";text-align:right;'>".number_format($row->sumQ_TOT,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumK_TOT,2)."</th>
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
							<th colspan='13' style='font-size:12pt;border:0px;vertical-align;middle;text-align:center;'>รายงานวิเคราะห์สภาพลูกหนี้ ณ ปัจจุบัน</th>
						</tr>
						<tr style='height:25px;'>
							<td colspan='13' style='border-bottom:1px solid #ddd;vertical-align;middle;text-align:center;'>ลูกหนี้จากวันที่ขาย ".$_REQUEST["FRMDATE"]." - ".$_REQUEST["TODATE"]." ".$rpcond."  ออกรายงาน ณ วันที่ ".$this->today('today')."</td>
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
							<th colspan='14' style='font-size:12pt;border:0px;text-align:center;'>รายงานวิเคราะห์สภาพลูกหนี้ ณ ปัจจุบัน</th>
						</tr>
						<tr>
							<td colspan='14' style='border:0px;text-align:center;'>ลูกหนี้จากวันที่ขาย ".$_REQUEST["FRMDATE"]." - ".$_REQUEST["TODATE"]." ".$rpcond." ออกรายงาน ณ วันที่ ".$this->today('today')."</td>
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
			$cond .= " AND (a.LOCAT LIKE '%".$LOCAT1."%')";
			$rpcond .= "  สาขา ".$LOCAT1;
		}
		
		if($BILLCOLL1 != ""){
			$cond .= " AND (a.BILLCOLL = '".$CONTNO1."')";
			$rpcond .= "  พนักงานเก็บเงิน ".$BILLCOLL1;
		}
		
		$sql = "
				IF OBJECT_ID('tempdb..#main') IS NOT NULL DROP TABLE #main
				select *
				into #main
				from(
					select LOCAT, BILLCOLL, CONTNO, NAME
					from {$this->MAuth->getdb('ARMAST')} a 
					left join {$this->MAuth->getdb('OFFICER')} b on a.BILLCOLL = b.CODE
					where TOTPRC > SMPAY
					and SDATE between '".$FRMDATE."' and '".$TODATE."'
					".$cond."
				)main
		";//echo $sql; 
		$query = $this->db->query($sql);
		
		$sql = "
				IF OBJECT_ID('tempdb..#main2') IS NOT NULL DROP TABLE #main2
				select *
				into #main2
				from(
					select CONTNO, LOCAT, datediff(day,MIN(DDATE),GETDATE()) as LATEDATE, SUM(DAMT - PAYMENT) as KANG
					from {$this->MAuth->getdb('ARPAY')}
					where CONTNO in (select CONTNO from #main) and DDATE < GETDATE() 
					group by CONTNO, LOCAT 
				)main2
		";//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$sql = "
				IF OBJECT_ID('tempdb..#main3') IS NOT NULL DROP TABLE #main3
				select *
				into #main3
				from(
					select LOCAT, BILLCOLL, NAME, isnull(Q_no,0) as Q_no, isnull(Q_0,0) as Q_0, isnull(Q1_60,0) as Q1_60, isnull(K1_60,0) as K1_60, isnull(Q61_90,0) as Q61_90, 
					isnull(K61_90,0) as K61_90, isnull(Q_91,0) as Q_91, isnull(K_91,0) as K_91, isnull(Q1_60,0)+isnull(Q61_90,0)+isnull(Q_91,0) as Q_TOT,
					isnull(K1_60,0)+isnull(K61_90,0)+isnull(K_91,0) as K_TOT
					from(
						select LOCAT, BILLCOLL, NAME, QQ, sum(QTY) as QTY
						from(
							select a.LOCAT, a.CONTNO, a.BILLCOLL, a.NAME,
							case	when b.CONTNO is null then 'Q_no'  
									when b.KANG = 0 then 'Q_0'
									when b.KANG > 0 and b.LATEDATE between 1 and 60 then 'Q1_60'
									when b.KANG > 0 and b.LATEDATE between 61 and 90 then 'Q61_90'
									when b.KANG > 0 and b.LATEDATE > 90 then 'Q_91'
							end as QQ, 1 as QTY
							from #main a
							left join #main2 b on a.CONTNO = b.CONTNO collate thai_cs_as
							union all
							select a.LOCAT, a.CONTNO, a.BILLCOLL, a.NAME, 
							case	when b.CONTNO is null then 'K_no'  
									when b.KANG = 0 then 'K_0'  
									when b.KANG > 0 and b.LATEDATE between 1 and 60 then 'K1_60'
									when b.KANG > 0 and b.LATEDATE between 61 and 90 then 'K61_90'
									when b.KANG > 0 and b.LATEDATE > 90 then 'K_91'
							end as QQ, case	when b.CONTNO is null then 0 else b.KANG end as QTY
							from #main a
							left join #main2 b on a.CONTNO = b.CONTNO collate thai_cs_as
						)A
						group by LOCAT, BILLCOLL, NAME, QQ
						
					)B
					pivot(
						max(QTY) for QQ in(Q_no,Q_0,Q1_60,Q61_90,Q_91,K_no,K_0,K1_60,K61_90,K_91)
					)as data
				)main3
		";//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$sql = "
				select LOCAT, BILLCOLL, NAME, Q_no, Q_0, Q1_60, K1_60, Q61_90, K61_90, Q_91, K_91, Q_TOT, K_TOT
				from #main3
				order by BILLCOLL
				
		";//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$sql = "
				select 'รวมทั้งหมด' as Total, sum(Q_no) as sumQ_no, sum(Q_0) as sumQ_0, sum(Q1_60) as sumQ1_60, sum(K1_60) as sumK1_60, sum(Q61_90) as sumQ61_90, 
				sum(K61_90) as sumK61_90, sum(Q_91) as sumQ_91, sum(K_91) as sumK_91, sum(Q_TOT) as sumQ_TOT, sum(K_TOT) as sumK_TOT
				from #main3
		";//echo $sql; exit;
		$query2 = $this->db->query($sql);
		
		$head = ""; $html = ""; $i=0; 

		$head = "
				<tr>
				<th rowspan='2' style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>#</th>
				<th rowspan='2' style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>สาขา</th>
				<th rowspan='2' style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>รหัส พนง.</th>
				<th rowspan='2' style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>ชื่อพนักงาน</th>
				<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>ยังไม่ถึงดิวแรก</th>
				<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>ชำระปกติ</th> 
				<th colspan='2' style='border-bottom:0.1px solid black;vertical-align:top;text-align:center;'>ค้างชำระ 1-60 วัน</th> 
				<th colspan='2' style='border-bottom:0.1px solid black;vertical-align:top;text-align:center;'>ค้างชำระ 61-90 วัน</th> 
				<th colspan='2' style='border-bottom:0.1px solid black;vertical-align:top;text-align:center;'>ค้างชำระมากกว่า 90 วัน</th> 
				<th colspan='2' style='border-bottom:0.1px solid black;vertical-align:top;text-align:center;'>รวมค้างชำระ</th> 
				</tr>
				<tr>
				<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>จำนวน</th>
				<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>จำนวน</th>
				<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>จำนวน</th>
				<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>เป็นเงิน</th>
				<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>จำนวน</th>
				<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>เป็นเงิน</th>
				<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>จำนวน</th> 
				<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>เป็นเงิน</th>
				<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>จำนวน</th>
				<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>เป็นเงิน</th>
				</tr>
		";
		
		$No = 1;
		if($query->row()){
			foreach($query->result() as $row){	
				$html .= "
					<tr class='trow' seq=".$No.">
						<td style='width:30px;'>".$No++."</td>
						<td style='width:50px;'>".$row->LOCAT."</td>
						<td style='width:60px;'>".$row->BILLCOLL."</td>
						<td style='width:130px;'>".$row->NAME."</td>
						<td style='width:80px;' align='right'>".number_format($row->Q_no)."</td>
						<td style='width:60px;' align='right'>".number_format($row->Q_0)."</td>
						<td style='width:60px;' align='right'>".number_format($row->Q1_60)."</td>
						<td style='width:100px;' align='right'>".number_format($row->K1_60,2)."</td>
						<td style='width:60px;' align='right'>".number_format($row->Q61_90)."</td>
						<td style='width:100px;' align='right'>".number_format($row->K61_90,2)."</td>
						<td style='width:60px;' align='right'>".number_format($row->Q_91)."</td>
						<td style='width:100px;' align='right'>".number_format($row->K_91,2)."</td>
						<td style='width:60px;' align='right'>".number_format($row->Q_TOT)."</td>
						<td style='width:100px;' align='right'>".number_format($row->K_TOT,2)."</td>
					</tr>
				";	
			}
		}
		
		if($query2->row()){
			foreach($query2->result() as $row){	
				$html .= "
					<tr class='trow bor' style='background-color:#ebebeb;'>
						<td colspan='4' style='text-align:center;vertical-align:middle;'>".$row->Total."</td>
						<td align='right'>".number_format($row->sumQ_no)."</td>
						<td align='right'>".number_format($row->sumQ_0)."</td>
						<td align='right'>".number_format($row->sumQ1_60)."</td>
						<td align='right'>".number_format($row->sumK1_60,2)."</td>
						<td align='right'>".number_format($row->sumQ61_90)."</td>
						<td align='right'>".number_format($row->sumK61_90,2)."</td>
						<td align='right'>".number_format($row->sumQ_91)."</td>
						<td align='right'>".number_format($row->sumK_91,2)."</td>
						<td align='right'>".number_format($row->sumQ_TOT)."</td>
						<td align='right'>".number_format($row->sumK_TOT,2)."</td>
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
			<table class='wf' style='font-size:8.5pt;height:700px;border-collapse:collapse;line-height:23px;overflow:wrap;vertical-align:text-top;'>
				<tbody>
					<tr>
						<th colspan='14' style='font-size:10pt;'>รายงานวิเคราะห์สภาพลูกหนี้ ณ ปัจจุบัน</th>
					</tr>
					<tr>
						<td colspan='14' style='font-size:9pt;height:35px;border-bottom:0.1px solid black;text-align:center;'>".$rpcond."  ลูกหนี้จากวันที่ขาย ".$tx[2]." - ".$tx[3]."</td>
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