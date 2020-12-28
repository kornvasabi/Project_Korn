<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/********************************************************
             ______@27/10/2020______
            pasakorn boonded
********************************************************/
class UpdateProfitNopay extends MY_Controller {
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
		//echo "เมนูยังไม่เสร็จ กำลังพัฒนาต่อครับ"; exit;
		$html = "
			<div class='tab1' name='home' locat='{$this->sess['branch']}' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}' style='height:calc(100vh - 132px);overflow:auto;background-color:white;'>
				<div class='col-sm-12' style='overflow:auto;'>					
					<div class='row'>
						<div class='col-sm-12 col-xs-12' style='background-color:#808b96;border:5px solid white;height:75px;text-align:center;font-size:12pt;color:white;font-weight:bold;'>	
							<br>การปรับปรุงดอกผลเช่าซื้อ<br>
						</div>
						<div class=' col-sm-8 col-sm-offset-2'>	
							<div class='col-sm-6'>
								<div class='form-group'>
									รหัสสาขา
									<select id='add_locat' class='form-control input-sm'>
									</select>
								</div>	
							</div>
							<div class='col-sm-6'>
								<div class='form-group'>
									รหัสสาขา
									<select id='add_contno' class='form-control input-sm'>
									</select>
								</div>	
							</div>
							<div class='col-sm-6'>
								<div class='form-group'>
									จากวันที่ใบรับ
									<input type='text' id='fdate' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' value='".$this->today('today')."'>
								</div>	
							</div>
							<div class='col-sm-6'>
								<div class='form-group'>
									จากวันที่ใบรับ
									<input type='text' id='tdate' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' value='".$this->today('today')."'>
								</div>	
							</div>
							<div class='col-sm-12'>
								<div class='form-group'>
									<div class='col-sm-12 col-xs-12' style='border:0.1px dotted #d6d6d6;'>
										<form id='condup'>
											<div class='col-sm-6'>
												<div class='form-group'>
													<br><label>
														<input type= 'radio' name='rediocond' checked value='U1'> เฉพาะที่ยังไม่เคยปรับ
													</label>
												</div>
											</div>
											<div class='col-sm-6'>
												<div class='form-group'>
													<br><label>
														<input type= 'radio' name='rediocond' value='U2'> ปรับใหม่ทั้งหมด
													</label>
												</div>
											</div>
										</form>
									</div>
								</div>	
							</div>
							<div class='col-sm-6'>
								<div class='form-group'>
									<br>
									<input type='button' id='btn_upstr' class='btn btn-sm btn-primary right' style='width:100%;' value='ปรับปรุงดอกผล STR ในตารางค่างวด' >
								</div>	
							</div>
							<div class='col-sm-6'>
								<div class='form-group'>
									<br>
									<input type='button' id='btn_upsyd' class='btn btn-sm btn-primary right' style='width:100%;' value='ปรับปรุงดอกผลตามใบรับ' >
								</div>	
							</div>
							<!--div class='col-sm-6'>
								<div class='form-group'>
									<br>
									<input type='button' id='btn_update' class='btn btn-sm btn-primary right' style='width:100%;' value='syd' >
								</div>	
							</div-->
						</div>
					</div>
				</div>
			</div>
		";
		$html .= "<script src='".base_url('public/js/SYS06/UpdateProfitNopay.js')."'></script>";
		echo $html;
	}
	function Update(){
		$arrs = array();
		$arrs["params"] = $_REQUEST["params"];
		$arrs["locat"]  = $_REQUEST["locat"];
		$arrs["contno"] = $_REQUEST["contno"];
		$arrs["fdate"]  = $this->Convertdate(1,$_REQUEST["fdate"]);
		$arrs["tdate"]  = $this->Convertdate(1,$_REQUEST["tdate"]);
		$arrs["condup"] = $_REQUEST["condup"];
		
		$sql = "
			if OBJECT_ID('tempdb..#udatesave') is not null drop table #udatesave;
			create table #udatesave (id varchar(1),msg varchar(max));
			begin tran udatesaveprodit
			begin try
				begin
					declare @checkcont int = (
						select COUNT(*) from {$this->MAuth->getdb('ARMAST')} 
						where CONTNO = '".$arrs["contno"]."' and LOCAT = '".$arrs["locat"]."'
					)
					declare @calcstr decimal(20,15) = (
						SELECT NPROFIT / NKANG FROM {$this->MAuth->getdb('ARMAST')} 
						where CONTNO = '".$arrs["contno"]."' and LOCAT = '".$arrs["locat"]."'
					)
					if (@checkcont > 0)
					begin
						UPDATE {$this->MAuth->getdb('ARPAY')} 
							SET STRPROF = convert(decimal(12,2),N_DAMT * @calcstr) 
						where CONTNO = '".$arrs["contno"]."' and LOCAT = '".$arrs["locat"]."'
						
						UPDATE {$this->MAuth->getdb('CHQTRAN')} 
							SET PROFSTR = convert(decimal(12,2),PAYAMT_N * @calcstr)
						where CONTNO = '".$arrs["contno"]."' and LOCATPAY = '".$arrs["locat"]."'
						
						insert into #udatesave select 'Y' as id,'สำเร็จ บันทึกข้อมูลเรียบร้อยแล้ว' as msg;
						commit tran udatesaveprodit;	
					end
					else
					begin
						rollback tran udatesaveprodit;
						insert into #udatesave select 'N' as id,'ปรับปรุงไม่สำเร็จ : ไม่มีข้อมูลตามเงื่อนไขที่กำหนด' as msg;
						return;
					end
				end
			end try
			begin catch
				rollback tran udatesaveprodit;
				insert into #udatesave select 'E' as id,'บันทึกข้อมูลไม่สำเร็จ : กรุณาติดต่อฝ่ายไอที' as msg;
				return;
			end catch
		";
		//echo $sql; exit;
		$this->db->query($sql);
		$sql = "select * from #udatesave";
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$response["error"] = $row->id;
				$response["msg"]   = $row->msg;
			}
		}else{
			$response["error"] = "error";
			$response["msg"]   = "ผิดพลาด กรุณาติดต่อฝ่ายไอที";
		}
		echo json_encode($response); 
	}
}




















