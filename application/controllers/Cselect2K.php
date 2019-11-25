<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/********************************************************
             _______________________
            Pasakorn
********************************************************/
class Cselect2K extends MY_Controller {
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
		$dataSearch = trim($_REQUEST['q']);
		$dataNow = (!isset($_REQUEST["now"]) ? "" : $_REQUEST["now"]);
		
		$sql = "
			select LOCATCD from {$this->MAuth->getdb('INVLOCAT')}
			where LOCATCD = '".$dataNow."' collate Thai_CI_AS
			
			union
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
	function getGROUP1(){
		$sess = $this->session->userdata('cbjsess001');
		$dataSearch = trim($_REQUEST['q']);
		$dataNow = (!isset($_REQUEST["now"]) ? "" : $_REQUEST["now"]);
		
		$sql = "
            select ARGCOD,ARGDES from {$this->MAuth->getdb('ARGROUP')}
            where ARGCOD='".$dataNow."' 

            union
            select top 20 ARGCOD,ARGDES from {$this->MAuth->getdb('ARGROUP')}
            where ARGDES like '%".$dataSearch."%' 
            ";
        
        $query = $this->db->query($sql);

        $json = array();
        if($query->row()){
            foreach($query->result() as $row){
                $json[] = array(
                    "id" =>str_replace(chr(0),"",$row->ARGCOD),
                    "text" =>str_replace(chr(0),"",$row->ARGDES),					 
                );
            }
        }
        echo json_encode($json);
	}
	function getGRADE(){
		$sess = $this->session->userdata('cbjsess001');
		$dataSearch = trim($_REQUEST['q']);
		$dataNow = (!isset($_REQUEST["now"]) ? "" : $_REQUEST["now"]);
		
		$sql = "select GRDCOD,GRDDES from {$this->MAuth->getdb('SETGRADCUS')}  
            where GRDCOD ='".$dataNow."' 

            union
            select GRDCOD,GRDDES from {$this->MAuth->getdb('SETGRADCUS')}
            where GRDDES like '%".$dataSearch."%' 
            ";
        
        $query = $this->db->query($sql);

        $json = array();
        if($query->row()){
            foreach($query->result() as $row){
                $json[] = array(
                    "id" =>str_replace(chr(0),"",$row->GRDCOD),
                    "text" =>str_replace(chr(0),"",$row->GRDCOD).str_replace(chr(0),"",$row->GRDDES),					 
                );
            }
        }
        echo json_encode($json);
	}
	function getSNAM(){
		$sess = $this->session->userdata('cbjsess001');
		$dataSearch = trim($_REQUEST['q']);
		$dataNow = (!isset($_REQUEST["now"]) ? "" : $_REQUEST["now"]);
		
		$sql = "
            select SIRCOD,SIRNAM from {$this->MAuth->getdb('SIRNAM')}
            where SIRCOD = '".$dataNow."'

            union
            select SIRCOD,SIRNAM from {$this->MAuth->getdb('SIRNAM')}
            where SIRNAM like '%".$dataSearch."%' 
            ";
        
        $query = $this->db->query($sql);

        $json = array();
        if($query->row()){
            foreach($query->result() as $row){
                $json[] = array(
                    "id" =>str_replace(chr(0),"",$row->SIRNAM),
                    "text" =>str_replace(chr(0),"",$row->SIRNAM),					 
                );
            }
        }
        echo json_encode($json);
	}
	function getAUMPCOD(){ //อำเภอ
		$sess = $this->session->userdata('cbjsess001');
		$dataNow = (!isset($_REQUEST["now"]) ? "" : $_REQUEST["now"]);
        $dataSearch = trim($_REQUEST["q"]);
        $provcod = $_REQUEST["provcod"];

        $cond = "";
        if($provcod == ""){
            $cond = "";
        }else{
            $cond = " and PROVCOD='".$provcod."'";
        }
        $sql = "
            select AUMPCOD,AUMPDES from {$this->MAuth->getdb('SETAUMP')}
            where AUMPCOD='".$dataNow."' 

            union
            select top 20 AUMPCOD,AUMPDES from {$this->MAuth->getdb('SETAUMP')}
            where AUMPDES like '%".$dataSearch."%' ".$cond."
            ";
        $query = $this->db->query($sql);

        $json = array();
        if($query->row()){
            foreach($query->result() as $row){
                $json[] = array(
                    "id" => $row->AUMPCOD,
                    "text" => $row->AUMPDES,					 
                );
            }
        }
        echo json_encode($json);
	}
	function getPROVCOD(){		//จังหวัด
		$sess = $this->session->userdata('cbjsess001');
		$dataNow = (!isset($_REQUEST["now"]) ? "" : $_REQUEST["now"]);
        $dataSearch = trim($_REQUEST["q"]);
        $aumpcod = $_REQUEST["aumpcod"];

        $cond = "";
        if($aumpcod == ""){
            $cond = "";
        }else{
            $cond = " and b.AUMPCOD='".$aumpcod."'";
        }
        $sql = "
            select a.PROVCOD,a.PROVDES from {$this->MAuth->getdb('SETPROV')} a 
            left join {$this->MAuth->getdb('SETAUMP')} b on a.PROVCOD=b.PROVCOD
            where a.PROVCOD='".$dataNow."'                        

            union
            select top 20 * from (
                select distinct a.PROVCOD,a.PROVDES from {$this->MAuth->getdb('SETPROV')} a
                left join {$this->MAuth->getdb('SETAUMP')} b on a.PROVCOD=b.PROVCOD
                where a.PROVDES like '%".$dataSearch."%' ".$cond." 			
            ) as data
        ";
       
        $query = $this->db->query($sql);
        $json = array();
        if($query->row()){
            foreach($query->result() as $row){
                $json[] = array(
                    "id" => $row->PROVCOD,
                    "text" => $row->PROVDES,					 
                );
            }
        }
        echo json_encode($json);
	}
	function getZIP(){		//รหัสไปรษณีย์
		$sess = $this->session->userdata('cbjsess001');
		$dataNow = (!isset($_REQUEST["now"]) ? "" : $_REQUEST["now"]);
        $dataSearch = trim($_REQUEST["q"]);
        $provcod = $_REQUEST["provcod"];

        $cond = "";
        if($provcod == ""){
            $cond = "";
        }else{
            $cond = " and b.PROVCOD ='".$provcod."' ";
        }
        $sql = "
            select PROVCOD,AUMPCOD from {$this->MAuth->getdb('SETAUMP')}
            where PROVCOD='".$dataNow."' 

            union
            select top 20 PROVCOD,AUMPCOD from {$this->MAuth->getdb('SETAUMP')}
            where AUMPCOD like '%".$dataSearch."%' ".$cond." 
        ";
        
        $query = $this->db->query($sql);
        $json = array();
        if($query->row()){
            foreach($query->result() as $row){
                $json[] = array(
                    "id" => $row->PROVCOD,
                    "text" => $row->AUMPCOD,					 
                );
            }
        }
        echo json_encode($json);
	}
	function getProv(){		//เลือกอำเภอโชว์จังหวัด
		$aumpcod = $_POST["aumpcod"];
        $sql ="
            select * from {$this->MAuth->getdb('SETPROV')} A
			inner join {$this->MAuth->getdb('SETAUMP')} B on A.PROVCOD = B.PROVCOD
            where AUMPCOD='".$aumpcod."'
            ";
        $query = $this->db->query($sql);

        $data = array();
        if($query->row()){
            foreach($query->result() as $row){
                $data["PROVCOD"] = $row->PROVCOD;
                $data["PROVDES"] = $row->PROVDES;
            }
        }
        echo json_encode($data);
	}
	function getZipshow(){		//เลือกอำเภอโชว์รหัสไปรษณีย์
		$aumpcod1 = $_POST["aumpcod1"];
        $sql = "
            select * from {$this->MAuth->getdb('SETPROV')} A
			inner join {$this->MAuth->getdb('SETAUMP')} B on A.PROVCOD = B.PROVCOD
            where AUMPCOD='".$aumpcod1."'
        ";
        $query = $this->db->query($sql);
        $data = array();
        if($query->row()){
            foreach($query->result() as $row){
                $data["AUMPCOD"] = $row->AUMPCOD;
                $data["AUMPCOD"] = $row->AUMPCOD;
            }
        }
        echo json_encode($data);
	}
}
	