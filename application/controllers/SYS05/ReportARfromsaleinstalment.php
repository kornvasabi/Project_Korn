<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//BEE+
class ReportARfromsaleinstalment extends MY_Controller {
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
						<div class='col-sm-12 col-xs-12' style='background-color:#4479aa;border:5px solid white;height:75px;text-align:center;font-size:12pt;color:white;font-weight:bold;'>	
							<br>รายงานลูกหนี้คงเหลือจากการขายผ่อน<br>
						</div>
						<div class='col-sm-8 col-xs-8 col-sm-offset-2'>
							<br>
							<div class='col-sm-4 col-xs-4'>	
								<div class='form-group'>
									สาขา
									<select id='LOCAT1' class='form-control input-sm' data-placeholder='สาขา'>
										<option value='".$this->sess["branch"]."'>".$this->sess["branch"]."</option>
									</select>
								</div>
							</div>
							<div class='col-sm-4 col-xs-4'>	
								<div class='form-group' >
									จากเลขที่สัญญา
									<select id='CONTNO1' class='form-control input-sm' data-placeholder='เลขที่สัญญา'></select>
								</div>
							</div>
							<div class='col-sm-4 col-xs-4'>	
								<div class='form-group'>
									ลูกหนี้ ณ วันที่
									<input type='text' id='ARDATE' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' placeholder='ถึงวันที่' value='".$this->today('today')."' style='font-size:10.5pt'>
								</div>
							</div>
							<div class='col-sm-4 col-xs-4'>	
								<div class='form-group' >
									พนักงานเก็บเงิน
									<select id='BILLCOL1' class='form-control input-sm' data-placeholder='พนักงานเก็บเงิน'></select>
								</div>
							</div>
							<div class='col-sm-4 col-xs-4'>	
								<div class='form-group' >
									ยี่ห้อสินค้า
									<select id='TYPE1' class='form-control input-sm' data-placeholder='ยี่ห้อสินค้า'></select>
								</div>
							</div>
							<div class='col-sm-4 col-xs-4'>	
								<div class='form-group' >
									รุ่นสินค้า
									<select id='MODEL1' class='form-control input-sm' data-placeholder='รุ่นสินค้า'></select>
								</div>
							</div>
							<div class='col-sm-4 col-xs-4'>	
								<div class='form-group' >
									สถานะสัญญา
									<select id='CONTSTAT1' class='form-control input-sm' data-placeholder='สถานะสัญญา'></select>
								</div>
							</div>
							<div class='col-sm-8 col-xs-8'>	
								<div class='form-group' >
									กลุ่มสินค้า
									<select id='GCODE1' class='form-control input-sm' data-placeholder='กลุ่มสินค้า'></select>
								</div>
							</div>
						</div>
						<div class='col-sm-8 col-xs-8 col-sm-offset-2'><br>	
							<div class='col-sm-12 col-xs-12'>	
								<div class='form-group'>
									เรียงลำดับข้อมูล
									<div class='col-sm-12 col-xs-12' style='border:0.1px dotted #d6d6d6;'>	
										<div class='col-sm-3 col-xs-3'>
											<div class='form-group'><br>
												<input type= 'radio' id='sdate' name='orderby' checked> ตามวันที่ขาย
											</div>
										</div>
										<div class='col-sm-3 col-xs-3'>
											<div class='form-group'><br>
												<input type= 'radio' id='contno' name='orderby'> เลขที่สัญญา
											</div>
										</div>
										<div class='col-sm-3 col-xs-3'>
											<div class='form-group'><br>
												<input type= 'radio' id='cuscod' name='orderby'> ตามรหัสลูกค้า
											</div>
										</div>
										<div class='col-sm-3 col-xs-3'>
											<div class='form-group'><br>
												<input type= 'radio' id='type' name='orderby'> ยี่ห้อ
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class='col-sm-8 col-xs-8 col-sm-offset-2'><br>	
							<div class='col-sm-4 col-xs-4'>	
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
							<div class='col-sm-4 col-xs-4'>	
								<div class='form-group'>
									ภาษี
									<div class='col-sm-12 col-xs-12' style='border:0.1px dotted #d6d6d6;'>	
										<div class='col-sm-12 col-xs-12'>
											<div class='form-group'>
												<br>
												<input type= 'radio' id='showvat' name='vat' checked> แสดงภาษี
												<br><br>
												<input type= 'radio' id='sumvat' name='vat'> รวมภาษี
											</div>
											
										</div>
									</div>
								</div>
							</div>
							<div class='col-sm-4 col-xs-4'>	
								<div class='form-group'>
									สถานะสินค้า
									<div class='col-sm-12 col-xs-12' style='border:0.1px dotted #d6d6d6;'>	
										<div class='col-sm-12 col-xs-12'>
											<div class='form-group'>
												<br>
												<input type= 'radio' id='NEW' name='stat'> ใหม่
												<br>
												<input type= 'radio' id='OLD' name='stat'> เก่า
												<br>
												<input type= 'radio' id='ALL' name='stat' checked> ทั้งหมด
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class='row' style='height:10%;'>
						<div class='col-sm-12 col-xs-12'><br>	
							<button id='btnt1search' class='btn btn-primary btn-sm' style='width:100%;font-size:10.5pt;'><span class='glyphicon glyphicon-search'> แสดง</span></button>
						</div>
					</div>
				</div>
			</div>
		";
		
		$html.= "<script src='".base_url('public/js/SYS05/ReportARfromsaleinstalment.js')."'></script>";
		echo $html;
	}
	
	function search(){
		$LOCAT1		= $_REQUEST["LOCAT1"];
		$CONTNO1 	= $_REQUEST["CONTNO1"];
		$BILLCOL1 	= str_replace(chr(0),'',$_REQUEST["BILLCOL1"]);
		$TYPE1 		= str_replace(chr(0),'',$_REQUEST["TYPE1"]);
		$MODEL1 	= str_replace(chr(0),'',$_REQUEST["MODEL1"]);
		$CONTSTAT1 	= str_replace(chr(0),'',$_REQUEST["CONTSTAT1"]);
		$GCODE1 	= str_replace(chr(0),'',$_REQUEST["GCODE1"]);
		$ARDATE 	= $_REQUEST["ARDATE"];
		$orderby 	= $_REQUEST["orderby"];
		$vat 		= $_REQUEST["vat"];
		$stat 		= $_REQUEST["stat"];
		
		$cond = "";
		$rpcond = "";
		if($LOCAT1 != ""){
			$cond .= " AND (A.LOCAT LIKE '%".$LOCAT1."%')";
			$rpcond .= "  สาขา ".$LOCAT1;
		}
		
		if($CONTNO1 != ""){
			$cond .= " AND (A.CONTNO LIKE '%".$CONTNO1."%' )";
			$rpcond .= "  เลขที่สัญญา ".$CONTNO1;
		}
		
		$ARDATES = "";
		if($ARDATE != ""){
			$ARDATES = $this->Convertdate(1,$ARDATE);
			$rpcond .= "  ลูกหนี้ ณ วันที่ ".$ARDATE;
		}
		
		if($BILLCOL1 != ""){
			$cond .= " AND A.BILLCOLL LIKE '%".$BILLCOL1."%'";
			$rpcond .= "  พนักงานเก็บเงิน ".$TYPE1;
		}else{
			$cond .= " AND (A.BILLCOLL LIKE '%%' OR A.BILLCOLL IS NULL)";
		}
		
		if($stat == "N"){
			$rpcond .= "  ประเภทสินค้า รถใหม่ ";
			$cond .= " AND (D.STAT LIKE '%".$stat."%')";
		}else if($stat == "O"){
			$rpcond .= "  ประเภทสินค้า รถมือสอง ";
			$cond .= " AND (D.STAT LIKE '%".$stat."%')";
		}else{
			$cond .= " AND (D.STAT LIKE '%%' OR D.STAT IS NULL )";
		}
		
		if($TYPE1 != ""){
			$cond .= " AND (D.TYPE  LIKE '%".$TYPE1."%')";
			$rpcond .= "  ยี่ห้อสินค้า ".$TYPE1;
		}else{
			$cond .= " AND (D.TYPE  LIKE '%%' OR D.TYPE IS NULL)";
		}
		
		if($MODEL1 != ""){
			$cond .= " AND (D.MODEL LIKE '%".$MODEL1."%')";
			$rpcond .= "  รุ่น ".$MODEL1;
		}else{
			$cond .= " AND (D.MODEL LIKE '%%' OR D.MODEL IS NULL)";
		}
		
		if($GCODE1 != ""){
			$cond .= " AND (D.GCODE LIKE '%".$GCODE1."%')";
			$rpcond .= "  สถานะสินค้า ".$GCODE1;
		}else{
			$cond .= " AND (D.GCODE LIKE '%%' OR D.GCODE IS NULL )";
		}
		
		if($CONTSTAT1 != ""){
			$cond .= " AND (A.CONTSTAT LIKE '%".$CONTSTAT1."%')";
			$rpcond .= "  สถานะสัญญา ".$CONTSTAT1;
		}else{
			$cond .= " AND (A.CONTSTAT LIKE '%%' OR A.CONTSTAT IS NULL)";
		}
		
		$sql = "
				IF OBJECT_ID('tempdb..#main') IS NOT NULL DROP TABLE #main
				select *
				into #main
				from(
					select LOCAT, CONTNO, CUSCOD, SNAM+NAME1+' '+NAME2 as CUSNAME, SDATE, LDATE, NPRICE, VATPRC, TOTPRC, NPAYRES, VATPRES, TOTPRES,
					NDAWN, VATDWN, TOTDWN, case when smdamt-snetp > 0 then smdamt-snetp else 0 end as KANGPAY, 
					totdwn-(snetp1+totpres)-(case when totdwn<>0 then (totdwn-(snetp1+totpres))*vatdwn/totdwn else 0 end) as KANHDOWN,
					case when totdwn <> 0 then (totdwn-(snetp1+totpres))*vatdwn/totdwn else 0 end as VKANHDOWN,
					totdwn-(snetp1+totpres) as NKANHDOWN, (d.snetp+d.snetp1)-(vpay+vdn) as PAY, vpay+vdn as VPAY,
					(d.snetp+d.snetp1) as NPAY, (totprc-(totpres+snetp1+snetp))-(vatprc-(vatpres+vdn+vpay)) as ARBALANCE,
					vatprc-(vatpres+vdn+vpay) as VARBALANCE, totprc-(totpres+snetp1+snetp) as NARBALANCE, 
					case when smv_damt-vpay>0 then smv_damt-vpay else 0 end as VKANGPAY, TYPE
					from ( 
						select DISTINCT A.LOCAT, A.CONTNO, A.TOTPRC, A.TOTPRES, A.CUSCOD, A.SDATE, B.SNAM, A.TOTDWN, A.NPRICE, A.VATPRC, A.NDAWN, A.VATDWN,
						A.NPAYRES, A.VATPRES, A.LDATE, B.NAME1, B.NAME2, D.TYPE, case when e.smdamt is null then 0 else e.smdamt end as smdamt,
						case when e.smv_damt is null then 0 else e.smv_damt end as smv_damt ,case when f.snetp is null then 0 else f.snetp end as snetp,
						case when f.snetp1 is null then 0 else f.snetp1 end as snetp1  ,case when f.vpay is null then 0 else f.vpay end as vpay,
						case when f.vdn is null then 0 else f.vdn end as vdn  
						from   {$this->MAuth->getdb('ARMAST')} 
						A left outer join {$this->MAuth->getdb('CUSTMAST')} B on a.cuscod = b.cuscod  
						left outer join (
							select strno, gcode, type, model, stat, contno from {$this->MAuth->getdb('INVTRAN')}  
							union 
							select strno, gcode, type, model, stat, contno from {$this->MAuth->getdb('HINVTRAN')}
						) as D on a.strno=d.strno and a.contno=d.contno  
						left outer join (
							select contno, locat, sum(damt) as smdamt, sum(v_damt) as smv_damt from {$this->MAuth->getdb('ARPAY')} 
							where ddate <= '".$ARDATES."' 
							group by contno, locat  
							union 
							select contno, locat, sum(damt) as smdamt, sum(v_damt) as smv_damt from {$this->MAuth->getdb('HARPAY')} 
							where ddate <= '".$ARDATES."' 
							group by contno, locat 
						) as e on a.contno=e.contno and a.locat=e.locat  
						left outer join (
							select contno, locatpay,
							sum(CASE WHEN (PAYDT <='".$ARDATES."' AND ((PAYFOR = '006') OR (PAYFOR = '007'))  AND FLAG <>'C' ) THEN  PAYAMT ELSE 0 END) AS SNETP,
							sum(CASE WHEN (PAYDT <='".$ARDATES."' AND (PAYFOR = '002')  AND FLAG <>'C' ) THEN  PAYAMT ELSE 0 END) AS SNETP1,
							sum(CASE WHEN (PAYDT <='".$ARDATES."' AND ((PAYFOR = '006') OR (PAYFOR = '007')) AND FLAG <>'C' ) THEN  PAYAMT_V ELSE 0 END) AS VPAY,
							sum(CASE WHEN (PAYDT <='".$ARDATES."' AND (PAYFOR = '002')  AND FLAG <>'C' ) THEN  PAYAMT_V ELSE 0 END) AS VDN  
							from {$this->MAuth->getdb('CHQTRAN')} 
							group by contno,locatpay
						) as f on a.contno = f.contno and a.locat = f.locatpay 
						where (A.SDATE <= '".$ARDATES."') AND A.TOTPRC > 0 AND (A.TOTPRC > A.SMPAY OR (A.TOTPRC = A.SMPAY AND A.LPAYD > '".$ARDATES."')) 
						".$cond."

						union 

						select DISTINCT A.LOCAT, A.CONTNO, A.TOTPRC, A.TOTPRES, A.CUSCOD, A.SDATE, B.SNAM, A.TOTDWN, A.NPRICE, A.VATPRC, A.NDAWN, A.VATDWN, 
						A.NPAYRES, A.VATPRES, A.LDATE, B.NAME1, B.NAME2, D.TYPE , case when e.smdamt is null then 0 else e.smdamt end as smdamt,
						case when e.smv_damt is null then 0 else e.smv_damt end as smv_damt ,case when f.snetp is null then 0 else f.snetp end as snetp,
						case when f.snetp1 is null then 0 else f.snetp1 end as snetp1, case when f.vpay is null then 0 else f.vpay end as vpay, 
						case when f.vdn is null then 0 else f.vdn end as vdn  
						from {$this->MAuth->getdb('HARMAST')} A 
						left outer join {$this->MAuth->getdb('chgar_view')} B on a.contno = b.contno and a.locat = b.locat  
						left outer join (
							select strno, gcode, type, model, stat, contno from {$this->MAuth->getdb('INVTRAN')}  
							union 
							select strno, gcode, type, model, stat, contno from {$this->MAuth->getdb('HINVTRAN')}
						) as D on a.strno=d.strno and a.contno=d.contno  
						left outer join (
							select contno, locat, sum(damt) as smdamt, sum(v_damt) as smv_damt from {$this->MAuth->getdb('ARPAY')} 
							where ddate <= '".$ARDATES."' 
							group by contno, locat  
							union 
							select contno, locat, sum(damt) as smdamt, sum(v_damt) as smv_damt from {$this->MAuth->getdb('HARPAY')} 
							where ddate <='".$ARDATES."' 
							group by contno, locat 
						) as e on a.contno = e.contno and a.locat = e.locat  
						left outer join (
							select contno, locatpay,
							sum(CASE WHEN (PAYDT <='".$ARDATES."' AND ((PAYFOR = '006') OR (PAYFOR = '007')) AND FLAG <>'C' ) THEN  PAYAMT ELSE 0 END) AS SNETP,
							sum(CASE WHEN (PAYDT <='".$ARDATES."' AND (PAYFOR = '002')  AND FLAG <>'C') THEN  PAYAMT ELSE 0 END) AS SNETP1,
							sum(CASE WHEN (PAYDT <='".$ARDATES."' AND ((PAYFOR = '006') OR (PAYFOR = '007')) AND FLAG <>'C' ) THEN  PAYAMT_V ELSE 0 END) AS VPAY,
							sum(CASE WHEN (PAYDT <='".$ARDATES."' AND (PAYFOR = '002') AND FLAG <>'C' ) THEN  PAYAMT_V ELSE 0 END) AS VDN  
							from {$this->MAuth->getdb('CHQTRAN')} 
							group by contno,locatpay
						) as f on a.contno = f.contno and a.locat = f.locatpay 
						where (A.SDATE <= '".$ARDATES."') AND A.TOTPRC > 0 AND (A.TOTPRC > A.SMPAY OR (A.TOTPRC = A.SMPAY AND A.LPAYD > '".$ARDATES."')) 
						AND b.date1 > '".$ARDATES."'  ".$cond."
					 ) as d 
				)main
		";//echo $sql; 
		$query = $this->db->query($sql);
		
		$sql = "
				select LOCAT, CONTNO, CUSCOD, CUSNAME, TYPE, SDATE, LDATE, convert(nvarchar,SDATE,112) as SDATES, 
				convert(nvarchar,LDATE,112) as LDATES,NPRICE, VATPRC, TOTPRC, NPAYRES, VATPRES, TOTPRES, NDAWN, VATDWN, 
				TOTDWN, KANGPAY, KANHDOWN, VKANHDOWN, NKANHDOWN, PAY, VPAY, NPAY, ARBALANCE, VARBALANCE, NARBALANCE, VKANGPAY
				from #main 
				order by ".$orderby."
		";//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$sql = "
				select 'รวมทั้งหมด' as Total, sum(NPRICE) as sumNPRICE, sum(VATPRC) as sumVATPRC, sum(TOTPRC) as sumTOTPRC, sum(NPAYRES) as sumNPAYRES, 
				sum(VATPRES) as sumVATPRES, sum(TOTPRES) as sumTOTPRES, sum(NDAWN) as sumNDAWN, sum(VATDWN) as sumVATDWN, sum(TOTDWN) as sumTOTDWN, 
				sum(KANGPAY) as sumKANGPAY, sum(KANHDOWN) as sumKANHDOWN, sum(VKANHDOWN) as sumVKANHDOWN, sum(NKANHDOWN) as sumNKANHDOWN, sum(PAY) as sumPAY, 
				sum(VPAY) as sumVPAY, sum(NPAY) as sumNPAY, sum(ARBALANCE) as sumARBALANCE, sum(VARBALANCE) as sumVARBALANCE, 
				sum(NARBALANCE) as sumNARBALANCE, sum(VKANGPAY) as sumVKANGPAY
				from #main 
		";
		//echo $sql; exit;
		$query2 = $this->db->query($sql);
		
		
		$head = ""; $html = ""; $head2 = "";  $report = ""; $sumreport = ""; $sumreport2 = ""; $i = 0; 
		
		if($vat == 'showvat'){
			$head = "<tr>
					<th style='display:none;'>#</th>
					<th style='vertical-align:middle;'>สาขา</th>
					<th style='vertical-align:middle;'>เลขที่สัญญา</th>
					<th style='vertical-align:middle;'>รหัสลูกค้า<br>ชื่อ - นามสกุล</th>
					<th style='vertical-align:middle;'>วันที่ขาย<br>วันดิวสุดท้าย</th> 
					<th style='text-align:right;'>มูลค่าราคาขาย<br>มูลค่าค้างดาวน์</th> 
					<th style='text-align:right;'>ภาษีขาย<br>ภาษีค้างดาวน์</th>
					<th style='text-align:right;'>ราคาขาย<br>ค้างดาวน์</th>
					<th style='text-align:right;'>มูลค่าเงินจอง<br>มูลค่าชำระ</th>
					<th style='text-align:right;'>ภาษีจอง<br>ภาษีชำระ</th>
					<th style='text-align:right;'>เงินจอง<br>ชำระแล้ว</th>
					<th style='text-align:right;'>มูลค่าเงินดาวน์<br>มูลค่าล/นคงเหลือ</th>
					<th style='text-align:right;'>ภาษีดาวน์<br>ภาษีคงเหลือ</th>
					<th style='text-align:right;'>เงินดาวน์<br>ลูกหนี้คงเหลือ</th>
					<th style='text-align:right;'>ค้างงวด<br>ภาษีค้างงวด</th>
					</tr>
			";
			
			$head2 = "<tr>
					<th style='vertical-align:middle;'>#</th>
					<th style='vertical-align:middle;'>สาขา</th>
					<th style='vertical-align:middle;'>เลขที่สัญญา</th>
					<th style='vertical-align:middle;'>รหัสลูกค้า</th>
					<th style='vertical-align:middle;'>ชื่อ - นามสกุล</th>
					<th style='vertical-align:middle;'>วันที่ขาย</th>
					<th style='vertical-align:middle;'>วันดิวสุดท้าย</th>
					<th style='vertical-align:middle;'>มูลค่าราคาขาย</th>
					<th style='vertical-align:middle;'>ภาษีขาย</th>
					<th style='vertical-align:middle;'>ราคาขาย</th>
					<th style='vertical-align:middle;'>มูลค่าเงินจอง</th>
					<th style='vertical-align:middle;'>ภาษีจอง</th>
					<th style='vertical-align:middle;'>เงินจอง</th>
					<th style='vertical-align:middle;'>มูลค่าเงินดาวน์</th>
					<th style='vertical-align:middle;'>ภาษีดาวน์</th>
					<th style='vertical-align:middle;'>เงินดาวน์</th>
					<th style='vertical-align:middle;'>มูลค่าค้างดาวน์</th>
					<th style='vertical-align:middle;'>ภาษีค้างดาวน์</th>
					<th style='vertical-align:middle;'>ค้างดาวน์</th>
					<th style='vertical-align:middle;'>มูลค่าชำระ</th>
					<th style='vertical-align:middle;'>ภาษีชำระ</th>
					<th style='vertical-align:middle;'>ชำระแล้ว</th>
					<th style='vertical-align:middle;'>มูลค่าล/นคงเหลือ</th>
					<th style='vertical-align:middle;'>ภาษีคงเหลือ</th>
					<th style='vertical-align:middle;'>ลูกหนี้คงเหลือ</th>
					<th style='vertical-align:middle;'>ค้างงวด</th>
					<th style='vertical-align:middle;'>ภาษีค้างงวด</th>
					</tr>
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
						<td>".$this->Convertdate(2,$row->SDATES)."<br>".$this->Convertdate(2,$row->LDATES)."</td>
						<td align='right'>".number_format($row->NPRICE,2)."<br>".number_format($row->KANHDOWN,2)."</td>
						<td align='right'>".number_format($row->VATPRC,2)."<br>".number_format($row->VKANHDOWN,2)."</td>
						<td align='right'>".number_format($row->TOTPRC,2)."<br>".number_format($row->NKANHDOWN,2)."</td>
						<td align='right'>".number_format($row->NPAYRES,2)."<br>".number_format($row->PAY,2)."</td>
						<td align='right'>".number_format($row->VATPRES,2)."<br>".number_format($row->VPAY,2)."</td>
						<td align='right'>".number_format($row->TOTPRES,2)."<br>".number_format($row->NPAY,2)."</td>
						<td align='right'>".number_format($row->NDAWN,2)."<br>".number_format($row->ARBALANCE,2)."</td>
						<td align='right'>".number_format($row->VATDWN,2)."<br>".number_format($row->VARBALANCE,2)."</td>
						<td align='right'>".number_format($row->TOTDWN,2)."<br>".number_format($row->NARBALANCE,2)."</td>
						<td align='right'>".number_format($row->KANGPAY,2)."<br>".number_format($row->VKANGPAY,2)."</td>
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
							<td style='mso-number-format:\"\@\";'>".$this->Convertdate(2,$row->SDATES)."</td>
							<td style='mso-number-format:\"\@\";'>".$this->Convertdate(2,$row->LDATES)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->NPRICE,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->VATPRC,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->TOTPRC,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->NPAYRES,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->VATPRES,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->TOTPRES,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->NDAWN,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->VATDWN,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->TOTDWN,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->KANHDOWN,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->VKANHDOWN,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->NKANHDOWN,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->PAY,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->VPAY,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->NPAY,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->ARBALANCE,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->VARBALANCE,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->NARBALANCE,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->KANGPAY,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->VKANGPAY,2)."</td>
						</tr>
					";	
				}
			}
			
			if($query2->row()){
				foreach($query2->result() as $row){
					$sumreport = "
						<tr>
							<th colspan='4' style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;text-align:center;'>".$row->Total."</th>
							<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;text-align:right;'>".number_format($row->sumNPRICE,2)."<br>".number_format($row->sumKANHDOWN,2)."</th>
							<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;text-align:right;'>".number_format($row->sumVATPRC,2)."<br>".number_format($row->sumVKANHDOWN,2)."</th>
							<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;text-align:right;'>".number_format($row->sumTOTPRC,2)."<br>".number_format($row->sumNKANHDOWN,2)."</th>
							<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;text-align:right;'>".number_format($row->sumNPAYRES,2)."<br>".number_format($row->sumPAY,2)."</th>
							<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;text-align:right;'>".number_format($row->sumVATPRES,2)."<br>".number_format($row->sumVPAY,2)."</th>
							<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;text-align:right;'>".number_format($row->sumTOTPRES,2)."<br>".number_format($row->sumNPAY,2)."</th>
							<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;text-align:right;'>".number_format($row->sumNDAWN,2)."<br>".number_format($row->sumARBALANCE,2)."</th>
							<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;text-align:right;'>".number_format($row->sumVATDWN,2)."<br>".number_format($row->sumVARBALANCE,2)."</th>
							<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;text-align:right;'>".number_format($row->sumTOTDWN,2)."<br>".number_format($row->sumNARBALANCE,2)."</th>
							<th style='border:0px;text-align:right;'>".number_format($row->sumKANGPAY,2)."<br>".number_format($row->sumVKANGPAY,2)."</th>
						</tr>
					";	
				}
			}
			
			if($query2->row()){
				foreach($query2->result() as $row){
					$sumreport2 = "
						<tr class='trow'>
							<td style='mso-number-format:\"\@\";text-align:center;' colspan='7'>".$row->Total."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumNPRICE,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumVATPRC,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumTOTPRC,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumNPAYRES,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumVATPRES,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumTOTPRES,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumNDAWN,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumVATDWN,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumTOTDWN,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumKANHDOWN,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumVKANHDOWN,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumNKANHDOWN,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumPAY,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumVPAY,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumNPAY,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumARBALANCE,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumVARBALANCE,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumNARBALANCE,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumKANGPAY,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumVKANGPAY,2)."</td>
						</tr>
					";	
				}
			}
		}else{
			$head = "<tr>
					<th style='display:none;'>#</th>
					<th style='vertical-align:middle;'>สาขา</th>
					<th style='vertical-align:middle;'>เลขที่สัญญา</th>
					<th style='vertical-align:middle;'>ชื่อ - นามสกุล</th>
					<th style='vertical-align:middle;'>วันที่ขาย</th> 
					<th style='vertical-align:middle;'>วันดิวสุดท้าย</th> 
					<th style='text-align:right;'>ราคาขาย</th>
					<th style='text-align:right;'>ค้างดาวน์</th>
					<th style='text-align:right;'>เงินจอง</th>
					<th style='text-align:right;'>ชำระแล้ว</th>
					<th style='text-align:right;'>เงินดาวน์</th>
					<th style='text-align:right;'>ลูกหนี้คงเหลือ</th>
					<th style='text-align:right;'>ค้างงวด</th>
					<th style='text-align:right;'>ภาษีค้างงวด</th>
					</tr>
			";
			
			$head2 = "<tr>
					<th style='vertical-align:middle;'>#</th>
					<th style='vertical-align:middle;'>สาขา</th>
					<th style='vertical-align:middle;'>เลขที่สัญญา</th>
					<th style='vertical-align:middle;'>ชื่อ - นามสกุล</th>
					<th style='vertical-align:middle;'>วันที่ขาย</th> 
					<th style='vertical-align:middle;'>วันดิวสุดท้าย</th> 
					<th style='text-align:right;'>ราคาขาย</th>
					<th style='text-align:right;'>ค้างดาวน์</th>
					<th style='text-align:right;'>เงินจอง</th>
					<th style='text-align:right;'>ชำระแล้ว</th>
					<th style='text-align:right;'>เงินดาวน์</th>
					<th style='text-align:right;'>ลูกหนี้คงเหลือ</th>
					<th style='text-align:right;'>ค้างงวด</th>
					<th style='text-align:right;'>ภาษีค้างงวด</th>
					</tr>
			";
			
			$NRow = 1;
			if($query->row()){
				foreach($query->result() as $row){$i++;
					$html .= "
						<tr class='trow' seq=".$NRow.">
						<td seq=".$NRow++." style='display:none;'></td>
						<td>".$row->LOCAT."</td>
						<td>".$row->CONTNO."</td>
						<td>".$row->CUSNAME."</td>
						<td>".$this->Convertdate(2,$row->SDATES)."</td>
						<td>".$this->Convertdate(2,$row->LDATES)."</td>
						<td align='right'>".number_format($row->TOTPRC,2)."</td>
						<td align='right'>".number_format($row->NKANHDOWN,2)."</td>
						<td align='right'>".number_format($row->TOTPRES,2)."</td>
						<td align='right'>".number_format($row->NPAY,2)."</td>
						<td align='right'>".number_format($row->TOTDWN,2)."</td>
						<td align='right'>".number_format($row->NARBALANCE,2)."</td>
						<td align='right'>".number_format($row->KANGPAY,2)."</td>
						<td align='right'>".number_format($row->VKANGPAY,2)."</td>
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
							<td style='mso-number-format:\"\@\";'>".$row->CUSNAME."</td>
							<td style='mso-number-format:\"\@\";'>".$this->Convertdate(2,$row->SDATES)."</td>
							<td style='mso-number-format:\"\@\";'>".$this->Convertdate(2,$row->LDATES)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->TOTPRC,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->NKANHDOWN,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->TOTPRES,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->NPAY,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->TOTDWN,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->NARBALANCE,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->KANGPAY,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->VKANGPAY,2)."</td>
						</tr>
					";	
				}
			}
			
			if($query2->row()){
				foreach($query2->result() as $row){
					$sumreport = "
						<tr>
							<th colspan='5' style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;text-align:center;'>".$row->Total."</th>
							<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;text-align:right;'>".number_format($row->sumTOTPRC,2)."</th>
							<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;text-align:right;'>".number_format($row->sumNKANHDOWN,2)."</th>
							<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;text-align:right;'>".number_format($row->sumTOTPRES,2)."</th>
							<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;text-align:right;'>".number_format($row->sumNPAY,2)."</th>
							<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;text-align:right;'>".number_format($row->sumTOTDWN,2)."</th>
							<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;text-align:right;'>".number_format($row->sumNARBALANCE,2)."</th>
							<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;text-align:right;'>".number_format($row->sumKANGPAY,2)."</th>
							<th style='border:0px;text-align:right;'>".number_format($row->sumVKANGPAY,2)."</th>
						</tr>
					";	
				}
			}
			
			if($query2->row()){
				foreach($query2->result() as $row){
					$sumreport2 = "
						<tr class='trow'>
							<td style='mso-number-format:\"\@\";text-align:center;' colspan='6'>".$row->Total."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumTOTPRC,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumNKANHDOWN,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumTOTPRES,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumNPAY,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumTOTDWN,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumNARBALANCE,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumKANGPAY,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumVKANGPAY,2)."</td>
						</tr>
					";	
				}
			}
		}
		
		if($i>0){
			$html = "
				<div id='table-fixed-ReportARfromsaleinstalment' class='col-sm-12' style='height:100%;width:100%;overflow:auto;font-size:8pt;'>
					<table id='table-ReportARfromsaleinstalment' style='background-color:white;' class='col-sm-12 display table table-bordered' cellspacing='0' width='calc(100% - 1px)'>
						<thead>
						<tr>
							<th colspan=".($vat == 'showvat' ? '14' : '13')." style='font-size:12pt;border:0px;text-align:center;'>รายงานลูกหนี้คงเหลือจากการขายผ่อน</th>
						</tr>
						<tr>
							<td colspan=".($vat == 'showvat' ? '14' : '13')." style='border-bottom:1px solid #ddd;text-align:center;'>".$rpcond."  ออกรายงาน ณ วันที่ ".$this->today('today')."</td>
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
			<div id='table-fixed-ReportARfromsaleinstalment2' class='col-sm-12' style='height:100%;width:100%;overflow:auto;'>
				<table id='table-ReportARfromsaleinstalment2' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='100%'>
					<thead>
						<tr>
							<th colspan=".($vat == 'showvat' ? '27' : '14')." style='font-size:12pt;border:0px;text-align:center;'>รายงานลูกหนี้คงเหลือจากการขายผ่อน</th>
						</tr>
						<tr>
							<td colspan=".($vat == 'showvat' ? '27' : '14')." style='border:0px;text-align:center;'>".$rpcond." ออกรายงาน ณ วันที่ ".$this->today('today')."</td>
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
		$data[] = 	urlencode($_REQUEST["LOCAT1"].'||'.$_REQUEST["CONTNO1"].'||'.$_REQUEST["BILLCOL1"].'||'.$_REQUEST["TYPE1"].'||'.$_REQUEST["MODEL1"]
					.'||'.$_REQUEST["CONTSTAT1"].'||'.$_REQUEST["GCODE1"].'||'.$_REQUEST["ARDATE"].'||'.$_REQUEST["orderby"].'||'.$_REQUEST["vat"]
					.'||'.$_REQUEST["stat"].'||'.$_REQUEST["layout"]);
		echo json_encode($this->generateData($data,"encode"));
	}
	
	function pdf(){
		$data = array();
		$data[] = $_GET["condpdf"];
		$arrs = $this->generateData($data,"decode");
		$arrs[0] = urldecode($arrs[0]);

		$tx = explode("||",$arrs[0]);
		$LOCAT1 	= $tx[0];
		$CONTNO1 	= $tx[1];
		$BILLCOL1 	= str_replace(chr(0),'',$tx[2]);
		$TYPE1 		= str_replace(chr(0),'',$tx[3]);
		$MODEL1 	= str_replace(chr(0),'',$tx[4]);
		$CONTSTAT1 	= str_replace(chr(0),'',$tx[5]);
		$GCODE1 	= str_replace(chr(0),'',$tx[6]);
		$ARDATE 	= $tx[7];
		$orderby 	= $tx[8];
		$vat 		= $tx[9];
		$stat 		= $tx[10];
		$layout 	= $tx[11];

		$cond = ""; $rpcond = "";

		if($LOCAT1 != ""){
			$cond .= " AND (A.LOCAT LIKE '%".$LOCAT1."%')";
			$rpcond .= "  สาขา ".$LOCAT1;
		}
		
		if($CONTNO1 != ""){
			$cond .= " AND (A.CONTNO LIKE '%".$CONTNO1."%' )";
			$rpcond .= "  เลขที่สัญญา ".$CONTNO1;
		}
		
		$ARDATES = "";
		if($ARDATE != ""){
			$ARDATES = $this->Convertdate(1,$ARDATE);
			$rpcond .= "  ลูกหนี้ ณ วันที่ ".$ARDATE;
		}
		
		if($BILLCOL1 != ""){
			$cond .= " AND A.BILLCOLL LIKE '%".$BILLCOL1."%'";
			$rpcond .= "  พนักงานเก็บเงิน ".$TYPE1;
		}else{
			$cond .= " AND (A.BILLCOLL LIKE '%%' OR A.BILLCOLL IS NULL)";
		}
		
		if($stat == "N"){
			$rpcond .= "  ประเภทสินค้า รถใหม่ ";
			$cond .= " AND (D.STAT LIKE '%".$stat."%')";
		}else if($stat == "O"){
			$rpcond .= "  ประเภทสินค้า รถมือสอง ";
			$cond .= " AND (D.STAT LIKE '%".$stat."%')";
		}else{
			$cond .= " AND (D.STAT LIKE '%%' OR D.STAT IS NULL )";
		}
		
		if($TYPE1 != ""){
			$cond .= " AND (D.TYPE  LIKE '%".$TYPE1."%')";
			$rpcond .= "  ยี่ห้อสินค้า ".$TYPE1;
		}else{
			$cond .= " AND (D.TYPE  LIKE '%%' OR D.TYPE IS NULL)";
		}
		
		if($MODEL1 != ""){
			$cond .= " AND (D.MODEL LIKE '%".$MODEL1."%')";
			$rpcond .= "  รุ่น ".$MODEL1;
		}else{
			$cond .= " AND (D.MODEL LIKE '%%' OR D.MODEL IS NULL)";
		}
		
		if($GCODE1 != ""){
			$cond .= " AND (D.GCODE LIKE '%".$GCODE1."%')";
			$rpcond .= "  สถานะสินค้า ".$GCODE1;
		}else{
			$cond .= " AND (D.GCODE LIKE '%%' OR D.GCODE IS NULL )";
		}
		
		if($CONTSTAT1 != ""){
			$cond .= " AND (A.CONTSTAT LIKE '%".$CONTSTAT1."%')";
			$rpcond .= "  สถานะสัญญา ".$CONTSTAT1;
		}else{
			$cond .= " AND (A.CONTSTAT LIKE '%%' OR A.CONTSTAT IS NULL)";
		}
		
		$sql = "
				IF OBJECT_ID('tempdb..#main') IS NOT NULL DROP TABLE #main
				select *
				into #main
				from(
					select LOCAT, CONTNO, CUSCOD, SNAM+NAME1+' '+NAME2 as CUSNAME, SDATE, LDATE, NPRICE, VATPRC, TOTPRC, NPAYRES, VATPRES, TOTPRES,
					NDAWN, VATDWN, TOTDWN, case when smdamt-snetp > 0 then smdamt-snetp else 0 end as KANGPAY, 
					totdwn-(snetp1+totpres)-(case when totdwn<>0 then (totdwn-(snetp1+totpres))*vatdwn/totdwn else 0 end) as KANHDOWN,
					case when totdwn <> 0 then (totdwn-(snetp1+totpres))*vatdwn/totdwn else 0 end as VKANHDOWN,
					totdwn-(snetp1+totpres) as NKANHDOWN, (d.snetp+d.snetp1)-(vpay+vdn) as PAY, vpay+vdn as VPAY,
					(d.snetp+d.snetp1) as NPAY, (totprc-(totpres+snetp1+snetp))-(vatprc-(vatpres+vdn+vpay)) as ARBALANCE,
					vatprc-(vatpres+vdn+vpay) as VARBALANCE, totprc-(totpres+snetp1+snetp) as NARBALANCE, 
					case when smv_damt-vpay>0 then smv_damt-vpay else 0 end as VKANGPAY, TYPE
					from ( 
						select DISTINCT A.LOCAT, A.CONTNO, A.TOTPRC, A.TOTPRES, A.CUSCOD, A.SDATE, B.SNAM, A.TOTDWN, A.NPRICE, A.VATPRC, A.NDAWN, A.VATDWN,
						A.NPAYRES, A.VATPRES, A.LDATE, B.NAME1, B.NAME2, D.TYPE, case when e.smdamt is null then 0 else e.smdamt end as smdamt,
						case when e.smv_damt is null then 0 else e.smv_damt end as smv_damt ,case when f.snetp is null then 0 else f.snetp end as snetp,
						case when f.snetp1 is null then 0 else f.snetp1 end as snetp1  ,case when f.vpay is null then 0 else f.vpay end as vpay,
						case when f.vdn is null then 0 else f.vdn end as vdn  
						from   {$this->MAuth->getdb('ARMAST')} 
						A left outer join {$this->MAuth->getdb('CUSTMAST')} B on a.cuscod = b.cuscod  
						left outer join (
							select strno, gcode, type, model, stat, contno from {$this->MAuth->getdb('INVTRAN')}  
							union 
							select strno, gcode, type, model, stat, contno from {$this->MAuth->getdb('HINVTRAN')}
						) as D on a.strno=d.strno and a.contno=d.contno  
						left outer join (
							select contno, locat, sum(damt) as smdamt, sum(v_damt) as smv_damt from {$this->MAuth->getdb('ARPAY')} 
							where ddate <= '".$ARDATES."' 
							group by contno, locat  
							union 
							select contno, locat, sum(damt) as smdamt, sum(v_damt) as smv_damt from {$this->MAuth->getdb('HARPAY')} 
							where ddate <= '".$ARDATES."' 
							group by contno, locat 
						) as e on a.contno=e.contno and a.locat=e.locat  
						left outer join (
							select contno, locatpay,
							sum(CASE WHEN (PAYDT <='".$ARDATES."' AND ((PAYFOR = '006') OR (PAYFOR = '007'))  AND FLAG <>'C' ) THEN  PAYAMT ELSE 0 END) AS SNETP,
							sum(CASE WHEN (PAYDT <='".$ARDATES."' AND (PAYFOR = '002')  AND FLAG <>'C' ) THEN  PAYAMT ELSE 0 END) AS SNETP1,
							sum(CASE WHEN (PAYDT <='".$ARDATES."' AND ((PAYFOR = '006') OR (PAYFOR = '007')) AND FLAG <>'C' ) THEN  PAYAMT_V ELSE 0 END) AS VPAY,
							sum(CASE WHEN (PAYDT <='".$ARDATES."' AND (PAYFOR = '002')  AND FLAG <>'C' ) THEN  PAYAMT_V ELSE 0 END) AS VDN  
							from {$this->MAuth->getdb('CHQTRAN')} 
							group by contno,locatpay
						) as f on a.contno = f.contno and a.locat = f.locatpay 
						where (A.SDATE <= '".$ARDATES."') AND A.TOTPRC > 0 AND (A.TOTPRC > A.SMPAY OR (A.TOTPRC = A.SMPAY AND A.LPAYD > '".$ARDATES."')) 
						".$cond."

						union 

						select DISTINCT A.LOCAT, A.CONTNO, A.TOTPRC, A.TOTPRES, A.CUSCOD, A.SDATE, B.SNAM, A.TOTDWN, A.NPRICE, A.VATPRC, A.NDAWN, A.VATDWN, 
						A.NPAYRES, A.VATPRES, A.LDATE, B.NAME1, B.NAME2, D.TYPE , case when e.smdamt is null then 0 else e.smdamt end as smdamt,
						case when e.smv_damt is null then 0 else e.smv_damt end as smv_damt ,case when f.snetp is null then 0 else f.snetp end as snetp,
						case when f.snetp1 is null then 0 else f.snetp1 end as snetp1, case when f.vpay is null then 0 else f.vpay end as vpay, 
						case when f.vdn is null then 0 else f.vdn end as vdn  
						from {$this->MAuth->getdb('HARMAST')} A 
						left outer join {$this->MAuth->getdb('chgar_view')} B on a.contno = b.contno and a.locat = b.locat  
						left outer join (
							select strno, gcode, type, model, stat, contno from {$this->MAuth->getdb('INVTRAN')}  
							union 
							select strno, gcode, type, model, stat, contno from {$this->MAuth->getdb('HINVTRAN')}
						) as D on a.strno=d.strno and a.contno=d.contno  
						left outer join (
							select contno, locat, sum(damt) as smdamt, sum(v_damt) as smv_damt from {$this->MAuth->getdb('ARPAY')} 
							where ddate <= '".$ARDATES."' 
							group by contno, locat  
							union 
							select contno, locat, sum(damt) as smdamt, sum(v_damt) as smv_damt from {$this->MAuth->getdb('HARPAY')} 
							where ddate <='".$ARDATES."' 
							group by contno, locat 
						) as e on a.contno = e.contno and a.locat = e.locat  
						left outer join (
							select contno, locatpay,
							sum(CASE WHEN (PAYDT <='".$ARDATES."' AND ((PAYFOR = '006') OR (PAYFOR = '007')) AND FLAG <>'C' ) THEN  PAYAMT ELSE 0 END) AS SNETP,
							sum(CASE WHEN (PAYDT <='".$ARDATES."' AND (PAYFOR = '002')  AND FLAG <>'C') THEN  PAYAMT ELSE 0 END) AS SNETP1,
							sum(CASE WHEN (PAYDT <='".$ARDATES."' AND ((PAYFOR = '006') OR (PAYFOR = '007')) AND FLAG <>'C' ) THEN  PAYAMT_V ELSE 0 END) AS VPAY,
							sum(CASE WHEN (PAYDT <='".$ARDATES."' AND (PAYFOR = '002') AND FLAG <>'C' ) THEN  PAYAMT_V ELSE 0 END) AS VDN  
							from {$this->MAuth->getdb('CHQTRAN')} 
							group by contno,locatpay
						) as f on a.contno = f.contno and a.locat = f.locatpay 
						where (A.SDATE <= '".$ARDATES."') AND A.TOTPRC > 0 AND (A.TOTPRC > A.SMPAY OR (A.TOTPRC = A.SMPAY AND A.LPAYD > '".$ARDATES."')) 
						AND b.date1 > '".$ARDATES."'  ".$cond."
					 ) as d 
				)main
		";//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$sql = "
				select LOCAT, CONTNO, CUSCOD, CUSNAME, TYPE, SDATE, LDATE, convert(nvarchar,SDATE,112) as SDATES, 
				convert(nvarchar,LDATE,112) as LDATES,NPRICE, VATPRC, TOTPRC, NPAYRES, VATPRES, TOTPRES, NDAWN, VATDWN, 
				TOTDWN, KANGPAY, KANHDOWN, VKANHDOWN, NKANHDOWN, PAY, VPAY, NPAY, ARBALANCE, VARBALANCE, NARBALANCE, VKANGPAY
				from #main 
				order by ".$orderby."
		";//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$sql = "
				select 'รวมทั้งหมด' as Total, sum(NPRICE) as sumNPRICE, sum(VATPRC) as sumVATPRC, sum(TOTPRC) as sumTOTPRC, sum(NPAYRES) as sumNPAYRES, 
				sum(VATPRES) as sumVATPRES, sum(TOTPRES) as sumTOTPRES, sum(NDAWN) as sumNDAWN, sum(VATDWN) as sumVATDWN, sum(TOTDWN) as sumTOTDWN, 
				sum(KANGPAY) as sumKANGPAY, sum(KANHDOWN) as sumKANHDOWN, sum(VKANHDOWN) as sumVKANHDOWN, sum(NKANHDOWN) as sumNKANHDOWN, sum(PAY) as sumPAY, 
				sum(VPAY) as sumVPAY, sum(NPAY) as sumNPAY, sum(ARBALANCE) as sumARBALANCE, sum(VARBALANCE) as sumVARBALANCE, 
				sum(NARBALANCE) as sumNARBALANCE, sum(VKANGPAY) as sumVKANGPAY
				from #main 
		";
		//echo $sql; exit;
		$query2 = $this->db->query($sql);
		
		$head = ""; $html = ""; $i=0; 
		
		if($vat == 'showvat'){
			$head = "
					<tr>
						<th style='border-bottom:0.1px solid black;text-align:left;'>#</th>
						<th style='border-bottom:0.1px solid black;text-align:left;'>สาขา</th>
						<th style='border-bottom:0.1px solid black;text-align:left;'>เลขที่สัญญา</th>
						<th style='border-bottom:0.1px solid black;text-align:left;'>รหัสลูกค้า<br>ชื่อ - นามสกุล</th>
						<th style='border-bottom:0.1px solid black;text-align:left;'>วันที่ขาย<br>วันดิวสุดท้าย</th> 
						<th style='border-bottom:0.1px solid black;text-align:right;'>มูลค่าราคาขาย<br>มูลค่าค้างดาวน์</th> 
						<th style='border-bottom:0.1px solid black;text-align:right;'>ภาษีขาย<br>ภาษีค้างดาวน์</th>
						<th style='border-bottom:0.1px solid black;text-align:right;'>ราคาขาย<br>ค้างดาวน์</th>
						<th style='border-bottom:0.1px solid black;text-align:right;'>มูลค่าเงินจอง<br>มูลค่าชำระ</th>
						<th style='border-bottom:0.1px solid black;text-align:right;'>ภาษีจอง<br>ภาษีชำระ</th>
						<th style='border-bottom:0.1px solid black;text-align:right;'>เงินจอง<br>ชำระแล้ว</th>
						<th style='border-bottom:0.1px solid black;text-align:right;'>มูลค่าเงินดาวน์<br>มูลค่าล/นคงเหลือ</th>
						<th style='border-bottom:0.1px solid black;text-align:right;'>ภาษีดาวน์<br>ภาษีคงเหลือ</th>
						<th style='border-bottom:0.1px solid black;text-align:right;'>เงินดาวน์<br>ลูกหนี้คงเหลือ</th>
						<th style='border-bottom:0.1px solid black;text-align:right;'>ค้างงวด<br>ภาษีค้างงวด</th>
					</tr>
			";
			
			$No = 1;
			if($query->row()){
				foreach($query->result() as $row){	
					$html .= "
						<tr class='trow' seq=".$No.">
							<td style='width:25px;'>".$No++."</td>
							<td style='width:40px;'>".$row->LOCAT."</td>
							<td style='width:75px;'>".$row->CONTNO."</td>
							<td style='width:170px;'>".$row->CUSCOD."<br>".$row->CUSNAME."</td>
							<td style='width:65px;'>".$this->Convertdate(2,$row->SDATES)."<br>".$this->Convertdate(2,$row->LDATES)."</td>
							<td style='width:75px;' align='right'>".number_format($row->NPRICE,2)."<br>".number_format($row->KANHDOWN,2)."</td>
							<td style='width:70px;' align='right'>".number_format($row->VATPRC,2)."<br>".number_format($row->VKANHDOWN,2)."</td>
							<td style='width:70px;' align='right'>".number_format($row->TOTPRC,2)."<br>".number_format($row->NKANHDOWN,2)."</td>
							<td style='width:70px;' align='right'>".number_format($row->NPAYRES,2)."<br>".number_format($row->PAY,2)."</td>
							<td style='width:70px;' align='right'>".number_format($row->VATPRES,2)."<br>".number_format($row->VPAY,2)."</td>
							<td style='width:70px;' align='right'>".number_format($row->TOTPRES,2)."<br>".number_format($row->NPAY,2)."</td>
							<td style='width:88px;' align='right'>".number_format($row->NDAWN,2)."<br>".number_format($row->ARBALANCE,2)."</td>
							<td style='width:70px;' align='right'>".number_format($row->VATDWN,2)."<br>".number_format($row->VARBALANCE,2)."</td>
							<td style='width:70px;' align='right'>".number_format($row->TOTDWN,2)."<br>".number_format($row->NARBALANCE,2)."</td>
							<td style='width:70px;' align='right'>".number_format($row->KANGPAY,2)."<br>".number_format($row->VKANGPAY,2)."</td>
						</tr>
					";	
				}
			}
			
			if($query2->row()){
				foreach($query2->result() as $row){	
					$html .= "
						<tr class='trow bor' style='background-color:#ebebeb;'>
							<td colspan='5' style='text-align:center;vertical-align:middle;'>".$row->Total."</td>
							<td align='right'>".number_format($row->sumNPRICE,2)."<br>".number_format($row->sumKANHDOWN,2)."</td>
							<td align='right'>".number_format($row->sumVATPRC,2)."<br>".number_format($row->sumVKANHDOWN,2)."</td>
							<td align='right'>".number_format($row->sumTOTPRC,2)."<br>".number_format($row->sumNKANHDOWN,2)."</td>
							<td align='right'>".number_format($row->sumNPAYRES,2)."<br>".number_format($row->sumPAY,2)."</td>
							<td align='right'>".number_format($row->sumVATPRES,2)."<br>".number_format($row->sumVPAY,2)."</td>
							<td align='right'>".number_format($row->sumTOTPRES,2)."<br>".number_format($row->sumNPAY,2)."</td>
							<td align='right'>".number_format($row->sumNDAWN,2)."<br>".number_format($row->sumARBALANCE,2)."</td>
							<td align='right'>".number_format($row->sumVATDWN,2)."<br>".number_format($row->sumVARBALANCE,2)."</td>
							<td align='right'>".number_format($row->sumTOTDWN,2)."<br>".number_format($row->sumNARBALANCE,2)."</td>
							<td align='right'>".number_format($row->sumKANGPAY,2)."<br>".number_format($row->sumVKANGPAY,2)."</td>
						</tr>
					";	
				}
			}
		}else{
			$head = "<tr>
					<th style='border-bottom:0.1px solid black;text-align:left;'>#</th>
					<th style='border-bottom:0.1px solid black;text-align:left;'>สาขา</th>
					<th style='border-bottom:0.1px solid black;text-align:left;'>เลขที่สัญญา</th>
					<th style='border-bottom:0.1px solid black;text-align:left;'>ชื่อ - นามสกุล</th>
					<th style='border-bottom:0.1px solid black;text-align:left;'>วันที่ขาย</th> 
					<th style='border-bottom:0.1px solid black;text-align:left;'>วันดิวสุดท้าย</th> 
					<th style='border-bottom:0.1px solid black;text-align:right;'>ราคาขาย</th>
					<th style='border-bottom:0.1px solid black;text-align:right;'>ค้างดาวน์</th>
					<th style='border-bottom:0.1px solid black;text-align:right;'>เงินจอง</th>
					<th style='border-bottom:0.1px solid black;text-align:right;'>ชำระแล้ว</th>
					<th style='border-bottom:0.1px solid black;text-align:right;'>เงินดาวน์</th>
					<th style='border-bottom:0.1px solid black;text-align:right;'>ลูกหนี้คงเหลือ</th>
					<th style='border-bottom:0.1px solid black;text-align:right;'>ค้างงวด</th>
					<th style='border-bottom:0.1px solid black;text-align:right;'>ภาษีค้างงวด</th>
					</tr>
			";
			
			$No = 1;
			if($query->row()){
				foreach($query->result() as $row){	
					$html .= "
						<tr class='trow' seq=".$No.">
							<td style='width:25px;'>".$No++."</td>
							<td style='width:40px;'>".$row->LOCAT."</td>
							<td style='width:75px;'>".$row->CONTNO."</td>
							<td style='width:180px;'>".$row->CUSNAME."</td>
							<td style='width:65px;'>".$this->Convertdate(2,$row->SDATES)."</td>
							<td style='width:65px;'>".$this->Convertdate(2,$row->LDATES)."</td>
							<td style='width:70px;text-align:right;'>".number_format($row->TOTPRC,2)."</td>
							<td style='width:70px;text-align:right;'>".number_format($row->NKANHDOWN,2)."</td>
							<td style='width:70px;text-align:right;'>".number_format($row->TOTPRES,2)."</td>
							<td style='width:70px;text-align:right;'>".number_format($row->NPAY,2)."</td>
							<td style='width:70px;text-align:right;'>".number_format($row->TOTDWN,2)."</td>
							<td style='width:70px;text-align:right;'>".number_format($row->NARBALANCE,2)."</td>
							<td style='width:70px;text-align:right;'>".number_format($row->KANGPAY,2)."</td>
							<td style='width:70px;text-align:right;'>".number_format($row->VKANGPAY,2)."</td>
						</tr>
					";	
				}
			}
			
			if($query2->row()){
				foreach($query2->result() as $row){	
					$html .= "
						<tr class='trow bor' style='background-color:#ebebeb;'>
							<td colspan='6' style='text-align:center;vertical-align:middle;'>".$row->Total."</td>
							<td style='text-align:right;'>".number_format($row->sumTOTPRC,2)."</td>
							<td style='text-align:right;'>".number_format($row->sumNKANHDOWN,2)."</td>
							<td style='text-align:right;'>".number_format($row->sumTOTPRES,2)."</td>
							<td style='text-align:right;'>".number_format($row->sumNPAY,2)."</td>
							<td style='text-align:right;'>".number_format($row->sumTOTDWN,2)."</td>
							<td style='text-align:right;'>".number_format($row->sumNARBALANCE,2)."</td>
							<td style='text-align:right;'>".number_format($row->sumKANGPAY,2)."</td>
							<td style='text-align:right;'>".number_format($row->sumVKANGPAY,2)."</td>
						</tr>
					";	
				}
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
						<th colspan=".($vat == 'showvat' ? '15' : '14')." style='font-size:10pt;'>รายงานลูกหนี้คงเหลือจากการขายส่งไฟแนนซ์</th>
					</tr>
					<tr>
						<td colspan=".($vat == 'showvat' ? '15' : '14')." style='font-size:8pt;height:35px;border-bottom:0.1px solid black;text-align:center;'>".$rpcond."</td>
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