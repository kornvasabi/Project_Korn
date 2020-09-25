<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/********************************************************
             _____05/08/2563______
            / / _ _   _ _     __ 
           / // __ \ / __ \ / __ \
       _ _/ // /_/ // / / // /_/ /
     /_ _ _/ \_ _ //_/ /_/ \__  /
                          _ _/ /
                         /___ /
********************************************************/
class AdjustCONTNO extends MY_Controller {
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
			<div class='tab1' name='home' locat='{$this->sess['branch']}' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}'>
				<br>
				<div class='row col-sm-6 col-sm-offset-3' style='border:0.1px dotted black;'>
					<div class='row'>
						<div class='col-sm-12'>	
							<h3>การปรับปรุงงวดที่ค้างชำระ</h3>
						</div>
					</div>
					<br>
					<div class='row'>
						<div class='col-sm-6'>	
							<div class='form-group'>
								รหัสสาขา
								<select id='LOCAT' class='form-control' title='เลือก'  multiple data-actions-box='true' data-size='8' data-live-search='true'>
									".$this->MMAIN->Option_get_locat(null)."
								</select>
							</div>
						</div>
						<div class='col-sm-6'>	
							<div class='form-group'>
								ลูกหนี้ ณ วันที่
								<input type='text' id='ADJDT' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' placeholder='ลูกหนี้ ณ วันที่' value='".$this->today('today')."'>
							</div>
						</div>
					</div>
					<div class='row'>
						<div class='col-sm-6 col-sm-offset-6'>	
							<div class='form-group'>
								<button id='btnAdjustNOPAY' class='btn btn-warning btn-block'><span class='glyphicon glyphicon-pencil'> ปรับปรุงงวดที่ขาด</span></button>
							</div>
						</div>
					</div>
				</div>
				
				<br><br>
				<div class='row col-sm-6 col-sm-offset-3' style='border:0.1px dotted black;'>
					<div class='row'>					
						<div class='col-sm-12'>	
							<h3>ปรับปรุงจำนวนวันที่ค้างชำระและปรับปรุงเบี้ยปรับ ณ วันปัจจุบัน</h3>
						</div>
					</div>
					<div class='row'>
						<div class='col-sm-6'>	
							<div class='form-group'>
								จากเลขที่สัญญา 
								<input type='text' id='SCONTNO' class='form-control input-sm' placeholder='จากเลขที่สัญญา '>
							</div>
						</div>
						<div class='col-sm-6'>	
							<div class='form-group'>
								ถึงเลขที่สัญญา 
								<input type='text' id='ECONTNO' class='form-control input-sm' placeholder='ถึงเลขที่สัญญา '>
							</div>
						</div>
					</div>
					<div class='row'>	
						<div class='col-sm-6'>	
							<div class='form-group'>
								<button id='btnAdjustHP' class='btn btn-warning btn-block'><span class='glyphicon glyphicon-pencil'> ปรับปรุงเบี้ยปรับ</span></button>
							</div>
						</div>
						<div class='col-sm-6'>	
							<div class='form-group'>
								<button id='btnAdjustDeley' class='btn btn-warning btn-block'><span class='glyphicon glyphicon-pencil'> ปรับปรุงวันค้างชำระ</span></button>
							</div>
						</div>
					</div>
				</div>
			</div>
		";
		
		$html.= "<script src='".base_url('public/js/SYS06/AdjustCONTNO.js')."'></script>";
		echo $html;
	}
	
	function getCONTNO(){
		$LOCAT = $_POST["LOCAT"];
		
		$cond = "";
		if(is_array($LOCAT)){
			$data = "";
			foreach($LOCAT as $key => $value){
				$data .= ($data != ""?",":"");
				$data .= "'".$value."'";
			}
			$cond .= " and LOCAT in ({$data})";
		}
		$sql = "
			select LOCAT,count(*) cnt from {$this->MAuth->getdb('ARMAST')}
			where 1=1 {$cond}
			group by LOCAT
		";
		$query = $this->db->query($sql);
		
		$html = array();
		if($query->row()){
			foreach($query->result() as $row){
				$html[] = array($row->LOCAT,$row->cnt);
			}
		}
		
		$this->response["html"] = $html;
		echo json_encode($this->response);
	}
	
	function adjustCONTNO(){
		//$CONTNO = $_POST["CONTNO"];
		$CONTNO = '';
		$LOCAT = $_POST["LOCAT"];
		$ADJDT = $this->Convertdate(1,$_POST["ADJDT"]);
		
		$sql = "
			if OBJECT_ID('tempdb..#adjustTemp') is not null drop table #adjustTemp;
			create table #adjustTemp (id varchar(20),contno varchar(20),msg varchar(max));
			
			SET NOCOUNT ON;
			begin tran leasingTran
			begin try
				declare @LOCAT varchar(5) = '{$LOCAT}';
				declare @ADJDT datetime = '{$ADJDT}';
				
				update a
				set a.EXP_AMT=isnull(b.EXP_AMT,0)
					,a.EXP_PRD=isnull(b.EXP_PRD,0)
					,a.EXP_FRM=isnull(b.EXP_FRM,0)
					,a.EXP_TO=isnull(b.EXP_TO,0)
				--select * 
				from {$this->MAuth->getdb('ARMAST')} a
				left join (
					select CONTNO,COUNT(*) as EXP_PRD
						,SUM(isnull(DAMT,0)-isnull(PAYMENT,0)) as EXP_AMT
						,MIN(NOPAY) as EXP_FRM
						,MAX(NOPAY) as EXP_TO
					from {$this->MAuth->getdb('ARPAY')}
					where 1=1 and DAMT>PAYMENT and DDATE <= @ADJDT
					group by CONTNO
				) as b on a.CONTNO=b.CONTNO
				where a.LOCAT=@LOCAT
			
				insert into {$this->MAuth->getdb('hp_UserOperationLog')} (userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
				values ('".$this->sess["IDNo"]."','SYS06::ปรับปรุงงวดที่ขาด ',' ".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');

				insert into #adjustTemp select 'S',@LOCAT,'ปรับปรุงงวดที่ขาด ของสาขา '+@LOCAT+' แล้วครับ';
				commit tran leasingTran;
			end try
			begin catch
				rollback tran leasingTran;
				insert into #adjustTemp select 'E','',ERROR_MESSAGE();
			end catch
		";
		//echo $sql; exit;
		$this->db->query($sql);
		$sql = "select * from #adjustTemp";
		$query = $this->db->query($sql);
		
		if($query->row()){
			foreach($query->result() as $row){
				if($row->id == "S"){
					$this->response["error"] = false;
					$this->response["successMessage"] = $row->msg;
				}else{
					$this->response["error"] = true;		
					$this->response["errorMessage"] = $row->msg;
				}
			}
		}else{
			$this->response["error"] = true;
			$this->response["errorMessage"] = 'ผิดพลาดไม่สามารถทำรายการได้ในขณะนี้ โปรดติดต่อฝ่ายไอที';
		}
		
		echo json_encode($this->response);
	}
	
	function getCONTNODelay(){
		$LOCAT = $_POST["LOCAT"];
		$ADJDT = $this->Convertdate(1,$_POST["ADJDT"]);
		$SCONTNO = $_POST["SCONTNO"];
		$ECONTNO = $_POST["ECONTNO"];
		
		$cond = "";
		if(is_array($LOCAT)){
			$data = "";
			foreach($LOCAT as $key => $value){
				$data .= ($data != ""?",":"");
				$data .= "'".$value."'";
			}
			$cond .= " and LOCAT in ({$data})";
		}
		
		if($SCONTNO != "" && $ECONTNO != ''){
			$cond .= " and CONTNO between '{$SCONTNO}' and '{$ECONTNO}'";
		}else if($SCONTNO == "" && $ECONTNO != ''){
			$cond .= " and CONTNO = '{$ECONTNO}'";
		}else if($SCONTNO != "" && $ECONTNO == ''){
			$cond .= " and CONTNO = '{$SCONTNO}'";
		}else{
			$this->response["error"] = true;		
			$this->response["errorMessage"] = "ปรับปรุงวันค้างชำระ โปรดระบุเลขที่สัญญาด้วยครับ";
			echo json_encode($this->response); exit;
		}
		
		$sql = "
			select LOCAT,count(*) as cnt from {$this->MAuth->getdb('ARMAST')}
			where 1=1 {$cond}
			group by LOCAT
		";
		$query = $this->db->query($sql);
		
		$html = array();
		if($query->row()){
			foreach($query->result() as $row){
				$html[] = array($row->LOCAT,$row->cnt);
			}
		}
		
		$this->response["html"] = $html;
		echo json_encode($this->response);
	}
	
	function adjustDelayCONTNO(){
		$LOCAT = $_POST["LOCAT"];
		$ADJDT = $this->Convertdate(1,$_POST["ADJDT"]);
		$SCONTNO = $_POST["SCONTNO"];
		$ECONTNO = $_POST["ECONTNO"];
		
		$cond = "";
		if($SCONTNO != "" && $ECONTNO != ''){
			$cond .= " and CONTNO between '{$SCONTNO}' and '{$ECONTNO}'";
		}else if($SCONTNO == "" && $ECONTNO != ''){
			$cond .= " and CONTNO = '{$ECONTNO}'";
		}else if($SCONTNO != "" && $ECONTNO == ''){
			$cond .= " and CONTNO = '{$SCONTNO}'";
		}
		
		$sql = "
			if OBJECT_ID('tempdb..#adjustTemp') is not null drop table #adjustTemp;
			create table #adjustTemp (id varchar(20),contno varchar(20),msg varchar(max));
			
			SET NOCOUNT ON;
			begin tran leasingTran
			begin try
				declare @LOCAT varchar(5) = '{$LOCAT}';
				declare @ADJDT datetime = '{$ADJDT}';
				
				update a
				set a.EXP_DAY= isnull(datediff(day,(
					select MIN(DDATE) as DDATE from {$this->MAuth->getdb('ARPAY')}
					where DAMT > PAYMENT and a.CONTNO=CONTNO
						and DDATE < @ADJDT
				),@ADJDT),0)
				from {$this->MAuth->getdb('ARMAST')} a
				where a.LOCAT=@LOCAT {$cond}
				
				
				insert into {$this->MAuth->getdb('hp_UserOperationLog')} (userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
				values ('".$this->sess["IDNo"]."','SYS06::ปรับปรุงวันค้างชำระ ',' ".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');

				insert into #adjustTemp select 'S',@LOCAT,'ปรับปรุงวันค้างชำระ ของสาขา '+@LOCAT+' แล้วครับ';
				commit tran leasingTran;
			end try
			begin catch
				rollback tran leasingTran;
				insert into #adjustTemp select 'E','',ERROR_MESSAGE();
			end catch
		";
		//echo $sql; exit;
		$this->db->query($sql);
		$sql = "select * from #adjustTemp";
		$query = $this->db->query($sql);
		
		if($query->row()){
			foreach($query->result() as $row){
				if($row->id == "S"){
					$this->response["error"] = false;
					$this->response["successMessage"] = $row->msg;
				}else{
					$this->response["error"] = true;		
					$this->response["errorMessage"] = $row->msg;
				}
			}
		}else{
			$this->response["error"] = true;
			$this->response["errorMessage"] = 'ผิดพลาดไม่สามารถทำรายการได้ในขณะนี้ โปรดติดต่อฝ่ายไอที';
		}
		
		echo json_encode($this->response);
	}
	
	function getCONTNOHP(){
		$LOCAT = $_POST["LOCAT"];
		$ADJDT = $this->Convertdate(1,$_POST["ADJDT"]);
		$SCONTNO = $_POST["SCONTNO"];
		$ECONTNO = $_POST["ECONTNO"];
		
		$cond = "";
		if(is_array($LOCAT)){
			$data = "";
			foreach($LOCAT as $key => $value){
				$data .= ($data != ""?",":"");
				$data .= "'".$value."'";
			}
			$cond .= " and LOCAT in ({$data})";
		}
		
		if($SCONTNO != "" && $ECONTNO != ''){
			$cond .= " and CONTNO between '{$SCONTNO}' and '{$ECONTNO}'";
		}else if($SCONTNO == "" && $ECONTNO != ''){
			$cond .= " and CONTNO = '{$ECONTNO}'";
		}else if($SCONTNO != "" && $ECONTNO == ''){
			$cond .= " and CONTNO = '{$SCONTNO}'";
		}else{
			$this->response["error"] = true;		
			$this->response["errorMessage"] = "ปรับปรุงเบี้ยปรับ โปรดระบุเลขที่สัญญาด้วยครับ";
			echo json_encode($this->response); exit;
		}
		
		$sql = "
			select LOCAT,count(*) as cnt from {$this->MAuth->getdb('ARMAST')}
			where 1=1 {$cond}
			group by LOCAT
		";
		$query = $this->db->query($sql);
		
		$html = array();
		if($query->row()){
			foreach($query->result() as $row){
				$html[] = array($row->LOCAT,$row->cnt);
			}
		}
		
		$this->response["html"] = $html;
		echo json_encode($this->response);
	}
	
	function adjustHPCONTNO(){
		$LOCAT = $_POST["LOCAT"];
		$ADJDT = $this->Convertdate(1,$_POST["ADJDT"]);
		$SCONTNO = $_POST["SCONTNO"];
		$ECONTNO = $_POST["ECONTNO"];
		
		$cond = "";
		if($SCONTNO != "" && $ECONTNO != ''){
			$cond .= " and CONTNO between '{$SCONTNO}' and '{$ECONTNO}'";
		}else if($SCONTNO == "" && $ECONTNO != ''){
			$cond .= " and CONTNO = '{$ECONTNO}'";
		}else if($SCONTNO != "" && $ECONTNO == ''){
			$cond .= " and CONTNO = '{$SCONTNO}'";
		}
		
		$sql = "
			if OBJECT_ID('tempdb..#adjustTemp') is not null drop table #adjustTemp;
			create table #adjustTemp (id varchar(20),contno varchar(20),msg varchar(max));
			
			SET NOCOUNT OFF;
			begin tran leasingTran
			begin try
				declare @LOCAT varchar(5) = '{$LOCAT}';
				declare @ADJDT datetime = '{$ADJDT}';
				declare @CONTNO varchar(12);
				
				declare cs cursor for (
					select CONTNO from {$this->MAuth->getdb('ARMAST')}
					where LOCAT=@LOCAT {$cond}
				);	
				open cs;
				
				fetch next from cs into @CONTNO;
				while @@FETCH_STATUS = 0
				begin
					exec {$this->MAuth->getdb('FN_JD_LatePenalty')} @contno=@CONTNO ,@dt=@ADJDT;
					fetch next from cs into @CONTNO;
				end
				close cs;
				deallocate cs;
				
				insert into {$this->MAuth->getdb('hp_UserOperationLog')} (userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
				values ('".$this->sess["IDNo"]."','SYS06::ปรับปรุงเบี้ยปรับ ',' ".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');

				insert into #adjustTemp select 'S',@LOCAT,'ปรับปรุงเบี้ยปรับ ของสาขา '+@LOCAT+' แล้วครับ';
				commit tran leasingTran;
			end try
			begin catch
				rollback tran leasingTran;
				insert into #adjustTemp select 'E','',ERROR_MESSAGE();
			end catch
		";
		//echo $sql; exit;
		if($this->db->query($sql)){
			$sql = "select * from #adjustTemp";
			$query = $this->db->query($sql);
			
			if($query->row()){
				foreach($query->result() as $row){
					if($row->id == "S"){
						$this->response["error"] = false;
						$this->response["successMessage"] = $row->msg;
					}else{
						$this->response["error"] = true;		
						$this->response["errorMessage"] = $row->msg;
					}
				}
			}else{
				$this->response["error"] = true;
				$this->response["errorMessage"] = 'ผิดพลาดไม่สามารถทำรายการได้ในขณะนี้ โปรดติดต่อฝ่ายไอที';
			}
			
			echo json_encode($this->response);			
		}
	}
}




















