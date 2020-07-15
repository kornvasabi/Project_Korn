<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/********************************************************
             ______@17/06/2020______
			 Pasakorn Boonded

********************************************************/
class STRIncomeMonth extends MY_Controller {
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
									ลูกหนี้ ณ วันที่
									<input type='text' id='DATE' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' value='".$this->today('endofmonth')."' placeholder='ถึงวันที่ขาย' >
								</div>
							</div>
							<div class='col-sm-3'>
								<div class='form-group'>
									กลุ่มสินค้า
									<select id='GCODE' class='form-control input-sm'></select>
								</div>
							</div>
							<div class='col-sm-3'>
								<div class='form-group'>
									Billcoll
									<select id='OFFICER' class='form-control input-sm'></select>
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
		$html .="<script src='".base_url('public/js/SYS06/STRIncomeMonth.js')."'></script>";
		echo $html;
	}
	function getendmonth(){
		$DATE = $this->Convertdate(1,$_REQUEST['DATE']);
		$sql = "
			select convert(varchar(8),(dateadd(day,-1,convert(varchar(6)
			,dateadd(month,1,'".$DATE."'),112)+'01')),112) as endofmonth
		";
		$query = $this->db->query($sql);
		$row = $query->row();
		$response['TODATE'] = $this->Convertdate(2,$row->endofmonth);
		echo json_encode($response);
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
				<td class='wf' style='height:1px;border-top:0.1px solid black;' colspan='13'></td>
			</tr>
			<tr>
				<th style='border-bottom:0.1px solid black;text-align:left;'>No.</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>สาขา</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>เลขที่สัญญา</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>ชื่อ - สกุล</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>วันทำสัญญา</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>วันดิวงวดแรก</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>จน. ทั้งหมด</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ดผ.ทั้งสัญญา<br>ลน.คงเหลือจริง</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>งวดนี้งวดที่<br>ดผ. ตามดิวงวดนี้</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ดผ.สะสมถึงงวดก่อนตามดิว<br>สล.ดอผลงวดนี้</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>สล.ดอกงวดก่อน<br>รับดอกผลจริงเดือนนี้</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ดผ.สะสมถึงงวดก่อนตามรับจริง<br>ดผ.งวดนี้ตาม บช.</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ดผ. สะสมต้องบันทึกบัญชี<br>ดผ.คงเหลือตาม บช.</th>
				
			</tr>
			<tr>
				<td class='wf' style='height:1px;border-top:0.1px solid black;' colspan='13'></td>
			</tr>
		";
		$sql = "
			select YEAR('".$DATE."') as getyear
			,MONTH('".$DATE."') as getmonth
			,convert(varchar(8),(convert(varchar(6),'".$DATE."',112)+'01'),112) as startofmonth
			,convert(varchar(8),(dateadd(day,-1,convert(varchar(6),dateadd(month,1,'".$DATE."'),112)+'01')),112) as endofmonth
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		$rown = $query->row();
		$getyear  = $rown->getyear;
		$getmonth = $rown->getmonth;
		$startofmonth = $rown->startofmonth;
		$endofmonth  = $rown->endofmonth;
		
		$sql = "
			select A.LOCAT,A.CONTNO,A.TOTPRC,A.TOTPRES,A.CUSCOD,convert(varchar(8),A.SDATE,112) as SDATE
				,B.SNAM+B.NAME1+' '+B.NAME2 as CUSNAME,A.TOTDWN
				,A.NPAYRES,A.NKANG,A.LPAYD,A.BILLCOLL,A.TOT_UPAY,A.EXP_AMT,A.SMPAY,A.EXP_FRM
				,A.EXP_TO,A.PAYDWN,A.LDATE,C.CRCOST,C.PRICE,A.T_NOPAY,A.OPTCST
				,A.NCARCST,A.NCSHPRC,A.NPRICE ,A.NPROFIT,convert(varchar(8),A.FDATE,112) as FDATE 
			from {$this->MAuth->getdb('ARMAST')} A,{$this->MAuth->getdb('CUSTMAST')} B
			,{$this->MAuth->getdb('INVTRAN')} C  
			where (A.CUSCOD = B.CUSCOD) and (A.STRNO = C.STRNO) and (A.CONTNO = C.CONTNO)  
			and (A.TSALE = C.TSALE) and (A.LOCAT like '".$LOCAT."%') and (A.CONTNO like '".$CONTNO."%') 
			and (C.GCODE like '".$GCODE."%' or C.GCODE is null) and (A.SDATE <= '".$endofmonth."')  
			and (A.TOTPRC > A.SMPAY or (A.TOTPRC = A.SMPAY and A.LPAYD >= '".$startofmonth."')) and A.TOTPRC > 0  
			and BILLCOLL like '".$OFFICER."%' 
			union 
			select A.LOCAT,A.CONTNO,A.TOTPRC,A.TOTPRES,A.CUSCOD,convert(varchar(8),A.SDATE,112) as SDATE
				,B.SNAM+B.NAME1+' '+B.NAME2 as CUSNAME,A.TOTDWN
				,A.NPAYRES,A.NKANG,A.LPAYD,A.BILLCOLL,A.TOT_UPAY,A.EXP_AMT,A.SMPAY,A.EXP_FRM
				,A.EXP_TO,A.PAYDWN,A.LDATE,C.CRCOST,C.PRICE,A.T_NOPAY,A.OPTCST
				,A.NCARCST,A.NCSHPRC,A.NPRICE,A.NPROFIT,convert(varchar(8),A.FDATE,112) as FDATE  
			from {$this->MAuth->getdb('HARMAST')} A,{$this->MAuth->getdb('CUSTMAST')} B
			,{$this->MAuth->getdb('HINVTRAN')} C  
			where (A.CUSCOD = B.CUSCOD) and (A.STRNO = C.STRNO) and (A.CONTNO = C.CONTNO)  
			and (A.TSALE = C.TSALE) and (A.LOCAT like '".$LOCAT."%') and (A.CONTNO like '".$CONTNO."%') 
			and (C.GCODE like '".$GCODE."%' or C.GCODE is null) and (A.SDATE <= '".$endofmonth."') 
			and (A.TOTPRC > A.SMPAY or (A.TOTPRC = A.SMPAY and A.LPAYD >= '".$startofmonth."')) and A.TOTPRC > 0  
			and BILLCOLL like '".$OFFICER."%' 
			union  
			select A.LOCAT,A.CONTNO,A.TOTPRC,A.TOTPRES,A.CUSCOD,convert(varchar(8),A.SDATE,112) as SDATE
				,B.SNAM+B.NAME1+' '+B.NAME2 as CUSNAME,A.TOTDWN
				,A.NPAYRES,A.NKANG,A.LPAYD,A.BILLCOLL,A.TOT_UPAY,A.EXP_AMT,A.SMPAY,A.EXP_FRM
				,A.EXP_TO,A.PAYDWN,A.LDATE,C.CRCOST,C.PRICE,A.T_NOPAY,A.OPTCST
				,A.NCARCST,A.NCSHPRC,A.NPRICE,A.NPROFIT,convert(varchar(8),A.FDATE,112) as FDATE
			from {$this->MAuth->getdb('HARMAST')} A,{$this->MAuth->getdb('CHGAR_VIEW')} B
			,{$this->MAuth->getdb('INVTRAN')} C  
			where (A.CONTNO = B.CONTNO) and (A.LOCAT = B.LOCAT) and (A.STRNO = C.STRNO) 
			and (A.CONTNO = C.CONTNO) and (A.TSALE = C.TSALE) and (A.LOCAT like '".$LOCAT."%') 
			and (A.CONTNO like '".$CONTNO."%') and (C.GCODE like '".$GCODE."%' or C.GCODE is null) 
			and (A.SDATE <= '".$endofmonth."') and (B.DATE1 > '".$startofmonth."') and (A.TOTPRC > 
			A.SMPAY or (A.TOTPRC = A.SMPAY and A.LPAYD >= '".$startofmonth."')) and A.TOTPRC > 0 
			and BILLCOLL like '".$OFFICER."%'  
			order by A.CONTNO,A.LOCAT
		";
		echo $sql;
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){$i++;
				$sql1 = "
					select 
						sum(case when (((month(A.PAYDT) < '".$getmonth."' 
						and year(A.PAYDT) = '".$getyear."') or year(A.PAYDT) < '".$getyear."') 
						and (A.PAYFOR = '006') or (A.PAYFOR = '007')) then A.PAYAMT_N else 0 end) as SNETP 
						,sum(case when ((month(A.PAYDT) < '".$getmonth."' 
						and year(A.PAYDT) = '".$getyear."') or year(A.PAYDT) < '".$getyear."') 
						and A.PAYFOR = '002' then A.PAYAMT_N else 0 end) as SNETP1
						,sum(case when (((month(A.PAYDT) < '".$getmonth."' 
						and year(A.PAYDT) = '".$getyear."') or year(A.PAYDT) < '".$getyear."') 
						and (A.PAYFOR = '006') or (A.PAYFOR = '007')) then A.DISCT else 0 end) as DISCT
						,sum(case when ((month(A.PAYDT) = '".$getmonth."' 
						and year(A.PAYDT) = '".$getyear."') and (A.PAYFOR = '006') 
						or (A.PAYFOR = '007')) then A.PAYAMT_N else 0 end) as SNETP2
						,sum(case when (month(A.PAYDT) = '".$getmonth."' 
						and year(A.PAYDT) = '".$getyear."') and A.PAYFOR = '002' 
						then A.PAYAMT_N else 0 end) as SNETP3
						,sum(case when ((month(A.PAYDT) = '".$getmonth."' 
						and year(A.PAYDT) = '".$getyear."') and (A.PAYFOR = '006') 
						or (A.PAYFOR = '007')) then A.DISCT else 0 end) as DISCT1  
					from {$this->MAuth->getdb('CHQTRAN')} A where A.LOCATPAY = '".$row->LOCAT."' 
					and A.CONTNO = '".$row->CONTNO."' and A.FLAG <> 'C' 
				";
				echo $sql1;
				$query1 = $this->db->query($sql1);
				if($query1->row()){
					foreach($query1->result() as $row1){
						$sql2 = "
							IF OBJECT_ID('tempdb..#pay') IS NOT NULL DROP TABLE #pay
							select *
							into #pay
							FROM(
								select NOPAY,DDATE,STRPROF,N_DAMT from {$this->MAuth->getdb('ARPAY')} 
								where LOCAT = '".$row->LOCAT."' and CONTNO = '".$row->CONTNO."' 
								union 
								select NOPAY,DDATE,STRPROF,N_DAMT from {$this->MAuth->getdb('HARPAY')} 
								where LOCAT = '".$row->LOCAT."' and CONTNO = '".$row->CONTNO."'
							)pay
						";
						echo $sql2;
						$this->db->query($sql2);
						$sql3 = "
							declare @tonopay varchar(max) = (
								select NOPAY from #pay 
								where month(DDATE) = '".$getmonth."' and year(DDATE) = '".$getyear."'
							);
							--ดอกผลเช่าซื้อตามดิวงวดนี้
							declare @tostrprof varchar(max) = (
								select STRPROF from #pay 
								where month(DDATE) = '".$getmonth."' and year(DDATE) = '".$getyear."'
							);
							select @tonopay as TONOPAY,@tostrprof as TOSTRPROF
						";
						echo $sql3; exit;
						$query3 = $this->db->query($sql3);
						if($query3->row()){
							foreach($query3->result() as $row3){
								$html .="
									<tr>
										<td style='width:2%;text-align:left;'>".$i."</td>
										<td style='width:3%;text-align:left;'>".$row->LOCAT."</td>
										<td style='width:7%;text-align:left;'>".$row->CONTNO."</td>
										<td style='width:10%;text-align:left;'>".$row->CUSNAME."</td>
										<td style='width:5%;text-align:left;'>".$row->SDATE."</td>
										<td style='width:5%;text-align:left;'>".$row->FDATE."</td>
										<td style='width:5%;text-align:right;'>".$row->T_NOPAY."</td>
										<td style='width:9%;text-align:right;'>".number_format($row->NPROFIT,2)."<br>".number_format($row->NKANG - $row1->SNETP,2)."</td>
										<td style='width:9%;text-align:right;'>".$row3->TONOPAY."<br>".number_format($row3->TOSTRPROF,2)."</td>
										<td style='width:9%;text-align:right;'><br>".number_format($row1->DISCT1,2)."</td>
										<td style='width:9%;text-align:right;'>".number_format($row1->DISCT,2)."<br></td>
										<td style='width:9%;text-align:right;'></td>
										<td style='width:9%;text-align:right;'></td>
									</tr>
								";
							}
						}	
					}
				}
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
		
		if($i > 0){
			$content = "
				<table class='wf' style='font-size:7.5pt;height:700px;border-collapse:collapse;line-height:23px;overflow:wrap;vertical-align:text-top;'>
					<tbody>
						<tr>
							<th colspan='13' style='font-size:10pt;'>บริษัท ตั้งใจพัฒนายานยนต์ จำกัด</th>
						</tr>
						<tr>
							<th colspan='13' style='font-size:9pt;'>รายงานการรับรู้รายได้ประจำเดือน &nbsp;".$getmonth."&nbsp;ปี &nbsp;".$getyear." (SYD)</th>
						</tr>
						<tr>
							<td style='text-align:center;' colspan='13'>
								<b>รหัสสาขา</b> &nbsp;&nbsp;".$LOCAT."&nbsp;&nbsp;
								<b>เลขที่สัญญา</b> &nbsp;&nbsp;".$CONTNO."&nbsp;&nbsp;
								<b>กลุ่มสินค้า</b>&nbsp;&nbsp;".$GCODE."&nbsp;&nbsp;
							</td>
						</tr>
						<tr>
							<td style='text-align:right;' colspan='13'>RpArD 10,11</td>
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