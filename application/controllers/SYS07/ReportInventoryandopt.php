<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//BEE+
class ReportInventoryandopt extends MY_Controller {
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
							<br>รายงานสินค้าและวัตถุดิบ (อุปกรณ์)<br>
						</div>
						<div class='col-sm-8 col-xs-8 col-sm-offset-2'>
							<br>
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group'>
									สาขา
									<select id='LOCAT1' class='form-control input-sm' data-placeholder='สถานที่รับรถ'></select>
								</div>
							</div>
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group' >
									รหัสอุปกรณ์
									<select id='OPTCOCE1' class='form-control input-sm' data-placeholder='รหัสอุปกรณ์'></select>
								</div>
							</div>
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group'>
									รายงานจากวันที่
									<input type='text' id='FRMDATE' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' placeholder='ณ วันที่' value='".$this->today('today')."' style='font-size:10.5pt'>
								</div>
							</div>
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group'>
									ถึงวันที่
									<input type='text' id='TODATE' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' placeholder='ณ วันที่' value='".$this->today('today')."' style='font-size:10.5pt'>
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
												<br>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group'>
									รายงาน
									<div class='col-sm-12 col-xs-12' style='border:0.1px dotted #d6d6d6;'>	
										<div class='col-sm-12 col-xs-12'>
											<div class='form-group'>
												<br>
												<input type= 'radio' id='notsumtran' name='report' checked> ไม่รวมการโอนย้าย
												<br><br>
												<input type= 'radio' id='sumtran' name='report'> รวมการโอนย้าย
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
		
		$html.= "<script src='".base_url('public/js/SYS07/ReportInventoryandopt.js')."'></script>";
		echo $html;
	}
	
	function search(){
		$LOCAT1		= $_REQUEST["LOCAT1"];
		$OPTCOCE1 	= str_replace(chr(0),'',$_REQUEST["OPTCOCE1"]);
		$FRMDATE 	= $this->Convertdate(1,$_REQUEST["FRMDATE"]);
		$TODATE 	= $this->Convertdate(1,$_REQUEST["TODATE"]);
		$reports 	= $_REQUEST["report"];
	
		$cond = ""; $rpcond = "";
		
		if($LOCAT1 != ""){
			$rpcond .= " สาขา ".$LOCAT1."";
		}
		
		if($OPTCOCE1 != ""){
			$cond .= " and (a.OPTCODE = '".$OPTCOCE1."')";
			$rpcond .= " รหัสอุปกรณ์ ".$OPTCOCE1."";
		}
		
		if($reports == "sumtran"){
			$reports = "
				union all
				select  a.MOVEFM, a.OPTCODE, a.MOVENO, a.MOVEDT, 0 as RV, a.MOVQTY as PV
				from {$this->MAuth->getdb('OPTMOVT')} a 
				where a.MOVEFM like '".$LOCAT1."%' ".$cond."
			";
		}else{
			$reports = "";
		}
		
		$sql = "
				IF OBJECT_ID('tempdb..#stock') IS NOT NULL DROP TABLE #stock
				select *
				into #stock
				from(
					select  a.RVLOCAT as LOCAT, a.OPTCODE, a.RECVNO, a.RECVDT, a.QTY as RV, 0 as PV
					from {$this->MAuth->getdb('OPTTRAN')} a 
					left join {$this->MAuth->getdb('OPTINV')} b on a.RECVNO = b.RECVNO
					where a.RVLOCAT like '".$LOCAT1."%' ".$cond."
					union all
					select LOCAT, OPTCODE, CONTNO, SDATE, 0 as RV, QTY as PV
					from {$this->MAuth->getdb('ARINOPT')} a
					where a.LOCAT like '".$LOCAT1."%' ".$cond."
					".$reports."
				)stock	
		";//echo $sql; 
		$query = $this->db->query($sql);
		
		$sql = "
				/* จัดลำดับ */
				IF OBJECT_ID('tempdb..#table1') IS NOT NULL DROP TABLE #table1
				select *
				into #table1
				from(
					select row_number() over(order by LOCAT, OPTCODE) as seq, LOCAT, OPTCODE
					from(
						select distinct LOCAT, OPTCODE 
						from #stock
						where RECVDT <= '".$TODATE."' and OPTCODE is not null
					)A
				)table1
		";//echo $sql;
		$query = $this->db->query($sql);
		
		$sql = "
				/* ยอดยกมา */
				IF OBJECT_ID('tempdb..#table2') IS NOT NULL DROP TABLE #table2
				select *
				into #table2
				from(
					select LOCAT, OPTCODE, SUM(RV) as qtyin, SUM(PV) as qtyout, SUM(RV) - SUM(PV) total 
					from #stock
					where RECVDT < '".$FRMDATE."'
					group by LOCAT, OPTCODE	
				)table2
		";//echo $sql;
		$query = $this->db->query($sql);
		
		$sql = "
				/* สินค้าหมุนเวียน */
				IF OBJECT_ID('tempdb..#table3') IS NOT NULL DROP TABLE #table3
				select *
				into #table3
				from(
					select LOCAT, OPTCODE, RECVNO, RECVDT, RV as qtyin, PV as qtyout, RV-PV as total
					from #stock
					where RECVDT between '".$FRMDATE."' and  '".$TODATE."'
				)table3
		";//echo $sql;
		$query = $this->db->query($sql);
		
		$sql = "
				/* ยอดยกไป */
				IF OBJECT_ID('tempdb..#table4') IS NOT NULL DROP TABLE #table4
				select *
				into #table4
				from(
					select LOCAT, OPTCODE, SUM(RV) as qtyin, SUM(PV) as qtyout, SUM(RV) - SUM(PV) total 
					from #stock
					where RECVDT <= '".$TODATE."'
					group by LOCAT, OPTCODE	
				)table4
		";//echo $sql;
		$query = $this->db->query($sql);
		
		$sql = "
				select seq, ffm, optcode, locat, optcode, optname, qtyin, qtyout, total 
				from (
					select a.seq, 1 ffm, b.locat, a.optcode, c.optname, b.qtyin, b.qtyout, b.total 
					from #table1 a
					left join #table2 b on a.locat=b.locat and a.optcode=b.optcode
					left join HIC2TAX.dbo.OPTMAST c on a.locat=c.locat collate thai_cs_as and a.optcode=c.optcode collate thai_cs_as
					union all
					select a.seq, 2 ffm, b.recvno, '' optcode, convert(char,dateadd(year,543,recvdt),103), b.qtyin, b.qtyout, b.total
					from #table1 a
					left join #table3 b on a.locat=b.locat and a.optcode=b.optcode
					where b.locat is not null and b.optcode is not null and b.recvno is not null and b.total is not null
					union all
					select a.seq, 3 ffm, NULL, NULL, NULL, b.qtyin, b.qtyout, b.total
					from #table1 a
					left join #table4 b on a.locat=b.locat and a.optcode=b.optcode
				)a
				order by seq, ffm, optname, qtyin desc
		";//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$sql = "
				select SUM(RV) - SUM(PV) as sumTotal from #stock where RECVDT <= '".$TODATE."'
		";//echo $sql; exit;
		$query2 = $this->db->query($sql);
		
		$head = ""; $html = ""; $head2 = "";  $report = ""; $sumreport = ""; $sumreport2 = ""; $i = 0; 
		
		$head = "<tr style='height:25px;background-color:#D3ECDC;'>
				<th style='vertical-align:top;text-align:center;'>ลำดับ</th>
				<th style='vertical-align:top;'>สาขา<br>ใบสำคัญเลขที่<br></th>
				<th style='vertical-align:top;'>รหัสอุปกรณ์</th>
				<th style='vertical-align:top;'>ชื่ออุปกรณ์<br>วันเดือนปี</th>
				<th style='vertical-align:top;text-align:right;'>จำนวนรับ</th>
				<th style='vertical-align:top;text-align:right;'>จำนวนจ่าย</th>
				<th style='vertical-align:top;text-align:right;'></th>
				<th style='vertical-align:top;text-align:right;'>ยอดคงเหลือ</th>
				</tr>
		";
		
		$NRow = 0; $data_sum = array();
		if($query->row()){
			foreach($query->result() as $row){$i++;
			
				if($row->ffm == 2 and $row->locat == ""){ continue; }
				if($row->ffm == 1){
					$data_sum["turnover"] = $row->total;
				}else if($row->ffm == 3){
					$data_sum["turnover"] = $row->total;
					$data_sum["turnoverSum"] = (isset($data_sum["turnoverSum"]) ? $data_sum["turnoverSum"] : 0) + $data_sum["turnover"];
				}else{
					$data_sum["turnover"] = $data_sum["turnover"] + $row->total;
				}
				
				$html .= "
					<tr>
						<td ".($row->ffm == 3 ? "style='border-bottom:0.1px solid #ddd;'" : "")." align='center'>".($row->ffm == 1 ? ++$NRow : "")."</td>
						<td ".($row->ffm == 3 ? "style='border-bottom:0.1px solid #ddd;'" : "").">".$row->locat."</td>
						<td ".($row->ffm == 3 ? "style='border-bottom:0.1px solid #ddd;'" : "").">".$row->optcode."</td>
						<td ".($row->ffm == 3 ? "style='border-bottom:0.1px solid #ddd;'" : "").">".$row->optname."</td>
						<td ".($row->ffm == 3 ? "style='border-bottom:0.1px solid #ddd;'" : "")." align='right'>".($row->ffm == 2 ? (number_format($row->qtyin) == 0 ? "" :number_format($row->qtyin)) : "")."</td>
						<td ".($row->ffm == 3 ? "style='border-bottom:0.1px solid #ddd;'" : "")." align='right'>".($row->ffm == 2 ? (number_format($row->qtyout) == 0 ? "" :number_format($row->qtyout)) : "")."</td>
						<td ".($row->ffm == 3 ? "style='border-bottom:0.1px solid #ddd;'" : "")." align='right'>".($row->ffm == 2 ? "":($row->ffm == 1 ? "ยอดยกมา":"ยอดยกไป"))."</td>
						<td ".($row->ffm == 3 ? "style='border-bottom:0.1px solid #ddd;'" : "")." align='right'>".$data_sum["turnover"]."</td>
					</tr>
				";	
			}
		}
		
		$head2 = "<tr>
					<th style='vertical-align:top;text-align:center;'>ลำดับ</th>
					<th style='vertical-align:top;text-align:left;'>สาขา<br>ใบสำคัญเลขที่<br></th>
					<th style='vertical-align:top;text-align:left;'>รหัสอุปกรณ์</th>
					<th style='vertical-align:top;text-align:left;'>ชื่ออุปกรณ์<br>วันเดือนปี</th>
					<th style='vertical-align:top;text-align:right;'>จำนวนรับ</th>
					<th style='vertical-align:top;text-align:right;'>จำนวนจ่าย</th>
					<th style='vertical-align:top;text-align:right;'></th>
					<th style='vertical-align:top;text-align:right;'>ยอดคงเหลือ</th>
				</tr>
		";
		
		$No = 0;
		if($query->row()){
			foreach($query->result() as $row){
				
				if($row->ffm == 2 and $row->locat == ""){ continue; }
				if($row->ffm == 1){
					$data_sum["turnover"] = $row->total;
				}else if($row->ffm == 3){
					$data_sum["turnover"] = $row->total;
					$data_sum["turnoverSum"] = (isset($data_sum["turnoverSum"]) ? $data_sum["turnoverSum"] : 0) + $data_sum["turnover"];
				}else{
					$data_sum["turnover"] = $data_sum["turnover"] + $row->total;
				}
				
				$report .= "
					<tr class='trow' >
						<td style='mso-number-format:\"\@\";'>".($row->ffm == 1 ? ++$No : "")."</td>
						<td style='mso-number-format:\"\@\";'>".$row->locat."</td>
						<td style='mso-number-format:\"\@\";'>".$row->optcode."</td>
						<td style='mso-number-format:\"\@\";'>".$row->optname."</td>
						<td style='mso-number-format:\"\#\,\#\#0\";text-align:right;'>".($row->ffm == 2 ? (number_format($row->qtyin) == 0 ? "" :number_format($row->qtyin)) : "")."</td>
						<td style='mso-number-format:\"\#\,\#\#0\";text-align:right;'>".($row->ffm == 2 ? (number_format($row->qtyout) == 0 ? "" :number_format($row->qtyout)) : "")."</td>
						<td style='mso-number-format:\"\@\";text-align:right;'>".($row->ffm == 2 ? "":($row->ffm == 1 ? "ยอดยกมา":"ยอดยกไป"))."</td>
						<td style='mso-number-format:\"\#\,\#\#0\";text-align:right;'>".$data_sum["turnover"]."</td>
					</tr>
				";	
			}
		}
		
		if($query2->row()){
			foreach($query2->result() as $row){
				$sumreport = "
					<tr style='height:25px;background-color:#D3ECDC;'>
						<th></th>
						<th colspan='5' style='vertical-align:middle;text-align:left;'>รวมทั้งสิ้น ".number_format($NRow)." รายการ</th>
						<th style='vertical-align:middle;text-align:right;'>รวมยอดยกไป</th>
						<th style='vertical-align:middle;text-align:right;'>".number_format($row->sumTotal)."</th>
					</tr>
				";	
			}
		}
		
		if($query2->row()){
			foreach($query2->result() as $row){
				$sumreport2 = "
					<tr class='trow'>
						<th></th>
						<th style='mso-number-format:\"\@\";text-align:left;' colspan='5'>รวมทั้งสิ้น ".$No." รายการ</th>
						<th style='mso-number-format:\"\@\";text-align:right;'>รวมยอดยกไป</th>
						<th style='mso-number-format:\"\#\,\#\#0\";text-align:right;'>".number_format($row->sumTotal)."</th>
					</tr>
				";	
			}
		}
		
		if($i>0){
			$html = "
				<div id='table-fixed-ReportInventoryandopt' class='col-sm-12' style='height:100%;width:100%;overflow:auto;font-size:9pt;'>
					<table id='table-ReportInventoryandopt' style='background-color:white;' class='col-sm-12 display' cellspacing='0' width='calc(100% - 1px)'>
						<thead>
						<tr style='background-color:white;'>
							<th colspan='8' id='H_ReportInventoryandopt' style='padding:10px;'></th>
						</tr>
						<tr style='background-color:white;height:40px;'>
							<th colspan='8' style='font-size:12pt;border:0px;vertical-align;middle;text-align:center;'>รายงานสินค้าและวัตถุดิบ (อุปกรณ์)</th>
						</tr>
						<tr style='background-color:white;height:25px;'>
							<td colspan='8' style='border-bottom:1px solid #ddd;vertical-align;middle;text-align:center;'>".$rpcond." ระหว่างวันที่  ".$_REQUEST["FRMDATE"]." ถึงวันที่ ".$_REQUEST["TODATE"]." ".$rpcond."  ออกรายงาน ณ วันที่ ".$this->today('today')."</td>
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
			<div id='table-fixed-ReportInventoryandopt2' class='col-sm-12' style='height:100%;width:100%;overflow:auto;'>
				<table id='table-ReportInventoryandopt2' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='100%'>
					<thead>
						<tr>
							<th colspan='8' style='font-size:12pt;border:0px;text-align:center;'>รายงานสินค้าและวัตถุดิบ (อุปกรณ์)</th>
						</tr>
						<tr>
							<td colspan='8' style='border:0px;text-align:center;'>".$rpcond." ระหว่างวันที่  ".$_REQUEST["FRMDATE"]." ถึงวันที่ ".$_REQUEST["TODATE"]." ".$rpcond."  ออกรายงาน ณ วันที่ ".$this->today('today')."</td>
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
						$_REQUEST["OPTCOCE1"].'||'.
						$_REQUEST["FRMDATE"].'||'.
						$_REQUEST["TODATE"].'||'.
						$_REQUEST["report"].'||'.
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
		$OPTCOCE1 	= str_replace(chr(0),'',$tx[1]);
		$FRMDATE 	= $this->Convertdate(1,$tx[2]);
		$TODATE 	= $this->Convertdate(1,$tx[3]);
		$report 	= $tx[4];
		$layout 	= $tx[5];
		
		$cond = ""; $rpcond = "";
		
		if($LOCAT1 != ""){
			$rpcond .= " สาขา ".$LOCAT1."";
		}
		
		if($OPTCOCE1 != ""){
			$cond .= " and (a.OPTCODE = '".$OPTCOCE1."')";
			$rpcond .= " รหัสอุปกรณ์ ".$OPTCOCE1."";
		}
		
		if($reports == "sumtran"){
			$reports = "
				union all
				select  a.MOVEFM, a.OPTCODE, a.MOVENO, a.MOVEDT, 0 as RV, a.MOVQTY as PV
				from {$this->MAuth->getdb('OPTMOVT')} a 
				where a.MOVEFM like '".$LOCAT1."%' ".$cond."
			";
		}else{
			$reports = "";
		}
		
		$sql = "
				IF OBJECT_ID('tempdb..#stock') IS NOT NULL DROP TABLE #stock
				select *
				into #stock
				from(
					select  a.RVLOCAT as LOCAT, a.OPTCODE, a.RECVNO, a.RECVDT, a.QTY as RV, 0 as PV
					from {$this->MAuth->getdb('OPTTRAN')} a 
					left join {$this->MAuth->getdb('OPTINV')} b on a.RECVNO = b.RECVNO
					where a.RVLOCAT like '".$LOCAT1."%' ".$cond."
					union all
					select LOCAT, OPTCODE, CONTNO, SDATE, 0 as RV, QTY as PV
					from {$this->MAuth->getdb('ARINOPT')} a
					where a.LOCAT like '".$LOCAT1."%' ".$cond."
					".$reports."
				)stock	
		";//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$sql = "
				/* จัดลำดับ */
				IF OBJECT_ID('tempdb..#table1') IS NOT NULL DROP TABLE #table1
				select *
				into #table1
				from(
					select row_number() over(order by LOCAT, OPTCODE) as seq, LOCAT, OPTCODE
					from(
						select distinct LOCAT, OPTCODE 
						from #stock
						where RECVDT <= '".$TODATE."' and OPTCODE is not null
					)A
				)table1
		";//echo $sql;
		$query = $this->db->query($sql);
		
		$sql = "
				/* ยอดยกมา */
				IF OBJECT_ID('tempdb..#table2') IS NOT NULL DROP TABLE #table2
				select *
				into #table2
				from(
					select LOCAT, OPTCODE, SUM(RV) as qtyin, SUM(PV) as qtyout, SUM(RV) - SUM(PV) total 
					from #stock
					where RECVDT < '".$FRMDATE."'
					group by LOCAT, OPTCODE	
				)table2
		";//echo $sql;
		$query = $this->db->query($sql);
		
		$sql = "
				/* สินค้าหมุนเวียน */
				IF OBJECT_ID('tempdb..#table3') IS NOT NULL DROP TABLE #table3
				select *
				into #table3
				from(
					select LOCAT, OPTCODE, RECVNO, RECVDT, RV as qtyin, PV as qtyout, RV-PV as total
					from #stock
					where RECVDT between '".$FRMDATE."' and  '".$TODATE."'
				)table3
		";//echo $sql;
		$query = $this->db->query($sql);
		
		$sql = "
				/* ยอดยกไป */
				IF OBJECT_ID('tempdb..#table4') IS NOT NULL DROP TABLE #table4
				select *
				into #table4
				from(
					select LOCAT, OPTCODE, SUM(RV) as qtyin, SUM(PV) as qtyout, SUM(RV) - SUM(PV) total 
					from #stock
					where RECVDT <= '".$TODATE."'
					group by LOCAT, OPTCODE	
				)table4
		";//echo $sql;
		$query = $this->db->query($sql);
		
		$sql = "
				select seq, ffm, optcode, locat, optcode, optname, qtyin, qtyout, total 
				from (
					select a.seq, 1 ffm, b.locat, a.optcode, c.optname, b.qtyin, b.qtyout, b.total 
					from #table1 a
					left join #table2 b on a.locat=b.locat and a.optcode=b.optcode
					left join HIC2TAX.dbo.OPTMAST c on a.locat=c.locat collate thai_cs_as and a.optcode=c.optcode collate thai_cs_as
					union all
					select a.seq, 2 ffm, b.recvno, '' optcode, convert(char,dateadd(year,543,recvdt),103), b.qtyin, b.qtyout, b.total
					from #table1 a
					left join #table3 b on a.locat=b.locat and a.optcode=b.optcode
					where b.locat is not null and b.optcode is not null and b.recvno is not null and b.total is not null
					union all
					select a.seq, 3 ffm, NULL, NULL, NULL, b.qtyin, b.qtyout, b.total
					from #table1 a
					left join #table4 b on a.locat=b.locat and a.optcode=b.optcode
				)a
				order by seq, ffm, optname, qtyin desc
		";//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$sql = "
				select SUM(RV) - SUM(PV) as sumTotal from #stock where RECVDT <= '".$TODATE."'
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
					<th width='50px' 	align='left'	style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;'>ลำดับ</th>
					<th width='130px' 	align='left'	style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;'>สาขา<br>ใบสำคัญเลขที่<br></th>
					<th width='130px' 	align='left'	style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;'>รหัสอุปกรณ์</th>
					<th width='240px' 	align='left'	style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;'>ชื่ออุปกรณ์<br>วันเดือนปี</th>
					<th width='120px' 	align='right'	style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;'>จำนวนรับ</th>
					<th width='120px' 	align='right'	style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;'>จำนวนจ่าย</th>
					<th width='100px' 	align='right'	style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;'></th>
					<th width='120px' 	align='right'	style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;'>ยอดคงเหลือ</th>
				</tr>
		";
		
		$No = 0;
		if($query->row()){
			foreach($query->result() as $row){	
				if($row->ffm == 2 and $row->locat == ""){ continue; }
				if($row->ffm == 1){
					$data_sum["turnover"] = $row->total;
				}else if($row->ffm == 3){
					$data_sum["turnover"] = $row->total;
					$data_sum["turnoverSum"] = (isset($data_sum["turnoverSum"]) ? $data_sum["turnoverSum"] : 0) + $data_sum["turnover"];
				}else{
					$data_sum["turnover"] = $data_sum["turnover"] + $row->total;
				}
				
				$html .= "
					<tr class='trow' seq=".$No.">
						<td width='50px'	height='25px'	align='left'	>".($row->ffm == 1 ? ++$No : "")."</td>
						<td width='130px'	height='25px'	align='left'	>".$row->locat."</td>
						<td width='130px'	height='25px'	align='left'	>".$row->optcode."</td>
						<td width='240px'	height='25px'	align='left'	>".$row->optname."</td>
						<td width='120px'	height='25px'	align='right'	>".($row->ffm == 2 ? (number_format($row->qtyin) == 0 ? "" :number_format($row->qtyin)) : "")."</td>
						<td width='120px'	height='25px'	align='right'	>".($row->ffm == 2 ? (number_format($row->qtyout) == 0 ? "" :number_format($row->qtyout)) : "")."</td>
						<td width='100px'	height='25px'	align='right'	>".($row->ffm == 2 ? "":($row->ffm == 1 ? "ยอดยกมา":"ยอดยกไป"))."</td>
						<td width='120px'	height='25px'	align='right'	>".$data_sum["turnover"]."</td>
					</tr>
				";	
			}
		}
		
		if($query2->row()){
			foreach($query2->result() as $row){	
				$html .= "
					<tr class='trow' style='background-color:#ebebeb;'>
						<th class='bor' style='border-left:0.1px solid black;'></th>
						<th class='bor' style='text-align:left;vertical-align:middle;' colspan='5'>รวมทั้งสิ้น ".number_format($No)." รายการ</th>
						<th class='bor' style='text-align:right;vertical-align:middle;'>รวมยอดยกไป</th>
						<th class='bor' style='border-right:0.1px solid black;text-align:right;vertical-align:middle;'>".number_format($row->sumTotal)."</th>
					</tr>
					
				";	
			}
		}
		$body = "<table class='fs9' cellspacing='0'>".$html."</table>";
		
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
				.fs9 { font-size:9pt; }
				.h30 { height:30px; }
				.bor { border-top:0.1px solid black;border-bottom:0.1px solid black;}
			</style>
		";
		
		$header = "
			<table class='wf fs9' cellspacing='0' style='border-collapse:collapse;line-height:23px;overflow:wrap;vertical-align:text-top;'>
				<tr>
					<th colspan='8' style='font-size:11pt;'>".$COMP_NM."<br>รายงานสินค้าและวัตถุดิบ (อุปกรณ์)</th>
				</tr>
				<tr>
					<td colspan='8' style='height:35px;text-align:center;'>".$rpcond." ระหว่างวันที่ ".$tx[2]." ถึงวันที่ ".$tx[3]."</td>
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
					<td colspan='9' align='left'>".$TAXID."</td>
				</tr>
				<tr>
					<td colspan='2' align='left'>วันที่พิมพ์รายงาน</td>
					<td colspan='5' align='left'>".date('d/m/').(date('Y')+543)." ".date('H:i')."</td>
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