<?php
error_reporting(E_STRICT);
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
					<div class='col-xs-6 col-sm-4'>	
						<div class='form-group'>
							เลขที่บิลโอน
							<input type='text' id='TRANSNO' class='form-control input-sm' placeholder='เลขที่โอน'>
						</div>
					</div>
					<div class='col-xs-6 col-sm-4'>	
						<div class='form-group'>
							วันที่สร้างบิลโอน จาก
							<input type='text' id='TRANSDTs' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' placeholder='วันที่โอน'>
						</div>
					</div>
					<div class='col-xs-6 col-sm-4'>	
						<div class='form-group'>
							วันที่สร้างบิลโอน ถึง
							<input type='text' id='TRANSDTe' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' placeholder='วันที่โอน'>
						</div>
					</div>
					<div class='col-xs-6 col-sm-4'>	
						<div class='form-group'>
							สาขาต้นทาง
							<input type='text' id='TRANSFM' class='form-control input-sm' placeholder='สาขาต้นทาง' value=''>
						</div>
					</div>
					<div class='col-xs-6 col-sm-4'>	
						<div class='form-group'>
							สาขาปลายทาง
							<input type='text' id='TRANSTO' class='form-control input-sm' placeholder='สาขาปลายทาง' value=''>
						</div>
					</div>
					<div class='col-xs-6 col-sm-4'>	
						<div class='form-group'>
							สถานะบิลโอน
							<select id='TRANSSTAT' class='form-control input-sm chosen-select' data-placeholder='สถานะ'>
								<optgroup label='สถานะบิลโอน'>
									<option value='' selected>ทุกสถานะ</option>
									<option value='Sendding'>อยู่ระหว่างการโอนย้ายรถ</option>
									<option value='Pendding'>รับโอนรถบางส่วน</option>
									<option value='Received'>รับโอนรถครบแล้ว</option>
								</optgroup>
								<optgroup label='สถานะรถ'>
									<option value='Sendding2'>อยู่ระหว่างการโอนย้ายรถ</option>
									<option value='Received2'>รับโอนแล้ว</option>
								</optgroup>
							</select>
						</div>
					</div>
					<div class='col-xs-6 col-sm-4'>	
						<div class='form-group'>
							สถานะโอน (รายคัน)
							<select id='TRANSSTAT2' class='form-control input-sm chosen-select' data-placeholder='สถานะ'>
								<option value='' selected>ทุกสถานะ</option>
								<option value='hasTRANSDT'>ระบุวันที่โอน และพขร.แล้ว</option>
								<option value='notTRANSDT'>ยังไม่ระบุวันที่โอน และพขร.</option>
							</select>
						</div>
					</div>
					<div class='col-xs-6 col-sm-4'>	
						<div class='form-group'>
							ระบบ
							<select id='TRANSSYS' class='form-control input-sm chosen-select' data-placeholder='สถานะ'>
								<option value='' selected>ทั้งหมด</option>
								<option value='MT'>โอนเอง</option>
								<option value='AT'>อัตโนมัติ</option>
							</select>
						</div>
					</div>
					<div class='col-xs-6 col-sm-4'>	
						<div class='form-group'>
							เลขตัวถัง
							<input type='text' id='STRNO' class='form-control input-sm' placeholder='เลขตัวถัง' value=''>
						</div>
					</div>
					
					
					<div class='col-xs-6 col-sm-4'>	
						<div class='form-group'>
							จำนวนวันที่สร้าง - วันที่โอน
							<input type='number' id='CT' class='form-control input-sm' placeholder='จำนวนวันที่สร้าง - วันที่โอน' value=''>
						</div>
					</div>
					<div class='col-xs-6 col-sm-4'>	
						<div class='form-group'>
							จำนวนวันที่โอน - รับโอน
							<input type='number' id='TR' class='form-control input-sm' placeholder='จำนวนวันที่โอน - รับโอน' value=''>
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
	
	public function TransfersSearch(){
		$arrs = array();
		$arrs["TRANSNO"] 	= $_REQUEST["TRANSNO"];
		$arrs["TRANSDTs"] 	= $this->Convertdate(1,$_REQUEST["TRANSDTs"]);
		$arrs["TRANSDTe"] 	= $this->Convertdate(1,$_REQUEST["TRANSDTe"]);
		$arrs["TRANSFM"] 	= $_REQUEST["TRANSFM"];
		$arrs["TRANSTO"] 	= $_REQUEST["TRANSTO"];
		$arrs["TRANSSTAT"]	= $_REQUEST["TRANSSTAT"];
		$arrs["TRANSSTAT2"] = $_REQUEST["TRANSSTAT2"];
		$arrs["TRANSSYS"] 	= $_REQUEST["TRANSSYS"];
		$arrs["STRNO"] 		= $_REQUEST["STRNO"];
		$arrs["CT"] 		= $_REQUEST["CT"];
		$arrs["TR"] 		= $_REQUEST["TR"];
		
		$cond = "";
		$condDesc = "";
		if($arrs["TRANSNO"] != ""){
			$condDesc .= " เลขที่บิลโอน ".$arrs["TRANSNO"];
			$cond .= " and a.TRANSNO like '".$arrs["TRANSNO"]."%'  collate thai_cs_as ";
		}else{
			$condDesc .= " เลขที่บิลโอน ทั้งหมด";
		}
		
		if($arrs["TRANSDTs"] != "" and $arrs["TRANSDTe"] != ""){
			$condDesc .= " วันที่สร้างบิลโอน ระหว่างวันที่ ".$this->Convertdate(2,$arrs["TRANSDTs"])." ถึงวันที่ ".$this->Convertdate(2,$arrs["TRANSDTe"]);
			$cond .= " and convert(varchar(8),a.TRANSDT,112) between '".$arrs["TRANSDTs"]."' and '".$arrs["TRANSDTe"]."' ";
		}else if($arrs["TRANSDTs"] != "" and $arrs["TRANSDTe"] == ""){
			$condDesc .= " วันที่สร้างบิลโอน วันที่ ".$this->Convertdate(2,$arrs["TRANSDTs"]);
			$cond .= " and convert(varchar(8),a.TRANSDT,112) = '".$arrs["TRANSDTs"]."' ";
		}else if($arrs["TRANSDTs"] == "" and $arrs["TRANSDTe"] != ""){
			$condDesc .= " วันที่สร้างบิลโอน วันที่ ".$this->Convertdate(2,$arrs["TRANSDTs"]);
			$cond .= " and convert(varchar(8),a.TRANSDT,112) = '".$arrs["TRANSDTe"]."' ";
		}else{
			$condDesc .= " วันที่สร้างบิลโอน ทั้งหมด";
		}
		
		if($arrs["TRANSFM"] != ""){
			$condDesc .= " สาขาต้นทาง ".$arrs["TRANSFM"];
		}else{
			$condDesc .= " สาขาต้นทาง ทั้งหมด";
		}
		
		if($arrs["TRANSTO"] != ""){
			$condDesc .= " สาขาปลายทาง  ".$arrs["TRANSTO"];
		}else{
			$condDesc .= " สาขาปลายทาง ทั้งหมด";
		}
		
		$cond .= " and a.TRANSFM like '".$arrs["TRANSFM"]."%'";
		$cond .= " and a.TRANSTO like '".$arrs["TRANSTO"]."%'";
		
		if($arrs["TRANSSTAT"] != ""){
			if($arrs["TRANSSTAT"] == "Sendding2"){
				$condDesc .= " สถานะรถ อยู่ระหว่างการโอนย้ายรถ";
				$cond .= " and b.RECEIVEDT is null ";
			}else if($arrs["TRANSSTAT"] == "Received2"){
				$condDesc .= " สถานะรถ อยู่ระหว่างการโอนย้ายรถ";
				$cond .= " and b.RECEIVEDT is not null ";
			}else{
				$data = ($arrs["TRANSSTAT"] == "Sendding" ? "อยู่ระหว่างการโอนย้ายรถ":($arrs["TRANSSTAT"] == "Pendding" ? "รับโอนรถบางส่วน" : "รับโอนรถครบแล้ว"));
				$condDesc .= " สถานะบิลโอน {$data}";
				$cond .= " and a.TRANSSTAT = '".$arrs["TRANSSTAT"]."' ";
			}
		}else{
			$condDesc .= " สถานะบิลโอน ทั้งหมด";
		}
		
		if($arrs["TRANSSTAT2"] == "hasTRANSDT"){
			$condDesc .= " สถานะโอน(รายคัน) ระบุวันที่โอน และพขร.แล้ว";
			$cond .= " and isnull(b.EMPCARRY,'') != '' and b.TRANSDT is not null ";
		}else if($arrs["TRANSSTAT2"] == "notTRANSDT"){
			$condDesc .= " สถานะโอน(รายคัน) ยังไม่ระบุวันที่โอน และพขร.";
			$cond .= " and isnull(b.EMPCARRY,'') = '' and b.TRANSDT is null ";
		}else{
			$condDesc .= " สถานะโอน(รายคัน) ทั้งหมด";
		}
		
		if($arrs["TRANSSYS"] != ""){
			$data = ($arrs["TRANSSYS"] == "MT" ? "โอนเอง":"อัตโนมัติ");
			$condDesc .= " ระบบ {$data}";
			$cond .= " and a.SYSTEM = '".$arrs["TRANSSYS"]."' ";
		}else{
			$condDesc .= " ระบบ ทั้งหมด";
		}
		
		if($arrs["STRNO"] != ""){
			$data = $arrs["STRNO"];
			$condDesc .= " เลขตัวถัง {$data}";
			$cond .= " and b.STRNO like '".$arrs["STRNO"]."' ";
		}
		
		if($arrs["CT"] != "" and $arrs["TR"] != ""){
			$condDesc .= " จน.วันที่สร้าง - วันที่โอน {$arrs["CT"]} วันขึ้นไป และวันที่โอน - วันที่รับ {$arrs["TR"]} วันขึ้นไป";
			$cond .= " and datediff(day,a.TRANSDT,isnull(b.TRANSDT,getdate())) >= {$arrs["CT"]} and datediff(day,b.TRANSDT,isnull(b.RECEIVEDT,getdate())) >= {$arrs["TR"]} ";
		}else if($arrs["CT"] != "" and $arrs["TR"] == ""){
			$condDesc .= " จน.วันที่สร้าง - วันที่โอน {$arrs["CT"]} วันขึ้นไป ";
			$cond .= " and datediff(day,a.TRANSDT,isnull(b.TRANSDT,getdate())) >= {$arrs["CT"]} ";
		}else if($arrs["CT"] == "" and $arrs["TR"] != ""){
			$condDesc .= " จน.วันที่โอน - วันที่รับ {$arrs["TR"]} วันขึ้นไป";
			$cond .= " and datediff(day,isnull(b.TRANSDT,a.TRANSDT),isnull(b.RECEIVEDT,getdate())) >= {$arrs["TR"]} ";
		}
		
		$sql = "
			select a.TRANSNO,a.TRANSFM,a.TRANSTO,b.TRANSITEM,b.STRNO
				,c.titleName+c.firstName+' '+c.lastName as EMPCARRY
				,convert(varchar(8),a.TRANSDT,112) as TRANSDTCreate
				,convert(varchar(8),b.TRANSDT,112) as TRANSDT
				,datediff(day,a.TRANSDT,isnull(b.TRANSDT,getdate())) as ct
				,datediff(day,b.TRANSDT,isnull(b.RECEIVEDT,getdate())) as tr
				,e.titleName+e.firstName+' '+e.lastName as RECEIVEBY,convert(varchar(8),b.RECEIVEDT,112) as RECEIVEDT
				,d.titleName+d.firstName+' '+d.lastName as INSERTBY
				,convert(varchar(8),b.INSERTDT,112) as INSERTDT 
				,convert(varchar(5),b.INSERTDT,108) as INSERTDTTime 
				,a.SYSTEM
			from {$this->MAuth->getdb('INVTransfers')} a
			left join {$this->MAuth->getdb('INVTransfersDetails')} b on a.TRANSNO=b.TRANSNO collate thai_cs_as
			left join {$this->MAuth->getdb('hp_vusers')} c on c.IDNo=b.EMPCARRY collate thai_cs_as
			left join {$this->MAuth->getdb('hp_vusers')} d on d.IDNo=b.INSERTBY collate thai_cs_as
			left join {$this->MAuth->getdb('hp_vusers')} e on e.IDNo=b.RECEIVEBY collate thai_cs_as
			where 1=1 ".$cond." and a.TRANSSTAT <> 'Cancel'
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
				}else if($row->EMPCARRY != "" and $row->TRANSDT != "" and $row->RECEIVEDT == ""){
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
						<td>".$row->ct."</td>
						<td>".$row->RECEIVEBY."</td>
						<td>".$this->Convertdate(2,$row->RECEIVEDT)."</td>
						<td>".$row->tr."</td>
						<td>".$row->SYSTEM."</td>
						<td>".$row->INSERTBY."</td>
						<td>".$this->Convertdate(2,$row->INSERTDT)." ".$row->INSERTDTTime."</td>
					</tr>
				";
			}
		}else{
			$html .= "
				<tr>
					<td class='text-center' colspan='14'>ไม่พบข้อมูลตามเงื่อนไข</td>
				</tr>
			";
		}
		
		$html = "
			<div id='table-fixed-TransfersSearch' class='col-sm-12' style='height:calc(100% - 25px);width:100%;overflow:auto;font-size:8pt;'>
				<table id='table-TransfersSearch' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='100%'>
					<thead>
						<tr>
							<th colspan='16' class='text-center' style='font-size:12pt;border:0px;'> 
								รายงานการโอนย้ายรถ
							</th>
						</tr>
						<tr>
							<th colspan='16' class='text-center' style='border:0px;'>
								ออกรายงานโดย ".$this->sess["name"]." &emsp; ณ วันที่ ".$this->MDATA->sysdt()."
							</th>
						</tr>
						<tr>
							<th colspan='16' class='text-center' style='border:0px;color:#666;'>
								เงื่อนไข :: ".$condDesc."
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
							<th style='vertical-align:middle;border:0px;'>จน.วัน<br/>สร้าง-โอน</th>
							<th style='vertical-align:middle;border:0px;'>ผู้รับโอน</th>
							<th style='vertical-align:middle;border:0px;'>วันที่รับโอน</th>
							<th style='vertical-align:middle;border:0px;'>จน.วัน<br/>โอน-รับ</th>
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
		
	public function MaxstockCompare(){
		$claim = $this->MLogin->getclaim(uri_string());
		if($claim['m_access'] != "T"){ echo "<div align='center' style='color:red;font-size:16pt;width:100%;'>ขออภัย คุณยังไม่มีสิทธิเข้าใช้งานหน้านี้ครับ</div>"; exit; }
		
		$html = "
			<div class='tab1' name='home' locat='{$this->sess['branch']}' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}' style='height:calc(100vh - 132px);overflow:auto;background-color:white;'>
				<div>					
					<div class='col-xs-6 col-sm-3'>	
						<div class='form-group'>
							<div class='col-sm-12'>
								<input class='form-check-input' style='cursor:pointer;max-width:20px;max-height:10px;' type='checkbox' id='tab11prov1' value='ตรัง'  >
								<label class='form-check-label' style='cursor:pointer;' for='tab11prov1'>ตรัง</label>
							</div>
							<div class='col-sm-12'>
								<input class='form-check-input' style='cursor:pointer;max-width:20px;max-height:10px;' type='checkbox' id='tab11prov2' value='สุราษฎร์ธานี' >
								<label class='form-check-label' style='cursor:pointer;' for='tab11prov2'>สุราษฎร์ธานี</label>
							</div>
							<div class='col-sm-12'>
								<input class='form-check-input' style='cursor:pointer;max-width:20px;max-height:10px;' type='checkbox' id='tab11prov3' value='ชุมพร' >
								<label class='form-check-label' style='cursor:pointer;' for='tab11prov3'>ชุมพร</label>																			
							</div>
							<div class='col-sm-12'>
								<input class='form-check-input' style='cursor:pointer;max-width:20px;max-height:10px;' type='checkbox' id='tab11prov4' value='กระบี่' >
								<label class='form-check-label' style='cursor:pointer;' for='tab11prov4'>กระบี่</label>
							</div>
							<div class='col-sm-12'>
								<input class='form-check-input' style='cursor:pointer;max-width:20px;max-height:10px;' type='checkbox' id='tab11prov5' value='พังงา' >
								<label class='form-check-label' style='cursor:pointer;' for='tab11prov5'>พังงา</label>
							</div>	
						</div>
					</div>
					<div class='col-xs-6 col-sm-6'>	
						<div class='form-group'>
							สถานะจัดรถ
							<select id='CONDCal' class='form-control selcls input-sm chosen-select' data-placeholder='แบบ' >
								<option value=''>ทั้งหมด</option>
								<option value='1'>เป็นสาขาที่ใช่ในการคำนวนจัดรถ</option>
								<option value='2'>ไม่เป็นสาขาที่ใช่ในการคำนวนจัดรถ</option>
							</select>
						</div>
					</div>
					<div class='col-xs-6 col-sm-3'>	
						<div class='form-group'>
							สาขา
							<input type='text' id='LOCAT' class='form-control input-sm' placeholder='สาขา' value=''>
						</div>
					</div>
					<div class='col-xs-6 col-sm-3'>	
						<div class='form-group'>
							รุ่น
							<!-- select id='MODEL' class='form-control selcls input-sm chosen-select' data-placeholder='รุ่น' ></select -->
							<input type='text' id='MODEL' class='form-control input-sm' placeholder='รุ่น' value=''>
						</div>
					</div>
					<div class='col-xs-6 col-sm-3'>	
						<div class='form-group'>
							แบบ
							<!-- select id='BAAB' class='form-control selcls input-sm chosen-select' data-placeholder='แบบ' ></select -->
							<input type='text' id='BAAB' class='form-control input-sm' placeholder='แบบ' value=''>
						</div>
					</div>
					<div class='col-xs-6 col-sm-3'>	
						<div class='form-group'>
							สี
							<!--input type='text' id='COLOR' class='form-control input-sm' placeholder='สี' value=''-->
							<input type='text' id='COLOR' class='form-control input-sm' placeholder='สี' value=''>
						</div>
					</div>
					
					
					<div class='col-xs-6 col-sm-3 col-sm-offset-3 '>
						<div class='form-group'>
							เงื่อนไข						
							<select id='CONDSort' class='form-control selcls input-sm chosen-select' data-placeholder='เงื่อนไข' >
								<option value='1'>Max Stock</option>
								<option value='2'>Stock</option>
								<option value='3'>รายการโอน</option>
								<option value='4'>พื้นที่ว่าง</option>
							</select>
						</div>
					</div>
						<div class='col-sm-2'>
							<div class='radio'>
								<label><input type='radio' class='sort' name='maxmin' value='asc' checked>น้อยไปมาก</label>
							</div>
							<div class='radio'>
								<label><input type='radio' class='sort' name='maxmin' value='desc'>มากไปน้อย</label>
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

		$html.= "<script src='".base_url('public/js/SYS02/CReport/MaxstockCompare.js')."'></script>";
		echo $html;	
	}
	
	public function MaxstockCompareSearch(){
		$arrs = array();
		$arrs["tab11prov1"] = $_REQUEST["tab11prov1"];
		$arrs["tab11prov2"] = $_REQUEST["tab11prov2"];
		$arrs["tab11prov3"] = $_REQUEST["tab11prov3"];
		$arrs["tab11prov4"] = $_REQUEST["tab11prov4"];
		$arrs["tab11prov5"] = $_REQUEST["tab11prov5"];
		$arrs["LOCAT"]		= $_REQUEST["LOCAT"];
		$arrs["MODEL"] 		= $_REQUEST["MODEL"];
		$arrs["BAAB"] 		= $_REQUEST["BAAB"];
		$arrs["COLOR"] 		= $_REQUEST["COLOR"];
		$arrs["CONDCal"] 	= $_REQUEST["CONDCal"];
		$arrs["CONDSort"] 	= $_REQUEST["CONDSort"];
		$arrs["sort"] 		= $_REQUEST["sort"];
		
		$condDesc = "";
		$Prov = "";
		if($arrs['tab11prov1'][0] == "true"){
			$condDesc .= $condDesc != "" ? ",".$arrs['tab11prov1'][1] : "จังหวัด  ".$arrs['tab11prov1'][1];
			$Prov .= "'".$arrs['tab11prov1'][1]."'";
		}
		if($arrs['tab11prov2'][0] == "true"){
			$condDesc .= $condDesc != "" ? ",".$arrs['tab11prov2'][1] : "จังหวัด  ".$arrs['tab11prov2'][1];
			$Prov .= "'".$arrs['tab11prov2'][1]."'";
		}
		if($arrs['tab11prov3'][0] == "true"){
			$condDesc .= $condDesc != "" ? ",".$arrs['tab11prov3'][1] : "จังหวัด  ".$arrs['tab11prov3'][1];
			$Prov .= "'".$arrs['tab11prov3'][1]."'";
		}
		if($arrs['tab11prov4'][0] == "true"){
			$condDesc .= $condDesc != "" ? ",".$arrs['tab11prov4'][1] : "จังหวัด  ".$arrs['tab11prov4'][1];
			$Prov .= "'".$arrs['tab11prov4'][1]."'";
		}
		if($arrs['tab11prov5'][0] == "true"){
			$condDesc .= $condDesc != "" ? ",".$arrs['tab11prov5'][1] : "จังหวัด  ".$arrs['tab11prov5'][1];
			$Prov .= "'".$arrs['tab11prov5'][1]."'";
		}
		$condDesc .= ($condDesc != "" ? $condDesc : "จังหวัด ทั้งหมด");
		$Prov = ($Prov == "" ? "" : " and b.Prov in (".str_replace("''","','",$Prov).")");
		
		$cond = $Prov;
		if($arrs["CONDCal"] == 1){
			$condDesc .= " สถานะจัดรถ ใช้งาน";
			$cond .= " and locatStatus='Y'";
		}else if($arrs["CONDCal"] == 2){
			$condDesc .= " สถานะจัดรถ ไม่ใช้งาน";
			$cond .= " and isnull(locatStatus,'N')='N'";
		}else{
			$condDesc .= " สถานะจัดรถ ทั้งหมด";
		}
		
		if($arrs["LOCAT"] != ""){
			$condDesc .= " สาขา ".$arrs["LOCAT"];
			$cond .= " and a.LOCATCD like '".$arrs["LOCAT"]."%' ";
		}else{
			$condDesc .= " สาขา ทั้งหมด ";
		}
		
		if($arrs["MODEL"] != ""){
			$condDesc .= " รุ่น ".$arrs["MODEL"];			
		}
		
		if($arrs["BAAB"] != ""){
			$condDesc .= " แบบ ".$arrs["BAAB"];			
		}
		
		if($arrs["COLOR"] != ""){
			$condDesc .= " สี ".$arrs["COLOR"];
		}
				
		$sort = "";
		switch($arrs["CONDSort"]){
			case 1: $sort = " b.MaxStock ".$arrs["sort"]; break;
			case 2: $sort = " (c.N+c.O) ".$arrs["sort"]; break;
			case 3: $sort = " (d.N+d.O) ".$arrs["sort"]; break;
			case 4: $sort = " (MaxStock-(c.N+c.O)) ".$arrs["sort"]; break;
		}
		
		$sql = "
			select a.LOCATCD as LOCAT 
				,b.MaxStockN
				,b.MaxStockO
				,b.MaxStock
				,b.MaxStore
				,c.N as SN,c.O as SO,c.N+c.O as SNO
				,d.N as TN,d.O as [TO],d.N+d.O as TNO
				,e.N as MBCN,e.O as MBCO,e.N+e.O as MBCNO
				,b.locatStatus
				,MaxStock-(c.N+c.O) xx
			from HIINCOME.dbo.INVLOCAT a
			left join YTKManagement.dbo.std_locatStock b on a.LOCATCD=b.LOCAT collate thai_cs_as
			left join (
				select * from (
					select CRLOCAT,STAT,STRNO from HIINCOME.dbo.INVTRAN
					where FLAG='D' and ISNULL(TSALE,'') = ''
						and ISNULL(RESVNO,'') = '' and RESVDT is null 
						and ISNULL(CONTNO,'') = '' and SDATE is null
				) as data
				pivot (
					count(STRNO) for STAT in ([N],[O])
				) as pv
			) as c on a.LOCATCD=c.CRLOCAT
			left join (
				select * from (
					select a.TRANSTO,c.STAT,c.STRNO from YTKManagement.dbo.INVTransfers a
					left join YTKManagement.dbo.INVTransfersDetails b on a.TRANSNO=b.TRANSNO collate thai_cs_as 
					left join HIINCOME.dbo.INVTRAN c on b.STRNO=c.STRNO collate thai_cs_as
					where isnull(b.MOVENO,'')='' and isnull(RECEIVEBY,'')='' and RECEIVEDT is null and c.CRLOCAT='TRANS'
				) as data
				pivot (
					count(STRNO) for STAT in ([N],[O])
				) as pv
			) as d on a.LOCATCD=d.TRANSTO collate thai_cs_as
			left join (
				select * from (
					select CRLOCAT,STAT,STRNO from HIINCOME.dbo.INVTRAN
					where FLAG='D' and ISNULL(TSALE,'') = ''
						and ISNULL(RESVNO,'') = '' and RESVDT is null 
						and ISNULL(CONTNO,'') = '' and SDATE is null
						and MODEL='".$arrs["MODEL"]."' and BAAB='".$arrs["BAAB"]."' and COLOR='".$arrs["COLOR"]."'
				) as data
				pivot (
					count(STRNO) for STAT in ([N],[O])
				) as pv
			) as e on a.LOCATCD=e.CRLOCAT
			where 1=1 ".$cond."
			order by ".$sort."
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		$NRow =  1;
		if($query->row()){
			foreach($query->result() as $row){
				$html .= "
					<tr>
						<td class='text-center'>".$NRow++."</td>
						<td class='text-center'>".$row->LOCAT."</td>
						<td class='text-right'>".$row->MaxStockN."</td>
						<td class='text-right'>".$row->MaxStockO."</td>
						<td class='text-right' style='background-color:#ddd;'>".$row->MaxStock."</td>
						<td class='text-right'>".$row->MaxStore."</td>
						<td class='text-right'>".$row->SN."</td>
						<td class='text-right'>".$row->SO."</td>
						<td class='text-right' style='background-color:#ddd;'>".$row->SNO."</td>
						<td class='text-right'>".$row->TN."</td>
						<td class='text-right'>".$row->TO."</td>
						<td class='text-right'>".$row->TNO."</td>
						<td class='text-right'>".$row->MBCN."</td>
						<td class='text-right'>".$row->MBCO."</td>
						<td class='text-right'>".$row->MBCNO."</td>
						<td class='text-center'>".$row->locatStatus."</td>
						<td class='text-right' style='background-color:#ddd;'>".$row->xx."</td>
					</tr>
				";
			}
		}else{
			$html .= "
				<tr>
					<td class='text-center' colspan='17'>ไม่พบข้อมูลตามเงื่อนไข</td>
				</tr>
			";
		}
		
		
		$html = "
			<div id='table-fixed-TransfersPenddingSearch' class='col-sm-12' style='height:calc(100% - 25px);width:100%;overflow:auto;font-size:8pt;'>
				<table id='table-TransfersPenddingSearch' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='100%'>
					<thead>
						<tr>
							<th colspan='17' class='text-center' style='font-size:12pt;border:0px;'> 
								รายงาน max stock เทียบสต๊อกสาขา รอรับโอน
							</th>
						</tr>
						<tr>
							<th colspan='17' class='text-center' style='border:0px;'>
								ออกรายงานโดย ".$this->sess["name"]." &emsp; ณ วันที่ ".$this->MDATA->sysdt()."
							</th>
						</tr>
						<tr>
							<th colspan='17' class='text-center' style='border:0px;color:#666;'>
								เงื่อนไข :: ".$condDesc."
							</th>
						</tr>
						<tr>
							<th style='vertical-align:middle;border:0px;'>#</th>
							<th style='vertical-align:middle;border:0px;'>สาขา</th>
							<th style='vertical-align:middle;border:0px;'>Max ใหม่</th>
							<th style='vertical-align:middle;border:0px;'>Max เก่า</th>
							<th style='vertical-align:middle;border:0px;'>Max รวม</th>
							<th style='vertical-align:middle;border:0px;'>Max คลัง</th>
							<th style='vertical-align:middle;border:0px;' title='สต๊อกรถใหม่'>st ใหม่</th>
							<th style='vertical-align:middle;border:0px;' title='สต๊อกรถเก่า'>st เก่า</th>
							<th style='vertical-align:middle;border:0px;' title='สต๊อกรวม'>st รวม</th>
							<th style='vertical-align:middle;border:0px;' title='รายการโอนออก (รถใหม่)'>tf ใหม่</th>
							<th style='vertical-align:middle;border:0px;' title='รายการโอนออก (รถเก่า)'>tf เก่า</th>
							<th style='vertical-align:middle;border:0px;' title='รายการโอนออก (รวม)'>tf รวม</th>
							<th style='vertical-align:middle;border:0px;' title='สต๊อกแยกตาม รุ่น แบบ สี (รถใหม่)'>mbc ใหม่</th>
							<th style='vertical-align:middle;border:0px;' title='สต๊อกแยกตาม รุ่น แบบ สี (รถเก่า)'>mbc เก่า</th>
							<th style='vertical-align:middle;border:0px;' title='สต๊อกแยกตาม รุ่น แบบ สี (รวม)'>mbc รวม</th>
							<th style='vertical-align:middle;border:0px;'>สถานะ</th>
							<th style='vertical-align:middle;border:0px;'>พื้นที่ว่าง</th>
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
			left join INVTransfersDetails b on a.TRANSNO=b.TRANSNO collate thai_cs_as 
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




















