<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/********************************************************
             ______@07/09/2020______
            / / _ _   _ _     __ 
           / // __ \ / __ \ / __ \
       _ _/ // /_/ // / / // /_/ /
     /_ _ _/ \_ _ //_/ /_/ \__  /
                          _ _/ /
                         /___ /
********************************************************/
class Finance extends MY_Controller {
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
	
	public function PAYTYP(){
		$claim = $this->MLogin->getclaim(uri_string());
		if($claim['m_access'] != "T"){ echo "<div align='center' style='color:red;font-size:16pt;width:100%;'>ขออภัย คุณยังไม่มีสิทธิเข้าใช้งานหน้านี้ครับ</div>"; exit; }
		
		$html = "
			<div class='tab1' name='home' locat='{$this->sess['branch']}' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}' >
				<div class='col-sm-2'>	
					<div class='form-group'>
						รหัสประเภท
						<input type='text' id='paycode' class='form-control input-sm' placeholder='รหัสประเภท'>
					</div>
				</div>
				<div class='col-sm-6'>	
					<div class='form-group'>
						ชื่อประเภท
						<input type='text' id='payname' class='form-control input-sm' placeholder='ชื่อประเภท'>	
					</div>
				</div>					
				<div class='col-sm-2'>	
					<div class='form-group'>
						<br>
						<input type='button' id='search_paytyp' class='btn btn-primary btn-sm btn-block' value='แสดง'>
					</div>
				</div>
				<div class='col-sm-2'>	
					<div class='form-group'>
						<br>
						<input type='button' id='add_paytyp' class='btn btn-cyan btn-sm btn-block' value='เพิ่ม' >
					</div>
				</div>
			</div>
			
			<div id='setPAYTYPResult' class='col-sm-12 tab1' style='height:calc(100vh - 197px);overflow:auto;background-color:#;'></div>
			
			<div id='tab2_main' class='col-sm-12 tab2' hidden style='height:calc(100vh - 130px);overflow:auto;background-color:#;'></div>
		";
	
		$html.= "<script src='".base_url('public/js/setup/Finance/setPAYTYP.js')."'></script>";
		echo $html;
	}
	
	public function PAYTYPSearch(){
		$arrs = array();
		$arrs['PAYCODE'] = !isset($_REQUEST['PAYCODE']) ? '' : $_REQUEST['PAYCODE'];
		$arrs['PAYDESC'] = !isset($_REQUEST['PAYDESC']) ? '' : $_REQUEST['PAYDESC'];
		
		$cond = "";
		if($arrs['PAYCODE'] != ''){
			$cond .= " and PAYCODE like '%".$arrs['PAYCODE']."%'";
		}
		
		if($arrs['PAYDESC'] != ''){
			$cond .= " and PAYDESC like '%".$arrs['PAYDESC']."%'";
		}
		
		$sql = "
			select * from {$this->MAuth->getdb('PAYTYP')}
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
						<td class='getit' seq='".$NRow++."' paycode='".str_replace(chr(0),'',$row->PAYCODE)."' style='width:50px;cursor:pointer;text-align:center;'><b>เลือก</b></td>
						<td>".str_replace(chr(0),'',$row->PAYCODE)."</td>
						<td>".str_replace(chr(0),'',$row->PAYDESC)."</td>
						<td>".str_replace(chr(0),'',$row->ACCODE1)."</td>
						<td>".str_replace(chr(0),'',$row->ACCODE2)."</td>
						<td>".str_replace(chr(0),'',$row->RLBILL)."</td>
						<!--td>".str_replace(chr(0),'',$row->SNCSET)."</td-->
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
							<th>รหัสประเภท</th>
							<th>ชื่อประเภท</th>
							<th>รหัสบัญชีรับ</th>
							<th>รหัสบัญชีจ่าย</th>
							<th>ออกใบเสร็จทันที</th>
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
	
	function PAYTYPGetFormAE(){
		$PAYCODE = $_POST["PAYCODE"];
		$data = array(
			"PAYCODE"=>""
			,"PAYDESC"=>""
			,"RLBILL"=>""
			,"MEMO1"=>""
			,"ACCODE1"=>""
			,"ACCODE2"=>""
		);
		
		if($PAYCODE != ''){
			$sql = "
				select * from {$this->MAuth->getdb('PAYTYP')}
				where PAYCODE='".$PAYCODE."'
			";
			$query = $this->db->query($sql);
			
			if($query->row()){
				foreach($query->result() as $row){
					$data['PAYCODE'] = str_replace(chr(0),'',$row->PAYCODE);
					$data['PAYDESC'] = str_replace(chr(0),'',$row->PAYDESC);
					$data['ACCODE1'] = str_replace(chr(0),'',$row->ACCODE1);
					$data['ACCODE2'] = str_replace(chr(0),'',$row->ACCODE2);
					$data['RLBILL'] = str_replace(chr(0),'',$row->RLBILL);
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
							รหัสประเภท
							<input type='text' id='t2PAYCODE' class='form-control input-sm' value='".$data['PAYCODE']."' maxlength=2>
						</div>
					</div>
					
					<div class='col-sm-4 col-sm-offset-4'>	
						<div class='form-group'>
							ชื่อประเภท
							<input type='text' id='t2PAYDESC' class='form-control input-sm' value='".$data['PAYDESC']."' maxlength=60>
						</div>
					</div>
					
					<div class='col-sm-4 col-sm-offset-4'>	
						<div class='form-group'>
							รหัสบัญชีรับ
							<input type='text' id='t2ACCODE1' class='form-control input-sm' value='".$data['ACCODE1']."' maxlength=12>
						</div>
					</div>
					
					<div class='col-sm-4 col-sm-offset-4'>	
						<div class='form-group'>
							รหัสบัญชีจ่าย
							<input type='text' id='t2ACCODE2' class='form-control input-sm' value='".$data['ACCODE2']."' maxlength=12>
						</div>
					</div>
					
					<div class='col-sm-4 col-sm-offset-4'>	
						<div class='form-group'>
							<label class='checkbox lobicheck lobicheck-primary lobicheck-inversed'>
								<input type='checkbox' id='t2RLBILL' ".($data['RLBILL']=="Y"?"checked":"")."> 
								<i></i> ออกใบเสร็จทันที
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
	
	function PAYTYPSave(){
		$arrs = array();
		$arrs['PAYCODE'] 	= $_POST['PAYCODE'];
		$arrs['PAYDESC'] 	= $_POST['PAYDESC'];
		$arrs['ACCODE1'] 	= $_POST['ACCODE1'];
		$arrs['ACCODE2'] 	= $_POST['ACCODE2'];
		$arrs['RLBILL'] 	= $_POST['RLBILL'];
		$arrs['MEMO1'] 		= $_POST['MEMO1'];
		
		$arrs['action']		= $_POST['action'];
		
		$data = "";
		if($arrs['action'] == "add"){
			$data = "
				declare @isval int = isnull((select count(*) from {$this->MAuth->getdb('PAYTYP')} where PAYCODE='".$arrs['PAYCODE']."'),0);
				if(@isval = 0)
				begin 
					insert into {$this->MAuth->getdb('PAYTYP')} (PAYCODE,PAYDESC,ACCODE1,ACCODE2,RLBILL,MEMO1)
					select '".$arrs['PAYCODE']."','".$arrs['PAYDESC']."','".$arrs['ACCODE1']."','".$arrs['ACCODE2']."'
						,'".$arrs['RLBILL']."','".$arrs['MEMO1']."'
					
					insert into {$this->MAuth->getdb('hp_UserOperationLog')}(userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
					values ('".$this->sess["IDNo"]."','setup ประเภทการชำระ เพิ่ม','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
				end 
				else
				begin 
					rollback tran tsc;
					insert into #tempolary select 'N' as id,'ไม่บันทึก : มีข้อมูลรหัส ประเภทการชำระ ".$arrs['PAYCODE']." อยู่แล้ว' as msg;
					return;
				end
			";
		}else{			
			$data = "
				update {$this->MAuth->getdb('PAYTYP')}
				set PAYDESC='".$arrs['PAYDESC']."'
					,ACCODE1='".$arrs['ACCODE1']."'
					,ACCODE2='".$arrs['ACCODE2']."'
					,RLBILL='".$arrs['RLBILL']."'
					,MEMO1='".$arrs['MEMO1']."'
				where PAYCODE='".$arrs['PAYCODE']."'
				
				insert into {$this->MAuth->getdb('hp_UserOperationLog')}(userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
				values ('".$this->sess["IDNo"]."','setup ประเภทการชำระ แก้ไข','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
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
	
	public function PAYTYPDel(){
		$arrs = array();
		$arrs['PAYCODE'] = (!isset($_REQUEST['PAYCODE'])?'':$_REQUEST['PAYCODE']);
		$arrs['PAYDESC'] = (!isset($_REQUEST['PAYDESC'])?'':$_REQUEST['PAYDESC']);
		
		$sql = "
			if object_id('tempdb..#tempolary') is not null drop table #tempolary;
			create table #tempolary (id varchar(1),msg varchar(max));
			
			begin tran tsc
			begin try			
				delete {$this->MAuth->getdb('PAYTYP')}
				where PAYCODE='".$arrs['PAYCODE']."'
				
				insert into {$this->MAuth->getdb('hp_UserOperationLog')}(userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
				values ('".$this->sess["IDNo"]."','บริษัทเจ้าหนี้ ลบ','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
				
				insert into #tempolary select 'Y' as id,'สำเร็จ ลบประเภทการชำระ ".$arrs['PAYCODE']." :: ".$arrs['PAYDESC']."  แล้ว' as msg;
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
	
	public function PAYFOR(){ 
		$claim = $this->MLogin->getclaim(uri_string());
		if($claim['m_access'] != "T"){ echo "<div align='center' style='color:red;font-size:16pt;width:100%;'>ขออภัย คุณยังไม่มีสิทธิเข้าใช้งานหน้านี้ครับ</div>"; exit; }
		
		$html = "
			<div class='tab1' name='home' locat='{$this->sess['branch']}' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}' >
				<div class='col-sm-2'>	
					<div class='form-group'>
						รหัสชำระ
						<input type='text' id='FORCODE' class='form-control input-sm' placeholder='รหัสชำระ'>
					</div>
				</div>
				<div class='col-sm-6'>	
					<div class='form-group'>
						ชำระค่า
						<input type='text' id='FORDESC' class='form-control input-sm' placeholder='ชำระค่า'>	
					</div>
				</div>					
				<div class='col-sm-2'>	
					<div class='form-group'>
						<br>
						<input type='button' id='search_payfor' class='btn btn-primary btn-sm btn-block' value='แสดง'>
					</div>
				</div>
				<div class='col-sm-2'>	
					<div class='form-group'>
						<br>
						<input type='button' id='add_payfor' class='btn btn-cyan btn-sm btn-block' value='เพิ่ม' >
					</div>
				</div>
			</div>
			
			<div id='setPAYFORResult' class='col-sm-12 tab1' style='height:calc(100vh - 197px);overflow:auto;background-color:#;'></div>
			
			<div id='tab2_main' class='col-sm-12 tab2' hidden style='height:calc(100vh - 130px);overflow:auto;background-color:#;'></div>
		";
	
		$html.= "<script src='".base_url('public/js/setup/Finance/setPAYFOR.js')."'></script>";
		echo $html;
	}
	
	public function PAYFORSearch(){
		$arrs = array();
		$arrs['FORCODE'] = !isset($_REQUEST['FORCODE']) ? '' : $_REQUEST['FORCODE'];
		$arrs['FORDESC'] = !isset($_REQUEST['FORDESC']) ? '' : $_REQUEST['FORDESC'];
		
		$cond = "";
		if($arrs['FORCODE'] != ''){
			$cond .= " and FORCODE like '%".$arrs['FORCODE']."%'";
		}
		
		if($arrs['FORDESC'] != ''){
			$cond .= " and FORDESC like '%".$arrs['FORDESC']."%'";
		}
		
		$sql = "
			select * from {$this->MAuth->getdb('PAYFOR')}
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
						<td class='getit' seq='".$NRow++."' FORCODE='".str_replace(chr(0),'',$row->FORCODE)."' style='width:50px;cursor:pointer;text-align:center;'><b>เลือก</b></td>
						<td>".str_replace(chr(0),'',$row->FORCODE)."</td>
						<td>".str_replace(chr(0),'',$row->FORDESC)."</td>
						<td>".str_replace(chr(0),'',$row->ACCODE1)."</td>
						<td>".str_replace(chr(0),'',$row->ACCODE2)."</td>
						<td>".str_replace(chr(0),'',$row->TAXFL)."</td>
						<td>".str_replace(chr(0),'',$row->FORREG)."</td>
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
							<th>รหัสชำระ</th>
							<th>ชำระค่า</th>
							<th>รหัสบัญชีรับ</th>
							<th>รหัสบัญชีจ่าย</th>
							<th>ออกใบกำกับภาษีเมื่อรับเงิน</th>
							<th>รหัสชำระค่าทะเบียน</th>
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
	
	function PAYFORGetFormAE(){
		$FORCODE = $_POST["FORCODE"];
		$data = array(
			"FORCODE"=>""
			,"FORDESC"=>""
			,"ACCODE1"=>""
			,"ACCODE2"=>""
			,"TAXFL"=>""
			,"FORREG"=>""
			,"MEMO1"=>""
		);
		
		if($FORCODE != ''){
			$sql = "
				select * from {$this->MAuth->getdb('PAYFOR')}
				where FORCODE='".$FORCODE."'
			";
			$query = $this->db->query($sql);
			
			if($query->row()){
				foreach($query->result() as $row){
					$data['FORCODE'] = str_replace(chr(0),'',$row->FORCODE);
					$data['FORDESC'] = str_replace(chr(0),'',$row->FORDESC);
					$data['ACCODE1'] = str_replace(chr(0),'',$row->ACCODE1);
					$data['ACCODE2'] = str_replace(chr(0),'',$row->ACCODE2);
					$data['TAXFL'] = str_replace(chr(0),'',$row->TAXFL);
					$data['FORREG'] = str_replace(chr(0),'',$row->FORREG);
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
							รหัสชำระ
							<input type='text' id='t2FORCODE' class='form-control input-sm' value='".$data['FORCODE']."' maxlength=3>
						</div>
					</div>
					
					<div class='col-sm-4 col-sm-offset-4'>	
						<div class='form-group'>
							ชำระค่า
							<input type='text' id='t2FORDESC' class='form-control input-sm' value='".$data['FORDESC']."' maxlength=60>
						</div>
					</div>
					
					<div class='col-sm-4 col-sm-offset-4'>	
						<div class='form-group'>
							รหัสบัญชีรับ
							<input type='text' id='t2ACCODE1' class='form-control input-sm' value='".$data['ACCODE1']."' maxlength=12>
						</div>
					</div>
					
					<div class='col-sm-4 col-sm-offset-4'>	
						<div class='form-group'>
							รหัสบัญชีจ่าย
							<input type='text' id='t2ACCODE2' class='form-control input-sm' value='".$data['ACCODE2']."' maxlength=12>
						</div>
					</div>
					
					
					<div class='col-sm-4 col-sm-offset-4'>	
						<div class='form-group'>
							<label class='checkbox lobicheck lobicheck-primary lobicheck-inversed'>
								<input type='checkbox' id='t2TAXFL' ".($data['TAXFL']=="Y"?"checked":"")."> 
								<i></i> ออกใบกำกับภาษีเมื่อรับเงิน
							</label>
						</div>
					</div>
					
					<div class='col-sm-4 col-sm-offset-4'>	
						<div class='form-group'>
							<label class='checkbox lobicheck lobicheck-primary lobicheck-inversed'>
								<input type='checkbox' id='t2FORREG' ".($data['FORREG']=="Y"?"checked":"")."> 
								<i></i> รหัสชำระค่าทะเบียน
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
	
	function PAYFORSave(){
		exit;
		$arrs = array();
		$arrs['FORCODE'] 	= $_POST['FORCODE'];
		$arrs['FORDESC'] 	= $_POST['FORDESC'];
		$arrs['ACCODE1'] 	= $_POST['ACCODE1'];
		$arrs['ACCODE2'] 	= $_POST['ACCODE2'];
		$arrs['TAXFL']		= $_POST['TAXFL'];
		$arrs['FORREG'] 	= $_POST['FORREG'];
		$arrs['MEMO1'] 	= $_POST['MEMO1'];
		$arrs['action']		= $_POST['action'];
		
		$data = "";
		if($arrs['action'] == "add"){
			$data = "
				declare @isval int = isnull((select count(*) from {$this->MAuth->getdb('PAYFOR')} where FORCODE='".$arrs['FORCODE']."'),0);
				if(@isval = 0)
				begin 
					insert into {$this->MAuth->getdb('PAYFOR')} (FORCODE,FORDESC,ACCODE1,ACCODE2,TAXFL,FORREG,MEMO1)
					select '".$arrs['FORCODE']."','".$arrs['FORDESC']."','".$arrs['ACCODE1']."'
						,'".$arrs['ACCODE2']."','".$arrs['TAXFL']."','".$arrs['FORREG']."','".$arrs['MEMO1']."'
					
					insert into {$this->MAuth->getdb('hp_UserOperationLog')}(userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
					values ('".$this->sess["IDNo"]."','setup ชำระค่า เพิ่ม','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
				end 
				else
				begin 
					rollback tran tsc;
					insert into #tempolary select 'N' as id,'ไม่บันทึก : มีข้อมูลรหัสชำระ ".$arrs['FORCODE']." อยู่แล้ว' as msg;
					return;
				end
			";
		}else{			
			$data = "
				update {$this->MAuth->getdb('PAYFOR')}
				set FORDESC='".$arrs['FORDESC']."'
					,ACCODE1='".$arrs['ACCODE1']."'
					,ACCODE2='".$arrs['ACCODE2']."'
					,TAXFL='".$arrs['TAXFL']."'
					,FORREG='".$arrs['FORREG']."'
					,MEMO1='".$arrs['MEMO1']."'
				where FORCODE='".$arrs['FORCODE']."'
				
				insert into {$this->MAuth->getdb('hp_UserOperationLog')}(userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
				values ('".$this->sess["IDNo"]."','setup ชำระค่า แก้ไข','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
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
	
	public function PAYFORDel(){
		$arrs = array();
		$arrs['FORCODE'] = (!isset($_REQUEST['FORCODE'])?'':$_REQUEST['FORCODE']);
		$arrs['FORDESC'] = (!isset($_REQUEST['FORDESC'])?'':$_REQUEST['FORDESC']);
		
		$sql = "
			if object_id('tempdb..#tempolary') is not null drop table #tempolary;
			create table #tempolary (id varchar(1),msg varchar(max));
			
			begin tran tsc
			begin try			
				delete {$this->MAuth->getdb('PAYFOR')}
				where FORCODE='".$arrs['FORCODE']."'
				
				insert into {$this->MAuth->getdb('hp_UserOperationLog')}(userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
				values ('".$this->sess["IDNo"]."','บริษัทประกันภัย ลบ','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
				
				insert into #tempolary select 'Y' as id,'สำเร็จ ลบบริษัทประกันภัย ".$arrs['FORCODE']." :: ".$arrs['FORDESC']."  แล้ว' as msg;
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
	
	public function PAYDUE(){ 
		$claim = $this->MLogin->getclaim(uri_string());
		if($claim['m_access'] != "T"){ echo "<div align='center' style='color:red;font-size:16pt;width:100%;'>ขออภัย คุณยังไม่มีสิทธิเข้าใช้งานหน้านี้ครับ</div>"; exit; }
		
		$html = "
			<div class='tab1' name='home' locat='{$this->sess['branch']}' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}' >
				<div class='col-sm-2'>	
					<div class='form-group'>
						รหัสชำระ
						<input type='text' id='PAYCODE' class='form-control input-sm' placeholder='รหัสชำระ'>
					</div>
				</div>
				<div class='col-sm-6'>	
					<div class='form-group'>
						ชำระค่า
						<input type='text' id='PAYDESC' class='form-control input-sm' placeholder='ชำระค่า'>	
					</div>
				</div>					
				<div class='col-sm-2'>	
					<div class='form-group'>
						<br>
						<input type='button' id='search_due' class='btn btn-primary btn-sm btn-block' value='แสดง'>
					</div>
				</div>
				<div class='col-sm-2'>	
					<div class='form-group'>
						<br>
						<input type='button' id='add_due' class='btn btn-cyan btn-sm btn-block' value='เพิ่ม' >
					</div>
				</div>
			</div>
			
			<div id='setPAYDUEResult' class='col-sm-12 tab1' style='height:calc(100vh - 197px);overflow:auto;background-color:#;'></div>
			
			<div id='tab2_main' class='col-sm-12 tab2' hidden style='height:calc(100vh - 130px);overflow:auto;background-color:#;'></div>
		";
	
		$html.= "<script src='".base_url('public/js/setup/Finance/setPAYDUE.js')."'></script>";
		echo $html;
	}
	
	public function PAYDUESearch(){
		$arrs = array();
		$arrs['PAYCODE'] = !isset($_REQUEST['PAYCODE']) ? '' : $_REQUEST['PAYCODE'];
		$arrs['PAYDESC'] = !isset($_REQUEST['PAYDESC']) ? '' : $_REQUEST['PAYDESC'];
		
		$cond = "";
		if($arrs['PAYCODE'] != ''){
			$cond .= " and PAYCODE like '%".$arrs['PAYCODE']."%'";
		}
		
		if($arrs['PAYDESC'] != ''){
			$cond .= " and PAYDESC like '%".$arrs['PAYDESC']."%'";
		}
		
		$sql = "
			select * from {$this->MAuth->getdb('PAYDUE')}
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
						<td class='getit' seq='".$NRow++."' PAYCODE='".str_replace(chr(0),'',$row->PAYCODE)."' style='width:50px;cursor:pointer;text-align:center;'><b>เลือก</b></td>
						<td>".str_replace(chr(0),'',$row->PAYCODE)."</td>
						<td>".str_replace(chr(0),'',$row->PAYDESC)."</td>
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
							<th>รหัสชำระ</th>
							<th>ชำระค่า</th>
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
	
	function PAYDUEGetFormAE(){
		$PAYCODE = $_POST["PAYCODE"];
		$data = array(
			"PAYCODE"=>""
			,"PAYDESC"=>""
			,"MEMO1"=>""
		);
		
		if($PAYCODE != ''){
			$sql = "
				select * from {$this->MAuth->getdb('PAYDUE')}
				where PAYCODE='".$PAYCODE."'
			";
			$query = $this->db->query($sql);
			
			if($query->row()){
				foreach($query->result() as $row){
					$data['PAYCODE'] = str_replace(chr(0),'',$row->PAYCODE);
					$data['PAYDESC'] = str_replace(chr(0),'',$row->PAYDESC);
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
							รหัสชำระ
							<input type='text' id='t2PAYCODE' class='form-control input-sm' value='".$data['PAYCODE']."' maxlength=2>
						</div>
					</div>
					
					<div class='col-sm-4 col-sm-offset-4'>	
						<div class='form-group'>
							ชำระค่า
							<input type='text' id='t2PAYDESC' class='form-control input-sm' value='".$data['PAYDESC']."' maxlength=60>
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
	
	function PAYDUESave(){
		$arrs = array();
		$arrs['PAYCODE'] 	= $_POST['PAYCODE'];
		$arrs['PAYDESC'] 	= $_POST['PAYDESC'];
		$arrs['MEMO1'] 		= $_POST['MEMO1'];
		$arrs['action']		= $_POST['action'];
		
		$data = "";
		if($arrs['action'] == "add"){
			$data = "
				declare @isval int = isnull((select count(*) from {$this->MAuth->getdb('PAYDUE')} where PAYCODE='".$arrs['PAYCODE']."'),0);
				if(@isval = 0)
				begin 
					insert into {$this->MAuth->getdb('PAYDUE')} (PAYCODE,PAYDESC,MEMO1)
					select '".$arrs['PAYCODE']."','".$arrs['PAYDESC']."','".$arrs['MEMO1']."'
					
					insert into {$this->MAuth->getdb('hp_UserOperationLog')}(userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
					values ('".$this->sess["IDNo"]."','setup วิธีชำระค่างวด เพิ่ม','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
				end 
				else
				begin 
					rollback tran tsc;
					insert into #tempolary select 'N' as id,'ไม่บันทึก : มีข้อมูลรหัสวิธีชำระค่างวด ".$arrs['PAYCODE']." อยู่แล้ว' as msg;
					return;
				end
			";
		}else{			
			$data = "
				update {$this->MAuth->getdb('PAYDUE')}
				set PAYDESC='".$arrs['PAYDESC']."'
					,MEMO1='".$arrs['MEMO1']."'
				where PAYCODE='".$arrs['PAYCODE']."'
				
				insert into {$this->MAuth->getdb('hp_UserOperationLog')}(userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
				values ('".$this->sess["IDNo"]."','setup วิธีชำระค่างวด แก้ไข','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
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
	
	public function PAYDUEDel(){
		$arrs = array();
		$arrs['PAYCODE'] = (!isset($_REQUEST['PAYCODE'])?'':$_REQUEST['PAYCODE']);
		$arrs['PAYDESC'] = (!isset($_REQUEST['PAYDESC'])?'':$_REQUEST['PAYDESC']);
		
		$sql = "
			if object_id('tempdb..#tempolary') is not null drop table #tempolary;
			create table #tempolary (id varchar(1),msg varchar(max));
			
			begin tran tsc
			begin try			
				delete {$this->MAuth->getdb('PAYDUE')}
				where PAYCODE='".$arrs['PAYCODE']."'
				
				insert into {$this->MAuth->getdb('hp_UserOperationLog')}(userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
				values ('".$this->sess["IDNo"]."','setup วิธีชำระค่างวด ลบ','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
				
				insert into #tempolary select 'Y' as id,'สำเร็จ ลบวิธีชำระค่างวด ".$arrs['PAYCODE']." :: ".$arrs['PAYDESC']."  แล้ว' as msg;
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
	
	public function RTCHQ(){ 
		$claim = $this->MLogin->getclaim(uri_string());
		if($claim['m_access'] != "T"){ echo "<div align='center' style='color:red;font-size:16pt;width:100%;'>ขออภัย คุณยังไม่มีสิทธิเข้าใช้งานหน้านี้ครับ</div>"; exit; }
		
		$html = "
			<div class='tab1' name='home' locat='{$this->sess['branch']}' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}' >
				<div class='col-sm-2'>	
					<div class='form-group'>
						รหัสการคืนเช็ค
						<input type='text' id='RTCODE' class='form-control input-sm' placeholder='รหัสการคืนเช็ค'>
					</div>
				</div>
				<div class='col-sm-6'>	
					<div class='form-group'>
						คำอธิบาย
						<input type='text' id='RTNAME' class='form-control input-sm' placeholder='คำอธิบาย'>	
					</div>
				</div>					
				<div class='col-sm-2'>	
					<div class='form-group'>
						<br>
						<input type='button' id='search_RT' class='btn btn-primary btn-sm btn-block' value='แสดง'>
					</div>
				</div>
				<div class='col-sm-2'>	
					<div class='form-group'>
						<br>
						<input type='button' id='add_RT' class='btn btn-cyan btn-sm btn-block' value='เพิ่ม' >
					</div>
				</div>
			</div>
			
			<div id='setRTResult' class='col-sm-12 tab1' style='height:calc(100vh - 197px);overflow:auto;background-color:#;'></div>
			
			<div id='tab2_main' class='col-sm-12 tab2' hidden style='height:calc(100vh - 130px);overflow:auto;background-color:#;'></div>
		";
	
		$html.= "<script src='".base_url('public/js/setup/Finance/setRTCHQ.js')."'></script>";
		echo $html;
	}
	
	public function RTCHQSearch(){
		$arrs = array();
		$arrs['RTCODE'] = !isset($_REQUEST['RTCODE']) ? '' : $_REQUEST['RTCODE'];
		$arrs['RTNAME'] = !isset($_REQUEST['RTNAME']) ? '' : $_REQUEST['RTNAME'];
		
		$cond = "";
		if($arrs['RTCODE'] != ''){
			$cond .= " and RTCODE like '%".$arrs['RTCODE']."%'";
		}
		
		if($arrs['RTNAME'] != ''){
			$cond .= " and RTNAME like '%".$arrs['RTNAME']."%'";
		}
		
		$sql = "
			select * from {$this->MAuth->getdb('RTCHQ')}
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
						<td class='getit' seq='".$NRow++."' RTCODE='".str_replace(chr(0),'',$row->RTCODE)."' style='width:50px;cursor:pointer;text-align:center;'><b>เลือก</b></td>
						<td>".str_replace(chr(0),'',$row->RTCODE)."</td>
						<td>".str_replace(chr(0),'',$row->RTNAME)."</td>
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
	
	function RTCHQGetFormAE(){
		$RTCODE = $_POST["RTCODE"];
		$data = array(
			"RTCODE"=>""
			,"RTNAME"=>""
			,"MEMO1"=>""
		);
		
		if($RTCODE != ''){
			$sql = "
				select * from {$this->MAuth->getdb('RTCHQ')}
				where RTCODE='".$RTCODE."'
			";
			$query = $this->db->query($sql);
			
			if($query->row()){
				foreach($query->result() as $row){
					$data['RTCODE'] = str_replace(chr(0),'',$row->RTCODE);
					$data['RTNAME'] = str_replace(chr(0),'',$row->RTNAME);
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
							<input type='text' id='t2RTCODE' class='form-control input-sm' value='".$data['RTCODE']."' maxlength=8>
						</div>
					</div>
					
					<div class='col-sm-4 col-sm-offset-4'>	
						<div class='form-group'>
							คำอธิบาย
							<input type='text' id='t2RTNAME' class='form-control input-sm' value='".$data['RTNAME']."' maxlength=60>
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
	
	function RTCHQSave(){
		$arrs = array();
		$arrs['RTCODE'] 	= $_POST['RTCODE'];
		$arrs['RTNAME'] 	= $_POST['RTNAME'];
		$arrs['MEMO1'] 		= $_POST['MEMO1'];
		$arrs['action']		= $_POST['action'];
		
		$data = "";
		if($arrs['action'] == "add"){
			$data = "
				declare @isval int = isnull((select count(*) from {$this->MAuth->getdb('RTCHQ')} where RTCODE='".$arrs['RTCODE']."'),0);
				if(@isval = 0)
				begin 
					insert into {$this->MAuth->getdb('RTCHQ')} (RTCODE,RTNAME,MEMO1)
					select '".$arrs['RTCODE']."','".$arrs['RTNAME']."','".$arrs['MEMO1']."'
					
					insert into {$this->MAuth->getdb('hp_UserOperationLog')}(userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
					values ('".$this->sess["IDNo"]."','setup รหัสการคืนเช็ค เพิ่ม','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
				end 
				else
				begin 
					rollback tran tsc;
					insert into #tempolary select 'N' as id,'ไม่บันทึก : มีข้อมูลรหัสการคืนเช็ค ".$arrs['RTCODE']." อยู่แล้ว' as msg;
					return;
				end
			";
		}else{			
			$data = "
				update {$this->MAuth->getdb('RTCHQ')}
				set RTNAME='".$arrs['RTNAME']."'
					,MEMO1='".$arrs['MEMO1']."'
				where RTCODE='".$arrs['RTCODE']."'
				
				insert into {$this->MAuth->getdb('hp_UserOperationLog')}(userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
				values ('".$this->sess["IDNo"]."','setup รหัสการคืนเช็ค แก้ไข','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
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
	
	public function RTCHQDel(){
		$arrs = array();
		$arrs['RTCODE'] = (!isset($_REQUEST['RTCODE'])?'':$_REQUEST['RTCODE']);
		$arrs['RTNAME'] = (!isset($_REQUEST['RTNAME'])?'':$_REQUEST['RTNAME']);
		
		$sql = "
			if object_id('tempdb..#tempolary') is not null drop table #tempolary;
			create table #tempolary (id varchar(1),msg varchar(max));
			
			begin tran tsc
			begin try			
				delete {$this->MAuth->getdb('RTCHQ')}
				where RTCODE='".$arrs['RTCODE']."'
				
				insert into {$this->MAuth->getdb('hp_UserOperationLog')}(userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
				values ('".$this->sess["IDNo"]."','setup รหัสการคืนเช็ค  ลบ','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
				
				insert into #tempolary select 'Y' as id,'สำเร็จ ลบรหัสการคืนเช็ค ".$arrs['RTCODE']." :: ".$arrs['RTNAME']."  แล้ว' as msg;
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
	
	public function BOOK(){ 
		$claim = $this->MLogin->getclaim(uri_string());
		if($claim['m_access'] != "T"){ echo "<div align='center' style='color:red;font-size:16pt;width:100%;'>ขออภัย คุณยังไม่มีสิทธิเข้าใช้งานหน้านี้ครับ</div>"; exit; }
		
		$html = "
			<div class='tab1' name='home' locat='{$this->sess['branch']}' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}' >
				<div class='col-sm-2'>	
					<div class='form-group'>
						รหัสบัญชี
						<input type='text' id='BKCODE' class='form-control input-sm' placeholder='รหัสบัญชี'>
					</div>
				</div>
				<div class='col-sm-6'>	
					<div class='form-group'>
						คำอธิบาย
						<input type='text' id='BKNAME' class='form-control input-sm' placeholder='คำอธิบาย'>	
					</div>
				</div>					
				<div class='col-sm-2'>	
					<div class='form-group'>
						<br>
						<input type='button' id='search_book' class='btn btn-primary btn-sm btn-block' value='แสดง'>
					</div>
				</div>
				<div class='col-sm-2'>	
					<div class='form-group'>
						<br>
						<input type='button' id='add_book' class='btn btn-cyan btn-sm btn-block' value='เพิ่ม' >
					</div>
				</div>
			</div>
			
			<div id='setBookResult' class='col-sm-12 tab1' style='height:calc(100vh - 197px);overflow:auto;background-color:#;'></div>
			
			<div id='tab2_main' class='col-sm-12 tab2' hidden style='height:calc(100vh - 130px);overflow:auto;background-color:#;'></div>
		";
	
		$html.= "<script src='".base_url('public/js/setup/Finance/setBOOK.js')."'></script>";
		echo $html;
	}
	
	public function BOOKSearch(){
		$arrs = array();
		$arrs['BKCODE'] = !isset($_REQUEST['BKCODE']) ? '' : $_REQUEST['BKCODE'];
		$arrs['BKNAME'] = !isset($_REQUEST['BKNAME']) ? '' : $_REQUEST['BKNAME'];
		
		$cond = "";
		if($arrs['BKCODE'] != ''){
			$cond .= " and BKCODE like '%".$arrs['BKCODE']."%'";
		}
		
		if($arrs['BKNAME'] != ''){
			$cond .= " and BKNAME like '%".$arrs['BKNAME']."%'";
		}
		
		$sql = "
			select * from {$this->MAuth->getdb('BOOK')}
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
						<td class='getit' seq='".$NRow++."' BKCODE='".str_replace(chr(0),'',$row->BKCODE)."' style='width:50px;cursor:pointer;text-align:center;'><b>เลือก</b></td>
						<td>".str_replace(chr(0),'',$row->BKCODE)."</td>
						<td>".str_replace(chr(0),'',$row->BKNAME)."</td>
						<td>".str_replace(chr(0),'',$row->ACC_CODE)."</td>
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
							<th>รหัสบัญชี</th>
							<th>คำอธิบาย</th>
							<th>เลขที่บัญชี</th>
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
	
	function BOOKGetFormAE(){
		$bkcode = $_POST["BKCODE"];
		$data = array(
			"BKCODE"=>""
			,"BKNAME"=>""
			,"ACC_CODE"=>""
			,"MEMO1"=>""
		);
		
		if($bkcode != ''){
			$sql = "
				select * from {$this->MAuth->getdb('BOOK')}
				where BKCODE='".$bkcode."'
			";
			$query = $this->db->query($sql);
			
			if($query->row()){
				foreach($query->result() as $row){
					$data['BKCODE'] = str_replace(chr(0),'',$row->BKCODE);
					$data['BKNAME'] = str_replace(chr(0),'',$row->BKNAME);
					$data['ACC_CODE'] = str_replace(chr(0),'',$row->ACC_CODE);
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
							<input type='text' id='t2BKCODE' class='form-control input-sm' value='".$data['BKCODE']."' maxlength=13>
						</div>
					</div>
					
					<div class='col-sm-4 col-sm-offset-4'>	
						<div class='form-group'>
							คำอธิบาย
							<input type='text' id='t2BKNAME' class='form-control input-sm' value='".$data['BKNAME']."' maxlength=60>
						</div>
					</div>
					
					<div class='col-sm-4 col-sm-offset-4'>	
						<div class='form-group'>
							เลขที่บัญชี
							<input type='text' id='t2ACC_CODE' class='form-control input-sm' value='".$data['ACC_CODE']."' maxlength=13>
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
	
	function BOOKSave(){
		$arrs = array();
		$arrs['BKCODE'] 	= $_POST['BKCODE'];
		$arrs['BKNAME'] 	= $_POST['BKNAME'];
		$arrs['ACC_CODE'] 	= $_POST['ACC_CODE'];
		$arrs['MEMO1'] 		= $_POST['MEMO1'];
		$arrs['action']		= $_POST['action'];
		
		$data = "";
		if($arrs['action'] == "add"){
			$data = "
				declare @isval int = isnull((select count(*) from {$this->MAuth->getdb('BOOK')} where BKCODE='".$arrs['BKCODE']."'),0);
				if(@isval = 0)
				begin 
					insert into {$this->MAuth->getdb('BOOK')} (BKCODE,BKNAME,ACC_CODE,MEMO1)
					select '".$arrs['BKCODE']."','".$arrs['BKNAME']."','".$arrs['ACC_CODE']."','".$arrs['MEMO1']."'
					
					insert into {$this->MAuth->getdb('hp_UserOperationLog')}(userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
					values ('".$this->sess["IDNo"]."','setup รหัสบัญชีนำฝาก เพิ่ม','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
				end 
				else
				begin 
					rollback tran tsc;
					insert into #tempolary select 'N' as id,'ไม่บันทึก : มีข้อมูลรหัสบัญชีนำฝาก ".$arrs['BKCODE']." อยู่แล้ว' as msg;
					return;
				end
			";
		}else{			
			$data = "
				update {$this->MAuth->getdb('BOOK')}
				set BKNAME='".$arrs['BKNAME']."'
					,ACC_CODE='".$arrs['ACC_CODE']."'
					,MEMO1='".$arrs['MEMO1']."'
				where BKCODE='".$arrs['BKCODE']."'
				
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
	
	public function BOOKDel(){
		$arrs = array();
		$arrs['BKCODE'] = (!isset($_REQUEST['BKCODE'])?'':$_REQUEST['BKCODE']);
		$arrs['BKNAME'] = (!isset($_REQUEST['BKNAME'])?'':$_REQUEST['BKNAME']);
		
		$sql = "
			if object_id('tempdb..#tempolary') is not null drop table #tempolary;
			create table #tempolary (id varchar(1),msg varchar(max));
			
			begin tran tsc
			begin try			
				delete {$this->MAuth->getdb('BOOK')}
				where BKCODE='".$arrs['BKCODE']."'
				
				insert into {$this->MAuth->getdb('hp_UserOperationLog')}(userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
				values ('".$this->sess["IDNo"]."','setup รหัสบัญชีนำฝาก  ลบ','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
				
				insert into #tempolary select 'Y' as id,'สำเร็จ ลบรหัสบัญชีนำฝาก ".$arrs['BKCODE']." :: ".$arrs['BKNAME']."  แล้ว' as msg;
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




















