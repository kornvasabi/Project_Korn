<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/********************************************************
             ______@19/08/2020______
			 Pasakorn Boonded

********************************************************/
class ReportDebtAtDate extends MY_Controller {
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
				<div class='col-sm-12 col-xs-12' style='background-color:#0480E0;border:5px solid white;height:75px;text-align:center;font-size:12pt;color:white;font-weight:bold;'>	
					<br>รายงานสรุปสภาพหนี้ ณ วันที่<br>
				</div>
				<div class='col-sm-8 col-xs-8 col-sm-offset-2'>
					<div class='col-sm-6 col-xs-6'>
						<div class='form-group'>
							<div class='col-sm-12'>	
								<div class='form-group'>
									รหัสพนักงานเก็บเงิน
									<select id='LOCAT' class='form-control input-sm'>
										<option value='{$this->sess['branch']}'>{$this->sess['branch']}</option>
									</select>
								</div>
								<!--div class='form-group'>
									สาขา
									<div class='input-group'>
										<input type='text' id='add_locat' LOCAT='' class='form-control input-sm' placeholder='สาขา' >
										<span class='input-group-btn'>
											<button id='btnaddlocat' class='btn btn-info btn-sm' type='button'>
												<span class='glyphicon glyphicon-hand-up' aria-hidden='true'></span>
											</button>
											<button id='removeadd' class='btn btn-danger btn-sm' type='button'>
												<span class='glyphicon glyphicon-remove' aria-hidden='true'></span>
											</button>
										</span>
									</div>
								</div-->
							</div>
							<div class='col-sm-12'>	
								<div class='form-group'>
									รหัสพนักงานเก็บเงิน
									<select id='BILLCOLL' class='form-control input-sm'></select>
								</div>
							</div>						
						</div>
					</div>
					<div class='col-sm-6 col-xs-6'>
						<div class='form-group'>
							<div class='col-sm-12'>	
								<div class='form-group'>
									ลูกหนี้ ณ วันที่
									<input type='text' id='ATDATE' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' value='".$this->today('today')."' styl='font-size:10.5pt;'>
								</div>
							</div>					
						</div>
					</div>
				</div>
				<div class='col-sm-8 col-xs-8 col-sm-offset-2'>
					<br>
					<button id='btnreport' type='button' class='btn btn-info btn-outline btn-block' style='height:40px;'><span class='fa fa-folder-open'><b>แสดง</b></span></button>
				</div>
				<div class='col-sm-12'>
					<div class='col-sm-12'>
						<div id='result'></div>
					</div>
				</div>
			</div>
		";
		$html .="<script src='".base_url('public/js/SYS09/ReportDebtAtDate.js')."'></script>";
		echo $html;
	}
	function conditiontopdf(){
		$data   = array();
		$data[] = urlencode(
			$_REQUEST['LOCAT'].'||'.$_REQUEST['BILLCOLL'].'||'.$_REQUEST['ATDATE']
		); 
		echo json_encode($this->generateData($data,"encode"));
	}
	function pdf(){
		$data   = array();
		$data[] = $_GET["condpdf"];
		$arrs   = $this->generateData($data,"decode");
		$arrs[0]= urldecode($arrs[0]);
		$tx     = explode("||",$arrs[0]);
		
		$LOCAT    = $tx[0];
		$BILLCOLL = $tx[1];
		$ATDATE   = $this->Convertdate(1,$tx[2]);
		
		$sql = "
			select COMP_NM from {$this->MAuth->getdb('CONDPAY')}
		";
		$query = $this->db->query($sql);
		$row1		= $query->row();
		$COMP_NM 	= $row1->COMP_NM;
		
		$head = ""; $html = ""; $i=0; 
		$head = "
			<tr>
				<th width='40px'  align='center' style='border-top:0.1px solid black;vertical-align:top;'>No.</th>
				<th width='150px' align='left'	 style='border-top:0.1px solid black;vertical-align:top;'>รหัสพนักงานเก็บเงิน</th>
				<th width='200px' align='left'   style='border-top:0.1px solid black;vertical-align:top;'>ชื่อ - สกุล</th>
				<th width='90px'  align='left' 	 style='border-top:0.1px solid black;vertical-align:top;'>ไม่ค้างชำระ</th>
				<th width='180px' align='center' style='border-top:0.1px solid black;vertical-align:top;'colspan='2'>ค้างชำระ 1 งวด</th>
				<th width='180px' align='center' style='border-top:0.1px solid black;vertical-align:top;'colspan='2'>ค้างชำระ 2 งวด</th>
				<th width='180px' align='center' style='border-top:0.1px solid black;vertical-align:top;'colspan='2'>ค้างชำระ 3 งวด</th>
				<th width='180px' align='center' style='border-top:0.1px solid black;vertical-align:top;'colspan='2'>ค้างชำระ >= 4 งวด</th>
				<th width='180px' align='center' style='border-top:0.1px solid black;vertical-align:top;'colspan='2'>รวมค้างชำระ</th>
			</tr>
			<tr>
				<th width='40px'  align='center' style='border-bottom:0.1px solid black;vertical-align:top;'></th>
				<th width='150px' align='left'	 style='border-bottom:0.1px solid black;vertical-align:top;'></th>
				<th width='200px' align='left'   style='border-bottom:0.1px solid black;vertical-align:top;'></th>
				<th width='90px'  align='left' 	 style='border-bottom:0.1px solid black;vertical-align:top;'>จำนวนราย</th>
				<th width='90px'  align='right'  style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;'>จำนวนราย</th>
				<th width='90px'  align='right'  style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;'>จำนวนเงิน</th>
				<th width='90px'  align='right'  style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;'>จำนวนราย</th>
				<th width='90px'  align='right'  style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;'>จำนวนเงิน</th>
				<th width='90px'  align='right'  style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;'>จำนวนราย</th>
				<th width='90px'  align='right'  style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;'>จำนวนเงิน</th>
				<th width='90px'  align='right'  style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;'>จำนวนราย</th>
				<th width='90px'  align='right'  style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;'>จำนวนเงิน</th>
				<th width='90px'  align='right'  style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;'>จำนวนราย</th>
				<th width='90px'  align='right'  style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;'>จำนวนเงิน</th>
			</tr>
		";
		$sql = "
			select 
				B.CODE,B.NAME,A.LOCAT 
			from {$this->MAuth->getdb('ARMAST')} A,{$this->MAuth->getdb('OFFICER')} B 
			where A.BILLCOLL = B.CODE and A.LOCAT like '".$LOCAT."%' and A.SDATE <= '".$ATDATE."' 
			and A.TOTPRC > 0 and (A.BILLCOLL like '".$BILLCOLL."%') and ((LPAYD > '".$ATDATE."' 
			and TOTPRC = SMPAY ) or (TOTPRC > SMPAY)) 
			group by B.CODE,B.NAME,A.LOCAT 
			union  
			select 
				A.BILLCOLL as CODE,'' as NAME,A.LOCAT  
			from {$this->MAuth->getdb('ARMAST')} A 
			where A.BILLCOLL not in (SELECT CODE FROM {$this->MAuth->getdb('OFFICER')}) 
			and  A.LOCAT like '".$LOCAT."%' and A.SDATE <= '".$ATDATE."' and (A.BILLCOLL like '".$BILLCOLL."%') 
			and ((LPAYD > '".$ATDATE."' and TOTPRC = SMPAY) or (TOTPRC > SMPAY))  
			group by A.BILLCOLL,A.LOCAT 
			union  
			select 
				B.CODE,B.NAME,A.LOCAT 
			from {$this->MAuth->getdb('HARMAST')} A,{$this->MAuth->getdb('OFFICER')} B 
			where A.BILLCOLL = B.CODE and A.LOCAT like '".$LOCAT."%' and A.SDATE <= '".$ATDATE."' 
			and A.TOTPRC > 0 and A.BILLCOLL like '".$BILLCOLL."%' and ((A.LPAYD > '".$ATDATE."' 
			and A.TOTPRC = A.SMPAY ) 
			or (A.YDATE > '".$ATDATE."' and A.TOTPRC > A.SMPAY)) 
			group by B.CODE,B.NAME,A.LOCAT 
			union  
			select A.BILLCOLL AS CODE,'' AS NAME,A.LOCAT   
			from {$this->MAuth->getdb('HARMAST')} A 
			where A.BILLCOLL not in (SELECT CODE FROM {$this->MAuth->getdb('OFFICER')}) 
			and  A.LOCAT like '".$LOCAT."%' 
			and A.SDATE <= '".$ATDATE."' and A.TOTPRC > 0 and A.BILLCOLL like '".$BILLCOLL."%' and (
				(A.LPAYD > '".$ATDATE."' and A.TOTPRC >= A.SMPAY) or (A.YDATE > '".$ATDATE."' 
				and A.TOTPRC > A.SMPAY)
			) 
			group by A.BILLCOLL,A.LOCAT 
			order by B.CODE  
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){$i++;
				$sql2 = "
					IF OBJECT_ID('tempdb..#temppay') IS NOT NULL DROP TABLE #temppay
					select *
					into #temppay
					FROM(
						select CONTNO,LOCAT from {$this->MAuth->getdb('ARMAST')}
						where BILLCOLL = '".str_replace(chr(0),'',$row->CODE)."' and LOCAT like '".$row->LOCAT."%' 
						and SDATE <= '".$ATDATE."' and TOTPRC > 0  
						and ((LPAYD > '".$ATDATE."' and TOTPRC = SMPAY) or (TOTPRC > SMPAY))  
						union 
						select CONTNO,LOCAT from {$this->MAuth->getdb('HARMAST')}  
						where BILLCOLL = '".str_replace(chr(0),'',$row->CODE)."' and LOCAT like '".$row->LOCAT."%' 
						and SDATE <= '".$ATDATE."' and TOTPRC > 0  
						and ((LPAYD > '".$ATDATE."' and TOTPRC = SMPAY) or (YDATE > '".$ATDATE."' and TOTPRC > SMPAY)) 
						--ORDER BY CONTNO
					)pay
				";
				//echo $sql2; exit;
				$this->db->query($sql2);
				$sql3 = "
					IF OBJECT_ID('tempdb..#temppay2') IS NOT NULL DROP TABLE #temppay2
					select *
					into #temppay2
					FROM(
						select distinct aa.CONTNO,
							case when A.CONTNO <> '' and H.CONTNO <> '' then CAST(ROUND((aa.DAMT1 - aa.PAYMENT) / aa.DAMT, 0) as decimal(2,0)) else 0 end as COUNTCONT
							,aa.DAMT1 - aa.PAYMENT as TOTMON
							,case when A.CONTNO <> '' and H.CONTNO <> '' then aa.DAMT1 - aa.PAYMENT else 0 end as TOTMON2
						from (
							select CONTNO,DAMT
							,SUM(CASE WHEN DDATE <= '".$ATDATE."' THEN  DAMT  ELSE 0 END) AS DAMT1  
								,sum(DAMT) AS DAMT2 
								,(
									select 
										SUM( CASE WHEN (PAYFOR = '006' or PAYFOR = '007') THEN PAYAMT ElSE 0 END) AS NETPAY  
									from {$this->MAuth->getdb('CHQTRAN')} where PAYDT <= '".$ATDATE."' and CONTNO = a.CONTNO 
									and LOCATPAY = '".$row->LOCAT."' AND FLAG <> 'C'  
								) as PAYMENT
							from {$this->MAuth->getdb('ARPAY')} a 
							where CONTNO in (select CONTNO from #temppay) and LOCAT = '".$row->LOCAT."'  
							group by CONTNO,DAMT
							union
							select CONTNO,DAMT
							,SUM(CASE WHEN DDATE <= '".$ATDATE."' THEN  DAMT  ELSE 0 END) AS DAMT1  
								,sum(DAMT) AS DAMT2 
								,(
									select 
										SUM( CASE WHEN (PAYFOR = '006' or PAYFOR = '007') THEN PAYAMT ElSE 0 END) AS NETPAY  
									from {$this->MAuth->getdb('CHQTRAN')} where PAYDT <= '".$ATDATE."' and CONTNO = h.CONTNO 
									and LOCATPAY = '".$row->LOCAT."' AND FLAG <> 'C'  
								) as PAYMENT
							from {$this->MAuth->getdb('HARPAY')} h 
							where CONTNO in (select CONTNO from #temppay) and LOCAT = '".$row->LOCAT."'  
							group by CONTNO,DAMT
						)aa
						left join {$this->MAuth->getdb('ARPAY')} A on aa.CONTNO = A.CONTNO
						left join {$this->MAuth->getdb('HARPAY')} H on aa.CONTNO = H.CONTNO
					)pay2
				";
				//echo $sql3;
				$this->db->query($sql3);
				$sql4 = "
					select a.*
						 ,(select COUNT(CONTNO) from #temppay)-(a.C01+a.C02+a.C03+a.C04) as NOSTALE
						 ,(a.C01+a.C02+a.C03+a.C04) as TOTCOUNT
						 ,(a.P01+a.P02+a.P03+a.P04) as TOTPRICE
					from (
						select 
							(select ISNULL(COUNT(TOTMON2),0) from #temppay2 where COUNTCONT = 1)  as C01
							,(select ISNULL(SUM(TOTMON2),0) from #temppay2 where COUNTCONT = 1)   as P01
							,(select ISNULL(COUNT(TOTMON2),0) from #temppay2 where COUNTCONT = 2) as C02
							,(select ISNULL(SUM(TOTMON2),0) from #temppay2 where COUNTCONT = 2)   as P02
							,(select ISNULL(COUNT(TOTMON2),0) from #temppay2 where COUNTCONT = 3) as C03 
							,(select ISNULL(SUM(TOTMON2),0) from #temppay2 where COUNTCONT = 3)   as P03
							,(select ISNULL(COUNT(TOTMON2),0) from #temppay2 where COUNTCONT >= 4)as C04
							,(select ISNULL(SUM(TOTMON2),0) from #temppay2 where COUNTCONT >= 4)  as P04
					)a
				";
				//echo $sql4; exit;
				$query4 = $this->db->query($sql4);
				if($query4->row()){
					foreach($query4->result() as $row4){
						$arrs['NOSTALE'][] = $row4->NOSTALE;
						$arrs['C01'][]     = $row4->C01;
						$arrs['P01'][]     = $row4->P01;
						$arrs['C02'][]     = $row4->C02;
						$arrs['P02'][]     = $row4->P02;
						$arrs['C03'][]     = $row4->C03;
						$arrs['P03'][]     = $row4->P03;
						$arrs['C04'][]     = $row4->C04;
						$arrs['P04'][]     = $row4->P04;
						$arrs['TOTCOUNT'][]= $row4->TOTCOUNT;
						$arrs['TOTPRICE'][]= $row4->TOTPRICE;
						
						$html .="
							<tr>
								<td width='40px'  height='40px'	align='center'>".$i."</td>
								<td width='150px' height='40px'	align='left'>".$row->CODE."</td>
								<td width='200px' height='40px'	align='left'>".$row->NAME."</td>
								<td width='90px'  height='40px'	align='center'>".$row4->NOSTALE."</td>
								<td width='90px'  height='40px'	align='right'>".$row4->C01."</td>
								<td width='90px'  height='40px'	align='right'>".number_format($row4->P01,2)."</td>
								<td width='90px'  height='40px'	align='right'>".$row4->C02."</td>
								<td width='90px'  height='40px'	align='right'>".number_format($row4->P02,2)."</td>
								<td width='90px'  height='40px'	align='right'>".$row4->C03."</td>
								<td width='90px'  height='40px'	align='right'>".number_format($row4->P03,2)."</td>
								<td width='90px'  height='40px'	align='right'>".$row4->C04."</td>
								<td width='90px'  height='40px'	align='right'>".number_format($row4->P04,2)."</td>
								<td width='90px'  height='40px'	align='right'>".$row4->TOTCOUNT."</td>
								<td width='90px'  height='40px'	align='right'>".number_format($row4->TOTPRICE,2)."</td>
							</tr>
						";
					}
				}
			}
		}
		if($i > 0){
			$html .= "
				<tr class='trow' style='background-color:#ebebeb;'>
					<th width='40px'  height='40px'	align='center' style='border-top:0.1px solid black;vertical-text:center;border-bottom:0.1px solid black;' ></th>
					<th width='150px' height='40px'	align='center' style='border-top:0.1px solid black;vertical-text:center;border-bottom:0.1px solid black;' >รวมทั้งสิ้น</th>
					<th width='200px' height='40px'	align='center' style='border-top:0.1px solid black;vertical-text:center;border-bottom:0.1px solid black;' ></th>
					<td width='90px'  height='40px'	align='center' style='border-top:0.1px solid black;vertical-text:center;border-bottom:0.1px solid black;' >".array_sum($arrs['NOSTALE'])."</td>
					<td width='90px'  height='40px'	align='right'  style='border-top:0.1px solid black;vertical-text:center;border-bottom:0.1px solid black;' >".array_sum($arrs['C01'])."</td>
					<td width='90px'  height='40px'	align='right'  style='border-top:0.1px solid black;vertical-text:center;border-bottom:0.1px solid black;' >".number_format(array_sum($arrs['P01']),2)."</td>
					<td width='90px'  height='40px'	align='right'  style='border-top:0.1px solid black;vertical-text:center;border-bottom:0.1px solid black;' >".array_sum($arrs['C02'])."</td>
					<td width='90px'  height='40px'	align='right'  style='border-top:0.1px solid black;vertical-text:center;border-bottom:0.1px solid black;' >".number_format(array_sum($arrs['P02']),2)."</td>
					<td width='90px'  height='40px'	align='right'  style='border-top:0.1px solid black;vertical-text:center;border-bottom:0.1px solid black;' >".array_sum($arrs['C03'])."</td>
					<td width='90px'  height='40px'	align='right'  style='border-top:0.1px solid black;vertical-text:center;border-bottom:0.1px solid black;' >".number_format(array_sum($arrs['P03']),2)."</td>
					<td width='90px'  height='40px'	align='right'  style='border-top:0.1px solid black;vertical-text:center;border-bottom:0.1px solid black;' >".array_sum($arrs['C04'])."</td>
					<td width='90px'  height='40px'	align='right'  style='border-top:0.1px solid black;vertical-text:center;border-bottom:0.1px solid black;' >".number_format(array_sum($arrs['P04']),2)."</td>
					<td width='90px'  height='40px'	align='right'  style='border-top:0.1px solid black;vertical-text:center;border-bottom:0.1px solid black;' >".array_sum($arrs['TOTCOUNT'])."</td>
					<td width='90px'  height='40px'	align='right'  style='border-top:0.1px solid black;vertical-text:center;border-bottom:0.1px solid black;' >".number_format(array_sum($arrs['TOTPRICE']),2)."</td>
				</tr>
			";
		}
		
		$body = "<table class='fs9' cellspacing='0'>".$html."</table>";
		
		$mpdf = new \Mpdf\Mpdf([
			'mode' => 'utf-8', 
			'format' => 'A4-L',
			'margin_top' => 45, 	//default = 16
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
				.fs9 { font-size:9pt; }
				.h30 { height:30px; }
				.bor { border-top:0.1px solid black;border-bottom:0.1px solid black;}
			</style>
		";
		
		$header = "
			<table class='wf fs9' cellspacing='0' style='border-collapse:collapse;line-height:23px;overflow:wrap;vertical-align:text-top;'>
				<tr>
					<th colspan='14' style='font-size:11pt;' align='center'>".$COMP_NM."<br>รายงานสรุปอายุลูกหนี้</th>
				</tr>
				<tr>
					<th colspan='14' style='font-size:11pt;' align='center'>
						<b>สาขา</b>&nbsp;&nbsp;".$LOCAT."&nbsp;&nbsp;
						<b>พนักงานเก็บเงิน</b>&nbsp;&nbsp;".$BILLCOLL."&nbsp;&nbsp;
						<b>ณ วันที่</b>&nbsp;&nbsp;".$this->Convertdate(2,$ATDATE)."&nbsp;&nbsp;
					</th>
				</tr>
				<tr>
					<th colspan='14' style='font-size:11pt;' align='right'>
						RpAsA50,51
					</th>
				</tr>
				<tr>
					<td colspan='2' align='left'>วันที่พิมพ์รายงาน</td>
					<td colspan='2' align='left'>".date('d/m/').(date('Y')+543)." ".date('H:i')."</td>
					<td colspan='10' align='right'>หน้าที่ : {PAGENO} / {nb} &emsp;&emsp;</td>
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