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
class ReportSell extends MY_Controller {
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
	
	function aud(){
		$claim = $this->MLogin->getclaim(uri_string());
		//print_r($claim); exit;
		if($claim['m_access'] != "T"){ echo "<div align='center' style='color:red;font-size:16pt;width:100%;'>ขออภัย คุณยังไม่มีสิทธิเข้าใช้งานหน้านี้ครับ</div>"; exit; }
		$this->_interface($claim,"AUD");
	}
	
	function acc(){
		$claim = $this->MLogin->getclaim(uri_string());
		//print_r($claim); exit;
		if($claim['m_access'] != "T"){ echo "<div align='center' style='color:red;font-size:16pt;width:100%;'>ขออภัย คุณยังไม่มีสิทธิเข้าใช้งานหน้านี้ครับ</div>"; exit; }
		$this->_interface($claim,"ACC");
	}
	
	function _interface($claim,$btn){
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
								วันที่ขาย จาก
								<input type='text' id='sSDATE' class='form-control input-sm' placeholder='ถึง' data-provide='datepicker' data-date-language='th-th' value='".$this->today('startofmonthB1')."'>
							</div>
						</div>	
						<div class='col-sm-2'>	
							<div class='form-group'>
								วันที่ขาย ถึง
								<input type='text' id='eSDATE' class='form-control input-sm' placeholder='ถึง' data-provide='datepicker' data-date-language='th-th' value='".$this->today('endofmonth')."'>
							</div>
						</div>	
						<div class='col-sm-2'>	
							<div class='form-group'>
								พนักงานขาย
								<select id='SALCOD' class='form-control input-sm chosen-select' data-placeholder='พนักงานขาย'></select>
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								ประเภทการชำระ
								<select id='PAYTYP' class='form-control input-sm chosen-select' data-placeholder='ประเภทการชำระ'></select>
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								กิจกรรมการขาย
								<select id='ACTICOD' class='form-control input-sm chosen-select' data-placeholder='กิจกรรมการขาย'></select>
							</div>
						</div>						
						<div class='col-sm-2'>	
							<div class='form-group'>
								กลุ่มรถ
								<select id='GCODE' class='form-control input-sm chosen-select' data-placeholder='กลุ่มรถ'></select>
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
								แบบ
								<select id='BAAB' class='form-control input-sm chosen-select' data-placeholder='แบบ'></select>
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								สี
								<select id='COLOR' class='form-control input-sm chosen-select' data-placeholder='สี'></select>
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
						<div class='col-sm-2'>	
							<div class='form-group'>
								กลุ่มลูกหนี้
								<select id='GROUPCUS' class='form-control input-sm chosen-select' data-placeholder='กลุ่มลูกหนี้'></select>
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								อำเภอ
								<select id='AUMPCOD' class='form-control input-sm chosen-select' data-placeholder='อำเภอ'></select>
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								จังหวัด
								<select id='PROVCOD' class='form-control input-sm chosen-select' data-placeholder='จังหวัด'></select>
							</div>
						</div>						
					</div>
					<div class='row'>
						<div class='col-sm-2 col-sm-offset-3'>
							<b>รายงาน</b>
							<div class='radio'><label><input type='radio' class='sort' name='REPORT' value='1' checked=''>รายงานการขายสด</label></div>
							<div class='radio'><label><input type='radio' class='sort' name='REPORT' value='2'>รายงานการขายผ่อน</label></div>
							<div class='radio'><label><input type='radio' class='sort' name='REPORT' value='3'>รายงานการขายไฟแนนท์</label></div>
							<div class='radio'><label><input type='radio' class='sort' name='REPORT' value='4'>รายงานการขายส่งเอเย่นต์</label></div>
						</div>
						<div class='col-sm-2'>
							<b>รูปแบบรายงาน</b>
							<div class='radio'><label><input type='radio' class='sort' name='RPT' value='1' checked=''>แบบเต็ม</label></div>
							".($btn == "AUD" ? "<div class='radio'><label><input type='radio' class='sort' name='RPT' value='2' checked=''>แบบย่อ</label></div>":"")."
						</div>
						<div class='col-sm-2'>
							<b>เรียงลำดับข้อมูล</b>
							<div class='radio'><label><input type='radio' class='sort' name='SORT' value='1'>วันที่ขาย</label></div>
							<div class='radio'><label><input type='radio' class='sort' name='SORT' value='2'>สาขา</label></div>
							<div class='radio'><label><input type='radio' class='sort' name='SORT' value='3'>พนักงานขาย</label></div>
							<div class='radio'><label><input type='radio' class='sort' name='SORT' value='4' checked=''>เลขที่สัญญา</label></div>
						</div>
					</div>
					<div class='row'>
						<div class='col-sm-12'>
							<div class='form-group'>
								<button id='btnt1search{$btn}' class='btn btn-primary btn-block'>
									<span class='glyphicon glyphicon-search'> แสดง</span>
								</button>
							</div>
						</div>
					</div>
				</div>
				<div id='result'></div>
			</div>
		";
		
		$html.= "<script src='".base_url('public/js/SYS04/ReportSell.js')."'></script>";
		echo $html;
	}
	
	function search(){
		$arrs = array();
		$arrs['locat']	  = $_POST['locat'];
		$arrs['sSDATE']   = $this->Convertdate(1,$_POST['sSDATE']);
		$arrs['eSDATE']   = $this->Convertdate(1,$_POST['eSDATE']);
		$arrs['SALCOD']	  = $_POST['SALCOD'];
		$arrs['PAYTYP']   = $_POST['PAYTYP'];
		$arrs['ACTICOD']  = $_POST['ACTICOD'];
		$arrs['GCODE'] 	  = $_POST['GCODE'];
		$arrs['MODEL'] 	  = $_POST['MODEL'];
		$arrs['BAAB'] 	  = $_POST['BAAB'];
		$arrs['COLOR'] 	  = $_POST['COLOR'];
		$arrs['STAT'] 	  = $_POST['STAT'];
		$arrs['GROUPCUS'] = $_POST['GROUPCUS'];
		$arrs['AUMPCOD']  = $_POST['AUMPCOD'];
		$arrs['PROVCOD']  = $_POST['PROVCOD'];
		$arrs['REPORT']   = $_POST['REPORT'];
		$arrs['RPT'] 	  = $_POST['RPT'];
		$arrs['SORT'] 	  = $_POST['SORT'];
		$arrs['action']   = $_POST['action'];
		
		if($arrs['action'] == "AUD" && $arrs['REPORT'] == 1){ $this->getAUDSELL($arrs); }
		else if($arrs['action'] == "AUD" && $arrs['REPORT'] == 2){ $this->getAUDLEASING($arrs); }
		else if($arrs['action'] == "AUD" && $arrs['REPORT'] == 3){ $this->getAUDFINANCE($arrs); }
		else if($arrs['action'] == "AUD" && $arrs['REPORT'] == 4){ $this->getAUDAGENT($arrs); }
		
		else if($arrs['action'] == "ACC" && $arrs['REPORT'] == 1){ $this->getACCSELL($arrs); }
		else if($arrs['action'] == "ACC" && $arrs['REPORT'] == 2){ $this->getACCLEASING($arrs); }
		else if($arrs['action'] == "ACC" && $arrs['REPORT'] == 3){ $this->getACCFINANCE($arrs); }
		else if($arrs['action'] == "ACC" && $arrs['REPORT'] == 4){ $this->getACCAGENT($arrs); }
	}
	
	function getAUDSELL($arrs){
		$cond = "";
		$condDesc = "";
		$reportName = "";
		if($arrs['REPORT'] == 1){
			$reportName = " รายงานการขายสดเพื่อแผนกตรวจสอบ";
		}else if($arrs['REPORT'] == 2){
			$cond .= " and isnull(A.STRNO,'') = ''";
			$reportName = " รายงานการขายผ่อนเพื่อแผนกตรวจสอบ";
		}else if($arrs['REPORT'] == 3){
			$cond .= " and A.SDATE is null";
			$reportName = " รายงานการขายไฟแนนท์เพื่อแผนกตรวจสอบ";
		}else if($arrs['REPORT'] == 4){
			$cond .= " and A.SDATE is not null";
			$reportName = " รายงานการขายส่งเอเย่นต์เพื่อแผนกตรวจสอบ";
		}
		
		if($arrs['locat'] != ""){
			$cond .= " and A.LOCAT like '".$arrs['locat']."%'";
			$condDesc .= "สาขา ".$arrs['locat'];
		}
		
		if($arrs['sSDATE'] != "" and $arrs['eSDATE'] != ""){
			$cond .= " and convert(varchar(8),A.SDATE,112) between '".$arrs['sSDATE']."' and '".$arrs['eSDATE']."' ";
			$condDesc .= " วันที่ทำสัญญา จากวันที่  ".$_POST['sSDATE']." - ".$_POST['eSDATE'];
		}else if($arrs['sSDATE'] != "" and $arrs['eSDATE'] == ""){
			$cond .= " and convert(varchar(8),A.SDATE,112) = '".$arrs['sSDATE']."'";
			$condDesc .= " วันที่ทำสัญญา  ".$_POST['sSDATE'];
		}else if($arrs['sSDATE'] == "" and $arrs['eSDATE'] != ""){
			$cond .= " and convert(varchar(8),A.SDATE,112) = '".$arrs['eSDATE']."'";
			$condDesc .= " วันที่ทำสัญญา  ".$_POST['eSDATE'];
		}
		
		if($arrs['MODEL'] != ""){
			$cond .= " and I.MODEL like '".$arrs['MODEL']."%'";
			$condDesc .= " รุ่น  ".$_POST['MODEL'];
		}
		
		if($arrs['BAAB'] != ""){
			$cond .= " and I.BAAB like '".$arrs['BAAB']."%'";
			$condDesc .= " แบบ ".$_POST['BAAB'];
		}
		
		if($arrs['COLOR'] != ""){
			$cond .= " and I.COLOR like '".$arrs['COLOR']."%'";
			$condDesc .= " สี ".$_POST['COLOR'];
		}
		
		if($arrs['STAT'] != "A"){
			$cond .= " and I.STAT like '".$arrs['STAT']."%'";
			$condDesc .= " สถานะรถ ".$_POST['STAT'];
		}
		
		if($arrs['GROUPCUS'] != ""){
			$cond .= " and C.GROUP1 like '".$arrs['GROUPCUS']."%'";
			$condDesc .= " กลุ่มลูกหนี้ ".$_POST['GROUPCUS'];
		}
		
		if($arrs['GCODE'] != ""){
			$cond .= " and G.GCODE like '".$arrs['GCODE']."%'";
			$condDesc .= " กลุ่มรถ ".$_POST['GCODE'];
		}
		
		if($arrs['SALCOD'] != ""){
			$cond .= " and A.SALCOD like '".$arrs['SALCOD']."'";
			$condDesc .= " พนักงานขาย ".$_POST['SALCOD'];
		}
		
		if($arrs['AUMPCOD'] != ""){
			$cond .= " and D.AUMPCOD like '".$arrs['AUMPCOD']."'";
			$condDesc .= " อำเภอ ".$_POST['AUMPCOD'];
		}
		
		if($arrs['PROVCOD'] != ""){
			$cond .= " and D.PROVCOD like '".$arrs['PROVCOD']."'";
			$condDesc .= " อำเภอ ".$_POST['PROVCOD'];
		}
		
		if($arrs['PAYTYP'] != ""){
			$cond .= " and A.PAYTYP like '".$arrs['PAYTYP']."'";
			$condDesc .= " ประเภทการชำระ ".$_POST['PAYTYP'];
		}
		
		$sort = "";
		if($arrs['SORT'] == 1){ $sort = "order by SDATE"; }
		else if($arrs['SORT'] == 2){ $sort = "order by LOCAT"; }
		else if($arrs['SORT'] == 3){ $sort = "order by SALCOD"; }
		else if($arrs['SORT'] == 4){ $sort = "order by CONTNO"; }
		
		$sql = "select COMP_NM from {$this->MAuth->getdb('CONDPAY')}";
		$query = $this->db->query($sql);
		$company = "";
		if($query->row()){
			foreach($query->result() as $row){
				$company = $row->COMP_NM;
			}
		}
		
		$sql = "
			SELECT A.TSALE,A.LOCAT,A.CONTNO,A.CUSCOD,A.RESVNO,A.CRDAMT, A.STRNO,I.COLOR
				,convert(varchar(8),A.SDATE,112) as SDATE,A.NPRICE,A.VATPRC,A.TOTPRC,A.NPAYRES
				, A.TKEYIN,A.TKEYIN-A.TOTPRES-A.CRDAMT AS TKANG,A.VATPRES,A.TOTPRES,A.OPTPTOT,A.OPTCST,A.OPTCVT,A.TAXDT,A.SALCOD
				,A.COMITN,A.VCARCST,A.NCARCST,isnull(A.TAXNO,'-') as TAXNO,I.STAT,C.SNAM+C.NAME1+' '+C.NAME2 as CUSNAME
				,G.GDESC,A.PAYTYP, A.RECOMCOD,isnull((
					select aa.SNAM+aa.NAME1+' '+aa.NAME2 as CUSNAME 
					from {$this->MAuth->getdb('CUSTMAST')} aa 
					where aa.CUSCOD=A.RECOMCOD
				),'-') as RECOMNAM,A.ACTICOD,A.COMEXT,A.COMOPT,A.COMOTH 
			FROM {$this->MAuth->getdb('ARCRED')} A  
			LEFT OUTER JOIN {$this->MAuth->getdb('CUSTMAST')} C ON C.CUSCOD = A.CUSCOD 
			LEFT OUTER JOIN {$this->MAuth->getdb('CUSTADDR')} D ON D.CUSCOD=C.CUSCOD AND (C.ADDRNO = D.ADDRNO) 
			LEFT OUTER JOIN {$this->MAuth->getdb('INVTRAN')} I ON A.STRNO = I.STRNO AND (A.CONTNO=I.CONTNO)
			LEFT OUTER JOIN {$this->MAuth->getdb('SETGROUP')} G ON G.GCODE = I.GCODE 
			WHERE  1=1 {$cond}
				
			union 
			SELECT A.TSALE,A.LOCAT,A.CONTNO,A.CUSCOD,A.RESVNO,A.CRDAMT, A.STRNO,I.COLOR
				,convert(varchar(8),A.SDATE,112) as SDATE,A.NPRICE,A.VATPRC,A.TOTPRC,A.NPAYRES
				,A.TKEYIN,A.TKEYIN-A.TOTPRES-A.CRDAMT AS TKANG,A.VATPRES,A.TOTPRES,A.OPTPTOT,A.OPTCST,A.OPTCVT,A.TAXDT,A.SALCOD
				,A.COMITN,A.VCARCST,A.NCARCST,isnull(A.TAXNO,'-') as TAXNO,I.STAT,C.SNAM+C.NAME1+' '+C.NAME2 as CUSNAME
				,G.GDESC,A.PAYTYP, A.RECOMCOD ,isnull((
					select aa.SNAM+aa.NAME1+' '+aa.NAME2 as CUSNAME 
					from {$this->MAuth->getdb('CUSTMAST')} aa 
					where aa.CUSCOD=A.RECOMCOD
				),'-') as RECOMNAM,A.ACTICOD,A.COMEXT,A.COMOPT,A.COMOTH 
			FROM {$this->MAuth->getdb('HARCRED')} A  
			LEFT OUTER JOIN {$this->MAuth->getdb('CUSTMAST')} C ON C.CUSCOD = A.CUSCOD 
			LEFT OUTER JOIN {$this->MAuth->getdb('CUSTADDR')} D ON D.CUSCOD=C.CUSCOD AND (C.ADDRNO = D.ADDRNO) 
			LEFT OUTER JOIN {$this->MAuth->getdb('HINVTRAN')} I ON A.STRNO = I.STRNO AND (A.CONTNO=I.CONTNO)
			LEFT OUTER JOIN {$this->MAuth->getdb('SETGROUP')} G ON G.GCODE = I.GCODE 
			WHERE  1=1 {$cond}
			{$sort}
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		$NRow = 1;
		$data_sum = array();
		if($query->row()){
			foreach($query->result() as $row){
				if($arrs['RPT'] == 1){
					$ARINOPT = $this->checkINOPT($row->CONTNO);
					$html .= "
						<tr>
							<td>
								".$row->LOCAT."
								<br>&emsp;
								".($ARINOPT["TOPIC"] == "" ? "" : "<br>".$ARINOPT["TOPIC"])."
							</td>
							<td>
								".$row->CONTNO."<br>".$row->RESVNO."
								".($ARINOPT["OPTCODE"] == "" ? "" : "<br>".$ARINOPT["OPTCODE"])."
							</td>
							<td>".$row->CUSCOD."<br>".$row->CUSNAME."
								".($ARINOPT["SIZE"] == "" ? "" : "<br>".$ARINOPT["SIZE"])."
							</td>							
							<td style='mso-number-format:\"\@\";'>".$this->Convertdate(2,$row->SDATE)."
								<br>&emsp;
								".($ARINOPT["QTY"] == "" ? "" : "<br>".$ARINOPT["QTY"])."
							</td>
							<td>".$row->STRNO."<br>".$row->COLOR."
								".($ARINOPT["UM"] == "" ? "" : "<br>".$ARINOPT["UM"])."
							</td>
							
							<td class='text-right'>".($row->STAT == "N"?"รถใหม่":"รถเก่า")."<br>".number_format($row->COMITN,2)."
								".($ARINOPT["TUPRICE"] == "" ? "" : "<br>".$ARINOPT["TUPRICE"])."
							</td>
							<td class='text-right'>".$row->TAXNO."<br>".number_format($row->COMEXT,2)."
								".($ARINOPT["UPRICE"] == "" ? "" : "<br>".$ARINOPT["UPRICE"])."
							</td>
							<td class='text-right'>".$row->SALCOD."<br>".number_format($row->COMOPT,2)."
								".($ARINOPT["UPRICE_UM"] == "" ? "" : "<br>".$ARINOPT["UPRICE_UM"])."
							</td>
							<td class='text-right'>".number_format($row->TOTPRC,2)."<br>".number_format($row->COMOTH,2)."
								".($ARINOPT["TTOTPRC"] == "" ? "" : "<br>".$ARINOPT["TTOTPRC"])."
							</td>
							<td class='text-right'>".number_format($row->TOTPRES,2)."<br>".$row->PAYTYP."
								".($ARINOPT["TOTPRC"] == "" ? "" : "<br>".$ARINOPT["TOTPRC"])."
							</td>
							<td class='text-right'>".number_format($row->CRDAMT,2)."<br>".$row->ACTICOD."
								".($ARINOPT["TOTPRC_UM"] == "" ? "" : "<br>".$ARINOPT["TOTPRC_UM"])."
							</td>
							<td class='text-right'>".number_format($row->TKANG,2)."<br>".$row->RECOMNAM."</td>
						</tr>
					";
				}else{
					$html .= "
						<tr>
							<td>".$NRow."</td>
							<td>".$row->LOCAT."</td>
							<td>".$row->CONTNO."</td>
							<td>".$row->CUSNAME."</td>
							<td style='mso-number-format:\"\@\";'>".$this->Convertdate(2,$row->SDATE)."</td>
							<td class='text-right'>".number_format($row->TOTPRC,2)."</td>
							<td>".$row->SALCOD."</td>
							<td>".$row->ACTICOD."</td>
							<td>".$row->STRNO."</td>
						</tr>
					";
				}
				
				foreach($row as $key => $val){
					if(is_numeric($val)){
						$data_sum[$key] = (isset($data_sum[$key]) ? $data_sum[$key]: 0)+$val;
					}
				}
				
				$NRow++;
			}
		}
		
		$headcs = 0;
		$head 	= "";
		$foot 	= "";
		if($arrs['RPT'] == 1){
			$headcs = 12;
			$head = "
				<tr>
					<th>สาขา</th>
					<th>เลขที่สัญญา<br>เลขที่ใบจอง</th>
					<th>รหัสลูกค้า<br>ชื่อ-สกุลลูกค้า</th>
					<th>วันที่ขาย</th>
					<th>เลขตัวถัง<br>สีรถ</th>
					<th>สถานะภาพรถ<br>คอมมิชชั่น</th>
					<th>เลขที่ใบกำกับ<br>ค่าคอมบุคคลนอก</th>
					<th>พนักงานขาย<br>ค่าของแถม</th>
					<th>ราคาขาย<br>คชจ.อื่นๆ</th>
					<th>เงินจอง<br>ชำระเงิน</th>
					<th>ยอดลดหนี้<br>กิจกรรมการขาย</th>
					<th>ยอดคงเหลือ<br>ผู้แนะนำ</th>
				</tr>
			";
			
			$foot .= "
				<tr>
					<th>รวม</th>
					<th>".--$NRow."</th>
					<th>รายการ</th>	
					<th style='mso-number-format:\"\@\";'></th>
					<th></th>
					
					<th class='text-right'>&emsp;<br>".number_format($data_sum["COMITN"],2)."</th>
					<th class='text-right'>&emsp;<br>".number_format($data_sum["COMEXT"],2)."</th>
					<th class='text-right'>&emsp;<br>".number_format($data_sum["COMOPT"],2)."</th>
					<th class='text-right'>".number_format($data_sum["TOTPRC"],2)."<br>".number_format($data_sum["COMOTH"],2)."</th>
					<th class='text-right'>".number_format($data_sum["TOTPRES"],2)."<br>&emsp;</th>
					<th class='text-right'>".number_format($data_sum["CRDAMT"],2)."<br>&emsp;</th>
					<th class='text-right'>".number_format($data_sum["TKANG"],2)."<br>&emsp;</th>
				</tr>
			";
		}else{
			$headcs = 9;
			$head = "
				<tr>
					<th>ลำดับ</th>
					<th>สาขา</th>
					<th>เลขที่สัญญา</th>
					<th>ชื่อ-สกุลลูกค้า</th>
					<th>วันที่ขาย</th>
					<th>ราคาขาย</th>
					<th>พนักงานขาย</th>
					<th>กิจกรรมการขาย</th>
					<th>เลขตัวถัง</th>
				</tr>
			";
			$foot .= "
				<tr>
					<th>รวม</th>
					<th>".--$NRow."</th>
					<th>รายการ</th>
					<th></th>
					<th style='mso-number-format:\"\@\";'>เป็นเงิน ===></th>
					<th class='text-right'>".number_format($data_sum["TOTPRC"],2)."</th>
					<th></th>
					<th></th>
					<th></th>
				</tr>
			";
		}
		
		$html = "
			<div id='table-fixed-RPSellCar' class='col-sm-12' style='height:calc(100% - 0px);width:100%;overflow:auto;font-size:8pt;'>
				<table id='table-RPSellCar' class='table table-bordered' cellspacing='0' width='calc(100% - 1px)'>
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
						{$head}
					</thead>	
					<tbody>{$html}</tbody>
					<tfoot style='background: rgba(0, 0, 0, 0) url(&#39;../public/lobiadmin-master/version/1.0/ajax/img/bg/bg4.png&#39;) repeat scroll 0% 0%;'>
						{$foot}
					</tfoot>
				</table>
			</div>
		";
		
		$response = array("html"=>$html,"status"=>true);
		echo json_encode($response);
	}
	
	function getAUDLEASING($arrs){
		$cond = "";
		$condDesc = "";
		$reportName = "";
		if($arrs['REPORT'] == 1){
			$reportName = " รายงานการขายสดเพื่อแผนกตรวจสอบ";
		}else if($arrs['REPORT'] == 2){
			$reportName = " รายงานการขายผ่อนเพื่อแผนกตรวจสอบ";
		}else if($arrs['REPORT'] == 3){
			$cond .= " and A.SDATE is null";
			$reportName = " รายงานการขายไฟแนนท์เพื่อแผนกตรวจสอบ";
		}else if($arrs['REPORT'] == 4){
			$cond .= " and A.SDATE is not null";
			$reportName = " รายงานการขายส่งเอเย่นต์เพื่อแผนกตรวจสอบ";
		}
		
		if($arrs['locat'] != ""){
			$cond .= " and A.LOCAT like '".$arrs['locat']."%'";
			$condDesc .= "สาขา ".$arrs['locat'];
		}
		
		if($arrs['sSDATE'] != "" and $arrs['eSDATE'] != ""){
			$cond .= " and convert(varchar(8),A.SDATE,112) between '".$arrs['sSDATE']."' and '".$arrs['eSDATE']."' ";
			$condDesc .= " วันที่ทำสัญญา จากวันที่  ".$_POST['sSDATE']." - ".$_POST['eSDATE'];
		}else if($arrs['sSDATE'] != "" and $arrs['eSDATE'] == ""){
			$cond .= " and convert(varchar(8),A.SDATE,112) = '".$arrs['sSDATE']."'";
			$condDesc .= " วันที่ทำสัญญา  ".$_POST['sSDATE'];
		}else if($arrs['sSDATE'] == "" and $arrs['eSDATE'] != ""){
			$cond .= " and convert(varchar(8),A.SDATE,112) = '".$arrs['eSDATE']."'";
			$condDesc .= " วันที่ทำสัญญา  ".$_POST['eSDATE'];
		}
		
		if($arrs['MODEL'] != ""){
			$cond .= " and I.MODEL like '".$arrs['MODEL']."%'";
			$condDesc .= " รุ่น  ".$_POST['MODEL'];
		}
		
		if($arrs['BAAB'] != ""){
			$cond .= " and I.BAAB like '".$arrs['BAAB']."%'";
			$condDesc .= " แบบ ".$_POST['BAAB'];
		}
		
		if($arrs['COLOR'] != ""){
			$cond .= " and I.COLOR like '".$arrs['COLOR']."%'";
			$condDesc .= " สี ".$_POST['COLOR'];
		}
		
		if($arrs['STAT'] != "A"){
			$cond .= " and I.STAT like '".$arrs['STAT']."%'";
			$condDesc .= " สถานะรถ ".$_POST['STAT'];
		}
		
		if($arrs['GROUPCUS'] != ""){
			$cond .= " and C.GROUP1 like '".$arrs['GROUPCUS']."%'";
			$condDesc .= " กลุ่มลูกหนี้ ".$_POST['GROUPCUS'];
		}
		
		if($arrs['GCODE'] != ""){
			$cond .= " and G.GCODE like '".$arrs['GCODE']."%'";
			$condDesc .= " กลุ่มรถ ".$_POST['GCODE'];
		}
		
		if($arrs['SALCOD'] != ""){
			$cond .= " and A.SALCOD like '".$arrs['SALCOD']."'";
			$condDesc .= " พนักงานขาย ".$_POST['SALCOD'];
		}
		
		if($arrs['AUMPCOD'] != ""){
			$cond .= " and D.AUMPCOD like '".$arrs['AUMPCOD']."'";
			$condDesc .= " อำเภอ ".$_POST['AUMPCOD'];
		}
		
		if($arrs['PROVCOD'] != ""){
			$cond .= " and D.PROVCOD like '".$arrs['PROVCOD']."'";
			$condDesc .= " อำเภอ ".$_POST['PROVCOD'];
		}
		
		if($arrs['PAYTYP'] != ""){
			$cond .= " and A.PAYTYP like '".$arrs['PAYTYP']."'";
			$condDesc .= " ประเภทการชำระ ".$_POST['PAYTYP'];
		}
		
		$sort = "";
		if($arrs['SORT'] == 1){ $sort = "order by SDATE"; }
		else if($arrs['SORT'] == 2){ $sort = "order by A.LOCAT"; }
		else if($arrs['SORT'] == 3){ $sort = "order by A.SALCOD"; }
		else if($arrs['SORT'] == 4){ $sort = "order by CONTNO"; }
		
		$sql = "select COMP_NM from {$this->MAuth->getdb('CONDPAY')}";
		$query = $this->db->query($sql);
		$company = "";
		if($query->row()){
			foreach($query->result() as $row){
				$company = $row->COMP_NM;
			}
		}
		
		$sql = "
			select A.LOCAT,A.TSALE,A.CONTNO,A.CUSCOD,A.RESVNO,A.STRNO
				, convert(varchar(8),A.SDATE,112) as SDATE
				, A.NPRICE,A.VATPRC,A.NPAYRES,A.VATPRES,A.BALANC
				, A.NKANG,A.VKANG,A.NDAWN,A.VATDWN,A.NCSHPRC,A.VCSHPRC
				, convert(varchar(8),A.FDATE,112) as FDATE,A.NPROFIT
				, convert(varchar(8),A.LDATE,112) as LDATE
				, A.N_FUPAY,A.V_FUPAY,A.N_UPAY,A.V_UPAY,A.N_LUPAY,A.V_LUPAY
				, A.TAXNO,convert(varchar(8),A.TAXDT,112) as TAXDT,A.BILLCOLL,A.CHECKER
				, A.DELYRT,A.DLDAY,A.T_NOPAY,A.T_UPAY,A.OPTCST,A.OPTCVT,A.OPTCTOT,A.OPTPRC
				, A.OPTPVT,A.OPTPTOT,A.NCARCST,A.VCARCST,A.SALCOD,A.COMITN,A.FLCANCL
				, A.TOTPRC,A.TCSHPRC,A.TOTPRES,A.TOTDWN,A.TKANG,A.TOT_UPAY,A.T_FUPAY
				, T_LUPAY,A.NPRICE - A.NCSHPRC AS VAR1,I.STAT,I.COLOR
				, C.SNAM+C.NAME1+' '+C.NAME2 as CUSNAME,G.GDESC
				, (select rc.SNAM+rc.NAME1+' '+rc.NAME2 from {$this->MAuth->getdb('CUSTMAST')} rc 
					where rc.CUSCOD=A.RECOMCOD) as RECOMCOD
				, A.ACTICOD,A.COMEXT,A.COMOPT,A.COMOTH ,A.INTRT
			FROM {$this->MAuth->getdb('ARMAST')} A  
			LEFT OUTER JOIN {$this->MAuth->getdb('CUSTMAST')} C ON C.CUSCOD = A.CUSCOD 
			LEFT OUTER JOIN {$this->MAuth->getdb('CUSTADDR')} D ON D.CUSCOD=C.CUSCOD AND (C.ADDRNO = D.ADDRNO) 
			LEFT OUTER JOIN {$this->MAuth->getdb('INVTRAN')} I ON A.STRNO = I.STRNO AND (A.CONTNO=I.CONTNO)
			LEFT OUTER JOIN {$this->MAuth->getdb('SETGROUP')} G ON G.GCODE = I.GCODE 
			WHERE 1=1 {$cond}
			
			UNION
			select A.LOCAT,A.TSALE,A.CONTNO,A.CUSCOD,A.RESVNO,A.STRNO
				, convert(varchar(8),A.SDATE,112) as SDATE
				, A.NPRICE,A.VATPRC,A.NPAYRES,A.VATPRES,A.BALANC
				, A.NKANG,A.VKANG,A.NDAWN,A.VATDWN,A.NCSHPRC,A.VCSHPRC
				, convert(varchar(8),A.FDATE,112) as FDATE,A.NPROFIT
				, convert(varchar(8),A.LDATE,112) as LDATE
				, A.N_FUPAY,A.V_FUPAY,A.N_UPAY,A.V_UPAY,A.N_LUPAY,A.V_LUPAY
				, A.TAXNO,convert(varchar(8),A.TAXDT,112) as TAXDT,A.BILLCOLL,A.CHECKER
				, A.DELYRT,A.DLDAY,A.T_NOPAY,A.T_UPAY,A.OPTCST,A.OPTCVT,A.OPTCTOT,A.OPTPRC
				, A.OPTPVT,A.OPTPTOT,A.NCARCST,A.VCARCST,A.SALCOD,A.COMITN,A.FLCANCL
				, A.TOTPRC,A.TCSHPRC,A.TOTPRES,A.TOTDWN,A.TKANG,A.TOT_UPAY,A.T_FUPAY
				, T_LUPAY,A.NPRICE - A.NCSHPRC AS VAR1,I.STAT,I.COLOR
				, C.SNAM+C.NAME1+' '+C.NAME2 as CUSNAME,G.GDESC
				, (select rc.SNAM+rc.NAME1+' '+rc.NAME2 from {$this->MAuth->getdb('CUSTMAST')} rc 
					where rc.CUSCOD=A.RECOMCOD) as RECOMCOD
				, A.ACTICOD,A.COMEXT,A.COMOPT,A.COMOTH ,A.INTRT
			FROM {$this->MAuth->getdb('HARMAST')} A  
			LEFT OUTER JOIN {$this->MAuth->getdb('CUSTMAST')} C ON C.CUSCOD = A.CUSCOD 
			LEFT OUTER JOIN {$this->MAuth->getdb('CUSTADDR')} D ON D.CUSCOD=C.CUSCOD AND (C.ADDRNO = D.ADDRNO) 
			LEFT OUTER JOIN {$this->MAuth->getdb('HINVTRAN')} I ON A.STRNO = I.STRNO AND (A.CONTNO=I.CONTNO)
			LEFT OUTER JOIN {$this->MAuth->getdb('SETGROUP')} G ON G.GCODE = I.GCODE 
			WHERE 1=1 {$cond}
			{$sort}
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		$NRow = 1;
		if($query->row()){
			foreach($query->result() as $row){
				if($arrs['RPT'] == 1){
					$ARINOPT = $this->checkINOPT($row->CONTNO);
					$html .= "
						<tr>
							<td>".$row->LOCAT."
								<br>&emsp;
								<br>&emsp;
								
							</td>
							<td>".$row->CONTNO."<br>".$row->CUSNAME."<br>".$row->STRNO."
								".($ARINOPT["TOPIC"] == "" ? "" : "<br>".$ARINOPT["TOPIC"])."
								
							</td>
							<td>".$row->CUSCOD."
								<br>&emsp;
								<br>&emsp;
								".($ARINOPT["OPTCODE"] == "" ? "" : "<br>".$ARINOPT["OPTCODE"])."
							</td>
							<td>
								<span style='mso-number-format:\"\@\";'>".$this->Convertdate(2,$row->SDATE)."</span>
								<br><span style='mso-number-format:\"\@\";'>".$this->Convertdate(2,$row->TAXDT)."</span>
								<br><span style='mso-number-format:\"\@\";'>".$this->Convertdate(2,$row->FDATE)."</span>
								".($ARINOPT["SIZE"] == "" ? "" : "<br>".$ARINOPT["SIZE"])."
							</td>
							<td class='text-right'>
								".$row->RESVNO."
								<br>".$row->TAXNO."
								<br>".number_format($row->T_UPAY,0)."
								".($ARINOPT["QTY"] == "" ? "" : "<br>".$ARINOPT["QTY"])."
							</td>
							<td class='text-right'>
								".number_format($row->DLDAY,0)."
								<br>".number_format($row->DELYRT,2)."
								<br>".number_format($row->T_NOPAY,0)."
								".($ARINOPT["UM"] == "" ? "" : "<br>".$ARINOPT["UM"])."
							</td>
							<td class='text-right'>
								".($row->SALCOD)."
								<br>".($row->BILLCOLL)."
								<br>".($row->CHECKER)."
								".($ARINOPT["TUPRICE"] == "" ? "" : "<br>".$ARINOPT["TUPRICE"])."
							</td>
							<td class='text-right'>
								".number_format($row->TOTPRC,2)."
								<br>".number_format($row->T_FUPAY,2)."
								<br>".number_format($row->COMITN,2)."
								".($ARINOPT["TUPRICE"] == "" ? "" : "<br>".$ARINOPT["UPRICE"])."
							</td>
							<td class='text-right'>
								".number_format($row->TOTPRES,2)."
								<br>".number_format($row->T_UPAY,2)."
								<br>".number_format($row->COMEXT,2)."
								".($ARINOPT["TUPRICE"] == "" ? "" : "<br>".$ARINOPT["UPRICE_UM"])."
							</td>
							<td class='text-right'>
								".number_format($row->BALANC,2)."
								<br>".number_format($row->T_LUPAY,2)."
								<br>".number_format($row->COMOPT,2)."
								".($ARINOPT["TTOTPRC"] == "" ? "" : "<br>".$ARINOPT["TTOTPRC"])."
							</td>
							
							<td class='text-right'>
								".number_format($row->TOTDWN,2)."
								<br>".number_format($row->NPROFIT,2)."
								<br>".number_format($row->COMOTH,2)."
								".($ARINOPT["TOTPRC"] == "" ? "" : "<br>".$ARINOPT["TOTPRC"])."
							</td>
							<td class='text-right'>
								".number_format($row->TKANG,2)."
								<br>".($row->ACTICOD)."
								<br>".($row->RECOMCOD)."
								".($ARINOPT["TOTPRC_UM"] == "" ? "" : "<br>".$ARINOPT["TOTPRC_UM"])."
							</td>
							<td class='text-right'>
								".number_format($row->TCSHPRC,2)."
								<br>".($row->STAT)."
								<br>".($row->COLOR)."
							</td>							
						</tr>
					";
				}else{
					$html .= "
						<tr>
							<td>".$NRow."</td>
							<td>".$row->LOCAT."</td>
							<td>".$row->CONTNO."</td>
							<td>".$row->CUSNAME."</td>
							<td style='mso-number-format:\"\@\";'>".$this->Convertdate(2,$row->SDATE)."</td>
							<td style='mso-number-format:\"\@\";'>".$this->Convertdate(2,$row->FDATE)."</td>							
							<td align='right'>".number_format($row->TOTPRC,2)."</td>
							<td align='right'>".number_format($row->TOTDWN,2)."</td>
							<td align='right'>".number_format($row->TKANG,2)."</td>
							<td align='right'>".number_format($row->T_NOPAY,2)."</td>
							<td align='right'>".number_format($row->T_UPAY,2)."</td>
							<td style='mso-number-format:\"\@\";'>".$this->Convertdate(2,$row->LDATE)."</td>							
							<td>".($row->SALCOD)."</td>
							<td align='right'>".number_format($row->VAR1,2)."</td>
							<td align='right'>".number_format($row->INTRT,2)."</td>
							<td>".($row->ACTICOD)."</td>
							<td>".($row->STRNO)."</td>
							<td>".($row->COLOR)."</td>
						</tr>
					";
				}
				
				foreach($row as $key => $val){
					if(is_numeric($val)){
						$data_sum[$key] = (isset($data_sum[$key]) ? $data_sum[$key]: 0)+$val;
					}
				}
				
				$NRow++;
			}
		}
		
		$headcs = 0;
		$head 	= "";
		$foot 	= "";
		if($arrs['RPT'] == 1){
			$headcs = 13;
			$head = "
				<tr>
					<th>สาขา<br>&emsp;<br>&emsp;</th>
					<th>เลขที่สัญญา<br>ชื่อ-สกุลลูกค้า<br>เลขตัวถัง</th>
					<th>รหัสลูกค้า<br>&emsp;<br>&emsp;</th>
					<th>วันที่ทำสัญญา<br>วันที่ใบกำกับ<br>วันดิวงวดแรก</th>
					<th>เลขที่บิลจอง<br>เลขที่ใบกำกับ<br>ผ่อน(เดือนงวด)</th>
					<th>ล่าช้าได้ไม่เกิน<br>เบี้ยปรับ(%)<br>ผ่อนจำนวน(งวด)</th>
					<th>พนักงานขาย<br>BILLCOLL<br>CHECKER</th>
					<th>ราคาขาย<br>ค่างวดแรก<br>คอมมิชชั่น</th>
					<th>เงินจอง<br>ค่างวดถัดไป<br>ค่าคอมบุคคลนอก</th>
					<th>ยอดคงเหลือ<br>ค่างวดสุดท้าย<br>ค่าของแถม</th>
					<th>เงินดาวน์<br>ดอกผลเช่าซื้อ<br>คชจ.อื่นๆ</th>
					<th>ตั้งลูกหนี้<br>กิจกรรมการขาย<br>ผู้แนะนำ</th>
					<th>ราคาขายสด<br>สถานะภาพรถ<br>สีรถ</th>
				</tr>
			";
			
			$foot = "
				<tr>
					<th>รวม</th>
					<th>".--$NRow."</th>
					<th>รายการ</th>
					<th></th>
					<th class='text-right'>เป็นเงิน </th>
					<th class='text-right'>====></th>
					<th class='text-right'></th>
					<th class='text-right'>
						".number_format($data_sum["TOTPRC"],2)."
						<br>&emsp;
						<br>".number_format($data_sum["COMITN"],2)."
					</th>
					<th class='text-right'>
						".number_format($data_sum["TOTPRES"],2)."
						<br>&emsp;
						<br>".number_format($data_sum["COMEXT"],2)."
					</th>
					<th class='text-right'>
						".number_format($data_sum["BALANC"],2)."
						<br>&emsp;
						<br>".number_format($data_sum["COMOPT"],2)."
					</th>
					<th class='text-right'>
						".number_format($data_sum["TOTDWN"],2)."
						<br>".number_format($data_sum["NPROFIT"],2)."
						<br>".number_format($data_sum["COMOTH"],2)."
					</th>
					<th class='text-right'>
						".number_format($data_sum["TKANG"],2)."
						<br>&emsp;
						<br>&emsp;
					</th>
					<th class='text-right'>
						".number_format($data_sum["TCSHPRC"],2)."
						<br>&emsp;
						<br>&emsp;
					</th>							
				</tr>
			";
		}else{
			$headcs = 18;
			$head = "
				<tr>
					<th>ลำดับ</th>
					<th>สาขา</th>
					<th>เลขที่สัญญา</th>
					<th>ชื่อ-สกุล</th>
					<th>วันที่ทำสัญญา</th>
					<th>วันดิวงวดแรก</th>
					<th>ราคาขาย</th>
					<th>เงินดาวน์</th>
					<th>ยอดลดหนี้</th>
					<th>จำนวนงวด</th>
					<th>ค่างวดถัดไป</th>
					<th>ดิวงวดสุดท้าย</th>
					<th>พนักงานขาย</th>
					<th>ดอกผลทั้งสัญญา</th>
					<th>อัตราดอกเบี้ย</th>
					<th>กิจกรรมการขาย</th>
					<th>เลขตัวถัง</th>
					<th>สีรถ</th>
				</tr>
			";
			$foot = "
				<tr>
					<th>รวมทั้งสิ้น</th>
					<th>".--$NRow."</th>
					<th>รายการ</th>
					<th>เป็นเงิน</th>
					<th>====></th>
					<th></th>
					<th align='right'>".number_format($data_sum["TOTPRC"],2)."</th>
					<th align='right'>".number_format($data_sum["TOTDWN"],2)."</th>
					<th align='right'>".number_format($data_sum["TKANG"],2)."</th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th align='right'>".number_format($data_sum["VAR1"],2)."</th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
				</tr>
			";
		}
		
		$html = "
			<div id='table-fixed-RPSellCar' class='col-sm-12' style='height:calc(100% - 0px);width:100%;overflow:auto;font-size:8pt;'>
				<table id='table-RPSellCar' class='table table-bordered' cellspacing='0' width='calc(100% - 1px)'>
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
						{$head}
					</thead>	
					<tbody>
						{$html}
					</tbody>
					<tfoot style='background: rgba(0, 0, 0, 0) url(&#39;../public/lobiadmin-master/version/1.0/ajax/img/bg/bg4.png&#39;) repeat scroll 0% 0%;'>
						{$foot}
					</tfoot>
				</table>
			</div>
		";
		
		$response = array("html"=>$html,"status"=>true);
		echo json_encode($response);
	}
	
	function getACCSELL($arrs){
		$cond = "";
		$condDesc = "";
		$reportName = "";
		if($arrs['REPORT'] == 1){
			$reportName = " รายงานการขายสดเพื่อแผนกบัญชี";
		}else if($arrs['REPORT'] == 2){
			$cond .= " and isnull(A.STRNO,'') = ''";
			$reportName = " รายงานการขายผ่อนเพื่อแผนกบัญชี";
		}else if($arrs['REPORT'] == 3){
			$cond .= " and A.SDATE is null";
			$reportName = " รายงานการขายไฟแนนท์เพื่อแผนกบัญชี";
		}else if($arrs['REPORT'] == 4){
			$cond .= " and A.SDATE is not null";
			$reportName = " รายงานการขายส่งเอเย่นต์เพื่อแผนกบัญชี";
		}
		
		if($arrs['locat'] != ""){
			$cond .= " and A.LOCAT like '".$arrs['locat']."%'";
			$condDesc .= "สาขา ".$arrs['locat'];
		}
		
		if($arrs['sSDATE'] != "" and $arrs['eSDATE'] != ""){
			$cond .= " and convert(varchar(8),A.SDATE,112) between '".$arrs['sSDATE']."' and '".$arrs['eSDATE']."' ";
			$condDesc .= " วันที่ทำสัญญา จากวันที่  ".$_POST['sSDATE']." - ".$_POST['eSDATE'];
		}else if($arrs['sSDATE'] != "" and $arrs['eSDATE'] == ""){
			$cond .= " and convert(varchar(8),A.SDATE,112) = '".$arrs['sSDATE']."'";
			$condDesc .= " วันที่ทำสัญญา  ".$_POST['sSDATE'];
		}else if($arrs['sSDATE'] == "" and $arrs['eSDATE'] != ""){
			$cond .= " and convert(varchar(8),A.SDATE,112) = '".$arrs['eSDATE']."'";
			$condDesc .= " วันที่ทำสัญญา  ".$_POST['eSDATE'];
		}
		
		if($arrs['MODEL'] != ""){
			$cond .= " and I.MODEL like '".$arrs['MODEL']."%'";
			$condDesc .= " รุ่น  ".$_POST['MODEL'];
		}
		
		if($arrs['BAAB'] != ""){
			$cond .= " and I.BAAB like '".$arrs['BAAB']."%'";
			$condDesc .= " แบบ ".$_POST['BAAB'];
		}
		
		if($arrs['COLOR'] != ""){
			$cond .= " and I.COLOR like '".$arrs['COLOR']."%'";
			$condDesc .= " สี ".$_POST['COLOR'];
		}
		
		if($arrs['STAT'] != "A"){
			$cond .= " and I.STAT like '".$arrs['STAT']."%'";
			$condDesc .= " สถานะรถ ".$_POST['STAT'];
		}
		
		if($arrs['GROUPCUS'] != ""){
			$cond .= " and C.GROUP1 like '".$arrs['GROUPCUS']."%'";
			$condDesc .= " กลุ่มลูกหนี้ ".$_POST['GROUPCUS'];
		}
		
		if($arrs['GCODE'] != ""){
			$cond .= " and G.GCODE like '".$arrs['GCODE']."%'";
			$condDesc .= " กลุ่มรถ ".$_POST['GCODE'];
		}
		
		if($arrs['SALCOD'] != ""){
			$cond .= " and A.SALCOD like '".$arrs['SALCOD']."'";
			$condDesc .= " พนักงานขาย ".$_POST['SALCOD'];
		}
		
		if($arrs['AUMPCOD'] != ""){
			$cond .= " and D.AUMPCOD like '".$arrs['AUMPCOD']."'";
			$condDesc .= " อำเภอ ".$_POST['AUMPCOD'];
		}
		
		if($arrs['PROVCOD'] != ""){
			$cond .= " and D.PROVCOD like '".$arrs['PROVCOD']."'";
			$condDesc .= " อำเภอ ".$_POST['PROVCOD'];
		}
		
		if($arrs['PAYTYP'] != ""){
			$cond .= " and A.PAYTYP like '".$arrs['PAYTYP']."'";
			$condDesc .= " ประเภทการชำระ ".$_POST['PAYTYP'];
		}
		
		$sort = "";
		if($arrs['SORT'] == 1){ $sort = "order by SDATE"; }
		else if($arrs['SORT'] == 2){ $sort = "order by A.LOCAT"; }
		else if($arrs['SORT'] == 3){ $sort = "order by A.SALCOD"; }
		else if($arrs['SORT'] == 4){ $sort = "order by CONTNO"; }
		
		$sql = "select COMP_NM from {$this->MAuth->getdb('CONDPAY')}";
		$query = $this->db->query($sql);
		$company = "";
		if($query->row()){
			foreach($query->result() as $row){
				$company = $row->COMP_NM;
			}
		}
		
		$sql = "
			SELECT A.TSALE,A.LOCAT,A.CONTNO,A.CUSCOD,A.RESVNO,A.STRNO,convert(varchar(8),A.SDATE,112) as SDATE
				,A.NPRICE,A.VATPRC,A.TOTPRC,A.NKEYIN,A.VKEYIN,A.TKEYIN,A.CRDAMT,A.NKEYIN-A.NPAYRES AS NKANG
				,A.VKEYIN-A.VATPRES AS VKANG,A.TKEYIN-A.TOTPRES AS TKANG,A.NPAYRES,A.VATPRES,A.TOTPRES,A.OPTPRC
				,A.OPTPVT,A.OPTPTOT,A.OPTCST,A.OPTCVT,A.OPTCTOT,A.NCARCST,A.VCARCST,A.TCARCST,A.TAXNO,A.TAXDT
				,A.SALCOD,A.COMITN,A.TKEYIN-A.TOTPRES-A.CRDAMT AS VAR1,A.NPRICE  - A.OPTPRC  AS VAR4
				,A.VATPRC  - A.OPTPVT  AS VAR5,A.TOTPRC  - A.OPTPTOT AS VAR6,A.NCARCST + A.OPTCST  AS VAR7
				,A.VCARCST + A.OPTCVT  AS VAR8,A.TCARCST + A.OPTCTOT AS VAR9,I.STAT
				,C.SNAM+C.NAME1+' '+C.NAME2 as CUSNAME,G.GDESC,I.TADDCOST,A.PAYTYP
			FROM {$this->MAuth->getdb('ARCRED')} A  
			LEFT OUTER JOIN {$this->MAuth->getdb('CUSTMAST')} C ON C.CUSCOD = A.CUSCOD 
			LEFT OUTER JOIN {$this->MAuth->getdb('CUSTADDR')} D ON D.CUSCOD=C.CUSCOD AND (C.ADDRNO = D.ADDRNO) 
			LEFT OUTER JOIN {$this->MAuth->getdb('INVTRAN')} I ON A.STRNO = I.STRNO AND (A.CONTNO=I.CONTNO)
			LEFT OUTER JOIN {$this->MAuth->getdb('SETGROUP')} G ON G.GCODE = I.GCODE 
			WHERE  1=1 {$cond}
				
			union 			
			SELECT A.TSALE,A.LOCAT,A.CONTNO,A.CUSCOD,A.RESVNO,A.STRNO,convert(varchar(8),A.SDATE,112) as SDATE
				,A.NPRICE,A.VATPRC,A.TOTPRC,A.NKEYIN,A.VKEYIN,A.TKEYIN,A.CRDAMT,A.NKEYIN-A.NPAYRES AS NKANG
				,A.VKEYIN-A.VATPRES AS VKANG,A.TKEYIN-A.TOTPRES AS TKANG,A.NPAYRES,A.VATPRES,A.TOTPRES,A.OPTPRC
				,A.OPTPVT,A.OPTPTOT,A.OPTCST,A.OPTCVT,A.OPTCTOT,A.NCARCST,A.VCARCST,A.TCARCST,A.TAXNO,A.TAXDT
				,A.SALCOD,A.COMITN,A.TKEYIN-A.TOTPRES-A.CRDAMT AS VAR1,A.NPRICE  - A.OPTPRC  AS VAR4
				,A.VATPRC  - A.OPTPVT  AS VAR5,A.TOTPRC  - A.OPTPTOT AS VAR6,A.NCARCST + A.OPTCST  AS VAR7
				,A.VCARCST + A.OPTCVT  AS VAR8,A.TCARCST + A.OPTCTOT AS VAR9,I.STAT
				,C.SNAM+C.NAME1+' '+C.NAME2 as CUSNAME,G.GDESC,I.TADDCOST,A.PAYTYP
			FROM {$this->MAuth->getdb('HARCRED')} A  
			LEFT OUTER JOIN {$this->MAuth->getdb('CUSTMAST')} C ON C.CUSCOD = A.CUSCOD 
			LEFT OUTER JOIN {$this->MAuth->getdb('CUSTADDR')} D ON D.CUSCOD=C.CUSCOD AND (C.ADDRNO = D.ADDRNO) 
			LEFT OUTER JOIN {$this->MAuth->getdb('HINVTRAN')} I ON A.STRNO = I.STRNO AND (A.CONTNO=I.CONTNO)
			LEFT OUTER JOIN {$this->MAuth->getdb('SETGROUP')} G ON G.GCODE = I.GCODE 
			WHERE  1=1 {$cond}
			{$sort}
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		$NRow = 1;
		if($query->row()){
			foreach($query->result() as $row){
				if($arrs['RPT'] == 1){
					$html .= "
						<tr>
							<td>".$row->LOCAT."</td>
							<td>".$row->CONTNO."<br>".$row->CUSCOD."<br>".$row->STRNO."</td>
							<td style='mso-number-format:\"\@\";'>".$this->Convertdate(2,$row->SDATE)."<br>".$row->CUSNAME."</td>
							<td>".($row->STAT == "N"?"รถใหม่":"รถเก่า")."<br><br>".$row->PAYTYP."</td>
							<td class='text-right'>".number_format($row->NKEYIN,2)."<br>".number_format($row->TADDCOST,2)."<br>".number_format($row->NCARCST,2)."</td>
							<td class='text-right'>".number_format($row->VKEYIN,2)."<br>".number_format($row->CRDAMT,2)."<br>".number_format($row->VCARCST,2)."</td>
							<td class='text-right'>".number_format($row->TKEYIN,2)."<br>".number_format($row->VAR1,2)."<br>".number_format($row->TCARCST,2)."</td>
							<td class='text-right'>".number_format($row->NPAYRES,2)."<br>".number_format($row->OPTPRC,2)."<br>".number_format($row->OPTCST,2)."</td>
							<td class='text-right'>".number_format($row->VATPRES,2)."<br>".number_format($row->OPTPVT,2)."<br>".number_format($row->OPTCVT,2)."</td>
							<td class='text-right'>".number_format($row->TOTPRES,2)."<br>".number_format($row->OPTPTOT,2)."<br>".number_format($row->OPTCTOT,2)."</td>
							<td class='text-right'>".number_format($row->NKANG,2)."<br>".number_format($row->VAR4,2)."<br>".number_format($row->VAR7,2)."</td>
							<td class='text-right'>".number_format($row->VKANG,2)."<br>".number_format($row->VAR5,2)."<br>".number_format($row->VAR8,2)."</td>
							<td class='text-right'>".number_format($row->TKANG,2)."<br>".number_format($row->VAR6,2)."<br>".number_format($row->VAR9,2)."</td>
						</tr>
					";
				}
				
				$data_sum["NKEYIN"]  = (isset($data_sum["NKEYIN"]) ? $data_sum["NKEYIN"]:0) + $row->NKEYIN;
				$data_sum["VKEYIN"]  = (isset($data_sum["VKEYIN"]) ? $data_sum["VKEYIN"]:0) + $row->VKEYIN;
				$data_sum["TKEYIN"]  = (isset($data_sum["TKEYIN"]) ? $data_sum["TKEYIN"]:0) + $row->TKEYIN;
				$data_sum["NPAYRES"] = (isset($data_sum["NPAYRES"]) ? $data_sum["NPAYRES"]:0) + $row->NPAYRES;
				$data_sum["VATPRES"] = (isset($data_sum["VATPRES"]) ? $data_sum["VATPRES"]:0) + $row->VATPRES;
				$data_sum["TOTPRES"] = (isset($data_sum["TOTPRES"]) ? $data_sum["TOTPRES"]:0) + $row->TOTPRES;
				$data_sum["NKANG"]   = (isset($data_sum["NKANG"]) ? $data_sum["NKANG"]:0) + $row->NKANG;
				$data_sum["VKANG"]   = (isset($data_sum["VKANG"]) ? $data_sum["VKANG"]:0) + $row->VKANG;
				$data_sum["TKANG"]   = (isset($data_sum["TKANG"]) ? $data_sum["TKANG"]:0) + $row->TKANG;
				
				$data_sum["TADDCOST"] = (isset($data_sum["TADDCOST"]) ? $data_sum["TADDCOST"]:0) + $row->TADDCOST;
				$data_sum["CRDAMT"]  = (isset($data_sum["CRDAMT"]) ? $data_sum["CRDAMT"]:0) + $row->CRDAMT;
				$data_sum["VAR1"]  	 = (isset($data_sum["VAR1"]) ? $data_sum["VAR1"]:0) + $row->VAR1;
				$data_sum["OPTPRC"]  = (isset($data_sum["OPTPRC"]) ? $data_sum["OPTPRC"]:0) + $row->OPTPRC;
				$data_sum["OPTPVT"]  = (isset($data_sum["OPTPVT"]) ? $data_sum["OPTPVT"]:0) + $row->OPTPVT;
				$data_sum["OPTPTOT"] = (isset($data_sum["OPTPTOT"]) ? $data_sum["OPTPTOT"]:0) + $row->OPTPTOT;
				$data_sum["VAR4"]  	 = (isset($data_sum["VAR4"]) ? $data_sum["VAR4"]:0) + $row->VAR4;
				$data_sum["VAR5"]  	 = (isset($data_sum["VAR5"]) ? $data_sum["VAR5"]:0) + $row->VAR5;
				$data_sum["VAR6"]  	 = (isset($data_sum["VAR6"]) ? $data_sum["VAR6"]:0) + $row->VAR6;
				
				
				$data_sum["NCARCST"] = (isset($data_sum["NCARCST"]) ? $data_sum["NCARCST"]:0) + $row->NCARCST;
				$data_sum["VCARCST"] = (isset($data_sum["VCARCST"]) ? $data_sum["VCARCST"]:0) + $row->VCARCST;
				$data_sum["TCARCST"] = (isset($data_sum["TCARCST"]) ? $data_sum["TCARCST"]:0) + $row->TCARCST;
				$data_sum["OPTCST"]  = (isset($data_sum["OPTCST"]) ? $data_sum["OPTCST"]:0) + $row->OPTCST;
				$data_sum["OPTCVT"]  =  (isset($data_sum["OPTCVT"]) ? $data_sum["OPTCVT"]:0) + $row->OPTCVT;
				$data_sum["OPTCTOT"] = (isset($data_sum["OPTCTOT"]) ? $data_sum["OPTCTOT"]:0) + $row->OPTCTOT;
				$data_sum["VAR7"] 	 = (isset($data_sum["VAR7"]) ? $data_sum["VAR7"]:0) + $row->VAR7;
				$data_sum["VAR8"] 	 = (isset($data_sum["VAR8"]) ? $data_sum["VAR8"]:0) + $row->VAR8;
				$data_sum["VAR9"] 	 = (isset($data_sum["VAR9"]) ? $data_sum["VAR9"]:0) + $row->VAR9;
				
				$NRow++;
			}
		}
		
		$headcs = 0;
		$head 	= "";
		$foot 	= "";
		if($arrs['RPT'] == 1){
			$headcs = 13;
			$head = "
				<tr>
					<th>สาขา<br>&emsp;<br>&emsp;</th>
					<th>เลขที่สัญญา<br>รหัสลูกค้า<br>เลขตัวถัง</th>
					<th>วันที่ทำสัญา<br>ชื่อ-สกุลลูกค้า<br>&emsp;</th>
					<th>สถานะภาพรถ<br><br>การชำระเงิน</th>
					<th>มูลค่าราคาขาย<br>ต้นทุนซ่อม<br>มูลค่าตัวรถ</th>
					
					<th>ภาษีราคาขาย<br>ยอดหนี้รวมภาษี<br>ภาษีทุนรถ</th>
					<th>ราคาขายรวมภาษี<br>ล/น.คงเหลือรวมภาษี<br>ราคาทุนรถรวมภาษี</th>
					<th>มูลค่าเงินจอง<br>มูลค่าขายอุปกรณ์<br>มูลค่าทุนอุปกรณ์</th>
					<th>ภาษีเงินจอง<br>ภาษีขายอุปกรณ์<br>ภาษีทุนอุปกรณ์</th>
					<th>จองรวมภาษี<br>ขายอุปกรณ์รวมภาษี<br>ทุนอุปกรณ์รวมภาษี</th>
					<th>มูลค่าคงเหลือ<br>มูลค่าราคารถ<br>มูลค่าราคาทุน</th>
					<th>ภาษีคงเหลือ<br>ภาษีราคารถ<br>ภาษีราคาทุน</th>
					<th>ล/น.คงเหลือรวมภาษี<br>ราคาขายรถรวมภาษี<br>ราคาทุนรวมภาษี</th>
				</tr>
			";
			$foot = "
				<tr>
					<th>รวม</th>
					<th>".--$NRow."</th>
					<th>รายการ</th>
					<th></th>
					<th class='text-right'>".number_format($data_sum["NKEYIN"],2)."<br>".number_format($data_sum["TADDCOST"],2)."<br>".number_format($data_sum["NCARCST"],2)."</th>
					<th class='text-right'>".number_format($data_sum["VKEYIN"],2)."<br>".number_format($data_sum["CRDAMT"],2)."<br>".number_format($data_sum["VCARCST"],2)."</th>
					<th class='text-right'>".number_format($data_sum["TKEYIN"],2)."<br>".number_format($data_sum["VAR1"],2)."<br>".number_format($data_sum["TCARCST"],2)."</th>
					<th class='text-right'>".number_format($data_sum["NPAYRES"],2)."<br>".number_format($data_sum["OPTPRC"],2)."<br>".number_format($data_sum["OPTCST"],2)."</th>
					<th class='text-right'>".number_format($data_sum["VATPRES"],2)."<br>".number_format($data_sum["OPTPVT"],2)."<br>".number_format($data_sum["OPTCVT"],2)."</th>
					<th class='text-right'>".number_format($data_sum["TOTPRES"],2)."<br>".number_format($data_sum["OPTPTOT"],2)."<br>".number_format($data_sum["OPTCTOT"],2)."</th>
					<th class='text-right'>".number_format($data_sum["NKANG"],2)."<br>".number_format($data_sum["VAR4"],2)."<br>".number_format($data_sum["VAR7"],2)."</th>
					<th class='text-right'>".number_format($data_sum["VKANG"],2)."<br>".number_format($data_sum["VAR5"],2)."<br>".number_format($data_sum["VAR8"],2)."</th>
					<th class='text-right'>".number_format($data_sum["TKANG"],2)."<br>".number_format($data_sum["VAR6"],2)."<br>".number_format($data_sum["VAR9"],2)."</th>
				</tr>
			";
		}
		
		$html = "
			<div id='table-fixed-RPSellCar' class='col-sm-12' style='height:calc(100% - 0px);width:100%;overflow:auto;font-size:8pt;'>
				<table id='table-RPSellCar' class='table table-bordered' cellspacing='0' width='calc(100% - 1px)'>
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
						{$head}
					</thead>	
					<tbody>
						{$html}
					</tbody>
					<tfoot style='background: rgba(0, 0, 0, 0) url(&#39;../public/lobiadmin-master/version/1.0/ajax/img/bg/bg4.png&#39;) repeat scroll 0% 0%;'>
						{$foot}
					</tfoot>
				</table>
			</div>
		";
		
		$response = array("html"=>$html,"status"=>true);
		echo json_encode($response);
	}
	
	function getACCLEASING($arrs){
		$cond = "";
		$condDesc = "";
		$reportName = "";
		if($arrs['REPORT'] == 1){
			$reportName = " รายงานการขายสดเพื่อแผนกบัญชี";
		}else if($arrs['REPORT'] == 2){
			$reportName = " รายงานการขายผ่อนเพื่อแผนกบัญชี";
		}else if($arrs['REPORT'] == 3){
			$cond .= " and A.SDATE is null";
			$reportName = " รายงานการขายไฟแนนท์เพื่อแผนกบัญชี";
		}else if($arrs['REPORT'] == 4){
			$cond .= " and A.SDATE is not null";
			$reportName = " รายงานการขายส่งเอเย่นต์เพื่อแผนกบัญชี";
		}
		
		if($arrs['locat'] != ""){
			$cond .= " and A.LOCAT like '".$arrs['locat']."%'";
			$condDesc .= "สาขา ".$arrs['locat'];
		}
		
		if($arrs['sSDATE'] != "" and $arrs['eSDATE'] != ""){
			$cond .= " and convert(varchar(8),A.SDATE,112) between '".$arrs['sSDATE']."' and '".$arrs['eSDATE']."' ";
			$condDesc .= " วันที่ทำสัญญา จากวันที่  ".$_POST['sSDATE']." - ".$_POST['eSDATE'];
		}else if($arrs['sSDATE'] != "" and $arrs['eSDATE'] == ""){
			$cond .= " and convert(varchar(8),A.SDATE,112) = '".$arrs['sSDATE']."'";
			$condDesc .= " วันที่ทำสัญญา  ".$_POST['sSDATE'];
		}else if($arrs['sSDATE'] == "" and $arrs['eSDATE'] != ""){
			$cond .= " and convert(varchar(8),A.SDATE,112) = '".$arrs['eSDATE']."'";
			$condDesc .= " วันที่ทำสัญญา  ".$_POST['eSDATE'];
		}
		
		if($arrs['MODEL'] != ""){
			$cond .= " and I.MODEL like '".$arrs['MODEL']."%'";
			$condDesc .= " รุ่น  ".$_POST['MODEL'];
		}
		
		if($arrs['BAAB'] != ""){
			$cond .= " and I.BAAB like '".$arrs['BAAB']."%'";
			$condDesc .= " แบบ ".$_POST['BAAB'];
		}
		
		if($arrs['COLOR'] != ""){
			$cond .= " and I.COLOR like '".$arrs['COLOR']."%'";
			$condDesc .= " สี ".$_POST['COLOR'];
		}
		
		if($arrs['STAT'] != "A"){
			$cond .= " and I.STAT like '".$arrs['STAT']."%'";
			$condDesc .= " สถานะรถ ".$_POST['STAT'];
		}
		
		if($arrs['GROUPCUS'] != ""){
			$cond .= " and C.GROUP1 like '".$arrs['GROUPCUS']."%'";
			$condDesc .= " กลุ่มลูกหนี้ ".$_POST['GROUPCUS'];
		}
		
		if($arrs['GCODE'] != ""){
			$cond .= " and G.GCODE like '".$arrs['GCODE']."%'";
			$condDesc .= " กลุ่มรถ ".$_POST['GCODE'];
		}
		
		if($arrs['SALCOD'] != ""){
			$cond .= " and A.SALCOD like '".$arrs['SALCOD']."'";
			$condDesc .= " พนักงานขาย ".$_POST['SALCOD'];
		}
		
		if($arrs['AUMPCOD'] != ""){
			$cond .= " and D.AUMPCOD like '".$arrs['AUMPCOD']."'";
			$condDesc .= " อำเภอ ".$_POST['AUMPCOD'];
		}
		
		if($arrs['PROVCOD'] != ""){
			$cond .= " and D.PROVCOD like '".$arrs['PROVCOD']."'";
			$condDesc .= " อำเภอ ".$_POST['PROVCOD'];
		}
		
		if($arrs['PAYTYP'] != ""){
			$cond .= " and A.PAYTYP like '".$arrs['PAYTYP']."'";
			$condDesc .= " ประเภทการชำระ ".$_POST['PAYTYP'];
		}
		
		$sort = "";
		if($arrs['SORT'] == 1){ $sort = "order by SDATE"; }
		else if($arrs['SORT'] == 2){ $sort = "order by A.LOCAT"; }
		else if($arrs['SORT'] == 3){ $sort = "order by A.SALCOD"; }
		else if($arrs['SORT'] == 4){ $sort = "order by CONTNO"; }
		
		$sql = "select COMP_NM from {$this->MAuth->getdb('CONDPAY')}";
		$query = $this->db->query($sql);
		$company = "";
		if($query->row()){
			foreach($query->result() as $row){
				$company = $row->COMP_NM;
			}
		}
		
		$sql = "
			SELECT A.LOCAT,A.TSALE,A.CONTNO,A.SALCOD,A.CUSCOD,A.RESVNO,A.STRNO,convert(varchar(8),A.SDATE,112) as SDATE
				,A.BILLCOLL,A.ACTICOD,A.NPRICE,A.VATPRC,A.TOTPRC,A.NPAYRES,A.VATPRES,A.TOTPRES,A.BALANC,A.NKANG,A.VKANG
				,A.TKANG,A.NDAWN,A.VATDWN,A.TOTDWN,A.NCSHPRC,A.VCSHPRC,A.TCSHPRC,A.OPTPRC,A.OPTPVT,A.OPTPTOT,A.OPTCST
				,A.OPTCVT,A.OPTCTOT,I.NETCOST,I.CRVAT,I.TOTCOST,A.N_FUPAY,A.V_FUPAY,A.T_FUPAY,A.N_UPAY,A.V_UPAY
				,A.TOT_UPAY,A.T_UPAY,A.N_LUPAY,A.V_LUPAY,A.T_LUPAY,A.T_NOPAY,A.NPRICE-A.NCSHPRC AS NPROFIT
				,A.NPRICE-NPAYRES AS VAR1
				,A.VATPRC-VATPRES AS VAR2
				,A.TOTPRC-TOTPRES AS VAR3
				,I.NETCOST+A.OPTCST AS VAR4
				,I.CRVAT+A.OPTCVT AS VAR5
				,I.TOTCOST+A.OPTCTOT AS VAR6
				,A.NCSHPRC-(I.NETCOST+A.OPTCST) AS VAR7
				,A.NPRICE-A.OPTPRC AS VAR8
				,A.VATPRC-A.OPTPVT AS VAR9
				,A.TOTPRC-A.OPTPTOT AS VAR10
				,A.NPRICE-(I.NETCOST+A.OPTCST) AS VAR11
				,I.STAT,C.SNAM+C.NAME1+' '+C.NAME2 as CUSNAME
				,G.GDESC,I.TADDCOST
				,0 as NKEYIN,0 as NCARCST
			FROM {$this->MAuth->getdb('ARMAST')} A  
			LEFT OUTER JOIN {$this->MAuth->getdb('CUSTMAST')} C ON C.CUSCOD = A.CUSCOD 
			LEFT OUTER JOIN {$this->MAuth->getdb('CUSTADDR')} D ON D.CUSCOD=C.CUSCOD AND (C.ADDRNO = D.ADDRNO) 
			LEFT OUTER JOIN {$this->MAuth->getdb('INVTRAN')} I ON A.STRNO = I.STRNO AND (A.CONTNO=I.CONTNO)
			LEFT OUTER JOIN {$this->MAuth->getdb('SETGROUP')} G ON G.GCODE = I.GCODE 
			WHERE 1=1 {$cond}
			
			UNION
			SELECT A.LOCAT,A.TSALE,A.CONTNO,A.SALCOD,A.CUSCOD,A.RESVNO,A.STRNO,convert(varchar(8),A.SDATE,112) as SDATE
				,A.BILLCOLL,A.ACTICOD,A.NPRICE,A.VATPRC,A.TOTPRC,A.NPAYRES,A.VATPRES,A.TOTPRES,A.BALANC,A.NKANG,A.VKANG
				,A.TKANG,A.NDAWN,A.VATDWN,A.TOTDWN,A.NCSHPRC,A.VCSHPRC,A.TCSHPRC,A.OPTPRC,A.OPTPVT,A.OPTPTOT,A.OPTCST
				,A.OPTCVT,A.OPTCTOT,I.NETCOST,I.CRVAT,I.TOTCOST,A.N_FUPAY,A.V_FUPAY,A.T_FUPAY,A.N_UPAY,A.V_UPAY
				,A.TOT_UPAY,A.T_UPAY,A.N_LUPAY,A.V_LUPAY,A.T_LUPAY,A.T_NOPAY,A.NPRICE-A.NCSHPRC AS NPROFIT
				,A.NPRICE-NPAYRES   AS VAR1,A.VATPRC-VATPRES  AS VAR2,A.TOTPRC-TOTPRES  AS VAR3,I.NETCOST+A.OPTCST  AS VAR4
				,I.CRVAT+A.OPTCVT  AS VAR5,I.TOTCOST+A.OPTCTOT AS VAR6,A.NCSHPRC-(I.NETCOST+A.OPTCST) AS VAR7,A.NPRICE-A.OPTPRC  AS VAR8
				,A.VATPRC-A.OPTPVT  AS VAR9,A.TOTPRC-A.OPTPTOT AS VAR10,A.NPRICE-(I.NETCOST+A.OPTCST) AS VAR11,I.STAT
				,C.SNAM+C.NAME1+' '+C.NAME2 as CUSNAME,G.GDESC,I.TADDCOST
				,0 as NKEYIN,0 as NCARCST
			FROM {$this->MAuth->getdb('HARMAST')} A  
			LEFT OUTER JOIN {$this->MAuth->getdb('CUSTMAST')} C ON C.CUSCOD = A.CUSCOD 
			LEFT OUTER JOIN {$this->MAuth->getdb('CUSTADDR')} D ON D.CUSCOD=C.CUSCOD AND (C.ADDRNO = D.ADDRNO) 
			LEFT OUTER JOIN {$this->MAuth->getdb('HINVTRAN')} I ON A.STRNO = I.STRNO AND (A.CONTNO=I.CONTNO)
			LEFT OUTER JOIN {$this->MAuth->getdb('SETGROUP')} G ON G.GCODE = I.GCODE 
			WHERE 1=1 {$cond}
			{$sort}
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		$NRow = 1;
		if($query->row()){
			foreach($query->result() as $row){
				if($arrs['RPT'] == 1){
					$html .= "
						<tr>
							<td>".$row->LOCAT."</td>
							<td>".$row->CONTNO."<br>".$row->CUSNAME."<br>".$row->STRNO."<br>".$row->ACTICOD."</td>
							<td>".$row->CUSCOD."<br><br><br><br></td>
							<td>
								<span style='mso-number-format:\"\@\";'>".$this->Convertdate(2,$row->SDATE)."</span>
								<br>".$row->BILLCOLL."
								<br>".($row->STAT == "N"?"รถใหม่":"รถเก่า")."
								<br>".number_format($row->TADDCOST,2)."
								<br>".$row->T_NOPAY."
							</td>
							<td class='text-right'>
								".number_format($row->NPRICE,2)."
								<br>".number_format($row->NPAYRES,2)."
								<br>".number_format($row->NETCOST,2)."
								<br>".number_format($row->N_FUPAY,2)."
								<br>".number_format($row->NCSHPRC,2)."
							</td>
							<td class='text-right'>
								".number_format($row->VATPRC,2)."
								<br>".number_format($row->VATPRES,2)."
								<br>".number_format($row->CRVAT,2)."
								<br>".number_format($row->V_FUPAY,2)."
								<br>".number_format($row->VCSHPRC,2)."
							</td>
							<td class='text-right'>
								".number_format($row->TOTPRC,2)."
								<br>".number_format($row->TOTPRES,2)."
								<br>".number_format($row->TOTCOST,2)."
								<br>".number_format($row->T_FUPAY,2)."
								<br>".number_format($row->TCSHPRC,2)."
							</td>
							<td class='text-right'>
								".number_format($row->OPTPRC,2)."
								<br>".number_format($row->NDAWN,2)."
								<br>".number_format($row->OPTCST,2)."
								<br>".number_format($row->N_UPAY,2)."
								<br>".number_format($row->NPROFIT,2)."
							</td>
							<td class='text-right'>
								".number_format($row->OPTPVT,2)."
								<br>".number_format($row->VATDWN,2)."
								<br>".number_format($row->OPTCVT,2)."
								<br>".number_format($row->V_UPAY,2)."
								<br>".number_format($row->VAR7,2)."
							</td>
							<td class='text-right'>
								".number_format($row->OPTPTOT,2)."
								<br>".number_format($row->TOTDWN,2)."
								<br>".number_format($row->OPTCTOT,2)."
								<br>".number_format($row->TOT_UPAY,2)."
								<br>".number_format($row->VAR11,2)."
							</td>
							<td class='text-right'>
								".number_format($row->VAR8,2)."
								<br>".number_format($row->VAR1,2)."
								<br>".number_format($row->VAR4,2)."
								<br>".number_format($row->N_LUPAY,2)."
								<br>".number_format($row->NKANG,2)."
							</td>
							<td class='text-right'>
								".number_format($row->VAR9,2)."
								<br>".number_format($row->VAR2,2)."
								<br>".number_format($row->VAR5,2)."
								<br>".number_format($row->V_LUPAY,2)."
								<br>".number_format($row->VKANG,2)."
							</td>
							<td class='text-right'>
								".number_format($row->VAR10,2)."
								<br>".number_format($row->VAR3,2)."
								<br>".number_format($row->VAR6,2)."
								<br>".number_format($row->T_LUPAY,2)."
								<br>".number_format($row->TKANG,2)."
							</td>							
						</tr>
					";
				}
				
				foreach($row as $key => $val){
					if(is_numeric($val)){
						$data_sum[$key] = (isset($data_sum[$key]) ? $data_sum[$key]: 0)+$val;
					}
				}
				
				$NRow++;
			}
		}
		
		$headcs = 0;
		$head 	= "";
		$foot	= "";
		if($arrs['RPT'] == 1){
			$headcs = 13;
			$head = "
				<tr>
					<th>สาขา<br>&emsp;<br>&emsp;<br>&emsp;<br>&emsp;</th>
					<th>เลขที่สัญญา<br>ชื่อ-สกุลลูกค้า<br>เลขตัวถัง<br>กิจกรรมการขาย<br>&emsp;</th>
					<th>รหัสลูกค้า<br>&emsp;<br>&emsp;<br>&emsp;<br>&emsp;</th>
					<th>วันที่ทำสัญญา<br>BillColl<br>สถานะภาพรถ<br>ต้นทุนซ่อม<br>จน.งวดทั้งหมด</th>
					<th>มูลค่าราคาขาย<br>มูลค่าเงินจอง<br>มูลค่าทุนรถ<br>มูลค่างวดแรก<br>มูลค่าราคาขายสด</th>
					<th>ภาษีราคาขาย<br>ภาษีเงินจอง<br>ภาษีทุนรถ<br>ภาษีงวดแรก<br>ภาษีขายสด</th>
					<th>ราคาขายรวมภาษี<br>เงินจองรวมภาษี<br>ทุนรถรวมภาษี<br>ค่างวดแรกรวมภาษี<br>ราคาขายสดรวมภาษี</th>
					<th>มูลค่าขายอุปกรณ์<br>มูลค่าเงินดาวน์<br>มูลค่าทุนอุปกรณ์<br>มูลค่างวดถัดไป<br>ดอกผลเช่าซื้อ</th>
					<th>ภาษีขายอุปกรณ์<br>ภาษีเงินดาวน์<br>ภาษีทุนอุปกรณ์<br>ภาษีงวดถัดไป<br>กำไรขั้นต้น</th>
					<th>ราคาอุปกรณ์รวมภาษี<br>เงินดาวน์รวมภาษี<br>ทุนอุปกรณ์รวมภาษี<br>ค่างวดถัดไปรวมภาษี<br>กำไรขั้นต้นดอกผล</th>
					<th>มูลค่าตัวรถ<br>มูลค่าราคาขายหักจอง<br>มูลค่าราคาทุน<br>มูลค่างวดสุดท้าย<br>มูลค่ายอดตั้งลูกหนี้</th>
					<th>ภาษีตัวรถ<br>ภาษีขายหักจอง<br>ภาษีราคาทุน<br>ภาษีงวดสุดท้าย<br>ภาษียอดตั้งลูกหนี้</th>
					<th>ราคาตัวรถรวมภาษี<br>ยอดขายหักจองรวมภาษ๊<br>ราคาทุนรวมภาษี<br>ค่างวดสุดท้ายรวมภาษี<br>ยอดตั้งลูกหนี้รวมภาษี</th>
				</tr>
			";
			$foot = "
				<tr>
					<th>รวม</th>
					<th>".--$NRow."</th>
					<th>รายการ</th>
					<th>
						<span style='mso-number-format:\"\@\";'></span>
						<br>&emsp;
						<br>&emsp;
						<br>".number_format($data_sum["TADDCOST"],2)."
						<br>&emsp;
					</th>
					<th class='text-right'>
						".number_format($data_sum["NPRICE"],2)."
						<br>".number_format($data_sum["NPAYRES"],2)."
						<br>".number_format($data_sum["NETCOST"],2)."
						<br>".number_format($data_sum["N_FUPAY"],2)."
						<br>".number_format($data_sum["NCSHPRC"],2)."
					</th>
					<th class='text-right'>
						".number_format($data_sum["VATPRC"],2)."
						<br>".number_format($data_sum["VATPRES"],2)."
						<br>".number_format($data_sum["CRVAT"],2)."
						<br>".number_format($data_sum["V_FUPAY"],2)."
						<br>".number_format($data_sum["VCSHPRC"],2)."
					</th>
					<th class='text-right'>
						".number_format($data_sum["TOTPRC"],2)."
						<br>".number_format($data_sum["TOTPRES"],2)."
						<br>".number_format($data_sum["TOTCOST"],2)."
						<br>".number_format($data_sum["T_FUPAY"],2)."
						<br>".number_format($data_sum["TCSHPRC"],2)."
					</th>
					<th class='text-right'>
						".number_format($data_sum["OPTPRC"],2)."
						<br>".number_format($data_sum["NDAWN"],2)."
						<br>".number_format($data_sum["OPTCST"],2)."
						<br>".number_format($data_sum["N_UPAY"],2)."
						<br>".number_format($data_sum["NPROFIT"],2)."
					</th>
					<th class='text-right'>
						".number_format($data_sum["OPTPVT"],2)."
						<br>".number_format($data_sum["VATDWN"],2)."
						<br>".number_format($data_sum["OPTCVT"],2)."
						<br>".number_format($data_sum["V_UPAY"],2)."
						<br>".number_format($data_sum["VAR7"],2)."
					</th>
					<th class='text-right'>
						".number_format($data_sum["OPTPTOT"],2)."
						<br>".number_format($data_sum["TOTDWN"],2)."
						<br>".number_format($data_sum["OPTCTOT"],2)."
						<br>".number_format($data_sum["TOT_UPAY"],2)."
						<br>".number_format($data_sum["VAR11"],2)."
					</th>
					<th class='text-right'>
						".number_format($data_sum["VAR8"],2)."
						<br>".number_format($data_sum["VAR1"],2)."
						<br>".number_format($data_sum["VAR4"],2)."
						<br>".number_format($data_sum["N_LUPAY"],2)."
						<br>".number_format($data_sum["NKANG"],2)."
					</th>
					<th class='text-right'>
						".number_format($data_sum["VAR9"],2)."
						<br>".number_format($data_sum["VAR2"],2)."
						<br>".number_format($data_sum["VAR5"],2)."
						<br>".number_format($data_sum["V_LUPAY"],2)."
						<br>".number_format($data_sum["VKANG"],2)."
					</th>
					<th class='text-right'>
						".number_format($data_sum["VAR10"],2)."
						<br>".number_format($data_sum["VAR3"],2)."
						<br>".number_format($data_sum["VAR6"],2)."
						<br>".number_format($data_sum["T_LUPAY"],2)."
						<br>".number_format($data_sum["TKANG"],2)."
					</th>							
				</tr>
			";
		}
		
		$html = "
			<div id='table-fixed-RPSellCar' class='col-sm-12' style='height:calc(100% - 0px);width:100%;overflow:auto;font-size:8pt;'>
				<table id='table-RPSellCar' class='table table-bordered' cellspacing='0' width='calc(100% - 1px)'>
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
						{$head}
					</thead>	
					<tbody>
						{$html}
					</tbody>
					<tfoot style='background: rgba(0, 0, 0, 0) url(&#39;../public/lobiadmin-master/version/1.0/ajax/img/bg/bg4.png&#39;) repeat scroll 0% 0%;'>
						{$foot}
					</tfoot>
				</table>
			</div>
		";
		
		$response = array("html"=>$html,"status"=>true);
		echo json_encode($response);
	}
	
	function getACCFINANCE($arrs){
		$cond = "";
		$condDesc = "";
		$reportName = "";
		if($arrs['REPORT'] == 1){
			$reportName = " รายงานการขายสดเพื่อแผนกบัญชี";
		}else if($arrs['REPORT'] == 2){
			$reportName = " รายงานการขายผ่อนเพื่อแผนกบัญชี";
		}else if($arrs['REPORT'] == 3){
			$reportName = " รายงานการขายไฟแนนท์เพื่อแผนกบัญชี";
		}else if($arrs['REPORT'] == 4){
			$reportName = " รายงานการขายส่งเอเย่นต์เพื่อแผนกบัญชี";
		}
		
		if($arrs['locat'] != ""){
			$cond .= " and A.LOCAT like '".$arrs['locat']."%'";
			$condDesc .= "สาขา ".$arrs['locat'];
		}
		
		if($arrs['sSDATE'] != "" and $arrs['eSDATE'] != ""){
			$cond .= " and convert(varchar(8),A.SDATE,112) between '".$arrs['sSDATE']."' and '".$arrs['eSDATE']."' ";
			$condDesc .= " วันที่จอง จากวันที่  ".$_POST['sSDATE']." - ".$_POST['eSDATE'];
		}else if($arrs['sSDATE'] != "" and $arrs['eSDATE'] == ""){
			$cond .= " and convert(varchar(8),A.SDATE,112) = '".$arrs['sSDATE']."'";
			$condDesc .= " วันที่จอง  ".$_POST['sSDATE'];
		}else if($arrs['sSDATE'] == "" and $arrs['eSDATE'] != ""){
			$cond .= " and convert(varchar(8),A.SDATE,112) = '".$arrs['eSDATE']."'";
			$condDesc .= " วันที่จอง  ".$_POST['eSDATE'];
		}
		
		if($arrs['MODEL'] != ""){
			$cond .= " and I.MODEL like '".$arrs['MODEL']."%'";
			$condDesc .= " รุ่น  ".$_POST['MODEL'];
		}
		
		if($arrs['BAAB'] != ""){
			$cond .= " and I.BAAB like '".$arrs['BAAB']."%'";
			$condDesc .= " แบบ ".$_POST['BAAB'];
		}
		
		if($arrs['COLOR'] != ""){
			$cond .= " and I.COLOR like '".$arrs['COLOR']."%'";
			$condDesc .= " สี ".$_POST['COLOR'];
		}
		
		if($arrs['STAT'] != "A"){
			$cond .= " and I.STAT like '".$arrs['STAT']."%'";
			$condDesc .= " สถานะรถ ".$_POST['STAT'];
		}
		
		if($arrs['GROUPCUS'] != ""){
			$cond .= " and C.GROUP1 like '".$arrs['GROUPCUS']."%'";
			$condDesc .= " กลุ่มลูกหนี้ ".$_POST['GROUPCUS'];
		}
		
		if($arrs['GCODE'] != ""){
			$cond .= " and G.GCODE like '".$arrs['GCODE']."%'";
			$condDesc .= " กลุ่มรถ ".$_POST['GCODE'];
		}
		
		if($arrs['SALCOD'] != ""){
			$cond .= " and A.SALCOD like '".$arrs['SALCOD']."'";
			$condDesc .= " พนักงานขาย ".$_POST['SALCOD'];
		}
		
		if($arrs['AUMPCOD'] != ""){
			$cond .= " and D.AUMPCOD like '".$arrs['AUMPCOD']."'";
			$condDesc .= " อำเภอ ".$_POST['AUMPCOD'];
		}
		
		if($arrs['PROVCOD'] != ""){
			$cond .= " and D.PROVCOD like '".$arrs['PROVCOD']."'";
			$condDesc .= " อำเภอ ".$_POST['PROVCOD'];
		}
		
		if($arrs['PAYTYP'] != ""){
			$cond .= " and A.PAYTYP like '".$arrs['PAYTYP']."'";
			$condDesc .= " ประเภทการชำระ ".$_POST['PAYTYP'];
		}
		
		$sort = "";
		if($arrs['SORT'] == 1){ $sort = "order by SDATE"; }
		else if($arrs['SORT'] == 2){ $sort = "order by A.LOCAT"; }
		else if($arrs['SORT'] == 3){ $sort = "order by A.SALCOD"; }
		else if($arrs['SORT'] == 4){ $sort = "order by CONTNO"; }
		
		$sql = "select COMP_NM from {$this->MAuth->getdb('CONDPAY')}";
		$query = $this->db->query($sql);
		$company = "";
		if($query->row()){
			foreach($query->result() as $row){
				$company = $row->COMP_NM;
			}
		}
		
		$sql = "
			SELECT A.LOCAT,A.TSALE,A.CONTNO,A.SALCOD,A.CUSCOD,convert(varchar(8),A.SDATE,112) as SDATE
				,A.FINCOD,A.FINCOM,A.COMITN,A.RESVNO
				,A.STRNO,A.NPRICE,A.VATPRC,A.TOTPRC,A.NPAYRES,A.VATPRES,A.TOTPRES,A.NDAWN,A.VATDWN,A.TOTDWN
				,A.NKANG,A.VKANG,A.TKANG,A.NFINAN,A.VATFIN,A.TOTFIN,A.NCARCST,A.VCARCST,A.TCARCST,A.OPTCST
				,A.OPTCVT,A.OPTCTOT,A.OPTPRC,A.OPTPVT,A.OPTPTOT,A.NKEYIN,A.VKEYIN,A.TKEYIN,A.CRDAMT
				,A.TKEYIN-A.TOTPRES-A.CRDAMT AS ARBAL,A.NPRICE-A.NDAWN    AS VAR1
				,A.VATPRC-A.VATDWN   AS VAR2,A.TOTPRC-A.TOTDWN   AS VAR3,A.NCARCST+A.OPTCST  AS VAR4
				,A.VCARCST+A.OPTCVT  AS VAR5,A.TCARCST+A.OPTCTOT AS VAR6,I.STAT,C.SNAM+C.NAME1+' '+C.NAME2 as CUSNAME
				,G.GDESC,F.FINNAME,I.TADDCOST  
			FROM {$this->MAuth->getdb('ARFINC')} A 
			LEFT OUTER JOIN {$this->MAuth->getdb('CUSTMAST')} C ON C.CUSCOD = A.CUSCOD 
			LEFT OUTER JOIN {$this->MAuth->getdb('CUSTADDR')} D ON D.CUSCOD=C.CUSCOD AND (C.ADDRNO = D.ADDRNO) 
			LEFT OUTER JOIN {$this->MAuth->getdb('INVTRAN')} I ON A.STRNO = I.STRNO 
			LEFT OUTER JOIN {$this->MAuth->getdb('SETGROUP')} G ON G.GCODE = I.GCODE 
			LEFT OUTER JOIN {$this->MAuth->getdb('FINMAST')} F ON F.FINCODE = A.FINCOD 
			WHERE  1=1 {$cond}
				
			UNION 
			SELECT A.LOCAT,A.TSALE,A.SALCOD,A.CONTNO,A.CUSCOD,convert(varchar(8),A.SDATE,112) as SDATE
				,A.FINCOD,A.FINCOM,A.COMITN,A.RESVNO
				,A.STRNO,A.NPRICE,A.VATPRC,A.TOTPRC,A.NPAYRES,A.VATPRES,A.TOTPRES,A.NDAWN,A.VATDWN,A.TOTDWN
				,A.NKANG,A.VKANG,A.TKANG,A.NFINAN,A.VATFIN,A.TOTFIN,A.NCARCST,A.VCARCST,A.TCARCST,A.OPTCST
				,A.OPTCVT,A.OPTCTOT,A.OPTPRC,A.OPTPVT,A.OPTPTOT,A.NKEYIN,A.VKEYIN,A.TKEYIN,A.CRDAMT
				,A.TKEYIN-A.TOTPRES-A.CRDAMT AS ARBAL,A.NPRICE-A.NDAWN    AS VAR1,A.VATPRC-A.VATDWN   AS VAR2
				,A.TOTPRC-A.TOTDWN   AS VAR3,A.NCARCST+A.OPTCST  AS VAR4,A.VCARCST+A.OPTCVT  AS VAR5
				,A.TCARCST+A.OPTCTOT AS VAR6,I.STAT,C.SNAM+C.NAME1+' '+C.NAME2 as CUSNAME,G.GDESC,F.FINNAME,I.TADDCOST  
			FROM {$this->MAuth->getdb('HARFINC')} A 
			LEFT OUTER JOIN {$this->MAuth->getdb('CUSTMAST')} C ON C.CUSCOD = A.CUSCOD 
			LEFT OUTER JOIN {$this->MAuth->getdb('CUSTADDR')} D ON D.CUSCOD=C.CUSCOD AND (C.ADDRNO = D.ADDRNO) 
			LEFT OUTER JOIN {$this->MAuth->getdb('HINVTRAN')} I ON A.STRNO = I.STRNO 
			LEFT OUTER JOIN {$this->MAuth->getdb('SETGROUP')} G ON G.GCODE = I.GCODE 
			LEFT OUTER JOIN {$this->MAuth->getdb('FINMAST')} F ON F.FINCODE = A.FINCOD 
			WHERE  1=1 {$cond}
			{$sort}
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		$NRow = 1;
		if($query->row()){
			foreach($query->result() as $row){
				if($arrs['RPT'] == 1){
					$html .= "
						<tr>
							<td>".$row->LOCAT."</td>
							<td>".$row->CONTNO."<br>".$row->CUSCOD."<br>".$row->STRNO."<br>".$row->FINNAME."</td>
							<td style='mso-number-format:\"\@\";'>".$this->Convertdate(2,$row->SDATE)."<br>".$row->CUSNAME."</td>
							<td>".($row->STAT == "N"?"รถใหม่":"รถเก่า")."<br><br></td>
							<td class='text-right'>".number_format($row->NKEYIN,2)."<br>".number_format($row->NFINAN,2)."<br>".number_format($row->NCARCST,2)."<br>".number_format($row->OPTPRC,2)."</td>
							<td class='text-right'>".number_format($row->VKEYIN,2)."<br>".number_format($row->VATFIN,2)."<br>".number_format($row->VCARCST,2)."<br>".number_format($row->OPTPVT,2)."</td>
							<td class='text-right'>".number_format($row->TKEYIN,2)."<br>".number_format($row->TOTFIN,2)."<br>".number_format($row->TCARCST,2)."<br>".number_format($row->OPTPTOT,2)."</td>
							<td class='text-right'>".number_format($row->NPAYRES,2)."<br>".number_format($row->VAR1,2)."<br>".number_format($row->OPTCST,2)."<br>".number_format($row->CRDAMT,2)."</td>
							<td class='text-right'>".number_format($row->VATPRES,2)."<br>".number_format($row->VAR2,2)."<br>".number_format($row->OPTCVT,2)."<br>".number_format($row->ARBAL,2)."</td>
							<td class='text-right'>".number_format($row->TOTPRES,2)."<br>".number_format($row->VAR3,2)."<br>".number_format($row->OPTCTOT,2)."</td>
							<td class='text-right'>".number_format($row->NDAWN,2)."<br>".number_format($row->NKANG,2)."<br>".number_format($row->NCARCST,2)."</td>
							<td class='text-right'>".number_format($row->VATDWN,2)."<br>".number_format($row->VKANG,2)."<br>".number_format($row->VCARCST,2)."</td>
							<td class='text-right'>".number_format($row->TOTDWN,2)."<br>".number_format($row->TKANG,2)."<br>".number_format($row->TCARCST,2)."</td>
						</tr>
					";
				}
				
				$NRow++;
			}
		}
		
		$head = "";
		$headcs = 0;
		if($arrs['RPT'] == 1){
			$head = "
				<tr>
					<th>สาขา<br>&emsp;<br>&emsp;<br>&emsp;</th>
					<th>เลขที่สัญญา<br>รหัสลูกค้า<br>เลขตัวถัง<br>บริษัทไฟแนนท์</th>
					<th>วันที่ทำสัญา<br>ชื่อ-สกุลลูกค้า<br>&emsp;<br>&emsp;</th>
					<th>สถานะภาพรถ<br>&emsp;<br>&emsp;<br>&emsp;</th>
					<th>มูลค่าราคาขาย<br>มูลค่าส่งไฟแนนท์<br>มูลค่าทุนรถ<br>มูลค่าขายอุปกรณ์</th>
					
					<th>ภาษีขายรถ<br>ภาษีส่งไฟแนนท์<br>ภาษีทุนตัวรถ<br>ภาษีขายอุปกรณ์</th>
					<th>ราคาขายรวมภาษี<br>ส่งไฟแนนท์รวมภาษี<br>ทุนรถรวมภาษี<br>ขายอุปกรณ์รวมภาษี</th>
					<th>มูลค่าเงินจอง<br>มูลค่าขาย-ดาวน์<br>มูลค่าทุนอุปกรณ์<br>ยอดลดหนี้รวมภาษี</th>
					<th>ภาษีเงินจอง<br>ภาษีขาย-ดาวน์<br>ภาษีทุนอุปกรณ์<br>ราคาขาย-จอง-ลดหนี้</th>
					<th>เงินจองรวมภาษี<br>ขายดาวน์รวมภาษี<br>ทุนอุปกรณ์รวมภาษี<br>ทุนค่าซ่อมรวมภาษี</th>
					
					<th>มูลค่าเงินดาวน์<br>มูลค่ายอดตั้งลูกหนี้<br>มูลค่าทุนรวม<br>&emsp;</th>
					<th>ภาษีเงินดาวน์<br>ภาษียอดตั้งลูกหนี้<br>ภาษีทุนรวม<br>&emsp;</th>
					<th>เงินดาวน์รวมภาษี<br>ยอดลูกหนี้รวมภาษี<br>ทุนรถ+อุปรวมภาษี<br>&emsp;</th>
				</tr>
			";
			$headcs = 13;
		}
		
		$html = "
			<div id='table-fixed-RPSellCar' class='col-sm-12' style='height:calc(100% - 0px);width:100%;overflow:auto;font-size:8pt;'>
				<table id='table-RPSellCar' class='table table-bordered' cellspacing='0' width='calc(100% - 1px)'>
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
						{$head}
					</thead>	
					<tbody>
						{$html}
					</tbody>
				</table>
			</div>
		";
		
		$response = array("html"=>$html,"status"=>true);
		echo json_encode($response);
	}
	
	function getACCAGENT($arrs){
		$cond = "";
		$condDesc = "";
		$reportName = "";
		if($arrs['REPORT'] == 1){
			$reportName = " รายงานการขายสดเพื่อแผนกบัญชี";
		}else if($arrs['REPORT'] == 2){
			$cond .= " and isnull(A.STRNO,'') = ''";
			$reportName = " รายงานการขายผ่อนเพื่อแผนกบัญชี";
		}else if($arrs['REPORT'] == 3){
			$cond .= " and A.SDATE is null";
			$reportName = " รายงานการขายไฟแนนท์เพื่อแผนกบัญชี";
		}else if($arrs['REPORT'] == 4){
			$cond .= " and A.SDATE is not null";
			$reportName = " รายงานการขายส่งเอเย่นต์เพื่อแผนกบัญชี";
		}
		
		if($arrs['locat'] != ""){
			$cond .= " and A.LOCAT like '".$arrs['locat']."%'";
			$condDesc .= "สาขา ".$arrs['locat'];
		}
		
		if($arrs['sSDATE'] != "" and $arrs['eSDATE'] != ""){
			$cond .= " and convert(varchar(8),A.SDATE,112) between '".$arrs['sSDATE']."' and '".$arrs['eSDATE']."' ";
			$condDesc .= " วันที่ทำสัญญา จากวันที่  ".$_POST['sSDATE']." - ".$_POST['eSDATE'];
		}else if($arrs['sSDATE'] != "" and $arrs['eSDATE'] == ""){
			$cond .= " and convert(varchar(8),A.SDATE,112) = '".$arrs['sSDATE']."'";
			$condDesc .= " วันที่ทำสัญญา  ".$_POST['sSDATE'];
		}else if($arrs['sSDATE'] == "" and $arrs['eSDATE'] != ""){
			$cond .= " and convert(varchar(8),A.SDATE,112) = '".$arrs['eSDATE']."'";
			$condDesc .= " วันที่ทำสัญญา  ".$_POST['eSDATE'];
		}
		
		if($arrs['MODEL'] != ""){
			$cond .= " and I.MODEL like '".$arrs['MODEL']."%'";
			$condDesc .= " รุ่น  ".$_POST['MODEL'];
		}
		
		if($arrs['BAAB'] != ""){
			$cond .= " and I.BAAB like '".$arrs['BAAB']."%'";
			$condDesc .= " แบบ ".$_POST['BAAB'];
		}
		
		if($arrs['COLOR'] != ""){
			$cond .= " and I.COLOR like '".$arrs['COLOR']."%'";
			$condDesc .= " สี ".$_POST['COLOR'];
		}
		
		if($arrs['STAT'] != "A"){
			$cond .= " and I.STAT like '".$arrs['STAT']."%'";
			$condDesc .= " สถานะรถ ".$_POST['STAT'];
		}
		
		if($arrs['GROUPCUS'] != ""){
			$cond .= " and C.GROUP1 like '".$arrs['GROUPCUS']."%'";
			$condDesc .= " กลุ่มลูกหนี้ ".$_POST['GROUPCUS'];
		}
		
		if($arrs['GCODE'] != ""){
			$cond .= " and G.GCODE like '".$arrs['GCODE']."%'";
			$condDesc .= " กลุ่มรถ ".$_POST['GCODE'];
		}
		
		if($arrs['SALCOD'] != ""){
			$cond .= " and A.SALCOD like '".$arrs['SALCOD']."'";
			$condDesc .= " พนักงานขาย ".$_POST['SALCOD'];
		}
		
		if($arrs['AUMPCOD'] != ""){
			$cond .= " and D.AUMPCOD like '".$arrs['AUMPCOD']."'";
			$condDesc .= " อำเภอ ".$_POST['AUMPCOD'];
		}
		
		if($arrs['PROVCOD'] != ""){
			$cond .= " and D.PROVCOD like '".$arrs['PROVCOD']."'";
			$condDesc .= " อำเภอ ".$_POST['PROVCOD'];
		}
		
		if($arrs['PAYTYP'] != ""){
			$cond .= " and A.PAYTYP like '".$arrs['PAYTYP']."'";
			$condDesc .= " ประเภทการชำระ ".$_POST['PAYTYP'];
		}
		
		$sort = "";
		if($arrs['SORT'] == 1){ $sort = "order by SDATE"; }
		else if($arrs['SORT'] == 2){ $sort = "order by A.LOCAT"; }
		else if($arrs['SORT'] == 3){ $sort = "order by A.SALCOD"; }
		else if($arrs['SORT'] == 4){ $sort = "order by CONTNO"; }
		
		$sql = "select COMP_NM from {$this->MAuth->getdb('CONDPAY')}";
		$query = $this->db->query($sql);
		$company = "";
		if($query->row()){
			foreach($query->result() as $row){
				$company = $row->COMP_NM;
			}
		}
		
		$sql = "
			

			SELECT A.LOCAT,A.CONTNO,A.CUSCOD,convert(varchar(8),A.SDATE,112) as SDATE
				,A.NPRICE,A.VATPRC,A.TOTPRC,A.TAXNO,A.TAXDT,A.SMPAY,A.SMCHQ
				,A.SALCOD,A.TKEYIN,A.NKEYIN,A.VKEYIN,A.CRDAMT,A.TKEYIN-A.CRDAMT-A.SMPAY-A.SMCHQ AS ARBAL
				,A.TOTPRC-A.SMPAY AS VAR1,A.TOTPRC-A.SMPAY-A.SMCHQ AS VAR2
				,RTRIM(C.SNAM)+' '+RTRIM(C.NAME1)+' '+RTRIM(C.NAME2) AS CUSNAME
				, a.PAYTYP 
			FROM {$this->MAuth->getdb('AR_INVOI')} A 
			LEFT OUTER JOIN {$this->MAuth->getdb('CUSTMAST')} C ON C.CUSCOD = A.CUSCOD 
			LEFT OUTER JOIN {$this->MAuth->getdb('CUSTADDR')} D ON D.CUSCOD=C.CUSCOD AND (C.ADDRNO = D.ADDRNO) 
			WHERE 1=1 {$cond}
			UNION 
			SELECT A.LOCAT,A.CONTNO,A.CUSCOD,convert(varchar(8),A.SDATE,112) as SDATE
				,A.NPRICE,A.VATPRC,A.TOTPRC,A.TAXNO,A.TAXDT,A.SMPAY,A.SMCHQ
				,A.SALCOD,A.TKEYIN,A.NKEYIN,A.VKEYIN,A.CRDAMT,A.TKEYIN-A.CRDAMT-A.SMPAY-A.SMCHQ AS ARBAL
				,A.TOTPRC-A.SMPAY AS VAR1,A.TOTPRC-A.SMPAY-A.SMCHQ AS VAR2
				,RTRIM(C.SNAM)+' '+RTRIM(C.NAME1)+' '+RTRIM(C.NAME2) AS CUSNAME
				, a.PAYTYP
			FROM {$this->MAuth->getdb('HAR_INVO')} A 
			LEFT OUTER JOIN {$this->MAuth->getdb('CUSTMAST')} C ON C.CUSCOD = A.CUSCOD 
			LEFT OUTER JOIN {$this->MAuth->getdb('CUSTADDR')} D ON D.CUSCOD=C.CUSCOD AND (C.ADDRNO = D.ADDRNO) 
			WHERE 1=1 {$cond}
			{$sort}
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		$NRow = 1;
		if($query->row()){
			foreach($query->result() as $row){
				if($arrs['RPT'] == 1){
					$sql = "
						SELECT T.CONTNO,T.STRNO,T.NPRICE as subNPRICE,T.VATPRC as subVATPRC,T.TOTPRC as subTOTPRC
							,I.STAT,I.CRCOST,I.NADDCOST,(I.CRCOST+I.NADDCOST) AS TOTCOST,T.NKEYIN as subNKEYIN
							,T.VKEYIN as subVKEYIN,T.TKEYIN  as subTKEYIN
						FROM {$this->MAuth->getdb('AR_TRANS')} T
						left join {$this->MAuth->getdb('INVTRAN')} I on (T.STRNO = I.STRNO) 
						left join {$this->MAuth->getdb('SETGROUP')} G on (G.GCODE = I.GCODE) 
						WHERE 1=1 AND (T.CONTNO='".$row->CONTNO."')

						UNION 
						SELECT T.CONTNO,T.STRNO,T.NPRICE,T.VATPRC,T.TOTPRC,I.STAT,I.CRCOST,I.NADDCOST
							,(I.CRCOST+I.NADDCOST) AS TOTCOST,T.NKEYIN,T.VKEYIN,T.TKEYIN  
						FROM {$this->MAuth->getdb('HAR_TRNS')} T
						left join {$this->MAuth->getdb('HINVTRAN')} I on (T.STRNO = I.STRNO)
						left join {$this->MAuth->getdb('SETGROUP')} G on (G.GCODE = I.GCODE) 
						WHERE 1=1 AND (T.CONTNO='".$row->CONTNO."')
					";
					$subQuery1 = $this->db->query($sql);
					
					$subNRow = 1;
					if($subQuery1->row()){
						$row = (array) $row;
						foreach($subQuery1->result() as $subRow){
							if($subNRow == 1){
								foreach($subRow as $key => $val){ 
									$row[$key] = ''; 
								}								
							}
							foreach($subRow as $key => $val){
								switch($key){
									case 'CONTNO': 	$row[$key] = $val; break;
									case 'STRNO': 	$row[$key] = ($row[$key] != '' ? $row[$key]."<br>":'').$val; break;
									case 'STAT':	
										$sub_data = ($val == "N" ? "รถใหม่":"รถเก่า");
										$row[$key] = ($row[$key] != '' ? $row[$key]."<br>":'').$sub_data; 
										break;
									case 'CRCOST':
										$sub_data = (string) (number_format($val,2));
										$row[$key] = ($row[$key] != '' ? $row[$key]."<br>":'').$sub_data;
										$row['HCRCOST'] = (isset($row['HCRCOST']) ? $row['HCRCOST']:0)+$val;
										break;
									default :
										$sub_data = (string) (number_format($val,2));
										$row[$key] = ($row[$key] != '' ? $row[$key]."<br>":'').$sub_data;
										break;
								}
							}
							
							$subNRow += 1;
						}
						$row = (object) $row;
					}
					
					
					$html .= "
						<tr>
							<td>".$row->LOCAT."</td>
							<td>".$row->CONTNO."<br>".$row->STRNO."</td>
							<td>".$row->CUSCOD."<br>".$row->STAT."</td>
							<td>".$row->CUSNAME."<br>".$row->CRCOST."</td>
							<td style='mso-number-format:\"\@\";'>".$this->Convertdate(2,$row->SDATE)."<br>".($row->NADDCOST)."</td>
							<td class='text-right'>".number_format($row->HCRCOST,2)."<br>".($row->TOTCOST)."</td>
							<td class='text-right'>".number_format($row->NKEYIN,2)."<br></td>
							<td class='text-right'>".number_format($row->VKEYIN,2)."<br>".($row->subNPRICE)."</td>
							<td class='text-right'>".number_format($row->TKEYIN,2)."<br>".($row->subVATPRC)."</td>
							<td class='text-right'>".number_format($row->CRDAMT,2)."<br>".($row->subTOTPRC)."</td>
							<td class='text-right'>".number_format($row->SMPAY,2)."<br></td>
							<td class='text-right'>".number_format($row->SMCHQ,2)."<br></td>
							<td class='text-right'>".number_format($row->ARBAL,2)."<br></td>
							<td class='text-right'>".($row->PAYTYP)."<br></td>
						</tr>
					";
				}
				
				$NRow++;
			}
		}
		
		$head = "";
		$headcs = 0;
		if($arrs['RPT'] == 1){
			$head = "
				<tr>
					<th>สาขา<br>&emsp;</th>
					<th>เลขที่สัญญา<br>เลขตัวถัง</th>
					<th>รหัสลูกค้า<br>สถานะภาพรถ</th>
					<th>ชื่อ-สกุลลูกค้า<br>มูลค่าทุนรถ</th>
					<th>วันที่ทำสัญญา<br>มูลค่าต้นทุนซ่อม</th>
					
					<th>ราคาต้นทุน<br>รวมมูลค่าต้นทุน</th>
					<th>ราคาขาย<br>&emsp;</th>
					<th>ภาษี<br>มูลค่าราคาขาย</th>
					<th>รวมราคาขาย<br>ภาษีขาย</th>
					<th>ยอดลดหนี้<br>ราคาขายรวมภาษี</th>
					<th>ชำระแล้ว<br>&emsp;</th>
					<th>เช็คไม่ถึงกำหนด<br>&emsp;</th>
					<th>คงเหลือสุทธิ<br>&emsp;</th>
					<th>การชำระเงิน<br>&emsp;</th>
				</tr>
			";
			$headcs = 14;
		}
		
		$html = "
			<div id='table-fixed-RPSellCar' class='col-sm-12' style='height:calc(100% - 0px);width:100%;overflow:auto;font-size:8pt;'>
				<table id='table-RPSellCar' class='table table-bordered' cellspacing='0' width='calc(100% - 1px)'>
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
						{$head}
					</thead>	
					<tbody>
						{$html}
					</tbody>
				</table>
			</div>
		";
		
		$response = array("html"=>$html,"status"=>true);
		echo json_encode($response);
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
				<input type='image' src='".base_url("public/images/loading-icon.gif")."'>			
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




















