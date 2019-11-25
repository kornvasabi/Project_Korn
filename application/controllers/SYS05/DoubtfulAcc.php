<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//BEE+
class DoubtfulAcc extends MY_Controller {
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
				<div class='col-sm-12' style='overflow:auto;'>					
					<div class='row'>
						<div class='col-sm-2 col-xs-2'>	
							<div class='form-group'>
								สาขา
								<select id='LOCAT1' class='form-control input-sm' data-placeholder='สาขา'></select>
							</div>
						</div>
						<div class='col-sm-2 col-xs-2'>	
							<div class='form-group'>
								ประเภทหนี้สูญ
								<select id='TYPLOST1' class='form-control input-sm' data-placeholder='ประเภทหนี้สูญ'></select>
							</div>
						</div>
						<div class='col-sm-2 col-xs-2'>	
							<div class='form-group'>
								จากวันที่
								<input type='text' id='FROMDATE' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' placeholder='จากวันที่'>
							</div>
						</div>
						<div class='col-sm-2 col-xs-2'>	
							<div class='form-group'>
								ถึงวันที่
								<input type='text' id='TODATE' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' placeholder='ถึงวันที่'>
							</div>
						</div>
						<div class='col-sm-2 col-xs-2'>	
							<div class='form-group'>
								<br>
								<button id='btnt1search' class='btn btn-primary btn-sm' style='width:100%'><span class='glyphicon glyphicon-search'> สอบถาม</span></button>
							</div>
						</div>
						<div class='col-sm-2 col-xs-2'>	
							<div class='form-group'>
								<br>
								<button id='bth1add' class='btn btn-cyan btn-sm'  style='width:100%'><span class='glyphicon glyphicon-pencil'> เพิ่มข้อมูล</span></button>
							</div>
						</div>
					</div><br>
					<div id='resultt_DoubtfulAcc' style='background-color:white;'></div>
				</div>
			</div>
		";
		
		$html.= "<script src='".base_url('public/js/SYS05/DoubtfulAcc.js')."'></script>";
		echo $html;
	}
	
	//ฟอร์มบันทึกลูกหนี้อื่น
	function getfromDoubtfulAcc(){
		$level	= $_REQUEST["level"];
		//$locat = $this->sess['branch'];
		
		$html = "
			<div class='b_add_arlost' style='width:100%;height:calc(100vh - 132px);overflow:auto;background-color:white;'>
				<div style='float:left;height:100%;overflow:auto;' class='col-sm-10 col-xs-10 col-sm-offset-1'>
					<div class='form-group col-sm-10 col-xs-10 col-sm-offset-1' style='border:0.1px solid #f0f0f0;'>
					<div class='form-group col-sm-10 col-xs-10 col-sm-offset-1'>
					<div class='row'>
						<div class='col-sm-4 col-xs-4'>	
							<div class='form-group' style='color:blue;'>
								เลขที่สัญญา
								<select id='CONTNO' class='form-control input-sm' data-placeholder='เลขที่สัญญา'></select>
							</div>
						</div>
					</div>
					<div class='row'>
						<div class='col-sm-4 col-xs-4'>	
							<div class='form-group'>
								สาขา
								<input type='text' id='LOCAT' class='form-control input-sm' style='font-size:10.5pt' disabled>
							</div>
						</div>
						<div class='col-sm-4 col-xs-4'>	
							<div class='form-group'>
								ชื่อ - สกุล ลูกหนี้
								<input type='text' id='CUSNAME' class='form-control input-sm' style='font-size:10.5pt' disabled> 
							</div>
						</div>
						<div class='col-sm-4 col-xs-4'>	
							<div class='form-group'>
								รหัสลูกหนี้
								<input type='text' id='CUSCOD' class='form-control input-sm' style='font-size:10.5pt' disabled>
							</div>
						</div>
						<div class='col-sm-4 col-xs-4'>	
							<div class='form-group'>
								เลขทะเบียน
								<input type='text' id='REGNO' class='form-control input-sm' style='font-size:10.5pt' disabled>
							</div>
						</div>
						<div class='col-sm-4 col-xs-4'>	
							<div class='form-group'>
								เลขตัวถัง
								<input type='text' id='STRNO' class='form-control input-sm' style='font-size:10.5pt' disabled>
							</div>
						</div>
						<div class='col-sm-4 col-xs-4'>	
							<div class='form-group'>
								ราคาขาย
								<input type='text' id='PRICE' class='form-control input-sm' style='font-size:10.5pt' disabled>
							</div>
						</div>
						<div class='col-sm-4 col-xs-4'>	
							<div class='form-group'>
								ชำระเงินแล้ว
								<input type='text' id='SMPAY' class='form-control input-sm' style='font-size:10.5pt' disabled>
							</div>
						</div>
						<div class='col-sm-4 col-xs-4'>	
							<div class='form-group'>
								ยอดคงเหลือ
								<input type='text' id='BALANCE' class='form-control input-sm' style='font-size:10.5pt' disabled>
							</div>
						</div>
						<div class='col-sm-4 col-xs-4'>	
							<div class='form-group'>
								ยอดค้างชำระ
								<input type='text' id='NETAR' class='form-control input-sm' style='font-size:10.5pt' disabled>
							</div>
						</div>
					</div>
					</div>
					</div>
					
					<div class='form-group col-sm-10 col-xs-10 col-sm-offset-1' style='border:0.1px solid #f0f0f0;'>
					<div class='form-group col-sm-10 col-xs-10 col-sm-offset-1'>
					<div class='row'>
						<div class='col-sm-4 col-xs-4'>	
							<div class='form-group'>
								มูลค่าคงเหลือตามบัญชี
								<input type='text' id='BOOKVALUE' class='form-control input-sm' style='font-size:10.5pt' placeholder='0.00'>
							</div>
						</div>
						<div class='col-sm-4 col-xs-4'>	
							<div class='form-group'>
								ภาษีคงเหลือ
								<input type='text' id='SALEVAT' class='form-control input-sm' style='font-size:10.5pt' placeholder='0.00'>
							</div>
						</div>
						<div class='col-sm-4 col-xs-4'>	
							<div class='form-group'>
								ดอกผลเช่าซื้อคงเหลือ
								<input type='text' id='NPROFIT' class='form-control input-sm' style='font-size:10.5pt;' placeholder='0.00'>
							</div>
						</div>
						<div class='col-sm-4 col-xs-4'>	
							<div class='form-group'>
								วันที่บันทึกหนี้สูญ
								<input type='text' id='DATELOST' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' style='font-size:10.5pt' value='".$this->today('today')."'>
							</div>
						</div>
						<div class='col-sm-4 col-xs-4'>	
							<div class='form-group' >
								ประเภทหนี้สูญ
								<select id='TYPLOST' class='form-control input-sm' data-placeholder='ประเภทหนี้สูญ' ></select>
							</div>
						</div>
					</div>
					</div>
					</div>
					<div class='row'>
					<div class=' col-sm-8 col-xs-8 col-sm-offset-2'>	
							<div class='form-group'>
								หมายเหตุ
								<textarea type='text' id='MEMO' rows='1' cols='20' class='form-control input-sm' style='font-size:10.5pt'></textarea>
							</div>
						</div>	
					</div>
					<div class='row'>
						<div class=' col-sm-2 col-sm-offset-4'>	
							<div class='form-group'>
								<br>
								<button id='btnsave_arlost' class='btn btn-primary btn-block'><span class='glyphicon glyphicon-floppy-disk'> บันทึก</span></button>
							</div>
						</div>
						<div class=' col-sm-2'>	
							<div class='form-group'>
								<br>
								<input type='button' id='btndel_arlost' class='btn btn-danger btn-sm' value='ลบ' style='width:100%'>
							</div>
						</div>
					</div>
				</div>
			</div>
		";
	
		$response = array("html"=>$html);
		echo json_encode($response);
	}
	
	function searchCONTNO(){
		$contno	= $_REQUEST["contno"];

		$sql = "
				select a.CONTNO, a.CRLOCAT, isnull(a.REGNO,'') as REGNO, a.STRNO, c.SNAM, c.NAME1, c.NAME2, b.CUSCOD, b.TOTPRC, b.SMPAY+b.SMCHQ as SMPAY, 
				b.TOTPRC - b.SMPAY - b.SMCHQ as BALANC, b.EXP_AMT, b.NKANG+b.TOTDWN-b.SMPAY-b.SMCHQ as BOOKVAL, b.VKANG, d.NPROF
				from {$this->MAuth->getdb('INVTRAN')} a
				left join {$this->MAuth->getdb('ARMAST')} b on a.CONTNO = b.CONTNO 
				left join {$this->MAuth->getdb('CUSTMAST')} c on b.CUSCOD = c.CUSCOD 
				left join (
					select CONTNO, convert(decimal(12,2),round(SUM(NPROF),2)) AS NPROF 
					FROM( 
						select CONTNO, CASE WHEN PAYMENT > 0 THEN (NPROF/DAMT)*PAYMENT ELSE NPROF END AS  NPROF  
						FROM {$this->MAuth->getdb('ARPAY')}  WHERE CONTNO = '".$contno."' and PAYMENT < DAMT
					)A
					group by CONTNO
				)d on a.CONTNO = d.CONTNO 
				where a.CONTNO = '".$contno."' 
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$response = array();
		if($query->row()){
			foreach($query->result() as $row){
				$response["CONTNO"] 	= $row->CONTNO;
				$response["CRLOCAT"] 	= $row->CRLOCAT;
				$response["CUSNAME"] 	= $row->SNAM.$row->NAME1.' '.$row->NAME2;
				$response["CUSCOD"] 	= $row->CUSCOD;
				$response["REGNO"] 		= $row->REGNO;
				$response["STRNO"] 		= str_replace(chr(0),'',$row->STRNO);
				$response["TOTPRC"] 	= number_format($row->TOTPRC,2);
				$response["SMPAY"] 		= number_format($row->SMPAY,2);
				$response["BALANCE"] 	= number_format($row->BALANC,2);
				$response["EXP_AMT"] 	= number_format($row->EXP_AMT,2);
				$response["BOOKVALUE"]	= number_format($row->BOOKVAL,2);
				$response["VATPRC"] 	= number_format($row->VKANG,2);
				$response["NPROF"]  	= number_format($row->NPROF,2);
			}
		}
		
		echo json_encode($response);
	}
	
	function search(){
		$LOCAT1	= $_REQUEST["LOCAT1"];
		$TYPLOST1 = $_REQUEST["TYPLOST1"];
		$FROMDATE = $_REQUEST["FROMDATE"];
		$TODATE = $_REQUEST["TODATE"];

		$cond = "";
		$rpcond = "";
		if($LOCAT1 != ""){
			$cond .= " and a.LOCAT = '".$LOCAT1."'";
			$rpcond .= " สาขา  ".$LOCAT1;
		}
		
		if($TYPLOST1 != ""){
			$cond .= " and a.LOSTCOD = '".$TYPLOST1."'";
			$rpcond .= " ประเภทหนี้สูญ  ".$TYPLOST1;
		}
		
		if($FROMDATE != ""){
			$cond .= " and a.LOSTDT >= '".$this->Convertdate(1,$FROMDATE)."'";
			$rpcond .= " จากวันที่ตั้งหนี้สูญ  ".$FROMDATE;
		}
		
		if($TODATE != ""){
			$cond .= " and a.LOSTDT <= '".$this->Convertdate(1,$TODATE)."'";
			$rpcond .= " ถึงวันที่  ".$TODATE;
		}
		
		$sql = "
				select  ".($cond == '' ? 'top 500':'')." a.LOCAT, a.CONTNO, a.CUSCOD, b.SNAM+b.NAME1+' '+b.NAME2 as CUSNAME, a.STRNO, isnull(a.REGNO,'') as REGNO, 
				convert(nvarchar,DATEADD(year,543,a.SDATE),103) as SDATE, c.GARNO as GARNO1, c.CUSCOD as GARCOD1, d.SNAM+d.NAME1+' '+d.NAME2 as GARNAME1, c.RELATN as RELATN1, 
				e.GARNO as GARNO2, e.CUSCOD as GARCOD2, f.SNAM+f.NAME1+' '+f.NAME2 as GARNAME2, e.RELATN as RELATN2, convert(nvarchar,DATEADD(year,543,a.LOSTDT),103) as LOSTDTS, 
				a.LOSTCOD, g.LOSTESC, a.TOTPRC, a.TOTBAL, 
				a.SMPAY, a.EXP_AMT, a.BOOKVAL, a.BOOKVAT, a.BALPROF, a.MEMO1
				from {$this->MAuth->getdb('ARLOST')} a
				left join {$this->MAuth->getdb('CUSTMAST')} b on a.CUSCOD = b.CUSCOD
				left join (
					select * from {$this->MAuth->getdb('HARMGAR')} where GARNO in ('1') 
				) c on a.CONTNO = c.CONTNO and a.LOCAT = c.LOCAT
				left join {$this->MAuth->getdb('CUSTMAST')} d on c.CUSCOD = d.CUSCOD
				left join (
					select * from {$this->MAuth->getdb('HARMGAR')} where GARNO in ('2') 
				)e on a.CONTNO = e.CONTNO and a.LOCAT = e.LOCAT
				left join {$this->MAuth->getdb('CUSTMAST')} f on e.CUSCOD = f.CUSCOD
				left join {$this->MAuth->getdb('TYPLOST')} g on a.LOSTCOD = g.LOSTCOD
				where 1=1 ".$cond."
				order by a.LOSTDT desc, a.LOCAT
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$sql2 = "
				select 'รวมทั้งหมด' as Total, SUM(TOTPRC) as sumTOTPRC, SUM(TOTBAL) as sumTOTBAL
				from {$this->MAuth->getdb('ARLOST')} a
				where 1=1 ".$cond."
		";
		//echo $sql2; exit;
		$query2 = $this->db->query($sql2);
		
		$head = ""; $html = ""; $report = ""; $sumreport = ""; $i=0; $ii=0;
	
		$head = "<tr>
				<th style='vertical-align:middle;'>#</th>
				<th style='vertical-align:middle;'>สาขา</th>
				<th style='vertical-align:middle;'>เลขที่สัญญา</th>
				<th style='vertical-align:middle;'>รหัสลูกค้า</th>
				<th style='vertical-align:middle;'>ชื่อ - นามสกุล</th>
				<th style='vertical-align:middle;'>เลขตัวถัง</th>
				<th style='vertical-align:middle;'>วันที่ขาย</th>
				<th style='vertical-align:middle;'>คนค้ำที่</th>
				<th style='vertical-align:middle;'>รหัสคนค้ำ</th>
				<th style='vertical-align:middle;'>ชื่อ - นามสกุล คนค้ำ</th>
				<th style='vertical-align:middle;'>ความสัมพันธ์</th>
				<th style='vertical-align:middle;'>วันที่บันทึกหนี้สูญ</th>
				<th style='vertical-align:middle;'>ประเภทหนี้สูญ</th>
				<th style='vertical-align:middle;'>ราคาขาย</th>
				<th style='vertical-align:middle;'>ลูกหนี้คงเหลือ</th>
				</tr>
		";
		
		$NRow = 1;
		if($query->row()){
			foreach($query->result() as $row){$i++;
				$bgcolor="";
				$html .= "
					<tr class='trow' seq=".$NRow." style='".$bgcolor."'>
						<td class='getit' seq=".$NRow++."  style='cursor:pointer;text-align:center;'
						CONTNO	= '".str_replace(chr(0),'',$row->CONTNO)."' 
						LOCAT	= '".str_replace(chr(0),'',$row->LOCAT)."' 
						CUSCOD	= '".str_replace(chr(0),'',$row->CUSCOD)."' 
						CUSNAME	= '".$row->CUSNAME."'
						REGNO	= '".str_replace(chr(0),'',$row->REGNO)."' 
						STRNO	= '".str_replace(chr(0),'',$row->STRNO)."' 
						TOTPRC	= '".number_format($row->TOTPRC,2)."'
						SMPAY	= '".number_format($row->SMPAY,2)."'
						TOTBAL	= '".number_format($row->TOTBAL,2)."'
						EXP_AMT	= '".number_format($row->EXP_AMT,2)."'
						BOOKVAL	= '".number_format($row->BOOKVAL,2)."'
						BOOKVAT = '".number_format($row->BOOKVAT,2)."'
						BALPROF = '".number_format($row->BALPROF,2)."'
						LOSTDTS	= '".$row->LOSTDTS."'
						LOSTCOD	= '".str_replace(chr(0),'',$row->LOSTCOD)."' 
						LOSTESC	= '".str_replace(chr(0),'',$row->LOSTESC)."' 
						MEMO1	= '".$row->MEMO1."'
						><b>เลือก</b></td>
						<td align='center'>".$row->LOCAT."</td>
						<td>".$row->CONTNO."</td>
						<td>".$row->CUSCOD."</td>
						<td>".($row->CUSNAME)."</td>
						<td>".$row->STRNO."</td>
						<td align='center'>".$row->SDATE."</td>
						<td align='center'>".$row->GARNO1."<br>".$row->GARNO2."</td>
						<td align='center'>".$row->GARCOD1."<br>".$row->GARCOD2."</td>
						<td>".$row->GARNAME1."<br>".$row->GARNAME2."</td>
						<td>".$row->RELATN1."<br>".$row->RELATN2."</td>
						<td align='center'>".$row->LOSTDTS."</td>
						<td align='center'>".$row->LOSTCOD.' - '.$row->LOSTESC."</td>
						<td align='right'>".number_format($row->TOTPRC,2)."</td>
						<td align='right'>".number_format($row->TOTBAL,2)."</td>
					</tr>
				";	
			}
		}
		
		$No = 1;
		if($query->row()){
			foreach($query->result() as $row){
				$report .= "
					<tr class='trow' seq=".$No.">
						<td style='mso-number-format:\"\@\";text-align:center;vertical-align:top;'>".$No++."</td>
						<td style='mso-number-format:\"\@\";text-align:center;vertical-align:top;'>".$row->LOCAT."</td>
						<td style='mso-number-format:\"\@\";text-align:center;vertical-align:top;'>".$row->CONTNO."</td>
						<td style='mso-number-format:\"\@\";text-align:center;vertical-align:top;'>".$row->CUSCOD."</td>
						<td style='mso-number-format:\"\@\";vertical-align:top;'>".$row->CUSNAME."</td>
						<td style='mso-number-format:\"\@\";vertical-align:top;'>".$row->STRNO."</td>
						<td style='mso-number-format:\"\@\";text-align:center;vertical-align:top;'>".$row->SDATE."</td>
						<td style='mso-number-format:\"\@\";text-align:center;'>".$row->GARNO1."<br>".$row->GARNO2."</td>
						<td style='mso-number-format:\"\@\";text-align:center;'>".$row->GARCOD1."<br>".$row->GARCOD2."</td>
						<td style='mso-number-format:\"\@\";'>".$row->GARNAME1."<br>".$row->GARNAME2."</td>
						<td style='mso-number-format:\"\@\";'>".$row->RELATN1."<br>".$row->RELATN2."</td>
						<td style='mso-number-format:\"\@\";text-align:center;vertical-align:top;'>".$row->LOSTDTS."</td>
						<td style='mso-number-format:\"\@\";vertical-align:top;'>".$row->LOSTCOD.' - '.$row->LOSTESC."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;vertical-align:top;'>".number_format($row->TOTPRC,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;vertical-align:top;'>".number_format($row->TOTBAL,2)."</td>
					</tr>
				";	
			}
		}
		
		if($query2->row()){
			foreach($query2->result() as $row){
				$sumreport .= "
					<tr class='trow'>
						<th colspan='13' align='right'>".$row->Total."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumTOTPRC,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumTOTBAL,2)."</th>
					</tr>
				";	
			}
		}
		
		if($i>0){
			$html = "
				<div id='table-fixed-ARlost' class='col-sm-12' style='height:100%;width:100%;overflow:auto;'>
					<table id='table-ARlost' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='calc(100% - 1px)'>
						<thead>
							".$head."
						</thead>	
						<tbody>
							".$html."
						</tbody>
					</table>
				</div>
				<!--input type='image' id='table-ARlost-excel' onclick=\"tableToExcel('table-ARlost2', 'Export Table')\" value='Export to Excell' alt='Export to Excell' style='width:50px;height:55px;cursor:pointer;' src='".base_url('public/images/excel-icon.png')."'--!>
			";
		}else{
			$html="<font style='color:red;'>ไม่มีข้อมูล</font>";
		}
		
		$report = "
			<div id='table-fixed-ARlost2' class='col-sm-12' style='height:100%;width:100%;overflow:auto;'>
				<table id='table-ARlost2' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='100%'>
					<thead>
						<tr>
							<th colspan='15' style='font-size:12pt;border:0px;text-align:center;'>รายงานการบันทึกหนี้สงสัยจะสูญ</th>
						</tr>
						<tr>
							<td colspan='15' style='border:0px;text-align:center;'>".$rpcond."  ออกรายงาน ณ วันที่ ".$this->today('today')."</td>
						</tr>
						".$head."
					</thead>	
					<tbody>
						".$report."
						".$sumreport."
					</tbody>
				</table>
			</div>
		";
		
		$response = array("html"=>$html, "report"=>$report);
		echo json_encode($response);
	}
	
	function conditiontopdf(){
		$data = array();
		$data[] = urlencode($_REQUEST["LOCAT1"].'||'.$_REQUEST["TYPLOST1"].'||'.$_REQUEST["FROMDATE"].'||'.$_REQUEST["TODATE"]);
		//echo urlencode($_REQUEST["TRANSNO"]); exit;
		echo json_encode($this->generateData($data,"encode"));
	}
	
	function pdf(){
		$data = array();
		$data[] = $_GET["condpdf"];
		$arrs = $this->generateData($data,"decode");
		$arrs[0] = urldecode($arrs[0]);
		
		$tx = explode("||",$arrs[0]);
		$locat = $tx[0];
		$typelost = str_replace(chr(0),'',$tx[1]);
		$fromdate = $tx[2];
		$todate = $tx[3];

		$cond = "";
		$rpcond = "";
		if($locat != ""){
			$cond .= " and a.LOCAT = '".$locat."'";
			$rpcond .= " สาขา  ".$locat;
		}
		
		if($typelost != ""){
			$cond .= " and a.LOSTCOD = '".$typelost."'";
			$rpcond .= " ประเภทหนี้สูญ  ".$typelost;
		}
		
		if($fromdate != ""){
			$cond .= " and a.LOSTDT >= '".$this->Convertdate(1,$fromdate)."'";
			$rpcond .= " จากวันที่ตั้งหนี้สูญ  ".$fromdate;
		}
		
		if($todate != ""){
			$cond .= " and a.LOSTDT <= '".$this->Convertdate(1,$todate)."'";
			$rpcond .= " ถึงวันที่  ".$todate;
		}

		$sql = "
				select  ".($cond == '' ? 'top 500':'')." a.LOCAT, a.CONTNO, a.CUSCOD, b.SNAM+b.NAME1+' '+b.NAME2 as CUSNAME, a.STRNO, isnull(a.REGNO,'') as REGNO, 
				convert(nvarchar,DATEADD(year,543,a.SDATE),103) as SDATE, c.GARNO as GARNO1, c.CUSCOD as GARCOD1, d.SNAM+d.NAME1+' '+d.NAME2 as GARNAME1, c.RELATN as RELATN1, 
				e.GARNO as GARNO2, e.CUSCOD as GARCOD2, f.SNAM+f.NAME1+' '+f.NAME2 as GARNAME2, e.RELATN as RELATN2, convert(nvarchar,DATEADD(year,543,a.LOSTDT),103) as LOSTDTS, 
				a.LOSTCOD, g.LOSTESC, a.TOTPRC, a.TOTBAL, 
				a.SMPAY, a.EXP_AMT, a.BOOKVAL, a.BOOKVAT, a.BALPROF, a.MEMO1
				from {$this->MAuth->getdb('ARLOST')} a
				left join {$this->MAuth->getdb('CUSTMAST')} b on a.CUSCOD = b.CUSCOD 
				left join (
					select * from {$this->MAuth->getdb('HARMGAR')} where GARNO in ('1') 
				) c on a.CONTNO = c.CONTNO and a.LOCAT = c.LOCAT
				left join {$this->MAuth->getdb('CUSTMAST')} d on c.CUSCOD = d.CUSCOD 
				left join (
					select * from {$this->MAuth->getdb('HARMGAR')} where GARNO in ('2') 
				)e on a.CONTNO = e.CONTNO and a.LOCAT = e.LOCAT
				left join {$this->MAuth->getdb('CUSTMAST')} f on e.CUSCOD = f.CUSCOD
				left join {$this->MAuth->getdb('TYPLOST')} g on a.LOSTCOD = g.LOSTCOD
				where 1=1 ".$cond."
				order by a.LOSTDT desc, a.LOCAT
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$sql2 = "
				select 'รวมทั้งหมด' as Total, SUM(TOTPRC) as sumTOTPRC, SUM(TOTBAL) as sumTOTBAL
				from {$this->MAuth->getdb('ARLOST')} a
				where 1=1 ".$cond."
		";
		//echo $sql2; exit;
		$query2 = $this->db->query($sql2);
		
		$head = ""; $html = ""; $report = ""; $sumreport = ""; $i=0; $ii=0;
	
		$head = "<tr class='trow'>
				<th style='text-align:left;border-bottom:0.1px solid black;'>#</th>
				<th style='text-align:left;border-bottom:0.1px solid black;'>สาขา</th>
				<th style='text-align:left;border-bottom:0.1px solid black;'>เลขที่สัญญา</th>
				<th style='text-align:left;border-bottom:0.1px solid black;'>รหัสลูกค้า</th>
				<th style='text-align:left;border-bottom:0.1px solid black;'>ชื่อ - นามสกุล ลูกค้า</th>
				<th style='text-align:left;border-bottom:0.1px solid black;'>เลขตัวถัง</th>
				<th style='text-align:left;border-bottom:0.1px solid black;'>วันที่ขาย</th>
				<th style='text-align:center;border-bottom:0.1px solid black;'>คนค้ำที่</th>
				<th style='text-align:left;border-bottom:0.1px solid black;'>ชื่อ - นามสกุล คนค้ำ</th>
				<th style='text-align:left;border-bottom:0.1px solid black;'>วันที่ตั้งหนี้สูญ</th>
				<th style='text-align:left;border-bottom:0.1px solid black;'>ประเภทหนี้สูญ</th>
				<th style='text-align:right;border-bottom:0.1px solid black;'>ราคาขาย</th>
				<th style='text-align:right;border-bottom:0.1px solid black;'>ลูกหนี้คงเหลือ</th>
				</tr>
		";
		
		$No = 1;
		if($query->row()){
			foreach($query->result() as $row){
				$report .= "
					<tr class='trow' seq=".$No.">
						<td style='vertical-align:top;width:20px;'>".$No++."</td>
						<td style='vertical-align:top;width:40px;'>".$row->LOCAT."</td>
						<td style='vertical-align:top;width:85px;'>".$row->CONTNO."</td>
						<td style='vertical-align:top;width:85px;'>".$row->CUSCOD."</td>
						<td style='vertical-align:top;width:145px;'>".$row->CUSNAME."</td>
						<td style='vertical-align:top;width:145px;'>".$row->STRNO."</td>
						<td style='vertical-align:top;width:60px;'>".$row->SDATE."</td>
						<td style='text-align:center;width:20px;'>".$row->GARNO1."<br>".$row->GARNO2."</td>
						<td style='width:145px;'>".$row->GARNAME1."<br>".$row->GARNAME2."</td>
						<td style='vertical-align:top;width:60px;'>".$row->LOSTDTS."</td>
						<td style='vertical-align:top;width:30px;'>".$row->LOSTCOD."</td>
						<td style='text-align:right;vertical-align:top;width:50px;'>".number_format($row->TOTPRC,2)."</td>
						<td style='text-align:right;vertical-align:top;width:50px;'>".number_format($row->TOTBAL,2)."</td>
					</tr>
				";	
			}
		}
		
		if($query2->row()){
			foreach($query2->result() as $row){
				$sumreport .= "
					<tr class='trow bor' style='background-color:#ebebeb;'>
						<th colspan='11' align='right'>".$row->Total."</th>
						<th style='text-align:right;'>".number_format($row->sumTOTPRC,2)."</th>
						<th style='text-align:right;'>".number_format($row->sumTOTBAL,2)."</th>
					</tr>
				";	
			}
		}
		
		$mpdf = new \Mpdf\Mpdf([
			'mode' => 'utf-8', 
			'format' => 'A4-L',
			'margin_top' => 10, 	//default = 16
			'margin_left' => 10, 	//default = 15
			'margin_right' => 10, 	//default = 15
			'margin_bottom' => 10, 	//default = 16
			'margin_header' => 9, 	//default = 9
			'margin_footer' => 9, 	//default = 9
		]);
		
		$content = "
			<table class='wf' style='font-size:8pt;height:700px;border-collapse:collapse;line-height:23px;overflow:wrap;vertical-align:text-top;'>
				<tbody>
					<tr>
						<th colspan='13' style='font-size:10pt;height:30px;'>รายงานการบันทึกหนี้สงสัยจะสูญ </th>
					</tr>
					<tr>
						<td colspan='13' style='font-size:8pt;height:35px;border-bottom:0.1px solid black;text-align:center;'>".$rpcond."</td>
					</tr>
					".$head."
					".$report."
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
			<div class='wf pf' style='top:715;left:960;font-size:6pt;'>".date('d/m/').(date('Y')+543)." ".date('H:i')." หน้า {PAGENO} / {nbpg}</div>
		";
		//<div class='wf pf' style='top:1050;left:580;'>{DATE j-m-Y H:s}  {PAGENO} / {nbpg}</div>
		//$mpdf->AddPage('L');	
		$mpdf->SetHTMLHeader($head);	
		$mpdf->WriteHTML($content);	
		$mpdf->Output();
		
	}
	
	function Save_ARlost(){
		$CONTNO 	= $_REQUEST["CONTNO"];
		$STRNO 		= $_REQUEST["STRNO"];
		$TYPLOST	= str_replace(chr(0),'',$_REQUEST["TYPLOST"]);
		$BOOKVAL	= str_replace(',','',$_REQUEST["BOOKVAL"]);
		$SALEVAT	= ($_REQUEST["SALEVAT"] == '' ? 0: str_replace(',','',$_REQUEST["SALEVAT"]));
		$NPROFIT	= str_replace(',','',$_REQUEST["NPROFIT"]);
		$DATELOST	= $this->Convertdate(1,$_REQUEST["DATELOST"]);
		$MEMO		= $_REQUEST["MEMO"];
		$USERID		= $this->sess["USERID"];
		
		if($MEMO == ''){
			$MEMO = 'NULL';
		}else{
			$MEMO = "'".$MEMO."'";
		}//echo $MEMO; exit;

		$sql = "
			if OBJECT_ID('tempdb..#AddARLOSTTemp') is not null drop table #AddARLOSTTemp;
			create table #AddARLOSTTemp (id varchar(20),contno varchar(20),msg varchar(max));
			
			begin tran AddARLOSTTemp
			begin try
			
				declare @CONTNO varchar(20) 	= '".$CONTNO."'
				declare @STRNO varchar(max) 	= '".$STRNO."'
				
				--บันทึกลง ARLOST
				insert into {$this->MAuth->getdb('ARLOST')} 
				select 
				a.CRLOCAT,	a.CONTNO, b.CUSCOD,	c.SNAM, c.NAME1, c.NAME2, a.STRNO, a.REGNO, a.SDATE, b.TOTPRC, b.NPRICE, b.VATPRC, b.SMPAY, b.SMCHQ,
				b.TOTPRC-b.SMPAY-b.SMCHQ as TOTBAL, b.TOTPRC-b.SMPAY-b.SMCHQ as NETBAL, 0 as VATBAL, b.EXP_AMT, ".$BOOKVAL." as BOOKVAL, 
				".$SALEVAT." as BOOKVAT, ".$BOOKVAL." as N_NETCST, ".$SALEVAT." as N_NETVAT, ".($BOOKVAL + $SALEVAT)." as N_NETTOT, '".$DATELOST."' as LOSTDT, 
				'".$TYPLOST."' as LOSTCOD, b.BILLCOLL, b.CHECKER, 'L' as FLAG, ".$MEMO." as MEMO1, d.NPROF as BALPROF, b.VKANG as BALVAT, b.NPROFIT, 0 as SMPRIN_EFF,
				0 as BALPRIN_EFF, 0 as SMPROF_EFF, 0 as BALPROF_EFF, 0 as EXP_VAT, 0 as EXP_NET, 0 as EXP_EFF, '' as PROF_METHOD, '' as N_GCODE, 0 as VATRT, 
				0 as TKANG, 0 NKANG, 0 as VKANG, 0 as NCSHPRC, 0 as VATBALDUE, 0 as SMNETPAY, 0 as SMVATPAY, 0 as SMPRIN_SYD, 0 as SMPROF_SYD, 0 as SMPRIN_STR, 
				0 as SMPROF_STR, 0 as BALPROF_SYD, 0 as BALPROF_STR, 0 as BALPRIN_SYD, 0 as BALPRIN_STR, 0 as STDPRC, 0 as VOUCHER, null as VOUCHDT, '".$USERID."' as USERID, 
				null as POSTDT, 0 as O_GCODE, 0 as YLOCAT, 0 as Y_USER, 0 as TYPHOLD, GETDATE() as INPDT
				from {$this->MAuth->getdb('INVTRAN')} a
				left join {$this->MAuth->getdb('ARMAST')} b on a.CONTNO = b.CONTNO
				left join {$this->MAuth->getdb('CUSTMAST')} c on b.CUSCOD = c.CUSCOD
				left join (
					SELECT CONTNO, convert(decimal(12,2),round(SUM(NPROF),2)) AS NPROF 
					FROM( 
						SELECT CONTNO, CASE WHEN PAYMENT > 0 THEN (NPROF/DAMT)*PAYMENT ELSE NPROF END AS  NPROF  
						FROM {$this->MAuth->getdb('ARPAY')}  WHERE CONTNO = @CONTNO and PAYMENT < DAMT
					)A
					group by CONTNO
				)d on a.CONTNO = d.CONTNO
				where a.CONTNO = '".$CONTNO."' and a.STRNO = '".$STRNO."'

				--บันทึกลง H
				insert into {$this->MAuth->getdb('HINVTRAN')} 	select * from {$this->MAuth->getdb('INVTRAN')} 	where CONTNO = '".$CONTNO."' and STRNO = '".$STRNO."'
				insert into {$this->MAuth->getdb('HARMAST')}	select * from {$this->MAuth->getdb('ARMAST')} 	where CONTNO = '".$CONTNO."' and STRNO = '".$STRNO."'
				insert into {$this->MAuth->getdb('HARPAY')} 	select * from {$this->MAuth->getdb('ARPAY')} 	where CONTNO = '".$CONTNO."' 
				insert into {$this->MAuth->getdb('HARMGAR')} 	select * from {$this->MAuth->getdb('ARMGAR')} 	where CONTNO = '".$CONTNO."' 

				--อัพเดท HARMAST
				UPDATE {$this->MAuth->getdb('HARMAST')} SET CLOSDT = '".$DATELOST."' WHERE CONTNO = '".$CONTNO."' and STRNO = '".$STRNO."'

				--ลบ 
				delete {$this->MAuth->getdb('INVTRAN')}	where CONTNO = '".$CONTNO."' and STRNO = '".$STRNO."' 
				delete {$this->MAuth->getdb('ARMAST')} 	where CONTNO = '".$CONTNO."' and STRNO = '".$STRNO."' 
				delete {$this->MAuth->getdb('ARPAY')} 	where CONTNO = '".$CONTNO."' 
				delete {$this->MAuth->getdb('ARMGAR')} 	where CONTNO = '".$CONTNO."' 
				
				insert into #AddARLOSTTemp select 'S',@CONTNO,'บันทึกรายการหนี้สงสัยจะสูญ  เลขที่สัญญา '+@CONTNO+' เรียบร้อย';
					
				commit tran AddARLOSTTemp;
			end try
			begin catch
				rollback tran AddARLOSTTemp;
				insert into #AddARLOSTTemp select 'E','',ERROR_MESSAGE();
			end catch
		";
		//echo $sql; exit;
		
		$this->db->query($sql);
		$sql = "select * from #AddARLOSTTemp";
		$query = $this->db->query($sql);
	  
		if($query->row()){
			foreach($query->result() as $row){
				$response["status"] = $row->id;
				$response["contno"] = $row->contno;
				$response["msg"] = $row->msg;
			}
		}else{
			$response["status"] = false;
			$response["contno"] = '';
			$response["msg"] = 'ผิดพลาดไม่สามารถบันทึกรายการหนี้สงสัยจะสูญได้ โปรดติดต่อฝ่ายไอที';
		}
		
		echo json_encode($response);
	}
	
	function Edit_arlost(){
		$CONTNO 	= $_REQUEST["CONTNO"];
		$STRNO 		= $_REQUEST["STRNO"];
		$TYPLOST	= str_replace(chr(0),'',$_REQUEST["TYPLOST"]);
		$BOOKVAL	= str_replace(',','',$_REQUEST["BOOKVAL"]);
		$SALEVAT	= ($_REQUEST["SALEVAT"] == '' ? 0: str_replace(',','',$_REQUEST["SALEVAT"]));
		$NPROFIT	= str_replace(',','',$_REQUEST["NPROFIT"]);
		$DATELOST	= $this->Convertdate(1,$_REQUEST["DATELOST"]);
		$MEMO		= $_REQUEST["MEMO"];
		//$CUSNAME= $_REQUEST["CUSNAME"];
		
		$sql = "
			if OBJECT_ID('tempdb..#EditARLOSTTemp') is not null drop table #EditARLOSTTemp;
			create table #EditARLOSTTemp (id varchar(20),contno varchar(20),msg varchar(max));
			
			begin tran EditARLOSTTemp
			begin try
				
				declare @CONTNO varchar(max) = '".$CONTNO."';
				declare @STRNO varchar(max) = '".$STRNO."';
				
				update {$this->MAuth->getdb('ARLOST')} 
				set BOOKVAL = ".$BOOKVAL.", BOOKVAT = ".$SALEVAT.", N_NETCST = ".$BOOKVAL.", N_NETVAT = ".$SALEVAT.", N_NETTOT = ".($BOOKVAL + $SALEVAT).",
				BALPROF = ".$NPROFIT.", LOSTDT = '".$DATELOST."', LOSTCOD = '".$TYPLOST."', MEMO1 = '".$MEMO."'
				where CONTNO like @CONTNO and STRNO like @STRNO
				
				insert into #EditARLOSTTemp select 'S',@CONTNO,'แก้ไขรายการตั้งหนี้สงสัยจะสูญ เลขที่สัญญา '+@CONTNO+' เรียบร้อย';

				commit tran EditARLOSTTemp;
			end try
			begin catch
				rollback tran EditARLOSTTemp;
				insert into #EditARLOSTTemp select 'E','',ERROR_MESSAGE();
			end catch
		";
		//echo $sql; exit;
		
		$this->db->query($sql);
		$sql = "select * from #EditARLOSTTemp";
		$query = $this->db->query($sql);
	  
		if($query->row()){
			foreach($query->result() as $row){
				$response["status"] = $row->id;
				$response["contno"] = $row->contno;
				$response["msg"] = $row->msg;
			}
		}else{
			$response["status"] = false;
			$response["contno"] = '';
			$response["msg"] = 'ผิดพลาดไม่สามารถแก้ไขรายการตั้งหนี้สงสัยจะสูญได้ โปรดติดต่อฝ่ายไอที';
		}
		
		echo json_encode($response);
	}
	
	function Delete_arlost(){
		$CONTNO	= $_REQUEST["CONTNO"];
		$CUSCOD	= $_REQUEST["CUSCOD"];
		$STRNO	= $_REQUEST["STRNO"];
		//$CUSNAME= $_REQUEST["CUSNAME"];
		
		$sql = "
			if OBJECT_ID('tempdb..#DelARLOSTTemp') is not null drop table #DelARLOSTTemp;
			create table #DelARLOSTTemp (id varchar(20),contno varchar(20),msg varchar(max));
			
			begin tran DelARLOSTTemp
			begin try
				
				declare @CONTNO varchar(max) = '".$CONTNO."';
				declare @STRNO varchar(max) = '".$STRNO."';
				
				-- ลบARLOST
				delete {$this->MAuth->getdb('ARLOST')}  
				where CONTNO = @CONTNO

				-- insert H กลับที่เดิม
				insert into {$this->MAuth->getdb('INVTRAN')} select * from {$this->MAuth->getdb('HINVTRAN')} 	where CONTNO = @CONTNO and STRNO = @STRNO
				insert into {$this->MAuth->getdb('ARMAST')} select * from {$this->MAuth->getdb('HARMAST')} 		where CONTNO = @CONTNO and STRNO = @STRNO
				insert into {$this->MAuth->getdb('ARPAY')} select * from {$this->MAuth->getdb('HARPAY')} 		where CONTNO = @CONTNO 
				insert into {$this->MAuth->getdb('ARMGAR')} select * from {$this->MAuth->getdb('HARMGAR')} 		where CONTNO = @CONTNO 

				-- ลบ H
				delete {$this->MAuth->getdb('HINVTRAN')} 	where CONTNO = @CONTNO and STRNO = @STRNO
				delete {$this->MAuth->getdb('HARMAST')} 	where CONTNO = @CONTNO and STRNO = @STRNO
				delete {$this->MAuth->getdb('HARPAY')} 		where CONTNO = @CONTNO 
				delete {$this->MAuth->getdb('HARMGAR')} 	where CONTNO = @CONTNO 
				
				insert into #DelARLOSTTemp select 'S',@CONTNO,'ลบรายการตั้งหนี้สงสัยจะสูญ เลขที่สัญญา '+@CONTNO+' เรียบร้อย';

				commit tran DelARLOSTTemp;
			end try
			begin catch
				rollback tran DelARLOSTTemp;
				insert into #DelARLOSTTemp select 'E','',ERROR_MESSAGE();
			end catch
		";
		//echo $sql; exit;
		
		$this->db->query($sql);
		$sql = "select * from #DelARLOSTTemp";
		$query = $this->db->query($sql);
	  
		if($query->row()){
			foreach($query->result() as $row){
				$response["status"] = $row->id;
				$response["contno"] = $row->contno;
				$response["msg"] = $row->msg;
			}
		}else{
			$response["status"] = false;
			$response["contno"] = '';
			$response["msg"] = 'ผิดพลาดไม่สามารถลบรายการตั้งหนี้สงสัยจะสูญได้ โปรดติดต่อฝ่ายไอที';
		}
		
		echo json_encode($response);
	}
	
}