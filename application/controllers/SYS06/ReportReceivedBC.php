<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/********************************************************
             ______@14/01/2020______
			 Pasakorn Boonded

********************************************************/
class ReportReceivedBC extends MY_Controller {
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
							<br>รายงานการรับชำระเงินตามพนักงานเก็บเงิน<br>
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
									<select id='LOCAT' class='form-control input-sm' data-placeholder='สาขา'></select>
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
									BILLCOLL
									<select id='BILLCOLL' class='form-control input-sm' data-placeholder='เลือก'></select>
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
									ตำบล
									<input type='text' id='TUMB' class='form-control input-sm'>
								</div>
							</div>
							<div class='col-sm-3'>	
								<div class='form-group'>
									อำเภอ
									<select id='AUMPCOD' class='form-control input-sm' data-placeholder='อำเภอ'></select>
								</div>
							</div>
							<div class='col-sm-3'>	
								<div class='form-group'>
									จังหวัด
									<select id='PROVCOD' class='form-control input-sm' data-placeholder='จังหวัด'></select>
								</div>
							</div>
							<div class='col-sm-3'>	
								<div class='form-group'>
									% ค่าคอมจากยอดชำระ
									<input type='text' id='PERSEN' class='form-control input-sm' value='0.00'>
								</div>
							</div>
						</div>
						<div class='col-sm-10 col-xs-10 col-sm-offset-1'><br>	
							<div class='col-sm-12 col-xs-12'>	
								<div class='form-group'>
									เรียงลำดับข้อมูลตาม <br>
									<div class='col-sm-12 col-xs-12' style='border:0.1px dotted #d6d6d6;'>	
										<div class='col-sm-3'>
											<div class='form-group'>
												<br>
												<label>
													<input type= 'radio' id='OR1' name='order' checked> เลขที่สัญญา
												</label>
											</div>
										</div>
										<div class='col-sm-3'>
											<div class='form-group'>
												<br>
												<label>
													<input type= 'radio' id='OR2' name='order'> รับเพื่อสาขา
												</label>
											</div>
										</div>
										<div class='col-sm-3'>
											<div class='form-group'>
												<br>
												<label>
													<input type= 'radio' id='OR3' name='order'> รหัสลูกค้า
												</label>
											</div>
										</div>
										<div class='col-sm-3'>
											<div class='form-group'>
												<br>
												<label>
													<input type= 'radio' id='OR4' name='order'> รหัส BILLCOLL
												</label>
											</div>
										</div>
										<div class='col-sm-3'>
											<div class='form-group'>
												<br>
												<label>
													<input type= 'radio' id='OR5' name='order'> วันที่รับเงิน
												</label>
											</div>
										</div>
										<div class='col-sm-3'>
											<div class='form-group'>
												<br>
												<label>
													<input type= 'radio' id='OR6' name='order'> วันดิว
												</label>
											</div>
										</div>
										<div class='col-sm-3'>
											<div class='form-group'>
												<br>
												<label>
													<input type= 'radio' id='OR7' name='order'> สาขาที่ได้รับ
												</label>
											</div>
										</div>
									</div>
								</div>
								<div class='col-sm-12 col-xs-12'>
									<br>
									<button id='btnreportBC' type='button' class='btn btn-info btn-outline btn-block' style='width:100%'><span class='fa fa-folder-open'><b>แสดง</b></span></button>
								</div><br>
							</div>
						</div>
					</div>
				</div>
			</div>
		";
		$html .="<script src='".base_url('public/js/SYS06/ReportReceivedBC.js')."'></script>";
		echo $html;
	}
	function conditiontopdf(){
		$data = array();
		$data[] = urlencode($_REQUEST["LOCATRECV"].'||'.$_REQUEST["LOCAT"].'||'.$_REQUEST["DATE1"].'||'.$_REQUEST["DATE2"]
		.'||'.$_REQUEST["BILLCOLL"].'||'.$_REQUEST["PAYTYP"].'||'.$_REQUEST["TUMB"].'||'.$_REQUEST["AUMPCOD"]
		.'||'.$_REQUEST["PROVCOD"].'||'.$_REQUEST["PERSEN"].'||'.$_REQUEST["order"]);
		echo json_encode($this->generateData($data,"encode"));
	}
	function pdf(){
		$data = array();
		$data[] = $_GET["condpdf"];
		$arrs = $this->generateData($data,"decode");
		$arrs[0] = urldecode($arrs[0]);
		
		$tx = explode("||",$arrs[0]);
		$LOCATRECV 	= $tx[0];
		$LOCAT 	    = $tx[1];
		$DATE1 	    = $this->Convertdate(1,$tx[2]);
		$DATE2 		= $this->Convertdate(1,$tx[3]);
		$BILLCOLL 	= $tx[4];
		$PAYTYP 	= $tx[5];
		$TUMB 	    = $tx[6];
		$AUMPCOD 	= $tx[7];
		$PROVCOD 	= $tx[8];
		$PERSEN 	= $tx[9];
		$order 	    = $tx[10];
		
		$SCRT = "";
		if($order == "CONTNO"){
			$SCRT = "เลขที่สัญญา";
		}else if($order == "LOCAT"){
			$SCRT = "รับเพื่อสาขา";
		}else if($order == "CUSCOD"){
			$SCRT = "รหัสลูกค้า";
		}else if($order == "BILLCOLL"){
			$SCRT = "รหัส BILLCOLL";
		}else if($order == "PAYDT"){
			$SCRT = "วันที่รับเงิน";
		}else if($order == "DDATE"){
			$SCRT = "วันดิว";
		}else if($order == "LOCATRECV"){
			$SCRT = "สาขาที่ได้รับ ";
		}
		$sql = "
			IF OBJECT_ID('tempdb..#OFFICER') IS NOT NULL DROP TABLE #OFFICER
			select TMBILL,BILLNO,LOCATRECV,CUSCOD,PAYTYP,FLAG,PAYFOR,CONTNO,PAYAMT,DISCT
				,PAYINT,DSCINT,COMAMT,NETPAY,convert(varchar(8),PAYDT,112) as PAYDT,F_PAY
				,L_PAY,BAL,LOCAT,STRNO,TOTPRC,SMPAY,SMCHQ,BILLCOLL,SNAM+NAME1+' '+NAME2 as CUSNAME,FORDESC
				,ADDRNO,ADDR1,ADDR2,TUMB,AUMPCOD,PROVCOD,ZIP,NAME,AUMPDES,convert(varchar(8),DDATE,112) as DDATE
				,DAMT,SDAMT,TDAMT,TPAY,TDAMT-TPAY as TTPAY1,TPAY-TDAMT as TTPAY2
			into #OFFICER
			FROM(
				select M.TMBILL,M.BILLNO,M.LOCATRECV,M.CUSCOD,M.PAYTYP,M.FLAG,T.PAYFOR,T.CONTNO,T.PAYAMT,T.DISCT,T.PAYINT
				,T.DSCINT,T.NETPAY * ".$PERSEN."/100 as COMAMT,T.NETPAY,T.PAYDT,T.F_PAY,T.L_PAY,A.TOTPRC - A.SMPAY - A.SMCHQ as BAL
				,A.LOCAT,A.STRNO,A.TOTPRC,A.SMPAY,A.SMCHQ,A.BILLCOLL,C.SNAM,C.NAME1,C.NAME2,P.FORDESC,E.ADDRNO,E.ADDR1
				,E.ADDR2,E.TUMB,E.AUMPCOD,E.PROVCOD,E.ZIP,F.NAME,G.AUMPDES
				,(select B.DDATE from {$this->MAuth->getdb('ARPAY')} B where (B.NOPAY = T.F_PAY) and (B.CONTNO = T.CONTNO)) as DDATE
				,(select B.DAMT from {$this->MAuth->getdb('ARPAY')} B where (B.NOPAY = T.F_PAY) and (B.CONTNO = T.CONTNO)) as DAMT
				,(select case when SUM(B.DAMT)is null then 0 else SUM(B.DAMT) end from {$this->MAuth->getdb('ARPAY')} B 
				where (B.NOPAY between T.F_PAY and T.L_PAY) and (B.CONTNO = T.CONTNO))as SDAMT
				,(select case when SUM(B.DAMT) is null then 0 else SUM(B.DAMT) end from {$this->MAuth->getdb('ARPAY')} B 
				where (B.NOPAY <= T.L_PAY)and (B.CONTNO = T.CONTNO)) as TDAMT

				,(select case when SUM(B.PAYAMT) is null then 0 else SUM(B.PAYAMT) end from {$this->MAuth->getdb('CHQTRAN')} B
				where (B.NOPAY <= T.L_PAY) and (B.CONTNO = T.CONTNO) and B.FLAG<>'C' and B.PAYFOR in ('006','007')) as TPAY  
				from {$this->MAuth->getdb('CHQMAS')} M
				left join {$this->MAuth->getdb('CHQTRAN')} T on M.TMBILL = T.TMBILL 
				left join {$this->MAuth->getdb('ARMAST')} A on A.CONTNO = T.CONTNO
				left join {$this->MAuth->getdb('CUSTMAST')} C on C.CUSCOD = M.CUSCOD
				left join {$this->MAuth->getdb('PAYFOR')} P on T.PAYFOR = P.FORCODE
				left join {$this->MAuth->getdb('OFFICER')} F on F.CODE = A.BILLCOLL
				left join {$this->MAuth->getdb('CUSTMAST')} D on D.CUSCOD = A.CUSCOD
				left join {$this->MAuth->getdb('CUSTADDR')} E on D.CUSCOD = E.CUSCOD and D.ADDRNO = E.ADDRNO
				left join {$this->MAuth->getdb('SETAUMP')} G on G.AUMPCOD = E.AUMPCOD and G.PROVCOD = E.PROVCOD 
				where (T.PAYFOR = '002' or T.PAYFOR = '006' or T.PAYFOR = '007') and (M.FLAG <> 'C' or M.FLAG is null) and
				M.LOCATRECV like '%".$LOCATRECV."%' and (A.LOCAT like '%".$LOCAT."%') and T.PAYDT between '".$DATE1."' and '".$DATE2."' 
				and (A.BILLCOLL like '%".$BILLCOLL."%')and (M.PAYTYP like '%".$PAYTYP."%') and (E.TUMB like '%".$TUMB."%' or E.TUMB is null) 
				and (E.AUMPCOD like '%".$AUMPCOD."%' or E.AUMPCOD is null) and (E.PROVCOD like '%".$PROVCOD."%' or E.PROVCOD is null) --order by T.CONTNO
			)OFFICER
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		$sql = "
			select *,case when TTPAY1<=0 then '0.00' else TTPAY1 end as DPAY1
			,case when TTPAY2<=0 then '0.00' else TTPAY2 end as DPAY2 from #OFFICER order by ".$order."
		";
		//echo $sql; exit;
		$query1 = $this->db->query($sql);
		$sql = "
			select COUNT(CONTNO) as countCONTNO,SUM(DAMT) as DAMT,SUM(PAYAMT) as PAYAMT,SUM(DISCT) as DISCT
			,SUM(PAYINT) as PAYINT,SUM(DSCINT) as DSCINT,SUM(NETPAY) as NETPAY 
			,sum(case when TTPAY1<=0 then '0.00' else TTPAY1 end) as DPAY1
			,sum(case when TTPAY2<=0 then '0.00' else TTPAY2 end) as DPAY2,SUM(BAL) as BAL,sum(COMAMT) as COMAMT from #OFFICER 
		";
		$query2 = $this->db->query($sql);
		$sql = "
			select CODE,NAME from {$this->MAuth->getdb('OFFICER')} where CODE = '".$BILLCOLL."'
		";
		$query3 = $this->db->query($sql);
		$BILL = "";
		if($query3->row()){
			foreach($query3->result() as $row){
				$BILL = $row->NAME;
			}
		}
		$sql = "
			select PAYCODE,PAYDESC from {$this->MAuth->getdb('PAYTYP')} where PAYCODE = '".$PAYTYP."'
		";
		$PAYDESC = "";
		$query4 = $this->db->query($sql);
		if($query4->row()){
			foreach($query4->result() as $row){
				$PAYDESC = $row->PAYDESC;
			}
		}
		$head = ""; $html = ""; $i = 0;
		$head = "
			<tr class='wm'>
				<td class='wf' style='height:1px;border-top:0.1px solid black;' colspan='13'></td>
			</tr>
			<tr>
				<th style='border-bottom:0.1px solid black;text-align:left;'>สาขารับชำระ<br>เลขที่สัญญา</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>เลขใบรับเงิน<br>ชื่อ - นามสกุล<br>พนักงานเก็บเงิน</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>เลขที่ใบเสร็จรับเงิน</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>วันดิวงวดนี้<br>เลขตัวถัง</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>วันที่รับชำระ</th> 
				<th style='border-bottom:0.1px solid black;text-align:left;'>งวดที่</th> 
				<th style='border-bottom:0.1px solid black;text-align:left;'>งวดที่ - งวดที่<br>ชำระค่า<br>ตำบล</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ค่างวดนี้</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ยอดหักลูกหนี้<br>ยอดค้าง ณ นี้<br>อำเภอ</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ส่วนลด</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>เบี้ยปรับ<br>ชำระล่วงหน้า ณ นี้<br>รหัสจังหวัด</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ส่วนลดเบี้ยปรับ<br><br>อัตราค่าคอม(%)</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ยอดรับสุทธิ<br>ลูกหนี้คงเหลือ<br><div style='color:red;'>ยอดคอมมิสชั่น</div></th>
			</tr>
			<tr>
				<td class='wf' style='height:1px;border-top:0.1px solid black;' colspan='13'></td>
			</tr>
		";
		if($query1->row()){
			foreach($query1->result() as $row){$i++;
				$html .= "
					<tr>
						<td style='width:90px;text-align:left;'>".$row->LOCATRECV."</td>
						<td style='width:90px;text-align:left;'>".$row->TMBILL."</td>
						<td style='width:70px;text-align:left;'>".$row->BILLNO."</td>
						<td style='width:70px;text-align:left;'>".$this->Convertdate(2,$row->DDATE)."</td>
						<td style='width:70px;text-align:left;'>".$this->Convertdate(2,$row->PAYDT)."</td> 
						<td style='width:70px;text-align:center;'>".$row->F_PAY."</td> 
						<td style='width:70px;text-align:left;'>".$row->F_PAY."-".$row->L_PAY."</td>
						<td style='width:70px;text-align:right;'>".number_format($row->DAMT,2)."</td>
						<td style='width:70px;text-align:right;'>".number_format($row->PAYAMT,2)."</td>
						<td style='width:70px;text-align:right;'>".number_format($row->DISCT,2)."</td>
						<td style='width:150px;text-align:right;color:#c0392b;'>".number_format($row->PAYINT,2)."</td>
						<td style='width:70px;text-align:right;'>".number_format($row->DSCINT,2)."</td>
						<td style='width:70px;text-align:right;color:blue;'>".number_format($row->NETPAY,2)."</td>
					</tr>
					<tr>
						<td style='width:90px;text-align:left;'>".$row->CONTNO."</td>
						<td style='width:90px;text-align:left;'colspan='2'>".$row->CUSNAME."</td>
						<td style='width:70px;text-align:left;'colspan='3'>".$row->STRNO."</td>
						<td style='width:150px;text-align:left;'colspan='2'>".$row->FORDESC."</td>
						<td style='width:90px;text-align:right;'>".number_format($row->DPAY1,2)."</td>
						<td style='width:90px;text-align:right;'colspan='2'>".number_format($row->DPAY2,2)."</td>
						<td style='width:90px;text-align:right;'colspan='2'>".number_format($row->BAL,2)."</td>
					</tr>
					<tr>
						<td style='width:90px;text-align:left;'></td>
						<td style='width:90px;text-align:left;color:#0963bd;'colspan='5'>".$row->BILLCOLL." ".$row->NAME."</td>
						<td style='width:70px;text-align:left;'colspan='2'>".$row->TUMB."</td>
						<td style='width:70px;text-align:right;'>".$row->AUMPDES."</td>
						<td style='width:90px;text-align:right;'colspan='2'>".$row->PROVCOD."</td>
						<td style='width:90px;text-align:right;color:blue;'>".number_format($PERSEN,2)."</td>
						<td style='width:90px;text-align:right;'>".number_format($row->COMAMT,2)."</td>
					</tr>
				";
			}
		}
		if($query2->row()){
			foreach($query2->result() as $row){
				$html .= "
					<tr class='wm'>
						<td class='wf' style='height:1px;border-top:0.1px solid black;' colspan='13'></td>
					</tr>
					<tr>
						<th style='width:90px;text-align:left;'>รวมทั้งสิ้น</th>
						<td style='width:90px;text-align:left;'>".$row->countCONTNO."</td>
						<th style='width:70px;text-align:left;'>รายการ</th>
						<td style='width:70px;text-align:right;' colspan='5'></td>
						<td style='width:70px;text-align:right;'>".number_format($row->PAYAMT,2)."</td>
						<td style='width:70px;text-align:right;'>".number_format($row->DISCT,2)."</td>
						<td style='width:70px;text-align:right;color:#c0392b;'>".number_format($row->PAYINT,2)."</td>
						<td style='width:70px;text-align:right;'>".number_format($row->DSCINT,2)."</td>
						<td style='width:70px;text-align:right;color:blue;'>".number_format($row->NETPAY,2)."</td>
					</tr>
					<tr>
						<td style='width:70px;text-align:right;' colspan='9'>".number_format($row->DPAY1,2)."</td>
						<td style='width:70px;text-align:right;' colspan='3'>".number_format($row->DPAY2,2)."</td>
						<td style='width:70px;text-align:right;'>".number_format($row->BAL,2)."<br>
						<div style='color:red;'>".number_format($row->COMAMT,2)."<div></td>
					</tr>
					<tr class='wm'>
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
		if($i > 0){
			$content = "
				<table class='wf' style='font-size:7.5pt;height:700px;border-collapse:collapse;line-height:23px;overflow:wrap;vertical-align:text-top;'>
					<tbody>
						<tr>
							<th colspan='13' style='font-size:10pt;text-align:center;'>บริษัท ตั้งใจพัฒนายานยนต์ จำกัด</th>
						</tr>
						<tr>
							<th colspan='13' style='font-size:9pt;'>รายงานการรับชำระตามพนักงานเก็บเงิน</th>
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
							<td style='text-align:center;' colspan='8'>
								<b>BILLCOLL</b>&nbsp;&nbsp;".$BILL."&nbsp;&nbsp;
								<b>ชำระโดย</b>&nbsp;&nbsp;".$PAYDESC."&nbsp;&nbsp;
							</td>
							<td style='text-align:right;' colspan='3'>RpRec51</td>
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