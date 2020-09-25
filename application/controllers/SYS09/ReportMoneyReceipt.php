<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/********************************************************
             ______@30/07/2020______
			 Pasakorn Boonded

********************************************************/
class ReportMoneyReceipt extends MY_Controller {
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
	function index(){
		$claim = $this->MLogin->getclaim(uri_string());
		if($claim['m_access'] != "T"){ echo "<div align='center' style='color:red;font-size:16pt;width:100%;'>ขออภัย คุณยังไม่มีสิทธิเข้าใช้งานหน้านี้ครับ</div>"; exit; }
		$html = "
			<div class='k_tab1' name='home' locat='{$this->sess['branch']}' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}' today='".$this->today('today')."' style='height:calc(100vh - 132px);overflow:auto;background-color:white;'>
				<div class='col-sm-12 col-xs-12' style='background-color:#0480E0;border:5px solid white;height:75px;text-align:center;font-size:12pt;color:white;font-weight:bold;'>	
					<br>รายงานสรุปการรับเงินตามวันที่ใบเสร็จ<br>
				</div>
				<div class='col-sm-8 col-xs-8 col-sm-offset-2'>
					<div class='col-sm-6 col-xs-6'>
						<div class='form-group'>
							<div class='col-sm-12'>	
								<div class='form-group'>
									รับชำระที่สาขา
									<select id='LOCATRECV' class='form-control input-sm'></select>
								</div>
							</div>
							<div class='col-sm-12'>	
								<div class='form-group'>
									จากวันที่
									<input type='text' id='TMBILDT_F' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' value='".$this->today('today')."' styl='font-size:10.5pt;'>
								</div>
							</div>
							<div class='col-sm-12'>	
								<div class='form-group'>
									ถึงวันที่
									<input type='text' id='TMBILDT_T' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' value='".$this->today('today')."' styl='font-size:10.5pt;'>
								</div>
							</div>							
						</div>
					</div>
					<div class='col-sm-6 col-xs-6'>
						<div class='form-group'>
							<div class='col-sm-12'>	
								<div class='form-group'>
									ชำระเพื่อ บ/ช ของสาขา
									<select id='LOCATPAY' class='form-control input-sm'></select>
								</div>
							</div>
							<div class='col-sm-12'>	
								<div class='form-group'>
									ประเภทการรับชำระ
									<select id='PAYTYP' class='form-control input-sm'></select>
								</div>
							</div>
							<div class='col-sm-12'>	
								<div class='form-group'>
									รหัสพนักงานรับเงิน
									<select id='CODE' class='form-control input-sm'></select>
								</div>
							</div>						
						</div>
					</div>
				</div>
				<div class='col-sm-8 col-xs-8 col-sm-offset-2'>
					<br>
					<button id='btnreport' type='button' class='btn btn-info btn-outline btn-block' style='height:40px;'><span class='fa fa-folder-open'><b>แสดง</b></span></button>
				</div>
				<div class='col-sm-12'>
					<div class='col-sm-12'>
						<div id='result'></div>
					</div>
				</div>
			</div>
		";
		$html .="<script src='".base_url('public/js/SYS09/ReportMoneyReceipt.js')."'></script>";
		echo $html;
	}
	function conditiontopdf(){
		$data 	= 	array();
		$data[] = 	urlencode(
			$_REQUEST["LOCATRECV"].'||'.$_REQUEST["TMBILDT_F"].'||'.$_REQUEST["TMBILDT_T"].'||'.
			$_REQUEST["LOCATPAY"].'||'.$_REQUEST["PAYTYP"].'||'.$_REQUEST["CODE"]
		);
		echo json_encode($this->generateData($data,"encode"));
	}
	function pdf(){
		$data 	= array();
		$data[] = $_GET["condpdf"];
		$arrs 	= $this->generateData($data,"decode");
		$arrs[0]= urldecode($arrs[0]);
		$tx 	= explode("||",$arrs[0]);
		
		$LOCATRECV	= $tx[0];
		$TMBILDT_F	= $this->Convertdate(1,$tx[1]);
		$TMBILDT_T	= $this->Convertdate(1,$tx[2]);
		$LOCATPAY	= $tx[3];
		$PAYTYP		= $tx[4];
		$CODE		= $tx[5];
		
		$sql = "
			select COMP_NM from {$this->MAuth->getdb('CONDPAY')}
		";
		$query = $this->db->query($sql);
		$row1		= $query->row();
		$COMP_NM 	= $row1->COMP_NM;
		
		$head = ""; $html = ""; $i=0; 
		
		$head = "
			<tr>
				<th width='40px'  align='center' style='border-top:0.1px solid black;vertical-align:top;border-bottom:0.1px solid black;'>No.</th>
				<th width='60px'  align='left'	 style='border-top:0.1px solid black;vertical-align:top;border-bottom:0.1px solid black;'>สาขา</th>
				<th width='100px' align='left'   style='border-top:0.1px solid black;vertical-align:top;border-bottom:0.1px solid black;'>รหัสพนักงาน</th>
				<th width='200px' align='left'   style='border-top:0.1px solid black;vertical-align:top;border-bottom:0.1px solid black;'>ชื่อ - สกุล</th>
				<th width='100px' align='right'	 style='border-top:0.1px solid black;vertical-align:top;border-bottom:0.1px solid black;'>ค่ารถเงินสด<br>[001]</th>
				<th width='90px'  align='right'  style='border-top:0.1px solid black;vertical-align:top;border-bottom:0.1px solid black;'>ดาวน์เช่าซื้อ<br>[002]</th>
				<th width='90px'  align='right'  style='border-top:0.1px solid black;vertical-align:top;border-bottom:0.1px solid black;'>ดาวน์ไฟแนนซ์<br>[003]</th>
				<th width='90px'  align='right'  style='border-top:0.1px solid black;vertical-align:top;border-bottom:0.1px solid black;'>ค่ารถจากไฟแนนซ์<br>[004]</th>
				<th width='90px'  align='right'  style='border-top:0.1px solid black;vertical-align:top;border-bottom:0.1px solid black;'>ค่าอุปกรณ์<br>[005]</th>
				<th width='90px'  align='right'  style='border-top:0.1px solid black;vertical-align:top;border-bottom:0.1px solid black;'>ค่างวด<br>[006]</th>
				
				<th width='90px'  align='right'  style='border-top:0.1px solid black;vertical-align:top;border-bottom:0.1px solid black;'>ตัดสด<br>[007]</th>
				<th width='90px'  align='right'  style='border-top:0.1px solid black;vertical-align:top;border-bottom:0.1px solid black;'>จอง<br>[008]</th>
				<th width='90px'  align='right'  style='border-top:0.1px solid black;vertical-align:top;border-bottom:0.1px solid black;'>เอเย่นต์<br>[009]</th>
				<th width='90px'  align='right'  style='border-top:0.1px solid black;vertical-align:top;border-bottom:0.1px solid black;'>คอมมิชชั่น<br>[011]</th>
				<th width='90px'  align='right'  style='border-top:0.1px solid black;vertical-align:top;border-bottom:0.1px solid black;'>อื่นๆ</th>
				<th width='90px'  align='right'  style='border-top:0.1px solid black;vertical-align:top;border-bottom:0.1px solid black;'>รวม</th>
			</tr>
		";
		
		$sql = "
			select  
				B.USERID as CODE,C.CUSCOD,A.NAME,B.LOCATRECV,COUNT(B.TMBILL) as TMBILL  
			from {$this->MAuth->getdb('OFFICER')} A,{$this->MAuth->getdb('CHQTRAN')} B
			,{$this->MAuth->getdb('PASSWRD')} C 
			where A.CODE = C.CUSCOD and B.USERID = C.USERID 
			and B.TMBILDT between '".$TMBILDT_F."' and '".$TMBILDT_T."' 
			and B.FLAG <> 'C' and B.LOCATRECV like '".$LOCATRECV."%' and B.LOCATPAY like '".$LOCATPAY."%' 
			and B.PAYTYP like '".$PAYTYP."%' and A.CODE like '".$CODE."%' 
			GROUP BY B.USERID,C.CUSCOD,A.NAME,B.LOCATRECV having COUNT(B.TMBILL) > 0   
			union  
			select  
				B.USERID as CODE,'' as CUSCOD,'' as NAME,B.LOCATRECV,COUNT(B.TMBILL) as TMBILL  
			from {$this->MAuth->getdb('CHQTRAN')} B  
			WHERE B.TMBILDT between '".$TMBILDT_F."' and '".$TMBILDT_T."' and B.FLAG <> 'C' 
			and B.USERID NOT IN (
				select A.USERID FROM {$this->MAuth->getdb('PASSWRD')} A
				,{$this->MAuth->getdb('OFFICER')} B 
				WHERE A.CUSCOD = B.CODE  
			) and B.LOCATRECV like '".$LOCATRECV."%' and B.LOCATPAY like '".$LOCATPAY."%' 
			and B.PAYTYP like '".$PAYTYP."%'   
			GROUP BY B.USERID,B.LOCATRECV 
			having COUNT(B.TMBILL) > 0 ORDER BY CODE 
		";
		//echo $sql; exit;
		
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){$i++;
				$sql2 = "
					select A.USERID,A.LOCATRECV
						,sum(case when (A.PAYFOR = '001' and A.FLAG <> 'C' ) then  A.NETPAY else 0 end) as P001 
						,sum(case when (A.PAYFOR = '002' and A.FLAG <> 'C' ) then  A.NETPAY else 0 end) as P002 
						,sum(case when (A.PAYFOR = '003' and A.FLAG <> 'C' ) then  A.NETPAY else 0 end) as P003 
						,sum(case when (A.PAYFOR = '004' and A.FLAG <> 'C' ) then  A.NETPAY else 0 end) as P004 
						,sum(case when (A.PAYFOR = '005' and A.FLAG <> 'C' ) then  A.NETPAY else 0 end) as P005 
						,sum(case when (A.PAYFOR = '006' and A.FLAG <> 'C' ) then  A.NETPAY else 0 end) as P006 
						,sum(case when (A.PAYFOR = '007' and A.FLAG <> 'C' ) then  A.NETPAY else 0 end) as P007 
						,sum(case when (A.PAYFOR = '008' and A.FLAG <> 'C' ) then  A.NETPAY else 0 end) as P008 
						,sum(case when (A.PAYFOR = '009' and A.FLAG <> 'C' ) then  A.NETPAY else 0 end) as P009 
						,sum(case when (A.PAYFOR = '011' and A.FLAG <> 'C' ) then  A.NETPAY else 0 end) as P011 
						,sum(
							case when (
								A.PAYFOR <> '001' and A.PAYFOR <> '002' and A.PAYFOR <> '003' 
								and A.PAYFOR <> '004' and A.PAYFOR <> '005' and A.PAYFOR <> '006' and A.PAYFOR <> '007' 
								and A.PAYFOR <> '008' and A.PAYFOR <> '009' and A.PAYFOR <> '011'  and A.FLAG <> 'C' 
							) then  A.NETPAY else 0 end
						) as P012 
						,sum(A.NETPAY) as P013 
					from {$this->MAuth->getdb('CHQTRAN')} A 
					where  A.USERID = '".$row->CODE."' and A.LOCATRECV = '".$LOCATRECV."' 
					and A.LOCATPAY like '".$LOCATPAY."%' 
					and A.TMBILDT between '".$TMBILDT_F."' and '".$TMBILDT_T."' 
					and A.PAYTYP like '".$PAYTYP."%'  and A.FLAG <> 'C' 
					GROUP BY A.USERID,A.LOCATRECV 
				";
				//echo $sql2; exit;
				$query2 = $this->db->query($sql2);
				if($query2->row()){
					foreach($query2->result() as $row2){
						$arrs["P001"][] = $row2->P001;
						$arrs["P002"][] = $row2->P002;
						$arrs["P003"][] = $row2->P003;
						$arrs["P004"][] = $row2->P004;
						$arrs["P005"][] = $row2->P005;
						$arrs["P006"][] = $row2->P006;
						$arrs["P007"][] = $row2->P007;
						$arrs["P008"][] = $row2->P008;
						$arrs["P009"][] = $row2->P009;
						$arrs["P011"][] = $row2->P011;
						$arrs["P012"][] = $row2->P012;
						$arrs["P013"][] = $row2->P013;
						
						$html .="
							<tr class='trow'>
								<td width='40px'  height='40px'	align='center' >".$i."</td>
								<td width='60px'  height='40px'	align='left'   >".$row->LOCATRECV."</td>
								<td width='100px' height='40px' align='left'   >".$row->CUSCOD."</td>
								<td width='200px' height='40px'	align='left'   >".$row->NAME."</td>
								<td width='100px' height='40px'	align='right'  >".number_format($row2->P001,2)."</td>
								<td width='90px'  height='40px'	align='right'  >".number_format($row2->P002,2)."</td>
								<td width='90px'  height='40px'	align='right'  >".number_format($row2->P003,2)."</td>
								<td width='90px'  height='40px'	align='right'  >".number_format($row2->P004,2)."</td>
								<td width='90px'  height='40px'	align='right'  >".number_format($row2->P005,2)."</td>
								<td width='90px'  height='40px'	align='right'  >".number_format($row2->P006,2)."</td>
								<td width='90px'  height='40px'	align='right'  >".number_format($row2->P007,2)."</td>
								<td width='90px'  height='40px'	align='right'  >".number_format($row2->P008,2)."</td>
								<td width='90px'  height='40px'	align='right'  >".number_format($row2->P009,2)."</td>
								<td width='90px'  height='40px'	align='right'  >".number_format($row2->P011,2)."</td>
								<td width='90px'  height='40px'	align='right'  >".number_format($row2->P012,2)."</td>
								<td width='90px'  height='40px'	align='right'  >".number_format($row2->P013,2)."</td>
							</tr>
						";
					}
				}
			}
		}
		//print_r($arrs["P001"]); exit;
		if($i > 1){
			$html .="
				<tr class='trow' style='background-color:#ebebeb;'>
					<td width='40px'  height='40px'	align='center' style='border-top:0.1px solid black;vertical-align:top;border-bottom:0.1px solid black;' colspan='2'>รวมทั้งสิ้น</td>
					<td width='60px'  height='40px'	align='left'   style='border-top:0.1px solid black;vertical-align:top;border-bottom:0.1px solid black;' >".$i."</td>
					<td width='200px' height='40px'	align='left'   style='border-top:0.1px solid black;vertical-align:top;border-bottom:0.1px solid black;' >รายการ</td>
					<td width='100px' height='40px'	align='right'  style='border-top:0.1px solid black;vertical-align:top;border-bottom:0.1px solid black;' >".number_format(array_sum($arrs["P001"]),2)."</td>
					<td width='90px'  height='40px'	align='right'  style='border-top:0.1px solid black;vertical-align:top;border-bottom:0.1px solid black;' >".number_format(array_sum($arrs["P002"]),2)."</td>
					<td width='90px'  height='40px'	align='right'  style='border-top:0.1px solid black;vertical-align:top;border-bottom:0.1px solid black;' >".number_format(array_sum($arrs["P003"]),2)."</td>
					<td width='90px'  height='40px'	align='right'  style='border-top:0.1px solid black;vertical-align:top;border-bottom:0.1px solid black;' >".number_format(array_sum($arrs["P004"]),2)."</td>
					<td width='90px'  height='40px'	align='right'  style='border-top:0.1px solid black;vertical-align:top;border-bottom:0.1px solid black;' >".number_format(array_sum($arrs["P005"]),2)."</td>
					<td width='90px'  height='40px'	align='right'  style='border-top:0.1px solid black;vertical-align:top;border-bottom:0.1px solid black;' >".number_format(array_sum($arrs["P006"]),2)."</td>
					<td width='90px'  height='40px'	align='right'  style='border-top:0.1px solid black;vertical-align:top;border-bottom:0.1px solid black;' >".number_format(array_sum($arrs["P007"]),2)."</td>
					<td width='90px'  height='40px'	align='right'  style='border-top:0.1px solid black;vertical-align:top;border-bottom:0.1px solid black;' >".number_format(array_sum($arrs["P008"]),2)."</td>
					<td width='90px'  height='40px'	align='right'  style='border-top:0.1px solid black;vertical-align:top;border-bottom:0.1px solid black;' >".number_format(array_sum($arrs["P009"]),2)."</td>
					<td width='90px'  height='40px'	align='right'  style='border-top:0.1px solid black;vertical-align:top;border-bottom:0.1px solid black;' >".number_format(array_sum($arrs["P011"]),2)."</td>
					<td width='90px'  height='40px'	align='right'  style='border-top:0.1px solid black;vertical-align:top;border-bottom:0.1px solid black;' >".number_format(array_sum($arrs["P012"]),2)."</td>
					<td width='90px'  height='40px'	align='right'  style='border-top:0.1px solid black;vertical-align:top;border-bottom:0.1px solid black;' >".number_format(array_sum($arrs["P013"]),2)."</td>
				</tr>
			";	
		}
		$body = "<table class='fs9' cellspacing='0'>".$html."</table>";
		$mpdf = new \Mpdf\Mpdf([
			'mode' => 'utf-8', 
			'format' => 'A4-L',
			'margin_top' => 50, 	//default = 16
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
					<th colspan='16' style='font-size:11pt;' align='center'>".$COMP_NM."<br>รายงานสรุปรายรับตามวันที่รับเงินจากสาขา</th>
				</tr>
				<tr>
					<th colspan='16' style='font-size:11pt;' align='center'>
						<br>สาขา</br>&nbsp;&nbsp;".$LOCATRECV."&nbsp;&nbsp;
						<b>ชำระเงินเพื่อ บ/ช สาขา</b>&nbsp;&nbsp;".$LOCATPAY."&nbsp;&nbsp;
						<b>จากวันที่</b>&nbsp;&nbsp;".$this->Convertdate(2,$TMBILDT_F)."&nbsp;&nbsp;
						<b>ถึงวันที่</b>&nbsp;&nbsp;".$this->Convertdate(2,$TMBILDT_T)."&nbsp;&nbsp;
						<b>ประเภทการรับชำระ</b>&nbsp;&nbsp;".$PAYTYP."&nbsp;&nbsp;
						<b>พนักงานเก็บเงิน</b>&nbsp;&nbsp;".$CODE."&nbsp;&nbsp;
					</th>
				</tr>
				<tr>
					<th colspan='16' style='font-size:11pt;' align='right'>
						RpAsA20,21
					</th>
				</tr>
				<tr>
					<td colspan='2' align='left'>วันที่พิมพ์รายงาน</td>
					<td colspan='3' align='left'>".date('d/m/').(date('Y')+543)." ".date('H:i')."</td>
					<td colspan='11' align='right'>หน้าที่ : {PAGENO} / {nb} &emsp;&emsp;</td>
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