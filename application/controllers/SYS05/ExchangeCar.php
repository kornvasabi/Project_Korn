<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//BEE+
class ExchangeCar extends MY_Controller {
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
		$sql = "
			select * from {$this->MAuth->getdb('VATMAST')}
			where getdate() between FRMDATE and TODATE
		";
		$query = $this->db->query($sql);
		$row = $query->row();
		$vat = number_format($row->VATRT,2);
		$html = "
			<div class='b_tab1' name='home' locat='{$this->sess['branch']}' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}' today='".$this->today('today')."' vat='".$vat."' style='height:calc(100vh - 132px);overflow:auto;background-color:white;'>
				<div class='col-sm-12 col-xs-12' style='overflow:auto;'>					
					<div class='row'>
						<div class='col-sm-2 col-xs-2'>	
							<div class='form-group'>
								สาขา
								<select id='LOCAT1' class='form-control input-sm' data-placeholder='สาขา'></select>
							</div>
						</div>
						<div class='col-sm-2 col-xs-2'>	
							<div class='form-group'>
								จากวันที่แลกเปลี่ยน
								<input type='text' id='FROMDATECHG' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' placeholder='จากวันที่แลกเปลี่ยน'>
							</div>
						</div>
						<div class='col-sm-2 col-xs-2'>	
							<div class='form-group'>
								ถึงวันที่แลกเปลี่ยน
								<input type='text' id='TODATECHG' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' placeholder='ถึงวันที่แลกเปลี่ยน'>
							</div>
						</div>
						<div class='col-sm-2 col-xs-2'>	
							<div class='form-group'>
								เลขที่สัญญา
								<input type='text' id='CONTNO1' class='form-control input-sm' placeholder='เลขที่สัญญา'>
							</div>
						</div>
						<!--div class='col-sm-2 col-xs-2'>	
							<div class='form-group'>
								ลูกหนี้
								<select id='CUSCOD1' class='form-control input-sm' data-placeholder='ลูกหนี้'></select>
							</div>
						</div--!>
						<div class='col-sm-2 col-xs-2'>	
							<div class='form-group'>
								<br>
								<button id='btnt1search' class='btn btn-primary btn-sm' style='width:100%'><span class='glyphicon glyphicon-search'> สอบถาม</span></button>
							</div>
						</div>
						<div class='col-sm-2 col-xs-2'>	
							<div class='form-group'>
								<br>
								<button id='bth1add' class='btn btn-cyan btn-sm'  style='width:100%'><span class='glyphicon glyphicon-pencil'> เพิ่มข้อมูล</span></button>
							</div>
						</div>
					</div><br>
					<div id='resultt_ExchangeCar' style='background-color:white;'></div>
				</div>
			</div>
		";
		
		$html.= "<script src='".base_url('public/js/SYS05/ExchangeCar.js')."'></script>";
		echo $html;
	}
	
	//ฟอร์มบันทึกลูกหนี้อื่น
	function getfromExchangeCar(){
		$level	= $_REQUEST["level"];
		//$locat = $this->sess['branch'];

		$html = "
			<div class='b_ExchangeCar' style='width:100%;height:calc(100vh - 132px);overflow:auto;background-color:white;'>
				<div style='float:left;height:100%;overflow:auto;' class='col-sm-10 col-xs-10 col-sm-offset-1'>
					<div class='form-group col-sm-10 col-xs-10 col-sm-offset-1' style='border:0.1px solid #f0f0f0;'>
					<div class='form-group col-sm-10 col-xs-10 col-sm-offset-1'>
					<div class='row'>
						<div class='col-sm-4 col-xs-4'>	
							<div class='form-group' style='color:blue;'>
								เลขที่สัญญา
								<select id='CONTNO' class='form-control input-sm' data-placeholder='เลขที่สัญญา'></select>
							</div>
						</div>
					</div>
					<div class='row'>
						<div class='col-sm-4 col-xs-4'>	
							<div class='form-group'>
								สาขา
								<input type='text' id='LOCAT' class='form-control input-sm' style='font-size:10.5pt' disabled>
							</div>
						</div>
						<div class='col-sm-4 col-xs-4'>	
							<div class='form-group'>
								ชื่อ - สกุล ลูกหนี้
								<input type='text' id='CUSNAME' class='form-control input-sm' style='font-size:10.5pt' disabled> 
							</div>
						</div>
						<div class='col-sm-4 col-xs-4'>	
							<div class='form-group'>
								รหัสลูกหนี้
								<input type='text' id='CUSCOD' class='form-control input-sm' style='font-size:10.5pt' disabled>
							</div>
						</div>
						<div class='col-sm-4 col-xs-4'>	
							<div class='form-group'>
								เลขทะเบียน
								<input type='text' id='REGNO' class='form-control input-sm' style='font-size:10.5pt' disabled>
							</div>
						</div>
						<div class='col-sm-4 col-xs-4'>	
							<div class='form-group'>
								เลขตัวถัง
								<input type='text' id='STRNO' class='form-control input-sm' style='font-size:10.5pt' disabled>
							</div>
						</div>
						<div class='col-sm-4 col-xs-4'>	
							<div class='form-group'>
								ราคาขาย
								<input type='text' id='PRICE' class='form-control input-sm' style='font-size:10.5pt' disabled>
							</div>
						</div>
						<div class='col-sm-4 col-xs-4'>	
							<div class='form-group'>
								ชำระเงินแล้ว
								<input type='text' id='SMPAY' class='form-control input-sm' style='font-size:10.5pt' disabled>
							</div>
						</div>
						<div class='col-sm-4 col-xs-4'>	
							<div class='form-group'>
								ยอดคงเหลือ
								<input type='text' id='BALANCE' class='form-control input-sm' style='font-size:10.5pt' disabled>
							</div>
						</div>
						<div class='col-sm-4 col-xs-4'>	
							<div class='form-group'>
								ยอดค้างชำระ
								<input type='text' id='NETAR' class='form-control input-sm' style='font-size:10.5pt' disabled>
							</div>
						</div>
					</div>
					</div>
					</div>
					
					<div class='form-group col-sm-10 col-xs-10 col-sm-offset-1' style='border:0.1px solid #f0f0f0;'>
					<div class='form-group col-sm-10 col-xs-10 col-sm-offset-1'>
					<div class='row'>
						<div class='col-sm-3 col-xs-3'>	
							<div class='form-group'>
								มูลค่าคงเหลือตามบัญชี
								<input type='text' id='BOOKVALUE' class='form-control input-sm' style='font-size:10.5pt' placeholder='0.00'>
							</div>
						</div>
						<div class='col-sm-3 col-xs-3'>	
							<div class='form-group'>
								ภาษีคงเหลือ
								<input type='text' id='SALEVAT' class='form-control input-sm' style='font-size:10.5pt' placeholder='0.00'>
							</div>
						</div>
						<div class='col-sm-3 col-xs-3'>	
							<div class='form-group'>
								มูลค่าต้นทุน (ไม่รวม VAT)
								<input type='text' id='COST' class='form-control input-sm' style='font-size:10.5pt' placeholder='0.00'>
							</div>
						</div>
						<div class='col-sm-3 col-xs-3'>	
							<div class='form-group'>
								ภาษีต้นทุนรถ
								<input type='text' id='COSTVAT' class='form-control input-sm' style='font-size:10.5pt' placeholder='0.00'>
							</div>
						</div>
						<div class='col-sm-3 col-xs-3'>	
							<div class='form-group'>
								วันที่เปลี่ยนเป็นรถเก่า
								<input type='text' id='DATECHG' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' style='font-size:10.5pt' value='".$this->today('today')."'>
							</div>
						</div>
						<div class='col-sm-3 col-xs-3'>	
							<div class='form-group'>
								สถานที่เก็บ
								<input type='text' id='LOCATR' class='form-control input-sm' style='font-size:10.5pt' placeholder='สถานที่เก็บ' readonly>
							</div>
						</div>
						<div class='col-sm-3 col-xs-3'>	
							<div class='form-group'>
								ราคาขายใหม่
								<input type='text' id='SALENEW' class='form-control input-sm' style='font-size:10.5pt' placeholder='0.00'>
							</div>
						</div>
						<div class='col-sm-3 col-xs-3'>	
							<div class='form-group' >
								ประเภทสินค้าใหม่
								<select id='GCODENEW' class='form-control input-sm' data-placeholder='ประเภทสินค้า' ></select>
							</div>
						</div>
					</div>
					</div>
					</div>
					<div class='row'>
					<div class=' col-sm-8 col-xs-8 col-sm-offset-2'>	
							<div class='form-group'>
								หมายเหตุ
								<textarea type='text' id='MEMO' rows='1' cols='20' class='form-control input-sm' style='font-size:10.5pt'></textarea>
							</div>
						</div>	
					</div>
					<div class='row'>
						<div class=' col-sm-2 col-sm-offset-4'>	
							<div class='form-group'>
								<br>
								<button id='btnsave_exchangecar' class='btn btn-primary btn-block'><span class='glyphicon glyphicon-floppy-disk'> บันทึก</span></button>
							</div>
						</div>
						<div class=' col-sm-2'>	
							<div class='form-group'>
								<br>
								<input type='button' id='btndel_exchangecar' class='btn btn-danger btn-sm' value='ลบ' style='width:100%'>
							</div>
						</div>
					</div>
				</div>
			</div>
		";
	
		$response = array("html"=>$html);
		echo json_encode($response);
	}
	
	function searchCONTNO(){
		$contno	= $_REQUEST["contno"];

		$sql = "
				select a.CONTNO, a.CRLOCAT, isnull(a.REGNO,'') as REGNO, a.STRNO , a.STAT, a.GCODE, d.GDESC, c.SNAM, c.NAME1, c.NAME2, b.CUSCOD, b.TOTPRC, b.SMPAY+b.SMCHQ as SMPAY, 
				b.TOTPRC - b.SMPAY - b.SMCHQ as BALANC, b.EXP_AMT, b.NKANG+b.TOTDWN-b.SMPAY-b.SMCHQ as BOOKVAL, b.VKANG, a.STDPRC, b.VATRT
				from {$this->MAuth->getdb('INVTRAN')} a
				left join {$this->MAuth->getdb('ARMAST')} b on a.CONTNO = b.CONTNO 
				left join {$this->MAuth->getdb('CUSTMAST')} c on b.CUSCOD = c.CUSCOD 
				left join {$this->MAuth->getdb('SETGROUP')} d on a.GCODE = d.GCODE
				where a.CONTNO = '".$contno."'
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$response = array();
		if($query->row()){
			foreach($query->result() as $row){
				$response["CONTNO"] 	= $row->CONTNO;
				$response["CRLOCAT"] 	= $row->CRLOCAT;
				$response["CUSNAME"] 	= $row->SNAM.$row->NAME1.' '.$row->NAME2;
				$response["CUSCOD"] 	= $row->CUSCOD;
				$response["REGNO"] 		= $row->REGNO;
				$response["STRNO"] 		= str_replace(chr(0),'',$row->STRNO);
				$response["TOTPRC"] 	= number_format($row->TOTPRC,2);
				$response["SMPAY"] 		= number_format($row->SMPAY,2);
				$response["BALANCE"] 	= number_format($row->BALANC,2);
				$response["EXP_AMT"] 	= number_format($row->EXP_AMT,2);
				$response["BOOKVALUE"]	= number_format($row->BOOKVAL,2);
				$response["VATPRC"] 	= number_format($row->VKANG,2);
				$response["RCVLOCAT"] 	= $row->CRLOCAT;
				$response["NEWPRC"] 	= number_format($row->STDPRC,2);
				$response["GCODE"] 		= str_replace(chr(0),'',$row->GCODE);
				$response["STAT"] 		= str_replace(chr(0),'',$row->STAT);
				$response["GDESC"] 		= $row->GDESC;
				$response["VATRT"]  	= number_format($row->VATRT);
			}
		}
		
		echo json_encode($response);
	}
	
	function search(){
		$LOCAT1	= $_REQUEST["LOCAT1"];
		$CONTNO1 = $_REQUEST["CONTNO1"];
		//$CUSCOD1 = $_REQUEST["CUSCOD1"];
		$FROMDATECHG = $_REQUEST["FROMDATECHG"];
		$TODATECHG = $_REQUEST["TODATECHG"];
		
		$cond = "";
		$rpcond = "";
		if($LOCAT1 != ""){
			$cond .= " and LOCAT = '".$LOCAT1."'";
			$rpcond .= "  สาขา ".$LOCAT1;
		}
		
		if($CONTNO1 != ""){
			$cond .= " and CONTNO like '".$CONTNO1."%' collate thai_cs_as";
		}
		
		/*if($CUSCOD1 != ""){
			$cond .= " and CUSCOD = '".$CUSCOD1."'";
		}*/
		
		if($FROMDATECHG != ""){
			$cond .= " and YDATE >= '".$this->Convertdate(1,$FROMDATECHG)."'";
			$rpcond .= "  จากวันที่แลกเปลี่ยน ".$FROMDATECHG;
		}
		
		if($TODATECHG != ""){
			$cond .= " and YDATE <= '".$this->Convertdate(1,$TODATECHG)."'";
			$rpcond .= "  ถึงวันที่ ".$TODATECHG;
		}
		
		$sql = "
				select ".($cond == '' ? 'top 100':'')." LOCAT, CONTNO, CUSCOD, SNAM, NAME1, NAME2, STRNO, isnull(REGNO,'') as REGNO, convert(nvarchar,dateadd(year,543,SDATE),103) as SDATE, 
				convert(nvarchar,dateadd(year,543,YDATE),103) as DATECHG, TOTPRC, SMPAY, TOTBAL, EXP_AMT, BOOKVAL, BOOKVAT, N_NETCST, N_NETVAT, N_NETTOT, STDPRC, 
				N_GCODE, GDESC, a.MEMO1
				from {$this->MAuth->getdb('ARCHAG')} a
				left join SETGROUP b on a.N_GCODE = b.GCODE 
				where 1=1 ".$cond."
				order by YDATE desc, LOCAT
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$sql2 = "
				select 'รวมทั้งสิน' as Total, sum(TOTPRC) as sumTOTPRC, sum(SMPAY) as sumSMPAY, sum(TOTBAL) as sumTOTBAL, sum(EXP_AMT) as sumEXP_AMT, sum(BOOKVAL) as sumBOOKVAL, sum(BOOKVAT) as sumBOOKVAT, 
				sum(N_NETCST) as sumN_NETCST, sum(N_NETVAT) as sumN_NETVAT, sum(N_NETTOT) as sumN_NETTOT
				from {$this->MAuth->getdb('ARCHAG')} 
				where 1=1 ".$cond."
		";
		//echo $sql2; exit;
		$query2 = $this->db->query($sql2);
		
		$head = ""; $html = ""; $report = ""; $sumreport = ""; $i=0; $ii=0;
	
		$head = "<tr style='height:30px;'>
				<th style='vertical-align:middle;'>#</th>
				<th style='vertical-align:middle;'>สาขา</th>
				<th style='vertical-align:middle;'>เลขที่สัญญา</th>
				<th style='vertical-align:middle;'>รหัสลูกค้า</th>
				<th style='vertical-align:middle;'>ชื่อ - นามสกุล</th>
				<th style='vertical-align:middle;'>เลขตัวถัง</th>
				<th style='vertical-align:middle;'>วันที่ขาย</th>
				<th style='vertical-align:middle;'>วันที่แลกเปลี่ยน</th>
				<th style='vertical-align:middle;'>ราคาขาย</th>
				<th style='vertical-align:middle;'>ชำระแล้ว</th>
				<th style='vertical-align:middle;'>คงเหลือ</th>
				<th style='vertical-align:middle;'>ค้างชำระ</th>
				<th style='vertical-align:middle;'>ราคาตามบัญชี</th>
				<th style='vertical-align:middle;'>ภาษีตามบัญชี</th>
				<th style='vertical-align:middle;'>ราคาประเมิน</th>
				<th style='vertical-align:middle;'>ภาษี</th>
				<th style='vertical-align:middle;'>รวมราคาประเมิน</th>
				</tr>
		";
		
		$NRow = 1;
		if($query->row()){
			foreach($query->result() as $row){$i++;
				$bgcolor="";
				$html .= "
					<tr class='trow' seq=".$NRow." style='".$bgcolor."'>
						<td class='getit' seq=".$NRow++."  style='cursor:pointer;text-align:center;'
						CONTNO	= '".str_replace(chr(0),'',$row->CONTNO)."' 
						LOCAT	= '".str_replace(chr(0),'',$row->LOCAT)."' 
						CUSCOD	= '".str_replace(chr(0),'',$row->CUSCOD)."' 
						CUSNAME	= '".($row->SNAM.$row->NAME1.' '.$row->NAME2)."'
						REGNO	= '".$row->REGNO."'
						STRNO	= '".str_replace(chr(0),'',$row->STRNO)."' 
						TOTPRC	= '".number_format($row->TOTPRC,2)."'
						SMPAY	= '".number_format($row->SMPAY,2)."'
						TOTBAL	= '".number_format($row->TOTBAL,2)."'
						EXP_AMT	= '".number_format($row->EXP_AMT,2)."'
						BOOKVAL	= '".number_format($row->BOOKVAL,2)."'
						BOOKVAT = '".number_format($row->BOOKVAT,2)."'
						COST	= '".number_format($row->N_NETCST,2)."'
						COSTVAT	= '".number_format($row->N_NETVAT,2)."'
						DATECHG	= '".$row->DATECHG."'
						RCVLOCAT= '".str_replace(chr(0),'',$row->LOCAT)."' 
						STDPRC	= '".number_format($row->STDPRC,2)."'
						N_GCODE	= '".str_replace(chr(0),'',$row->N_GCODE)."' 
						GDESC	= '".str_replace(chr(0),'',$row->GDESC)."' 
						MEMO1	= '".$row->MEMO1."'
						><b>เลือก</b></td>
						<td align='center'>".$row->LOCAT."</td>
						<td>".$row->CONTNO."</td>
						<td>".$row->CUSCOD."</td>
						<td>".($row->SNAM.$row->NAME1.' '.$row->NAME2)."</td>
						<td>".$row->STRNO."</td>
						<td align='center'>".$row->SDATE."</td>
						<td align='center'>".$row->DATECHG."</td>
						<td align='right'>".number_format($row->TOTPRC,2)."</td>
						<td align='right'>".number_format($row->SMPAY,2)."</td>
						<td align='right'>".number_format($row->TOTBAL,2)."</td>
						<td align='right'>".number_format($row->EXP_AMT,2)."</td>
						<td align='right'>".number_format($row->BOOKVAL,2)."</td>
						<td align='right'>".number_format($row->BOOKVAT,2)."</td>
						<td align='right'>".number_format($row->N_NETCST,2)."</td>
						<td align='right'>".number_format($row->N_NETVAT,2)."</td>
						<td align='right'>".number_format($row->N_NETTOT,2)."</td>
					</tr>
				";	
			}
		}
		
		$No = 1;
		if($query->row()){
			foreach($query->result() as $row){
				$report .= "
					<tr class='trow' seq=".$No.">
						<td style='mso-number-format:\"\@\";text-align:center;'>".$No++."</td>
						<td style='mso-number-format:\"\@\";text-align:center;'>".$row->LOCAT."</td>
						<td style='mso-number-format:\"\@\";text-align:center;'>".$row->CONTNO."</td>
						<td style='mso-number-format:\"\@\";text-align:center;'>".$row->CUSCOD."</td>
						<td style='mso-number-format:\"\@\";'>".($row->SNAM.$row->NAME1.' '.$row->NAME2)."</td>
						<td style='mso-number-format:\"\@\";'>".$row->STRNO."</td>
						<td style='mso-number-format:\"\@\";text-align:center;'>".$row->SDATE."</td>
						<td style='mso-number-format:\"\@\";text-align:center;'>".$row->DATECHG."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->TOTPRC,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->SMPAY,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->TOTBAL,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->EXP_AMT,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->BOOKVAL,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->BOOKVAT,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->N_NETCST,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->N_NETVAT,2)."</td>
						<td style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->N_NETTOT,2)."</td>
					</tr>
				";	
			}
		}
		
		if($query2->row()){
			foreach($query2->result() as $row){
				$sumreport .= "
					<tr class='trow'>
						<th colspan='8' align='center'>".$row->Total."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumTOTPRC,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumSMPAY,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumTOTBAL,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumEXP_AMT,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumBOOKVAL,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumBOOKVAT,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumN_NETCST,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumN_NETVAT,2)."</th>
						<th style='mso-number-format:\"\#\,\#\#0.00\";text-align:right;'>".number_format($row->sumN_NETTOT,2)."</th>
					</tr>
				";	
			}
		}
		
		if($i>0){
			$html = "
				<div id='table-fixed-ExchangeCar' class='col-sm-12' style='height:100%;width:100%;overflow:auto;'>
					<table id='table-ExchangeCar' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='calc(100% - 1px)'>
						<thead>
							".$head."
						</thead>	
						<tbody>
							".$html."
						</tbody>
					</table>
				</div>
			";
		}else{
			$html="<font style='color:red;'>ไม่มีข้อมูล</font>";
		}
		
		$report = "
			<div id='table-fixed-ExchangeCar2' class='col-sm-12' style='height:100%;width:100%;overflow:auto;'>
				<table id='table-ExchangeCar2' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='100%'>
					<thead>
						<tr>
							<th colspan='17' style='font-size:12pt;border:0px;text-align:center;'>รายงานการแลกเปลี่ยนรถ</th>
						</tr>
						<tr>
							<td colspan='17' style='border:0px;text-align:center;'>".$rpcond." ออกรายงาน ณ วันที่ ".$this->today('today')."</td>
						</tr>
						".$head."
					</thead>	
					<tbody>
						".$report."
						".$sumreport."
					</tbody>
				</table>
			</div>
		";
		
		$response = array("html"=>$html, "report"=>$report);
		echo json_encode($response);
	}
	
	function conditiontopdf(){
		$data = array();
		$data[] = urlencode($_REQUEST["LOCAT1"].'||'.$_REQUEST["CONTNO1"].'||'.$_REQUEST["FROMDATECHG"].'||'.$_REQUEST["TODATECHG"]);
		echo json_encode($this->generateData($data,"encode"));
	}
	
	function pdf(){
		$data = array();
		$data[] = $_GET["condpdf"];
		$arrs = $this->generateData($data,"decode");
		$arrs[0] = urldecode($arrs[0]);

		$tx = explode("||",$arrs[0]);
		$locat = $tx[0];
		$contno = $tx[1];
		$fromdate = $tx[2];
		$todate = $tx[3];

		$cond = "";
		$rpcond = "";
		if($locat != ""){
			$cond .= " and LOCAT = '".$locat."'";
			$rpcond .= "  สาขา ".$locat;
		}
		
		if($contno != ""){
			$cond .= " and CONTNO like '%".$contno."%' collate thai_cs_as";
		}
		
		if($fromdate != ""){
			$cond .= " and YDATE >= '".$this->Convertdate(1,$fromdate)."'";
			$rpcond .= "  จากวันที่แลกเปลี่ยน ".$fromdate;
		}
		
		if($todate != ""){
			$cond .= " and YDATE <= '".$this->Convertdate(1,$todate)."'";
			$rpcond .= "  ถึงวันที่ ".$todate;
		}
		
		$sql = "
				select ".($cond == '' ? 'top 100':'')." LOCAT, CONTNO, CUSCOD, SNAM, NAME1, NAME2, STRNO, isnull(REGNO,'') as REGNO, convert(nvarchar,dateadd(year,543,SDATE),103) as SDATE, 
				convert(nvarchar,dateadd(year,543,YDATE),103) as DATECHG, TOTPRC, SMPAY, TOTBAL, EXP_AMT, BOOKVAL, BOOKVAT, N_NETCST, N_NETVAT, N_NETTOT, STDPRC, 
				N_GCODE, GDESC, a.MEMO1
				from {$this->MAuth->getdb('ARCHAG')} a
				left join SETGROUP b on a.N_GCODE = b.GCODE 
				where 1=1 ".$cond."
				order by YDATE desc, LOCAT
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$sql2 = "
				select 'รวมทั้งสิน' as Total, sum(TOTPRC) as sumTOTPRC, sum(SMPAY) as sumSMPAY, sum(TOTBAL) as sumTOTBAL, sum(EXP_AMT) as sumEXP_AMT, sum(BOOKVAL) as sumBOOKVAL, sum(BOOKVAT) as sumBOOKVAT, 
				sum(N_NETCST) as sumN_NETCST, sum(N_NETVAT) as sumN_NETVAT, sum(N_NETTOT) as sumN_NETTOT
				from {$this->MAuth->getdb('ARCHAG')} 
				where 1=1 ".$cond."
		";
		//echo $sql2; exit;
		$query2 = $this->db->query($sql2);
		
		$head = ""; $html = ""; $report = ""; $sumreport = ""; $i=0; $ii=0;
	
		$head = "<tr>
				<th style='text-align:left;border-bottom:0.1px solid black;'>#</th>
				<th style='text-align:left;border-bottom:0.1px solid black;'>สาขา</th>
				<th style='text-align:left;border-bottom:0.1px solid black;'>เลขที่สัญญา</th>
				<th style='text-align:left;border-bottom:0.1px solid black;'>รหัสลูกค้า</th>
				<th style='text-align:left;border-bottom:0.1px solid black;'>ชื่อ - นามสกุล</th>
				<th style='text-align:left;border-bottom:0.1px solid black;'>เลขตัวถัง</th>
				<th style='text-align:left;border-bottom:0.1px solid black;'>วันที่ขาย</th>
				<th style='text-align:left;border-bottom:0.1px solid black;'>วันที่แลกเปลี่ยน</th>
				<th style='text-align:right;border-bottom:0.1px solid black;'>ราคาขาย</th>
				<th style='text-align:right;border-bottom:0.1px solid black;'>ชำระแล้ว</th>
				<th style='text-align:right;border-bottom:0.1px solid black;'>คงเหลือ</th>
				<th style='text-align:right;border-bottom:0.1px solid black;'>ค้างชำระ</th>
				<th style='text-align:right;border-bottom:0.1px solid black;'>ราคาตามบัญชี</th>
				<th style='text-align:right;border-bottom:0.1px solid black;'>ภาษีตามบัญชี</th>
				<th style='text-align:right;border-bottom:0.1px solid black;'>ราคาประเมิน</th>
				<th style='text-align:right;border-bottom:0.1px solid black;'>ภาษี</th>
				<th style='text-align:right;border-bottom:0.1px solid black;'>รวมราคาประเมิน</th>
				</tr>
		";
		
		$No = 1;
		if($query->row()){
			foreach($query->result() as $row){
				$report .= "
					<tr class='trow' seq=".$No.">
						<td style='width:20px;'>".$No++."</td>
						<td style='width:35px;'>".$row->LOCAT."</td>
						<td style='width:75px;'>".$row->CONTNO."</td>
						<td style='width:75px;'>".$row->CUSCOD."</td>
						<td style='width:125px;'>".($row->SNAM.$row->NAME1.' '.$row->NAME2)."</td>
						<td style='width:110px;'>".$row->STRNO."</td>
						<td style='width:50px;'>".$row->SDATE."</td>
						<td style='width:50px;'>".$row->DATECHG."</td>
						<td style='text-align:right;width:75px;'>".number_format($row->TOTPRC,2)."</td>
						<td style='text-align:right;width:75px;'>".number_format($row->SMPAY,2)."</td>
						<td style='text-align:right;width:75px;'>".number_format($row->TOTBAL,2)."</td>
						<td style='text-align:right;width:75px;'>".number_format($row->EXP_AMT,2)."</td>
						<td style='text-align:right;width:75px;'>".number_format($row->BOOKVAL,2)."</td>
						<td style='text-align:right;width:75px;'>".number_format($row->BOOKVAT,2)."</td>
						<td style='text-align:right;width:75px;'>".number_format($row->N_NETCST,2)."</td>
						<td style='text-align:right;width:75px;'>".number_format($row->N_NETVAT,2)."</td>
						<td style='text-align:right;width:75px;'>".number_format($row->N_NETTOT,2)."</td>
					</tr>
				";	
			}
		}
		
		if($query2->row()){
			foreach($query2->result() as $row){
				$sumreport .= "
					<tr class='trow bor' style='background-color:#ebebeb;'>
						<th colspan='8' align='center'>".$row->Total."</th>
						<th style='text-align:right;'>".number_format($row->sumTOTPRC,2)."</th>
						<th style='text-align:right;'>".number_format($row->sumSMPAY,2)."</th>
						<th style='text-align:right;'>".number_format($row->sumTOTBAL,2)."</th>
						<th style='text-align:right;'>".number_format($row->sumEXP_AMT,2)."</th>
						<th style='text-align:right;'>".number_format($row->sumBOOKVAL,2)."</th>
						<th style='text-align:right;'>".number_format($row->sumBOOKVAT,2)."</th>
						<th style='text-align:right;'>".number_format($row->sumN_NETCST,2)."</th>
						<th style='text-align:right;'>".number_format($row->sumN_NETVAT,2)."</th>
						<th style='text-align:right;'>".number_format($row->sumN_NETTOT,2)."</th>
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
		
		$content = "
			<table class='wf' style='font-size:7pt;height:700px;border-collapse:collapse;line-height:23px;overflow:wrap;vertical-align:text-top;'>
				<tbody>
					<tr>
						<th colspan='17' style='font-size:10pt;'>รายงานการแลกเปลี่ยนรถ </th>
					</tr>
					<tr>
						<td colspan='17' style='font-size:8pt;height:35px;border-bottom:0.1px solid black;text-align:center;'>".$rpcond."</td>
					</tr>
					".$head."
					".$report."
					".$sumreport."
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
			<div class='wf pf' style='top:715;left:960;font-size:6pt;'>".date('d/m/').(date('Y')+543)." ".date('H:i')." หน้า {PAGENO} / {nbpg}</div>
		";
		//<div class='wf pf' style='top:1050;left:580;'>{DATE j-m-Y H:s}  {PAGENO} / {nbpg}</div>
		//$mpdf->AddPage('L');	
		$mpdf->SetHTMLHeader($head);	
		$mpdf->WriteHTML($content);	
		$mpdf->Output();
		
	}
	
	function Save_exchangecar(){
		$CONTNO 	= $_REQUEST["CONTNO"];
		$STRNO 		= $_REQUEST["STRNO"];
		$GCODENEW	= str_replace(chr(0),'',$_REQUEST["GCODENEW"]);
		$DATECHG	= $this->Convertdate(1,$_REQUEST["DATECHG"]);
		$BOOKVAL	= str_replace(',','',$_REQUEST["BOOKVAL"]);
		$SALEVAT	= str_replace(',','',$_REQUEST["SALEVAT"]);
		$COST		= str_replace(',','',$_REQUEST["COST"]);
		$COSTVAT	= str_replace(',','',$_REQUEST["COSTVAT"]);
		$SALENEW	= str_replace(',','',$_REQUEST["SALENEW"]);
		$MEMO		= $_REQUEST["MEMO"];
		$USERID		= $this->sess["USERID"];
		
		if($COSTVAT == ''){
			$COSTVAT = 0;
		}
		
		if($MEMO == ''){
			$MEMO = 'NULL';
		}else{
			$MEMO = "'".$MEMO."'";
		}
		//echo $MEMO; exit;

		$sql = "
			if OBJECT_ID('tempdb..#AddCHANGETemp') is not null drop table #AddCHANGETemp;
			create table #AddCHANGETemp (id varchar(20),contno varchar(20),msg varchar(max));
			
			begin tran AddCHANGETemp
			begin try
			
				declare @CONTNO varchar(20) 	= '".$CONTNO."'
				declare @STRNO varchar(max) 	= '".$STRNO."'
				declare @GCODEold varchar(12) 	= (select GCODE from {$this->MAuth->getdb('INVTRAN')} where CONTNO = '".$CONTNO."');
				declare @STATold varchar(2) 	= (select STAT from {$this->MAuth->getdb('INVTRAN')} where CONTNO = '".$CONTNO."');
				
				if (@STATold = 'N') and (@GCODEold = '".$GCODENEW."')
				begin
					insert into #AddCHANGETemp select 'W','','กรุณาเปลี่ยนประเภทรถ';
				end
				else
				begin
					-- บันทึกลง ARCHAG
					insert into {$this->MAuth->getdb('ARCHAG')}
					select 
					a.CRLOCAT,	a.CONTNO, b.CUSCOD,	c.SNAM, c.NAME1, c.NAME2, a.STRNO, a.REGNO, a.SDATE, b.TOTPRC, b.NPRICE, b.VATPRC, b.SMPAY, b.SMCHQ,
					b.TOTPRC-b.SMPAY-b.SMCHQ as TOTBAL, b.TOTPRC-b.SMPAY-b.SMCHQ as NETBAL, 0 as VATBAL, b.EXP_AMT, ".$BOOKVAL." as BOOKVAL, 
					".$SALEVAT." as BOOKVAT, ".$COST." as N_NETCST, ".$COSTVAT." as N_NETVAT, ".($COST+$COSTVAT)." as N_NETTOT, '".$DATECHG."' as YDATE, a.CRLOCAT as YLOCAT, 
					b.BILLCOLL, b.CHECKER, 'C' as FLAG, ".$MEMO." as MEMO1, d.NPROF as BALPROF, b.VKANG as BALVAT, b.NPROFIT, a.GCODE as O_GCODE, GETDATE() as INPDT, 
					0 as SMPRIN_EFF, 0 as BALPRIN_EFF, 0 as SMPROF_EFF, 0 as BALPROF_EFF, 0 as EXP_VAT, 0 as EXP_NET, 0 as EXP_EFF, '' as PROF_METHOD, '".$GCODENEW."' as N_GCODE,
					0 as VATRT, 0 as TKANG, 0 as NKANG, 0 as VKANG, 0 as NCSHPRC, 0 as VATBALDUE, 0 as SMNETPAY, 0 as SMVATPAY, 0 as SMPRIN_SYD, 0 as SMPROF_SYD, 0 as SMPRIN_STR, 0 as SMPROF_STR,
					0 as BALPROF_SYD, 0 as BALPROF_STR, 0 as BALPRIN_SYD, 0 as BALPRIN_STR, '".$SALENEW."' as STDPRC, '' as LOSTCOD, '' as VOUCHER, null as VOUCHDT, '".$USERID."' as USERID, 
					null as POSTDT, null as LOSTDT, '' as Y_USER, '' as TYPHOLD
					from {$this->MAuth->getdb('INVTRAN')} a
					left join {$this->MAuth->getdb('ARMAST')} b on a.CONTNO = b.CONTNO 
					left join {$this->MAuth->getdb('CUSTMAST')} c on b.CUSCOD = c.CUSCOD 
					left join (
						select CONTNO, SUM(NPROF) as NPROF from {$this->MAuth->getdb('ARPAY')} where CONTNO in ('".$CONTNO."') and DDATE >= '".$DATECHG."' group by CONTNO
					)d on a.CONTNO = d.CONTNO 
					where a.CONTNO in ('".$CONTNO."') 

					-- บันทึกลง H
					insert into {$this->MAuth->getdb('HINVTRAN')} 	select * from {$this->MAuth->getdb('INVTRAN')} 	where CONTNO = '".$CONTNO."' 
					insert into {$this->MAuth->getdb('HARMAST')}	select * from {$this->MAuth->getdb('ARMAST')} 	where CONTNO = '".$CONTNO."' 
					insert into {$this->MAuth->getdb('HARPAY')} 	select * from {$this->MAuth->getdb('ARPAY')} 	where CONTNO = '".$CONTNO."' 
					insert into {$this->MAuth->getdb('HARMGAR')} 	select * from {$this->MAuth->getdb('ARMGAR')} 	where CONTNO = '".$CONTNO."'  collate thai_cs_as

					-- อัพเดท INVTRAN
					update {$this->MAuth->getdb('INVTRAN')}	
					set		RECVDT	= '".$DATECHG."',	
							GCODE	= '".$GCODENEW."',
							STAT	= 'O',
							CRCOST  = ".$COST.",
							DISCT	= 0,
							VATRT 	= 0,
							NADDCOST= 0,	
							VADDCOST= 0,	
							TADDCOST= 0,
							NETCOST	= ".$COST.",
							CRVAT	= ".$COSTVAT.",
							TOTCOST	= ".($COST+$COSTVAT).",
							STDPRC	= ".$SALENEW.",
							SDATE	= null,
							PRICE	= 0,
							TSALE	= '',
							CONTNO	= '',
							CURSTAT	= '',
							CRDTXNO	= NULL, 
							CRDAMT	= NULL,
							RESVNO	= '',
							RESVDT	= NULL,
							FLAG	= 'D',
							YSTAT	= 'C',
							MEMO1	= NULL
					where STRNO = '".$STRNO."'

					-- ลบ ARMAST, ARPAY
					delete {$this->MAuth->getdb('ARMAST')} 	where CONTNO = '".$CONTNO."' 
					delete {$this->MAuth->getdb('ARPAY')} 	where CONTNO = '".$CONTNO."' 
					delete {$this->MAuth->getdb('ARMGAR')} 	where CONTNO = '".$CONTNO."' collate thai_cs_as
					
					--อัพดท HARMAST
					update {$this->MAuth->getdb('HARMAST')} set CLOSDT = '".$DATECHG."' where CONTNO = '".$CONTNO."' 
					
					insert into #AddCHANGETemp select 'S',@CONTNO,'บันทึกรถแลกเปลี่ยน เลขตัวถัง '+@STRNO+' เลขที่สัญญาเดิม '+@CONTNO+' เรียบร้อย';
				end
					
				commit tran AddCHANGETemp;
			end try
			begin catch
				rollback tran AddCHANGETemp;
				insert into #AddCHANGETemp select 'E','',ERROR_MESSAGE();
			end catch
		";
		//echo $sql; exit;
		
		$this->db->query($sql);
		$sql = "select * from #AddCHANGETemp";
		$query = $this->db->query($sql);
	  
		if($query->row()){
			foreach($query->result() as $row){
				$response["status"] = $row->id;
				$response["contno"] = $row->contno;
				$response["msg"] = $row->msg;
			}
		}else{
			$response["status"] = false;
			$response["contno"] = '';
			$response["msg"] = 'ผิดพลาดไม่สามารถบันทึกรถแลกเปลี่ยนได้ โปรดติดต่อฝ่ายไอที';
		}
		
		echo json_encode($response);
	}
	
	function Edit_exchangecar(){
		$CONTNO 	= $_REQUEST["CONTNO"];
		$CUSCOD 	= $_REQUEST["CUSCOD"];
		$STRNO 		= $_REQUEST["STRNO"];
		$GCODENEW	= str_replace(chr(0),'',$_REQUEST["GCODENEW"]);
		$DATECHG	= $this->Convertdate(1,$_REQUEST["DATECHG"]);
		$BOOKVAL	= str_replace(',','',$_REQUEST["BOOKVAL"]);
		$SALEVAT	= str_replace(',','',$_REQUEST["SALEVAT"]);
		$COST		= str_replace(',','',$_REQUEST["COST"]);
		$COSTVAT	= str_replace(',','',$_REQUEST["COSTVAT"]);
		$SALENEW	= str_replace(',','',$_REQUEST["SALENEW"]);
		$MEMO		= $_REQUEST["MEMO"];
		
		if($COSTVAT == ''){
			$COSTVAT = 0;
		}
		
		$sql = "
			if OBJECT_ID('tempdb..#EditCHGTemp') is not null drop table #EditCHGTemp;
			create table #EditCHGTemp (id varchar(20),contno varchar(20),msg varchar(max));
			
			begin tran EditCHGTemp
			begin try
				
				declare @CONTNO varchar(max) = '".$CONTNO."';
				declare @STRNO varchar(max) = '".$STRNO."';
				
				if(select SDATE from {$this->MAuth->getdb('INVTRAN')} where STRNO = @STRNO) is null
				begin
					
					update {$this->MAuth->getdb('ARCHAG')}	
					set BOOKVAL = ".$BOOKVAL.", BOOKVAT = ".$SALEVAT.", N_NETCST = ".$COST.", N_NETVAT = ".$COSTVAT.", N_NETTOT = ".($COST+$COSTVAT).",
					YDATE = '".$DATECHG."', N_GCODE = '".$GCODENEW."', STDPRC = '".$SALENEW."', MEMO1 = '".$MEMO."'
					where CONTNO like '%".$CONTNO."%' and CUSCOD like '%".$CUSCOD."%' and STRNO like '%".$STRNO."%'

					update {$this->MAuth->getdb('INVTRAN')}	
					set		RECVDT	= '".$DATECHG."',	
							GCODE	= '".$GCODENEW."',
							CRCOST  = ".$COST.",
							NETCOST	= ".$COST.",
							CRVAT	= ".$COSTVAT.",
							TOTCOST	= ".($COST+$COSTVAT).",
							STDPRC	= ".$SALENEW."
					where STRNO = '".$STRNO."'
					
					insert into {$this->MAuth->getdb('hp_UserOperationLog')} (userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
					values ('".$this->sess["IDNo"]."','SYS05::บันทึกรถแลกเปลี่ยน (แก้ไข)',' ".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');

					insert into #EditCHGTemp select 'S',@CONTNO,'แก้ไขรายการแลกเปลี่ยนรถ เลขที่สัญญา '+@CONTNO+' เรียบร้อย';
					
				end
				else
				begin
					insert into #EditCHGTemp select 'E',@CONTNO,'ไม่สามารถแก้ไขได้ เนื่องจากมีการขาย เลขตัวถัง '+@STRNO+' แล้ว';
				end

				commit tran EditCHGTemp;
			end try
			begin catch
				rollback tran EditCHGTemp;
				insert into #EditCHGTemp select 'E','',ERROR_MESSAGE();
			end catch
		";
		//echo $sql; exit;
		
		$this->db->query($sql);
		$sql = "select * from #EditCHGTemp";
		$query = $this->db->query($sql);
	  
		if($query->row()){
			foreach($query->result() as $row){
				$response["status"] = $row->id;
				$response["contno"] = $row->contno;
				$response["msg"] = $row->msg;
			}
		}else{
			$response["status"] = false;
			$response["contno"] = '';
			$response["msg"] = 'ผิดพลาดไม่สามารถแก้ไขรายการแลกเปลี่ยนรถได้ โปรดติดต่อฝ่ายไอที';
		}
		
		echo json_encode($response);
	}
	
	function Delete_exchangecar(){
		$CONTNO	= $_REQUEST["CONTNO"];
		$CUSCOD	= $_REQUEST["CUSCOD"];
		$STRNO	= $_REQUEST["STRNO"];
		//$CUSNAME= $_REQUEST["CUSNAME"];
		
		$sql = "
			if OBJECT_ID('tempdb..#DelCHGTemp') is not null drop table #DelCHGTemp;
			create table #DelCHGTemp (id varchar(20),contno varchar(20),msg varchar(max));
			
			begin tran DelCHGTemp
			begin try
				
				declare @CONTNO varchar(max) = '".$CONTNO."';
				declare @STRNO varchar(max) = '".$STRNO."';
				
				if(select SDATE from {$this->MAuth->getdb('INVTRAN')} where STRNO = @STRNO) is null
				begin
					
					--UPDATE INVTRAN
					delete {$this->MAuth->getdb('INVTRAN')} where STRNO = @STRNO and SDATE is null
					insert {$this->MAuth->getdb('INVTRAN')} select * from {$this->MAuth->getdb('HINVTRAN')} where CONTNO = @CONTNO and STRNO = @STRNO

					--INSERT ARMAST, ARPAY
					insert into {$this->MAuth->getdb('ARMAST')} 	select * from {$this->MAuth->getdb('HARMAST')} 	where CONTNO = @CONTNO and STRNO = @STRNO
					insert into {$this->MAuth->getdb('ARPAY')} 		select * from {$this->MAuth->getdb('HARPAY')} 	where CONTNO = @CONTNO 
					insert into {$this->MAuth->getdb('ARMGAR')} 	select * from {$this->MAuth->getdb('HARMGAR')} 	where CONTNO = @CONTNO 

					--ลบ H
					delete {$this->MAuth->getdb('HINVTRAN')} 	where CONTNO = @CONTNO and STRNO = @STRNO
					delete {$this->MAuth->getdb('HARMAST')} 	where CONTNO = @CONTNO and STRNO = @STRNO
					delete {$this->MAuth->getdb('HARPAY')} 		where CONTNO = @CONTNO 
					delete {$this->MAuth->getdb('HARMGAR')} 	where CONTNO = @CONTNO 
					
					--ลบ ARCHAG
					delete {$this->MAuth->getdb('ARCHAG')} where CONTNO = @CONTNO and STRNO = @STRNO
					
					insert into {$this->MAuth->getdb('hp_UserOperationLog')} (userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
					values ('".$this->sess["IDNo"]."','SYS05::บันทึกรถแลกเปลี่ยน (ลบ)',' ".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
					
					insert into #DelCHGTemp select 'S',@CONTNO,'ลบรายการแลกเปลี่ยนรถ เลขที่สัญญา '+@CONTNO+' เรียบร้อย';
					
				end
				else
				begin
					insert into #DelCHGTemp select 'E',@CONTNO,'ไม่สามารถลบได้ เนื่องจากมีการขาย เลขตัวถัง '+@STRNO+' แล้ว';
				end

				commit tran DelCHGTemp;
			end try
			begin catch
				rollback tran DelCHGTemp;
				insert into #DelCHGTemp select 'E','',ERROR_MESSAGE();
			end catch
		";
		//echo $sql; exit;
		
		$this->db->query($sql);
		$sql = "select * from #DelCHGTemp";
		$query = $this->db->query($sql);
	  
		if($query->row()){
			foreach($query->result() as $row){
				$response["status"] = $row->id;
				$response["contno"] = $row->contno;
				$response["msg"] = $row->msg;
			}
		}else{
			$response["status"] = false;
			$response["contno"] = '';
			$response["msg"] = 'ผิดพลาดไม่สามารถลบรายการแลกเปลี่ยนรถได้ โปรดติดต่อฝ่ายไอที';
		}
		
		echo json_encode($response);
	}
	
}