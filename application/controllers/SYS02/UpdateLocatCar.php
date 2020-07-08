<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/********************************************************
             ______@07/04/2020______
			 Pasakorn Boonded

********************************************************/
class UpdateLocatCar extends MY_Controller {
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
							<br>ปรับปรุงสาขาปัจจุบันของรถ<br>
						</div>
						<div class='col-sm-10 col-xs-10 col-sm-offset-1'><br>	
							<div class='col-sm-12 col-xs-12'>	
								<div class='form-group'>
									<div class='col-sm-12 col-xs-12'>	
										<div class='col-sm-6'>
											<div class='form-group'>
												ณ วันที่
												<input type='text' id='DATE' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' value='".$this->today('today')."' styl='font-size:10.5pt;'>
											</div>
										</div>
										<div class='col-sm-6'>
											<div class='form-group'>
												เลขตัวถัง
												<select id='STRNO' class='form-control input-sm'></select>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class='col-sm-12 col-xs-12'>
								<br>
								<button id='btnupdate' type='button' class='btn btn-info' style='width:100%;'>
									<span class='glyphicon glyphicon-pencil'><b>ปรับปรุง</b></span>
								</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		";
		$html .="<script src='".base_url('public/js/SYS02/UpdateLocatCar.js')."'></script>";
		echo ($html);
	}
	function UpdateLocat(){
		$STRNO = $_REQUEST['STRNO'];

		if($STRNO == ""){
			$response['error'] = true;
			$response['msg']   = "กรุณาเลือกเลขถังก่อนครับ";
			echo json_encode($response); exit;	
		}
		$sql = "
			select RECVNO,STRNO from {$this->MAuth->getdb('INVTRAN')} 
			where STRNO like '%".$STRNO."%'
		";
		//echo $sql; exit;
		$updatelocat = "";
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$updatelocat .= "
					update {$this->MAuth->getdb('INVTRAN')} set RVLOCAT = (
						select LOCAT from {$this->MAuth->getdb('INVINVO')} 
						where RECVNO = '".$row->RECVNO."'
					) where STRNO = '".$row->STRNO."'
				";
			}
		}
		$sql = "
			if OBJECT_ID('temp..#uplocat') is not null drop table #uplocat
			create table #uplocat (id varchar(1),msg varchar(max));
			begin tran updatelocat
			begin try
				".$updatelocat."
				insert into #uplocat select 'Y' as id,'สำเร็จ : ปรับปรุงที่อยู่ของรถสาขาปัจจุบันแล้วครับ' as msg;
				commit tran updatelocat;
			end try
			begin catch
				rollback tran updatelocat;
				insert into #uplocat select 'N' as id,'บันทึกข้อมูลไม่สำเร็จ : กรุณาติดต่อฝ่ายไอที' as msg;
				return;
			end catch
		";
		//echo $sql; exit;
		$this->db->query($sql);
		$sql3 = "select * from #uplocat";
		$query3 = $this->db->query($sql3);
		if($query3->row()){
			foreach($query3->result() as $row3){
				$response['status'] = $row3->id;
				$response['msg']    = $row3->msg;
			}
		}else{
			$response['status'] = false;
			$response['msg']    = "ผิดพลาด";
		}
		echo json_encode($response);
	}
}