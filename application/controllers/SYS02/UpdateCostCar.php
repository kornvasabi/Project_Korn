<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/********************************************************
             ______@08/04/2020______
			 Pasakorn Boonded

********************************************************/
class UpdateCostCar extends MY_Controller {
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
		$html ="
			<div class='k_tab1' name='home' locat='{$this->sess['branch']}' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}' today='".$this->today('today')."' style='height:calc(100vh - 132px);overflow:auto;background-color:white;'>
				<!------------------------------header--------------------------------->
				<div class='col-sm-12 col-xs-12' style='height:100%;overflow:auto;font-size:11pt;'>
					<div class='row' style='height:90%;'>
						<div class='col-sm-12 col-xs-12' style='background-color:#1abc9c;border:5px solid white;height:75px;text-align:center;font-size:12pt;color:white;font-weight:bold;'>
							<br>ปรับปรุงราคาต้นทุนรถ<br>
						</div><br><br><br><br><br>
						<div class='col-sm-10 col-xs-10 col-sm-offset-1' style='border:0.1px dotted #d6d6d6;'>
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group'>
									<div class='col-sm-12 col-xs-12'>	
										<div class='col-sm-12'>
											<div class='form-group'>
												เลขตัวถัง
												<select id='STRNO' class='form-control input-sm'></select>
											</div>
										</div>
										<div class='col-sm-12'>
											<div class='form-group'>
												เลขที่สัญญา
												<input type='text' id='CONTNO' class='form-control input-sm'>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group'>
									<div class='col-sm-12 col-xs-12'>	
										<div class='col-sm-6'>
											<div class='form-group'>
												ประเภทการขาย
												<input type='text' id='TSALE' class='form-control input-sm'>
											</div>
										</div>
										<div class='col-sm-12'>
											<div class='form-group'>
												<br>
												<font>ประเภทการขาย C = สด,H = ผ่อน,F = ไฟแนนซ์</font>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div><br><br><br><br><br><br><br><br>
						<div class='col-sm-10 col-xs-10 col-sm-offset-1' style='border:0.1px dotted #d6d6d6;'>
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group'>
									<div class='col-sm-12 col-xs-12'>	
										<div class='col-sm-12'>
											<div class='form-group'>
												มูลค่าสินค้า
												<input type='text' id='NETCOST' class='form-control input-sm 
												jzAllowNumber' style='text-align:right;'>
											</div>
										</div>
										<div class='col-sm-12'>
											<div class='form-group'>
												ภาษีมูลค่าเพิ่ม
												<input type='text' id='CRVAT' class='form-control input-sm
												jzAllowNumber' style='text-align:right;'>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group'>
									<div class='col-sm-12 col-xs-12'>	
										<div class='col-sm-12'>
											<div class='form-group'>
												อัตราภาษี
												<input type='text' id='VATRT' class='form-control input-sm
												jzAllowNumber' style='text-align:right;'>
											</div>
										</div>
										<div class='col-sm-12'>
											<div class='form-group'>
												ราคารวมภาษี
												<input type='text' id='TOTCOST' class='form-control input-sm 
												jzAllowNumber' style='text-align:right;'>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div><br>
						<div class='col-sm-10 col-xs-10 col-sm-offset-1'><br>
							<div class='col-sm-4 col-xs-4'>
								<br>
								<button id='btnedit' type='button' class='btn btn-warning' style='width:100%;'>
									<span class='fa fa-edit'><b>แก้ไข</b></span>
								</button>
							</div>
							<div class='col-sm-4 col-xs-4'>
								<br>
								<button id='btnsave' type='button' class='btn btn-info' style='width:100%;'>
									<span class='fa fa-save'><b>บันทึก</b></span>
								</button>
							</div>
							<div class='col-sm-4 col-xs-4'>
								<br>
								<button id='btnclear' type='button' class='btn btn-danger' style='width:100%;'>
									<span class='fa fa-remove'><b>ยกเลิก</b></span>
								</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		";
		$html .="<script src='".base_url('public/js/SYS02/UpdateCostCar.js')."'></script>";
		echo ($html);
	}
	function getCostCar(){
		$STRNO = $_REQUEST['STRNO'];

		$sql = "
			select CONTNO,TSALE,NETCOST,CRVAT,TOTCOST,VATRT from {$this->MAuth->getdb('INVTRAN')}
			where STRNO = '".$STRNO."'
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				if($row->TSALE == "A"){
					$response["error"] = true;
					$response["msg"]   = 'ไม่สามารถแก้ไขรายการขายส่งเอเยนส์ได้ครับ';	
					echo json_encode($response); exit;
				}else{
					$response['CONTNO'] = $row->CONTNO;
					$response['TSALE']  = $row->TSALE;
					$response['NETCOST']= number_format($row->NETCOST,2);
					$response['CRVAT']  = number_format($row->CRVAT,2);
					$response['TOTCOST']= number_format($row->TOTCOST,2);
					$response['VATRT']  = number_format($row->VATRT,2);
				}
			}
		}else{
			$response['CONTNO'] = "";
			$response['TSALE']  = "";
			$response['NETCOST']= "";
			$response['CRVAT']  = "";
			$response['TOTCOST']= "";
			$response['VATRT']  = "";
		}
		echo json_encode($response);
	}
	function getcalculation(){
		$NETCOST = str_replace(',','',$_REQUEST['NETCOST']);
		$VATRT   = str_replace(',','',$_REQUEST['VATRT']);
		
		$response['CRVAT']   = number_format($NETCOST * $VATRT / 100,2);
		$response['TOTCOST'] = number_format(str_replace(',','',$response['CRVAT']) + $NETCOST,2);

		echo json_encode($response);
	}
	function Savecostcar(){
		$STRNO   = $_REQUEST['STRNO'];
		$CONTNO  = $_REQUEST['CONTNO'];
		$NETCOST = str_replace(',','',$_REQUEST['NETCOST']);
		$CRVAT   = str_replace(',','',$_REQUEST['CRVAT']);
		$VATRT   = str_replace(',','',$_REQUEST['VATRT']);
		$TOTCOST = str_replace(',','',$_REQUEST['TOTCOST']);

		$sql = "
			select RECVNO from {$this->MAuth->getdb('INVTRAN')}
			where STRNO = '".$STRNO."'
		";
		$recvno = "";
		$queryrn = $this->db->query($sql);
		$rowrn = $queryrn->row();
		$recvno = $rowrn->RECVNO;
		//echo $recvno; exit;

		$sql = "
			if OBJECT_ID('temp..#UpdateCost') is not null drop table #UpdateCost;
			create table #UpdateCost(id varchar(1),msg varchar(max));

			begin tran updatecostcar
			begin try
				/*
				declare @armast int = (select COUNT(*) from {$this->MAuth->getdb('ARMAST')}
				where STRNO = '".$STRNO."' and CONTNO = '".$CONTNO."');
				declare @invinvo int = (select COUTN(*) from {$this->MAuth->getdb('INVTINVO')}
				where RECVNO = '".$recvno."');
				if (@armast = 1 and @invinvo = 1)
					begin
						update {$this->MAuth->getdb('INVTRAN')} set NETCOST = '".$NETCOST."'
						,CRVAT = '".$CRVAT."',TOTCOST = '".$TOTCOST."',VATRT = '".$VATRT."' 
						where STRNO = '".$STRNO."' and CONTNO = '".$CONTNO."'

						update {$this->MAuth->getdb('ARMAST')} set NCARCST = '".$NETCOST."'
						,VCARCST = '".$CRVAT."',TCARCST = '".$TOTCOST."'
						where STRNO = '".$STRNO."' and CONTNO = '".$CONTNO."' 	
						
						update {$this->MAuth->getdb('INVINVO')} set NCARCST = '".$NETCOST."'
						,VCARCST = '".$CRVAT."',TCARCST = '".$TOTCOST."' where RECVNO = '".$recvno."'
					end
					insert into #UpdateCost select 'Y' as id,'สำเร็จ : ปรับปรุงรายการราคาต้นทุนรถแล้วครับ' as msg;
					commit tran updatecostcar
				else
					begin
						rollback tran updatecostcar
						insert into #UpdateCost select 'N' as id,'บันทึกข้อมูลไม่สำเร็จ : กรุณาติดต่อฝ่ายไอที' as msg;
						return;
					end	
				*/
				update {$this->MAuth->getdb('INVTRAN')} set NETCOST = '".$NETCOST."'
				,CRVAT = '".$CRVAT."',TOTCOST = '".$TOTCOST."',VATRT = '".$VATRT."' 
				where STRNO = '".$STRNO."' and CONTNO = '".$CONTNO."'

				update {$this->MAuth->getdb('ARMAST')} set NCARCST = '".$NETCOST."'
				,VCARCST = '".$CRVAT."',TCARCST = '".$TOTCOST."'
				where STRNO = '".$STRNO."' and CONTNO = '".$CONTNO."' 	
				
				update {$this->MAuth->getdb('INVINVO')} set NETCST = '".$NETCOST."'
				,NETVAT = '".$CRVAT."',NETTOT = '".$TOTCOST."',UPD = 'X' where RECVNO = '".$recvno."'

				insert into #UpdateCost select 'Y' as id,'สำเร็จ : ปรับปรุงรายการราคาต้นทุนรถแล้วครับ' as msg;
				commit tran updatecostcar
			end try
			begin catch
				rollback tran updatecostcar
				insert into #UpdateCost select 'N' as id,'บันทึกข้อมูลไม่สำเร็จ : กรุณาติดต่อฝ่ายไอที' as msg;
				return;
			end catch
		";
		$this->db->query($sql);
		//echo $sql; exit;
		$sql = "
			select * from #UpdateCost
		";
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$response['status'] = $row->id;
				$response['msg']   = $row->msg;
			}
		}else{
			$response['status'] = false;
			$response['msg']    = "ผิดพลาด";
		}
		echo json_encode($response);
	}
}