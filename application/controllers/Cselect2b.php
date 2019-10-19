<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//BEE+
class Cselect2b extends MY_Controller {
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
			select top 20 CUSCOD,SNAM+NAME1+' '+NAME2+' ('+CUSCOD+')'+' - '+GRADE as CUSNAME from {$this->MAuth->getdb('CUSTMAST')}
			where CUSCOD = '".$dataNow."' collate Thai_CI_AS 			
			
			union
			select top 20 CUSCOD,SNAM+NAME1+' '+NAME2+' ('+CUSCOD+')'+' - '+GRADE as CUSNAME from {$this->MAuth->getdb('CUSTMAST')}
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
			select ADDRNO,'('+ADDRNO+') '+ADDR1+' '+ADDR2+' '+TUMB as ADDRNODetails from {$this->MAuth->getdb('CUSTADDR')}
			where CUSCOD = '".$cuscod."' collate Thai_CI_AS and ADDRNO = '".$dataNow."' collate Thai_CI_AS
			
			union
			select ADDRNO,'('+ADDRNO+') '+ADDR1+' '+ADDR2+' '+TUMB as ADDRNODetails from {$this->MAuth->getdb('CUSTADDR')}
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
	
	function getTYPESALE(){
		//ประเภทการขาย
		$sess = $this->session->userdata('cbjsess001');
		$dataSearch = trim($_GET['q']);
		
		$sql = "
			select TSALE, TSALE+' '+DESC1 as TSALENAME from {$this->MAuth->getdb('TYPSALE')} 
			where TSALE like '%".$dataSearch."%'
			order by TSALE
		"; 
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$json[] = ['id'=>$row->TSALE, 'text'=>$row->TSALENAME];
			}
		}
		
		echo json_encode($json);
	}
	
	
	function getPAYFOR(){
		//ประเภทการขาย
		$sess = $this->session->userdata('cbjsess001');
		$dataSearch = trim($_GET['q']);
		
		$sql = "
			select top 10 FORCODE, FORCODE+' - '+FORDESC as FORDESC
			from {$this->MAuth->getdb('PAYFOR')} 
			where FORCODE not like '0%' and (FORCODE like '".$dataSearch."%' or FORDESC like '%".$dataSearch."%')
			order by FORCODE
		"; 
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$json[] = ['id'=>$row->FORCODE, 'text'=>$row->FORDESC];
			}
		}
		
		echo json_encode($json);
	}
	
	
	function getCONTNO_AR(){
		//เลขที่สัญญา
		$sess = $this->session->userdata('cbjsess001');
		$dataSearch = trim($_GET['q']);
		$dataNow = (!isset($_REQUEST["now"]) ? "" : $_REQUEST["now"]);
		$TSALE = (!isset($_REQUEST["now2"]) ? "" : $_REQUEST["now2"]);
		$top = ""; $cond = ""; $tsales = ""; $tsales2 = "";

		if($TSALE == 'H'){
			$tsales = "where b.TSALE = 'H'";
		}else if($TSALE == 'C'){
			$tsales = "where b.TSALE = 'C'";
		}else if($TSALE == 'F'){
			$tsales = "where b.CONTNO like '%FN%'";
		}
		
		if($dataNow != ''){
			$sql = "
					select top 10 CONTNO
					from( 
						select top 10 CONTNO, CUSCOD from {$this->MAuth->getdb('ARMAST')} 
						where 1=1 and CUSCOD = '".$dataNow."' collate thai_cs_as 

						union

						select top 10 CONTNO, CUSCOD
						from(
							select distinct a.CONTNO, a.CUSCOD, isnull(b.TSALE,'H') as TSALE
							from {$this->MAuth->getdb('ARHOLD')} a
							left join HINVTRAN b on a.CONTNO = b.CONTNO 
						)a
						where 1=1 and CUSCOD = '".$dataNow."' collate thai_cs_as 

						union

						select top 10 CONTNO, CUSCOD from {$this->MAuth->getdb('ARCRED')} 
						where 1=1 and CUSCOD = '".$dataNow."' collate thai_cs_as 
					)A
					order by CONTNO desc
				";
			$cond = "and CUSCOD = '".$dataNow."' collate thai_cs_as";
		}else{
			$sql = "
					select top 20 CONTNO
					from( 
						select top 10 CONTNO, CUSCOD 
						from (select CONTNO, CUSCOD from {$this->MAuth->getdb('ARMAST')} b ".$tsales.")a
						where 1=1 and CONTNO like '%".$dataSearch."%' collate thai_cs_as

						union

						select top 10 CONTNO, CUSCOD
						from(
							select distinct a.CONTNO, a.CUSCOD, isnull(b.TSALE,'H') as TSALE
							from {$this->MAuth->getdb('ARHOLD')} a
							left join HINVTRAN b on a.CONTNO = b.CONTNO 
							".$tsales."
						)a
						where 1=1 and CONTNO like '%".$dataSearch."%' collate thai_cs_as

						union

						select top 10 CONTNO, CUSCOD 
						from (select CONTNO, CUSCOD from {$this->MAuth->getdb('ARCRED')} b ".$tsales.")a 
						where 1=1 and CONTNO like '%".$dataSearch."%' collate thai_cs_as
					)A
					order by substring(CONTNO,2,2) desc
				";
		}//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$json[] = ['id'=>$row->CONTNO, 'text'=>$row->CONTNO];
			}
		}
		
		echo json_encode($json);
	}
	
	function getCUSTOMERS_AR(){
		$sess = $this->session->userdata('cbjsess001');
		$dataSearch = trim($_REQUEST['q']);
		//$dataNow = (!isset($_REQUEST["now"]) ? "" : $_REQUEST["now"]);

		$sql = "
				select top 20 CUSCOD,SNAM+NAME1+' '+NAME2+' ('+CUSCOD+')'+' - '+GRADE as CUSNAME
				from {$this->MAuth->getdb('CUSTMAST')}
				where CUSCOD like '%".$dataSearch."%' collate Thai_CI_AS 
					or NAME1+' '+NAME2 like '%".$dataSearch."%' collate Thai_CI_AS
					or IDNO like '%".$dataSearch."%' collate Thai_CI_AS
		";//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$json[] = ['id'=>$row->CUSCOD, 'text'=>$row->CUSNAME];					
			}
		}
		
		echo json_encode($json);
	}
	
	function getCUSTOMERS_AR11(){
		$contno = $_REQUEST["contno"];
		
		$sql = "
			select top 20 a.CUSCOD, SNAM + NAME1 +' '+ NAME2 +' ('+a.CUSCOD+')'+' - '+ GRADE as CUSNAME 
			from (
				select distinct CONTNO, CUSCOD from {$this->MAuth->getdb('ARMAST')} where CONTNO = '".$contno."' collate thai_cs_as
				union
				select distinct CONTNO, CUSCOD from {$this->MAuth->getdb('ARHOLD')} where CONTNO = '".$contno."' collate thai_cs_as
				union
				select distinct CONTNO, CUSCOD from {$this->MAuth->getdb('ARCRED')} where CONTNO = '".$contno."' collate thai_cs_as
			) as a
			left join {$this->MAuth->getdb('CUSTMAST')} b on a.CUSCOD=b.CUSCOD collate thai_cs_as
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$response = array();
		if($query->row()){
			foreach($query->result() as $row){
				$response["CUSCOD"] = $row->CUSCOD;
				$response["CUSNAME"] = $row->CUSNAME;
			}
		}
		
		echo json_encode($response);	
	}
	
	function getCUSTOMERS_AR22(){
		$customer = $_REQUEST["customer"];
		
		$sql = "
			select top 20 CONTNO 
			from (
				select distinct CONTNO, CUSCOD from {$this->MAuth->getdb('ARMAST')} where CUSCOD = '".$customer."' collate thai_cs_as
				union
				select distinct CONTNO, CUSCOD from {$this->MAuth->getdb('ARHOLD')} where CUSCOD = '".$customer."' collate thai_cs_as
				union
				select distinct CONTNO, CUSCOD from {$this->MAuth->getdb('ARCRED')} where CUSCOD = '".$customer."' collate thai_cs_as
			)A
		";
		
		$query = $this->db->query($sql);
		
		$response = array();
		if($query->row()){
			foreach($query->result() as $row){
				$json[] = ['id'=>$row->CONTNO, 'text'=>$row->CONTNO];					
			}
		}
		
		echo json_encode($response);	
	}
	
	function getCONTNO_ExchangCar(){
		$sess = $this->session->userdata('cbjsess001');
		$dataSearch = trim($_REQUEST['q']);
		//$dataNow = (!isset($_REQUEST["now"]) ? "" : $_REQUEST["now"]);

		$sql = "
				select top 20 CONTNO, CUSCOD from {$this->MAuth->getdb('ARMAST')}
				where YSTAT != 'Y' and TOTPRC-SMPAY-SMCHQ >0 and CONTNO like '%".$dataSearch."%' 
		";//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$json[] = ['id'=>$row->CONTNO, 'text'=>$row->CONTNO];					
			}
		}
		
		echo json_encode($json);
	}
	
	function getGCode_ExchangCar(){
		//กลุ่มสินค้า
		$sess = $this->session->userdata('cbjsess001');
		$GCODEold = $_REQUEST["GCODEold"];
		$dataSearch = trim($_GET['q']);
		$sql = "
			select GCODE,'('+GCODE+') '+GDESC as GDESC,  case when GCODE = '".$GCODEold."' then 'disabled' else '' end as disabled
			from {$this->MAuth->getdb('SETGROUP')}
			where GCODE like '%".$dataSearch."%' collate Thai_CI_AS
				or GDESC like '%".$dataSearch."%' collate Thai_CI_AS
			order by GCODE
		"; 
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$json[] = ['id'=>$row->GCODE, 'text'=>$row->GDESC, 'disabled'=>$row->disabled];
			}
		}
		
		echo json_encode($json);
	}
	
	function getGCode_typecar(){
		//กลุ่มสินค้า
		$sess = $this->session->userdata('cbjsess001');
		$dataSearch = trim($_GET['q']);
		$ugroup = $_REQUEST["ugroup"];

		$select = "";
		if($ugroup == 'HP'){
			$select = "";
		}else{
			$select = "where GCODE in ('15','16','022','023','024','29','30','15F','16F','22F','23F','24F','29F','30F','027','27F')";
		}
		
		$sql = "
			select GCODE, GDESC
			from(
				select GCODE, GDESC from {$this->MAuth->getdb('SETGROUP')} ".$select."
			)a
			where GCODE like '%".$dataSearch."%' collate Thai_CI_AS or GDESC like '%".$dataSearch."%' collate Thai_CI_AS
		"; 
		//echo $sql; exit;
		
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$json[] = ['id'=>$row->GCODE, 'text'=>'('.$row->GCODE.') '.$row->GDESC];
			}
		}
		
		echo json_encode($json);
	}
	
	function getGCode_typecar2(){
		//กลุ่มสินค้า
		$sess = $this->session->userdata('cbjsess001');
		$dataSearch = trim($_GET['q']);
		$ugroup = $_REQUEST["ugroup"];
		$GCODES = $_REQUEST["GCODES"];

		$select = "";
		if($ugroup == 'HP'){
			$select = "";
		}else{
			$select = "where GCODE in ('15','16','022','023','024','29','30','15F','16F','22F','23F','24F','29F','30F','027','27F')";
		}
		
		$sql = "
			select GCODE, GDESC, 
			case 	when GCODE = '".$GCODES."' then 'disabled' 
					when GCODE = '04' then 'disabled'
					else '' end as disabled
			from(
				select GCODE, GDESC from {$this->MAuth->getdb('SETGROUP')} ".$select."
			)a
			where GCODE like '%".$dataSearch."%' collate Thai_CI_AS or GDESC like '%".$dataSearch."%' collate Thai_CI_AS
		"; 
		//echo $sql; exit;
		
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$json[] = ['id'=>$row->GCODE, 'text'=>'('.$row->GCODE.') '.$row->GDESC, 'disabled'=>$row->disabled];
			}
		}
		
		echo json_encode($json);
	}
	
	function getTYPLOST(){
		//ประเภทการขาย
		$sess = $this->session->userdata('cbjsess001');
		$dataSearch = trim($_GET['q']);
		
		$sql = "
			select LOSTCOD, LOSTCOD+' - '+LOSTESC as LOSTESC
			from {$this->MAuth->getdb('TYPLOST')} 
			where LOSTCOD like '".$dataSearch."%' or LOSTESC like '%".$dataSearch."%'
			order by LOSTCOD
		"; 
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$json[] = ['id'=>$row->LOSTCOD, 'text'=>$row->LOSTESC];
			}
		}
		
		echo json_encode($json);
	}
	
	function getCONTNO_DoubtfulAcc(){
		$sess = $this->session->userdata('cbjsess001');
		$dataSearch = trim($_REQUEST['q']);
		//$dataNow = (!isset($_REQUEST["now"]) ? "" : $_REQUEST["now"]);

		$sql = "
				select top 20 CONTNO, CUSCOD from {$this->MAuth->getdb('ARMAST')}
				where YSTAT != 'Y' and TOTPRC-SMPAY-SMCHQ >0 and CONTNO like '%".$dataSearch."%' 
		";//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$json[] = ['id'=>$row->CONTNO, 'text'=>$row->CONTNO];					
			}
		}
		
		echo json_encode($json);
	}
	
	function getCONTNO_ChangeContstat(){
		$sess = $this->session->userdata('cbjsess001');
		$dataSearch = trim($_REQUEST['q']);

		$sql = "
				select top 20 CONTNO from {$this->MAuth->getdb('ARMAST')}
				where CONTNO like '%".$dataSearch."%'
		";//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$json[] = ['id'=>$row->CONTNO, 'text'=>$row->CONTNO];					
			}
		}
		
		echo json_encode($json);
	}
	
	function getTYPCONT_ChangeContstat(){
		$sess = $this->session->userdata('cbjsess001');
		$TYPCONTold = $_REQUEST["TYPCONTold"];
		$dataSearch = trim($_GET['q']);
		$sql = "
			select CONTTYP, CONTTYP+' - '+CONTDESC as CONTDESC, case when CONTTYP = '".$TYPCONTold."' then 'disabled' else '' end as disabled
			from {$this->MAuth->getdb('TYPCONT')}
			where CONTTYP like '%".$dataSearch."%' collate Thai_CI_AS
				or CONTDESC like '%".$dataSearch."%' collate Thai_CI_AS
			order by CONTTYP
		"; 
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$json[] = ['id'=>$row->CONTTYP, 'text'=>$row->CONTDESC, 'disabled'=>$row->disabled];
			}
		}
		echo json_encode($json);
	}
	
	function getAUMPHUR(){
		$sess = $this->session->userdata('cbjsess001');
		$dataNow = (!isset($_REQUEST["now"]) ? "" : $_REQUEST["now"]);
		$dataSearch = trim($_GET['q']);
		if($dataNow != ""){
			$sql = "
				select 	PROVCOD, AUMPCOD, AUMPDES from {$this->MAuth->getdb('SETAUMP')}
				where 	PROVCOD like '%".$dataNow."%' collate Thai_CI_AS
				order by PROVCOD, AUMPDES
			"; 
		}else{
			$sql = "
				select 	top 20 PROVCOD, AUMPCOD, AUMPDES from {$this->MAuth->getdb('SETAUMP')}
				where 	AUMPCOD like '%".$dataSearch."%' collate Thai_CI_AS
						or AUMPDES like '%".$dataSearch."%' collate Thai_CI_AS
				order by PROVCOD, AUMPDES
			"; 	
		}
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$json[] = ['id'=>$row->AUMPCOD, 'text'=>$row->AUMPDES];
			}
		}
		echo json_encode($json);
	}
	
	function getPROVINCE(){
		$sess = $this->session->userdata('cbjsess001');
		$dataSearch = trim($_GET['q']);
		$sql = "
			select 	top 20 PROVCOD, PROVDES from {$this->MAuth->getdb('SETPROV')}
			where 	PROVCOD like '%".$dataSearch."%' collate Thai_CI_AS
					or PROVDES like '%".$dataSearch."%' collate Thai_CI_AS
			order by PROVDES
		";
		
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$json[] = ['id'=>$row->PROVCOD, 'text'=>$row->PROVDES];
			}
		}
		echo json_encode($json);
	}
	
	function getPROVINCEbyAUMPHUR(){
		$sess = $this->session->userdata('cbjsess001');
		//$dataSearch = trim($_GET['q']);
		$aumphur = $_REQUEST["aumphur"];
		$sql = "
			select  a.PROVCOD, b.PROVDES, a.AUMPCOD, a.AUMPDES
			from SETAUMP a
			left join SETPROV b on a.PROVCOD = b.PROVCOD
			where a.AUMPCOD = '".$aumphur."'
		";
		
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$response = array();
		if($query->row()){
			foreach($query->result() as $row){
				$response["PROVCOD"] = $row->PROVCOD;
				$response["PROVDES"] = $row->PROVDES;
			}
		}
		echo json_encode($response);
	}
	
	function getOFFICER(){
		$sess = $this->session->userdata('cbjsess001');
		$dataSearch = trim($_REQUEST['q']);
		
		$sql = "
			select  top 20 CODE, NAME+' ('+CODE +')'  as NAME, DEPCODE from {$this->MAuth->getdb('OFFICER')}
			where 	CODE like '%".$dataSearch."%' collate Thai_CI_AS 
					or NAME like '%".$dataSearch."%' collate Thai_CI_AS
		";
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$json[] = ['id'=>$row->CODE, 'text'=>$row->NAME];
			}
		}
		
		echo json_encode($json);
	}
	
	function getCONTNO_HoldtoStock(){
		$sess = $this->session->userdata('cbjsess001');
		$dataSearch = trim($_REQUEST['q']);

		$sql = "
				select top 20 CONTNO from {$this->MAuth->getdb('ARMAST')}
				where TOTPRC > SMPAY and CONTNO like '%".$dataSearch."%'
		";//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$json[] = ['id'=>$row->CONTNO, 'text'=>$row->CONTNO];					
			}
		}
		
		echo json_encode($json);
	}

	function getTYPHOLD(){
		$sess = $this->session->userdata('cbjsess001');
		$dataSearch = trim($_REQUEST['q']);
		//$dataNow = (!isset($_REQUEST["now"]) ? "" : $_REQUEST["now"]);

		$sql = "
				select HOLDCOD, '('+HOLDCOD+') '+HOLDESC as HOLDESC from {$this->MAuth->getdb('TYPHOLD')}
				where HOLDCOD like '%".$dataSearch."%' or HOLDESC like '%".$dataSearch."%'
		";//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$json[] = ['id'=>$row->HOLDCOD, 'text'=>$row->HOLDESC];					
			}
		}
		
		echo json_encode($json);
	}
	
	function getCONTNO_HOLDTOOLDCAR(){
		$sess = $this->session->userdata('cbjsess001');
		$dataSearch = trim($_REQUEST['q']);

		$sql = "
				select top 20 CONTNO from {$this->MAuth->getdb('ARMAST')}
				where YSTAT='Y' and Totprc > smpay and CONTNO not in(SELECT CONTNO FROM {$this->MAuth->getdb('ARHOLD')})
				and CONTNO like '%".$dataSearch."%'
		";//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$json[] = ['id'=>$row->CONTNO, 'text'=>$row->CONTNO];					
			}
		}
		
		echo json_encode($json);
	}
	
	function getCONTNO_ARHOLD(){
		$sess = $this->session->userdata('cbjsess001');
		$dataSearch = trim($_REQUEST['q']);

		$sql = "
				select top 20 CONTNO from {$this->MAuth->getdb('ARHOLD')}
				where CONTNO like '%".$dataSearch."%'
				order by YDATE desc
		";//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$json[] = ['id'=>$row->CONTNO, 'text'=>$row->CONTNO];					
			}
		}
		
		echo json_encode($json);
	}
	
	function getCONTNO_AlertMsg(){
		$sess = $this->session->userdata('cbjsess001');
		$dataSearch = trim($_REQUEST['q']);

		$sql = "
				select top 20 CONTNO from {$this->MAuth->getdb('INVTRAN')}
				where CONTNO != '' and CONTNO is not null and CONTNO like '%".$dataSearch."%'
		";//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$json[] = ['id'=>$row->CONTNO, 'text'=>$row->CONTNO];					
			}
		}
		
		echo json_encode($json);
	}
	
	function getCUSTOMERSALL(){
		$sess = $this->session->userdata('cbjsess001');
		$dataSearch = trim($_REQUEST['q']);
		$dataNow = (!isset($_REQUEST["now"]) ? "" : $_REQUEST["now"]);
		
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
				$json[] = ['id'=>$row->CUSCOD, 'text'=>$row->CUSNAME];
			}
		}
		
		echo json_encode($json);
	}
	
	function getCONTNOALL(){
		$sess = $this->session->userdata('cbjsess001');
		$dataSearch = trim($_REQUEST['q']);

		$sql = "
				select top 20 CONTNO
				from(
					select CONTNO from {$this->MAuth->getdb('INVTRAN')}
					where CONTNO != '' and CONTNO is not null and CONTNO like '%".$dataSearch."%'
					union
					select CONTNO from {$this->MAuth->getdb('HINVTRAN')}
					where CONTNO != '' and CONTNO is not null and CONTNO like '%".$dataSearch."%'
				)A
		";//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$json[] = ['id'=>$row->CONTNO, 'text'=>$row->CONTNO];					
			}
		}
		
		echo json_encode($json);
	}
	
}