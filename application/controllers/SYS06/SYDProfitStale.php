<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/********************************************************
             ______@30/05/2020______
			 Pasakorn Boonded

********************************************************/
class SYDProfitStale extends MY_Controller {
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
							<br>รายงานดอกผลเช่าซื้อค้างรับ (SYD)<br>
						</div>
						<div class='col-sm-12'>
							<div class='col-sm-3'>
								<div class='form-group'>
									รหัสสาขา
									<select id='LOCAT' class='form-control input-sm'></select>
								</div>
							</div>
							<div class='col-sm-3'>
								<div class='form-group'>
									จากเลขที่สัญญา
									<select id='CONTNO' class='form-control input-sm'></select>
								</div>
							</div>
							<div class='col-sm-3'>
								<div class='form-group'>
									ณ วันที่
									<input type='text' id='DATE' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' value='".$this->today('today')."' placeholder='ถึงวันที่ขาย' >
								</div>
							</div>
							<div class='col-sm-3'>
								<div class='form-group'>
									กลุ่มสินค้า
									<select id='GCODE' class='form-control input-sm'></select>
								</div>
							</div>
						</div>
						<div class='col-sm-12' style='height:50px;'></div>
						<div class='col-sm-12'>
							<div class='col-sm-10'>
								<b>การเรียงในรายงาน</b>
								<div class='col-sm-12' style='height:100%;width:100%;border:1px dotted #aaa;'>
									<div class='col-sm-4'>
										<div class='form-group'>
											<br>
											<label>
												<input type= 'radio' id='OR1' name='order' checked> รหัสสาขา
											</label>
										</div>
									</div>
									<div class='col-sm-4'>
										<div class='form-group'>
											<br>
											<label>
												<input type= 'radio' id='OR2' name='order'> เลขที่สัญญา
											</label>
										</div>
									</div>
									<div class='col-sm-4'>
										<div class='form-group'>
											<br>
											<label>
												<input type= 'radio' id='OR3' name='order'> รหัสลูกค้า
											</label>
										</div>
									</div>
								</div>	
							</div>
							<div class='col-sm-2'>
								<br>
								<button id='btnreport' style='width:100%;height:60px;' class='btn btn-cyan btn-sm'><span class='fa fa-file-text'>SCREEN</span></button>
							</div>
						</div>
					</div>	
				</div>
			</div>
		";
		$html .="<script src='".base_url('public/js/SYS06/SYDProfitStale.js')."'></script>";
		echo $html;
	}
	function conditiontopdf(){
		$data = array();
		$data[] = urlencode($_REQUEST['LOCAT'].'||'.$_REQUEST['CONTNO'].'||'.$_REQUEST['DATE']
		.'||'.$_REQUEST['GCODE'].'||'.$_REQUEST['order']);
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
		$DATE      = $this->Convertdate(1,$tx[2]);
		$GCODE     = $tx[3];
		$order     = $tx[4];
		
		$head = ""; $html = ""; $i = 0;
		
		$head = "
			<tr class='wm'>
				<td class='wf' style='height:1px;border-top:0.1px solid black;' colspan='17'></td>
			</tr>
			<tr>
				<th style='border-bottom:0.1px solid black;text-align:left;'>No.</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>สาขา</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>เลขที่สัญญา</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>รหัสลูกค้า</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>ชื่อ - สกุล</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>วันที่ทำสัญญา</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>มูลค่าราคาขาย</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ดอกผลเช่าซื้อ</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ค่างวดครบ<br>กำหนด</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ดอกผลครบ<br>กำหนด</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>งวดที่</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ค่างวดชำระแล้ว</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ส่วนลด</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ดอกผล<br>ชำระแล้ว</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ดอกผลค้างรับ</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ดอกผลคง<br>เหลือจริง</th>	
				<th style='border-bottom:0.1px solid black;text-align:right;'>ค่างวดคงเหลือ</th>	
			</tr>
			<tr>
				<td class='wf' style='height:1px;border-top:0.1px solid black;' colspan='17'></td>
			</tr>
		";
		$sql = "
			select A.LOCAT,A.CONTNO,A.TOTPRC,A.TOTPRES,A.CUSCOD,convert(varchar(8),A.SDATE,112) as SDATE
				,B.SNAM,A.TOTDWN,A.NPAYRES,A.NKANG,A.LPAYD,A.BILLCOLL,A.TOT_UPAY,A.EXP_AMT
				,A.SMPAY,A.EXP_FRM,A.EXP_TO,B.SNAM+B.NAME1+' '+B.NAME2 as CUSNAME,A.PAYDWN
				,A.LDATE,C.CRCOST,C.PRICE,A.T_NOPAY, A.OPTCST,A.NCARCST,A.NCSHPRC,A.NPRICE ,A.NPROFIT 
			from {$this->MAuth->getdb('ARMAST')} A,{$this->MAuth->getdb('CUSTMAST')} B,{$this->MAuth->getdb('INVTRAN')} C  
			where (A.CUSCOD = B.CUSCOD) and (A.STRNO = C.STRNO) and (A.CONTNO = C.CONTNO) 
			and (A.TSALE = C.TSALE) and (A.LOCAT like '".$LOCAT."%') and (A.CONTNO like '".$CONTNO."%') 
			and (C.GCODE like '".$GCODE."%' or C.GCODE is null) and (A.FDATE <= '".$DATE."') 
			and (A.TOTPRC > A.SMPAY or (A.TOTPRC = A.SMPAY AND A.LPAYD > '".$DATE."')) 
			and A.TOTPRC > 0  
			union 
			select A.LOCAT,A.CONTNO,A.TOTPRC,A.TOTPRES,A.CUSCOD,convert(varchar(8),A.SDATE,112) as SDATE
				,B.SNAM,A.TOTDWN,A.NPAYRES,A.NKANG,A.LPAYD,A.BILLCOLL,A.TOT_UPAY,A.EXP_AMT
				,A.SMPAY,A.EXP_FRM,A.EXP_TO,B.SNAM+B.NAME1+' '+B.NAME2 as CUSNAME,A.PAYDWN
				,A.LDATE,C.CRCOST,C.PRICE,A.T_NOPAY, A.OPTCST,A.NCARCST,A.NCSHPRC,A.NPRICE,A.NPROFIT  
			from {$this->MAuth->getdb('HARMAST')} A,{$this->MAuth->getdb('CUSTMAST')} B
			,{$this->MAuth->getdb('HINVTRAN')} C  
			where (A.CUSCOD = B.CUSCOD) and (A.STRNO = C.STRNO) and (A.CONTNO = C.CONTNO) 
			and (A.TSALE = C.TSALE) and (A.LOCAT like '".$LOCAT."%') and (A.CONTNO like '".$CONTNO."%') 
			and (C.GCODE like '".$GCODE."%' or C.GCODE is null) and (A.FDATE <= '".$DATE."') 
			and (A.TOTPRC > A.SMPAY or (A.TOTPRC = A.SMPAY and A.LPAYD > '".$DATE."')) and A.TOTPRC > 0  
			union  
			select A.LOCAT,A.CONTNO,A.TOTPRC,A.TOTPRES,A.CUSCOD,convert(varchar(8),A.SDATE,112) as SDATE
				,B.SNAM,A.TOTDWN,A.NPAYRES,A.NKANG,A.LPAYD,A.BILLCOLL,A.TOT_UPAY,A.EXP_AMT
				,A.SMPAY,A.EXP_FRM,A.EXP_TO,B.SNAM+B.NAME1+' '+B.NAME2 as CUSNAME,A.PAYDWN
				,A.LDATE,C.CRCOST,C.PRICE,A.T_NOPAY, A.OPTCST,A.NCARCST,A.NCSHPRC,A.NPRICE,A.NPROFIT  
			from {$this->MAuth->getdb('HARMAST')} A,{$this->MAuth->getdb('CHGAR_VIEW')} B
			,{$this->MAuth->getdb('INVTRAN')} C  
			where (A.CONTNO = B.CONTNO) and (A.LOCAT = B.LOCAT) and (A.STRNO = C.STRNO) 
			and (A.CONTNO = C.CONTNO) and (A.TSALE = C.TSALE) and (A.LOCAT like '".$LOCAT."%') 
			and (A.CONTNO like '".$CONTNO."%') and (C.GCODE like '".$GCODE."%' or C.GCODE is null) 
			and (A.FDATE <= '') and (B.DATE1 > '".$DATE."') and (A.TOTPRC > A.SMPAY 
			or (A.TOTPRC = A.SMPAY and A.LPAYD > '".$DATE."')) and A.TOTPRC > 0  
			ORDER BY A.".$order."
		";
		//echo $sql;
		$query = $this->db->query($sql);
		$arrs = array();
		if($query->row()){
			foreach($query->result() as $row){$i++;
				$sql1 = "
					select 
						isnull(sum(
							case when (A.PAYDT <= '".$DATE."' and ((A.PAYFOR = '006') 
							or (A.PAYFOR = '007')) ) then A.PAYAMT_N else 0 end
						),0) as SNETP 
						,isnull(sum(
							case when (A.PAYDT <= '".$DATE."' and A.PAYFOR = '002') 
							then A.PAYAMT_N else 0 end
						),0) as SNETP1
						,isnull(sum(
							case when (A.PAYDT <= '".$DATE."' and ((A.PAYFOR = '006') or (A.PAYFOR = '007')) ) 
							then  A.DISCT else 0 end
						),0) as DISCT  
					from {$this->MAuth->getdb('CHQTRAN')} A 
					where A.LOCATPAY = '".$row->LOCAT."' and  A.CONTNO = '".$row->CONTNO."'  
					and A.FLAG <> 'C' and A.TSALE = 'H' 
				"; 
				//echo $sql1; exit;
				$query1 = $this->db->query($sql1);
				if($query1->row()){
					foreach($query1->result() as $row1){
						$sql2 = "
							IF OBJECT_ID('tempdb..#cal') IS NOT NULL DROP TABLE #cal
							select *
							into #cal
							FROM(
								select NOPAY,DDATE,DAMT,N_DAMT,NINSTAL,NPROF,V_DAMT,DATE1,PAYMENT
									,(
										select SUM(NPROF) from {$this->MAuth->getdb('ARPAY')} B
										where B.NOPAY <= A.NOPAY and CONTNO = '".$row->CONTNO."'
									) as T_NPROFIT 
									,(
										select SUM(DAMT) from {$this->MAuth->getdb('ARPAY')} B 
										where B.NOPAY <= A.NOPAY and CONTNO = '".$row->CONTNO."'
									) as T_DAMT
								from {$this->MAuth->getdb('ARPAY')} A where CONTNO = '".$row->CONTNO."'
								union
								select NOPAY,DDATE,DAMT,N_DAMT,NINSTAL,NPROF,V_DAMT,DATE1,PAYMENT
									,(
										select SUM(NPROF) from {$this->MAuth->getdb('HARPAY')} B
										where B.NOPAY <= A.NOPAY and CONTNO = '".$row->CONTNO."'
									) as T_NPROFIT 
									,(
										select SUM(DAMT) from {$this->MAuth->getdb('HARPAY')} B 
										where B.NOPAY <= A.NOPAY and CONTNO = '".$row->CONTNO."'
									) as T_DAMT
								from {$this->MAuth->getdb('HARPAY')} A where CONTNO = '".$row->CONTNO."'
							)cal
						";
						//echo $sql2; 
						$this->db->query($sql2);
						$sql3 = "
							--ค่างวดครบกำหนด
							declare @v_damt_set varchar(max) = (	
								select SUM(N_DAMT) as N_DAMT 
								from #cal
								where DDATE <= '".$DATE."'
							);
							--ดอกผลครบกำหนด
							declare @nprof_set varchar(max) = (	
								select SUM(NPROF) as NPROF 
								from #cal
								where DDATE <= '".$DATE."'
							);
							--งวดที่
							declare @nopayf varchar(max) = (
								select MAX(NOPAY) as NOPAY from #cal
								where DDATE <= '".$DATE."' 
							);
							declare @nopayt varchar(max) = (
								select MAX(NOPAY) as NOPAY from #cal
							);
							--ค่างวดชำระจริง
							declare @payamt decimal(20,2) = (
								select isnull(SUM(PAYAMT_N),0) from {$this->MAuth->getdb('CHQTRAN')} 
								where CONTNO = '".$row->CONTNO."' 
								and FLAG <> 'C' and ((PAYFOR = '006') or (PAYFOR = '007')) 
								and PAYDT <= '".$DATE."'
							);
							--ค่างวดคงเหลือ
							declare @b_damt decimal(20,2) = (
								select SUM(DAMT) - @payamt from #cal 
							);
							select @v_damt_set as V_DAMT_SET,@nprof_set as NPROF_SET,@nopayf as NOPAYF
							,@nopayt as NOPAYT,@b_damt as B_DAMT
						";
						$sql4 = "
							--คำนวณดอกผลเช่าซื้อชำระแล้ว
							declare @payamt_n decimal(18,2) = (
								select 
									SUM(PAYAMT_N)
								from {$this->MAuth->getdb('CHQTRAN')} A 
								where A.LOCATPAY = '".$row->LOCAT."' and  A.CONTNO = '".$row->CONTNO."'  
								and A.FLAG <> 'C' and A.TSALE = 'H' and (A.PAYDT <= '".$DATE."' 
								and ((A.PAYFOR = '006') or (A.PAYFOR = '007')))
							);
							declare @nprof1 decimal(18,2) = (
								select (select sum(NPROF) from #cal where DATE1 < '".$DATE."') + 
								NPROF * (((@payamt_n % DAMT) * 100 / DAMT) / 100)
								from #cal where NOPAY in (select MAX(NOPAY)+1 from #cal 
								where DATE1 < '".$DATE."') 
							);
							declare @nprof2 decimal(18,2) = (
								select SUM(NPROF - BL_NPROF)
								from (
									select
										NPROF
										,NPROF - (NPROF * ((PAYMENT * 100 / DAMT) / 100)) as BL_NPROF
										
									from #cal A where DATE1 <= '".$DATE."'
								)a
							);
							select bl_nprof from (
								select case when '".$DATE."' >= (select MAX(DATE1) from #cal) then @nprof2 else @nprof1 end as bl_nprof
								from #cal
							)a group by bl_nprof
						";
						//echo $sql4; exit;
						$query4 = $this->db->query($sql4);
						$rown = $query4->row();
						$BL_NPROF = $rown->bl_nprof;
						
						$query3 = $this->db->query($sql3);
						if($query3->row()){
							foreach($query3->result() as $row3){
								$arrs['NPRICE'][]    = $row->NPRICE;
								$arrs['NPROFIT'][]   = $row->NPROFIT;
								$arrs['V_DAMT_SET'][]= $row3->V_DAMT_SET;
								$arrs['NPROF_SET'][] = $row3->NPROF_SET;
								$arrs['SNETP'][]     = $row1->SNETP;
								$arrs['DISCT'][]     = $row1->DISCT;
								$arrs['BL_NPROF'][]      = $BL_NPROF;
								$arrs['NPROFIT_STALE'][] = $row3->NPROF_SET - $BL_NPROF;
								$arrs['NPROFIT_BL'][]    = $row->NPROFIT - $BL_NPROF;
								$arrs['B_DAMT'][]         = $row3->B_DAMT;
								
								$html .="
									<tr>
										<td style='width:2%;text-align:left;'>".$i."</td>
										<td style='width:3%;text-align:left;'>".$row->LOCAT."</td>
										<td style='width:9%;text-align:left;'>".$row->CONTNO."</td>
										<td style='width:9%;text-align:left;'>".$row->CUSCOD."</td>
										<td style='width:14%;text-align:left;'>".$row->CUSNAME."</td>
										<td style='width:5%;text-align:right;'>".$this->Convertdate(2,$row->SDATE)."</td>
										<td style='width:5%;text-align:right;'>".number_format($row->NPRICE,2)."</td>
										<td style='width:5%;text-align:right;'>".number_format($row->NPROFIT,2)."</td>
										<td style='width:5%;text-align:right;'>".number_format($row3->V_DAMT_SET,2)."</td>
										<td style='width:5%;text-align:right;'>".number_format($row3->NPROF_SET,2)."</td>
										<td style='width:5%;text-align:right;'>".$row3->NOPAYF."/".$row3->NOPAYT."</td>
										<td style='width:5%;text-align:right;'>".number_format($row1->SNETP,2)."</td>
										<td style='width:5%;text-align:right;'>".number_format($row1->DISCT,2)."</td>
										<td style='width:5%;text-align:right;'>".number_format($BL_NPROF,2)."</td>
										<td style='width:5%;text-align:right;'>".number_format($row3->NPROF_SET - $BL_NPROF,2)."</td>
										<td style='width:5%;text-align:right;'>".number_format($row->NPROFIT - $BL_NPROF,2)."</td>
										<td style='width:5%;text-align:right;'>".number_format($row3->B_DAMT,2)."</td>
									</tr>
								";	
							}
						}
					}
				}
			}
		}
		if($i > 0){
			$html .="
				<tr>
					<td class='wf' style='height:1px;border-top:0.1px solid black;' colspan='17'></td>
				</tr>
				<tr>
					<td style='text-align:left;' colspan='2'><b>รวมทั้งสิ้น</b></td>
					<td style='text-align:center;' colspan='1'>".$i."</td>
					<td style='text-align:left;' colspan='2'><b>รายการ</b></td>
					<td style='text-align:right;' colspan='2'>".number_format(array_sum($arrs['NPRICE']),2)."</td>
					<td style='text-align:right;'>".number_format(array_sum($arrs['NPROFIT']),2)."</td>
					<td style='text-align:right;'>".number_format(array_sum($arrs['V_DAMT_SET']),2)."</td>
					<td style='text-align:right;'>".number_format(array_sum($arrs['NPROF_SET']),2)."</td>
					<td style='text-align:right;' colspan='2'>".number_format(array_sum($arrs['SNETP']),2)."</td>
					<td style='text-align:right;'>".number_format(array_sum($arrs['DISCT']),2)."</td>
					<td style='text-align:right;'>".number_format(array_sum($arrs['BL_NPROF']),2)."</td>
					<td style='text-align:right;'>".number_format(array_sum($arrs['NPROFIT_STALE']),2)."</td>
					<td style='text-align:right;'>".number_format(array_sum($arrs['NPROFIT_BL']),2)."</td>
					<td style='text-align:right;'>".number_format(array_sum($arrs['B_DAMT']),2)."</td>
				</tr>
				<tr>
					<td class='wf' style='height:1px;border-top:0.1px solid black;' colspan='17'></td>
				</tr>
			";
		}
		//echo $i; exit;
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
		if($i > 0){
			$content = "
				<table class='wf' style='font-size:7.8pt;height:700px;border-collapse:collapse;line-height:23px;overflow:wrap;vertical-align:text-top;'>
					<tbody>
						<tr>
							<th colspan='17' style='font-size:10pt;'>บริษัท ตั้งใจพัฒนายานยนต์ จำกัด</th>
						</tr>
						<tr>
							<th colspan='17' style='font-size:9pt;'>รายงานกำไรตามดิว (SYD)</th>
						</tr>
						<tr>
							<td style='text-align:center;' colspan='17'>
								<b>รหัสสาขา</b> &nbsp;&nbsp;".$LOCAT."&nbsp;&nbsp;
								<b>จากเลขที่สัญญา</b>&nbsp;&nbsp;".$CONTNO."&nbsp;&nbsp;
								<b>ณ วันที่</b>&nbsp;&nbsp;".$this->Convertdate(2,$DATE)."&nbsp;&nbsp;
								<b>กลุ่มสินค้า</b>&nbsp;&nbsp;".$GCODE."&nbsp;&nbsp;
							</td>
						</tr>
						<tr>
							<td style='text-align:right;' colspan='17'>Rpsyd 50,51</td>
						</tr>
						<br>
						".$head."
						".$html."
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