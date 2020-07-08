<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/********************************************************
             ______@23/02/2020______
			 Pasakorn Boonded

********************************************************/
class DebtorStopVat_Cancel extends MY_Controller {
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
		$html = "
			<div class='k_tab1' name='home' locat='{$this->sess['branch']}' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}' today='".$this->today('today')."' style='height:calc(100vh - 132px);overflow:auto;background-color:white;'>
				<div class='col-sm-12 col-xs-12' style='height:100%;overflow:auto;font-size:11pt;'>					
					<div class='row' style='height:10%;'>
						<!--div class='col-sm-12 col-xs-12' style='background-color:#2e86c1;border:5px solid white;height:75px;text-align:center;font-size:12pt;color:white;font-weight:bold;'>	
							<br>บันทึกลูกหนี้หยุด Vat<br>
						</div-->
						<div class='col-sm-10 col-xs-10 col-sm-offset-1 text-primary'>	
							<br>
							<div class='col-sm-4'>	
								<div class='form-group'>
									สาขาของสัญญา
									<select id='LOCAT' class='form-control input-sm' >
										<!-- option value='".$this->sess['branch']."'>".$this->sess['branch']."</option -->
										".$this->MMAIN->Option_get_locat($this->sess["branch"])."
									</select>
								</div>
							</div>
							<div class='col-sm-4'>	
								<div class='form-group'>
									วันที่หยุด Vat
									<input type='text' id='STOPDT' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' value='".$this->today('today')."' styl='font-size:10.5pt;'>
								</div>
							</div>
							<div class='col-sm-4'>	
								<div class='form-group'>
									เลขที่ทำการหยุด Vat
									<input type='text' id='CANSTVNO' class='form-control input-sm text-danger' style='font-size:12pt;' readonly>
								</div>
							</div>
							<div class='col-sm-12 text-info'>
								<h4>***ต้องทำการปรับงวดที่ขาดที่ระบบการเงินก่อนทุกครั้ง***</4>
							</div>
							<div class='col-sm-12 col-xs-12 col-sm-offset'><br>	
								<div class='col-sm-12 col-xs-12'>	
									<div class='form-group'>
										เงื่อนไขการหยุด Vat
										<div class='col-sm-12 col-xs-12'style='border:0.1px dotted #d6d6d6;'><br>	
											<div class='col-sm-4'>	
												<div class='form-group'>
													จากเลขที่สัญญา
													<select id='FRMCONTNO' class='form-control input-sm'></select>
												</div>
											</div>
											<div class='col-sm-4'>	
												<div class='form-group'>
													ถึงเลขที่สัญญา
													<select id='TOCONTNO' class='form-control input-sm'></select>
												</div>
											</div>
											<div class='col-sm-4'>	
												<div class='form-group'>
													ยกเลิกหยุด Vat สำหรับลูกหนี้ค้างชำระ >=
													<div class='input-group'>
														<input type='text' id='EXP_PRD' value='0' class='form-control input-sm' style='text-align:right;'>
														<span class='input-group-addon'>งวด</span>
													</div>
												</div>
											</div>
											<div class='col-sm-6'>
											</div>
											<div class='col-sm-2'>
												<br>
												<button id='btnsearch' class='btn btn-info pull-right'style='width:100%;'><span class='glyphicon glyphicon-search'><b>ค้นหา</b></span></button>
											</div>
											<div class='col-sm-2'>
												<br>
												<button id='btnlist' class='btn btn-cyan pull-right'style='width:100%;'><span class='glyphicon glyphicon-list-alt'><b>แสดงที่เลือก</b></span></button>
											</div>
											<div class='col-sm-2'>
												<br>
												<button id='btnlistall' class='btn btn-primary pull-right'style='width:100%;'><span class='glyphicon glyphicon-ok'><b>เลือกทั้งหมด</b></span></button>
											</div><br><br><br><br><br><br>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class='col-sm-10 col-xs-10 col-sm-offset-1' style='height:30%;'>	
						<fieldset style='height:100%'>
							<div style='float:left;height:100%;' class='col-sm-12 col-xs-12'>
								<div class='row' id='' style='height:100%;border:0.1px'><br>
									<div class='row' style='height:100%;border:0.1px solid #bdbdbd;background-color:#eee;'>
										<div class='col-sm-12 col-xs-12' style='height:100%;'>
											<div id='dataTable-stop-vat' class='dataTables_wrapper dt-bootstrap4 table-responsive' style='height:100%;width:100%;overflow:auto;'>
												<table id='dataTable-stopvat' class='table table-bordered dataTable table-hover' stat='' aria-describedby='dataTable_info' cellspacing='0' width='calc(100% - 1px)'>
													<thead>
														<tr role='row' style='height:30px;font-size:8pt;background-color:#e67e22;color:white;'>
															<th width='1%' style='vertical-align:middle;'>เลือก</th>
															<th width='12%' style='vertical-align:middle;'>เลขที่สัญญา</th>
															<th width='12%' style='vertical-align:middle;'>ชื่อ - สกุล ลูกค้า</th>
															<th width='12%' style='vertical-align:middle;'>จำนวนขาดงวด</th>
															<th width='12%' style='vertical-align:middle;'>วันที่หยุด Vat</th>
														</tr>
													</thead>
													<tbody id='data-tbody' style='white-space:nowrap;background-color:white;font-size:9pt;'></tbody>
												</table>
											</div>
										</div>
									</div>
									<div class='row' style='border:0.1px solid #bdbdbd;background-color:#dedede;'>
										<div class='col-sm-12 col-xs-12' style='height:100%;'>	
											<div id='' style='width:100%;overflow:auto;'>
												<table id='' width='calc(100% - 1px)'>
													<tr style='font-size:9pt;'>
														<th width='14%'><button id='btnadd' class='btn btn-primary pull-right' style='width:80%;'><span class='fa fa-plus-square'><b>เพิ่ม</b></span></button></th>
														<th width='14%'><button id='btnshow' class='btn btn-info pull-right' style='width:80%;'><span class='fa fa-folder-open'><b>สอบถาม</b></span></button></th>
														<th width='14%'><button id='btnsave' class='btn btn-cyan pull-right' style='width:80%;'><span class='fa fa-floppy-o'><b>บันทึก</b></span></button></th>
														<th width='14%'><button id='btnclear' class='btn btn-light pull-right'style='width:80%;'><span class='' style='font-color:blue;'><b>Clear</b></span></button></th>
														<th colspan='1' class='text-primary' style='text-align:right;'><b>จำนวนสัญญา</b></th>
														<th width='14%' style='padding:2px;'><input type='text' id='COUNTCONTNO' value='0' class='form-control input-sm' style='text-align:right;' readonly></th>
													</tr>
												</table>
											</div>
										</div>
									</div>
								</div>
							</div>
						</fieldset>
					</div>
				</div>
			</div>
		";
		$html .="<link rel='stylesheet' href='".base_url('public/css/test_korn.css')."'>";
		$html .="<script src='".base_url('public/js/SYS07/DebtorStopVat_Cancel.js')."'></script>";
		echo $html;
	}
	function getSTOPVNO(){
		$LOCAT = $_REQUEST['LOCAT'];
		$STOPDT  = $this->Convertdate(1,$_REQUEST['STOPDT']);
		$FRMCONTNO = $_REQUEST['FRMCONTNO'];
		if($FRMCONTNO <> ''){
			$sql = "
				declare @year varchar(2) = (select SUBSTRING('".$STOPDT."',3,2));
				declare @month varchar(2) = (select SUBSTRING('".$STOPDT."',5,2));
				declare @locat varchar(2) = (select SHORTL from {$this->MAuth->getdb('INVLOCAT')} where LOCATCD = '".$LOCAT."');
				declare @canstvno varchar(4) = (
					select RIGHT('0000'+CAST(MAX(SUBSTRING(CANSTVNO,10,10)+1) as nvarchar(4)), 4) from {$this->MAuth->getdb('CANSTVHD')}
					where LOCAT = '".$LOCAT."' and SUBSTRING(CANSTVNO,3,1) = 'V' 
					and SUBSTRING(CONVERT(varchar(8),STOPDT,112),3,2) = @year and SUBSTRING(convert(varchar(8),STOPDT,112),5,2) = @month
				);
				declare @stopvhd varchar(1) = (
					select count(*) from {$this->MAuth->getdb('CANSTVHD')} where LOCAT = '".$LOCAT."' and SUBSTRING(CANSTVNO,3,1) = 'V' 
					and SUBSTRING(CONVERT(varchar(8),STOPDT,112),3,2) = @year and SUBSTRING(convert(varchar(8),STOPDT,112),5,2) = @month
				);
				--select @stopvhd
				if @stopvhd = 1
				begin
					select @locat+'V-'+@year+@month+@canstvno as CANSTVNO
				end
				else
				begin
					select @locat+'V-'+@year+@month+'0001' as CANSTVNO
				end
			";
			//echo $sql; exit;
			$query = $this->db->query($sql);
			if($query->row()){
				foreach($query->result() as $row){
					$response['CANSTVNO'] = $row->CANSTVNO;
				}
			}
		}else{
			$response['CANSTVNO'] = "";
		}
		echo json_encode($response);
	}
	function ResultStopVat(){
		$LOCAT     = $_REQUEST['LOCAT'];
		$STOPDT    = $_REQUEST['STOPDT'];
		$FRMCONTNO = $_REQUEST['FRMCONTNO'];
		$TOCONTNO  = $_REQUEST['TOCONTNO'];
		$EXP_PRD   = $_REQUEST['EXP_PRD'];
		if($FRMCONTNO == ""){
			$response["error"] = true;
			$response["msg"]   = 'กรุณาเลือกเลขที่สัญญาถึงเลขที่สัญญาก่อนครับ';	
			echo json_encode($response); exit;
		}
		if($TOCONTNO == ""){
			$response["error"] = true;
			$response["msg"]   = 'กรุณาเลือกเลขที่สัญญาถึงเลขที่สัญญาก่อนครับ';	
			echo json_encode($response); exit;
		}
		
		$sql = "
			select A.CONTNO,A.LOCAT,A.CUSCOD,floor(A.EXP_PRD) as EXP_PRD,C.SNAM+C.NAME1+' '+C.NAME2 as CUSNAM from {$this->MAuth->getdb('ARMAST')} A 
			left join {$this->MAuth->getdb('CUSTMAST')} C on A.CUSCOD = C.CUSCOD
			where A.FLSTOPV = 'S' and A.LOCAT = '".$LOCAT."' and A.CONTNO between '".$FRMCONTNO."' and '".$TOCONTNO."'
			and A.EXP_PRD >= '".$EXP_PRD."' 
		";
		//echo $sql; exit; 
		
		$query = $this->db->query($sql);
		$stopvat = ""; $i = 0;
		if($query->row()){
			foreach($query->result() as $row){$i++;
				$stopvat .= "
					<tr class='trow' seq='old'>
						<td>
							<input type='checkbox' id='checkstopvat' class='form-check-input checklist' style='cursor:pointer;max-width:20px;max-height:10px;'
								CONTNO = '".$row->CONTNO."'
								LOCAT  = '".$row->LOCAT."'
								CUSCOD = '".$row->CUSCOD."'
								EXP_PRD= '".$row->EXP_PRD."'
							>
						</td>
						<td>".$row->CONTNO."</td>
						<td>".$row->CUSNAM."</td>
						<td>".$row->EXP_PRD."</td>
						<td>".$STOPDT."</td>
					</tr>
				";	
			}
		}else{
			$stopvat .="<tr class='trow'><td colspan='9' style='color:red;'>ไม่มีข้อมูล</td></tr>";
			$response["error"] = true;
			$response["msg"]   = 'ไม่มีลูกหนี้ตามช่วงที่ระบุครับ';	//echo json_encode($response); //exit;
		}
		$response['stopvat'] = $stopvat;
		$response['countcontno'] = $i;
		echo json_encode($response);
	}
	function SanveCancelStopvat(){
		$LOCAT     = $_REQUEST['LOCAT'];
		$STOPDT    = $this->Convertdate(1,$_REQUEST['STOPDT']);
		$CANSTVNO  = $_REQUEST['CANSTVNO'];
		$FRMCONTNO = $_REQUEST['FRMCONTNO'];
		$TOCONTNO  = $_REQUEST['TOCONTNO'];
		$EXP_PRD   = $_REQUEST['EXP_PRD'];
		$USERID    = $this->sess['USERID'];
		
		//print_r ($SVAT);
		if($FRMCONTNO == ""){
			$response["error"] = true;
			$response["msg"]   = 'กรุณาเลือกเลขที่สัญญาถึงเลขที่สัญญาก่อนครับ';	
			echo json_encode($response); exit;
		}
		if($TOCONTNO == ""){
			$response["error"] = true;
			$response["msg"]   = 'กรุณาเลือกเลขที่สัญญาถึงเลขที่สัญญาก่อนครับ';	
			echo json_encode($response); exit;
		}
		if(isset($_REQUEST['CVAT'])){}else{
			$response["error"] = true;
			$response["msg"]   = 'กรุณาค้นหาสัญญาที่จะหยุด Vat ก่อนครับ';	
			echo json_encode($response); exit;
		}
		$CVAT    = $_REQUEST['CVAT'];
		
		$savesvat = "";
		$sizecus = count($CVAT);
		for($i=0; $i < $sizecus; $i++){
			$savesvat .= "
				update {$this->MAuth->getdb('ARMAST')} set FLSTOPV = '',DTSTOPV = null
				where CONTNO = '".$CVAT[$i][0]."' and LOCAT = '".$LOCAT."'
				
				insert into {$this->MAuth->getdb('CANSTVTR')}(
					[CANSTVNO],[STOPDT],[LOCAT],[CONTNO],[EXP_PRD],[USERID]
					,[INPDT],[CANCELID],[CANCELDT],[MARK],[CUSCOD]
				)values(
					'".$CANSTVNO."','".$STOPDT."','".$LOCAT."','".$CVAT[$i][0]."','".$CVAT[$i][1]."'
					,'".$USERID."',getdate(),null,null,'Y','".$CVAT[$i][2]."'
				)
			";
		}
		//print_r($savesvat);
		$sql = "
			if object_id('tempdb..#Cancelvatstop') is not null drop table #Cancelvatstop;
			create table #Cancelvatstop (id varchar(1),msg varchar(max)); 	
			begin tran Vatstop
			begin try
				insert into {$this->MAuth->getdb('CANSTVHD')}(
					[CANSTVNO],[LOCAT],[STOPDT],[EXP_PRD],[USERID],[INPDT]
					,[CANCELID],[CANCELDT],[FRMCONTNO],[TOCONTNO]
				)values(
					'".$CANSTVNO."','".$LOCAT."','".$STOPDT."','".$EXP_PRD."','".$USERID."'
					,getdate(),null,null,'".$FRMCONTNO."','".$TOCONTNO."'
				)
				".$savesvat."
				
				insert into #Cancelvatstop select 'Y' as id,'สำเร็จ บันทึกข้อมูลเรียบร้อยแล้ว' as msg;
				commit tran Vatstop;
			end try
			begin catch
				rollback tran Vatstop;
				insert into #Cancelvatstop select 'N' as id,'บันทึกข้อมูลไม่สำเร็จ : กรุณาติดต่อฝ่ายไอที' as msg;
				return;
			end catch
		";
		//echo $sql; exit;
		$this->db->query($sql);	
		
		$sql = "
			select * from #Cancelvatstop
		";
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