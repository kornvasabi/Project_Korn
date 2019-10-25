<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//BEE+
class ReportARfromsalecash extends MY_Controller {
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
				<div class='col-sm-12 col-xs-12' style='overflow:auto;font-size:11pt;'>					
					<div class='row'>
						<div class='col-sm-12 col-xs-12' style='background-color:#3d9690;border:5px solid white;height:75px;text-align:center;font-size:12pt;color:white;font-weight:bold;'>	
							<br>รายงานลูกหนี้คงเหลือจากการขายสด<br>
						</div>
					</div>
					<br>
					<div class='row'>
						<div class='col-sm-8 col-xs-8 col-sm-offset-2'>	
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
								<div class='form-group' >
									กลุ่มสินค้า
									<select id='GCODE1' class='form-control input-sm' data-placeholder='กลุ่มสินค้า'></select>
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
					</div>
					<div class='row'>
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
					</div>
					<div class='row'>
						<div class='col-sm-8 col-xs-8 col-sm-offset-2'><br>	
							<div class='col-sm-4 col-xs-4'>	
								<div class='form-group'>
									รูปแบบการพิมพ์
									<div class='col-sm-12 col-xs-12' style='border:0.1px dotted #d6d6d6;'>	
										<div class='col-sm-12 col-xs-12'>
											<div class='form-group'>
												<br>
												<input type= 'radio' id='hor' name='layout' checked> แนวนอน
												<br><br><br><br>
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
												<br><br><br><br>
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
												<br><br>
												<input type= 'radio' id='OLD' name='stat'> เก่า
												<br><br>
												<input type= 'radio' id='ALL' name='stat' checked> ทั้งหมด
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class='row'>
						<div class='col-sm-12 col-xs-12'><br>	
							<div class='form-group'>
								<br><br>
								<button id='btnt1search' class='btn btn-primary btn-sm' style='width:100%'><span class='glyphicon glyphicon-search'> แสดง</span></button>
							</div>
						</div>
					</div>
				</div>
			</div>
		";
		
		$html.= "<script src='".base_url('public/js/SYS05/ReportARfromsalecash.js')."'></script>";
		echo $html;
	}
	
	function search(){
		$LOCAT1		= $_REQUEST["LOCAT1"];
		$CONTNO1 	= $_REQUEST["CONTNO1"];
		$GCODE1 	= $_REQUEST["GCODE1"];
		$TYPE1 		= $_REQUEST["TYPE1"];
		$MODEL1 	= $_REQUEST["MODEL1"];
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
		
		if($stat == "N"){
			$rpcond .= "  ประเภทสินค้า รถใหม่ ";
		}else if($stat == "O"){
			$rpcond .= "  ประเภทสินค้า รถมือสอง ";
		}
		
		if($TYPE1 != ""){
			$cond .= " AND (C.TYPE  LIKE '%".$TYPE1."%')";
			$rpcond .= "  ยี่ห้อสินค้า ".$TYPE1;
		}
		
		if($MODEL1 != ""){
			$cond .= " AND (C.MODEL LIKE '%".$MODEL1."%')";
			$rpcond .= "  รุ่น ".$MODEL1;
		}
		
		$GCODES = "";
		if($GCODE1 != ""){
			$GCODES = $GCODE1;
			$rpcond .= "  สถานะสินค้า ".$MODEL1;
		}
		
		$sql = "
				IF OBJECT_ID('tempdb..#SALE') IS NOT NULL DROP TABLE #SALE
				select *
				into #SALE
				from(
					select A.LOCAT, A.CONTNO, A.CUSCOD, A.SDATE, B.SNAM+B.NAME1+'  '+B.NAME2 as CUSNAME, A.NPRICE, A.VATPRC, A.TOTPRC, A.NPAYRES, A.VATPRES, 
					A.TOTPRES, A.SMPAY, A.SMCHQ
					from {$this->MAuth->getdb('ARCRED')} A
					left join CUSTMAST B on A.CUSCOD = B.CUSCOD
					left join INVTRAN C on A.STRNO = C.STRNO
					where (A.SDATE <= '".$ARDATES."') AND (C.GCODE LIKE '%".$GCODES."%' OR C.GCODE IS NULL ) AND (C.STAT LIKE '%".$stat."%' OR C.STAT IS NULL ) 
					AND A.TOTPRC > 0 AND (A.TOTPRC > A.SMPAY OR (A.TOTPRC = A.SMPAY AND A.LPAYDT > '".$ARDATES."')) ".$cond."
					union
					select A.LOCAT, A.CONTNO, A.CUSCOD, A.SDATE, B.SNAM+B.NAME1+'  '+B.NAME2 as CUSNAME, A.NPRICE, A.VATPRC, A.TOTPRC, A.NPAYRES, A.VATPRES, 
					A.TOTPRES, A.SMPAY, A.SMCHQ
					from {$this->MAuth->getdb('HARCRED')} A
					left join CUSTMAST B on A.CUSCOD = B.CUSCOD
					left join HINVTRAN C on A.STRNO = C.STRNO
					where (A.SDATE <= '".$ARDATES."') AND (C.GCODE LIKE '%".$GCODES."%' OR C.GCODE IS NULL ) AND (C.STAT LIKE '%".$stat."%' OR C.STAT IS NULL ) 
					AND A.TOTPRC > 0 AND (A.TOTPRC > A.SMPAY OR (A.TOTPRC = A.SMPAY AND A.LPAYDT > '".$ARDATES."')) ".$cond."
				)SALE
		";
		//echo $sql; 
		$query = $this->db->query($sql);
		
		$sql = "
				IF OBJECT_ID('tempdb..#VAT') IS NOT NULL DROP TABLE #VAT
				select *
				into #VAT
				from(
					select A.CONTNO, 
					sum(CASE WHEN (A.PAYDT <= '".$ARDATES."') THEN  A.PAYAMT ELSE 0 END) AS SNETP  ,
					sum(CASE WHEN ((A.PAYDT > '".$ARDATES."' OR A.PAYDT IS NULL) AND A.PAYTYP = '02' AND A.TMBILDT <= '".$ARDATES."') THEN  A.PAYAMT ELSE 0 END) AS SNETP1,
					sum(CASE WHEN (A.PAYDT <= '".$ARDATES."') THEN  A.PAYAMT_V ELSE 0 END) AS VPY  ,
					sum(CASE WHEN ((A.PAYDT > '".$ARDATES."' OR A.PAYDT IS NULL) AND A.PAYTYP = '02' AND A.TMBILDT <= '".$ARDATES."') THEN  A.PAYAMT_V ELSE 0 END) AS VCQ  
					from CHQTRAN A
					where  A.FLAG != 'C' AND A.CONTNO in (select CONTNO from #SALE)
					group by A.CONTNO
				)VAT
		";
		//echo $sql;
		$query = $this->db->query($sql);
		
		$sql = "
				select A.LOCAT, A.CONTNO, A.CUSCOD, A.CUSNAME, convert(nvarchar,A.SDATE,112) as SDATE, A.NPRICE, A.VATPRC, A.TOTPRC, A.NPAYRES, A.VATPRES, A.TOTPRES, A.SMPAY-isnull(B.VPY,0) as SMPAY, 
				isnull(B.VPY,0) as VPY, A.SMPAY as TOTSMPAY, A.NPRICE-(A.SMPAY-isnull(B.VPY,0)) as ARBALANC, A.VATPRC-isnull(B.VPY,0) as ARVAT, 
				A.TOTPRC-A.SMPAY as TOTAR, A.SMCHQ-isnull(B.VCQ,0) as SMCHQ, isnull(B.VCQ,0) as VCQ, A.SMCHQ, 
				A.NPRICE-(A.SMPAY-isnull(B.VPY,0))-(A.SMCHQ-isnull(B.VCQ,0)) as BALANCE, A.VATPRC-isnull(B.VPY,0)-isnull(B.VCQ,0) as VATBALNCE,
				A.TOTPRC-A.SMPAY-A.SMCHQ as TOTBALANCE
				from #SALE A
				left join #VAT B on A.CONTNO = B.CONTNO
				order by A.SDATE
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$head = ""; $html = ""; $head2 = "";  $report = ""; $i=0; 
		
		if($vat == 'showvat'){
			$head = "<tr>
					<th style='display:none;'>#</th>
					<th style='vertical-align:middle;'>สาขา</th>
					<th style='vertical-align:middle;'>เลขที่สัญญา</th>
					<th style='vertical-align:middle;'>รหัสลูกค้า</th>
					<th style='vertical-align:middle;'>ชื่อ - นามสกุล</th>
					<th style='vertical-align:middle;'>วันที่ขาย</th>
					<th style='vertical-align:middle;'>มูลค่าราคาขาย</th>
					<th style='vertical-align:middle;'>ภาษีขาย</th>
					<th style='vertical-align:middle;'>ราคาขาย</th>
					<th style='vertical-align:middle;'>มูลค่าเงินจอง</th>
					<th style='vertical-align:middle;'>ภาษีจอง</th>
					<th style='vertical-align:middle;'>เงินจอง</th>
					<th style='vertical-align:middle;'>มูลค่าชำระ</th>
					<th style='vertical-align:middle;'>ภาษีชำระ</th>
					<th style='vertical-align:middle;'>ชำระแล้ว</th>
					<th style='vertical-align:middle;'>มูลค่าล/นคงเหลือ</th>
					<th style='vertical-align:middle;'>ภาษีคงเหลือ</th>
					<th style='vertical-align:middle;'>ลูกหนี้คงเหลือ</th>
					<th style='vertical-align:middle;'>มูลค่าเช็ครอเรียกเก็บ</th>
					<th style='vertical-align:middle;'>ภาษีเช็ค</th>
					<th style='vertical-align:middle;'>เช็ครอเรียกเก็บ</th>
					<th style='vertical-align:middle;'>มูลค่าล/นหักเช็ค</th>
					<th style='vertical-align:middle;'>ภาษีล/นหักเช็ค</th>
					<th style='vertical-align:middle;'>ล/นหักเช็ค</th>
					</tr>
			";
			
			$head2 = "<tr>
					<th style='vertical-align:middle;'>#</th>
					<th style='vertical-align:middle;'>สาขา</th>
					<th style='vertical-align:middle;'>เลขที่สัญญา</th>
					<th style='vertical-align:middle;'>รหัสลูกค้า</th>
					<th style='vertical-align:middle;'>ชื่อ - นามสกุล</th>
					<th style='vertical-align:middle;'>วันที่ขาย</th>
					<th style='vertical-align:middle;'>มูลค่าราคาขาย</th>
					<th style='vertical-align:middle;'>ภาษีขาย</th>
					<th style='vertical-align:middle;'>ราคาขาย</th>
					<th style='vertical-align:middle;'>มูลค่าเงินจอง</th>
					<th style='vertical-align:middle;'>ภาษีจอง</th>
					<th style='vertical-align:middle;'>เงินจอง</th>
					<th style='vertical-align:middle;'>มูลค่าชำระ</th>
					<th style='vertical-align:middle;'>ภาษีชำระ</th>
					<th style='vertical-align:middle;'>ชำระแล้ว</th>
					<th style='vertical-align:middle;'>มูลค่าล/นคงเหลือ</th>
					<th style='vertical-align:middle;'>ภาษีคงเหลือ</th>
					<th style='vertical-align:middle;'>ลูกหนี้คงเหลือ</th>
					<th style='vertical-align:middle;'>มูลค่าเช็ครอเรียกเก็บ</th>
					<th style='vertical-align:middle;'>ภาษีเช็ค</th>
					<th style='vertical-align:middle;'>เช็ครอเรียกเก็บ</th>
					<th style='vertical-align:middle;'>มูลค่าล/นหักเช็ค</th>
					<th style='vertical-align:middle;'>ภาษีล/นหักเช็ค</th>
					<th style='vertical-align:middle;'>ล/นหักเช็ค</th>
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
							<td>".$row->CUSCOD."</td>
							<td>".$row->CUSNAME."</td>
							<td>".$this->Convertdate(2,$row->SDATE)."</td>
							<td align='right'>".number_format($row->NPRICE,2)."</td>
							<td align='right'>".number_format($row->VATPRC,2)."</td>
							<td align='right'>".number_format($row->TOTPRC,2)."</td>
							<td align='right'>".number_format($row->NPAYRES,2)."</td>
							<td align='right'>".number_format($row->VATPRES,2)."</td>
							<td align='right'>".number_format($row->TOTPRES,2)."</td>
							<td align='right'>".number_format($row->SMPAY,2)."</td>
							<td align='right'>".number_format($row->VPY,2)."</td>
							<td align='right'>".number_format($row->TOTSMPAY,2)."</td>
							<td align='right'>".number_format($row->ARBALANC,2)."</td>
							<td align='right'>".number_format($row->ARVAT,2)."</td>
							<td align='right'>".number_format($row->TOTAR,2)."</td>
							<td align='right'>".number_format($row->SMCHQ,2)."</td>
							<td align='right'>".number_format($row->VCQ,2)."</td>
							<td align='right'>".number_format($row->SMCHQ,2)."</td>
							<td align='right'>".number_format($row->BALANCE,2)."</td>
							<td align='right'>".number_format($row->VATBALNCE,2)."</td>
							<td align='right'>".number_format($row->TOTBALANCE,2)."</td>
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
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->NPRICE,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->VATPRC,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->TOTPRC,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->NPAYRES,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->VATPRES,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->TOTPRES,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->SMPAY,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->VPY,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->TOTSMPAY,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->ARBALANC,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->ARVAT,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->TOTAR,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->SMCHQ,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->VCQ,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->SMCHQ,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->BALANCE,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->VATBALNCE,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->TOTBALANCE,2)."</td>
						</tr>
					";	
				}
			}
		}
		
		if($i>0){
			$html = "
				<div id='table-fixed-ReportHoldtoStock' class='col-sm-12' style='height:100%;width:100%;overflow:auto;font-size:9pt;'>
					<table id='table-ReportHoldtoStock' style='background-color:white;' class='col-sm-12 display table table-bordered' cellspacing='0' width='calc(100% - 1px)'>
						<thead>
						<tr>
							<th colspan='23' style='font-size:12pt;border:0px;text-align:center;'>รายงานรถยึดรอไถ่ถอน</th>
						</tr>
						<tr>
							<td colspan='23' style='border-bottom:1px solid #ddd;text-align:center;'>".$rpcond."  ออกรายงาน ณ วันที่ ".$this->today('today')."</td>
						</tr>
						".$head."
						</thead>	
						<tbody style='height: 10px !important; overflow: scroll;'>
						".$html."
						</tbody>	
					</table>
				</div>
			";
		}else{
			$html="<font style='color:red;'>ไม่มีข้อมูล</font>";
		}
		
		$report = "
			<div id='table-fixed-ReportHoldtoStock2' class='col-sm-12' style='height:100%;width:100%;overflow:auto;'>
				<table id='table-ReportHoldtoStock2' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='100%'>
					<thead>
						<tr>
							<th colspan='24' style='font-size:12pt;border:0px;text-align:center;'>รายงานรถยึดรอไถ่ถอน</th>
						</tr>
						<tr>
							<td colspan='24' style='border:0px;text-align:center;'>".$rpcond." ออกรายงาน ณ วันที่ ".$this->today('today')."</td>
						</tr>
						".$head2."
					</thead>	
					<tbody>
						".$report."
					</tbody>
				</table>
			</div>
		";
		
		$response = array("html"=>$html, "report"=>$report, "reporttoday"=>str_replace('/','-',$this->today('today')));
		echo json_encode($response);
	}
	
	function conditiontopdf(){
		$data = array();
		$data[] = urlencode($_REQUEST["LOCAT1"].'||'.$_REQUEST["FROMDATECHG"].'||'.$_REQUEST["TODATECHG"].'||'.$_REQUEST["orderby"].'||'.$_REQUEST["layout"]);
		echo json_encode($this->generateData($data,"encode"));
	}
	
	function pdf(){
		$data = array();
		$data[] = $_GET["condpdf"];
		$arrs = $this->generateData($data,"decode");
		$arrs[0] = urldecode($arrs[0]);

		$tx = explode("||",$arrs[0]);
		$locat = $tx[0];
		$fromdate = $tx[1];
		$todate = $tx[2];
		$orderby = $tx[3];
		$layout = $tx[4];

		$cond = "";
		$rpcond = "";
		
		if($locat != ""){
			$cond .= " and a.LOCAT = '".$locat."'";
			$rpcond .= "  สาขา ".$locat;
		}
		
		if($fromdate != ""){
			$cond .= " and a.YDATE >= '".$this->Convertdate(1,$fromdate)."'";
			$rpcond .= "  วันที่ยึดรถ ".$fromdate;
		}
		
		if($todate != ""){
			$cond .= " and a.YDATE <= '".$this->Convertdate(1,$todate)."'";
			$rpcond .= "  ถึงวันที่ ".$todate;
		}
		
		$sql = "
				select a.LOCAT, a.CONTNO, a.CUSCOD, b.SNAM, b.NAME1, b.NAME2, a.STRNO, a.SMPAY, convert(nvarchar,DATEADD(year,543,a.SDATE),103) as SDATE, 
				a.KEYINPRC, a.TOTPRC, a.EXP_PRD, a.EXP_AMT, convert(nvarchar,DATEADD(year,543,a.YDATE),103) as YDATES, a.CHECKER, a.BILLCOLL, c.CRLOCAT
				from  {$this->MAuth->getdb('ARMAST')} a
				left join {$this->MAuth->getdb('CUSTMAST')} b on a.CUSCOD = b.CUSCOD
				left join {$this->MAuth->getdb('INVTRAN')} c on a.STRNO = c.STRNO
				where a.YSTAT = 'Y' ".$cond."
				order by ".$orderby."
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$sql = "
				select 'รวมทั้งหมด' as TOTAL, 
				sum(A.NPRICE) as NPRICE, sum(A.VATPRC) as VATPRC, sum(A.TOTPRC) as TOTPRC, sum(A.NPAYRES) as NPAYRES, sum(A.VATPRES) as VATPRES, 
				sum(A.TOTPRES) as TOTPRES, sum(A.SMPAY-isnull(B.VPY,0)) as SMPAY, sum(isnull(B.VPY,0)) as VPY, sum(A.SMPAY) as TOTSMPAY, 
				sum(A.NPRICE-(A.SMPAY-isnull(B.VPY,0))) as ARBALANC, sum(A.VATPRC-isnull(B.VPY,0)) as ARVAT, 
				sum(A.TOTPRC-A.SMPAY) as TOTAR, sum(A.SMCHQ-isnull(B.VCQ,0)) as SMCHQ, sum(isnull(B.VCQ,0)) as VCQ, sum(A.SMCHQ) as SMCHQ, 
				sum(A.NPRICE-(A.SMPAY-isnull(B.VPY,0))-(A.SMCHQ-isnull(B.VCQ,0))) as BALANCE, sum(A.VATPRC-isnull(B.VPY,0)-isnull(B.VCQ,0)) as VATBALNCE,
				sum(A.TOTPRC-A.SMPAY-A.SMCHQ) as TOTBALANCE
				from #SALE A
				left join #VAT B on A.CONTNO = B.CONTNO
		";
		//echo $sql; exit;
		$query2 = $this->db->query($sql);
		
		$head = ""; $html = ""; $i=0; 
	
		$head = "
				<tr >
				<th style='border-bottom:0.1px solid black;text-align:left;'>#</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>สาขา</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>เลขที่สัญญา</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>ชื่อ - นามสกุล</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>เลขตัวถัง</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>วันทำสัญญา</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ราคาขาย</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ชำระแล้ว</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ค้างชำระ<br>(บาท)</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ค้างชำระ<br>(งวด)</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>วันที่ยึด</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>สาขาที่เก็บ</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>Billcoll</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>Checker</th>
				</tr>
		";
		
		$No = 1;
		if($query->row()){
			foreach($query->result() as $row){	
				$html .= "
					<tr class='trow' seq=".$No.">
						<td style='width:30px;'>".$No++."</td>
						<td	style='width:40px;'>".$row->LOCAT."</td>
						<td style='width:80px;'>".$row->CONTNO."</td>
						<td style='width:200px;'>".($row->SNAM.$row->NAME1.' '.$row->NAME2.' ('.$row->CUSCOD.')')."</td>
						<td style='width:125px;'>".$row->STRNO."</td>
						<td style='width:70px;'>".$row->SDATE."</td>
						<td style='width:75px;' align='right'>".number_format($row->TOTPRC,2)."</td>
						<td style='width:75px;' align='right'>".number_format($row->SMPAY,2)."</td>
						<td style='width:75px;' align='right'>".number_format($row->EXP_AMT,2)."</td>
						<td style='width:60px;' align='right'>".number_format($row->EXP_PRD)."&nbsp;</td>
						<td style='width:70px;'>".$row->YDATES."</td>
						<td style='width:50px;'>".$row->CRLOCAT."</td>
						<td style='width:45px;'>".$row->BILLCOLL."</td>
						<td style='width:45px;'>".$row->CHECKER."</td>
					</tr>
				";	
			}
		}
		
		$mpdf = new \Mpdf\Mpdf([
			'mode' => 'utf-8', 
			'format' => $layout,
			'margin_top' => 10, 	//default = 16
			'margin_left' => 10, 	//default = 15
			'margin_right' => 10, 	//default = 15
			'margin_bottom' => 10, 	//default = 16
			'margin_header' => 9, 	//default = 9
			'margin_footer' => 9, 	//default = 9
		]);
		
		$content = "
			<table class='wf' style='font-size:7.5pt;height:700px;border-collapse:collapse;line-height:23px;overflow:wrap;vertical-align:text-top;'>
				<tbody>
					<tr>
						<th colspan='14' style='font-size:10pt;'>รายงานรถยึดรอไถ่ถอน </th>
					</tr>
					<tr>
						<td colspan='14' style='font-size:8pt;height:35px;border-bottom:0.1px solid black;text-align:center;'>".$rpcond."</td>
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