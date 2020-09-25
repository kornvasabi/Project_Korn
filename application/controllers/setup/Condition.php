<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/********************************************************
             ______@08/09/2020______
            / / _ _   _ _     __ 
           / // __ \ / __ \ / __ \
       _ _/ // /_/ // / / // /_/ /
     /_ _ _/ \_ _ //_/ /_/ \__  /
                          _ _/ /
                         /___ /
********************************************************/
class Condition extends MY_Controller {
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
	
	public function SETARGAR(){
		$claim = $this->MLogin->getclaim(uri_string());
		if($claim['m_access'] != "T"){ echo "<div align='center' style='color:red;font-size:16pt;width:100%;'>ขออภัย คุณยังไม่มีสิทธิเข้าใช้งานหน้านี้ครับ</div>"; exit; }
		
		$html = "
			<div class='tab1' name='home' locat='{$this->sess['branch']}' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}' >
				<div class='col-sm-2'>	
					<div class='form-group'>
						รหัสหลักทรัพย์
						<input type='text' id='GARCODE' class='form-control input-sm' placeholder='รหัสหลักทรัพย์'>
					</div>
				</div>
				<div class='col-sm-6'>	
					<div class='form-group'>
						คำอธิบาย
						<input type='text' id='GARDESC' class='form-control input-sm' placeholder='คำอธิบาย'>	
					</div>
				</div>					
				<div class='col-sm-2'>	
					<div class='form-group'>
						<br>
						<input type='button' id='search_argar' class='btn btn-primary btn-sm btn-block' value='แสดง'>
					</div>
				</div>
				<div class='col-sm-2'>	
					<div class='form-group'>
						<br>
						<input type='button' id='add_argar' class='btn btn-cyan btn-sm btn-block' value='เพิ่ม' >
					</div>
				</div>
			</div>
			
			<div id='setArgarResult' class='col-sm-12 tab1' style='height:calc(100vh - 197px);overflow:auto;background-color:#;'></div>
			
			<div id='tab2_main' class='col-sm-12 tab2' hidden style='height:calc(100vh - 130px);overflow:auto;background-color:#;'></div>
		";
	
		$html.= "<script src='".base_url('public/js/setup/Condition/setARGAR.js')."'></script>";
		echo $html;
	}
	
	public function SETARGARSearch(){
		$arrs = array();
		$arrs['GARCODE'] = !isset($_REQUEST['GARCODE']) ? '' : $_REQUEST['GARCODE'];
		$arrs['GARDESC'] = !isset($_REQUEST['GARDESC']) ? '' : $_REQUEST['GARDESC'];
		
		$cond = "";
		if($arrs['GARCODE'] != ''){
			$cond .= " and GARCODE like '%".$arrs['GARCODE']."%'";
		}
		
		if($arrs['GARDESC'] != ''){
			$cond .= " and GARDESC like '%".$arrs['GARDESC']."%'";
		}
		
		$sql = "
			select * from {$this->MAuth->getdb('SETARGAR')}
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
						<td class='getit' seq='".$NRow++."' GARCODE='".str_replace(chr(0),'',$row->GARCODE)."' style='width:50px;cursor:pointer;text-align:center;'><b>เลือก</b></td>
						<td>".str_replace(chr(0),'',$row->GARCODE)."</td>
						<td>".str_replace(chr(0),'',$row->GARDESC)."</td>
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
							<th>รหัสหลักทรัพย์</th>
							<th>คำอธิบาย</th>
							<th>หมายเหตุ</th>
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
	
	function SETARGARGetFormAE(){
		$GARCODE = $_POST["GARCODE"];
		$data = array(
			"GARCODE"=>""
			,"GARDESC"=>""
			,"MEMO1"=>""
		);
		
		if($GARCODE != ''){
			$sql = "
				select * from {$this->MAuth->getdb('SETARGAR')}
				where GARCODE='".$GARCODE."'
			";
			$query = $this->db->query($sql);
			
			if($query->row()){
				foreach($query->result() as $row){
					$data['GARCODE'] = str_replace(chr(0),'',$row->GARCODE);
					$data['GARDESC'] = str_replace(chr(0),'',$row->GARDESC);
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
							รหัสหลักทรัพย์
							<input type='text' id='t2GARCODE' class='form-control input-sm' value='".$data['GARCODE']."' maxlength=8>
						</div>
					</div>
					
					<div class='col-sm-4 col-sm-offset-4'>	
						<div class='form-group'>
							คำอธิบาย
							<input type='text' id='t2GARDESC' class='form-control input-sm' value='".$data['GARDESC']."' maxlength=60>
						</div>
					</div>
					
					<div class='col-sm-4 col-sm-offset-4'>	
						<div class='form-group'>
							หมายเหตุ
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
					<input type='button' id='tab2save' class='btn btn-primary btn-sm' style='width:100%;' action='' value='บันทึก'>
				</div>
			</div>	
		";
		
		echo json_encode($response);
	}
	
	function SETARGARSave(){
		$arrs = array();
		$arrs['GARCODE'] 	= $_POST['GARCODE'];
		$arrs['GARDESC'] 	= $_POST['GARDESC'];
		$arrs['MEMO1'] 		= $_POST['MEMO1'];
		
		$arrs['action']		= $_POST['action'];
		
		$data = "";
		if($arrs['action'] == "add"){
			$data = "
				declare @isval int = isnull((select count(*) from {$this->MAuth->getdb('SETARGAR')} where GARCODE='".$arrs['GARCODE']."'),0);
				if(@isval = 0)
				begin 
					insert into {$this->MAuth->getdb('SETARGAR')} (GARCODE,GARDESC,MEMO1)
					select '".$arrs['GARCODE']."','".$arrs['GARDESC']."','".$arrs['MEMO1']."'
					
					insert into {$this->MAuth->getdb('hp_UserOperationLog')}(userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
					values ('".$this->sess["IDNo"]."','setup รหัสหลักทรัพย์ เพิ่ม','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
				end 
				else
				begin 
					rollback tran tsc;
					insert into #tempolary select 'N' as id,'ไม่บันทึก : มีข้อมูลรหัสหลักทรัพย์ ".$arrs['GARCODE']." อยู่แล้ว' as msg;
					return;
				end
			";
		}else{			
			$data = "
				update {$this->MAuth->getdb('SETARGAR')}
				set GARDESC='".$arrs['GARDESC']."'
					,MEMO1='".$arrs['MEMO1']."'
				where GARCODE='".$arrs['GARCODE']."'
				
				insert into {$this->MAuth->getdb('hp_UserOperationLog')}(userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
				values ('".$this->sess["IDNo"]."','setup รหัสหลักทรัพย์ แก้ไข','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
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
	
	public function SETARGARDel(){
		$arrs = array();
		$arrs['GARCODE'] = (!isset($_REQUEST['GARCODE'])?'':$_REQUEST['GARCODE']);
		$arrs['GARDESC'] = (!isset($_REQUEST['GARDESC'])?'':$_REQUEST['GARDESC']);
		
		$sql = "
			if object_id('tempdb..#tempolary') is not null drop table #tempolary;
			create table #tempolary (id varchar(1),msg varchar(max));
			
			begin tran tsc
			begin try			
				delete {$this->MAuth->getdb('SETARGAR')}
				where GARCODE='".$arrs['GARCODE']."'
				
				insert into {$this->MAuth->getdb('hp_UserOperationLog')}(userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
				values ('".$this->sess["IDNo"]."','setup รหัสหลักทรัพย์ ลบ','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
				
				insert into #tempolary select 'Y' as id,'สำเร็จ ลบประเภทการชำระ ".$arrs['GARCODE']." :: ".$arrs['GARDESC']."  แล้ว' as msg;
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
	
	public function TYPCONT(){ 
		$claim = $this->MLogin->getclaim(uri_string());
		if($claim['m_access'] != "T"){ echo "<div align='center' style='color:red;font-size:16pt;width:100%;'>ขออภัย คุณยังไม่มีสิทธิเข้าใช้งานหน้านี้ครับ</div>"; exit; }
		
		$html = "
			<div class='tab1' name='home' locat='{$this->sess['branch']}' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}' >
				<div class='col-sm-2'>	
					<div class='form-group'>
						รหัสสถานะสัญญา
						<input type='text' id='CONTTYP' class='form-control input-sm' placeholder='รหัสสถานะสัญญา'>
					</div>
				</div>
				<div class='col-sm-6'>	
					<div class='form-group'>
						คำอธิบาย
						<input type='text' id='CONTDESC' class='form-control input-sm' placeholder='คำอธิบาย'>	
					</div>
				</div>					
				<div class='col-sm-2'>	
					<div class='form-group'>
						<br>
						<input type='button' id='search_typcont' class='btn btn-primary btn-sm btn-block' value='แสดง'>
					</div>
				</div>
				<div class='col-sm-2'>	
					<div class='form-group'>
						<br>
						<input type='button' id='add_typcont' class='btn btn-cyan btn-sm btn-block' value='เพิ่ม' >
					</div>
				</div>
			</div>
			
			<div id='setTypcontResult' class='col-sm-12 tab1' style='height:calc(100vh - 197px);overflow:auto;background-color:#;'></div>
			
			<div id='tab2_main' class='col-sm-12 tab2' hidden style='height:calc(100vh - 130px);overflow:auto;background-color:#;'></div>
		";
	
		$html.= "<script src='".base_url('public/js/setup/Condition/setTYPCONT.js')."'></script>";
		echo $html;
	}
	
	public function TYPCONTSearch(){
		$arrs = array();
		$arrs['CONTTYP'] = !isset($_REQUEST['CONTTYP']) ? '' : $_REQUEST['CONTTYP'];
		$arrs['CONTDESC'] = !isset($_REQUEST['CONTDESC']) ? '' : $_REQUEST['CONTDESC'];
		
		$cond = "";
		if($arrs['CONTTYP'] != ''){
			$cond .= " and CONTTYP like '%".$arrs['CONTTYP']."%'";
		}
		
		if($arrs['CONTDESC'] != ''){
			$cond .= " and CONTDESC like '%".$arrs['CONTDESC']."%'";
		}
		
		$sql = "
			select * from {$this->MAuth->getdb('TYPCONT')}
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
						<td class='getit' seq='".$NRow++."' CONTTYP='".str_replace(chr(0),'',$row->CONTTYP)."' style='width:50px;cursor:pointer;text-align:center;'><b>เลือก</b></td>
						<td>".str_replace(chr(0),'',$row->CONTTYP)."</td>
						<td>".str_replace(chr(0),'',$row->CONTDESC)."</td>
						<td>".str_replace(chr(0),'',$row->ALERT)."</td>
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
							<th>รหัสสถานะสัญญา</th>
							<th>คำอธิบาย</th>
							<th>แจ้งเตือน</th>
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
	
	function TYPCONTGetFormAE(){
		$CONTTYP = $_POST["CONTTYP"];
		$data = array(
			"CONTTYP"=>""
			,"CONTDESC"=>""
			,"ALERT"=>""
		);
		
		if($CONTTYP != ''){
			$sql = "
				select * from {$this->MAuth->getdb('TYPCONT')}
				where CONTTYP='".$CONTTYP."'
			";
			$query = $this->db->query($sql);
			
			if($query->row()){
				foreach($query->result() as $row){
					$data['CONTTYP'] = str_replace(chr(0),'',$row->CONTTYP);
					$data['CONTDESC'] = str_replace(chr(0),'',$row->CONTDESC);
					$data['ALERT'] = str_replace(chr(0),'',$row->ALERT);
				}
			}
		}
		
		$response = array();
		$response['html'] = "
			<div class='col-sm-12'>
				<div style='height:calc(100vh - 165px);overflow:auto;'>
					<div class='col-sm-4 col-sm-offset-4'>	
						<div class='form-group'>
							รหัสชำระ
							<input type='text' id='t2CONTTYP' class='form-control input-sm' value='".$data['CONTTYP']."' maxlength=1>
						</div>
					</div>
					
					<div class='col-sm-4 col-sm-offset-4'>	
						<div class='form-group'>
							ชำระค่า
							<input type='text' id='t2CONTDESC' class='form-control input-sm' value='".$data['CONTDESC']."' maxlength=60>
						</div>
					</div>
								
					
					<div class='col-sm-4 col-sm-offset-4'>	
						<div class='form-group'>
							<label class='checkbox lobicheck lobicheck-primary lobicheck-inversed'>
								<input type='checkbox' id='t2ALERT' ".($data['ALERT']=="Y"?"checked":"")."> 
								<i></i> แจ้งเตือนเมื่อชำระ
							</label>
						</div>
					</div>	

					<div class='col-sm-4 col-sm-offset-4'>	
						<div class='text-red'>
							*** สถานะ ป ลูกหนี้ปกติ เป็นค่า Defualt แก้ไขไม่ได้ ***
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
					<input type='button' id='tab2save' class='btn btn-primary btn-sm' style='width:100%;' action='' value='บันทึก'>
				</div>
			</div>	
		";
		
		echo json_encode($response);
	}
	
	function TYPCONTSave(){
		$arrs = array();
		$arrs['CONTTYP'] 	= $_POST['CONTTYP'];
		$arrs['CONTDESC'] 	= $_POST['CONTDESC'];
		$arrs['ALERT'] 		= $_POST['ALERT'];
		
		$arrs['action']		= $_POST['action'];
		
		$data = "";
		if($arrs['action'] == "add"){
			$data = "
				declare @isval int = isnull((select count(*) from {$this->MAuth->getdb('TYPCONT')} where CONTTYP='".$arrs['CONTTYP']."'),0);
				if(@isval = 0)
				begin 
					insert into {$this->MAuth->getdb('TYPCONT')} (CONTTYP,CONTDESC,ALERT)
					select '".$arrs['CONTTYP']."','".$arrs['CONTDESC']."','".$arrs['ALERT']."'
					
					insert into {$this->MAuth->getdb('hp_UserOperationLog')}(userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
					values ('".$this->sess["IDNo"]."','setup สถานะสัญญา เพิ่ม','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
				end 
				else
				begin 
					rollback tran tsc;
					insert into #tempolary select 'N' as id,'ไม่บันทึก : มีข้อมูลรหัสสถานะสัญญา ".$arrs['CONTTYP']." อยู่แล้ว' as msg;
					return;
				end
			";
		}else{			
			$data = "
				if('".$arrs['CONTTYP']."' = 'ป')
				begin 
					rollback tran tsc;
					insert into #tempolary select 'N' as id,'ไม่บันทึก : สถานะสัญญา ".$arrs['CONTTYP']." เป็นค่า Defualt แก้ไขไม่ได้' as msg;
					return;
				end else begin
					update {$this->MAuth->getdb('TYPCONT')}
					set CONTDESC='".$arrs['CONTDESC']."'
						,ALERT='".$arrs['ALERT']."'
					where CONTTYP='".$arrs['CONTTYP']."'
					
					insert into {$this->MAuth->getdb('hp_UserOperationLog')}(userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
					values ('".$this->sess["IDNo"]."','setup สถานะสัญญา แก้ไข','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
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
	
	public function TYPCONTDel(){
		$arrs = array();
		$arrs['CONTTYP'] = (!isset($_REQUEST['CONTTYP'])?'':$_REQUEST['CONTTYP']);
		$arrs['CONTDESC'] = (!isset($_REQUEST['CONTDESC'])?'':$_REQUEST['CONTDESC']);
		
		$sql = "
			if object_id('tempdb..#tempolary') is not null drop table #tempolary;
			create table #tempolary (id varchar(1),msg varchar(max));
			
			begin tran tsc
			begin try			
				delete {$this->MAuth->getdb('TYPCONT')}
				where CONTTYP='".$arrs['CONTTYP']."'
				
				insert into {$this->MAuth->getdb('hp_UserOperationLog')}(userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
				values ('".$this->sess["IDNo"]."','setup สถานะสัญญา ลบ','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
				
				insert into #tempolary select 'Y' as id,'สำเร็จ ลบรหัสสถานะสัญญา ".$arrs['CONTTYP']." :: ".$arrs['CONTDESC']."  แล้ว' as msg;
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
	
	public function INTRMAST(){ 
		$claim = $this->MLogin->getclaim(uri_string());
		if($claim['m_access'] != "T"){ echo "<div align='center' style='color:red;font-size:16pt;width:100%;'>ขออภัย คุณยังไม่มีสิทธิเข้าใช้งานหน้านี้ครับ</div>"; exit; }
		
		$html = "
			<div class='tab1' name='home' locat='{$this->sess['branch']}' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}' >
				<div class='col-sm-2'>	
					<div class='form-group'>
						จากวันที่
						<input type='text' id='FRMDATE' class='form-control input-sm' placeholder='จาก'  data-provide='datepicker' data-date-language='th-th' value=''>
					</div>
				</div>
				<div class='col-sm-6'>	
					<div class='form-group'>
						ถึงวันที่
						<input type='text' id='TODATE' class='form-control input-sm' placeholder='จาก'  data-provide='datepicker' data-date-language='th-th' value=''>
					</div>
				</div>					
				<div class='col-sm-2'>	
					<div class='form-group'>
						<br>
						<input type='button' id='search_intrmast' class='btn btn-primary btn-sm btn-block' value='แสดง'>
					</div>
				</div>
				<div class='col-sm-2'>	
					<div class='form-group'>
						<br>
						<input type='button' id='add_intrmast' class='btn btn-cyan btn-sm btn-block' value='เพิ่ม' >
					</div>
				</div>
			</div>
			
			<div id='setIntrmastResult' class='col-sm-12 tab1' style='height:calc(100vh - 197px);overflow:auto;background-color:#;'></div>
			
			<div id='tab2_main' class='col-sm-12 tab2' hidden style='height:calc(100vh - 130px);overflow:auto;background-color:#;'></div>
		";
	
		$html.= "<script src='".base_url('public/js/setup/Condition/setINTRMAST.js')."'></script>";
		echo $html;
	}
	
	public function INTRMASTSearch(){
		$arrs = array();
		$arrs['FRMDATE'] = !isset($_REQUEST['FRMDATE']) ? '' : $_REQUEST['FRMDATE'];
		$arrs['TODATE'] = !isset($_REQUEST['TODATE']) ? '' : $_REQUEST['TODATE'];
		
		$cond = "";
		if($arrs['FRMDATE'] != '' && $arrs['TODATE'] != ''){
			$cond .= " and FRMDATE >= '".$this->Convertdate(1,$arrs['FRMDATE'])."'  and TODATE <= '".$this->Convertdate(1,$arrs['TODATE'])."'";
		}
		
		if($arrs['TODATE'] != ''){
			$cond .= " and TODATE like '%".$arrs['TODATE']."%'";
		}
		
		$sql = "
			select * from {$this->MAuth->getdb('INTRMAST')}
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
						<td class='getit' seq='".$NRow++."' 
							FRMDATE='".$this->Convertdate(103,str_replace(chr(0),'',$row->FRMDATE))."' 
							TODATE='".$this->Convertdate(103,str_replace(chr(0),'',$row->TODATE))."'
							style='width:50px;cursor:pointer;text-align:center;'><b>เลือก</b></td>
						<td>".$this->Convertdate(103,str_replace(chr(0),'',$row->FRMDATE))."</td>
						<td>".$this->Convertdate(103,str_replace(chr(0),'',$row->TODATE))."</td>
						<td>".str_replace(chr(0),'',$row->INTR)."</td>
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
							<th>จากวันที่</th>
							<th>ถึงวันที่</th>
							<th>อัตราดอกเบี้ย</th>
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
	
	function INTRMASTGetFormAE(){
		$FRMDATE = $this->Convertdate(1,$_POST["FRMDATE"]);
		$TODATE  = $this->Convertdate(1,$_POST["TODATE"]);
		
		$data = array(
			"FRMDATE"=>""
			,"TODATE"=>""
			,"INTR"=>""
		);
		
		if($FRMDATE != ''){
			$sql = "
				select * from {$this->MAuth->getdb('INTRMAST')}
				where FRMDATE='".$FRMDATE."' and TODATE='".$TODATE."'
			";
			$query = $this->db->query($sql);
			
			if($query->row()){
				foreach($query->result() as $row){
					$data['FRMDATE'] = $this->Convertdate(103,str_replace(chr(0),'',$row->FRMDATE));
					$data['TODATE'] = $this->Convertdate(103,str_replace(chr(0),'',$row->TODATE));
					$data['INTR'] = str_replace(chr(0),'',$row->INTR);
				}
			}
		}
		
		$response = array();
		$response['html'] = "
			<div class='col-sm-12'>
				<div style='height:calc(100vh - 165px);overflow:auto;'>
					<div class='col-sm-4 col-sm-offset-4'>	
						<div class='form-group'>
							จากวันที่
							<input type='text' id='t2FRMDATE' class='form-control input-sm' placeholder='จาก'  data-provide='datepicker' data-date-language='th-th' value='".$data['FRMDATE']."'>
						</div>
					</div>
					
					<div class='col-sm-4 col-sm-offset-4'>	
						<div class='form-group'>
							ถึงวันที่
							<input type='text' id='t2TODATE' class='form-control input-sm' placeholder='จาก'  data-provide='datepicker' data-date-language='th-th' value='".$data['TODATE']."'>
						</div>
					</div>
					
					<div class='col-sm-4 col-sm-offset-4'>	
						<div class='form-group'>
							อัตราดอกเบี้ย
							<input type='number' step='0.01' id='t2INTR' class='form-control input-sm' placeholder='อัตราดอกเบี้ย' value='".$data['INTR']."' maxlength=6>
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
					<input type='button' id='tab2save' class='btn btn-primary btn-sm' style='width:100%;' action='' value='บันทึก'>
				</div>
			</div>	
		";
		
		echo json_encode($response);
	}
	
	function INTRMASTSave(){
		$arrs = array();
		$arrs['FRMDATE'] 	= $this->Convertdate(1,$_POST['FRMDATE']);
		$arrs['TODATE'] 	= $this->Convertdate(1,$_POST['TODATE']);
		$arrs['INTR'] 		= $_POST['INTR'];
		$arrs['action']		= $_POST['action'];
		
		$data = "";
		if($arrs['action'] == "add"){
			$data = "
				declare @isval int = isnull((
					select count(*) from {$this->MAuth->getdb('INTRMAST')} 
					where FRMDATE between '".$arrs['FRMDATE']."' and '".$arrs['TODATE']."'
						or TODATE between '".$arrs['FRMDATE']."' and '".$arrs['TODATE']."'
				),0);
				
				if(@isval = 0)
				begin 
					insert into {$this->MAuth->getdb('INTRMAST')} (FRMDATE,TODATE,INTR)
					select '".$arrs['FRMDATE']."','".$arrs['TODATE']."','".$arrs['INTR']."'
					
					insert into {$this->MAuth->getdb('hp_UserOperationLog')}(userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
					values ('".$this->sess["IDNo"]."','setup อัตราดอกเบี้ย ธ.กรุงไทย เพิ่ม','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
				end 
				else
				begin 
					rollback tran tsc;
					insert into #tempolary select 'N' as id,'ไม่บันทึก : มีข้อมูลอัตราดอกเบี้ย ธ.กรุงไทย ระหว่างวันที่ ".$arrs['FRMDATE']." ถึงวันที่ ".$arrs['TODATE']." อยู่แล้ว' as msg;
					return;
				end
			";
		}else{			
			$data = "
				update {$this->MAuth->getdb('INTRMAST')}
				set INTR='".$arrs['INTR']."'
				where FRMDATE='".$arrs['FRMDATE']."' and TODATE='".$arrs['TODATE']."'
				
				insert into {$this->MAuth->getdb('hp_UserOperationLog')}(userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
				values ('".$this->sess["IDNo"]."','setup อัตราดอกเบี้ย ธ.กรุงไทย แก้ไข','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
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
	
	public function INTRMASTDel(){
		$arrs = array();
		$arrs['FRMDATE'] = $this->Convertdate(1,(!isset($_REQUEST['FRMDATE'])?'':$_REQUEST['FRMDATE']));
		$arrs['TODATE'] = $this->Convertdate(1,(!isset($_REQUEST['TODATE'])?'':$_REQUEST['TODATE']));
		
		$sql = "
			if object_id('tempdb..#tempolary') is not null drop table #tempolary;
			create table #tempolary (id varchar(1),msg varchar(max));
			
			begin tran tsc
			begin try			
				delete {$this->MAuth->getdb('INTRMAST')}
				where FRMDATE='".$arrs['FRMDATE']."' and TODATE='".$arrs['TODATE']."'
				
				insert into {$this->MAuth->getdb('hp_UserOperationLog')}(userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
				values ('".$this->sess["IDNo"]."','setup อัตราดอกเบี้ย ธ.กรุงไทย ลบ','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
				
				insert into #tempolary select 'Y' as id,'สำเร็จ อัตราดอกเบี้ยจากวันที่ ".$arrs['FRMDATE']." ถึงวันที่ ".$arrs['TODATE']."  แล้ว' as msg;
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
	
	public function TYPLOST(){ 
		$claim = $this->MLogin->getclaim(uri_string());
		if($claim['m_access'] != "T"){ echo "<div align='center' style='color:red;font-size:16pt;width:100%;'>ขออภัย คุณยังไม่มีสิทธิเข้าใช้งานหน้านี้ครับ</div>"; exit; }
		
		$html = "
			<div class='tab1' name='home' locat='{$this->sess['branch']}' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}' >
				<div class='col-sm-2'>	
					<div class='form-group'>
						รหัสหนี้สูญ
						<input type='text' id='LOSTCOD' class='form-control input-sm' placeholder='รหัสหนี้สูญ'>
					</div>
				</div>
				<div class='col-sm-6'>	
					<div class='form-group'>
						คำอธิบาย
						<input type='text' id='LOSTESC' class='form-control input-sm' placeholder='คำอธิบาย'>	
					</div>
				</div>					
				<div class='col-sm-2'>	
					<div class='form-group'>
						<br>
						<input type='button' id='search_typlost' class='btn btn-primary btn-sm btn-block' value='แสดง'>
					</div>
				</div>
				<div class='col-sm-2'>	
					<div class='form-group'>
						<br>
						<input type='button' id='add_typlost' class='btn btn-cyan btn-sm btn-block' value='เพิ่ม' >
					</div>
				</div>
			</div>
			
			<div id='setTyplostResult' class='col-sm-12 tab1' style='height:calc(100vh - 197px);overflow:auto;background-color:#;'></div>
			
			<div id='tab2_main' class='col-sm-12 tab2' hidden style='height:calc(100vh - 130px);overflow:auto;background-color:#;'></div>
		";
	
		$html.= "<script src='".base_url('public/js/setup/Condition/setTYPLOST.js')."'></script>";
		echo $html;
	}
	
	public function TYPLOSTSearch(){
		$arrs = array();
		$arrs['LOSTCOD'] = !isset($_REQUEST['LOSTCOD']) ? '' : $_REQUEST['LOSTCOD'];
		$arrs['LOSTESC'] = !isset($_REQUEST['LOSTESC']) ? '' : $_REQUEST['LOSTESC'];
		
		$cond = "";
		if($arrs['LOSTCOD'] != ''){
			$cond .= " and LOSTCOD like '%".$arrs['LOSTCOD']."%'";
		}
		
		if($arrs['LOSTESC'] != ''){
			$cond .= " and LOSTESC like '%".$arrs['LOSTESC']."%'";
		}
		
		$sql = "
			select * from {$this->MAuth->getdb('TYPLOST')}
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
						<td class='getit' seq='".$NRow++."' LOSTCOD='".str_replace(chr(0),'',$row->LOSTCOD)."' style='width:50px;cursor:pointer;text-align:center;'><b>เลือก</b></td>
						<td>".str_replace(chr(0),'',$row->LOSTCOD)."</td>
						<td>".str_replace(chr(0),'',$row->LOSTESC)."</td>
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
							<th>รหัสการคืนเช็ค</th>
							<th>คำอธิบาย</th>
							<th>หมายเหตุ</th>
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
	
	function TYPLOSTGetFormAE(){
		$LOSTCOD = $_POST["LOSTCOD"];
		$data = array(
			"LOSTCOD"=>""
			,"LOSTESC"=>""
			,"MEMO1"=>""
		);
		
		if($LOSTCOD != ''){
			$sql = "
				select * from {$this->MAuth->getdb('TYPLOST')}
				where LOSTCOD='".$LOSTCOD."'
			";
			$query = $this->db->query($sql);
			
			if($query->row()){
				foreach($query->result() as $row){
					$data['LOSTCOD'] = str_replace(chr(0),'',$row->LOSTCOD);
					$data['LOSTESC'] = str_replace(chr(0),'',$row->LOSTESC);
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
							รหัสการคืนเช็ค
							<input type='text' id='t2LOSTCOD' class='form-control input-sm' value='".$data['LOSTCOD']."' maxlength=1>
						</div>
					</div>
					
					<div class='col-sm-4 col-sm-offset-4'>	
						<div class='form-group'>
							คำอธิบาย
							<input type='text' id='t2LOSTESC' class='form-control input-sm' value='".$data['LOSTESC']."' maxlength=60> 
						</div>
					</div>
					
					<div class='col-sm-4 col-sm-offset-4'>	
						<div class='form-group'>
							หมายเหตุ
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
					<input type='button' id='tab2save' class='btn btn-primary btn-sm' style='width:100%;' action='' value='บันทึก'>
				</div>
			</div>	
		";
		
		echo json_encode($response);
	}
	
	function TYPLOSTSave(){
		$arrs = array();
		$arrs['LOSTCOD'] 	= $_POST['LOSTCOD'];
		$arrs['LOSTESC'] 	= $_POST['LOSTESC'];
		$arrs['MEMO1'] 		= $_POST['MEMO1'];
		$arrs['action']		= $_POST['action'];
		
		$data = "";
		if($arrs['action'] == "add"){
			$data = "
				declare @isval int = isnull((select count(*) from {$this->MAuth->getdb('TYPLOST')} where LOSTCOD='".$arrs['LOSTCOD']."'),0);
				if(@isval = 0)
				begin 
					insert into {$this->MAuth->getdb('TYPLOST')} (LOSTCOD,LOSTESC,MEMO1)
					select '".$arrs['LOSTCOD']."','".$arrs['LOSTESC']."','".$arrs['MEMO1']."'
					
					insert into {$this->MAuth->getdb('hp_UserOperationLog')}(userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
					values ('".$this->sess["IDNo"]."','setup รหัสหนี้สูญ เพิ่ม','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
				end 
				else
				begin 
					rollback tran tsc;
					insert into #tempolary select 'N' as id,'ไม่บันทึก : มีข้อมูลรหัสหนี้สูญ ".$arrs['LOSTCOD']." อยู่แล้ว' as msg;
					return;
				end
			";
		}else{			
			$data = "
				update {$this->MAuth->getdb('TYPLOST')}
				set LOSTESC='".$arrs['LOSTESC']."'
					,MEMO1='".$arrs['MEMO1']."'
				where LOSTCOD='".$arrs['LOSTCOD']."'
				
				insert into {$this->MAuth->getdb('hp_UserOperationLog')}(userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
				values ('".$this->sess["IDNo"]."','setup รหัสหนี้สูญ แก้ไข','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
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
	
	public function TYPLOSTDel(){
		$arrs = array();
		$arrs['LOSTCOD'] = (!isset($_REQUEST['LOSTCOD'])?'':$_REQUEST['LOSTCOD']);
		$arrs['LOSTESC'] = (!isset($_REQUEST['LOSTESC'])?'':$_REQUEST['LOSTESC']);
		
		$sql = "
			if object_id('tempdb..#tempolary') is not null drop table #tempolary;
			create table #tempolary (id varchar(1),msg varchar(max));
			
			begin tran tsc
			begin try			
				delete {$this->MAuth->getdb('TYPLOST')}
				where LOSTCOD='".$arrs['LOSTCOD']."'
				
				insert into {$this->MAuth->getdb('hp_UserOperationLog')}(userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
				values ('".$this->sess["IDNo"]."','setup รหัสหนี้สูญ  ลบ','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
				
				insert into #tempolary select 'Y' as id,'สำเร็จ ลบรหัสหนี้สูญ ".$arrs['LOSTCOD']." :: ".$arrs['LOSTESC']."  แล้ว' as msg;
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
	
	public function TYPHOLD(){ 
		$claim = $this->MLogin->getclaim(uri_string());
		if($claim['m_access'] != "T"){ echo "<div align='center' style='color:red;font-size:16pt;width:100%;'>ขออภัย คุณยังไม่มีสิทธิเข้าใช้งานหน้านี้ครับ</div>"; exit; }
		
		$html = "
			<div class='tab1' name='home' locat='{$this->sess['branch']}' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}' >
				<div class='col-sm-2'>	
					<div class='form-group'>
						รหัสสาเหตุ
						<input type='text' id='HOLDCOD' class='form-control input-sm' placeholder='รหัสสาเหตุ'>
					</div>
				</div>
				<div class='col-sm-6'>	
					<div class='form-group'>
						คำอธิบาย
						<input type='text' id='HOLDESC' class='form-control input-sm' placeholder='คำอธิบาย'>	
					</div>
				</div>					
				<div class='col-sm-2'>	
					<div class='form-group'>
						<br>
						<input type='button' id='search_typhold' class='btn btn-primary btn-sm btn-block' value='แสดง'>
					</div>
				</div>
				<div class='col-sm-2'>	
					<div class='form-group'>
						<br>
						<input type='button' id='add_typhold' class='btn btn-cyan btn-sm btn-block' value='เพิ่ม' >
					</div>
				</div>
			</div>
			
			<div id='setTypholdResult' class='col-sm-12 tab1' style='height:calc(100vh - 197px);overflow:auto;background-color:#;'></div>
			
			<div id='tab2_main' class='col-sm-12 tab2' hidden style='height:calc(100vh - 130px);overflow:auto;background-color:#;'></div>
		";
	
		$html.= "<script src='".base_url('public/js/setup/Condition/setTYPHOLD.js')."'></script>";
		echo $html;
	}
	
	public function TYPHOLDSearch(){
		$arrs = array();
		$arrs['HOLDCOD'] = !isset($_REQUEST['HOLDCOD']) ? '' : $_REQUEST['HOLDCOD'];
		$arrs['HOLDESC'] = !isset($_REQUEST['HOLDESC']) ? '' : $_REQUEST['HOLDESC'];
		
		$cond = "";
		if($arrs['HOLDCOD'] != ''){
			$cond .= " and HOLDCOD like '%".$arrs['HOLDCOD']."%'";
		}
		
		if($arrs['HOLDESC'] != ''){
			$cond .= " and HOLDESC like '%".$arrs['HOLDESC']."%'";
		}
		
		$sql = "
			select * from {$this->MAuth->getdb('TYPHOLD')}
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
						<td class='getit' seq='".$NRow++."' HOLDCOD='".str_replace(chr(0),'',$row->HOLDCOD)."' style='width:50px;cursor:pointer;text-align:center;'><b>เลือก</b></td>
						<td>".str_replace(chr(0),'',$row->HOLDCOD)."</td>
						<td>".str_replace(chr(0),'',$row->HOLDESC)."</td>
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
							<th>รหัสสาเหตุ</th>
							<th>คำอธิบาย</th>
							<th>หมายเหตุ</th>
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
	
	function TYPHOLDGetFormAE(){
		$HOLDCOD = $_POST["HOLDCOD"];
		$data = array(
			"HOLDCOD"=>""
			,"HOLDESC"=>""
			,"MEMO1"=>""
		);
		
		if($HOLDCOD != ''){
			$sql = "
				select * from {$this->MAuth->getdb('TYPHOLD')}
				where HOLDCOD='".$HOLDCOD."'
			";
			$query = $this->db->query($sql);
			
			if($query->row()){
				foreach($query->result() as $row){
					$data['HOLDCOD'] = str_replace(chr(0),'',$row->HOLDCOD);
					$data['HOLDESC'] = str_replace(chr(0),'',$row->HOLDESC);
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
							รหัสสาเหตุ
							<input type='text' id='t2HOLDCOD' class='form-control input-sm' value='".$data['HOLDCOD']."' maxlength=1>
						</div>
					</div>
					
					<div class='col-sm-4 col-sm-offset-4'>	
						<div class='form-group'>
							คำอธิบาย
							<input type='text' id='t2HOLDESC' class='form-control input-sm' value='".$data['HOLDESC']."' maxlength=60>
						</div>
					</div>
					
					<div class='col-sm-4 col-sm-offset-4'>	
						<div class='form-group'>
							หมายเหตุ
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
					<input type='button' id='tab2save' class='btn btn-primary btn-sm' style='width:100%;' action='' value='บันทึก'>
				</div>
			</div>	
		";
		
		echo json_encode($response);
	}
	
	function TYPHOLDSave(){
		$arrs = array();
		$arrs['HOLDCOD'] 	= $_POST['HOLDCOD'];
		$arrs['HOLDESC'] 	= $_POST['HOLDESC'];
		$arrs['MEMO1'] 		= $_POST['MEMO1'];
		$arrs['action']		= $_POST['action'];
		
		$data = "";
		if($arrs['action'] == "add"){
			$data = "
				declare @isval int = isnull((select count(*) from {$this->MAuth->getdb('TYPHOLD')} where HOLDCOD='".$arrs['HOLDCOD']."'),0);
				if(@isval = 0)
				begin 
					insert into {$this->MAuth->getdb('TYPHOLD')} (HOLDCOD,HOLDESC,MEMO1)
					select '".$arrs['HOLDCOD']."','".$arrs['HOLDESC']."','".$arrs['MEMO1']."'
					
					insert into {$this->MAuth->getdb('hp_UserOperationLog')}(userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
					values ('".$this->sess["IDNo"]."','setup รหัสสาเหตุ เพิ่ม','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
				end 
				else
				begin 
					rollback tran tsc;
					insert into #tempolary select 'N' as id,'ไม่บันทึก : มีข้อมูลรหัสสาเหตุ ".$arrs['HOLDCOD']." อยู่แล้ว' as msg;
					return;
				end
			";
		}else{			
			$data = "
				update {$this->MAuth->getdb('TYPHOLD')}
				set HOLDESC='".$arrs['HOLDESC']."'
					,MEMO1='".$arrs['MEMO1']."'
				where HOLDCOD='".$arrs['HOLDCOD']."'
				
				insert into {$this->MAuth->getdb('hp_UserOperationLog')}(userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
				values ('".$this->sess["IDNo"]."','setup รหัสสาเหตุ แก้ไข','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
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
	
	public function TYPHOLDDel(){
		$arrs = array();
		$arrs['HOLDCOD'] = (!isset($_REQUEST['HOLDCOD'])?'':$_REQUEST['HOLDCOD']);
		$arrs['HOLDESC'] = (!isset($_REQUEST['HOLDESC'])?'':$_REQUEST['HOLDESC']);
		
		$sql = "
			if object_id('tempdb..#tempolary') is not null drop table #tempolary;
			create table #tempolary (id varchar(1),msg varchar(max));
			
			begin tran tsc
			begin try			
				delete {$this->MAuth->getdb('TYPHOLD')}
				where HOLDCOD='".$arrs['HOLDCOD']."'
				
				insert into {$this->MAuth->getdb('hp_UserOperationLog')}(userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
				values ('".$this->sess["IDNo"]."','setup รหัสสาเหตุ  ลบ','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
				
				insert into #tempolary select 'Y' as id,'สำเร็จ ลบรหัสสาเหตุ ".$arrs['HOLDCOD']." :: ".$arrs['HOLDESC']."  แล้ว' as msg;
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
	
	
	public function ARGROUP(){ 
		$claim = $this->MLogin->getclaim(uri_string());
		if($claim['m_access'] != "T"){ echo "<div align='center' style='color:red;font-size:16pt;width:100%;'>ขออภัย คุณยังไม่มีสิทธิเข้าใช้งานหน้านี้ครับ</div>"; exit; }
		
		$html = "
			<div class='tab1' name='home' locat='{$this->sess['branch']}' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}' >
				<div class='col-sm-2'>	
					<div class='form-group'>
						รหัสบัญชี
						<input type='text' id='ARGCOD' class='form-control input-sm' placeholder='รหัสบัญชี'>
					</div>
				</div>
				<div class='col-sm-6'>	
					<div class='form-group'>
						คำอธิบาย
						<input type='text' id='ARGDES' class='form-control input-sm' placeholder='คำอธิบาย'>	
					</div>
				</div>					
				<div class='col-sm-2'>	
					<div class='form-group'>
						<br>
						<input type='button' id='search_argroup' class='btn btn-primary btn-sm btn-block' value='แสดง'>
					</div>
				</div>
				<div class='col-sm-2'>	
					<div class='form-group'>
						<br>
						<input type='button' id='add_argroup' class='btn btn-cyan btn-sm btn-block' value='เพิ่ม' >
					</div>
				</div>
			</div>
			
			<div id='setArgroupResult' class='col-sm-12 tab1' style='height:calc(100vh - 197px);overflow:auto;background-color:#;'></div>
			
			<div id='tab2_main' class='col-sm-12 tab2' hidden style='height:calc(100vh - 130px);overflow:auto;background-color:#;'></div>
		";
	
		$html.= "<script src='".base_url('public/js/setup/Condition/setARGROUP.js')."'></script>";
		echo $html;
	}
	
	public function ARGROUPSearch(){
		$arrs = array();
		$arrs['ARGCOD'] = !isset($_REQUEST['ARGCOD']) ? '' : $_REQUEST['ARGCOD'];
		$arrs['ARGDES'] = !isset($_REQUEST['ARGDES']) ? '' : $_REQUEST['ARGDES'];
		
		$cond = "";
		if($arrs['ARGCOD'] != ''){
			$cond .= " and ARGCOD like '%".$arrs['ARGCOD']."%'";
		}
		
		if($arrs['ARGDES'] != ''){
			$cond .= " and ARGDES like '%".$arrs['ARGDES']."%'";
		}
		
		$sql = "
			select * from {$this->MAuth->getdb('ARGROUP')}
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
						<td class='getit' seq='".$NRow++."' ARGCOD='".str_replace(chr(0),'',$row->ARGCOD)."' style='width:50px;cursor:pointer;text-align:center;'><b>เลือก</b></td>
						<td>".str_replace(chr(0),'',$row->ARGCOD)."</td>
						<td>".str_replace(chr(0),'',$row->ARGDES)."</td>
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
							<th>รหัสกลุ่มลูกหนี้</th>
							<th>ชื่อกลุ่ม</th>
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
	
	function ARGROUPGetFormAE(){
		$ARGCOD = $_POST["ARGCOD"];
		$data = array(
			"ARGCOD"=>""
			,"ARGDES"=>""
		);
		
		if($ARGCOD != ''){
			$sql = "
				select * from {$this->MAuth->getdb('ARGROUP')}
				where ARGCOD='".$ARGCOD."'
			";
			$query = $this->db->query($sql);
			
			if($query->row()){
				foreach($query->result() as $row){
					$data['ARGCOD'] = str_replace(chr(0),'',$row->ARGCOD);
					$data['ARGDES'] = str_replace(chr(0),'',$row->ARGDES);
				}
			}
		}
		
		$response = array();
		$response['html'] = "
			<div class='col-sm-12'>
				<div style='height:calc(100vh - 165px);overflow:auto;'>
					<div class='col-sm-4 col-sm-offset-4'>	
						<div class='form-group'>
							รหัสกลุ่มลูกหนี้
							<input type='text' id='t2ARGCOD' class='form-control input-sm' value='".$data['ARGCOD']."' maxlength=3>
						</div>
					</div>
					
					<div class='col-sm-4 col-sm-offset-4'>	
						<div class='form-group'>
							ชื่อกลุ่ม
							<input type='text' id='t2ARGDES' class='form-control input-sm' value='".$data['ARGDES']."' maxlength=60>
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
					<input type='button' id='tab2save' class='btn btn-primary btn-sm' style='width:100%;' action='' value='บันทึก'>
				</div>
			</div>	
		";
		
		echo json_encode($response);
	}
	
	function ARGROUPSave(){
		$arrs = array();
		$arrs['ARGCOD'] 	= $_POST['ARGCOD'];
		$arrs['ARGDES'] 	= $_POST['ARGDES'];
		
		$arrs['action']		= $_POST['action'];
		
		$data = "";
		if($arrs['action'] == "add"){
			$data = "
				declare @isval int = isnull((select count(*) from {$this->MAuth->getdb('ARGROUP')} where ARGCOD='".$arrs['ARGCOD']."'),0);
				if(@isval = 0)
				begin 
					insert into {$this->MAuth->getdb('ARGROUP')} (ARGCOD,ARGDES)
					select '".$arrs['ARGCOD']."','".$arrs['ARGDES']."'
					
					insert into {$this->MAuth->getdb('hp_UserOperationLog')}(userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
					values ('".$this->sess["IDNo"]."','setup รหัสบัญชีนำฝาก เพิ่ม','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
				end 
				else
				begin 
					rollback tran tsc;
					insert into #tempolary select 'N' as id,'ไม่บันทึก : มีข้อมูลรหัสบัญชีนำฝาก ".$arrs['ARGCOD']." อยู่แล้ว' as msg;
					return;
				end
			";
		}else{			
			$data = "
				update {$this->MAuth->getdb('ARGROUP')}
				set ARGDES='".$arrs['ARGDES']."'
				where ARGCOD='".$arrs['ARGCOD']."'
				
				insert into {$this->MAuth->getdb('hp_UserOperationLog')}(userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
				values ('".$this->sess["IDNo"]."','setup รหัสบัญชีนำฝาก แก้ไข','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
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
	
	public function ARGROUPDel(){
		$arrs = array();
		$arrs['ARGCOD'] = (!isset($_REQUEST['ARGCOD'])?'':$_REQUEST['ARGCOD']);
		$arrs['ARGDES'] = (!isset($_REQUEST['ARGDES'])?'':$_REQUEST['ARGDES']);
		
		$sql = "
			if object_id('tempdb..#tempolary') is not null drop table #tempolary;
			create table #tempolary (id varchar(1),msg varchar(max));
			
			begin tran tsc
			begin try			
				delete {$this->MAuth->getdb('ARGROUP')}
				where ARGCOD='".$arrs['ARGCOD']."'
				
				insert into {$this->MAuth->getdb('hp_UserOperationLog')}(userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
				values ('".$this->sess["IDNo"]."','setup รหัสบัญชีนำฝาก  ลบ','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
				
				insert into #tempolary select 'Y' as id,'สำเร็จ ลบรหัสบัญชีนำฝาก ".$arrs['ARGCOD']." :: ".$arrs['ARGDES']."  แล้ว' as msg;
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
	
	public function SETGRADCUS(){ 
		$claim = $this->MLogin->getclaim(uri_string());
		if($claim['m_access'] != "T"){ echo "<div align='center' style='color:red;font-size:16pt;width:100%;'>ขออภัย คุณยังไม่มีสิทธิเข้าใช้งานหน้านี้ครับ</div>"; exit; }
		
		$html = "
			<div class='tab1' name='home' locat='{$this->sess['branch']}' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}' >
				<div class='col-sm-2'>	
					<div class='form-group'>
						เกรด
						<input type='text' id='GRDCOD' class='form-control input-sm' placeholder='รหัสบัญชี'>
					</div>
				</div>
				<div class='col-sm-6'>	
					<div class='form-group'>
						รายละเอียด
						<input type='text' id='GRDDES' class='form-control input-sm' placeholder='รายละเอียด'>	
					</div>
				</div>					
				<div class='col-sm-2'>	
					<div class='form-group'>
						<br>
						<input type='button' id='search_gradcus' class='btn btn-primary btn-sm btn-block' value='แสดง'>
					</div>
				</div>
				<div class='col-sm-2'>	
					<div class='form-group'>
						<br>
						<input type='button' id='add_gradcus' class='btn btn-cyan btn-sm btn-block' value='เพิ่ม' >
					</div>
				</div>
			</div>
			
			<div id='setGradcusResult' class='col-sm-12 tab1' style='height:calc(100vh - 197px);overflow:auto;background-color:#;'></div>
			
			<div id='tab2_main' class='col-sm-12 tab2' hidden style='height:calc(100vh - 130px);overflow:auto;background-color:#;'></div>
		";
	
		$html.= "<script src='".base_url('public/js/setup/Condition/setGRADCUS.js')."'></script>";
		echo $html;
	}
	
	public function SETGRADCUSSearch(){
		$arrs = array();
		$arrs['GRDCOD'] = !isset($_REQUEST['GRDCOD']) ? '' : $_REQUEST['GRDCOD'];
		$arrs['GRDDES'] = !isset($_REQUEST['GRDDES']) ? '' : $_REQUEST['GRDDES'];
		
		$cond = "";
		if($arrs['GRDCOD'] != ''){
			$cond .= " and GRDCOD like '%".$arrs['GRDCOD']."%'";
		}
		
		if($arrs['GRDDES'] != ''){
			$cond .= " and GRDDES like '%".$arrs['GRDDES']."%'";
		}
		
		$sql = "
			select * from {$this->MAuth->getdb('SETGRADCUS')}
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
						<td class='getit' seq='".$NRow++."' GRDCOD='".str_replace(chr(0),'',$row->GRDCOD)."' style='width:50px;cursor:pointer;text-align:center;'><b>เลือก</b></td>
						<td>".str_replace(chr(0),'',$row->GRDCOD)."</td>
						<td>".str_replace(chr(0),'',$row->GRDDES)."</td>
						<td>".str_replace(chr(0),'',$row->GRDFLG)."</td>
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
							<th>เกรด</th>
							<th>รายละเอียด</th>
							<th>ควรปล่อยสินเชื่อ</th>
							<th>หมายเหตุ</th>
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
	
	function SETGRADCUSGetFormAE(){
		$GRDCOD = $_POST["GRDCOD"];
		$data = array(
			"GRDCOD"=>""
			,"GRDDES"=>""
			,"GRDFLG"=>""
			,"MEMO1"=>""
		);
		
		if($GRDCOD != ''){
			$sql = "
				select * from {$this->MAuth->getdb('SETGRADCUS')}
				where GRDCOD='".$GRDCOD."'
			";
			$query = $this->db->query($sql);
			
			if($query->row()){
				foreach($query->result() as $row){
					$data['GRDCOD'] = str_replace(chr(0),'',$row->GRDCOD);
					$data['GRDDES'] = str_replace(chr(0),'',$row->GRDDES);
					$data['GRDFLG'] = str_replace(chr(0),'',$row->GRDFLG);
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
							รหัสบัญชี
							<input type='text' id='t2GRDCOD' class='form-control input-sm' value='".$data['GRDCOD']."' maxlength=2>
						</div>
					</div>
					
					<div class='col-sm-4 col-sm-offset-4'>	
						<div class='form-group'>
							คำอธิบาย
							<input type='text' id='t2GRDDES' class='form-control input-sm' value='".$data['GRDDES']."' maxlength=60>
						</div>
					</div>
					
					<div class='col-sm-4 col-sm-offset-4'>	
						<div class='form-group'>
							<label class='checkbox lobicheck lobicheck-primary lobicheck-inversed'>
								<input type='checkbox' id='t2GRDFLG' ".($data['GRDFLG']=="Y"?"checked":"")."> 
								<i></i> ควรปล่อยสินเชื่อ
							</label>
						</div>
					</div>
					
					<div class='col-sm-4 col-sm-offset-4'>	
						<div class='form-group'>
							หมายเหตุ
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
					<input type='button' id='tab2save' class='btn btn-primary btn-sm' style='width:100%;' action='' value='บันทึก'>
				</div>
			</div>	
		";
		
		echo json_encode($response);
	}
	
	function SETGRADCUSSave(){
		$arrs = array();
		$arrs['GRDCOD'] 	= $_POST['GRDCOD'];
		$arrs['GRDDES'] 	= $_POST['GRDDES'];
		$arrs['GRDFLG'] 	= $_POST['GRDFLG'];
		$arrs['MEMO1'] 		= $_POST['MEMO1'];
		$arrs['action']		= $_POST['action'];
		
		$data = "";
		if($arrs['action'] == "add"){
			$data = "
				declare @isval int = isnull((select count(*) from {$this->MAuth->getdb('SETGRADCUS')} where GRDCOD='".$arrs['GRDCOD']."'),0);
				if(@isval = 0)
				begin 
					insert into {$this->MAuth->getdb('SETGRADCUS')} (GRDCOD,GRDDES,GRDFLG,MEMO1)
					select '".$arrs['GRDCOD']."','".$arrs['GRDDES']."','".$arrs['GRDFLG']."','".$arrs['MEMO1']."'
					
					insert into {$this->MAuth->getdb('hp_UserOperationLog')}(userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
					values ('".$this->sess["IDNo"]."','setup เกรดลูกค้า เพิ่ม','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
				end 
				else
				begin 
					rollback tran tsc;
					insert into #tempolary select 'N' as id,'ไม่บันทึก : มีข้อมูลเกรดลูกค้า ".$arrs['GRDCOD']." อยู่แล้ว' as msg;
					return;
				end
			";
		}else{			
			$data = "
				update {$this->MAuth->getdb('SETGRADCUS')}
				set GRDDES='".$arrs['GRDDES']."'
					,GRDFLG='".$arrs['GRDFLG']."'
					,MEMO1='".$arrs['MEMO1']."'
				where GRDCOD='".$arrs['GRDCOD']."'
				
				insert into {$this->MAuth->getdb('hp_UserOperationLog')}(userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
				values ('".$this->sess["IDNo"]."','setup เกรดลูกค้า แก้ไข','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
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
	
	public function SETGRADCUSDel(){
		$arrs = array();
		$arrs['GRDCOD'] = (!isset($_REQUEST['GRDCOD'])?'':$_REQUEST['GRDCOD']);
		$arrs['GRDDES'] = (!isset($_REQUEST['GRDDES'])?'':$_REQUEST['GRDDES']);
		
		$sql = "
			if object_id('tempdb..#tempolary') is not null drop table #tempolary;
			create table #tempolary (id varchar(1),msg varchar(max));
			
			begin tran tsc
			begin try			
				delete {$this->MAuth->getdb('SETGRADCUS')}
				where GRDCOD='".$arrs['GRDCOD']."'
				
				insert into {$this->MAuth->getdb('hp_UserOperationLog')}(userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
				values ('".$this->sess["IDNo"]."','setup เกรดลูกค้า  ลบ','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
				
				insert into #tempolary select 'Y' as id,'สำเร็จ ลบเกรดลูกค้า ".$arrs['GRDCOD']." :: ".$arrs['GRDDES']."  แล้ว' as msg;
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
	
	public function SETGRADE(){ 
		$claim = $this->MLogin->getclaim(uri_string());
		if($claim['m_access'] != "T"){ echo "<div align='center' style='color:red;font-size:16pt;width:100%;'>ขออภัย คุณยังไม่มีสิทธิเข้าใช้งานหน้านี้ครับ</div>"; exit; }
		
		$html = "
			<div class='tab1' name='home' locat='{$this->sess['branch']}' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}' >
				<div class='col-sm-2'>	
					<div class='form-group'>
						เกรด
						<input type='text' id='GRDCOD' class='form-control input-sm' placeholder='เกรด'>
					</div>
				</div>
				<div class='col-sm-6'>	
					<div class='form-group'>
						รายละเอียด
						<input type='text' id='GRDDES' class='form-control input-sm' placeholder='รายละเอียด'>	
					</div>
				</div>					
				<div class='col-sm-2'>	
					<div class='form-group'>
						<br>
						<input type='button' id='search_grade' class='btn btn-primary btn-sm btn-block' value='แสดง'>
					</div>
				</div>
				<div class='col-sm-2'>	
					<div class='form-group'>
						<br>
						<input type='button' id='add_grade' class='btn btn-cyan btn-sm btn-block' value='เพิ่ม' >
					</div>
				</div>
			</div>
			
			<div id='setGradeResult' class='col-sm-12 tab1' style='height:calc(100vh - 197px);overflow:auto;background-color:#;'></div>
			
			<div id='tab2_main' class='col-sm-12 tab2' hidden style='height:calc(100vh - 130px);overflow:auto;background-color:#;'></div>
		";
	
		$html.= "<script src='".base_url('public/js/setup/Condition/setGRADE.js')."'></script>";
		echo $html;
	}
	
	public function SETGRADESearch(){
		$arrs = array();
		$arrs['GRDCOD'] = !isset($_REQUEST['GRDCOD']) ? '' : $_REQUEST['GRDCOD'];
		$arrs['GRDDES'] = !isset($_REQUEST['GRDDES']) ? '' : $_REQUEST['GRDDES'];
		
		$cond = "";
		if($arrs['GRDCOD'] != ''){
			$cond .= " and GRDCOD like '%".$arrs['GRDCOD']."%'";
		}
		
		if($arrs['GRDDES'] != ''){
			$cond .= " and GRDDES like '%".$arrs['GRDDES']."%'";
		}
		
		$sql = "
			select * from {$this->MAuth->getdb('SETGRADE')}
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
						<td class='getit' seq='".$NRow++."' GRDCOD='".str_replace(chr(0),'',$row->GRDCOD)."' style='width:50px;cursor:pointer;text-align:center;'><b>เลือก</b></td>
						<td>".str_replace(chr(0),'',$row->GRDCOD)."</td>
						<td>".str_replace(chr(0),'',$row->GRDDES)."</td>
						<td>".str_replace(chr(0),'',$row->GRDCAL)."</td>
						<td>".str_replace(chr(0),'',$row->GRDFLG)."</td>
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
							<th>เกรด</th>
							<th>รายละเอียด</th>
							<th>จน.งวดที่ขาด/ล่าช้าไม่เกิน X งวด</th>
							<th>ควรปล่อยสินเชื่อ</th>
							<th>หมายเหตุ</th>
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
	
	function SETGRADEGetFormAE(){
		$GRDCOD = $_POST["GRDCOD"];
		$data = array(
			"GRDCOD"=>""
			,"GRDDES"=>""
			,"GRDCAL"=>""
			,"GRDFLG"=>""
			,"MEMO1"=>""
		);
		
		if($GRDCOD != ''){
			$sql = "
				select * from {$this->MAuth->getdb('SETGRADE')}
				where GRDCOD='".$GRDCOD."'
			";
			$query = $this->db->query($sql);
			
			if($query->row()){
				foreach($query->result() as $row){
					$data['GRDCOD'] = str_replace(chr(0),'',$row->GRDCOD);
					$data['GRDDES'] = str_replace(chr(0),'',$row->GRDDES);
					$data['GRDCAL'] = str_replace(chr(0),'',$row->GRDCAL);
					$data['GRDFLG'] = str_replace(chr(0),'',$row->GRDFLG);
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
							เกรด
							<input type='text' id='t2GRDCOD' class='form-control input-sm' value='".$data['GRDCOD']."' maxlength=2>
						</div>
					</div>
					
					<div class='col-sm-4 col-sm-offset-4'>	
						<div class='form-group'>
							รายละเอียด
							<input type='text' id='t2GRDDES' class='form-control input-sm' value='".$data['GRDDES']."' maxlength=60>
						</div>
					</div>
					
					<div class='col-sm-4 col-sm-offset-4'>	
						<div class='form-group'>
							จน.งวดที่ขาด/ล่าช้าไม่เกิน X งวด
							<input type='number' id='t2GRDCAL' class='form-control input-sm' value='".$data['GRDCAL']."' maxlength=5>
						</div>
					</div>
					
					<div class='col-sm-4 col-sm-offset-4'>	
						<div class='form-group'>
							<label class='checkbox lobicheck lobicheck-primary lobicheck-inversed'>
								<input type='checkbox' id='t2GRDFLG' ".($data['GRDFLG']=="Y"?"checked":"")."> 
								<i></i> ควรปล่อยสินเชื่อ
							</label>
						</div>
					</div>
					
					<div class='col-sm-4 col-sm-offset-4'>	
						<div class='form-group'>
							หมายเหตุ
							<textarea id='t2MEMO1' class='form-control input-sm'>".$data['MEMO1']."</textarea>
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
					<input type='button' id='tab2save' class='btn btn-primary btn-sm' style='width:100%;' action='' value='บันทึก'>
				</div>
			</div>	
		";
		
		echo json_encode($response);
	}
	
	function SETGRADESave(){
		$arrs = array();
		$arrs['GRDCOD'] 	= $_POST['GRDCOD'];
		$arrs['GRDDES'] 	= $_POST['GRDDES'];
		$arrs['GRDCAL'] 	= $_POST['GRDCAL'];
		$arrs['GRDFLG'] 	= $_POST['GRDFLG'];
		$arrs['MEMO1'] 		= $_POST['MEMO1'];
		$arrs['action']		= $_POST['action'];
		
		$data = "";
		if($arrs['action'] == "add"){
			$data = "
				declare @isval int = isnull((select count(*) from {$this->MAuth->getdb('SETGRADE')} where GRDCOD='".$arrs['GRDCOD']."'),0);
				if(@isval = 0)
				begin 
					insert into {$this->MAuth->getdb('SETGRADE')} (GRDCOD,GRDDES,GRDCAL,GRDFLG,MEMO1)
					select '".$arrs['GRDCOD']."','".$arrs['GRDDES']."','".$arrs['GRDCAL']."','".$arrs['GRDFLG']."','".$arrs['MEMO1']."'
					
					insert into {$this->MAuth->getdb('hp_UserOperationLog')}(userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
					values ('".$this->sess["IDNo"]."','setup เกรดสัญญา เพิ่ม','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
				end 
				else
				begin 
					rollback tran tsc;
					insert into #tempolary select 'N' as id,'ไม่บันทึก : มีข้อมูลรหัสเกรดสัญญา ".$arrs['GRDCOD']." อยู่แล้ว' as msg;
					return;
				end
			";
		}else{			
			$data = "
				update {$this->MAuth->getdb('SETGRADE')}
				set GRDDES='".$arrs['GRDDES']."'
					,GRDCAL='".$arrs['GRDCAL']."'
					,GRDFLG='".$arrs['GRDFLG']."'
					,MEMO1='".$arrs['MEMO1']."'
				where GRDCOD='".$arrs['GRDCOD']."'
				
				insert into {$this->MAuth->getdb('hp_UserOperationLog')}(userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
				values ('".$this->sess["IDNo"]."','setup เกรดสัญญา แก้ไข','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
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
	
	public function SETGRADEDel(){
		$arrs = array();
		$arrs['GRDCOD'] = (!isset($_REQUEST['GRDCOD'])?'':$_REQUEST['GRDCOD']);
		$arrs['GRDDES'] = (!isset($_REQUEST['GRDDES'])?'':$_REQUEST['GRDDES']);
		
		$sql = "
			if object_id('tempdb..#tempolary') is not null drop table #tempolary;
			create table #tempolary (id varchar(1),msg varchar(max));
			
			begin tran tsc
			begin try			
				delete {$this->MAuth->getdb('SETGRADE')}
				where GRDCOD='".$arrs['GRDCOD']."'
				
				insert into {$this->MAuth->getdb('hp_UserOperationLog')}(userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
				values ('".$this->sess["IDNo"]."','setup เกรดสัญญา  ลบ','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
				
				insert into #tempolary select 'Y' as id,'สำเร็จ ลบรหัสเกรดสัญญา ".$arrs['GRDCOD']." :: ".$arrs['GRDDES']."  แล้ว' as msg;
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
	
	
}




















