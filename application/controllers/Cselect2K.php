<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/********************************************************
            ________________________
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
			select LOCATCD,LOCATNM from {$this->MAuth->getdb('INVLOCAT')}
			where LOCATCD = '".$dataNow."' collate Thai_CI_AS
			union
			select top 20 LOCATCD,LOCATNM from {$this->MAuth->getdb('INVLOCAT')}
			where LOCATCD like '%".$dataSearch."%' collate Thai_CI_AS 
			or LOCATNM like '%".$dataSearch."%' collate Thai_CI_AS
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
	function getLOCATNM(){
		$sess = $this->session->userdata('cbjsess001');
		$dataSearch = trim($_REQUEST['q']);
		
		$sql = "
			select LOCATCD,LOCATNM+' ('+LOCATCD+')' as LOCATNM from {$this->MAuth->getdb('INVLOCAT')}
			where LOCATCD like '%".$dataSearch."%' or LOCATNM like '%".$dataSearch."%'
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$json[] = ['id'=>str_replace(chr(0),'',$row->LOCATCD)
				,'text'=>str_replace(chr(0),'',$row->LOCATNM)];
			}
		}
		echo json_encode($json);
	}
	function getCUSCODNM(){
		$sess = $this->session->userdata('cbjsess001');
		$dataSearch = trim($_REQUEST['q']);
		
		$sql = "
			select top 20 CUSCOD,CUSNAM,CUSNAME from (
				select CUSCOD,SNAM+NAME1+'  '+NAME2 as CUSNAME,+NAME1+'  '+NAME2+' ('+CUSCOD+')' as CUSNAM 
				from {$this->MAuth->getdb('CUSTMAST')}
			)a where CUSCOD like '%".$dataSearch."%' or CUSNAME like '%".$dataSearch."%'
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$json[] = array(
					'id'=>str_replace(chr(0),'',$row->CUSCOD),
					'text'=>str_replace(chr(0),'',$row->CUSNAM)
				);
			}
		}
		echo json_encode($json);
	}
	function getGROUP1(){
		$sess = $this->session->userdata('cbjsess001');
		$dataSearch = trim($_REQUEST['q']);
		$dataNow = (!isset($_REQUEST["now"]) ? "" : $_REQUEST["now"]);
		
		$sql = "
            select ARGCOD,'('+ARGCOD+') '+ARGDES as ARGDES from {$this->MAuth->getdb('ARGROUP')}
            where ARGCOD='".$dataNow."' 

            union
            select top 20 ARGCOD,'('+ARGCOD+') '+ARGDES as ARGDES from {$this->MAuth->getdb('ARGROUP')}
            where ARGDES like '".$dataSearch."%' or ARGCOD like '".$dataSearch."%'
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
		
		$sql = "
			select GRDCOD,'('+GRDCOD+') '+GRDDES as GRDDES from {$this->MAuth->getdb('SETGRADCUS')}  
            where GRDCOD ='".$dataNow."' 

            union
            select GRDCOD,'('+GRDCOD+') '+GRDDES as GRDDES from {$this->MAuth->getdb('SETGRADCUS')}
            where GRDDES like '".$dataSearch."%' or GRDCOD like '".$dataSearch."%'
        ";
        
        $query = $this->db->query($sql);

        $json = array();
        if($query->row()){
            foreach($query->result() as $row){
                $json[] = array(
                    "id" =>str_replace(chr(0),"",$row->GRDCOD),
                    "text" =>str_replace(chr(0),"",$row->GRDDES),					 
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
        if($provcod != ""){
            $cond .= " and PROVCOD = '".$provcod."'";
        }
		
        $sql = "
            select PROVCOD,AUMPCOD,AUMPDES from {$this->MAuth->getdb('SETAUMP')}
            where AUMPCOD = '".$dataNow."' 
            union
            select PROVCOD,AUMPCOD,AUMPDES from (
				select PROVCOD,AUMPCOD,AUMPDES from {$this->MAuth->getdb('SETAUMP')}
				where AUMPCOD like '".$dataSearch."%' or AUMPDES like '".$dataSearch."%'
			)a where 1=1 ".$cond."
        ";
		//echo $sql; exit;
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
        $aumpcod = (!isset($_REQUEST["aumpcod"]) ? "":$_REQUEST["aumpcod"]);
		$provcod = (!isset($_REQUEST["provcod"]) ? "":$_REQUEST["provcod"]);

        $cond = "";
        if($aumpcod != ""){
            $cond .= " and AUMPCOD like '".$aumpcod."%'";
        }
		if($provcod != ""){
            $cond .= " and PROVCOD like '".$provcod."%'";
        }
		
        $sql = "
            select PROVCOD,AUMPCOD from {$this->MAuth->getdb('SETAUMP')}
            where PROVCOD='".$dataNow."' 

            union
            select top 20 * from (
				select PROVCOD,AUMPCOD from {$this->MAuth->getdb('SETAUMP')}
				where 1=1 ".$cond."				
			)a where AUMPCOD like '%".$dataSearch."%'  
        ";
		//echo $sql; exit;
		
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
		$s_name2  = $_POST['s_name2'];
		
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
			SELECT 
				top 100 A.CONTNO,A.LOCAT,C.SNAM+C.NAME1+' '+C.NAME2+'' as CUSNAME 
			FROM {$this->MAuth->getdb('ARMAST')} A,{$this->MAuth->getdb('CUSTMAST')} C
			,{$this->MAuth->getdb('INVTRAN')} I 
			WHERE A.CUSCOD = C.CUSCOD AND A.STRNO = I.STRNO ".$cond."
			ORDER BY A.CONTNO 
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
							CONTNO  = '".$row->CONTNO."'
							LOCAT   = '".$row->LOCAT."'
							CUSNAME = '".$row->CUSNAME."' 
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
			,B.STRNO from {$this->MAuth->getdb('ALERTMSG')} A 
			left join {$this->MAuth->getdb('ARMAST')} B on A.CONTNO = B.CONTNO where 1=1 ".$cond." 
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
			select top 50 GCODE,GDESC from {$this->MAuth->getdb('SETGROUP')} 
			where GCODE like '".$dataSearch."%' or GDESC like '".$dataSearch."%' 
			order by GCODE 
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
			select top 20 CODE,NAME,NAME+'('+CODE+')' as CNAM from {$this->MAuth->getdb('OFFICER')} 
			where CODE like '%".$dataSearch."%' order by CODE
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$json[] = ['id'=>str_replace(chr(0),'',$row->CODE), 'text'=>str_replace(chr(0),'',$row->CNAM)];
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
				select top 100 A.CONTNO,B.SNAM+B.NAME1+' '+B.NAME2+''as CUSNAME,A.LOCAT  from {$this->MAuth->getdb('ARCRED')} A 
				left join {$this->MAuth->getdb('CUSTMAST')} B on A.CUSCOD = B.CUSCOD where 1=1 and A.TOTPRC > A.SMPAY ".$cond." 
				ORDER BY A.CONTNO,A.LOCAT
			";
		}else if($price == "P2"){
			$sql = "
				select top 100 A.CONTNO,B.SNAM+B.NAME1+' '+B.NAME2+''as CUSNAME,A.LOCAT  from {$this->MAuth->getdb('ARMAST')} A 
				left join {$this->MAuth->getdb('CUSTMAST')} B on A.CUSCOD = B.CUSCOD where 1=1 and A.TOTPRC > A.SMPAY ".$cond."
				ORDER BY A.CONTNO,A.LOCAT
			";
		}else if($price == "P3"){
			$sql = "
				select top 100 A.CONTNO,B.SNAM+B.NAME1+' '+B.NAME2+''as CUSNAME,A.LOCAT  from {$this->MAuth->getdb('ARFINC')} A 
				left join {$this->MAuth->getdb('CUSTMAST')} B on A.CUSCOD = B.CUSCOD where 1=1 ".$cond."
				ORDER BY A.CONTNO,A.LOCAT
			";
		}else if($price == "P4"){
			$sql = "
				select top 100 A.CONTNO,B.SNAM+B.NAME1+' '+B.NAME2+''as CUSNAME,A.LOCAT  from {$this->MAuth->getdb('AR_INVOI')} A 
				left join {$this->MAuth->getdb('CUSTMAST')} B on A.CUSCOD = B.CUSCOD where 1=1 and A.TOTPRC > A.SMPAY ".$cond." 
				ORDER BY A.CONTNO,A.LOCAT
			";
		}else if($price == "P5"){
			$sql = "
				select top 100 A.CONTNO,B.SNAM+B.NAME1+' '+B.NAME2+''as CUSNAME,A.LOCAT  from {$this->MAuth->getdb('AROPTMST')} A 
				left join {$this->MAuth->getdb('CUSTMAST')} B on A.CUSCOD = B.CUSCOD where 1=1 and A.OPTPTOT > A.SMPAY ".$cond." 
				ORDER BY A.CONTNO,A.LOCAT
			";
		}else if($price == "P6"){
			$sql = "
				select top 100 A.CONTNO,B.SNAM+B.NAME1+' '+B.NAME2+''as CUSNAME,A.LOCAT  from {$this->MAuth->getdb('AROTHR')} A 
				left join {$this->MAuth->getdb('CUSTMAST')} B on A.CUSCOD = B.CUSCOD where 1=1 and A.PAYAMT > A.SMPAY ".$cond."
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
			select top 100 CUSCOD,SNAM+NAME1+' '+NAME2+''as CUSNAME from {$this->MAuth->getdb('CUSTMAST')}
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
	function getBKCODE(){
		$sess = $this->session->userdata('cbjsess001');
		$dataSearch = trim($_REQUEST['q']);
		$sql = "
			select BKCODE,BKNAME,BKCODE+' ('+BKNAME+')' as CODENAM from {$this->MAuth->getdb('BOOK')} 
			where BKCODE like '%".$dataSearch."%' order by BKCODE
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$json[] = ['id'=>str_replace(chr(0),'',$row->BKCODE)
						  ,'text'=>str_replace(chr(0),'',$row->CODENAM)];
			}
		}
		echo json_encode($json);
	}
	function getCONTNO_RP(){
		$sess = $this->session->userdata('cbjsess001');
		$dataSearch = trim($_REQUEST['q']);
		$sql = "
			select top 100 A.CONTNO,A.LOCAT,C.SNAM+C.NAME1+' '+C.NAME2+' ('+A.CONTNO+')' as CUSNAME 
			from {$this->MAuth->getdb('ARMAST')} A
			left join {$this->MAuth->getdb('CUSTMAST')} C on A.CUSCOD = C.CUSCOD
			left join {$this->MAuth->getdb('INVTRAN')} I on A.STRNO = I.STRNO where 1=1 
			and A.CONTNO like '%".$dataSearch."%' or C.NAME1 like '%".$dataSearch."%' or C.NAME2 like '%".$dataSearch."%'
			order by A.CONTNO
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$json[] = ['id'=>str_replace(chr(0),'',$row->CONTNO)
				,'text'=>str_replace(chr(0),'',$row->CUSNAME)];
			}
		}
		echo json_encode($json);
	}
	function getTAXNO(){
		$sess = $this->session->userdata('cbjsess001');
		$dataNow = (!isset($_REQUEST["now"]) ? "" : $_REQUEST["now"]);
        $dataSearch = trim($_REQUEST["q"]);
        $locat = $_REQUEST["locat"];

        $sql = "
           select top 20 TAXNO,TAXNO+'('+NAME1+')' as TAXNAME from {$this->MAuth->getdb('TAXBUY')}
		   where TAXNO like '%".$dataSearch."%' and LOCAT = '".$locat."' and (FLAG <> 'C' or FLAG is null)
        ";
        $query = $this->db->query($sql);
        $json = array();
        if($query->row()){
            foreach($query->result() as $row){
                $json[] = array(
                    "id" => $row->TAXNO,
                    "text" => $row->TAXNAME,					 
                );
            }
        }
        echo json_encode($json);
	}
	function getSTRNO(){
		$sess = $this->session->userdata('cbjsess001');
		$dataNow = (!isset($_REQUEST["now"]) ? "" : $_REQUEST["now"]);
        $dataSearch = trim($_REQUEST["q"]);
        $taxno = (!isset($_REQUEST["taxno"]) ? "" : $_REQUEST["taxno"]);
		$recvno = (!isset($_REQUEST['recvno']) ? "" : $_REQUEST['recvno']);
		$vat   = $_REQUEST['vat'];
		if($vat == "vatcar"){
			$sql = "
				select A.RECVNO,A.STRNO,A.RECVNO,B.TAXNO from {$this->MAuth->getdb('INVTRAN')} A
				left join {$this->MAuth->getdb('TAXBUY')} B on A.RECVNO = B.REFNO 
				where A.STRNO like '%".$dataSearch."%' and B.TAXNO = '".$taxno."'
			";
		}else{
			$sql = "
				select RECVNO,STRNO,NETCOST,CRVAT,TOTCOST,VATRT,CRDTXNO,CRDAMT,FLAG from {$this->MAuth->getdb('INVTRAN')}
				where RECVNO = '".$recvno."'
			";
			//echo $sql; exit;
		}
        $query = $this->db->query($sql);
        $json = array();
        if($query->row()){
            foreach($query->result() as $row){
                $json[] = array(
                    "id" => $row->STRNO,
                    "text" => $row->STRNO,					 
                );
            }
        }
        echo json_encode($json);
	}
	function getfromREDUCECAR(){
		$html = "
			<div class='row'>
				<div class='col-sm-4'>
					<div class='form-group'>
						ออกโดยสาขา
						<input type='text' id='locat' class='form-control'>
					</div>
				</div>
				<div class='col-sm-4'>
					<div class='form-group'>
						เลขที่ใบลดหนี้
						<input type='text' id='taxno' class='form-control'>
					</div>
				</div>
				<div class='col-sm-4'>
					<div class='form-group'>
						อ้างถึงใบกำกับ
						<input type='text' id='refno' class='form-control'>
					</div>
				</div>
				
				<div class='col-sm-12'>
					<button id='btnsearch' class='btn btn-primary btn-block'><span class='glyphicon glyphicon-search'>ค้นหา</span></button>
				</div>
				<br>
				<div id='vat_result' class='col-sm-12'></div>
			</div>
		";
		$response = array("html"=>$html);
		echo json_encode($response);
	}
	function getsearchREDUCECAR(){
		$sess = $this->session->userdata('cbjsess001');
		$locat    = $_POST['locat'];
		$taxno    = $_POST['taxno'];
		$refno    = $_POST['refno'];
		$vatprice = $_POST['vatprice'];
		
		$cond = ""; //$vatprice = "";
		if($locat != ""){
			$cond .= "and LOCAT like '%".$locat."%'"; 
		}
		if($taxno != ""){
			$cond .= "and TAXNO like '%".$taxno."%'";
		}
		if($refno != ""){
			$cond .= "and REFNO like '%".$refno."%'";
		}
		if($vatprice == "debtcar"){
			$sql = "
				select LOCAT,TAXNO,convert(varchar(8),TAXDT,112) as TAXDT,REFNO,convert(varchar(8),REFDT,112) as REFDT
				,CUSCOD,NAME1,TOTAMT,FLAG,STRNO,NETAMT,VATAMT,TOTAMT
				from {$this->MAuth->getdb('TAXBUY')} where TAXTYP = '1'  ".$cond." 
			";
		}else{
			$sql = "
				select LOCAT,TAXNO,convert(varchar(8),TAXDT,112) as TAXDT,REFNO,convert(varchar(8),REFDT,112) as REFDT
				,CUSCOD,NAME1,TOTAMT,FLAG,STRNO,NETAMT,VATAMT,TOTAMT
				from {$this->MAuth->getdb('TAXBUY')} where TAXTYP = '2'  ".$cond." 
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
							LOCAT  ='".$row->LOCAT."'
							TAXNO  ='".$row->TAXNO."'
							REFNO  ='".$row->REFNO."'
							STRNO  ='".$row->STRNO."'
							TAXDT  ='".$this->Convertdate(2,$row->TAXDT)."'
							REFDT  ='".$this->Convertdate(2,$row->REFDT)."'
							NETAMT ='".number_format($row->NETAMT,2)."'
							VATAMT ='".number_format($row->VATAMT,2)."'
							TOTAMT ='".number_format($row->TOTAMT,2)."'
							FLAG   ='".$row->FLAG."'
						><b>เลือก</b></td>
						<td>".$row->LOCAT."</td>
						<td>".$row->TAXNO."</td>
						<td>".$row->REFNO."</td>
						<td>".$row->FLAG."</td>
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
							<th>ออกโดยสาขา</th>
							<th>เลขที่ใบลดหนี้</th>
							<th>อ้างถีงใบกำกับ</th>
							<th>#</th>
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
	function getTAXNO_VP(){
		$sess = $this->session->userdata('cbjsess001');
		$dataSearch = trim($_REQUEST["q"]);
		$locat = $_REQUEST['locat'];
		$taxno = $_REQUEST['TAXNO'];
		if($taxno == "ShuntVP"){
			$sql = "
				select TAXNO,TAXDT,LOCAT,TSALE,CONTNO,CUSCOD,NAME1,NAME2,TOTAMT,FPAY,FLAG 
				from {$this->MAuth->getdb('TAXTRAN')} where TSALE in ('C','F') and LOCAT = '".$locat."' 
				and (FLAG <> 'C' or FLAG is null) and TAXNO like '%".$dataSearch."%'
			";
		}else if($taxno == "SendVP"){
			$sql = "
				select TAXNO from {$this->MAuth->getdb('TAXTRAN')} where LOCAT = '".$locat."' 
				and (FLAG <> 'C' or FLAG is null) and TSALE in ('A') and TAXNO like '%".$dataSearch."%' 
			";
		}else if($taxno == "MoneyVP"){
			$sql = "
				select top 100 TAXNO from {$this->MAuth->getdb('TAXTRAN')} where 
				TAXNO like '%".$dataSearch."%' --and LOCAT = '".$locat."'
			";
		}
        $query = $this->db->query($sql);
        $json = array();
        if($query->row()){
            foreach($query->result() as $row){
                $json[] = array(
                    "id" => $row->TAXNO,
                    "text" => $row->TAXNO,					 
                );
            }
        }
        echo json_encode($json);
	}
	function getSTRNO_VP(){
		$sess = $this->session->userdata('cbjsess001');
		$dataSearch = trim($_REQUEST["q"]);
		$locat = $_REQUEST['locat'];
		$contno = $_REQUEST['contno'];
		$json = array();
		if($contno !== ''){
			$sql = "
				select STRNO from {$this->MAuth->getdb('INVTRAN')} 
				where CONTNO = '".$contno."' and STRNO like '%".$dataSearch."%'
			";
			//echo $sql; exit;
			$query = $this->db->query($sql);
			if($query->row()){
				foreach($query->result() as $row){
					$json[]= array(
						"id" => $row->STRNO,
						"text" => $row->STRNO,
					);
				}
			}
		}
		echo json_encode($json);
	}
	function SearchDebtPrice(){
		$sess = $this->session->userdata('cbjsess001');
		$locat    = $_POST['locat'];
		$taxno    = $_POST['taxno'];
		$refno    = $_POST['refno'];
		$vatprice = $_POST['vatprice'];
		if($vatprice == 'debtshunt'){
			$sql = "
				select LOCAT,REFNO,STRNO,TAXNO,convert(varchar(8),TAXDT,112) as TAXDT,INPDT
				,convert(varchar(8),REFDT,112) as REFDT,CONTNO,CUSCOD,SNAM,NAME1,NAME2,TSALE
				,DESCP,NETAMT,VATAMT,TOTAMT,FPAY,FLAG from {$this->MAuth->getdb('TAXTRAN')} 
				where TSALE in ('C','F') and TAXTYP = '6'
			";
		}else if($vatprice == "debtmoney"){
			$sql = "
				select LOCAT,REFNO,STRNO,TAXNO,convert(varchar(8),TAXDT,112) as TAXDT,INPDT
				,convert(varchar(8),REFDT,112) as REFDT,CONTNO,CUSCOD,SNAM,NAME1,NAME2,TSALE
				,DESCP,NETAMT,VATAMT,TOTAMT,FPAY,FLAG from {$this->MAuth->getdb('TAXTRAN')}
				where TAXTYP = '8' and LOCAT like '%".$locat."%' and TAXNO like '%".$taxno."%' 
				and REFNO like '%".$refno."%' order by INPDT
			";
		}		
        $query = $this->db->query($sql);
		$html = "";
		$NRow = 1;
		if($query->row()){
			foreach($query->result() as $row){
				$html .="
					<tr class='trow' seq='".$NRow."'>
						<td style='cursor:pointer;' class='getit' seq='".$NRow++."'
							LOCAT  ='".$row->LOCAT."'
							REFNO  ='".$row->REFNO."'
							STRNO  ='".$row->STRNO."'
							TAXNO  ='".$row->TAXNO."'
							TAXDT  ='".$this->Convertdate(2,$row->TAXDT)."'
							REFDT  ='".$this->Convertdate(2,$row->REFDT)."'
							CONTNO ='".$row->CONTNO."'
							CUSCOD ='".$row->CUSCOD."'
							SNAM   ='".$row->SNAM."'
							NAME1  ='".$row->NAME1."'
							NAME2  ='".$row->NAME2."'
							TSALE  ='".$row->TSALE."'
							DESCP  ='".$row->DESCP."'
							NETAMT ='".number_format($row->NETAMT,2)."'
							VATAMT ='".number_format($row->VATAMT,2)."'
							TOTAMT ='".number_format($row->TOTAMT,2)."'
							FLAG   ='".$row->FLAG."'
						><b>เลือก</b></td>
						<td>".$row->LOCAT."</td>
						<td>".$row->TAXNO."</td>
						<td>".$row->REFNO."</td>
						<td>".$row->FLAG."</td>
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
							<th>ออกโดยสาขา</th>
							<th>เลขที่ใบลดหนี้</th>
							<th>อ้างถีงใบกำกับ</th>
							<th>#</th>
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
	function getTAXNO_Reduce(){
		$sess = $this->session->userdata('cbjsess001');
		$dataSearch = trim($_REQUEST["q"]);	
		$locat = $_REQUEST['locat'];
		$sql = "
			select TAXNO from {$this->MAuth->getdb('TAXTRAN')}
			where LOCAT = '".$locat."' and TAXTYP between '1' and '9' and TAXNO like '%".$dataSearch."%'
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$json[]= array(
					"id" => $row->TAXNO,
					"text" => $row->TAXNO,
				);
			}
		}
		echo json_encode($json);
	}
	function getRESONCD(){
		$sess = $this->session->userdata('cbjsess001');
		$dataSearch = trim($_REQUEST["q"]);
		$sql = "
			select RESONCD,RESNDES from {$this->MAuth->getdb('SETRESON')} where RESONCD like '%".$dataSearch."%'
			and RESNDES like '%".$dataSearch."%'
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$json[]= array(
					"id" => str_replace(chr(0),'',$row->RESONCD),
					"text" => str_replace(chr(0),'',$row->RESONCD),
				);
			}
		}
		echo json_encode($json);
	}
	function getCONTNO_V(){
		$sess = $this->session->userdata('cbjsess001');
		$dataSearch = trim($_REQUEST["q"]);
		$locat = $_REQUEST['locat'];
		$vatstop = $_REQUEST['vatstop'];
		$status = "";
		if($vatstop == "save"){
			$status = "<>";
		}else{
			$status = "=";
		}
		$sql = "
			select A.LOCAT,A.CONTNO,A.CUSCOD,C.SNAM,C.NAME1,C.NAME2,A.STRNO,A.RESVNO,I.REGNO,I.CURSTAT,A.BILLCOLL 
			,C.SNAM+C.NAME1+' '+C.NAME2+'('+A.CONTNO+')' as CONTNOC from {$this->MAuth->getdb('ARMAST')} A
			left join {$this->MAuth->getdb('CUSTMAST')} C on A.CUSCOD = C.CUSCOD
			left join {$this->MAuth->getdb('INVTRAN')} I on A.STRNO = I.STRNO
			where A.FLSTOPV ".$status." 'S' and A.LOCAT = '".$locat."' and A.CONTNO like '%".$dataSearch."%' order by A.CONTNO
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		$json = array();
		if($query->row()){
			foreach($query->result() as $row){
				$json[]= array(
					"id" => str_replace(chr(0),'',$row->CONTNO),
					"text" => str_replace(chr(0),'',$row->CONTNOC),
				);
			}
		}
		echo json_encode($json);
	}
	function getSearchfromstopvat(){
		$html = "
			<div class='row'>
				<div class='col-sm-12'>
					<div class='form-group'>
						เลขที่หยุด Vat
						<input type='text' id='stopvno' class='form-control'>
					</div>
				</div>
				<div class='col-sm-12'>
					<button id='btnSearchResult' class='btn btn-primary btn-block'><span class='glyphicon glyphicon-search'>ค้นหา</span></button>
				</div>
				<br>
				<div id='StopVat_result' class='col-sm-12'></div>
			</div>
		";
		$response = array("html"=>$html);
		echo json_encode($response);
	}
	function getResultstopvat(){
		$sess = $this->session->userdata('cbjsess001');
		$stopvno  = $_REQUEST['stopvno'];
		$sql = "
			select STOPVNO,LOCAT,convert(varchar(8),STOPDT,112) as STOPDT,EXP_PRD,USERID,CANCELID,FRMCONTNO,TOCONTNO
			from {$this->MAuth->getdb('STOPVHD')} where STOPVNO like '%".$stopvno."%'
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
							STOPVNO   = '".$row->STOPVNO."'
							LOCAT     = '".$row->LOCAT."'
							STOPDT    = '".$this->Convertdate(2,$row->STOPDT)."'
							EXP_PRD   = '".$row->EXP_PRD."'
							USERID    = '".$row->USERID."'
							CANCELID  = '".$row->CANCELID."'
							FRMCONTNO = '".$row->FRMCONTNO."'
							TOCONTNO  = '".$row->TOCONTNO."'
						><b>เลือก</b></td>
						<td>".$row->STOPVNO."</td>
						<td>".$row->LOCAT."</td>
						<td>".$this->Convertdate(2,$row->STOPDT)."</td>
						<td>".$row->EXP_PRD."</td>
						<td>".$row->USERID."</td>
						<td>".$row->CANCELID."</td>
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
							<th>เลขที่หยุด Vat</th>
							<th>รหัสสาขา</th>
							<th>วันที่หยุด Vat</th>
							<th>จำนวนงวด</th>
							<th>ผู้ทำการ</th>
							<th>ผู้ยกเลิก</th>
						</tr>
					</thead>	
					<tbody>
						".$html."				
					</tbody>
				</table>
			</div>
		";
		$response['html'] = $html; 
		echo json_encode($response);
	}
	function getResultstopvat_TR(){
		$sess = $this->session->userdata('cbjsess001');
		$STOPVTR  = !isset($_REQUEST['STOPVTR']) ? '' : $_REQUEST['STOPVTR'];
		
		$sql = "
			select A.CONTNO,CONVERT(varchar(8),A.STOPDT,112) as STOPDT,A.EXP_PRD
			,A.LOCAT,C.SNAM+C.NAME1+' '+C.NAME2 as CUSNAM from {$this->MAuth->getdb('STOPVTR')} A
			left join {$this->MAuth->getdb('CUSTMAST')} C on A.CUSCOD = C.CUSCOD 
			where A.STOPVNO = '".$STOPVTR."'	
		";
		//echo $sql; exit; 
		$query = $this->db->query($sql);
		$tr_stopvat = ""; $i = 0;
		if($query->row()){
			foreach($query->result() as $row){$i++;
				$tr_stopvat .= "
					<tr class='trow' seq='old'>
						<td>
							<input type='checkbox' id='checkstopvat' class='form-check-input checklist' style='cursor:pointer;max-width:20px;max-height:10px;' checked>
						</td>
						<td>".$row->CONTNO."</td>
						<td>".$row->CUSNAM."</td>
						<td>".$row->EXP_PRD."</td>
						<td>".$this->Convertdate(2,$row->STOPDT)."</td>
					</tr>
				";	
			}
		}
		$response['countrow'] = $i;
		$response['tr_stopvat'] = $tr_stopvat;
		echo json_encode($response);
	}
	function getSearchfromcancelstopvat(){
		$html = "
			<div class='row'>
				<div class='col-sm-12'>
					<div class='form-group'>
						เลขที่ยกเลิกหยุด Vat
						<input type='text' id='canstvno' class='form-control'>
					</div>
				</div>
				<div class='col-sm-12'>
					<button id='btnSearchResult' class='btn btn-primary btn-block'><span class='glyphicon glyphicon-search'>ค้นหา</span></button>
				</div>
				<br>
				<div id='StopVat_result' class='col-sm-12'></div>
			</div>
		";
		$response = array("html"=>$html);
		echo json_encode($response);
	}
	function getResultcancelstopvat(){
		$sess = $this->session->userdata('cbjsess001');
		$canstvno  = $_REQUEST['canstvno'];
		$sql = "
			select CANSTVNO,LOCAT,convert(varchar(8),STOPDT,112) as STOPDT,EXP_PRD,USERID,CANCELID,FRMCONTNO,TOCONTNO
			from {$this->MAuth->getdb('CANSTVHD')} where CANSTVNO like '%".$canstvno."%'
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
							CANSTVNO   = '".$row->CANSTVNO."'
							LOCAT     = '".$row->LOCAT."'
							STOPDT    = '".$this->Convertdate(2,$row->STOPDT)."'
							EXP_PRD   = '".$row->EXP_PRD."'
							USERID    = '".$row->USERID."'
							CANCELID  = '".$row->CANCELID."'
							FRMCONTNO = '".$row->FRMCONTNO."'
							TOCONTNO  = '".$row->TOCONTNO."'
						><b>เลือก</b></td>
						<td>".$row->CANSTVNO."</td>
						<td>".$row->LOCAT."</td>
						<td>".$this->Convertdate(2,$row->STOPDT)."</td>
						<td>".$row->EXP_PRD."</td>
						<td>".$row->USERID."</td>
						<td>".$row->CANCELID."</td>
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
							<th>เลขที่ยกเลิกหยุด Vat</th>
							<th>รหัสสาขา</th>
							<th>วันที่ยกเลิกหยุด Vat</th>
							<th>จำนวนงวด</th>
							<th>ผู้ทำการ</th>
							<th>ผู้ยกเลิก</th>
						</tr>
					</thead>	
					<tbody>
						".$html."				
					</tbody>
				</table>
			</div>
		";
		$response['html'] = $html; 
		echo json_encode($response);
	}
	function getResultcancelstopvat_TR(){
		$sess = $this->session->userdata('cbjsess001');
		$CANSTVNO  = $_REQUEST['CANSTVNO'];
		
		$sql = "
			select A.CONTNO,CONVERT(varchar(8),A.STOPDT,112) as STOPDT,A.EXP_PRD
			,A.LOCAT,C.SNAM+C.NAME1+' '+C.NAME2 as CUSNAM from {$this->MAuth->getdb('CANSTVTR')} A
			left join {$this->MAuth->getdb('CUSTMAST')} C on A.CUSCOD = C.CUSCOD 
			where A.CANSTVNO = '".$CANSTVNO."'	
		";
		//echo $sql; exit; 
		$query = $this->db->query($sql);
		$tr_stopvat = ""; $i = 0;
		if($query->row()){
			foreach($query->result() as $row){$i++;
				$tr_stopvat .= "
					<tr class='trow' seq='old'>
						<td>
							<input type='checkbox' id='checkstopvat' class='form-check-input checklist' style='cursor:pointer;max-width:20px;max-height:10px;' checked>
						</td>
						<td>".$row->CONTNO."</td>
						<td>".$row->CUSNAM."</td>
						<td>".$row->EXP_PRD."</td>
						<td>".$this->Convertdate(2,$row->STOPDT)."</td>
					</tr>
				";	
			}
		}
		$response['countrow'] = $i;
		$response['tr_stopvat'] = $tr_stopvat;
		echo json_encode($response);
	}
	function getReportCONTNOVAT(){
		$sess 		   = $this->session->userdata('cbjsess001');
		$dataSearch    = trim($_REQUEST['q']);
		
		$sql = "
			select top 100 CONTNO,CUSNAM from (
				select A.CONTNO,C.SNAM+C.NAME1+' '+C.NAME2+'('+A.CONTNO+')' as CUSNAM 
				from {$this->MAuth->getdb('ARMAST')} A
				left join {$this->MAuth->getdb('CUSTMAST')} C on A.CUSCOD = C.CUSCOD
				left join {$this->MAuth->getdb('INVTRAN')} I on A.STRNO = I.STRNO
			)a where 1=1 and CONTNO like '%".$dataSearch."%' or CUSNAM like '%".$dataSearch."%'  order by CONTNO
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$json[] = array(
					"id" => $row->CONTNO,
					"text" => $row->CUSNAM
				);
			}
		}
		echo json_encode($json);
	}
	function getSTRNO_ST(){
		$sess 		   = $this->session->userdata('cbjsess001');
		$dataSearch    = trim($_REQUEST['q']);
		$no 		   = $_REQUEST['no'];
		if($no == "sno"){
			$sql = "
				select top 20 STRNO from {$this->MAuth->getdb('INVTRAN')}
				where STRNO like '%".$dataSearch."%' order by STRNO desc
			";
		}else{
			$sql = "
				select top 20 STRNO from {$this->MAuth->getdb('INVTRAN')}
				where STRNO like '%".$dataSearch."%' and TSALE <> '' order by STRNO desc
			";
		}
		
		//echo $sql; exit;
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$json[] = array(
					"id" => str_replace(chr(0),'',$row->STRNO),
					"text" => str_replace(chr(0),'',$row->STRNO)
				);
			}
		}
		echo json_encode($json);
	}
	function getTYPECOD(){
		$sess 		   = $this->session->userdata('cbjsess001');
		$dataSearch    = trim($_REQUEST['q']);
		
		$sql = "
			select TYPECOD from {$this->MAuth->getdb('SETTYPE')}
			where TYPECOD like '%".$dataSearch."%' order by TYPECOD
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$json[] = array(
					"id" => str_replace(chr(0),'',$row->TYPECOD),
					"text" => str_replace(chr(0),'',$row->TYPECOD)
				);
			}
		}
		echo json_encode($json);
	}
	function getMODELCOD(){
		$sess 		   = $this->session->userdata('cbjsess001');
		$dataSearch    = trim($_REQUEST['q']);
		
		$sql = "
			select top 50 MODELCOD from {$this->MAuth->getdb('SETMODEL')}
			where MODELCOD like '%".$dataSearch."%' order by MODELCOD
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$json[] = array(
					"id" => str_replace(chr(0),'',$row->MODELCOD),
					"text" => str_replace(chr(0),'',$row->MODELCOD)
				);
			}
		}
		echo json_encode($json);
	}
	function getBAABCOD(){
		$sess 		   = $this->session->userdata('cbjsess001');
		$dataSearch    = trim($_REQUEST['q']);
		
		$sql = "
			select top 100 BAABCOD,BAABCOD+' ('+TYPECOD+')' as BAABNAM from {$this->MAuth->getdb('SETBAAB')}
			where BAABCOD like '".$dataSearch."%' order by BAABCOD
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$json[] = array(
					"id" => str_replace(chr(0),'',$row->BAABCOD),
					"text" => str_replace(chr(0),'',$row->BAABNAM)
				);
			}
		}
		echo json_encode($json);
	}
	function getCOLORCOD(){
		$sess 		   = $this->session->userdata('cbjsess001');
		$dataSearch    = trim($_REQUEST['q']);
		
		$sql = "
			select top 20 COLORCOD from {$this->MAuth->getdb('SETCOLOR')}
			where COLORCOD like '%".$dataSearch."%' order by COLORCOD
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$json[] = array(
					"id" => str_replace(chr(0),'',$row->COLORCOD),
					"text" => str_replace(chr(0),'',$row->COLORCOD)
				);
			}
		}
		echo json_encode($json);
	}
	function getCCCOD(){
		$sess 		   = $this->session->userdata('cbjsess001');
		$dataSearch    = trim($_REQUEST['q']);
		
		$sql = "
			select top 20 CCCOD from {$this->MAuth->getdb('SETCC')}
			where CCCOD like '%".$dataSearch."%' order by CCCOD
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$json[] = array(
					"id" => str_replace(chr(0),'',$row->CCCOD),
					"text" => str_replace(chr(0),'',$row->CCCOD)
				);
			}
		}
		echo json_encode($json);
	}
	function getTableclearnull(){
		$sess = $this->session->userdata('cbjsess001');
		$dataSearch = $_REQUEST['q'];
		
		$sql = "
			select A.NAME from ".$this->sess["db"].".dbo.sysobjects A
			left join ".$this->sess["db"].".dbo.syscolumns B on A.ID = B.ID
			left join ".$this->sess["db"].".dbo.systypes C on B.xtype = C.xtype 
			where A.NAME like '%".$dataSearch."%' and A.type = 'U' group by A.NAME
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$json[] = array(
					"id" => $row->NAME,
					"text" => $row->NAME
				);
			}
		}
		echo json_encode($json);
	}
	function getOPTCODE(){
		$sess = $this->session->userdata('cbjsess001');
		$dataSearch = $_REQUEST['q'];
		$locat = (!isset($_REQUEST["LOCAT"]) ? "":$_REQUEST["LOCAT"]);
		$cond = "";
		if($locat !== ""){
			$cond .=" and LOCAT = '".$locat."'";
		}
		$sql = "
			select LOCAT,OPTCODE,OPTNAME,OPTNAM from (
				select LOCAT,OPTCODE,OPTNAME,OPTCODE+'('+OPTNAME+')' as OPTNAM 
				from {$this->MAuth->getdb('OPTMAST')} where 1=1 ".$cond."
			)a where OPTCODE like '".$dataSearch."%' or OPTNAME like '".$dataSearch."%' 
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$json[] = array(
					"id" => $row->OPTCODE,
					"text" => $row->OPTNAM
				);
			}
		}
		echo json_encode($json);
	}
	function getformCUSTMAST(){
		$html = "
			<div class='row'>
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
				<div class='col-sm-4'>
					<div class='form-group'>
						เลขที่บัตร
						<input type='text' id='idno' class='form-control'>
					</div>
				</div>
				<div class='col-sm-12'>
					<button id='btn_search' class='btn btn-primary btn-block'><span class='glyphicon glyphicon-search'>ค้นหา</span></button>
				</div>
				<br>
				<div id='cus_result' class='col-sm-12'></div>
			</div>
		";
		$response = array("html"=>$html);
		echo json_encode($response);
	}
	function getSearchCUSTMAST(){
		$name1  = $_POST['name1'];
		$name2  = $_POST['name2'];
		$idno   = $_POST['idno'];
		
		$cond = "";
		if($name1 != ""){
			$cond .= "and NAME1 like '".$name1."%'";
		}
		if($name2 != ""){
			$cond .= "and NAME2 like '".$name2."%'";
		}		
		if($idno != ""){
			$cond .= "and IDNO like '".$idno."%'"; 
		}
		$sql = "
			select top 100 CUSCOD,IDNO,GRADE
				,SNAM+NAME1+' '+NAME2 as CUSNAME1,NAME1,NAME2
				,SNAM+NAME1+' '+NAME2+' ('+CUSCOD+')' as CUSNAME2
			from {$this->MAuth->getdb('CUSTMAST')} where 1=1 ".$cond."
			order by NAME1,NAME2
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
							CUSCOD    ='".$row->CUSCOD."'
							CUSNAME2  ='".$row->CUSNAME2."'
						><b>เลือก</b></td>
						<td>".$row->CUSCOD."</td>
						<td>".$row->CUSNAME1."</td>
						<td>".$row->GRADE."</td>
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
							<th>เกรด</th>
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
	function getOPTCODE_ACS(){
		$sess = $this->session->userdata('cbjsess001');
		$dataSearch = $_REQUEST['q'];
		$add_locat = (!isset($_REQUEST['add_locat']) ? "":$_REQUEST['add_locat']);
		
		$sql = "
			select top 100 OPTCODE,OPTNAME,OPTCODE+'('+OPTNAME+')' as OPTNAM 
			from {$this->MAuth->getdb('OPTMAST')} where LOCAT like '".$add_locat."%' and 
			(OPTCODE like '".$dataSearch."%' or OPTNAME like '".$dataSearch."%') 
			ORDER BY OPTCODE
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$json[] = array(
					"id" => str_replace(chr(0),'',$row->OPTCODE),
					"text" => str_replace(chr(0),'',$row->OPTNAM)
				);
			}
		}
		echo json_encode($json);
	}
	function getfromSTRNO(){
		$html = "
			<div class='row'>
				<div class='col-sm-4'>
					<div class='form-group'>
						เลขตัวถัง
						<input type='text' id='s_strno' class='form-control'>
					</div>
				</div>
				<div class='col-sm-4'>
					<div class='form-group'>
						ยี่ห้อ
						<input type='text' id='s_type' class='form-control'>
					</div>
				</div>
				<div class='col-sm-4'>
					<div class='form-group'>
						รุ่น
						<input type='text' id='s_model' class='form-control'>
					</div>
				</div>
				<div class='col-sm-12'>
					<button id='str_search' class='btn btn-primary btn-block'><span class='glyphicon glyphicon-search'>ค้นหา</span></button>
				</div>
				<br>
				<div id='str_result' class='col-sm-12'></div>
			</div>
		";
		$response = array("html"=>$html);
		echo json_encode($response);
	}
	function getResultSTRNO(){
		$sess = $this->session->userdata('cbjsess001');
		$s_strno  = $_REQUEST['s_strno'];
		$s_type   = $_REQUEST['s_type'];
		$s_model  = $_REQUEST['s_model'];
		$cond = "";
		if($s_strno !== ""){
			$cond .=" and STRNO like '".$s_strno."%'";
		}
		if($s_type !== ""){
			$cond .=" and TYPE like '".$s_type."%'";
		}
		if($s_model !== ""){
			$cond .=" and MODEL like '".$s_model."%'";
		}
		
		$sql = "
			select top 100 STRNO,TYPE,MODEL,STAT 
			from {$this->MAuth->getdb('INVTRAN')} 
			where 1=1 ".$cond." 
		";
		$query = $this->db->query($sql);
		$html = "";
		$NRow = 1;
		if($query->row()){
			foreach($query->result() as $row){
				$html .="
					<tr class='trow' seq='".$NRow."'>
						<td style='cursor:pointer;' class='getit' seq='".$NRow++."'
							STRNO  ='".$row->STRNO."'
						><b>เลือก</b></td>
						<td>".$row->STRNO."</td>
						<td>".$row->TYPE."</td>
						<td>".$row->MODEL."</td>
						<td>".$row->STAT."</td>
					</tr>
				";
			}
		}
		$html = "
			<div id='tbstrno' class='col-sm-12' style='height:100%;overflow:auto;background-color:#eee;'>
				<table id='data-table-example2' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='100%'>
					<thead>
						<tr>
							<th>#</th>
							<th>เลขตัวถัง</th>
							<th>ยี่ห้อ</th>
							<th>รุ่น</th>
							<th>สถานะ</th>
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
	function getformLOCAT(){
		$html = "
			<div class='row'>
				<div class='col-sm-4'>
					<div class='form-group'>
						รหัสสาขา
						<input type='text' id='locat' class='form-control'>
					</div>
				</div>
				<div class='col-sm-4'>
					<div class='form-group'>
						ชื่อสาขา
						<input type='text' id='locatnm' class='form-control'>
					</div>
				</div>
				<div class='col-sm-4'>
					<div class='form-group'>
						รหัสย่อ
						<input type='text' id='shortl' class='form-control'>
					</div>
				</div>
				
				<div class='col-sm-12'>
					<button id='btnsearch' class='btn btn-primary btn-block'><span class='glyphicon glyphicon-search'>ค้นหา</span></button>
				</div>
				<br>
				<div id='locat_result' class='col-sm-12'></div>
			</div>
		";
		$response = array("html"=>$html);
		echo json_encode($response);
	}
	function getResultLOCAT(){
		$locat    = $_REQUEST['locat'];
		$locatnm  = $_REQUEST['locatnm'];
		$shortl   = $_REQUEST['shortl'];
		
		$cond = "";
		if($locat !== ""){
			$cond .=" and LOCATCD like '".$locat."%'";
		}
		if($locatnm !== ""){
			$cond .=" and LOCATNM like '".$locatnm."%'";
		}
		if($shortl !== ""){
			$cond .=" and SHORTL like '".$shortl."%'";
		}
		
		$sql = "
			select LOCATCD,LOCATNM,SHORTL,'('+LOCATCD+') '+LOCATNM as LOCATNAM
			from {$this->MAuth->getdb('INVLOCAT')} 
			where 1=1 ".$cond." 
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
							LOCAT ='".$row->LOCATCD."' LOCATNAM = '".$row->LOCATNAM."' 
						><b>เลือก</b></td>
						<td>".$row->LOCATCD."</td>
						<td>".$row->LOCATNM."</td>
						<td>".$row->SHORTL."</td>
					</tr>
				";
			}
		}
		$html = "
			<div id='tbstrno' class='col-sm-12' style='height:100%;overflow:auto;background-color:#eee;'>
				<table id='data-table-example2' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='100%'>
					<thead>
						<tr>
							<th>#</th>
							<th>รหัสสาขา</th>
							<th>ชื่อสาขา</th>
							<th>รหัสย่อ</th>
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
	function getAPCODE(){
		$sess = $this->session->userdata('cbjsess001');
		$dataSearch = trim($_REQUEST['q']);
		$dataNow = (!isset($_REQUEST["now"]) ? "" : $_REQUEST["now"]);
		
		$sql = "
			select APCODE,'('+APCODE+') '+APNAME as APNAME 
			from {$this->MAuth->getdb('APMAST')} 
			where APCODE = '".$dataNow."' collate Thai_ci_as
			union
			select top 20 APCODE,'('+APCODE+') '+APNAME as APNAME
			from {$this->MAuth->getdb('APMAST')} 
			where APCODE like '".$dataNow."%' collate Thai_ci_as 
			or APNAME like '".$dataSearch."%' collate Thai_ci_as
			order by APCODE
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$json[] = ['id'=>str_replace(chr(0),'',$row->APCODE), 'text'=>str_replace(chr(0),'',$row->APNAME)];
			}
		}
		echo json_encode($json);
	}
	function getformOPTCODE(){
		$html = "
			<div class='row'>
				<div class='col-sm-4'>
					<div class='form-group'>
						รหัสอุปกรณ์
						<input type='text' id='optcode' class='form-control'>
					</div>
				</div>
				<div class='col-sm-4'>
					<div class='form-group'>
						ชื่อชื่ออุปกรณ์
						<input type='text' id='optname' class='form-control'>
					</div>
				</div>
				<div class='col-sm-4'>
					<div class='form-group'>
						สาขา
						<input type='text' id='locat' class='form-control'>
					</div>
				</div>
				<div class='col-sm-12'>
					<button id='btn_searchopt' class='btn btn-primary btn-block'><span class='glyphicon glyphicon-search'>ค้นหา</span></button>
				</div>
				<br>
				<div id='opt_result' class='col-sm-12'></div>
			</div>
		";
		$response = array("html"=>$html);
		$response["locat"] = $this->sess["branch"];
		echo json_encode($response);
	}
	function getSearchOPTCODE(){
		$optcode = $_REQUEST["optcode"];
		$optname = $_REQUEST["optname"];
		$locat = $_REQUEST["locat"];
		
		$cond = "";
		if($optcode !== ""){
			$cond .=" and OPTCODE like '".$optcode."%'";
		}
		if($optname !== ""){
			$cond .=" and OPTNAME like '".$optname."%'";
		}
		if($locat !== ""){
			$cond .=" and LOCAT like '".$locat."%'";
		}
		
		$sql = "
			select OPTCODE,OPTNAME,LOCAT from {$this->MAuth->getdb('OPTMAST')}
			where 1=1 ".$cond."
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		$html = "";
		$NRow = 1;
		if($query->row()){
			foreach($query->result() as $row){
				$html .="
					<tr class='trows' seqs='".$NRow."'>
						<td style='cursor:pointer;' class='getitopt' seqs='".$NRow++."'
							OPTCODE = '".str_replace(chr(0),"",$row->OPTCODE)."' 
							OPTNAME = '".str_replace(chr(0),"",$row->OPTNAME)."' 
						><b>เลือก</b></td>
						<td>".$row->OPTCODE."</td>
						<td>".$row->OPTNAME."</td>
						<td>".$row->LOCAT."</td>
					</tr>
				";
			}
		}
		$html = "
			<div id='table_optcode' class='col-sm-12' style='height:100%;overflow:auto;background-color:#eee;'>
				<table id='data-table-optcode' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='100%'>
					<thead>
						<tr>
							<th>#</th>
							<th>รหัสอุปกรณ์</th>
							<th>ชื่ออุปกรณ์</th>
							<th>สาขา</th>
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
	function changeLOCAT(){
		$response = array();
		$locat  = (!isset($_REQUEST["locat"]) ? "":$_REQUEST["locat"]);
		
		$sql = "
			select LOCATCD,LOCATNM from {$this->MAuth->getdb('INVLOCAT')}
			where LOCATCD = '".$locat."'
		";
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$response["locatnm"] = $row->LOCATNM;
			}
		}else{
			$response["locatnm"] = "";
		}
		echo json_encode($response);
	}
	function getFINCODE(){
		$dataSearch = trim($_REQUEST['q']);
		$dataNow = (!isset($_REQUEST["now"]) ? "" : $_REQUEST["now"]);
		
		$sql = "
			select FINCODE,FINNAME 
			from {$this->MAuth->getdb('FINMAST')}
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$json[] = [
					'id'=>str_replace(chr(0),'',$row->FINCODE)
					,'text'=>str_replace(chr(0),'',$row->FINNAME)
				];
			}
		}
		echo json_encode($json);
	}
}
	