<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/********************************************************
             ______@28/12/2019______
			 Pasakorn Boonded

********************************************************/
class ReportReceivedDT extends MY_Controller {
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
							<br>รายงานการรับชำระเงินตามวันที่รับเงิน<br>
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
									ถึงวันที่
									<input type='text' id='DATE2' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' value='".$this->today('today')."' style='font-size:10.5pt'>
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
							<div class='col-sm-3'>	
								<div class='form-group'>
									กลุ่มลูกค้า
									<select id='GROUP1' class='form-control input-sm' data-placeholder='กลุ่มลูกค้า'></select>
								</div>
							</div>
							<div class='col-sm-3'>	
								<div class='form-group'>
									กลุ่มสินค้า
									<select id='GCODE' class='form-control input-sm' data-placeholder='กลุ่มสินค้า'></select>
								</div>
							</div>
							<div class='col-sm-3'>	
								<div class='form-group'>
									รหัสพนักงานเก็บเงิน
									<select id='CODE' class='form-control input-sm' data-placeholder='รหัสพนักงานเก็บเงิน'></select>
								</div>
							</div>
						</div>
						<div class='col-sm-10 col-xs-10 col-sm-offset-1'><br>	
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group'>
									รูปแบบรายงาน
									<div class='col-sm-12 col-xs-12' style='border:0.1px dotted #d6d6d6;'>	
										<div class='col-sm-12'>
											<div class='form-group'>
												<br>
												<label>
													<input type= 'radio' id='one' name='report' checked> แสดงรายการหนึ่งบรรทัด
												</label>
												<br><br>
												<label>
													<input type= 'radio' id='all' name='report'> แสดงรายการทั้งหมด
												</label>
												<br><br>
												<label>
													<input type= 'radio' id='pay' name='report'> สรุปตามการรับชำระ
												</label>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group'><br>
									<div class='col-sm-12 col-xs-12' style='border:0.1px dotted #d6d6d6;'>	
										<div class='col-sm-12'>
											<div class='form-group'>
												<br>
												<label>
													<input type= 'radio' id='tdt' name='DT' checked> ตามวันที่ใบรับ(TmBilDt)
												</label>
												<br><br>
												<label>
													<input type= 'radio' id='pdt' name='DT'> ตามวันที่ใบรับจริง(PayDt)
												</label>
												<br><br><br>
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
										<div class='col-sm-4'>
											<div class='form-group'>
												<br>
												<label>
													<input type= 'radio' id='bi1' name='Sort' checked> ใบรับเงินชั่วคราว
												</label>
											</div>
										</div>
										<div class='col-sm-4'>
											<div class='form-group'>
												<br>
												<label>
													<input type= 'radio' id='d1' name='Sort'> วันที่เช็ค
												</label>
											</div>
										</div>
										<div class='col-sm-4'>
											<div class='form-group'>
												<br>
												<label>
													<input type= 'radio' id='cont' name='Sort'> เลขที่สัญญา
												</label>
											</div>
										</div>
										<div class='col-sm-4'>
											<div class='form-group'>
												<br>
												<label>
													<input type= 'radio' id='bi2' name='Sort'> ใบเสร็จรับเงิน
												</label>
											</div>
										</div>
										<div class='col-sm-4'>
											<div class='form-group'>
												<br>
												<label>
													<input type= 'radio' id='d2' name='Sort'> วันที่นำฝาก
												</label>
											</div>
										</div>
										<div class='col-sm-4'>
											<div class='form-group'>
												<br>
												<label>
													<input type= 'radio' id='d3' name='Sort'> วันที่ใบรับ
												</label>
											</div>
										</div>
										<div class='col-sm-4'>
											<div class='form-group'>
												<br>
												<label>
													<input type= 'radio' id='locat' name='Sort'> สาขาที่ได้รับ
												</label>
											</div>
										</div>
									</div>
								</div>
								<div class='col-sm-12 col-xs-12'>
									<br>
									<button id='btnreport' type='button' class='btn btn-info btn-outline btn-block' style='width:100%'><span class='fa fa-folder-open'><b>แสดง</b></span></button>
								</div><br>
							</div>
						</div>
					</div>
				</div>
			</div>
		";
		$html .="<script src='".base_url('public/js/SYS06/ReportReceivedDT.js')."'></script>";
		echo $html;
	}
	function conditiontopdf(){
		$data = array();
		$data[] = urlencode($_REQUEST["LOCATRECV"].'||'.$_REQUEST["LOCATPAY"].'||'.$_REQUEST["DATE1"].'||'.$_REQUEST["DATE2"].'||'.$_REQUEST["PAYTYP"]
		.'||'.$_REQUEST["PAYFOR"].'||'.$_REQUEST["USERID"].'||'.$_REQUEST["GROUP1"].'||'.$_REQUEST["GCODE"].'||'.$_REQUEST["CODE"]
		.'||'.$_REQUEST["report"].'||'.$_REQUEST["dt"].'||'.$_REQUEST["sort"]);
		echo json_encode($this->generateData($data,"encode"));
	}
	function pdfone(){
		$data = array();
		$data[] = $_GET["condpdf"];
		$arrs = $this->generateData($data,"decode");
		$arrs[0] = urldecode($arrs[0]);
		
		$tx = explode("||",$arrs[0]);
		$LOCATRECV 	= $tx[0];
		$LOCATPAY 	= $tx[1];
		$DATE1 	    = $this->Convertdate(1,$tx[2]);
		$DATE2 		= $this->Convertdate(1,$tx[3]);
		$PAYTYP 	= $tx[4];
		$PAYFOR 	= $tx[5];
		$USERID 	= $tx[6];
		$GROUP1 	= $tx[7];
		$GCODE 		= $tx[8];
		$CODE 	    = $tx[9];
		$report 	= $tx[10];
		$dt 		= $tx[11];
		$sort 	    = $tx[12];
		
		$MDT = "";
		if($dt == "tdt"){
			$MDT = "TMBILDT";
		}else{
			$MDT = "PAYDT";
		}
		$MDT1 = "";
		if($dt == "tdt"){
			$MDT1 = "A.TMBILDT";
		}else{
			$MDT1 = "A.PAYDT";
		}
		$sql = "
			IF OBJECT_ID('tempdb..#ONE') IS NOT NULL DROP TABLE #ONE
			select LOCATRECV,TMBILL,convert(varchar(8),TMBILDT,112) as TMBILDT,CONTNO,LOCATPAY,PAYFOR,PAYTYP,PAYAMT,DISCT
			,NPAYINT,NETPAY,USERID,INPDT,convert(varchar(8),INPTIME,112) as INPTIME_D,convert(varchar(8),INPTIME,114) as INPTIME_T
			,FLAG,NAME,BAL,BILLCOLL,FINNAME,PAYINDT,CHQDT,BILLNO
			into #ONE
			FROM(
				select A.LOCATRECV,A.TMBILL,A.TMBILDT,A.CONTNO,A.LOCATPAY,A.PAYFOR,A.PAYTYP,A.PAYAMT,A.DISCT
				,(A.PAYINT-A.DSCINT) as NPAYINT,A.NETPAY,A.USERID,A.INPDT,A.INPTIME,A.FLAG,b.snam+RTrim(b.NAME1)+''+RTrim(b.NAME2) as NAME
				,XX.TOTPRC-XX.SMPAY as BAL,XX.BILLCOLL,F.FINNAME,S.PAYINDT,A.CHQDT,S.BILLNO from {$this->MAuth->getdb('CHQMAS')} S

				left join {$this->MAuth->getdb('CHQTRAN')} A on A.TMBILL = S.TMBILL and A.LOCATRECV = S.LOCATRECV
				left join {$this->MAuth->getdb('CUSTMAST')} B on A.CUSCOD = B.CUSCOD
				left join (select CONTNO,STRNO,LOCAT,BILLCOLL,TOTPRC,SMPAY,'H' as TSALE,'' as FINCOD from {$this->MAuth->getdb('ARMAST')} where CONTNO in 
				(select CONTNO from {$this->MAuth->getdb('CHQTRAN')} where LOCATRECV like '%".$LOCATRECV."%' and ".$MDT." BETWEEN '".$DATE1."' and '".$DATE2."')
				union 
				select CONTNO,STRNO,LOCAT,'''' as BILLCOLL,TOTPRC,SMPAY,'C' as TSALE,'''' as FINCOD from {$this->MAuth->getdb('ARCRED')}  
				where CONTNO in (select CONTNO from {$this->MAuth->getdb('CHQTRAN')} where LOCATRECV like '%".$LOCATRECV."%' and ".$MDT." 
				BETWEEN '".$DATE1."' and '".$DATE2."')  
				union
				select CONTNO,STRNO,LOCAT,'''' as BILLCOLL,TOTPRC,SMPAY,'F' as TSALE,FINCOD 
				from {$this->MAuth->getdb('ARFINC')} where CONTNO IN (select CONTNO from {$this->MAuth->getdb('CHQTRAN')} 
				where LOCATRECV LIKE '%".$LOCATRECV."%' and ".$MDT." BETWEEN 
				'".$DATE1."' and '".$DATE2."')  
				union
				select CONTNO,'''' as STRNO,LOCAT,'''' as BILLCOLL,TOTPRC,SMPAY,'A' as TSALE,'''' as FINCOD from {$this->MAuth->getdb('AR_INVOI')}  
				where CONTNO IN (select CONTNO from {$this->MAuth->getdb('CHQTRAN')} where LOCATRECV LIKE '%".$LOCATRECV."%' 
				AND ".$MDT." BETWEEN '".$DATE1."' and '".$DATE2."')
				union
				select CONTNO,'''' as STRNO,LOCAT,'''' as BILLCOLL,OPTPTOT as TOTPRC,SMPAY,'O' as TSALE,'''' as FINCOD 
				from {$this->MAuth->getdb('AROPTMST')} where CONTNO IN (select CONTNO from {$this->MAuth->getdb('CHQTRAN')} 
				where LOCATRECV like '%".$LOCATRECV."%' AND ".$MDT." BETWEEN '".$DATE1."' and '".$DATE2."')
				union
				select CONTNO,STRNO,LOCAT,BILLCOLL,TOTPRC,SMPAY,'H' as TSALE,'''' as FINCOD from {$this->MAuth->getdb('HARMAST')}  
				where CONTNO in (select CONTNO from {$this->MAuth->getdb('CHQTRAN')} where LOCATRECV like '%".$LOCATRECV."%' 
				and ".$MDT." BETWEEN '".$DATE1."' and '".$DATE2."') 
				union
				select CONTNO,STRNO,LOCAT,'''' as BILLCOLL,TOTPRC,SMPAY,'C' as TSALE,'''' as FINCOD from {$this->MAuth->getdb('HARCRED')} 
				where CONTNO in (select CONTNO from {$this->MAuth->getdb('CHQTRAN')} where LOCATRECV like '%".$LOCATRECV."%' 
				and ".$MDT." BETWEEN '".$DATE1."' and '".$DATE2."') 
				union
				select CONTNO,STRNO,LOCAT,'''' as BILLCOLL,TOTPRC,SMPAY,'F' as TSALE,FINCOD from {$this->MAuth->getdb('HARFINC')}  
				where CONTNO IN (select CONTNO from {$this->MAuth->getdb('CHQTRAN')} where LOCATRECV LIKE '%".$LOCATRECV."%' 
				and ".$MDT." BETWEEN '".$DATE2."' and '".$DATE1."')
				union
				select CONTNO,'''' as STRNO,LOCAT,'''' as BILLCOLL,TOTPRC,SMPAY,'A' as TSALE,'''' as FINCOD from {$this->MAuth->getdb('HAR_INVO')}  
				where CONTNO IN (select CONTNO from {$this->MAuth->getdb('CHQTRAN')} where LOCATRECV like '%".$LOCATRECV."%' 
				and ".$MDT." BETWEEN '".$DATE1."' and '".$DATE2."') 
				union
				select CONTNO,'''' as STRNO,LOCAT,'''' as BILLCOLL,OPTPTOT as TOTPRC,SMPAY,'O' as TSALE,'''' as FINCOD 
				from {$this->MAuth->getdb('HAROPMST')} where CONTNO in (select CONTNO from {$this->MAuth->getdb('CHQTRAN')} 
				where LOCATRECV like '%".$LOCATRECV."%' and ".$MDT." BETWEEN '".$DATE1."' and '".$DATE2."')) as XX  on XX.CONTNO = A.CONTNO and XX.LOCAT = A.LOCATPAY  
				left join {$this->MAuth->getdb('FINMAST')} F on XX.FINCOD = F.FINCODE and (A.PAYFOR = '004' or A.PAYFOR = '011') 
				left join {$this->MAuth->getdb('INVTRAN')} G on XX.STRNO = G.STRNO  where  A.LOCATRECV like '%".$LOCATRECV."%' and A.LOCATPAY like '%".$LOCATPAY."%' 
				and ".$MDT1." BETWEEN '".$DATE1."' and '".$DATE2."' and  A.PAYTYP like '%".$PAYTYP."%' and A.PAYFOR like '%".$PAYFOR."%' and (A.USERID like '%".$USERID."%')  
				and (A.TMBILL in (select TMBILL from {$this->MAuth->getdb('CHQTRAN')} T where (LOCATPAY like '%".$LOCATPAY."%') and (PAYFOR like '%".$PAYFOR."%'))) and  B.GROUP1 
				like '%".$GROUP1."%' --order by A.TMBILL  
			)ONE
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$SET = "";
		if($sort == "bi1"){
			$SET = "TMBILDT";
		}else if($sort == "d1"){
			$SET = "CHQDT,TMBILL";
		}else if($sort == "cont"){
			$SET = "CONTNO,TMBILDT";
		}else if($sort == "bi2"){
			$SET = "BILLNO";
		}else if($sort == "d2"){
			$SET = "PAYINDT,TMBILL";
		}else if($sort == "d3"){
			$SET = "TMBILDT,TMBILL";
		}else if($sort == "locat"){
			$SET = "LOCATRECV,TMBILL";
		}
		$SCRT = "";
		if($sort == "bi1"){
			$SCRT = "ใบรับเงินชั่วคราว ";
		}else if($sort == "d1"){
			$SCRT = "วันที่เช็ค ";
		}else if($sort == "cont"){
			$SCRT = "เลขที่สัญญา ";
		}else if($sort == "bi2"){
			$SCRT = "ใบเสร็จรับเงิน ";
		}else if($sort == "d2"){
			$SCRT = "วันที่นำฝาก ";
		}else if($sort == "d3"){
			$SCRT = "วันที่ใบรับ ";
		}else if($sort == "locat"){
			$SCRT = "สาขาที่ได้รับ ";
		}
		
		$sql = "
			select * from #ONE order by ".$SET."
		";
		$query = $this->db->query($sql);
		
		$sql = "
			select COUNT(PAYAMT) as countorder,SUM(PAYAMT) as Total,sum(DISCT) as DISCT
			,sum(NPAYINT) as NPAYINT,sum(NETPAY) as NETPAY from #ONE
		";
		$query1 = $this->db->query($sql);
		
		$sql = "
			select COUNT(PAYAMT) as countorder,SUM(PAYAMT) as Total,sum(DISCT) as DISCT
			,sum(NPAYINT)as NPAYINT,sum(NETPAY) as NETPAY from #ONE where FLAG = 'C'
		";
		$query2 = $this->db->query($sql);
		$sql = "
			declare @A1 decimal(12) = (select coalesce(COUNT(PAYAMT),0) from #ONE); 
			declare @B1 decimal(12) = (select coalesce(COUNT(PAYAMT),0) from #ONE where FLAG = 'C'); 
			declare @A2 decimal(12,2) = (select coalesce(SUM(PAYAMT),0) from #ONE); 
			declare @B2 decimal(12,2) = (select coalesce(SUM(PAYAMT),0) from #ONE where FLAG = 'C'); 
			declare @A3 decimal(12,2) = (select coalesce(SUM(DISCT),0) from #ONE); 
			declare @B3 decimal(12,2) = (select coalesce(SUM(DISCT),0) from #ONE where FLAG = 'C'); 
			declare @A4 decimal(12,2) = (select coalesce(SUM(NPAYINT),0) from #ONE); 
			declare @B4 decimal(12,2) = (select coalesce(SUM(NPAYINT),0) from #ONE where FLAG = 'C'); 
			declare @A5 decimal(12,2) = (select coalesce(SUM(NETPAY),0) from #ONE); 
			declare @B5 decimal(12,2) = (select coalesce(SUM(NETPAY),0) from #ONE where FLAG = 'C'); 

			select @A1-@B1 as RESULT,@A2-@B2 as PAYAMT,@A3-@B3 as DISCT,@A4-@B4 as NPAYINT,@A5-@B5 as NETPAY
		";
		$query3 = $this->db->query($sql);
		$sql = "
			select top 20 PAYCODE,PAYDESC from {$this->MAuth->getdb('PAYTYP')} 
			where PAYCODE = '".$PAYTYP."'
		";
		$PAYDESC = "";
		$query4 = $this->db->query($sql);
		if($query4->row()){
			foreach($query4->result() as $row){
				$PAYDESC = $row->PAYDESC;
			}
		}
		$sql = "
			select top 20 FORCODE,FORDESC from {$this->MAuth->getdb('PAYFOR')} 
			where FORCODE = '".$PAYFOR."'
		";
		$FORDESC = "";
		$query5 = $this->db->query($sql);
		if($query5->row()){
			foreach($query5->result() as $row){
				$FORDESC = $row->FORDESC;
			}
		}
		$sql = "
			select USERID,USERNAME from {$this->MAuth->getdb('PASSWRD')} 
			where USERID = '".$USERID."'
		";
		//echo $sql; exit;
		$USERNAME = "";
		$query6 = $this->db->query($sql);
		if($query6->row()){
			foreach($query6->result() as $row){
				$USERNAME = $row->USERNAME;
			}
		}
		$sql = "
			select ARGCOD,ARGDES from {$this->MAuth->getdb('ARGROUP')}
            where ARGCOD = '".$GROUP1."' 
		";
		$ARGDES = "";
		$query7 = $this->db->query($sql);
		if($query7->row()){
			foreach($query7->result() as $row){
				$ARGDES = $row->ARGDES;
			}
		}
		$head = ""; $html = ""; 
		$head = "
			<tr class='wm'>
				<td class='wf pd' style='height:1px;border-top:0.1px solid black;' colspan='16'></td>
			</tr>
			<tr>
				<th style='border-bottom:0.1px solid black;text-align:left;'>สาขา</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>เลขใบรับเงิน<br>ชั่วคราว</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>วันที่รับชำระ</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>เลขที่สัญญา</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>ชื่อ-นามสกุล</th>			
				<th style='border-bottom:0.1px solid black;text-align:left;'>ชำระโดย</th> 
				<th style='border-bottom:0.1px solid black;text-align:left;'>ชำระค่า</th> 
				<th style='border-bottom:0.1px solid black;text-align:right;'>ยอดหักลูกหนี้</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ส่วนลด</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ชำระเบี้ยปรับ</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ยอดรับสุทธิ</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>พนง. <br>เก็บเงิน</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ล/น เหลือ<br>ณ วันพิมพ์</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>UserID</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>วันเวลาบันทึก</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'></th>
			</tr>
			<tr>
			</tr>
		";
		$status = "";
		if($query->row()){
			foreach($query->result() as $row){	
				if($row->FLAG == "C"){
					$status = "*ยกเลิก*";
				}else{
					$status = "";
				}
				$html .= "
					<tr class='trow'>
						<td style='width:50px;text-align:left;'>".$row->LOCATRECV."</td>
						<td style='width:80px;text-align:left;'>".$row->TMBILL."</td>
						<td style='width:50px;text-align:left;'>".$this->Convertdate(2,$row->TMBILDT)."</td>
						<td style='width:80spx;text-align:left;'>".$row->CONTNO."</td>
						<td style='width:150px;text-align:left;'>".$row->NAME."</td>
						<td style='width:50px;text-align:left;'>".$row->PAYTYP."</td>
						<td style='width:50px;text-align:left;'>".$row->PAYFOR."</td>
						<td style='width:50px;text-align:right;'>".number_format($row->PAYAMT,2)."</td>
						<td style='width:50px;text-align:right;'>".number_format($row->DISCT,2)."</td>
						<td style='width:50px;text-align:right;'>".number_format($row->NPAYINT,2)."</td>
						<td style='width:50px;text-align:right;'>".number_format($row->NETPAY,2)."</td>
						<td style='width:50px;text-align:right;'>".$row->BILLCOLL."</td>
						<td style='width:50px;text-align:right;'>".number_format($row->BAL,2)."</td>
						<td style='width:50px;text-align:right;'>".$row->USERID."</td>
						<td style='width:240px;text-align:right;'>".$this->Convertdate(2,$row->INPTIME_D)." ".$row->INPTIME_T."</td>
						<td style='width:50px;text-align:right;'>".$status."</td>
					</tr>
				";	
			}
		}
		if($query1->row()){
			foreach($query1->result() as $row){
				$html .="
					<tr class='trow'>
						<th align='right' colspan='3'>รวมทั้งสิ้น</th>
						<td align='right'>".$row->countorder."&nbsp;&nbsp;<b>รายการ</b></td>
						<td align='right' colspan='4'>".number_format($row->Total,2)."</td>
						<td align='right'>".number_format($row->DISCT,2)."</td>
						<td align='right'>".number_format($row->NPAYINT,2)."</td>
						<td align='right'>".number_format($row->NETPAY,2)."</td>
					</tr>
				";
			}
		}
		if($query2->row()){
			foreach($query2->result() as $row){
				$html .="
					<tr class='trow'>
						<th align='right' colspan='3'>รายการที่ยกเลิก</th>
						<td align='right'>".$row->countorder."&nbsp;&nbsp;<b>รายการ</b></td>
						<td align='right' colspan='4'>".number_format($row->Total,2)."</td>
						<td align='right'>".number_format($row->DISCT,2)."</td>
						<td align='right'>".number_format($row->NPAYINT,2)."</td>
						<td align='right'>".number_format($row->NETPAY,2)."</td>
					</tr>
				";
			}
		}
		if($query3->row()){
			foreach($query3->result() as $row){
				$html .="
					<tr class='trow'>
						<th align='right' colspan='3'>รายการรับสุทธิ</th>
						<td align='right'>".$row->RESULT."<b>&nbsp;&nbsp;รายการ</b></td>
						<td align='right' colspan='4'>".number_format($row->PAYAMT,2)."</td>
						<td align='right'>".number_format($row->DISCT,2)."</td>
						<td align='right'>".number_format($row->NPAYINT,2)."</td>
						<td align='right'>".number_format($row->NETPAY,2)."</td>
					</tr>
				";
			}
		}
		$mpdf = new \Mpdf\Mpdf([
			'mode' => 'utf-8', 
			'format' =>'A4-L',
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
						<th colspan='16' style='font-size:10pt;'>บริษัท ตั้งใจพัฒนายานยนต์ จำกัด</th>
					</tr>
					<tr>
						<th colspan='16' style='font-size:9pt;'>รายงานการรับชำระตามวันที่ใบรับ</th>
					</tr>
					<tr>
						<td style='text-align:center;' colspan='16'>
							<b>สาขาที่รับชำระ</b> &nbsp;&nbsp;".$LOCATRECV."&nbsp;&nbsp;
							<b>ชำระเพื่อ บ/ช สาขา</b>&nbsp;&nbsp;".$LOCATPAY."&nbsp;&nbsp;
							<b>จากวันที่</b>&nbsp;&nbsp;".$this->Convertdate(2,$DATE1)."&nbsp;&nbsp;
							<b>ถึงวันที่</b>&nbsp;&nbsp;".$this->Convertdate(2,$DATE2)."&nbsp;&nbsp;
						</td>
					</tr>
					<tr>
						<td style='text-align:left;' colspan='3'><b>Scrt By :</b>&nbsp;&nbsp;".$SCRT."</td>
						<td style='text-align:center;' colspan='10'>
							<b>ชำระโดย</b>&nbsp;&nbsp;".$PAYDESC."&nbsp;&nbsp;
							<b>ชำระค่า</b>&nbsp;&nbsp;".$FORDESC."&nbsp;&nbsp;
							<b>พนักงานบันทึกข้อมูล</b>&nbsp;&nbsp;".$USERNAME."&nbsp;&nbsp;
							<b>กลุ่มลูกค้า</b>&nbsp;&nbsp;".$ARGDES."&nbsp;&nbsp;
						</td>
						<td style='text-align:right;' colspan='3'>RpRec13</td>
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
			<div class='wf pf' style='top:1060;left:600;top:715;left:880; font-size:6pt;'>วันที่พิมพ์รายงาน : ".date('d/m/').(date('Y')+543)." ".date('H:i')." หน้า {PAGENO} / {nbpg}</div>
		";
		$mpdf->SetHTMLHeader($head);	
		$mpdf->WriteHTML($content);	
		$mpdf->Output();
	}
	function pdfall(){
		$data = array();
		$data[] = $_GET["condpdf"];
		$arrs = $this->generateData($data,"decode");
		$arrs[0] = urldecode($arrs[0]);
		
		$tx = explode("||",$arrs[0]);
		$LOCATRECV 	= $tx[0];
		$LOCATPAY 	= $tx[1];
		$DATE1 	    = $this->Convertdate(1,$tx[2]);
		$DATE2 		= $this->Convertdate(1,$tx[3]);
		$PAYTYP 	= $tx[4];
		$PAYFOR 	= $tx[5];
		$USERID 	= $tx[6];
		$GROUP1 	= $tx[7];
		$GCODE 		= $tx[8];
		$CODE 	    = $tx[9];
		$report 	= $tx[10];
		$dt 		= $tx[11];
		$sort 	    = $tx[12];
		
		$MDT = "";
		if($dt == "tdt"){
			$MDT = "TMBILDT";
		}else{
			$MDT = "PAYDT";
		}
		$MDT1 = "";
		if($dt == "tdt"){
			$MDT1 = "A.TMBILDT";
		}else{
			$MDT1 = "A.PAYDT";
		}
		$sql = "
			IF OBJECT_ID('tempdb..#ALL') IS NOT NULL DROP TABLE #ALL --,b.snam+RTrim(b.NAME1)+''''+RTrim(b.NAME2) as NAME
			select TMBILL,convert(varchar(8),TMBILDT,112) as TMBILDT,BILLNO,convert(varchar(8),BILLDT,112) as BILLDT,PAYTYP
				,CHQNO,CHQBK,CHQAMT,USERID,convert(varchar(8),INPDT,112) as INPDT,INPTIME,TSALE
				,LOCATRECV,LOCATPAY,CONTNO,PAYAMT,FORDESC,REFNO,PAYAMT_N+PAYAMT_V as PAYAMT_NV,PAYAMT_N,PAYAMT_V,DISCT,PAYFOR,INT
				,NETPAY,FLAG,SNAM+RTrim(NAME1)+' '+RTrim(NAME2) as NAM,BILLCOLL,TOTPRC,SMPAY,FINNAME,XBAL,CHQDT,PAYINDT
			into #ALL
			FROM(
				select A.TMBILL,A.TMBILDT,A.BILLNO,A.BILLDT,A.PAYTYP,A.CHQNO,A.CHQBK,A.CHQAMT,A.USERID,A.INPDT,A.INPTIME,B.TSALE
				,A.LOCATRECV,B.LOCATPAY,B.CONTNO,B.PAYAMT,D.FORDESC,A.REFNO,B.PAYAMT_N,B.PAYAMT_V,B.DISCT,B.PAYFOR,B.PAYINT-B.DSCINT as INT
				,B.NETPAY,B.FLAG,C.SNAM,C.NAME1,C.NAME2,E.BILLCOLL,TOTPRC,E.SMPAY,F.FINNAME,E.TOTPRC-E.SMPAY as XBAL,A.CHQDT,A.PAYINDT
				from {$this->MAuth->getdb('CHQMAS')} A
				left join {$this->MAuth->getdb('CHQTRAN')} B on A.TMBILL = B.TMBILL and A.LOCATRECV = B.LOCATRECV
				left join {$this->MAuth->getdb('CUSTMAST')} C on A.CUSCOD = C.CUSCOD 
				left join {$this->MAuth->getdb('PAYFOR')} D on B.PAYFOR = D.FORCODE
				left join (select CONTNO,LOCAT,BILLCOLL,TOTPRC,SMPAY,'H' as TSALE,'''' as FINCOD from {$this->MAuth->getdb('ARMAST')}
				where CONTNO in (select CONTNO from {$this->MAuth->getdb('CHQTRAN')} where LOCATRECV like '%".$LOCATRECV."%' and ".$MDT." 
				BETWEEN '".$DATE1."' and '".$DATE2."')
				union 
				select CONTNO,LOCAT,'''' as BILLCOLL,TOTPRC,SMPAY,'C' AS TSALE,'''' 
				as FINCOD from {$this->MAuth->getdb('ARCRED')}  where CONTNO in (select CONTNO from {$this->MAuth->getdb('CHQTRAN')} 
				where LOCATRECV LIKE '%".$LOCATRECV."%' AND ".$MDT." BETWEEN '".$DATE1."' AND '".$DATE2."')  
				union 
				select CONTNO,LOCAT,'''' as BILLCOLL,TOTPRC,SMPAY,'F' as TSALE,FINCOD from {$this->MAuth->getdb('ARFINC')} where CONTNO in 
				(select CONTNO from {$this->MAuth->getdb('CHQTRAN')} where LOCATRECV like '%".$LOCATRECV."%' and ".$MDT." 
				BETWEEN '".$DATE1."' and '".$DATE2."')  
				union 
				select CONTNO,LOCAT,'''' as BILLCOLL,TOTPRC,SMPAY,'A' as TSALE,'''' as FINCOD from {$this->MAuth->getdb('AR_INVOI')}  
				where CONTNO in (select CONTNO from {$this->MAuth->getdb('CHQTRAN')} where LOCATRECV like '%".$LOCATRECV."%' and ".$MDT." 
				BETWEEN '".$DATE1."' and '".$DATE2."')  
				union 
				select CONTNO,LOCAT,'''' as BILLCOLL,OPTPTOT as TOTPRC,SMPAY,'O' as TSALE,'''' as FINCOD from {$this->MAuth->getdb('AROPTMST')}  
				where CONTNO in (select CONTNO from {$this->MAuth->getdb('CHQTRAN')} where LOCATRECV like '%".$LOCATRECV."%' and ".$MDT." 
				BETWEEN '".$DATE1."' and '".$DATE2."')  
				union 
				select CONTNO,LOCAT,BILLCOLL,TOTPRC,SMPAY,'H' as TSALE,'''' as FINCOD from {$this->MAuth->getdb('HARMAST')}  where CONTNO in 
				(select CONTNO from {$this->MAuth->getdb('CHQTRAN')} where LOCATRECV like '%".$LOCATRECV."%' and ".$MDT." 
				BETWEEN '".$DATE1."' and '".$DATE2."')  
				union 
				select CONTNO,LOCAT,'''' as BILLCOLL,TOTPRC,SMPAY,'C' as TSALE,'''' as FINCOD from {$this->MAuth->getdb('HARCRED')}  
				where CONTNO in (select CONTNO from {$this->MAuth->getdb('CHQTRAN')} where LOCATRECV like '%".$LOCATRECV."%' and ".$MDT." 
				BETWEEN '".$DATE1."' and '".$DATE2."')  
				union 
				select CONTNO,LOCAT,'''' as BILLCOLL,TOTPRC,SMPAY,'F' as TSALE,FINCOD from {$this->MAuth->getdb('HARFINC')}  
				where CONTNO in (select CONTNO from {$this->MAuth->getdb('CHQTRAN')} where LOCATRECV like '%".$LOCATRECV."%' and ".$MDT." 
				BETWEEN '".$DATE1."' and '".$DATE2."')  
				union 
				select CONTNO,LOCAT,'''' as BILLCOLL,TOTPRC,SMPAY,'A' as TSALE,'''' as FINCOD from {$this->MAuth->getdb('HAR_INVO')}  
			    where CONTNO in (select CONTNO from {$this->MAuth->getdb('CHQTRAN')} where LOCATRECV like '%".$LOCATRECV."%' AND ".$MDT." 
				BETWEEN '".$DATE2."' and '".$DATE2."')  
				union 
				select CONTNO,LOCAT,'''' as BILLCOLL,OPTPTOT as TOTPRC,SMPAY,'O' as TSALE,'''' as FINCOD from {$this->MAuth->getdb('HAROPMST')}  
				where CONTNO in (select CONTNO from {$this->MAuth->getdb('CHQTRAN')} where LOCATRECV like '%".$LOCATRECV."%' and ".$MDT." 
				BETWEEN '".$DATE1."' and '".$DATE2."')) as E on B.CONTNO = E.CONTNO and B.LOCATPAY = E.LOCAT  
				left join {$this->MAuth->getdb('FINMAST')} F on E.FINCOD = F.FINCODE and (B.PAYFOR = '004' or B.PAYFOR='011') where 
				(C.GROUP1 like '%".$GROUP1."%' or C.GROUP1 is null) and (A.LOCATRECV like '%".$LOCATRECV."%') 
				and (".$MDT1." BETWEEN '".$DATE1."' and '".$DATE2."') and  (A.PAYTYP like '%".$PAYTYP."%') and (A.USERID like '%".$USERID."%')  
				and (A.TMBILL in (select TMBILL from {$this->MAuth->getdb('CHQTRAN')} T where (LOCATPAY like '%".$LOCATPAY."%') and (PAYFOR like '%".$PAYFOR."%'))) --order by A.TMBILDT
			)A
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$SET = "";
		if($sort == "bi1"){
			$SET = "TMBILDT";
		}else if($sort == "d1"){
			$SET = "CHQDT";
		}else if($sort == "cont"){
			$SET = "CONTNO";
		}else if($sort == "bi2"){
			$SET = "BILLNO";
		}else if($sort == "d2"){
			$SET = "PAYINDT";
		}else if($sort == "d3"){
			$SET = "TMBILDT";
		}else if($sort == "locat"){
			$SET = "LOCATRECV";
		
		}
		$SCRT = "";
		if($sort == "bi1"){
			$SCRT = "ใบรับเงินชั่วคราว ";
		}else if($sort == "d1"){
			$SCRT = "วันที่เช็ค ";
		}else if($sort == "cont"){
			$SCRT = "เลขที่สัญญา ";
		}else if($sort == "bi2"){
			$SCRT = "ใบเสร็จรับเงิน ";
		}else if($sort == "d2"){
			$SCRT = "วันที่นำฝาก ";
		}else if($sort == "d3"){
			$SCRT = "วันที่ใบรับ ";
		}else if($sort == "locat"){
			$SCRT = "สาขาที่ได้รับ ";
		}
		$sql = "select * from #ALL order by ".$SET."";
		$query = $this->db->query($sql);
		
		$sql = "
			select COUNT(PAYAMT) as countPAYAMT,SUM(PAYAMT) as sumPAYAMT,SUM(PAYAMT_V) as sumPAYAMT_V
			,SUM(DISCT) as sumDISCT,SUM(INT) as sumINT,SUM(NETPAY) as sumNETPAY from #ALL
		";
		$query1 = $this->db->query($sql);
		$sql = "
			select COUNT(PAYAMT) as countPAYAMT,SUM(PAYAMT) as sumPAYAMT,SUM(PAYAMT_V) as sumPAYAMT_V
			,SUM(DISCT) as sumDISCT,SUM(INT) as sumINT,SUM(NETPAY) as sumNETPAY from #ALL where FLAG = 'C'
		";
		$query2 = $this->db->query($sql);
		$sql = "
			declare @A1 decimal(12,2) = (select coalesce(SUM(PAYAMT),0) from #ALL); 
			declare @B1 decimal(12,2) = (select coalesce(SUM(PAYAMT),0) from #ALL where FLAG = 'C'); 
			declare @A2 decimal(12,2) = (select coalesce(SUM(PAYAMT_V),0) from #ALL); 
			declare @B2 decimal(12,2) = (select coalesce(SUM(PAYAMT_V),0) from #ALL where FLAG = 'C'); 
			declare @A3 decimal(12,2) = (select coalesce(SUM(DISCT),0) from #ALL); 
			declare @B3 decimal(12,2) = (select coalesce(SUM(DISCT),0) from #ALL where FLAG = 'C'); 
			declare @A4 decimal(12,2) = (select coalesce(SUM(INT),0) from #ALL); 
			declare @B4 decimal(12,2) = (select coalesce(SUM(INT),0) from #ALL where FLAG = 'C'); 
			declare @A5 decimal(12,2) = (select coalesce(SUM(NETPAY),0) from #ALL); 
			declare @B5 decimal(12,2) = (select coalesce(SUM(NETPAY),0) from #ALL where FLAG = 'C'); 

			select @A1-@B1 as RESULT,@A2-@B2 as PAYAMT,@A3-@B3 as DISCT,@A4-@B4 as INT,@A5-@B5 as NETPAY
		";
		$query3 = $this->db->query($sql);
		$sql = "
			select top 20 PAYCODE,PAYDESC from {$this->MAuth->getdb('PAYTYP')} 
			where PAYCODE = '".$PAYTYP."'
		";
		$PAYDESC = "";
		$query4 = $this->db->query($sql);
		if($query4->row()){
			foreach($query4->result() as $row){
				$PAYDESC = $row->PAYDESC;
			}
		}
		$sql = "
			select top 20 FORCODE,FORDESC from {$this->MAuth->getdb('PAYFOR')} 
			where FORCODE = '".$PAYFOR."'
		";
		$FORDESC = "";
		$query5 = $this->db->query($sql);
		if($query5->row()){
			foreach($query5->result() as $row){
				$FORDESC = $row->FORDESC;
			}
		}
		$sql = "
			select USERID,USERNAME from {$this->MAuth->getdb('PASSWRD')} 
			where USERID = '".$USERID."'
		";
		//echo $sql; exit;
		$USERNAME = "";
		$query6 = $this->db->query($sql);
		if($query6->row()){
			foreach($query6->result() as $row){
				$USERNAME = $row->USERNAME;
			}
		}
		$sql = "
			select ARGCOD,ARGDES from {$this->MAuth->getdb('ARGROUP')}
            where ARGCOD = '".$GROUP1."' 
		";
		$ARGDES = "";
		$query7 = $this->db->query($sql);
		if($query7->row()){
			foreach($query7->result() as $row){
				$ARGDES = $row->ARGDES;
			}
		}
		$head = ""; $html = ""; 
		$head = "
			<tr class='wm'>
				<td class='wf' style='height:1px;border-top:0.1px solid black;' colspan='13'></td>
			</tr>
			<tr>
				<th style='border-bottom:0.1px solid black;text-align:left;'>สาขา</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>เลขใบรับเงินชั่วคราว<br>เลขที่สัญญา</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>วันที่รับชำระ<br>ชื่อ-นามสกุล</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>เลขที่ใบเสร็จรับเงิน</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>วันที่ใบเสร็จรับเงิน<br>ชำระค่า</th> 
				<th style='border-bottom:0.1px solid black;text-align:left;'>ชำโดย</th> 
				<th style='border-bottom:0.1px solid black;text-align:left;'>เลขที่เช็ค<br>Billcoll</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'><br>ยอกหักลูกหนี้</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>เช็คธนาคาร<br>ภาษีมูลค่าเพิ่ม</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>จำนวนเงินทั้งหมด<br>ส่วนลด</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>UserID<br>ชำระเบี้ยปรับ</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>วันเวลาบันทึก<br>ยอดรับสุทธิ</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>Refno<br>ล/นเหลือ ณ วันพิมพ์</th>
			</tr>
			<tr>
				<td class='wf' style='height:1px;border-top:0.1px solid black;' colspan='13'></td>
			</tr>
		";
		$status = "";
		if($query->row()){
			foreach($query->result() as $row){
				if($row->FLAG == "C"){
					$status = "*ยกเลิก*";
				}else{
					$status = "";
				}
				$html .= "
					<tr>
						<th style='width:40px;text-align:left;'>".$row->LOCATRECV."</th>
						<th style='width:40px;text-align:left;'>".$row->TMBILL."<br>".$row->CONTNO."</th>
						<th style='width:400px;text-align:left;'>".$this->Convertdate(2,$row->TMBILDT)."<br>".$row->NAM."</th>
						<th style='width:10px;text-align:left;'>".$row->BILLNO."</th>
						<th style='width:300px;text-align:left;'>".$this->Convertdate(2,$row->BILLDT)."<br>".$row->FORDESC."</th> 
						<th style='width:40px;text-align:left;'>".$row->PAYTYP."</th> 
						<th style='width:80px;text-align:left;'>".$row->CHQNO."<br>".$row->BILLCOLL."</th>
						<th style='width:40px;text-align:right;'><br>".number_format($row->PAYAMT,2)."</th>
						<th style='width:40px;text-align:right;'>".$row->CHQBK."<br>".number_format($row->PAYAMT_V,2)."</th>
						<th style='width:40px;text-align:right;'>".number_format($row->CHQAMT,2)."<br>".number_format($row->DISCT,2)."</th>
						<th style='width:40px;text-align:right;'>".$row->USERID."<br>".number_format($row->DISCT,2)."</th>
						<th style='width:80px;text-align:right;'>".$this->Convertdate(2,$row->INPDT)."<br>".$row->NETPAY."</th>
						<th style='width:40px;text-align:right;'>".$status."<br>".number_format($row->XBAL,2)."</th>
					</tr>
				";
			}
		}
		$total = "";
		if($query1->row()){
			foreach($query1->result() as $row){
				$html .= "
					<tr>
						<td class='wf' style='height:1px;border-top:0.1px solid black;' colspan='13'></td>
					</tr>
					<tr>
						<th style='text-align:right;' colspan='2'>รวมใบเสร็จทั้งสิ้น</th>
						<td style='text-align:right;'>".$row->countPAYAMT."</td>
						<th>รายการ</th>
						<th colspan='2'>ยอดรวมทั้งสิ้น</th>
						<td style='text-align:right;' colspan='2'>".number_format($row->sumPAYAMT,2)."</th>
						<td style='text-align:right;'>".number_format($row->sumPAYAMT_V,2)."</th>
						<td style='text-align:right;'>".number_format($row->sumDISCT,2)."</th>
						<td style='text-align:right;'>".number_format($row->sumINT,2)."</th>
						<td style='text-align:right;'>".number_format($row->sumNETPAY,2)."</th>
					</tr>
				";
				$total = $row->countPAYAMT;
			}
		}
		if($query2->row()){
			foreach($query2->result() as $row){
				$html .= "
					<tr>
						<th style='text-align:right;' colspan='2'>รวมทั้งสิ้น</th>
						<td style='text-align:right;'>".$total."</td>
						<th>รายการ</th>
						<th colspan='2'>ยอดรวมรายการยกเลิก</th>
						<td style='text-align:right;' colspan='2'>".number_format($row->sumPAYAMT,2)."</th>
						<td style='text-align:right;'>".number_format($row->sumPAYAMT_V,2)."</th>
						<td style='text-align:right;'>".number_format($row->sumDISCT,2)."</th>
						<td style='text-align:right;'>".number_format($row->sumINT,2)."</th>
						<td style='text-align:right;'>".number_format($row->sumNETPAY,2)."</th>
					</tr>
				";
			}
		}
		if($query3->row()){
			foreach($query3->result() as $row){
				$html .= "
					<tr>
						<th style='text-align:right;' colspan='2'></th>
						<td style='text-align:right;'></td>
						<th></th>
						<th colspan='2'>ยอดรวมสุทธิ</th>
						<td style='text-align:right;' colspan='2'>".number_format($row->RESULT,2)."</th>
						<td style='text-align:right;'>".number_format($row->PAYAMT,2)."</th>
						<td style='text-align:right;'>".number_format($row->DISCT,2)."</th>
						<td style='text-align:right;'>".number_format($row->INT,2)."</th>
						<td style='text-align:right;'>".number_format($row->NETPAY,2)."</th>
					</tr>
					<tr>
						<td class='wf' style='height:1px;border-top:0.1px solid black;' colspan='13'></td>
					</tr>
				";
			}
		}
		$mpdf = new \Mpdf\Mpdf([
			'mode' => 'utf-8', 
			'format' =>'A4-L',
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
						<th colspan='13' style='font-size:10pt;text-align:center;'>บริษัท ตั้งใจพัฒนายานยนต์ จำกัด</th>
					</tr>
					<tr>
						<th colspan='13' style='font-size:9pt;'>รายงานการรับชำระตามวันที่ใบรับ</th>
					</tr>
					<tr>
						<td style='text-align:center;' colspan='13'>
							<b>สาขาที่รับชำระ</b> &nbsp;&nbsp;".$LOCATRECV."&nbsp;&nbsp;
							<b>ชำระเพื่อ บ/ช สาขา</b>&nbsp;&nbsp;".$LOCATPAY."&nbsp;&nbsp;
							<b>จากวันที่</b>&nbsp;&nbsp;".$this->Convertdate(2,$DATE1)."&nbsp;&nbsp;
							<b>ถึงวันที่</b>&nbsp;&nbsp;".$this->Convertdate(2,$DATE2)."&nbsp;&nbsp;
						</td>
					</tr>
					<tr>
						<td style='text-align:left;' colspan='2'><b>Scrt By :</b>&nbsp;&nbsp;".$SCRT."</th>
						<td style='text-align:center;' colspan='9'>
							<b>ชำระโดย</b>&nbsp;&nbsp;".$PAYDESC."&nbsp;&nbsp;
							<b>ชำระค่า</b>&nbsp;&nbsp;".$FORDESC."&nbsp;&nbsp;
							<b>พนักงานบันทึกข้อมูล</b>&nbsp;&nbsp;".$USERNAME."&nbsp;&nbsp;
							<b>กลุ่มลูกค้า</b>&nbsp;&nbsp;".$ARGDES."&nbsp;&nbsp;
						</td>
						<td style='text-align:right;' colspan='2'>RpRec11</td>
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
			<div class='wf pf' style='top:1060;left:600;top:715;left:880; font-size:6pt;'>วันที่พิมพ์รายงาน : ".date('d/m/').(date('Y')+543)." ".date('H:i')." หน้า {PAGENO} / {nbpg}</div>
		";
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
		$DATE2 		= $this->Convertdate(1,$tx[3]);
		$PAYTYP 	= $tx[4];
		$PAYFOR 	= $tx[5];
		$USERID 	= $tx[6];
		$GROUP1 	= $tx[7];
		$GCODE 		= $tx[8];
		$CODE 	    = $tx[9];
		$report 	= $tx[10];
		$dt 		= $tx[11];
		$sort 	    = $tx[12];
		
		$MDT1 = "";
		if($dt == "tdt"){
			$MDT1 = "A.TMBILDT";
		}else{
			$MDT1 = "A.PAYDT";
		}
		
		$sql = "
			IF OBJECT_ID('tempdb..#PAY') IS NOT NULL DROP TABLE #PAY
				select PAYFOR,FORDESC,T01,N01,V01,D01,I01,NET01,T02,N02,V02,D02,I02,NET02,T03,N03,V03
				,D03,I03,NET03,T04,N04,V04,D04,I04,NET04
			into #PAY
			FROM(
				select A.PAYFOR,B.FORDESC ,sum(case when (A.PAYTYP = '01' ) then A.PAYAMT else 0 end) as T01 
				,sum(case when (A.PAYTYP = '01') then A.PAYAMT_N else 0 end) as N01 ,sum(case when (A.PAYTYP = '01') 
				then  A.PAYAMT_V else 0 end) as V01 ,sum(case when ( A.PAYTYP = '01' ) then  A.DISCT else 0 end) as D01 
				,sum(case when ( A.PAYTYP = '01') then (A.PAYINT-A.DSCINT) else 0 end) as I01 ,sum(case when ( A.PAYTYP = '01') 
				then  A.NETPAY else 0 end) as NET01 ,sum(case when ( A.PAYTYP = '02') then  A.PAYAMT else 0 end) as T02 
				,sum(case when ( A.PAYTYP = '02') then  A.PAYAMT_N else 0 end) as N02 ,sum(case when (A.PAYTYP = '02') 
				then  A.PAYAMT_V else 0 end) as V02 ,sum(case when ( A.PAYTYP = '02') then  A.DISCT else 0 end) as D02 
				,sum(case when (A.PAYTYP = '02') then (A.PAYINT-A.DSCINT) else 0 end) as I02 ,sum(case when ( A.PAYTYP = '02' ) 
				then A.NETPAY else 0 end) as NET02,sum(case when (A.PAYTYP not in ('01','02')) then A.PAYAMT else 0 end) as T03 
				,sum(case when ( A.PAYTYP not in ('01','02')) then  A.PAYAMT_N else 0 end) as N03 ,sum(case when 
				(A.PAYTYP not in ('01','02')) then  A.PAYAMT_V else 0 end) as V03 ,sum(case when (A.PAYTYP not in ('01','02')) 
				then  A.DISCT else 0 end) as D03 ,sum(case when (A.PAYTYP not in ('01','02')) then (A.PAYINT-A.DSCINT) else 0 end) 
				as I03 ,sum(case when (A.PAYTYP not in ('01','02')) then A.NETPAY else 0 end) as NET03 ,sum(A.PAYAMT) as T04 
				,sum(A.PAYAMT_N) as N04 ,sum(A.PAYAMT_V) as V04 ,sum(A.DISCT) as D04,sum(A.PAYINT-A.DSCINT) as I04 ,sum(A.NETPAY) 
				as NET04 from {$this->MAuth->getdb('CHQTRAN')} A,{$this->MAuth->getdb('PAYFOR')} B,{$this->MAuth->getdb('CUSTMAST')} c 
				where B.FORCODE = A.PAYFOR and A.CUSCOD = C.CUSCOD and A.USERID like '%".$USERID."%' AND A.LOCATRECV like '%".$LOCATRECV."%' 
				AND A.LOCATPAY like '%".$LOCATPAY."%' AND ".$MDT1." BETWEEN '".$DATE1."' AND '".$DATE2."' AND A.PAYTYP like '%".$PAYTYP."%'
				AND A.PAYFOR like '%".$PAYFOR."%' AND A.FLAG <> 'C' and (C.GROUP1 like '%".$GROUP1."%' or C.GROUP1 is null) 
				group by A.PAYFOR,B.FORDESC 
			)PAY
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$sql = "
			select * from #PAY order by PAYFOR,FORDESC 
		";
		$query1 = $this->db->query($sql);
		$sql = "
			select count(PAYFOR) as countPAYFOR,SUM(T01) as T01,SUM(N01) as N01,SUM(V01) as V01,SUM(D01) as D01,SUM(I01) as I01,SUM(NET01) as NET01
				,SUM(T02) as T02,SUM(N02) as N02,SUM(V02) as V02,SUM(D02) as D02,SUM(I02) as I02,SUM(NET02) as NET02
				,SUM(T03) as T03,SUM(N03) as N03,SUM(V03) as V03,SUM(D03) as D03,SUM(I03) as I03,SUM(NET03) as NET03
				,SUM(T04) as T01,SUM(N04) as N04,SUM(V04) as V04,SUM(D04) as D04,SUM(I04) as I04,SUM(NET04) as NET04
			from #PAY
		";
		$query2 = $this->db->query($sql); 
		$sql = "
			select top 20 PAYCODE,PAYDESC from {$this->MAuth->getdb('PAYTYP')} 
			where PAYCODE = '".$PAYTYP."'
		";
		$PAYDESC = "";
		$query3 = $this->db->query($sql);
		if($query3->row()){
			foreach($query3->result() as $row){
				$PAYDESC = $row->PAYDESC;
			}
		}
		$sql = "
			select top 20 FORCODE,FORDESC from {$this->MAuth->getdb('PAYFOR')} 
			where FORCODE = '".$PAYFOR."'
		";
		$FORDESC = "";
		$query4 = $this->db->query($sql);
		if($query4->row()){
			foreach($query4->result() as $row){
				$FORDESC = $row->FORDESC;
			}
		}
		$sql = "
			select USERID,USERNAME from {$this->MAuth->getdb('PASSWRD')} 
			where USERID = '".$USERID."'
		";
		//echo $sql; exit;
		$USERNAME = "";
		$query5 = $this->db->query($sql);
		if($query5->row()){
			foreach($query5->result() as $row){
				$USERNAME = $row->USERNAME;
			}
		}
		$sql = "
			select ARGCOD,ARGDES from {$this->MAuth->getdb('ARGROUP')}
            where ARGCOD = '".$GROUP1."' 
		";
		$ARGDES = "";
		$query6 = $this->db->query($sql);
		if($query6->row()){
			foreach($query6->result() as $row){
				$ARGDES = $row->ARGDES;
			}
		}
		$head = ""; $html = ""; 
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
		if($query1->row()){
			foreach($query1->result() as $row){
				$html .="
					<tr>
						<th style='width:200px;text-align:left;'>".$row->PAYFOR." ".$row->FORDESC."</th>
						<th style='width:10px;text-align:left;'></th>
						<th style='width:70px;text-align:right;'>".number_format($row->T01,2)."<br>".number_format($row->N01,2)."</th>
						<th style='width:70px;text-align:right;'>".number_format($row->V01,2)."<br>".number_format($row->D01,2)."</th>
						<th style='width:50px;text-align:right;'>".number_format($row->I01,2)."</th>
						<th style='width:70px;text-align:right;'>".number_format($row->NET01,2)."</th>
						<th style='width:70px;text-align:right;'>".number_format($row->T02,2)."<br>".number_format($row->N02,2)."</th>
						<th style='width:70px;text-align:right;'>".number_format($row->V02,2)."<br>".number_format($row->D02,2)."</th>
						<th style='width:50px;text-align:right;'>".number_format($row->I02,2)."</th>
						<th style='width:70px;text-align:right;'>".number_format($row->NET02,2)."</th>
						<th style='width:70px;text-align:right;'>".number_format($row->T03,2)."<br>".number_format($row->N03,2)."</th>
						<th style='width:70px;text-align:right;'>".number_format($row->V03,2)."<br>".number_format($row->D03,2)."</th>
						<th style='width:50px;text-align:right;'>".number_format($row->I03,2)."</th>
						<th style='width:70px;text-align:right;'>".number_format($row->NET03,2)."</th>
						<th style='width:70px;text-align:right;'>".number_format($row->T04,2)."<br>".number_format($row->N04,2)."</th>
						<th style='width:70px;text-align:right;'>".number_format($row->V04,2)."<br>".number_format($row->D04,2)."</th>
						<th style='width:50px;text-align:right;'>".number_format($row->I04,2)."</th>
						<th style='width:70px;text-align:right;'>".number_format($row->NET04,2)."</th>
					</tr>
				";
			}
		}
		if($query2->row()){
			foreach($query2->result() as $row){
				$html .="
					<tr class='trow bor' style='background-color:#ebebeb;'>
						<th style='width:70px;text-align:center;' colspan='2'>รวมทั้งสิ้น  ".$row->countPAYFOR." รายการ</th>
						<th style='width:70px;text-align:right;'>".number_format($row->T01,2)."<br>".number_format($row->N01,2)."</th>
						<th style='width:70px;text-align:right;'>".number_format($row->V01,2)."<br>".number_format($row->D01,2)."</th>
						<th style='width:50px;text-align:right;'>".number_format($row->I01,2)."</th>
						<th style='width:70px;text-align:right;'>".number_format($row->NET01,2)."</th>
						<th style='width:70px;text-align:right;'>".number_format($row->T02,2)."<br>".number_format($row->N02,2)."</th>
						<th style='width:70px;text-align:right;'>".number_format($row->V02,2)."<br>".number_format($row->D02,2)."</th>
						<th style='width:50px;text-align:right;'>".number_format($row->I02,2)."</th>
						<th style='width:70px;text-align:right;'>".number_format($row->NET02,2)."</th>
						<th style='width:70px;text-align:right;'>".number_format($row->T03,2)."<br>".number_format($row->N03,2)."</th>
						<th style='width:70px;text-align:right;'>".number_format($row->V03,2)."<br>".number_format($row->D03,2)."</th>
						<th style='width:50px;text-align:right;'>".number_format($row->I03,2)."</th>
						<th style='width:70px;text-align:right;'>".number_format($row->NET03,2)."</th>
						<th style='width:70px;text-align:right;'>".number_format($row->T04,2)."<br>".number_format($row->N04,2)."</th>
						<th style='width:70px;text-align:right;'>".number_format($row->V04,2)."<br>".number_format($row->D04,2)."</th>
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
		$content = "
			<table class='wf' style='font-size:7.5pt;height:700px;border-collapse:collapse;line-height:23px;overflow:wrap;vertical-align:text-top;'>
				<tbody>
					<tr>
						<th colspan='18' style='font-size:10pt;'>บริษัท ตั้งใจพัฒนายานยนต์ จำกัด</th>
					</tr>
					<tr>
						<th colspan='18' style='font-size:9pt;'>รายงานการรับชำระตามวันที่ใบรับ</th>
					</tr>
					<tr>
						<td style='text-align:center;' colspan='18'>
							<b>สาขาที่รับชำระ</b> &nbsp;&nbsp;".$LOCATRECV."&nbsp;&nbsp;
							<b>ชำระเพื่อ บ/ช สาขา</b>&nbsp;&nbsp;".$LOCATPAY."&nbsp;&nbsp;
							<b>จากวันที่</b>&nbsp;&nbsp;".$this->Convertdate(2,$DATE1)."&nbsp;&nbsp;
							<b>ถึงวันที่</b>&nbsp;&nbsp;".$this->Convertdate(2,$DATE2)."&nbsp;&nbsp;
						</td>
					</tr>
					<tr>
						<td style='text-align:center;' colspan='17'>
							<b>ชำระโดย</b>&nbsp;&nbsp;".$PAYDESC."&nbsp;&nbsp;
							<b>ชำระค่า</b>&nbsp;&nbsp;".$FORDESC."&nbsp;&nbsp;
							<b>พนักงานบันทึกข้อมูล</b>&nbsp;&nbsp;".$USERNAME."&nbsp;&nbsp;
							<b>กลุ่มลูกค้า</b>&nbsp;&nbsp;".$ARGDES."&nbsp;&nbsp;
						</td>
						<td style='text-align:right;' colspan='1'>RpRec11</td>
					</tr>
					<br><br>
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
			<div class='wf pf' style='top:1060;left:600;top:715;left:880; font-size:6pt;'>วันที่พิมพ์รายงาน : ".date('d/m/').(date('Y')+543)." ".date('H:i')." หน้า {PAGENO} / {nbpg}</div>
		";
		$mpdf->SetHTMLHeader($head);	
		$mpdf->WriteHTML($content);	
		$mpdf->Output();
	}
}