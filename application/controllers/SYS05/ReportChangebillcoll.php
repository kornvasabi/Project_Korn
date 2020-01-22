<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//BEE+
class ReportChangebillcoll extends MY_Controller {
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
							<br>รายงานการเปลี่ยนผู้รับผิดชอบสัญญา<br>
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
									เลขที่สัญญา
									<select id='CONTNO1' class='form-control input-sm' data-placeholder='เลขที่สัญญา'></select>
								</div>
							</div>
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group'>
									ตั้งแต่วันที่
									<input type='text' id='FRMDATE' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' placeholder='ตั้งแต่วันที่' value='".$this->today('today')."' style='font-size:10.5pt'>
								</div>
							</div>
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group'>
									ถึงวันที่
									<input type='text' id='TODATE' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' placeholder='ถึงวันที่' value='".$this->today('today')."' style='font-size:10.5pt'>
								</div>
							</div>
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group' >
									รหัส Billcol ใหม่
									<select id='BILLCOLL1' class='form-control input-sm' data-placeholder='รหัส Billcol ใหม่'></select>
								</div>
							</div>
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group'>
									ค้างมากกว่า (งวด)
									<input type='text' id='EXPRD1' class='form-control input-sm' style='font-size:10.5pt' value='0'>
								</div>
							</div>
						</div>
						<div class='col-sm-8 col-xs-8 col-sm-offset-2'>
							<br>
							<div class='col-sm-12 col-xs-12'>
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
					<div class='row' style='height:10%;'>
						<div class='col-sm-12 col-xs-12'><br>	
							<button id='btnt1search' class='btn btn-primary btn-sm' style='width:100%;font-size:10.5pt;'><span class='glyphicon glyphicon-search'> แสดง</span></button>
						</div>
					</div>
				</div>
			</div>
		";
		
		$html.= "<script src='".base_url('public/js/SYS05/ReportChangebillcoll.js')."'></script>";
		echo $html;
	}
	
	function search(){
		$LOCAT1		= $_REQUEST["LOCAT1"];
		$CONTNO1	= $_REQUEST["CONTNO1"];
		$BILLCOLL1 	= str_replace(chr(0),'',$_REQUEST["BILLCOLL1"]);
		$FRMDATE 	= $this->Convertdate(1,$_REQUEST["FRMDATE"]);
		$TODATE 	= $this->Convertdate(1,$_REQUEST["TODATE"]);
		$EXPRD1 		= $_REQUEST["EXPRD1"];
		
		$cond = ""; $rpcond = "";
		
		if($LOCAT1 != ""){
			$cond .= " AND (a.LOCAT LIKE '%".$LOCAT1."%')";
			$rpcond .= "  สาขา ".$LOCAT1;
		}
		
		if($CONTNO1 != ""){
			$cond .= " AND (a.CONTNO LIKE '%".$CONTNO1."%')";
			$rpcond .= "  เลขที่สัญญา ".$CONTNO1;
		}
		
		if($BILLCOLL1 != ""){
			$cond .= " AND (a.NEW_BILLC LIKE '%".$CONTNO1."%')";
			$rpcond .= "  พนักงานเก็บเงิน ".$BILLCOLL1;
		}
		
		$sql = "
				select a.CONTNO, rtrim(d.NAME1)+' '+d.NAME2 as CUSNAME, a.CHGNO, convert(nvarchar,a.CHGDATE,112) as CHGDATE,
				a.OLD_BILLC+' '+b.name As OLDNAM, a.NEW_BILLC+' '+c.name as NEWNAM, a.LOCAT 
				from {$this->MAuth->getdb('CHG_BILLTR')}  a  
				left outer join {$this->MAuth->getdb('OFFICER')} b on a.old_Billc=b.code  
				left outer join {$this->MAuth->getdb('OFFICER')} c on a.new_Billc=c.code  
				left outer join {$this->MAuth->getdb('CUSTMAST')} d on a.cuscod=d.cuscod  
				where CHGDATE between '".$FRMDATE."' and '".$TODATE."' and EXP_prd >= ".$EXPRD1." ".$cond."
		";//echo $sql; 
		$query = $this->db->query($sql);
		
		$head = ""; $html = ""; $head2 = "";  $report = ""; $sumreport = ""; $sumreport2 = ""; $i = 0; 
		
		$head = "<tr style='height:25px;'>
				<th style='display:none;'>#</th>
				<th style='vertical-align:top;'>เลขที่สัญญา</th>
				<th style='vertical-align:top;'>ชื่อ - นามสกุล ลูกค้า</th>
				<th style='vertical-align:top;'>เลขที่เปลี่ยน</th>
				<th style='vertical-align:top;'>วันที่เปลี่ยน</th>
				<th style='vertical-align:top;'>เปลี่ยนจาก</th>
				<th style='vertical-align:top;'>เปลี่ยนเป็น</th>
				<th style='vertical-align:top;'>ชื่อสาขาที่เปลี่ยน</th>
				</tr>
		";
		
		$head2 = "<tr>
					<th style='vertical-align:middle;'>#</th>
					<th style='vertical-align:top;'>เลขที่สัญญา</th>
					<th style='vertical-align:top;'>ชื่อ - นามสกุล ลูกค้า</th>
					<th style='vertical-align:top;'>เลขที่เปลี่ยน</th>
					<th style='vertical-align:top;'>วันที่เปลี่ยน</th>
					<th style='vertical-align:top;'>เปลี่ยนจาก</th>
					<th style='vertical-align:top;'>เปลี่ยนเป็น</th>
					<th style='vertical-align:top;'>ชื่อสาขาที่เปลี่ยน</th>
				</tr>
		";
		
		$NRow = 1;
		if($query->row()){
			foreach($query->result() as $row){$i++;
				$html .= "
					<tr class='trow' seq=".$NRow.">
						<td seq=".$NRow++." style='display:none;'></td>
						<td>".$row->CONTNO."</td>
						<td>".$row->CUSNAME."</td>
						<td>".$row->CHGNO."</td>
						<td>".$this->Convertdate(2,$row->CHGDATE)."</td>
						<td>".$row->OLDNAM."</td>
						<td>".$row->NEWNAM."</td>
						<td>".$row->LOCAT."</td>
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
						<td style='mso-number-format:\"\@\";'>".$row->CONTNO."</td>
						<td style='mso-number-format:\"\@\";'>".$row->CUSNAME."</td>
						<td style='mso-number-format:\"\@\";'>".$row->CHGNO."</td>
						<td style='mso-number-format:\"\@\";'>".$this->Convertdate(2,$row->CHGDATE)."</td>
						<td style='mso-number-format:\"\@\";'>".$row->OLDNAM."</td>
						<td style='mso-number-format:\"\@\";'>".$row->NEWNAM."</td>
						<td style='mso-number-format:\"\@\";'>".$row->LOCAT."</td>
					</tr>
				";	
			}
		}
		
		if($i>0){
			$html = "
				<div id='table-fixed-ReportChangebillcoll' class='col-sm-12' style='height:100%;width:100%;overflow:auto;'>
					<table id='table-ReportChangebillcoll' style='background-color:white;' class='col-sm-12 display table table-bordered' cellspacing='0' width='calc(100% - 1px)'>
						<thead>
						<tr style='height:40px;'>
							<th colspan='7' style='font-size:12pt;border:0px;vertical-align;middle;text-align:center;'>รายงานการเปลี่ยนผู้รับผิดชอบสัญญา</th>
						</tr>
						<tr style='height:25px;'>
							<td colspan='7' style='border-bottom:1px solid #ddd;vertical-align;middle;text-align:center;'>".$rpcond."  ออกรายงาน ณ วันที่ ".$this->today('today')."</td>
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
			<div id='table-fixed-ReportChangebillcoll2' class='col-sm-12' style='height:100%;width:100%;overflow:auto;'>
				<table id='table-ReportChangebillcoll2' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='100%'>
					<thead>
						<tr>
							<th colspan='8' style='font-size:12pt;border:0px;text-align:center;'>รายงานการเปลี่ยนผู้รับผิดชอบสัญญา</th>
						</tr>
						<tr>
							<td colspan='8' style='border:0px;text-align:center;'>".$rpcond." ออกรายงาน ณ วันที่ ".$this->today('today')."</td>
						</tr>
						".$head2."
						".$report."
					</thead>	
				</table>
			</div>
		";
		
		$response = array("html"=>$html, "report"=>$report, "reporttoday"=>str_replace('/','-',$this->today('today')));
		echo json_encode($response);
	}
	
	function conditiontopdf(){
		$data 	= 	array();
		$data[] = 	urlencode($_REQUEST["LOCAT1"].'||'.$_REQUEST["CONTNO1"].'||'.$_REQUEST["BILLCOLL1"].'||'.$_REQUEST["FRMDATE"]
					.'||'.$_REQUEST["TODATE"].'||'.$_REQUEST["EXPRD1"].'||'.$_REQUEST["layout"]);
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
		$CONTNO1	= $tx[1];
		$BILLCOLL1 	= str_replace(chr(0),'',$tx[2]);
		$FRMDATE 	= $this->Convertdate(1,$tx[3]);
		$TODATE 	= $this->Convertdate(1,$tx[4]);
		$EXPRD1 	= $tx[5];
		$layout 	= $tx[6];
		
		$cond = ""; $rpcond = "";
		
		if($LOCAT1 != ""){
			$cond .= " AND (a.LOCAT LIKE '%".$LOCAT1."%')";
			$rpcond .= "  สาขา ".$LOCAT1;
		}
		
		if($CONTNO1 != ""){
			$cond .= " AND (a.CONTNO LIKE '%".$CONTNO1."%')";
			$rpcond .= "  เลขที่สัญญา ".$CONTNO1;
		}
		
		if($BILLCOLL1 != ""){
			$cond .= " AND (a.NEW_BILLC LIKE '%".$CONTNO1."%')";
			$rpcond .= "  พนักงานเก็บเงิน ".$BILLCOLL1;
		}
		
		$sql = "
				select a.CONTNO, rtrim(d.NAME1)+' '+d.NAME2 as CUSNAME, a.CHGNO, convert(nvarchar,a.CHGDATE,112) as CHGDATE,
				a.OLD_BILLC+' '+b.name As OLDNAM, a.NEW_BILLC+' '+c.name as NEWNAM, a.LOCAT 
				from {$this->MAuth->getdb('CHG_BILLTR')}  a  
				left outer join {$this->MAuth->getdb('OFFICER')} b on a.old_Billc=b.code  
				left outer join {$this->MAuth->getdb('OFFICER')} c on a.new_Billc=c.code  
				left outer join {$this->MAuth->getdb('CUSTMAST')} d on a.cuscod=d.cuscod  
				where CHGDATE between '".$FRMDATE."' and '".$TODATE."' and EXP_prd >= ".$EXPRD1." ".$cond."
		";//echo $sql; 
		$query = $this->db->query($sql);
		
		$sql = "
				select 'รวม '+convert(nvarchar,COUNT(CHGNO+CONTNO))+' รายการ' as QTY
				from {$this->MAuth->getdb('CHG_BILLTR')} a
				where CHGDATE between '".$FRMDATE."' and '".$TODATE."' and EXP_prd >= ".$EXPRD1." ".$cond."
		";//echo $sql; exit;
		$query2 = $this->db->query($sql);
		
		$head = ""; $html = ""; $i=0; 
		
		$head = "<tr>
				<th style='border-bottom:0.1px solid black;text-align:left;'>#</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>เลขที่สัญญา</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>ชื่อ - นามสกุล ลูกค้า</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>เลขที่เปลี่ยน</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>วันที่เปลี่ยน</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>เปลี่ยนจาก</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>เปลี่ยนเป็น</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>ชื่อสาขาที่เปลี่ยน</th>
				</tr>
		";
		
		$No = 1;
		if($query->row()){
			foreach($query->result() as $row){	
				$html .= "
					<tr class='trow' seq=".$No.">
						<td style='width:50px;'>".$No++."</td>
						<td style='width:150px;'>".$row->CONTNO."</td>
						<td style='width:280px;'>".$row->CUSNAME."</td>
						<td style='width:150px;'>".$row->CHGNO."</td>
						<td style='width:150px;'>".$this->Convertdate(2,$row->CHGDATE)."</td>
						<td style='width:280px;'>".$row->OLDNAM."</td>
						<td style='width:280px;'>".$row->NEWNAM."</td>
						<td style='width:150px;'>".$row->LOCAT."</td>
					</tr>
				";	
			}
		}
		
		if($query2->row()){
			foreach($query2->result() as $row){	
				$html .= "<tr class='trow' seq=".$No."><td colspan='8' style='height:30px;vertical-align:bottom;'>".$row->QTY."</td></tr>";	
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
			<table class='wf' style='font-size:12pt;height:700px;border-collapse:collapse;line-height:23px;overflow:wrap;vertical-align:text-top;'>
				<tbody>
					<tr>
						<th colspan='8' style='font-size:14pt;'>รายงานการเปลี่ยนผู้รับผิดชอบสัญญา</th>
					</tr>
					<tr>
						<td colspan='8' style='font-size:12pt;height:35px;border-bottom:0.1px solid black;text-align:center;'>".$rpcond."</td>
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