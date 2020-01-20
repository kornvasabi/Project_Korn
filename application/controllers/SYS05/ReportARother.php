<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//BEE+
class ReportARother extends MY_Controller {
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
							<br>รายงานลูกหนี้คงเหลือจากลูกหนี้อื่น<br>
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
									จากเลขที่สัญญา
									<select id='CONTNO1' class='form-control input-sm' data-placeholder='เลขที่สัญญา'></select>
								</div>
							</div>
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group'>
									ตั้งแต่วันที่
									<input type='text' id='FRMDATE' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' placeholder='ตั้งแต่วันที่' value='".$this->today('today')."' style='font-size:10.5pt'>
								</div>
							</div>
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group'>
									ถึงวันที่
									<input type='text' id='TODATE' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' placeholder='ถึงวันที่' value='".$this->today('today')."' style='font-size:10.5pt'>
								</div>
							</div>
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group' >
									รหัสลูกค้า
									<select id='CUSCOD1' class='form-control input-sm' data-placeholder='รหัสลูกค้า'></select>
								</div>
							</div>	
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group' >
									ประเภทการขาย
									<select id='TSALE1' class='form-control input-sm' data-placeholder='ประเภทการขาย'></select>
								</div>
							</div>	
						</div>
						<div class='col-sm-8 col-xs-8 col-sm-offset-2'><br>	
							<div class='col-sm-6 col-xs-6'>	
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
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group'>
									เรียงลำดับข้อมูล
									<div class='col-sm-12 col-xs-12' style='border:0.1px dotted #d6d6d6;'>	
										<div class='col-sm-12 col-xs-12'>
											<div class='form-group'>
												<br>
												<input type= 'radio' id='sdate' name='orderby' checked> ตามวันที่ขาย
												<br><br>
												<input type= 'radio' id='contno' name='orderby'> เลขที่สัญญา
												<br><br>
												<input type= 'radio' id='cuscod' name='orderby'> ตามรหัสลูกค้า
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
		
		$html.= "<script src='".base_url('public/js/SYS05/ReportARother.js')."'></script>";
		echo $html;
	}
	
	function search(){
		$LOCAT1		= $_REQUEST["LOCAT1"];
		$CONTNO1 	= $_REQUEST["CONTNO1"];
		$CUSCOD1 	= str_replace(chr(0),'',$_REQUEST["CUSCOD1"]);
		$TSALE1 	= str_replace(chr(0),'',$_REQUEST["TSALE1"]);
		$FRMDATE 	= $this->Convertdate(1,$_REQUEST["FRMDATE"]);
		$TODATE 	= $this->Convertdate(1,$_REQUEST["TODATE"]);
		$orderby 	= $_REQUEST["orderby"];
		
		$cond = ""; $rpcond = "";
		
		if($LOCAT1 != ""){
			$cond .= " AND (A.LOCAT LIKE '%".$LOCAT1."%')";
			$rpcond .= "  สาขา ".$LOCAT1;
		}
		
		if($CONTNO1 != ""){
			$cond .= " AND (A.ARCONT LIKE '%".$CONTNO1."%' )";
			$rpcond .= "  เลขที่สัญญา ".$CONTNO1;
		}
		
		if($CUSCOD1 != ""){
			$cond .= " AND (A.CUSCOD LIKE '%".$CUSCOD1."%' )";
			$rpcond .= "  รหัสลูกค้า ".$CUSCOD1;
		}
		
		if($TSALE1 != ""){
			$cond .= " AND (A.TSALE LIKE '%".$TSALE1."%' )";
		}
		
		$sql = "
				IF OBJECT_ID('tempdb..#main') IS NOT NULL DROP TABLE #main
				select *
				into #main
				from(
					select A.LOCAT, A.CONTNO, A.ARCONT, A.CUSCOD, B.SNAM+B.NAME1+' '+B.NAME2 as CUSNAME, A.TSALE, A.ARDATE, A.PAYFOR, C.FORDESC,
					A.VATRT, case when A.VATRT != 0 then A.PAYAMT-((A.PAYAMT*7)/107) else A.PAYAMT end as NPAYAMT,
					case when A.VATRT != 0 then (A.PAYAMT*7)/107 else 0 end as VPAYAMT,  A.PAYAMT,
					sum(case when  D.PAYDT <= '".$TODATE."'  AND FLAG<>'C' then D.PAYAMT else 0 end) as PAY,a.userid 
					from {$this->MAuth->getdb('AROTHR')} A
					left join {$this->MAuth->getdb('CUSTMAST')} B on A.CUSCOD = B.CUSCOD
					left join {$this->MAuth->getdb('PAYFOR')} C on A.PAYFOR = C.FORCODE
					left join {$this->MAuth->getdb('CHQTRAN')} D on A.ARCONT = D.CONTNO
					where A.ARDATE BETWEEN '".$FRMDATE."' AND '".$TODATE."' AND A.PAYAMT > 0  AND FLAG != 'C'  and A.CUSCOD != ''
					".$cond."
					group by A.LOCAT,A.CONTNO,A.ARCONT,A.PAYFOR,C.FORDESC, A.PAYAMT ,A.CUSCOD,A.ARDATE ,B.SNAM, B.NAME1,B.NAME2,A.TSALE,A.VATRT,a.userid 
					having A.PAYAMT > sum(case when  D.PAYDT <= '".$TODATE."' then D.PAYAMT else 0 end) 
					union 
					select A.LOCAT, A.CONTNO, A.ARCONT, A.CUSCOD, B.SNAM+B.NAME1+' '+B.NAME2 as CUSNAME, A.TSALE, A.ARDATE, A.PAYFOR, C.FORDESC,
					A.VATRT, case when A.VATRT != 0 then A.PAYAMT-((A.PAYAMT*7)/107) else A.PAYAMT end as NPAYAMT,
					case when A.VATRT != 0 then (A.PAYAMT*7)/107 else 0 end as VPAYAMT, A.PAYAMT,
					0 as PAY,a.userid 
					from {$this->MAuth->getdb('AROTHR')} A
					left join {$this->MAuth->getdb('CUSTMAST')} B on A.CUSCOD = B.CUSCOD
					left join {$this->MAuth->getdb('PAYFOR')} C on A.PAYFOR = C.FORCODE
					where A.PAYAMT > 0 AND A.ARCONT NOT IN (SELECT CONTNO FROM {$this->MAuth->getdb('CHQTRAN')} WHERE PAYDT <= '".$TODATE."' AND FLAG != 'C') 
					AND A.ARDATE BETWEEN '".$FRMDATE."' AND '".$TODATE."' and A.CUSCOD != ''
					".$cond."
					group by A.LOCAT,A.CONTNO,A.ARCONT,A.PAYFOR,C.FORDESC, A.PAYAMT ,A.CUSCOD,A.ARDATE ,B.SNAM,B.NAME1,B.NAME2,A.TSALE,A.VATRT,a.userid  
					having A.PAYAMT >  0  
				)main
		";//echo $sql; 
		$query = $this->db->query($sql);
		
		$sql = "
				IF OBJECT_ID('tempdb..#main2') IS NOT NULL DROP TABLE #main2
				select *
				into #main2
				from(
					select CONTNO, LOCATPAY, PAYFOR,
					sum(CASE WHEN (PAYDT BETWEEN '".$TODATE."'  AND '".$TODATE."') THEN  PAYAMT ELSE 0 END) AS SNETP ,
					sum(CASE WHEN ((PAYDT > '".$TODATE."' OR PAYDT IS NULL)) THEN  PAYAMT ELSE 0 END) AS SNETP1 , 
					sum(CASE WHEN (PAYDT BETWEEN '".$TODATE."'  AND '".$TODATE."') THEN  PAYAMT_V ELSE 0 END) AS VPY ,
					sum(CASE WHEN ((PAYDT > '".$TODATE."' OR PAYDT IS NULL)) THEN  PAYAMT_V ELSE 0 END) AS VCQ 
					from {$this->MAuth->getdb('CHQTRAN')} A  
					where FLAG != 'C' and CONTNO in (select CONTNO from #main)
					group by CONTNO, LOCATPAY, PAYFOR
				)main2
		";//echo $sql; 
		$query = $this->db->query($sql);
		
		$sql = "
				select LOCAT, a.CONTNO, ARCONT, CUSCOD, CUSNAME, TSALE, ARDATE, convert(nvarchar,ARDATE,112) as ARDATES, a.PAYFOR, FORDESC, userid as USERID, VATRT, 
				NPAYAMT, VPAYAMT, PAYAMT, isnull(SNETP,0)-isnull(VPY,0) as PAY, isnull(VPY,0) as VPY, isnull(SNETP,0) as TOTPAY, 
				NPAYAMT-(isnull(SNETP,0)-isnull(VPY,0)) as AR, VPAYAMT-isnull(VPY,0) as VTAR, PAYAMT-isnull(SNETP,0) as TOTAR, 
				isnull(SNETP1,0)-isnull(VCQ,0) as CHQ, isnull(VCQ,0) as VCHQ, isnull(SNETP1,0) as TOTCHQ,
				NPAYAMT-(isnull(SNETP,0)-isnull(VPY,0))-isnull(SNETP1,0)-isnull(VCQ,0) as BALANCE, 
				VPAYAMT-isnull(VPY,0)-isnull(VCQ,0) as VBALANCE, PAYAMT-isnull(SNETP,0)-isnull(SNETP1,0) as TOTBANCE
				from #main a
				left join #main2 b on a.CONTNO = b.CONTNO and a.LOCAT = b.LOCATPAY and a.PAYFOR = b.PAYFOR
				order by ".$orderby."
		";//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$sql = "
				select 'รวมทั้งหมด' as Total, sum(NPAYAMT) as sumNPAYAMT, sum(VPAYAMT) as sumVPAYAMT, sum(PAYAMT) as sumPAYAMT, 
				sum(isnull(SNETP,0)-isnull(VPY,0)) as sumPAY, sum(isnull(VPY,0)) as sumVPY, sum(isnull(SNETP,0)) as sumTOTPAY, 
				sum(NPAYAMT-(isnull(SNETP,0)-isnull(VPY,0))) as sumAR, sum(VPAYAMT-isnull(VPY,0)) as sumVTAR, 
				sum(PAYAMT-isnull(SNETP,0)) as sumTOTAR, sum(isnull(SNETP1,0)-isnull(VCQ,0)) as sumCHQ, sum(isnull(VCQ,0)) as sumVCHQ, 
				sum(isnull(SNETP1,0)) as sumTOTCHQ, sum(NPAYAMT-(isnull(SNETP,0)-isnull(VPY,0))-isnull(SNETP1,0)-isnull(VCQ,0)) as sumBALANCE, 
				sum(VPAYAMT-isnull(VPY,0)-isnull(VCQ,0)) as sumVBALANCE, sum(PAYAMT-isnull(SNETP,0)-isnull(SNETP1,0)) as sumTOTBANCE
				from #main a
				left join #main2 b on a.CONTNO = b.CONTNO and a.LOCAT = b.LOCATPAY and a.PAYFOR = b.PAYFOR
		";
		//echo $sql; exit;
		$query2 = $this->db->query($sql);
		
		
		$head = ""; $html = ""; $head2 = "";  $report = ""; $sumreport = ""; $sumreport2 = ""; $i = 0; 
		
		$head = "<tr style='height:30px;'>
				<th style='display:none;'>#</th>
				<th style='vertical-align:top;'>สาขา</th>
				<th style='vertical-align:top;'>เลขที่สัญญา<br>เลขรันลูกหนี้</th>
				<th style='vertical-align:top;'>รหัสลูกค้า<br>ชื่อ - นามสกุล</th>
				<th style='vertical-align:top;'>วันค้างชำระ<br>การขาย</th> 
				<th style='vertical-align:top;'>ค้างชำระ</th> 
				<th style='vertical-align:top;text-align:right;'>มูลค่ายอดลูกหนี้<br>ภาษียอดลูกหนี้</th> 
				<th style='vertical-align:top;text-align:right;'>ยอดตั้งลูกหนี้<br>มูลค่าเช็ค</th>
				<th style='vertical-align:top;text-align:right;'>มูลค่าชำระ<br>ภาษีเช็ค</th>
				<th style='vertical-align:top;text-align:right;'>ภาษีชำระ<br>เช็ครอเรียกเก็บ</th>
				<th style='vertical-align:top;text-align:right;'>ชำระแล้ว<br>มูลค่าหักเช็ค</th>
				<th style='vertical-align:top;text-align:right;'>มูลค่าล/นคงเหลือ<br>ภาษีหักเช็ค</th>
				<th style='vertical-align:top;text-align:right;'>ภาษีคงเหลือ<br>คงเหลือหักเช็ค</th>
				<th style='vertical-align:top;text-align:right;'>ลูกหนี้คงเหลือ<br>ผู้บันทึกรายการ</th>
				</tr>
		";
		
		$head2 = "<tr>
					<th style='vertical-align:middle;'>#</th>
					<th style='vertical-align:middle;'>สาขา</th>
					<th style='vertical-align:middle;'>เลขที่สัญญา</th>
					<th style='vertical-align:middle;'>เลขรันลูกหนี้</th>
					<th style='vertical-align:middle;'>รหัสลูกค้า</th>
					<th style='vertical-align:middle;'>ชื่อ - นามสกุล</th>
					<th style='vertical-align:middle;'>การขาย</th>
					<th style='vertical-align:middle;'>วันค้างชำระ</th>
					<th style='vertical-align:middle;'>ค้างชำระค่า</th>
					<th style='vertical-align:middle;'>ผู้บันทึกรายการ</th>
					<th style='vertical-align:middle;'>มูลค่ายอดลูกหนี้</th>
					<th style='vertical-align:middle;'>ภาษียอดลูกหนี้</th>
					<th style='vertical-align:middle;'>ยอดตั้งลูกหนี้</th>
					<th style='vertical-align:middle;'>มูลค่าชำระ</th>
					<th style='vertical-align:middle;'>ภาษีชำระ</th>
					<th style='vertical-align:middle;'>ชำระแล้ว</th>
					<th style='vertical-align:middle;'>มูลค่าล/นคงเหลือ</th>
					<th style='vertical-align:middle;'>ภาษีคงเหลือ</th>
					<th style='vertical-align:middle;'>ลูกหนี้คงเหลือ</th>
					<th style='vertical-align:middle;'>มูลค่าเช็ค</th>
					<th style='vertical-align:middle;'>ภาษีเช็ค</th>
					<th style='vertical-align:middle;'>เช็ครอเรียกเก็บ</th>
					<th style='vertical-align:middle;'>มูลค่าหักเช็ค</th>
					<th style='vertical-align:middle;'>ภาษีหักเช็ค</th>
					<th style='vertical-align:middle;'>คงเหลือหักเช็ค</th>
				</tr>
		";
		
		$NRow = 1;
		if($query->row()){
			foreach($query->result() as $row){$i++;
				$html .= "
					<tr class='trow' seq=".$NRow.">
						<td seq=".$NRow++." style='display:none;'></td>
						<td>".$row->LOCAT."</td>
						<td>".$row->CONTNO."<br>".$row->ARCONT."</td>
						<td>".$row->CUSCOD."<br>".$row->CUSNAME."</td>
						<td>".$this->Convertdate(2,$row->ARDATES)."<br>".$row->TSALE."</td>
						<td>".$row->PAYFOR."<br>".$row->FORDESC."</td>
						<td align='right'>".number_format($row->NPAYAMT,2)."<br>".number_format($row->VPAYAMT,2)."</td>
						<td align='right'>".number_format($row->PAYAMT,2)."<br>".number_format($row->CHQ,2)."</td>
						<td align='right'>".number_format($row->PAY,2)."<br>".number_format($row->VCHQ,2)."</td>
						<td align='right'>".number_format($row->VPY,2)."<br>".number_format($row->TOTCHQ,2)."</td>
						<td align='right'>".number_format($row->TOTPAY,2)."<br>".number_format($row->BALANCE,2)."</td>
						<td align='right'>".number_format($row->AR,2)."<br>".number_format($row->VBALANCE,2)."</td>
						<td align='right'>".number_format($row->VTAR,2)."<br>".number_format($row->TOTBANCE,2)."</td>
						<td align='right'>".number_format($row->TOTAR,2)."<br>".$row->USERID."</td>
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
						<td style='mso-number-format:\"\@\";'>".$row->ARCONT."</td>
						<td style='mso-number-format:\"\@\";'>".$row->CUSCOD."</td>
						<td style='mso-number-format:\"\@\";'>".$row->CUSNAME."</td>
						<td style='mso-number-format:\"\@\";'>".$row->TSALE."</td>
						<td style='mso-number-format:\"\@\";'>".$this->Convertdate(2,$row->ARDATES)."</td>
						<td style='mso-number-format:\"\@\";'>".$row->PAYFOR.' '.$row->FORDESC."</td>
						<td style='mso-number-format:\"\@\";'>".$row->USERID."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->NPAYAMT,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->VPAYAMT,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->PAYAMT,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->PAY,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->VPY,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->TOTPAY,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->AR,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->VTAR,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->TOTAR,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->CHQ,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->VCHQ,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->TOTCHQ,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->BALANCE,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->VBALANCE,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->TOTBANCE,2)."</td>
					</tr>
				";	
			}
		}
		
		if($query2->row()){
			foreach($query2->result() as $row){
				$sumreport = "
					<tr>
						<th colspan='5' style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;text-align:center;'>".$row->Total."</th>
						<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;text-align:right;'>".number_format($row->sumNPAYAMT,2)."<br>".number_format($row->sumVPAYAMT,2)."</th>
						<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;text-align:right;'>".number_format($row->sumPAYAMT,2)."<br>".number_format($row->sumCHQ,2)."</th>
						<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;text-align:right;'>".number_format($row->sumPAY,2)."<br>".number_format($row->sumVCHQ,2)."</th>
						<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;text-align:right;'>".number_format($row->sumVPY,2)."<br>".number_format($row->sumTOTCHQ,2)."</th>
						<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;text-align:right;'>".number_format($row->sumTOTPAY,2)."<br>".number_format($row->sumBALANCE,2)."</th>
						<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;text-align:right;'>".number_format($row->sumAR,2)."<br>".number_format($row->sumVBALANCE,2)."</th>
						<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;text-align:right;'>".number_format($row->sumVTAR,2)."<br>".number_format($row->sumTOTBANCE,2)."</th>
						<th style='border:0px;text-align:right;'>".number_format($row->sumTOTAR,2)."<br></th>
					</tr>
				";	
			}
		}
		
		if($query2->row()){
			foreach($query2->result() as $row){
				$sumreport2 = "
					<tr class='trow'>
						<th style='mso-number-format:\"\@\";text-align:center;' colspan='10'>".$row->Total."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumNPAYAMT,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumVPAYAMT,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumPAYAMT,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumPAY,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumVPY,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumTOTPAY,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumAR,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumVTAR,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumTOTAR,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumCHQ,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumVCHQ,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumTOTCHQ,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumBALANCE,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumVBALANCE,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumTOTBANCE,2)."</th>
					</tr>
				";	
			}
		}
		
		if($i>0){
			$html = "
				<div id='table-fixed-ReportARother' class='col-sm-12' style='height:100%;width:100%;overflow:auto;font-size:8pt;'>
					<table id='table-ReportARother' style='background-color:white;' class='col-sm-12 display table table-bordered' cellspacing='0' width='calc(100% - 1px)'>
						<thead>
						<tr style='height:40px;'>
							<th colspan='13' style='font-size:12pt;border:0px;text-align:center;'>รายงานลูกหนี้คงเหลือจากลูกหนี้อื่น</th>
						</tr>
						<tr style='height:25px;'>
							<td colspan='13' style='border-bottom:1px solid #ddd;text-align:center;'>".$rpcond."  ออกรายงาน ณ วันที่ ".$this->today('today')."</td>
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
			<div id='table-fixed-ReportARother2' class='col-sm-12' style='height:100%;width:100%;overflow:auto;'>
				<table id='table-ReportARother2' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='100%'>
					<thead>
						<tr>
							<th colspan='25' style='font-size:12pt;border:0px;text-align:center;'>รายงานลูกหนี้คงเหลือจากลูกหนี้อื่น</th>
						</tr>
						<tr>
							<td colspan='25' style='border:0px;text-align:center;'>".$rpcond." ออกรายงาน ณ วันที่ ".$this->today('today')."</td>
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
		$data[] = 	urlencode($_REQUEST["LOCAT1"].'||'.$_REQUEST["CONTNO1"].'||'.$_REQUEST["CUSCOD1"].'||'.$_REQUEST["TSALE1"].'||'.$_REQUEST["FRMDATE"]
					.'||'.$_REQUEST["TODATE"].'||'.$_REQUEST["orderby"].'||'.$_REQUEST["layout"]);
		echo json_encode($this->generateData($data,"encode"));
	}
	
	function pdf(){
		$data 	= array();
		$data[] = $_GET["condpdf"];
		$arrs 	= $this->generateData($data,"decode");
		$arrs[0]= urldecode($arrs[0]);
		$tx 	= explode("||",$arrs[0]);
		$LOCAT1 	= $tx[0];
		$CONTNO1 	= $tx[1];
		$CUSCOD1 	= str_replace(chr(0),'',$tx[2]);
		$TSALE1 	= str_replace(chr(0),'',$tx[3]);
		$FRMDATE 	= $this->Convertdate(1,$tx[4]);
		$TODATE 	= $this->Convertdate(1,$tx[5]);
		$orderby 	= $tx[6];
		$layout 	= $tx[7];
		
		$cond = ""; $rpcond = "";

		if($LOCAT1 != ""){
			$cond .= " AND (A.LOCAT LIKE '%".$LOCAT1."%')";
			$rpcond .= "  สาขา ".$LOCAT1;
		}
		
		if($CONTNO1 != ""){
			$cond .= " AND (A.ARCONT LIKE '%".$CONTNO1."%' )";
			$rpcond .= "  เลขที่สัญญา ".$CONTNO1;
		}
		
		if($CUSCOD1 != ""){
			$cond .= " AND (A.CUSCOD LIKE '%".$CUSCOD1."%' )";
			$rpcond .= "  รหัสลูกค้า ".$CUSCOD1;
		}
		
		if($TSALE1 != ""){
			$cond .= " AND (A.TSALE LIKE '%".$TSALE1."%' )";
		}
		
		$sql = "
				IF OBJECT_ID('tempdb..#main') IS NOT NULL DROP TABLE #main
				select *
				into #main
				from(
					select A.LOCAT, A.CONTNO, A.ARCONT, A.CUSCOD, B.SNAM+B.NAME1+' '+B.NAME2 as CUSNAME, A.TSALE, A.ARDATE, A.PAYFOR, C.FORDESC,
					A.VATRT, case when A.VATRT != 0 then A.PAYAMT-((A.PAYAMT*7)/107) else A.PAYAMT end as NPAYAMT,
					case when A.VATRT != 0 then (A.PAYAMT*7)/107 else 0 end as VPAYAMT,  A.PAYAMT,
					sum(case when  D.PAYDT <= '".$TODATE."'  AND FLAG<>'C' then D.PAYAMT else 0 end) as PAY,a.userid 
					from {$this->MAuth->getdb('AROTHR')} A
					left join {$this->MAuth->getdb('CUSTMAST')} B on A.CUSCOD = B.CUSCOD
					left join {$this->MAuth->getdb('PAYFOR')} C on A.PAYFOR = C.FORCODE
					left join {$this->MAuth->getdb('CHQTRAN')} D on A.ARCONT = D.CONTNO
					where A.ARDATE BETWEEN '".$FRMDATE."' AND '".$TODATE."' AND A.PAYAMT > 0  AND FLAG != 'C'  and A.CUSCOD != ''
					".$cond."
					group by A.LOCAT,A.CONTNO,A.ARCONT,A.PAYFOR,C.FORDESC, A.PAYAMT ,A.CUSCOD,A.ARDATE ,B.SNAM, B.NAME1,B.NAME2,A.TSALE,A.VATRT,a.userid 
					having A.PAYAMT > sum(case when  D.PAYDT <= '".$TODATE."' then D.PAYAMT else 0 end) 
					union 
					select A.LOCAT, A.CONTNO, A.ARCONT, A.CUSCOD, B.SNAM+B.NAME1+' '+B.NAME2 as CUSNAME, A.TSALE, A.ARDATE, A.PAYFOR, C.FORDESC,
					A.VATRT, case when A.VATRT != 0 then A.PAYAMT-((A.PAYAMT*7)/107) else A.PAYAMT end as NPAYAMT,
					case when A.VATRT != 0 then (A.PAYAMT*7)/107 else 0 end as VPAYAMT, A.PAYAMT,
					0 as PAY,a.userid 
					from {$this->MAuth->getdb('AROTHR')} A
					left join {$this->MAuth->getdb('CUSTMAST')} B on A.CUSCOD = B.CUSCOD
					left join {$this->MAuth->getdb('PAYFOR')} C on A.PAYFOR = C.FORCODE
					where A.PAYAMT > 0 AND A.ARCONT NOT IN (SELECT CONTNO FROM {$this->MAuth->getdb('CHQTRAN')} WHERE PAYDT <= '".$TODATE."' AND FLAG != 'C') 
					AND A.ARDATE BETWEEN '".$FRMDATE."' AND '".$TODATE."' and A.CUSCOD != ''
					".$cond."
					group by A.LOCAT,A.CONTNO,A.ARCONT,A.PAYFOR,C.FORDESC, A.PAYAMT ,A.CUSCOD,A.ARDATE ,B.SNAM,B.NAME1,B.NAME2,A.TSALE,A.VATRT,a.userid  
					having A.PAYAMT >  0  
				)main
		";//echo $sql; 
		$query = $this->db->query($sql);
		
		$sql = "
				IF OBJECT_ID('tempdb..#main2') IS NOT NULL DROP TABLE #main2
				select *
				into #main2
				from(
					select CONTNO, LOCATPAY, PAYFOR,
					sum(CASE WHEN (PAYDT BETWEEN '".$TODATE."'  AND '".$TODATE."') THEN  PAYAMT ELSE 0 END) AS SNETP ,
					sum(CASE WHEN ((PAYDT > '".$TODATE."' OR PAYDT IS NULL)) THEN  PAYAMT ELSE 0 END) AS SNETP1 , 
					sum(CASE WHEN (PAYDT BETWEEN '".$TODATE."'  AND '".$TODATE."') THEN  PAYAMT_V ELSE 0 END) AS VPY ,
					sum(CASE WHEN ((PAYDT > '".$TODATE."' OR PAYDT IS NULL)) THEN  PAYAMT_V ELSE 0 END) AS VCQ 
					from {$this->MAuth->getdb('CHQTRAN')} A  
					where FLAG != 'C' and CONTNO in (select CONTNO from #main)
					group by CONTNO, LOCATPAY, PAYFOR
				)main2
		";//echo $sql; 
		$query = $this->db->query($sql);
		
		$sql = "
				select LOCAT, a.CONTNO, ARCONT, CUSCOD, CUSNAME, TSALE, ARDATE, convert(nvarchar,ARDATE,112) as ARDATES, a.PAYFOR, FORDESC, userid as USERID, VATRT, 
				NPAYAMT, VPAYAMT, PAYAMT, isnull(SNETP,0)-isnull(VPY,0) as PAY, isnull(VPY,0) as VPY, isnull(SNETP,0) as TOTPAY, 
				NPAYAMT-(isnull(SNETP,0)-isnull(VPY,0)) as AR, VPAYAMT-isnull(VPY,0) as VTAR, PAYAMT-isnull(SNETP,0) as TOTAR, 
				isnull(SNETP1,0)-isnull(VCQ,0) as CHQ, isnull(VCQ,0) as VCHQ, isnull(SNETP1,0) as TOTCHQ,
				NPAYAMT-(isnull(SNETP,0)-isnull(VPY,0))-isnull(SNETP1,0)-isnull(VCQ,0) as BALANCE, 
				VPAYAMT-isnull(VPY,0)-isnull(VCQ,0) as VBALANCE, PAYAMT-isnull(SNETP,0)-isnull(SNETP1,0) as TOTBANCE
				from #main a
				left join #main2 b on a.CONTNO = b.CONTNO and a.LOCAT = b.LOCATPAY and a.PAYFOR = b.PAYFOR
				order by ".$orderby."
		";//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$sql = "
				select 'รวมทั้งหมด' as Total, sum(NPAYAMT) as sumNPAYAMT, sum(VPAYAMT) as sumVPAYAMT, sum(PAYAMT) as sumPAYAMT, 
				sum(isnull(SNETP,0)-isnull(VPY,0)) as sumPAY, sum(isnull(VPY,0)) as sumVPY, sum(isnull(SNETP,0)) as sumTOTPAY, 
				sum(NPAYAMT-(isnull(SNETP,0)-isnull(VPY,0))) as sumAR, sum(VPAYAMT-isnull(VPY,0)) as sumVTAR, 
				sum(PAYAMT-isnull(SNETP,0)) as sumTOTAR, sum(isnull(SNETP1,0)-isnull(VCQ,0)) as sumCHQ, sum(isnull(VCQ,0)) as sumVCHQ, 
				sum(isnull(SNETP1,0)) as sumTOTCHQ, sum(NPAYAMT-(isnull(SNETP,0)-isnull(VPY,0))-isnull(SNETP1,0)-isnull(VCQ,0)) as sumBALANCE, 
				sum(VPAYAMT-isnull(VPY,0)-isnull(VCQ,0)) as sumVBALANCE, sum(PAYAMT-isnull(SNETP,0)-isnull(SNETP1,0)) as sumTOTBANCE
				from #main a
				left join #main2 b on a.CONTNO = b.CONTNO and a.LOCAT = b.LOCATPAY and a.PAYFOR = b.PAYFOR
		";
		//echo $sql; exit;
		$query2 = $this->db->query($sql);
		
		$head = ""; $html = ""; $i=0; 

		$head = "
				<tr>
					<th style='border-bottom:0.1px solid black;text-align:left;'>#</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>สาขา</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>เลขที่สัญญา<br>เลขรันลูกหนี้</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>รหัสลูกค้า<br>ชื่อ - นามสกุล</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>วันค้างชำระ<br>การขาย</th> 
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>ค้างชำระ</th> 
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>มูลค่ายอดลูกหนี้<br>ภาษียอดลูกหนี้</th> 
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>ยอดตั้งลูกหนี้<br>มูลค่าเช็ค</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>มูลค่าชำระ<br>ภาษีเช็ค</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>ภาษีชำระ<br>เช็ครอเรียกเก็บ</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>ชำระแล้ว<br>มูลค่าหักเช็ค</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>มูลค่าล/นคงเหลือ<br>ภาษีหักเช็ค</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>ภาษีคงเหลือ<br>คงเหลือหักเช็ค</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>ลูกหนี้คงเหลือ<br>ผู้บันทึกรายการ</th>
				</tr>
		";
		
		$No = 1;
		if($query->row()){
			foreach($query->result() as $row){	
				$html .= "
					<tr class='trow' seq=".$No.">
						<td style='width:25px;'>".$No++."</td>
						<td style='width:40px;'>".$row->LOCAT."</td>
						<td style='width:85px;'>".$row->CONTNO."<br>".$row->ARCONT."</td>
						<td style='width:130px;'>".$row->CUSCOD."<br>".$row->CUSNAME."</td>
						<td style='width:80px;'>".$this->Convertdate(2,$row->ARDATES)."<br>".$row->TSALE."</td>
						<td style='width:120px;'>".$row->PAYFOR."<br>".$row->FORDESC."</td>
						<td style='width:80px;' align='right'>".number_format($row->NPAYAMT,2)."<br>".number_format($row->VPAYAMT,2)."</td>
						<td style='width:75px;' align='right'>".number_format($row->PAYAMT,2)."<br>".number_format($row->CHQ,2)."</td>
						<td style='width:60px;' align='right'>".number_format($row->PAY,2)."<br>".number_format($row->VCHQ,2)."</td>
						<td style='width:75px;' align='right'>".number_format($row->VPY,2)."<br>".number_format($row->TOTCHQ,2)."</td>
						<td style='width:70px;' align='right'>".number_format($row->TOTPAY,2)."<br>".number_format($row->BALANCE,2)."</td>
						<td style='width:85px;' align='right'>".number_format($row->AR,2)."<br>".number_format($row->VBALANCE,2)."</td>
						<td style='width:75px;' align='right'>".number_format($row->VTAR,2)."<br>".number_format($row->TOTBANCE,2)."</td>
						<td style='width:75px;' align='right'>".number_format($row->TOTAR,2)."<br>".$row->USERID."</td>
					</tr>
				";	
			}
		}
		
		if($query2->row()){
			foreach($query2->result() as $row){	
				$html .= "
					<tr class='trow bor' style='background-color:#ebebeb;'>
						<td colspan='6' style='text-align:center;vertical-align:middle;'>".$row->Total."</td>
						<td align='right'>".number_format($row->sumNPAYAMT,2)."<br>".number_format($row->sumVPAYAMT,2)."</td>
						<td align='right'>".number_format($row->sumPAYAMT,2)."<br>".number_format($row->sumCHQ,2)."</td>
						<td align='right'>".number_format($row->sumPAY,2)."<br>".number_format($row->sumVCHQ,2)."</td>
						<td align='right'>".number_format($row->sumVPY,2)."<br>".number_format($row->sumTOTCHQ,2)."</td>
						<td align='right'>".number_format($row->sumTOTPAY,2)."<br>".number_format($row->sumBALANCE,2)."</td>
						<td align='right'>".number_format($row->sumAR,2)."<br>".number_format($row->sumVBALANCE,2)."</td>
						<td align='right'>".number_format($row->sumVTAR,2)."<br>".number_format($row->sumTOTBANCE,2)."</td>
						<td align='right'>".number_format($row->sumTOTAR,2)."<br>".$row->sumUSERID."</td>
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
						<th colspan='14' style='font-size:10pt;'>รายงานลูกหนี้คงเหลือจากลูกหนี้อื่น</th>
					</tr>
					<tr>
						<td colspan='14' style='font-size:8pt;height:35px;border-bottom:0.1px solid black;text-align:center;'>".'ลูกหนี้ระหว่างวันที่ '.$this->Convertdate(2,$FRMDATE).' ถึง '.$this->Convertdate(2,$TODATE)."".$rpcond."</td>
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