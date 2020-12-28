<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/********************************************************
             ______@26/10/2020______
            pasakorn boonded
********************************************************/
class CloseAccountLocat extends MY_Controller {
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
		//echo "เมนูยังไม่เสร็จ กำลังพัฒนาต่อครับ"; exit;
		$html = "
			<div class='tab1' name='home' locat='{$this->sess['branch']}' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}' style='height:calc(100vh - 132px);overflow:auto;background-color:white;'>
				<div class='col-sm-12' style='overflow:auto;'>					
					<div class='row'>
						<div class=' col-sm-6 col-sm-offset-3'>	
							<div class='col-sm-12'>
								<div class='form-group'>
									รหัสสาขา
									<select id='add_locat' class='form-control input-sm'>
									</select>
								</div>	
							</div>
							<div class='col-sm-12'>
								<div class='form-group'>
									ชื่อสาขา
									<input type='text' id='add_locatnm' class='form-control input-sm' disabled>
								</div>	
							</div>
							<div class='col-sm-12'>
								<div class='form-group'>
									ปิดบัญชีถึงวันที่
									<input type='text' id='add_closedt' class='form-control input-sm' placeholder='วันที่ทำสัญญา' data-provide='datepicker' data-date-language='th-th' value='".$this->today('today')."'>
								</div>	
							</div>
							<div class='col-sm-12'>
								<div class='form-group'>
									<br>
									<input type='button' id='btn_save' class='btn btn-sm btn-primary right' style='width:100%;' value='บันทึก' >
								</div>	
							</div>
						</div>
					</div>
				</div>
			</div>
		";
		$html.= "<script src='".base_url('public/js/SYS06/CloseAccountLocat.js')."'></script>";
		echo $html;
	}
	function Save(){
		$locat   = $_REQUEST["locat"];
		$closedt = $this->Convertdate(1,$_REQUEST["closedt"]);
		
		$sql = "
			if OBJECT_ID('tempdb..#tempsave') is not null drop table #tempsave;
			create table #tempsave (id varchar(1),msg varchar(max));
			begin tran savecloseacc
			begin try
				begin
					declare @locat int = (
						select COUNT(*) from {$this->MAuth->getdb('INVLOCAT')} 
						where LOCATCD = '".$locat."'
					)
					if (@locat > 0)
					begin
						update {$this->MAuth->getdb('INVLOCAT')} 
							set CLOSEDT = '".$closedt."' 
						where LOCATCD = '".$locat."'
						
						insert into #tempsave select 'Y' as id,'สำเร็จ บันทึกข้อมูลเรียบร้อยแล้ว' as msg;
						commit tran savecloseacc;	
					end
					else
					begin
						rollback tran savecloseacc;
						insert into #tempsave select 'E' as id,'บันทึกข้อมูลไม่สำเร็จ : กรุณาตรวจสอบข้อมูลอีกครั้ง' as msg;
						return;
					end
				end
			end try
			begin catch
				rollback tran savecloseacc;
				insert into #tempsave select 'E' as id,'บันทึกข้อมูลไม่สำเร็จ : กรุณาติดต่อฝ่ายไอที' as msg;
				return;
			end catch
		";
		//echo $sql; exit;
		$this->db->query($sql);
		
		$sql = "select * from #tempsave";
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$response["error"] = $row->id;
				$response["msg"]   = $row->msg;
			}
		}else{
			$response["error"] = "error";
			$response["msg"]   = "ผิดพลาด กรุณาติดต่อฝ่ายไอที";
		}
		echo json_encode($response); 
	}
}




















