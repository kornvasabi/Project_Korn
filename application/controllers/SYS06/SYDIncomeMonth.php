<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/********************************************************
             ______@03/06/2020______
			 Pasakorn Boonded

********************************************************/
class SYDIncomeMonth extends MY_Controller {
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
							<br>รายงานการรับรู้รายได้ (SYD)<br>
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
									ลูกหนี้ ณ. วันที่
									<input type='text' id='TODATE' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' value='".$this->today('endofmonth')."'>
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
									billcoll
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
							<div class='col-sm-12' style='height:170px;'></div>
						</div>
					</div>	
				</div>
			</div>
		";
		$html .="<script src='".base_url('public/js/SYS06/SYDIncomeMonth.js')."'></script>";
		echo $html;
	}
	function getendmonth(){
		$TODATE = $this->Convertdate(1,$_REQUEST['TODATE']);
		$sql = "
			select convert(varchar(8),(dateadd(day,-1,convert(varchar(6),dateadd(month,1,'".$TODATE."'),112)+'01')),112) as endofmonth
		";
		$query = $this->db->query($sql);
		$row = $query->row();
		$response['TODATE'] = $this->Convertdate(2,$row->endofmonth);
		echo json_encode($response);
	}
	function conditiontopdf(){
		$data = array();
		$data[] = urlencode($_REQUEST['LOCAT'].'||'.$_REQUEST['CONTNO'].'||'.$_REQUEST['TODATE']
		.'||'.$_REQUEST['GCODE'].'||'.$_REQUEST['OFFICER'].'||'.$_REQUEST['order']);
		echo json_encode($this->generateData($data,"encode"));
	}
	function pdf(){
		$data = array();
		$data[] = $_REQUEST["condpdf"];
		$arrs = $this->generateData($data,"decode");
		$arrs[0] = urldecode($arrs[0]);
		
		$tx = explode('||',$arrs[0]);
		$LOCAT        = $tx[0];
		$CONTNO       = $tx[1];
		$TODATE       = $this->Convertdate(1,$tx[2]);
		$GCODE        = $tx[3];
		$OFFICER   	  = $tx[4];
		$order        = $tx[5];
		
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
				<th style='border-bottom:0.1px solid black;text-align:left;'>วันทำสัญญา<br>วันดิวงวดแรก</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>จน. งวด<br>งวดนี้งวดที่</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ดอกผลทั้งสัญญา<br>ลูกหนี้คงเหลือจริง</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ส่วนลด ดผ. งวดก่อน<br>ดผ. ถึงงวดก่อนตามดิว</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ดผ.ถึง งก.ตามรับจริง<br>ดผ.ตามดิวงวดนี้</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ส่วนลดดอกผลงวดนี้<br>ดผ. ถึงงวดก่อนตาม บช.</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>รับดอกผลจริงเดือนนี้<br>ดอกผลงวดนี้ตามบช.</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ดผ. คงเหลือตามจริง<br>ดอกผลถึงงวดนี้ตามบช.</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ดผ.คงเหลือตามจริง<br>ดผ.คงเหลือตามบช.</th>
				
			</tr>
			<tr>
				<td class='wf' style='height:1px;border-top:0.1px solid black;' colspan='13'></td>
			</tr>
		";
		$sql = "
			select YEAR('".$TODATE."')+543 as getyear,MONTH('".$TODATE."') as getmonth
			,convert(varchar(8),(convert(varchar(6),'".$TODATE."',112)+'01'),112) as startofmonth
			,convert(varchar(8),(dateadd(day,-1,convert(varchar(6),dateadd(month,1,'".$TODATE."'),112)+'01')),112) as endofmonth
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		$rown = $query->row();
		$getyear  = $rown->getyear;
		$getmonth = $rown->getmonth;
		$startofmonth = $rown->startofmonth;
		$endofmonth   = $rown->endofmonth;
		$sql = "
			select A.LOCAT,A.CONTNO,A.TOTPRC,A.TOTPRES,A.CUSCOD,convert(varchar(8),A.SDATE,112) as SDATE
				,B.SNAM+B.NAME1+' '+B.NAME2 as CUSNAME,A.TOTDWN,A.NPAYRES,A.NKANG,A.LPAYD,A.BILLCOLL
				,A.TOT_UPAY,A.EXP_AMT,A.SMPAY,A.EXP_FRM,A.EXP_TO,A.PAYDWN,convert(varchar(8),A.FDATE,112) as FDATE
				,C.CRCOST,C.PRICE,A.T_NOPAY, A.OPTCST,A.NCARCST,A.NCSHPRC,A.NPRICE,A.NPROFIT  
			from {$this->MAuth->getdb('ARMAST')} A,{$this->MAuth->getdb('CUSTMAST')} B
			,{$this->MAuth->getdb('INVTRAN')} C 
			where (A.CUSCOD = B.CUSCOD) and (A.STRNO = C.STRNO) and (A.CONTNO = C.CONTNO) 
			and (A.TSALE = C.TSALE) and (A.LOCAT like '".$LOCAT."%') and (A.CONTNO like '".$CONTNO."%')  
			and (C.GCODE like '".$GCODE."%' or C.GCODE is null) and (A.SDATE <= '".$endofmonth."')  
			and (A.TOTPRC > A.SMPAY or (A.TOTPRC = A.SMPAY 
			and (select MAX(PAYDT) from {$this->MAuth->getdb('CHQTRAN')} where CONTNO = A.CONTNO 
			and LOCATPAY=A.LOCAT and FLAG <> 'C' and PAYFOR in ('006','007') AND PAYAMT > 0) >= '".$startofmonth."')) 
			and A.TOTPRC > 0 and A.BILLCOLL like '".$OFFICER."%'  
			union  
			select A.LOCAT,A.CONTNO,A.TOTPRC,A.TOTPRES,A.CUSCOD,convert(varchar(8),A.SDATE,112) as SDATE
				,B.SNAM+B.NAME1+' '+B.NAME2 as CUSNAME,A.TOTDWN,A.NPAYRES,A.NKANG,A.LPAYD,A.BILLCOLL
				,A.TOT_UPAY,A.EXP_AMT,A.SMPAY,A.EXP_FRM,A.EXP_TO,A.PAYDWN,convert(varchar(8),A.FDATE,112) as FDATE
				,C.CRCOST,C.PRICE,A.T_NOPAY,A.OPTCST,A.NCARCST,A.NCSHPRC,A.NPRICE,A.NPROFIT  
			from {$this->MAuth->getdb('HARMAST')} A,{$this->MAuth->getdb('CUSTMAST')} B
			,{$this->MAuth->getdb('HINVTRAN')} C,{$this->MAuth->getdb('ARLOST')} R   
			where (A.CUSCOD = B.CUSCOD) and (A.STRNO = C.STRNO) and (A.CONTNO = C.CONTNO)  
			and (A.TSALE = C.TSALE) and (A.LOCAT like '".$LOCAT."%') and (A.CONTNO like '".$CONTNO."%')  
			and (C.GCODE like '".$GCODE."%' or C.GCODE is null) and (A.SDATE <= '".$endofmonth."') 
			and (R.LOSTDT >= '".$endofmonth."') and A.YSTAT <> 'Y' and (A.TOTPRC > A.SMPAY or (A.TOTPRC = A.SMPAY 
			and (select MAX(PAYDT) from {$this->MAuth->getdb('CHQTRAN')} where CONTNO = A.CONTNO and LOCATPAY = A.LOCAT and FLAG <>'C' 
			and PAYFOR in ('006','007') and PAYAMT > 0) >= '".$startofmonth."')) and A.TOTPRC > 0 
			and A.BILLCOLL like '".$OFFICER."%'  
			union  
			select A.LOCAT,A.CONTNO,A.TOTPRC,A.TOTPRES,A.CUSCOD,convert(varchar(8),A.SDATE,112) as SDATE
				,B.SNAM+B.NAME1+' '+B.NAME2 as CUSNAME,A.TOTDWN,A.NPAYRES,A.NKANG,A.LPAYD,A.BILLCOLL
				,A.TOT_UPAY,A.EXP_AMT,A.SMPAY,A.EXP_FRM,A.EXP_TO,A.PAYDWN,convert(varchar(8),A.FDATE,112) as FDATE
				,C.CRCOST,C.PRICE,A.T_NOPAY,A.OPTCST,A.NCARCST,A.NCSHPRC,A.NPRICE,A.NPROFIT  
			from {$this->MAuth->getdb('HARMAST')} A,{$this->MAuth->getdb('CHGAR_VIEW')} B
			,{$this->MAuth->getdb('INVTRAN')} C   
			where (A.CONTNO = B.CONTNO) and (A.LOCAT = B.LOCAT) and (A.STRNO = C.STRNO) 
			and (A.LOCAT like '".$LOCAT."%') and (A.CONTNO like '".$CONTNO."%') 
			and (C.GCODE like '".$GCODE."%' or C.GCODE is null) and (A.SDATE <= '".$endofmonth."') 
			and A.CLOSDT > '".$endofmonth."' and A.YSTAT = 'Y' and (B.DATE1 > '".$startofmonth."') 
			and (A.TOTPRC > A.SMPAY or (A.TOTPRC = A.SMPAY 
			and (select MAX(PAYDT) from {$this->MAuth->getdb('CHQTRAN')} where CONTNO = A.CONTNO and LOCATPAY = A.LOCAT and FLAG <> 'C' 
			and PAYFOR in ('006','007') and PAYAMT > 0) >= '".$startofmonth."')) and A.TOTPRC > 0 
			and A.BILLCOLL like '".$OFFICER."%' 
			order by A.".$order."
		";
		//echo $sql;
		$query = $this->db->query($sql);
		$arrs = array();
		if($query->row()){
			foreach($query->result() as $row){$i++;
				$sql1 = "
					IF OBJECT_ID('tempdb..#nopay') IS NOT NULL DROP TABLE #nopay
					select *
					into #nopay
					FROM(
						select CONTNO,DDATE,N_DAMT,DAMT,NOPAY,LOCAT,NPROF,DATE1,PAYMENT
						from {$this->MAuth->getdb('ARPAY')} where LOCAT = '".$row->LOCAT."' 
						and CONTNO = '".$row->CONTNO."' 
						union  
						select CONTNO,DDATE,N_DAMT,DAMT,NOPAY,LOCAT,NPROF,DATE1,PAYMENT 
						from {$this->MAuth->getdb('HARPAY')} where LOCAT = '".$row->LOCAT."' 
						and CONTNO = '".$row->CONTNO."'
					)nopay
					IF OBJECT_ID('tempdb..#chq') IS NOT NULL DROP TABLE #chq
					select *
					into #chq
					FROM(
						select * from {$this->MAuth->getdb('CHQTRAN')} 
						where CONTNO = '".$row->CONTNO."' 
						and FLAG <> 'C' and ((PAYFOR = '006') or (PAYFOR = '007'))
					)chq
				";
				//echo $sql1;
				$this->db->query($sql1);
				$sql2 = "
					declare @countnopay varchar(max) = (
						select MAX(NOPAY) from #nopay
					);
					declare @tonopay varchar(max) = (
						select MAX(NOPAY) from #nopay where DDATE <= '".$endofmonth."'
					);
					declare @debtor_true decimal(18,2) = (
						select ".$row->NPRICE." - sum(
							case when PAYDT  <= '".$endofmonth."' and ((PAYFOR = '006') 
							or (PAYFOR = '007') or (PAYFOR = '002')) then PAYAMT_N else 0 end
						)
						from {$this->MAuth->getdb('CHQTRAN')} where LOCATPAY = '".$row->LOCAT."' and 
						CONTNO = '".$row->CONTNO."' and FLAG <> 'C'
					);
					--ส่วนลดดอกผลงวดก่อน
					declare @disctpay_b decimal(18,2) = (
						select SUM(DISCT) from #chq where PAYDT < '".$startofmonth."'
					);
					--ดอกผลตามดิวงวดนี้
					declare @nproftopay decimal(18,2) = (
						select isnull(NPROF,0) from #nopay where DDATE between '".$startofmonth."' and '".$endofmonth."'
					);
					--ดอกผลถึงงวดก่อนตามรับจริง
					declare @payamt_n decimal(18,2) = (
						select 
							SUM(PAYAMT_N)
						from #chq A 
						where  (A.PAYDT < '".$startofmonth."')
					);
					declare @nprof_bf1 decimal(18,2) = (
						select (select sum(NPROF) from #nopay where DATE1 < '".$startofmonth."') + 
						NPROF * (((@payamt_n % N_DAMT) * 100 / DAMT) / 100)
						from #nopay where NOPAY in (select MAX(NOPAY)+1 from #nopay 
						where DATE1 < '".$startofmonth."') 
					);
					declare @nprof_bf2 decimal(18,2) = (
						select SUM(NPROF - BL_NPROF)
						from (
							select
								NPROF
								,NPROF - (NPROF * ((PAYMENT * 100 / N_DAMT) / 100)) as BL_NPROF
							from #nopay A where DATE1 <= '".$startofmonth."'
						)a
					);
					declare @disctnow decimal(18,2) = (
						select ISNULL(sum(case when PAYDT  Between '".$startofmonth."' and '".$endofmonth."' 
						then  DISCT else 0 end),0) as DISCTNOW from #chq  
					);
					--ดอกผลถึงงวดนี้ตามจริง
					declare @nprof_topay1 decimal(18,2) = (
						select (select sum(NPROF) from #nopay where DATE1 < '".$endofmonth."') + 
						NPROF * (((@payamt_n % N_DAMT) * 100 / DAMT) / 100)
						from #nopay where NOPAY in (select MAX(NOPAY)+1 from #nopay 
						where DATE1 < '".$endofmonth."') 
					);
					declare @nprof_topay2 decimal(18,2) = (
						select SUM(NPROF - BL_NPROF)
						from (
							select
								NPROF
								,NPROF - (NPROF * ((PAYMENT * 100 / N_DAMT) / 100)) as BL_NPROF
							from #nopay A where DATE1 <= '".$endofmonth."'
						)a
					);
					
					select @countnopay as countnopay
					,case when @tonopay = @countnopay then 0 else @tonopay end as tonopay 
					,@debtor_true as DEBTOR_TRUE,@disctpay_b as DISCTPAY_B
					,isnull(@nproftopay,0) as NPROFTOPAY
					
					,case when '".$startofmonth."' >= (select MAX(DATE1) from #nopay) 
					then @nprof_bf2 else @nprof_bf1 end as bf_nprof 
					,@disctnow as DISCTNOW
					
					,case when '".$endofmonth."' >= (select MAX(DATE1) from #nopay) 
					then @nprof_topay2 else @nprof_topay1 end as topay_nprof 
					
					order by bf_nprof				
				";
				//echo $sql2; exit;
				$query2 = $this->db->query($sql2);
				if($query2->row()){
					foreach($query2->result() as $row2){
						$arrs['NPROFIT'][]  	= $row->NPROFIT;
						$arrs['DEBTOR_TRUE'][]  = $row2->DEBTOR_TRUE;
						$arrs['DISCTPAY_B'][]   = $row2->DISCTPAY_B;
						$arrs['bf_nprof'][]  	= $row2->bf_nprof;
						$arrs['NPROFTOPAY'][]   = $row2->NPROFTOPAY;
						$arrs['DISCTNOW'][]  	  = $row2->DISCTNOW;
						$arrs['nprof_paymonth'][] = $row2->topay_nprof - $row2->bf_nprof;
						$arrs['topay_nprof'][]    = $row2->topay_nprof;
						$arrs['bl_nprof'][]       = $row->NPROFIT - $row2->topay_nprof;
						
						$html .="
							<tr>
								<td style='width:2%;text-align:left;'>".$i."</td>
								<td style='width:3%;text-align:left;'>".$row->LOCAT."</td>
								<td style='width:7%;text-align:left;'>".$row->CONTNO."</td>
								<td style='width:10%;text-align:left;'>".$row->CUSNAME."</td>
								<td style='width:5%;text-align:left;'>".$row->SDATE."<br>".$row->FDATE."</td>
								<td style='width:5%;text-align:left;'>".$row2->countnopay."<br>".$row2->tonopay."</td>
								<td style='width:5%;text-align:right;'>".$row->NPROFIT."<br>".$row2->DEBTOR_TRUE."</td>
								<td style='width:9%;text-align:right;'>".number_format($row2->DISCTPAY_B,2)."</td>
								<td style='width:9%;text-align:right;'>".number_format($row2->bf_nprof,2)."<br>".number_format($row2->NPROFTOPAY,2)."</td>
								<td style='width:9%;text-align:right;'>".number_format($row2->DISCTNOW,2)."</td>
								<td style='width:9%;text-align:right;'>".number_format($row2->topay_nprof - $row2->bf_nprof,2)."</td>
								<td style='width:9%;text-align:right;'>".number_format($row2->topay_nprof,2)."</td>
								<td style='width:9%;text-align:right;'>".number_format($row->NPROFIT - $row2->topay_nprof,2)."</td>
							</tr>
						";	
					}
				}
			}
		}
		if($i > 0){
			$html .="
				<tr>
					<td class='wf' style='height:1px;border-top:0.1px solid black;' colspan='13'></td>
				</tr>
				<tr>
					<td style='text-align:left;' colspan='2'><b>รวมทั้งสิ้น</b></td>
					<td style='text-align:center;' colspan='1'>".$i."</td>
					<td style='text-align:left;' colspan='2'><b>รายการ</b></td>
					<td style='text-align:right;' colspan='2'>".number_format(array_sum($arrs['NPROFIT']),2)."<br>".number_format(array_sum($arrs['DEBTOR_TRUE']),2)."</td>
					<td style='text-align:right;'>".number_format(array_sum($arrs['DISCTPAY_B']),2)."</td>
					<td style='text-align:right;'>".number_format(array_sum($arrs['bf_nprof']),2)."<br>".number_format(array_sum($arrs['NPROFTOPAY']),2)."</td>
					<td style='text-align:right;'>".number_format(array_sum($arrs['DISCTNOW']),2)."</td>
					<td style='text-align:right;'>".number_format(array_sum($arrs['nprof_paymonth']),2)."</td>
					<td style='text-align:right;'>".number_format(array_sum($arrs['topay_nprof']),2)."</td>
					<td style='text-align:right;'>".number_format(array_sum($arrs['bl_nprof']),2)."</td>
				</tr>
				<tr>
					<td class='wf' style='height:1px;border-top:0.1px solid black;' colspan='13'></td>
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
							<th colspan='13' style='font-size:10pt;'>บริษัท ตั้งใจพัฒนายานยนต์ จำกัด</th>
						</tr>
						<tr>
							<th colspan='13' style='font-size:9pt;'>รายงานการรับรู้รายได้ประจำเดือน &nbsp;".$getmonth."&nbsp;ปี &nbsp;".$getyear." (SYD)</th>
						</tr>
						<tr>
							<td style='text-align:center;' colspan='13'>
								<b>สาขา</b> &nbsp;&nbsp;".$LOCAT."&nbsp;&nbsp;
								<b>กลุ่มเลขที่สัญญา</b> &nbsp;&nbsp;".$CONTNO."&nbsp;&nbsp;
								<b>กลุ่มสินค้า</b>&nbsp;&nbsp;".$GCODE."&nbsp;&nbsp;
							</td>
						</tr>
						<tr>
							<td style='text-align:right;' colspan='13'>Rpsyd 60,61</td>
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