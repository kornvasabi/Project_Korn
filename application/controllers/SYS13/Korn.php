<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/********************************************************
             _______________________
            pasakorn boonded
********************************************************/
class Korn extends MY_Controller {
	private $sess = array();
	
	function __construct()
	{
		parent::__construct();
		/*Additional code which you want to run automatically in every function call */
		
		$sess = $this->session->userdata('cbjsess001');
		if(!$sess){ redirect(base_url("welcome/index"),"_parent"); }else{
			foreach ($sess as $key => $value) {
				if($key == "lock" and $value == "yes"){
					redirect(base_url("clogout/lock"),"_parent");
				}
				
                $this->sess[$key] = $value;
            }
		}
	}
	function index(){
		$claim = $this->MLogin->getclaim(uri_string());
		if($claim['m_access'] != "T"){ echo "<div align='center' style='color:red;font-size:16pt;width:100%;'>ขออภัย คุณยังไม่มีสิทธิเข้าใช้งานหน้านี้ครับ</div>"; exit; }
		$html = "
			<div class='k_tab1' name='home' locat='{$this->sess['branch']}' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}' today='".$this->today('today')."' style='height:calc(100vh - 132px);overflow:auto;background-color:white;'>
				<div class='col-sm-12 col-xs-12' style='height:100%;overflow:auto;font-size:11pt;'>					
					<div class='row' style='height:90%;'>
						<div class='col-sm-12 col-xs-12' style='background-color:#45b39d;border:5px solid white;height:75px;text-align:center;font-size:12pt;color:white;font-weight:bold;'>	
							<br>รายงานลูกหนี้หยุด Vat<br>
						</div>
						<div class='col-sm-10 col-xs-10 col-sm-offset-1 text-primary'>	
							<br>
							<div class='col-sm-4'>	
								<div class='form-group'>
									รหัสสาขา
									<select id='CUSCOD' class='form-control input-sm'></select>
								</div>
							</div>
							<div class='col-sm-12 col-xs-12'>
								<br>
								<button id='btnReportVat' type='button' class='btn btn-info btn-outline btn-block' style='width:100%'><span class='fa fa-folder-open'><b>แสดง</b></span></button>
							</div><br>
						</div>
					</div>
				</div>
			</div>
		";
		$html .="<script src='".base_url('public/js/SYS13/Korn.js')."'></script>";
		echo $html;
	}
	function testloop(){
		$CUSCOD = $_REQUEST['CUSCOD'];
		
		$sql = "
			select CUSCOD from HIC3.dbo.CUSTMAST order by CUSCOD
		";
		$i = 0;
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){$i++;
				$sql2 = "
					update HIC3.dbo.CUSTMAST set BOSSNM = '".$row->CUSCOD."' where CUSCOD = '".$row->CUSCOD."'
				";	
				echo $sql; exit;
				$this->db->query($sql2);
				$korn['status'] = "Y";
			}		
		}else{
			$korn['status'] = "N";
		}
		echo json_encode($korn);
	}
}