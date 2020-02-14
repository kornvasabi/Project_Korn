<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/********************************************************
             ______@03/02/2020______
			 Pasakorn Boonded

********************************************************/
class ReportCardDT extends MY_Controller {
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
							<br>การ์ดลูกหนี้และการชำระ<br>
						</div>
						<div class='col-sm-10 col-xs-10 col-sm-offset-1 text-primary'>	
							<br>
							<div class='col-sm-5'>	
								<div class='form-group'>
									เลขที่สัญญา
									<select id='CONTNO' class='form-control input-sm' data-placeholder='เลขที่สัญญา'></select>
								</div>
							</div>
							<div class='col-sm-2'>	
								<div class='form-group' >
									สาขา
									<input type='text' id='LOCAT' class='form-control input-sm' readonly>
								</div>
							</div>
							<div class='col-sm-5'>	
								<div class='form-group'>
									ชื่อ - นามสกุล
									<input type='text' id='CUSNAME' class='form-control input-sm' readonly>
								</div>
							</div>
						</div>
						<div class='col-sm-10 col-xs-10 col-sm-offset-1'><br>	
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group'>
									<div class='col-sm-12 col-xs-12' style='border:0.1px dotted #aed6f1;'>	
										<div class='col-sm-6'>
											<div class='form-group'>
												<br>
												<label>
													<input type= 'radio' id='SYD' name='show' checked> โชว์ดอกผล SYD
												</label>
											</div>
										</div>
										<div class='col-sm-6'>
											<div class='form-group'>
												<br>
												<label>
													<input type= 'radio' id='STR' name='show'> โชว์ดอกผล STR
												</label>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group'>
									<div class='col-sm-12 col-xs-12' style='border:0.1px dotted #aed6f1;'>	
										<div class='col-sm-6'>
											<div class='form-group'>
												<br>
												<label>
													<input type= 'radio' id='YSH' name='show1' checked> โชว์ดอกผล
												</label>
											</div>
										</div>
										<div class='col-sm-6'>
											<div class='form-group'>
												<br>
												<label>
													<input type= 'radio' id='NSH' name='show1'> ไม่โชว์ดอกผล
												</label>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class='col-sm-12 col-xs-12'>
								<br>
								<button id='btnreportCardDT' type='button' class='btn btn-info btn-outline btn-block' style='width:100%'><span class='fa fa-folder-open'><b>แสดง</b></span></button>
							</div>
						</div>
					</div>
				</div>
			</div>
		";
		$html .="<script src='".base_url('public/js/SYS06/ReportCardDT.js')."'></script>";
		echo $html;
	}
	function getCONTNO_D(){
		$response = array();
		$CONTNO = $_POST['CONTNO'];
		//echo $BIRTHDT;
		$sql = "
			select top 100 A.CONTNO,A.LOCAT,C.SNAM+C.NAME1+' '+C.NAME2 as CUSNAME 
			from {$this->MAuth->getdb('ARMAST')} A
			left join {$this->MAuth->getdb('CUSTMAST')} C on A.CUSCOD = C.CUSCOD
			left join {$this->MAuth->getdb('INVTRAN')} I on A.STRNO = I.STRNO 
			where A.CONTNO = '".$CONTNO."'
		";
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$response['LOCAT']   = $row->LOCAT;
				$response['CUSNAME'] = $row->CUSNAME;
			}
		}
		echo json_encode($response);
	}
	function conditiontopdf(){
		$data = array();
		$data[] = urlencode($_REQUEST["CONTNO"].'||'.$_REQUEST["LOCAT"].'||'.$_REQUEST["show1"].'||'.$_REQUEST['show2']);
		echo json_encode($this->generateData($data,"encode"));
	}
	function pdf(){
		$data = array();
		$data[] = $_REQUEST["condpdf"];
		$arrs = $this->generateData($data,"decode");
		$arrs[0] = urldecode($arrs[0]);
		
		$tx = explode('||',$arrs[0]);
		$CONTNO = $tx[0];
		$LOCAT  = $tx[1];
		$show1  = $tx[2];
		$show2  = $tx[3];
		$round = "";
		if($show1 == 'SYD'){
			$round = ",ROUND(ROUND(CAST(Z.N_DAMT as DEC(12, 2)) * CAST(Z.PAYDUE as DEC(12, 2)) / Z.DAMT, 2) * Z.NPROF / Z.N_DAMT, 2) as RCPROF--1";
		}else{
			$round = ",ROUND(CAST(Z.N_DAMT as DEC(12, 2)) * CAST(Z.NPROFIT_1 as DEC(12, 2)) / Z.NKANG_1, 2) as RCPROF";
		}
		$sql = "
			IF OBJECT_ID('tempdb..#RCDT') IS NOT NULL DROP TABLE #RCDT
			select CONVERT(varchar(8),SDATE,112) as SDATE,TOTPRC,STRNO,ENGNO,BILLCOLL,NPROFIT,KEYINUPAY,T_NOPAY,TOTDWN,NKANG,VKANG
				,TKANG,MEMO1,CONTNO,LOCAT,NOPAY,CONVERT(varchar(8),DDATE,112) as DDATE,DAMT,N_DAMT,V_DAMT,VATRT,NPROF,TMBILL
				,CONVERT(varchar(8),TMBILDT,112) as TMBILDT,BILLNO,CONVERT(varchar(8),BILLDT,112) as BILLDT,PAYAMT
				,PAYTYP,DSCAMTA,ARAMT,AARAMT,INTAMT,BARAMT,DISCT,PAYAMTA,PAYINT,DISINT,PAYAMTB,PAYDUE,CRE,PAYVAT
				,PAYNET,RCPROF,FLAG,ARBL,INTBL,NAME
			into #RCDT
			from(
				select M.SDATE,M.TOTPRC,M.STRNO,I.ENGNO,M.BILLCOLL,M.NPROFIT,M.KEYINUPAY,M.T_NOPAY,M.TOTDWN,M.NKANG,M.VKANG,M.TKANG,M.MEMO1  
					,T.* ,ARAMT-(case when PAYAMTA > 0 then PAYAMTA ELSE 0 end) + case when PAYDUE = 0 then 0 else 
					case when PAYDUE = DAMT then PAYAMTA - AARAMT  else case when PAYDUE < DAMT and AARAMT > PAYAMTA then 0 else  
					PAYAMTA - AARAMT  end  end  end as ARBL 
					,(INTAMT - (case when PAYINT is null then 0 ELSE PAYINT END)) as INTBL 
					,RTRIM(C.SNAM)+RTRIM(C.NAME1)+' '+RTRIM(C.NAME2) as NAME  
				from(select Z.* 
					,ROUND(CAST(Z.V_DAMT as DEC (12, 2)) * CAST(Z.PAYDUE as DEC(12, 2)) / Z.DAMT, 2) as PAYVAT
					,ROUND(CAST(Z.N_DAMT as DEC(12, 2)) * CAST(Z.PAYDUE as DEC(12, 2)) / Z.DAMT, 2) as PAYNET
					".$round."
					,1 as FLAG 
					from( 
						select Y. *,case when Y.PAYAMTA >= Y.AARAMT and Y.PAYAMTB <=  Y.BARAMT then Y.AARAMT - Y.BARAMT else 
							case when Y.PAYAMTA > Y.AARAMT and Y.PAYAMTB > Y.BARAMT then Y.AARAMT - Y.PAYAMTB else 
							case when Y.PAYAMTA <= Y.AARAMT and Y.PAYAMTB > Y.BARAMT then Y.PAYAMTA - Y.PAYAMTB else
							Y.PAYAMTA - Y.BARAMT end end end  as  PAYDUE
							,case when Y.PAYAMTA >= Y.AARAMT and Y.PAYAMTB <= Y.BARAMT then 1 else case when Y.PAYAMTA > Y.AARAMT 
							and Y.PAYAMTB > Y.BARAMT then 2 else case when Y.PAYAMTA < Y.AARAMT AND Y.PAYAMTB > Y.PAYAMT then 3 else 4 
							end end end   as  CRE  
						from(
							select X.CONTNO,X.LOCAT,X.NOPAY,X.DDATE,X.DAMT,X.N_DAMT,X.V_DAMT,X.VATRT,X.NPROF,D.TMBILL,D.TMBILDT,E.BILLNO
							,E.BILLDT,D.PAYAMT,D.PAYTYP,D.DISCT as DSCAMTA,(select  SUM(A.DAMT) from {$this->MAuth->getdb('ARPAY')} A 
							where A.CONTNO = X.CONTNO 
							and A.LOCAT = X.LOCAT) as ARAMT,(select  SUM(A.DAMT) from {$this->MAuth->getdb('ARPAY')} A where A.NOPAY <= X.NOPAY and A.CONTNO = X.CONTNO 
							and A.LOCAT = X.LOCAT) as AARAMT,(select SUM(A.INTAMT) from {$this->MAuth->getdb('ARPAY')} A where A.CONTNO = X.CONTNO 
							and A.LOCAT = X.LOCAT) as INTAMT
							
							,(case when (select SUM(A.DAMT) from {$this->MAuth->getdb('ARPAY')} A where A.CONTNO = X.CONTNO 
							and A.LOCAT = X.LOCAT and A.NOPAY < X.NOPAY) is null then 0 else (select SUM(A.DAMT) from {$this->MAuth->getdb('ARPAY')} A 
							where A.CONTNO = X.CONTNO and A.LOCAT = X.LOCAT and A.NOPAY < X.NOPAY) end) as BARAMT
							
							,(select A.NPROFIT from {$this->MAuth->getdb('ARMAST')} A where A.CONTNO = X.CONTNO and A.LOCAT = X.LOCAT ) as NPROFIT_1
							,(select A.NKANG from {$this->MAuth->getdb('ARMAST')} A where A.CONTNO = X.CONTNO and A.LOCAT = X.LOCAT ) as NKANG_1
							,(select SUM(A.DISCT) from {$this->MAuth->getdb('CHQTRAN')} A where  A.NOPAY <= D.NOPAY and A.TMBILDT<=D.TMBILDT and A.CONTNO = X.CONTNO 
							and A.LOCATPAY = X.LOCAT and A.PAYFOR in ('006','007') and A.FLAG <> 'C' and A.F_PAY > 0) as DISCT
							
							,(select SUM(A.PAYAMT) from {$this->MAuth->getdb('CHQTRAN')} A where A.NOPAY <= D.NOPAY and A.TMBILDT <= D.TMBILDT and A.CONTNO = X.CONTNO 
							and A.LOCATPAY = X.LOCAT and A.PAYFOR in ('006','007') and A.FLAG <> 'C' and A.F_PAY > 0) as PAYAMTA
							,(select SUM(A.PAYINT) from {$this->MAuth->getdb('CHQTRAN')} A where A.CONTNO = X.CONTNO and A.LOCATPAY = X.LOCAT  and A.PAYFOR 
							in ('006','007') and A.FLAG <> 'C' and A.F_PAY > 0) as PAYINT
							,(select SUM(A.DSCINT) from {$this->MAuth->getdb('CHQTRAN')} A where A.CONTNO = X.CONTNO and A.LOCATPAY = X.LOCAT and A.PAYFOR 
							in ('006','007') and A.FLAG <> 'C' and A.F_PAY > 0) as DISINT
							
							,(case when (
								select SUM(A.PAYAMT) from {$this->MAuth->getdb('CHQTRAN')}  A where A.CONTNO = X.CONTNO and A.LOCATPAY = X.LOCAT 
								and A.PAYFOR in ('006','007') and (A.NOPAY < D.NOPAY OR A.TMBILDT<D.TMBILDT) and A.FLAG <> 'C' 
								and A.F_PAY > 0
							)
							is null then 0 else (
								select SUM(A.PAYAMT) from {$this->MAuth->getdb('CHQTRAN')} A where A.CONTNO = X.CONTNO and A.LOCATPAY = X.LOCAT 
								and  A.PAYFOR in ('006','007') and (A.NOPAY < D.NOPAY or A.TMBILDT < D.TMBILDT) and A.FLAG <> 'C' 
								and A.F_PAY > 0
							)end) as PAYAMTB 
						from {$this->MAuth->getdb('ARPAY')} X
						left join {$this->MAuth->getdb('CHQTRAN')} D on X.CONTNO = D.CONTNO and X.LOCAT = D.LOCATPAY  
						left join {$this->MAuth->getdb('CHQMAS')} E on D.TMBILL = E.TMBILL 
						where X.NOPAY between D.F_PAY and D.L_PAY and D.FLAG <> 'C' and D.PAYFOR in ('006','007') and X.CONTNO = '".$CONTNO."' 
						group by X.CONTNO,X.LOCAT,X.NOPAY,X.DDATE,X.DAMT,X.N_DAMT,X.V_DAMT,X.NPROF,X.VATRT,D.TMBILL,D.TMBILDT,E.BILLNO
						,E.BILLDT,D.NOPAY,D.F_PAY,D.PAYAMT,D.PAYTYP,D.DISCT
						) as Y 
					)as Z 
					union 
					select X.CONTNO,X.LOCAT,X.NOPAY,X.DDATE,X.DAMT,X.N_DAMT,X.V_DAMT,X.VATRT,X.NPROF,'' as TMBILL,X.DATE1 as TMBILDT
						,'' as BILLNO,X.DATE1 as BILLDT,0 as PAYAMT,'' as PAYTYP,0 as DSCAMTA
						,(select SUM(A.DAMT) from {$this->MAuth->getdb('ARPAY')} A where A.CONTNO = X.CONTNO and A.LOCAT = X.LOCAT ) as ARAMT
						,(select SUM(A.DAMT) from {$this->MAuth->getdb('ARPAY')} A where A.NOPAY <= X.NOPAY and A.CONTNO = X.CONTNO and A.LOCAT = X.LOCAT) as AARAMT
						,(select SUM(A.INTAMT) from {$this->MAuth->getdb('ARPAY')} A where A.CONTNO = X.CONTNO and A.LOCAT = X.LOCAT ) as INTAMT
						,0 as BARAMT,0 as NPROFIT_1,0 as NKANG_1
						,(select SUM(A.DISCT) from {$this->MAuth->getdb('CHQTRAN')} A where A.CONTNO = X.CONTNO and A.LOCATPAY = X.LOCAT and A.PAYFOR  
						in ('006','007') and A.FLAG <> 'C' and A.F_PAY > 0) as DISCT
						,(select SUM(A.PAYAMT) from {$this->MAuth->getdb('CHQTRAN')} A where A.CONTNO = X.CONTNO and A.LOCATPAY = X.LOCAT and A.PAYFOR  
						in ('006','007') and A.FLAG <> 'C' and A.F_PAY > 0) as PAYAMTA 
						,(select SUM(A.PAYINT) from  {$this->MAuth->getdb('CHQTRAN')} A where A.CONTNO = X.CONTNO and A.LOCATPAY = X.LOCAT and A.PAYFOR  
						in ('006','007') and A.FLAG <> 'C' and A.F_PAY > 0) as PAYINT
						,(select SUM(A.DSCINT) from {$this->MAuth->getdb('CHQTRAN')} A where A.CONTNO = X.CONTNO and A.LOCATPAY = X.LOCAT and A.PAYFOR  
						in ('006','007') and A.FLAG <> 'C' and A.F_PAY > 0) as DISINT
						,0 as PAYAMTB,0 as PAYDUE,0 as CRE,0 as PAYVAT,0 as PAYNET,0 as RCPROF,2 as FLAG 
					from {$this->MAuth->getdb('ARPAY')} X where X.PAYMENT = 0 and X.CONTNO = '".$CONTNO."'
				)as T
				,ARMAST M,INVTRAN I,CUSTMAST C where T.CONTNO = M.CONTNO and T.LOCAT = M.LOCAT and M.CUSCOD = C.CUSCOD and M.STRNO = I.STRNO  
				--ORDER BY T.CONTNO,T.LOCAT,T.NOPAY,T.FLAG,T.TMBILDT,T.TMBILL
			)RCDT
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		$sql = "
			select * from #RCDT order by CONTNO,LOCAT,NOPAY,FLAG,TMBILDT,TMBILL
		";
		$query1 = $this->db->query($sql);
		$sql = "
			select top 1 CONVERT(varchar(8),SDATE,112) as SDATE,TOTPRC,TOTDWN,T_NOPAY,KEYINUPAY
			,NAME,STRNO,ENGNO,BILLCOLL,NPROFIT from #RCDT
		";
		$query2 = $this->db->query($sql); 
		$sql = "
			select MEMO1 from {$this->MAuth->getdb('ARMAST')} where CONTNO = '".$CONTNO."' and LOCAT = '".$LOCAT."'
		";
		$query3 = $this->db->query($sql);
		$memo1 = "";
		if($query3->row()){
			foreach($query3->result() as $row){
				$memo1 = $row->MEMO1;
			}
		}
		$sql = "
			select SUM(PAYDUE) as PAYDUE,SUM(PAYNET) as PAYNET,SUM(PAYVAT) as PAYVAT
			,SUM(RCPROF) as RCPROF from #RCDT 
		";
		$query4 = $this->db->query($sql);
		$sql = "
			declare @A varchar(4) = (select coalesce(MAX(NOPAY),0) from {$this->MAuth->getdb('ARPAY')} where CONTNO = '".$CONTNO."');
			declare @B varchar(5) = (select coalesce(MAX(NOPAY),0) from {$this->MAuth->getdb('ARPAY')} where CONTNO = '".$CONTNO."' 
			and ((PAYMENT = DAMT)or(PAYMENT > 0)));

			select @B+'/'+@A as countNOPAY	
		";
		$query5 = $this->db->query($sql);
		//echo $sql; exit;
		$head = ""; $html = ""; $i = 0;
		$rcprof = "";
		if($show2 == "Y"){
			$rcprof = "รับดอกผล";
		}else{
			$rcprof = "";
		}
		$head = "
			<tr class='wm'>
				<td class='wf' style='height:1px;border-top:0.1px solid black;' colspan='17'></td>
			</tr>
			<tr>
				<th style='border-bottom:0.1px solid black;text-align:left;'>งวดที่</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>วันดิว</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>จำนวนเงินตามดิว</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>มูลค่าสินค้า</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ภาษี</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>เลขที่ใบรับ</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>วันที่ใบรับ</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>เลขที่ใบเสร็จ</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>วันชำระ</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>อัตราภาษี</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ชำระโดย</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>จำนวนชำระ</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>มูลค่า</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ภาษี</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ส่วนลด</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>".$rcprof."</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ลูกหนี้คงเหลือ</th>
			</tr>
			<tr>
				<td class='wf' style='height:1px;border-top:0.1px solid black;' colspan='17'></td>
			</tr>
		";
		if($query1->row()){
			foreach($query1->result() as $row){$i++;
				$rcprof2 = "";
				if($show2 == "Y"){
					$rcprof2 = number_format($row->RCPROF,2);
				}else{
					$rcprof2 = "";
				}
				$sql1 = "select DISTINCT NOPAY from #RCDT";
				$query1 = $this->db->query($sql1);
				$nopay = "";
				if($query1->row()){
					foreach($query1->result() as $row1){
						$nopay = $row1->NOPAY;
					}
				}
				$html .="
					<tr>
						<td style='width:70px;text-align:left;'>".$row->NOPAY."</td>
						<td style='width:70px;text-align:left;'>".$this->Convertdate(2,$row->DDATE,2)."</td>
						<td style='width:100px;text-align:right;'>".number_format($row->DAMT,2)."</td>
						<td style='width:70px;text-align:right;'>".number_format($row->N_DAMT,2)."</td>
						<td style='width:70px;text-align:right;'>".number_format($row->V_DAMT,2)."</td>
						<td style='width:90px;text-align:right;'>".$row->TMBILL."</td>
						<td style='width:70px;text-align:right;'>".$this->Convertdate(2,$row->TMBILDT)."</td>
						<td style='width:80px;text-align:right;'>".$row->BILLNO."</td>
						<td style='width:70px;text-align:right;'>".$this->Convertdate(2,$row->BILLDT)."</td>
						<td style='width:70px;text-align:right;'>".number_format($row->VATRT,2)."</td>
						<td style='width:70px;text-align:right;'>".$row->PAYTYP."</td>
						<td style='width:70px;text-align:right;'>".number_format($row->PAYDUE,2)."</td>
						<td style='width:70px;text-align:right;'>".number_format($row->PAYNET,2)."</td>
						<td style='width:70px;text-align:right;'>".number_format($row->PAYVAT,2)."</td>
						<td style='width:70px;text-align:right;'>".number_format($row->DISCT,2)."</td>
						<td style='width:70px;text-align:right;'>".$rcprof2."</td>
						<td style='width:70px;text-align:right;'>".number_format($row->ARBL,2)."</td>
					</tr>
				";
			}
		}
		if($query4->row()){
			foreach($query4->result() as $row){
				$rcprof3 = "";
				if($show2 == "Y"){
					$rcprof3 = number_format($row->RCPROF,2);
				}else{
					$rcprof3 = "";
				}
				$arrs = array();
				$sql1 = "
					select NKANG,VKANG,TKANG from ARMAST where CONTNO = '".$CONTNO."'
				";
				$query1 = $this->db->query($sql1);
				if($query1->row()){
					foreach($query1->result() as $row1){
						$arrs['NKANG'] = $row1->NKANG;
						$arrs['TKANG'] = $row1->TKANG;
						$arrs['VKANG'] = $row1->VKANG;
					}
				}
				$html .="
					<tr class='wm'>
						<td class='wf' style='height:1px;border-top:0.1px solid black;' colspan='17'></td>
					</tr>
					<tr>
						<td style='width:70px;text-align:center;'><b>รวมทั้งสิ้น</b></td>
						<td style='width:70px;text-align:right;'colspan='2'>".number_format($arrs['NKANG'],2)."</td>
						<td style='width:70px;text-align:right;'>".number_format($arrs['TKANG'],2)."</td>
						<td style='width:70px;text-align:right;'>".number_format($arrs['VKANG'],2)."</td>
						<td style='width:70px;text-align:right;'><b>ชำระถึงงวดที่</b></td>
						<td style='width:90px;text-align:right;'><b>มูลค่าคงเหลือจริง</b></td>
						<td style='width:70px;text-align:right;'><b>ภาษีคงเหลือจริง</b></td>
						<td style='width:90px;text-align:right;'><b>ลูกหนี้คงเหลือ</b></td>
						
						<td style='width:70px;text-align:right;' colspan='3'>".number_format($row->PAYDUE,2)."</td>
						<td style='width:70px;text-align:right;'>".number_format($row->PAYNET,2)."</td>
						<td style='width:70px;text-align:right;'>".number_format($row->PAYVAT,2)."</td>
						<td style='width:70px;text-align:right;'colspan='2'><b>".$rcprof3."</b></td>
					</tr>
				";
			}
		}
		if($query5->row()){
			foreach($query5->result() as $row){
				$arrs = array();
				$sql1 = "
					select NKANG,VKANG,TKANG,EXP_AMT from {$this->MAuth->getdb('ARMAST')} where CONTNO = '".$CONTNO."'
				";
				$query1 = $this->db->query($sql1);
				if($query1->row()){
					foreach($query1->result() as $row1){
						$arrs['EXP_AMT'] = $row1->EXP_AMT;
						$arrs['VKANG']   = $row1->VKANG;
					}
				}
				$sql3 = "
					select top 1 INTAMT,PAYINT,DISINT,INTBL from #RCDT 
				";
				$query3 = $this->db->query($sql3);
				if($query3->row()){
					foreach($query3->result() as $row3){
						$arrs['INTAMT'] = $row3->INTAMT;
						$arrs['PAYINT'] = $row3->PAYINT;
						$arrs['DISINT'] = $row3->DISINT;
						$arrs['INTBL']  = $row3->INTBL;
					}
				}
				$html .="
					<tr>
						<td style='width:70px;text-align:right;' colspan='6'>".$row->countNOPAY."</td>
						<td style='width:70px;text-align:right;'>".number_format($arrs['EXP_AMT'],2)."</td>
						<td style='width:70px;text-align:right;'>".number_format($arrs['VKANG'],2)."</td>
						<td style='width:70px;text-align:right;'>".number_format($arrs['EXP_AMT'],2)."</td>
						<td style='width:70px;text-align:right;'><b>ยอดเบี้ยปรับ</b></td>
						<td style='width:70px;text-align:right;'>".number_format($arrs['INTAMT'],2)."</td>
						<td style='width:60px;text-align:right;'><b>ชำระแล้ว</b></td>
						<td style='width:60px;text-align:right;'>".number_format($arrs['PAYINT'],2)."</td>
						<td style='width:60px;text-align:right;'><b>ส่วนลด</b></td>
						<td style='width:60px;text-align:right;'>".number_format($arrs['DISINT'],2)."</td>
						<td style='width:110px;text-align:right;'><b>เบี้ยปรับคงเหลือ</b></td>
						<td style='width:60px;text-align:right;'>".number_format($arrs['INTBL'],2)."</td>
					</tr>
					<tr class='wm'>
						<td class='wf' style='height:1px;border-top:0.1px solid black;' colspan='17'></td>
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
			if($query2->row()){
				foreach($query2->result() as $row){
					$content = "
						<table class='wf' style='font-size:8pt;height:700px;border-collapse:collapse;line-height:23px;overflow:wrap;vertical-align:text-top;'>
							<tbody>
								<tr>
									<th colspan='17' style='font-size:10pt;'>บริษัท ตั้งใจพัฒนายานยนต์ จำกัด</th>
								</tr>
								<tr>
									<th colspan='17' style='font-size:9pt;'>รายงานการ์ดลูกหนี้และการชำระ</th>
								</tr>
								<tr>
									<td style='text-align:left;' colspan='3'>
										<b>เลขที่สัญญา</b> &nbsp;&nbsp;".$CONTNO."&nbsp;&nbsp;
									</td>
									<td style='text-align:left;' colspan='3'>
										<b>วันทำสัญญา</b> &nbsp;&nbsp;".$this->Convertdate(2,$row->SDATE)."&nbsp;&nbsp;
									</td>
									<td style='text-align:left;' colspan='3'>
										<b>ราคาขาย</b> &nbsp;&nbsp;".number_format($row->TOTPRC,2)."&nbsp;&nbsp;<b>บาท</b>
									</td>
									<td style='text-align:left;' colspan='3'>
										<b>เงินดาวน์</b> &nbsp;&nbsp;".number_format($row->TOTDWN,2)."&nbsp;&nbsp;<b>บาท</b>
									</td>
									<td style='text-align:left;' colspan='3'>
										<b>ผ่อนจำนวน</b> &nbsp;&nbsp;".$row->T_NOPAY."&nbsp;&nbsp;<b>งวด</b>
									</td>
									<td style='text-align:left;' colspan='3'>
										<b>งวดละ</b> &nbsp;&nbsp;".number_format($row->KEYINUPAY,2)."&nbsp;&nbsp;<b>บาท</b>
									</td>
								</tr>
								<tr>
									<td style='text-align:left;' colspan='4'>
										<b>ชื่อ - นามสกุล</b> &nbsp;&nbsp;".$row->NAME."&nbsp;&nbsp;
									</td>
									<td style='text-align:left;' colspan='3'>
										<b>หมายเลขเครื่อง</b> &nbsp;&nbsp;".$row->ENGNO."&nbsp;&nbsp;
									</td>
									<td style='text-align:left;' colspan='3'>
										<b>เลขตัวถัง</b> &nbsp;&nbsp;".$row->STRNO."&nbsp;&nbsp;
									</td>
									<td style='text-align:left;' colspan='3'>
										<b>พนักงานเก็บเงิน</b> &nbsp;&nbsp;".$row->BILLCOLL."&nbsp;&nbsp;
									</td>
									<td style='text-align:left;' colspan='3'>
										<b>ดอกผลเช่าซื้อ</b> &nbsp;&nbsp;".number_format($row->NPROFIT,2)."&nbsp;&nbsp;<b>บาท</b>
									</td>
								</tr>
								<tr>
									<td style='text-align:left;' colspan='17'><b>หมายเหตุ : &nbsp;&nbsp;</b>".$memo1."</td>
								</tr>
								<tr>
									<td style='text-align:right;' colspan='17'>RpCrdpy10,11</td>
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
				}
			}
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