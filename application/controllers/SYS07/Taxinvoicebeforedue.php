<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//BEE+
class Taxinvoicebeforedue extends MY_Controller {
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
			<div class='b_tab1' name='home' locat='{$this->sess['branch']}' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}' today='".$this->today('today')."' style='height:calc(100vh - 132px);overflow:auto;background-color:#f6fefa;'>
				<div class='col-sm-12 col-xs-12' style='height:100%;overflow:auto;font-size:10.5pt;'>					
					<div class='row' style='height:90%;'><br>
						<div class='col-sm-6 col-xs-6 col-sm-offset-3' style='border:0.1px dotted #d6d6d6;'>
							<div class='col-sm-6 col-xs-6'>	
								<label class='radio lobiradio lobiradio-info'>
									<input type='radio' id='normal' name='vat' value='normal' checked><i></i> ยื่นภาษีปกติ
								</label>
							</div>
							<div class='col-sm-6 col-xs-6'>	
								<label class='radio lobiradio lobiradio-info'>
									<input type='radio' id='more' value='more' name='vat'><i></i> ยื่นเพิ่มเติม
								</label>
							</div>
						</div>
						<div class='col-sm-6 col-xs-6 col-sm-offset-3'>
							<br>
							<div class='col-sm-12 col-xs-12'>	
								<div class='form-group'>
									วันที่ออกใบกำกับภาษียื่นเพิ่มเติม
									<input type='text' id='VATDATE' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' value='' style='font-size:10.5pt' disabled>
								</div>
							</div>
						</div>
						<div class='col-sm-6 col-xs-6 col-sm-offset-3'>
							<div class='col-sm-12 col-xs-12'>	
								<div class='form-group'>
									รหัสสาขา
									<select id='LOCAT1' class='form-control input-sm' data-placeholder='รหัสสาขา'>
										<option value='".$this->sess["branch"]."'>".$this->sess["branch"]."</option>
									</select>
								</div>
							</div>
							<div class='col-sm-12 col-xs-12'>	
								<div class='form-group'>
									จากวันที่ดิว
									<input type='text' id='FRMDATE' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' placeholder='จากวันที่' value='".$this->today('today')."' style='font-size:10.5pt'>
								</div>
							</div>
							<div class='col-sm-12 col-xs-12'>	
								<div class='form-group'>
									ถึงวันที่
									<input type='text' id='TODATE' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' placeholder='ถึงวันที่' value='".$this->today('today')."' style='font-size:10.5pt'>
								</div>
							</div>
							<div class='col-sm-12 col-xs-12'>	
								<div class='form-group' >
									เฉพาะเลขที่สัญญา
									<select id='CONTNO1' class='form-control input-sm' data-placeholder='เลขที่สัญญา'></select>
								</div>
							</div>
							<div class='col-sm-12 col-xs-12'>	
								<div class='form-group'>
									<button id='btnt1search' class='btn btn-info btn-sm' style='width:100%;font-size:10.5pt;'><span class='glyphicon glyphicon-search'> สอบถามการ Run ล่าสุด</span></button>
								</div>
							</div>
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group '>
									วันที่ RUN ใบกำกับล่าสุด
									<input type='text' id='LRUNDT' class='form-control input-sm' style='font-size:10.5pt' disabled>
								</div>
							</div>
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group'>
									ใบกำกับภาษีเลขที่ล่าสุด
									<input type='text' id='LTAXNO' class='form-control input-sm' style='font-size:10.5pt' disabled>
								</div>
							</div>
						</div>
					</div>
					<div class='row' style='height:10%;'>
						<div class='col-sm-6 col-xs-6 col-sm-offset-3'>
							<div class='col-sm-6 col-xs-6'>	
								<button id='btnprint' class='btn btn-primary btn-sm' style='width:100%;font-size:10.5pt;'><span class='glyphicon glyphicon-print'> Print</span></button>
							</div>
							<div class='col-sm-6 col-xs-6'>	
								<button id='btnrunno' class='btn btn-primary btn-sm' style='width:100%;font-size:10.5pt;'><span class='glyphicon glyphicon-ok'> RunNo</span></button>
							</div>
						</div>
						<div class='col-sm-12 col-xs-12 text-red' style='font-size:8pt;text-align:center;'>	
							<br>
							** ก่อนออกใบกำกับภาษีก่อนดิว ต้องตรวจสอบการรับชำระให้ถูกต้องก่อน หากต้องการยกเลิกการรับชำระรายการที่ออกใบกำกับภาษี อาจทำให้ภาษีไม่ถูกต้อง **
						</div>
					</div>
				</div>
			</div>
		";
		
		$html.= "<script src='".base_url('public/js/SYS07/Taxinvoicebeforedue.js')."'></script>";
		echo $html;
	}
	
	function searchLASTTAXNO(){
		$LOCAT1		= $_REQUEST["LOCAT1"];
		$FRMDATE	= $this->Convertdate(1,$_REQUEST["FRMDATE"]);
		$TODATE		= $this->Convertdate(1,$_REQUEST["TODATE"]);

		$sql = "
				declare @locat varchar(10) = '".$LOCAT1."';
				declare @year varchar(4)	= (select substring('".$FRMDATE."',3,2));
				declare @month varchar(2)	= (select substring('".$FRMDATE."',5,2));
				declare @year2 varchar(4)	= (select substring('".$TODATE."',1,4));
				declare @month2 varchar(2)	= (select substring('".$TODATE."',5,2));
				declare @shortL varchar(4)	= (select ShortL from {$this->MAuth->getdb('INVLOCAT')} where LOCATCD = @locat);
				declare @no varchar(2)		= (select H_TXPAY from {$this->MAuth->getdb('CONDPAY')})
				declare @Taxno varchar(15)	= @shortL+@no+'-'+@year+@month;
				declare @Ltaxno varchar(20) = (select max(taxno) as LTaxno from {$this->MAuth->getdb('TAXTRAN')} where locat=@locat and Taxno like '%'+@Taxno+'%');
				declare @Lrundt datetime	= (select LRUNTAX as LRUNDT from {$this->MAuth->getdb('LASTNO')} where locat=@locat and Cr_year=@year2 and Cr_month=@month2)

				select @Ltaxno as Ltaxno, convert(char,@Lrundt,112) as Lrundt
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$response = array();
		if($query->row()){
			foreach($query->result() as $row){
				$response["Ltaxno"] 	= $row->Ltaxno;
				$response["Lrundt"] 	= $this->Convertdate(2,$row->Lrundt);
			}
		}
		
		echo json_encode($response);
	}

	function conditiontopdf(){
		$data 	= 	array();
		$data[] = 	urlencode(
						$_REQUEST["LOCAT1"].'||'.
						$_REQUEST["CONTNO1"].'||'.
						$_REQUEST["VATDATE"].'||'.
						$_REQUEST["FRMDATE"].'||'.
						$_REQUEST["TODATE"].'||'.
						$_REQUEST["vat"]
					);
		echo json_encode($this->generateData($data,"encode"));
	}
	
	function pdf(){
		ini_set("memory_limit","-1");
		ini_set("pcre.backtrack_limit", "100000000");

		$data 		= array();
		$data[] 	= $_GET["condpdf"];
		$arrs 		= $this->generateData($data,"decode");
		$arrs[0]	= urldecode($arrs[0]);
		$tx 		= explode("||",$arrs[0]);
		$LOCAT1		= $tx[0];
		$CONTNO1 	= str_replace(chr(0),'',$tx[1]);
		$VATDATE 	= $tx[2];
		$FRMDATE 	= $tx[3];
		$TODATE 	= $tx[4];
		$vat 		= $tx[5];
		
		$cond = "";
		$rpcond = "";
		
		if($LOCAT1 != ""){
			$cond .= " and (LOCAT like '".$LOCAT1."%') ";
			$rpcond .= " สาขา ".$LOCAT1."";
		}
		
		if($CONTNO1 != ""){
			$cond .= " and (CONTNO like '".$CONTNO1."%') ";
			$rpcond .= " เลขที่สัญญา ".$CONTNO1."";
		}
		
		if($vat == "normal"){
			$cond .= " and (TAXDT between '".$this->Convertdate(1,$FRMDATE)."' and '".$this->Convertdate(1,$TODATE)."') and (TAXFLG ='N')";
			$rpcond .= " จากวันที่ ".$FRMDATE." ถึงวันที่ ".$TODATE."";
		}else{
			$cond .= " and (TAXDT = '".$this->Convertdate(1,$VATDATE)."') and (TAXFLG ='A')";
			$rpcond .= " ณ วันที่ ".$VATDATE."";
		}

		$sql = "
				IF OBJECT_ID('tempdb..#main') IS NOT NULL DROP TABLE #main
				select *
				into #main
				from(
					select convert(char,TAXDT,112) as TAXDTS, TAXNO, CUSCOD, SNAM+NAME1+' '+NAME2 as CUSTNAME, CONTNO, 
					convert(nvarchar(3),FPAY)+'-'+convert(nvarchar(3),LPAY) as PAY, VATRT, NETAMT, VATAMT, TOTAMT, FLAG,
					case when FLAG = 'C' then 'ยกเลิก'  else '' end as CANCEL
					from {$this->MAuth->getdb('TAXTRAN')} 
					where (TAXTYP = 'B') ".$cond."
				)main
		";//echo $sql; 
		$query = $this->db->query($sql);
		
		$sql = " 
				select TAXDTS, TAXNO, CUSCOD, CUSTNAME, CONTNO, PAY, VATRT, NETAMT, VATAMT, TOTAMT, CANCEL
				from #main
				order by TAXDTS, TAXNO
		";//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$sql2 = " 
				select 'ยอดรวมทั้งสิ้น' as Total1, sum(NETAMT) as NETAMT1, sum(VATAMT) as VATAMT1, sum(TOTAMT) as TOTAMT1
				from #main
		";//echo $sql; exit;
		$query2 = $this->db->query($sql2);
		
		$sql3 = " 
				select 'ยอดรวมรายการยกเลิก' as Total2, sum(NETAMT) as NETAMT2, sum(VATAMT) as VATAMT2, sum(TOTAMT) as TOTAMT2
				from #main
				where FLAG = 'C'
		";//echo $sql; exit;
		$query3 = $this->db->query($sql3);
		
		$sql4 = " 
				select 'ยอดรวมสุทธิ' as Total3, sum(NETAMT) as NETAMT3, sum(VATAMT) as VATAMT3, sum(TOTAMT) as TOTAMT3
				from #main
				where FLAG != 'C'
		";//echo $sql; exit;
		$query4 = $this->db->query($sql4);

		$html = "";
		
		$head = "
				<tr>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>ลำดับ</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>วันที่ใบกำกับ</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>เลขที่ใบกำกับ</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>รหัสลูกค้า</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>ชื่อ - นามสกุล</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>เลขที่สัญญา</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:center;'>งวดที่</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:center;'>อัตราภาษี<br>(%)</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>มูลค่าก่อนภาษี<br>(บาท)</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>มูลค่าภาษี<br>(บาท)</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>มูลค่ารวมภาษี<br>(บาท)</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:center;'></th>
				</tr>
		";
		
		$No = 1;
		if($query->row()){
			foreach($query->result() as $row){	
				$html .= "
					<tr class='trow' seq=".$No.">
						<td style='width:30px;'>".$No++."</td>
						<td style='width:70px;'>".$this->Convertdate(2,$row->TAXDTS)."</td>
						<td style='width:80px;'>".$row->TAXNO."</td>
						<td style='width:80px;'>".$row->CUSCOD."</td>
						<td style='width:230px;'>".$row->CUSTNAME."</td>
						<td style='width:80px;'>".$row->CONTNO."</td>
						<td style='width:40px;' align='center'>".$row->PAY."</td>
						<td style='width:50px;' align='center'>".number_format($row->VATRT)."</td>
						<td style='width:80px;' align='right'>".number_format($row->NETAMT,2)."</td>
						<td style='width:70px;' align='right'>".number_format($row->VATAMT,2)."</td>
						<td style='width:90px;' align='right'>".number_format($row->TOTAMT,2)."</td>
						<td style='width:50px;' align='center'>".$row->CANCEL."</td>
					</tr>
				";	
			}
		}else{
			$html .= "<tr class='trow'><td colspan='12'>ไม่มี</td></tr>";
		}
		
		if($query2->row()){
			foreach($query2->result() as $row){	
				$html .= "
					<tr class='trow bor' style='background-color:#ebebeb;'>
						<th colspan='8' style='text-align:left;vertical-align:middle;'>".$row->Total1."</th>
						<th style='text-align:right;vertical-align:middle;'>".number_format($row->NETAMT1,2)."</th>
						<th style='text-align:right;vertical-align:middle;'>".number_format($row->VATAMT1,2)."</th>
						<th style='text-align:right;vertical-align:middle;'>".number_format($row->TOTAMT1,2)."</th>
						<th style='text-align:right;vertical-align:middle;'></th>
					</tr>
				";	
			}
		}
		
		if($query3->row()){
			foreach($query3->result() as $row){	
				$html .= "
					<tr class='trow bor' style='background-color:#ebebeb;'>
						<th colspan='8' style='text-align:left;vertical-align:middle;'>".$row->Total2."</th>
						<th style='text-align:right;vertical-align:middle;'>".number_format($row->NETAMT2,2)."</th>
						<th style='text-align:right;vertical-align:middle;'>".number_format($row->VATAMT2,2)."</th>
						<th style='text-align:right;vertical-align:middle;'>".number_format($row->TOTAMT2,2)."</th>
						<th style='text-align:right;vertical-align:middle;'></th>
					</tr>
				";	
			}
		}
		
		if($query4->row()){
			foreach($query4->result() as $row){	
				$html .= "
					<tr class='trow bor' style='background-color:#ebebeb;'>
						<th colspan='8' style='text-align:left;vertical-align:middle;'>".$row->Total3."</th>
						<th style='text-align:right;vertical-align:middle;'>".number_format($row->NETAMT3,2)."</th>
						<th style='text-align:right;vertical-align:middle;'>".number_format($row->VATAMT3,2)."</th>
						<th style='text-align:right;vertical-align:middle;'>".number_format($row->TOTAMT3,2)."</th>
						<th style='text-align:right;vertical-align:middle;'></th>
					</tr>
				";	
			}
		}
		
		$mpdf = new \Mpdf\Mpdf([
			'mode' => 'utf-8', 
			'format' => 'A4-L',
			'margin_top' => 10, 	//default = 16
			'margin_left' => 8, 	//default = 15
			'margin_right' => 8, 	//default = 15
			'margin_bottom' => 10, 	//default = 16
			'margin_header' => 9, 	//default = 9
			'margin_footer' => 9, 	//default = 9
		]);
		
		$content = "
			<table class='wf' style='font-size:8pt;height:700px;width:100%;border-collapse:collapse;line-height:23px;overflow:wrap;vertical-align:text-top;'>
				<tbody>
					<tr>
						<th colspan='12' style='font-size:10pt;'>รายงานใบกำกับภาษีค่างวดก่อนดิว</th>
					</tr>
					<tr>
						<td colspan='12' style='font-size:9pt;height:35px;border-bottom:0.1px solid black;text-align:center;'>ออกรายงาน ณ วันที่ ".$this->today('today')."</td>
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
			<div class='wf pf' style='top:1060;left:600;font-size:6pt;'>".date('d/m/').(date('Y')+543)." ".date('H:i')." หน้า {PAGENO} / {nbpg}</div>
		";
		$mpdf->SetHTMLHeader($head);	
		$mpdf->WriteHTML($content);	
		$mpdf->Output();
	}
}