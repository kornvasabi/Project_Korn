<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/********************************************************
             ______@17/03/2020______
			 Pasakorn Boonded

********************************************************/
class ReduceDebtFormVP extends MY_Controller {
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
						<div class='col-sm-12 col-xs-12' style='background-color:#2e86c1;border:5px solid white;height:75px;text-align:center;font-size:12pt;color:white;font-weight:bold;'>	
							<br>ใบลดหนี้<br>
						</div>
						<div class='col-sm-10 col-xs-10 col-sm-offset-1 text-primary'>	
							<br>
							<div class='col-sm-2'>	
								<div class='form-group'>
									สาขาที่ออกใบลดหนี้
									<select id='LOCAT' class='form-control input-sm'></select>
								</div>
							</div>
							<div class='col-sm-5'>	
								<div class='form-group'>
									จากเลขที่สาขา
									<select id='TAXNO1' class='form-control input-sm'></select>
								</div>
							</div>
							<div class='col-sm-5'>	
								<div class='form-group'>
									ถึงเลขที่สาขา
									<select id='TAXNO2' class='form-control input-sm'></select>
								</div>
							</div>
						</div>
						<div class='col-sm-10 col-xs-10 col-sm-offset-1'><br>	
							<div class='col-sm-12 col-xs-12'>	
								<div class='form-group'>
									คำนำหน้าลูกค้า<br>
									<div class='col-sm-12 col-xs-12' style='border:0.1px dotted #d6d6d6;'>	
										<div class='col-sm-12 col-sm-offset-1'>
											<div class='col-sm-6'>
												<div class='form-group'>
													<br>
													<label>
														<input type= 'radio' id='S1' name='snam' checked> ตามข้อมูลจริง
													</label>
												</div>
											</div>
											<div class='col-sm-6'>
												<div class='form-group'>
													<br>
													<label>
														<input type= 'radio' id='S2' name='snam'> คุณ
													</label>
												</div>
											</div>
										</div>	
									</div>
								</div>
								<div class='col-sm-12 col-xs-12'>
									<br>
									<button id='btnBateDebt' type='button' class='btn btn-info btn-outline btn-block' style='width:100%'><span class='fa fa-folder-open'><b>แสดง</b></span></button>
								</div><br>
							</div>
						</div>
					</div>
				</div>
			</div>
		";
		$html .="<script src='".base_url('public/js/SYS07/ReduceDebtFormVP.js')."'></script>";
		echo $html;
	}
	function conditiontopdf(){
		$data = array();
		$data[] = urlencode($_REQUEST['LOCAT'].'||'.$_REQUEST['TAXNO1'].'||'.$_REQUEST['TAXNO2'].'||'.$_REQUEST['snam']);
		echo json_encode($this->generateData($data,"encode"));
	}
	function pdfDebtReduce(){
		$data = array();
		$data[] = $_REQUEST["condpdf"];
		$arrs = $this->generateData($data,"decode");
		$arrs[0] = urldecode($arrs[0]);
		
		$tx = explode('||',$arrs[0]);
		$LOCAT 	 = $tx[0];
		$TAXNO1  = $tx[1];
		$TAXNO2  = $tx[2];
		$snam 	 = $tx[3];

		$mpdf = new \Mpdf\Mpdf([
			'mode' => 'utf-8', 
			'format' =>'A4',
			'margin_top' => 10, 	//default = 16
			'margin_left' => 10, 	//default = 15
			'margin_right' => 10, 	//default = 15
			'margin_bottom' => 10, 	//default = 16
			'margin_header' => 9, 	//default = 9
			'margin_footer' => 9, 	//default = 9
		]);
		$sql = "
			IF OBJECT_ID('tempdb..#DFVP') IS NOT NULL DROP TABLE #DFVP
			select LOCATNM,TAXNO,TAXDT,REFNO,REFDT,CUSNAM1,CUSNAM2,ADDR1,ADDR2,CUSCAR1,CUSCAR2,VATRT,NETAMT,VATAMT,TOTAMT
			into #DFVP
			FROM(
				select I.LOCATNM,T.TAXNO,HIC2SHORTL.dbo.ThaiDate(T.TAXDT) as TAXDT,T.REFNO
				,HIC2SHORTL.dbo.ThaiDate(T.REFDT) as REFDT,T.VATRT,T.NETAMT,T.VATAMT,T.TOTAMT
				,C.SNAM+C.NAME1+' '+C.NAME2 as CUSNAM1,'คุณ'+' '+C.NAME1+' '+C.NAME2 as CUSNAM2 
				,A.ADDR1+' '+'หมู่บ้าน'+A.MOOBAN+' '+'ซอย'+A.SOI+' '+'ถนน'+A.ADDR2+' '+'ตำบล'+A.TUMB as ADDR1
				,'อำเภอ'+P.AUMPDES+' '+'จังหวัด'+V.PROVDES+' '+'รหัสไปรษณีย์'+' '+A.ZIP as ADDR2 
				,T.SNAM+T.NAME1+' '+T.NAME2 as CUSCAR1,'คุณ'+' '+T.NAME1+' '+T.NAME2 as CUSCAR2 from {$this->MAuth->getdb('TAXTRAN')} T
				left join {$this->MAuth->getdb('INVLOCAT')} I on T.LOCAT = I.LOCATCD
				left join {$this->MAuth->getdb('CUSTMAST')} C on T.CUSCOD = C.CUSCOD
				left join {$this->MAuth->getdb('CUSTADDR')} A on C.CUSCOD = A.CUSCOD
				left join {$this->MAuth->getdb('SETAUMP')} P on A.AUMPCOD = P.AUMPCOD
				left join {$this->MAuth->getdb('SETPROV')} V on A.PROVCOD = V.PROVCOD
				where A.ADDRNO = '1' and LOCAT = '".$LOCAT."' and TAXNO between '".$TAXNO1."' and '".$TAXNO2."'
			)DFVP
		";
		//echo $sql; exit;
		$this->db->query($sql);
		$sql = "
			select A.LOCATNM,A.TAXNO,A.TAXDT,A.REFNO,A.REFDT,A.CUSNAM1,A.CUSNAM2,A.ADDR1,A.ADDR2,A.CUSCAR1,CUSCAR2
			,A.VATRT,A.NETAMT,A.VATAMT,A.TOTAMT,B.NETAMT as NETAMTF,B.VATAMT as VATAMTF,B.TOTAMT as TOTAMTF from #DFVP A 
			left join {$this->MAuth->getdb('TAXTRAN')} B on B.TAXNO = A.REFNO
		";
		//echo $sql;exit;
		$html = ""; $content = "";
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$cuscar = ""; $cusnam = "";
				if($snam == 'S1'){
					$cuscar = $row->CUSCAR1;
					$cusnam = $row->CUSNAM1;
				}else{
					$cuscar = $row->CUSCAR2;
					$cusnam = $row->CUSNAM2;
				}
				$content = "
					<table class='wf' style='font-size:7.5pt;height:700px;border-collapse:collapse;line-height:23px;overflow:wrap;vertical-align:text-top;'>
						<tbody>
							<tr>
								<th colspan='4' style='font-size:10pt;'>บริษัท ตั้งใจพัฒนายานยนต์ จำกัด</th>
							</tr>
							<tr>
								<th colspan='4' style='font-size:10pt;'>187 ถนนวิเศษกุล อำเภอเมือง จังหวัดตรัง 92000</th>
							</tr>
							<tr>
								<th colspan='4' style='font-size:10pt;'>โทรศัพท์ 04-0528269,075-223316-19 ต่อ 21</th>
							</tr>
							<tr>
								<th colspan='4' style='font-size:10pt;'>เลขประจำตัวผู้เสียภาษี</th>
							</tr><br><br><br>
							<tr>
								<th colspan='4' style='font-size:11pt;'><b>ใบลดหนี้/ใบกำกับภาษี</b></th>
							</tr><br><br>
							<tr>
								<td style='width:70px;text-align:left;font-size:9pt;'><b>สาขา</b></td>
								<td style='width:200px;text-align:left;font-size:9pt;'>".$row->LOCATNM."</td>
								<td style='width:70px;text-align:left;font-size:9pt;'><b>เลขที่ใบลดหนี้</b></td>
								<td style='width:70px;text-align:left;font-size:9pt;'>".$row->TAXNO."</td>
							</tr>
							<tr>
								<td style='width:120px;text-align:left;font-size:9pt;'><b>ชื่อ - นามสกุล</b></td>
								<td style='width:150px;text-align:left;font-size:9pt;'>".$cusnam."</td>
								<td style='width:70px;text-align:left;font-size:9pt;'><b>วันที่</b></td>
								<td style='width:70px;text-align:left;font-size:9pt;'>".$row->TAXDT."</td>
							</tr>
							<tr>
								<td style='width:70px;text-align:left;font-size:9pt;'><b>ที่อยู่</b></td>
								<td style='width:200px;text-align:left;font-size:9pt;'>".$row->ADDR1."</td>
								<td style='width:70px;text-align:left;font-size:9pt;'><b>อ้างถึงใบเลขที่กำกับ</b></td>
								<td style='width:70px;text-align:left;font-size:9pt;'>".$row->REFNO."</td>
							</tr>
							<tr>
								<td style='width:70px;text-align:left;font-size:9pt;'></td>
								<td style='width:200px;text-align:left;font-size:9pt;'>".$row->ADDR2."</td>
								<td style='width:70px;text-align:left;font-size:9pt;'><b>ลงวันที่</b></td>
								<td style='width:70px;text-align:left;font-size:9pt;'>".$row->REFDT."</td>
							</tr>
							<tr>
								<td style='width:70px;text-align:left;font-size:9pt;'><b>ชื่อ - ผู้ใช้รถ</b></td>
								<td style='width:200px;text-align:left;font-size:9pt;'>".$cuscar."</td>
							</tr><br><br><br>
							<tr>
								<td class='wf pd' style='height:1px;border-top:0.1px solid black;' colspan='4'></td>
							</tr>
							<tr>
								<td style='width:70px;text-align:left;font-size:9pt;' colspan='3'>&nbsp;&nbsp;<b>รายการ</b></td>
								<td style='width:70px;text-align:right;font-size:9pt;'><b>จำนวนเงิน</b></td>
							</tr>
							<tr>
								<td class='wf pd' style='height:1px;border-top:0.1px solid black;' colspan='4'></td>
							</tr>
							<tr>
								<td style='width:70px;text-align:left;font-size:9pt;' colspan='3'>&nbsp;&nbsp;มูลค่าสินค่าตามใบกำกับเดิม</td>
								<td style='width:70px;text-align:right;font-size:9pt;'>".number_format($row->NETAMTF,2)."</td>
							</tr><tr>
								<td style='width:70px;text-align:left;font-size:9pt;' colspan='3'>&nbsp;&nbsp;มูลค่าที่ถูกต้อง</td>
								<td style='width:70px;text-align:right;font-size:9pt;'>".number_format($row->NETAMTF - $row->NETAMT,2)."</td>
							</tr>
							<tr>
								<td style='width:70px;text-align:left;font-size:9pt;' colspan='3'>&nbsp;&nbsp;ผลต่าง</td>
								<td style='width:70px;text-align:right;font-size:9pt;'>".number_format($row->NETAMT,2)."</td>
							</tr>
							<tr>
								<td class='wf pd' style='height:1px;border-top:0.1px solid black;' colspan='4'></td>
							</tr>
							<tr>
								<td style='width:70px;text-align:right;font-size:9pt;' colspan='3'>ภาษีมูลค่าเพิ่ม&nbsp;&nbsp;7.00 %</td>
								<td style='width:70px;text-align:right;font-size:9pt;'>".number_format($row->VATAMT,2)."</td>
							</tr>
							<tr>
								<td style='width:70px;text-align:right;font-size:9pt;' colspan='3'>รวมทั้งสิ้น</td>
								<td style='width:70px;text-align:right;font-size:9pt;'>".number_format($row->TOTAMT,2)."</td>
							</tr>
							<tr>
								<td class='wf pd' style='height:1px;border-top:0.1px solid black;' colspan='4'></td>
							</tr><br><br><br><br>
							<tr>
								<th colspan='2' style='font-size:8pt;text-align:left;'>เหตุผลในการลดหนี้</th>
								<th colspan='2' style='font-size:8pt;text-align:left;'>ผู้มีอำนาจลงนาม...............................................................</th>
							</tr>
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
				//$mpdf->SetHTMLHeader($head);
				$mpdf->AddPage();		
				$mpdf->WriteHTML($content);	
			}
		}else{
			$content = "<font style='color:red;'>ไม่พบข้อมูล</font>";		
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
			//$mpdf->SetHTMLHeader($head);
			$mpdf->AddPage();		
			$mpdf->WriteHTML($content);	
		}
		$mpdf->Output();
	}
}