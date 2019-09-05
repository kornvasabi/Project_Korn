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
		$dataSearch = trim($_REQUEST['q']);
		$dataNow = (!isset($_REQUEST["now"]) ? "" : $_REQUEST["now"]);
		
		$sql = "
			select top 20 CUSCOD,SNAM+NAME1+' '+NAME2+' ('+CUSCOD+')'+'-'+GRADE as CUSNAME from {$this->MAuth->getdb('CUSTMAST')}
			where CUSCOD = '".$dataNow."' collate Thai_CI_AS 			
			
			union
			select top 20 CUSCOD,SNAM+NAME1+' '+NAME2+' ('+CUSCOD+')'+'-'+GRADE as CUSNAME from {$this->MAuth->getdb('CUSTMAST')}
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
	
	function getBAAB(){
		//แบบรถ
		$sess = $this->session->userdata('cbjsess001');
		$dataSearch = trim($_GET['q']);
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
				$json[] = array('id'=>$row->BAABCOD, 'text'=>$row->BAABCOD);
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
		$query = $this->db->query($sql);
		
		$html = "";
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
		
		$sql = "
			select STRNO from {$this->MAuth->getdb('INVTRAN')}
			where CRLOCAT = '".$locat."' collate Thai_CI_AS 
				and STRNO='".$dataNow."' collate Thai_CI_AS
				and FLAG='D' and isnull(CONTNO,'')='' and SDATE is null 
				and isnull(RESVNO,'')=''
			union
			select top 20 STRNO from {$this->MAuth->getdb('INVTRAN')}
			where CRLOCAT = '".$locat."' collate Thai_CI_AS 
				and STRNO like '".$dataSearch."%' collate Thai_CI_AS 
				and FLAG='D' and isnull(CONTNO,'')='' and SDATE is null 
				and isnull(RESVNO,'')=''
			order by STRNO desc
		";
		$query = $this->db->query($sql);
		
		$html = "";
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
		
		$html = "";
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
	
}




















