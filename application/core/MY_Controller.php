<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller {
	public $username = "";
	public function input($type,$id,$class,$attr,$value){
		if($type != ""){
			$type = " type='".$type."' ";
		}
		
		if($id != ""){
			$id = " id='".$id."' ";
		}
		
		if($class != ""){
			$size = sizeof($class);
			$data = "";
			for($i=0;$i<$size;$i++){
				if($data != ""){ $data.=" "; }
				$data.=$class[$i];
			}
			$class = " class='".$data."' ";
		}
		
		if($attr != ""){
			$size = sizeof($attr);
			$data = "";
			for($i=0;$i<$size;$i++){
				if($data != ""){ $data.=" "; }
				$data.=$attr[$i];
			}
			$attr = " class='".$data."' ";
		}
		
		if($value != ""){
			$value=" value='".$value."' ";
		}
		
		$input = "<input".$type.$id.$class.$attr.$value." >";
		
		return $input;
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
	
	public function param($val){
		$data = array();
		switch($val){
			case 'database': 
				$data = array(
					0=> array('HIC2SHORTL'),
					1=> array('HIINCOME','HN','FN'),
					2=> array('RJYN','HRJYN','FRJYN'),
					3=> array('TJHON'),
					4=> array('TJPAT','HTJPAT','FTJPAT'),
					5=> array('TJYL2556'),
					6=> array('TJYN','HTJYN','FTJYN'),
					7=> array('TJYN2004','HTJYN2004','FTJYN2004'),
				);
				break;
			default: 
				$data = array(); 
				break;
		}
		
		return $data;
	}
}