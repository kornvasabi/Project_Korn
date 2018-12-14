<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MDATA extends CI_Model {
	
	function loadLocat(){
		$sess = $this->session->userdata('cbjsess001');
		
		$sql = "select LOCATCD from {$sess['db']}.dbo.INVLOCAT order by LOCATCD";
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$html .= "<option value='".$row->LOCATCD."'>".$row->LOCATCD."</option>";
			}
		}else{
			$html .= "<option value=''>-</option>";		
		}
		
		return $html;
	}
	
	/*
	function loadLocat(){
		$sess = $this->session->userdata('cbjsess001');
		
		$sql = "select LOCATCD from {$sess['db']}.dbo.INVLOCAT order by LOCATCD";
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$html .= "<option value='".$row->LOCATCD."'>".$row->LOCATCD."</option>";
			}
		}else{
			$html .= "<option value=''>-</option>";		
		}
		
		return $html;
	}
	*/
}
