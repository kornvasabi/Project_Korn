<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//BEE+
class ReportKangdue extends MY_Controller {
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
						<div class='col-sm-12 col-xs-12 bg-info' style='border:5px solid white;height:75px;text-align:center;font-size:12pt;color:white;font-weight:bold;'>	
							<br>รายงานภาษีค่างวดค้างชำระ<br>
						</div>
						<div class='col-sm-8 col-xs-8 col-sm-offset-2'>
							<br>
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group'>
									ณ วันที่
									<input type='text' id='TODATE' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' placeholder='ถึงวันที่' value='".$this->today('today')."' style='font-size:10.5pt'>
								</div>
							</div>
						</div>
						<div class='col-sm-8 col-xs-8 col-sm-offset-2'>
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group'>
									สาขา
									<select id='LOCAT1' class='form-control input-sm' data-placeholder='สาขา'></select>
								</div>
							</div>
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group'>
									จากเลขที่สัญญา
									<select id='CONTNO1' class='form-control input-sm' data-placeholder='จากเลขที่สัญญา'></select>
								</div>
							</div>
						</div>
						<div class='col-sm-8 col-xs-8 col-sm-offset-2'>
							<br>
							<div class='col-sm-6 col-xs-6'>	
								รูปแบบการพิมพ์
								<div class='col-sm-12 col-xs-12' style='border:0.1px dotted #d6d6d6;'>	
									<div class='col-sm-12 col-xs-12'>
										<div class='form-group'>
											<br>
											<input type= 'radio' id='hor' name='layout' checked> แนวนอน
											<br><br>
											<input type= 'radio' id='ver' name='layout'> แนวตั้ง
											<br>
										</div>
									</div>
								</div>
							</div>
							<div class='col-sm-6 col-xs-6'>	
								เรียงลำดับข้อมูล
								<div class='col-sm-12 col-xs-12' style='border:0.1px dotted #d6d6d6;'>	
									<div class='col-sm-12 col-xs-12'>
										<div class='form-group'>
											<br>
											<input type= 'radio' id='locat' name='orderby' checked> ตามรหัสสาขา
											<br>
											<input type= 'radio' id='contno' name='orderby'> ตามเลขสัญญา
											<br>
											<input type= 'radio' id='cuscod' name='orderby'> ตามชื่อลูกค้า
											<br>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class='row' style='height:10%;'>
						<div class='col-sm-12 col-xs-12'><br>	
							<button id='btnt1search' class='btn btn-info btn-sm' style='width:100%;font-size:10.5pt;'><span class='glyphicon glyphicon-search'> แสดง</span></button>
						</div>
					</div>
				</div>
			</div>
		";
		
		$html.= "<script src='".base_url('public/js/SYS07/ReportKangdue.js')."'></script>";
		echo $html;
	}
	
	function search(){
		$LOCAT1		= $_REQUEST["LOCAT1"];
		$CONTNO1 	= str_replace(chr(0),'',$_REQUEST["CONTNO1"]);
		$TODATE 	= $this->Convertdate(1,$_REQUEST["TODATE"]);
		$order 		= $_REQUEST["order"];
		
		$cond = ""; $rpcond = "";
		
		if($LOCAT1 != ""){
			$cond .= "";
			$rpcond .= "  สาขา ".$LOCAT1;
		}
		
		if($CONTNO1 != ""){
			$cond .= "";
			$rpcond .= "  เลขที่สัญญา ".$CONTNO1;
		}

		$orderby = "";
		if($order == "locat"){
			$orderby = "order by A.LOCAT, A.CONTNO";
		}else if($order == "contno"){
			$orderby = "order by A.CONTNO"; 
		}else if($order == "cuscod"){
			$orderby = "order by B.NAME1"; 
		}
		
		$sql = "
				declare @todate varchar(8) 	= '".$TODATE."' ;
				declare @locat 	varchar(10) = '".$LOCAT1."%' ;
				declare @contno varchar(20) = '".$CONTNO1."%' ;

				IF OBJECT_ID('tempdb..#main') IS NOT NULL DROP TABLE #main
				select *
				into #main
				from(
					select LOCAT, CONTNO, CUSCOD, SDATE, DAMT_N, DAMT_V, DAMT, PAYAMT_N, PAYAMT_V, PAYAMT, KDAMT_N, KDAMT_V, KDAMT, VKANG-DAMT_V as VKANG
					from(
						select A.LOCAT, A.CONTNO, A.CUSCOD, A.SDATE, A.VKANG, SUM(B.DAMT) AS DAMT, SUM(B.N_DAMT) AS DAMT_N, SUM(B.V_DAMT) AS DAMT_V,  
						(	SELECT SUM(PAYAMT) FROM {$this->MAuth->getdb('CHQTRAN')} 
							WHERE CONTNO = A.CONTNO AND LOCATPAY = A.LOCAT AND FLAG <> 'C' AND PAYFOR IN ('006','007') AND PAYDT <=@todate
						) AS PAYAMT, 
						(	SELECT SUM(PAYAMT_N) FROM {$this->MAuth->getdb('CHQTRAN')} 
							WHERE CONTNO = A.CONTNO AND LOCATPAY = A.LOCAT AND FLAG <> 'C' AND PAYFOR IN ('006','007') AND PAYDT <= @todate
						) AS PAYAMT_N, 
						(	SELECT SUM(PAYAMT_V) FROM {$this->MAuth->getdb('CHQTRAN')} 
							WHERE CONTNO = A.CONTNO AND LOCATPAY = A.LOCAT AND FLAG <> 'C' AND PAYFOR IN ('006','007')AND PAYDT <=@todate
						)AS PAYAMT_V, 
						SUM(B.DAMT)-(
							SELECT SUM(PAYAMT) FROM {$this->MAuth->getdb('CHQTRAN')} 
							WHERE CONTNO = A.CONTNO AND LOCATPAY = A.LOCAT  AND FLAG <> 'C' AND PAYFOR IN ('006','007') AND PAYDT <=@todate 
						) AS KDAMT, 
						SUM(B.N_DAMT)-(
							SELECT SUM(PAYAMT_N) FROM {$this->MAuth->getdb('CHQTRAN')} 
							WHERE CONTNO = A.CONTNO AND LOCATPAY = A.LOCAT AND FLAG <> 'C' AND PAYFOR IN ('006','007') AND PAYDT <=@todate
						) AS KDAMT_N, 
						SUM(B.V_DAMT)-(
							SELECT SUM(PAYAMT_V) FROM {$this->MAuth->getdb('CHQTRAN')} 
							WHERE CONTNO = A.CONTNO AND LOCATPAY = A.LOCAT AND FLAG <> 'C' AND PAYFOR IN ('006','007') AND PAYDT <=@todate
						) AS KDAMT_V  
						from {$this->MAuth->getdb('ARMAST')} A, {$this->MAuth->getdb('ARPAY')} B  
						where (A.CONTNO = B.CONTNO) AND (A.LOCAT = B.LOCAT) AND (A.LOCAT LIKE @locat)  AND (A.CONTNO LIKE @contno) AND (A.FDATE <=@todate)  
						AND (A.TOTPRC > A.SMPAY OR (A.TOTPRC = A.SMPAY AND A.LPAYD >@todate)) AND A.TOTPRC > 0 AND B.DDATE <=@todate  
						GROUP BY A.LOCAT,A.CONTNO,A.CUSCOD,A.SDATE,A.VKANG 
						HAVING	SUM(B.DAMT)-(SELECT SUM( PAYAMT)  FROM {$this->MAuth->getdb('CHQTRAN')} WHERE CONTNO = A.CONTNO AND LOCATPAY = A.LOCAT  AND FLAG <> 'C' 
						AND PAYFOR IN ('006','007') AND PAYDT <=@todate )  > 0  

						UNION  

						select A.LOCAT, A.CONTNO, A.CUSCOD, A.SDATE, A.VKANG, SUM(B.DAMT) AS DAMT, SUM(B.N_DAMT) AS DAMT_N, SUM(B.V_DAMT) AS DAMT_V, 0 AS PAYAMT,
						0 AS PAYAMT_N, 0 AS PAYAMT_V, SUM(B.DAMT) AS KDAMT, SUM(B.N_DAMT) AS KDAMT_N, SUM(B.V_DAMT) AS KDAMT_V  
						from {$this->MAuth->getdb('ARMAST')} A, {$this->MAuth->getdb('ARPAY')} B  
						where (A.CONTNO = B.CONTNO) AND (A.LOCAT = B.LOCAT) AND (A.LOCAT LIKE @locat) AND (A.CONTNO LIKE @contno) AND (A.FDATE <=@todate)  
						AND (A.TOTPRC > A.SMPAY OR (A.TOTPRC = A.SMPAY AND A.LPAYD >@todate)) AND A.TOTPRC > 0 AND B.DDATE <=@todate  
						GROUP BY A.LOCAT, A.CONTNO, A.CUSCOD, A.SDATE, A.VKANG 
						HAVING (SELECT SUM(PAYAMT) FROM {$this->MAuth->getdb('CHQTRAN')} WHERE CONTNO = A.CONTNO AND LOCATPAY = A.LOCAT  AND FLAG <> 'C' AND PAYFOR IN ('006','007') 
						AND PAYDT <=@todate ) IS NULL  

						UNION  

						select A.LOCAT, A.CONTNO, A.CUSCOD, A.SDATE, A.VKANG, SUM(B.DAMT) AS DAMT, SUM(B.N_DAMT) AS DAMT_N, SUM(B.V_DAMT) AS DAMT_V,  
						(	SELECT SUM(PAYAMT) FROM {$this->MAuth->getdb('CHQTRAN')} 
							WHERE CONTNO = A.CONTNO AND LOCATPAY = A.LOCAT AND FLAG <> 'C' AND PAYFOR IN ('006','007') AND PAYDT <=@todate
						) AS PAYAMT, 
						(	SELECT SUM(PAYAMT_N) FROM {$this->MAuth->getdb('CHQTRAN')} 
							WHERE CONTNO = A.CONTNO AND LOCATPAY = A.LOCAT AND FLAG <> 'C' AND PAYFOR IN ('006','007') AND PAYDT <=@todate
						) AS PAYAMT_N, 
						(	SELECT SUM(PAYAMT_V) FROM {$this->MAuth->getdb('CHQTRAN')} 
							WHERE CONTNO = A.CONTNO AND LOCATPAY = A.LOCAT AND FLAG <> 'C' AND PAYFOR IN ('006','007')AND PAYDT <=@todate
						)AS PAYAMT_V, 
						SUM(B.DAMT)-(
							SELECT SUM(PAYAMT) FROM {$this->MAuth->getdb('CHQTRAN')} 
							WHERE CONTNO = A.CONTNO AND LOCATPAY = A.LOCAT  AND FLAG <> 'C' AND PAYFOR IN ('006','007') AND PAYDT <=@todate
						)AS KDAMT, 
						SUM(B.N_DAMT)-(
							SELECT SUM(PAYAMT_N) FROM {$this->MAuth->getdb('CHQTRAN')} 
							WHERE CONTNO = A.CONTNO AND LOCATPAY = A.LOCAT AND FLAG <> 'C' AND PAYFOR IN ('006','007') AND PAYDT <=@todate
						) AS KDAMT_N, 
						SUM(B.V_DAMT)-(
							SELECT SUM(PAYAMT_V) FROM {$this->MAuth->getdb('CHQTRAN')} 
							WHERE CONTNO = A.CONTNO AND LOCATPAY = A.LOCAT AND FLAG <> 'C' AND PAYFOR IN ('006','007') AND PAYDT <=@todate
						) AS KDAMT_V  
						from {$this->MAuth->getdb('HARMAST')} A, {$this->MAuth->getdb('HARPAY')} B  
						where (A.CONTNO = B.CONTNO) AND (A.LOCAT = B.LOCAT) AND (A.LOCAT LIKE @locat) AND (A.CONTNO LIKE @contno ) AND (A.FDATE <=@todate)  
						AND (A.TOTPRC > A.SMPAY OR (A.TOTPRC = A.SMPAY AND A.LPAYD >@todate)) AND (SELECT DATE1 FROM {$this->MAuth->getdb('CHGAR_VIEW')} WHERE CONTNO = A.CONTNO 
						AND LOCAT = LOCAT ) > @todate AND  A.TOTPRC > 0 AND B.DDATE <=@todate  
						GROUP BY A.LOCAT, A.CONTNO, A.CUSCOD, A.SDATE, A.VKANG 
						HAVING SUM(B.DAMT)-(SELECT SUM(PAYAMT) FROM {$this->MAuth->getdb('CHQTRAN')} WHERE CONTNO = A.CONTNO AND LOCATPAY = A.LOCAT  AND FLAG <> 'C' 
						AND PAYFOR IN ('006','007') AND PAYDT <=@todate) > 0  

						UNION  

						select A.LOCAT, A.CONTNO, A.CUSCOD, A.SDATE, A.VKANG, SUM(B.DAMT) AS DAMT, SUM(B.N_DAMT) AS DAMT_N, SUM(B.V_DAMT) AS DAMT_V,  
						0 AS PAYAMT, 0 AS PAYAMT_N, 0 AS PAYAMT_V, SUM(B.DAMT) AS KDAMT, SUM(B.N_DAMT) AS KDAMT_N, SUM(B.V_DAMT) AS KDAMT_V  
						from {$this->MAuth->getdb('HARMAST')} A, {$this->MAuth->getdb('HARPAY')} B  
						where (A.CONTNO = B.CONTNO) AND (A.LOCAT = B.LOCAT) AND (A.LOCAT LIKE @locat) AND (A.CONTNO LIKE @contno)  AND (A.FDATE <=@todate)  
						AND (A.TOTPRC > A.SMPAY OR (A.TOTPRC = A.SMPAY AND A.LPAYD >@todate)) AND (SELECT DATE1 FROM {$this->MAuth->getdb('CHGAR_VIEW')} WHERE CONTNO = A.CONTNO 
						AND LOCAT = LOCAT) > @todate AND A.TOTPRC > 0 AND B.DDATE <=@todate  
						GROUP BY A.LOCAT, A.CONTNO, A.CUSCOD, A.SDATE, A.VKANG 
						HAVING (SELECT SUM(PAYAMT) FROM {$this->MAuth->getdb('CHQTRAN')} WHERE CONTNO = A.CONTNO AND LOCATPAY = A.LOCAT  AND FLAG <> 'C' AND PAYFOR IN ('006','007') 
						AND PAYDT <= @todate ) IS NULL   
					)A
				)main
		";//echo $sql; 
		$query = $this->db->query($sql);
		
		$sql = "
				select A.LOCAT, A.CONTNO, B.SNAM+B.NAME1+' '+B.NAME2 as CUSNAME, convert(char,A.SDATE,112) as SDATE, DAMT_N, DAMT_V, DAMT, 
				PAYAMT_N, PAYAMT_V, PAYAMT, KDAMT_N, KDAMT_V, KDAMT, VKANG, NOPAYKANG
				from #main A
				left join {$this->MAuth->getdb('CUSTMAST')} B on A.CUSCOD = B.CUSCOD
				left join (
					select CONTNO, LOCAT, case when MIN(NOPAY) = MAX(NOPAY) then  convert(nvarchar(2),MAX(NOPAY)) else 
					convert(nvarchar(2),MIN(NOPAY))+'-' + convert(nvarchar(2),MAX(NOPAY)) end as NOPAYKANG
					from {$this->MAuth->getdb('ARPAY')} 
					where DDATE < '".$TODATE."' and DAMT-PAYMENT > 0 and CONTNO in (select CONTNO from #main)
					group by CONTNO, LOCAT
				)C on A.CONTNO = C.CONTNO and A.LOCAT = C.LOCAT
				".$orderby."
		";//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$sql = "
				select  'รวมทั้งสิ้น '+convert(nvarchar(8),count(CONTNO))+' รายการ' as Total, sum(DAMT_N) as sumDAMT_N, sum(DAMT_V) as sumDAMT_V, 
				sum(DAMT) as sumDAMT, sum(PAYAMT_N) as sumPAYAMT_N, sum(PAYAMT_V) as sumPAYAMT_V, sum(PAYAMT) as sumPAYAMT, 
				sum(KDAMT_N) as sumKDAMT_N, sum(KDAMT_V) as sumKDAMT_V, sum(KDAMT) as sumKDAMT, sum(VKANG) as sumVKANG
				from #main
		";//echo $sql; exit;
		$query2 = $this->db->query($sql);
		
		$head = ""; $html = ""; $head2 = "";  $report = ""; $sumreport = ""; $sumreport2 = ""; $i = 0; 
		
		$head = "<tr style='height:25px;background-color:#D3ECDC;'>
					<th style='vertical-align:middle;' rowspan='2'>#</th>
					<th style='vertical-align:middle;' rowspan='2'>สาขา</th>
					<th style='vertical-align:middle;' rowspan='2'>เลขที่สัญญา</th>
					<th style='vertical-align:middle;' rowspan='2'>ชื่อ-นามสกุล</th>
					<th style='vertical-align:middle;' rowspan='2'>วันที่ขาย</th>
					<th style='vertical-align:middle;' class='text-center' 	colspan='3'>ครบกำหนดชำระ</th>
					<th style='vertical-align:middle;' class='text-center' 	colspan='3'>รับชำระแล้ว</th>
					<th style='vertical-align:middle;' class='text-center' 	colspan='3'>ค่างวดค้างชำระ</th>
					<th style='vertical-align:middle;' class='text-right' 	rowspan='2'>ภาษี<br>ยังไม่ถึงดิว</th>
					<th style='vertical-align:middle;' class='text-center' 	rowspan='2'>ค้างงวดที่</th>
				</tr>
				<tr style='height:25px;background-color:#D3ECDC;'>
					<th style='vertical-align:middle;' class='text-right'>มูลค่า<br>ค่างวด</th>
					<th style='vertical-align:middle;' class='text-right'>ภาษี<br>ค่างวด</th>
					<th style='vertical-align:middle;' class='text-right'>ค่างวด<br>รวมภาษี</th>
					<th style='vertical-align:middle;' class='text-right'>มูลค่า<br>ค่างวด</th>
					<th style='vertical-align:middle;' class='text-right'>ภาษี<br>ค่างวด</th>
					<th style='vertical-align:middle;' class='text-right'>ค่างวด<br>รวมภาษี</th>
					<th style='vertical-align:middle;' class='text-right'>มูลค่า<br>ค่างวด</th>
					<th style='vertical-align:middle;' class='text-right'>ภาษี<br>ค่างวด</th>
					<th style='vertical-align:middle;' class='text-right'>ค่างวด<br>รวมภาษี</th>
				</tr>
		";
		
		$head2 = "<tr>
					<th style='vertical-align:middle;taxt-align:left;' rowspan='2'>#</th>
					<th style='vertical-align:middle;taxt-align:left;' rowspan='2'>สาขา</th>
					<th style='vertical-align:middle;taxt-align:left;' rowspan='2'>เลขที่สัญญา</th>
					<th style='vertical-align:middle;taxt-align:left;' rowspan='2'>ชื่อ-นามสกุล</th>
					<th style='vertical-align:middle;taxt-align:left;' rowspan='2'>วันที่ขาย</th>
					<th style='vertical-align:middle;taxt-align:center;' colspan='3'>ครบกำหนดชำระ</th>
					<th style='vertical-align:middle;taxt-align:center;' colspan='3'>รับชำระแล้ว</th>
					<th style='vertical-align:middle;taxt-align:center;' colspan='3'>ค่างวดค้างชำระ</th>
					<th style='vertical-align:middle;taxt-align:right;' rowspan='2'>ภาษียังไม่ถึงดิว</th>
					<th style='vertical-align:middle;taxt-align:center;' rowspan='2'>ค้างงวดที่</th>
				</tr>
				<tr>
					<th style='vertical-align:middle;taxt-align:right;'>มูลค่าค่างวด</th>
					<th style='vertical-align:middle;taxt-align:right;'>ภาษีค่างวด</th>
					<th style='vertical-align:middle;taxt-align:right;'>ค่างวดรวมภาษี</th>
					<th style='vertical-align:middle;taxt-align:right;'>มูลค่าค่างวด</th>
					<th style='vertical-align:middle;taxt-align:right;'>ภาษีค่างวด</th>
					<th style='vertical-align:middle;taxt-align:right;'>ค่างวดรวมภาษี</th>
					<th style='vertical-align:middle;taxt-align:right;'>มูลค่าค่างวด</th>
					<th style='vertical-align:middle;taxt-align:right;'>ภาษีค่างวด</th>
					<th style='vertical-align:middle;taxt-align:right;'>ค่างวดรวมภาษี</th>
				</tr>
		";
		
		$NRow = 1;
		if($query->row()){
			foreach($query->result() as $row){$i++;
				$html .= "
					<tr class='trow' seq=".$NRow.">
						<td seq=".$NRow.">".$NRow++."</td>
						<td>".$row->LOCAT."</td>
						<td>".$row->CONTNO."</td>
						<td>".$row->CUSNAME."</td>
						<td>".$this->Convertdate(2,$row->SDATE)."</td>
						<td align='right'>".number_format($row->DAMT_N,2)."</td>
						<td align='right'>".number_format($row->DAMT_V,2)."</td>
						<td align='right'>".number_format($row->DAMT,2)."</td>
						<td align='right'>".number_format($row->PAYAMT_N,2)."</td>
						<td align='right'>".number_format($row->PAYAMT_V,2)."</td>
						<td align='right'>".number_format($row->PAYAMT,2)."</td>
						<td align='right'>".number_format($row->KDAMT_N,2)."</td>
						<td align='right'>".number_format($row->KDAMT_V,2)."</td>
						<td align='right'>".number_format($row->KDAMT,2)."</td>
						<td align='right'>".number_format($row->VKANG,2)."</td>
						<td align='center'>".$row->NOPAYKANG."</td>
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
						<td style='mso-number-format:\"\@\";'>".$row->CUSNAME."</td>
						<td style='mso-number-format:\"\@\";'>".$this->Convertdate(2,$row->SDATE)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->DAMT_N,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->DAMT_V,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->DAMT,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->PAYAMT_N,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->PAYAMT_V,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->PAYAMT,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->KDAMT_N,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->KDAMT_V,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->KDAMT,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->VKANG,2)."</td>
						<td style='mso-number-format:\"\@\";taxt-align:center;'>".$row->NOPAYKANG."</td>
					</tr>
				";	
			}
		}
		
		if($query2->row()){
			foreach($query2->result() as $row){
				$sumreport .= "
					<tr class='trow' style='height:25px;background-color:#D3ECDC;'>
						<th style='border:0px;vertical-align:middle;text-align:left;' colspan='5'>".$row->Total."</th>
						<th style='border:0px;vertical-align:middle;text-align:right;'>".number_format($row->sumDAMT_N,2)."</th>
						<th style='border:0px;vertical-align:middle;text-align:right;'>".number_format($row->sumDAMT_V,2)."</th>
						<th style='border:0px;vertical-align:middle;text-align:right;'>".number_format($row->sumDAMT,2)."</th>
						<th style='border:0px;vertical-align:middle;text-align:right;'>".number_format($row->sumPAYAMT_N,2)."</th>
						<th style='border:0px;vertical-align:middle;text-align:right;'>".number_format($row->sumPAYAMT_V,2)."</th>
						<th style='border:0px;vertical-align:middle;text-align:right;'>".number_format($row->sumPAYAMT,2)."</th>
						<th style='border:0px;vertical-align:middle;text-align:right;'>".number_format($row->sumKDAMT_N,2)."</th>
						<th style='border:0px;vertical-align:middle;text-align:right;'>".number_format($row->sumKDAMT_V,2)."</th>
						<th style='border:0px;vertical-align:middle;text-align:right;'>".number_format($row->sumKDAMT,2)."</th>
						<th style='border:0px;vertical-align:middle;text-align:right;'>".number_format($row->sumVKANG,2)."</th>
						<th style='border:0px;vertical-align:middle;text-align:right;'></th>
					</tr>
				";	
			}
		}
		
		if($query2->row()){
			foreach($query2->result() as $row){
				$sumreport2 .= "
					<tr class='trow'>
						<th style='mso-number-format:\"\@\";text-align:left;' colspan='5'>".$row->Total."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumDAMT_N,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumDAMT_V,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumDAMT,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumPAYAMT_N,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumPAYAMT_V,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumPAYAMT,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumKDAMT_N,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumKDAMT_V,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumKDAMT,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumVKANG,2)."</th>
						<th ></th>
					</tr>
				";	
			}
		}
		
		if($i>0){
			$html = "
				<div id='table-fixed-ReportKangdue' class='col-sm-12' style='height:100%;width:100%;overflow:auto;font-size:9pt;'>
					<table id='table-ReportKangdue' style='background-color:white;' class='col-sm-12 display table table-bordered' cellspacing='0' width='calc(100% - 1px)'>
						<thead>
						<tr style='height:40px;'>
							<th colspan='16' style='font-size:12pt;border:0px;vertical-align;middle;text-align:center;'>รายงานภาษีค่างวดค้างชำระ</th>
						</tr>
						<tr style='height:25px;'>
							<td colspan='16' style='border-bottom:1px solid #ddd;vertical-align;middle;text-align:center;'>ณ วันที่ ".$_REQUEST["TODATE"]." ".$rpcond."   ออกรายงาน ณ วันที่ ".$this->today('today')."</td>
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
			<div id='table-fixed-ReportKangdue2' class='col-sm-12' style='height:100%;width:100%;overflow:auto;'>
				<table id='table-ReportKangdue2' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='100%'>
					<thead>
						<tr>
							<th colspan='16' style='font-size:12pt;border:0px;text-align:center;'>รายงานภาษีค่างวดค้างชำระ</th>
						</tr>
						<tr>
							<td colspan='16' style='border:0px;text-align:center;'>ณ วันที่ ".$_REQUEST["TODATE"]." ".$rpcond."  ออกรายงาน ณ วันที่ ".$this->today('today')."</td>
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
		$data[] = 	urlencode(
						$_REQUEST["LOCAT1"].'||'.
						$_REQUEST["CONTNO1"].'||'.
						$_REQUEST["TODATE"].'||'.
						$_REQUEST["order"].'||'.
						$_REQUEST["layout"]
					);
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
		$CONTNO1 	= str_replace(chr(0),'',$tx[1]);
		$TODATE 	= $this->Convertdate(1,$tx[2]);
		$order 		= $tx[3];
		$layout 	= $tx[4];
		
		$cond = ""; $rpcond = ""; 
		
		if($LOCAT1 != ""){
			$cond .= "";
			$rpcond .= "  สาขา ".$LOCAT1;
		}
		
		if($CONTNO1 != ""){
			$cond .= "";
			$rpcond .= "  เลขที่สัญญา ".$CONTNO1;
		}

		$orderby = ""; $ordername = "";
		if($order == "locat"){
			$orderby = "order by A.LOCAT, A.CONTNO";
			$ordername = "สาขา";
		}else if($order == "contno"){
			$orderby = "order by A.CONTNO"; 
			$ordername = "เลขที่สัญญา";
		}else if($order == "cuscod"){
			$orderby = "order by B.NAME1"; 
			$ordername = "ชื่อลูกค้า";
		}
		
		$sql = "
				declare @todate varchar(8) 	= '".$TODATE."' ;
				declare @locat 	varchar(10) = '".$LOCAT1."%' ;
				declare @contno varchar(20) = '".$CONTNO1."%' ;

				IF OBJECT_ID('tempdb..#main') IS NOT NULL DROP TABLE #main
				select *
				into #main
				from(
					select LOCAT, CONTNO, CUSCOD, SDATE, DAMT_N, DAMT_V, DAMT, PAYAMT_N, PAYAMT_V, PAYAMT, KDAMT_N, KDAMT_V, KDAMT, VKANG-DAMT_V as VKANG
					from(
						select A.LOCAT, A.CONTNO, A.CUSCOD, A.SDATE, A.VKANG, SUM(B.DAMT) AS DAMT, SUM(B.N_DAMT) AS DAMT_N, SUM(B.V_DAMT) AS DAMT_V,  
						(	SELECT SUM(PAYAMT) FROM {$this->MAuth->getdb('CHQTRAN')} 
							WHERE CONTNO = A.CONTNO AND LOCATPAY = A.LOCAT AND FLAG <> 'C' AND PAYFOR IN ('006','007') AND PAYDT <=@todate
						) AS PAYAMT, 
						(	SELECT SUM(PAYAMT_N) FROM {$this->MAuth->getdb('CHQTRAN')} 
							WHERE CONTNO = A.CONTNO AND LOCATPAY = A.LOCAT AND FLAG <> 'C' AND PAYFOR IN ('006','007') AND PAYDT <= @todate
						) AS PAYAMT_N, 
						(	SELECT SUM(PAYAMT_V) FROM {$this->MAuth->getdb('CHQTRAN')} 
							WHERE CONTNO = A.CONTNO AND LOCATPAY = A.LOCAT AND FLAG <> 'C' AND PAYFOR IN ('006','007')AND PAYDT <=@todate
						)AS PAYAMT_V, 
						SUM(B.DAMT)-(
							SELECT SUM(PAYAMT) FROM {$this->MAuth->getdb('CHQTRAN')} 
							WHERE CONTNO = A.CONTNO AND LOCATPAY = A.LOCAT  AND FLAG <> 'C' AND PAYFOR IN ('006','007') AND PAYDT <=@todate 
						) AS KDAMT, 
						SUM(B.N_DAMT)-(
							SELECT SUM(PAYAMT_N) FROM {$this->MAuth->getdb('CHQTRAN')} 
							WHERE CONTNO = A.CONTNO AND LOCATPAY = A.LOCAT AND FLAG <> 'C' AND PAYFOR IN ('006','007') AND PAYDT <=@todate
						) AS KDAMT_N, 
						SUM(B.V_DAMT)-(
							SELECT SUM(PAYAMT_V) FROM {$this->MAuth->getdb('CHQTRAN')} 
							WHERE CONTNO = A.CONTNO AND LOCATPAY = A.LOCAT AND FLAG <> 'C' AND PAYFOR IN ('006','007') AND PAYDT <=@todate
						) AS KDAMT_V  
						from {$this->MAuth->getdb('ARMAST')} A, {$this->MAuth->getdb('ARPAY')} B  
						where (A.CONTNO = B.CONTNO) AND (A.LOCAT = B.LOCAT) AND (A.LOCAT LIKE @locat)  AND (A.CONTNO LIKE @contno) AND (A.FDATE <=@todate)  
						AND (A.TOTPRC > A.SMPAY OR (A.TOTPRC = A.SMPAY AND A.LPAYD >@todate)) AND A.TOTPRC > 0 AND B.DDATE <=@todate  
						GROUP BY A.LOCAT,A.CONTNO,A.CUSCOD,A.SDATE,A.VKANG 
						HAVING	SUM(B.DAMT)-(SELECT SUM( PAYAMT)  FROM {$this->MAuth->getdb('CHQTRAN')} WHERE CONTNO = A.CONTNO AND LOCATPAY = A.LOCAT  AND FLAG <> 'C' 
						AND PAYFOR IN ('006','007') AND PAYDT <=@todate )  > 0  

						UNION  

						select A.LOCAT, A.CONTNO, A.CUSCOD, A.SDATE, A.VKANG, SUM(B.DAMT) AS DAMT, SUM(B.N_DAMT) AS DAMT_N, SUM(B.V_DAMT) AS DAMT_V, 0 AS PAYAMT,
						0 AS PAYAMT_N, 0 AS PAYAMT_V, SUM(B.DAMT) AS KDAMT, SUM(B.N_DAMT) AS KDAMT_N, SUM(B.V_DAMT) AS KDAMT_V  
						from {$this->MAuth->getdb('ARMAST')} A, {$this->MAuth->getdb('ARPAY')} B  
						where (A.CONTNO = B.CONTNO) AND (A.LOCAT = B.LOCAT) AND (A.LOCAT LIKE @locat) AND (A.CONTNO LIKE @contno) AND (A.FDATE <=@todate)  
						AND (A.TOTPRC > A.SMPAY OR (A.TOTPRC = A.SMPAY AND A.LPAYD >@todate)) AND A.TOTPRC > 0 AND B.DDATE <=@todate  
						GROUP BY A.LOCAT, A.CONTNO, A.CUSCOD, A.SDATE, A.VKANG 
						HAVING (SELECT SUM(PAYAMT) FROM {$this->MAuth->getdb('CHQTRAN')} WHERE CONTNO = A.CONTNO AND LOCATPAY = A.LOCAT  AND FLAG <> 'C' AND PAYFOR IN ('006','007') 
						AND PAYDT <=@todate ) IS NULL  

						UNION  

						select A.LOCAT, A.CONTNO, A.CUSCOD, A.SDATE, A.VKANG, SUM(B.DAMT) AS DAMT, SUM(B.N_DAMT) AS DAMT_N, SUM(B.V_DAMT) AS DAMT_V,  
						(	SELECT SUM(PAYAMT) FROM {$this->MAuth->getdb('CHQTRAN')} 
							WHERE CONTNO = A.CONTNO AND LOCATPAY = A.LOCAT AND FLAG <> 'C' AND PAYFOR IN ('006','007') AND PAYDT <=@todate
						) AS PAYAMT, 
						(	SELECT SUM(PAYAMT_N) FROM {$this->MAuth->getdb('CHQTRAN')} 
							WHERE CONTNO = A.CONTNO AND LOCATPAY = A.LOCAT AND FLAG <> 'C' AND PAYFOR IN ('006','007') AND PAYDT <=@todate
						) AS PAYAMT_N, 
						(	SELECT SUM(PAYAMT_V) FROM {$this->MAuth->getdb('CHQTRAN')} 
							WHERE CONTNO = A.CONTNO AND LOCATPAY = A.LOCAT AND FLAG <> 'C' AND PAYFOR IN ('006','007')AND PAYDT <=@todate
						)AS PAYAMT_V, 
						SUM(B.DAMT)-(
							SELECT SUM(PAYAMT) FROM {$this->MAuth->getdb('CHQTRAN')} 
							WHERE CONTNO = A.CONTNO AND LOCATPAY = A.LOCAT  AND FLAG <> 'C' AND PAYFOR IN ('006','007') AND PAYDT <=@todate
						)AS KDAMT, 
						SUM(B.N_DAMT)-(
							SELECT SUM(PAYAMT_N) FROM {$this->MAuth->getdb('CHQTRAN')} 
							WHERE CONTNO = A.CONTNO AND LOCATPAY = A.LOCAT AND FLAG <> 'C' AND PAYFOR IN ('006','007') AND PAYDT <=@todate
						) AS KDAMT_N, 
						SUM(B.V_DAMT)-(
							SELECT SUM(PAYAMT_V) FROM {$this->MAuth->getdb('CHQTRAN')} 
							WHERE CONTNO = A.CONTNO AND LOCATPAY = A.LOCAT AND FLAG <> 'C' AND PAYFOR IN ('006','007') AND PAYDT <=@todate
						) AS KDAMT_V  
						from {$this->MAuth->getdb('HARMAST')} A, {$this->MAuth->getdb('HARPAY')} B  
						where (A.CONTNO = B.CONTNO) AND (A.LOCAT = B.LOCAT) AND (A.LOCAT LIKE @locat) AND (A.CONTNO LIKE @contno ) AND (A.FDATE <=@todate)  
						AND (A.TOTPRC > A.SMPAY OR (A.TOTPRC = A.SMPAY AND A.LPAYD >@todate)) AND (SELECT DATE1 FROM {$this->MAuth->getdb('CHGAR_VIEW')} WHERE CONTNO = A.CONTNO 
						AND LOCAT = LOCAT ) > @todate AND  A.TOTPRC > 0 AND B.DDATE <=@todate  
						GROUP BY A.LOCAT, A.CONTNO, A.CUSCOD, A.SDATE, A.VKANG 
						HAVING SUM(B.DAMT)-(SELECT SUM(PAYAMT) FROM {$this->MAuth->getdb('CHQTRAN')} WHERE CONTNO = A.CONTNO AND LOCATPAY = A.LOCAT  AND FLAG <> 'C' 
						AND PAYFOR IN ('006','007') AND PAYDT <=@todate) > 0  

						UNION  

						select A.LOCAT, A.CONTNO, A.CUSCOD, A.SDATE, A.VKANG, SUM(B.DAMT) AS DAMT, SUM(B.N_DAMT) AS DAMT_N, SUM(B.V_DAMT) AS DAMT_V,  
						0 AS PAYAMT, 0 AS PAYAMT_N, 0 AS PAYAMT_V, SUM(B.DAMT) AS KDAMT, SUM(B.N_DAMT) AS KDAMT_N, SUM(B.V_DAMT) AS KDAMT_V  
						from {$this->MAuth->getdb('HARMAST')} A, {$this->MAuth->getdb('HARPAY')} B  
						where (A.CONTNO = B.CONTNO) AND (A.LOCAT = B.LOCAT) AND (A.LOCAT LIKE @locat) AND (A.CONTNO LIKE @contno)  AND (A.FDATE <=@todate)  
						AND (A.TOTPRC > A.SMPAY OR (A.TOTPRC = A.SMPAY AND A.LPAYD >@todate)) AND (SELECT DATE1 FROM {$this->MAuth->getdb('CHGAR_VIEW')} WHERE CONTNO = A.CONTNO 
						AND LOCAT = LOCAT) > @todate AND A.TOTPRC > 0 AND B.DDATE <=@todate  
						GROUP BY A.LOCAT, A.CONTNO, A.CUSCOD, A.SDATE, A.VKANG 
						HAVING (SELECT SUM(PAYAMT) FROM {$this->MAuth->getdb('CHQTRAN')} WHERE CONTNO = A.CONTNO AND LOCATPAY = A.LOCAT  AND FLAG <> 'C' AND PAYFOR IN ('006','007') 
						AND PAYDT <= @todate ) IS NULL   
					)A
				)main
		";//echo $sql; 
		$query = $this->db->query($sql);
		
		$sql = "
				select A.LOCAT, A.CONTNO, B.SNAM+B.NAME1+' '+B.NAME2 as CUSNAME, convert(char,A.SDATE,112) as SDATE, DAMT_N, DAMT_V, DAMT, 
				PAYAMT_N, PAYAMT_V, PAYAMT, KDAMT_N, KDAMT_V, KDAMT, VKANG, NOPAYKANG
				from #main A
				left join {$this->MAuth->getdb('CUSTMAST')} B on A.CUSCOD = B.CUSCOD
				left join (
					select CONTNO, LOCAT, case when MIN(NOPAY) = MAX(NOPAY) then  convert(nvarchar(2),MAX(NOPAY)) else 
					convert(nvarchar(2),MIN(NOPAY))+'-' + convert(nvarchar(2),MAX(NOPAY)) end as NOPAYKANG
					from {$this->MAuth->getdb('ARPAY')} 
					where DDATE < '".$TODATE."' and DAMT-PAYMENT > 0 and CONTNO in (select CONTNO from #main)
					group by CONTNO, LOCAT
				)C on A.CONTNO = C.CONTNO and A.LOCAT = C.LOCAT
				".$orderby."
		";//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$sql = "
				select  'รวมทั้งสิ้น '+convert(nvarchar(8),count(CONTNO))+' รายการ' as Total, sum(DAMT_N) as sumDAMT_N, sum(DAMT_V) as sumDAMT_V, 
				sum(DAMT) as sumDAMT, sum(PAYAMT_N) as sumPAYAMT_N, sum(PAYAMT_V) as sumPAYAMT_V, sum(PAYAMT) as sumPAYAMT, 
				sum(KDAMT_N) as sumKDAMT_N, sum(KDAMT_V) as sumKDAMT_V, sum(KDAMT) as sumKDAMT, sum(VKANG) as sumVKANG
				from #main
		";//echo $sql; exit;
		$query2 = $this->db->query($sql);
		
		$sql = "
				SELECT COMP_NM, COMP_ADR1, COMP_ADR2, TELP, TAXID FROM {$this->MAuth->getdb('CONDPAY')}
		";//echo $sql; exit;
		$query3 = $this->db->query($sql);
		
		$row1		= $query3->row();
		$COMP_NM 	= $row1->COMP_NM;
		$COMP_ADR1 	= $row1->COMP_ADR1;
		$COMP_ADR2 	= $row1->COMP_ADR2;
		$TELP 		= $row1->TELP;
		$TAXID 		= $row1->TAXID;
		
		$LOCATCD = ""; $LOCATNM = ""; $LOCADDR1 = ""; $LOCADDR2 = "";
		if($LOCAT1 != ""){
			$sql = "
					SELECT LOCATCD,LOCATNM,LOCADDR1,LOCADDR2 from {$this->MAuth->getdb('INVLOCAT')} where LOCATCD = '".$LOCAT1."'
			";//echo $sql; exit;
			$query4 = $this->db->query($sql);
			
			$row2 		= $query4->row();
			$LOCATCD 	= $row2->LOCATCD;
			$LOCATNM 	= $row2->LOCATNM;
			$LOCADDR1 	= $row2->LOCADDR1;
			$LOCADDR2 	= $row2->LOCADDR2;
		}else{
			$LOCATNM 	= "";
			$LOCADDR1 	= $COMP_ADR1;
			$LOCADDR2 	= $COMP_ADR2;
		}
		
		$head = ""; $html = ""; $i=0; 
		
		$head = "
				<tr>
					<th width='40px' 	style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;text-align:left;' 	rowspan='2'>#</th>
					<th width='50px' 	style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;text-align:left;' 	rowspan='2'>สาขา</th>
					<th width='90px' 	style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;text-align:left;' 	rowspan='2'>เลขที่สัญญา<br>วันที่ขาย</th>
					<th width='130px' 	style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;text-align:left;' 	rowspan='2'>ชื่อ-นามสกุล</th>
					<th width='240px' 	style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;text-align:center;'	colspan='3'>ครบกำหนดชำระ</th>
					<th width='240px' 	style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;text-align:center;'	colspan='3'>รับชำระแล้ว</th>
					<th width='240px' 	style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;text-align:center;'	colspan='3'>ค่างวดค้างชำระ</th>
					<th width='80px' 	style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;text-align:right;' 	rowspan='2'>ภาษี<br>ยังไม่ถึงดิว</th>
					<th width='60px' 	style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;text-align:center;'	rowspan='2'>ค้างงวดที่</th>
				</tr>
				<tr>
					<th width='80px' style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>มูลค่า<br>ค่างวด</th>
					<th width='80px' style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>ภาษี<br>ค่างวด</th>
					<th width='80px' style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>ค่างวด<br>รวมภาษี</th>
					<th width='80px' style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>มูลค่า<br>ค่างวด</th>
					<th width='80px' style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>ภาษี<br>ค่างวด</th>
					<th width='80px' style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>ค่างวด<br>รวมภาษี</th>
					<th width='80px' style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>มูลค่า<br>ค่างวด</th>
					<th width='80px' style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>ภาษี<br>ค่างวด</th>
					<th width='80px' style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>ค่างวด<br>รวมภาษี</th>
				</tr>
		";
		
		$No = 1;
		if($query->row()){
			foreach($query->result() as $row){	
				$html .= "
					<tr class='trow' seq=".$No.">
						<td width='40px' 	height='40px'	align='left'  style='vertical-align:top;'>".$No++."</td>
						<td width='50px' 	height='40px'	align='left'  style='vertical-align:top;'>".$row->LOCAT."</td>
						<td width='90px'	height='40px'	align='left'  style='vertical-align:top;'>".$row->CONTNO."<br>".$this->Convertdate(2,$row->SDATE)."</td>
						<td width='130px'	height='40px'	align='left'  style='vertical-align:top;'>".$row->CUSNAME."</td>
						<td width='80px'	height='40px'	align='right' style='vertical-align:top;'>".number_format($row->DAMT_N,2)."</td>
						<td width='80px'	height='40px'	align='right' style='vertical-align:top;'>".number_format($row->DAMT_V,2)."</td>
						<td width='80px'	height='40px'	align='right' style='vertical-align:top;'>".number_format($row->DAMT,2)."</td>
						<td width='80px'	height='40px'	align='right' style='vertical-align:top;'>".number_format($row->PAYAMT_N,2)."</td>
						<td width='80px'	height='40px'	align='right' style='vertical-align:top;'>".number_format($row->PAYAMT_V,2)."</td>
						<td width='80px'	height='40px'	align='right' style='vertical-align:top;'>".number_format($row->PAYAMT,2)."</td>
						<td width='80px'	height='40px'	align='right' style='vertical-align:top;'>".number_format($row->KDAMT_N,2)."</td>
						<td width='80px'	height='40px'	align='right' style='vertical-align:top;'>".number_format($row->KDAMT_V,2)."</td>
						<td width='80px'	height='40px'	align='right' style='vertical-align:top;'>".number_format($row->KDAMT,2)."</td>
						<td width='80px'	height='40px'	align='right' style='vertical-align:top;'>".number_format($row->VKANG,2)."</td>
						<td width='60px'	height='40px'	align='center' style='vertical-align:top;'>".$row->NOPAYKANG."</td>
					</tr>
				";	
			}
		}
		
		if($query2->row()){
			foreach($query2->result() as $row){	
				$html .= "
					<tr class='trow' style='background-color:#ebebeb;'>
						<td style='border-top:0.1px solid black;border-bottom:0.1px solid black;border-left:0.1px solid black;text-align:left;vertical-align:middle;' colspan='4'>".$row->Total."</td>
						<td style='border-top:0.1px solid black;border-bottom:0.1px solid black;text-align:right;vertical-align:middle;'>".number_format($row->sumDAMT_N,2)."</td>
						<td style='border-top:0.1px solid black;border-bottom:0.1px solid black;text-align:right;vertical-align:middle;'>".number_format($row->sumDAMT_V,2)."</td>
						<td style='border-top:0.1px solid black;border-bottom:0.1px solid black;text-align:right;vertical-align:middle;'>".number_format($row->sumDAMT,2)."</td>
						<td style='border-top:0.1px solid black;border-bottom:0.1px solid black;text-align:right;vertical-align:middle;'>".number_format($row->sumPAYAMT_N,2)."</td>
						<td style='border-top:0.1px solid black;border-bottom:0.1px solid black;text-align:right;vertical-align:middle;'>".number_format($row->sumPAYAMT_V,2)."</td>
						<td style='border-top:0.1px solid black;border-bottom:0.1px solid black;text-align:right;vertical-align:middle;'>".number_format($row->sumPAYAMT,2)."</td>
						<td style='border-top:0.1px solid black;border-bottom:0.1px solid black;text-align:right;vertical-align:middle;'>".number_format($row->sumKDAMT_N,2)."</td>
						<td style='border-top:0.1px solid black;border-bottom:0.1px solid black;text-align:right;vertical-align:middle;'>".number_format($row->sumKDAMT_V,2)."</td>
						<td style='border-top:0.1px solid black;border-bottom:0.1px solid black;text-align:right;vertical-align:middle;'>".number_format($row->sumKDAMT,2)."</td>
						<td style='border-top:0.1px solid black;border-bottom:0.1px solid black;text-align:right;vertical-align:middle;'>".number_format($row->sumVKANG,2)."</td>
						<td style='border-top:0.1px solid black;border-bottom:0.1px solid black;border-right:0.1px solid black;'></td>
					</tr>
				";	
			}
		}
		$body = "<table class='wf fs9' cellspacing='0'>".$html."</table>";
		
		$mpdf = new \Mpdf\Mpdf([
			'mode' => 'utf-8', 
			'format' => $layout,
			'margin_top' => ($layout == "A4-L" ? "72" : "52"), 	//default = 16
			'margin_left' => 10, 	//default = 15
			'margin_right' => 10, 	//default = 15
			'margin_bottom' => 9, 	//default = 16
			'margin_header' => 9, 	//default = 9
			'margin_footer' => 9, 	//default = 9
		]);
		
		$stylesheet = "
			<style>
				body { font-family: garuda;font-size:9pt; }
				.wf { width:100%; }
				.fs8 { font-size:8pt; }
				.fs10 { font-size:10pt; }
				.h30 { height:30px; }
				.bor { border-top:0.1px solid black;border-bottom:0.1px solid black;}
			</style>
		";
		
		$header = "
			<table class='wf fs10' cellspacing='0' style='border-collapse:collapse;line-height:23px;overflow:wrap;vertical-align:text-top;'>
				<tr>
					<th colspan='15' style='font-size:11pt;'>".$COMP_NM."<br>รายงานภาษีค่างวดค้างชำระ</th>
				</tr>
				<tr>
					<td colspan='15' style='height:35px;text-align:center;'>".$rpcond." ณ วันที่ ".$tx[2]."</td>
				</tr>
				<tr>
					<td colspan='3' align='left'>ชื่อผู้ประกอบการ</td>
					<td colspan='10' align='left'>".$COMP_NM."</td>
				</tr>
				<tr>
					<td colspan='3' align='left'>ชื่อสถานที่ประกอบการ</td>
					<td colspan='12' align='left'>".$LOCADDR1." ".$LOCADDR2."</td>
				</tr>
				<tr>
					<td colspan='3' align='left'>เลขประจำตัวผู้เสียภาษี</td>
					<td colspan='6' align='left'>".$TAXID."</td>
					<td colspan='6' align='right'>เรียงรายงาน: ".$ordername."</td>
				</tr>
				<tr>
					<td colspan='3' align='left'>วันที่พิมพ์รายงาน</td>
					<td colspan='6' align='left'>".date('d/m/').(date('Y')+543)." ".date('H:i')."</td>
					<td colspan='6' align='right'>หน้าที่ : {PAGENO} / {nb} &emsp;&emsp;</td>
					
				</tr>
				".$head."
			</table>
		";		
		$mpdf->SetHTMLHeader($header);
		$mpdf->WriteHTML($body.$stylesheet);
		$mpdf->fontdata['qanela'] = array('R' => "QanelasSoft-Regular.ttf",'B' => "QanelasSoft-Bold.ttf",); //แก้ปริ้นแล้วอ่านไม่ออก
		$mpdf->Output();
		
	}
}