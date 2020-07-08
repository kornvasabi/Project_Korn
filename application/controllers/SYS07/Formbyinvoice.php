<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//BEE+
class Formbyinvoice extends MY_Controller {
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
					<div class='row' style='height:93%;'>
						<br>
						<div class='col-sm-8 col-xs-8 col-sm-offset-2'><b>ประเภทใบกำกับ</b></div>
						<div class='col-sm-8 col-xs-8 col-sm-offset-2' style='border:0.1px solid #f6fefa;background-color:#d5f2fb;'>
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group'>
									<br>
									<input type= 'radio' id='PAY1' name='invotype'> ค่างวด, ตัดสด
									<br>
									<input type= 'radio' id='PAY2' name='invotype'> การขาย, เงินดาวน์
								</div>
							</div>
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group'>
									<br>
									<input type= 'radio' id='PAY3' name='invotype'> เงินจอง
									<br>
									<input type= 'radio' id='PAY4' name='invotype' checked> ทั้งหมด
								</div>
							</div>
						</div>
						<div class='col-sm-8 col-xs-8 col-sm-offset-2' style='height:40px;'></div>
						<div class='col-sm-8 col-xs-8 col-sm-offset-2' style='border:0.1px solid #f6fefa;background-color:#dafbeb;'>
							<br>
							<div class='col-sm-12 col-xs-12'>
								<div class='col-sm-6 col-xs-6'>	
									<div class='form-group'>
										สาขา
										<select id='LOCAT1' class='form-control input-sm' data-placeholder='สาขา'></select>
									</div>
								</div>
							</div>
							<div class='col-sm-12 col-xs-12'>	
								<div class='col-sm-6 col-xs-6'>	
									<div class='form-group'>
										จากวันที่ใบกำกับ
										<input type='text' id='FRMDATE' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' placeholder='จากวันที่ใบกำกับ' value='".$this->today('today')."' style='font-size:10.5pt'>
									</div>
								</div>
								<div class='col-sm-6 col-xs-6'>	
									<div class='form-group'>
										ถึงวันที่ใบกำกับ
										<input type='text' id='TODATE' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' placeholder='ถึงวันที่ใบกำกับ' value='".$this->today('today')."' style='font-size:10.5pt'>
									</div>
								</div>
								<div class='col-sm-6 col-xs-6'>	
									<div class='form-group' >
										จากเลขที่ใบกำกับ
										<select id='FRMINVNO' class='form-control input-sm' data-placeholder='เลขที่ใบกำกับ'></select>
										<br><br>
									</div>
								</div>
								<div class='col-sm-6 col-xs-6'>	
									<div class='form-group'>
										ถึงเลขที่สัญญา
										<select id='TOINVNO' class='form-control input-sm' data-placeholder='เลขที่ใบกำกับ'></select>
										<br><br>
									</div>
								</div>
							</div>	
						</div>
						<div class='col-sm-8 col-xs-8 col-sm-offset-2' style='height:40px;top:20px;'><b>ที่อยู่</b></div>
						<div class='col-sm-8 col-xs-8 col-sm-offset-2' style='border:0.1px solid #f6fefa;background-color:#d5f2fb;'>
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
					<div class='row' style='height:7%;'>
						<div class='col-sm-8 col-xs-8 col-sm-offset-2'>
							<button id='btnt1search' class='btn btn-primary btn-sm' style='width:100%;font-size:10.5pt;'><span class='glyphicon glyphicon-print'> พิมพ์ใบกำกับ</span></button>
						</div>
					</div>
				</div>
			</div>
		";

		$html.= "<script src='".base_url('public/js/SYS07/Formbyinvoice.js')."'></script>";
		echo $html;
	}
	
	function conditiontopdf(){
		$data 	= 	array();
		$data[] = 	urlencode(
						$_REQUEST["LOCAT1"].'||'.
						$_REQUEST["FRMINVNO"].'||'.
						$_REQUEST["TOINVNO"].'||'.
						$_REQUEST["FRMDATE"].'||'.
						$_REQUEST["TODATE"].'||'.
						$_REQUEST["paytype"].'||'.
						$_REQUEST["address"]
					);
		echo json_encode($this->generateData($data,"encode"));
	}
	
	function pdf(){
		ini_set("memory_limit","-1");
		ini_set("pcre.backtrack_limit", "100000000");
		$layout = 'A4-L';
		$addP = 'L';
		$data 	= array();
		$data[] = $_GET["condpdf"];
		$arrs 	= $this->generateData($data,"decode");
		$arrs[0]= urldecode($arrs[0]);
		$tx 	= explode("||",$arrs[0]);
		$LOCAT1		= $tx[0];
		$FRMINVNO 	= str_replace(chr(0),'',$tx[1]);
		$TOINVNO 	= str_replace(chr(0),'',$tx[2]);
		$FRMDATE 	= $this->Convertdate(1,$tx[3]);
		$TODATE 	= $this->Convertdate(1,$tx[4]);
		$paytype 	= $tx[5];
		$address 	= $tx[6];
		
		$cond = "";
		
		if($LOCAT1 != ""){
			$cond .= " and (t.LOCAT like '".$LOCAT1."%')";
		}
		
		if($paytype == 'PAY1'){
			$cond .= " and t.TSALE = 'H' and (t.TAXTYP = 'B' or t.TAXTYP = 'D')";
		}else if($paytype == 'PAY2'){
			$cond .= " and (t.TAXTYP = 'S' or t.TAXTYP = 'O')";
		}else if($paytype == 'PAY3'){
			$cond .= " and (t.TAXTYP = 'O' or t.TAXTYP = 'O')";
		}else{
			$cond .= " and (t.TAXTYP ='B' or t.TAXTYP='D' or t.TAXTYP='S' or t.TAXTYP='O' or t.TAXTYP='R')";
		}
		
		if($FRMINVNO !== '' && $TOINVNO == ''){
			$cond .= " and T.taxno >= '".$FRMINVNO."'";
		}else if($FRMINVNO == '' && $TOINVNO !== ''){
			$cond .= " and T.taxno <= '".$TOINVNO."'";
		}else if($FRMINVNO !== '' && $TOINVNO !== ''){
			$cond .= " and t.TAXNO between '".$FRMINVNO."' and '".$TOINVNO."'";
		}
		
		$sql = "
				SELECT  
				TAXNO, convert(char,TAXDT,112) as TAXDT, CANCEL, CONTNO, CUSTNAME, CUSADD1, CUSADD2, LOCADDR1, LOCATNM, 
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
					),'[explode]',' ')AS MEMO1,  T.DESCP, G.GDESC, d.LOCATNM , D.LOCADDR1, D.LOCADDR2,   
					t.CUSCOD, t.TSALE, t.CONTNO, t.VATRT, t.VATAMT, t.NETAMT, t.TOTAMT, t.TAXNO, t.TAXDT, 
					t.FPAY, t.LPAY, case when T.FLAG = 'C' then '**ยกเลิก**' else '' end as CANCEL, p.DDATE,   
					I.TYPE,  I.MODEL, I.BAAB, I.COLOR, I.STRNO, I.ENGNO , I.CC, I.REGNO,  R.GAREXP, R.GAR3EXP, 
					R.REGEXP, (case when (T.LPAY = (select t_nopay from armast where CONTNO = t.CONTNO)) and 
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
					where (t.TAXTYP ='B' or t.TAXTYP='D' or t.TAXTYP='S' or t.TAXTYP='O' or t.TAXTYP='R') 
					and t.TAXDT between '".$FRMDATE."' and '".$TODATE."' ".$cond."
				)A
				order by TAXNO
		";//echo $sql; exit;
		$query = $this->db->query($sql);
		
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