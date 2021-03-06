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
class CStock extends MY_Controller {
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
	
	public function group(){
		$claim = $this->MLogin->getclaim(uri_string());
		if($claim['m_access'] != "T"){ echo "<div align='center' style='color:red;font-size:16pt;width:100%;'>ขออภัย คุณยังไม่มีสิทธิเข้าใช้งานหน้านี้ครับ</div>"; exit; }
		
		$html = "
			<div class='tab1' name='home' locat='{$this->sess['branch']}' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}' >
				<div class='col-sm-2'>	
					<div class='form-group'>
						รหัสกลุ่ม
						<input type='text' id='gcode' class='form-control input-sm' placeholder='รหัสกลุ่ม'>
					</div>
				</div>
				<div class='col-sm-6'>	
					<div class='form-group'>
						ชื่อกลุ่ม
						<input type='text' id='gdesc' class='form-control input-sm' placeholder='ชื่อกลุ่ม'>	
					</div>
				</div>					
				<div class='col-sm-2'>	
					<div class='form-group'>
						<br>
						<input type='button' id='search_group' class='btn btn-primary btn-sm btn-block' value='แสดง'>
					</div>
				</div>
				<div class='col-sm-2'>	
					<div class='form-group'>
						<br>
						<input type='button' id='add_group' class='btn btn-cyan btn-sm btn-block' value='เพิ่ม' >
					</div>
				</div>
			</div>
			
			<div id='setgroupResult' class='col-sm-12 tab1' style='height:calc(100vh - 197px);overflow:auto;background-color:#;'></div>
			
			<div id='tab2_main' class='col-sm-12 tab2' hidden style='height:calc(100vh - 130px);overflow:auto;background-color:#;'></div>
		";
	
		$html.= "<script src='".base_url('public/js/setup/CStock/setgroup.js')."'></script>";
		echo $html;
	}
	
	public function groupSearch(){
		$arrs = array();
		$arrs['gcode'] = !isset($_REQUEST['gcode']) ? '' : $_REQUEST['gcode'];
		$arrs['gdesc'] = !isset($_REQUEST['gdesc']) ? '' : $_REQUEST['gdesc'];
		
		$cond = "";
		if($arrs['gcode'] != ''){
			$cond .= " and GCODE like '%".$arrs['gcode']."%'";
		}
		
		if($arrs['gdesc'] != ''){
			$cond .= " and GDESC like '%".$arrs['gdesc']."%'";
		}
		
		$sql = "
			select * from {$this->MAuth->getdb('setgroup')}
			where 1=1 ".$cond."
		";
		//echo $sql;exit;
		$query = $this->db->query($sql);
				
		$NRow = 1;
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$html .= "
					<tr class='trow' seq='".$NRow."'>
						<td class='getit' seq='".$NRow++."' GCODE='".str_replace(chr(0),'',$row->GCODE)."' style='width:50px;cursor:pointer;text-align:center;'><b>เลือก</b></td>
						<td>".str_replace(chr(0),'',$row->GCODE)."</td>
						<td>".str_replace(chr(0),'',$row->GDESC)."</td>
						<td>".str_replace(chr(0),'',$row->MEMO1)."</td>
					</tr>
				";
			}
		}
		
		$html = "
			<div id='tbScroll' class='col-sm-12' style='height:100%;overflow:auto;background-color:#eee;'>
				<table id='data-table-example2' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='100%'>
					<thead>
						<!-- tr>
							<th colspan='4' align='center'>
								<span style='cursor:pointer;'>Excel</span>
								<span style='cursor:pointer;'>PDF</span>
							</th>
						</tr -->
						<tr>
							<th>#</th>
							<th>รหัสกลุ่ม</th>
							<th>ชื่อกลุ่ม</th>
							<th>คำอธิบาย</th>							
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
	
	public function groupGetFormAE(){
		$arrs = array();
		$arrs['GCODE'] = (!isset($_REQUEST['GCODE']) ? '' : $_REQUEST['GCODE']);
		
		$data = array(
			'GCODE'=>'',
			'GDESC'=>'',
			'MEMO1'=>'',
		);
		if($arrs['GCODE'] != ''){
			$sql = "
				select * from {$this->MAuth->getdb('SETGROUP')}
				where GCODE='".$arrs['GCODE']."'
			";
			$query = $this->db->query($sql);
			
			if($query->row()){
				foreach($query->result() as $row){
					$data['GCODE'] = str_replace(chr(0),'',$row->GCODE);
					$data['GDESC'] = str_replace(chr(0),'',$row->GDESC);
					$data['MEMO1'] = str_replace(chr(0),'',$row->MEMO1);
				}
			}
		}
		
		$response = array();
		$response['html'] = "
			<div class='col-sm-12'>
				<div style='height:calc(100vh - 165px);overflow:auto;'>
					<div class='col-sm-4 col-sm-offset-4'>	
						<div class='form-group'>
							รหัสกลุ่ม
							<input type='text' id='t2gcode' class='form-control input-sm' value='".$data['GCODE']."' maxlength=3>
						</div>
					</div>
					
					<div class='col-sm-4 col-sm-offset-4'>	
						<div class='form-group'>
							ชื่อกลุ่ม
							<input type='text' id='t2gdesc' class='form-control input-sm' value='".$data['GDESC']."' maxlength=60>
						</div>
					</div>
					
					<div class='col-sm-4 col-sm-offset-4'>	
						<div class='form-group'>
							คำอธิบาย
							<textarea id='t2memo1' class='form-control input-sm' >".$data['MEMO1']."</textarea>
						</div>
					</div>
				</div>
				
				<div class='col-sm-1 col-sm-offset-5'>
					<input type='button' id='tab2back' class='btn btn-inverse btn-sm' style='width:100%;' value='ย้อนกลับ'>					
				</div>
				<div class='col-sm-1'>
					<input type='button' id='tab2del' class='btn btn-danger btn-sm' style='width:100%;' value='ลบ'>
				</div>
				<div class='col-sm-1'>
					<input type='button' id='tab2save' class='btn btn-primary btn-sm' style='width:100%;' value='บันทึก'>
				</div>
			</div>	
		";
		
		echo json_encode($response);
	}
	
	public function groupSave(){
		$arrs = array();
		$arrs['gcode'] = (!isset($_REQUEST['gcode'])?'':$_REQUEST['gcode']);
		$arrs['gdesc'] = (!isset($_REQUEST['gdesc'])?'':$_REQUEST['gdesc']);
		$arrs['memo1'] = (!isset($_REQUEST['memo1'])?'':$_REQUEST['memo1']);
		$arrs['action'] = (!isset($_REQUEST['action'])?'':$_REQUEST['action']);
		
		$data = "";
		if($arrs['action'] == 'add'){
			$data = "
				declare @isval int = isnull((select count(*) from {$this->MAuth->getdb('SETGROUP')} where GCODE='".$arrs['gcode']."'),0);
				if(@isval = 0)
				begin 
					insert into {$this->MAuth->getdb('SETGROUP')} (GCODE,GDESC,MEMO1)
					select '".$arrs['gcode']."','".$arrs['gdesc']."','".$arrs['memo1']."'
					
					insert into {$this->MAuth->getdb('hp_UserOperationLog')}(userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
					values ('".$this->sess["IDNo"]."','กลุ่มรถ เพิ่ม','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
				end 
				else
				begin 
					rollback tran tsc;
					insert into #tempolary select 'N' as id,'ไม่บันทึก : มีข้อมูลรหัสกลุ่ม ".$arrs['gcode']." อยู่แล้ว' as msg;
					return;
				end
			";
		}else{			
			$data = "
				update {$this->MAuth->getdb('SETGROUP')}
				set GDESC='".$arrs['gdesc']."'
					,MEMO1='".$arrs['memo1']."'
				where GCODE='".$arrs['gcode']."'
				
				insert into {$this->MAuth->getdb('hp_UserOperationLog')}(userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
				values ('".$this->sess["IDNo"]."','กลุ่มรถ แก้ไข','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
			";
		}
		
		$sql = "
			if object_id('tempdb..#tempolary') is not null drop table #tempolary;
			create table #tempolary (id varchar(1),msg varchar(max));
			
			begin tran tsc
			begin try			
				".$data."
				
				insert into #tempolary select 'Y' as id,'สำเร็จ บันทึกข้อมูลเรียบร้อยแล้ว' as msg;
				commit tran tsc;
			end try
			begin catch
				rollback tran tsc;
				insert into #tempolary select 'N' as id,'Fail : '+ERROR_MESSAGE() as msg;
			end catch
		";
		
		$this->db->query($sql);
		$sql = "select * from #tempolary";
		$query = $this->db->query($sql);
		
		$response = array();
		if($query->row()){
			foreach($query->result() as $row){
				$response['stat'] = ($row->id == 'Y' ? true:false);
				$response['msg'] = $row->msg;
			}
		}else{
			$response['stat'] = false;
			$response['msg'] = 'ผิดพลาด';
		}
		
		echo json_encode($response);
	}
	
	public function groupDel(){
		$arrs = array();
		$arrs['gcode'] = (!isset($_REQUEST['gcode'])?'':$_REQUEST['gcode']);
		$arrs['gdesc'] = (!isset($_REQUEST['gdesc'])?'':$_REQUEST['gdesc']);
		$arrs['memo1'] = (!isset($_REQUEST['memo1'])?'':$_REQUEST['memo1']);
		$arrs['action'] = (!isset($_REQUEST['action'])?'':$_REQUEST['action']);
		
		$sql = "
			if object_id('tempdb..#tempolary') is not null drop table #tempolary;
			create table #tempolary (id varchar(1),msg varchar(max));
			
			begin tran tsc
			begin try			
				delete {$this->MAuth->getdb('SETGROUP')}
				where GCODE='".$arrs['gcode']."'
				
				insert into {$this->MAuth->getdb('hp_UserOperationLog')}(userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
				values ('".$this->sess["IDNo"]."','กลุ่มรถ ลบ','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
				
				insert into #tempolary select 'Y' as id,'สำเร็จ ลบกลุ่ม ".$arrs['gcode'].$arrs['gdesc']."  รถแล้ว' as msg;
				commit tran tsc;
			end try
			begin catch
				rollback tran tsc;
				insert into #tempolary select 'N' as id,'Fail : '+ERROR_MESSAGE() as msg;
			end catch
		";
		
		$this->db->query($sql);
		$sql = "select * from #tempolary";
		$query = $this->db->query($sql);
		
		$response = array();
		if($query->row()){
			foreach($query->result() as $row){
				$response['stat'] = ($row->id == 'Y' ? true:false);
				$response['msg'] = $row->msg;
			}
		}else{
			$response['stat'] = false;
			$response['msg'] = 'ผิดพลาด';
		}
		
		echo json_encode($response);
	}
	
	public function type(){
		$claim = $this->MLogin->getclaim(uri_string());
		if($claim['m_access'] != "T"){ echo "<div align='center' style='color:red;font-size:16pt;width:100%;'>ขออภัย คุณยังไม่มีสิทธิเข้าใช้งานหน้านี้ครับ</div>"; exit; }
		
		$html = "
			<div class='tab1' name='home' locat='{$this->sess['branch']}' cin='{$claim['m_insert']}'  cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}' >
				<div class='col-sm-2'>	
					<div class='form-group'>
						ยี่ห้อ
						<input type='text' id='typecod' class='form-control input-sm' placeholder='ยี่ห้อ'>
					</div>
				</div>
				<div class='col-sm-2 col-sm-offset-6'>	
					<div class='form-group'>
						<br>
						<input type='button' id='search_type' class='btn btn-primary btn-sm btn-block' value='แสดง'>
					</div>
				</div>
				<div class='col-sm-2'>	
					<div class='form-group'>
						<br>
						<input type='button' id='add_type' class='btn btn-cyan btn-sm btn-block' value='เพิ่ม' >
					</div>
				</div>
			</div>
			<div id='settypeResult' class='col-sm-12 tab1' style='height:calc(100vh - 197px);overflow:auto;background-color:#;'></div>
			
			<div id='tab2_main' class='col-sm-12 tab2' hidden style='height:calc(100vh - 130px);overflow:auto;background-color:#;'></div>
		";
	
		$html.= "<script src='".base_url('public/js/setup/CStock/settype.js')."'></script>";
		echo $html;
	}
	
	function typeSearch(){
		$arrs = array();
		$arrs['typecod'] = !isset($_REQUEST['typecod']) ? '' : $_REQUEST['typecod'];
		
		$cond = "";
		if($arrs['typecod'] != ''){
			$cond .= " and TYPECOD like '%".$arrs['typecod']."%'";
		}
		
		$sql = "
			select * from {$this->MAuth->getdb('settype')}
			where 1=1 ".$cond."
		";
		//echo $sql;exit;
		$query = $this->db->query($sql);
				
		$NRow = 1;
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$html .= "
					<tr class='trow' seq='".$NRow."'>
						<td class='getit' seq='".$NRow++."' TYPECOD='".str_replace(chr(0),'',$row->TYPECOD)."' style='width:50px;cursor:pointer;text-align:center;'><b>เลือก</b></td>
						<td>".str_replace(chr(0),'',$row->TYPECOD)."</td>
						<td>".str_replace(chr(0),'',$row->MEMO1)."</td>
					</tr>
				";
			}
		}
		
		$html = "
			<div id='tbScroll' class='col-sm-12' style='height:100%;overflow:auto;background-color:#eee;'>
				<table id='data-table-example2' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='100%'>
					<thead>
						<tr>
							<th>#</th>
							<th>ยี่ห้อ</th>							
							<th>คำอธิบาย</th>							
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
	
	function typeGetFormAE(){
		$arrs = array();
		$arrs['TYPECOD'] = (!isset($_REQUEST['TYPECOD']) ? '' : $_REQUEST['TYPECOD']);
		
		$data = array(
			'TYPECOD'=>'',
			'MEMO1'=>'',
		);
		if($arrs['TYPECOD'] != ''){
			$sql = "
				select * from {$this->MAuth->getdb('SETTYPE')}
				where TYPECOD='".$arrs['TYPECOD']."'
			";
			$query = $this->db->query($sql);
			
			if($query->row()){
				foreach($query->result() as $row){
					$data['TYPECOD'] = str_replace(chr(0),'',$row->TYPECOD);
					$data['MEMO1'] = str_replace(chr(0),'',$row->MEMO1);
				}
			}
		}
		
		$response = array();
		$response['html'] = "
			<div class='col-sm-12'>
				<div style='height:calc(100vh - 165px);overflow:auto;'>
					<div class='col-sm-4 col-sm-offset-4'>	
						<div class='form-group'>
							<span class='text-red'>*</span> ยี่ห้อ
							<input type='text' id='t2typecod' class='form-control input-sm' value='".$data['TYPECOD']."' maxlength=12>
						</div>
					</div>
					
					<div class='col-sm-4 col-sm-offset-4'>	
						<div class='form-group'>
							คำอธิบาย
							<textarea id='t2memo1' class='form-control input-sm' >".$data['MEMO1']."</textarea>
						</div>
					</div>
				</div>
				
				<div class='col-sm-1 col-sm-offset-5'>
					<input type='button' id='tab2back' class='btn btn-inverse btn-sm' style='width:100%;' value='ย้อนกลับ'>					
				</div>
				<div class='col-sm-1'>
					<input type='button' id='tab2del' class='btn btn-danger btn-sm' style='width:100%;' value='ลบ'>
				</div>
				<div class='col-sm-1'>
					<input type='button' id='tab2save' class='btn btn-primary btn-sm' style='width:100%;' value='บันทึก'>
				</div>
			</div>	
		";
		
		echo json_encode($response);
	}
	
	function typeSave(){
		$arrs = array();
		$arrs['typecod'] = (!isset($_REQUEST['typecod'])?'':$_REQUEST['typecod']);
		$arrs['memo1'] = (!isset($_REQUEST['memo1'])?'':$_REQUEST['memo1']);
		$arrs['action'] = (!isset($_REQUEST['action'])?'':$_REQUEST['action']);
		
		$data = "";
		if($arrs['action'] == 'add'){
			$data = "
				declare @isval int = isnull((select count(*) from {$this->MAuth->getdb('SETTYPE')} where TYPECOD='".$arrs['typecod']."'),0);
				if(@isval = 0)
				begin 
					insert into {$this->MAuth->getdb('SETTYPE')} (TYPECOD,MEMO1)
					select '".$arrs['typecod']."',MEMO1='".$arrs['memo1']."'
					
					insert into {$this->MAuth->getdb('hp_UserOperationLog')}(userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
					values ('".$this->sess["IDNo"]."','ยี่ห้อ เพิ่ม','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
				end 
				else
				begin 
					rollback tran tsc;
					insert into #tempolary select 'N' as id,'ไม่บันทึก : มีข้อมูลยี่ห้อ  ".$arrs['typecod']." อยู่แล้ว' as msg;
					return;
				end
			";
		}else{			
			$data = "
				update {$this->MAuth->getdb('SETTYPE')}
				set TYPECOD='".$arrs['typecod']."',MEMO1='".$arrs['memo1']."'
				where TYPECOD='".$arrs['typecod']."'
				
				insert into {$this->MAuth->getdb('hp_UserOperationLog')}(userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
				values ('".$this->sess["IDNo"]."','ยี่ห้อ แก้ไข','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
			";
		}
		
		$sql = "
			if object_id('tempdb..#tempolary') is not null drop table #tempolary;
			create table #tempolary (id varchar(1),msg varchar(max));
			
			begin tran tsc
			begin try			
				".$data."
				
				insert into #tempolary select 'Y' as id,'สำเร็จ บันทึกข้อมูลเรียบร้อยแล้ว' as msg;
				commit tran tsc;
			end try
			begin catch
				rollback tran tsc;
				insert into #tempolary select 'N' as id,'Fail : '+ERROR_MESSAGE() as msg;
			end catch
		";
		//echo $sql; exit;
		$this->db->query($sql);
		$sql = "select * from #tempolary";
		$query = $this->db->query($sql);
		
		$response = array();
		if($query->row()){
			foreach($query->result() as $row){
				$response['stat'] = ($row->id == 'Y' ? true:false);
				$response['msg'] = $row->msg;
			}
		}else{
			$response['stat'] = false;
			$response['msg'] = 'ผิดพลาด';
		}
		
		echo json_encode($response);
	}
	
	function typeDel(){
		$arrs = array();
		$arrs['typecod'] = (!isset($_REQUEST['typecod'])?'':$_REQUEST['typecod']);
		$arrs['memo1'] = (!isset($_REQUEST['memo1'])?'':$_REQUEST['memo1']);
				
		$sql = "
			if object_id('tempdb..#tempolary') is not null drop table #tempolary;
			create table #tempolary (id varchar(1),msg varchar(max));
			
			begin tran tsc
			begin try			
				delete {$this->MAuth->getdb('SETTYPE')}
				where TYPECOD='".$arrs['typecod']."'
				
				insert into {$this->MAuth->getdb('hp_UserOperationLog')}(userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
				values ('".$this->sess["IDNo"]."','ยี่ห้อ ลบ','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
				
				insert into #tempolary select 'Y' as id,'สำเร็จ ลบยี่ห้อ ".$arrs['typecod']."  แล้ว' as msg;
				commit tran tsc;
			end try
			begin catch
				rollback tran tsc;
				insert into #tempolary select 'N' as id,'Fail : '+ERROR_MESSAGE() as msg;
			end catch
		";
		
		$this->db->query($sql);
		$sql = "select * from #tempolary";
		$query = $this->db->query($sql);
		
		$response = array();
		if($query->row()){
			foreach($query->result() as $row){
				$response['stat'] = ($row->id == 'Y' ? true:false);
				$response['msg'] = $row->msg;
			}
		}else{
			$response['stat'] = false;
			$response['msg'] = 'ผิดพลาด';
		}
		
		echo json_encode($response);
	}
	
	public function model(){
		$claim = $this->MLogin->getclaim(uri_string());
		if($claim['m_access'] != "T"){ echo "<div align='center' style='color:red;font-size:16pt;width:100%;'>ขออภัย คุณยังไม่มีสิทธิเข้าใช้งานหน้านี้ครับ</div>"; exit; }
		
		$html = "
			<div class='tab1' name='home' locat='{$this->sess['branch']}' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}' >
				<div class='col-sm-2'>	
					<div class='form-group'>
						ยี่ห้อ
						<input type='text' id='TYPECOD' class='form-control input-sm' placeholder='ยี่ห้อ'>
					</div>
				</div>
				<div class='col-sm-2'>	
					<div class='form-group'>
						รุ่น
						<input type='text' id='MODELCOD' class='form-control input-sm' placeholder='รุ่น'>	
					</div>
				</div>
				<div class='col-sm-2'>	
					<div class='form-group'>
						ขนาด
						<input type='text' id='CC' class='form-control input-sm' placeholder='ขนาด'>	
					</div>
				</div>
				<div class='col-sm-2'>	
					<div class='form-group'>
						ชื่อเรียก
						<input type='text' id='MODELDESC' class='form-control input-sm' placeholder='ชื่อเรียก'>	
					</div>
				</div>
				<div class='col-sm-2'>	
					<div class='form-group'>
						<br>
						<input type='button' id='search_model' class='btn btn-primary btn-sm btn-block' value='แสดง'>
					</div>
				</div>
				<div class='col-sm-2'>	
					<div class='form-group'>
						<br>
						<input type='button' id='add_model' class='btn btn-cyan btn-sm btn-block' value='เพิ่ม' >
					</div>
				</div>
			</div>
			<div id='setmodelResult' class='col-sm-12 tab1' style='height:calc(100vh - 197px);overflow:auto;background-color:#;'></div>
			
			<div id='tab2_main' class='col-sm-12 tab2' hidden style='height:calc(100vh - 130px);overflow:auto;background-color:#;'></div>
		";
	
		$html.= "<script src='".base_url('public/js/setup/CStock/setmodel.js')."'></script>";
		echo $html;
	}
	
	public function modelSearch(){
		$arrs = array();
		$arrs['TYPECOD'] = !isset($_REQUEST['TYPECOD']) ? '' : $_REQUEST['TYPECOD'];
		$arrs['MODELCOD'] = !isset($_REQUEST['MODELCOD']) ? '' : $_REQUEST['MODELCOD'];
		$arrs['CC'] = !isset($_REQUEST['CC']) ? '' : $_REQUEST['CC'];
		$arrs['MODELDESC'] = !isset($_REQUEST['MODELDESC']) ? '' : $_REQUEST['MODELDESC'];
		
		$cond = "";
		if($arrs['TYPECOD'] != ''){
			$cond .= " and a.TYPECOD like '%".$arrs['TYPECOD']."%'";
		}
		
		if($arrs['MODELCOD'] != ''){
			$cond .= " and a.MODELCOD like '%".$arrs['MODELCOD']."%'";
		}
		
		if($arrs['CC'] != ''){
			$cond .= " and a.CC like '%".$arrs['CC']."%'";
		}
		
		if($arrs['MODELDESC'] != ''){
			$cond .= " and b.MODELDESC like '%".$arrs['MODELDESC']."%'";
		}
		
		$sql = "
			select top 1000 a.TYPECOD,a.MODELCOD,a.CC,a.RCC
				,a.MEMO1,b.MODELDESC,b.MODELTYPE
			from {$this->MAuth->getdb('SETMODEL')} a 
			left join {$this->MAuth->getdb('SETMODELDESC')} b on a.MDDID=b.MDDID
			where 1=1 ".$cond."
			order by a.TYPECOD,a.CC,a.MODELCOD
		";
		//echo $sql;exit;
		$query = $this->db->query($sql);
				
		$NRow = 1;
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$html .= "
					<tr class='trow' seq='".$NRow."'>
						<td class='getit' seq='".$NRow++."' TYPECOD='".str_replace(chr(0),'',$row->TYPECOD)."' MODELCOD='".str_replace(chr(0),'',$row->MODELCOD)."' style='width:50px;cursor:pointer;text-align:center;'><b>เลือก</b></td>
						<td>".str_replace(chr(0),'',$row->TYPECOD)."</td>
						<td>".str_replace(chr(0),'',$row->MODELCOD)."</td>
						<td>".str_replace(chr(0),'',$row->CC)."</td>
						<td>".str_replace(chr(0),'',$row->RCC)."</td>
						<td>".str_replace(chr(0),'',$row->MODELDESC)."</td>
						<td>".str_replace(chr(0),'',$row->MODELTYPE)."</td>
						<td>".str_replace(chr(0),'',$row->MEMO1)."</td>
					</tr>
				";
			}
		}
		
		$html = "
			<div id='tbScroll' class='col-sm-12' style='height:100%;overflow:auto;background-color:#eee;'>
				<table id='data-table-example2' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='100%'>
					<thead>
						<tr>
							<th colspan='8'>แสดงข้อมูล 1,000 รายการแรก</th>							
						</tr>
						<tr>
							<th>#</th>
							<th>ยี่ห้อ</th>
							<th>รุ่น</th>
							<th>ขนาด</th>
							<th>ขนาดจริง</th>
							<th>ชื่อเรียก</th>
							<th>กลุ่ม</th>
							<th>คำอธิบาย</th>							
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
	
	public function modelGetFormAE(){
		$arrs = array();
		$arrs['TYPECOD'] = (!isset($_REQUEST['TYPECOD']) ? '' : $_REQUEST['TYPECOD']);
		$arrs['MODELCOD'] = (!isset($_REQUEST['MODELCOD']) ? '' : $_REQUEST['MODELCOD']);
		$arrs['MDDID'] = (!isset($_REQUEST['MDDID']) ? '' : $_REQUEST['MDDID']);
		
		$data = array(
			'TYPECOD'=>'',
			'MODELCOD'=>'',
			'CC'=>'',
			'RCC'=>'',
			'MEMO1'=>'',
			'MDDID'=>''
		);
		if($arrs['TYPECOD'] != '' and $arrs['MODELCOD'] != ''){
			$sql = "
				select a.* from {$this->MAuth->getdb('SETMODEL')} a 
				where a.TYPECOD='".$arrs['TYPECOD']."' and a.MODELCOD='".$arrs['MODELCOD']."'
			";
			$query = $this->db->query($sql);
			
			if($query->row()){
				foreach($query->result() as $row){
					$data['TYPECOD'] 	= str_replace(chr(0),'',$row->TYPECOD);
					$data['MODELCOD'] 	= str_replace(chr(0),'',$row->MODELCOD);
					$data['CC'] 		= str_replace(chr(0),'',$row->CC);
					$data['RCC'] 		= str_replace(chr(0),'',$row->RCC);
					$data['MEMO1'] 		= str_replace(chr(0),'',$row->MEMO1);
					$data['MDDID'] 		= str_replace(chr(0),'',$row->MDDID);
				}
			}
		}
		
		$response = array();
		$response['html'] = "
			<div class='col-sm-12'>
				<div style='height:calc(100vh - 165px);overflow:auto;'>
					<div class='col-sm-4 col-sm-offset-4'>	
						<div class='form-group'>
							<span class='text-red'>*</span> ยี่ห้อ
							<select id='t2TYPECOD' class='form-control input-sm' data-placeholder='ยี่ห้อ'>
								<option value='".$data['TYPECOD']."'>".$data['TYPECOD']."</option>
							</select>
						</div>
					</div>
					
					<div class='col-sm-4 col-sm-offset-4'>	
						<div class='form-group'>
							<span class='text-red'>*</span> รุ่น
							<input type='text' id='t2MODEL' class='form-control input-sm' model='".$data['MODELCOD']."' value='".$data['MODELCOD']."' maxlength=20>
						</div>
					</div>
					
					<div class='col-sm-4 col-sm-offset-4'>	
						<div class='form-group'>
							ขนาด
							<input type='text' id='t2CC' class='form-control input-sm' value='".$data['CC']."'>
						</div>
					</div>
					
					<div class='col-sm-4 col-sm-offset-4'>	
						<div class='form-group'>
							ขนาดจริง
							<input type='text' id='t2RCC' class='form-control input-sm' value='".$data['RCC']."'>
						</div>
					</div>
					
					<div class='col-sm-4 col-sm-offset-4'>	
						<div class='form-group'>
							ชื่อเรียก/กลุ่ม
							<select id='t2MDDID' class='form-control' title='เลือก'  data-actions-box='true' data-size='8' data-live-search='true'>
								".$this->MMAIN->Option_get_modeldesc($data['MDDID'])."
							</select>
						</div>
					</div>
					
					<div class='col-sm-4 col-sm-offset-4'>	
						<div class='form-group'>
							คำอธิบาย
							<textarea id='t2MEMO1' class='form-control input-sm' >".$data['MEMO1']."</textarea>
						</div>
					</div>
				</div>
				
				<div class='col-sm-1 col-sm-offset-5'>
					<input type='button' id='tab2back' class='btn btn-inverse btn-sm' style='width:100%;' value='ย้อนกลับ'>					
				</div>
				<div class='col-sm-1'>
					<input type='button' id='tab2del' class='btn btn-danger btn-sm' style='width:100%;' value='ลบ'>
				</div>
				<div class='col-sm-1'>
					<input type='button' id='tab2save' class='btn btn-primary btn-sm' style='width:100%;' value='บันทึก'>
				</div>
			</div>	
		";
		
		echo json_encode($response);
	}
	
	public function modelSave(){
		$arrs = array();
		$arrs['TYPECOD'] = (!isset($_REQUEST['TYPECOD'])?'':$_REQUEST['TYPECOD']);
		$arrs['OMODEL'] = (!isset($_REQUEST['OMODEL'])?'':$_REQUEST['OMODEL']);
		$arrs['MODEL'] = (!isset($_REQUEST['MODEL'])?'':$_REQUEST['MODEL']);
		$arrs['CC'] = (!isset($_REQUEST['CC'])?'null':($_REQUEST['CC'] == "" ? "null":$_REQUEST['CC']));
		$arrs['RCC'] = (!isset($_REQUEST['RCC'])?'null':($_REQUEST['RCC'] == "" ? "null":$_REQUEST['RCC']));
		$arrs['MEMO1'] = (!isset($_REQUEST['MEMO1'])?'':$_REQUEST['MEMO1']);
		$arrs['MDDID'] = (!isset($_REQUEST['MDDID'])?'':$_REQUEST['MDDID']);
		$arrs['action'] = (!isset($_REQUEST['action'])?'':$_REQUEST['action']);
		
		if($arrs['MDDID'] == "nouse" || $arrs['MDDID'] == ""){ $arrs['MDDID'] = "null"; }
		
		$data = "";
		if($arrs['action'] == 'add'){
			$data = "
				declare @isval int = isnull((
					select count(*) from {$this->MAuth->getdb('SETMODEL')}
					where TYPECOD='".$arrs['TYPECOD']."' and MODELCOD='".$arrs['MODEL']."')
				,0);
				
				if(@isval = 0)
				begin 
					insert into {$this->MAuth->getdb('SETMODEL')} (TYPECOD,MODELCOD,CC,RCC,MDDID,MEMO1)
					select '".$arrs['TYPECOD']."','".$arrs['MODEL']."',".$arrs['CC'].",".$arrs['RCC'].",".$arrs['MDDID'].",'".$arrs['MEMO1']."'
					
					insert into {$this->MAuth->getdb('hp_UserOperationLog')}(userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
					values ('".$this->sess["IDNo"]."','รุ่นรถ เพิ่ม','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
				end 
				else
				begin 
					rollback tran tsc;
					insert into #tempolary select 'N' as id,'ไม่บันทึก : มีข้อมูลรหัสรุ่นรถ ".$arrs['MODEL']." แล้ว' as msg;
					return;
				end
			";
		}else{			
			$data = "
				if exists( 
					select * from {$this->MAuth->getdb('SETMODEL')}
					where TYPECOD='".$arrs['TYPECOD']."' and MODELCOD='".$arrs['MODEL']."' and '".$arrs['OMODEL']."'!='".$arrs['MODEL']."'
				)
				begin 
					rollback tran tsc;
					insert into #tempolary select 'N' as id,'ผิดพลาด : ไม่สามารถแก้ไขจากรุ่น ".$arrs["OMODEL"]." เป็นรุ่น ".$arrs["MODEL"]." ได้ เนื่องจากมีรุ่น  ".$arrs["MODEL"]."  อยู่แล้ว' as msg;
					return;
				end 
				else 
				begin 
					update {$this->MAuth->getdb('SETMODEL')}
					set TYPECOD='".$arrs['TYPECOD']."'
						,MODELCOD='".$arrs['MODEL']."'
						,CC=".$arrs['CC']."
						,RCC=".$arrs['RCC']."
						,MEMO1='".$arrs['MEMO1']."'
						,MDDID=".$arrs['MDDID']."
					where TYPECOD='".$arrs['TYPECOD']."' and MODELCOD='".$arrs['OMODEL']."'
				end
				
				insert into {$this->MAuth->getdb('hp_UserOperationLog')}(userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
				values ('".$this->sess["IDNo"]."','รุ่นรถ แก้ไข','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
			";
		}
		
		$sql = "
			if object_id('tempdb..#tempolary') is not null drop table #tempolary;
			create table #tempolary (id varchar(1),msg varchar(max));
			
			begin tran tsc
			begin try			
				".$data."
				
				insert into #tempolary select 'Y' as id,'สำเร็จ บันทึกข้อมูลเรียบร้อยแล้ว' as msg;
				commit tran tsc;
			end try
			begin catch
				rollback tran tsc;
				insert into #tempolary select 'N' as id,'Fail : '+ERROR_MESSAGE() as msg;
			end catch
		";
		//echo $sql; exit;
		$this->db->query($sql);
		$sql = "select * from #tempolary";
		$query = $this->db->query($sql);
		
		$response = array();
		if($query->row()){
			foreach($query->result() as $row){
				$response['stat'] = ($row->id == 'Y' ? true:false);
				$response['msg'] = $row->msg;
			}
		}else{
			$response['stat'] = false;
			$response['msg'] = 'ผิดพลาด';
		}
		
		echo json_encode($response);
	}
	
	public function modelDel(){
		$arrs = array();
		$arrs['TYPECOD'] = (!isset($_REQUEST['TYPECOD'])?'':$_REQUEST['TYPECOD']);
		$arrs['MODEL'] = (!isset($_REQUEST['MODEL'])?'':$_REQUEST['MODEL']);
		$arrs['MEMO1'] = (!isset($_REQUEST['MEMO1'])?'':$_REQUEST['MEMO1']);
		$arrs['action'] = (!isset($_REQUEST['action'])?'':$_REQUEST['action']);
		
		$sql = "
			if object_id('tempdb..#tempolary') is not null drop table #tempolary;
			create table #tempolary (id varchar(1),msg varchar(max));
			
			begin tran tsc
			begin try			
				delete {$this->MAuth->getdb('SETMODEL')}
				where TYPECOD='".$arrs['TYPECOD']."' and MODELCOD='".$arrs['MODEL']."'
				
				insert into {$this->MAuth->getdb('hp_UserOperationLog')}(userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
				values ('".$this->sess["IDNo"]."','กลุ่มรถ ลบ','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
				
				insert into #tempolary select 'Y' as id,'สำเร็จ ลบรุ่นรถ ".$arrs['MODEL']."  แล้ว' as msg;
				commit tran tsc;
			end try
			begin catch
				rollback tran tsc;
				insert into #tempolary select 'N' as id,'Fail : '+ERROR_MESSAGE() as msg;
			end catch
		";
		
		$this->db->query($sql);
		$sql = "select * from #tempolary";
		$query = $this->db->query($sql);
		
		$response = array();
		if($query->row()){
			foreach($query->result() as $row){
				$response['stat'] = ($row->id == 'Y' ? true:false);
				$response['msg'] = $row->msg;
			}
		}else{
			$response['stat'] = false;
			$response['msg'] = 'ผิดพลาด';
		}
		
		echo json_encode($response);
	}
	
	#20191229 JDH
	public function baab(){
		$claim = $this->MLogin->getclaim(uri_string());
		if($claim['m_access'] != "T"){ echo "<div align='center' style='color:red;font-size:16pt;width:100%;'>ขออภัย คุณยังไม่มีสิทธิเข้าใช้งานหน้านี้ครับ</div>"; exit; }
		
		$html = "
			<div class='tab1' name='home' locat='{$this->sess['branch']}' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}' >
				<div class='col-sm-2'>	
					<div class='form-group'>
						ยี่ห้อ
						<input type='text' id='TYPECOD' class='form-control input-sm' placeholder='ยี่ห้อ'>
					</div>
				</div>
				<div class='col-sm-2'>	
					<div class='form-group'>
						รุ่น
						<input type='text' id='MODELCOD' class='form-control input-sm' placeholder='รุ่น'>	
					</div>
				</div>
				<div class='col-sm-2'>	
					<div class='form-group'>
						แบบ
						<input type='text' id='BAABCOD' class='form-control input-sm' placeholder='แบบ'>	
					</div>
				</div>				
				<div class='col-sm-2 col-sm-offset-2'>	
					<div class='form-group'>
						<br>
						<input type='button' id='search_baab' class='btn btn-primary btn-sm btn-block' value='แสดง'>
					</div>
				</div>
				<div class='col-sm-2'>	
					<div class='form-group'>
						<br>
						<input type='button' id='add_baab' class='btn btn-cyan btn-sm btn-block' value='เพิ่ม' >
					</div>
				</div>
			</div>
			<div id='setbaabResult' class='col-sm-12 tab1' style='height:calc(100vh - 197px);overflow:auto;background-color:#;'></div>
			
			<div id='tab2_main' class='col-sm-12 tab2' hidden style='height:calc(100vh - 130px);overflow:auto;background-color:#;'></div>
		";
	
		$html.= "<script src='".base_url('public/js/setup/CStock/setbaab.js')."'></script>";
		echo $html;
	}
	
	public function baabSearch(){
		$arrs = array();
		$arrs['TYPECOD'] 	= !isset($_REQUEST['TYPECOD']) ? '' : $_REQUEST['TYPECOD'];
		$arrs['MODELCOD'] 	= !isset($_REQUEST['MODELCOD']) ? '' : $_REQUEST['MODELCOD'];
		$arrs['BAABCOD'] 	= !isset($_REQUEST['BAABCOD']) ? '' : $_REQUEST['BAABCOD'];
		
		$cond = "";
		if($arrs['TYPECOD'] != ''){
			$cond .= " and TYPECOD like '%".$arrs['TYPECOD']."%'";
		}
		
		if($arrs['MODELCOD'] != ''){
			$cond .= " and MODELCOD like '%".$arrs['MODELCOD']."%'";
		}
		
		if($arrs['BAABCOD'] != ''){
			$cond .= " and BAABCOD like '%".$arrs['BAABCOD']."%'";
		}
		
		$sql = "
			select top 1000 * from {$this->MAuth->getdb('SETBAAB')}
			where 1=1 ".$cond."
			order by TYPECOD,MODELCOD,BAABCOD
		";
		//echo $sql;exit;
		$query = $this->db->query($sql);
				
		$NRow = 1;
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$html .= "
					<tr class='trow' seq='".$NRow."'>
						<td class='getit' seq='".$NRow++."' 
							TYPECOD='".str_replace(chr(0),'',$row->TYPECOD)."' 
							MODELCOD='".str_replace(chr(0),'',$row->MODELCOD)."' 
							BAABCOD='".str_replace(chr(0),'',$row->BAABCOD)."' 
							style='width:50px;cursor:pointer;text-align:center;'><b>เลือก</b></td>
						<td>".str_replace(chr(0),'',$row->TYPECOD)."</td>
						<td>".str_replace(chr(0),'',$row->MODELCOD)."</td>
						<td>".str_replace(chr(0),'',$row->BAABCOD)."</td>
						<td>".str_replace(chr(0),'',$row->MEMO1)."</td>
					</tr>
				";
			}
		}
		
		$html = "
			<div id='tbScroll' class='col-sm-12' style='height:100%;overflow:auto;background-color:#eee;'>
				<table id='data-table-example2' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='100%'>
					<thead>
						<tr>
							<th colspan='5'>แสดงข้อมูล 1,000 รายการแรก</th>							
						</tr>
						<tr>
							<th>#</th>
							<th>ยี่ห้อ</th>
							<th>รุ่น</th>
							<th>แบบ</th>
							<th>คำอธิบาย</th>							
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
	
	public function baabGetFormAE(){
		$arrs = array();
		$arrs['TYPECOD'] = (!isset($_REQUEST['TYPECOD']) ? '' : $_REQUEST['TYPECOD']);
		$arrs['MODELCOD'] = (!isset($_REQUEST['MODELCOD']) ? '' : $_REQUEST['MODELCOD']);
		$arrs['BAABCOD'] = (!isset($_REQUEST['BAABCOD']) ? '' : $_REQUEST['BAABCOD']);
		
		$data = array(
			'TYPECOD'=>'',
			'MODELCOD'=>'',
			'BAABCOD'=>'',
			'MEMO1'=>'',
		);
		if($arrs['TYPECOD'] != '' and $arrs['MODELCOD'] != '' and $arrs['BAABCOD'] != ''){
			$sql = "
				select * from {$this->MAuth->getdb('SETBAAB')}
				where TYPECOD='".$arrs['TYPECOD']."' 
					and MODELCOD='".$arrs['MODELCOD']."'
					and BAABCOD='".$arrs['BAABCOD']."'
			";
			$query = $this->db->query($sql);
			
			if($query->row()){
				foreach($query->result() as $row){
					$data['TYPECOD'] 	= str_replace(chr(0),'',$row->TYPECOD);
					$data['MODELCOD'] 	= str_replace(chr(0),'',$row->MODELCOD);
					$data['BAABCOD'] 	= str_replace(chr(0),'',$row->BAABCOD);
					$data['MEMO1'] 		= str_replace(chr(0),'',$row->MEMO1);
				}
			}
		}else{
			$data['TYPECOD'] 	= "HONDA";
		}
		
		$response = array();
		$response['html'] = "
			<div class='col-sm-12'>
				<div style='height:calc(100vh - 165px);overflow:auto;'>
					<div class='col-sm-4 col-sm-offset-4'>	
						<div class='form-group'>
							<span class='text-red'>*</span> ยี่ห้อ
							<select id='t2TYPECOD' TYPECOD='".$data['TYPECOD']."' class='form-control input-sm' data-placeholder='ยี่ห้อ'>
								<option value='".$data['TYPECOD']."'>".$data['TYPECOD']."</option>
							</select>
						</div>
					</div>
					
					<div class='col-sm-4 col-sm-offset-4'>	
						<div class='form-group'>
							<span class='text-red'>*</span> รุ่น
							<select id='t2MODEL' MODELCOD='".$data['MODELCOD']."' class='form-control input-sm' data-placeholder='รุ่น'>
								<option value='".$data['MODELCOD']."'>".$data['MODELCOD']."</option>
							</select>
						</div>
					</div>
					
					<div class='col-sm-4 col-sm-offset-4'>	
						<div class='form-group'>
							<span class='text-red'>*</span> แบบ
							<input type='text' id='t2BAAB' BAABCOD='".$data['BAABCOD']."' class='form-control input-sm' value='".$data['BAABCOD']."'  maxlength=20>
						</div>
					</div>
					
					<div class='col-sm-4 col-sm-offset-4'>	
						<div class='form-group'>
							คำอธิบาย
							<textarea id='t2MEMO1' class='form-control input-sm' >".$data['MEMO1']."</textarea>
						</div>
					</div>
				</div>
				
				<div class='col-sm-1 col-sm-offset-5'>
					<input type='button' id='tab2back' class='btn btn-inverse btn-sm' style='width:100%;' value='ย้อนกลับ'>					
				</div>
				<div class='col-sm-1'>
					<input type='button' id='tab2del' class='btn btn-danger btn-sm' style='width:100%;' value='ลบ'>
				</div>
				<div class='col-sm-1'>
					<input type='button' id='tab2save' class='btn btn-primary btn-sm' style='width:100%;' value='บันทึก'>
				</div>
			</div>	
		";
		
		echo json_encode($response);
	}
	
	public function baabSave(){
		$arrs = array();
		$arrs['TYPECOD'] = (!isset($_REQUEST['TYPECOD'])?'':$_REQUEST['TYPECOD']);
		$arrs['MODEL'] 	 = (!isset($_REQUEST['MODEL'])?'':$_REQUEST['MODEL']);
		$arrs['BAAB'] 	 = (!isset($_REQUEST['BAAB'])?'':$_REQUEST['BAAB']);
		
		$arrs['TYPECOD_OLD'] = (!isset($_REQUEST['TYPECOD_OLD'])?'':$_REQUEST['TYPECOD_OLD']);
		$arrs['MODEL_OLD'] 	 = (!isset($_REQUEST['MODEL_OLD'])?'':$_REQUEST['MODEL_OLD']);
		$arrs['BAAB_OLD'] 	 = (!isset($_REQUEST['BAAB_OLD'])?'':$_REQUEST['BAAB_OLD']);
		
		$arrs['MEMO1']	 = (!isset($_REQUEST['MEMO1'])?'':$_REQUEST['MEMO1']);
		$arrs['action']  = (!isset($_REQUEST['action'])?'':$_REQUEST['action']);
		
		$data = "";
		if($arrs['action'] == 'add'){
			$data = "
				declare @isval int = isnull((
					select count(*) from {$this->MAuth->getdb('SETBAAB')}
					where TYPECOD='".$arrs['TYPECOD']."' 
						and MODELCOD='".$arrs['MODEL']."'
						and BAABCOD='".$arrs['BAAB']."'
				),0);
				
				if(@isval = 0)
				begin 
					insert into {$this->MAuth->getdb('SETBAAB')} (TYPECOD,MODELCOD,BAABCOD,MEMO1)
					select '".$arrs['TYPECOD']."','".$arrs['MODEL']."','".$arrs['BAAB']."','".$arrs['MEMO1']."'
					
					insert into {$this->MAuth->getdb('hp_UserOperationLog')}(userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
					values ('".$this->sess["IDNo"]."','แบบรถ เพิ่ม','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
				end 
				else
				begin 
					rollback tran tsc;
					insert into #tempolary select 'N' as id,'ไม่บันทึก : มีข้อมูลรหัสรุ่น ".$arrs['MODEL']." แบบ  ".$arrs['BAAB']." แล้ว' as msg;
					return;
				end
			";
		}else{			
			$data = "
				update {$this->MAuth->getdb('SETBAAB')}
				set TYPECOD='".$arrs['TYPECOD']."'
					,MODELCOD='".$arrs['MODEL']."'
					,BAABCOD='".$arrs['BAAB']."'
					,MEMO1='".$arrs['MEMO1']."'
				where TYPECOD='".$arrs['TYPECOD_OLD']."' 
					and MODELCOD='".$arrs['MODEL_OLD']."'
					and BAABCOD='".$arrs['BAAB_OLD']."'
				
				insert into {$this->MAuth->getdb('hp_UserOperationLog')}(userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
				values ('".$this->sess["IDNo"]."','แบบรถ แก้ไข','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
			";
		}
		
		$sql = "
			if object_id('tempdb..#tempolary') is not null drop table #tempolary;
			create table #tempolary (id varchar(1),msg varchar(max));
			
			begin tran tsc
			begin try			
				".$data."
				
				insert into #tempolary select 'Y' as id,'สำเร็จ บันทึกข้อมูลเรียบร้อยแล้ว' as msg;
				commit tran tsc;
			end try
			begin catch
				rollback tran tsc;
				insert into #tempolary select 'N' as id,'Fail : '+ERROR_MESSAGE() as msg;
			end catch
		";
		
		$this->db->query($sql);
		$sql = "select * from #tempolary";
		$query = $this->db->query($sql);
		
		$response = array();
		if($query->row()){
			foreach($query->result() as $row){
				$response['stat'] = ($row->id == 'Y' ? true:false);
				$response['msg'] = $row->msg;
			}
		}else{
			$response['stat'] = false;
			$response['msg'] = 'ผิดพลาด';
		}
		
		echo json_encode($response);
	}
	
	public function baabDel(){
		$arrs = array();
		$arrs['TYPECOD'] = (!isset($_REQUEST['TYPECOD'])?'':$_REQUEST['TYPECOD']);
		$arrs['MODEL'] 	 = (!isset($_REQUEST['MODEL'])?'':$_REQUEST['MODEL']);
		$arrs['BAAB'] 	 = (!isset($_REQUEST['BAAB'])?'':$_REQUEST['BAAB']);
		
		$arrs['TYPECOD_OLD'] = (!isset($_REQUEST['TYPECOD_OLD'])?'':$_REQUEST['TYPECOD_OLD']);
		$arrs['MODEL_OLD'] 	 = (!isset($_REQUEST['MODEL_OLD'])?'':$_REQUEST['MODEL_OLD']);
		$arrs['BAAB_OLD'] 	 = (!isset($_REQUEST['BAAB_OLD'])?'':$_REQUEST['BAAB_OLD']);
		
		$arrs['MEMO1']	 = (!isset($_REQUEST['MEMO1'])?'':$_REQUEST['MEMO1']);
		$arrs['action']  = (!isset($_REQUEST['action'])?'':$_REQUEST['action']);
		
		$sql = "
			if object_id('tempdb..#tempolary') is not null drop table #tempolary;
			create table #tempolary (id varchar(1),msg varchar(max));
			
			begin tran tsc
			begin try			
				delete {$this->MAuth->getdb('SETBAAB')}
				where TYPECOD='".$arrs['TYPECOD_OLD']."' 
					and MODELCOD='".$arrs['MODEL_OLD']."'
					and BAABCOD='".$arrs['BAAB_OLD']."'
				
				insert into {$this->MAuth->getdb('hp_UserOperationLog')}(userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
				values ('".$this->sess["IDNo"]."','แบบรถ ลบ','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
				
				insert into #tempolary select 'Y' as id,'สำเร็จ ลบแบบ ".$arrs['BAAB_OLD']." ของรุ่นรถ ".$arrs['MODEL_OLD']."  แล้ว' as msg;
				commit tran tsc;
			end try
			begin catch
				rollback tran tsc;
				insert into #tempolary select 'N' as id,'Fail : '+ERROR_MESSAGE() as msg;
			end catch
		";
		
		$this->db->query($sql);
		$sql = "select * from #tempolary";
		$query = $this->db->query($sql);
		
		$response = array();
		if($query->row()){
			foreach($query->result() as $row){
				$response['stat'] = ($row->id == 'Y' ? true:false);
				$response['msg'] = $row->msg;
			}
		}else{
			$response['stat'] = false;
			$response['msg'] = 'ผิดพลาด';
		}
		
		echo json_encode($response);
	}
	
	#20191229 JDH
	public function color(){
		$claim = $this->MLogin->getclaim(uri_string());
		if($claim['m_access'] != "T"){ echo "<div align='center' style='color:red;font-size:16pt;width:100%;'>ขออภัย คุณยังไม่มีสิทธิเข้าใช้งานหน้านี้ครับ</div>"; exit; }
		
		$html = "
			<div class='tab1' name='home' locat='{$this->sess['branch']}' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}' >
				<div class='col-sm-2'>	
					<div class='form-group'>
						ยี่ห้อ
						<input type='text' id='TYPECOD' class='form-control input-sm' placeholder='ยี่ห้อ'>
					</div>
				</div>
				<div class='col-sm-2'>	
					<div class='form-group'>
						รุ่น
						<input type='text' id='MODELCOD' class='form-control input-sm' placeholder='รุ่น'>	
					</div>
				</div>
				<div class='col-sm-2'>	
					<div class='form-group'>
						แบบ
						<input type='text' id='BAABCOD' class='form-control input-sm' placeholder='แบบ'>	
					</div>
				</div>	
				<div class='col-sm-2'>	
					<div class='form-group'>
						สี
						<input type='text' id='COLORCOD' class='form-control input-sm' placeholder='สี'>	
					</div>
				</div>		
				<div class='col-sm-2'>	
					<div class='form-group'>
						<br>
						<input type='button' id='search_color' class='btn btn-primary btn-sm btn-block' value='แสดง'>
					</div>
				</div>
				<div class='col-sm-2'>	
					<div class='form-group'>
						<br>
						<input type='button' id='add_color' class='btn btn-cyan btn-sm btn-block' value='เพิ่ม' >
					</div>
				</div>
			</div>
			<div id='setcolorResult' class='col-sm-12 tab1' style='height:calc(100vh - 197px);overflow:auto;background-color:#;'></div>
			
			<div id='tab2_main' class='col-sm-12 tab2' hidden style='height:calc(100vh - 130px);overflow:auto;background-color:#;'></div>
		";
	
		$html.= "<script src='".base_url('public/js/setup/CStock/setcolor.js')."'></script>";
		echo $html;
	}
	
	public function colorSearch(){
		$arrs = array();
		$arrs['TYPECOD'] 	= !isset($_REQUEST['TYPECOD']) ? '' : $_REQUEST['TYPECOD'];
		$arrs['MODELCOD'] 	= !isset($_REQUEST['MODELCOD']) ? '' : $_REQUEST['MODELCOD'];
		$arrs['BAABCOD'] 	= !isset($_REQUEST['BAABCOD']) ? '' : $_REQUEST['BAABCOD'];
		$arrs['COLORCOD'] 	= !isset($_REQUEST['COLORCOD']) ? '' : $_REQUEST['COLORCOD'];
		
		$cond = "";
		if($arrs['TYPECOD'] != ''){
			$cond .= " and TYPECOD like '%".$arrs['TYPECOD']."%'";
		}
		
		if($arrs['MODELCOD'] != ''){
			$cond .= " and MODELCOD like '%".$arrs['MODELCOD']."%'";
		}
		
		if($arrs['BAABCOD'] != ''){
			$cond .= " and BAABCOD like '%".$arrs['BAABCOD']."%'";
		}
		
		if($arrs['COLORCOD'] != ''){
			$cond .= " and COLORCOD like '%".$arrs['COLORCOD']."%'";
		}
		
		$sql = "
			select top 1000 * from {$this->MAuth->getdb('JD_SETCOLOR')}
			where 1=1 ".$cond."
			order by TYPECOD,MODELCOD,BAABCOD
		";
		//echo $sql;exit;
		$query = $this->db->query($sql);
				
		$NRow = 1;
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$html .= "
					<tr class='trow' seq='".$NRow."'>
						<td class='getit' seq='".$NRow++."' 
							TYPECOD='".str_replace(chr(0),'',$row->TYPECOD)."' 
							MODELCOD='".str_replace(chr(0),'',$row->MODELCOD)."' 
							BAABCOD='".str_replace(chr(0),'',$row->BAABCOD)."' 
							COLORCOD='".str_replace(chr(0),'',$row->COLORCOD)."' 
							style='width:50px;cursor:pointer;text-align:center;'><b>เลือก</b></td>
						<td>".str_replace(chr(0),'',$row->TYPECOD)."</td>
						<td>".str_replace(chr(0),'',$row->MODELCOD)."</td>
						<td>".str_replace(chr(0),'',$row->BAABCOD)."</td>
						<td>".str_replace(chr(0),'',$row->COLORCOD)."</td>
						<td>".str_replace(chr(0),'',$row->MEMO1)."</td>
					</tr>
				";
			}
		}
		
		$html = "
			<div id='tbScroll' class='col-sm-12' style='height:100%;overflow:auto;background-color:#eee;'>
				<table id='data-table-example2' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='100%'>
					<thead>
						<tr>
							<th colspan='6'>แสดงข้อมูล 1,000 รายการแรก</th>							
						</tr>
						<tr>
							<th>#</th>
							<th>ยี่ห้อ</th>
							<th>รุ่น</th>
							<th>แบบ</th>
							<th>สีรถ</th>
							<th>คำอธิบาย</th>							
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
	
	public function colorGetFormAE(){
		$arrs = array();
		$arrs['TYPECOD'] 	= (!isset($_REQUEST['TYPECOD']) ? '' : $_REQUEST['TYPECOD']);
		$arrs['MODELCOD'] 	= (!isset($_REQUEST['MODELCOD']) ? '' : $_REQUEST['MODELCOD']);
		$arrs['BAABCOD'] 	= (!isset($_REQUEST['BAABCOD']) ? '' : $_REQUEST['BAABCOD']);
		$arrs['COLORCOD'] 	= (!isset($_REQUEST['COLORCOD']) ? '' : $_REQUEST['COLORCOD']);
		
		$data = array(
			'TYPECOD'=>'',
			'MODELCOD'=>'',
			'BAABCOD'=>'',
			'COLORCOD'=>'',
			'MEMO1'=>'',
		);
		if($arrs['TYPECOD'] != '' and $arrs['MODELCOD'] != '' and $arrs['BAABCOD'] != '' and $arrs['COLORCOD'] != ''){
			$sql = "
				select * from {$this->MAuth->getdb('JD_SETCOLOR')}
				where TYPECOD='".$arrs['TYPECOD']."' 
					and MODELCOD='".$arrs['MODELCOD']."'
					and BAABCOD='".$arrs['BAABCOD']."'
					and COLORCOD='".$arrs['COLORCOD']."'
			";
			$query = $this->db->query($sql);
			
			if($query->row()){
				foreach($query->result() as $row){
					$data['TYPECOD'] 	= str_replace(chr(0),'',$row->TYPECOD);
					$data['MODELCOD'] 	= str_replace(chr(0),'',$row->MODELCOD);
					$data['BAABCOD'] 	= str_replace(chr(0),'',$row->BAABCOD);
					$data['COLORCOD'] 	= str_replace(chr(0),'',$row->COLORCOD);
					$data['MEMO1'] 		= str_replace(chr(0),'',$row->MEMO1);
				}
			}
		}else{
			$data['TYPECOD'] 	= "HONDA";
		}
		
		$response = array();
		$response['html'] = "
			<div class='col-sm-12'>
				<div style='height:calc(100vh - 165px);overflow:auto;'>
					<div class='col-sm-4 col-sm-offset-4'>	
						<div class='form-group'>
							<span class='text-red'>*</span> ยี่ห้อ
							<select id='t2TYPECOD' TYPECOD='".$data['TYPECOD']."' class='form-control input-sm' data-placeholder='ยี่ห้อ'>
								<option value='".$data['TYPECOD']."'>".$data['TYPECOD']."</option>
							</select>
						</div>
					</div>
					
					<div class='col-sm-4 col-sm-offset-4'>	
						<div class='form-group'>
							<span class='text-red'>*</span> รุ่น
							<select id='t2MODEL' MODELCOD='".$data['MODELCOD']."' class='form-control input-sm' data-placeholder='รุ่น'>
								<option value='".$data['MODELCOD']."'>".$data['MODELCOD']."</option>
							</select>
						</div>
					</div>
					
					<div class='col-sm-4 col-sm-offset-4'>	
						<div class='form-group'>
							<span class='text-red'>*</span> แบบ
							<select id='t2BAAB' BAABCOD='".$data['BAABCOD']."' class='form-control input-sm' data-placeholder='แบบ'>
								<option value='".$data['BAABCOD']."'>".$data['BAABCOD']."</option>
							</select>
						</div>
					</div>
					
					<div class='col-sm-4 col-sm-offset-4'>	
						<div class='form-group'>
							<span class='text-red'>*</span> สี
							<input type='text' id='t2COLOR' COLORCOD='".$data['COLORCOD']."' class='form-control input-sm' value='".$data['COLORCOD']."'  maxlength=20>
						</div>
					</div>
					
					<div class='col-sm-4 col-sm-offset-4'>	
						<div class='form-group'>
							คำอธิบาย
							<textarea id='t2MEMO1' class='form-control input-sm' >".$data['MEMO1']."</textarea>
						</div>
					</div>
				</div>
				
				<div class='col-sm-1 col-sm-offset-4'>
					<input type='button' id='tab2back' class='btn btn-inverse btn-sm' style='width:100%;' value='ย้อนกลับ'>					
				</div>
				<div class='col-sm-1'>
					<input type='button' id='tab2Import' class='btn btn-info btn-sm' style='width:100%;' value='นำเข้า'>
				</div>
				<div class='col-sm-1'>
					<input type='button' id='tab2del' class='btn btn-danger btn-sm' style='width:100%;' value='ลบ'>
				</div>
				<div class='col-sm-1'>
					<input type='button' id='tab2save' class='btn btn-primary btn-sm' style='width:100%;' value='บันทึก'>
				</div>
			</div>	
		";
		
		echo json_encode($response);
	}
	
	public function colorSave(){
		$arrs = array();
		$arrs['TYPECOD'] = (!isset($_REQUEST['TYPECOD'])?'':$_REQUEST['TYPECOD']);
		$arrs['MODEL'] 	 = (!isset($_REQUEST['MODEL'])?'':$_REQUEST['MODEL']);
		$arrs['BAAB'] 	 = (!isset($_REQUEST['BAAB'])?'':$_REQUEST['BAAB']);
		$arrs['COLOR'] 	 = (!isset($_REQUEST['BAAB'])?'':$_REQUEST['COLOR']);
		
		$arrs['TYPECOD_OLD'] = (!isset($_REQUEST['TYPECOD_OLD'])?'':$_REQUEST['TYPECOD_OLD']);
		$arrs['MODEL_OLD'] 	 = (!isset($_REQUEST['MODEL_OLD'])?'':$_REQUEST['MODEL_OLD']);
		$arrs['BAAB_OLD'] 	 = (!isset($_REQUEST['BAAB_OLD'])?'':$_REQUEST['BAAB_OLD']);
		$arrs['COLOR_OLD'] 	 = (!isset($_REQUEST['BAAB'])?'':$_REQUEST['COLOR_OLD']);
		
		$arrs['MEMO1']	 = (!isset($_REQUEST['MEMO1'])?'':$_REQUEST['MEMO1']);
		$arrs['action']  = (!isset($_REQUEST['action'])?'':$_REQUEST['action']);
		
		$data = "";
		if($arrs['action'] == 'add'){
			$data = "
				declare @isval int = isnull((
					select count(*) from {$this->MAuth->getdb('JD_SETCOLOR')}
					where TYPECOD='".$arrs['TYPECOD']."' 
						and MODELCOD='".$arrs['MODEL']."'
						and BAABCOD='".$arrs['BAAB']."'
						and COLORCOD='".$arrs['COLOR']."'
				),0);
				
				if(@isval = 0)
				begin 
					insert into {$this->MAuth->getdb('JD_SETCOLOR')} (TYPECOD,MODELCOD,BAABCOD,COLORCOD,MEMO1)
					select '".$arrs['TYPECOD']."','".$arrs['MODEL']."','".$arrs['BAAB']."','".$arrs['COLOR']."','".$arrs['MEMO1']."'
					
					insert into {$this->MAuth->getdb('hp_UserOperationLog')}(userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
					values ('".$this->sess["IDNo"]."','สีรถ เพิ่ม','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
				end 
				else
				begin 
					rollback tran tsc;
					insert into #tempolary select 'N' as id,'ไม่บันทึก : มีข้อมูลรหัสรุ่น ".$arrs['MODEL']." แบบ  ".$arrs['BAAB']."  สี ".$arrs['COLOR_OLD']." แล้ว' as msg;
					return;
				end
			";
		}else{			
			$data = "
				update {$this->MAuth->getdb('JD_SETCOLOR')}
				set TYPECOD='".$arrs['TYPECOD']."'
					,MODELCOD='".$arrs['MODEL']."'
					,BAABCOD='".$arrs['BAAB']."'
					,COLORCOD='".$arrs['COLOR']."'
					,MEMO1='".$arrs['MEMO1']."'
				where TYPECOD='".$arrs['TYPECOD_OLD']."' 
					and MODELCOD='".$arrs['MODEL_OLD']."'
					and BAABCOD='".$arrs['BAAB_OLD']."'
					and COLORCOD='".$arrs['COLOR_OLD']."'
				
				insert into {$this->MAuth->getdb('hp_UserOperationLog')}(userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
				values ('".$this->sess["IDNo"]."','สีรถ แก้ไข','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
			";
		}
		
		$sql = "
			if object_id('tempdb..#tempolary') is not null drop table #tempolary;
			create table #tempolary (id varchar(1),msg varchar(max));
			
			begin tran tsc
			begin try			
				".$data."
				
				insert into #tempolary select 'Y' as id,'สำเร็จ บันทึกข้อมูลเรียบร้อยแล้ว' as msg;
				commit tran tsc;
			end try
			begin catch
				rollback tran tsc;
				insert into #tempolary select 'N' as id,'Fail : '+ERROR_MESSAGE() as msg;
			end catch
		";
		
		$this->db->query($sql);
		$sql = "select * from #tempolary";
		$query = $this->db->query($sql);
		
		$response = array();
		if($query->row()){
			foreach($query->result() as $row){
				$response['stat'] = ($row->id == 'Y' ? true:false);
				$response['msg'] = $row->msg;
			}
		}else{
			$response['stat'] = false;
			$response['msg'] = 'ผิดพลาด';
		}
		
		echo json_encode($response);
	}
	
	public function colorDel(){
		$arrs = array();
		$arrs['TYPECOD'] = (!isset($_REQUEST['TYPECOD'])?'':$_REQUEST['TYPECOD']);
		$arrs['MODEL'] 	 = (!isset($_REQUEST['MODEL'])?'':$_REQUEST['MODEL']);
		$arrs['BAAB'] 	 = (!isset($_REQUEST['BAAB'])?'':$_REQUEST['BAAB']);
		$arrs['COLOR'] 	 = (!isset($_REQUEST['BAAB'])?'':$_REQUEST['COLOR']);
		
		$arrs['TYPECOD_OLD'] = (!isset($_REQUEST['TYPECOD_OLD'])?'':$_REQUEST['TYPECOD_OLD']);
		$arrs['MODEL_OLD'] 	 = (!isset($_REQUEST['MODEL_OLD'])?'':$_REQUEST['MODEL_OLD']);
		$arrs['BAAB_OLD'] 	 = (!isset($_REQUEST['BAAB_OLD'])?'':$_REQUEST['BAAB_OLD']);
		$arrs['COLOR_OLD'] 	 = (!isset($_REQUEST['BAAB'])?'':$_REQUEST['COLOR_OLD']);
		
		$arrs['MEMO1']	 = (!isset($_REQUEST['MEMO1'])?'':$_REQUEST['MEMO1']);
		$arrs['action']  = (!isset($_REQUEST['action'])?'':$_REQUEST['action']);
		
		$sql = "
			if object_id('tempdb..#tempolary') is not null drop table #tempolary;
			create table #tempolary (id varchar(1),msg varchar(max));
			
			begin tran tsc
			begin try			
				delete {$this->MAuth->getdb('JD_SETCOLOR')}
				where TYPECOD='".$arrs['TYPECOD_OLD']."' 
					and MODELCOD='".$arrs['MODEL_OLD']."'
					and BAABCOD='".$arrs['BAAB_OLD']."'
					and COLORCOD='".$arrs['COLOR_OLD']."'
				
				insert into {$this->MAuth->getdb('hp_UserOperationLog')}(userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
				values ('".$this->sess["IDNo"]."','สีรถ ลบ','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
				
				insert into #tempolary select 'Y' as id,'สำเร็จ ลบรุ่นรถ ".$arrs['MODEL_OLD']."  แบบ ".$arrs['BAAB_OLD']." สี ".$arrs['COLOR_OLD']." แล้ว' as msg;
				commit tran tsc;
			end try
			begin catch
				rollback tran tsc;
				insert into #tempolary select 'N' as id,'Fail : '+ERROR_MESSAGE() as msg;
			end catch
		";
		
		$this->db->query($sql);
		$sql = "select * from #tempolary";
		$query = $this->db->query($sql);
		
		$response = array();
		if($query->row()){
			foreach($query->result() as $row){
				$response['stat'] = ($row->id == 'Y' ? true:false);
				$response['msg'] = $row->msg;
			}
		}else{
			$response['stat'] = false;
			$response['msg'] = 'ผิดพลาด';
		}
		
		echo json_encode($response);
	}
	
	public function colorFormUPLOAD(){
		$html = "
			<div class='row'>
				<input type='button' id='tab2FormImport' class='btn btn-info btn-sm' style='width:100%;' value='Form COLOR'>
			</div><hr>
			<div class='row'>
				<div id='fileupload'></div>
			</div>
		";
		
		$response = array("html"=>$html);
		echo json_encode($response);
	}
	
	public function colorImport(){
		$this->load->library('excel');
		
		$file = $_FILES["myfile"]["tmp_name"];
		
		//read file from path
		$objPHPExcel = PHPExcel_IOFactory::load($file);
		
		//X ตรวจสอบว่ามีกี่ sheet
		//X $sheetCount = $objPHPExcel->getSheetCount();
		//X จะดึงข้อมูลแค่ sheet 1 เท่านั้น
		$sheetCount = 1; 
		for($sheetIndex=0;$sheetIndex<$sheetCount;$sheetIndex++){
			$objPHPExcel->setActiveSheetIndex($sheetIndex);
			//get only the Cell Collection
			$cell_collection = $objPHPExcel->getActiveSheet()->getCellCollection();
			 
			$arrs = array("now"=>1,"old"=>1); 
			//extract to a PHP readable array format			
			foreach ($cell_collection as $cell) {
				$column = $objPHPExcel->getActiveSheet()->getCell($cell)->getColumn();
				$row = $objPHPExcel->getActiveSheet()->getCell($cell)->getRow();
				$data_value = $objPHPExcel->getActiveSheet()->getCell($cell)->getValue();
				
				if($arrs["old"] == 1){
					$arrs["now"] = 1;
				}else if($arrs["old"] == $row){
					$arrs["now"] = $arrs["now"];
				}else{
					$arrs["now"] += 1;
				}
				//The header will/should be in row 1 only. of course, this can be modified to suit your need.
				if ($row == 1 and $sheetIndex == 0) {
					$header[$row][$column] = $data_value;
				} else {
					switch($column){
						case 'H': $arr_data[$arrs["now"]][$column] = $this->Convertdate(2,$data_value); break;
						case 'I': $arr_data[$arrs["now"]][$column] = $this->Convertdate(2,$data_value); break;
						default: $arr_data[$arrs["now"]][$column] = $data_value; break;
					}
				}
				
				
				$arrs["old"] = $row;
			}
		}
		
		$arrs = array("A","B","C","D","E");
		$datasize = sizeof($arr_data);
		for($i=1;$i<=$datasize;$i++){
			foreach($arrs as $key => $val){
				if(!isset($arr_data[$i][$val])){
					$arr_data[$i][$val] = '';
				}
			}
		}
		//var_dump($arr_data); exit;
		
		$sql_origin = "";
		foreach($arr_data as $key => $val){
			if($key == 1){ $sql_origin .= "select "; }else{ $sql_origin .= "union all select "; }
			foreach($arr_data[$key] as $key2 => $val2){
				if($key2 != "A"){ $sql_origin .= ","; }
				$sql_origin .= "'".$val2."' as ".$key2." ";
			}
		}
		
		$sql = "
			select row_number() over(partition by A,B,C,D order by A,B,C,D) as seq,* into #tempDATA from (
				select distinct a.* 
					,case when b.COLORCOD is null then 'no' else 'yes' end as active
				from (
					".$sql_origin."
				) as a
				left join {$this->MAuth->getdb('JD_SETCOLOR')} as b on a.A=b.TYPECOD and a.B=b.MODELCOD and a.C=b.BAABCOD and a.D=b.COLORCOD
			) as data
		";
		//echo $sql; exit;
		$this->db->query($sql);
		
		$sql = "
			select b.rnk,b.seq,a.A,a.B,a.C,a.D,a.E,a.active from #tempDATA a
			left join (
				select row_number() over(order by A,B,C,D) rnk,* from (
					select max(seq) seq,A,B,C,D from #tempDATA				
					group by A,B,C,D
				) as data
			) as b on a.A=b.A and a.B=b.B and a.C=b.C and a.D=b.D 
		";
		$query = $this->db->query($sql);
		
		$html = "";
		$cnt_all = 0;
		$cnt_have = 0;
		$cnt_not_have = 0;
		if($query->row()){
			foreach($query->result() as $row){
				$cssbgcolor = "";
				IF($row->seq > 1){
					$cssbgcolor = "background-color:yellow;";
				}
				
				$html .= "<tr style='color:".($row->active == "no" ? "black":"red").";{$cssbgcolor}'>";
				foreach($row as $key => $val){
					switch($key){
						case 'active': 
							$cnt_all 		+= 1;
							$cnt_have 		+= ($val == "yes" ? 1:0);
							$cnt_not_have 	+= ($val == "yes" ? 0:1);
							$html .= "
								<td>
									<button type='button' class='".($val == "yes" ? "":"btn_remove_color_upload")." item btn btn-labeled btn-danger btn-xs' 
										TYPECOD='".$row->A."' MODELCOD='".$row->B."'
										BAABCOD='".$row->C."' COLORCOD='".$row->D."' COLORTH='".$row->E."'
										SEQ='".$row->seq."'
										item='{$row->rnk}'
										".($val == "yes" ? "disabled":"")."
									>
										<span class='btn-label'><i class='glyphicon glyphicon-trash'></i></span> ลบ
									</button>
								</td>
							";
							break;
						default: $html .= "<td>{$val}</td>"; break;
					}
				}
				$html .= "</tr>";
			}
		}
		
		$html = "
			<div style='width:100%;height:calc(100vh - 135px);overflow:auto;'>
				<table border=1 width='100%'>
					<thead>
						<tr>
							<th>#</th>
							<th>#</th>
							<th>ชนิด</th>
							<th>รุ่น</th>
							<th>แบบ</th>
							<th>สี</th>
							<th>สี (ไทย)</th>
							<th>#</th>
						</tr>
					</thead>
					<tbody>
						{$html}
					</tbody>
				</table>
			</div>
			<div style='width:100%;height:60px;'><br>
				<div class='col-sm-10'>
					<span class='badge badge-default' style='float:left;'>ทั้งหมด <span class='nowall'>{$cnt_all}</span> รายการ</span>
					<span class='badge badge-danger' style='float:left;'>มีข้อมูลในระบบแล้ว {$cnt_have} รายการ</span>
					<span class='badge badge-primary' style='float:left;'>จำนวนที่สามารถเพิ่มได้ <span class='nowadd'>{$cnt_not_have}</span> รายการ</span>
				</div>
				<div class='col-sm-2 col-xs-8' align='right'>
					<button type='button' id='btn_save_upload' class='btn btn-labeled btn-primary btn-xs' ".($val == "yes" ? "disabled":"").">
						<span class='btn-label'><i class='glyphicon glyphicon-floppy-disk'></i></span> บันทึก
					</button>
				</div>
			</div>
		";
		
		$response = array();
		$response["error"] = false;
		$response["html"] = $html;
		echo json_encode($response); 
	}
	
	function color_save_import(){
		$data = $_POST["data"];
		$size = sizeof($data);
		
		$sql = "";
		for($i=0;$i<$size;$i++){
			$sql .= ($i==0?"insert into {$this->MAuth->getdb('JD_SETCOLOR')}(TYPECOD,MODELCOD,BAABCOD,COLORCOD,MEMO1)":" union all ")." select '{$data[$i]["type"]}','{$data[$i]["model"]}','{$data[$i]["baab"]}','{$data[$i]["color"]}','{$data[$i]["colorTH"]}'";
		}
		
		$sql = "
			if object_id('tempdb..#tempolary') is not null drop table #tempolary;
			create table #tempolary (id varchar(1),msg varchar(max));
			
			begin tran tsc
			begin try			
				".$sql."
				
				insert into #tempolary select 'Y' as id,'สำเร็จ บันทึกข้อมูลเรียบร้อยแล้ว' as msg;
				commit tran tsc;
			end try
			begin catch
				rollback tran tsc;
				insert into #tempolary select 'N' as id,'Fail : '+ERROR_MESSAGE() as msg;
			end catch
		";
		
		$this->db->query($sql);
		$sql = "select * from #tempolary";
		$query = $this->db->query($sql);
		
		$response = array();
		if($query->row()){
			foreach($query->result() as $row){
				$response['stat'] = ($row->id == 'Y' ? true:false);
				$response['msg'] = $row->msg;
			}
		}else{
			$response['stat'] = false;
			$response['msg'] = 'ผิดพลาด';
		}
		
		echo json_encode($response);
	}
	
	public function maxstock(){
		$claim = $this->MLogin->getclaim(uri_string());
		if($claim['m_access'] != "T"){ echo "<div align='center' style='color:red;font-size:16pt;width:100%;'>ขออภัย คุณยังไม่มีสิทธิเข้าใช้งานหน้านี้ครับ</div>"; exit; }
		
		$html = "
			<div class='tab1' name='home' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}' style='height:65px;overflow:auto;'>
				<div class='col-sm-2'>	
					<div class='form-group'>
						<span class='small'>สาขา</span>
						<input type='text'  id='ms_locat' class='form-control input-sm' data-alert='สาขา' placeholder='สาขา'>
					</div>
				</div>
				<div class='col-sm-8'>	
					<div class='form-group'>
						<span class='small'>จังหวัด</span>
						<select id='ms_prov' class='form-control input-sm'>
							<option value=''>ทั้งหมด</option>
							<option value='ตรัง'>ตรัง</option>
							<option value='กระบี่'>กระบี่</option>
							<option value='พังงา'>พังงา</option>
							<option value='สุราษฎร์ธานี'>สุราษฎร์ธานี</option>
							<option value='ชุมพร'>ชุมพร</option>
						</select>
					</div>
				</div>					
				<div class='col-sm-1'>	
					<div class='form-group'>
						<br>
						<input type='button' id='search_group' class='btn btn-primary btn-sm btn-block' value='แสดง'>
					</div>
				</div>
				<div class='col-sm-1'>	
					<div class='form-group'>
						<br>
						<input type='button' id='add_group' class='btn btn-cyan btn-sm btn-block' value='เพิ่ม' >
					</div>
				</div>
			</div>
			<div id='setmaxstockResult' class='col-sm-12 tab1' style='height:calc(100vh - 197px);overflow:auto;background-color:#;'></div>
			
			<div id='tab2_main' class='col-sm-12 tab2' hidden style='height:calc(100vh - 130px);overflow:auto;background-color:#;'></div>
		";
	
		$html.= "<script src='".base_url('public/js/setup/CStock/setmaxstock.js')."'></script>";
		echo $html;
	}
	
	function maxstock_search(){
		$arrs = array();
		$arrs["locat"] = $_REQUEST["locat"];
		$arrs["prov"]  = $_REQUEST["prov"];
		$arrs["canup"] = $_REQUEST["canup"];
		
		$cond = "";
		if($arrs["locat"] != ""){
			$cond .= " and LOCAT like '".$arrs["locat"]."%' ";
		}
		
		if($arrs["prov"] != ""){
			$cond .= " and Prov ='".$arrs["prov"]."' ";
		}
		
		$sql = "
			select * from {$this->MAuth->getdb('std_locatStock')}
			where 1=1 ".$cond."
			order by Prov,LINE,AREA,LOCAT
		";
		$query = $this->db->query($sql);
		
		$html = "";
		$NRow = 1;
		if($query->row()){
			foreach($query->result() as $row){
				$html.= "
					<tr class='trow' seq=".$NRow.">
						<td class='getit' seq=".$NRow++." align='center' style='".($arrs["canup"] != "T" ? "cursor:not-allowed;" : "")."'>
							<b><i class='glyphicon glyphicon-edit mst-edit' LOCAT='".$row->LOCAT."' style='".($arrs["canup"] != "T" ? "cursor:not-allowed;" : "cursor:pointer;")."'></i></b>
						</td>
						<td>".$row->LOCAT."</td>
						<td>".$row->Prov."</td>
						<td>".$row->LINE."</td>
						<td>".$row->AREA."</td>
						<td>".$row->MaxStockN."</td>
						<td>".$row->MaxStockO."</td>
						<td>".$row->MaxStock."</td>
						<td>".$row->MaxStore."</td>
						<td>".($row->locatStatus=="Y"?"สาขาปกติ":"ปิดสาขา")."</td>
					</tr>
				";
			}
		}
		
		$html = "
			<div id='table-fixed-maxstockSearch' class='col-sm-12' style='height:100%;overflow:auto;background-color:#eee;'>
				<table id='table-maxstockSearch' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='100%'>
					<thead>
						<tr>
							<th align='center'>#</th>
							<th>สาขา</th>
							<th>จังหวัด</th>
							<th>สาย</th>
							<th>พื้นที่</th>
							<th>max stock (รถใหม่)</th>
							<th>max stock (รถเก่า)</th>
							<th>max stock (รวม)</th>
							<th>max คลัง</th>
							<th>สถานะ</th>
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
	
	public function maxstock_checkaddLOCAT(){
		$locat = $_REQUEST["locat"];
		
		$sql = "select count(*) r from YTKManagement.dbo.std_locatStock where LOCAT='".$locat."'";
		$query = $this->db->query($sql);
		
		$html = 0;
		if($query->row()){
			foreach($query->result() as $row){
				$html = $row->r;
			}
		}
		
		$response = array("html"=>$html);
		echo json_encode($response);
	}
	
	public function maxstock_formedit(){
		$html = "";
		
		$arrs =  array(
			"LOCAT" => $_REQUEST["LOCAT"]
		);
		
		$sql = "
			select * from {$this->MAuth->getdb('std_locatStock')}
			where LOCAT='".$arrs["LOCAT"]."'
		";
		$query = $this->db->query($sql);
		
		if($query->row()){
			foreach($query->result() as $row){
				$html = "
					<div class='row'>
						<div class='col-sm-4 col-sm-offset-4'>
							<div class='form-group'>
								<span class='small'>สาขา</span>
								<input type='text' id='fa_locat' class='form-control input-sm' value='".$row->LOCAT."' disabled>
							</div>
							<div class='form-group'>
								<span class='small text-primary'>จังหวัด</span>
								<!-- input class='form-control input-sm' value='".$row->Prov."' -->
								<select id='fa_prov' class='form-control'>
									<option value='ตรัง' ".($row->Prov == "ตรัง" ? "selected":"").">ตรัง</option>
									<option value='กระบี่' ".($row->Prov == "กระบี่" ? "selected":"").">กระบี่</option>
									<option value='พังงา' ".($row->Prov == "พังงา" ? "selected":"").">พังงา</option>
									<option value='สุราษฎร์ธานี' ".($row->Prov == "สุราษฎร์ธานี" ? "selected":"").">สุราษฎร์ธานี</option>
									<option value='ชุมพร' ".($row->Prov == "ชุมพร" ? "selected":"").">ชุมพร</option>
								</select>
							</div>
							<div class='form-group'>
								<span class='small'>สาย</span>
								<input type='number' id='fa_line' class='form-control input-sm' value='".$row->LINE."'>
							</div>
							<div class='form-group'>
								<span class='small'>พื้นที่</span>
								<input type='number' id='fa_area' class='form-control input-sm' value='".$row->AREA."'>
							</div>
							<div class='form-group'>
								<span class='small text-primary'>Max Stock รถใหม่</span>
								<input type='number' id='fa_maxn' class='form-control input-sm' value='".$row->MaxStockN."'>
							</div>
							<div class='form-group'>
								<span class='small text-primary'>Max Stock รถเก่า</span>
								<input type='number' id='fa_maxo' class='form-control input-sm' value='".$row->MaxStockO."'>
							</div>
							<div class='form-group'>
								<span class='small text-primary'>Max Stock คลัง</span>
								<input type='number' id='fa_maxs' class='form-control input-sm' value='".$row->MaxStore."'>
							</div>
							<div class='form-group'>
								<span class='small text-primary'>สถานะ</span>
								<select id='fa_locatStatus' class='form-control'>
									<option value='Y' ".($row->locatStatus == "Y" ? "selected":"").">สาขาปกติ</option>
									<option value='N' ".($row->locatStatus == "N" ? "selected":"").">ปิดสาขา</option>
								</select>
							</div>
							
							<div class='form-group'>
								<button id='fa_edit' class='btn btn-sm btn-primary btn-block' >บันทึก</button>
							</div>
						</div>
					</div>
				";				
			}
		}
		
		$response = array("html"=>$html);
		echo json_encode($response);
	}
	
	public function maxstock_edit(){
		$arrs = array(
			"locat" => $_REQUEST["locat"],
			"prov" => $_REQUEST["prov"],
			"line" => $_REQUEST["line"],
			"area" => $_REQUEST["area"],
			"maxn" => $_REQUEST["maxn"],
			"maxo" => $_REQUEST["maxo"],
			"maxa" => $_REQUEST["maxn"]+$_REQUEST["maxo"],
			"maxs" => $_REQUEST["maxs"],
			"locatStatus" => $_REQUEST["locatStatus"],
			"IDNo" => $this->sess["IDNo"]
		);
		
		$sql = "
			if object_id('tempdb..#tempfaedit') is not null drop table #tempfaedit;
			create table #tempfaedit (id varchar(2),msg varchar(max));
			
			begin tran faadd
			begin try 
				declare @has int = (select count(*) from {$this->MAuth->getdb('std_locatStock')} where LOCAT='".$arrs["locat"]."');
				
				if(@has > 0)
				begin
					update {$this->MAuth->getdb('std_locatStock')}
					set Prov		 = '".$arrs["prov"]."'
						,LINE		 = '".$arrs["line"]."'
						,AREA		 = '".$arrs["area"]."'
						,MaxStockN	 = '".$arrs["maxn"]."'
						,MaxStockO	 = '".$arrs["maxo"]."'
						,MaxStock	 = '".$arrs["maxa"]."'
						,MaxStore	 = '".$arrs["maxs"]."'
						,locatStatus = '".$arrs["locatStatus"]."'
						,updateBy	 = '".$arrs["IDNo"]."'
						,updateDt	 = getdate()
					where LOCAT='".$arrs["locat"]."'
				end
				else
				begin
					rollback tran faadd;
					insert into #tempfaedit select 'F','ผิดพลาด ไม่สามารถแก้ไขพื้นที่จอดรถสาขา ".$arrs["locat"]." ได้ เนื่องจากไม่พบข้อมูลพื้นที่สต๊อก';	
					return;
				end	
				
				insert into {$this->MAuth->getdb('hp_UserOperationLog')} (userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
				values ('".$this->sess["IDNo"]."','SYS01::แก้ไขพื้นที่จอดรถแต่ละพื้นที่ ',' ".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
				
				insert into #tempfaedit select 'T','บันทึกพื้นที่จอดรถเรียบร้อยแล้วครับ';
				commit tran faadd;
			end try
			begin catch
				rollback tran faadd;
				insert into #tempfaedit select 'F',ERROR_MESSAGE();
			end catch
		";
		//echo $sql; exit;
		$this->db->query($sql);
		$sql = "select  * from #tempfaedit";
		$query = $this->db->query($sql);
		
		if($query->row()) {
			foreach ($query->result() as $row) {
				$stat = ($row->id == "T" ? true : false);
				$msg = $row->msg;
			}
		}else{
			$stat = false;
			$msg = "ผิดพลาด :: ไม่สามารถทำรายการได้ในขณะนี้ โปรดลองทำรายการใหม่ภายหลัง";
		}
		
		$response = array();
		$response['status'] = $stat;
		$response['msg'] = $msg;
		echo json_encode($response); exit;
	}
	
	public function maxstock_formadd(){
		$html = "
			<div class='row'>
				<div class='col-sm-4 col-sm-offset-4'>
					<div class='form-group'>
						<span class='small text-primary'>สาขา</span>
						<select id='fa_locat' class='form-control'></select>
					</div>
					<div class='form-group'>
						<span class='small text-primary'>จังหวัด</span>
						<select id='fa_prov' class='form-control'>
							<option value='ตรัง'>ตรัง</option>
							<option value='กระบี่'>กระบี่</option>
							<option value='พังงา'>พังงา</option>
							<option value='สุราษฎร์ธานี'>สุราษฎร์ธานี</option>
							<option value='ชุมพร'>ชุมพร</option>
						</select>
					</div>
					<div class='form-group'>
						<span class='small'>สาย</span>
						<input type='number' id='fa_line' class='form-control input-sm' value='0'>
					</div>
					<div class='form-group'>
						<span class='small'>พื้นที่</span>
						<input type='number' id='fa_area' class='form-control input-sm' value='0'>
					</div>
					<div class='form-group'>
						<span class='small text-primary'>Max Stock รถใหม่</span>
						<input type='number' id='fa_maxn' class='form-control input-sm' value='0'>
					</div>
					<div class='form-group'>
						<span class='small text-primary'>Max Stock รถเก่า</span>
						<input type='number' id='fa_maxo' class='form-control input-sm' value='0'>
					</div>
					<div class='form-group'>
						<span class='small text-primary'>Max Stock คลัง</span>
						<input type='number' id='fa_maxs' class='form-control input-sm' value='0'>
					</div>
					<div class='form-group'>
						<span class='small text-primary'>สถานะ</span>
						<select id='fa_locatStatus' class='form-control'>
							<option value='Y'>สาขาปกติ</option>
							<option value='N'>ปิดสาขา</option>
						</select>
					</div>
					
					<div class='form-group'>
						<button id='fa_save' class='btn btn-sm btn-primary btn-block' >บันทึก</button>
					</div>
				</div>
			</div>
		";		
		$response = array("html"=>$html);
		echo json_encode($response);
	}
	
	function maxstock_add(){
		$arrs = array(
			"locat" => $_REQUEST["locat"],
			"prov" => $_REQUEST["prov"],
			"line" => $_REQUEST["line"],
			"area" => $_REQUEST["area"],
			"maxn" => $_REQUEST["maxn"],
			"maxo" => $_REQUEST["maxo"],
			"maxa" => $_REQUEST["maxn"]+$_REQUEST["maxo"],
			"maxs" => $_REQUEST["maxs"],
			"locatStatus" => $_REQUEST["locatStatus"],
			"IDNo" => $this->sess["IDNo"]
		);
		
		$sql = "
			if object_id('tempdb..#tempfaadd') is not null drop table #tempfaadd;
			create table #tempfaadd (id varchar(2),msg varchar(max));
			
			begin tran faadd
			begin try 
				declare @has int = (select count(*) from {$this->MAuth->getdb('std_locatStock')} where LOCAT='".$arrs["locat"]."');
				
				if(@has = 0)
				begin
					insert into {$this->MAuth->getdb('std_locatStock')}
					select '".$arrs["locat"]."','".$arrs["prov"]."','".$arrs["line"]."'
						,'".$arrs["area"]."','".$arrs["maxn"]."','".$arrs["maxo"]."','".$arrs["maxa"]."'
						,'".$arrs["maxs"]."','".$arrs["locatStatus"]."','".$arrs["IDNo"]."',getdate();
				end
				else
				begin
					rollback tran faadd;
					insert into #tempfaadd select 'F','ผิดพลาด ไม่สามารถเพิ่มพื้นที่จอดรถสาขา ".$arrs["locat"]." ได้ เนื่องจากมีข้อมูลอยู่แล้วครับ';	
					return;
				end	
				
				insert into {$this->MAuth->getdb('hp_UserOperationLog')} (userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
				values ('".$this->sess["IDNo"]."','SYS01::บันทึกพื้นที่จอดรถแต่ละพื้นที่ ',' ".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
				
				insert into #tempfaadd select 'T','บันทึกพื้นที่จอดรถเรียบร้อยแล้วครับ';
				commit tran faadd;
			end try
			begin catch
				rollback tran faadd;
				insert into #tempfaadd select 'F',ERROR_MESSAGE();
			end catch
		";
		$this->db->query($sql);
		$sql = "select  * from #tempfaadd";
		$query = $this->db->query($sql);
		
		if($query->row()) {
			foreach ($query->result() as $row) {
				$stat = ($row->id == "T" ? true : false);
				$msg = $row->msg;
			}
		}else{
			$stat = false;
			$msg = "ผิดพลาด :: ไม่สามารถทำรายการได้ในขณะนี้ โปรดลองทำรายการใหม่ภายหลัง";
		}
		
		$response = array();
		$response['status'] = $stat;
		$response['msg'] = $msg;
		echo json_encode($response); exit;
	}
	function accessory(){
		/**KORN setup อุปกรณ์เสริม**/
		//echo "เมนูนี้กำลังพัฒนาครับ"; exit;
		$claim = $this->MLogin->getclaim(uri_string());
		if($claim['m_access'] != "T"){ echo "<div align='center' style='color:red;font-size:16pt;width:100%;'>ขออภัย คุณยังไม่มีสิทธิเข้าใช้งานหน้านี้ครับ</div>"; exit; }
		$html = "
			<div class='k_tab1' name='home' locat='{$this->sess['branch']}' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}' today='".$this->today('today')."' style='height:calc(100vh - 132px);overflow:auto;background-color:white;'>
				<div class='col-sm-12'>
					<div class=' col-sm-2'>	
						<div class='form-group'>
							รหัสอุปกรณ์
							<input type='text' id='OPTCODE' class='form-control input-sm' placeholder='รหัสอุปกรณ์' >
						</div>
					</div>
					<div class='col-sm-3'>	
						<div class='form-group'>
							ชื่ออุปกรณ์
							<input type='text' id='OPTNAME' class='form-control input-sm' placeholder='ชื่ออุปกรณ์' >
						</div>
					</div>
					<div class='col-sm-3'>	
						<div class='form-group'>
							สาขา
							<input type='text' id='LOCAT' class='form-control input-sm' value='".$this->sess["branch"]."'>
						</div>
					</div>
					<div class='col-sm-2'>	
						<div class='form-group'>
							<br>
							<button id='btnsearch' class='btn btn-primary btn-block'><span class='glyphicon glyphicon-search'>ค้นหา</span></button>
						</div>
					</div>
					<div class='col-sm-2'>	
						<div class='form-group'>
							<br>
							<button id='btnadd' class='btn btn-cyan btn-block'><span class='glyphicon glyphicon-pencil'>เพิ่ม</span></button>
						</div>
					</div>
				</div>
				<div class='col-sm-12'>
					<div id='setoptresult' class='col-sm-12 tab1' style='height:calc(100vh - 220px);overflow:auto;background-color:#;'></div>
				</div>
			</div>
			
		";
		$html .="<script src='".base_url('public/js/setup/setAccessory.js')."'></script>";
		echo $html;
	}
	function accessory_search(){
		$arrs = array();
		$arrs["OPTCODE"] = $_REQUEST["OPTCODE"];
		$arrs["OPTNAME"] = $_REQUEST["OPTNAME"];
		$arrs["LOCAT"]   = $_REQUEST["LOCAT"];
		$cond = "";
		if($arrs["OPTCODE"] != ""){
			$cond .=" and OPTCODE like '".$arrs["OPTCODE"]."%'";
		}
		if($arrs["OPTNAME"] != ""){
			$cond .=" and OPTNAME like '".$arrs["OPTNAME"]."%'";
		}
		if($arrs["LOCAT"] != ""){
			$cond .=" and LOCAT like '".$arrs["LOCAT"]."%'";
		}
		
		$sql = "
			select  OPTCODE,OPTNAME,LOCAT,UNITPRC,ONHAND 
			from {$this->MAuth->getdb('OPTMAST')} where 1=1
			".$cond."
		";
		$query = $this->db->query($sql);
		$html = "";
		$NRow = 1;
		if($query->row()){
			foreach($query->result() as $row){
				$html .="
					<tr class='trow' seq='".$NRow."'>
						<td class='getit' seq='".$NRow++."' 
							OPTCODE = '".str_replace(chr(0),"",$row->OPTCODE)."' 
							LOCAT   = '".str_replace(chr(0),"",$row->LOCAT)."'
						style='width:50px;cursor:pointer;text-align:center;'><b>เลือก</b></td>
						<td>".str_replace(chr(0),'',$row->OPTCODE)."</td>
						<td>".str_replace(chr(0),'',$row->OPTNAME)."</td>
						<td>".str_replace(chr(0),'',$row->LOCAT)."</td>
						<td>".number_format($row->UNITPRC,2)."</td>
						<td>".str_replace(chr(0),'',$row->ONHAND)."</td>
					</tr>
				";
			}
		}
		//onmousedown='return false;'
		$html = "
			<div id='table-fixed-accessory' class='col-sm-12' style='height:100%;overflow:auto;background-color:#eee;'>
				<table id='table-accessory' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='100%'>
					<thead>
						<!-- tr>
							<th colspan='4' align='center'>
								<span style='cursor:pointer;'>Excel</span>
								<span style='cursor:pointer;'>PDF</span>
							</th>
						</tr -->
						<tr>
							<th>#</th>
							<th>รหัสกลุ่ม</th>
							<th>ชื่อกลุ่ม</th>
							<th>สถานที่เก็บ</th>
							<th>ราคา/หน่วย</th>
							<th>จำนวนคงเหลือ</th>
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
	function accessory_formsetopt(){
		$html = "
			<div class='col-sm-12'>
				<div class='row'>
					<div class='col-sm-5'>
						<div class='form-group'>
							<span class='text-red'>*</span>
							รหัสอุปกรณ์
							<div class='input-group'>
								<input type='text' class='form-control input-sm' id='add_optcode' value=''>
								<span class='input-group-btn'>
									<button id='btn_addopt' class='btn btn-primary btn-sm' type='button'>
										<span class='glyphicon glyphicon-hand-up' aria-hidden='true'></span>
									</button>
								</span>
							</div>
						</div>
					</div>
					<div class='col-sm-7'>
						ชื่ออุปกรณ์
						<input type='text' class='form-control input-sm' id='add_optname' value=''>
					</div>
				</div>
				<div class='row'>
					<div class='col-sm-5'>
						<span class='text-red'>*</span>
						รหัสสถานที่เก็บ
						<select type='text' class='form-control input-sm' id='add_locat'>
						
						</select>
					</div>
					<div class='col-sm-7'>
						ชื่อสถานที่เก็บ
						<input type='text' class='form-control input-sm' id='add_locatnm' readonly>
					</div>
				</div>
				<div class='col-sm-12'>
					<div class='row'>
						<div class='col-sm-6'>
							<br>
							ราคาขาย/หน่วย (ไม่รวม Vat)
						</div>
						<div class='col-sm-6'>
							<br>
							<input class='form-control text-right jzAllowNumber' id='add_unitprc' placeholder='0.00'>
						</div>
					</div>
				</div>	
				<div class='col-sm-12'>
					<div class='row'>
						<div class='col-sm-6'>
							<br>
							ราคาทุน/หน่วย (ไม่รวม Vat)
						</div>
						<div class='col-sm-6'>
							<br>
							<input class='form-control text-right jzAllowNumber' id='add_unitcst' placeholder='0.00'>
						</div>
					</div>
				</div>
				<div class='col-sm-12'>
					<div class='row'>
						<div class='col-sm-6'>
							<br>
							สต๊อกคงเหลือ
						</div>
						<div class='col-sm-6'>
							<br>
							<input class='form-control text-right jzAllowNumber' id='add_onhand' placeholder='0.00'>
						</div>
					</div>
				</div>
				<div class='col-sm-12 text-right'>
					<div class='row'>
						<br><br>
						<button id='btn_save' class='btn btn-xs btn-primary' style='width:100px;'><span class='glyphicon glyphicon-floppy-disk'> บันทึก</span></button>
						<button id='btn_del' class='btn btn-xs btn-danger' style='width:100px;'><span class='glyphicon glyphicon-trash'> ลบ</span></button>
					</div>
				</div>
			</div>
		";
		$response = array("html"=>$html);
		echo json_encode($response);
	}
	function accessory_loaddata(){
		$response = array();
		$optcode  = $_REQUEST["optcode"];
		$locat    = $_REQUEST["locat"];
		
		$sql = "
			select 
				O.OPTCODE,O.OPTNAME,O.LOCAT,I.LOCATNM,O.UNITPRC
				,O.UNITCST,O.ONHAND
			from {$this->MAuth->getdb('OPTMAST')} O
			left join {$this->MAuth->getdb('INVLOCAT')} I on O.LOCAT = I.LOCATCD
			where O.OPTCODE = '".$optcode."' and O.LOCAT like '".$locat."%'
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$response["OPTCODE"] = str_replace(chr(0),"",$row->OPTCODE);
				$response["OPTNAME"] = str_replace(chr(0),"",$row->OPTNAME);
				$response["LOCAT"]   = str_replace(chr(0),"",$row->LOCAT);
				$response["LOCATNM"] = str_replace(chr(0),"",$row->LOCATNM);
				$response["UNITPRC"] = number_format($row->UNITPRC,2);
				$response["UNITCST"] = number_format($row->UNITCST,2);
				$response["ONHAND"]  = number_format($row->ONHAND,2);
			}
		}
		echo json_encode($response);
	}
	function accessory_save(){
		$arrs = array();
		$arrs["OPTCODE"]  = (!isset($_REQUEST["OPTCODE"]) ? "":$_REQUEST["OPTCODE"]);
		$arrs["OPTNAME"]  = (!isset($_REQUEST["OPTNAME"]) ? "":$_REQUEST["OPTNAME"]);
		$arrs["LOCAT"]    = (!isset($_REQUEST["LOCAT"]) ? "":$_REQUEST["LOCAT"]);
		$arrs["UNITPRC"]  = (!isset($_REQUEST["UNITPRC"]) ? "":$_REQUEST["UNITPRC"]);
		$arrs["UNITCST"]  = (!isset($_REQUEST["UNITCST"]) ? "":$_REQUEST["UNITCST"]);
		$arrs["ONHAND"]   = (!isset($_REQUEST["ONHAND"]) ? "":$_REQUEST["ONHAND"]);
		$arrs["EVENT"]    = $_REQUEST["EVENT"];
		
		$response = array();
		if($arrs["OPTCODE"] == ""){
			$response["error"] = "N";
			$response["msg"]   = "กรุณากรอกรหัสอุปกรณ์ก่อนครับ";
			echo json_encode($response); exit;
		}
		if($arrs["OPTNAME"] == ""){
			$response["error"] = "N";
			$response["msg"]   = "กรุณากรอกชื่ออุปกรณ์ก่อนครับ";
			echo json_encode($response); exit;
		}
		if($arrs["LOCAT"] == ""){
			$response["error"] = "N";
			$response["msg"]   = "กรุณาเลือกรหัสสถานที่เก็บก่อนครับ";
			echo json_encode($response); exit;
		}
		if($arrs["UNITPRC"] == ""){
			$response["error"] = "N";
			$response["msg"]   = "กรุณากรอกราคาขาย/หน่วย (ไม่รวม Vat)เป็นตัวเลขก่อนครับ";
			echo json_encode($response); exit;
		}
		if($arrs["UNITCST"] == ""){
			$response["error"] = "N";
			$response["msg"]   = "กรุณากรอกราคาทุน/หน่วย (ไม่รวม Vat)เป็นตัวเลขก่อนครับ";
			echo json_encode($response); exit;
		}
		if($arrs["ONHAND"] == ""){
			$arrs["ONHAND"] = 0;
		}
		
		if($arrs["EVENT"] == "add"){
			$sql = "
				if OBJECT_ID('tempdb..#tempsaveopt') is not null drop table #tempsaveopt;
				create table #tempsaveopt (id varchar(1),msg varchar(max));
				begin tran saveopt
				begin try
					if not exists(
						select * from {$this->MAuth->getdb('OPTMAST')} 
						where OPTCODE = '".$arrs["OPTCODE"]."' and LOCAT = '".$arrs["LOCAT"]."'
					)
					begin
						insert into {$this->MAuth->getdb('OPTMAST')} (
							[OPTCODE],[LOCAT],[OPTNAME],[UNITPRC]
							,[UNITCST],[ONHAND],[TAVGCST],[LDTMOVE]
						)values(
							'".$arrs["OPTCODE"]."','".$arrs["LOCAT"]."','".$arrs["OPTNAME"]."'
							,".$arrs["UNITPRC"].",".$arrs["UNITCST"].",0,0,null
						)
						
						insert into {$this->MAuth->getdb('hp_UserOperationLog')} (
							userId,descriptions,postReq,dateTimeTried,ipAddress,functionName
						)values (
							'".$this->sess["IDNo"]."','SYS01::บันทึกรหัสอุปกรณ์เสริม'
							,'".$arrs["OPTCODE"]."'+' ".str_replace("'","",var_export($_REQUEST, true))."'
							,getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."'
						);
						insert into #tempsaveopt select 'Y' as id,'สำเร็จ บันทึก รหัสอุปกรณ์เสริม :: ".$arrs["OPTCODE"]." เรียบร้อยแล้ว' as msg;
						commit tran saveopt;
					end
					else
					begin
						rollback tran saveopt;
						insert into #tempsaveopt select 'N' as id,'บันทึกข้อมูลไม่สำเร็จ : รหัสอุปกรณ์ซ้ำกับของเดิม' as msg;
						return;
					end
				end try
				begin catch
					rollback tran saveopt;
					insert into #tempsaveopt select 'E' as id,'บันทึกข้อมูลไม่สำเร็จ : กรุณาติดต่อฝ่ายไอที' as msg;
					return;
				end catch
			";	
		}else{
			$sql = "
				if OBJECT_ID('tempdb..#tempsaveopt') is not null drop table #tempsaveopt;
				create table #tempsaveopt (id varchar(1),msg varchar(max));
				begin tran saveopt
				begin try
					begin
						update {$this->MAuth->getdb('OPTMAST')} 
						set OPTCODE = '".$arrs["OPTCODE"]."',OPTNAME = '".$arrs["OPTNAME"]."'
							,LOCAT = '".$arrs["LOCAT"]."',UNITPRC = ".$arrs["UNITPRC"]."
							,UNITCST = ".$arrs["UNITCST"].",ONHAND = ".$arrs["ONHAND"]."
						where OPTCODE = '".$arrs["OPTCODE"]."' and LOCAT = '".$arrs["LOCAT"]."'	
						
						insert into {$this->MAuth->getdb('hp_UserOperationLog')} (
							userId,descriptions,postReq,dateTimeTried,ipAddress,functionName
						)values (
							'".$this->sess["IDNo"]."','SYS01::แก้ไขรหัสอุปกรณ์เสริม'
							,'".$arrs["OPTCODE"]."'+' ".str_replace("'","",var_export($_REQUEST, true))."'
							,getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."'
						);
						insert into #tempsaveopt select 'Y' as id,'สำเร็จ แก้ไขรหัสอุปกรณ์เสริม :: ".$arrs["OPTCODE"]." เรียบร้อยแล้ว' as msg;
						commit tran saveopt;
					end
				end try
				begin catch
					rollback tran saveopt;
					insert into #tempsaveopt select 'E' as id,'บันทึกข้อมูลไม่สำเร็จ : กรุณาติดต่อฝ่ายไอที' as msg;
					return;
				end catch
			";	
		}
		//echo $sql; exit;
		$this->db->query($sql);
		$sql = "select * from #tempsaveopt";
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$response["error"] = $row->id;
				$response["msg"]   = $row->msg;
			}
		}else{
			$response["error"] = "E";
			$response["msg"]   = "ผิดพลาด กรุณาติดต่อฝ่ายไอที";
		}
		echo json_encode($response);
	}
	function accessory_del(){
		$arrs = array();
		$arrs["OPTCODE"]  = (!isset($_REQUEST["OPTCODE"]) ? "":$_REQUEST["OPTCODE"]);
		$arrs["LOCAT"]    = (!isset($_REQUEST["LOCAT"]) ? "":$_REQUEST["LOCAT"]);
		
		$sql = "
			if OBJECT_ID('tempdb..#tempdelopt') is not null drop table #tempdelopt;
			create table #tempdelopt (id varchar(1),msg varchar(max));
			begin tran delopt
			begin try
				if not exists(
					select * from {$this->MAuth->getdb('OPTMAST')} 
					where OPTCODE = '".$arrs["OPTCODE"]."' and LOCAT = '".$arrs["LOCAT"]."' 
					and ONHAND <> 0
				)
				begin
					delete from {$this->MAuth->getdb('OPTMAST')}
					where OPTCODE = '".$arrs["OPTCODE"]."' and LOCAT = '".$arrs["LOCAT"]."'
					
					insert into {$this->MAuth->getdb('hp_UserOperationLog')} (
						userId,descriptions,postReq,dateTimeTried,ipAddress,functionName
					)values (
						'".$this->sess["IDNo"]."','SYS01::ลบรหัสอุปกรณ์เสริม'
						,'".$arrs["OPTCODE"]."'+' ".str_replace("'","",var_export($_REQUEST, true))."'
						,getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."'
					);
					insert into #tempdelopt select 'Y' as id,'สำเร็จ ลบรหัสอุปกรณ์เสริม :: ".$arrs["OPTCODE"]." เรียบร้อยแล้ว' as msg;
					commit tran delopt;
				end
				else
				begin
					rollback tran delopt;
					insert into #tempdelopt select 'N' as id,'บันทึกข้อมูลไม่สำเร็จ : รหัสอุปกรณ์ถูกนำไปใช้แล้ว' as msg;
					return;
				end
			end try
			begin catch
				rollback tran delopt;
				insert into #tempdelopt select 'E' as id,'บันทึกข้อมูลไม่สำเร็จ : กรุณาติดต่อฝ่ายไอที' as msg;
				return;
			end catch
		";
		//echo $sql; exit;
		$this->db->query($sql);
		
		$sql = "select * from #tempdelopt";
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$response["error"] = $row->id;
				$response["msg"]   = $row->msg;
			}
		}else{
			$response["error"] = "E";
			$response["msg"]   = "ผิดพลาด กรุณาติดต่อฝ่ายไอที";
		}
		echo json_encode($response);
	}
}




















