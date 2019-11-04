<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//BEE+
class HoldtoStock extends MY_Controller {
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
								จากวันที่ยึด
								<input type='text' id='FROMDATEHOLD' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' placeholder='จากวันที่'>
							</div>
						</div>
						<div class='col-sm-2 col-xs-2'>	
							<div class='form-group'>
								ถึงวันที่ยึด
								<input type='text' id='TODATEHOLD' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' placeholder='ถึงวันที่'>
							</div>
						</div>
						<div class='col-sm-2 col-xs-2'>	
							<div class='form-group'>
								เลขตัวถังรถ
								<input type='text' id='STRNO1' class='form-control input-sm' placeholder='เลขตัวถังรถ'>
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
					<div id='resultt_HoldtoStock' style='background-color:white;'></div>
				</div>
			</div>
		";
		
		$html.= "<script src='".base_url('public/js/SYS05/HoldtoStock.js')."'></script>";
		echo $html;
	}
	
	//ฟอร์มบันทึกลูกหนี้อื่น
	function getfromHoldtoStock(){
		$level	= $_REQUEST["level"];
		//$locat = $this->sess['branch'];

		$html = "
			<div class='b_ChangeContstat' style='width:100%;height:calc(100vh - 132px);overflow:auto;background-color:white;'>
				<div style='float:left;height:100%;overflow:auto;' class='col-sm-6 col-xs-6 col-sm-offset-3'>
					<div class='row'>
						<br>
						<div class='col-sm-6 col-xs-6'>	
							<div class='form-group' style='color:blue;'>
								เลขที่สัญญา
								<select id='CONTNO' class='form-control input-sm' data-placeholder='เลขที่สัญญา'></select>
							</div>
						</div>
						<div class='col-sm-6 col-xs-6'>	
							<div class='form-group'>
								สาขา
								<input type='text' id='LOCAT' class='form-control input-sm' style='font-size:10.5pt' disabled>
							</div>
						</div>
						<div class='col-sm-6 col-xs-6'>	
							<div class='form-group'>
								ชื่อ - สกุล ลูกค้า
								<input type='text' id='CUSNAME' class='form-control input-sm' style='font-size:10.5pt' disabled>
							</div>
						</div>
						<div class='col-sm-6 col-xs-6'>	
							<div class='form-group'>
								รหัสลูกค้า
								<input type='text' id='CUSCOD' class='form-control input-sm' style='font-size:10.5pt' disabled>
							</div>
						</div>
						<div class='col-sm-6 col-xs-6'>	
							<div class='form-group'>
								เลขตัวถังรถ
								<input type='text' id='STRNO' class='form-control input-sm' style='font-size:10.5pt' disabled>
							</div>
						</div>
						<div class='col-sm-6 col-xs-6'>	
							<div class='form-group'>
								ราคาขาย
								<input type='text' id='TOTPRC' class='form-control input-sm' style='font-size:10.5pt' disabled>
							</div>
						</div>
						<div class='col-sm-6 col-xs-6'>	
							<div class='form-group'>
								ชำระเงินแล้ว
								<input type='text' id='SMPAY' class='form-control input-sm' style='font-size:10.5pt' disabled>
							</div>
						</div>
						<div class='col-sm-6 col-xs-6'>	
							<div class='form-group'>
								ยอดคงเหลือ
								<input type='text' id='BALANCE' class='form-control input-sm' style='font-size:10.5pt' disabled>
							</div>
						</div>
						<div class='col-sm-6 col-xs-6'>	
							<div class='form-group'>
								ค้างชำระ
								<input type='text' id='EXP_AMT' class='form-control input-sm' style='font-size:10.5pt' disabled>
							</div>
						</div>
						<div class='col-sm-6 col-xs-6'>	
							<div class='form-group'>
								สถานะรถ
								<div class='form-control' style='font-size:10.5pt;'>
									<div class='col-sm-6 col-xs-6'>	
										<input type= 'radio' id='YSTAT_Y' name='YSTAT'>&nbsp;รถยึด
									</div>
									<div class='col-sm-6 col-xs-6'>	
										<input type= 'radio' id='YSTAT_N' name='YSTAT'>&nbsp;รถปกติ
									</div>
								</div>
							</div>
						</div>
						<div class='col-sm-6 col-xs-6'>	
							<div class='form-group'>
								วันที่ยึดรถ
								<input type='text' id='DATEHOLD' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' style='font-size:10.5pt;' placeholder='วันที่ยึดรถ'>
							</div>
						</div>
						<div class='col-sm-6 col-xs-6'>	
							<div class='form-group'>
								สาขาที่เก็บรถ
								<select id='RVLOCAT' class='form-control input-sm' data-placeholder='สาขาที่เก็บรถ'></select>
							</div>
						</div>
					</div>
					<div class='row'>
						<div class=' col-sm-3 col-sm-offset-3'>	
							<div class='form-group'>
								<br>
								<button id='btnsave_holdtostock' class='btn btn-primary btn-block'><span class='glyphicon glyphicon-floppy-disk'> บันทึก</span></button>
							</div>
						</div>
						<div class=' col-sm-3'>	
							<div class='form-group'>
								<br>
								<input type='button' id='btnclr_holdtostock' class='btn btn-default btn-sm' value='เคลียร์' style='width:100%'>
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
				select a.CONTNO, a.LOCAT, c.CRLOCAT, b.SNAM, b.NAME1, b.NAME2, a.CUSCOD, a.STRNO, a.TOTPRC, a.SMPAY, a.TOTPRC - a.SMPAY - a.SMCHQ as BALANC, a.EXP_AMT, 
				a.YSTAT, convert(nvarchar,dateadd(year,543,a.YDATE),103) as YDATE
				from {$this->MAuth->getdb('ARMAST')} a
				left join {$this->MAuth->getdb('CUSTMAST')} b on a.CUSCOD = b.CUSCOD
				left join {$this->MAuth->getdb('INVTRAN')} c on a.CONTNO = c.CONTNO and a.STRNO = c.STRNO
				where a.TOTPRC > a.SMPAY and a.CONTNO = '".$contno."'
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$response = array();
		if($query->row()){
			foreach($query->result() as $row){
				$response["CONTNO"] 	= $row->CONTNO;
				$response["LOCAT"] 		= $row->CRLOCAT;
				$response["CUSNAME"] 	= $row->SNAM.$row->NAME1.' '.$row->NAME2;
				$response["CUSCOD"] 	= $row->CUSCOD;
				$response["STRNO"] 		= str_replace(chr(0),'',$row->STRNO);
				$response["TOTPRC"] 	= number_format($row->TOTPRC,2);
				$response["SMPAY"] 		= number_format($row->SMPAY,2);
				$response["BALANCE"] 	= number_format($row->BALANC,2);
				$response["EXP_AMT"] 	= number_format($row->EXP_AMT,2);
				$response["YSTAT"] 		= str_replace(chr(0),'',$row->YSTAT);
				$response["YDATE"] 		= $row->YDATE;
			}
		}
		echo json_encode($response);
	}
	
	function search(){
		//echo base64_encode('SYS005-001-003'); exit;
		$LOCAT1	= $_REQUEST["LOCAT1"];
		$STRNO1 = $_REQUEST["STRNO1"];
		$FROMDATEHOLD = $_REQUEST["FROMDATEHOLD"];
		$TODATEHOLD = $_REQUEST["TODATEHOLD"];
		
		$cond = "";
		if($LOCAT1 != ""){
			$cond .= " and a.LOCAT = '".$LOCAT1."'";
		}
		
		if($STRNO1 != ""){
			$cond .= " and a.STRNO like '%".$STRNO1."%' collate thai_cs_as";
		}
		
		if($FROMDATEHOLD != ""){
			$cond .= " and a.YDATE >= '".$this->Convertdate(1,$FROMDATEHOLD)."'";
		}
		
		if($TODATEHOLD != ""){
			$cond .= " and a.YDATE <= '".$this->Convertdate(1,$TODATEHOLD)."'";
		}
		
		$sql = "				
				select ".(($cond == '') ? 'top 50':'')." a.CONTNO, a.CUSCOD, b.SNAM+b.NAME1+' '+b.NAME2 as CUSNAME, convert(nvarchar,dateadd(year,543,a.YDATE),103) as YDATES, 
				a.LOCAT, a.STRNO, case when c.STAT = 'N' then 'รถใหม่' else 'รถเก่า' end as STAT, c.GCODE, a.EXP_PRD, c.CRLOCAT, a.YSTAT, a.YDATE,
				a.TOTPRC, a.SMPAY, a.TOTPRC - a.SMPAY - a.SMCHQ as BALANC, a.EXP_AMT
				from {$this->MAuth->getdb('ARMAST')} a
				left join {$this->MAuth->getdb('CUSTMAST')} b on a.CUSCOD = b.CUSCOD
				left join {$this->MAuth->getdb('INVTRAN')} c on a.CONTNO = c.CONTNO and a.STRNO = c.STRNO
				where a.YSTAT = 'Y' ".$cond."
				order by YDATE desc
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$head = ""; $html = ""; $i=0;
	
		$head = "<tr>
				<th style='vertical-align:middle;'>#</th>
				<th style='vertical-align:middle;'>เลขที่สัญญา</th>
				<th style='vertical-align:middle;'>รหัสลูกค้า</th>
				<th style='vertical-align:middle;'>ชื่อ - สกุล ลูกค้า</th>
				<th style='vertical-align:middle;'>วันที่ยึดรถ</th>
				<th style='vertical-align:middle;'>สัญญาสาขา</th>
				<th style='vertical-align:middle;'>เลขตัวถังรถ</th>
				<th style='vertical-align:middle;'>ประเภท</th>
				<th style='vertical-align:middle;'>ค้างชำระ (งวด)</th>
				<th style='vertical-align:middle;'>สาขาที่เก็บรถ</th>
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
						YDATE		= '".$row->YDATES."' 
						LOCAT		= '".$row->LOCAT."' 
						CUSNAME		= '".$row->CUSNAME."' 
						CUSCOD		= '".$row->CUSCOD."'
						STRNO		= '".str_replace(chr(0),'',$row->STRNO)."' 
						TOTPRC		= '".number_format($row->TOTPRC,2)."'
						SMPAY		= '".number_format($row->SMPAY,2)."'
						BALANCE		= '".number_format($row->BALANC,2)."'
						EXP_AMT		= '".number_format($row->EXP_AMT,2)."'
						YSTAT		= '".str_replace(chr(0),'',$row->YSTAT)."' 
						RVLOCAT		= '".$row->CRLOCAT."'
						><b>เลือก</b></td>
						<td>".$row->CONTNO."</td>
						<td>".$row->CUSCOD."</td>
						<td>".$row->CUSNAME."</td>
						<td>".$row->YDATES."</td>
						<td>".$row->LOCAT."</td>
						<td>".$row->STRNO."</td>
						<td>".$row->STAT."</td>
						<td>".number_format($row->EXP_PRD)."</td>
						<td>".$row->CRLOCAT."</td>
					</tr>
				";	
			}
		}
		
		if($i>0){
			$html = "
				<div id='table-fixed-changecontstat' class='col-sm-12' style='height:100%;width:100%;overflow:auto;'>
					<table id='table-changecontstat' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='calc(100% - 1px)'>
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
		$CUSCOD 	= $_REQUEST["CUSCOD"];
		$STRNO 		= $_REQUEST["STRNO"];
		$YSTAT		= $_REQUEST["YSTAT"];
		$DATEHOLD	= $this->Convertdate(1,$_REQUEST["DATEHOLD"]);
		$RVLOCAT	= $_REQUEST["RVLOCAT"];
		$USERID		= $this->sess["USERID"];
		//echo $DATEHOLD; exit;
		
		$sql = "
			if OBJECT_ID('tempdb..#AddHoldtoStock') is not null drop table #AddHoldtoStock;
			create table #AddHoldtoStock (id varchar(20),contno varchar(20),msg varchar(max));
			
			begin tran AddHoldtoStock
			begin try
			
				declare @CONTNO varchar(20) = '".$CONTNO."';
				declare @CUSCOD varchar(20) = '".$CUSCOD."';
				declare @STRNO 	varchar(max) = '".$STRNO."';
				
				if '".$YSTAT."' = 'Y'
				begin
					update INVTRAN
					set CRLOCAT = '".$RVLOCAT."', CURSTAT = 'Y', YSTAT = 'Y'
					where CONTNO = @CONTNO and STRNO = @STRNO

					update ARMAST 
					set YSTAT = 'Y', YDATE = '".$DATEHOLD."'
					where CONTNO = @CONTNO and CUSCOD = @CUSCOD
				end
				else if '".$YSTAT."' = 'N'
				begin
					update INVTRAN
					set CRLOCAT = '".$RVLOCAT."', CURSTAT = '', YSTAT = ''
					where CONTNO = @CONTNO and STRNO = @STRNO

					update ARMAST 
					set YSTAT = 'N', YDATE = NULL
					where CONTNO = @CONTNO and CUSCOD = @CUSCOD
				end
					
				insert into #AddHoldtoStock select 'S',@CONTNO,'บันทึกรายการรถยึดเข้าสต็อก เลขตัวถังรถ '+@STRNO+' เรียบร้อย';
					
				commit tran AddHoldtoStock;
			end try
			begin catch
				rollback tran AddHoldtoStock;
				insert into #AddHoldtoStock select 'E','',ERROR_MESSAGE();
			end catch
		";
		//echo $sql; exit;
		
		$this->db->query($sql);
		$sql = "select * from #AddHoldtoStock";
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
}