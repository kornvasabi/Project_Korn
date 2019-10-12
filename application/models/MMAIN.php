<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/********************************************************
             ______@06/09/2019______
            / / _ _   _ _     __ 
           / // __ \ / __ \ / __ \
       _ _/ // /_/ // / / // /_/ /
     /_ _ _/ \_ _ //_/ /_/ \__  /
                          _ _/ /
                         /___ /
********************************************************/
class MMAIN extends CI_Model {
	
	public function SETACTI(){
		//กิจกรรมการขาย select
		$sql = "select ACTICOD,'('+ACTICOD+') '+ACTIDES as ACTIDES from {$this->MAuth->getdb('SETACTI')}";
		$query = $this->db->query($sql);
		
		$opt = "";
		if($query->row()){
			foreach($query->result() as $row){
				$opt .= "<option value='".str_replace(chr(0),"",$row->ACTICOD)."'>".str_replace(chr(0),"",$row->ACTIDES)."</option>";
			}
		}
		
		return $opt;
	}
}