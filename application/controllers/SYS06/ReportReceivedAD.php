<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/********************************************************
             ______@17/01/2020______
			 Pasakorn Boonded

********************************************************/
class ReportReceivedAD extends MY_Controller {
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
	function index(){
		$claim = $this->MLogin->getclaim(uri_string());
		if($claim['m_access'] != "T"){ echo "<div align='center' style='color:red;font-size:16pt;width:100%;'>ขออภัย คุณยังไม่มีสิทธิเข้าใช้งานหน้านี้ครับ</div>"; exit; }
		$html = "
			<div class='k_tab1' name='home' locat='{$this->sess['branch']}' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}' today='".$this->today('today')."' style='height:calc(100vh - 132px);overflow:auto;background-color:white;'>
				<div class='col-sm-12 col-xs-12' style='height:100%;overflow:auto;font-size:11pt;'>					
					<div class='row' style='height:90%;'>
						<div class='col-sm-12 col-xs-12' style='background-color:#808b96;border:5px solid white;height:75px;text-align:center;font-size:12pt;color:white;font-weight:bold;'>	
							<br>รายงานการรับหลังวันที่ปัจจุบัน<br>
						</div>
						<div class='col-sm-10 col-xs-10 col-sm-offset-1 text-primary'>	
							<br>
							<div class='col-sm-3'>	
								<div class='form-group'>
									รับชำระที่สาขา
									<select id='LOCATRECV' class='form-control input-sm' data-placeholder='สาขา'></select>
								</div>
							</div>
							<div class='col-sm-3'>	
								<div class='form-group' >
									เพื่อ บ/ช ของสาขา
									<select id='LOCATPAY' class='form-control input-sm' data-placeholder='สาขา'></select>
								</div>
							</div>
							<div class='col-sm-3'>	
								<div class='form-group'>
									รับชำระจากวันที่
									<input type='text' id='DATE1' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' value='".$this->today('today')."' style='font-size:10.5pt'>
								</div>
							</div>
							<div class='col-sm-3'>	
								<div class='form-group'>
									ชำระโดย
									<select id='PAYTYP' class='form-control input-sm' data-placeholder='ชำระโดย'></select>
								</div>
							</div>
							<div class='col-sm-3'>	
								<div class='form-group'>
									ชำระค่า
									<select id='PAYFOR' class='form-control input-sm' data-placeholder='ชำระค่า'></select>
								</div>
							</div>
							<div class='col-sm-3'>	
								<div class='form-group'>
									พนักงานที่บันทึก
									<select id='USERID' class='form-control input-sm' data-placeholder='พนักงานที่บันทึก'></select>
								</div>
							</div>
						</div>
						<div class='col-sm-10 col-xs-10 col-sm-offset-1'><br>	
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group'>
									รูปแบบรายงาน
									<div class='col-sm-12 col-xs-12' style='border:0.1px dotted #d6d6d6;'>	
										<div class='col-sm-12 col-sm-offset-2'>
											<div class='form-group'>
												<br>
												<label>
													&nbsp;&nbsp;<input type= 'radio' id='R1' name='report' checked> แสดงรายการทั้งหมด
												</label>
												<br><br>
												<label>
													&nbsp;&nbsp;<input type= 'radio' id='R2' name='report'> สรุปตามการรับชำระ
												</label>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group'>
									จาก วันที่
									<div class='col-sm-12 col-xs-12' style='border:0.1px dotted #d6d6d6;'>	
										<div class='col-sm-12 col-sm-offset-2'>
											<div class='form-group'>
												<br>
												<label>
													<input type= 'radio' id='D1' name='DP' checked> บันทึก
												</label>
												<br><br>
												<label>
													<input type= 'radio' id='D2' name='DP'> รับชำระ
												</label>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class='col-sm-10 col-xs-10 col-sm-offset-1'><br>	
							<div class='col-sm-12 col-xs-12'>	
								<div class='form-group'>
									เรียงลำดับข้อมูลตาม <br>
									<div class='col-sm-12 col-xs-12' style='border:0.1px dotted #d6d6d6;'>	
										<div class='col-sm-12 col-sm-offset-1'>
											<div class='col-sm-4'>
												<div class='form-group'>
													<br>
													<label>
														<input type= 'radio' id='OR1' name='order' checked> ใบรับเงินชั่วคราว
													</label>
												</div>
											</div>
											<div class='col-sm-4'>
												<div class='form-group'>
													<br>
													<label>
														<input type= 'radio' id='OR2' name='order'> วันที่ใบรับ
													</label>
												</div>
											</div>
											<div class='col-sm-4'>
												<div class='form-group'>
													<br>
													<label>
														<input type= 'radio' id='OR3' name='order'> วันที่นำฝาก
													</label>
												</div>
											</div>
											<div class='col-sm-4'>
												<div class='form-group'>
													<br>
													<label>
														<input type= 'radio' id='OR4' name='order'> ใบเสร็จรับเงิน
													</label>
												</div>
											</div>
											<div class='col-sm-4'>
												<div class='form-group'>
													<br>
													<label>
														<input type= 'radio' id='OR5' name='order'> วันที่เช็ค
													</label>
												</div>
											</div>
											<div class='col-sm-4'>
												<div class='form-group'>
													<br>
													<label>
														<input type= 'radio' id='OR6' name='order'> สาขาที่รับ
													</label>
												</div>
											</div>
										</div>	
									</div>
								</div>
								<div class='col-sm-12 col-xs-12'>
									<br>
									<button id='btnreportAD' type='button' class='btn btn-info btn-outline btn-block' style='width:100%'><span class='fa fa-folder-open'><b>แสดง</b></span></button>
								</div><br>
							</div>
						</div>
					</div>
				</div>
			</div>
		";
		$html .="<script src='".base_url('public/js/SYS06/ReportReceivedAD.js')."'></script>";
		echo $html;
	}
	function conditiontopdf(){
		$data = array();
		$data[] = urlencode($_REQUEST["LOCATRECV"].'||'.$_REQUEST["LOCATPAY"].'||'.$_REQUEST["DATE1"]
		.'||'.$_REQUEST["PAYTYP"].'||'.$_REQUEST["PAYFOR"].'||'.$_REQUEST["USERID"]
		.'||'.$_REQUEST["report"].'||'.$_REQUEST["dat"].'||'.$_REQUEST["order"]);
		echo json_encode($this->generateData($data,"encode"));
	}
	function pdflistall(){
		$data = array();
		$data[] = $_GET["condpdf"];
		$arrs = $this->generateData($data,"decode");
		$arrs[0] = urldecode($arrs[0]);
		
		$tx = explode("||",$arrs[0]);
		$LOCATRECV 	= $tx[0];
		$LOCATPAY 	= $tx[1];
		$DATE1 	    = $this->Convertdate(1,$tx[2]);
		$PAYTYP 	= $tx[3];
		$PAYFOR 	= $tx[4];
		$USERID 	= $tx[5];
		$report 	= $tx[6];
		$dat 	    = $tx[7];
		$order 	    = $tx[8];
		//echo $order; exit;
		$SCRT = "";
		if($order == "TMBILL"){
			$SCRT = "ใบรับเงินชั่วคราว";
		}else if($order == "TMBILDT"){
			$SCRT = "วันที่ใบรับ";
		}else if($order == "PAYINDT"){
			$SCRT = "วันที่นำฝาก";
		}else if($order == "BILLNO"){
			$SCRT = "ใบเสร็จรับเงิน";
		}else if($order == "CHQDT"){
			$SCRT = "วันที่เช็ค";
		}else if($order == "LOCATRECV"){
			$SCRT = "สาขาที่รับ";
		}
		$sql = "
			select PAYCODE,PAYDESC from {$this->MAuth->getdb('PAYTYP')} 
			where PAYCODE = '".$PAYTYP."'
		";
		$PAYDESC = "";
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$PAYDESC = $row->PAYDESC;
			}
		}
		$sql = "
			select FORCODE,FORDESC from {$this->MAuth->getdb('PAYFOR')} 
			where FORCODE = '".$PAYFOR."'
		";
		$FORDESC = "";
		$query1 = $this->db->query($sql);
		if($query1->row()){
			foreach($query1->result() as $row){
				$FORDESC = $row->FORDESC;
			}
		}
		$sql = "
			select USERID,USERNAME from {$this->MAuth->getdb('PASSWRD')} 
			where USERID = '".$USERID."'
		";
		//echo $sql; exit;
		$USERNAME = "";
		$query2 = $this->db->query($sql);
		if($query2->row()){
			foreach($query2->result() as $row){
				$USERNAME = $row->USERNAME;
			}
		}
		$sql = "
			IF OBJECT_ID('tempdb..#RAD') IS NOT NULL DROP TABLE #RAD
			select LOCATRECV,TMBILDT,TMBILL,CHQNO,CHQDT,CHQBR,ACCTNO,PAYINACC,PAYINDT,FLAG,CHQAMT
				,CHQBK,PAYTYP,INPDT,USERID,BILLNO,BILLDT
			into #RAD
			FROM(
				select LOCATRECV,convert(varchar(8),TMBILDT,112) as TMBILDT,TMBILL,CHQNO,CHQDT,CHQBR,ACCTNO,PAYINACC,PAYINDT,FLAG,CHQAMT
				,CHQBK,PAYTYP,convert(varchar(8),INPDT,112) as INPDT,USERID,BILLNO,convert(varchar(8),BILLDT,112) as BILLDT 
				from {$this->MAuth->getdb('CHQMAS')} 
				where LOCATRECV like '%".$LOCATRECV."%' and ".$dat." > '".$DATE1."' and PAYTYP like '%".$PAYTYP."%'
				and USERID like '%".$USERID."%' and TMBILL in (select TMBILL from {$this->MAuth->getdb('CHQTRAN')} T 
				where LOCATPAY like '%".$LOCATPAY."%' and PAYFOR like '%".$PAYFOR."%')
			)RAD
		";
		//echo $sql; exit;
		$query3 = $this->db->query($sql);
		$sql = "
			select * from #RAD order by ".$order."
		";
		$query4 = $this->db->query($sql);
		$sql = "
			IF OBJECT_ID('tempdb..#TAD') IS NOT NULL DROP TABLE #TAD
			select TMBILL,TMBILDT,TSALE,LOCATRECV,LOCATPAY,CONTNO,PAYAMT,PAYAMT_V
				,DISCT,PAYFOR,PAYINT,DSCINT,NETPAY,FLAG,SNAM,NAME1,NAME2,FORDESC
			into #TAD
			FROM(
				select A.TMBILL,A.TMBILDT,A.TSALE,A.LOCATRECV,A.LOCATPAY,A.CONTNO,A.PAYAMT,A.PAYAMT_V
				,A.DISCT,A.PAYFOR,A.PAYINT,A.DSCINT,A.NETPAY,A.FLAG,B.SNAM,B.NAME1,B.NAME2,C.FORDESC 
				from {$this->MAuth->getdb('CHQTRAN')} A
				left join {$this->MAuth->getdb('CUSTMAST')} B on A.CUSCOD = B.CUSCOD 
				left join {$this->MAuth->getdb('PAYFOR')} C on A.PAYFOR = C.FORCODE
				where A.TMBILL in (select TMBILL from #RAD)
			)TAD
		";
		$query5 = $this->db->query($sql);
		$sql = "
			select COUNT(PAYAMT) as countPAYAMT,SUM(PAYAMT) as PAYAMT,SUM(PAYAMT_V) as PAYAMT_V,SUM(DISCT) as DISCT
			,SUM(PAYINT) as PAYINT,SUM(DSCINT) as DSCINT,SUM(NETPAY) as NETPAY from #TAD
		";
		$query6 = $this->db->query($sql);
		$sql = "
			select COUNT(PAYAMT) as countPAYAMT,SUM(PAYAMT) as PAYAMT,SUM(PAYAMT_V) as PAYAMT_V,SUM(DISCT) as DISCT
			,SUM(PAYINT) as PAYINT,SUM(DSCINT) as DSCINT,SUM(NETPAY) as NETPAY from #TAD where FLAG = 'C'
		";
		$query7 = $this->db->query($sql);
		$sql = "
			declare @A1 decimal(12,2) = (select coalesce(SUM(PAYAMT),0) from #TAD); 
			declare @B1 decimal(12,2) = (select coalesce(SUM(PAYAMT),0) from #TAD where FLAG = 'C'); 
			declare @A2 decimal(12,2) = (select coalesce(SUM(PAYAMT_V),0)  from #TAD); 
			declare @B2 decimal(12,2) = (select coalesce(SUM(PAYAMT_V),0)  from #TAD where FLAG = 'C'); 
			declare @A3 decimal(12,2) = (select coalesce(SUM(DISCT),0) from #TAD); 
			declare @B3 decimal(12,2) = (select coalesce(SUM(DISCT),0) from #TAD where FLAG = 'C'); 
			declare @A4 decimal(12,2) = (select coalesce(SUM(PAYINT),0) from #TAD); 
			declare @B4 decimal(12,2) = (select coalesce(SUM(PAYINT),0) from #TAD where FLAG = 'C');
			declare @A5 decimal(12,2) = (select coalesce(SUM(DSCINT),0) from #TAD); 
			declare @B5 decimal(12,2) = (select coalesce(SUM(DSCINT),0) from #TAD where FLAG = 'C');
			declare @A6 decimal(12,2) = (select coalesce(SUM(NETPAY),0) from #TAD); 
			declare @B6 decimal(12,2) = (select coalesce(SUM(NETPAY),0) from #TAD where FLAG = 'C'); 

			select @A1-@B1 as PAYAMT,@A2-@B2 as PAYAMT_V,@A3-@B3 as DISCT,@A4-@B4 as PAYINT,@A5-@B5 as DSCINT,@A6-@B6 as NETPAY
		";
		$query8 = $this->db->query($sql);
		$head = ""; $html = ""; $i = 0;
		$head = "
			<tr class='wm'>
				<td class='wf' style='height:1px;border-top:0.1px solid black;' colspan='14'></td>
			</tr>
			<tr>
				<th style='border-bottom:0.1px solid black;text-align:left;'>สาขา</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>เลขที่ใบรับเงินชั่วคราว<br>เลขที่สัญญา</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>วันที่ใบรับเงิน<br>ชื่อ - สกุล</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>เลขที่ใบเสร็จรับเงิน</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>วันที่ใบเสร็จ<br>ชำระค่า</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>ชำระโดย</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>เลขที่เช็ค<br>BillColl</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>เช็คธนาคาร<br>ยอดหักลูกหนี้</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>จำนวนเงินทั้งหมด<br>ภาษีมูลค่าเพิ่ม</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>UserID<br>ส่วนลด</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>วันที่บันทึก<br>เบี้ยปรับ</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'><br>ส่วนลดเบี้ยปรับ</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'><br>ยอดรับสุทธิ</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'><br>ลูกหนี้คงเหลือ</th>
			</tr>
			<tr>
				<td class='wf' style='height:1px;border-top:0.1px solid black;' colspan='14'></td>
			</tr>
		";
		$FLAG = "";
		if($query4->row()){
			foreach($query4->result() as $row){$i++;
				if($row->FLAG == "C"){
					$FLAG = "**ยกเลิก**";
				}else{
					$FLAG = "";
				}
				$sql1 = "
					IF OBJECT_ID('tempdb..#TAD1') IS NOT NULL DROP TABLE #TAD1
					select TMBILL,TMBILDT,TSALE,LOCATRECV,LOCATPAY,CONTNO,PAYAMT,PAYAMT_V
						,DISCT,PAYFOR,PAYINT,DSCINT,NETPAY,FLAG,CUSNAME
						,FORDESC
					into #TAD1
					from(
						select A.TMBILL,A.TMBILDT,A.TSALE,A.LOCATRECV,A.LOCATPAY,A.CONTNO,A.PAYAMT,A.PAYAMT_V
						,A.DISCT,A.PAYFOR,A.PAYINT,A.DSCINT,A.NETPAY,A.FLAG,B.SNAM+B.NAME1+' '+B.NAME2 as CUSNAME
						,C.FORDESC from {$this->MAuth->getdb('CHQTRAN')} A
						left join {$this->MAuth->getdb('CUSTMAST')} B on A.CUSCOD = B.CUSCOD 
						left join {$this->MAuth->getdb('PAYFOR')} C on A.PAYFOR = C.FORCODE
						where A.TMBILL = '{$row->TMBILL}' and A.LOCATRECV = '{$row->LOCATRECV}'
					)TAD1
				";
				//echo $sql1; exit;
				$query1 = $this->db->query($sql1);
				$sql2 = "
					select A.TMBILL,A.TMBILDT,A.TSALE,A.LOCATRECV,A.LOCATPAY,A.CONTNO
					,A.PAYAMT,A.PAYAMT_V,A.DISCT,A.PAYFOR,A.PAYINT,A.DSCINT,A.NETPAY
					,A.FLAG,A.CUSNAME,A.FORDESC,B.BILLCOLL,B.TOTPRC-B.TOTPRES as TOTP from #TAD1 A
					left join (
						select CONTNO,BILLCOLL,TOTPRC,TOTPRES from {$this->MAuth->getdb('ARMAST')} 
						where CONTNO in (select CONTNO from #TAD1) and LOCAT in (select LOCATPAY from #TAD1)
						union
						select CONTNO,BILLCOLL,TOTPRC,TOTPRES from {$this->MAuth->getdb('HARMAST')} 
						where CONTNO in (select CONTNO from #TAD1) and LOCAT in (select LOCATPAY from #TAD1)
					)B on A.CONTNO = B.CONTNO
				";
				//echo $sql2; exit;
				$query2 = $this->db->query($sql2);
				$html2 = "";
				if($query2->row()){
					foreach($query2->result() as $row2){
						$html2 .="
							<tr>
								<th style='width:70px;text-align:left;'></th>
								<th style='width:180px;text-align:left;'>".$row2->CONTNO."</th>
								<th style='width:70px;text-align:left;' colspan='2'>".$row2->CUSNAME."</th>
								<th style='width:90px;text-align:left;' colspan='2'>".$row2->FORDESC."</th>
								<th style='width:80px;text-align:left;'>".$row2->BILLCOLL."</th>
								<th style='width:70px;text-align:right;'>".number_format($row2->PAYAMT,2)."</th>
								<th style='width:70px;text-align:right;'>".number_format($row2->PAYAMT_V,2)."</th>
								<th style='width:70px;text-align:right;'>".number_format($row2->DISCT,2)."</th>
								<th style='width:70px;text-align:right;'>".number_format($row2->PAYINT,2)."</th>
								<th style='width:70px;text-align:right;'>".number_format($row2->DSCINT,2)."</th>
								<th style='width:100px;text-align:right;'>".number_format($row2->NETPAY,2)."</th>
								<th style='width:70px;text-align:right;'></th>
							</tr>
						";
					}
				}
				$html .="
					<tr>
						<th style='width:70px;text-align:left;'>".$row->LOCATRECV."</th>
						<th style='width:180px;text-align:left;'>".$row->TMBILL."</th>
						<th style='width:80px;text-align:left;'>".$this->Convertdate(2,$row->TMBILDT)."</th>
						<th style='width:90px;text-align:left;'>".$row->BILLNO."</th>
						<th style='width:80px;text-align:left;'>".$this->Convertdate(2,$row->BILLDT)."</th>
						<th style='width:70px;text-align:left;'>".$row->PAYTYP."</th>
						<th style='width:70px;text-align:left;'>".$row->CHQNO."</th>
						<th style='width:70px;text-align:right;'>".$row->CHQBK."</th>
						<th style='width:70px;text-align:right;'>".number_format($row->CHQAMT,2)."</th>
						<th style='width:70px;text-align:right;'>".$row->USERID."</th>
						<th style='width:90px;text-align:right;'>".$this->Convertdate(2,$row->INPDT)."</th>
						<th style='width:70px;text-align:right;' colspan='3'>".$FLAG."</th>
					</tr>
					".$html2."
				";
			}
		}
		if($query6->row()){
			foreach($query6->result() as $row){
				$sql1 = "select COUNT(TMBILDT) as countTMBILDT from #RAD";
				$query1 = $this->db->query($sql1);
				$countTMBILDT = "";
				if($query1->row()){
					foreach($query1->result() as $row1){
						$countTMBILDT = $row1->countTMBILDT;
					}
				}
				$html .="
					<tr class='wm'>
						<td class='wf' style='height:1px;border-top:0.1px solid black;' colspan='14'></td>
					</tr>
					<tr>
						<th style='width:70px;text-align:left;'>รวมทั้งสิ้น</th>
						<th style='width:70px;text-align:center;' colspan='2'>".$countTMBILDT."</th>
						<th style='width:70px;text-align:left;'>รายการ</th>
						<th style='width:70px;text-align:left;' colspan='3'>รวมทั้งสิ้น</th>
						<th style='width:70px;text-align:right;'>".number_format($row->PAYAMT,2)."</th>
						<th style='width:70px;text-align:right;'>".number_format($row->PAYAMT_V,2)."</th>
						<th style='width:70px;text-align:right;'>".number_format($row->DISCT,2)."</th>
						<th style='width:70px;text-align:right;'>".number_format($row->PAYINT,2)."</th>
						<th style='width:70px;text-align:right;'>".number_format($row->DSCINT,2)."</th>
						<th style='width:70px;text-align:right;'>".number_format($row->NETPAY,2)."</th>
					</tr>
				";
			}
		}
		if($query7->row()){
			foreach($query7->result() as $row){
				$html .="
					<tr>
						<th style='width:70px;text-align:left;'></th>
						<th style='width:70px;text-align:center;' colspan='2'></th>
						<th style='width:70px;text-align:left;'></th>
						<th style='width:70px;text-align:left;' colspan='3'>ยอดรวมรายการยกเลิก</th>
						<th style='width:70px;text-align:right;'>".number_format($row->PAYAMT,2)."</th>
						<th style='width:70px;text-align:right;'>".number_format($row->PAYAMT_V,2)."</th>
						<th style='width:70px;text-align:right;'>".number_format($row->DISCT,2)."</th>
						<th style='width:70px;text-align:right;'>".number_format($row->PAYINT,2)."</th>
						<th style='width:70px;text-align:right;'>".number_format($row->DSCINT,2)."</th>
						<th style='width:70px;text-align:right;'>".number_format($row->NETPAY,2)."</th>
					</tr>
				";
			}
		}
		if($query8->row()){
			foreach($query8->result() as $row){
				$html .="
					<tr>
						<th style='width:70px;text-align:left;'></th>
						<th style='width:70px;text-align:center;' colspan='2'></th>
						<th style='width:70px;text-align:left;'></th>
						<th style='width:70px;text-align:left;' colspan='3'>ยอดรวมสุทธิ</th>
						<th style='width:70px;text-align:right;'>".number_format($row->PAYAMT,2)."</th>
						<th style='width:70px;text-align:right;'>".number_format($row->PAYAMT_V,2)."</th>
						<th style='width:70px;text-align:right;'>".number_format($row->DISCT,2)."</th>
						<th style='width:70px;text-align:right;'>".number_format($row->PAYINT,2)."</th>
						<th style='width:70px;text-align:right;'>".number_format($row->DSCINT,2)."</th>
						<th style='width:70px;text-align:right;'>".number_format($row->NETPAY,2)."</th>
					</tr>
					<tr class='wm'>
						<td class='wf' style='height:1px;border-top:0.1px solid black;' colspan='14'></td>
					</tr>
				";
			}
		}
		$mpdf = new \Mpdf\Mpdf([
			'mode' => 'utf-8', 
			'format' => 'A4-L',
			'margin_top' => 10, 	//default = 16
			'margin_left' => 10, 	//default = 15
			'margin_right' => 10, 	//default = 15
			'margin_bottom' => 10, 	//default = 16
			'margin_header' => 9, 	//default = 9
			'margin_footer' => 9, 	//default = 9
		]);
		if($i > 0){
			$content = "
				<table class='wf' style='font-size:7.5pt;height:700px;border-collapse:collapse;line-height:23px;overflow:wrap;vertical-align:text-top;'>
					<tbody>
						<tr>
							<th colspan='14' style='font-size:10pt;'>บริษัท ตั้งใจพัฒนายานยนต์ จำกัด</th>
						</tr>
						<tr>
							<th colspan='14' style='font-size:9pt;'>รายงานการรับหลังวันที่ปัจจุบัน</th>
						</tr>
						<tr>
							<td style='text-align:center;' colspan='14'>
								<b>สาขาที่รับชำระ</b> &nbsp;&nbsp;".$LOCATRECV."&nbsp;&nbsp;
								<b>ชำระเพื่อ บ/ช สาขา</b>&nbsp;&nbsp;".$LOCATPAY."&nbsp;&nbsp;
								<b>จากวันที่</b>&nbsp;&nbsp;".$this->Convertdate(2,$DATE1)."&nbsp;&nbsp;
							</td>
						</tr>
						<tr>
							<td style='text-align:left;' colspan='2'><b>Sort By :</b>&nbsp;&nbsp;".$SCRT."</td>
							<td style='text-align:center;' colspan='10'>
								<b>ชำระโดย</b>&nbsp;&nbsp;".$PAYDESC."&nbsp;&nbsp;
								<b>ชำระค่า</b>&nbsp;&nbsp;".$FORDESC."&nbsp;&nbsp;
								<b>พนักงานบันทึกข้อมูล</b>&nbsp;&nbsp;".$USERNAME."&nbsp;&nbsp;
							</td>
							<td style='text-align:right;' colspan='2'>RpRec,RpRec 61</td>
						</tr>
						<br>
						".$head."
						".$html."
					</tbody>
				</table>
			";
			$head = "
				<div class='wf pf' style='top:1060;left:600;top:715;left:880; font-size:6pt;'>วันที่พิมพ์รายงาน : ".date('d/m/').(date('Y')+543)." ".date('H:i')." หน้า {PAGENO} / {nbpg}</div>
			";
		}else{
			$content = "<div style='color:red;'>ไม่พบข้อมูลตามเงื่อนไข</div>";
			$head = "
				<div class='wf pf' style='top:1060;left:600;top:715;left:880; font-size:6pt;'></div>
			";
		}
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
		$mpdf->SetHTMLHeader($head);	
		$mpdf->WriteHTML($content);	
		$mpdf->Output();
	}
	function pdfpay(){
		$data = array();
		$data[] = $_GET["condpdf"];
		$arrs = $this->generateData($data,"decode");
		$arrs[0] = urldecode($arrs[0]);
		
		$tx = explode("||",$arrs[0]);
		$LOCATRECV 	= $tx[0];
		$LOCATPAY 	= $tx[1];
		$DATE1 	    = $this->Convertdate(1,$tx[2]);
		$PAYTYP 	= $tx[3];
		$PAYFOR 	= $tx[4];
		$USERID 	= $tx[5];
		$report 	= $tx[6];
		$dat 	    = $tx[7];
		$order 	    = $tx[8];
		
		$sql = "
			select PAYCODE,PAYDESC from {$this->MAuth->getdb('PAYTYP')} 
			where PAYCODE = '".$PAYTYP."'
		";
		$PAYDESC = "";
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$PAYDESC = $row->PAYDESC;
			}
		}
		$sql = "
			select FORCODE,FORDESC from {$this->MAuth->getdb('PAYFOR')} 
			where FORCODE = '".$PAYFOR."'
		";
		$FORDESC = "";
		$query1 = $this->db->query($sql);
		if($query1->row()){
			foreach($query1->result() as $row){
				$FORDESC = $row->FORDESC;
			}
		}
		$sql = "
			select USERID,USERNAME from {$this->MAuth->getdb('PASSWRD')} 
			where USERID = '".$USERID."'
		";
		//echo $sql; exit;
		$USERNAME = "";
		$query2 = $this->db->query($sql);
		if($query2->row()){
			foreach($query2->result() as $row){
				$USERNAME = $row->USERNAME;
			}
		}
		$sql = "
			IF OBJECT_ID('tempdb..#PAY') IS NOT NULL DROP TABLE #PAY
			select PAYFOR,FORDESC,T01,N01,V01,D01,I01,NET01,T02,N02,V02,D02,I02,NET02,T03,N03,V03
				,D03,I03,NET03,T04,N04,V04,D04,I04,NET04
			into #PAY
			FROM(
				select A.PAYFOR,B.FORDESC 
				,sum(case when (A.PAYTYP = '01') then  A.PAYAMT else 0 end) as T01 
				,sum(case when (A.PAYTYP = '01') then  A.PAYAMT_N else 0 end) as N01 
				,sum(case when (A.PAYTYP = '01') then  A.PAYAMT_V else 0 end) as V01 
				,sum(case when (A.PAYTYP = '01') then  A.DISCT else 0 end) as D01 
				,sum(case when (A.PAYTYP = '01') then  (A.PAYINT-A.DSCINT) else 0 end) as I01 
				,sum(case when (A.PAYTYP = '01') then  A.NETPAY else 0 end) as NET01 
				,sum(case when (A.PAYTYP = '02') then  A.PAYAMT else 0 end) as T02 
				,sum(case when (A.PAYTYP = '02') then  A.PAYAMT_N else 0 end) as N02 
				,sum(case when (A.PAYTYP = '02') then  A.PAYAMT_V else 0 end) as V02 
				,sum(case when (A.PAYTYP = '02') then  A.DISCT else 0 end) as D02 
				,sum(case when (A.PAYTYP = '02') then  (A.PAYINT-A.DSCINT) else 0 end) as I02 
				,sum(case when (A.PAYTYP = '02') then  A.NETPAY else 0 end) as NET02 
				,sum(case when (A.PAYTYP not in ('01' ,'02')) then  A.PAYAMT else 0 end) as T03 
				,sum(case when (A.PAYTYP not in ('01' ,'02')) then  A.PAYAMT_N else 0 end) as N03 
				,sum(case when (A.PAYTYP not in ('01' ,'02')) then  A.PAYAMT_V else 0 end) as V03 
				,sum(case when (A.PAYTYP not in ('01' ,'02')) then  A.DISCT else 0 end) as D03 
				,sum(case when (A.PAYTYP not in ('01' ,'02')) then  (A.PAYINT-A.DSCINT) else 0 END) as I03 
				,sum(case when (A.PAYTYP not in ('01' ,'02')) then  A.NETPAY else 0 end) as NET03 
				,sum(A.PAYAMT) as T04 ,sum(A.PAYAMT_N) as N04 ,sum(A.PAYAMT_V) AS V04 ,sum(A.DISCT) as D04 
				,sum(A.PAYINT-A.DSCINT) as I04 ,sum(A.NETPAY) as NET04 from {$this->MAuth->getdb('CHQTRAN')} A
				left join {$this->MAuth->getdb('PAYFOR')} B on B.FORCODE = A.PAYFOR 
				where  A.USERID like '%".$USERID."%' and A.LOCATRECV like '%".$LOCATRECV."%' and A.LOCATPAY like '%".$LOCATPAY."%' 
				and A.INPDT > '".$DATE1."'  
				and A.PAYTYP like '%".$PAYTYP."%' and A.PAYFOR like '%".$PAYFOR."%' and A.FLAG <> 'C' group by A.PAYFOR,B.FORDESC
			)PAY
		";
		//echo $sql; exit;
		$query3 = $this->db->query($sql);
		$sql = "select * from #PAY";
		$query4 = $this->db->query($sql);
		$sql = "
			select count(PAYFOR) as countPAYFOR
				,SUM(T01) as T01,SUM(N01) as N01,SUM(V01) as V01,SUM(D01) as D01,SUM(I01) as I01,SUM(NET01) as NET01
				,SUM(T02) as T02,SUM(N02) as N02,SUM(V02) as V02,SUM(D02) as D02,SUM(I02) as I02,SUM(NET02) as NET02
				,SUM(T03) as T03,SUM(N03) as N03,SUM(V03) as V03,SUM(D03) as D03,SUM(I03) as I03,SUM(NET03) as NET03
				,SUM(T04) as T04,SUM(N04) as N04,SUM(V04) as V04,SUM(D04) as D04,SUM(I04) as I04,SUM(NET04) as NET04
			from #PAY
		";
		$query5 = $this->db->query($sql); 
		$head = ""; $html = ""; $i = 0;
		$head = "
			<tr class='wm'>
				<td class='wf' style='height:1px;border-top:0.1px solid black;' colspan='18'></td>
			</tr>
			<tr>
				<th style='border-bottom:0.1px solid black;text-aling:right;' colspan='2'>รายการรับชำระ</th>
				<th style='border-bottom:0.1px solid black;text-align:right;' colspan='4'>ชำระโดยเงินสด(1)</th>
				<th style='border-bottom:0.1px solid black;text-align:right;' colspan='3'>ชำระโดยเช็ค(2)</th>
				<th style='border-bottom:0.1px solid black;text-align:right;' colspan='3'>อื่นๆ</th>
				<th style='border-bottom:0.1px solid black;text-align:right;' colspan='3'>รวม</th>
			</tr>
			<tr>
				<td class='wf' style='height:1px;border-top:0.1px solid black;' colspan='18'></td>
			</tr>
			<tr>
				<th style='border-bottom:0.1px solid black;text-align:right;' colspan='3'>หักลูกหนี้<br>มูลค่าหัก ล/น</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ส่วนลด<br>ภาษี</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>เบี้ยปรับ</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>รับสุทธิ</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>หักลูกหนี้<br>มูลค่าหัก ล/น</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ส่วนลด<br>ภาษี</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>เบี้ยปรับ</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>รับสุทธิ</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>หักลูกหนี้<br>มูลค่าหัก ล/น</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ส่วนลด<br>ภาษี</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>เบี้ยปรับ</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>รับสุทธิ</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>หักลูกหนี้<br>มูลค่าหัก ล/น</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ส่วนลด<br>ภาษี</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>เบี้ยปรับ</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>รับสุทธิ</th>
			</tr>
			<tr>
				<td class='wf' style='height:1px;border-top:0.1px solid black;' colspan='18'></td>
			</tr>
		";
		if($query4->row()){
			foreach($query4->result() as $row){$i++;
				$html .="
					<tr>
						<th style='width:200px;text-align:left;'>".$row->PAYFOR." ".$row->FORDESC."</th>
						<th style='width:10px;text-align:left;'></th>
						<th style='width:70px;text-align:right;'>".number_format($row->T01,2)."<br>".number_format($row->N01,2)."</th>
						<th style='width:70px;text-align:right;'>".number_format($row->D01,2)."<br>".number_format($row->V01,2)."</th>
						<th style='width:50px;text-align:right;'>".number_format($row->I01,2)."</th>
						<th style='width:70px;text-align:right;'>".number_format($row->NET01,2)."</th>
						<th style='width:70px;text-align:right;'>".number_format($row->T02,2)."<br>".number_format($row->N02,2)."</th>
						<th style='width:70px;text-align:right;'>".number_format($row->D02,2)."<br>".number_format($row->V02,2)."</th>
						<th style='width:50px;text-align:right;'>".number_format($row->I02,2)."</th>
						<th style='width:70px;text-align:right;'>".number_format($row->NET02,2)."</th>
						<th style='width:70px;text-align:right;'>".number_format($row->T03,2)."<br>".number_format($row->N03,2)."</th>
						<th style='width:70px;text-align:right;'>".number_format($row->D03,2)."<br>".number_format($row->V03,2)."</th>
						<th style='width:50px;text-align:right;'>".number_format($row->I03,2)."</th>
						<th style='width:70px;text-align:right;'>".number_format($row->NET03,2)."</th>
						<th style='width:70px;text-align:right;'>".number_format($row->T04,2)."<br>".number_format($row->N04,2)."</th>
						<th style='width:70px;text-align:right;'>".number_format($row->D04,2)."<br>".number_format($row->V04,2)."</th>
						<th style='width:50px;text-align:right;'>".number_format($row->I04,2)."</th>
						<th style='width:70px;text-align:right;'>".number_format($row->NET04,2)."</th>
					</tr>
				";
			}
		}
		if($query5->row()){
			foreach($query5->result() as $row){
				$html .="
					<tr class='trow bor' style='background-color:#ebebeb;'>
						<th style='width:70px;text-align:center;' colspan='2'>รวมทั้งสิ้น  ".$row->countPAYFOR." รายการ</th>
						<th style='width:70px;text-align:right;'>".number_format($row->T01,2)."<br>".number_format($row->N01,2)."</th>
						<th style='width:70px;text-align:right;'>".number_format($row->D01,2)."<br>".number_format($row->V01,2)."</th>
						<th style='width:50px;text-align:right;'>".number_format($row->I01,2)."</th>
						<th style='width:70px;text-align:right;'>".number_format($row->NET01,2)."</th>
						<th style='width:70px;text-align:right;'>".number_format($row->T02,2)."<br>".number_format($row->N02,2)."</th>
						<th style='width:70px;text-align:right;'>".number_format($row->D02,2)."<br>".number_format($row->V02,2)."</th>
						<th style='width:50px;text-align:right;'>".number_format($row->I02,2)."</th>
						<th style='width:70px;text-align:right;'>".number_format($row->NET02,2)."</th>
						<th style='width:70px;text-align:right;'>".number_format($row->T03,2)."<br>".number_format($row->N03,2)."</th>
						<th style='width:70px;text-align:right;'>".number_format($row->D03,2)."<br>".number_format($row->V03,2)."</th>
						<th style='width:50px;text-align:right;'>".number_format($row->I03,2)."</th>
						<th style='width:70px;text-align:right;'>".number_format($row->NET03,2)."</th>
						<th style='width:70px;text-align:right;'>".number_format($row->T04,2)."<br>".number_format($row->N04,2)."</th>
						<th style='width:70px;text-align:right;'>".number_format($row->D04,2)."<br>".number_format($row->V04,2)."</th>
						<th style='width:50px;text-align:right;'>".number_format($row->I04,2)."</th>
						<th style='width:70px;text-align:right;'>".number_format($row->NET04,2)."</th>
					</tr>
				";
			}
		}
		$mpdf = new \Mpdf\Mpdf([
			'mode' => 'utf-8', 
			'format' => 'A4-L',
			'margin_top' => 10, 	//default = 16
			'margin_left' => 10, 	//default = 15
			'margin_right' => 10, 	//default = 15
			'margin_bottom' => 10, 	//default = 16
			'margin_header' => 9, 	//default = 9
			'margin_footer' => 9, 	//default = 9
		]);
		if($i > 0){
			$content = "
				<table class='wf' style='font-size:7.5pt;height:700px;border-collapse:collapse;line-height:23px;overflow:wrap;vertical-align:text-top;'>
					<tbody>
						<tr>
							<th colspan='18' style='font-size:10pt;'>บริษัท ตั้งใจพัฒนายานยนต์ จำกัด</th>
						</tr>
						<tr>
							<th colspan='18' style='font-size:9pt;'>รายงานการรับชำระตามวันที่บันทึก</th>
						</tr>
						<tr>
							<td style='text-align:center;' colspan='18'>
								<b>สาขาที่รับชำระ</b> &nbsp;&nbsp;".$LOCATRECV."&nbsp;&nbsp;
								<b>ชำระเพื่อ บ/ช สาขา</b>&nbsp;&nbsp;".$LOCATPAY."&nbsp;&nbsp;
								<b>จากวันที่</b>&nbsp;&nbsp;".$DATE1."&nbsp;&nbsp;
							</td>
						</tr>
						<tr>
							<td style='text-align:center;' colspan='18'>
								<b>ชำระโดย</b>&nbsp;&nbsp;".$PAYDESC."&nbsp;&nbsp;
								<b>ชำระค่า</b>&nbsp;&nbsp;".$FORDESC."&nbsp;&nbsp;
								<b>พนักงานบันทึกข้อมูล</b>&nbsp;&nbsp;".$USERNAME."&nbsp;&nbsp;
							</td>
						</tr>
						<tr>
							<td style='text-align:right;' colspan='18'>RpRec,RpRec 61</td>
						</tr>
						<br>
						".$head."
						".$html."
					</tbody>
				</table>
			";
			$head = "
				<div class='wf pf' style='top:1060;left:600;top:715;left:880; font-size:6pt;'>วันที่พิมพ์รายงาน : ".date('d/m/').(date('Y')+543)." ".date('H:i')." หน้า {PAGENO} / {nbpg}</div>
			";
		}else{
			$content = "<div style='color:red;'>ไม่พบข้อมูลตามเงื่อนไข</div>";
			$head = "
				<div class='wf pf' style='top:1060;left:600;top:715;left:880; font-size:6pt;'></div>
			";
		}
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
		$mpdf->SetHTMLHeader($head);	
		$mpdf->WriteHTML($content);	
		$mpdf->Output();
	}
}