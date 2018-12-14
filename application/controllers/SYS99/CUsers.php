<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CUsers extends MY_Controller {
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
	
	function index(){
		$claim = $this->MLogin->getclaim(uri_string());
		if($claim['m_access'] != "T"){ echo "<div align='center' style='color:red;font-size:16pt;width:100%;'>ขออภัย คุณยังไม่มีสิทธิเข้าใช้งานหน้านี้ครับ</div>"; exit; }
		
		$getdb = $this->param('database');
		$selectdb = "";
		
		for($i=0;$i<sizeof($getdb);$i++){
			for($j=0;$j<sizeof($getdb[$i]);$j++){
				$selectdb .= "<option value='".$getdb[$i][$j]."' ".($getdb[$i][$j] == $this->sess['db'] ? 'selected':'').">".$getdb[$i][$j]."</option>";
			}
		}
		$selectdb .= "<option value='YTKManagement'>YTKManagement</option>";
		
		$html = "
			<div class='tab1' name='home' locat='{$this->sess['branch']}' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}' style='height:calc(100vh - 132px);overflow:auto;background-color:white;'>
				<div style='height:65px;overflow:auto;'>					
					<div class='col-xs-2 col-sm-1'>	
						<div class='form-group'>
							ห้อง
							<select id='dblocat' class='form-control input-sm chosen-select' data-placeholder='สถานะ'>
								".$selectdb."
								
								<!-- option value='HIC2SHORTL' selected>HIC2SHORTL</option>
								<option value='HIINCOME'>HIINCOME</option>
								<option value='HN'>HN</option>
								<option value='FN'>FN</option>
								<option value='RJYN'>RJYN</option>
								<option value='HRJYN'>HRJYN</option>
								<option value='FRJYN'>FRJYN</option>
								<option value='TJHON'>TJHON</option>
								<option value='HTJHON'>HTJHON</option>
								<option value='FTJHON'>FTJHON</option>
								<option value='TJPAT'>TJPAT</option>
								<option value='HTJPAT'>HTJPAT</option>
								<option value='FTJPAT'>FTJPAT</option>
								<option value='TJYL2556'>TJYL2556</option>
								<option value='HTJYL2556'>HTJYL2556</option>
								<option value='FTJYL2556'>FTJYL2556</option>
								<option value='TJYN'>TJYN</option>
								<option value='HTJYN'>HTJYN</option>
								<option value='FTJYN'>FTJYN</option>
								<option value='TJYN2004'>TJYN2004</option>
								<option value='HTJYN2004'>HTJYN2004</option>
								<option value='FTJYN2004'>FTJYN2004</option -->
							</select>
						</div>
					</div>
					<div class='col-xs-2 col-sm-1'>	
						<div class='form-group'>
							รหัส Senior
							<input type='text' id='USERID' class='form-control input-sm' placeholder='รหัส Senior' >
						</div>
					</div>
					
					<div class='col-xs-2 col-sm-2'>	
						<div class='form-group'>
							รหัส ปชช.
							<input type='text' id='IDNo' class='form-control input-sm' placeholder='รหัส ปชช.' >
						</div>
					</div>
					<div class='col-xs-2 col-sm-2'>	
						<div class='form-group'>
							ชื่อ-สกุล
							<input type='text' id='Name' class='form-control input-sm' placeholder='ชื่อ-สกุล' >
						</div>
					</div>
					<div class='col-xs-2 col-sm-1'>	
						<div class='form-group'>
							กลุ่มผู้ใช้งาน
							<select id='groupCode' class='form-control input-sm chosen-select' data-placeholder='สถานะ'>
								<option value=''>ทั้งหมด</option>
							</select>
						</div>
					</div>
					<div class='col-xs-2 col-sm-1'>	
						<div class='form-group'>
							<br>
							<input type='button' id='btnt1search' class='btn btn-primary btn-sm' value='แสดง' style='width:100%'>
						</div>
					</div>
					<div class='col-xs-2 col-sm-1'>	
						<div class='form-group'>
							<br>
							<input type='button' id='btnt1addUsers' class='btn btn-cyan btn-sm' value='เพิ่ม' style='width:100%'>
						</div>
					</div>
				</div>
				<div id='resultt1users' style='height:calc(100% - 65px);overflow:auto;background-color:white;'></div>
			</div>
			<div class='tab2' style='height:calc(100vh - 132px);width:100%;overflow:auto;background-color:white;'>
				<div id='resultt2users' class='col-sm-12' style='height:calc(100% - 65px);overflow:auto;'></div>
				<div id='resultt2footer' class='col-sm-12' style='height:30px;'>
					<div class='row'>
						<div class='col-sm-1'>	
							<div class='form-group'>
								<br>
								<input type='button' id='btnt2home' class='btn btn-inverse btn-sm' value='หน้าแรก' style='width:100%'>
							</div>
						</div>
						<div class='col-sm-1 col-sm-offset-10'>	
							<div class='form-group'>
								<br>
								<input type='button' id='btnt2save' class='btn btn-primary btn-sm' value='บันทึก' style='width:100%'>
							</div>
						</div>
					</div>
				</div>
			</div>
		";
		
		$html.= "<script src='".base_url('public/js/SYS99/CUsers.js')."'></script>";
		echo $html;
	}
	
	function search(){
		$arrs = array();
		$arrs['dblocat'] = $_REQUEST['dblocat'];
		$arrs['USERID'] = $_REQUEST['USERID'];
		$arrs['IDNo'] = $_REQUEST['IDNo'];
		$arrs['Name'] = $_REQUEST['Name'];
		$arrs['groupCode'] = $_REQUEST['groupCode'];
		
		$cond = "";
		if($arrs['dblocat'] != ""){
			//$cond .= " and a.dblocat like '".$arrs['dblocat']."%'";
		}
		
		if($arrs['USERID'] != ""){
			$cond .= " and a.USERID like '%".$arrs['USERID']."%'";
		}
		
		if($arrs['IDNo'] != ""){
			$cond .= " and b.IDNo like '%".$arrs['IDNo']."%'";
		}
		
		if($arrs['Name'] != ""){
			$cond .= " and c.titleName+c.firstName+' '+c.lastName+(case when isnull(c.nick,'')='' then '' else ' ('+c.nick+')' end) like '%".$arrs['Name']."%'";
		}
		
		if($arrs['groupCode'] != ""){
			$cond .= " and a.DEPCODE = '".$arrs['groupCode']."'";
		}
		
		$sql = "
			select a.USERID,c.employeeCode,c.IDNo
				,c.titleName+c.firstName+' '+c.lastName+(case when isnull(c.nick,'')='' then '' else ' ('+c.nick+')' end) as Name
				,a.DEPCODE,a.LEVEL_1,a.LOCAT,a.PASSWD 
				,RAND() * 10000 as OTP
			from {$arrs['dblocat']}.dbo.PASSWRD a
			left join YTKManagement.dbo.hp_mapusers b on a.USERID=b.USERID collate Thai_CI_AS and b.dblocat='{$arrs['dblocat']}'
			left join YTKManagement.dbo.hp_vusers c on b.IDNo=c.IDNo and b.employeeCode=c.employeeCode
			where 1=1 ".$cond."
			order by b.employeeCode desc
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		$NRow = 1;
		if($query->row()){
			foreach($query->result() as $row){
				$html .= "
					<tr class='trow' seq=".$NRow.">						
						<td class='getit' seq=".$NRow++." USERID='".$row->USERID."' style='width:50px;cursor:pointer;text-align:center;'>".$row->USERID."</td>
						<td>
							".$row->employeeCode."<br/>
							".$row->IDNo."<br/>
							".$row->Name."<br/>
						</td>
						<td>".$row->DEPCODE."</td>
						<td align='center'>".$row->LEVEL_1."</td>			
						<td>".$row->LOCAT."</td>			
						<td>".strtoupper(substr(md5(rand(10,100)),0,3).$row->PASSWD."P".substr(md5(rand(10,1000)),0,7))."</td>
					</tr>
				";
			}
		}
		
		$html = "
			<div id='table-fixed-CUsers' class='col-sm-12' style='height:100%;width:100%;overflow:auto;'>
				<table id='table-CUsers' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='100%'>
					<thead>
						<tr><th id='tab1dblocat' dblocat='".$arrs['dblocat']."' colspan='6'>".$arrs['dblocat']."</th></tr>
						<tr>
							<th style='vertical-align:middle;'>รหัส Senior</th>
							<th style='vertical-align:middle;'>รหัสพนักงาน<br>รหัส ปชช.<br>ชื่อ-สกุล</th>
							<th style='vertical-align:middle;'>กลุ่มผู้ใช้งาน</th>
							<th style='vertical-align:middle;'>ระดับ</th>
							<th style='vertical-align:middle;'>สาขา</th>
							<th style='vertical-align:middle;'>โค๊ด</th>
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
	
	function getDetails(){
		$arrs = array();
		$arrs['USERID'] = $_REQUEST['USERID'];
		$arrs['dblocat'] = $_REQUEST['dblocat'];		
		$arrs['cup'] = $_REQUEST['cup'];
		$arrs['clev'] = $_REQUEST['clev'];
		
		$sql = "
			select a.USERID,a.IDNo,a.employeeCode,a.dblocat,a.groupCode 
				,b.titleName+b.firstName+' '+b.lastName+(case when isnull(b.nick,'')='' then '' else ' ('+b.nick+')' end) as Name
			from YTKManagement.dbo.hp_mapusers a
			left join hp_vusers b on a.employeeCode=b.employeeCode and a.IDNo=b.IDNo
			where a.USERID='".$arrs['USERID']."' and a.dblocat='".$arrs['dblocat']."'
		";
		$query = $this->db->query($sql);
		
		$html = "";
		$NRow = 1;
		if($query->row()){
			foreach($query->result() as $row){
				$html .= "
					<tr class='trow' seq=".$NRow.">						
						<td class='getitclaim' seq=".$NRow++." USERID='".$row->USERID."' employeeCode='".$row->employeeCode."' style='width:100px;cursor:pointer;text-align:center;background-color:red;color:white;'>ยกเลิก</td>
						<td>".$row->employeeCode."</td>
						<td>".$row->IDNo."</td>
						<td>".$row->dblocat."</td>
						<td>".$row->groupCode."</td>
						<td>".$row->Name."</td>
					</tr>
				";
			}
		}
		
		$sql = "
			select USERID,dblocat,LOCATCD,action from YTKManagement.dbo.hp_maplocat 
			where USERID='".$arrs['USERID']."' and dblocat='".$arrs['dblocat']."'
			order by action desc,LOCATCD
		";
		$query = $this->db->query($sql);
		
		$locat = "";
		if($query->row()){
			foreach($query->result() as $row){
				$locat .= "
					<tr class='trow' seq=".$NRow." style='".($row->action == 'T' ? 'color:blue;' : '')."'>						
						<td class='getitlocat' seq=".$NRow++." USERID='".$row->USERID."' dblocat='".$row->dblocat."' LOCATCD='".$row->LOCATCD."' style='width:100px;cursor:pointer;text-align:center;background-color:red;color:white;'>ลบสิทธิ์</td>
						<td>".$row->LOCATCD."</td>
						<td>".($row->action == 'T' ? 'สาขาหลัก':'สาขารอง')."</td>
					</tr>
				";
			}
		}
		
		$html = "
			<div class='col-xs-2 col-sm-2'>	
				<div class='form-group'>
					รหัส senior
					<input type='text' id='t2USERID' class='form-control input-sm' readonly value='".$_REQUEST['USERID']."'>
				</div>
			</div>
			<div class='col-xs-2 col-sm-2'>	
				<div class='form-group'>
					รหัสพนักงาน
					<select id='t2mapusers' ".($html == '' ? '':' disabled ')." class='form-control input-sm chosen-select' data-placeholder='เลือก'><select>
				</div>
			</div>
			<div class='col-xs-2 col-sm-2'>	
				<div class='form-group'>
					กลุ่มผู้ใช้งาน
					<select id='t2groupCode' ".($html == '' ? '':' disabled ')." class='form-control input-sm chosen-select' data-placeholder='เลือก'><select>
				</div>
			</div>
			<div class='col-xs-2 col-sm-1'>	
				<div class='form-group'>
					<br>
					<input type='button' id='btnt2mapusers' class='btn btn-primary btn-sm' ".($html == '' ? '':' disabled ')." value='เพิ่มสิทธิ์' style='width:100%'>
				</div>
			</div>
			<div id='table-fixed-mapusers' class='col-sm-12' style='width:100%;overflow:auto;'>
				<table id='table-mapusers' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='100%'>
					<thead>
						<tr>
							<th style='vertical-align:middle;'>#</th>
							<th style='vertical-align:middle;'>รหัสพนักงาน</th>
							<th style='vertical-align:middle;'>รหัส ปชช.</th>
							<th style='vertical-align:middle;'>ห้อง</th>
							<th style='vertical-align:middle;'>กลุ่มผู้ใช้งาน</th>
							<th style='vertical-align:middle;'>ชื่อ-สกุล</th>
						</tr>
					</thead>	
					<tbody>						
						".$html."
					</tbody>
				</table>
			</div>
			<hr>
		
			<div class='col-xs-2 col-sm-2'>	
				<div class='form-group'>
					สาขา
					<select id='t2alocat' ".($html != '' ? '':' disabled ')." class='form-control input-sm chosen-select' data-placeholder='เลือก'><select>
				</div>
			</div>
			<div class='col-xs-2 col-sm-1'>	
				<div class='form-group'>
					สาขาหลัก
					<select id='t2amainlocat' ".($html != '' ? '':' disabled ')." class='form-control input-sm chosen-select input-block' data-placeholder='เลือก'>
						<option value='T'>ใช่</option>
						<option value='F'>ไม่ใช่</option>
					<select>
				</div>
			</div>
			<div class='col-xs-2 col-sm-1'>	
				<div class='form-group'>
					<br>
					<input type='button' id='btnt2addlocat' class='btn btn-primary btn-sm' ".($html != '' ? '':' disabled ')." value='เพิ่มสิทธิ์' style='width:100%'>
				</div>
			</div>
			<div id='table-fixed-locat' class='col-sm-12' style='width:100%;overflow:auto;'>
				<table id='table-locat' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='100%'>
					<thead>
						<tr>
							<th style='vertical-align:middle;'>#</th>
							<th style='vertical-align:middle;'>สาขา</th>
							<th style='vertical-align:middle;'>สถานะ</th>							
						</tr>
					</thead>	
					<tbody>						
						".$locat."
					</tbody>
				</table>
			</div>
		";
		
		
		$response = array("html"=>$html);
		echo json_encode($response);
	}
	
	function mapUsers(){
		$arrs = array();
		$arrs['employeeCode'] = $_REQUEST['employeeCode'];
		$arrs['USERID'] = $_REQUEST['USERID'];
		$arrs['groupCode'] = $_REQUEST['groupCode'];
		$arrs['dblocat'] = $_REQUEST['dblocat'];
		
		if($arrs['USERID'] == ''){
			$response = array();
			$response['status'] = false;
			$response['msg'] = 'ไม่พบข้อมูลรหัส senior โปรดตรวจสอบรายการใหม่อีกครั้งครับ';
			echo json_encode($response); exit;
		}
		
		if($arrs['employeeCode'] == ''){
			$response = array();
			$response['status'] = false;
			$response['msg'] = 'ไม่พบข้อมูลรหัสพนักงาน โปรดตรวจสอบรายการใหม่อีกครั้งครับ';
			echo json_encode($response); exit;
		}
		
		if($arrs['groupCode'] == ''){
			$response = array();
			$response['status'] = false;
			$response['msg'] = 'ไม่พบข้อมูลกลุ่มผู้ใช้งาน โปรดตรวจสอบรายการใหม่อีกครั้งครับ';
			echo json_encode($response); exit;
		}
		
		$sql = "
			if object_id('tempdb..#transaction') is not null drop table #transaction;
			create table #transaction (id varchar(20),msg varchar(max));

			begin tran ins
			begin try
				if((select count(*) from YTKManagement.dbo.hp_mapusers
				where employeeCode='".$arrs['employeeCode']."' and dblocat='".$arrs['dblocat']."') > 0)
				begin 
					rollback tran ins;
					insert into #transaction select 'n' as id,'ผิดพลาด รหัสพนักงาน ".$arrs['employeeCode']." ได้ mapusers ไปแล้ว' as msg;
					return;
				end 
				else 
				begin 
					insert into YTKManagement.dbo.hp_mapusers
					select '".$arrs['USERID']."','".$arrs['dblocat']."',IDNo,employeeCode
						,'".$arrs['groupCode']."','".$this->sess['IDNo']."',getdate() 
					from YTKManagement.dbo.hp_vusers 
					where employeeCode='".$arrs['employeeCode']."'
					
					update YTKManagement.dbo.hp_maplocat
					set action='F'
					where USERID='".$arrs['USERID']."' and dblocat = '".$arrs['dblocat']."'
					
					insert into YTKManagement.dbo.hp_maplocat
					select USERID,'".$arrs['dblocat']."',LOCAT,'T','".$this->sess['IDNo']."',GETDATE(),GETDATE() from {$arrs['dblocat']}.dbo.PASSWRD
					where USERID='".$arrs['USERID']."'
				end
				
				insert into {$this->MAuth->getdb('hp_UserOperationLog')} (userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
				values ('".$this->sess["IDNo"]."','SYS99::บันทึก mapusers','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
				
				insert into #transaction select 'y' as id,'Mapuser เรียบร้อยแล้ว' as msg;
				commit tran ins;
			end try
			begin catch
				rollback tran ins;
				insert into #transaction select 'n' as id,ERROR_MESSAGE() as msg;
			end catch
		";
		
		$this->db->query($sql);
	
		$sql = "select * from #transaction";   
		$query = $this->db->query($sql);
		$stat = true;
		$msg  = '';
		if ($query->row()) {
			foreach ($query->result() as $row) {
				$stat = ($row->id == "y" ? true : false);
				$msg = $row->msg;
			}
		}else{
			$stat = false;
			$msg = "ผิดพลาด :: ไม่สามารถทำรายการได้ในขณะนี้ โปรดลองทำรายการใหม่ภายหลัง";
		}
		
		$response = array();
		$response['status'] = $stat;
		$response['msg'] = $msg;
		echo json_encode($response);
	}
	
	function unmapUsers(){
		$arrs = array();
		$arrs['employeeCode'] = $_REQUEST['employeeCode'];
		$arrs['USERID'] = $_REQUEST['USERID'];
		$arrs['dblocat'] = $_REQUEST['dblocat'];
		
		$sql = "
			if object_id('tempdb..#transaction') is not null drop table #transaction;
			create table #transaction (id varchar(20),msg varchar(max));

			begin tran ins
			begin try
				delete YTKManagement.dbo.hp_mapusers
				where USERID='".$arrs['USERID']."' and employeeCode='".$arrs['employeeCode']."' and dblocat='".$arrs['dblocat']."'
				
				delete YTKManagement.dbo.hp_maplocat
				where USERID='".$arrs['USERID']."' and dblocat='".$arrs['dblocat']."'
				
				insert into {$this->MAuth->getdb('hp_UserOperationLog')} (userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
				values ('".$this->sess["IDNo"]."','SYS99::ยกเลิก mapusers','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
				
				insert into #transaction select 'y' as id,'ยกเลิกการ  mapuser เรียบร้อยแล้ว' as msg;
				commit tran ins;
			end try
			begin catch
				rollback tran ins;
				insert into #transaction select 'n' as id,ERROR_MESSAGE() as msg;
			end catch
		";
		
		$this->db->query($sql);
	
		$sql = "select * from #transaction";   
		$query = $this->db->query($sql);
		$stat = true;
		$msg  = '';
		if ($query->row()) {
			foreach ($query->result() as $row) {
				$stat = ($row->id == "y" ? true : false);
				$msg = $row->msg;
			}
		}else{
			$stat = false;
			$msg = "ผิดพลาด :: ไม่สามารถทำรายการได้ในขณะนี้ โปรดลองทำรายการใหม่ภายหลัง";
		}
		
		$response = array();
		$response['status'] = $stat;
		$response['msg'] = $msg;
		echo json_encode($response);
	}
	
	function addLOCATUsers(){
		$arrs = array();
		$arrs['USERID'] 	= $_REQUEST['USERID'];
		$arrs['dblocat'] 	= $_REQUEST['dblocat'];
		$arrs['LOCATCD'] 	= $_REQUEST['LOCATCD'];
		$arrs['mainlocat']  = $_REQUEST['mainlocat'];
		
		if($arrs['USERID'] == ''){
			$response = array();
			$response['status'] = false;
			$response['msg'] = 'ไม่พบข้อมูลรหัส senior โปรดตรวจสอบรายการใหม่อีกครั้งครับ';
			echo json_encode($response); exit;
		}
		
		if($arrs['dblocat'] == ''){
			$response = array();
			$response['status'] = false;
			$response['msg'] = 'ไม่พบข้อมูลห้อง โปรดตรวจสอบรายการใหม่อีกครั้งครับ';
			echo json_encode($response); exit;
		}
		
		if($arrs['LOCATCD'] == ''){
			$response = array();
			$response['status'] = false;
			$response['msg'] = 'ไม่พบข้อมูลสาขา โปรดตรวจสอบรายการใหม่อีกครั้งครับ';
			echo json_encode($response); exit;
		}
		
		if($arrs['mainlocat'] == ''){
			$arrs['mainlocat'] = 'F';
		}
		
		
		$sql = "
			if object_id('tempdb..#transaction') is not null drop table #transaction;
			create table #transaction (id varchar(20),msg varchar(max));

			begin tran ins
			begin try
				if((select count(*) from YTKManagement.dbo.hp_maplocat
				where USERID='".$arrs['USERID']."' and dblocat='".$arrs['dblocat']."' and LOCATCD='".$arrs['LOCATCD']."') > 0)
				begin 
					rollback tran ins;
					insert into #transaction select 'n' as id,'ผิดพลาด รหัสพนักงาน ".$arrs['USERID']." มีสิทธิ์สาขา ".$arrs['LOCATCD']." อยู่แล้ว' as msg;
					return;
				end 
				else 
				begin 
					if('".$arrs['mainlocat']."' = 'T')
					begin 
						update YTKManagement.dbo.hp_maplocat
						set action='F'
						where USERID='".$arrs['USERID']."' and dblocat='".$arrs['dblocat']."';
					end
					
					insert into YTKManagement.dbo.hp_maplocat
					select '".$arrs['USERID']."','".$arrs['dblocat']."','".$arrs['LOCATCD']."','".$arrs['mainlocat']."','".$this->sess['IDNo']."',GETDATE(),GETDATE();
				end 
				
				insert into {$this->MAuth->getdb('hp_UserOperationLog')} (userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
				values ('".$this->sess["IDNo"]."','SYS99::เพิ่มสิทธิ์ สาขา','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
				
				insert into #transaction select 'y' as id,'เพิ่มสิทธิ์ใช้งานสาขา  ".$arrs['LOCATCD']." เรียบร้อยแล้ว' as msg;
				commit tran ins;
			end try
			begin catch
				rollback tran ins;
				insert into #transaction select 'n' as id,ERROR_MESSAGE() as msg;
			end catch
		";
		
		$this->db->query($sql);
	
		$sql = "select * from #transaction";   
		$query = $this->db->query($sql);
		$stat = true;
		$msg  = '';
		if ($query->row()) {
			foreach ($query->result() as $row) {
				$stat = ($row->id == "y" ? true : false);
				$msg = $row->msg;
			}
		}else{
			$stat = false;
			$msg = "ผิดพลาด :: ไม่สามารถทำรายการได้ในขณะนี้ โปรดลองทำรายการใหม่ภายหลัง";
		}
		
		$response = array();
		$response['status'] = $stat;
		$response['msg'] = $msg;
		echo json_encode($response);
	}
	
	function delLOCATUsers(){
		$arrs = array();
		$arrs['USERID'] 	= $_REQUEST['USERID'];
		$arrs['dblocat'] 	= $_REQUEST['dblocat'];
		$arrs['LOCATCD'] 	= $_REQUEST['LOCATCD'];
		
		if($arrs['USERID'] == ''){
			$response = array();
			$response['status'] = false;
			$response['msg'] = 'ไม่พบข้อมูลรหัส senior โปรดตรวจสอบรายการใหม่อีกครั้งครับ';
			echo json_encode($response); exit;
		}
		
		if($arrs['dblocat'] == ''){
			$response = array();
			$response['status'] = false;
			$response['msg'] = 'ไม่พบข้อมูลห้อง โปรดตรวจสอบรายการใหม่อีกครั้งครับ';
			echo json_encode($response); exit;
		}
		
		if($arrs['LOCATCD'] == ''){
			$response = array();
			$response['status'] = false;
			$response['msg'] = 'ไม่พบข้อมูลสาขา โปรดตรวจสอบรายการใหม่อีกครั้งครับ';
			echo json_encode($response); exit;
		}
		
		$sql = "
			if object_id('tempdb..#transaction') is not null drop table #transaction;
			create table #transaction (id varchar(20),msg varchar(max));

			begin tran ins
			begin try
				delete YTKManagement.dbo.hp_maplocat
				where USERID='".$arrs['USERID']."' and dblocat='".$arrs['dblocat']."' and LOCATCD='".$arrs['LOCATCD']."'
				
				if((select count(*) from YTKManagement.dbo.hp_maplocat
					where USERID='".$arrs['USERID']."' and dblocat='".$arrs['dblocat']."' and action='T') = 0)
				begin
					update YTKManagement.dbo.hp_maplocat
					set action='T'
					where USERID='".$arrs['USERID']."' and dblocat='".$arrs['dblocat']."'
						and LOCATCD=(
							select top 1 LOCATCD from YTKManagement.dbo.hp_maplocat
							where USERID='".$arrs['USERID']."' and dblocat='".$arrs['dblocat']."'						
						);
				end
				
				insert into {$this->MAuth->getdb('hp_UserOperationLog')} (userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
				values ('".$this->sess["IDNo"]."','SYS99::ลบสิทธิ์ สาขา','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
				
				insert into #transaction select 'y' as id,'เพิ่มสิทธิ์ใช้งานสาขา  ".$arrs['LOCATCD']." เรียบร้อยแล้ว' as msg;
				commit tran ins;
			end try
			begin catch
				rollback tran ins;
				insert into #transaction select 'n' as id,ERROR_MESSAGE() as msg;
			end catch
		";
		
		$this->db->query($sql);
	
		$sql = "select * from #transaction";   
		$query = $this->db->query($sql);
		$stat = true;
		$msg  = '';
		if ($query->row()) {
			foreach ($query->result() as $row) {
				$stat = ($row->id == "y" ? true : false);
				$msg = $row->msg;
			}
		}else{
			$stat = false;
			$msg = "ผิดพลาด :: ไม่สามารถทำรายการได้ในขณะนี้ โปรดลองทำรายการใหม่ภายหลัง";
		}
		
		$response = array();
		$response['status'] = $stat;
		$response['msg'] = $msg;
		echo json_encode($response);
	}
}




















