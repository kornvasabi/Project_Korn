<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/********************************************************
             ______@04/04/2020______
			 Pasakorn Boonded

********************************************************/
class EditStrno extends MY_Controller {
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
				<div class='col-sm-12 col-xs-12' style='height:100%;overflow:auto;font-size:11pt;'>
					<div class='row' style='height:90%;'>
						<div class='col-sm-12 col-xs-12' style='background-color:#1abc9c;border:5px solid white;height:75px;text-align:center;font-size:12pt;color:white;font-weight:bold;'>	
							<br>เปลี่ยนหมายเลขเครื่องรถที่ขายแล้ว<br>
						</div>
						<div class='col-sm-10 col-xs-10 col-sm-offset-1'><br>	
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group'>
									<div class='col-sm-12 col-xs-12' style='border:0.1px dotted #d6d6d6;background-color:#aeb6bf;'>	
										<div class='col-sm-12'>
											<div class='form-group'>
												เลขตัวถัง
												<select id='STRNO' class='form-control input-sm'></select>
											</div>
										</div>
										<div class='col-sm-12'>
											<div class='form-group'>
												เลขเครื่อง
												<input type='text' id='ENGNO' class='form-control input-sm' maxlength='20'>
											</div>
										</div>
										<div class='col-sm-12'>
											<div class='form-group'>
												ยี่ห้อ
												<select id='TYPE' class='form-control input-sm'></select>
											</div>
										</div>
										<div class='col-sm-12'>
											<div class='form-group'>
												รุ่น
												<select id='MODEL' class='form-control input-sm'></select>
											</div>
										</div>
										<div class='col-sm-12'>
											<div class='form-group'>
												แบบ
												<select id='BAAB' class='form-control input-sm'></select>
											</div>
										</div>
										<div class='col-sm-12'>
											<div class='form-group'>
												สี
												<select id='COLOR' class='form-control input-sm'></select>
											</div>
										</div>
										<div class='col-sm-12'>
											<div class='form-group'>
												ขนาด (ซีซี)
												<select id='CC' class='form-control input-sm'></select>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class='col-sm-6 col-xs-6'>	
								<div class='form-group'>
									<div class='col-sm-12 col-xs-12' style='border:0.1px dotted #d6d6d6;background-color:#aeb6bf;'>	
										<div class='col-sm-12'>
											<div class='form-group'>
												เลขที่สัญญา
												<input type='text' id='CONTNO' class='form-control input-sm'>
											</div>
										</div>
										<div class='col-sm-12'>
											<div class='form-group'>
												กลุ่มสินค้า
												<input type='text' id='GCODE' class='form-control input-sm' maxlength='3'>
											</div>
										</div>
										<div class='col-sm-12'>
											<div class='form-group'>
												สถานะ (N/O)
												<input type='text' id='STAT' class='form-control input-sm'>
											</div>
										</div>
										<div class='col-sm-12'>
											<div class='form-group'>
												ประเภทการขาย
												<input type='text' id='TSALE' class='form-control input-sm'>
											</div>
										</div>
										<div class='col-sm-12'>
											<div class='form-group'>
												เลขไมค์
												<input type='text' id='MILERT' class='form-control input-sm jzAllowNumber' 
												maxlength='12'>
											</div>
										</div>
										<div class='col-sm-12'>
											<div class='form-group'>
												<br>
												<font>C: ขายสด  ,  A: ส่งเอเย่นต์</font>
											</div>
										</div>
										<div class='col-sm-12'>
											<div class='form-group'>
												<br>
												<font>F: ส่งไฟแนนซ์  ,  H: เช่าซื้อ</font>
											</div><br>
										</div>
									</div>
								</div>
							</div>
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
		$html .="<script src='".base_url('public/js/SYS02/EditStrno.js')."'></script>";
		echo ($html);
	}
	function DetailCar(){
		$STRNO = $_REQUEST['STRNO'];
		
		$sql = "
			select RECVNO,RECVDT,GCODE,TYPE,MODEL,COLOR,BAAB,COLOR,CC,STAT,STRNO
			STRF,ENGNO,REGNO,KEYNO,REFNO,CAST(ROUND(MILERT,0) as decimal(12)) MILERT	
			,CRLOCAT,MOVENO,MOVEDT,RVCODE,RVLOCAT,STDCOST,CRCOST,DISCT,NETCOST
			,CRVAT,TOTCOST,VATRT,NADDCOST,VADDCOST,TADDCOST,STDPRC,SDATE,PRICE,BONUS
			,TSALE,CONTNO,CURSTAT,CRDTXNO,CRDAMT,RESVNO,RESVDT,FLAG,MEMO1,POSTDT,INPDT
			,USERID,DORECV,MANUYR,YSTAT,JOBDATE from INVTRAN where STRNO = '".$STRNO."'
		";
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$response['ENGNO'] = $row->ENGNO;
				$response['TYPE']  = $row->TYPE;
				$response['MODEL'] = $row->MODEL;
				$response['BAAB']  = $row->BAAB;
				$response['COLOR'] = $row->COLOR;
				$response['CC']    = $row->CC;
				$response['CONTNO']= $row->CONTNO;
				$response['GCODE'] = $row->GCODE;
				$response['STAT']  = $row->STAT;
				$response['TSALE'] = $row->TSALE;
				$response['MILERT']= $row->MILERT;			
			}
		}else{
			$response['ENGNO'] = "";
			$response['TYPE']  = "";
			$response['MODEL'] = "";
			$response['BAAB']  = "";
			$response['COLOR'] = "";
			$response['CC']    = "";
			$response['CONTNO']= "";
			$response['GCODE'] = "";
			$response['STAT']  = "";
			$response['TSALE'] = "";
			$response['MILERT']= "";		
		}
		echo json_encode($response);
	}
	function SaveEditCarDetail(){
		$STRNO = $_REQUEST['STRNO'];
		$ENGNO = $_REQUEST['ENGNO'];
		$TYPE  = $_REQUEST['TYPE'];
		$MODEL = $_REQUEST['MODEL'];
		$BAAB  = $_REQUEST['BAAB'];
		$COLOR = $_REQUEST['COLOR'];
		$CC    = $_REQUEST['CC'];
		$GCODE = $_REQUEST['GCODE'];
		$STAT  = $_REQUEST['STAT'];
		$MILERT= $_REQUEST['MILERT'];

		if($ENGNO == ''){
			$response["error"] = true;
			$response["msg"]   = 'กรุณากรอกเลขเครื่องก่อนครับ';	
			echo json_encode($response); exit;
		}
		if($TYPE == ''){
			$response["error"] = true;
			$response["msg"]   = 'กรุณาเลือกยี่ห้อก่อนครับ';	
			echo json_encode($response); exit;
		}
		if($MODEL == ''){
			$response["error"] = true;
			$response["msg"]   = 'กรุณาเลือกรุ่นก่อนครับ';	
			echo json_encode($response); exit;
		}
		if($BAAB == ''){
			$response["error"] = true;
			$response["msg"]   = 'กรุณาเลือกแบบก่อนครับ';	
			echo json_encode($response); exit;
		}
		if($COLOR == ''){
			$response["error"] = true;
			$response["msg"]   = 'กรุณาเลือกสีก่อนครับ';	
			echo json_encode($response); exit;
		}
		if($CC == ''){
			$response["error"] = true;
			$response["msg"]   = 'กรุณาซีซีก่อนครับ';	
			echo json_encode($response); exit;
		}
		if($GCODE == ''){
			$response["error"] = true;
			$response["msg"]   = 'กรุณากรอกกลุ่มลูกค้าก่อนครับ';	
			echo json_encode($response); exit;
		}
		if($STAT == ''){
			$response["error"] = true;
			$response["msg"]   = 'กรุณากรอกสถานะก่อนครับ';	
			echo json_encode($response); exit;
		}
		if($MILERT == ''){
			$response["error"] = true;
			$response["msg"]   = 'กรุณากรอกเลขไมค์ก่อนครับ';	
			echo json_encode($response); exit;
		}
		$sql = "
			if OBJECT_ID('temp..#editST') is not null drop table #editST;
			create table #editST (id varchar(1),msg varchar(max));
			begin tran editstrno
			begin try
				update {$this->MAuth->getdb('INVTRAN')} set ENGNO = '".$ENGNO."',TYPE = '".$TYPE."',MODEL = '".$MODEL."'
				,BAAB = '".$BAAB."',COLOR = '".$COLOR."',CC = '".$CC."',GCODE = '".$GCODE."',STAT = '".$STAT."'
				,MILERT = '".$MILERT."' where STRNO = '".$STRNO."'

				insert into #editST select 'Y' as id,'สำเร็จ : แก้ไขข้อมูลเรียบร้อยแล้วครับ' as msg;
				commit tran editstrno
			end try
			begin catch
				rollback tran editstrno
				insert into #editST select 'N' as id,'บันทึกข้อมูลไม่สำเร็จ : กรุณาติดต่อฝ่ายไอที'
				return;
			end catch
		";
		//echo $sql; exit;
		$this->db->query($sql);
		$sql = "select * from #editST";
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$response['status']  = $row->id;
				$response['msg'] = $row->msg; 
			}
		}else{
			$response['status']  = false;
			$response['msg'] = 'ผิดพลาด'; 
		}
		echo json_encode($response);
	}
}