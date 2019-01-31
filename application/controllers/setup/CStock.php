<?php
defined('BASEPATH') OR exit('No direct script access allowed');

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
			<div class='tab1' name='home' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}' style='height:65px;overflow:auto;'>
				<div class='col-sm-2'>	
					<div class='form-group'>
						รหัสกลุ่ม
						<input type='text' id='gcode' class='form-control input-sm' placeholder='รหัสกลุ่ม'>
					</div>
				</div>
				<div class='col-sm-8'>	
					<div class='form-group'>
						ชื่อกลุ่ม
						<input type='text' id='gdesc' class='form-control input-sm' placeholder='ชื่อกลุ่ม'>	
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
			<div id='setgroupResult' class='col-sm-12 tab1' style='height:calc(100vh - 197px);overflow:auto;background-color:#;'></div>
			
			<div id='tab2_main' class='col-sm-12 tab2' hidden style='height:calc(100vh - 130px);overflow:auto;background-color:#;'></div>
		";
	
		$html.= "<script src='".base_url('public/js/setup/setgroup.js')."'></script>";
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
							<input type='text' id='t2gcode' class='form-control input-sm' value='".$data['GCODE']."'>
						</div>
					</div>
					
					<div class='col-sm-4 col-sm-offset-4'>	
						<div class='form-group'>
							ชื่อกลุ่ม
							<input type='text' id='t2gdesc' class='form-control input-sm' value='".$data['GDESC']."'>
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
					select '".$arrs['gcode']."','".$arrs['gdesc']."',MEMO1='".$arrs['memo1']."'
					
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
				set GDESC='".$arrs['gdesc']."',MEMO1='".$arrs['memo1']."'
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
		$html = "
			<div class='tab1' name='home' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}' style='height:65px;overflow:auto;'>
				<div class='col-sm-2'>	
					<div class='form-group'>
						รหัสกลุ่ม
						<input type='text' id='gcode' class='form-control input-sm' placeholder='รหัสกลุ่ม'>
					</div>
				</div>
				<div class='col-sm-8'>	
					<div class='form-group'>
						ชื่อกลุ่ม
						<input type='text' id='gdesc' class='form-control input-sm' placeholder='ชื่อกลุ่ม'>	
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
			<div id='setgroupResult' class='col-sm-12 tab1' style='height:calc(100vh - 197px);overflow:auto;background-color:#;'></div>
			
			<div id='tab2_main' class='col-sm-12 tab2' hidden style='height:calc(100vh - 130px);overflow:auto;background-color:#;'></div>
		";
	
		$html.= "<script src='".base_url('public/js/setup/setgroup.js')."'></script>";
		echo $html;
	}
	
	
	public function maxstock(){
		$claim = $this->MLogin->getclaim(uri_string());
		if($claim['m_access'] != "T"){ echo "<div align='center' style='color:red;font-size:16pt;width:100%;'>ขออภัย คุณยังไม่มีสิทธิเข้าใช้งานหน้านี้ครับ</div>"; exit; }
		
		$html = "
			<div class='tab1' name='home' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}' style='height:65px;overflow:auto;'>
				<div class='col-sm-2'>	
					<div class='form-group'>
						สาขา
						<input type='text' id='locat' class='form-control input-sm' data-alert='สาขา' placeholder='สาขา'>
					</div>
				</div>
				<div class='col-sm-8'>	
					<div class='form-group'>
						จังหวัด
						<select class='form-control input-sm'>
							<option value='x'>x</option>
							<option value='xx'>xx</option>
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
	
		$html.= "<script src='".base_url('public/js/setup/setmaxstock.js')."'></script>";
		echo $html;
	}
	
	function maxstock_search(){
		
		$sql = "
			select * from {$this->MAuth->getdb('std_locatStock')}
			order by Prov,LINE,AREA,LOCAT
		";
		$query = $this->db->query($sql);
		
		$html = "";
		$NRow = 1;
		if($query->row()){
			foreach($query->result() as $row){
				$html.= "
					<tr class='trow' seq=".$NRow.">
						<td class='getit' seq=".$NRow++." align='center'>
							<b><i class='glyphicon glyphicon-edit mst-edit' LOCAT='".$row->LOCAT."'  style='cursor:pointer;'></i></b>
						</td>
						<td>".$row->LOCAT."</td>
						<td>".$row->Prov."</td>
						<td>".$row->LINE."</td>
						<td>".$row->AREA."</td>
						<td>".$row->MaxStockN."</td>
						<td>".$row->MaxStockO."</td>
						<td>".$row->MaxStock."</td>
						<td>".$row->MaxStore."</td>
						<td>".$row->locatStatus."</td>
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
	
	public function maxstock_form_edit(){
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
								สาขา
								<input class='form-control input-sm' value='".$row->LOCAT."'>
							</div>
							<div class='form-group'>
								สาขา
								<input class='form-control input-sm' value='".$row->LOCAT."'>
							</div>
							
						</div>
					</div>
				";				
			}
		}
		
		$response = array("html"=>$html);
		echo json_encode($response);
	}
}




















