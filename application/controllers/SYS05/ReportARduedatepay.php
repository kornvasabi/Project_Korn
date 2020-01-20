<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//BEE+
class ReportARduedatepay extends MY_Controller {
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
							<br>รายงานลูกหนี้ครบกำหนดชำระค่างวด<br>
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
									เลขที่สัญญา
									<select id='CONTNO1' class='form-control input-sm' data-placeholder='เลขที่สัญญา'></select>
								</div>
							</div>
							<div class='col-sm-4 col-xs-4'>	
								<div class='form-group' >
									BillColl
									<select id='BILLCOL1' class='form-control input-sm' data-placeholder='BillColl'></select>
								</div>
							</div>
							<div class='col-sm-4 col-xs-4'>	
								<div class='form-group'>
									จากวันที่ดิว
									<input type='text' id='FRMDATE' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' placeholder='จากวันที่ดิว' value='".$this->today('startofmonth')."' style='font-size:10.5pt'>
								</div>
							</div>
							<div class='col-sm-4 col-xs-4'>	
								<div class='form-group'>
									ถึงวันที่ดิว
									<input type='text' id='TODATE' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' placeholder='ถึงวันที่' value='".$this->today('today')."' style='font-size:10.5pt'>
								</div>
							</div>
							<div class='col-sm-4 col-xs-4'>	
								<div class='form-group'>
									ตำบล
									<input type='text' id='TUMBON1' class='form-control input-sm' style='font-size:10.5pt' value=''>
								</div>
							</div>
							<div class='col-sm-4 col-xs-4'>	
								<div class='form-group' >
									อำเภอ
									<select id='AMPHUR1' class='form-control input-sm AUMP' data-placeholder='อำเภอ'></select>
								</div>
							</div>
							<div class='col-sm-8 col-xs-8'>	
								<div class='form-group' >
									จังหวัด
									<select id='PROVINCE1' class='form-control input-sm AUMP' data-placeholder='จังหวัด'></select>
								</div>
							</div>
						</div>
						<div class='col-sm-8 col-xs-8 col-sm-offset-2'>
							<br>
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
									เรียงลำดับข้อมูล
									<div class='col-sm-12 col-xs-12' style='border:0.1px dotted #d6d6d6;'>	
										<div class='col-sm-12 col-xs-12'>
											<div class='form-group'>
												<br>
												<input type= 'radio' id='contno' name='orderby'> เลขที่สัญญา
												<br><br>
												<input type= 'radio' id='billcoll' name='orderby' checked> พนักงานเก็บเงิน
												<br><br>
												<input type= 'radio' id='duedate' name='orderby'> วันดิว
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class='col-sm-4 col-xs-4'>	
								<div class='form-group'>
									แสดงผล
									<div class='col-sm-12 col-xs-12' style='border:0.1px dotted #d6d6d6;'>	
										<div class='col-sm-12 col-xs-12'>
											<div class='form-group'>
												<br>
												<input type= 'radio' id='detail' name='report' checked> แสดงรายการ
												<br><br><br><br>
												<input type= 'radio' id='summary' name='report'> สรุป
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class='col-sm-8 col-xs-8 col-sm-offset-2' style='text-align:center;color:#999;'><br><br>รายงานนี้ใช้พิมพ์ทุกต้นเดือน ช่วงละ 1 เดือน และไม่สามารถ ดูรายงานย้อนหลัง จะรายงานผล ณ ปัจจุบันเสมอ</div>
					</div>
					<div class='row' style='height:10%;'>
						<div class='col-sm-12 col-xs-12'><br>	
							<button id='btnt1search' class='btn btn-primary btn-sm' style='width:100%;font-size:10.5pt;'><span class='glyphicon glyphicon-search'> แสดง</span></button>
						</div>
					</div>
				</div>
			</div>
		";
		
		$html.= "<script src='".base_url('public/js/SYS05/ReportARduedatepay.js')."'></script>";
		echo $html;
	}
	
	function search(){
		$LOCAT1		= $_REQUEST["LOCAT1"];
		$CONTNO1 	= str_replace(chr(0),'',$_REQUEST["CONTNO1"]);
		$BILLCOL1 	= str_replace(chr(0),'',$_REQUEST["BILLCOL1"]);
		$AMPHUR1 	= str_replace(chr(0),'',$_REQUEST["AMPHUR1"]);
		$PROVINCE1 	= str_replace(chr(0),'',$_REQUEST["PROVINCE1"]);
		$FRMDATE 	= $this->Convertdate(1,$_REQUEST["FRMDATE"]);
		$TODATE 	= $this->Convertdate(1,$_REQUEST["TODATE"]);
		$TUMBON1 	= $_REQUEST["TUMBON1"];
		$typereport = $_REQUEST["report"];
		$orderby 	= $_REQUEST["orderby"];
		
		$cond = ""; $rpcond = "";
		
		if($LOCAT1 != ""){
			$cond .= " AND (m.LOCAT LIKE '%".$LOCAT1."%')";
			$rpcond .= "  สาขา ".$LOCAT1;
		}
		
		if($CONTNO1 != ""){
			$cond .= " AND (m.CONTNO LIKE '%".$CONTNO1."%')";
			$rpcond .= "  เลขที่สัญญา ".$CONTNO1;
		}
		
		if($BILLCOL1 != ""){
			$cond .= " AND (m.BILLCOLL LIKE '%".$BILLCOL1."%' )";
			$rpcond .= "  พนักงานเก็บเงิน ".$BILLCOL1;
		}else{
			$cond .= " AND (m.BILLCOLL LIKE '%%' OR m.BILLCOLL IS NULL)";
		}
		
		if($AMPHUR1 != ""){
			$cond .= " AND (a.AUMPCOD LIKE '%".$AMPHUR1."%' )";
		}else{
			$cond .= " AND (a.AUMPCOD LIKE '%%' OR a.AUMPCOD IS NULL)";
		}
		
		if($PROVINCE1 != ""){
			$cond .= " AND (a.PROVCOD LIKE '%".$PROVINCE1."%' )";
		}else{
			$cond .= " AND (a.PROVCOD LIKE '%%' OR a.PROVCOD IS NULL)";
		}
		
		if($TUMBON1 != ""){
			$cond .= " AND a.TUMB LIKE '%".$TUMBON1."%'";
		}
		
		$sql = "
				IF OBJECT_ID('tempdb..#main') IS NOT NULL DROP TABLE #main
				select *
				into #main
				from(
					select LOCAT, CONTNO, CUSCOD, SNAM+NAME1+' '+NAME2 as CUSNAME, isnull(addr1,'')+isnull(' ถ.'+addr2,'')+isnull(' ต.'+tumb,'')+
					isnull(' อ.'+aumpdes,'')+isnull(' จ.'+provdes,'')+' '+isnull(zip,'')+isnull(' โทร. '+telp,'') as CUSADD, SDATE, FDATE, LDATE,
					TOTPRC, TOTDWN, SMPAY, TOTPRC-SMPAY as TOTAR, T_NOPAY, case when (smdue-smbpay) < 0 then 0 else (smdue-smbpay) end as INIKANG,
					LNOPAY, CURNOPAY, LPAYD, LPAYA, DDATE, case when (smdue+smdue2-smbpay) < 0 then 0 else (smdue+smdue2-smbpay) end as PAYNOTINT,
					isnull(INTAMT,0)-isnull(SMPINT,0) as INTAMT, case when (smdue+smdue2-smbpay) < 0 then isnull(INTAMT,0)-isnull(SMPINT,0) 
					else (smdue+smdue2-smbpay)+isnull(INTAMT,0)-isnull(SMPINT,0) end as PAYSUMINT, SMDUE2, BILLCOLL
					from (  
						select 
						m.contno, m.locat, p.nopay, m.cuscod, m.sdate, m.fdate, m.ldate, m.t_nopay, m.smpay, m.smchq, m.totprc, m.totdwn, m.billcoll, 
						m.lpaya, m.lpayd, g.SMPINT, lpay, c.snam, c.name1, c.name2, a.addrno, a.addr1, a.addr2, a.tumb, a.aumpcod, a.provcod, a.zip, a.telp,
						case when smdue is null then 0 else smdue end as smdue, case when curnopay is null then 0 else curnopay end as curnopay,
						case when SmBPay is null then 0 else SmBPay end as SmBPay, (select aumpdes from {$this->MAuth->getdb('SETAUMP')} where aumpcod=a.aumpcod) as aumpdes,
						(select provdes from {$this->MAuth->getdb('SETPROV')} where provcod=a.provcod) as provdes, (select min(nopay) from {$this->MAuth->getdb('ARPAY')} 
						where damt>payment and contno=m.contno and locat=m.locat) as Lnopay, p.ddate, (select case when sum(damt) is null then 0 else sum(damt) end  
						from {$this->MAuth->getdb('ARPAY')} where contno=m.contno and locat=m.locat and (ddate between '".$FRMDATE."' and '".$TODATE."') and ddate<=p.ddate) as smdue2,
						(select case when sum(INTAMT) is null then 0 else sum(INTAMT) end from {$this->MAuth->getdb('ARPAY')} where contno=m.contno and locat=m.locat) as INTAMT
						from  {$this->MAuth->getdb('ARPAY')} p  
						left outer join {$this->MAuth->getdb('ARMAST')} m on p.contno=m.contno and p.locat=m.locat 
						left outer join {$this->MAuth->getdb('CUSTMAST')} c on (c.cuscod = m.cuscod)  
						left outer join {$this->MAuth->getdb('CUSTADDR')} a on (c.cuscod = a.cuscod) and (c.addrno = a.addrno)  
						left outer join (
							select locat,contno,
							sum(case when (DDate < '".$FRMDATE."') then  Damt else 0 end) As SmDue,
							max(case when (DDate >= '".$FRMDATE."') and (DDate <= '".$TODATE."') then  nopay else 0 end) as CurNopay  
							from {$this->MAuth->getdb('ARPAY')}  
							group by locat,contno
						) e on m.contno=e.contno and m.locat=e.locat  
						left outer join (
							select contno,locatpay,
							sum(case when (Tmbildt < '".$FRMDATE."') and (payfor = '006' or payfor = '007') then Payamt else 0 end) As SmBPay,
							max(L_pay) As Lpay 
							from {$this->MAuth->getdb('CHQTRAN')}  
							where  ((paytyp<>'02') or (paytyp='02' and flag='P'))  and flag <> 'C' 
							group by contno,locatpay
						) f on m.contno=f.contno and m.locat=f.locatpay 
						left outer join (
							select contno, locatpay, SUM(PAYINT) AS SMPINT 
							from {$this->MAuth->getdb('CHQTRAN')} 
							where (Tmbildt < '".$FRMDATE."')and ((paytyp<>'02') or (paytyp='02' and flag='P')) 
							and flag <> 'C' and (payfor = '006' or payfor = '007') 
							group By locatpay,Contno
						) g on m.contno=g.contno and m.locat=g.locatpay
						where ((not(m.ystat = 'O') and not(m.ystat = 'C' )) or (m.ystat is null)) and ((m.smpay < m.totprc) or ((m.smpay = m.totprc) 
						and (m.lpayd >= '".$FRMDATE."'))) and (p.ddate between '".$FRMDATE."' and '".$TODATE."')
						".$cond."
					) AS D 
				)main
		";//echo $sql; 
		$query = $this->db->query($sql);
		
		$sql = "
				select LOCAT, CONTNO, CUSCOD, CUSNAME, CUSADD, convert(nvarchar,SDATE,112) as SDATE, convert(nvarchar,FDATE,112) as FDATE, LDATE, TOTPRC, TOTDWN, SMPAY, 
				TOTAR, T_NOPAY, INIKANG, LNOPAY, CURNOPAY, convert(nvarchar,LPAYD,112) as LPAYD, LPAYA, DDATE, convert(nvarchar,DDATE,112) as DDATES, PAYNOTINT, 
				INTAMT, PAYSUMINT, SMDUE2, BILLCOLL
				from #main
				order by ".$orderby."
		";//echo $sql; exit;
		$query = $this->db->query($sql); 
		
		$sql = "
				select 'รวมทั้งหมด' as Total, sum(TOTPRC) as sumTOTPRC, sum(SMPAY) as sumSMPAY, sum(TOTAR) as sumTOTAR, sum(INIKANG) as sumINIKANG, 
				sum(LPAYA) as sumLPAYA, sum(SMDUE2) as sumSMDUE2, sum(PAYNOTINT) as sumPAYNOTINT, sum(INTAMT) as sumINTAMT, sum(PAYSUMINT) as sumPAYSUMINT
				from #main
		";//echo $sql; exit;
		$query2 = $this->db->query($sql);
		
		$head = ""; $html = ""; $head2 = "";  $report = ""; $sumreport = ""; $sumreport2 = ""; $i = 0; 
		
		if($typereport == "detail"){
			$head = "<tr style='height:30px;'>
					<th style='display:none;'>#</th>
					<th style='vertical-align:top;'>สาขา</th>
					<th style='vertical-align:top;'>เลขที่สัญญา</th>
					<th style='vertical-align:top;'>รหัสลูกค้า/ชื่อ - นามสกุล<br>ที่อยู่</th>
					<th style='vertical-align:top;'>วันที่ขาย<br>Billcoll</th>
					<th style='vertical-align:top;text-align:right;'>ราคาขาย</th> 
					<th style='vertical-align:top;text-align:right;'>ลูกหนี้คงเหลือจริง </th>
					<th style='vertical-align:top;text-align:right;'>ค้างชำระยกมา<br>วันดิว</th>
					<th style='vertical-align:top;text-align:center;'>งวดที่ต้องจ่าย<br>งวดที่งวดนี้</th>
					<th style='vertical-align:top;text-align:right;'>วันชำระล่าสุด<br>ค่างวดดิวนี้</th>
					<th style='vertical-align:top;text-align:right;'>ยอดชำระล่าสุด<br>รวมต้องชำระไม่รวมเบี้ยปรับ</th>
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
							<td>".$row->CUSCOD.'   '.$row->CUSNAME."<br>".$row->CUSADD."</td>
							<td>".$this->Convertdate(2,$row->SDATE)."<br>".$row->BILLCOLL."</td>
							<td align='right'>".number_format($row->TOTPRC,2)."</td>
							<td align='right'>".number_format($row->TOTAR,2)."</td>
							<td align='right'>".number_format($row->INIKANG,2)."<br>".number_format($row->DDATES)."</td>
							<td align='center'>".number_format($row->LNOPAY).'-'.number_format($row->CURNOPAY)."<br>".number_format($row->CURNOPAY)."</td>
							<td align='right'>".$this->Convertdate(2,$row->LPAYD)."<br>".number_format($row->SMDUE2,2)."</td>
							<td align='right'>".number_format($row->LPAYA,2)."<br>".number_format($row->PAYNOTINT,2)."</td>
						</tr>
					";	
				}
			}
			
			if($query2->row()){
				foreach($query2->result() as $row){
					$sumreport = "
						<tr style='height:25px;'>
							<th colspan='4' style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;vertical-align:middle;text-align:center;'>".$row->Total."</th>
							<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;vertical-align:middle;text-align:right;'>".number_format($row->sumTOTPRC)."</th>
							<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;vertical-align:middle;text-align:right;'>".number_format($row->sumTOTAR,2)."</th>
							<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;vertical-align:middle;text-align:right;'>".number_format($row->sumINIKANG,2)."</th>
							<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;vertical-align:middle;text-align:right;'></th>
							<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;vertical-align:middle;text-align:right;'>".number_format($row->sumSMDUE2,2)."</th>
							<th style='border:0px;vertical-align:middle;text-align:right;'>".number_format($row->sumPAYNOTINT,2)."</th>
						</tr>
					";	
				}
			}
		}else if($typereport == "summary"){	
			$head = "<tr style='height:30px;'>
					<th style='display:none;'>#</th>
					<th style='vertical-align:top;'>สาขา</th>
					<th style='vertical-align:top;'>เลขที่สัญญา<br>ชื่อ - นามสกุล</th>
					<th style='vertical-align:top;text-align:center;'>วันที่ขาย<br>วันดิวงวดแรก</th>
					<th style='vertical-align:top;text-align:right;'>ราคาขาย<br>ราคาดาวน์</th> 
					<th style='vertical-align:top;text-align:right;'>ชำระแล้ว<br>BillColl</th>
					<th style='vertical-align:top;text-align:right;'>ลูกหนี้คงเหลือจริง<br>วันดิว</th>
					<th style='vertical-align:top;text-align:center;'>งวดทั้งหมด<br>งวดที่ต้องจ่าย</th>
					<th style='vertical-align:top;text-align:right;'>ค้างชำระยกมา</th>
					<th style='vertical-align:top;text-align:right;'>ค่างวดดิวนี้</th>
					<th style='vertical-align:top;text-align:right;'>เบี้ยปรับ</th>
					<th style='vertical-align:top;text-align:right;'>รวมต้องชำระรวมเบี้ยปรับ<br>วันชำระล่าสุด</th>
					</tr>
			";
			
			$NRow = 1;
			if($query->row()){
				foreach($query->result() as $row){$i++;
					$html .= "
						<tr class='trow' seq=".$NRow.">
							<td seq=".$NRow++." style='display:none;'></td>
							<td>".$row->LOCAT."</td>
							<td>".$row->CONTNO."<br>".$row->CUSNAME."</td>
							<td align='center'>".$this->Convertdate(2,$row->SDATE)."<br>".$this->Convertdate(2,$row->FDATE)."</td>
							<td align='right'>".number_format($row->TOTPRC,2)."<br>".number_format($row->TOTDWN,2)."</td>
							<td align='right'>".number_format($row->SMPAY,2)."<br>".$row->BILLCOLL."</td>
							<td align='right'>".number_format($row->TOTAR,2)."<br>".$this->Convertdate(2,$row->DDATES)."</td>
							<td align='center'>".number_format($row->T_NOPAY)."<br>".number_format($row->LNOPAY).'-'.number_format($row->CURNOPAY)."</td>
							<td align='right'>".number_format($row->INIKANG,2)."</td>
							<td align='right'>".number_format($row->SMDUE2,2)."</td>
							<td align='right'>".number_format($row->INTAMT,2)."</td>
							<td align='right'>".number_format($row->PAYSUMINT,2)."<br>".$this->Convertdate(2,$row->LPAYD)."</td>
						</tr>
					";	
				}
			}
			
			if($query2->row()){
				foreach($query2->result() as $row){
					$sumreport = "
						<tr style='height:25px;'>
							<th colspan='3' style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;vertical-align:middle;text-align:center;'>".$row->Total."</th>
							<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;vertical-align:middle;text-align:right;'>".number_format($row->sumTOTPRC)."</th>
							<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;vertical-align:middle;text-align:right;'>".number_format($row->sumSMPAY,2)."</th>
							<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;vertical-align:middle;text-align:right;'>".number_format($row->sumTOTAR,2)."</th>
							<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;vertical-align:middle;text-align:right;'></th>
							<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;vertical-align:middle;text-align:right;'>".number_format($row->sumINIKANG,2)."</th>
							<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;vertical-align:middle;text-align:right;'>".number_format($row->sumSMDUE2,2)."</th>
							<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;vertical-align:middle;text-align:right;'>".number_format($row->sumINTAMT,2)."</th>
							<th style='border:0px;vertical-align:middle;text-align:right;'>".number_format($row->sumPAYSUMINT,2)."</th>
						</tr>
					";	
				}
			}
		}
		
		$head2 = "<tr>
					<th style='vertical-align:middle;'>#</th>
					<th style='vertical-align:top;'>สาขา</th>
					<th style='vertical-align:top;'>เลขที่สัญญา</th>
					<th style='vertical-align:top;'>รหัสลูกค้า</th>
					<th style='vertical-align:top;'>ชื่อ - นามสกุล</th>
					<th style='vertical-align:top;'>ที่อยู่</th>
					<th style='vertical-align:top;'>วันที่ขาย</th>
					<th style='vertical-align:top;text-align:right;'>ราคาขาย</th> 
					<th style='vertical-align:top;text-align:right;'>ลูกหนี้คงเหลือจริง</th>
					<th style='vertical-align:top;text-align:right;'>ค้างชำระยกมา</th>
					<th style='vertical-align:top;text-align:center;'>งวดที่ต้องจ่าย</th>
					<th style='vertical-align:top;text-align:center;'>วันชำระล่าสุด</th>
					<th style='vertical-align:top;text-align:right;'>ยอดชำระล่าสุด</th>
					<th style='vertical-align:top;text-align:center;'>วันที่ดิว</th>
					<th style='vertical-align:top;text-align:center;'>งวดที่งวดนี้</th>
					<th style='vertical-align:top;text-align:right;'>ค่างวดดิวนี้</th>
					<th style='vertical-align:top;text-align:right;'>รวมต้องชำระรวมเบี้ยปรับ</th>
					<th style='vertical-align:top;text-align:center;'>Billcoll</th>
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
						<td style='mso-number-format:\"\@\";'>".$row->CUSCOD."</td>
						<td style='mso-number-format:\"\@\";'>".$row->CUSNAME."</td>
						<td style='mso-number-format:\"\@\";'>".$row->CUSADD."</td>
						<td style='mso-number-format:\"\@\";text-align:center;'>".$this->Convertdate(2,$row->SDATE)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->TOTPRC,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->TOTAR,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->INIKANG,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:center;'>".number_format($row->LNOPAY).'-'.number_format($row->CURNOPAY)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:center;'>".$this->Convertdate(2,$row->LPAYD)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->LPAYA,2)."</td>
						<td style='mso-number-format:\"\@\";text-align:center;'>".$this->Convertdate(2,$row->DDATES)."</td>
						<td style='mso-number-format:\"\@\";text-align:center;'>".number_format($row->CURNOPAY)."</td>
						<td style='mso-number-format:\"\@\";text-align:right;'>".number_format($row->SMDUE2,2)."</td>
						<td style='mso-number-format:\"\@\";text-align:right;'>".number_format($row->PAYSUMINT,2)."</td>
						<td style='mso-number-format:\"\@\";text-align:center;'>".$row->BILLCOLL."</td>
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
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumTOTAR,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumINIKANG,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\"; colspan='2'></th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumLPAYA,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\"; colspan='2'></th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumSMDUE2,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumPAYSUMINT,2)."</th>
						<th style='mso-number-format:\"\@\";'></th>
					</tr>
				";	
			}
		}
		
		if($i>0){
			$html = "
				<div id='table-fixed-ReportARduedatepay' class='col-sm-12' style='height:100%;width:100%;overflow:auto;font-size:8pt;'>
					<table id='table-ReportARduedatepay' style='background-color:white;' class='col-sm-12 display table table-bordered' cellspacing='0' width='calc(100% - 1px)'>
						<thead>
						<tr style='height:40px;'>
							<th colspan=".($typereport == 'detail' ? '10' : '11')." style='font-size:12pt;border:0px;text-align:center;'>รายงานลูกหนี้ครบกำหนดชำระค่างวด</th>
						</tr>
						<tr style='height:25px;'>
							<td colspan=".($typereport == 'detail' ? '10' : '11')."  style='border-bottom:1px solid #ddd;text-align:center;'>วันที่ขาย ".$_REQUEST["FRMDATE"]." - ".$_REQUEST["TODATE"]." ".$rpcond."  ออกรายงาน ณ วันที่ ".$this->today('today')."</td>
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
			<div id='table-fixed-ReportARduedatepay2' class='col-sm-12' style='height:100%;width:100%;overflow:auto;'>
				<table id='table-ReportARduedatepay2' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='100%'>
					<thead>
						<tr>
							<th colspan='18' style='font-size:12pt;border:0px;text-align:center;'>รายงานลูกหนี้ครบกำหนดชำระค่างวด</th>
						</tr>
						<tr>
							<td colspan='18' style='border:0px;text-align:center;'>จากวันที่ดิว ".$_REQUEST["FRMDATE"]." - ".$_REQUEST["TODATE"]." ".$rpcond." ออกรายงาน ณ วันที่ ".$this->today('today')."</td>
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
		$data[] = 	urlencode($_REQUEST["LOCAT1"].'||'.$_REQUEST["CONTNO1"].'||'.$_REQUEST["BILLCOL1"].'||'.$_REQUEST["AMPHUR1"]
					.'||'.$_REQUEST["PROVINCE1"].'||'.$_REQUEST["FRMDATE"].'||'.$_REQUEST["TODATE"].'||'.$_REQUEST["TUMBON1"]
					.'||'.$_REQUEST["report"].'||'.$_REQUEST["orderby"].'||'.$_REQUEST["layout"]);
		echo json_encode($this->generateData($data,"encode"));
	}
	
	function pdf(){
		$data 	= array();
		$data[] = $_GET["condpdf"];
		$arrs 	= $this->generateData($data,"decode");
		$arrs[0]= urldecode($arrs[0]);
		$tx 	= explode("||",$arrs[0]);
		$LOCAT1		= $tx[0];
		$CONTNO1 	= str_replace(chr(0),'',$tx[1]);
		$BILLCOL1 	= str_replace(chr(0),'',$tx[2]);
		$AMPHUR1 	= str_replace(chr(0),'',$tx[3]);
		$PROVINCE1 	= str_replace(chr(0),'',$tx[4]);
		$FRMDATE 	= $this->Convertdate(1,$tx[5]);
		$TODATE 	= $this->Convertdate(1,$tx[6]);
		$TUMBON1 	= $tx[7];
		$typereport = $tx[8];
		$orderby 	= $tx[9];
		$layout 	= $tx[10];
		
		$cond = ""; $rpcond = "";
		
		if($LOCAT1 != ""){
			$cond .= " AND (m.LOCAT LIKE '%".$LOCAT1."%')";
			$rpcond .= "  สาขา ".$LOCAT1;
		}
		
		if($CONTNO1 != ""){
			$cond .= " AND (m.CONTNO LIKE '%".$CONTNO1."%')";
			$rpcond .= "  เลขที่สัญญา ".$CONTNO1;
		}
		
		if($BILLCOL1 != ""){
			$cond .= " AND (m.BILLCOLL LIKE '%".$BILLCOL1."%' )";
			$rpcond .= "  พนักงานเก็บเงิน ".$BILLCOL1;
		}else{
			$cond .= " AND (m.BILLCOLL LIKE '%%' OR m.BILLCOLL IS NULL)";
		}
		
		if($AMPHUR1 != ""){
			$cond .= " AND (a.AUMPCOD LIKE '%".$AMPHUR1."%' )";
		}else{
			$cond .= " AND (a.AUMPCOD LIKE '%%' OR a.AUMPCOD IS NULL)";
		}
		
		if($PROVINCE1 != ""){
			$cond .= " AND (a.PROVCOD LIKE '%".$PROVINCE1."%' )";
		}else{
			$cond .= " AND (a.PROVCOD LIKE '%%' OR a.PROVCOD IS NULL)";
		}
		
		if($TUMBON1 != ""){
			$cond .= " AND a.TUMB LIKE '%".$TUMBON1."%'";
		}
		
		$sql = "
				IF OBJECT_ID('tempdb..#main') IS NOT NULL DROP TABLE #main
				select *
				into #main
				from(
					select LOCAT, CONTNO, CUSCOD, SNAM+NAME1+' '+NAME2 as CUSNAME, isnull(addr1,'')+isnull(' ถ.'+addr2,'')+isnull(' ต.'+tumb,'')+
					isnull(' อ.'+aumpdes,'')+isnull(' จ.'+provdes,'')+' '+isnull(zip,'')+isnull(' โทร. '+telp,'') as CUSADD, SDATE, FDATE, LDATE,
					TOTPRC, TOTDWN, SMPAY, TOTPRC-SMPAY as TOTAR, T_NOPAY, case when (smdue-smbpay) < 0 then 0 else (smdue-smbpay) end as INIKANG,
					LNOPAY, CURNOPAY, LPAYD, LPAYA, DDATE, case when (smdue+smdue2-smbpay) < 0 then 0 else (smdue+smdue2-smbpay) end as PAYNOTINT,
					isnull(INTAMT,0)-isnull(SMPINT,0) as INTAMT, case when (smdue+smdue2-smbpay) < 0 then isnull(INTAMT,0)-isnull(SMPINT,0) 
					else (smdue+smdue2-smbpay)+isnull(INTAMT,0)-isnull(SMPINT,0) end as PAYSUMINT, SMDUE2, BILLCOLL
					from (  
						select 
						m.contno, m.locat, p.nopay, m.cuscod, m.sdate, m.fdate, m.ldate, m.t_nopay, m.smpay, m.smchq, m.totprc, m.totdwn, m.billcoll, 
						m.lpaya, m.lpayd, g.SMPINT, lpay, c.snam, c.name1, c.name2, a.addrno, a.addr1, a.addr2, a.tumb, a.aumpcod, a.provcod, a.zip, a.telp,
						case when smdue is null then 0 else smdue end as smdue, case when curnopay is null then 0 else curnopay end as curnopay,
						case when SmBPay is null then 0 else SmBPay end as SmBPay, (select aumpdes from {$this->MAuth->getdb('SETAUMP')} where aumpcod=a.aumpcod) as aumpdes,
						(select provdes from {$this->MAuth->getdb('SETPROV')} where provcod=a.provcod) as provdes, (select min(nopay) from {$this->MAuth->getdb('ARPAY')} 
						where damt>payment and contno=m.contno and locat=m.locat) as Lnopay, p.ddate, (select case when sum(damt) is null then 0 else sum(damt) end  
						from {$this->MAuth->getdb('ARPAY')} where contno=m.contno and locat=m.locat and (ddate between '".$FRMDATE."' and '".$TODATE."') and ddate<=p.ddate) as smdue2,
						(select case when sum(INTAMT) is null then 0 else sum(INTAMT) end from {$this->MAuth->getdb('ARPAY')} where contno=m.contno and locat=m.locat) as INTAMT
						from  {$this->MAuth->getdb('ARPAY')} p  
						left outer join {$this->MAuth->getdb('ARMAST')} m on p.contno=m.contno and p.locat=m.locat 
						left outer join {$this->MAuth->getdb('CUSTMAST')} c on (c.cuscod = m.cuscod)  
						left outer join {$this->MAuth->getdb('CUSTADDR')} a on (c.cuscod = a.cuscod) and (c.addrno = a.addrno)  
						left outer join (
							select locat,contno,
							sum(case when (DDate < '".$FRMDATE."') then  Damt else 0 end) As SmDue,
							max(case when (DDate >= '".$FRMDATE."') and (DDate <= '".$TODATE."') then  nopay else 0 end) as CurNopay  
							from {$this->MAuth->getdb('ARPAY')}  
							group by locat,contno
						) e on m.contno=e.contno and m.locat=e.locat  
						left outer join (
							select contno,locatpay,
							sum(case when (Tmbildt < '".$FRMDATE."') and (payfor = '006' or payfor = '007') then Payamt else 0 end) As SmBPay,
							max(L_pay) As Lpay 
							from {$this->MAuth->getdb('CHQTRAN')}  
							where  ((paytyp<>'02') or (paytyp='02' and flag='P'))  and flag <> 'C' 
							group by contno,locatpay
						) f on m.contno=f.contno and m.locat=f.locatpay 
						left outer join (
							select contno, locatpay, SUM(PAYINT) AS SMPINT 
							from {$this->MAuth->getdb('CHQTRAN')} 
							where (Tmbildt < '".$FRMDATE."')and ((paytyp<>'02') or (paytyp='02' and flag='P')) 
							and flag <> 'C' and (payfor = '006' or payfor = '007') 
							group By locatpay,Contno
						) g on m.contno=g.contno and m.locat=g.locatpay
						where ((not(m.ystat = 'O') and not(m.ystat = 'C' )) or (m.ystat is null)) and ((m.smpay < m.totprc) or ((m.smpay = m.totprc) 
						and (m.lpayd >= '".$FRMDATE."'))) and (p.ddate between '".$FRMDATE."' and '".$TODATE."')
						".$cond."
					) AS D 
				)main
		";//echo $sql; 
		$query = $this->db->query($sql);
		
		$sql = "
				select LOCAT, CONTNO, CUSCOD, CUSNAME, CUSADD, convert(nvarchar,SDATE,112) as SDATE, convert(nvarchar,FDATE,112) as FDATE, LDATE, TOTPRC, TOTDWN, SMPAY, 
				TOTAR, T_NOPAY, INIKANG, LNOPAY, CURNOPAY, convert(nvarchar,LPAYD,112) as LPAYD, LPAYA, DDATE, convert(nvarchar,DDATE,112) as DDATES, PAYNOTINT, 
				INTAMT, PAYSUMINT, SMDUE2, BILLCOLL
				from #main
				order by ".$orderby."
		";//echo $sql; exit;
		$query = $this->db->query($sql); 
		
		$sql = "
				select 'รวมทั้งหมด' as Total, sum(TOTPRC) as sumTOTPRC, sum(SMPAY) as sumSMPAY, sum(TOTAR) as sumTOTAR, sum(INIKANG) as sumINIKANG, 
				sum(LPAYA) as sumLPAYA, sum(SMDUE2) as sumSMDUE2, sum(PAYNOTINT) as sumPAYNOTINT, sum(INTAMT) as sumINTAMT, sum(PAYSUMINT) as sumPAYSUMINT
				from #main
		";//echo $sql; exit;
		$query2 = $this->db->query($sql);
		
		$head = ""; $html = ""; $i=0; 
		
		if($typereport == "detail"){
			$head = "
					<tr>
						<th style='border-bottom:0.1px solid black;text-align:left;'>#</th>
						<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>สาขา</th>
						<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>เลขที่สัญญา</th>
						<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>รหัสลูกค้า/ชื่อ - นามสกุล<br>ที่อยู่</th>
						<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>วันที่ขาย<br>Billcoll</th>
						<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>ราคาขาย</th> 
						<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>ลูกหนี้คงเหลือจริง </th>
						<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>ค้างชำระยกมา<br>วันดิว</th>
						<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>งวดที่ต้องจ่าย<br>งวดที่งวดนี้</th>
						<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>วันชำระล่าสุด<br>ค่างวดดิวนี้</th>
						<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>ยอดชำระล่าสุด<br>รวมต้องชำระไม่รวมเบี้ยปรับ</th>
					</tr>
			";
			
			$No = 1;
			if($query->row()){
				foreach($query->result() as $row){	
					$html .= "
						<tr class='trow' seq=".$No.">
							<td style='width:35px;'>".$No++."</td>
							<td style='width:40px;'>".$row->LOCAT."</td>
							<td style='width:75px;'>".$row->CONTNO."</td>
							<td style='width:280px;'>".$row->CUSCOD.'   '.$row->CUSNAME."<br>".$row->CUSADD."</td>
							<td style='width:75px;'>".$this->Convertdate(2,$row->SDATE)."<br>".$row->BILLCOLL."</td>
							<td style='width:75px;' align='right'>".number_format($row->TOTPRC,2)."</td>
							<td style='width:75px;' align='right'>".number_format($row->TOTAR,2)."</td>
							<td style='width:75px;' align='right'>".number_format($row->INIKANG,2)."<br>".number_format($row->DDATES)."</td>
							<td style='width:50px;' align='center'>".number_format($row->LNOPAY).'-'.number_format($row->CURNOPAY)."<br>".number_format($row->CURNOPAY)."</td>
							<td style='width:75px;' align='right'>".$this->Convertdate(2,$row->LPAYD)."<br>".number_format($row->SMDUE2,2)."</td>
							<td style='width:90px;' align='right'>".number_format($row->LPAYA,2)."<br>".number_format($row->PAYNOTINT,2)."</td>
							
						</tr>
					";	
				}
			}
			
			if($query2->row()){
				foreach($query2->result() as $row){	
					$html .= "
						<tr class='trow bor' style='background-color:#ebebeb;'>
							<td colspan='5' style='text-align:center;vertical-align:middle;'>".$row->Total."</td>
							<td align='right'>".number_format($row->sumTOTPRC)."</td>
							<td align='right'>".number_format($row->sumTOTAR,2)."</td>
							<td align='right'>".number_format($row->sumINIKANG,2)."</td>
							<td align='right'></td>
							<td align='right'>".number_format($row->sumSMDUE2,2)."</td>
							<td align='right'>".number_format($row->sumPAYNOTINT,2)."</td>
						</tr>
					";	
				}
			}
		}else if($typereport == "summary"){
			$head = "
					<tr>
						<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>#</th>
						<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>สาขา</th>
						<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>เลขที่สัญญา<br>ชื่อ - นามสกุล</th>
						<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:center;'>วันที่ขาย<br>วันดิวงวดแรก</th>
						<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>ราคาขาย<br>ราคาดาวน์</th> 
						<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>ชำระแล้ว<br>BillColl</th>
						<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>ลูกหนี้คงเหลือจริง<br>วันดิว</th>
						<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:center;'>งวดทั้งหมด<br>งวดที่ต้องจ่าย</th>
						<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>ค้างชำระยกมา</th>
						<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>ค่างวดดิวนี้</th>
						<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>เบี้ยปรับ</th>
						<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>รวมต้องชำระรวมเบี้ยปรับ<br>วันชำระล่าสุด</th>
					</tr>
			";
			
			$No = 1;
			if($query->row()){
				foreach($query->result() as $row){	
					$html .= "
						<tr class='trow' seq=".$No.">
							<td style='width:35px;'>".$No++."</td>
							<td style='width:40px;'>".$row->LOCAT."</td>
							<td style='width:170px;'>".$row->CONTNO."<br>".$row->CUSNAME."</td>
							<td style='width:80px;' align='center'>".$this->Convertdate(2,$row->SDATE)."<br>".$this->Convertdate(2,$row->FDATE)."</td>
							<td style='width:85px;' align='right'>".number_format($row->TOTPRC,2)."<br>".number_format($row->TOTDWN,2)."</td>
							<td style='width:85px;' align='right'>".number_format($row->SMPAY,2)."<br>".$row->BILLCOLL."</td>
							<td style='width:90px;' align='right'>".number_format($row->TOTAR,2)."<br>".$this->Convertdate(2,$row->DDATES)."</td>
							<td style='width:85px;' align='center'>".number_format($row->T_NOPAY)."<br>".number_format($row->LNOPAY).'-'.number_format($row->CURNOPAY)."</td>
							<td style='width:85px;' align='right'>".number_format($row->INIKANG,2)."</td>
							<td style='width:85px;' align='right'>".number_format($row->SMDUE2,2)."</td>
							<td style='width:75px;' align='right'>".number_format($row->INTAMT,2)."</td>
							<td style='width:120px;' align='right'>".number_format($row->PAYSUMINT,2)."<br>".$this->Convertdate(2,$row->LPAYD)."</td>
						</tr>
					";	
				}
			}
			
			if($query2->row()){
				foreach($query2->result() as $row){	
					$html .= "
						<tr class='trow bor' style='background-color:#ebebeb;'>
							<td colspan='4' style='text-align:center;vertical-align:middle;'>".$row->Total."</td>
							<td align='right'>".number_format($row->sumTOTPRC)."</td>
							<td align='right'>".number_format($row->sumSMPAY,2)."</td>
							<td align='right'>".number_format($row->sumTOTAR,2)."</td>
							<td align='right'></td>
							<td align='right'>".number_format($row->sumINIKANG,2)."</td>
							<td align='right'>".number_format($row->sumSMDUE2,2)."</td>
							<td align='right'>".number_format($row->sumINTAMT,2)."</td>
							<td align='right'>".number_format($row->sumINTAMT,2)."</td>
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
						<th colspan='".($typereport == 'detail' ? '11' : '12')."' style='font-size:10pt;'>รายงานลูกหนี้ครบกำหนดชำระค่างวด</th>
					</tr>
					<tr>
						<td colspan='".($typereport == 'detail' ? '11' : '12')."' style='font-size:8pt;height:35px;border-bottom:0.1px solid black;text-align:center;'>".$rpcond." จากวันที่ดิว ".$tx[5]." - ".$tx[6]."</td>
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