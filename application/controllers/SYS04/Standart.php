<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/********************************************************
             ______@06/09/2019______
            / / _ _   _ _     __ 
           / // __ \ / __ \ / __ \
       _ _/ // /_/ // / / // /_/ /
     /_ _ _/ \_ _ //_/ /_/ \__  /
                          _ _/ /
                         /___ /
********************************************************/
class Standart extends MY_Controller {
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
				<div class='col-sm-12' >
					<div class='row'>
						<div class='col-sm-2'>	
							<div class='form-group'>
								ชื่อเรียก
								<input type='text' id='SNAME' class='form-control input-sm' placeholder='ชื่อเรียก' >
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
								แบบ
								<input type='text' id='SBAAB' class='form-control input-sm' placeholder='แบบ' >
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								สี
								<input type='text' id='SCOLOR' class='form-control input-sm' placeholder='สี' >
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								บังคับใช้ จาก
								<input type='text' id='SEVENTS' class='form-control input-sm' placeholder='จาก'  data-provide='datepicker' data-date-language='th-th' value=''>
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								บังคับใช้ ถึง
								<input type='text' id='SEVENTE' class='form-control input-sm' placeholder='ถึง'  data-provide='datepicker' data-date-language='th-th' value=''>
							</div>
						</div>
						
						<div class='col-sm-6'>	
							<div class='form-group'>
								กิจกรรมการขาย
								<select id='SACTICOD' class='form-control JD-BSSELECT' multiple data-actions-box='true' data-size='8' data-live-search='true'></select>
							</div>
						</div>
					</div>
					<div class='row'>
						<div class='col-sm-2'>	
							<div class='form-group'>
								<button id='btnt1createStd' class='btn btn-cyan btn-block'><span class='glyphicon glyphicon-pencil'> สร้าง</span></button>
							</div>
						</div>
						<div class='col-sm-2 col-sm-offset-8'>	
							<div class='form-group'>
								<button id='btnt1search' class='btn btn-primary btn-block'><span class='glyphicon glyphicon-search'> ค้นหา</span></button>
							</div>
						</div>
					</div>
				</div>
			</div>
		";
		
		$html.= "<script src='".base_url('public/js/SYS04/Standart.js')."'></script>";
		echo $html;
	}
	
	function search_old(){
		$arrs = array();
		$arrs['name']	= $_POST['name'];
		$arrs['model']	= $_POST['model'];
		$arrs['baab']	= $_POST['baab'];
		$arrs['color']	= $_POST['color'];
		$arrs['events']	= $this->Convertdate(1,$_POST['events']);
		$arrs['evente']	= $this->Convertdate(1,$_POST['evente']);
		$arrs['acticod']= $_POST['acticod'];
		
		$condDesc = "";
		/*
		$cond = "";
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
		*/
		
		
		$sql = "
			select a.id,b.name,b.details
				,a.model
				,case when (a.baab='ALL') then 'ทั้งหมด' else a.baab end as baab
				,case when (a.color='ALL') then 'ทั้งหมด' else a.color end as color
				,(select '('+bb.ACTICOD+') '+bb.ACTIDES as ACTIDES from {$this->MAuth->getdb('SETACTI')} bb where bb.ACTICOD=b.ACTICOD collate thai_cs_as) as ACTIDES
				,convert(varchar(8),b.event_s,112) as es
				,convert(varchar(8),b.event_e,112) as ee
				,b.plrank
			from {$this->MAuth->getdb('std_vehicles')} a
			left join {$this->MAuth->getdb('std_pricelist')} b on a.id=b.id
			where 1=1
			order by a.id,b.event_s,b.plrank
		";
		//echo $sql; exit;		
		$query = $this->db->query($sql);
		
		$html = "";
		$NRow = 1;
		if($query->row()){
			foreach($query->result() as $row){
				
				$locat = "";
				$sql2 = "
					select case when locat='ALL' then 'ทั้งหมด' else locat end as locat 
					from {$this->MAuth->getdb('std_pricelist_locat')} a
					where a.id='".$row->id."' and a.plrank='".$row->plrank."'
				";
				//echo $sql2; exit;
				$query2 = $this->db->query($sql2);
				if($query2->row()){
					foreach($query2->result() as $row2){
						if($locat != ""){ $locat .= ","; }
						$locat .= $row2->locat;
					}
				}
				
				$html .= "
					<tr>
						<td>
							<i class='stddetail btn btn-xs btn-success glyphicon glyphicon-zoom-in' STDID='".$row->id."' STDRank='".$row->plrank."' style='cursor:pointer;'> รายละเอียด  </i>
						</td>
						<td>".$row->name."</td>
						<td style='max-width:150px;white-space:normal;'>".$row->details."</td>
						<td>".$row->model."</td>
						<td>".$row->baab."</td>
						<td>".$row->color."</td>
						<td>".$row->ACTIDES."</td>
						<td style='max-width:200px;white-space:normal;'>".$locat."</td>
						<td>".$this->Convertdate(2,$row->es)." ถึง ".$this->Convertdate(2,$row->ee)."</td>
					</tr>
				";
				
				$NRow++;
			}
		}
		
		$html = "
			<div id='table-fixed-std' class='col-sm-12' style='height:calc(100% - 30px);width:100%;overflow:auto;font-size:8pt;'>
				<table id='table-std' class='table table-bordered' cellspacing='0' width='calc(100% - 1px)'>
					<thead>						
						<tr align='center' style='line-height:20px;'>
							<td style='vertical-align:middle;background-color:#c8e6b7;text-align:center;font-size:8pt;' colspan='9'>
								เงื่อนไข :: ".$condDesc."
							</td>
						</tr>
						<tr align='center'>
							<th style='vertical-align:middle;background-color:#c8e6b7;'>###</th>
							<th style='vertical-align:middle;background-color:#c8e6b7;'>ชื่อเรียก</th>
							<th style='vertical-align:middle;background-color:#c8e6b7;'>รายละเอียด</th>
							<th style='vertical-align:middle;background-color:#c8e6b7;'>รุ่น</th>
							<th style='vertical-align:middle;background-color:#c8e6b7;'>แบบ</th>
							<th style='vertical-align:middle;background-color:#c8e6b7;'>สี</th>
							<th style='vertical-align:middle;background-color:#c8e6b7;'>กิจกรรมการขาย</th>
							<th style='vertical-align:middle;background-color:#c8e6b7;'>ใช้กับสาขา</th>
							<th style='vertical-align:middle;background-color:#c8e6b7;'>บังคับใช้วันที่</th>
						</tr>
					</thead>	
					<tbody>
						".$html."
					</tbody>
				</table>
			</div>
			<div id='table-fixed-std-detail' class='col-sm-12' style='height:calc(100% - 30px);width:100%;overflow:auto;font-size:8pt;'>
			
			</div>
		";
		
		$response = array("html"=>$html,"status"=>true);
		echo json_encode($response);
	}
	
	function search_20191003(){
		$arrs = array();
		$arrs['name']	= $_POST['name'];
		$arrs['model']	= $_POST['model'];
		$arrs['baab']	= $_POST['baab'];
		$arrs['color']	= $_POST['color'];
		$arrs['events']	= $this->Convertdate(1,$_POST['events']);
		$arrs['evente']	= $this->Convertdate(1,$_POST['evente']);
		$arrs['acticod']= $_POST['acticod'];
		
		$cond 		= "";
		$condDesc 	= "";
		if($arrs['name'] != ""){
			$cond .= " and b.name like '%".$arrs['name']."%'";
			$condDesc .= " ชื่อเรียก ::  ".$arrs['name'];
		}
		
		if($arrs['model'] != ""){
			$cond .= " and a.model = '".$arrs['model']."'";
			$condDesc .= " รุ่น ::  ".$arrs['model'];
		}
		
		if($arrs['baab'] != ""){
			$cond .= " and a.baab = '".$arrs['baab']."'";
			$condDesc .= " แบบ ::  ".$arrs['baab'];
		}
		
		if($arrs['color'] != ""){
			$cond .= " and a.color = '".$arrs['color']."'";
			$condDesc .= " สี ::  ".$arrs['color'];
		}
		
		if($arrs['acticod'] != ""){
			$temp = "";
			$size = sizeof($arrs['acticod']);
			for($i=0;$i<$size;$i++){
				if($temp != ""){ $temp .= ","; }
				$temp .= "'".$arrs['acticod'][$i]."'";
			}
			
			$cond .= " and b.ACTICOD in (".$temp.")";
			$condDesc .= " กิจกรรมการขาย :: (".$temp.")";
		}
		
		$sql = "						
			select a.id ,b.plrank
				,a.model + case when a.baab = 'ALL' then ' (ทุกแบบ)' else ' ('+a.baab+')' end as model
				,b.name
				,b.details
				,a.color
				,b.price
				,b.pricespecial
				,b.ACTICOD
				
				,c.level_r
				,c.dwnrate_s
				,c.dwnrate_e
				,c.interest_rate
				,c.interest_rate2
				--,'ของแถม'
				
				,c.insurance
				,c.transfers
				,c.regist
				,c.act
				,c.coupon
				
				,convert(char(8),b.event_s,112) as event_s
				,convert(char(8),b.event_e,112) as event_e
				
				,(select MAX(cc.level_r) from {$this->MAuth->getdb('std_down')} cc where cc.id=b.id and cc.plrank=b.plrank) as maxR
			from  {$this->MAuth->getdb('std_vehicles')} a
			left join {$this->MAuth->getdb('std_pricelist')} b on a.id=b.id
			left join {$this->MAuth->getdb('std_down')} c on b.id=c.id and b.plrank=c.plrank
			where 1=1 ".$cond."
			order by a.model,a.id,b.plrank,b.event_s
		";
		//echo $sql; exit;		
		$query = $this->db->query($sql);
		
		$data = "";
		$data_excel = "";
		$NRow = 1;
		if($query->row()){
			$bg = "#d6e8ff";
			foreach($query->result() as $row){
				$other 		= "";
				$locat 		= "";
				$btn 		= "";
				$tb_free 	= "";
				if($row->level_r == 1){
					$bg = ($bg == "#d6e8ff" ? "#fff":"#d6e8ff");
					$locat = "";
					$sql2 = "
						select case when locat='ALL' then 'ทั้งหมด' else locat end as locat 
						from {$this->MAuth->getdb('std_pricelist_locat')} a
						where a.id='".$row->id."' and a.plrank='".$row->plrank."'
					";
					//echo $sql2; exit;
					$query2 = $this->db->query($sql2);
					if($query2->row()){
						foreach($query2->result() as $row2){
							if($locat != ""){ $locat .= ","; }
							$locat .= $row2->locat;
						}
					}
					
					$other = "
						<td rowspan='".$row->maxR."' style='vertical-align:text-top;'>".$NRow++."</td>
						<td rowspan='".$row->maxR."' style='vertical-align:text-top;'>".$row->model."</td>
						<td rowspan='".$row->maxR."' style='max-width:150px;white-space:normal;vertical-align:text-top;'>".$row->name."</td>
						<td rowspan='".$row->maxR."' style='max-width:150px;white-space:normal;vertical-align:text-top;'>".$row->details."</td>
						<td rowspan='".$row->maxR."' style='vertical-align:text-top;'>".$row->color."</td>
						<td rowspan='".$row->maxR."' style='vertical-align:text-top;'>".number_format($row->price,0)."</td>
						<td rowspan='".$row->maxR."' style='vertical-align:text-top;'>".number_format($row->pricespecial,0)."</td>
						<td rowspan='".$row->maxR."' style='vertical-align:text-top;'>".$row->ACTICOD."</td>
					";
					$locat = "
						<td rowspan='".$row->maxR."' style='min-width:200px;width:200px;white-space:normal;vertical-align:text-top;'>".$locat."</td>
						<td rowspan='".$row->maxR."' style='vertical-align:text-top;'>".$this->Convertdate(2,$row->event_s)." ถึง ".($row->event_e == "" ? "จนกว่าจะมีการเปลี่ยนแปลง":$this->Convertdate(2,$row->event_e))."</td>
					";
					
					$btn = "
						<td rowspan='".$row->maxR."' ><i class='stddetail btn btn-xs btn-warning glyphicon glyphicon-edit' STDID='".$row->id."' STDRank='".$row->plrank."' style='cursor:pointer;'> แก้ไข  </i></td>
					";
					
					/*ของแถม*/
					$sqlfree = "
						select nopay_s,nopay_e,free_rate
						from {$this->MAuth->getdb('std_package')} a
						where a.id='".$row->id."' and a.plrank='".$row->plrank."'
					";
					$queryfree = $this->db->query($sqlfree);
					
					if($queryfree->row()){
						foreach($queryfree->result() as $rowfree){
							$tb_free .= "
								<tr>
									<td style='mso-number-format:&#34;\@&#34;;'>".$rowfree->nopay_s." - ".($rowfree->nopay_e == ""?"ขึ้นไป":$rowfree->nopay_e)."</td>
									<td>".number_format($rowfree->free_rate,0)."</td>
								</tr>
							";
						}
					}
					
					if($tb_free != ""){
						$tb_free = "
							<td rowspan='".$row->maxR."'>
								<table class='table table-bordered' cellspacing='0'>".$tb_free."</table>
							</td>
						";
					}
				}
				
				$data .= "
					<tr style='background-color:{$bg};'>
						{$other}
						<td>".$row->level_r."</td>
						<td>".number_format($row->dwnrate_s,0)." - ".($row->dwnrate_e == "" ? "ขึ้นไป":number_format($row->dwnrate_e,0))."</td>
						<td>".$row->interest_rate."</td>
						<td>".$row->interest_rate2."</td>
						{$tb_free}
						<td>".number_format($row->insurance,0)."</td>
						<td>".number_format($row->transfers,0)."</td>
						<td>".number_format($row->regist,0)."</td>
						<td>".number_format($row->act,0)."</td>
						<td>".($row->coupon == "" ? "ไม่ระบุ":number_format($row->coupon,0))."</td>
						{$locat}
						{$btn}
					</tr>
				";
				
				$data_excel .= "
					<tr style='background-color:{$bg};'>
						{$other}
						<td>".$row->level_r."</td>
						<td>".number_format($row->dwnrate_s,0)." - ".($row->dwnrate_e == "" ? "ขึ้นไป":number_format($row->dwnrate_e,0))."</td>
						<td>".$row->interest_rate."</td>
						<td>".$row->interest_rate2."</td>
						{$tb_free}
						<td>".number_format($row->insurance,0)."</td>
						<td>".number_format($row->transfers,0)."</td>
						<td>".number_format($row->regist,0)."</td>
						<td>".number_format($row->act,0)."</td>
						<td>".($row->coupon == "" ? "ไม่ระบุ":number_format($row->coupon,0))."</td>
						{$locat}
					</tr>
				";
			}
		}
		
		$html = "
			<button id='excelstd'> Excel </button>
			<div id='table-fixed-std' class='col-sm-12' style='height:calc(100% - 25px);width:100%;overflow:auto;font-size:8pt;'>
				<table id='table-std' class='table table-bordered' cellspacing='0' width='calc(100% - 1px)'>
					<thead>						
						<tr align='center' style='line-height:20px;'>
							<th style='vertical-align:middle;background-color:#c8e6b7;text-align:center;font-size:8pt;' colspan='21'>
								เงื่อนไข :: ".$condDesc."
							</th>
						</tr>
						<tr align='center'>
							<th style='vertical-align:middle;background-color:#c8e6b7;'>###</th>
							<th style='vertical-align:middle;background-color:#c8e6b7;'>รุ่น (แบบ)</th>
							<th style='vertical-align:middle;background-color:#c8e6b7;'>ชื่อเรียก</th>
							<th style='vertical-align:middle;background-color:#c8e6b7;'>ลักษณะ</th>
							<th style='vertical-align:middle;background-color:#c8e6b7;'>สี</th>
							<th style='vertical-align:middle;background-color:#c8e6b7;'>ราคาขาย</th>
							<th style='vertical-align:middle;background-color:#c8e6b7;'>ราคาผลัด</th>
							<th style='vertical-align:middle;background-color:#c8e6b7;'>กิจกรรมการขาย</th>
							<th style='vertical-align:middle;background-color:#c8e6b7;'>ขั้นเงินดาวน์</th>
							<th style='vertical-align:middle;background-color:#c8e6b7;'>ช่วงเงินดาวน์</th>
							<th style='vertical-align:middle;background-color:#c8e6b7;'>ดอกเบี้ย</th>
							<th style='vertical-align:middle;background-color:#c8e6b7;'>ดอกเบี้ย</th>
							<th style='vertical-align:middle;background-color:#c8e6b7;'>ของแถม</th>
							<th style='vertical-align:middle;background-color:#c8e6b7;'>ประกัน</th>
							<th style='vertical-align:middle;background-color:#c8e6b7;'>โอน</th>
							<th style='vertical-align:middle;background-color:#c8e6b7;'>ทะเบียน</th>
							<th style='vertical-align:middle;background-color:#c8e6b7;'>พรบ</th>
							<th style='vertical-align:middle;background-color:#c8e6b7;'>คูปอง</th>
							<th style='vertical-align:middle;background-color:#c8e6b7;'>สาขา</th>
							<th style='vertical-align:middle;background-color:#c8e6b7;'>วันที่</th>
							<th style='vertical-align:middle;background-color:#c8e6b7;'>###</th>
						</tr>
					</thead>	
					<tbody>
						".$data."
					</tbody>
				</table>
			</div>
			<div id='table-fixed-std-detail' class='col-sm-12' style='height:calc(100%);width:100%;overflow:auto;font-size:8pt;'>
			
			</div>
		";
		
		$response = array("html"=>$html,"status"=>true,"excel"=>$this->html_excel($query));
		echo json_encode($response);
	}
	
	function search(){
		$arrs = array();
		$arrs['name']	 = $_POST['name'];
		$arrs['model']	 = $_POST['model'];
		$arrs['baab']	 = $_POST['baab'];
		$arrs['color']	 = $_POST['color'];
		$arrs['events']	 = $this->Convertdate(1,$_POST['events']);
		$arrs['evente']	 = $this->Convertdate(1,$_POST['evente']);
		$arrs['acticod'] = $_POST['acticod'];
		
		$cond 		= "";
		$condDesc 	= "";
		if($arrs['name'] != ""){
			$cond .= " and b.name like ''%".$arrs['name']."%''";
			$condDesc .= " ชื่อเรียก ::  ".$arrs['name'];
		}
		
		if($arrs['model'] != ""){
			$cond .= " and a.model = ''".$arrs['model']."''";
			$condDesc .= " รุ่น ::  ".$arrs['model'];
		}
		
		if($arrs['baab'] != ""){
			$cond .= " and a.baab = ''".$arrs['baab']."''";
			$condDesc .= " แบบ ::  ".$arrs['baab'];
		}
		
		if($arrs['color'] != ""){
			$cond .= " and a.color = ''".$arrs['color']."''";
			$condDesc .= " สี ::  ".$arrs['color'];
		}
		
		if($arrs['acticod'] != ""){
			$temp = "";
			$size = sizeof($arrs['acticod']);
			for($i=0;$i<$size;$i++){
				if($temp != ""){ $temp .= ","; }
				$temp .= "''".$arrs['acticod'][$i]."''";
			}
			
			$cond .= " and b.ACTICOD in (".$temp.")";
			$condDesc .= " กิจกรรมการขาย :: (".$temp.")";
		}
		
		if($arrs['events'] != "" && $arrs['evente'] != ""){
			$cond .= " 
				and ''".$arrs['events']."'' between b.event_s and b.event_e
				and ''".$arrs['evente']."'' <= b.event_e
			";
			$condDesc .= " วันที่บังคับใช้ std. ::  [".$_POST['events']." - ".$_POST['evente']."]";
		}else if($arrs['events'] != "" && $arrs['evente'] == ""){
			$cond .= " and ''".$arrs['events']."'' between b.event_s and b.event_e";
			$condDesc .= " วันที่บังคับใช้ std. ::  [".$_POST['events']." เป็นต้นไป]";
		}else if($arrs['events'] == "" && $arrs['evente'] != ""){
			$cond .= " and ''".$arrs['evente']."'' between b.event_s and b.event_e";
			$condDesc .= " วันที่บังคับใช้ std. :: ถึงวันที่ [".$_POST['evente']."]";
		}
		
		$sql = "						
			declare @s int = 1
			declare @maxfree int = (select max(r) from (select ROW_NUMBER() over(partition by id,plrank order by id,plrank,nopay_s) r from {$this->MAuth->getdb('std_package')}) data)
			declare @pv varchar(max);
			declare @sl varchar(max);

			while @s <= ISNULL(@maxfree,0)
			begin 
				select @pv = replace(isnull(@pv,'') + QUOTENAME(@s),'][','],[');
				select @sl = replace(isnull(@sl,'') + QUOTENAME(@s),'][','] as '+CHAR(67)+cast(@s - 1 as varchar)+',[');
				
				set @s += 1;
			end 
			set @sl = ','+@sl+' as '+CHAR(67)+cast(@s - 1 as varchar);

			exec(N'
				select a.id ,b.plrank
					,a.model + case when a.baab = ''ALL'' then '' (ทุกแบบ)'' else '' (''+a.baab+'')'' end as model
					,b.name
					,b.details
					,a.color
					,b.price
					,b.pricespecial
					,case when b.ACTICOD = ''ALL'' then ''ALL :: ทุกกิจกรรมการขาย'' 
						else cast(b.ACTICOD as varchar)+'' :: ''+e.ACTIDES collate thai_ci_as
						end as ACTICOD
					
					,c.level_r
					,c.dwnrate_s
					,c.dwnrate_e
					,c.interest_rate
					,c.interest_rate2
					,'+@maxfree+' as free_col
					'+@sl+'
					
					,c.insurance
					,c.transfers
					,c.regist
					,c.act
					,c.coupon
					
					,convert(char(8),b.event_s,112) as event_s
					,convert(char(8),b.event_e,112) as event_e
					
					,(select MAX(cc.level_r) from {$this->MAuth->getdb('std_down')} cc where cc.id=b.id and cc.plrank=b.plrank) as maxR
				from  {$this->MAuth->getdb('std_vehicles')} a
				left join {$this->MAuth->getdb('std_pricelist')} b on a.id=b.id
				left join {$this->MAuth->getdb('std_down')} c on b.id=c.id and b.plrank=c.plrank
				left join (
					select * from (
						select ROW_NUMBER() over(partition by id,plrank order by id,plrank,nopay_s) r 
							,QUOTENAME(cast(nopay_s as varchar(10)))
							+QUOTENAME(isnull(cast(nopay_e as varchar(10)),''max''))
							+QUOTENAME(cast(free_rate as varchar(10))) as data
							,id,plrank
						from {$this->MAuth->getdb('std_package')}
					) as a
					pivot ( 
						max(data) for r in ('+@pv+')
					) as pv
				) d on b.id=d.id and b.plrank=d.plrank
				left join {$this->MAuth->getdb('SETACTI')} e on b.ACTICOD=e.ACTICOD collate thai_ci_as
				where 1=1 ".$cond."
				order by a.model,a.id,b.plrank,b.event_s
			');
		";
		//echo $sql; exit;		
		$query = $this->db->query($sql);
		
		$data = "";
		$data_excel = "";
		$NRow = 1;
		if($query->row()){
			$bg = "#d6e8ff";
			foreach($query->result() as $row){
				$row 		= (array) $row;
				$other 		= "";
				$locat 		= "";
				$btn 		= "";
				$tb_free 	= "";
				$free 		= "";
				if($row["level_r"] == 1){
					$bg = ($bg == "#d6e8ff" ? "#fff":"#d6e8ff");
					$locat = "";
					$sql2 = "
						select case when locat='ALL' then 'ทั้งหมด' else locat end as locat 
						from {$this->MAuth->getdb('std_pricelist_locat')} a
						where a.id='".$row["id"]."' and a.plrank='".$row["plrank"]."'
					";
					//echo $sql2; exit;
					$query2 = $this->db->query($sql2);
					if($query2->row()){
						foreach($query2->result() as $row2){
							if($locat != ""){ $locat .= ","; }
							$locat .= $row2->locat;
						}
					}
					
					$other = "
						<td rowspan='".$row["maxR"]."' style='vertical-align:text-top;'>".$NRow++."</td>
						<td rowspan='".$row["maxR"]."' style='vertical-align:text-top;'>".$row["model"]."</td>
						<td rowspan='".$row["maxR"]."' style='max-width:150px;min-width:150px;white-space:normal;vertical-align:text-top;'>".$row["name"]."</td>
						<td rowspan='".$row["maxR"]."' style='max-width:150px;min-width:150px;white-space:normal;vertical-align:text-top;'>".$row["details"]."</td>
						<td rowspan='".$row["maxR"]."' style='vertical-align:text-top;'>".$row["color"]."</td>
						<td rowspan='".$row["maxR"]."' style='vertical-align:text-top;'>".number_format($row["price"],0)."</td>
						<td rowspan='".$row["maxR"]."' style='vertical-align:text-top;'>".number_format($row["pricespecial"],0)."</td>
						<td rowspan='".$row["maxR"]."' style='vertical-align:text-top;'>".$row["ACTICOD"]."</td>
					";
					$locat = "
						<td rowspan='".$row["maxR"]."' style='min-width:200px;width:200px;white-space:normal;vertical-align:text-top;'>".$locat."</td>
						<td rowspan='".$row["maxR"]."' style='vertical-align:text-top;'>".$this->Convertdate(2,$row["event_s"])." ถึง ".($row["event_e"] == "" ? "จนกว่าจะมีการเปลี่ยนแปลง":$this->Convertdate(2,$row["event_e"]))."</td>
					";
					
					$btn = "
						<td rowspan='".$row["maxR"]."' ><i class='stddetail btn btn-xs btn-warning glyphicon glyphicon-edit' STDID='".$row["id"]."' STDRank='".$row["plrank"]."' style='cursor:pointer;'> แก้ไข  </i></td>
					";
					
					/*ของแถม*/
					$sqlfree = "
						select nopay_s,nopay_e,free_rate
						from {$this->MAuth->getdb('std_package')} a
						where a.id='".$row["id"]."' and a.plrank='".$row["plrank"]."'
					";
					$queryfree = $this->db->query($sqlfree);
					
					if($queryfree->row()){
						foreach($queryfree->result() as $rowfree){
							$rowfree = (array) $rowfree;
							$tb_free .= "
								<tr>
									<td style='mso-number-format:&#34;\@&#34;;'>".$rowfree["nopay_s"]." - ".($rowfree["nopay_e"] == ""?"ขึ้นไป":$rowfree["nopay_e"])."</td>
									<td>".number_format($rowfree["free_rate"],0)."</td>
								</tr>
							";
						}
					}
					
					if($tb_free != ""){
						$tb_free = "
							<td rowspan='".$row["maxR"]."'>
								<table class='table table-bordered' cellspacing='0'>".$tb_free."</table>
							</td>
						";
					}
					
					$size_free_col = $row["free_col"]; 
					for($i=1;$i<=($size_free_col);$i++){
						if($row["C".$i] != ""){
							$ex = explode("][",$row["C".$i]);
							$free .= "<td rowspan='".$row["maxR"]."'>".$ex[0].($ex[1] == "max" ? " งวด ขึ้นไป":" - ".$ex[1]." งวด")."] [แถม :: ".number_format(str_replace("]","",$ex[2]),0)."]</td>";
						}else{
							$free .= "<td rowspan='".$row["maxR"]."'></td>";
						}
					}
				}
				
				
				$data .= "
					<tr style='background-color:{$bg};'>
						{$other}
						<td>".$row["level_r"]."</td>
						<td>".number_format($row["dwnrate_s"],0)." - ".($row["dwnrate_e"] == "" ? "ขึ้นไป":number_format($row["dwnrate_e"],0))."</td>
						<td>".$row["interest_rate"]."</td>
						<td>".$row["interest_rate2"]."</td>
						".$free."
						<td>".number_format($row["insurance"],0)."</td>
						<td>".number_format($row["transfers"],0)."</td>
						<td>".number_format($row["regist"],0)."</td>
						<td>".number_format($row["act"],0)."</td>
						<td>".($row["coupon"] == "" ? "ไม่ระบุ":number_format($row["coupon"],0))."</td>
						{$locat}
						{$btn}
					</tr>
				";
			}
		}
		
		$html = "
			<img id='excelstd' src='../public/images/excel.png' style='width:30px;height:30px;cursor:pointer;'>
			<div id='table-fixed-std' class='col-sm-12' style='height:calc(100% - 25px);width:100%;overflow:auto;font-size:8pt;'>
				<table id='table-std' class='table table-bordered' cellspacing='0' width='calc(100% - 1px)'>
					<thead>						
						<tr align='center' style='line-height:20px;'>
							<th style='vertical-align:middle;background-color:#c8e6b7;text-align:center;font-size:8pt;' colspan='".($size_free_col+21)."'>
								เงื่อนไข :: ".$condDesc."
							</th>
						</tr>
						<tr align='center'>
							<th style='vertical-align:middle;background-color:#c8e6b7;'>###</th>
							<th style='vertical-align:middle;background-color:#c8e6b7;'>รุ่น (แบบ)</th>
							<th style='vertical-align:middle;background-color:#c8e6b7;'>ชื่อเรียก</th>
							<th style='vertical-align:middle;background-color:#c8e6b7;'>ลักษณะ</th>
							<th style='vertical-align:middle;background-color:#c8e6b7;'>สี</th>
							<th style='vertical-align:middle;background-color:#c8e6b7;'>ราคาขาย</th>
							<th style='vertical-align:middle;background-color:#c8e6b7;'>ราคาผลัด</th>
							<th style='vertical-align:middle;background-color:#c8e6b7;'>กิจกรรมการขาย</th>
							<th style='vertical-align:middle;background-color:#c8e6b7;'>ขั้นเงินดาวน์</th>
							<th style='vertical-align:middle;background-color:#c8e6b7;'>ช่วงเงินดาวน์</th>
							<th style='vertical-align:middle;background-color:#c8e6b7;'>ดอกเบี้ย</th>
							<th style='vertical-align:middle;background-color:#c8e6b7;'>ดอกเบี้ย</th>
							<th style='vertical-align:middle;background-color:#c8e6b7;' colspan='".$size_free_col."'>std. ของแถม</th>
							<th style='vertical-align:middle;background-color:#c8e6b7;'>ประกัน</th>
							<th style='vertical-align:middle;background-color:#c8e6b7;'>โอน</th>
							<th style='vertical-align:middle;background-color:#c8e6b7;'>ทะเบียน</th>
							<th style='vertical-align:middle;background-color:#c8e6b7;'>พรบ</th>
							<th style='vertical-align:middle;background-color:#c8e6b7;'>คูปอง</th>
							<th style='vertical-align:middle;background-color:#c8e6b7;'>สาขา</th>
							<th style='vertical-align:middle;background-color:#c8e6b7;'>วันที่บังคับใช้ std.</th>
							<th style='vertical-align:middle;background-color:#c8e6b7;'>###</th>
						</tr>
					</thead>	
					<tbody>
						".$data."
					</tbody>
				</table>
			</div>
			<div id='table-fixed-std-detail' class='col-sm-12' style='height:calc(100%);width:100%;overflow:auto;font-size:8pt;'>
			
			</div>
		";
		
		$response = array("html"=>$html,"status"=>true,"excel"=>$this->html_excel($query));
		echo json_encode($response);
	}
	
	function html_excel($query){
		$data_excel = "";
		$NRow = 0;
		if($query->row()){
			$bg = "#d6e8ff";
			foreach($query->result() as $row){
				$row 		= (array) $row;
				$other 		= "";
				$locat 		= "";
				$btn 		= "";
				$tb_free 	= "";
				$free 		= "";
				if($row["level_r"] == 1){
					$bg = ($bg == "#d6e8ff" ? "#fff":"#d6e8ff");
					$locat = "";
					$sql2 = "
						select case when locat='ALL' then 'ทั้งหมด' else locat end as locat 
						from {$this->MAuth->getdb('std_pricelist_locat')} a
						where a.id='".$row["id"]."' and a.plrank='".$row["plrank"]."'
					";
					//echo $sql2; exit;
					$query2 = $this->db->query($sql2);
					if($query2->row()){
						foreach($query2->result() as $row2){
							if($locat != ""){ $locat .= ","; }
							$locat .= $row2->locat;
						}
					}
					
					$other = "
						<td rowspan='".$row["maxR"]."' style='vertical-align:text-top;'>".++$NRow."</td>
						<td rowspan='".$row["maxR"]."' style='vertical-align:text-top;'>".$row["model"]."</td>
						<td rowspan='".$row["maxR"]."' style='max-width:150px;white-space:normal;vertical-align:text-top;'>".$row["name"]."</td>
						<td rowspan='".$row["maxR"]."' style='max-width:150px;white-space:normal;vertical-align:text-top;'>".$row["details"]."</td>
						<td rowspan='".$row["maxR"]."' style='vertical-align:text-top;'>".$row["color"]."</td>
						<td rowspan='".$row["maxR"]."' style='vertical-align:text-top;'>".number_format($row["price"],0)."</td>
						<td rowspan='".$row["maxR"]."' style='vertical-align:text-top;'>".number_format($row["pricespecial"],0)."</td>
						<td rowspan='".$row["maxR"]."' style='vertical-align:text-top;'>".$row["ACTICOD"]."</td>
					";
					$locat = "
						<td rowspan='".$row["maxR"]."' style='min-width:200px;width:200px;white-space:normal;vertical-align:text-top;'>".$locat."</td>
						<td rowspan='".$row["maxR"]."' style='vertical-align:text-top;'>".$this->Convertdate(2,$row["event_s"])." ถึง ".($row["event_e"] == "" ? "จนกว่าจะมีการเปลี่ยนแปลง":$this->Convertdate(2,$row["event_e"]))."</td>
					";
					
					$btn = "
						<td rowspan='".$row["maxR"]."' ><i class='stddetail btn btn-xs btn-warning glyphicon glyphicon-edit' STDID='".$row["id"]."' STDRank='".$row["plrank"]."' style='cursor:pointer;'> แก้ไข  </i></td>
					";
					
					/*ของแถม*/
					$sqlfree = "
						select nopay_s,nopay_e,free_rate
						from {$this->MAuth->getdb('std_package')} a
						where a.id='".$row["id"]."' and a.plrank='".$row["plrank"]."'
					";
					$queryfree = $this->db->query($sqlfree);
					
					if($queryfree->row()){
						foreach($queryfree->result() as $rowfree){
							$rowfree = (array) $rowfree;
							$tb_free .= "
								<tr>
									<td style='mso-number-format:&#34;\@&#34;;'>".$rowfree["nopay_s"]." - ".($rowfree["nopay_e"] == ""?"ขึ้นไป":$rowfree["nopay_e"])."</td>
									<td>".number_format($rowfree["free_rate"],0)."</td>
								</tr>
							";
						}
					}
					
					if($tb_free != ""){
						$tb_free = "
							<td rowspan='".$row["maxR"]."'>
								<table class='table table-bordered' cellspacing='0'>".$tb_free."</table>
							</td>
						";
					}
					
					$size_free_col = $row["free_col"]; 
					for($i=1;$i<=($size_free_col);$i++){
						if($row["C".$i] != ""){
							$ex = explode("][",$row["C".$i]);
							$free .= "<td rowspan='".$row["maxR"]."'>".$ex[0].($ex[1] == "max" ? " งวด ขึ้นไป":" - ".$ex[1]." งวด")."] [แถม :: ".number_format(str_replace("]","",$ex[2]),0)."]</td>";
						}else{
							$free .= "<td rowspan='".$row["maxR"]."'></td>";
						}
					}
				}
				
				
				$data_excel .= "
					<tr style='background-color:{$bg};vertical-align:text-top;'>
						{$other}
						<td>".$row["level_r"]."</td>
						<td>".number_format($row["dwnrate_s"],0)." - ".($row["dwnrate_e"] == "" ? "ขึ้นไป":number_format($row["dwnrate_e"],0))."</td>
						<td>".$row["interest_rate"]."</td>
						<td>".$row["interest_rate2"]."</td>
						".$free."
						<td>".number_format($row["insurance"],0)."</td>
						<td>".number_format($row["transfers"],0)."</td>
						<td>".number_format($row["regist"],0)."</td>
						<td>".number_format($row["act"],0)."</td>
						<td>".($row["coupon"] == "" ? "ไม่ระบุ":number_format($row["coupon"],0))."</td>
						{$locat}
					</tr>
				";
			}
		}
		
		$html_excel = "
			<table cellspacing='0' border=1 width='calc(100% - 1px)'>
				<thead>
					<tr align='center'>
						<th style='vertical-align:middle;background-color:#c8e6b7;'>###</th>
						<th style='vertical-align:middle;background-color:#c8e6b7;'>รุ่น (แบบ)</th>
						<th style='vertical-align:middle;background-color:#c8e6b7;'>ชื่อเรียก</th>
						<th style='vertical-align:middle;background-color:#c8e6b7;'>ลักษณะ</th>
						<th style='vertical-align:middle;background-color:#c8e6b7;'>สี</th>
						<th style='vertical-align:middle;background-color:#c8e6b7;'>ราคาขาย</th>
						<th style='vertical-align:middle;background-color:#c8e6b7;'>ราคาผลัด</th>
						<th style='vertical-align:middle;background-color:#c8e6b7;'>กิจกรรมการขาย</th>
						<th style='vertical-align:middle;background-color:#c8e6b7;'>ขั้นเงินดาวน์</th>
						<th style='vertical-align:middle;background-color:#c8e6b7;'>ช่วงเงินดาวน์</th>
						<th style='vertical-align:middle;background-color:#c8e6b7;'>ดอกเบี้ย</th>
						<th style='vertical-align:middle;background-color:#c8e6b7;'>ดอกเบี้ย</th>
						<th style='vertical-align:middle;background-color:#c8e6b7;' colspan='".$size_free_col."'>ของแถม</th>
						<th style='vertical-align:middle;background-color:#c8e6b7;'>ประกัน</th>
						<th style='vertical-align:middle;background-color:#c8e6b7;'>โอน</th>
						<th style='vertical-align:middle;background-color:#c8e6b7;'>ทะเบียน</th>
						<th style='vertical-align:middle;background-color:#c8e6b7;'>พรบ</th>
						<th style='vertical-align:middle;background-color:#c8e6b7;'>คูปอง</th>
						<th style='vertical-align:middle;background-color:#c8e6b7;'>สาขา</th>
						<th style='vertical-align:middle;background-color:#c8e6b7;'>วันที่บังคับใช้ std.</th>
					</tr>
				</thead>	
				<tbody style='vertical-align:text-top;'>
					".$data_excel."
				</tbody>
			</table>
		";
		
		return $html_excel;
	}
	
	function searchDetail(){
		$stdid 		= $_POST["stdid"];
		$stdrank 	= $_POST["stdrank"];
		
		/* ข้อมูล std */
		$sql = "
			select a.id ,b.plrank
				,a.model
				,a.baab
				,a.color
				,b.price
				,b.pricespecial
				,b.ACTICOD
				,b.name
				,b.details
				,convert(char(8),b.event_s,112) as event_s
				,convert(char(8),b.event_e,112) as event_e
			from {$this->MAuth->getdb('std_vehicles')} a
			left join {$this->MAuth->getdb('std_pricelist')} b on a.id=b.id
			where a.id='{$stdid}' and b.plrank={$stdrank}
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$arrs = array("null"=>"");
		$NRow = 1;
		if($query->row()){
			foreach($query->result() as $row){
				foreach($row as $key => $val){
					switch($key){
						case 'event_s':
						case 'event_e':
							$arrs[$key] = $this->Convertdate(2,$val); 
							break;
						default: 
							$arrs[$key] = $val;
							break;
					}
				}
			}
		}
		
		/* ข้อมูล std ใช้กับสาขาไหนบ้าง */
		$sql = "
			select * from {$this->MAuth->getdb('std_pricelist_locat')} 
			where id='{$stdid}' and plrank={$stdrank}
		";
		$query = $this->db->query($sql);
		
		if($query->row()){
			foreach($query->result() as $row){
				$arrs["locat"][] = $row->locat;
			}
		}
		
		$sql = "select * from {$this->MAuth->getdb('INVLOCAT')} where LOCATCD<>'TRANS'";
		$q = $this->db->query($sql);
		
		$locatopt = "";
		if($q->row()){
			$i=1;
			foreach($q->result() as $row){
				$locatopt .= "<option value='{$row->LOCATCD}' title='{$row->LOCATNM}' 
					data-toggle='tooltip'
					data-placement='top'
					data-html='true'
					data-original-title='{$row->LOCATNM}'
					".($arrs["locat"][0] == "ALL" ? "selected" : (in_array($row->LOCATCD,$arrs["locat"]) ? "selected":""))."
				>{$row->LOCATCD}</option>";
			}
		}
		
		/* ข้อมูล std การดาวน์รถ  */
		$sql = "
			select * from {$this->MAuth->getdb('std_down')} 
			where id='{$stdid}' and plrank={$stdrank}
			order by level_r
		";
		$query = $this->db->query($sql);
		
		if($query->row()){
			$arrs["down"] = "";
			foreach($query->result() as $row){
				$arrs["down"] .= "
					<tr>
						<td>".$row->dwnrate_s."</td>
						<td>".$row->dwnrate_e."</td>
						<td>".$row->interest_rate.($row->interest_rate2 == "" ? "":" (".$row->interest_rate2.")")."</td>
						<td>".$row->insurance."</td>
						<td>".$row->transfers."</td>
						<td>".$row->regist."</td>
						<td>".$row->act."</td>
						<td>".$row->coupon."</td>
						<!-- td>
							<button class='editDwn btn-warning'".
								"formdwns='".$row->dwnrate_s."'".
								"formdwne='".$row->dwnrate_e."'".
								"forminterest='".$row->interest_rate."'".
								"forminterest2='".$row->interest_rate2."'".
								"forminsurance='".$row->insurance."'".
								"formtrans='".$row->transfers."'".
								"formregist='".$row->regist."'".
								"formact='".$row->act."'".
								"formcoupon='".$row->coupon."'".
								" disabled ".
							"><span class='glyphicon glyphicon-edit'> แก้ไข</span></button>
							<button class='deleteDwn btn-danger'".
								"formdwns='".$row->dwnrate_s."'".
								"formdwne='".$row->dwnrate_e."'".
								"forminterest='".$row->interest_rate."'".
								"forminterest2='".$row->interest_rate2."'".
								"forminsurance='".$row->insurance."'".
								"formtrans='".$row->transfers."'".
								"formregist='".$row->regist."'".
								"formact='".$row->act."'".
								"formcoupon='".$row->coupon."'".
								" disabled ".
							"><span class='glyphicon glyphicon-trash'> ลบ</span></button>
						</td -->
					</tr>
				";
			}
		}
		
		/* ข้อมูล std ของแถม  */
		$sql = "
			select * from {$this->MAuth->getdb('std_package')} 
			where id='{$stdid}' and plrank={$stdrank}
			order by nopay_s,free_rate
		";
		$query = $this->db->query($sql);
		
		if($query->row()){
			$arrs["package"] = "";
			foreach($query->result() as $row){
				$arrs["package"] .= "
					<tr>
						<td>".$row->nopay_s."</td>
						<td>".$row->nopay_e."</td>
						<td>".$row->free_rate."</td>
						<td style='max-width:230px;white-space:normal;'>".$row->detail."</td>
						<!-- td>
							<button class='editFree btn-warning'".
								"formnopays='".$row->nopay_s."'".
								"formnopaye='".$row->nopay_e."'".
								"formrate='".$row->free_rate."'".
								"formdetail='".$row->detail."'".
								" disabled ".
							"><span class='glyphicon glyphicon-edit'> แก้ไข</span></button>
							<button class='deleteFree btn-danger'".
								"formnopays='".$row->nopay_s."'".
								"formnopaye='".$row->nopay_e."'".
								"formrate='".$row->free_rate."'".
								"formdetail='".$row->detail."'".
								" disabled ".
							"><span class='glyphicon glyphicon-trash'> ลบ</span></button>
						</td -->
					</tr>
				";
			}
		}
		
		$html = "
			<div id='panel'>
				<div class='row' style='border:1px dotted #aaa;background-color:#d5f2ba;'>
					<h3>
						<div class='col-sm-10 col-sm-offset-1 text-primary'>
							<span class='toggleData glyphicon glyphicon-minus' thisc='toggleData1' style='cursor:pointer;'>&emsp;ข้อมูลรถ</span>
						</div>
					</h3>
					<div class='row'>
						<div class='col-sm-2 col-sm-offset-1'>
							<div class='col-sm-12'>	
								<div class='form-group'>	
									รุ่น
									<select id='FMODEL' class='form-control'><option>{$arrs["model"]}</option></select>
								</div>
							</div>
							<div class='col-sm-12'>	
								<div class='form-group'>	
									แบบ
									<select id='FBAAB' class='form-control'><option>{$arrs["baab"]}</option></select>
								</div>
							</div>
							<div class='col-sm-12'>	
								<div class='form-group'>
									สี
									<select id='FCOLOR' class='form-control'><option>{$arrs["color"]}</option></select>
								</div>
							</div>
							<div class='col-sm-12'>
								<div class='form-group'>
									กิจกรรมการขาย
									<select id='FACTI' class='form-control'><option>{$arrs["ACTICOD"]}</option></select>
								</div>
							</div>
						</div>
						
						<div class='col-sm-4'>
							<div class='col-sm-6'>
								<div class='form-group'>
									บังคับใช้ จาก
									<input type='text' id='FEVENTS' value='{$arrs["event_s"]}' disabled class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' maxlength=10>
								</div>
							</div>
							<div class='col-sm-6'>
								<div class='form-group'>
									บังคับใช้ ถึง
									<input type='text' id='FEVENTE' value='{$arrs["event_e"]}' ".($arrs["event_e"] == ""?"":"disabled")." class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' maxlength=10>
								</div>
							</div>
							<div class='col-sm-12'>
								<div class='form-group'>
									ชื่อเรียก
									<input type='text' id='FEVENTNAME' value='{$arrs["name"]}' class='form-control input-sm' maxlength=500>
								</div>
							</div>
							<div class='col-sm-12'>	
								<div class='form-group'>	
									ลักษณะ
									<textarea type='text' id='FDETAIL' class='form-control' rows='2' maxlength=2000>{$arrs["details"]}</textarea>
								</div>
							</div>
							<div class='col-sm-6'>
								<div class='form-group'>
									ราคาสด
									<input type='text' id='FPRICE' value='{$arrs["price"]}' class='form-control input-sm jzAllowNumber'>
								</div>
							</div>
							<div class='col-sm-6'>
								<div class='form-group'>
									ราคาผลัด
									<input type='text' id='FPRICE2' value='{$arrs["pricespecial"]}' class='form-control input-sm jzAllowNumber'>
								</div>
							</div>
						</div>
						
						<div class='col-sm-4'>
							<div class='col-sm-12'>
								<div class='form-group'>
									สาขา
									<select id='FLOCAT' multiple='multiple' size='10' name='duallistbox_demo1[]'>{$locatopt}</select>
								</div>
							</div>
						</div>
					</div>
					
					<div class='col-sm-7' style='border:1px dotted #aaa;background-color:#fff;'>
						<div id='table-fixed-stdfa' class='col-sm-12' style='width:100%;overflow:auto;font-size:8pt;'>
							<table id='table-stdfa' class='table table-bordered' cellspacing='0' width='calc(100% - 1px)'>
								<thead>	
									<tr align='center'>
										<th colspan='9' style='vertical-align:middle;background-color:#c8e6b7;'>
											Standart การดาวน์รถ
										</th>
									</tr>
									<tr align='center'>
										<th style='vertical-align:middle;background-color:#c8e6b7;'>เงินดาวน์</th>
										<th style='vertical-align:middle;background-color:#c8e6b7;'>เงินดาวน์</th>
										<th style='vertical-align:middle;background-color:#c8e6b7;'>ดอกเบี้ย</th>
										<th style='vertical-align:middle;background-color:#c8e6b7;'>ค่าประกัน</th>
										<th style='vertical-align:middle;background-color:#c8e6b7;'>ค่าโอน</th>
										<th style='vertical-align:middle;background-color:#c8e6b7;'>ค่าทบ. จดใหม่</th>
										<th style='vertical-align:middle;background-color:#c8e6b7;'>ค่าพรบ.</th>
										<th style='vertical-align:middle;background-color:#c8e6b7;'>คูปอง</th>
										<!-- th style='vertical-align:middle;background-color:#c8e6b7;'>###</th -->
									</tr>
								</thead>
								<tbody style='background-color:whiteSmoke;'>
									{$arrs["down"]}
								</tbody>
								<tfoot>
									<tr align='center'>
										<th style='vertical-align:middle;background-color:#c8e6b7;'>เงินดาวน์</th>
										<th style='vertical-align:middle;background-color:#c8e6b7;'>เงินดาวน์</th>
										<th style='vertical-align:middle;background-color:#c8e6b7;'>ดอกเบี้ย</th>
										<th style='vertical-align:middle;background-color:#c8e6b7;'>ค่าประกัน</th>
										<th style='vertical-align:middle;background-color:#c8e6b7;'>ค่าโอน</th>
										<th style='vertical-align:middle;background-color:#c8e6b7;'>ค่าทบ. จดใหม่</th>
										<th style='vertical-align:middle;background-color:#c8e6b7;'>ค่าพรบ.</th>
										<th style='vertical-align:middle;background-color:#c8e6b7;'>คูปอง</th>
										<!-- th style='vertical-align:middle;background-color:#c8e6b7;'>###</th -->
									</tr>
									<!-- tr align='center'>
										<th colspan='9' style='vertical-align:middle;background-color:#c8e6b7;'>
											<button id='btnAddDwn' class='btn btn-warning btn-block'><span class='glyphicon glyphicon-plus'> เพิ่ม</span></button>
										</th>
									</tr -->
								</tfoot>
							</table>
						</div>
					</div>
					<div class='col-sm-5' style='border:1px dotted #aaa;background-color:#fff;'>	
						<div id='table-fixed-stdfree' class='col-sm-12' style='width:100%;overflow:auto;font-size:8pt;'>
							<table id='table-stdfree' class='table table-bordered' cellspacing='0' width='calc(100% - 1px)'>
								<thead>
									<tr align='center'>
										<th colspan='5' style='vertical-align:middle;background-color:#c8e6b7;'>
											Standart ของแถม
										</th>
									</tr>
									<tr align='center'>
										<th style='vertical-align:middle;background-color:#c8e6b7;'>งวด</th>
										<th style='vertical-align:middle;background-color:#c8e6b7;'>งวด</th>
										<th style='vertical-align:middle;background-color:#c8e6b7;'>ของแถม</th>
										<th style='vertical-align:middle;background-color:#c8e6b7;'>หมายเหตุ</th>
										<!-- th style='vertical-align:middle;background-color:#c8e6b7;'>###</th -->
									</tr>
								</thead>
								<tbody style='background-color:whiteSmoke;'>
									{$arrs["package"]}
								</tbody>
								<tfoot>
									<tr align='center'>
										<th style='vertical-align:middle;background-color:#c8e6b7;'>งวด</th>
										<th style='vertical-align:middle;background-color:#c8e6b7;'>งวด</th>
										<th style='vertical-align:middle;background-color:#c8e6b7;'>ของแถม</th>
										<th style='vertical-align:middle;background-color:#c8e6b7;'>หมายเหตุ</th>
										<!-- th style='vertical-align:middle;background-color:#c8e6b7;'>###</th -->
									</tr>
									<!-- tr align='center'>
										<th colspan='5' style='vertical-align:middle;background-color:#c8e6b7;'>
											<button id='btnAddFree' class='btn btn-warning btn-block'><span class='glyphicon glyphicon-plus'> เพิ่ม</span></button>
										</th>
									</tr -->
								</tfoot>
							</table>
						</div>
					</div>
					
					<div class='col-sm-12'>
						<br>
						<div class='col-sm-2 col-sm-offset-10'>	
							<button id='btnSave' class='btn btn-primary btn-block' stdid='{$stdid}' plrank='{$stdrank}'><span class='glyphicon glyphicon-floppy-disk'> บันทึก</span></button>
							<br>
						</div>						
					</div>					
				</div>
			</div>
		";
		
		$response = array("html"=>$html,"status"=>true);
		echo json_encode($response);
	}
	
	function loadform(){
		$sql = "select * from {$this->MAuth->getdb('INVLOCAT')} where LOCATCD<>'TRANS'";
		$q = $this->db->query($sql);
		
		$locatopt = "";
		if($q->row()){
			$i=1;
			foreach($q->result() as $row){
				$locatopt .= "<option value='{$row->LOCATCD}' title='{$row->LOCATNM}' 
					data-toggle='tooltip'
					data-placement='top'
					data-html='true'
					data-original-title='{$row->LOCATNM}'
				>{$row->LOCATCD}</option>";
			}
		}
		
		
		$html = "
			<div id='panel'>
				<div class='row' style='border:1px dotted #aaa;background-color:#d5f2ba;'>
					<h3>
						<div class='col-sm-10 col-sm-offset-1 text-primary'>
							<span class='toggleData glyphicon glyphicon-minus' thisc='toggleData1' style='cursor:pointer;'>&emsp;ข้อมูลรถ</span>
						</div>
					</h3>
					<div class='row'>
						<div class='col-sm-2 col-sm-offset-1'>
							<div class='col-sm-12'>	
								<div class='form-group'>	
									รุ่น
									<select id='FMODEL' class='form-control'></select>
								</div>
							</div>
							<div class='col-sm-12'>	
								<div class='form-group'>	
									แบบ
									<select id='FBAAB' class='form-control'></select>
									<h6 class='text-danger'><i>(ทุกแบบ ให้เว้นว่างไว้)</i></h6>
								</div>
							</div>
							<div class='col-sm-12'>	
								<div class='form-group'>	
									สี
									<select id='FCOLOR' class='form-control'></select>
									<h6 class='text-danger'><i>(ทุกสี ให้เว้นว่างไว้)</i></h6>
								</div>
							</div>
							<div class='col-sm-12'>	
								<div class='form-group'>	
									กิจกรรมการขาย
									<select id='FACTI' class='form-control'></select>
									<h6 class='text-danger'><i>(ทั้งหมด ให้เว้นว่างไว้)</i></h6>
								</div>
							</div>
						</div>
						
						
						<div class='col-sm-4'>
							<div class='col-sm-6'>	
								<div class='form-group'>	
									บังคับใช้ จาก
									<input type='text' id='FEVENTS' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' maxlength=10>
								</div>
							</div>
							<div class='col-sm-6'>	
								<div class='form-group'>	
									บังคับใช้ ถึง
									<input type='text' id='FEVENTE' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' maxlength=10>
								</div>
							</div>
							<div class='col-sm-12'>	
								<div class='form-group'>	
									ชื่อเรียก
									<input type='text' id='FEVENTNAME' class='form-control input-sm' maxlength=500>
								</div>
							</div>
							<div class='col-sm-12'>	
								<div class='form-group'>	
									ลักษณะ
									<textarea type='text' id='FDETAIL' class='form-control' rows='2' maxlength=2000></textarea>
								</div>
							</div>
							<div class='col-sm-6'>	
								<div class='form-group'>	
									ราคาสด
									<input type='text' id='FPRICE' class='form-control input-sm jzAllowNumber'>
								</div>
							</div>
							<div class='col-sm-6'>	
								<div class='form-group'>	
									ราคาผลัด
									<input type='text' id='FPRICE2' class='form-control input-sm jzAllowNumber'>
								</div>
							</div>
						</div>
						
						
						<div class='col-sm-4'>
							<div class='col-sm-12'>	
								<div class='form-group'>
									สาขา
									<select id='FLOCAT' multiple='multiple' size='10' name='duallistbox_demo1[]'>{$locatopt}</select>
								</div>
							</div>
						</div>
					</div>
					
					<div class='col-sm-7' style='border:1px dotted #aaa;background-color:#fff;'>
						<div id='table-fixed-stdfa' class='col-sm-12' style='width:100%;overflow:auto;font-size:8pt;'>
							<table id='table-stdfa' class='table table-bordered' cellspacing='0' width='calc(100% - 1px)'>
								<thead>	
									<tr align='center'>
										<th colspan='8' style='vertical-align:middle;background-color:#c8e6b7;'>
											Standart การดาวน์รถ
										</th>
									</tr>
									<tr align='center'>
										<th style='vertical-align:middle;background-color:#c8e6b7;'>เงินดาวน์</th>
										<th style='vertical-align:middle;background-color:#c8e6b7;'>ดอกเบี้ย</th>
										<th style='vertical-align:middle;background-color:#c8e6b7;'>ค่าประกัน</th>
										<th style='vertical-align:middle;background-color:#c8e6b7;'>ค่าโอน</th>
										<th style='vertical-align:middle;background-color:#c8e6b7;'>ค่าทบ. จดใหม่</th>
										<th style='vertical-align:middle;background-color:#c8e6b7;'>ค่าพรบ.</th>
										<th style='vertical-align:middle;background-color:#c8e6b7;'>คูปอง</th>
										<th style='vertical-align:middle;background-color:#c8e6b7;'>###</th>
									</tr>
								</thead>
								<tbody style='background-color:whiteSmoke;'>
									
								</tbody>
								<tfoot>
									<tr align='center'>
										<th style='vertical-align:middle;background-color:#c8e6b7;'>เงินดาวน์</th>
										<th style='vertical-align:middle;background-color:#c8e6b7;'>ดอกเบี้ย</th>
										<th style='vertical-align:middle;background-color:#c8e6b7;'>ค่าประกัน</th>
										<th style='vertical-align:middle;background-color:#c8e6b7;'>ค่าโอน</th>
										<th style='vertical-align:middle;background-color:#c8e6b7;'>ค่าทบ. จดใหม่</th>
										<th style='vertical-align:middle;background-color:#c8e6b7;'>ค่าพรบ.</th>
										<th style='vertical-align:middle;background-color:#c8e6b7;'>คูปอง</th>
										<th style='vertical-align:middle;background-color:#c8e6b7;'>###</th>
									</tr>
									<tr align='center'>
										<th colspan='8' style='vertical-align:middle;background-color:#c8e6b7;'>
											<button id='btnAddDwn' class='btn btn-warning btn-block'><span class='glyphicon glyphicon-plus'> เพิ่ม</span></button>
										</th>
									</tr>
								</tfoot>
							</table>
						</div>
					</div>
					<div class='col-sm-5' style='border:1px dotted #aaa;background-color:#fff;'>	
						<div id='table-fixed-stdfree' class='col-sm-12' style='width:100%;overflow:auto;font-size:8pt;'>
							<table id='table-stdfree' class='table table-bordered' cellspacing='0' width='calc(100% - 1px)'>
								<thead>
									<tr align='center'>
										<th colspan='6' style='vertical-align:middle;background-color:#c8e6b7;'>
											Standart ของแถม
										</th>
									</tr>
									<tr align='center'>
										<th style='vertical-align:middle;background-color:#c8e6b7;'>งวด</th>
										<th style='vertical-align:middle;background-color:#c8e6b7;'>ของแถม</th>
										<th style='vertical-align:middle;background-color:#c8e6b7;'>หมายเหตุ</th>
										<th style='vertical-align:middle;background-color:#c8e6b7;'>###</th>
									</tr>
								</thead>
								<tbody style='background-color:whiteSmoke;'>
									
								</tbody>
								<tfoot>
									<tr align='center'>
										<th style='vertical-align:middle;background-color:#c8e6b7;'>งวด</th>
										<th style='vertical-align:middle;background-color:#c8e6b7;'>ของแถม</th>
										<th style='vertical-align:middle;background-color:#c8e6b7;'>หมายเหตุ</th>
										<th style='vertical-align:middle;background-color:#c8e6b7;'>###</th>
									</tr>
									<tr align='center'>
										<th colspan='6' style='vertical-align:middle;background-color:#c8e6b7;'>
											<button id='btnAddFree' class='btn btn-warning btn-block'><span class='glyphicon glyphicon-plus'> เพิ่ม</span></button>
										</th>
									</tr>
								</tfoot>
							</table>
						</div>
					</div>
					
					<div class='col-sm-12'>
						<br>
						<div class='col-sm-2 col-sm-offset-10'>	
							<button id='btnSave' class='btn btn-primary btn-block'><span class='glyphicon glyphicon-floppy-disk'> บันทึก</span></button>
						</div>
						<br>
					</div>
				</div>
			</div>
		";
		
		$response = array("html"=>$html);
		echo json_encode($response);
	}
	
	function dataResv(){
		$resvno = $_POST["resvno"];
		
		$sql = "
			select a.RESVNO,a.RESPAY,a.STRNO,a.MODEL,a.BAAB,a.COLOR
				,case when a.STAT='N' then 'รถใหม่'  else 'รถเก่า' end as STAT
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
			from {$this->MAuth->getdb('ARRESV')} a 
			left join (
				select ROW_NUMBER() over(partition by STRNO order by STRNO,sdate desc) r,* 
				from {$this->MAuth->getdb('ARHOLD')}
			) as b on a.STRNO=b.STRNO and b.r=1
			left join {$this->MAuth->getdb('CUSTMAST')} c on a.CUSCOD=c.CUSCOD
			left join {$this->MAuth->getdb('CUSTADDR')} d on c.CUSCOD=d.CUSCOD and c.ADDRNO=d.ADDRNO
			left join {$this->MAuth->getdb('SETAUMP')} e on d.AUMPCOD=e.AUMPCOD
			left join {$this->MAuth->getdb('SETPROV')} f on e.PROVCOD=f.PROVCOD
			where RESVNO='".$resvno."'
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
						default:  $data[$key] = $val; break;
					}
				}
			}
		}
		
		$response = array("html"=>$data);
		echo json_encode($response);
	}
	
	function dataSTR(){
		$strno = $_POST["strno"];
		
		$sql = "
			select a.STRNO,a.MODEL,a.BAAB,a.COLOR
				,case when a.STAT='N' then 'รถใหม่'  else 'รถเก่า' end as STAT
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
		
		$response = array("html"=>$data);
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
					ID,LOCAT,RESVNO,RESVAMT,DWN,DWN_INSURANCE,NOPAY,STRNO,MODEL
					,BAAB,COLOR,STAT,SDATE,YDATE,PRICE,ANSTAT,INSBY,INSDT
				) select @ANID,".$arrs["locat"].",".$arrs["resvno"].",".$arrs["resvAmt"].",".$arrs["dwnAmt"]."
					,".$arrs["insuranceAmt"].",".$arrs["nopay"].",".$arrs["strno"].",".$arrs["model"]."
					,".$arrs["baab"].",".$arrs["color"].",".$arrs["stat"].",".$arrs["sdateold"].",".$arrs["ydate"]."
					,".$arrs["price"].",'I','".$this->sess["IDNo"]."',getdate();
				
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
					set EMP=''
						,EMPTEL=''
						,MNG=''
						,MNGTEL=''						
					where ID=@ANID
				end
				else 
				begin
					insert into {$this->MAuth->getdb('ARANALYZEDATA')}(ID,EMP,EMPTEL,MNG,MNGTEL)
					select @ANID,'','','','';
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
		echo $sql; exit;
	}
	
	function formStdDWN(){
		$html = "
			<div class='col-sm-6'>
				<div class='form-group'>
					เงินดาวน์ จาก
					<input type='text' id='formdwns' class='form-control input-sm jzAllowNumber' value='".$_POST["formdwns"]."'>
				</div>
			</div>
			<div class='col-sm-6'>
				<div class='form-group'>
					เงินดาวน์ ถึง
					<input type='text' id='formdwne' class='form-control input-sm jzAllowNumber' value='".$_POST["formdwne"]."'>
				</div>
			</div>
			<div class='col-sm-6'>
				<div class='form-group'>
					ดอกเบี้ย ทั่วไป
					<input type='text' id='forminterest' class='form-control input-sm jzAllowNumber' value='".$_POST["forminterest"]."'>
				</div>
			</div>
			<div class='col-sm-6'>
				<div class='form-group'>
					ดอกเบี้ย ข้าราชการ
					<input type='text' id='forminterest2' class='form-control input-sm jzAllowNumber' value='".$_POST["forminterest2"]."'>
				</div>
			</div>
			
			<div class='col-sm-6'>
				<div class='form-group'>
					ค่าประกัน
					<input type='text' id='forminsurance' class='form-control input-sm jzAllowNumber' value='".$_POST["forminsurance"]."'>
				</div>
			</div>
			<div class='col-sm-6'>
				<div class='form-group'>
					ค่าโอน
					<input type='text' id='formtrans' class='form-control input-sm jzAllowNumber' value='".$_POST["formtrans"]."'>
				</div>
			</div>
			<div class='col-sm-6'>
				<div class='form-group'>
					ค่าทบ. จดใหม่
					<input type='text' id='formregist' class='form-control input-sm jzAllowNumber' value='".$_POST["formregist"]."'>
				</div>
			</div>
			<div class='col-sm-6'>
				<div class='form-group'>
					ค่าพรบ.
					<input type='text' id='formact' class='form-control input-sm jzAllowNumber' value='".$_POST["formact"]."'>
				</div>
			</div>
			<div class='col-sm-6'>
				<div class='form-group'>
					คูปอง
					<input type='text' id='formcoupon' class='form-control input-sm jzAllowNumber' value='".$_POST["formcoupon"]."'>
				</div>
			</div>
			<div class='col-sm-10 col-sm-offset-1'>
				<button id='btnSWNADD' class='btn btn-warning btn-block'><span class='glyphicon glyphicon-".($_POST["formevent"] == "add"?"plus":"edit")."'> ".($_POST["formevent"] == "add"?"เพิ่ม":"แก้ไข")."</span></button>
			</div>
		";
		
		$response = array("html"=>$html);
		echo json_encode($response);
	}
	
	function formStdFREE(){
		$html = "
			<div class='col-sm-6'>
				<div class='form-group'>
					จากงวดที่
					<input type='text' id='formnopays' class='form-control input-sm jzAllowNumber' value='".$_POST["formnopays"]."'>
				</div>
			</div>
			<div class='col-sm-6'>
				<div class='form-group'>
					ถึงงวดที่
					<input type='text' id='formnopaye' class='form-control input-sm jzAllowNumber' value='".$_POST["formnopaye"]."'>
				</div>
			</div>
			<div class='col-sm-6'>
				<div class='form-group'>
					ของแถม
					<input type='text' id='formrate' class='form-control input-sm jzAllowNumber' value='".$_POST["formrate"]."'>
				</div>
			</div>
			<div class='col-sm-12'>
				<div class='form-group'>
					หมายเหตุ
					<textarea id='formdetail' class='form-control input-sm'>".$_POST["formdetail"]."</textarea>
				</div>
			</div>
			<div class='col-sm-10 col-sm-offset-1'>
				<button id='btnSWNADD' class='btn btn-warning btn-block'><span class='glyphicon glyphicon-".($_POST["formevent"] == "add"?"plus":"edit")."'> ".($_POST["formevent"] == "add"?"เพิ่ม":"แก้ไข")."</span></button>
			</div>
		";
		
		$response = array("html"=>$html);
		echo json_encode($response);
	}
	
	function certificate_std(){
		if(@$_POST["MODEL"] == ""){
			$response["error"] = true;
			$response["msg"] = 'โปรดระบุรุ่น std. ก่อนครับ';
			echo json_encode($response); exit;
		}
		
		if(@$_POST["EVENTS"] == ""){
			$response["error"] = true;
			$response["msg"] = 'โปรดระบุ วันที่เริ่มบังคับใช้ std. ก่อนครับ';
			echo json_encode($response); exit;
		}
		
		if(@$_POST["EVENTNAME"] == ""){
			$response["error"] = true;
			$response["msg"] = 'โปรดระบุ ชื่อเรียก std. ก่อนครับ';
			echo json_encode($response); exit;
		}
		
		if(@$_POST["DETAIL"] == ""){
			$response["error"] = true;
			$response["msg"] = 'โปรดระบุ ลักษณะของ std. ก่อนครับ';
			echo json_encode($response); exit;
		}
		
		if(!is_numeric(@$_POST["FPRICE"])){
			$response["error"] = true;
			$response["msg"] = 'โปรดระบุ ราคาขายสด ให้ถูกต้อง ก่อนครับ';
			echo json_encode($response); exit;
		}
		
		if(@$_POST["FPRICE"] < 1){
			$response["error"] = true;
			$response["msg"] = 'โปรดระบุ ราคาขายสด ให้ถูกต้อง ก่อนครับ';
			echo json_encode($response); exit;
		}
		
		if(!is_numeric(@$_POST["FPRICE2"])){
			$response["error"] = true;
			$response["msg"] = 'โปรดระบุ ราคาขายสด ให้ถูกต้อง ก่อนครับ';
			echo json_encode($response); exit;
		}
		
		if(@$_POST["FPRICE2"] < 1){
			$response["error"] = true;
			$response["msg"] = 'โปรดระบุ ราคาขายสด ให้ถูกต้อง ก่อนครับ';
			echo json_encode($response); exit;
		}
		
		if(@$_POST["STDDWN"] == ""){
			$response["error"] = true;
			$response["msg"] = 'โปรดระบุ Standart การดาวน์รถก่อนครับ';
			echo json_encode($response); exit;
		}
		
		if(@$_POST["STDFREE"] == ""){
			$response["error"] = true;
			$response["msg"] = 'โปรดระบุ Standart ของแถมก่อนครับ';
			echo json_encode($response); exit;
		}
		
		return;
	}
	
	function SaveSTD(){
		$this->certificate_std();
		
		$MODEL 		= ($_POST["MODEL"]);
		$BAAB 		= ($_POST["BAAB"] == "" ? "ALL":$_POST["BAAB"]);
		$COLOR 		= ($_POST["COLOR"] == "" ? "ALL":$_POST["COLOR"]);
		$ACTI 		= ($_POST["ACTI"] == "" ? "ALL":$_POST["ACTI"]);
		$EVENTS 	= $this->Convertdate(1,($_POST["EVENTS"]));
		$EVENTE 	= $this->Convertdate(1,($_POST["EVENTE"]));
		$EVENTNAME 	= ($_POST["EVENTNAME"]);
		$DETAIL 	= ($_POST["DETAIL"]);
		$FPRICE 	= ($_POST["FPRICE"] == "" ?"NULL":$_POST["FPRICE"]);
		$FPRICE2 	= ($_POST["FPRICE2"] == "" ?"NULL":$_POST["FPRICE2"]);
		$LOCAT 	 	= ($_POST["LOCAT"]);
		$STDDWN	 	= ($_POST["STDDWN"]);
		$STDFREE 	= ($_POST["STDFREE"]);
		
		/* กำหนดสาขา ที่ใช้กับ std */
		$sql = "
			if object_id('tempdb..#templocat') is not null drop table #templocat;
			create table #templocat (locat varchar(5),insby varchar(13),insdt datetime);
		";
		$this->db->query($sql);
		
		$slocat = sizeof($LOCAT);
		if($slocat > 0){
			for($i=0;$i<$slocat;$i++){
				$sql = "
					insert into #templocat 
					select '{$LOCAT[$i]}','{$this->sess["IDNo"]}',getdate();
				";
				$this->db->query($sql);
			}			
		}else{
			$sql = "
				insert into #templocat 
				select 'ALL','{$this->sess["IDNo"]}',getdate();
			";
			$this->db->query($sql);
		}
		
		/* เงินดาวน์ */
		$sdwn = sizeof($STDDWN);
		$qdwn = "";
		for($i=0;$i<$sdwn;$i++){
			if($qdwn != ""){ $qdwn .= " union all "; }
			
			$formdwns = "";
			if($STDDWN[$i]["formdwns"] == ""){
				$formdwns = "NULL";
			}else{
				$formdwns = $STDDWN[$i]["formdwns"];
			}
			$formdwne = "";
			if($STDDWN[$i]["formdwne"] == ""){
				$formdwne = "NULL";
			}else{
				$formdwne = $STDDWN[$i]["formdwne"];
			}
			
			if($qdwn == ""){
				$qdwn .= "
					select @id as id,@plrank as plrank,{$formdwns} as dwnrate_s,{$formdwne} as dwnrate_e
						,".($STDDWN[$i]["forminterest"] == "" ? "NULL":$STDDWN[$i]["forminterest"])." as interest_rate
						,".($STDDWN[$i]["forminterest2"] == "" ? "NULL":$STDDWN[$i]["forminterest2"])." as interest_rate2
						,".($STDDWN[$i]["forminsurance"] == "" ? "NULL":$STDDWN[$i]["forminsurance"])." as insurance
						,".($STDDWN[$i]["formtrans"] == "" ? "NULL":$STDDWN[$i]["formtrans"])." as transfers
						,".($STDDWN[$i]["formregist"] == "" ? "NULL":$STDDWN[$i]["formregist"])." as regist
						,".($STDDWN[$i]["formact"] == "" ? "NULL":$STDDWN[$i]["formact"])." as act
						,".($STDDWN[$i]["formcoupon"] == "" ? "NULL":$STDDWN[$i]["formcoupon"])." as coupon
						,'{$this->sess["IDNo"]}' as insby,getdate() as insdt,null as updby,null as upddt
				";
			}else{
				$qdwn .= "
					select @id,@plrank,{$formdwns},{$formdwne}
						,".($STDDWN[$i]["forminterest"] == "" ? "NULL":$STDDWN[$i]["forminterest"])."
						,".($STDDWN[$i]["forminterest2"] == "" ? "NULL":$STDDWN[$i]["forminterest2"])."
						,".($STDDWN[$i]["forminsurance"] == "" ? "NULL":$STDDWN[$i]["forminsurance"])."
						,".($STDDWN[$i]["formtrans"] == "" ? "NULL":$STDDWN[$i]["formtrans"])."
						,".($STDDWN[$i]["formregist"] == "" ? "NULL":$STDDWN[$i]["formregist"])."
						,".($STDDWN[$i]["formact"] == "" ? "NULL":$STDDWN[$i]["formact"])."
						,".($STDDWN[$i]["formcoupon"] == "" ? "NULL":$STDDWN[$i]["formcoupon"])."
						,'{$this->sess["IDNo"]}',getdate(),null,null
				";
			}
		}
		
		$qdwn = "
			insert into {$this->MAuth->getdb('std_down')} (
				id,plrank,level_r,dwnrate_s,dwnrate_e,interest_rate,interest_rate2 
				,insurance,transfers,regist,act,coupon,insby,insdt,updby,upddt
			)
			select id,plrank,ROW_NUMBER() over(order by dwnrate_s) r,dwnrate_s,dwnrate_e
				,interest_rate,interest_rate2,insurance,transfers,regist,act,coupon,insby,insdt,updby,upddt 
			from (
				{$qdwn}
			) as data
		";
		
		/* ของแถม */
		$sfree = sizeof($STDFREE);
		$qfree = "";
		for($i=0;$i<$sfree;$i++){
			if($qfree != ""){ $qfree .= " union all "; }
			if($qfree == ""){
				$qfree .= "select @id as id,@plrank as plrank,'{$STDFREE[$i]["formnopays"]}' as dwnrate_s,'{$STDFREE[$i]["formnopaye"]}' as dwnrate_e,'{$STDFREE[$i]["formrate"]}' as interest_rate,'{$STDFREE[$i]["formdetail"]}' as interest_rate2,'{$this->sess["IDNo"]}' as insby,getdate() as insdt,null as updby,null as upddt";
			}else{
				$qfree .= "select @id,@plrank,'{$STDFREE[$i]["formnopays"]}','{$STDFREE[$i]["formnopaye"]}','{$STDFREE[$i]["formrate"]}','{$STDFREE[$i]["formdetail"]}','{$this->sess["IDNo"]}',getdate(),null,null";
			}
		}
		
		$qfree = "
			insert into {$this->MAuth->getdb('std_package')} (id,plrank,nopay_s,nopay_e,free_rate,detail,insby,insdt,updby,upddt)
			{$qfree}
		";
		
		$sql = "
			if object_id('tempdb..#tempResult') is not null drop table #tempResult;
			create table #tempResult (error varchar(1),msg varchar(max));
			
			begin tran tsins
			begin try
				declare @id varchar(15)					= '';
				declare @plrank int						= '';
				declare @model varchar(20)				= '".$MODEL."';
				declare @baab varchar(20)				= '".$BAAB."';
				declare @color varchar(20)				= '".$COLOR."';
				declare @acticod varchar(20)			= '".$ACTI."';
				declare @eventname varchar(200)			= '".$EVENTNAME."';
				declare @eventdetail varchar(2000)		= '".$DETAIL."';
				declare @event_s datetime				= ".($EVENTS == "" ? "NULL":"'".$EVENTS."'").";
				declare @event_e datetime				= ".($EVENTE == "" ? "NULL":"'".$EVENTE."'").";
				declare @price decimal(18,2)			= '".$FPRICE."';
				declare @pricespc decimal(18,2)			= '".$FPRICE2."';
				declare @IDNo varchar(13)				= '".$this->sess["IDNo"]."';
				
				
				declare @invehicles int = (
					select COUNT(*) from {$this->MAuth->getdb('std_vehicles')}
					where model=@model and baab=@baab and color=@color
				);

				declare @inpricelist int = (
					select COUNT(*) from {$this->MAuth->getdb('std_pricelist')}
					where id in (
						select id from {$this->MAuth->getdb('std_vehicles')}
						where model=@model and baab=@baab and color=@color
					) and ACTICOD=@acticod
					and @event_s between event_s and isnull(event_e,GETDATE())
					and isnull(@event_e,GETDATE()) between event_s and isnull(event_e,GETDATE())
				);

				declare @inpricelistlocat int = (
					select COUNT(*) from {$this->MAuth->getdb('std_pricelist')} a
					left join {$this->MAuth->getdb('std_pricelist_locat')} b on a.id=b.id and a.plrank=b.plrank
					where a.id in (
						select id from {$this->MAuth->getdb('std_vehicles')}
						where model=@model and baab=@baab and color=@color
					) and a.ACTICOD=@acticod and (
						@event_s between event_s and isnull(event_e,GETDATE()) 
						or 
						isnull(@event_e,GETDATE()) between event_s and isnull(event_e,GETDATE())
					)
					and b.locat in (select locat from #templocat)	
					and b.id is not null
				);
				
				if(@invehicles = 0 and @inpricelist = 0)
				begin 
					set @id = 'PL'+CONVERT(varchar(8),GETDATE(),112)+'%'
					set @id = isnull((
						select left(id,10)+right(right(id,5) + 100001,5) from {$this->MAuth->getdb('std_vehicles')}
						where id like @id
					),left(@id,10)+'00001');					
					set @plrank = 1;
					
					insert into {$this->MAuth->getdb('std_vehicles')} (id,model,baab,color,insby,insdt)
					select @id,@model,@baab,@color,@IDNo,getdate();
					
					insert into {$this->MAuth->getdb('std_pricelist')} (id,plrank,price,pricespecial,ACTICOD,name,details,event_s,event_e,insby,insdt)
					select @id,@plrank,@price,@pricespc,@acticod,@eventname,@eventdetail,@event_s,@event_e,@IDNo,getdate();
					
					insert into {$this->MAuth->getdb('std_pricelist_locat')} (id,plrank,locat,insby,insdt) 
					select @id,@plrank,locat,insby,insdt from #templocat
					order by insdt
				end 
				else if(@invehicles > 0 and @inpricelist >= 0 and @inpricelistlocat = 0)
				begin 
					set @id = (
						select id from {$this->MAuth->getdb('std_vehicles')}
						where model=@model and baab=@baab and color=@color
					);
					
					set @plrank = isnull((
						select MAX(plrank) + 1 from {$this->MAuth->getdb('std_pricelist')}
						where id in (
							select id from {$this->MAuth->getdb('std_vehicles')}
							where model=@model and baab=@baab and color=@color
						) --and ACTICOD=@acticod
					),1);
					
					insert into {$this->MAuth->getdb('std_pricelist')} (id,plrank,price,pricespecial,ACTICOD,name,details,event_s,event_e,insby,insdt)
					select @id,@plrank,@price,@pricespc,@acticod,@eventname,@eventdetail,@event_s,@event_e,@IDNo,getdate();
					
					insert into {$this->MAuth->getdb('std_pricelist_locat')} (id,plrank,locat,insby,insdt) 
					select @id,@plrank,locat,insby,insdt from #templocat
					order by insdt
				end 
				else if(@inpricelistlocat > 0)
				begin 
					rollback tran tsins;
					declare @msg varchar(max) = '';
					set @msg = 'ผิดพลาด :: ไม่สามารถเพิ่มข้อมูลได้';
					set @msg = @msg+'<br>เนื่องจากรุ่น '+@model+' แบบ '+@baab+' สี '+@color;
					set @msg = @msg+'<br>วันที่ '+convert(varchar(10),@event_s,111)+' ถึง '+(case when @event_e is null then 'ปัจจุบัน' else convert(varchar(10),@event_e,111) end);
					set @msg = @msg+'<br>มีข้อมูล std อยู่แล้ว';
					
					insert into #tempResult select 'y',@msg;
					return;
				end 
				
				
				/* ใช้ std กับสาขาไหนบ้าง */				
				/*ระบุสาขามา*/
				insert into {$this->MAuth->getdb('std_pricelist_locat')} (id,plrank,locat,insby,insdt) 
				select @id,@plrank,locat,insby,insdt from #templocat
				order by insdt
				
				/* ขั้นเงินดาวน์ */
				{$qdwn}
				
				/* ของแถม */
				{$qfree}
				
				insert into #tempResult select 'n','บันทึกข้อมูลเรียบร้อยแล้ว';
				commit tran tsins;
			end try
			begin catch
				rollback tran tsins;
				insert into #tempResult select 'y',ERROR_MESSAGE();
			end catch
		";
		//echo $sql; exit;
		$this->db->query($sql);
		$sql = "select * from #tempResult";
		$query = $this->db->query($sql);
		
		if($query->row()){
			foreach($query->result() as $row){
				$response["error"] = ($row->error == "n" ? false:true);
				$response["msg"] = $row->msg;
			}
		}else{
			$response["error"] = true;
			$response["msg"] = 'ผิดพลาดไม่สามารถบันทึกราคาขายได้ โปรดติดต่อฝ่ายไอที';
		}
		
		echo json_encode($response);
	}
	
	function EditSTD(){
		$stdid 	 	 = $_POST["stdid"];
		$plrank 	 = $_POST["plrank"];
		$evente 	 = $this->Convertdate(1,($_POST["evente"]));
		$eventname 	 = $_POST["eventname"];
		$eventdetail = $_POST["eventdetail"];
		$price1 	 = $_POST["price1"];
		$price2 	 = $_POST["price2"];
		
		if(!is_numeric($price1)){
			$response["error"] = true;
			$response["msg"] = 'โปรดระบุราคาสด ให้ถูกต้อง';
			echo json_encode($response); exit;
		}else if($price1 < 1){
			$response["error"] = true;
			$response["msg"] = 'โปรดระบุราคาสด ให้ถูกต้อง';
			echo json_encode($response); exit;
		}
		
		if(!is_numeric($price2)){
			$response["error"] = true;
			$response["msg"] = 'โปรดระบุราคาผลัด ให้ถูกต้อง';
			echo json_encode($response); exit;
		}else if($price2 < 1){
			$response["error"] = true;
			$response["msg"] = 'โปรดระบุราคาผลัด ให้ถูกต้อง';
			echo json_encode($response); exit;
		}
		
		$sql = "
			if object_id('tempdb..#tempResult') is not null drop table #tempResult;
			create table #tempResult (error varchar(1),msg varchar(max));
			
			begin tran tsup
			begin try
			
				if not exists (
					select '' from {$this->MAuth->getdb('std_pricelist')}
					where id='".$stdid."' and plrank='".$plrank."' and event_s < ".($evente == "" ? "NULL":"'".$evente."'")."					
				)
				begin 
					rollback tran tsup;
					insert into #tempResult select 'y','ผิดพลาด วันที่สิ้นสุด ต้องมากกว่าวันที่บังคับใช้';
					return;
				end 
				else
				begin 
					update {$this->MAuth->getdb('std_pricelist')}
					set event_e = ".($evente == "" ? "NULL":"'".$evente."'")."
						,name='".$eventname."'
						,details='".$eventdetail."'
						,price='".$price1."'
						,pricespecial='".$price2."'
						,updby='".$this->sess["IDNo"]."'
						,upddt=getdate()
					where id='".$stdid."' and plrank='".$plrank."'
				end
				
				insert into #tempResult select 'n','แก้ไขข้อมูล std. เรียบร้อยแล้ว';
				commit tran tsup;
			end try
			begin catch
				rollback tran tsup;
				insert into #tempResult select 'y',ERROR_MESSAGE();
			end catch
		";
		//echo $sql; exit;
		$this->db->query($sql);
		$sql = "select * from #tempResult";
		$query = $this->db->query($sql);
		
		if($query->row()){
			foreach($query->result() as $row){
				$response["error"] = ($row->error == "n" ? false:true);
				$response["msg"] = $row->msg;
			}
		}else{
			$response["error"] = true;
			$response["msg"] = 'ผิดพลาดไม่สามารถแก้ไข std. ได้ โปรดติดต่อฝ่ายไอที';
		}
		
		echo json_encode($response);
	}
	
	function getSACTICOD2(){
		$response 	= array("opt"=>"");
		$dataSearch = trim($_POST["filter"]);
		$dataNow 	= ($_POST["now"] == "" ? array():$_POST["now"]);
		$snow 		= sizeof($dataNow);
		
		$now = "";
		for($i=0;$i<$snow;$i++){
			if($now != ""){ $now .= ","; }
			$now .= "'".$dataNow[$i]."'";
		}
		
		if($now != ""){
			$now = " and ACTICOD collate Thai_CI_AS in (".$now.") ";
		}else{
			$now = " and 1=2 ";
		}
		
		$sql = "
			select ACTICOD,'('+ACTICOD+') '+ACTIDES as ACTIDES
			from {$this->MAuth->getdb('SETACTI')}
			where 1=1 ".$now."
				
			union
			select ACTICOD,'('+ACTICOD+') '+ACTIDES as ACTIDES
			from {$this->MAuth->getdb('SETACTI')}
			where 1=1 and '('+ACTICOD+') '+ACTIDES like '%".$dataSearch."%' collate Thai_CI_AS
			order by ACTICOD
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$opt = "";
		if($query->row()){
			foreach($query->result() as $row){
				$opt .= "
					<option value='".str_replace(chr(0),"",$row->ACTICOD)."' 
						".(in_array(str_replace(chr(0),"",$row->ACTICOD),$dataNow) ? "selected":"").">
						".$row->ACTIDES."
					</option>
				";
			}
		}
		
		$response["opt"] = $opt;
		echo json_encode($response);
	}
	
	/*
	function getCUSTOMERS2(){
		$response 	= array("opt"=>"");
		$dataSearch = trim($_POST["filter"]);
		$dataNow 	= ($_POST["now"] == "" ? array():$_POST["now"]);
		$snow 		= sizeof($dataNow);
		
		$now = "";
		for($i=0;$i<$snow;$i++){
			if($now != ""){ $now .= ","; }
			$now .= "'".$dataNow[$i]."'";
		}
		
		if($now != ""){
			$now = " and CUSCOD collate Thai_CI_AS in (".$now.") ";
		}else{
			$now = " and 1=2 ";
		}
		
		$sql = "
			select CUSCOD,SNAM+NAME1+' '+NAME2+' ('+CUSCOD+')'+'-'+GRADE as CUSNAME from {$this->MAuth->getdb('CUSTMAST')}
			where 1=1 ".$now."
			
			union
			select top 20 CUSCOD,SNAM+NAME1+' '+NAME2+' ('+CUSCOD+')'+'-'+GRADE as CUSNAME from {$this->MAuth->getdb('CUSTMAST')}
			where CUSCOD like '%".$dataSearch."%' collate Thai_CI_AS 
				or NAME1+' '+NAME2 like '%".$dataSearch."%' collate Thai_CI_AS
				or IDNO like '%".$dataSearch."%' collate Thai_CI_AS
			order by CUSCOD
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$opt = "";
		if($query->row()){
			foreach($query->result() as $row){
				$opt .= "
					<option value='".str_replace(chr(0),"",$row->CUSCOD)."' 
						".(in_array(str_replace(chr(0),"",$row->CUSCOD),$dataNow) ? "selected":"").">
						".$row->CUSNAME."
					</option>
				";
			}
		}
		
		$response["opt"] = $opt;
		echo json_encode($response);
	}
	*/
	
}

































