<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//BEE+
class HoldtoOldcar extends MY_Controller {
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
								จากวันที่เปลี่ยน
								<input type='text' id='FROMDATECHG' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' placeholder='จากวันที่แลกเปลี่ยน' >
							</div>
						</div>
						<div class='col-sm-2 col-xs-2'>	
							<div class='form-group'>
								ถึงวันที่เปลี่ยน
								<input type='text' id='TODATECHG' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' placeholder='ถึงวันที่แลกเปลี่ยน' >
							</div>
						</div>
						<div class='col-sm-2 col-xs-2'>	
							<div class='form-group'>
								เลขตัวถัง
								<input type='text' id='STRNO1' class='form-control input-sm' placeholder='เลขตัวถัง'>
							</div>
						</div>
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
					<div id='resultt_HoldtoOldcar' style='background-color:white;'></div>
				</div>
			</div>
		";
		
		$html.= "<script src='".base_url('public/js/SYS05/HoldtoOldcar.js')."'></script>";
		echo $html;
	}
	
	//ฟอร์มบันทึกลูกหนี้อื่น
	function getfromHoldtoOldcar(){
		$level	= $_REQUEST["level"];
		//$locat = $this->sess['branch'];

		$html = "
			<div class='b_HoldtoOldcar' style='width:100%;height:calc(100vh - 85px);overflow:auto;background-color:white;'>
				<div style='float:left;height:100%;overflow:auto;' class='col-sm-10 col-xs-10 col-sm-offset-1'>
					<div class='form-group col-sm-12 col-xs-12' style='border:0.1px solid #f0f0f0;'>
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
					
					<div class='form-group col-sm-12 col-xs-12' style='border:0.1px solid #f0f0f0;'>
						<div class='form-group col-sm-10 col-xs-10 col-sm-offset-1'>
							<br>
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
										สถานที่เก็บปัจจุบัน
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
								<div class='col-sm-3 col-xs-3'>	
									<div class='form-group' >
										เหตุที่บอกเลิกสัญญา
										<select id='TYPHOLD' class='form-control input-sm' data-placeholder='เหตุที่บอกเลิกสัญญา' ></select>
									</div>
								</div>
								<div class='col-sm-3 col-xs-3'>	
									<div class='form-group' >
										พนักงานที่ยึดรถ
										<select id='Y_USER' class='form-control input-sm' data-placeholder='พนักงานที่ยึดรถ' ></select>
									</div>
								</div>
								<div class=' col-sm-6 col-xs-6'>	
									<div class='form-group'>
										หมายเหตุ
										<textarea type='text' id='MEMO' rows='1' cols='20' class='form-control input-sm' style='height:30px;font-size:10.5pt'></textarea>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class='row'>
						<div class=' col-sm-2 col-sm-offset-4'>	
							<div class='form-group'>
								<br>
								<button id='btnsave_holdtooldcar' class='btn btn-primary btn-block'><span class='glyphicon glyphicon-floppy-disk'> บันทึก</span></button>
							</div>
						</div>
						<div class=' col-sm-2'>	
							<div class='form-group'>
								<br>
								<input type='button' id='btndel_holdtooldcar' class='btn btn-danger btn-sm' value='ลบ' style='width:100%'>
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
				select b.CONTNO, b.CRLOCAT, isnull(b.REGNO,'') as REGNO, b.STRNO , b.STAT, b.GCODE, d.GDESC, c.SNAM, c.NAME1, c.NAME2, a.CUSCOD, a.TOTPRC, a.SMPAY+a.SMCHQ as SMPAY, 
				a.TOTPRC - a.SMPAY - a.SMCHQ as BALANC, a.EXP_AMT, a.NKANG+a.TOTDWN-a.SMPAY-a.SMCHQ as BOOKVAL, a.VKANG, b.STDPRC, a.VATRT, a.BILLCOLL, e.NAME
				from {$this->MAuth->getdb('ARMAST')} a
				left join {$this->MAuth->getdb('INVTRAN')} b on a.CONTNO = b.CONTNO 
				left join {$this->MAuth->getdb('CUSTMAST')} c on a.CUSCOD = c.CUSCOD 
				left join {$this->MAuth->getdb('SETGROUP')} d on b.GCODE = d.GCODE
				left join {$this->MAuth->getdb('OFFICER')} e on a.BILLCOLL = e.CODE
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
				$response["BILLCOLL"] 	= str_replace(chr(0),'',$row->BILLCOLL);
				$response["NAME"] 		= $row->NAME;
			}
		}
		
		echo json_encode($response);
	}
	
	function search(){
		$LOCAT1	= $_REQUEST["LOCAT1"];
		$STRNO1	= $_REQUEST["STRNO1"];
		$FROMDATECHG = $_REQUEST["FROMDATECHG"];
		$TODATECHG = $_REQUEST["TODATECHG"];
		
		$cond = "";
		if($LOCAT1 != ""){
			$cond .= " and a.LOCAT = '".$LOCAT1."'";
		}
		
		if($STRNO1 != ""){
			$cond .= " and a.STRNO like '%".$STRNO1."%' collate thai_cs_as";
		}
		
		if($FROMDATECHG != ""){
			$cond .= " and a.YDATE >= '".$this->Convertdate(1,$FROMDATECHG)."'";
		}
		
		if($TODATECHG != ""){
			$cond .= " and a.YDATE <= '".$this->Convertdate(1,$TODATECHG)."'";
		}
		
		$sql = "
			select ".($cond == '' ? 'top 100':'')." 
			a.CONTNO, a.LOCAT, isnull(a.REGNO,'') as REGNO, a.STRNO , b.GCODE, e.GDESC, a.SNAM, a.NAME1, a.NAME2, a.CUSCOD, a.TOTPRC, a.SMPAY+a.SMCHQ as SMPAY, 
			a.TOTPRC - a.SMPAY - a.SMCHQ as BALANC, a.EXP_AMT, a.BOOKVAL, a.BOOKVAT, a.VKANG, case when a.STDPRC = 0 then b.STDPRC else a.STDPRC end STDPRC, 
			a.VATRT, a.N_NETCST, a.N_NETVAT, a.BILLCOLL, f.NAME, convert(nvarchar,dateadd(year,543,a.YDATE),103) as YDATES, a.TYPHOLD, c.HOLDESC, a.MEMO1
			from {$this->MAuth->getdb('ARHOLD')} a
			left join {$this->MAuth->getdb('INVTRAN')} b on a.STRNO = b.STRNO
			left join {$this->MAuth->getdb('TYPHOLD')} c on a.TYPHOLD = c.HOLDCOD
			left join {$this->MAuth->getdb('SETGROUP')} e on b.GCODE = e.GCODE
			left join {$this->MAuth->getdb('OFFICER')} f on a.BILLCOLL = f.CODE
			where 1 = 1 ".$cond." 
			order by YDATE desc 
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		$NRow = 1; $No = 1;
		if($query->row()){
			foreach($query->result() as $row){
				$bgcolor="";
				//print_r($row->DESC1);
				$html .= "
					<tr class='trow' seq=".$NRow." style='".$bgcolor."'>
						<td class='getit' seq=".$NRow++."  style='cursor:pointer;text-align:center;'
						CONTNO 		= '".$row->CONTNO."' 
						CRLOCAT 	= '".$row->LOCAT."'
						CUSNAME		= '".$row->SNAM.$row->NAME1.' '.$row->NAME2."'
						CUSCOD 		= '".$row->CUSCOD."'
						REGNO 		= '".$row->REGNO."'
						STRNO		= '".str_replace(chr(0),'',$row->STRNO)."'
						TOTPRC 		= '".number_format($row->TOTPRC,2)."'
						SMPAY 		= '".number_format($row->SMPAY,2)."'
						TOTBAL 		= '".number_format($row->BALANC,2)."'
						EXP_AMT 	= '".number_format($row->EXP_AMT,2)."'
						BOOKVAL		= '".number_format($row->BOOKVAL,2)."'
						BOOKVAT		= '".number_format($row->BOOKVAT,2)."'
						COST		= '".number_format($row->N_NETCST,2)."'
						COSTVAT		= '".number_format($row->N_NETVAT,2)."'
						VATPRC 		= '".number_format($row->VKANG,2)."'
						RCVLOCAT 	= '".$row->LOCAT."'
						STDPRC 		= '".number_format($row->STDPRC,2)."'
						GCODE 		= '".str_replace(chr(0),'',$row->GCODE)."'
						GDESC		= '".str_replace(chr(0),'',$row->GDESC)."'
						VATRT  		= '".number_format($row->VATRT)."'
						BILLCOLL 	= '".str_replace(chr(0),'',$row->BILLCOLL)."'
						NAME 		= '".str_replace(chr(0),'',$row->NAME)."'
						YDATE 		= '".$row->YDATES."'
						TYPHOLD 	= '".str_replace(chr(0),'',$row->TYPHOLD)."'
						HOLDESC 	= '".str_replace(chr(0),'',$row->HOLDESC)."'
						MEMO1 		= '".$row->MEMO1."'
						>เลือก</td>
						<td>".$row->CONTNO."</td>
						<td>".$row->LOCAT."</td>
						<td>".$row->SNAM.$row->NAME1.' '.$row->NAME2."</td>
						<td>".$row->STRNO."</td>
						<td>".$row->YDATES."</td>
						<td>".$row->HOLDESC."</td>
					</tr>
				";	
			}
		}
		
		$html = "
			<div id='table-fixed-holdtooldcar' class='col-sm-12' style='height:100%;width:100%;overflow:auto;'>
				<table id='table-holdtooldcar' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='calc(100% - 1px)'>
					<thead>
						<tr>
							<th style='vertical-align:middle;'>#</th>
							<th style='vertical-align:middle;'>เลขที่สัญญา</th>
							<th style='vertical-align:middle;'>สัญญาสาขา</th>
							<th style='vertical-align:middle;'>ชื่อ-สุกล ลูกค้า</th>
							<th style='vertical-align:middle;'>เลขตัวถัง</th>
							<th style='vertical-align:middle;'>วันที่ยึดรถ</th>
							<th style='vertical-align:middle;'>เหตุที่บอกเลิกสัญญา</th>
						</tr>
					</thead>	
					<tbody>
						".$html."
					</tbody>
				</table>
			</div>
		";
		
		$response = array("html"=>$html);
		echo json_encode($response);
	}
	
	function Save_holdtooldcar(){
		$CONTNO 	= $_REQUEST["CONTNO"];
		$STRNO 		= $_REQUEST["STRNO"];
		$GCODENEW	= str_replace(chr(0),'',$_REQUEST["GCODENEW"]);
		$TYPHOLD	= str_replace(chr(0),'',$_REQUEST["TYPHOLD"]);
		$Y_USER		= str_replace(chr(0),'',$_REQUEST["Y_USER"]);
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
		
		if($Y_USER == ''){
			$Y_USER = 'NULL';
		}else{
			$Y_USER = "'".$Y_USER."'";
		}
		
		if($MEMO == ''){
			$MEMO = 'NULL';
		}else{
			$MEMO = "'".$MEMO."'";
		}
		//echo $MEMO; exit;

		$sql = "
			if OBJECT_ID('tempdb..#AddHOLDTemp') is not null drop table #AddHOLDTemp;
			create table #AddHOLDTemp (id varchar(20),contno varchar(20),msg varchar(max));
			
			begin tran AddHOLDTemp
			begin try
			
				declare @CONTNO varchar(20) 	= '".$CONTNO."'
				declare @STRNO varchar(max) 	= '".$STRNO."'
				declare @GCODEold varchar(12) 	= (select GCODE from {$this->MAuth->getdb('INVTRAN')} where CONTNO = '".$CONTNO."');
				declare @STATold varchar(2) 	= (select STAT from {$this->MAuth->getdb('INVTRAN')} where CONTNO = '".$CONTNO."');
				
				if (@STATold = 'N') and (@GCODEold = '".$GCODENEW."')
				begin
					insert into #AddHOLDTemp select 'W','','กรุณาเปลี่ยนประเภทรถ';
				end
				else
				begin
					-- บันทึกลง ARHOLD
					insert into {$this->MAuth->getdb('ARHOLD')}
					select 
					a.CRLOCAT,	a.CONTNO, b.CUSCOD,	c.SNAM, c.NAME1, c.NAME2, a.STRNO, a.REGNO, a.SDATE, b.TOTPRC, b.NPRICE, b.VATPRC, b.SMPAY, b.SMCHQ,
					b.TOTPRC-b.SMPAY-b.SMCHQ as TOTBAL, b.TOTPRC-b.SMPAY-b.SMCHQ as NETBAL, 0 as VATBAL, b.EXP_AMT, '".$BOOKVAL."' as BOOKVAL, '".$SALEVAT."' as BOOKVAT, 
					".$COST." as N_NETCST, ".$COSTVAT." as N_NETVAT, ".($COST+$COSTVAT)." as N_NETTOT, '".$DATECHG."' as YDATE, a.CRLOCAT as YLOCAT, b.BILLCOLL, 
					b.CHECKER, 'Y' FLAG, ".$MEMO." as MEMO1, '".$TYPHOLD."' as TYPHOLD, d.NPROF as BALPROF, b.VKANG as BALVAT, b.NPROFIT, a.GCODE as O_GCODE, 
					".$Y_USER." as Y_USER, GETDATE() as INPDT, 0 as SMPRIN_EFF, 0 as BALPRIN_EFF, 0 as SMPROF_EFF, 0 as BALPROF_EFF, 0 as EXP_VAT, 0 as EXP_NET, 
					0 as EXP_EFF, '' as PROF_METHOD, NULL as LOSTDT, '".$GCODENEW."' as N_GCODE, 0 as VATRT, 0 as TKANG, 0 as NKANG, 0 as VKANG, 0 as NCSHPRC, 
					0 as VATBALDUE, 0 as SMNETPAY, 0 as SMVATPAY, 0 as SMPRIN_SYD, 0 as SMPROF_SYD, 0 as SMPRIN_STR, 0 as SMPROF_STR, 0 as BALPROF_SYD, 0 as BALPROF_STR,
					0 as BALPRIN_SYD, 0 as BALPRIN_STR, '".$SALENEW."' as STDPRC, '' as LOSTCOD, '' as VOUCHER, null as VOUCHDT, '".$USERID."' as USERID, 
					null as POSTDT
					from {$this->MAuth->getdb('INVTRAN')} a
					left join {$this->MAuth->getdb('ARMAST')} b on a.CONTNO = b.CONTNO 
					left join {$this->MAuth->getdb('CUSTMAST')} c on b.CUSCOD = c.CUSCOD 
					left join (
						select CONTNO, convert(decimal(12,2),round(SUM(NPROF),2)) AS NPROF 
						from(
							select CONTNO, CASE WHEN PAYMENT > 0 THEN (NPROF/DAMT)*PAYMENT ELSE NPROF END AS  NPROF  
							from {$this->MAuth->getdb('ARPAY')}  WHERE CONTNO = @CONTNO and PAYMENT < DAMT
						)A
						group by CONTNO
					)d on a.CONTNO = d.CONTNO 
					where a.CONTNO = @CONTNO and a.STRNO = @STRNO

					-- บันทึกลง H
					insert into {$this->MAuth->getdb('HINVTRAN')} 	select * from {$this->MAuth->getdb('INVTRAN')} 	where CONTNO = @CONTNO 
					insert into {$this->MAuth->getdb('HARMAST')}	select * from {$this->MAuth->getdb('ARMAST')} 	where CONTNO = @CONTNO 
					insert into {$this->MAuth->getdb('HARPAY')} 	select * from {$this->MAuth->getdb('ARPAY')} 	where CONTNO = @CONTNO 
					insert into {$this->MAuth->getdb('HARMGAR')} 	select * from {$this->MAuth->getdb('ARMGAR')} 	where CONTNO = @CONTNO collate thai_cs_as

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
							NETCOST = ".($COST-$COSTVAT).",
							CRVAT	= ".$COSTVAT.",
							TOTCOST	= ".$COST.",
							STDPRC	= '".$SALENEW."',
							SDATE	= null,
							PRICE	= 0,
							BONUS	= 0,
							TSALE	= '',
							CONTNO	= '',
							CURSTAT	= '',
							CRDTXNO	= NULL, 
							CRDAMT	= NULL,
							RESVNO	= '',
							RESVDT	= NULL,
							FLAG	= 'D',
							YSTAT	= 'Y',
							JOBDATE	= '".$DATECHG."',
							MEMO1	= ''
					where STRNO = @STRNO

					-- ลบ ARMAST, ARPAY
					delete {$this->MAuth->getdb('ARMAST')} 	where CONTNO = @CONTNO 
					delete {$this->MAuth->getdb('ARPAY')} 	where CONTNO = @CONTNO 
					delete {$this->MAuth->getdb('ARMGAR')} 	where CONTNO = @CONTNO collate thai_cs_as
					
					--อัพดท HARMAST
					update {$this->MAuth->getdb('HARMAST')} set CLOSDT = '".$DATECHG."' where CONTNO = @CONTNO and STRNO = @STRNO
		
					insert into #AddHOLDTemp select 'S',@CONTNO,'บันทึกเปลี่ยนรถยึดเป็นรถเก่า เลขตัวถัง '+@STRNO+' เรียบร้อย';
				end
					
				commit tran AddHOLDTemp;
			end try
			begin catch
				rollback tran AddHOLDTemp;
				insert into #AddHOLDTemp select 'E','',ERROR_MESSAGE();
			end catch
		";
		//echo $sql; exit;
		
		$this->db->query($sql);
		$sql = "select * from #AddHOLDTemp";
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
			$response["msg"] = 'ผิดพลาดไม่สามารถบันทึกเปลี่ยนรถยึดเป็นรถเก่าได้ โปรดติดต่อฝ่ายไอที';
		}
		
		echo json_encode($response);
	}
	
	function Edit_holdtooldcar(){
		$CONTNO 	= $_REQUEST["CONTNO"];
		$STRNO 		= $_REQUEST["STRNO"];
		$GCODENEW	= str_replace(chr(0),'',$_REQUEST["GCODENEW"]);
		$TYPHOLD	= str_replace(chr(0),'',$_REQUEST["TYPHOLD"]);
		$Y_USER		= str_replace(chr(0),'',$_REQUEST["Y_USER"]);
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
		
		if($Y_USER == ''){
			$Y_USER = 'NULL';
		}else{
			$Y_USER = "'".$Y_USER."'";
		}

		$sql = "
			if OBJECT_ID('tempdb..#EditHOLDTemp') is not null drop table #EditHOLDTemp;
			create table #EditHOLDTemp (id varchar(20),contno varchar(20),msg varchar(max));
			
			begin tran EditHOLDTemp
			begin try
			
				declare @CONTNO varchar(20) 	= '".$CONTNO."'
				declare @STRNO varchar(max) 	= '".$STRNO."'
				
				if(select SDATE from {$this->MAuth->getdb('INVTRAN')} where STRNO = @STRNO) is null
				begin
					-- แก้ไข ARHOLD
					update {$this->MAuth->getdb('ARHOLD')}
					set BOOKVAL = ".$BOOKVAL.", BOOKVAT = ".$SALEVAT.", N_NETCST = ".$COST.", N_NETVAT = ".$COSTVAT.", N_NETTOT = ".($COST+$COSTVAT).",
					YDATE = '".$DATECHG."', MEMO1 = '".$MEMO."', TYPHOLD = '".$TYPHOLD."', N_GCODE = '".$GCODENEW."', STDPRC = ".$SALENEW.", Y_USER = ".$Y_USER."
					where CONTNO like @CONTNO and STRNO = @STRNO
					
					-- อัพเดท INVTRAN
					update {$this->MAuth->getdb('INVTRAN')}	
					set		RECVDT	= '".$DATECHG."',	
							GCODE	= '".$GCODENEW."',
							CRCOST  = ".$COST.",
							NETCOST = ".($COST-$COSTVAT).",
							CRVAT	= ".$COSTVAT.",
							TOTCOST	= ".$COST.",
							STDPRC	= '".$SALENEW."',
							JOBDATE	= '".$DATECHG."'
					where STRNO = @STRNO
					
					--อัพดท HARMAST
					update {$this->MAuth->getdb('HARMAST')} set CLOSDT = '".$DATECHG."' where CONTNO = @CONTNO and STRNO = @STRNO
	
					insert into #EditHOLDTemp select 'S',@CONTNO,'แก้ไขเปลี่ยนรถยึดเป็นรถเก่า เลขตัวถัง '+@STRNO+' เรียบร้อย';
					
				end
				else
				begin
					insert into #EditHOLDTemp select 'E',@CONTNO,'ไม่สามารถแก้ไขได้ เนื่องจากมีการขาย เลขตัวถัง '+@STRNO+' แล้ว';
				end
				
				commit tran EditHOLDTemp;
			end try
			begin catch
				rollback tran EditHOLDTemp;
				insert into #EditHOLDTemp select 'E','',ERROR_MESSAGE();
			end catch
		";
		//echo $sql; exit;
		
		$this->db->query($sql);
		$sql = "select * from #EditHOLDTemp";
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
			$response["msg"] = 'ผิดพลาดไม่สามารถแก้ไขเปลี่ยนรถยึดเป็นรถเก่าได้ โปรดติดต่อฝ่ายไอที';
		}
		
		echo json_encode($response);
	}
	
	function Delete_holdtooldcar(){
		$CONTNO	= $_REQUEST["CONTNO"];
		$CUSCOD	= $_REQUEST["CUSCOD"];
		$STRNO	= $_REQUEST["STRNO"];
		
		$sql = "
			if OBJECT_ID('tempdb..#DelHoldTemp') is not null drop table #DelHoldTemp;
			create table #DelHoldTemp (id varchar(20),contno varchar(20),msg varchar(max));
			
			begin tran DelHoldTemp
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
					insert into {$this->MAuth->getdb('ARMGAR')} 	select * from {$this->MAuth->getdb('HARMGAR')} 	where CONTNO = @CONTNO  collate thai_cs_as

					--ลบ H
					delete {$this->MAuth->getdb('HINVTRAN')} 	where CONTNO = @CONTNO and STRNO = @STRNO
					delete {$this->MAuth->getdb('HARMAST')} 	where CONTNO = @CONTNO and STRNO = @STRNO
					delete {$this->MAuth->getdb('HARPAY')} 		where CONTNO = @CONTNO 
					delete {$this->MAuth->getdb('HARMGAR')} 	where CONTNO = @CONTNO  collate thai_cs_as
					
					--ลบ ARHOLD
					delete {$this->MAuth->getdb('ARHOLD')} where CONTNO = @CONTNO and STRNO = @STRNO
					
					insert into #DelHoldTemp select 'S',@CONTNO,'ลบรายการเปลี่ยนรถยึดเป็นรถเก่า เลขที่สัญญา '+@CONTNO+' เรียบร้อย';
					
				end
				else
				begin
					insert into #DelHoldTemp select 'E',@CONTNO,'ไม่สามารถลบได้ เนื่องจากมีการคีย์ขาย เลขตัวถัง '+@STRNO+' แล้ว';
				end

				commit tran DelHoldTemp;
			end try
			begin catch
				rollback tran DelHoldTemp;
				insert into #DelHoldTemp select 'E','',ERROR_MESSAGE();
			end catch
		";
		//echo $sql; exit;
		
		$this->db->query($sql);
		$sql = "select * from #DelHoldTemp";
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
			$response["msg"] = 'ผิดพลาดไม่สามารถลบรายการเปลี่ยนรถยึดเป็นรถเก่าได้ โปรดติดต่อฝ่ายไอที';
		}
		
		echo json_encode($response);
	}
	
}