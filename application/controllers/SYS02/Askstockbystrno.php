<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/********************************************************
             ______@23/07/2020______
			 Pasakorn Boonded

********************************************************/
class Askstockbystrno extends MY_Controller {
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
			<div class='k_tab1' name='home' locat='{$this->sess['branch']}' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}' today='".$this->today('today')."' style='height:calc(100vh - 132px);overflow:auto;background-color:white;'>
				<div class='col-sm-12'>
					<div class='col-sm-4'>	
						<div class='form-group'>
							ค้นหาตามเลขที่ตัวถัง
							<div class='input-group' style='cursor:pointer;'>
								<input type='text' id='add_strno' class='form-control input-sm' placeholder='เลขที่ตัวถังรถ'  readonly>
								<span class='input-group-btn'>
									<button id='btnaddcont' class='btn btn-danger btn-sm' type='button'>
										<span id='btnremove' class='glyphicon glyphicon-remove' aria-hidden='true'></span>
									</button>
								</span>
							</div>
						</div>
					</div>
					<div class='col-sm-4'>
					</div>
					<div class='col-sm-4'>
						<div class='form-group'>
							<br>
							<button id='btnsearchstock' type='button' class='btn btn-info' style='width:100%'><span class='fa fa-search'><b>ค้นหา</b></span></button>
						</div>
					</div>
					<div class='col-sm-12 col-xs-12' style='float:left;height:100%;overflow:auto;'>
						<div id='wizard-financedetail' class='wizard-wrapper'>    
							<div class='wizard'>
								<form id='demo-form2' action='' class='lobi-form' novalidate='novalidate'>
									<ul class='wizard-tabs nav-justified nav nav-pills' style='width:100%;height:100%;'>
										<li class='active' style='background-color:#83f0d6; solid #83f0d6;'>
											<a href='#tab11' prev='#' data-toggle='tab' aria-expanded='true'>
												<span class='step'></span>
												<span class='title'><b>รายละเอียดรถ</b></span>
											</a>
										</li>
										<li style='background-color:#83f0d6; solid #83f0d6;'>
											<a href='#tab22' prev='#tab11' data-toggle='tab'>
												<span class='step'></span>
												<span class='title'><b>รายการซ่อม</b></span>
											</a>
										</li>
										<li style='background-color:#83f0d6; solid #83f0d6;'>
											<a href='#tab33' prev='#tab22' data-toggle='tab'>
												<span class='step'></span>
												<span class='title'><b>ดูลำดับการโอนย้าย</b></span>
											</a>
										</li>
									</ul>
									<div class='tab-content bg-white'>
										".$this->getTab11()."
										".$this->getTab22()."
										".$this->getTab33()."
									</div>
								</form>
							</div>
						</div>				
					</div>
				</div>
			</div>
		";
		$html .="<script src='".base_url('public/js/SYS02/Askstockbystrno.js')."'></script>";
		echo $html;
	}
	function getTab11(){
		$html = "
			<div class='tab-pane active' name='tab11' style='height:calc(100vh - 290px);overflow:auto;'>
				<fieldset style='height:100%'>
					<div style='float:left;height:100%;' class='col-sm-12 col-xs-12'>
						<div class='col-sm-12' style='width:100%;border:1px dotted #aaa; background-color:#dedede;'><br>
							<div class='col-sm-12'>
								<div class='col-sm-3'>	
									<div class='form-group'>
										เลขตัวถัง
										<input type='text' id='STRNO' class='form-control input-sm' readonly>
									</div>
								</div>
								<div class='col-sm-3'>	
									<div class='form-group'>
										เลขที่ทะเบียน
										<input type='text' id='REGNO' class='form-control input-sm' readonly>
									</div>
								</div>
								<div class='col-sm-3'>	
									<div class='form-group'>
										เลขอ้างอิง
										<input type='text' id='REFNO' class='form-control input-sm' readonly>
									</div>
								</div>
								<div class='col-sm-3'>	
									<div class='form-group'>
										เลขกุญแจ
										<input type='text' id='KEYNO' class='form-control input-sm' readonly>
									</div>
								</div>
								
								<div class='col-sm-2'>	
									<div class='form-group'>
										รหัสลูกค้า
										<input type='text' value='' id='CUSCOD' class='form-control input-sm' readonly>
									</div>
								</div>
								<div class='col-sm-3'>	
									<div class='form-group'>
										<br>
										<input type='text' id='NAME1' class='form-control input-sm' readonly>
									</div>
								</div>
								<div class='col-sm-3'>	
									<div class='form-group'>
										<br>
										<input type='text' id='NAME2' class='form-control input-sm' readonly>
									</div>
								</div>
								<div class='col-sm-2'>	
									<div class='form-group'>
										เลขที่สัญญา
										<input type='text' id='CONTNO' class='form-control input-sm' readonly>
									</div>
								</div>
								<div class='col-sm-2'>	
									<div class='form-group'>
										ประเภทการขาย
										<input type='text' id='TSALE' class='form-control input-sm' readonly>
									</div>
								</div>
							</div>
						</div>
						<div style='height:170px;'></div>
						<div class='col-sm-12' style='width:100%;border:1px dotted #aaa; background-color:#dedede;'><br>
							<div class='col-sm-12'>
								<div class='col-sm-2'>	
									<div class='form-group'>
										ยี่ห้อ
										<input type='text' id='TYPE' class='form-control input-sm' readonly>
									</div>
								</div>
								<div class='col-sm-2'>	
									<div class='form-group'>
										สถานะปัจจุบัน
										<input type='text' id='STATNOW' class='form-control input-sm' readonly>
									</div>
								</div>
								<div class='col-sm-2'>	
									<div class='form-group'>
										เลขที่ใบกำกับ
										<input type='text' id='TAXNO' class='form-control input-sm' readonly>
									</div>
								</div>
								<div class='col-sm-2'>	
									<div class='form-group'>
										รุ่น
										<input type='text' id='MODEL' class='form-control input-sm' readonly>
									</div>
								</div>
								<div class='col-sm-2'>	
									<div class='form-group'>
										สถานะ
										<input type='text' id='STAT' class='form-control input-sm' readonly>
									</div>
								</div>
								<div class='col-sm-2'>	
									<div class='form-group'>
										วันที่ใบกำกับ
										<input type='text' id='TAXDT' class='form-control input-sm' readonly>
									</div>
								</div>
								<div class='col-sm-2'>	
									<div class='form-group'>
										แบบ
										<input type='text' id='BAAB' class='form-control input-sm' readonly>
									</div>
								</div>
								<div class='col-sm-2'>	
									<div class='form-group'>
										เลขที่ใบรับ
										<input type='text' id='RECVNO' class='form-control input-sm' readonly>
									</div>
								</div>
								<div class='col-sm-2'>	
									<div class='form-group'>
										บริษัทเจ้าหนี้
										<input type='text' id='APCODE' class='form-control input-sm' readonly>
									</div>
								</div>
								<div class='col-sm-2'>	
									<div class='form-group'>
										สี
										<input type='text' id='COLOR' class='form-control input-sm' readonly>
									</div>
								</div>
								<div class='col-sm-2'>	
									<div class='form-group'>
										วันที่รับ
										<input type='text' id='RECVDT' class='form-control input-sm' readonly>
									</div>
								</div>
								<div class='col-sm-2'>	
									<div class='form-group'>
										เลขเครื่อง
										<input type='text' id='ENGNO' class='form-control input-sm' readonly>
									</div>
								</div>
								<div class='col-sm-2'>	
									<div class='form-group'>
										ขนาด
										<input type='text' id='CC' class='form-control input-sm' readonly>
									</div>
								</div>
								<div class='col-sm-2'>	
									<div class='form-group'>
										สถานที่รับ
										<input type='text' id='RVLOCAT' class='form-control input-sm' readonly>
									</div>
								</div>
								<div class='col-sm-2'>	
									<div class='form-group'>
										สถานที่เก็บ
										<input type='text' id='CRLOCAT' class='form-control input-sm' readonly>
									</div>
								</div>
								<div class='col-sm-6'>
									<div class='form-group'>
										กลุ่มรถ
										<input style='width:50%;' type='text' id='GCODE' class='form-control input-sm' readonly>
									</div>
								</div>
								<div class='col-sm-2'>	
									<div class='form-group'>
										ค่าซ่อม
										<input style='text-align:right;' type='text' id='NADDCOST' class='form-control input-sm text-danger' readonly>
									</div>
								</div>
								<div class='col-sm-2'>	
									<div class='form-group'>
										ภาษีค่าซ่อม
										<input style='text-align:right;' type='text' id='VADDCOST' class='form-control input-sm text-danger' readonly>
									</div>
								</div>
								<div class='col-sm-2'>	
									<div class='form-group'>
										รวมค่าซ่อม
										<input style='text-align:right;' type='text' id='TADDCOST' class='form-control input-sm text-danger' readonly>
									</div>
								</div>
								<!--div class='col-sm-8'></div-->
								<div class='col-sm-6' style='align:right;'>	
									<div class='form-group'>
										รวมต้นทุนรถ
										<input style='text-align:right;width:50%;' type='text' id='TOTCOSTCAR' class='form-control input-sm' readonly>
									</div>
								</div>
							</div>
						</div>
					</div>
				</fieldset>
			</div>	
		";
		return $html;
	}
	function getTab22(){
		$html = "
			<div class='tab-pane' name='tab22' style='height:calc(100vh - 290px);overflow:auto;'>
				<fieldset style='height:100%'>
					<div style='float:left;height:90%;' class='col-sm-12 col-xs-12'>
						<div class='col-sm-12' style='height:100%;'>
							<div class='row' style='height:100%;border:0.1px solid #bdbdbd;background-color:#eee;'>
								<div class='col-sm-12 col-xs-12' style='height:100%;'>
									<div id='dataTable-fixed-listservice' class='dataTables_wrapper dt-bootstrap4 table-responsive' style='height:100%;width:100%;overflow:auto;'>
										<table id='dataTables-listservice' class='table table-bordered dataTable table-hover' stat='' aria-describedby='dataTable_info' cellspacing='0' width='calc(100% - 1px)'>
											<thead>
												<tr role='row' style='height:30px;font-size:8pt;background-color:#117a65;color:white;'>
													<th width='12.5%' style='vertical-align:middle;color:#f4d03f;'>#</th>
													<th width='12.5%' style='vertical-align:middle;color:#f4d03f;'>วันที่ JOB</th>
													<th width='12.5%' style='vertical-align:middle;color:#f4d03f;'>รหัสสาขา</th>
													<th width='12.5%' style='vertical-align:middle;color:#f4d03f;'>ข้อมูลต้นทุน</th>
													<th width='12.5%' style='vertical-align:middle;color:#f4d03f;'>ภาษี</th>
													<th width='12.5%' style='vertical-align:middle;color:#f4d03f;'>ยอดรวมภาษี</th>
													<th width='12.5%' style='vertical-align:middle;color:#f4d03f;'>รหัสพนักงาน</th>
													<th width='12.5%' style='vertical-align:middle;color:#f4d03f;'>รหัสช่าง</th>
													<th width='12.5%' style='vertical-align:middle;color:#f4d03f;'>ผู้ยกเลิก</th>
												</tr>
											</thead>
											<tbody style='white-space:nowrap;background-color:white;font-size:9pt;'></tbody>
										</table>
									</div>
								</div>
							</div>
						</div>
					</div><br>
					<div class='col-sm-12'>
						<div class='col-sm-6'></div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								<input type='text' id='COSTAMT' style='text-align:right;' class='form-control input-sm' disabled>
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								<input type='text' id='VATAMT' style='text-align:right;' class='form-control input-sm' disabled>
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								<input type='text' id='TOTCOST' style='text-align:right;' class='form-control input-sm' disabled>
							</div>
						</div>
					</div>
				</fieldset>
			</div>";
		return $html;
	}
	function getTab33(){
		$html = "
			<div class='tab-pane' name='tab33' style='height:calc(100vh - 290px);overflow:auto;'>
				<fieldset style='height:100%'>
					<div style='float:left;height:100%;' class='col-sm-12 col-xs-12'>
						<div class='col-sm-12' style='height:100%;'>
							<div class='row' style='height:100%;border:0.1px solid #bdbdbd;background-color:#eee;'>
								<div class='col-sm-12 col-xs-12' style='height:100%;'>
									<div id='dataTable-fixed-listmove' class='dataTables_wrapper dt-bootstrap4 table-responsive' style='height:100%;width:100%;overflow:auto;'>
										<table id='dataTables-listmove' class='table table-bordered dataTable table-hover' stat='' aria-describedby='dataTable_info' cellspacing='0' width='calc(100% - 1px)'>
											<thead>
												<tr role='row' style='height:30px;font-size:8pt;background-color:#117a65;color:white;'>
													<th width='12.5%' style='vertical-align:middle;color:#f4d03f;'>หมายเลขตัวถัง</th>
													<th width='12.5%' style='vertical-align:middle;color:#f4d03f;'>ลำดับการโอนย้าย</th>
													<th width='12.5%' style='vertical-align:middle;color:#f4d03f;'>เลขที่ใบโอนย้าย</th>
													<th width='12.5%' style='vertical-align:middle;color:#f4d03f;'>โอนจากสาขา</th>
													<th width='12.5%' style='vertical-align:middle;color:#f4d03f;'>ไปยังสาขา</th>
													<th width='12.5%' style='vertical-align:middle;color:#f4d03f;'>วันที่โอนย้าย</th>
													<th width='12.5%' style='vertical-align:middle;color:#f4d03f;'>วันเวลาทำรายการ</th>
												</tr>
											</thead>
											<tbody style='white-space:nowrap;background-color:white;font-size:9pt;'></tbody>
										</table>
									</div>
								</div>
							</div>
						</div>
					</div>
				</fieldset>
			</div>";
		return $html;
	}
	function Searchstockbystrno(){
		$response = array();
		$STRNO  = $_REQUEST['STRNO'];
		if($STRNO == ""){
			$response["error"] = true;
			$response["msg"] = "ไม่พบเลขตัวถัง กรุณาระบุเลขที่ตัวถังก่อนครับ";
			echo json_encode($response); exit;	
		}
		$sql = "
			select CUSCOD,NAME1,NAME2 from {$this->MAuth->getdb('CUSTMAST')}
			where CUSCOD = (
				select CUSCOD from {$this->MAuth->getdb('ARMAST')} 
				where STRNO = '".$STRNO."'
				union
				select CUSCOD from {$this->MAuth->getdb('ARCRED')} 
				where STRNO = '".$STRNO."'
			) 
		";
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$response['CUSCOD'] = $row->CUSCOD;
				$response['NAME1']  = $row->NAME1;
				$response['NAME2']  = $row->NAME2;
			}
		}
		$sql = "
			select I.CONTNO,I.STRNO,I.REGNO,I.REFNO,I.KEYNO,'('+S.GCODE+') '+S.GDESC as GCODE			
				,case	
					when I.TSALE = 'A' then 'ขายส่ง'
					when I.TSALE = 'C' then 'ขายสด'
					when I.TSALE = 'H' then 'ขายผ่อน'
					when I.TSALE = 'F' then 'ขายไฟแนนซ์'
					else ''
				end as TSALE
				,I.TYPE,I.MODEL,I.BAAB,I.COLOR,I.CC
				,case 
					when I.TSALE = '' then 'รถในสต๊อก'
					when I.TSALE = 'C' and I.FLAG = 'D' then 'รถในสต๊อก'
					when I.CURSTAT = 'Y' and I.TSALE <> '' then 'รถยึด'
					else 'ปกติ' 
				  end as STATNOW
				,case when I.STAT = 'N' then 'รถใหม่' else 'รถเก่า' end as STATT
				,I.RECVNO,CONVERT(varchar(8),I.RECVDT,112) as RECVDT,RVLOCAT
				,N.TAXNO,CONVERT(varchar(8),N.TAXDT,112) as TAXDT,N.APCODE,I.ENGNO,I.CRLOCAT 
				,I.NADDCOST,I.VADDCOST,I.TADDCOST,I.TOTCOST + I.TADDCOST as TOTCOSTCAR
			from {$this->MAuth->getdb('INVTRAN')} I 
			left join {$this->MAuth->getdb('INVINVO')} N on I.RECVNO = N.RECVNO 
			left join {$this->MAuth->getdb('SETGROUP')} S on I.GCODE = S.GCODE
			where RTRIM(I.STRNO) like '".$STRNO."%'
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$response["STRNO"]  =  str_replace(chr(0),"",$row->STRNO);
				$response["REGNO"]  =  str_replace(chr(0),"",$row->REGNO);
				$response["REFNO"]  =  str_replace(chr(0),"",$row->REFNO);
				$response["KEYNO"]  =  str_replace(chr(0),"",$row->KEYNO);
				$response["CONTNO"] =  str_replace(chr(0),"",$row->CONTNO);
				$response["TSALE"]  =  str_replace(chr(0),"",$row->TSALE);
				$response["TYPE"]   =  str_replace(chr(0),"",$row->TYPE);
				$response["MODEL"]  =  str_replace(chr(0),"",$row->MODEL);
				$response["BAAB"]   =  str_replace(chr(0),"",$row->BAAB);
				$response["COLOR"]  =  str_replace(chr(0),"",$row->COLOR);
				$response["CC"]     =  str_replace(chr(0),"",$row->CC);
				$response["STATNOW"]=  str_replace(chr(0),"",$row->STATNOW);
				$response["STAT"]   =  $row->STATT;
				$response["RECVNO"] =  str_replace(chr(0),"",$row->RECVNO);
				$response["RECVDT"] =  $this->Convertdate(2,$row->RECVDT);
				$response["RVLOCAT"]=  str_replace(chr(0),"",$row->RVLOCAT);
				$response["TAXNO"]  =  str_replace(chr(0),"",$row->TAXNO);
				$response["TAXDT"]  =  $this->Convertdate(2,$row->TAXDT);
				$response["APCODE"]     =  str_replace(chr(0),"",$row->APCODE);
				$response["ENGNO"]      =  $row->ENGNO;
				$response["CRLOCAT"]    =  $row->CRLOCAT;
				$response["NADDCOST"]   =  number_format($row->NADDCOST,2);
				$response["VADDCOST"]   =  number_format($row->VADDCOST,2);
				$response["TADDCOST"]   =  number_format($row->TADDCOST,2);
				$response["TOTCOSTCAR"] =  number_format($row->TOTCOSTCAR,2);
				$response["GCODE"]      =  $row->GCODE;
			}
		}
		$sql = "
			select 
				CONVERT(varchar(8),JOBDATE,112) as JOBDATE,LOCAT,COSTAMT
				,VATAMT,TOTCOST,OFFICCOD,SERVICCOD,CANCELID,CANCELDT
			from {$this->MAuth->getdb('JOBTRAN')} 
			where STRNO = '".$STRNO."'
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		$listservic = ""; $i = 0;
		if($query->row()){
			foreach($query->result() as $row){$i++;
				$css = "";
				if($row->CANCELDT !== "" and $row->CANCELDT !== null){
					$css = "color:red;";
				}
				$listservic .="
					<tr class='trow' style='{$css}height:20px;'>
						<td style='vertical-align:middle;'>".$i."</td>
						<td style='vertical-align:middle;'>".$this->Convertdate(2,$row->JOBDATE)."</td>
						<td style='vertical-align:middle;'>".$row->LOCAT."</td>
						<td style='vertical-align:middle;'>".number_format($row->COSTAMT,2)."</td>
						<td style='vertical-align:middle;'>".number_format($row->VATAMT,2)."</td>
						<td style='vertical-align:middle;'>".number_format($row->TOTCOST,2)."</td>
						<td style='vertical-align:middle;'>".$row->OFFICCOD."</td>
						<td style='vertical-align:middle;'>".$row->SERVICCOD."</td>
						<td style='vertical-align:middle;'>".$row->CANCELID."</td>
					</tr>
				";
			}
		}else{
			$listservic = "<tr class='trow'><td colspan='9' style='color:red;height:100%;'>ไม่มีข้อมูล</td></tr>";
		}
		$response['listservic'] = $listservic;
		$sql = "
			select isnull(sum(COSTAMT),0) as COSTAMT
				,isnull(sum(VATAMT),0) as VATAMT
				,isnull(sum(TOTCOST),0) as TOTCOST 
			from {$this->MAuth->getdb('JOBTRAN')} 
			where CANCELDT is null and STRNO = '".$STRNO."'
		";
		//echo $sql; exit;
		$querys = $this->db->query($sql);
		$rown = $querys->row();
		$response["COSTAMT"] = number_format($rown->COSTAMT,2);
		$response["VATAMT"]  = number_format($rown->VATAMT,2);
		$response["TOTCOST"] = number_format($rown->TOTCOST,2);
		
		$sql = "
			select 
				STRNO,MOVSEQ,MOVENO,MOVEFM,MOVETO,CONVERT(varchar(8),MOVEDT,112) as MOVEDT
				,CONVERT(varchar(8),INPDT,112) as INPDT 
			from {$this->MAuth->getdb('INVMOVT')} 
			where STRNO = '".$STRNO."' order by STRNO,MOVSEQ
		";
		$query = $this->db->query($sql);
		$listmove = "";
		if($query->row()){
			foreach($query->result() as $row){
				$listmove .="
					<tr class='trow' style='height:20px;'>
						<td style='vertical-align:middle;'>".$row->STRNO."</td>
						<td style='vertical-align:middle;'>".$row->MOVSEQ."</td>
						<td style='vertical-align:middle;'>".$row->MOVENO."</td>
						<td style='vertical-align:middle;'>".$row->MOVEFM."</td>
						<td style='vertical-align:middle;'>".$row->MOVETO."</td>
						<td style='vertical-align:middle;'>".$this->Convertdate(2,$row->MOVEDT)."</td>
						<td style='vertical-align:middle;'>".$this->Convertdate(2,$row->INPDT)."</td>
					</tr>
				";
			}
		}else{
			$listmove = "<tr class='trow'><td colspan='7' style='color:red;height:100%;'>ไม่มีข้อมูล</td></tr>";
		}
		$response['listmove'] = $listmove;
		echo json_encode($response);
	}
}