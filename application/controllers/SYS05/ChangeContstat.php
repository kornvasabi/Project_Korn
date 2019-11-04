<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//BEE+
class ChangeContstat extends MY_Controller {
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
			<div class='b_tab1' name='home' locat='{$this->sess['branch']}' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}' today='".$this->today('today')."' usergroup='{$claim['groupCode']}' style='height:calc(100vh - 132px);overflow:auto;background-color:white;'>
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
								จากวันที่เปลี่ยนสถานะ
								<input type='text' id='FROMDATECHG' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' placeholder='จากวันที่'>
							</div>
						</div>
						<div class='col-sm-2 col-xs-2'>	
							<div class='form-group'>
								ถึงวันที่เปลี่ยนสถานะ
								<input type='text' id='TODATECHG' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' placeholder='ถึงวันที่'>
							</div>
						</div>
						<div class='col-sm-2 col-xs-2'>	
							<div class='form-group'>
								เลขที่สัญญา
								<input type='text' id='CONTNO1' class='form-control input-sm' placeholder='เลขที่สัญญา'>
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
					</div>
					<div id='resultt_ChangeContstat' style='background-color:white;'></div>
				</div>
			</div>
		";
		
		$html.= "<script src='".base_url('public/js/SYS05/ChangeContstat.js')."'></script>";
		echo $html;
	}
	
	//ฟอร์มบันทึกลูกหนี้อื่น
	function getfromChangeContstat(){
		$level	= $_REQUEST["level"];
		//$locat = $this->sess['branch'];

		$html = "
			<div class='b_ChangeContstat' style='width:100%;height:calc(100vh - 132px);overflow:auto;background-color:white;'>
				<div style='float:left;height:100%;overflow:auto;' class='col-sm-6 col-xs-6 col-sm-offset-3'>
					<div class='row'>
						<div class='col-sm-12 col-xs-12' style='height:50px;'></div>
						<div class='col-sm-6 col-xs-6'>	
							<div class='form-group' style='color:blue;'>
								เลขที่สัญญา
								<select id='CONTNO' class='form-control input-sm' data-placeholder='เลขที่สัญญา'></select>
							</div>
						</div>
						<div class='col-sm-6 col-xs-6'>	
							<div class='form-group'>
								วันที่เปลี่ยนสถานะ
								<input type='text' id='DATECHG' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' style='font-size:10.5pt' value='".$this->today('today')."'>
							</div>
						</div>
						<div class='col-sm-6 col-xs-6'>	
							<div class='form-group'>
								จากสถานะ
								<input type='text' id='FROMSTAT' class='form-control input-sm' style='font-size:10.5pt' placeholder='จากสถานะ' disabled>
							</div>
						</div>
						<div class='col-sm-6 col-xs-6'>	
							<div class='form-group'>
								เป็นสถานะ
								<select id='TOSTAT' class='form-control input-sm' data-placeholder='เป็นสถานะ'></select>
							</div>
						</div>
						<div class='col-sm-6 col-xs-6'>	
							<div class='form-group'>
								ค้างชำระ (งวด)
								<input type='text' id='EXP_PRD' class='form-control input-sm' style='font-size:10.5pt' placeholder='ค้างชำระ (งวด)' disabled>
							</div>
						</div>
						<div class='col-sm-6 col-xs-6'>	
							<div class='form-group'>
								ค้างชำระ (บาท)
								<input type='text' id='EXP_AMT' class='form-control input-sm' style='font-size:10.5pt' placeholder='ค้างชำระ (บาท)' disabled>
							</div>
						</div>
						<div class='col-sm-6 col-xs-6'>	
							<div class='form-group'>
								จากพนักงานเก็บเงิน
								<input type='text' id='FROMBILL' class='form-control input-sm' style='font-size:10.5pt' placeholder='จากพนักงานเก็บเงิน' disabled>
							</div>
						</div>
						<div class='col-sm-6 col-xs-6'>	
							<div class='form-group'>
								เป็นพนักงานเก็บเงิน
								<select id='TOBILL' class='form-control input-sm' data-placeholder='เป็นพนักงานเก็บเงิน'></select>
							</div>
						</div>
						<div class='col-sm-12 col-xs-12'>	
							<div class='form-group'>
								หมายเหตุ
								<textarea type='text' id='MEMO' rows='3' cols='20' class='form-control input-sm' style='font-size:10.5pt'></textarea>
							</div>
						</div>	
					</div>
					<div class='row'>
						<div class=' col-sm-3 col-sm-offset-3'>	
							<div class='form-group'>
								<br>
								<button id='btnsave_changecontstat' class='btn btn-primary btn-block'><span class='glyphicon glyphicon-floppy-disk'> บันทึก</span></button>
							</div>
						</div>
						<div class=' col-sm-3'>	
							<div class='form-group'>
								<br>
								<input type='button' id='btnclr_changecontstat' class='btn btn-default btn-sm' value='เคลียร์' style='width:100%'>
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
				select CONTNO, CONTSTAT, EXP_PRD, EXP_AMT, BILLCOLL, USERNAME+' ('+BILLCOLL+')' as USERNAME
				from {$this->MAuth->getdb('ARMAST')} a
				left join {$this->MAuth->getdb('PASSWRD')} b on a.BILLCOLL = b.USERID
				where CONTNO = '".$contno."'
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$response = array();
		if($query->row()){
			foreach($query->result() as $row){
				$response["CONTNO"] 	= $row->CONTNO;
				$response["CONTSTAT"] 	= str_replace(chr(0),'',$row->CONTSTAT);
				$response["EXP_PRD"] 	= number_format($row->EXP_PRD);
				$response["EXP_AMT"] 	= number_format($row->EXP_AMT,2);
				$response["USERNAME"] 	= str_replace(chr(0),'',$row->USERNAME);
				$response["BILLCOLL"] 	= $row->BILLCOLL;
			}
		}
		
		echo json_encode($response);
	}
	
	function search(){
		//echo base64_encode('SYS005-001-003'); exit;
		$LOCAT1	= $_REQUEST["LOCAT1"];
		$CONTNO1 = $_REQUEST["CONTNO1"];
		$FROMDATECHG = $_REQUEST["FROMDATECHG"];
		$TODATECHG = $_REQUEST["TODATECHG"];
		
		$cond = "";
		if($LOCAT1 != ""){
			$cond .= " and a.LOCAT = '".$LOCAT1."'";
		}
		
		if($CONTNO1 != ""){
			$cond .= " and a.CONTNO like '%".$CONTNO1."%' collate thai_cs_as";
		}
		
		if($FROMDATECHG != ""){
			$cond .= " and CHGDATE >= '".$this->Convertdate(1,$FROMDATECHG)."'";
		}
		
		if($TODATECHG != ""){
			$cond .= " and CHGDATE <= '".$this->Convertdate(1,$TODATECHG)."'";
		}
		
		$sql = "				
				select ".(($cond == '') ? 'top 50':'')." a.LOCAT, a.CONTNO, STATFRM, b.CONTDESC as CONTDESCFRM, STATTO, c.CONTDESC as CONTDESCTO, 
				FRMBILL, d.USERNAME as FRMBILLNAME, TOBILL, e.USERNAME as TOBILLNAME, case when year(CHGDATE) <= YEAR(GETDATE()) then DATEADD(YEAR,543,CHGDATE) else CHGDATE end as CHGDATE2, 
				case when year(CHGDATE) <= YEAR(GETDATE()) then convert(nvarchar,dateadd(year,543,CHGDATE),103) else convert(nvarchar,CHGDATE,103) end as CHGDATES,
				AUTHCOD, f.USERNAME as AUTHCODNAME, MEMO1,EXP_PRD, EXP_AMT
				from {$this->MAuth->getdb('STATTRAN')} a
				left join {$this->MAuth->getdb('TYPCONT')} b on a.STATFRM = b.CONTTYP
				left join {$this->MAuth->getdb('TYPCONT')} c on a.STATTO = c.CONTTYP
				left join {$this->MAuth->getdb('PASSWRD')} d on a.FRMBILL = d.USERID
				left join {$this->MAuth->getdb('PASSWRD')} e on a.TOBILL = e.USERID
				left join {$this->MAuth->getdb('PASSWRD')} f on a.AUTHCOD = f.USERID
				left join (select CONTNO, EXP_PRD, EXP_AMT from {$this->MAuth->getdb('ARMAST')})g on a.CONTNO = g.CONTNO
				where 1=1 ".$cond."
				order by case when year(CHGDATE) <= YEAR(GETDATE()) then DATEADD(YEAR,543,CHGDATE) else CHGDATE end desc, a.LOCAT
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$head = ""; $html = ""; $i=0;
	
		$head = "<tr>
				<th style='vertical-align:middle;'>#</th>
				<th style='vertical-align:middle;'>สาขา</th>
				<th style='vertical-align:middle;'>เลขที่สัญญา</th>
				<th style='vertical-align:middle;'>จากสถานะสัญญา</th>
				<th style='vertical-align:middle;'>เป็นสถานะสัญญา</th>
				<th style='vertical-align:middle;'>จากพนักงานเก็บเงิน</th>
				<th style='vertical-align:middle;'>เป็นพนักงานเก็บเงิน</th>
				<th style='vertical-align:middle;'>วันที่เปลี่ยนสถานะ</th>
				<th style='vertical-align:middle;'>ผู้ทำรายการ</th>
				<th style='vertical-align:middle;'>หมายเหตุ</th>
				</tr>
		";
		
		$NRow = 1;
		if($query->row()){
			foreach($query->result() as $row){$i++;
				$bgcolor="";
				$html .= "
					<tr class='trow' seq=".$NRow." style='".$bgcolor."'>
						<td class='getit' seq=".$NRow++."  style='cursor:pointer;text-align:center;'
						CONTNO		= '".str_replace(chr(0),'',$row->CONTNO)."' 
						CHGDATE		= '".$row->CHGDATES."' 
						FROMSTAT	= '".str_replace(chr(0),'',$row->STATFRM)."' 
						CONTDESCFRM	= '".str_replace(chr(0),'',$row->CONTDESCFRM)."'
						TOSTAT		= '".str_replace(chr(0),'',$row->STATTO)."' 
						CONTDESCTO	= '".str_replace(chr(0),'',$row->CONTDESCTO)."'
						EXP_PRD		= '".number_format($row->EXP_PRD)."'
						EXP_AMT		= '".number_format($row->EXP_AMT,2)."'
						FRMBILL		= '".str_replace(chr(0),'',$row->FRMBILL)."' 
						FRMBILLNAME	= '".str_replace(chr(0),'',$row->FRMBILLNAME)."'
						TOBILL		= '".str_replace(chr(0),'',$row->TOBILL)."' 
						TOBILLNAME	= '".str_replace(chr(0),'',$row->TOBILLNAME)."'
						MEMO1		= '".$row->MEMO1."'
						LOCAT		= '".$row->LOCAT."'
						><b>เลือก</b></td>
						<td align='center'>".$row->LOCAT."</td>
						<td>".$row->CONTNO."</td>
						<td align='center'>".$row->STATFRM."</td>
						<td align='center'>".$row->STATTO."</td>
						<td>".$row->FRMBILLNAME."</td>
						<td>".$row->TOBILLNAME."</td>
						<td>".$row->CHGDATES."</td>
						<td>".$row->AUTHCODNAME."</td>
						<td>".$row->MEMO1."</td>
					</tr>
				";	
			}
		}
		
		if($i>0){
			$html = "
				<div id='table-fixed-ChangeContstat' class='col-sm-12' style='height:100%;width:100%;overflow:auto;'>
					<table id='table-ChangeContstat' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='calc(100% - 1px)'>
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
		
		$response = array("html"=>$html);
		echo json_encode($response);
	}
	
	function Save_changecontstat(){
		$CONTNO 	= $_REQUEST["CONTNO"];
		$TOSTAT 	= str_replace(chr(0),'',$_REQUEST["TOSTAT"]);
		$TOBILL		= str_replace(' ','',$_REQUEST["TOBILL"]);
		$DATECHG	= $this->Convertdate(1,$_REQUEST["DATECHG"]);
		$MEMO		= $_REQUEST["MEMO"];
		$USERID		= $this->sess["USERID"];
		//echo $TOBILL; exit;
		if($TOBILL == ''){
			$TOBILL = 'NULL';
		}else{
			$TOBILL = "'".$TOBILL."'";
		}
		
		if($MEMO == ''){
			$MEMO = 'NULL';
		}else{
			$MEMO = "'".$MEMO."'";
		}
		//echo $MEMO; exit;

		$sql = "
			if OBJECT_ID('tempdb..#AddCHGSTATTemp') is not null drop table #AddCHGSTATTemp;
			create table #AddCHGSTATTemp (id varchar(20),contno varchar(20),msg varchar(max));
			
			begin tran AddCHGSTATTemp
			begin try
			
				declare @CONTNO varchar(20) = '".$CONTNO."';
				declare @BILLCOLL varchar(10) = (select ".($TOBILL == 'NULL' ? 'BILLCOLL':$TOBILL)." as BILLCOLL from ARMAST where CONTNO = @CONTNO);

				insert into {$this->MAuth->getdb('STATTRAN')} 
				select LOCAT, CONTNO, CONTSTAT as STATFRM, '".$TOSTAT."' as STATTO, '".$DATECHG."' as CHGDATE, '".$USERID."' as AUTHCOD, ".$MEMO." as MEMO1, BILLCOLL as FRMBILL, '".$TOBILL."' as TOBILL
				from {$this->MAuth->getdb('ARMAST')}
				where CONTNO = @CONTNO

				update {$this->MAuth->getdb('ARMAST')}
				set CONTSTAT = '".$TOSTAT."', BILLCOLL = @BILLCOLL
				where CONTNO = @CONTNO
					
				insert into #AddCHGSTATTemp select 'S',@CONTNO,'บันทึกเปลี่ยนสถานะสัญญาเลขที่ '+@CONTNO+' เรียบร้อย';
					
				commit tran AddCHGSTATTemp;
			end try
			begin catch
				rollback tran AddCHGSTATTemp;
				insert into #AddCHGSTATTemp select 'E','',ERROR_MESSAGE();
			end catch
		";
		//echo $sql; exit;
		
		$this->db->query($sql);
		$sql = "select * from #AddCHGSTATTemp";
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
			$response["msg"] = 'ผิดพลาดไม่สามารถบันทึกเปลี่ยนสถานะสัญญาได้ โปรดติดต่อฝ่ายไอที';
		}
		
		echo json_encode($response);
	}
	
	function Edit_changecontstat(){
		$CONTNO 	= $_REQUEST["CONTNO"];
		$TOSTAT 	= str_replace(chr(0),'',$_REQUEST["TOSTAT"]);
		$TOBILL		= str_replace(' ','',$_REQUEST["TOBILL"]);
		$FROMSTATold= $_REQUEST["FROMSTATold"];
		$TOSTATold	= $_REQUEST["TOSTATold"];
		$FRMBILLold	= $_REQUEST["FRMBILLold"];
		$TOBILLold	= $_REQUEST["TOBILLold"];
		$MEMO		= $_REQUEST["MEMO"];
		
		if($TOBILL == ''){
			$TOBILL = 'NULL';
		}else{
			$TOBILL = "'".$TOBILL."'";
		}

		$sql = "
			if OBJECT_ID('tempdb..#EditCHGSTATTemp') is not null drop table #EditCHGSTATTemp;
			create table #EditCHGSTATTemp (id varchar(20),contno varchar(20),msg varchar(max));
			
			begin tran EditCHGSTATTemp
			begin try
			
				declare @CONTNO varchar(20) = '".$CONTNO."';
				declare @BILLCOLL varchar(10) = (select ".($TOBILL == 'NULL' ? 'BILLCOLL':$TOBILL)." as BILLCOLL from ARMAST where CONTNO = @CONTNO);
				
				update {$this->MAuth->getdb('STATTRAN')}
				set STATTO = '".$TOSTAT."', TOBILL = '".$TOBILL."', MEMO1 = '".$MEMO."'
				where CONTNO like '%".$CONTNO."%' and STATFRM like '%".$FROMSTATold."%' and STATTO like '%".$TOSTATold."%' 
				and FRMBILL like '%".$FRMBILLold."%' and TOBILL like '%".$TOBILLold."%'

				update {$this->MAuth->getdb('ARMAST')}
				set CONTSTAT = '".$TOSTAT."', BILLCOLL = @BILLCOLL
				where CONTNO = @CONTNO
					
				insert into #EditCHGSTATTemp select 'S',@CONTNO,'แก้ไขเปลี่ยนสถานะสัญญาเลขที่ '+@CONTNO+' เรียบร้อย';
					
				commit tran EditCHGSTATTemp;
			end try
			begin catch
				rollback tran EditCHGSTATTemp;
				insert into #EditCHGSTATTemp select 'E','',ERROR_MESSAGE();
			end catch
		";
		//echo $sql; exit;
		
		$this->db->query($sql);
		$sql = "select * from #EditCHGSTATTemp";
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
			$response["msg"] = 'ผิดพลาดไม่สามารถแก้ไขเปลี่ยนสถานะสัญญาได้ โปรดติดต่อฝ่ายไอที';
		}
		
		echo json_encode($response);
	}
}