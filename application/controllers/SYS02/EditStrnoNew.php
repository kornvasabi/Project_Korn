<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/********************************************************
             ______@07/04/2020______
			 Pasakorn Boonded

********************************************************/
class EditStrnoNew extends MY_Controller {
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
					<div class='row' style='height:90%;'>
						<div class='col-sm-12 col-xs-12' style='background-color:#1abc9c;border:5px solid 
						white;height:75px;text-align:center;font-size:12pt;color:white;font-weight:bold;'>
							<br>เปลี่ยนหมายเลขถังรถที่ขายแล้ว<br>
						</div>
						<div class='col-sm-10 col-xs-10 col-sm-offset-1'><br>	
							<div class='col-sm-12 col-xs-12'>	
								<div class='form-group'>
									<div class='col-sm-12 col-xs-12'>	
										<div class='col-sm-4'>
											<div class='form-group'>
												เลขตัวถัง
												<select id='STRNOOLD' class='form-control input-sm'></select>
											</div>
										</div>
										<div class='col-sm-4'>
											<div class='form-group'>
												เลขตัวถังใหม่
												<input type='text' id='STRNONEW' class='form-control input-sm'>
											</div>
										</div>
										<div class='col-sm-4'>
											<div class='form-group'>
												ประเภทการขาย
												<input type='text' id='TSALE' class='form-control input-sm'>
											</div>
										</div>
										<div class='col-sm-4'>
											<div class='form-group'>
												เลขที่สัญญา
												<input type='text' id='CONTNO' class='form-control input-sm'>
											</div>
										</div>
										<div class='col-sm-4'>
											<div class='form-group'>
												รหัสลูกค้า
												<input type='text' id='CUSCOD' class='form-control input-sm'>
											</div>
										</div>
										<div class='col-sm-4'>
											<div class='form-group'>
												เลขใบกำกับภาษี
												<input type='text' id='TAXNO' class='form-control input-sm'>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class='col-sm-6 col-xs-6'>
								<br>
								<button id='btnsave' type='button' class='btn btn-info' style='width:100%;'>
									<span class='fa fa-save'><b>บันทึก</b></span>
								</button>
							</div>
							<div class='col-sm-6 col-xs-6'>
								<br>
								<button id='btnclear' type='button' class='btn btn-danger' style='width:100%;'>
									<span class='fa fa-remove'><b>ยกเลิก</b></span>
								</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		";
		$html .="<script src='".base_url('public/js/SYS02/EditStrnoNew.js')."'></script>";
		echo ($html);
	}
	function getstrnodetails(){
		$STRNO = $_REQUEST['STRNO'];

		$sql = "
			select LOCAT,CONTNO,STRNO,TSALE,TAXNO,CUSCOD from {$this->MAuth->getdb('ARMAST')}
			where STRNO = '".$STRNO."'
		";
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$response['TSALE']   = str_replace(chr(0),'',$row->TSALE);
				$response['CONTNO']  = str_replace(chr(0),'',$row->CONTNO);
				$response['CUSCOD']  = str_replace(chr(0),'',$row->CUSCOD);
				$response['TAXNO']   = str_replace(chr(0),'',$row->TAXNO);
			}
		}else{
			$response['TSALE']   = "";
			$response['CONTNO']  = "";
			$response['CUSCOD']  = "";
			$response['TAXNO']   = "";
		}
		echo json_encode($response);
	}
	//$arrs['sircod'] = (!isset($_REQUEST['sircod'])?'':$_REQUEST['sircod']);
	function SaveEditCarDetail(){
		$STRNOOLD   = $_REQUEST['STRNOOLD'];
		$STRNONEW   = $_REQUEST['STRNONEW'];
		$CONTNO     = $_REQUEST['CONTNO'];
		$CUSCOD     = $_REQUEST['CUSCOD'];
		if($STRNONEW == ''){
			$response["error"] = true;
			$response["msg"]   = 'กรุณากรอกเลขตัวถังก่อนครับ';	
			echo json_encode($response); exit;
		}

		$sql = "
			if OBJECT_ID('temp..#editST') is not null drop table #editST;
			create table #editST (id varchar(1),msg varchar(max));
			begin tran editstrno
			begin try
				declare @invtran int =(select COUNT(*) from {$this->MAuth->getdb('INVTRAN')} 
				where STRNO = '".$STRNONEW."');
				declare @armast  int =(select COUNT(*) from {$this->MAuth->getdb('ARMAST')} 
				where STRNO = '".$STRNONEW."');
				if(@invtran = 0 and @armast = 0)
					begin
						update {$this->MAuth->getdb('INVTRAN')} set STRNO = '".$STRNONEW."' 
						where STRNO = '".$STRNOOLD."'
						update {$this->MAuth->getdb('ARMAST')} set STRNO = '".$STRNONEW."' 
						where STRNO = '".$STRNOOLD."' and CONTNO = '".$CONTNO."' and CUSCOD = '".$CUSCOD."'
						insert into #editST select 'Y' as id,'สำเร็จ : แก้ไขข้อมูลเรียบร้อยแล้วครับ' as msg;
						commit tran editstrno
					end
				else
					begin
						rollback tran editstrno
						insert into #editST select 'N' as id,'ไม่สำเร็จ : มีเลขตัวถังรถนี้อยู่แล้ว'
						return;
					end
			end try
			begin catch
				rollback tran editstrno
				insert into #editST select 'N' as id,'บันทึกข้อมูลไม่สำเร็จ : กรุณาติดต่อฝ่ายไอที' as msg
				return;
			end catch
		";
		//echo $sql; exit;
		$this->db->query($sql);
		$sql = "
			select * from #editST
		";
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$response['status'] = $row->id;
				$response['msg']    = $row->msg;
			}
		}
		echo json_encode($response);
	}
}