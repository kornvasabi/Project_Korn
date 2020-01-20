<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//BEE+
class ReportALar_agging extends MY_Controller {
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
				<div class='col-sm-12 col-xs-12' style='height:100%;overflow:auto;font-size:10.5pt;'>					
					<div class='row' style='height:90%;'>
						<div class='col-sm-12 col-xs-12' style='background-color:#0067a5;border:5px solid white;height:75px;text-align:center;font-size:12pt;color:white;font-weight:bold;'>	
							<br>รายงานรายละเอียดอายุหนี้<br>
						</div>
						<div class='col-sm-8 col-xs-8 col-sm-offset-2'>
							<br>
							<div class='col-sm-4 col-xs-4'>	
								<div class='form-group'>
									สาขา
									<select id='LOCAT1' class='form-control input-sm' data-placeholder='สาขา'></select>
								</div>
							</div>
							<div class='col-sm-4 col-xs-4'>	
								<div class='form-group' >
									จากเลขที่สัญญา
									<select id='CONTNO1' class='form-control input-sm' data-placeholder='จากเลขที่สัญญา'></select>
								</div>
							</div>
							<div class='col-sm-4 col-xs-4'>	
								<div class='form-group' >
									รหัสพนักงาน
									<select id='BILLCOLL1' class='form-control input-sm' data-placeholder='รหัสพนักงาน'></select>
								</div>
							</div>
							<div class='col-sm-4 col-xs-4'>	
								<div class='form-group'>
									ลูกหนี้ จากวันที่
									<input type='text' id='FRMDATE' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' placeholder='ลูกหนี้ จากวันที่' value='".$this->today('startofmonth')."' style='font-size:10.5pt'>
								</div>
							</div>
							<div class='col-sm-8 col-xs-8'>	
								<div class='form-group'>
									ถึงวันที่
									<input type='text' id='TODATE' class='form-control input-sm' style='font-size:10.5pt' value='".$this->today('endofmonth')."' disabled>
								</div>
							</div>
						</div>
						<div class='col-sm-8 col-xs-8 col-sm-offset-2'>
							<br>	
							<div class='col-sm-12 col-xs-12'>	
								<div class='form-group'>
									เรียงลำดับข้อมูล
									<div class='col-sm-12 col-xs-12' style='border:0.1px dotted #d6d6d6;'>	
										<div class='col-sm-4 col-xs-4'>
											<div class='form-group'><br>
												<input type= 'radio' id='ldate' name='orderby' checked> ตามวันครบกำหนด
											</div>
										</div>
										<div class='col-sm-4 col-xs-4'>
											<div class='form-group'><br>
												<input type= 'radio' id='contno' name='orderby'> ตามเลขที่สัญญา
											</div>
										</div>
										<div class='col-sm-4 col-xs-4'>
											<div class='form-group'><br>
												<input type= 'radio' id='billcoll' name='orderby'> ตามพนักงานเก็บเงิน
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class='col-sm-8 col-xs-8 col-sm-offset-2'>
							<br>
							<div class='col-sm-6 col-xs-6'>
								<div class='form-group'>
									รูปแบบการพิมพ์
									<div class='col-sm-12 col-xs-12' style='border:0.1px dotted #d6d6d6;'>	
										<div class='col-sm-12 col-xs-12'>
											<div class='form-group'>
												<br>
												<input type= 'radio' id='hor' name='layout' checked> แนวนอน
												<br><br>
												<input type= 'radio' id='ver' name='layout'> แนวตั้ง
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group'>
									รายงาน
									<div class='col-sm-12 col-xs-12' style='border:0.1px dotted #d6d6d6;'>	
										<div class='col-sm-12 col-xs-12'>
											<div class='form-group'>
												<br>
												<input type= 'radio' id='sumvat' name='vat' checked> รวม VAT
												<br><br>
												<input type= 'radio' id='notvat' name='vat'> แยก VAT
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class='col-sm-8 col-xs-8 col-sm-offset-2' style='text-align:center;color:#999;'><br><br>** คำแนะนำ : เพื่อความสมบูรณ์ของข้อมูล ควรพิมพ์รายงาน ณ วันสิ้นเดือน **</div>
					</div>
					<div class='row' style='height:10%;'>
						<div class='col-sm-12 col-xs-12'><br>	
							<button id='btnt1search' class='btn btn-primary btn-sm' style='width:100%;font-size:10.5pt;'><span class='glyphicon glyphicon-search'> แสดง</span></button>
						</div>
					</div>
				</div>
			</div>
		";
		
		$html.= "<script src='".base_url('public/js/SYS05/ReportALar_agging.js')."'></script>";
		echo $html;
	}
	
	function search(){
		$LOCAT1		= $_REQUEST["LOCAT1"];
		$CONTNO1	= $_REQUEST["CONTNO1"];
		$BILLCOLL1 	= str_replace(chr(0),'',$_REQUEST["BILLCOLL1"]);
		$FRMDATE 	= $this->Convertdate(1,$_REQUEST["FRMDATE"]);
		$TODATE 	= $this->Convertdate(1,$_REQUEST["TODATE"]);
		$orderby 	= $_REQUEST["orderby"];
		$vat 		= $_REQUEST["vat"];
		
		$cond = ""; $rpcond = "";
		
		if($CONTNO1 != ""){
			$cond .= " AND (A.CONTNO LIKE '".$CONTNO1."%')";
			$rpcond .= "  เลขที่สัญญา ".$CONTNO1;
		}
		
		if($LOCAT1 != ""){
			$cond .= " AND (A.LOCAT LIKE '".$LOCAT1."%')";
			$rpcond .= "  สาขา ".$LOCAT1;
		}
		
		if($BILLCOLL1 != ""){
			$cond .= " AND (A.BILLCOLL = '".$BILLCOLL1."' OR A.BILLCOLL IS NULL)";
			$rpcond .= "  พนักงานเก็บเงิน ".$BILLCOLL1;
		}
		
		if($vat == "sumvat"){
			$sql = "
					IF OBJECT_ID('tempdb..#main') IS NOT NULL DROP TABLE #main
					select *
					into #main
					from(
						select A.LOCAT, A.CONTNO, A.CUSCOD, B.SNAM+B.NAME1+' '+B.NAME2 as CUSNAME, convert(nvarchar,A.SDATE,112) as SDATE, 
						convert(nvarchar,A.FDATE,112) as FDATE, A.LDATE, A.NPRICE, A.TOTPRC, A.NPAYRES, A.TOTPRES, A.NDAWN, A.TOTDWN, A.BILLCOLL
						from {$this->MAuth->getdb('ARMAST')} A  
						left join {$this->MAuth->getdb('CUSTMAST')} B on A.CUSCOD = B.CUSCOD
						left join {$this->MAuth->getdb('CHQTRAN')} C on A.CONTNO = C.CONTNO and C.LOCATPAY = A.LOCAT
						where (C.FLAG <> 'C') AND A.TOTPRC > 0 AND (C.PAYFOR = '002' OR C.PAYFOR = '006' OR C.PAYFOR = '007') AND C.payamt > 0 
						AND (A.SDATE <= '".$TODATE."') ".$cond."
						group by A.CONTNO, A.CUSCOD, A.LOCAT, A.FDATE, A.SDATE, B.SNAM, B.NAME1, B.NAME2, A.NPRICE, A.NPAYRES, A.NDAWN, A.TOTDWN, A.LDATE, A.BILLCOLL, 
						A.TOTPRC, A.TOTPRES, A.SMPAY
						having (A.TOTPRC > A.SMPAY OR (A.TOTPRC <= A.SMPAY AND MAX(C.PAYDT) > '".$TODATE."')) 
						
						union 
						
						select A.LOCAT, A.CONTNO, A.CUSCOD, B.SNAM+B.NAME1+' '+B.NAME2 as CUSNAME, convert(nvarchar,A.SDATE,112) as SDATE, 
						convert(nvarchar,A.FDATE,112) as FDATE, A.LDATE, A.NPRICE, A.TOTPRC, A.NPAYRES, A.TOTPRES, A.NDAWN, A.TOTDWN, A.BILLCOLL
						from {$this->MAuth->getdb('ARMAST')} A  
						left join {$this->MAuth->getdb('CUSTMAST')} B on A.CUSCOD = B.CUSCOD
						where A.contno not in (select c.contno from {$this->MAuth->getdb('CHQTRAN')} c where (c.flag <> 'C') and (c.payfor = '002' or c.payfor = '006' or c.payfor = '007')) 
						AND A.TOTPRC > 0 
						AND (A.SDATE <= '".$TODATE."') ".$cond." 
						group by A.CONTNO, A.CUSCOD, A.LOCAT, A.FDATE, A.SDATE, B.SNAM, B.NAME1, B.NAME2, A.NPRICE, A.NPAYRES, A.NDAWN, A.TOTDWN, A.LDATE, A.BILLCOLL, 
						A.TOTPRC, A.TOTPRES, A.SMPAY
						
						union
						 
						select A.LOCAT, A.CONTNO, A.CUSCOD, B.SNAM+B.NAME1+' '+B.NAME2 as CUSNAME, convert(nvarchar,A.SDATE,112) as SDATE, 
						convert(nvarchar,A.FDATE,112) as FDATE, A.LDATE, A.NPRICE, A.TOTPRC, A.NPAYRES, A.TOTPRES, A.NDAWN, A.TOTDWN, A.BILLCOLL
						from {$this->MAuth->getdb('HARMAST')} A
						left join {$this->MAuth->getdb('CHGAR_VIEW')} B on A.CONTNO = B.CONTNO and (A.LOCAT = B.LOCAT)
						left join {$this->MAuth->getdb('CHQTRAN')} C on A.CONTNO = C.CONTNO and C.LOCATPAY = A.LOCAT
						where B.date1 > '".$TODATE."' AND (C.FLAG <> 'C') AND A.TOTPRC > 0 AND (C.PAYFOR = '002' OR C.PAYFOR = '006' OR C.PAYFOR = '007') and C.payamt > 0
						AND (A.SDATE <= '".$TODATE."') ".$cond." 
						group by A.CONTNO, A.CUSCOD, A.LOCAT, A.FDATE, A.SDATE, B.SNAM, B.NAME1, B.NAME2, A.NPRICE, A.NPAYRES, A.NDAWN, A.TOTDWN, A.LDATE, A.BILLCOLL, 
						A.TOTPRC, A.TOTPRES, A.SMPAY
						having (A.TOTPRC > A.SMPAY OR (A.TOTPRC <= A.SMPAY AND MAX(C.PAYDT) > '".$TODATE."')) 

						union 
						
						select A.LOCAT, A.CONTNO, A.CUSCOD, B.SNAM+B.NAME1+' '+B.NAME2 as CUSNAME, convert(nvarchar,A.SDATE,112) as SDATE, 
						convert(nvarchar,A.FDATE,112) as FDATE, A.LDATE, A.NPRICE, A.TOTPRC, A.NPAYRES, A.TOTPRES, A.NDAWN, A.TOTDWN, A.BILLCOLL
						from {$this->MAuth->getdb('HARMAST')} A
						left join {$this->MAuth->getdb('CHGAR_VIEW')} B on A.CONTNO = B.CONTNO and (A.LOCAT = B.LOCAT)
						where a.contno not in (select c.contno from {$this->MAuth->getdb('CHQTRAN')} c where  (c.flag <> 'C') and (c.payfor = '002' or c.payfor = '006' or c.payfor = '007')) 
						AND b.date1 > '".$TODATE."' AND A.TOTPRC > 0
						AND (A.SDATE <= '".$TODATE."') ".$cond."
						group by A.CONTNO, A.CUSCOD, A.LOCAT, A.FDATE, A.SDATE, B.SNAM, B.NAME1, B.NAME2, A.NPRICE, A.NPAYRES, A.NDAWN, A.TOTDWN, A.LDATE, A.BILLCOLL, 
						A.TOTPRC, A.TOTPRES, A.SMPAY
					)main
			";//echo $sql; 
			$query = $this->db->query($sql);
			
			$sql = "
					IF OBJECT_ID('tempdb..#damt') IS NOT NULL DROP TABLE #damt
					select *
					into #damt
					from(
						select CONTNO,
						sum(CASE WHEN (DDATE BETWEEN  '".$FRMDATE."' AND '".$TODATE."' ) THEN  DAMT ELSE 0 END) AS DAMT ,
						sum(CASE WHEN (DDATE < '".$FRMDATE."' ) THEN  DAMT  ELSE 0 END) AS B_DAMT ,
						sum(CASE WHEN (DDATE > '".$TODATE."') THEN  DAMT  ELSE 0 END) AS A_DAMT
						from {$this->MAuth->getdb('ARPAY')} 
						where CONTNO in (select CONTNO from #main) 
						group by CONTNO 
						union  
						select CONTNO,
						sum(CASE WHEN (DDATE BETWEEN  '".$FRMDATE."' AND '".$TODATE."' ) THEN  DAMT ELSE 0 END) AS DAMT ,
						sum(CASE WHEN (DDATE < '".$FRMDATE."' ) THEN  DAMT  ELSE 0 END) AS B_DAMT ,
						sum(CASE WHEN (DDATE > '".$TODATE."') THEN  DAMT  ELSE 0 END) AS A_DAMT
						from {$this->MAuth->getdb('HARPAY')}
						where CONTNO in (select CONTNO from #main) 
						group by CONTNO 
					)damt
			";//echo $sql; exit;
			$query = $this->db->query($sql);
			
			$sql = "
					IF OBJECT_ID('tempdb..#pay') IS NOT NULL DROP TABLE #pay
					select *
					into #pay
					from(
						select A.CONTNO, 
						sum(CASE WHEN (A.PAYDT < '".$FRMDATE."' AND ((A.PAYFOR = '006') OR (A.PAYFOR = '007') OR (A.PAYFOR = '002'))AND A.FLAG <>'C'  ) THEN  A.PAYAMT ELSE 0 END) AS B_TOTPAYDAMT, 
						sum(CASE WHEN (A.PAYDT < '".$FRMDATE."' AND ((A.PAYFOR = '006') OR (A.PAYFOR = '007')) AND A.FLAG <>'C' ) THEN  A.PAYAMT ELSE 0 END) AS B_PAYDAMT,
						sum(CASE WHEN (A.PAYDT < '".$FRMDATE."' AND A.PAYFOR = '002' AND A.FLAG <>'C' ) THEN  A.PAYAMT ELSE 0 END) AS B_PAYDWN,
						sum(CASE WHEN (A.PAYDT BETWEEN '".$FRMDATE."' AND '".$TODATE."' AND ((A.PAYFOR = '006') OR (A.PAYFOR = '007')) AND A.FLAG <>'C' ) THEN A.PAYAMT ELSE 0 END) AS PAYDAMT,
						sum(CASE WHEN (A.PAYDT BETWEEN '".$FRMDATE."' AND '".$TODATE."' AND (A.PAYFOR = '002') AND A.FLAG <>'C' ) THEN  A.PAYAMT ELSE 0 END) AS PAYDWN,
						sum(CASE WHEN (A.PAYDT > '".$TODATE."' AND ((A.PAYFOR = '006') OR (A.PAYFOR = '007') OR (A.PAYFOR = '002'))AND A.FLAG <>'C'  ) THEN  A.PAYAMT ELSE 0 END) AS A_TOTPAYDAMT
						FROM {$this->MAuth->getdb('CHQTRAN')} A  
						WHERE A.LOCATPAY+A.CONTNO in (select LOCAT+CONTNO from #main)
						group by A.CONTNO 
					)pay

			";//echo $sql; 
			$query = $this->db->query($sql);
			
			$sql = "
					IF OBJECT_ID('tempdb..#daykang') IS NOT NULL DROP TABLE #daykang
					select *
					into #daykang
					from(
						select CONTNO, PAYDT, DATEDIFF(DAY,PAYDT,'".$TODATE."') as DAYKANG
						from (
							select CONTNO, MAX(PAYDT) as PAYDT
							FROM {$this->MAuth->getdb('CHQTRAN')} A  
							WHERE A.LOCATPAY+A.CONTNO in (select LOCAT+CONTNO from #main)
							and A.PAYDT < '".$FRMDATE."' and (A.PAYFOR = '006' or A.PAYFOR = '007')
							group by CONTNO
						)A
					)daykang
			";//echo $sql; 
			$query = $this->db->query($sql);
			
			$sql = "
					IF OBJECT_ID('tempdb..#report') IS NOT NULL DROP TABLE #report
					select *
					into #report
					from(
						select LOCAT, CONTNO, CUSCOD, CUSNAME, SDATE, FDATE, LDATE, TOTPRC, B_DAMT, DAMT, PAYDAMT, A_DAMT, KANGDWN, PAYDWN, BALAR, A_TOTPAYDAMT, TOTAR, DAYKANG, BILLCOLL,
						case	when DAYKANG is not null and DAYKANG between 1 and 30 then  A_DAMT 
								when DAYKANG is null and PAYDAMT >= DAMT and DAMT != 0 then  A_DAMT else 0 end as KANG1,
						case	when DAYKANG is not null and DAYKANG between 31 and 60 then  A_DAMT else 0 end as KANG2,
						case	when DAYKANG is not null and DAYKANG > 60 then  A_DAMT 
								when DAYKANG is null and (PAYDAMT < DAMT or DAMT = 0) then  A_DAMT else 0 end as KANG3
						from(
							select LOCAT, a.CONTNO, CUSCOD, CUSNAME, SDATE, FDATE, LDATE, TOTPRC, B_DAMT, DAMT, isnull(PAYDAMT,0) as PAYDAMT, 
							(isnull(B_DAMT,0)+isnull(DAMT,0)+isnull(A_DAMT,0))-(isnull(B_PAYDAMT,0)+isnull(PAYDAMT,0)) as A_DAMT,
							isnull(NDAWN,0)-isnull(B_PAYDWN,0) as KANGDWN, isnull(PAYDWN,0) as PAYDWN, 
							((isnull(B_DAMT,0)+isnull(DAMT,0)+isnull(A_DAMT,0))-(isnull(B_PAYDAMT,0)+(isnull(PAYDAMT,0)))+(isnull(NDAWN,0)-isnull(B_PAYDWN,0)-isnull(PAYDWN,0))) as BALAR,
							isnull(A_TOTPAYDAMT,0) as A_TOTPAYDAMT, ((isnull(B_DAMT,0)+isnull(DAMT,0)+isnull(A_DAMT,0))-(isnull(B_PAYDAMT,0)+(isnull(PAYDAMT,0)))+
							(isnull(NDAWN,0)-isnull(B_PAYDWN,0)-isnull(PAYDWN,0)))-isnull(A_TOTPAYDAMT,0) as TOTAR, DAYKANG, BILLCOLL
							from #main a
							left join #damt b on a.CONTNO = b.CONTNO
							left join #pay c on a.CONTNO = c.CONTNO
							left join #daykang d on a.CONTNO = d.CONTNO
						)A
					)report
			";//echo $sql; 
			$query = $this->db->query($sql);
			
			$sql = "
					select LOCAT, CONTNO, CUSCOD, CUSNAME, SDATE, FDATE, TOTPRC, B_DAMT, DAMT, PAYDAMT, KANG1, KANG2, KANG3, A_DAMT, KANGDWN, PAYDWN, BALAR, 
					A_TOTPAYDAMT, TOTAR, DAYKANG, BILLCOLL
					from #report
					order by ".$orderby."
			";//echo $sql; 
			$query = $this->db->query($sql);
			
			$sql = "
					select 'รวมทั้งหมด' as Total, sum(TOTPRC) as sumTOTPRC, sum(B_DAMT) as sumB_DAMT, sum(DAMT) as sumDAMT, sum(DAMT) as sumDAMT, sum(PAYDAMT) as sumPAYDAMT, sum(KANG1) as sumKANG1, sum(KANG2) as sumKANG2, 
					sum(KANG3) as sumKANG3, sum(A_DAMT) as sumA_DAMT, sum(KANGDWN) as sumKANGDWN, sum(PAYDWN) as sumPAYDWN, sum(BALAR) as sumBALAR, 
					sum(A_TOTPAYDAMT) as sumA_TOTPAYDAMT, sum(TOTAR) as sumTOTAR
					from #report
			";//echo $sql; exit;
			$query2 = $this->db->query($sql);
		}else if($vat == "notvat"){
			$sql = "
					IF OBJECT_ID('tempdb..#main') IS NOT NULL DROP TABLE #main
					select *
					into #main
					from(
						select A.LOCAT, A.CONTNO, A.CUSCOD, B.SNAM+B.NAME1+' '+B.NAME2 as CUSNAME, convert(nvarchar,A.SDATE,112) as SDATE, 
						convert(nvarchar,A.FDATE,112) as FDATE, A.LDATE, A.NPRICE, A.TOTPRC, A.NPAYRES, A.TOTPRES, A.NDAWN, A.TOTDWN, A.BILLCOLL
						from {$this->MAuth->getdb('ARMAST')} A  
						left join {$this->MAuth->getdb('CUSTMAST')} B on A.CUSCOD = B.CUSCOD
						left join {$this->MAuth->getdb('CHQTRAN')} C on A.CONTNO = C.CONTNO and C.LOCATPAY = A.LOCAT
						where (C.FLAG <> 'C') AND A.TOTPRC > 0 AND (C.PAYFOR = '002' OR C.PAYFOR = '006' OR C.PAYFOR = '007') AND C.payamt > 0 
						AND (A.SDATE <= '".$TODATE."') ".$cond."
						group by A.CONTNO, A.CUSCOD, A.LOCAT, A.FDATE, A.SDATE, B.SNAM, B.NAME1, B.NAME2, A.NPRICE, A.NPAYRES, A.NDAWN, A.TOTDWN, A.LDATE, A.BILLCOLL, 
						A.TOTPRC, A.TOTPRES, A.SMPAY
						having (A.TOTPRC > A.SMPAY OR (A.TOTPRC <= A.SMPAY AND MAX(C.PAYDT) > '".$TODATE."')) 
						
						union 
						
						select A.LOCAT, A.CONTNO, A.CUSCOD, B.SNAM+B.NAME1+' '+B.NAME2 as CUSNAME, convert(nvarchar,A.SDATE,112) as SDATE, 
						convert(nvarchar,A.FDATE,112) as FDATE, A.LDATE, A.NPRICE, A.TOTPRC, A.NPAYRES, A.TOTPRES, A.NDAWN, A.TOTDWN, A.BILLCOLL
						from {$this->MAuth->getdb('ARMAST')} A  
						left join {$this->MAuth->getdb('CUSTMAST')} B on A.CUSCOD = B.CUSCOD
						where A.contno not in (select c.contno from {$this->MAuth->getdb('CHQTRAN')} c where (c.flag <> 'C') and (c.payfor = '002' or c.payfor = '006' or c.payfor = '007')) 
						AND A.TOTPRC > 0 
						AND (A.SDATE <= '".$TODATE."') ".$cond." 
						group by A.CONTNO, A.CUSCOD, A.LOCAT, A.FDATE, A.SDATE, B.SNAM, B.NAME1, B.NAME2, A.NPRICE, A.NPAYRES, A.NDAWN, A.TOTDWN, A.LDATE, A.BILLCOLL, 
						A.TOTPRC, A.TOTPRES, A.SMPAY
						
						union
						 
						select A.LOCAT, A.CONTNO, A.CUSCOD, B.SNAM+B.NAME1+' '+B.NAME2 as CUSNAME, convert(nvarchar,A.SDATE,112) as SDATE, 
						convert(nvarchar,A.FDATE,112) as FDATE, A.LDATE, A.NPRICE, A.TOTPRC, A.NPAYRES, A.TOTPRES, A.NDAWN, A.TOTDWN, A.BILLCOLL
						from {$this->MAuth->getdb('HARMAST')} A
						left join {$this->MAuth->getdb('CHGAR_VIEW')} B on A.CONTNO = B.CONTNO and (A.LOCAT = B.LOCAT)
						left join {$this->MAuth->getdb('CHQTRAN')} C on A.CONTNO = C.CONTNO and C.LOCATPAY = A.LOCAT
						where B.date1 > '".$TODATE."' AND (C.FLAG <> 'C') AND A.TOTPRC > 0 AND (C.PAYFOR = '002' OR C.PAYFOR = '006' OR C.PAYFOR = '007') and C.payamt > 0
						AND (A.SDATE <= '".$TODATE."') ".$cond." 
						group by A.CONTNO, A.CUSCOD, A.LOCAT, A.FDATE, A.SDATE, B.SNAM, B.NAME1, B.NAME2, A.NPRICE, A.NPAYRES, A.NDAWN, A.TOTDWN, A.LDATE, A.BILLCOLL, 
						A.TOTPRC, A.TOTPRES, A.SMPAY
						having (A.TOTPRC > A.SMPAY OR (A.TOTPRC <= A.SMPAY AND MAX(C.PAYDT) > '".$TODATE."')) 

						union 
						
						select A.LOCAT, A.CONTNO, A.CUSCOD, B.SNAM+B.NAME1+' '+B.NAME2 as CUSNAME, convert(nvarchar,A.SDATE,112) as SDATE, 
						convert(nvarchar,A.FDATE,112) as FDATE, A.LDATE, A.NPRICE, A.TOTPRC, A.NPAYRES, A.TOTPRES, A.NDAWN, A.TOTDWN, A.BILLCOLL
						from {$this->MAuth->getdb('HARMAST')} A
						left join {$this->MAuth->getdb('CHGAR_VIEW')} B on A.CONTNO = B.CONTNO and (A.LOCAT = B.LOCAT)
						where a.contno not in (select c.contno from {$this->MAuth->getdb('CHQTRAN')} c where  (c.flag <> 'C') and (c.payfor = '002' or c.payfor = '006' or c.payfor = '007')) 
						AND b.date1 > '".$TODATE."' AND A.TOTPRC > 0
						AND (A.SDATE <= '".$TODATE."') ".$cond."
						group by A.CONTNO, A.CUSCOD, A.LOCAT, A.FDATE, A.SDATE, B.SNAM, B.NAME1, B.NAME2, A.NPRICE, A.NPAYRES, A.NDAWN, A.TOTDWN, A.LDATE, A.BILLCOLL, 
						A.TOTPRC, A.TOTPRES, A.SMPAY
					)main
			";//echo $sql; 
			$query = $this->db->query($sql);
			
			$sql = "
					IF OBJECT_ID('tempdb..#damt') IS NOT NULL DROP TABLE #damt
					select *
					into #damt
					from(
						select CONTNO,
						sum(CASE WHEN (DDATE BETWEEN  '".$FRMDATE."' AND '".$TODATE."' ) THEN  N_DAMT ELSE 0 END) AS DAMT ,
						sum(CASE WHEN (DDATE < '".$FRMDATE."' ) THEN  N_DAMT  ELSE 0 END) AS B_DAMT ,
						sum(CASE WHEN (DDATE > '".$TODATE."') THEN  N_DAMT  ELSE 0 END) AS A_DAMT
						from {$this->MAuth->getdb('ARPAY')} 
						where CONTNO in (select CONTNO from #main) 
						group by CONTNO 
						union  
						select CONTNO,
						sum(CASE WHEN (DDATE BETWEEN  '".$FRMDATE."' AND '".$TODATE."' ) THEN  N_DAMT ELSE 0 END) AS DAMT ,
						sum(CASE WHEN (DDATE < '".$FRMDATE."' ) THEN  N_DAMT  ELSE 0 END) AS B_DAMT ,
						sum(CASE WHEN (DDATE > '".$TODATE."') THEN  N_DAMT  ELSE 0 END) AS A_DAMT
						from {$this->MAuth->getdb('HARPAY')}
						where CONTNO in (select CONTNO from #main) 
						group by CONTNO 
					)damt
			";//echo $sql; 
			$query = $this->db->query($sql);
			
			$sql = "
					IF OBJECT_ID('tempdb..#pay') IS NOT NULL DROP TABLE #pay
					select *
					into #pay
					from(
						select A.CONTNO, 
						sum(CASE WHEN (A.PAYDT < '".$FRMDATE."' AND ((A.PAYFOR = '006') OR (A.PAYFOR = '007') OR (A.PAYFOR = '002'))AND A.FLAG <>'C'  ) THEN  A.PAYAMT_N ELSE 0 END) AS B_TOTPAYDAMT, 
						sum(CASE WHEN (A.PAYDT < '".$FRMDATE."' AND ((A.PAYFOR = '006') OR (A.PAYFOR = '007')) AND A.FLAG <>'C' ) THEN  A.PAYAMT_N ELSE 0 END) AS B_PAYDAMT,
						sum(CASE WHEN (A.PAYDT < '".$FRMDATE."' AND A.PAYFOR = '002' AND A.FLAG <>'C' ) THEN  A.PAYAMT_N ELSE 0 END) AS B_PAYDWN,
						sum(CASE WHEN (A.PAYDT BETWEEN '".$FRMDATE."' AND '".$TODATE."' AND ((A.PAYFOR = '006') OR (A.PAYFOR = '007')) AND A.FLAG <>'C' ) THEN A.PAYAMT_N ELSE 0 END) AS PAYDAMT,
						sum(CASE WHEN (A.PAYDT BETWEEN '".$FRMDATE."' AND '".$TODATE."' AND (A.PAYFOR = '002') AND A.FLAG <>'C' ) THEN  A.PAYAMT_N ELSE 0 END) AS PAYDWN,
						sum(CASE WHEN (A.PAYDT > '".$TODATE."' AND ((A.PAYFOR = '006') OR (A.PAYFOR = '007') OR (A.PAYFOR = '002'))AND A.FLAG <>'C'  ) THEN  A.PAYAMT_N ELSE 0 END) AS A_TOTPAYDAMT
						FROM {$this->MAuth->getdb('CHQTRAN')} A  
						WHERE A.LOCATPAY+A.CONTNO in (select LOCAT+CONTNO from #main)
						group by A.CONTNO 
					)pay
			";//echo $sql; 
			$query = $this->db->query($sql);
			
			$sql = "
					IF OBJECT_ID('tempdb..#daykang') IS NOT NULL DROP TABLE #daykang
					select *
					into #daykang
					from(
						select CONTNO, PAYDT, DATEDIFF(DAY,PAYDT,'".$TODATE."') as DAYKANG
						from (
							select CONTNO, MAX(PAYDT) as PAYDT
							FROM {$this->MAuth->getdb('CHQTRAN')} A  
							WHERE A.LOCATPAY+A.CONTNO in (select LOCAT+CONTNO from #main)
							and A.PAYDT < '".$FRMDATE."' and (A.PAYFOR = '006' or A.PAYFOR = '007')
							group by CONTNO
						)A
					)daykang
			";//echo $sql; 
			$query = $this->db->query($sql);
			
			$sql = "
					IF OBJECT_ID('tempdb..#report') IS NOT NULL DROP TABLE #report
					select *
					into #report
					from( 
						select LOCAT, CONTNO, CUSCOD, CUSNAME, SDATE, FDATE, LDATE, NPRICE, B_DAMT ,DAMT, PAYDAMT, A_DAMT, KANGDWN, PAYDWN, BALAR, A_TOTPAYDAMT, TOTAR, DAYKANG, BILLCOLL,
						case	when DAYKANG is not null and DAYKANG between 1 and 30 then  A_DAMT 
								when DAYKANG is null and PAYDAMT >= DAMT and DAMT != 0 then  A_DAMT else 0 end as KANG1,
						case	when DAYKANG is not null and DAYKANG between 31 and 60 then  A_DAMT else 0 end as KANG2,
						case	when DAYKANG is not null and DAYKANG > 60 then  A_DAMT 
								when DAYKANG is null and (PAYDAMT < DAMT or DAMT = 0) then  A_DAMT else 0 end as KANG3
						from(
							select LOCAT, a.CONTNO, CUSCOD, CUSNAME, SDATE, FDATE, LDATE, NPRICE, B_DAMT, DAMT, isnull(PAYDAMT,0) as PAYDAMT, 
							(isnull(B_DAMT,0)+isnull(DAMT,0)+isnull(A_DAMT,0))-(isnull(B_PAYDAMT,0)+isnull(PAYDAMT,0)) as A_DAMT,
							isnull(NDAWN,0)-isnull(B_PAYDWN,0) as KANGDWN, isnull(PAYDWN,0) as PAYDWN, 
							((isnull(B_DAMT,0)+isnull(DAMT,0)+isnull(A_DAMT,0))-(isnull(B_PAYDAMT,0)+(isnull(PAYDAMT,0)))+(isnull(NDAWN,0)-isnull(B_PAYDWN,0)-isnull(PAYDWN,0))) as BALAR,
							isnull(A_TOTPAYDAMT,0) as A_TOTPAYDAMT, ((isnull(B_DAMT,0)+isnull(DAMT,0)+isnull(A_DAMT,0))-(isnull(B_PAYDAMT,0)+(isnull(PAYDAMT,0)))+
							(isnull(NDAWN,0)-isnull(B_PAYDWN,0)-isnull(PAYDWN,0)))-isnull(A_TOTPAYDAMT,0) as TOTAR, DAYKANG, BILLCOLL
							from #main a
							left join #damt b on a.CONTNO = b.CONTNO
							left join #pay c on a.CONTNO = c.CONTNO
							left join #daykang d on a.CONTNO = d.CONTNO
						)A
					)report
			";//echo $sql; 
			$query = $this->db->query($sql);
			
			$sql = "
					select LOCAT, CONTNO, CUSCOD, CUSNAME, SDATE, FDATE, NPRICE as TOTPRC, B_DAMT, DAMT, PAYDAMT, KANG1, KANG2, KANG3, A_DAMT, KANGDWN, PAYDWN, BALAR, 
					A_TOTPAYDAMT, TOTAR, DAYKANG, BILLCOLL
					from #report
					order by ".$orderby."
			";//echo $sql; exit;
			$query = $this->db->query($sql);
			
			$sql = "
					select 'รวมทั้งหมด' as Total, sum(NPRICE) as sumTOTPRC, sum(B_DAMT) as sumB_DAMT, sum(DAMT) as sumDAMT, sum(PAYDAMT) as sumPAYDAMT, sum(KANG1) as sumKANG1, sum(KANG2) as sumKANG2, 
					sum(KANG3) as sumKANG3, sum(A_DAMT) as sumA_DAMT, sum(KANGDWN) as sumKANGDWN, sum(PAYDWN) as sumPAYDWN, sum(BALAR) as sumBALAR, 
					sum(A_TOTPAYDAMT) as sumA_TOTPAYDAMT, sum(TOTAR) as sumTOTAR
					from #report
			";//echo $sql; exit;
			$query2 = $this->db->query($sql);
		}
		
		$head = ""; $html = ""; $head2 = "";  $report = ""; $sumreport = ""; $sumreport2 = ""; $i = 0; 
		
		$head = "<tr style='height:25px;'>
				<th style='display:none;'>#</th>
				<th style='vertical-align:top;'>สาขา</th>
				<th style='vertical-align:top;'>เลขที่สัญญา</th>
				<th style='vertical-align:top;'>รหัสลูกค้า<br>ชื่อ - นามสกุล</th>
				<th style='vertical-align:top;text-align:center;'>วันที่ขาย<br>วันดิวงวดแรก</th>
				<th style='vertical-align:top;text-align:right;'>ราคาขาย<br>ค่างวดก่อนหน้า</th> 
				<th style='vertical-align:top;text-align:right;'>ค่างวดเดือนนี้<br>รับชำระเดือนนี้</th>
				<th style='vertical-align:top;text-align:right;'>ค้างยกไป<br>1-30วัน</th>
				<th style='vertical-align:top;text-align:right;'>ค้างยกไป<br>31-60วัน</th>
				<th style='vertical-align:top;text-align:right;'>ค้างยกไป<br>มากกว่า 60วัน</th>
				<th style='vertical-align:top;text-align:right;'>ค้างงวด<br>ยกไป</th>
				<th style='vertical-align:top;text-align:right;'>ค้างดาวน์<br>ชำระดาวน์</th>
				<th style='vertical-align:top;text-align:right;'>คงเหลือตามสัญญา<br>ชำระค่างวดล่วงหน้า</th>
				<th style='vertical-align:top;text-align:right;'>ลูกหนี้คงเหลือจริง<br>BillColl</th>
				</tr>
		";
		
		$head2 = "<tr>
				<th style='vertical-align:top;'>#</th>
				<th style='vertical-align:top;'>สาขา</th>
				<th style='vertical-align:top;'>เลขที่สัญญา</th>
				<th style='vertical-align:top;'>รหัสลูกค้า</th>
				<th style='vertical-align:top;'>ชื่อ - นามสกุล</th>
				<th style='vertical-align:top;text-align:center;'>วันที่ขาย</th>
				<th style='vertical-align:top;text-align:center;'>วันดิวงวดแรก</th>
				<th style='vertical-align:top;text-align:right;'>ราคาขาย</th> 
				<th style='vertical-align:top;text-align:right;'>ค่างวดก่อนหน้า</th> 
				<th style='vertical-align:top;text-align:right;'>ค่างวดเดือนนี้</th>
				<th style='vertical-align:top;text-align:right;'>รับชำระเดือนนี้</th>
				<th style='vertical-align:top;text-align:right;'>ค้างยกไป 1-30 วัน</th>
				<th style='vertical-align:top;text-align:right;'>ค้างยกไป 31-60 วัน</th>
				<th style='vertical-align:top;text-align:right;'>ค้างยกไป มากกว่า 60 วัน</th>
				<th style='vertical-align:top;text-align:right;'>ค้างงวดยกไป</th>
				<th style='vertical-align:top;text-align:right;'>ค้างดาวน์</th>
				<th style='vertical-align:top;text-align:right;'>ชำระดาวน์</th>
				<th style='vertical-align:top;text-align:right;'>คงเหลือตามสัญญา</th>
				<th style='vertical-align:top;text-align:right;'>ชำระค่างวดล่วงหน้า</th>
				<th style='vertical-align:top;text-align:right;'>ลูกหนี้คงเหลือจริง</th>
				<th style='vertical-align:top;text-align:center;'>BillColl</th>
		";
		
		$NRow = 1;
		if($query->row()){
			foreach($query->result() as $row){$i++;
				$html .= "
					<tr class='trow' seq=".$NRow.">
						<td seq=".$NRow++." style='display:none;'></td>
						<td>".$row->LOCAT."</td>
						<td>".$row->CONTNO."</td>
						<td>".$row->CUSCOD."<br>".$row->CUSNAME."</td>
						<td align='center'>".$this->Convertdate(2,$row->SDATE)."<br>".$this->Convertdate(2,$row->FDATE)."</td>
						<td align='right'>".number_format($row->TOTPRC,2)."<br>".number_format($row->B_DAMT,2)."</td>
						<td align='right'>".number_format($row->DAMT,2)."<br>".number_format($row->PAYDAMT,2)."</td>
						<td align='right'>".number_format($row->KANG1,2)."</td>
						<td align='right'>".number_format($row->KANG2,2)."</td>
						<td align='right'>".number_format($row->KANG3,2)."</td>
						<td align='right'>".number_format($row->A_DAMT,2)."</td>
						<td align='right'>".number_format($row->KANGDWN,2)."<br>".number_format($row->PAYDWN,2)."</td>
						<td align='right'>".number_format($row->BALAR,2)."<br>".number_format($row->A_TOTPAYDAMT,2)."</td>
						<td align='right'>".number_format($row->TOTAR,2)."<br>".$row->BILLCOLL."</td>
					</tr>
				";	
			}
		}
		
		
		$No = 1;
		if($query->row()){
			foreach($query->result() as $row){
				$report .= "
					<tr class='trow'>
						<td style='mso-number-format:\"\@\";'>".$No++."</td>
						<td style='mso-number-format:\"\@\";'>".$row->LOCAT."</td>
						<td style='mso-number-format:\"\@\";'>".$row->CONTNO."</td>
						<td style='mso-number-format:\"\@\";'>".$row->CUSCOD."</td>
						<td style='mso-number-format:\"\@\";'>".$row->CUSNAME."</td>
						<td style='mso-number-format:\"\@\";'>".$this->Convertdate(2,$row->SDATE)."</td>
						<td style='mso-number-format:\"\@\";'>".$this->Convertdate(2,$row->FDATE)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->TOTPRC,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->B_DAMT,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->DAMT,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->PAYDAMT,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->KANG1,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->KANG2,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->KANG3,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->A_DAMT,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->KANGDWN,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->PAYDWN,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->BALAR,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->A_TOTPAYDAMT,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->TOTAR,2)."</td>
						<td style='mso-number-format:\"\@\";text-align:center;'>".$row->BILLCOLL."</td>
					</tr>
				";	
			}
		}
		
		if($query2->row()){
			foreach($query2->result() as $row){
				$sumreport = "
					<tr style='height:30px;'>
						<th colspan='4' style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;vertical-align:middle;text-align:center;'>".$row->Total."</th>
						<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;vertical-align:top;text-align:right;'>".number_format($row->sumTOTPRC,2)."<br>".number_format($row->sumB_DAMT,2)."</th>
						<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;vertical-align:top;text-align:right;'>".number_format($row->sumDAMT,2)."<br>".number_format($row->sumPAYDAMT,2)."</th>
						<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;vertical-align:top;text-align:right;'>".number_format($row->sumKANG1,2)."</th>
						<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;vertical-align:top;text-align:right;'>".number_format($row->sumKANG2,2)."</th>
						<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;vertical-align:top;text-align:right;'>".number_format($row->sumKANG3,2)."</th>
						<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;vertical-align:top;text-align:right;'>".number_format($row->sumA_DAMT,2)."</th>
						<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;vertical-align:top;text-align:right;'>".number_format($row->sumKANGDWN,2)."<br>".number_format($row->sumPAYDWN,2)."</th>
						<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;vertical-align:top;text-align:right;'>".number_format($row->sumBALAR,2)."<br>".number_format($row->sumA_TOTPAYDAMT,2)."</th>
						<th style='border:0px;text-align:right;'>".number_format($row->sumTOTAR,2)."</th>
					</tr>
				";	
			}
		}
		
		if($query2->row()){
			foreach($query2->result() as $row){
				$sumreport2 = "
					<tr class='trow'>
						<th style='mso-number-format:\"\@\";text-align:center;' colspan='7'>".$row->Total."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumTOTPRC,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumB_DAMT,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumDAMT,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumPAYDAMT,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumKANG1,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumKANG2,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumKANG3,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumA_DAMT,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumKANGDWN,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumPAYDWN,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumBALAR,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumA_TOTPAYDAMT,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumTOTAR,2)."</th>
						<th style='mso-number-format:\"\@\";text-align:center;'></th>
					</tr>
				";	
			}
		}
		
		if($i>0){
			$html = "
				<div id='table-fixed-ReportALar_agging' class='col-sm-12' style='height:100%;width:100%;overflow:auto;font-size:9pt;'>
					<table id='table-ReportALar_agging' style='background-color:white;' class='col-sm-12 display table table-bordered' cellspacing='0' width='calc(100% - 1px)'>
						<thead>
						<tr style='height:40px;'>
							<th colspan='13' style='font-size:12pt;border:0px;vertical-align;middle;text-align:center;'>รายงานรายละเอียดอายุหนี้</th>
						</tr>
						<tr style='height:25px;'>
							<td colspan='13' style='border-bottom:1px solid #ddd;vertical-align;middle;text-align:center;'>ณ วันที่  ".$_REQUEST["TODATE"]." ".$rpcond."  ออกรายงาน ณ วันที่ ".$this->today('today')."</td>
						</tr>
						".$head."
						</thead>	
						<tbody style='height: 10px !important; overflow: scroll;'>
						".$html."
						</tbody>	
						<tfoot>
						".$sumreport."
						</tfoot>
					</table>
				</div>
			";
		}else{
			$html="<font style='color:red;'>ไม่มีข้อมูล</font>";
		}
		
		$report = "
			<div id='table-fixed-ReportALar_agging2' class='col-sm-12' style='height:100%;width:100%;overflow:auto;'>
				<table id='table-ReportALar_agging2' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='100%'>
					<thead>
						<tr>
							<th colspan='21' style='font-size:12pt;border:0px;text-align:center;'>รายงานรายละเอียดอายุหนี้</th>
						</tr>
						<tr>
							<td colspan='21' style='border:0px;text-align:center;'>ณ วันที่  ".$_REQUEST["TODATE"]." ".$rpcond." ออกรายงาน ณ วันที่ ".$this->today('today')."</td>
						</tr>
						".$head2."
					</thead>	
					<tbody>
						".$report."
						".$sumreport2."
					</tbody>
				</table>
			</div>
		";
		
		$response = array("html"=>$html, "report"=>$report, "reporttoday"=>str_replace('/','-',$this->today('today')));
		echo json_encode($response);
	}
	
	function conditiontopdf(){
		$data 	= 	array();
		$data[] = 	urlencode($_REQUEST["LOCAT1"].'||'.$_REQUEST["CONTNO1"].'||'.$_REQUEST["BILLCOLL1"].'||'.$_REQUEST["FRMDATE"].'||'.$_REQUEST["TODATE"]
					.'||'.$_REQUEST["vat"].'||'.$_REQUEST["orderby"].'||'.$_REQUEST["layout"]);
		echo json_encode($this->generateData($data,"encode"));
	}
	
	function pdf(){
		ini_set("memory_limit","-1");
		ini_set("pcre.backtrack_limit", "100000000");
		
		$data 	= array();
		$data[] = $_GET["condpdf"];
		$arrs 	= $this->generateData($data,"decode");
		$arrs[0]= urldecode($arrs[0]);
		$tx 	= explode("||",$arrs[0]);
		$LOCAT1		= $tx[0];
		$CONTNO1	= $tx[1];
		$BILLCOLL1 	= str_replace(chr(0),'',$tx[2]);
		$FRMDATE 	= $this->Convertdate(1,$tx[3]);
		$TODATE 	= $this->Convertdate(1,$tx[4]);
		$vat 		= $tx[5];
		$orderby 	= $tx[6];
		$layout 	= $tx[7];
		
		$cond = ""; $rpcond = "";
		
		if($CONTNO1 != ""){
			$cond .= " AND (A.CONTNO LIKE '".$CONTNO1."%')";
			$rpcond .= "  เลขที่สัญญา ".$CONTNO1;
		}
		
		if($LOCAT1 != ""){
			$cond .= " AND (A.LOCAT LIKE '".$LOCAT1."%')";
			$rpcond .= "  สาขา ".$LOCAT1;
		}
		
		if($BILLCOLL1 != ""){
			$cond .= " AND (A.BILLCOLL = '".$BILLCOLL1."' OR A.BILLCOLL IS NULL)";
			$rpcond .= "  พนักงานเก็บเงิน ".$BILLCOLL1;
		}
		
		if($vat == "sumvat"){
			$sql = "
					IF OBJECT_ID('tempdb..#main') IS NOT NULL DROP TABLE #main
					select *
					into #main
					from(
						select A.LOCAT, A.CONTNO, A.CUSCOD, B.SNAM+B.NAME1+' '+B.NAME2 as CUSNAME, convert(nvarchar,A.SDATE,112) as SDATE, 
						convert(nvarchar,A.FDATE,112) as FDATE, A.LDATE, A.NPRICE, A.TOTPRC, A.NPAYRES, A.TOTPRES, A.NDAWN, A.TOTDWN, A.BILLCOLL
						from {$this->MAuth->getdb('ARMAST')} A  
						left join {$this->MAuth->getdb('CUSTMAST')} B on A.CUSCOD = B.CUSCOD
						left join {$this->MAuth->getdb('CHQTRAN')} C on A.CONTNO = C.CONTNO and C.LOCATPAY = A.LOCAT
						where (C.FLAG <> 'C') AND A.TOTPRC > 0 AND (C.PAYFOR = '002' OR C.PAYFOR = '006' OR C.PAYFOR = '007') AND C.payamt > 0 
						AND (A.SDATE <= '".$TODATE."') ".$cond."
						group by A.CONTNO, A.CUSCOD, A.LOCAT, A.FDATE, A.SDATE, B.SNAM, B.NAME1, B.NAME2, A.NPRICE, A.NPAYRES, A.NDAWN, A.TOTDWN, A.LDATE, A.BILLCOLL, 
						A.TOTPRC, A.TOTPRES, A.SMPAY
						having (A.TOTPRC > A.SMPAY OR (A.TOTPRC <= A.SMPAY AND MAX(C.PAYDT) > '".$TODATE."')) 
						
						union 
						
						select A.LOCAT, A.CONTNO, A.CUSCOD, B.SNAM+B.NAME1+' '+B.NAME2 as CUSNAME, convert(nvarchar,A.SDATE,112) as SDATE, 
						convert(nvarchar,A.FDATE,112) as FDATE, A.LDATE, A.NPRICE, A.TOTPRC, A.NPAYRES, A.TOTPRES, A.NDAWN, A.TOTDWN, A.BILLCOLL
						from {$this->MAuth->getdb('ARMAST')} A  
						left join {$this->MAuth->getdb('CUSTMAST')} B on A.CUSCOD = B.CUSCOD
						where A.contno not in (select c.contno from {$this->MAuth->getdb('CHQTRAN')} c where (c.flag <> 'C') and (c.payfor = '002' or c.payfor = '006' or c.payfor = '007')) 
						AND A.TOTPRC > 0 
						AND (A.SDATE <= '".$TODATE."') ".$cond." 
						group by A.CONTNO, A.CUSCOD, A.LOCAT, A.FDATE, A.SDATE, B.SNAM, B.NAME1, B.NAME2, A.NPRICE, A.NPAYRES, A.NDAWN, A.TOTDWN, A.LDATE, A.BILLCOLL, 
						A.TOTPRC, A.TOTPRES, A.SMPAY
						
						union
						 
						select A.LOCAT, A.CONTNO, A.CUSCOD, B.SNAM+B.NAME1+' '+B.NAME2 as CUSNAME, convert(nvarchar,A.SDATE,112) as SDATE, 
						convert(nvarchar,A.FDATE,112) as FDATE, A.LDATE, A.NPRICE, A.TOTPRC, A.NPAYRES, A.TOTPRES, A.NDAWN, A.TOTDWN, A.BILLCOLL
						from {$this->MAuth->getdb('HARMAST')} A
						left join {$this->MAuth->getdb('CHGAR_VIEW')} B on A.CONTNO = B.CONTNO and (A.LOCAT = B.LOCAT)
						left join {$this->MAuth->getdb('CHQTRAN')} C on A.CONTNO = C.CONTNO and C.LOCATPAY = A.LOCAT
						where B.date1 > '".$TODATE."' AND (C.FLAG <> 'C') AND A.TOTPRC > 0 AND (C.PAYFOR = '002' OR C.PAYFOR = '006' OR C.PAYFOR = '007') and C.payamt > 0
						AND (A.SDATE <= '".$TODATE."') ".$cond." 
						group by A.CONTNO, A.CUSCOD, A.LOCAT, A.FDATE, A.SDATE, B.SNAM, B.NAME1, B.NAME2, A.NPRICE, A.NPAYRES, A.NDAWN, A.TOTDWN, A.LDATE, A.BILLCOLL, 
						A.TOTPRC, A.TOTPRES, A.SMPAY
						having (A.TOTPRC > A.SMPAY OR (A.TOTPRC <= A.SMPAY AND MAX(C.PAYDT) > '".$TODATE."')) 

						union 
						
						select A.LOCAT, A.CONTNO, A.CUSCOD, B.SNAM+B.NAME1+' '+B.NAME2 as CUSNAME, convert(nvarchar,A.SDATE,112) as SDATE, 
						convert(nvarchar,A.FDATE,112) as FDATE, A.LDATE, A.NPRICE, A.TOTPRC, A.NPAYRES, A.TOTPRES, A.NDAWN, A.TOTDWN, A.BILLCOLL
						from {$this->MAuth->getdb('HARMAST')} A
						left join {$this->MAuth->getdb('CHGAR_VIEW')} B on A.CONTNO = B.CONTNO and (A.LOCAT = B.LOCAT)
						where a.contno not in (select c.contno from {$this->MAuth->getdb('CHQTRAN')} c where  (c.flag <> 'C') and (c.payfor = '002' or c.payfor = '006' or c.payfor = '007')) 
						AND b.date1 > '".$TODATE."' AND A.TOTPRC > 0
						AND (A.SDATE <= '".$TODATE."') ".$cond."
						group by A.CONTNO, A.CUSCOD, A.LOCAT, A.FDATE, A.SDATE, B.SNAM, B.NAME1, B.NAME2, A.NPRICE, A.NPAYRES, A.NDAWN, A.TOTDWN, A.LDATE, A.BILLCOLL, 
						A.TOTPRC, A.TOTPRES, A.SMPAY
					)main
			";//echo $sql; 
			$query = $this->db->query($sql);
			
			$sql = "
					IF OBJECT_ID('tempdb..#damt') IS NOT NULL DROP TABLE #damt
					select *
					into #damt
					from(
						select CONTNO,
						sum(CASE WHEN (DDATE BETWEEN  '".$FRMDATE."' AND '".$TODATE."' ) THEN  DAMT ELSE 0 END) AS DAMT ,
						sum(CASE WHEN (DDATE < '".$FRMDATE."' ) THEN  DAMT  ELSE 0 END) AS B_DAMT ,
						sum(CASE WHEN (DDATE > '".$TODATE."') THEN  DAMT  ELSE 0 END) AS A_DAMT
						from {$this->MAuth->getdb('ARPAY')} 
						where CONTNO in (select CONTNO from #main) 
						group by CONTNO 
						union  
						select CONTNO,
						sum(CASE WHEN (DDATE BETWEEN  '".$FRMDATE."' AND '".$TODATE."' ) THEN  DAMT ELSE 0 END) AS DAMT ,
						sum(CASE WHEN (DDATE < '".$FRMDATE."' ) THEN  DAMT  ELSE 0 END) AS B_DAMT ,
						sum(CASE WHEN (DDATE > '".$TODATE."') THEN  DAMT  ELSE 0 END) AS A_DAMT
						from {$this->MAuth->getdb('HARPAY')}
						where CONTNO in (select CONTNO from #main) 
						group by CONTNO 
					)damt
			";//echo $sql; exit;
			$query = $this->db->query($sql);
			
			$sql = "
					IF OBJECT_ID('tempdb..#pay') IS NOT NULL DROP TABLE #pay
					select *
					into #pay
					from(
						select A.CONTNO, 
						sum(CASE WHEN (A.PAYDT < '".$FRMDATE."' AND ((A.PAYFOR = '006') OR (A.PAYFOR = '007') OR (A.PAYFOR = '002'))AND A.FLAG <>'C'  ) THEN  A.PAYAMT ELSE 0 END) AS B_TOTPAYDAMT, 
						sum(CASE WHEN (A.PAYDT < '".$FRMDATE."' AND ((A.PAYFOR = '006') OR (A.PAYFOR = '007')) AND A.FLAG <>'C' ) THEN  A.PAYAMT ELSE 0 END) AS B_PAYDAMT,
						sum(CASE WHEN (A.PAYDT < '".$FRMDATE."' AND A.PAYFOR = '002' AND A.FLAG <>'C' ) THEN  A.PAYAMT ELSE 0 END) AS B_PAYDWN,
						sum(CASE WHEN (A.PAYDT BETWEEN '".$FRMDATE."' AND '".$TODATE."' AND ((A.PAYFOR = '006') OR (A.PAYFOR = '007')) AND A.FLAG <>'C' ) THEN A.PAYAMT ELSE 0 END) AS PAYDAMT,
						sum(CASE WHEN (A.PAYDT BETWEEN '".$FRMDATE."' AND '".$TODATE."' AND (A.PAYFOR = '002') AND A.FLAG <>'C' ) THEN  A.PAYAMT ELSE 0 END) AS PAYDWN,
						sum(CASE WHEN (A.PAYDT > '".$TODATE."' AND ((A.PAYFOR = '006') OR (A.PAYFOR = '007') OR (A.PAYFOR = '002'))AND A.FLAG <>'C'  ) THEN  A.PAYAMT ELSE 0 END) AS A_TOTPAYDAMT
						FROM {$this->MAuth->getdb('CHQTRAN')} A  
						WHERE A.LOCATPAY+A.CONTNO in (select LOCAT+CONTNO from #main)
						group by A.CONTNO 
					)pay

			";//echo $sql; 
			$query = $this->db->query($sql);
			
			$sql = "
					IF OBJECT_ID('tempdb..#daykang') IS NOT NULL DROP TABLE #daykang
					select *
					into #daykang
					from(
						select CONTNO, PAYDT, DATEDIFF(DAY,PAYDT,'".$TODATE."') as DAYKANG
						from (
							select CONTNO, MAX(PAYDT) as PAYDT
							FROM {$this->MAuth->getdb('CHQTRAN')} A  
							WHERE A.LOCATPAY+A.CONTNO in (select LOCAT+CONTNO from #main)
							and A.PAYDT < '".$FRMDATE."' and (A.PAYFOR = '006' or A.PAYFOR = '007')
							group by CONTNO
						)A
					)daykang
			";//echo $sql; 
			$query = $this->db->query($sql);
			
			$sql = "
					IF OBJECT_ID('tempdb..#report') IS NOT NULL DROP TABLE #report
					select *
					into #report
					from(
						select LOCAT, CONTNO, CUSCOD, CUSNAME, SDATE, FDATE, LDATE, TOTPRC, B_DAMT, DAMT, PAYDAMT, A_DAMT, KANGDWN, PAYDWN, BALAR, A_TOTPAYDAMT, TOTAR, DAYKANG, BILLCOLL,
						case	when DAYKANG is not null and DAYKANG between 1 and 30 then  A_DAMT 
								when DAYKANG is null and PAYDAMT >= DAMT and DAMT != 0 then  A_DAMT else 0 end as KANG1,
						case	when DAYKANG is not null and DAYKANG between 31 and 60 then  A_DAMT else 0 end as KANG2,
						case	when DAYKANG is not null and DAYKANG > 60 then  A_DAMT 
								when DAYKANG is null and (PAYDAMT < DAMT or DAMT = 0) then  A_DAMT else 0 end as KANG3
						from(
							select LOCAT, a.CONTNO, CUSCOD, CUSNAME, SDATE, FDATE, LDATE, TOTPRC, B_DAMT, DAMT, isnull(PAYDAMT,0) as PAYDAMT, 
							(isnull(B_DAMT,0)+isnull(DAMT,0)+isnull(A_DAMT,0))-(isnull(B_PAYDAMT,0)+isnull(PAYDAMT,0)) as A_DAMT,
							isnull(NDAWN,0)-isnull(B_PAYDWN,0) as KANGDWN, isnull(PAYDWN,0) as PAYDWN, 
							((isnull(B_DAMT,0)+isnull(DAMT,0)+isnull(A_DAMT,0))-(isnull(B_PAYDAMT,0)+(isnull(PAYDAMT,0)))+(isnull(NDAWN,0)-isnull(B_PAYDWN,0)-isnull(PAYDWN,0))) as BALAR,
							isnull(A_TOTPAYDAMT,0) as A_TOTPAYDAMT, ((isnull(B_DAMT,0)+isnull(DAMT,0)+isnull(A_DAMT,0))-(isnull(B_PAYDAMT,0)+(isnull(PAYDAMT,0)))+
							(isnull(NDAWN,0)-isnull(B_PAYDWN,0)-isnull(PAYDWN,0)))-isnull(A_TOTPAYDAMT,0) as TOTAR, DAYKANG, BILLCOLL
							from #main a
							left join #damt b on a.CONTNO = b.CONTNO
							left join #pay c on a.CONTNO = c.CONTNO
							left join #daykang d on a.CONTNO = d.CONTNO
						)A
					)report
			";//echo $sql; 
			$query = $this->db->query($sql);
			
			$sql = "
					select LOCAT, CONTNO, CUSCOD, CUSNAME, SDATE, FDATE, TOTPRC, B_DAMT, DAMT, PAYDAMT, KANG1, KANG2, KANG3, A_DAMT, KANGDWN, PAYDWN, BALAR, 
					A_TOTPAYDAMT, TOTAR, DAYKANG, BILLCOLL
					from #report
					order by ".$orderby."
			";//echo $sql; 
			$query = $this->db->query($sql);
			
			$sql = "
					select 'รวมทั้งหมด' as Total, sum(TOTPRC) as sumTOTPRC, sum(B_DAMT) as sumB_DAMT, sum(DAMT) as sumDAMT, sum(DAMT) as sumDAMT, sum(PAYDAMT) as sumPAYDAMT, sum(KANG1) as sumKANG1, sum(KANG2) as sumKANG2, 
					sum(KANG3) as sumKANG3, sum(A_DAMT) as sumA_DAMT, sum(KANGDWN) as sumKANGDWN, sum(PAYDWN) as sumPAYDWN, sum(BALAR) as sumBALAR, 
					sum(A_TOTPAYDAMT) as sumA_TOTPAYDAMT, sum(TOTAR) as sumTOTAR
					from #report
			";//echo $sql; exit;
			$query2 = $this->db->query($sql);
		}else if($vat == "notvat"){
			$sql = "
					IF OBJECT_ID('tempdb..#main') IS NOT NULL DROP TABLE #main
					select *
					into #main
					from(
						select A.LOCAT, A.CONTNO, A.CUSCOD, B.SNAM+B.NAME1+' '+B.NAME2 as CUSNAME, convert(nvarchar,A.SDATE,112) as SDATE, 
						convert(nvarchar,A.FDATE,112) as FDATE, A.LDATE, A.NPRICE, A.TOTPRC, A.NPAYRES, A.TOTPRES, A.NDAWN, A.TOTDWN, A.BILLCOLL
						from {$this->MAuth->getdb('ARMAST')} A  
						left join {$this->MAuth->getdb('CUSTMAST')} B on A.CUSCOD = B.CUSCOD
						left join {$this->MAuth->getdb('CHQTRAN')} C on A.CONTNO = C.CONTNO and C.LOCATPAY = A.LOCAT
						where (C.FLAG <> 'C') AND A.TOTPRC > 0 AND (C.PAYFOR = '002' OR C.PAYFOR = '006' OR C.PAYFOR = '007') AND C.payamt > 0 
						AND (A.SDATE <= '".$TODATE."') ".$cond."
						group by A.CONTNO, A.CUSCOD, A.LOCAT, A.FDATE, A.SDATE, B.SNAM, B.NAME1, B.NAME2, A.NPRICE, A.NPAYRES, A.NDAWN, A.TOTDWN, A.LDATE, A.BILLCOLL, 
						A.TOTPRC, A.TOTPRES, A.SMPAY
						having (A.TOTPRC > A.SMPAY OR (A.TOTPRC <= A.SMPAY AND MAX(C.PAYDT) > '".$TODATE."')) 
						
						union 
						
						select A.LOCAT, A.CONTNO, A.CUSCOD, B.SNAM+B.NAME1+' '+B.NAME2 as CUSNAME, convert(nvarchar,A.SDATE,112) as SDATE, 
						convert(nvarchar,A.FDATE,112) as FDATE, A.LDATE, A.NPRICE, A.TOTPRC, A.NPAYRES, A.TOTPRES, A.NDAWN, A.TOTDWN, A.BILLCOLL
						from {$this->MAuth->getdb('ARMAST')} A  
						left join {$this->MAuth->getdb('CUSTMAST')} B on A.CUSCOD = B.CUSCOD
						where A.contno not in (select c.contno from {$this->MAuth->getdb('CHQTRAN')} c where (c.flag <> 'C') and (c.payfor = '002' or c.payfor = '006' or c.payfor = '007')) 
						AND A.TOTPRC > 0 
						AND (A.SDATE <= '".$TODATE."') ".$cond." 
						group by A.CONTNO, A.CUSCOD, A.LOCAT, A.FDATE, A.SDATE, B.SNAM, B.NAME1, B.NAME2, A.NPRICE, A.NPAYRES, A.NDAWN, A.TOTDWN, A.LDATE, A.BILLCOLL, 
						A.TOTPRC, A.TOTPRES, A.SMPAY
						
						union
						 
						select A.LOCAT, A.CONTNO, A.CUSCOD, B.SNAM+B.NAME1+' '+B.NAME2 as CUSNAME, convert(nvarchar,A.SDATE,112) as SDATE, 
						convert(nvarchar,A.FDATE,112) as FDATE, A.LDATE, A.NPRICE, A.TOTPRC, A.NPAYRES, A.TOTPRES, A.NDAWN, A.TOTDWN, A.BILLCOLL
						from {$this->MAuth->getdb('HARMAST')} A
						left join {$this->MAuth->getdb('CHGAR_VIEW')} B on A.CONTNO = B.CONTNO and (A.LOCAT = B.LOCAT)
						left join {$this->MAuth->getdb('CHQTRAN')} C on A.CONTNO = C.CONTNO and C.LOCATPAY = A.LOCAT
						where B.date1 > '".$TODATE."' AND (C.FLAG <> 'C') AND A.TOTPRC > 0 AND (C.PAYFOR = '002' OR C.PAYFOR = '006' OR C.PAYFOR = '007') and C.payamt > 0
						AND (A.SDATE <= '".$TODATE."') ".$cond." 
						group by A.CONTNO, A.CUSCOD, A.LOCAT, A.FDATE, A.SDATE, B.SNAM, B.NAME1, B.NAME2, A.NPRICE, A.NPAYRES, A.NDAWN, A.TOTDWN, A.LDATE, A.BILLCOLL, 
						A.TOTPRC, A.TOTPRES, A.SMPAY
						having (A.TOTPRC > A.SMPAY OR (A.TOTPRC <= A.SMPAY AND MAX(C.PAYDT) > '".$TODATE."')) 

						union 
						
						select A.LOCAT, A.CONTNO, A.CUSCOD, B.SNAM+B.NAME1+' '+B.NAME2 as CUSNAME, convert(nvarchar,A.SDATE,112) as SDATE, 
						convert(nvarchar,A.FDATE,112) as FDATE, A.LDATE, A.NPRICE, A.TOTPRC, A.NPAYRES, A.TOTPRES, A.NDAWN, A.TOTDWN, A.BILLCOLL
						from {$this->MAuth->getdb('HARMAST')} A
						left join {$this->MAuth->getdb('CHGAR_VIEW')} B on A.CONTNO = B.CONTNO and (A.LOCAT = B.LOCAT)
						where a.contno not in (select c.contno from {$this->MAuth->getdb('CHQTRAN')} c where  (c.flag <> 'C') and (c.payfor = '002' or c.payfor = '006' or c.payfor = '007')) 
						AND b.date1 > '".$TODATE."' AND A.TOTPRC > 0
						AND (A.SDATE <= '".$TODATE."') ".$cond."
						group by A.CONTNO, A.CUSCOD, A.LOCAT, A.FDATE, A.SDATE, B.SNAM, B.NAME1, B.NAME2, A.NPRICE, A.NPAYRES, A.NDAWN, A.TOTDWN, A.LDATE, A.BILLCOLL, 
						A.TOTPRC, A.TOTPRES, A.SMPAY
					)main
			";//echo $sql; 
			$query = $this->db->query($sql);
			
			$sql = "
					IF OBJECT_ID('tempdb..#damt') IS NOT NULL DROP TABLE #damt
					select *
					into #damt
					from(
						select CONTNO,
						sum(CASE WHEN (DDATE BETWEEN  '".$FRMDATE."' AND '".$TODATE."' ) THEN  N_DAMT ELSE 0 END) AS DAMT ,
						sum(CASE WHEN (DDATE < '".$FRMDATE."' ) THEN  N_DAMT  ELSE 0 END) AS B_DAMT ,
						sum(CASE WHEN (DDATE > '".$TODATE."') THEN  N_DAMT  ELSE 0 END) AS A_DAMT
						from {$this->MAuth->getdb('ARPAY')} 
						where CONTNO in (select CONTNO from #main) 
						group by CONTNO 
						union  
						select CONTNO,
						sum(CASE WHEN (DDATE BETWEEN  '".$FRMDATE."' AND '".$TODATE."' ) THEN  N_DAMT ELSE 0 END) AS DAMT ,
						sum(CASE WHEN (DDATE < '".$FRMDATE."' ) THEN  N_DAMT  ELSE 0 END) AS B_DAMT ,
						sum(CASE WHEN (DDATE > '".$TODATE."') THEN  N_DAMT  ELSE 0 END) AS A_DAMT
						from {$this->MAuth->getdb('HARPAY')}
						where CONTNO in (select CONTNO from #main) 
						group by CONTNO 
					)damt
			";//echo $sql; 
			$query = $this->db->query($sql);
			
			$sql = "
					IF OBJECT_ID('tempdb..#pay') IS NOT NULL DROP TABLE #pay
					select *
					into #pay
					from(
						select A.CONTNO, 
						sum(CASE WHEN (A.PAYDT < '".$FRMDATE."' AND ((A.PAYFOR = '006') OR (A.PAYFOR = '007') OR (A.PAYFOR = '002'))AND A.FLAG <>'C'  ) THEN  A.PAYAMT_N ELSE 0 END) AS B_TOTPAYDAMT, 
						sum(CASE WHEN (A.PAYDT < '".$FRMDATE."' AND ((A.PAYFOR = '006') OR (A.PAYFOR = '007')) AND A.FLAG <>'C' ) THEN  A.PAYAMT_N ELSE 0 END) AS B_PAYDAMT,
						sum(CASE WHEN (A.PAYDT < '".$FRMDATE."' AND A.PAYFOR = '002' AND A.FLAG <>'C' ) THEN  A.PAYAMT_N ELSE 0 END) AS B_PAYDWN,
						sum(CASE WHEN (A.PAYDT BETWEEN '".$FRMDATE."' AND '".$TODATE."' AND ((A.PAYFOR = '006') OR (A.PAYFOR = '007')) AND A.FLAG <>'C' ) THEN A.PAYAMT_N ELSE 0 END) AS PAYDAMT,
						sum(CASE WHEN (A.PAYDT BETWEEN '".$FRMDATE."' AND '".$TODATE."' AND (A.PAYFOR = '002') AND A.FLAG <>'C' ) THEN  A.PAYAMT_N ELSE 0 END) AS PAYDWN,
						sum(CASE WHEN (A.PAYDT > '".$TODATE."' AND ((A.PAYFOR = '006') OR (A.PAYFOR = '007') OR (A.PAYFOR = '002'))AND A.FLAG <>'C'  ) THEN  A.PAYAMT_N ELSE 0 END) AS A_TOTPAYDAMT
						FROM {$this->MAuth->getdb('CHQTRAN')} A  
						WHERE A.LOCATPAY+A.CONTNO in (select LOCAT+CONTNO from #main)
						group by A.CONTNO 
					)pay
			";//echo $sql; 
			$query = $this->db->query($sql);
			
			$sql = "
					IF OBJECT_ID('tempdb..#daykang') IS NOT NULL DROP TABLE #daykang
					select *
					into #daykang
					from(
						select CONTNO, PAYDT, DATEDIFF(DAY,PAYDT,'".$TODATE."') as DAYKANG
						from (
							select CONTNO, MAX(PAYDT) as PAYDT
							FROM {$this->MAuth->getdb('CHQTRAN')} A  
							WHERE A.LOCATPAY+A.CONTNO in (select LOCAT+CONTNO from #main)
							and A.PAYDT < '".$FRMDATE."' and (A.PAYFOR = '006' or A.PAYFOR = '007')
							group by CONTNO
						)A
					)daykang
			";//echo $sql; 
			$query = $this->db->query($sql);
			
			$sql = "
					IF OBJECT_ID('tempdb..#report') IS NOT NULL DROP TABLE #report
					select *
					into #report
					from( 
						select LOCAT, CONTNO, CUSCOD, CUSNAME, SDATE, FDATE, LDATE, NPRICE, B_DAMT ,DAMT, PAYDAMT, A_DAMT, KANGDWN, PAYDWN, BALAR, A_TOTPAYDAMT, TOTAR, DAYKANG, BILLCOLL,
						case	when DAYKANG is not null and DAYKANG between 1 and 30 then  A_DAMT 
								when DAYKANG is null and PAYDAMT >= DAMT and DAMT != 0 then  A_DAMT else 0 end as KANG1,
						case	when DAYKANG is not null and DAYKANG between 31 and 60 then  A_DAMT else 0 end as KANG2,
						case	when DAYKANG is not null and DAYKANG > 60 then  A_DAMT 
								when DAYKANG is null and (PAYDAMT < DAMT or DAMT = 0) then  A_DAMT else 0 end as KANG3
						from(
							select LOCAT, a.CONTNO, CUSCOD, CUSNAME, SDATE, FDATE, LDATE, NPRICE, B_DAMT, DAMT, isnull(PAYDAMT,0) as PAYDAMT, 
							(isnull(B_DAMT,0)+isnull(DAMT,0)+isnull(A_DAMT,0))-(isnull(B_PAYDAMT,0)+isnull(PAYDAMT,0)) as A_DAMT,
							isnull(NDAWN,0)-isnull(B_PAYDWN,0) as KANGDWN, isnull(PAYDWN,0) as PAYDWN, 
							((isnull(B_DAMT,0)+isnull(DAMT,0)+isnull(A_DAMT,0))-(isnull(B_PAYDAMT,0)+(isnull(PAYDAMT,0)))+(isnull(NDAWN,0)-isnull(B_PAYDWN,0)-isnull(PAYDWN,0))) as BALAR,
							isnull(A_TOTPAYDAMT,0) as A_TOTPAYDAMT, ((isnull(B_DAMT,0)+isnull(DAMT,0)+isnull(A_DAMT,0))-(isnull(B_PAYDAMT,0)+(isnull(PAYDAMT,0)))+
							(isnull(NDAWN,0)-isnull(B_PAYDWN,0)-isnull(PAYDWN,0)))-isnull(A_TOTPAYDAMT,0) as TOTAR, DAYKANG, BILLCOLL
							from #main a
							left join #damt b on a.CONTNO = b.CONTNO
							left join #pay c on a.CONTNO = c.CONTNO
							left join #daykang d on a.CONTNO = d.CONTNO
						)A
					)report
			";//echo $sql; 
			$query = $this->db->query($sql);
			
			$sql = "
					select LOCAT, CONTNO, CUSCOD, CUSNAME, SDATE, FDATE, NPRICE as TOTPRC, B_DAMT, DAMT, PAYDAMT, KANG1, KANG2, KANG3, A_DAMT, KANGDWN, PAYDWN, BALAR, 
					A_TOTPAYDAMT, TOTAR, DAYKANG, BILLCOLL
					from #report
					order by ".$orderby."
			";//echo $sql; exit;
			$query = $this->db->query($sql);
			
			$sql = "
					select 'รวมทั้งหมด' as Total, sum(NPRICE) as sumTOTPRC, sum(B_DAMT) as sumB_DAMT, sum(DAMT) as sumDAMT, sum(PAYDAMT) as sumPAYDAMT, sum(KANG1) as sumKANG1, sum(KANG2) as sumKANG2, 
					sum(KANG3) as sumKANG3, sum(A_DAMT) as sumA_DAMT, sum(KANGDWN) as sumKANGDWN, sum(PAYDWN) as sumPAYDWN, sum(BALAR) as sumBALAR, 
					sum(A_TOTPAYDAMT) as sumA_TOTPAYDAMT, sum(TOTAR) as sumTOTAR
					from #report
			";//echo $sql; exit;
			$query2 = $this->db->query($sql);
		}
		
		$head = ""; $html = ""; $i=0; 

		$head = "
				<tr>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>#</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>สาขา</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>เลขที่สัญญา</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>รหัสลูกค้า<br>ชื่อ - นามสกุล</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>วันที่ขาย<br>วันดิวงวดแรก</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>ราคาขาย<br>ค่างวดก่อนหน้า</th> 
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>ค่างวดเดือนนี้<br>รับชำระเดือนนี้</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>ค้างยกไป<br>1-30วัน</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>ค้างยกไป<br>31-60วัน</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>ค้างยกไป<br>มากกว่า 60วัน</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>ค้างงวด<br>ยกไป</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>ค้างดาวน์<br>ชำระดาวน์</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>คงเหลือตามสัญญา<br>ชำระค่างวดล่วงหน้า</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>ลูกหนี้คงเหลือจริง<br>BillColl</th>
				</tr>
		";
		
		$No = 1;
		if($query->row()){
			foreach($query->result() as $row){	
				$html .= "
					<tr class='trow' seq=".$No.">
						<td style='width:30px;'>".$No++."</td>
						<td style='width:40px;'>".$row->LOCAT."</td>
						<td style='width:75px;'>".$row->CONTNO."</td>
						<td style='width:140px;'>".$row->CUSCOD."<br>".$row->CUSNAME."</td>
						<td style='width:75px;'>".$this->Convertdate(2,$row->SDATE)."<br>".$this->Convertdate(2,$row->FDATE)."</td>
						<td style='width:75px;' align='right'>".number_format($row->TOTPRC,2)."<br>".number_format($row->B_DAMT,2)."</td>
						<td style='width:75px;' align='right'>".number_format($row->DAMT,2)."<br>".number_format($row->PAYDAMT,2)."</td>
						<td style='width:70px;' align='right'>".number_format($row->KANG1,2)."</td>
						<td style='width:70px;' align='right'>".number_format($row->KANG2,2)."</td>
						<td style='width:75px;' align='right'>".number_format($row->KANG3,2)."</td>
						<td style='width:75px;' align='right'>".number_format($row->A_DAMT,2)."</td>
						<td style='width:70px;' align='right'>".number_format($row->KANGDWN,2)."<br>".number_format($row->PAYDWN,2)."</td>
						<td style='width:80px;' align='right'>".number_format($row->BALAR,2)."<br>".number_format($row->A_TOTPAYDAMT,2)."</td>
						<td style='width:75px;' align='right'>".number_format($row->TOTAR,2)."<br>".$row->BILLCOLL."</td>
					</tr>
				";	
			}
		}
		
		if($query2->row()){
			foreach($query2->result() as $row){	
				$html .= "
					<tr class='trow bor' style='background-color:#ebebeb;'>
						<th colspan='5' style='text-align:center;vertical-align:middle;'>".$row->Total."</th>
						<th align='right'>".number_format($row->sumTOTPRC,2)."<br>".number_format($row->sumB_DAMT,2)."</th>
						<th align='right'>".number_format($row->sumDAMT,2)."<br>".number_format($row->sumPAYDAMT,2)."</th>
						<th align='right'>".number_format($row->sumKANG1,2)."</th>
						<th align='right'>".number_format($row->sumKANG2,2)."</th>
						<th align='right'>".number_format($row->sumKANG3,2)."</th>
						<th align='right'>".number_format($row->sumA_DAMT,2)."</th>
						<th align='right'>".number_format($row->sumKANGDWN,2)."<br>".number_format($row->sumPAYDWN,2)."</th>
						<th align='right'>".number_format($row->sumBALAR,2)."<br>".number_format($row->sumA_TOTPAYDAMT,2)."</th>
						<th align='right'>".number_format($row->sumTOTAR,2)."</th>
					</tr>
				";	
			}
		}
		
		$mpdf = new \Mpdf\Mpdf([
			'mode' => 'utf-8', 
			'format' => $layout,
			'margin_top' => 10, 	//default = 16
			'margin_left' => 8, 	//default = 15
			'margin_right' => 8, 	//default = 15
			'margin_bottom' => 10, 	//default = 16
			'margin_header' => 9, 	//default = 9
			'margin_footer' => 9, 	//default = 9
		]);
		
		$content = "
			<table class='wf' style='font-size:7.5pt;height:700px;border-collapse:collapse;line-height:23px;overflow:wrap;vertical-align:text-top;'>
				<tbody>
					<tr>
						<th colspan='14' style='font-size:10pt;'>รายงานรายละเอียดอายุหนี้</th>
					</tr>
					<tr>
						<td colspan='14' style='font-size:8pt;height:35px;border-bottom:0.1px solid black;text-align:center;'>".$rpcond." ณ วันที่ ".$tx[4]."</td>
					</tr>
					".$head."
					".$html."
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
			<div class='wf pf' style='".($layout == 'A4' ? 'top:1060;left:600;':'top:715;left:960;')." font-size:6pt;'>".date('d/m/').(date('Y')+543)." ".date('H:i')." หน้า {PAGENO} / {nbpg}</div>
		";
		//<div class='wf pf' style='top:1050;left:580;'>{DATE j-m-Y H:s}  {PAGENO} / {nbpg}</div>
		//$mpdf->AddPage('L');	
		$mpdf->SetHTMLHeader($head);	
		$mpdf->WriteHTML($content);	
		$mpdf->Output();
		
	}
}