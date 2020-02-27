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
	
	//public function SETACTI(){
	public function Option_get_acti($selected){	
		//กิจกรรมการขาย select
		$sql = "
			select ACTICOD,'('+ACTICOD+') '+ACTIDES as ACTIDES from {$this->MAuth->getdb('SETACTI')} 
			order by ACTICOD
		";
		$query = $this->db->query($sql);
		
		$opt = "";
		if($query->row()){
			foreach($query->result() as $row){
				$acticod = str_replace(chr(0),"",$row->ACTICOD);
				$actides = str_replace(chr(0),"",$row->ACTIDES);
				
				$opt .= "
					<option value='{$acticod}' ".(in_array($acticod,$selected) ? "selected":"").">
						{$actides}
					</option>
				";
			}
		}
		
		return $opt;
	}
	
	public function Option_get_type($selected){
		$opt = "";
		if(isset($selected[0])){
			$arrs = array(
				$selected[0] => $selected[0],
				$selected[0] => $selected[0]
			);
			foreach($arrs as $key => $val){
				$opt .= "
					<option value='{$key}' ".(in_array($key,$selected) ? "selected":"").">
						{$val}
					</option>
				";
			}
		}
		
		return $opt;
	}
	
	public function Option_get_model($selected){
		$opt = "";
		if(isset($selected[0])){
			$arrs = array(
				$selected[0] => $selected[0],
				$selected[0] => $selected[0]
			);
			foreach($arrs as $key => $val){
				$opt .= "
					<option value='{$key}' ".(in_array($key,$selected) ? "selected":"").">
						{$val}
					</option>
				";
			}
		}
		
		return $opt;
	}
	
	public function Option_get_baab($selected){
		$opt = "";
		if(isset($selected["model"])){
			$sql = "
				select BAABCOD from {$this->MAuth->getdb('SETBAAB')}
				where MODELCOD='{$selected["model"]}'
				order by BAABCOD
			";
			//echo $sql; exit;
			$query = $this->db->query($sql);
			
			if($query->row()){
				foreach($query->result() as $row){
					$baabcod = str_replace(chr(0),"",$row->BAABCOD);				
					
					$opt .= "
						<option value='{$baabcod}' ".(in_array($baabcod,$selected["baab"]) ? "selected":"").">
							{$baabcod}
						</option>
					";
				}
			}
		}
		
		return $opt;
	}
	
	public function Option_get_color($selected,$model,$baab){
		//echo implode("','",$selected); exit;
		if(!is_array($model)){ $model = array('๛',$model); }
		if(!is_array($baab)){ $baab = array('๛',$baab); }
		
		$sql = "
			select COLORCOD from {$this->MAuth->getdb('JD_SETCOLOR')}
			where MODELCOD in ('".implode("','",$model)."') and BAABCOD in ('".implode("','",$baab)."') 
			order by COLORCOD
		";
		$query = $this->db->query($sql);
		
		$opt = "";
		if($query->row()){
			foreach($query->result() as $row){
				$colorcod = str_replace(chr(0),"",$row->COLORCOD);				
				
				$opt .= "
					<option value='{$colorcod}' ".(in_array($colorcod,$selected) ? "selected":"").">
						{$colorcod}
					</option>
				";
			}
		}
		
		return $opt;
	}
	
	public function Option_get_stat($selected){
		$arrs = array(
			"N" => "รถใหม่",
			"O" => "รถเก่า"
		);
		
		$opt = "";
		foreach($arrs as $key => $val){
			$opt .= "
				<option value='{$key}' ".(in_array($key,$selected) ? "selected":"").">
					{$val}
				</option>
			";
		}
		
		return $opt;
	}
	
	public function Option_get_locat($selected){	
		//กิจกรรมการขาย select
		$sql = "
			select LOCATCD,LOCATNM from {$this->MAuth->getdb('INVLOCAT')} 
			where LOCATCD<>'TRANS'
			order by LOCATCD
		";
		$query = $this->db->query($sql);
		
		$opt = "";
		if($query->row()){
			foreach($query->result() as $row){
				$locatcd = str_replace(chr(0),"",$row->LOCATCD);
				$locatnm = str_replace(chr(0),"",$row->LOCATNM);
				
				$opt .= "
					<option value='{$locatcd}' title='{$locatnm}' ".(in_array($locatcd,$selected) ? "selected":"").">
						{$locatcd}
					</option>
				";
			}
		}
		
		return $opt;
	}
	
	public function Option_get_groupcode($selected){
		$opt = "";
		if(isset($selected)){
			$sql = "
				select GCODE,'('+GCODE+') '+GDESC as GDESC from {$this->MAuth->getdb('SETGROUP')} 
				order by GCODE
			";
			//echo $sql; exit;
			$query = $this->db->query($sql);
			
			if($query->row()){
				foreach($query->result() as $row){
					$gcode = str_replace(chr(0),"",$row->GCODE);
					$gdesc = str_replace(chr(0),"",$row->GDESC);
					
					$opt .= "
						<option value='{$gcode}' ".(in_array('G'.$gcode,$selected) ? "selected":"").">
							{$gdesc}
						</option>
					";
				}
			}
		}
		
		return $opt;
	}
	
	public function locat_claim($locat){
		$response = array();
		
		$sql = "
			select * from {$this->MAuth->getdb('INVLOCAT')}
			where LOCATCD='{$locat}'
		";
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$response["error"]  = false;
				$response["FLSALE"] = $row->FLSALE;
				$response["msg"] 	= "";
			}
		}else{
			$response["error"]  = true;
			$response["FLSALE"] = "E";
			$response["msg"]    = "ไม่พบข้อมูลสาขา ".$locat;
		}
		
		return $response;
	}
	
}
















