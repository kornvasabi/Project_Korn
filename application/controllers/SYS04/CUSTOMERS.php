<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/********************************************************
             ______@04/11/2019______
			 Pasakorn

********************************************************/
class CUSTOMERS extends MY_Controller {
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
	function index(){
		$claim = $this->MLogin->getclaim(uri_string());
		if($claim['m_access'] != "T"){ echo "<div align='center' style='color:red;font-size:16pt;width:100%;'>ขออภัย คุณยังไม่มีสิทธิเข้าใช้งานหน้านี้ครับ</div>"; exit; }
		$html = "
			<div class='tab1' name='home' locat='{$this->sess['branch']}' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}'>
				<div class='col-sm-12'>					
					<div class='row'>
						<div class=' col-sm-2'>	
							<div class='form-group'>
								รหัสลูกค้า
								<input type='text' id='cuscod' class='form-control input-sm' placeholder='รหัสลูกค้า' >
							</div>
						</div>
						<div class=' col-sm-3'>	
							<div class='form-group'>
								ชื่อ-สกุล  ลูกค้า
								<input type='text' id='surname' class='form-control input-sm' placeholder='ชื่อ-สกุล ลูกค้า' >
							</div>
						</div>
						<div class=' col-sm-3'>	
							<div class='form-group'>
								ที่อยู่ลูกค้า
								<input type='text' id='address' class='form-control input-sm' placeholder='ที่อยู่ลูกค้า' >
							</div>
						</div>
						<div class=' col-sm-2'>	
							<div class='form-group'>
								<br>
								<button id='search_groupcm' class='btn btn-primary btn-sm' style='width:100%'><span class='glyphicon glyphicon-search'><b>แสดง</b></span></button>
							</div>
						</div>
						<div class=' col-sm-2'>	
							<div class='form-group'>
								<br>
								<button id='add_custmast' class='btn btn-cyan btn-sm' style='width:100%'><span class='fa fa-plus-square'><b>เพิ่ม</b></span></button>
							</div>
						</div>
						<div id='setgroupResult' class='col-sm-12 tab1' style='height:calc(100vh - 197px);overflow:auto;background-color:#;'></div>
					</div>		
				</div>
			</div>
		";
		$html.= "<script src='".base_url('public/js/SYS04/CUSTOMERS.js')."'></script>";
		echo $html;
	}
	function groupSearchcm(){
		$arrs = array();
		$arrs['cuscod'] = !isset($_REQUEST['cuscod']) ? '' : $_REQUEST['cuscod'];
		$arrs['surname'] = !isset($_REQUEST['surname']) ? '' : $_REQUEST['surname'];
		$arrs['address'] = !isset($_REQUEST['address']) ? '' : $_REQUEST['address'];
		$top = "";
		$cond = "";
		if($arrs['cuscod'] != ''){
			$cond .= " and CUSCOD like '%".$arrs['cuscod']."%'";
			$top = "";
		}else{
			$top = "top 100";
		}
		if($arrs['surname'] != ''){
			$cond .= " and NAME1 like '%".$arrs['surname']."%'";
			$top = "";
		}else{
			$top = "top 100";
		}
		if($arrs['surname'] != ''){
			$cond .= " or NAME2 like '%".$arrs['surname']."%'";
			$top = "";
		}else{
			$top = "top 100";
		}
		if($arrs['address'] != ''){
			$cond .= " and ADDR like '%".$arrs['address']."%'";
			$top = "";
		}else{
			$top = "top 100";
		}
		$sql = "
			select ".$top." * from (
				select replace(STUFF(
					(
						select '๛' 
						+' '+ CONVERT(nvarchar(20),CC.ADDRNO)
						+'. '+'บ้านเลขที่ '+ convert(nvarchar(20),CC.ADDR1) 
						+' '+'ซอย '+ convert(nvarchar(20),CC.SOI)
						+' '+'ถนน '+ convert(nvarchar(20),CC.ADDR2)
						+' '+'หมู่บ้าน '+ CONVERT(nvarchar(20),CC.MOOBAN)
						+' '+'ตำบล '+ convert(nvarchar(20),CC.TUMB)
						+' '+'อำเภอ '+ convert(nvarchar(20),B.AUMPDES)
						+' '+'จังหวัด '+ convert(nvarchar(20),C.PROVDES)
						+' '+'รหัสไปรษณีย์ '+ CONVERT(nvarchar(20),CC.ZIP)
						+' '+'เบอร์โทร '+ CONVERT(nvarchar(20),CC.TELP)
						from {$this->MAuth->getdb('CUSTADDR')} CC left join {$this->MAuth->getdb('SETAUMP')} B on 
						CC.AUMPCOD = B.AUMPCOD 
						left join {$this->MAuth->getdb('SETPROV')} C on CC.PROVCOD=C.PROVCOD
						where A.CUSCOD = CC.CUSCOD FOR XML path('') 
					),1, 1, ''
				),'๛','<br>') as ADDR,CONVERT(varchar(8),BIRTHDT,112) as BIRT,* from {$this->MAuth->getdb('CUSTMAST')} A 
			)A
			where 1=1 ".$cond." order by CUSCOD
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$html .="
					<tr>
						<td style='width:70px;text-align:left;'>
							<button CUSCOD ='{$row->CUSCOD}' class='btnDetail btn btn-xs btn-info' style='width:100%;color:'>
								<span class='fa fa-edit'><b>รายละเอียด</b></span>
							</button>
						</td>
						<td style='width:70px;text-align:center;'>".$row->CUSCOD."</td>
						<td style='width:70px;text-align:left;'>".$row->NAME1." ".$row->NAME2."</td>
						<td style='width:70px;text-align:center;'>".$this->Convertdate(2,$row->BIRT)."</td>
						<td style='width:70px;text-align:center;'>".$row->AGE."</td>
						<td style='width:70px;text-align:left;'>".$row->OCCUP."</td>
						<td style='width:70px;text-align:left;'>".$row->ADDR."</td>
					</tr>
				";
			}
		}
		/*
		$sql = "select top 20 * from CUSTMAST where 1=1 ".$cond." order by CUSCOD";
		$query = $this->db->query($sql);
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$sql2  = "
					select * from CUSTADDR where 1=1 and CUSCOD = '{$row->CUSCOD}' 
				";
				$query2 = $this->db->query($sql2);
				$addr  = "";
				if($query2->row()){
					foreach($query2->result() as $row2){
						if($addr <> '') { $addr .= "<br>"; }
						$addr .= "{$row2->ADDR1} {$row2->ADDR2}";
					}
				}
				$html .= "
					<tr>
						<td style='width:70px;text-align:left;'><button CUSCOD ='".$row->CUSCOD."' class='btnDetail btn btn-xs btn-info btn btn-cyan' style='width:100%'><span class='fa fa-edit'><b>รายละเอียด</b></span></button></td>
						<td style='width:70px;text-align:center;'>".$row->CUSCOD."</td>
						<td style='width:70px;text-align:left;'>".$row->NAME1." ".$row->NAME2."</td>
						<td style='width:70px;text-align:center;'>".$this->dateselectshow($row->BIRTHDT)."</td>
						<td style='width:70px;text-align:center;'>".$row->AGE."</td>
						<td style='width:70px;text-align:center;'>".$row->OCCUP."</td>
						<td style='width:70px;text-align:left;'>".$addr."</td>
					</tr>
				";
			}
		}
		*/
		$html = "
			<div id='tbScroll' class='col-sm-12' style='height:calc(100% - 30px);width:100%;overflow:auto;font-size:8pt;'>
				<table id='data-table-example2' class='table table-bordered' cellspacing='0' width='calc(100% - 1px)'>
					<thead style='background: rgba(0, 0, 0, 0) url(&#39;../public/lobiadmin-master/version/1.0/ajax/img/bg/bg6.png&#39;) repeat scroll 0% 0%;'>
						<!-- tr>
							<th colspan='4' align='center'>
								<span style='cursor:pointer;'>Excel</span>
								<span style='cursor:pointer;'>PDF</span>
							</th>
						</tr -->
						<tr>
							<th style='text-align:center;color:blue;' colspan='7'>ประวัติลูกค้า</th>
						</tr>
						<tr>
							<th>#</th>
							<th>รหัสลูกค้า</th>
							<th>ชื่อ-สกุล</th>		
							<th>วัน-เดือน-ปี เกิด</th>	
							<th>อายุ</th>	
							<th>อาชีพ</th>	
							<th>ที่อยู่</th>
						</tr>
					</thead>	
					<tbody>
						".$html."				
					</tbody>
				</table>
			</div>
		";
		$response = array();
		$response['html'] = $html;
		echo json_encode($response);
	}
	function groupShowca(){
		$arrs = array();
		$arrs['CUSCOD'] = $_POST['CUSCOD'];
		
		$sql ="
            select A.CUSCOD,A.ADDRNO,A.ADDR1,(select house from HIC2SHORTL.dbo.FN_JD_REPLACEADDR(ADDR1)) as HOUSE
			,(select swine from HIC2SHORTL.dbo.FN_JD_REPLACEADDR(ADDR1)) as SWIN,A.ADDR2,A.TUMB
			,A.AUMPCOD,A.PROVCOD,A.ZIP,A.TELP,A.MEMO1,A.ACPDT
			,A.USERID,A.PICT1,A.MOOBAN,A.SOI,B.PROVCOD,B.AUMPCOD,B.AUMPDES
			,C.PROVCOD,C.PROVDES from {$this->MAuth->getdb('CUSTADDR')} A
			left join {$this->MAuth->getdb('SETAUMP')} B on A.AUMPCOD=B.AUMPCOD 
			left join {$this->MAuth->getdb('SETPROV')} C on B.PROVCOD=C.PROVCOD
			where CUSCOD='".$arrs['CUSCOD']."'
			order by ADDRNO";
			
		//echo $sql ; exit;
        $query = $this->db->query($sql);
		$html = "";
		
		if($query->row()){
			foreach($query->result() as $row){
				$html .= "
					<tr>
						<td>".str_replace(chr(0),'',$row->ADDRNO)."</td>
						<td>
							บ้านเลขที่ ".str_replace(chr(0),'',$row->HOUSE)."
							หมู่ที่ ".str_replace(chr(0),'',$row->SWIN)."
							ซอย ".str_replace(chr(0),'',$row->SOI)."
							ถนน ".str_replace(chr(0),'',$row->ADDR2)."
							ตำบล ".str_replace(chr(0),'',$row->TUMB)."
							หมู่บ้าน ".str_replace(chr(0),'',$row->MOOBAN)."
							อำเภอ ".str_replace(chr(0),'',$row->AUMPDES)."
							จังหวัด ".str_replace(chr(0),'',$row->PROVDES)."
							รหัสไปรษณีย์ ".str_replace(chr(0),'',$row->ZIP)."
						</td>
						<td>".str_replace(chr(0),'',$row->TELP)."</td>
						<td>".str_replace(chr(0),'',$row->MEMO1)."</td>
					</tr>
				";
			}
		}
		$html = "
			<div id='tbScroll' class='col-sm-12'>
				<table id='data-table-example2' class='col-sm-12 display table table-striped table-bordered table-hover' cellspacing='0' width='100%'>
					<thead>
						<!-- tr>
							<th colspan='4' align='center'>
								<span style='cursor:pointer;'>Excel</span>
								<span style='cursor:pointer;'>PDF</span>
							</th>
						</tr -->
						<tr>
							<th>ลำดับ</th>
							<th>ที่อยู่</th>
							<th>เบอร์ติดต่อ</th>
							<th>หมายเหตุ</th>
						</tr>
					</thead>	
					<tbody>
						".$html."				
					</tbody>
				</table>
			</div>
		";
		$response = array();
		$response['html'] = $html;
		echo json_encode($response);
	}
	function groupGetFromCM(){
		$CUSCOD = $_POST["CUSCOD"];
		$EVENT  = $_POST['EVENT'];
		$arrs 	= array();
		$arrs['CUSCOD']     = "Auto Genarate";
		$arrs['GROUP1']     = "";
		$arrs['GRADE']      = "";
		$arrs['SNAM']       = "";
		$arrs['NAME1']      = "";
		$arrs['NAME2']      = "";
		$arrs['NICKNM']     = "";
		$arrs['BIRTHDT']    = "";
		$arrs['IDCARD']     = "";
		$arrs['IDNO']       = "";
		$arrs['ISSUBY']     = "";
		$arrs['ISSUDT']     = "";
		$arrs['EXPDT']      = "";
		$arrs['AGE']        = "";
		$arrs['NATION']     = "";
		$arrs['OCCUP']      = "";
		$arrs['OFFIC']      = "";
		$arrs['MAXCRED']    = "";
		$arrs['MREVENU']    = "";
		$arrs['YREVENU']    = "";
		$arrs['MOBILENO']   = "";
		$arrs['EMAIL1']     = "";
		$arrs['ADDRNO1']	= "";
		$arrs['ADDRNO2']    = "";
		$arrs['ADDRNO3']    = "";
		$arrs['MEMOADD']   	= "";
		$sql = "
			select * from {$this->MAuth->getdb('CUSTMAST')} where CUSCOD = '".$CUSCOD."'
		";
        $query = $this->db->query($sql);
		
		if($query->row()){
			foreach($query->result() as $row){
				$arrs['CUSCOD']     = $row->CUSCOD;
				$arrs['SNAM']		= $row->SNAM;
				$arrs['NAME1']      = $row->NAME1;
				$arrs['NAME2']      = $row->NAME2;
				$arrs['NICKNM']     = $row->NICKNM;
				$arrs['BIRTHDT']    = $row->BIRTHDT;
				$arrs['IDCARD']     = $row->IDCARD;
				$arrs['IDNO']       = $row->IDNO;
				$arrs['ISSUBY']     = $row->ISSUBY;
				$arrs['ISSUDT']     = $row->ISSUDT;
				$arrs['EXPDT']      = $row->EXPDT;
				$arrs['AGE']        = $row->AGE;
				$arrs['NATION']     = $row->NATION;
				$arrs['OCCUP']      = $row->OCCUP;
				$arrs['OFFIC']      = $row->OFFIC;
				$arrs['MAXCRED']    = number_format($row->MAXCRED,2);
				$arrs['MREVENU']    = number_format($row->MREVENU,2);
				$arrs['YREVENU']    = number_format($row->YREVENU,2);
				$arrs['MOBILENO']   = $row->MOBILENO;
				$arrs['EMAIL1']     = $row->EMAIL1;
				$arrs['ADDRNO1']	= $row->ADDRNO;
				$arrs['ADDRNO2']    = $row->ADDRNO2;
				$arrs['ADDRNO3']    = $row->ADDRNO3;
				$arrs['MEMOADD']    = $row->MEMO1;
			}
		}
		//print_r($arrs); exit;
		//select2 กลุ่มลูกค้า
		$sqlA = "
			select * from {$this->MAuth->getdb('CUSTMAST')} A
            left join {$this->MAuth->getdb('ARGROUP')} B on A.GROUP1=B.ARGCOD where A.CUSCOD = '".$CUSCOD."' 
		";
        $queryA = $this->db->query($sqlA);
        if($queryA->row()){
			foreach($queryA->result() as $rowA){
				$arrs['GROUP1'] = "<option value='".str_replace(chr(0),"",$rowA->ARGCOD)."'>".str_replace(chr(0),"",$rowA->ARGDES)."</option>";
			}
		}
		$sqlB ="
			select * from {$this->MAuth->getdb('CUSTMAST')} A
            left join {$this->MAuth->getdb('SETGRADCUS')} B on A.GRADE=B.GRDCOD where A.CUSCOD ='".$CUSCOD."'
		";
		$queryB = $this->db->query($sqlB);
		if($queryB->row()){
			foreach($queryB->result() as $rowB){
				$arrs['GRADE'] = "<option value='".str_replace(chr(0),"",$rowB->GRDCOD)."'>".str_replace(chr(0),"",$rowB->GRDCOD)." ".str_replace(chr(0),"",$rowB->GRDDES)."</option>";
			}
		}
		/*
		$sqlC ="
			select * from {$this->MAuth->getdb('CUSTMAST')} A 
            left join {$this->MAuth->getdb('SIRNAM')} B on A.SNAM=B.SIRCOD where A.CUSCOD ='".$CUSCOD."' 
		";
		$queryC = $this->db->query($sqlC);
		if($queryC->row()){
			foreach($queryC->result() as $rowC){
				$arrs['SNAM'] = "<option value='".str_replace(chr(0),"",$rowC->SIRCOD)."'>".str_replace(chr(0),"",$rowC->SIRNAM)."</option>";
			}
		}
		*/
		$sqlD = "
			select A.CUSCOD,A.ADDRNO,A.ADDR1,(select house from HIC2SHORTL.dbo.FN_JD_REPLACEADDR(ADDR1)) as HOUSE
			,(select swine from HIC2SHORTL.dbo.FN_JD_REPLACEADDR(ADDR1)) as SWIN,A.ADDR2,A.TUMB
			,A.AUMPCOD,A.PROVCOD,A.ZIP,A.TELP,A.MEMO1,A.ACPDT
			,A.USERID,A.PICT1,A.MOOBAN,A.SOI,B.PROVCOD,B.AUMPCOD,B.AUMPDES
			,C.PROVCOD,C.PROVDES from {$this->MAuth->getdb('CUSTADDR')} A
			left join {$this->MAuth->getdb('SETAUMP')} B on A.AUMPCOD=B.AUMPCOD 
			left join {$this->MAuth->getdb('SETPROV')} C on B.PROVCOD=C.PROVCOD
			where CUSCOD='".$CUSCOD."'
			order by ADDRNO
		";
		//echo $sqlD; exit;
		$tbody ="";
		$addrno1="";
		$addrno2="";
		$addrno3="";
		$queryD = $this->db->query($sqlD);
		if($queryD->row()){
			foreach($queryD->result() as $row){
				$tbody .= "
					<tr>
						<td><button class='btnEditAddrTable btn btn-sm btn-warning fa fa-edit' 
							ADDRNO  = '".str_replace(chr(0),'',$row->ADDRNO)."'
							ADDR1   = '".str_replace(chr(0),'',$row->HOUSE)."'
							SOI     = '".str_replace(chr(0),'',$row->SOI)."'
							ADDR2   = '".str_replace(chr(0),'',$row->ADDR2)."'
							MOOBAN  = '".str_replace(chr(0),'',$row->MOOBAN)."'
							TUMB    = '".str_replace(chr(0),'',$row->TUMB)."'
							AUMPCOD = '".str_replace(chr(0),'',$row->AUMPCOD)."'
							PROVCOD = '".str_replace(chr(0),'',$row->PROVCOD)."'
							AUMPDES = '".str_replace(chr(0),'',$row->AUMPDES)."'
							PROVDES = '".str_replace(chr(0),'',$row->PROVDES)."'
							ZIP     = '".str_replace(chr(0),'',$row->ZIP)."'
							TELP    = '".str_replace(chr(0),'',$row->TELP)."'
							MEMO1   = '".str_replace(chr(0),'',$row->MEMO1)."'
							SWIN	= '".str_replace(chr(0),'',$row->SWIN)."'
							>แก้ไข</button>
						</td>
						<td>".str_replace(chr(0),'',$row->ADDRNO)."</td>
						<td>
							บ้านเลขที่ ".str_replace(chr(0),'',$row->HOUSE)."
							หมู่ที่ ".str_replace(chr(0),'',$row->SWIN)."
							ซอย ".str_replace(chr(0),'',$row->SOI)."
							ถนน ".str_replace(chr(0),'',$row->ADDR2)."
							หมู่บ้าน ".str_replace(chr(0),'',$row->MOOBAN)."
							ตำบล".str_replace(chr(0),'',$row->TUMB)."
							อำเภอ".str_replace(chr(0),'',$row->AUMPDES)."
							จังหวัด".str_replace(chr(0),'',$row->PROVDES)."
							รหัสไปรษณีย์ ".str_replace(chr(0),'',$row->ZIP)."
						</td>
						<td>".str_replace(chr(0),'',$row->TELP)."</td>
						<td>".str_replace(chr(0),'',$row->MEMO1)."</td>
						<td>
							<button class='btnDelAddrTable btn btn-sm btn-danger fa fa-trash'>ลบ</button>
						</td>
					</tr>
				";
				$addrno1 .= "<option value='".str_replace(chr(0),'',$row->ADDRNO)."'>".str_replace(chr(0),'',$row->ADDRNO)."</option>";
				$addrno2 .= "<option value='".str_replace(chr(0),'',$row->ADDRNO)."'>".str_replace(chr(0),'',$row->ADDRNO)."</option>";
				$addrno3 .= "<option value='".str_replace(chr(0),'',$row->ADDRNO)."'>".str_replace(chr(0),'',$row->ADDRNO)."</option>";
			}
		}
		$html ="
			<div class='col-sm-10 col-sm-offset-1'>
				<div class='row'>
					<div class='col-sm-4'>
						สาขา
						<select id='add_locat' class='form-control input-sm'>
							<option value='".$this->sess['branch']."'>".$this->sess['branch']."</option>
						</select>
					</div>
				</div>
			</div>
			<div class='col-sm-10 col-sm-offset-1'>
				<div class='row'>
					<div class='col-sm-4'>
						รหัสลูกค้า
						<input type='text' class='form-control input-sm' id='CUSCOD' value='{$arrs['CUSCOD']}' readonly>
					</div>
					<div class='col-sm-4'>
						กลุ่มลูกค้า
						<select type='text' class='form-control' id='GROUP1'>
						{$arrs['GROUP1']}
						</select>
					</div>
					<div class='col-sm-4'>
						เกรด
						<select type='text' class='form-control' id='GRADE'>
						{$arrs['GRADE']}
						</select>
					</div>
				</div>
			</div>
			<div class='col-sm-10 col-sm-offset-1'>
				<div class='row'>
					<div class='col-sm-4'>
						คำนำหน้า
						<select type='text' class='form-control' id='SNAM'>
							<option value='".$arrs['SNAM']."'>".$arrs['SNAM']."</option>
						</select>
					</div>
					<div class='col-sm-4'>
						ชื่อ
						<input type='text' class='form-control input-sm checkvalue' id='NAME1' value='{$arrs['NAME1']}'>
					</div>
					<div class='col-sm-4'>
						นามสกุล
						<input type='text' class='form-control input-sm checkvalue' id='NAME2' value='{$arrs['NAME2']}'>
					</div>
				</div>
			</div>
			<div class='col-sm-10 col-sm-offset-1'>
				<div class='row'>
					<div class='col-sm-4'>
						ชื่อเล่น
						<input type='text' class='form-control input-sm checkvalue' id='NICKNM' value='{$arrs['NICKNM']}'>
					</div>
					<div class='col-sm-4'>
						วัน/เดือน/ปี เกิด
						<input type='text' id='BIRTHDT' class='form-control input-sm' placeholder='วว/ดด/ปป' data-provide='datepicker' data-date-language='th-th' value='".$this->dateselectshow($arrs['BIRTHDT'])."'>
					</div>
					<div class='col-sm-4'>
						ประเภทบัตรประจำตัว
						<select type='text' class='form-control input-sm' id='IDCARD' >
							<option></option>
							<option value='บัตรประชาชน' ".($arrs['IDCARD'] == 'บัตรประชาชน' ? "selected":"").">บัตรประชาชน</option>
							<option value='บัตรข้าราชการ/รัฐวิสาหกิจ' ".($arrs['IDCARD'] == 'บัตรข้าราชการ/รัฐวิสาหกิจ' ? "selected":"").">บัตรข้าราชการ/รัฐวิสาหกิจ</option>
							<option value='ทะเบียนการค้า' ".($arrs['IDCARD'] == 'ทะเบียนการค้า' ? "selected":"").">ทะเบียนการค้า</option>
							<option value='บัตรต่างด้าว' ".($arrs['IDCARD'] == 'บัตรต่างด้าว' ? "selected":"").">บัตรต่างด้าว</option>
							<option value='ไม่ระบุ' ".($arrs['IDCARD'] == 'ไม่ระบุ' ? "selected":"").">ไม่ระบุ</option>
							<option value='อื่นๆ' ".($arrs['IDCARD'] == 'อื่นๆ' ? "selected":"").">อื่นๆ</option>
						</select>
					</div>
				</div>
			</div>
			<div class='col-sm-10 col-sm-offset-1'>
				<div class='row'>
					<div class='col-sm-4'>
						เลขที่
						<input type='text' maxlength='13' class='form-control input-sm checkvalue' id='IDNO' value='{$arrs['IDNO']}'>
					</div>
					<div class='col-sm-4'>
						ออกโดย
						<input type='text' class='form-control input-sm checkvalue' id='ISSUBY' value='{$arrs['ISSUBY']}'>
					</div>
					<div class='col-sm-4'>
						วัน/เดือน/ปี ที่ออกบัตร
						<input type='text' id='ISSUDT' class='form-control input-sm' placeholder='วว/ดด/ปป' data-provide='datepicker' data-date-language='th-th' value='".$this->dateselectshow($arrs['ISSUDT'])."'>
					</div>    
				</div>
			</div>
			<div class='col-sm-10 col-sm-offset-1'>
				<div class='row'>
					<div class='col-sm-4'>
						วัน/เดือน/ปี บัตรหมดอายุ
						<input type='text' id='EXPDT' class='form-control input-sm' placeholder='วว/ดด/ปป' data-provide='datepicker' data-date-language='th-th' value='".$this->dateselectshow($arrs['EXPDT'])."'>
					</div>
					<div class='col-sm-4'>
						อายุ
						<input type='text' class='form-control input-sm checkvalue' id='AGE' value='{$arrs['AGE']}'>
					</div>
					<div class='col-sm-4'>
						สัญชาติ
						<select type='text' class='form-control input-sm' id='NATION' >
							<option></option>
							<option value='ไทย' ".($arrs['NATION'] == 'ไทย' ? "selected":"").">ไทย</option>
							<option value='จีน' ".($arrs['NATION'] == 'จีน' ? "selected":"").">จีน</option>
							<option value='ลาว' ".($arrs['NATION'] == 'ลาว' ? "selected":"").">ลาว</option>
							<option value='เขมร' ".($arrs['NATION'] == 'เขมร' ? "selected":"").">เขมร</option>
							<option value='มาเลเซีย' ".($arrs['NATION'] == 'มาเลเซีย' ? "selected":"").">มาเลเซีย</option>
							<option value='พม่า' ".($arrs['NATION'] == 'พม่า' ? "selected":"").">พม่า</option>
						</select>
					</div>
				</div>
			</div>
			<div class='col-sm-10 col-sm-offset-1'>
				<div class='row'>
					<div class='col-sm-4'>
						อาชีพ
						<input type='text' class='form-control input-sm checkvalue' id='OCCUP' value='{$arrs['OCCUP']}'>
					</div>
					<div class='col-sm-4'>
						สถานที่ทำงาน
						<input type='text' class='form-control input-sm checkvalue' id='OFFIC' value='{$arrs['OFFIC']}'>
					</div>
					<div class='col-sm-4'>
						วงเงินเครดิต
						<input type='text' class='form-control input-sm checkvalue' id='MAXCRED' value='{$arrs['MAXCRED']}'>
					</div>
				</div>
			</div>
			<div class='col-sm-10 col-sm-offset-1'>
				<div class='row'>
					<div class='col-sm-4'>
						รายได้ต่อเดือน
						<input type='text' class='form-control input-sm checkvalue' id='MREVENU' value='{$arrs['MREVENU']}'>
						</div>
					<div class='col-sm-4'>
						รายได้พิเศษต่อปี  
						<input type='text' class='form-control input-sm checkvalue' id='YREVENU' value='{$arrs['YREVENU']}'>
					</div>
					<div class='col-sm-4'>
						เบอร์โทร
						<input type='text' maxlength='10' class='form-control input-sm checkvalue' id='MOBILENO' maxlength='10' value='{$arrs['MOBILENO']}'>
					</div>
				</div>
			</div>
			<div class='col-sm-10 col-sm-offset-1'>
				<div class='row'>
					<div class='col-sm-4'>
						อีเมล์
						<input type='email' class='form-control input-sm checkvalue' id='EMAIL1' value='{$arrs['EMAIL1']}'><br>
					</div>
				</div>
			</div>
			<div class='col-sm-10 col-sm-offset-1'>
				<h3 class='text-primary'>เพิ่มที่อยู่ลูกค้า</h3>
				<div class='row' style='border:1px dotted #aaa; height:100%;overflow:auto;'>
					<table id = 'data-table-address' class='col-sm-12 display table table-striped table-bordered table-hover' cellspacing='0' width='100%'>
						<thead>
							<tr>
								<th><center>#</center></th>
								<th>ลำดับ</th>
								<th>ที่อยู่</th>
								<th>เบอร์ติดต่อ</th>
								<th>หมายเหตุ</th>
								<th>ลบ</th>
							</tr>
						</thead>
						<tbody id='tableaddr'>
							".$tbody."
						</tbody>
						<tfoot>
							<tr>
								<td colspan='6'>
									<button id='btnAddAddressFirst' class='btn btn-sm btn-cyan btn-block fa fa-plus-square'>เพิ่มที่อยู่</button>
								</td>	
							</tr>
						</tfoot>
					</table>
					<div class='col-sm-4'>
						ที่อยู่ตามทะเบียนบ้าน
						<select type='text' class='form-control' id='addrno1'>
							".$addrno1."
						</select>
					</div>
					<div class='col-sm-4'>
						ที่อยู่ปัจจุบัน
						<select type='text' class='form-control' id='addrno2'>
							".$addrno2."
						</select>
					</div>
					<div class='col-sm-4'>
						ที่อยู่ที่อยู่ที่ส่งจดหมาย
						<select type='text' class='form-control' id='addrno3'>
							".$addrno3."
						</select>
					</div>
				</div>
			</div>
			<div class='col-sm-10 col-sm-offset-1'>
				<div class='row'>
					<div class='col-sm-6'>
						หมายเหตุ
						<textarea class='form-control' id='MEMOADD'}'>{$arrs['MEMOADD']}</textarea>
					</div>
				</div>
			</div>
        ";
		if($EVENT == "add"){
			$html .="
				<div class='col-sm-10 col-sm-offset-1'>
					<div class='row'><br>
						<div class='col-sm-3 col-sm-offset-9'>
							<button type='button' id='add_save' class='btn btn-sm btn-primary btn-block fa fa-floppy-o'>บันทึก</button>
						</div>
					</div>    
				</div>
				<div class='col-sm-12'>&emsp;</div>
			";
		}else{
			$html .="
				<div class='col-sm-10 col-sm-offset-1'>
					<div class='row'><br>
						<div class='col-sm-3 col-sm-offset-6'>
							<button type='button' id='add_update' class='btn btn-sm btn-primary btn-block fa fa-floppy-o'>บันทึก</button>
						</div>
						<div class='col-sm-3'>
							<button type='button' id='btn_Delete' class='btn btn-sm btn-danger btn-block fa fa-trash'>ลบ</button>
						</div>
					</div>    
				</div>
				<div class='col-sm-12'>&emsp;</div>
			";
		}
        $response = array("html"=>$html);
        echo json_encode($response);
	}
	function getAge(){
		$response = array();
		$BIRTHDT = $this->Convertdate(1,$_POST['BIRTHDT']);
		//echo $BIRTHDT;
		$sql = "
			select datediff(month,'".$BIRTHDT."',getdate())/12 as GETAGE 
		";
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$response['getdate'] = $row->GETAGE;
			}
		}
		echo json_encode($response);
	}
	function getFormAddressCM(){
		$arrs = array();
        $arrs["ADDRNO"]	  = (!isset($_POST["ADDRNO"])?  "":$_POST["ADDRNO"]);
        $arrs["ADDR1"] 	  = (!isset($_POST["ADDR1"])?   "":$_POST["ADDR1"]);
		$arrs["SWIN"]	  = (!isset($_POST["SWIN"])?	"":$_POST["SWIN"]);
        $arrs["SOI"] 	  = (!isset($_POST["SOI"])?     "":$_POST["SOI"]);
        $arrs["ADDR2"] 	  = (!isset($_POST["ADDR2"])?   "":$_POST["ADDR2"]);
        $arrs["MOOBAN"]   = (!isset($_POST["MOOBAN"])?  "":$_POST["MOOBAN"]);
        $arrs["TUMB"] 	  = (!isset($_POST["TUMB"])?    "":$_POST["TUMB"]);
        $arrs["AUMPCOD"]  = (!isset($_POST["AUMPCOD"])? "":$_POST["AUMPCOD"]);
        $arrs["PROVCOD"]  = (!isset($_POST["PROVCOD"])? "":$_POST["PROVCOD"]);
        $arrs["AUMPDES"]  = (!isset($_POST["AUMPDES"])? "":$_POST["AUMPDES"]);
        $arrs["PROVDES"]  = (!isset($_POST["PROVDES"])? "":$_POST["PROVDES"]);
        $arrs["ZIP"] 	  = (!isset($_POST["ZIP"])?     "":$_POST["ZIP"]);
        $arrs["TELP"] 	  = (!isset($_POST["TELP"])?    "":$_POST["TELP"]);
        $arrs["MEMO1"] 	  = (!isset($_POST["MEMO1"])?   "":$_POST["MEMO1"]);
		
		$arrs['ACTION']   = $_POST['ACTION'];
		
		$DIS ="";
		if($arrs["ADDRNO"] !== ""){
			$DIS = "disabled";
		}
		$html ="
			<div class='col-sm-10 col-sm-offset-1'>
				<div class='row'>
					<div class='col-sm-12'>
						ลำดับ
						<input type='number' class='form-control' id='ADDRNO' value='".$arrs["ADDRNO"]."' ".$DIS.">
					</div>
					<div class='col-sm-12'>
						บ้านเลขที่
						<input type='text' class='form-control' id='ADDR1' value='".$arrs["ADDR1"]."'>
					</div>  
					<div class='col-sm-12'>
						หมู่ที่
						<input type='text' class='form-control' id='SWIN' value='".$arrs["SWIN"]."'>
					</div>
					<div class='col-sm-12'>
						ซอย
						<input type='text' class='form-control' id='SOI' value='".$arrs["SOI"]."'>
					</div>
					<div class='col-sm-12'>
						ถนน
						<input type='text' class='form-control' id='ADDR2' value='".$arrs["ADDR2"]."'>
					</div>
					<div class='col-sm-12'>
						หมู่บ้าน
						<input type='text' class='form-control' id='MOOBAN' value='".$arrs["MOOBAN"]."'>
					</div>
					<div class='col-sm-12'>
						ตำบล
						<input type='text' class='form-control' id='TUMB' value='".$arrs["TUMB"]."'>
					</div>
					<div class='col-sm-12'>
						อำเภอ
						<select type='text' id='AUMPCOD' class='form-control'>
							<option value='".$arrs["AUMPCOD"]."'>".$arrs["AUMPDES"]."</option>
						</select>
					</div>
					<div class='col-sm-12'>
						จังหวัด
						<select type='text' id='PROVCOD' class='form-control'>
							<option value='".$arrs["PROVCOD"]."'>".$arrs["PROVDES"]."</option>
						</select>
					</div>
					<div class='col-sm-12'>
						รหัสไปรษณีย์
						<select type='text' id='ZIP' class='form-control'>
							<option value='".$arrs["ZIP"]."'>".$arrs["ZIP"]."</option>
						</select>
					</div>
					<div class='col-sm-12'>
						เบอร์โทรศัพท์ติดต่อ
						<input type='text' class='form-control' id='TELP' value='".$arrs["TELP"]."'>
					</div>
					<div class='col-sm-12'>
						หมายเหตุ
						<textarea class='form-control' rows='2' cols='30' id='MEMO1' value='".$arrs["MEMO1"]."'>".$arrs["MEMO1"]."</textarea><br>
					</div>
				</div>
			</div>
		";
		if($arrs["ACTION"] == "add"){//btnAddAddr
			$html .="
				<div class='row col-sm-12'>
					<div class='col-sm-6'>
						<button id='btnAddTableHtml' class='btn btn-block btn-primary fa fa-check-square-o'>เพิ่ม</button><br>					
					</div>
					<div class='col-sm-6'>
						<button id='btnWACloseAdd' class='btn btn-block btn-danger fa fa-remove'>ยกเลิก</button><br>
					</div>
				</div>
			";
		}else{
			$html .="
				<div class='row col-sm-12'>
					<div class='col-sm-6'>
						<button id='btneditTableHtml' class='btn btn-block btn-warning fa fa-edit'>แก้ไข</button><br>					
					</div>
					<div class='col-sm-6'>
						<button id='btnWAClose' class='btn btn-block btn-danger fa fa-remove'>ยกเลิก</button><br>
					</div>
				</div>
			";
		}
		$response = array("html" => $html);
        echo json_encode($response);
	}
	function SetAddr_TableHtml(){
		$response = array('error'=>false,'msg'=>'');
		$arrs = array(); 
		$arrs["ADDRNO"]  = $_POST["ADDRNO"];
        $arrs["ADDR1"]   = $_POST["ADDR1"];
		$arrs["SWIN"]	 = $_POST["SWIN"];
        $arrs["SOI"] 	 = $_POST["SOI"];
        $arrs["ADDR2"] 	 = $_POST["ADDR2"];
        $arrs["MOOBAN"]  = $_POST["MOOBAN"];
        $arrs["TUMB"] 	 = $_POST["TUMB"];
        $arrs["AUMPCOD"] = $_POST["AUMPCOD"];
        $arrs["PROVCOD"] = $_POST["PROVCOD"];
        $arrs["AUMPDES"] = $_POST["AUMPDES"];
        $arrs["PROVDES"] = $_POST["PROVDES"];
        $arrs["ZIP"] 	 = $_POST["ZIP"];
        $arrs["TELP"] 	 = $_POST["TELP"];
        $arrs["MEMO1"] 	 = $_POST["MEMO1"];
		
		if($arrs['ADDRNO'] ==""){
			$response["error"] = true;
			$response["msg"] = "กรุณกรอกลำดับที่อยู่เป็นตัวเลขครับ";
			echo json_encode ($response); exit;
		}
		if($arrs['ADDR1'] ==""){
			$response["error"] = true;
			$response["msg"] = "กรุณากรอกบ้านเลขที่ก่อนครับ";
			echo json_encode($response); exit;
		}
		/*if($arrs['SWIN'] ==""){
			$response["error"] = true;
			$response["msg"] = "กรุณกรอกเลขที่หมู่บ้านเป็นตัวเลขก่อนครับ";
			echo json_encode ($response); exit;
		}
		*/
		/*if($arrs['SOI'] ==""){
			$response["error"] = true;
			$response["msg"] = "กรุณากรอกซอย : ไม่ระบุโปรดใส่เครื่องหมาย -";
			echo json_encode($response); exit;
		}
		if($arrs['ADDR2'] ==""){
			$response["error"] = true;
			$response["msg"] = "กรุณกรอกถนน : ไม่ระบุโปรดใส่เครื่องหมาย -";
			echo json_encode ($response); exit;
		}
		if($arrs['MOOBAN'] ==""){
			$response["error"] = true;
			$response["msg"] = "กรุณากรอกชื่อหมู่บ้านก่อนครับ";
			echo json_encode($response); exit;
		}
		*/
		if($arrs['TUMB'] ==""){
			$response["error"] = true;
			$response["msg"] = "กรุณกรอกตำบลก่อนครับครับ";
			echo json_encode ($response); exit;
		}
		if($arrs['AUMPDES'] ==""){
			$response["error"] = true;
			$response["msg"] = "กรุณากรอกอำเภอก่อนครับ";
			echo json_encode($response); exit;
		}
		
		if($arrs['PROVDES'] ==""){
			$response["error"] = true;
			$response["msg"] = "กรุณกรอกจังหวัดก่อนครับ";
			echo json_encode ($response); exit;
		}
		if($arrs['ZIP'] ==""){
			$response["error"] = true;
			$response["msg"] = "กรุณากรอกรหัสไปรษณีย์ก่อนครับ";
			echo json_encode($response); exit;
		}
		if($arrs['TELP'] ==""){
			$response["error"] = true;
			$response["msg"] = "กรุณกรอกเบอร์โทรศัพท์ก่อนครับ";
			echo json_encode ($response); exit;
		}
        $address = "";
        if($arrs["ADDR1"] != ""){
			$address .= "บ้านเลขที่ ".$arrs["ADDR1"];
        }
		if($arrs["SWIN"] !=""){
			$address .= "หมู่ที่".$arrs["SWIN"];
		}
        if($arrs["SOI"] != ""){
			$address .= " ซอย ".$arrs["SOI"];
        }
        if($arrs["ADDR2"] != ""){
			$address .= " ถนน ".$arrs["ADDR2"];
        }
        if($arrs["MOOBAN"] != ""){
			$address .= " หมู่บ้าน ".$arrs["MOOBAN"];
        }
        if($arrs["TUMB"] != ""){
			$address .= " ตำบล".$arrs["TUMB"];
        }
        if($arrs["AUMPDES"] != ""){
			$address .= " อำเภอ".$arrs["AUMPDES"];
        }
        if($arrs["PROVDES"] != ""){
			$address .= " จังหวัด".$arrs["PROVDES"];
        }
        if($arrs["ZIP"] != ""){
			$address .= " รหัสไปรษณีย์ ".$arrs["ZIP"];
        }
        $tbody = "
            <tr>
				<td>
                    <button class='btnEditAddrTable btn btn-sm btn-warning fa fa-edit' 
                        ADDRNO='".$arrs["ADDRNO"]."'
                        ADDR1='".$arrs["ADDR1"]."'
                        SOI='".$arrs["SOI"]."'
                        ADDR2='".$arrs["ADDR2"]."'
                        MOOBAN='".$arrs["MOOBAN"]."'
                        TUMB='".$arrs["TUMB"]."'
                        AUMPCOD='".$arrs["AUMPCOD"]."'
                        PROVCOD='".$arrs["PROVCOD"]."'
                        AUMPDES='".$arrs["AUMPDES"]."'
                        PROVDES='".$arrs["PROVDES"]."'
                        ZIP='".$arrs["ZIP"]."'
                        TELP='".$arrs["TELP"]."'
                        MEMO1='".$arrs["MEMO1"]."'
						SWIN='".$arrs["SWIN"]."'
					>แก้ไข</button>
                </td>
                <td>".$arrs["ADDRNO"]."</td>
                <td>".$address."</td>
                <td>".$arrs["TELP"]."</td>
                <td>".$arrs["MEMO1"]."</td>
                <td>
                    <button ADDRNO='".$arrs["ADDRNO"]."' class='btnDelAddrTable btn btn-sm btn-danger fa fa-trash'>ลบ</button>
                </td>
            </tr>
            ";	
        $response = array("tbody"=>$tbody);
        echo json_encode($response);
	}
	function SetAddr_TableHtml_Cancel(){
		$response = array('error'=>false,'msg'=>'');
		$arrs = array(); 
		$arrs["ADDRNO"]  = $_POST["ADDRNO"];
        $arrs["ADDR1"]   = $_POST["ADDR1"];
		$arrs["SWIN"]	 = $_POST["SWIN"];
        $arrs["SOI"] 	 = $_POST["SOI"];
        $arrs["ADDR2"] 	 = $_POST["ADDR2"];
        $arrs["MOOBAN"]  = $_POST["MOOBAN"];
        $arrs["TUMB"] 	 = $_POST["TUMB"];
        $arrs["AUMPCOD"] = $_POST["AUMPCOD"];
        $arrs["PROVCOD"] = $_POST["PROVCOD"];
        $arrs["AUMPDES"] = $_POST["AUMPDES"];
        $arrs["PROVDES"] = $_POST["PROVDES"];
        $arrs["ZIP"] 	 = $_POST["ZIP"];
        $arrs["TELP"] 	 = $_POST["TELP"];
        $arrs["MEMO1"] 	 = $_POST["MEMO1"];
		
        $address = "";
        if($arrs["ADDR1"] != ""){
			$address .= "บ้านเลขที่ ".$arrs["ADDR1"];
        }
		if($arrs["SWIN"] !=""){
			$address .= "หมู่ที่".$arrs["SWIN"];
		}
        if($arrs["SOI"] != ""){
			$address .= " ซอย ".$arrs["SOI"];
        }
        if($arrs["ADDR2"] != ""){
			$address .= " ถนน ".$arrs["ADDR2"];
        }
        if($arrs["MOOBAN"] != ""){
			$address .= " หมู่บ้าน ".$arrs["MOOBAN"];
        }
        if($arrs["TUMB"] != ""){
			$address .= " ตำบล".$arrs["TUMB"];
        }
        if($arrs["AUMPDES"] != ""){
			$address .= " อำเภอ".$arrs["AUMPDES"];
        }
        if($arrs["PROVDES"] != ""){
			$address .= " จังหวัด".$arrs["PROVDES"];
        }
        if($arrs["ZIP"] != ""){
			$address .= " รหัสไปรษณีย์ ".$arrs["ZIP"];
        }
        $tbody = "
            <tr>
				<td>
                    <button class='btnEditAddrTable btn btn-sm btn-warning fa fa-edit' 
                        ADDRNO='".$arrs["ADDRNO"]."'
                        ADDR1='".$arrs["ADDR1"]."'
                        SOI='".$arrs["SOI"]."'
                        ADDR2='".$arrs["ADDR2"]."'
                        MOOBAN='".$arrs["MOOBAN"]."'
                        TUMB='".$arrs["TUMB"]."'
                        AUMPCOD='".$arrs["AUMPCOD"]."'
                        PROVCOD='".$arrs["PROVCOD"]."'
                        AUMPDES='".$arrs["AUMPDES"]."'
                        PROVDES='".$arrs["PROVDES"]."'
                        ZIP='".$arrs["ZIP"]."'
                        TELP='".$arrs["TELP"]."'
                        MEMO1='".$arrs["MEMO1"]."'
						SWIN='".$arrs["SWIN"]."'
					>แก้ไข</button>
                </td>
                <td>".$arrs["ADDRNO"]."</td>
                <td>".$address."</td>
                <td>".$arrs["TELP"]."</td>
                <td>".$arrs["MEMO1"]."</td>
                <td>
                    <button ADDRNO='".$arrs["ADDRNO"]."' class='btnDelAddrTable btn btn-sm btn-danger fa fa-trash'>ลบ</button>
                </td>
            </tr>
            ";	
        $response = array("tbody"=>$tbody);
        echo json_encode($response);
	}
	function save(){
		$response = array('error'=>false,'msg'=>'');
		$arrs = array();
		$arrs['locat']	 	 = $_REQUEST["locat"];
		$arrs['CUSCOD']		 = $_POST["CUSCOD"];
		$arrs['GROUP1']      = $_POST["GROUP1"];
        $arrs['GRADE']       = $_POST["GRADE"];
        $arrs['SNAM']        = $_POST["SNAM"];
        $arrs['NAME1']       = $_POST["NAME1"];
        $arrs['NAME2']       = $_POST["NAME2"];
        $arrs['NICKNM']      = $_POST["NICKNM"];
        $arrs['BIRTHDT']     = $this->Convertdate(1,$_POST["BIRTHDT"]);
		$arrs['ADDRNO']		 = $_POST["ADDRNO"];
        $arrs['IDCARD']      = $_POST["IDCARD"];
        $arrs['IDNO']        = $_POST["IDNO"];
        $arrs['ISSUBY']      = $_POST["ISSUBY"];
        $arrs['ISSUDT']      = $this->Convertdate(1,$_POST["ISSUDT"]);
        $arrs['EXPDT']       = $this->Convertdate(1,$_POST["EXPDT"]);
        $arrs['AGE']         = $_POST["AGE"];
        $arrs['NATION']      = $_POST["NATION"];
        $arrs['OCCUP']       = $_POST["OCCUP"];
        $arrs['OFFIC']       = $_POST["OFFIC"];
        $arrs['MAXCRED']     = $_POST["MAXCRED"];
        $arrs['MREVENU']     = $_POST["MREVENU"];
        $arrs['YREVENU']     = $_POST["YREVENU"];
        $arrs['MOBILENO']    = $_POST["MOBILENO"];
        $arrs['EMAIL1']      = $_POST["EMAIL1"];
        $arrs['ADDRNO2']     = $_POST["ADDRNO2"];
        $arrs['ADDRNO3']     = $_POST["ADDRNO3"];
        $arrs['MEMOADD']     = $_POST["MEMOADD"];
		
		if($arrs["MAXCRED"] == ""){ $arrs["MAXCRED"] = "0.00"; }else{ $arrs["MAXCRED"] = str_replace(",","",$arrs["MAXCRED"]); }
		if($arrs["MREVENU"] == ""){ $arrs["MREVENU"] = "0.00"; }else{ $arrs["MREVENU"] = str_replace(",","",$arrs["MREVENU"]); } 
		if($arrs["YREVENU"] == ""){ $arrs["YREVENU"] = "0.00"; }else{ $arrs["YREVENU"] = str_replace(",","",$arrs["YREVENU"]); }
		
		$arrs['action']		 = $_POST["action"];	
		
		if(isset($_POST['ADDR'])){}else{
			$tablehtml = "K";
			$response = array("tablehtml"=>$tablehtml);
			echo json_encode ($response); exit;
		}
		$ADDR     			 = $_POST["ADDR"];
		//print_r ($ADDR); exit;
		if($arrs["GROUP1"] ==""){
			$response["error"] = true;
			$response["msg"] = "กรุณากรอกข้อมูลให้ครบถ้วนด้วยครับ";
			echo json_encode($response); exit;
		}
		if($arrs["GRADE"] ==""){
			$response["error"] = true;
			$response["msg"] = "กรุณากรอกข้อมูลให้ครบถ้วนด้วยครับ";
			echo json_encode($response); exit;
		}
		if($arrs["SNAM"] ==""){
			$response["error"] = true;
			$response["msg"] = "กรุณากรอกข้อมูลให้ครบถ้วนด้วยครับ";
			echo json_encode($response); exit;
		}
		if($arrs["NAME1"] == ""){
			$response["error"] = true;
			$response["msg"] = "กรุณากรอกข้อมูลให้ครบถ้วนด้วยครับ";
			echo json_encode($response); exit;
		}
		if($arrs["NAME2"] == ""){
			$response["error"] = true;
			$response["msg"] = "กรุณากรอกข้อมูลให้ครบถ้วนด้วยครับ";
			echo json_encode($response); exit;
		}
		if($arrs["NICKNM"] ==""){
			$response["error"] = true;
			$response["msg"] = "กรุณากรอกข้อมูลให้ครบถ้วนด้วยครับ";
			echo json_encode($response); exit;
		}
		if($arrs["BIRTHDT"] ==""){
			$response["error"] = true;
			$response["msg"] = "กรุณากรอกข้อมูลให้ครบถ้วนด้วยครับ";
			echo json_encode($response); exit;
		}
		if($arrs["ADDRNO"] ==""){
			$response["error"] = true;
			$response["msg"] = "กรุณากรอกข้อมูลให้ครบถ้วนด้วยครับ";
			echo json_encode($response); exit;
		}
		if($arrs["IDCARD"] ==""){
			$response["error"] = true;
			$response["msg"] = "กรุณากรอกข้อมูลให้ครบถ้วนด้วยครับ";
			echo json_encode($response); exit;
		}
		if($arrs["IDNO"] == ""){
			$response["error"] = true;
			$response["msg"] = "กรุณากรอกข้อมูลให้ครบถ้วนด้วยครับ";
			echo json_encode($response); exit;
		}
		if($arrs["ISSUBY"] == ""){
			$response["error"] = true;
			$response["msg"] = "กรุณากรอกข้อมูลให้ครบถ้วนด้วยครับ";
			echo json_encode($response); exit;
		}
		if($arrs["ISSUDT"] ==""){
			$response["error"] = true;
			$response["msg"] = "กรุณากรอกข้อมูลให้ครบถ้วนด้วยครับ";
			echo json_encode($response); exit;
		}
		if($arrs["EXPDT"] ==""){
			$response["error"] = true;
			$response["msg"] = "กรุณากรอกข้อมูลให้ครบถ้วนด้วยครับ";
			echo json_encode($response); exit;
		}
		if($arrs["AGE"] ==""){
			$response["error"] = true;
			$response["msg"] = "กรุณากรอกข้อมูลให้ครบถ้วนด้วยครับ";
			echo json_encode($response); exit;
		}
		if($arrs["NATION"] ==""){
			$response["error"] = true;
			$response["msg"] = "กรุณากรอกข้อมูลให้ครบถ้วนด้วยครับ";
			echo json_encode($response); exit;
		}
		if($arrs["OCCUP"] == ""){
			$response["error"] = true;
			$response["msg"] = "กรุณากรอกข้อมูลให้ครบถ้วนด้วยครับ";
			echo json_encode($response); exit;
		}
		if($arrs["OFFIC"] == ""){
			$response["error"] = true;
			$response["msg"] = "กรุณากรอกข้อมูลให้ครบถ้วนด้วยครับ";
			echo json_encode($response); exit;
		}
		if($arrs["MAXCRED"] ==""){
			$response["error"] = true;
			$response["msg"] = "กรุณากรอกข้อมูลให้ครบถ้วนด้วยครับ";
			echo json_encode($response); exit;
		}
		if($arrs["MREVENU"] ==""){
			$response["error"] = true;
			$response["msg"] = "กรุณากรอกข้อมูลให้ครบถ้วนด้วยครับ";
			echo json_encode($response); exit;
		}
		if($arrs["YREVENU"] ==""){
			$response["error"] = true;
			$response["msg"] = "กรุณากรอกข้อมูลให้ครบถ้วนด้วยครับ";
			echo json_encode($response); exit;
		}
		if($arrs["MOBILENO"] ==""){
			$response["error"] = true;
			$response["msg"] = "กรุณากรอกข้อมูลให้ครบถ้วนด้วยครับ";
			echo json_encode($response); exit;
		}
		/*
		if($arrs["EMAIL1"] == ""){
			$response["error"] = true;
			$response["msg"] = "กรุณากรอกข้อมูลให้ครบถ้วนด้วยครับ";
			echo json_encode($response); exit;
		}
		*/
		if($arrs["ADDRNO2"] == ""){
			$response["error"] = true;
			$response["msg"] = "กรุณากรอกข้อมูลให้ครบถ้วนด้วยครับ";
			echo json_encode($response); exit;
		}
		if($arrs["ADDRNO3"] ==""){
			$response["error"] = true;
			$response["msg"] = "กรุณากรอกข้อมูลให้ครบถ้วนด้วยครับ";
			echo json_encode($response); exit;
		}
		/*if($arrs["MEMOADD"] ==""){
			$response["error"] = true;
			$response["msg"] = "กรุณากรอกข้อมูลให้ครบถ้วนด้วยครับ";
		}*/
		
		if($arrs["MAXCRED"] == ""){$arrs["MAXCRED"] = "0.00"; }else{ $arrs["MAXCRED"] = str_replace(",","",$arrs["MAXCRED"]);}
		
		if($arrs['action'] == "add"){
			$this->saveCustomerHistory($arrs,$ADDR);
		}
		else{
			$this->updateCustomerHistory($arrs,$ADDR);
		}
	}
	function saveCustomerHistory($arrs,$ADDR){
		$SWIN = "";
		$SWIN = "ม.";
		$sql_addr = "";		//บันทึกที่อยู่ของลูกค้าเข้าฐานข้อมูล
        $sizeArr = count($ADDR);
        for($P=0; $P < $sizeArr; $P++){
		$sql_addr .="
			insert into {$this->MAuth->getdb('CUSTADDR')}(
				[CUSCOD],[ADDRNO],[ADDR1],[ADDR2],[TUMB],[AUMPCOD],[PROVCOD]
				,[ZIP],[TELP],[MEMO1],[ACPDT],[USERID],[PICT1],[MOOBAN],[SOI]
			)values(
				@CONTNO,'".$ADDR[$P][0]."','".$ADDR[$P][1]." ".$SWIN."".$ADDR[$P][11]."','".$ADDR[$P][3]."'
				,'".$ADDR[$P][5]."','".$ADDR[$P][6]."','".$ADDR[$P][7]."','".$ADDR[$P][8]."'
				,'".$ADDR[$P][9]."','".$ADDR[$P][10]."',null,null,null,'".$ADDR[$P][4]."'
				,'".$ADDR[$P][2]."'
				)
			";
        }
		$sql ="
			if OBJECT_ID('tempdb..#custmastTemp') is not null drop table #custmastTemp;
			create table #custmastTemp (id varchar(1),msg varchar(max));

			begin tran custmastTran
			begin try

				/* @symbol = สัญลักษณ์แทนประเภทของเลขที่ นั้นๆ */
				declare @symbol varchar(10) = (select H_MASTNO from {$this->MAuth->getdb('CONDPAY')}); 
				/* @rec = รหัสพื้นฐาน */
				declare @rec varchar(10) = (select SHORTL+@symbol+'-'+right(left(convert(varchar(8),GETDATE(),112),6),4) from {$this->MAuth->getdb('INVLOCAT')} where LOCATCD='".$arrs['locat']."');
				/* @RESVNO = รหัสที่จะใช้ */
				declare @CONTNO varchar(12) = isnull((select MAX(CUSCOD) from {$this->MAuth->getdb('CUSTMAST')} where CUSCOD like ''+@rec+'%' collate thai_cs_as),@rec+'0000');
				set @CONTNO = left(@CONTNO,8)+right(right(@CONTNO,4)+10001,4);
				
				set @symbol = (select H_TXMAST from {$this->MAuth->getdb('CONDPAY')});
				set @rec = (select SHORTL+@symbol+'-'+right(left(convert(varchar(8),GETDATE(),112),6),4) from {$this->MAuth->getdb('INVLOCAT')} where LOCATCD='".$arrs['locat']."');
				
				declare @TAXNO varchar(12) = isnull((select MAX(TAXNO) from {$this->MAuth->getdb('TAXTRAN')} where TAXNO like ''+@rec+'%' collate thai_cs_as),@rec+'0000');
				declare @TAXDT datetime = (select convert(varchar(8),getdate(),112));
				set @TAXNO = left(@TAXNO ,8)+right(right(@TAXNO ,4)+10001,4);
				
				declare @isval int = isnull((select count(*) from {$this->MAuth->getdb('CUSTMAST')} where IDNO='".$arrs['IDNO']."'),0);
				
				declare @sircod varchar(2) = (select SIRCOD from {$this->MAuth->getdb('SIRNAM')} where SIRNAM = '".$arrs['SNAM']."');
				
				BEGIN
					set @TAXNO = null;
					set @TAXDT = null;
				END
				
				if(@isval = 0)
				begin
					insert into {$this->MAuth->getdb('CUSTMAST')} (
						[CUSCOD],[GROUP1],[SNAM],[NAME1],[NAME2],[NICKNM],[BIRTHDT],[ADDRNO],[IDCARD],[IDNO],[ISSUBY]
						,[ISSUDT],[EXPDT],[AGE],[NATION],[OCCUP],[OFFIC],[BOSSNM],[GRADE],[ACPDT],[MEMO1],[USERID],[PICT1]
						,[MINCOME],[YINCOME],[MAXCRED],[MREVENU],[YREVENU],[MEMBCOD],[MOBILENO],[APPVCODE],[SIRCOD]
						,[CUSTTYPE],[ADDRNO2],[ADDRNO3],[EMAIL1],[EMAIL2]
					)values(
						@CONTNO,'".$arrs["GROUP1"]."','".$arrs['SNAM']."','".$arrs["NAME1"]."'
						,'".$arrs["NAME2"]."','".$arrs["NICKNM"]."','".$arrs["BIRTHDT"]."','".$arrs['ADDRNO']."','".$arrs["IDCARD"]."'
						,'".$arrs["IDNO"]."','".$arrs["ISSUBY"]."','".$arrs["ISSUDT"]."','".$arrs["EXPDT"]."','".$arrs["AGE"]."'
						,'".$arrs['NATION']."','".$arrs['OCCUP']."','".$arrs['OFFIC']."',null,'".$arrs['GRADE']."',null
						,'".$arrs['MEMOADD']."',null,null,null,null,'".$arrs['MAXCRED']."','".$arrs['MREVENU']."'
						,'".$arrs['YREVENU']."',null,'".$arrs['MOBILENO']."',null,@sircod,null,'".$arrs['ADDRNO2']."'
						,'".$arrs['ADDRNO3']."','".$arrs['EMAIL1']."',null
						);
					".$sql_addr."	
					
					insert into {$this->MAuth->getdb('hp_UserOperationLog')} (userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
					values ('".$this->sess["IDNo"]."','SYS04::บันทึกประวัติลูกค้า',@CONTNO+' ".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
				end
				
				else
				begin
					rollback tran custmastTran;
					insert into #custmastTemp select 'N' as id,'ไม่บันทึก : มีรหัสบัตรประจำตัวเลขที่ ".$arrs['IDNO']." อยู่แล้ว ' as msg;
					return;
				end	
				
				insert into #custmastTemp select 'Y' as id,'สำเร็จ บันทึกข้อมูลประวัติลูกค้าใหม่รหัสลูกค้าเลขที่ :: '+@CONTNO+' เรียบร้อยแล้ว' as msg;
				commit tran custmastTran;
			end try
			begin catch
				rollback tran custmastTran;
				insert into #custmastTemp select 'N' as id,'บันทึกข้อมูลไม่สำเร็จ : กรุณาติดต่อฝ่ายไอที' as msg;
			end catch
		";
		//echo $sql; exit;
		
		$this->db->query($sql);
		$sql = "select * from #custmastTemp";
		$query = $this->db->query($sql);
		
		$response = array();
		if($query->row()){
			foreach($query->result() as $row){
				$response['stat'] = ($row->id == 'Y' ? true:false);
				$response['msg'] = $row->msg;
			}
		}else{
			$response['stat'] = false;
			$response['msg'] = 'ผิดพลาด';
		}
		echo json_encode($response);
	}
	function updateCustomerHistory($arrs,$ADDR){
		//echo $ADDR[0][1]; exit;
		$SWIN = "";
		$SWIN = "ม.";
		$sql_addr = "";		//บันทึกที่อยู่ของลูกค้าเข้าฐานข้อมูล
        $sizeArr = count($ADDR);
        for($P=0; $P < $sizeArr; $P++){
		$sql_addr .="
			if exists (
				select * from {$this->MAuth->getdb('CUSTADDR')} 
				where CUSCOD='".$arrs['CUSCOD']."' and ADDRNO='".$ADDR[$P][0]."'
			)
			begin
			update {$this->MAuth->getdb('CUSTADDR')}
			set
				[CUSCOD]='".$arrs['CUSCOD']."',[ADDRNO]='".$ADDR[$P][0]."'
				,[ADDR1]='".$ADDR[$P][1]." ".$SWIN."".$ADDR[$P][11]."',[ADDR2]='".$ADDR[$P][3]."'
				,[TUMB]='".$ADDR[$P][5]."',[AUMPCOD]='".$ADDR[$P][6]."'
				,[PROVCOD]='".$ADDR[$P][7]."',[ZIP]='".$ADDR[$P][8]."'
				,[TELP]='".$ADDR[$P][9]."',[MEMO1]='".$ADDR[$P][10]."'
				,[MOOBAN]='".$ADDR[$P][4]."',[SOI]='".$ADDR[$P][2]."'
				WHERE [CUSCOD]='".$arrs['CUSCOD']."' and [ADDRNO]='".$ADDR[$P][0]."'
			end
			else	
			begin
			insert into {$this->MAuth->getdb('CUSTADDR')}(
				[CUSCOD],[ADDRNO],[ADDR1],[ADDR2],[TUMB],[AUMPCOD],[PROVCOD]
				,[ZIP],[TELP],[MEMO1],[ACPDT],[USERID],[PICT1],[MOOBAN],[SOI]
			)values(
				'".$arrs['CUSCOD']."','".$ADDR[$P][0]."','".$ADDR[$P][1]." ".$SWIN."".$ADDR[$P][11]."','".$ADDR[$P][3]."'
				,'".$ADDR[$P][5]."','".$ADDR[$P][6]."','".$ADDR[$P][7]."','".$ADDR[$P][8]."'
				,'".$ADDR[$P][9]."','".$ADDR[$P][10]."',null,null,null,'".$ADDR[$P][4]."'
				,'".$ADDR[$P][2]."'
				)
			end	
			";
        }
		$sql ="
			if OBJECT_ID('tempdb..#custmastTemp') is not null drop table #custmastTemp;
			create table #custmastTemp (id varchar(1),msg varchar(max));
			
			declare @sircod varchar(2) = (select SIRCOD from SIRNAM where SIRNAM = '".$arrs['SNAM']."');
			
			begin tran custmastTran
			begin try
			if exists(
				select * from {$this->MAuth->getdb('CUSTMAST')}
				where CUSCOD = '".$arrs['CUSCOD']."'
			)
			begin
			update {$this->MAuth->getdb('CUSTMAST')}
			set [CUSCOD]='".$arrs['CUSCOD']."',[GROUP1]='".$arrs['GROUP1']."',[SNAM]='".$arrs['SNAM']."',[NAME1]='".$arrs['NAME1']."'
				,[NAME2]='".$arrs['NAME2']."',[NICKNM]='".$arrs['NICKNM']."',[BIRTHDT]='".$arrs['BIRTHDT']."',[ADDRNO]='".$arrs['ADDRNO']."',[IDCARD]='".$arrs['IDCARD']."'
				,[IDNO]='".$arrs['IDNO']."',[ISSUBY]='".$arrs['ISSUBY']."',[ISSUDT]='".$arrs['ISSUDT']."',[EXPDT]='".$arrs['EXPDT']."',[AGE]='".$arrs['AGE']."'
				,[NATION]='".$arrs['NATION']."',[OCCUP]='".$arrs['OCCUP']."',[OFFIC]='".$arrs['OFFIC']."',[GRADE]='".$arrs['GRADE']."'
				,[MEMO1]='".$arrs['MEMOADD']."',[MAXCRED]='".$arrs['MAXCRED']."',[MREVENU]='".$arrs['MREVENU']."'
				,[YREVENU]='".$arrs['YREVENU']."',[MOBILENO]='".$arrs['MOBILENO']."',[SIRCOD]= @sircod,[ADDRNO2]='".$arrs['ADDRNO2']."'
				,[ADDRNO3]='".$arrs['ADDRNO3']."',[EMAIL1]='".$arrs['EMAIL1']."' WHERE [CUSCOD]='".$arrs['CUSCOD']."'
				
			".$sql_addr."
			
				insert into {$this->MAuth->getdb('hp_UserOperationLog')} (userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
				values ('".$this->sess["IDNo"]."','SYS04::แก้ไขประวัติลูกค้า','".$arrs['CUSCOD']."'+' ".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
			end
			else
			begin
				rollback tran custmastTran;
				insert into #custmastTemp select 'N' as id,'บันทึกข้อมูลไม่สำเร็จ : ไม่พบข้อมูลรหัสประวัติลูกค้า โปรดตรวจสอบข้อมูลใหม่อีกครั้ง' as msg;
				return;
			end
				insert into #custmastTemp select 'Y' as id,'แก้ไขประวัติลูกค้าเลขที่ :: '+'".$arrs['CUSCOD']."'+' เรียบร้อยแล้ว' as msg;
				commit tran custmastTran;
				
			end try
			begin catch
				rollback tran custmastTran;
				insert into #custmastTemp select 'N' as id,'บันทึกข้อมูลไม่สำเร็จ : กรุณาติดต่อฝ่ายไอที' as msg;
			end catch
		";
		//echo $sql; exit;
		$this->db->query($sql);
		$sql = "select * from #custmastTemp";
		$query = $this->db->query($sql);
		
		$response = array();
		if($query->row()){
			foreach($query->result() as $row){
				$response['stat'] = ($row->id == 'Y' ? true:false);
				$response['msg'] = $row->msg;
			}
		}else{
			$response['stat'] = false;
			$response['msg'] = 'ผิดพลาด';
		}
		echo json_encode($response);
	}
	function DeletedCUSCOD(){
		$CUSCOD = $_POST["CUSCOD"];
		$sql ="
			if object_id('tempdb..#temp') is not null drop table #temp;
			select ROW_NUMBER() over(order by _table) r,* into #temp from (
				select a.name as _table,b.name as _column from ".$this->sess["db"].".sys.tables a
				left join ".$this->sess["db"].".sys.columns b on a.object_id=b.object_id
			) a
			where a._column='CUSCOD' and a._table not in ('CUSTMAST','CUSTADDR')

			declare @CUSCODDEL varchar(12) = '".$CUSCOD."';
			declare @started int = 1;
			declare @sizeof int = (select COUNT(*) from #temp);
			create table #has (total int);
			
			while @started <= @sizeof
			begin 
				declare @table varchar(max) = (select _table from #temp where r=@started);
				
				insert into #has
				exec(N'
					select count(*) from '+@table+'
					where CUSCOD='''+@CUSCODDEL+'''	
				');
				
				set @started = @started+1;
			end
			
			create table #custmastTemp (id varchar(1),msg varchar(max));

			begin tran custmastTran
			begin try
			
				if((select SUM(total) from #has)>0)
				begin
					rollback tran custmastTran;
					insert into #custmastTemp select 'N' as id,'ไม่สามารถลบประวัติลูกค้ารหัส : ".$CUSCOD." เพราะได้นำไปใช้งานแล้ว' as msg;
					return;
				end
				else
				begin
					delete from {$this->MAuth->getdb('CUSTMAST')} where CUSCOD = '".$CUSCOD."'
					
					delete from {$this->MAuth->getdb('CUSTADDR')} where CUSCOD = '".$CUSCOD."'
					
					insert into {$this->MAuth->getdb('hp_UserOperationLog')} (userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
					values ('".$this->sess["IDNo"]."','SYS04::ลบประวัติลูกค้า','".$CUSCOD."'+' ".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
				end
				
				insert into #custmastTemp select 'Y' as id,'ลบประวัติลูกค้าเลขที่ :: '+'".$CUSCOD."'+' เรียบร้อยแล้ว' as msg;
				commit tran custmastTran;
				
			end try
			begin catch
				rollback tran custmastTran;
				insert into #custmastTemp select 'N' as id,'บันทึกข้อมูลไม่สำเร็จ : กรุณาติดต่อฝ่ายไอที' as msg;
			end catch
		";
		//echo $sql; exit;
		$this->db->query($sql);
		$sql = "select * from #custmastTemp";
		$query = $this->db->query($sql);
		
		$response = array();
		if($query->row()){
			foreach($query->result() as $row){
				$response['stat'] = ($row->id == 'Y' ? true:false);
				$response['msg'] = $row->msg;
			}
		}else{
			$response['stat'] = false;
			$response['msg'] = 'ผิดพลาด';
		}
		echo json_encode($response);
	}
	
	function dateselectshow($date){
        if ($date!=""){
            return substr($date,8,2)."/".substr($date,5,2)."/".(substr($date,0,4)+543);
        }
        return $date;
    }
	/*
    function dateformatsql($date){
        if($date!=""){
            return substr($date, 6,4).substr($date, 3,2).(substr($date, 0,2)-543);
        }
        return $date;
    }
	
    function dateselectedit($date){
        if ($date!=""){
            return substr($date,8,2.)."/".substr($date,5,2)."/".substr($date,0,4);
        }
        return $date;
    }
	*/
	function SetTitle(){
		$claim = $this->MLogin->getclaim(uri_string());
		if($claim['m_access'] != "T"){ echo "<div align='center' style='color:red;font-size:16pt;width:100%;'>ขออภัย คุณยังไม่มีสิทธิเข้าใช้งานหน้านี้ครับ</div>"; exit; }
		
		$html = "
			<div class='tab1' name='home' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}'>
				<div class=' col-sm-2'>	
					<div class='form-group'>
						รหัสคำนำหน้าชื่อ
						<input type='text' id='sircod' class='form-control input-sm' placeholder='รหัสคำนำหน้าชื่อ'>
					</div>
				</div>
				<div class=' col-sm-8'>	
					<div class='form-group'>
						คำนำหน้าชื่อ
						<input type='text' id='sirnam' class='form-control input-sm' placeholder='คำนำหน้าชื่อ'  data-provide='datepicker' data-date-language='th-th'>
					</div>
				</div>	
				<div class=' col-sm-1'>	
					<div class='form-group'>
						<br>
						<input type='button' id='search_groupsn' class='btn btn-primary btn-sm' value='แสดง' style='width:100%'>
					</div>
				</div>	
				<div class=' col-sm-1'>	
					<div class='form-group'>
						<br>
						<input type='button' id='add_groupsn' class='btn btn-cyan btn-sm' value='เพิ่ม' style='width:100%'>
					</div>
				</div>
			</div>
			<div id='setgroupResult' class='col-sm-12 tab1' style='height:calc(100vh - 197px);overflow:auto;background-color:#;'></div>
	
			<div id='tab2_main' class='col-sm-12 tab2' hidden style='height:calc(100vh - 130px);overflow:auto;background-color:#;'></div>
		";
		$html.= "<script src='".base_url('public/js/SYS04/CUSTOMERS.js')."'></script>";
		echo $html;
	}
	function groupSearchsn(){
		$arrs = array();
		$arrs['sircod'] = !isset($_REQUEST['sircod']) ? '' : $_REQUEST['sircod'];
		$arrs['sirnam'] = !isset($_REQUEST['sirnam']) ? '' : $_REQUEST['sirnam'];
		
		$cond = "";
		if($arrs['sircod'] != ''){
			$cond .= " and SIRCOD like '%".$arrs['sircod']."%'";
		}
		
		if($arrs['sirnam'] != ''){
			$cond .= " and SIRNAM like '%".$arrs['sirnam']."%'";
		}
		
		$sql = "
			select * from {$this->MAuth->getdb('SIRNAM')}
			where 1=1 ".$cond." order by SIRCOD
		";
		//echo $sql ; exit;
		$query = $this->db->query($sql);
				
		$NRow = 1;
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$html .= "
					<tr class='trow' seq='".$NRow."'>
						<td class='getit' seq='".$NRow++."' SIRCOD='".str_replace(chr(0),'',$row->SIRCOD)."' style='width:50px;cursor:pointer;text-align:center;'><b>เลือก</b></td>
						<td>".str_replace(chr(0),'',$row->SIRCOD)."</td>
						<td>".str_replace(chr(0),'',$row->SIRNAM)."</td>
					</tr>
				";
			}
		}
		
		$html = "
			<div id='tbScroll' class='col-sm-12' style='height:100%;overflow:auto;background-color:#eee;'>
				<table id='data-table-example2' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='100%'>
					<thead>
						<!-- tr>
							<th colspan='4' align='center'>
								<span style='cursor:pointer;'>Excel</span>
								<span style='cursor:pointer;'>PDF</span>
							</th>
						</tr -->
						<tr>
							<th>#</th>
							<th>รหัสคำนำหน้าชื่อ</th>
							<th>คำนำหน้าชื่อ</th>							
						</tr>
					</thead>	
					<tbody>
						".$html."				
					</tbody>
				</table>
			</div>
		";
		
		$response = array();
		$response['html'] = $html;
		echo json_encode($response);
	}
	function groupGetFormSN(){
		$arrs = array();
		$arrs['SIRCOD'] = (!isset($_REQUEST['SIRCOD']) ? '' : $_REQUEST['SIRCOD']);
		
		$data = array(
			'SIRCOD'=>'',
			'SIRNAM'=>'',
		);
		if($arrs['SIRCOD'] != ''){
			$sql = "
				select * from {$this->MAuth->getdb('SIRNAM')}
				where SIRCOD='".$arrs['SIRCOD']."'
			";
			$query = $this->db->query($sql);
			
			if($query->row()){
				foreach($query->result() as $row){
					$data['SIRCOD'] = str_replace(chr(0),'',$row->SIRCOD); 
					$data['SIRNAM'] = str_replace(chr(0),'',$row->SIRNAM);
				}
			}
		}
		$response = array();
		$response['html'] = "
			<div class='col-sm-12'>
				<div style='height:calc(100vh - 165px);overflow:auto;'>
					<div class='col-sm-4 col-sm-offset-4'>	
						<div class='form-group'>
							รหัสคำนำหน้าชื่อ
							<input type='text' id='t2sircod' class='form-control input-sm' placeholder='Auto Genarate' value='".$data['SIRCOD']."' readonly>
						</div>
					</div>
					
					<div class='col-sm-4 col-sm-offset-4'>	
						<div class='form-group'>
							คำนำหน้าชื่อ
							<input type='text' id='t2sirnam' class='form-control input-sm' value='".$data['SIRNAM']."'>
						</div>
					</div>
				</div>
				
				<div class='col-sm-1 col-sm-offset-4'>
					<input type='button' id='tab2back' class='btn btn-inverse btn-sm' style='width:100%;' value='ย้อนกลับ'>					
				</div>
				<div class='col-sm-1'>
					<input type='button' id='tab2del' class='btn btn-danger btn-sm' style='width:100%;' value='ลบ'>
				</div>
				<div class='col-sm-1'>
					<input type='button' id='tab2save' class='btn btn-primary btn-sm' style='width:100%;' value='บันทึก'>
				</div>
			</div>	
		";
		echo json_encode($response);
	}
	function groupSave(){
		$arrs = array();
		$arrs['sircod'] = (!isset($_REQUEST['sircod'])?'':$_REQUEST['sircod']);
		$arrs['sirnam'] = (!isset($_REQUEST['sirnam'])?'':$_REQUEST['sirnam']);
		$arrs['action'] = (!isset($_REQUEST['action'])?'':$_REQUEST['action']);
		//echo ($arrs); exit;
		
		if($arrs["sirnam"] == ""){
			$response["error"] = true;
			$response["msg"] = 'คุณยังไม่ได้ระบุคำนำหน้าเลย กรุณากรอกคำนำหน้าก่อนนะครับ';
			echo json_encode($response); exit;
		}
		
		$data = "";
		if($arrs['action'] == 'add'){
			$data = "
				declare @isval int = isnull((select count(*) from {$this->MAuth->getdb('SIRNAM')} where SIRNAM='".$arrs['sirnam']."'),0);
				declare @A varchar(20) = (select MAX(SIRCOD) from {$this->MAuth->getdb('SIRNAM')});
				declare @B varchar(20) = @A+1;
				
				if(@isval = 0)
				begin 
					insert into {$this->MAuth->getdb('SIRNAM')} (SIRCOD,SIRNAM)
					select @B,'".$arrs['sirnam']."'
					
					insert into {$this->MAuth->getdb('hp_UserOperationLog')}(userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
					values ('".$this->sess["IDNo"]."','กลุ่มคำนำหน้า เพิ่ม',@B+'".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
				end 
				
				else
				begin 
					rollback tran tsc;
					insert into #tempolary select 'N' as id,'ไม่บันทึก : มีข้อมูลรหัสกลุ่ม ".$arrs['sirnam']." อยู่แล้ว' as msg;
					return;
				end
			";
		}else{			
			$data = "
				if exists (
					select * from {$this->MAuth->getdb('SIRNAM')}
					where SIRCOD ='".$arrs['sircod']."'
				)
				begin
					update {$this->MAuth->getdb('SIRNAM')}
					set SIRNAM ='".$arrs['sirnam']."'
					where SIRCOD ='".$arrs['sircod']."'
					
					insert into {$this->MAuth->getdb('hp_UserOperationLog')}(userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
					values ('".$this->sess["IDNo"]."','กลุ่มคำนำหน้า แก้ไข','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
				end
				else
				begin
					rollback tran tsc;
					insert into #tempolary select 'N' as id,'บันทึกข้อมูลไม่สำเร็จ : ไม่พบข้อมูลคำนำหน้ารหัสกลุ่ม ".$arrs['sircod']." โปรดตรวจสอบข้อมูลใหม่อีกครั้ง' as msg;
					return;
				end	
			";
		}
		
		$sql = "
			if object_id('tempdb..#tempolary') is not null drop table #tempolary;
			create table #tempolary (id varchar(1),msg varchar(max));
			
			begin tran tsc
			begin try			
				".$data."
				insert into #tempolary select 'Y' as id,'สำเร็จ บันทึกข้อมูลเรียบร้อยแล้ว' as msg;
				commit tran tsc;
			end try
			begin catch
				rollback tran tsc;
				insert into #tempolary select 'N' as id,'บันทึกข้อมูลไม่สำเร็จ : กรุณาติดต่อฝ่ายไอที' as msg;
			end catch
		";
		
		$this->db->query($sql);
		$sql = "select * from #tempolary";
		$query = $this->db->query($sql);
		
		$response = array();
		if($query->row()){
			foreach($query->result() as $row){
				$response['stat'] = ($row->id == 'Y' ? true:false);
				$response['msg'] = $row->msg;
			}
		}else{
			$response['stat'] = false;
			$response['msg'] = 'ผิดพลาด';
		}
		echo json_encode($response);
	}
	function groupDel(){
		$arrs = array();
		$arrs['sircod'] = (!isset($_REQUEST['sircod'])?'':$_REQUEST['sircod']);
		$arrs['sirnam'] = (!isset($_REQUEST['sirnam'])?'':$_REQUEST['sirnam']);
		$arrs['action'] = (!isset($_REQUEST['action'])?'':$_REQUEST['action']);
		//echo ($arrs); exit;
		
		$data = "";
		if($arrs['action'] == 'del'){
			$data = "
				declare @isval int = isnull((select count(*) from {$this->MAuth->getdb('CUSTMAST')} where SIRCOD='".$arrs['sircod']."'),0);
				
				if(@isval = 0)
				begin 
					delete {$this->MAuth->getdb('SIRNAM')}
					where SIRCOD='".$arrs['sircod']."'
					
					insert into {$this->MAuth->getdb('hp_UserOperationLog')}(userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
					values ('".$this->sess["IDNo"]."','กลุ่มคำนำหน้า ลบ','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
				end 
				
				else
				begin 
					rollback tran tsc;
					insert into #tempolary select 'N' as id,'ไม่สามารถลบ: ข้อมูลรหัสกลุ่ม".$arrs['sircod']." เพราะได้นำไปใช้งานแล้ว' as msg;
					return;
				end
			";
		}
		$sql = "
			if object_id('tempdb..#tempolary') is not null drop table #tempolary;
			create table #tempolary (id varchar(1),msg varchar(max));
			
			begin tran tsc
			begin try			
				".$data."
				insert into #tempolary select 'Y' as id,'สำเร็จ บันทึกข้อมูลเรียบร้อยแล้ว' as msg;
				commit tran tsc;
			end try
			begin catch
				rollback tran tsc;
				insert into #tempolary select 'N' as id,'Fail : '+ERROR_MESSAGE() as msg;
			end catch
		";
		
		$this->db->query($sql);
		$sql = "select * from #tempolary";
		$query = $this->db->query($sql);
		
		$response = array();
		if($query->row()){
			foreach($query->result() as $row){
				$response['stat'] = ($row->id == 'Y' ? true:false);
				$response['msg'] = $row->msg;
			}
		}else{
			$response['stat'] = false;
			$response['msg'] = 'ผิดพลาด';
		}
		echo json_encode($response);
	}
}




















