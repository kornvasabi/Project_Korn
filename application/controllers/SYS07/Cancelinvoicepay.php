<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//BEE+
class Cancelinvoicepay extends MY_Controller {
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
						<div class='col-sm-10 col-xs-10 col-sm-offset-1 text-primary'><b>ยกเลิกใบกำกับภาษีซื้อ</b></div>
						<div class='col-sm-10 col-xs-10 col-sm-offset-1' style='border:0.5px dotted #afe4cf;background-color:#f6fefa;' >
							<br>
							<div class='col-sm-4 col-xs-4'>	
								<div class='form-group text-primary' >
									เลขที่ใบกำกับ
									<select id='INVNO1' class='form-control input-sm' data-placeholder='เลขที่ใบกำกับ'></select>
								</div>
							</div>
							<div class='col-sm-4 col-xs-4'>	
								<div class='form-group'>
									วันที่ใบกำกับ
									<input type='text' id='VATDATE' class='form-control input-sm' placeholder='วันที่ใบกำกับ'  style='font-size:10.5pt' readonly>
								</div>
							</div>
							<div class='col-sm-4 col-xs-4'>	
								<div class='form-group'>
									สาขา
									<input type='text' id='LOCAT1' class='form-control input-sm' placeholder='สาขา' style='font-size:10.5pt' readonly>
								</div>
							</div>
							<div class='col-sm-4 col-xs-4'>	
								<div class='form-group'>
									มูลค่าสินค้า
									<input type='text' id='NETAMT1' class='form-control input-sm' placeholder='มูลค่าสินค้า' style='font-size:10.5pt' readonly>
									<br>
								</div>
							</div>
							<div class='col-sm-4 col-xs-4'>	
								<div class='form-group'>
									ภาษี
									<input type='text' id='VAT1' class='form-control input-sm' placeholder='ภาษี' style='font-size:10.5pt' readonly>
									<br>
								</div>
							</div>
							<div class='col-sm-4 col-xs-4'>	
								<div class='form-group'>
									จำนวนเงิน
									<input type='text' id='AMOUNT1' class='form-control input-sm' placeholder='จำนวนเงิน' style='font-size:10.5pt' readonly>
									<br>
								</div>
							</div>
						</div>
						<br>
						<div class='col-sm-10 col-xs-10 col-sm-offset-1' style='height:20px;'>
						</div>
						<div class='col-sm-10 col-xs-10 col-sm-offset-1' style='background-color:#dafbeb;'>
							<div class='col-sm-12 col-xs-12' >
								<div id='dataTable-fixed-taxdata' class='dataTables_wrapper dt-bootstrap4 table-responsive' style='height:200px;width:100%;overflow:auto;'>
									<table id='dataTables-taxdata' class='table table-bordered dataTable table-hover' stat='' aria-describedby='dataTable_info' cellspacing='0' width='calc(100% - 1px)'>
										<thead>
											<tr role='row' style='height:30px;font-size:8pt;background-color:#009966;color:white;'>
												<th style='vertical-align:middle;'>เลขที่ใบกำกับ</th>
												<th style='vertical-align:middle;'>สาขา</th>
												<th style='vertical-align:middle;text-align:right;'>มูลค่า</th>
												<th style='vertical-align:middle;text-align:right;'>ภาษี</th>
												<th style='vertical-align:middle;text-align:right;'>ราคารวมภาษี</th>
												<th style='vertical-align:middle;text-align:center;'>เลขที่ใบรับ</th>
												<th style='vertical-align:middle;text-align:center;'>วันที่รับ</th>
											</tr>
										</thead>
										<tbody style='white-space:nowrap;background-color:white;font-size:9pt;'></tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
					<div class='row' style='height:10%;'>
						<div class='col-sm-12 col-xs-12' style='text-align:center;font-size:8pt;color:#999;'>
							** User ที่จะใช้งานหัวข้อนี้ได้ ต้องมีสิทธิ์ในการลบข้อมูล **
						</div>
					</div>
					<div class='row' style='height:10%;'>
						<div class='col-sm-10 col-xs-10 col-sm-offset-1'>
							<div class='col-sm-6 col-xs-6'>	
								<button id='btnsearch' class='btn btn-primary btn-sm' style='width:100%;font-size:10.5pt;'><span class='glyphicon glyphicon-search'> แสดงใบกำกับที่ควรยกเลิก</span></button>
							</div>
							<div class='col-sm-6 col-xs-6'>	
								<button id='btncancel' class='btn btn-danger btn-sm' style='width:100%;font-size:10.5pt;'><span class='glyphicon glyphicon-ban-circle'> ยกเลิกใบกำกับ</span></button>
							</div>
						</div>
					</div>
				</div>
			</div>
		";
		
		$html.= "<script src='".base_url('public/js/SYS07/Cancelinvoicepay.js')."'></script>";
		echo $html;
	}
	
	function searchINVNO(){
		$INVNO1	= $_REQUEST["INVNO1"];
		$response = array();
		
		$sql = "
				select TAXNO, convert(char,TAXDT,112) as TAXDT, LOCAT, NETAMT, VATAMT, TOTAMT 
				from {$this->MAuth->getdb('TAXBUY')}
				where TAXNO = '".$INVNO1."'
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
	
		if($query->row()){
			foreach($query->result() as $row){
				$response["TAXNO"] 		= $row->TAXNO;
				$response["TAXDT"] 		= $this->Convertdate(2,$row->TAXDT);
				$response["LOCAT"] 		= str_replace(chr(0),'',$row->LOCAT);
				$response["NETAMT"] 	= number_format($row->NETAMT,2);
				$response["VATAMT"] 	= number_format($row->VATAMT,2);
				$response["TOTAMT"] 	= number_format($row->TOTAMT,2);
			}
		}
		
		$sql2 = "
				select RECVNO, convert(char,RECVDT,112) as TAXDT, LOCAT, NETCST, NETVAT, NETTOT, INVNO
				from {$this->MAuth->getdb('INVINVO')}
				where TAXNO = '".$INVNO1."'
		";
		//echo $sql2; exit;
		$query2 = $this->db->query($sql2);
		
		$taxdata = ""; 
		if($query2->row()){
			foreach($query2->result() as $row){
				$taxdata .= "
					<tr class='trow' style='height:25px;'>
						<td style='vertical-align:middle;'>".$row->INVNO."</td>
						<td style='vertical-align:middle;' >".$row->LOCAT."</td>
						<td style='vertical-align:middle;text-align:right;'>".number_format($row->NETCST,2)."</td>
						<td style='vertical-align:middle;text-align:right;'>".number_format($row->NETVAT,2)."</td>
						<td style='vertical-align:middle;text-align:right;'>".number_format($row->NETTOT,2)."</td>
						<td style='vertical-align:middle;text-align:center;' >".$row->RECVNO."</td>
						<td style='vertical-align:middle;text-align:center;' >".$this->Convertdate(2,$row->TAXDT)."</td>
					</tr>
				";	
			}	
		}else{
			$taxdata .= "<tr class='trow'><td colspan='7'>ไม่มี</td></tr>";
		}

		$response["taxdata"] = $taxdata;
		
		echo json_encode($response);
	}
	
	function searchINVNO2(){
		$INVNO1	= $_REQUEST["INVNO1"];
		$response = array();
		
		$sql = "
				select TAXNO, LOCAT, NETAMT, VATAMT, TOTAMT
				from {$this->MAuth->getdb('TAXBUY')}  
				where NETAMT = 0 and TAXNO not in (select TAXNO from INVINVO)
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$taxdata2 = ""; 
		if($query->row()){
			foreach($query->result() as $row){
				$taxdata2 .= "
					<tr class='trow' style='height:25px;'>
						<td style='vertical-align:middle;'>".$row->TAXNO."</td>
						<td style='vertical-align:middle;' >".$row->LOCAT."</td>
						<td style='vertical-align:middle;text-align:right;'>".number_format($row->NETAMT,2)."</td>
						<td style='vertical-align:middle;text-align:right;'>".number_format($row->VATAMT,2)."</td>
						<td style='vertical-align:middle;text-align:right;'>".number_format($row->TOTAMT,2)."</td>
						<td style='vertical-align:middle;text-align:center;' >-</td>
						<td style='vertical-align:middle;text-align:center;' >-</td>
					</tr>
				";	
			}	
		}else{
			$taxdata2 .= "<tr class='trow'><td colspan='7'>ไม่มี</td></tr>";
		}

		$response["taxdata2"] = $taxdata2;
		
		echo json_encode($response);
	}
	
	function Cancel_invoince(){
		$INVNO1 	= $_REQUEST["INVNO1"];
		$LOCAT1 	= $_REQUEST["LOCAT1"];
		$USERID		= $this->sess["USERID"];
		
		$sql = "
			if OBJECT_ID('tempdb..#Cancelinvoicebuy') is not null drop table #Cancelinvoicebuy;
			create table #Cancelinvoicebuy (id varchar(20),contno varchar(20),msg varchar(max));
			
			begin tran Cancelinvoicebuy
			begin try
					
					declare @taxno varchar(20)		= '".$INVNO1 ."';
					declare @locat varchar(10)		= '".$LOCAT1 ."';

					if @taxno in (select TAXNO from {$this->MAuth->getdb('INVINVO')} where TAXNO = @taxno and LOCAT = @locat)
					begin
						insert into #Cancelinvoicebuy select 'W',@taxno,'ใบกำกับเลขที่ '+@taxno+' มีการอ้างอิงถึง ไม่สามารถทำการยกเลิกได้';
					end
					else
					begin
						update {$this->MAuth->getdb('TAXBUY')}
						set FLAG = 'C', CANDT = GETDATE(), CANID = '".$USERID."'
						where TAXNO = @taxno
						
						insert into {$this->MAuth->getdb('hp_UserOperationLog')} (userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
						values ('".$this->sess["IDNo"]."','SYS07::ยกเลิกใบกำกับภาษีซื้อ (แก้ไข)',' ".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
						
						insert into #Cancelinvoicebuy select 'S',@taxno,'บันทึกยกเลิกใบกำกับภาษีซื้อเลขที่ '+@taxno+' เรียบร้อย';
					end

				commit tran Cancelinvoicebuy;
			end try
			begin catch
				rollback tran Cancelinvoicebuy;
				insert into #Cancelinvoicebuy select 'E','',ERROR_MESSAGE();
			end catch
		";
		//echo $sql; exit;
		
		$this->db->query($sql);
		$sql = "select * from #Cancelinvoicebuy";
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
			$response["msg"] = 'ผิดพลาดไม่สามารถยกเลิกใบกำกับภาษีซื้อได้ โปรดติดต่อฝ่ายไอที';
		}
		
		echo json_encode($response);
	}
}