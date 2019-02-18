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
class CReport extends MY_Controller {
	private $sess = array();
	
	function __construct(){
		parent::__construct();
		
		$sess = $this->session->userdata('cbjsess001');
		if(!$sess){ redirect(base_url("welcome/"),"_parent"); }else{
			foreach ($sess as $key => $value) {
                $this->sess[$key] = $value;
            }
		}
		
		$this->load->model('MDATA');
	}
	
	function Transfers(){
		$claim = $this->MLogin->getclaim(uri_string());
		if($claim['m_access'] != "T"){ echo "<div align='center' style='color:red;font-size:16pt;width:100%;'>ขออภัย คุณยังไม่มีสิทธิเข้าใช้งานหน้านี้ครับ</div>"; exit; }
		
		$html = "
			<div class='tab1' name='home' locat='{$this->sess['branch']}' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}' style='height:calc(100vh - 132px);overflow:auto;background-color:white;'>
				<div>					
					<div class='col-xs-6 col-sm-3'>	
						<div class='form-group'>
							เลขที่โอน
							<input type='text' id='TRANSNO' class='form-control input-sm' placeholder='เลขที่โอน'>
						</div>
					</div>
					<div class='col-xs-6 col-sm-3'>	
						<div class='form-group'>
							วันที่สร้างบิลโอน จาก
							<input type='text' id='TRANSDTs' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' placeholder='วันที่โอน'>
						</div>
					</div>
					<div class='col-xs-6 col-sm-3'>	
						<div class='form-group'>
							วันที่สร้างบิลโอน ถึง
							<input type='text' id='TRANSDTe' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' placeholder='วันที่โอน'>
						</div>
					</div>
					<div class='col-xs-6 col-sm-3'>	
						<div class='form-group'>
							โอนจากสาขา
							<input type='text' id='TRANSFM' class='form-control input-sm' placeholder='โอนจากสาขา' value='".$this->sess['branch']."'>
						</div>
					</div>
					<div class='col-xs-6 col-sm-3'>	
						<div class='form-group'>
							สถานะ
							<select id='TRANSSTAT' class='form-control input-sm chosen-select' data-placeholder='สถานะ'>
								<option value='' selected>ทุกสถานะ</option>
								<option value='Sendding'>อยู่ระหว่างการโอนย้ายรถ</option>
								<option value='Pendding'>รับโอนรถบางส่วน</option>
								<option value='Received'>รับโอนรถครบแล้ว</option>
							</select>
						</div>
					</div>
					<div class='col-xs-6 col-sm-3'>	
						<div class='form-group'>
							ประเภทการโอน
							<select id='TRANSSYS' class='form-control input-sm chosen-select' data-placeholder='สถานะ'>
								<option value='' selected>ทั้งหมด</option>
								<option value='MT'>โอนเอง</option>
								<option value='AT'>อัตโนมัติ</option>
							</select>
						</div>
					</div>
					<div class='col-xs-12 col-sm-12'>	
						<div class='form-group'>
							<br>
							<input type='button' id='btnt1transfer' class='btn btn-primary btn-sm' value='แสดง' style='width:100%'>
						</div>
					</div>
				</div>
				<!-- div id='resultt1transfer' style='height:calc(100% - 65px);overflow:auto;background-color:white;'></div -->
			</div>
		";

		$html.= "<script src='".base_url('public/js/SYS02/CReport/Transfers.js')."'></script>";
		echo $html;		
	}
	
	function TransfersSearch(){
		$arrs = array();
		$arrs["TRANSNO"] = $_REQUEST["TRANSNO"];
		$arrs["TRANSDTs"] = $this->Convertdate(1,$_REQUEST["TRANSDTs"]);
		$arrs["TRANSDTe"] = $this->Convertdate(1,$_REQUEST["TRANSDTe"]);
		$arrs["TRANSFM"] = $_REQUEST["TRANSFM"];
		$arrs["TRANSSTAT"] = $_REQUEST["TRANSSTAT"];
		$arrs["TRANSSYS"] = $_REQUEST["TRANSSYS"];
		
		$cond = "";
		if($arrs["TRANSNO"] != ""){
			$cond .= " and a.TRANSNO like '".$arrs["TRANSNO"]."%' ";
		}		
		
		if($arrs["TRANSDTs"] != "" and $arrs["TRANSDTe"] != ""){
			$cond .= " and convert(varchar(8),a.TRANSDT,112) between '".$arrs["TRANSDTs"]."' and '".$arrs["TRANSDTe"]."' ";
		}else if($arrs["TRANSDTs"] != "" and $arrs["TRANSDTe"] == ""){
			$cond .= " and convert(varchar(8),a.TRANSDT,112) = '".$arrs["TRANSDTs"]."' ";
		}else if($arrs["TRANSDTs"] == "" and $arrs["TRANSDTe"] != ""){
			$cond .= " and convert(varchar(8),a.TRANSDT,112) = '".$arrs["TRANSDTe"]."' ";
		}
		
		$cond .= " and a.TRANSFM like '%".$arrs["TRANSFM"]."%'";
		
		if($arrs["TRANSSTAT"] != ""){
			$cond .= " and a.TRANSSTAT = '".$arrs["TRANSSTAT"]."' ";
		}		
		
		if($arrs["TRANSSYS"] != ""){
			$cond .= " and a.SYSTEM = '".$arrs["TRANSSYS"]."' ";
		}		
		
		
		$sql = "
			select a.TRANSNO,a.TRANSFM,a.TRANSTO,b.TRANSITEM,b.STRNO
				,c.titleName+c.firstName+' '+c.lastName as EMPCARRY
				,convert(varchar(8),a.TRANSDT,112) as TRANSDTCreate
				,convert(varchar(8),b.TRANSDT,112) as TRANSDT
				,e.titleName+e.firstName+' '+e.lastName as RECEIVEBY,convert(varchar(8),b.RECEIVEDT,112) as RECEIVEDT
				,d.titleName+d.firstName+' '+d.lastName as INSERTBY
				,convert(varchar(8),b.INSERTDT,112) as INSERTDT 
				,convert(varchar(5),b.INSERTDT,108) as INSERTDTTime 
				,a.SYSTEM
			from {$this->MAuth->getdb('INVTransfers')} a
			left join {$this->MAuth->getdb('INVTransfersDetails')} b on a.TRANSNO=b.TRANSNO
			left join {$this->MAuth->getdb('hp_vusers')} c on c.IDNo=b.EMPCARRY collate thai_cs_as
			left join {$this->MAuth->getdb('hp_vusers')} d on d.IDNo=b.INSERTBY collate thai_cs_as
			left join {$this->MAuth->getdb('hp_vusers')} e on e.IDNo=b.RECEIVEBY collate thai_cs_as
			where 1=1 ".$cond."
			order by a.TRANSNO,b.TRANSITEM
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		$NRow = 1;
		if($query->row()){
			foreach($query->result() as $row){
				$css = "color:black;";
				if($row->RECEIVEDT != ""){
					$css = "color:blue;";
				}else if($row->TRANSDT != "" and $row->RECEIVEDT == ""){
					$css = "color:red;";
				}
				$html .= "
					<tr style='".$css."'>
						<td>".$NRow++."</td>
						<td>".$row->TRANSNO."</td>
						<td>".$this->Convertdate(2,$row->TRANSDTCreate)."</td>						
						<td>".$row->TRANSFM."</td>
						<td>".$row->TRANSTO."</td>
						<td>".$row->TRANSITEM."</td>
						<td>".$row->STRNO."</td>
						<td>".$row->EMPCARRY."</td>
						<td>".$this->Convertdate(2,$row->TRANSDT)."</td>
						<td>".$row->RECEIVEBY."</td>
						<td>".$this->Convertdate(2,$row->RECEIVEDT)."</td>
						<td>".$row->SYSTEM."</td>
						<td>".$row->INSERTBY."</td>
						<td>".$this->Convertdate(2,$row->INSERTDT)." ".$row->INSERTDTTime."</td>
					</tr>
				";
			}
		}
		
		$html = "
			<div id='table-fixed-TransfersSearch' class='col-sm-12' style='height:calc(100% - 25px);width:100%;overflow:auto;font-size:8pt;'>
				<table id='table-TransfersSearch' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='100%'>
					<thead>
						<tr>
							<th colspan='14' class='text-center' style='font-size:12pt;border:0px;'> 
								รายงานการโอนย้ายรถ
							</th>
						</tr>
						<tr>
							<th colspan='14' class='text-center' style='border:0px;'>
								ออกรายงานโดย ".$this->sess["name"]." &emsp; ณ วันที่ ".$this->MDATA->sysdt()."
							</th>
						</tr>
						<tr>
							<th style='vertical-align:middle;border:0px;'>#</th>
							<th style='vertical-align:middle;border:0px;'>เลขที่โอน</th>
							<th style='vertical-align:middle;border:0px;'>วันที่สร้างบิลโอน</th>
							<th style='vertical-align:middle;border:0px;'>สาขาต้นทาง</th>
							<th style='vertical-align:middle;border:0px;'>สาขาปลายทาง</th>
							<th style='vertical-align:middle;border:0px;'>ลำดับการโอน</th>
							<th style='vertical-align:middle;border:0px;'>เลขตัวถัง</th>
							<th style='vertical-align:middle;border:0px;'>พขร.</th>
							<th style='vertical-align:middle;border:0px;'>วันที่โอน</th>
							<th style='vertical-align:middle;border:0px;'>ผู้รับโอน</th>
							<th style='vertical-align:middle;border:0px;'>วันที่รับโอน</th>
							<th style='vertical-align:middle;border:0px;'>ระบบ</th>
							<th style='vertical-align:middle;border:0px;'>ผู้โอนย้าย</th>
							<th style='vertical-align:middle;border:0px;'>วันที่บันทึกโอน</th>
						</tr>
					</thead>	
					<tbody style='height: 10px !important; overflow: scroll;'>
						".$html."
					</tbody>					
				</table>
			</div>
			<div>
				<img src='".base_url("/public/images/excel.png")."'  onclick=\"tableToExcel('table-TransfersSearch', 'รายงานการโอนย้ายรถ');\" style='width:30px;height:30px;cursor:pointer;'/>
			</div>
		";
		
		$response = array("html"=>$html);
		echo json_encode($response);
	}
	
	public function TransfersPendding(){
		$claim = $this->MLogin->getclaim(uri_string());
		if($claim['m_access'] != "T"){ echo "<div align='center' style='color:red;font-size:16pt;width:100%;'>ขออภัย คุณยังไม่มีสิทธิเข้าใช้งานหน้านี้ครับ</div>"; exit; }
		
		$html = "
			<div class='tab1' name='home' locat='{$this->sess['branch']}' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}' style='height:calc(100vh - 132px);overflow:auto;background-color:white;'>
				<div>					
					<div class='col-xs-6 col-sm-3'>	
						<div class='form-group'>
							วันที่สร้างบิลโอน จาก
							<input type='text' id='TRANSDTs' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' placeholder='วันที่โอน'>
						</div>
					</div>
					<div class='col-xs-6 col-sm-3'>	
						<div class='form-group'>
							วันที่สร้างบิลโอน ถึง
							<input type='text' id='TRANSDTe' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' placeholder='วันที่โอน'>
						</div>
					</div>
					<div class='col-xs-6 col-sm-3'>	
						<div class='form-group'>
							สาขาต้นทาง
							<input type='text' id='TRANSFM' class='form-control input-sm' placeholder='โอนจากสาขา' value=''>
						</div>
					</div>
					<div class='col-xs-6 col-sm-3'>	
						<div class='form-group'>
							สาขาปลายทาง
							<input type='text' id='TRANSTO' class='form-control input-sm' placeholder='โอนจากสาขา' value=''>
						</div>
					</div>
					<div class='col-xs-6 col-sm-3'>	
						<div class='form-group'>
							สถานะ
							<select id='TRANSSTAT' class='form-control input-sm chosen-select' data-placeholder='สถานะ'>
								<option value='' selected>ทุกสถานะ</option>
								<option value='hasTRANSDT'>ระบุวันที่โอนแล้ว</option>
								<option value='notTRANSDT'>ยังไม่ระบุวันที่โอน</option>
							</select>
						</div>
					</div>
					<div class='col-xs-12 col-sm-12'>	
						<div class='form-group'>
							<br>
							<input type='button' id='btnt1transferPendding' class='btn btn-primary btn-sm' value='แสดง' style='width:100%'>
						</div>
					</div>
				</div>
			</div>
		";

		$html.= "<script src='".base_url('public/js/SYS02/CReport/TransfersPendding.js')."'></script>";
		echo $html;	
	}
	
	public function TransfersPenddingSearch(){
		$arrs = array();
		$arrs["TRANSDTs"] = $this->Convertdate(1,$_REQUEST["TRANSDTs"]);
		$arrs["TRANSDTe"] = $this->Convertdate(1,$_REQUEST["TRANSDTe"]);
		$arrs["TRANSFM"] = $_REQUEST["TRANSFM"];
		$arrs["TRANSTO"] = $_REQUEST["TRANSTO"];
		$arrs["TRANSSTAT"] = $_REQUEST["TRANSSTAT"];		
		
		$cond = "";
		
		if($arrs["TRANSDTs"] != "" and $arrs["TRANSDTe"] != ""){
			$cond .= " and convert(varchar(8),a.TRANSDT,112) between '".$arrs["TRANSDTs"]."' and '".$arrs["TRANSDTe"]."' ";
		}else if($arrs["TRANSDTs"] != "" and $arrs["TRANSDTe"] == ""){
			$cond .= " and convert(varchar(8),a.TRANSDT,112) = '".$arrs["TRANSDTs"]."' ";
		}else if($arrs["TRANSDTs"] == "" and $arrs["TRANSDTe"] != ""){
			$cond .= " and convert(varchar(8),a.TRANSDT,112) = '".$arrs["TRANSDTe"]."' ";
		}
		
		$cond .= " and a.TRANSFM like '%".$arrs["TRANSFM"]."%' and a.TRANSTO like '%".$arrs["TRANSTO"]."%'";
		
		if($arrs["TRANSSTAT"] == "hasTRANSDT"){
			$cond .= " and b.EMPCARRY is not null and b.TRANSDT is not null ";
		}else if($arrs["TRANSSTAT"] == "notTRANSDT"){
			$cond .= " and b.EMPCARRY is null and b.TRANSDT is null ";
		}
		
		$sql = "
			select a.TRANSNO,b.STRNO,a.TRANSFM,a.TRANSTO
				,convert(varchar(8),b.TRANSDT,112) as TRANSDT
				,c.titleName+c.firstName+' '+c.lastName as EMPCARRY 
				,a.SYSTEM
			from INVTransfers a
			left join INVTransfersDetails b on a.TRANSNO=b.TRANSNO
			left join {$this->MAuth->getdb('hp_vusers')} c on c.IDNo=b.EMPCARRY collate thai_cs_as
			where b.MOVENO is null and b.RECEIVEBY is null and b.RECEIVEDT is null ".$cond."
			order by b.TRANSDT 
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		$NRow = 1;
		if($query->row()){
			foreach($query->result() as $row){
				$css = "color:black;";
				/*
				if($row->RECEIVEDT != ""){
					$css = "color:blue;";
				}else if($row->TRANSDT != "" and $row->RECEIVEDT == ""){
					$css = "color:red;";
				}
				*/
				$html .= "
					<tr style='".$css."'>
						<td>".$NRow++."</td>
						<td>".$row->STRNO."</td>
						<td>".$row->TRANSNO."</td>
						<td>".$row->TRANSFM."</td>
						<td>".$row->TRANSTO."</td>
						<td>".$row->EMPCARRY."</td>
						<td>".$this->Convertdate(2,$row->TRANSDT)."</td>
						<td>".$row->SYSTEM."</td>
					</tr>
				";
			}
		}
		
		$html = "
			<div id='table-fixed-TransfersPenddingSearch' class='col-sm-12' style='height:calc(100% - 25px);width:100%;overflow:auto;font-size:8pt;'>
				<table id='table-TransfersPenddingSearch' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='100%'>
					<thead>
						<tr>
							<th colspan='8' class='text-center' style='font-size:12pt;border:0px;'> 
								รายงานรถที่อยู่ระหว่างการโอนย้าย
							</th>
						</tr>
						<tr>
							<th colspan='8' class='text-center' style='border:0px;'>
								ออกรายงานโดย ".$this->sess["name"]." &emsp; ณ วันที่ ".$this->MDATA->sysdt()."
							</th>
						</tr>
						<tr>
							<th style='vertical-align:middle;border:0px;'>#</th>
							<th style='vertical-align:middle;border:0px;'>เลขตัวถัง</th>
							<th style='vertical-align:middle;border:0px;'>เลขที่บิลโอน</th>
							<th style='vertical-align:middle;border:0px;'>สาขาต้นทาง</th>
							<th style='vertical-align:middle;border:0px;'>สาขาปลายทาง</th>
							<th style='vertical-align:middle;border:0px;'>พขร.</th>
							<th style='vertical-align:middle;border:0px;'>วันที่โอน</th>
							<th style='vertical-align:middle;border:0px;'>ระบบ</th>
						</tr>
					</thead>	
					<tbody style='height: 10px !important; overflow: scroll;'>
						".$html."
					</tbody>					
				</table>
			</div>
			<div>
				<img src='".base_url("/public/images/excel.png")."'  onclick=\"tableToExcel('table-TransfersPenddingSearch', 'รายงานรถที่อยู่ระหว่างการโอนย้าย');\" style='width:30px;height:30px;cursor:pointer;'/>
			</div>
		";
		
		$response = array("html"=>$html);
		echo json_encode($response);
	}
}




















