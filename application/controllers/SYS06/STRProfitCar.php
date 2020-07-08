<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/********************************************************
             ______@17/06/2020______
			 Pasakorn Boonded

********************************************************/
class STRProfitCar extends MY_Controller {
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
						<div class='col-sm-12 col-xs-12' style='background-color:#16a085;border:5px solid white;height:75px;text-align:center;font-size:12pt;color:white;font-weight:bold;'>	
							<br>รายงานกำไรคงเหลือจากรถเปลี่ยนสภาพ (STR)<br>
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
									<div class='input-group'>
										<input type='text' id='CONTNO' class='form-control input-sm' placeholder='เลขที่สัญญา' >
										<span class='input-group-btn'>
											<button id='btnaddcont' class='btn btn-info btn-sm' type='button'>
												<span class='glyphicon glyphicon-hand-up' aria-hidden='true'></span>
											</button>
										</span>
									</div>
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
							<div class='col-sm-3'>
								<div class='form-group'>
									กลุ่มสินค้า
									<select id='GCODE' class='form-control input-sm'></select>
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
		$html .="<script src='".base_url('public/js/SYS06/STRProfitCar.js')."'></script>";
		echo $html;
	}
	function conditiontopdf(){
		$data = array();
		$data[] = urlencode($_REQUEST['LOCAT'].'||'.$_REQUEST['CONTNO'].'||'.$_REQUEST['F_DATE']
		.'||'.$_REQUEST['T_DATE'].'||'.$_REQUEST['OFFICER'].'||'.$_REQUEST['GCODE'].'||'.$_REQUEST['order']);
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
		$GCODE     = $tx[5];
		$order     = $tx[6];
		
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
				<th style='border-bottom:0.1px solid black;text-align:right;'>ดอกผลทั้งหมด</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>จำนวนงวด<br>ทั้งหมด</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>งวดที่คงเหลือ</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ดอกผลสะสม<br>จากงวดก่อน</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>วันครบกำหนด</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ค่างวดคงเหลือ</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ภาษีคงเหลือ</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>เงินต้นคงเหลือ</th>
				<th style='border-bottom:0.1px solid black;text-align:right;'>ดอกผลคงเหลือ</th>	
			</tr>
			<tr>
				<td class='wf' style='height:1px;border-top:0.1px solid black;' colspan='15'></td>
			</tr>
		";
		$sql = "
			select A.LOCAT,A.CONTNO,A.TOTPRC,A.TOTPRES,A.CUSCOD,A.SDATE
				,B.SNAM+B.NAME1+' '+B.NAME2 as CUSNAME
				,A.LPAYD,A.BILLCOLL,A.TOT_UPAY,A.EXP_AMT,A.SMPAY,A.EXP_FRM,A.EXP_TO
				,A.PAYDWN,convert(varchar(8),A.FDATE,112) as FDATE,A.T_NOPAY,A.OPTCST,A.NCARCST
				,A.NCSHPRC,A.NPRICE,A.NPROFIT,convert(varchar(8),YDATE,112) as YDATE,NKANG  
			from {$this->MAuth->getdb('HARMAST')} A,{$this->MAuth->getdb('CUSTMAST')} B 
			where (A.CUSCOD = B.CUSCOD) and (A.LOCAT like '".$LOCAT."%') and (A.CONTNO like '".$CONTNO."%')
			and (A.BILLCOLL like '".$OFFICER."%') and (A.YDATE between '".$F_DATE."' and '".$T_DATE."') 
			and A.TOTPRC > 0 and A.TOTPRC <> A.SMPAY   
			and A.STRNO in (
				select STRNO from {$this->MAuth->getdb('INVTRAN')} where GCODE like '".$GCODE."%' 
				union 
				select STRNO from {$this->MAuth->getdb('HINVTRAN')} where GCODE like '".$GCODE."%'
			) 
			order by A.".$order."
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		$arrs = array();
		if($query->row()){
			foreach($query->result() as $row){$i++;
				$sql1 = "
					IF OBJECT_ID('tempdb..#STRtemp') IS NOT NULL DROP TABLE #STRtemp
					select *
					into #STRtemp
					FROM(
						select 
							NOPAY,DDATE,CONTNO,LOCAT,V_DAMT,N_DAMT,STRPROF 
						from {$this->MAuth->getdb('HARPAY')} 
						where LOCAT = '".$row->LOCAT."' and CONTNO = '".$row->CONTNO."' 
						--and DDATE >= '2011-05-30' order by NOPAY
					)STRt
				";
				$this->db->query($sql1);
				$sql2 = "
					--งวดที่คงเหลือ
					declare @minNOPAY varchar(max) = (
						select MIN(NOPAY)
						from #STRtemp where DDATE >= '".$row->YDATE."'
					);
					declare @maxNOPAY varchar(max) = (
						select MAX(NOPAY)
						from #STRtemp where DDATE >= '".$row->YDATE."'
					);
					--ดอกผลสะสมจากงวดก่อน
					declare @BF_STRPROF decimal(18,2) = (
						select
							SUM(STRPROF)
						from #STRtemp where DDATE <= '".$row->YDATE."'
					);
					--วันครบกำหนด
					declare @TO_DDATE varchar(8) = (
						select 
							CONVERT(varchar(8),MIN(DDATE),112)
						from #STRtemp where DDATE <= '".$row->YDATE."'
					);
					--ค่างวดคงเหลือ
					declare @BL_N_DAMT decimal(18,2) = (
						select 
							SUM(N_DAMT)
						from #STRtemp where DDATE >= '".$row->YDATE."'
					);
					--ภาษีคงเหลือ
					declare @BL_V_DAMT decimal(18,2) = (
						select 
							SUM(V_DAMT)
						from #STRtemp where DDATE >= '".$row->YDATE."'
					);
					--เงินต้นคงเหลือ
					declare @BL_COST decimal(18,2) = (
						select 
							SUM(N_DAMT - STRPROF)
						from #STRtemp where DDATE >= '".$row->YDATE."'
					);
					--ดอกผลคงเหลือ
					declare @BL_STRPROF decimal(18,2) = (
						select 
							SUM(STRPROF)
						from #STRtemp where DDATE >= '".$row->YDATE."'
					);
					select @minNOPAY+'-'+@maxNOPAY as BL_NOPAY,@BF_STRPROF as BF_STRPROF
					,@TO_DDATE as TO_DDATE,@BL_N_DAMT as BL_N_DAMT,@BL_V_DAMT as BL_V_DAMT
					,@BL_COST as BL_COST,@BL_STRPROF as BL_STRPROF
				";
				$query2 = $this->db->query($sql2);
				if($query2->row()){
					foreach($query2->result() as $row1){
						$arrs['NPROFIT'][]    = $row->NPROFIT;
						$arrs['BF_STRPROF'][] = $row1->BF_STRPROF;
						$arrs['BL_N_DAMT'][]  = $row1->BL_N_DAMT;
						$arrs['BL_V_DAMT'][]  = $row1->BL_V_DAMT;
						$arrs['BL_COST'][]    = $row1->BL_COST;
						$arrs['BL_STRPROF'][] = $row1->BL_STRPROF;
						
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
									<td style='width:6%;text-align:right;'>".$row1->BL_NOPAY."</td>
									<td style='width:5%;text-align:right;'>".number_format($row1->BF_STRPROF,2)."</td>
									<td style='width:6%;text-align:right;'>".$row1->TO_DDATE."</td>
									<td style='width:6%;text-align:right;'>".number_format($row1->BL_N_DAMT,2)."</td>
									<td style='width:6%;text-align:right;'>".number_format($row1->BL_V_DAMT,2)."</td>
									<td style='width:6%;text-align:right;'>".number_format($row1->BL_COST,2)."</td>
									<td style='width:6%;text-align:right;'>".number_format($row1->BL_STRPROF,2)."</td>
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
					<td style='text-align:right;' colspan='3'>".number_format(array_sum($arrs['BF_STRPROF']),2)."</td>
					<td style='text-align:right;' colspan='2'>".number_format(array_sum($arrs['BL_N_DAMT']),2)."</td>
					<td style='text-align:right;'>".number_format(array_sum($arrs['BL_V_DAMT']),2)."</td>
					<td style='text-align:right;'>".number_format(array_sum($arrs['BL_COST']),2)."</td>
					<td style='text-align:right;'>".number_format(array_sum($arrs['BL_STRPROF']),2)."</td>
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
				<table class='wf' style='font-size:7.5pt;height:700px;border-collapse:collapse;line-height:23px;overflow:wrap;vertical-align:text-top;'>
					<tbody>
						<tr>
							<th colspan='15' style='font-size:10pt;'>บริษัท ตั้งใจพัฒนายานยนต์ จำกัด</th>
						</tr>
						<tr>
							<th colspan='15' style='font-size:9pt;'>รายงานกำไรคงเหลือแลกเปลี่ยนและเปลี่ยนสภาพเป็นรถเก่า(STR)</th>
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
							<td style='text-align:right;' colspan='15'>Rpstr 30,31</td>
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