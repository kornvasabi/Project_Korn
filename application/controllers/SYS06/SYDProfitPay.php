<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/********************************************************
             ______@16/05/2020______
			 Pasakorn Boonded

********************************************************/
class SYDProfitPay extends MY_Controller {
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
							<br>รายงานกำไรตามวันครบกำหนดชำระ (SYD)<br>
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
									<select id='CONTNO' class='form-control input-sm'></select>
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
						</div>
						<div class='col-sm-12'>
							<div class='col-sm-5'>
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
							<div class='col-sm-5'>
								<b>รูปแบบรายงาน</b>
								<div class='col-sm-12' style='height:100%;width:100%;border:1px dotted #aaa;'>
									<div class='col-sm-6'>
										<div class='form-group'>
											<br>
											<label>
												<input type= 'radio' id='BR1' name='baabreport' checked> แสดงรายการ
											</label>
										</div>
									</div>
									<div class='col-sm-6'>
										<div class='form-group'>
											<br>
											<label>
												<input type= 'radio' id='BR2' name='baabreport'> สรุป
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
		$html .="<script src='".base_url('public/js/SYS06/SYDProfitPay.js')."'></script>";
		echo $html;
	}
	function getLocat(){
		$CONTNO = $_REQUEST['CONTNO'];
		$response = array();
		$sql = "
			select top 100 A.CONTNO,A.LOCAT,C.SNAM+C.NAME1+' '+C.NAME2+' ('+A.CONTNO+')' as CUSNAME 
			from {$this->MAuth->getdb('ARMAST')} A
			left join {$this->MAuth->getdb('CUSTMAST')} C on A.CUSCOD = C.CUSCOD
			left join {$this->MAuth->getdb('INVTRAN')} I on A.STRNO = I.STRNO 
			where A.CONTNO = '".$CONTNO."' order by A.CONTNO
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$response['LOCAT'] = $row->LOCAT;
			}
		}else{
			$response['LOCAT'] = "";
		}
		echo json_encode($response);
	}
	function conditiontopdf(){
		$data = array();
		$data[] = urlencode($_REQUEST['LOCAT'].'||'.$_REQUEST['CONTNO'].'||'.$_REQUEST['F_DATE'].'||'.$_REQUEST['T_DATE']
		.'||'.$_REQUEST['OFFICER'].'||'.$_REQUEST['order']);
		echo json_encode($this->generateData($data,"encode"));
	}
	function pdflist(){
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
		$order     = $tx[5];
		
		$sql = "
			IF OBJECT_ID('tempdb..#SYD') IS NOT NULL DROP TABLE #SYD
			select *
			into #SYD
			FROM(
				select A.LOCAT,A.CONTNO,A.TOTPRC,A.TOTPRES,A.CUSCOD,A.SDATE,A.LPAYD,A.BILLCOLL,A.TOT_UPAY
					,A.EXP_AMT,A.SMPAY,A.EXP_FRM,A.EXP_TO,B.SNAM+B.NAME1+' '+B.NAME2 as CUSNAME
					,A.PAYDWN,convert(varchar(8),A.FDATE,112) as FDATE,A.T_NOPAY, A.OPTCST,A.NCARCST
					,A.NCSHPRC,A.NPRICE,A.NPROFIT 
				from {$this->MAuth->getdb('ARMAST')} A,{$this->MAuth->getdb('CUSTMAST')} B
				,{$this->MAuth->getdb('ARPAY')} C 
				where (A.CUSCOD = B.CUSCOD) and (A.CONTNO = C.CONTNO) and (A.LOCAT = C.LOCAT) 
				and (A.LOCAT like '".$LOCAT."%') and (A.CONTNO like '".$CONTNO."%') 
				and (A.BILLCOLL like '".$OFFICER."%') 
				and (C.DDATE between '".$F_DATE."' and '".$T_DATE."') and A.TOTPRC > 0  
				and ((A.TOTPRC > A.SMPAY) OR (A.TOTPRC = A.SMPAY AND A.LPAYD > '')) 
				and A.NPROFIT > 0   
				union  
				select A.LOCAT,A.CONTNO,A.TOTPRC,A.TOTPRES,A.CUSCOD,A.SDATE,A.LPAYD,A.BILLCOLL,A.TOT_UPAY
					,A.EXP_AMT,A.SMPAY,A.EXP_FRM,A.EXP_TO,B.SNAM+B.NAME1+' '+B.NAME2 as CUSNAME
					,A.PAYDWN ,convert(varchar(8),A.FDATE,112) as FDATE,A.T_NOPAY, A.OPTCST,A.NCARCST
					,A.NCSHPRC,A.NPRICE,A.NPROFIT  
				from {$this->MAuth->getdb('ARMAST')} A,{$this->MAuth->getdb('CUSTMAST')} B
				,{$this->MAuth->getdb('CHQTRAN')} D 
				where (A.CUSCOD = B.CUSCOD) and (A.CONTNO = D.CONTNO) and (A.LOCAT = D.LOCATPAY) 
				and (D.PAYFOR = '007') and (D.TSALE = 'H') and (D.FLAG <> 'C') and (A.LOCAT like '".$LOCAT."%') 
				and (A.CONTNO like '".$CONTNO."%') and (A.BILLCOLL like '".$OFFICER."%') 
				and (D.PAYDT between '".$F_DATE."' and '".$T_DATE."') 
				and A.TOTPRC > 0 and A.LDATE >= '".$F_DATE."' and A.NPROFIT > 0 
				union  
				select A.LOCAT,A.CONTNO,A.TOTPRC,A.TOTPRES,A.CUSCOD,A.SDATE,A.LPAYD,A.BILLCOLL,A.TOT_UPAY
					,A.EXP_AMT,A.SMPAY,A.EXP_FRM,A.EXP_TO,B.SNAM+B.NAME1+' '+B.NAME2 as CUSNAME
					,A.PAYDWN,convert(varchar(8),A.FDATE,112) as FDATE,A.T_NOPAY, A.OPTCST,A.NCARCST
					,A.NCSHPRC,A.NPRICE,A.NPROFIT  
				from {$this->MAuth->getdb('HARMAST')} A,{$this->MAuth->getdb('CUSTMAST')} B
				,{$this->MAuth->getdb('CHQTRAN')} D 
				where (A.CUSCOD = B.CUSCOD) and (A.CONTNO = D.CONTNO) and (A.LOCAT = D.LOCATPAY) 
				and (D.PAYFOR = '007') and (D.TSALE = 'H') and (D.FLAG <> 'C') and (A.LOCAT like '".$LOCAT."%') 
				and (A.CONTNO like '".$CONTNO."%') and (A.BILLCOLL like '".$OFFICER."%') 
				and (D.PAYDT between '".$F_DATE."' and '".$T_DATE."') 
				and A.TOTPRC > 0 and A.LDATE >= '".$F_DATE."' and A.NPROFIT > 0 
				union  
				select A.LOCAT,A.CONTNO,A.TOTPRC,A.TOTPRES,A.CUSCOD,A.SDATE,A.LPAYD,A.BILLCOLL,A.TOT_UPAY
					,A.EXP_AMT,A.SMPAY,A.EXP_FRM,A.EXP_TO,B.SNAM+B.NAME1+' '+B.NAME2 as CUSNAME
					,A.PAYDWN ,convert(varchar(8),A.FDATE,112) as FDATE,A.T_NOPAY, A.OPTCST,A.NCARCST
					,A.NCSHPRC,A.NPRICE,A.NPROFIT  
				from {$this->MAuth->getdb('HARMAST')} A,{$this->MAuth->getdb('CHGAR_VIEW')} B
				,{$this->MAuth->getdb('HARPAY')} C 
				where (A.CONTNO = B.CONTNO) and (A.LOCAT = B.LOCAT) and (A.CONTNO = C.CONTNO) 
				and (A.LOCAT = C.LOCAT) and (A.LOCAT like '".$LOCAT."%') and (A.CONTNO like '".$CONTNO."%') 
				and (A.BILLCOLL like '".$OFFICER."%') and (B.DATE1  > '".$T_DATE."') 
				and (C.DDATE between '".$F_DATE."' and '".$T_DATE."') and A.TOTPRC > 0 
				and (A.TOTPRC > A.SMPAY) and A.NPROFIT > 0   
				--order by A.".$order."
			)SYD
		";
		//echo $sql;
		$this->db->query($sql);
		$sql = "
			select * from #SYD order by ".$order."
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		$head = ""; $html = ""; $i = 0;
		
		$head = "
			<tr class='wm'>
				<td class='wf' style='height:1px;border-top:0.1px solid black;' colspan='16'></td>
			</tr>
			<tr>
				<th style='border-bottom:0.1px solid black;text-align:left;'>No.</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>สาขา</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>เลขที่สัญญา</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>รหัสลูกค้า</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>ชื่อ - สกุล</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>วันดิวงวดแรก</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ดอกผล<br>ทั้งหมด</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>จน. งวด<br>ทั้งหมด</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>งวดนี้<br>งวดที่</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ดอกผลสะสม<br>ถึงงวดก่อน</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>วันครบกำหนด</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ค่างวดงวดนี้</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ภาษีงวดนี้</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ดอกผล<br>งวดนี้</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ดอกผล<br>คงเหลือ</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ภาษีคงเหลือ<br>ตามดิว</th>	
			</tr>
			<tr>
				<td class='wf' style='height:1px;border-top:0.1px solid black;' colspan='16'></td>
			</tr>
		";
		$arrs = array();
		if($query->row()){
			foreach($query->result() as $row){$i++;
				$sql2 = "
					/*
					declare @nprofit int = (
						select NPROFIT from #SYD where CONTNO = '".$row->CONTNO."'
					);
					*/
					--งวดนี้ งวดที่
					declare @maxnopay varchar(max) = (
						select MAX(NOPAY) from (
							select NOPAY 
							from {$this->MAuth->getdb('ARPAY')} where LOCAT = '".$row->LOCAT."' 
							and CONTNO = '".$row->CONTNO."' 
							and DDATE between '".$F_DATE."' and '".$T_DATE."'
							union
							select NOPAY 
							from {$this->MAuth->getdb('HARPAY')} where LOCAT = '".$row->LOCAT."' 
							and CONTNO = '".$row->CONTNO."' 
							and DDATE between '".$F_DATE."' and '".$T_DATE."'	
						)a
					);
					declare @minnopay varchar(max) = (
						select MIN(NOPAY)from(
							select NOPAY
							from {$this->MAuth->getdb('ARPAY')} where LOCAT = '".$row->LOCAT."'
							and CONTNO = '".$row->CONTNO."' 
							and DDATE between '".$F_DATE."' and '".$T_DATE."'
							union
							select NOPAY
							from {$this->MAuth->getdb('HARPAY')} where LOCAT = '".$row->LOCAT."'
							and CONTNO = '".$row->CONTNO."' 
							and DDATE between '".$F_DATE."' and '".$T_DATE."'	
						)a
					);
					--ดอกผลสะสมถึงงวดก่อน
					declare @NPROF_BEFORE decimal(20,2) = (						
						select SUM(NPROF) from(
							select NPROF
							from {$this->MAuth->getdb('ARPAY')} where LOCAT = '".$row->LOCAT."' 
							and CONTNO = '".$row->CONTNO."' 
							and DDATE < '".$F_DATE."'
							union
							select NPROF
							from {$this->MAuth->getdb('HARPAY')} where LOCAT = '".$row->LOCAT."' 
							and CONTNO = '".$row->CONTNO."' 
							and DDATE < '".$F_DATE."'	
						)a
					);
					--วันครบกำหนด
					declare @DDATE varchar(8) =(
						select min(CONVERT(varchar(8),DDATE,112)) as DDATE from(
							select DDATE
							from HIC2SHORTL.dbo.ARPAY where LOCAT = '".$row->LOCAT."' 
							and CONTNO = '".$row->CONTNO."'
							and DDATE between '".$F_DATE."' and '".$T_DATE."'
							union
							select DDATE
							from HIC2SHORTL.dbo.HARPAY where LOCAT = 'Fกป'
							and CONTNO = '".$row->CONTNO."'
							and DDATE between '".$F_DATE."' and '".$T_DATE."'
						)a
					);
					--ค่างวดงวดนี้
					declare @N_DAMT decimal(20,2) = (
						select SUM(N_DAMT) from(
							select ISNULL(SUM(N_DAMT),0) as N_DAMT 
							from {$this->MAuth->getdb('ARPAY')} where LOCAT = '".$row->LOCAT."' 
							and CONTNO = '".$row->CONTNO."' 
							and DDATE between '".$F_DATE."' and '".$T_DATE."'
							union
							select ISNULL(SUM(N_DAMT),0) as N_DAMT 
							from {$this->MAuth->getdb('HARPAY')} where LOCAT = '".$row->LOCAT."' 
							and CONTNO = '".$row->CONTNO."' 
							and DDATE between '".$F_DATE."' and '".$T_DATE."'	
						)a
					);
					--ภาษีงวดนี้
					declare @V_DAMT_NOPAY decimal(20,2) = (
						select SUM(V_DAMT) from(
							select V_DAMT
							from {$this->MAuth->getdb('ARPAY')} where LOCAT = '".$row->LOCAT."' 
							and CONTNO = '".$row->CONTNO."' 
							and DDATE between '".$F_DATE."' and '".$T_DATE."'
							union
							select V_DAMT
							from {$this->MAuth->getdb('HARPAY')} where LOCAT = '".$row->LOCAT."' 
							and CONTNO = '".$row->CONTNO."' 
							and DDATE between '".$F_DATE."' and '".$T_DATE."'
						)a
					);
					--ดอกผลงวดนี้
					declare @NPROF_NOPAY decimal(20,2) = (
						select SUM(NPROF) from(
							select NPROF
							from {$this->MAuth->getdb('ARPAY')} where LOCAT = '".$row->LOCAT."' 
							and CONTNO = '".$row->CONTNO."' 
							and DDATE between '".$F_DATE."' and '".$T_DATE."'
							union
							select NPROF
							from {$this->MAuth->getdb('HARPAY')} where LOCAT = '".$row->LOCAT."' 
							and CONTNO = '".$row->CONTNO."' 
							and DDATE between '".$F_DATE."' and '".$T_DATE."'
						)a
					);
					--ดอกผลคงเหลือ
					declare @NPROF_REMAIN decimal(20,2) = (
						select ".$row->NPROFIT." - SUM(NPROF) from(
							select NPROF 
							from {$this->MAuth->getdb('ARPAY')} where LOCAT = '".$row->LOCAT."' 
							and CONTNO = '".$row->CONTNO."' 
							and DDATE <= '".$T_DATE."'
							union
							select NPROF 
							from {$this->MAuth->getdb('HARPAY')} where LOCAT = '".$row->LOCAT."' 
							and CONTNO = '".$row->CONTNO."' 
							and DDATE <= '".$T_DATE."'	
						)a
					);
					--ภาษีคงเหลือตามดิว
					declare @V_DAMT decimal(20,2) = (
						select SUM(V_DAMT) from(
							select V_DAMT
							from {$this->MAuth->getdb('ARPAY')} where LOCAT = '".$row->LOCAT."' 
							and CONTNO = '".$row->CONTNO."' 
							and DDATE > '".$T_DATE."'
							union all
							select V_DAMT
							from {$this->MAuth->getdb('HARPAY')} where LOCAT = '".$row->LOCAT."' 
							and CONTNO = '".$row->CONTNO."' 
							and DDATE > '".$T_DATE."'	
						)a
					);
					select @minnopay as MINNOPAY,@maxnopay as MAXNOPAY,@NPROF_BEFORE as NPROF_BEFORE
					,@DDATE as DDATE,@N_DAMT as N_DAMT,@V_DAMT_NOPAY as V_DAMT_NOPAY,@NPROF_NOPAY as NPROF_NOPAY
					,@NPROF_REMAIN as NPROF_REMAIN,@V_DAMT as V_DAMT
				";
				//echo $sql2; exit;
				$query2 = $this->db->query($sql2);
				if($query2->row()){
					foreach($query2->result() as $row2){
						//$nopay = ""; $nprof = "";
						if($row2->MINNOPAY == $row2->MAXNOPAY){
							$nopay = $row2->MINNOPAY;
						}else{
							$nopay = $row2->MINNOPAY."-".$row2->MAXNOPAY;
						}
						$arrs['NPROFIT'][]     = $row->NPROFIT;
						$arrs['NPROF_BEFORE'][]= $row2->NPROF_BEFORE;
						$arrs['N_DAMT'][]      = $row2->N_DAMT;  
						$arrs['V_DAMT_NOPAY'][]= $row2->V_DAMT_NOPAY; 
						$arrs['NPROF_NOPAY'][] = $row2->NPROF_NOPAY; 
						$arrs['NPROF_REMAIN'][]= $row2->NPROF_REMAIN; 
						$arrs['V_DAMT'][]      = $row2->V_DAMT; 	
						
						$html .= "
							<tr>
								<td style='width:3%;text-align:left;'>".$i."</td>
								<td style='width:4%;text-align:left;'>".$row->LOCAT."</td>
								<td style='width:8%;text-align:left;'>".$row->CONTNO."</td>
								<td style='width:8%;text-align:left;'>".$row->CUSCOD."</td>
								<td style='width:12%;text-align:left;'>".$row->CUSNAME."</td>
								<td style='width:5%;text-align:left;'>".$this->Convertdate(2,$row->FDATE)."</td>
								<td style='width:5%;text-align:right;'>".number_format($row->NPROFIT,2)."</td>
								<td style='width:9%;text-align:right;'>".$row->T_NOPAY."</td>
								<td style='width:4%;text-align:right;'>".$nopay."</td>
								<td style='width:6%;text-align:right;'>".number_format($row2->NPROF_BEFORE,2)."</td>
								<td style='width:6%;text-align:right;'>".$this->Convertdate(2,$row2->DDATE)."</td>
								<td style='width:6%;text-align:right;'>".number_format($row2->N_DAMT,2)."</td>
								<td style='width:6%;text-align:right;'>".number_format($row2->V_DAMT_NOPAY,2)."</td>
								<td style='width:6%;text-align:right;'>".number_format($row2->NPROF_NOPAY,2)."</td>
								<td style='width:6%;text-align:right;'>".number_format($row2->NPROF_REMAIN,2)."</td>
								<td style='width:6%;text-align:right;'>".number_format($row2->V_DAMT,2)."</td>
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
					<td style='width:3.25%;text-align:left;' colspan='2'><b>รวมทั้งสิ้น</b></td>
					<td style='width:3.25%;text-align:center;' colspan='1'>".$i."</td>
					<td style='width:3.25%;text-align:left;' colspan='2'><b>รายการ</b></td>
					<td style='width:3.25%;text-align:right;' colspan='2'>".number_format(array_sum($arrs['NPROFIT']),2)."</td>
					<td style='width:3.25%;text-align:right;' colspan='3'>".number_format(array_sum($arrs['NPROF_BEFORE']),2)."</td>
					<td style='width:3.25%;text-align:right;' colspan='2'>".number_format(array_sum($arrs['N_DAMT']),2)."</td>
					<td style='width:3.25%;text-align:right;'>".number_format(array_sum($arrs['V_DAMT_NOPAY']),2)."</td>
					<td style='width:3.25%;text-align:right;'>".number_format(array_sum($arrs['NPROF_NOPAY']),2)."</td>
					<td style='width:3.25%;text-align:right;'>".number_format(array_sum($arrs['NPROF_REMAIN']),2)."</td>
					<td style='width:3.25%;text-align:right;'>".number_format(array_sum($arrs['V_DAMT']),2)."</td>
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
							<th colspan='16' style='font-size:9pt;'>รายงานกำไรตามดิว (SYD)</th>
						</tr>
						<tr>
							<td style='text-align:center;' colspan='16'>
								<b>รหัสสาขา</b> &nbsp;&nbsp;".$LOCAT."&nbsp;&nbsp;
								<b>เลขที่สัญญา</b>&nbsp;&nbsp;".$CONTNO."&nbsp;&nbsp;
								<b>จากวันที่</b>&nbsp;&nbsp;".$this->Convertdate(2,$F_DATE)."&nbsp;&nbsp;
								<b>ถึงวันที่</b>&nbsp;&nbsp;".$this->Convertdate(2,$T_DATE)."&nbsp;&nbsp;
								<b>พนักงานเก็บเงิน</b>&nbsp;&nbsp;".$OFFICER."&nbsp;&nbsp;
							</td>
						</tr>
						<tr>
							<td style='text-align:right;' colspan='16'>Rpsyd 10,11</td>
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
	function pdfcal(){
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
		$order     = $tx[5];
		
		$head = ""; $html = ""; $i = 0;
		$head = "
			<tr class='wm'>
				<td class='wf' style='height:1px;border-top:0.1px solid black;' colspan='12'></td>
			</tr>
			<tr>
				<th style='border-bottom:0.1px solid black;text-align:right;'>จำนวนสัญญา</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>มูลค่าราคาขาย</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ภาษีราคาขาย</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ราคาขายรวม</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ดอกผลทั้งหมด</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ดอกผลสะสม<br>ถึงงวดก่อน</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>มูลค่าค่างวดงวดนี้</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ภาษีงวดนี้</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ค่างวดงวดนี้รวมภาษี</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ดอกผลงวดนี้</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ดอกผลคงเหลือ</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ภาษีคงเหลือตามดิว</th>
			</tr>
			<tr>
				<td class='wf' style='height:1px;border-top:0.1px solid black;' colspan='12'></td>
			</tr>
		";
		
		$sql = "
			IF OBJECT_ID('tempdb..#PFT') IS NOT NULL DROP TABLE #PFT
			select CONTNO,TOTPRC,NPRICE,LOCAT,VATPRC,NPROFIT
				,SMPAY,LPAYD
			into #PFT
			FROM(
				select A.CONTNO,A.TOTPRC,A.NPRICE,A.LOCAT,A.VATPRC,A.NPROFIT
					,A.SMPAY,A.LPAYD  
				from {$this->MAuth->getdb('ARMAST')} A,{$this->MAuth->getdb('ARPAY')} B 
				where A.CONTNO = B.CONTNO and A.LOCAT = B.LOCAT and (A.LOCAT like '".$LOCAT."%') 
				and (A.CONTNO like '".$CONTNO."%') and (A.BILLCOLL like '".$OFFICER."%') 
				and B.DDATE between '".$F_DATE."' and '".$T_DATE."' 
				and A.TOTPRC > 0 and (A.TOTPRC > A.SMPAY 
				or (A.TOTPRC = A.SMPAY and A.LPAYD > '".$F_DATE."')) and A.NPROFIT > 0 
				group by A.CONTNO,A.LOCAT,A.TOTPRC,A.NPRICE
				,A.NPROFIT,A.VATPRC,A.SMPAY,A.LPAYD 
				union   
				select A.CONTNO,A.TOTPRC,A.NPRICE,A.LOCAT,A.VATPRC,A.NPROFIT
					,A.SMPAY,A.LPAYD  
				from {$this->MAuth->getdb('ARMAST')} A,{$this->MAuth->getdb('CHQTRAN')} B 
				where A.CONTNO = B.CONTNO and A.LOCAT = B.LOCATPAY and B.TSALE = 'H'  
				and (A.LOCAT like '".$LOCAT."%') and (A.CONTNO like '".$CONTNO."%') 
				and (A.BILLCOLL like '".$OFFICER."%') and B.PAYDT between '".$F_DATE."' and '".$T_DATE."'  
				and A.TOTPRC > 0 and (B.PAYFOR = '007') and (B.FLAG <> 'C') and A.NPROFIT > 0 
				group by A.CONTNO,A.LOCAT,A.TOTPRC,A.NPRICE,A.NPROFIT,A.VATPRC,A.SMPAY,A.LPAYD  
				union   
				select A.CONTNO,A.TOTPRC,A.NPRICE,A.LOCAT,A.VATPRC,A.NPROFIT
					,A.SMPAY,A.LPAYD  
				from {$this->MAuth->getdb('HARMAST')} A,{$this->MAuth->getdb('CHQTRAN')} B 
				where A.CONTNO = B.CONTNO and A.LOCAT = B.LOCATPAY 
				and B.TSALE = 'H' and (A.LOCAT like '".$LOCAT."%') and (A.CONTNO like '".$CONTNO."%') 
				and (A.BILLCOLL like '".$OFFICER."%') and  
				B.PAYDT between '".$F_DATE."' and '".$T_DATE."' and A.TOTPRC > 0 and (B.PAYFOR = '007') 
				and (B.FLAG <> 'C') and A.NPROFIT > 0 group by A.CONTNO,A.LOCAT,A.TOTPRC
				,A.NPRICE,A.NPROFIT,A.VATPRC,A.SMPAY,A.LPAYD  
				union   
				select A.CONTNO,A.TOTPRC,A.NPRICE,A.LOCAT,A.VATPRC,A.NPROFIT,A.SMPAY,A.LPAYD  
				from {$this->MAuth->getdb('HARMAST')} A,{$this->MAuth->getdb('HARPAY')} B,{$this->MAuth->getdb('CHGAR_VIEW')} C 
				where A.CONTNO = B.CONTNO and A.LOCAT = B.LOCAT and (A.CONTNO = C.CONTNO) 
				and (A.LOCAT = C.LOCAT) and (A.LOCAT like '".$LOCAT."%') and (A.CONTNO like '".$CONTNO."%') 
				and (A.BILLCOLL like '".$OFFICER."%') and  B.DDATE between '".$F_DATE."' and '".$T_DATE."' 
				and A.TOTPRC > 0 and (C.DATE1 > '".$T_DATE."') and (A.TOTPRC > A.SMPAY or (A.TOTPRC = A.SMPAY 
				and A.LPAYD > '".$F_DATE."')) and A.NPROFIT > 0 group by A.CONTNO,A.LOCAT,A.TOTPRC
				,A.NPRICE,A.NPROFIT,A.VATPRC,A.SMPAY
				,A.LPAYD 	
			)PFT
		";
		//echo $sql; exit;
		$this->db->query($sql);
		$sql = "
			select CONTNO,LOCAT from #PFT
		";
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){$i++;
				$sql2 = "
					select top 1 coalesce(DAMT,0) as DAMT,coalesce(V_DAMT,0) as V_DAMT 
						,coalesce(N_DAMT,0) as N_DAMT,coalesce(NPROF,0) as NPROF,coalesce(A1,0) as A1
						,coalesce(A2,0) as A2,coalesce(A3,0) as A3,coalesce(A0,0) as A0  
					from (
						select 
							SUM(case when A.DDATE BETWEEN '".$F_DATE."' and '".$T_DATE."' then A.DAMT else 0 end) as DAMT
							,SUM(case when A.DDATE BETWEEN '".$F_DATE."' and '".$T_DATE."' then A.V_DAMT else 0 end) as V_DAMT
							,SUM(case when A.DDATE BETWEEN '".$F_DATE."' and '".$T_DATE."' then A.N_DAMT else 0 end) as N_DAMT
							,SUM(case when A.DDATE < '".$F_DATE."' then A.NPROF else 0 end) as NPROF
							,SUM(case when A.DDATE BETWEEN '".$F_DATE."' and '".$T_DATE."' then A.NPROF else 0 end) as A1
							,SUM(case when A.DDATE > '".$T_DATE."' then A.NPROF else 0 end) as A2
							,SUM(case when A.DDATE > '".$T_DATE."' then A.V_DAMT else 0 end) as A3 
							,SUM(A.NPROF) as A0  
						from {$this->MAuth->getdb('ARPAY')} A where A.CONTNO = '".$row->CONTNO."' and A.LOCAT =  '".$row->LOCAT."' 
						union  
						select 
							SUM(case when A.DDATE BETWEEN '".$F_DATE."' and '".$T_DATE."' then A.DAMT else 0 end) as DAMT
							,SUM(case when A.DDATE BETWEEN '".$F_DATE."' and '".$T_DATE."' then A.V_DAMT else 0 end) as V_DAMT
							,SUM(case when A.DDATE BETWEEN '".$F_DATE."' and '".$T_DATE."' then A.N_DAMT else 0 end) as N_DAMT
							,SUM(case when A.DDATE < '".$F_DATE."' then A.NPROF else 0 end) as NPROF
							,SUM(case when A.DDATE BETWEEN '".$F_DATE."' and '".$T_DATE."' then A.NPROF else 0 end) as A1
							,SUM(case when A.DDATE > '".$T_DATE."' then A.NPROF else 0 end) as A2
							,SUM(case when A.DDATE > '".$T_DATE."' then A.V_DAMT else 0 end) as A3 
							,SUM(A.NPROF) as A0  
						from {$this->MAuth->getdb('HARPAY')} A where A.CONTNO = '".$row->CONTNO."'  and A.LOCAT =  '".$row->LOCAT."' 
					)a order by DAMT
				";
				//echo $sql2; exit;
				$query2 = $this->db->query($sql2);
				if($query2->row()){
					foreach($query2->result() as $row2){
						if($row2->DAMT !== null){
							$arrs['NPROF'][]   = $row2->NPROF;
							$arrs['DAMT'][]    = $row2->DAMT;	
							$arrs['V_DAMT'][]  = $row2->V_DAMT;
							$arrs['N_DAMT'][]  = $row2->N_DAMT;
							$arrs['A1'][]      = $row2->A1;
							$arrs['A2'][]      = $row2->A2;
							$arrs['A3'][]      = $row2->A3;
						}
						
					}
				}
			}	
		}
		//echo array_sum($arrs['DAMT']); exit;
		//print_r($arrs['DAMT']); exit;
		$sql = "
			select COUNT(CONTNO) as countCONTNO,(SUM(TOTPRC) - SUM(VATPRC)) as C_TOTPRC
			,sum(VATPRC) as VATPRC,SUM(TOTPRC) as TOTPRC,SUM(NPROFIT) as NPROFIT from #PFT
		";
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				if($i > 0){
					$html .="
						<tr>
							<td style='width:6%;text-align:center;'>".$row->countCONTNO."</td>
							<td style='width:8%;text-align:right;'>".number_format($row->C_TOTPRC,2)."</td>
							<td style='width:8%;text-align:right;'>".number_format($row->VATPRC,2)."</td>
							<td style='width:8%;text-align:right;'>".number_format($row->TOTPRC,2)."</td>
							<td style='width:8%;text-align:right;'>".number_format($row->NPROFIT,2)."</td>
							<td style='width:9%;text-align:right;'>".number_format(array_sum($arrs['NPROF']),2)."</td>
							<td style='width:10%;text-align:right;'>".number_format(array_sum($arrs['DAMT']),2)."</td>
							<td style='width:6%;text-align:right;'>".number_format(array_sum($arrs['V_DAMT']),2)."</td>
							<td style='width:9%;text-align:right;'>".number_format(array_sum($arrs['N_DAMT']),2)."</td>
							<td style='width:8%;text-align:right;'>".number_format(array_sum($arrs['A1']),2)."</td>
							<td style='width:8%;text-align:right;'>".number_format(array_sum($arrs['A2']),2)."</td>
							<td style='width:9%;text-align:right;'>".number_format(array_sum($arrs['A3']),2)."</td>
						</tr>
					";
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
				<table class='wf' style='font-size:8pt;height:700px;border-collapse:collapse;line-height:23px;overflow:wrap;vertical-align:text-top;'>
					<tbody>
						<tr>
							<th colspan='12' style='font-size:10pt;'>บริษัท ตั้งใจพัฒนายานยนต์ จำกัด</th>
						</tr>
						<tr>
							<th colspan='12' style='font-size:9pt;'>รายงานกำไรตามดิว (SYD)</th>
						</tr>
						<tr>
							<td style='text-align:center;' colspan='12'>
								<b>รหัสสาขา</b> &nbsp;&nbsp;".$LOCAT."&nbsp;&nbsp;
								<b>เลขที่สัญญา</b>&nbsp;&nbsp;".$CONTNO."&nbsp;&nbsp;
								<b>จากวันที่</b>&nbsp;&nbsp;".$this->Convertdate(2,$F_DATE)."&nbsp;&nbsp;
								<b>ถึงวันที่</b>&nbsp;&nbsp;".$this->Convertdate(2,$T_DATE)."&nbsp;&nbsp;
								<b>พนักงานเก็บเงิน</b>&nbsp;&nbsp;".$OFFICER."&nbsp;&nbsp;
							</td>
						</tr>
						<tr>
							<td style='text-align:right;' colspan='12'>Rpsyd 10,12</td>
						</tr>
						<br>
						".$head."
						".$html."
						<tr>
							<td class='wf' style='height:1px;border-top:0.1px solid black;' colspan='12'></td>
						</tr>
						<tr>
							<td class='wf' style='height:1px;border-top:0.1px solid black;' colspan='12'></td>
						</tr>
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