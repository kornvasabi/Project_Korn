<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/********************************************************
             ______@12/11/2019______
            / / _ _   _ _     __ 
           / // __ \ / __ \ / __ \
       _ _/ // /_/ // / / // /_/ /
     /_ _ _/ \_ _ //_/ /_/ \__  /
                          _ _/ /
                         /___ /
********************************************************/
class Report extends MY_Controller {
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
	
	function stockcard(){
		$claim = $this->MLogin->getclaim(uri_string());
		//print_r($claim); exit;
		if($claim['m_access'] != "T"){ echo "<div align='center' style='color:red;font-size:16pt;width:100%;'>ขออภัย คุณยังไม่มีสิทธิเข้าใช้งานหน้านี้ครับ</div>"; exit; }
		
		$html = "
			<div class='tab1' name='home' locat='{$this->sess['branch']}' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}'>
				<div>
					<div class='row'>
						<div class='col-sm-2'>	
							<div class='form-group'>
								สาขา
								<select id='locat' class='form-control input-sm chosen-select' data-placeholder='สาขา'>
									<option value='{$this->sess['branch']}'>{$this->sess['branch']}</option>
								</select>
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								วันที่รับ จาก
								<input type='text' id='SDATE' class='form-control input-sm' placeholder='จาก' data-provide='datepicker' data-date-language='th-th' value='".$this->today('startofmonthB1')."'>
							</div>
						</div>	
						<div class='col-sm-2'>	
							<div class='form-group'>
								วันที่รับ ถึง
								<input type='text' id='EDATE' class='form-control input-sm' placeholder='ถึง' data-provide='datepicker' data-date-language='th-th' value='".$this->today('endofmonthB1')."'>
							</div>
						</div>	
						
						<div class='col-sm-2'>	
							<div class='form-group'>
								ยี่ห้อ
								<select id='TYPE' class='form-control input-sm chosen-select' data-placeholder='ยี่ห้อ'></select>
							</div>
						</div>
						
						<div class='col-sm-2'>	
							<div class='form-group'>
								รุ่น
								<select id='MODEL' class='form-control input-sm chosen-select' data-placeholder='รุ่น'></select>
							</div>
						</div>
						
						<div class='col-sm-2'>	
							<div class='form-group'>
								สถานะรถ
								<select id='STAT' class='form-control input-sm chosen-select' data-placeholder='สถานะรถ'>
									<option value='A'>ทั้งหมด</option>
									<option value='N'>รถใหม่</option>
									<option value='O'>รถเก่า</option>
								</select>
							</div>
						</div>						
					</div>
					<div class='row'>
						<div class='col-sm-2 col-sm-offset-4'>
							<b>รายงาน</b>
							<div class='radio'><label><input type='radio' class='sort' name='REPORT' value='1' checked=''>วันที่รับ</label></div>
							<div class='radio'><label><input type='radio' class='sort' name='REPORT' value='2'>วันที่ใบกำกับภาษี</label></div>
						</div>
						
						<div class='col-sm-2'>
							<b>สินค้าและวัตถุดิบ</b>
							<div class='radio'><label><input type='radio' class='sort' name='turnover' value='Y' checked=''>มีการเคลื่อนไหว</label></div>
							<div class='radio'><label><input type='radio' class='sort' name='turnover' value='N'>ทั้งหมด</label></div>
						</div>
					</div>
					<div class='row'>
						<div class='col-sm-6'>
							<div class='form-group'>
								<button id='btnt1search' class='btn btn-primary btn-block'>
									<span class='glyphicon glyphicon-search'> แสดง</span>
								</button>
							</div>
						</div>
						<div class='col-sm-6'>
							<div class='form-group'>
								<button id='btnt1PDF' class='btn btn-danger btn-block'>
									<span class='glyphicon glyphicon-download'> Donwload PDF</span>
								</button>
							</div>
						</div>
					</div>
				</div>
				<div id='result'></div>
			</div>
		";
		
		$html.= "<script src='".base_url('public/js/SYS07/ReportStockcard.js')."'></script>";
		echo $html;
	}
	
	function stockcardSearch(){
		$arrs = array();
		$arrs['LOCAT']	  = $_POST['LOCAT'];
		$arrs['SDATE']    = $this->Convertdate(1,$_POST['SDATE']);
		$arrs['EDATE']    = $this->Convertdate(1,$_POST['EDATE']);
		$arrs['TYPE'] 	  = $_POST['TYPE'];
		$arrs['MODEL'] 	  = $_POST['MODEL'];
		$arrs['STAT'] 	  = $_POST['STAT'];
		$arrs['REPORT']   = $_POST['REPORT'];
		$arrs['turnover'] = $_POST['turnover'];
		
		if($arrs['STAT'] == 'A'){
			$arrs['STAT'] = "null";
		}else{
			$arrs['STAT'] = "'".$arrs['STAT']."'";
		}
		$sql = "
			declare @fmdt datetime		 = '{$arrs['SDATE']}';
			declare @todt datetime		 = '{$arrs['EDATE']}';
			declare @locat varchar(5)	 = ".($arrs['LOCAT'] == "" ? "'%'":"'".$arrs['LOCAT']."'").";
			declare @type varchar(20)	 = ".($arrs['TYPE'] == "" ? "'%'":"'".$arrs['TYPE']."'").";
			declare @model varchar(20)	 = ".($arrs['MODEL'] == "" ? "'%'":"'".$arrs['MODEL']."'").";
			declare @stat varchar(1)	 = ".($arrs['STAT']).";
			declare @turnover varchar(1) = '{$arrs['turnover']}';
			
			select seq,model,ffm,model
				,case when model='โอนย้ายสินค้า' then stklocat else fmlocat end fmlocat
				,case when model='โอนย้ายสินค้า' then fmlocat else stklocat end stklocat
				,refno,convert(varchar(8),stkdate,112) as stkdate
				,strno,baab,color,qtyin,qtyout,total
			from {$this->MAuth->getdb('stockcard')}(@fmdt,@todt,@locat,@type,@model,@stat,@turnover);
		";
		$query = $this->db->query($sql);
		
		$data_sum = array();
		$html = "";
		$NRow = 0;
		$bgcolor = "#fff;";
		if($query->row()){
			foreach($query->result() as $row){
				if($row->ffm == 2 and $row->model == ""){ continue; }
				if($row->ffm == 1){
					$bgcolor = ($bgcolor == "#fff;" ? "#e7e4df;":"#fff;");
					$data_sum["turnover"] = $row->total;
				}else if($row->ffm == 3){
					$data_sum["turnover"] = $row->total;
					$data_sum["turnoverSum"] = (isset($data_sum["turnoverSum"]) ? $data_sum["turnoverSum"] : 0) + $data_sum["turnover"];
				}else{
					$data_sum["turnover"] = $data_sum["turnover"] + $row->total;
				}
				
				$html .= "
					<tr style='background-color:{$bgcolor}'>
						<td>".($row->ffm == 1 ? ++$NRow:"")."</td>
						<td>".($row->ffm == 3 ? "":$row->model)."</td>
						<td>".$row->fmlocat."</td>
						<td>".($row->ffm != 2 ? "":$row->stklocat)."</td>
						<td>".$row->refno."</td>
						<td>".$this->Convertdate(2,$row->stkdate)."</td>
						<td>".$row->strno."</td>
						<td>".$row->baab."</td>
						<td>".$row->color."</td>
						<td align='right'>".($row->ffm == 2 ? ($row->qtyin == 0 ? "" :$row->qtyin) : "")."</td>
						<td align='right'>".($row->ffm == 2 ? ($row->qtyout == 0 ? "" :$row->qtyout) : "")."</td>
						<td>".($row->ffm == 2 ? "":($row->ffm == 1 ? "ยอดยกมา":"ยอดยกไป"))."</td>
						<td align='right'>".$data_sum["turnover"]."</td>
					</tr>
				";				
			}
		}
		
		
		$sql = "select comp_nm,comp_adr1,comp_adr2,telp,taxid from {$this->MAuth->getdb('condpay')}";
		$query = $this->db->query($sql);
		
		$headcs		= 13;
		$company	= '';
		$reportName = 'รายงานสินค้าและวัตถุดิบ';
		$condDesc	= '';
		if($query->row()){
			foreach($query->result() as $row){
				$company = $row->comp_nm;
			}
		}
		
		$html = "
			<div id='table-fixed-RPstc' class='col-sm-12' style='height:calc(100% - 0px);width:100%;overflow:auto;font-size:8pt;'>
				<table id='table-RPstc' class='table table-bordered' cellspacing='0' width='calc(100% - 1px)'>
					<thead style='background: rgba(0, 0, 0, 0) url(&#39;../public/lobiadmin-master/version/1.0/ajax/img/bg/bg6.png&#39;) repeat scroll 0% 0%;'>
						<tr style='line-height:20px;'>
							<th style='vertical-align:middle;text-align:center;font-size:12pt;' colspan='{$headcs}'>
								{$company}<br><span style='font-size:10pt;'>{$reportName}</span>
							</th>
						</tr>
						<tr style='line-height:20px;'> 
							<th style='vertical-align:middle;text-align:center;font-size:8pt;' colspan='{$headcs}'>
								เงื่อนไข {$condDesc}
							</th>
						</tr>
						<tr>
							<th>ลำดับ<br>&emsp;</th>
							<th>รุ่น<br>ประเภทการเคลื่อนไหว</th>
							<th>&emsp;<br>โอนจากสาขา</th>
							<th>&emsp;<br>โอนไปสาขา</th>
							<th>&emsp;<br>ใบสำคัญเลขที่</th>
							<th>&emsp;<br>วดป.</th>
							<th>&emsp;<br>เลขตัวถัง</th>
							<th>&emsp;<br>แบบ</th>
							<th>&emsp;<br>สี</th>
							<th>&emsp;<br>จำนวนรับ</th>
							<th>&emsp;<br>จำนวนจ่าย</th>
							<th></th>
							<th>&emsp;<br>คงเหลือ</th>
						</tr>
					</thead>	
					<tbody>{$html}</tbody>
					<tfoot style='background: rgba(0, 0, 0, 0) url(&#39;../public/lobiadmin-master/version/1.0/ajax/img/bg/bg4.png&#39;) repeat scroll 0% 0%;'>
						<tr>
							<th></th>
							<th>รวมทั้งสิ้น</th>
							<th>".$NRow."</th>
							<th>รายการ</th>
							<th colspan='7'></th>
							<th>รวมยอดยกไป</th>
							<td align='right'>".$data_sum["turnoverSum"]."</td>
						</tr>
					</tfoot>
				</table>
			</div>
		";
		
		$response = array("html"=>$html,"status"=>true);
		echo json_encode($response);
		
	}
	
	function stockcardFormPrint(){
		$arrs = array();
		$arrs['LOCAT']	  = $_POST['LOCAT'];
		$arrs['SDATE']    = $this->Convertdate(1,$_POST['SDATE']);
		$arrs['EDATE']    = $this->Convertdate(1,$_POST['EDATE']);
		$arrs['TYPE'] 	  = $_POST['TYPE'];
		$arrs['MODEL'] 	  = $_POST['MODEL'];
		$arrs['STAT'] 	  = $_POST['STAT'];
		$arrs['REPORT']   = $_POST['REPORT'];
		$arrs['turnover'] = $_POST['turnover'];
		
		$html = "
			<form id='formsubmit' action='".base_url("SYS07/report/stockcardPDF")."' method='post' target='my_iframe'>
				<input type='text' name='LOCAT' value='".$arrs['LOCAT']."' hidden>
				<input type='text' name='SDATE' value='".$arrs['SDATE']."' hidden>
				<input type='text' name='EDATE' value='".$arrs['EDATE']."' hidden>
				<input type='text' name='TYPE' value='".$arrs['TYPE']."' hidden>
				<input type='text' name='MODEL' value='".$arrs['MODEL']."' hidden>
				<input type='text' name='STAT' value='".$arrs['STAT']."' hidden>
				<input type='text' name='REPORT' value='".$arrs['REPORT']."' hidden>
				<input type='text' name='turnover' value='".$arrs['turnover']."' hidden>
			</form>
			<iframe name='my_iframe' src='".base_url("SYS07/report/formloadding")."' style='width:100%;height:100%;border:0px solid #fff;'></iframe>
		";
		//<iframe name='my_iframe' src='".base_url("SYS07/report/xxx2")."'></iframe>
		$response = array("html"=>$html);
		echo json_encode($response);
	}
	
	function stockcardPDFx(){
		$mpdf = new \Mpdf\Mpdf([
			'mode' => 'utf-8', 
			'format' => 'A4-L',
			'margin_top' => 80, 	//default = 16
			'margin_left' => 8, 	//default = 15
			'margin_right' => 8, 	//default = 15
			'margin_bottom' => 6, 	//default = 16
			'margin_header' => 6, 	//default = 9
			'margin_footer' => 9, 	//default = 9
		]);
		
		$mpdf->SetImportUse();
		$pagecount = $mpdf->SetSourceFile('x.pdf');
		
		for($i = 1; $i<=$pagecount;$i++){
			if($i != 1) $mpdf->AddPage();
			$tplId = $mpdf->ImportPage($i);
			$mpdf->UseTemplate($tplId);
		}

		$mpdf->AddPage();
		$mpdf->WriteHTML('Hello World');
		$mpdf->Output();
	}
	
	function stockcardPDF_Success(){
		ini_set("memory_limit","-1");
		ini_set("pcre.backtrack_limit", "100000000");
		
		/* declare filename start*/
		$date = new DateTime();
		$text = $this->sess["USERID"]." :: ".$date->format('Y-m-d H:i:s'); 
		$data = array($text);
		$filename = $this->generateData($data,'encode');
		$filename = $filename[0].".pdf";
		/* declare filename end*/
		
		$logDT = "";
		$date = new DateTime();
		$logDT .= "เริ่มต้น :: ".$date->format('Y-m-d H:i:s'); 

		$arrs = array();
		$arrs['LOCAT']	  = $_POST['LOCAT'];
		$arrs['SDATE']    = $_POST['SDATE'];
		$arrs['EDATE']    = $_POST['EDATE'];
		$arrs['TYPE'] 	  = $_POST['TYPE'];
		$arrs['MODEL'] 	  = $_POST['MODEL'];
		$arrs['STAT'] 	  = $_POST['STAT'];
		$arrs['REPORT']   = $_POST['REPORT'];
		$arrs['turnover'] = $_POST['turnover'];
		
		if($arrs['STAT'] == 'A'){
			$arrs['STAT'] = "null";
		}else{
			$arrs['STAT'] = "'".$arrs['STAT']."'";
		}
		
		$sql = "
			declare @fmdt datetime		 = '{$arrs['SDATE']}';
			declare @todt datetime		 = '{$arrs['EDATE']}';
			declare @locat varchar(5)	 = ".($arrs['LOCAT'] == "" ? "'%'":"'".$arrs['LOCAT']."'").";
			declare @type varchar(20)	 = ".($arrs['TYPE'] == "" ? "'%'":"'".$arrs['TYPE']."'").";
			declare @model varchar(20)	 = ".($arrs['MODEL'] == "" ? "'%'":"'".$arrs['MODEL']."'").";
			declare @stat varchar(1)	 = ".($arrs['STAT']).";
			declare @turnover varchar(1) = '{$arrs['turnover']}';
			
			select * into #temp_stc from (
				select row_number() over(order by seq) as r,seq,model,ffm
					,case when model='โอนย้ายสินค้า' then stklocat else fmlocat end fmlocat
					,case when model='โอนย้ายสินค้า' then fmlocat else stklocat end stklocat
					,refno,convert(varchar(8),stkdate,112) as stkdate
					,strno,baab,color,qtyin,qtyout,total
				from {$this->MAuth->getdb('stockcard')}(@fmdt,@todt,@locat,@type,@model,@stat,@turnover)
			) as data
		";
		$this->db->query($sql);
		
		$sql 	 = "select count(*) as r from #temp_stc";
		$query 	 = $this->db->query($sql);
		$row 	 = $query->row();
		$row_all = $row->r;
		
		$date = new DateTime();
		$logDT .= "<br> โหลดข้อมูลเสร็จ :: ".$date->format('Y-m-d H:i:s'); 
		
		$data_sum = array();
		$NRow = 0;
		$bod = "";
		
		// Loop รันข้อมูลครั้งละ 3000 แถว  เนื่องจากมีข้อมูลจำนวนมาก ทำให้ PHP ไม่สามารถดึงข้อมูลมาแสดงได้ 
		for($query_run = 1;$query_run <= $row_all; $query_run += 3000){			
			$sql = "
				select * from #temp_stc
				where 1=1 and r between ".$query_run." and ".($query_run+3000)."
			";
			$query 	 = $this->db->query($sql);
			
			if($query->row()){
				foreach($query->result() as $row){
					if($row->ffm == 2 and $row->model == ""){ continue; }
					if($row->ffm == 1){
						$data_sum["turnover"] = $row->total;
					}else if($row->ffm == 3){
						$data_sum["turnover"] = $row->total;
						$data_sum["turnoverSum"] = (isset($data_sum["turnoverSum"]) ? $data_sum["turnoverSum"] : 0) + $data_sum["turnover"];
					}else{
						$data_sum["turnover"] = $data_sum["turnover"] + $row->total;
					}
					
					$bod .= "
						<tr>
							<td style='width:50px;max-width:50px;'>".($row->ffm == 1 ? ++$NRow:"")."</td>
							<td style='width:170px;max-width:170px;'>".($row->ffm == 3 ? "":$row->model)."</td>
							<td style='width:80px;max-width:80px;'>".$row->fmlocat."</td>
							<td style='width:70px;max-width:70px;'>".($row->ffm != 2 ? "":$row->stklocat)."</td>
							<td style='width:90px;max-width:90px;;'>".$row->refno."</td>
							<td style='width:60px;max-width:60px;'>".$this->Convertdate(2,$row->stkdate)."</td>
							<td style='width:110px;max-width:110px;'>".$row->strno."</td>
							<td style='width:40px;max-width:40px;'>".$row->baab."</td>
							<td style='width:120px;max-width:120px;'>".$row->color."</td>
							<td align='right' style='width:60px;max-width:60px;'>".($row->ffm == 2 ? ($row->qtyin == 0 ? "" :$row->qtyin) : "")."</td>
							<td align='right' style='width:60px;max-width:60px;'>".($row->ffm == 2 ? ($row->qtyout == 0 ? "" :$row->qtyout) : "")."</td>
							<td style='width:70px;max-width:70px;'>".($row->ffm == 2 ? "":($row->ffm == 1 ? "ยอดยกมา":"ยอดยกไป"))."</td>
							<td align='right' style='width:70px;max-width:70px;'>".$data_sum["turnover"]."</td>
						</td>
					";
				}
			}			
		}
		
		$sql = "select comp_nm,comp_adr1,comp_adr2,telp,taxid from {$this->MAuth->getdb('condpay')}";
		$query = $this->db->query($sql);
		
		$company	= '';
		$compadr1	= '';
		$comptax	= '';
		$reportName = 'รายงานสินค้าและวัตถุดิบ';
		$condDesc	= '';
		if($query->row()){
			foreach($query->result() as $row){
				$company = $row->comp_nm;
				$compadr1 = $row->comp_adr1." ".$row->comp_adr2;
				$comptax = $row->taxid;
			}
		}
		
		$bod .= "			
			<tr class='wf fs7'>
				<td class='bor3' style='width:50px;max-width:50px;'>&emsp;</td>
				<td class='bor3' style='width:170px;max-width:170px;'>รวมทั้งสิ้น</td>
				<td class='bor3' style='width:80px;max-width:80px;'>".$NRow."</td>
				<td class='bor3' style='width:70px;max-width:70px;'>รายการ</td>
				<td class='bor3' style='width:90px;max-width:90px;;'>&emsp;</td>
				<td class='bor3' style='width:60px;max-width:60px;'>&emsp;</td>
				<td class='bor3' style='width:110px;max-width:110px;'>&emsp;</td>
				<td class='bor3' style='width:50px;max-width:50px;'>&emsp;</td>
				<td class='bor3' style='width:110px;max-width:110px;'>&emsp;</td>
				<td class='bor3' align='right' style='width:60px;max-width:60px;'>&emsp;</td>
				<td class='bor3' align='right' style='width:60px;max-width:60px;'>&emsp;</td>
				<td class='bor3' style='width:70px;max-width:70px;'>รวมยอดยกไป</td>
				<td class='bor3' align='right' style='width:70px;max-width:70px;'>".$data_sum["turnoverSum"]."</td>
			</tr>			
		";
		$bod = "<table class='fs7'>".$bod."</table>";
		
		$mpdf = new \Mpdf\Mpdf([
			'mode' => 'utf-8', 
			'format' => 'A4-L',
			'margin_top' => 80, 	//default = 16
			'margin_left' => 8, 	//default = 15
			'margin_right' => 8, 	//default = 15
			'margin_bottom' => 6, 	//default = 16
			'margin_header' => 6, 	//default = 9
			'margin_footer' => 9, 	//default = 9
		]);
		
		$stylesheet = "
			<style>
				body { font-family: garuda;font-size:9pt; }
				.wf { width:100%; }
				.pdlr { pedding-left:2px;pedding-right:2px; }
				.fs7 { font-size:7pt; }
				.fs12 { font-size:12pt; }
				.h10 { height:10px; }
				.tc { text-align:center; }
				.pf { position:fixed; }
				.bor { border:0.1px solid white; }
				.bor2 { border:0.1px dotted black; }
				.bor3 { border-top:0.5px solid black;border-bottom:0.5px solid black; }
				.data { background-color:#fff;font-size:9pt; }
			</style>
		";
		
		$locat = " ระหว่างวันที่ ".$this->Convertdate(2,$arrs['SDATE'])." ถึงวันที่ ".$this->Convertdate(2,$arrs['EDATE']);
		$content = "
			<div class='wf tc fs12'><b>{$company}</b></div>
			<div class='wf tc fs12'><b>{$reportName}</b></div>
			<div class='wf tc'>{$locat}</div>
			<div class='wf'>ชื่อผู้ประกอบการ</div>
			<div class='wf'>ชื่อสถานที่ประกอบการ : {$compadr1}</div>
			<div class='wf'>เลขประจำตัวผู้เสียภาษี : {$comptax}</div>
			<div style='width:50%;float:left;'>วันที่พิมพ์รายงาน : ".date('d/m/').(date('Y')+543)." ".date('H:i')."</div>
			<div style='width:50%;float:left;text-align:right;'>หน้าที่ : {PAGENO} / {nb} &emsp;&emsp;</div>
			<hr>
			<div class='wf'>
				<div style='width:50px;float:left;'>ลำดับที่</div>
				<div style='width:170px;float:left;'>รุ่น<br>ประเภทการเคลื่อนไหว</div>
				<div style='width:80px;float:left;'>&emsp;<br>โอนจากสาขา</div>
				<div style='width:70px;float:left;'>&emsp;<br>โอนไปสาขา</div>
				<div style='width:90px;float:left;;float:left;'>&emsp;<br>ใบสำคัญเลขที่</div>
				<div style='width:60px;float:left;'>&emsp;<br>วดป.</div>
				<div style='width:110px;float:left;'>&emsp;<br>เลขตัวถัง</div>
				<div style='width:40px;float:left;'>&emsp;<br>แบบ</div>
				<div style='width:120px;float:left;'>&emsp;<br>สี</div>
				<div style='width:60px;float:left;'>&emsp;<br>จำนวนรับ</div>
				<div style='width:60px;float:left;'>&emsp;<br>จำนวนจ่าย</div>
				<div style='width:70px;float:left;'>&emsp;<br>&emsp;</div>
				<div style='width:70px;float:left;'>&emsp;<br>ยอดคงเหลือ</div>
			</div>
			<hr>
		";		
		$mpdf->SetHTMLHeader($content);
		$mpdf->WriteHTML($bod.$stylesheet);
		
		$date = new DateTime();
		$logDT .= "<br> สร้างไฟล์ PDF เสร็จ :: ".$date->format('Y-m-d H:i:s'); 
		$mpdf->addPage();
		$mpdf->WriteHTML($logDT);
		//$mpdf->SetHTMLFooter("<div class='wf pf' style='top:730;left:0;font-size:6pt;width:1020px;text-align:right;'>{$this->sess["name"]} ออกเอกสาร ณ วันที่ ".date('d/m/').(date('Y')+543)." ".date('H:i')."</div>");
		$mpdf->fontdata['qanela'] = array('R' => "QanelasSoft-Regular.ttf",'B' => "QanelasSoft-Bold.ttf",); //แก้ปริ้นแล้วอ่านไม่ออก
		//$mpdf->Output('report.pdf', 'D');
		//$mpdf->Output('x.pdf','F');
		$mpdf->Output();
	}
	
	
	function stockcardPDF(){
		ini_set("memory_limit","-1");
		ini_set("pcre.backtrack_limit", "100000000");
		
		/* declare filename start*/
		$date = new DateTime();
		$text = $this->sess["db"]."_".$this->sess["USERID"]."_".$date->format('Ymd_His_'); 
		$data = array($text);
		$filename = $this->generateData($data,'encode');
		$filename = $filename[0].".pdf";
		$filename = $text.".pdf";
		/* declare filename end*/
		
		$logDT = "";
		$date = new DateTime();
		$logDT .= "เริ่มต้น :: ".$date->format('Y-m-d H:i:s'); 

		$arrs = array();
		$arrs['LOCAT']	  = $_POST['LOCAT'];
		$arrs['SDATE']    = $_POST['SDATE'];
		$arrs['EDATE']    = $_POST['EDATE'];
		$arrs['TYPE'] 	  = $_POST['TYPE'];
		$arrs['MODEL'] 	  = $_POST['MODEL'];
		$arrs['STAT'] 	  = $_POST['STAT'];
		$arrs['REPORT']   = $_POST['REPORT'];
		$arrs['turnover'] = $_POST['turnover'];
		
		if($arrs['STAT'] == 'A'){
			$arrs['STAT'] = "null";
		}else{
			$arrs['STAT'] = "'".$arrs['STAT']."'";
		}
		
		/*start header*/
		$sql = "select comp_nm,comp_adr1,comp_adr2,telp,taxid from {$this->MAuth->getdb('condpay')}";
		$query = $this->db->query($sql);
		
		
		$arrs['company']	= '';
		$arrs['compadr1']	= '';
		$arrs['comptax']	= '';
		$arrs['reportName'] = 'รายงานสินค้าและวัตถุดิบ';
		$arrs['condDesc']	= '';
		if($query->row()){
			foreach($query->result() as $row){
				$arrs['company'] = $row->comp_nm;
				$arrs['compadr1'] = $row->comp_adr1." ".$row->comp_adr2;
				$arrs['comptax'] = $row->taxid;
			}
		}
		/*end header*/
		
		$sql = "
			declare @fmdt datetime		 = '{$arrs['SDATE']}';
			declare @todt datetime		 = '{$arrs['EDATE']}';
			declare @locat varchar(5)	 = ".($arrs['LOCAT'] == "" ? "'%'":"'".$arrs['LOCAT']."'").";
			declare @type varchar(20)	 = ".($arrs['TYPE'] == "" ? "'%'":"'".$arrs['TYPE']."'").";
			declare @model varchar(20)	 = ".($arrs['MODEL'] == "" ? "'%'":"'".$arrs['MODEL']."'").";
			declare @stat varchar(1)	 = ".($arrs['STAT']).";
			declare @turnover varchar(1) = '{$arrs['turnover']}';
			
			select * into #temp_stc from (
				select row_number() over(order by seq) as r,seq,model,ffm
					,case when model='โอนย้ายสินค้า' then stklocat else fmlocat end fmlocat
					,case when model='โอนย้ายสินค้า' then fmlocat else stklocat end stklocat
					,refno,convert(varchar(8),stkdate,112) as stkdate
					,strno,baab,color,qtyin,qtyout,total
				from {$this->MAuth->getdb('stockcard')}(@fmdt,@todt,@locat,@type,@model,@stat,@turnover)
			) as data
		";
		$this->db->query($sql);
		
		$date = new DateTime();
		$logDT .= "<br> โหลดข้อมูลเสร็จ :: ".$date->format('Y-m-d H:i:s'); 
		
		$sql 		= "select count(*) as r from #temp_stc";
		$query 		= $this->db->query($sql);
		$row 	 	= $query->row();
		$row_all 	= $row->r;
		$_seq	 	= 3000; // จำนวนข้อมูล แต่ละรอบ
		$file_all 	= (int) ($row_all / $_seq);
		$file_all	= $file_all + ($row_all % $_seq == 0 ? 0 : 1);
		
		$data_sum 	= array();
		$NRow 		= 0;
		$bod 		= "";
		$arrs["filename"] = array();
		
		$s = 0;
		$e = 0;
		// Loop รันข้อมูลครั้งละ 3000 แถว  เนื่องจากมีข้อมูลจำนวนมาก ทำให้ PHP ไม่สามารถดึงข้อมูลมาแสดงได้ 
		for($query_run = 1;$query_run <= $file_all; $query_run++){
			$islast 	= ($query_run == $file_all ? true:false);
			
			$ex = explode("_",$filename);
			$filename_replace 	= 'public/pdffile/'.$ex[0]."_".$ex[1]."_".$ex[2]."_".$ex[3]."_".$query_run.".pdf";
			$arrs["filename"][] = $filename_replace;
			
			if($query_run == 1){
				$s = 1;
				$e = $_seq;
			}else{
				$s = $s+$_seq;
				$e = $e+$_seq;
			}
			
			$sql = "
				select * from #temp_stc
				where 1=1 and r between ".$s." and ".$e."
			";
			$result = $this->stockcardPDF_SAVEFILE($filename_replace,$NRow,$sql,$arrs,$data_sum,$islast);
			//print_r($result); exit;
			$NRow 	= $result["NRow"];
			$data_sum["turnover"] 	 = $result["data_sum"]["turnover"];
			$data_sum["turnoverSum"] = $result["data_sum"]["turnoverSum"];
		}
		
		$date = new DateTime();
		$logDT .= "<br> สร้างไฟล์ PDF เสร็จ :: ".$date->format('Y-m-d H:i:s'); 
		
		$mpdf = new \Mpdf\Mpdf([
			'mode' => 'utf-8', 
			'format' => 'A4-L',
			'margin_top' => 80, 	//default = 16
			'margin_left' => 8, 	//default = 15
			'margin_right' => 8, 	//default = 15
			'margin_bottom' => 6, 	//default = 16
			'margin_header' => 6, 	//default = 9
			'margin_footer' => 9, 	//default = 9
		]);
		
		$pdf_all = sizeof($arrs["filename"]);
		$pageall = 0;
		$pagenow = 0;
		
		for($i=0;$i<$pdf_all;$i++){
			$pageall += $mpdf->SetSourceFile($arrs["filename"][$i]);
		}
		
		for($i=0;$i<$pdf_all;$i++){
			$mpdf->SetImportUse();
			$pagecount = $mpdf->SetSourceFile($arrs["filename"][$i]);
			
			for($j = 1; $j<=$pagecount;$j++){
				if($i != 0){
					$mpdf->AddPage();
				} else if($j != 1) {
					$mpdf->AddPage();
				}
				$tplId = $mpdf->ImportPage($j);
				$mpdf->UseTemplate($tplId);
				$mpdf->WriteHTML("<div style='position:fixed;top:-105px;left:970px;width:100px;font-size:7pt;font-family: garuda;font-size:9pt;'>หน้าที่ ".(++$pagenow)." / ".$pageall."</div>");
				$mpdf->fontdata['qanela'] = array('R' => "QanelasSoft-Regular.ttf",'B' => "QanelasSoft-Bold.ttf",); //แก้ปริ้นแล้วอ่านไม่ออก
			}
		}
		
		$date = new DateTime();
		$logDT .= "<br> รวมไฟล์ :: ".$date->format('Y-m-d H:i:s'); 
		
		$mpdf->addPage();
		$mpdf->WriteHTML("<div style='width:100%;font-family: garuda;font-size:9pt;'>".$logDT."</div>");
		$mpdf->fontdata['qanela'] = array('R' => "QanelasSoft-Regular.ttf",'B' => "QanelasSoft-Bold.ttf",); //แก้ปริ้นแล้วอ่านไม่ออก
		
		$mpdf->Output();
		
		// ลบไฟล์
		for($i=0;$i<$pdf_all;$i++){ unlink($arrs["filename"][$i]); }
	}
	
	function stockcardPDF_SAVEFILE($filename,$NRow,$sql,$arrs,$data_sum,$islast){
		ini_set("memory_limit","-1");
		ini_set("pcre.backtrack_limit", "100000000");
		
		$query = $this->db->query($sql);
		$bod = "";
		if($query->row()){
			foreach($query->result() as $row){
				if($row->ffm == 2 and $row->model == ""){ continue; }
				if($row->ffm == 1){
					$data_sum["turnover"] = $row->total;
				}else if($row->ffm == 3){
					$data_sum["turnover"] = $row->total;
					$data_sum["turnoverSum"] = (isset($data_sum["turnoverSum"]) ? $data_sum["turnoverSum"] : 0) + $data_sum["turnover"];
				}else{
					$data_sum["turnover"] = $data_sum["turnover"] + $row->total;
				}
				
				$bod .= "
					<tr>
						<td style='width:50px;max-width:50px;'>".($row->ffm == 1 ? ++$NRow:"")."</td>
						<td style='width:170px;max-width:170px;'>".($row->ffm == 3 ? "":$row->model)."</td>
						<td style='width:80px;max-width:80px;'>".$row->fmlocat."</td>
						<td style='width:70px;max-width:70px;'>".($row->ffm != 2 ? "":$row->stklocat)."</td>
						<td style='width:90px;max-width:90px;;'>".$row->refno."</td>
						<td style='width:60px;max-width:60px;'>".$this->Convertdate(2,$row->stkdate)."</td>
						<td style='width:110px;max-width:110px;'>".$row->strno."</td>
						<td style='width:40px;max-width:40px;'>".$row->baab."</td>
						<td style='width:120px;max-width:120px;'>".$row->color."</td>
						<td align='right' style='width:60px;max-width:60px;'>".($row->ffm == 2 ? ($row->qtyin == 0 ? "" :$row->qtyin) : "")."</td>
						<td align='right' style='width:60px;max-width:60px;'>".($row->ffm == 2 ? ($row->qtyout == 0 ? "" :$row->qtyout) : "")."</td>
						<td style='width:70px;max-width:70px;'>".($row->ffm == 2 ? "":($row->ffm == 1 ? "ยอดยกมา":"ยอดยกไป"))."</td>
						<td align='right' style='width:70px;max-width:70px;'>".$data_sum["turnover"]."</td>
					</td>
				";
			}
		}
		
		// ข้อมูลชุดสุดท้ายหรือไม่ TRUE or FLASE
		if($islast){
			$bod .= "			
				<tr class='wf fs7'>
					<td class='bor3' style='width:50px;max-width:50px;'>&emsp;</td>
					<td class='bor3' style='width:170px;max-width:170px;'>รวมทั้งสิ้น</td>
					<td class='bor3' style='width:80px;max-width:80px;'>".$NRow."</td>
					<td class='bor3' style='width:70px;max-width:70px;'>รายการ</td>
					<td class='bor3' style='width:90px;max-width:90px;;'>&emsp;</td>
					<td class='bor3' style='width:60px;max-width:60px;'>&emsp;</td>
					<td class='bor3' style='width:110px;max-width:110px;'>&emsp;</td>
					<td class='bor3' style='width:50px;max-width:50px;'>&emsp;</td>
					<td class='bor3' style='width:110px;max-width:110px;'>&emsp;</td>
					<td class='bor3' align='right' style='width:60px;max-width:60px;'>&emsp;</td>
					<td class='bor3' align='right' style='width:60px;max-width:60px;'>&emsp;</td>
					<td class='bor3' style='width:70px;max-width:70px;'>รวมยอดยกไป</td>
					<td class='bor3' align='right' style='width:70px;max-width:70px;'>".$data_sum["turnoverSum"]."</td>
				</tr>
			";
		}
		$bod = "<table class='fs7'>".$bod."</table>";
		
		$mpdf = new \Mpdf\Mpdf([
			'mode' => 'utf-8', 
			'format' => 'A4-L',
			'margin_top' => 80, 	//default = 16
			'margin_left' => 8, 	//default = 15
			'margin_right' => 8, 	//default = 15
			'margin_bottom' => 6, 	//default = 16
			'margin_header' => 6, 	//default = 9
			'margin_footer' => 9, 	//default = 9
		]);
		
		$stylesheet = "
			<style>
				body { font-family: garuda;font-size:9pt; }
				.wf { width:100%; }
				.pdlr { pedding-left:2px;pedding-right:2px; }
				.fs7 { font-size:7pt; }
				.fs12 { font-size:12pt; }
				.h10 { height:10px; }
				.tc { text-align:center; }
				.pf { position:fixed; }
				.bor { border:0.1px solid white; }
				.bor2 { border:0.1px dotted black; }
				.bor3 { border-top:0.5px solid black;border-bottom:0.5px solid black; }
				.data { background-color:#fff;font-size:9pt; }
			</style>
		";
		
		$locat = " ระหว่างวันที่ ".$this->Convertdate(2,$arrs['SDATE'])." ถึงวันที่ ".$this->Convertdate(2,$arrs['EDATE']);
		$content = "
			<div class='wf tc fs12'><b>{$arrs['company']}</b></div>
			<div class='wf tc fs12'><b>{$arrs['reportName']}</b></div>
			<div class='wf tc'>{$locat}</div>
			<div class='wf'>ชื่อผู้ประกอบการ</div>
			<div class='wf'>ชื่อสถานที่ประกอบการ : {$arrs['compadr1']}</div>
			<div class='wf'>เลขประจำตัวผู้เสียภาษี : {$arrs['comptax']}</div>
			<div style='width:100%;float:left;'>วันที่พิมพ์รายงาน : ".date('d/m/').(date('Y')+543)." ".date('H:i')."</div>
			<!-- div style='width:50%;float:left;text-align:right;'>หน้าที่ : {PAGENO} / {nb} &emsp;&emsp;</div -->
			
			<hr>
			<div class='wf'>
				<div style='width:50px;float:left;'>ลำดับที่</div>
				<div style='width:170px;float:left;'>รุ่น<br>ประเภทการเคลื่อนไหว</div>
				<div style='width:80px;float:left;'>&emsp;<br>โอนจากสาขา</div>
				<div style='width:70px;float:left;'>&emsp;<br>โอนไปสาขา</div>
				<div style='width:90px;float:left;;float:left;'>&emsp;<br>ใบสำคัญเลขที่</div>
				<div style='width:60px;float:left;'>&emsp;<br>วดป.</div>
				<div style='width:110px;float:left;'>&emsp;<br>เลขตัวถัง</div>
				<div style='width:40px;float:left;'>&emsp;<br>แบบ</div>
				<div style='width:120px;float:left;'>&emsp;<br>สี</div>
				<div style='width:60px;float:left;'>&emsp;<br>จำนวนรับ</div>
				<div style='width:60px;float:left;'>&emsp;<br>จำนวนจ่าย</div>
				<div style='width:70px;float:left;'>&emsp;<br>&emsp;</div>
				<div style='width:70px;float:left;'>&emsp;<br>ยอดคงเหลือ</div>
			</div>
			<hr>
		";		
		$mpdf->SetHTMLHeader($content);
		$mpdf->WriteHTML($bod.$stylesheet);
		
		// $mpdf->SetHTMLFooter("<div class='wf pf' style='top:730;left:0;font-size:6pt;width:1020px;text-align:right;'>{$this->sess["name"]} ออกเอกสาร ณ วันที่ ".date('d/m/').(date('Y')+543)." ".date('H:i')."</div>");
		$mpdf->fontdata['qanela'] = array('R' => "QanelasSoft-Regular.ttf",'B' => "QanelasSoft-Bold.ttf",); //แก้ปริ้นแล้วอ่านไม่ออก
		// $mpdf->Output('report.pdf', 'D');
		// $mpdf->Output('x.pdf','F');
		$mpdf->Output($filename,'F');
		
		$response = array(
			'NRow' => $NRow,
			'data_sum' => array(
				'turnover' => $data_sum["turnover"],
				'turnoverSum' => $data_sum["turnoverSum"]
			)
		);
		
		return $response;
	}
	
	function formloadding(){
		echo "
			<img id='table-RPstc-print' 
				src='".base_url("/public/images/loading-icon2.gif")."' 
				style='width:30px;height:30px;cursor:pointer;margin-left:10px;'>
		";
	}
	
	function checkINOPT($contno){
		$sql = "
			select * from {$this->MAuth->getdb('ARINOPT')} 
			where CONTNO='".$contno."'
			order by OPTCODE
		";
		$q = $this->db->query($sql);
		$ARINOPT = array("TOPIC"=>"","OPTCODE"=>"","SIZE"=>"","QTY"=>"","UM"=>"","TUPRICE"=>"","UPRICE"=>"","UPRICE_UM"=>"","TTOTPRC"=>"","TOTPRC"=>"","TOTPRC_UM"=>"");
		if($q->row()){
			$ARINOPT_R = 0;
			foreach($q->result() as $r){
				if($ARINOPT_R++ != 0){
					$ARINOPT["TOPIC"] 	.= "<br>";
					$ARINOPT["OPTCODE"] .= "<br>";
					$ARINOPT["SIZE"] .= "<br>";
					$ARINOPT["QTY"] .= "<br>";
					$ARINOPT["UM"] .= "<br>";
					$ARINOPT["TUPRICE"] .= "<br>";
					$ARINOPT["UPRICE"] .= "<br>";
					$ARINOPT["UPRICE_UM"] .= "<br>";
					$ARINOPT["TTOTPRC"] .= "<br>";
					$ARINOPT["TOTPRC"] .= "<br>";
					$ARINOPT["TOTPRC_UM"] .= "<br>";
				}
				$ARINOPT["TOPIC"] 	.= "รหัสอุปกรณ์เสริม";
				$ARINOPT["OPTCODE"] .= $r->OPTCODE;
				$ARINOPT["SIZE"] .= "จำนวน";
				$ARINOPT["QTY"] .= $r->QTY;
				$ARINOPT["UM"] .=  "ชิ้น";
				$ARINOPT["TUPRICE"] .= "ราคาต่อหน่วย";
				$ARINOPT["UPRICE"] .= number_format($r->UPRICE,2);
				$ARINOPT["UPRICE_UM"] .= "บาท";
				$ARINOPT["TTOTPRC"] .= "ราคาขาย(รวมภาษี)";
				$ARINOPT["TOTPRC"] .= number_format($r->TOTPRC,2);
				$ARINOPT["TOTPRC_UM"] .= "บาท";
			}
		}
		
		return $ARINOPT;
	}
	
	function loadding(){
		$html = "
			<div align='center' style='width:100%;'>
				<input type='image' src='".base_url("public/images/loading-icon.gif")."' style='width:100%;'>
			</div>
		";
		echo $html;
	}
	
	function pdf(){
		$arrs = array();
		$arrs['locat']	= $_POST['locat'];
		$arrs['sSDATE'] = $this->Convertdate(1,$_POST['sSDATE']);
		$arrs['eSDATE'] = $this->Convertdate(1,$_POST['eSDATE']);
		$arrs['CUSCOD']	= $_POST['CUSCOD'];
		$arrs['MODEL'] 	= $_POST['MODEL'];
		$arrs['BAAB'] 	= $_POST['BAAB'];
		$arrs['COLOR'] 	= $_POST['COLOR'];
		$arrs['GCODE'] 	= $_POST['GCODE'];
		$arrs['REPORT'] = $_POST['REPORT'];
		$cond = "";
		$condDesc = "";
		if($arrs['locat'] != ""){
			$cond .= " and A.LOCAT like '".$arrs['locat']."%'";
			$condDesc .= "สาขา ".$arrs['locat'];
		}
		
		if($arrs['sSDATE'] != "" and $arrs['eSDATE'] != ""){
			$cond .= " and convert(varchar(8),A.RESVDT,112) between '".$arrs['sSDATE']."' and '".$arrs['eSDATE']."' ";
			$condDesc .= " วันที่จอง จากวันที่  ".$_POST['sSDATE']." - ".$_POST['eSDATE'];
		}else if($arrs['sSDATE'] != "" and $arrs['eSDATE'] == ""){
			$cond .= " and convert(varchar(8),A.RESVDT,112) = '".$arrs['sSDATE']."'";
			$condDesc .= " วันที่จอง  ".$_POST['sSDATE'];
		}else if($arrs['sSDATE'] == "" and $arrs['eSDATE'] != ""){
			$cond .= " and convert(varchar(8),A.RESVDT,112) = '".$arrs['eSDATE']."'";
			$condDesc .= " วันที่จอง  ".$_POST['eSDATE'];
		}
		
		if($arrs['MODEL'] != ""){
			$cond .= " and A.MODEL like '".$arrs['MODEL']."%'";
			$condDesc .= " รุ่น  ".$_POST['MODEL'];
		}
		
		if($arrs['BAAB'] != ""){
			$cond .= " and A.BAAB like '".$arrs['BAAB']."%'";
			$condDesc .= " แบบ ".$_POST['BAAB'];
		}
		
		if($arrs['COLOR'] != ""){
			$cond .= " and A.COLOR like '".$arrs['COLOR']."%'";
			$condDesc .= " สี ".$_POST['COLOR'];
		}
		
		if($arrs['GCODE'] != ""){
			$cond .= " and A.GRPCOD like '".$arrs['GCODE']."%'";
			$condDesc .= " กลุ่มรถ ".$_POST['GCODE'];
		}
		
		if($arrs['CUSCOD'] != ""){
			$cond .= " and A.CUSCOD like '".$arrs['CUSCOD']."'";
			$condDesc .= " รหัสลูกค้า ".$_POST['CUSCOD'];
		}
		
		if($arrs['REPORT'] == 1){
			$condDesc = " รายงานการจองรถ :: ".$condDesc;
		}else if($arrs['REPORT'] == 2){
			$cond .= " and isnull(A.STRNO,'') = ''";
			$condDesc = " รายงานการจองรถไม่ระบุเลขถัง :: ".$condDesc;
		}else if($arrs['REPORT'] == 3){
			$cond .= " and A.SDATE is null";
			$condDesc = " รายงานการจองรถยังไม่ได้ขาย :: ".$condDesc;
		}else if($arrs['REPORT'] == 4){
			$cond .= " and A.SDATE is not null";
			$condDesc = " รายงานการขายรถจอง :: ".$condDesc;
		}
		
		$sql = "select COMP_NM from {$this->MAuth->getdb('CONDPAY')}";
		$query = $this->db->query($sql);
		$company = "";
		if($query->row()){
			foreach($query->result() as $row){
				$company = $row->COMP_NM;
			}
		}
		
		$sql = "
			SELECT A.CUSCOD,(select SNAM+NAME1+' '+NAME2 from {$this->MAuth->getdb('CUSTMAST')} CM where CM.CUSCOD=A.CUSCOD) as CUSNAME
				,A.RESVNO,A.LOCAT,convert(varchar(8),A.RESVDT,112) as RESVDT
				,convert(varchar(8),A.RECVDUE,112) as RECVDUE
				,A.GRPCOD,A.TYPE,A.BAAB,A.MODEL,A.COLOR,A.CC,A.STAT
				,A.SALCOD,A.VATRT,A.PRICE,A.RESPAY,A.BALANCE,A.SMPAY,A.SMCHQ,A.STRNO,A.ISSUNO
				,A.RECVDT,A.RECVCD,A.SDATE,A.TAXNO,A.TAXDT,A.MEMO1,A.REQNO,A.REQLOCAT,A.POSTDT,A.INPDT,A.USERID
				,A.GRPCOD
			FROM {$this->MAuth->getdb('ARRESV')} A
			WHERE 1=1 ".$cond."
			ORDER BY A.RESVNO
		";
		$query = $this->db->query($sql);
		
		$html = "";
		$NRow = 1;		
		if($query->row()){
			foreach($query->result() as $row){
				$html .= "
					<div style='line-height:22px;font-size:8pt;border-bottom:1px dotted black;'>
						<div class='tl' style='width:50px;line-height:20px;float:left;'><br>".$row->LOCAT."<br></div>
						<div class='tl' style='width:100px;line-height:20px;float:left;'>".$row->RESVNO."<br>".$this->Convertdate(2,$row->RESVDT)."<br>".$this->Convertdate(2,$row->RECVDUE)."</div>
						<div class='tl' style='width:200px;line-height:20px;float:left;'>".$row->CUSCOD."<br>".$row->CUSNAME."</div>
						<div class='tl' style='width:170px;line-height:20px;float:left;'>".$row->STRNO."<br>".$row->GRPCOD."</div>
						<div class='tl' style='width:150px;line-height:20px;float:left;'>".$row->MODEL."<br>".$row->COLOR."</div>
						<div class='tl' style='width:80px;line-height:20px;float:left;'>".$row->BAAB."<br>".$row->CC."</div>
						<div class='tr' style='width:110px;line-height:20px;float:left;'>".number_format($row->PRICE,2)."<br>".number_format($row->RESPAY,2)."<br>".number_format($row->BALANCE,2)."</div>
						<div class='tc' style='width:160px;line-height:20px;float:left;'><br>".$row->SALCOD."<br></div>
						
					</div>
				";
				$NRow++;
			}
		}
		
		$head = "
			<div style='line-height:24px;'>
				<div class='wf tc f14'><b>{$company}</b></div>
				<div class='wf tc'><b>เงื่อนไข {$condDesc}</b></div>
				<div class='wf'><hr></div>
				<div class='tc' style='width:50px;line-height:20px;float:left;'><b><br>สาขา<br></b></div>
				<div class='tc' style='width:100px;line-height:20px;float:left;'><b>เลขที่บิลจอง<br>วันที่จอง<br>วันที่นัดรับรถ</b></div>
				<div class='tc' style='width:200px;line-height:20px;float:left;'><b>รหัสลูกค้า<br>ชื่อ-สกุล</b></div>
				<div class='tc' style='width:170px;line-height:20px;float:left;'><b>เลขตัวถัง<br>กลุ่มรถ</b></div>
				<div class='tc' style='width:150px;line-height:20px;float:left;'><b>รุ่น<br>สี</b></div>
				<div class='tc' style='width:80px;line-height:20px;float:left;'><b>แบบ<br>ขนาด</b></div>
				<div class='tc' style='width:110px;line-height:20px;float:left;'><b>ราคารถ<br>จอง<br>คงเหลือ</b></div>
				<div class='tc' style='width:160px;line-height:20px;float:left;'><b><br>พนักงานขาย<br></b></div>
				<div class='wf'><hr></div>
			</div>
		";
		
		try {
			$mpdf = new \Mpdf\Mpdf([
				'mode' => 'utf-8',
				'format' => 'A4-L',
				'margin_top' => 48, 	//default = 16
				'margin_left' => 10, 	//default = 15
				'margin_right' => 10, 	//default = 15
				'margin_bottom' => 16, 	//default = 16
				'margin_header' => 9, 	//default = 9
				'margin_footer' => 2, 	//default = 9
			]);

			$stylesheet = "
				<style>
					body { font-family: garuda;font-size:10pt; }
					.wf { width:100%; }
					.f14 { font-size:14pt; }
					.h10 { height:10px; }
					.tc { text-align:center; }
					.tl { text-align:left; }
					.tr { text-align:right; }
					.pf { position:fixed; }
					.bor { border:0.5px solid black; }
					.bor2 { border:0.1px dotted black; }
				</style>
			";
			$content = $html.$stylesheet;
			
			$mpdf->SetHTMLHeader($head);
			$mpdf->SetHTMLFooter("
				<div class='wf pf' style='top:720;font-size:6pt;text-align:right;'>พิมพ์โดย :: ".$this->sess["name"]." ".date('d/m/').(date('Y')+543)." ".date('H:i')." หน้า {PAGENO} / {nbpg}</div>
			");
			$mpdf->WriteHTML($content);
				
			$mpdf->Output();
		} catch (Exception $e) {
			die('Error loading file "' . pathinfo($file, PATHINFO_BASENAME). '": ' . $e->getMessage());
		}
	}
	
}




















