<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//BEE+
class ReportInventorykang extends MY_Controller {
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
							<br>รายงานสินค้าค้างในสต็อกเกิน x วัน<br>
						</div>
						<div class='col-sm-6 col-xs-6 col-sm-offset-3'>
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group'>
									สถานที่รับรถ
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
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group' >
									แบบ
									<select id='BAAB1' class='form-control input-sm' data-placeholder='แบบ'></select>
								</div>
							</div>
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group' >
									สี
									<select id='COLOR1' class='form-control input-sm' data-placeholder='สี'></select>
								</div>
							</div>
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group' >
									ขนาด>=
									<select id='CC1' class='form-control input-sm' data-placeholder='ขนาด'></select>
								</div>
							</div>
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group' >
									เกินกว่า (วัน)
									<input type='number' id='daykang' class='form-control input-sm' min='0' max='1000000' value='1'>
								</div>
							</div>
						</div>
						<div class='col-sm-6 col-xs-6 col-sm-offset-3'>
							<br>
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group'>
									รูปแบบการพิมพ์
									<div class='col-sm-12 col-xs-12' style='border:0.1px dotted #d6d6d6;'>	
										<div class='col-sm-12 col-xs-12'>
											<div class='form-group'>
												<br>
												<input type= 'radio' id='hor' name='layout' checked> แนวนอน
												<br>
												<input type= 'radio' id='ver' name='layout'> แนวตั้ง
												<br><br>
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
												<input type= 'radio' id='new' value='new' name='stat'> ใหม่
												<br>
												<input type= 'radio' id='old' value='old' name='stat'> เก่า
												<br>
												<input type= 'radio' id='all' value='all' name='stat' checked> ทั้งหมด
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class='col-sm-12 col-xs-12' id='showystat' style='display:none;'>	
								<div class='form-group'>
									รถยึด
									<div class='col-sm-12 col-xs-12' style='border:0.1px dotted #d6d6d6;'>	
										<div class='col-sm-4 col-xs-4'>
											<div class='form-group'>
												<br>
												<input type= 'radio' id='typey' name='ystat'> รถยึด
											</div>
										</div>
										<div class='col-sm-4 col-xs-4'>
											<div class='form-group'>
												<br>
												<input type= 'radio' id='typeold' name='ystat'> รถเก่าปกติ
											</div>
										</div>
										<div class='col-sm-4 col-xs-4'>
											<div class='form-group'>
												<br>
												<input type= 'radio' id='typeall' name='ystat' checked> ทั้งหมด
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
		
		$html.= "<script src='".base_url('public/js/SYS02/ReportInventorykang.js')."'></script>";
		echo $html;
	}
	
	function search(){
		$LOCAT1		= $_REQUEST["LOCAT1"];
		$TYPE1 		= str_replace(chr(0),'',$_REQUEST["TYPE1"]);
		$GCOCE1 	= str_replace(chr(0),'',$_REQUEST["GCOCE1"]);
		$BAAB1 		= str_replace(chr(0),'',$_REQUEST["BAAB1"]);
		$MODEL1 	= str_replace(chr(0),'',$_REQUEST["MODEL1"]);
		$CC1 		= str_replace(chr(0),'',$_REQUEST["CC1"]);
		$COLOR1 	= str_replace(chr(0),'',$_REQUEST["COLOR1"]);
		$KANG 		= ($_REQUEST["KANG"] == "" ? 0 : $_REQUEST["KANG"]);
		$stat 		= $_REQUEST["stat"];
		$ystat 		= $_REQUEST["ystat"];
		
		$cond = ""; $rpcond = "";
		
		if($LOCAT1 != ""){
			$cond .= " AND (a.LOCAT LIKE '".$LOCAT1."%')";
			$rpcond .= "  สาขา ".$LOCAT1;
		}
		
		if($TYPE1 != ""){
			$cond .= " AND (b.TYPE LIKE '".$TYPE1."%')";
			$rpcond .= "  ยี่ห้อ ".$TYPE1;
		}
		
		if($GCOCE1 != ""){
			$cond .= " AND ( b.GCODE LIKE '".$GCOCE1."%') ";
		}
		
		if($MODEL1 != ""){
			$cond .= " AND (b.MODEL LIKE '".$MODEL1."%') ";
			$rpcond .= "  รุ่น ".$MODEL1;
		}
		
		if($BAAB1 != ""){
			$cond .= " AND (b.BAAB LIKE '".$BAAB1."%')";
			$rpcond .= "  แบบ ".$BAAB1;
		}
		
		if($CC1 != ""){
			$cond .= " AND (b.CC >= ".$CC1." ) ";
		}else{
			$cond .= " AND (b.CC >= 0 ) ";
		}
		
		if($COLOR1 != ""){
			$cond .= " AND (b.COLOR LIKE '".$COLOR1."%') ";
			$rpcond .= "  สี ".$COLOR1;
		}
		
		if($stat == "O"){
			if($ystat == "typey"){
				$cond .= " AND (b.STAT = 'O') AND (b.YSTAT = 'Y') AND Datediff(Day,b.JOBDATE,GetDate()) >= ".$KANG."";
			}else if($ystat == "typeold"){
				$cond .= " AND (b.STAT = 'O') AND (b.YSTAT != 'Y') AND Datediff(Day,b.RECVDT,GetDate()) >= ".$KANG."";
			}else{
				$cond .= " AND (b.STAT = 'O') AND Datediff(Day,b.RECVDT,GetDate()) >= ".$KANG."";
			}
		}else if($stat == "N"){
			$cond .= " AND (b.STAT = 'N') AND Datediff(Day,b.RECVDT,GetDate()) >= ".$KANG."";
		}else{
			$cond .= " AND Datediff(Day,b.RECVDT,GetDate()) >= ".$KANG."";
		}
		
		$sql = "
				IF OBJECT_ID('tempdb..#main') IS NOT NULL DROP TABLE #main
				select *
				into #main
				from(
					select  b.TYPE, b.MODEL, b.COLOR, b.STAT, b.STRNO, b.GCODE, b.RECVNO, convert(char,b.RECVDT,112) as RECVDT, b.TOTCOST,
					Datediff(Day,b.RECVDT,GetDate()) as Delay  
					from {$this->MAuth->getdb('INVINVO')} a
					left join {$this->MAuth->getdb('INVTRAN')} b on a.RECVNO=b.RECVNO and a.LOCAT=b.RVLOCAT
					where (b.SDATE is null) ".$cond."
				)main
		";//echo $sql; 
		$query = $this->db->query($sql);
		
		$sql = "
				select TYPE, MODEL, COLOR, STAT, STRNO, GCODE, RECVNO, RECVDT, TOTCOST, Delay  
				from #main
				order by RECVDT, RECVNO 
		";//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$sql = "
				select 'รวมทั้งหมด  '+convert(nvarchar,COUNT(STRNO+GCODE))+' คัน' as Total, sum(TOTCOST) as sumTOTCOST from #main
		";//echo $sql; exit;
		$query2 = $this->db->query($sql);
		
		$head = ""; $html = ""; $head2 = "";  $report = ""; $sumreport = ""; $sumreport2 = ""; $i = 0; 
		
		$head = "<tr style='height:25px;'>
				<th style='display:none;'>#</th>
				<th style='vertical-align:top;'>ยี่ห้อ</th>
				<th style='vertical-align:top;'>รุ่น</th>
				<th style='vertical-align:top;'>สี</th>
				<th style='vertical-align:top;text-align:center;'>สถานะ</th>
				<th style='vertical-align:top;text-align:center;'>ประเภทสินค้า</th>
				<th style='vertical-align:top;'>เลขตัวถัง</th>
				<th style='vertical-align:top;text-align:center;'>เลขที่ใบรับ</th>
				<th style='vertical-align:top;text-align:center;'>วันที่รับ</th>
				<th style='vertical-align:top;text-align:right;'>ทุนรวมภาษี</th>
				<th style='vertical-align:top;text-align:center;'>จน.วันที่อยู่ในสต็อก</th>
				</tr>
		";
		
		$NRow = 1;
		if($query->row()){
			foreach($query->result() as $row){$i++;
				$html .= "
					<tr class='trow' seq=".$NRow.">
						<td seq=".$NRow++." style='display:none;'></td>
						<td>".$row->TYPE."</td>
						<td>".$row->MODEL."</td>
						<td>".$row->COLOR."</td>
						<td align='center'>".$row->STAT."</td>
						<td align='center'>".$row->GCODE."</td>
						<td>".$row->STRNO."</td>
						<td align='center'>".$row->RECVNO."</td>
						<td align='center'>".$this->Convertdate(2,$row->RECVDT)."</td>
						<td align='right'>".number_format($row->TOTCOST,2)."</td>
						<td align='center'>".number_format($row->Delay)."</td>
					</tr>
				";	
			}
		}
		
		$head2 = "<tr>
					<th style='vertical-align:middle;'>#</th>
					<th style='vertical-align:top;'>ยี่ห้อ</th>
					<th style='vertical-align:top;'>รุ่น</th>
					<th style='vertical-align:top;'>สี</th>
					<th style='vertical-align:top;'>สถานะ</th>
					<th style='vertical-align:top;'>ประเภทสินค้า</th>
					<th style='vertical-align:top;'>เลขตัวถัง</th>
					<th style='vertical-align:top;'>เลขที่ใบรับ</th>
					<th style='vertical-align:top;'>วันที่รับ</th>
					<th style='vertical-align:top;'>ทุนรวมภาษี</th>
					<th style='vertical-align:top;'>จน.วันที่อยู่ในสต็อก</th>
				</tr>
		";
		
		$No = 1;
		if($query->row()){
			foreach($query->result() as $row){
				$report .= "
					<tr class='trow'>
						<td style='mso-number-format:\"\@\";'>".$No++."</td>
						<td style='mso-number-format:\"\@\";'>".$row->TYPE."</td>
						<td style='mso-number-format:\"\@\";'>".$row->MODEL."</td>
						<td style='mso-number-format:\"\@\";'>".$row->COLOR."</td>
						<td style='mso-number-format:\"\@\";text-align:center;'>".$row->STAT."</td>
						<td style='mso-number-format:\"\@\";text-align:center;'>".$row->GCODE."</td>
						<td style='mso-number-format:\"\@\";'>".$row->STRNO."</td>
						<td style='mso-number-format:\"\@\";'>".$row->RECVNO."</td>
						<td style='mso-number-format:\"\@\";text-align:center;'>".$this->Convertdate(2,$row->RECVDT)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->TOTCOST,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0\";text-align:center;'>".number_format($row->Delay)."</td>
					</tr>
				";	
			}
		}
		
		if($query2->row()){
			foreach($query2->result() as $row){
				$sumreport = "
					<tr style='height:25px;'>
						<th colspan='8' style='border-left:0px;border-bottom:0px;border-top:0px;vertical-align:middle;text-align:left;'>".$row->Total."</th>
						<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;vertical-align:middle;text-align:right;'>".number_format($row->sumTOTCOST,2)."</th>
						<th style='border:0px;text-align:right;'></th>
					</tr>
				";	
			}
		}
		
		if($query2->row()){
			foreach($query2->result() as $row){
				$sumreport2 = "
					<tr class='trow'>
						<th style='mso-number-format:\"\@\";text-align:left;' colspan='9'>".$row->Total."</th>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumTOTCOST,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0\";text-align:right;'></td>
					</tr>
				";	
			}
		}
		
		if($i>0){
			$html = "
				<div id='table-fixed-ReportInventorykang' class='col-sm-12' style='height:100%;width:100%;overflow:auto;font-size:9pt;'>
					<table id='table-ReportInventorykang' style='background-color:white;' class='col-sm-12 display table table-bordered' cellspacing='0' width='calc(100% - 1px)'>
						<thead>
						<tr style='height:40px;'>
							<th colspan='10' style='font-size:12pt;border:0px;vertical-align;middle;text-align:center;'>รายงานสินค้าค้างในสต็อกเกิน x วัน</th>
						</tr>
						<tr style='height:25px;'>
							<td colspan='10' style='border-bottom:1px solid #ddd;vertical-align;middle;text-align:center;'>".$rpcond."  ออกรายงาน ณ วันที่ ".$this->today('today')."</td>
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
			<div id='table-fixed-ReportInventorykang2' class='col-sm-12' style='height:100%;width:100%;overflow:auto;'>
				<table id='table-ReportInventorykang2' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='100%'>
					<thead>
						<tr>
							<th colspan='11' style='font-size:12pt;border:0px;text-align:center;'>รายงานสินค้าค้างในสต็อกเกิน x วัน</th>
						</tr>
						<tr>
							<td colspan='11' style='border:0px;text-align:center;'>".$rpcond."  ออกรายงาน ณ วันที่ ".$this->today('today')."</td>
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
						$_REQUEST["BAAB1"].'||'.
						$_REQUEST["MODEL1"].'||'.
						$_REQUEST["CC1"].'||'.
						$_REQUEST["COLOR1"].'||'.
						$_REQUEST["KANG"].'||'.
						$_REQUEST["stat"].'||'.
						$_REQUEST["ystat"].'||'.
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
		$BAAB1 		= str_replace(chr(0),'',$tx[3]);
		$MODEL1 	= str_replace(chr(0),'',$tx[4]);
		$CC1 		= str_replace(chr(0),'',$tx[5]);
		$COLOR1 	= str_replace(chr(0),'',$tx[6]);
		$KANG 		= $tx[7];
		$stat 		= $tx[8];
		$ystat 		= $tx[9];
		$layout 	= $tx[10];
		
		$cond = ""; $rpcond = "";
		
		if($LOCAT1 != ""){
			$cond .= " AND (a.LOCAT LIKE '".$LOCAT1."%')";
			$rpcond .= "  สาขา ".$LOCAT1;
		}
		
		if($TYPE1 != ""){
			$cond .= " AND (b.TYPE LIKE '".$TYPE1."%')";
			$rpcond .= "  ยี่ห้อ ".$TYPE1;
		}
		
		if($GCOCE1 != ""){
			$cond .= " AND ( b.GCODE LIKE '".$GCOCE1."%') ";
		}
		
		if($MODEL1 != ""){
			$cond .= " AND (b.MODEL LIKE '".$MODEL1."%') ";
			$rpcond .= "  รุ่น ".$MODEL1;
		}
		
		if($BAAB1 != ""){
			$cond .= " AND (b.BAAB LIKE '".$BAAB1."%')";
			$rpcond .= "  แบบ ".$BAAB1;
		}
		
		if($CC1 != ""){
			$cond .= " AND (b.CC >= ".$CC1." ) ";
		}else{
			$cond .= " AND (b.CC >= 0 ) ";
		}
		
		if($COLOR1 != ""){
			$cond .= " AND (b.COLOR LIKE '".$COLOR1."%') ";
			$rpcond .= "  สี ".$COLOR1;
		}
		
		if($stat == "O"){
			if($ystat == "typey"){
				$cond .= " AND (b.STAT = 'O') AND (b.YSTAT = 'Y') AND Datediff(Day,b.JOBDATE,GetDate()) >= ".$KANG."";
			}else if($ystat == "typeold"){
				$cond .= " AND (b.STAT = 'O') AND (b.YSTAT != 'Y') AND Datediff(Day,b.RECVDT,GetDate()) >= ".$KANG."";
			}else{
				$cond .= " AND (b.STAT = 'O') AND Datediff(Day,b.RECVDT,GetDate()) >= ".$KANG."";
			}
		}else if($stat == "N"){
			$cond .= " AND (b.STAT = 'N') AND Datediff(Day,b.RECVDT,GetDate()) >= ".$KANG."";
		}else{
			$cond .= " AND Datediff(Day,b.RECVDT,GetDate()) >= ".$KANG."";
		}
		
		$sql = "
				IF OBJECT_ID('tempdb..#main') IS NOT NULL DROP TABLE #main
				select *
				into #main
				from(
					select b.TYPE, b.MODEL, b.COLOR, b.STAT, b.STRNO, b.GCODE, b.RECVNO, convert(char,b.RECVDT,112) as RECVDT, b.TOTCOST,
					Datediff(Day,b.RECVDT,GetDate()) as Delay  
					from {$this->MAuth->getdb('INVINVO')} a
					left join {$this->MAuth->getdb('INVTRAN')} b on a.RECVNO=b.RECVNO and a.LOCAT=b.RVLOCAT
					where (b.SDATE is null) ".$cond."
				)main
		";//echo $sql; 
		$query = $this->db->query($sql);
		
		$sql = "
				select TYPE, MODEL, COLOR, STAT, STRNO, GCODE, RECVNO, RECVDT, TOTCOST, Delay  
				from #main
				order by RECVDT, RECVNO 
		";//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$sql = "
				select 'รวมทั้งหมด  '+convert(nvarchar,COUNT(STRNO+GCODE))+' คัน' as Total, sum(TOTCOST) as sumTOTCOST from #main
		";//echo $sql; exit;
		$query2 = $this->db->query($sql);
		
		$head = ""; $html = ""; $i=0; 

		$head = "
				<tr>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>#</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>ยี่ห้อ</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>รุ่น</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>สี</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:center;'>สถานะ</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:center;'>ประเภทสินค้า</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>เลขตัวถัง</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:center;'>เลขที่ใบรับ</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:center;'>วันที่รับ</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>ทุนรวมภาษี</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:center;'>จน.วันที่อยู่ในสต็อก</th>
				</tr>
		";
		
		$No = 1;
		if($query->row()){
			foreach($query->result() as $row){	
				$html .= "
					<tr class='trow' seq=".$No.">
						<td style='width:40px;'>".$No++."</td>
						<td style='width:100px;'>".$row->TYPE."</td>
						<td style='width:100px;'>".$row->MODEL."</td>
						<td style='width:150px;'>".$row->COLOR."</td>
						<td style='width:80px;' align='center'>".$row->STAT."</td>
						<td style='width:80px;' align='center'>".$row->GCODE."</td>
						<td style='width:150px;'>".$row->STRNO."</td>
						<td style='width:100px;' align='center'>".$row->RECVNO."</td>
						<td style='width:100px;' align='center'>".$this->Convertdate(2,$row->RECVDT)."</td>
						<td style='width:120px;' align='right'>".number_format($row->TOTCOST,2)."</td>
						<td style='width:120px;' align='center'>".number_format($row->Delay)."</td>
					</tr>
				";	
			}
		}
		
		if($query2->row()){
			foreach($query2->result() as $row){	
				$html .= "
					<tr class='trow bor' style='background-color:#ebebeb;'>
						<th colspan='9' style='text-align:left;vertical-align:middle;'>".$row->Total."</th>
						<th style='text-align:right;vertical-align:middle;'>".number_format($row->sumTOTCOST,2)."</th>
						<th></th>
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
						<th colspan='11' style='font-size:10pt;'>รายงานสินค้าค้างในสต็อกเกิน x วัน</th>
					</tr>
					<tr>
						<td colspan='11' style='font-size:9pt;height:35px;border-bottom:0.1px solid black;text-align:center;'>".$rpcond."</td>
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