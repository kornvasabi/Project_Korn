<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/********************************************************
             ______@07/12/2019______
            / / _ _   _ _     __ 
           / // __ \ / __ \ / __ \
       _ _/ // /_/ // / / // /_/ /
     /_ _ _/ \_ _ //_/ /_/ \__  /
                          _ _/ /
                         /___ /
********************************************************/
class Standard extends MY_Controller {
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
		
		$this->load->model('MMAIN');
	}
	
	function index(){
		$claim = $this->MLogin->getclaim(uri_string());
		if($claim['m_access'] != "T"){ echo "<div align='center' style='color:red;font-size:16pt;width:100%;'>ขออภัย คุณยังไม่มีสิทธิเข้าใช้งานหน้านี้ครับ</div>"; exit; }
		
		$html = "
			<div class='tab1' name='home' locat='{$this->sess['branch']}' is_mobile='{$this->sess['is_mobile']}' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}' style='height:calc(100vh - 132px);overflow:auto;background-color:white;'>
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
								<select id='SACTICOD' class='form-control JD-BSSELECT' title='เลือก' multiple data-actions-box='true' data-size='8' data-live-search='true'></select>
							</div>
						</div>
						
						<div class='col-sm-6'>	
							<div class='form-group'>
								สาขา
								<select id='Search_LOCAT' class='form-control JD-BSSELECT' title='เลือก'  multiple data-actions-box='true' data-size='8' data-live-search='true'></select>
							</div>
						</div>
						
						<div class='col-sm-6'>
							<div class='form-group'>
								สถานะภาพรถ
								<div class='row'>
									<div class='col-xs-12'>
										<label class='radio-inline lobiradio-success lobiradio'>
											<input type='radio' name='s_std_stat' value='all' checked=''> 
											<i></i> ทั้งหมด
										</label>
										<label class='radio-inline lobiradio'>
											<input type='radio' name='s_std_stat' value='N'> 
											<i></i> รถใหม่
										</label>
										<label class='radio-inline lobiradio-danger lobiradio'>
											<input type='radio' name='s_std_stat' value='O'> 
											<i></i> รถเก่า
										</label>
									</div>
								</div>
							</div>	
						</div>
					</div>
					
					<div class='row'>
						<div class='col-sm-6'>	
							<div class='form-group'>
								<button id='btnt1createStd' class='btn btn-cyan btn-block'><span class='glyphicon glyphicon-pencil'> สร้าง</span></button>
							</div>
						</div>
						<div class='col-sm-6'>	
							<div class='form-group'>
								<button id='btnt1search' class='btn btn-primary btn-block'><span class='glyphicon glyphicon-search'> ค้นหา</span></button>
							</div>
						</div>
					</div>
				</div>
			</div>
		";
		
		$html.= "<script src='".base_url('public/js/SYS04/Standard.js')."'></script>";
		echo $html;
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
		$arrs['locat'] 	 = $_POST['locat'];
		$arrs['stat'] 	 = $_POST['stat'];
		
		$cond 		= "";
		$condDesc 	= "";
		if($arrs['name'] != ""){
			$cond .= " and b.STDNAME like '%".$arrs['name']."%'";
			$condDesc .= " ชื่อเรียก ::  ".$arrs['name'];
		}
		
		if($arrs['model'] != ""){
			$cond .= " and a.MODEL = '".$arrs['model']."'";
			$condDesc .= " รุ่น ::  ".$arrs['model'];
		}
		
		if($arrs['baab'] != ""){
			$cond .= " and d.BAAB = '".$arrs['baab']."'";
			$condDesc .= " แบบ ::  ".$arrs['baab'];
		}
		
		if($arrs['color'] != ""){
			$cond .= " and e.COLOR = '".$arrs['color']."'";
			$condDesc .= " สี ::  ".$arrs['color'];
		}
		
		if($arrs['acticod'] != ""){
			$temp = "";
			$size = sizeof($arrs['acticod']);
			for($i=0;$i<$size;$i++){
				if($temp != ""){ $temp .= ","; }
				$temp .= "'".$arrs['acticod'][$i]."'";
			}
			
			$cond .= " and c.ACTICOD in (".$temp.")";
			$condDesc .= " กิจกรรมการขาย :: (".$temp.")";
		}
		
		if($arrs['locat'] != ""){
			$temp = "";
			$size = sizeof($arrs['locat']);
			for($i=0;$i<$size;$i++){
				if($temp != ""){ $temp .= ","; }
				$temp .= "'".$arrs['locat'][$i]."'";
			}
			
			$cond .= " and f.LOCAT in (".$temp.")";
			$condDesc .= " กิจกรรมการขาย :: (".$temp.")";
		}
		
		if($arrs['stat'] != "all"){
			$cond .= " and b.STAT = '".$arrs['stat']."'";
			$condDesc .= " สถานะภาพรถ :: (".($arrs['stat'] == "N" ? "รถใหม่":"รถเก่า").")";
		}
		
		if($arrs['events'] != "" && $arrs['evente'] != ""){
			$cond .= " 
				and ''".$arrs['events']."'' between b.EVENTStart and b.EVENTEnd
				and ''".$arrs['evente']."'' <= b.EVENTEnd
			";
			$condDesc .= " วันที่บังคับใช้ std. ::  [".$_POST['events']." - ".$_POST['evente']."]";
		}else if($arrs['events'] != "" && $arrs['evente'] == ""){
			$cond .= " and ''".$arrs['events']."'' between b.EVENTStart and b.EVENTEnd";
			$condDesc .= " วันที่บังคับใช้ std. ::  [".$_POST['events']." เป็นต้นไป]";
		}else if($arrs['events'] == "" && $arrs['evente'] != ""){
			$cond .= " and ''".$arrs['evente']."'' between b.EVENTStart and b.EVENTEnd";
			$condDesc .= " วันที่บังคับใช้ std. :: ถึงวันที่ [".$_POST['evente']."]";
		}
		
		$sql = "						
			select distinct a.STDID,b.SUBID,a.MODEL 
				,convert(varchar(8),b.EVENTStart,112) as EVENTStart
				,convert(varchar(8),b.EVENTEnd,112) as EVENTEnd
				,b.STDNAME,b.STDDESC,case when b.STAT='N' then 'รถใหม่' else 'รถเก่า' end STAT
			from {$this->MAuth->getdb('STDVehicles')} a
			inner join {$this->MAuth->getdb('STDVehiclesDetail')} b on a.STDID=b.STDID
			left join {$this->MAuth->getdb('STDVehiclesACTI')} c on b.STDID=c.STDID and b.SUBID=c.SUBID
			left join {$this->MAuth->getdb('STDVehiclesBAAB')} d on b.STDID=d.STDID and b.SUBID=d.SUBID
			left join {$this->MAuth->getdb('STDVehiclesCOLOR')} e on b.STDID=e.STDID and b.SUBID=e.SUBID
			left join {$this->MAuth->getdb('STDVehiclesLOCAT')} f on b.STDID=f.STDID and b.SUBID=f.SUBID
			where 1=1 ".$cond."
		";
		//echo $sql; exit;		
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$STDID = $row->STDID;
				$SUBID = $row->SUBID;
				
				$sql_df = "
					select a.STDID,b.SUBID,a.MODEL 
						,b.EVENTStart,b.EVENTEnd
						,b.STDNAME,b.STDDESC
						,c.PRICE
						,c.PRICES
						,d.DOWNS
						,d.DOWNE
						,d.INTERESTRT
						,d.INTERESTRT_GVM
						,d.INSURANCE
						,d.TRANSFERS
						,d.REGIST
						,d.ACT
						,d.COUPON
						,d.APPROVE	
						,case when e.FORCUS = 'C' then 'คนซื้อ'
							when e.FORCUS = 'S' then 'ผู้แนะนำ'
							when e.FORCUS = 'I' then 'คนค้ำ'
							else '' end FORCUS
						,e.NOPAYS
						,e.NOPAYE
						,e.RATE		
					from {$this->MAuth->getdb('STDVehicles')} a
					left join {$this->MAuth->getdb('STDVehiclesDetail')} b on a.STDID=b.STDID
					left join {$this->MAuth->getdb('STDVehiclesPRICE')} c on b.STDID=c.STDID and b.SUBID=c.SUBID
					left join {$this->MAuth->getdb('STDVehiclesDown')} d on c.STDID=d.STDID and c.SUBID=d.SUBID and c.PRICE=d.PRICES and c.PRICES=d.PRICEE	
					left join {$this->MAuth->getdb('STDVehiclesPackages')} e on e.STDID=d.STDID and e.SUBID=d.SUBID and e.PRICES=d.PRICES and e.PRICEE=d.PRICEE and d.DOWNS=e.DOWNS and d.DOWNE=e.DOWNE
					where a.STDID='{$STDID}' and b.SUBID='{$SUBID}' and c.ACTIVE='yes' --and d.ACTIVE='yes' and e.ACTIVE='yes' 
				";
				$query_df = $this->db->query($sql_df);
				
				$df = "";
				$NRow = 1;
				if($query_df->row()){
					$PRICES = "";
					$DOWNS = "";
					$color1 = "";
					$color2 = "";
					foreach($query_df->result() as $row_df){
						
						if($NRow++ == 1){
							$color1 = "black";
						}else if($PRICES == $row_df->PRICE){
							$color1 = "white";
						}else{
							$color1 = "black";
						}
						
						if($NRow++ == 1){
							$color2 = "black";
						}else if($DOWNS == $row_df->DOWNS){
							$color2 = "white";
						}else{
							$color2 = "black";
						}
												
						$df .= "
							<tr>
								<td align='right' style='border:0.1px solid black;color:{$color1};'>".number_format($row_df->PRICE,2)." - ".($row_df->PRICE == 9999999.99 ? "ขึ้นไป":number_format($row_df->PRICES,2))."</td>
								<td align='right' style='border:0.1px solid black;color:{$color2};'>".number_format($row_df->DOWNS,2)." - ".($row_df->DOWNE == 9999999.99 ? "ขึ้นไป":number_format($row_df->DOWNE,2))."</td>
								<td align='right' style='border:0.1px solid black;color:{$color2};'>".$row_df->INTERESTRT."</td>
								<td align='right' style='border:0.1px solid black;color:{$color2};'>".$row_df->INTERESTRT_GVM."</td>
								<td align='right' style='border:0.1px solid black;color:{$color2};'>".number_format($row_df->INSURANCE,2)."</td>
								<td align='right' style='border:0.1px solid black;color:{$color2};'>".number_format($row_df->TRANSFERS,2)."</td>
								<td align='right' style='border:0.1px solid black;color:{$color2};'>".number_format($row_df->REGIST,2)."</td>
								<td align='right' style='border:0.1px solid black;color:{$color2};'>".number_format($row_df->ACT,2)."</td>
								<td align='right' style='border:0.1px solid black;color:{$color2};'>".number_format($row_df->COUPON,2)."</td>
								<td style='border:0.1px solid black;color:{$color2};'>".$row_df->APPROVE."</td>
								<td style='border:0.1px solid black;'>".$row_df->FORCUS."</td>
								<td align='right' style='border:0.1px solid black;'>".$row_df->NOPAYS." - ".$row_df->NOPAYE."</td>
								<td align='right' style='border:0.1px solid black;'>".number_format($row_df->RATE,2)."</td>
							</tr>
						";
						
						$PRICES = $row_df->PRICE;
						$DOWNS 	= $row_df->DOWNS;
					}
				}
				
				$df = "
					<table>
						<tr>
							<th>ช่วงราคารถ</th>
							<th>ช่วงเงินดาวน์</th>
							<th>ดบ.</th>
							<th>ดบ.</th>
							<th>ประกัน</th>
							<th>โอน</th>
							<th>ทบ.จดใหม่</th>
							<th>พรบ.</th>
							<th>คูปอง</th>
							<th>อนุมัติ</th>
							<th>ประเภท</th>
							<th>ช่วงงวด</th>
							<th>ของแถม</th>
						</tr>
						".$df."
					</table>
				";
				
				$html .= "
					<tr>
						<th style='width:100px;vertical-align: text-top;background-color:#81e9d1;color:black;'>
							<span style='color:blue;'>STDID<br>SUBID</span>
							<br>รุ่น
							<br>จากวันที่
							<br>ถึงวันที่
							<br>ชื่อ
							<br>ลักษณะ
							<br>สถานะภาพรถ
							<br><br><button stdid='{$row->STDID}' subid='{$row->SUBID}' class='editstd btn btn-xs btn-block btn-warning'>แก้ไข</button>
						</th>
						<td style='max-width:200px;overflow:auto;vertical-align: text-top;'>
							<div style='max-width:200px;overflow:hidden;text-overflow:ellipsis;'>
								<span style='color:blue;'>".$row->STDID."<br>".$row->SUBID."</span>
								<br>".$row->MODEL."
								<br>".$this->Convertdate(2,$row->EVENTStart)."
								<br>".$this->Convertdate(2,$row->EVENTEnd)."
								<br><span class='JDtooltip' title='{$row->STDNAME}'>".$row->STDNAME."</span>
								<br><span class='JDtooltip' title='{$row->STDDESC}'>".$row->STDDESC."</span>
								<br>".$row->STAT."
							</div>
						</td>
						<td style='vertical-align: text-top;'>
							<div style='max-width:200px;max-height:500px;overflow:auto;'>
								".($this->search_acti($STDID,$SUBID))."
							</div>
						</td>
						<td style='vertical-align: text-top;'>
							<div style='max-width:200px;max-height:500px;overflow:auto;'>
								".($this->search_baab($STDID,$SUBID))."
							</div>
						</td>
						<td style='vertical-align: text-top;'>
							<div style='max-width:200px;max-height:500px;overflow:auto;'>
								".($this->search_color($STDID,$SUBID))."
							</div>
						</td>
						<td style='width:70px;vertical-align: text-top;'>
							<div style='max-width:60px;width:60px;max-height:500px;overflow:auto;'>
								".($this->search_locat($STDID,$SUBID))."
							</div>
						</td>
						<td><div style='width:830px;max-height:500px;overflow:auto;'>".($this->search_price($STDID,$SUBID))."</div></td>
					</tr>
				";				
			}
		}
		
		$html = "
			<table border=1 style='vertical-align:text-top;font-size:10pt;'>
				<tr style='background-color:#81e9d1;color:black;'>
					<th colspan=2 style='text-align:center;'>ข้อมูล standard</th>
					<th style='text-align:center;'>กิจกรรมการขาย</th>
					<th style='text-align:center;'>แบบ</th>
					<th style='text-align:center;'>สี</th>
					<th style='text-align:center;'>สาขา</th>
					<th>
						<div style='width:151px;max-width:151px;overflow:auto;float:left;border:0.1px solid black;text-align:center;'>ช่วงราคารถ</div>
						<div style='width:151px;max-width:151px;overflow:auto;float:left;border:0.1px solid black;text-align:center;'>ช่วงเงินดาวน์</div>
						<div style='width:251px;max-width:251px;float:left;border:0.1px solid black;text-align:center;'>รายละเอียด</div>
						<div style='width:calc(100% - 553px);float:left;border:0.1px solid black;text-align:center;'>ของแถม</div>
					</th>
				</tr>
				".$html."
			</table>
		";
		
		//$response = array("html"=>$html,"status"=>true,"excel"=>$this->html_excel($query));
		$response = array("html"=>$html,"status"=>true,"excel"=>"");
		echo json_encode($response);
	}
	
	function search_acti($STDID,$SUBID){
		$sql = "
			select '('+c.ACTICOD+') '+d.ACTIDES collate thai_cs_as as ACTICOD
			from {$this->MAuth->getdb('STDVehicles')} a
			left join {$this->MAuth->getdb('STDVehiclesDetail')} b on a.STDID=b.STDID
			left join {$this->MAuth->getdb('STDVehiclesACTI')} c on b.STDID=c.STDID and b.SUBID=c.SUBID
			left join {$this->MAuth->getdb('SETACTI')} d on c.ACTICOD=d.ACTICOD collate thai_cs_as
			where a.STDID='{$STDID}' and b.SUBID='{$SUBID}' and c.ACTIVE='yes'
		";
		//echo $sql ; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				if($html != ""){ $html .= "<br>"; }
				$html .= $row->ACTICOD;
				//$html .= "<tr><td>".$row->ACTICOD."</td></tr>";
			}
		}
		//$html = "<table>".$html."</table>";
		
		return $html;
	}
	
	function search_baab($STDID,$SUBID){
		$sql = "
			select c.BAAB
			from {$this->MAuth->getdb('STDVehicles')} a
			left join {$this->MAuth->getdb('STDVehiclesDetail')} b on a.STDID=b.STDID
			left join {$this->MAuth->getdb('STDVehiclesBAAB')} c on b.STDID=c.STDID and b.SUBID=c.SUBID
			where a.STDID='{$STDID}' and b.SUBID='{$SUBID}' and c.ACTIVE='yes'
		";
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				if($html != ""){ $html .= "<br>"; }
				$html .= $row->BAAB;
				//$html .= "<tr><td>".$row->BAAB."</td></tr>";
			}
		}
		//$html = "<table>".$html."</table>";
		
		return $html;
	}
	
	function search_color($STDID,$SUBID){
		$sql = "
			select c.COLOR
			from {$this->MAuth->getdb('STDVehicles')} a
			left join {$this->MAuth->getdb('STDVehiclesDetail')} b on a.STDID=b.STDID
			left join {$this->MAuth->getdb('STDVehiclesCOLOR')} c on b.STDID=c.STDID and b.SUBID=c.SUBID
			where a.STDID='{$STDID}' and b.SUBID='{$SUBID}' and c.ACTIVE='yes'
		";
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				if($html != ""){ $html .= "<br>"; }
				$html .= $row->COLOR;
				//$html .= "<tr><td>".$row->COLOR."</td></tr>";
			}
		}
		//$html = "<table>".$html."</table>";
		
		return $html;
	}
	
	function search_locat($STDID,$SUBID){
		$sql = "
			select c.LOCAT
			from {$this->MAuth->getdb('STDVehicles')} a
			left join {$this->MAuth->getdb('STDVehiclesDetail')} b on a.STDID=b.STDID
			left join {$this->MAuth->getdb('STDVehiclesLOCAT')} c on b.STDID=c.STDID and b.SUBID=c.SUBID
			where a.STDID='{$STDID}' and b.SUBID='{$SUBID}' and c.ACTIVE='yes'
		";
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				if($html != ""){ $html .= "<br>"; }
				$html .= $row->LOCAT;
				//$html .= "<tr><td>".$sql->LOCAT."</td></tr>";
			}
		}
		//$html = "<table>".$html."</table>";
		return $html;
	}
	
	function search_price($STDID,$SUBID){
		$sql = "
			select c.PRICE,c.PRICES
			from {$this->MAuth->getdb('STDVehicles')} a
			left join {$this->MAuth->getdb('STDVehiclesDetail')} b on a.STDID=b.STDID
			left join {$this->MAuth->getdb('STDVehiclesPRICE')} c on b.STDID=c.STDID and b.SUBID=c.SUBID
			where a.STDID='{$STDID}' and b.SUBID='{$SUBID}' and c.ACTIVE='yes'
		";
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$PRICES = $row->PRICE;
				$PRICEE = $row->PRICES;
				
				$html .= "
					<tr>
						<td style='width:150px;vertical-align: text-top;'>".number_format($row->PRICE,2)." - ".($row->PRICES == 9999999.99 ? "ขึ้นไป":number_format($row->PRICES,2))."</td>
						<td>".($this->search_down($STDID,$SUBID,$PRICES,$PRICEE))."</td>
					</tr>
				";
			}
		}
		
		$html = "
			<table border=1>
				".$html."
			</table>
		";
		
		return $html;
	}
	
	function search_down($STDID,$SUBID,$PRICES,$PRICEE){
		$sql = "
			select a.STDID,b.SUBID,a.MODEL 
				,b.EVENTStart,b.EVENTEnd
				,b.STDNAME,b.STDDESC
				,c.PRICE
				,c.PRICES
				,d.DOWNS
				,d.DOWNE
				,d.INTERESTRT
				,d.INTERESTRT_GVM
				,d.INSURANCE
				,d.TRANSFERS
				,d.REGIST
				,d.ACT
				,d.COUPON
				,d.APPROVE
				,case when d.APPROVE = 'Y' then 'ต้องขออนุมัติ' else 'ไม่ต้องขออนุมัติ' end as APPROVEDESC
			from {$this->MAuth->getdb('STDVehicles')} a
			left join {$this->MAuth->getdb('STDVehiclesDetail')} b on a.STDID=b.STDID
			left join {$this->MAuth->getdb('STDVehiclesPRICE')} c on b.STDID=c.STDID and b.SUBID=c.SUBID
			left join {$this->MAuth->getdb('STDVehiclesDown')} d on c.STDID=d.STDID and c.SUBID=d.SUBID and c.PRICE=d.PRICES and c.PRICES=d.PRICEE	
			where a.STDID='{$STDID}' and b.SUBID='{$SUBID}' 
				and c.PRICE='{$PRICES}' and c.PRICES='{$PRICEE}' 
				and c.ACTIVE='yes' and d.ACTIVE='yes'
		";
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$DOWNS 	 = $row->DOWNS;
				$DOWNE 	 = $row->DOWNE;
				$APPROVE = ($row->APPROVE == 'N' ? "#555":"orange");
				
				$html .= "
					<tr style='color:{$APPROVE};'>
						<td style='width:150px;vertical-align: text-top;'>".number_format($row->DOWNS,2)." - ".($row->DOWNE == 9999999.99 ? "ขึ้นไป":number_format($row->DOWNE,2))."</td>
						<th style='width:150px;vertical-align: text-top;background-color:#81e9d1;color:black;'>
							ดบ.ทั่วไป - ดบ.ราชการ
							<br>ค่าป.1
							<br>ค่าโอน
							<br>ค่าจดทะเบียน
							<br>ค่าพรบ.
							<br>คูปองชิงโชค
							<br>อนุมัติ
						</th>						
						<td style='width:100px;vertical-align: text-top;text-align:right;'>
							".number_format($row->INTERESTRT,2)." - ".number_format($row->INTERESTRT_GVM,2)."
							<br>".number_format($row->INSURANCE,2)."
							<br>".number_format($row->TRANSFERS,2)."
							<br>".number_format($row->REGIST,2)."
							<br>".number_format($row->ACT,2)."
							<br>".number_format($row->COUPON,2)."
							<br>".($row->APPROVEDESC)."
						</td>
						<td style='vertical-align: text-top;'>".$this->search_free($STDID,$SUBID,$PRICES,$PRICEE,$DOWNS,$DOWNE)."</td>
					</tr>
				";
			}
		}
		
		$html = "<table border=1>".$html."</table>";
		
		return $html;
	}
	
	function search_free($STDID,$SUBID,$PRICES,$PRICEE,$DOWNS,$DOWNE){
		$sql = "
			select a.STDID,b.SUBID,a.MODEL 
				,b.EVENTStart,b.EVENTEnd
				,b.STDNAME,b.STDDESC
				,c.PRICE
				,c.PRICES
				,d.DOWNS
				,d.DOWNE
				,d.INTERESTRT
				,d.INTERESTRT_GVM
				,d.INSURANCE
				,d.TRANSFERS
				,d.REGIST
				,d.ACT
				,d.COUPON
				,d.APPROVE	
				,case when e.FORCUS = 'C' then 'คนซื้อ'
					when e.FORCUS = 'S' then 'ผู้แนะนำ'
					when e.FORCUS = 'I' then 'คนค้ำ'
					else '' end FORCUS
				,e.NOPAYS
				,e.NOPAYE
				,e.RATE		
			from {$this->MAuth->getdb('STDVehicles')} a
			left join {$this->MAuth->getdb('STDVehiclesDetail')} b on a.STDID=b.STDID
			left join {$this->MAuth->getdb('STDVehiclesPRICE')} c on b.STDID=c.STDID and b.SUBID=c.SUBID
			left join {$this->MAuth->getdb('STDVehiclesDown')} d on c.STDID=d.STDID and c.SUBID=d.SUBID and c.PRICE=d.PRICES and c.PRICES=d.PRICEE	
			left join {$this->MAuth->getdb('STDVehiclesPackages')} e on e.STDID=d.STDID and e.SUBID=d.SUBID and e.PRICES=d.PRICES and e.PRICEE=d.PRICEE and d.DOWNS=e.DOWNS and d.DOWNE=e.DOWNE
			where a.STDID='{$STDID}' and b.SUBID='{$SUBID}' 
				and c.PRICE='{$PRICES}' and c.PRICES='{$PRICEE}' 
				and d.DOWNS='{$DOWNS}' and d.DOWNE='{$DOWNE}' 
				and c.ACTIVE='yes' and d.ACTIVE='yes' and e.ACTIVE='yes'
		";
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$html .= "
					<tr>
						<td style='border:0.1px dotted black;'>".$row->FORCUS."</td>
						<td style='border:0.1px dotted black;text-align:right;'>".$row->NOPAYS."</td>
						<td style='border:0.1px dotted black;text-align:right;'>".$row->NOPAYE."</td>
						<td style='border:0.1px dotted black;text-align:right;'>".number_format($row->RATE,2)."</td>
					</tr>
				";
			}
		}else{
			$html .= "<tr><td colspan='4'  style='border:0.1px dotted black;text-align:center;'>ไม่พบข้อมูล</td></tr>";
		}
		
		$html = "
			<table style='width:250px;'>
				<tr style='background-color:#81e9d1;color:black;'>
					<th>ประเภท</th>
					<th>งวด</th>
					<th>ถึงงวด</th>
					<th>ของแถม</th>
				</tr>
				".$html."
			</table>
		";
		
		return $html;
	}
	
	function html_excel($query){
		$data_excel = "";
		$NRow = 0;
		$size_free_col = 0;
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
	
	// function searchDetail(){
		// $stdid = $_POST["stdid"];
		// $subid = $_POST["subid"];
		
		// /* ข้อมูล std */
		// $sql = "
			// select a.id ,b.plrank
				// ,a.model
				// ,a.baab
				// ,a.color
				// ,b.price
				// ,b.pricespecial
				// ,b.ACTICOD
				// ,b.name
				// ,b.details
				// ,convert(char(8),b.event_s,112) as event_s
				// ,convert(char(8),b.event_e,112) as event_e
			// from {$this->MAuth->getdb('std_vehicles')} a
			// left join {$this->MAuth->getdb('std_pricelist')} b on a.id=b.id
			// where a.id='{$stdid}' and b.plrank={$stdrank}
		// ";
		// echo $sql; exit;
		// $query = $this->db->query($sql);
		
		// $arrs = array("null"=>"");
		// $NRow = 1;
		// if($query->row()){
			// foreach($query->result() as $row){
				// foreach($row as $key => $val){
					// switch($key){
						// case 'event_s':
						// case 'event_e':
							// $arrs[$key] = $this->Convertdate(2,$val); 
							// break;
						// default: 
							// $arrs[$key] = $val;
							// break;
					// }
				// }
			// }
		// }
		
		// /* ข้อมูล std ใช้กับสาขาไหนบ้าง */
		// $sql = "
			// select * from {$this->MAuth->getdb('std_pricelist_locat')} 
			// where id='{$stdid}' and plrank={$stdrank}
		// ";
		// $query = $this->db->query($sql);
		
		// if($query->row()){
			// foreach($query->result() as $row){
				// $arrs["locat"][] = $row->locat;
			// }
		// }
		
		// $sql = "select * from {$this->MAuth->getdb('INVLOCAT')} where LOCATCD<>'TRANS'";
		// $q = $this->db->query($sql);
		
		// $locatopt = "";
		// if($q->row()){
			// $i=1;
			// foreach($q->result() as $row){
				// $locatopt .= "<option value='{$row->LOCATCD}' title='{$row->LOCATNM}' 
					// data-toggle='tooltip'
					// data-placement='top'
					// data-html='true'
					// data-original-title='{$row->LOCATNM}'
					// ".($arrs["locat"][0] == "ALL" ? "selected" : (in_array($row->LOCATCD,$arrs["locat"]) ? "selected":""))."
				// >{$row->LOCATCD}</option>";
			// }
		// }
		
		// /* ข้อมูล std การดาวน์รถ  */
		// $sql = "
			// select * from {$this->MAuth->getdb('std_down')} 
			// where id='{$stdid}' and plrank={$stdrank}
			// order by level_r
		// ";
		// $query = $this->db->query($sql);
		
		// if($query->row()){
			// $arrs["down"] = "";
			// foreach($query->result() as $row){
				// $arrs["down"] .= "
					// <tr>
						// <td>".$row->dwnrate_s."</td>
						// <td>".$row->dwnrate_e."</td>
						// <td>".$row->interest_rate.($row->interest_rate2 == "" ? "":" (".$row->interest_rate2.")")."</td>
						// <td>".$row->insurance."</td>
						// <td>".$row->transfers."</td>
						// <td>".$row->regist."</td>
						// <td>".$row->act."</td>
						// <td>".$row->coupon."</td>
						// <!-- td>
							// <button class='editDwn btn-warning'".
								// "formdwns='".$row->dwnrate_s."'".
								// "formdwne='".$row->dwnrate_e."'".
								// "forminterest='".$row->interest_rate."'".
								// "forminterest2='".$row->interest_rate2."'".
								// "forminsurance='".$row->insurance."'".
								// "formtrans='".$row->transfers."'".
								// "formregist='".$row->regist."'".
								// "formact='".$row->act."'".
								// "formcoupon='".$row->coupon."'".
								// " disabled ".
							// "><span class='glyphicon glyphicon-edit'> แก้ไข</span></button>
							// <button class='deleteDwn btn-danger'".
								// "formdwns='".$row->dwnrate_s."'".
								// "formdwne='".$row->dwnrate_e."'".
								// "forminterest='".$row->interest_rate."'".
								// "forminterest2='".$row->interest_rate2."'".
								// "forminsurance='".$row->insurance."'".
								// "formtrans='".$row->transfers."'".
								// "formregist='".$row->regist."'".
								// "formact='".$row->act."'".
								// "formcoupon='".$row->coupon."'".
								// " disabled ".
							// "><span class='glyphicon glyphicon-trash'> ลบ</span></button>
						// </td -->
					// </tr>
				// ";
			// }
		// }
		
		// /* ข้อมูล std ของแถม  */
		// $sql = "
			// select * from {$this->MAuth->getdb('std_package')} 
			// where id='{$stdid}' and plrank={$stdrank}
			// order by nopay_s,free_rate
		// ";
		// $query = $this->db->query($sql);
		
		// if($query->row()){
			// $arrs["package"] = "";
			// foreach($query->result() as $row){
				// $arrs["package"] .= "
					// <tr>
						// <td>".$row->nopay_s."</td>
						// <td>".$row->nopay_e."</td>
						// <td>".$row->free_rate."</td>
						// <td style='max-width:230px;white-space:normal;'>".$row->detail."</td>
						// <!-- td>
							// <button class='editFree btn-warning'".
								// "formnopays='".$row->nopay_s."'".
								// "formnopaye='".$row->nopay_e."'".
								// "formrate='".$row->free_rate."'".
								// "formdetail='".$row->detail."'".
								// " disabled ".
							// "><span class='glyphicon glyphicon-edit'> แก้ไข</span></button>
							// <button class='deleteFree btn-danger'".
								// "formnopays='".$row->nopay_s."'".
								// "formnopaye='".$row->nopay_e."'".
								// "formrate='".$row->free_rate."'".
								// "formdetail='".$row->detail."'".
								// " disabled ".
							// "><span class='glyphicon glyphicon-trash'> ลบ</span></button>
						// </td -->
					// </tr>
				// ";
			// }
		// }
		
		// $html = "
			// <div id='panel'>
				// <div class='row' style='border:1px dotted #aaa;background-color:#d5f2ba;'>
					// <h3>
						// <div class='col-sm-10 col-sm-offset-1 text-primary'>
							// <span class='toggleData glyphicon glyphicon-minus' thisc='toggleData1' style='cursor:pointer;'>&emsp;ข้อมูลรถ</span>
						// </div>
					// </h3>
					// <div class='row'>
						// <div class='col-sm-2 col-sm-offset-1'>
							// <div class='col-sm-12'>	
								// <div class='form-group'>	
									// รุ่น
									// <select id='FMODEL' class='form-control'><option>{$arrs["model"]}</option></select>
								// </div>
							// </div>
							// <div class='col-sm-12'>	
								// <div class='form-group'>	
									// แบบ
									// <select id='FBAAB' class='form-control'><option>{$arrs["baab"]}</option></select>
								// </div>
							// </div>
							// <div class='col-sm-12'>	
								// <div class='form-group'>
									// สี
									// <select id='FCOLOR' class='form-control'><option>{$arrs["color"]}</option></select>
								// </div>
							// </div>
							// <div class='col-sm-12'>
								// <div class='form-group'>
									// กิจกรรมการขาย
									// <select id='FACTI' class='form-control'><option>{$arrs["ACTICOD"]}</option></select>
								// </div>
							// </div>
						// </div>
						
						// <div class='col-sm-4'>
							// <div class='col-sm-6'>
								// <div class='form-group'>
									// บังคับใช้ จาก
									// <input type='text' id='FEVENTS' value='{$arrs["event_s"]}' disabled class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' maxlength=10>
								// </div>
							// </div>
							// <div class='col-sm-6'>
								// <div class='form-group'>
									// บังคับใช้ ถึง
									// <input type='text' id='FEVENTE' value='{$arrs["event_e"]}' ".($arrs["event_e"] == ""?"":"disabled")." class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' maxlength=10>
								// </div>
							// </div>
							// <div class='col-sm-12'>
								// <div class='form-group'>
									// ชื่อเรียก
									// <input type='text' id='FEVENTNAME' value='{$arrs["name"]}' class='form-control input-sm' maxlength=500>
								// </div>
							// </div>
							// <div class='col-sm-12'>	
								// <div class='form-group'>	
									// ลักษณะ
									// <textarea type='text' id='FDETAIL' class='form-control' rows='2' maxlength=2000>{$arrs["details"]}</textarea>
								// </div>
							// </div>
							// <div class='col-sm-6'>
								// <div class='form-group'>
									// ราคาสด
									// <input type='text' id='FPRICE' value='{$arrs["price"]}' class='form-control input-sm jzAllowNumber'>
								// </div>
							// </div>
							// <div class='col-sm-6'>
								// <div class='form-group'>
									// ราคาผลัด
									// <input type='text' id='FPRICE2' value='{$arrs["pricespecial"]}' class='form-control input-sm jzAllowNumber'>
								// </div>
							// </div>
						// </div>
						
						// <div class='col-sm-4'>
							// <div class='col-sm-12'>
								// <div class='form-group'>
									// สาขา
									// <select id='FLOCAT' multiple='multiple' size='10' name='duallistbox_demo1[]'>{$locatopt}</select>
								// </div>
							// </div>
						// </div>
					// </div>
					
					// <div class='col-sm-7' style='border:1px dotted #aaa;background-color:#fff;'>
						// <div id='table-fixed-stdfa' class='col-sm-12' style='width:100%;overflow:auto;font-size:8pt;'>
							// <table id='table-stdfa' class='table table-bordered' cellspacing='0' width='calc(100% - 1px)'>
								// <thead>	
									// <tr align='center'>
										// <th colspan='9' style='vertical-align:middle;background-color:#c8e6b7;'>
											// Standard การดาวน์รถ
										// </th>
									// </tr>
									// <tr align='center'>
										// <th style='vertical-align:middle;background-color:#c8e6b7;'>เงินดาวน์</th>
										// <th style='vertical-align:middle;background-color:#c8e6b7;'>เงินดาวน์</th>
										// <th style='vertical-align:middle;background-color:#c8e6b7;'>ดอกเบี้ย</th>
										// <th style='vertical-align:middle;background-color:#c8e6b7;'>ค่าประกัน</th>
										// <th style='vertical-align:middle;background-color:#c8e6b7;'>ค่าโอน</th>
										// <th style='vertical-align:middle;background-color:#c8e6b7;'>ค่าทบ. จดใหม่</th>
										// <th style='vertical-align:middle;background-color:#c8e6b7;'>ค่าพรบ.</th>
										// <th style='vertical-align:middle;background-color:#c8e6b7;'>คูปอง</th>
										// <!-- th style='vertical-align:middle;background-color:#c8e6b7;'>###</th -->
									// </tr>
								// </thead>
								// <tbody style='background-color:whiteSmoke;'>
									// {$arrs["down"]}
								// </tbody>
								// <tfoot>
									// <tr align='center'>
										// <th style='vertical-align:middle;background-color:#c8e6b7;'>เงินดาวน์</th>
										// <th style='vertical-align:middle;background-color:#c8e6b7;'>เงินดาวน์</th>
										// <th style='vertical-align:middle;background-color:#c8e6b7;'>ดอกเบี้ย</th>
										// <th style='vertical-align:middle;background-color:#c8e6b7;'>ค่าประกัน</th>
										// <th style='vertical-align:middle;background-color:#c8e6b7;'>ค่าโอน</th>
										// <th style='vertical-align:middle;background-color:#c8e6b7;'>ค่าทบ. จดใหม่</th>
										// <th style='vertical-align:middle;background-color:#c8e6b7;'>ค่าพรบ.</th>
										// <th style='vertical-align:middle;background-color:#c8e6b7;'>คูปอง</th>
										// <!-- th style='vertical-align:middle;background-color:#c8e6b7;'>###</th -->
									// </tr>
									// <!-- tr align='center'>
										// <th colspan='9' style='vertical-align:middle;background-color:#c8e6b7;'>
											// <button id='btnAddDwn' class='btn btn-warning btn-block'><span class='glyphicon glyphicon-plus'> เพิ่ม</span></button>
										// </th>
									// </tr -->
								// </tfoot>
							// </table>
						// </div>
					// </div>
					// <div class='col-sm-5' style='border:1px dotted #aaa;background-color:#fff;'>	
						// <div id='table-fixed-stdfree' class='col-sm-12' style='width:100%;overflow:auto;font-size:8pt;'>
							// <table id='table-stdfree' class='table table-bordered' cellspacing='0' width='calc(100% - 1px)'>
								// <thead>
									// <tr align='center'>
										// <th colspan='5' style='vertical-align:middle;background-color:#c8e6b7;'>
											// Standard ของแถม
										// </th>
									// </tr>
									// <tr align='center'>
										// <th style='vertical-align:middle;background-color:#c8e6b7;'>งวด</th>
										// <th style='vertical-align:middle;background-color:#c8e6b7;'>งวด</th>
										// <th style='vertical-align:middle;background-color:#c8e6b7;'>ของแถม</th>
										// <th style='vertical-align:middle;background-color:#c8e6b7;'>หมายเหตุ</th>
										// <!-- th style='vertical-align:middle;background-color:#c8e6b7;'>###</th -->
									// </tr>
								// </thead>
								// <tbody style='background-color:whiteSmoke;'>
									// {$arrs["package"]}
								// </tbody>
								// <tfoot>
									// <tr align='center'>
										// <th style='vertical-align:middle;background-color:#c8e6b7;'>งวด</th>
										// <th style='vertical-align:middle;background-color:#c8e6b7;'>งวด</th>
										// <th style='vertical-align:middle;background-color:#c8e6b7;'>ของแถม</th>
										// <th style='vertical-align:middle;background-color:#c8e6b7;'>หมายเหตุ</th>
										// <!-- th style='vertical-align:middle;background-color:#c8e6b7;'>###</th -->
									// </tr>
									// <!-- tr align='center'>
										// <th colspan='5' style='vertical-align:middle;background-color:#c8e6b7;'>
											// <button id='btnAddFree' class='btn btn-warning btn-block'><span class='glyphicon glyphicon-plus'> เพิ่ม</span></button>
										// </th>
									// </tr -->
								// </tfoot>
							// </table>
						// </div>
					// </div>
					
					// <div class='col-sm-12'>
						// <br>
						// <div class='col-sm-2 col-sm-offset-10'>	
							// <button id='btnSave' class='btn btn-primary btn-block' stdid='{$stdid}' plrank='{$stdrank}'><span class='glyphicon glyphicon-floppy-disk'> บันทึก</span></button>
							// <br>
						// </div>						
					// </div>					
				// </div>
			// </div>
		// ";
		
		// $response = array("html"=>$html,"status"=>true);
		// echo json_encode($response);
	// }
	
	function searchDetail(){
		$stdid = $_POST["stdid"];
		$subid = $_POST["subid"];
		
		$sql = "
			select distinct a.STDID,b.SUBID,a.MODEL 
				,convert(varchar(8),b.EVENTStart,112) as EVENTStart
				,convert(varchar(8),b.EVENTEnd,112) as EVENTEnd
				,b.STDNAME,b.STDDESC,case when b.STAT='N' then 'รถใหม่' else 'รถเก่า' end STAT
			from {$this->MAuth->getdb('STDVehicles')} a
			inner join {$this->MAuth->getdb('STDVehiclesDetail')} b on a.STDID=b.STDID
			left join {$this->MAuth->getdb('STDVehiclesACTI')} c on b.STDID=c.STDID and b.SUBID=c.SUBID
			left join {$this->MAuth->getdb('STDVehiclesBAAB')} d on b.STDID=d.STDID and b.SUBID=d.SUBID
			left join {$this->MAuth->getdb('STDVehiclesCOLOR')} e on b.STDID=e.STDID and b.SUBID=e.SUBID
			left join {$this->MAuth->getdb('STDVehiclesLOCAT')} f on b.STDID=f.STDID and b.SUBID=f.SUBID
			where 1=1 and a.STDID={$stdid} and a.SUBID={$subid}
		";
		
	}
	
	function loadSTD(){
		$sql = "
			select distinct a.STDID,b.SUBID,a.MODEL 
				,convert(varchar(8),b.EVENTStart,112) as EVENTStart
				,convert(varchar(8),b.EVENTEnd,112) as EVENTEnd
				,b.STDNAME,b.STDDESC
				,case when b.STAT='N' then 'รถใหม่' else 'รถเก่า' end STATDesc
				,b.STAT
			from {$this->MAuth->getdb('STDVehicles')} a
			inner join {$this->MAuth->getdb('STDVehiclesDetail')} b on a.STDID=b.STDID
			left join {$this->MAuth->getdb('STDVehiclesACTI')} c on b.STDID=c.STDID and b.SUBID=c.SUBID
			left join {$this->MAuth->getdb('STDVehiclesBAAB')} d on b.STDID=d.STDID and b.SUBID=d.SUBID
			left join {$this->MAuth->getdb('STDVehiclesCOLOR')} e on b.STDID=e.STDID and b.SUBID=e.SUBID
			left join {$this->MAuth->getdb('STDVehiclesLOCAT')} f on b.STDID=f.STDID and b.SUBID=f.SUBID
			where 1=1 and a.STDID='".@$_POST["stdid"]."' and b.SUBID='".@$_POST["subid"]."'
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$data = array();
		$data["FEVENTS"] 	= "";
		$data["FEVENTE"] 	= "";
		$data["FEVENTNAME"] = "";
		$data["FDETAIL"] 	= "";
		$data["FSTAT"] 		= array();
		$data["STAT"] 		= "";
		$data["tb_car_old"] = "";
		
		$data["table_stdfa"] 	= "";
		$data["table_stdfree"] 	= "";
		
		$data["FACTI"]  = array();
		$data["FMODEL"] = array();
		$data["FBAAB"]  = array();
		$data["FCOLOR"] = array();
		$data["FLOCAT"] = array();
		
		if($query->row()){
			foreach($query->result() as $row){
				$data["FEVENTS"] 	= $this->Convertdate(2,$row->EVENTStart);
				$data["FEVENTE"] 	= $this->Convertdate(2,$row->EVENTEnd);
				$data["FEVENTNAME"] = $row->STDNAME;
				$data["FDETAIL"] 	= $row->STDDESC;
				$data["FMODEL"][] 	= $row->MODEL;
				$data["FSTAT"][]	= $row->STAT;
				
			}
		}
		
		$sql = "
			select c.ACTICOD
			from {$this->MAuth->getdb('STDVehicles')} a
			left join {$this->MAuth->getdb('STDVehiclesDetail')} b on a.STDID=b.STDID
			left join {$this->MAuth->getdb('STDVehiclesACTI')} c on b.STDID=c.STDID and b.SUBID=c.SUBID
			where 1=1 and a.STDID='".@$_POST["stdid"]."' and b.SUBID='".@$_POST["subid"]."' and c.ACTIVE='yes'
		";
		$query = $this->db->query($sql);
		
		if($query->row()){
			foreach($query->result() as $row){
				$data["FACTI"][] = $row->ACTICOD;
			}
		}
		
		$sql = "
			select c.BAAB
			from {$this->MAuth->getdb('STDVehicles')} a
			left join {$this->MAuth->getdb('STDVehiclesDetail')} b on a.STDID=b.STDID
			left join {$this->MAuth->getdb('STDVehiclesBAAB')} c on b.STDID=c.STDID and b.SUBID=c.SUBID
			where 1=1 and a.STDID='".@$_POST["stdid"]."' and b.SUBID='".@$_POST["subid"]."' and c.ACTIVE='yes'
		";
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$data["FBAAB"][] = $row->BAAB;
			}
		}
		
		$sql = "
			select c.COLOR
			from {$this->MAuth->getdb('STDVehicles')} a
			left join {$this->MAuth->getdb('STDVehiclesDetail')} b on a.STDID=b.STDID
			left join {$this->MAuth->getdb('STDVehiclesCOLOR')} c on b.STDID=c.STDID and b.SUBID=c.SUBID
			where 1=1 and a.STDID='".@$_POST["stdid"]."' and b.SUBID='".@$_POST["subid"]."' and c.ACTIVE='yes'
		";
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$data["FCOLOR"][] = $row->COLOR;
			}
		}
		
		$sql = "
			select c.LOCAT
			from {$this->MAuth->getdb('STDVehicles')} a
			left join {$this->MAuth->getdb('STDVehiclesDetail')} b on a.STDID=b.STDID
			left join {$this->MAuth->getdb('STDVehiclesLOCAT')} c on b.STDID=c.STDID and b.SUBID=c.SUBID
			where 1=1 and a.STDID='".@$_POST["stdid"]."' and b.SUBID='".@$_POST["subid"]."' and c.ACTIVE='yes'
		";
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$data["FLOCAT"][] = $row->LOCAT;
			}
		}
		
		$sql = "
			select c.PRICE,c.PRICES
			from {$this->MAuth->getdb('STDVehicles')} a
			left join {$this->MAuth->getdb('STDVehiclesDetail')} b on a.STDID=b.STDID
			left join {$this->MAuth->getdb('STDVehiclesPRICE')} c on b.STDID=c.STDID and b.SUBID=c.SUBID
			where 1=1 and a.STDID='".@$_POST["stdid"]."' and b.SUBID='".@$_POST["subid"]."' and c.ACTIVE='yes'
		";
		$query = $this->db->query($sql);
		
		if($query->row()){
			$data["tb_car_old"] = "";
			foreach($query->result() as $row){
				$data["tb_car_old"] .= "
					<tr>
						<td>".number_format($row->PRICE,2)."</td>
						<td>".($row->PRICES == "9999999.99" ? "ขึ้นไป":number_format($row->PRICES,2))."</td>
						<td>
							<button class='btn_car_old_delete btn btn-xs btn-danger' 
								FPRICE='".$row->PRICE."'
								TPRICE='".$row->PRICES."'>
								<span class='glyphicon glyphicon-trash'> ลบ</span>
							</button>
						</td>
					</tr>
				";
			}
		}
		
		$sql = "
			select a.STDID,b.SUBID,a.MODEL 
				,b.EVENTStart,b.EVENTEnd
				,b.STDNAME,b.STDDESC
				,c.PRICE
				,c.PRICES
				,d.DOWNS
				,d.DOWNE
				,d.INTERESTRT
				,d.INTERESTRT_GVM
				,d.INSURANCE
				,d.TRANSFERS
				,d.REGIST
				,d.ACT
				,d.COUPON
				,d.APPROVE
				,case when d.APPROVE = 'Y' then 'ต้องขออนุมัติ' else 'ไม่ต้องขออนุมัติ' end as APPROVEDESC
			from {$this->MAuth->getdb('STDVehicles')} a
			left join {$this->MAuth->getdb('STDVehiclesDetail')} b on a.STDID=b.STDID
			left join {$this->MAuth->getdb('STDVehiclesPRICE')} c on b.STDID=c.STDID and b.SUBID=c.SUBID
			left join {$this->MAuth->getdb('STDVehiclesDown')} d on c.STDID=d.STDID and c.SUBID=d.SUBID and c.PRICE=d.PRICES and c.PRICES=d.PRICEE	
			where 1=1 and a.STDID='".@$_POST["stdid"]."' and b.SUBID='".@$_POST["subid"]."' 
				and c.ACTIVE='yes' and d.ACTIVE='yes'
		";
		$query = $this->db->query($sql);
		$nrow = 1;
		if($query->row()){
			$data["table_stdfa"] = "";
			foreach($query->result() as $row){
				if($nrow++ == 1){
					$color = "style='color:blue;'";
					$PRICE = $row->PRICE;
				}else if($PRICE != $row->PRICE){
					$color = ($color == "style='color:blue;'" ? "":"style='color:blue;'");						
					$PRICE = $row->PRICE;
				}
				
				$data["table_stdfa"] .= "
					<tr {$color}>
						<td>
							<button class='editDwn btn btn-xs btn-warning'
								formpriceFP='".$row->PRICE."'
								formpriceTP='".$row->PRICES."'
								formdwns='".$row->DOWNS."'
								formdwne='".$row->DOWNE."'
								forminterest='".$row->INTERESTRT."'
								forminterest2='".$row->INTERESTRT_GVM."'
								forminsurance='".$row->INSURANCE."'
								formtrans='".$row->TRANSFERS."'
								formregist='".$row->REGIST."'
								formact='".$row->ACT."'
								formcoupon='".$row->COUPON."'
								formapprv='".$row->APPROVE."'
								><span class='glyphicon glyphicon-edit'> แก้ไข</span></button>
							<button class='deleteDwn btn btn-xs btn-danger'
								formpriceFP='".$row->PRICE."'
								formpriceTP='".$row->PRICES."'
								formdwns='".$row->DOWNS."'
								formdwne='".$row->DOWNE."'
								forminterest='".$row->INTERESTRT."'
								forminterest2='".$row->INTERESTRT_GVM."'
								forminsurance='".$row->INSURANCE."'
								formtrans='".$row->TRANSFERS."'
								formregist='".$row->REGIST."'
								formact='".$row->ACT."'
								formcoupon='".$row->COUPON."'
								formapprv='".$row->APPROVE."'
								><span class='glyphicon glyphicon-trash'> ลบ</span></button>	
						</td>
						<td>".(number_format($row->PRICE,2))."-".($row->PRICES == '9999999.99' ? "ขึ้นไป":number_format($row->PRICES,2))."</td>
						<td>".(number_format($row->DOWNS,2))."-".($row->DOWNE == '9999999.99' ? "ขึ้นไป":number_format($row->DOWNE,2))."</td>
						<td>".(number_format($row->INTERESTRT,2))." ".($row->INTERESTRT_GVM == "" ? "" : "(".number_format($row->INTERESTRT_GVM,2).")")."</td>
						<td>".($row->INSURANCE == "" ? "":number_format($row->INSURANCE,2))."</td>
						<td>".($row->TRANSFERS == "" ? "":number_format($row->TRANSFERS,2))."</td>
						<td>".($row->REGIST == "" ? "":number_format($row->REGIST,2))."</td>
						<td>".($row->ACT == "" ? "":number_format($row->ACT,2))."</td>
						<td>".($row->COUPON == "" ? "":number_format($row->COUPON,2))."</td>
						<td>".($row->APPROVE)."</td>
					</tr>
				";
			}
		}
		
		$sql = "
			select a.STDID,b.SUBID,a.MODEL 
				,b.EVENTStart,b.EVENTEnd
				,b.STDNAME,b.STDDESC
				,c.PRICE
				,c.PRICES
				,d.DOWNS
				,d.DOWNE			
				,e.FORCUS
				,case when e.FORCUS = 'C' then 'คนซื้อ'
					when e.FORCUS = 'S' then 'ผู้แนะนำ'
					when e.FORCUS = 'I' then 'คนค้ำ'
					else '' end FORCUSDesc
				,e.NOPAYS
				,e.NOPAYE
				,e.RATE	
				,e.MEMO1
			from {$this->MAuth->getdb('STDVehicles')} a
			left join {$this->MAuth->getdb('STDVehiclesDetail')} b on a.STDID=b.STDID
			left join {$this->MAuth->getdb('STDVehiclesPRICE')} c on b.STDID=c.STDID and b.SUBID=c.SUBID
			left join {$this->MAuth->getdb('STDVehiclesDown')} d on c.STDID=d.STDID and c.SUBID=d.SUBID and c.PRICE=d.PRICES and c.PRICES=d.PRICEE	
			left join {$this->MAuth->getdb('STDVehiclesPackages')} e on e.STDID=d.STDID and e.SUBID=d.SUBID and e.PRICES=d.PRICES and e.PRICEE=d.PRICEE and d.DOWNS=e.DOWNS and d.DOWNE=e.DOWNE
			where 1=1 and a.STDID='".@$_POST["stdid"]."' and b.SUBID='".@$_POST["subid"]."'
				and c.ACTIVE='yes' and d.ACTIVE='yes' and e.ACTIVE='yes'
			order by e.FORCUS, c.PRICE ,d.DOWNS ,e.NOPAYS 
		";
		$query = $this->db->query($sql);
		$nrow = 1;
		if($query->row()){
			$data["table_stdfree"] = "";
			foreach($query->result() as $row){
				if($nrow++ == 1){
					$color = "style='color:blue;'";
					$PRICE = $row->PRICE;
				}else if($PRICE != $row->PRICE){
					$color = ($color == "style='color:blue;'" ? "":"style='color:blue;'");
					$PRICE = $row->PRICE;
				}
				
				$data["table_stdfree"] .= "
					<tr {$color}>
						<td>
							<button class='editFree btn btn-xs btn-warning'
								formpriceFP='".$row->PRICE."'
								formpriceTP='".$row->PRICES."'
								formdwns='".$row->DOWNS."'
								formdwne='".$row->DOWNE."'
								formtypeV='".$row->FORCUS."'
								formrate='".$row->RATE."'
								formnopays='".$row->NOPAYS."'
								formnopaye='".$row->NOPAYE."'
								formdetail='".$row->MEMO1."'
								><span class='glyphicon glyphicon-edit'> แก้ไข</span></button>
							<button class='deleteFree btn btn-xs btn-danger'
								formpriceFP='".$row->PRICE."'
								formpriceTP='".$row->PRICES."'
								formdwns='".$row->DOWNS."'
								formdwne='".$row->DOWNE."'
								formtypeV='".$row->FORCUS."'
								formrate='".$row->RATE."'
								formnopays='".$row->NOPAYS."'
								formnopaye='".$row->NOPAYE."'
								formdetail='".$row->MEMO1."'
								><span class='glyphicon glyphicon-trash'> ลบ</span></button>	
						</td>
						<td>".(number_format($row->PRICE,2))."-".($row->PRICES == '9999999.99' ? "ขึ้นไป":number_format($row->PRICES,2))."</td>
						<td>".(number_format($row->DOWNS,2))."-".($row->DOWNE == '9999999.99' ? "ขึ้นไป":number_format($row->DOWNE,2))."</td>
						<td>".($row->FORCUSDesc)."</td>
						<td>".($row->NOPAYS == "" ? "":number_format($row->NOPAYS,0))."</td>
						<td>".($row->NOPAYE == "99" ? "ขึ้นไป":number_format($row->NOPAYE,0))."</td>
						<td>".($row->RATE == "" ? "":number_format($row->RATE,2))."</td>
						<td>".($row->MEMO1)."</td>
					</tr>
				";
			}
		}
		return $data;
	}
	
	function loadform(){
		$arrs = array();
		if($_POST["event"] == "add"){
			$arrs["STDID"] 		= "Auto Genarate";
			$arrs["SUBID"] 		= "Auto Genarate";
			$arrs["FEVENTS"] 	= "";
			$arrs["FEVENTE"] 	= "";
			$arrs["FEVENTNAME"] = "";
			$arrs["FDETAIL"] 	= "";
			$arrs["FACTI"] 		= $this->MMAIN->Option_get_acti(array());
			
			$arrs["FMODEL"] 	= $this->MMAIN->Option_get_model(array());
			$arrs["FBAAB"]	 	= $this->MMAIN->Option_get_baab(array("model"=>"","baab"=>array()));
			$arrs["FCOLOR"] 	= $this->MMAIN->Option_get_color(array());
			$arrs["FSTAT"]	 	= $this->MMAIN->Option_get_stat(array());
			$arrs["STAT"]	 	= "N";
			$arrs["tb_car_old"]	= "";
			
			$arrs["FLOCAT"]	 	= $this->MMAIN->Option_get_locat(array());
			$arrs["table_stdfa"]	= "";
			$arrs["table_stdfree"]	= "";
		}else{
			$now = $this->loadSTD();
			$arrs["STDID"] 		= $_POST["stdid"];
			$arrs["SUBID"] 		= $_POST["subid"];
			$arrs["FEVENTS"] 	= $now["FEVENTS"];
			$arrs["FEVENTE"] 	= $now["FEVENTE"];
			$arrs["FEVENTNAME"] = $now["FEVENTNAME"];
			$arrs["FDETAIL"] 	= $now["FDETAIL"];
			$arrs["FACTI"] 		= $this->MMAIN->Option_get_acti($now["FACTI"]);
			
			$arrs["FMODEL"] 	= $this->MMAIN->Option_get_model($now["FMODEL"]);
			$arrs["FBAAB"]	 	= $this->MMAIN->Option_get_baab(array("model"=>$now["FMODEL"][0],"baab"=>$now["FBAAB"]));
			$arrs["FCOLOR"] 	= $this->MMAIN->Option_get_color($now["FCOLOR"]);
			$arrs["FSTAT"]	 	= $this->MMAIN->Option_get_stat($now["FSTAT"]);
			$arrs["STAT"]	 	= $now["FSTAT"][0];
			$arrs["tb_car_old"]	= $now["tb_car_old"];
			
			$arrs["FLOCAT"]	 	= $this->MMAIN->Option_get_locat($now["FLOCAT"]);
			$arrs["table_stdfa"]	= $now["table_stdfa"];
			$arrs["table_stdfree"]	= $now["table_stdfree"];
		}
		//echo $arrs["FBAAB"]; exit;
		$html = "
			<div id='wizard-std' class='wizard-wrapper'>    
				<div class='wizard'>
					<form id='demo-form2' action='' class='lobi-form' novalidate='novalidate'>
						<ul class='wizard-tabs wizard-tab-balls nav-justified nav nav-pills'>
							<li class='active'>
								<a href='#tab11' prev='#' data-toggle='tab' aria-expanded='true'>
									<span class='step'>1</span>
									<span class='title'>ข้อมูล standard</span>
								</a>
							</li>
							<li>
								<a href='#tab22' prev='#tab11' data-toggle='tab'>
									<span class='step'>2</span>
									<span class='title'>ข้อมูลรถ</span>
								</a>
							</li>
							<li>
								<a href='#tab33' prev='#tab22' data-toggle='tab'>
									<span class='step'>3</span>
									<span class='title'>สาขา</span>
								</a>
							</li>
							
							<li>
								<a href='#tab44' prev='#tab33' data-toggle='tab'>
									<span class='step'>4</span>
									<span class='title'>Standard การดาวน์รถ</span>
								</a>
							</li>							
							<li>
								<a href='#tab55' prev='#tab44' data-toggle='tab'>
									<span class='step'>5</span>
									<span class='title'>Standard ของแถม </span>
								</a>
							</li>
						</ul>
						<div class='tab-content bg-white'>
							".$this->getfromLeasingTab11($arrs)."
							".$this->getfromLeasingTab22($arrs)."
							".$this->getfromLeasingTab33($arrs)."
							".$this->getfromLeasingTab44($arrs)."
							".$this->getfromLeasingTab55($arrs)."
							
							<!-- ul class='pager'>
								<li class='previous first disabled' style='display:none;'><a href='javascript:void(0)'>First</a></li>
								<li class='previous disabled'><a href='javascript:void(0)'>ย้อนกลับ</a></li>
								<li class='next last' style='display:none;'><a href='javascript:void(0)'>Last</a></li>
								<li class='next'><a href='javascript:void(0)'>ถัดไป</a></li>
							</ul -->
						</div>
					</form>
				</div>
			</div>
			<div>
				<div class='col-sm-6 text-left'>
					<button id='btnUpload' ".($_POST["event"] == "add" ? "":"disabled")." class='btn btn-xs btn-info' style='width:100px;'><span class='glyphicon glyphicon-upload'> นำเข้า</span></button>
				</div>
				<div class='col-sm-6 text-right'>
					<button id='add_delete' class='btn btn-xs btn-danger' style='width:100px;'><span class='glyphicon glyphicon-trash'> ลบ</span></button>
					<button id='btnSave' event='".$_POST["event"]."' class='btn btn-xs btn-primary' style='width:100px;'><span class='glyphicon glyphicon-floppy-disk'> บันทึก</span></button>
				</div>
			</div>
		";
		
		$response = array('html'=>$html,'status'=>true);
		echo json_encode($response);
	}
	
	function getfromLeasingTab11($arrs){ 
		$html = "
			<div class='tab-pane active' name='tab11' style='height:calc(100vh - 230px);overflow:auto;'>
				<fieldset style='height:100%'>
					<div class='row'>
						<div class='col-sm-6'>
							<div class='col-sm-6'>	
								<div class='form-group'>	
									STDID
									<input type='text' id='STDID' class='form-control input-sm' placeholder='' value='".$arrs["STDID"]."' readonly>
								</div>
							</div>
							<div class='col-sm-6'>	
								<div class='form-group'>	
									SUBID
									<input type='text' id='SUBID' class='form-control input-sm' placeholder='' value='".$arrs["SUBID"]."' readonly>
								</div>
							</div>
							<div class='col-sm-6'>	
								<div class='form-group'>	
									บังคับใช้ จาก
									<input type='text' id='FEVENTS' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' maxlength=10 title='".$arrs["FEVENTS"]."' value='".$arrs["FEVENTS"]."'>
								</div>
							</div>
							<div class='col-sm-6'>	
								<div class='form-group'>	
									บังคับใช้ ถึง
									<input type='text' id='FEVENTE' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' maxlength=10 title='".$arrs["FEVENTE"]."' value='".$arrs["FEVENTE"]."'> 
								</div>
							</div>
							<div class='col-sm-12'>	
								<div class='form-group'>	
									ชื่อเรียก
									<input type='text' id='FEVENTNAME' class='form-control input-sm' maxlength=500 value='".$arrs["FEVENTNAME"]."'>
								</div>
							</div>
							<div class='col-sm-12'>	
								<div class='form-group'>	
									ลักษณะ
									<textarea type='text' id='FDETAIL' class='form-control' rows='2' maxlength=2000>".$arrs["FDETAIL"]."</textarea>
								</div>
							</div>
						</div>
						
						<div class='col-sm-6'>
							<div class='col-sm-12'>	
								<div class='form-group'>	
									กิจกรรมการขาย
									<!-- select id='FACTI' class='form-control'></select -->
									<select id='FACTI' multiple='multiple' size='10' 
										data-toggle='tooltip' data-placement='right' 
										data-html='true' data-original-title='tooltip'
									name='duallistbox_demo1[]'>{$arrs["FACTI"]}</select>
									<!-- h6 class='text-danger'><i>(ทั้งหมด ให้เว้นว่างไว้)</i></h6 -->
								</div>
							</div>
						</div>

						
						
					</div>
				</fieldset>
			</div>
		";
		
		return $html;
	}
	
	function getfromLeasingTab22($arrs){ 
		$html = "
			<div class='tab-pane' name='tab22' style='height:calc(100vh - 230px);overflow:auto;'>
				<fieldset style='height:100%'>
					<div class='row'>
						<div class='col-sm-8'>
							<div class='form-group'>	
								รุ่น
								<select id='FMODEL' class='form-control'>{$arrs["FMODEL"]}</select>
							</div>
						</div>
						<div class='col-sm-4'>	
							<div class='form-group'>
								สถานะภาพรถ
								<select id='FSTAT' class='form-control'>{$arrs["FSTAT"]}</select>
							</div>
						</div>
						
						<div class='col-sm-4'>	
							<div class='form-group'>	
								แบบ
								<select id='FBAAB' multiple='multiple' size='10' name='duallistbox_demo1[]'>
									{$arrs["FBAAB"]}
								</select>
								<h6 class='text-danger'><i>(ทุกแบบ ให้เว้นว่างไว้)</i></h6>
							</div>
						</div>
						
						<div class='col-sm-4'>	
							<div class='form-group'>
								สี
								<select id='FCOLOR' multiple='multiple' size='10' name='duallistbox_demo1[]'>
									{$arrs["FCOLOR"]}
								</select>
								<h6 class='text-danger'><i>(ทุกสี ให้เว้นว่างไว้)</i></h6>
							</div>
						</div>
						<div id='FPRICEO' class='col-sm-4'>	
							<div class='col-sm-6'>	
								<div class='form-group'>	
									<span class='FPRICEN' ".($arrs["STAT"] == "N"?"":"hidden").">ราคาสด</span>
									<span class='FPRICEO' ".($arrs["STAT"] == "O"?"":"hidden").">ช่วงราคารถ จาก</span>
									<input type='text' id='F_OLD_PRICE' class='form-control input-sm jzAllowNumber'>
								</div>
							</div>
							<div class='col-sm-6'>	
								<div class='form-group'>
									<span class='FPRICEN' ".($arrs["STAT"] == "N"?"":"hidden").">ราคาผลัด</span>
									<span class='FPRICEO' ".($arrs["STAT"] == "O"?"":"hidden").">ช่วงราคารถ ถึง</span>
									<input type='text' id='F_OLD_PRICE2' class='form-control input-sm jzAllowNumber'>
								</div>
							</div>
							<div class='col-sm-12'>	
								<button id='btnAddPSTD' class='btn btn-block btn-xs btn-primary'><span class='glyphicon glyphicon-plus'> เพิ่ม</span></button>
							</div>
							<div class='col-sm-12'>	
								<table id='tb_car_old' class='table table-bordered' cellspacing='0' style='width:100%;'>
									<thead>
										<tr>
											<th>".($arrs["STAT"] == "N"?"ราคาสด":"ช่วงราคารถ จาก")."</th>
											<th>".($arrs["STAT"] == "N"?"ราคาผลัด":"ช่วงราคารถ ถึง")."</th>
											<th>#</th>
										</tr>
									</thead>	
									<tbody>".$arrs["tb_car_old"]."</tbody>
								</table>
							</div>
						</div>	
					</div>
				</fieldset>
			</div>
		";
		
		return $html;
	}
	function getfromLeasingTab33($arrs){
		$html = "
			<div class='tab-pane' name='tab33' style='height:calc(100vh - 230px);overflow:auto;'>
				<fieldset style='height:100%'>
					<div class='row'>
						<div class='col-sm-6 col-sm-offset-3'>
							<div class='col-sm-12'>	
								<div class='form-group'>
									สาขา
									<select id='FLOCAT' multiple='multiple' size='10' name='duallistbox_demo1[]'>{$arrs["FLOCAT"]}</select>
								</div>
							</div>
						</div>
					</div>
				</fieldset>
			</div>
		";
		
		return $html;
	}
	
	function getfromLeasingTab44($arrs){
		$body = "";
		/*
		for($i=0;$i<20;$i++){
			$body .= "
				<tr>
					<th style='vertical-align:middle;'>
						<button id='btnAddDwn' class='btn btn-xs btn-warning'><span class='glyphicon glyphicon-plus'> เพิ่ม</span></button>
					</th>
					<th style='vertical-align:middle;'>ราคารถ</th>
					<th style='vertical-align:middle;'>เงินดาวน์</th>
					<th style='vertical-align:middle;'>ดอกเบี้ย</th>
					<th style='vertical-align:middle;'>ค่าประกัน</th>
					<th style='vertical-align:middle;'>ค่าโอน</th>
					<th style='vertical-align:middle;'>ค่าทบ. จดใหม่</th>
					<th style='vertical-align:middle;'>ค่าพรบ.</th>
					<th style='vertical-align:middle;'>คูปอง</th>
				</tr>
			";
		}
		*/
		
		$html = "
			<div class='tab-pane' name='tab44' style='width:100%;height:calc(100vh - 230px);overflow:auto;'>
				<fieldset style='height:100%'>
					<div id='table-fixed-stdfa' class='col-sm-12' style='width:100%;overflow:auto;font-size:8pt;'>
						<table id='table-stdfa' class='table table-bordered' cellspacing='0' width='calc(100% - 1px)'>
							<thead>	
								<tr>
									<th colspan='10' style='vertical-align:middle;'>
										Standard การดาวน์รถ
									</th>
								</tr>
								<tr>
									<th style='width:150px;vertical-align:middle;'>
										<button id='btnAddDwn' class='btn btn-xs btn-warning'><span class='glyphicon glyphicon-plus'> เพิ่ม</span></button>
									</th>
									<th style='vertical-align:middle;'>ราคารถ</th>
									<th style='vertical-align:middle;'>เงินดาวน์</th>
									<th style='vertical-align:middle;'>ดอกเบี้ย</th>
									<th style='vertical-align:middle;'>ค่าประกัน</th>
									<th style='vertical-align:middle;'>ค่าโอน</th>
									<th style='vertical-align:middle;'>ค่าทบ. จดใหม่</th>
									<th style='vertical-align:middle;'>ค่าพรบ.</th>
									<th style='vertical-align:middle;'>คูปอง</th>
									<th style='vertical-align:middle;'>ขออนุมัติ</th>
								</tr>
							</thead>
							<tbody>".$arrs["table_stdfa"]."</tbody>
						</table>
					</div>
				</fieldset>
			</div>
		";
		return $html;
	}
	
	function getfromLeasingTab55($arrs){
		$html = "
			<div class='tab-pane' name='tab55' style='width:100%;height:calc(100vh - 230px);overflow:auto;'>
				<fieldset style='height:100%'>					
					<div id='table-fixed-stdfree' class='col-sm-12' style='width:100%;overflow:auto;font-size:8pt;'>
						<table id='table-stdfree' class='table table-bordered' cellspacing='0' width='100vw'>
							<thead>
								<tr align='center'>
									<th colspan='8' style='vertical-align:middle;'>
										Standard ของแถม
									</th>
								</tr>
								<tr align='center'>
									<th style='width:150px;vertical-align:middle;'>
										<button id='btnAddFree' class='btn btn-xs btn-warning'><span class='glyphicon glyphicon-plus'> เพิ่ม</span></button>
									</th>
									<th style='vertical-align:middle;'>ราคารถ</th>
									<th style='vertical-align:middle;'>ช่วงเงินดาวน์</th>
									<th style='vertical-align:middle;'>แถม</th>
									<th style='vertical-align:middle;'>งวด จาก</th>
									<th style='vertical-align:middle;'>งวด ถึง</th>
									<th style='vertical-align:middle;'>ของแถม</th>
									<th style='vertical-align:middle;'>หมายเหตุ</th>
								</tr>
							</thead>
							<tbody>".$arrs["table_stdfree"]."</tbody>
						</table>
					</div>
				</fieldset>
			</div>			
		";
		return $html;
	}
	
	function setRankPRICE(){
		$response = array("error"=>false,"msg"=>"");
		
		$FSTAT  = $_POST['FSTAT'];
		$FPRICE = trim($_POST['FPRICE']);
		$TPRICE = (trim($_POST['TPRICE']) == "" ? '9999999.99': trim($_POST['TPRICE']));
		$NPRICE = $_POST['NPRICE'];
		
		if(!is_numeric($FPRICE)){
			$response["error"] = true;
			$response["msg"] = "ผิดพลาดราคารถ ไม่ถูกต้อง !!!";
			echo json_encode($response); exit;
		}
		
		if(!is_numeric($TPRICE)){
			$response["error"] = true;
			$response["msg"] = "ผิดพลาดราคารถ ไม่ถูกต้อง !!!";
			echo json_encode($response); exit;
		}
	
		if($FPRICE > $TPRICE){
			$response["error"] = true;
			$response["msg"] = "ผิดพลาดช่วงราคารถ ไม่ถูกต้อง !!!";
			echo json_encode($response); exit;
		}
		
		if($FSTAT == "N" && is_array($NPRICE)){
			$response["error"] = true;
			$response["msg"] = "ผิดพลาด คุณระบุราคารถใหม่แล้ว";
			echo json_encode($response); exit;
		}
		
		if($FSTAT == "N" && $TPRICE == '9999999.99'){
			$response["error"] = true;
			$response["msg"] = "ผิดพลาด คุณยังไม่ระบุราคาผลัดรถใหม่";
			echo json_encode($response); exit;
		}
		
		$sql = "
			if object_id('tempdb..#tempdbRP') is not null drop table #tempdbRP;
			create table #tempdbRP (FPRICE decimal(9,2),TPRICE decimal(9,2));
		";
		// echo $sql;
		$this->db->query($sql);
		
		if(is_array($NPRICE)){
			$size = sizeof($NPRICE);
			for($i=0;$i<$size;$i++){
				$sql = "
					insert into #tempdbRP
					select '".$NPRICE[$i]["FPRICE"]."','".$NPRICE[$i]["TPRICE"]."'
				";
				// echo $sql;
				$this->db->query($sql);
			}
		}
		
		$sql = "
			if exists(
				select '' from #tempdbRP
				where '".$FPRICE."' between FPRICE and TPRICE
				union
				select '' from #tempdbRP
				where '".$TPRICE."' between FPRICE and TPRICE
			)
			begin
				delete #tempdbRP;
	
				insert into #tempdbRP
				select null as FPRICE,null as TPRICE
			end
			else 
			begin 			
				insert into #tempdbRP
				select '".$FPRICE."','".$TPRICE."'
			end
		";
		// echo $sql;
		$this->db->query($sql);
		
		$sql = "
			select * from #tempdbRP
			order by FPRICE
		";
		// echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				if($row->FPRICE != ""){
					$html .= "
						<tr>
							<td>".number_format($row->FPRICE,2)."</td>
							<td>".($row->TPRICE == "9999999.99" ? "ขึ้นไป":number_format($row->TPRICE,2))."</td>
							<td>
								<button class='btn_car_old_delete btn-danger' 
									FPRICE='".$row->FPRICE."'
									TPRICE='".$row->TPRICE."'> ลบ
								</button>
							</td>
						</tr>
					";					
				}else{
					$response["error"] = true;
					$response["msg"] = "ช่วงเงินดาวน์ซ้ำซ้อน โปรดตรวจสอบใหม่อีกครั้ง";
					echo json_encode($response); exit;
				}				
			}
		}else{
			$response["error"] = true;
			$response["msg"] = "Error no record !!";
			echo json_encode($response); exit;
		}
		
		
		$response["html"] = $html;
		echo json_encode($response);
	}	
	
	function datatablesArr(){
		$html = array();
		
		for($i=0;$i<20;$i++){
			$html["data"][$i][] = 5000;
			$html["data"][$i][] = 6000;
			$html["data"][$i][] = 1.5;
			$html["data"][$i][] = 2300;
			$html["data"][$i][] = 500;
			$html["data"][$i][] = 500;
			$html["data"][$i][] = 500;
			$html["data"][$i][] = "<input>";
		}
		
		echo json_encode($html);
	}
	
	function loadform_old(){
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
											Standard การดาวน์รถ
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
											Standard ของแถม
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
						<div class='col-sm-4'>	
							<button id='btnUpload' class='btn btn-warning'><span class='glyphicon glyphicon-upload'> Upload</span></button>
							
							<a id='btnDownload' class='btn btn-info' href='".base_url()."public/FileStandard.xlsx'>
								<span class='glyphicon glyphicon-download'> Download</span>
							</a>
						</div>
						
						<div class='col-sm-2 col-sm-offset-6'>	
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
	
	function getFormUPLOAD(){
		$html = "
			<div id='fileupload'></div>
		";
		
		$response = array("html"=>$html);
		echo json_encode($response);
	}
	
	function getDataINFile(){
		$this->load->library('excel');
		
		$file = $_FILES["myfile"]["tmp_name"];
		
		//read file from path
		$objPHPExcel = PHPExcel_IOFactory::load($file);
		
		//X ตรวจสอบว่ามีกี่ sheet
		//X $sheetCount = $objPHPExcel->getSheetCount();
		//X จะดึงข้อมูลแค่ sheet 1 เท่านั้น
		$sheetCount = 1; 
		for($sheetIndex=0;$sheetIndex<$sheetCount;$sheetIndex++){
			$objPHPExcel->setActiveSheetIndex($sheetIndex);
			//get only the Cell Collection
			$cell_collection = $objPHPExcel->getActiveSheet()->getCellCollection();
			 
			$arrs = array("now"=>1,"old"=>1); 
			//extract to a PHP readable array format			
			foreach ($cell_collection as $cell) {
				$column = $objPHPExcel->getActiveSheet()->getCell($cell)->getColumn();
				$row = $objPHPExcel->getActiveSheet()->getCell($cell)->getRow();
				$data_value = $objPHPExcel->getActiveSheet()->getCell($cell)->getValue();
				
				if($arrs["old"] == 1){
					$arrs["now"] = 1;
				}else if($arrs["old"] == $row){
					$arrs["now"] = $arrs["now"];
				}else{
					$arrs["now"] += 1;
				}
				//The header will/should be in row 1 only. of course, this can be modified to suit your need.
				if ($row == 1 and $sheetIndex == 0) {
					$header[$row][$column] = $data_value;
				} else {
					switch($column){
						case 'H': $arr_data[$arrs["now"]][$column] = $this->Convertdate(2,$data_value); break;
						case 'I': $arr_data[$arrs["now"]][$column] = $this->Convertdate(2,$data_value); break;
						default: $arr_data[$arrs["now"]][$column] = $data_value; break;
					}
				}
				
				
				$arrs["old"] = $row;
			}
		}
		
		$arrs = array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z","AA");
		$datasize = sizeof($arr_data);
		for($i=1;$i<=$datasize;$i++){
			foreach($arrs as $key => $val){
				if(!isset($arr_data[$i][$val])){
					$arr_data[$i][$val] = '';
				}
			}
		}
		//var_dump($arr_data); exit;
		
		$sql_origin = "";
		foreach($arr_data as $key => $val){
			if($key == 1){ $sql_origin .= "select "; }else{ $sql_origin .= "union all select "; }
			foreach($arr_data[$key] as $key2 => $val2){
				if($key2 != "A"){ $sql_origin .= ","; }
				$sql_origin .= "'".$val2."' as ".$key2." ";
			}
		}
		//echo $sql_origin; exit;
		
		# เช็คว่าข้อมูล รุ่น แบบ สี สถานะภาพรถ กิจกรรมการขาย วันที่บังคับใช้ สาขา เหมือนกันหรือไม่ 
		$sql_temp_01 	= "select count(*) as r from (select distinct A,B,C,D,E,F,G,H,I,J from (".$sql_origin.") as data) as data";
		//echo $sql_temp_01; exit;
		$query_temp_01 	= $this->db->query($sql_temp_01);
		$data_temp_01 	= $query_temp_01->row();
		if($data_temp_01->r > 1){
			$response = array("error"=>true,"errorMsg"=>"ผิดพลาด(E001) <br>โปรดตรวจสอบข้อมูล รุ่น แบบ สี สถานะภาพรถ กิจกรรมการขาย วันที่บังคับใช้ สาขา ใหม่อีกครั้งครับ");
			echo json_encode($response); exit;
		}
		
		# ตรวจสอบรุ่น แบบ สี กิจกรรมการขาย สาขา
		$sql_temp_02 	= "select distinct A,B,C,D,E,F,G,H,I,J from (".$sql_origin.") as data";
		$query_temp_02 	= $this->db->query($sql_temp_02);
		$data_temp_02 	= $query_temp_02->row();
		
		if(!in_array($data_temp_02->F,array("N","O"))){
			$response = array("error"=>true,"errorMsg"=>"ผิดพลาด(E002) <br>สถานะภาพรถต้องเป็น N หรือ O เท่านั้น <br>โปรดตรวจสอบข้อมูลใหม่อีกครั้ง");
			echo json_encode($response); exit;
		}
		
		$ex_baab = explode(',',$data_temp_02->D);
		for($i=0;$i<count($ex_baab);$i++){
			if(trim($ex_baab[$i]) != "ALL"){
				$sql = "
					select count(*) r from {$this->MAuth->getdb('SETBAAB')}
					where MODELCOD='".$data_temp_02->C."' and BAABCOD='".trim($ex_baab[$i])."'
				";
				$query = $this->db->query($sql);
				$data = $query->row();
				if($data->r == 0){
					$response = array("error"=>true,"errorMsg"=>"ผิดพลาด(E003) <br>ไม่พบข้อมูลรุ่น ".$data_temp_02->C." แบบ ".trim($ex_baab[$i])." <br>โปรดตรวจสอบข้อมูลใหม่อีกครั้ง");
					echo json_encode($response); exit;
				}
			}
		}
		
		$ex_color = explode(',',$data_temp_02->E);
		for($i=0;$i<count($ex_color);$i++){
			if(trim($ex_color[$i]) != "ALL"){
				$sql = "
					select count(*) r from {$this->MAuth->getdb('JD_SETCOLOR')}
					where MODELCOD='".$data_temp_02->C."' and COLORCOD='".trim($ex_color[$i])."'
				";
				//echo $sql; exit;
				$query = $this->db->query($sql);
				$data = $query->row();
				if($data->r == 0){
					$response = array("error"=>true,"errorMsg"=>"ผิดพลาด(E004) <br>ไม่พบข้อมูลรุ่น ".$data_temp_02->C." สี ".trim($ex_color[$i])." <br>โปรดตรวจสอบข้อมูลใหม่อีกครั้ง");
					echo json_encode($response); exit;
				}
			}
		}
		
		$ex_acti = explode(',',$data_temp_02->G);
		for($i=0;$i<count($ex_acti);$i++){
			if(trim($ex_acti[$i]) != "ALL"){
				$sql = "
					select count(*) r from {$this->MAuth->getdb('SETACTI')}
					where ACTICOD='".trim($ex_acti[$i])."'
				";
				$query = $this->db->query($sql);
				$data = $query->row();
				if($data->r == 0){
					$response = array("error"=>true,"errorMsg"=>"ผิดพลาด(E005) <br>ไม่พบข้อมูลกิจกรรมการขาย ".trim($ex_acti[$i])." <br>โปรดตรวจสอบข้อมูลใหม่อีกครั้ง");
					echo json_encode($response); exit;
				}
			}
		}
		
		$ex_locat = explode(',',$data_temp_02->J);
		for($i=0;$i<count($ex_locat);$i++){
			if(trim($ex_locat[$i]) != "ALL"){
				$sql = "
					select count(*) r from {$this->MAuth->getdb('INVLOCAT')}
					where LOCATCD='".trim($ex_locat[$i])."'
				";
				$query = $this->db->query($sql);
				$data = $query->row();
				if($data->r == 0){
					$response = array("error"=>true,"errorMsg"=>"ผิดพลาด(E006) <br>ไม่พบสาขา ".trim($ex_locat[$i])." <br>โปรดตรวจสอบข้อมูลใหม่อีกครั้ง");
					echo json_encode($response); exit;
				}
			}
		}
		
		#เช็คถ้าเป็นรถใหม่ ต้องมีช่วงราคารถ 1 ช่วงเท่านั้น
		$sql_temp_03 = "
			select F,count(*) as ROW from (
				select distinct A,B,C,D,E,F,G,H,I,J,K,L from (
					".$sql_origin."
				) as data 
			) as data 
			group by F
		";
		
		$query_temp_03 = $this->db->query($sql_temp_03);
		$data_temp_03 = $query_temp_03->row();
		if($data_temp_03->F == "N" and $data_temp_03->ROW > 1){
			$response = array("error"=>true,"errorMsg"=>"ผิดพลาด(E007) <br>ระบุ standard รถใหม่มา แต่มีราคามากกว่า 1 <br>โปรดตรวจสอบข้อมูลใหม่อีกครั้ง");
			echo json_encode($response); exit;
		}
		
		$sql_temp_03 = "
			select * from (
				select distinct A,B,C,D,E,F,G,H,I,J,K,L from (
					".$sql_origin."
				) as data 
			) as data 
			order by K
		";
		$query_temp_03 	= $this->db->query($sql_temp_03);
		
		$stdprice = "";
		if($query_temp_03->row()){
			foreach($query_temp_03->result() as $row){
				$stdprice .= "
					<tr>
						<td>".($row->K == "" ? "" : number_format($row->K,2))."</td>
						<td>".($row->L == "" ? "" : number_format($row->L,2))."</td>
						<td>
							<button class='btn_car_old_delete btn-danger' fprice='".$row->K."' tprice='".$row->L."'> ลบ</button>
						</td>
					</tr>
				";
			}
		}
		
		$sql_temp_04 	= "
			select * from (
				select distinct A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V from (
					".$sql_origin."
				) as data 
			) as data 
			order by cast(K as decimal),cast(M as decimal)
		";
		//echo $sql_temp_04; exit;
		$query_temp_04 	= $this->db->query($sql_temp_04);
		
		$stddown = "";
		if($query_temp_04->row()){
			$nrow = 1;
			foreach($query_temp_04->result() as $row){
				if($nrow++ == 1){
					$color = "style='color:blue;'";
					$formpriceFP = $row->K;
				}else if($formpriceFP != $row->K){
					$color = ($color == "style='color:blue;'" ? "":"style='color:blue;'");
					$formpriceFP = $row->K;
				}
				
				$stddown .= "
					<tr {$color}>
						<td>
							<button class='editDwn btn btn-xs btn-warning' formpricefp='".$row->K."' formpricetp='".($row->L == "" ? "9999999.99":$row->L)."' formdwns='".$row->M."' formdwne='".($row->N == "" ? "9999999.99":$row->N)."' forminterest='".$row->O."' forminterest2='".$row->P."' forminsurance='".$row->Q."' formtrans='".$row->R."' formregist='".$row->S."' formact='".$row->T."' formcoupon='".$row->U."' formapprv='".($row->V == "yes" ? "Y" : "N")."'><span class='glyphicon glyphicon-edit'> แก้ไข</span></button>
							<button class='deleteDwn btn btn-xs btn-danger' formpricefp='".$row->K."' formpricetp='".($row->L == "" ? "9999999.99":$row->L)."' formdwns='".$row->M."' formdwne='".($row->N == "" ? "9999999.99":$row->N)."' forminterest='".$row->O."' forminterest2='".$row->P."' forminsurance='".$row->Q."' formtrans='".$row->R."' formregist='".$row->S."' formact='".$row->T."' formcoupon='".$row->U."' formapprv='".($row->V == "yes" ? "Y" : "N")."'><span class='glyphicon glyphicon-trash'> ลบ</span></button>	
						</td>
						<td>".($row->K == "" ? "" : number_format($row->K,2))."-".($row->L == "" ? "ขึ้นไป" : number_format($row->L,2))."</td>
						<td>".($row->M == "" ? "" : number_format($row->M,2))."-".($row->N == "" ? "ขึ้นไป" : number_format($row->N,2))."</td>
						<td>".($row->O == "" ? "" : number_format($row->O,2))." ".($row->P == "" ? "" : "(".number_format($row->P,2).")")."</td>
						<td>".($row->Q == "" ? "" : number_format($row->Q,2))."</td>
						<td>".($row->R == "" ? "" : number_format($row->R,2))."</td>
						<td>".($row->S == "" ? "" : number_format($row->S,2))."</td>
						<td>".($row->T == "" ? "" : number_format($row->T,2))."</td>
						<td>".($row->U == "" ? "" : number_format($row->U,2))."</td>
						<td>".($row->V == "yes" ? "Y" : "N")."</td>
					</tr>
				";
			}
		}
		
		$sql_temp_05 	= "
			select * from (
				select distinct A,B,C,D,E,F,G,H,I,J,K,L,M,N,W,X,Y,Z,AA from (
					".$sql_origin."
				) as data 
			) as data 
			order by cast(K as decimal),cast(M as decimal),cast(X as decimal)	
		";
		//echo $sql_temp_05; exit;
		$query_temp_05 	= $this->db->query($sql_temp_05);
		
		$stdfree = "";
		if($query_temp_05->row()){
			$nrow = 1;
			foreach($query_temp_05->result() as $row){
				if($nrow++ == 1){
					$color = "style='color:blue;'";
					$formpriceFP = $row->K;
				}else if($formpriceFP != $row->K){
					$color = ($color == "style='color:blue;'" ? "":"style='color:blue;'");
					$formpriceFP = $row->K;
				}
				
				$stdfree .= "
					<tr {$color}>
						<td>
							<button class='editFree btn btn-xs btn-warning' formpricefp='".$row->K."' formpricetp='".($row->L == "" ? "9999999.99":$row->L)."' formdwns='".$row->M."' formdwne='".($row->N == "" ? "9999999.99":$row->N)."' formtypev='".$row->W."' formrate='".$row->Z."' formnopays='".$row->X."' formnopaye='".$row->Y."' formdetail='".$row->AA."'><span class='glyphicon glyphicon-edit'> แก้ไข</span></button>
							<button class='deleteFree btn btn-xs btn-danger' formpricefp='".$row->K."' formpricetp='".($row->L == "" ? "9999999.99":$row->L)."' formdwns='".$row->M."' formdwne='".($row->N == "" ? "9999999.99":$row->N)."' formtypev='".$row->W."' formrate='".$row->Z."' formnopays='".$row->X."' formnopaye='".$row->Y."' formdetail='".$row->AA."'><span class='glyphicon glyphicon-trash'> ลบ</span></button>	
						</td>
						<td>".($row->K == "" ? "" : number_format($row->K,2))."-".($row->L == "" ? "ขึ้นไป" : number_format($row->L,2))."</td>
						<td>".($row->M == "" ? "" : number_format($row->M,2))."-".($row->N == "" ? "ขึ้นไป" : number_format($row->N,2))."</td>
						<td>".($row->W == "C" ? "คนซื้อ" : ($row->W == "I" ? "คนค้ำ" : "ผู้แนะนำ"))."</td>
						<td>".($row->X == "" ? "" : number_format($row->X,2))."</td>
						<td>".($row->Y == "" ? "" : number_format($row->Y,2))."</td>
						<td>".($row->Z == "" ? "" : number_format($row->Z,2))."</td>
						<td>".($row->AA)."</td>
					</tr>
				";
			}
		}
		
		$sql = "
			select STDID from {$this->MAuth->getdb('STDVehicles')}
			where MODEL='".trim($data_temp_02->C)."'
		";
		$query = $this->db->query($sql);
		$data = $query->row();
		
		$response = array();
		$response["stdid"] 	= $data->STDID;
		$response["eventname"] 	= $data_temp_02->A;
		$response["detail"] = $data_temp_02->B;
		$response["model"] 	= $data_temp_02->C;
		$response["baab"] 	= $data_temp_02->D;
		$response["color"] 	= $data_temp_02->E;
		$response["stat"] 	= $data_temp_02->F;
		$response["acti"] 	= $data_temp_02->G;
		$response["events"] = $data_temp_02->H;
		$response["evente"] = $data_temp_02->I;
		$response["locat"] 	= $data_temp_02->J;
		$response["stdprice"] = $stdprice;
		$response["stddown"] = $stddown;
		$response["stdfree"] = $stdfree;
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
	
	function JDFormStdDWN(){
		$formPrice = "";
		// if($_POST["fstat"] == "O"){
			$price = $_POST["price"];
			if(is_array($price)){
				$price_all = sizeof($price);
				for($i=0;$i<$price_all;$i++){
					$value = $price[$i]["FPRICE"]."-".$price[$i]["TPRICE"];
					
					if($price[$i]["TPRICE"] == "9999999.99"){
						$text  = "ช่วงราคารถ :: ".number_format($price[$i]["FPRICE"])." บาท ขึ้นไป";
					}else{
						$text  = "ช่วงราคารถ :: ".number_format($price[$i]["FPRICE"])."-".number_format($price[$i]["TPRICE"])." บาท";						
					}
					
					$selected = "";
					if($price[$i]["FPRICE"] == $_POST["formpriceFP"] and $price[$i]["TPRICE"] == $_POST["formpriceTP"]){
						$selected = "selected";
					}
					
					$formPrice .= "<option value='".$value."' FPRICE='".$price[$i]["FPRICE"]."' TPRICE='".$price[$i]["TPRICE"]."' {$selected}>".$text." </option>";
				}
				
				$formPrice = "
					<div class='col-sm-12'>
						<div class='form-group'>
							ช่วงราคารถ
							<select id='formprice' class='form-control input-sm'>".$formPrice."</select>
						</div>
					</div>
				";
			}else{
				$html = "รถเก่า คุณยังไม่ได้ระบุช่วงราคารถ โปรดระบุช่วงราคารถก่อนครับ";
				$response = array("html"=>$html);
				echo json_encode($response); exit;
			}
		// }
		
		$html = $formPrice."
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
			<div class='col-sm-12'>
				<div class='form-group'>
					<div class='checkbox'>
						<label>
							<input type='checkbox' id='formapprv' value='' ".($_POST["formapprv"] == "Y" ? "checked":"").">
							ขั้นเงินดาวน์นี้ต้องแนบรายการขออนุมัติด้วย
						</label>
					</div>
				</div>
			</div>
			<div class='col-sm-10 col-sm-offset-1'>
				<button id='btnSWNADD' class='btn btn-warning btn-block'><span class='glyphicon glyphicon-".($_POST["formevent"] == "add"?"plus":"edit")."'> ".($_POST["formevent"] == "add"?"เพิ่ม":"แก้ไข")."</span></button>
			</div>
		";
		
		$response = array("html"=>$html);
		echo json_encode($response);
	}
	
	function JDFormStdFREE(){
		$formPrice = "";
		$price 	   = $_POST["price"];
		
		if(is_array($price)){
			$price_all = sizeof($price);
			for($i=0;$i<$price_all;$i++){
				$value = $price[$i]["FPRICE"]."-".$price[$i]["TPRICE"];
				
				if($price[$i]["TPRICE"] == "9999999.99"){
					$text  = "ช่วงราคารถ :: ".number_format($price[$i]["FPRICE"])." บาท ขึ้นไป";
				}else{
					$text  = "ช่วงราคารถ :: ".number_format($price[$i]["FPRICE"])."-".number_format($price[$i]["TPRICE"])." บาท";						
				}
				
				$selected = "";
				if($price[$i]["FPRICE"] == $_POST["formpriceFP"] and $price[$i]["TPRICE"] == $_POST["formpriceTP"]){
					$selected = "selected";
				}
				
				$formPrice .= "<option value='".$value."' FPRICE='".$price[$i]["FPRICE"]."' TPRICE='".$price[$i]["TPRICE"]."' {$selected}>".$text." </option>";
			}
			
			$formPrice = "
				<div class='col-sm-12'>
					<div class='form-group'>
						ช่วงราคารถ
						<select id='formprice' class='form-control input-sm'>".$formPrice."</select>
					</div>
				</div>
			";
		}else{
			$html = "คุณยังไม่ได้ระบุช่วงราคารถ โปรดระบุช่วงราคารถก่อนครับ";
			$response = array("html"=>$html);
			echo json_encode($response); exit;
		}
		
		$formDwn = "";
		$editDwn = $_POST["editDwn"];
		if(is_array($editDwn)){
			$price_all = sizeof($editDwn);
			$option 	= "";
			$groupopt 	= "";
			$formpriceFP = "";
			for($i=0;$i<$price_all;$i++){
				$value = $editDwn[$i]["formdwns"]."-".$editDwn[$i]["formdwne"];
				
				if($editDwn[$i]["formdwne"] == "9999999.99"){
					$text  = "ช่วงเงินดาวน์ :: ".number_format($editDwn[$i]["formdwns"])." บาท ขึ้นไป";
				}else{
					$text  = "ช่วงเงินดาวน์ :: ".number_format($editDwn[$i]["formdwns"])."-".number_format($editDwn[$i]["formdwne"])." บาท";						
				}
				
				$selected = "";
				if($editDwn[$i]["formdwns"] == $_POST["formdwns"] and $editDwn[$i]["formdwne"] == $_POST["formdwne"]){
					$selected = "selected";
				}
				
				$disabled = "disabled";
				if($i == 0 and $_POST["formpriceFP"] == ""){
					$disabled = "case='1'";
				}else if($i == 0 and $_POST["formpriceFP"] == $editDwn[$i]["formpriceFP"]){
					$disabled = "case='2'";
				}else if($editDwn[0]["formpriceFP"] == $editDwn[$i]["formpriceFP"] and $_POST["formpriceFP"] == ""){
					$disabled = "case='3'";
				}else if($_POST["formpriceFP"] == $editDwn[$i]["formpriceFP"]){
					$disabled = "case='4'";
				}
				
				if($i==0){
					$option .= "
						<option value='".$value."' {$disabled} formpriceFP='".$editDwn[$i]["formpriceFP"]."' formpriceTP='".$editDwn[$i]["formpriceTP"]."' formdwns='".$editDwn[$i]["formdwns"]."' formdwne='".$editDwn[$i]["formdwne"]."' {$selected}>
							".$text." 
						</option>
					";
					
					if($editDwn[$i]["formpriceTP"] == "9999999.99"){
						$gropt  = "ช่วงราคารถ :: ".number_format($editDwn[$i]["formpriceFP"])." บาท ขึ้นไป";
					}else{
						$gropt  = "ช่วงราคารถ :: ".number_format($editDwn[$i]["formpriceFP"])."-".number_format($editDwn[$i]["formpriceTP"])." บาท";						
					}
					
					$formpriceFP = $editDwn[$i]["formpriceFP"];
				}else if ($formpriceFP != $editDwn[$i]["formpriceFP"]){
					$groupopt .= "<optgroup label='".$gropt."'>{$option}</optgroup>";
					
					$option = "
						<option value='".$value."' {$disabled} formpriceFP='".$editDwn[$i]["formpriceFP"]."' formpriceTP='".$editDwn[$i]["formpriceTP"]."' formdwns='".$editDwn[$i]["formdwns"]."' formdwne='".$editDwn[$i]["formdwne"]."' {$selected}>
							".$text." 
						</option>
					";
					
					if($editDwn[$i]["formpriceTP"] == "9999999.99"){
						$gropt  = "ช่วงราคารถ :: ".number_format($editDwn[$i]["formpriceFP"])." บาท ขึ้นไป";
					}else{
						$gropt  = "ช่วงราคารถ :: ".number_format($editDwn[$i]["formpriceFP"])."-".number_format($editDwn[$i]["formpriceTP"])." บาท";						
					}
					
					$formpriceFP = $editDwn[$i]["formpriceFP"];
				}else{
					$option .= "
						<option value='".$value."' {$disabled} formpriceFP='".$editDwn[$i]["formpriceFP"]."' formpriceTP='".$editDwn[$i]["formpriceTP"]."' formdwns='".$editDwn[$i]["formdwns"]."' formdwne='".$editDwn[$i]["formdwne"]."' {$selected}>
							".$text." 
						</option>
					";

					if($editDwn[$i]["formpriceTP"] == "9999999.99"){
						$gropt  = "ช่วงราคารถ :: ".number_format($editDwn[$i]["formpriceFP"])." บาท ขึ้นไป";
					}else{
						$gropt  = "ช่วงราคารถ :: ".number_format($editDwn[$i]["formpriceFP"])."-".number_format($editDwn[$i]["formpriceTP"])." บาท";						
					}
					
					$formpriceFP = $editDwn[$i]["formpriceFP"];
				}
			}
			$groupopt .= "<optgroup label='".$gropt."'>{$option}</optgroup>";
			
			$formDwn = "
				<div class='col-sm-12'>
					<div class='form-group'>
						ช่วงเงินดาวน์
						<select id='formDwn' class='form-control input-sm'>".$groupopt."</select>
					</div>
				</div>
			";
		}else{
			$html = "คุณยังไม่ได้ระบุช่วงราคารถ โปรดระบุช่วงราคารถก่อนครับ";
			$response = array("html"=>$html);
			echo json_encode($response); exit;
		}
		
		$html = "
			<div id='main_form_free'>
				".$formPrice."
				".$formDwn."
				<div class='col-sm-6'>
					<div class='form-group'>
						ประเภท
						<select id='formtype' class='form-control input-sm'>
							<option value='C' ".($_POST["formtypeV"] == "C"?"selected":"").">คนซื้อ</option>
							<option value='I' ".($_POST["formtypeV"] == "I"?"selected":"").">คนค้ำ</option>
							<option value='S' ".($_POST["formtypeV"] == "S"?"selected":"").">ผู้แนะนำ</option>
						</select>
					</div>
				</div>
				<div class='col-sm-6'>
					<div class='form-group'>
						std ของแถม
						<input type='text' id='formrate' class='form-control input-sm jzAllowNumber' value='".$_POST["formrate"]."'>
					</div>
				</div>
				
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
				
				<div class='col-sm-12'>
					<div class='form-group'>
						หมายเหตุ
						<textarea id='formdetail' class='form-control input-sm'>".$_POST["formdetail"]."</textarea>
					</div>
				</div>
				<div class='col-sm-10 col-sm-offset-1'>
					<button id='btnSWNADD' class='btn btn-warning btn-block'><span class='glyphicon glyphicon-".($_POST["formevent"] == "add"?"plus":"edit")."'> ".($_POST["formevent"] == "add"?"เพิ่ม":"แก้ไข")."</span></button>
				</div>
			</div>
		";
		
		$response = array("html"=>$html);
		echo json_encode($response);
	}
	
	function getStdDWN(){
		$response = array("error"=>false,"msg"=>"");
		
		$formpriceFP 	= trim($_POST['formpriceFP']);
		$formpriceTP 	= trim($_POST['formpriceTP']);
		$formdwns 		= trim($_POST['formdwns']);
		$formdwne 		= trim($_POST['formdwne']);
		$forminterest 	= trim($_POST['forminterest']);
		$forminterest2 	= trim($_POST['forminterest2']);
		$forminsurance 	= trim($_POST['forminsurance']);
		$formtrans 		= trim($_POST['formtrans']);
		$formregist 	= trim($_POST['formregist']);
		$formact 		= trim($_POST['formact']);
		$formcoupon 	= trim($_POST['formcoupon']);
		$formapprv 		= trim($_POST['formapprv']);
		
		if($formdwne == ""){ $formdwne = '9999999.99'; }
		
		$editDwn 		= $_POST['editDwn'];
		//print_r($editDwn); exit;
		
		$sql = "
			if object_id('tempdb..#tempdbStdDWN') is not null drop table #tempdbStdDWN;
			create table #tempdbStdDWN (
				formpriceFP decimal(9,2),formpriceTP decimal(9,2),
				formdwns decimal(9,2),formdwne decimal(9,2),
				forminterest decimal(5,2),forminterest2 decimal(5,2),
				forminsurance decimal(9,2),formtrans decimal(9,2),
				formregist decimal(9,2),formact decimal(9,2),
				formcoupon decimal(9,2),formapprv varchar(1)
			);
		";
		#echo $sql; //sqlgetStdDWN
		$this->db->query($sql);
		
		if(is_array($editDwn)){
			$size = sizeof($editDwn);
			for($i=0;$i<$size;$i++){
				$sql = "
					insert into #tempdbStdDWN
					select '".$editDwn[$i]["formpriceFP"]."'
						,'".$editDwn[$i]["formpriceTP"]."'
						,'".$editDwn[$i]["formdwns"]."'
						,'".($editDwn[$i]["formdwne"])."'
						,'".$editDwn[$i]["forminterest"]."'
						,'".$editDwn[$i]["forminterest2"]."'
						,'".$editDwn[$i]["forminsurance"]."'
						,'".$editDwn[$i]["formtrans"]."'
						,'".$editDwn[$i]["formregist"]."'
						,'".$editDwn[$i]["formact"]."'
						,'".$editDwn[$i]["formcoupon"]."'
						,'".$editDwn[$i]["formapprv"]."'
				";
				#echo $sql; //sqlgetStdDWN
				$this->db->query($sql); 
			}
		}
		
		$sql = "
			if exists(
				select * from #tempdbStdDWN
				where formpriceFP='".$formpriceFP."' and formpriceTP='".$formpriceTP."'
					and '".$formdwns."' between formdwns and formdwne
				union
				select * from #tempdbStdDWN
				where formpriceFP='".$formpriceFP."' and formpriceTP='".$formpriceTP."'
					and '".$formdwne."' between formdwns and formdwne
			)
			begin
				delete #tempdbStdDWN;
	
				insert into #tempdbStdDWN
				select null,null,null,null,null,null,null,null,null,null,null,null
			end
			else 
			begin 			
				insert into #tempdbStdDWN
				select '".$formpriceFP."',".($formpriceTP == "" ? "null":"'".$formpriceTP."'")."
					,'".$formdwns."','".$formdwne."'
					,'".$forminterest."','".$forminterest2."'
					,'".$forminsurance."','".$formtrans."'
					,'".$formregist."','".$formact."'
					,'".$formcoupon."','".$formapprv."'
			end
		";
		#echo $sql; //sqlgetStdDWN
		$this->db->query($sql);
		
		$sql = "
			select * from #tempdbStdDWN
			order by formpriceFP ,formdwns 
		";
		#echo $sql; //sqlgetStdDWN
		$query = $this->db->query($sql);
		
		$html = "";
		$nrow = 1;
		if($query->row()){
			foreach($query->result() as $row){
				if($nrow++ == 1){
					$color = "style='color:blue;'";
					$formpriceFP = $row->formpriceFP;
				}else if($formpriceFP != $row->formpriceFP){
					$color = ($color == "style='color:blue;'" ? "":"style='color:blue;'");
					$formpriceFP = $row->formpriceFP;
				}
				
				if($row->formpriceFP != ""){
					$html .= "
						<tr {$color}>
							<td>
								<button class='editDwn btn btn-xs btn-warning'
									formpriceFP='".$row->formpriceFP."'
									formpriceTP='".$row->formpriceTP."'
									formdwns='".$row->formdwns."'
									formdwne='".$row->formdwne."'
									forminterest='".$row->forminterest."'
									forminterest2='".$row->forminterest2."'
									forminsurance='".$row->forminsurance."'
									formtrans='".$row->formtrans."'
									formregist='".$row->formregist."'
									formact='".$row->formact."'
									formcoupon='".$row->formcoupon."'
									formapprv='".$row->formapprv."'
									><span class='glyphicon glyphicon-edit'> แก้ไข</span></button>
								<button class='deleteDwn btn btn-xs btn-danger'
									formpriceFP='".$row->formpriceFP."'
									formpriceTP='".$row->formpriceTP."'
									formdwns='".$row->formdwns."'
									formdwne='".$row->formdwne."'
									forminterest='".$row->forminterest."'
									forminterest2='".$row->forminterest2."'
									forminsurance='".$row->forminsurance."'
									formtrans='".$row->formtrans."'
									formregist='".$row->formregist."'
									formact='".$row->formact."'
									formcoupon='".$row->formcoupon."'
									formapprv='".$row->formapprv."'
									><span class='glyphicon glyphicon-trash'> ลบ</span></button>	
							</td>
							<td>".(number_format($row->formpriceFP,2))."-".($row->formpriceTP == '9999999.99' ? "ขึ้นไป":number_format($row->formpriceTP,2))."</td>
							<td>".(number_format($row->formdwns,2))."-".($row->formdwne == '9999999.99' ? "ขึ้นไป":number_format($row->formdwne,2))."</td>
							<td>".(number_format($row->forminterest,2))." ".($row->forminterest2 == "" ? "" : "(".number_format($row->forminterest2,2).")")."</td>
							<td>".($row->forminsurance == "" ? "":number_format($row->forminsurance,2))."</td>
							<td>".($row->formtrans == "" ? "":number_format($row->formtrans,2))."</td>
							<td>".($row->formregist == "" ? "":number_format($row->formregist,2))."</td>
							<td>".($row->formact == "" ? "":number_format($row->formact,2))."</td>
							<td>".($row->formcoupon == "" ? "":number_format($row->formcoupon,2))."</td>
							<td>".($row->formapprv)."</td>
						</tr>
					";					
				}else{
					$response["error"] = true;
					$response["msg"] = "ช่วงเงินดาวน์ซ้ำซ้อน โปรดตรวจสอบใหม่อีกครั้ง";
					echo json_encode($response); exit;
				}				
			}
		}else{
			$response["error"] = true;
			$response["msg"] = "Error no record !!";
			echo json_encode($response); exit;
		}
		
		$response["html"] = $html;
		echo json_encode($response);
	}
	
	function getStdFree(){
		$response = array("error"=>false,"msg"=>"");
		
		$formpriceFP 	= trim($_POST['formpriceFP']);
		$formpriceTP 	= trim($_POST['formpriceTP']);
		$formdwns 		= trim($_POST['formdwns']);
		$formdwne 		= trim($_POST['formdwne']);
		$formtypeT 		= (isset($_POST['formtypeT']) ? trim($_POST['formtypeT']) : "");
		$formtypeV 		= trim($_POST['formtypeV']);
		$formrate 		= trim($_POST['formrate']);
		$formnopays 	= trim($_POST['formnopays']);
		$formnopaye 	= trim($_POST['formnopaye']);
		$formdetail 	= trim($_POST['formdetail']);
		$editFree 		= $_POST['editFree'];
		
		if($formnopays == "0"){ $formnopays = '1'; }
		if($formnopaye == ""){ $formnopaye = '99'; }
		
		$sql = "
			if object_id('tempdb..#tempdbStdFree') is not null drop table #tempdbStdFree;
			create table #tempdbStdFree (
				formpriceFP decimal(9,2),formpriceTP decimal(9,2),
				formdwns decimal(9,2),formdwne decimal(9,2),
				formtypeV varchar(1),formrate decimal(9,2),
				formnopays int,formnopaye int,formdetail varchar(max)
			);
		";
		// echo $sql;
		$this->db->query($sql);
		
		if(is_array($editFree)){
			$size = sizeof($editFree);
			for($i=0;$i<$size;$i++){
				$sql = "
					insert into #tempdbStdFree
					select '".$editFree[$i]["formpriceFP"]."'
						,'".$editFree[$i]["formpriceTP"]."'
						,'".$editFree[$i]["formdwns"]."'
						,'".$editFree[$i]["formdwne"]."'
						,'".$editFree[$i]["formtypeV"]."'
						,'".$editFree[$i]["formrate"]."'
						,'".$editFree[$i]["formnopays"]."'
						,'".$editFree[$i]["formnopaye"]."'
						,'".$editFree[$i]["formdetail"]."'						
				";
				// echo $sql;
				$this->db->query($sql);
			}
		}
		
		$sql = "
			if exists(
				select * from #tempdbStdFree
				where formpriceFP='".$formpriceFP."' and formpriceTP='".$formpriceTP."'
					and formdwns='".$formdwns."' and formdwne='".$formdwne."'
					and '".$formnopays."' between formnopays and formnopaye
					and formtypeV='".$formtypeV."'
					and 1=2
				union
				select * from #tempdbStdFree
				where formpriceFP='".$formpriceFP."' and formpriceTP='".$formpriceTP."'
					and formdwns='".$formdwns."' and formdwne='".$formdwne."'
					and '".$formnopaye."' between formnopays and formnopaye
					and formtypeV='".$formtypeV."'
					and 1=2
			)
			begin
				delete #tempdbStdFree;
	
				insert into #tempdbStdFree
				select null,null,null,null,null,null,null,null,null
			end
			else 
			begin 			
				insert into #tempdbStdFree
				select '".$formpriceFP."'
					,'".$formpriceTP."'
					,'".$formdwns."'
					,'".$formdwne."'
					,'".$formtypeV."'
					,'".$formrate."'
					,'".$formnopays."'
					,'".$formnopaye."'
					,'".$formdetail."'
			end
		";
		// echo $sql;
		$this->db->query($sql);
		
		$sql = "
			select distinct * from #tempdbStdFree
			order by formtypeV, formpriceFP ,formdwns ,formnopays 
		";
		// echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		$nrow = 1;
		if($query->row()){
			$color = "style='color:#aaa;'";
			$formpriceFP = "";
			foreach($query->result() as $row){
				if($row->formpriceFP != ""){
					if($nrow++ == 1){
						$color = "style='color:blue;'";
						$formpriceFP = $row->formpriceFP;
					}else if($formpriceFP != $row->formpriceFP){
						$color = ($color == "style='color:blue;'" ? "":"style='color:blue;'");
						$formpriceFP = $row->formpriceFP;
					}
					
					$html .= "
						<tr {$color}>
							<td>
								<button class='editFree btn btn-xs btn-warning'
									formpriceFP='".$row->formpriceFP."'
									formpriceTP='".$row->formpriceTP."'
									formdwns='".$row->formdwns."'
									formdwne='".$row->formdwne."'
									formtypeV='".$row->formtypeV."'
									formrate='".$row->formrate."'
									formnopays='".$row->formnopays."'
									formnopaye='".$row->formnopaye."'
									formdetail='".$row->formdetail."'
									><span class='glyphicon glyphicon-edit'> แก้ไข</span></button>
								<button class='deleteFree btn btn-xs btn-danger'
									formpriceFP='".$row->formpriceFP."'
									formpriceTP='".$row->formpriceTP."'
									formdwns='".$row->formdwns."'
									formdwne='".$row->formdwne."'
									formtypeV='".$row->formtypeV."'
									formrate='".$row->formrate."'
									formnopays='".$row->formnopays."'
									formnopaye='".$row->formnopaye."'
									formdetail='".$row->formdetail."'
									><span class='glyphicon glyphicon-trash'> ลบ</span></button>	
							</td>
							<td>".(number_format($row->formpriceFP,2))."-".($row->formpriceTP == '9999999.99' ? "ขึ้นไป":number_format($row->formpriceTP,2))."</td>
							<td>".(number_format($row->formdwns,2))."-".($row->formdwne == '9999999.99' ? "ขึ้นไป":number_format($row->formdwne,2))."</td>
							<td>".($row->formtypeV == "C" ? "คนซื้อ": ($row->formtypeV == "S"? "ผู้แนะนำ":"คนค้ำ"))."</td>
							<td>".($row->formnopays == "" ? "":number_format($row->formnopays,0))."</td>
							<td>".($row->formnopaye == "99" ? "ขึ้นไป":number_format($row->formnopaye,0))."</td>
							<td>".($row->formrate == "" ? "":number_format($row->formrate,2))."</td>
							<td>".($row->formdetail)."</td>
						</tr>
					";					
				}else{
					$response["error"] = true;
					$response["msg"] = "ช่วงเงินดาวน์ซ้ำซ้อน โปรดตรวจสอบใหม่อีกครั้ง";
					echo json_encode($response); exit;
				}				
			}
		}else{
			$response["error"] = true;
			$response["msg"] = "Error no record !!";
			echo json_encode($response); exit;
		}
		
		$response["html"] = $html;
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
		
		/*
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
		*/
		
		if(@$_POST["STDDWN"] == ""){
			$response["error"] = true;
			$response["msg"] = 'โปรดระบุ Standard การดาวน์รถก่อนครับ';
			echo json_encode($response); exit;
		}
		
		if(@$_POST["STDFREE"] == ""){
			$response["error"] = true;
			$response["msg"] = 'โปรดระบุ Standard ของแถมก่อนครับ';
			echo json_encode($response); exit;
		}
		
		return;
	}
	
	function SaveSTD(){
		$this->certificate_std();
		
		$STDID 		= ($_POST["STDID"]);
		$SUBID 		= ($_POST["SUBID"]);
		$EVENTS 	= $this->Convertdate(1,($_POST["EVENTS"]));
		$EVENTE 	= $this->Convertdate(1,($_POST["EVENTE"]));
		$EVENTNAME 	= ($_POST["EVENTNAME"]);
		$DETAIL 	= ($_POST["DETAIL"]);
		$ACTI 		= ($_POST["ACTI"] == "" ? array():$_POST["ACTI"]);
		$MODEL 		= ($_POST["MODEL"]);
		$BAAB 		= ($_POST["BAAB"] == "" ? array():$_POST["BAAB"]);
		$COLOR 		= ($_POST["COLOR"] == "" ? array():$_POST["COLOR"]);
		$STAT 		= ($_POST["STAT"]);
		$PRICE 		= ($_POST["PRICE"] == "" ? array():$_POST["PRICE"]);
		$LOCAT 	 	= ($_POST["LOCAT"] == "" ? array():$_POST["LOCAT"]);
		$STDDWN 	= ($_POST["STDDWN"] == "" ? array():$_POST["STDDWN"]);
		$STDFREE 	= ($_POST["STDFREE"] == "" ? array():$_POST["STDFREE"]);
		
		/* ACTI */
		$sql = "
			if object_id('tempdb..#tempACTI') is not null drop table #tempACTI;
			create table #tempACTI (ACTICOD varchar(20));
		";
		#echo $sql; //savestd
		$this->db->query($sql);
		
		$SIZE_ACTI = sizeof($ACTI);
		if($SIZE_ACTI > 0){
			for($i=0;$i<$SIZE_ACTI;$i++){
				$sql = " insert into #tempACTI  select '{$ACTI[$i]}'";
				#echo $sql; //savestd
				$this->db->query($sql);
			}			
		}else{
			$sql = "insert into #tempACTI  select 'ALL'";
			#echo $sql; //savestd
			$this->db->query($sql);
		}
		
		/* BAAB */
		$sql = "
			if object_id('tempdb..#tempBAAB') is not null drop table #tempBAAB;
			create table #tempBAAB (BAAB varchar(20));
		";
		#echo $sql; //savestd
		$this->db->query($sql);
		
		$SIZE_BAAB = sizeof($BAAB);
		if($SIZE_BAAB > 0){
			for($i=0;$i<$SIZE_BAAB;$i++){
				$sql = "insert into #tempBAAB  select '{$BAAB[$i]}'";
				#echo $sql; //savestd
				$this->db->query($sql);
			}			
		}else{
			$sql = "insert into #tempBAAB  select 'ALL'";
			#echo $sql; //savestd
			$this->db->query($sql);
		}
		
		/* COLOR */
		$sql = "
			if object_id('tempdb..#tempCOLOR') is not null drop table #tempCOLOR;
			create table #tempCOLOR (COLOR varchar(20));
		";
		#echo $sql; //savestd
		$this->db->query($sql);
		
		$SIZE_COLOR = sizeof($COLOR);
		if($SIZE_COLOR > 0){
			for($i=0;$i<$SIZE_COLOR;$i++){
				$sql = "insert into #tempCOLOR select '{$COLOR[$i]}'";
				#echo $sql; //savestd
				$this->db->query($sql);
			}			
		}else{
			$sql = "insert into #tempCOLOR  select 'ALL'";
			#echo $sql; //savestd
			$this->db->query($sql);
		}
		
		/* PRICE */
		$sql = "
			if object_id('tempdb..#tempPRICE') is not null drop table #tempPRICE;
			create table #tempPRICE (FPRICE decimal(18,2),TPRICE decimal(18,2));
		";
		#echo $sql; //savestd
		$this->db->query($sql);
		
		$SIZE_PRICE = sizeof($PRICE);
		if($SIZE_PRICE > 0){
			for($i=0;$i<$SIZE_PRICE;$i++){
				$sql = "insert into #tempPRICE select '{$PRICE[$i]["fprice"]}','{$PRICE[$i]["tprice"]}'";
				#echo $sql; //savestd
				$this->db->query($sql);
			}			
		}
		
		/* LOCAT */
		$sql = "
			if object_id('tempdb..#templocat') is not null drop table #templocat;
			create table #templocat (locat varchar(5));
		";
		#echo $sql; //savestd
		$this->db->query($sql);
		
		$SIZE_LOCAT = sizeof($LOCAT);
		if($SIZE_LOCAT > 0){
			for($i=0;$i<$SIZE_LOCAT;$i++){
				$sql = "insert into #templocat select '{$LOCAT[$i]}'";
				#echo $sql; //savestd
				$this->db->query($sql);
			}			
		}else{
			$sql = "insert into #templocat select 'ALL'";
			#echo $sql; //savestd
			$this->db->query($sql);
		}
		
		/* DOWN */
		$sql = "
			if object_id('tempdb..#tempDOWN') is not null drop table #tempDOWN;
			create table #tempDOWN (
				FPRICE decimal(18,2),
				TPRICE decimal(18,2),
				DOWNS  decimal(18,2),
				DOWNE  decimal(18,2),
				INTERESTRT decimal(5,2) not null,
				INTERESTRT_GVM decimal(5,2) null,
				INSURANCE decimal(7,2) null,
				TRANSFERS decimal(7,2) null,
				REGIST decimal(7,2) null,
				ACT decimal(7,2) null,
				COUPON decimal(7,2) null,
				APPROVE varchar(3) not null
			);
		";
		#echo $sql; //savestd
		$this->db->query($sql);
		
		$SIZE_STDDWN = sizeof($STDDWN);
		if($SIZE_STDDWN > 0){
			for($i=0;$i<$SIZE_STDDWN;$i++){
				$sql = "
					insert into #tempDOWN 
					select '{$STDDWN[$i]["formpriceFP"]}','{$STDDWN[$i]["formpriceTP"]}'
						,'{$STDDWN[$i]["formdwns"]}','{$STDDWN[$i]["formdwne"]}'
						,'{$STDDWN[$i]["forminterest"]}','{$STDDWN[$i]["forminterest2"]}'
						,'{$STDDWN[$i]["forminsurance"]}','{$STDDWN[$i]["formtrans"]}'
						,'{$STDDWN[$i]["formregist"]}','{$STDDWN[$i]["formact"]}'
						,'{$STDDWN[$i]["formcoupon"]}','{$STDDWN[$i]["formapprv"]}'
				";
				#echo $sql; //savestd
				$this->db->query($sql);
			}			
		}
		
		/* FREE */
		$sql = "
			if object_id('tempdb..#tempFREE') is not null drop table #tempFREE;
			create table #tempFREE (
				FPRICE decimal(18,2),
				TPRICE decimal(18,2),
				DOWNS decimal(18,2),
				DOWNE decimal(18,2),
				FORCUS varchar(5) not null,
				NOPAYS int not null,
				NOPAYE int null,
				RATE decimal(7,2) not null,
				MEMO1 varchar(5) null
			);
		";
		#echo $sql; //savestd
		$this->db->query($sql);
		
		$SIZE_STDDWN = sizeof($STDFREE);
		if($SIZE_STDDWN > 0){
			for($i=0;$i<$SIZE_STDDWN;$i++){
				$sql = "
					insert into #tempFREE 
					select '{$STDFREE[$i]["formpriceFP"]}','{$STDFREE[$i]["formpriceTP"]}'
						,'{$STDFREE[$i]["formdwns"]}','{$STDFREE[$i]["formdwne"]}'
						,'{$STDFREE[$i]["formtype"]}','{$STDFREE[$i]["formnopays"]}'
						,'{$STDFREE[$i]["formnopaye"]}','{$STDFREE[$i]["formrate"]}'
						,'{$STDFREE[$i]["formdetail"]}'
				";
				#echo $sql; //savestd
				$this->db->query($sql);
			}			
		}
		
		if($_POST["event"] == "add"){
			$this->fn_ins_std();
		}else{
			$this->fn_upd_std();
		}
	}
	
	function fn_ins_std(){
		$STDID 		= (@$_POST["STDID"]);
		$SUBID 		= (@$_POST["SUBID"]);
		$EVENTS 	= $this->Convertdate(1,(@$_POST["EVENTS"]));
		$EVENTE 	= $this->Convertdate(1,(@$_POST["EVENTE"]));
		$EVENTNAME 	= (@$_POST["EVENTNAME"]);
		$DETAIL 	= (@$_POST["DETAIL"]);
		$ACTI 		= (@$_POST["ACTI"] == "" ? array():@$_POST["ACTI"]);
		$MODEL 		= (@$_POST["MODEL"]);
		$BAAB 		= (@$_POST["BAAB"] == "" ? array():@$_POST["BAAB"]);
		$COLOR 		= (@$_POST["COLOR"] == "" ? array():@$_POST["COLOR"]);
		$STAT 		= (@$_POST["STAT"]);
		$PRICE 		= (@$_POST["PRICE"] == "" ? array():@$_POST["PRICE"]);
		$LOCAT 	 	= (@$_POST["LOCAT"] == "" ? array():@$_POST["LOCAT"]);
		$STDDWN 	= (@$_POST["STDDWN"] == "" ? array():@$_POST["STDDWN"]);
		$STDFREE 	= (@$_POST["STDFREE"] == "" ? array():@$_POST["STDFREE"]);
		
		$sql = "
			if object_id('tempdb..#tempResult') is not null drop table #tempResult;
			create table #tempResult (error varchar(1),msg varchar(max));
			
			declare @STDID varchar(30)		= '".$STDID."';
			declare @SUBID varchar(30)		= '".$SUBID."';
			declare @EVENTS datetime		= '".$EVENTS."';
			declare @EVENTE datetime		= ".($EVENTE == "" ? "null":"'".$EVENTE."'").";
			declare @EVENTNAME varchar(500) = '".$EVENTNAME."';
			declare @DETAIL varchar(max)	= '".$DETAIL."';

			declare @tbACTI table (ACTICOD varchar(20));
			insert into @tbACTI select * from #tempACTI
			declare @MODEL varchar(20) = '".$MODEL."';
			declare @tbBAAB table (BAAB varchar(20));
			insert into @tbBAAB select * from #tempBAAB
			declare @tbCOLOR table (COLOR varchar(20));
			insert into @tbCOLOR select * from #tempCOLOR
			declare @STAT varchar(1) = '".$STAT."';

			declare @tbPRICE table (FPRICE decimal(18,2),TPRICE decimal(18,2));
			insert into @tbPRICE select * from #tempPRICE
			
			declare @tbLOCAT table (LOCAT varchar(20));
			insert into @tbLOCAT select * from #templocat			
			
			declare @insby varchar(20) = '".$this->sess["IDNo"]."';
			declare @insdt varchar(20) = getdate();
			
			declare @tbDwn table (
				FPRICE decimal(18,2),
				TPRICE decimal(18,2),
				DOWNS  decimal(18,2),
				DOWNE  decimal(18,2),
				INTERESTRT decimal(5,2) not null,
				INTERESTRT_GVM decimal(5,2) null,
				INSURANCE decimal(7,2) null,
				TRANSFERS decimal(7,2) null,
				REGIST decimal(7,2) null,
				ACT decimal(7,2) null,
				COUPON decimal(7,2) null,
				APPROVE varchar(3)
			);
			insert into @tbDwn select * from #tempDOWN

			declare @tbFre table (
				FPRICE decimal(18,2),
				TPRICE decimal(18,2),
				DOWNS decimal(18,2),
				DOWNE decimal(18,2),
				FORCUS varchar(5) not null,
				NOPAYS int not null,
				NOPAYE int null,
				RATE decimal(7,2) not null,
				MEMO1 varchar(max) null
			);
			insert into @tbFre select * from #tempFREE
			
			begin tran tsins
			begin try
				if (@STDID = 'Auto Genarate')
				begin 
					set @STDID = isnull((select MAX(STDID)+1 from {$this->MAuth->getdb('STDVehicles')}),1);
					
					insert into {$this->MAuth->getdb('STDVehicles')}
					select @STDID,@MODEL,@insby,@insdt
				end
				
				if (@SUBID = 'Auto Genarate')
				begin
					set @SUBID = isnull((select MAX(SUBID)+1 from {$this->MAuth->getdb('STDVehiclesDetail')} where STDID=@STDID),1);
				end
				
				if not exists (select * from @tbACTI)
				begin
					rollback tran tsins;
					insert into #tempResult 
					select 'y','ผิดพลาด คุณยังไม่ได้ระบุกิจกรรมการขาย';
					return;
				end
				
				declare @hasACTI varchar(3) = isnull((
					select top 1 'YES' from {$this->MAuth->getdb('STDVehicles')} a
					left join {$this->MAuth->getdb('STDVehiclesDetail')} b on a.STDID=b.STDID
					left join {$this->MAuth->getdb('STDVehiclesACTI')} c on b.STDID=c.STDID and b.SUBID=c.SUBID
					where a.STDID=@STDID and (
						EVENTStart between @EVENTS and isnull(@EVENTE,EVENTStart)
						or 
						EVENTEnd between @EVENTS and isnull(@EVENTE,EVENTEnd)
					) and c.ACTICOD collate thai_cs_as in (select * from @tbACTI)
					and b.STAT=@STAT and b.SUBID <> @subid
				),'NO');
				
				declare @hasBAAB varchar(3) = isnull((
					select top 1 'YES' from {$this->MAuth->getdb('STDVehicles')} a
					left join {$this->MAuth->getdb('STDVehiclesDetail')} b on a.STDID=b.STDID
					left join {$this->MAuth->getdb('STDVehiclesBAAB')} c on b.STDID=c.STDID and b.SUBID=c.SUBID
					where a.STDID=@STDID and (
						EVENTStart between @EVENTS and isnull(@EVENTE,EVENTStart)
						or 
						EVENTEnd between @EVENTS and isnull(@EVENTE,EVENTEnd)
					) and c.BAAB collate thai_cs_as in (select * from @tbBAAB)
					and b.STAT=@STAT and b.SUBID <> @subid
				),'NO');
				
				declare @hasCOLOR varchar(3) = isnull((
					select top 1 'YES' from {$this->MAuth->getdb('STDVehicles')} a
					left join {$this->MAuth->getdb('STDVehiclesDetail')} b on a.STDID=b.STDID
					left join {$this->MAuth->getdb('STDVehiclesCOLOR')} c on b.STDID=c.STDID and b.SUBID=c.SUBID
					where a.STDID=@STDID and (
						EVENTStart between @EVENTS and isnull(@EVENTE,EVENTStart)
						or 
						EVENTEnd between @EVENTS and isnull(@EVENTE,EVENTEnd)
					) and c.COLOR collate thai_cs_as in (select * from @tbCOLOR)
					and b.STAT=@STAT and b.SUBID <> @subid
				),'NO');
				
				declare @hasLOCAT varchar(3) = isnull((
					select top 1 'YES' from {$this->MAuth->getdb('STDVehicles')} a
					left join {$this->MAuth->getdb('STDVehiclesDetail')} b on a.STDID=b.STDID
					left join {$this->MAuth->getdb('STDVehiclesLOCAT')} c on b.STDID=c.STDID and b.SUBID=c.SUBID
					where a.STDID=@STDID and (
						EVENTStart between @EVENTS and isnull(@EVENTE,EVENTStart)
						or 
						EVENTEnd between @EVENTS and isnull(@EVENTE,EVENTEnd)
					) and c.LOCAT collate thai_cs_as in (select * from @tbLOCAT)
					and b.STAT=@STAT and b.SUBID <> @subid
				),'NO');
				
				if ((@hasACTI = 'YES') and (@hasBAAB = 'YES') and (@hasCOLOR = 'YES') and (@hasLOCAT = 'YES'))
				begin
					rollback tran tsins;
					insert into #tempResult 
					select 'y','ผิดพลาด เนื่องจากกิจกรรมการขาย รุ่นรถ แบบ สี <br>สถานะภาพรถ และสาขาที่กำหนดใช้ std. <br>ในช่วงวันที่ ".@$_POST["EVENTS"]." ถึง ".@$_POST["EVENTE"]."<br>มีข้อมูลอยู่แล้ว';
					return;
				end	
				else if (isdate(@EVENTS) = 0 or isdate(isnull(@EVENTE,getdate())) = 0)
				begin
					rollback tran tsins;
					insert into #tempResult 
					select 'y','ผิดพลาด วันที่ไม่ถูกต้อง';
					return;
				end					
				else 
				begin
					if exists(
						select * from {$this->MAuth->getdb('STDVehiclesDetail')}
						where STDID=@STDID and SUBID=@SUBID
					)
					begin
						rollback tran tsins;
						insert into #tempResult 
						select 'y','ผิดพลาด มี standard อยู่แล้ว';
						return;
					end
					else
					begin
						insert into {$this->MAuth->getdb('STDVehiclesDetail')} (STDID,SUBID,STDNAME,STDDESC,STAT,EVENTStart,EVENTEnd)
						select @STDID,@SUBID,@EVENTNAME,@DETAIL,@STAT,@EVENTS,@EVENTE
						
						insert into {$this->MAuth->getdb('STDVehiclesACTI')} (STDID,SUBID,ACTICOD,ACTIVE,INSBY,INSDT)
						select @STDID,@SUBID,ACTICOD,'yes',@insby,@insdt from @tbACTI
						
						insert into {$this->MAuth->getdb('STDVehiclesBAAB')} (STDID,SUBID,BAAB,ACTIVE,INSBY,INSDT)
						select @STDID,@SUBID,BAAB,'yes',@insby,@insdt from @tbBAAB
						
						insert into {$this->MAuth->getdb('STDVehiclesCOLOR')} (STDID,SUBID,COLOR,ACTIVE,INSBY,INSDT)
						select @STDID,@SUBID,COLOR,'yes',@insby,@insdt from @tbCOLOR
						
						insert into {$this->MAuth->getdb('STDVehiclesPRICE')} (STDID,SUBID,PRICE,PRICES,ACTIVE,INSBY,INSDT)
						select @STDID,@SUBID,FPRICE,TPRICE,'yes',@insby,@insdt from @tbPRICE
						
						insert into {$this->MAuth->getdb('STDVehiclesLOCAT')} (STDID,SUBID,LOCAT,ACTIVE,INSBY,INSDT)
						select @STDID,@SUBID,LOCAT,'yes',@insby,@insdt from @tbLOCAT
						
						insert into {$this->MAuth->getdb('STDVehiclesDOWN')} (
							STDID,SUBID,PRICES,PRICEE,DOWNS,DOWNE
							,INTERESTRT,INTERESTRT_GVM,INSURANCE,TRANSFERS
							,REGIST,ACT,COUPON,APPROVE,ACTIVE,INSBY,INSDT
						) 
						select @STDID,@SUBID,FPRICE,TPRICE,DOWNS,DOWNE
							,INTERESTRT,INTERESTRT_GVM,INSURANCE,TRANSFERS
							,REGIST,ACT,COUPON,APPROVE,'yes',@insby,@insdt
						from @tbDwn
						
						insert into {$this->MAuth->getdb('STDVehiclesPackages')} (
							STDID,SUBID,PRICES,PRICEE,DOWNS,DOWNE,FORCUS,NOPAYS
							,NOPAYE,RATE,MEMO1,ACTIVE,INSBY,INSDT
						) 
						select @STDID,@SUBID,FPRICE,TPRICE,DOWNS,DOWNE,FORCUS,NOPAYS
							,NOPAYE,RATE,MEMO1,'yes',@insby,@insdt
						from @tbFre		
					end
				end
				
				declare @stdlog varchar(100) = 'STDID::'+cast(@STDID as varchar)+'_SUBID::'+cast(@SUBID as varchar)+'_';
				insert into {$this->MAuth->getdb('hp_UserOperationLog')} (userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
				values ('".$this->sess["IDNo"]."','SYS04::กำหนด standard รถ',@stdlog+' ".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');

				insert into #tempResult select 'n','บันทึกข้อมูลเรียบร้อยแล้ว';
				commit tran tsins;
			end try
			begin catch
				rollback tran tsins;
				insert into #tempResult select 'y',ERROR_MESSAGE();
			end catch
		";			
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
	
	function fn_upd_std(){
		$STDID 		= (@$_POST["STDID"]);
		$SUBID 		= (@$_POST["SUBID"]);
		$EVENTS 	= $this->Convertdate(1,(@$_POST["EVENTS"]));
		$EVENTE 	= $this->Convertdate(1,(@$_POST["EVENTE"]));
		$EVENTNAME 	= (@$_POST["EVENTNAME"]);
		$DETAIL 	= (@$_POST["DETAIL"]);
		$ACTI 		= (@$_POST["ACTI"] == "" ? array():@$_POST["ACTI"]);
		$MODEL 		= (@$_POST["MODEL"]);
		$BAAB 		= (@$_POST["BAAB"] == "" ? array():@$_POST["BAAB"]);
		$COLOR 		= (@$_POST["COLOR"] == "" ? array():@$_POST["COLOR"]);
		$STAT 		= (@$_POST["STAT"]);
		$PRICE 		= (@$_POST["PRICE"] == "" ? array():@$_POST["PRICE"]);
		$LOCAT 	 	= (@$_POST["LOCAT"] == "" ? array():@$_POST["LOCAT"]);
		$STDDWN 	= (@$_POST["STDDWN"] == "" ? array():@$_POST["STDDWN"]);
		$STDFREE 	= (@$_POST["STDFREE"] == "" ? array():@$_POST["STDFREE"]);
		
		$sql = "
			if object_id('tempdb..#tempResult') is not null drop table #tempResult;
			create table #tempResult (error varchar(1),msg varchar(max));
			
			begin tran tsup
			begin try
				declare @STDID varchar(30)		= '".$STDID."';
				declare @SUBID varchar(30)		= '".$SUBID."';
				declare @EVENTS datetime		= '".$EVENTS."';
				declare @EVENTE datetime		= ".($EVENTE == "" ? "null":"'".$EVENTE."'").";
				declare @EVENTNAME varchar(500) = '".$EVENTNAME."';
				declare @DETAIL varchar(max)	= '".$DETAIL."';

				declare @tbACTI table (ACTICOD varchar(20));
				insert into @tbACTI select * from #tempACTI
				declare @MODEL varchar(20) = '".$MODEL."';
				declare @tbBAAB table (BAAB varchar(20));
				insert into @tbBAAB select * from #tempBAAB
				declare @tbCOLOR table (COLOR varchar(20));
				insert into @tbCOLOR select * from #tempCOLOR
				declare @STAT varchar(1) = '".$STAT."';

				declare @tbPRICE table (FPRICE decimal(18,2),TPRICE decimal(18,2));
				insert into @tbPRICE select * from #tempPRICE
				
				declare @tbLOCAT table (LOCAT varchar(20));
				insert into @tbLOCAT select * from #templocat			
				
				declare @updby varchar(20) = '".$this->sess["IDNo"]."';
				declare @upddt varchar(20) = getdate();
				
				declare @tbDwn table (
					FPRICE decimal(18,2),
					TPRICE decimal(18,2),
					DOWNS  decimal(18,2),
					DOWNE  decimal(18,2),
					INTERESTRT decimal(5,2) not null,
					INTERESTRT_GVM decimal(5,2) null,
					INSURANCE decimal(7,2) null,
					TRANSFERS decimal(7,2) null,
					REGIST decimal(7,2) null,
					ACT decimal(7,2) null,
					COUPON decimal(7,2) null,
					APPROVE varchar(3)
				);
				insert into @tbDwn select * from #tempDOWN

				declare @tbFre table (
					FPRICE decimal(18,2),
					TPRICE decimal(18,2),
					DOWNS decimal(18,2),
					DOWNE decimal(18,2),
					FORCUS varchar(5) not null,
					NOPAYS int not null,
					NOPAYE int null,
					RATE decimal(7,2) not null,
					MEMO1 varchar(max) null
				);
				insert into @tbFre select * from #tempFREE
				
				if not exists (select * from @tbACTI where ACTICOD <> 'ALL')
				begin
					rollback tran tsup;
					insert into #tempResult 
					select 'y','ผิดพลาด คุณยังไม่ได้ระบุกิจกรรมการขาย';
					return;
				end				
				
				declare @hasACTI varchar(3) = isnull((
					select top 1 'YES' from {$this->MAuth->getdb('STDVehicles')} a
					left join {$this->MAuth->getdb('STDVehiclesDetail')} b on a.STDID=b.STDID
					left join {$this->MAuth->getdb('STDVehiclesACTI')} c on b.STDID=c.STDID and b.SUBID=c.SUBID
					where a.STDID=@STDID and (
						EVENTStart between @EVENTS and isnull(@EVENTE,EVENTStart)
						or 
						EVENTEnd between @EVENTS and isnull(@EVENTE,EVENTEnd)
					) and c.ACTICOD collate thai_cs_as in (select * from @tbACTI)
					and b.STAT=@STAT and b.SUBID <> @subid
				),'NO');
				
				declare @hasBAAB varchar(3) = isnull((
					select top 1 'YES' from {$this->MAuth->getdb('STDVehicles')} a
					left join {$this->MAuth->getdb('STDVehiclesDetail')} b on a.STDID=b.STDID
					left join {$this->MAuth->getdb('STDVehiclesBAAB')} c on b.STDID=c.STDID and b.SUBID=c.SUBID
					where a.STDID=@STDID and (
						EVENTStart between @EVENTS and isnull(@EVENTE,EVENTStart)
						or 
						EVENTEnd between @EVENTS and isnull(@EVENTE,EVENTEnd)
					) and c.BAAB collate thai_cs_as in (select * from @tbBAAB)
					and b.STAT=@STAT and b.SUBID <> @subid
				),'NO');
				
				declare @hasCOLOR varchar(3) = isnull((
					select top 1 'YES' from {$this->MAuth->getdb('STDVehicles')} a
					left join {$this->MAuth->getdb('STDVehiclesDetail')} b on a.STDID=b.STDID
					left join {$this->MAuth->getdb('STDVehiclesCOLOR')} c on b.STDID=c.STDID and b.SUBID=c.SUBID
					where a.STDID=@STDID and (
						EVENTStart between @EVENTS and isnull(@EVENTE,EVENTStart)
						or 
						EVENTEnd between @EVENTS and isnull(@EVENTE,EVENTEnd)
					) and c.COLOR collate thai_cs_as in (select * from @tbCOLOR)
					and b.STAT=@STAT and b.SUBID <> @subid
				),'NO');
				
				declare @hasLOCAT varchar(3) = isnull((
					select top 1 'YES' from {$this->MAuth->getdb('STDVehicles')} a
					left join {$this->MAuth->getdb('STDVehiclesDetail')} b on a.STDID=b.STDID
					left join {$this->MAuth->getdb('STDVehiclesLOCAT')} c on b.STDID=c.STDID and b.SUBID=c.SUBID
					where a.STDID=@STDID and (
						EVENTStart between @EVENTS and isnull(@EVENTE,EVENTStart)
						or 
						EVENTEnd between @EVENTS and isnull(@EVENTE,EVENTEnd)
					) and c.LOCAT collate thai_cs_as in (select * from @tbLOCAT)
					and b.STAT=@STAT and b.SUBID <> @subid
				),'NO');
				
				if ((@hasACTI = 'YES') and (@hasBAAB = 'YES') and (@hasCOLOR = 'YES') and (@hasLOCAT = 'YES'))
				begin
					rollback tran tsup;
					insert into #tempResult 
					select 'y','ผิดพลาด เนื่องจากกิจกรรมการขาย รุ่นรถ แบบ สี <br>สถานะภาพรถ และสาขาที่กำหนดใช้ std. <br>ในช่วงวันที่ ".@$_POST["EVENTS"]." ถึง ".@$_POST["EVENTE"]."<br>มีข้อมูลอยู่แล้ว';
					return;					
				end	
				else if (isdate(@EVENTS) = 0 or isdate(isnull(@EVENTE,getdate())) = 0)
				begin
					rollback tran tsup;
					insert into #tempResult 
					select 'y','ผิดพลาด วันที่ไม่ถูกต้อง';
					return;
				end					
				else 
				begin
					-- แก้ไขข้อมูลทั่วไป
					if exists(
						select * from {$this->MAuth->getdb('STDVehiclesDetail')}
						where STDID=@STDID and SUBID=@SUBID and STAT=@STAT
					)
					begin
						declare @eve datetime = (
							select convert(varchar(8),EVENTEnd,112) from {$this->MAuth->getdb('STDVehiclesDetail')}
							where STDID=@STDID and SUBID=@SUBID and STAT=@STAT 
						);
						
						if(@eve is null or @eve >= @EVENTE or @EVENTE is null)
						begin
							update {$this->MAuth->getdb('STDVehiclesDetail')}
							set EVENTStart=@EVENTS
								,EVENTEnd=@EVENTE
								,STDNAME=@EVENTNAME
								,STDDESC=@DETAIL
							where STDID=@STDID and SUBID=@SUBID and STAT=@STAT				
						end
						else 
						begin
							rollback tran tsup;
							insert into #tempResult 
							select 'y','ผิดพลาด วันที่บังคับใช้งาน ต้องไม่เกินวันปัจจุบันครับ';
							return;
						end
					end
					
					-- แก้ไขกิจกรรมการขาย
					if exists(
						select * from {$this->MAuth->getdb('STDVehiclesACTI')}
						where STDID=@STDID and SUBID=@SUBID and ACTIVE='yes' and ACTICOD collate thai_cs_as not in (select ACTICOD from @tbACTI)
					)
					begin 
						--  กรณ๊ข้อมูลใหม่ ถูกลบออก
						update {$this->MAuth->getdb('STDVehiclesACTI')}
						set ACTIVE='no'
							,UPDBY=@updby
							,UPDDT=@upddt
						where STDID=@STDID and SUBID=@SUBID and ACTICOD collate thai_cs_as not in (select ACTICOD from @tbACTI)
					end 
					else if exists(
						select ACTICOD from @tbACTI
						where ACTICOD collate thai_cs_as not in (
							select ACTICOD from {$this->MAuth->getdb('STDVehiclesACTI')}
							where STDID=@STDID and SUBID=@SUBID and ACTIVE='yes' 
						)
					)
					begin
						insert into {$this->MAuth->getdb('STDVehiclesACTI')} (STDID,SUBID,ACTICOD,ACTIVE,INSBY,INSDT)
						select @STDID,@SUBID,ACTICOD,'yes',@updby,@upddt from @tbACTI					
						where ACTICOD collate thai_cs_as not in (
							select ACTICOD from {$this->MAuth->getdb('STDVehiclesACTI')}
							where STDID=@STDID and SUBID=@SUBID and ACTIVE='yes' 
						)
					end
					else if ((select count(*) from @tbACTI) = 0)
					begin
						insert into {$this->MAuth->getdb('STDVehiclesACTI')} (STDID,SUBID,ACTICOD,ACTIVE,INSBY,INSDT)
						select @STDID,@SUBID,'ALL','yes',@updby,@upddt
					end
					
					-- แก้ไขแบบ
					if exists(
						select * from {$this->MAuth->getdb('STDVehiclesBAAB')}
						where STDID=@STDID and SUBID=@SUBID and ACTIVE='yes' and BAAB collate thai_cs_as not in (select BAAB from @tbBAAB)
					)
					begin 
						--  กรณ๊ข้อมูลใหม่ ถูกลบออก
						update {$this->MAuth->getdb('STDVehiclesBAAB')}
						set ACTIVE='no'
							,UPDBY=@updby
							,UPDDT=@upddt
						where STDID=@STDID and SUBID=@SUBID and BAAB collate thai_cs_as not in (select BAAB from @tbBAAB)
					end 
					else if exists(
						select BAAB from @tbBAAB
						where BAAB collate thai_cs_as not in (
							select BAAB from {$this->MAuth->getdb('STDVehiclesBAAB')}
							where STDID=@STDID and SUBID=@SUBID and ACTIVE='yes' 
						)
					)
					begin
						insert into {$this->MAuth->getdb('STDVehiclesBAAB')} (STDID,SUBID,BAAB,ACTIVE,INSBY,INSDT)
						select @STDID,@SUBID,BAAB,'yes',@updby,@upddt from @tbBAAB					
						where BAAB collate thai_cs_as not in (
							select BAAB from {$this->MAuth->getdb('STDVehiclesBAAB')}
							where STDID=@STDID and SUBID=@SUBID and ACTIVE='yes' 
						)
					end
					else if ((select count(*) from @tbBAAB) = 0)
					begin
						insert into {$this->MAuth->getdb('STDVehiclesBAAB')} (STDID,SUBID,BAAB,ACTIVE,INSBY,INSDT)
						select @STDID,@SUBID,'ALL','yes',@updby,@upddt
					end
					
					-- แก้ไขสี
					if exists(
						select * from {$this->MAuth->getdb('STDVehiclesCOLOR')}
						where STDID=@STDID and SUBID=@SUBID and ACTIVE='yes' and COLOR collate thai_cs_as not in (select COLOR from @tbCOLOR)
					)
					begin 
						--  กรณ๊ข้อมูลใหม่ ถูกลบออก
						update {$this->MAuth->getdb('STDVehiclesCOLOR')}
						set ACTIVE='no'
							,UPDBY=@updby
							,UPDDT=@upddt
						where STDID=@STDID and SUBID=@SUBID and COLOR collate thai_cs_as not in (select COLOR from @tbCOLOR)
					end 
					else if exists(
						select COLOR from @tbCOLOR
						where COLOR collate thai_cs_as not in (
							select COLOR from {$this->MAuth->getdb('STDVehiclesCOLOR')}
							where STDID=@STDID and SUBID=@SUBID and ACTIVE='yes' 
						)
					)
					begin
						insert into {$this->MAuth->getdb('STDVehiclesCOLOR')} (STDID,SUBID,COLOR,ACTIVE,INSBY,INSDT)
						select @STDID,@SUBID,COLOR,'yes',@updby,@upddt from @tbCOLOR					
						where COLOR collate thai_cs_as not in (
							select COLOR from {$this->MAuth->getdb('STDVehiclesCOLOR')}
							where STDID=@STDID and SUBID=@SUBID and ACTIVE='yes' 
						)
					end
					else if ((select count(*) from @tbCOLOR) = 0)
					begin
						insert into {$this->MAuth->getdb('STDVehiclesCOLOR')} (STDID,SUBID,COLOR,ACTIVE,INSBY,INSDT)
						select @STDID,@SUBID,'ALL','yes',@updby,@upddt 
					end
					
					-- แก้ไขสาขา
					if exists(
						select * from {$this->MAuth->getdb('STDVehiclesLOCAT')}
						where STDID=@STDID and SUBID=@SUBID and ACTIVE='yes' and LOCAT collate thai_cs_as not in (select LOCAT from @tbLOCAT)
					)
					begin 
						-- กรณ๊ข้อมูลใหม่ ถูกลบออก
						update {$this->MAuth->getdb('STDVehiclesLOCAT')}
						set ACTIVE='no'
							,UPDBY=@updby
							,UPDDT=@upddt
						where STDID=@STDID and SUBID=@SUBID and LOCAT collate thai_cs_as not in (select LOCAT from @tbLOCAT)
					end 
					else if exists(
						select LOCAT from @tbLOCAT
						where LOCAT collate thai_cs_as not in (
							select LOCAT from {$this->MAuth->getdb('STDVehiclesLOCAT')}
							where STDID=@STDID and SUBID=@SUBID and ACTIVE='yes' 
						)
					)
					begin
						insert into {$this->MAuth->getdb('STDVehiclesLOCAT')} (STDID,SUBID,LOCAT,ACTIVE,INSBY,INSDT)
						select @STDID,@SUBID,LOCAT,'yes',@updby,@upddt from @tbLOCAT					
						where LOCAT collate thai_cs_as not in (
							select LOCAT from {$this->MAuth->getdb('STDVehiclesLOCAT')}
							where STDID=@STDID and SUBID=@SUBID and ACTIVE='yes' 
						)
					end
					else if ((select count(*) from @tbLOCAT) = 0)
					begin
						insert into {$this->MAuth->getdb('STDVehiclesLOCAT')} (STDID,SUBID,LOCAT,ACTIVE,INSBY,INSDT)
						select @STDID,@SUBID,'ALL','yes',@updby,@upddt
					end

					/* ราคา */
					declare @npc int =(select COUNT(*) from @tbPRICE);					
					declare @opc int =(
						select COUNT(*) from {$this->MAuth->getdb('STDVehiclesPRICE')} a
						where a.STDID=@STDID and a.SUBID=@SUBID and a.ACTIVE='yes'
					);
					declare @upc int =(	
						select COUNT(*) from {$this->MAuth->getdb('STDVehiclesPRICE')} a
						inner join @tbPRICE b on a.PRICE=b.FPRICE and isnull(a.PRICES,9999999.99)=isnull(b.TPRICE,9999999.99)
						where a.STDID=@STDID and a.SUBID=@SUBID and a.ACTIVE='yes'
					);
					
					if (@opc!=@npc or @opc!=@upc)
					begin
						update {$this->MAuth->getdb('STDVehiclesPRICE')}
						set ACTIVE='no'
							,UPDBY=@updby
							,UPDDT=@upddt
						where STDID=@STDID and SUBID=@SUBID and ACTIVE='yes'
						
						insert into {$this->MAuth->getdb('STDVehiclesPRICE')} (STDID,SUBID,PRICE,PRICES,ACTIVE,INSBY,INSDT)
						select @STDID,@SUBID,FPRICE,TPRICE,'yes',@updby,@upddt from @tbPRICE
					end
					
					/* Down */
					set @npc =(select COUNT(*) from @tbDwn);
					set @opc =(
						select COUNT(*) from {$this->MAuth->getdb('STDVehiclesDOWN')} a
						where a.STDID=@STDID and a.SUBID=@SUBID and a.ACTIVE='yes'
					);
					set @upc =(	
						select COUNT(*) from {$this->MAuth->getdb('STDVehiclesDOWN')} a
						inner join @tbDwn b on a.PRICES=b.FPRICE 
							and isnull(a.PRICEE,9999999.99)=isnull(b.TPRICE,9999999.99)
							and a.DOWNS=b.DOWNS
							and isnull(a.DOWNE,9999999.99)=isnull(b.DOWNE,9999999.99)
							and a.INTERESTRT=b.INTERESTRT
							and a.INTERESTRT_GVM=b.INTERESTRT_GVM
							and isnull(a.TRANSFERS,0)=isnull(b.TRANSFERS,0)
							and isnull(a.REGIST,0)=isnull(b.REGIST,0)
							and isnull(a.ACT,0)=isnull(b.ACT,0)
							and isnull(a.COUPON,0)=isnull(b.COUPON,0)
							and a.APPROVE=b.APPROVE collate thai_cs_as
						where a.STDID=@STDID and a.SUBID=@SUBID and a.ACTIVE='yes'
					);
					if (@opc!=@npc or @opc!=@upc)
					begin
						update {$this->MAuth->getdb('STDVehiclesDOWN')}
						set ACTIVE='no'
							,UPDBY=@updby
							,UPDDT=@upddt
						where STDID=@STDID and SUBID=@SUBID and ACTIVE='yes'
						
						insert into {$this->MAuth->getdb('STDVehiclesDOWN')} (
							STDID,SUBID,PRICES,PRICEE,DOWNS,DOWNE
							,INTERESTRT,INTERESTRT_GVM,INSURANCE,TRANSFERS
							,REGIST,ACT,COUPON,APPROVE,ACTIVE,INSBY,INSDT
						) 
						select @STDID,@SUBID,FPRICE,TPRICE,DOWNS,DOWNE
							,INTERESTRT,INTERESTRT_GVM,INSURANCE,TRANSFERS
							,REGIST,ACT,COUPON,APPROVE,'yes',@updby,@upddt
						from @tbDwn
					end
					
					/* free */
					set @npc =(select COUNT(*) from @tbFre);
					set @opc =(
						select COUNT(*) from {$this->MAuth->getdb('STDVehiclesPackages')} a
						where a.STDID=@STDID and a.SUBID=@SUBID and a.ACTIVE='yes'
					);
					set @upc =(	
						select COUNT(*) from {$this->MAuth->getdb('STDVehiclesPackages')} a
						inner join @tbFre b on a.PRICES=b.FPRICE 
							and isnull(a.PRICEE,9999999.99)=isnull(b.TPRICE,9999999.99)
							and a.DOWNS=b.DOWNS
							and isnull(a.DOWNE,9999999.99)=isnull(b.DOWNE,9999999.99)
							and a.NOPAYS=b.NOPAYS
							and a.NOPAYE=b.NOPAYE
							and a.FORCUS=b.FORCUS collate thai_cs_as
						where a.STDID=@STDID and a.SUBID=@SUBID and a.ACTIVE='yes'
					);
					if (@opc!=@npc or @opc!=@upc)
					begin
						update {$this->MAuth->getdb('STDVehiclesPackages')}
						set ACTIVE='no'
							,UPDBY=@updby
							,UPDDT=@upddt
						where STDID=@STDID and SUBID=@SUBID and ACTIVE='yes'
						
						insert into {$this->MAuth->getdb('STDVehiclesPackages')} (
							STDID,SUBID,PRICES,PRICEE,DOWNS,DOWNE,FORCUS,NOPAYS
							,NOPAYE,RATE,MEMO1,ACTIVE,INSBY,INSDT
						) 
						select @STDID,@SUBID,FPRICE,TPRICE,DOWNS,DOWNE,FORCUS,NOPAYS
							,NOPAYE,RATE,MEMO1,'yes',@updby,@upddt
						from @tbFre	
					end
				end
				
				insert into #tempResult select 'n','เปลี่ยนแปลงข้อมูล standard เรียบร้อยแล้ว';
				commit tran tsup;
			end try
			begin catch
				rollback tran tsup;
				insert into #tempResult select 'y',ERROR_MESSAGE();
			end catch
		";
		/*
			declare @stdlog varchar(100) = 'STDID::'+cast(@STDID as varchar)+'_SUBID::'+cast(@SUBID as varchar)+'_';
			insert into {$this->MAuth->getdb('hp_UserOperationLog')} (userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
			values ('".$this->sess["IDNo"]."','SYS04::เปลี่ยนแปลง standard รถ',@stdlog+' ".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
		*/
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
	
	function getLOCAT2(){
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
			$now = " and LOCATCD collate Thai_CI_AS in (".$now.") ";
		}else{
			$now = " and 1=2 ";
		}
		
		$sql = "
			select LOCATCD,'('+LOCATCD+') '+LOCATNM as LOCATNM
			from {$this->MAuth->getdb('INVLOCAT')}
			where 1=1 ".$now."
				
			union
			select LOCATCD,'('+LOCATCD+') '+LOCATNM as LOCATNM
			from {$this->MAuth->getdb('INVLOCAT')}
			where 1=1 and '('+LOCATCD+') '+LOCATNM like '%".$dataSearch."%' collate Thai_CI_AS
			order by LOCATCD
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$opt = "";
		if($query->row()){
			foreach($query->result() as $row){
				$opt .= "
					<option value='".str_replace(chr(0),"",$row->LOCATCD)."' 
						".(in_array(str_replace(chr(0),"",$row->LOCATCD),$dataNow) ? "selected":"").">
						".$row->LOCATNM."
					</option>
				";
			}
		}
		
		$response["opt"] = $opt;
		echo json_encode($response);
	}
	
	function getSTDID(){
		$MODEL = $_POST["MODEL"];
		
		$sql = "
			select * from {$this->MAuth->getdb('STDVehicles')}
			where MODEL='".$MODEL."'
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$STDID = "Auto Genarate";
		if($query->row()){
			foreach($query->result() as $row){
				$STDID = $row->STDID;
			}
		}
		
		echo json_encode($STDID);
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

































