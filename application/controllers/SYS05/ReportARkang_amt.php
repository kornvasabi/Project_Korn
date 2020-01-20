<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//BEE+
class ReportARkang_amt extends MY_Controller {
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
							<br>รายงานลูกหนี้เช่าซื้อค้างชำระเบี้ยปรับ<br>
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
								<div class='form-group'>
									เลขที่สัญญา
									<select id='CONTNO1' class='form-control input-sm' data-placeholder='เลขที่สัญญา'></select>
								</div>
							</div>
							<div class='col-sm-4 col-xs-4'>	
								<div class='form-group'>
									สถานะสัญญา
									<select id='CONTSTAT1' class='form-control input-sm' data-placeholder='สถานะสัญญา'></select>
								</div>
							</div>
						</div>
						<div class='col-sm-8 col-xs-8 col-sm-offset-2'><br>	
							<div class='col-sm-12 col-xs-12'>	
								<div class='form-group'>
									ข้อมูลรายงาน
									<div class='col-sm-12 col-xs-12' style='border:0.1px dotted #d6d6d6;'>	
										<div class='col-sm-4 col-xs-4'>
											<div class='form-group'>
												<br>
												<input type= 'radio' id='ar0' name='report' checked> เฉพาะลูกหนี้คงเหลือ = 0
											</div>
										</div>
										<div class='col-sm-4 col-xs-4'>
											<div class='form-group'>
												<br>
												<input type= 'radio' id='armore0' name='report'> เฉพาะยอดคงเหลือ > 0
											</div>
										</div>
										<div class='col-sm-4 col-xs-4'>
											<div class='form-group'>
												<br>
												<input type= 'radio' id='arall' name='report'> ทั้งหมด
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class='col-sm-12 col-xs-12 text-danger' style='text-align:center;'>
									<br><br><< เพื่อความถูกต้องของข้อมูลกรุณาทำการปรับปรุงเบี้ยปรับสำหรับวันนี้ก่อนพิมพ์รายงาน >><br><br>
							</div>
							<div class='col-sm-6 col-xs-6'><br>	
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
							<div class='col-sm-6 col-xs-6'><br>	
								<div class='form-group'>
									เรียงลำดับข้อมูล
									<div class='col-sm-12 col-xs-12' style='border:0.1px dotted #d6d6d6;'>	
										<div class='col-sm-12 col-xs-12'>
											<div class='form-group'>
												<br>
												<input type= 'radio' id='locat' name='orderby' checked> ตามรหัสสาขา
												<br><br>
												<input type= 'radio' id='contno' name='orderby'> ตามเลขที่สัญญา
												<br><br>
												<input type= 'radio' id='cuscod' name='orderby'> ตามรหัสลูกค้า
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
		
		$html.= "<script src='".base_url('public/js/SYS05/ReportARkang_amt.js')."'></script>";
		echo $html;
	}
	
	function search(){
		$LOCAT1		= $_REQUEST["LOCAT1"];
		$CONTNO1	= $_REQUEST["CONTNO1"];
		$CONTSTAT1	= str_replace(chr(0),'',$_REQUEST["CONTSTAT1"]);
		$report 	= $_REQUEST["report"];
		$orderby 	= $_REQUEST["orderby"];
	
		$cond = ""; $rpcond = ""; $datecond = "";
		
		if($LOCAT1 != ""){
			$cond .= " AND (A.LOCAT LIKE '%".$LOCAT1."%')";
			$rpcond .= "  สาขา ".$LOCAT1;
		}
		
		if($CONTNO1 != ""){
			$cond .= " AND (A.CONTNO LIKE '%".$CONTNO1."%')";
			$rpcond .= "  เลขที่สัญญา ".$CONTNO1;
		}
		
		if($CONTSTAT1 != ""){
			$cond .= " AND (A.CONTSTAT LIKE '%".$CONTSTAT1."%')";
			$rpcond .= "  สถานะสัญญา ".$CONTSTAT1;
		}
		
		if($report == "ar0"){
			$cond .= " AND ((A.TOTPRC = A.SMPAY AND A.LPAYD <=convert(nvarchar,GETDATE(),112))) AND A.TOTPRC > 0";
		}else if($report == "armore0"){
			$cond .= " AND (A.TOTPRC > A.SMPAY OR (A.TOTPRC = A.SMPAY AND A.LPAYD > convert(nvarchar,GETDATE(),112))) AND A.TOTPRC > 0";
		}
		
		$sql = "
				IF OBJECT_ID('tempdb..#main') IS NOT NULL DROP TABLE #main
				select *
				into #main
				from(
					select  LOCAT, CONTNO, CUSCOD, SNAM+NAME1+' '+NAME2 as CUSNAME, STRNO, TOTPRC-SMPAY as TOTAR, EXP_AMT, EXP_PRD, EXP_FRM, EXP_TO, 
					sumintamt, sumpayint, sumdscint, sumintamt-sumpayint as totpayint
					from ( 
						select A.LOCAT, A.CONTNO, A.TOTPRC, A.CUSCOD, A.SDATE, C.SNAM, C.NAME1, C.NAME2, A.LPAYD, A.EXP_AMT, A.SMPAY, A.EXP_PRD, 
						A.EXP_FRM, A.EXP_TO, A.STRNO, A.CALINT, convert(nvarchar,GETDATE(),112) as DATE1, nopayfrm, nopayto, 
						(case when sumintamt is null then 0 else sumintamt end) as sumintamt,(case when sumpayint is null then 0 else sumpayint end) as sumpayint, 
						(case when sumdscint is null then 0 else sumdscint end) as sumdscint
						from {$this->MAuth->getdb('ARMAST')} A  
						left outer join (
							select B.CONTNO, B.LOCAT, SUM(B.DELAY) AS DELAY, SUM(B.PAYMENT) AS SUMPAYMENT, SUM(B.DAMT) AS SUMDAMT, sum(b.intamt) as sumintamt,
							min(case when (damt > payment) or (damt = payment and date1 > convert(nvarchar,GETDATE(),112)) then nopay else 1000 end) as nopayfrm, 
							max(nopay) as nopayto  
							from {$this->MAuth->getdb('ARPAY')} B 
							where DDATE <= convert(nvarchar,GETDATE(),112) 
							group by B.CONTNO,B.LOCAT
						) B on A.CONTNO = B.CONTNO AND A.LOCAT = B.LOCAT  
						left outer join {$this->MAuth->getdb('CUSTMAST')} C ON A.CUSCOD=C.CUSCOD 
						left outer join (
							select contno, locatpay,
							sum(case when (payfor = '006' or payfor = '007') then payamt else 0 end) as sumpayamt,
							sum(case when (payfor = '006' or payfor = '007') then payint else 0 end) as sumpayint,
							sum(case when (payfor = '006' or payfor = '007') then dscint else 0 end) as sumdscint  
							from {$this->MAuth->getdb('CHQTRAN')} 
							where flag <> 'C' and paydt <= convert(nvarchar,GETDATE(),112)
							group by contno,locatpay 
						) e on a.contno=e.contno and a.locat=e.locatpay  
						where 1=1 ".$cond."   
					) AS D 
					where sumintamt-sumpayint > 0
				)main
		";//echo $sql; 
		$query = $this->db->query($sql);
		
		$sql = "
				select LOCAT, CONTNO, CUSCOD, CUSNAME, STRNO, TOTAR, EXP_PRD, EXP_FRM, EXP_TO, sumintamt, sumpayint-sumdscint as sumpayint, sumdscint, totpayint
				from #main
				order by ".$orderby."
		";//echo $sql; 
		$query = $this->db->query($sql);
		
		$sql = "
				select 'รวมทั้งหมด'  as Total, sum(TOTAR) as sumTOTAR, sum(EXP_AMT) as sumEXP_AMT, sum(sumintamt) as sumsumintamt, sum(sumpayint-sumdscint) as sumsumpayint,
				sum(sumdscint) as sumsumdscint, sum(totpayint) as sumtotpayint
				from #main
		";
		//echo $sql; exit;
		$query2 = $this->db->query($sql);
		
		$head = ""; $html = ""; $head2 = "";  $report = ""; $sumreport = ""; $sumreport2 = ""; $i = 0; 
		
		$head = "<tr style='height:30px;'>
				<th style='display:none;'>#</th>
				<th style='vertical-align:top;'>สาขา</th>
				<th style='vertical-align:top;'>เลขที่สัญญา</th>
				<th style='vertical-align:top;'>รหัสลูกค้า</th>
				<th style='vertical-align:top;'>ชื่อ - นามสกุล</th>
				<th style='vertical-align:top;text-align:right;'>ลูกหนี้คงเหลือ</th>
				<th style='vertical-align:top;text-align:center;'>จน.งวดค้าง</th>
				<th style='vertical-align:top;text-align:center;'>ค้างงวดที่</th>
				<th style='vertical-align:top;text-align:right;'>เบี้ยปรับทั้งหมด</th>
				<th style='vertical-align:top;text-align:right;'>ชำระแล้ว</th>
				<th style='vertical-align:top;text-align:right;'>ส่วนสด</th>
				<th style='vertical-align:top;text-align:right;'>เบี้ยปรับคงเหลือ</th>
				</tr>
		";
		
		$head2 = "<tr>
					<th style='vertical-align:middle;'>#</th>
					<th style='vertical-align:top;'>สาขา</th>
					<th style='vertical-align:top;'>เลขที่สัญญา</th>
					<th style='vertical-align:top;'>รหัสลูกค้า</th>
					<th style='vertical-align:top;'>ชื่อ - นามสกุล</th>
					<th style='vertical-align:top;text-align:right;'>ลูกหนี้คงเหลือ</th>
					<th style='vertical-align:top;text-align:center;'>จน.งวดค้าง</th>
					<th style='vertical-align:top;text-align:center;'>ค้างงวดที่</th>
					<th style='vertical-align:top;text-align:right;'>เบี้ยปรับทั้งหมด</th>
					<th style='vertical-align:top;text-align:right;'>ชำระแล้ว</th>
					<th style='vertical-align:top;text-align:right;'>ส่วนสด</th>
					<th style='vertical-align:top;text-align:right;'>เบี้ยปรับคงเหลือ</th>
				</tr>
		";
		
		$NRow = 1;
		if($query->row()){
			foreach($query->result() as $row){$i++;
				$html .= "
					<tr class='trow' seq=".$NRow.">
						<td seq=".$NRow++." style='display:none;'></td>
						<td>".$row->LOCAT."</td>
						<td>".$row->CONTNO."</td>
						<td>".$row->CUSCOD."</td>
						<td>".$row->CUSNAME."</td>
						<td align='right'>".number_format($row->TOTAR,2)."</td>
						<td align='center'>".number_format($row->EXP_PRD)."</td>
						<td align='center'>".number_format($row->EXP_FRM).' - '.number_format($row->EXP_TO)."</td>
						<td align='right'>".number_format($row->sumintamt,2)."</td>
						<td align='right'>".number_format($row->sumpayint,2)."</td>
						<td align='right'>".number_format($row->sumdscint,2)."</td>
						<td align='right'>".number_format($row->totpayint,2)."</td>
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
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->TOTAR,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0\";text-align:center;'>".number_format($row->EXP_PRD)."</td>
						<td style='mso-number-format:\"\#\,\#\#0\";text-align:center;'>".number_format($row->EXP_FRM).' - '.number_format($row->EXP_TO)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumintamt,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumpayint,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumdscint,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->totpayint,2)."</td>
					</tr>
				";	
			}
		}
		
		if($query2->row()){
			foreach($query2->result() as $row){
				$sumreport = "
					<tr>
						<th colspan='4' style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;text-align:center;'>".$row->Total."</th>
						<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;text-align:right;'>".number_format($row->sumTOTAR,2)."</th>
						<th colspan='2' style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;'></th>
						<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;text-align:right;'>".number_format($row->sumsumintamt,2)."</th>
						<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;text-align:right;'>".number_format($row->sumsumpayint,2)."</th>
						<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;text-align:right;'>".number_format($row->sumsumdscint,2)."</th>
						<th style='border:0px;text-align:right;'>".number_format($row->sumtotpayint,2)."</th>
					</tr>
				";	
			}
		}
		
		if($query2->row()){
			foreach($query2->result() as $row){
				$sumreport2 = "
					<tr class='trow'>
						<th style='mso-number-format:\"\@\";text-align:center;' colspan='5'>".$row->Total."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumTOTAR,2)."</th>
						<th style='mso-number-format:\"\@\";text-align:center;' colspan='2'></th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumsumintamt,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumsumpayint,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumsumdscint,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumtotpayint,2)."</th>
					</tr>
				";	
			}
		}
		
		if($i>0){
			$html = "
				<div id='table-fixed-ReportARkang_amt' class='col-sm-12' style='height:100%;width:100%;overflow:auto;font-size:8pt;'>
					<table id='table-ReportARkang_amt' style='background-color:white;' class='col-sm-12 display table table-bordered' cellspacing='0' width='calc(100% - 1px)'>
						<thead>
						<tr style='height:40px;'>
							<th colspan='11' style='font-size:12pt;border:0px;text-align:center;'>รายงานลูกหนี้เช่าซื้อค้างชำระเบี้ยปรับ</th>
						</tr>
						<tr style='height:25px;'>
							<td colspan='11' style='border-bottom:1px solid #ddd;text-align:center;'>".$rpcond."  ออกรายงาน ณ วันที่ ".$this->today('today')."</td>
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
			<div id='table-fixed-ReportARkang_amt2' class='col-sm-12' style='height:100%;width:100%;overflow:auto;'>
				<table id='table-ReportARkang_amt2' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='100%'>
					<thead>
						<tr>
							<th colspan='12' style='font-size:12pt;border:0px;text-align:center;'>รายงานลูกหนี้เช่าซื้อค้างชำระเบี้ยปรับ</th>
						</tr>
						<tr>
							<td colspan='12' style='border:0px;text-align:center;'>".$rpcond."  ออกรายงาน ณ วันที่ ".$this->today('today')."</td>
						</tr>
						".$head2."
						<tbody>
						".$report."
						".$sumreport2."
						</tbody>
					</thead>	
				</table>
			</div>
		";
		
		
		$response = array("html"=>$html, "report"=>$report, "reporttoday"=>str_replace('/','-',$this->today('today')));
		echo json_encode($response);
	}
	
	function conditiontopdf(){
		$data 	= 	array();
		$data[] = 	urlencode($_REQUEST["LOCAT1"].'||'.$_REQUEST["CONTNO1"].'||'.$_REQUEST["CONTSTAT1"].'||'.$_REQUEST["report"].'||'.$_REQUEST["orderby"].'||'.$_REQUEST["layout"]);
		echo json_encode($this->generateData($data,"encode"));
	}
	
	function pdf(){
		$data 	= array();
		$data[] = $_GET["condpdf"];
		$arrs 	= $this->generateData($data,"decode");
		$arrs[0]= urldecode($arrs[0]);
		$tx 	= explode("||",$arrs[0]);
		$LOCAT1		= $tx[0];
		$CONTNO1	= $tx[1];
		$CONTSTAT1 	= str_replace(chr(0),'',$tx[2]);
		$report 	= $tx[3];
		$orderby 	= $tx[4];
		$layout 	= $tx[5];
		
		$cond = ""; $rpcond = ""; $datecond = "";
		
		if($LOCAT1 != ""){
			$cond .= " AND (A.LOCAT LIKE '%".$LOCAT1."%')";
			$rpcond .= "  สาขา ".$LOCAT1;
		}
		
		if($CONTNO1 != ""){
			$cond .= " AND (A.CONTNO LIKE '%".$CONTNO1."%')";
			$rpcond .= "  เลขที่สัญญา ".$CONTNO1;
		}
		
		if($CONTSTAT1 != ""){
			$cond .= " AND (A.CONTSTAT LIKE '%".$CONTSTAT1."%')";
			$rpcond .= "  สถานะสัญญา ".$CONTSTAT1;
		}
		
		if($report == "ar0"){
			$cond .= " AND ((A.TOTPRC = A.SMPAY AND A.LPAYD <=convert(nvarchar,GETDATE(),112))) AND A.TOTPRC > 0";
		}else if($report == "armore0"){
			$cond .= " AND (A.TOTPRC > A.SMPAY OR (A.TOTPRC = A.SMPAY AND A.LPAYD > convert(nvarchar,GETDATE(),112))) AND A.TOTPRC > 0";
		}
		
		$sql = "
				IF OBJECT_ID('tempdb..#main') IS NOT NULL DROP TABLE #main
				select *
				into #main
				from(
					select  LOCAT, CONTNO, CUSCOD, SNAM+NAME1+' '+NAME2 as CUSNAME, STRNO, TOTPRC-SMPAY as TOTAR, EXP_AMT, EXP_PRD, EXP_FRM, EXP_TO, 
					sumintamt, sumpayint, sumdscint, sumintamt-sumpayint as totpayint
					from ( 
						select A.LOCAT, A.CONTNO, A.TOTPRC, A.CUSCOD, A.SDATE, C.SNAM, C.NAME1, C.NAME2, A.LPAYD, A.EXP_AMT, A.SMPAY, A.EXP_PRD, 
						A.EXP_FRM, A.EXP_TO, A.STRNO, A.CALINT, convert(nvarchar,GETDATE(),112) as DATE1, nopayfrm, nopayto, 
						(case when sumintamt is null then 0 else sumintamt end) as sumintamt,(case when sumpayint is null then 0 else sumpayint end) as sumpayint, 
						(case when sumdscint is null then 0 else sumdscint end) as sumdscint
						from {$this->MAuth->getdb('ARMAST')} A  
						left outer join (
							select B.CONTNO, B.LOCAT, SUM(B.DELAY) AS DELAY, SUM(B.PAYMENT) AS SUMPAYMENT, SUM(B.DAMT) AS SUMDAMT, sum(b.intamt) as sumintamt,
							min(case when (damt > payment) or (damt = payment and date1 > convert(nvarchar,GETDATE(),112)) then nopay else 1000 end) as nopayfrm, 
							max(nopay) as nopayto  
							from {$this->MAuth->getdb('ARPAY')} B 
							where DDATE <= convert(nvarchar,GETDATE(),112) 
							group by B.CONTNO,B.LOCAT
						) B on A.CONTNO = B.CONTNO AND A.LOCAT = B.LOCAT  
						left outer join {$this->MAuth->getdb('CUSTMAST')} C ON A.CUSCOD=C.CUSCOD 
						left outer join (
							select contno, locatpay,
							sum(case when (payfor = '006' or payfor = '007') then payamt else 0 end) as sumpayamt,
							sum(case when (payfor = '006' or payfor = '007') then payint else 0 end) as sumpayint,
							sum(case when (payfor = '006' or payfor = '007') then dscint else 0 end) as sumdscint  
							from {$this->MAuth->getdb('CHQTRAN')} 
							where flag <> 'C' and paydt <= convert(nvarchar,GETDATE(),112)
							group by contno,locatpay 
						) e on a.contno=e.contno and a.locat=e.locatpay  
						where 1=1 ".$cond."   
					) AS D 
					where sumintamt-sumpayint > 0
				)main
		";//echo $sql; 
		$query = $this->db->query($sql);
		
		$sql = "
				select LOCAT, CONTNO, CUSCOD, CUSNAME, STRNO, TOTAR, EXP_PRD, EXP_FRM, EXP_TO, sumintamt, sumpayint-sumdscint as sumpayint, sumdscint, totpayint
				from #main
				order by ".$orderby."
		";//echo $sql; 
		$query = $this->db->query($sql);
		
		$sql = "
				select 'รวมทั้งหมด'  as Total, sum(TOTAR) as sumTOTAR, sum(EXP_AMT) as sumEXP_AMT, sum(sumintamt) as sumsumintamt, sum(sumpayint-sumdscint) as sumsumpayint,
				sum(sumdscint) as sumsumdscint, sum(totpayint) as sumtotpayint
				from #main
		";
		//echo $sql; exit;
		$query2 = $this->db->query($sql);
		
		$head = ""; $html = ""; $i=0; 

		$head = "
				<tr>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>#</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>สาขา</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>เลขที่สัญญา</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>รหัสลูกค้า</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>ชื่อ - นามสกุล</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>ลูกหนี้คงเหลือ</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:center;'>จน.งวดค้าง</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:center;'>ค้างงวดที่</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>เบี้ยปรับทั้งหมด</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>ชำระแล้ว</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>ส่วนสด</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>เบี้ยปรับคงเหลือ</th>
				</tr>
		";
		
		$No = 1;
		if($query->row()){
			foreach($query->result() as $row){	
				$html .= "
					<tr class='trow' seq=".$No.">
						<td style='width:25px;'>".$No++."</td>
						<td style='width:50px;'>".$row->LOCAT."</td>
						<td style='width:80px;'>".$row->CONTNO."</td>
						<td style='width:80px;'>".$row->CUSCOD."</td>
						<td style='width:160px;'>".$row->CUSNAME."</td>
						<td style='width:80px;' align='right'>".number_format($row->TOTAR,2)."</td>
						<td style='width:80px;' align='center'>".number_format($row->EXP_PRD,2)."</td>
						<td style='width:80px;' align='center'>".number_format($row->EXP_FRM).' - '.number_format($row->EXP_TO)."</td>
						<td style='width:80px;' align='right'>".number_format($row->sumintamt,2)."</td>
						<td style='width:80px;' align='right'>".number_format($row->sumpayint,2)."</td>
						<td style='width:80px;' align='right'>".number_format($row->sumdscint,2)."</td>
						<td style='width:80px;' align='right'>".number_format($row->totpayint,2)."</td>
					</tr>
				";	
			}
		}
		
		if($query2->row()){
			foreach($query2->result() as $row){	
				$html .= "
					<tr class='trow bor' style='background-color:#ebebeb;'>
						<td colspan='5' style='text-align:center;vertical-align:middle;'>".$row->Total."</td>
						<td align='right'>".number_format($row->sumTOTPRC,2)."</td>
						<td colspan='2'></td>
						<td align='right'>".number_format($row->sumsumintamt,2)."</td>
						<td align='right'>".number_format($row->sumsumpayint,2)."</td>
						<td align='right'>".number_format($row->sumsumdscint,2)."</td>
						<td align='right'>".number_format($row->sumtotpayint,2)."</td>
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
						<th colspan='12' style='font-size:10pt;'>รายงานลูกหนี้เช่าซื้อค้างชำระเบี้ยปรับ</th>
					</tr>
					<tr>
						<td colspan='12' style='font-size:8pt;height:35px;border-bottom:0.1px solid black;text-align:center;'>".$rpcond." ณ วันที่ ".$this->today('today')."</td>
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