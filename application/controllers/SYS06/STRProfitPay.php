<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/********************************************************
             ______@12/06/2020______
			 Pasakorn Boonded

********************************************************/
class STRProfitPay extends MY_Controller {
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
							<br>รายงานกำไรตามวันครบกำหนดชำระ (STR)<br>
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
							<div class='col-sm-12'>
								<div style='color:#f9e79f;background-color:#808b96;text-align:center;'>หมายเหตุ : ข้อมูลรายงานจะรายงานเฉพาะสัญญาที่ดอกผลเช่าซื้อมากกว่า 0 บาทเท่านั้น</div>
							</div>
						</div>
					</div>	
				</div>
			</div>
		";
		$html .="<script src='".base_url('public/js/SYS06/STRProfitPay.js')."'></script>";
		echo $html;
	}
	function conditiontopdf(){
		$data = array();
		$data[] = urlencode($_REQUEST['LOCAT'].'||'.$_REQUEST['CONTNO'].'||'.$_REQUEST['F_DATE']
		.'||'.$_REQUEST['T_DATE'].'||'.$_REQUEST['OFFICER'].'||'.$_REQUEST['GCODE']
		.'||'.$_REQUEST['order']);
		echo json_encode($this->generateData($data,"encode"));
	}
	function pdftax(){
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
				<td class='wf' style='height:1px;border-top:0.1px solid black;' colspan='17'></td>
			</tr>
			<tr>
				<th style='border-bottom:0.1px solid black;text-align:left;'>No.</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>สาขา</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>เลขที่สัญญา</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>รหัสลูกค้า</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>ชื่อ - สกุล</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>วันดิวงวดแรก</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ราคาขาย<br>ไม่รวม VAT</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ภาษีราคาขาย</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ดอกผลเช่าซื้อ</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>จำนวนงวด</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>งวดที่</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>วันครบกำหนด</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ค่างวดงวดนี้</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ภาษีงวดนี้</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ดอกผลงวดนี้</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ดอกผลคงเหลือ</th>	
				<th style='border-bottom:0.1px solid black;text-align:right;'>ภาษีคงเหลือ<br>ตามดิว</th>	
			</tr>
			<tr>
				<td class='wf' style='height:1px;border-top:0.1px solid black;' colspan='17'></td>
			</tr>
		";
		$sql = "
			select A.LOCAT,A.CONTNO,A.TOTPRC,A.TOTPRES,A.CUSCOD,A.SDATE,B.SNAM+B.NAME1+' '+B.NAME2 as CUSNAME
				,A.LPAYD,A.BILLCOLL,A.TOT_UPAY,A.EXP_AMT,A.SMPAY,A.EXP_FRM,A.EXP_TO,A.PAYDWN
				,convert(varchar(8),A.FDATE,112) as FDATE
 				,A.T_NOPAY,A.OPTCST,A.NCARCST,A.NCSHPRC,A.NPRICE,A.NPROFIT,A.NKANG,A.VATPRC  
			from {$this->MAuth->getdb('ARMAST')} A,{$this->MAuth->getdb('CUSTMAST')} B
			,{$this->MAuth->getdb('ARPAY')} C 
			where (A.CUSCOD = B.CUSCOD) and (A.CONTNO = C.CONTNO) and (A.LOCAT = C.LOCAT) 
			and (A.LOCAT like '".$LOCAT."%') and (A.CONTNO like '".$CONTNO."%') 
			and (A.BILLCOLL like '".$OFFICER."%') 
			and (C.DDATE between '".$F_DATE."' and '".$T_DATE."') 
			and A.TOTPRC > 0 and ((A.TOTPRC > A.SMPAY) or (A.TOTPRC = A.SMPAY and A.LPAYD > '".$T_DATE."')) 
			and A.NPROFIT > 0 and A.STRNO in (
				select STRNO from {$this->MAuth->getdb('INVTRAN')} where GCODE like '".$GCODE."%' 
				union 
				select STRNO from {$this->MAuth->getdb('HINVTRAN')} where GCODE like '".$GCODE."%'
			) 
			union  
			select A.LOCAT,A.CONTNO,A.TOTPRC,A.TOTPRES,A.CUSCOD,A.SDATE,B.SNAM+B.NAME1+' '+B.NAME2 as CUSNAME
				,A.LPAYD,A.BILLCOLL,A.TOT_UPAY,A.EXP_AMT,A.SMPAY,A.EXP_FRM,A.EXP_TO,A.PAYDWN
				,convert(varchar(8),A.FDATE,112) as FDATE
				,A.T_NOPAY, A.OPTCST,A.NCARCST,A.NCSHPRC,A.NPRICE,A.NPROFIT,A.NKANG,A.VATPRC
			from {$this->MAuth->getdb('ARMAST')} A,{$this->MAuth->getdb('CUSTMAST')} B
			,{$this->MAuth->getdb('CHQTRAN')} D 
			where (A.CUSCOD = B.CUSCOD) and (A.CONTNO = D.CONTNO) and (A.LOCAT = D.LOCATPAY) 
			and (D.PAYFOR = '007') and (D.TSALE = 'H') and (D.FLAG <> 'C') and (A.LOCAT like '".$LOCAT."%') 
			and (A.CONTNO like '".$CONTNO."%') and (A.BILLCOLL like '".$OFFICER."%') 
			and (D.PAYDT between '".$F_DATE."' and '".$T_DATE."') and A.TOTPRC > 0 and A.LDATE >= '".$F_DATE."'  
			and A.NPROFIT > 0 and A.STRNO in (
				select STRNO from {$this->MAuth->getdb('INVTRAN')} where GCODE like '".$GCODE."%' 
				union 
				select STRNO from {$this->MAuth->getdb('HINVTRAN')} where GCODE like '".$GCODE."%'
			) 
			union  
			select A.LOCAT,A.CONTNO,A.TOTPRC,A.TOTPRES,A.CUSCOD,A.SDATE,B.SNAM+B.NAME1+' '+B.NAME2 as CUSNAME
				,A.LPAYD,A.BILLCOLL,A.TOT_UPAY,A.EXP_AMT,A.SMPAY,A.EXP_FRM,A.EXP_TO,A.PAYDWN
				,convert(varchar(8),A.FDATE,112) as FDATE
				,A.T_NOPAY, A.OPTCST,A.NCARCST,A.NCSHPRC,A.NPRICE,A.NPROFIT,A.NKANG,A.VATPRC 
			from {$this->MAuth->getdb('HARMAST')} A,{$this->MAuth->getdb('CUSTMAST')} B
			,{$this->MAuth->getdb('CHQTRAN')} D  
			where (A.CUSCOD = B.CUSCOD) and (A.CONTNO = D.CONTNO) and (A.LOCAT = D.LOCATPAY) 
			and (D.PAYFOR = '007') and (D.TSALE = 'H') and (D.FLAG <> 'C') and (A.LOCAT like '".$LOCAT."%') 
			and (A.CONTNO like '".$CONTNO."%') and (A.BILLCOLL like '".$OFFICER."%')
			and (D.PAYDT between '".$F_DATE."' and '".$T_DATE."') and A.TOTPRC > 0 and A.LDATE >= '".$F_DATE."' 
			and A.NPROFIT > 0 and A.STRNO in (
				select STRNO from {$this->MAuth->getdb('INVTRAN')} where GCODE like '".$GCODE."%'
				union 
				select STRNO from {$this->MAuth->getdb('HINVTRAN')} where GCODE like '".$GCODE."%'
			) 
			union  
			select A.LOCAT,A.CONTNO,A.TOTPRC,A.TOTPRES,A.CUSCOD,A.SDATE,B.SNAM+B.NAME1+' '+B.NAME2 as CUSNAME
				,A.LPAYD,A.BILLCOLL,A.TOT_UPAY,A.EXP_AMT,A.SMPAY,A.EXP_FRM,A.EXP_TO,A.PAYDWN
				,convert(varchar(8),A.FDATE,112) as FDATE
				,A.T_NOPAY, A.OPTCST,A.NCARCST,A.NCSHPRC,A.NPRICE,A.NPROFIT,A.NKANG,A.VATPRC  
			from {$this->MAuth->getdb('HARMAST')} A,{$this->MAuth->getdb('CHGAR_VIEW')} B
			,{$this->MAuth->getdb('HARPAY')} C 
			where (A.CONTNO = B.CONTNO ) and (A.LOCAT = B.LOCAT) and (A.CONTNO = C.CONTNO) 
			and (A.LOCAT = C.LOCAT) and (A.LOCAT like '".$LOCAT."%') and (A.CONTNO like '".$CONTNO."%') 
			and (A.BILLCOLL like '".$OFFICER."%') and (B.DATE1 > '".$T_DATE."') 
			and (C.DDATE between '".$F_DATE."' and '".$T_DATE."') and A.TOTPRC > 0 and (A.TOTPRC > A.SMPAY) 
			and A.NPROFIT > 0 and A.STRNO in (
				select STRNO from {$this->MAuth->getdb('INVTRAN')} where GCODE like '".$GCODE."%' 
				union 
				select STRNO from {$this->MAuth->getdb('HINVTRAN')} where GCODE like '".$GCODE."%'
			) order by A.".$order."
		";
		//echo $sql; exit;
		$arrs = array();
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){$i++;
				$sql1 = "
					select MIN(MINNOPAY) as MINNOPAY,MAX(MAXNOPAY) as MAXNOPAY
						,MIN(CONVERT(varchar(8),DDATE,112)) as DDATE,SUM(N_DAMT) as N_DAMT
						,SUM(V_DAMT) as V_DAMT,SUM(STRPROF) as STRPROF,SUM(STRPROF_T) as STRPROF_T
						,SUM(VT_DAMT) as VT_DAMT
					from (
						select  
							MIN(case when DDATE between '".$F_DATE."' and '".$T_DATE."'
							then NOPAY end) as MINNOPAY 
							,MAX(case when DDATE between '".$F_DATE."' and '".$T_DATE."'
							then NOPAY end) as MAXNOPAY 
							,MIN(case when DDATE between '".$F_DATE."' and '".$T_DATE."'
							then DDATE end) as DDATE
							,isnull(
								sum(case when DDATE between '".$F_DATE."' and '".$T_DATE."'
								then N_DAMT else 0 end),0
							) as N_DAMT  --ค่างวดงวดนี้
							,isnull(
								sum(case when DDATE between '".$F_DATE."' and '".$T_DATE."'
								then V_DAMT else 0 end),0	
							) as V_DAMT 
							,isnull(
								sum(case when DDATE between '".$F_DATE."' and '".$T_DATE."'
								then STRPROF else 0 end),0
							) as STRPROF 
							,isnull(
								sum(case when DDATE > '".$T_DATE."'
								then STRPROF else 0 end),0
							) as STRPROF_T 
							,isnull(
								sum(case when DDATE > '".$T_DATE."'
								then V_DAMT else 0 end),0
							) as VT_DAMT 
						from {$this->MAuth->getdb('ARPAY')} where LOCAT = '".$row->LOCAT."' 
						and  CONTNO = '".$row->CONTNO."'
						union
						select  
							MIN(case when DDATE between '".$F_DATE."' and '".$T_DATE."'
							then NOPAY end) as MINNOPAY 
							,MAX(case when DDATE between '".$F_DATE."' and '".$T_DATE."'
							then NOPAY end) as MAXNOPAY
							,MIN(case when DDATE between '".$F_DATE."' and '".$T_DATE."'
							then DDATE end) as DDATE							
							,isnull(
								sum(case when DDATE between '".$F_DATE."' and '".$T_DATE."'
								then N_DAMT else 0 end),0
							) as N_DAMT  --ค่างวดงวดนี้
							,isnull(
								sum(case when DDATE between '".$F_DATE."' and '".$T_DATE."'
								then V_DAMT else 0 end),0	
							) as V_DAMT 
							,isnull(
								sum(case when DDATE between '".$F_DATE."' and '".$T_DATE."'
								then STRPROF else 0 end),0
							) as STRPROF 
							,isnull(
								sum(case when DDATE > '".$T_DATE."'
								then STRPROF else 0 end),0
							) as STRPROF_T 
							,isnull(
								sum(case when DDATE > '".$T_DATE."'
								then V_DAMT else 0 end)
							,0) as VT_DAMT 
						from {$this->MAuth->getdb('HARPAY')} where LOCAT = '".$row->LOCAT."' 
						and  CONTNO = '".$row->CONTNO."'
					)a
				";
				//echo $sql1; exit;
				$query1 = $this->db->query($sql1);
				if($query1->row()){
					foreach($query1->result() as $row1){
						$nopay = "";
						if($row1->MINNOPAY == $row1->MAXNOPAY){
							$nopay = $row1->MINNOPAY;
						}else{
							$nopay = $row1->MINNOPAY."-".$row1->MAXNOPAY;
						}
						$arrs['NPRICE'][]    = $row->NPRICE;
						$arrs['VATPRC'][]    = $row->VATPRC;
						$arrs['NPROFIT'][]   = $row->NPROFIT;
						$arrs['N_DAMT'][]    = $row1->N_DAMT;
						$arrs['V_DAMT'][]    = $row1->V_DAMT;
						$arrs['STRPROF'][]   = $row1->STRPROF;
						$arrs['STRPROF_T'][] = $row1->STRPROF_T;
						$arrs['VT_DAMT'][]   = $row1->VT_DAMT;
						
						$html .="
							<tr>
								<td style='width:5%;text-align:left;'>".$i."</td>
								<td style='width:5%;text-align:left;'>".$row->LOCAT."</td>
								<td style='width:5%;text-align:left;'>".$row->CONTNO."</td>
								<td style='width:5%;text-align:left;'>".$row->CUSCOD."</td>
								<td style='width:5%;text-align:left;'>".$row->CUSNAME."</td>
								<td style='width:5%;text-align:left;'>".$row->FDATE."</td>
								<td style='width:5%;text-align:right;'>".number_format($row->NPRICE,2)."</td>
								<td style='width:5%;text-align:right;'>".number_format($row->VATPRC,2)."</td>
								<td style='width:5%;text-align:right;'>".number_format($row->NPROFIT,2)."</td>
								<td style='width:5%;text-align:right;'>".$row->T_NOPAY."</td>
								<td style='width:5%;text-align:right;'>".$nopay."</td>
								<td style='width:5%;text-align:right;'>".$this->Convertdate(2,$row1->DDATE)."</td>
								<td style='width:5%;text-align:right;'>".number_format($row1->N_DAMT,2)."</td>
								<td style='width:5%;text-align:right;'>".number_format($row1->V_DAMT,2)."</td>
								<td style='width:5%;text-align:right;'>".number_format($row1->STRPROF,2)."</td>
								<td style='width:5%;text-align:right;'>".number_format($row1->STRPROF_T,2)."</td>
								<td style='width:5%;text-align:right;'>".number_format($row1->VT_DAMT,2)."</td>
							</tr>
						";	
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
					<td style='width:3.25%;text-align:left;' colspan='2'><b>รวมทั้งสิ้น</b></td>
					<td style='width:3.25%;text-align:center;' colspan='1'>".$i."</td>
					<td style='width:3.25%;text-align:left;' colspan='2'><b>รายการ</b></td>
					<td style='width:3.25%;text-align:right;' colspan='2'>".number_format(array_sum($arrs['NPRICE']),2)."</td>
					<td style='width:3.25%;text-align:right;'>".number_format(array_sum($arrs['VATPRC']),2)."</td>
					<td style='width:3.25%;text-align:right;'>".number_format(array_sum($arrs['NPROFIT']),2)."</td>
					<td style='width:3.25%;text-align:right;' colspan='4'>".number_format(array_sum($arrs['N_DAMT']),2)."</td>
					<td style='width:3.25%;text-align:right;'>".number_format(array_sum($arrs['V_DAMT']),2)."</td>
					<td style='width:3.25%;text-align:right;'>".number_format(array_sum($arrs['STRPROF']),2)."</td>
					<td style='width:3.25%;text-align:right;'>".number_format(array_sum($arrs['STRPROF_T']),2)."</td>
					<td style='width:3.25%;text-align:right;'>".number_format(array_sum($arrs['VT_DAMT']),2)."</td>
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
							<th colspan='17' style='font-size:9pt;'>รายงานกำไรตามดิว(STR)</th>
						</tr>
						<tr>
							<td style='text-align:center;' colspan='17'>
								<b>รหัสสาขา</b> &nbsp;&nbsp;".$LOCAT."&nbsp;&nbsp;
								<b>เลขที่สัญญา</b>&nbsp;&nbsp;".$CONTNO."&nbsp;&nbsp;
								<b>จากวันที่</b>&nbsp;&nbsp;".$this->Convertdate(2,$F_DATE)."&nbsp;&nbsp;
								<b>ถึงวันที่</b>&nbsp;&nbsp;".$this->Convertdate(2,$T_DATE)."&nbsp;&nbsp;
								<b>พนักงานเก็บเงิน</b>&nbsp;&nbsp;".$OFFICER."&nbsp;&nbsp;
							</td>
						</tr>
						<tr>
							<td style='text-align:right;' colspan='17'>Rpstr 10,11</td>
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