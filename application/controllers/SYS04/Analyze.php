<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/********************************************************
             ______@27/08/2019______
            / / _ _   _ _     __ 
           / // __ \ / __ \ / __ \
       _ _/ // /_/ // / / // /_/ /
     /_ _ _/ \_ _ //_/ /_/ \__  /
                          _ _/ /
                         /___ /
********************************************************/
class Analyze extends MY_Controller {
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
		// style='height:calc(100vh - 132px);overflow:auto;background-color:white;'
		// $this->load->library('user_agent'); print_r($this->agent); exit;
		$html = "
			<div class='tab1' name='home' locat='{$this->sess['branch']}' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}'>
				<div class='divcondition col-sm-12'>
					<div class='row'>
						<div class='col-sm-2'>	
							<div class='form-group'>
								เลขตัวถัง
								<input type='text' id='SSTRNO' class='form-control input-sm' placeholder='เลขตัวถัง' >
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								รุ่น
								<input type='text' id='SMODEL' class='form-control input-sm' placeholder='รุ่น' >
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								วันที่สร้าง จาก
								<input type='text' id='SCREATEDATEF' class='form-control input-sm' placeholder='จาก'  data-provide='datepicker' data-date-language='th-th' value=''>
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								วันที่สร้าง ถึง
								<input type='text' id='SCREATEDATET' class='form-control input-sm' placeholder='ถึง'  data-provide='datepicker' data-date-language='th-th' value=''>
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								วันที่อนุมัติ จาก
								<input type='text' id='SAPPROVEF' class='form-control input-sm' placeholder='จาก'  data-provide='datepicker' data-date-language='th-th' value=''>
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								วันที่อนุมัติ ถึง
								<input type='text' id='SAPPROVET' class='form-control input-sm' placeholder='ถึง'  data-provide='datepicker' data-date-language='th-th' value=''>
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								เลขที่บิลจอง
								<input type='text' id='SRESVNO' class='form-control input-sm' placeholder='เลขที่บิลจอง' >
							</div>
						</div>	
						
						<div class='col-sm-2'>	
							<div class='form-group'>
								ชื่อ-สกุล ผู้เช่าซื้อ
								<input type='text' id='SCUSNAME' class='form-control input-sm' placeholder='ชื่อ-สกุล ผู้เช่าซื้อ' >
							</div>
						</div>
						
						<div class='col-sm-2'>	
							<div class='form-group'>
								สถานะใบวิเคราะห์
								<select id='SANSTAT' class='form-control input-sm select2'>
									<option value=''>ทั้งหมด</option>
									<option value='A'>อนุมัติ</option>
									<option value='N'>ไม่อนุมัติ</option>
									<option value='I'>รออนุมัติ</option>
									<option value='C'>ยกเลิก</option>
								</select>	
							</div>
						</div>
					</div>
					<div class='row'>
						<div class='col-sm-6'>	
							<div class='form-group'>
								<button id='btnt1createappr' class='btn btn-cyan btn-block'><span class='glyphicon glyphicon-pencil'> สร้างรายการขออนุมัติ</span></button>
							</div>
						</div>
						<div class='col-sm-6'>	
							<div class='form-group'>
								<button id='btnt1search' class='btn btn-primary btn-block'><span class='glyphicon glyphicon-search'> ค้นหา</span></button>
							</div>
						</div>
					</div>
					
					<div id='result'></div>
				</div>
			</div>
		";
		
		$html.= "<script src='".base_url('public/js/SYS04/Analyze.js?tm='.date("his"))."'></script>";
		echo $html;
	}
	
	function search(){
		$arrs = array();
		
		$arrs['SSTRNO']	= $_POST['SSTRNO'];
		$arrs['SMODEL']	= $_POST['SMODEL'];
		$arrs['SCREATEDATEF']	= $this->Convertdate(1,$_POST['SCREATEDATEF']);
		$arrs['SCREATEDATET']	= $this->Convertdate(1,$_POST['SCREATEDATET']);
		$arrs['SAPPROVEF']	= $this->Convertdate(1,$_POST['SAPPROVEF']);
		$arrs['SAPPROVET']	= $this->Convertdate(1,$_POST['SAPPROVET']);
		$arrs['SRESVNO']	= $_POST['SRESVNO'];
		$arrs['SCUSNAME']	= $_POST['SCUSNAME'];
		$arrs['SANSTAT']	= $_POST['SANSTAT'];
		
		
		$cond = "";
		$condDesc = "";
		$condcnd = 0;
		if($arrs['SSTRNO'] != ""){			
			$cond .= " and a.STRNO like '".$arrs['SSTRNO']."%'";
			$condDesc .= " [เลขตัวถัง :: ".$arrs['SSTRNO']."]";
		}
		if($arrs['SMODEL'] != ""){
			$cond .= " and a.MODEL like '".$arrs['SMODEL']."%'";
			$condDesc .= " [รุ่น :: ".$arrs['SMODEL']."]";
		}
		
		if($arrs['SCREATEDATEF'] != "" and $arrs['SCREATEDATET'] != ""){
			$cond .= " and convert(varchar(8),a.CREATEDATE,112) between '".$arrs['SCREATEDATEF']."' and '".$arrs['SCREATEDATET']."' ";
			$condDesc .= " [วันที่สร้าง :: ".$_POST['SCREATEDATEF']." - ".$_POST['SCREATEDATET']."]";
		}else if($arrs['SCREATEDATEF'] != "" and $arrs['SCREATEDATET'] == ""){
			$cond .= " and convert(varchar(8),a.CREATEDATE,112) = '".$arrs['SCREATEDATEF']."'";
			$condDesc .= " [วันที่สร้าง :: ".$_POST['SCREATEDATEF']."]";
		}else if($arrs['SCREATEDATEF'] == "" and $arrs['SCREATEDATET'] != ""){
			$cond .= " and convert(varchar(8),a.CREATEDATE,112) = '".$arrs['SCREATEDATET']."'";
			$condDesc .= " [วันที่สร้าง :: ".$_POST['SCREATEDATET']."]";
		}
		
		if($arrs['SAPPROVEF'] != "" and $arrs['SAPPROVET'] != ""){
			$cond .= " and convert(varchar(8),b.APPROVEDT,112) between '".$arrs['SAPPROVEF']."' and '".$arrs['SAPPROVET']."' ";
			$condDesc .= " [วันที่อนุมัติ :: ".$_POST['SAPPROVEF']." - ".$_POST['SAPPROVET']."]";
		}else if($arrs['SAPPROVEF'] != "" and $arrs['SAPPROVET'] == ""){
			$cond .= " and convert(varchar(8),b.APPROVEDT,112) = '".$arrs['SAPPROVEF']."'";
			$condDesc .= " [วันที่อนุมัติ :: ".$_POST['SAPPROVEF']."]";
		}else if($arrs['SAPPROVEF'] == "" and $arrs['SAPPROVET'] != ""){
			$cond .= " and convert(varchar(8),b.APPROVEDT,112) = '".$arrs['SAPPROVET']."'";
			$condDesc .= " [วันที่อนุมัติ :: ".$_POST['SAPPROVET']."]";
		}
		
		if($arrs['SRESVNO'] != ""){
			$cond .= " and a.RESVNO like '".$arrs['SRESVNO']."%'";
			$condDesc .= " [เลขที่บิลจอง :: ".$arrs['SRESVNO']."]";
		}
		
		if($arrs['SANSTAT'] != ""){
			$cond .= " and a.ANSTAT = '".$arrs['SANSTAT']."'";
			$condDesc .= " [สถานะใบวิเคราะห์ :: ".$arrs['SANSTAT']."]";
		}
		
		
		$sql = "
			select ".($cond == "" ? "top 20":"")." * from (
				select a.ID,(
						select SNAM+NAME1+' '+NAME2 from {$this->MAuth->getdb('CUSTMAST')}
						where CUSCOD collate thai_cs_as in (
							select CUSCOD from {$this->MAuth->getdb('ARANALYZEREF')} where ID=a.ID and CUSTYPE=0
						)
					) as CUSNAME
					,a.STRNO,a.MODEL,a.BAAB,a.COLOR,a.STAT
					,CONVERT(varchar(8),a.CREATEDATE,112) as CREATEDATE
					,CONVERT(varchar(5),a.CREATEDATE,108) as CREATETM
					,a.LOCAT,a.RESVNO,a.NOPAY
					,a.ANSTAT
					,case when a.ANSTAT='I' then 'รออนุมัติ' 
						when a.ANSTAT='A' then 'อนุมัติ' 
						when a.ANSTAT='N' then 'ไม่อนุมัติ' 
						when a.ANSTAT='C' then 'ยกเลิก'  end as ANSTATDESC
					,CONVERT(varchar(8),b.APPROVEDT,112) as APPROVEDT
					,CONVERT(varchar(5),b.APPROVEDT,108) as APPROVETM
				from {$this->MAuth->getdb('ARANALYZE')} a 
				left join {$this->MAuth->getdb('ARANALYZEDATA')} b on a.ID=b.ID
				where 1=1 ".$cond."
			) as data
			where data.CUSNAME like '%".$arrs['SCUSNAME']."%'
			order by data.ID
		";
		//echo $sql; exit;		
		$query = $this->db->query($sql);
		
		$html = "";
		$NRow = 1;
		if($query->row()){
			foreach($query->result() as $row){
				$css = "color:black";
				if($row->ANSTAT == "A"){
					$css = "color:green";
				}else if($row->ANSTAT == "N"){
					$css = "color:red";
				}
				
				$html .= "
					<tr style='{$css}'>
						<td>
							<i class='andetail btn btn-xs btn-success glyphicon glyphicon-zoom-in' ANID='".$row->ID."' style='cursor:pointer;'> รายละเอียด  </i>
						</td>
						<td>".$row->ID."</td>
						<td>".$row->CUSNAME."</td>
						<td>".$row->STRNO."</td>
						<td>".$row->MODEL."</td>
						<td>".$row->BAAB."</td>
						<td>".$row->COLOR."</td>
						<td>".$row->STAT."</td>
						<td>".$this->Convertdate(2,$row->CREATEDATE)." ".$row->CREATETM."</td>
						<td>".$row->LOCAT."</td>
						<td>".$row->RESVNO."</td>
						<td>".$row->NOPAY."</td>
						<td>".$row->ANSTATDESC."</td>
						<td>".$this->Convertdate(2,$row->APPROVEDT)." ".$row->APPROVETM."</td>
					</tr>
				";
				
				$NRow++;
			}
		}
		
		$html = "
			<div id='table-fixed-Analyze' class='col-sm-12' style='height:calc(100% - 30px);width:100%;overflow:auto;font-size:8pt;'>
				<table id='table-Analyze' class='table table-bordered' cellspacing='0' width='calc(100% - 1px)' style='background: rgba(0, 0, 0, 0) url(&#39;../public/lobiadmin-master/version/1.0/ajax/img/bg/bg4.png&#39;) repeat scroll 0% 0%;'>
					<thead style='background: rgba(0, 0, 0, 0) url(&#39;../public/lobiadmin-master/version/1.0/ajax/img/bg/bg6.png&#39;) repeat scroll 0% 0%;'>
						<tr align='center' style='line-height:20px;'>
							<th style='vertical-align:middle;text-align:center;font-size:8pt;' colspan='14'>
								เงื่อนไข :: ".$condDesc."
							</th>
						</tr>
						<tr align='center'>
							<th>###</th>
							<th style='vertical-align:middle;'>เลขที่<br>ใบวิเคราะห์</th>
							<th style='vertical-align:middle;'>ผู้เช่าซื้อ</th>
							<th style='vertical-align:middle;'>เลขตัวถัง</th>
							<th style='vertical-align:middle;'>รุ่น</th>
							<th style='vertical-align:middle;'>แบบ</th>
							<th style='vertical-align:middle;'>สี</th>
							<th style='vertical-align:middle;'>สถานะรถ</th>
							<th style='vertical-align:middle;'>วันที่สร้าง</th>
							<th style='vertical-align:middle;'>สาขา</th>
							<th style='vertical-align:middle;'>เลขที่บิลจอง</th>
							<th style='vertical-align:middle;'>จำนวนงวด</th>
							<th style='vertical-align:middle;'>สถานะ<br>ใบวิเคราะห์</th>
							<th style='vertical-align:middle;'>วันที่อนุมัติ</th>
						</tr>
					</thead>	
					<tbody>
						".$html."
					</tbody>
				</table>
			</div>
			<div id='table-fixed-Analyze-detail' class='col-sm-12' style='height:calc(100% - 30px);width:100%;overflow:auto;font-size:8pt;'>
			
			</div>
		";
		
		$response = array("html"=>$html,"status"=>true);
		echo json_encode($response);
	}
	
	function searchDetail(){
		$ANID = $_POST["ANID"];
		
		$sql = "
			select a.ID,a.LOCAT
				,CONVERT(varchar(8),a.CREATEDATE,112) as CREATEDATE
				,CONVERT(varchar(5),a.CREATEDATE,108) as CREATETIME
				,a.RESVNO
				,a.RESVAMT as M_RESVAMT,a.DWN as M_DWN
				,a.DWN_INSURANCE as M_DWN_INSURANCE,a.NOPAY
				,a.STRNO,a.MODEL+' ('+a.BAAB+')' as MODEL,a.COLOR
				,case when a.STAT='N' then 'รถใหม่' else 'รถเก่า' end as STAT
				,CONVERT(varchar(8),a.SDATE,112) as SDATE
				,CONVERT(varchar(8),a.YDATE,112) as YDATE
				,a.PRICE as M_PRICE,a.ANSTAT,a.INSBY
				,CONVERT(varchar(8),a.INSDT,112) as INSDT
				,CONVERT(varchar(5),a.INSDT,108) as INSTM
				,CONVERT(varchar(8),b.APPROVEDT,112) as APPDT
				,CONVERT(varchar(5),b.APPROVEDT,108) as APPTM
				,case when a.ANSTAT='I' then 'รออนุมัติ' 
					when a.ANSTAT='A' then 'อนุมัติ' 
					when a.ANSTAT='N' then 'ไม่อนุมัติ' 
					when a.ANSTAT='C' then 'ยกเลิก'  end as ANSTATDESC
				,a.STDID
				,a.STDPLRANK
			from {$this->MAuth->getdb('ARANALYZE')} a
			left join {$this->MAuth->getdb('ARANALYZEDATA')} b on a.ID=b.ID
			where a.ID='".$ANID."'
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$arrs = array("null"=>"");
		$NRow = 1;
		if($query->row()){
			foreach($query->result() as $row){
				foreach($row as $key => $val){
					switch($key){
						case 'CREATEDATE': 
							$arrs[$key] = $this->Convertdate(2,$val); 
							break;
						case 'SDATE': 
							$arrs[$key] = $this->Convertdate(2,$val); 
							break;
						case 'APPDT': 
							$arrs[$key] = $this->Convertdate(2,$val); 
							break;
						default: 
							if(substr($key,0,2) == "M_"){
								$arrs[str_replace("M_","",$key)] = number_format($val,2);
							}else{
								$arrs[$key] = $val;
							}
							break;
					}
				}
			}
		}
		
		$sql = "
			select a.CUSTYPE,a.CUSCOD
				,b.SNAM+b.NAME1+' '+b.NAME2 as CUSNAME 
				,b.IDNO as CUSIDNO
				,convert(varchar(10),b.BIRTHDT,103) as CUSBIRTH
				,convert(varchar(10),b.EXPDT,103) as CUSEXPDT
				,datediff(YEAR,b.BIRTHDT,GETDATE()) as CUSAGE
				,case when a.CUSSTAT=1 then 'โสด'
					when a.CUSSTAT=2 then 'สมรส'
					when a.CUSSTAT=3 then 'หม้าย'
					when a.CUSSTAT=4 then 'หย่า'
					when a.CUSSTAT=5 then 'แยกกันอยู่' end as CUSSTAT
				,a.CUSBABY
				,c.ADDR1+' '+c.ADDR2+' '+c.TUMB+' '+d.AUMPDES+' '+e.PROVDES+' '+c.ZIP as CUSADDR1
				,f.ADDR1+' '+f.ADDR2+' '+f.TUMB+' '+g.AUMPDES+' '+h.PROVDES+' '+f.ZIP as CUSADDR2
				,a.CAREER,a.CAREERADDR,a.CAREERTEL,a.SOCAILSECURITY
				,b.MREVENU as M_MREVENU,b.MOBILENO,a.HOSTNAME,a.HOSTIDNO,a.HOSTTEL
				,a.HOSTRELATION,a.EMPRELATION,a.REFERANT,b.GRADE
			from {$this->MAuth->getdb('ARANALYZEREF')} a
			left join {$this->MAuth->getdb('CUSTMAST')} b on a.CUSCOD=b.CUSCOD collate thai_ci_as
			left join {$this->MAuth->getdb('CUSTADDR')} c on cast(a.ADDRNO as varchar)=c.ADDRNO and a.CUSCOD=c.CUSCOD collate thai_ci_as
			left join {$this->MAuth->getdb('SETAUMP')} d on c.AUMPCOD=d.AUMPCOD
			left join {$this->MAuth->getdb('SETPROV')} e on d.PROVCOD=e.PROVCOD
			left join {$this->MAuth->getdb('CUSTADDR')} f on cast(a.ADDRDOCNO as varchar)=f.ADDRNO and a.CUSCOD=f.CUSCOD collate thai_ci_as 
			left join {$this->MAuth->getdb('SETAUMP')} g on f.AUMPCOD=g.AUMPCOD
			left join {$this->MAuth->getdb('SETPROV')} h on g.PROVCOD=h.PROVCOD
			where a.ID='".$ANID."'
			order by a.CUSTYPE
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		if($query->row()){
			foreach($query->result() as $row){
				foreach($row as $key => $val){
					switch($key){
						default: 
							if(substr($key,0,2) == "M_"){
								$arrs[str_replace("M_","",$key)][$row->CUSTYPE] = ($val == "" ? "-":number_format($val,2));
							}else{
								$arrs[$key][$row->CUSTYPE] = ($val == "" ? "-":$val);
							}
							break;
					}
				}
			}
		}
		
		$sql = "
			select a.EMP,'คุณ'+b.firstName+' '+b.lastName as EMPNAME,a.EMPTEL
				,a.MNG,'คุณ'+c.firstName+' '+c.lastName as MNGNAME,a.MNGTEL
				,a.APPROVE,'คุณ'+d.firstName+' '+d.lastName as APPROVENAME,a.APPROVETEL
				,a.comment
			from {$this->MAuth->getdb('ARANALYZEDATA')} a
			left join {$this->MAuth->getdb('hp_vusers')} b on a.EMP=b.IDNo collate thai_cs_as
			left join {$this->MAuth->getdb('hp_vusers')} c on a.MNG=c.IDNo collate thai_cs_as
			left join {$this->MAuth->getdb('hp_vusers')} d on a.APPROVE=d.IDNo collate thai_cs_as
			where a.ID='".$ANID."'
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		if($query->row()){
			foreach($query->result() as $row){
				foreach($row as $key => $val){
					$arrs[$key] = ($val == "" ? "-":$val);
				}
			}
		}
		
		$css = "";
		if($arrs["ANSTAT"] == "A"){
			$css = "color:green";
		}else if($arrs["ANSTAT"] == "N"){
			$css = "color:red";
		}
		
		$csscus0 = "";
		$csscus1 = "";
		$csscus2 = "";		
		if(in_array($arrs["GRADE"][0],array('F','FF'))){ $csscus0 = "color:red;"; }
		if(in_array($arrs["GRADE"][1],array('F','FF'))){ $csscus1 = "color:red;"; }
		if(isset($arrs["GRADE"][2])){
			if(in_array($arrs["GRADE"][2],array('F','FF'))){ $csscus2 = "color:red;"; }			
		}
		 
		$html = "
			<div class='col-sm-12' style='border:1px dotted #aaa;{$css}'>
				<div class='col-sm-10 col-sm-offset-1'>	
					<table  style='width:100%;font-size:10pt;'>
						<!-- tr style='background-color:#1fecff;' -->
						<tr style='background: rgba(0, 0, 0, 0) url(&#39;../public/lobiadmin-master/version/1.0/ajax/img/bg/bg6.png&#39;) repeat scroll 0% 0%;'>
							<td>
								<div class='col-sm-4'><b>เลขที่ใบวิเคราะห์ :: </b>".$arrs["ID"]."</div>
								<div class='col-sm-4 col-sm-offset-4'><b>วันที่ขออนุมัติ :: </b> ".$arrs["CREATEDATE"]." ".$arrs["CREATETIME"]."</div>
							</td>
						</tr>
						<!-- tr style='background-color:#1fecff;' -->
						<tr style='background: rgba(0, 0, 0, 0) url(&#39;../public/lobiadmin-master/version/1.0/ajax/img/bg/bg6.png&#39;) repeat scroll 0% 0%;'>
							<td>
								<div class='col-sm-4'><b>สาขา :: </b>".$arrs["LOCAT"]."</div>
								<div class='col-sm-4 col-sm-offset-4'><b>วันที่อนุมัติ :: </b> ".$arrs["APPDT"]." ".$arrs["APPTM"]."</div>
							</td>
						</tr>
						<!-- tr style='background-color:#1fecff;' -->
						<tr style='background: rgba(0, 0, 0, 0) url(&#39;../public/lobiadmin-master/version/1.0/ajax/img/bg/bg6.png&#39;) repeat scroll 0% 0%;'>
							<td>
								<div class='col-sm-4'><b>รุ่น :: </b>".$arrs["MODEL"]."</div>
								<div class='col-sm-4'><b>สถานะรถ :: </b> ".$arrs["STAT"]."</div>
								<div class='col-sm-4'><b>เลขตัวถัง :: </b> ".$arrs["STRNO"]."</div>
							</td>
						</tr>
						<!-- tr style='background-color:#1fecff;' -->
						<tr style='background: rgba(0, 0, 0, 0) url(&#39;../public/lobiadmin-master/version/1.0/ajax/img/bg/bg6.png&#39;) repeat scroll 0% 0%;'>
							<td>
								<div class='col-sm-4'><b>เงินดาวน์รถ :: </b>".$arrs["DWN"]."</div>
								<div class='col-sm-4'><b>เงินจอง :: </b> ".$arrs["RESVAMT"]."</div>
								<div class='col-sm-4'><b>วันที่ขายล่าสุด :: </b> ".$arrs["SDATE"]."</div>
							</td>
						</tr>
						<!-- tr style='background-color:#1fecff;' -->
						<tr style='background: rgba(0, 0, 0, 0) url(&#39;../public/lobiadmin-master/version/1.0/ajax/img/bg/bg6.png&#39;) repeat scroll 0% 0%;'>
							<td>
								<div class='col-sm-4'><b>เงินดาวน์ ป.1 :: </b>".$arrs["DWN_INSURANCE"]."</div>
								<div class='col-sm-4'><b>จำนวนงวด :: </b> ".$arrs["NOPAY"]."</div>
								<div class='col-sm-4'><b>วันที่ยึด :: </b> ".$arrs["YDATE"]."</div>
							</td>
						</tr>
						<tr style='background: rgba(0, 0, 0, 0) url(&#39;../public/lobiadmin-master/version/1.0/ajax/img/bg/bg6.png&#39;) repeat scroll 0% 0%;'>
							<td>
								<div class='col-sm-4'><b>บิล std :: </b>".$arrs["STDID"]."</div>
								<div class='col-sm-4'></div>
								<div class='col-sm-4'></div>
							</td>
						</tr>
						<tr style='background: rgba(0, 0, 0, 0) url(&#39;../public/lobiadmin-master/version/1.0/ajax/img/bg/bg6.png&#39;) repeat scroll 0% 0%;'>
							<td>
								<hr>
							</td>
						</tr>
						<!-- tr style='background-color:#f2ff8f;' -->
						<tr style='background: rgba(0, 0, 0, 0) url(&#39;../public/lobiadmin-master/version/1.0/ajax/img/bg/bg4.png&#39;) repeat scroll 0% 0%;'>
							<td>
								<table style='width:100%;font-size:10pt;'>
									<tr>
										<th></th>
										<th>ผู้เช่าซื้อ</th>
										<th>ผู้ค้ำประกัน 1</th>
										<th>ผู้ค้ำประกัน 2</th>
									</tr>
									<tr>
										<th style='text-align:right;padding-right:20px;'>ชื่อ-สกุล</th>
										<td style='".$csscus0."'>".(isset($arrs["CUSNAME"][0]) ? $arrs["CUSNAME"][0]:"-")."</td>
										<td style='".$csscus1."'>".(isset($arrs["CUSNAME"][1]) ? $arrs["CUSNAME"][1]:"-")."</td>
										<td style='".$csscus2."'>".(isset($arrs["CUSNAME"][2]) ? $arrs["CUSNAME"][2]:"-")."</td>
									</tr>
									<tr>
										<th style='text-align:right;padding-right:20px;'>เลขที่บัตรประชาชน</th>
										<td style='".$csscus0."'>".(isset($arrs["CUSIDNO"][0]) ? $arrs["CUSIDNO"][0]:"-")."</td>
										<td style='".$csscus1."'>".(isset($arrs["CUSIDNO"][1]) ? $arrs["CUSIDNO"][1]:"-")."</td>
										<td style='".$csscus2."'>".(isset($arrs["CUSIDNO"][2]) ? $arrs["CUSIDNO"][2]:"-")."</td>
									</tr>
									<tr>
										<th style='text-align:right;padding-right:20px;'>ว.ด.ป.เกิด (คศ)</th>
										<td style='".$csscus0."'>".(isset($arrs["CUSBIRTH"][0]) ? $arrs["CUSBIRTH"][0]:"-")."</td>
										<td style='".$csscus1."'>".(isset($arrs["CUSBIRTH"][1]) ? $arrs["CUSBIRTH"][1]:"-")."</td>
										<td style='".$csscus2."'>".(isset($arrs["CUSBIRTH"][2]) ? $arrs["CUSBIRTH"][2]:"-")."</td>
									</tr>
									<tr>
										<th style='text-align:right;padding-right:20px;'>ว.ด.ป.บัตรหมดอายุ (คศ)</th>
										<td style='".$csscus0."'>".(isset($arrs["CUSEXPDT"][0]) ? $arrs["CUSEXPDT"][0]:"-")."</td>
										<td style='".$csscus1."'>".(isset($arrs["CUSEXPDT"][1]) ? $arrs["CUSEXPDT"][1]:"-")."</td>
										<td style='".$csscus2."'>".(isset($arrs["CUSEXPDT"][2]) ? $arrs["CUSEXPDT"][2]:"-")."</td>
									</tr>
									<tr>
										<th style='text-align:right;padding-right:20px;'>อายุ</th>
										<td style='".$csscus0."'>".(isset($arrs["CUSAGE"][0]) ? $arrs["CUSAGE"][0]:"-")."</td>
										<td style='".$csscus1."'>".(isset($arrs["CUSAGE"][1]) ? $arrs["CUSAGE"][1]:"-")."</td>
										<td style='".$csscus2."'>".(isset($arrs["CUSAGE"][2]) ? $arrs["CUSAGE"][2]:"-")."</td>
									</tr>
									<tr>
										<th style='text-align:right;padding-right:20px;'>สถานะภาพการสมรส</th>
										<td style='".$csscus0."'>".(isset($arrs["CUSSTAT"][0]) ? $arrs["CUSSTAT"][0]:"-")."</td>
										<td style='".$csscus1."'>".(isset($arrs["CUSSTAT"][1]) ? $arrs["CUSSTAT"][1]:"-")."</td>
										<td style='".$csscus2."'>".(isset($arrs["CUSSTAT"][2]) ? $arrs["CUSSTAT"][2]:"-")."</td>
									</tr>
									<tr>
										<th style='text-align:right;padding-right:20px;'>จำนวนบุตร</th>
										<td style='".$csscus0."'>".(isset($arrs["CUSBABY"][0]) ? $arrs["CUSBABY"][0]:"-")."</td>
										<td style='".$csscus1."'>".(isset($arrs["CUSBABY"][1]) ? $arrs["CUSBABY"][1]:"-")."</td>
										<td style='".$csscus2."'>".(isset($arrs["CUSBABY"][2]) ? $arrs["CUSBABY"][2]:"-")."</td>
									</tr>
									<tr>
										<th style='text-align:right;padding-right:20px;'>ที่อยู่ตาม ทบ.บ้าน</th>
										<td style='".$csscus0."max-width:150px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;'>".(isset($arrs["CUSADDR1"][0]) ? $arrs["CUSADDR1"][0]:"-")."</td>
										<td style='".$csscus1."max-width:150px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;'>".(isset($arrs["CUSADDR1"][1]) ? $arrs["CUSADDR1"][1]:"-")."</td>
										<td style='".$csscus2."max-width:150px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;'>".(isset($arrs["CUSADDR1"][2]) ? $arrs["CUSADDR1"][2]:"-")."</td>
									</tr>
									<tr>
										<th style='text-align:right;padding-right:20px;'>ที่อยู่ส่งเอกสาร</th>
										<td style='".$csscus0."max-width:150px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;'>".(isset($arrs["CUSADDR2"][0]) ? $arrs["CUSADDR2"][0]:"-")."</td>
										<td style='".$csscus1."max-width:150px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;'>".(isset($arrs["CUSADDR2"][1]) ? $arrs["CUSADDR2"][1]:"-")."</td>
										<td style='".$csscus2."max-width:150px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;'>".(isset($arrs["CUSADDR2"][2]) ? $arrs["CUSADDR2"][2]:"-")."</td>
									</tr>
									<tr>
										<th style='text-align:right;padding-right:20px;'>อาชีพ</th>
										<td style='".$csscus0."'>".(isset($arrs["CAREER"][0]) ? $arrs["CAREER"][0]:"-")."</td>
										<td style='".$csscus1."'>".(isset($arrs["CAREER"][1]) ? $arrs["CAREER"][1]:"-")."</td>
										<td style='".$csscus2."'>".(isset($arrs["CAREER"][2]) ? $arrs["CAREER"][2]:"-")."</td>
									</tr>
									<tr>
										<th style='text-align:right;padding-right:20px;'>สถานที่ทำงาน/ที่อยู่</th>
										<td style='".$csscus0."max-width:150px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;'>".(isset($arrs["CAREERADDR"][0]) ? $arrs["CAREERADDR"][0]:"-")."</td>
										<td style='".$csscus1."max-width:150px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;'>".(isset($arrs["CAREERADDR"][1]) ? $arrs["CAREERADDR"][1]:"-")."</td>
										<td style='".$csscus2."max-width:150px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;'>".(isset($arrs["CAREERADDR"][2]) ? $arrs["CAREERADDR"][2]:"-")."</td>
									</tr>
									<tr>
										<th style='text-align:right;padding-right:20px;'>เบอร์ติดต่อได้.ที่ทำงาน</th>
										<td style='".$csscus0."'>".(isset($arrs["CAREERTEL"][0]) ? $arrs["CAREERTEL"][0]:"-")."</td>
										<td style='".$csscus1."'>".(isset($arrs["CAREERTEL"][1]) ? $arrs["CAREERTEL"][1]:"-")."</td>
										<td style='".$csscus2."'>".(isset($arrs["CAREERTEL"][2]) ? $arrs["CAREERTEL"][2]:"-")."</td>
									</tr>
									<tr>
										<th style='text-align:right;padding-right:20px;'>ประกันสังคม (มี/ไม่มี)</th>
										<td style='".$csscus0."'>".(isset($arrs["SOCAILSECURITY"][0]) ? $arrs["SOCAILSECURITY"][0]:"-")."</td>
										<td style='".$csscus1."'>".(isset($arrs["SOCAILSECURITY"][1]) ? $arrs["SOCAILSECURITY"][1]:"-")."</td>
										<td style='".$csscus2."'>".(isset($arrs["SOCAILSECURITY"][2]) ? $arrs["SOCAILSECURITY"][2]:"-")."</td>
									</tr>
									<tr>
										<th style='text-align:right;padding-right:20px;'>รายได้ต่อเดือน</th>
										<td style='".$csscus0."'>".(isset($arrs["MREVENU"][0]) ? $arrs["MREVENU"][0]:"-")."</td>
										<td style='".$csscus1."'>".(isset($arrs["MREVENU"][1]) ? $arrs["MREVENU"][1]:"-")."</td>
										<td style='".$csscus2."'>".(isset($arrs["MREVENU"][2]) ? $arrs["MREVENU"][2]:"-")."</td>
									</tr>
									<tr>
										<th style='text-align:right;padding-right:20px;'>เบอร์โทรศัทพ์ติดต่อ.ลูกค้า</th>
										<td style='".$csscus0."'>".(isset($arrs["MOBILENO"][0]) ? $arrs["MOBILENO"][0]:"-")."</td>
										<td style='".$csscus1."'>".(isset($arrs["MOBILENO"][1]) ? $arrs["MOBILENO"][1]:"-")."</td>
										<td style='".$csscus2."'>".(isset($arrs["MOBILENO"][2]) ? $arrs["MOBILENO"][2]:"-")."</td>
									</tr>
									<tr>
										<th style='text-align:right;padding-right:20px;'>ชื่อ-สกุล (เจ้าบ้านตาม ทบ.บ้าน )</th>
										<td style='".$csscus0."'>".(isset($arrs["HOSTNAME"][0]) ? $arrs["HOSTNAME"][0]:"-")."</td>
										<td style='".$csscus1."'>".(isset($arrs["HOSTNAME"][1]) ? $arrs["HOSTNAME"][1]:"-")."</td>
										<td style='".$csscus2."'>".(isset($arrs["HOSTNAME"][2]) ? $arrs["HOSTNAME"][2]:"-")."</td>
									</tr>
									<tr>
										<th style='text-align:right;padding-right:20px;'>เลขที่บัตรประชาชน (เจ้าบ้าน)</th>
										<td style='".$csscus0."'>".(isset($arrs["HOSTIDNO"][0]) ? $arrs["HOSTIDNO"][0]:"-")."</td>
										<td style='".$csscus1."'>".(isset($arrs["HOSTIDNO"][1]) ? $arrs["HOSTIDNO"][1]:"-")."</td>
										<td style='".$csscus2."'>".(isset($arrs["HOSTIDNO"][2]) ? $arrs["HOSTIDNO"][2]:"-")."</td>
									</tr>
									<tr>
										<th style='text-align:right;padding-right:20px;'>เบอร์โทรศัทพ์ติดต่อ (เจ้าบ้าน)</th>
										<td style='".$csscus0."'>".(isset($arrs["HOSTTEL"][0]) ? $arrs["HOSTTEL"][0]:"-")."</td>
										<td style='".$csscus1."'>".(isset($arrs["HOSTTEL"][1]) ? $arrs["HOSTTEL"][1]:"-")."</td>
										<td style='".$csscus2."'>".(isset($arrs["HOSTTEL"][2]) ? $arrs["HOSTTEL"][2]:"-")."</td>
									</tr>
									<tr>
										<th style='text-align:right;padding-right:20px;'>ความสัมพันธ์กับ  เจ้าบ้าน</th>
										<td style='".$csscus0."'>".(isset($arrs["HOSTRELATION"][0]) ? $arrs["HOSTRELATION"][0]:"-")."</td>
										<td style='".$csscus1."'>".(isset($arrs["HOSTRELATION"][1]) ? $arrs["HOSTRELATION"][1]:"-")."</td>
										<td style='".$csscus2."'>".(isset($arrs["HOSTRELATION"][2]) ? $arrs["HOSTRELATION"][2]:"-")."</td>
									</tr>
									<tr>
										<th style='text-align:right;padding-right:20px;'>ความสัมพันธ์กับ พนักงาน</th>
										<td style='".$csscus0."'>".(isset($arrs["EMPRELATION"][0]) ? $arrs["EMPRELATION"][0]:"-")."</td>
										<td style='".$csscus1."'>".(isset($arrs["EMPRELATION"][1]) ? $arrs["EMPRELATION"][1]:"-")."</td>
										<td style='".$csscus2."'>".(isset($arrs["EMPRELATION"][2]) ? $arrs["EMPRELATION"][2]:"-")."</td>
									</tr>
									<tr>
										<th style='text-align:right;padding-right:20px;'>บุคคลอ้างอิง</th>
										<td style='".$csscus0."'>".(isset($arrs["REFERANT"][0]) ? $arrs["REFERANT"][0]:"-")."</td>
										<td style='".$csscus1."'>".(isset($arrs["REFERANT"][1]) ? $arrs["REFERANT"][1]:"-")."</td>
										<td style='".$csscus2."'>".(isset($arrs["REFERANT"][2]) ? $arrs["REFERANT"][2]:"-")."</td>
									</tr>
									<tr>
										<th style='text-align:right;padding-right:20px;'>อยู่ในกลุ่มเสี่ยงหรือไม่</th>
										<td style='".$csscus0."'>".(isset($arrs["GRADE"][0]) ? $arrs["GRADE"][0]:"-")."</td>
										<td style='".$csscus1."'>".(isset($arrs["GRADE"][1]) ? $arrs["GRADE"][1]:"-")."</td>
										<td style='".$csscus2."'>".(isset($arrs["GRADE"][2]) ? $arrs["GRADE"][2]:"-")."</td>
									</tr>									
									<tr>
										<th style='text-align:right;padding-right:20px;'></th>
										<td style='".$csscus0."'>
											".(isset($arrs["CUSCOD"][0]) ? "<input type='button' class='cushistory' cuscod='".$arrs["CUSCOD"][0]."' class='btn btn-xs btn-block' value='ประวัติการซื้อ'>":"")."
										</td>
										<td style='".$csscus1."'>										
											".(isset($arrs["CUSIDNO"][1]) ? "<input type='button' class='cushistory' cuscod='".$arrs["CUSCOD"][1]."' class='btn btn-xs btn-block' value='ประวัติการซื้อ'>":"")."
										</td>
										<td style='".$csscus2."'>
											".(isset($arrs["CUSIDNO"][2]) ? "<input type='button' class='cushistory' cuscod='".$arrs["CUSCOD"][2]."' class='btn btn-xs btn-block' value='ประวัติการซื้อ'>":"")."
										</td>
									</tr>									
								</table>
							</td>
						</tr>
						
						<!-- tr style='background-color:#1fecff;' -->
						<tr style='background: rgba(0, 0, 0, 0) url(&#39;../public/lobiadmin-master/version/1.0/ajax/img/bg/bg6.png&#39;) repeat scroll 0% 0%;'>
							<td>
								<div class='col-sm-4'>
									<div class='form-group'>
										พนักงาน
										<span class='form-control'>".(!isset($arrs["EMPNAME"]) ?"":$arrs["EMPNAME"])." ".(!isset($arrs["EMPTEL"]) ?"":$arrs["EMPTEL"])."</span>
									</div>
								</div>
								<div class='col-sm-4'>
									<div class='form-group'>
										ผู้จัดการสาขา
										<span class='form-control'>".(!isset($arrs["MNGNAME"]) ?"":$arrs["MNGNAME"])." ".(!isset($arrs["MNGTEL"]) ?"":$arrs["MNGTEL"])."</span>
									</div>
								</div>
								<div class='col-sm-4'>
									<div class='form-group'>
										ผู้อนุมัติ
										<span class='form-control'>".(!isset($arrs["APPROVENAME"]) ?"":$arrs["APPROVENAME"])." ".(!isset($arrs["APPROVETEL"]) ?"":$arrs["APPROVETEL"])." ".$arrs["ANSTATDESC"]."</span>
									</div>
								</div>
							</td>
						</tr>	
						<tr style='background: rgba(0, 0, 0, 0) url(&#39;../public/lobiadmin-master/version/1.0/ajax/img/bg/bg6.png&#39;) repeat scroll 0% 0%;'>
							<td>
								<div class='col-sm-12'>
									<div class='form-group'>
										หมายเหตุ
										<textarea class='form-control' rows=5 readonly>".(!isset($arrs["comment"]) ?"":$arrs["comment"])."</textarea>
									</div>
								</div>
							</td>
						</tr>								
					</table>
				</div>
				
				<div class='col-sm-12'><div class='row'>&emsp;</div></div>
				<div class='col-sm-10 col-sm-offset-1'>	
					<div class='row'>
						<div class='col-sm-2'>	
							<button id='back' class='btn btn-sm btn-danger btn-block'><span class='glyphicon glyphicon-step-backward'> ย้อนกลับ</span></button>							
						</div>
						
						<div class='col-sm-2 col-sm-offset-8'>	
							<button id='approve' class='btn btn-sm btn-primary btn-block'><span class='glyphicon glyphicon-thumbs-up'> อนุมัติ</span></button>							
						</div>
					</div>
				</div>
				<div class='col-sm-12'><div class='row'>&emsp;</div></div>
			</div>
		";
		
		$response = array("html"=>$html,"status"=>true);
		echo json_encode($response);
	}
	
	function loadform(){
		$html = "
			<div id='panel'>
				".$this->_formCAR()."
				".$this->_formCUS()."
				".$this->_formGRT1()."
				".$this->_formGRT2()."
				".$this->_formEMP()."
				
				<div class='row' style='padding-top:30px;padding-bottom:30px;'>
					<div class='col-sm-2 col-sm-offset-9'>	
						<!-- button id='save' class='btn-sm btn-primary btn-block'>บันทึก</button -->
						<button id='save' class='btn btn-primary btn-block'><span class='glyphicon glyphicon-floppy-disk'> บันทึก</span></button>
					</div>
				</div>
			</div>
		";
		
		$response = array("html"=>$html);
		echo json_encode($response);
	}
	
	function _formCAR(){
		$html = "
			<div class='row' style='border:1px dotted #aaa;background-color:#d5f2ba;'>
				<h3>
					<div class='col-sm-10 col-sm-offset-1 text-primary'>
						<span class='toggleData glyphicon glyphicon-minus' thisc='toggleData1' style='cursor:pointer;'>&emsp;ข้อมูลรถ</span>
					</div>
				</h3>
				<div class='toggleData1' isshow=1>
					<div class='row'>							
						<div class='col-sm-2 col-sm-offset-1'>	
							<div class='form-group'>
								สาขา
								<select id='locat' class='form-control input-sm select2'>
									<option value='".$this->sess['branch']."'>".$this->sess['branch']."</option>
								</select>
							</div>
						</div>
						<div class='col-sm-2 col-sm-offset-4'>	
							<div class='form-group'>
								กิจกรรมการขาย
								<select id='acticod' class='form-control input-sm select2'></select>
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								วันที่
								<input type='text' id='createDate' class='form-control input-sm' value='".$this->today('today')."' disabled>
							</div>
						</div>
					</div>
					<div class='row'>
						<div class='col-sm-2 col-sm-offset-1'>	
							<div class='form-group'>
								เงินดาวน์รถ
								<input type='text' id='dwnAmt' class='form-control input-sm jzAllowNumber'>
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								เงินดาวน์ ป.1 &emsp;
								<div class='radio-inline' style='margin-top:-5px'>
									<label>
										<input type='radio' class='insuranceType' name='insuranceType' value='Y' >รวม
									</label>
								</div>
								<div class='radio-inline' style='margin-top:-5px'>
									<label>
										<input type='radio' class='insuranceType' name='insuranceType' value='N' checked>แยก
									</label>
								</div>
								<input type='text' id='insuranceAmt' class='form-control input-sm jzAllowNumber'>
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								จำนวนงวด
								<input type='text' id='nopay' class='form-control input-sm jzAllowNumber' maxlength=2>
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								เลขที่บิลจอง
								<select id='resvno' class='form-control input-sm select2'></select>	
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								เงินจอง
								<input type='text' id='resvAmt' class='form-control input-sm' value='' disabled>
							</div>
						</div>
					</div>
					<div class='row'>
						<div class='col-sm-2 col-sm-offset-1'>	
							<div class='form-group'>
								เลขตัวถัง
								<select id='strno' class='form-control input-sm select2'></select>	
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								รุ่น
								<select id='model' class='form-control input-sm select2'></select>	
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								แบบ
								<select id='baab' class='form-control input-sm select2'></select>	
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								สี
								<select id='color' class='form-control input-sm select2'></select>	
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								สถานะรถ
								<input type='text' id='stat' class='form-control input-sm' value='' disabled>
							</div>
						</div>
					</div>
					<div class='row'>
						<div class='col-sm-2 col-sm-offset-1'>	
							<div class='form-group'>
								วันที่ซื้อ ก่อนยึด
								<input type='text' id='sdateold' class='form-control input-sm' value='' disabled>
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								วันที่ยึด
								<input type='text' id='ydate' class='form-control input-sm' value='' disabled>
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								ราคารถ(สด) ก่อนหักส่วนลด
								<input type='text' id='price' class='form-control input-sm jzAllowNumber' stdid='' stdplrank=''> 
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								อัตราดอกเบี้ยต่อเดือน
								<input type='text' id='interatert' class='form-control input-sm jzAllowNumber'>
							</div>
						</div>
					</div>
				</div>
			</div>
		";
		
		return $html;
	}
	
	function _formCUS(){
		$html = "
			<div class='row' style='border:1px dotted #aaa;background-color:#eff2ba;'>
				<h3>
					<div class='col-sm-10 col-sm-offset-1 text-primary'>
						<span class='toggleData glyphicon glyphicon-minus' thisc='toggleData2' style='cursor:pointer;'>&emsp;ผู้เช่าซื้อ</span>
					</div>
				</h3>
				<div class='toggleData2' isshow=1>
					<div class='row'>
						<div class='col-sm-2 col-sm-offset-1'>	
							<div class='form-group'>
								ชื่อ-สกุล ลูกค้า
								<select id='cuscod' class='form-control input-sm select2'></select>	
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								เลข ปชช.
								<input type='text' id='idno' class='form-control input-sm' value=''>
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								วันเกิด
								<input type='text' id='idnoBirth' class='form-control input-sm datepicker'>
							</div>
						</div>
						<div class='col-sm-2'>
							<div class='form-group'>
								วันหมดอายุบัตร
								<input type='text' id='idnoExpire' class='form-control input-sm datepicker'>
							</div>
						</div>
						<div class='col-sm-2'>
							<div class='row'>
								<div class='col-sm-6'>
									<div class='form-group'>
										อายุ
										<input type='text' id='idnoAge' class='form-control input-sm jzAllowNumber'>
									</div>
								</div>
								<div class='col-sm-6'>
									<div class='form-group'>
										สถานะ
										<select id='idnoStat' class='form-control input-sm select2'>
											<option value='1'>โสด</option>
											<option value='2'>สมรส</option>
											<option value='3'>หม้าย</option>
											<option value='4'>หย่า</option>
											<option value='5'>แยกกันอยู่</option>
										</select>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class='row'>
						<div class='col-sm-3 col-sm-offset-1'>	
							<div class='form-group'>
								ที่อยู่ตาม ทบ.บ้าน
								<select id='addr1' class='form-control input-sm select2'></select>	
							</div>
						</div>
						<div class='col-sm-3'>
							<div class='form-group'>
								ที่อยู่ส่งเอกสาร
								<select id='addr2' class='form-control input-sm select2'></select>	
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								เบอร์ติดต่อ
								<input type='text' id='phoneNumber' class='form-control input-sm jzAllowNumber'>
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								จำนวนบุตร
								<input type='text' id='baby' class='form-control input-sm jzAllowNumber'>
							</div>
						</div>
					</div>
					<div class='row'>
						<div class='col-sm-2 col-sm-offset-1'>	
							<div class='form-group'>
								ประกันสังคม
								<input type='text' id='socialSecurity' class='form-control input-sm'>
							</div>
						</div>
						<div class='col-sm-3'>	
							<div class='form-group'>
								อาชีพ
								<input type='text' id='career' class='form-control input-sm'>
							</div>
						</div>
						<div class='col-sm-5'>
							<div class='form-group'>
								ที่อยู๋ที่ทำงาน
								<input type='text' id='careerOffice' class='form-control input-sm'>
							</div>
						</div>
					</div>
					<div class='row'>
						<div class='col-sm-2 col-sm-offset-1'>	
							<div class='form-group'>
								เบอร์ติดต่อที่ทำงาน
								<input type='text' id='careerPhone' class='form-control input-sm jzAllowNumber' maxlength=10>
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								รายได้/เดือน
								<input type='text' id='income' class='form-control input-sm jzAllowNumber'>
							</div>
						</div>
						<div class='col-sm-3'>
							<div class='form-group'>
								ชื่อ-สกุล (เจ้าบ้านตาม ทบ.บ้าน)
								<input type='text' id='hostName' class='form-control input-sm'>
							</div>
						</div>
						<div class='col-sm-3'>
							<div class='form-group'>
								เลข ปชช.(เจ้าบ้าน)
								<input type='text' id='hostIDNo' class='form-control input-sm'>
							</div>
						</div>
					</div>
					<div class='row'>
						<div class='col-sm-2 col-sm-offset-1'>	
							<div class='form-group'>
								เบอร์ติดต่อที่ (เจ้าบ้าน)
								<input type='text' id='hostPhone' class='form-control input-sm jzAllowNumber' maxlength=10>
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								ความสัมพันธ์กับเจ้าบ้าน
								<input type='text' id='hostRelation' class='form-control input-sm'>
							</div>
						</div>
						<div class='col-sm-3'>
							<div class='form-group'>
								ความสัมพันธ์กับพนักงาน
								<select id='empRelation' class='form-control input-sm select2'></select>	
							</div>
						</div>
						<div class='col-sm-3'>
							<div class='form-group'>
								บุคคลอ้างอิง
								<input type='text' id='reference' class='form-control input-sm'>
							</div>
						</div>
					</div>
				</div>
			</div>
		";
		return $html;
	}
	
	function _formGRT1(){
		$html = "
			<div class='row' style='border:1px dotted #aaa;background-color:#f2cdba;'>
				<h3>
					<div class='col-sm-10 col-sm-offset-1 text-primary'>
						<span class='toggleData glyphicon glyphicon-plus' thisc='toggleData3' style='cursor:pointer;'>&emsp;ผู้ค้ำประกัน 1</span>
					</div>
				</h3>
				<div class='toggleData3' isshow=0 hidden>
					<div class='row'>
						<div class='col-sm-2 col-sm-offset-1'>	
							<div class='form-group'>
								ชื่อ-สกุล ลูกค้า
								<select id='is1_cuscod' class='form-control input-sm select2'></select>	
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								เลข ปชช.
								<input type='text' id='is1_idno' class='form-control input-sm' value=''>
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								วันเกิด
								<input type='text' id='is1_idnoBirth' class='form-control input-sm datepicker'>
							</div>
						</div>
						<div class='col-sm-2'>
							<div class='form-group'>
								วันหมดอายุบัตร
								<input type='text' id='is1_idnoExpire' class='form-control input-sm datepicker'>
							</div>
						</div>
						<div class='col-sm-2'>
							<div class='row'>
								<div class='col-sm-6'>
									<div class='form-group'>
										อายุ
										<input type='text' id='is1_idnoAge' class='form-control input-sm'>
									</div>
								</div>
								<div class='col-sm-6'>
									<div class='form-group'>
										สถานะ
										<select id='is1_idnoStat' class='form-control input-sm select2'>
											<option value='1'>โสด</option>
											<option value='2'>สมรส</option>
											<option value='3'>หม้าย</option>
											<option value='4'>หย่า</option>
											<option value='5'>แยกกันอยู่</option>
										</select>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class='row'>
						<div class='col-sm-3 col-sm-offset-1'>	
							<div class='form-group'>
								ที่อยู่ตาม ทบ.บ้าน
								<select id='is1_addr1' class='form-control input-sm select2'></select>	
							</div>
						</div>
						<div class='col-sm-3'>
							<div class='form-group'>
								ที่อยู่ส่งเอกสาร
								<select id='is1_addr2' class='form-control input-sm select2'></select>	
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								เบอร์ติดต่อ
								<input type='text' id='is1_phoneNumber' class='form-control input-sm jzAllowNumber'>
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								จำนวนบุตร
								<input type='text' id='is1_baby' class='form-control input-sm jzAllowNumber'>
							</div>
						</div>
					</div>
					<div class='row'>
						<div class='col-sm-2 col-sm-offset-1'>	
							<div class='form-group'>
								ประกันสังคม
								<input type='text' id='is1_socialSecurity' class='form-control input-sm'>
							</div>
						</div>
						<div class='col-sm-3'>	
							<div class='form-group'>
								อาชีพ
								<input type='text' id='is1_career' class='form-control input-sm'>
							</div>
						</div>
						<div class='col-sm-5'>
							<div class='form-group'>
								ที่อยู๋ที่ทำงาน
								<input type='text' id='is1_careerOffice' class='form-control input-sm'>
							</div>
						</div>
					</div>
					<div class='row'>
						<div class='col-sm-2 col-sm-offset-1'>	
							<div class='form-group'>
								เบอร์ติดต่อที่ทำงาน
								<input type='text' id='is1_careerPhone' class='form-control input-sm jzAllowNumber' maxlength=10>
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								รายได้/เดือน
								<input type='text' id='is1_income' class='form-control input-sm jzAllowNumber'>
							</div>
						</div>
						<div class='col-sm-3'>
							<div class='form-group'>
								ชื่อ-สกุล (เจ้าบ้านตาม ทบ.บ้าน)
								<input type='text' id='is1_hostName' class='form-control input-sm'>
							</div>
						</div>
						<div class='col-sm-3'>
							<div class='form-group'>
								เลข ปชช.(เจ้าบ้าน)
								<input type='text' id='is1_hostIDNo' class='form-control input-sm'>
							</div>
						</div>
					</div>
					<div class='row'>
						<div class='col-sm-2 col-sm-offset-1'>	
							<div class='form-group'>
								เบอร์ติดต่อที่ (เจ้าบ้าน)
								<input type='text' id='is1_hostPhone' class='form-control input-sm jzAllowNumber' maxlength=10>
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								ความสัมพันธ์กับเจ้าบ้าน
								<input type='text' id='is1_hostRelation' class='form-control input-sm'>
							</div>
						</div>
						<div class='col-sm-3'>
							<div class='form-group'>
								ความสัมพันธ์กับพนักงาน
								<select id='is1_empRelation' class='form-control input-sm select2'></select>	
							</div>
						</div>
						<div class='col-sm-3'>
							<div class='form-group'>
								บุคคลอ้างอิง
								<input type='text' id='is1_reference' class='form-control input-sm'>
							</div>
						</div>
						
						<div class='col-sm-2 col-sm-offset-1'>	
							<div class='form-group'>
								แนบรูป
								<input type='text' id='is1_reference' class='form-control input-sm'>
							</div>
						</div>
					</div>
				</div>
			</div>
		";
		
		return $html;
	}
	
	function _formGRT2(){
		$html = "
			<div class='row' style='border:1px dotted #aaa;background-color:#f2baba;'>
				<h3>
					<div class='col-sm-10 col-sm-offset-1 text-primary'>
						<span class='toggleData glyphicon glyphicon-plus' thisc='toggleData4' style='cursor:pointer;'>&emsp;ผู้ค้ำประกัน 2</span>
					</div>
				</h3>
				<div class='toggleData4' isshow=0 hidden>
					<div class='row'>
						<div class='col-sm-2 col-sm-offset-1'>	
							<div class='form-group'>
								ชื่อ-สกุล ลูกค้า
								<select id='is2_cuscod' class='form-control input-sm select2'></select>	
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								เลข ปชช.
								<input type='text' id='is2_idno' class='form-control input-sm' value=''>
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								วันเกิด
								<input type='text' id='is2_idnoBirth' class='form-control input-sm datepicker'>
							</div>
						</div>
						<div class='col-sm-2'>
							<div class='form-group'>
								วันหมดอายุบัตร
								<input type='text' id='is2_idnoExpire' class='form-control input-sm datepicker'>
							</div>
						</div>
						<div class='col-sm-2'>
							<div class='row'>
								<div class='col-sm-6'>
									<div class='form-group'>
										อายุ
										<input type='text' id='is2_idnoAge' class='form-control input-sm'>
									</div>
								</div>
								<div class='col-sm-6'>
									<div class='form-group'>
										สถานะ
										<select id='is2_idnoStat' class='form-control input-sm select2'>
											<option value='1'>โสด</option>
											<option value='2'>สมรส</option>
											<option value='3'>หม้าย</option>
											<option value='4'>หย่า</option>
											<option value='5'>แยกกันอยู่</option>
										</select>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class='row'>
						<div class='col-sm-3 col-sm-offset-1'>	
							<div class='form-group'>
								ที่อยู่ตาม ทบ.บ้าน
								<select id='is2_addr1' class='form-control input-sm select2'></select>	
							</div>
						</div>
						<div class='col-sm-3'>
							<div class='form-group'>
								ที่อยู่ส่งเอกสาร
								<select id='is2_addr2' class='form-control input-sm select2'></select>	
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								เบอร์ติดต่อ
								<input type='text' id='is2_phoneNumber' class='form-control input-sm jzAllowNumber'>
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								จำนวนบุตร
								<input type='text' id='is2_baby' class='form-control input-sm jzAllowNumber'>
							</div>
						</div>
					</div>
					<div class='row'>
						<div class='col-sm-2 col-sm-offset-1'>	
							<div class='form-group'>
								ประกันสังคม
								<input type='text' id='is2_socialSecurity' class='form-control input-sm'>
							</div>
						</div>
						<div class='col-sm-3'>	
							<div class='form-group'>
								อาชีพ
								<input type='text' id='is2_career' class='form-control input-sm'>
							</div>
						</div>
						<div class='col-sm-5'>
							<div class='form-group'>
								ที่อยู๋ที่ทำงาน
								<input type='text' id='is2_careerOffice' class='form-control input-sm'>
							</div>
						</div>
					</div>
					<div class='row'>
						<div class='col-sm-2 col-sm-offset-1'>	
							<div class='form-group'>
								เบอร์ติดต่อที่ทำงาน
								<input type='text' id='is2_careerPhone' class='form-control input-sm jzAllowNumber' maxlength=10>
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								รายได้/เดือน
								<input type='text' id='is2_income' class='form-control input-sm jzAllowNumber'>
							</div>
						</div>
						<div class='col-sm-3'>
							<div class='form-group'>
								ชื่อ-สกุล (เจ้าบ้านตาม ทบ.บ้าน)
								<input type='text' id='is2_hostName' class='form-control input-sm'>
							</div>
						</div>
						<div class='col-sm-3'>
							<div class='form-group'>
								เลข ปชช.(เจ้าบ้าน)
								<input type='text' id='is2_hostIDNo' class='form-control input-sm'>
							</div>
						</div>
					</div>
					<div class='row'>
						<div class='col-sm-2 col-sm-offset-1'>	
							<div class='form-group'>
								เบอร์ติดต่อที่ (เจ้าบ้าน)
								<input type='text' id='is2_hostPhone' class='form-control input-sm jzAllowNumber' maxlength=10>
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								ความสัมพันธ์กับเจ้าบ้าน
								<input type='text' id='is2_hostRelation' class='form-control input-sm'>
							</div>
						</div>
						<div class='col-sm-3'>
							<div class='form-group'>
								ความสัมพันธ์กับพนักงาน
								<select id='is2_empRelation' class='form-control input-sm select2'></select>	
							</div>
						</div>
						<div class='col-sm-3'>
							<div class='form-group'>
								บุคคลอ้างอิง
								<input type='text' id='is2_reference' class='form-control input-sm'>
							</div>
						</div>
					</div>
				</div>
			</div>
		";
		
		return $html;
	}
	
	function _formEMP(){
		$html = "
			<div class='row' style='border:1px dotted #aaa;background-color:#baeff2;'>
				<h3>
					<div class='col-sm-10 col-sm-offset-1 text-primary'>
						<span class='toggleData glyphicon glyphicon-minus' thisc='toggleData5' style='cursor:pointer;'>&emsp;สาขา</span>
					</div>
				</h3>
				<div class='toggleData5' isshow=1>
					<div class='row'>
						<div class='col-sm-3 col-sm-offset-1'>	
							<div class='form-group'>
								พนักงาน
								<select id='empIDNo' class='form-control input-sm select2'></select>	
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								เบอร์ติดต่อ
								<input type='text' id='empTel' class='form-control input-sm jzAllowNumber' maxlength=10>
							</div>
						</div>
						<div class='col-sm-3'>	
							<div class='form-group'>
								ผู้จัดการสาขา
								<select id='mngIDNo' class='form-control input-sm select2'></select>	
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								เบอร์ติดต่อ
								<input type='text' id='mngTel' class='form-control input-sm jzAllowNumber' maxlength=10>
							</div>
						</div>
					</div>
				</div>
			</div>
		";
		
		return $html;
	}
	
	function dataResv(){
		$response = array("html"=>"","error"=>false,"msg"=>"");
		$dwnAmt	  = str_replace(",","",$_POST["dwnAmt"]);
		$resvno   = $_POST["resvno"];
		$acticod  = $_POST["acticod"];
		
		if($dwnAmt == ""){
			$response["error"] = true;
			$response["msg"] = "ผิดพลาด :: โปรดระบุเงินดาวน์ก่อนครับ";
			echo json_encode($response); exit;
		}else if(!is_numeric($dwnAmt)){
			$response["error"] = true;
			$response["msg"] = "ผิดพลาด :: โปรดระบุเงินดาวน์ให้ถูกต้อง";
			echo json_encode($response); exit;
		}
		
		/*
		if($acticod == "ALL"){
			$response["error"] = true;
			$response["msg"] = "ผิดพลาด :: โปรดระบุกิจกรรมการขายก่อนครับ";
			echo json_encode($response); exit;
		}
		*/
		
		$sql = "
			select a.RESVNO,a.RESPAY,a.STRNO,a.MODEL,a.BAAB,a.COLOR
				,case when a.STAT='N' then 'รถใหม่'  else 'รถเก่า' end as STAT
				,a.STAT as STATEN
				,convert(varchar(8),a.RESVDT,112) as RESVDT
				,convert(varchar(8),b.SDATE,112) as SDATE
				,convert(varchar(8),b.YDATE,112) as YDATE
				,a.CUSCOD
				,c.SNAM+c.NAME1+' '+c.NAME2+' ('+c.CUSCOD+')-'+c.GRADE as CUSNAME
				,c.IDNO
				,convert(varchar(8),c.BIRTHDT,112) as BIRTHDT
				,convert(varchar(8),c.EXPDT,112) as EXPDT
				,DATEDIFF(YEAR,c.BIRTHDT,GETDATE()) as AGE
				,c.ADDRNO
				,'('+c.ADDRNO+') '+d.ADDR1+' '+d.ADDR2+' ต.'+d.TUMB+' อ.'+e.AUMPDES+' จ.'+f.PROVDES+' '+d.ZIP as ADDR
				,c.MOBILENO
				,isnull(c.MREVENU,0) as MREVENU
				,g.ACTICOD
				,'('+g.ACTICOD+') '+h.ACTIDES collate thai_cs_as as ACTIDES
				,case when i.price is null then a.PRICE else i.price end as price
				,isnull(g.STDID,'') as stdid
				,isnull(cast(g.STDPLRANK as varchar),'') as stdplrank
				,a.RESPAY - (isnull(a.SMPAY,0) + isnull(a.SMCHQ,0)) as BALANCE
			from {$this->MAuth->getdb('ARRESV')} a 
			left join (
				select ROW_NUMBER() over(partition by STRNO order by STRNO,sdate desc) r,* 
				from {$this->MAuth->getdb('ARHOLD')}
			) as b on a.STRNO=b.STRNO and b.r=1
			left join {$this->MAuth->getdb('CUSTMAST')} c on a.CUSCOD=c.CUSCOD
			left join {$this->MAuth->getdb('CUSTADDR')} d on c.CUSCOD=d.CUSCOD and c.ADDRNO=d.ADDRNO
			left join {$this->MAuth->getdb('SETAUMP')} e on d.AUMPCOD=e.AUMPCOD
			left join {$this->MAuth->getdb('SETPROV')} f on e.PROVCOD=f.PROVCOD
			left join {$this->MAuth->getdb('ARRESVOTH')} g on a.RESVNO=g.RESVNO collate thai_cs_as
			left join {$this->MAuth->getdb('SETACTI')} h on g.ACTICOD=h.ACTICOD collate thai_cs_as
			left join {$this->MAuth->getdb('std_pricelist')} i on g.STDID=i.id and g.STDPLRANK=i.plrank 
			where a.RESVNO='".$resvno."'
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$data = array();
		if($query->row()){
			foreach($query->result() as $row){
				foreach($row as $key => $val){
					switch($key){
						case 'SDATE': $data[$key] = $this->Convertdate(2,$val); break;
						case 'YDATE': $data[$key] = $this->Convertdate(2,$val); break;
						case 'BIRTHDT': $data[$key] = $this->Convertdate(2,$val); break;
						case 'EXPDT': $data[$key] = $this->Convertdate(2,$val); break;
						case 'MREVENU': $data[$key] = number_format($val,2); break;
						case 'ACTICOD': $data[$key] = str_replace(chr(0),"",$val); break;
						case 'ACTIDES': $data[$key] = str_replace(chr(0),"",$val); break;
						default:  $data[$key] = $val; break;
					}
				}
			}
		}
		
		if($data["BALANCE"] > 0)
		{
			$response["error"] = true;
			$response["msg"] = "ผิดพลาด :: โปรดเลขที่บิลจอง ".$data["RESVNO"]." ยังไม่ได้ชำระค่าจองทีครับ";
			echo json_encode($response); exit;	
		}	
	
		if($data["STATEN"] == "N" and ($data["stdid"] == "" or $data["stdplrank"] == "")){
			$sql = "
				if exists(
					select * from {$this->MAuth->getdb('std_vehicles')} a
					left join {$this->MAuth->getdb('std_pricelist')} b on a.id=b.id and '".$data["RESVDT"]."' between event_s and isnull(event_e,GETDATE())
					where a.model='{$data["MODEL"]}' and a.baab='{$data["BAAB"]}' and a.color='{$data["COLOR"]}' and b.ACTICOD='{$acticod}'
				)
				begin 
					select * from {$this->MAuth->getdb('std_vehicles')} a
					left join {$this->MAuth->getdb('std_pricelist')} b on a.id=b.id and '".$data["RESVDT"]."' between event_s and isnull(event_e,GETDATE())
					where a.model='{$data["MODEL"]}' and a.baab='{$data["BAAB"]}' and a.color='{$data["COLOR"]}' and b.ACTICOD='{$acticod}'
				end 
				else if exists(
					select * from {$this->MAuth->getdb('std_vehicles')} a
					left join {$this->MAuth->getdb('std_pricelist')} b on a.id=b.id and '".$data["RESVDT"]."' between event_s and isnull(event_e,GETDATE())
					where a.model='{$data["MODEL"]}' and a.baab='{$data["BAAB"]}' and a.color='{$data["COLOR"]}' and b.ACTICOD='ALL'
				)
				begin 
					select * from {$this->MAuth->getdb('std_vehicles')} a
					left join {$this->MAuth->getdb('std_pricelist')} b on a.id=b.id and '".$data["RESVDT"]."' between event_s and isnull(event_e,GETDATE())
					where a.model='{$data["MODEL"]}' and a.baab='{$data["BAAB"]}' and a.color='{$data["COLOR"]}' and b.ACTICOD='ALL'
				end 
				else if exists(
					select * from {$this->MAuth->getdb('std_vehicles')} a
					left join {$this->MAuth->getdb('std_pricelist')} b on a.id=b.id and '".$data["RESVDT"]."' between event_s and isnull(event_e,GETDATE())
					where a.model='{$data["MODEL"]}' and a.baab='{$data["BAAB"]}' and a.color='ALL' and b.ACTICOD='{$acticod}'
				)
				begin 
					select * from {$this->MAuth->getdb('std_vehicles')} a
					left join {$this->MAuth->getdb('std_pricelist')} b on a.id=b.id and '".$data["RESVDT"]."' between event_s and isnull(event_e,GETDATE())
					where a.model='{$data["MODEL"]}' and a.baab='{$data["BAAB"]}' and a.color='ALL' and b.ACTICOD='{$acticod}'
				end 
				else if exists(
					select * from {$this->MAuth->getdb('std_vehicles')} a
					left join {$this->MAuth->getdb('std_pricelist')} b on a.id=b.id and '".$data["RESVDT"]."' between event_s and isnull(event_e,GETDATE())
					where a.model='{$data["MODEL"]}' and a.baab='{$data["BAAB"]}' and a.color='ALL' and b.ACTICOD='ALL'
				)
				begin 
					select * from {$this->MAuth->getdb('std_vehicles')} a
					left join {$this->MAuth->getdb('std_pricelist')} b on a.id=b.id and '".$data["RESVDT"]."' between event_s and isnull(event_e,GETDATE())
					where a.model='{$data["MODEL"]}' and a.baab='{$data["BAAB"]}' and a.color='ALL' and b.ACTICOD='ALL'
				end 
				else if exists(
					select * from {$this->MAuth->getdb('std_vehicles')} a
					left join {$this->MAuth->getdb('std_pricelist')} b on a.id=b.id and '".$data["RESVDT"]."' between event_s and isnull(event_e,GETDATE())
					where a.model='{$data["MODEL"]}' and a.baab='ALL' and a.color='ALL' and b.ACTICOD='{$acticod}'
				)
				begin 
					select * from {$this->MAuth->getdb('std_vehicles')} a
					left join {$this->MAuth->getdb('std_pricelist')} b on a.id=b.id and '".$data["RESVDT"]."' between event_s and isnull(event_e,GETDATE())
					where a.model='{$data["MODEL"]}' and a.baab='ALL' and a.color='ALL' and b.ACTICOD='{$acticod}'
				end
				else if exists(
					select * from {$this->MAuth->getdb('std_vehicles')} a
					left join {$this->MAuth->getdb('std_pricelist')} b on a.id=b.id and '".$data["RESVDT"]."' between event_s and isnull(event_e,GETDATE())
					where a.model='{$data["MODEL"]}' and a.baab='ALL' and a.color='ALL' and b.ACTICOD='ALL'
				)
				begin 
					select * from {$this->MAuth->getdb('std_vehicles')} a
					left join {$this->MAuth->getdb('std_pricelist')} b on a.id=b.id and '".$data["RESVDT"]."' between event_s and isnull(event_e,GETDATE())
					where a.model='{$data["MODEL"]}' and a.baab='ALL' and a.color='ALL' and b.ACTICOD='ALL'
				end
			";
			$query = $this->db->query($sql);
			
			if($query->row()){
				foreach($query->result() as $row){
					$data["stdid"] 		= $row->id;
					$data["stdplrank"] 	= $row->plrank;
					$data["price"] 		= $row->price;
				}
			}else{
				$response["error"] = true;
				$response["msg"] = "
					ผิดพลาด ไม่พบราคาขายรถใหม่ โปรดติดต่อฝ่ายเช่าซื้อ/ฝ่ายวิเคราะห์ เพื่อกำหนดราคาขายก่อนครับ<br><br>
					รุ่น :: ".$data["MODEL"]."<br>
					แบบ :: ".$data["BAAB"]."<br>
					สี :: ".$data["COLOR"]."<br>
					วันที่จอง :: ".$this->Convertdate(2,$data["RESVDT"])."
				";
			}
		}
		
		$data["interest_rate"]  = 0;
		$data["interest_rate2"] = 0;
		if($data["STATEN"] == "N"){
			$sql = "
				select * from {$this->MAuth->getdb('std_down')} a
				where id='".$data["stdid"]."' and plrank='".$data["stdplrank"]."' and '".$dwnAmt."' between dwnrate_s and isnull(dwnrate_e,'".$data["price"]."')
			";
			$query = $this->db->query($sql);
			
			if($query->row()){
				foreach($query->result() as $row){
					$data["interest_rate"] 	= $row->interest_rate;
					$data["interest_rate2"]	= $row->interest_rate2;
				}
			}else{
				$response["error"] = true;
				$response["msg"] = "
					ผิดพลาด ไม่พบขั้นเงินดาวน์ที่ระบุมา โปรดตรวจสอบข้อมูลใหม่อีกครั้ง<br><br>
					รุ่น :: ".$data["MODEL"]."<br>
					แบบ :: ".$data["BAAB"]."<br>
					สี :: ".$data["COLOR"]."<br>
					วันที่จอง :: ".$this->Convertdate(2,$data["RESVDT"])."
				";
			}
		}
		
		$response["html"] = $data;
		echo json_encode($response);
	}
	
	function dataSTR(){
		$response   = array("html"=>"","error"=>false,"msg"=>"");
		$dwnAmt	    = $_POST["dwnAmt"];
		$createDate = $this->Convertdate(1,$_POST["createDate"]);
		$strno 	    = $_POST["strno"];
		$acticod    = $_POST["acticod"];
		
		if($acticod == "ALL"){
			$response["error"] = true;
			$response["msg"] = "ผิดพลาด :: โปรดระบุกิจกรรมการขายก่อนครับ";
			echo json_encode($response); exit;
		}
		
		$sql = "
			select a.STRNO,a.MODEL,a.BAAB,a.COLOR
				,case when a.STAT='N' then 'รถใหม่'  else 'รถเก่า' end as STAT
				,a.STAT as STATEN
				,convert(varchar(8),b.SDATE,112) as SDATE
				,convert(varchar(8),b.YDATE,112) as YDATE
				,a.CRLOCAT
			from {$this->MAuth->getdb('INVTRAN')} a
			left join (
				select ROW_NUMBER() over(partition by STRNO order by STRNO,sdate desc) r,* 
				from {$this->MAuth->getdb('ARHOLD')}
			) as b on a.STRNO=b.STRNO and b.r=1
			where a.STRNO='".$strno."'
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$data = array();
		if($query->row()){
			foreach($query->result() as $row){
				foreach($row as $key => $val){
					switch($key){
						case 'SDATE': $data[$key] = $this->Convertdate(2,$val); break;
						case 'YDATE': $data[$key] = $this->Convertdate(2,$val); break;
						default:  $data[$key] = $val; break;
					}
				}
			}
		}
		
		$data["interest_rate"] 	= "";
		$data["interest_rate2"]	= "";
		if($data["STATEN"] == "N"){
			$sql = "
				if exists(
					select * from {$this->MAuth->getdb('std_vehicles')} a
					left join {$this->MAuth->getdb('std_pricelist')} b on a.id=b.id and '".$createDate."' between event_s and isnull(event_e,GETDATE())
					where a.model='{$data["MODEL"]}' and a.baab='{$data["BAAB"]}' and a.color='{$data["COLOR"]}' and b.ACTICOD='{$acticod}'
				)
				begin 
					select * from {$this->MAuth->getdb('std_vehicles')} a
					left join {$this->MAuth->getdb('std_pricelist')} b on a.id=b.id and '".$createDate."' between event_s and isnull(event_e,GETDATE())
					where a.model='{$data["MODEL"]}' and a.baab='{$data["BAAB"]}' and a.color='{$data["COLOR"]}' and b.ACTICOD='{$acticod}'
				end 
				else if exists(
					select * from {$this->MAuth->getdb('std_vehicles')} a
					left join {$this->MAuth->getdb('std_pricelist')} b on a.id=b.id and '".$createDate."' between event_s and isnull(event_e,GETDATE())
					where a.model='{$data["MODEL"]}' and a.baab='{$data["BAAB"]}' and a.color='{$data["COLOR"]}' and b.ACTICOD='ALL'
				)
				begin 
					select * from {$this->MAuth->getdb('std_vehicles')} a
					left join {$this->MAuth->getdb('std_pricelist')} b on a.id=b.id and '".$createDate."' between event_s and isnull(event_e,GETDATE())
					where a.model='{$data["MODEL"]}' and a.baab='{$data["BAAB"]}' and a.color='{$data["COLOR"]}' and b.ACTICOD='ALL'
				end 
				else if exists(
					select * from {$this->MAuth->getdb('std_vehicles')} a
					left join {$this->MAuth->getdb('std_pricelist')} b on a.id=b.id and '".$createDate."' between event_s and isnull(event_e,GETDATE())
					where a.model='{$data["MODEL"]}' and a.baab='{$data["BAAB"]}' and a.color='ALL' and b.ACTICOD='{$acticod}'
				)
				begin 
					select * from {$this->MAuth->getdb('std_vehicles')} a
					left join {$this->MAuth->getdb('std_pricelist')} b on a.id=b.id and '".$createDate."' between event_s and isnull(event_e,GETDATE())
					where a.model='{$data["MODEL"]}' and a.baab='{$data["BAAB"]}' and a.color='ALL' and b.ACTICOD='{$acticod}'
				end 
				else if exists(
					select * from {$this->MAuth->getdb('std_vehicles')} a
					left join {$this->MAuth->getdb('std_pricelist')} b on a.id=b.id and '".$createDate."' between event_s and isnull(event_e,GETDATE())
					where a.model='{$data["MODEL"]}' and a.baab='{$data["BAAB"]}' and a.color='ALL' and b.ACTICOD='ALL'
				)
				begin 
					select * from {$this->MAuth->getdb('std_vehicles')} a
					left join {$this->MAuth->getdb('std_pricelist')} b on a.id=b.id and '".$createDate."' between event_s and isnull(event_e,GETDATE())
					where a.model='{$data["MODEL"]}' and a.baab='{$data["BAAB"]}' and a.color='ALL' and b.ACTICOD='ALL'
				end 
				else if exists(
					select * from {$this->MAuth->getdb('std_vehicles')} a
					left join {$this->MAuth->getdb('std_pricelist')} b on a.id=b.id and '".$createDate."' between event_s and isnull(event_e,GETDATE())
					where a.model='{$data["MODEL"]}' and a.baab='ALL' and a.color='ALL' and b.ACTICOD='{$acticod}'
				)
				begin 
					select * from {$this->MAuth->getdb('std_vehicles')} a
					left join {$this->MAuth->getdb('std_pricelist')} b on a.id=b.id and '".$createDate."' between event_s and isnull(event_e,GETDATE())
					where a.model='{$data["MODEL"]}' and a.baab='ALL' and a.color='ALL' and b.ACTICOD='{$acticod}'
				end
				else if exists(
					select * from {$this->MAuth->getdb('std_vehicles')} a
					left join {$this->MAuth->getdb('std_pricelist')} b on a.id=b.id and '".$createDate."' between event_s and isnull(event_e,GETDATE())
					where a.model='{$data["MODEL"]}' and a.baab='ALL' and a.color='ALL' and b.ACTICOD='ALL'
				)
				begin 
					select * from {$this->MAuth->getdb('std_vehicles')} a
					left join {$this->MAuth->getdb('std_pricelist')} b on a.id=b.id and '".$createDate."' between event_s and isnull(event_e,GETDATE())
					where a.model='{$data["MODEL"]}' and a.baab='ALL' and a.color='ALL' and b.ACTICOD='ALL'
				end
			";
			//echo $sql; exit;
			$query = $this->db->query($sql);
			
			if($query->row()){
				foreach($query->result() as $row){
					$data["stdid"] = $row->id;
					$data["stdplrank"] = $row->plrank;
					$data["price"] = $row->price;
				}
			}else{
				$response["error"] = true;
				$response["msg"] = "
					ผิดพลาด ไม่พบราคาขายรถใหม่ โปรดติดต่อฝ่ายเช่าซื้อ/ฝ่ายวิเคราะห์ เพื่อกำหนดราคาขายก่อนครับ<br><br>
					รุ่น :: ".$data["MODEL"]."<br>
					แบบ :: ".$data["BAAB"]."<br>
					สี :: ".$data["COLOR"]."<br>
					วันที่ขออนุมัติ :: ".$this->Convertdate(2,$createDate)."
				";
				echo json_encode($response); exit;
			}
			
			$sql = "
				select * from {$this->MAuth->getdb('std_down')} a
				where id='".$data["stdid"]."' and plrank='".$data["stdplrank"]."' and '".$dwnAmt."' between dwnrate_s and isnull(dwnrate_e,'".$data["price"]."')
			";
			$query = $this->db->query($sql);
			
			if($query->row()){
				foreach($query->result() as $row){
					$data["interest_rate"] 	= $row->interest_rate;
					$data["interest_rate2"]	= $row->interest_rate2;
				}
			}else{
				$response["error"] = true;
				$response["msg"] = "
					ผิดพลาด ไม่พบขั้นเงินดาวน์ที่ระบุมา โปรดตรวจสอบข้อมูลใหม่อีกครั้ง<br><br>
					รุ่น :: ".$data["MODEL"]."<br>
					แบบ :: ".$data["BAAB"]."<br>
					สี :: ".$data["COLOR"]."<br>
					วันที่ขออนุมัติ :: ".$this->Convertdate(2,$createDate)."
				";
				
				echo json_encode($response); exit;
			}
		}
		
		$response["html"] = $data;
		echo json_encode($response);
	}
	
	function dataCUS(){
		$cuscod = $_POST["cuscod"];
		
		$sql = "
			select a.CUSCOD
				,a.SNAM+a.NAME1+' '+a.NAME2+' ('+a.CUSCOD+')-'+a.GRADE as CUSNAME
				,a.IDNO
				,convert(varchar(8),a.BIRTHDT,112) as BIRTHDT
				,convert(varchar(8),a.EXPDT,112) as EXPDT
				,DATEDIFF(YEAR,a.BIRTHDT,GETDATE()) as AGE
				,a.ADDRNO
				,'('+a.ADDRNO+') '+b.ADDR1+' '+b.ADDR2+' ต.'+b.TUMB+' อ.'+c.AUMPDES+' จ.'+d.PROVDES+' '+b.ZIP as ADDR
				,a.MOBILENO
				,isnull(a.MREVENU,0) as MREVENU
				,a.OCCUP
				,a.OFFIC
				,a.GRADE
			from {$this->MAuth->getdb('CUSTMAST')} a 
			left join {$this->MAuth->getdb('CUSTADDR')} b on a.CUSCOD=b.CUSCOD and a.ADDRNO=b.ADDRNO
			left join {$this->MAuth->getdb('SETAUMP')} c on b.AUMPCOD=c.AUMPCOD
			left join {$this->MAuth->getdb('SETPROV')} d on c.PROVCOD=d.PROVCOD
			where a.CUSCOD='".$cuscod."'
		";
		$query = $this->db->query($sql);
		
		$data = array();
		if($query->row()){
			foreach($query->result() as $row){
				foreach($row as $key => $val){
					switch($key){
						case 'BIRTHDT': $data[$key] = $this->Convertdate(2,$val); break;
						case 'EXPDT': $data[$key] = $this->Convertdate(2,$val); break;
						default:  $data[$key] = $val; break;
					}
				}
			}
		}
		
		$sql = "
			select (
				select count(*) r from {$this->MAuth->getdb('ARMAST')}
				where CUSCOD='".$cuscod."' and SDATE between convert(varchar(8),dateadd(day,-7,getdate()),112) and convert(varchar(8),getdate(),112)
			) as ARM
			,(
				select count(*) r from {$this->MAuth->getdb('ARRESV')}
				where CUSCOD='".$cuscod."' and SDATE is null and RESVDT between convert(varchar(8),dateadd(day,-7,getdate()),112) and convert(varchar(8),getdate(),112)					
			) as ARR
		";
		$query = $this->db->query($sql);
		
		if($query->row()){
			foreach($query->result() as $row){
				foreach($row as $key => $val){
					switch($key){
						default:  $data[$key] = $val; break;
					}
				}
			}
		}
		
		
		$response = array("html"=>$data);
		echo json_encode($response);
	}
	
	function save(){
		$this->saveCheck();
		$response = array("error"=>false,"msg"=>array());
	
		$arrs = array();
		$arrs["locat"] 		= "'".$_POST["locat"]."'";
		$arrs["resvno"] 	= ($_POST["resvno"] == "" ? "NULL":"'".$_POST["resvno"]."'");
		$arrs["resvAmt"] 	= ($_POST["resvAmt"] == "" ? "NULL":"'".$_POST["resvAmt"]."'");
		$arrs["dwnAmt"] 	= ($_POST["dwnAmt"] == "" ? "NULL":"'".$_POST["dwnAmt"]."'");
		$arrs["insuranceType"] = ($_POST["insuranceType"] == "" ? "NULL":"'".$_POST["insuranceType"]."'");
		$arrs["insuranceAmt"] = ($_POST["insuranceAmt"] == "" ? "NULL":"'".$_POST["insuranceAmt"]."'");
		$arrs["nopay"] 		= "'".$_POST["nopay"]."'";
		$arrs["strno"] 		= "'".$_POST["strno"]."'";
		$arrs["model"]		= "'".$_POST["model"]."'";
		$arrs["baab"] 		= "'".$_POST["baab"]."'";
		$arrs["color"] 		= "'".$_POST["color"]."'";
		$arrs["stat"] 		= "'".$_POST["stat"]."'";
		$arrs["sdateold"] 	= ($_POST["sdateold"] == "" ? "NULL":"'".$this->Convertdate(1,$_POST["sdateold"])."'");
		$arrs["ydate"] 		= ($_POST["ydate"] == "" ? "NULL":"'".$this->Convertdate(1,$_POST["ydate"])."'");
		$arrs["price"] 		= "'".$_POST["price"]."'";
		$arrs["stdid"] 		= "'".$_POST["stdid"]."'";
		$arrs["stdplrank"]	= "'".$_POST["stdplrank"]."'";
		$arrs["interatert"]	= "'".$_POST["interatert"]."'";
		
		$arrs["cuscod"] 	= "'".$_POST["cuscod"]."'";
		$arrs["idno"] 		= "'".$_POST["idno"]."'";
		$arrs["idnoBirth"] 	= ($_POST["idnoBirth"] == "" ? "NULL":"'".$_POST["idnoBirth"]."'");
		$arrs["idnoExpire"] = ($_POST["idnoExpire"] == "" ? "NULL":"'".$_POST["idnoExpire"]."'");
		$arrs["idnoAge"] 	= "'".$_POST["idnoAge"]."'";
		$arrs["idnoStat"] 	= "'".$_POST["idnoStat"]."'";
		$arrs["addr1"] 		= "'".$_POST["addr1"]."'";
		$arrs["addr2"] 		= "'".$_POST["addr2"]."'";
		$arrs["phoneNumber"] 	= "'".$_POST["phoneNumber"]."'";
		$arrs["baby"] 			= "'".$_POST["baby"]."'";		
		$arrs["socialSecurity"] = "'".$_POST["socialSecurity"]."'";
		$arrs["career"] 		= "'".$_POST["career"]."'";
		$arrs["careerOffice"] 	= "'".$_POST["careerOffice"]."'";
		$arrs["careerPhone"] 	= "'".$_POST["careerPhone"]."'";
		$arrs["income"] 		= "'".str_replace(",","",$_POST["income"])."'";
		$arrs["hostName"] 		= "'".$_POST["hostName"]."'";
		$arrs["hostIDNo"] 		= "'".$_POST["hostIDNo"]."'";
		$arrs["hostPhone"] 		= "'".$_POST["hostPhone"]."'";
		$arrs["hostRelation"] 	= "'".$_POST["hostRelation"]."'";
		$arrs["empRelation"] 	= "'".$_POST["empRelation"]."'";
		$arrs["reference"] 		= "'".$_POST["reference"]."'";
		
		$arrs["is1_cuscod"] 	= "'".$_POST["is1_cuscod"]."'";
		$arrs["is1_idno"] 		= "'".$_POST["is1_idno"]."'";
		$arrs["is1_idnoBirth"] 	= ($_POST["is1_idnoBirth"] == "" ? "NULL":"'".$_POST["is1_idnoBirth"]."'");
		$arrs["is1_idnoExpire"] = ($_POST["is1_idnoExpire"] == "" ? "NULL":"'".$_POST["is1_idnoExpire"]."'");
		$arrs["is1_idnoAge"] 	= "'".$_POST["is1_idnoAge"]."'";
		$arrs["is1_idnoStat"] 	= "'".$_POST["is1_idnoStat"]."'";
		$arrs["is1_addr1"] 		= "'".$_POST["is1_addr1"]."'";
		$arrs["is1_addr2"] 		= "'".$_POST["is1_addr2"]."'";
		$arrs["is1_phoneNumber"] 	= "'".$_POST["is1_phoneNumber"]."'";
		$arrs["is1_baby"] 			= "'".$_POST["is1_baby"]."'";		
		$arrs["is1_socialSecurity"] = "'".$_POST["is1_socialSecurity"]."'";
		$arrs["is1_career"] 		= "'".$_POST["is1_career"]."'";
		$arrs["is1_careerOffice"] 	= "'".$_POST["is1_careerOffice"]."'";
		$arrs["is1_careerPhone"] 	= "'".$_POST["is1_careerPhone"]."'";
		$arrs["is1_income"] 		= "'".str_replace(",","",$_POST["is1_income"])."'";
		$arrs["is1_hostName"] 		= "'".$_POST["is1_hostName"]."'";
		$arrs["is1_hostIDNo"] 		= "'".$_POST["is1_hostIDNo"]."'";
		$arrs["is1_hostPhone"] 		= "'".$_POST["is1_hostPhone"]."'";
		$arrs["is1_hostRelation"] 	= "'".$_POST["is1_hostRelation"]."'";
		$arrs["is1_empRelation"] 	= "'".$_POST["is1_empRelation"]."'";
		$arrs["is1_reference"] 		= "'".$_POST["is1_reference"]."'";
		
		$arrs["is2_cuscod"] 	= "'".$_POST["is2_cuscod"]."'";
		$arrs["is2_idno"] 		= "'".$_POST["is2_idno"]."'";
		$arrs["is2_idnoBirth"] 	= ($_POST["is2_idnoBirth"] == "" ? "NULL":"'".$_POST["is2_idnoBirth"]."'");
		$arrs["is2_idnoExpire"] = ($_POST["is2_idnoExpire"] == "" ? "NULL":"'".$_POST["is2_idnoExpire"]."'");
		$arrs["is2_idnoAge"] 	= "'".$_POST["is2_idnoAge"]."'";
		$arrs["is2_idnoStat"] 	= "'".$_POST["is2_idnoStat"]."'";
		$arrs["is2_addr1"] 		= "'".$_POST["is2_addr1"]."'";
		$arrs["is2_addr2"] 		= "'".$_POST["is2_addr2"]."'";
		$arrs["is2_phoneNumber"] 	= "'".$_POST["is2_phoneNumber"]."'";
		$arrs["is2_baby"] 			= "'".$_POST["is2_baby"]."'";		
		$arrs["is2_socialSecurity"] = "'".$_POST["is2_socialSecurity"]."'";
		$arrs["is2_career"] 		= "'".$_POST["is2_career"]."'";
		$arrs["is2_careerOffice"] 	= "'".$_POST["is2_careerOffice"]."'";
		$arrs["is2_careerPhone"] 	= "'".$_POST["is2_careerPhone"]."'";
		$arrs["is2_income"] 		= "'".str_replace(",","",$_POST["is2_income"])."'";
		$arrs["is2_hostName"] 		= "'".$_POST["is2_hostName"]."'";
		$arrs["is2_hostIDNo"] 		= "'".$_POST["is2_hostIDNo"]."'";
		$arrs["is2_hostPhone"] 		= "'".$_POST["is2_hostPhone"]."'";
		$arrs["is2_hostRelation"] 	= "'".$_POST["is2_hostRelation"]."'";
		$arrs["is2_empRelation"] 	= "'".$_POST["is2_empRelation"]."'";
		$arrs["is2_reference"] 		= "'".$_POST["is2_reference"]."'";
		
		$arrs["empIDNo"] 	= "'".$_POST["empIDNo"]."'";
		$arrs["empTel"] 	= "'".$_POST["empTel"]."'";
		$arrs["mngIDNo"] 	= "'".$_POST["mngIDNo"]."'";
		$arrs["mngTel"] 	= "'".$_POST["mngTel"]."'";
				
		$sql = "
			if object_id('tempdb..#transaction') is not null drop table #transaction;
			create table #transaction (error varchar(1),id varchar(12),msg varchar(max));

			begin tran ins
			begin try
				/* @symbol = สัญลักษณ์แทนประเภทของเลขที่ นั้นๆ */
				declare @symbol varchar(10) = (select H_ANALYZE from {$this->MAuth->getdb('CONDPAY')});
				/* @rec = รหัสพื้นฐาน */
				declare @rec varchar(10) = (select SHORTL+@symbol+'-'+right(left(convert(varchar(8),GETDATE(),112),6),4) from {$this->MAuth->getdb('INVLOCAT')} where LOCATCD=".$arrs['locat'].");
				/* @ANID = รหัสที่จะใช้ */
				declare @ANID varchar(12) = (select isnull(MAX(ID),@rec+'0000') from ( 
					select ID collate Thai_CS_AS as ID from {$this->MAuth->getdb('ARANALYZE')} where ID like ''+@rec+'%' collate thai_cs_as
				) as a);
				set @ANID = left(@ANID ,8)+right(right(@ANID ,4)+10001,4);
				
				
				declare @id bigint;
				insert into {$this->MAuth->getdb('ARANALYZE')} (
					ID,LOCAT,RESVNO,RESVAMT,DWN,INSURANCE_TYP,DWN_INSURANCE,INTEREST_RT,NOPAY,STRNO,MODEL
					,BAAB,COLOR,STAT,SDATE,YDATE,PRICE,ANSTAT,STDID,STDPLRANK,INSBY,INSDT
				) 
				select @ANID,".$arrs["locat"].",".$arrs["resvno"].",".$arrs["resvAmt"].",".$arrs["dwnAmt"].",".$arrs["insuranceType"]."
					,".$arrs["insuranceAmt"].",".$arrs["interatert"].",".$arrs["nopay"].",".$arrs["strno"].",".$arrs["model"]."
					,".$arrs["baab"].",".$arrs["color"].",".$arrs["stat"].",".$arrs["sdateold"].",".$arrs["ydate"]."
					,".$arrs["price"].",'I',".$arrs["stdid"].",".$arrs["stdplrank"].",'".$this->sess["IDNo"]."',getdate();
				
				insert into {$this->MAuth->getdb('ARANALYZEREF')} (
					ID,CUSCOD,CUSTYPE,CUSSTAT,CUSBABY,ADDRNO,ADDRDOCNO,SOCAILSECURITY,CAREER,CAREERADDR,
					CAREERTEL,HOSTNAME,HOSTIDNO,HOSTTEL,HOSTRELATION,EMPRELATION,REFERANT
				)
				select @ANID,".$arrs["cuscod"].",0,".$arrs["idnoStat"].",".$arrs["baby"].",".$arrs["addr1"]."
					,".$arrs["addr2"].",".$arrs["socialSecurity"].",".$arrs["career"].",".$arrs["careerOffice"]."
					,".$arrs["careerPhone"].",".$arrs["hostName"].",".$arrs["hostIDNo"]."
					,".$arrs["hostPhone"].",".$arrs["hostRelation"].",".$arrs["empRelation"]."
					,".$arrs["reference"]."
				union all
				select @ANID,".$arrs["is1_cuscod"].",1,".$arrs["is1_idnoStat"].",".$arrs["is1_baby"].",".$arrs["is1_addr1"]."
					,".$arrs["is1_addr2"].",".$arrs["is1_socialSecurity"].",".$arrs["is1_career"].",".$arrs["is1_careerOffice"]."
					,".$arrs["is1_careerPhone"].",".$arrs["is1_hostName"].",".$arrs["is1_hostIDNo"]."
					,".$arrs["is1_hostPhone"].",".$arrs["is1_hostRelation"].",".$arrs["is1_empRelation"]."
					,".$arrs["is1_reference"]."
				
				update {$this->MAuth->getdb('CUSTMAST')}
				set MOBILENO=".$arrs["phoneNumber"]."
					,OCCUP=".$arrs["career"]."
					,OFFIC=".$arrs["careerOffice"]."
					,AGE=".$arrs["idnoAge"]."
					,MREVENU=".$arrs["income"]."
				where CUSCOD=".$arrs["cuscod"]."
				
				update {$this->MAuth->getdb('CUSTMAST')}
				set MOBILENO=".$arrs["phoneNumber"]."
					,OCCUP=".$arrs["career"]."
					,OFFIC=".$arrs["careerOffice"]."
					,AGE=".$arrs["idnoAge"]."
					,MREVENU=".$arrs["income"]."
				where CUSCOD=".$arrs["cuscod"]."
				update {$this->MAuth->getdb('CUSTMAST')}
				set MOBILENO=".$arrs["is1_phoneNumber"]."
					,OCCUP=".$arrs["is1_career"]."
					,OFFIC=".$arrs["is1_careerOffice"]."
					,AGE=".$arrs["is1_idnoAge"]."
					,MREVENU=".$arrs["is1_income"]."
				where CUSCOD=".$arrs["is1_cuscod"]."
				
				if(".$arrs["is2_cuscod"]." <> '')
				begin 
					insert into {$this->MAuth->getdb('ARANALYZEREF')} (
						ID,CUSCOD,CUSTYPE,CUSSTAT,CUSBABY,ADDRNO,ADDRDOCNO,SOCAILSECURITY,CAREER,CAREERADDR,
						CAREERTEL,HOSTNAME,HOSTIDNO,HOSTTEL,HOSTRELATION,EMPRELATION,REFERANT
					)
					select @ANID,".$arrs["is2_cuscod"].",2,".$arrs["is2_idnoStat"].",".$arrs["is2_baby"].",".$arrs["is2_addr1"]."
						,".$arrs["is2_addr2"].",".$arrs["is2_socialSecurity"].",".$arrs["is2_career"].",".$arrs["is2_careerOffice"]."
						,".$arrs["is2_careerPhone"].",".$arrs["is2_hostName"].",".$arrs["is2_hostIDNo"]."
						,".$arrs["is2_hostPhone"].",".$arrs["is2_hostRelation"].",".$arrs["is2_empRelation"]."
						,".$arrs["is2_reference"].";
						
					update {$this->MAuth->getdb('CUSTMAST')}
					set MOBILENO=".$arrs["is2_phoneNumber"]."
						,OCCUP=".$arrs["is2_career"]."
						,OFFIC=".$arrs["is2_careerOffice"]."
						,AGE=".$arrs["is2_idnoAge"]."
						,MREVENU=".$arrs["is2_income"]."
					where CUSCOD=".$arrs["is2_cuscod"]."
				end			
				
				if exists(select * from {$this->MAuth->getdb('ARANALYZEDATA')} where ID=@ANID)
				begin 
					update {$this->MAuth->getdb('ARANALYZEDATA')}
					set EMP=".$arrs["empIDNo"]."
						,EMPTEL=".$arrs["empTel"]."
						,MNG=".$arrs["mngIDNo"]."
						,MNGTEL=".$arrs["mngTel"]."					
					where ID=@ANID
				end
				else 
				begin
					insert into {$this->MAuth->getdb('ARANALYZEDATA')}(ID,EMP,EMPTEL,MNG,MNGTEL)
					select @ANID,".$arrs["empIDNo"].",".$arrs["empTel"].",".$arrs["mngIDNo"].",".$arrs["mngTel"].";
				end
				
				insert into {$this->MAuth->getdb('hp_UserOperationLog')} (userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
				values ('".$this->sess["IDNo"]."','SYS04::สร้างใบวิเคราะห์สินเชื่อ',@ANID+' ".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
				
				insert into #transaction select 'n' as error,@ANID as id,'สร้างใบวิเคราะห์สินเชื่อเสร็จแล้วครับ <br>เลขที่ใบวิเคราะห์สินเชื่อ '+@ANID+' ' as msg;
				commit tran ins;
			end try
			begin catch
				rollback tran ins;
				insert into #transaction select 'y' as error,'' as id,ERROR_MESSAGE() as msg;
			end catch		
		";
		//echo $sql; exit;
		$this->db->query($sql);
		
		$sql   = "select * from #transaction";
		$query = $this->db->query($sql);
		
		$stat  = true;
		$msg   = '';
		$ARANALYZE_ID  = '';
		
		if($query->row()) {
			foreach ($query->result() as $row) {
				$stat = ($row->error == "y" ? true : false);
				$ARANALYZE_ID = $row->id;
				$msg = $row->msg;
			}
		}else{
			$stat = false;
			$msg = "ผิดพลาด :: ไม่สามารถทำรายการได้ในขณะนี้ โปรดลองทำรายการใหม่ภายหลัง";
		}
		
		$response = array();
		$response['error'] = $stat;
		$response['msg'][] = $msg;
		$response['ARANALYZE_ID'] = $ARANALYZE_ID;
		
		echo json_encode($response); exit;
	}
	
	function saveCheck(){
		$response = array("error"=>false,"msg"=>array());
		
		// ข้อมูลรถ
		if($_POST["strno"] == ""){ 
			$response["error"] = true; 
			$response["msg"][] = "คุณยังไม่ระบุเลขตัวถัง"; 
		}
		if($_POST["dwnAmt"] == ""){ 
			$response["error"] = true; 
			$response["msg"][] = "คุณยังไม่ระบุเงินดาวน์รถ"; 
		}else if(!is_numeric(str_replace(",","",$_POST["dwnAmt"]))){ 
			$response["error"] = true; 
			$response["msg"][] = "คุณระบุเงินดาวน์รถไม่ถูกต้อง"; 
		}
		if($_POST["insuranceAmt"] == ""){ 
			$response["error"] = true; 
			$response["msg"][] = "คุณยังไม่ระบุเงินดาวน์ ป1"; 
		}else if(!is_numeric(str_replace(",","",$_POST["insuranceAmt"]))){
			$response["error"] = true; 
			$response["msg"][] = "คุณระบุเงินดาวน์ ป1 ไม่ถูกต้อง"; 
		}
		if($_POST["nopay"] == ""){ 
			$response["error"] = true; 
			$response["msg"][] = "คุณยังไม่ระบุจำนวนงวด"; 
		}else if(!is_numeric(str_replace(",","",$_POST["nopay"]))){
			$response["error"] = true; 
			$response["msg"][] = "คุณระบุจำนวนงวดไม่ถูกต้อง"; 
		}
		if($_POST["price"] == ""){ 
			$response["error"] = true; 
			$response["msg"][] = "คุณยังไม่ระบุราคารถ(สด) ก่อนหักส่วนลด"; 
		}else if(!is_numeric(str_replace(",","",$_POST["price"]))){
			$response["error"] = true; 
			$response["msg"][] = "คุณระบุราคารถ(สด) ก่อนหักส่วนลด ไม่ถูกต้อง"; 
		}
		
		// ลูกค้า
		if($_POST["cuscod"] == ""){ 
			$response["error"] = true; 
			$response["msg"][] = "คุณยังไม่ระบุผู้เช่าซื้อ"; 
		}else{
			if($_POST["idnoStat"] == ""){ 
				$response["error"] = true; 
				$response["msg"][] = "คุณยังไม่ระบุสถานะ ผู้เช่าซื้อ"; 
			}
			if($_POST["addr1"] == ""){ 
				$response["error"] = true; 
				$response["msg"][] = "คุณยังไม่ระบุที่อยู่ตาม ทบ.บ้าน"; 
			}
			if($_POST["addr2"] == ""){ 
				$response["error"] = true; 
				$response["msg"][] = "คุณยังไม่ระบุที่อยู่ส่งเอกสาร"; 
			}
			if($_POST["phoneNumber"] == ""){ 
				$response["error"] = true; 
				$response["msg"][] = "คุณยังไม่ระบุเบอร์ติดต่อ"; 
			}
			if($_POST["career"] == ""){ 
				$response["error"] = true; 
				$response["msg"][] = "คุณยังไม่ระบุอาชีพของผู้เช่าซื้อ"; 
			}
			if($_POST["careerOffice"] == ""){ 
				$response["error"] = true; 
				$response["msg"][] = "คุณยังไม่ระบุสถานที่ทำงาน ของผู้เช่าซื้อ"; 
			}
			if($_POST["careerPhone"] == ""){ 
				$response["error"] = true; 
				$response["msg"][] = "คุณยังไม่ระบุเบอร์ติดต่อที่ทำงาน ของผู้เช่าซื้อ"; 
			}
			if($_POST["income"] == ""){ 
				$response["error"] = true; 
				$response["msg"][] = "คุณยังไม่ระบุรายได้/เดือน ของผู้เช่าซื้อ"; 
			}else if(!is_numeric(str_replace(",","",$_POST["income"]))){
				$response["error"] = true; 
				$response["msg"][] = "คุณระบุรายได้/เดือน ของผู้เช่าซื้อ ไม่ถูกต้อง"; 
			}
		}
		
		// คนค้ำประกัน 1
		if($_POST["is1_cuscod"] == ""){ 
			$response["error"] = true; 
			$response["msg"][] = "คุณยังไม่ระบุผู้ค้ำประกัน 1"; 
		}else{
			if($_POST["is1_idnoStat"] == ""){ 
				$response["error"] = true; 
				$response["msg"][] = "คุณยังไม่ระบุสถานะ ผู้ค้ำประกัน 1"; 
			}
			if($_POST["is1_addr1"] == ""){ 
				$response["error"] = true; 
				$response["msg"][] = "คุณยังไม่ระบุที่อยู่ตาม ทบ.บ้าน"; 
			}
			if($_POST["is1_addr2"] == ""){ 
				$response["error"] = true; 
				$response["msg"][] = "คุณยังไม่ระบุที่อยู่ส่งเอกสาร"; 
			}
			if($_POST["is1_phoneNumber"] == ""){ 
				$response["error"] = true; 
				$response["msg"][] = "คุณยังไม่ระบุเบอร์ติดต่อ"; 
			}
			if($_POST["is1_career"] == ""){ 
				$response["error"] = true; 
				$response["msg"][] = "คุณยังไม่ระบุอาชีพของผู้ค้ำประกัน 1"; 
			}
			if($_POST["is1_careerOffice"] == ""){ 
				$response["error"] = true; 
				$response["msg"][] = "คุณยังไม่ระบุสถานที่ทำงาน ของผู้ค้ำประกัน 1"; 
			}
			if($_POST["is1_careerPhone"] == ""){ 
				$response["error"] = true; 
				$response["msg"][] = "คุณยังไม่ระบุเบอร์ติดต่อที่ทำงาน ของผู้ค้ำประกัน 1"; 
			}
			if($_POST["is1_income"] == ""){ 
				$response["error"] = true; 
				$response["msg"][] = "คุณยังไม่ระบุรายได้/เดือน ของผู้ค้ำประกัน 1"; 
			}else if(!is_numeric(str_replace(",","",$_POST["is1_income"]))){
				$response["error"] = true; 
				$response["msg"][] = "คุณระบุรายได้/เดือน ของผู้ค้ำประกัน 1 ไม่ถูกต้อง"; 
			}
		}
		
		// คนค้ำประกัน 2
		if($_POST["is2_cuscod"] != ""){
			if($_POST["is2_idnoStat"] == ""){ 
				$response["error"] = true; 
				$response["msg"][] = "คุณยังไม่ระบุสถานะ ผู้ค้ำประกัน 2"; 
			}
			if($_POST["is2_addr1"] == ""){ 
				$response["error"] = true; 
				$response["msg"][] = "คุณยังไม่ระบุที่อยู่ตาม ทบ.บ้าน"; 
			}
			if($_POST["is2_addr2"] == ""){ 
				$response["error"] = true; 
				$response["msg"][] = "คุณยังไม่ระบุที่อยู่ส่งเอกสาร"; 
			}
			if($_POST["is2_phoneNumber"] == ""){ 
				$response["error"] = true; 
				$response["msg"][] = "คุณยังไม่ระบุเบอร์ติดต่อ"; 
			}
			if($_POST["is2_career"] == ""){ 
				$response["error"] = true; 
				$response["msg"][] = "คุณยังไม่ระบุอาชีพของผู้ค้ำประกัน 2"; 
			}
			if($_POST["is2_careerOffice"] == ""){ 
				$response["error"] = true; 
				$response["msg"][] = "คุณยังไม่ระบุสถานที่ทำงาน ของผู้ค้ำประกัน 2"; 
			}
			if($_POST["is2_careerPhone"] == ""){ 
				$response["error"] = true; 
				$response["msg"][] = "คุณยังไม่ระบุเบอร์ติดต่อที่ทำงาน ของผู้ค้ำประกัน 2"; 
			}
			if($_POST["is2_income"] == ""){ 
				$response["error"] = true; 
				$response["msg"][] = "คุณยังไม่ระบุรายได้/เดือน ของผู้ค้ำประกัน 2"; 
			}else if(!is_numeric(str_replace(",","",$_POST["is2_income"]))){
				$response["error"] = true; 
				$response["msg"][] = "คุณระบุรายได้/เดือน ของผู้ค้ำประกัน 2 ไม่ถูกต้อง"; 
			}
		}
		
		if($_POST["empIDNo"] == ""){ 
			$response["error"] = true; 
			$response["msg"][] = "คุณยังไม่ระบุพนักงานสาขา"; 
		}
		
		if($_POST["empTel"] == ""){ 
			$response["error"] = true; 
			$response["msg"][] = "คุณยังไม่ระบุเบอร์ติดต่อพนักงานสาขา"; 
		}
		
		if($_POST["mngIDNo"] == ""){ 
			$response["error"] = true; 
			$response["msg"][] = "คุณยังไม่ระบุผจก/ว่าที่ ผจก.สาขา"; 
		}
		
		if($_POST["mngTel"] == ""){ 
			$response["error"] = true; 
			$response["msg"][] = "คุณยังไม่ระบุเบอร์ติดต่อผจก/ว่าที่ ผจก.สาขา";
		}
		
		if($response["error"]){ echo json_encode($response); exit; }
	}
	
	function approved(){
		$anid 	 = $_POST["ANID"];
		$apptype = $_POST["apptype"];
		$comment = $_POST["comment"];
		
		$sql = "
			if object_id('tempdb..#transaction') is not null drop table #transaction;
			create table #transaction (error varchar(1),id varchar(12),msg varchar(max));

			begin tran upd
			begin try
				if exists(select * from {$this->MAuth->getdb('ARANALYZE')} where ID='".$anid."')
				begin 
					update {$this->MAuth->getdb('ARANALYZE')} 
					set ANSTAT='".$apptype."'
					where ID='".$anid."'
				end
				else
				begin
					rollback tran upd;
					insert into #transaction select 'y' as error,'".$anid."' as id,'ผิดพลาด ไม่พบข้อมูล<br>เลขที่ใบวิเคราะห์สินเชื่อ (1)".$anid."' as msg;
					return;
				end
				
				if exists(select * from {$this->MAuth->getdb('ARANALYZEDATA')} where ID='".$anid."')
				begin 
					update {$this->MAuth->getdb('ARANALYZEDATA')} 
					set APPROVE='".$this->sess["IDNo"]."'
						,APPROVEDT=getdate()
						,COMMENT='".$comment."'
					where ID='".$anid."'
				end
				else
				begin
					rollback tran upd;
					insert into #transaction select 'y' as error,'".$anid."' as id,'ผิดพลาด ไม่พบข้อมูล<br>เลขที่ใบวิเคราะห์สินเชื่อ (2)".$anid."' as msg;
					return;
				end
				
				insert into {$this->MAuth->getdb('hp_UserOperationLog')} (userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
				values ('".$this->sess["IDNo"]."','SYS04::อนุมัติใบวิเคราะห์สินเชื่อ ".$anid."','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
				
				insert into #transaction select 'n' as error,'".$anid."' as id,'อนุมัติรายการวิเคราะห์สินเชื่อเสร็จแล้ว <br>เลขที่ใบวิเคราะห์สินเชื่อ ".$anid."' as msg;
				commit tran upd;
			end try
			begin catch
				rollback tran upd;
				insert into #transaction select 'y' as error,'' as id,ERROR_MESSAGE() as msg;
			end catch
		";
		$this->db->query($sql);
		
		$sql 	= "select * from #transaction";   
		$query 	= $this->db->query($sql);
		
		$stat 	= true;
		$ARANALYZE_ID  = '';
		$msg  	= '';
		
		if($query->row()) {
			foreach ($query->result() as $row) {
				$stat = ($row->error == "y" ? true : false);
				$ARANALYZE_ID = $row->id;
				$msg = $row->msg;
			}
		}else{
			$stat = false;
			$msg = "ผิดพลาด :: ไม่สามารถทำรายการได้ในขณะนี้ โปรดลองทำรายการใหม่ภายหลัง";
		}
		
		$response = array();
		$response['error'] = $stat;
		$response['msg'][] = $msg;
		$response['ARANALYZE_ID'] = $ARANALYZE_ID;
		
		echo json_encode($response); exit;
	}
	
	function formApproved(){
		$html = "
			<div class='row'><div class='col-sm-12'>
				<div class='form-group'>
					รายการอนุมัติ 
					<select id='APPTYPE' class='form-control'>
						<option value='A'>อนุมัติ</option>
						<option value='N'>ไม่อนุมัติ</option>
					</select>
					
					ผลการวิเคราะห์ 
					<textarea id='APPCOMMENT' class='form-control' rows='10' style='color:green;'></textarea>
					
					เปลี่ยนแปลงอัตราดอกผลเช่าซื้อ
					<input type='text' id='' class='form-control'>
				</div>
			</div>
		";						
		$response = array("html" => $html);
		echo json_encode($response);
	}
	
	function getCusHistory(){
		$cuscod = $_POST["cuscod"];
		$tableid = md5($cuscod);
		
		$sql = "
			declare @cuscod varchar(12) = '".$cuscod."';
			SELECT A.CONTNO,A.CUSCOD,A.TYPESALE,A.LOCAT
				,convert(varchar(8),A.SDATE,112) as SDATE
				,A.TOTPRC,A.SMPAY,A.BALANCE,A.SMCHQ
				,A.TKANG,A.STRNO,A.RESVNO,A.TSALE,A.FL  
			FROM (
				SELECT CONTNO,CUSCOD,'ขายผ่อน      ' AS TYPESALE
					,LOCAT,SDATE,TOTPRC,SMPAY,(TOTPRC-SMPAY) AS BALANCE,SMCHQ
					,TKANG,STRNO,RESVNO,TSALE,'' AS FL 
				FROM ARMAST  
				WHERE DELDT IS NULL AND CUSCOD = @cuscod
				UNION 
				SELECT CONTNO,CUSCOD,'ขายผ่อน      ' AS TYPESALE,LOCAT,SDATE,TOTPRC
					,SMPAY,(TOTPRC-SMPAY) AS BALANCE,SMCHQ,TKANG,STRNO,RESVNO,TSALE,'*' AS FL 
				FROM HARMAST  
				WHERE DELDT IS NULL AND CUSCOD = @cuscod
				UNION 
				SELECT CONTNO,CUSCOD,'ขายสด        ' AS TYPESALE,LOCAT,SDATE,TOTPRC,SMPAY
					,(TOTPRC-SMPAY) AS BALANCE,SMCHQ,TKANG,STRNO,RESVNO,TSALE,'' AS FL 
				FROM ARCRED  
				WHERE DELDT IS NULL AND CUSCOD = @cuscod
				UNION 
				SELECT CONTNO,CUSCOD,'ขายสด        ' AS TYPESALE,LOCAT,SDATE,TOTPRC,SMPAY
					,(TOTPRC-SMPAY) AS BALANCE,SMCHQ,TKANG,STRNO,RESVNO,TSALE,'*' AS FL 
				FROM HARCRED  
				WHERE DELDT IS NULL and CUSCOD = @cuscod
				UNION 
				SELECT CONTNO,CUSCOD,'ขายไฟแนนซ์   ' AS TYPESALE,LOCAT,SDATE,TOTPRC,SMPAY
					,(TOTPRC-SMPAY) AS BALANCE,SMCHQ,TKANG,STRNO,RESVNO,TSALE,'' AS FL 
				FROM ARFINC  
				WHERE DELDT IS NULL and CUSCOD = @cuscod
				UNION 
				SELECT CONTNO,CUSCOD,'ขายไฟแนนซ์   ' AS TYPESALE,LOCAT,SDATE,TOTPRC,SMPAY
					,(TOTPRC-SMPAY) AS BALANCE,SMCHQ,TKANG,STRNO,RESVNO,TSALE,'*' AS FL 
				FROM HARFINC  
				WHERE DELDT IS NULL and CUSCOD = @cuscod
				UNION 
				SELECT A.CONTNO,A.CUSCOD,'ขายส่งเอเยนต์' AS TYPESALE,A.LOCAT,A.SDATE,A.TOTPRC,A.SMPAY
					,(A.TOTPRC-A.SMPAY) AS BALANCE,A.SMCHQ,0 AS TKANG,B.STRNO ,'' AS RESVNO,A.TSALE,'' AS FL 
				FROM AR_INVOI A,INVTRAN B  
				WHERE A.DELDT IS NULL AND A.CONTNO=B.CONTNO  AND A.CUSCOD = @cuscod
				UNION 
				SELECT A.CONTNO,A.CUSCOD,'ขายส่งเอเยนต์' AS TYPESALE,A.LOCAT,A.SDATE,A.TOTPRC,A.SMPAY
					,(A.TOTPRC-A.SMPAY) AS BALANCE,A.SMCHQ,0 AS TKANG,B.STRNO,'*' AS RESVNO,A.TSALE,'' AS FL 
				FROM HAR_INVO A,HINVTRAN B  
				WHERE A.DELDT IS NULL AND A.CONTNO=B.CONTNO  AND A.CUSCOD = @cuscod
				UNION 
				SELECT A.ARCONT AS CONTNO,A.CUSCOD,'ลูกหนี้อื่น  ' AS TYPESALE,A.LOCAT,A.ARDATE AS SDATE,A.PAYAMT AS TOTPRC
					,A.SMPAY,(A.PAYAMT-A.SMPAY) AS BALANCE,A.SMCHQ,0 AS TKANG,'' AS STRNO,'' AS RESVNO,A.TSALE,'' AS FL 
				FROM AROTHR A  
				WHERE A.CUSCOD = @cuscod
			) AS A 
			LEFT OUTER JOIN REGTAB B ON A.STRNO=B.STRNO  
			WHERE (B.REGNO LIKE '%' OR (B.REGNO IS NULL) ) AND A.STRNO LIKE '%' 
			ORDER BY TYPESALE,SDATE DESC
		";
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$html .= "
					<tr>
						<td>".$row->CONTNO."</td>
						<td>".$row->TYPESALE."</td>
						<td>".$row->LOCAT."</td>
						<td>".$this->Convertdate(2,$row->SDATE)."</td>
						<td align='right'>".number_format($row->TOTPRC,2)."</td>
						<td align='right'>".number_format($row->SMPAY,2)."</td>
						<td align='right'>".number_format($row->BALANCE,2)."</td>						
						<td>".$row->FL."</td>
					</tr>
				";
			}
		}
		
		$html = "
			<table id='".$tableid."' class='table table-bordered' cellspacing='0' width='calc(100% - 1px)'>			
				<thead>
					<tr>
						<th colspan='8'>การซื้อ</th>
					</tr>
					<tr>
						<th>เลขที่สัญญา</th>
						<th>ประเภท ล/น.</th>
						<th>สาขา</th>
						<th>วันที่ทำสัญญา</th>
						<th>ราคา</th>
						<th>ชำระแล้ว</th>
						<th>ล/น. คงเหลือ</th>
						<th>*</th>
					</tr>
				</thead>
				<tbody>
					".$html."
				</tbody>				
			</table>
		";
		
		$response = array("html"=>$html,"tableName"=>$tableid);
		echo json_encode($response);
	}
	
}




















