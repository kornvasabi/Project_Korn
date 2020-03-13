<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/********************************************************
             _______________________
            / / _ _   _ _     __ 
           / // __ \ / __ \ / __ \
       _ _/ // /_/ // / / // /_/ /
     /_ _ _/ \_ _ //_/ /_/ \__  /
                          _ _/ /
                         /___ /
********************************************************/
class CHomenew extends MY_Controller {
	private $sess = array();
	
	function __construct()
	{
		parent::__construct();
		/*Additional code which you want to run automatically in every function call */
		
		$sess = $this->session->userdata('cbjsess001');
		if(!$sess){ redirect(base_url("welcome/index"),"_parent"); }else{
			foreach ($sess as $key => $value) {
				if($key == "lock" and $value == "yes"){
					redirect(base_url("clogout/lock"),"_parent");
				}
				
                $this->sess[$key] = $value;
            }
		}
	}
	
	function index(){
		$this->load->view('lobiLogin');
	}
	
	function TypeCar(){
		$claim = $this->MLogin->getclaim(uri_string());
		if($claim['m_access'] != "T"){ echo "<div align='center' style='color:red;font-size:16pt;width:100%;'>ขออภัย คุณยังไม่มีสิทธิเข้าใช้งานหน้านี้ครับ</div>"; exit; }
		
		$sql = "
			select distinct groupCode from YTKManagement.dbo.hp_mapusers
			where dblocat='".$this->sess["db"]."' and USERID = '".$this->sess["USERID"]."'
		";
	
		$query = $this->db->query($sql);
		
		$usergroup = "";
		if($query->row()){
			foreach($query->result() as $row){
				$usergroup .= $row->groupCode;
			}
		} 
		
		$html = "
			<div class='tab1 btab1' name='home' usergroup='".$usergroup."' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}' style='height:65px;overflow:auto;background-color:white;'>
				<div class='col-sm-12' style='overflow:auto;'>					
				<div class='row'>
					<div class='col-sm-2'>
						<div class='form-group'>
							สาขา
							<input type='text' id='inpLOCAT' class='form-control input-sm' value='".$this->sess['branch']."' placeholder='สาขา'>
						</div>
					</div>
					<div class='col-sm-2'>	
						<div class='form-group'>
							เลขที่สัญญา
							<input type='text' id='inpCONTNO' class='form-control input-sm' placeholder='เลขที่สัญญา'>
						</div>
					</div>
					<div class='col-sm-2'>	
						<div class='form-group'>
							เลขตัวถัง
							<input type='text' id='inpSTRNO' class='form-control input-sm' placeholder='เลขตัวถัง'>	
						</div>
					</div>
					<div class='col-sm-2'>	
						<div class='form-group'>
							ลูกค้า
							<select id='inpCUSCOD' class='form-control input-sm' data-placeholder='ลูกค้า'></select>
						</div>
					</div>
					<div class='col-sm-3'>	
						<div class='form-group'>
							สถานะรถ
							<select id='inpGCODES' class='form-control input-sm' data-placeholder='สถานะรถ'></select>
						</div>
					</div>
					<div class='col-sm-1'>	
						<div class='form-group'>
							<br>
							<input type='button' id='search_TypeCar' class='btn btn-primary btn-sm' value='แสดง' style='width:100%'>
						</div>
					</div>
				</div>
				</div>
			</div>
			<div id='result_TypeCar' class='col-sm-12 tab1' style='height:calc(100vh - 197px);overflow:auto;background-color:white;'></div>
			
			<div id='tab2_main' class='col-sm-12 tab2' hidden style='height:calc(100vh - 130px);overflow:auto;background-color:white;'></div>
		";
	
		$html.= "<script src='".base_url('public/js/typeCar.js')."'></script>";
		echo $html;
	}
	
	
	function getTypeCar(){
		$html = "";
		$cond = "";
		$cond1 = "";
		$ugroup = $_REQUEST["ugroup"];
		//echo $ugroup; exit;
		if(isset($_REQUEST["inpCONTNO"])){
			$cond .= " and isnull(a.CONTNO,'') like '%".$_REQUEST["inpCONTNO"]."%'";
		}
		
		if(isset($_REQUEST["inpSTRNO"])){
			$cond .= " and isnull(a.STRNO,'') like '%".$_REQUEST["inpSTRNO"]."%'";
		}
		
		if(isset($_REQUEST["inpLOCAT"])){
			$cond .= " and isnull(a.CRLOCAT,'') like '%".$_REQUEST["inpLOCAT"]."%'";
		}
		
		if(isset($_REQUEST["inpCUSCOD"])){
			$cond .= " and isnull(c.CUSCOD,'') like '%".$_REQUEST["inpCUSCOD"]."%'";
		}
		
		/*if(isset($_REQUEST["inpCUSNAME"])){
			$cond .= " and isnull(d.SNAM,'')+isnull(d.NAME1,'')+' '+isnull(d.NAME2,'') like '%".$_REQUEST["inpCUSNAME"]."%'";
		}*/
		
		if(isset($_REQUEST["inpGCODE"])){
			if($_REQUEST["inpGCODE"] == ''){
				$cond .= " and a.GCODE collate thai_ci_as in (select GCODE from {$this->MAuth->getdb('hp_groupuser_GCODE')} where groupCode='{$ugroup}')";
			}else{
				$cond .= " and a.GCODE = '".str_replace(chr(0),'',$_REQUEST["inpGCODE"])."' ";				
			}
		}else{
			$cond .= " and a.GCODE collate thai_ci_as in (select GCODE from {$this->MAuth->getdb('hp_groupuser_GCODE')} where groupCode='{$ugroup}')";
		}
		$top = ""; 
		if($_REQUEST["inpCONTNO"] == '' && $_REQUEST["inpCUSCOD"] == '' && $_REQUEST["inpSTRNO"] == '' && $_REQUEST["inpLOCAT"] == '' && $_REQUEST["inpGCODE"] == ''){
			$top = "top 100";
		}
		
		
		$sql = "
			select ".$top." a.STRNO,a.CONTNO,a.CRLOCAT
				,a.GCODE+'.'+b.GDESC as GCODE, a.GCODE as GCODES
				,case when isnull(c.CUSCOD,'') <> '' then c.CUSCOD else '' end as CUSCOD
				,case when isnull(c.CUSCOD,'') <> '' then d.SNAM+d.NAME1+' '+d.NAME2 else '' end as CUSNAME
			from {$this->MAuth->getdb('INVTRAN')} a 
			left join {$this->MAuth->getdb('SETGROUP')} b on a.GCODE=b.GCODE
			left join (
				select CONTNO,CUSCOD from {$this->MAuth->getdb('ARMAST')} 
				union
				select CONTNO,CUSCOD from {$this->MAuth->getdb('HARMAST')}
				union 
				select CONTNO,CUSCOD from {$this->MAuth->getdb('ARCRED')}
				union 
				select CONTNO,CUSCOD from {$this->MAuth->getdb('HARCRED')}
				union 
				select CONTNO,CUSCOD from {$this->MAuth->getdb('ARFINC')}
				union 
				select CONTNO,CUSCOD from {$this->MAuth->getdb('HARFINC')}
			) c on a.CONTNO=c.CONTNO
			left join {$this->MAuth->getdb('CUSTMAST')} d on c.CUSCOD=d.CUSCOD
			where 1=1 ".$cond." 
			order by a.CRLOCAT,a.CONTNO
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$NRow = 1;
		if($query->row()){
			foreach($query->result() as $row){
				$html .= "
					<tr class='trow' seq='".$NRow."'>
						<td class='getit' seq='".$NRow++."' STRNO='".$row->STRNO."' GCODES='".$row->GCODES."' style='width:50px;cursor:pointer;text-align:center;'><b>เลือก</b></td>
						<td>".$row->STRNO."</td>
						<td>".$row->CONTNO."</td>
						<td>".$row->CUSCOD."</td>
						<td>".$row->CUSNAME."</td>
						<td>".$row->CRLOCAT."</td>
						<td>".$row->GCODE."</td>
					</tr>
				";
			}
		}
		
		$html = "
			<div id='test' class='col-sm-12' style='height:100%;overflow:auto;'>
				<table id='data-table-example2' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='100%'>
					<thead>
						<tr>
							<th>#</th>
							<th>เลขตัวถัง</th>
							<th>เลขที่สัญญา</th>
							<th>รหัสลูกค้า</th>
							<th>ชื่อ-สกุล ลูกค้า</th>
							<th>สาขา</th>
							<th>กลุ่ม</th>
						</tr>
					</thead>	
					<tbody>
						".$html."				
					</tbody>
				</table>
			</div>
		";
		
		$response = array();
		$response['html'] = $html;
		echo json_encode($response);
	}
	
	function getFormChangeTypeCar(){
		$STRNO = $_REQUEST['STRNO'];
		$ugroups = $_REQUEST["ugroup"];
		
		/*
		$gcodes = "";
		if($ugroups != 'HP'){
			$gcodes = " and a.GCODE in ('','15','16','022','023','024','027','29','30','','15F','16F','22F','23F','24F','27F','29F','30F')";
		}else{
			$gcodes = "";
		}
		*/
		
		$sql = "
			select a.STRNO,a.CONTNO,a.CRLOCAT,a.STAT
				,a.GCODE,a.GCODE+'.'+b.GDESC as GCODENAME
				,case when isnull(c.CUSCOD,'') <> '' then c.CUSCOD else '' end as CUSCOD
				,case when isnull(c.CUSCOD,'') <> '' then d.SNAM+d.NAME1+' '+d.NAME2 else '' end as CUSNAME
			from {$this->MAuth->getdb('INVTRAN')} a 
			left join {$this->MAuth->getdb('SETGROUP')} b on a.GCODE=b.GCODE
			left join (
				select CONTNO,CUSCOD from {$this->MAuth->getdb('ARMAST')} 
				union
				select CONTNO,CUSCOD from {$this->MAuth->getdb('HARMAST')}
				union 
				select CONTNO,CUSCOD from {$this->MAuth->getdb('ARCRED')}
				union 
				select CONTNO,CUSCOD from {$this->MAuth->getdb('HARCRED')}
				union 
				select CONTNO,CUSCOD from {$this->MAuth->getdb('ARFINC')}
				union 
				select CONTNO,CUSCOD from {$this->MAuth->getdb('HARFINC')}
			) c on a.CONTNO=c.CONTNO
			left join {$this->MAuth->getdb('CUSTMAST')} d on c.CUSCOD=d.CUSCOD
			where a.STRNO='".$STRNO."' and a.GCODE collate thai_cs_as in (
				select GCODE from {$this->MAuth->getdb('hp_groupuser_GCODE')} 
				where groupCode='".$ugroups."'
			)
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$data = array();
		if($query->row()){
			foreach($query->result() as $row){
				$data['STRNO'] = $row->STRNO;
				$data['CONTNO'] = $row->CONTNO;
				$data['GCODE'] = str_replace(chr(0),'',$row->GCODE);
				$data['GCODENAME'] = str_replace(chr(0),'',$row->GCODENAME);
				$data['STAT'] = ( $row->STAT == 'N' ? $row->STAT.'.'.'รถใหม่' : $row->STAT.'.'.'รถเก่า' );
				$data['CUSCOD'] = $row->CUSCOD;
				$data['CUSNAME'] = $row->CUSNAME;
			}
		}
		
		
		$html = "
			<div class='col-sm-12  tbchangetypecode'>
				<div class='col-sm-6 col-sm-offset-3' style='height:calc(100vh - 260px);overflow:auto;'>
					<div class='col-sm-6'>	
						<div class='form-group'>
							เลขตัวถัง
							<input type='text' id='t2inpSTRNO' class='form-control input-sm' value='".$data['STRNO']."' readonly>
						</div>
					</div>
					
					<div class='col-sm-6'>	
						<div class='form-group'>
							เลขที่สัญญา
							<input type='text' id='t2inpCONTNO' class='form-control input-sm' value='".$data['CONTNO']."' readonly>
						</div>
					</div>
					
					<div class='col-sm-6'>	
						<div class='form-group'>
							รหัสลูกค้า
							<input type='text' id='t2inpCUSCOD' class='form-control input-sm' value='".$data['CUSCOD']."' readonly>
						</div>
					</div>
					
					<div class='col-sm-6'>	
						<div class='form-group'>
							ชื่อ-สกุล ลูกค้า
							<input type='text' id='t2inpCUSNAME' class='form-control input-sm' value='".$data['CUSNAME']."' readonly>
						</div>
					</div>
					
					<div class='col-sm-6'>	
						<div class='form-group'>
							สถานะรถ
							<input type='text' id='t2inpSTAT' class='form-control input-sm' value='".$data['STAT']."' readonly>
						</div>
					</div>
					
					<div class='col-sm-6'>	
						<div class='form-group'>
							กลุ่ม
							<input type='text' id='t2inpGCODE' class='form-control input-sm' data-value='".$data['GCODE']."' value='".$data['GCODENAME']."' readonly>
						</div>
					</div>
					<div class='col-sm-6'>	
						<div class='form-group'>
							เปลี่ยนเป็นกลุ่ม
							<select id='t2inpGCODENEW' class='form-control input-sm' data-placeholder='สถานะรถ'></select>
						</div>
					</div>
				</div>				
				
				<div class='col-sm-2 col-sm-offset-4'>
					<input type='button' id='tab2back' class='btn btn-danger btn-sm' style='width:100%;' value='ย้อนกลับ'>
				</div>
				<div class='col-sm-2'>
					<input type='button' id='tab2save' class='btn btn-primary btn-sm' style='width:100%;' value='บันทึก'>
				</div>
				
			</div>
			
		";
		
		$response = array();
		$response['html'] = $html;
		echo json_encode($response);
	}
	
	function setTypecars(){
		$strno = (isset($_REQUEST['STRNO']) ? $_REQUEST['STRNO']:'');
		$gcode = (isset($_REQUEST['GCODE']) ? $_REQUEST['GCODE']:'');
		$gcode = str_replace(chr(0),'',$gcode);
		//echo $gcode; exit;
		$response = array();
		if($strno == ''){
			$response['msg'] = 'ผิดพลาด ไม่พบข้อมูลเลขตัวถัง';
			$response['stat'] = false;
			echo json_encode($response); exit;
		}
		
		if($gcode == ''){
			$response['msg'] = 'ผิดพลาด โปรดระบุกลุ่มที่จะเปลี่ยนให้ถูกต้อง';
			$response['stat'] = false;
			echo json_encode($response); exit;
		}
		
		$sess = $this->session->userdata('cbjsess001');
		
		$sql = "
			if object_id('tempdb..#typeCar') is not null drop table #typeCar;
			create table #typeCar (id varchar(1),msg varchar(max));
			
			begin tran changeType
			begin try 
				insert into serviceweb.dbo.sn_invtranGCODELogs(STRNO,GCODE,GCODENew,insertBy,dt,ipAddress)
				select STRNO,GCODE,'".$gcode."','".$sess['IDNo']."',getdate(),'".$_SERVER['REMOTE_ADDR']."' from {$this->MAuth->getdb('INVTRAN')} where STRNO='".$strno."'
			
				insert into {$this->MAuth->getdb('INVTRANGCODE')} (STRNO,GCODE,GCODENew,dblocat,insby,insdt,ipAddress)
				select STRNO,GCODE,'".$gcode."','".$sess["db"]."','".$sess['IDNo']."',getdate(),'".$_SERVER['REMOTE_ADDR']."' from {$this->MAuth->getdb('INVTRAN')} where STRNO='".$strno."'
				
				update {$this->MAuth->getdb('INVTRAN')}
				set GCODE='".$gcode."'
				where STRNO='".$strno."'
				
				insert into {$this->MAuth->getdb('hp_UserOperationLog')} (userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
				values ('".$this->sess["IDNo"]."','SYS02::เปลี่ยนกลุ่มรถ','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
				
				insert into #typeCar select 'Y' as id,'สำเร็จ เลขตัวถัง ".$strno." เปลี่ยนกลุ่มเป็นกลุ่ม แล้ว' as msg;
				commit tran changeType;
			end try			
			begin catch
				rollback tran changeType;
				insert into #typeCar select 'N' as id,ERROR_MESSAGE() as msg;
			end catch
		";
		//echo $sql; exit;
		$this->db->query($sql);
		$sql = "select * from #typeCar";
		$query = $this->db->query($sql);
		
		if($query->row()){
			foreach($query->result() as $row){				
				$response["msg"] = $row->msg;
				$response["stat"] = ($row->id == 'Y' ? true : false);
			}
		}else{
			$response['msg'] = 'ผิดพลาด ไม่สามารถทำรายการได้ โปรดตรวจสอบข้อมูลใหม่อีกครั้ง';
			$response['stat'] = false;
		}
		
		echo json_encode($response); 
	}
	
	function LocatChangeView(){
		
		$sql = "
			select count(*) r from YTKManagement.dbo.hp_maplocat
			where USERID='".$this->sess["USERID"]."' and dblocat='".$this->sess["db"]."'
		";
		$query = $this->db->query($sql);
		
		$html = array();
		if($query->row()){
			foreach($query->result() as $row){
				$html["LOCATClaim"] = $row->r;
			}
		}else{
			$html["LOCATClaim"] = 0;
		}
		
		$sql = "
			select LOCATCD from YTKManagement.dbo.hp_maplocat
			where USERID='".$this->sess["USERID"]."' and dblocat='".$this->sess["db"]."'
		";
		$query = $this->db->query($sql);
		
		$html["data"] = "";
		if($query->row()){
			foreach($query->result() as $row){
				$html["data"] .= "
					<tr>
						<td>".$row->LOCATCD."</td>
						<td>".($row->LOCATCD == $this->sess["branch"] ? "" : "<input type='button' class='ChangeLOCAT' LOCAT='".$row->LOCATCD."' value='เปลี่ยน'>")."</td>
					</tr>
				";
			}
		}else{
			$html["data"] .= "
				<tr>
					<td colspan='2'>-</td>					
				</tr>
			";
		}
		
		$html["data"] = "
			<div id='table-fixed-changelocat' class='col-sm-12' style='height:100%;width:100%;overflow:auto;'>
				<table id='table-changelocat' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='100%'>
					<thead>
						<tr>
							<th>สาขา</th>
							<th style='width:80px;'>#</th>
						</tr>
					</thead>
					".$html["data"]."
				</table>
			</div>
		";
		
		$response = array();
		$response["html"] = $html;
		echo json_encode($response);
	}
	
	function LocatChange(){
		$LOCAT = $_REQUEST["LOCAT"];
		
		$sql = "
			if object_id('tempdb..#transaction') is not null drop table #transaction;
			create table #transaction (id varchar(20),msg varchar(max));

			begin tran ins
			begin try
				update YTKManagement.dbo.hp_maplocat
				set action = 'F'
				where USERID='".$this->sess["USERID"]."' and dblocat='".$this->sess["db"]."'
				
				update YTKManagement.dbo.hp_maplocat
				set action = 'T'
				where LOCATCD='".$LOCAT."' and USERID='".$this->sess["USERID"]."' and dblocat='".$this->sess["db"]."'
				
				insert into #transaction select 'y' as id,'เปลี่ยนเป็นสาขา ".$LOCAT." แล้ว' as msg;
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
		
		if($stat){
			$sess = $this->session->userdata('cbjsess001');
			$this->session->unset_userdata('cbjsess001');
			$sess_array = array(
				'employeeCode' => $sess['employeeCode'],
				'IDNo' => $sess['IDNo'],
				'USERID' => $sess['USERID'],
				'password' => $sess['password'],
				'name' => $sess['name'],
				'positionName' => $sess['positionName'],
				'corpName' => $sess['corpName'],
				'branch' => $LOCAT,
				'lock' => 'no',
				'is_mobile' => ($this->agent->is_mobile() == 1 ? "yes":"no"),
				'db' => $sess['db']
			);
			$this->session->set_userdata('cbjsess001', $sess_array);
		}
		
		$response = array();
		$response['status'] = $stat;
		$response['msg'] = $msg;
		echo json_encode($response);
	}
	
	function Help(){
		$url = explode('#',$_REQUEST["url"]);
		
		$sql = "
			select * from YTKManagement.dbo.hp_menu
			where menulink='".$url[1]."'
		";
		$query = $this->db->query($sql);
		
		$data = "";
		if($query->row()){
			foreach($query->result() as $row){
				$list = glob("public/help/".base64_encode($row->menuid).".pdf"); //ไปหาว่ามีไฟล์หรือไม่
				if(sizeof($list) > 0){
					$url = base_url("public/help/".base64_encode($row->menuid).".pdf?Help");
				}else{
					$url = base_url("public/help/none.pdf?".base64_encode($row->menuid));
				}
			}
		}
		
		$response=array("url"=>$url);
		echo json_encode($response);
	}
	
	function Rating(){
		$url = explode('#',$_REQUEST["url"]);
				
		$sql = "
			select menuid,menuname from YTKManagement.dbo.hp_menu
			where menulink='".$url[1]."'
		";
		$menu = $this->db->query($sql);		
		
		if($menu->row()){
			$menu = $menu->row();
		}else{
			$response = array("html"=>"หน้านี้ไม่สามารถประเมินได้ครับ","status"=>false);
			echo json_encode($response); exit;
		}
		
		$sql = "
			select IDNo,menuid,isnull(correct,0) as correct
				,isnull(easy,0) as easy,isnull(fast,0) as fast,comments 
			from YTKManagement.dbo.hp_rating
			where menuid = '".$menu->menuid."' and IDNo='".$this->sess["IDNo"]."'
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$html .= "
					<tr>
						<td>เนื้อหา ความถูกต้อง</td>
						<td>
							<div class='lobi-rating lobi-rating-warning'>
								<input class='starinput5' type='radio' value='5' name='correct' ".($row->correct == 5 ? "checked":"")."> 
								<label class='checkstar' level='5' dataname='correct'>
									<i class='fa fa-star'></i>
								</label>
								<input class='starinput4' type='radio' value='4' name='correct' ".($row->correct == 4 ? "checked":"")."> 
								<label class='checkstar' level='4' dataname='correct'>
									<i class='fa fa-star'></i>
								</label>
								<input class='starinput3' type='radio' value='3' name='correct' ".($row->correct == 3 ? "checked":"")."> 
								<label class='checkstar' level='3' dataname='correct'>
									<i class='fa fa-star'></i>
								</label>
								<input class='starinput2' type='radio' value='2' name='correct' ".($row->correct == 2 ? "checked":"")."> 
								<label class='checkstar' level='2' dataname='correct'>
									<i class='fa fa-star'></i>
								</label>
								<input class='starinput1' type='radio' value='1' name='correct' ".($row->correct == 1 ? "checked":"")."> 
								<label class='checkstar' level='1' dataname='correct'>
									<i class='fa fa-star'></i>
								</label>
							</div>
						</td>
					</tr>
					<tr>
						<td>ง่ายต่อการใช้งาน</td>
						<td>
							<div class='lobi-rating lobi-rating-warning'>
								<input class='starinput5' type='radio' value='5' name='easy' ".($row->easy == 5 ? "checked":"")."> 
								<label class='checkstar' level='5' dataname='easy'>
									<i class='fa fa-star'></i>
								</label>
								<input class='starinput4' type='radio' value='4' name='easy' ".($row->easy == 4 ? "checked":"").">
								<label class='checkstar' level='4' dataname='easy'>
									<i class='fa fa-star'></i>
								</label>
								<input class='starinput3' type='radio' value='3' name='easy' ".($row->easy == 3 ? "checked":"").">
								<label class='checkstar' level='3' dataname='easy'>
									<i class='fa fa-star'></i>
								</label>
								<input class='starinput2' type='radio' value='2' name='easy' ".($row->easy == 2 ? "checked":"").">
								<label class='checkstar' level='2' dataname='easy'>
									<i class='fa fa-star'></i>
								</label>
								<input class='starinput1' type='radio' value='1' name='easy' ".($row->easy == 1 ? "checked":"").">
								<label class='checkstar' level='1' dataname='easy'>
									<i class='fa fa-star'></i>
								</label>
							</div>
						</td>
					</tr>
					<tr>
						<td>ความรวดเร็วในการทำงาน</td>
						<td>
							<div class='lobi-rating lobi-rating-warning'>
								<input class='starinput5' type='radio' value='5' name='fast' ".($row->fast == 5 ? "checked":"")."> 
								<label class='checkstar' level='5' dataname='fast'>
									<i class='fa fa-star'></i>
								</label>
								<input class='starinput4' type='radio' value='4' name='fast' ".($row->fast == 4 ? "checked":"").">
								<label class='checkstar' level='4' dataname='fast'>
									<i class='fa fa-star'></i>
								</label>
								<input class='starinput3' type='radio' value='3' name='fast' ".($row->fast == 3 ? "checked":"").">
								<label class='checkstar' level='3' dataname='fast'>
									<i class='fa fa-star'></i>
								</label>
								<input class='starinput2' type='radio' value='2' name='fast' ".($row->fast == 2 ? "checked":"").">
								<label class='checkstar' level='2' dataname='fast'>
									<i class='fa fa-star'></i>
								</label>
								<input class='starinput1' type='radio' value='1' name='fast' ".($row->fast == 1 ? "checked":"").">
								<label class='checkstar' level='1' dataname='fast'>
									<i class='fa fa-star'></i>
								</label>
							</div>
						</td>
					</tr>
					<tr>
						<td colspan='2'>
							<div class='col-sm-12'>
								<div class='form-group'>
									ความเห็น 
									<textarea id='comments' class='form-control' row=3>".$row->comments."</textarea>
								</div>
							</div>
						</td>
					</tr>
					<tr>
						<td colspan='2'>
							<input type='button' id='sendRating' class='btn btn-cyan btn-sm btn-block' value='ส่ง' >
						</td>
					</tr>
				";
			}
		}else{
			$html .= "
				<tr>
					<td>เนื้อหา ความถูกต้อง</td>
					<td>
						<div class='lobi-rating lobi-rating-warning'>
							<input class='starinput5' type='radio' value='5' name='correct'> 
							<label class='checkstar' level='5' dataname='correct'>
								<i class='fa fa-star'></i>
							</label>
							<input class='starinput4' type='radio' value='4' name='correct'>
							<label class='checkstar' level='4' dataname='correct'>
								<i class='fa fa-star'></i>
							</label>
							<input class='starinput3' type='radio' value='3' name='correct'>
							<label class='checkstar' level='3' dataname='correct'>
								<i class='fa fa-star'></i>
							</label>
							<input class='starinput2' type='radio' value='2' name='correct'>
							<label class='checkstar' level='2' dataname='correct'>
								<i class='fa fa-star'></i>
							</label>
							<input class='starinput1' type='radio' value='1' name='correct'>
							<label class='checkstar' level='1' dataname='correct'>
								<i class='fa fa-star'></i>
							</label>
						</div>
					</td>
				</tr>
				<tr>
					<td>ง่ายต่อการใช้งาน</td>
					<td>
						<div class='lobi-rating lobi-rating-warning'>
							<input class='starinput5' type='radio' value='5' name='easy'> 
							<label class='checkstar' level='5' dataname='easy'>
								<i class='fa fa-star'></i>
							</label>
							<input class='starinput4' type='radio' value='4' name='easy'>
							<label class='checkstar' level='4' dataname='easy'>
								<i class='fa fa-star'></i>
							</label>
							<input class='starinput3' type='radio' value='3' name='easy'>
							<label class='checkstar' level='3' dataname='easy'>
								<i class='fa fa-star'></i>
							</label>
							<input class='starinput2' type='radio' value='2' name='easy'>
							<label class='checkstar' level='2' dataname='easy'>
								<i class='fa fa-star'></i>
							</label>
							<input class='starinput1' type='radio' value='1' name='easy'>
							<label class='checkstar' level='1' dataname='easy'>
								<i class='fa fa-star'></i>
							</label>
						</div>
					</td>
				</tr>
				<tr>
					<td>ความรวดเร็วในการทำงาน</td>
					<td>
						<div class='lobi-rating lobi-rating-warning'>
							<input class='starinput5' type='radio' value='5' name='fast'> 
							<label class='checkstar' level='5' dataname='fast'>
								<i class='fa fa-star'></i>
							</label>
							<input class='starinput4' type='radio' value='4' name='fast'>
							<label class='checkstar' level='4' dataname='fast'>
								<i class='fa fa-star'></i>
							</label>
							<input class='starinput3' type='radio' value='3' name='fast'>
							<label class='checkstar' level='3' dataname='fast'>
								<i class='fa fa-star'></i>
							</label>
							<input class='starinput2' type='radio' value='2' name='fast'>
							<label class='checkstar' level='2' dataname='fast'>
								<i class='fa fa-star'></i>
							</label>
							<input class='starinput1' type='radio' value='1' name='fast'>
							<label class='checkstar' level='1' dataname='fast'>
								<i class='fa fa-star'></i>
							</label>
						</div>
					</td>
				</tr>
				<tr>
					<td colspan='2'>
						<div class='col-sm-12'>
							<div class='form-group'>
								ความเห็น 
								<textarea id='comments' class='form-control' row=3></textarea>
							</div>
						</div>
					</td>
				</tr>
				<tr>
					<td colspan='2'>
						<input type='button' id='sendRating' class='btn btn-cyan btn-sm btn-block' value='ส่ง' >
					</td>
				</tr>
			";
		}
		
		$html = "
			<div id='table-fixed-CGroup' class='col-sm-12' style='height:100%;width:100%;overflow:auto;'>
				<table id='table-CGroup' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='100%'>
					<thead>
						<tr>
							<th colspan='2' class='thismenu text-center' style='color:blue;font-size:16pt;' menuid='".$menu->menuid."'>เมนู ".$menu->menuname."</th>
						</tr>
						<tr>
							<th style='vertical-align:middle;text-align:center;'>หัวข้อ</th>
							<th style='vertical-align:middle;text-align:center;'>คะแนน</th>
						</tr>
					</thead>	
					<tbody>						
						".$html."
					</tbody>
				</table>
			</div>
		";
		
		$response = array("html"=>$html,"status"=>true);
		echo json_encode($response);
	}
	
	function saveRating(){
		$arrs = array();
		$arrs["IDNo"]  	  = $this->sess["IDNo"];
		$arrs["menuid"]   = $_REQUEST["menuid"];
		$arrs["correct"]  = (!isset($_REQUEST["correct"]) ? 0 : $_REQUEST["correct"]);
		$arrs["easy"] 	  = (!isset($_REQUEST["easy"]) ? 0 : $_REQUEST["easy"]);
		$arrs["fast"] 	  = (!isset($_REQUEST["fast"]) ? 0 : $_REQUEST["fast"]);
		$arrs["comments"] = $_REQUEST["comments"];
		
		$sql = "
			if object_id('tempdb..#tempSave') is not null drop table #tempSave;
			create table #tempSave (id varchar(5),msg varchar(max));
			
			begin tran ins
			begin try
				declare @idno varchar(20) = '".$arrs["IDNo"]."';
				declare @menuid varchar(30) = '".$arrs["menuid"]."';
				declare @has int = (select count(*) from YTKManagement.dbo.hp_rating where IDNo=@idno and menuid=@menuid);
				
				if @has > 0
				begin 
					update YTKManagement.dbo.hp_rating
					set correct=".$arrs["correct"]."
						,easy=".$arrs["easy"]."
						,fast=".$arrs["fast"]."
						,comments='".$arrs["comments"]."'
					where IDNo=@idno and menuid=@menuid						
				end
				else 
				begin
					insert into YTKManagement.dbo.hp_rating
					select @idno,@menuid,".$arrs["correct"].",".$arrs["easy"].",".$arrs["fast"].",'".$arrs["comments"]."';
				end
				
				insert into #tempSave select 'Y','ขอบคุณ สำหรับการประเมินครับ';
				commit tran ins;
			end try
			begin catch
				rollback tran ins;
				insert into #tempSave select 'N',ERROR_MESSAGE();
			end catch
		";
		//echo $sql; exit;
		$this->db->query($sql);
		$sql = "select * from #tempSave";
		$query = $this->db->query($sql);
		
		$response = array();		
		if($query->row()){
			foreach($query->result() as $row){
				$response["status"] = ($row->id == "Y" ? true : false);
				$response["msg"] = $row->msg;				
			}
		}else{
			$response["status"] = false;
			$response["msg"] = "ผิดพลาด ไม่ทราบสาเหตุ";
		}
		
		echo json_encode($response);
	}
	
}




















