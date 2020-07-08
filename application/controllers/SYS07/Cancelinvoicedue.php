<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//BEE+
class Cancelinvoicedue extends MY_Controller {
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
			<div class='b_tab1' name='home' locat='{$this->sess['branch']}' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}' today='".$this->today('today')."' style='height:calc(100vh - 132px);overflow:auto;background-color:white;'>
				<div class='col-sm-12 col-xs-12' style='height:100%;overflow:auto;font-size:10.5pt;'>					
					<div class='row' style='height:80%;'>
						<br><br>
						<div class='col-sm-8 col-xs-8 col-sm-offset-2 text-primary'><b>ยกเลิกใบกำกับค่างวด</b></div>
						<div class='col-sm-8 col-xs-8 col-sm-offset-2' style='border:0.5px dotted #afe4cf;background-color:#f6fefa;' >
							<div class='col-sm-12 col-xs-12' style='height:40px;padding:10px;' align='right'>
								<div id='INVSTATUS' class='bg-danger text-white' style='width:100px;text-align:center;font-size;9pt;display:none;'>
									<b>ยกเลิก </b>
								</div>
							</div>
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group text-primary' >
									เลขที่ใบกำกับ
									<select id='INVNO1' class='form-control input-sm' data-placeholder='เลขที่ใบกำกับ'></select>
								</div>
							</div>
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group'>
									วันที่ใบกำกับ
									<input type='text' id='VATDATE' class='form-control input-sm' placeholder='วันที่ใบกำกับ'  style='font-size:10.5pt' readonly>
								</div>
							</div>
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group'>
									เลขที่สัญญา
									<input type='text' id='CONTNO1' class='form-control input-sm' placeholder='เลขที่สัญญา' style='font-size:10.5pt' readonly>
								</div>
							</div>
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group'>
									สาขา
									<input type='text' id='LOCAT1' class='form-control input-sm' placeholder='สาขา' style='font-size:10.5pt' readonly>
								</div>
							</div>
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group'>
									รหัสลูกค้า
									<input type='text' id='CUSCOD1' class='form-control input-sm' placeholder='รหัสลูกค้า' style='font-size:10.5pt' readonly>
								</div>
							</div>
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group'>
									ชื่อ-สกุล ลูกค้า
									<input type='text' id='CUSNAME1' class='form-control input-sm' placeholder='ชื่อ-สกุล ลูกค้า' style='font-size:10.5pt' readonly>
								</div>
							</div>
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group'>
									จำนวนเงิน
									<input type='text' id='AMOUNT1' class='form-control input-sm' placeholder='จำนวนเงิน' style='font-size:10.5pt' readonly>
								</div>
							</div>
							<div class='col-sm-3 col-xs-3'>	
								<div class='form-group'>
									จากงวดที่
									<input type='text' id='FPAY' class='form-control input-sm' value='' style='font-size:10.5pt' readonly>
								</div>
							</div>
							<div class='col-sm-3 col-xs-3'>	
								<div class='form-group'>
									ถึงงวดที่
									<input type='text' id='LPAY' class='form-control input-sm' value='' style='font-size:10.5pt' readonly>
								</div>
							</div>
							<div class='col-sm-12 col-xs-12'>	
								<div class='form-group'>
									รายการ
									<input type='text' id='DETAIL' class='form-control input-sm' value='' style='font-size:10.5pt' readonly>
									<br><br>
								</div>
							</div>
						</div>
					</div>
					<div class='row' style='height:10%;'>
						<div class='col-sm-12 col-xs-12' style='text-align:center;font-size:8pt;color:#999;'>
							** User ที่จะใช้งานหัวข้อนี้ได้ ต้องมีสิทธิ์ในการแก้ไขข้อมูล **
						</div>
					</div>
					<div class='row' style='height:10%;'>
						<div class='col-sm-8 col-xs-8 col-sm-offset-2'>
							<button id='btncancel' class='btn btn-danger btn-sm' style='width:100%;font-size:10.5pt;'><span class='glyphicon glyphicon-ban-circle'><b> ยกเลิกใบกำกับ</b></span></button>
						</div>
					</div>
				</div>
			</div>
		";
		
		$html.= "<script src='".base_url('public/js/SYS07/Cancelinvoicedue.js')."'></script>";
		echo $html;
	}
	
	function searchINVNO(){
		$INVNO1	= $_REQUEST["INVNO1"];
		$sql = "
				SELECT TAXNO, convert(char,TAXDT,112) as TAXDT, CONTNO, LOCAT, CUSCOD, SNAM, NAME1, NAME2,
				TOTAMT, FPAY, LPAY, DESCP, FLAG
				FROM {$this->MAuth->getdb('TAXTRAN')} 
				WHERE TAXNO = '".$INVNO1."'
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$response = array();
		if($query->row()){
			foreach($query->result() as $row){
				$response["TAXNO"] 		= $row->TAXNO;
				$response["TAXDT"] 		= $this->Convertdate(2,$row->TAXDT);
				$response["CONTNO"] 	= str_replace(chr(0),'',$row->CONTNO);
				$response["LOCAT"] 		= str_replace(chr(0),'',$row->LOCAT);
				$response["CUSCOD"] 	= str_replace(chr(0),'',$row->CUSCOD);
				$response["CUSNAME"] 	= str_replace(chr(0),'',$row->SNAM).str_replace(chr(0),'',$row->NAME1).' '.str_replace(chr(0),'',$row->NAME2);
				$response["TOTAMT"] 	= number_format($row->TOTAMT,2);
				$response["FPAY"] 		= number_format($row->FPAY);
				$response["LPAY"] 		= number_format($row->LPAY);
				$response["DESCP"] 		= str_replace(chr(0),'',$row->DESCP);
				$response["FLAG"] 		= str_replace(chr(0),'',$row->FLAG);
			}
		}
		
		echo json_encode($response);
	}
	
	function Cancel_invoince(){
		$INVNO1 	= $_REQUEST["INVNO1"];
		$USERID		= $this->sess["USERID"];
		
		$sql = "
			if OBJECT_ID('tempdb..#Cancelinvoice') is not null drop table #Cancelinvoice;
			create table #Cancelinvoice (id varchar(20),contno varchar(20),msg varchar(max));
			
			begin tran Cancelinvoice
			begin try
					
					declare @taxno varchar(20)		= '".$INVNO1 ."';
					declare @contno varchar(20) 	= (select CONTNO from {$this->MAuth->getdb('TAXTRAN')} where TAXNO = @taxno collate thai_cs_as);

					update {$this->MAuth->getdb('TAXTRAN')}  
					set FLAG = 'C', CANDT = GETDATE(), FLCANCL = '".$USERID."'
					where TAXNO = @taxno

					update {$this->MAuth->getdb('ARPAY')}  
					set TAXINV = '', TAXDT = NULL, TAXAMT = 0, TAXPAY = 0
					where CONTNO = @contno and TAXINV = @taxno
					
					insert into {$this->MAuth->getdb('hp_UserOperationLog')} (userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
					values ('".$this->sess["IDNo"]."','SYS07::ยกเลิกใบกำกับภาษีค่างวด (แก้ไข)',' ".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
					
					insert into #Cancelinvoice select 'S',@taxno,'บันทึกยกเลิกใบกำกับค่างวดเลขที่ '+@taxno+' เรียบร้อย';

				commit tran Cancelinvoice;
			end try
			begin catch
				rollback tran Cancelinvoice;
				insert into #Cancelinvoice select 'E','',ERROR_MESSAGE();
			end catch
		";
		//echo $sql; exit;
		
		$this->db->query($sql);
		$sql = "select * from #Cancelinvoice";
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
			$response["msg"] = 'ผิดพลาดไม่สามารถยกเลิกใบกำกับค่างวดได้ โปรดติดต่อฝ่ายไอที';
		}
		
		echo json_encode($response);
	}
}