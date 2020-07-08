<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/********************************************************
             ______@16/04/2020______
			 Pasakorn Boonded

********************************************************/
class ClearNullFromUpgrade extends MY_Controller {
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
		$html ="
			<div class='k_tab1' name='home' locat='{$this->sess['branch']}' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}' today='".$this->today('today')."' style='height:calc(100vh - 132px);overflow:auto;background-color:white;'>
				<div class='col-sm-12 col-xs-12' style='height:100%;overflow:auto;font-size:11pt;'>
					<div id='selectTB' class='row' style='height:90%;'>
						<div class='col-sm-12 col-xs-12' style='background-color:#1abc9c;border:5px solid 
						white;height:75px;text-align:center;font-size:12pt;color:white;font-weight:bold;'>
							<br>ClearDataNullFromUpgradeVersion<br>
						</div>
						<div class='col-sm-10 col-xs-10 col-sm-offset-1'><br>	
							<div class='col-sm-12 col-xs-12'>	
								<div class='form-group'>
									<div class='col-sm-12 col-xs-12'>	
										<div class='col-sm-4'>
											<div class='form-group'>
												Table<br>
												<select id='TABLENM' class='form-control input-sm' style='width:30%;'></select>
											</div>
										</div>
										<div class='col-sm-4'>
											<div class='form-group'>
												<br>
												<div style='text-align:center;'>ตรวจสอบข้อมูล Null</div>
											</div>
										</div>
										<div class='col-sm-2'>
											<div class='form-group'>
												<br>
												<input id='COUNTFIEDLS' value='0' style='background-color:black; color:red; text-align:center;' class='form-control input-sm checkvalue' readonly>
											</div>
										</div>
										<div class='col-sm-12 col-xs-12'>
											<br>
											<button id='btnclear' type='button' class='btn btn-info' style='width:100%;'>
												<span class='glyphicon glyphicon-pencil'><b>Clear</b></span>
											</button>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		";
		$html .="<script src='".base_url('public/js/SYS02/ClearNullFromUpgrade.js')."'></script>";
		echo ($html);
	}
	function CountFields(){
		$TABLENM = $_REQUEST['TABLENM'];
		
		$sql = "
			select count(A.NAME) as countFIELDS from HIC2SHORTL.dbo.sysobjects A
			left join HIC2SHORTL.dbo.syscolumns B on A.ID = B.ID
			left join HIC2SHORTL.dbo.systypes C on B.xtype = C.xtype 
			where A.name = '".$TABLENM."' and A.type = 'U'
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$response['count'] = $row->countFIELDS;
			}
		}else{
			$response['count'] = "0";
		}
		echo json_encode($response);
	}
	function ClearNullTable(){
		$TABLENM = $_REQUEST['TABLENM'];
		$tbnm = "";
		if($TABLENM !== ""){
			$tbnm = "A.name = '".$TABLENM."' and";
		}else{
			$tbnm = "";
		}
		$sql = "
			select A.NAME AS TBNAME,B.NAME AS COLM,C.Name As COLTYP from HIC2SHORTL.dbo.sysobjects A
			left join HIC2SHORTL.dbo.syscolumns B on A.ID = B.ID
			left join HIC2SHORTL.dbo.systypes C on B.xtype = C.xtype 
			where C.name in ('varchar') and ".$tbnm."
			A.type = 'U' order by A.NAME
		";
		//echo $sql; exit;
		$i = 0;
		$setnotnullvarchar = "";
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){$i++;
				$setnotnullvarchar .="
					update HIC2SHORTL.dbo.".$row->TBNAME." set ".$row->COLM." = '' 
					where ".$row->COLM." is null
				";
			}
		}
		$sql = "
			select A.NAME AS TBNAME,B.NAME AS COLM,C.Name As COLTYP from HIC2SHORTL.dbo.sysobjects A
			left join HIC2SHORTL.dbo.syscolumns B on A.ID = B.ID
			left join HIC2SHORTL.dbo.systypes C on B.xtype = C.xtype 
			where C.name in ('decimal') and ".$tbnm."
			A.type = 'U' order by A.NAME
		";
		//echo $sql; exit;
		$setnotnulldecimal = "";
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$setnotnulldecimal .="
					update HIC2SHORTL.dbo.".$row->TBNAME." set ".$row->COLM." = 0 
					where ".$row->COLM." is null
				";
			}
		}
		//echo $updatenotnull; exit;
		$sql = "
			if OBJECT_ID('temp..#UPPASR') is not null drop table #UPPASR
			create table #UPPASR (id varchar(1),msg varchar(max));
			begin tran updateproduct
			begin try
				".$setnotnullvarchar."
				".$setnotnulldecimal."
				commit tran updateproduct;
				insert into #UPPASR select 'Y' as id,'สำเร็จ : ปรับปรุงค่า Null แล้วครับ' as msg;
			end try
			begin catch
				rollback tran updateproduct;
				insert into #UPPASR select 'N' as id,'บันทึกข้อมูลไม่สำเร็จ : กรุณาติดต่อฝ่ายไอที' as msg;
				return;
			end catch
		";
		//echo $sql; exit;
		$this->db->query($sql);
		
		$sql = "select * from #UPPASR";
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$response['status'] = $row->id;
				$response['msg']    = $row->msg;
			}
		}else{
			$response['status'] = false;
			$response['msg']    = "ผิดพลาด";
		}
		echo json_encode($response);
	}
}