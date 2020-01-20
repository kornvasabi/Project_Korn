<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//BEE+
class ReportALsummaryarpay extends MY_Controller {
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
							<br>รายงานสรุปผลการจัดเก็บ<br>
						</div>
						<div class='col-sm-8 col-xs-8 col-sm-offset-2'>
							<br>
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group'>
									สาขา
									<select id='LOCAT1' class='form-control input-sm' data-placeholder='สาขา'></select>
								</div>
							</div>
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group' >
									รหัสพนักงาน
									<select id='BILLCOLL1' class='form-control input-sm' data-placeholder='รหัสพนักงาน'></select>
								</div>
							</div>
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group'>
									วัดผลงานจากวันที่
									<input type='text' id='FRMDATE' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' placeholder='วัดผลงานจากวันที่' value='".$this->today('startofmonth')."' style='font-size:10.5pt'>
								</div>
							</div>
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group'>
									ถึงวันที่
									<input type='text' id='TODATE' class='form-control input-sm' style='font-size:10.5pt' value='".$this->today('endofmonth')."' disabled>
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
									การรายงาน
									<div class='col-sm-12 col-xs-12' style='border:0.1px dotted #d6d6d6;'>	
										<div class='col-sm-12 col-xs-12'>
											<div class='form-group'>
												<br>
												<input type= 'radio' id='y_no' name='sumy'> ไม่รวมรถยึด
												<br><br>
												<input type= 'radio' id='y_yes' name='sumy' checked> รวมรถยึด
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
		
		$html.= "<script src='".base_url('public/js/SYS05/ReportALsummaryarpay.js')."'></script>";
		echo $html;
	}
	
	function search(){
		$LOCAT1		= $_REQUEST["LOCAT1"];
		$BILLCOLL1 	= str_replace(chr(0),'',$_REQUEST["BILLCOLL1"]);
		$FRMDATE 	= $this->Convertdate(1,$_REQUEST["FRMDATE"]);
		$TODATE 	= $this->Convertdate(1,$_REQUEST["TODATE"]);
		$ystat 		= $_REQUEST["ystat"];
		
		$cond = ""; $rpcond = "";
		
		if($LOCAT1 != ""){
			$cond .= " AND (A.LOCAT LIKE '%".$LOCAT1."%')";
			$rpcond .= "  สาขา ".$LOCAT1;
		}
		
		if($BILLCOLL1 != ""){
			$cond .= " AND (A.BILLCOLL = '".$BILLCOLL1."')";
			$rpcond .= "  พนักงานเก็บเงิน ".$BILLCOLL1;
		}
		
		if($ystat == 'NO'){
			$cond .= " AND (A.YDATE IS NULL OR A.YDATE >= '".$TODATE."')";
			$rpcond .= "  ไม่รวมรถยึด";
		}else{
			$rpcond .= "  รวมรถยึด";
		}
		
		$sql = "
				IF OBJECT_ID('tempdb..#typear') IS NOT NULL DROP TABLE #typear
				select *
				into #typear
				from(	
					select 	'00' as ID,'ล่วงหน้า/ปกติ' TYPEKANG 
					union 
					select	'01','ค้าง 1 งวด'					
					union 
					select	'02','ค้าง 2 งวด'
					union 
					select	'03','ค้าง 3 งวด'
					union 
					select	'04','ค้าง 4 งวด'
					union 
					select	'05','ค้าง 5 งวด'
					union 
					select	'06','ค้าง 6 งวด'
					union 
					select	'07','ค้าง 7 งวด'
					union 
					select	'08','ค้าง 8 งวด'
					union 
					select	'09','ค้าง 9 งวด'
					union 
					select	'10','ค้าง 10 งวด'
					union 
					select	'11','ค้าง 11 งวด'
					union 
					select	'11','ค้าง 12 งวด'
					union 
					select	'13','ค้าง 13-18 งวด'
					union 
					select	'19','ค้าง 19-24 งวด'
					union 
					select	'25','ค้าง 25 งวดขึ้นไป'
				)typear
		";//echo $sql; 
		$query = $this->db->query($sql);
		
		$sql = "
				IF OBJECT_ID('tempdb..#main') IS NOT NULL DROP TABLE #main
				select *
				into #main
				from(
					SELECT A.LOCAT, A.CONTNO, A.LPAYD  
					FROM {$this->MAuth->getdb('ARMAST')} A 
					WHERE (A.TOTPRC > A.SMPAY OR (A.TOTPRC <= A.SMPAY  AND  (CASE WHEN (SELECT MAX(PAYDT) FROM CHQTRAN WHERE CONTNO = A.CONTNO) 
					IS NULL THEN  SDATE ELSE (SELECT MAX(PAYDT) FROM CHQTRAN WHERE CONTNO = A.CONTNO) END) >= '".$FRMDATE."'))  ".$cond."

					UNION 

					SELECT A.LOCAT, A.CONTNO, A.LPAYD 
					FROM {$this->MAuth->getdb('HARMAST')} A  
					WHERE (A.TOTPRC > A.SMPAY OR (A.TOTPRC <= A.SMPAY  AND (CASE WHEN (SELECT MAX(PAYDT) FROM CHQTRAN WHERE CONTNO = A.CONTNO) 
					IS NULL THEN  SDATE ELSE (SELECT MAX(PAYDT) FROM CHQTRAN WHERE CONTNO=A.CONTNO) END) >= '".$FRMDATE."')) AND CONTNO NOT IN 
					(SELECT CONTNO FROM  ARHOLD WHERE YDATE <= '".$FRMDATE."' UNION SELECT CONTNO FROM ARLOST WHERE LOSTDT<='".$FRMDATE."'  
					UNION SELECT CONTNO FROM ARCHAG WHERE YDATE<='".$FRMDATE."' UNION SELECT CONTNO FROM ARCLOSE WHERE CLDATE<= '".$FRMDATE."') ".$cond."
				)main
		";//echo $sql; 
		$query = $this->db->query($sql);
		
		$sql = "
				IF OBJECT_ID('tempdb..#damt') IS NOT NULL DROP TABLE #damt
				select *
				into #damt
				from(
					SELECT A.CONTNO, SUM(A.DAMT) AS DAMT, SUM(A.B_DAMT) AS B_DAMT, SUM(A.TOTDAMT)AS TOTDAMT 
					FROM( 
						SELECT CONTNO,
						SUM(CASE WHEN (DDATE BETWEEN '".$FRMDATE."' AND '".$TODATE."' ) THEN  DAMT ELSE 0 END) AS DAMT  ,
						SUM(CASE WHEN (DDATE < '".$FRMDATE."' ) THEN DAMT ELSE 0 END) AS B_DAMT,
						SUM(DAMT) AS TOTDAMT  
						FROM {$this->MAuth->getdb('ARPAY')} 
						WHERE CONTNO in (select CONTNO from #main)
						GROUP BY CONTNO
						UNION  
						SELECT CONTNO,
						SUM(CASE WHEN (DDATE BETWEEN '".$FRMDATE."' AND '".$TODATE."' ) THEN  DAMT ELSE 0 END) AS DAMT  ,
						SUM(CASE WHEN (DDATE < '".$FRMDATE."' ) THEN DAMT ELSE 0 END) AS B_DAMT  ,
						SUM(DAMT) AS TOTDAMT  
						FROM {$this->MAuth->getdb('HARPAY')} 
						WHERE CONTNO in (select CONTNO from #main)  
						GROUP BY CONTNO 
					)A  
					GROUP BY A.CONTNO
				)damt
		";//echo $sql; 
		$query = $this->db->query($sql);
		
		$sql = "
				IF OBJECT_ID('tempdb..#pay') IS NOT NULL DROP TABLE #pay
				select *
				into #pay
				from(
					SELECT CONTNO,
					SUM(CASE WHEN (PAYDT < '".$FRMDATE."'  AND FLAG<>'C' AND (( PAYFOR = '006') OR ( PAYFOR = '007'))) THEN PAYAMT ELSE 0 END) AS B_PAYDAMT,
					SUM(CASE WHEN (PAYDT BETWEEN '".$FRMDATE."' AND '".$TODATE."' AND FLAG<>'C' AND (( PAYFOR = '006') OR ( PAYFOR = '007')))THEN PAYAMT ELSE 0 END) AS PAYDAMT 
					FROM {$this->MAuth->getdb('CHQTRAN')}  
					WHERE  CONTNO+LOCATPAY in (select CONTNO+LOCAT from #main)  
					GROUP BY CONTNO
				)pay
		";//echo $sql; 
		$query = $this->db->query($sql);
		
		$sql = "
				IF OBJECT_ID('tempdb..#main2') IS NOT NULL DROP TABLE #main2
				select *
				into #main2
				from(
					select LOCAT, a.CONTNO, LPAYD, TOTDAMT, B_DAMT, DAMT, isnull(B_PAYDAMT,0) as B_PAYDAMT, isnull(PAYDAMT,0) as PAYDAMT,
					B_DAMT-isnull(B_PAYDAMT,0) as B_AR
					from #main a
					left join #damt b on a.CONTNO = b.CONTNO
					left join #pay c on a.CONTNO = c.CONTNO
				)main2
		";//echo $sql; 
		$query = $this->db->query($sql);
		
		$sql = "
				IF OBJECT_ID('tempdb..#main3') IS NOT NULL DROP TABLE #main3
				select *
				into #main3
				from(
					select CONTNO, 0 as QTY
					from #main2 where B_AR <= 0
					union all
					select CONTNO, count(NOPAY) as QTY
					from(
						select CONTNO, NOPAY
						from {$this->MAuth->getdb('ARPAY')}
						WHERE CONTNO in (select CONTNO from #main2 where B_AR > 0)
						and (DDATE < '".$FRMDATE."') and (DATE1 >= '".$FRMDATE."' or DAMT - PAYMENT >0)
						union 
						select  CONTNO, NOPAY
						from {$this->MAuth->getdb('HARPAY')}
						WHERE CONTNO in (select CONTNO from #main2 where B_AR > 0)
						and (DDATE < '".$FRMDATE."') and (DATE1 >= '".$FRMDATE."' or DAMT - PAYMENT > 0)
					)A
					group by CONTNO
				)main3
		";//echo $sql; 
		$query = $this->db->query($sql);
		
		$sql = "
				IF OBJECT_ID('tempdb..#main4') IS NOT NULL DROP TABLE #main4
				select *
				into #main4
				from(
					select TYPEKANG, ID, B_QTY, B_DAMT, B_KANG, B_RCV, B_AR,  QTY,  KANG, DAMT, PAYDAMT, AR, PERQTY, PERDAMT, PERKANG, PERPAYDAMT
					from #typear a
					left join(
						select	TYPEAR, 
								SUM(B_QTY) as B_QTY, SUM(B_RCV-B_KANG) as B_DAMT, SUM(B_KANG) as B_KANG, SUM(B_RCV) as B_RCV, SUM(B_AR) as B_AR,  
								SUM(QTY) as QTY,  SUM(KANG) as KANG,  SUM(DAMT) as DAMT, SUM(PAYDAMT) as PAYDAMT, SUM(AR) as AR,
								round(SUM(QTY)*100.0/nullif(SUM(B_QTY),0),0) as PERQTY,
								round(SUM(DAMT)*100.0/nullif(SUM(B_RCV-B_KANG),0),0) as PERDAMT,
								round(SUM(KANG)*100.0/nullif(SUM(B_KANG),0),0) as PERKANG,
								round(SUM(PAYDAMT)*100.0/nullif(SUM(B_RCV),0),0) as PERPAYDAMT
						from(
							select	a.CONTNO,
									case	
									when QTY = 0 or QTY is null		then	'ล่วงหน้า/ปกติ'
									when QTY = 1					then	'ค้าง 1 งวด'
									when QTY = 2					then	'ค้าง 2 งวด'
									when QTY = 3					then	'ค้าง 3 งวด'
									when QTY = 4					then	'ค้าง 4 งวด'
									when QTY = 5					then	'ค้าง 5 งวด'
									when QTY = 6					then	'ค้าง 6 งวด'
									when QTY = 7					then	'ค้าง 7 งวด'
									when QTY = 8					then	'ค้าง 8 งวด'
									when QTY = 9					then	'ค้าง 9 งวด'
									when QTY = 10					then	'ค้าง 10 งวด'
									when QTY = 11					then	'ค้าง 11 งวด'
									when QTY = 12					then	'ค้าง 12 งวด'
									when QTY between 13 and 18		then	'ค้าง 13-18 งวด'
									when QTY between 19 and 24		then	'ค้าง 19-24 งวด'
									when QTY >= 25					then	'ค้าง 25 งวดขึ้นไป'
									end as TYPEAR,
									1 as B_QTY, 
									(case when DAMT+(B_DAMT-B_PAYDAMT) < 0 then 0 else DAMT+(B_DAMT-B_PAYDAMT) end-case when (B_DAMT-B_PAYDAMT) < 0 then 0 else B_DAMT-B_PAYDAMT end) as B_DAMT,
									case when (B_DAMT-B_PAYDAMT) < 0 then 0 else B_DAMT-B_PAYDAMT end as B_KANG, 
									case when DAMT+(B_DAMT-B_PAYDAMT) < 0 then 0 else DAMT+(B_DAMT-B_PAYDAMT) end as B_RCV, 
									TOTDAMT-B_PAYDAMT as B_AR,
									case when PAYDAMT > 0 then 1 else 0 end as QTY, 
									case when (case when (B_DAMT-B_PAYDAMT) < 0 then 0 else B_DAMT-B_PAYDAMT end)-PAYDAMT < 0 then 0 else PAYDAMT end as KANG,
									case when (case when (B_DAMT-B_PAYDAMT) < 0 then 0 else B_DAMT-B_PAYDAMT end)-PAYDAMT >= 0 
									then PAYDAMT-(case when (case when (B_DAMT-B_PAYDAMT) < 0 then 0 else B_DAMT-B_PAYDAMT end)-PAYDAMT < 0 then 0 else PAYDAMT end ) else PAYDAMT end as DAMT,
									case when PAYDAMT > 0 then PAYDAMT else 0 end as PAYDAMT,
									TOTDAMT-(B_PAYDAMT+PAYDAMT) as AR
							from #main2 a
							left join #main3 b on a.CONTNO = b.CONTNO collate thai_cs_as
						)A
						group by TYPEAR
					)b on a.TYPEKANG = b.TYPEAR
				)main4
		";//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$sql = "
				select ID, TYPEKANG, B_QTY, B_DAMT, B_KANG, B_RCV, B_AR,  QTY,  KANG, DAMT, PAYDAMT, AR, PERQTY, PERDAMT, PERKANG, PERPAYDAMT 
				from #main4
				union all
				select '99', 'รวมทั้งหมด' as Total, SUM(B_QTY) as B_QTY, SUM(B_DAMT) as B_DAMT, SUM(B_KANG) as B_KANG, SUM(B_RCV) as B_RCV, SUM(B_AR) as B_AR,  
				SUM(QTY) as QTY,  SUM(KANG) as KANG,  SUM(DAMT) as DAMT, SUM(PAYDAMT) as PAYDAMT, SUM(AR) as AR,
				round(SUM(QTY)*100.0/nullif(SUM(B_QTY),0),0) as PERQTY, round(SUM(DAMT)*100.0/nullif(SUM(B_DAMT),0),0) as PERDAMT,
				round(SUM(KANG)*100.0/nullif(SUM(B_KANG),0),0) as PERKANG, round(SUM(PAYDAMT)*100.0/nullif(SUM(B_RCV),0),0) as PERPAYDAMT
				from #main4 
				order by ID
		";//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$head = ""; $html = ""; $head2 = "";  $report = ""; $sumreport = ""; $sumreport2 = ""; $i = 0; 
		
		$head = "<tr style='height:25px;'>
					<th rowspan='2' style='display:none;'></th>
					<th rowspan='2' style='vertical-align:top;'>รายการลุกหนี้</th>
					<th colspan='5' style='vertical-align:top;text-align:center;'>ยอดที่มีให้เก็บ</th>
					<th colspan='5' style='vertical-align:top;text-align:center;'>ยอดที่เก็บได้</th>
					<th colspan='4' style='vertical-align:top;text-align:center;'>เปอร์เซ็นเก็บได้</th>
				</tr>
				<tr style='height:25px;'>
					<th style='vertical-align:top;text-align:right;'>จน.ราย</th>
					<th style='vertical-align:top;text-align:right;'>จน.เงินตามดิว</th>
					<th style='vertical-align:top;text-align:right;'>เงินค้างค่างวด</th>
					<th style='vertical-align:top;text-align:right;'>รวมต้องเก็บ</th>
					<th style='vertical-align:top;text-align:right;'>ลูกหนี้คงเหลือ</th> 
					<th style='vertical-align:top;text-align:right;'>จน.ราย</th>
					<th style='vertical-align:top;text-align:right;'>จน.เงินตามดิว</th>
					<th style='vertical-align:top;text-align:right;'>เงินค้างค่างวด</th>
					<th style='vertical-align:top;text-align:right;'>รวมเก็บได้</th>
					<th style='vertical-align:top;text-align:right;'>ลูกหนี้คงเหลือ</th> 
					<th style='vertical-align:top;text-align:right;'>จน.ราย</th>
					<th style='vertical-align:top;text-align:right;'>จน.เงินตามดิว</th>
					<th style='vertical-align:top;text-align:right;'>เงินค้างค่างวด</th>
					<th style='vertical-align:top;text-align:right;'>รวมเก็บได้</th>
				</tr>
		";
		
		$head2 = "<tr>
					<th rowspan='2' style='vertical-align:top;'>รายการลุกหนี้</th>
					<th colspan='5' style='vertical-align:top;text-align:center;'>ยอดที่มีให้เก็บ</th>
					<th colspan='5' style='vertical-align:top;text-align:center;'>ยอดที่เก็บได้</th>
					<th colspan='4' style='vertical-align:top;text-align:center;'>เปอร์เซ็นเก็บได้</th>
				</tr>
				<tr>
					<th style='vertical-align:top;text-align:right;'>จน.ราย</th>
					<th style='vertical-align:top;text-align:right;'>จน.เงินตามดิว</th>
					<th style='vertical-align:top;text-align:right;'>เงินค้างค่างวด</th>
					<th style='vertical-align:top;text-align:right;'>รวมต้องเก็บ</th>
					<th style='vertical-align:top;text-align:right;'>ลูกหนี้คงเหลือ</th> 
					<th style='vertical-align:top;text-align:right;'>จน.ราย</th>
					<th style='vertical-align:top;text-align:right;'>จน.เงินตามดิว</th>
					<th style='vertical-align:top;text-align:right;'>เงินค้างค่างวด</th>
					<th style='vertical-align:top;text-align:right;'>รวมเก็บได้</th>
					<th style='vertical-align:top;text-align:right;'>ลูกหนี้คงเหลือ</th> 
					<th style='vertical-align:top;text-align:right;'>จน.ราย</th>
					<th style='vertical-align:top;text-align:right;'>จน.เงินตามดิว</th>
					<th style='vertical-align:top;text-align:right;'>เงินค้างค่างวด</th>
					<th style='vertical-align:top;text-align:right;'>รวมเก็บได้</th>
				</tr>
		";
		
		$NRow = 1;
		if($query->row()){
			foreach($query->result() as $row){$i++;
				$bgcolor = ""; 
				if(($row->TYPEKANG) == "รวมทั้งหมด"){
					$bgcolor = "style='background-color:#eee;'";
				}
				$html .= "
					<tr class='trow' seq=".$NRow.">
						<td style='display:none;' seq=".$NRow++." ></td>
						<td ".$bgcolor.">".$row->TYPEKANG."</td>
						<td align='right' ".$bgcolor.">".number_format($row->B_QTY)."</td>
						<td align='right' ".$bgcolor.">".number_format($row->B_DAMT,2)."</td>
						<td align='right' ".$bgcolor.">".number_format($row->B_KANG,2)."</td>
						<td align='right' ".$bgcolor.">".number_format($row->B_RCV,2)."</td>
						<td align='right' ".$bgcolor.">".number_format($row->B_AR,2)."</td>
						<td align='right' ".$bgcolor.">".number_format($row->QTY)."</td>
						<td align='right' ".$bgcolor.">".number_format($row->DAMT,2)."</td>
						<td align='right' ".$bgcolor.">".number_format($row->KANG,2)."</td>
						<td align='right' ".$bgcolor.">".number_format($row->PAYDAMT,2)."</td>
						<td align='right' ".$bgcolor.">".number_format($row->AR,2)."</td>
						<td align='right' ".$bgcolor.">".number_format($row->PERQTY,2)."</td>
						<td align='right' ".$bgcolor.">".number_format($row->PERDAMT,2)."</td>
						<td align='right' ".$bgcolor.">".number_format($row->PERKANG,2)."</td>
						<td align='right' ".$bgcolor.">".number_format($row->PERPAYDAMT,2)."</td>
					</tr>
				";
			}
		}
		
		
		$No = 1;
		if($query->row()){
			foreach($query->result() as $row){
				$report .= "
					<tr class='trow'>
						<td style='mso-number-format:\"\@\";'>".$row->TYPEKANG."</td>
						<td style='mso-number-format:\"\#\,\#\#0\";text-align:right;'>".number_format($row->B_QTY)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;''>".number_format($row->B_DAMT,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->B_KANG,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->B_RCV,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->B_AR,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0\";text-align:right;'>".number_format($row->QTY)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->DAMT,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->KANG,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->PAYDAMT,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->AR,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->PERQTY,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->PERDAMT,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->PERKANG,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->PERPAYDAMT,2)."</td>
						</tr>
				";	
			}
		}
		
		if($i>0){
			$html = "
				<div id='table-fixed-ReportALsummaryarpay' class='col-sm-12' style='height:100%;width:100%;overflow:auto;font-size:9pt;'>
					<table id='table-ReportALsummaryarpay' style='background-color:white;' class='col-sm-12 display table table-bordered' cellspacing='0' width='calc(100% - 1px)'>
						<thead>
						<tr style='height:40px;'>
							<th colspan='16' style='font-size:12pt;border:0px;vertical-align;middle;text-align:center;'>รายงานสรุปผลการจัดเก็บ</th>
						</tr>
						<tr style='height:25px;'>
							<td colspan='16' style='border-bottom:1px solid #ddd;vertical-align;middle;text-align:center;'>จากวันที่ ".$_REQUEST["FRMDATE"]." - ".$_REQUEST["TODATE"]." ".$rpcond."  ออกรายงาน ณ วันที่ ".$this->today('today')."</td>
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
			<div id='table-fixed-ReportALsummaryarpay2' class='col-sm-12' style='height:100%;width:100%;overflow:auto;'>
				<table id='table-ReportALsummaryarpay2' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='100%'>
					<thead>
						<tr>
							<th colspan='15' style='font-size:12pt;border:0px;text-align:center;'>รายงานสรุปผลการจัดเก็บ</th>
						</tr>
						<tr>
							<td colspan='15' style='border:0px;text-align:center;'>จากวันที่ ".$_REQUEST["FRMDATE"]." - ".$_REQUEST["TODATE"]." ".$rpcond." ออกรายงาน ณ วันที่ ".$this->today('today')."</td>
						</tr>
						".$head2."
						".$report."
					</thead>	
				</table>
			</div>
		";
		
		$response = array("html"=>$html, "report"=>$report, "reporttoday"=>str_replace('/','-',$this->today('today')));
		echo json_encode($response);
	}
	
	function conditiontopdf(){
		$data 	= 	array();
		$data[] = 	urlencode($_REQUEST["LOCAT1"].'||'.$_REQUEST["BILLCOLL1"].'||'.$_REQUEST["FRMDATE"].'||'.$_REQUEST["TODATE"]
					.'||'.$_REQUEST["ystat"].'||'.$_REQUEST["layout"]);
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
		$BILLCOLL1 	= str_replace(chr(0),'',$tx[1]);
		$FRMDATE 	= $this->Convertdate(1,$tx[2]);
		$TODATE 	= $this->Convertdate(1,$tx[3]);
		$ystat 		= $tx[4];
		$layout 	= $tx[5];
		
		$cond = ""; $rpcond = "";
		
		if($LOCAT1 != ""){
			$cond .= " AND (A.LOCAT LIKE '%".$LOCAT1."%')";
			$rpcond .= "  สาขา ".$LOCAT1;
		}
		
		if($BILLCOLL1 != ""){
			$cond .= " AND (A.BILLCOLL = '".$BILLCOLL1."')";
			$rpcond .= "  พนักงานเก็บเงิน ".$BILLCOLL1;
		}
		
		if($ystat == 'NO'){
			$cond .= " AND (A.YDATE IS NULL OR A.YDATE >= '".$TODATE."')";
			$rpcond .= "  ไม่รวมรถยึด";
		}else{
			$rpcond .= "  รวมรถยึด";
		}
		
		$sql = "
				IF OBJECT_ID('tempdb..#typear') IS NOT NULL DROP TABLE #typear
				select *
				into #typear
				from(	
					select 	'00' as ID,'ล่วงหน้า/ปกติ' TYPEKANG 
					union 
					select	'01','ค้าง 1 งวด'					
					union 
					select	'02','ค้าง 2 งวด'
					union 
					select	'03','ค้าง 3 งวด'
					union 
					select	'04','ค้าง 4 งวด'
					union 
					select	'05','ค้าง 5 งวด'
					union 
					select	'06','ค้าง 6 งวด'
					union 
					select	'07','ค้าง 7 งวด'
					union 
					select	'08','ค้าง 8 งวด'
					union 
					select	'09','ค้าง 9 งวด'
					union 
					select	'10','ค้าง 10 งวด'
					union 
					select	'11','ค้าง 11 งวด'
					union 
					select	'11','ค้าง 12 งวด'
					union 
					select	'13','ค้าง 13-18 งวด'
					union 
					select	'19','ค้าง 19-24 งวด'
					union 
					select	'25','ค้าง 25 งวดขึ้นไป'
				)typear
		";//echo $sql; 
		$query = $this->db->query($sql);
		
		$sql = "
				IF OBJECT_ID('tempdb..#main') IS NOT NULL DROP TABLE #main
				select *
				into #main
				from(
					SELECT A.LOCAT, A.CONTNO, A.LPAYD  
					FROM {$this->MAuth->getdb('ARMAST')} A 
					WHERE (A.TOTPRC > A.SMPAY OR (A.TOTPRC <= A.SMPAY  AND  (CASE WHEN (SELECT MAX(PAYDT) FROM CHQTRAN WHERE CONTNO = A.CONTNO) 
					IS NULL THEN  SDATE ELSE (SELECT MAX(PAYDT) FROM CHQTRAN WHERE CONTNO = A.CONTNO) END) >= '".$FRMDATE."'))  ".$cond."

					UNION 

					SELECT A.LOCAT, A.CONTNO, A.LPAYD 
					FROM {$this->MAuth->getdb('HARMAST')} A  
					WHERE (A.TOTPRC > A.SMPAY OR (A.TOTPRC <= A.SMPAY  AND (CASE WHEN (SELECT MAX(PAYDT) FROM CHQTRAN WHERE CONTNO = A.CONTNO) 
					IS NULL THEN  SDATE ELSE (SELECT MAX(PAYDT) FROM CHQTRAN WHERE CONTNO=A.CONTNO) END) >= '".$FRMDATE."')) AND CONTNO NOT IN 
					(SELECT CONTNO FROM  ARHOLD WHERE YDATE <= '".$FRMDATE."' UNION SELECT CONTNO FROM ARLOST WHERE LOSTDT<='".$FRMDATE."'  
					UNION SELECT CONTNO FROM ARCHAG WHERE YDATE<='".$FRMDATE."' UNION SELECT CONTNO FROM ARCLOSE WHERE CLDATE<= '".$FRMDATE."') ".$cond."
				)main
		";//echo $sql; 
		$query = $this->db->query($sql);
		
		$sql = "
				IF OBJECT_ID('tempdb..#damt') IS NOT NULL DROP TABLE #damt
				select *
				into #damt
				from(
					SELECT A.CONTNO, SUM(A.DAMT) AS DAMT, SUM(A.B_DAMT) AS B_DAMT, SUM(A.TOTDAMT)AS TOTDAMT 
					FROM( 
						SELECT CONTNO,
						SUM(CASE WHEN (DDATE BETWEEN '".$FRMDATE."' AND '".$TODATE."' ) THEN  DAMT ELSE 0 END) AS DAMT  ,
						SUM(CASE WHEN (DDATE < '".$FRMDATE."' ) THEN DAMT ELSE 0 END) AS B_DAMT,
						SUM(DAMT) AS TOTDAMT  
						FROM {$this->MAuth->getdb('ARPAY')} 
						WHERE CONTNO in (select CONTNO from #main)
						GROUP BY CONTNO
						UNION  
						SELECT CONTNO,
						SUM(CASE WHEN (DDATE BETWEEN '".$FRMDATE."' AND '".$TODATE."' ) THEN  DAMT ELSE 0 END) AS DAMT  ,
						SUM(CASE WHEN (DDATE < '".$FRMDATE."' ) THEN DAMT ELSE 0 END) AS B_DAMT  ,
						SUM(DAMT) AS TOTDAMT  
						FROM {$this->MAuth->getdb('HARPAY')} 
						WHERE CONTNO in (select CONTNO from #main)  
						GROUP BY CONTNO 
					)A  
					GROUP BY A.CONTNO
				)damt
		";//echo $sql; 
		$query = $this->db->query($sql);
		
		$sql = "
				IF OBJECT_ID('tempdb..#pay') IS NOT NULL DROP TABLE #pay
				select *
				into #pay
				from(
					SELECT CONTNO,
					SUM(CASE WHEN (PAYDT < '".$FRMDATE."'  AND FLAG<>'C' AND (( PAYFOR = '006') OR ( PAYFOR = '007'))) THEN PAYAMT ELSE 0 END) AS B_PAYDAMT,
					SUM(CASE WHEN (PAYDT BETWEEN '".$FRMDATE."' AND '".$TODATE."' AND FLAG<>'C' AND (( PAYFOR = '006') OR ( PAYFOR = '007')))THEN PAYAMT ELSE 0 END) AS PAYDAMT 
					FROM {$this->MAuth->getdb('CHQTRAN')}  
					WHERE  CONTNO+LOCATPAY in (select CONTNO+LOCAT from #main)  
					GROUP BY CONTNO
				)pay
		";//echo $sql; 
		$query = $this->db->query($sql);
		
		$sql = "
				IF OBJECT_ID('tempdb..#main2') IS NOT NULL DROP TABLE #main2
				select *
				into #main2
				from(
					select LOCAT, a.CONTNO, LPAYD, TOTDAMT, B_DAMT, DAMT, isnull(B_PAYDAMT,0) as B_PAYDAMT, isnull(PAYDAMT,0) as PAYDAMT,
					B_DAMT-isnull(B_PAYDAMT,0) as B_AR
					from #main a
					left join #damt b on a.CONTNO = b.CONTNO
					left join #pay c on a.CONTNO = c.CONTNO
				)main2
		";//echo $sql; 
		$query = $this->db->query($sql);
		
		$sql = "
				IF OBJECT_ID('tempdb..#main3') IS NOT NULL DROP TABLE #main3
				select *
				into #main3
				from(
					select CONTNO, 0 as QTY
					from #main2 where B_AR <= 0
					union all
					select CONTNO, count(NOPAY) as QTY
					from(
						select CONTNO, NOPAY
						from {$this->MAuth->getdb('ARPAY')}
						WHERE CONTNO in (select CONTNO from #main2 where B_AR > 0)
						and (DDATE < '".$FRMDATE."') and (DATE1 >= '".$FRMDATE."' or DAMT - PAYMENT >0)
						union 
						select  CONTNO, NOPAY
						from {$this->MAuth->getdb('HARPAY')}
						WHERE CONTNO in (select CONTNO from #main2 where B_AR > 0)
						and (DDATE < '".$FRMDATE."') and (DATE1 >= '".$FRMDATE."' or DAMT - PAYMENT > 0)
					)A
					group by CONTNO
				)main3
		";//echo $sql; 
		$query = $this->db->query($sql);
		
		$sql = "
				IF OBJECT_ID('tempdb..#main4') IS NOT NULL DROP TABLE #main4
				select *
				into #main4
				from(
					select TYPEKANG, ID, B_QTY, B_DAMT, B_KANG, B_RCV, B_AR,  QTY,  KANG, DAMT, PAYDAMT, AR, PERQTY, PERDAMT, PERKANG, PERPAYDAMT
					from #typear a
					left join(
						select	TYPEAR, 
								SUM(B_QTY) as B_QTY, SUM(B_DAMT) as B_DAMT, SUM(B_KANG) as B_KANG, SUM(B_RCV) as B_RCV, SUM(B_AR) as B_AR,  
								SUM(QTY) as QTY,  SUM(KANG) as KANG,  SUM(DAMT) as DAMT, SUM(PAYDAMT) as PAYDAMT, SUM(AR) as AR,
								round(SUM(QTY)*100.0/nullif(SUM(B_QTY),0),0) as PERQTY,
								round(SUM(DAMT)*100.0/nullif(SUM(B_DAMT),0),0) as PERDAMT,
								round(SUM(KANG)*100.0/nullif(SUM(B_KANG),0),0) as PERKANG,
								round(SUM(PAYDAMT)*100.0/nullif(SUM(B_RCV),0),0) as PERPAYDAMT
						from(
							select	a.CONTNO,
									case	
									when QTY = 0 or QTY is null		then	'ล่วงหน้า/ปกติ'
									when QTY = 1					then	'ค้าง 1 งวด'
									when QTY = 2					then	'ค้าง 2 งวด'
									when QTY = 3					then	'ค้าง 3 งวด'
									when QTY = 4					then	'ค้าง 4 งวด'
									when QTY = 5					then	'ค้าง 5 งวด'
									when QTY = 6					then	'ค้าง 6 งวด'
									when QTY = 7					then	'ค้าง 7 งวด'
									when QTY = 8					then	'ค้าง 8 งวด'
									when QTY = 9					then	'ค้าง 9 งวด'
									when QTY = 10					then	'ค้าง 10 งวด'
									when QTY = 11					then	'ค้าง 11 งวด'
									when QTY = 12					then	'ค้าง 12 งวด'
									when QTY between 13 and 18		then	'ค้าง 13-18 งวด'
									when QTY between 19 and 24		then	'ค้าง 19-24 งวด'
									when QTY >= 25					then	'ค้าง 25 งวดขึ้นไป'
									end as TYPEAR,
									1 as B_QTY, DAMT as B_DAMT, 
									case when (B_DAMT-B_PAYDAMT) < 0 then 0 else B_DAMT-B_PAYDAMT end as B_KANG, 
									case when DAMT+(B_DAMT-B_PAYDAMT) < 0 then 0 else DAMT+(B_DAMT-B_PAYDAMT) end as B_RCV, 
									TOTDAMT-B_PAYDAMT as B_AR,
									case when PAYDAMT > 0 then 1 else 0 end as QTY, 
									case when (B_DAMT+DAMT)-(B_PAYDAMT+PAYDAMT) < 0 then 0 else (B_DAMT+DAMT)-(B_PAYDAMT+PAYDAMT) end as KANG,
									case when PAYDAMT > 0 then DAMT else 0 end as DAMT,
									case when PAYDAMT > 0 then PAYDAMT else 0 end as PAYDAMT,
									TOTDAMT-(B_PAYDAMT+PAYDAMT) as AR
							from #main2 a
							left join #main3 b on a.CONTNO = b.CONTNO collate thai_cs_as
						)A
						group by TYPEAR
					)b on a.TYPEKANG = b.TYPEAR
				)main4
		";//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$sql = "
				select ID, TYPEKANG, B_QTY, B_DAMT, B_KANG, B_RCV, B_AR,  QTY,  KANG, DAMT, PAYDAMT, AR, PERQTY, PERDAMT, PERKANG, PERPAYDAMT 
				from #main4
				union all
				select '99', 'รวมทั้งหมด' as Total, SUM(B_QTY) as B_QTY, SUM(B_DAMT) as B_DAMT, SUM(B_KANG) as B_KANG, SUM(B_RCV) as B_RCV, SUM(B_AR) as B_AR,  
				SUM(QTY) as QTY,  SUM(KANG) as KANG,  SUM(DAMT) as DAMT, SUM(PAYDAMT) as PAYDAMT, SUM(AR) as AR,
				round(SUM(QTY)*100.0/nullif(SUM(B_QTY),0),0) as PERQTY, round(SUM(DAMT)*100.0/nullif(SUM(B_DAMT),0),0) as PERDAMT,
				round(SUM(KANG)*100.0/nullif(SUM(B_KANG),0),0) as PERKANG, round(SUM(PAYDAMT)*100.0/nullif(SUM(B_RCV),0),0) as PERPAYDAMT
				from #main4 
				order by ID
		";//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$head = ""; $html = ""; $i=0; 

		$head = "
				<tr>
					<th rowspan='2' style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>รายการลุกหนี้</th>
					<th colspan='5' style='border-bottom:0.1px solid black;vertical-align:top;text-align:center;'>ยอดที่มีให้เก็บ</th>
					<th colspan='5' style='border-bottom:0.1px solid black;vertical-align:top;text-align:center;'>ยอดที่เก็บได้</th>
					<th colspan='4' style='border-bottom:0.1px solid black;vertical-align:top;text-align:center;'>เปอร์เซ็นเก็บได้</th>
				</tr>
				<tr>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>จน.ราย</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>จน.เงินตามดิว</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>เงินค้างค่างวด</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>รวมต้องเก็บ</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>ลูกหนี้คงเหลือ</th> 
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>จน.ราย</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>จน.เงินตามดิว</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>เงินค้างค่างวด</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>รวมเก็บได้</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>ลูกหนี้คงเหลือ</th> 
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>จน.ราย</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>จน.เงินตามดิว</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>เงินค้างค่างวด</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>รวมเก็บได้</th>
				</tr>
		";
		
		$No = 1;
		if($query->row()){
			foreach($query->result() as $row){	
					$border = "";
					if(($row->TYPEKANG) == "รวมทั้งหมด"){
						$border = "border-top:0.1px solid black;border-bottom:0.1px solid black;";
					}
				$html .= "
					<tr class='trow' seq=".$No.">
						<td style='width:85px;".$border."'>".$row->TYPEKANG."</td>
						<td style='width:50px;".$border."' align='right'>".number_format($row->B_QTY)."</td>
						<td style='width:75px;".$border."' align='right'>".number_format($row->B_DAMT,2)."</td>
						<td style='width:70px;".$border."' align='right'>".number_format($row->B_KANG,2)."</td>
						<td style='width:70px;".$border."' align='right'>".number_format($row->B_RCV,2)."</td>
						<td style='width:75px;".$border."' align='right'>".number_format($row->B_AR,2)."</td>
						<td style='width:50px;".$border."' align='right'>".number_format($row->QTY)."</td>
						<td style='width:75px;".$border."' align='right'>".number_format($row->DAMT,2)."</td>
						<td style='width:70px;".$border."' align='right'>".number_format($row->KANG,2)."</td>
						<td style='width:70px;".$border."' align='right'>".number_format($row->PAYDAMT,2)."</td>
						<td style='width:75px;".$border."' align='right'>".number_format($row->AR,2)."</td>
						<td style='width:50px;".$border."' align='right'>".number_format($row->PERQTY,2)."</td>
						<td style='width:75px;".$border."' align='right'>".number_format($row->PERDAMT,2)."</td>
						<td style='width:70px;".$border."' align='right'>".number_format($row->PERKANG,2)."</td>
						<td style='width:70px;".$border."' align='right'>".number_format($row->PERPAYDAMT,2)."</td>
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
			<table class='wf' style='font-size:8pt;height:700px;border-collapse:collapse;line-height:23px;overflow:wrap;vertical-align:text-top;'>
				<tbody>
					<tr>
						<th colspan='15' style='font-size:10pt;'>รายงานสรุปผลการจัดเก็บ</th>
					</tr>
					<tr>
						<td colspan='15' style='font-size:9pt;height:35px;border-bottom:0.1px solid black;text-align:center;'>".$rpcond."  จากวันที่ ".$tx[2]." - ".$tx[3]."</td>
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