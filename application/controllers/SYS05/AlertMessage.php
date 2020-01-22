<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//BEE+
class AlertMessage extends MY_Controller {
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
								จากวันที่บันทึก
								<input type='text' id='FROMDATE' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' placeholder='จากวันที่บันทึก' >
							</div>
						</div>
						<div class='col-sm-2 col-xs-2'>	
							<div class='form-group'>
								ถึงวันที่บันทึก
								<input type='text' id='TODATE' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' placeholder='ถึงวันที่บันทึก' >
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
					</div><br>
					<div id='resultt_AlertMessage' style='background-color:white;'></div>
				</div>
			</div>
		";
		
		$html.= "<script src='".base_url('public/js/SYS05/AlertMessage.js')."'></script>";
		echo $html;
	}
	
	function getfromAlertMessage(){
		$level	= $_REQUEST["level"];
		//$locat = $this->sess['branch'];

		$html = "
			<div class='b_HoldtoOldcar' style='width:800px;height:480px;overflow:auto;background-color:white;'>
				<div style='float:left;overflow:auto;' class='col-sm-12 col-xs-12'>
					<div class='row'>
						<div class='col-sm-12 col-xs-12' style='background-color:#d11226;border:5px solid white;height:75px;text-align:center;font-size:12pt;color:white;font-weight:bold;'>	
							<br>บันทึกข้อความเตือน<br>
						</div>
					</div>
					<div class='row'>
						<div class='col-sm-12 col-xs-12' align='right'>	
							<img id='DISCRIPTION' src='../public/images/manual-icon.png' style='width:30px;height:30px;cursor:pointer;filter: contrast(100%);'>
						</div>
					</div>
					<div class='row'>
						<div class='col-sm-10 col-xs-10 col-sm-offset-1'>	
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group'>
									เลขที่สัญญา
									<select id='CONTNO' class='form-control input-sm' data-placeholder='เลขที่สัญญา'></select>
								</div>
							</div>
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group' >
									วันที่บันทึก
									<input type='text' id='CREATEDT' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' placeholder='วันที่บันทึก' value='".$this->today('today')."' style='font-size:10.5pt'>
								</div>
							</div>
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group'>
									วันที่เริ่มเตือน
									<input type='text' id='STARTDT' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' placeholder='วันที่เริ่มเตือน' value='".$this->today('today')."' style='font-size:10.5pt'>
								</div>
							</div>
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group'>
									วันที่สิ้นสุดการเตือน
									<input type='text' id='ENDDT' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' placeholder='วันที่สิ้นสุดการเตือน' value='".$this->today('endofmonth')."' style='font-size:10.5pt'>
								</div>
							</div>
						</div>
					</div>
					<div class='row'>
						<div class='col-sm-10 col-xs-10 col-sm-offset-1'>
							<div class='col-sm-12 col-xs-12'>	
								<div class='form-group'>
									บันทึกข้อความที่จะแสดงเตือน
									<textarea type='text' id='MEMO' rows='2' cols='20' class='form-control input-sm' style='font-size:10.5pt'></textarea>
								</div>
							</div>
						</div>
					</div>
					<div class='row'>
						<div class='col-sm-10 col-xs-10 col-sm-offset-1'>
							<div class='col-sm-12 col-xs-12'>	
								<div class='form-group'>
									<div class='col-sm-6 col-xs-6'>
										<div style='color:red;'>
											<input type= 'radio' id='notedit' name='useredit' checked> ผู้อ่านแก้ไขข้อความไม่ได้ (สีแดง)
										</div>
									</div>
									<div class='col-sm-6 col-xs-6'>
										<div style='color:blue;'>
											<input type= 'radio' id='edit' name='useredit'> ผู้อ่านแก้ไขข้อความได้ (สีน้ำเงิน)
										</div>
									</div>
								</div>
							</div>
						</div>
					</div><br><br><br>
					<div class='row'>
						<div class=' col-sm-2 col-sm-offset-3'>	
							<div class='form-group'>
								<button id='btnsave_alertmsg' class='btn btn-primary btn-block'><span class='glyphicon glyphicon-floppy-disk'> บันทึก</span></button>
							</div>
						</div>
						<div class=' col-sm-2'>	
							<div class='form-group'>
								<input type='button' id='btncancel_alertmsg' class='btn btn-default btn-sm' value='เคลียร์' style='width:100%'>
							</div>
						</div>
						<div class=' col-sm-2'>	
							<div class='form-group'>
								<input type='button' id='btndelete_alertmsg' class='btn btn-danger btn-sm' value='ลบ' style='width:100%'>
							</div>
						</div>
					</div>
				</div>
			</div>
		";
	
		$response = array("html"=>$html);
		echo json_encode($response);
	}
	
	function search(){
		$LOCAT1		= $_REQUEST["LOCAT1"];
		$CONTNO1	= $_REQUEST["CONTNO1"];
		$FROMDATE 	= $_REQUEST["FROMDATE"];
		$TODATE 	= $_REQUEST["TODATE"];
		
		$cond = "";
		if($LOCAT1 != ""){
			$cond .= " and LOCAT = '".$LOCAT1."'";
		}
		
		if($CONTNO1 != ""){
			$cond .= " and CONTNO like '%".$CONTNO1."%' collate thai_cs_as";
		}
		
		if($FROMDATE != ""){
			$cond .= " and CREATEDT >= '".$this->Convertdate(1,$FROMDATE)."'";
		}
		
		if($TODATE != ""){
			$cond .= " and CREATEDT <= '".$this->Convertdate(1,$TODATE)."'";
		}
		
		$sql = "
			select ".($cond == '' ? 'top 50':'')."  convert(nvarchar,dateadd(year,543,CREATEDT),103) as CREATEDTS, LOCAT, CONTNO, MEMO1, 
			convert(nvarchar,dateadd(year,543,STARTDT),103) as STARTDTS, convert(nvarchar,dateadd(year,543,ENDDT),103) as ENDDTS , USERID
			from {$this->MAuth->getdb('ALERTMSG')}
			where 1 = 1 ".$cond." 
			order by CREATEDT desc, LOCAT
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
						LOCAT 		= '".$row->LOCAT."'
						CREATEDT 	= '".$row->CREATEDTS."'
						STARTDT 	= '".$row->STARTDTS."'
						ENDDT 		= '".$row->ENDDTS."'
						USERID		= '".str_replace(chr(0),'',$row->USERID)."'
						MEMO1 		= '".$row->MEMO1."'
						><b>เลือก</b></td>
						<td>".$row->CREATEDTS."</td>
						<td>".$row->LOCAT."</td>
						<td>".$row->CONTNO."</td>
						<td>".$row->MEMO1."</td>
						<td>".$row->STARTDTS."</td>
						<td>".$row->ENDDTS."</td>
					</tr>
				";	
			}
		}
		
		$html = "
			<div id='table-fixed-alertmsg' class='col-sm-12' style='height:100%;width:100%;overflow:auto;'>
				<table id='table-alertmsg' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='calc(100% - 1px)'>
					<thead>
						<tr style='height:30px;'>
							<th style='vertical-align:middle;'>#</th>
							<th style='vertical-align:middle;'>วันที่บันทึก</th>
							<th style='vertical-align:middle;'>สาขา</th>
							<th style='vertical-align:middle;'>เลขที่สัญญา</th>
							<th style='vertical-align:middle;'>รายละเอียดการแจ้งเตือน</th>
							<th style='vertical-align:middle;'>วันที่เริ่มเตือน</th>
							<th style='vertical-align:middle;'>วันที่สิ้นสุดการเตือน</th>
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
	
	function Save_alertmsg(){
		$CONTNO 	= $_REQUEST["CONTNO"];
		$CREATEDT 	= $this->Convertdate(1,$_REQUEST["CREATEDT"]);
		$STARTDT 	= $this->Convertdate(1,$_REQUEST["STARTDT"]);
		$ENDDT 		= $this->Convertdate(1,$_REQUEST["ENDDT"]);
		$MEMO		= $_REQUEST["MEMO"];
		$USERID		= $this->sess["USERID"];
		$useredit	= $_REQUEST["useredit"];
		
		if($useredit == 'edit'){
			$USERID = 'XX';
		}else{
			$USERID = $USERID;
		}
		//echo $MEMO; exit;

		$sql = "
			if OBJECT_ID('tempdb..#AddALERTMSG') is not null drop table #AddALERTMSG;
			create table #AddALERTMSG (id varchar(20),contno varchar(20),msg varchar(max));
			
			begin tran AddALERTMSG
			begin try
			
				declare @CONTNO varchar(20) = '".$CONTNO."'
				declare @LOCAT varchar(10) =(select CRLOCAT from INVTRAN where CONTNO = '".$CONTNO."')
				
				if @CONTNO = (select distinct CONTNO from {$this->MAuth->getdb('ALERTMSG')} where CONTNO = @CONTNO and ENDDT >= '".$STARTDT."')
				begin 
					insert into #AddALERTMSG select 'W',@CONTNO,'มีข้อความที่ได้บันทึกในช่วงนี้แล้ว กรุณาสอบถามมาแก้ไขแทน';
				end
				else
				begin 
					insert into {$this->MAuth->getdb('ALERTMSG')} (CONTNO, LOCAT, CREATEDT, STARTDT, ENDDT, MEMO1, READERID, READDT, INPDT, USERID)
					values (@CONTNO, @LOCAT, '".$CREATEDT."', '".$STARTDT."', '".$ENDDT."', '".$MEMO."', '', NULL, getdate(), '".$USERID."')
					
					insert into #AddALERTMSG select 'S',@CONTNO,'บันทึกข้อความเตือน เลขที่สัญญา '+@CONTNO+' เรียบร้อย';
				end

				commit tran AddALERTMSG;
			end try
			begin catch
				rollback tran AddALERTMSG;
				insert into #AddALERTMSG select 'E','',ERROR_MESSAGE();
			end catch
		";
		//echo $sql; exit;
		
		$this->db->query($sql);
		$sql = "select * from #AddALERTMSG";
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
			$response["msg"] = 'ผิดพลาดไม่สามารถบันทึกข้อความเตือนได้ โปรดติดต่อฝ่ายไอที';
		}
		
		echo json_encode($response);
	}
	
	function Delete_alertmsg(){
		$CONTNO	= $_REQUEST["CONTNO"];
		$STARTDT = $this->Convertdate(1,$_REQUEST["STARTDT"]);
		$ENDDT = $this->Convertdate(1,$_REQUEST["ENDDT"]);
		$MEMO = $_REQUEST["MEMOold"];
		
		$sql = "
			if OBJECT_ID('tempdb..#DelALERTMSG') is not null drop table #DelALERTMSG;
			create table #DelALERTMSG (id varchar(20),contno varchar(20),msg varchar(max));
			
			begin tran DelALERTMSG
			begin try
				
				declare @CONTNO varchar(max) = '".$CONTNO."';
				
				delete {$this->MAuth->getdb('ALERTMSG')}
				where CONTNO = @CONTNO collate thai_cs_as and STARTDT = '".$STARTDT."' 
				and ENDDT = '".$ENDDT."' and MEMO1 like '".$MEMO."%'
				
				insert into {$this->MAuth->getdb('hp_UserOperationLog')} (userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
				values ('".$this->sess["IDNo"]."','SYS05::บันทึกข้อความเตือน (ลบ)',' ".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
				
				insert into #DelALERTMSG select 'S',@CONTNO,'ลบข้อความแจ้งเตือน เลขที่สัญญา '+@CONTNO+' เรียบร้อย';
				
				commit tran DelALERTMSG;
			end try
			begin catch
				rollback tran DelALERTMSG;
				insert into #DelALERTMSG select 'E','',ERROR_MESSAGE();
			end catch
		";
		//echo $sql; exit;
		
		$this->db->query($sql);
		$sql = "select * from #DelALERTMSG";
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
			$response["msg"] = 'ผิดพลาดไม่สามารถลบข้อความแจ้งเตือน โปรดติดต่อฝ่ายไอที';
		}
		
		echo json_encode($response);
	}
	
	function Edit_alertmsg(){
		$CONTNO	= $_REQUEST["CONTNO"];
		$STARTDT = $this->Convertdate(1,$_REQUEST["STARTDT"]);
		$ENDDT = $this->Convertdate(1,$_REQUEST["ENDDT"]);
		$MEMOold = $_REQUEST["MEMOold"];
		$MEMO = $_REQUEST["MEMO"];
		
		$sql = "
			if OBJECT_ID('tempdb..#UpdateALERTMSG') is not null drop table #UpdateALERTMSG;
			create table #UpdateALERTMSG (id varchar(20),contno varchar(20),msg varchar(max));
			
			begin tran UpdateALERTMSG
			begin try
				
				declare @CONTNO varchar(max) = '".$CONTNO."';
				
				update {$this->MAuth->getdb('ALERTMSG')}
				set MEMO1 = '".$MEMO."'
				where CONTNO = @CONTNO collate thai_cs_as and STARTDT = '".$STARTDT."' 
				and ENDDT = '".$ENDDT."' and MEMO1 like '".$MEMOold."%'
				
				insert into {$this->MAuth->getdb('hp_UserOperationLog')} (userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
				values ('".$this->sess["IDNo"]."','SYS05::บันทึกข้อความเตือน (แก้ไข)',' ".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
				
				insert into #UpdateALERTMSG select 'S',@CONTNO,'แก้ไขข้อความแจ้งเตือน เลขที่สัญญา '+@CONTNO+' เรียบร้อย';
				
				commit tran UpdateALERTMSG;
			end try
			begin catch
				rollback tran UpdateALERTMSG;
				insert into #UpdateALERTMSG select 'E','',ERROR_MESSAGE();
			end catch
		";
		//echo $sql; exit;
		
		$this->db->query($sql);
		$sql = "select * from #UpdateALERTMSG";
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
			$response["msg"] = 'ผิดพลาดไม่สามารถแก้ไขข้อความแจ้งเตือน โปรดติดต่อฝ่ายไอที';
		}
		
		echo json_encode($response);
	}
	
}