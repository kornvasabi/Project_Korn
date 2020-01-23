<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//BEE+
class ReportHoldtoStock extends MY_Controller {
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
				<div class='col-sm-12 col-xs-12' style='height:100%;overflow:auto;font-size:11pt;'>					
					<div class='row' style='height:90%;'>
						<div class='col-sm-12 col-xs-12' style='background-color:#0067a5;border:5px solid white;height:75px;text-align:center;font-size:12pt;color:white;font-weight:bold;'>	
							<br>รายงานรถยึดรอไถ่ถอน<br>
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
									<input type='text' id='FROMDATECHG' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' placeholder='จากวันที่' value='".$this->today('today')."' style='font-size:10.5pt'>
								</div>
							</div>
							<div class='col-sm-4 col-xs-4'>	
								<div class='form-group'>
									ถึงวันที่
									<input type='text' id='TODATECHG' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' placeholder='ถึงวันที่' value='".$this->today('today')."' style='font-size:10.5pt'>
								</div>
							</div>
						</div>
						<div class='col-sm-8 col-xs-8 col-sm-offset-2'>	
							<div class='col-sm-12 col-xs-12'>	
								<div class='form-group'>
									รูปแบบการพิมพ์
									<div class='col-sm-12 col-xs-12' style='border:0.1px dotted #d6d6d6;'>	
										<div class='col-sm-4 col-xs-4'>
											<div class='form-group'><br>
												<input type= 'radio' id='ver' name='layout' checked> แนวตั้ง
											</div>
										</div>
										<div class='col-sm-4 col-xs-4'>
											<div class='form-group'><br>
												<input type= 'radio' id='hor' name='layout'> แนวนอน
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
										<div class='col-sm-4 col-xs-4'>
											<div class='form-group'><br>
												<input type= 'radio' id='strno' name='orderby'> เลขตัวถัง
											</div>
										</div>
										<div class='col-sm-4 col-xs-4'>
											<div class='form-group'><br>
												<input type= 'radio' id='contno' name='orderby' checked> เลขที่สัญญา
											</div>
										</div>
										<div class='col-sm-4 col-xs-4'>
											<div class='form-group'><br>
												<input type= 'radio' id='ydate' name='orderby'> วันที่ยึดรถ
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class='row' style='height:10%;'>
						<div class='col-sm-12 col-xs-12'><br>	
							<div class='form-group'>
								<button id='btnt1search' class='btn btn-primary btn-sm' style='width:100%'><span class='glyphicon glyphicon-search'> แสดง</span></button>
							</div>
						</div>
					</div>
				</div>
			</div>
		";
		
		$html.= "<script src='".base_url('public/js/SYS05/ReportHoldtoStock.js')."'></script>";
		echo $html;
	}
	
	function search(){
		$LOCAT1			= $_REQUEST["LOCAT1"];
		$FROMDATECHG 	= $_REQUEST["FROMDATECHG"];
		$TODATECHG 		= $_REQUEST["TODATECHG"];
		$orderby 		= $_REQUEST["orderby"];
		
		$cond = "";
		$rpcond = "";
		if($LOCAT1 != ""){
			$cond .= " and a.LOCAT = '".$LOCAT1."'";
			$rpcond .= "  สาขา ".$LOCAT1;
		}
		
		if($FROMDATECHG != ""){
			$cond .= " and a.YDATE >= '".$this->Convertdate(1,$FROMDATECHG)."'";
			$rpcond .= "  วันที่ยึดรถ ".$FROMDATECHG;
		}
		
		if($TODATECHG != ""){
			$cond .= " and a.YDATE <= '".$this->Convertdate(1,$TODATECHG)."'";
			$rpcond .= "  ถึงวันที่ ".$TODATECHG;
		}
		
		$sql = "
				select a.LOCAT, a.CONTNO, a.CUSCOD, b.SNAM, b.NAME1, b.NAME2, a.STRNO, a.SMPAY, convert(nvarchar,DATEADD(year,543,a.SDATE),103) as SDATE, 
				a.KEYINPRC, a.TOTPRC, a.EXP_PRD, a.EXP_AMT, convert(nvarchar,DATEADD(year,543,a.YDATE),103) as YDATES, a.CHECKER, a.BILLCOLL, c.CRLOCAT
				from  {$this->MAuth->getdb('ARMAST')} a
				left join {$this->MAuth->getdb('CUSTMAST')} b on a.CUSCOD = b.CUSCOD
				left join {$this->MAuth->getdb('INVTRAN')} c on a.STRNO = c.STRNO
				where a.YSTAT = 'Y' ".$cond."
				order by ".$orderby."
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$head = ""; $html = ""; $head2 = "";  $report = ""; $i=0; 
	
		$head = "<tr>
				<th style='display:none;'>#</th>
				<th style='vertical-align:middle;'>สาขา</th>
				<th style='vertical-align:middle;'>เลขที่สัญญา</th>
				<th style='vertical-align:middle;'>ชื่อ - นามสกุล</th>
				<th style='vertical-align:middle;'>เลขตัวถัง</th>
				<th style='vertical-align:middle;'>วันทำสัญญา</th>
				<th style='vertical-align:middle;'>ราคาขาย</th>
				<th style='vertical-align:middle;'>ชำระแล้ว</th>
				<th style='vertical-align:middle;'>ค้างชำระ<br>(บาท)</th>
				<th style='vertical-align:middle;'>ค้างชำระ<br>(งวด)</th>
				<th style='vertical-align:middle;'>วันที่ยึด</th>
				<th style='vertical-align:middle;'>สาขาที่เก็บ</th>
				<th style='vertical-align:middle;'>Billcoll</th>
				<th style='vertical-align:middle;'>Checker</th>
				</tr>
		";
		
		$head2 = "<tr>
				<th style='vertical-align:middle;'>#</th>
				<th style='vertical-align:middle;'>สาขา</th>
				<th style='vertical-align:middle;'>เลขที่สัญญา</th>
				<th style='vertical-align:middle;'>ชื่อ - นามสกุล</th>
				<th style='vertical-align:middle;'>เลขตัวถัง</th>
				<th style='vertical-align:middle;'>วันทำสัญญา</th>
				<th style='vertical-align:middle;'>ราคาขาย</th>
				<th style='vertical-align:middle;'>ชำระแล้ว</th>
				<th style='vertical-align:middle;text-align:right;'>ค้างชำระ<br>(บาท)</th>
				<th style='vertical-align:middle;text-align:center;'>ค้างชำระ<br>(งวด)</th>
				<th style='vertical-align:middle;'>วันที่ยึด</th>
				<th style='vertical-align:middle;'>สาขาที่เก็บ</th>
				<th style='vertical-align:middle;'>Billcoll</th>
				<th style='vertical-align:middle;'>Checker</th>
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
						<td>".($row->SNAM.$row->NAME1.' '.$row->NAME2.' ('.$row->CUSCOD.')')."</td>
						<td>".$row->STRNO."</td>
						<td>".$row->SDATE."</td>
						<td align='right'>".number_format($row->TOTPRC,2)."</td>
						<td align='right'>".number_format($row->SMPAY,2)."</td>
						<td align='right'>".number_format($row->EXP_AMT,2)."</td>
						<td align='center'>".number_format($row->EXP_PRD)."</td>
						<td>".$row->YDATES."</td>
						<td>".$row->CRLOCAT."</td>
						<td>".$row->BILLCOLL."</td>
						<td>".$row->CHECKER."</td>
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
						<td style='mso-number-format:\"\@\";'>".($row->SNAM.$row->NAME1.' '.$row->NAME2.' ('.$row->CUSCOD.')')."</td>
						<td style='mso-number-format:\"\@\";'>".$row->STRNO."</td>
						<td style='mso-number-format:\"\@\";'>".$row->SDATE."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->TOTPRC,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->SMPAY,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->EXP_AMT,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0\";text-align:right;'>".number_format($row->EXP_PRD)."</td>
						<td style='mso-number-format:\"\@\";'>".$row->YDATES."</td>
						<td style='mso-number-format:\"\@\";'>".$row->CRLOCAT."</td>
						<td style='mso-number-format:\"\@\";'>".$row->BILLCOLL."</td>
						<td style='mso-number-format:\"\@\";'>".$row->CHECKER."</td>
					</tr>
				";	
			}
		}
		
		if($i>0){
			$html = "
				<div id='table-fixed-ReportHoldtoStock' class='col-sm-12' style='height:100%;width:100%;overflow:auto;font-size:9pt;'>
					<table id='table-ReportHoldtoStock' style='background-color:white;' class='col-sm-12 display table table-bordered' cellspacing='0' width='calc(100% - 1px)'>
						<thead>
						<tr style='height:40px;'>
							<th colspan='14' style='font-size:12pt;border:0px;text-align:center;'>รายงานรถยึดรอไถ่ถอน</th>
						</tr>
						<tr style='height:25px;'>
							<td colspan='14' style='border-bottom:1px solid #ddd;text-align:center;'>".$rpcond."  ออกรายงาน ณ วันที่ ".$this->today('today')."</td>
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
			<div id='table-fixed-ReportHoldtoStock2' class='col-sm-12' style='height:100%;width:100%;overflow:auto;'>
				<table id='table-ReportHoldtoStock2' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='100%'>
					<thead>
						<tr>
							<th colspan='14' style='font-size:12pt;border:0px;text-align:center;'>รายงานรถยึดรอไถ่ถอน</th>
						</tr>
						<tr>
							<td colspan='14' style='border:0px;text-align:center;'>".$rpcond." ออกรายงาน ณ วันที่ ".$this->today('today')."</td>
						</tr>
						".$head2."
					</thead>	
					<tbody>
						".$report."
					</tbody>
				</table>
			</div>
		";
		
		$response = array("html"=>$html, "report"=>$report, "reporttoday"=>str_replace('/','-',$this->today('today')));
		echo json_encode($response);
	}
	
	function conditiontopdf(){
		$data = array();
		$data[] = urlencode($_REQUEST["LOCAT1"].'||'.$_REQUEST["FROMDATECHG"].'||'.$_REQUEST["TODATECHG"].'||'.$_REQUEST["orderby"].'||'.$_REQUEST["layout"]);
		echo json_encode($this->generateData($data,"encode"));
	}
	
	function pdf(){
		$data = array();
		$data[] = $_GET["condpdf"];
		$arrs = $this->generateData($data,"decode");
		$arrs[0] = urldecode($arrs[0]);

		$tx = explode("||",$arrs[0]);
		$locat = $tx[0];
		$fromdate = $tx[1];
		$todate = $tx[2];
		$orderby = $tx[3];
		$layout = $tx[4];

		$cond = "";
		$rpcond = "";
		
		if($locat != ""){
			$cond .= " and a.LOCAT = '".$locat."'";
			$rpcond .= "  สาขา ".$locat;
		}
		
		if($fromdate != ""){
			$cond .= " and a.YDATE >= '".$this->Convertdate(1,$fromdate)."'";
			$rpcond .= "  วันที่ยึดรถ ".$fromdate;
		}
		
		if($todate != ""){
			$cond .= " and a.YDATE <= '".$this->Convertdate(1,$todate)."'";
			$rpcond .= "  ถึงวันที่ ".$todate;
		}
		
		$sql = "
				select a.LOCAT, a.CONTNO, a.CUSCOD, b.SNAM, b.NAME1, b.NAME2, a.STRNO, a.SMPAY, convert(nvarchar,DATEADD(year,543,a.SDATE),103) as SDATE, 
				a.KEYINPRC, a.TOTPRC, a.EXP_PRD, a.EXP_AMT, convert(nvarchar,DATEADD(year,543,a.YDATE),103) as YDATES, a.CHECKER, a.BILLCOLL, c.CRLOCAT
				from  {$this->MAuth->getdb('ARMAST')} a
				left join {$this->MAuth->getdb('CUSTMAST')} b on a.CUSCOD = b.CUSCOD
				left join {$this->MAuth->getdb('INVTRAN')} c on a.STRNO = c.STRNO
				where a.YSTAT = 'Y' ".$cond."
				order by ".$orderby."
		";
		//echo $sql; exit;
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$head = ""; $html = ""; $i=0; 
	
		$head = "
				<tr >
				<th style='border-bottom:0.1px solid black;text-align:left;'>#</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>สาขา</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>เลขที่สัญญา</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>ชื่อ - นามสกุล</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>เลขตัวถัง</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>วันทำสัญญา</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ราคาขาย</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ชำระแล้ว</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ค้างชำระ<br>(บาท)</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ค้างชำระ<br>(งวด)</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>วันที่ยึด</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>สาขาที่เก็บ</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>Billcoll</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>Checker</th>
				</tr>
		";
		
		$No = 1;
		if($query->row()){
			foreach($query->result() as $row){	
				$html .= "
					<tr class='trow' seq=".$No.">
						<td style='width:30px;'>".$No++."</td>
						<td	style='width:40px;'>".$row->LOCAT."</td>
						<td style='width:80px;'>".$row->CONTNO."</td>
						<td style='width:200px;'>".($row->SNAM.$row->NAME1.' '.$row->NAME2.' ('.$row->CUSCOD.')')."</td>
						<td style='width:125px;'>".$row->STRNO."</td>
						<td style='width:70px;'>".$row->SDATE."</td>
						<td style='width:75px;' align='right'>".number_format($row->TOTPRC,2)."</td>
						<td style='width:75px;' align='right'>".number_format($row->SMPAY,2)."</td>
						<td style='width:75px;' align='right'>".number_format($row->EXP_AMT,2)."</td>
						<td style='width:60px;' align='right'>".number_format($row->EXP_PRD)."&nbsp;</td>
						<td style='width:70px;'>".$row->YDATES."</td>
						<td style='width:50px;'>".$row->CRLOCAT."</td>
						<td style='width:45px;'>".$row->BILLCOLL."</td>
						<td style='width:45px;'>".$row->CHECKER."</td>
					</tr>
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
						<th colspan='14' style='font-size:10pt;'>รายงานรถยึดรอไถ่ถอน </th>
					</tr>
					<tr>
						<td colspan='14' style='font-size:8pt;height:35px;border-bottom:0.1px solid black;text-align:center;'>".$rpcond."</td>
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