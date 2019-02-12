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
	
	function sysdt(){
		$sql = "select convert(varchar(8),getdate() ,112) as sysd,convert(varchar(5),getdate() ,108) as syst";
		$sysdt = $this->db->query($sql);
		$sysdt = $sysdt->row();
		$sysdt = $this->Convertdate(2,$sysdt->sysd).' '.$sysdt->syst;
		
		return $sysdt;
	}
	
	public function Convertdate($param,$date){
		// $param = 1 > to Database
		// $param = 2 > to User Interface
		if($date == ''){
			return '';
		}else{
			if($param == 1){
				$dd = substr($date, 0,2);
				$mm = substr($date, 3,2);
				$yy = substr($date, 6,4) - 543;
				return $yy.$mm.$dd;
			}else{
				$yy = substr($date, 0,4) + 543;
				$mm = substr($date, 4,2);
				$dd = substr($date, 6,2);
				return $dd."/".$mm."/".$yy;
			}
		}
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
