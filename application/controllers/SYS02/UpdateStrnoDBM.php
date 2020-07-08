<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/********************************************************
             ______@13/04/2020______
			 Pasakorn Boonded

********************************************************/
class UpdateStrnoDBM extends MY_Controller {
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
						<div class='col-sm-12 col-xs-12' style='background-color:#1abc9c;border:5px solid 
						white;height:75px;text-align:center;font-size:12pt;color:white;font-weight:bold;'>
							<br>ปรับปรุงรายการย้ายรถซ้ำ<br>
						</div>
						<div class='col-sm-10 col-xs-10 col-sm-offset-1'><br>	
							<div class='col-sm-12 col-xs-12'>	
								<div class='form-group'>
									<div class='col-sm-12 col-xs-12'>	
										<div class='col-sm-6'>
											<div class='form-group'>
												จากวันที่ย้าย
												<input type='text' id='MOVEDT_F' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' value='".$this->today('today')."' styl='font-size:10.5pt;'>
											</div>
										</div>
										<div class='col-sm-6'>
											<div class='form-group'>
												ถึงวันที่ย้าย
												<input type='text' id='MOVEDT_T' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' value='".$this->today('today')."' styl='font-size:10.5pt;'>
											</div>
										</div>
										<div class='col-sm-6'>
											<div class='form-group'>
												เลขตัวถัง
												<select id='STRNO' class='form-control input-sm'></select>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class='col-sm-12 col-xs-12'>
								<br>
								<button id='btnupdate' type='button' class='btn btn-info' style='width:100%;'>
									<span class='glyphicon glyphicon-pencil'><b>ปรับปรุงรายการ</b></span>
								</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		";
		$html .="<script src='".base_url('public/js/SYS02/UpdateStrnoDBM.js')."'></script>";
		echo ($html);
	}
	function UpdateStrnoBle(){
		$MOVEDT_F = $this->Convertdate(1,$_REQUEST['MOVEDT_F']);
		$MOVEDT_T = $this->Convertdate(1,$_REQUEST['MOVEDT_T']);
		$STRNO    = $_REQUEST['STRNO'];
		
		$sql = "
			select B.MOVEDT,B.STRNO from {$this->MAuth->getdb('INVMOVM')} A
			left join {$this->MAuth->getdb('INVMOVT')} B on A.MOVENO = B.MOVENO 
			where B.MOVEDT >= '".$MOVEDT_F."' and B.MOVEDT <= '".$MOVEDT_T."'
			and STRNO like '%".$STRNO."%' GROUP BY B.MOVEDT,B.STRNO
		";
		//echo $sql; exit;
		$i = 0;
		$updatestr = "";
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$sql2 = "
					select MIN(B.MOVENO) as MOVENO,COUNT(*) as AMOT,B.STRNO,B.MOVETO,B.MOVEFM,B.MOVEDT
					from {$this->MAuth->getdb('INVMOVM')} A 
					left join {$this->MAuth->getdb('INVMOVT')} B on A.MOVENO = B.MOVENO 
					where B.MOVEDT = '".$row->MOVEDT."' and STRNO like '%".$row->STRNO."%'
					group by B.STRNO,B.MOVEDT,B.MOVETO,B.MOVEFM
				";
				//echo $sql2;
				$query2 = $this->db->query($sql2);
				if($query2->row()){
					foreach($query2->result() as $row2){
						$sql3 = "
							select B.MOVENO,B.STRNO,convert(varchar(8),B.MOVEDT,112) as MOVEDT,B.MOVEFM
							,B.MOVETO from {$this->MAuth->getdb('INVMOVM')} A
							left join {$this->MAuth->getdb('INVMOVT')} B on A.MOVENO = B.MOVENO 
							where B.MOVENO <> '".$row2->MOVENO."' and B.MOVEDT = '".$row2->MOVEDT."'
							and B.STRNO = '".$row2->STRNO."' and B.MOVETO = '".$row2->MOVETO."' 
							and B.MOVEFM = '".$row2->MOVEFM."'
						";
						//echo $sql3;  
						$query3 = $this->db->query($sql3);
						if($query3->row()){
							foreach($query3->result() as $row3){$i++;
								$updatestr .= "
									delete from {$this->MAuth->getdb('INVMOVT')} 
									where MOVENO = '".$row3->MOVENO."' and MOVEDT = '".$row3->MOVEDT."'
									and STRNO = '".$row3->STRNO."' and MOVETO = '".$row3->MOVETO."' 
									and MOVEFM = '".$row3->MOVEFM."'	
								";
							}
						}
					}
				}
			}
		}
		if($updatestr == ""){
			$response['none'] = true;
			$response['msg'] = "ไม่พบเงื่อนไขที่กำหนดครับ";
			echo json_encode($response); exit;
		}
		$sql = "
			if OBJECT_ID('temp..#UPDSTR') is not null drop table #UPDSTR
			create table #UPDSTR (id varchar(1),msg varchar(max));
			begin tran updatestrno
			begin try
				".$updatestr."
				commit tran updatestrno;
				insert into #UPDSTR select 'Y' as id,'สำเร็จ : ปรับปรุงเลขถังซ้ำตอนโอนย้ายแล้วครับ' as msg;
			end try
			begin catch
				rollback tran updatestrno;
				insert into #UPDSTR select 'N' as id,'บันทึกข้อมูลไม่สำเร็จ : กรุณาติดต่อฝ่ายไอที' as msg;
				return;
			end catch	
		";
		//echo $sql; exit;
		$this->db->query($sql);
		
		$sql = "select * from #UPDSTR";
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$response['status'] = $row->id;
				$response['msg']    = $row->msg;
			}
		}else{
			$response['status'] = false;
			$response['msg']    = "ผิดพลาด";
		}
		echo json_encode($response);
	}
}