<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/********************************************************
             ______@20/05/2020______
			 Pasakorn Boonded

********************************************************/
class SYDDeptorPay extends MY_Controller {
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
				<div class='col-sm-12 col-xs-12' style='height:100%;overflow:auto;font-size:11pt;'>					
					<div class='row' style='height:90%;'>
						<div class='col-sm-12 col-xs-12' style='background-color:#808b96;border:5px solid white;height:75px;text-align:center;font-size:12pt;color:white;font-weight:bold;'>	
							<br>รายงานการ์ดลูกหนี้และกำไร (SYD)<br>
						</div>
						<div class='col-sm-12' style='height:30px;'></div>
						<div class='col-sm-12'>
							<div class='col-sm-3'>
								<div class='form-group'>
									เลขที่สัญญา
									<select id='CONTNO' class='form-control input-sm'></select>
								</div>
							</div>
							<div class='col-sm-3'>
								<div class='form-group'>
									สาขา
									<select id='LOCAT' class='form-control input-sm'></select>
								</div>
							</div>
							<div class='col-sm-6'>
								<div class='form-group'>
									ชื่อ - สกุล
									<input type='text' id='CUSNAME' class='form-control input-sm' disabled>
								</div>
							</div>
							<div class='col-sm-12' style='height:100px;'></div>
							<div class='col-sm-12'>
								<button id='btnreport' style='width:100%;' class='btn btn-cyan btn-sm'><span class='fa fa-file-text'>SCREEN</span></button>
							</div>
						</div>
					</div>	
				</div>
			</div>
		";
		$html .="<script src='".base_url('public/js/SYS06/SYDDeptorPay.js')."'></script>";
		echo $html;
	}
	function getLocat(){
		$CONTNO = $_REQUEST['CONTNO'];
		$response = array();
		$sql = "
			select top 100 A.CONTNO,A.LOCAT,C.SNAM+C.NAME1+' '+C.NAME2 as CUSNAME 
			from {$this->MAuth->getdb('ARMAST')} A
			left join {$this->MAuth->getdb('CUSTMAST')} C on A.CUSCOD = C.CUSCOD
			left join {$this->MAuth->getdb('INVTRAN')} I on A.STRNO = I.STRNO 
			where A.CONTNO = '".$CONTNO."' order by A.CONTNO
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$response['LOCAT']   = $row->LOCAT;
				$response['CUSNAME'] = $row->CUSNAME;
			}
		}else{
			$response['LOCAT'] = "";
			$response['CUSNAME'] = "";
		}
		echo json_encode($response);
	}
	function conditiontopdf(){
		$data = array();
		$data[] = urlencode($_REQUEST['LOCAT'].'||'.$_REQUEST['CONTNO']);
		echo json_encode($this->generateData($data,"encode"));
	}
	function pdf(){
		$data = array();
		$data[] = $_REQUEST["condpdf"];
		$arrs = $this->generateData($data,"decode");
		$arrs[0] = urldecode($arrs[0]);
		
		$tx = explode('||',$arrs[0]);
		$LOCAT     = $tx[0];
		$CONTNO    = $tx[1];
		
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
		
		$head = ""; $html = ""; $html2 = ""; $html3 = ""; $html4=""; $i = 0;
		$sql = "
			select A.CONTNO,A.LOCAT,A.CUSCOD,B.SNAM+B.NAME1+' '+B.NAME2 as CUSNAME,A.DLDAY
				,A.EFRATE,A.INTRT,A.STRNO,C.ENGNO,C.TYPE,C.MODEL,C.COLOR,C.CC,C.REGNO,C.BAAB,A.BILLCOLL
				,A.NPRICE,A.NCSHPRC,A.NDAWN,A.NKANG,A.T_FUPAY,A.N_UPAY,A.VATPRC,A.VCSHPRC,A.VATDWN,A.VATDWN
				,A.V_UPAY,A.TOTPRC,A.T_NOPAY,A.TOT_UPAY,A.TCSHPRC,A.TOTDWN,A.TKANG,A.NPROFIT,A.TOTPRES
				,A.VATPRES,A.NPAYRES,convert(varchar(8),A.FDATE,112) as FDATE,convert(varchar(8),A.LDATE,112) as LDATE
				,A.T_LUPAY,A.VKANG,convert(varchar(8),A.SDATE,112) as SDATE,C.CRCOST,C.NETCOST,C.CRVAT,C.TOTCOST 
			from {$this->MAuth->getdb('ARMAST')} A
			,{$this->MAuth->getdb('CUSTMAST')} B,{$this->MAuth->getdb('INVTRAN')} C 
			where A.CUSCOD = B.CUSCOD and A.STRNO = C.STRNO and A.CONTNO like '".$CONTNO."%'
			AND A.LOCAT like '".$LOCAT."%' and A.TOTPRC > 0	
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		$contno = ""; $locat = "";
		if($query->row()){
			foreach($query->result() as $row){$i++;
				$contno = $row->CONTNO;
				$locat  = $row->LOCAT;
				$html .= "
					<tr>
						<td class='wf' style='height:1px;border-top:0.1px solid black;' colspan='14'></td>
					</tr>
					<tr>
						<td style='width:%;text-align:left;' colspan='2'><b>เลขที่สัญญา</b>&nbsp;&nbsp;".$row->CONTNO."</td>
						<td style='width:%;text-align:left;' colspan='2'><b>รหัสลูกค้า</b>&nbsp;&nbsp;".$row->CUSCOD."</td>
						<td style='width:%;text-align:left;' colspan='4'><b>ชื่อ - สกุลลูกค้า</b>&nbsp;&nbsp;".$row->CUSNAME."</td>
						<td style='width:%;text-align:left;' colspan='2'><b>สัญญาวันที่</b>&nbsp;&nbsp;".$row->SDATE."</td>
						<td style='width:%;text-align:left;' colspan='2'><b>เงื่อนไขล่าช้าได้ไม่เกิน</b>&nbsp;&nbsp;".$row->DLDAY."&nbsp;&nbsp;<b>วัน</b></td>
						<td style='width:%;text-align:right;' colspan='2'><b>ดอกเบี้ยร้อยละ </b>&nbsp;&nbsp;".$row->INTRT."&nbsp;&nbsp;<b>บาทต่อปี</b></td>
					</tr>
					<tr>
						<td style='width:%;text-align:left;' colspan='2'><b>เลขตัวถัง</b>&nbsp;&nbsp;".$row->STRNO."</td>
						<td style='width:%;text-align:left;' colspan='2'><b>ยี่ห้อ</b>&nbsp;&nbsp;".$row->TYPE."</td>
						<td style='width:%;text-align:left;' colspan='2'><b>รุ่น</b>&nbsp;&nbsp;".$row->MODEL."</td>
						<td style='width:%;text-align:left;' colspan='2'><b>แบบ</b>&nbsp;&nbsp;".$row->BAAB."</td>
						<td style='width:%;text-align:left;' colspan='2'><b>ขนาด</b>&nbsp;&nbsp;".$row->CC."</b></td>
						<td style='width:%;text-align:right;' colspan='2'><b>ทะเบียน</b>&nbsp;&nbsp;".$row->REGNO."</b></td>
						<td style='width:%;text-align:right;' colspan='2'><b>Bollcoll</b>&nbsp;&nbsp;".$row->BILLCOLL."</b></td>
					</tr>
				";
				$html2 .="
					<tr>
						<td style='width:2%;text-align:left;'></td>
						<td style='width:12%;text-align:left;'><b>ราคาขายผ่อนก่อน VAT</b></td>
						<td style='width:6%;text-align:right;'>".number_format($row->NPRICE,2)."</td>
						<td style='width:6%;text-align:right;'><b>บาท</b></td>
						
						<td style='width:12%;text-align:right;'><b>ภาษีขายผ่อน</b></td>
						<td style='width:6%;text-align:right;'>".number_format($row->VATPRC,2)."</td>
						<td style='width:6%;text-align:right;'><b>บาท</b></td>
						
						<td style='width:12%;text-align:right;'><b>ราคาขายผ่อนรวม VAT</b></td>
						<td style='width:5%;text-align:right;'>".number_format($row->TOTPRC,2)."</td>
						<td style='width:4%;text-align:right;'><b>บาท</b></td>
						
						<td style='width:15%;text-align:right;'><b>ผ่อนจำนวน VAT</b></td>
						<td style='width:6%;text-align:right;'>".$row->T_NOPAY."</td>
						<td style='width:6%;text-align:right;'><b>งวด</b></td>
						<td style='width:2%;text-align:right;'></td>
					</tr>
					<tr>
						<td style='width:2%;text-align:left;'></td>
						<td style='width:12%;text-align:left;'><b>ราคาขายสดก่อน VAT</b></td>
						<td style='width:6%;text-align:right;'>".number_format($row->NCSHPRC,2)."</td>
						<td style='width:6%;text-align:right;'><b>บาท</b></td>
						
						<td style='width:12%;text-align:right;'><b>ภาษีขายสด</b></td>
						<td style='width:6%;text-align:right;'>".number_format($row->VCSHPRC,2)."</td>
						<td style='width:6%;text-align:right;'><b>บาท</b></td>
						
						<td style='width:12%;text-align:right;'><b>ราคาขายสดรวม VAT</b></td>
						<td style='width:6%;text-align:right;'>".number_format($row->TCSHPRC,2)."</td>
						<td style='width:6%;text-align:right;'><b>บาท</b></td>
						
						<td style='width:12%;text-align:right;'><b>ดอกผลเช่าซื้อ</b></td>
						<td style='width:6%;text-align:right;'>".number_format($row->NPROFIT,2)."</td>
						<td style='width:6%;text-align:right;'><b>บาท</b></td>
						<td style='width:2%;text-align:right;'></td>
					</tr>
					<tr>
						<td style='width:2%;text-align:left;'></td>
						<td style='width:12%;text-align:left;'><b>ราคาต้นทุนก่อน VAT</b></td>
						<td style='width:6%;text-align:right;'>".number_format($row->NETCOST,2)."</td>
						<td style='width:6%;text-align:right;'><b>บาท</b></td>
						
						<td style='width:12%;text-align:right;'><b>ภาษีราคาทุน</b></td>
						<td style='width:6%;text-align:right;'>".number_format($row->CRVAT,2)."</td>
						<td style='width:6%;text-align:right;'><b>บาท</b></td>
						
						<td style='width:12%;text-align:right;'><b>ราคาทุนรวม VAT</b></td>
						<td style='width:6%;text-align:right;'>".number_format($row->TOTCOST,2)."</td>
						<td style='width:6%;text-align:right;'><b>บาท</b></td>
						
						<td style='width:12%;text-align:right;'><b>กำไรเบื้องต้น + ดอกผลเช่าซื้อ</b></td>
						<td style='width:6%;text-align:right;'>".number_format($row->NPRICE - $row->NETCOST,2)."</td>
						<td style='width:6%;text-align:right;'><b>บาท</b></td>
						<td style='width:2%;text-align:right;'></td>
					</tr>
					<tr>
						<td style='width:2%;text-align:left;'></td>
						<td style='width:12%;text-align:left;'><b>เงินดาวน์ก่อน VAT</b></td>
						<td style='width:6%;text-align:right;'>".number_format($row->NDAWN,2)."</td>
						<td style='width:6%;text-align:right;'><b>บาท</b></td>
						
						<td style='width:12%;text-align:right;'><b>ภาษีเงินดาวน์</b></td>
						<td style='width:6%;text-align:right;'>".number_format($row->VATDWN,2)."</td>
						<td style='width:6%;text-align:right;'><b>บาท</b></td>
						
						<td style='width:12%;text-align:right;'><b>เงินดาวน์รวม VAT</b></td>
						<td style='width:6%;text-align:right;'>".number_format($row->TOTDWN,2)."</td>
						<td style='width:6%;text-align:right;'><b>บาท</b></td>
						
						<td style='width:12%;text-align:right;'><b>กำไรเงินดาวน์เบื้องต้น</b></td>
						<td style='width:6%;text-align:right;'></td>
						<td style='width:6%;text-align:right;'><b>บาท</b></td>
						<td style='width:2%;text-align:right;'></td>
					</tr>
					<tr>
						<td style='width:2%;text-align:left;'></td>
						<td style='width:12%;text-align:left;'><b>ยอดตั้งลูกหนี้ก่อน VAT</b></td>
						<td style='width:6%;text-align:right;'>".number_format($row->NKANG,2)."</td>
						<td style='width:6%;text-align:right;'><b>บาท</b></td>
						
						<td style='width:12%;text-align:right;'><b>ภาษีรอตัด บ/ช</b></td>
						<td style='width:6%;text-align:right;'>".number_format($row->VKANG,2)."</td>
						<td style='width:6%;text-align:right;'><b>บาท</b></td>
						
						<td style='width:12%;text-align:right;'><b>ยอดตั้งลูกหนี้รวม VAT</b></td>
						<td style='width:6%;text-align:right;'>".number_format($row->TKANG,2)."</td>
						<td style='width:6%;text-align:right;'><b>บาท</b></td>
						
						<td style='width:12%;text-align:right;'><b>Sum of Digit</b></td>
						<td style='width:6%;text-align:right;'></td>
						<td style='width:6%;text-align:right;'><b>บาท</b></td>
						<td style='width:2%;text-align:right;'></td>
					</tr>
					<tr>
						<td style='width:2%;text-align:left;'></td>
						<td style='width:12%;text-align:left;'><b>ค่างวดงวดแรกรวม VAT</b></td>
						<td style='width:6%;text-align:right;'>".number_format($row->T_FUPAY,2)."</td>
						<td style='width:6%;text-align:right;'><b>บาท</b></td>
						
						<td style='width:12%;text-align:right;'><b>งวดสุดท้ายรวม VAT</b></td>
						<td style='width:6%;text-align:right;'>".number_format($row->T_LUPAY,2)."</td>
						<td style='width:6%;text-align:right;'><b>บาท</b></td>
						
						<td style='width:12%;text-align:right;'><b>ค่างวดถัดไปรวม VAT</b></td>
						<td style='width:6%;text-align:right;'>".number_format($row->TOT_UPAY,2)."</td>
						<td style='width:6%;text-align:right;'><b>บาท</b></td>
						
						<td style='width:12%;text-align:right;'><b>วันครบกำหนดงวดแรก</b></td>
						<td style='width:6%;text-align:right;'>".$row->FDATE."</td>
						<td style='width:6%;text-align:right;'></td>
						<td style='width:2%;text-align:right;'></td>
					</tr>
					<tr>
						<td style='width:2%;text-align:left;'></td>
						<td style='width:12%;text-align:left;'><b>ค่างวดๆ ถัดไปก่อน VAT</b></td>
						<td style='width:6%;text-align:right;'>".number_format($row->N_UPAY,2)."</td>
						<td style='width:6%;text-align:right;'><b>บาท</b></td>
						
						<td style='width:12%;text-align:right;'><b>ภาษีค่างวด</b></td>
						<td style='width:6%;text-align:right;'>".number_format($row->V_UPAY,2)."</td>
						<td style='width:6%;text-align:right;'><b>บาท</b></td>
						
						<td style='width:12%;text-align:right;'></td>
						<td style='width:6%;text-align:right;'></td>
						<td style='width:6%;text-align:right;'></td>
						
						<td style='width:12%;text-align:right;'><b>วันครบกำหนดดิวงวดสุดท้าย</b></td>
						<td style='width:6%;text-align:right;'>".$row->LDATE."</td>
						<td style='width:6%;text-align:right;'></td>
						<td style='width:2%;text-align:right;'></td>
					</tr>
				";
			}
		}
		//echo $a; exit;
		$html3 .="
			<tr class='wm'>
				<td class='wf' style='height:1px;border-top:0.1px solid black;' colspan='14'></td>
			</tr>
			<tr>
				<th style='border-bottom:0.1px solid black;text-align:left;'>งวดที่</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>วันครบกำหนด</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ใบกำกับภาษี</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ชำระภาษีแล้ว</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ใบเสร็จเลขที่</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ลูกหนี้คงเหลือ</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ภาษีคงเหลือ</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>เงินต้นคงเหลือ</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ดอกผลเช่าซื้อคงเหลือ</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ค่างวด</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>มูลค่าสินค้า</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ภาษี</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>เงินต้น</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ดอกผลเช่าซื้อ</th>
			</tr>
			<tr>
				<td class='wf' style='height:1px;border-top:0.1px solid black;' colspan='14'></td>
			</tr>
		";
		$sql = "
			select 0 as NOPAY,A.TKANG,A.VKANG,A.NCSHPRC,A.NPROFIT
			from {$this->MAuth->getdb('ARMAST')} A,{$this->MAuth->getdb('CUSTMAST')} B
			,{$this->MAuth->getdb('INVTRAN')} C 
			where A.CUSCOD = B.CUSCOD and A.STRNO = C.STRNO and A.CONTNO like '".$CONTNO."%'
			and A.LOCAT like '".$LOCAT."%' and A.TOTPRC > 0		
		";
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$html3 .= "
					<tr>
						<td style='width:5%;text-align:left;'>".$row->NOPAY."</td>
						<td style='width:7%;text-align:right;' colspan='5'>".number_format($row->TKANG,2)."</td>
						<td style='width:7%;text-align:right;'>".number_format($row->VKANG,2)."</td>
						<td style='width:7%;text-align:right;'>".number_format($row->NCSHPRC,2)."</td>
						<td style='width:7%;text-align:right;'>".number_format($row->NPROFIT,2)."</td>
					</tr>
				";
			}
		}
		$sql = "
			IF OBJECT_ID('tempdb..#SYDD') IS NOT NULL DROP TABLE #SYDD
			select *
			into #SYDD
			from(
				select A.CONTNO,A.NOPAY,convert(varchar(8),A.DDATE,112) as DDATE,A.TAXINV
					,B.NKANG,B.NCSHPRC,B.NCSHPRC - B.NDAWN as NCSHPRC_ND,B.NPROFIT,B.NDAWN,A.TAXAMT,A.DAMT
					,A.N_DAMT,V_DAMT,A.NINSTAL,A.NPROF 
				from {$this->MAuth->getdb('ARPAY')} A 
				left join {$this->MAuth->getdb('ARMAST')} B on A.CONTNO = B.CONTNO 
				where A.CONTNO = '".$contno."' and A.LOCAT = '".$locat."'
			)SYDD
		";
		//echo $sql; 
		$this->db->query($sql);
		$sql = "
			select *,V_DAMT - TV_DAMT as TV_DAMT
			from (
				select *,(
						select SUM(V_DAMT) from #SYDD B
						where B.NOPAY <= A.NOPAY
					) as TV_DAMT
				from #SYDD A 
			)a 
		";
		//echo $sql;
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$sql2 = "
					declare @maxnopay varchar(max) = (select max(NOPAY) from #SYDD);
					select (NKANG - T_DAMT) as T_DAMT,(V_DAMT - TV_DAMT) as TV_DAMT
						,(NCSHPRC_ND - T_KANG) as T_KANG,(NPROFIT - T_NPROFIT) as T_NPROFIT 
					from (
						select ROW_NUMBER() OVER(ORDER BY NOPAY ASC) AS NOPAY2,*
						from (
							select top 1 *,
								(
									0
								) as T_DAMT
								,
								(
									0
								) as T_KANG  
								,
								(
									0
								) as T_NPROFIT
								,
								(
									0
								) as TV_DAMT 
							from #SYDD A where A.NOPAY = '1'
							union all
							select *,
								(
									select SUM(DAMT) from #SYDD B 
									where B.NOPAY <= A.NOPAY
								) as T_DAMT
								,
								(
									select SUM(NINSTAL) from #SYDD B 
									where B.NOPAY <= A.NOPAY
								) as T_KANG  
								,
								(
									select SUM(NPROF) from #SYDD B
									where B.NOPAY <= A.NOPAY
								) as T_NPROFIT
								,
								(
									select SUM(V_DAMT) from #SYDD B
									where B.NOPAY <= A.NOPAY
								) as TV_DAMT 
							from #SYDD A where A.NOPAY <> @maxnopay
						)a
					)a where NOPAY2 = '".$row->NOPAY."'	
				";
				//echo $sql2; exit;
				$query2 = $this->db->query($sql2);
				if($query2->row()){
					foreach($query2->result() as $row2){
						$html3 .= "
							<tr>
								<td style='width:5%;text-align:left;'>".$row->NOPAY."</td>
								<td style='width:7%;text-align:left;'>".$row->DDATE."</td>
								<td style='width:7%;text-align:right;'>".$row->TAXINV."</td>
								<td style='width:7%;text-align:right;'>".number_format($row->TAXAMT,2)."</td>
								<td style='width:7%;text-align:right;'></td>
								<td style='width:7%;text-align:right;'>".number_format($row2->T_DAMT,2)."</td>
								<td style='width:7%;text-align:right;'>".number_format($row->TV_DAMT,2)."</td>
								<td style='width:7%;text-align:right;'>".number_format($row2->T_KANG,2)."</td>
								<td style='width:9%;text-align:right;'>".number_format($row2->T_NPROFIT,2)."</td>
								<td style='width:7%;text-align:right;'>".number_format($row->DAMT,2)."</td>
								<td style='width:7%;text-align:right;'>".number_format($row->N_DAMT,2)."</td>
								<td style='width:7%;text-align:right;'>".number_format($row->V_DAMT,2)."</td>
								<td style='width:7%;text-align:right;'>".number_format($row->NINSTAL,2)."</td>
								<td style='width:7%;text-align:right;'>".number_format($row->NPROF,2)."</td>
							</tr>
						";
					}
				}
			}
		}
		$sql = "
			select A.TKANG,A.VKANG,A.TKANG - NPROFIT as TKNP,NPROFIT
			from {$this->MAuth->getdb('ARMAST')} A,{$this->MAuth->getdb('CUSTMAST')} B
			,{$this->MAuth->getdb('INVTRAN')} C 
			where A.CUSCOD = B.CUSCOD and A.STRNO = C.STRNO and A.CONTNO like '".$contno."%'
			AND A.LOCAT like '".$locat."%' and A.TOTPRC > 0			
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$html3 .="
					<tr class='wm'>
						<td class='wf' style='height:1px;border-top:0.1px solid black;' colspan='14'></td>
					</tr>
						<tr>
							<td style='width:%;text-align:center;' colspan='3'><b>รวมทั้งสิ้น</b></td>
							<td style='width:%;text-align:left;' colspan='3'>0.00</td>
							<td style='width:%;text-align:right;' colspan='3'>".number_format($row->TKANG,2)."</td>
							<td style='width:%;text-align:right;'>".number_format($row->TKANG,2)."</td>
							<td style='width:%;text-align:right;'>".number_format($row->VKANG,2)."</td>
							<td style='width:%;text-align:right;'>".number_format($row->TKNP,2)."</td>
							<td style='width:%;text-align:right;'>".number_format($row->NPROFIT,2)."</td>
						</tr>
					<tr>
						<td class='wf' style='height:1px;border-top:0.1px solid black;' colspan='14'></td>
					</tr>
				";
			}
		}
		$html4 .="
			<tr>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ชำระถึงงวดที่</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>มูลค่าคงเหลือจริง</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ภาษีที่ยังไม่ได้รับเงิน</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ลูกหนี้คงเหลือจริง</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>เงินต้นคงเหลือจริง</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ดอกผลคงเหลือจริง</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ดอกผลคงเหลือตามดิว</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ดอกผลที่ยังไม่ได้รับเงิน</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ภาษีที่ยังไม่ออกใบกำกับ</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ภาษีที่ออกแทนลูกค่า</th>
			</tr>
		";
		$sql = "
			select  
				(select isnull(MAX(NOPAY),0) from {$this->MAuth->getdb('ARPAY')} 
				where CONTNO = '".$contno."' and LOCAT = '".$locat."' and PAYMENT <> 0) as P_NOPAY
				,(select MAX(NOPAY) from {$this->MAuth->getdb('ARPAY')} 
				where CONTNO = '".$contno."' and LOCAT = '".$locat."') as ALLNOPAY
				,b.NPRICE - b.NDAWN - a.PAYMENT as NPRICE_T
				,b.VATPRC - PAYAMT_V as VATPRC_T
				,b.TOTPRC - b.NDAWN - a.PAYMENT as TOTPRC_T  
			from (
				select 
					CONTNO,SUM(PAYMENT) as PAYMENT
					,SUM(N_PAYMENT) as N_PAYAMT
					,SUM(V_PAYMENT) as PAYAMT_V  
				from {$this->MAuth->getdb('ARPAY')} 
				where CONTNO = '".$contno."' and LOCAT = '".$locat."' group by CONTNO
			)a left join {$this->MAuth->getdb('ARMAST')} b on a.CONTNO = b.CONTNO
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$sql2 = "
					select NPROFIT - PROFSYD1 - PROFSYD2 as NPROFIT_T
						,ISNULL(BALNDUE,0) as BALNDUE,(NPROFIT - PROFSYD1 - PROFSYD2) - ISNULL(BALNDUE,0) as NPROFIT_N
						,TAXBAL,TAXCUS 
					from (
						select
							CONTNO,LOCAT,NPROFIT
							,(
								select SUM(V_DAMT-TAXAMT) as TAXBAL 
								from {$this->MAuth->getdb('ARPAY')} group by CONTNO,LOCAT 
								having CONTNO = {$this->MAuth->getdb('ARMAST')}.CONTNO 
								and LOCAT = {$this->MAuth->getdb('ARMAST')}.LOCAT
							)as TAXBAL
							,(
								select SUM(TAXAMT-V_DAMT) as TAXCUS 
								from {$this->MAuth->getdb('ARPAY')} group by CONTNO,LOCAT 
								having CONTNO = {$this->MAuth->getdb('ARMAST')}.CONTNO 
								and LOCAT = {$this->MAuth->getdb('ARMAST')}.LOCAT
							)as TAXCUS 
							,(
								select SUM(ROUND(N_PAYMENT * NPROF/N_DAMT,2)) as PROFSYD2
								from {$this->MAuth->getdb('ARPAY')} where PAYMENT < DAMT group by CONTNO,LOCAT 
								having CONTNO = {$this->MAuth->getdb('ARMAST')}.CONTNO 
								and LOCAT = {$this->MAuth->getdb('ARMAST')}.LOCAT
							)as PROFSYD2
							,(
								select SUM(NPROF) as PROFSYD1
								from {$this->MAuth->getdb('ARPAY')} where PAYMENT = DAMT group by CONTNO,LOCAT 
								having CONTNO = {$this->MAuth->getdb('ARMAST')}.CONTNO 
								and LOCAT = {$this->MAuth->getdb('ARMAST')}.LOCAT
							)as PROFSYD1
							,(
								select SUM(NPROF) as BALNDUE 
								from {$this->MAuth->getdb('ARPAY')} where DDATE > getdate() group by CONTNO,LOCAT 
								having CONTNO = {$this->MAuth->getdb('ARMAST')}.CONTNO 
								and LOCAT = {$this->MAuth->getdb('ARMAST')}.LOCAT
							)as BALNDUE
						from {$this->MAuth->getdb('ARMAST')} where CONTNO = '".$contno."' and LOCAT = '".$locat."'
					)a
				";
				//echo $sql2; exit;
				$query2 = $this->db->query($sql2);
				if($query2->row()){
					foreach($query2->result() as $row2){
						$html4 .="
							<tr>
								<td style='width:8%;text-align:right;'>".$row->P_NOPAY."/".$row->ALLNOPAY."</td>
								<td style='width:8%;text-align:right;'>".number_format($row->NPRICE_T,2)."</td>
								<td style='width:10%;text-align:right;'>".number_format($row->VATPRC_T,2)."</td>
								<td style='width:10%;text-align:right;'>".number_format($row->TOTPRC_T,2)."</td>
								<td style='width:10%;text-align:right;'>".number_format($row->TOTPRC_T - $row2->NPROFIT_T,2)."</td>
								<td style='width:10%;text-align:right;'>".number_format($row2->NPROFIT_T,2)."</td>
								<td style='width:10%;text-align:right;'>".number_format($row2->BALNDUE,2)."</td>
								<td style='width:10%;text-align:right;'>".number_format($row2->NPROFIT_N,2)."</td>
								<td style='width:10%;text-align:right;'>".number_format($row2->TAXBAL,2)."</td>
								<td style='width:10%;text-align:right;'>".number_format($row2->TAXCUS,2)."</td>
							</tr>
							<tr>
								<td class='wf' style='height:1px;border-top:0.1px solid black;' colspan='10'></td>
							</tr>
						";
					}
				}
			}
		}
		if($i > 0){
			$content = "
				<table class='wf' style='font-size:8pt;height:700px;border-collapse:collapse;line-height:23px;overflow:wrap;vertical-align:text-top;'>
					<tbody>
						<tr>
							<th colspan='14' style='font-size:10pt;'>บริษัท ตั้งใจพัฒนายานยนต์ จำกัด</th>
						</tr>
						<tr>
							<th colspan='14' style='font-size:9pt;'>รายงานการ์ดลูกหนี้และดอกผลเช่าซื้อ (SYD)</th>
						</tr>
						<tr>
							<td style='text-align:center;' colspan='14'>
								<b>รหัสสาขา</b> &nbsp;&nbsp;".$LOCAT."&nbsp;&nbsp;
							</td>
						</tr>
						<tr>
							<td style='text-align:right;' colspan='14'>Rpsyd 20,21</td>
						</tr>
						<br>
						".$head."
						".$html."
					</tbody>
				</table><br>
				<table class='wf' style='font-size:8pt;height:700px;border-collapse:collapse;line-height:23px;overflow:wrap;vertical-align:text-top;'>
					<tbody>
						".$html2."
					</tbody>
				</table>
				<table class='wf' style='font-size:8pt;height:700px;border-collapse:collapse;line-height:23px;overflow:wrap;vertical-align:text-top;'>
					<tbody>
						".$html3."
					</tbody>
				</table>
				<table class='wf' style='font-size:8pt;height:700px;border-collapse:collapse;line-height:23px;overflow:wrap;vertical-align:text-top;'>
					<tbody>
						".$html4."
					</tbody>
				</table>
			";
			$head = "
				<div class='wf pf' style='top:1060;left:600;top:715;left:880; font-size:6pt;'>วันที่พิมพ์รายงาน : ".date('d/m/').(date('Y')+543)." ".date('H:i')." หน้า {PAGENO} / {nbpg}</div>
			";
		}else{
			$content = "<div style='color:red;'>ไม่พบข้อมูลตามเงื่อนไข</div>";
			$head = "
				<div class='wf pf' style='top:1060;left:600;top:715;left:880; font-size:6pt;'></div>
			";
		}
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
		$mpdf->SetHTMLHeader($head);	
		$mpdf->WriteHTML($content);	
		$mpdf->Output();
	}
}