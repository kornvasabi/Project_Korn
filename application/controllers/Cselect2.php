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
			select a.ADDRNO,'('+a.ADDRNO+') '+a.ADDR1+' '+isnull(a.ADDR2,'')+' ต.'+a.TUMB
				+' อ.'+b.AUMPDES+' จ.'+c.PROVDES+' '+a.ZIP	as ADDRNODetails 			
			from {$this->MAuth->getdb('CUSTADDR')} a
			left join {$this->MAuth->getdb('SETAUMP')} b on a.AUMPCOD=b.AUMPCOD
			left join {$this->MAuth->getdb('SETPROV')} c on b.PROVCOD=c.PROVCOD
			where CUSCOD = '".$cuscod."' collate Thai_CI_AS and ADDRNO = '".$dataNow."' collate Thai_CI_AS
			
			union
			select a.ADDRNO,'('+a.ADDRNO+') '+a.ADDR1+' '+isnull(a.ADDR2,'')+' ต.'+a.TUMB
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
		$dataNow = (!isset($_REQUEST["now"]) ? "" : $_REQUEST["now"]);
		
		$sql = "
			select GCODE,'('+GCODE+') '+GDESC as GDESC from {$this->MAuth->getdb('SETGROUP')}
			where GCODE='".$dataNow."' collate Thai_CI_AS
			union
			
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
		$dataSearch = trim($_REQUEST['q']);
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
		$dataSearch = trim($_REQUEST['q']);
		$dataNow = (!isset($_REQUEST["now"]) ? "" : $_REQUEST["now"]);
		$TYPECOD = $_REQUEST['TYPECOD'];
		
		$sql = "
			select top 100 MODELCOD from {$this->MAuth->getdb('SETMODEL')}
			where TYPECOD='".$TYPECOD."' and MODELCOD like '%".$dataSearch."%' collate thai_ci_as
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
	
	function getMODEL_Analyze(){
		//รุ่นรถ
		$sess = $this->session->userdata('cbjsess001');
		$dataSearch = trim($_REQUEST['q']);
		$dataNow = (!isset($_REQUEST["now"]) ? "" : $_REQUEST["now"]);
		$TYPECOD = $_REQUEST['TYPECOD'];
		$STAT = (!isset($_REQUEST["STAT"]) ? "N" : $_REQUEST["STAT"]);
		
		$sql = "
			select top 100 MODELCOD,CC from {$this->MAuth->getdb('SETMODEL')}
			where TYPECOD='".$TYPECOD."' and MODELCOD like '%".$dataSearch."%' collate thai_ci_as 
				and MODELCOD collate thai_ci_as in (
					select distinct a.MODEL collate thai_ci_as from {$this->MAuth->getdb('STDVehicles')} a
					left join {$this->MAuth->getdb('STDVehiclesDetail')} b on a.STDID=b.STDID
					where b.STAT='{$STAT}' and a.STDTYPE='model'
					
					union 
					
					select a.MODELCOD collate thai_ci_as from {$this->MAuth->getdb('SETMODEL')} a 
					left join {$this->MAuth->getdb('SETMODELDESC')} b on a.MDDID=b.MDDID
					where b.MODELDESC collate thai_ci_as  in (
						select distinct a.MODEL from {$this->MAuth->getdb('STDVehicles')} a
						left join {$this->MAuth->getdb('STDVehiclesDetail')} b on a.STDID=b.STDID
						where b.STAT='{$STAT}' and a.STDTYPE='desc'
					)
					
					union 
					
					select a.MODELCOD collate thai_ci_as from {$this->MAuth->getdb('SETMODEL')} a 
					where 'ทุกรุ่น' = (
						select distinct a.MODEL from {$this->MAuth->getdb('STDVehicles')} a
						left join {$this->MAuth->getdb('STDVehiclesDetail')} b on a.STDID=b.STDID
						where b.STAT='O' and a.STDTYPE='all'
					)
				)
			order by MODELCOD
		"; 
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		$json = array();
		if($query->row()){
			foreach($query->result() as $row){
				//$json[] = ['id'=>$row->MODELCOD, 'text'=>$row->MODELCOD];
				$json[] = array('id'=>str_replace(chr(0),"",$row->MODELCOD), 'text'=>str_replace(chr(0),"",$row->MODELCOD) ,'CC' => str_replace(chr(0),"",$row->CC));
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
	
	function getCOLOR_STD(){
		$sess = $this->session->userdata('cbjsess001');
		$dataSearch = trim($_GET['q']);
		$dataNow = (!isset($_REQUEST["now"]) ? "" : $_REQUEST["now"]);
		
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
				$json[] = array('id'=>str_replace(chr(0),"",$row->COLORCOD), 'text'=>str_replace(chr(0),"",$row->COLORCOD));
			}
		}
		
		echo json_encode($json);
	}
	
	function getJDCOLOR(){
		$sess 		= $this->session->userdata('cbjsess001');
		$dataSearch = trim($_REQUEST['q']);
		$dataNow 	= (!isset($_REQUEST["now"]) ? "" : $_REQUEST["now"]);
		$MODEL 		= $_REQUEST['MODEL'];
		$BAAB 		= $_REQUEST['BAAB'];
		
		if(isset($_REQUEST['NOTB'])){
			$cond = "";
			if(is_array($BAAB)){ 
				$BAAB = implode("','",$BAAB); 
				$cond = " and BAABCOD collate Thai_CI_AS in ('{$BAAB}') ";
			}
			$sql = "
				select distinct COLORCOD,COLORCOD+' :: '+MEMO1 as COLORNM from {$this->MAuth->getdb('JD_SETCOLOR')}
				where MODELCOD='{$MODEL}' collate Thai_CI_AS {$cond}
				order by COLORCOD
			"; 			
		}else{
			$sql = "
				select distinct COLORCOD,COLORCOD+' :: '+MEMO1 as COLORNM from {$this->MAuth->getdb('JD_SETCOLOR')}
				where MODELCOD='{$MODEL}' collate Thai_CI_AS
					and BAABCOD='{$BAAB}' collate Thai_CI_AS
				order by COLORCOD
			";
		}
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		$json = array();
		if($query->row()){
			foreach($query->result() as $row){
				$json[] = array('id'=>str_replace(chr(0),"",$row->COLORCOD), 'text'=>str_replace(chr(0),"",$row->COLORNM));
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
			select a.RESVNO from (
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
			) as a
			left join {$this->MAuth->getdb('ARANALYZE')} as b on a.RESVNO=b.RESVNO collate thai_cs_as and b.ANSTAT not in ('C')
			where b.RESVNO is null
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
	
	// ใช้ดึงเลขที่บิลจองในใบวิเคราะห์  ซึ่งยังไม่จำเป็นต้องระบุเลขตัวถัง
	function getRESVNO2(){
		$sess = $this->session->userdata('cbjsess001');
		$dataSearch = trim($_REQUEST['q']);
		$dataNow = (!isset($_REQUEST["now"]) ? "" : $_REQUEST["now"]);
		$locat = $_REQUEST['locat'];
		
		$sql = "
			select a.RESVNO from (
				select RESVNO from {$this->MAuth->getdb('ARRESV')}
				where LOCAT = '".$locat."' collate Thai_CI_AS 
					and RESVNO='".$dataNow."' collate Thai_CI_AS 
					and SDATE is null
				union
				select top 10 RESVNO from {$this->MAuth->getdb('ARRESV')}
				where LOCAT = '".$locat."' collate Thai_CI_AS 
					and RESVNO like '".$dataSearch."%' collate Thai_CI_AS 
					and SDATE is null
				
				union
				--บิลจองจากสาขาอื่น แต่ลูกค้ามาออกรถกับอีกสาขา
				select top 10 a.RESVNO from {$this->MAuth->getdb('ARRESV')} a
				left join {$this->MAuth->getdb('INVTRAN')} b on a.STRNO=b.STRNO and a.RESVNO=b.RESVNO
				where b.CRLOCAT='".$locat."' 
					and a.RESVNO like '".$dataSearch."%' collate Thai_CI_AS 
					and a.SDATE is null
			) as a
			left join {$this->MAuth->getdb('ARANALYZE')} as b on a.RESVNO=b.RESVNO collate thai_cs_as and b.ANSTAT not in ('C','N')
			where b.RESVNO is null
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
		$ANID = (!isset($_REQUEST["ANID"]) ? "" : $_REQUEST["ANID"]);
		
		
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
		
		/*
		if($COLOR != ""){
			$cond .= " 
				and COLOR collate thai_cs_as in (
					select COLORCOD from {$this->MAuth->getdb('JD_SETCOLOR')}
					where MEMO1 in (
						select distinct MEMO1 from {$this->MAuth->getdb('JD_SETCOLOR')} 
						where COLORCOD='".$COLOR."' 
					) and MODELCOD='".$MODEL."' and BAABCOD='".$BAAB."'
				)
			";
		}
		*/
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
			order by STRNO asc
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$json = array();
		if($query->row()){
			foreach($query->result() as $row){
				$sql = "
					select count(*) r from {$this->MAuth->getdb('ARANALYZE')} 
					where ANSTAT not in ('N','C') and STRNO='{$row->STRNO}' and ID != '".$ANID."'
				";
				//echo $sql; exit;
				$query = $this->db->query($sql);
				$row_check = $query->row();
				
				$json[] = array(
					'id'=>str_replace(chr(0),'',$row->STRNO), 
					'text'=>str_replace(chr(0),'',$row->STRNO),
					'disabled'=>($row_check->r > 0 ? true:false)
				);
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
	
	function getPAYFOR(){
		$sess = $this->session->userdata('cbjsess001');
		$dataTop = (!isset($_POST['top']) ? " top 20 ": ($_POST['top'] == "" ? "" : " top ".$_POST['top']));
		$dataSearch = trim($_REQUEST['q']);
		$dataNow = (!isset($_REQUEST["now"]) ? "" : $_REQUEST["now"]);
		
		$sql = "
			select FORCODE,'('+FORCODE+') '+FORDESC FORDESC from {$this->MAuth->getdb('PAYFOR')}
			where 1=1 and FORCODE='".$dataNow."' collate Thai_CI_AS
				
			union
			select {$dataTop} FORCODE,'('+FORCODE+') '+FORDESC FORDESC from {$this->MAuth->getdb('PAYFOR')}
			where 1=1 and '('+FORCODE+') '+FORDESC like '%".$dataSearch."%' collate Thai_CI_AS 
			order by FORCODE
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$json = array();
		if($query->row()){
			foreach($query->result() as $row){
				$json[] = ['id'=>str_replace(chr(0),'',$row->FORCODE), 'text'=>str_replace(chr(0),'',$row->FORDESC)];
			}
		}
		
		echo json_encode($json);
	}
	
	function getPAYTYP(){
		$sess = $this->session->userdata('cbjsess001');
		$dataSearch = trim($_REQUEST['q']);
		$dataNow = (!isset($_REQUEST["now"]) ? "" : $_REQUEST["now"]);
		
		$sql = "
			select PAYCODE,'('+PAYCODE+') '+PAYDESC PAYDESC from {$this->MAuth->getdb('PAYTYP')}
			where 1=1 and PAYCODE='".$dataNow."' collate Thai_CI_AS
				
			union
			select top 20 PAYCODE,'('+PAYCODE+') '+PAYDESC PAYDESC from {$this->MAuth->getdb('PAYTYP')}
			where 1=1 and '('+PAYCODE+') '+PAYDESC like '%".$dataSearch."%' collate Thai_CI_AS 
			order by PAYCODE
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$json = array();
		if($query->row()){
			foreach($query->result() as $row){
				$json[] = ['id'=>str_replace(chr(0),'',$row->PAYCODE), 'text'=>str_replace(chr(0),'',$row->PAYDESC)];
			}
		}
		
		echo json_encode($json);
	}
	
	function getBKMAST(){
		$sess = $this->session->userdata('cbjsess001');
		$dataSearch = trim($_REQUEST['q']);
		$dataNow = (!isset($_REQUEST["now"]) ? "" : $_REQUEST["now"]);
		
		$sql = "
			select BKCODE,'('+BKCODE+') '+BKNAME BKNAME from {$this->MAuth->getdb('BKMAST')}
			where 1=1 and BKCODE='".$dataNow."' collate Thai_CI_AS
				
			union
			select top 20 BKCODE,'('+BKCODE+') '+BKNAME BKNAME from {$this->MAuth->getdb('BKMAST')}
			where 1=1 and '('+BKCODE+') '+BKNAME like '%".$dataSearch."%' collate Thai_CI_AS 
			order by BKCODE
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$json = array();
		if($query->row()){
			foreach($query->result() as $row){
				$json[] = ['id'=>str_replace(chr(0),'',$row->BKCODE), 'text'=>str_replace(chr(0),'',$row->BKNAME)];
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
			where 1=1 and '('+OPTCODE+') '+OPTNAME like '%".$dataSearch."%' collate Thai_CI_AS  and LOCAT='".$locat."'
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
			select a.ACTICOD,'('+a.ACTICOD+') '+a.ACTIDES as ACTIDES
			from {$this->MAuth->getdb('SETACTI')} a
			left join {$this->MAuth->getdb('STDVehiclesACTI')} b on a.ACTICOD=b.ACTICOD collate thai_cs_as
			where 1=1 and '('+a.ACTICOD+') '+a.ACTIDES like '%".$dataSearch."%' collate Thai_CI_AS
				and b.ACTICOD is not null
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
		$dataNow 	= (!isset($_REQUEST["now"]) ? "" : $_REQUEST["now"]);
		$locat 		= $_REQUEST['locat'];
		$sdate 		= $this->Convertdate(1,$_REQUEST['sdate']);
		$customers 	= $_REQUEST['customers'];
		
		$cond = "";
		foreach($customers as $key => $val){
			if($cond != ""){ $cond .= ","; }
			$cond .= "'".$val."'";
		}
		
		if ($cond != ""){
			$sql = "
				select IDNO from {$this->MAuth->getdb('CUSTMAST')} 
				where CUSCOD in (".$cond.")
			";
			$query = $this->db->query($sql);
			
			$cond = "'NONE'";
			if($query->row()){
				foreach($query->result() as $row){
					if($cond != ""){ $cond .= ","; }
					$cond .= "'".str_replace(chr(0),'',$row->IDNO)."'";
				}
			}
		}
		
		$sql = "
			select senior,free,spss from serviceweb.dbo.fn_branchMaps
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
			select 'F'+SaleNo as SaleNo from DBFREE.dbo.SPSale a 
			left join DBFREE.dbo.Customer b on a.CustID=b.CustID
			where cast(left(a.SaleDate,4)-543 as varchar(4))+CAST(replace(right(a.SaleDate,5),'/','') as varchar(4))>='".$sdate."'
				and a.BranchNo='".$row->free."' 
				and a.SaleNo = '".$dataNow."' 
				and a.SaleStatus collate thai_ci_as in ('paid','Approved') 
				and b.CustIDCardNo collate thai_cs_as in (".$cond.")
			union 
			select 'F'+SaleNo as SaleNo from DBFREE.dbo.SPSale a
			left join DBFREE.dbo.Customer b on a.CustID=b.CustID
			where cast(left(a.SaleDate,4)-543 as varchar(4))+CAST(replace(right(a.SaleDate,5),'/','') as varchar(4))>='".$sdate."'
				and a.BranchNo='".$row->free."' 
				and a.SaleNo like '%".$dataSearch."%' 
				and a.SaleStatus collate thai_ci_as in ('paid','Approved') 
				and b.CustIDCardNo collate thai_cs_as in (".$cond.")
				
			union 
			select 'S'+SaleNo as SaleNo from DBSPS.dbo.SPSale a
			left join DBSPS.dbo.Customer b on a.CustID=b.CustID
			where cast(left(a.SaleDate,4)-543 as varchar(4))+CAST(replace(right(a.SaleDate,5),'/','') as varchar(4))>='".$sdate."'
				and a.BranchNo='".$row->free."' 
				and a.SaleNo like '%".$dataSearch."%' 
				and a.SaleStatus collate thai_ci_as in ('paid','Approved') 
				and b.CustIDCardNo collate thai_cs_as in (".$cond.")	
		";
		//echo $sql; exit;
		$DAS = $this->load->database('DAS',true);
		$query = $DAS->query($sql);
		
		$json   = array();
		if($query->row()){
			foreach($query->result() as $row){
				$json[] = array('id'=>str_replace(chr(0),'',$row->SaleNo), 'text'=>str_replace(chr(0),'',$row->SaleNo));
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
			select * from (
				select ID,ANSTAT
				from {$this->MAuth->getdb('ARANALYZE')}
				where 1=1 and ID='".$dataNow."' collate Thai_CI_AS and LOCAT='".$locat."' 
					
				union
				select ID,ANSTAT
				from {$this->MAuth->getdb('ARANALYZE')}
				where 1=1 and ID like '%".$dataSearch."%' collate Thai_CI_AS 
					and LOCAT='".$locat."' 
					and isnull(CONTNO,'')=''
			) as a
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
					'disabled'=>(in_array($row->ANSTAT,array("A","FA")) ? false:true)
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
			select CONTTYP,'('+CONTTYP+') '+CONTDESC as CONTDESC
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
	
	function getformCUSTOMER(){
		$html = "
			<div class='row'>
				<div class='col-sm-4'>
					<div class='form-group'>
						ชื่อ
						<input type='text' id='cus_fname' class='form-control' maxlength='30'>
					</div>
				</div>
				<div class='col-sm-4'>
					<div class='form-group'>
						สกุล
						<input type='text' id='cus_lname' class='form-control' maxlength='30'>
					</div>
				</div>
				<div class='col-sm-4'>
					<div class='form-group'>
						บัตร ปชช./รหัสลูกค้า
						<input type='text' id='cus_idno' class='form-control' maxlength='20'>
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
		$allow_risk  = (isset($_POST['allow_risk']) ? $_POST['allow_risk']:'N');
		
		$cuscod = array();
		$cuscod[] = (isset($_POST['cuscod']) ? $_POST['cuscod']:'');
		$cuscod[] = (isset($_POST['is1_cuscod']) ? $_POST['is1_cuscod']:'');
		$cuscod[] = (isset($_POST['is2_cuscod']) ? $_POST['is2_cuscod']:'');
		$cuscod[] = (isset($_POST['is3_cuscod']) ? $_POST['is3_cuscod']:'');
		
		$cond = "";
		if($fname != ""){
			$cond .= " and a.NAME1 like '%".$fname."%'";
		}
		if($lname != ""){
			$cond .= " and a.NAME2 like '%".$lname."%'";
		}
		if($idno != ""){
			$cond .= " and (a.IDNo like '%".$idno."%' or a.CUSCOD like '%".$idno."%')";
		}
		
		$sql = "
			select top 100 a.CUSCOD,a.SNAM+a.NAME1+' '+a.NAME2 as CUSNAME,a.GRADE
				,case when a.GRADE in ('F','FF') then 'F' 
					when a.GRADE not in (select GRDCOD from {$this->MAuth->getdb('SETGRADCUS')}) then 'F'
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
				if(in_array($row->CUSCOD,$cuscod)){ $row->GRADESTAT = 'INUSE'; }
				if($allow_risk!='Y' and $row->GRADESTAT=='F' ){ $row->GRADESTAT = 'INUSE'; } 
				$html .= "
					<tr style='".($row->GRADESTAT == 'INUSE' ? "color:#aaa;":"")."'>
						<td style='width:40px;'>
							<i class='
								".($row->GRADESTAT == 'INUSE' ? "":"CUSDetails")."
								".($row->GRADESTAT == 'INUSE' ? "btn-default":"btn-warning")."
								btn btn-xs glyphicon glyphicon-zoom-in' 
								CUSCOD='".$row->CUSCOD."'
								CUSNAMES='".$row->CUSNAMES."' 
								ADDRNO='".$row->ADDRNO."' 
								ADDRDES='".$row->ADDRDES."' 
								style='cursor:pointer;".($row->GRADESTAT == 'INUSE' ? "color:#ddd;":"")."'> เลือก  </i>
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
	
	function getResultCUSTOMERALL(){
		$fname = $_POST['fname'];
		$lname = $_POST['lname'];
		$idno  = $_POST['idno'];
		$allow_risk  = (isset($_POST['allow_risk']) ? $_POST['allow_risk']:'N');
		
		$cuscod = array();
		$cuscod[] = (isset($_POST['cuscod']) ? $_POST['cuscod']:'');
		$cuscod[] = (isset($_POST['is1_cuscod']) ? $_POST['is1_cuscod']:'');
		$cuscod[] = (isset($_POST['is2_cuscod']) ? $_POST['is2_cuscod']:'');
		$cuscod[] = (isset($_POST['is3_cuscod']) ? $_POST['is3_cuscod']:'');
		
		$cond = "";
		if($fname != ""){
			$cond .= " and a.NAME1 like '%".$fname."%'";
		}
		if($lname != ""){
			$cond .= " and a.NAME2 like '%".$lname."%'";
		}
		if($idno != ""){
			$cond .= " and (a.IDNo like '%".$idno."%' or a.CUSCOD like '%".$idno."%')";
		}
		
		$sql = "
			select top 100 a.CUSCOD,a.SNAM+a.NAME1+' '+a.NAME2 as CUSNAME,a.GRADE
				,case when a.GRADE in ('F','FF') then 'F' 
					when a.GRADE not in (select GRDCOD from {$this->MAuth->getdb('SETGRADCUS')}) then 'F'
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
				,(select sa.TELP from {$this->MAuth->getdb('CUSTADDR')} sa where sa.CUSCOD=a.CUSCOD and sa.ADDRNO=a.ADDRNO) as TELP
			from {$this->MAuth->getdb('CUSTMAST')} a 
			where 1=1 ".$cond."
			order by a.NAME1,a.NAME2
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$html .= "
					<tr>
						<td style='width:40px;'>
							<i class='
								CUSDetails
								btn-warning
								btn btn-xs glyphicon glyphicon-zoom-in' 
								CUSCOD='".$row->CUSCOD."'
								CUSNAMES='".$row->CUSNAMES."' 
								ADDRNO='".$row->ADDRNO."' 
								ADDRDES='".$row->ADDRDES."' 
								TELP='".$row->TELP."'
								style='cursor:pointer;'> เลือก  </i>
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
	
	function getformCONTNO(){
		$data = $_POST["data"];
		
		$titilCONTNO  = "เลขที่สัญญา";
		$leyout = 4;
		$other_leyout = "";
		if($data == "OTHER"){
			$titilCONTNO  = "เลขที่สัญญาลูกหนี้อื่น";
			$leyout = 3;
			$other_leyout = "
				<div class='col-sm-{$leyout}'>
					ลูกหนี้อื่น
					<select id='cont_other' class='form-control'>
						<option value='Y'>ตั้งลูกหนี้แล้ว</option>
						<option value='N'>ไม่ได้ตั้งลูกหนี้</option>
					</select>
				</div>
			";
		}
		
		$html = "
			<div class='row'>
				<div class='col-sm-{$leyout}'>
					<div class='form-group'>
						ชำระค่า
						<input type='text' id='cont_payfor' class='form-control' >
					</div>
				</div>
				<div class='col-sm-{$leyout}'>
					ลูกค้า
					<div class='input-group'>
					   <input type='text' id='cont_cus' CUSCOD='' class='form-control input-sm' placeholder='ลูกค้า'  value=''>
					   <span class='input-group-btn'>
					   <button id='cont_cus_removed' class='btn btn-danger btn-sm' type='button'>
							<span class='glyphicon glyphicon-remove' aria-hidden='true'></span></button>
					   </span>
					</div>
				</div>
				{$other_leyout}
				<div class='col-sm-{$leyout}'>
					<div class='form-group'>
						{$titilCONTNO}
						<input type='text' id='cont_no' class='form-control'  maxlength='13' value=''>
					</div>
				</div>
				
				<div class='col-sm-12'>
					<button id='cont_search' class='btn btn-primary btn-block'><span class='glyphicon glyphicon-search'> ค้นหา</span></button>
				</div>
				
				<div id='cont_result' class='col-sm-12'></div>
			</div>
		";
		
		echo json_encode(array("html"=>$html));
	}
	
	function getResultCONTNO(){
		$PAYFOR = $_POST['PAYFOR'];
		$CUSCOD = $_POST['CUSCOD'];
		$OTHER  = $_POST['OTHER'];
		$CONTNO = $_POST['CONTNO'];
		
		$html = "";
		if ($PAYFOR == '001'){
			$html = $this->getResultCONTNO001($CUSCOD,$CONTNO);
		}else if ($PAYFOR == '002'){
			$html = $this->getResultCONTNO002($CUSCOD,$CONTNO);
		}else if ($PAYFOR == '003'){
			$html = $this->getResultCONTNO003($CUSCOD,$CONTNO);
		}else if ($PAYFOR == '004'){
			$html = $this->getResultCONTNO004($CUSCOD,$CONTNO);
		}else if ($PAYFOR == '005'){
			$html = $this->getResultCONTNO005($CUSCOD,$CONTNO);
		}else if ($PAYFOR == '006'){
			$html = $this->getResultCONTNO006($CUSCOD,$CONTNO);
		}else if ($PAYFOR == '007'){
			$html = $this->getResultCONTNO007($CUSCOD,$CONTNO);
		}else if ($PAYFOR == '008'){
			$html = $this->getResultCONTNO008($CUSCOD,$CONTNO);
		}else if ($PAYFOR == '009'){
			$html = $this->getResultCONTNO009($CUSCOD,$CONTNO);
		}else if ($PAYFOR == '011'){
			$html = $this->getResultCONTNO011($CUSCOD,$CONTNO);
		}else{
			if($OTHER == "Y"){
				$html = $this->getResultCONTNOOTher($CUSCOD,$CONTNO,$PAYFOR);
			}else{
				$html = $this->getResultCONTNOOTherCustomers($CUSCOD,$CONTNO,$PAYFOR);
			}
		}
		
		$response = array("html"=>$html);
		echo json_encode($response);
	}
	
	private function getResultCONTNO001($CUSCOD,$CONTNO){
		$sql = "
			declare @CUSCOD varchar(13) = '{$CUSCOD}%';
			declare @CONTNO varchar(13) = '{$CONTNO}%';
			
			SELECT top 100 A.CONTNO
				,A.LOCAT
				,A.CUSCOD
				,B.SNAM+B.NAME1+' '+B.NAME2 as CUSNAME
				,A.STRNO,A.SDATE,A.TOTPRC
				,A.SMPAY,A.SMCHQ,A.TOTPRC-A.SMPAY-A.SMCHQ AS BALANCE
				,A.CREDTM,A.DUEDT,A.RESVNO,A.SALCOD  
				,(select sa.TELP from {$this->MAuth->getdb('CUSTADDR')} sa where sa.CUSCOD=A.CUSCOD and sa.ADDRNO=A.ADDRNO) as TELP
			FROM {$this->MAuth->getdb('ARCRED')} A
			left join {$this->MAuth->getdb('CUSTMAST')} B on A.CUSCOD=B.CUSCOD 
			WHERE 1=1 AND A.TOTPRC>A.SMPAY AND A.CUSCOD like @CUSCOD and A.CONTNO like @CONTNO
			ORDER BY B.NAME1,B.NAME2
		";
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$html .= "
					<tr>
						<td>
							<button class='cont_selected btn btn-xs btn-warning glyphicon glyphicon-plus' 
								contno  ='".$row->CONTNO."' 
								locat   ='".$row->LOCAT."' 
								cuscod  ='".$row->CUSCOD."' 
								cusname ='".$row->CUSNAME."' 
								telp	='".$row->TELP."'
								total	='".str_replace(",","",(number_format($row->BALANCE,2)))."'
								error	=''
								style='cursor:pointer;padding:3px;'> เลือก </button>
						</td>
						<td style='padding:3px;'>".$row->CONTNO."</td>
						<td style='padding:3px;'>".$row->LOCAT."</td>
						<td style='padding:3px;'>".$row->CUSNAME."</td>
						<td style='padding:3px;'>".$row->STRNO."</td>
						<td style='padding:3px;'>".$this->Convertdate(103,$row->SDATE)."</td>
						<td style='padding:3px;' align='right'>".number_format($row->TOTPRC,2)."</td>
						<td style='padding:3px;' align='right'>".number_format($row->SMPAY,2)."</td>
						<td style='padding:3px;' align='right'>".number_format($row->SMCHQ,2)."</td>
						<td style='padding:3px;' align='right'>".number_format($row->BALANCE,2)."</td>
						<td style='padding:3px;'>".$row->CREDTM."</td>
						<td style='padding:3px;'>".$this->Convertdate(103,$row->DUEDT)."</td>
						<td style='padding:3px;'>".$row->RESVNO."</td>
					</tr>
				";
			}
		}
		
		$html = "
			<table border=1 style='width:100%;border-collapse:collapse;'>
				<thead>
					<tr>
						<th style='padding:3px;'>#</th>
						<th style='padding:3px;'>เลขที่สัญญา</th>
						<th style='padding:3px;'>สาขา</th>
						<th style='padding:3px;'>ลูกค้า</th>
						<th style='padding:3px;'>เลขตัวถัง</th>
						<th style='padding:3px;'>วันที่ขาย</th>
						<th style='padding:3px;'>จำนวน</th>
						<th style='padding:3px;'>ชำระแล้ว</th>
						<th style='padding:3px;'>เช็ค</th>
						<th style='padding:3px;'>คงเหลือ</th>
						<th style='padding:3px;'>เครดิต</th>
						<th style='padding:3px;'>วันดิว</th>
						<th style='padding:3px;'>เลขที่จอง</th>
					</tr>
				</thead>
				<tbody>{$html}</tbody>
			</table>
		";
		
		return $html;
	}
	
	private function getResultCONTNO002($CUSCOD,$CONTNO){
		$sql = "
			declare @CUSCOD varchar(13) = '{$CUSCOD}%';
			declare @CONTNO varchar(13) = '{$CONTNO}%';
			
			SELECT top 100 A.CONTNO
				,A.LOCAT
				,A.CUSCOD
				,B.SNAM+B.NAME1+' '+B.NAME2 as CUSNAME
				,A.STRNO,A.SDATE,A.TOTPRC
				,A.SMPAY,A.SMCHQ,A.TOTPRC-A.SMPAY-A.SMCHQ AS BALANCE
				,A.NDAWN+A.VATDWN AS TOTDWN
				,A.PAYDWN,A.TOTDWN-A.PAYDWN AS BALDWN
				,A.NPAYRES+A.VATPRES AS PAYRES,A.BILLCOLL  
				,(select sa.TELP from {$this->MAuth->getdb('CUSTADDR')} sa where sa.CUSCOD=B.CUSCOD and sa.ADDRNO=B.ADDRNO) as TELP
			FROM {$this->MAuth->getdb('ARMAST')} A
			left join {$this->MAuth->getdb('CUSTMAST')} B on A.CUSCOD=B.CUSCOD
			WHERE 1=1 AND A.TOTPRC > isnull(A.SMPAY,0)
				AND A.CUSCOD like @CUSCOD 
				and A.CONTNO like @CONTNO
				and A.TOTDWN-A.PAYDWN > 0
			ORDER BY B.NAME1 
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$html .= "
					<tr>
						<td>
							<button class='cont_selected btn btn-xs btn-warning glyphicon glyphicon-plus' 
								contno	='".$row->CONTNO."' 
								locat   ='".$row->LOCAT."' 
								cuscod	='".$row->CUSCOD."' 
								cusname ='".$row->CUSNAME."' 
								telp	='".$row->TELP."' 
								total	='".str_replace(",","",(number_format($row->BALDWN,2)))."'
								error	=''
								style='cursor:pointer;padding:3px;'> เลือก </button>
						</td>
						<td style='padding:3px;'>".$row->CONTNO."</td>
						<td style='padding:3px;'>".$row->LOCAT."</td>
						<td style='padding:3px;'>".$row->CUSNAME."</td>
						<td style='padding:3px;'>".$row->STRNO."</td>
						<td style='padding:3px;'>".$this->Convertdate(103,$row->SDATE)."</td>
						<td style='padding:3px;' align='right'>".number_format($row->TOTPRC,2)."</td>
						<td style='padding:3px;' align='right'>".number_format($row->SMPAY,2)."</td>
						<td style='padding:3px;' align='right'>".number_format($row->SMCHQ,2)."</td>
						<td style='padding:3px;' align='right'>".number_format($row->BALANCE,2)."</td>
						<td style='padding:3px;' align='right'>".number_format($row->TOTDWN,2)."</td>
						<td style='padding:3px;' align='right'>".number_format($row->PAYDWN,2)."</td>
						<td style='padding:3px;' align='right'>".number_format($row->BALDWN,2)."</td>
						<td style='padding:3px;' align='right'>".number_format($row->PAYRES,2)."</td>
						<td style='padding:3px;'>".$row->BILLCOLL."</td>
					</tr>
				";
			}
		}
		
		$html = "
			<table border=1 style='width:100%;border-collapse:collapse;'>
				<thead>
					<tr>
						<th style='padding:3px;'>#</th>
						<th style='padding:3px;'>เลขที่สัญญา</th>
						<th style='padding:3px;'>สาขา</th>
						<th style='padding:3px;'>ลูกค้า</th>
						<th style='padding:3px;'>เลขตัวถัง</th>
						<th style='padding:3px;'>วันที่ขาย</th>
						<th style='padding:3px;'>จำนวน</th>
						<th style='padding:3px;'>ชำระแล้ว</th>
						<th style='padding:3px;'>เช็ค</th>
						<th style='padding:3px;'>คงเหลือ</th>
						<th style='padding:3px;'>เงินดาวน์</th>
						<th style='padding:3px;'>ชำระดาวน์</th>
						<th style='padding:3px;'>ค้างดาวน์</th>
						<th style='padding:3px;'>เงินจอง</th>
						<th style='padding:3px;'>Billcoll</th>
					</tr>
				</thead>
				<tbody>{$html}</tbody>
			</table>
		";
		
		return $html;
	}
	
	private function getResultCONTNO003($CUSCOD,$CONTNO){
		$sql = "
			declare @CUSCOD varchar(13) = '{$CUSCOD}%';
			declare @CONTNO varchar(13) = '{$CONTNO}%';
			
			SELECT top 100 A.CONTNO
				,A.LOCAT
				,A.CUSCOD
				,B.SNAM+B.NAME1+' '+B.NAME2 as CUSNAME
				,A.NDAWN,A.VATDWN,A.PAYDWN
				,A.PAYFIN,A.NFINAN,A.VATFIN, A.RESVNO,A.TOTPRC
				,A.SMPAY,A.TOTPRC-A.SMPAY AS BALANCE
				,A.SMCHQ,A.NDAWN+A.VATDWN AS TOTDWN,A.PAYDWN
				,A.NDAWN+A.VATDWN-A.PAYDWN AS BALDWN
				,A.NFINAN+A.VATFIN AS TOTFINC,A.PAYFIN
				,A.NFINAN+A.VATFIN-A.PAYFIN AS BALFINC
				,A.SDATE,A.STRNO,A.FINCOD 
				,(select sa.TELP from {$this->MAuth->getdb('CUSTADDR')} sa where sa.CUSCOD=B.CUSCOD and sa.ADDRNO=B.ADDRNO) as TELP
			FROM {$this->MAuth->getdb('ARFINC')} A
			left join {$this->MAuth->getdb('CUSTMAST')} B on A.CUSCOD=B.CUSCOD 
			WHERE 1=1 AND A.TOTPRC>A.SMPAY AND A.CUSCOD like @CUSCOD and A.CONTNO like @CONTNO
			ORDER BY A.CONTNO,A.LOCAT 
		";
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$html .= "
					<tr>
						<td>
							<button class='cont_selected btn btn-xs btn-warning glyphicon glyphicon-plus' 
								contno	='".$row->CONTNO."' 
								locat   ='".$row->LOCAT."' 
								cuscod	='".$row->CUSCOD."' 
								cusname ='".$row->CUSNAME."' 
								telp 	='".$row->TELP."' 
								total	='".str_replace(",","",(number_format($row->BALDWN,2)))."'
								error	=''
								style='cursor:pointer;padding:3px;'> เลือก </button>
						</td>
						<td style='padding:3px;'>".$row->CONTNO."</td>
						<td style='padding:3px;'>".$row->LOCAT."</td>
						<td style='padding:3px;'>".$row->CUSNAME."</td>
						<td style='padding:3px;'>".$row->STRNO."</td>
						<td style='padding:3px;'>".$this->Convertdate(103,$row->SDATE)."</td>
						<td style='padding:3px;' align='right'>".number_format($row->TOTPRC,2)."</td>
						<td style='padding:3px;' align='right'>".number_format($row->SMPAY,2)."</td>
						<td style='padding:3px;' align='right'>".number_format($row->SMCHQ,2)."</td>
						<td style='padding:3px;' align='right'>".number_format($row->BALANCE,2)."</td>
						<td style='padding:3px;' align='right'>".number_format($row->TOTDWN,2)."</td>
						<td style='padding:3px;' align='right'>".number_format($row->PAYDWN,2)."</td>
						<td style='padding:3px;' align='right'>".number_format($row->BALDWN,2)."</td>
						<td style='padding:3px;' align='right'>".number_format($row->TOTFINC,2)."</td>
						<td style='padding:3px;' align='right'>".number_format($row->PAYFIN,2)."</td>
						<td style='padding:3px;' align='right'>".number_format($row->BALFINC,2)."</td>
						<td style='padding:3px;'>".$row->FINCOD."</td>
					</tr>
				";
			}
		}
		
		$html = "
			<table border=1 style='width:100%;border-collapse:collapse;'>
				<thead>
					<tr>
						<th style='padding:3px;'>#</th>
						<th style='padding:3px;'>เลขที่สัญญา</th>
						<th style='padding:3px;'>สาขา</th>
						<th style='padding:3px;'>ลูกค้า</th>
						<th style='padding:3px;'>เลขตัวถัง</th>
						<th style='padding:3px;'>วันที่ขาย</th>
						<th style='padding:3px;'>จำนวน</th>
						<th style='padding:3px;'>ชำระแล้ว</th>
						<th style='padding:3px;'>เช็ค</th>
						<th style='padding:3px;'>คงเหลือ</th>
						<th style='padding:3px;'>เงินดาวน์</th>
						<th style='padding:3px;'>ชำระดาวน์</th>
						<th style='padding:3px;'>ค้างดาวน์</th>
						<th style='padding:3px;'>ยอดส่งไฟแนนท์</th>
						<th style='padding:3px;'>รับจากไฟแนนท์</th>
						<th style='padding:3px;'>ค้างรับจากไฟแนนท์</th>
						<th style='padding:3px;'>รหัสไฟแนนท์</th>
					</tr>
				</thead>
				<tbody>{$html}</tbody>
			</table>
		";
		
		return $html;
	}
	
	private function getResultCONTNO004($CUSCOD,$CONTNO){
		$sql = "
			declare @CUSCOD varchar(13) = '{$CUSCOD}%';
			declare @CONTNO varchar(13) = '{$CONTNO}%';
			
			SELECT top 100 A.CONTNO
				,A.LOCAT
				,A.CUSCOD
				,B.SNAM+B.NAME1+' '+B.NAME2 as CUSNAME
				,A.NDAWN,A.VATDWN,A.PAYDWN
				,A.PAYFIN,A.NFINAN,A.VATFIN
				,A.RESVNO,A.TOTPRC,A.SMPAY,A.TOTPRC-A.SMPAY AS BALANCE
				,A.SMCHQ,A.NDAWN+A.VATDWN AS TOTDWN,A.PAYDWN
				,A.NDAWN+A.VATDWN-A.PAYDWN AS BALDWN
				,A.NFINAN+A.VATFIN AS TOTFINC,A.PAYFIN
				,A.NFINAN+A.VATFIN-A.PAYFIN AS BALFINC
				,A.SDATE,A.STRNO,A.FINCOD 
				,(select sa.TELP from {$this->MAuth->getdb('CUSTADDR')} sa where sa.CUSCOD=B.CUSCOD and sa.ADDRNO=B.ADDRNO) as TELP
			FROM {$this->MAuth->getdb('ARFINC')} A
			left join {$this->MAuth->getdb('CUSTMAST')} B on A.CUSCOD=B.CUSCOD 
			WHERE 1=1 AND A.TOTPRC>A.SMPAY AND A.CUSCOD like @CUSCOD and A.CONTNO like @CONTNO
			ORDER BY A.CONTNO,A.LOCAT 
		";
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$html .= "
					<tr>
						<td>
							<button class='cont_selected btn btn-xs btn-warning glyphicon glyphicon-plus' 
								contno	='".$row->CONTNO."' 
								locat   ='".$row->LOCAT."' 
								cuscod	='".$row->CUSCOD."' 
								cusname ='".$row->CUSNAME."' 
								telp 	='".$row->TELP."' 
								total	='".str_replace(",","",(number_format($row->BALFINC,2)))."'
								error	=''
								style='cursor:pointer;padding:3px;'> เลือก </button>
						</td>
						<td style='padding:3px;'>".$row->CONTNO."</td>
						<td style='padding:3px;'>".$row->LOCAT."</td>
						<td style='padding:3px;'>".$row->CUSNAME."</td>
						<td style='padding:3px;'>".$row->STRNO."</td>
						<td style='padding:3px;'>".$this->Convertdate(103,$row->SDATE)."</td>
						<td style='padding:3px;' align='right'>".number_format($row->TOTPRC,2)."</td>
						<td style='padding:3px;' align='right'>".number_format($row->SMPAY,2)."</td>
						<td style='padding:3px;' align='right'>".number_format($row->SMCHQ,2)."</td>
						<td style='padding:3px;' align='right'>".number_format($row->BALANCE,2)."</td>
						<td style='padding:3px;' align='right'>".number_format($row->TOTDWN,2)."</td>
						<td style='padding:3px;' align='right'>".number_format($row->PAYDWN,2)."</td>
						<td style='padding:3px;' align='right'>".number_format($row->BALDWN,2)."</td>
						<td style='padding:3px;' align='right'>".number_format($row->TOTFINC,2)."</td>
						<td style='padding:3px;' align='right'>".number_format($row->PAYFIN,2)."</td>
						<td style='padding:3px;' align='right'>".number_format($row->BALFINC,2)."</td>
						<td style='padding:3px;'>".$row->FINCOD."</td>
					</tr>
				";
			}
		}
		
		$html = "
			<table border=1 style='width:100%;border-collapse:collapse;'>
				<thead>
					<tr>
						<th style='padding:3px;'>#</th>
						<th style='padding:3px;'>เลขที่สัญญา</th>
						<th style='padding:3px;'>สาขา</th>
						<th style='padding:3px;'>ลูกค้า</th>
						<th style='padding:3px;'>เลขตัวถัง</th>
						<th style='padding:3px;'>วันที่ขาย</th>
						<th style='padding:3px;'>จำนวน</th>
						<th style='padding:3px;'>ชำระแล้ว</th>
						<th style='padding:3px;'>เช็ค</th>
						<th style='padding:3px;'>คงเหลือ</th>
						<th style='padding:3px;'>เงินดาวน์</th>
						<th style='padding:3px;'>ชำระดาวน์</th>
						<th style='padding:3px;'>ค้างดาวน์</th>
						<th style='padding:3px;'>ยอดส่งไฟแนนท์</th>
						<th style='padding:3px;'>รับจากไฟแนนท์</th>
						<th style='padding:3px;'>ค้างรับจากไฟแนนท์</th>
						<th style='padding:3px;'>รหัสไฟแนนท์</th>
					</tr>
				</thead>
				<tbody>{$html}</tbody>
			</table>
		";
		
		return $html;
	}
	
	private function getResultCONTNO005($CUSCOD,$CONTNO){
		$sql = "
			declare @CUSCOD varchar(13) = '{$CUSCOD}%';
			declare @CONTNO varchar(13) = '{$CONTNO}%';
			
			SELECT top 100 A.CONTNO
				,A.LOCAT
				,A.CUSCOD
				,B.SNAM+B.NAME1+' '+B.NAME2 as CUSNAME
				,A.SDATE,A.OPTPTOT,A.SMPAY, A.SMCHQ
				,A.OPTPTOT-A.SMPAY-A.SMCHQ AS BALANCE
				,(select sa.TELP from {$this->MAuth->getdb('CUSTADDR')} sa where sa.CUSCOD=B.CUSCOD and sa.ADDRNO=B.ADDRNO) as TELP
			FROM {$this->MAuth->getdb('AROPTMST')} A
			left join {$this->MAuth->getdb('CUSTMAST')} B on A.CUSCOD=B.CUSCOD 
			WHERE 1=1 AND A.OPTPTOT>A.SMPAY AND A.CUSCOD like @CUSCOD and A.CONTNO like @CONTNO
			ORDER BY A.CONTNO,A.LOCAT 
		";
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$html .= "
					<tr>
						<td>
							<button class='cont_selected btn btn-xs btn-warning glyphicon glyphicon-plus' 
								contno	='".$row->CONTNO."' 
								locat   ='".$row->LOCAT."' 
								cuscod	='".$row->CUSCOD."' 
								cusname ='".$row->CUSNAME."' 
								telp 	='".$row->TELP."' 
								total	='".str_replace(",","",(number_format($row->BALANCE,2)))."'
								error	=''
								style='cursor:pointer;padding:3px;'> เลือก </button>
						</td>
						<td style='padding:3px;'>".$row->CONTNO."</td>
						<td style='padding:3px;'>".$row->LOCAT."</td>
						<td style='padding:3px;'>".$row->CUSNAME."</td>
						<td style='padding:3px;'>".$this->Convertdate(103,$row->SDATE)."</td>
						<td style='padding:3px;' align='right'>".number_format($row->OPTPTOT,2)."</td>
						<td style='padding:3px;' align='right'>".number_format($row->SMPAY,2)."</td>
						<td style='padding:3px;' align='right'>".number_format($row->SMCHQ,2)."</td>
						<td style='padding:3px;' align='right'>".number_format($row->BALANCE,2)."</td>
					</tr>
				";
			}
		}
		
		$html = "
			<table border=1 style='width:100%;border-collapse:collapse;'>
				<thead>
					<tr>
						<th style='padding:3px;'>#</th>
						<th style='padding:3px;'>เลขที่สัญญา</th>
						<th style='padding:3px;'>สาขา</th>
						<th style='padding:3px;'>ลูกค้า</th>
						<th style='padding:3px;'>วันที่ขาย</th>
						<th style='padding:3px;'>จำนวน</th>
						<th style='padding:3px;'>ชำระแล้ว</th>
						<th style='padding:3px;'>เช็ค</th>
						<th style='padding:3px;'>คงเหลือ</th>
					</tr>
				</thead>
				<tbody>{$html}</tbody>
			</table>
		";
		
		return $html;
	}
	
	private function getResultCONTNO006($CUSCOD,$CONTNO){
		$sql = "
			declare @CUSCOD varchar(13) = '{$CUSCOD}%';
			declare @CONTNO varchar(13) = '{$CONTNO}%';
			
			SELECT top 100 A.CONTNO
				,A.LOCAT
				,A.CUSCOD
				,B.SNAM+B.NAME1+' '+B.NAME2 as CUSNAME
				,A.STRNO,A.SDATE,A.TOTPRC,A.SMPAY
				,A.SMCHQ,A.TOTPRC-A.SMPAY-A.SMCHQ AS BALANCE
				,A.NDAWN+A.VATDWN AS TOTDWN
				,A.PAYDWN,A.TOTDWN-A.PAYDWN AS BALDWN
				,A.NPAYRES+A.VATPRES AS PAYRES,A.BILLCOLL  
				,A.CONTSTAT,C.CONTDESC
				,(select sa.TELP from {$this->MAuth->getdb('CUSTADDR')} sa where sa.CUSCOD=B.CUSCOD and sa.ADDRNO=B.ADDRNO) as TELP
			FROM {$this->MAuth->getdb('ARMAST')} A
			left join {$this->MAuth->getdb('CUSTMAST')} B on A.CUSCOD=B.CUSCOD 
			left join {$this->MAuth->getdb('TYPCONT')} C on A.CONTSTAT=C.CONTTYP
			WHERE 1=1 AND A.TOTPRC>A.SMPAY AND A.CUSCOD like @CUSCOD and A.CONTNO like @CONTNO
			ORDER BY B.NAME1 
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$error = "";
				if($row->BALDWN > 0){ $error = "ผิดพลาด เลขที่สัญญา ".$row->CONTNO."<br>ยังค้างชำระเงินดาวน์อยู่ ".$row->BALDWN." บาท"; }
				
				$html .= "
					<tr>
						<td>
							<button class='cont_selected btn btn-xs btn-warning glyphicon glyphicon-plus' 
								contno  ='".$row->CONTNO."' 
								locat   ='".$row->LOCAT."' 
								cuscod  ='".$row->CUSCOD."' 
								cusname ='".$row->CUSNAME."' 
								telp 	='".$row->TELP."' 
								total   ='".str_replace(",","",(number_format($row->BALANCE,2)))."'
								error   ='".$error."'
								style='cursor:pointer;padding:3px;'> เลือก </button>
						</td>
						<td style='padding:3px;'>".$row->CONTNO."</td>
						<td style='padding:3px;'>".$row->LOCAT."</td>
						<td style='padding:3px;'>".$row->CUSNAME."</td>
						<td style='padding:3px;'>".$row->STRNO."</td>
						<td style='padding:3px;'>".$this->Convertdate(103,$row->SDATE)."</td>
						<td style='padding:3px;' align='right'>".number_format($row->TOTPRC,2)."</td>
						<td style='padding:3px;' align='right'>".number_format($row->SMPAY,2)."</td>
						<td style='padding:3px;' align='right'>".number_format($row->SMCHQ,2)."</td>
						<td style='padding:3px;' align='right'>".number_format($row->BALANCE,2)."</td>
						<td style='padding:3px;' align='right'>".number_format($row->TOTDWN,2)."</td>
						<td style='padding:3px;' align='right'>".number_format($row->PAYDWN,2)."</td>
						<td style='padding:3px;' align='right'>".number_format($row->BALDWN,2)."</td>
						<td style='padding:3px;' align='right'>".number_format($row->PAYRES,2)."</td>
						<td style='padding:3px;'>".$row->BILLCOLL."</td>
						<td style='padding:3px;'>".$row->CONTDESC."</td>
					</tr>
				";
			}
		}
		
		$html = "
			<table border=1 style='width:100%;border-collapse:collapse;'>
				<thead>
					<tr>
						<th style='padding:3px;'>#</th>
						<th style='padding:3px;'>เลขที่สัญญา</th>
						<th style='padding:3px;'>สาขา</th>
						<th style='padding:3px;'>ลูกค้า</th>
						<th style='padding:3px;'>เลขตัวถัง</th>
						<th style='padding:3px;'>วันที่ขาย</th>
						<th style='padding:3px;'>จำนวน</th>
						<th style='padding:3px;'>ชำระแล้ว</th>
						<th style='padding:3px;'>เช็ค</th>
						<th style='padding:3px;'>คงเหลือ</th>
						<th style='padding:3px;'>เงินดาวน์</th>
						<th style='padding:3px;'>ชำระดาวน์</th>
						<th style='padding:3px;'>ค้างดาวน์</th>
						<th style='padding:3px;'>เงินจอง</th>
						<th style='padding:3px;'>Billcoll</th>
						<th style='padding:3px;'>สถานะสัญญา</th>
					</tr>
				</thead>
				<tbody>{$html}</tbody>
			</table>
		";
		
		return $html;
	}
	
	private function getResultCONTNO007($CUSCOD,$CONTNO){
		$sql = "
			declare @CUSCOD varchar(13) = '{$CUSCOD}%';
			declare @CONTNO varchar(13) = '{$CONTNO}%';
			
			SELECT top 100 A.CONTNO
				,A.LOCAT
				,A.CUSCOD
				,B.SNAM+B.NAME1+' '+B.NAME2+' ('+A.CUSCOD+')-'+B.GRADE as CUSNAME
				,A.STRNO,A.SDATE,A.TOTPRC,A.SMPAY
				,A.SMCHQ,A.TOTPRC-A.SMPAY-A.SMCHQ AS BALANCE
				,A.NDAWN+A.VATDWN AS TOTDWN
				,A.PAYDWN,A.TOTDWN-A.PAYDWN AS BALDWN
				,A.NPAYRES+A.VATPRES AS PAYRES,A.BILLCOLL  
				,A.CONTSTAT,C.CONTDESC
				,(select sa.TELP from {$this->MAuth->getdb('CUSTADDR')} sa where sa.CUSCOD=B.CUSCOD and sa.ADDRNO=B.ADDRNO) as TELP
			FROM {$this->MAuth->getdb('ARMAST')} A
			left join {$this->MAuth->getdb('CUSTMAST')} B on A.CUSCOD=B.CUSCOD
			left join {$this->MAuth->getdb('TYPCONT')} C on A.CONTSTAT=C.CONTTYP
			WHERE 1=1 AND A.TOTPRC>A.SMPAY AND A.CUSCOD like @CUSCOD and A.CONTNO like @CONTNO
			ORDER BY B.NAME1 
		";
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$error = "";
				if($row->BALDWN > 0){ $error = "ผิดพลาด เลขที่สัญญา ".$row->CONTNO."<br>ยังค้างชำระเงินดาวน์อยู่ ".$row->BALDWN." บาท"; }
				
				$html .= "
					<tr>
						<td>
							<button class='cont_selected btn btn-xs btn-warning glyphicon glyphicon-plus' 
								contno  ='".$row->CONTNO."' 
								locat   ='".$row->LOCAT."' 
								cuscod  ='".$row->CUSCOD."' 
								cusname ='".$row->CUSNAME."' 
								telp 	='".$row->TELP."' 
								total   ='".str_replace(",","",(number_format($row->BALANCE,2)))."'
								error   ='".$error."'
								style='cursor:pointer;padding:3px;'> เลือก </button>
						</td>
						<td style='padding:3px;'>".$row->CONTNO."</td>
						<td style='padding:3px;'>".$row->LOCAT."</td>
						<td style='padding:3px;'>".$row->CUSNAME."</td>
						<td style='padding:3px;'>".$row->STRNO."</td>
						<td style='padding:3px;'>".$this->Convertdate(103,$row->SDATE)."</td>
						<td style='padding:3px;' align='right'>".number_format($row->TOTPRC,2)."</td>
						<td style='padding:3px;' align='right'>".number_format($row->SMPAY,2)."</td>
						<td style='padding:3px;' align='right'>".number_format($row->SMCHQ,2)."</td>
						<td style='padding:3px;' align='right'>".number_format($row->BALANCE,2)."</td>
						<td style='padding:3px;' align='right'>".number_format($row->TOTDWN,2)."</td>
						<td style='padding:3px;' align='right'>".number_format($row->PAYDWN,2)."</td>
						<td style='padding:3px;' align='right'>".number_format($row->BALDWN,2)."</td>
						<td style='padding:3px;' align='right'>".number_format($row->PAYRES,2)."</td>
						<td style='padding:3px;'>".$row->BILLCOLL."</td>
						<td style='padding:3px;'>".$row->CONTDESC."</td>
					</tr>
				";
			}
		}
		
		$html = "
			<table border=1 style='width:100%;border-collapse:collapse;'>
				<thead>
					<tr>
						<th style='padding:3px;'>#</th>
						<th style='padding:3px;'>เลขที่สัญญา</th>
						<th style='padding:3px;'>สาขา</th>
						<th style='padding:3px;'>ลูกค้า</th>
						<th style='padding:3px;'>เลขตัวถัง</th>
						<th style='padding:3px;'>วันที่ขาย</th>
						<th style='padding:3px;'>จำนวน</th>
						<th style='padding:3px;'>ชำระแล้ว</th>
						<th style='padding:3px;'>เช็ค</th>
						<th style='padding:3px;'>คงเหลือ</th>
						<th style='padding:3px;'>เงินดาวน์</th>
						<th style='padding:3px;'>ชำระดาวน์</th>
						<th style='padding:3px;'>ค้างดาวน์</th>
						<th style='padding:3px;'>เงินจอง</th>
						<th style='padding:3px;'>Billcoll</th>
						<th style='padding:3px;'>สถานะสัญญา</th>
					</tr>
				</thead>
				<tbody>{$html}</tbody>
			</table>
		";
		
		return $html;
	}
	
	private function getResultCONTNO008($CUSCOD,$CONTNO){
		$sql = "
			declare @CUSCOD varchar(13) = '{$CUSCOD}%';
			declare @CONTNO varchar(13) = '{$CONTNO}%';
			
			SELECT top 100 A.RESVNO as CONTNO
				,A.LOCAT
				,A.CUSCOD
				,A.STRNO
				,B.SNAM+B.NAME1+' '+B.NAME2 as CUSNAME
				,A.RESVDT,A.RESPAY,A.SMPAY,A.SMCHQ
				,A.RESPAY-A.SMPAY-A.SMCHQ AS BALANCE
				,(select sa.TELP from {$this->MAuth->getdb('CUSTADDR')} sa where sa.CUSCOD=B.CUSCOD and sa.ADDRNO=B.ADDRNO) as TELP
			FROM {$this->MAuth->getdb('ARRESV')} A
			left join {$this->MAuth->getdb('CUSTMAST')} B on A.CUSCOD=B.CUSCOD  
			WHERE 1=1 AND A.RESPAY>A.SMPAY AND A.CUSCOD like @CUSCOD and A.RESVNO like @CONTNO
			ORDER BY A.RESVNO,A.LOCAT 
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$html .= "
					<tr>
						<td>
							<button class='cont_selected btn btn-xs btn-warning glyphicon glyphicon-plus' 
								contno	='".$row->CONTNO."' 
								locat   ='".$row->LOCAT."' 
								cuscod	='".$row->CUSCOD."' 
								cusname ='".$row->CUSNAME."' 
								telp 	='".$row->TELP."' 
								total	='".str_replace(",","",(number_format($row->BALANCE,2)))."'
								error	=''
								style='cursor:pointer;padding:3px;'> เลือก </button>
						</td>
						<td style='padding:3px;'>".$row->CONTNO."</td>
						<td style='padding:3px;'>".$row->LOCAT."</td>
						<td style='padding:3px;'>".$row->CUSNAME."</td>
						<td style='padding:3px;'>".$row->STRNO."</td>
						<td style='padding:3px;'>".$this->Convertdate(103,$row->RESVDT)."</td>
						<td style='padding:3px;' align='right'>".number_format($row->RESPAY,2)."</td>
						<td style='padding:3px;' align='right'>".number_format($row->SMPAY,2)."</td>
						<td style='padding:3px;' align='right'>".number_format($row->SMCHQ,2)."</td>
						<td style='padding:3px;' align='right'>".number_format($row->BALANCE,2)."</td>
					</tr>
				";
			}
		}
		
		$html = "
			<table border=1 style='width:100%;border-collapse:collapse;'>
				<thead>
					<tr>
						<th style='padding:3px;'>#</th>
						<th style='padding:3px;'>เลขที่ลูกหนี้อื่น</th>
						<th style='padding:3px;'>สาขา</th>
						<th style='padding:3px;'>ลูกค้า</th>
						<th style='padding:3px;'>เลขตัวถัง</th>
						<th style='padding:3px;'>วันที่ขาย</th>
						<th style='padding:3px;'>จำนวน</th>
						<th style='padding:3px;'>ชำระแล้ว</th>
						<th style='padding:3px;'>เช็ค</th>
						<th style='padding:3px;'>คงเหลือ</th>
					</tr>
				</thead>
				<tbody>{$html}</tbody>
			</table>
		";
		
		return $html;
	}
	
	private function getResultCONTNO009($CUSCOD,$CONTNO){
		$sql = "
			declare @CUSCOD varchar(13) = '{$CUSCOD}%';
			declare @CONTNO varchar(13) = '{$CONTNO}%';
			
			SELECT top 100 A.CONTNO
				,A.LOCAT
				,A.CUSCOD
				,B.SNAM+B.NAME1+' '+B.NAME2 as CUSNAME
				,A.SDATE,A.TOTPRC,A.SMPAY,A.SMCHQ
				,A.TOTPRC-A.SMPAY-A.SMCHQ AS BALANCE
				,(select sa.TELP from {$this->MAuth->getdb('CUSTADDR')} sa where sa.CUSCOD=B.CUSCOD and sa.ADDRNO=B.ADDRNO) as TELP
			FROM {$this->MAuth->getdb('AR_INVOI')} A
			left join {$this->MAuth->getdb('CUSTMAST')} B on A.CUSCOD=B.CUSCOD 
			WHERE 1=1 AND A.TOTPRC>A.SMPAY AND A.CUSCOD like @CUSCOD and A.CONTNO like @CONTNO
			ORDER BY A.CONTNO,A.LOCAT 
		";
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$html .= "
					<tr>
						<td>
							<button class='cont_selected btn btn-xs btn-warning glyphicon glyphicon-plus' 
								contno	='".$row->CONTNO."' 
								locat   ='".$row->LOCAT."' 
								cuscod	='".$row->CUSCOD."' 
								cusname ='".$row->CUSNAME."' 
								telp 	='".$row->TELP."' 
								total	='".str_replace(",","",(number_format($row->BALANCE,2)))."'
								error	=''
								style='cursor:pointer;padding:3px;'> เลือก </button>
						</td>
						<td style='padding:3px;'>".$row->CONTNO."</td>
						<td style='padding:3px;'>".$row->LOCAT."</td>
						<td style='padding:3px;'>".$row->CUSNAME."</td>
						<td style='padding:3px;'>".$this->Convertdate(103,$row->SDATE)."</td>
						<td style='padding:3px;' align='right'>".number_format($row->TOTPRC,2)."</td>
						<td style='padding:3px;' align='right'>".number_format($row->SMPAY,2)."</td>
						<td style='padding:3px;' align='right'>".number_format($row->SMCHQ,2)."</td>
						<td style='padding:3px;' align='right'>".number_format($row->BALANCE,2)."</td>
					</tr>
				";
			}
		}
		
		$html = "
			<table border=1 style='width:100%;border-collapse:collapse;'>
				<thead>
					<tr>
						<th style='padding:3px;'>#</th>
						<th style='padding:3px;'>เลขที่สัญญา</th>
						<th style='padding:3px;'>สาขา</th>
						<th style='padding:3px;'>ลูกค้า</th>
						<th style='padding:3px;'>วันที่ขาย</th>
						<th style='padding:3px;'>จำนวน</th>
						<th style='padding:3px;'>ชำระแล้ว</th>
						<th style='padding:3px;'>เช็ค</th>
						<th style='padding:3px;'>คงเหลือ</th>
					</tr>
				</thead>
				<tbody>{$html}</tbody>
			</table>
		";
		
		return $html;
	}
	
	private function getResultCONTNO011($CUSCOD,$CONTNO){
		$sql = "
			declare @CUSCOD varchar(13) = '{$CUSCOD}%';
			declare @CONTNO varchar(13) = '{$CONTNO}%';
			
			SELECT top 100 A.CONTNO
				,A.LOCAT
				,A.CUSCOD
				,B.SNAM+B.NAME1+' '+B.NAME2 as CUSNAME
				,A.NDAWN,A.VATDWN,A.PAYDWN
				,A.PAYFIN,A.NFINAN,A.VATFIN, A.RESVNO
				,A.TOTPRC,A.SMPAY,A.TOTPRC-A.SMPAY AS BALANCE
				,A.SMCHQ,A.NDAWN+A.VATDWN AS TOTDWN
				,A.PAYDWN,A.NDAWN+A.VATDWN-A.PAYDWN AS BALDWN
				,A.NFINAN+A.VATFIN AS TOTFINC,A.PAYFIN
				,A.NFINAN+A.VATFIN-A.PAYFIN AS BALFINC
				,A.SDATE,A.STRNO,A.FINCOD 
				,(select sa.TELP from {$this->MAuth->getdb('CUSTADDR')} sa where sa.CUSCOD=B.CUSCOD and sa.ADDRNO=B.ADDRNO) as TELP
			FROM {$this->MAuth->getdb('ARFINC')} A
			left join {$this->MAuth->getdb('CUSTMAST')} B on A.CUSCOD=B.CUSCOD 
			WHERE 1=1 AND A.TOTPRC>A.SMPAY AND A.CUSCOD like @CUSCOD and A.CONTNO like @CONTNO
			ORDER BY A.CONTNO,A.LOCAT 
		";
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$html .= "
					<tr>
						<td>
							<button class='cont_selected btn btn-xs btn-warning glyphicon glyphicon-plus' 
								contno	='".$row->CONTNO."' 
								locat   ='".$row->LOCAT."' 
								cuscod	='".$row->CUSCOD."' 
								cusname ='".$row->CUSNAME."' 
								telp 	='".$row->TELP."' 
								total	='".str_replace(",","",(number_format($row->BALANCE,2)))."'
								error	=''
								style='cursor:pointer;padding:3px;'> เลือก </button>
						</td>
						<td style='padding:3px;'>".$row->CONTNO."</td>
						<td style='padding:3px;'>".$row->LOCAT."</td>
						<td style='padding:3px;'>".$row->CUSNAME."</td>
						<td style='padding:3px;'>".$row->STRNO."</td>
						<td style='padding:3px;'>".$this->Convertdate(103,$row->SDATE)."</td>
						<td style='padding:3px;' align='right'>".number_format($row->TOTPRC,2)."</td>
						<td style='padding:3px;' align='right'>".number_format($row->SMPAY,2)."</td>
						<td style='padding:3px;' align='right'>".number_format($row->SMCHQ,2)."</td>
						<td style='padding:3px;' align='right'>".number_format($row->BALANCE,2)."</td>
						<td style='padding:3px;' align='right'>".number_format($row->TOTDWN,2)."</td>
						<td style='padding:3px;' align='right'>".number_format($row->PAYDWN,2)."</td>
						<td style='padding:3px;' align='right'>".number_format($row->BALDWN,2)."</td>
						<td style='padding:3px;' align='right'>".number_format($row->TOTFINC,2)."</td>
						<td style='padding:3px;' align='right'>".number_format($row->PAYFIN,2)."</td>
						<td style='padding:3px;' align='right'>".number_format($row->BALFINC,2)."</td>
						<td style='padding:3px;'>".$row->FINCOD."</td>
					</tr>
				";
			}
		}
		
		$html = "
			<table border=1 style='width:100%;border-collapse:collapse;'>
				<thead>
					<tr>
						<th style='padding:3px;'>#</th>
						<th style='padding:3px;'>เลขที่สัญญา</th>
						<th style='padding:3px;'>สาขา</th>
						<th style='padding:3px;'>ลูกค้า</th>
						<th style='padding:3px;'>เลขตัวถัง</th>
						<th style='padding:3px;'>วันที่ขาย</th>
						<th style='padding:3px;'>จำนวน</th>
						<th style='padding:3px;'>ชำระแล้ว</th>
						<th style='padding:3px;'>เช็ค</th>
						<th style='padding:3px;'>คงเหลือ</th>
						<th style='padding:3px;'>เงินดาวน์</th>
						<th style='padding:3px;'>ชำระดาวน์</th>
						<th style='padding:3px;'>ค้างดาวน์</th>
						<th style='padding:3px;'>ยอดส่งไฟแนนท์</th>
						<th style='padding:3px;'>รับจากไฟแนนท์</th>
						<th style='padding:3px;'>ค้างรับจากไฟแนนท์</th>
						<th style='padding:3px;'>รหัสไฟแนนท์</th>
					</tr>
				</thead>
				<tbody>{$html}</tbody>
			</table>
		";
		
		return $html;
	}
	
	private function getResultCONTNOOTher($CUSCOD,$CONTNO,$PAYFOR){
		$sql = "
			declare @CUSCOD varchar(13) = '{$CUSCOD}%';
			declare @CONTNO varchar(13) = '{$CONTNO}%';
			declare @PAYFOR varchar(13) = '{$PAYFOR}';
			
			SELECT top 100 A.ARCONT as CONTNO
				,A.LOCAT
				,A.CUSCOD
				,B.SNAM+B.NAME1+' '+B.NAME2 as CUSNAME
				,A.PAYFOR,A.ARDATE,A.PAYAMT,A.SMPAY
				,A.SMCHQ,A.PAYAMT-A.SMPAY-A.SMCHQ AS BALANCE
				,(select sa.TELP from {$this->MAuth->getdb('CUSTADDR')} sa where sa.CUSCOD=B.CUSCOD and sa.ADDRNO=B.ADDRNO) as TELP
			FROM {$this->MAuth->getdb('AROTHR')} A
			left join {$this->MAuth->getdb('CUSTMAST')} B on A.CUSCOD=B.CUSCOD 
			WHERE 1=1 AND A.PAYAMT>A.SMPAY 
				AND A.CUSCOD like @CUSCOD 
				and A.ARCONT like @CONTNO
				and A.PAYFOR = @PAYFOR
			ORDER BY A.ARCONT,A.LOCAT
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$html .= "
					<tr>
						<td>
							<button class='cont_selected btn btn-xs btn-warning glyphicon glyphicon-plus' 
								contno	='".$row->CONTNO."' 
								locat   ='".$row->LOCAT."' 
								cuscod	='".$row->CUSCOD."' 
								cusname ='".$row->CUSNAME."' 
								telp 	='".$row->TELP."' 
								total	='".str_replace(",","",(number_format($row->BALANCE,2)))."'
								error	=''
								style='cursor:pointer;padding:3px;'> เลือก </button>
						</td>
						<td style='padding:3px;'>".$row->CONTNO."</td>
						<td style='padding:3px;'>".$row->LOCAT."</td>
						<td style='padding:3px;'>".$row->CUSNAME."</td>
						<td style='padding:3px;'>".$row->PAYFOR."</td>
						<td style='padding:3px;'>".$this->Convertdate(103,$row->ARDATE)."</td>
						<td style='padding:3px;' align='right'>".number_format($row->PAYAMT,2)."</td>
						<td style='padding:3px;' align='right'>".number_format($row->SMPAY,2)."</td>
						<td style='padding:3px;' align='right'>".number_format($row->SMCHQ,2)."</td>
						<td style='padding:3px;' align='right'>".number_format($row->BALANCE,2)."</td>
					</tr>
				";
			}
		}
		
		$html = "
			<table border=1 style='width:100%;border-collapse:collapse;'>
				<thead>
					<tr>
						<th style='padding:3px;'>#</th>
						<th style='padding:3px;'>เลขที่ลูกหนี้อื่น</th>
						<th style='padding:3px;'>สาขา</th>
						<th style='padding:3px;'>ลูกค้า</th>
						<th style='padding:3px;'>ค่าชำระค่า</th>
						<th style='padding:3px;'>วันที่ตั้งลูกหนี้</th>
						<th style='padding:3px;'>จำนวน</th>
						<th style='padding:3px;'>ชำระแล้ว</th>
						<th style='padding:3px;'>เช็ค</th>
						<th style='padding:3px;'>คงเหลือ</th>
					</tr>
				</thead>
				<tbody>{$html}</tbody>
			</table>
		";
		
		return $html;
	}
		
	private function getResultCONTNOOTherCustomers($CUSCOD,$CONTNO,$PAYFOR){
		$sql = "
			select CUSCOD,SNAM+NAME1+' '+NAME2 as CUSNAME 
				,(select sa.TELP from {$this->MAuth->getdb('CUSTADDR')} sa where sa.CUSCOD=a.CUSCOD and sa.ADDRNO=a.ADDRNO) as TELP
			from {$this->MAuth->getdb('CUSTMAST')} a
			where a.CUSCOD='{$CUSCOD}'
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$html .= "
					<tr>
						<td>
							<button class='cont_selected btn btn-xs btn-warning glyphicon glyphicon-plus' 
								contno	='".$row->CUSCOD."' 
								locat   =''
								cuscod	='".$row->CUSCOD."' 
								cusname ='".$row->CUSNAME."' 
								telp ='".$row->TELP."' 
								total	='0'
								error	=''
								style='cursor:pointer;padding:3px;'> เลือก </button>
						</td>
						<td style='padding:3px;'>".$row->CUSCOD."</td>
						<td style='padding:3px;'>".$row->CUSNAME."</td>
					</tr>
				";
			}
		}
		
		$html = "
			<table border=1 style='width:100%;border-collapse:collapse;'>
				<thead>
					<tr>
						<th style='padding:3px;'>#</th>
						<th style='padding:3px;'>รหัสลูกค้า</th>
						<th style='padding:3px;'>ลูกค้า</th>
					</tr>
				</thead>
				<tbody>{$html}</tbody>
			</table>
		";
		
		return $html;
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
	
	function getBILLForCancel(){
		$sess = $this->session->userdata('cbjsess001');
		$dataSearch = trim($_REQUEST['q']);
		$dataNow = (!isset($_REQUEST["now"]) ? "" : $_REQUEST["now"]);
		
		$sql = "
			select TOPICID,'('+cast(TOPICID as varchar)+') '+TOPICName as TOPICName from {$this->MAuth->getdb('JD_BILLTP')}
			where 1=1 and TOPICID='".$dataNow."' collate Thai_CI_AS
				
			union
			select top 20 TOPICID,'('+cast(TOPICID as varchar)+') '+TOPICName as TOPICName from {$this->MAuth->getdb('JD_BILLTP')}
			where 1=1 and '('+cast(TOPICID as varchar)+') '+TOPICName like '%".$dataSearch."%' collate Thai_CI_AS 
			order by TOPICID
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$json[] = ['id'=>str_replace(chr(0),'',$row->TOPICID), 'text'=>str_replace(chr(0),'',$row->TOPICName)];
			}
		}
		
		echo json_encode($json);
	}
	
}




















