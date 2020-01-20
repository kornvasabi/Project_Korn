<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//BEE+
class ReportARdelaydue extends MY_Controller {
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
							<br>รายงานลูกหนี้ขอผลัดดิว<br>
						</div>
						<div class='col-sm-6 col-xs-6 col-sm-offset-3'>	
							<br>
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group'>
									สาขา
									<select id='LOCAT1' class='form-control input-sm' data-placeholder='สาขา'></select>
								</div>
							</div>
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group'>
									เลขที่สัญญา
									<select id='CONTNO1' class='form-control input-sm' data-placeholder='เลขที่สัญญา'></select>
								</div>
							</div>
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group'>
									จากวันที่ขอผลัดดิว
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
						<div class='col-sm-6 col-xs-6 col-sm-offset-3'><br>	
							<div class='col-sm-6 col-xs-6'>	
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
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group'>
									เรียงลำดับข้อมูล
									<div class='col-sm-12 col-xs-12' style='border:0.1px dotted #d6d6d6;'>	
										<div class='col-sm-12 col-xs-12'>
											<div class='form-group'>
												<br>
												<input type= 'radio' id='contno' name='orderby' checked> เลขที่สัญญา
												<br><br>
												<input type= 'radio' id='cuscod' name='orderby'> ตามรหัสลูกค้า
												<br><br>
												<input type= 'radio' id='laccpdt' name='orderby'> วันที่ขอผลัดดิว
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
		
		$html.= "<script src='".base_url('public/js/SYS05/ReportARdelaydue.js')."'></script>";
		echo $html;
	}
	
	function search(){
		$LOCAT1		= $_REQUEST["LOCAT1"];
		$CONTNO1	= $_REQUEST["CONTNO1"];
		$FRMDATE 	= $this->Convertdate(1,$_REQUEST["FRMDATE"]);
		$TODATE 	= $this->Convertdate(1,$_REQUEST["TODATE"]);
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
		
		$sql = "
				select A.LOCAT, A.CONTNO, A.CUSCOD, B.SNAM+B.NAME1+' '+B.NAME2 as CUSNAME, convert(nvarchar,A.SDATE,112) as SDATE, A.STRNO, 
				convert(nvarchar,C.RECVDT,112) as RECVDT, C.AUTHRBY, C.APPRVBY, C.LACCPDT, convert(nvarchar,C.LACCPDT,112) as LACCPDTS, 
				isnull(C.DELYCNT,0) as DELYCNT  
				from {$this->MAuth->getdb('ARMAST')} A
				left join {$this->MAuth->getdb('CUSTMAST')} B on A.CUSCOD = B.CUSCOD
				left join {$this->MAuth->getdb('ACCPCON')} C on A.CONTNO = C.CONTNO
				where (C.LACCPDT BETWEEN '".$FRMDATE."' and '".$TODATE."') AND A.TOTPRC > 0
				".$cond." 
				order by ".$orderby."
		";//echo $sql; 
		$query = $this->db->query($sql);
		
		$head = ""; $html = ""; $head2 = "";  $report = ""; $sumreport = ""; $sumreport2 = ""; $i = 0; 
		
		$head = "<tr style='height:30px;'>
				<th style='display:none;'>#</th>
				<th style='vertical-align:top;'>สาขา</th>
				<th style='vertical-align:top;'>เลขที่สัญญา</th>
				<th style='vertical-align:top;'>รหัสลูกค้า</th>
				<th style='vertical-align:top;'>ชื่อ - นามสกุล</th>
				<th style='vertical-align:top;'>วันที่ขาย</th>
				<th style='vertical-align:top;'>เลขตัวถัง</th>
				<th style='vertical-align:top;'>วันที่แจ้ง</th>
				<th style='vertical-align:top;'>พนง.รับแจ้ง</th>
				<th style='vertical-align:top;'>ผู้อนุมัติ</th>
				<th style='vertical-align:top;'>ผลัดดิวถึงวันที่</th>
				<th style='vertical-align:top;text-align:center;'>จำนวนครั้งที่ขอผลัด</th>
				</tr>
		";
		
		$head2 = "<tr>
					<th style='vertical-align:middle;'>#</th>
					<th style='vertical-align:top;'>สาขา</th>
					<th style='vertical-align:top;'>เลขที่สัญญา</th>
					<th style='vertical-align:top;'>รหัสลูกค้า</th>
					<th style='vertical-align:top;'>ชื่อ - นามสกุล</th>
					<th style='vertical-align:top;'>วันที่ขาย</th>
					<th style='vertical-align:top;'>เลขตัวถัง</th>
					<th style='vertical-align:top;'>วันที่แจ้ง</th>
					<th style='vertical-align:top;'>พนง.รับแจ้ง</th>
					<th style='vertical-align:top;'>ผู้อนุมัติ</th>
					<th style='vertical-align:top;'>ผลัดดิวถึงวันที่</th>
					<th style='vertical-align:top;'>จำนวนครั้งที่ขอผลัด</th>
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
						<td>".$this->Convertdate(2,$row->SDATE)."</td>
						<td>".$row->STRNO."</td>
						<td>".$this->Convertdate(2,$row->RECVDT)."</td>
						<td>".$row->AUTHRBY."</td>
						<td>".$row->APPRVBY."</td>
						<td>".$this->Convertdate(2,$row->LACCPDTS)."</td>
						<td align='center'>".number_format($row->DELYCNT)."</td>
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
						<td style='mso-number-format:\"\@\";'>".$this->Convertdate(2,$row->SDATE)."</td>
						<td style='mso-number-format:\"\@\";'>".$row->STRNO."</td>
						<td style='mso-number-format:\"\@\";'>".$this->Convertdate(2,$row->RECVDT)."</td>
						<td style='mso-number-format:\"\@\";'>".$row->AUTHRBY."</td>
						<td style='mso-number-format:\"\@\";'>".$row->APPRVBY."</td>
						<td style='mso-number-format:\"\@\";'>".$this->Convertdate(2,$row->LACCPDTS)."</td>
						<td style='mso-number-format:\"\@\";text-align:center;'>".number_format($row->DELYCNT)."</td>
					</tr>
				";	
			}
		}
		
		if($i>0){
			$html = "
				<div id='table-fixed-ReportARdelaydue' class='col-sm-12' style='height:100%;width:100%;overflow:auto;font-size:8pt;'>
					<table id='table-ReportARdelaydue' style='background-color:white;' class='col-sm-12 display table table-bordered' cellspacing='0' width='calc(100% - 1px)'>
						<thead>
						<tr style='height:40px;'>
							<th colspan='11' style='font-size:12pt;border:0px;text-align:center;'>รายงานลูกหนี้ขอผลัดดิว</th>
						</tr>
						<tr style='height:25px;'>
							<td colspan='11' style='border-bottom:1px solid #ddd;text-align:center;'>ขอผลัดดิวระหว่างวันที่ ".$_REQUEST["FRMDATE"]." - ".$_REQUEST["TODATE"]." ".$rpcond."  ออกรายงาน ณ วันที่ ".$this->today('today')."</td>
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
			<div id='table-fixed-ReportARdelaydue2' class='col-sm-12' style='height:100%;width:100%;overflow:auto;'>
				<table id='table-ReportARdelaydue2' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='100%'>
					<thead>
						<tr>
							<th colspan='12' style='font-size:12pt;border:0px;text-align:center;'>รายงานลูกหนี้ขอผลัดดิว</th>
						</tr>
						<tr>
							<td colspan='12' style='border:0px;text-align:center;'>ขอผลัดดิวระหว่างวันที่ ".$_REQUEST["FRMDATE"]." - ".$_REQUEST["TODATE"]." ".$rpcond."  ออกรายงาน ณ วันที่ ".$this->today('today')."</td>
						</tr>
						".$head2."
						<tbody>
						".$report."
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
		$data[] = 	urlencode($_REQUEST["LOCAT1"].'||'.$_REQUEST["CONTNO1"].'||'.$_REQUEST["FRMDATE"].'||'.$_REQUEST["TODATE"].'||'.$_REQUEST["orderby"].'||'.$_REQUEST["layout"]);
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
		$FRMDATE 	= $this->Convertdate(1,$tx[2]);
		$TODATE 	= $this->Convertdate(1,$tx[3]);
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
		
		$sql = "
				IF OBJECT_ID('tempdb..#main') IS NOT NULL DROP TABLE #main
				select *
				into #main
				from(
					select A.LOCAT, A.CONTNO, A.CUSCOD, B.SNAM+B.NAME1+' '+B.NAME2 as CUSNAME, convert(nvarchar,A.SDATE,112) as SDATE, A.STRNO, 
					convert(nvarchar,C.RECVDT,112) as RECVDT, C.AUTHRBY, C.APPRVBY, C.LACCPDT, convert(nvarchar,C.LACCPDT,112) as LACCPDTS, 
					isnull(C.DELYCNT,0) as DELYCNT  
					from {$this->MAuth->getdb('ARMAST')} A
					left join {$this->MAuth->getdb('CUSTMAST')} B on A.CUSCOD = B.CUSCOD
					left join {$this->MAuth->getdb('ACCPCON')} C on A.CONTNO = C.CONTNO
					where (C.LACCPDT BETWEEN '".$FRMDATE."' and '".$TODATE."') AND A.TOTPRC > 0
					".$cond." 
				)main
		";//echo $sql; 
		$query = $this->db->query($sql);
		
		$sql = "
				select LOCAT, CONTNO, CUSCOD, CUSNAME, SDATE, STRNO, RECVDT, AUTHRBY, APPRVBY, LACCPDT, LACCPDTS, DELYCNT  
				from #main
				order by ".$orderby."
		";//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$sql = "select count(*) r from #main";
		$dtrow = $this->db->query($sql);
		$dtrow = $dtrow->row();
		
		$head = ""; $html = ""; $i=0; 

		$head = "
				<tr>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>#</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>สาขา</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>เลขที่สัญญา</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>รหัสลูกค้า</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>ชื่อ - นามสกุล</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>วันที่ขาย</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>เลขตัวถัง</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>วันที่แจ้ง</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>พนง.รับแจ้ง</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>ผู้อนุมัติ</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>ผลัดดิวถึงวันที่</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:center;'>จำนวนครั้งที่ขอผลัด</th>
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
						<td style='width:70px;'>".$this->Convertdate(2,$row->SDATE)."</td>
						<td style='width:140px;'>".$row->STRNO."</td>
						<td style='width:70px;'>".$this->Convertdate(2,$row->RECVDT)."</td>
						<td style='width:70px;'>".$row->AUTHRBY."</td>
						<td style='width:70px;'>".$row->APPRVBY."</td>
						<td style='width:80px;'>".$this->Convertdate(2,$row->LACCPDTS)."</td>
						<td style='width:100px;' align='center'>".number_format($row->DELYCNT)."</td>
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
						<th colspan='12' style='font-size:10pt;'>รายงานลูกหนี้ขอผลัดดิว</th>
					</tr>
					<tr>
						<td colspan='12' style='font-size:8pt;height:35px;border-bottom:0.1px solid black;text-align:center;'>".$rpcond." ขอผลัดดิวระหว่างวันที่ ".$tx[2]." - ".$tx[3]."</td>
					</tr>
					".$head."
					".$html."
					<tr>
					<td colspan='12'><b>รวมทั้งสิ้น</b> ".$dtrow->r." <b>รายการ</b></td>
					<tr>
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