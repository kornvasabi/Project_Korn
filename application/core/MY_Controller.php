<?php
defined('BASEPATH') OR exit('No direct script access allowed');
ini_set('max_execution_time',-1);
require_once './vendor/autoload.php';
date_default_timezone_set('Asia/Bangkok');

class MY_Controller extends CI_Controller {
	//public $sess = array(); 
	public $connect_db = "";
	public $config_db = array(); 
	public $response = array();
	public $username = "";
	public $thaiLongMonthArray = array(1=>'มกราคม',2=>'กุมภาพันธ์',3=>'มีนาคม',4=>'เมษายน',5=>'พฤษภาคม',6=>'มิถุนายน',7=>'กรกฎาคม',8=>'สิงหาคม',9=>'กันยายน',10=>'ตุลาคม',11=>'พฤศจิกายน',12=>'ธันวาคม');
	
	function __construct(){
		parent::__construct();
		
		$this->config_db['hostname'] = '192.168.0.10';
		$this->config_db['username'] = 'YTKMini';
		$this->config_db['password'] = 'senior';
		//$this->config_db['database'] = $this->sess["db"];
		$this->config_db['dbdriver'] = 'sqlsrv';
		$this->config_db['dbprefix'] = 'wb_';
		$this->config_db['pconnect'] = FALSE;
		$this->config_db['db_debug'] = FALSE;
		$this->config_db['cache_on'] = FALSE;
		$this->config_db['char_set'] = 'UTF-8';
		$this->config_db['autoinit'] = TRUE;
		$this->config_db['stricton'] = FALSE;
		//$this->connect_db = $this->load->database($config_db,true);
		
		$this->response["error"] = false;
		$this->response["errorMessage"] = "";
	}
	
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
		// $param = 22 > to User Interface
		// $param = 108 > to time User Interface
		if($date == ''){
			return '';
		}else{
			if($param == 1){
				$dd = substr($date, 0,2);
				$mm = substr($date, 3,2);
				$yy = substr($date, 6,4) - 543;
				return $yy.$mm.$dd;
			}else if($param == 2){
				$yy = substr($date, 0,4) + 543;
				$mm = substr($date, 4,2);
				$dd = substr($date, 6,2);
				return $dd."/".$mm."/".$yy;
			}else if($param == 103){
				$yy = substr($date, 0,4) + 543;
				$mm = substr($date, 5,2);
				$dd = substr($date, 8,2);
				return $dd."/".$mm."/".$yy;
			}else if($param == 108){
				$time = substr($date, 11,5);
				return $time;
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
			case 'checkmaindb':
				$data = array(
					'HIC2SHORTL' => 'HIC2SHORTL',
					'HIINCOME' => 'HIINCOME',
					'HN' => 'HIINCOME',
					'FN' => 'HIINCOME',
					'RJYN' => 'RJYN',
					'HRJYN' => 'RJYN',
					'FRJYN' => 'RJYN',
					'TJHON' => 'TJHON',
					'TJPAT' => 'TJPAT',
					'HTJPAT' => 'TJPAT',
					'FTJPAT' => 'TJPAT',
					'TJYL2556' => 'TJYL2556',
					'TJYN' => 'TJYN',
					'HTJYN' => 'TJYN',
					'FTJYN' => 'TJYN',
					'TJYN2004' => 'TJYN2004',
					'HTJYN2004' => 'TJYN2004',
					'FTJYN2004' => 'TJYN2004'					
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
	
	public function today($param){
		$sql = "
			select 	cast(year(getdate()) as varchar(4))+'0101' as startinyear
				,convert(varchar(8),(convert(varchar(6),dateadd(month,-1,getdate()),112)+'01'),112) as startofmonthB1
				,convert(varchar(8),(convert(varchar(6),getdate(),112)+'01'),112) as startofmonth
				,convert(varchar(8),getdate(),112) as today
				,convert(varchar(8),(dateadd(day,-1,convert(varchar(6),dateadd(month,1,getdate()),112)+'01')),112) as endofmonth
				,convert(varchar(8),dateadd(day,-1,convert(varchar(6),getdate(),112)+'01'),112) as endofmonthB1
				,convert(varchar(8),dateadd(month,1,getdate()),112) as todaynextmonth
		";
		$query = $this->db->query($sql);
		
		if($query->row()){
			foreach($query->result() as $row){
				switch($param){
					case 'startinyear': return $this->Convertdate(2,$row->startinyear); break;
					case 'startofmonthB1': return $this->Convertdate(2,$row->startofmonthB1); break;
					case 'startofmonth': return $this->Convertdate(2,$row->startofmonth); break;
					case 'today': return $this->Convertdate(2,$row->today); break;
					case 'endofmonth': return $this->Convertdate(2,$row->endofmonth); break;
					case 'endofmonthB1': return $this->Convertdate(2,$row->endofmonthB1); break;
					case 'todaynextmonth': return $this->Convertdate(2,$row->todaynextmonth); break;
				}
			}
		}
		
		return "";
	}
	
	public function opt($data,$valued){
		$opt = "";
		switch($data){
			case 'CC':
				$sql = "select CCCOD from {$this->MAuth->getdb('SETCC')}";
				$query = $this->db->query($sql);
				
				if($query->row()){
					foreach($query->result() as $row){
						$cccod = (str_replace(chr(0),'',$row->CCCOD));
						$opt .= "<option value='".$cccod."' ".($valued == $cccod ? "selected":"").">".$cccod."</option>";
					}
				}
				break;
			case 'STAT':
				$sql = "
					select 'N' as STATCOD,'รถใหม่' as STATNAME
					union
					select 'O' as STATCOD,'รถเก่า' as STATNAME
				";
				$query = $this->db->query($sql);
				
				if($query->row()){
					foreach($query->result() as $row){
						$STATCOD = (str_replace(chr(0),'',$row->STATCOD));
						$STATNAME = (str_replace(chr(0),'',$row->STATNAME));
						
						$opt .= "<option value='".$STATCOD."' ".($valued == $STATCOD ? "selected":"").">".$STATNAME."</option>";
					}
				}
				break;
		}
		
		return $opt;
	}
	public function ConvertText($amount_number){
		$amount_number = str_replace(",","",$amount_number);
		$pt = strpos($amount_number , ".");
		$number = $fraction = "";
		if ($pt === false) 
			$number = $amount_number;
		else
		{
			$number = substr($amount_number, 0, $pt);
			$fraction = substr($amount_number, $pt + 1);
		}
		
		$ret = "";
		$baht = $this->ReadNumber($number);
		if ($baht != "")
			$ret .= $baht . "บาท";
		
		$satang = $this->ReadNumber($fraction);
		if ($satang != "")
			$ret .=  $satang . "สตางค์";
		else 
			$ret .= "ถ้วน";
		return $ret;
	}
	private function ReadNumber($number){
		$position_call = array("แสน", "หมื่น", "พัน", "ร้อย", "สิบ", "");
		$number_call = array("", "หนึ่ง", "สอง", "สาม", "สี่", "ห้า", "หก", "เจ็ด", "แปด", "เก้า");
		$number = $number + 0;
		$ret = "";
		if ($number == 0) return $ret;
		if ($number > 1000000)
		{
			$ret .= ReadNumber(intval($number / 1000000)) . "ล้าน";
			$number = intval(fmod($number, 1000000));
		}
		
		$divider = 100000;
		$pos = 0;
		while($number > 0)
		{
			$d = intval($number / $divider);
			$ret .= (($divider == 10) && ($d == 2)) ? "ยี่" : 
				((($divider == 10) && ($d == 1)) ? "" :
				((($divider == 1) && ($d == 1) && ($ret != "")) ? "เอ็ด" : $number_call[$d]));
			$ret .= ($d ? $position_call[$pos] : "");
			$number = $number % $divider;
			$divider = $divider / 10;
			$pos++;
		}
		return $ret;
	}
	
	public function getInfoSTRNO($strno){
		/*
			ดึงข้อมูล ณ วันที่ 15/08/2563
			หมายเหตุ เป็นการดึงข้อมูลจาก aphonda โดยไม่ได้แจ้งให้ทราบก่อน ซึ่งในอนาคตมีโอกาศที่เราจะไม่สามารถดึงข้อมูลได้
		*/
		
		$pc = 'vindat';
		$vin = $strno;
		
		return file_get_contents('https://dealer.aphonda.co.th/service/claim/clmcommon.asp?pc='.$pc.'&vin='.$vin);
	}
	public function DateThai($strDate){
		$strYear = date("Y",strtotime($strDate))+543;
		$strMonth= date("n",strtotime($strDate));
		$strDay= date("j",strtotime($strDate));
		$strMonthCut = Array("","ม.ค.","ก.พ.","มี.ค.","เม.ย.","พ.ค.","มิ.ย.","ก.ค.","ส.ค.","ก.ย.","ต.ค.","พ.ย.","ธ.ค.");
		$strMonthThai=$strMonthCut[$strMonth];
		return "$strDay $strMonthThai $strYear";
	}
}














