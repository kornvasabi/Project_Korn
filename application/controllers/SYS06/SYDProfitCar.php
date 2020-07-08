<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/********************************************************
             ______@21/05/2020______
			 Pasakorn Boonded

********************************************************/
class SYDProfitCar extends MY_Controller {
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
							<br>รายงานกำไรคงเหลือจากรถเปลี่ยนสภาพ (SYD)<br>
						</div>
						<div class='col-sm-12'>
							<div class='col-sm-3'>
								<div class='form-group'>
									รหัสสาขา
									<select id='LOCAT' class='form-control input-sm'></select>
								</div>
							</div>
							<div class='col-sm-3'>
								<div class='form-group'>
									เลขที่สัญญา
									<select id='CONTNO' class='form-control input-sm'></select>
								</div>
							</div>
							<div class='col-sm-3'>
								<div class='form-group'>
									จากวันที่
									<input type='text' id='F_DATE' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' value='".$this->today('today')."' placeholder='ถึงวันที่ขาย' >
								</div>
							</div>
							<div class='col-sm-3'>
								<div class='form-group'>
									ถึงวันที่
									<input type='text' id='T_DATE' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' value='".$this->today('today')."' placeholder='ถึงวันที่ขาย' >
								</div>
							</div>
							<div class='col-sm-3'>
								<div class='form-group'>
									พนักงานเก็บเงิน
									<select id='OFFICER' class='form-control input-sm'></select>
								</div>
							</div>
						</div>
						<div class='col-sm-12'>
							<div class='col-sm-10'>
								<b>การเรียงในรายงาน</b>
								<div class='col-sm-12' style='height:100%;width:100%;border:1px dotted #aaa;'>
									<div class='col-sm-4'>
										<div class='form-group'>
											<br>
											<label>
												<input type= 'radio' id='OR1' name='order' checked> รหัสสาขา
											</label>
										</div>
									</div>
									<div class='col-sm-4'>
										<div class='form-group'>
											<br>
											<label>
												<input type= 'radio' id='OR2' name='order'> เลขที่สัญญา
											</label>
										</div>
									</div>
									<div class='col-sm-4'>
										<div class='form-group'>
											<br>
											<label>
												<input type= 'radio' id='OR3' name='order'> รหัสลูกค้า
											</label>
										</div>
									</div>
								</div>	
							</div>
							<div class='col-sm-2'>
								<br>
								<button id='btnreport' style='width:100%;height:60px;' class='btn btn-cyan btn-sm'><span class='fa fa-file-text'>SCREEN</span></button>
							</div>
						</div>
					</div>	
				</div>
			</div>
		";
		$html .="<script src='".base_url('public/js/SYS06/SYDProfitCar.js')."'></script>";
		echo $html;
	}
	function getLocat(){
		$CONTNO = $_REQUEST['CONTNO'];
		$response = array();
		$sql = "
			select top 100 A.CONTNO,A.LOCAT,C.SNAM+C.NAME1+' '+C.NAME2+' ('+A.CONTNO+')' as CUSNAME 
			from {$this->MAuth->getdb('ARMAST')} A
			left join {$this->MAuth->getdb('CUSTMAST')} C on A.CUSCOD = C.CUSCOD
			left join {$this->MAuth->getdb('INVTRAN')} I on A.STRNO = I.STRNO 
			where A.CONTNO = '".$CONTNO."' order by A.CONTNO
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$response['LOCAT'] = $row->LOCAT;
			}
		}else{
			$response['LOCAT'] = "";
		}
		echo json_encode($response);
	}
	function conditiontopdf(){
		$data = array();
		$data[] = urlencode($_REQUEST['LOCAT'].'||'.$_REQUEST['CONTNO'].'||'.$_REQUEST['F_DATE'].'||'.$_REQUEST['T_DATE']
		.'||'.$_REQUEST['OFFICER'].'||'.$_REQUEST['order']);
		echo json_encode($this->generateData($data,"encode"));
	}
	function pdf(){
		$data = array();
		$data[] = $_REQUEST["condpdf"];
		$arrs = $this->generateData($data,"decode");
		$arrs[0] = urldecode($arrs[0]);
		
		$tx = explode('||',$arrs[0]);
		$LOCAT     = $tx[0];
		$CONTNO    = $tx[1];
		$F_DATE    = $this->Convertdate(1,$tx[2]);
		$T_DATE    = $this->Convertdate(1,$tx[3]);
		$OFFICER   = $tx[4];
		$order     = $tx[5];
		
		$head = ""; $html = ""; $i = 0;
		$head = "
			<tr class='wm'>
				<td class='wf' style='height:1px;border-top:0.1px solid black;' colspan='15'></td>
			</tr>
			<tr>
				<th style='border-bottom:0.1px solid black;text-align:left;'>No.</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>สาขา</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>เลขที่สัญญา</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>รหัสลูกค้า</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>ชื่อ - สกุล</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>วันดิวงวดแรก</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ดอกผล<br>ทั้งหมด</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>จน. งวด<br>ทั้งหมด</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>งวดที่คงเหลือ</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ดอกผลสะสม<br>จากงวดก่อน</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>วันครบกำหนด</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ค่างวดคงเหลือ</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ภาษีคงเหลือ</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>เงินต้นคงเหลือ</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ดอกผล<br>คงเหลือ</th>	
			</tr>
			<tr>
				<td class='wf' style='height:1px;border-top:0.1px solid black;' colspan='15'></td>
			</tr>
		";
		$sql = "
			select A.LOCAT,A.CONTNO,A.TOTPRC,A.TOTPRES,A.CUSCOD,A.SDATE,A.LPAYD,A.BILLCOLL
				,A.TOT_UPAY,A.EXP_AMT,A.SMPAY,A.EXP_FRM,A.EXP_TO,B.SNAM+B.NAME1+' '+B.NAME2 as CUSNAME
				,A.PAYDWN,convert(varchar(8),A.FDATE,112) as FDATE,A.T_NOPAY,A.OPTCST,A.NCARCST,A.NCSHPRC
				,A.NPRICE,A.NPROFIT,convert(varchar(8),A.YDATE,112) as YDATE 
			from {$this->MAuth->getdb('HARMAST')} A,{$this->MAuth->getdb('CUSTMAST')} B  
			where (A.CUSCOD = B.CUSCOD) and (A.LOCAT like '".$LOCAT."%') and (A.CONTNO like '".$CONTNO."%') 
			and (A.BILLCOLL like '".$OFFICER."%') 
			and (A.YDATE between '".$F_DATE."' and '".$T_DATE."') and A.TOTPRC > 0 and A.TOTPRC <> A.SMPAY 
			order by A.".$order."
		";
		//echo $sql; exit;
		$arrs = array();
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){$i++;
				$sql2 = "
					--จำนวนงวดที่เหลือ
					declare @minnopay varchar(max) = (
						select MIN(NOPAY) 
						from {$this->MAuth->getdb('HARPAY')}  
						where LOCAT = '".$row->LOCAT."' and CONTNO = '".$row->CONTNO."' 
						and DDATE >= '".$row->YDATE."'
					);
					declare @maxnopay varchar(max) = (
						select MAX(NOPAY) 
						from {$this->MAuth->getdb('HARPAY')}  
						where LOCAT = '".$row->LOCAT."' and CONTNO = '".$row->CONTNO."' 
						and DDATE >= '".$row->YDATE."'
					);
					--ดอกผลสะสมจากงวดก่อน
					declare @nprof varchar(max) = (
						select sum(NPROF) 
						from {$this->MAuth->getdb('HARPAY')}  
						where LOCAT = '".$row->LOCAT."' and CONTNO = '".$row->CONTNO."' 
						and DDATE < '".$row->YDATE."'
					);
					--วันครบกำหนด
					declare @ddate varchar(max) = (
						select top 1 CONVERT(varchar(8),DDATE,112) as DDATE 
						from {$this->MAuth->getdb('HARPAY')}  
						where LOCAT = '".$row->LOCAT."' and CONTNO = '".$row->CONTNO."' 
						and DDATE >= '".$row->YDATE."'
					);
					--ค่างวดคงเหลือ
					declare @n_damt varchar(max) = (
						select SUM(N_DAMT) 
						from {$this->MAuth->getdb('HARPAY')}  
						where LOCAT = '".$row->LOCAT."' and CONTNO = '".$row->CONTNO."' 
						and DDATE >= '".$row->YDATE."'
					);
					--ภาษีคงเหลือ
					declare @v_damt varchar(max) = (
						select SUM(V_DAMT) 
						from {$this->MAuth->getdb('HARPAY')}  
						where LOCAT = '".$row->LOCAT."' and CONTNO = '".$row->CONTNO."' 
						and DDATE >= '".$row->YDATE."'
					);
					--เงินต้นคงเหลือ
					declare @moneyfirst varchar(max) = (
						select SUM(N_DAMT) - sum(NPROF) 
						from {$this->MAuth->getdb('HARPAY')}  
						where LOCAT = '".$row->LOCAT."' and CONTNO = '".$row->CONTNO."' 
						and DDATE >= '".$row->YDATE."'
					);
					--ดอกผลคงเหลือ
					declare @nprof_surplus varchar(max) = (
						select sum(NPROF) 
						from {$this->MAuth->getdb('HARPAY')}  
						where LOCAT = '".$row->LOCAT."' and CONTNO = '".$row->CONTNO."' 
						and DDATE >= '".$row->YDATE."'
					);
					select @minnopay+' - '+@maxnopay as B_NOPAY,@nprof as NPROF,@ddate as DDATE
					,@n_damt as N_DAMT,@v_damt as V_DAMT,@moneyfirst as MONEYFIRST
					,@nprof_surplus as NPROF_SURPLUS
				";
				//echo $sql2; exit;
				$query2 = $this->db->query($sql2);
				if($query2->row()){
					foreach($query2->result() as $row2){
						$arrs['NPROFIT'][]		 = $row->NPROFIT;
						$arrs['NPROF'][] 		 = $row2->NPROF;
						$arrs['N_DAMT'][] 		 = $row2->N_DAMT;
						$arrs['V_DAMT'][] 		 = $row2->V_DAMT;
						$arrs['MONEYFIRST'][]    = $row2->MONEYFIRST;
						$arrs['NPROF_SURPLUS'][] = $row2->NPROF_SURPLUS;
						$html .="
							<tr>
								<tr>
									<td style='width:2%;text-align:left;'>".$i."</td>
									<td style='width:5%;text-align:left;'>".$row->LOCAT."</td>
									<td style='width:8%;text-align:left;'>".$row->CONTNO."</td>
									<td style='width:8%;text-align:left;'>".$row->CUSCOD."</td>
									<td style='width:12%;text-align:left;'>".$row->CUSNAME."</td>
									<td style='width:5%;text-align:left;'>".$this->Convertdate(2,$row->FDATE)."</td>
									<td style='width:5%;text-align:right;'>".number_format($row->NPROFIT,2)."</td>
									<td style='width:4%;text-align:right;'>".$row->T_NOPAY."</td>
									<td style='width:6%;text-align:right;'>".$row2->B_NOPAY."</td>
									<td style='width:5%;text-align:right;'>".number_format($row2->NPROF,2)."</td>
									<td style='width:6%;text-align:right;'>".$this->Convertdate(2,$row2->DDATE)."</td>
									<td style='width:6%;text-align:right;'>".number_format($row2->N_DAMT,2)."</td>
									<td style='width:6%;text-align:right;'>".number_format($row2->V_DAMT,2)."</td>
									<td style='width:6%;text-align:right;'>".number_format($row2->MONEYFIRST,2)."</td>
									<td style='width:6%;text-align:right;'>".number_format($row2->NPROF_SURPLUS,2)."</td>
								</tr>
							</tr>
						";	
					}
				}
			}
		}
		if($i > 0){
			$html .="
				<tr>
					<td class='wf' style='height:1px;border-top:0.1px solid black;' colspan='15'></td>
				</tr>
				<tr>
					<td style='text-align:left;' colspan='2'><b>รวมทั้งสิ้น</b></td>
					<td style='text-align:center;' colspan='1'>".$i."</td>
					<td style='text-align:left;' colspan='2'><b>รายการ</b></td>
					<td style='text-align:right;' colspan='2'>".number_format(array_sum($arrs['NPROFIT']),2)."</td>
					<td style='text-align:right;' colspan='3'>".number_format(array_sum($arrs['NPROF']),2)."</td>
					<td style='text-align:right;' colspan='2'>".number_format(array_sum($arrs['N_DAMT']),2)."</td>
					<td style='text-align:right;'>".number_format(array_sum($arrs['V_DAMT']),2)."</td>
					<td style='text-align:right;'>".number_format(array_sum($arrs['MONEYFIRST']),2)."</td>
					<td style='text-align:right;'>".number_format(array_sum($arrs['NPROF_SURPLUS']),2)."</td>
				</tr>
				<tr>
					<td class='wf' style='height:1px;border-top:0.1px solid black;' colspan='15'></td>
				</tr>
			";
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
				<table class='wf' style='font-size:7.8pt;height:700px;border-collapse:collapse;line-height:23px;overflow:wrap;vertical-align:text-top;'>
					<tbody>
						<tr>
							<th colspan='15' style='font-size:10pt;'>บริษัท ตั้งใจพัฒนายานยนต์ จำกัด</th>
						</tr>
						<tr>
							<th colspan='15' style='font-size:9pt;'>รายงานกำไรกำไรคงเหลือรถแลกเปลี่ยนและเปลี่ยนสภาพเป็นรถเก่า (SYD)</th>
						</tr>
						<tr>
							<td style='text-align:center;' colspan='15'>
								<b>รหัสสาขา</b> &nbsp;&nbsp;".$LOCAT."&nbsp;&nbsp;
								<b>เลขที่สัญญา</b>&nbsp;&nbsp;".$CONTNO."&nbsp;&nbsp;
								<b>จากวันที่เปลี่ยนสภาพ</b>&nbsp;&nbsp;".$this->Convertdate(2,$F_DATE)."&nbsp;&nbsp;
								<b>ถึงวันที่</b>&nbsp;&nbsp;".$this->Convertdate(2,$T_DATE)."&nbsp;&nbsp;
								<b>พนักงานเก็บเงิน</b>&nbsp;&nbsp;".$OFFICER."&nbsp;&nbsp;
							</td>
						</tr>
						<tr>
							<td style='text-align:right;' colspan='15'>Rpsyd 30,31</td>
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