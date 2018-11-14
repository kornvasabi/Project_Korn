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
}