<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//BEE+
class ReportHoldtoOldcar extends MY_Controller {
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
							<br>รายงานรถยึดเปลี่ยนเป็นรถเก่า<br>
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
								<div class='form-group' >
									วันที่ยึดรถ
									<input type='text' id='FROMDATEHOLD' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' placeholder='จากวันที่' value='".$this->today('today')."' style='font-size:10.5pt'>
								</div>
							</div>
							<div class='col-sm-4 col-xs-4'>	
								<div class='form-group'>
									ถึงวันที่
									<input type='text' id='TODATEHOLD' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' placeholder='ถึงวันที่' value='".$this->today('today')."' style='font-size:10.5pt'>
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
									พนักงานที่ยึด
									<select id='Y_USER1' class='form-control input-sm' data-placeholder='พนักงานที่ยึด'></select>
								</div>
							</div>
							<div class='col-sm-4 col-xs-4'>	
								<div class='form-group'>
									เลขตัวถัง
									<input type='text' id='STRNO1' class='form-control input-sm' placeholder='เลขตัวถัง' style='font-size:10.5pt' >
								</div>
							</div>
						</div>
						<div class='col-sm-8 col-xs-8 col-sm-offset-2'>	
							<div class='col-sm-12 col-xs-12'>	
								<div class='form-group'>
									รายงาน
									<div class='col-sm-12 col-xs-12' style='border:0.1px dotted #d6d6d6;'>	
										<div class='col-sm-3 col-xs-3'>
											<div>
												<input type= 'radio' id='SYD' name='rpt' style='margin:10px;'> SYD
											</div>
										</div>
										<div class='col-sm-3 col-xs-3'>
											<div >
												<input type= 'radio' id='STR' name='rpt' style='margin:10px;' checked> STR
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class='col-sm-8 col-xs-8 col-sm-offset-2'><br>	
							<div class='col-sm-12 col-xs-12'>	
								<div class='form-group'>
									รูปแบบการพิมพ์
									<div class='col-sm-12 col-xs-12' style='border:0.1px dotted #d6d6d6;'>	
										<div class='col-sm-3 col-xs-3'>
											<div>
												<input type= 'radio' id='ver' name='layout' style='margin:10px;'> แนวตั้ง
											</div>
										</div>
										<div class='col-sm-3 col-xs-3'>
											<div >
												<input type= 'radio' id='hor' name='layout' style='margin:10px;' checked> แนวนอน
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class='col-sm-8 col-xs-8 col-sm-offset-2'><br>	
							<div class='col-sm-12 col-xs-12'>	
								<div class='form-group'>
									เงือนไขวันที่
									<div class='col-sm-12 col-xs-12' style='border:0.1px dotted #d6d6d6;'>	
										<div class='col-sm-3 col-xs-3'>
											<div>
												<input type= 'radio' id='c_ydate' name='conddate' style='margin:10px;' checked> ตามวันที่เปลี่ยนเป็นรถเก่า
											</div>
										</div>
										<div class='col-sm-3 col-xs-3'>
											<div >
												<input type= 'radio' id='c_sdate' name='conddate' style='margin:10px;'> ตามวันที่ขาย
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class='col-sm-8 col-xs-8 col-sm-offset-2'><br>	
							<div class='col-sm-12 col-xs-12'>	
								<div class='form-group'>
									เรียงลำดับข้อมูล
									<div class='col-sm-12 col-xs-12' style='border:0.1px dotted #d6d6d6;'>	
										<div class='col-sm-3 col-xs-3'>
											<div>
												<input type= 'radio' id='strno' name='orderby' style='margin:10px;'> เลขตัวถัง
											</div>
										</div>
										<div class='col-sm-3 col-xs-3'>
											<div >
												<input type= 'radio' id='contno' name='orderby' style='margin:10px;'> เลขที่สัญญา
											</div>
										</div>
										<div class='col-sm-3 col-xs-3'>
											<div>
												<input type= 'radio' id='ydate' name='orderby' style='margin:10px;' checked> วันที่เปลี่ยนสภาพ
											</div>
										</div>
										<div class='col-sm-3 col-xs-3'>
											<div >
												<input type= 'radio' id='sdate' name='orderby' style='margin:10px;'> วันที่ขาย
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class='row' style='height:10%;'>
						<div class='col-sm-12 col-xs-12'><br>		
							<button id='btnt1search' class='btn btn-primary btn-sm' style='width:100%'><span class='glyphicon glyphicon-search'> แสดง</span></button>
						</div>
					</div>
				</div>
			</div>
		";
		
		$html.= "<script src='".base_url('public/js/SYS05/ReportHoldtoOldcar.js')."'></script>";
		echo $html;
	}
	
	function search(){
		$LOCAT1			= $_REQUEST["LOCAT1"];
		$FROMDATEHOLD 	= $_REQUEST["FROMDATEHOLD"];
		$TODATEHOLD 	= $_REQUEST["TODATEHOLD"];
		$CONTNO1 		= $_REQUEST["CONTNO1"];
		$Y_USER1 		= $_REQUEST["Y_USER1"];
		$STRNO1 		= $_REQUEST["STRNO1"];
		$calcul 		= $_REQUEST["calcul"];
		$conddate 		= $_REQUEST["conddate"];
		$orderby 		= $_REQUEST["orderby"];
		
		$cond = "";
		$rpcond = "";
		if($LOCAT1 != ""){
			$cond .= " and a.LOCAT = '".$LOCAT1."'";
			$rpcond .= "  สาขา ".$LOCAT1;
		}
		
		if($FROMDATEHOLD != ""){
			$cond .= " and a.".$conddate." >= '".$this->Convertdate(1,$FROMDATEHOLD)."'";
			$rpcond .= "  วันที่ยึดรถ ".$FROMDATEHOLD;
		}
		
		if($TODATEHOLD != ""){
			$cond .= " and a.".$conddate." <= '".$this->Convertdate(1,$TODATEHOLD)."'";
			$rpcond .= "  ถึงวันที่ ".$TODATEHOLD;
		}
		
		if($CONTNO1 != ""){
			$cond .= " and a.CONTNO = '".$CONTNO1."'";
			$rpcond .= "  เลขที่สัญญา ".$CONTNO1;
		}
		
		if($Y_USER1 != ""){
			$cond .= " and a.Y_USER = '".$Y_USER1."'";
			$rpcond .= "  พนักงานยึด ".$Y_USER1;
		}
		
		if($STRNO1 != ""){
			$cond .= " and a.STRNO = '".$STRNO1."'";
		}
		
		$sql = "
				IF OBJECT_ID('tempdb..#HOLD') IS NOT NULL DROP TABLE #HOLD
				select *
				into #HOLD
				from(	
					select a.LOCAT, a.CONTNO, a.SNAM+a.NAME1+' '+a.NAME2 as CUSNAME, a.STRNO, SDATE, convert(nvarchar,DATEADD(year,543,a.SDATE),103) as SDATES, 
					YDATE, convert(nvarchar,DATEADD(year,543,a.YDATE),103) as YDATES, isnull(b.NAME,'-') as NAME, a.SMPAY, a.TOTBAL, a.EXP_AMT, 
					a.BOOKVAL, a.BOOKVAT, a.N_NETCST, a.N_NETVAT, a.N_NETTOT
					from {$this->MAuth->getdb('ARHOLD')} a
					left join {$this->MAuth->getdb('OFFICER')} b on a.Y_USER = b.CODE
					where a.Flag ='Y' ".$cond."
				)HOLD
		";
		//echo $sql; 
		$query = $this->db->query($sql);
		$sql = "
				IF OBJECT_ID('tempdb..#TON') IS NOT NULL DROP TABLE #TON
				select *
				into #TON
				from(
					select CONTNO, 
					sum(N_DAMT-N_PAYMENT)-sum(case when (N_DAMT*(N_DAMT-N_PAYMENT)) != 0 then NPROF/N_DAMT*(N_DAMT-N_PAYMENT) else 0 end) as TONSYD ,
					sum(case when (N_DAMT*(N_DAMT-N_PAYMENT)) != 0 then NPROF/N_DAMT*(N_DAMT-N_PAYMENT) else 0 end) as SYDPROF,   
					sum(N_DAMT-N_PAYMENT)- sum(case when (N_DAMT*(N_DAMT-N_PAYMENT)) != 0 then STRPROF/N_DAMT*(N_DAMT-N_PAYMENT) else 0 end) as TONSTR ,  
					sum(case when (N_DAMT*(N_DAMT-N_PAYMENT)) != 0 then STRPROF/N_DAMT*(N_DAMT-N_PAYMENT) else 0 end) AS STRPROF  
					from {$this->MAuth->getdb('HARPAY')} 
					where PAYMENT < DAMT and CONTNO in (select CONTNO from #HOLD)
					group by CONTNO
				)TON
		";
		//echo $sql;
		$query = $this->db->query($sql);
		$sql = "
				select LOCAT, a.CONTNO, CUSNAME, a.STRNO, SDATE, SDATES, YDATE, YDATES, NAME, SMPAY, TOTBAL, TON".$calcul." as TON, ".$calcul."PROF as PROF, EXP_AMT, 
				BOOKVAL, BOOKVAT, N_NETCST, N_NETVAT, N_NETTOT
				from #HOLD a
				left join #TON b on a.CONTNO = b.CONTNO
				order by ".$orderby."
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$sql2 = "
				select 'รวมทั้งสิน' as Total, sum(SMPAY) as sumSMPAY, sum(TOTBAL) as sumTOTBAL, sum(TON".$calcul.") as sumTON, sum(".$calcul."PROF) as sumPROF, sum(EXP_AMT) as sumEXP_AMT, 
				sum(BOOKVAL) as sumBOOKVAL , sum(BOOKVAT) as sumBOOKVAT, sum(N_NETCST) as sumN_NETCST, sum(N_NETVAT) as sumN_NETVAT, sum(N_NETTOT) as sumN_NETTOT
				from #HOLD a
				left join #TON b on a.CONTNO = b.CONTNO
		";
		//echo $sql2; exit;
		$query2 = $this->db->query($sql2);
		
		$head = ""; $html = ""; $head2 = "";  $report = ""; $sumreport = ""; $i=0; 
	
		$head = "<tr>
				<th style='display:none;'>#</th>
				<th style='vertical-align:top;'>สาขา</th>
				<th style='vertical-align:top;'>เลขที่สัญญา /<br>ชื่อ - นามสกุล</th>
				<th style='vertical-align:top;'>เลขตัวถัง /<br>พนักงานยึด</th>
				<th style='vertical-align:top;'>วันขาย /<br>วันเปลี่ยนสภาพ</th>
				<th style='vertical-align:top;text-align:right;'>ชำระแล้ว</th>
				<th style='vertical-align:top;text-align:right;'>ลูกหนี้<br>คงเหลือ</th>
				<th style='vertical-align:top;text-align:right;'>เงินต้น<br>คงเหลือ</th>
				<th style='vertical-align:top;text-align:right;'>ดอกผล<br>คงเหลือ</th>
				<th style='vertical-align:top;text-align:right;'>ค้างชำระ</th>
				<th style='vertical-align:top;text-align:right;'>ราคา<br>ตามบัญชี</th>
				<th style='vertical-align:top;text-align:right;'>ภาษี<br>ตามบัญชี</th>
				<th style='vertical-align:top;text-align:right;'>ราคา<br>ประเมิน</th>
				<th style='vertical-align:top;text-align:right;'>ภาษี</th>
				<th style='vertical-align:top;text-align:right;'>รวมราคา<br>ประเมิน</th>
				</tr>
		";
		
		$head2 = "<tr>
				<th style='vertical-align:middle;'>#</th>
				<th style='vertical-align:middle;'>สาขา</th>
				<th style='vertical-align:middle;'>เลขที่สัญญา</th>
				<th style='vertical-align:middle;'>ชื่อ - นามสกุล</th>
				<th style='vertical-align:middle;'>เลขตัวถัง</th>
				<th style='vertical-align:middle;'>วันขาย</th>
				<th style='vertical-align:middle;'>วันที่เปลี่ยนสภาพ</th>
				<th style='vertical-align:middle;'>พนักงานยึด</th>
				<th style='vertical-align:middle;'>ชำระแล้ว</th>
				<th style='vertical-align:middle;'>ลูกหนี้คงเหลือ</th>
				<th style='vertical-align:middle;'>เงินต้นคงเหลือ<br>(STR)</th>
				<th style='vertical-align:middle;'>ดอกผลคงเหลือ</th>
				<th style='vertical-align:middle;'>ค้างชำระ</th>
				<th style='vertical-align:middle;'>ราคาตามบัญชี</th>
				<th style='vertical-align:middle;'>ภาษีตามบัญชี</th>
				<th style='vertical-align:middle;'>ราคาประเมิน</th>
				<th style='vertical-align:middle;'>ภาษี</th>
				<th style='vertical-align:middle;'>รวมราคาประเมิน</th>
				</tr>
		";
		
		$NRow = 1;
		if($query->row()){
			foreach($query->result() as $row){$i++;
				$html .= "
					<tr class='trow' seq=".$NRow.">
						<td seq=".$NRow++." style='display:none;'></td>
						<td>".$row->LOCAT."</td>
						<td>".$row->CONTNO."<br>".$row->CUSNAME."</td>
						<td>".$row->STRNO."<br>".$row->NAME."</td>
						<td>".$row->SDATES."<br>".$row->YDATES."</td>
						<td align='right'>".number_format($row->SMPAY,2)."</td>
						<td align='right'>".number_format($row->TOTBAL,2)."</td>
						<td align='right'>".number_format($row->TON,2)."</td>
						<td align='right'>".number_format($row->PROF,2)."</td>
						<td align='right'>".number_format($row->EXP_AMT,2)."</td>
						<td align='right'>".number_format($row->BOOKVAL,2)."</td>
						<td align='right'>".number_format($row->BOOKVAT,2)."</td>
						<td align='right'>".number_format($row->N_NETCST,2)."</td>
						<td align='right'>".number_format($row->N_NETVAT,2)."</td>
						<td align='right'>".number_format($row->N_NETTOT,2)."</td>
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
						<td style='mso-number-format:\"\@\";'>".$row->CUSNAME."</td>
						<td style='mso-number-format:\"\@\";'>".$row->STRNO."</td>
						<td style='mso-number-format:\"\@\";'>".$row->SDATES."</td>
						<td style='mso-number-format:\"\@\";'>".$row->YDATES."</td>
						<td style='mso-number-format:\"\@\";'>".$row->NAME."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->SMPAY,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->TOTBAL,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->TON,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->PROF,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->EXP_AMT,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->BOOKVAL,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->BOOKVAT,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->N_NETCST,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->N_NETVAT,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->N_NETTOT,2)."</td>
					</tr>
				";	
			}
		}
		
		if($query2->row()){
			foreach($query2->result() as $row){
				$sumreport .= "
					<tr class='trow'>
						<th colspan='8' align='center'>".$row->Total."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumSMPAY,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumTOTBAL,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumTON,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumPROF,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumEXP_AMT,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumBOOKVAL,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumBOOKVAT,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumN_NETCST,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumN_NETVAT,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumN_NETTOT,2)."</th>
				";	
			}
		}
		
		if($i>0){
			$html = "
				<div id='table-fixed-ReportHoldtoOldcar' class='col-sm-12' style='height:100%;width:100%;overflow:auto;font-size:8pt;'>
					<table id='table-ReportHoldtoOldcar' style='background-color:white;' class='col-sm-12 display table table-bordered' cellspacing='0' width='calc(100% - 1px)'>
						<thead>
						<tr style='height:40px;'>
							<th colspan='14' style='font-size:12pt;border:0px;text-align:center;'>รายงานรถยึดเปลี่ยนเป็นรถเก่า</th>
						</tr>
						<tr style='height:25px;'>
							<td colspan='14' style='border-bottom:1px solid #ddd;text-align:center;'>รายงาน ".$calcul." ".$rpcond."  ออกรายงาน ณ วันที่ ".$this->today('today')."</td>
						</tr>
						".$head."
						</thead>	
						<tbody style='height: 10px !important; overflow: scroll;'>
						".$html."
						</tbody>	
					</table>
				</div>
			";
		}else{
			$html="<font style='color:red;'>ไม่มีข้อมูล</font>";
		}
		
		$report = "
			<div id='table-fixed-ReportHoldtoOldcar2' class='col-sm-12' style='height:100%;width:100%;overflow:auto;'>
				<table id='table-ReportHoldtoOldcar2' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='100%'>
					<thead>
						<tr>
							<th colspan='18' style='font-size:12pt;border:0px;text-align:center;'>รายงานรถยึดเปลี่ยนเป็นรถเก่า</th>
						</tr>
						<tr>
							<td colspan='18' style='border:0px;text-align:center;'>รายงาน ".$calcul." ".$rpcond." ออกรายงาน ณ วันที่ ".$this->today('today')."</td>
						</tr>
						".$head2."
					</thead>	
					<tbody>
						".$report."
						".$sumreport."
					</tbody>
				</table>
			</div>
		";
		
		$response = array("html"=>$html, "report"=>$report, "reporttoday"=>str_replace('/','-',$this->today('today')));
		echo json_encode($response);
	}
	
	function conditiontopdf(){
		$data = array();
		$data[] = urlencode($_REQUEST["LOCAT1"].'||'.$_REQUEST["FROMDATEHOLD"].'||'.$_REQUEST["TODATEHOLD"].'||'.$_REQUEST["CONTNO1"].'||'.$_REQUEST["Y_USER1"].
				  '||'.$_REQUEST["STRNO1"].'||'.$_REQUEST["calcul"].'||'.$_REQUEST["conddate"].'||'.$_REQUEST["orderby"].'||'.$_REQUEST["layout"]);
		echo json_encode($this->generateData($data,"encode"));
	}
	
	function pdf(){
		$data = array();
		$data[] = $_GET["condpdf"];
		$arrs = $this->generateData($data,"decode");
		$arrs[0] = urldecode($arrs[0]);

		$tx = explode("||",$arrs[0]);
		$locat 		= $tx[0];
		$fromdate 	= $tx[1];
		$todate 	= $tx[2];
		$contno 	= $tx[3];
		$yuser 		= $tx[4];
		$strno 		= $tx[5];
		$calcul 	= $tx[6];
		$conddate 	= $tx[7];
		$orderby 	= $tx[8];
		$layout 	= $tx[9];

		$cond = "";
		$rpcond = "";
		if($locat != ""){
			$cond .= " and a.LOCAT = '".$locat."'";
			$rpcond .= "  สาขา ".$locat;
		}
		
		if($fromdate != ""){
			$cond .= " and a.".$conddate." >= '".$this->Convertdate(1,$fromdate)."'";
			$rpcond .= "  วันที่ยึดรถ ".$fromdate;
		}
		
		if($todate != ""){
			$cond .= " and a.".$conddate." <= '".$this->Convertdate(1,$todate)."'";
			$rpcond .= "  ถึงวันที่ ".$todate;
		}
		
		if($contno != ""){
			$cond .= " and a.CONTNO = '".$contno."'";
			$rpcond .= "  เลขที่สัญญา ".$contno;
		}
		
		if($yuser != ""){
			$cond .= " and a.Y_USER = '".$yuser."'";
			$rpcond .= "  พนักงานยึด ".$yuser;
		}
		
		if($strno != ""){
			$cond .= " and a.STRNO = '".$strno."'";
		}
		
		$sql = "
				IF OBJECT_ID('tempdb..#HOLD') IS NOT NULL DROP TABLE #HOLD
				select *
				into #HOLD
				from(	
					select a.LOCAT, a.CONTNO, a.SNAM+a.NAME1+' '+a.NAME2 as CUSNAME, a.STRNO, SDATE, convert(nvarchar,DATEADD(year,543,a.SDATE),103) as SDATES, 
					YDATE, convert(nvarchar,DATEADD(year,543,a.YDATE),103) as YDATES, isnull(b.NAME,'-') as NAME, a.SMPAY, a.TOTBAL, a.EXP_AMT, 
					a.BOOKVAL, a.BOOKVAT, a.N_NETCST, a.N_NETVAT, a.N_NETTOT
					from {$this->MAuth->getdb('ARHOLD')} a
					left join {$this->MAuth->getdb('OFFICER')} b on a.Y_USER = b.CODE
					where a.Flag ='Y' ".$cond."
				)HOLD
		";
		//echo $sql; 
		$query = $this->db->query($sql);
		$sql = "
				IF OBJECT_ID('tempdb..#TON') IS NOT NULL DROP TABLE #TON
				select *
				into #TON
				from(
					select CONTNO, 
					sum(N_DAMT-N_PAYMENT)-sum(case when (N_DAMT*(N_DAMT-N_PAYMENT)) != 0 then NPROF/N_DAMT*(N_DAMT-N_PAYMENT) else 0 end) as TONSYD ,
					sum(case when (N_DAMT*(N_DAMT-N_PAYMENT)) != 0 then NPROF/N_DAMT*(N_DAMT-N_PAYMENT) else 0 end) as SYDPROF,   
					sum(N_DAMT-N_PAYMENT)- sum(case when (N_DAMT*(N_DAMT-N_PAYMENT)) != 0 then STRPROF/N_DAMT*(N_DAMT-N_PAYMENT) else 0 end) as TONSTR ,  
					sum(case when (N_DAMT*(N_DAMT-N_PAYMENT)) != 0 then STRPROF/N_DAMT*(N_DAMT-N_PAYMENT) else 0 end) AS STRPROF  
					from {$this->MAuth->getdb('HARPAY')} 
					where PAYMENT < DAMT and CONTNO in (select CONTNO from #HOLD)
					group by CONTNO
				)TON
		";
		//echo $sql;
		$query = $this->db->query($sql);
		$sql = "
				select LOCAT, a.CONTNO, CUSNAME, a.STRNO, SDATE, SDATES, YDATE, YDATES, NAME, SMPAY, TOTBAL, TON".$calcul." as TON, ".$calcul."PROF as PROF, EXP_AMT, 
				BOOKVAL, BOOKVAT, N_NETCST, N_NETVAT, N_NETTOT
				from #HOLD a
				left join #TON b on a.CONTNO = b.CONTNO
				order by ".$orderby."
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$sql2 = "
				select 'รวมทั้งสิน' as Total, sum(SMPAY) as sumSMPAY, sum(TOTBAL) as sumTOTBAL, sum(TON".$calcul.") as sumTON, sum(".$calcul."PROF) as sumPROF, sum(EXP_AMT) as sumEXP_AMT, 
				sum(BOOKVAL) as sumBOOKVAL , sum(BOOKVAT) as sumBOOKVAT, sum(N_NETCST) as sumN_NETCST, sum(N_NETVAT) as sumN_NETVAT, sum(N_NETTOT) as sumN_NETTOT
				from #HOLD a
				left join #TON b on a.CONTNO = b.CONTNO
		";
		//echo $sql2; exit;
		$query2 = $this->db->query($sql2);
		
		$head = ""; $html = ""; $sumreport = ""; $i=0; 
	
		$head = "
				<tr>
				<th style='border-bottom:0.1px solid black;'>#</th>
				<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>สาขา</th>
				<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>เลขที่สัญญา /<br>ชื่อ - นามสกุล</th>
				<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>เลขตัวถัง /<br>พนักงานยึด</th>
				<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>วันขาย /<br>วันเปลี่ยนสภาพ</th>
				<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>ชำระแล้ว</th>
				<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>ลูกหนี้<br>คงเหลือ</th>
				<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>เงินต้น<br>คงเหลือ</th>
				<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>ดอกผล<br>คงเหลือ</th>
				<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>ค้างชำระ</th>
				<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>ราคา<br>ตามบัญชี</th>
				<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>ภาษี<br>ตามบัญชี</th>
				<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>ราคา<br>ประเมิน</th>
				<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>ภาษี</th>
				<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>รวมราคา<br>ประเมิน</th>
				</tr>
		";
		
		$No = 1;
		if($query->row()){
			foreach($query->result() as $row){	
				$html .= "
					<tr class='trow' seq=".$No.">
						<td style='width:25px;'>".$No++."</td>
						<td style='width:40px;'>".$row->LOCAT."</td>
						<td style='width:125px;'>".$row->CONTNO."<br>".$row->CUSNAME."</td>
						<td style='width:125px;'>".$row->STRNO."<br>".$row->NAME."</td>
						<td style='width:80px;'>".$row->SDATES."<br>".$row->YDATES."</td>
						<td style='width:70px;' align='right'>".number_format($row->SMPAY,2)."</td>
						<td style='width:70px;' align='right'>".number_format($row->TOTBAL,2)."</td>
						<td style='width:70px;' align='right'>".number_format($row->TON,2)."</td>
						<td style='width:70px;' align='right'>".number_format($row->PROF,2)."</td>
						<td style='width:70px;' align='right'>".number_format($row->EXP_AMT,2)."</td>
						<td style='width:70px;' align='right'>".number_format($row->BOOKVAL,2)."</td>
						<td style='width:70px;' align='right'>".number_format($row->BOOKVAT,2)."</td>
						<td style='width:70px;' align='right'>".number_format($row->N_NETCST,2)."</td>
						<td style='width:70px;' align='right'>".number_format($row->N_NETVAT,2)."</td>
						<td style='width:70px;' align='right'>".number_format($row->N_NETTOT,2)."</td>
					</tr>
				";	
			}
		}
		
		if($query2->row()){
			foreach($query2->result() as $row){
				$sumreport .= "
					<tr class='trow bor' style='background-color:#ebebeb;'>
						<td colspan='5' align='center'>".$row->Total."</td>
						<td style='width:70px;text-align:right;'>".number_format($row->sumSMPAY,2)."</td>
						<td style='width:70px;text-align:right;'>".number_format($row->sumTOTBAL,2)."</td>
						<td style='width:70px;text-align:right;'>".number_format($row->sumTON,2)."</td>
						<td style='width:70px;text-align:right;'>".number_format($row->sumPROF,2)."</td>
						<td style='width:70px;text-align:right;'>".number_format($row->sumEXP_AMT,2)."</td>
						<td style='width:70px;text-align:right;'>".number_format($row->sumBOOKVAL,2)."</td>
						<td style='width:70px;text-align:right;'>".number_format($row->sumBOOKVAT,2)."</td>
						<td style='width:70px;text-align:right;'>".number_format($row->sumN_NETCST,2)."</td>
						<td style='width:70px;text-align:right;'>".number_format($row->sumN_NETVAT,2)."</td>
						<td style='width:70px;text-align:right;'>".number_format($row->sumN_NETTOT,2)."</td>
				";	
			}
		}
		
		$mpdf = new \Mpdf\Mpdf([
			'mode' => 'utf-8', 
			'format' => $layout,
			'margin_top' => 10, 	//default = 16
			'margin_left' => 10, 	//default = 15
			'margin_right' => 10, 	//default = 15
			'margin_bottom' => 10, 	//default = 16
			'margin_header' => 9, 	//default = 9
			'margin_footer' => 9, 	//default = 9
		]);
		
		$content = "
			<table class='wf' style='font-size:7.5pt;height:700px;border-collapse:collapse;line-height:23px;overflow:wrap;vertical-align:text-top;'>
				<tbody>
					<tr>
						<th colspan='15' style='font-size:10pt;'>รายงานรถยึดเปลี่ยนเป็นรถเก่า </th>
					</tr>
					<tr>
						<td colspan='15' style='font-size:8pt;height:35px;border-bottom:0.1px solid black;text-align:center;'>".$rpcond."</td>
					</tr>
					".$head."
					".$html."
					".$sumreport."
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