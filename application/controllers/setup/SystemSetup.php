<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/********************************************************
             ______@29/09/2020______
            / / _ _   _ _     __ 
           / // __ \ / __ \ / __ \
       _ _/ // /_/ // / / // /_/ /
     /_ _ _/ \_ _ //_/ /_/ \__  /
                          _ _/ /
                         /___ /
********************************************************/
class SystemSetup extends MY_Controller {
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
	
	public function SETINVLOCAT(){
		$claim = $this->MLogin->getclaim(uri_string());
		if($claim['m_access'] != "T"){ echo "<div align='center' style='color:red;font-size:16pt;width:100%;'>ขออภัย คุณยังไม่มีสิทธิเข้าใช้งานหน้านี้ครับ</div>"; exit; }
		
		$html = "
			<div class='tab1' name='home' locat='{$this->sess['branch']}' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}' >
				<div class='col-sm-2'>	
					<div class='form-group'>
						รหัสสาขา
						<input type='text' id='LOCATCD' class='form-control input-sm' placeholder='รหัสสาขา'>
					</div>
				</div>
				<div class='col-sm-6'>	
					<div class='form-group'>
						ชื่อสาขา
						<input type='text' id='LOCATNM' class='form-control input-sm' placeholder='ชื่อสาขา'>	
					</div>
				</div>					
				<div class='col-sm-2'>	
					<div class='form-group'>
						<br>
						<input type='button' id='search_invlocat' class='btn btn-primary btn-sm btn-block' value='แสดง'>
					</div>
				</div>
				<div class='col-sm-2'>	
					<div class='form-group'>
						<br>
						<input type='button' id='add_invlocat' class='btn btn-cyan btn-sm btn-block' value='เพิ่ม' >
					</div>
				</div>
			</div>
			
			<div id='setInvlocatResult' class='col-sm-12 tab1' style='height:calc(100vh - 197px);overflow:auto;background-color:#;'></div>
			
			<div id='tab2_main' class='col-sm-12 tab2' hidden style='height:calc(100vh - 130px);overflow:auto;background-color:#;'></div>
		";
	
		$html.= "<script src='".base_url('public/js/setup/SystemSetup/setINVLOCAT.js')."'></script>";
		echo $html;
	}
	
	public function SETINVLOCATSearch(){
		$arrs = array();
		$arrs['LOCATCD'] = !isset($_REQUEST['LOCATCD']) ? '' : $_REQUEST['LOCATCD'];
		$arrs['LOCATNM'] = !isset($_REQUEST['LOCATNM']) ? '' : $_REQUEST['LOCATNM'];
		
		$cond = "";
		if($arrs['LOCATCD'] != ''){
			$cond .= " and LOCATCD like '%".$arrs['LOCATCD']."%'";
		}
		
		if($arrs['LOCATNM'] != ''){
			$cond .= " and LOCATNM like '%".$arrs['LOCATNM']."%'";
		}
		
		$sql = "
			select * from {$this->MAuth->getdb('INVLOCAT')}
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
						<td class='getit' seq='".$NRow++."' LOCATCD='".str_replace(chr(0),'',$row->LOCATCD)."' style='width:50px;cursor:pointer;text-align:center;'><b>เลือก</b></td>
						<td>".str_replace(chr(0),'',$row->LOCATCD)."</td>
						<td>".str_replace(chr(0),'',$row->LOCATNM)."</td>
						<td>".str_replace(chr(0),'',$row->SHORTL)."</td>
						<td>".str_replace(chr(0),'',$row->LOCADDR1)."</td>
						<td>".str_replace(chr(0),'',$row->LOCADDR2)."</td>
						<td>".str_replace(chr(0),'',$row->FLSALE)."</td>
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
							<th>รหัสสาขา</th>
							<th>ชื่อสาขา</th>
							<th>รหัสย่อ</th>
							<th>ที่อยู่ 1</th>
							<th>ที่อยู่ 2</th>
							<th>การขาย</th>
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
	
	function SETINVLOCATGetFormAE(){
		$LOCATCD = $_POST["LOCATCD"];
		$data = array(
			"LOCATCD"=>""
			,"LOCATNM"=>""
			,"SHORTL"=>""
			,"LOCADDR1"=>""
			,"LOCADDR2"=>""
			,"FLSALE"=>""
			,"MEMO1"=>""
		);
		
		if($LOCATCD != ''){
			$sql = "
				select * from {$this->MAuth->getdb('INVLOCAT')}
				where LOCATCD='".$LOCATCD."'
			";
			$query = $this->db->query($sql);
			
			if($query->row()){
				foreach($query->result() as $row){
					$data['LOCATCD'] = str_replace(chr(0),'',$row->LOCATCD);
					$data['LOCATNM'] = str_replace(chr(0),'',$row->LOCATNM);
					$data['SHORTL'] = str_replace(chr(0),'',$row->SHORTL);
					$data['LOCADDR1'] = str_replace(chr(0),'',$row->LOCADDR1);
					$data['LOCADDR2'] = str_replace(chr(0),'',$row->LOCADDR2);
					$data['FLSALE'] = str_replace(chr(0),'',$row->FLSALE);
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
							รหัสสาขา
							<input type='text' id='t2LOCATCD' class='form-control input-sm' value='".$data['LOCATCD']."' maxlength=8>
						</div>
					</div>
					
					<div class='col-sm-4 col-sm-offset-4'>	
						<div class='form-group'>
							ชื่อสาขา
							<input type='text' id='t2LOCATNM' class='form-control input-sm' value='".$data['LOCATNM']."' maxlength=60>
						</div>
					</div>
					
					<div class='col-sm-4 col-sm-offset-4'>	
						<div class='form-group'>
							รหัสย่อ
							<input type='text' id='t2SHORTL' class='form-control input-sm' value='".$data['SHORTL']."' maxlength=60>
						</div>
					</div>
					
					<div class='col-sm-4 col-sm-offset-4'>	
						<div class='form-group'>
							ที่อยู่ 1
							<input type='text' id='t2LOCADDR1' class='form-control input-sm' value='".$data['LOCADDR1']."' maxlength=60>
						</div>
					</div>
					
					<div class='col-sm-4 col-sm-offset-4'>	
						<div class='form-group'>
							ที่อยู่ 2
							<input type='text' id='t2LOCADDR2' class='form-control input-sm' value='".$data['LOCADDR2']."' maxlength=60>
						</div>
					</div>
					
					<div class='col-sm-4 col-sm-offset-4'>	
						<div class='form-group'>
							<label class='checkbox lobicheck lobicheck-primary lobicheck-inversed'>
								<input type='checkbox' id='t2FLSALE' ".($data['FLSALE']=="T"?"checked":"")."> 
								<i></i> ขายได้
							</label>
						</div>
					</div>
					
					<!-- div class='col-sm-4 col-sm-offset-4'>	
						<div class='form-group'>
							<label class='checkbox lobicheck lobicheck-primary lobicheck-inversed'>
								<input type='checkbox' id='t2FLSALE' ".($data['FLSALE']=="T"?"checked":"")."> 
								<i></i> ห้ามเปลี่ยนวันที่
							</label>
						</div>
					</div -->
					
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
	
	function SETINVLOCATSave(){
		$arrs = array();
		$arrs['LOCATCD'] 	= $_POST['LOCATCD'];
		$arrs['LOCATNM'] 	= $_POST['LOCATNM'];
		$arrs['SHORTL'] 	= $_POST['SHORTL'];
		$arrs['LOCADDR1'] 	= $_POST['LOCADDR1'];
		$arrs['LOCADDR2'] 	= $_POST['LOCADDR2'];
		$arrs['FLSALE'] 	= $_POST['FLSALE'];
		$arrs['MEMO1'] 		= $_POST['MEMO1'];
		
		$arrs['action']		= $_POST['action'];
		
		$data = "";
		if($arrs['action'] == "add"){
			$data = "
				declare @isvalLOCAT int = isnull((select count(*) from {$this->MAuth->getdb('INVLOCAT')} where LOCATCD='".$arrs['LOCATCD']."'),0);
				declare @isvalSHORTL int = isnull((select count(*) from {$this->MAuth->getdb('INVLOCAT')} where SHORTL='".$arrs['SHORTL']."'),0);
				if(@isvalLOCAT > 0)
				begin 
					rollback tran tsc;
					insert into #tempolary select 'N' as id,'ไม่บันทึก : มีข้อมูลรหัสสาขา ".$arrs['LOCATCD']." อยู่แล้ว' as msg;
					return;
				end 
				else if(@isvalSHORTL > 0)
				begin 
					rollback tran tsc;
					insert into #tempolary select 'N' as id,'ไม่บันทึก : มีข้อมูลรหัสย่อสาขา ".$arrs['SHORTL']." อยู่แล้ว' as msg;
					return;
				end 
				else
				begin 
					insert into {$this->MAuth->getdb('INVLOCAT')} (LOCATCD,LOCATNM,SHORTL,LOCADDR1,LOCADDR2,FLSALE,MEMO1)
					select '".$arrs['LOCATCD']."','".$arrs['LOCATNM']."','".$arrs['SHORTL']."'
						,'".$arrs['LOCADDR1']."','".$arrs['LOCADDR2']."','".$arrs['FLSALE']."','".$arrs['MEMO1']."'
					
					insert into {$this->MAuth->getdb('hp_UserOperationLog')}(userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
					values ('".$this->sess["IDNo"]."','setup รหัสสาขา เพิ่ม','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
				end
			";
		}else{			
			$data = "
				declare @isvalSHORTL varchar(max) = isnull(stuff((
					select ','+LOCATCD from {$this->MAuth->getdb('INVLOCAT')} 
					where SHORTL='".$arrs['SHORTL']."' and LOCATCD!='".$arrs['LOCATCD']."'
					for xml path('')
				),1,1,''),'');
				
				if(@isvalSHORTL != '')
				begin 
					rollback tran tsc;
					insert into #tempolary select 'N' as id,'ไม่บันทึก : มีข้อมูลรหัสย่อสาขา ".$arrs['SHORTL']." อยู่แล้ว<br>สาขา '+@isvalSHORTL as msg;
					return;
				end 
				else
				begin
					update {$this->MAuth->getdb('INVLOCAT')}
					set LOCATNM='".$arrs['LOCATNM']."'
						,SHORTL='".$arrs['SHORTL']."'
						,LOCADDR1='".$arrs['LOCADDR1']."'
						,LOCADDR2='".$arrs['LOCADDR2']."'
						,FLSALE='".$arrs['FLSALE']."'
						,MEMO1='".$arrs['MEMO1']."'
					where LOCATCD='".$arrs['LOCATCD']."'
				end 
				
				insert into {$this->MAuth->getdb('hp_UserOperationLog')}(userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
				values ('".$this->sess["IDNo"]."','setup รหัสสาขา แก้ไข','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
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
	
	public function SETINVLOCATDel(){
		$arrs = array();
		$arrs['LOCATCD'] = (!isset($_REQUEST['LOCATCD'])?'':$_REQUEST['LOCATCD']);
		$arrs['LOCATNM'] = (!isset($_REQUEST['LOCATNM'])?'':$_REQUEST['LOCATNM']);
		
		$sql = "
			if object_id('tempdb..#tempolary') is not null drop table #tempolary;
			create table #tempolary (id varchar(1),msg varchar(max));
			
			begin tran tsc
			begin try
				if exists (
					select distinct 1 from {$this->MAuth->getdb('ARMAST')} where LOCAT='".$arrs['LOCATCD']."'
					union select distinct 1 from {$this->MAuth->getdb('ARPAY')} where LOCAT='".$arrs['LOCATCD']."'
					union select distinct 1 from {$this->MAuth->getdb('ARCRED')} where LOCAT='".$arrs['LOCATCD']."'
					union select distinct 1 from {$this->MAuth->getdb('ARFINC')} where LOCAT='".$arrs['LOCATCD']."'
					union select distinct 1 from {$this->MAuth->getdb('ARRESV')} where LOCAT='".$arrs['LOCATCD']."'
					union select distinct 1 from {$this->MAuth->getdb('INVINVO')} where LOCAT='".$arrs['LOCATCD']."'
					union select distinct 1 from {$this->MAuth->getdb('INVTRAN')} where CRLOCAT='".$arrs['LOCATCD']."'
					union select distinct 1 from {$this->MAuth->getdb('CHQMAS')} where LOCATRECV='".$arrs['LOCATCD']."'
					union select distinct 1 from {$this->MAuth->getdb('CHQTRAN')} where LOCATRECV='".$arrs['LOCATCD']."'
				)
				begin 
					rollback tran tsc;
					insert into #tempolary select 'N' as id,'ผิดพลาด  :  มีข้อมูลในสาขา ".$arrs['LOCATCD']." ไม่สามารถลบสาขาได้' as msg;
					return;
				end else begin
					delete {$this->MAuth->getdb('INVLOCAT')}
					where LOCATCD='".$arrs['LOCATCD']."'
				end 
				insert into {$this->MAuth->getdb('hp_UserOperationLog')}(userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
				values ('".$this->sess["IDNo"]."','setup รหัสสาขา ลบ','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
				
				insert into #tempolary select 'Y' as id,'สำเร็จ ลบรหัสสาขา ".$arrs['LOCATCD']." :: ".$arrs['LOCATNM']."  แล้ว' as msg;
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
		
	public function SETDEPMAST(){
		$claim = $this->MLogin->getclaim(uri_string());
		if($claim['m_access'] != "T"){ echo "<div align='center' style='color:red;font-size:16pt;width:100%;'>ขออภัย คุณยังไม่มีสิทธิเข้าใช้งานหน้านี้ครับ</div>"; exit; }
		
		$html = "
			<div class='tab1' name='home' locat='{$this->sess['branch']}' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}' >
				<div class='col-sm-2'>	
					<div class='form-group'>
						รหัสแผนก
						<input type='text' id='DEPCODE' class='form-control input-sm' placeholder='รหัสแผนก'>
					</div>
				</div>
				<div class='col-sm-6'>	
					<div class='form-group'>
						ชื่อแผนก
						<input type='text' id='DEPNAME' class='form-control input-sm' placeholder='ชื่อแผนก'>	
					</div>
				</div>					
				<div class='col-sm-2'>	
					<div class='form-group'>
						<br>
						<input type='button' id='search_depmast' class='btn btn-primary btn-sm btn-block' value='แสดง'>
					</div>
				</div>
				<div class='col-sm-2'>	
					<div class='form-group'>
						<br>
						<input type='button' id='add_depmast' class='btn btn-cyan btn-sm btn-block' value='เพิ่ม' >
					</div>
				</div>
			</div>
			
			<div id='setDepmastResult' class='col-sm-12 tab1' style='height:calc(100vh - 197px);overflow:auto;background-color:#;'></div>
			
			<div id='tab2_main' class='col-sm-12 tab2' hidden style='height:calc(100vh - 130px);overflow:auto;background-color:#;'></div>
		";
	
		$html.= "<script src='".base_url('public/js/setup/SystemSetup/setDEPMAST.js')."'></script>";
		echo $html;
	}
	
	public function SETDEPMASTSearch(){
		$arrs = array();
		$arrs['DEPCODE'] = !isset($_REQUEST['DEPCODE']) ? '' : $_REQUEST['DEPCODE'];
		$arrs['DEPNAME'] = !isset($_REQUEST['DEPNAME']) ? '' : $_REQUEST['DEPNAME'];
		
		$cond = "";
		if($arrs['DEPCODE'] != ''){
			$cond .= " and DEPCODE like '%".$arrs['DEPCODE']."%'";
		}
		
		if($arrs['DEPNAME'] != ''){
			$cond .= " and DEPNAME like '%".$arrs['DEPNAME']."%'";
		}
		
		$sql = "
			select * from {$this->MAuth->getdb('DEPMAST')}
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
						<td class='getit' seq='".$NRow++."' DEPCODE='".str_replace(chr(0),'',$row->DEPCODE)."' style='width:50px;cursor:pointer;text-align:center;'><b>เลือก</b></td>
						<td>".str_replace(chr(0),'',$row->DEPCODE)."</td>
						<td>".str_replace(chr(0),'',$row->DEPNAME)."</td>
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
							<th>รหัสแผนก</th>
							<th>ชื่อแผนก</th>
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
	
	function SETDEPMASTGetFormAE(){
		$DEPCODE = $_POST["DEPCODE"];
		$data = array(
			"DEPCODE"=>""
			,"DEPNAME"=>""
			,"MEMO1"=>""
		);
		
		if($DEPCODE != ''){
			$sql = "
				select * from {$this->MAuth->getdb('DEPMAST')}
				where DEPCODE='".$DEPCODE."'
			";
			$query = $this->db->query($sql);
			
			if($query->row()){
				foreach($query->result() as $row){
					$data['DEPCODE'] = str_replace(chr(0),'',$row->DEPCODE);
					$data['DEPNAME'] = str_replace(chr(0),'',$row->DEPNAME);
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
							รหัสสาขา
							<input type='text' id='t2DEPCODE' class='form-control input-sm' value='".$data['DEPCODE']."' maxlength=8>
						</div>
					</div>
					
					<div class='col-sm-4 col-sm-offset-4'>	
						<div class='form-group'>
							ชื่อสาขา
							<input type='text' id='t2DEPNAME' class='form-control input-sm' value='".$data['DEPNAME']."' maxlength=60>
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
	
	function SETDEPMASTSave(){
		$arrs = array();
		$arrs['DEPCODE'] 	= $_POST['DEPCODE'];
		$arrs['DEPNAME'] 	= $_POST['DEPNAME'];
		$arrs['MEMO1'] 		= $_POST['MEMO1'];
		
		$arrs['action']		= $_POST['action'];
		
		$data = "";
		if($arrs['action'] == "add"){
			$data = "
				declare @isval int = isnull((select count(*) from {$this->MAuth->getdb('DEPMAST')} where DEPCODE='".$arrs['DEPCODE']."'),0);
				
				if(@isval > 0)
				begin 
					rollback tran tsc;
					insert into #tempolary select 'N' as id,'ไม่บันทึก : มีข้อมูลรหัสแผนก ".$arrs['DEPCODE']." อยู่แล้ว' as msg;
					return;
				end 
				else
				begin 
					insert into {$this->MAuth->getdb('DEPMAST')} (DEPCODE,DEPNAME,MEMO1)
					select '".$arrs['DEPCODE']."','".$arrs['DEPNAME']."','".$arrs['MEMO1']."'
					
					insert into {$this->MAuth->getdb('hp_UserOperationLog')}(userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
					values ('".$this->sess["IDNo"]."','setup รหัสแผนก เพิ่ม','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
				end
			";
		}else{			
			$data = "
				update {$this->MAuth->getdb('DEPMAST')}
				set DEPNAME='".$arrs['DEPNAME']."'
					,MEMO1='".$arrs['MEMO1']."'
				where DEPCODE='".$arrs['DEPCODE']."'
				
				
				insert into {$this->MAuth->getdb('hp_UserOperationLog')}(userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
				values ('".$this->sess["IDNo"]."','setup รหัสแผนก แก้ไข','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
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
	
	public function SETDEPMASTDel(){
		$arrs = array();
		$arrs['DEPCODE'] = (!isset($_REQUEST['DEPCODE'])?'':$_REQUEST['DEPCODE']);
		$arrs['DEPNAME'] = (!isset($_REQUEST['DEPNAME'])?'':$_REQUEST['DEPNAME']);
		
		$sql = "
			if object_id('tempdb..#tempolary') is not null drop table #tempolary;
			create table #tempolary (id varchar(1),msg varchar(max));
			
			begin tran tsc
			begin try
				
				delete {$this->MAuth->getdb('DEPMAST')}
				where DEPCODE='".$arrs['DEPCODE']."'
				
				insert into {$this->MAuth->getdb('hp_UserOperationLog')}(userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
				values ('".$this->sess["IDNo"]."','setup รหัสแผนก ลบ','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
				
				insert into #tempolary select 'Y' as id,'สำเร็จ ลบรหัสแผนก ".$arrs['DEPCODE']." :: ".$arrs['DEPNAME']."  แล้ว' as msg;
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
	
	public function SETOFFICER(){
		$claim = $this->MLogin->getclaim(uri_string());
		if($claim['m_access'] != "T"){ echo "<div align='center' style='color:red;font-size:16pt;width:100%;'>ขออภัย คุณยังไม่มีสิทธิเข้าใช้งานหน้านี้ครับ</div>"; exit; }
		
		$html = "
			<div class='tab1' name='home' locat='{$this->sess['branch']}' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}' >
				<div class='col-sm-2'>	
					<div class='form-group'>
						รหัสพนักงาน
						<input type='text' id='CODE' class='form-control input-sm' placeholder='รหัสพนักงาน'>
					</div>
				</div>
				<div class='col-sm-6'>	
					<div class='form-group'>
						ชื่อ-สกุล
						<input type='text' id='NAME' class='form-control input-sm' placeholder='ชื่อ-สกุล'>	
					</div>
				</div>					
				<div class='col-sm-2'>	
					<div class='form-group'>
						<br>
						<input type='button' id='search_official' class='btn btn-primary btn-sm btn-block' value='แสดง'>
					</div>
				</div>
				<div class='col-sm-2'>	
					<div class='form-group'>
						<br>
						<input type='button' id='add_official' class='btn btn-cyan btn-sm btn-block' value='เพิ่ม' >
					</div>
				</div>
			</div>
			
			<div id='setOfficialResult' class='col-sm-12 tab1' style='height:calc(100vh - 197px);overflow:auto;background-color:#;'></div>
			
			<div id='tab2_main' class='col-sm-12 tab2' hidden style='height:calc(100vh - 130px);overflow:auto;background-color:#;'></div>
		";
	
		$html.= "<script src='".base_url('public/js/setup/SystemSetup/setOFFICER.js')."'></script>";
		echo $html;
	}
	
	public function SETOFFICERSearch(){
		$arrs = array();
		$arrs['CODE'] = !isset($_REQUEST['CODE']) ? '' : $_REQUEST['CODE'];
		$arrs['NAME'] = !isset($_REQUEST['NAME']) ? '' : $_REQUEST['NAME'];
		
		$cond = "";
		if($arrs['CODE'] != ''){
			$cond .= " and CODE like '%".$arrs['CODE']."%'";
		}
		
		if($arrs['NAME'] != ''){
			$cond .= " and NAME like '%".$arrs['NAME']."%'";
		}
		
		$sql = "
			select * from {$this->MAuth->getdb('OFFICER')}
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
						<td class='getit' seq='".$NRow++."' CODE='".str_replace(chr(0),'',$row->CODE)."' style='width:50px;cursor:pointer;text-align:center;'><b>เลือก</b></td>
						<td>".str_replace(chr(0),'',$row->CODE)."</td>
						<td>".str_replace(chr(0),'',$row->NAME)."</td>
						<td>".str_replace(chr(0),'',$row->DEPCODE)."</td>
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
							<th>รหัสพนักงาน</th>
							<th>ชื่อ-สกุล</th>
							<th>แผนก</th>
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
	
	function SETOFFICERGetFormAE(){
		$CODE = $_POST["CODE"];
		$data = array(
			"CODE"=>""
			,"NAME"=>""
			,"DEPCODE"=>""
			,"MEMO1"=>""
		);
		
		if($CODE != ''){
			$sql = "
				select * from {$this->MAuth->getdb('OFFICER')}
				where CODE='".$CODE."'
			";
			$query = $this->db->query($sql);
			
			if($query->row()){
				foreach($query->result() as $row){
					$data['CODE'] = str_replace(chr(0),'',$row->CODE);
					$data['NAME'] = str_replace(chr(0),'',$row->NAME);
					$data['DEPCODE'] = str_replace(chr(0),'',$row->DEPCODE);
					$data['MEMO1'] = str_replace(chr(0),'',$row->MEMO1);
				}
			}
		}
		
		$sqlDep = "select DEPCODE,DEPNAME from {$this->MAuth->getdb('DEPMAST')}";
		$queryDep = $this->db->query($sqlDep);
		
		$OptionDEP = "<option value=''> เลือก</option>";
		if($queryDep->row()){
			foreach($queryDep->result() as $rowDep){
				$OptionDEP .= "<option value='".$rowDep->DEPCODE."' ".($data['DEPCODE']==$rowDep->DEPCODE?"selected":"").">".$rowDep->DEPCODE." ".$rowDep->DEPNAME."</option>";
			}			
		}
		
		$response = array();
		$response['html'] = "
			<div class='col-sm-12'>
				<div style='height:calc(100vh - 165px);overflow:auto;'>
					<div class='col-sm-4 col-sm-offset-4'>	
						<div class='form-group'>
							รหัสพนักงาน
							<input type='text' id='t2CODE' class='form-control input-sm' value='".$data['CODE']."' maxlength=8>
						</div>
					</div>
					
					<div class='col-sm-4 col-sm-offset-4'>	
						<div class='form-group'>
							ชื่อ-สกุล
							<input type='text' id='t2NAME' class='form-control input-sm' value='".$data['NAME']."' maxlength=60>
						</div>
					</div>
					
					<div class='col-sm-4 col-sm-offset-4'>	
						<div class='form-group'>
							แผนก
							<!-- input type='text' id='t2DEPCODE' class='form-control input-sm' value='".$data['DEPCODE']."' maxlength=60 -->
							<select id='t2DEPCODE' class='form-control input-sm' data-placeholder='แผนก'>{$OptionDEP}</select>
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
	
	function SETOFFICERSave(){
		$arrs = array();
		$arrs['CODE'] 	= $_POST['CODE'];
		$arrs['NAME'] 	= $_POST['NAME'];
		$arrs['DEPCODE'] = $_POST['DEPCODE'];
		$arrs['MEMO1'] 	 = $_POST['MEMO1'];
		
		$arrs['action']		= $_POST['action'];
		
		$data = "";
		if($arrs['action'] == "add"){
			$data = "
				declare @isval int = isnull((select count(*) from {$this->MAuth->getdb('OFFICER')} where CODE='".$arrs['CODE']."'),0);
				
				if(@isval > 0)
				begin 
					rollback tran tsc;
					insert into #tempolary select 'N' as id,'ไม่บันทึก : มีข้อมูลรหัสพนักงาน ".$arrs['CODE']." อยู่แล้ว' as msg;
					return;
				end 
				else
				begin 
					insert into {$this->MAuth->getdb('OFFICER')} (CODE,NAME,DEPCODE,MEMO1)
					select '".$arrs['CODE']."','".$arrs['NAME']."','".$arrs['DEPCODE']."','".$arrs['MEMO1']."'
					
					insert into {$this->MAuth->getdb('hp_UserOperationLog')}(userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
					values ('".$this->sess["IDNo"]."','setup รหัสพนักงาน เพิ่ม','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
				end
			";
		}else{			
			$data = "
				update {$this->MAuth->getdb('OFFICER')}
				set NAME='".$arrs['NAME']."'
					,DEPCODE='".$arrs['DEPCODE']."'
					,MEMO1='".$arrs['MEMO1']."'
				where CODE='".$arrs['CODE']."'
				
				
				insert into {$this->MAuth->getdb('hp_UserOperationLog')}(userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
				values ('".$this->sess["IDNo"]."','setup รหัสพนักงาน แก้ไข','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
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
	
	public function SETOFFICERDel(){
		$arrs = array();
		$arrs['CODE'] = (!isset($_REQUEST['CODE'])?'':$_REQUEST['CODE']);
		$arrs['NAME'] = (!isset($_REQUEST['NAME'])?'':$_REQUEST['NAME']);
		
		$sql = "
			if object_id('tempdb..#tempolary') is not null drop table #tempolary;
			create table #tempolary (id varchar(1),msg varchar(max));
			
			begin tran tsc
			begin try
				if exists(
					select * from {$this->MAuth->getdb('PASSWRD')} 
					where USERID='".$arrs['CODE']."'
				)
				begin 
					rollback tran tsc;
					insert into #tempolary select 'N' as id,'ผิดพลาด : รหัสพนักงานถูกนำไปใช้งานแล้ว ไม่สามารถลบได้' as msg;
					return;
				end else begin 
					delete {$this->MAuth->getdb('OFFICER')}
					where CODE='".$arrs['CODE']."'
				end 
				
				insert into {$this->MAuth->getdb('hp_UserOperationLog')}(userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
				values ('".$this->sess["IDNo"]."','setup รหัสพนักงาน ลบ','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
				
				insert into #tempolary select 'Y' as id,'สำเร็จ ลบรหัสพนักงาน ".$arrs['CODE']." :: ".$arrs['NAME']."  แล้ว' as msg;
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
	
	public function SETAUMP(){
		$claim = $this->MLogin->getclaim(uri_string());
		if($claim['m_access'] != "T"){ echo "<div align='center' style='color:red;font-size:16pt;width:100%;'>ขออภัย คุณยังไม่มีสิทธิเข้าใช้งานหน้านี้ครับ</div>"; exit; }
		
		$sqlprov = "select PROVCOD,PROVDES from {$this->MAuth->getdb('SETPROV')}";
		$queryprov = $this->db->query($sqlprov);
		
		$OptionPROV = "<option value=''> เลือก</option>";
		if($queryprov->row()){
			foreach($queryprov->result() as $rowprov){
				$OptionPROV .= "<option value='".$rowprov->PROVCOD."'>".$rowprov->PROVCOD." ".$rowprov->PROVDES."</option>";
			}			
		}
		
		$html = "
			<div class='tab1' name='home' locat='{$this->sess['branch']}' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}' >
				<div class='col-sm-2'>	
					<div class='form-group'>
						จังหวัด
						<select id='PROVCOD' class='form-control input-sm' data-placeholder='จังหวัด'>{$OptionPROV}</select>
					</div>
				</div>
				<div class='col-sm-2'>	
					<div class='form-group'>
						อำเภอ
						<input type='text' id='AUMPCOD' class='form-control input-sm' placeholder='อำเภอ'>	
					</div>
				</div>					
				<div class='col-sm-2'>	
					<div class='form-group'>
						<br>
						<input type='button' id='search_official' class='btn btn-primary btn-sm btn-block' value='แสดง'>
					</div>
				</div>
				<div class='col-sm-2'>	
					<div class='form-group'>
						<br>
						<input type='button' id='add_official' class='btn btn-cyan btn-sm btn-block' value='เพิ่ม' >
					</div>
				</div>
			</div>
			
			<div id='setOfficialResult' class='col-sm-12 tab1' style='height:calc(100vh - 197px);overflow:auto;background-color:#;'></div>
			
			<div id='tab2_main' class='col-sm-12 tab2' hidden style='height:calc(100vh - 130px);overflow:auto;background-color:#;'></div>
		";
	
		$html.= "<script src='".base_url('public/js/setup/SystemSetup/setAUMP.js')."'></script>";
		echo $html;
	}
	
	public function SETAUMPSearch(){
		$arrs = array();
		$arrs['CODE'] = !isset($_REQUEST['CODE']) ? '' : $_REQUEST['CODE'];
		$arrs['NAME'] = !isset($_REQUEST['NAME']) ? '' : $_REQUEST['NAME'];
		
		$cond = "";
		if($arrs['CODE'] != ''){
			$cond .= " and CODE like '%".$arrs['CODE']."%'";
		}
		
		if($arrs['NAME'] != ''){
			$cond .= " and NAME like '%".$arrs['NAME']."%'";
		}
		
		$sql = "
			select * from {$this->MAuth->getdb('OFFICER')}
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
						<td class='getit' seq='".$NRow++."' CODE='".str_replace(chr(0),'',$row->CODE)."' style='width:50px;cursor:pointer;text-align:center;'><b>เลือก</b></td>
						<td>".str_replace(chr(0),'',$row->CODE)."</td>
						<td>".str_replace(chr(0),'',$row->NAME)."</td>
						<td>".str_replace(chr(0),'',$row->DEPCODE)."</td>
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
							<th>รหัสพนักงาน</th>
							<th>ชื่อ-สกุล</th>
							<th>แผนก</th>
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
	
	function SETAUMPGetFormAE(){
		$CODE = $_POST["CODE"];
		$data = array(
			"CODE"=>""
			,"NAME"=>""
			,"DEPCODE"=>""
			,"MEMO1"=>""
		);
		
		if($CODE != ''){
			$sql = "
				select * from {$this->MAuth->getdb('OFFICER')}
				where CODE='".$CODE."'
			";
			$query = $this->db->query($sql);
			
			if($query->row()){
				foreach($query->result() as $row){
					$data['CODE'] = str_replace(chr(0),'',$row->CODE);
					$data['NAME'] = str_replace(chr(0),'',$row->NAME);
					$data['DEPCODE'] = str_replace(chr(0),'',$row->DEPCODE);
					$data['MEMO1'] = str_replace(chr(0),'',$row->MEMO1);
				}
			}
		}
		
		$sqlDep = "select DEPCODE,DEPNAME from {$this->MAuth->getdb('DEPMAST')}";
		$queryDep = $this->db->query($sqlDep);
		
		$OptionDEP = "<option value=''> เลือก</option>";
		if($queryDep->row()){
			foreach($queryDep->result() as $rowDep){
				$OptionDEP .= "<option value='".$rowDep->DEPCODE."' ".($data['DEPCODE']==$rowDep->DEPCODE?"selected":"").">".$rowDep->DEPCODE." ".$rowDep->DEPNAME."</option>";
			}			
		}
		
		$response = array();
		$response['html'] = "
			<div class='col-sm-12'>
				<div style='height:calc(100vh - 165px);overflow:auto;'>
					<div class='col-sm-4 col-sm-offset-4'>	
						<div class='form-group'>
							รหัสพนักงาน
							<input type='text' id='t2CODE' class='form-control input-sm' value='".$data['CODE']."' maxlength=8>
						</div>
					</div>
					
					<div class='col-sm-4 col-sm-offset-4'>	
						<div class='form-group'>
							ชื่อ-สกุล
							<input type='text' id='t2NAME' class='form-control input-sm' value='".$data['NAME']."' maxlength=60>
						</div>
					</div>
					
					<div class='col-sm-4 col-sm-offset-4'>	
						<div class='form-group'>
							แผนก
							<!-- input type='text' id='t2DEPCODE' class='form-control input-sm' value='".$data['DEPCODE']."' maxlength=60 -->
							<select id='t2DEPCODE' class='form-control input-sm' data-placeholder='แผนก'>{$OptionDEP}</select>
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
	
	function SETAUMPSave(){
		$arrs = array();
		$arrs['CODE'] 	= $_POST['CODE'];
		$arrs['NAME'] 	= $_POST['NAME'];
		$arrs['DEPCODE'] = $_POST['DEPCODE'];
		$arrs['MEMO1'] 	 = $_POST['MEMO1'];
		
		$arrs['action']		= $_POST['action'];
		
		$data = "";
		if($arrs['action'] == "add"){
			$data = "
				declare @isval int = isnull((select count(*) from {$this->MAuth->getdb('OFFICER')} where CODE='".$arrs['CODE']."'),0);
				
				if(@isval > 0)
				begin 
					rollback tran tsc;
					insert into #tempolary select 'N' as id,'ไม่บันทึก : มีข้อมูลรหัสพนักงาน ".$arrs['CODE']." อยู่แล้ว' as msg;
					return;
				end 
				else
				begin 
					insert into {$this->MAuth->getdb('OFFICER')} (CODE,NAME,DEPCODE,MEMO1)
					select '".$arrs['CODE']."','".$arrs['NAME']."','".$arrs['DEPCODE']."','".$arrs['MEMO1']."'
					
					insert into {$this->MAuth->getdb('hp_UserOperationLog')}(userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
					values ('".$this->sess["IDNo"]."','setup รหัสพนักงาน เพิ่ม','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
				end
			";
		}else{			
			$data = "
				update {$this->MAuth->getdb('OFFICER')}
				set NAME='".$arrs['NAME']."'
					,DEPCODE='".$arrs['DEPCODE']."'
					,MEMO1='".$arrs['MEMO1']."'
				where CODE='".$arrs['CODE']."'
				
				
				insert into {$this->MAuth->getdb('hp_UserOperationLog')}(userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
				values ('".$this->sess["IDNo"]."','setup รหัสพนักงาน แก้ไข','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
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
	
	public function SETAUMPDel(){
		$arrs = array();
		$arrs['CODE'] = (!isset($_REQUEST['CODE'])?'':$_REQUEST['CODE']);
		$arrs['NAME'] = (!isset($_REQUEST['NAME'])?'':$_REQUEST['NAME']);
		
		$sql = "
			if object_id('tempdb..#tempolary') is not null drop table #tempolary;
			create table #tempolary (id varchar(1),msg varchar(max));
			
			begin tran tsc
			begin try
				if exists(
					select * from {$this->MAuth->getdb('PASSWRD')} 
					where USERID='".$arrs['CODE']."'
				)
				begin 
					rollback tran tsc;
					insert into #tempolary select 'N' as id,'ผิดพลาด : รหัสพนักงานถูกนำไปใช้งานแล้ว ไม่สามารถลบได้' as msg;
					return;
				end else begin 
					delete {$this->MAuth->getdb('OFFICER')}
					where CODE='".$arrs['CODE']."'
				end 
				
				insert into {$this->MAuth->getdb('hp_UserOperationLog')}(userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
				values ('".$this->sess["IDNo"]."','setup รหัสพนักงาน ลบ','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
				
				insert into #tempolary select 'Y' as id,'สำเร็จ ลบรหัสพนักงาน ".$arrs['CODE']." :: ".$arrs['NAME']."  แล้ว' as msg;
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




















