<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/********************************************************
             _______________________
            Pasakorn
********************************************************/
class Test extends MY_Controller {
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
}
