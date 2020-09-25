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
class Company extends MY_Controller {
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
	
	public function Debtor(){
		$claim = $this->MLogin->getclaim(uri_string());
		if($claim['m_access'] != "T"){ echo "<div align='center' style='color:red;font-size:16pt;width:100%;'>ขออภัย คุณยังไม่มีสิทธิเข้าใช้งานหน้านี้ครับ</div>"; exit; }
		
		$html = "
			<div class='tab1' name='home' locat='{$this->sess['branch']}' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}' >
				<div class='col-sm-2'>	
					<div class='form-group'>
						รหัสเจ้าหนี้
						<input type='text' id='apcode' class='form-control input-sm' placeholder='รหัสเจ้าหนี้'>
					</div>
				</div>
				<div class='col-sm-6'>	
					<div class='form-group'>
						ชื่อเจ้าหนี้
						<input type='text' id='apname' class='form-control input-sm' placeholder='ชื่อเจ้าหนี้'>	
					</div>
				</div>					
				<div class='col-sm-2'>	
					<div class='form-group'>
						<br>
						<input type='button' id='search_debtor' class='btn btn-primary btn-sm btn-block' value='แสดง'>
					</div>
				</div>
				<div class='col-sm-2'>	
					<div class='form-group'>
						<br>
						<input type='button' id='add_debtor' class='btn btn-cyan btn-sm btn-block' value='เพิ่ม' >
					</div>
				</div>
			</div>
			
			<div id='setDebtorResult' class='col-sm-12 tab1' style='height:calc(100vh - 197px);overflow:auto;background-color:#;'></div>
			
			<div id='tab2_main' class='col-sm-12 tab2' hidden style='height:calc(100vh - 130px);overflow:auto;background-color:#;'></div>
		";
	
		$html.= "<script src='".base_url('public/js/setup/Company/setDebtor.js')."'></script>";
		echo $html;
	}
	
	public function DebtorSearch(){
		$arrs = array();
		$arrs['apcode'] = !isset($_REQUEST['apcode']) ? '' : $_REQUEST['apcode'];
		$arrs['apname'] = !isset($_REQUEST['apname']) ? '' : $_REQUEST['apname'];
		
		$cond = "";
		if($arrs['apcode'] != ''){
			$cond .= " and APCODE like '%".$arrs['apcode']."%'";
		}
		
		if($arrs['apname'] != ''){
			$cond .= " and APNAME like '%".$arrs['apname']."%'";
		}
		
		$sql = "
			select * from {$this->MAuth->getdb('APMAST')}
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
						<td class='getit' seq='".$NRow++."' apcode='".str_replace(chr(0),'',$row->APCODE)."' style='width:50px;cursor:pointer;text-align:center;'><b>เลือก</b></td>
						<td>".str_replace(chr(0),'',$row->APCODE)."</td>
						<td>".str_replace(chr(0),'',$row->APNAME)."</td>
						<td>".str_replace(chr(0),'',$row->APADDR1)."</td>
						<td>".str_replace(chr(0),'',$row->APADDR2)."</td>
						<td>".str_replace(chr(0),'',$row->ACC_CODE)."</td>
						<td>".str_replace(chr(0),'',$row->MEMO1)."</td>
						<td>".str_replace(chr(0),'',$row->CREDTM)."</td>
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
							<th>รหัสเจ้าหนี้</th>
							<th>ชื่อเจ้าหนี้</th>
							<th>ที่อยู่ 1</th>
							<th>ที่อยู่ 2</th>
							<th>รหัสบัญชี</th>
							<th>หมายเหตุ</th>
							<th>เครดิต (วัน)</th>
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
	
	function DebtorGetFormAE(){
		$apcode = $_POST["APCODE"];
		$data = array(
			"APCODE"=>""
			,"APNAME"=>""
			,"APADDR1"=>""
			,"APADDR2"=>""
			,"ACC_CODE"=>""
			,"MEMO1"=>""
			,"CREDTM"=>""
		);
		
		if($apcode != ''){
			$sql = "
				select * from {$this->MAuth->getdb('APMAST')}
				where APCODE='".$apcode."'
			";
			$query = $this->db->query($sql);
			
			if($query->row()){
				foreach($query->result() as $row){
					$data['APCODE'] = str_replace(chr(0),'',$row->APCODE);
					$data['APNAME'] = str_replace(chr(0),'',$row->APNAME);
					$data['APADDR1'] = str_replace(chr(0),'',$row->APADDR1);
					$data['APADDR2'] = str_replace(chr(0),'',$row->APADDR2);
					$data['ACC_CODE'] = str_replace(chr(0),'',$row->ACC_CODE);
					$data['MEMO1'] = str_replace(chr(0),'',$row->MEMO1);
					$data['CREDTM'] = str_replace(chr(0),'',$row->CREDTM);
				}
			}
		}
		
		$response = array();
		$response['html'] = "
			<div class='col-sm-12'>
				<div style='height:calc(100vh - 165px);overflow:auto;'>
					<div class='col-sm-4 col-sm-offset-4'>	
						<div class='form-group'>
							รหัสเจ้าหนี้
							<input type='text' id='t2APCODE' class='form-control input-sm' value='".$data['APCODE']."' maxlength=8>
						</div>
					</div>
					
					<div class='col-sm-4 col-sm-offset-4'>	
						<div class='form-group'>
							ชื่อเจ้าหนี้
							<input type='text' id='t2APNAME' class='form-control input-sm' value='".$data['APNAME']."' maxlength=60>
						</div>
					</div>
					
					<div class='col-sm-4 col-sm-offset-4'>	
						<div class='form-group'>
							ที่อยู่ 1
							<input type='text' id='t2APADDR1' class='form-control input-sm' value='".$data['APADDR1']."' maxlength=60>
						</div>
					</div>
					
					<div class='col-sm-4 col-sm-offset-4'>	
						<div class='form-group'>
							ที่อยู่ 2
							<input type='text' id='t2APADDR2' class='form-control input-sm' value='".$data['APADDR2']."' maxlength=60>
						</div>
					</div>
					
					
					<div class='col-sm-4 col-sm-offset-4'>	
						<div class='form-group'>
							รหัสบัญชี
							<input type='text' id='t2ACC_CODE' class='form-control input-sm' value='".$data['ACC_CODE']."' maxlength=8>
						</div>
					</div>
					
					
					<div class='col-sm-4 col-sm-offset-4'>	
						<div class='form-group'>
							เครดิต(วัน)
							<input type='text' id='t2CREDTM' class='form-control input-sm' value='".$data['CREDTM']."' maxlength=6>
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
					<input type='button' id='tab2save' class='btn btn-primary btn-sm' style='width:100%;' action='' value='บันทึก'>
				</div>
			</div>	
		";
		
		echo json_encode($response);
	}
	
	function DebtorSave(){
		$arrs = array();
		$arrs['APCODE'] 	= $_POST['APCODE'];
		$arrs['APNAME'] 	= $_POST['APNAME'];
		$arrs['APADDR1'] 	= $_POST['APADDR1'];
		$arrs['APADDR2'] 	= $_POST['APADDR2'];
		$arrs['ACC_CODE'] 	= $_POST['ACC_CODE'];
		$arrs['MEMO1'] 		= $_POST['MEMO1'];
		$arrs['CREDTM']		= $_POST['CREDTM'];
		$arrs['action']		= $_POST['action'];
		
		$data = "";
		if($arrs['action'] == "add"){
			$data = "
				declare @isval int = isnull((select count(*) from {$this->MAuth->getdb('APMAST')} where APCODE='".$arrs['APCODE']."'),0);
				if(@isval = 0)
				begin 
					insert into {$this->MAuth->getdb('APMAST')} (APCODE,APNAME,APADDR1,APADDR2,ACC_CODE,MEMO1,CREDTM)
					select '".$arrs['APCODE']."','".$arrs['APNAME']."','".$arrs['APADDR1']."','".$arrs['APADDR2']."'
						,'".$arrs['ACC_CODE']."','".$arrs['MEMO1']."',".($arrs['CREDTM'] == ""?"NULL":$arrs['CREDTM'])."
					
					insert into {$this->MAuth->getdb('hp_UserOperationLog')}(userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
					values ('".$this->sess["IDNo"]."','เจ้าหนี้ เพิ่ม','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
				end 
				else
				begin 
					rollback tran tsc;
					insert into #tempolary select 'N' as id,'ไม่บันทึก : มีข้อมูลรหัสเจ้าหนี้ ".$arrs['APCODE']." อยู่แล้ว' as msg;
					return;
				end
			";
		}else{			
			$data = "
				update {$this->MAuth->getdb('APMAST')}
				set APNAME='".$arrs['APNAME']."'
					,APADDR1='".$arrs['APADDR1']."'
					,APADDR2='".$arrs['APADDR2']."'
					,ACC_CODE='".$arrs['ACC_CODE']."'
					,MEMO1='".$arrs['MEMO1']."'
					,CREDTM=".($arrs['CREDTM'] == ""?"NULL":$arrs['CREDTM'])."
				where APCODE='".$arrs['APCODE']."'
				
				insert into {$this->MAuth->getdb('hp_UserOperationLog')}(userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
				values ('".$this->sess["IDNo"]."','เจ้าหนี้ แก้ไข','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
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
	
	public function DebtorDel(){
		$arrs = array();
		$arrs['APCODE'] = (!isset($_REQUEST['APCODE'])?'':$_REQUEST['APCODE']);
		$arrs['APNAME'] = (!isset($_REQUEST['APNAME'])?'':$_REQUEST['APNAME']);
		
		$sql = "
			if object_id('tempdb..#tempolary') is not null drop table #tempolary;
			create table #tempolary (id varchar(1),msg varchar(max));
			
			begin tran tsc
			begin try			
				delete {$this->MAuth->getdb('APMAST')}
				where APCODE='".$arrs['APCODE']."'
				
				insert into {$this->MAuth->getdb('hp_UserOperationLog')}(userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
				values ('".$this->sess["IDNo"]."','บริษัทเจ้าหนี้ ลบ','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
				
				insert into #tempolary select 'Y' as id,'สำเร็จ ลบบริษัทเจ้าหนี้ ".$arrs['APCODE']." :: ".$arrs['APNAME']."  แล้ว' as msg;
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
	
	public function Insurance(){ 
		$claim = $this->MLogin->getclaim(uri_string());
		if($claim['m_access'] != "T"){ echo "<div align='center' style='color:red;font-size:16pt;width:100%;'>ขออภัย คุณยังไม่มีสิทธิเข้าใช้งานหน้านี้ครับ</div>"; exit; }
		
		$html = "
			<div class='tab1' name='home' locat='{$this->sess['branch']}' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}' >
				<div class='col-sm-2'>	
					<div class='form-group'>
						รหัสบริษัทประกันภัย
						<input type='text' id='garcode' class='form-control input-sm' placeholder='รหัสบริษัทประกันภัย'>
					</div>
				</div>
				<div class='col-sm-6'>	
					<div class='form-group'>
						ชื่อบริษัทประกันภัย
						<input type='text' id='garname' class='form-control input-sm' placeholder='ชื่อบริษัทประกันภัย'>	
					</div>
				</div>					
				<div class='col-sm-2'>	
					<div class='form-group'>
						<br>
						<input type='button' id='search_gar' class='btn btn-primary btn-sm btn-block' value='แสดง'>
					</div>
				</div>
				<div class='col-sm-2'>	
					<div class='form-group'>
						<br>
						<input type='button' id='add_gar' class='btn btn-cyan btn-sm btn-block' value='เพิ่ม' >
					</div>
				</div>
			</div>
			
			<div id='setGarResult' class='col-sm-12 tab1' style='height:calc(100vh - 197px);overflow:auto;background-color:#;'></div>
			
			<div id='tab2_main' class='col-sm-12 tab2' hidden style='height:calc(100vh - 130px);overflow:auto;background-color:#;'></div>
		";
	
		$html.= "<script src='".base_url('public/js/setup/Company/setGARMAST.js')."'></script>";
		echo $html;
	}
	
	public function GarSearch(){
		$arrs = array();
		$arrs['garcode'] = !isset($_REQUEST['garcode']) ? '' : $_REQUEST['garcode'];
		$arrs['garname'] = !isset($_REQUEST['garname']) ? '' : $_REQUEST['garname'];
		
		$cond = "";
		if($arrs['garcode'] != ''){
			$cond .= " and GARCODE like '%".$arrs['garcode']."%'";
		}
		
		if($arrs['garname'] != ''){
			$cond .= " and GARNAME like '%".$arrs['garname']."%'";
		}
		
		$sql = "
			select * from {$this->MAuth->getdb('GARMAST')}
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
						<td class='getit' seq='".$NRow++."' garcode='".str_replace(chr(0),'',$row->GARCODE)."' style='width:50px;cursor:pointer;text-align:center;'><b>เลือก</b></td>
						<td>".str_replace(chr(0),'',$row->GARCODE)."</td>
						<td>".str_replace(chr(0),'',$row->GARNAME)."</td>
						<td>".str_replace(chr(0),'',$row->GARADDR1)."</td>
						<td>".str_replace(chr(0),'',$row->GARADDR2)."</td>
						<td>".str_replace(chr(0),'',$row->GARTELP)."</td>
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
							<th>รหัสบริษัท</th>
							<th>ชื่อบริษัทประกันภัย</th>
							<th>ที่อยู่ 1</th>
							<th>ที่อยู่ 2</th>
							<th>โทรศัพท์</th>
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
	
	function GarGetFormAE(){
		$garcode = $_POST["GARCODE"];
		$data = array(
			"GARCODE"=>""
			,"GARNAME"=>""
			,"GARADDR1"=>""
			,"GARADDR2"=>""
			,"GARTELP"=>""
			,"MEMO1"=>""
		);
		
		if($garcode != ''){
			$sql = "
				select * from {$this->MAuth->getdb('GARMAST')}
				where GARCODE='".$garcode."'
			";
			$query = $this->db->query($sql);
			
			if($query->row()){
				foreach($query->result() as $row){
					$data['GARCODE'] = str_replace(chr(0),'',$row->GARCODE);
					$data['GARNAME'] = str_replace(chr(0),'',$row->GARNAME);
					$data['GARADDR1'] = str_replace(chr(0),'',$row->GARADDR1);
					$data['GARADDR2'] = str_replace(chr(0),'',$row->GARADDR2);
					$data['GARTELP'] = str_replace(chr(0),'',$row->GARTELP);
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
							รหัสบริษัทประกันภัย
							<input type='text' id='t2GARCODE' class='form-control input-sm' value='".$data['GARCODE']."' maxlength=8>
						</div>
					</div>
					
					<div class='col-sm-4 col-sm-offset-4'>	
						<div class='form-group'>
							ชื่อบริษัทประกันภัย
							<input type='text' id='t2GARNAME' class='form-control input-sm' value='".$data['GARNAME']."' maxlength=60>
						</div>
					</div>
					
					<div class='col-sm-4 col-sm-offset-4'>	
						<div class='form-group'>
							ที่อยู่ 1
							<input type='text' id='t2GARADDR1' class='form-control input-sm' value='".$data['GARADDR1']."' maxlength=60>
						</div>
					</div>
					
					<div class='col-sm-4 col-sm-offset-4'>	
						<div class='form-group'>
							ที่อยู่ 2
							<input type='text' id='t2GARADDR2' class='form-control input-sm' value='".$data['GARADDR2']."' maxlength=60>
						</div>
					</div>
					
					
					<div class='col-sm-4 col-sm-offset-4'>	
						<div class='form-group'>
							โทรศัพท์
							<input type='text' id='t2GARTELP' class='form-control input-sm' value='".$data['GARTELP']."' maxlength=13>
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
					<input type='button' id='tab2save' class='btn btn-primary btn-sm' style='width:100%;' action='' value='บันทึก'>
				</div>
			</div>	
		";
		
		echo json_encode($response);
	}
	
	function GarSave(){
		$arrs = array();
		$arrs['GARCODE'] 	= $_POST['GARCODE'];
		$arrs['GARNAME'] 	= $_POST['GARNAME'];
		$arrs['GARADDR1'] 	= $_POST['GARADDR1'];
		$arrs['GARADDR2'] 	= $_POST['GARADDR2'];
		$arrs['GARTELP']	= $_POST['GARTELP'];
		$arrs['MEMO1'] 		= $_POST['MEMO1'];
		$arrs['action']		= $_POST['action'];
		
		$data = "";
		if($arrs['action'] == "add"){
			$data = "
				declare @isval int = isnull((select count(*) from {$this->MAuth->getdb('GARMAST')} where GARCODE='".$arrs['GARCODE']."'),0);
				if(@isval = 0)
				begin 
					insert into {$this->MAuth->getdb('GARMAST')} (GARCODE,GARNAME,GARADDR1,GARADDR2,GARTELP,MEMO1)
					select '".$arrs['GARCODE']."','".$arrs['GARNAME']."','".$arrs['GARADDR1']."'
						,'".$arrs['GARADDR2']."','".$arrs['GARTELP']."','".$arrs['MEMO1']."'
					
					insert into {$this->MAuth->getdb('hp_UserOperationLog')}(userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
					values ('".$this->sess["IDNo"]."','บริษัทประกันภัย เพิ่ม','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
				end 
				else
				begin 
					rollback tran tsc;
					insert into #tempolary select 'N' as id,'ไม่บันทึก : มีข้อมูลรหัสเจ้าหนี้ ".$arrs['GARCODE']." อยู่แล้ว' as msg;
					return;
				end
			";
		}else{			
			$data = "
				update {$this->MAuth->getdb('GARMAST')}
				set GARNAME='".$arrs['GARNAME']."'
					,GARADDR1='".$arrs['GARADDR1']."'
					,GARADDR2='".$arrs['GARADDR2']."'
					,GARTELP='".$arrs['GARTELP']."'
					,MEMO1='".$arrs['MEMO1']."'
				where GARCODE='".$arrs['GARCODE']."'
				
				insert into {$this->MAuth->getdb('hp_UserOperationLog')}(userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
				values ('".$this->sess["IDNo"]."','บริษัทประกันภัย แก้ไข','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
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
	
	public function GarDel(){
		$arrs = array();
		$arrs['GARCODE'] = (!isset($_REQUEST['GARCODE'])?'':$_REQUEST['GARCODE']);
		$arrs['GARNAME'] = (!isset($_REQUEST['GARNAME'])?'':$_REQUEST['GARNAME']);
		
		$sql = "
			if object_id('tempdb..#tempolary') is not null drop table #tempolary;
			create table #tempolary (id varchar(1),msg varchar(max));
			
			begin tran tsc
			begin try			
				delete {$this->MAuth->getdb('GARMAST')}
				where GARCODE='".$arrs['GARCODE']."'
				
				insert into {$this->MAuth->getdb('hp_UserOperationLog')}(userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
				values ('".$this->sess["IDNo"]."','บริษัทประกันภัย ลบ','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
				
				insert into #tempolary select 'Y' as id,'สำเร็จ ลบบริษัทประกันภัย ".$arrs['GARCODE']." :: ".$arrs['GARNAME']."  แล้ว' as msg;
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
	
	public function Finance(){ 
		$claim = $this->MLogin->getclaim(uri_string());
		if($claim['m_access'] != "T"){ echo "<div align='center' style='color:red;font-size:16pt;width:100%;'>ขออภัย คุณยังไม่มีสิทธิเข้าใช้งานหน้านี้ครับ</div>"; exit; }
		
		$html = "
			<div class='tab1' name='home' locat='{$this->sess['branch']}' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}' >
				<div class='col-sm-2'>	
					<div class='form-group'>
						รหัสไฟแนนซ์
						<input type='text' id='fincode' class='form-control input-sm' placeholder='รหัสไฟแนนซ์'>
					</div>
				</div>
				<div class='col-sm-6'>	
					<div class='form-group'>
						ชื่อไฟแนนซ์
						<input type='text' id='finname' class='form-control input-sm' placeholder='ชื่อไฟแนนซ์'>	
					</div>
				</div>					
				<div class='col-sm-2'>	
					<div class='form-group'>
						<br>
						<input type='button' id='search_fin' class='btn btn-primary btn-sm btn-block' value='แสดง'>
					</div>
				</div>
				<div class='col-sm-2'>	
					<div class='form-group'>
						<br>
						<input type='button' id='add_fin' class='btn btn-cyan btn-sm btn-block' value='เพิ่ม' >
					</div>
				</div>
			</div>
			
			<div id='setFNCorpResult' class='col-sm-12 tab1' style='height:calc(100vh - 197px);overflow:auto;background-color:#;'></div>
			
			<div id='tab2_main' class='col-sm-12 tab2' hidden style='height:calc(100vh - 130px);overflow:auto;background-color:#;'></div>
		";
	
		$html.= "<script src='".base_url('public/js/setup/Company/setFNCorp.js')."'></script>";
		echo $html;
	}
	
	public function FNCorpSearch(){
		$arrs = array();
		$arrs['fincode'] = !isset($_REQUEST['fincode']) ? '' : $_REQUEST['fincode'];
		$arrs['finname'] = !isset($_REQUEST['finname']) ? '' : $_REQUEST['finname'];
		
		$cond = "";
		if($arrs['fincode'] != ''){
			$cond .= " and FINCODE like '%".$arrs['fincode']."%'";
		}
		
		if($arrs['finname'] != ''){
			$cond .= " and FINNAME like '%".$arrs['finname']."%'";
		}
		
		$sql = "
			select * from {$this->MAuth->getdb('FINMAST')}
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
						<td class='getit' seq='".$NRow++."' fincode='".str_replace(chr(0),'',$row->FINCODE)."' style='width:50px;cursor:pointer;text-align:center;'><b>เลือก</b></td>
						<td>".str_replace(chr(0),'',$row->FINCODE)."</td>
						<td>".str_replace(chr(0),'',$row->FINNAME)."</td>
						<td>".str_replace(chr(0),'',$row->FINADDR1)."</td>
						<td>".str_replace(chr(0),'',$row->FINADDR2)."</td>
						<td>".str_replace(chr(0),'',$row->FINTELP)."</td>
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
							<th>รหัสบริษัท</th>
							<th>ชื่อบริษัทไฟแนนซ์</th>
							<th>ที่อยู่ 1</th>
							<th>ที่อยู่ 2</th>
							<th>โทรศัพท์</th>
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
	
	function FNCorpGetFormAE(){
		$fincode = $_POST["FINCODE"];
		$data = array(
			"FINCODE"=>""
			,"FINNAME"=>""
			,"FINADDR1"=>""
			,"FINADDR2"=>""
			,"FINTELP"=>""
			,"MEMO1"=>""
		);
		
		if($fincode != ''){
			$sql = "
				select * from {$this->MAuth->getdb('FINMAST')}
				where FINCODE='".$fincode."'
			";
			$query = $this->db->query($sql);
			
			if($query->row()){
				foreach($query->result() as $row){
					$data['FINCODE'] = str_replace(chr(0),'',$row->FINCODE);
					$data['FINNAME'] = str_replace(chr(0),'',$row->FINNAME);
					$data['FINADDR1'] = str_replace(chr(0),'',$row->FINADDR1);
					$data['FINADDR2'] = str_replace(chr(0),'',$row->FINADDR2);
					$data['FINTELP'] = str_replace(chr(0),'',$row->FINTELP);
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
							รหัสไฟแนนซ์
							<input type='text' id='t2FINCODE' class='form-control input-sm' value='".$data['FINCODE']."' maxlength=8>
						</div>
					</div>
					
					<div class='col-sm-4 col-sm-offset-4'>	
						<div class='form-group'>
							ชื่อบริษัทไฟแนนซ์
							<input type='text' id='t2FINNAME' class='form-control input-sm' value='".$data['FINNAME']."' maxlength=60>
						</div>
					</div>
					
					<div class='col-sm-4 col-sm-offset-4'>	
						<div class='form-group'>
							ที่อยู่ 1
							<input type='text' id='t2FINADDR1' class='form-control input-sm' value='".$data['FINADDR1']."' maxlength=60>
						</div>
					</div>
					
					<div class='col-sm-4 col-sm-offset-4'>	
						<div class='form-group'>
							ที่อยู่ 2
							<input type='text' id='t2FINADDR2' class='form-control input-sm' value='".$data['FINADDR2']."' maxlength=60>
						</div>
					</div>
					
					
					<div class='col-sm-4 col-sm-offset-4'>	
						<div class='form-group'>
							โทรศัพท์
							<input type='text' id='t2FINTELP' class='form-control input-sm' value='".$data['FINTELP']."' maxlength=13>
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
					<input type='button' id='tab2save' class='btn btn-primary btn-sm' style='width:100%;' action='' value='บันทึก'>
				</div>
			</div>	
		";
		
		echo json_encode($response);
	}
	
	function FNCorpSave(){
		$arrs = array();
		$arrs['FINCODE'] 	= $_POST['FINCODE'];
		$arrs['FINNAME'] 	= $_POST['FINNAME'];
		$arrs['FINADDR1'] 	= $_POST['FINADDR1'];
		$arrs['FINADDR2'] 	= $_POST['FINADDR2'];
		$arrs['FINTELP']	= $_POST['FINTELP'];
		$arrs['MEMO1'] 		= $_POST['MEMO1'];
		$arrs['action']		= $_POST['action'];
		
		$data = "";
		if($arrs['action'] == "add"){
			$data = "
				declare @isval int = isnull((select count(*) from {$this->MAuth->getdb('FINMAST')} where FINCODE='".$arrs['FINCODE']."'),0);
				if(@isval = 0)
				begin 
					insert into {$this->MAuth->getdb('FINMAST')} (FINCODE,FINNAME,FINADDR1,FINADDR2,FINTELP,MEMO1)
					select '".$arrs['FINCODE']."','".$arrs['FINNAME']."','".$arrs['FINADDR1']."'
						,'".$arrs['FINADDR2']."','".$arrs['FINTELP']."','".$arrs['MEMO1']."'
					
					insert into {$this->MAuth->getdb('hp_UserOperationLog')}(userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
					values ('".$this->sess["IDNo"]."','บริษัทไฟแนนซ์ เพิ่ม','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
				end 
				else
				begin 
					rollback tran tsc;
					insert into #tempolary select 'N' as id,'ไม่บันทึก : มีข้อมูลรหัสไฟแนนซ์ ".$arrs['FINCODE']." อยู่แล้ว' as msg;
					return;
				end
			";
		}else{			
			$data = "
				update {$this->MAuth->getdb('FINMAST')}
				set FINNAME='".$arrs['FINNAME']."'
					,FINADDR1='".$arrs['FINADDR1']."'
					,FINADDR2='".$arrs['FINADDR2']."'
					,FINTELP='".$arrs['FINTELP']."'
					,MEMO1='".$arrs['MEMO1']."'
				where FINCODE='".$arrs['FINCODE']."'
				
				insert into {$this->MAuth->getdb('hp_UserOperationLog')}(userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
				values ('".$this->sess["IDNo"]."','บริษัทประกันภัย แก้ไข','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
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
	
	public function FNCorpDel(){
		$arrs = array();
		$arrs['FINCODE'] = (!isset($_REQUEST['FINCODE'])?'':$_REQUEST['FINCODE']);
		$arrs['FINNAME'] = (!isset($_REQUEST['FINNAME'])?'':$_REQUEST['FINNAME']);
		
		$sql = "
			if object_id('tempdb..#tempolary') is not null drop table #tempolary;
			create table #tempolary (id varchar(1),msg varchar(max));
			
			begin tran tsc
			begin try			
				delete {$this->MAuth->getdb('FINMAST')}
				where FINCODE='".$arrs['FINCODE']."'
				
				insert into {$this->MAuth->getdb('hp_UserOperationLog')}(userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
				values ('".$this->sess["IDNo"]."','บริษัทไฟแนนซ์ ลบ','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
				
				insert into #tempolary select 'Y' as id,'สำเร็จ ลบบริษัทไฟแนนซ์ ".$arrs['FINCODE']." :: ".$arrs['FINNAME']."  แล้ว' as msg;
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
	
	public function Bank(){ 
		$claim = $this->MLogin->getclaim(uri_string());
		if($claim['m_access'] != "T"){ echo "<div align='center' style='color:red;font-size:16pt;width:100%;'>ขออภัย คุณยังไม่มีสิทธิเข้าใช้งานหน้านี้ครับ</div>"; exit; }
		
		$html = "
			<div class='tab1' name='home' locat='{$this->sess['branch']}' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}' >
				<div class='col-sm-2'>	
					<div class='form-group'>
						รหัสธนาคาร
						<input type='text' id='bkcode' class='form-control input-sm' placeholder='รหัสธนาคาร'>
					</div>
				</div>
				<div class='col-sm-6'>	
					<div class='form-group'>
						ชื่อธนาคาร
						<input type='text' id='bkname' class='form-control input-sm' placeholder='ชื่อธนาคาร'>	
					</div>
				</div>					
				<div class='col-sm-2'>	
					<div class='form-group'>
						<br>
						<input type='button' id='search_bk' class='btn btn-primary btn-sm btn-block' value='แสดง'>
					</div>
				</div>
				<div class='col-sm-2'>	
					<div class='form-group'>
						<br>
						<input type='button' id='add_bk' class='btn btn-cyan btn-sm btn-block' value='เพิ่ม' >
					</div>
				</div>
			</div>
			
			<div id='setFNCorpResult' class='col-sm-12 tab1' style='height:calc(100vh - 197px);overflow:auto;background-color:#;'></div>
			
			<div id='tab2_main' class='col-sm-12 tab2' hidden style='height:calc(100vh - 130px);overflow:auto;background-color:#;'></div>
		";
	
		$html.= "<script src='".base_url('public/js/setup/Company/setBank.js')."'></script>";
		echo $html;
	}
	
	public function BankSearch(){
		$arrs = array();
		$arrs['bkcode'] = !isset($_REQUEST['bkcode']) ? '' : $_REQUEST['bkcode'];
		$arrs['bkname'] = !isset($_REQUEST['bkname']) ? '' : $_REQUEST['bkname'];
		
		$cond = "";
		if($arrs['bkcode'] != ''){
			$cond .= " and BKCODE like '%".$arrs['bkcode']."%'";
		}
		
		if($arrs['bkname'] != ''){
			$cond .= " and BKNAME like '%".$arrs['bkname']."%'";
		}
		
		$sql = "
			select * from {$this->MAuth->getdb('BKMAST')}
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
						<td class='getit' seq='".$NRow++."' bkcode='".str_replace(chr(0),'',$row->BKCODE)."' style='width:50px;cursor:pointer;text-align:center;'><b>เลือก</b></td>
						<td>".str_replace(chr(0),'',$row->BKCODE)."</td>
						<td>".str_replace(chr(0),'',$row->BKNAME)."</td>
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
							<th>รหัสธนาคาร</th>
							<th>ชื่อธนาคาร</th>
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
	
	function BankGetFormAE(){
		$bkcode = $_POST["BKCODE"];
		$data = array(
			"BKCODE"=>""
			,"BKNAME"=>""
			,"MEMO1"=>""
		);
		
		if($bkcode != ''){
			$sql = "
				select * from {$this->MAuth->getdb('BKMAST')}
				where BKCODE='".$bkcode."'
			";
			$query = $this->db->query($sql);
			
			if($query->row()){
				foreach($query->result() as $row){
					$data['BKCODE'] = str_replace(chr(0),'',$row->BKCODE);
					$data['BKNAME'] = str_replace(chr(0),'',$row->BKNAME);
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
							รหัสธนาคาร
							<input type='text' id='t2BKCODE' class='form-control input-sm' value='".$data['BKCODE']."' maxlength=13>
						</div>
					</div>
					
					<div class='col-sm-4 col-sm-offset-4'>	
						<div class='form-group'>
							ชื่อธนาคาร
							<input type='text' id='t2BKNAME' class='form-control input-sm' value='".$data['BKNAME']."' maxlength=60>
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
					<input type='button' id='tab2save' class='btn btn-primary btn-sm' style='width:100%;' action='' value='บันทึก'>
				</div>
			</div>	
		";
		
		echo json_encode($response);
	}
	
	function BankSave(){
		$arrs = array();
		$arrs['BKCODE'] 	= $_POST['BKCODE'];
		$arrs['BKNAME'] 	= $_POST['BKNAME'];
		$arrs['MEMO1'] 		= $_POST['MEMO1'];
		$arrs['action']		= $_POST['action'];
		
		$data = "";
		if($arrs['action'] == "add"){
			$data = "
				declare @isval int = isnull((select count(*) from {$this->MAuth->getdb('BKMAST')} where BKCODE='".$arrs['BKCODE']."'),0);
				if(@isval = 0)
				begin 
					insert into {$this->MAuth->getdb('BKMAST')} (BKCODE,BKNAME,MEMO1)
					select '".$arrs['BKCODE']."','".$arrs['BKNAME']."','".$arrs['MEMO1']."'
					
					insert into {$this->MAuth->getdb('hp_UserOperationLog')}(userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
					values ('".$this->sess["IDNo"]."','รหัสธนาคาร เพิ่ม','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
				end 
				else
				begin 
					rollback tran tsc;
					insert into #tempolary select 'N' as id,'ไม่บันทึก : มีข้อมูลรหัสธนาคาร ".$arrs['BKCODE']." อยู่แล้ว' as msg;
					return;
				end
			";
		}else{			
			$data = "
				update {$this->MAuth->getdb('BKMAST')}
				set BKNAME='".$arrs['BKNAME']."'
					,MEMO1='".$arrs['MEMO1']."'
				where BKCODE='".$arrs['BKCODE']."'
				
				insert into {$this->MAuth->getdb('hp_UserOperationLog')}(userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
				values ('".$this->sess["IDNo"]."','รหัสธนาคาร แก้ไข','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
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
	
	public function BankDel(){
		$arrs = array();
		$arrs['BKCODE'] = (!isset($_REQUEST['BKCODE'])?'':$_REQUEST['BKCODE']);
		$arrs['BKNAME'] = (!isset($_REQUEST['BKNAME'])?'':$_REQUEST['BKNAME']);
		
		$sql = "
			if object_id('tempdb..#tempolary') is not null drop table #tempolary;
			create table #tempolary (id varchar(1),msg varchar(max));
			
			begin tran tsc
			begin try			
				delete {$this->MAuth->getdb('BKMAST')}
				where BKCODE='".$arrs['BKCODE']."'
				
				insert into {$this->MAuth->getdb('hp_UserOperationLog')}(userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
				values ('".$this->sess["IDNo"]."','รหัสธนาคาร ลบ','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
				
				insert into #tempolary select 'Y' as id,'สำเร็จ ลบรหัสธนาคาร ".$arrs['BKCODE']." :: ".$arrs['BKNAME']."  แล้ว' as msg;
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




















