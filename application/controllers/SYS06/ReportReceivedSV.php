<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/********************************************************
             ______@09/01/2020______
			 Pasakorn Boonded

********************************************************/
class ReportReceivedSV extends MY_Controller {
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
							<br>รายงานการรับชำระเงินตามวันที่บันทึก<br>
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
									รหัสพนักงานเก็บเงิน
									<select id='CODE' class='form-control input-sm' data-placeholder='รหัสพนักงานเก็บเงิน'></select>
								</div>
							</div>
						</div>
						<div class='col-sm-10 col-xs-10 col-sm-offset-1'><br>	
							<div class='col-sm-12 col-xs-12'>	
								<div class='form-group'>
									รูปแบบรายงาน
									<div class='col-sm-12 col-xs-12' style='border:0.1px dotted #d6d6d6;'>	
										<div class='col-sm-6'>
											<div class='form-group'>
												<br>
												<label>
													<input type= 'radio' id='all' name='report' checked> แสดงรายการทั้งหมด
												</label>
											</div>
										</div>
										<div class='col-sm-6'>
											<div class='form-group'>
												<br>
												<label>
													<input type= 'radio' id='pay' name='report'> สรุปตามการรับชำระ
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
										<div class='col-sm-4'>
											<div class='form-group'>
												<br>
												<label>
													<input type= 'radio' id='set1' name='Sort' checked> วันที่,เลขที่ใบรับเงิน
												</label>
											</div>
										</div>
										<div class='col-sm-4'>
											<div class='form-group'>
												<br>
												<label>
													<input type= 'radio' id='set2' name='Sort'> วันที่เช็ค
												</label>
											</div>
										</div>
										<div class='col-sm-4'>
											<div class='form-group'>
												<br>
												<label>
													<input type= 'radio' id='set3' name='Sort'> เลขที่สัญญา
												</label>
											</div>
										</div>
										<div class='col-sm-4'>
											<div class='form-group'>
												<br>
												<label>
													<input type= 'radio' id='set4' name='Sort'> เลขที่ใบเสร็จรับเงิน
												</label>
											</div>
										</div>
										<div class='col-sm-4'>
											<div class='form-group'>
												<br>
												<label>
													<input type= 'radio' id='set5' name='Sort'> วันที่นำฝาก
												</label>
											</div>
										</div>
										<div class='col-sm-4'>
											<div class='form-group'>
												<br>
												<label>
													<input type= 'radio' id='set6' name='Sort'> เลขที่ใบอ้างอิง
												</label>
											</div>
										</div>
										<div class='col-sm-4'>
											<div class='form-group'>
												<br>
												<label>
													<input type= 'radio' id='set7' name='Sort'> สาขาที่ได้รับ
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
		$html .="<script src='".base_url('public/js/SYS06/ReportReceivedSV.js')."'></script>";
		echo $html;
	}
	function conditiontopdf(){
		$data = array();
		$data[] = urlencode($_REQUEST["LOCATRECV"].'||'.$_REQUEST["LOCATPAY"].'||'.$_REQUEST["DATE1"]
		.'||'.$_REQUEST["DATE2"].'||'.$_REQUEST["PAYTYP"].'||'.$_REQUEST["PAYFOR"].'||'.$_REQUEST["USERID"]
		.'||'.$_REQUEST["GROUP1"].'||'.$_REQUEST["CODE"].'||'.$_REQUEST["sort"]);
		echo json_encode($this->generateData($data,"encode"));
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
		$CODE 	    = $tx[8];
		$sort 	    = $tx[9];
		//echo $sort; exit;
		$SCRT = "";
		$SET = "";
		if($sort == "set1"){
			$SCRT = "วันที่,เลขที่ใบรับเงิน";
			$SET  = "TMBILDT,TMBILL";
		}else if($sort == "set2"){
			$SCRT = "วันที่เช็ค ";
			$SET = "CHQDT";
		}else if($sort == "set3"){
			$SCRT = "เลขที่สัญญา ";
			$SET = "CONTNO,TMBILDT";
		}else if($sort == "set4"){
			$SCRT = "เลขที่ใบเสร็จรับเงิน ";
			$SET = "BILLNO";
		}else if($sort == "set5"){
			$SCRT = "วันที่นำฝาก ";
			$SET = "PAYINDT";
		}else if($sort == "set6"){
			$SCRT = "เลขที่ใบอ้างอิง ";
			$SET = "REFNO";
		}else if($sort == "set7"){
			$SCRT = "สาขาที่ได้รับ ";
			$SET = "LOCATRECV,TMBILDT,TMBILL";
		}
		
		$sql = "
			IF OBJECT_ID('tempdb..#ALLR') IS NOT NULL DROP TABLE #ALLR
			select TMBILL,TMBILDT,BILLNO,BILLDT,PAYTYP,CHQNO,CHQBK,CHQAMT,USERID,INPDT_D,INPDT_T,INPTIME,TSALE
				,LOCATRECV,LOCATPAY,CONTNO,PAYAMT,FORDESC,REFNO,PAYAMT_N,PAYAMT_V,DISCT,PAYFOR,INT,PAYINT
				,DSCINT,NETPAY,FLAG,NAME,BILLCOLL,TOTPRC,SMPAY,FINNAME,XBAL,CHQDT,PAYINDT
			into #ALLR
			FROM(
				select A.TMBILL,convert(varchar(8),A.TMBILDT,112) as TMBILDT,A.BILLNO,convert(varchar(8),A.BILLDT,112) as BILLDT
				,A.PAYTYP,A.CHQNO,A.CHQBK,A.CHQAMT,A.USERID,convert(varchar(8),A.INPDT,112) as INPDT_D,convert(varchar(8),A.INPDT,114) as INPDT_T
				,A.INPTIME,B.TSALE,A.LOCATRECV,B.LOCATPAY,B.CONTNO,B.PAYAMT,D.FORDESC,A.REFNO,B.PAYAMT_N,B.PAYAMT_V,B.DISCT,B.PAYFOR
				,B.PAYINT-B.DSCINT as INT,B.PAYINT,B.DSCINT,B.NETPAY,B.FLAG,C.SNAM+RTRIM(C.NAME1)+''+RTRIM(C.NAME2) as NAME,E.BILLCOLL
				,TOTPRC,E.SMPAY,F.FINNAME,E.TOTPRC-E.SMPAY as XBAL,A.CHQDT,A.PAYINDT from {$this->MAuth->getdb('CHQMAS')} A
				left join {$this->MAuth->getdb('CHQTRAN')} B on A.TMBILL = B.TMBILL AND A.LOCATRECV=B.LOCATRECV 
				left join {$this->MAuth->getdb('CUSTMAST')} C on A.CUSCOD = C.CUSCOD 
				left join {$this->MAuth->getdb('PAYFOR')} D on B.PAYFOR = D.FORCODE 
				left join (
					select CONTNO,LOCAT,BILLCOLL,TOTPRC,SMPAY,'H' as TSALE,'' as FINCOD from {$this->MAuth->getdb('ARMAST')}  
					where CONTNO in (select CONTNO from {$this->MAuth->getdb('CHQTRAN')} where LOCATRECV like '%".$LOCATRECV."%' and 
					INPDT >= '".$DATE1."' and INPDT < '".$DATE2."')  
					union 
					select CONTNO,LOCAT,'' as BILLCOLL,TOTPRC,SMPAY,'C' as TSALE,'' as FINCOD from {$this->MAuth->getdb('ARCRED')}  
					where CONTNO in (select CONTNO from {$this->MAuth->getdb('CHQTRAN')} where LOCATRECV like '%".$LOCATRECV."%' and 
					INPDT >= '".$DATE1."' and INPDT < '".$DATE2."')
					union
					select CONTNO,LOCAT,'' as BILLCOLL,TOTPRC,SMPAY,'F' as TSALE,FINCOD from {$this->MAuth->getdb('ARFINC')} 
					where CONTNO in (select CONTNO from {$this->MAuth->getdb('CHQTRAN')} where LOCATRECV like '%".$LOCATRECV."%' and 
					INPDT >= '".$DATE1."' and INPDT < '".$DATE2."')  
					union
					select CONTNO,LOCAT,'' as BILLCOLL,TOTPRC,SMPAY,'A' as TSALE,'' as FINCOD from {$this->MAuth->getdb('AR_INVOI')} 
					where CONTNO in (select CONTNO from {$this->MAuth->getdb('CHQTRAN')} where LOCATRECV like '%".$LOCATRECV."%' and 
					INPDT >= '".$DATE1."' and INPDT < '".$DATE2."')  
					union
					select CONTNO,LOCAT,'' as BILLCOLL,OPTPTOT as TOTPRC,SMPAY,'O' as TSALE,'' as FINCOD from {$this->MAuth->getdb('AROPTMST')}
					where CONTNO in (select CONTNO from {$this->MAuth->getdb('CHQTRAN')} where LOCATRECV like '%".$LOCATRECV."%' and 
					INPDT >= '".$DATE1."' and INPDT < '".$DATE2."')  
					union
					select CONTNO,LOCAT,BILLCOLL,TOTPRC,SMPAY,'H' as TSALE,'' as FINCOD from {$this->MAuth->getdb('HARMAST')} 
					where CONTNO in (select CONTNO from {$this->MAuth->getdb('CHQTRAN')} where LOCATRECV like '%".$LOCATRECV."%' and 
					INPDT >= '".$DATE1."' and INPDT < '".$DATE2."')  
					union
					select CONTNO,LOCAT,'' as BILLCOLL,TOTPRC,SMPAY,'C' as TSALE,'' as FINCOD from {$this->MAuth->getdb('HARCRED')}  
					where CONTNO in (select CONTNO from {$this->MAuth->getdb('CHQTRAN')} where LOCATRECV like '%".$LOCATRECV."%' 
					and INPDT >= '".$DATE1."' and INPDT < '".$DATE2."')  
					union
					select CONTNO,LOCAT,'' as BILLCOLL,TOTPRC,SMPAY,'F' as TSALE,FINCOD from {$this->MAuth->getdb('HARFINC')} 
					where CONTNO in (select CONTNO from {$this->MAuth->getdb('CHQTRAN')} where LOCATRECV like '%".$LOCATRECV."%' 
					and INPDT >= '".$DATE1."' and INPDT < '".$DATE2."')  
					union
					select CONTNO,LOCAT,'' as BILLCOLL,TOTPRC,SMPAY,'A' as TSALE,'' as FINCOD from {$this->MAuth->getdb('HAR_INVO')} 
					where CONTNO in (select CONTNO from {$this->MAuth->getdb('CHQTRAN')} where LOCATRECV like '%".$LOCATRECV."%' 
					and INPDT >= '".$DATE1."' and INPDT < '".$DATE2."')  
					union
					select CONTNO,LOCAT,'' as BILLCOLL,OPTPTOT as TOTPRC,SMPAY,'O' as TSALE,'' as FINCOD from {$this->MAuth->getdb('HAROPMST')} 
					where CONTNO in (select CONTNO from {$this->MAuth->getdb('CHQTRAN')} where LOCATRECV like '%".$LOCATRECV."%' 
					and INPDT >= '".$DATE1."' and INPDT < '".$DATE2."')
				) as E on B.CONTNO = E.CONTNO and B.LOCATPAY = E.LOCAT  
				left join {$this->MAuth->getdb('FINMAST')} F on E.FINCOD = F.FINCODE 
				and (B.PAYFOR = '004' or B.PAYFOR = '011') where (C.GROUP1 like '%".$GROUP1."%' or C.GROUP1 is null) 
				and (A.LOCATRECV like '%".$LOCATRECV."%') AND (A.INPDT >= '".$DATE1."' and A.INPDT < '".$DATE2."') 
				and (A.PAYTYP like '%".$PAYTYP."%') and (A.USERID like '%".$USERID."%') and (A.TMBILL in 
				(select TMBILL from {$this->MAuth->getdb('CHQTRAN')} T where (LOCATPAY like '%".$LOCATPAY."%') and (PAYFOR like '%".$PAYFOR."%'))) --order by  A.TMBILDT,A.TMBILL
			)ALLR
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		$sql = "
			select * from #ALLR order by ".$SET."
		";
		$query1 = $this->db->query($sql);
		$sql = "
			select COUNT(PAYAMT) as countPAYAMT,SUM(PAYAMT) as sumPAYAMT,SUM(PAYAMT_V) as sumPAYAMT_V,SUM(DISCT) as sumDISCT 
			,SUM(PAYINT) as sumPAYINT,SUM(DSCINT) as sumDSCINT,SUM(NETPAY) as sumNETPAY from #ALLR
		";
		$query2 = $this->db->query($sql);
		$sql = "
			select COUNT(PAYAMT) as countPAYAMT,SUM(PAYAMT) as sumPAYAMT,SUM(PAYAMT_V) as sumPAYAMT_V,SUM(DISCT) as sumDISCT 
			,SUM(PAYINT) as sumPAYINT,SUM(DSCINT) as sumDSCINT,SUM(NETPAY) as sumNETPAY from #ALLR where FLAG = 'C'
		";
		$query3 = $this->db->query($sql);
		$sql = "
			declare @A1 decimal(12,2) = (select coalesce(SUM(PAYAMT),0) from #ALLR); 
			declare @B1 decimal(12,2) = (select coalesce(SUM(PAYAMT),0) from #ALLR where FLAG = 'C'); 
			declare @A2 decimal(12,2) = (select coalesce(SUM(PAYAMT_V),0) from #ALLR); 
			declare @B2 decimal(12,2) = (select coalesce(SUM(PAYAMT_V),0) from #ALLR where FLAG = 'C'); 
			declare @A3 decimal(12,2) = (select coalesce(SUM(DISCT),0) from #ALLR); 
			declare @B3 decimal(12,2) = (select coalesce(SUM(DISCT),0) from #ALLR where FLAG = 'C'); 
			declare @A4 decimal(12,2) = (select coalesce(SUM(PAYINT),0) from #ALLR); 
			declare @B4 decimal(12,2) = (select coalesce(SUM(PAYINT),0) from #ALLR where FLAG = 'C');
			declare @A5 decimal(12,2) = (select coalesce(SUM(DSCINT),0) from #ALLR); 
			declare @B5 decimal(12,2) = (select coalesce(SUM(DSCINT),0) from #ALLR where FLAG = 'C'); 
			declare @A6 decimal(12,2) = (select coalesce(SUM(NETPAY),0) from #ALLR); 
			declare @B6 decimal(12,2) = (select coalesce(SUM(NETPAY),0) from #ALLR where FLAG = 'C'); 

			select @A1-@B1 as PAYAMT,@A2-@B2 as PAYAMT_V,@A3-@B3 as DISCT,@A4-@B4 as PAYINT,@A5-@B5 as DSCINT,@A6-@B6 as NETPAY
		";
		$query4 = $this->db->query($sql);
		$sql = "
			select top 20 PAYCODE,PAYDESC from {$this->MAuth->getdb('PAYTYP')} 
			where PAYCODE = '".$PAYTYP."'
		";
		$PAYDESC = "";
		$query5 = $this->db->query($sql);
		if($query5->row()){
			foreach($query5->result() as $row){
				$PAYDESC = $row->PAYDESC;
			}
		}
		$sql = "
			select top 20 FORCODE,FORDESC from {$this->MAuth->getdb('PAYFOR')} 
			where FORCODE = '".$PAYFOR."'
		";
		$FORDESC = "";
		$query6 = $this->db->query($sql);
		if($query6->row()){
			foreach($query6->result() as $row){
				$FORDESC = $row->FORDESC;
			}
		}
		$sql = "
			select USERID,USERNAME from {$this->MAuth->getdb('PASSWRD')} 
			where USERID = '".$USERID."'
		";
		//echo $sql; exit;
		$USERNAME = "";
		$query7 = $this->db->query($sql);
		if($query7->row()){
			foreach($query7->result() as $row){
				$USERNAME = $row->USERNAME;
			}
		}
		$sql = "
			select ARGCOD,ARGDES from {$this->MAuth->getdb('ARGROUP')}
            where ARGCOD = '".$GROUP1."' 
		";
		$ARGDES = "";
		$query8 = $this->db->query($sql);
		if($query8->row()){
			foreach($query8->result() as $row){
				$ARGDES = $row->ARGDES;
			}
		}
		
		$head = ""; $html = ""; $i = 0;
		$head = "
			<tr class='wm'>
				<td class='wf pd' style='height:1px;border-top:0.1px solid black;' colspan='14'></td>
			</tr>
			<tr>
				<th style='border-bottom:0.1px solid black;text-align:center;'>สาขา</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>เลขใบรับเงินชั่วคราว<br>เลขที่สัญญา</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>วันที่ใบรับเงิน<br>ชื่อ - นามสกุล</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>เลขที่ใบเสร็จรับเงิน</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>วันที่ใบเสร็จ<br>ชำระค่า</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>ชำระโดย</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>เลขที่เช็ค<br>BillColl</th> 
				<th style='border-bottom:0.1px solid black;text-align:right;'>เช็คธนาคาร<br>ยอดหักลูกหนี้</th> 
				<th style='border-bottom:0.1px solid black;text-align:right;'>จำนวนเงินทั้งหมด<br>ภาษีมูลค่าเพิ่ม</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>UserID<br>ส่วนลด</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>วันเวลาบันทึก<br>เบี้ยปรับส่วนลด</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>Reference No<br>เบี้ยปรับ</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'><br>ยอดรับสุทธิ</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'><br>ล/น เหลือ ณ วันพิมพ์</th>
			</tr>
			<tr>
			</tr>
		";
		$status = "";
		if($query1->row()){
			foreach($query1->result() as $row){$i++;
				if($row->FLAG == 'C'){
					$status = "*ยกเลิก*";
				}else{
					$status = "";
				}
				$html .="
					<tr class='trow'>
						<td style='width:50px;text-align:center;'>".$row->LOCATRECV."</td>
						<td style='width:50px;text-align:left;'>".$row->TMBILL."<br>".$row->CONTNO."</td>
						<td style='width:200px;text-align:left;'>".$this->Convertdate(2,$row->TMBILDT)."<br>".$row->NAME."</td>
						<td style='width:30px;text-align:left;'>".$row->BILLNO."</td>
						<td style='width:400px;text-align:left;'>".$this->Convertdate(2,$row->BILLDT)."<br>".$row->FORDESC."</td>
						<td style='width:30px;text-align:left;'>".$row->PAYTYP."</td>
						<td style='width:50px;text-align:left;'>".$row->CHQNO."<br>".$row->BILLCOLL."</td>
						<td style='width:50px;text-align:right;'>".$row->CHQBK."<br>".number_format($row->PAYAMT,2)."</td>
						<td style='width:50px;text-align:right;'>".number_format($row->CHQAMT,2)."<br>".number_format($row->PAYAMT_V,2)."</td>
						<td style='width:50px;text-align:right;'>".$row->USERID."<br>".number_format($row->DISCT,2)."</td>
						<td style='width:100px;text-align:right;'>".$this->Convertdate(2,$row->INPDT_D)." ".$row->INPDT_T."<br>".number_format($row->PAYINT,2)."</td>
						<td style='width:50px;text-align:right;'>".$row->REFNO."<br>".number_format($row->DSCINT,2)."</td>
						<td style='width:50px;text-align:right;'><br>".number_format($row->NETPAY,2)."</td>
						<td style='width:50px;text-align:right;'>".$status."<br>".number_format($row->XBAL,2)."</td>
					</tr>
				";
			}
		}
		$countPAYAMT = "";
		if($query2->row()){
			foreach($query2->result() as $row){
				$countPAYAMT = $row->countPAYAMT;
				$html .="
					<tr>
						<td class='wf' style='height:1px;border-top:0.1px solid black;' colspan='14'></td>
					</tr>
					<tr class='trow'>
						<th style='width:50px;text-align:right;'colspan='2'>รวมใบเสร็จทั้งสิ้น</th>
						<td style='width:50px;text-align:right;'>".$row->countPAYAMT."</td>
						<th style='width:50px;text-align:right;'>รายการ</th>
						<th style='width:50px;text-align:center;'colspan='3'>ยอดรวมทั้งสิ้น</th>
						<td style='width:50px;text-align:right;'>".number_format($row->sumPAYAMT,2)."</td>
						<td style='width:50px;text-align:right;'>".number_format($row->sumPAYAMT_V,2)."</td>
						<td style='width:50px;text-align:right;'>".number_format($row->sumDISCT,2)."</td>
						<td style='width:50px;text-align:right;'>".number_format($row->sumPAYINT,2)."</td>
						<td style='width:50px;text-align:right;'>".number_format($row->sumDSCINT,2)."</td>
						<td style='width:50px;text-align:right;'>".number_format($row->sumNETPAY,2)."</td>
					</tr>
				";
			}
		}
		if($query3->row()){
			foreach($query3->result() as $row){
				$html .="
					<tr class='trow'>
						<th style='width:50px;text-align:right;'colspan='2'>รวมทั้งสิ้น</th>
						<td style='width:50px;text-align:right;'>".$countPAYAMT."</td>
						<th style='width:50px;text-align:right;'>รายการ</th>
						<th style='width:50px;text-align:center;'colspan='3'>ยอดรวมรายการยกเลิก</th>
						<td style='width:50px;text-align:right;'>".number_format($row->sumPAYAMT,2)."</td>
						<td style='width:50px;text-align:right;'>".number_format($row->sumPAYAMT_V,2)."</td>
						<td style='width:50px;text-align:right;'>".number_format($row->sumDISCT,2)."</td>
						<td style='width:50px;text-align:right;'>".number_format($row->sumPAYINT,2)."</td>
						<td style='width:50px;text-align:right;'>".number_format($row->sumDSCINT,2)."</td>
						<td style='width:50px;text-align:right;'>".number_format($row->sumNETPAY,2)."</td>
					</tr>
				";
			}
		}
		if($query4->row()){
			foreach($query4->result() as $row){
				$html .="
					<tr class='trow'>
						<th style='width:50px;text-align:right;'colspan='2'></th>
						<td style='width:50px;text-align:right;'></td>
						<th style='width:50px;text-align:right;'></th>
						<th style='width:50px;text-align:center;'colspan='3'>ยอดรวมสุทธิ</th>
						<td style='width:50px;text-align:right;'>".number_format($row->PAYAMT,2)."</td>
						<td style='width:50px;text-align:right;'>".number_format($row->PAYAMT_V,2)."</td>
						<td style='width:50px;text-align:right;'>".number_format($row->DISCT,2)."</td>
						<td style='width:50px;text-align:right;'>".number_format($row->PAYINT,2)."</td>
						<td style='width:50px;text-align:right;'>".number_format($row->DSCINT,2)."</td>
						<td style='width:50px;text-align:right;'>".number_format($row->NETPAY,2)."</td>
					</tr>
					<tr>
						<td class='wf' style='height:1px;border-top:0.1px solid black;' colspan='14'></td>
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
		if($i > 0){
			$content = "
				<table class='wf' style='font-size:7.5pt;height:700px;border-collapse:collapse;line-height:23px;overflow:wrap;vertical-align:text-top;'>
					<tbody>
						<tr>
							<th colspan='14' style='font-size:10pt;'>บริษัท ตั้งใจพัฒนายานยนต์ จำกัด</th>
						</tr>
						<tr>
							<th colspan='14' style='font-size:9pt;'>รายงานการรับชำระตามวันที่บันทึก</th>
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
							<td style='text-align:left;' colspan='2'><b>Scrt By :</b>&nbsp;&nbsp;".$SCRT."</td>
							<td style='text-align:center;' colspan='10'>
								<b>ชำระโดย</b>&nbsp;&nbsp;".$PAYDESC."&nbsp;&nbsp;
								<b>ชำระค่า</b>&nbsp;&nbsp;".$FORDESC."&nbsp;&nbsp;
								<b>พนักงานบันทึกข้อมูล</b>&nbsp;&nbsp;".$USERNAME."&nbsp;&nbsp;
								<b>กลุ่มลูกค้า</b>&nbsp;&nbsp;".$ARGDES."&nbsp;&nbsp;
							</td>
							<td style='text-align:right;' colspan='2'>RpRec21</td>
						</tr>
						".$head."
						".$html."
					</tbody>
				</table>
			";			
			$head = "
				<div class='wf pf' style='top:1060;left:600;top:715;left:880; font-size:6pt;'>วันที่พิมพ์รายงาน : ".date('d/m/').(date('Y')+543)." ".date('H:i')." หน้า {PAGENO} / {nbpg}</div>
			";
		}else{
			$content = "<font style='color:red;'>ไม่พบข้อมูล</font>";
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
		$DATE2 		= $this->Convertdate(1,$tx[3]);
		$PAYTYP 	= $tx[4];
		$PAYFOR 	= $tx[5];
		$USERID 	= $tx[6];
		$GROUP1 	= $tx[7];
		$CODE 	    = $tx[8];
		$sort 	    = $tx[9];
		$sql = "
			IF OBJECT_ID('tempdb..#PAY') IS NOT NULL DROP TABLE #PAY
			select PAYFOR,FORDESC,T01,N01,V01,D01,I01,NET01,T02,N02,V02,D02,I02,NET02,T03,N03,V03
				,D03,I03,NET03,T04,N04,V04,D04,I04,NET04
			into #PAY
			FROM(
				select A.PAYFOR,B.FORDESC
					,sum(case when (A.PAYTYP = '01') then A.PAYAMT else 0 end) as T01
					,sum(case when (A.PAYTYP = '01') then A.PAYAMT_N else 0 end) as N01 
					,sum(case when (A.PAYTYP = '01') then A.PAYAMT_V else 0 end) as V01 
					,sum(case when (A.PAYTYP = '01') then A.DISCT else 0 end) as D01
					,sum(case when (A.PAYTYP = '01') then (A.PAYINT-A.DSCINT) else 0 end) as I01 
					,sum(case when (A.PAYTYP = '01') then A.NETPAY else 0 end) as NET01 
					,sum(case when (A.PAYTYP = '02') then A.PAYAMT else 0 end) as T02 
					,sum(case when (A.PAYTYP = '02') then A.PAYAMT_N else 0 end) as N02 
					,sum(case when (A.PAYTYP = '02') then A.PAYAMT_V else 0 end) as V02 
					,sum(case when (A.PAYTYP = '02') then A.DISCT else 0 end) as D02 
					,sum(case when (A.PAYTYP = '02') then (A.PAYINT-A.DSCINT) else 0 end) as I02 
					,sum(case when (A.PAYTYP = '02') then A.NETPAY else 0 end) as NET02 
					,sum(case when (A.PAYTYP not in ('01','02')) then A.PAYAMT else 0 end) as T03 
					,sum(case when (A.PAYTYP not in ('01','02')) then A.PAYAMT_N else 0 end) as N03 
					,sum(case when (A.PAYTYP not in ('01','02')) then A.PAYAMT_V else 0 end) as V03 
					,sum(case when (A.PAYTYP not in ('01','02')) then A.DISCT else 0 end) as D03 
					,sum(case when (A.PAYTYP not in ('01','02')) then (A.PAYINT-A.DSCINT) else 0 end) as I03 
					,sum(case when (A.PAYTYP not in ('01','02')) then A.NETPAY else 0 end) as NET03 
					,sum(A.PAYAMT) as T04 ,sum(A.PAYAMT_N) as N04 ,sum(A.PAYAMT_V) AS V04 ,sum(A.DISCT) as D04 
					,sum(A.PAYINT-A.DSCINT) as I04 
					,sum(A.NETPAY) as NET04 from {$this->MAuth->getdb('CHQTRAN')} A  
				left join {$this->MAuth->getdb('PAYFOR')}   B on B.FORCODE = A.PAYFOR  
				left join {$this->MAuth->getdb('CUSTMAST')} C on A.CUSCOD = C.CUSCOD where A.USERID like '%".$USERID."%' 
				and A.LOCATRECV like '%".$LOCATRECV."%' 
				and A.LOCATPAY like '%".$LOCATPAY."%' and A.INPDT BETWEEN '".$DATE1."' and '".$DATE2."' 
				and A.PAYTYP like '%".$PAYTYP."%' 
				and A.PAYFOR like '%".$PAYFOR."%' and A.FLAG <> 'C' and (C.GROUP1 like '%".$GROUP1."%' OR C.GROUP1 is null)  
				group by A.PAYFOR,B.FORDESC 
			)PAY
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		$sql = "
			select * from #PAY
		";
		$query1 = $this->db->query($sql);
		$sql = "
			select count(PAYFOR) as countPAYFOR
				,SUM(T01) as T01,SUM(N01) as N01,SUM(V01) as V01,SUM(D01) as D01,SUM(I01) as I01,SUM(NET01) as NET01
				,SUM(T02) as T02,SUM(N02) as N02,SUM(V02) as V02,SUM(D02) as D02,SUM(I02) as I02,SUM(NET02) as NET02
				,SUM(T03) as T03,SUM(N03) as N03,SUM(V03) as V03,SUM(D03) as D03,SUM(I03) as I03,SUM(NET03) as NET03
				,SUM(T04) as T04,SUM(N04) as N04,SUM(V04) as V04,SUM(D04) as D04,SUM(I04) as I04,SUM(NET04) as NET04
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
		
		if($query1->row()){
			foreach($query1->result() as $row){$i++;
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
		if($query2->row()){
			foreach($query2->result() as $row){
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
						<th style='width:70px;text-align:right;'>".number_format($row->I04,2)."</th>
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
			$head = "
				<div class='wf pf' style='top:1060;left:600;top:715;left:880; font-size:6pt;'>วันที่พิมพ์รายงาน : ".date('d/m/').(date('Y')+543)." ".date('H:i')." หน้า {PAGENO} / {nbpg}</div>
			";
		}else{
			$content = "<font style='color:red;'>ไม่พบข้อมูล</font>";
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