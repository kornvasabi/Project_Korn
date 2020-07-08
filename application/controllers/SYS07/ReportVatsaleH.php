<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//BEE+
class ReportVatsaleH extends MY_Controller {
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
							<br>รายงานภาษีคงเหลือจากการขายผ่อน<br>
						</div>
						<div class='col-sm-8 col-xs-8 col-sm-offset-2'>
							<br>
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group'>
									สาขา
									<select id='LOCAT1' class='form-control input-sm'>
										<option value='".$this->sess["branch"]."'>".$this->sess["branch"]."</option>
									</select>
								</div>
							</div>
							<div class='col-sm-3 col-xs-3'>	
								<div class='form-group' >
									ภาษี ณ เดือน
									<input type='number' id='VATMONTH' class='form-control input-sm' min='1' max='12' value='".number_format(substr($this->today('today'),3,2))."'>
								</div>
							</div>
							<div class='col-sm-3 col-xs-3'>	
								<div class='form-group' >
									ปี
									<input type='number' id='VATYEAR' class='form-control input-sm' min='2500' max='2600' value='".substr($this->today('today'),6,4)."'>
								</div>
							</div>
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group'>
									จากเลขที่สัญญา
									<select id='CONTNO1' class='form-control input-sm' data-placeholder='จากเลขที่สัญญา'></select>
								</div>
							</div>
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group'>
									ถึงเลขที่สัญญา
									<select id='CONTNO2' class='form-control input-sm' data-placeholder='ถึงเลขที่สัญญา'></select>
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
											<input type= 'radio' id='contno' name='orderby' checked> เลขที่สัญญา
											<br>
											<input type= 'radio' id='locat' name='orderby'> สาขา
											<br>
											<input type= 'radio' id='sdate' name='orderby'> วันที่ขาย
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
		
		$html.= "<script src='".base_url('public/js/SYS07/ReportVatsaleH.js')."'></script>";
		echo $html;
	}//background: rgba(0, 0, 0, 0) url(&#39;../public/lobiadmin-master/version/1.0/ajax/img/bg/bgrp07.jpg&#39;) repeat scroll 0% 0%;background-size:cover;
	
	function search(){
		$LOCAT1		= $_REQUEST["LOCAT1"];
		$CONTNO1 	= str_replace(chr(0),'',$_REQUEST["CONTNO1"]);
		$CONTNO2 	= str_replace(chr(0),'',$_REQUEST["CONTNO2"]);
		$VATMONTH 	= $_REQUEST["VATMONTH"];
		$VATYEAR 	= $_REQUEST["VATYEAR"];
		$order 		= $_REQUEST["order"];
		
		$cond = ""; $rpcond = "";
		
		if($LOCAT1 != ""){
			$rpcond .= "  สาขา ".$LOCAT1;
		}
		
		if($CONTNO1 != ""){
			$rpcond .= "  จากเลขที่สัญญา ".$CONTNO1;
			$cond  	.= " and CONTNO >= '".$CONTNO1."'";
		}
		
		if($CONTNO2 != ""){
			$rpcond .= "  ถึงเลขที่สัญญา ".$CONTNO2;
			$cond 	.= " and CONTNO <= '".$CONTNO2."'";
		}
		
		$orderby = "";
		if($order == "CONTNO"){
			$orderby = "order by CONTNO, LOCAT, SDATE";
		}else if($order == "LOCAT"){
			$orderby = "order by LOCAT, SDATE, CONTNO"; 
		}else if($order == "SDATE"){
			$orderby = "order by SDATE, LOCAT, CONTNO"; 
		}
		
		$sql = "
				declare @locat varchar(10)	= '".$LOCAT1."' ;
				declare @year varchar(4)	= ".$VATYEAR."-543 ;
				declare @month varchar(2)	= ".$VATMONTH.";
				declare @datefrm datetime	= (select @year+'-'+@month+'-1');
				declare @dateto datetime	= (select dateadd(day,-1,dateadd(month,1,convert(date,@year+'-'+@month+'-1'))));

				IF OBJECT_ID('tempdb..#main') IS NOT NULL DROP TABLE #main
				select *
				into #main
				from(	
					SELECT LOCAT, CONTNO, DTSTOPV, (SELECT RTRIM(SNAM)+' '+RTRIM(NAME1)+' '+NAME2 FROM {$this->MAuth->getdb('CUSTMAST')} WHERE CUSCOD = A.CUSCOD) AS CUSNAME,
					convert(char,SDATE,112) as SDATE, TOTPRC, VATPRC, VATPRES, VATDWN, PVATDWN, VATDWN-PVATDWN as KANGDWN, isnull(VAT,0) as VAT, 
					convert(char,FDATE,112) as FDATE, BEFORE, isnull(V_DAMT,0) as V_DAMT,	isnull(TNV_DAMT,0) as TNV_DAMT, isnull(TAXAMT,0) as TAXAMT, 
					isnull(PV_PAY,0) as PV_PAY, isnull(NV_PAY,0) as NV_PAY, isnull(DUEVAT,0) as DUEVAT, convert(char,LDATE,112) as LDATE
					FROM(  
						SELECT A.DTSTOPV, A.LOCAT, A.CONTNO, A.CUSCOD, A.SDATE, A.TOTPRC, A.VATPRC, A.VATPRES, A.VATDWN, (
						SELECT (CASE WHEN SUM(VATAMT) IS NOT NULL THEN SUM(VATAMT) ELSE 0 END) AS PVATDWN FROM {$this->MAuth->getdb('TAXTRAN')} 
						WHERE  TAXTYP='S' AND FLAG<>'C' AND CONTNO=A.CONTNO AND TAXDT <= @dateto) AS PVATDWN, A.FDATE,
						LDATE, B.VAT, (CASE WHEN B.VAT-B.PVAT IS NOT NULL THEN B.VAT-B.PVAT ELSE 0 END) AS BEFORE ,  
						(CASE WHEN B.TAXAMT IS NOT NULL THEN B.TAXAMT ELSE 0 END)TAXAMT, 
						(CASE WHEN B.PV_PAY IS NOT NULL THEN B.PV_PAY ELSE 0 END) AS PV_PAY,  
						(CASE WHEN B.NV_PAY IS NOT NULL THEN B.NV_PAY ELSE 0 END) AS NV_PAY, 
						(SELECT V_DAMT FROM {$this->MAuth->getdb('ARPAY')} WHERE CONTNO=A.CONTNO AND DDATE >= @datefrm AND DDATE <= @dateto) AS V_DAMT, 
						(SELECT (V_DAMT-V_PAYMENT) AS DUEVAT FROM {$this->MAuth->getdb('ARPAY')} WHERE CONTNO=A.CONTNO AND DDATE >= @datefrm AND DDATE <= @dateto) AS DUEVAT, 
						(SELECT SUM(V_DAMT) AS TNV_DAMT FROM {$this->MAuth->getdb('ARPAY')} WHERE CONTNO=A.CONTNO AND DDATE <= @dateto) AS TNV_DAMT    
						FROM {$this->MAuth->getdb('ARMAST')} A  
						LEFT OUTER JOIN (
							SELECT CONTNO, SUM(V_DAMT) AS VAT, SUM(PVAT) AS PVAT, SUM(TAXAMT) AS TAXAMT, 
							SUM(PV_PAY) AS PV_PAY, SUM(NV_PAY) AS NV_PAY
							FROM(	
								SELECT V_DAMT, CONTNO, TAXAMT, (CASE WHEN TAXDT <= @dateto THEN V_PAYMENT ELSE 0 END) AS PVAT,
								(CASE WHEN TAXAMT=V_PAYMENT THEN V_PAYMENT ELSE 0 END) AS PV_PAY,
								(CASE WHEN TAXAMT>V_PAYMENT THEN V_PAYMENT ELSE 0 END ) AS NV_PAY  
								FROM {$this->MAuth->getdb('ARPAY')} 
							)A  
							GROUP BY CONTNO
						)B ON A.CONTNO=B.CONTNO  
					)A  
					WHERE LOCAT like @locat ".$cond."
				)main
		";//echo $sql; 
		$query = $this->db->query($sql);
		
		$sql = "
				declare @year2 varchar(4)	= ".$VATYEAR."-543 ;
				declare @month2 varchar(2)	= ".$VATMONTH.";
				declare @dateto2 datetime	= (select dateadd(day,-1,dateadd(month,1,convert(date,@year2+'-'+@month2+'-1'))));
				IF OBJECT_ID('tempdb..#main2') IS NOT NULL DROP TABLE #main2
				select *
				into #main2
				from(
					select LOCAT, a.CONTNO, CUSNAME, SDATE, 
					DTSTOPV,
					TOTPRC,			--ราคาขาย
					VATPRC,			--ภาษีขาย
					VATPRES,		--ภาษีเงินจอง
					VATDWN,			--ภาษีดาวน์
					PVATDWN,		--ภาษีดาวน์ชำระแล้ว
					KANGDWN,		--ภาษีดาวนืค้างชำระ
					FDATE, 
					VAT,			--ค่างวดคงเหลือ
					BEFORE,			--ภาษีค่างวดยกมา
					V_DAMT,			--ภาษีตามดิว
					TNV_DAMT,		--ภาษีค่างวดถึงดิวปัจจุบัน
					isnull(case when DTSTOPV is not null then VATSTOP else 0 end,0) as VATSTOP,			--ภาษีหยุดvatไว้
					TAXAMT,			--ภาษีนำส่งแล้ว
					PV_PAY,			--นำส่งแล้วชำระแล้ว
					NV_PAY,			--นำส่งแล้วค้างชำระ
					DUEVAT,			--ภาษีคงเหลือตามดิว
					isnull(case when DTSTOPV is not null then VATSTOP else 0 end,0)+DUEVAT as VATBAL	--ภาษีคงเหลือตามจิง
					,LDATE 
					from #main a
					left join(
						select a.CONTNO, SUM(b.V_DAMT) as VATSTOP 
						from #main a
						left join {$this->MAuth->getdb('ARPAY')} b on a.CONTNO = b.CONTNO and b.DDATE >= a.DTSTOPV and b.DDATE <= @dateto2
						group by a.CONTNO
					)b on a.CONTNO = b.CONTNO
				)main2
		";//echo $sql;
		$query = $this->db->query($sql);
		
		$sql = "
				select LOCAT, CONTNO, CUSNAME, SDATE, TOTPRC, VATPRC, VATPRES, VATDWN, PVATDWN, KANGDWN, FDATE, VAT, BEFORE, V_DAMT, TNV_DAMT,
				VATSTOP, TAXAMT, PV_PAY, NV_PAY, DUEVAT, VATBAL, LDATE
				from #main2
				".$orderby."
		";//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$sql = "
				select COUNT(CONTNO) as Total, sum(TOTPRC) as sumTOTPRC, sum(VATPRC) as sumVATPRC, sum(VATPRES) as sumVATPRES,		
				sum(VATDWN) as sumVATDWN, sum(PVATDWN) as sumPVATDWN, sum(KANGDWN) as sumKANGDWN, sum(VAT) as sumVAT,
				sum(BEFORE) as sumBEFORE, sum(V_DAMT) as sumV_DAMT, sum(TNV_DAMT) as sumTNV_DAMT, sum(VATSTOP) as sumVATSTOP, 
				sum(TAXAMT) as sumTAXAMT,sum(PV_PAY) as sumPV_PAY, sum(NV_PAY) as sumNV_PAY,	sum(DUEVAT) as sumDUEVAT, 
				sum(VATBAL) as sumVATBAL
				from #main2 
		";//echo $sql; exit;
		$query2 = $this->db->query($sql);
		
		$head = ""; $html = ""; $head2 = "";  $report = ""; $sumreport = ""; $sumreport2 = ""; $i = 0; 
		
		$head = "<tr style='height:25px;background-color:#D3ECDC;'>
					<th style='vertical-align:top;taxt-align:left;display:none;'>#</th>
					<th style='vertical-align:top;taxt-align:left;'>สาขา</th>
					<th style='vertical-align:top;taxt-align:left;'>เลขที่สัญญา<br>ชื่อ-นามสกุล</th>
					<th style='vertical-align:top;taxt-align:center;'>วันที่ทำสัญญา</th>
					<th style='vertical-align:top;taxt-align:center;'>วันดิวงวดแรก<br>วันดิวงวดสุดท้าย</th>
					<th style='vertical-align:top;taxt-align:right;'>ราคาขาย<br>ภาษีตามดิว</th>
					<th style='vertical-align:top;taxt-align:right;'>ภาษีขาย<br>ภาษีค่างวดถึงดิว<br>ปัจจุบัน</th>
					<th style='vertical-align:top;taxt-align:right;'>ภาษีเงินจอง<br>ภาษีหยุดvatไว้</th>
					<th style='vertical-align:top;taxt-align:right;'>ภาษีดาวน์<br>ภาษีนำส่งแล้ว</th>
					<th style='vertical-align:top;taxt-align:right;'>ภาษีดาวน์ชำระแล้ว<br>นำส่งแล้วชำระแล้ว</th>
					<th style='vertical-align:top;taxt-align:right;'>ภาษีดาวน์ค้างชำระ<br>นำส่งแล้วค้างชำระ</th>
					<th style='vertical-align:top;taxt-align:right;'>ภาษีค่างวด<br>ภาษีคงเหลือ<br>ตามดิว</th>
					<th style='vertical-align:top;taxt-align:right;'>ภาษีค่างวดยกมา<br>ภาษีคงเหลือตามจริง</th>
				</tr>
		";
		
		$NRow = 1;
		if($query->row()){
			foreach($query->result() as $row){$i++;
				$html .= "
					<tr class='trow' seq=".$NRow.">
						<td seq=".$NRow." style='display:none;'>".$NRow++."</td>
						<td>".$row->LOCAT."</td>
						<td>".$row->CONTNO."<br>".$row->CUSNAME."</td>
						<td align='center'>".$this->Convertdate(2,$row->SDATE)."</td>
						<td align='center'>".$this->Convertdate(2,$row->FDATE)."<br>".$this->Convertdate(2,$row->LDATE)."</td>
						<td align='right'>".number_format($row->TOTPRC,2)."<br>".number_format($row->V_DAMT,2)."</td>
						<td align='right'>".number_format($row->VATPRC,2)."<br>".number_format($row->TNV_DAMT,2)."</td>
						<td align='right'>".number_format($row->VATPRES,2)."<br>".number_format($row->VATSTOP,2)."</td>
						<td align='right'>".number_format($row->VATDWN,2)."<br>".number_format($row->TAXAMT,2)."</td>
						<td align='right'>".number_format($row->PVATDWN,2)."<br>".number_format($row->PV_PAY,2)."</td>
						<td align='right'>".number_format($row->KANGDWN,2)."<br>".number_format($row->NV_PAY,2)."</td>
						<td align='right'>".number_format($row->VAT,2)."<br>".number_format($row->DUEVAT,2)."</td>
						<td align='right'>".number_format($row->BEFORE,2)."<br>".number_format($row->VATBAL,2)."</td>
					</tr>
				";	
			}
		}
		
		$head2 = "<tr>
					<th style='vertical-align:top;taxt-align:left;'>#</th>
					<th style='vertical-align:top;taxt-align:left;'>สาขา</th>
					<th style='vertical-align:top;taxt-align:left;'>เลขที่สัญญา</th>
					<th style='vertical-align:top;taxt-align:left;'>ชื่อ-นามสกุล</th>
					<th style='vertical-align:top;taxt-align:left;'>วันที่ทำสัญญา</th>
					<th style='vertical-align:top;taxt-align:left;'>วันดิวงวดแรก</th>
					<th style='vertical-align:top;taxt-align:left;'>วันดิวงวดสุดท้าย</th>
					<th style='vertical-align:top;taxt-align:right;'>ราคาขาย</th>
					<th style='vertical-align:top;taxt-align:right;'>ภาษีขาย</th>
					<th style='vertical-align:top;taxt-align:right;'>ภาษีเงินจอง</th>
					<th style='vertical-align:top;taxt-align:right;'>ภาษีดาวน์</th>
					<th style='vertical-align:top;taxt-align:right;'>ภาษีดาวน์ชำระแล้ว</th>
					<th style='vertical-align:top;taxt-align:right;'>ภาษีดาวนืค้างชำระ</th>
					<th style='vertical-align:top;taxt-align:right;'>ภาษีค่างวด</th>
					<th style='vertical-align:top;taxt-align:right;'>ภาษีค่างวดยกมา</th>
					<th style='vertical-align:top;taxt-align:right;'>ภาษีตามดิว</th>
					<th style='vertical-align:top;taxt-align:right;'>ภาษีค่างวดถึงดิว<br>ปัจจุบัน</th>
					<th style='vertical-align:top;taxt-align:right;'>ภาษีหยุดvatไว้</th>
					<th style='vertical-align:top;taxt-align:right;'>ภาษีนำส่งแล้ว</th>
					<th style='vertical-align:top;taxt-align:right;'>นำส่งแล้วชำระแล้ว</th>
					<th style='vertical-align:top;taxt-align:right;'>นำส่งแล้วค้างชำระ</th>
					<th style='vertical-align:top;taxt-align:right;'>ภาษีคงเหลือตามดิว</th>
					<th style='vertical-align:top;taxt-align:right;'>ภาษีคงเหลือตามจริง</th>
				</tr>
		";
		
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
						<td style='mso-number-format:\"\@\";'>".$this->Convertdate(2,$row->FDATE)."</td>
						<td style='mso-number-format:\"\@\";'>".$this->Convertdate(2,$row->LDATE)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->TOTPRC,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->VATPRC,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->VATPRES,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->VATDWN,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->PVATDWN,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->KANGDWN,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->VAT,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->BEFORE,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->V_DAMT,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->TNV_DAMT,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->VATSTOP,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->TAXAMT,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->PV_PAY,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->NV_PAY,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->DUEVAT,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->VATBAL,2)."</td>
					</tr>
				";	
			}
		}
		
		if($query2->row()){
			foreach($query2->result() as $row){
				$sumreport .= "
					<tr class='trow' style='height:25px;background-color:#D3ECDC;'>
						<th style='border:0px;vertical-align:middle;text-align:left;' colspan='4'>รวมทั้งสิ้น ".number_format($row->Total)." รายการ</th>
						<th style='border:0px;vertical-align:middle;text-align:right;'>".number_format($row->sumTOTPRC,2)."<br>".number_format($row->sumV_DAMT,2)."</th>
						<th style='border:0px;vertical-align:middle;text-align:right;'>".number_format($row->sumVATPRC,2)."<br>".number_format($row->sumTNV_DAMT,2)."</th>
						<th style='border:0px;vertical-align:middle;text-align:right;'>".number_format($row->sumVATPRES,2)."<br>".number_format($row->sumVATSTOP,2)."</th>
						<th style='border:0px;vertical-align:middle;text-align:right;'>".number_format($row->sumVATDWN,2)."<br>".number_format($row->sumTAXAMT,2)."</th>
						<th style='border:0px;vertical-align:middle;text-align:right;'>".number_format($row->sumPVATDWN,2)."<br>".number_format($row->sumPV_PAY,2)."</th>
						<th style='border:0px;vertical-align:middle;text-align:right;'>".number_format($row->sumKANGDWN,2)."<br>".number_format($row->sumNV_PAY,2)."</th>
						<th style='border:0px;vertical-align:middle;text-align:right;'>".number_format($row->sumVAT,2)."<br>".number_format($row->sumDUEVAT,2)."</th>
						<th style='border:0px;vertical-align:middle;text-align:right;'>".number_format($row->sumBEFORE,2)."<br>".number_format($row->sumVATBAL,2)."</th>
					</tr>
				";	
			}
		}
		
		if($query2->row()){
			foreach($query2->result() as $row){
				$sumreport2 .= "
					<tr class='trow'>
						<th style='mso-number-format:\"\@\";text-align:left;' colspan='7'>รวมทั้งสิ้น ".number_format($row->Total)." รายการ</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumTOTPRC,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumVATPRC,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumVATPRES,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumVATDWN,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumPVATDWN,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumKANGDWN,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumVAT,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumBEFORE,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumV_DAMT,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumTNV_DAMT,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumVATSTOP,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumTAXAMT,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumPV_PAY,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumNV_PAY,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumDUEVAT,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumVATBAL,2)."</th>
					</tr>
				";	
			}
		}
		
		if($i>0){
			$html = "
				<div id='table-fixed-ReportVatsaleH' class='col-sm-12' style='height:100%;width:100%;overflow:auto;font-size:9pt;'>
					<table id='table-ReportVatsaleH' style='background-color:white;' class='col-sm-12 display table table-bordered' cellspacing='0' width='calc(100% - 1px)'>
						<thead>
						<tr style='height:40px;'>
							<th colspan='12' style='font-size:12pt;border:0px;vertical-align;middle;text-align:center;'>รายงานภาษีคงเหลือจากการขายผ่อน</th>
						</tr>
						<tr style='height:25px;'>
							<td colspan='12' style='border-bottom:1px solid #ddd;vertical-align;middle;text-align:center;'>".$rpcond."  ภาษี ณ ".$_REQUEST["VATMONTH"]."/".$_REQUEST["VATYEAR"]."  ออกรายงาน ณ วันที่ ".$this->today('today')."</td>
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
			<div id='table-fixed-ReportVatsaleH2' class='col-sm-12' style='height:100%;width:100%;overflow:auto;'>
				<table id='table-ReportVatsaleH2' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='100%'>
					<thead>
						<tr>
							<th colspan='23' style='font-size:12pt;border:0px;text-align:center;'>รายงานภาษีคงเหลือจากการขายผ่อน</th>
						</tr>
						<tr>
							<td colspan='23' style='border:0px;text-align:center;'>".$rpcond."  ภาษี ณ ".$_REQUEST["VATMONTH"]."/".$_REQUEST["VATYEAR"]."  ออกรายงาน ณ วันที่ ".$this->today('today')."</td>
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
						$_REQUEST["CONTNO2"].'||'.
						$_REQUEST["VATMONTH"].'||'.
						$_REQUEST["VATYEAR"].'||'.
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
		$CONTNO2 	= str_replace(chr(0),'',$tx[2]);
		$VATMONTH 	= $tx[3];
		$VATYEAR 	= $tx[4];
		$order 		= $tx[5];
		$layout 	= $tx[6];
		
		$cond = ""; $rpcond = "";
		
		if($LOCAT1 != ""){
			$rpcond .= "  สาขา ".$LOCAT1;
		}
		
		if($CONTNO1 != ""){
			$rpcond .= "  จากเลขที่สัญญา ".$CONTNO1;
			$cond  	.= " and CONTNO >= '".$CONTNO1."'";
		}
		
		if($CONTNO2 != ""){
			$rpcond .= "  ถึงเลขที่สัญญา ".$CONTNO2;
			$cond 	.= " and CONTNO <= '".$CONTNO2."'";
		}
		
		$orderby = ""; $ordername = "";
		if($order == "CONTNO"){
			$orderby = "order by CONTNO, LOCAT, SDATE";
			$ordername = "เลขที่สัญญา";
		}else if($order == "LOCAT"){
			$orderby = "order by LOCAT, SDATE, CONTNO"; 
			$ordername = "สาขา";
		}else if($order == "SDATE"){
			$orderby = "order by SDATE, LOCAT, CONTNO"; 
			$ordername = "วันที่ขาย";
		}
		
		$sql = "
				declare @locat varchar(10)	= '".$LOCAT1."' ;
				declare @year varchar(4)	= ".$VATYEAR."-543 ;
				declare @month varchar(2)	= ".$VATMONTH.";
				declare @datefrm datetime	= (select @year+'-'+@month+'-1');
				declare @dateto datetime	= (select dateadd(day,-1,dateadd(month,1,convert(date,@year+'-'+@month+'-1'))));

				IF OBJECT_ID('tempdb..#main') IS NOT NULL DROP TABLE #main
				select *
				into #main
				from(	
					SELECT LOCAT, CONTNO, DTSTOPV, (SELECT RTRIM(SNAM)+' '+RTRIM(NAME1)+' '+NAME2 FROM {$this->MAuth->getdb('CUSTMAST')} WHERE CUSCOD = A.CUSCOD) AS CUSNAME,
					convert(char,SDATE,112) as SDATE, TOTPRC, VATPRC, VATPRES, VATDWN, PVATDWN, VATDWN-PVATDWN as KANGDWN, isnull(VAT,0) as VAT, 
					convert(char,FDATE,112) as FDATE, BEFORE, isnull(V_DAMT,0) as V_DAMT,	isnull(TNV_DAMT,0) as TNV_DAMT, isnull(TAXAMT,0) as TAXAMT, 
					isnull(PV_PAY,0) as PV_PAY, isnull(NV_PAY,0) as NV_PAY, isnull(DUEVAT,0) as DUEVAT, convert(char,LDATE,112) as LDATE
					FROM(  
						SELECT A.DTSTOPV, A.LOCAT, A.CONTNO, A.CUSCOD, A.SDATE, A.TOTPRC, A.VATPRC, A.VATPRES, A.VATDWN, (
						SELECT (CASE WHEN SUM(VATAMT) IS NOT NULL THEN SUM(VATAMT) ELSE 0 END) AS PVATDWN FROM {$this->MAuth->getdb('TAXTRAN')} 
						WHERE  TAXTYP='S' AND FLAG<>'C' AND CONTNO=A.CONTNO AND TAXDT <= @dateto) AS PVATDWN, A.FDATE,
						LDATE, B.VAT, (CASE WHEN B.VAT-B.PVAT IS NOT NULL THEN B.VAT-B.PVAT ELSE 0 END) AS BEFORE ,  
						(CASE WHEN B.TAXAMT IS NOT NULL THEN B.TAXAMT ELSE 0 END)TAXAMT, 
						(CASE WHEN B.PV_PAY IS NOT NULL THEN B.PV_PAY ELSE 0 END) AS PV_PAY,  
						(CASE WHEN B.NV_PAY IS NOT NULL THEN B.NV_PAY ELSE 0 END) AS NV_PAY, 
						(SELECT V_DAMT FROM {$this->MAuth->getdb('ARPAY')} WHERE CONTNO=A.CONTNO AND DDATE >= @datefrm AND DDATE <= @dateto) AS V_DAMT, 
						(SELECT (V_DAMT-V_PAYMENT) AS DUEVAT FROM {$this->MAuth->getdb('ARPAY')} WHERE CONTNO=A.CONTNO AND DDATE >= @datefrm AND DDATE <= @dateto) AS DUEVAT, 
						(SELECT SUM(V_DAMT) AS TNV_DAMT FROM {$this->MAuth->getdb('ARPAY')} WHERE CONTNO=A.CONTNO AND DDATE <= @dateto) AS TNV_DAMT    
						FROM {$this->MAuth->getdb('ARMAST')} A  
						LEFT OUTER JOIN (
							SELECT CONTNO, SUM(V_DAMT) AS VAT, SUM(PVAT) AS PVAT, SUM(TAXAMT) AS TAXAMT, 
							SUM(PV_PAY) AS PV_PAY, SUM(NV_PAY) AS NV_PAY
							FROM(	
								SELECT V_DAMT, CONTNO, TAXAMT, (CASE WHEN TAXDT <= @dateto THEN V_PAYMENT ELSE 0 END) AS PVAT,
								(CASE WHEN TAXAMT=V_PAYMENT THEN V_PAYMENT ELSE 0 END) AS PV_PAY,
								(CASE WHEN TAXAMT>V_PAYMENT THEN V_PAYMENT ELSE 0 END ) AS NV_PAY  
								FROM {$this->MAuth->getdb('ARPAY')} 
							)A  
							GROUP BY CONTNO
						)B ON A.CONTNO=B.CONTNO  
					)A  
					WHERE LOCAT like @locat ".$cond."
				)main
		";//echo $sql; 
		$query = $this->db->query($sql);
		
		$sql = "
				declare @year2 varchar(4)	= ".$VATYEAR."-543 ;
				declare @month2 varchar(2)	= ".$VATMONTH.";
				declare @dateto2 datetime	= (select dateadd(day,-1,dateadd(month,1,convert(date,@year2+'-'+@month2+'-1'))));
				IF OBJECT_ID('tempdb..#main2') IS NOT NULL DROP TABLE #main2
				select *
				into #main2
				from(
					select LOCAT, a.CONTNO, CUSNAME, SDATE, 
					DTSTOPV,
					TOTPRC,			--ราคาขาย
					VATPRC,			--ภาษีขาย
					VATPRES,		--ภาษีเงินจอง
					VATDWN,			--ภาษีดาวน์
					PVATDWN,		--ภาษีดาวน์ชำระแล้ว
					KANGDWN,		--ภาษีดาวนืค้างชำระ
					FDATE, 
					VAT,			--ค่างวดคงเหลือ
					BEFORE,			--ภาษีค่างวดยกมา
					V_DAMT,			--ภาษีตามดิว
					TNV_DAMT,		--ภาษีค่างวดถึงดิวปัจจุบัน
					isnull(case when DTSTOPV is not null then VATSTOP else 0 end,0) as VATSTOP,			--ภาษีหยุดvatไว้
					TAXAMT,			--ภาษีนำส่งแล้ว
					PV_PAY,			--นำส่งแล้วชำระแล้ว
					NV_PAY,			--นำส่งแล้วค้างชำระ
					DUEVAT,			--ภาษีคงเหลือตามดิว
					isnull(case when DTSTOPV is not null then VATSTOP else 0 end,0)+DUEVAT as VATBAL	--ภาษีคงเหลือตามจิง
					,LDATE 
					from #main a
					left join(
						select a.CONTNO, SUM(b.V_DAMT) as VATSTOP 
						from #main a
						left join {$this->MAuth->getdb('ARPAY')} b on a.CONTNO = b.CONTNO and b.DDATE >= a.DTSTOPV and b.DDATE <= @dateto2
						group by a.CONTNO
					)b on a.CONTNO = b.CONTNO
				)main2
		";//echo $sql;
		$query = $this->db->query($sql);
		
		$sql = "
				select LOCAT, CONTNO, CUSNAME, SDATE, TOTPRC, VATPRC, VATPRES, VATDWN, PVATDWN, KANGDWN, FDATE, VAT, BEFORE, V_DAMT, TNV_DAMT,
				VATSTOP, TAXAMT, PV_PAY, NV_PAY, DUEVAT, VATBAL, LDATE
				from #main2
				".$orderby."
		";//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$sql = "
				select COUNT(CONTNO) as Total, sum(TOTPRC) as sumTOTPRC, sum(VATPRC) as sumVATPRC, sum(VATPRES) as sumVATPRES,		
				sum(VATDWN) as sumVATDWN, sum(PVATDWN) as sumPVATDWN, sum(KANGDWN) as sumKANGDWN, sum(VAT) as sumVAT,
				sum(BEFORE) as sumBEFORE, sum(V_DAMT) as sumV_DAMT, sum(TNV_DAMT) as sumTNV_DAMT, sum(VATSTOP) as sumVATSTOP, 
				sum(TAXAMT) as sumTAXAMT,sum(PV_PAY) as sumPV_PAY, sum(NV_PAY) as sumNV_PAY,	sum(DUEVAT) as sumDUEVAT, 
				sum(VATBAL) as sumVATBAL
				from #main2 
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
					<th width='70px'	align='left' 	style='font-size:7.5pt;border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;'>สาขา<br>วันที่ทำสัญญา</th>
					<th width='180px'	align='left' 	style='font-size:7.5pt;border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;'>เลขที่สัญญา<br>ชื่อ-นามสกุล</th>
					<th width='70px'	align='center' 	style='font-size:7.5pt;border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;'>วันดิวแรก<br>วันดิวสุดท้าย</th>
					<th width='90px'	align='right' 	style='font-size:7.5pt;border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;'>ราคาขาย<br>ภาษีตามดิว</th>
					<th width='90px'	align='right' 	style='font-size:7.5pt;border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;'>ภาษีขาย<br>ภาษีค่างวดถึงดิว<br>ปัจจุบัน</th>
					<th width='90px'	align='right' 	style='font-size:7.5pt;border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;'>ภาษีเงินจอง<br>ภาษีหยุดvatไว้</th>
					<th width='90px'	align='right' 	style='font-size:7.5pt;border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;'>ภาษีดาวน์<br>ภาษีนำส่งแล้ว</th>
					<th width='90px'	align='right' 	style='font-size:7.5pt;border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;'>ภาษีดาวน์ชำระแล้ว<br>นำส่งแล้วชำระแล้ว</th>
					<th width='90px'	align='right' 	style='font-size:7.5pt;border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;'>ภาษีดาวน์ค้างชำระ<br>นำส่งแล้วค้างชำระ</th>
					<th width='90px'	align='right' 	style='font-size:7.5pt;border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;'>ภาษีค่างวด<br>ภาษีคงเหลือ<br>ตามดิว</th>
					<th width='90px'	align='right' 	style='font-size:7.5pt;border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;'>ภาษีค่างวดยกมา<br>ภาษีคงเหลือ<br>ตามจริง</th>
				</tr>
		";
		
		$No = 1;
		if($query->row()){
			foreach($query->result() as $row){	
				$html .= "
					<tr class='trow' seq=".$No.">
						<td width='70px'	height='40px'	align='left'	style='vertical-align:top;'>".$row->LOCAT."<br>".$this->Convertdate(2,$row->SDATE)."</td>
						<td width='180px'	height='40px'	align='left'	style='vertical-align:top;'>".$row->CONTNO."<br>".$row->CUSNAME."</td>
						<td width='70px'	height='40px'	align='center'	style='vertical-align:top;'>".$this->Convertdate(2,$row->FDATE)."<br>".$this->Convertdate(2,$row->LDATE)."</td>
						<td width='90px'	height='40px'	align='right'	style='vertical-align:top;'>".number_format($row->TOTPRC,2)."<br>".number_format($row->V_DAMT,2)."</td>
						<td width='90px'	height='40px'	align='right'	style='vertical-align:top;'>".number_format($row->VATPRC,2)."<br>".number_format($row->TNV_DAMT,2)."</td>
						<td width='90px'	height='40px'	align='right'	style='vertical-align:top;'>".number_format($row->VATPRES,2)."<br>".number_format($row->VATSTOP,2)."</td>
						<td width='90px'	height='40px'	align='right'	style='vertical-align:top;'>".number_format($row->VATDWN,2)."<br>".number_format($row->TAXAMT,2)."</td>
						<td width='90px'	height='40px'	align='right'	style='vertical-align:top;'>".number_format($row->PVATDWN,2)."<br>".number_format($row->PV_PAY,2)."</td>
						<td width='90px'	height='40px'	align='right'	style='vertical-align:top;'>".number_format($row->KANGDWN,2)."<br>".number_format($row->NV_PAY,2)."</td>
						<td width='90px'	height='40px'	align='right'	style='vertical-align:top;'>".number_format($row->VAT,2)."<br>".number_format($row->DUEVAT,2)."</td>
						<td width='90px'	height='40px'	align='right'	style='vertical-align:top;'>".number_format($row->BEFORE,2)."<br>".number_format($row->VATBAL,2)."</td>
					</tr>
				";	
			}
		}
		
		if($query2->row()){
			foreach($query2->result() as $row){	
				$html .= "
					<tr class='trow' style='background-color:#ebebeb;'>
						<td height='40px'	align='left'	style='border-top:0.1px solid black;border-bottom:0.1px solid black;border-left:0.1px solid black;vertical-align:middle;' colspan='3'>รวมทั้งสิ้น ".number_format($row->Total)." รายการ</td>
						<td height='40px'	align='right'	style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:middle;'>".number_format($row->sumTOTPRC,2)."<br>".number_format($row->sumV_DAMT,2)."</td>
						<td height='40px'	align='right'	style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:middle;'>".number_format($row->sumVATPRC,2)."<br>".number_format($row->sumTNV_DAMT,2)."</td>
						<td height='40px'	align='right'	style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:middle;'>".number_format($row->sumVATPRES,2)."<br>".number_format($row->sumVATSTOP,2)."</td>
						<td height='40px'	align='right'	style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:middle;'>".number_format($row->sumVATDWN,2)."<br>".number_format($row->sumTAXAMT,2)."</td>
						<td height='40px'	align='right'	style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:middle;'>".number_format($row->sumPVATDWN,2)."<br>".number_format($row->sumPV_PAY,2)."</td>
						<td height='40px'	align='right'	style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:middle;'>".number_format($row->sumKANGDWN,2)."<br>".number_format($row->sumNV_PAY,2)."</td>
						<td height='40px'	align='right' 	style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:middle;'>".number_format($row->sumVAT,2)."<br>".number_format($row->sumDUEVAT,2)."</td>
						<td height='40px'	align='right'	style='border-top:0.1px solid black;border-bottom:0.1px solid black;border-right:0.1px solid black;'>".number_format($row->sumBEFORE,2)."<br>".number_format($row->sumVATBAL,2)."</td>
					</tr>
					
				";	
			}
		}
		$body = "<table class='wf fs8' cellspacing='0'>".$html."</table>";
		
		$mpdf = new \Mpdf\Mpdf([
			'mode' => 'utf-8', 
			'format' => $layout,
			'margin_top' => ($layout == "A4-L" ? "78" : "57"), 	//default = 16
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
				.fs9 { font-size:9pt; }
				.h30 { height:30px; }
				.bor { border-top:0.1px solid black;border-bottom:0.1px solid black;}
			</style>
		";
		
		$header = "
			<table class='wf fs9' cellspacing='0' style='border-collapse:collapse;line-height:23px;overflow:wrap;vertical-align:text-top;'>
				<tr>
					<th colspan='11' style='font-size:11pt;'>".$COMP_NM."<br>รายงานภาษีคงเหลือจากการขายผ่อน</th>
				</tr>
				<tr>
					<td colspan='11' style='height:35px;text-align:center;'>".$rpcond." ภาษี ณ ".$VATMONTH."/".$VATYEAR."</td>
				</tr>
				<tr>
					<td colspan='2' align='left'>ชื่อผู้ประกอบการ</td>
					<td colspan='9' align='left'>".$COMP_NM."</td>
				</tr>
				<tr>
					<td colspan='2' align='left'>ชื่อสถานที่ประกอบการ</td>
					<td colspan='9' align='left'>".$LOCADDR1." ".$LOCADDR2."</td>
				</tr>
				<tr>
					<td colspan='2' align='left'>เลขประจำตัวผู้เสียภาษี</td>
					<td colspan='4' align='left'>".$TAXID."</td>
					<td colspan='5' align='right'>เรียงรายงาน: ".$ordername."</td>
				</tr>
				<tr>
					<td colspan='2' align='left'>วันที่พิมพ์รายงาน</td>
					<td colspan='4' align='left'>".date('d/m/').(date('Y')+543)." ".date('H:i')."</td>
					<td colspan='5' align='right'>หน้าที่ : {PAGENO} / {nb} &emsp;&emsp;</td>
					
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