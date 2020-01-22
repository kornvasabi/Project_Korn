<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//BEE+
class ReportARxlastpay extends MY_Controller {
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
							<br>รายงานลูกหนี้คงเหลือ x งวดสุดท้าย ณ ปัจจุบัน<br>
						</div>
						<div class='col-sm-10 col-xs-10 col-sm-offset-1'>
							<br>
							<div class='col-sm-3 col-xs-3'>	
								<div class='form-group'>
									สาขา
									<select id='LOCAT1' class='form-control input-sm' data-placeholder='สาขา'></select>
								</div>
							</div>
							<div class='col-sm-3 col-xs-3'>	
								<div class='form-group' >
									จากเลขที่สัญญา
									<select id='CONTNO1' class='form-control input-sm' data-placeholder='เลขที่สัญญา'></select>
								</div>
							</div>
							<div class='col-sm-3 col-xs-3'>	
								<div class='form-group' >
									Checker
									<select id='CHECKER1' class='form-control input-sm' data-placeholder='Checker'></select>
								</div>
							</div>
							<div class='col-sm-3 col-xs-3'>	
								<div class='form-group' >
									พนักงานเก็บเงิน
									<select id='BILLCOL1' class='form-control input-sm' data-placeholder='พนักงานเก็บเงิน'></select>
								</div>
							</div>
							<div class='col-sm-3 col-xs-3'>	
								<div class='form-group'>
									ตำบล
									<input type='text' id='TUMBON1' class='form-control input-sm' style='font-size:10.5pt' placeholder='ตำบล'>
								</div>
							</div>
							<div class='col-sm-3 col-xs-3'>	
								<div class='form-group' >
									อำเภอ
									<select id='AMPHUR1' class='form-control input-sm AUMP' data-placeholder='อำเภอ'></select>
								</div>
							</div>
							<div class='col-sm-3 col-xs-3'>	
								<div class='form-group' >
									จังหวัด
									<select id='PROVINCE1' class='form-control input-sm AUMP' data-placeholder='จังหวัด'></select>
								</div>
							</div>
							<div class='col-sm-3 col-xs-3'>	
								<div class='form-group' >
									กลุ่มสินค้า
									<select id='GCOCE1' class='form-control input-sm' data-placeholder='กลุ่มสินค้า'></select>
								</div>
							</div>
							<div class='col-sm-3 col-xs-3'>	
								<div class='form-group' >
									ยี่ห้อ
									<select id='TYPE1' class='form-control input-sm' data-placeholder='ยี่ห้อ'></select>
								</div>
							</div>
							<div class='col-sm-3 col-xs-3'>	
								<div class='form-group' >
									รุ่น
									<select id='MODEL1' class='form-control input-sm' data-placeholder='รุ่น'></select>
								</div>
							</div>
							<div class='col-sm-3 col-xs-3'>	
								<div class='form-group' >
									แบบ
									<select id='BAAB1' class='form-control input-sm' data-placeholder='แบบ'></select>
								</div>
							</div>
							<div class='col-sm-3 col-xs-3'>	
								<div class='form-group' >
									สี
									<select id='COLOR1' class='form-control input-sm' data-placeholder='สี'></select>
								</div>
							</div>
							<div class='col-sm-3 col-xs-3'>	
								<div class='form-group'>
									ลูกหนี้คงเหลือ <= (งวด)
									<input type='text' id='EXPPRD1' class='form-control input-sm' style='font-size:10.5pt' value='1'>
								</div>
							</div>
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group'>
									เลขที่สมาชิก
									<input type='text' id='CUSCOD1' class='form-control input-sm' style='font-size:10.5pt' placeholder='เลขที่สมาชิก'>
								</div>
							</div>
							<div class='col-sm-3 col-xs-3'>	
								<div class='form-group' style='padding:4px;'>
									<br>
									<input type='checkbox' class='form-check-input' id='sumkang' name='exppay' checked> รวมงวดที่ค้างชำระ
								</div>
							</div>
						</div>
						<div class='col-sm-10 col-xs-10 col-sm-offset-1'>	
							<div class='col-sm-3 col-xs-3'>
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
							<div class='col-sm-3 col-xs-3'>	
								<div class='form-group'>
									ประเภท
									<div class='col-sm-12 col-xs-12' style='border:0.1px dotted #d6d6d6;'>	
										<div class='col-sm-12 col-xs-12'>
											<div class='form-group'>
												<br>
												<input type= 'radio' id='new' name='types'> รถใหม่
												<br><br>
												<input type= 'radio' id='old' name='types'> รถเก่า
												<br><br>
												<input type= 'radio' id='all' name='types' checked> ทั้งหมด
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class='col-sm-3 col-xs-3'>	
								<div class='form-group'>
									เรียงลำดับข้อมูล
									<div class='col-sm-12 col-xs-12' style='border:0.1px dotted #d6d6d6;'>	
										<div class='col-sm-12 col-xs-12'>
											<div class='form-group'>
												<br>
												<input type= 'radio' id='contno' name='orderby' checked> ตามเลขที่สัญญา
												<br><br>
												<input type= 'radio' id='cuscod' name='orderby'> ตามรหัสลูกค้า
												<br><br>
												<input type= 'radio' id='lpayd' name='orderby'> วันชำระล่าสุด
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class='col-sm-3 col-xs-3'>	
								<div class='form-group'>
									ต้องการ
									<div class='col-sm-12 col-xs-12' style='border:0.1px dotted #d6d6d6;'>	
										<div class='col-sm-12 col-xs-12'>
											<div class='form-group'>
												<br>
												<input type= 'radio' id='y_yes' name='sumy' checked> รวมรถยึด
												<br><br><br><br>
												<input type= 'radio' id='y_no' name='sumy'> ไม่รวมรถยึด
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
		
		$html.= "<script src='".base_url('public/js/SYS05/ReportARxlastpay.js')."'></script>";
		echo $html;
	}
	
	function search(){
		$LOCAT1		= $_REQUEST["LOCAT1"];
		$CONTNO1 	= str_replace(chr(0),'',$_REQUEST["CONTNO1"]);
		$BILLCOL1 	= str_replace(chr(0),'',$_REQUEST["BILLCOL1"]);
		$CHECKER1 	= str_replace(chr(0),'',$_REQUEST["CHECKER1"]);
		$AMPHUR1 	= str_replace(chr(0),'',$_REQUEST["AMPHUR1"]);
		$PROVINCE1 	= str_replace(chr(0),'',$_REQUEST["PROVINCE1"]);
		$GCOCE1 	= str_replace(chr(0),'',$_REQUEST["GCOCE1"]);
		$TYPE1 		= str_replace(chr(0),'',$_REQUEST["TYPE1"]);
		$MODEL1 	= str_replace(chr(0),'',$_REQUEST["MODEL1"]);
		$BAAB1 		= str_replace(chr(0),'',$_REQUEST["BAAB1"]);
		$COLOR1 	= str_replace(chr(0),'',$_REQUEST["COLOR1"]);
		$TUMBON1 	= $_REQUEST["TUMBON1"];
		$EXPPRD1 	= $_REQUEST["EXPPRD1"];
		$MEMBCOD 	= $_REQUEST["CUSCOD1"];
		$types 		= $_REQUEST["types"];
		$ystat 		= $_REQUEST["ystat"];
		$sumkang 	= $_REQUEST["sumkang"];
		$orderby 	= $_REQUEST["orderby"];
	
		$cond = ""; $rpcond = "";
		
		if($LOCAT1 != ""){
			$cond .= " AND (A.LOCAT LIKE '%".$LOCAT1."%')";
			$rpcond .= "  สาขา ".$LOCAT1;
		}
		
		if($CONTNO1 != ""){
			$cond .= " AND (A.CONTNO LIKE '%".$CONTNO1."%')";
			$rpcond .= "  เลขที่สัญญา ".$CONTNO1;
		}
		
		if($BILLCOL1 != ""){
			$cond .= " AND (A.BILLCOLL = '".$BILLCOL1."' )";
			$rpcond .= "  พนักงานเก็บเงิน ".$BILLCOL1;
		}else{
			$cond .= " AND (A.BILLCOLL LIKE '%%' OR A.BILLCOLL IS NULL)";
		}
		
		if($CHECKER1 != ""){
			$cond .= " AND (A.Checker LIKE '%".$CHECKER1."%' )";
			$rpcond .= "  Checker ".$CHECKER1;
		}else{
			$cond .= " AND (A.Checker LIKE '%%' OR A.Checker IS NULL)";
		}
		
		if($AMPHUR1 != ""){
			$cond .= " AND (D.AUMPCOD LIKE '%".$AMPHUR1."%' )";
		}else{
			$cond .= " AND (D.AUMPCOD LIKE '%%' OR D.AUMPCOD IS NULL)";
		}
		
		if($PROVINCE1 != ""){
			$cond .= " AND (D.PROVCOD LIKE '%".$PROVINCE1."%' )";
		}else{
			$cond .= " AND (D.PROVCOD LIKE '%%' OR D.PROVCOD IS NULL)";
		}
		
		if($GCOCE1 != ""){
			$cond .= " AND (C.GCODE LIKE '%".$GCOCE1."%' )";
			$rpcond .= "  กลุ่มสินค้า ".$GCOCE1;
		}
		
		if($TYPE1 != ""){
			$cond .= " AND (C.TYPE LIKE '%".$TYPE1."%' )";
		}else{
			$cond .= " AND (C.TYPE LIKE '%%' OR C.TYPE IS NULL)";
		}
		
		if($MODEL1 != ""){
			$cond .= " AND (C.MODEL LIKE '%".$MODEL1."%' )";
		}else{
			$cond .= " AND (C.MODEL LIKE '%%' OR C.MODEL IS NULL)";
		}
		
		if($BAAB1 != ""){
			$cond .= " AND (C.BAAB LIKE '%".$BAAB1."%' )";
		}else{
			$cond .= " AND (C.BAAB LIKE '%%' OR C.BAAB IS NULL)";
		}
		
		if($COLOR1 != ""){
			$cond .= " AND (C.COLOR LIKE '%".$COLOR1."%' )";
		}else{
			$cond .= " AND (C.COLOR LIKE '%%' OR C.COLOR IS NULL) ";
		}
		
		if($TUMBON1 != ""){
			$cond .= " AND D.TUMB LIKE '%".$TUMBON1."%'";
		}
		
		if($MEMBCOD != ""){
			$cond .= " AND B.MEMBCOD LIKE '%".$MEMBCOD."%'";
		}
		
		if($ystat == 'NO'){
			$cond .= " AND (A.YSTAT<>'Y')";
		}
		
		if($types != ""){
			$cond .= " AND (C.STAT = '".$types."')";
		}
		
		if($sumkang == 'sumkang'){
			$sumkang = " DDATE >= '18000101'";
		}else{
			$sumkang = " convert(nvarchar,getdate(),112)";
		}
		
		$sql = "
				IF OBJECT_ID('tempdb..#main') IS NOT NULL DROP TABLE #main
				select *
				into #main
				from(
					select LOCAT, CONTNO, CUSCOD, SNAM+NAME1+' '+NAME2 as CUSNAME, isnull(addr1,'')+isnull(' ถ.'+addr2,'')+isnull(' ต.'+tumb,'')+
					isnull(' อ.'+aumpdes,'')+isnull(' จ.'+provdes,'')+' '+isnull(zip,'')+isnull(' โทร. '+telp,'') as CUSADD, convert(nvarchar,SDATE,112) as SDATE,
					TOTPRC, T_NOPAY, TOTPRC-SMPAY as TOTAR, EXP_FRM, EXP_TO, LPAYD, convert(nvarchar,LPAYD,112) as LPAYDS, SMPAY, convert(nvarchar,LDATE,112) as LDATE, 
					EXP_AMT,
					TYPE, MODEL, BAAB, COLOR
					from(
						SELECT A.LOCAT, A.CONTNO, A.TOTPRC, A.TOTPRES, A.CUSCOD, A.SDATE, B.SNAM, A.TOTDWN, A.LPAYD, A.LDATE, A.BILLCOLL,
						A.TOT_UPAY, A.EXP_AMT, A.SMPAY, A.EXP_FRM, A.EXP_TO, B.NAME1, B.NAME2, B.MEMBCOD, A.PAYDWN, A.T_NOPAY, C.TYPE, C.BAAB,
						C.COLOR, C.MODEL, B.addrno , D.addr1, D.addr2, D.tumb, D.aumpcod, D.provcod, D.zip, D.telp, E.nopayfrm, E.nopayto,  
						(select aumpdes from setaump where aumpcod=D.aumpcod) as aumpdes, (select provdes from setprov where provcod=D.provcod) as provdes 
						FROM ARMAST A 
						LEFT OUTER JOIN CUSTMAST B ON (A.CUSCOD = B.CUSCOD)  
						LEFT OUTER JOIN INVTRAN C ON (A.STRNO = C.STRNO)  
						LEFT OUTER JOIN CUSTADDR D ON (A.CUSCOD = D.CUSCOD) AND (A.ADDRNO = D.ADDRNO)  
						LEFT OUTER JOIN (
							SELECT E.CONTNO, E.LOCAT, COUNT(NOPAY) AS KANGNOPAY, min(nopay) as nopayfrm, max(nopay) as nopayto  
							FROM ARPAY E 
							WHERE ".$sumkang." AND (damt > payment)  
							GROUP BY E.CONTNO, E.LOCAT
						) E ON A.CONTNO=E.CONTNO AND A.LOCAT=E.LOCAT  
						WHERE KANGNOPAY <= ".$EXPPRD1." ".$cond." 
					)a
				)main
		";//echo $sql; 
		$query = $this->db->query($sql);
		
		$sql = "
				select LOCAT, CONTNO, CUSCOD, CUSNAME, CUSCOD+'  '+CUSNAME as CUSTOMER, CUSADD, SDATE, TOTPRC, T_NOPAY, TOTAR, EXP_FRM, EXP_TO, LPAYD, LPAYDS, SMPAY, LDATE, 
				EXP_AMT, TYPE, MODEL, BAAB, COLOR
				from #main
				order by ".$orderby."
		";//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$sql = "
				select 'รวมทั้งหมด' as Total, sum(TOTPRC) as sumTOTPRC, sum(TOTAR) as sumTOTAR, sum(SMPAY) as sumSMPAY, sum(EXP_AMT) as sumEXP_AMT
				from #main
		";
		//echo $sql; exit;
		$query2 = $this->db->query($sql);
		
		$head = ""; $html = ""; $head2 = "";  $report = ""; $sumreport = ""; $sumreport2 = ""; $i = 0; 
		
		$head = "<tr style='height:35px;'>
				<th style='display:none;'>#</th>
				<th style='vertical-align:top;'>สาขา</th>
				<th style='vertical-align:top;'>เลขที่สัญญา</th>
				<th style='vertical-align:top;'>รหัส/ชื่อ-สกุล ลูกค้า<br>ที่อยู่ลูกค้า</th>
				<th style='vertical-align:top;'>วันที่ขาย<br>ยี่ห้อ</th>
				<th style='vertical-align:top;'>วันดิวงวดสุดท้าย<br>รุ่น</th>
				<th style='vertical-align:top;'>จน. งวดทั้งหมด<br>แบบ</th>
				<th style='vertical-align:top;text-align:right;'>ราคาขาย<br>สี</th> 
				<th style='vertical-align:top;text-align:right;'>จำนวนที่ชำระ<br>วันที่ชำระล่าสุด</th>
				<th style='vertical-align:top;text-align:right;'>ยอดคงเหลือ</th>
				<th style='vertical-align:top;text-align:right;'>งวดที่</th>
				<th style='vertical-align:top;text-align:right;'>ค้างงวด</th>
				</tr>
		";
		
		$head2 = "<tr>
					<th style='vertical-align:middle;'>#</th>
					<th style='vertical-align:top;'>สาขา</th>
					<th style='vertical-align:top;'>เลขที่สัญญา</th>
					<th style='vertical-align:top;'>รหัสลูกค้า</th>
					<th style='vertical-align:top;'>ชื่อ - นามสกุล</th>
					<th style='vertical-align:top;'>ที่อยู่</th>
					<th style='vertical-align:top;'>ยี่ห้อ</th>
					<th style='vertical-align:top;'>รุ่น</th>
					<th style='vertical-align:top;'>แบบ</th>
					<th style='vertical-align:top;'>สี</th>
					<th style='vertical-align:top;'>วันที่ขาย</th>
					<th style='vertical-align:top;'>วันดิวงวดสุดท้าย</th>
					<th style='vertical-align:top;'>จน. งวดทั้งหมด</th>
					<th style='vertical-align:top;text-align:right;'>ราคาขาย</th> 
					<th style='vertical-align:top;text-align:right;'>จำนวนที่ชำระ </th>
					<th style='vertical-align:top;text-align:right;'>ยอดคงเหลือ</th>
					<th style='vertical-align:top;text-align:center;'>งวดที่</th>
					<th style='vertical-align:top;text-align:right;'>วันที่ชำระล่าสุด</th>
					<th style='vertical-align:top;text-align:right;'>ค้างงวด</th>
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
							<td>".$row->CUSTOMER."<br>".$row->CUSADD."<br></td>
							<td>".$this->Convertdate(2,$row->SDATE)."<br>".$row->TYPE."</td>
							<td>".$this->Convertdate(2,$row->LDATE)."<br>".$row->MODEL."</td>
							<td>".$row->T_NOPAY."<br>".$row->BAAB."</td>
							<td align='right'>".number_format($row->TOTPRC,2)."<br>".$row->COLOR."</td>
							<td align='right'>".number_format($row->SMPAY,2)."<br>".$this->Convertdate(2,$row->LPAYDS)."</td>
							<td align='right'>".number_format($row->TOTAR,2)."</td>
							<td align='center'>".number_format($row->EXP_FRM).'-'.number_format($row->EXP_TO)."</td>
							<td align='right'>".number_format($row->EXP_AMT,2)."</td>
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
							<td style='mso-number-format:\"\@\";'>".$row->CUSADD."</td>
							<td style='mso-number-format:\"\@\";'>".$row->TYPE."</td>
							<td style='mso-number-format:\"\@\";'>".$row->MODEL."</td>
							<td style='mso-number-format:\"\@\";'>".$row->BAAB."</td>
							<td style='mso-number-format:\"\@\";'>".$row->COLOR."</td>
							<td style='mso-number-format:\"\@\";'>".$this->Convertdate(2,$row->SDATE)."</td>
							<td style='mso-number-format:\"\@\";'>".$this->Convertdate(2,$row->LDATE)."</td>
							<td style='mso-number-format:\"\@\";'>".$row->T_NOPAY."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->TOTPRC,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->SMPAY,2)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->TOTAR,2)."</td>
							<td style='mso-number-format:\"\@\";text-align:center;'>".number_format($row->EXP_FRM).'-'.number_format($row->EXP_TO)."</td>
							<td style='mso-number-format:\"\@\";'>".$this->Convertdate(2,$row->LPAYDS)."</td>
							<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->EXP_AMT,2)."</td>
						</tr>
					";	
				}
			}
			
			if($query2->row()){
				foreach($query2->result() as $row){
					$sumreport = "
						<tr style='height:30px;'>
							<th colspan='6' style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;vertical-align:middle;text-align:center;'>".$row->Total."</th>
							<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;vertical-align:middle;text-align:right;'>".number_format($row->sumTOTPRC,2)."</th>
							<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;vertical-align:middle;text-align:right;'>".number_format($row->sumSMPAY,2)."</th>
							<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;vertical-align:middle;text-align:right;'>".number_format($row->sumTOTAR,2)."</th>
							<th style='border-right:1px solid #ddd;border-left:0px;border-bottom:0px;border-top:0px;vertical-align:middle;text-align:right;'></th>
							<th style='border:0px;vertical-align:middle;text-align:right;'>".number_format($row->sumEXP_AMT,2)."</th>
						</tr>
					";
				}
			}
			
			if($query2->row()){
				foreach($query2->result() as $row){
					$sumreport2 = "
						<tr class='trow'>
							<th style='mso-number-format:\"\@\";text-align:center;' colspan='13'>".$row->Total."</th>
							<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumTOTPRC,2)."</th>
							<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumSMPAY,2)."</th>
							<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumTOTAR,2)."</th>
							<th style='mso-number-format:\"\@\";text-align:center;' colspan='2'></th>
							<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumEXP_AMT,2)."</th>
						</tr>
					";	
				}
			}
			
			if($i>0){
				$html = "
					<div id='table-fixed-ReportARxlastpay' class='col-sm-12' style='height:100%;width:100%;overflow:auto;font-size:8pt;'>
						<table id='table-ReportARxlastpay' style='background-color:white;' class='col-sm-12 display table table-bordered' cellspacing='0' width='calc(100% - 1px)'>
							<thead>
							<tr>
								<th colspan='11' style='height:30px;font-size:12pt;border:0px;text-align:center;'>รายงานลูกหนี้ค้างชำระน้อยกว่าหรือเท่ากับ ".$EXPPRD1." งวดสุดท้าย</th>
							</tr>
							<tr>
								<td colspan='11' style='height:30px;border-bottom:1px solid #ddd;text-align:center;'><br>".$rpcond."  ออกรายงาน ณ วันที่ ".$this->today('today')."</td>
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
				<div id='table-fixed-ReportARxlastpay2' class='col-sm-12' style='height:100%;width:100%;overflow:auto;'>
					<table id='table-ReportARxlastpay2' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='100%'>
						<thead>
							<tr>
								<th colspan='19' style='font-size:12pt;border:0px;text-align:center;'>รายงานลูกหนี้ค้างชำระน้อยกว่าหรือเท่ากับ ".$EXPPRD1." งวดสุดท้าย</th>
							</tr>
							<tr>
								<td colspan='19' style='border:0px;text-align:center;'>".$rpcond." ออกรายงาน ณ วันที่ ".$this->today('today')."</td>
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
		$data[] = 	urlencode($_REQUEST["LOCAT1"].'||'.$_REQUEST["CONTNO1"].'||'.$_REQUEST["BILLCOL1"].'||'.$_REQUEST["CHECKER1"]
					.'||'.$_REQUEST["AMPHUR1"].'||'.$_REQUEST["PROVINCE1"].'||'.$_REQUEST["GCOCE1"].'||'.$_REQUEST["TYPE1"]
					.'||'.$_REQUEST["MODEL1"].'||'.$_REQUEST["BAAB1"].'||'.$_REQUEST["COLOR1"].'||'.$_REQUEST["TUMBON1"]
					.'||'.$_REQUEST["EXPPRD1"].'||'.$_REQUEST["CUSCOD1"].'||'.$_REQUEST["types"].'||'.$_REQUEST["ystat"]
					.'||'.$_REQUEST["sumkang"].'||'.$_REQUEST["orderby"].'||'.$_REQUEST["layout"]);
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
		$CHECKER1 	= str_replace(chr(0),'',$tx[3]);
		$AMPHUR1 	= str_replace(chr(0),'',$tx[4]);
		$PROVINCE1 	= str_replace(chr(0),'',$tx[5]);
		$GCOCE1 	= str_replace(chr(0),'',$tx[6]);
		$TYPE1 		= str_replace(chr(0),'',$tx[7]);
		$MODEL1 	= str_replace(chr(0),'',$tx[8]);
		$BAAB1 		= str_replace(chr(0),'',$tx[9]);
		$COLOR1 	= str_replace(chr(0),'',$tx[10]);
		$TUMBON1 	= $tx[11];
		$EXPPRD1 	= $tx[12];
		$CUSCOD1 	= $tx[13];
		$types 		= $tx[14];
		$ystat 		= $tx[15];
		$sumkang 	= $tx[16];
		$orderby 	= $tx[17];
		$layout 	= $tx[18];
		
		$cond = ""; $rpcond = "";
		
		if($LOCAT1 != ""){
			$cond .= " AND (A.LOCAT LIKE '%".$LOCAT1."%')";
			$rpcond .= "  สาขา ".$LOCAT1;
		}
		
		if($CONTNO1 != ""){
			$cond .= " AND (A.CONTNO LIKE '%".$CONTNO1."%')";
			$rpcond .= "  เลขที่สัญญา ".$CONTNO1;
		}
		
		if($BILLCOL1 != ""){
			$cond .= " AND (A.BILLCOLL LIKE '%".$BILLCOL1."%' )";
			$rpcond .= "  พนักงานเก็บเงิน ".$BILLCOL1;
		}else{
			$cond .= " AND (A.BILLCOLL LIKE '%%' OR A.BILLCOLL IS NULL)";
		}
		
		if($CHECKER1 != ""){
			$cond .= " AND (A.Checker LIKE '%".$CHECKER1."%' )";
			$rpcond .= "  Checker ".$CHECKER1;
		}else{
			$cond .= " AND (A.Checker LIKE '%%' OR A.Checker IS NULL)";
		}
		
		if($AMPHUR1 != ""){
			$cond .= " AND (D.AUMPCOD LIKE '%".$AMPHUR1."%' )";
		}else{
			$cond .= " AND (D.AUMPCOD LIKE '%%' OR D.AUMPCOD IS NULL)";
		}
		
		if($PROVINCE1 != ""){
			$cond .= " AND (D.PROVCOD LIKE '%".$PROVINCE1."%' )";
		}else{
			$cond .= " AND (D.PROVCOD LIKE '%%' OR D.PROVCOD IS NULL)";
		}
		
		if($GCOCE1 != ""){
			$cond .= " AND (C.GCODE LIKE '%".$GCOCE1."%' )";
			$rpcond .= "  กลุ่มสินค้า ".$GCOCE1;
		}
		
		if($TYPE1 != ""){
			$cond .= " AND (C.TYPE LIKE '%".$TYPE1."%' )";
		}else{
			$cond .= " AND (C.TYPE LIKE '%%' OR C.TYPE IS NULL)";
		}
		
		if($MODEL1 != ""){
			$cond .= " AND (C.MODEL LIKE '%".$MODEL1."%' )";
		}else{
			$cond .= " AND (C.MODEL LIKE '%%' OR C.MODEL IS NULL)";
		}
		
		if($BAAB1 != ""){
			$cond .= " AND (C.BAAB LIKE '%".$BAAB1."%' )";
		}else{
			$cond .= " AND (C.BAAB LIKE '%%' OR C.BAAB IS NULL)";
		}
		
		if($COLOR1 != ""){
			$cond .= " AND (C.COLOR LIKE '%".$COLOR1."%' )";
		}else{
			$cond .= " AND (C.COLOR LIKE '%%' OR C.COLOR IS NULL) ";
		}
		
		if($TUMBON1 != ""){
			$cond .= " AND D.TUMB LIKE '%".$TUMBON1."%'";
		}
		
		if($MEMBCOD != ""){
			$cond .= " AND B.MEMBCOD LIKE '%".$MEMBCOD."%'";
		}
		
		if($ystat == 'NO'){
			$cond .= " AND (A.YSTAT<>'Y')";
		}
		
		if($types != ""){
			$cond .= " AND (C.STAT = '".$types."')";
		}
		
		if($sumkang == 'sumkang'){
			$sumkang = " DDATE >= '18000101'";
		}else{
			$sumkang = " convert(nvarchar,getdate(),112)";
		}
		
		$sql = "
				IF OBJECT_ID('tempdb..#main') IS NOT NULL DROP TABLE #main
				select *
				into #main
				from(
					select LOCAT, CONTNO, CUSCOD, SNAM+NAME1+' '+NAME2 as CUSNAME, isnull(addr1,'')+isnull(' ถ.'+addr2,'')+isnull(' ต.'+tumb,'')+
					isnull(' อ.'+aumpdes,'')+isnull(' จ.'+provdes,'')+' '+isnull(zip,'')+isnull(' โทร. '+telp,'') as CUSADD, convert(nvarchar,SDATE,112) as SDATE,
					TOTPRC, T_NOPAY, TOTPRC-SMPAY as TOTAR, EXP_FRM, EXP_TO, LPAYD, convert(nvarchar,LPAYD,112) as LPAYDS, SMPAY, convert(nvarchar,LDATE,112) as LDATE, 
					EXP_AMT,
					TYPE, MODEL, BAAB, COLOR
					from(
						SELECT A.LOCAT, A.CONTNO, A.TOTPRC, A.TOTPRES, A.CUSCOD, A.SDATE, B.SNAM, A.TOTDWN, A.LPAYD, A.LDATE, A.BILLCOLL,
						A.TOT_UPAY, A.EXP_AMT, A.SMPAY, A.EXP_FRM, A.EXP_TO, B.NAME1, B.NAME2, B.MEMBCOD, A.PAYDWN, A.T_NOPAY, C.TYPE, C.BAAB,
						C.COLOR, C.MODEL, B.addrno , D.addr1, D.addr2, D.tumb, D.aumpcod, D.provcod, D.zip, D.telp, E.nopayfrm, E.nopayto,  
						(select aumpdes from setaump where aumpcod=D.aumpcod) as aumpdes, (select provdes from setprov where provcod=D.provcod) as provdes 
						FROM ARMAST A 
						LEFT OUTER JOIN CUSTMAST B ON (A.CUSCOD = B.CUSCOD)  
						LEFT OUTER JOIN INVTRAN C ON (A.STRNO = C.STRNO)  
						LEFT OUTER JOIN CUSTADDR D ON (A.CUSCOD = D.CUSCOD) AND (A.ADDRNO = D.ADDRNO)  
						LEFT OUTER JOIN (
							SELECT E.CONTNO, E.LOCAT, COUNT(NOPAY) AS KANGNOPAY, min(nopay) as nopayfrm, max(nopay) as nopayto  
							FROM ARPAY E 
							WHERE ".$sumkang." AND (damt > payment)  
							GROUP BY E.CONTNO, E.LOCAT
						) E ON A.CONTNO=E.CONTNO AND A.LOCAT=E.LOCAT  
						WHERE KANGNOPAY <= ".$EXPPRD1." ".$cond." 
					)a
				)main
		";//echo $sql; 
		$query = $this->db->query($sql);
		
		$sql = "
				select LOCAT, CONTNO, CUSCOD, CUSNAME, CUSCOD+'  '+CUSNAME as CUSTOMER, CUSADD, SDATE, TOTPRC, T_NOPAY, TOTAR, EXP_FRM, EXP_TO, LPAYD, LPAYDS, SMPAY, LDATE, 
				EXP_AMT, TYPE, MODEL, BAAB, COLOR
				from #main
				order by ".$orderby."
		";//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$sql = "
				select 'รวมทั้งหมด' as Total, sum(TOTPRC) as sumTOTPRC, sum(TOTAR) as sumTOTAR, sum(SMPAY) as sumSMPAY, sum(EXP_AMT) as sumEXP_AMT
				from #main
		";
		//echo $sql; exit;
		$query2 = $this->db->query($sql);
		
		$head = ""; $html = ""; $i=0; 

		$head = "
				<tr>
					<th style='border-bottom:0.1px solid black;text-align:left;'>#</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>สาขา</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>เลขที่สัญญา</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>รหัส/ชื่อ-สกุล ลูกค้า<br>ที่อยู่ลูกค้า</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>วันที่ขาย<br>ยี่ห้อ</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>วันดิวงวดสุดท้าย<br>รุ่น</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>จน. งวดทั้งหมด<br>แบบ</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>ราคาขาย<br>สี</th> 
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>จำนวนที่ชำระ<br>วันที่ชำระล่าสุด</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>ยอดคงเหลือ</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:center;'>งวดที่</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>ค้างงวด</th>
				</tr>
		";
		
		$No = 1;
		if($query->row()){
			foreach($query->result() as $row){	
				$html .= "
					<tr class='trow' seq=".$No.">
						<td style='width:25px;'>".$No++."</td>
						<td style='width:50px;'>".$row->LOCAT."</td>
						<td style='width:75px;'>".$row->CONTNO."</td>
						<td style='width:250px;'>".$row->CUSTOMER."<br>".$row->CUSADD."</td>
						<td style='width:75px;'>".$this->Convertdate(2,$row->SDATE)."<br>".$row->TYPE."</td>
						<td style='width:90px;'>".$this->Convertdate(2,$row->LDATE)."<br>".$row->MODEL."</td>
						<td style='width:70px;'>".$row->T_NOPAY."<br>".$row->BAAB."</td>
						<td style='width:90px;' align='right'>".number_format($row->TOTPRC,2)."<br>".$row->COLOR."</td>
						<td style='width:90px;' align='right'>".number_format($row->SMPAY,2)."<br>".$this->Convertdate(2,$row->LPAYDS)."</td>
						<td style='width:90px;' align='right'>".number_format($row->TOTAR,2)."</td>
						<td style='width:50px;' align='center'>".number_format($row->EXP_FRM).'-'.number_format($row->EXP_TO)."</td>
						<td style='width:80px;' align='right'>".number_format($row->EXP_AMT,2)."</td>
					</tr>
				";	
			}
		}
		
		if($query2->row()){
			foreach($query2->result() as $row){	
				$html .= "
					<tr class='trow bor' style='background-color:#ebebeb;'>
						<td colspan='7' style='text-align:center;vertical-align:middle;'>".$row->Total."</td>
						<td align='right'>".number_format($row->sumTOTPRC)."</td>
						<td align='right'>".number_format($row->sumSMPAY,2)."</td>
						<td align='right'>".number_format($row->sumTOTAR,2)."</td>
						<td></td>
						<td align='right'>".number_format($row->sumEXP_AMT,2)."</td>
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
						<th colspan='12' style='font-size:10pt;'>รายงานลูกหนี้ค้างชำระน้อยกว่าหรือเท่ากับ ".$EXPPRD1." งวดสุดท้าย</th>
					</tr>
					<tr>
						<td colspan='12' style='font-size:8pt;height:35px;border-bottom:0.1px solid black;text-align:center;'>".$rpcond." ณ วันที่ ".$this->today('today')."</td>
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