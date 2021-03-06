<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//BEE+
class ReportOptioninstock extends MY_Controller {
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
							<br>รายงานอุปกรณ์เสริมในสต็อก<br>
						</div>
						<div class='col-sm-8 col-xs-8 col-sm-offset-2'>
							<br>
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group'>
									รหัสสาขา
									<select id='LOCAT1' class='form-control input-sm' data-placeholder='รหัสสาขา'></select>
								</div>
							</div>
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group'>
									ณ วันที่
									<input type='text' id='TODATE' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' placeholder='ณ วันที่' value='".$this->today('today')."' style='font-size:10.5pt'>
								</div>
							</div>
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group' >
									รหัสอุปกรณ์
									<select id='OPTCOCE1' class='form-control input-sm' data-placeholder='รหัสอุปกรณ์'></select>
								</div>
							</div>
						</div>
						<div class='col-sm-8 col-xs-8 col-sm-offset-2'>
							<br>
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group'>
									เงื่อนไข
									<div class='col-sm-12 col-xs-12' style='border:0.1px dotted #d6d6d6;'>	
										<div class='form-group'>
											<br>
											<input type= 'radio' id='blmore0' name='condition' checked> ยอดคงเหลือมากกว่า 0
											<br>
											<input type= 'radio' id='blless0' name='condition'> ยอดคงเหลือน้อยกว่า 0
											<br>
											<input type= 'radio' id='bl0' name='condition'> ยอดคงเหลือเท่ากับ 0
											<br>
											<input type= 'radio' id='blnot0' name='condition'> ยอดคงเหลือไม่เท่ากับ  0
										</div>
									</div>
								</div>
							</div>
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group'>
									รูปแบบการพิมพ์
									<div class='col-sm-12 col-xs-12' style='border:0.1px dotted #d6d6d6;'>	
										<div class='form-group'>
											<br>
											<input type= 'radio' id='hor' name='layout' checked> แนวนอน
											<br><br><br>
											<input type= 'radio' id='ver' name='layout'> แนวตั้ง
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
		
		$html.= "<script src='".base_url('public/js/SYS02/ReportOptioninstock.js')."'></script>";
		echo $html;
	}
	
	function search(){
		$LOCAT1		= $_REQUEST["LOCAT1"];
		$OPTCOCE1 	= str_replace(chr(0),'',$_REQUEST["OPTCOCE1"]);
		$TODATE 	= $this->Convertdate(1,$_REQUEST["TODATE"]);
		$condition 	= $_REQUEST["condition"];
		
		$cond = ""; $rpcond = "";
		
		if($LOCAT1 != ""){
			$cond .= " AND (a.LOCAT LIKE '".$LOCAT1."%')";
			$rpcond .= "  จากสาขา ".$LOCAT1;
		}
		
		if($OPTCOCE1 != ""){
			$cond .= " AND (b.OPTCODE LIKE '".$OPTCOCE1."%') ";
			$rpcond .= "  รหัสอุปกรณ์ ".$OPTCOCE1;
		}
		
		$sql = "
				IF OBJECT_ID('tempdb..#MAIN') IS NOT NULL DROP TABLE #MAIN
				SELECT *
				INTO #MAIN
				FROM(
					select b.OPTCODE, b.OPTNAME, b.LOCAT, (case 
					when (select sum(a.qty) from {$this->MAuth->getdb('OPTTRAN')} a where a.recvdt <= '".$TODATE."' and a.optcode = b.optcode and a.rvlocat = b.locat group by a.optcode) is null 
					then 0 
					else (select sum(a.qty) from {$this->MAuth->getdb('OPTTRAN')} a where a.recvdt <= '".$TODATE."' and a.optcode = b.optcode and a.rvlocat = b.locat group by a.optcode) 
					end - case 
					when (select sum(c.qty) from {$this->MAuth->getdb('ARINOPT')} c where c.sdate <= '".$TODATE."' and c.optcode = b.optcode and c.locat = b.locat group by c.optcode) is null 
					then 0 
					else (select sum(c.qty) from {$this->MAuth->getdb('ARINOPT')} c where c.sdate <= '".$TODATE."' and c.optcode = b.optcode and c.locat = b.locat group by c.optcode) 
					end + case 
					when (select sum(d.movqty) from {$this->MAuth->getdb('OPTMOVT')} d where d.movedt <= '".$TODATE."' and d.optcode = b.optcode and d.moveto = b.locat group by d.optcode) is null 
					then 0 
					else (select sum(d.movqty) from {$this->MAuth->getdb('OPTMOVT')} d where d.movedt <= '".$TODATE."' and d.optcode = b.optcode and d.moveto = b.locat group by d.optcode) 
					end - case 
					when (select sum(e.movqty) from {$this->MAuth->getdb('OPTMOVT')} e where e.movedt <= '".$TODATE."' and e.optcode = b.optcode and e.movefm = b.locat group by e.optcode) is null 
					then 0 
					else (select sum(e.movqty) from {$this->MAuth->getdb('OPTMOVT')} e where e.movedt <= '".$TODATE."' and e.optcode = b.optcode and e.movefm = b.locat group by e.optcode) 
					end) as QTY, b.UNITCST 
					from {$this->MAuth->getdb('OPTMAST')} b 
					where  1=1 ".$cond."
					group by b.OPTCODE, b.OPTNAME, b.LOCAT, b.UNITCST 
				)MAIN 
		";//echo $sql; 
		$query = $this->db->query($sql);
		
		$sql = "
				SELECT OPTCODE, OPTNAME, LOCAT, QTY, UNITCST, QTY*UNITCST as TOT
				FROM #MAIN
				WHERE QTY ".$condition." 0
				ORDER BY OPTCODE, LOCAT
		";//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$sql = "
				SELECT 'รวมทั้งหมด' as Total, sum(QTY) as sumQTY, sum(QTY*UNITCST) as sumTOT
				FROM #MAIN
				WHERE QTY ".$condition." 0
		";//echo $sql; exit;
		$query2 = $this->db->query($sql);
		
		$head = ""; $html = ""; $head2 = "";  $report = ""; $sumreport = ""; $sumreport2 = ""; $i = 0; 
		
		$head = "<tr style='height:25px;'>
				<th style='display:none;'>#</th>
				<th style='vertical-align:top;text-align:left;'>สาขา</th>
				<th style='vertical-align:top;text-align:left;'>รหัสอุปกรณ์</th>
				<th style='vertical-align:top;text-align:left;'>ชื่ออุปกรณ์</th>
				<th style='vertical-align:top;text-align:right;'>จำนวนคงเหลือ</th>
				<th style='vertical-align:top;text-align:right;'>ราคา/หน่วย</th>
				<th style='vertical-align:top;text-align:right;'>รวมเป็นเงิน</th>
				</tr>
		";
		
		$NRow = 1;
		if($query->row()){
			foreach($query->result() as $row){$i++;
				$html .= "
					<tr class='trow' seq=".$NRow.">
						<td seq=".$NRow++." style='display:none;'></td>
						<td>".$row->LOCAT."</td>
						<td>".$row->OPTCODE."</td>
						<td>".$row->OPTNAME."</td>
						<td align='right'>".number_format($row->QTY)."</td>
						<td align='right'>".number_format($row->UNITCST,2)."</td>
						<td align='right'>".number_format($row->TOT,2)."</td>
					</tr>
				";	
			}
		}
		
		$head2 = "<tr>
					<th style='vertical-align:top;text-align:left;'>#</th>
					<th style='vertical-align:top;text-align:left;'>สาขา</th>
					<th style='vertical-align:top;text-align:left;'>รหัสอุปกรณ์</th>
					<th style='vertical-align:top;text-align:left;'>ชื่ออุปกรณ์</th>
					<th style='vertical-align:top;text-align:right;'>จำนวนคงเหลือ</th>
					<th style='vertical-align:top;text-align:right;'>ราคา/หน่วย</th>
					<th style='vertical-align:top;text-align:right;'>รวมเป็นเงิน</th>
				</tr>
		";
		
		$No = 1;
		if($query->row()){
			foreach($query->result() as $row){
				$report .= "
					<tr class='trow'>
						<td style='mso-number-format:\"\@\";'>".$No++."</td>
						<td style='mso-number-format:\"\@\";'>".$row->LOCAT."</td>
						<td style='mso-number-format:\"\@\";'>".$row->OPTCODE."</td>
						<td style='mso-number-format:\"\@\";'>".$row->OPTNAME."</td>
						<td style='mso-number-format:\"\#\,\#\#0\";text-align:right;'>".number_format($row->QTY)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->UNITCST,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->TOT,2)."</td>
					</tr>
				";	
			}
		}
		
		if($query2->row()){
			foreach($query2->result() as $row){
				$sumreport = "
					<tr style='height:25px;'>
						<th colspan='3' style='border-left:0px;border-bottom:0px;border-top:0px;vertical-align:middle;text-align:left;'>".$row->Total."</th>
						<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;vertical-align:middle;text-align:right;'>".number_format($row->sumQTY)."</th>
						<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;vertical-align:middle;text-align:right;'></th>
						<th style='border:0px;text-align:right;'>".number_format($row->sumTOT,2)."</th>
					</tr>
				";	
			}
		}
		
		if($query2->row()){
			foreach($query2->result() as $row){
				$sumreport2 = "
					<tr class='trow'>
						<th style='mso-number-format:\"\@\";text-align:left;' colspan='4'>".$row->Total."</th>
						<td style='mso-number-format:\"\#\,\#\#0\";text-align:right;'>".number_format($row->sumQTY)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'></td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumTOT,2)."</td>
					</tr>
				";	
			}
		}
		
		if($i>0){
			$html = "
				<div id='table-fixed-ReportOptioninstock' class='col-sm-12' style='height:100%;width:100%;overflow:auto;font-size:9pt;'>
					<table id='table-ReportOptioninstock' style='background-color:white;' class='col-sm-12 display table table-bordered' cellspacing='0' width='calc(100% - 1px)'>
						<thead>
						<tr style='height:40px;'>
							<th colspan='6' style='font-size:12pt;border:0px;vertical-align;middle;text-align:center;'>รายงานอุปกรณ์เสริมในสต็อก</th>
						</tr>
						<tr style='height:25px;'>
							<td colspan='6' style='border-bottom:1px solid #ddd;vertical-align;middle;text-align:center;'>สต็อก ณ วันที่  ".$_REQUEST["TODATE"]." ".$rpcond."  ออกรายงาน ณ วันที่ ".$this->today('today')."</td>
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
			<div id='table-fixed-ReportOptioninstock2' class='col-sm-12' style='height:100%;width:100%;overflow:auto;'>
				<table id='table-ReportOptioninstock2' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='100%'>
					<thead>
						<tr>
							<th colspan='7' style='font-size:12pt;border:0px;text-align:center;'>รายงานอุปกรณ์เสริมในสต็อก</th>
						</tr>
						<tr>
							<td colspan='7' style='border:0px;text-align:center;'>สต็อก ณ วันที่ ".$_REQUEST["TODATE"]." ".$rpcond." ออกรายงาน ณ วันที่ ".$this->today('today')."</td>
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
						$_REQUEST["TODATE"].'||'.
						$_REQUEST["condition"].'||'.
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
		$TODATE 	= $this->Convertdate(1,$tx[2]);
		$condition 	= $tx[3];
		$layout 	= $tx[4];
		
		$cond = ""; $rpcond = "";
		
		if($LOCAT1 != ""){
			$cond .= " AND (a.LOCAT LIKE '".$LOCAT1."%')";
			$rpcond .= "  จากสาขา ".$LOCAT1;
		}
		
		if($OPTCOCE1 != ""){
			$cond .= " AND (b.OPTCODE LIKE '".$OPTCOCE1."%') ";
			$rpcond .= "  รหัสอุปกรณ์ ".$OPTCOCE1;
		}
		
		$sql = "
				IF OBJECT_ID('tempdb..#MAIN') IS NOT NULL DROP TABLE #MAIN
				SELECT *
				INTO #MAIN
				FROM(
					select b.OPTCODE, b.OPTNAME, b.LOCAT, (case 
					when (select sum(a.qty) from {$this->MAuth->getdb('OPTTRAN')} a where a.recvdt <= '".$TODATE."' and a.optcode = b.optcode and a.rvlocat = b.locat group by a.optcode) is null 
					then 0 
					else (select sum(a.qty) from {$this->MAuth->getdb('OPTTRAN')} a where a.recvdt <= '".$TODATE."' and a.optcode = b.optcode and a.rvlocat = b.locat group by a.optcode) 
					end - case 
					when (select sum(c.qty) from {$this->MAuth->getdb('ARINOPT')} c where c.sdate <= '".$TODATE."' and c.optcode = b.optcode and c.locat = b.locat group by c.optcode) is null 
					then 0 
					else (select sum(c.qty) from {$this->MAuth->getdb('ARINOPT')} c where c.sdate <= '".$TODATE."' and c.optcode = b.optcode and c.locat = b.locat group by c.optcode) 
					end + case 
					when (select sum(d.movqty) from {$this->MAuth->getdb('OPTMOVT')} d where d.movedt <= '".$TODATE."' and d.optcode = b.optcode and d.moveto = b.locat group by d.optcode) is null 
					then 0 
					else (select sum(d.movqty) from {$this->MAuth->getdb('OPTMOVT')} d where d.movedt <= '".$TODATE."' and d.optcode = b.optcode and d.moveto = b.locat group by d.optcode) 
					end - case 
					when (select sum(e.movqty) from {$this->MAuth->getdb('OPTMOVT')} e where e.movedt <= '".$TODATE."' and e.optcode = b.optcode and e.movefm = b.locat group by e.optcode) is null 
					then 0 
					else (select sum(e.movqty) from {$this->MAuth->getdb('OPTMOVT')} e where e.movedt <= '".$TODATE."' and e.optcode = b.optcode and e.movefm = b.locat group by e.optcode) 
					end) as QTY, b.UNITCST 
					from {$this->MAuth->getdb('OPTMAST')} b 
					where  1=1 ".$cond."
					group by b.OPTCODE, b.OPTNAME, b.LOCAT, b.UNITCST 
				)MAIN 
		";//echo $sql; 
		$query = $this->db->query($sql);
		
		$sql = "
				SELECT OPTCODE, OPTNAME, LOCAT, QTY, UNITCST, QTY*UNITCST as TOT
				FROM #MAIN
				WHERE QTY ".$condition." 0
				ORDER BY OPTCODE, LOCAT
		";//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$sql = "
				SELECT 'รวมทั้งหมด' as Total, sum(QTY) as sumQTY, sum(QTY*UNITCST) as sumTOT
				FROM #MAIN
				WHERE QTY ".$condition." 0
		";//echo $sql; exit;
		$query2 = $this->db->query($sql);
		
		$head = ""; $html = ""; $i=0; 

		$head = "
				<tr>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>#</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>สาขา</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>รหัสอุปกรณ์</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>ชื่ออุปกรณ์</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>จำนวนคงเหลือ</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>ราคา/หน่วย</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>รวมเป็นเงิน</th>
				</tr>
		";
		
		$No = 1;
		if($query->row()){
			foreach($query->result() as $row){	
				$html .= "
					<tr class='trow' seq=".$No.">
						<td style='width:50px;'>".$No++."</td>
						<td style='width:100px;'>".$row->LOCAT."</td>
						<td style='width:100px;'>".$row->OPTCODE."</td>
						<td style='width:200px;'>".$row->OPTNAME."</td>
						<td style='width:100px;' align='right'>".number_format($row->QTY)."</td>
						<td style='width:100px;' align='right'>".number_format($row->UNITCST,2)."</td>
						<td style='width:100px;' align='right'>".number_format($row->TOT,2)."</td>
					</tr>
				";	
			}
		}
		
		if($query2->row()){
			foreach($query2->result() as $row){	
				$html .= "
					<tr class='trow bor' style='background-color:#ebebeb;'>
						<th colspan='4' style='text-align:left;vertical-align:middle;'>".$row->Total."</th>
						<th style='text-align:right;vertical-align:middle;'>".number_format($row->sumQTY)."</th>
						<th style='text-align:right;vertical-align:middle;'></th>
						<th style='text-align:right;vertical-align:middle;'>".number_format($row->sumTOT,2)."</th>
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
						<th colspan='7' style='font-size:10pt;'>รายงานอุปกรณ์เสริมในสต็อก</th>
					</tr>
					<tr>
						<td colspan='7' style='font-size:9pt;height:35px;border-bottom:0.1px solid black;text-align:center;'>สต็อกคงเหลือ ณ ".$tx[2]." ".$rpcond."</td>
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