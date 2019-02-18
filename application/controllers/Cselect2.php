<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/********************************************************
             _______________________
            / / _ _   _ _     __ 
           / // __ \ / __ \ / __ \
       _ _/ // /_/ // / / // /_/ /
     /_ _ _/ \_ _ //_/ /_/ \__  /
                          _ _/ /
                         /___ /
********************************************************/
class Cselect2 extends MY_Controller {
	private $sess = array();
	
	function __construct(){
		parent::__construct();
		/*Additional code which you want to run automatically in every function call */
		$sess = $this->session->userdata('cbjsess001');
		if(!$sess){ redirect(base_url("welcome/"),"_parent"); }else{
			foreach ($sess as $key => $value) {
                $this->sess[$key] = $value;
            }
		}
	}
	
	function getLOCAT(){
		$sess = $this->session->userdata('cbjsess001');
		$dataSearch = trim($_GET['q']);
		
		$sql = "
			select top 20 LOCATCD from {$this->MAuth->getdb('INVLOCAT')}
			where LOCATCD like '%".$dataSearch."%' collate Thai_CI_AS or LOCATNM like '%".$dataSearch."%' collate Thai_CI_AS
			order by LOCATCD
		";
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$json[] = ['id'=>str_replace(chr(0),'',$row->LOCATCD), 'text'=>str_replace(chr(0),'',$row->LOCATCD)];
			}
		}
		
		echo json_encode($json);
	}
	
	function getUSERS(){
		$sess = $this->session->userdata('cbjsess001');
		$dataSearch = trim($_GET['q']);
		
		$sql = "
			select top 20 USERID,USERNAME+' ('+USERID+')' as USERNAME from {$this->MAuth->getdb('PASSWRD')}
			where EXPDATE is null and (
				USERID like '%".$dataSearch."%' collate Thai_CI_AS 
				or USERNAME like '%".$dataSearch."%' collate Thai_CI_AS
			)	
			order by USERID
		";
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$json[] = ['id'=>$row->USERID, 'text'=>$row->USERNAME];
			}
		}
		
		echo json_encode($json);
	}
	
	function getVUSER(){
		$sess = $this->session->userdata('cbjsess001');
		$dataSearch = trim($_GET['q']);
		
		$sql = "
			select top 20 IDNo,employeeCode,employeeCode+' :: '+firstName+' '+LastName as Name from YTKManagement.dbo.hp_vusers
			where employeeCode like '%".$dataSearch."%' collate Thai_CI_AS
				or IDNo like '%".$dataSearch."%' collate Thai_CI_AS
				or employeeCode+' :: '+firstName+' '+LastName like '%".$dataSearch."%' collate Thai_CI_AS
			order by employeeCode
		"; 
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$json[] = ['id'=>$row->IDNo, 'text'=>$row->Name];
			}
		}
		
		echo json_encode($json);
	}
	
	function getCUSTOMERS(){
		$sess = $this->session->userdata('cbjsess001');
		$dataSearch = trim($_GET['q']);
		
		$sql = "
			select top 20 CUSCOD,SNAM+NAME1+' '+NAME2+' ('+CUSCOD+')' as CUSNAME from {$this->MAuth->getdb('CUSTMAST')}
			where CUSCOD like '%".$dataSearch."%' collate Thai_CI_AS 
				or NAME1+' '+NAME2 like '%".$dataSearch."%' collate Thai_CI_AS
				or IDNO like '%".$dataSearch."%' collate Thai_CI_AS
			order by CUSCOD
		";
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$json[] = ['id'=>$row->CUSCOD, 'text'=>$row->CUSNAME];
			}
		}
		
		echo json_encode($json);
	}
	
	function getTransfercars(){
		$sess = $this->session->userdata('cbjsess001');
		$dataSearch = trim($_GET['q']);
		$TRANSTO = $_REQUEST["TRANSTO"];
		
		$sql = "
			select top 20 TRANSNO from {$this->MAuth->getdb('INVTransfers')}
			where TRANSNO like '%".$dataSearch."%' collate Thai_CI_AS
				and TRANSTO='".$TRANSTO."'
			order by TRANSNO
		";
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$json[] = ['id'=>$row->TRANSNO, 'text'=>$row->TRANSNO];
			}
		}
		
		echo json_encode($json);
	}
	
	function getINVINVO(){
		//ดึงเลขที่บิลรับ เพื่อคำนวณจัดรถให้สาขา
		$sess = $this->session->userdata('cbjsess001');
		$dataSearch = trim($_GET['q']);
		$RECVDT = $this->Convertdate(1,$_GET['RECVDT']);
		$RVLOCAT = trim($_GET['RVLOCAT']);
		
		$cond='';
		if($RECVDT != ''){
			$cond .= " and convert(varchar(8),RECVDT,112)='".$RECVDT."'";
		}
		
		$sql = "
			select top 20 RECVNO from {$this->MAuth->getdb('INVINVO')}
			where RECVNO like '%".$dataSearch."%' collate Thai_CI_AS
				and LOCAT='".$RVLOCAT."' ".$cond."
			order by RECVNO desc
		"; 
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$json[] = ['id'=>$row->RECVNO, 'text'=>$row->RECVNO];
			}
		}
		
		echo json_encode($json);
	}
	
	function getWarehouse(){
		//ดึงเลขที่บิลรับ เพื่อคำนวณจัดรถให้สาขา
		$sess = $this->session->userdata('cbjsess001');
		$dataSearch = trim($_GET['q']);
		
		
		$sql = "
			select top 20 LOCAT from YTKManagement.dbo.std_locatWarehouse
			where LOCAT like '%".$dataSearch."%' collate Thai_CI_AS				
			order by LOCAT
		"; 
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$json[] = ['id'=>$row->LOCAT, 'text'=>$row->LOCAT];
			}
		}
		
		echo json_encode($json);
	}
	
	function getGroupCode(){
		//ดึงเลขที่บิลรับ เพื่อคำนวณจัดรถให้สาขา
		$sess = $this->session->userdata('cbjsess001');
		$dataSearch = trim($_GET['q']);
		
		$sql = "
			select top 20 groupCode,groupName from YTKManagement.dbo.hp_groupuser
			where groupCode like '%".$dataSearch."%' collate Thai_CI_AS
				or groupName like '%".$dataSearch."%' collate Thai_CI_AS
			order by groupCode
		"; 
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$json[] = ['id'=>$row->groupCode, 'text'=>$row->groupCode];
			}
		}
		
		echo json_encode($json);
	}
	
	function getGCode(){
		//กลุ่มสินค้า
		$sess = $this->session->userdata('cbjsess001');
		$dataSearch = trim($_GET['q']);
		
		$sql = "
			select GCODE,'('+GCODE+') '+GDESC as GDESC from {$this->MAuth->getdb('SETGROUP')}
			where GCODE like '%".$dataSearch."%' collate Thai_CI_AS
				or GDESC like '%".$dataSearch."%' collate Thai_CI_AS
			order by GCODE
		"; 
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$json[] = ['id'=>$row->GCODE, 'text'=>$row->GDESC];
			}
		}
		
		echo json_encode($json);
	}
	
	function getMODEL(){
		//รุ่นรถ
		$sess = $this->session->userdata('cbjsess001');
		$dataSearch = trim($_GET['q']);
		$TYPECOD = $_REQUEST['TYPECOD'];
		
		$sql = "
			select MODELCOD from {$this->MAuth->getdb('SETMODEL')}
			where TYPECOD='".$TYPECOD."' and MODELCOD like '%".$dataSearch."%' collate Thai_CI_AS
			order by MODELCOD
		"; 
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		$json = array();
		if($query->row()){
			foreach($query->result() as $row){
				//$json[] = ['id'=>$row->MODELCOD, 'text'=>$row->MODELCOD];
				$json[] = array('id'=>$row->MODELCOD, 'text'=>$row->MODELCOD);
			}
		}
		
		echo json_encode($json);
	}
	
	function getCOLOR(){
		//สีรถ
		$sess = $this->session->userdata('cbjsess001');
		$dataSearch = trim($_GET['q']);
		
		$sql = "
			select COLORCOD from {$this->MAuth->getdb('SETCOLOR')}
			where COLORCOD like '%".$dataSearch."%' collate Thai_CI_AS
			order by COLORCOD
		"; 
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		$json = array();
		if($query->row()){
			foreach($query->result() as $row){
				$json[] = array('id'=>$row->COLORCOD, 'text'=>$row->COLORCOD);
			}
		}
		
		echo json_encode($json);
	}
	
}




















