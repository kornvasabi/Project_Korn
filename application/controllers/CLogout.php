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
class CLogout extends MY_Controller {

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
	function index(){
		$this->session->unset_userdata('cbjsess001');
		redirect(base_url("CLogin/index"),"_parent");		
	}
	
	function lock(){
		$sess = $this->session->userdata('cbjsess001');
		
		$this->session->unset_userdata('cbjsess001');
		$sess_array = array(
			'employeeCode' => $sess['employeeCode'],
			'IDNo' => $sess['IDNo'],
			'USERID' => $sess['USERID'],
			'password' => $sess['password'],
			'name' => $sess['name'],
			'positionName' => $sess['positionName'],
			'corpName' => $sess['corpName'],
			'branch' => $sess['branch'],
			'lock' => 'yes',
			'db' => $sess['db']
		);
		$this->session->set_userdata('cbjsess001',$sess_array);
		
		$data["user"]	  = $sess["name"];
		$data["position"] = $sess["positionName"];
		$this->load->view('lobiLock',$data);
	}
	
	function unlockVertify(){
		$sess = $this->session->userdata('cbjsess001');
		/*
		$sess_array = array(
			'employeeCode' => $row->employeeCode,
			'IDNo' => $row->IDNo,
			'password' => $row->passwords,
			'name' => $row->titleName.$row->firstName.' '.$row->lastName,
			'positionName' => $row->positionName,
			'corpName' => $row->corpName,
		);
		*/
		
		if($sess["password"] == md5($_REQUEST["pass"])){
			//redirect(base_url("/welcome/test"),"_parent"); exit;
			$sess = $this->session->userdata('cbjsess001');
		
			$this->session->unset_userdata('cbjsess001');
			$sess_array = array(
				'employeeCode' => $sess['employeeCode'],
				'IDNo' => $sess['IDNo'],
				'USERID' => $sess['USERID'],
				'password' => $sess['password'],
				'name' => $sess['name'],
				'positionName' => $sess['positionName'],
				'corpName' => $sess['corpName'],
				'branch' => $sess['branch'],
				'lock' => 'no',
				'db' => $sess['db']
			);
			$this->session->set_userdata('cbjsess001',$sess_array);
			
			$response = array("html"=>"","status"=>true);
			echo json_encode($response);
		}else{
			$response = array("html"=>"ผิดพลาด รหัสผ่านไม่ถูกต้อง","status"=>false);			
			echo json_encode($response);
		}
	}	
}
