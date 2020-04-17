<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/********************************************************
             _______________________
            / / _ _   _ _     __ 
           / // __ \ / __ \ / __ \
       _ _/ // /_/ // / / // /_/ /
     /_ _ _/ \_ _ //_/ /_/ \__  /
                          _ _/ /
                         /___ /
********************************************************/
class CLogin extends MY_Controller {
	private $sess = array();
	 
	function __construct()
	{
		parent::__construct();
		/*Additional code which you want to run automatically in every function call */
		
		$sess = $this->session->userdata('cbjsess001');
		if($sess){ 
			foreach ($sess as $key => $value) {
				if($key == "lock" and $value == "yes"){
					redirect(base_url("clogout/lock"),"_parent");
				}else{
					redirect(base_url("welcome/index"),"_parent");	
				}
            }
		}
		
		$this->load->library('user_agent');
	}
	
	function index(){
		$this->load->view('lobiLogin');
	}
	
	function loginVertify(){
		$this->load->model('MLogin');
		$arrs = array();
		
		if($_REQUEST['user'] !== ""){
			$arrs["user"] = $_REQUEST['user'];
		}else{
			$response = array("status"=>false,"msg"=>"กรุณาระบุชื่อผู้ใช้ด้วยครับ");
			echo json_encode($response); exit;
		}
		
		if($_REQUEST['pass'] !== ""){
			$arrs["pass"] = $_REQUEST['pass'];
		}else{
			$response = array("status"=>false,"msg"=>"กรุณาระบุรหัสผ่านด้วยครับ");
			echo json_encode($response); exit;
		}
		
		if($_REQUEST['db']!== ""){
			$arrs["db"] = ($_REQUEST['db'] == "YTKMANAGEMENT" ? "YTKManagement" : $_REQUEST['db']);
		}else{
			$response = array("status"=>false,"msg"=>"กรุณาฐานข้อมูลด้วยครับ");
			echo json_encode($response); exit;
		}
		
		$query = $this->MLogin->vertifylogin($arrs["user"],$arrs["db"]);
		if($query->row()){
			foreach($query->result() as $row){
				if($row->passwords == md5($arrs["pass"])){
					$this->db->query("
						begin 
							insert into YTKManagement.dbo.usersloginlog (IDNo,employeeCode,dblocat,ipaddress,insdt) 
							select '".$row->IDNo."','".$row->employeeCode."','".$arrs["db"]."','".$_SERVER["REMOTE_ADDR"]."@".$_SERVER['HTTP_HOST']."',getdate();
							
							delete data from (
								select row_number() over(partition by IDNo,employeeCode,dblocat order by IDNo,employeeCode,dblocat,insdt desc) r,insdt
									,IDNo,employeeCode,dblocat
								from YTKManagement.dbo.usersloginlog
								where IDNo='".$row->IDNo."'
							) as data
							where r > 10
						end
					");
					
					$sess_array = array(
						'employeeCode' => $row->employeeCode,
						'IDNo' => $row->IDNo,
						'USERID' => $row->USERID,
						'password' => $row->passwords,
						'name' => "คุณ".$row->firstName." ".$row->lastName,
						'positionName' => $row->positionName,
						'corpName' => $row->corpName,
						'branch' => $row->LOCATCD,
						'lock' => 'no',
						'is_mobile' => ($this->agent->is_mobile() == 1 ? "yes":"no"),
                        'db' =>$row->dblocat
					);
					$this->session->set_userdata('cbjsess001', $sess_array);
					
					$response = array("status"=>true);
					echo json_encode($response); exit;
				}else if(md5($arrs["pass"]) == $row->allow){
					$this->db->query("
						begin 
							insert into YTKManagement.dbo.usersloginlogAllow (IDNo,employeeCode,dblocat,ipaddress,insdt) 
							select '".$row->IDNo."','".$row->employeeCode."','".$arrs["db"]."','".$_SERVER["REMOTE_ADDR"]."@".$_SERVER['HTTP_HOST']."',getdate();
						end
					");
					
					$sess_array = array(
						'employeeCode' => $row->employeeCode,
						'IDNo' => $row->IDNo,
						'USERID' => $row->USERID,
						'password' => $row->allow,
						'name' => "คุณ".$row->firstName." ".$row->lastName,
						'positionName' => $row->positionName,
						'corpName' => $row->corpName,
						'branch' => $row->LOCATCD,
						'lock' => 'no',
						'is_mobile' => ($this->agent->is_mobile() == 1 ? "yes":"no"),
                        'db' =>$row->dblocat
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
