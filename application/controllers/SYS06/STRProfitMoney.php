<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/********************************************************
             ______@17/06/2020______
			 Pasakorn Boonded

********************************************************/
class STRProfitMoney extends MY_Controller {
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
							<br>รายงานกำไรตามวันที่ได้รับเงิน (STR)<br>
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
									จากวันที่
									<input type='text' id='F_DATE' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' value='".$this->today('today')."' placeholder='ถึงวันที่ขาย' >
								</div>
							</div>
							<div class='col-sm-3'>
								<div class='form-group'>
									ถึงวันที่
									<input type='text' id='T_DATE' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' value='".$this->today('today')."' placeholder='ถึงวันที่ขาย' >
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
									<div class='col-sm-3'>
										<div class='form-group'>
											<br>
											<label>
												<input type= 'radio' id='OR1' name='order' checked> รหัสสาขา
											</label>
										</div>
									</div>
									<div class='col-sm-3'>
										<div class='form-group'>
											<br>
											<label>
												<input type= 'radio' id='OR2' name='order'> เลขที่สัญญา
											</label>
										</div>
									</div>
									<div class='col-sm-3'>
										<div class='form-group'>
											<br>
											<label>
												<input type= 'radio' id='OR3' name='order'> รหัสลูกค้า
											</label>
										</div>
									</div>
									<div class='col-sm-3'>
										<div class='form-group'>
											<br>
											<label>
												<input type= 'radio' id='OR4' name='order'> วันที่ใบเสร็จ
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
		$html .="<script src='".base_url('public/js/SYS06/STRProfitMoney.js')."'></script>";
		echo $html;
	}
	function conditiontopdf(){
		$data = array();
		$data[] = urlencode($_REQUEST['LOCAT'].'||'.$_REQUEST['CONTNO'].'||'.$_REQUEST['F_DATE']
		.'||'.$_REQUEST['T_DATE'].'||'.$_REQUEST['OFFICER'].'||'.$_REQUEST['GCODE'].'||'.$_REQUEST['order']);
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
		$F_DATE    = $this->Convertdate(1,$tx[2]);
		$T_DATE    = $this->Convertdate(1,$tx[3]);
		$OFFICER   = $tx[4];
		$GCODE     = $tx[5];
		$order     = $tx[6];
		
		$head = ""; $html = ""; $i = 0;
		$head = "
			<tr class='wm'>
				<td class='wf' style='height:1px;border-top:0.1px solid black;' colspan='16'></td>
			</tr>
			<tr>
				<th style='border-bottom:0.1px solid black;text-align:left;'>No.</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>สาขาที่รับ</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>วันที่ใบเสร็จ</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>เลขที่ใบเสร็จ</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>เลขที่สัญญา</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>ชื่อ - สกุล</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>ชำระโดย</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>จน.งวด<br>ทั้งหมด</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ดอกผล<br>ทั้งหมด</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ดอกผล<br>สะสมงวดก่อน</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ส่วนลด<br>ละสม</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ชำระงวดที่</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>จำนวนเงิน<br>ตัดลูกหนี้</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ส่วนลด</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ดอกผล<br>งวดนี้</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ดอกผลคงเหลือ</th>	
			</tr>
			<tr>
				<td class='wf' style='height:1px;border-top:0.1px solid black;' colspan='16'></td>
			</tr>
		";
		$sql = "
			select A.LOCAT,convert(varchar(8),C.TMBILDT,112) as TMBILDT,A.CONTNO
				,A.CUSCOD,B.SNAM+B.NAME1+' '+B.NAME2 as CUSNAME,A.TOT_UPAY,C.TMBILL
				,C.LOCATRECV,C.PAYAMT,C.F_PAR,C.F_PAY,C.L_PAY,C.L_PAR,C.PAYAMT_N
				,A.PAYDWN,A.T_NOPAY,D.BILLDT,D.BILLNO,A.NPRICE
				,A.NPROFIT,convert(varchar(8),C.PAYDT,112) as PAYDT,C.PAYTYP,A.NKANG,C.DISCT  
			from {$this->MAuth->getdb('ARMAST')} A,{$this->MAuth->getdb('CUSTMAST')} B
			,{$this->MAuth->getdb('CHQTRAN')} C,{$this->MAuth->getdb('CHQMAS')} D 
			where (A.CUSCOD = B.CUSCOD) and (A.CONTNO = C.CONTNO) and (C.TMBILL = D.TMBILL) 
			and (A.LOCAT like '".$LOCAT."%') and (A.LOCAT = C.LOCATPAY) and (C.LOCATRECV = D.LOCATRECV) 
			and (A.CONTNO like '".$CONTNO."%') and (A.BILLCOLL like '".$OFFICER."%') 
			and (C.PAYDT between '".$F_DATE."' and '".$T_DATE."') and A.TOTPRC > 0 and C.FLAG <> 'C' 
			and (C.PAYFOR = '006' or C.PAYFOR = '007') and (C.F_PAY > 0) 
			and (C.PAYAMT_N) > 0 and A.NPROFIT > 0 and A.CONTNO in (
				select CONTNO from {$this->MAuth->getdb('INVTRAN')} where GCODE like '".$GCODE."%' 
				union 
				select CONTNO from {$this->MAuth->getdb('HINVTRAN')} where GCODE like '".$GCODE."%'
			)
			union  
			select A.LOCAT,convert(varchar(8),C.TMBILDT,112) as TMBILDT,A.CONTNO
				,A.CUSCOD,B.SNAM+B.NAME1+' '+B.NAME2 as CUSNAME,A.TOT_UPAY,C.TMBILL
				,C.LOCATRECV,C.PAYAMT,C.F_PAR,C.F_PAY,C.L_PAY,C.L_PAR,C.PAYAMT_N
				,A.PAYDWN,A.T_NOPAY,D.BILLDT,D.BILLNO,A.NPRICE
				,A.NPROFIT,convert(varchar(8),C.PAYDT,112) as PAYDT,C.PAYTYP,A.NKANG,C.DISCT  
			from {$this->MAuth->getdb('HARMAST')} A,{$this->MAuth->getdb('CHGAR_VIEW')} B
			,{$this->MAuth->getdb('CHQTRAN')} C,{$this->MAuth->getdb('CHQMAS')} D 
			where (A.CONTNO = B.CONTNO) and (A.LOCAT = B.LOCAT) and (A.CONTNO = C.CONTNO) 
			and (C.TMBILL = D.TMBILL) and (A.LOCAT like '".$LOCAT."%') and (A.LOCAT = C.LOCATPAY) 
			and (C.LOCATRECV = D.LOCATRECV) and (A.CONTNO like '".$CONTNO."%')  
			and (A.BILLCOLL like '".$OFFICER."%') and (C.PAYDT between '".$F_DATE."' and '".$T_DATE."') 
			and A.TOTPRC > 0 and C.FLAG <> 'C' and (C.PAYFOR = '006' or C.PAYFOR = '007') 
			and (C.F_PAY > 0) and (C.PAYAMT_N > 0 ) and A.NPROFIT > 0     
			and A.CONTNO in (
				select CONTNO from {$this->MAuth->getdb('INVTRAN')} where GCODE like '".$GCODE."%' 
				union 
				select CONTNO from {$this->MAuth->getdb('HINVTRAN')} where GCODE like '".$GCODE."%'
			) 
			order by A.CONTNO
		";
		//echo $sql; 
		$query = $this->db->query($sql);
		$arrs = array();
		if($query->row()){
			foreach($query->result() as $row){$i++;
				$lp = "";
				if($row->F_PAR == "*" or $row->L_PAR == "*"){
					$lp = "<";
				}else{
					$lp = "<=";
				}	
				$sql1 = "
					IF OBJECT_ID('tempdb..#pay') IS NOT NULL DROP TABLE #pay
					select *
					into #pay
					FROM(
						select 
							NOPAY,DDATE,CONTNO,LOCAT,V_DAMT,N_DAMT,STRPROF,DATE1 
						from {$this->MAuth->getdb('ARPAY')} 
						where CONTNO = '".$row->CONTNO."' and LOCAT = '".$row->LOCAT."'
						union
						select 
							NOPAY,DDATE,CONTNO,LOCAT,V_DAMT,N_DAMT,STRPROF,DATE1 
						from {$this->MAuth->getdb('HARPAY')} 
						where CONTNO = '".$row->CONTNO."' and LOCAT = '".$row->LOCAT."'
					)pay
				";
				//echo $sql1;
				$this->db->query($sql1);
				$sql2 = "
					declare @payf decimal(18,2) = (
						select sum(A.payamt_n) as payf 
						from {$this->MAuth->getdb('CHQTRAN')} a,{$this->MAuth->getdb('CHQMAS')} B 
						where  (a.tmbill = b.tmbill) and (a.locatrecv = b.locatrecv) 
						and (a.payfor = '006' or a.payfor = '007') 
						and (a.flag <> 'C') and a.contno = '".$row->CONTNO."' and  (
							(a.paydt < '".$row->TMBILDT."') or (a.paydt = '".$row->TMBILDT."' 
							AND B.BILLNO < '".$row->BILLNO."')
						) AND (A.F_PAY <= '".$row->F_PAY."' AND A.L_PAY <= '".$row->F_PAY."') 
						AND (a.paydt IS NOT NULL) 
						and (a.locatpay = '".$row->LOCAT."')
					); 
					declare @bl_strprof decimal(18,2) = (
						select isnull(SUM(STRPROF),0) from #pay
						where DATE1 < '".$row->PAYDT."'
					);
					--ดอกผลคงเหลือ
					declare @bf_strprof decimal(18,2) = (
						select 
							ISNULL(STRPROF * (((@payf % N_DAMT) * 100 / N_DAMT) / 100) + @bl_strprof,0) as bf_strprof
							from #pay  
						where CONTNO = '".$row->CONTNO."' and NOPAY = '".$row->F_PAY."'
					);
					--ส่วนลดสะสม
					declare @sumdisct decimal(18,2) = (
						select 
							isnull(sum(A.DISCT),0) AS DISCT  
						from {$this->MAuth->getdb('CHQTRAN')} A,{$this->MAuth->getdb('CHQMAS')} B 
						where (A.TMBILL = B.TMBILL) and (A.LOCATRECV = B.LOCATRECV) 
						and (A.PAYFOR = '006' or A.PAYFOR = '007') and (A.FLAG <> 'C') 
						and A.CONTNO = '".$row->CONTNO."' and  ((A.PAYDT < '".$row->PAYDT."' ) 
						or (A.PAYDT = '".$row->PAYDT."' and B.BILLNO < '".$row->BILLNO."')) 
						and (A.F_PAY <= '".$row->F_PAY."' and A.L_PAY <= '".$row->F_PAY."') 
						and (a.paydt is not null) and (A.LOCATPAY = '".$row->LOCAT."') 
					);
					declare @topay decimal(18,2) = (
						select 
							isnull(case when ".$row->TOT_UPAY." > ".$row->PAYAMT." then 0 else SUM(STRPROF) end ,0) 
						from #pay where NOPAY >= '".$row->F_PAY."' and NOPAY ".$lp." '".$row->L_PAY."'
					)
					--ดอกผลงวดนี้
					declare @strprof_topay decimal(18,2) = (
						select STRPROF * (((".$row->PAYAMT." % N_DAMT) * 100 / N_DAMT) / 100) + @topay
						from #pay 
						where CONTNO = '".$row->CONTNO."' and NOPAY = '".$row->L_PAY."'
					);
					
					/*
					declare @strprof_topay decimal(18,2) = (
						select SUM(strprof_topay) from (
							select ISNULL(SUM(STRPROF) + @strprof_nofull,0) as strprof_topay 
							from #pay
							where CONTNO = '".$row->CONTNO."' and NOPAY >= '".$row->F_PAY."' 
							and NOPAY ".$lp." '".$row->L_PAY."'
						)a
					);
					*/
					select @bf_strprof as BF_STRPROF,@sumdisct as SUMDISCT
					,@strprof_topay as STRPROF_TOPAY
				";
				//echo $sql2; exit;
				$query1 = $this->db->query($sql2);
				if($query1->row()){
					foreach($query1->result() as $row1){
						$arrs['PAYAMT_N'][]   = $row->PAYAMT_N;
						$arrs['DISCT'][]      = $row->DISCT;
						$arrs['STRPROF_TOPAY'][]   = $row1->STRPROF_TOPAY;
						$arrs['STRPROF_BL'][] = $row->NPROFIT - $row1->BF_STRPROF;
						
						$html .="
							<tr>
								<td style='width:2%;text-align:left;'>".$i."</td>
								<td style='width:5%;text-align:left;'>".$row->LOCATRECV."</td>
								<td style='width:7%;text-align:left;'>".$row->TMBILDT."</td>
								<td style='width:8%;text-align:left;'>".$row->BILLNO."</td>
								<td style='width:8%;text-align:left;'>".$row->CONTNO."</td>
								<td style='width:14%;text-align:left;'>".$row->CUSNAME."</td>
								<td style='width:5%;text-align:right;'>".$row->PAYTYP."</td>
								<td style='width:5%;text-align:right;'>".$row->T_NOPAY."</td>
								<td style='width:6%;text-align:right;'>".number_format($row->NPROFIT,2)."</td>
								<td style='width:6%;text-align:right;'>".number_format($row1->BF_STRPROF,2)."</td>
								<td style='width:5%;text-align:right;'>".number_format($row1->SUMDISCT,2)."</td>
								<td style='width:5%;text-align:right;'>".$row->F_PAR."".$row->F_PAY."-".$row->L_PAY."".$row->L_PAR."</td>
								<td style='width:5%;text-align:right;'>".number_format($row->PAYAMT_N,2)."</td>
								<td style='width:5%;text-align:right;'>".number_format($row->DISCT,2)."</td>
								<td style='width:6%;text-align:right;'>".number_format($row1->STRPROF_TOPAY,2)."</td>
								<td style='width:7%;text-align:right;'>".number_format($row->NPROFIT - $row1->BF_STRPROF,2)."</td>
							</tr>
						";	
					}
				}
			}
		}
		if($i > 0){
			$html .="
				<tr>
					<td class='wf' style='height:1px;border-top:0.1px solid black;' colspan='16'></td>
				</tr>
				<tr>
					<td style='width:%;text-align:left;' colspan='3'><b>รวมทั้งสิน</b></td>
					<td style='width:%;text-align:left;' colspan='2'>".$i."</td>
					<td style='width:%;text-align:left;' colspan='3'><b>รายการ</b></td>
					<td style='width:%;text-align:right;' colspan='5'>".number_format(array_sum($arrs['PAYAMT_N']),2)."</td>
					<td style='width:%;text-align:right;'>".number_format(array_sum($arrs['DISCT']),2)."</td>
					<td style='width:%;text-align:right;'>".number_format(array_sum($arrs['STRPROF_TOPAY']),2)."</td>
					<td style='width:%;text-align:right;'>".number_format(array_sum($arrs['STRPROF_BL']),2)."</td>
				</tr>
				<tr>
					<td class='wf' style='height:1px;border-top:0.1px solid black;' colspan='16'></td>
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
							<th colspan='16' style='font-size:10pt;'>บริษัท ตั้งใจพัฒนายานยนต์ จำกัด</th>
						</tr>
						<tr>
							<th colspan='16' style='font-size:9pt;'>รายงานกำไรตามการรับชำระ(STR)</th>
						</tr>
						<tr>
							<td style='text-align:center;' colspan='16'>
								<b>รหัสสาขา</b> &nbsp;&nbsp;".$LOCAT."&nbsp;&nbsp;
								<b>เลขที่สัญญา</b>&nbsp;&nbsp;".$CONTNO."&nbsp;&nbsp;
								<b>จากวันที่รับชำระ</b>&nbsp;&nbsp;".$this->Convertdate(2,$F_DATE)."&nbsp;&nbsp;
								<b>ถึงวันที่</b>&nbsp;&nbsp;".$this->Convertdate(2,$T_DATE)."&nbsp;&nbsp;
								<b>พนักงานเก็บเงิน</b>&nbsp;&nbsp;".$OFFICER."&nbsp;&nbsp;
							</td>
						</tr>
						<tr>
							<td style='text-align:right;' colspan='16'>Rpstr 40,41</td>
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