<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//BEE+
class ReportARfromsalefinance extends MY_Controller {
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
							<br>รายงานลูกหนี้คงเหลือจากการขายส่งไฟแนนซ์<br>
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
								<div class='form-group'>
									จากวันที่ขาย
									<input type='text' id='FRMDATE' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' placeholder='ถึงวันที่' value='".$this->today('today')."' style='font-size:10.5pt'>
								</div>
							</div>
							<div class='col-sm-4 col-xs-4'>	
								<div class='form-group'>
									ถึงวันที่ขาย
									<input type='text' id='TODATE' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' placeholder='ถึงวันที่' value='".$this->today('today')."' style='font-size:10.5pt'>
								</div>
							</div>
							<div class='col-sm-4 col-xs-4'>	
								<div class='form-group' >
									กลุ่มสินค้า
									<select id='GCODE1' class='form-control input-sm' data-placeholder='กลุ่มสินค้า'></select>
								</div>
							</div>
							<div class='col-sm-4 col-xs-4'>	
								<div class='form-group' >
									บริษัทไฟแนนซ์
									<select id='FINCODE1' class='form-control input-sm' data-placeholder='บริษัทไฟแนนซ์'></select>
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
						</div>
						<div class='col-sm-8 col-xs-8 col-sm-offset-2'><br>	
							<div class='col-sm-12 col-xs-12'>	
								<div class='form-group'>
									เรียงลำดับข้อมูล
									<div class='col-sm-12 col-xs-12' style='border:0.1px dotted #d6d6d6;'>	
										<div class='col-sm-4 col-xs-4'>
											<div class='form-group'><br>
												<input type= 'radio' id='sdate' name='orderby' checked> ตามวันที่ขาย
											</div>
										</div>
										<div class='col-sm-4 col-xs-4'>
											<div class='form-group'><br>
												<input type= 'radio' id='contno' name='orderby'> เลขที่สัญญา
											</div>
										</div>
										<div class='col-sm-4 col-xs-4'>
											<div class='form-group'><br>
												<input type= 'radio' id='cuscod' name='orderby'> ตามรหัสลูกค้า
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
		
		$html.= "<script src='".base_url('public/js/SYS05/ReportARfromsalefinance.js')."'></script>";
		echo $html;
	}
	
	function search(){
		$LOCAT1		= $_REQUEST["LOCAT1"];
		$CONTNO1 	= $_REQUEST["CONTNO1"];
		$GCODE1 	= str_replace(chr(0),'',$_REQUEST["GCODE1"]);
		$FINCODE1 	= str_replace(chr(0),'',$_REQUEST["FINCODE1"]);
		$TYPE1 		= str_replace(chr(0),'',$_REQUEST["TYPE1"]);
		$MODEL1 	= str_replace(chr(0),'',$_REQUEST["MODEL1"]);
		$GCODE1 	= str_replace(chr(0),'',$_REQUEST["GCODE1"]);
		$ARDATE 	= $_REQUEST["ARDATE"];
		$FRMDATE 	= $this->Convertdate(1,$_REQUEST["FRMDATE"]);
		$TODATE 	= $this->Convertdate(1,$_REQUEST["TODATE"]);
		$orderby 	= $_REQUEST["orderby"];
		$vat 		= $_REQUEST["vat"];
		$stat 		= $_REQUEST["stat"];
		
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
		
		if($FINCODE1 != ""){
			$cond .= " AND A.FINCOD LIKE '%".$FINCODE1."%'";
			$rpcond .= "  รหัสบริษัทไฟแนนซ์ ".$FINCODE1;
		}else{
			$cond .= " AND (A.FINCOD LIKE '%%' OR A.FINCOD IS NULL ) ";
		}
		
		if($stat == "N"){
			$rpcond .= "  ประเภทสินค้า รถใหม่ ";
			$cond .= " AND (C.STAT LIKE '%".$stat."%')";
		}else if($stat == "O"){
			$rpcond .= "  ประเภทสินค้า รถมือสอง ";
			$cond .= " AND (C.STAT LIKE '%".$stat."%')";
		}else{
			$cond .= " AND (C.STAT LIKE '%%' OR C.STAT IS NULL )";
		}
		
		if($GCODE1 != ""){
			$cond .= " AND (C.GCODE LIKE '%".$GCODE1."%')";
			$rpcond .= "  สถานะสินค้า ".$GCODE1;
		}else{
			$cond .= " AND (C.GCODE LIKE '%%' OR C.GCODE IS NULL )";
		}
		
		if($TYPE1 != ""){
			$cond .= " AND (C.TYPE  LIKE '%".$TYPE1."%')";
			$rpcond .= "  ยี่ห้อสินค้า ".$TYPE1;
		}else{
			$cond .= " AND (C.TYPE  LIKE '%%' OR C.TYPE IS NULL)";
		}
		
		if($MODEL1 != ""){
			$cond .= " AND (C.MODEL LIKE '%".$MODEL1."%')";
			$rpcond .= "  รุ่น ".$MODEL1;
		}else{
			$cond .= " AND (C.MODEL LIKE '%%' OR C.MODEL IS NULL)";
		}
		
		$sql = "
				IF OBJECT_ID('tempdb..#main') IS NOT NULL DROP TABLE #main
				select *
				into #main
				from(
					select A.LOCAT, A.CONTNO, A.SDATE, convert(nvarchar,A.SDATE,112) as SDATES, A.CUSCOD, B.SNAM+B.NAME1+' '+B.NAME2 as CUSNAME, 
					A.NPRICE, A.VATPRC, A.TOTPRC, A.NPAYRES, A.VATPRES, A.TOTPRES, A.NDAWN, A.VATDWN, A.TOTDWN, A.NFINAN, A.VATFIN, A.TOTFIN, 
					case when A.VATRT != 0 then (A.TOTDWN-A.PAYDWN)-(((A.TOTDWN-A.PAYDWN)*7)/107) else (A.TOTDWN-A.PAYDWN) end as NKANGDWN, 
					case when A.VATRT != 0 then ((A.TOTDWN-A.PAYDWN)*7)/107 else 0 end as VKANGDWN, (A.TOTDWN-A.PAYDWN) as KANGDWN,
					case when A.VATRT != 0 then (A.TOTFIN-A.PAYFIN)-(((A.TOTFIN-A.PAYFIN)*7)/107) else (A.TOTFIN-A.PAYFIN) end as NKANGFN, 
					case when A.VATRT != 0 then ((A.TOTFIN-A.PAYFIN)*7)/107 else 0 end as VKANGFN, (A.TOTFIN-A.PAYFIN) as KANGFN,
					DATEDIFF(Day,A.SDATE,'".$ARDATES."') AS NUMDATE,  A.FINCOD, (SELECT FINNAME FROM {$this->MAuth->getdb('FINMAST')} WHERE FINCODE=A.FINCOD)as FINNAME,
					A.SALCOD, (SELECT NAME FROM {$this->MAuth->getdb('OFFICER')} WHERE CODE = A.SALCOD)AS NAME
					from {$this->MAuth->getdb('ARFINC')} A
					left join {$this->MAuth->getdb('CUSTMAST')} B on A.CUSCOD = B.CUSCOD
					left join {$this->MAuth->getdb('INVTRAN')} C on A.STRNO = C.STRNO
					where A.TOTPRC > 0   AND (A.TOTPRC > A.SMPAY OR (A.TOTPRC = A.SMPAY AND A.LPAYD > '".$ARDATES."' ))  
					AND (A.SDATE <= '".$ARDATES."') AND A.SDATE BETWEEN '".$FRMDATE."' AND '".$TODATE."'  
					".$cond."
					UNION  
					select A.LOCAT, A.CONTNO, A.SDATE, convert(nvarchar,A.SDATE,102) as SDATES, A.CUSCOD, B.SNAM+B.NAME1+' '+B.NAME2 as CUSNAME, 
					A.NPRICE, A.VATPRC, A.TOTPRC, A.NPAYRES, A.VATPRES, A.TOTPRES, A.NDAWN, A.VATDWN, A.TOTDWN, A.NFINAN, A.VATFIN, A.TOTFIN, 
					case when A.VATRT != 0 then (A.TOTDWN-A.PAYDWN)-(((A.TOTDWN-A.PAYDWN)*7)/107) else (A.TOTDWN-A.PAYDWN) end as NKANGDWN, 
					case when A.VATRT != 0 then ((A.TOTDWN-A.PAYDWN)*7)/107 else 0 end as VKANGDWN, (A.TOTDWN-A.PAYDWN) as KANGDWN,
					case when A.VATRT != 0 then (A.TOTFIN-A.PAYFIN)-(((A.TOTFIN-A.PAYFIN)*7)/107) else (A.TOTFIN-A.PAYFIN) end as NKANGFN, 
					case when A.VATRT != 0 then ((A.TOTFIN-A.PAYFIN)*7)/107 else 0 end as VKANGFN, (A.TOTFIN-A.PAYFIN) as KANGFN,
					DATEDIFF(Day,A.SDATE,'".$ARDATES."') AS NUMDATE,  A.FINCOD, (SELECT FINNAME FROM {$this->MAuth->getdb('FINMAST')} WHERE FINCODE=A.FINCOD)as FINNAME,
					A.SALCOD, (SELECT NAME FROM {$this->MAuth->getdb('OFFICER')} WHERE CODE = A.SALCOD)AS NAME
					from {$this->MAuth->getdb('HARFINC')} A 
					left join {$this->MAuth->getdb('CUSTMAST')} B on A.CUSCOD = B.CUSCOD
					left join {$this->MAuth->getdb('HINVTRAN')} C on A.STRNO = C.STRNO
					where A.TOTPRC > 0 AND (A.LPAYD > '".$ARDATES."') AND A.SDATE BETWEEN '".$FRMDATE."' AND '".$TODATE."' AND (A.SDATE <= '".$ARDATES."')
					".$cond."
				)main	
		";//echo $sql; 
		$query = $this->db->query($sql);
		
		$sql = "
				select LOCAT, CONTNO, SDATE, SDATES, CUSCOD, CUSNAME, NPRICE, VATPRC, TOTPRC, NPAYRES, VATPRES, TOTPRES, 
				NDAWN, VATDWN, TOTDWN, NFINAN, VATFIN, TOTFIN, NKANGDWN, VKANGDWN, KANGDWN, NKANGFN, VKANGFN, KANGFN, 
				NKANGDWN+NKANGFN as NARBALANCE, VKANGDWN+VKANGFN as VARBALANCE, KANGDWN+KANGFN as ARBALANCE, NUMDATE, 
				FINCOD, FINNAME, SALCOD, NAME 
				from #main
				order by ".$orderby."
		";//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$sql = "
				select 'รวมทั้งหมด' as Total, sum(NPRICE) as sumNPRICE, sum(VATPRC) as sumVATPRC, sum(TOTPRC) as sumTOTPRC, sum(NPAYRES) as sumNPAYRES, 
				sum(VATPRES) as sumVATPRES, sum(TOTPRES) as sumTOTPRES, sum(NDAWN) as sumNDAWN, sum(VATDWN) as sumVATDWN, sum(TOTDWN) as sumTOTDWN, 
				sum(NFINAN) as sumNFINAN, sum(VATFIN) as sumVATFIN, sum(TOTFIN) as sumTOTFIN, sum(NKANGDWN) as sumNKANGDWN, sum(VKANGDWN) as sumVKANGDWN, 
				sum(KANGDWN) as sumKANGDWN, sum(NKANGFN) as sumNKANGFN, sum(VKANGFN) as sumVKANGFN, sum(KANGFN) as sumKANGFN, sum(NKANGDWN+NKANGFN) as sumNARBALANCE, 
				sum(VKANGDWN+VKANGFN) as sumVARBALANCE, sum(KANGDWN+KANGFN) as sumARBALANCE
				from #main
		";
		//echo $sql; exit;
		$query2 = $this->db->query($sql);
		
		
		$head = ""; $html = ""; $head2 = "";  $report = ""; $sumreport = ""; $sumreport2 = ""; $i = 0; 
		
		if($vat == 'showvat'){
			$head = "<tr>
					<th style='display:none;'>#</th>
					<th style='vertical-align:top;'>สาขา</th>
					<th style='vertical-align:top;'>เลขที่สัญญา<br>วันที่ขาย</th>
					<th style='vertical-align:top;'>รหัสลูกค้า<br>ชื่อ - นามสกุล</th>
					<th style='vertical-align:top;'>บริษัทไฟแนนซ์<br>พนักงานขาย</th> 
					<th style='vertical-align:top;'>จำนวนวัน<br>ค้างชำระ</th> 
					<th style='vertical-align:top;text-align:right;'>มูลค่าราคาขาย</th> 
					<th style='vertical-align:top;text-align:right;'>ภาษีขาย</th>
					<th style='vertical-align:top;text-align:right;'>ราคาขาย</th>
					<th style='vertical-align:top;text-align:right;'>มูลค่าเงินจอง<br>มูลค่าค้างดาวน์</th>
					<th style='vertical-align:top;text-align:right;'>ภาษีจอง<br>ภาษีค้างดาวน์</th>
					<th style='vertical-align:top;text-align:right;'>เงินจอง<br>ค้างดาวน์</th>
					<th style='vertical-align:top;text-align:right;'>มูลค่าเงินดาวน์<br>มูลค่าค้าง FIN</th>
					<th style='vertical-align:top;text-align:right;'>ภาษีดาวน์<br>ภาษีค้าง FIN</th>
					<th style='vertical-align:top;text-align:right;'>เงินดาวน์<br>ค้าง FIN</th>
					<th style='vertical-align:top;text-align:right;'>มูลค่าส่ง FIN<br>มูลค่าคงเหลือ</th>
					<th style='vertical-align:top;text-align:right;'>ภาษีส่ง FIN<br>ภาษีคงเหลือ</th>
					<th style='vertical-align:top;text-align:right;'>ยอดส่ง FIN<br>ลูกหนี้คงเหลือ</th>
					</tr>
			";
			
			$head2 = "<tr>
					<th style='vertical-align:middle;'>#</th>
					<th style='vertical-align:middle;'>สาขา</th>
					<th style='vertical-align:middle;'>เลขที่สัญญา</th>
					<th style='vertical-align:middle;'>รหัสลูกค้า</th>
					<th style='vertical-align:middle;'>ชื่อ - นามสกุล</th>
					<th style='vertical-align:middle;'>วันที่ขาย</th>
					<th style='vertical-align:middle;'>บริษัทไฟแนนซ์</th>
					<th style='vertical-align:middle;'>พนักงานขาย</th>
					<th style='vertical-align:middle;'>จำนวนวันค้างชำระ</th>
					<th style='vertical-align:middle;'>มูลค่าราคาขาย</th>
					<th style='vertical-align:middle;'>ภาษีขาย</th>
					<th style='vertical-align:middle;'>ราคาขาย</th>
					<th style='vertical-align:middle;'>มูลค่าเงินจอง</th>
					<th style='vertical-align:middle;'>ภาษีจอง</th>
					<th style='vertical-align:middle;'>เงินจอง</th>
					<th style='vertical-align:middle;'>มูลค่าเงินดาวน์</th>
					<th style='vertical-align:middle;'>ภาษีดาวน์</th>
					<th style='vertical-align:middle;'>เงินดาวน์</th>
					<th style='vertical-align:middle;'>มูลค่าส่ง FIN</th>
					<th style='vertical-align:middle;'>ภาษีส่ง FIN</th>
					<th style='vertical-align:middle;'>ยอดส่ง FIN</th>
					<th style='vertical-align:middle;'>มูลค่าค้างดาวน์</th>
					<th style='vertical-align:middle;'>ภาษีค้างดาวน์</th>
					<th style='vertical-align:middle;'>ค้างดาวน์</th>
					<th style='vertical-align:middle;'>มูลค่าค้าง FIN</th>
					<th style='vertical-align:middle;'>ภาษีค้าง FIN</th>
					<th style='vertical-align:middle;'>ค้าง FIN</th>
					<th style='vertical-align:middle;'>มูลค่าคงเหลือ</th>
					<th style='vertical-align:middle;'>ภาษีคงเหลือ</th>
					<th style='vertical-align:middle;'>ลูกหนี้คงเหลือ</th>
					</tr>
			";
			
			$NRow = 1;
			if($query->row()){
				foreach($query->result() as $row){$i++;
					$html .= "
						<tr class='trow' seq=".$NRow.">
							<td seq=".$NRow++." style='display:none;'></td>
							<td>".$row->LOCAT."</td>
							<td>".$row->CONTNO."<br>".$this->Convertdate(2,$row->SDATES)."</td>
							<td>".$row->CUSCOD."<br>".$row->CUSNAME."</td>
							<td>".$row->FINNAME."<br>".$row->SALCOD."</td>
							<td>".number_format($row->NUMDATE)."</td>
							<td align='right'>".number_format($row->NPRICE,2)."</td>
							<td align='right'>".number_format($row->VATPRC,2)."</td>
							<td align='right'>".number_format($row->TOTPRC,2)."</td>
							<td align='right'>".number_format($row->NPAYRES,2)."<br>".number_format($row->KANGDWN,2)."</td>
							<td align='right'>".number_format($row->VATPRES,2)."<br>".number_format($row->VKANGDWN,2)."</td>
							<td align='right'>".number_format($row->TOTPRES,2)."<br>".number_format($row->NKANGDWN,2)."</td>
							<td align='right'>".number_format($row->NDAWN,2)."<br>".number_format($row->NKANGFN,2)."</td>
							<td align='right'>".number_format($row->VATDWN,2)."<br>".number_format($row->VKANGFN,2)."</td>
							<td align='right'>".number_format($row->TOTDWN,2)."<br>".number_format($row->KANGFN,2)."</td>
							<td align='right'>".number_format($row->NFINAN,2)."<br>".number_format($row->NARBALANCE,2)."</td>
							<td align='right'>".number_format($row->VATFIN,2)."<br>".number_format($row->VARBALANCE,2)."</td>
							<td align='right'>".number_format($row->TOTFIN,2)."<br>".number_format($row->ARBALANCE,2)."</td>
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
							<td style='mso-number-format:\"\@\";'>".$row->FINNAME."</td>
							<td style='mso-number-format:\"\@\";'>".$row->SALCOD."</td>
							<td style='mso-number-format:\"\@\";'>".$row->NUMDATE."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->NPRICE,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->VATPRC,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->TOTPRC,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->NPAYRES,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->VATPRES,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->TOTPRES,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->NDAWN,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->VATDWN,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->TOTDWN,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->NFINAN,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->VATFIN,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->TOTFIN,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->KANGDWN,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->VKANGDWN,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->NKANGDWN,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->NKANGFN,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->VKANGFN,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->KANGFN,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->NARBALANCE,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->VARBALANCE,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->ARBALANCE,2)."</td>
						</tr>
					";	
				}
			}
			
			if($query2->row()){
				foreach($query2->result() as $row){
					$sumreport = "
						<tr>
							<th colspan='5' style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;text-align:center;'>".$row->Total."</th>
							<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;text-align:right;'>".number_format($row->sumNPRICE,2)."</th>
							<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;text-align:right;'>".number_format($row->sumVATPRC,2)."</th>
							<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;text-align:right;'>".number_format($row->sumTOTPRC,2)."</th>
							<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;text-align:right;'>".number_format($row->sumNPAYRES,2)."<br>".number_format($row->sumKANGDWN,2)."</th>
							<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;text-align:right;'>".number_format($row->sumVATPRES,2)."<br>".number_format($row->sumVKANGDWN,2)."</th>
							<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;text-align:right;'>".number_format($row->sumTOTPRES,2)."<br>".number_format($row->sumNKANGDWN,2)."</th>
							<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;text-align:right;'>".number_format($row->sumNDAWN,2)."<br>".number_format($row->sumNKANGFN,2)."</th>
							<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;text-align:right;'>".number_format($row->sumVATDWN,2)."<br>".number_format($row->sumVKANGFN,2)."</th>
							<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;text-align:right;'>".number_format($row->sumTOTDWN,2)."<br>".number_format($row->sumKANGFN,2)."</th>
							<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;text-align:right;'>".number_format($row->sumNFINAN,2)."<br>".number_format($row->sumNARBALANCE,2)."</th>
							<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;text-align:right;'>".number_format($row->sumVATFIN,2)."<br>".number_format($row->sumVARBALANCE,2)."</th>
							<th style='border:0px;text-align:right;'>".number_format($row->sumTOTFIN,2)."<br>".number_format($row->sumARBALANCE,2)."</th>
						</tr>
					";	
				}
			}
			
			if($query2->row()){
				foreach($query2->result() as $row){
					$sumreport2 = "
						<tr class='trow'>
							<th style='mso-number-format:\"\@\";text-align:center;' colspan='9'>".$row->Total."</th>
							<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumNPRICE,2)."</th>
							<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumVATPRC,2)."</th>
							<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumTOTPRC,2)."</th>
							<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumNPAYRES,2)."</th>
							<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumVATPRES,2)."</th>
							<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumTOTPRES,2)."</th>
							<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumNDAWN,2)."</th>
							<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumVATDWN,2)."</th>
							<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumTOTDWN,2)."</th>
							<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumNFINAN,2)."</th>
							<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumVATFIN,2)."</th>
							<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumTOTFIN,2)."</th>
							<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumKANGDWN,2)."</th>
							<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumVKANGDWN,2)."</th>
							<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumNKANGDWN,2)."</th>
							<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumNKANGFN,2)."</th>
							<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumVKANGFN,2)."</th>
							<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumKANGFN,2)."</th>
							<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumNARBALANCE,2)."</th>
							<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumVARBALANCE,2)."</th>
							<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumARBALANCE,2)."</th>
						</tr>
					";	
				}
			}
		}else{
			$head = "<tr>
					<th style='display:none;'>#</th>
					<th style='vertical-align:top;'>สาขา</th>
					<th style='vertical-align:top;'>เลขที่สัญญา</th>
					<th style='vertical-align:top;'>รหัสลูกค้า<br>ชื่อ - นามสกุล</th>
					<th style='vertical-align:top;'>บริษัทไฟแนนซ์<br>พนักงานขาย</th> 
					<th style='vertical-align:top;'>วันที่ขาย</th>
					<th style='vertical-align:top;'>จำนวนวัน<br>ค้างชำระ</th> 
					<th style='vertical-align:top;text-align:right;'>ราคาขาย</th>
					<th style='vertical-align:top;text-align:right;'>เงินจอง</th>
					<th style='vertical-align:top;text-align:right;'>ค้างดาวน์</th>
					<th style='vertical-align:top;text-align:right;'>เงินดาวน์</th>
					<th style='vertical-align:top;text-align:right;'>ค้าง FIN</th>
					<th style='vertical-align:top;text-align:right;'>ยอดส่ง FIN</th>
					<th style='vertical-align:top;text-align:right;'>ลูกหนี้คงเหลือ</th>
					</tr>
			";
			
			$head2 = "<tr>
					<th style='vertical-align:middle;'>#</th>
					<th style='vertical-align:middle;'>สาขา</th>
					<th style='vertical-align:middle;'>เลขที่สัญญา</th>
					<th style='vertical-align:middle;'>รหัสลูกค้า</th>
					<th style='vertical-align:middle;'>ชื่อ - นามสกุล</th>
					<th style='vertical-align:middle;'>วันที่ขาย</th>
					<th style='vertical-align:middle;'>บริษัทไฟแนนซ์</th>
					<th style='vertical-align:middle;'>พนักงานขาย</th>
					<th style='vertical-align:middle;'>จำนวนวันค้างชำระ</th>
					<th style='text-align:right;'>ราคาขาย</th>
					<th style='text-align:right;'>เงินจอง</th>
					<th style='text-align:right;'>ค้างดาวน์</th>
					<th style='text-align:right;'>เงินดาวน์</th>
					<th style='text-align:right;'>ค้าง FIN</th>
					<th style='text-align:right;'>ยอดส่ง FIN</th>
					<th style='text-align:right;'>ลูกหนี้คงเหลือ</th>
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
						<td>".$row->FINNAME."<br>".$row->SALCOD."</td>
						<td>".$this->Convertdate(2,$row->SDATES)."</td>
						<td>".number_format($row->NUMDATE)."</td>
						<td align='right'>".number_format($row->NPRICE,2)."</td>
						<td align='right'>".number_format($row->TOTPRES,2)."</td>
						<td align='right'>".number_format($row->NKANGDWN,2)."</td>
						<td align='right'>".number_format($row->TOTDWN,2)."</td>
						<td align='right'>".number_format($row->KANGFN,2)."</td>
						<td align='right'>".number_format($row->TOTFIN,2)."</td>
						<td align='right'>".number_format($row->ARBALANCE,2)."</td>
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
							<td style='mso-number-format:\"\@\";'>".$row->FINNAME."</td>
							<td style='mso-number-format:\"\@\";'>".$row->SALCOD."</td>
							<td style='mso-number-format:\"\@\";'>".$row->NUMDATE."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->NPRICE,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->TOTPRES,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->NKANGDWN,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->TOTDWN,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->KANGFN,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->TOTFIN,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->ARBALANCE,2)."</td>
						</tr>
					";	
				}
			}
			
			if($query2->row()){
				foreach($query2->result() as $row){
					$sumreport = "
						<tr>
							<th colspan='6' style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;text-align:center;'>".$row->Total."</th>
							<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;text-align:right;'>".number_format($row->sumNPRICE,2)."</th>
							<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;text-align:right;'>".number_format($row->sumTOTPRES,2)."</th>
							<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;text-align:right;'>".number_format($row->sumNKANGDWN,2)."</th>
							<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;text-align:right;'>".number_format($row->sumTOTDWN,2)."</th>
							<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;text-align:right;'>".number_format($row->sumKANGFN,2)."</th>
							<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;text-align:right;'>".number_format($row->sumTOTFIN,2)."</th>
							<th style='border:0px;text-align:right;'>".number_format($row->sumARBALANCE,2)."</th>
						</tr>
					";	
				}
			}
			
			if($query2->row()){
				foreach($query2->result() as $row){
					$sumreport2 = "
						<tr class='trow'>
							<th style='mso-number-format:\"\@\";text-align:center;' colspan='9'>".$row->Total."</th>
							<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumNPRICE,2)."</th>
							<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumVATPRC,2)."</th>
							<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumTOTPRC,2)."</th>
							<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumNPAYRES,2)."</th>
							<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumVATPRES,2)."</th>
							<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumTOTPRES,2)."</th>
							<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumNDAWN,2)."</th>
						</tr>
					";	
				}
			}
		}
		
		if($i>0){
			$html = "
				<div id='table-fixed-ReportARfromsalefinance' class='col-sm-12' style='height:100%;width:100%;overflow:auto;font-size:8pt;'>
					<table id='table-ReportARfromsalefinance' style='background-color:white;' class='col-sm-12 display table table-bordered' cellspacing='0' width='calc(100% - 1px)'>
						<thead>
						<tr>
							<th colspan=".($vat == 'showvat' ? '17' : '13')." style='font-size:12pt;border:0px;text-align:center;'>รายงานลูกหนี้คงเหลือจากการขายส่งไฟแนนซ์</th>
						</tr>
						<tr>
							<td colspan=".($vat == 'showvat' ? '17' : '13')." style='border-bottom:1px solid #ddd;text-align:center;'>".$rpcond."  ออกรายงาน ณ วันที่ ".$this->today('today')."</td>
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
			<div id='table-fixed-ReportARfromsalefinance2' class='col-sm-12' style='height:100%;width:100%;overflow:auto;'>
				<table id='table-ReportARfromsalefinance2' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='100%'>
					<thead>
						<tr>
							<th colspan=".($vat == 'showvat' ? '30' : '16')." style='font-size:12pt;border:0px;text-align:center;'>รายงานลูกหนี้คงเหลือจากการขายส่งไฟแนนซ์</th>
						</tr>
						<tr>
							<td colspan=".($vat == 'showvat' ? '30' : '16')." style='border:0px;text-align:center;'>".$rpcond." ออกรายงาน ณ วันที่ ".$this->today('today')."</td>
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
		$data[] = 	urlencode($_REQUEST["LOCAT1"].'||'.$_REQUEST["CONTNO1"].'||'.$_REQUEST["GCODE1"].'||'.$_REQUEST["FINCODE1"].'||'.$_REQUEST["TYPE1"]
					.'||'.$_REQUEST["MODEL1"].'||'.$_REQUEST["ARDATE"].'||'.$_REQUEST["FRMDATE"].'||'.$_REQUEST["TODATE"].'||'.$_REQUEST["orderby"]
					.'||'.$_REQUEST["vat"].'||'.$_REQUEST["stat"].'||'.$_REQUEST["layout"]);
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
		$GCODE1 	= str_replace(chr(0),'',$tx[2]);
		$FINCODE1 	= str_replace(chr(0),'',$tx[3]);
		$TYPE1 		= str_replace(chr(0),'',$tx[4]);
		$MODEL1 	= str_replace(chr(0),'',$tx[5]);
		$ARDATE 	= $tx[6];
		$FRMDATE 	= $this->Convertdate(1,$tx[7]);
		$TODATE 	= $this->Convertdate(1,$tx[8]);
		$orderby 	= $tx[9];
		$vat 		= $tx[10];
		$stat 		= $tx[11];
		$layout 	= $tx[12];
		
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
		
		if($FINCODE1 != ""){
			$cond .= " AND A.FINCOD LIKE '%".$FINCODE1."%'";
			$rpcond .= "  รหัสบริษัทไฟแนนซ์ ".$FINCODE1;
		}else{
			$cond .= " AND (A.FINCOD LIKE '%%' OR A.FINCOD IS NULL ) ";
		}
		
		if($stat == "N"){
			$rpcond .= "  ประเภทสินค้า รถใหม่ ";
			$cond .= " AND (C.STAT LIKE '%".$stat."%')";
		}else if($stat == "O"){
			$rpcond .= "  ประเภทสินค้า รถมือสอง ";
			$cond .= " AND (C.STAT LIKE '%".$stat."%')";
		}else{
			$cond .= " AND (C.STAT LIKE '%%' OR C.STAT IS NULL )";
		}
		
		if($GCODE1 != ""){
			$cond .= " AND (C.GCODE LIKE '%".$GCODE1."%')";
			$rpcond .= "  สถานะสินค้า ".$GCODE1;
		}else{
			$cond .= " AND (C.GCODE LIKE '%%' OR C.GCODE IS NULL )";
		}
		
		if($TYPE1 != ""){
			$cond .= " AND (C.TYPE  LIKE '%".$TYPE1."%')";
			$rpcond .= "  ยี่ห้อสินค้า ".$TYPE1;
		}else{
			$cond .= " AND (C.TYPE  LIKE '%%' OR C.TYPE IS NULL)";
		}
		
		if($MODEL1 != ""){
			$cond .= " AND (C.MODEL LIKE '%".$MODEL1."%')";
			$rpcond .= "  รุ่น ".$MODEL1;
		}else{
			$cond .= " AND (C.MODEL LIKE '%%' OR C.MODEL IS NULL)";
		}
		
		$sql = "
				IF OBJECT_ID('tempdb..#main') IS NOT NULL DROP TABLE #main
				select *
				into #main
				from(
					select A.LOCAT, A.CONTNO, A.SDATE, convert(nvarchar,A.SDATE,112) as SDATES, A.CUSCOD, B.SNAM+B.NAME1+' '+B.NAME2 as CUSNAME, 
					A.NPRICE, A.VATPRC, A.TOTPRC, A.NPAYRES, A.VATPRES, A.TOTPRES, A.NDAWN, A.VATDWN, A.TOTDWN, A.NFINAN, A.VATFIN, A.TOTFIN, 
					case when A.VATRT != 0 then (A.TOTDWN-A.PAYDWN)-(((A.TOTDWN-A.PAYDWN)*7)/107) else (A.TOTDWN-A.PAYDWN) end as NKANGDWN, 
					case when A.VATRT != 0 then ((A.TOTDWN-A.PAYDWN)*7)/107 else 0 end as VKANGDWN, (A.TOTDWN-A.PAYDWN) as KANGDWN,
					case when A.VATRT != 0 then (A.TOTFIN-A.PAYFIN)-(((A.TOTFIN-A.PAYFIN)*7)/107) else (A.TOTFIN-A.PAYFIN) end as NKANGFN, 
					case when A.VATRT != 0 then ((A.TOTFIN-A.PAYFIN)*7)/107 else 0 end as VKANGFN, (A.TOTFIN-A.PAYFIN) as KANGFN,
					DATEDIFF(Day,A.SDATE,'".$ARDATES."') AS NUMDATE,  A.FINCOD, (SELECT FINNAME FROM {$this->MAuth->getdb('FINMAST')} WHERE FINCODE=A.FINCOD)as FINNAME,
					A.SALCOD, (SELECT NAME FROM {$this->MAuth->getdb('OFFICER')} WHERE CODE = A.SALCOD)AS NAME
					from {$this->MAuth->getdb('ARFINC')} A
					left join {$this->MAuth->getdb('CUSTMAST')} B on A.CUSCOD = B.CUSCOD
					left join {$this->MAuth->getdb('INVTRAN')} C on A.STRNO = C.STRNO
					where A.TOTPRC > 0   AND (A.TOTPRC > A.SMPAY OR (A.TOTPRC = A.SMPAY AND A.LPAYD > '".$ARDATES."' ))  
					AND (A.SDATE <= '".$ARDATES."') AND A.SDATE BETWEEN '".$FRMDATE."' AND '".$TODATE."'  
					".$cond."
					UNION  
					select A.LOCAT, A.CONTNO, A.SDATE, convert(nvarchar,A.SDATE,112) as SDATES, A.CUSCOD, B.SNAM+B.NAME1+' '+B.NAME2 as CUSNAME, 
					A.NPRICE, A.VATPRC, A.TOTPRC, A.NPAYRES, A.VATPRES, A.TOTPRES, A.NDAWN, A.VATDWN, A.TOTDWN, A.NFINAN, A.VATFIN, A.TOTFIN, 
					case when A.VATRT != 0 then (A.TOTDWN-A.PAYDWN)-(((A.TOTDWN-A.PAYDWN)*7)/107) else (A.TOTDWN-A.PAYDWN) end as NKANGDWN, 
					case when A.VATRT != 0 then ((A.TOTDWN-A.PAYDWN)*7)/107 else 0 end as VKANGDWN, (A.TOTDWN-A.PAYDWN) as KANGDWN,
					case when A.VATRT != 0 then (A.TOTFIN-A.PAYFIN)-(((A.TOTFIN-A.PAYFIN)*7)/107) else (A.TOTFIN-A.PAYFIN) end as NKANGFN, 
					case when A.VATRT != 0 then ((A.TOTFIN-A.PAYFIN)*7)/107 else 0 end as VKANGFN, (A.TOTFIN-A.PAYFIN) as KANGFN,
					DATEDIFF(Day,A.SDATE,'".$ARDATES."') AS NUMDATE,  A.FINCOD, (SELECT FINNAME FROM {$this->MAuth->getdb('FINMAST')} WHERE FINCODE=A.FINCOD)as FINNAME,
					A.SALCOD, (SELECT NAME FROM {$this->MAuth->getdb('OFFICER')} WHERE CODE = A.SALCOD)AS NAME
					from {$this->MAuth->getdb('HARFINC')} A 
					left join {$this->MAuth->getdb('CUSTMAST')} B on A.CUSCOD = B.CUSCOD
					left join {$this->MAuth->getdb('HINVTRAN')} C on A.STRNO = C.STRNO
					where A.TOTPRC > 0 AND (A.LPAYD > '".$ARDATES."') AND A.SDATE BETWEEN '".$FRMDATE."' AND '".$TODATE."' AND (A.SDATE <= '".$ARDATES."')
					".$cond."
				)main	
		";//echo $sql; 
		$query = $this->db->query($sql);
		
		$sql = "
				select LOCAT, CONTNO, SDATE, SDATES, CUSCOD, CUSNAME, NPRICE, VATPRC, TOTPRC, NPAYRES, VATPRES, TOTPRES, 
				NDAWN, VATDWN, TOTDWN, NFINAN, VATFIN, TOTFIN, NKANGDWN, VKANGDWN, KANGDWN, NKANGFN, VKANGFN, KANGFN, 
				NKANGDWN+NKANGFN as NARBALANCE, VKANGDWN+VKANGFN as VARBALANCE, KANGDWN+KANGFN as ARBALANCE, NUMDATE, 
				FINCOD, FINNAME, SALCOD, NAME 
				from #main
				order by ".$orderby."
		";//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$sql = "
				select 'รวมทั้งหมด' as Total, sum(NPRICE) as sumNPRICE, sum(VATPRC) as sumVATPRC, sum(TOTPRC) as sumTOTPRC, sum(NPAYRES) as sumNPAYRES, 
				sum(VATPRES) as sumVATPRES, sum(TOTPRES) as sumTOTPRES, sum(NDAWN) as sumNDAWN, sum(VATDWN) as sumVATDWN, sum(TOTDWN) as sumTOTDWN, 
				sum(NFINAN) as sumNFINAN, sum(VATFIN) as sumVATFIN, sum(TOTFIN) as sumTOTFIN, sum(NKANGDWN) as sumNKANGDWN, sum(VKANGDWN) as sumVKANGDWN, 
				sum(KANGDWN) as sumKANGDWN, sum(NKANGFN) as sumNKANGFN, sum(VKANGFN) as sumVKANGFN, sum(KANGFN) as sumKANGFN, sum(NKANGDWN+NKANGFN) as sumNARBALANCE, 
				sum(VKANGDWN+VKANGFN) as sumVARBALANCE, sum(KANGDWN+KANGFN) as sumARBALANCE
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
						<th style='border-bottom:0.1px solid black;text-align:left;'>เลขที่สัญญา<br>วันที่ขาย</th>
						<th style='border-bottom:0.1px solid black;text-align:left;'>รหัสลูกค้า<br>ชื่อ - นามสกุล</th>
						<th style='border-bottom:0.1px solid black;text-align:left;'>บริษัทไฟแนนซ์<br>พนักงานขาย</th> 
						<th style='border-bottom:0.1px solid black;text-align:left;'>จำนวนวัน<br>ค้างชำระ</th> 
						<th style='border-bottom:0.1px solid black;text-align:right;'>มูลค่าราคาขาย</th> 
						<th style='border-bottom:0.1px solid black;text-align:right;'>ภาษีขาย</th>
						<th style='border-bottom:0.1px solid black;text-align:right;'>ราคาขาย</th>
						<th style='border-bottom:0.1px solid black;text-align:right;'>มูลค่าเงินจอง<br>มูลค่าค้างดาวน์</th>
						<th style='border-bottom:0.1px solid black;text-align:right;'>ภาษีจอง<br>ภาษีค้างดาวน์</th>
						<th style='border-bottom:0.1px solid black;text-align:right;'>เงินจอง<br>ค้างดาวน์</th>
						<th style='border-bottom:0.1px solid black;text-align:right;'>มูลค่าเงินดาวน์<br>มูลค่าค้าง FIN</th>
						<th style='border-bottom:0.1px solid black;text-align:right;'>ภาษีดาวน์<br>ภาษีค้าง FIN</th>
						<th style='border-bottom:0.1px solid black;text-align:right;'>เงินดาวน์<br>ค้าง FIN</th>
						<th style='border-bottom:0.1px solid black;text-align:right;'>มูลค่าส่ง FIN<br>มูลค่าคงเหลือ</th>
						<th style='border-bottom:0.1px solid black;text-align:right;'>ภาษีส่ง FIN<br>ภาษีคงเหลือ</th>
						<th style='border-bottom:0.1px solid black;text-align:right;'>ยอดส่ง FIN<br>ลูกหนี้คงเหลือ</th>
					</tr>
			";
			
			$No = 1;
			if($query->row()){
				foreach($query->result() as $row){	
					$html .= "
						<tr class='trow' seq=".$No.">
							<td style='width:25px;'>".$No++."</td>
							<td style='width:40px;'>".$row->LOCAT."</td>
							<td style='width:80px;'>".$row->CONTNO."<br>".$this->Convertdate(2,$row->SDATES)."</td>
							<td style='width:120px;'>".$row->CUSCOD."<br>".$row->CUSNAME."</td>
							<td style='width:250px;'>".$row->FINNAME."<br>".$row->SALCOD."</td>
							<td style='width:80px;'>".number_format($row->NUMDATE)."</td>
							<td style='width:85px;' align='right'>".number_format($row->NPRICE,2)."</td>
							<td style='width:85px;' align='right'>".number_format($row->VATPRC,2)."</td>
							<td style='width:85px;' align='right'>".number_format($row->TOTPRC,2)."</td>
							<td style='width:85px;' align='right'>".number_format($row->NPAYRES,2)."<br>".number_format($row->KANGDWN,2)."</td>
							<td style='width:85px;' align='right'>".number_format($row->VATPRES,2)."<br>".number_format($row->VKANGDWN,2)."</td>
							<td style='width:85px;' align='right'>".number_format($row->TOTPRES,2)."<br>".number_format($row->NKANGDWN,2)."</td>
							<td style='width:85px;' align='right'>".number_format($row->NDAWN,2)."<br>".number_format($row->NKANGFN,2)."</td>
							<td style='width:85px;' align='right'>".number_format($row->VATDWN,2)."<br>".number_format($row->VKANGFN,2)."</td>
							<td style='width:85px;' align='right'>".number_format($row->TOTDWN,2)."<br>".number_format($row->KANGFN,2)."</td>
							<td style='width:85px;' align='right'>".number_format($row->NFINAN,2)."<br>".number_format($row->NARBALANCE,2)."</td>
							<td style='width:85px;' align='right'>".number_format($row->VATFIN,2)."<br>".number_format($row->VARBALANCE,2)."</td>
							<td style='width:85px;' align='right'>".number_format($row->TOTFIN,2)."<br>".number_format($row->ARBALANCE,2)."</td>
						</tr>
					";	
				}
			}
			
			if($query2->row()){
				foreach($query2->result() as $row){	
					$html .= "
						<tr class='trow bor' style='background-color:#ebebeb;'>
							<td colspan='6' style='text-align:center;vertical-align:middle;'>".$row->Total."</td>
							<th align='right'>".number_format($row->sumNPRICE,2)."</th>
							<th align='right'>".number_format($row->sumVATPRC,2)."</th>
							<th align='right'>".number_format($row->sumTOTPRC,2)."</th>
							<th align='right'>".number_format($row->sumNPAYRES,2)."<br>".number_format($row->sumKANGDWN,2)."</th>
							<th align='right'>".number_format($row->sumVATPRES,2)."<br>".number_format($row->sumVKANGDWN,2)."</th>
							<th align='right'>".number_format($row->sumTOTPRES,2)."<br>".number_format($row->sumNKANGDWN,2)."</th>
							<th align='right'>".number_format($row->sumNDAWN,2)."<br>".number_format($row->sumNKANGFN,2)."</th>
							<th align='right'>".number_format($row->sumVATDWN,2)."<br>".number_format($row->sumVKANGFN,2)."</th>
							<th align='right'>".number_format($row->sumTOTDWN,2)."<br>".number_format($row->sumKANGFN,2)."</th>
							<th align='right'>".number_format($row->sumNFINAN,2)."<br>".number_format($row->sumNARBALANCE,2)."</th>
							<th align='right'>".number_format($row->sumVATFIN,2)."<br>".number_format($row->sumVARBALANCE,2)."</th>
							<th align='right'>".number_format($row->sumTOTFIN,2)."<br>".number_format($row->sumARBALANCE,2)."</th>
						</tr>
					";	
				}
			}
		}else{
			$head = "<tr>
						<th style='border-bottom:0.1px solid black;text-align:left;'>#</th>
						<th style='border-bottom:0.1px solid black;text-align:left;'>สาขา</th>
						<th style='border-bottom:0.1px solid black;text-align:left;'>เลขที่สัญญา</th>
						<th style='border-bottom:0.1px solid black;text-align:left;'>รหัสลูกค้า<br>ชื่อ - นามสกุล</th>
						<th style='border-bottom:0.1px solid black;text-align:left;'>บริษัทไฟแนนซ์<br>พนักงานขาย</th> 
						<th style='border-bottom:0.1px solid black;text-align:left;'>วันที่ขาย</th>
						<th style='border-bottom:0.1px solid black;text-align:left;'>จำนวนวัน<br>ค้างชำระ</th> 
						<th style='border-bottom:0.1px solid black;text-align:right;'>ราคาขาย</th>
						<th style='border-bottom:0.1px solid black;text-align:right;'>เงินจอง</th>
						<th style='border-bottom:0.1px solid black;text-align:right;'>ค้างดาวน์</th>
						<th style='border-bottom:0.1px solid black;text-align:right;'>เงินดาวน์</th>
						<th style='border-bottom:0.1px solid black;text-align:right;'>ค้าง FIN</th>
						<th style='border-bottom:0.1px solid black;text-align:right;'>ยอดส่ง FIN</th>
						<th style='border-bottom:0.1px solid black;text-align:right;'>ลูกหนี้คงเหลือ</th>
					</tr>
			";
			
			$No = 1;
			if($query->row()){
				foreach($query->result() as $row){	
					$html .= "
						<tr class='trow' seq=".$No.">
							<td style='width:25px;'>".$No++."</td>
							<td style='width:40px;'>".$row->LOCAT."</td>
							<td style='width:85px;'>".$row->CONTNO."</td>
							<td style='width:120px;'>".$row->CUSCOD."<br>".$row->CUSNAME."</td>
							<td style='width:250px;'>".$row->FINNAME."<br>".$row->SALCOD."</td>
							<td style='width:65px;'>".$this->Convertdate(2,$row->SDATES)."</td>
							<td style='width:60px;'>".number_format($row->NUMDATE)."</td>
							<td style='width:75px;' align='right'>".number_format($row->NPRICE,2)."</td>
							<td style='width:75px;' align='right'>".number_format($row->TOTPRES,2)."</td>
							<td style='width:75px;' align='right'>".number_format($row->KANGDWN,2)."</td>
							<td style='width:75px;' align='right'>".number_format($row->TOTDWN,2)."</td>
							<td style='width:75px;' align='right'>".number_format($row->KANGFN,2)."</td>
							<td style='width:75px;' align='right'>".number_format($row->TOTFIN,2)."</td>
							<td style='width:80px;' align='right'>".number_format($row->ARBALANCE,2)."</td>
						</tr>
					";	
				}
			}
			
			if($query2->row()){
				foreach($query2->result() as $row){	
					$html .= "
						<tr class='trow bor' style='background-color:#ebebeb;'>
							<td colspan='7' style='text-align:center;vertical-align:middle;'>".$row->Total."</td>
							<td style='text-align:right;'>".number_format($row->sumNPRICE,2)."</td>
							<td style='text-align:right;'>".number_format($row->sumTOTPRES,2)."</td>
							<td style='text-align:right;'>".number_format($row->sumKANGDWN,2)."</td>
							<td style='text-align:right;'>".number_format($row->sumTOTDWN,2)."</td>
							<td style='text-align:right;'>".number_format($row->sumKANGFN,2)."</td>
							<td style='text-align:right;'>".number_format($row->sumTOTFIN,2)."</td>
							<td style='text-align:right;'>".number_format($row->sumARBALANCE,2)."</td>
						</tr>
					";	
				}
			}
		}
		
		$mpdf = new \Mpdf\Mpdf([
			'mode' => 'utf-8', 
			'format' => $layout,
			'margin_top' => 10, 	//default = 16
			'margin_left' => 6, 	//default = 15
			'margin_right' => 6, 	//default = 15
			'margin_bottom' => 10, 	//default = 16
			'margin_header' => 9, 	//default = 9
			'margin_footer' => 9, 	//default = 9
		]);
		
		$content = "
			<table class='wf' style='font-size:8pt;height:700px;border-collapse:collapse;line-height:23px;overflow:wrap;vertical-align:text-top;'>
				<tbody>
					<tr>
						<th colspan=".($vat == 'showvat' ? '18' : '14')." style='font-size:10pt;'>รายงานลูกหนี้คงเหลือจากการขายส่งไฟแนนซ์</th>
					</tr>
					<tr>
						<td colspan=".($vat == 'showvat' ? '18' : '14')." style='font-size:8pt;height:35px;border-bottom:0.1px solid black;text-align:center;'>".$rpcond."</td>
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