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
	
	function getUSERS(){
		$sess = $this->session->userdata('cbjsess001');
		$dataSearch = trim($_REQUEST['q']);
		$dataNow = (!isset($_REQUEST["now"]) ? "" : $_REQUEST["now"]);
		
		$sql = "
			select USERID,USERNAME+' ('+USERID+')' as USERNAME from {$this->MAuth->getdb('PASSWRD')}
			where 1=1 and USERID='".$dataNow."' collate Thai_CI_AS 
			
			union
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
		
		$json = array();
		if($query->row()){
			foreach($query->result() as $row){
				$json[] = ['id'=>$row->IDNo, 'text'=>$row->Name];
			}
		}
		
		echo json_encode($json);
	}
	
	function getCUSTOMERS(){
		$sess = $this->session->userdata('cbjsess001');
		$dataSearch = trim($_REQUEST['q']);
		$dataNow = (!isset($_REQUEST["now"]) ? "" : $_REQUEST["now"]);
		
		$sql = "
			select top 20 CUSCOD,SNAM+NAME1+' '+NAME2+' ('+CUSCOD+')'+'-'+GRADE as CUSNAME from {$this->MAuth->getdb('CUSTMAST')}
			where CUSCOD = '".$dataNow."' collate Thai_CI_AS 			
			
			union
			select top 20 CUSCOD,SNAM+NAME1+' '+NAME2+' ('+CUSCOD+')'+'-'+GRADE as CUSNAME from {$this->MAuth->getdb('CUSTMAST')}
			where CUSCOD in (
				select CUSCOD from {$this->MAuth->getdb('CUSTMAST')}
				where CUSCOD like '%".$dataSearch."%' collate Thai_CI_AS 
					or NAME1+' '+NAME2+' '+IDNO like '%".$dataSearch."%' collate Thai_CI_AS
			)
			order by CUSCOD
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$json[] = ['id'=>$row->CUSCOD, 'text'=>$row->CUSNAME];
			}
		}
		
		echo json_encode($json);
	}
	
	function getCUSTOMERSADDRNo(){
		$sess = $this->session->userdata('cbjsess001');
		$dataSearch = trim($_REQUEST['q']);
		$dataNow = (!isset($_REQUEST["now"]) ? "" : $_REQUEST["now"]);
		$cuscod = $_REQUEST["cuscod"];
		
		$sql = "
			select a.ADDRNO,'('+a.ADDRNO+') '+a.ADDR1+' '+a.ADDR2+' ต.'+a.TUMB
				+' อ.'+b.AUMPDES+' จ.'+c.PROVDES+' '+a.ZIP	as ADDRNODetails 			
			from {$this->MAuth->getdb('CUSTADDR')} a
			left join {$this->MAuth->getdb('SETAUMP')} b on a.AUMPCOD=b.AUMPCOD
			left join {$this->MAuth->getdb('SETPROV')} c on b.PROVCOD=c.PROVCOD
			where CUSCOD = '".$cuscod."' collate Thai_CI_AS and ADDRNO = '".$dataNow."' collate Thai_CI_AS
			
			union
			select a.ADDRNO,'('+a.ADDRNO+') '+a.ADDR1+' '+a.ADDR2+' ต.'+a.TUMB
				+' อ.'+b.AUMPDES+' จ.'+c.PROVDES+' '+a.ZIP	as ADDRNODetails
			from {$this->MAuth->getdb('CUSTADDR')} a
			left join {$this->MAuth->getdb('SETAUMP')} b on a.AUMPCOD=b.AUMPCOD
			left join {$this->MAuth->getdb('SETPROV')} c on b.PROVCOD=c.PROVCOD
			where CUSCOD = '".$cuscod."' collate Thai_CI_AS and '('+ADDRNO+') '+ADDR1+' '+ADDR2+' '+TUMB like '%".$dataSearch."%' collate Thai_CI_AS
			order by ADDRNO
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$json[] = ['id'=>$row->ADDRNO, 'text'=>$row->ADDRNODetails];
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
			select top 20 groupCode,groupCode+' ('+groupName+')' as groupName from YTKManagement.dbo.hp_groupuser
			where (groupCode like '%".$dataSearch."%' collate Thai_CI_AS
				or groupName like '%".$dataSearch."%' collate Thai_CI_AS)
				and groupCode <> 'MOD'
			order by groupCode
		"; 
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$json[] = ['id'=>$row->groupCode, 'text'=>$row->groupName];
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
	
	function getTYPES(){
		//รุ่นรถ
		$sess = $this->session->userdata('cbjsess001');
		$dataSearch = trim($_GET['q']);
		$dataNow = (!isset($_REQUEST["now"]) ? "" : $_REQUEST["now"]);
		
		$sql = "
			select TYPECOD from {$this->MAuth->getdb('SETTYPE')}
			where TYPECOD='".$dataNow."' collate Thai_CI_AS
			
			union
			select TYPECOD from {$this->MAuth->getdb('SETTYPE')}
			where TYPECOD like '%".$dataSearch."%' collate Thai_CI_AS
			order by TYPECOD
		"; 
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		$json = array();
		if($query->row()){
			foreach($query->result() as $row){
				//$json[] = ['id'=>$row->MODELCOD, 'text'=>$row->MODELCOD];
				$json[] = array('id'=>str_replace(chr(0),"",$row->TYPECOD), 'text'=>str_replace(chr(0),"",$row->TYPECOD));
			}
		}
		
		echo json_encode($json);
	}
	
	function getMODEL(){
		//รุ่นรถ
		$sess = $this->session->userdata('cbjsess001');
		$dataSearch = trim($_GET['q']);
		$dataNow = (!isset($_REQUEST["now"]) ? "" : $_REQUEST["now"]);
		$TYPECOD = $_REQUEST['TYPECOD'];
		
		$sql = "
			select top 100 MODELCOD from {$this->MAuth->getdb('SETMODEL')}
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
				$json[] = array('id'=>str_replace(chr(0),"",$row->MODELCOD), 'text'=>str_replace(chr(0),"",$row->MODELCOD));
			}
		}
		
		echo json_encode($json);
	}
	
	function getBAAB(){
		//แบบรถ
		$sess = $this->session->userdata('cbjsess001');
		$dataSearch = trim($_GET['q']);
		$dataNow = (!isset($_REQUEST["now"]) ? "" : $_REQUEST["now"]);
		$TYPECOD = $_REQUEST['TYPECOD'];
		$MODEL = $_REQUEST['MODEL'];
		
		$sql = "
			select BAABCOD from {$this->MAuth->getdb('SETBAAB')}
			where TYPECOD='".$TYPECOD."' and MODELCOD='".$MODEL."' and BAABCOD like '%".$dataSearch."%' collate Thai_CI_AS
			order by BAABCOD
		"; 
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		$json = array();
		if($query->row()){
			foreach($query->result() as $row){
				//$json[] = ['id'=>$row->MODELCOD, 'text'=>$row->MODELCOD];
				$json[] = array('id'=>str_replace(chr(0),"",$row->BAABCOD), 'text'=>str_replace(chr(0),"",$row->BAABCOD));
			}
		}
		
		echo json_encode($json);
	}
	
	function getCOLORSTOCK(){
		//สีรถ
		$sess = $this->session->userdata('cbjsess001');
		$dataSearch = trim($_GET['q']);
		$dataNow = (!isset($_REQUEST["now"]) ? "" : $_REQUEST["now"]);
		//$model = (!isset($_REQUEST["model"]) ? "" : $_REQUEST["model"]);
		//$baab  = (!isset($_REQUEST["baab"]) ? "" : $_REQUEST["baab"]);
		
		$sql = "
			select COLORCOD from {$this->MAuth->getdb('SETCOLOR')}
			where COLORCOD = '".$dataNow."' collate Thai_CI_AS
			union
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
				$json[] = array('id'=>str_replace(chr(0),"",$row->COLORCOD), 'text'=>str_replace(chr(0),"",$row->COLORCOD));
			}
		}
		
		echo json_encode($json);
	}
	
	function getCOLOR(){
		//สีรถ
		$sess = $this->session->userdata('cbjsess001');
		$dataSearch = trim($_GET['q']);
		$dataNow = (!isset($_REQUEST["now"]) ? "" : $_REQUEST["now"]);
		$model = (!isset($_REQUEST["model"]) ? "" : $_REQUEST["model"]);
		$baab  = (!isset($_REQUEST["baab"]) ? "" : $_REQUEST["baab"]);
		
		$sql = "
			/*
			select COLORCOD from {$this->MAuth->getdb('SETCOLOR')}
			where COLORCOD like '%".$dataSearch."%' collate Thai_CI_AS
			order by COLORCOD
			*/
			
			select COLOR as COLORCOD from {$this->MAuth->getdb('INVTRAN')}
			where MODEL='".$model."' and BAAB='".$baab."' and COLOR like '%".$dataSearch."%'
			union
			select COLOR from {$this->MAuth->getdb('HINVTRAN')}
			where MODEL='".$model."' and BAAB='".$baab."' and COLOR like '%".$dataSearch."%'
		"; 
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		$json = array();
		if($query->row()){
			foreach($query->result() as $row){
				$json[] = array('id'=>str_replace(chr(0),"",$row->COLORCOD), 'text'=>str_replace(chr(0),"",$row->COLORCOD));
			}
		}
		
		echo json_encode($json);
	}
	
	function getCC(){
		$sess = $this->session->userdata('cbjsess001');
		$dataSearch = trim($_GET['q']);
		$dataNow = (!isset($_REQUEST["now"]) ? "" : $_REQUEST["now"]);
		
		$sql = "
			select CCCOD from {$this->MAuth->getdb('SETCC')}
			where cast(CCCOD as varchar) = '".$dataNow."'
			union
			select CCCOD from {$this->MAuth->getdb('SETCC')}
			where cast(CCCOD as varchar) like '%".$dataSearch."%'
		"; 
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		$json = array();
		if($query->row()){
			foreach($query->result() as $row){
				$json[] = array('id'=>str_replace(chr(0),"",$row->CCCOD), 'text'=>str_replace(chr(0),"",$row->CCCOD));
			}
		}
		
		echo json_encode($json);
	}
	
	function getAPMAST(){
		$sess = $this->session->userdata('cbjsess001');
		$dataSearch = trim($_REQUEST['q']);
		$dataNow = (!isset($_REQUEST["now"]) ? "" : $_REQUEST["now"]);
		
		$sql = "
			select APCODE,APCODE+' '+APNAME as APNAME from {$this->MAuth->getdb('APMAST')}
			where APCODE='".$dataNow."'
			
			union all
			select APCODE,APCODE+' '+APNAME as APNAME from {$this->MAuth->getdb('APMAST')}
			where APCODE+' '+APNAME like '%".$dataSearch."%'
		";
		$query = $this->db->query($sql);
		
		$html = "";
		$json = array();
		if($query->row()){
			foreach($query->result() as $row){
				$json[] = array('id'=>str_replace(chr(0),"",$row->APCODE), 'text'=>str_replace(chr(0),"",$row->APNAME));
			}
		}
		
		echo json_encode($json);
	}
	
	function getRESVNO(){
		$sess = $this->session->userdata('cbjsess001');
		$dataSearch = trim($_REQUEST['q']);
		$dataNow = (!isset($_REQUEST["now"]) ? "" : $_REQUEST["now"]);
		$locat = $_REQUEST['locat'];
		
		$sql = "
			select RESVNO from {$this->MAuth->getdb('ARRESV')}
			where LOCAT = '".$locat."' collate Thai_CI_AS and RESVNO='".$dataNow."' collate Thai_CI_AS 
				and isnull(STRNO,'') <> '' and SDATE is null
			union
			select top 10 RESVNO from {$this->MAuth->getdb('ARRESV')}
			where LOCAT = '".$locat."' collate Thai_CI_AS and RESVNO like '".$dataSearch."%' collate Thai_CI_AS 
				and isnull(STRNO,'') <> '' and SDATE is null
			
			union
			--บิลจองจากสาขาอื่น แต่ลูกค้ามาออกรถกับอีกสาขา
			select top 10 a.RESVNO from {$this->MAuth->getdb('ARRESV')} a
			left join {$this->MAuth->getdb('INVTRAN')} b on a.STRNO=b.STRNO and a.RESVNO=b.RESVNO
			where b.CRLOCAT='".$locat."' and a.RESVNO like '".$dataSearch."%' collate Thai_CI_AS 
				and isnull(a.STRNO,'') <> '' and a.SDATE is null
				
			--order by RESVNO desc
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$json = array();
		if($query->row()){
			foreach($query->result() as $row){
				$json[] = ['id'=>str_replace(chr(0),'',$row->RESVNO), 'text'=>str_replace(chr(0),'',$row->RESVNO)];
			}
		}
		
		echo json_encode($json);
	}
	
	function getSTRNO(){
		$sess = $this->session->userdata('cbjsess001');
		$dataSearch = trim($_REQUEST['q']);
		$dataNow = (!isset($_REQUEST["now"]) ? "" : $_REQUEST["now"]);
		$locat = $_REQUEST['locat'];
		
		$strno = (!isset($_REQUEST['strno']) ? '':$_REQUEST['strno']);
		
		$GCODE = (!isset($_REQUEST["GCODE"]) ? "" : $_REQUEST["GCODE"]);
		$TYPE = (!isset($_REQUEST["TYPE"]) ? "" : $_REQUEST["TYPE"]);
		$MODEL = (!isset($_REQUEST["MODEL"]) ? "" : $_REQUEST["MODEL"]);
		$BAAB = (!isset($_REQUEST["BAAB"]) ? "" : $_REQUEST["BAAB"]);
		$COLOR = (!isset($_REQUEST["COLOR"]) ? "" : $_REQUEST["COLOR"]);
		$STAT = (!isset($_REQUEST["STAT"]) ? "" : $_REQUEST["STAT"]);
		
		$cond = "";
		if($GCODE != ""){
			$cond .= " and GCODE='".$GCODE."'";
		}
		if($TYPE != ""){
			$cond .= " and TYPE='".$TYPE."'";
		}
		if($MODEL != ""){
			$cond .= " and MODEL='".$MODEL."'";
		}
		if($BAAB != ""){
			$cond .= " and BAAB='".$BAAB."'";
		}
		if($COLOR != ""){
			$cond .= " and COLOR='".$COLOR."'";
		}
		if($STAT != ""){
			$cond .= " and STAT='".$STAT."'";
		}
		
		if($strno != ""){
			$strnolength = sizeof($strno);
			$str = "";
			for($i=0;$i<$strnolength;$i++){
				if($str != ""){ $str .= ","; }
				$str.= "'".$strno[$i]["strno"]."'";
			}
			$cond .= " and STRNO not in (".$str.")";
		}
		
		$sql = "
			select STRNO from {$this->MAuth->getdb('INVTRAN')}
			where CRLOCAT = '".$locat."' collate Thai_CI_AS 
				and STRNO='".$dataNow."' collate Thai_CI_AS
				--and FLAG='D' and isnull(CONTNO,'')='' and SDATE is null 
				--and isnull(RESVNO,'')=''
			union
			select top 20 STRNO from {$this->MAuth->getdb('INVTRAN')}
			where CRLOCAT = '".$locat."' collate Thai_CI_AS 
				and STRNO like '".$dataSearch."%' collate Thai_CI_AS 
				and FLAG='D' and isnull(CONTNO,'')='' and SDATE is null 
				and isnull(RESVNO,'')='' ".$cond."
			order by STRNO desc
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$json = array();
		if($query->row()){
			foreach($query->result() as $row){
				$json[] = ['id'=>str_replace(chr(0),'',$row->STRNO), 'text'=>str_replace(chr(0),'',$row->STRNO)];
			}
		}
		
		echo json_encode($json);
	}
	
	function getPAYDUE(){
		$sess = $this->session->userdata('cbjsess001');
		$dataSearch = trim($_REQUEST['q']);
		$dataNow = (!isset($_REQUEST["now"]) ? "" : $_REQUEST["now"]);
		
		$sql = "
			select PAYCODE,'('+PAYCODE+') '+PAYDESC PAYDESC from {$this->MAuth->getdb('PAYDUE')}
			where 1=1 and PAYCODE='".$dataNow."' collate Thai_CI_AS
				
			union
			select top 20 PAYCODE,'('+PAYCODE+') '+PAYDESC PAYDESC from {$this->MAuth->getdb('PAYDUE')}
			where 1=1 and '('+PAYCODE+') '+PAYDESC like '".$dataSearch."%' collate Thai_CI_AS 
			order by PAYCODE
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
	
	function getOPTMAST(){
		$sess = $this->session->userdata('cbjsess001');
		$dataSearch = trim($_REQUEST['q']);
		$dataNow = (!isset($_REQUEST["now"]) ? "" : $_REQUEST["now"]);
		$locat = $_REQUEST["locat"];
		$sql = "
			select OPTCODE,case when OPTCODE = '' then '(ว่าง)' else '('+OPTCODE+') '+OPTNAME end as OPTNAME
			from {$this->MAuth->getdb('OPTMAST')}
			where 1=1 and OPTCODE='".$dataNow."' collate Thai_CI_AS and LOCAT='".$locat."'
				
			union
			select top 20 OPTCODE,case when OPTCODE = '' then '(ว่าง)' else '('+OPTCODE+') '+OPTNAME end as OPTNAME 
			from {$this->MAuth->getdb('OPTMAST')}
			where 1=1 and '('+OPTCODE+') '+OPTNAME like '".$dataSearch."%' collate Thai_CI_AS  and LOCAT='".$locat."'
			order by OPTCODE
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$json[] = ['id'=>str_replace(chr(0),'',$row->OPTCODE), 'text'=>str_replace(chr(0),'',$row->OPTNAME)];
			}
		}
		
		echo json_encode($json);
	}
	
	function getACTI(){
		$sess = $this->session->userdata('cbjsess001');
		$dataSearch = trim($_REQUEST['q']);
		$dataNow = (!isset($_REQUEST["now"]) ? "" : $_REQUEST["now"]);
		
		$sql = "
			select ACTICOD,'('+ACTICOD+') '+ACTIDES as ACTIDES
			from {$this->MAuth->getdb('SETACTI')}
			where 1=1 and ACTICOD='".$dataNow."' collate Thai_CI_AS 
				
			union
			select ACTICOD,'('+ACTICOD+') '+ACTIDES as ACTIDES
			from {$this->MAuth->getdb('SETACTI')}
			where 1=1 and '('+ACTICOD+') '+ACTIDES like '%".$dataSearch."%' collate Thai_CI_AS
			order by ACTICOD
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$json = array();
		if($query->row()){
			foreach($query->result() as $row){
				$json[] = ['id'=>str_replace(chr(0),'',$row->ACTICOD), 'text'=>str_replace(chr(0),'',$row->ACTIDES)];
			}
		}
		
		echo json_encode($json);
	}
	
	function getBILLDAS(){
		$sess = $this->session->userdata('cbjsess001');
		$dataSearch = trim($_REQUEST['q']);
		$dataNow = (!isset($_REQUEST["now"]) ? "" : $_REQUEST["now"]);
		$locat = $_REQUEST['locat'];
		$sdate = $this->Convertdate(1,$_REQUEST['sdate']);
		
		$sql = "
			select free from serviceweb.dbo.fn_branchMaps
			where senior='".$locat."'
		";
		$query = $this->db->query($sql);
		$row = $query->row();
		
		/*@@@@@@@@@@@@@@@@@@@@@@@@
			@@ RTSL ขายปลีก
			@@ WHSL ขายส่ง
			@@ PMSL ขายโปรโมชั่น
			@@ SLCP ขายแคมเปญ
		@@@@@@@@@@@@@@@@@@@@@@@@*/
		$sql = "
			select SaleNo from DBFREE.dbo.SPSale 
			where cast(left(SaleDate,4)-543 as varchar(4))+CAST(replace(right(SaleDate,5),'/','') as varchar(4))='".$sdate."'
				and BranchNo='".$row->free."' and SaleNo = '".$dataNow."'
			union 
			select SaleNo from DBFREE.dbo.SPSale 
			where cast(left(SaleDate,4)-543 as varchar(4))+CAST(replace(right(SaleDate,5),'/','') as varchar(4))='".$sdate."'
				and BranchNo='".$row->free."' and SaleNo like '%".$dataSearch."%'
		";
		//echo $sql; exit;
		$DAS = $this->load->database('DAS',true);
		$query = $DAS->query($sql);
		$row = $query->row();
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$json[] = ['id'=>str_replace(chr(0),'',$row->SaleNo), 'text'=>str_replace(chr(0),'',$row->SaleNo)];
			}
		}
		
		echo json_encode($json);
	}
	
	function getANALYZE(){
		$sess = $this->session->userdata('cbjsess001');
		$dataSearch = trim($_REQUEST['q']);
		$dataNow = (!isset($_REQUEST["now"]) ? "" : $_REQUEST["now"]);
		$locat = ($_REQUEST["locat"]);
		
		$sql = "
			select ID,ANSTAT
			from {$this->MAuth->getdb('ARANALYZE')}
			where 1=1 and ID='".$dataNow."' collate Thai_CI_AS and LOCAT='".$locat."' 
				
			union
			select ID,ANSTAT
			from {$this->MAuth->getdb('ARANALYZE')}
			where 1=1 and ID like '%".$dataSearch."%' collate Thai_CI_AS 
				and LOCAT='".$locat."' and isnull(CONTNO,'')=''
			order by ID
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$json = array();
		if($query->row()){
			foreach($query->result() as $row){
				$json[] = array(
					'id'=>str_replace(chr(0),'',$row->ID), 
					'text'=>str_replace(chr(0),'',$row->ID),
					'disabled'=>($row->ANSTAT == 'A' ? false:true)
				);
			}
		}
		
		echo json_encode($json);
	}
	
	function getTYPCONT(){
		$sess = $this->session->userdata('cbjsess001');
		$dataSearch = trim($_REQUEST['q']);
		$dataNow = (!isset($_REQUEST["now"]) ? "" : $_REQUEST["now"]);
		
		$sql = "
			select CONTTYP,'('+CONTTYP+') '+CONTDESC as CONTDESC
			from {$this->MAuth->getdb('TYPCONT')}
			where 1=1 and CONTTYP='".$dataNow."' collate Thai_CS_AS 
				
			union
			select top 20 CONTTYP,'('+CONTTYP+') '+CONTDESC as CONTDESC
			from {$this->MAuth->getdb('TYPCONT')}
			where 1=1 and CONTTYP+' '+CONTDESC like '%".$dataSearch."%' collate Thai_CS_AS 
			order by CONTTYP
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$json = array();
		if($query->row()){
			foreach($query->result() as $row){
				$json[] = array(
					'id'=>str_replace(chr(0),'',$row->CONTTYP), 
					'text'=>str_replace(chr(0),'',$row->CONTDESC)
				);
			}
		}
		
		echo json_encode($json);
	}
	
	function getfromCUSTOMER(){
		$html = "
			<div class='row'>
				<div class='col-sm-4'>
					<div class='form-group'>
						ชื่อ
						<input type='text' id='cus_fname' class='form-control'>
					</div>
				</div>
				<div class='col-sm-4'>
					<div class='form-group'>
						สกุล
						<input type='text' id='cus_lname' class='form-control'>
					</div>
				</div>
				<div class='col-sm-4'>
					<div class='form-group'>
						หมายเลขบัตร
						<input type='text' id='cus_idno' class='form-control'>
					</div>
				</div>
				
				<div class='col-sm-12'>
					<button id='cus_search' class='btn btn-primary btn-block'><span class='glyphicon glyphicon-search'> ค้นหา</span></button>
				</div>
				
				<div id='cus_result' class='col-sm-12'></div>
			</div>
		";
		
		echo json_encode(array("html"=>$html));
	}
	
	function getResultCUSTOMER(){
		$fname = $_POST['fname'];
		$lname = $_POST['lname'];
		$idno  = $_POST['idno'];
		
		$cond = "";
		if($fname != ""){
			$cond .= " and a.NAME1 like '%".$fname."%'";
		}
		if($lname != ""){
			$cond .= " and a.NAME2 like '%".$lname."%'";
		}
		if($idno != ""){
			$cond .= " and a.IDNo like '%".$idno."%'";
		}
		
		
		$sql = "
			select top 100 a.CUSCOD,a.SNAM+a.NAME1+' '+a.NAME2 as CUSNAME,a.GRADE
				,case when a.GRADE in ('F','FF') then 'F' 
					when a.GRADE not in (select GRDCOD from SETGRADCUS) then 'F'
					else '' end GRADESTAT
				,a.SNAM+a.NAME1+' '+a.NAME2+' ('+a.CUSCOD+')'+'-'+a.GRADE as CUSNAMES
				,1 as ADDRNO
				,(
					select '('+aa.ADDRNO+') '+aa.ADDR1+' '+aa.ADDR2+' ต.'+aa.TUMB
						+' อ.'+bb.AUMPDES+' จ.'+cc.PROVDES+' '+aa.ZIP as ADDRNODetails 			
					from {$this->MAuth->getdb('CUSTADDR')} aa
					left join {$this->MAuth->getdb('SETAUMP')} bb on aa.AUMPCOD=bb.AUMPCOD
					left join {$this->MAuth->getdb('SETPROV')} cc on bb.PROVCOD=cc.PROVCOD
					where aa.CUSCOD=a.CUSCOD and aa.ADDRNO=1
				) as ADDRDES
			from {$this->MAuth->getdb('CUSTMAST')} a 
			where 1=1 ".$cond."
			order by CUSCOD
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$html .= "
					<tr style='".($row->GRADESTAT == 'F' ? "color:#aaa;":"")."'>
						<td style='width:40px;'>
							<i class='
								".($row->GRADESTAT == 'F' ? "":"CUSDetails")."
								".($row->GRADESTAT == 'F' ? "btn-default":"btn-warning")."
								btn btn-xs glyphicon glyphicon-zoom-in' 
								CUSCOD='".$row->CUSCOD."'
								CUSNAMES='".$row->CUSNAMES."' 
								ADDRNO='".$row->ADDRNO."' 
								ADDRDES='".$row->ADDRDES."' 
								style='cursor:pointer;".($row->GRADESTAT == 'F' ? "color:#ddd;":"")."'> เลือก  </i>
						</td>
						<td style='vertical-align:middle;'>".$row->CUSCOD."</td>
						<td style='vertical-align:middle;'>".$row->CUSNAME."</td>
						<td style='vertical-align:middle;'>".$row->GRADE."</td>
					</tr>
				";
			}
		}
		
		$html = "
			<div>
				<table class='table'>
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
	
	function getGROUPCUS(){
		$sess = $this->session->userdata('cbjsess001');
		$dataSearch = trim($_REQUEST['q']);
		$dataNow = (!isset($_REQUEST["now"]) ? "" : $_REQUEST["now"]);
		
		$sql = "
			select ARGCOD,'('+ARGCOD+') '+ARGDES ARGDES from {$this->MAuth->getdb('ARGROUP')}
			where 1=1 and ARGCOD='".$dataNow."' collate Thai_CI_AS
				
			union
			select top 20 ARGCOD,'('+ARGCOD+') '+ARGDES ARGDES from {$this->MAuth->getdb('ARGROUP')}
			where 1=1 and '('+ARGCOD+') '+ARGDES like '".$dataSearch."%' collate Thai_CI_AS 
			order by ARGCOD
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$json[] = ['id'=>str_replace(chr(0),'',$row->ARGCOD), 'text'=>str_replace(chr(0),'',$row->ARGDES)];
			}
		}
		
		echo json_encode($json);
	}
	
	function getAUMP(){
		$sess = $this->session->userdata('cbjsess001');
		$dataSearch = trim($_REQUEST['q']);
		$dataNow = (!isset($_REQUEST["now"]) ? "" : $_REQUEST["now"]);
		$PROVCOD = (!isset($_REQUEST["PROVCOD"]) ? "" : $_REQUEST["PROVCOD"]);
		
		$sql = "
			select AUMPCOD,'('+AUMPCOD+') '+AUMPDES AUMPDES from {$this->MAuth->getdb('SETAUMP')}
			where 1=1 and AUMPCOD='".$dataNow."' collate Thai_CI_AS
				
			union
			select top 20 AUMPCOD,'('+AUMPCOD+') '+AUMPDES AUMPDES from {$this->MAuth->getdb('SETAUMP')}
			where 1=1 and '('+AUMPCOD+') '+AUMPDES like '%".$dataSearch."%' collate Thai_CI_AS 
				".($PROVCOD == ""?"":" and PROVCOD='".$PROVCOD."'")."
			order by AUMPCOD
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$json[] = ['id'=>str_replace(chr(0),'',$row->AUMPCOD), 'text'=>str_replace(chr(0),'',$row->AUMPDES)];
			}
		}
		
		echo json_encode($json);
	}
	
	function getPROV(){
		$sess = $this->session->userdata('cbjsess001');
		$dataSearch = trim($_REQUEST['q']);
		$dataNow = (!isset($_REQUEST["now"]) ? "" : $_REQUEST["now"]);
		
		$sql = "
			select PROVCOD,'('+PROVCOD+') '+PROVDES PROVDES from {$this->MAuth->getdb('SETPROV')}
			where 1=1 and PROVCOD='".$dataNow."' collate Thai_CI_AS
				
			union
			select top 20 PROVCOD,'('+PROVCOD+') '+PROVDES as PROVDES from {$this->MAuth->getdb('SETPROV')}
			where 1=1 and '('+PROVCOD+') '+PROVDES like '%".$dataSearch."%' collate Thai_CI_AS 
			order by PROVCOD
		";
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$json[] = ['id'=>str_replace(chr(0),'',$row->PROVCOD), 'text'=>str_replace(chr(0),'',$row->PROVDES)];
			}
		}
		
		echo json_encode($json);
	}
	
}




















