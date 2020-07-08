<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/********************************************************
             ______@17/06/2020______
			 Pasakorn Boonded

********************************************************/
class STRProfitStale extends MY_Controller {
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
						<div class='col-sm-12 col-xs-12' style='background-color:#16a085;border:5px solid white;height:75px;text-align:center;font-size:12pt;color:white;font-weight:bold;'>	
							<br>รายงานดอกผลเช่าซื้อค้างรับ (STR)<br>
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
									เลขที่สัญญา
									<div class='input-group'>
										<input type='text' id='CONTNO' class='form-control input-sm' placeholder='เลขที่สัญญา' >
										<span class='input-group-btn'>
											<button id='btnaddcont' class='btn btn-info btn-sm' type='button'>
												<span class='glyphicon glyphicon-hand-up' aria-hidden='true'></span>
											</button>
										</span>
									</div>
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
									พนักงานเก็บเงิน
									<select id='OFFICER' class='form-control input-sm'></select>
								</div>
							</div>
							<div class='col-sm-3'>
								<div class='form-group'>
									กลุ่มสินค้า
									<select id='GCODE' class='form-control input-sm'></select>
								</div>
							</div>
						</div>
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
		$html .="<script src='".base_url('public/js/SYS06/STRProfitStale.js')."'></script>";
		echo $html;
	}
	function conditiontopdf(){
		$data = array();
		$data[] = urlencode($_REQUEST['LOCAT'].'||'.$_REQUEST['CONTNO'].'||'.$_REQUEST['DATE']
		.'||'.$_REQUEST['OFFICER'].'||'.$_REQUEST['GCODE'].'||'.$_REQUEST['order']);
		echo json_encode($this->generateData($data,"encode"));
	}
	function pdf(){
		$data = array();
		$data[] = $_REQUEST["condpdf"];
		$arrs = $this->generateData($data,"decode");
		$arrs[0] = urldecode($arrs[0]);
		//print_r($arrs); exit;
		$tx = explode('||',$arrs[0]);
		$LOCAT     = $tx[0];
		$CONTNO    = $tx[1];
		$DATE      = $this->Convertdate(1,$tx[2]);
		$OFFICER   = $tx[3];
		$GCODE     = $tx[4];
		$order     = $tx[5];
		
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
				<th style='border-bottom:0.1px solid black;text-align:left;'>มูลค่า<br>ราคาขาย</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ดอกผล<br>เช่าซื้อ</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ค่างวด<br>ครบกำหนด</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ดอกผล<br>ครบกำหนด</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>งวดที่</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ค่างวดชำระแล้ว</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ส่วนลด</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ดอกผล<br>ชำระแล้ว</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ดอกผลค้างรับ</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ดอกผล<br>คงเหลือ</th>	
				<th style='border-bottom:0.1px solid black;text-align:right;'>ค่างวดคงเหลือ</th>	
			</tr>
			<tr>
				<td class='wf' style='height:1px;border-top:0.1px solid black;' colspan='17'></td>
			</tr>
		";
		$sql = "
			select A.LOCAT,A.CONTNO,A.TOTPRC,A.TOTPRES,A.CUSCOD,convert(varchar(8),A.SDATE,112) as SDATE
				,B.SNAM+B.NAME1+' '+B.NAME2 as CUSNAME,A.TOTDWN,A.NPAYRES
				,A.NKANG,A.LPAYD,A.BILLCOLL,A.TOT_UPAY,A.EXP_AMT,A.SMPAY,A.EXP_FRM,A.EXP_TO
				,A.PAYDWN,A.LDATE,C.CRCOST,C.PRICE,A.T_NOPAY,A.OPTCST,A.NCARCST,A.NCSHPRC
				,A.NPRICE,A.NPROFIT 
			from {$this->MAuth->getdb('ARMAST')} A,{$this->MAuth->getdb('CUSTMAST')} B
			,{$this->MAuth->getdb('INVTRAN')} C  
			where (A.CUSCOD = B.CUSCOD) and (A.STRNO = C.STRNO) and (A.CONTNO = C.CONTNO) 
			and (A.TSALE = C.TSALE) and (A.LOCAT like '".$LOCAT."%') and (A.CONTNO like '".$CONTNO."%') 
			and (C.GCODE like '".$GCODE."%' or C.GCODE is null) 
			and (A.FDATE <= '".$DATE."') and (A.TOTPRC > A.SMPAY or (A.TOTPRC = A.SMPAY 
			and A.LPAYD > '".$DATE."')) and A.TOTPRC > 0  and A.BILLCOLL like '".$OFFICER."%' 
			union 
			select A.LOCAT,A.CONTNO,A.TOTPRC,A.TOTPRES,A.CUSCOD,convert(varchar(8),A.SDATE,112) as SDATE
				,B.SNAM+B.NAME1+' '+B.NAME2 as CUSNAME,A.TOTDWN,A.NPAYRES
				,A.NKANG,A.LPAYD,A.BILLCOLL,A.TOT_UPAY,A.EXP_AMT,A.SMPAY,A.EXP_FRM,A.EXP_TO
				,A.PAYDWN,A.LDATE,C.CRCOST,C.PRICE,A.T_NOPAY,A.OPTCST,A.NCARCST
				,A.NCSHPRC,A.NPRICE,A.NPROFIT  
			from {$this->MAuth->getdb('HARMAST')} A,{$this->MAuth->getdb('CUSTMAST')} B
			,{$this->MAuth->getdb('HINVTRAN')} C  
			where (A.CUSCOD = B.CUSCOD) and (A.STRNO = C.STRNO) and (A.CONTNO = C.CONTNO) 
			and (A.TSALE = C.TSALE) and (A.LOCAT like '".$LOCAT."%') and (A.CONTNO like '".$CONTNO."%') 
			and (C.GCODE like '".$GCODE."%' or C.GCODE is null) 
			and (A.FDATE <= '".$DATE."') and (A.TOTPRC > A.SMPAY or (A.TOTPRC = A.SMPAY 
			and A.LPAYD > '".$DATE."')) 
			and A.TOTPRC > 0 and A.BILLCOLL like '".$OFFICER."%' 
			union  
			select A.LOCAT,A.CONTNO,A.TOTPRC,A.TOTPRES,A.CUSCOD,convert(varchar(8),A.SDATE,112) as SDATE
				,B.SNAM+B.NAME1+' '+B.NAME2 as CUSNAME,A.TOTDWN,A.NPAYRES
				,A.NKANG,A.LPAYD,A.BILLCOLL,A.TOT_UPAY,A.EXP_AMT,A.SMPAY,A.EXP_FRM,A.EXP_TO
				,A.PAYDWN ,A.LDATE,C.CRCOST,C.PRICE,A.T_NOPAY, A.OPTCST,A.NCARCST
				,A.NCSHPRC,A.NPRICE,A.NPROFIT  
			from {$this->MAuth->getdb('HARMAST')} A,{$this->MAuth->getdb('CHGAR_VIEW')} B
			,{$this->MAuth->getdb('INVTRAN')} C  
			where (A.CONTNO = B.CONTNO) and (A.LOCAT = B.LOCAT) and (A.STRNO = C.STRNO) 
			and (A.CONTNO = C.CONTNO) 
			and (A.TSALE = C.TSALE) and (A.LOCAT like '".$LOCAT."%') and (A.CONTNO like '".$CONTNO."%') 
			and (C.GCODE like '".$GCODE."%' or C.GCODE is null) 
			and (A.FDATE <= '".$DATE."') and (B.DATE1 > '".$DATE."') 
			and (A.TOTPRC > A.SMPAY or (A.TOTPRC = A.SMPAY and A.LPAYD > '".$DATE."')) 
			and A.TOTPRC > 0 and A.BILLCOLL like '".$OFFICER."%' 
			order by A.CONTNO,A.LOCAT,A.CUSCOD
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		$arrs = array();
		if($query->row()){
			foreach($query->result() as $row){$i++;
				$sql1 = "
					declare @maxnopay varchar(max) = (
						select MAX(NOPAY) from (
							select NOPAY 
							from {$this->MAuth->getdb('ARPAY')} 
							where CONTNO = '".$row->CONTNO."' and DDATE <= '".$DATE."'
							union
							select NOPAY 
							from {$this->MAuth->getdb('HARPAY')} 
							where CONTNO = '".$row->CONTNO."' and DDATE <= '".$DATE."'	
						)a
					);
					select CONTNO,LOCAT
						,sum(N_DAMT) as SNETP
						,SUM(STRPROF) as BL_STRPROF
						,(select max(A.NOPAY) from {$this->MAuth->getdb('ARPAY')} A 
						where A.DDATE <= '".$DATE."'  
						and A.LOCAT = '".$row->LOCAT."' and A.CONTNO = '".$row->CONTNO."') as NOPA 
						,(select max(STRPROF) from {$this->MAuth->getdb('ARPAY')} A 
						where NOPAY = @maxnopay and A.LOCAT = '".$row->LOCAT."' 
						and A.CONTNO = '".$row->CONTNO."') as STRPROF_NOPAY
						,(select max(N_DAMT) from {$this->MAuth->getdb('ARPAY')} A 
						where NOPAY = @maxnopay and A.LOCAT = '".$row->LOCAT."' 
						and A.CONTNO = '".$row->CONTNO."') as N_DAMT_NOPAY
						
					from {$this->MAuth->getdb('ARPAY')} where DDATE <= '".$DATE."' 
					and LOCAT = '".$row->LOCAT."' and CONTNO = '".$row->CONTNO."'  
					group by CONTNO,LOCAT 
					union  
					select CONTNO,LOCAT
						,sum(N_DAMT) as SNETP
						,SUM(STRPROF) as BL_STRPROF
						,(select max(A.NOPAY) from {$this->MAuth->getdb('HARPAY')} A 
						where A.DDATE <= '".$DATE."'  
						and A.LOCAT = '".$row->LOCAT."' and A.CONTNO = '".$row->CONTNO."') as NOPA 
						,(select max(STRPROF) from {$this->MAuth->getdb('HARPAY')} A 
						where NOPAY = @maxnopay and A.LOCAT = '".$row->LOCAT."' 
						and A.CONTNO = '".$row->CONTNO."') as STRPROF_NOPAY
						,(select max(N_DAMT) from {$this->MAuth->getdb('HARPAY')} A 
						where NOPAY = @maxnopay and A.LOCAT = '".$row->LOCAT."' 
						and A.CONTNO = '".$row->CONTNO."') as N_DAMT_NOPAY
					from {$this->MAuth->getdb('HARPAY')} where DDATE <= '".$DATE."' 
					and LOCAT = '".$row->LOCAT."' and CONTNO = '".$row->CONTNO."'  
					group by CONTNO,LOCAT
				";
				//echo $sql1; exit;
				$query1 = $this->db->query($sql1);
				if($query1->row()){
					foreach($query1->result() as $row1){
						$sql2 = "
							select 
								sum(case when (A.PAYDT <= '".$DATE."' and ((A.PAYFOR = '006') 
								or (A.PAYFOR = '007'))) 
								then A.PAYAMT_N else 0 end) as PAYAMT_N 
								,sum(case when (A.PAYDT <= '".$DATE."' and ((A.PAYFOR = '006') 
								or (A.PAYFOR = '007'))) 
								then A.DISCT else 0 end) as DISCT  
								,sum(case when (A.PAYDT <= '".$DATE."' and ((A.PAYFOR = '006') 
								or (A.PAYFOR = '007'))) then A.PROFSTR else 0 end) as PROFSTR
							from {$this->MAuth->getdb('CHQTRAN')} A 
							where A.LOCATPAY = '".$row->LOCAT."' and A.CONTNO = '".$row->CONTNO."' 
							and A.FLAG <> 'C' 
						";
						$query2 = $this->db->query($sql2);
						if($query2->row()){
							foreach($query2->result() as $row2){
								//echo $strstale; exit;
								$arrs['NPRICE'][]     = $row->NPRICE;
								$arrs['NPROFIT'][]    = $row->NPROFIT;
								$arrs['SNETP'][]      = $row1->SNETP;
								$arrs['BL_STRPROF'][] = $row1->BL_STRPROF;
								$arrs['PAYAMT_N'][]   = $row2->PAYAMT_N;
								$arrs['DISCT'][]      = $row2->DISCT;
								$arrs['PROFSTR'][]    = $row2->PROFSTR;
								
								$arrs['NPROFIT_T'][]= $row->NPROFIT - $row2->PROFSTR;
								$arrs['BL_PAY'][]     = $row->NKANG - $row2->PAYAMT_N;
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
										<td style='width:5%;text-align:right;'>".number_format($row1->SNETP,2)."</td>
										<td style='width:5%;text-align:right;'>".number_format($row1->BL_STRPROF,2)."</td>
										<td style='width:5%;text-align:right;'>".$row1->NOPA."/".$row->T_NOPAY."</td>
										<td style='width:5%;text-align:right;'>".number_format($row2->PAYAMT_N,2)."</td>
										<td style='width:5%;text-align:right;'>".number_format($row2->DISCT,2)."</td>
										<td style='width:5%;text-align:right;'>".number_format($row2->PROFSTR,2)."</td>
										<td style='width:5%;text-align:right;'>".number_format($row1->BL_STRPROF,2)."</td>
										<td style='width:5%;text-align:right;'>".number_format($row->NPROFIT - $row2->PROFSTR,2)."</td>
										<td style='width:5%;text-align:right;'>".number_format($row->NKANG - $row2->PAYAMT_N,2)."</td>
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
					<td style='text-align:right;'>".number_format(array_sum($arrs['SNETP']),2)."</td>
					<td style='text-align:right;'>".number_format(array_sum($arrs['BL_STRPROF']),2)."</td>
					<td style='text-align:right;' colspan='2'>".number_format(array_sum($arrs['PAYAMT_N']),2)."</td>
					<td style='text-align:right;'>".number_format(array_sum($arrs['DISCT']),2)."</td>
					<td style='text-align:right;'>".number_format(array_sum($arrs['PROFSTR']),2)."</td>
					<td style='text-align:right;'>".number_format(array_sum($arrs['BL_STRPROF']),2)."</td>
					<td style='text-align:right;'>".number_format(array_sum($arrs['NPROFIT_T']),2)."</td>
					<td style='text-align:right;'>".number_format(array_sum($arrs['BL_PAY']),2)."</td>
				</tr>
				<tr>
					<td class='wf' style='height:1px;border-top:0.1px solid black;' colspan='17'></td>
				</tr>
			";
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
		
		if($i > 0){
			$content = "
				<table class='wf' style='font-size:7.5pt;height:700px;border-collapse:collapse;line-height:23px;overflow:wrap;vertical-align:text-top;'>
					<tbody>
						<tr>
							<th colspan='17' style='font-size:10pt;'>บริษัท ตั้งใจพัฒนายานยนต์ จำกัด</th>
						</tr>
						<tr>
							<th colspan='17' style='font-size:9pt;'>รายงานดอกผลเช่าซื้อค้างรับ(STR)</th>
						</tr>
						<tr>
							<td style='text-align:center;' colspan='17'>
								<b>รหัสสาขา</b> &nbsp;&nbsp;".$LOCAT."&nbsp;&nbsp;
								<b>เลขที่สัญญา</b> &nbsp;&nbsp;".$CONTNO."&nbsp;&nbsp;
								<b>ณ วันที่</b>&nbsp;&nbsp;".$this->Convertdate(2,$DATE)."&nbsp;&nbsp;
								<b>พนักงานเก็บเงิน</b>&nbsp;&nbsp;".$OFFICER."&nbsp;&nbsp;
							</td>
						</tr>
						<tr>
							<td style='text-align:right;' colspan='17'>Rpstr 50,51</td>
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