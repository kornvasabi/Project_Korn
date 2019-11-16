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
			<div class='tab1' name='home' locat='{$this->sess['branch']}' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}' style='height:calc(100vh - 132px);overflow:auto;background-color:white;'>
				<div class='col-sm-12' style='overflow:auto;'>					
					<div class='row'>
						<div class=' col-sm-2'>	
							<div class='form-group'>
								รหัสลูกค้า
								<input type='text' id='cuscod' class='form-control input-sm' placeholder='รหัสลูกค้า' value='FFH-191100' >
							</div>
						</div>
						<div class=' col-sm-6'>	
							<div class='form-group'>
								ชื่อ-สกุล  ลูกค้า
								<input type='text' id='surname' class='form-control input-sm' placeholder='ชื่อ-สกุล ลูกค้า' >
							</div>
						</div>
						<div class=' col-sm-2'>	
							<div class='form-group'>
								<br>
								<input type='button' id='search_groupcm' class='btn btn-cyan btn-sm' value='แสดง' style='width:100%'>
							</div>
						</div>
						<div class=' col-sm-2'>	
							<div class='form-group'>
								<br>
								<input type='button' id='add_custmast' class='btn btn-primary btn-sm' value='เพิ่มประวัติลูกค้า' style='width:100%'>
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
		
		$cond = "";
		if($arrs['cuscod'] != ''){
			$cond .= " and CUSCOD like '%".$arrs['cuscod']."%'";
		}
		
		if($arrs['surname'] != ''){
			$cond .= " and NAME1 like '%".$arrs['surname']."%'";
		}
		if($arrs['surname'] != ''){
			$cond .= " or NAME2 like '%".$arrs['surname']."%'";
		}
		
		$sql = "
			select top 20 * from {$this->MAuth->getdb('CUSTMAST')}
			where 1=1 ".$cond." order by CUSCOD
		";
		//echo $sql ; exit;
		$query = $this->db->query($sql);
				
		//$NRow = 1;
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$html .= "
					<tr>
						<td><input type='button' value='รายละเอียด' class='btnDetail btn btn-xs btn-info glyphicon glyphicon-zoom-in' CUSCOD ='".str_replace(chr(0),'',$row->CUSCOD)."' style='cursor:pointer;'></td>
						<td>".str_replace(chr(0),'',$row->CUSCOD)."</td>
						<td>".str_replace(chr(0),'',$row->NAME1)." ".str_replace(chr(0),'',$row->NAME2)."</td>
						<td>".str_replace(chr(0),'',$row->BIRTHDT)."</td>
						<td>".str_replace(chr(0),'',$row->AGE)."</td>
						<td>".str_replace(chr(0),'',$row->OCCUP)."</td>
						<td><input type='button' class='btn btn-primary btn-sm btnshow_Addr' CUSCOD='".str_replace(chr(0),'',$row->CUSCOD)."'  value='แสดงที่อยู่'  style='width:100%'></td>
					</tr>
				";
			}
		}
		
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
		$arrs['CUSCOD'] = !isset($_REQUEST['CUSCOD']) ? '' : $_REQUEST['CUSCOD'];
		$sql ="
            select * from {$this->MAuth->getdb('CUSTADDR')} A
            inner join {$this->MAuth->getdb('SETAUMP')} B on A.AUMPCOD = B.AUMPCOD 
            inner join {$this->MAuth->getdb('SETPROV')} C on B.PROVCOD = C.PROVCOD
            where CUSCOD ='".$arrs['CUSCOD']."'
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
							บ้านเลขที่ ".str_replace(chr(0),'',$row->ADDR1)."
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
			<div id='tbScroll' class='col-sm-12' style='height:100%;overflow:auto;background-color:#eee;'>
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
		
		$arrs["ADDRNO"]	  = "";
        $arrs["ADDR1"] 	  = "";
        $arrs["SOI"] 	  = "";
        $arrs["ADDR2"] 	  = "";
        $arrs["MOOBAN"]   = "";
        $arrs["TUMB"] 	  = "";
        $arrs["AUMPCOD"]  = "";
        $arrs["PROVCOD"]  = "";
        $arrs["AUMPDES"]  = "";
        $arrs["PROVDES"]  = "";
        $arrs["ZIP"] 	  = "";
        $arrs["TELP"] 	  = "";
        $arrs["MEMO2"] 	  = "";
		
		$sql = "select * from CUSTMAST where CUSCOD = '".$CUSCOD."' ";
        $query = $this->db->query($sql);
		
		if($query->row()){
			foreach($query->result() as $row){
				$arrs['CUSCOD']     = $row->CUSCOD;
				//$arrs['GROUP1']     = $row->GROUP1;
				//$arrs['GRADE']      = $row->GRADE;
				//$arrs['SNAM']       = $row->SNAM;
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
				$arrs['MAXCRED']    = $row->MAXCRED;
				$arrs['MREVENU']    = $row->MREVENU;
				$arrs['YREVENU']    = $row->YREVENU;
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
		$sql = "
			select * from CUSTMAST A
            left join ARGROUP B on A.GROUP1=B.ARGCOD where A.CUSCOD = '".$CUSCOD."' 
			";
        $query = $this->db->query($sql);
        if($query->row()){
			foreach($query->result() as $row){
				$arrs['GROUP1'] = "<option value='".str_replace(chr(0),"",$row->ARGCOD)."'>".str_replace(chr(0),"",$row->ARGDES)."</option>";
			}
		}
		
		$sql ="
			select * from CUSTMAST A
            left join SETGRADCUS B on A.GRADE=B.GRDCOD where A.CUSCOD ='".$CUSCOD."'
		";
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$arrs['GRADE'] = "<option value='".str_replace(chr(0),"",$row->GRDCOD)."'>".str_replace(chr(0),"",$row->GRDCOD)." ".str_replace(chr(0),"",$row->GRDDES)."</option>";
			}
		}
		
		$sql ="
			select * from CUSTMAST A 
            left join SIRNAM B on A.SNAM=B.SIRCOD where A.CUSCOD ='".$CUSCOD."' 
		";
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$arrs['SNAM'] = "<option value='".str_replace(chr(0),"",$row->SIRCOD)."'>".str_replace(chr(0),"",$row->SIRNAM)."</option>";
			}
		}
		
		$sql = "
			select * from CUSTADDR A
			inner join SETAUMP B on A.AUMPCOD=B.AUMPCOD
			inner join SETPROV C on B.PROVCOD=C.PROVCOD
			where CUSCOD = '".$CUSCOD."' order by ADDRNO
		";
		
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$arrs["ADDRNO"]	  = $row->ADDRNO;
				$arrs["ADDR1"] 	  = $row->ADDR1;
				$arrs["SOI"] 	  = $row->SOI;
				$arrs["ADDR2"] 	  = $row->ADDR2;
				$arrs["MOOBAN"]   = $row->MOOBAN;
				$arrs["TUMB"] 	  = $row->TUMB;
				$arrs["AUMPCOD"]  = $row->AUMPCOD;
				$arrs["PROVCOD"]  = $row->PROVCOD;
				$arrs["AUMPDES"]  = $row->AUMPDES;
				$arrs["PROVDES"]  = $row->PROVDES;
				$arrs["ZIP"] 	  = $row->ZIP;
				$arrs["TELP"] 	  = $row->TELP;
				$arrs["MEMO2"]= $row->MEMO1;
				
				$arrs["addrno1"]  ="<option value='".$row->ADDRNO."'>".$row->ADDRNO."</option>";
				$arrs["addrno2"]  ="<option value='".$row->ADDRNO."'>".$row->ADDRNO."</option>";
				$arrs["addrno3"]  ="<option value='".$row->ADDRNO."'>".$row->ADDRNO."</option>";
			}
		}
		$tbody_db = "";
		$tbody_db = "
			<tr>
				<td>".$arrs["ADDRNO"]."</td>
				<td>บ้านเลขที่ ".$arrs["ADDR1"]." ซอย ".$arrs["SOI"]." ถนน ".$arrs["ADDR2"]." หมู่บ้าน ".$arrs["MOOBAN"]." ตำบล ".$arrs["TUMB"]." อำเภอ ".$arrs['AUMPDES']." จังหวัด ".$arrs['PROVDES']." รหัสไปรษณีย์ ".$arrs['ZIP']."</td>
				<td>".$arrs['TELP']."</td>
				<td>".$arrs['MEMO2']."</td>
				<td><button class='btnEditAddrTable btn btn-sm btn-warning fa fa-edit' 
					ADDRNO  = ".$arrs['ADDRNO']."
					ADDR1   = ".$arrs['ADDR1']."
					SOI     = ".$arrs['SOI']."
					ADDR2   = ".$arrs['ADDR2']."
					MOOBAN  = ".$arrs['MOOBAN']."
					TUMB    = ".$arrs['TUMB']."
					AUMPCOD = ".$arrs['AUMPCOD']."
					PROVCOD = ".$arrs['PROVCOD']."
					AUMPDES = ".$arrs['AUMPDES']."
					PROVDES = ".$arrs['PROVDES']."
					ZIP     = ".$arrs['ZIP']."
					TELP    = ".$arrs['TELP']."
					MEMO1   = ".$arrs['MEMO2']."
					>แก้ไข</button></td>
				<td><button class='DelTableAddr btn btn-sm btn-danger fa fa-trash'>ลบ</button><td>
			</tr>
		";
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
						{$arrs['SNAM']}
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
						<input type='text' class='form-control input-sm checkvalue' id='BIRTHDT' data-date-format= 'yyyy-mm-dd' value='{$arrs['BIRTHDT']}'>
					</div>
					<div class='col-sm-4'>
						ประเภทบัตรประจำตัว
						<select type='text' class='form-control input-sm' id='IDCARD' >
							<option value='{$arrs['IDCARD']}'>{$arrs['IDCARD']}</option>
							<option value='บัตรประชาชน'>บัตรประชาชน</option>
							<option value='บัตรข้าราชการ/รัฐวิสาหกิจ'>บัตรข้าราชการ/รัฐวิสาหกิจ</option>
							<option value='ทะเบียนการค้า'>ทะเบียนการค้า</option>
							<option value='บัตรต่างด้าว'>บัตรต่างด้าว</option>
							<option value='ไม่ระบุ'>ไม่ระบุ</option>
							<option value='อื่นๆ'>อื่นๆ</option>
						</select>
					</div>
				</div>
			</div>
			<div class='col-sm-10 col-sm-offset-1'>
				<div class='row'>
					<div class='col-sm-4'>
						เลขที่
						<input type='text' class='form-control input-sm checkvalue' id='IDNO' value='{$arrs['IDNO']}'>
					</div>
					<div class='col-sm-4'>
						ออกโดย
						<input type='text' class='form-control input-sm checkvalue' id='ISSUBY' value='{$arrs['ISSUBY']}'>
					</div>
					<div class='col-sm-4'>
						วัน/เดือน/ปี ที่ออกบัตร
						<input type='text' class='form-control input-sm checkvalue' id='ISSUDT' data-date-format= 'yyyy-mm-dd' value='{$arrs['ISSUDT']}'>
					</div>    
				</div>
			</div>
			<div class='col-sm-10 col-sm-offset-1'>
				<div class='row'>
					<div class='col-sm-4'>
						วัน/เดือน/ปี บัตรหมดอายุ
						<input type='text' class='form-control input-sm checkvalue' id='EXPDT' data-date-format= 'yyyy-mm-dd' value='{$arrs['EXPDT']}'>
					</div>
					<div class='col-sm-4'>
						อายุ
						<input type='number' class='form-control input-sm checkvalue' id='AGE' value='{$arrs['AGE']}'>
					</div>
					<div class='col-sm-4'>
						สัญชาติ
						<select type='text' class='form-control input-sm' id='NATION' >
							<option value='{$arrs['NATION']}'>{$arrs['NATION']}</option>
							<option value='ไทย'>ไทย</option>
							<option value='จีน'>จีน</option>
							<option value='ลาว'>ลาว</option>
							<option value='เขมร'>เขมร</option>
							<option value='มาเลเซีย'>มาเลเซีย</option>
							<option value='พม่า'>พม่า</option>
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
						<input type='text' class='form-control input-sm checkvalue' id='MOBILENO' maxlength='10' value='{$arrs['MOBILENO']}'>
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
			</div>";    
			if($EVENT == 'add'){
				$html .="<div class='col-sm-10 col-sm-offset-1'>
					<div class='row'>
						<table id = 'data-table-address' class='col-sm-12 display table table-striped table-bordered table-hover' cellspacing='0' width='100%' id='dataAddress'>
							<thead>
								<tr>
									<th>ลำดับ</th>
									<th>ที่อยู่</th>
									<th>เบอร์ติดต่อ</th>
									<th>หมายเหตุ</th>
									<th>แก้ไข</th>
									<th>ลบ</th>
								</tr>
							</thead>
							<tbody id='AA'>
							
							</tbody>
							<tfoot>
								<tr>
									<td colspan='6'>
										<button id='btnAddAddressFirst' class='btn btn-sm btn-cyan btn-block glyphicon glyphicon-plus'>เพิ่มที่อยู่</button>
									</td>	
								</tr>
							</tfoot>
						</table>
					</div>    
				</div>";
			}else{
				$html .="<div class='col-sm-10 col-sm-offset-1'>
					<div class='row'>
						<table id = 'data-table-address' class='col-sm-12 display table table-striped table-bordered table-hover' cellspacing='0' width='100%' id='dataAddress'>
							<thead>
								<tr>
									<th>ลำดับ</th>
									<th>ที่อยู่</th>
									<th>เบอร์ติดต่อ</th>
									<th>หมายเหตุ</th>
									<th>แก้ไข</th>
									<th>ลบ</th>
								</tr>
							</thead>
							<tbody id='AA'>
								".$tbody_db."
							</tbody>
							<tfoot>
								<tr>
									<td colspan='6'>
										<button id='btnAddAddressFirst' class='btn btn-sm btn-cyan btn-block glyphicon glyphicon-plus'>เพิ่มที่อยู่</button>
									</td>	
								</tr>
							</tfoot>
						</table>
					</div>    
				</div>";
			}	
			$html .="<div class='col-sm-10 col-sm-offset-1'>
				<div class='row'>
					<div class='col-sm-4'>
						ที่อยู่ตามทะเบียนบ้าน
						<select type='text' class='form-control' id='addrno1'>
						
						</select>
					</div>
					<div class='col-sm-4'>
						ที่อยู่ปัจจุบัน
						<select type='text' class='form-control' id='addrno2'>
						
						</select>
					</div>
					<div class='col-sm-4'>
						ที่อยู่ที่อยู่ที่ส่งจดหมาย
						<select type='text' class='form-control' id='addrno3'>
						
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
			
			<div class='col-sm-10 col-sm-offset-7'>
				<div class='row'><br>
					<div class='col-sm-3'>
						<button type='button' id='add_save' class='btn btn-sm btn-primary btn-block'>บันทึก</button>
					</div>
					<div class='col-sm-3'>
						<button type='button' id='add_del' class='btn btn-sm btn-danger btn-block'>ลบ</button>
					</div><br><br>
				</div>    
			</div>
        ";
        $response = array("html"=>$html);
        echo json_encode($response);
	}
	function getFormAddressCM(){
		$arrs = array();
		$arrs = array();
        $arrs["ADDRNO"]	  = (!isset($_POST["ADDRNO"])?  "":$_POST["ADDRNO"]);
        $arrs["ADDR1"] 	  = (!isset($_POST["ADDR1"])?   "":$_POST["ADDR1"]);
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
		
		$html ="
			<div class='col-sm-10 col-sm-offset-1'>
				<div class='row'>
					<div class='col-sm-12'>
						ลำดับ
						<input type='number' class='form-control' id='ADDRNO' value='".$arrs["ADDRNO"]."'>
					</div>
					<div class='col-sm-12'>
						บ้านเลขที่
						<input type='text' class='form-control' id='ADDR1' value='".$arrs["ADDR1"]."'>
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
						<button id='btnAddTableHtml' class='btn btn-block btn-primary'>เพิ่ม</button><br>					
					</div>
					<div class='col-sm-6'>
						<button id='btnWACloseAdd' class='btn btn-block btn-danger'>ยกเลิก</button><br>
					</div>
				</div>
			";
		}else{
			$html .="
				<div class='row col-sm-12'>
					<div class='col-sm-6'>
						<button id='btneditTableHtml' class='btn btn-block btn-warning'>แก้ไข</button><br>					
					</div>
					<div class='col-sm-6'>
						<button id='btnWAClose' class='btn btn-block btn-danger'>ยกเลิก</button><br>
					</div>
				</div>
			";
		}
		$response = array("html" => $html);
        echo json_encode($response);
	}
	function SetAddr_TableHtml(){
		$arrs = array(); 
		$arrs["ADDRNO"]  = $_POST["ADDRNO"];
        $arrs["ADDR1"]   = $_POST["ADDR1"];
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
			$address .= "บ้านเลขที่".$arrs["ADDR1"];
        }
        if($arrs["SOI"] != ""){
			$address .= " ซอย".$arrs["SOI"];
        }
        if($arrs["ADDR2"] != ""){
			$address .= " ถนน".$arrs["ADDR2"];
        }
        if($arrs["MOOBAN"] != ""){
			$address .= " หมู่บ้าน".$arrs["MOOBAN"];
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
			$address .= " ".$arrs["ZIP"];
        }
        $tbody = "
            <tr>
                <td>".$arrs["ADDRNO"]."</td>
                <td>".$address."</td>
                <td>".$arrs["TELP"]."</td>
                <td>".$arrs["MEMO1"]."</td>
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
					>แก้ไข</button>
                </td>
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
        $arrs['BIRTHDT']     = $this->dateformatsql($_POST["BIRTHDT"]);
		$arrs['ADDRNO']		 = $_POST["ADDRNO"];
        $arrs['IDCARD']      = $_POST["IDCARD"];
        $arrs['IDNO']        = $_POST["IDNO"];
        $arrs['ISSUBY']      = $_POST["ISSUBY"];
        $arrs['ISSUDT']      = $this->dateformatsql($_POST["ISSUDT"]);
        $arrs['EXPDT']       = $this->dateformatsql($_POST["EXPDT"]);
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
		//echo ($arrs); exit;
		
		$ADDR     			 = $_POST["ADDR"];
		
		//print_r ($ADDR); exit;
		
		if($arrs["NAME1"] == ""){
			$response["error"] = true;
			$response["msg"] = 'คุณยังไม่ได้ระบุชื่อลูกค้าเลย กรุณากรอกชื่อลูกค้าก่อนนะครับ';
			echo json_encode($response); exit;
		}
		
		if($arrs["MAXCRED"] == ""){$arrs["MAXCRED"] = "0.00"; }else{ $arrs["MAXCRED"] = str_replace(",","",$arrs["MAXCRED"]);}
		
		if($arrs["CUSCOD"] == "Auto Genarate"){
			$this->saveCustomerHistory($arrs,$ADDR);
		}
	}
	function saveCustomerHistory($arrs,$ADDR){
		$sql_addr = "";		//บันทึกที่อยู่ของลูกค้าเข้าฐานข้อมูล
        $sizeArr = count($ADDR);
        for($P=0; $P < $sizeArr; $P++){
		$sql_addr .="
			insert into {$this->MAuth->getdb('CUSTADDR')}(
				[CUSCOD],[ADDRNO],[ADDR1],[ADDR2],[TUMB],[AUMPCOD],[PROVCOD]
				,[ZIP],[TELP],[MEMO1],[ACPDT],[USERID],[PICT1],[MOOBAN],[SOI]
			)values(
				@CONTNO,'".$ADDR[$P][0]."','".$ADDR[$P][1]."','".$ADDR[$P][3]."'
				,'".$ADDR[$P][5]."','".$ADDR[$P][6]."','".$ADDR[$P][7]."','".$ADDR[$P][8]."'
				,'".$ADDR[$P][9]."','".$ADDR[$P][10]."',null,null,null,'".$ADDR[$P][4]."'
				,'".$ADDR[$P][2]."'
				)
			";
        }
		
		$sql ="
			if OBJECT_ID('tempdb..#custmastTemp') is not null drop table #custmastTemp;
			create table #custmastTemp (id varchar(20),contno varchar(20),msg varchar(max));

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
				
				BEGIN
					set @TAXNO = null;
					set @TAXDT = null;
				END
				begin
				insert into {$this->MAuth->getdb('CUSTMAST')} (
					[CUSCOD],[GROUP1],[SNAM],[NAME1],[NAME2],[NICKNM],[BIRTHDT],[ADDRNO],[IDCARD],[IDNO],[ISSUBY]
					,[ISSUDT],[EXPDT],[AGE],[NATION],[OCCUP],[OFFIC],[BOSSNM],[GRADE],[ACPDT],[MEMO1],[USERID],[PICT1]
					,[MINCOME],[YINCOME],[MAXCRED],[MREVENU],[YREVENU],[MEMBCOD],[MOBILENO],[APPVCODE],[SIRCOD]
					,[CUSTTYPE],[ADDRNO2],[ADDRNO3],[EMAIL1],[EMAIL2]
				)values(
					@CONTNO,'".$arrs["GROUP1"]."','".$arrs["SNAM"]."','".$arrs["NAME1"]."'
                    ,'".$arrs["NAME2"]."','".$arrs["NICKNM"]."','".$arrs["BIRTHDT"]."','".$arrs['ADDRNO']."','".$arrs["IDCARD"]."'
                    ,'".$arrs["IDNO"]."','".$arrs["ISSUBY"]."','".$arrs["ISSUDT"]."','".$arrs["EXPDT"]."','".$arrs["AGE"]."'
                    ,'".$arrs['NATION']."','".$arrs['OCCUP']."','".$arrs['OFFIC']."',null,'".$arrs['GRADE']."',null
                    ,'".$arrs['MEMOADD']."',null,null,null,null,'".$arrs['MAXCRED']."','".$arrs['MREVENU']."'
                    ,'".$arrs['YREVENU']."',null,'".$arrs['MOBILENO']."',null,null,null,'".$arrs['ADDRNO2']."'
                    ,'".$arrs['ADDRNO3']."','".$arrs['EMAIL1']."',null
					);
				".$sql_addr."	
				end
				
				insert into {$this->MAuth->getdb('hp_UserOperationLog')} (userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
				values ('".$this->sess["IDNo"]."','SYS04::บันทึกประวัติลูกค้า',@CONTNO+' ".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
				
				insert into #custmastTemp select 'N',@CONTNO,'บันทึกประวัติลูกค้าเลขที่ :: '+@CONTNO+' เรียบร้อยแล้ว';
				
				commit tran custmastTran;
			end try
			begin catch
				rollback tran custmastTran;
				insert into #custmastTemp select 'E','',ERROR_MESSAGE();
			end catch
		";
		$this->db->query($sql);
		$sql = "select * from #custmastTemp";
		$query = $this->db->query($sql);
	  
		if($query->row()){
			foreach($query->result() as $row){
				$response["status"] = $row->id;
				$response["contno"] = $row->contno;
				$response["msg"] = $row->msg;
			}
		}else{
			$response["status"] = false;
			$response["contno"] = '';
			$response["msg"] = 'ผิดพลาดไม่สามารถบันทึกการขายได้ โปรดติดต่อฝ่ายไอที';
		}
		echo json_encode($response);
	}
	
	function dateformatsql($date){
        if($date!=""){
            return substr($date, 6,4).substr($date, 3,2).substr($date, 0,2);
        }
        return $date;
    }
	function SetTitle(){
		$claim = $this->MLogin->getclaim(uri_string());
		if($claim['m_access'] != "T"){ echo "<div align='center' style='color:red;font-size:16pt;width:100%;'>ขออภัย คุณยังไม่มีสิทธิเข้าใช้งานหน้านี้ครับ</div>"; exit; }
		
		$html = "
			<div class='tab1' name='home' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}' style='height:65px;overflow:auto;'>
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
						<input type='button' id='add_groupsn' class='btn btn-cyan btn-sm' value='เพิ่มคำนำหน้า' style='width:100%'>
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




















