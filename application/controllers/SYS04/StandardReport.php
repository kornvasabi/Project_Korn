<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/********************************************************
             ______@21/04/2020______
			 Pasakorn Boonded

********************************************************/
class StandardReport extends MY_Controller {
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
		$claim_branch = "";
		if($this->sess['branch'] !== "OFFยน"){
			$claim_branch = "<option value='{$this->sess['branch']}' selected>{$this->sess['branch']}</option>";
		}
		$html = "
			<div class='k_tab1' name='home' locat='{$this->sess['branch']}' is_mobile='{$this->sess['is_mobile']}' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}' style='height:calc(100vh - 132px);overflow:auto;background-color:#eaeded;'>
				<div class='col-sm-12' >
					<div class='row'>
						<div class='col-sm-2'>	
							<div class='form-group'>
								รุ่น
								<input type='text' id='SMODEL' class='form-control input-sm' placeholder='รุ่น'  value=''>
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
						<!--div class='col-sm-2'>	
							<div class='form-group'>
								กลุ่มรถ
								<input type='text' id='SGCODE' class='form-control input-sm' placeholder='กลุ่มรถ' >
							</div>
						</div-->
						<div class='col-sm-2'>	
							<div class='form-group'>
								จำนวนเงินดาวน์
								<input type='text' id='SDOWN' class='form-control input-sm jzAllowNumber' placeholder='จำนวนเงินดาวน์'  value=''>
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								จำนวนงวด
								<input type='text' id='SNOPAY' class='form-control input-sm jzAllowNumber' placeholder='จำนวนงวด' value=''>
							</div>
						</div>
						<div class='col-sm-2'>
							<div class='form-group'>
								วันที่
								<input type='text' id='EVENTDT' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' value='' style='font-size:10.5pt'>
							</div>
						</div>
						<div class='col-sm-1'>	
							<div class='form-group'>
								STDID
								<input type='text' id='Search_STDID' class='form-control input-sm jzAllowNumber' placeholder='STDID'>
							</div>
						</div>
						<div class='col-sm-1'>	
							<div class='form-group'>
								STDID
								<input type='text' id='Search_SUBID' class='form-control input-sm jzAllowNumber' placeholder='SUBID'>
							</div>
						</div>
						<div class='col-sm-5'>	
							<div class='form-group'>
								กิจกรรมการขาย
								<select id='SACTICOD' class='form-control JD-BSSELECT' title='เลือก' multiple data-actions-box='true' data-size='8' data-live-search='true'></select>
							</div>
						</div>
						<div class='col-sm-5'>	
							<div class='form-group'>
								สาขา
								<select id='Search_LOCAT' class='form-control JD-BSSELECT' title='เลือก'  multiple data-actions-box='true' data-size='8' data-live-search='true'>
									".$claim_branch."
								</select>
							</div>
						</div>
						<div class='col-sm-3'>
							<div class='form-group'>
								สถานะภาพรถ
								<div class='row'>
									<div class='col-xs-12'><br>
										<label class='radio-inline lobiradio-success lobiradio'>
											<input type='radio' name='stana' value='all' checked=''> 
											<i></i> ทั้งหมด
										</label>
										<label class='radio-inline lobiradio'>
											<input type='radio' name='stana' value='N'> 
											<i></i> รถใหม่
										</label>
										<label class='radio-inline lobiradio-danger lobiradio'>
											<input type='radio' name='stana' value='O'> 
											<i></i> รถเก่า 
										</label><br><br>
									</div>
								</div>
							</div>	
						</div>
						<div class='col-sm-9'>	
							<div class='form-group'>
								<BR><BR>
								<button id='btnsearchRP' class='btn btn-cyan btn-block'>
									<span class='glyphicon glyphicon-search'> สอบถามข้อมูลสแตนดาร์ด</span>
								</button>
							</div>
						</div>
						<div class='col-sm-12' id='TableResultStandard'></div>
					</div>
				</div>
			</div>
		";
		
		$html.= "<script src='".base_url('public/js/SYS04/StandardReport.js')."'></script>";
		echo $html;
	}
	function getSACTICOD2(){
		$response  = array("opt"=>"");
		$dataSearch = trim($_REQUEST['filter']);
		$dataNow   = ($_REQUEST["now"] == "" ? array():$_REQUEST["now"]);
		$snow = sizeof($dataNow);
		
		$now = "";
		for($i=0;$i<$snow;$i++){
			if($now !== ""){ $now .=",";}
			$now .="'".$dataNow[$i]."'";
		}
		if($now !== ""){
			$now = " and ACTICOD in (".$now.")";
		}else{
			$now = "and 1=2";
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
	function Search(){
		$MODEL 		   = $_REQUEST['MODEL'];
		$BAAB          = $_REQUEST['BAAB'];
		$COLOR         = $_REQUEST['COLOR'];
		//$GCODE   	   = $_REQUEST['GCODE'];
		$DOWN   	   = str_replace(",","",$_REQUEST['DOWN']);
		$NOPAY  	   = str_replace(",","",$_REQUEST['NOPAY']);
		$EVENTDT       = $this->Convertdate(1,$_REQUEST['EVENTDT']);
		$Search_STDID  = $_REQUEST['Search_STDID'];
		$Search_SUBID  = $_REQUEST['Search_SUBID'];
		$SACTICOD      = $_REQUEST['SACTICOD'];
		$Search_LOCAT  = $_REQUEST['Search_LOCAT'];
		$stat          = $_REQUEST['stat'];
		//$payment       = $_REQUEST['payment'];
		
		$cond = ""; $cond2="";
		$response = array("error"=>false,"msg"=>array());
		
		if($MODEL !== ""){
			$cond .=" and a.MODEL = '".$MODEL."'";
		}else{
			$response['error'] = true;
			$response['msg'][] = "กรุณาระบุรุ่นรถก่อนที่จะค้นหาครับ";
		}
		if($DOWN !== ""){
			$cond .=" and ".$DOWN." between z.DOWNS and z.DOWNE /*and h.PRICE >= ".$DOWN."*/";
		}else{
			$response['error'] = true;
			$response['msg'][] = "กรุณาระบุเงินดาวน์รถก่อนที่จะค้นหาครับ";
		}
		if($NOPAY !== ""){
			$cond .=" and ".$NOPAY." between x.NOPAYS and x.NOPAYE";
		}else{
			$response['error'] = true;
			$response['msg'][] = "กรุณาระบุจำนวนงวดในการผ่อนรถก่อนที่จะค้นหาครับ";
		}
		if($BAAB !== ""){
			$cond .=" and d.BAAB like '".$BAAB."%'";
		}
		if($COLOR !== ""){
			$cond .=" and e.COLOR like '".$COLOR."%'";
		}
		/*
		if($GCODE !== ""){
			$cond .=" and w.GCODE like '".$GCODE."%'";
		}
		*/
		if($EVENTDT !== ""){
			$cond2 .=" and ".$EVENTDT." between EVENTStart and EVENTEnd";
		}
		if($Search_STDID !== ""){
			$cond .=" and a.STDID = '".$Search_STDID."'";
		}
		if($Search_SUBID !== ""){
			$cond .=" and b.SUBID = '".$Search_SUBID."'";
		}
		if($SACTICOD !== ""){
			$temp = "";
			$size = sizeof($SACTICOD);
			for($i=0;$i<$size;$i++){
				if($temp !== ""){$temp .=",";}
				$temp .= $SACTICOD[$i];
			}
			//print_r ($temp); exit;
			$cond .=" and c.ACTICOD in (".$temp.",'all')";
		}else{
			$response['error'] = true;
			$response['msg'][] = "กรุณาเลือกกิจกรรมการขายรถก่อนที่จะค้นหาครับ";
		}
		if($response["error"]){echo json_encode($response); exit;}
		
		if($Search_LOCAT !== ""){
			$temp ="";
			$size = sizeof($Search_LOCAT);
			for($i=0;$i<$size;$i++){
				if($temp !== ""){$temp .=",";}
				$temp .= "'".$Search_LOCAT[$i]."'";
			}
			$cond .=" and f.LOCAT in(".$temp.",'all')";
		}
		if($stat == 'N'){
			$cond .=" and b.STAT = 'N' ";
		}else if($stat == 'O'){
			$cond .=" and b.STAT = 'O'";
		}
		
		$sql = "
			select distinct
				STDID,SUBID,MODEL,EVENTStart,EVENTEnd
				,STDNAME,STDDESC,STAT 
			from (
				select distinct a.STDID,b.SUBID,a.MODEL 
					,convert(varchar(8),b.EVENTStart,112) as EVENTStart
					,convert(varchar(8),b.EVENTEnd,112) as EVENTEnd
					,z.DOWNE,z.DOWNS,x.NOPAYE,x.NOPAYS
					,b.STDNAME,b.STDDESC,case when b.STAT = 'N' then 'รถใหม่' else 'รถเก่า' end STAT,c.ACTICOD
				from {$this->MAuth->getdb('STDVehicles')} a
				inner join {$this->MAuth->getdb('STDVehiclesDetail')} b on a.STDID = b.STDID
				left join {$this->MAuth->getdb('STDVehiclesACTI')} c on b.STDID = c.STDID and b.SUBID = c.SUBID
				left join {$this->MAuth->getdb('STDVehiclesBAAB')} d on b.STDID = d.STDID and b.SUBID = d.SUBID
				left join {$this->MAuth->getdb('STDVehiclesCOLOR')} e on b.STDID = e.STDID and b.SUBID = e.SUBID
				left join {$this->MAuth->getdb('STDVehiclesLOCAT')} f on b.STDID = f.STDID and b.SUBID = f.SUBID
				--left join {$this->MAuth->getdb('SETGROUP')} w on c.ACTICOD = w.GCODE collate thai_cs_as
				left join {$this->MAuth->getdb('STDVehiclesDown')} z on b.STDID = z.STDID and b.SUBID = z.SUBID     --->เงินดาวน์
				left join {$this->MAuth->getdb('STDVehiclesPackages')} x on b.STDID = x.STDID and b.SUBID = x.SUBID --->จำนวนงวด
				where 1=1 ".$cond."
			)a where 1=1 ".$cond2."
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$html .="
					<tr>
						<td style='width:40px'>
							<i class='detailCalculate btn btn-xs btn-success' STDID='".$row->STDID."' SUBID='".$row->SUBID."' style='cursor:pointer;'><span class='fa fa-folder-open'>คำนวนค่างวดรถ </span></i>
						</td>
						<td style='vertical-align:middle;'>".$row->STDID."</td>
						<td style='vertical-align:middle;'>".$row->SUBID."</td>
						<td style='vertical-align:middle;'>".$row->MODEL."</td>
						<td style='vertical-align:middle;'>".$this->Convertdate(2,$row->EVENTStart)."</td>
						<td style='vertical-align:middle;'>".$this->Convertdate(2,$row->EVENTEnd)."</td>
						<td style='vertical-align:middle;'>".$row->STDDESC."</td>
					</tr>
				";
			}
		}
		$html = "
			<div id='table-result-standard' class='col-sm-12' style='height:calc(100% - 30px);width:100%;overflow:auto;font-size:8pt;'>
				<table id='table-standard' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='100%' border=1>
					<thead style='background: rgba(0, 0, 0, 0) url(&#39;../public/lobiadmin-master/version/1.0/ajax/img/bg/bg6.png&#39;) repeat scroll 0% 0%;'>						
						<tr style='line-height:20px;'>
							<td style='vertical-align:middle;text-align:center;font-size:8pt;' colspan='8'>
								เงื่อนไข 
							</td>
						</tr>
						<tr>
							<th style='vertical-align:middle; width:10%;'>#</th>
							<th style='vertical-align:middle; width:10%;''>STDID</th>
							<th style='vertical-align:middle; width:10%;''>SUBID</th>
							<th style='vertical-align:middle; width:20%;''>รุ่น</th>
							<th style='vertical-align:middle; width:10%;''>จากวันที่</th>
							<th style='vertical-align:middle; width:10%;''>ถึงวันที่</th>
							<th style='vertical-align:middle; width:30%;''>สภาพรถ</th>
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
	function DetailCalculate(){
		$STDID = $_REQUEST['STDID'];
		$SUBID = $_REQUEST['SUBID'];
		$DOWN  = str_replace(",","",$_REQUEST['DOWN']);
		$NOPAY = str_replace(",","",$_REQUEST['NOPAY']);
		$sql = "
			select distinct
				STDID,SUBID,MODEL,EVENTStart,EVENTEnd
				,STDNAME,STDDESC,STAT 
			from (
				select distinct a.STDID,b.SUBID,a.MODEL 
					,convert(varchar(8),b.EVENTStart,112) as EVENTStart
					,convert(varchar(8),b.EVENTEnd,112) as EVENTEnd
					,z.DOWNE,z.DOWNS,x.NOPAYE,x.NOPAYS
					,b.STDNAME,b.STDDESC,case when b.STAT = 'N' then 'รถใหม่' else 'รถเก่า' end STAT,c.ACTICOD
				from {$this->MAuth->getdb('STDVehicles')} a
				inner join {$this->MAuth->getdb('STDVehiclesDetail')} b on a.STDID = b.STDID
				left join {$this->MAuth->getdb('STDVehiclesACTI')} c on b.STDID = c.STDID and b.SUBID = c.SUBID
				left join {$this->MAuth->getdb('STDVehiclesBAAB')} d on b.STDID = d.STDID and b.SUBID = d.SUBID
				left join {$this->MAuth->getdb('STDVehiclesCOLOR')} e on b.STDID = e.STDID and b.SUBID = e.SUBID
				left join {$this->MAuth->getdb('STDVehiclesLOCAT')} f on b.STDID = f.STDID and b.SUBID = f.SUBID
				left join {$this->MAuth->getdb('SETGROUP')} w on c.ACTICOD = w.GCODE collate thai_cs_as
				left join {$this->MAuth->getdb('STDVehiclesDown')} z on b.STDID = z.STDID and b.SUBID = z.SUBID     --->เงินดาวน์
				left join {$this->MAuth->getdb('STDVehiclesPackages')} x on b.STDID = x.STDID and b.SUBID = x.SUBID --->จำนวนงวด
				where a.STDID = '".$STDID."' and b.SUBID = '".$SUBID."' 
			)a 
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$html .= "
					<div class='col-sm-12' style='background-color:#85929e;'>	
						<div class='col-sm-5'>
							<div class='form-group'>
								<div class='col-sm-12' style='height:100%;width:100%;overflow-x:auto;font-size:8pt;border:0.1px dotted red;'>
									<table id='dataTable-stopvat' style='color:#0000b3;' class='table table-bordered dataTable table-hover'>
										<thead>
											<tr role='row' style='height:50px;font-size:8pt;background-color:#48c9b0;color:white;'>
												<th width='20%' colspan='2' style='vertical-align:middle;'>ข้อมูลสแตนดาร์ด</th>
												<th width='40%' style='vertical-align:middle;'>กิจกรรมการขายรถ</th>
												<th width='20%' style='vertical-align:middle;'>แบบ</th>
												<th width='10%' style='vertical-align:middle;'>สี</th>
												<th width='10%' style='vertical-align:middle;'>สาขา</th>
											</tr>
										</thead>
										<tbody id='data-tbody' style='white-space:nowrap;background-color:white;font-size:9pt;height'>
											<tr>
												<td>
													<span style='color:red;'><b>STDID :</b><br><br><b>SUBID :</b></span>
													<br><br><b>รุ่น</b>
													<br><br><b>จากวันที่</b>
													<br><br><b>ถึงวันที่</b>
													<br><br><b>ชื่อ</b>
													<br><br><b>ลักษณะ</b>
													<br><br><b>สภาพ</b>
													<br><br><b>สถานะ</b>
												</td>
												<td>
													{$row->STDID}
													<br><br>{$row->SUBID}
													<br><br>{$row->MODEL}
													<br><br>".$this->Convertdate(2,$row->EVENTStart)."
													<br><br>".$this->Convertdate(2,$row->EVENTEnd)."
													<br><br>{$row->STDNAME}
													<br><br>{$row->STDNAME}
													<br><br>{$row->STDDESC}
													<br><br>{$row->STAT}
												</td>
												<td style='vertical-align: text-top;'>
													<div style='max-width:200px;max-height:220px;overflow:auto;'>
														".($this->search_acti($STDID,$SUBID))."
													</div>
												</td>
												<td style='vertical-align: text-top;'>
													<div style='max-width:200px;max-height:220px;overflow:auto;'>
														".($this->search_baab($STDID,$SUBID))."
													</div>
												</td>
												<td style='vertical-align: text-top;'>
													<div style='max-width:200px;max-height:220px;overflow:auto;'>
														".($this->search_color($STDID,$SUBID))."
													</div>
												</td>
												<td style='vertical-align: text-top;'>
													<div style='width:60px;height:220px;overflow: auto;'>
														".($this->search_locat($STDID,$SUBID))."
													</div>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
								<div class='col-sm-12' style='background-color:#d9d9d9;'><br>
									".($this->search_price_down($STDID,$SUBID,$DOWN,$NOPAY))."
								</div>
							</div>
						</div>
						<div class='col-sm-7'>
							<div class='form-group'>	
								<div class='row' style='background-color:#d9d9d9;'>
									".($this->search_payment($STDID,$SUBID,$DOWN,$NOPAY))."
								</div>
							</div>	
						</div>	
					</div>
				";
			}
		}
		$response = array("html"=>$html);
		echo json_encode($response);
	}
	function search_acti($STDID,$SUBID){
		$sql = "
			select '('+c.ACTICOD+') '+d.ACTIDES collate thai_cs_as as ACTICOD
			from {$this->MAuth->getdb('STDVehicles')} a
			left join {$this->MAuth->getdb('STDVehiclesDetail')} b on a.STDID=b.STDID
			left join {$this->MAuth->getdb('STDVehiclesACTI')} c on b.STDID=c.STDID and b.SUBID=c.SUBID
			left join {$this->MAuth->getdb('SETACTI')} d on c.ACTICOD=d.ACTICOD collate thai_cs_as
			where a.STDID='{$STDID}' and b.SUBID='{$SUBID}' and c.ACTIVE = 'yes'
		";
		//echo $sql ; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				if($html != ""){ $html .= "<BR>"; }
				$html .= str_replace(chr(0),"",$row->ACTICOD);
			}
		}
		return $html;
	}
	function search_baab($STDID,$SUBID){
		$sql = "
			select c.BAAB
			from {$this->MAuth->getdb('STDVehicles')} a
			left join {$this->MAuth->getdb('STDVehiclesDetail')} b on a.STDID = b.STDID
			left join {$this->MAuth->getdb('STDVehiclesBAAB')} c on b.STDID = c.STDID and b.SUBID = c.SUBID
			where a.STDID = '{$STDID}' and b.SUBID = '{$SUBID}' and c.ACTIVE = 'yes'
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				if($html !== ""){ $html .="<BR>"; }
				$html .=str_replace(chr(0),"",$row->BAAB);
			}
		}
		return $html;
	}
	function search_color($STDID,$SUBID){
		$sql = "
			select c.COLOR
			from {$this->MAuth->getdb('STDVehicles')} a
			left join {$this->MAuth->getdb('STDVehiclesDetail')} b on a.STDID = b.STDID
			left join {$this->MAuth->getdb('STDVehiclesCOLOR')} c on b.STDID = c.STDID and b.SUBID = c.SUBID
			where a.STDID = '{$STDID}' and b.SUBID = '{$SUBID}' and c.ACTIVE = 'yes'
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				if($html !== ""){ $html .="<BR>"; }
				$html .=str_replace(chr(0),"",$row->COLOR);
			}
		}
		return $html;
	}
	function search_locat($STDID,$SUBID){
		$sql = "
			select c.LOCAT
			from {$this->MAuth->getdb('STDVehicles')} a
			left join {$this->MAuth->getdb('STDVehiclesDetail')} b on a.STDID = b.STDID
			left join {$this->MAuth->getdb('STDVehiclesLOCAT')} c on b.STDID = c.STDID and b.SUBID = c.SUBID
			where a.STDID = '{$STDID}' and b.SUBID = '{$SUBID}' and c.ACTIVE = 'yes'
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				if($html !== ""){ $html .="<BR><BR>"; }
				$html .=str_replace(chr(0),"",$row->LOCAT);
			}
		}
		return $html;
	}
	/*--------------เงินดาวน์---งวด--ราคารถ---ค่าใช้จ่ายอื่นๆ--------------------------------*/
	function search_price_down($STDID,$SUBID,$DOWN,$NOPAY){
		$arrs['SUBID'] = $SUBID;
		$arrs['STDID'] = $STDID;
		$sql = "
			select top 1 a.STDID,b.SUBID,a.MODEL 
				,b.EVENTStart,b.EVENTEnd
				,case when b.EVENTEnd > GETDATE() then 'สามารถใช้โปรนี้ได้' else 'ไม่สามารถใช้โปรโมชั่นนี้ได้' end as GETDATEADD
				,b.STDNAME,b.STDDESC
				,c.PRICE2
				,c.PRICE3
				,d.DOWNS
				,d.DOWNE
				,d.INTERESTRT
				,d.INTERESTRT_GVM
				,d.INSURANCEPAY
				,d.INSURANCE
				,d.TRANSFERS
				,d.REGIST
				,d.ACT
				,d.COUPON
				,d.APPROVE
				,(d.INSURANCE + d.TRANSFERS + d.REGIST + d.ACT + d.COUPON)  as TOTAL
				,case when d.APPROVE = 'Y' then 'ต้องขออนุมัติ' else 'ไม่ต้องขออนุมัติ' end as APPROVEDESC
			from {$this->MAuth->getdb('STDVehicles')} a
			left join {$this->MAuth->getdb('STDVehiclesDetail')} b on a.STDID = b.STDID
			left join {$this->MAuth->getdb('STDVehiclesPRICE')} c on b.STDID = c.STDID and b.SUBID = c.SUBID
			left join {$this->MAuth->getdb('STDVehiclesDown')} d on c.STDID = d.STDID and c.SUBID = d.SUBID 
				and c.PRICE2=d.PRICE2 and c.PRICE3=d.PRICE3	
			where a.STDID = '{$STDID}' and b.SUBID='{$SUBID}'
			and ".$DOWN." between d.DOWNS and d.DOWNE
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		$html = "";
		$response = array();
		if($query->row()){
			foreach($query->result() as $row){
				$price = ""; $statusdown = ""; $eventcolor = "";
				$price = $row->PRICE2 + $row->TOTAL;
				if($row->APPROVEDESC == "ต้องขออนุมัติ"){
					$statusdown ="text-danger";
				}else{
					$statusdown ="text-success"; 
				}
				if($row->GETDATEADD == "สามารถใช้โปรนี้ได้"){
					$eventcolor = "text-primary";
				}else{
					$eventcolor = "text-danger";
				}
				//echo $PERMONTH_TOTAL; exit;
				$html .="
					<div class='col-sm-4'>	
						<div class='form-group'>
							<span class='label label-primary' style='font-size:12px;'>ราคารถ</span>
							<input type='text' class='form-control input-sm' style='text-align:right;' id='PRICE' value='".($this->search_price($STDID,$SUBID))."' readonly>
						</div>
					</div>
					<div class='col-sm-4'>	
						<div class='form-group'>
							<span class='label label-primary' style='font-size:12px;'>เงินดาวน์</span>
							<input type='text' class='form-control input-sm' style='text-align:right;' id='DOWN' value='".number_format($DOWN,2)."' readonly>
						</div>
					</div>
					<div class='col-sm-4'>	
						<div class='form-group'>
							<span class='label label-primary' style='font-size:12px;'>จำนวนงวด</span>
							<input type='text' class='form-control input-sm' style='text-align:right;' id='NOPAY' value='".$NOPAY."' readonly>
						</div>
					</div>
					<div class='checkpayment' id1='".$arrs['STDID']."' id2='".$arrs['SUBID']."'>
						<div class='col-sm-6'>	
							<div class='form-group'>
								<span class='label label-warning' style='font-size:12px;'>ค่าป. 1 ผ่อน</span>
								<span style='color:#d53320;'>
									<input class='form-check-input checkall' type='checkbox' name='insurance' value='".number_format($row->INSURANCE,2)."'>
									<label class='form-check-label' for='insurance' style='font-size:14px;'>รวมราคารถ</label>
								</span>
								<input type='text' class='form-control input-sm' id='INSURANCE' style='text-align:right;' value='".number_format($row->INSURANCE,2)."' readonly>
							</div>
						</div>
						<div class='col-sm-6'>	
							<div class='form-group'>
								<span class='label label-warning' style='font-size:12px;'>ค่าโอน</span>
								<span style='color:#d53320;'>
									<input class='form-check-input checkall' type='checkbox' name='transfers' value='".number_format($row->TRANSFERS,2)."'>
									<label class='form-check-label' for='transfers' style='font-size:14px;'>รวมราคารถ</label>
								</span>
								<input type='text' class='form-control input-sm' id='TRANSFERS' style='text-align:right;' value='".number_format($row->TRANSFERS,2)."' readonly>
							</div>
						</div>
						<div class='col-sm-6'>	
							<div class='form-group'>
								<span class='label label-warning' style='font-size:12px;'>ค่าจดทะเบียน</span>
								<span style='color:#d53320;'>
									<input class='form-check-input checkall' type='checkbox' name='regist' value='".number_format($row->REGIST,2)."'>
									<label class='form-check-label' for='regist' style='font-size:14px;'>รวมราคารถ</label>
								</span>
								<input type='text' class='form-control input-sm' id='REGIST' style='text-align:right;' value='".number_format($row->REGIST,2)."' readonly>
							</div>
						</div>
						<div class='col-sm-6'>	
							<div class='form-group'>
								<span class='label label-warning' style='font-size:12px;'>ค่า พรบ.</span>
								<span style='color:#d53320;'>
									<input class='form-check-input checkall' type='checkbox' name='act' value='".number_format($row->ACT,2)."'>
									<label class='form-check-label' for='act' style='font-size:14px;'>รวมราคารถ</label>
								</span>
								<input type='text' class='form-control input-sm' id='ACT' style='text-align:right;' value='".number_format($row->ACT,2)."' readonly>
							</div>
						</div>
						<div class='col-sm-6'>	
							<div class='form-group'>
								<span class='label label-warning' style='font-size:12px;'>คู่ปองชิงโชค</span>
								<span style='color:#d53320;'>
									<input class='form-check-input checkall' type='checkbox' name='coupon' value='".number_format($row->COUPON,2)."'>
									<label class='form-check-label' for='coupon' style='font-size:14px;'>รวมราคารถ</label>
								</span>
								<input type='text' class='form-control input-sm' id='COUPON' style='text-align:right;' value='".number_format($row->COUPON,2)."' readonly>
							</div>
						</div>
						<div class='col-sm-6'>	
							<div class='form-group'>
								<span style='color:#d53320;'>
									<input class='form-check-input checkedtotal' type='checkbox' name='total' value=''>
									<label class='form-check-label' for='total' style='font-size:14px;'>เลือกทั้งหมด</label>
								</span>
							</div>
						</div>
					</div>
					<div class='col-sm-7'>	
						<div class='form-group'>
							<span class='label label-cyan' style='font-size:12px;'>อนุมัติการขาย</span>
							<input type='text' class='form-control input-sm ".$statusdown."' id='TOTAL' style='text-align:center;font-size:14px;' value='".$row->APPROVEDESC."' readonly>
						</div>
					</div>
					<div class='col-sm-5'>	
						<div class='form-group'>
							<span class='label label-cyan' style='font-size:12px;'>โปรโมชั่น</span>
							<input type='text' class='form-control input-sm ".$eventcolor."' id='TOTAL' style='text-align:center;font-size:14px;' value='".$row->GETDATEADD."' readonly>
						</div>
					</div>
					<!--div class='col-sm-4'>	
						<div class='form-group'>
							<br>
							<input class='form-check-input Ctotal' style='cursor:pointer;max-width:20px;max-height:10px;' type='checkbox' id='Ctotal' stdid='".$STDID."' checked> รวมค่าใช่จ่ายอื่นๆ
						</div>
					</div--><br>
					<!--div id='STANA' style='text-align:center;font-size:18px;color:;'>***".$row->APPROVEDESC."***</div-->
					<div class='col-sm-4'>	
						<br>
					</div><br>
				";
			}
		}
		return $html;
	}
	function search_price($STDID,$SUBID){
		$sql = "
			select c.PRICE2
			from {$this->MAuth->getdb('STDVehicles')} a
			left join {$this->MAuth->getdb('STDVehiclesDetail')} b on a.STDID = b.STDID
			left join {$this->MAuth->getdb('STDVehiclesPRICE')} c on b.STDID = c.STDID and b.SUBID = c.SUBID
			where a.STDID = '{$STDID}' and b.SUBID = '{$SUBID}' and c.ACTIVE = 'yes'
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$html .= number_format($row->PRICE2,2);
			}
		}
		return $html;
	}
	/*-------------------------------------คำนวนค่างวดรถ---------------------------------------------*/
	function search_payment($STDID,$SUBID,$DOWN,$NOPAY){
		$sql = "
			select VATRT from {$this->MAuth->getdb('VATMAST')} 
			where GETDATE() between FRMDATE and isnull(TODATE,GETDATE())
		";
		$vatrt = "";
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$vatrt = number_format($row->VATRT,2);
			}
		}
		$sql = "
			select top 1 a.STDID,b.SUBID,a.MODEL 
				,b.EVENTStart,b.EVENTEnd
				,b.STDNAME,b.STDDESC
				,c.PRICE2
				,c.PRICE3
				,d.DOWNS
				,d.DOWNE
				,d.INTERESTRT
				,d.INTERESTRT_GVM
				,d.INSURANCEPAY
				,d.INSURANCE
				,d.TRANSFERS
				,d.REGIST
				,d.ACT
				,d.COUPON
				,d.APPROVE
				,(d.INSURANCE + d.TRANSFERS + d.REGIST + d.ACT + d.COUPON)  as TOTAL
				,case when d.APPROVE = 'Y' then 'ต้องขออนุมัติ' else 'ไม่ต้องขออนุมัติ' end as APPROVEDESC
			from {$this->MAuth->getdb('STDVehicles')} a
			left join {$this->MAuth->getdb('STDVehiclesDetail')} b on a.STDID = b.STDID
			left join {$this->MAuth->getdb('STDVehiclesPRICE')} c on b.STDID = c.STDID and b.SUBID = c.SUBID
			left join {$this->MAuth->getdb('STDVehiclesDown')} d on c.STDID = d.STDID and c.SUBID = d.SUBID 
				and c.PRICE2=d.PRICE2 and c.PRICE3=d.PRICE3
			where a.STDID = '{$STDID}' and b.SUBID='{$SUBID}'
			and ".$DOWN." between d.DOWNS and d.DOWNE
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		$html = "";
		$response = array();
		if($query->row()){
			foreach($query->result() as $row){
				$price = ""; $pricesother = "";
				$price = $row->PRICE2/* + $row->TOTAL*/;
				
				$sql2 = "
					select * 
						,cast((INTERAST_RATE / 12) as decimal(18,2)) as INTERAST_RATE_MONTH
						,PRICE_TOTAL - cast(OPTPERMONTH_AFTER_VAT_TOTAL * NOPAY as decimal(18,2)) as PRICEHP_AFTER_VAT_TOTAL				
						,cast(OPTPERMONTH_AFTER_VAT_TOTAL * NOPAY as decimal(18,2)) as OPT_AFTER_VAT_TOTAL
					from {$this->MAuth->getdb('fn_jd_calPriceForSale')}(
						'".$price."',
						'".$DOWN."',
						'".$vatrt."',
						'0',
						'".$row->INTERESTRT."',
						'".$NOPAY."',
						'5'
					)
				";
				//echo $sql2; exit;
				$query2 = $this->db->query($sql2);
				if($query2->row()){
					foreach($query2->result() as $row2){
						$html .="
							<div class='calculate'>
								<div class='col-sm-6' style='background-color:#00b3b3;'>	
									<div class='col-sm-6'>
										<div class='form-group'>
											ราคารถรวม VAT
											<input type='text' class='form-control input-sm text-primary PRICE_AFTER_VAT' style='text-align:right;' value='".number_format($row2->PRICE_AFTER_VAT,2)."' readonly>
										</div>
									</div>
									<div class='col-sm-6'>
										<div class='form-group'>
											ราคารถก่อน VAT
											<input type='text' class='form-control input-sm text-primary PRICE_BEFORE_VAT' style='text-align:right;' value='".number_format($row2->PRICE_BEFORE_VAT,2)."' readonly>
										</div>
									</div>	
									<div class='col-sm-6'>
										<div class='form-group'>
											เงินดาวน์รวม VAT
											<input type='text' class='form-control input-sm text-primary DWN_AFTER_VAT' style='text-align:right;' value='".number_format($row2->DWN_AFTER_VAT,2)."' readonly>
										</div>
									</div>
									<div class='col-sm-6'>
										<div class='form-group'>
											เงินดาวน์ก่อน VAT
											<input type='text' class='form-control input-sm text-primary DWN_BEFORE_VAT' style='text-align:right;' value='".number_format($row2->DWN_BEFORE_VAT,2)."' readonly>
										</div>
									</div>
									<div class='col-sm-6'>
										<div class='form-group'>
											ยอดตั้งลูกหนี้รวม VAT
											<input type='text' class='form-control input-sm text-primary PRICEDOWN_AFTER_VAT' style='text-align:right;' value='".number_format($row2->PRICEDOWN_AFTER_VAT,2)."' readonly>
										</div>
									</div>
									<div class='col-sm-6'>
										<div class='form-group'>
											ยอดตั้งลูกหนี้ก่อน VAT
											<input type='text' class='form-control input-sm text-primary PRICEDOWN_BEFORE_VAT' style='text-align:right;' value='".number_format($row2->PRICEDOWN_BEFORE_VAT,2)."' readonly>
										</div>
									</div>
									<div class='col-sm-6'>
										<div class='form-group'>
											อัตราดอกเบี้ยต่อปี 
											<input type='text' class='form-control input-sm text-primary INTERAST_RATE' style='text-align:right;' value='".number_format($row2->INTERAST_RATE,2)."' readonly>
										</div>
									</div>
									<div class='col-sm-6'>
										<div class='form-group'>
											อัตราดอกเบี้ยต่อเดือน
											<input type='text' class='form-control input-sm text-primary INTERAST_RATE_MONTH' style='text-align:right;' value='".number_format($row2->INTERAST_RATE_MONTH,2)."' readonly>
										</div>
									</div>
									<div class='col-sm-6'>
										<div class='form-group'>
											ดอกผลเช่าซื้อรวม VAT
											<input type='text' class='form-control input-sm text-primary HP_AFTER_VAT' style='text-align:right;' value='".number_format($row2->HP_AFTER_VAT,2)."' readonly>
										</div>
									</div>
									<div class='col-sm-6'>
										<div class='form-group'>
											ดอกผลเช่าซื้อก่อน VAT
											<input type='text' class='form-control input-sm text-primary HP_BEFORE_VAT' style='text-align:right;' value='".number_format($row2->HP_BEFORE_VAT,2)."' readonly>
										</div>
									</div>	
									<div class='col-sm-6'>
										<div class='form-group'>
											ราคาขายผ่อนก่อน VAT
											<input type='text' class='form-control input-sm text-primary PRICEHP_BEFORE_VAT' style='text-align:right;' value='".number_format($row2->PRICEHP_BEFORE_VAT,2)."' readonly>
										</div>
									</div>
									<div class='col-sm-6'>
										<div class='form-group'>
											ผ่อนงวดละรวม
											<input type='text' class='form-control input-sm text-primary PERMONTH_BEFORE_VAT' style='text-align:right;' value='".number_format($row2->PERMONTH_BEFORE_VAT,2)."' readonly>
										</div>
									</div>
									<div class='col-sm-6'>
										<div class='form-group'>
											ราคาขายผ่อนรวม VAT
											<input type='text' class='form-control input-sm text-primary PRICEHP_AFTER_VAT' style='text-align:right;' value='".number_format($row2->PRICEHP_AFTER_VAT,2)."' readonly>
										</div>
									</div>
									<div class='col-sm-6'>
										<div class='form-group'>
											ผ่อนงวดละรวม VAT
											<input type='text' class='form-control input-sm text-primary PERMONTH_AFTER_VAT' style='text-align:right;' value='".number_format($row2->PERMONTH_AFTER_VAT,2)."' readonly>
										</div>
									</div>
									<div class='col-sm-6'>
										<div class='form-group'>
											ราคาขายผ่อนสุทธิรวม VAT
											<input type='text' class='form-control input-sm text-primary PRICEHP_AFTER_VAT_TOTAL' style='text-align:right;' value='".number_format($row2->PRICEHP_AFTER_VAT_TOTAL,2)."' readonly>
										</div>
									</div>
									<div class='col-sm-6'>
										<div class='form-group'>
											ผ่อนงวดละรวม VAT
											<input type='text' class='form-control input-sm text-primary PERMONTH_AFTER_VAT_TOTAL' style='text-align:right;' value='".number_format($row2->PERMONTH_AFTER_VAT_TOTAL,2)."' readonly>
										</div>
									</div>
								</div>
								<div class='col-sm-6' style='background-color:#48c9b0;'>	
									<div class='col-sm-6'>
										<div class='form-group'>
											ราคาขายสดรวม VAT
											<input type='text' class='form-control input-sm text-primary OPT_AFTER_VAT' style='text-align:right;' value='".number_format($row2->OPT_AFTER_VAT,2)."' readonly>
										</div>
									</div>
									<div class='col-sm-6'>
										<div class='form-group'>
											ราคาขายสดก่อน VAT
											<input type='text' class='form-control input-sm text-primary OPT_BEFORE_VAT' style='text-align:right;' value='".number_format($row2->OPT_BEFORE_VAT,2)."' readonly>
										</div>
									</div>	
									<div style='min-height:325px;'></div>
									<div class='col-sm-6'>
										<div class='form-group'>
											ดอกผลเช่าซื้อก่อน VAT
											<input type='text' class='form-control input-sm text-primary OPT_BEFORE_VAT' style='text-align:right;' value='".number_format($row2->OPT_BEFORE_VAT,2)."' readonly>
										</div>
									</div>
									<div class='col-sm-6'>
										<div class='form-group'>
											ผ่อนงวดละรวม
											<input type='text' class='form-control input-sm text-primary OPTPERMONTH_AFTER_VAT' style='text-align:right;' value='".number_format($row2->OPTPERMONTH_AFTER_VAT,2)."' readonly>
										</div>
									</div>	<div class='col-sm-6'>
										<div class='form-group'>
											ดอกผลเช่าซื้อรวม VAT
											<input type='text' class='form-control input-sm text-primary OPT_AFTER_VAT' style='text-align:right;' value='".number_format($row2->OPT_AFTER_VAT,2)."' readonly>
										</div>
									</div>
									<div class='col-sm-6'>
										<div class='form-group'>
											ผ่อนงวดละรวม VAT
											<input type='text' class='form-control input-sm text-primary OPTPERMONTH_AFTER_VAT' style='text-align:right;' value='".number_format($row2->OPTPERMONTH_AFTER_VAT,2)."' readonly>
										</div>
									</div>	<div class='col-sm-6'>
										<div class='form-group'>
											ราคาขายผ่อนสุทธิรวม VAT
											<input type='text' class='form-control input-sm text-primary OPT_AFTER_VAT_TOTAL' style='text-align:right;' value='".number_format($row2->OPT_AFTER_VAT_TOTAL,2)."' readonly>
										</div>
									</div>
									<div class='col-sm-6'>
										<div class='form-group'>
											ผ่อนงวดละรวม VAT
											<input type='text' class='form-control input-sm text-primary OPTPERMONTH_AFTER_VAT_TOTAL' style='text-align:right;' value='".number_format($row2->OPTPERMONTH_AFTER_VAT_TOTAL,2)."' readonly>
										</div>
									</div>	
								</div>
								<div class='col-sm-12' style='background-color:#82e0aa;'>	
									<div class='col-sm-3'>	
										<div class='form-group'>
											ดอกผลเช่าซื้อรวม VAT
											<input type='text' class='form-control input-sm text-primary HP_TOTAL' style='text-align:right;border-color:#5d6d7e;font-weight: bold;' value='".number_format($row2->HP_TOTAL,2)."' readonly>
										</div>
									</div>
									<div class='col-sm-1'>
									</div>
									<div class='col-sm-4'>	
										<div class='form-group'>
											ผ่อนงวดละ + อุปกรณ์ รวม VAT
											<input type='text' class='form-control input-sm text-primary PERMONTH_TOTAL' id='PAYMONTH' style='text-align:right;border-color:#5d6d7e;font-weight: bold;' value='".number_format($row2->PERMONTH_TOTAL,2)."' readonly>
										</div>
									</div>
									<div class='col-sm-4'>	
										<div class='form-group'>
											ราคาขายผ่อน + อุปกรณ์รวม VAT
											<input stdid='' subid='' type='text' class='form-control input-sm text-primary PRICE_TOTAL' id='PRITOTAL' style='text-align:right;border-color:#5d6d7e;font-weight: bold;' value='".number_format($row2->PRICE_TOTAL,2)."' readonly>
										</div>
									</div>
								</div>
							</div>
						";
					}
				}
			}
		}
		return $html;
	}
	
	function Calculate(){
		$DOWN  = $_REQUEST['DOWN'];
		$NOPAY = $_REQUEST['NOPAY'];
		$stdid = $_REQUEST['stdid'];
		$subid = $_REQUEST['subid'];
		$sumpay= !isset($_REQUEST['sumpay']) ? '' : $_REQUEST['sumpay'];
		$payment = "";
		if($sumpay !== ""){
			$sum = sizeof($sumpay);
			for($i=0; $i<$sum; $i++){
				$cal[]= str_replace(",","",$sumpay[$i]);
			}
			$payment = array_sum($cal);
		}else{
			$payment = 0;
		}
		//echo $payment; exit;
		$sql = "
			select VATRT from {$this->MAuth->getdb('VATMAST')} 
			where GETDATE() between FRMDATE and isnull(TODATE,GETDATE())
		";
		$vatrt = "";
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$vatrt = number_format($row->VATRT,2);
			}
		}
		$sql = "
			select top 1 a.STDID,b.SUBID,a.MODEL 
				,b.EVENTStart,b.EVENTEnd
				,b.STDNAME,b.STDDESC
				,c.PRICE2
				,c.PRICE3
				,d.DOWNS
				,d.DOWNE
				,d.INTERESTRT
				,d.INTERESTRT_GVM
				,d.INSURANCEPAY
				,d.INSURANCE
				,d.TRANSFERS
				,d.REGIST
				,d.ACT
				,d.COUPON
				,d.APPROVE
				,(d.INSURANCE + d.TRANSFERS + d.REGIST + d.ACT + d.COUPON)  as TOTAL
				,case when d.APPROVE = 'Y' then 'ต้องขออนุมัติ' else 'ไม่ต้องขออนุมัติ' end as APPROVEDESC
			from {$this->MAuth->getdb('STDVehicles')} a
			left join {$this->MAuth->getdb('STDVehiclesDetail')} b on a.STDID = b.STDID
			left join {$this->MAuth->getdb('STDVehiclesPRICE')} c on b.STDID = c.STDID and b.SUBID = c.SUBID
			left join {$this->MAuth->getdb('STDVehiclesDown')} d on c.STDID = d.STDID and c.SUBID = d.SUBID 
				and c.PRICE2=d.PRICE2 and c.PRICE3=d.PRICE3	
			where a.STDID = '".$stdid."' and b.SUBID='".$subid."'
			and ".$DOWN." between d.DOWNS and d.DOWNE
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		$html = "";
		$response = array();
		if($query->row()){
			foreach($query->result() as $row){
				$price = ""; $pay = "";
				$price = $row->PRICE2;
				$sql2 = "
					select * 
						,cast((INTERAST_RATE / 12) as decimal(18,2)) as INTERAST_RATE_MONTH
						,PRICE_TOTAL - cast(OPTPERMONTH_AFTER_VAT_TOTAL * NOPAY as decimal(18,2)) as PRICEHP_AFTER_VAT_TOTAL				
						,cast(OPTPERMONTH_AFTER_VAT_TOTAL * NOPAY as decimal(18,2)) as OPT_AFTER_VAT_TOTAL
					from {$this->MAuth->getdb('fn_jd_calPriceForSale')}(
						'".$price."',
						'".$DOWN."',
						'".$vatrt."',
						'".$payment."',--payment
						'".$row->INTERESTRT."',
						'".$NOPAY."',
						'5'
					)
				";
				//echo $sql2; exit;
				$query2 = $this->db->query($sql2);
				if($query2->row()){
					foreach($query2->result() as $row2){
						/*
						$response['PRICE_AFTER_VAT'] = number_format($row2->PRICE_AFTER_VAT,2);
						$response['PRICE_BEFORE_VAT']= number_format($row2->PRICE_BEFORE_VAT,2);
						$response['DWN_AFTER_VAT'] = number_format($row2->DWN_AFTER_VAT,2);
						$response['DWN_BEFORE_VAT'] = number_format($row2->DWN_BEFORE_VAT,2);
						$response['PRICEDOWN_AFTER_VAT'] = number_format($row2->PRICEDOWN_AFTER_VAT,2);
						$response['PRICEDOWN_BEFORE_VAT']= number_format($row2->PRICEDOWN_BEFORE_VAT,2);
						$response['INTERAST_RATE'] = number_format($row2->INTERAST_RATE,2);
						$response['INTERAST_RATE_MONTH'] = number_format($row2->INTERAST_RATE_MONTH,2);
						$response['HP_AFTER_VAT'] = number_format($row2->HP_AFTER_VAT,2);
						$response['HP_BEFORE_VAT'] = number_format($row2->HP_BEFORE_VAT,2);
						$response['PRICEHP_BEFORE_VAT'] = number_format($row2->PRICEHP_BEFORE_VAT,2);
						$response['PERMONTH_BEFORE_VAT'] = number_format($row2->PERMONTH_BEFORE_VAT,2);
						$response['PRICEHP_AFTER_VAT'] = number_format($row2->PRICEHP_AFTER_VAT,2);
						$response['PERMONTH_AFTER_VAT'] = number_format($row2->PERMONTH_AFTER_VAT,2);
						$response['PRICEHP_AFTER_VAT_TOTAL'] = number_format($row2->PRICEHP_AFTER_VAT_TOTAL,2);
						$response['PERMONTH_AFTER_VAT_TOTAL'] = number_format($row2->PERMONTH_AFTER_VAT_TOTAL,2);
						*/
						//$response['OPT_AFTER_VAT'] = number_format($row2->OPT_AFTER_VAT,2);
						$response['OPT_BEFORE_VAT'] = number_format($row2->OPT_BEFORE_VAT,2);
						$response['OPTPERMONTH_AFTER_VAT'] = number_format($row2->OPTPERMONTH_AFTER_VAT,2);
						$response['OPT_AFTER_VAT'] = number_format($row2->OPT_AFTER_VAT,2);
						$response['OPT_AFTER_VAT_TOTAL'] = number_format($row2->OPT_AFTER_VAT_TOTAL,2);
						$response['OPTPERMONTH_AFTER_VAT_TOTAL'] = number_format($row2->OPTPERMONTH_AFTER_VAT_TOTAL,2);
						$response['HP_TOTAL'] = number_format($row2->HP_TOTAL,2);
						$response['PERMONTH_TOTAL'] = number_format($row2->PERMONTH_TOTAL,2);
						$response['PRICE_TOTAL'] = number_format($row2->PRICE_TOTAL,2);
					}
				}
			}
		}
		echo json_encode($response);
	}
}