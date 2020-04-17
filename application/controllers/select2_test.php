<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/********************************************************
             ______@28/02/2020______
			 Pasakorn Boonded

********************************************************/
class select2_test extends MY_Controller {
	private $sess = array(); 
	
	function __construct(){
		parent::__construct();
		//Additional code which you want to run automatically in every function call 
		$sess = $this->session->userdata('cbjsess001');
		if(!$sess){ redirect(base_url("welcome/"),"_parent"); }else{
			foreach ($sess as $key => $value) {
                $this->sess[$key] = $value;
            }
		}
	}
	function CUSCOD(){
		$dataSearch = $_REQUEST['q'];
		
		$sql = "
			select CUSCOD from HIC3.dbo.CUSTMAST where CUSCOD like '%".$dataSearch."%'
		";
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$json[] = array(
					'id' => $row->CUSCOD,
					'text' => $row->CUSCOD
				);
			}	
		}
		echo json_encode($json);
	}
}