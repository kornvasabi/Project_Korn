<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/********************************************************
             ______@18/04/2020______
			 Pasakorn Boonded

********************************************************/
class UpdateProductAccessory extends MY_Controller {
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
							<br>ปรับปรุงสินค้าคงเหลืออุปกรณ์เสริม<br>
						</div>
						<div class='col-sm-10 col-xs-10 col-sm-offset-1'><br>	
							<div class='col-sm-12 col-xs-12'>	
								<div class='form-group'>
									<div class='col-sm-12 col-xs-12'>	
										<div class='col-sm-6'>
											<div class='form-group'>
												รหัสอุปกรณ์จาก
												<select id='OPTCODE_F' class='form-control input-sm'></select>
											</div>
										</div>
										<div class='col-sm-6'>
											<div class='form-group'>
												ถึง
												<select id='OPTCODE_T' class='form-control input-sm'></select>
											</div>
										</div>
										<div class='col-sm-6'>
											<div class='form-group'>
												สาขา
												<select id='LOCAT' class='form-control input-sm'></select>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class='col-sm-12 col-xs-12'>
								<br>
								<button id='btnupdate' type='button' class='btn btn-info' style='width:100%;'>
									<span class='glyphicon glyphicon-pencil'><b>ปรับปรุงสินค้า</b></span>
								</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		";
		$html .="<script src='".base_url('public/js/SYS02/UpdateProductAccessory.js')."'></script>";
		echo ($html);
	}
	function UpdateProduct(){
		$OPTCODE_F = $_REQUEST['OPTCODE_F'];
		$OPTCODE_T = $_REQUEST['OPTCODE_T'];
		$LOCAT     = $_REQUEST['LOCAT'];
		
		if($OPTCODE_F == "" or $OPTCODE_T == ""){
			$response['error'] = true;
			$response['msg'] = "กรุณาเลือกรหัสอุปกรณ์ก่อนครับ";
			echo json_encode($response); exit;
		}
		if($LOCAT == ""){
			$response['error'] = true;
			$response['msg'] = "กรุณาเลือกรหัสสาขาก่อนครับ";
			echo json_encode($response); exit;
		}
		
		$sql = "	
			select OPTCODE,LOCAT,OPTNAME,UNITCST from {$this->MAuth->getdb('OPTMAST')} 
			where OPTCODE between '".str_replace(chr(0),'',$OPTCODE_F)."' 
			and '".str_replace(chr(0),'',$OPTCODE_T)."' and LOCAT = '".str_replace(chr(0),'',$LOCAT)."'
		";
		//echo $sql; exit;
		$updateproduct = "";
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$sql2 = "
					select sum(RV)-sum(PV) as ONHAND from (
						select A.RECVNO,B.RECVDT,A.QTY as RV,0 as PV from {$this->MAuth->getdb('OPTTRAN')} A
						left join {$this->MAuth->getdb('OPTINV')} B on A.RECVNO = B.RECVNO
						where A.OPTCODE = '".str_replace(chr(0),'',$row->OPTCODE)."' and A.RVLOCAT = '".str_replace(chr(0),'',$row->LOCAT)."'
						union
						select A.CONTNO as RECVNO,A.SDATE as RECVDT,0 as RV,A.QTY as PV 
						from {$this->MAuth->getdb('ARINOPT')} A
						where A.OPTCODE = '".str_replace(chr(0),'',$row->OPTCODE)."' and A.LOCAT = '".str_replace(chr(0),'',$row->LOCAT)."'
					)a
				";
				//echo $sql2;
				$query2 = $this->db->query($sql2);
				if($query2->row()){
					foreach($query2->result() as $row2){
						$onhand = str_replace(chr(0),'',$row2->ONHAND);
						$updateproduct .="
							update {$this->MAuth->getdb('OPTMAST')} 
							set ONHAND = ".($onhand == "" ? 0 : $onhand)." 
							where OPTCODE = '".str_replace(chr(0),'',$row->OPTCODE)."' 
							and LOCAT = '".str_replace(chr(0),'',$row->LOCAT)."'
						";
					}
				}
			}
		}
		//echo $updateproduct; exit;
		$sql = "
			if OBJECT_ID('temp..#UPA') is not null drop table #UPA
			create table #UPA (id varchar(1),msg varchar(max));
			begin tran updateproduct
			begin try
				".$updateproduct."
				insert into #UPA select 'Y' as id,'สำเร็จ : ปรับปรุงสินค้าคงเหลืออุปกรณ์เสริมแล้วครับ' as msg;
				commit tran updateproduct;
			end try
			begin catch
				rollback tran updateproduct;
				insert into #UPA select 'N' as id,'ปรับข้อมูลไม่สำเร็จ : กรุณาติดต่อฝ่ายไอที' as msg;
				return;
			end catch
		";
		//echo $sql; exit;
		$this->db->query($sql);
		$sql = "select * from #UPA";
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