<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/********************************************************
             _______________________
            Pasakorn Boonded
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
		//echo $sql; exit;
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
            $cond = " and PROVCOD ='".$provcod."' ";
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
	function getCUSTOMER(){
		$sess = $this->session->userdata('cbjsess001');
		$dataSearch = trim($_REQUEST['q']);
		$dataNow = (!isset($_REQUEST['now']) ? "" : $_REQUEST['now']);
		
		$sql = "
			select top 20 CUSCOD,SNAM+NAME1+' '+NAME2+' ('+CUSCOD+')' as CUSNAME from {$this->MAuth->getdb('CUSTMAST')}
			where CUSCOD = '".$dataNow."' collate Thai_CI_AS 			
			union
			select top 20 CUSCOD,SNAM+NAME1+' '+NAME2+' ('+CUSCOD+')' as CUSNAME from {$this->MAuth->getdb('CUSTMAST')}
			where CUSCOD like '%".$dataSearch."%' collate Thai_CI_AS 
				or NAME1+' '+NAME2 like '%".$dataSearch."%' collate Thai_CI_AS
				or IDNO like '%".$dataSearch."%' collate Thai_CI_AS
			order by CUSCOD
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$json[] = ['id'=>str_replace(chr(0),'',$row->CUSCOD), 'text'=>str_replace(chr(0),'',$row->CUSNAME)];
			}
		}
		echo json_encode($json);
	}
	function getCONTNO(){
		$sess = $this->session->userdata('cbjsess001');
		$dataSearch = trim($_REQUEST['q']);
		
		$sql = "
			select top 20 CONTNO
			from(
				select CONTNO from {$this->MAuth->getdb('ARMAST')}
				where CONTNO != '' and CONTNO is not null and CONTNO like '%".$dataSearch."%'
				union
				select CONTNO from {$this->MAuth->getdb('HARMAST')} 
				where CONTNO != '' and CONTNO is not null and CONTNO like '%".$dataSearch."%'
				union
				select CONTNO from {$this->MAuth->getdb('ARCRED')}
				where CONTNO != '' and CONTNO is not null and CONTNO like '%".$dataSearch."%'
				union
				select CONTNO from {$this->MAuth->getdb('HARCRED')}
				where CONTNO != '' and CONTNO is not null and CONTNO like '%".$dataSearch."%'
				union 
				select CONTNO from {$this->MAuth->getdb('ARFINC')}
				where CONTNO != '' and CONTNO is not null and CONTNO like '%".$dataSearch."%'
				union 
				select CONTNO from {$this->MAuth->getdb('AR_INVOI')}
				where CONTNO != '' and CONTNO is not null and CONTNO like '%".$dataSearch."%'
				union
				select CONTNO from {$this->MAuth->getdb('HARFINC')}
				where CONTNO != '' and CONTNO is not null and CONTNO like '%".$dataSearch."%'
				union 
				select CONTNO from {$this->MAuth->getdb('HAR_INVO')}
				where CONTNO != '' and CONTNO is not null and CONTNO like '%".$dataSearch."%'
			)A
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$json[] = ['id'=>str_replace(chr(0),'',$row->CONTNO), 'text'=>str_replace(chr(0),'',$row->CONTNO)];
			}
		}
		echo json_encode($json);
	}
	function getSIRNO(){
		$sess = $this->session->userdata('cbjsess001');
		$dataSearch = trim($_REQUEST['q']);
		
		$sql = "
			select top 20 STRNO
			from(
				select STRNO from {$this->MAuth->getdb('INVTRAN')}
				where STRNO != '' and STRNO is not null and STRNO like '%".$dataSearch."%'
			)A
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$json[] = ['id'=>str_replace(chr(0),'',$row->STRNO), 'text'=>str_replace(chr(0),'',$row->STRNO)];
			}
		}
		echo json_encode($json);
	}
	function getfromCONTNO(){
		$html = "
			<div class='row'>
				<div class='col-sm-4'>
					<div class='form-group'>
						เลขที่สัญญา
						<input type='text' id='s_contno' class='form-control'>
					</div>
				</div>
				<div class='col-sm-4'>
					<div class='form-group'>
						ชื่อ
						<input type='text' id='s_name1' class='form-control'>
					</div>
				</div>
				<div class='col-sm-4'>
					<div class='form-group'>
						นามสกุล
						<input type='text' id='s_name2' class='form-control'>
					</div>
				</div>
				
				<div class='col-sm-12'>
					<button id='cont_search' class='btn btn-primary btn-block'><span class='glyphicon glyphicon-search'>ค้นหา</span></button>
				</div>
				
				<br>
				<div id='cont_result' class='col-sm-12'></div>
			</div>
		";
		$response = array("html"=>$html);
		echo json_encode($response);
	}
	function getResultCONTNO(){
		$s_contno = $_POST['s_contno'];
		$s_name1  = $_POST['s_name1'];
		$s_name2 = $_POST['s_name2'];
		
		$cond = "";
		if($s_contno != ""){
			$cond .= "and A.CONTNO like '%".$s_contno."%'"; 
		}
		if($s_name1 != ""){
			$cond .= "and C.NAME1 like '%".$s_name1."%'";
		}
		if($s_name2 != ""){
			$cond .= "and C.NAME2 like '%".$s_name2."%'";
		}
		$sql = "
			select top 100 A.CONTNO,A.LOCAT,C.SNAM+C.NAME1+' '+C.NAME2+'' as CUSNAME from ARMAST A
			left join CUSTMAST C on A.CUSCOD = C.CUSCOD
			left join INVTRAN I on A.STRNO = I.STRNO where 1=1 ".$cond."
			order by A.CONTNO
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		$html = "";
		$NRow = 1;
		if($query->row()){
			foreach($query->result() as $row){
				$html .="
					<tr class='trow' seq='".$NRow."'>
						<td  style='cursor:pointer;' class='getit' seq='".$NRow++."'
							CONTNO ='".$row->CONTNO."'
						><b>เลือก</b></td>
						<td>".$row->CONTNO."</td>
						<td>".$row->LOCAT."</td>
						<td>".$row->CUSNAME."</td>
					</tr>
				";
			}
		}
		$html = "
			<div id='tbcont' class='col-sm-12' style='height:100%;overflow:auto;background-color:#eee;'>
				<table id='data-table-example2' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='100%'>
					<thead>
						<tr>
							<th>#</th>
							<th>เลขที่สัญญา</th>
							<th>สาขา</th>
							<th>ชื่อ-สกุล</th>						
						</tr>
					</thead>	
					<tbody>
						".$html."				
					</tbody>
				</table>
			</div>
		";
		$response = array("html"=>$html);
		echo json_encode($response);
	}
	function getfromSearchCONTNO(){
		$html = "
			<div class='row'>
				<div class='col-sm-6'>
					<div class='form-group'>
						เลขที่สัญญา
						<input type='text' id='S_contno' class='form-control'>
					</div>
				</div>
				<div class='col-sm-6'>
					<div class='form-group'>
						เลขตัวถัง
						<input type='text' id='S_strno' class='form-control'>
					</div>
				</div>
				<div class='col-sm-12'>
					<button id='Cont_search' class='btn btn-primary btn-block'><span class='glyphicon glyphicon-search'>ค้นหา</span></button>
				</div>
				<div id='Cont_result' class='col-sm-12'></div>
			</div>
		";
		$response = array("html"=>$html);
		echo json_encode($response);
	}
	function getSearchCONTNO(){
		$S_contno = $_POST['S_contno'];
		$S_strno  = $_POST['S_strno'];
		$cond = "";
		if($S_contno != ""){
			$cond .= "and A.CONTNO like '%".$S_contno."%'"; 
		}
		if($S_strno != ""){
			$cond .= "and B.STRNO like '%".$S_strno."%'";
		}
		$sql = "
			select top 100 A.CONTNO,A.LOCAT,convert(varchar(8),A.CREATEDT,112) as CREATEDT,convert(varchar(8),A.STARTDT,112) as STARTDT
			,convert(varchar(8),A.ENDDT,112) as ENDDT,A.MEMO1,case when A.USERID <>'XX' then 'แดง' else 'น้ำเงิน' end as USERID 
			,B.STRNO from ALERTMSG A left join ARMAST B on A.CONTNO = B.CONTNO where 1=1 ".$cond." 
			order by A.STARTDT desc	";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		$html = "";
		$NRow = 1;
		if($query->row()){
			foreach($query->result() as $row){
				$html .="
					<tr class='trow' seq='".$NRow."'>
						<td  style='cursor:pointer;' class='getit' seq='".$NRow++."'
							CONTNO ='".$row->CONTNO."'
							CREATEDT = '".$this->Convertdate(2,$row->CREATEDT)."'
							STARTDT = '".$this->Convertdate(2,$row->STARTDT)."'
							ENDDT = '".$this->Convertdate(2,$row->ENDDT)."'
							MEMO1 = '".$row->MEMO1."'
							USERID= '".$row->USERID."'
						><b>เลือก</b></td>
						<td>".$row->CONTNO."</td>
						<td>".$row->LOCAT."</td>
						<td>".$row->STRNO."</td>
						<td>".$row->USERID."</td>
					</tr>
				";
			}
		}
		$html = "
			<div id='tbcont' class='col-sm-12' style='height:100%;overflow:auto;background-color:#eee;'>
				<table id='data-table-example2' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='100%'>
					<thead>
						<tr>
							<th>#</th>
							<th>เลขที่สัญญา</th>
							<th>สาขา</th>
							<th>เลขตัวถัง</th>
							<th>สถานะสี</th>						
						</tr>
					</thead>	
					<tbody>
						".$html."				
					</tbody>
				</table>
			</div>
		";
		$response = array("html"=>$html);
		echo json_encode($response);
	}
	function getPAYTYP(){
		$sess = $this->session->userdata('cbjsess001');
		$dataSearch = trim($_REQUEST['q']);
		
		$sql = "
			select top 20 PAYCODE,PAYDESC from {$this->MAuth->getdb('PAYTYP')} 
			where PAYCODE like '%".$dataSearch."%' order by PAYCODE
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$json[] = ['id'=>str_replace(chr(0),'',$row->PAYCODE), 'text'=>str_replace(chr(0),'',$row->PAYDESC)];
			}
		}
		echo json_encode($json);
	}
	function getPAYFOR(){
		$sess = $this->session->userdata('cbjsess001');
		$dataSearch = trim($_REQUEST['q']);
		
		$sql = "
			select top 20 FORCODE,FORDESC from {$this->MAuth->getdb('PAYFOR')} 
			where FORCODE like '%".$dataSearch."%' order by FORCODE
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$json[] = ['id'=>str_replace(chr(0),'',$row->FORCODE), 'text'=>str_replace(chr(0),'',$row->FORDESC)];
			}
		}
		echo json_encode($json);
	}
	function getUSERID(){
		$sess = $this->session->userdata('cbjsess001');
		$dataSearch = trim($_REQUEST['q']);
		
		$sql = "
			select top 20 USERID,USERNAME from {$this->MAuth->getdb('PASSWRD')} 
			where USERID like '%".$dataSearch."%' order by USERID
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$json[] = ['id'=>str_replace(chr(0),'',$row->USERID), 'text'=>str_replace(chr(0),'',$row->USERNAME)];
			}
		}
		echo json_encode($json);
	}
	function getGCODE(){
		$sess = $this->session->userdata('cbjsess001');
		$dataSearch = trim($_REQUEST['q']);
		
		$sql = "
			select top 20 GCODE,GDESC from {$this->MAuth->getdb('SETGROUP')} 
			where GCODE like '%".$dataSearch."%' order by GCODE 
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$json[] = ['id'=>str_replace(chr(0),'',$row->GCODE), 'text'=>str_replace(chr(0),'',$row->GDESC)];
			}
		}
		echo json_encode($json);
	}
	function getOFFICER(){
		$sess = $this->session->userdata('cbjsess001');
		$dataSearch = trim($_REQUEST['q']);
		
		$sql = "
			select top 20 CODE,NAME from {$this->MAuth->getdb('OFFICER')} 
			where CODE like '%".$dataSearch."%' order by CODE
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$json[] = ['id'=>str_replace(chr(0),'',$row->CODE), 'text'=>str_replace(chr(0),'',$row->NAME)];
			}
		}
		echo json_encode($json);
	}
	function getResultCONTNO_R(){
		$s_contno = $_POST['s_contno'];
		$s_name1  = $_POST['s_name1'];
		$s_name2  = $_POST['s_name2'];
		$price    = $_POST['price'];
		
		$cond = "";
		if($s_contno != ""){
			$cond .= "and A.CONTNO like '%".$s_contno."%'"; 
		}
		if($s_name1 != ""){
			$cond .= "and B.NAME1 like '%".$s_name1."%'";
		}
		if($s_name2 != ""){
			$cond .= "and B.NAME2 like '%".$s_name2."%'";
		}
		if ($price == "P1"){
			$sql = "
				select top 100 A.CONTNO,B.SNAM+B.NAME1+' '+B.NAME2+''as CUSNAME,A.LOCAT  from ARCRED A 
				left join CUSTMAST B on A.CUSCOD = B.CUSCOD where 1=1 and A.TOTPRC > A.SMPAY ".$cond." 
				ORDER BY A.CONTNO,A.LOCAT
			";
		}else if($price == "P2"){
			$sql = "
				select top 100 A.CONTNO,B.SNAM+B.NAME1+' '+B.NAME2+''as CUSNAME,A.LOCAT  from ARMAST A 
				left join CUSTMAST B on A.CUSCOD = B.CUSCOD where 1=1 and A.TOTPRC > A.SMPAY ".$cond."
				ORDER BY A.CONTNO,A.LOCAT
			";
		}else if($price == "P3"){
			$sql = "
				select top 100 A.CONTNO,B.SNAM+B.NAME1+' '+B.NAME2+''as CUSNAME,A.LOCAT  from ARFINC A 
				left join CUSTMAST B on A.CUSCOD = B.CUSCOD where 1=1 ".$cond."
				ORDER BY A.CONTNO,A.LOCAT
			";
		}else if($price == "P4"){
			$sql = "
				select top 100 A.CONTNO,B.SNAM+B.NAME1+' '+B.NAME2+''as CUSNAME,A.LOCAT  from AR_INVOI A 
				left join CUSTMAST B on A.CUSCOD = B.CUSCOD where 1=1 and A.TOTPRC > A.SMPAY ".$cond." 
				ORDER BY A.CONTNO,A.LOCAT
			";
		}else if($price == "P5"){
			$sql = "
				select top 100 A.CONTNO,B.SNAM+B.NAME1+' '+B.NAME2+''as CUSNAME,A.LOCAT  from AROPTMST A 
				left join CUSTMAST B on A.CUSCOD = B.CUSCOD where 1=1 and A.OPTPTOT > A.SMPAY ".$cond." 
				ORDER BY A.CONTNO,A.LOCAT
			";
		}else if($price == "P6"){
			$sql = "
				select top 100 A.CONTNO,B.SNAM+B.NAME1+' '+B.NAME2+''as CUSNAME,A.LOCAT  from AROTHR A 
				left join CUSTMAST B on A.CUSCOD = B.CUSCOD where 1=1 and A.PAYAMT > A.SMPAY ".$cond."
				ORDER BY A.ARCONT,A.LOCAT
			";
			
		}
		//echo $sql; exit;
		$query = $this->db->query($sql);
		$html = "";
		$NRow = 1;
		if($query->row()){
			foreach($query->result() as $row){
				$html .="
					<tr class='trow' seq='".$NRow."'>
						<td style='cursor:pointer;' class='getit' seq='".$NRow++."'
							CONTNO ='".$row->CONTNO."'
							LOCAT  ='".$row->LOCAT."'
							CUSNAME='".$row->CUSNAME."'
						><b>เลือก</b></td>
						<td>".$row->CONTNO."</td>
						<td>".$row->LOCAT."</td>
						<td>".$row->CUSNAME."</td>
					</tr>
				";
			}
		}
		$html = "
			<div id='tbcont' class='col-sm-12' style='height:100%;overflow:auto;background-color:#eee;'>
				<table id='data-table-example2' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='100%'>
					<thead>
						<tr>
							<th>#</th>
							<th>เลขที่สัญญา</th>
							<th>สาขา</th>
							<th>ชื่อ-สกุล</th>
						</tr>
					</thead>	
					<tbody>
						".$html."				
					</tbody>
				</table>
			</div>
		";
		$response = array("html"=>$html);
		echo json_encode($response);
	}
	function getfromCUSCOD(){
		$html = "
			<div class='row'>
				<div class='col-sm-4'>
					<div class='form-group'>
						รหัสลูกค้า
						<input type='text' id='cuscod' class='form-control'>
					</div>
				</div>
				<div class='col-sm-4'>
					<div class='form-group'>
						ชื่อ
						<input type='text' id='name1' class='form-control'>
					</div>
				</div>
				<div class='col-sm-4'>
					<div class='form-group'>
						นามสกุล
						<input type='text' id='name2' class='form-control'>
					</div>
				</div>
				
				<div class='col-sm-12'>
					<button id='btnsearch' class='btn btn-primary btn-block'><span class='glyphicon glyphicon-search'>ค้นหา</span></button>
				</div>
				<br>
				<div id='cus_result' class='col-sm-12'></div>
			</div>
		";
		$response = array("html"=>$html);
		echo json_encode($response);
	}
	function getResultCUSTMAST(){
		$cuscod = $_POST['cuscod'];
		$name1  = $_POST['name1'];
		$name2  = $_POST['name2'];
		
		$cond = "";
		if($cuscod != ""){
			$cond .= "and CUSCOD like '%".$cuscod."%'"; 
		}
		if($name1 != ""){
			$cond .= "and NAME1 like '%".$name1."%'";
		}
		if($name2 != ""){
			$cond .= "and NAME2 like '%".$name2."%'";
		}		
		$sql = "
			select top 100 CUSCOD,SNAM+NAME1+' '+NAME2+''as CUSNAME from CUSTMAST
			where 1=1  ".$cond." 
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		$html = "";
		$NRow = 1;
		if($query->row()){
			foreach($query->result() as $row){
				$html .="
					<tr class='trow' seq='".$NRow."'>
						<td style='cursor:pointer;' class='getit' seq='".$NRow++."'
							CUSCOD ='".$row->CUSCOD."'
							CUSNAME  ='".$row->CUSNAME."'
						><b>เลือก</b></td>
						<td>".$row->CUSCOD."</td>
						<td>".$row->CUSNAME."</td>
					</tr>
				";
			}
		}
		$html = "
			<div id='tbcont' class='col-sm-12' style='height:100%;overflow:auto;background-color:#eee;'>
				<table id='data-table-example2' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='100%'>
					<thead>
						<tr>
							<th>#</th>
							<th>รหัสลูกค้า</th>
							<th>ชื่อ-สกุล</th>
						</tr>
					</thead>	
					<tbody>
						".$html."				
					</tbody>
				</table>
			</div>
		";
		$response = array("html"=>$html);
		echo json_encode($response);
	}
}
	