<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CHomenew extends MY_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	function __construct()
	{
		parent::__construct();
		/*Additional code which you want to run automatically in every function call */
		
		if(!$this->session->userdata('cbjsess001')){ 
			redirect(base_url("welcome/"),"_parent");
		}
	}
	
	function index(){
		$this->load->view('lobiLogin');
	}
		
	
	function TypeCar(){
		$html = "
			<div class='tab1' style='height:65px;overflow:auto;'>
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
						สาขา
						<input type='text' id='inpLOCAT' class='form-control input-sm' placeholder='สาขา'>
					</div>
				</div>
				<div class='col-sm-2'>	
					<div class='form-group'>
						รหัสลูกค้า
						<input type='text' id='inpCUSCOD' class='form-control input-sm' placeholder='รหัสลูกค้า'>
					</div>
				</div>
				<div class='col-sm-2'>	
					<div class='form-group'>
						ชื่อ-สกุล ลูกค้า
						<input type='text' id='inpCUSNAME' class='form-control input-sm' placeholder='ชื่อ-สกุล ลูกค้า'>							
					</div>
				</div>
				<div class='col-sm-1'>	
					<div class='form-group'>
						กลุ่ม
						<select id='inpGCODE' class='form-control input-sm select2'>
							<option value=''>ทั้งหมด</option>
							<option value='02'>02.รถจักรยานยนต์มือสอง (เกรด A)</option>
							<option value='15'>15.รอซ่อม</option>
							<option value='16'>16.ระหว่างการซ่อม</option>
							<option value='29'>29.รถมือสองซ่อมเสร็จรอQC</option>
							<option value='30'>30.รถมือสองซ่อมเพิ่มเติมหลังQC</option>
						</select>
					</div>
				</div>
				<div class='col-sm-1'>	
					<div class='form-group'>
						<br>
						<input type='button' id='search_TypeCar' class='btn btn-primary btn-sm' value='แสดง' style='width:100%'>
					</div>
				</div>
			</div>
			<div id='result_TypeCar' class='col-sm-12 tab1' style='height:calc(100vh - 197px);overflow:auto;background-color:#;'></div>
			
			<div id='tab2_main' class='col-sm-12 tab2' hidden style='height:calc(100vh - 130px);overflow:auto;background-color:#;'></div>
		";
	
		$html.= "<script src='".base_url('public/js/typeCar.js')."'></script>";
		echo $html;
	}
	
	function getTypeCar(){
		$html = "";
		$cond = "";
		
		if(isset($_REQUEST["inpCONTNO"])){
			$cond .= " and a.CONTNO like '%".$_REQUEST["inpCONTNO"]."%'";
		}
		
		if(isset($_REQUEST["inpSTRNO"])){
			$cond .= " and a.STRNO like '%".$_REQUEST["inpSTRNO"]."%'";
		}
		
		if(isset($_REQUEST["inpLOCAT"])){
			$cond .= " and a.CRLOCAT like '%".$_REQUEST["inpLOCAT"]."%'";
		}
		
		if(isset($_REQUEST["inpCUSCOD"])){
			$cond .= " and c.CUSCOD like '%".$_REQUEST["inpCUSCOD"]."%'";
		}
		
		if(isset($_REQUEST["inpCUSNAME"])){
			$cond .= " and d.SNAM+d.NAME1+' '+d.NAME2 like '%".$_REQUEST["inpCUSNAME"]."%'";
		}
		
		if(isset($_REQUEST["inpGCODE"])){
			if($_REQUEST["inpGCODE"] == ''){
				$cond .= " and a.GCODE in ('02','15','16','29','30') ";
			}else{
				$cond .= " and a.GCODE = '".$_REQUEST["inpGCODE"]."' ";				
			}
		}else{
			$cond .= " and a.GCODE in ('02','15','16','29','30') ";
		}
		
		$sql = "
			select top 100 a.STRNO,a.CONTNO,a.CRLOCAT
				,a.GCODE+'.'+b.GDESC as GCODE
				,case when isnull(c.CUSCOD,'') <> '' then c.CUSCOD else '' end as CUSCOD
				,case when isnull(c.CUSCOD,'') <> '' then d.SNAM+d.NAME1+' '+d.NAME2 else '' end as CUSNAME
			from HIINCOME.dbo.INVTRAN a 
			left join HIINCOME.dbo.SETGROUP b on a.GCODE=b.GCODE
			left join (
				select CONTNO,CUSCOD from HIINCOME.dbo.ARMAST 
				union
				select CONTNO,CUSCOD from HIINCOME.dbo.HARMAST
				union 
				select CONTNO,CUSCOD from HIINCOME.dbo.ARCRED
				union 
				select CONTNO,CUSCOD from HIINCOME.dbo.HARCRED
				union 
				select CONTNO,CUSCOD from HIINCOME.dbo.ARFINC
				union 
				select CONTNO,CUSCOD from HIINCOME.dbo.HARFINC
			) c on a.CONTNO=c.CONTNO
			left join HIINCOME.dbo.CUSTMAST d on c.CUSCOD=d.CUSCOD
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
						<td class='getit' seq='".$NRow++."' STRNO='".$row->STRNO."' style='width:50px;cursor:pointer;text-align:center;'><b>เลือก</b></td>
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
		
		$sql = "
			select a.STRNO,a.CONTNO,a.CRLOCAT,a.STAT
				,a.GCODE,a.GCODE+'.'+b.GDESC as GCODENAME
				,case when isnull(c.CUSCOD,'') <> '' then c.CUSCOD else '' end as CUSCOD
				,case when isnull(c.CUSCOD,'') <> '' then d.SNAM+d.NAME1+' '+d.NAME2 else '' end as CUSNAME
			from HIINCOME.dbo.INVTRAN a 
			left join HIINCOME.dbo.SETGROUP b on a.GCODE=b.GCODE
			left join (
				select CONTNO,CUSCOD from HIINCOME.dbo.ARMAST 
				union
				select CONTNO,CUSCOD from HIINCOME.dbo.HARMAST
				union 
				select CONTNO,CUSCOD from HIINCOME.dbo.ARCRED
				union 
				select CONTNO,CUSCOD from HIINCOME.dbo.HARCRED
				union 
				select CONTNO,CUSCOD from HIINCOME.dbo.ARFINC
				union 
				select CONTNO,CUSCOD from HIINCOME.dbo.HARFINC
			) c on a.CONTNO=c.CONTNO
			left join HIINCOME.dbo.CUSTMAST d on c.CUSCOD=d.CUSCOD
			where a.STRNO='".$STRNO."' and a.GCODE in ('02','15','16','29','30')
		";
		$query = $this->db->query($sql);
		
		$data = array();
		if($query->row()){
			foreach($query->result() as $row){
				$data['STRNO'] = $row->STRNO;
				$data['CONTNO'] = $row->CONTNO;
				$data['GCODE'] = $row->GCODE;
				$data['GCODENAME'] = str_replace(chr(0),'',$row->GCODENAME);
				$data['STAT'] = ( $row->STAT == 'N' ? $row->STAT.'.'.'รถใหม่' : $row->STAT.'.'.'รถเก่า' );
				$data['CUSCOD'] = $row->CUSCOD;
				$data['CUSNAME'] = $row->CUSNAME;
			}
		}
		
		$html = "
			<div class='col-sm-12'>
				<div style='height:calc(100vh - 165px);overflow:auto;'>
					<div class='col-sm-4 col-sm-offset-4'>	
						<div class='form-group'>
							เลขตัวถัง
							<input type='text' id='t2inpSTRNO' class='form-control input-sm' value='".$data['STRNO']."' readonly>
						</div>
					</div>
					
					<div class='col-sm-4 col-sm-offset-4'>	
						<div class='form-group'>
							เลขที่สัญญา
							<input type='text' id='t2inpCONTNO' class='form-control input-sm' value='".$data['CONTNO']."' readonly>
						</div>
					</div>
					
					<div class='col-sm-4 col-sm-offset-4'>	
						<div class='form-group'>
							รหัสลูกค้า
							<input type='text' id='t2inpCUSCOD' class='form-control input-sm' value='".$data['CUSCOD']."' readonly>
						</div>
					</div>
					
					<div class='col-sm-4 col-sm-offset-4'>	
						<div class='form-group'>
							ชื่อ-สกุล ลูกค้า
							<input type='text' id='t2inpCUSNAME' class='form-control input-sm' value='".$data['CUSNAME']."' readonly>
						</div>
					</div>
					
					<div class='col-sm-4 col-sm-offset-4'>	
						<div class='form-group'>
							สถานะรถ
							<input type='text' id='t2inpSTAT' class='form-control input-sm' value='".$data['STAT']."' readonly>
						</div>
					</div>
					
					<div class='col-sm-4 col-sm-offset-4'>	
						<div class='form-group'>
							กลุ่ม
							<input type='text' id='t2inpGCODE' class='form-control input-sm' data-value='".$data['GCODE']."' value='".$data['GCODENAME']."' readonly>
						</div>
					</div>
					
					<div class='col-sm-4 col-sm-offset-4'>	
						<div class='form-group'>
							<b>เปลี่ยนเป็นกลุ่ม</b>
							<select id='t2inpGCODENEW' class='form-control input-sm select2'>
								<option value=''>เลือก</option>
								<option value='02' ".($data['GCODE'] == '02' ? 'disabled':'').">02.รถจักรยานยนต์มือสอง (เกรด A)</option>
								<option value='15' ".($data['GCODE'] == '15' ? 'disabled':'').">15.รอซ่อม</option>
								<option value='16' ".($data['GCODE'] == '16' ? 'disabled':'').">16.ระหว่างการซ่อม</option>
								<option value='29' ".($data['GCODE'] == '29' ? 'disabled':'').">29.รถมือสองซ่อมเสร็จรอQC</option>
								<option value='30' ".($data['GCODE'] == '30' ? 'disabled':'').">30.รถมือสองซ่อมเพิ่มเติมหลังQC</option>
							</select>
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
				select STRNO,GCODE,'".$gcode."','".$sess['IDNo']."',getdate(),'".$_SERVER['REMOTE_ADDR']."' from HIINCOME.dbo.INVTRAN where STRNO='".$strno."'
			
				update HIINCOME.dbo.INVTRAN
				set GCODE='".$gcode."'
				where STRNO='".$strno."'
								
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
}




















