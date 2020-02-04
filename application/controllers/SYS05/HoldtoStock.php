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
			$cond .= " and a.STRNO like '".$STRNO1."%' collate thai_cs_as";
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
	
		$head = "<tr style='height:30px;'>
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
	
	function Save_holdtostock(){
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
					update {$this->MAuth->getdb('INVTRAN')}
					set CRLOCAT = '".$RVLOCAT."', CURSTAT = 'Y', YSTAT = 'Y'
					where CONTNO = @CONTNO and STRNO = @STRNO

					update {$this->MAuth->getdb('ARMAST')} 
					set YSTAT = 'Y', YDATE = '".$DATEHOLD."'
					where CONTNO = @CONTNO and CUSCOD = @CUSCOD
				end
				else if '".$YSTAT."' = 'N'
				begin
					update {$this->MAuth->getdb('INVTRAN')}
					set CRLOCAT = '".$RVLOCAT."', CURSTAT = '', YSTAT = ''
					where CONTNO = @CONTNO and STRNO = @STRNO

					update {$this->MAuth->getdb('ARMAST')} 
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
				$response["stat"] = $YSTAT;
			}
		}else{
			$response["status"] = false;
			$response["contno"] = '';
			$response["msg"] = 'ผิดพลาดไม่สามารถบันทึกเปลี่ยนสถานะสัญญาได้ โปรดติดต่อฝ่ายไอที';
			$response["stat"] = $row->msg;
			$response["stat"] = $YSTAT;
		}
		
		echo json_encode($response);
	}
	
	function Edit_holdtostock(){
		$CONTNO 	= $_REQUEST["CONTNO"];
		$CUSCOD 	= $_REQUEST["CUSCOD"];
		$STRNO 		= $_REQUEST["STRNO"];
		$YSTAT		= $_REQUEST["YSTAT"];
		$RVLOCAT	= $_REQUEST["RVLOCAT"];
		//echo $DATEHOLD; exit;
		
		$sql = "
			if OBJECT_ID('tempdb..#Editholdtostock') is not null drop table #Editholdtostock;
			create table #Editholdtostock (id varchar(20),contno varchar(20),msg varchar(max));
			
			begin tran Editholdtostock
			begin try
			
				declare @CONTNO varchar(20) = '".$CONTNO."';
				declare @CUSCOD varchar(20) = '".$CUSCOD."';
				declare @STRNO 	varchar(max) = '".$STRNO."';
				
				update {$this->MAuth->getdb('INVTRAN')}
				set CRLOCAT = '".$RVLOCAT."', CURSTAT = '', YSTAT = ''
				where CONTNO = @CONTNO and STRNO = @STRNO

				update {$this->MAuth->getdb('ARMAST')} 
				set YSTAT = 'N', YDATE = NULL
				where CONTNO = @CONTNO and CUSCOD = @CUSCOD
					
				insert into #Editholdtostock select 'S',@CONTNO,'แก้ไขรายการรถยึดเข้าสต็อก เลขตัวถังรถ '+@STRNO+' เรียบร้อย';
					
				commit tran Editholdtostock;
			end try
			begin catch
				rollback tran Editholdtostock;
				insert into #Editholdtostock select 'E','',ERROR_MESSAGE();
			end catch
		";
		//echo $sql; exit;
		
		$this->db->query($sql);
		$sql = "select * from #Editholdtostock";
		$query = $this->db->query($sql);
	  
		if($query->row()){
			foreach($query->result() as $row){
				$response["status"] = $row->id;
				$response["contno"] = $row->contno;
				$response["msg"] = $row->msg;
				$response["stat"] = $YSTAT;
			}
		}else{
			$response["status"] = false;
			$response["contno"] = '';
			$response["msg"] = 'ผิดพลาดไม่สามารถบันทึกเปลี่ยนสถานะสัญญาได้ โปรดติดต่อฝ่ายไอที';
			$response["stat"] = $row->msg;
			$response["stat"] = $YSTAT;
		}
		
		echo json_encode($response);
	}
	
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
										<select id='CONTNO2' class='form-control input-sm' data-placeholder='เลขที่สัญญา'></select>
									</div>
								</div>
							</div>
							<div class='row'>
								<div class='col-sm-4 col-xs-4'>	
									<div class='form-group'>
										สาขา
										<input type='text' id='LOCAT2' class='form-control input-sm' style='font-size:10.5pt' disabled>
									</div>
								</div>
								<div class='col-sm-4 col-xs-4'>	
									<div class='form-group'>
										ชื่อ - สกุล ลูกหนี้
										<input type='text' id='CUSNAME2' class='form-control input-sm' style='font-size:10.5pt' disabled> 
									</div>
								</div>
								<div class='col-sm-4 col-xs-4'>	
									<div class='form-group'>
										รหัสลูกหนี้
										<input type='text' id='CUSCOD2' class='form-control input-sm' style='font-size:10.5pt' disabled>
									</div>
								</div>
								<div class='col-sm-4 col-xs-4'>	
									<div class='form-group'>
										เลขทะเบียน
										<input type='text' id='REGNO2' class='form-control input-sm' style='font-size:10.5pt' disabled>
									</div>
								</div>
								<div class='col-sm-4 col-xs-4'>	
									<div class='form-group'>
										เลขตัวถัง
										<input type='text' id='STRNO2' class='form-control input-sm' style='font-size:10.5pt' disabled>
									</div>
								</div>
								<div class='col-sm-4 col-xs-4'>	
									<div class='form-group'>
										ราคาขาย
										<input type='text' id='PRICE2' class='form-control input-sm' style='font-size:10.5pt' disabled>
									</div>
								</div>
								<div class='col-sm-4 col-xs-4'>	
									<div class='form-group'>
										ชำระเงินแล้ว
										<input type='text' id='SMPAY2' class='form-control input-sm' style='font-size:10.5pt' disabled>
									</div>
								</div>
								<div class='col-sm-4 col-xs-4'>	
									<div class='form-group'>
										ยอดคงเหลือ
										<input type='text' id='BALANCE2' class='form-control input-sm' style='font-size:10.5pt' disabled>
									</div>
								</div>
								<div class='col-sm-4 col-xs-4'>	
									<div class='form-group'>
										ยอดค้างชำระ
										<input type='text' id='NETAR2' class='form-control input-sm' style='font-size:10.5pt' disabled>
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
										<input type='text' id='BOOKVALUE2' class='form-control input-sm' style='font-size:10.5pt' placeholder='0.00'>
									</div>
								</div>
								<div class='col-sm-3 col-xs-3'>	
									<div class='form-group'>
										ภาษีคงเหลือ
										<input type='text' id='SALEVAT2' class='form-control input-sm' style='font-size:10.5pt' placeholder='0.00'>
									</div>
								</div>
								<div class='col-sm-3 col-xs-3'>	
									<div class='form-group'>
										มูลค่าต้นทุน (ไม่รวม VAT)
										<input type='text' id='COST2' class='form-control input-sm' style='font-size:10.5pt' placeholder='0.00'>
									</div>
								</div>
								<div class='col-sm-3 col-xs-3'>	
									<div class='form-group'>
										ภาษีต้นทุนรถ
										<input type='text' id='COSTVAT2' class='form-control input-sm' style='font-size:10.5pt' placeholder='0.00'>
									</div>
								</div>
								<div class='col-sm-3 col-xs-3'>	
									<div class='form-group'>
										วันที่เปลี่ยนเป็นรถเก่า
										<input type='text' id='DATECHG2' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' style='font-size:10.5pt' value='".$this->today('today')."'>
									</div>
								</div>
								<div class='col-sm-3 col-xs-3'>	
									<div class='form-group'>
										สถานที่เก็บปัจจุบัน
										<input type='text' id='LOCATR2' class='form-control input-sm' style='font-size:10.5pt' placeholder='สถานที่เก็บ' readonly>
									</div>
								</div>
								<div class='col-sm-3 col-xs-3'>	
									<div class='form-group'>
										ราคาขายใหม่
										<input type='text' id='SALENEW2' class='form-control input-sm' style='font-size:10.5pt' placeholder='0.00'>
									</div>
								</div>
								<div class='col-sm-3 col-xs-3'>	
									<div class='form-group' >
										ประเภทสินค้าใหม่
										<select id='GCODENEW2' class='form-control input-sm' data-placeholder='ประเภทสินค้า' ></select>
									</div>
								</div>
								<div class='col-sm-3 col-xs-3'>	
									<div class='form-group' >
										เหตุที่บอกเลิกสัญญา
										<select id='TYPHOLD2' class='form-control input-sm' data-placeholder='เหตุที่บอกเลิกสัญญา' ></select>
									</div>
								</div>
								<div class='col-sm-3 col-xs-3'>	
									<div class='form-group' >
										พนักงานที่ยึดรถ
										<select id='Y_USER2' class='form-control input-sm' data-placeholder='พนักงานที่ยึดรถ' ></select>
									</div>
								</div>
								<div class=' col-sm-6 col-xs-6'>	
									<div class='form-group'>
										หมายเหตุ
										<textarea type='text' id='MEMO2' rows='1' cols='20' class='form-control input-sm' style='height:30px;font-size:10.5pt'></textarea>
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
	
	function searchCONTNOtoOldcar(){
		$contno	= $_REQUEST["contno"];

		$sql = "
				declare @NPROF decimal(8,2)	= ( select convert(decimal(12,2),round(SUM(NPROF),2)) AS NPROF 
				from(
					select CONTNO, CASE WHEN PAYMENT > 0 THEN (NPROF/DAMT)*PAYMENT ELSE NPROF END AS  NPROF  
					from {$this->MAuth->getdb('ARPAY')}  WHERE CONTNO = '".$contno."' and PAYMENT < DAMT
				)A)
				
				select b.CONTNO, b.CRLOCAT, isnull(b.REGNO,'') as REGNO, b.STRNO , b.STAT, b.GCODE, d.GDESC, c.SNAM, c.NAME1, c.NAME2, a.CUSCOD, a.TOTPRC, a.SMPAY+a.SMCHQ as SMPAY, 
				a.TOTPRC - a.SMPAY - a.SMCHQ as BALANC, a.EXP_AMT, a.NKANG+a.TOTDWN-a.SMPAY-a.SMCHQ-@NPROF as BOOKVAL, a.VKANG, b.STDPRC, a.VATRT, a.BILLCOLL, e.NAME
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
}