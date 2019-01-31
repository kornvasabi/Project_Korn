<?php
defined('BASEPATH') OR exit('No direct script access allowed');

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
	}
	
	function Transfers(){
		$claim = $this->MLogin->getclaim(uri_string());
		if($claim['m_access'] != "T"){ echo "<div align='center' style='color:red;font-size:16pt;width:100%;'>ขออภัย คุณยังไม่มีสิทธิเข้าใช้งานหน้านี้ครับ</div>"; exit; }
		
		$html = "
			<div class='tab1' name='home' locat='{$this->sess['branch']}' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}' style='height:calc(100vh - 132px);overflow:auto;background-color:white;'>
				<div style='height:65px;overflow:auto;'>					
					<div class='col-xs-2 col-sm-2'>	
						<div class='form-group'>
							เลขที่โอน
							<input type='text' id='TRANSNO' class='form-control input-sm' placeholder='เลขที่โอน'>
						</div>
					</div>
					<div class='col-xs-2 col-sm-2'>	
						<div class='form-group'>
							วันที่โอน
							<input type='text' id='TRANSDT' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' placeholder='วันที่โอน'>
						</div>
					</div>
					<div class='col-xs-2 col-sm-1'>	
						<div class='form-group'>
							โอนจากสาขา
							<input type='text' id='TRANSFM' class='form-control input-sm' placeholder='โอนจากสาขา' value='".$this->sess['branch']."'>
						</div>
					</div>
					<div class='col-xs-2 col-sm-2'>	
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
					<div class='col-xs-2 col-sm-1'>	
						<div class='form-group'>
							
						</div>
					</div>
					<div class='col-xs-2 col-sm-1 col-sm-offset-3'>	
						<div class='form-group'>
							<br>
							<input type='button' id='btnt1transfer' class='btn btn-primary btn-sm' value='แสดง' style='width:100%'>
						</div>
					</div>
				</div>
				<div id='resultt1transfer' style='height:calc(100% - 65px);overflow:auto;background-color:white;'></div>
			</div>
		";

		$html.= "<script src='".base_url('public/js/SYS02/CReport.js')."'></script>";
		echo $html;		
	}
	
	function TransfersSearch(){
		$sql = "
			select a.TRANSNO,a.TRANSFM,a.TRANSTO,b.TRANSITEM,b.STRNO,b.EMPCARRY
				,convert(varchar(8),b.TRANSDT,112) as TRANSDT
				,b.RECEIVEBY,convert(varchar(8),b.RECEIVEDT,112) as RECEIVEDT
				,b.INSERTBY,convert(varchar(8),b.INSERTDT,112) as INSERTDT 
				,convert(varchar(5),b.INSERTDT,108) as INSERTDTTime 
			from {$this->MAuth->getdb('INVTransfers')} a
			left join {$this->MAuth->getdb('INVTransfersDetails')} b on a.TRANSNO=b.TRANSNO
			where 1=1
			order by a.TRANSNO,b.TRANSITEM
		";
		
		$query = $this->db->query($sql);
		
		$html = "";
		$NRow = 1;
		if($query->row()){
			foreach($query->result() as $row){
				$html .= "
					<tr>
						<td>".$NRow++."</td>
						<td>".$row->TRANSNO."</td>
						<td>".$row->TRANSFM."</td>
						<td>".$row->TRANSTO."</td>
						<td>".$row->TRANSITEM."</td>
						<td>".$row->STRNO."</td>
						<td>".$row->EMPCARRY."</td>
						<td>".$this->Convertdate(2,$row->TRANSDT)."</td>
						<td>".$row->RECEIVEBY."</td>
						<td>".$this->Convertdate(2,$row->RECEIVEDT)."</td>
						<td>".$row->INSERTBY."</td>
						<td>".$this->Convertdate(2,$row->INSERTDT)." ".$row->INSERTDTTime."</td>
						
					</tr>
				";
			}
		}
		
		$html = "
			<div id='table-fixed-TransfersSearch' class='col-sm-12' style='height:100%;width:100%;overflow:auto;'>
				<table id='table-TransfersSearch' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='100%'>
					<thead>
						<tr>
							<th style='vertical-align:middle;'>#</th>
							<th style='vertical-align:middle;'>เลขที่โอน</th>
							<th style='vertical-align:middle;'>สาขาต้นทาง</th>
							<th style='vertical-align:middle;'>สาขาปลายทาง</th>
							<th style='vertical-align:middle;'>ลำดับการโอน</th>
							<th style='vertical-align:middle;'>เลขตัวถัง</th>
							<th style='vertical-align:middle;'>พขร.</th>
							<th style='vertical-align:middle;'>วันที่โอน</th>
							<th style='vertical-align:middle;'>ผู้รับโอน</th>
							<th style='vertical-align:middle;'>วันที่รับโอน</th>
							<th style='vertical-align:middle;'>ผู้โอนย้าย</th>
							<th style='vertical-align:middle;'>วันที่บันทึกโอน</th>
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
}




















