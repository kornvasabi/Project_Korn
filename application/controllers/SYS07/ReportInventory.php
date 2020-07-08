<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//BEE+
class ReportInventory extends MY_Controller {
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
							<br>รายงานสินค้าคงเหลือ<br>
						</div>
						<div class='col-sm-8 col-xs-8 col-sm-offset-2'>
							<br>
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group'>
									ณ วันที่
									<input type='text' id='TODATE' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' placeholder='ณ วันที่' value='".$this->today('today')."' style='font-size:10.5pt'>
								</div>
							</div>
						</div>
						<div class='col-sm-8 col-xs-8 col-sm-offset-2'>
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group'>
									สาขา
									<select id='LOCAT1' class='form-control input-sm' data-placeholder='สถานที่รับรถ'></select>
								</div>
							</div>
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group' >
									กลุ่มสินค้า
									<select id='GCOCE1' class='form-control input-sm' data-placeholder='กลุ่มสินค้า'></select>
								</div>
							</div>
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group' >
									ยี่ห้อ
									<select id='TYPE1' class='form-control input-sm' data-placeholder='ยี่ห้อ'></select>
								</div>
							</div>
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group' >
									รุ่น
									<select id='MODEL1' class='form-control input-sm' data-placeholder='รุ่น'></select>
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
									สถานะสินค้า
									<div class='col-sm-12 col-xs-12' style='border:0.1px dotted #d6d6d6;'>	
										<div class='col-sm-12 col-xs-12'>
											<div class='form-group'>
												<br>
												<input type= 'radio' id='new' name='stat'> ใหม่
												<br>
												<input type= 'radio' id='old' name='stat'> เก่า
												<br>
												<input type= 'radio' id='all' name='stat' checked> ทั้งหมด
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
		
		$html.= "<script src='".base_url('public/js/SYS07/ReportInventory.js')."'></script>";
		echo $html;
	}
	
	function search(){
		$LOCAT1		= $_REQUEST["LOCAT1"];
		$TYPE1 		= str_replace(chr(0),'',$_REQUEST["TYPE1"]);
		$GCOCE1 	= str_replace(chr(0),'',$_REQUEST["GCOCE1"]);
		$MODEL1 	= str_replace(chr(0),'',$_REQUEST["MODEL1"]);
		$TODATE 	= $this->Convertdate(1,$_REQUEST["TODATE"]);
		$stat 		= $_REQUEST["stat"];
		
		$cond = ""; $rpcond = "";
		
		if($LOCAT1 != ""){
			$cond .= " AND (CRLOCAT LIKE '".$LOCAT1."%')";
		}
		
		if($TYPE1 != ""){
			$cond .= " AND (TYPE LIKE '".$TYPE1."%')";
		}
		
		if($GCOCE1 != ""){
			$cond .= " AND (GCODE LIKE '".$GCOCE1."%') ";
		}
		
		if($MODEL1 != ""){
			$cond .= " AND (MODEL LIKE '".$MODEL1."%') ";
		}
		
		if($stat != ""){
			$cond .= " AND (STAT LIKE '".$stat."%')";
		}
		
		$sql = "
				SELECT NUM, TYPE, MODEL, BAAB, COLOR, CC, STAT, ST, NETCOST, CRVAT, TOTCOST
				FROM(
					SELECT TYPE as A, MODEL as B, BAAB as C, COLOR as D, CC as E, STAT as F, 1 as G,
					ROW_NUMBER () OVER(ORDER BY TYPE, MODEL, BAAB, COLOR, CC, STAT) as NUM , TYPE, MODEL, BAAB, COLOR, convert(char,convert(int,CC)) as CC,
					STAT, COUNT(STRNO) as ST, NULL as NETCOST, NULL as CRVAT, NULL as TOTCOST
					FROM {$this->MAuth->getdb('INVTRAN')}
					WHERE (SDATE > '".$TODATE."' OR SDATE IS NULL)  ".$cond."
					GROUP BY TYPE, MODEL, BAAB, COLOR, CC, STAT
					union all
					SELECT TYPE as A, MODEL as B, BAAB as C, COLOR as D, CC as E, STAT as F, 2 as G,
					NULL as NUM, RECVNO, convert(char,RECVDT,103) as RECVDT, STRNO, RESVNO, CRLOCAT, convert(nvarchar(2),convert(int,VATRT))+'%' as VATRT, 
					NADDCOST, NETCOST, CRVAT, TOTCOST
					FROM {$this->MAuth->getdb('INVTRAN')}
					WHERE (SDATE > '".$TODATE."' OR SDATE IS NULL)  ".$cond." 
				)A
				ORDER BY A, B, C, D, E, F, G, TYPE
		";//echo $sql;  exit;
		$query = $this->db->query($sql);
		
		$sql = "
				SELECT 'รวมทั้งหมด '+convert(nvarchar(6),COUNT(STRNO))+' คัน' as Total, COUNT(STRNO) as sumST, sum(NADDCOST) as sumNADDCOST, sum(NETCOST) as sumNETCOST, 
				sum(CRVAT) as sumCRVAT, sum(TOTCOST) as sumTOTCOST
				FROM {$this->MAuth->getdb('INVTRAN')} 
				WHERE (SDATE > '".$TODATE."' OR SDATE IS NULL)  ".$cond."
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
			$LOCATNM == "";
			$LOCADDR1 == $COMP_ADR1;
			$COMP_ADR2 == $COMP_ADR2;
		}
		
		$head = ""; $html = ""; $head2 = "";  $report = ""; $sumreport = ""; $sumreport2 = ""; $i = 0; 
		
		$head = "<tr style='height:25px;background-color:#D3ECDC;'>
				<th style='display:none;'>#</th>
				<th style='vertical-align:top;text-align:center;'>ลำดับ</th>
				<th style='vertical-align:top;'>ยี่ห้อรถ<br>เลขที่ใบรับ<br></th>
				<th style='vertical-align:top;'>รุ่น<br>วันที่รับ</th>
				<th style='vertical-align:top;'>แบบ<br>เลขตัวถัง</th>
				<th style='vertical-align:top;'>สี<br>เลขที่ใบจอง</th>
				<th style='vertical-align:top;text-align:center;'>ขนาด(ซีซี)<br>สาขา</th>
				<th style='vertical-align:top;text-align:center;'>สถานะ<br>อัตราภาษี</th>
				<th style='vertical-align:top;text-align:right;'>ยอดคงเหลือ<br>ต้นทุนค่าซ่อม</th>
				<th style='vertical-align:top;text-align:right;'><br>ต้นทุนตัวรถ</th>
				<th style='vertical-align:top;text-align:right;'><br>ภาษี</th>
				<th style='vertical-align:top;text-align:right;'><br>ต้นทุนรวมภาษี</th>
				</tr>
		";
		
		$NRow = 1;
		if($query->row()){
			foreach($query->result() as $row){$i++;
				$html .= "
					<tr class='trow' seq=".$NRow.">
						<td seq=".$NRow++." style='display:none;'></td>
						<td align='center'>".($row->NUM == '' ? '': $row->NUM)."</td>
						<td>".$row->TYPE."</td>
						<td>".$row->MODEL."</td>
						<td>".$row->BAAB."</td>
						<td>".$row->COLOR."</td>
						<td align='center'>".$row->CC."</td>
						<td align='center'>".$row->STAT."</td>
						<td align='right'>".number_format($row->ST,2)."</td>
						<td align='right'>".($row->NETCOST == '' ? '': number_format($row->NETCOST,2))."</td>
						<td align='right'>".($row->CRVAT == '' ? '': number_format($row->CRVAT,2))."</td>
						<td align='right'>".($row->TOTCOST == '' ? '': number_format($row->TOTCOST,2))."</td>
					</tr>
				";	
			}
		}
		
		$head2 = "<tr>
					<th style='vertical-align:top;'>ลำดับ</th>
					<th style='vertical-align:top;'>ยี่ห้อรถ<br>เลขที่ใบรับ<br></th>
					<th style='vertical-align:top;'>รุ่น<br>วันที่รับ</th>
					<th style='vertical-align:top;'>แบบ<br>เลขตัวถัง</th>
					<th style='vertical-align:top;'>สี<br>เลขที่ใบจอง</th>
					<th style='vertical-align:top;text-align:center;'>ขนาด(ซีซี)<br>สาขา</th>
					<th style='vertical-align:top;text-align:center;'>สถานะ<br>อัตราภาษี</th>
					<th style='vertical-align:top;text-align:right;'>ยอดคงเหลือ<br>ต้นทุนค่าซ่อม</th>
					<th style='vertical-align:top;text-align:right;'><br>ต้นทุนตัวรถ</th>
					<th style='vertical-align:top;text-align:right;'><br>ภาษี</th>
					<th style='vertical-align:top;text-align:right;'><br>ต้นทุนรวมภาษี</th>
				</tr>
		";
		
		$No = 1;
		if($query->row()){
			foreach($query->result() as $row){
				$report .= "
					<tr class='trow' >
						<td style='mso-number-format:\"\@\";'>".($row->NUM == '' ? '': $row->NUM)."</td>
						<td style='mso-number-format:\"\@\";'>".$row->TYPE."</td>
						<td style='mso-number-format:\"\@\";'>".$row->MODEL."</td>
						<td style='mso-number-format:\"\@\";'>".$row->BAAB."</td>
						<td style='mso-number-format:\"\@\";'>".$row->COLOR."</td>
						<td style='mso-number-format:\"\@\";text-align:center;'>".$row->CC."</td>
						<td style='mso-number-format:\"\@\";text-align:center;'>".$row->STAT."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->ST,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".($row->NETCOST == '' ? '': number_format($row->NETCOST,2))."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".($row->CRVAT == '' ? '': number_format($row->CRVAT,2))."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".($row->TOTCOST == '' ? '': number_format($row->TOTCOST,2))."</td>
					</tr>
				";	
			}
		}
		
		if($query2->row()){
			foreach($query2->result() as $row){
				$sumreport = "
					<tr style='height:25px;background-color:#D3ECDC;'>
						<th colspan='8' style='vertical-align:middle;text-align:center;'>".$row->Total."</th>
						<th style='vertical-align:middle;text-align:right;'>".number_format($row->sumNETCOST,2)."</th>
						<th style='vertical-align:middle;text-align:right;'>".number_format($row->sumCRVAT,2)."</th>
						<th style='vertical-align:middle;text-align:right;'>".number_format($row->sumTOTCOST,2)."</th>
					</tr>
				";	
			}
		}
		
		if($query2->row()){
			foreach($query2->result() as $row){
				$sumreport2 = "
					<tr class='trow'>
						<th style='mso-number-format:\"\@\";text-align:center;' colspan='8'>".$row->Total."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumNETCOST,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumCRVAT,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumTOTCOST,2)."</th>
					</tr>
				";	
			}
		}
		
		if($i>0){
			$html = "
				<div id='table-fixed-ReportInventory' class='col-sm-12' style='height:100%;width:100%;overflow:auto;font-size:9pt;'>
					<table id='table-ReportInventory' style='background-color:white;' class='col-sm-12 display' cellspacing='0' width='calc(100% - 1px)'>
						<thead>
						<tr style='background-color:white;'>
							<th colspan='11' id='H_ReportInventory' style='padding:10px;'></th>
						</tr>
						<tr style='background-color:white;height:40px;'>
							<th colspan='11' style='font-size:12pt;border:0px;vertical-align;middle;text-align:center;'>รายงานสินค้าคงเหลือ</th>
						</tr>
						<tr style='background-color:white;height:25px;'>
							<td colspan='11' style='border-bottom:1px solid #ddd;vertical-align;middle;text-align:center;'>".$LOCATNM." คงเหลือ ณ วันที่  ".$_REQUEST["TODATE"]." ".$rpcond."  ออกรายงาน ณ วันที่ ".$this->today('today')."</td>
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
			<div id='table-fixed-ReportInventory2' class='col-sm-12' style='height:100%;width:100%;overflow:auto;'>
				<table id='table-ReportInventory2' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='100%'>
					<thead>
						<tr>
							<th colspan='11' style='font-size:12pt;border:0px;text-align:center;'>รายงานสินค้าคงเหลือ</th>
						</tr>
						<tr>
							<td colspan='11' style='border:0px;text-align:center;'>".$LOCATNM." คงเหลือ ณ วันที่  ".$_REQUEST["TODATE"]." ".$rpcond."  ออกรายงาน ณ วันที่ ".$this->today('today')."</td>
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
						$_REQUEST["TYPE1"].'||'.
						$_REQUEST["GCOCE1"].'||'.
						$_REQUEST["MODEL1"].'||'.
						$_REQUEST["TODATE"].'||'.
						$_REQUEST["stat"].'||'.
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
		$TYPE1 		= str_replace(chr(0),'',$tx[1]);
		$GCOCE1 	= str_replace(chr(0),'',$tx[2]);
		$MODEL1 	= str_replace(chr(0),'',$tx[3]);
		$TODATE 	= $this->Convertdate(1,$tx[4]);
		$stat 		= $tx[5];
		$layout 	= $tx[6];
		
		$cond = ""; $rpcond = "";
		
		if($LOCAT1 != ""){
			$cond .= " AND (CRLOCAT LIKE '".$LOCAT1."%')";
		}
		
		if($TYPE1 != ""){
			$cond .= " AND (TYPE LIKE '".$TYPE1."%')";
		}
		
		if($GCOCE1 != ""){
			$cond .= " AND (GCODE LIKE '".$GCOCE1."%') ";
		}
		
		if($MODEL1 != ""){
			$cond .= " AND (MODEL LIKE '".$MODEL1."%') ";
		}
		
		if($stat != ""){
			$cond .= " AND (STAT LIKE '".$stat."%')";
		}
		
		$sql = "
				SELECT NUM, TYPE, MODEL, BAAB, COLOR, CC, STAT, ST, NETCOST, CRVAT, TOTCOST
				FROM(
					SELECT TYPE as A, MODEL as B, BAAB as C, COLOR as D, CC as E, STAT as F, 1 as G,
					ROW_NUMBER () OVER(ORDER BY TYPE, MODEL, BAAB, COLOR, CC, STAT) as NUM , TYPE, MODEL, BAAB, COLOR, convert(char,convert(int,CC)) as CC,
					STAT, COUNT(STRNO) as ST, NULL as NETCOST, NULL as CRVAT, NULL as TOTCOST
					FROM {$this->MAuth->getdb('INVTRAN')}
					WHERE (SDATE > '".$TODATE."' OR SDATE IS NULL)  ".$cond."
					GROUP BY TYPE, MODEL, BAAB, COLOR, CC, STAT
					union all
					SELECT TYPE as A, MODEL as B, BAAB as C, COLOR as D, CC as E, STAT as F, 2 as G,
					NULL as NUM, RECVNO, convert(char,RECVDT,103) as RECVDT, STRNO, RESVNO, CRLOCAT, convert(nvarchar(2),convert(int,VATRT))+'%' as VATRT, 
					NADDCOST, NETCOST, CRVAT, TOTCOST
					FROM {$this->MAuth->getdb('INVTRAN')}
					WHERE (SDATE > '".$TODATE."' OR SDATE IS NULL)  ".$cond." 
				)A
				ORDER BY A, B, C, D, E, F, G, TYPE
		";//echo $sql;  exit;
		$query = $this->db->query($sql);
		
		$sql = "
				SELECT 'รวมทั้งหมด '+convert(nvarchar(6),COUNT(STRNO))+' คัน' as Total, COUNT(STRNO) as sumST, sum(NADDCOST) as sumNADDCOST, sum(NETCOST) as sumNETCOST, 
				sum(CRVAT) as sumCRVAT, sum(TOTCOST) as sumTOTCOST
				FROM {$this->MAuth->getdb('INVTRAN')} 
				WHERE (SDATE > '".$TODATE."' OR SDATE IS NULL)  ".$cond."
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
					<th width='60px' 	align='center'	style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;'>ลำดับ</th>
					<th width='100px' 	align='left'	style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;'>ยี่ห้อรถ<br>เลขที่ใบรับ<br></th>
					<th width='120px' 	align='left'	style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;'>รุ่น<br>วันที่รับ</th>
					<th width='160px' 	align='left' 	style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;'>แบบ<br>เลขตัวถัง</th>
					<th width='150px' 	align='left'	style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;'>สี<br>เลขที่ใบจอง</th>
					<th width='60px' 	align='center' 	style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;'>ขนาด(ซีซี)<br>สาขา</th>
					<th width='60px' 	align='center'	style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;'>สถานะ<br>อัตราภาษี</th>
					<th width='90px' 	align='right'	style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;'>ยอดคงเหลือ<br>ต้นทุนค่าซ่อม</th>
					<th width='110px'	align='right'	style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;'><br>ต้นทุนตัวรถ</th>
					<th width='110px' 	align='right'	style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;'><br>ภาษี</th>
					<th width='110px' 	align='right' 	style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;'><br>ต้นทุนรวมภาษี</th>
				</tr>
		";
		
		$No = 1;
		if($query->row()){
			foreach($query->result() as $row){	
				$html .= "
					<tr class='trow' seq=".$No.">
						<td width='60px'	height='25px'	align='center'	>".($row->NUM == '' ? '': $row->NUM)."</td>
						<td width='100px'	height='25px'	align='left'	>".$row->TYPE."</td>
						<td width='120px' 	height='25px'	align='left'	>".$row->MODEL."</td>
						<td width='160px' 	height='25px'	align='left'	>".$row->BAAB."</td>
						<td width='150px'	height='25px'	align='left'	>".$row->COLOR."</td>
						<td width='60px'	height='25px'	align='center'	>".$row->CC."</td>
						<td width='60px' 	height='25px'	align='center'	>".$row->STAT."</td>
						<td width='90px' 	height='25px'	align='right'	>".number_format($row->ST,2)."</td>
						<td width='110px' 	height='25px'	align='right'	>".($row->NETCOST == '' ? '': number_format($row->NETCOST,2))."</td>
						<td width='110px' 	height='25px'	align='right'	>".($row->CRVAT == '' ? '': number_format($row->CRVAT,2))."</td>
						<td width='110px' 	height='25px'	align='right'	>".($row->TOTCOST == '' ? '': number_format($row->TOTCOST,2))."</td>
					</tr>
				";	
			}
		}
		
		if($query2->row()){
			foreach($query2->result() as $row){	
				$html .= "
					<tr class='trow' style='background-color:#ebebeb;'>
						<th class='bor' style='border-left:0.1px solid black;text-align:center;vertical-align:middle;' colspan='8'>".$row->Total."</th>
						<th class='bor' style='text-align:right;vertical-align:middle;'>".number_format($row->sumNETCOST,2)."</th>
						<th class='bor' style='text-align:right;vertical-align:middle;'>".number_format($row->sumCRVAT,2)."</th>
						<th class='bor' style='border-right:0.1px solid black;text-align:right;vertical-align:middle;'>".number_format($row->sumTOTCOST,2)."</th>
					</tr>
					
				";	
			}
		}
		$body = "<table class='fs9' cellspacing='0'>".$html."</table>";
		
		$mpdf = new \Mpdf\Mpdf([
			'mode' => 'utf-8', 
			'format' => $layout,
			'margin_top' => ($layout == "A4-L" ? "68" : "52"), 	//default = 16
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
					<th colspan='11' style='font-size:11pt;'>".$COMP_NM."<br>รายงานสินค้าคงเหลือ</th>
				</tr>
				<tr>
					<td colspan='11' style='height:35px;text-align:center;'>".$LOCATNM." คงเหลือ ณ วันที่ ".$tx[4]."</td>
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