<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//BEE+
class Formbycontno extends MY_Controller {
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
	
	//หน้าแรก
	function index(){
		$claim = $this->MLogin->getclaim(uri_string());
		if($claim['m_access'] != "T"){ echo "<div align='center' style='color:red;font-size:16pt;width:100%;'>ขออภัย คุณยังไม่มีสิทธิเข้าใช้งานหน้านี้ครับ</div>"; exit; }

		$html = "
			<div class='b_tab1' name='home' locat='{$this->sess['branch']}' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}' today='".$this->today('today')."' style='height:calc(100vh - 132px);overflow:auto;background-color:white;'>
				<div class='col-sm-12 col-xs-12' style='height:100%;overflow:auto;font-size:10.5pt;'>					
					<div class='row' style='height:90%;'>
						<br>
						<div class='col-sm-10 col-xs-10 col-sm-offset-1' style='border:0.5px dotted #afe4cf;background-color:#d5f2fb;' >
							<br>
							<div class='col-sm-3 col-xs-3'>	
								<div class='form-group text-primary' >
									<b>เลขที่ใบกำกับ</b>
									<select id='INVNO1' class='form-control input-sm' data-placeholder='เลขที่ใบกำกับ'></select>
								</div>
							</div>
							<div class='col-sm-3 col-xs-3'>	
								<div class='form-group text-primary' >
									<b>เลขที่สัญญา</b>
									<select id='CONTNO1' class='form-control input-sm' data-placeholder='เลขที่สัญญา'></select>
								</div>
							</div>
							<div class='col-sm-4 col-xs-4'>	
								<div class='form-group text-primary' >
									<b>รหัสลูกค้า</b>
									<select id='CUSCOD1' class='form-control input-sm' data-placeholder='รหัสลูกค้า'></select>
								</div>
							</div>
							<div class='col-sm-2 col-xs-2'>	
								<div class='form-group'>
									<br>
									<button id='btnsearch' class='btn btn-cyan btn-sm' style='width:100%;font-size:10.5pt;'><b>ค้นหา</b></button>
								</div>
							</div>
						</div>
						<div class='col-sm-10 col-xs-10 col-sm-offset-1' style='height:20px;'></div>
						<div class='col-sm-10 col-xs-10 col-sm-offset-1' style='border:5px solid #dafbeb;background-color:#dafbeb;'>
							<div class='col-sm-12 col-xs-12' >
								<div id='dataTable-fixed-taxdata' class='dataTables_wrapper dt-bootstrap4 table-responsive' style='height:200px;width:100%;overflow:auto;'>
									<table id='dataTables-taxdata' class='table table-bordered dataTable table-hover' stat='' aria-describedby='dataTable_info' cellspacing='0' width='calc(100% - 1px)'>
										<thead>
											<tr role='row' style='height:30px;font-size:8pt;background-color:#19be87;color:white;'>
												<th style='vertical-align:middle;'>เลขที่ใบกำกับ</th>
												<th style='vertical-align:middle;'>วันที่ใบกำกับ</th>
												<th style='vertical-align:middle;'>เลขที่สัญญา</th>
												<th style='vertical-align:middle;'>รหัสลูกค้า</th>
												<th style='vertical-align:middle;'>ชื่อ - สกุล ลูกค้า</th>
												<th style='vertical-align:middle;text-align:right;'>จำนวน</th>
												<th style='vertical-align:middle;text-align:center;'>flag</th>
											</tr>
										</thead>
										<tbody style='white-space:nowrap;background-color:white;font-size:9pt;'></tbody>
									</table>
								</div>
							</div>
						</div>
						<div class='col-sm-10 col-xs-10 col-sm-offset-1' style='height:20px;'></div>
						<div class='col-sm-10 col-xs-10 col-sm-offset-1' style='border:0.1px solid #f6fefa;background-color:#d5f2fb;'>
							<div class='col-sm-4 col-xs-4 col-sm-offset-2'>	
								<div class='form-group'>
									จากใบกำกับ
									<input type='text' id='FRMINVNO' class='form-control input-sm' style='font-size:10.5pt'>
								</div>
							</div>
							<div class='col-sm-4 col-xs-4'>	
								<div class='form-group'>
									ถึงใบกำกับ
									<input type='text' id='TOINVNO' class='form-control input-sm' style='font-size:10.5pt'>
								</div>
							</div>
						</div>
						<div class='col-sm-10 col-xs-10 col-sm-offset-1' style='height:40px;top:20px;'>
							<div class='col-sm-6 col-xs-6'>ประเภท</div>
							<div class='col-sm-6 col-xs-6'>ที่อยู่</div>
						</div>
						<div class='col-sm-5 col-xs-5 col-sm-offset-1' style='border:0.1px solid #f6fefa;background-color:#d5f2fb;'>
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group'>
									<br>
									<input type= 'radio' id='form' name='printtype' checked> ฟอร์ม
								</div>
							</div>
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group'>
									<br>
									<input type= 'radio' id='report' name='printtype'> รายงาน
								</div>
							</div>
						</div>
						<div class='col-sm-5 col-xs-5' style='border:0.1px solid #f6fefa;background-color:#d5f2fb;'>
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group'>
									<br>
									<input type= 'radio' id='add1' name='address'> ที่อยู่ที่ติดต่อ
								</div>
							</div>
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group'>
									<br>
									<input type= 'radio' id='add2' name='address' checked> ที่อยู่ตามสัญญา
								</div>
							</div>
						</div>
					</div>
					<div class='row' style='height:10%;'>
						<div class='col-sm-10 col-xs-10 col-sm-offset-1'>
							<button id='btnprint' class='btn btn-primary btn-sm' style='width:100%;font-size:10.5pt;'><span class='glyphicon glyphicon-search'> พิมพ์ใบกำกับ</span></button>	
						</div>
					</div>
				</div>
			</div>
		";
		
		$html.= "<script src='".base_url('public/js/SYS07/Formbycontno.js')."'></script>";
		echo $html;
	}
	
	function searchINVNO(){
		$INVNO1		= $_REQUEST["INVNO1"];
		$CONTNO1	= $_REQUEST["CONTNO1"];
		$CUSCOD1	= $_REQUEST["CUSCOD1"];
		$response = array();
		
		$sql = "
				select TAXNO, convert(char,TAXDT,112) as TAXDT, CONTNO, CUSCOD, SNAM, NAME1, NAME2, TOTAMT, FLAG
				from {$this->MAuth->getdb('TAXTRAN')}
				where 	TAXNO like '%".$INVNO1."%' collate thai_cs_as
						and CONTNO like '%".$CONTNO1."%' collate thai_cs_as
						and CUSCOD like '%".$CUSCOD1."%' collate thai_cs_as
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$taxdata = ""; 
		if($query->row()){
			foreach($query->result() as $row){
				$taxdata .= "
					<tr class='trow' style='height:25px;'>
						<td style='vertical-align:middle;'>".$row->TAXNO."</td>
						<td style='vertical-align:middle;' >".$this->Convertdate(2,$row->TAXDT)."</td>
						<td style='vertical-align:middle;'>".$row->CONTNO."</td>
						<td style='vertical-align:middle;'>".$row->CUSCOD."</td>
						<td style='vertical-align:middle;'>".$row->SNAM.$row->NAME1.'  '.$row->NAME2."</td>
						<td style='vertical-align:middle;text-align:right;'>".number_format($row->TOTAMT,2)."</td>
						<td style='vertical-align:middle;text-align:center;'>".$row->FLAG."</td>
					</tr>
				";	
			}	
		}else{
			$taxdata .= "<tr class='trow' style='height:25px;'><td colspan='7'>ไม่มี</td></tr>";
		}

		$response["taxdata"] = $taxdata;
		
		echo json_encode($response);
	}
	
	function conditiontopdf(){
		$data 	= 	array();
		$data[] = 	urlencode(
						$_REQUEST["INVNO1"].'||'.
						$_REQUEST["CONTNO1"].'||'.
						$_REQUEST["CUSCOD1"].'||'.
						$_REQUEST["FRMINVNO"].'||'.
						$_REQUEST["TOINVNO"].'||'.
						$_REQUEST["printtype"].'||'.
						$_REQUEST["address"]
					);
		echo json_encode($this->generateData($data,"encode"));
	}
	
	function pdf(){
		ini_set("memory_limit","-1");
		ini_set("pcre.backtrack_limit", "100000000");
		$layout = 'A4-L';
		$addP = 'L';
		$layout2 = 'A4';
		$addP2 = '';
		$data 	= array();
		$data[] = $_GET["condpdf"];
		$arrs 	= $this->generateData($data,"decode");
		$arrs[0]= urldecode($arrs[0]);
		$tx 	= explode("||",$arrs[0]);
		$INVNO1		= $tx[0];
		$CONTNO1 	= str_replace(chr(0),'',$tx[1]);
		$CUSCOD1 	= str_replace(chr(0),'',$tx[2]);
		$FRMINVNO 	= str_replace(' ','',$tx[3]);
		$TOINVNO 	= str_replace(' ','',$tx[4]);
		$printtype 	= $tx[5];
		$address 	= $tx[6];
		
		$cond = "";
		
		if($FRMINVNO !== '' && $TOINVNO == ''){
			$cond .= " and t.TAXNO >= '".$FRMINVNO."'";
		}else if($FRMINVNO == '' && $TOINVNO !== ''){
			$cond .= " and t.TAXNO <= '".$TOINVNO."'";
		}else if($FRMINVNO !== '' && $TOINVNO !== ''){
			$cond .= " and t.TAXNO between '".$FRMINVNO."' and '".$TOINVNO."'";
		}
		
		$sql = "IF OBJECT_ID('tempdb..#MAIN') IS NOT NULL DROP TABLE #MAIN
				SELECT *
				INTO #MAIN
				FROM(
					SELECT  
					TAXNO, convert(char,TAXDT,112) as TAXDT, CANCEL, CONTNO, CUSTNAME, CUSADD1, CUSADD2, LOCADDR1, LOCATNM, LOCAT,
					TYPE, MODEL, COLOR, REGNO, STRNO, ENGNO, convert(varchar(2),FPAY)+'-'+convert(varchar(2),LPAY) as PAYDUE, 
					convert(char,DDATE,112) as DDATE, isnull(VATRT,0) as VATRT, isnull(NETAMT,0) as NETAMT, isnull(VATAMT,0) as VATAMT, 
					isnull(TOTAMT,0) as TOTAMT, MEMO1
					FROM(
						SELECT  
						(select FORCODE from {$this->MAuth->getdb('PAYFOR')} where FORDESC = T.DESCP) as RCVNO,  
						(select convert(char,day(FDATE)) as day1 from {$this->MAuth->getdb('ARMAST')} where CONTNO = t.CONTNO) as PAYDAY,   
						(select T_NOPAY from {$this->MAuth->getdb('ARMAST')} where CONTNO = t.CONTNO)AS T_NOPAY,   
						replace((	
							select cast(MEMO1 as varchar(1024)) as MEMO1 from {$this->MAuth->getdb('ARMAST')} where CONTNO = T.CONTNO                   
							union                    
							select cast(MEMO1 as varchar(1024)) as MEMO1 from {$this->MAuth->getdb('ARCRED')} where CONTNO = T.CONTNO                   
							union                    
							select cast(MEMO1 as varchar(1024)) as MEMO1 from {$this->MAuth->getdb('ARFINC')} where CONTNO = T.CONTNO                    
							union                    
							select cast(MEMO1 as varchar(1024)) as MEMO1 from {$this->MAuth->getdb('AR_INVOI')}	where CONTNO = T.CONTNO                   
							union                    
							select cast(MEMO1 as varchar(1024)) as MEMO1 from {$this->MAuth->getdb('ARRESV')} where RESVNO = T.CONTNO                   
							union                    
							select cast(MEMO1 as varchar(1024)) as MEMO1 from {$this->MAuth->getdb('AROTHR')} where ARCONT = T.CONTNO  
						),'[explode]',' ')AS MEMO1,  t.LOCAT, T.DESCP, G.GDESC, d.LOCATNM , D.LOCADDR1, D.LOCADDR2,   
						t.CUSCOD, t.TSALE, t.CONTNO, t.VATRT, t.VATAMT, t.NETAMT, t.TOTAMT, t.TAXNO, t.TAXDT, 
						t.FPAY, t.LPAY, case when T.FLAG = 'C' then '**ยกเลิก**' else '' end as CANCEL, p.DDATE,   
						I.TYPE,  I.MODEL, I.BAAB, I.COLOR, I.STRNO, I.ENGNO , I.CC, I.REGNO,  R.GAREXP, R.GAR3EXP, 
						R.REGEXP, (case when (T.LPAY = (select t_nopay from {$this->MAuth->getdb('ARMAST')} where CONTNO = t.CONTNO)) and 
						(T.TAXTYP IN ('D','B')) then '**งวดสุดท้าย**' else '' end) as LASTNOPAY,  
						RTRIM(C.SNAM) +' '+ RTRIM(C.NAME1) +' '+ RTRIM(C.NAME2) as CUSTNAME, 
						isnull(ADDR1,'-')+' ถ.'+isnull(ADDR2,'-')+' ต.'+isnull(TUMB,'-') as CUSADD1,
						' อ.'+isnull(AUMPDES,'-')+' จ.'+isnull(PROVDES,'-')+' '+isnull(ZIP,'') as CUSADD2
						FROM {$this->MAuth->getdb('TAXTRAN')} t  
						LEFT OUTER JOIN {$this->MAuth->getdb('CUSTMAST')} C ON C.CUSCOD = T.CUSCOD  
						LEFT OUTER JOIN {$this->MAuth->getdb('INVLOCAT')} D ON t.LOCAT = D.LOCATCD   
						LEFT OUTER JOIN {$this->MAuth->getdb('ARPAY')} P ON(t.FPAY = p.NOPAY) and (t.CONTNO = p.CONTNO)   
						LEFT OUTER JOIN {$this->MAuth->getdb('INVTRAN')} I ON t.STRNO = I.STRNO  
						LEFT OUTER JOIN {$this->MAuth->getdb('SETGROUP')} G ON I.GCODE = G.GCODE  
						LEFT OUTER JOIN {$this->MAuth->getdb('REGTAB')} R ON I.STRNO = R.STRNO  
						LEFT OUTER JOIN {$this->MAuth->getdb('CUSTADDR')} A ON t.CUSCOD = A.CUSCOD and A.ADDRNO = 1
						LEFT OUTER JOIN {$this->MAuth->getdb('SETAUMP')} S on A.AUMPCOD = S.AUMPCOD 
						LEFT OUTER JOIN {$this->MAuth->getdb('SETPROV')} V on S.PROVCOD = V.PROVCOD
						where 	t.TAXNO like '%".$INVNO1."%' collate thai_cs_as
								and t.CONTNO like '%".$CONTNO1."%' collate thai_cs_as
								and t.CUSCOD like '%".$CUSCOD1."%' collate thai_cs_as
								".$cond."
					)A
				)MAIN	
		";//echo $sql; 
		$query = $this->db->query($sql);
		
		$sql = " 
				select * from #MAIN order by TAXNO
		";//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$sql2 = " 	select 'รวมทั้งสิ้น '+convert(nvarchar(6),count(TAXNO))+' รายการ' as Total,  
							sum(case when CANCEL like '%ยกเลิก%' then 0 else NETAMT end) as sumNETAMT,
							sum(case when CANCEL like '%ยกเลิก%' then 0 else VATAMT end) as sumVATAMT,
							sum(case when CANCEL like '%ยกเลิก%' then 0 else TOTAMT end) as sumTOTAMT
					from #MAIN 
		";//echo $sql; exit;
		$query2 = $this->db->query($sql2);
		
		if($printtype == 'form'){
			$mpdf = new \Mpdf\Mpdf([
				'mode' => 'utf-8', 
				'format' => $layout,
				'margin_top' => 16, 	//default = 16
				'margin_left' => 15, 	//default = 15
				'margin_right' => 15, 	//default = 15
				'margin_bottom' => 16, 	//default = 16
				'margin_header' => 9, 	//default = 9
				'margin_footer' => 9, 	//default = 9
			]);
			
			$stylesheet = "
				<style>
					body { font-family: garuda;font-size:10pt; }
					.wm { width:100%;}
					.w1 { width:15%;height:30px;font-size:10pt;vertical-align:top;}
					.w2 { width:25%;height:30px;font-size:10pt;vertical-align:top;}
					.pd { padding:5px;}
					
				</style>
			";
			
			if($query->row()){
				foreach($query->result() as $row){	
				
					$content = "
						<table width='100%' cellspacing='0'>
							<tr class='wm'> 
								<td class='w1 pd'></td>
								<td class='w2 pd'></td>
								<td class='w1 pd'></td>
								<td class='w1 pd'></td>
								<td class='w1 pd'><b>เลขที่ใบกำกับภาษี</b></td>
								<td class='w1 pd' align='right'>".$row->TAXNO."</td>
							</tr>
							<tr class='wm'>
								<td class='w1 pd'></td>
								<td class='w2 pd'></td>
								<td class='w1 pd'></td>
								<td class='w1 pd'></td>
								<td class='w1 pd'><b>วันที่ใบกำกับภาษี</b></td>
								<td class='w1 pd' align='right'>".$this->Convertdate(2,$row->TAXDT)."</td>
							</tr>
							<tr class='wm'>
								<td class='w1 pd' colspan='6' align='center'>".$row->CANCEL."</td>
							</tr>
							<tr class='wm'>
								<td class='w1 pd'><b>ได้จาก</b></td>
								<td class='w2 pd' colspan='3'>".$row->CUSTNAME."</td>
								<td class='w1 pd'><b>เลขที่สัญญา</b></td>
								<td class='w1 pd' align='right'>".$row->CONTNO."</td>
							</tr>
							<tr class='wm'>
								<td class='w1 pd'></td>
								<td class='w2 pd' colspan='3'>".$row->CUSADD1."</td>
								<td class='w1 pd'></td>
								<td class='w1 pd'></td>
							</tr>
							<tr class='wm'>
								<td class='w1 pd'></td>
								<td class='w2 pd' colspan='3'>".$row->CUSADD2."</td>
								<td class='w1 pd'></td>
								<td class='w1 pd'></td>
							</tr>
							<tr class='wm'>
								<td class='w1 pd'><b>ออกโดย</b></td>
								<td class='w2 pd'>".$row->LOCATNM."</td>
								<td class='w1 pd'></td>
								<td class='w1 pd'></td>
								<td class='w1 pd'></td>
								<td class='w1 pd'></td>
							</tr>
							<tr class='wm'>
								<td class='w1 pd'></td>
								<td class='w2 pd'></td>
								<td class='w1 pd'></td>
								<td class='w1 pd'></td>
								<td class='w1 pd'></td>
								<td class='w1 pd'></td>
							</tr>
							<tr class='wm'>
								<td class='w1 pd'><b>ยี่ห้อ</b></td>
								<td class='w2 pd'>".$row->TYPE."</td>
								<td class='w1 pd'><b>รุ่น</b></td>
								<td class='w1 pd'>".$row->MODEL."</td>
								<td class='w1 pd'><b>มูลค่าสินค้า</b></td>
								<td class='w1 pd' align='right'>".number_format($row->NETAMT,2)."</td>
							</tr>
							<tr class='wm'>
								<td class='w1 pd'><b>สี</b></td>
								<td class='w2 pd'>".$row->COLOR."</td>
								<td class='w1 pd'><b>ทะเบียน</b></td>
								<td class='w1 pd'>".$row->REGNO."</td>
								<td class='w1 pd'></td>
								<td class='w1 pd'></td>
							</tr>
							<tr class='wm'>
								<td class='w1 pd'><b>เลขถัง</b></td>
								<td class='w2 pd'>".$row->STRNO."</td>
								<td class='w1 pd'><b>เลขเครื่องยนต์</b></td>
								<td class='w1 pd'>".$row->ENGNO."</td>
								<td class='w1 pd'><b>ภาษีมูลค่าเพิ่ม (".number_format($row->VATRT,2)."%)</b></td>
								<td class='w1 pd' align='right'>".number_format($row->VATAMT,2)."</td>
							</tr>
							<tr class='wm'>
								<td class='w1 pd'><b>ชำระงวดที่</b></td>
								<td class='w2 pd'>".$row->PAYDUE."</td>
								<td class='w1 pd'><b>วันครบกำหนด</b></td>
								<td class='w1 pd'>".$row->DDATE."</td>
								<td class='w1 pd'></td>
								<td class='w1 pd'></td>
							</tr>
							<tr class='wm'>
								<td class='w1 pd'><b>หมายเหตุ</b></td>
								<td class='w2 pd' colspan='5'>".$row->MEMO1."</td>
							</tr>
							<tr class='wm'>
								<td class='w1 pd'></td>
								<td class='w2 pd'></td>
								<td class='w1 pd'></td>
								<td class='w1 pd'></td>
								<td class='w1 pd'></td>
								<td class='w1 pd'></td>
							</tr>
							<tr class='wm'>
								<td class='w1 pd'><b>จำนวนเงิน</b></td>
								<td class='w2 pd' colspan='3'>(".($row->TOTAMT == '0.00' ? 'ศูนย์บาทถ้วน' : $this->Convertnumber($row->TOTAMT)).")</td>
								<td class='w1 pd'><b>รวมจำนวนเงิน</b></td>
								<td class='w1 pd' align='right'><u>".number_format($row->TOTAMT,2)."</u></td>
							</tr>
						</table>
					";

					$mpdf->AddPage($addP);
					$mpdf->SetHTMLFooter("<div class='wm' style='top:1060;font-size:7pt;text-align:right;'>ออกเอกสาร ณ วันที่ ".date('d/m/').(date('Y')+543)." ".date('H:i')."</div>");
					$mpdf->WriteHTML($content.$stylesheet);
					$mpdf->fontdata['qanela'] = array('R' => "QanelasSoft-Regular.ttf",'B' => "QanelasSoft-Bold.ttf",); //แก้ปริ้นแล้วอ่านไม่ออก
				}
			}
		}else if($printtype == 'report'){
			$html = "";
			$head = "
					<tr>
						<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>รหัสสาขา</th>
						<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>เลขที่ใบกำกับ</th>
						<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>วันที่ใบกำกับ</th>
						<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>เลขที่สัญญา</th>
						<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>ชื่อ - สกุล</th>
						<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>อัตราภาษี<br>(%)</th>
						<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>มูลค่าก่อนภาษี<br>(บาท)</th>
						<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>มูลค่าภาษี<br>(บาท)</th>
						<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:right;'>มูลค่ารวมภาษี<br>(บาท)</th>
						<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:center;'></th>
					</tr>
			";
			
			$No = 1;
			if($query->row()){
				foreach($query->result() as $row){	
					$html .= "
						<tr class='trow' seq=".$No.">
							<td style='width:55px;'>".$row->LOCAT."</td>
							<td style='width:80px;'>".$row->TAXNO."</td>
							<td style='width:70px;'>".$this->Convertdate(2,$row->TAXDT)."</td>
							<td style='width:80px;'>".$row->CONTNO."</td>
							<td style='width:150px;'>".$row->CUSTNAME."</td>
							<td style='width:60px;' align='right'>".number_format($row->VATRT)."</td>
							<td style='width:90px;' align='right'>".number_format($row->NETAMT,2)."</td>
							<td style='width:80px;' align='right'>".number_format($row->VATAMT,2)."</td>
							<td style='width:90px;' align='right'>".number_format($row->TOTAMT,2)."</td>
							<td style='width:80px;' align='center'>".$row->CANCEL."</td>
						</tr>
					";	
				}
			}else{
				$html .= "<tr class='trow'><td colspan='10'>ไม่มี</td></tr>";
			}
			
			if($query2->row()){
				foreach($query2->result() as $row){	
					$html .= "
						<tr class='trow bor' style='background-color:#ebebeb;'>
							<th colspan='6' style='text-align:left;vertical-align:middle;'>".$row->Total."</th>
							<th style='text-align:right;vertical-align:middle;'>".number_format($row->sumNETAMT,2)."</th>
							<th style='text-align:right;vertical-align:middle;'>".number_format($row->sumVATAMT,2)."</th>
							<th style='text-align:right;vertical-align:middle;'>".number_format($row->sumTOTAMT,2)."</th>
							<th style='text-align:right;vertical-align:middle;'></th>
						</tr>
						
					";	
				}
			}
			
			$mpdf = new \Mpdf\Mpdf([
				'mode' => 'utf-8', 
				'format' => $layout2,
				'margin_top' => 10, 	//default = 16
				'margin_left' => 8, 	//default = 15
				'margin_right' => 8, 	//default = 15
				'margin_bottom' => 10, 	//default = 16
				'margin_header' => 9, 	//default = 9
				'margin_footer' => 9, 	//default = 9
			]);
			
			$content = "
				<table class='wf' style='font-size:8pt;height:700px;width:100%;border-collapse:collapse;line-height:23px;overflow:wrap;vertical-align:text-top;'>
					<tbody>
						<tr>
							<th colspan='10' style='font-size:10pt;'>รายงานใบกำกับตามเลขที่สัญญา</th>
						</tr>
						<tr>
							<td colspan='10' style='font-size:8pt;height:35px;border-bottom:0.1px solid black;text-align:center;'>ออกรายงาน ณ วันที่ ".$this->today('today')."</td>
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
				<div class='wf pf' style='".($layout2 == 'A4' ? 'top:1060;left:600;':'top:715;left:960;')." font-size:6pt;'>".date('d/m/').(date('Y')+543)." ".date('H:i')." หน้า {PAGENO} / {nbpg}</div>
			";
			$mpdf->SetHTMLHeader($head);	
			$mpdf->WriteHTML($content);	
		}
		
		$mpdf->Output();
	}
	
	function Convertnumber($amount_number){
		$amount_number = number_format($amount_number, 2, ".","");
		$pt = strpos($amount_number , ".");
		$number = $fraction = "";
		if($pt === false){ 
			$number = $amount_number;
		}else{
			$number = substr($amount_number, 0, $pt);
			$fraction = substr($amount_number, $pt + 1);
		}

		$ret = "";
		$baht = $this->ReadNumber($number);
		if($baht != ""){
			$ret .= $baht . "บาท";
		}
		$satang = $this->ReadNumber($fraction);
		if($satang != ""){
			$ret .=  $satang . "สตางค์";
		}else{ 
			$ret .= "ถ้วน";
		}
		return $ret;
	}

	function ReadNumber($number){
		$position_call = array("แสน", "หมื่น", "พัน", "ร้อย", "สิบ", "");
		$number_call = array("", "หนึ่ง", "สอง", "สาม", "สี่", "ห้า", "หก", "เจ็ด", "แปด", "เก้า");
		$number = $number + 0;
		$ret = "";
		if($number == 0){
			return $ret;
		}
		if($number > 1000000){
			$ret .= ReadNumber(intval($number / 1000000)) . "ล้าน";
			$number = intval(fmod($number, 1000000));
		}
		$divider = 100000;
		$pos = 0;
		while($number > 0){
			$d = intval($number / $divider);
			$ret .= (($divider == 10) && ($d == 2)) ? "ยี่" : 
			((($divider == 10) && ($d == 1)) ? "" :
			((($divider == 1) && ($d == 1) && ($ret != "")) ? "เอ็ด" : $number_call[$d]));
			$ret .= ($d ? $position_call[$pos] : "");
			$number = $number % $divider;
			$divider = $divider / 10;
			$pos++;
		}
		return $ret;
	}
}