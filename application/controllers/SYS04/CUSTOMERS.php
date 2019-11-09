<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/********************************************************
             ______@04/11/2019______
			 Pasakorn

********************************************************/
class CUSTOMERS extends MY_Controller {
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
		
		$html = "
			<div class='tab1' name='home' locat='{$this->sess['branch']}' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}' style='height:calc(100vh - 132px);overflow:auto;background-color:white;'>
				<div class='col-sm-12' style='overflow:auto;'>					
					<div class='row'>
						<div class=' col-sm-2'>	
							<div class='form-group'>
								รหัสลูกค้า
								<input type='text' id='cuscod' class='form-control input-sm' placeholder='รหัสลูกค้า' >
							</div>
						</div>
						<div class=' col-sm-6'>	
							<div class='form-group'>
								ชื่อ-สกุล  ลูกค้า
								<input type='text' id='name1-2' class='form-control input-sm' placeholder='ชื่อ-สกุล ลูกค้า' >
							</div>
						</div>
						<div class=' col-sm-2'>	
							<div class='form-group'>
								<br>
								<input type='button' id='search_custmast' class='btn btn-cyan btn-sm' value='แสดง' style='width:100%'>
							</div>
						</div>
						<div class=' col-sm-2'>	
							<div class='form-group'>
								<br>
								<input type='button' id='add_custmast' class='btn btn-primary btn-sm' value='' style='width:100%'>
							</div>
						</div>
					</div>		
				</div>
			</div>
		";
		
		$html.= "<script src='".base_url('public/js/SYS04/CUSTOMERS.js')."'></script>";
		echo $html;
	}
	
	function SetTitle(){
		$claim = $this->MLogin->getclaim(uri_string());
		if($claim['m_access'] != "T"){ echo "<div align='center' style='color:red;font-size:16pt;width:100%;'>ขออภัย คุณยังไม่มีสิทธิเข้าใช้งานหน้านี้ครับ</div>"; exit; }
		
		$html = "
			<div class='tab1' name='home' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}' style='height:65px;overflow:auto;'>
				<div class=' col-sm-2'>	
					<div class='form-group'>
						รหัสคำนำหน้าชื่อ
						<input type='text' id='sircod' class='form-control input-sm' placeholder='รหัสคำนำหน้าชื่อ'>
					</div>
				</div>
				<div class=' col-sm-8'>	
					<div class='form-group'>
						คำนำหน้าชื่อ
						<input type='text' id='sirnam' class='form-control input-sm' placeholder='คำนำหน้าชื่อ'  data-provide='datepicker' data-date-language='th-th'>
					</div>
				</div>	
				<div class=' col-sm-1'>	
					<div class='form-group'>
						<br>
						<input type='button' id='search_groupsn' class='btn btn-primary btn-sm' value='แสดง' style='width:100%'>
					</div>
				</div>	
				<div class=' col-sm-1'>	
					<div class='form-group'>
						<br>
						<input type='button' id='add_groupsn' class='btn btn-cyan btn-sm' value='เพิ่มคำนำหน้า' style='width:100%'>
					</div>
				</div>
			</div>
			<div id='setgroupResult' class='col-sm-12 tab1' style='height:calc(100vh - 197px);overflow:auto;background-color:#;'></div>
	
			<div id='tab2_main' class='col-sm-12 tab2' hidden style='height:calc(100vh - 130px);overflow:auto;background-color:#;'></div>
		";
		$html.= "<script src='".base_url('public/js/SYS04/CUSTOMERS.js')."'></script>";
		echo $html;
	}
	function groupSearchsn(){
		$arrs = array();
		$arrs['sircod'] = !isset($_REQUEST['sircod']) ? '' : $_REQUEST['sircod'];
		$arrs['sirnam'] = !isset($_REQUEST['sirnam']) ? '' : $_REQUEST['sirnam'];
		
		$cond = "";
		if($arrs['sircod'] != ''){
			$cond .= " and SIRCOD like '%".$arrs['sircod']."%'";
		}
		
		if($arrs['sirnam'] != ''){
			$cond .= " and SIRNAM like '%".$arrs['sirnam']."%'";
		}
		
		$sql = "
			select * from {$this->MAuth->getdb('SIRNAM')}
			where 1=1 ".$cond." order by SIRCOD
		";
		//echo $sql ; exit;
		$query = $this->db->query($sql);
				
		$NRow = 1;
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$html .= "
					<tr class='trow' seq='".$NRow."'>
						<td class='getit' seq='".$NRow++."' SIRCOD='".str_replace(chr(0),'',$row->SIRCOD)."' style='width:50px;cursor:pointer;text-align:center;'><b>เลือก</b></td>
						<td>".str_replace(chr(0),'',$row->SIRCOD)."</td>
						<td>".str_replace(chr(0),'',$row->SIRNAM)."</td>
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
							<th>รหัสคำนำหน้าชื่อ</th>
							<th>คำนำหน้าชื่อ</th>							
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
	function groupGetFormSN(){
		$arrs = array();
		$arrs['SIRCOD'] = (!isset($_REQUEST['SIRCOD']) ? '' : $_REQUEST['SIRCOD']);
		
		$data = array(
			'SIRCOD'=>'',
			'SIRNAM'=>'',
		);
		if($arrs['SIRCOD'] != ''){
			$sql = "
				select * from {$this->MAuth->getdb('SIRNAM')}
				where SIRCOD='".$arrs['SIRCOD']."'
			";
			$query = $this->db->query($sql);
			
			if($query->row()){
				foreach($query->result() as $row){
					$data['SIRCOD'] = str_replace(chr(0),'',$row->SIRCOD); //readonly
					$data['SIRNAM'] = str_replace(chr(0),'',$row->SIRNAM);
				}
			}
		}
		$response = array();
		$response['html'] = "
			<div class='col-sm-12'>
				<div style='height:calc(100vh - 165px);overflow:auto;'>
					<div class='col-sm-4 col-sm-offset-4'>	
						<div class='form-group'>
							รหัสคำนำหน้าชื่อ
							<input type='text' id='t2sircod' class='form-control input-sm' placeholder='Auto Genarate' value='".$data['SIRCOD']."' readonly>
						</div>
					</div>
					
					<div class='col-sm-4 col-sm-offset-4'>	
						<div class='form-group'>
							คำนำหน้าชื่อ
							<input type='text' id='t2sirnam' class='form-control input-sm' value='".$data['SIRNAM']."'>
						</div>
					</div>
				</div>
				
				<div class='col-sm-1 col-sm-offset-4'>
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
	function groupSave(){
		$arrs = array();
		$arrs['sircod'] = (!isset($_REQUEST['sircod'])?'':$_REQUEST['sircod']);
		$arrs['sirnam'] = (!isset($_REQUEST['sirnam'])?'':$_REQUEST['sirnam']);
		$arrs['action'] = (!isset($_REQUEST['action'])?'':$_REQUEST['action']);
		//echo ($arrs); exit;
		
		if($arrs["sirnam"] == ""){
			$response["error"] = true;
			$response["msg"] = 'คุณยังไม่ได้ระบุคำนำหน้าเลย กรุณากรอกคำนำหน้าก่อนนะครับ';
			echo json_encode($response); exit;
		}
		
		$data = "";
		if($arrs['action'] == 'add'){
			$data = "
				declare @isval int = isnull((select count(*) from {$this->MAuth->getdb('SIRNAM')} where SIRNAM='".$arrs['sirnam']."'),0);
				declare @A varchar(20) = (select MAX(SIRCOD) from {$this->MAuth->getdb('SIRNAM')});
				declare @B varchar(20) = @A+1;
				if(@isval = 0)
				begin 
					insert into {$this->MAuth->getdb('SIRNAM')} (SIRCOD,SIRNAM)
					select @B,'".$arrs['sirnam']."'
					
					insert into {$this->MAuth->getdb('hp_UserOperationLog')}(userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
					values ('".$this->sess["IDNo"]."','กลุ่มคำนำหน้า เพิ่ม','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
				end 
				
				else
				begin 
					rollback tran tsc;
					insert into #tempolary select 'N' as id,'ไม่บันทึก : มีข้อมูลรหัสกลุ่ม ".$arrs['sirnam']." อยู่แล้ว' as msg;
					return;
				end
			";
		}else{			
			$data = "
				--declare @isval int = isnull((select count(*) from {$this->MAuth->getdb('SIRNAM')} where SIRCOD ='".$arrs['sircod']."'),0);
				--id(@isval = 0)
				begin
					update {$this->MAuth->getdb('SIRNAM')}
					set SIRNAM ='".$arrs['sirnam']."'
					where SIRCOD ='".$arrs['sircod']."'
					
					insert into {$this->MAuth->getdb('hp_UserOperationLog')}(userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
					values ('".$this->sess["IDNo"]."','กลุ่มคำนำหน้า แก้ไข','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
				end
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
				insert into #tempolary select 'N' as id,'บันทึกข้อมูลไม่สำเร็จ : กรุณาติดต่อฝ่ายไอที' as msg;
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
	function groupDel(){
		$arrs = array();
		$arrs['sircod'] = (!isset($_REQUEST['sircod'])?'':$_REQUEST['sircod']);
		$arrs['sirnam'] = (!isset($_REQUEST['sirnam'])?'':$_REQUEST['sirnam']);
		$arrs['action'] = (!isset($_REQUEST['action'])?'':$_REQUEST['action']);
		//echo ($arrs); exit;
		
		$data = "";
		if($arrs['action'] == 'del'){
			$data = "
				declare @isval int = isnull((select count(*) from {$this->MAuth->getdb('CUSTMAST')} where SIRCOD='".$arrs['sircod']."'),0);
				
				if(@isval = 0)
				begin 
					delete {$this->MAuth->getdb('SIRNAM')}
					where SIRCOD='".$arrs['sircod']."'
					
					insert into {$this->MAuth->getdb('hp_UserOperationLog')}(userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
					values ('".$this->sess["IDNo"]."','กลุ่มคำนำหน้า ลบ','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
				end 
				
				else
				begin 
					rollback tran tsc;
					insert into #tempolary select 'N' as id,'ไม่สามารถลบ: ข้อมูลรหัสกลุ่ม".$arrs['sircod']." เพราะได้นำไปใช้งานแล้ว' as msg;
					return;
				end
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
}




















