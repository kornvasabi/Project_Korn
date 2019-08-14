<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//BEE+
class ExchangeCar extends MY_Controller {
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
	
	//หน้าแรก
	function index(){
		$claim = $this->MLogin->getclaim(uri_string());
		if($claim['m_access'] != "T"){ echo "<div align='center' style='color:red;font-size:16pt;width:100%;'>ขออภัย คุณยังไม่มีสิทธิเข้าใช้งานหน้านี้ครับ</div>"; exit; }
		
		$html = "
			<div class='b_tab1' name='home' locat='{$this->sess['branch']}' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}' style='height:calc(100vh - 132px);overflow:auto;background-color:white;'>
				<div class='col-sm-12' style='overflow:auto;'>					
					<div class='row'>
						<div class='col-sm-2 col-xs-2'>	
							<div class='form-group'>
								สาขา
								<select id='LOCAT1' class='form-control input-sm' data-placeholder='สาขา'></select>
							</div>
						</div>
						<div class='col-sm-2 col-xs-2'>	
							<div class='form-group'>
								เลขที่สัญญา
								<select id='CONTNO1' class='form-control input-sm' data-placeholder='เลขที่สัญญา'></select>
							</div>
						</div>
						<div class='col-sm-2 col-xs-2'>	
							<div class='form-group'>
								ลูกหนี้
								<select id='CUSCOD1' class='form-control input-sm' data-placeholder='ลูกหนี้'></select>
							</div>
						</div>
						<div class='col-sm-1 col-xs-1'>	
							<div class='form-group'>
								<br>
								<input type='button' id='btnt1search' class='btn btn-primary btn-sm' value='?: สอบถาม' style='width:100%'>
							</div>
						</div>
						<div class='col-sm-1 col-xs-1'>	
							<div class='form-group'>
								<br>
								<input type='button' id='bth1add' class='btn btn-cyan btn-sm' value='+ เพิ่มข้อมูล' style='width:100%'>
							</div>
						</div>
					</div>
					<div id='resultt_ExchangeCar' style='background-color:white;'></div>					
				</div>
			</div>
		";
		
		$html.= "<script src='".base_url('public/js/SYS05/ExchangeCar.js')."'></script>";
		echo $html;
	}
	
	//ฟอร์มบันทึกลูกหนี้อื่น
	function getfromExchangeCar(){
		$level	= $_REQUEST["level"];
		//$locat = $this->sess['branch'];
		$sql = "
			select * from {$this->MAuth->getdb('VATMAST')}
			where getdate() between FRMDATE and TODATE
		";
		$query = $this->db->query($sql);
		$row = $query->row();
		$data["vatrt"] = number_format($row->VATRT,2);

		$html = "
			<div class='b_add_arother' style='width:100%;height:calc(100vh - 132px);overflow:auto;background-color:white;'>
				<div style='float:left;height:100%;overflow:auto;' class='col-sm-10 col-sm-offset-1'>
					<div class='form-group col-sm-10 col-xs-10 col-sm-offset-1' style='border:0.1px solid #f0f0f0;'>
					<div class='form-group col-sm-10 col-xs-10 col-sm-offset-1'>
					<div class='row'>
						<div class='col-sm-4 col-xs-4'>	
							<div class='form-group'>
								<b>เลขที่สัญญา</b>
								<select id='CONTNO' class='form-control input-sm' data-placeholder='เลขที่สัญญา'></select>
							</div>
						</div>
					</div>
					<div class='row'>
						<div class='col-sm-4 col-xs-4'>	
							<div class='form-group'>
								สาขา
								<input type='text' id='LOCAT' class='form-control input-sm' style='font-size:10.5pt' disabled>
							</div>
						</div>
						<div class='col-sm-4 col-xs-4'>	
							<div class='form-group'>
								ชื่อ - สกุล ลูกหนี้
								<input type='text' id='CUSNAME' class='form-control input-sm' style='font-size:10.5pt' disabled> 
							</div>
						</div>
						<div class='col-sm-4 col-xs-4'>	
							<div class='form-group'>
								รหัสลูกหนี้
								<input type='text' id='CUSCOD' class='form-control input-sm' style='font-size:10.5pt' disabled>
							</div>
						</div>
						<div class='col-sm-4 col-xs-4'>	
							<div class='form-group'>
								เลขทะเบียน
								<input type='text' id='REGNO' class='form-control input-sm' style='font-size:10.5pt' disabled>
							</div>
						</div>
						<div class='col-sm-4 col-xs-4'>	
							<div class='form-group'>
								เลขตัวถัง
								<input type='text' id='STRNO' class='form-control input-sm' style='font-size:10.5pt' disabled>
							</div>
						</div>
						<div class='col-sm-4 col-xs-4'>	
							<div class='form-group'>
								ราคาขาย
								<input type='text' id='PRICE' class='form-control input-sm' style='font-size:10.5pt' disabled>
							</div>
						</div>
						<div class='col-sm-4 col-xs-4'>	
							<div class='form-group'>
								ชำระเงินแล้ว
								<input type='text' id='SMPAY' class='form-control input-sm' style='font-size:10.5pt' disabled>
							</div>
						</div>
						<div class='col-sm-4 col-xs-4'>	
							<div class='form-group'>
								ยอดคงเหลือ
								<input type='text' id='BALANCE' class='form-control input-sm' style='font-size:10.5pt' disabled>
							</div>
						</div>
						<div class='col-sm-4 col-xs-4'>	
							<div class='form-group'>
								ยอดค้างชำระ
								<input type='text' id='NETAR' class='form-control input-sm' style='font-size:10.5pt' disabled>
							</div>
						</div>
					</div>
					</div>
					</div>
					
					<div class='form-group col-sm-10 col-xs-10 col-sm-offset-1' style='border:0.1px solid #f0f0f0;'>
					<div class='form-group col-sm-10 col-xs-10 col-sm-offset-1'>
					<div class='row'>
						<div class='col-sm-3 col-xs-3'>	
							<div class='form-group'>
								มูลค่าคงเหลือตามบัญชี
								<input type='text' id='BOOKVALUE' class='form-control input-sm' style='font-size:10.5pt' >
							</div>
						</div>
						<div class='col-sm-3 col-xs-3'>	
							<div class='form-group'>
								ภาษีคงเหลือ
								<input type='text' id='SALEVAT' class='form-control input-sm' style='font-size:10.5pt' >
							</div>
						</div>
						<div class='col-sm-3 col-xs-3'>	
							<div class='form-group'>
								มูลค่าต้นทุน
								<input type='text' id='COST' class='form-control input-sm' style='font-size:10.5pt' >
							</div>
						</div>
						<div class='col-sm-3 col-xs-3'>	
							<div class='form-group'>
								ภาษีต้นทุนรถ
								<input type='text' id='COSTVAT' class='form-control input-sm' style='font-size:10.5pt' >
							</div>
						</div>
						<div class='col-sm-3 col-xs-3'>	
							<div class='form-group'>
								วันที่เปลี่ยนเป็นรถเก่า
								<input type='text' id='DATECHG' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' style='font-size:10.5pt' value='".$this->today('today')."'>
							</div>
						</div>
						<div class='col-sm-3 col-xs-3'>	
							<div class='form-group'>
								สถานที่เก็บ
								<input type='text' id='LOCATR' class='form-control input-sm' style='font-size:10.5pt' >
							</div>
						</div>
						<div class='col-sm-3 col-xs-3'>	
							<div class='form-group'>
								ราคาขายใหม่
								<input type='text' id='SALENEW' class='form-control input-sm' style='font-size:10.5pt'>
							</div>
						</div>
						<div class='col-sm-3 col-xs-3'>	
							<div class='form-group' >
								ประเภทสินค้าใหม่
								<select id='GCODENEW' class='form-control input-sm' data-placeholder='ประเภทสินค้า' ></select>
							</div>
						</div>
					</div>
					</div>
					</div>
					<div class='row'>
					<div class=' col-sm-8 col-xs-8 col-sm-offset-2'>	
							<div class='form-group'>
								หมายเหตุ
								<textarea type='text' id='MEMO' rows='1' cols='20' class='form-control input-sm' style='font-size:10.5pt'></textarea>
							</div>
						</div>	
					</div>
					<div class='row'>
						<div class=' col-sm-2 col-sm-offset-4'>	
							<div class='form-group'>
								<br>
								<input type='button' id='btnsave_exchangecar' class='btn btn-primary btn-sm' value='บันทึก' style='width:100%' >
							</div>
						</div>
						<div class=' col-sm-2'>	
							<div class='form-group'>
								<br>
								<input type='button' id='btndel_exchangecar' class='btn btn-danger btn-sm' value='ลบ' style='width:100%'>
							</div>
						</div>
					</div>
				</div>
			</div>
		";
	
		$response = array("html"=>$html);
		echo json_encode($response);
	}
	
	function searchCONTNO(){
		$contno	= $_REQUEST["contno"];

		$sql = "
				select a.CONTNO, a.CRLOCAT, isnull(a.REGNO,'') as REGNO, a.STRNO , a.GCODE, c.SNAM, c.NAME1, c.NAME2, b.CUSCOD, b.TOTPRC, b.SMPAY, 
				b.TOTPRC - b.SMPAY as BALANCE, 0 as CR
				from {$this->MAuth->getdb('INVTRAN')} a
				left join {$this->MAuth->getdb('ARMAST')} b on a.CONTNO = b.CONTNO
				left join {$this->MAuth->getdb('CUSTMAST')} c on b.CUSCOD = c.CUSCOD
				where a.CONTNO = '".$contno."'
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$response = array();
		if($query->row()){
			foreach($query->result() as $row){
				$response["CONTNO"] 	= $row->CONTNO;
				$response["CRLOCAT"] 	= $row->CRLOCAT;
				$response["CUSNAME"] 	= $row->SNAM.$row->NAME1.' '.$row->NAME2;
				$response["CUSCOD"] 	= $row->CUSCOD;
				$response["REGNO"] 		= $row->REGNO;
				$response["STRNO"] 		= $row->STRNO;
				$response["STRNO"] 		= $row->STRNO;
			}
		}
		
		echo json_encode($response);
	}
	
}