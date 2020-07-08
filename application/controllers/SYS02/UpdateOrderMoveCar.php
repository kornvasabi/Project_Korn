<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/********************************************************
             ______@16/04/2020______
			 Pasakorn Boonded

********************************************************/
class UpdateOrderMoveCar extends MY_Controller {
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
							<br>โปรแกรมปรับปรุงลำดับการโอนย้ายรถ<br>
						</div>
						<div class='col-sm-10 col-xs-10 col-sm-offset-1'><br>	
							<div class='col-sm-12 col-xs-12'>	
								<div class='form-group'>
									<div class='col-sm-12 col-xs-12'>	
										<div class='col-sm-6'>
											<div class='form-group'>
												เลขตัวถัง
												<input type='text' id='STRNO' class='form-control input-sm' placeholder='เลขถัง' value=''>
											</div>
										</div>
										<div class='col-sm-6'>
											<div class='form-group'>
												<br>
												<button id='btnsearch' type='button' class='btn btn-cyan' style='width:50%; align:center;'>
													<span class='glyphicon glyphicon-search'><b>ค้าหารายการปรับปรุง</b></span>
												</button>
											</div>
										</div>
									</div>
									<div class='col-sm-12 col-xs-12'>
										<br>
										<div class='row' style='height:51%;border:0.1px dotted #bdbdbd;'>
											<div class='col-sm-12 col-xs-12' style='height:250px;border:5px solid white;'>
												<div id='dataTable-fixed-Invtran' class='dataTables_wrapper dt-bootstrap4 table-responsive' style='height:100%;width:100%;overflow:auto;'>
													<table id='dataTables-Invtran' class='table table-bordered dataTable table-hover' stat='' aria-describedby='dataTable_info' cellspacing='0' width='calc(100% - 1px)'>
														<thead>
															<tr role='row' style='height:25px;font-size:8pt;background-color:#2fa39d;color:white;'>
																<th width='11%' style='text-align:center;'>#</th>
																<th width='11%' style='text-align:center;'>เลขตัวถัง</th>
																<th width='11%' style='text-align:center;'>สาขารับ</th>
																<th width='11%' style='text-align:center;'>สาขาปัจจุบัน</th>
																<th width='11%' style='text-align:center;'>วันที่ขาย</th>
																<th width='11%' style='text-align:center;'>ประเภทการขาย</th>
																<th width='11%' style='text-align:center;'>เลขที่สัญญา</th>
																<th width='11%' style='text-align:center;'>กลุ่มสินค้า</th>
																<th width='11%' style='text-align:center;'>ยี่ห้อ</th>
																<th width='11%' style='text-align:center;'>รุ่น</th>
																
																<th width='11%' style='text-align:center;'>แบบ</th>
																<th width='11%' style='text-align:center;'>สี</th>
																<th width='11%' style='text-align:center;'>CC</th>
																<th width='11%' style='text-align:center;'>STAT</th>
																<th width='11%' style='text-align:center;'>เลขที่ใบรับ</th>
																<th width='11%' style='text-align:center;'>วันที่รับ</th>
																<th width='11%' style='text-align:center;'>เลขเครื่อง</th>
																<th width='11%' style='text-align:center;'>FLAG</th>
															</tr>
														</thead>
														<tbody  id='searchupdate' style='white-space:nowrap;background-color:white;font-size:9pt;'></tbody>
													</table>
												</div>
											</div>
										</div>
									</div> 
									<div id='invmovt' class='col-sm-12 col-xs-12'>
										<br>
										<div class='row' style='height:51%;border:0.1px dotted #bdbdbd;'>
											<div class='col-sm-12 col-xs-12' style='height:50%;border:5px solid white;'>
												<div id='dataTable-fixed-Invmovt' class='dataTables_wrapper dt-bootstrap4 table-responsive' style='height:100%;width:100%;overflow:auto;'>
													<table id='dataTables-Invmovt' class='table table-bordered dataTable table-hover' stat='' aria-describedby='dataTable_info' cellspacing='0' width='calc(100% - 1px)'>
														<thead>
															<tr role='row' style='height:25px;font-size:8pt;background-color:#b7950b;color:white;'>
																<th width='11%' style='text-align:center;'>เลขที่การย้าย</th>
																<th width='11%' style='text-align:center;'>เลขตัวถัง</th>
																<th width='11%' style='text-align:center;'>วันที่ย้าย</th>
																<th width='11%' style='text-align:center;'>ย้ายจากสาขา</th>
																<th width='11%' style='text-align:center;'>ไปสาขา</th>
																<th width='11%' style='text-align:center;'>ลำดับการย้าย</th>
															</tr>
														</thead>
														<tbody style='white-space:nowrap;background-color:white;font-size:9pt;'></tbody>
													</table><br><br><br><br>
												</div>
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
		$html .="<script src='".base_url('public/js/SYS02/UpdateOrderMoveCar.js')."'></script>";
		echo ($html);
	}
	function Searchdetailcar(){
		$STRNO = $_REQUEST['STRNO'];
		
		$sql = "
			select top 1000 STRNO,CRLOCAT,RVLOCAT,CONVERT(varchar(8),SDATE,112) as SDATE,TSALE,CONTNO,GCODE,TYPE
			,MODEL,BAAB,COLOR,CC,STAT,RECVNO,convert(varchar(8),RECVDT,112) as RECVDT,ENGNO,FLAG 
			from {$this->MAuth->getdb('INVTRAN')} where STRNO like '%".$STRNO."%' order by STRNO
		";
		$invtran = ""; $i=0;
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){$i++;
				$invtran .="
					<tr>
						<td>{$i}</td>
						<td>{$row->STRNO}</td>
						<td>{$row->CRLOCAT}</td>
						<td>{$row->RVLOCAT}</td>
						<td>".$this->Convertdate(2,$row->SDATE)."</td>
						<td>{$row->TSALE}</td>
						<td>{$row->CONTNO}</td>
						<td>{$row->GCODE}</td>
						<td>{$row->TYPE}</td>
						<td>{$row->MODEL}</td>
						<td>{$row->BAAB}</td>
						<td>{$row->COLOR}</td>
						<td>{$row->CC}</td>
						<td>{$row->STAT}</td>
						<td>{$row->RECVNO}</td>
						<td>".$this->Convertdate(2,$row->RECVDT)."</td>
						<td>{$row->ENGNO}</td>
						<td>{$row->FLAG}</td>
					</tr>
				";
			}
		}else{
			$invtran .="<tr class='trow'><td colspan='17' style='color:red;'>ไม่มีข้อมูล</td></tr>";
		}
		$response['invtran'] = $invtran;
		
		echo json_encode($response);
	}
	function Updateordercar(){
		$STRNO = $_REQUEST['STRNO'];
		
		$sql = "
			select top 1000 STRNO from {$this->MAuth->getdb('INVTRAN')} 
			where STRNO like '%".str_replace(chr(0),'',$STRNO)."%' order by STRNO
		";
		$ordercar = "";
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$z = 1;
				$sql2 = "
					select MOVENO,STRNO,MOVEDT,MOVEFM,MOVETO,MOVSEQ,INPDT 
					from {$this->MAuth->getdb('INVMOVT')} A 
					where STRNO = '".str_replace(chr(0),'',$row->STRNO)."' order by MOVEDT,MOVENO
				";
				$query = $this->db->query($sql2);
				if($query->row()){
					foreach($query->result() as $rowa){
						$ordercar .= "
							update {$this->MAuth->getdb('INVMOVT')} set MOVSEQ = ".$z++."
							where MOVENO = '".str_replace(chr(0),'',$rowa->MOVENO)."' 
							and STRNO = '".str_replace(chr(0),'',$rowa->STRNO)."'
						";
					}
				}
			}
		}
		//echo $ordercar;
		$sql = "
			if OBJECT_ID('temp..#ordercar') is not null drop table #ordercar
			create table #ordercar (id varchar(1),msg varchar(max));
			begin tran ordermovt
			begin try
				".$ordercar."
				commit tran ordermovt;
				insert into #ordercar select 'Y' as id,'สำเร็จ : ปรับปรุงลำดับการย้ายรถเรียบร้อยแล้วครับ' as msg;
			end try
			begin catch
				rollback tran ordermovt;
				insert into #ordercar select 'N' as id,'ปรับปรุงข้อมูลไม่สำเร็จ : กรุณาติดต่อฝ่ายไอที' as msg;
				return;
			end catch
		";
		//echo $sql; exit;
		$this->db->query($sql);
		$sql = "select * from #ordercar";
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$response['status'] = $row->id;
				$response['msg']    = $row->msg;
			}
		}else{
			$response['status'] = false;
			$response['msg'] = "ผิดพลาด";
		}
		echo json_encode($response);
	}
}