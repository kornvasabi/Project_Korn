<?php
defined('BASEPATH') OR exit('No direct script access allowed');
ini_set('max_execution_time',-1);
require_once './vendor/autoload.php';
date_default_timezone_set('Asia/Bangkok');

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
	
	public function generateData($data,$typ){
		$response = array();
		if($typ == "encode"){
			$datasize = sizeof($data);
			for($i=0;$i<$datasize;$i++){
				$response[$i] = $this->_base64_encryptPOST($data[$i]);
			}
		}else if($typ == "decode"){
			$datasize = sizeof($data);
			for($i=0;$i<$datasize;$i++){
				$response[$i] = $this->_base64_decryptPOST($data[$i]);
			}
		}
		
		return $response;
	}
	
	private function _base64_encryptPOST($str,$passw=null){
		$r='';
		$md=$passw?substr(md5($passw),0,16):'';
		$str=base64_encode($md.$str);
		$abc='A0BC1DEF2GHIJ9KLMNO8PQRSTU7VWXYZabc6defghijkl5mnopqrstuv4wxyz3';
		$a=str_split(''.$abc);
		$b=strrev(''.$abc);
		if($passw){
			$b=$this->_mixing_passw($b,$passw);
		}else{
			$r=rand(10,65);
			$b=mb_substr($b,$r).mb_substr($b,0,$r);
		}
		$s='';
		$b=str_split($b);
		$str=str_split($str);
		$lens=count($str);
		$lena=count($a);
		for($i=0;$i<$lens;$i++){
			for($j=0;$j<$lena;$j++){
				if($str[$i]==$a[$j]){
					$s.=$b[$j];
				}
			};
		};
		return $s.$r;
	}
	
	private function _base64_decryptPOST($str,$passw=null){
		$abc='A0BC1DEF2GHIJ9KLMNO8PQRSTU7VWXYZabc6defghijkl5mnopqrstuv4wxyz3';
		$a=str_split(''.$abc);
		$b=strrev(''.$abc);
		if($passw){
			$b=$this->_mixing_passw($b,$passw);
		}else{
			$r=mb_substr($str,-2);
			$str=mb_substr($str,0,-2);
			$b=mb_substr($b,$r).mb_substr($b,0,$r);
		}
		$s='';
		$b=str_split($b);
		$str=str_split($str);
		$lens=count($str);
		$lenb=count($b);
		for($i=0;$i<$lens;$i++){
			for($j=0;$j<$lenb;$j++){
				if($str[$i]==$b[$j]){
					$s.=$a[$j];
				}
			};
		};
		$s=base64_decode($s);
		if($passw&&substr($s,0,16)==substr(md5($passw),0,16)){
			return substr($s,16);
		}else{
			return $s;
		}
	}

	private function _mixing_passw($b,$passw){
		$s='';
		$c=$b;
		$b=str_split($b);
		$passw=str_split(sha1($passw));
		$lenp=count($passw);
		$lenb=count($b);
		for($i=0;$i<$lenp;$i++){
			for($j=0;$j<$lenb;$j++){
				if($passw[$i]==$b[$j]){
					$c=str_replace($b[$j],'',$c);
					if(!preg_match('/'.$b[$j].'/',$s)){
						$s.=$b[$j];
					}
				}
			};
		};
		return $c.''.$s;
	}
}