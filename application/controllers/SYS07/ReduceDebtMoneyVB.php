<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/********************************************************
             ______@21/02/2020______
			 Pasakorn Boonded

********************************************************/
class ReduceDebtMoneyVB extends MY_Controller {
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
					<div class='row' style='height:90%;'>
						<div class='col-sm-10 col-xs-10 col-sm-offset-1 text-primary'>	
							<br>
							<div class='col-sm-3'>	
								<div class='form-group'>
									ออกใบลดหนี้ที่สาขา
									<select id='LOCAT' class='form-control input-sm' data-placeholder='สาขา'></select>
								</div>
							</div>
							<div class='col-sm-3'>	
								<div class='form-group' >
									ประเภทใบลดหนี้
									<select id='TAXTYP' value='2' class='form-control input-sm' disabled><option>2	</option></select>
								</div>
							</div>
							<div class='col-sm-3'>	
								<div class='form-group'>
									อ้างถึงใบกำกับเลขที่
									<select id='TAXNO' class='form-control input-sm' data-placeholder='เลือก'></select>
								</div>
							</div>
							<div class='col-sm-3'>	
								<div class='form-group' >
									เลขตัวถัง
									<select id='STRNO' class='form-control input-sm' data-placeholder='เลือก'></select>
								</div>
							</div>
							<div class='col-sm-3'>	
								<div class='form-group'>
									เลขที่ใบลดหนี้
									<input type='text' id='DEBTNO' class='form-control input-sm'>
								</div>
							</div>
							<div class='col-sm-3'>	
								<div class='form-group'>
									วันที่ออก
									<input type='text' id='TAXDT' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' style='font-size:10.5pt'>
								</div>
							</div>
							<div class='col-sm-3'>	
								<div class='form-group'>
									ลงวันที่
									<input type='text' id='REFDT' class='form-control input-sm' readonly>
								</div>
							</div>
							<div class='col-sm-3'>	
								<div class='form-group'>
									เลขที่ใบรับสินค้า
									<input type='text' id='RECVNO' class='form-control input-sm' readonly>
								</div>
							</div>
						</div>
						<div class='col-sm-10 col-xs-10 col-sm-offset-1'><br>	
							<div class='col-sm-12 col-xs-12'>	
								<div class='form-group'>
									<div class='col-sm-12 col-xs-12' style='border:0.1px dotted #d6d6d6;background-color:#148f77;'>	
										<div class='col-sm-12 col-xs-12'>
											<div class='col-sm-7' style='text-align:right;'>
											</div>
											<div class='col-sm-4' style='text-align:left;color:#f5cc28;'>	
												<div class='form-group'>
													<b>มูลค่าสินค้า</b>
													<div class='input-group'>
														<!--input id='NETAMT' value='0.00' style='text-align:right;' class='form-control input-sm jzAllowNumber' type='text' onkeyup='format(this)' onchange='format(this)' onblur='if(this.value.indexOf('.')==-1)this.value=this.value+'.00''--> 
														<input type='text' id='NETAMT' class='form-control input-sm jzAllowNumber' style='text-align:right;' value='0.00'>
														<span class='input-group-addon'>บาท</span>
													</div>
												</div>
											</div>
										</div>
										<div class='col-sm-12 col-xs-12'>
											<div class='col-sm-7' style='text-align:center;'>
												<br>
												<input id='stana' style='text-align:center;font-size:20px;' type='text' value='***ยกเลิก***' id='VATAMT' class='form-control input-sm text-danger bg-warning' readonly>
											</div>
											<div class='col-sm-4' style='text-align:left;color:#f5cc28;'>	
												<div class='form-group'>
													<b>ภาษีมูลค่าเพิ่ม</b>
													<div class='input-group'>
														<input type='text' id='VATAMT' class='form-control input-sm jzAllowNumber text-primary' style='text-align:right;' value='0.00'>
														<span class='input-group-addon'>บาท</span>
													</div>
												</div>
											</div>
										</div>
										<div class='col-sm-12 col-xs-12'>
											<div class='col-sm-7' style='text-align:right;'>
											</div>
											<div class='col-sm-4' style='text-align:left;color:#f5cc28;'>	
												<div class='form-group'>
													<b>ยอดรวมสุทธิ</b>
													<div class='input-group'>
														<input type='text' id='TOTAMT' class='form-control input-sm jzAllowNumber text-danger' style='text-align:right;' value='0.00'>
														<span class='input-group-addon'>บาท</span>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class='col-sm-2 col-sm-2'></div>
								<div class='col-sm-2 col-sm-2'>
									<br>
									<button id='btnaddRD' type='button' class='btn btn-info'style='width:100%;'><span class='fa fa-plus-square'><b>เพิ่ม</b></span></button>
								</div>
								<div class='col-sm-2 col-sm-2'>
									<br>
									<button id='btnsaveRD' type='button' class='btn btn-primary' style='width:100%;'><span class='fa fa-floppy-o'><b>บันทึก</b></span></button>
								</div>
								<div class='col-sm-2 col-sm-2'>
									<br>
									<button id='btndelRD' type='button' class='btn btn-info btn btn-danger'style='width:100%;'><span class='glyphicon glyphicon-trash'><b>ลบ</b></span></button>
								</div><br><div class='col-sm-2 col-sm-2'>
									<br>
									<button id='btnshowRD' type='button' class='btn btn-info btn btn-cyan'style='width:100%;'><span class='fa fa-folder-open'><b>สอบถาม</b></span></button>
								</div><br><div class='col-sm-2 col-sm-2'>
									<br>
									<button id='btnclearRD' type='button' class='btn btn-info btn btn-light'style='width:100%;'><span class='' style='color:blue;'><b>Clear</b></span></button>
								</div><br>
							</div>
						</div>
					</div>
				</div>
			</div>
		";
		$html .="<script src='".base_url('public/js/SYS07/ReduceDebtMoneyVB.js')."'></script>";
		echo $html;
	}
	function getTAXNO(){
		$TAXNO = str_replace(chr(0),'',$_REQUEST['TAXNO']);
		$LOCAT = str_replace(chr(0),'',$_REQUEST['LOCAT']);
		$response = array();
		$sql = "
			select TAXNO,CONVERT(varchar(8),TAXDT,112) as TAXDT,REFNO,TAXTYP 
			from {$this->MAuth->getdb('TAXBUY')} 
			where TAXNO = '".$TAXNO."' and TAXTYP = 'B' and LOCAT = '".$LOCAT."'
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$response['TAXDT'] = $this->Convertdate(2,$row->TAXDT);
				$response['REFNO'] = $row->REFNO;
			}
		}
		echo json_encode($response);
	}
	function getSTRNO(){
		$STRNO = $_REQUEST['STRNO'];
		$response = array();
		
		$sql = "
			select STRNO,FLAG,NETCOST,CRVAT,TOTCOST 
			from {$this->MAuth->getdb('INVTRAN')} where STRNO = '".$STRNO."'
		";
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				if($row->FLAG == 'C'){
					$response["error"] = true;
					$response["msg"] = "รถเลขถังตัวนี้ถูกขายไปแล้ว";
					echo json_encode($response); exit;
				}
			}
		}
		echo json_encode($response);
	}
	function getTAXDT(){
		$TAXDT = $this->Convertdate(1,$_REQUEST['TAXDT']);
		$LOCAT = $_REQUEST['LOCAT'];
		$response = array();
		if($LOCAT ==""){
			$response["error"] = true;
			$response["msg"] = "ไม่พบรหัสสาขา กรุณาเลือกรหัสสาขาก่อนครับ";
			echo json_encode($response); exit;
		}
		$sql = "
			SELECT RIGHT(YEAR('".$TAXDT."'),2) as Yyear ,RIGHT('0' + RTRIM(MONTH('".$TAXDT."')), 2) as Mmonth
		";
		//Right(Year('20260203'),2)
		//echo $sql; exit;
		$query = $this->db->query($sql);
		$year = ""; $month = "";
		if($query->row()){
			foreach($query->result() as $row){
				$year = $row->Yyear;
				$month = $row->Mmonth;
			}
		}
		$sql = "
			declare @taxdt varchar(4) = (select YEAR('".$TAXDT."'));
			declare @locat varchar(3) = (select SHORTL from {$this->MAuth->getdb('INVLOCAT')} where LOCATCD = '".$LOCAT."');
			declare @tcby  varchar(4) = (select RIGHT('0000'+CAST(MAX(CAST(coalesce(L_TCBUY,0) AS int) + 1) as nvarchar(4)), 4) 
			as TCBUY from {$this->MAuth->getdb('LASTNO')} where LOCAT = '".$LOCAT."' and CR_YEAR = @taxdt and CR_MONTH = '".$month."');
			
			select Llocat+'W-'+year1+CR_MONTH+@tcby as TAXNO
			from (
				select @locat Llocat,LOCAT,CR_YEAR,RIGHT(YEAR(CR_YEAR),2) as year1,CR_MONTH,coalesce(L_TCBUY,0) as L_TCBUY  
				from {$this->MAuth->getdb('LASTNO')} where LOCAT = '".$LOCAT."' and CR_YEAR = @taxdt 
				and CR_MONTH = '".$month."'	
			)a
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$response['DEBTNO'] = $row->TAXNO;
			}
		}else{
			$sql = "
				declare @locat varchar(3) = (select SHORTL from {$this->MAuth->getdb('INVLOCAT')} 
				where LOCATCD = '".$LOCAT."');
				declare @date date = '".$TAXDT."'
				select @locat+'W-'+CONVERT(varchar(4),@date,12)+'0001' as DEBTNO
			";
			//echo $sql; exit;
			$query = $this->db->query($sql);
			if($query->row()){
				foreach($query->result() as $row){
					$response['DEBTNO'] = $row->DEBTNO;
				}
			}
		}
		echo json_encode($response);
	}
	function getVATAMT(){
		$NETAMT = $_REQUEST['NETAMT'];
		$response = array();
		$vat = ''; $vat1 = ''; $vat2 = ''; $netamt = "";
		$netamt = str_replace(',','',$NETAMT);
		$vat1 = "7";
		$vat2 = "100";
		$vat = $netamt * $vat1 / $vat2;
		$response['netamt']   = number_format($NETAMT,2);
		$response['totalvat'] = number_format($netamt * $vat1 / $vat2,2);
		$response['total']    = number_format($vat + $netamt,2);
		echo json_encode($response);
	}
	function Save_reducemoney(){
		$LOCAT 	 = $_REQUEST["LOCAT"];
		$TAXTYP  = $_REQUEST["TAXTYP"];
		$TAXNO 	 = $_REQUEST["TAXNO"];
		$STRNO 	 = $_REQUEST["STRNO"];
		$DEBTNO  = $_REQUEST["DEBTNO"];
		$TAXDT 	 = $this->Convertdate(1,$_REQUEST["TAXDT"]);
		$REFDT 	 = $this->Convertdate(1,$_REQUEST["REFDT"]);
		$RECVNO  = $_REQUEST["RECVNO"];
		$NETAMT  = str_replace(',','',$_REQUEST["NETAMT"]);
		$VATAMT  = str_replace(',','',$_REQUEST["VATAMT"]);
		$TOTAMT  = str_replace(',','',$_REQUEST["TOTAMT"]);
		
		$USERID  = $this->sess["USERID"];
		if($LOCAT == ''){
			$response["error"] = true;
			$response["msg"] = "กรุณาเลือกออกใบลดหนี้ที่สาขาก่อนครับ";
			echo json_encode($response); exit;
		}
		if($TAXNO == ''){
			$response["error"] = true;
			$response["msg"] = "กรุณาเลือกอ้างใบกำกับเลขที่ก่อนครับ";
			echo json_encode($response); exit;
		}
		if($STRNO == ''){
			$response["error"] = true;
			$response["msg"] = "กรุณาเลือกอ้างเลขถังก่อนครับ";
			echo json_encode($response); exit;
		}
		if($TAXDT == ''){
			$response["error"] = true;
			$response["msg"] = "กรุณาเลือกวันที่ออกก่อนครับ";
			echo json_encode($response); exit;
		}
		if($NETAMT == '0.00'){
			$response["error"] = true;
			$response["msg"] = "กรุณากรอกมูลค่าสินค้าก่อนครับ";
			echo json_encode($response); exit;
		}
		if($VATAMT == '0.00'){
			$response["error"] = true;
			$response["msg"] = "กรุณากรอกภาษีมูลค่าเพิ่มก่อนครับ";
			echo json_encode($response); exit;
		}
		if($TOTAMT == '0.00' && $TOTAMT == ''){
			$response["error"] = true;
			$response["msg"] = "กรุณากรอกยอดรวมสุทธิก่อนครับ";
			echo json_encode($response); exit;
		}
		$arrs = array();
		$sql = "
			select T.CUSCOD,T.TAXNO,T.TAXDT,T.SNAM,T.NAME1,T.NAME2,T.TAXNO,T.REFDT
			,I.STRNO,T.VATRT,I.NETCOST,I.CRVAT,I.TOTCOST,T.TAXFLG 
			from {$this->MAuth->getdb('INVTRAN')} I
			left join {$this->MAuth->getdb('TAXBUY')} T on I.RECVNO = T.REFNO where I.RECVNO = '".$RECVNO."'
			and I.STRNO = '".$STRNO."'
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$arrs['CUSCOD']  = $row->CUSCOD;
				$arrs['SNAM']    = $row->SNAM;
				$arrs['NAME1']   = $row->NAME1;
				$arrs['NAME2']   = $row->NAME2;
				$arrs['REFDT'] 	 = $row->REFDT;
				$arrs['VATRT']   = $row->VATRT;
				$arrs['TAXFLG']  = $row->TAXFLG;
			}
		}
		$sql = "
			if object_id('tempdb..#AddReduceMoney') is not null drop table #AddReduceMoney;
			create table #AddReduceMoney (id varchar(1),msg varchar(max));

			begin tran AddReduce
			begin try
				declare @invtran  varchar(1) = (select COUNT(*) from {$this->MAuth->getdb('INVTRAN')} 
				where STRNO = '".$STRNO."' and RECVNO = '".$RECVNO."'/* and CRDAMT = '0.00'*/);
				declare @apinvoi  varchar(1) = (select COUNT(*) from {$this->MAuth->getdb('APINVOI')} 
				where LOCAT = '".$LOCAT."' and RECVNO = '".$RECVNO."'/* and RTNAMT = '0.00'*/);
				--รอเช็คเงื่อนไข INSERT
				/*
				declare @crdamt   decimal(12,2) = (select TOTCOST from {$this->MAuth->getdb('INVTRAN')} 
				where STRNO = '".$STRNO."' and RECVNO = '".$RECVNO."');
				declare @kang decimal(12,2) = (select KANG from {$this->MAuth->getdb('APINVOI')} 
				where LOCAT = '".$LOCAT."' and RECVNO = '".$RECVNO."');
				*/
				--declare @year varchar(4) = (select year('".$TAXDT."'));
				declare @taxdt  varchar(4) = (select YEAR('".$TAXDT."'));
				declare @month  varchar(2) = (select RIGHT('0' + RTRIM(MONTH('".$TAXDT."')), 2));
				declare @lastno varchar(1) = (select COUNT(*) from {$this->MAuth->getdb('LASTNO')} 
				where LOCAT = '".$LOCAT."' and CR_YEAR = @taxdt and CR_MONTH = @month);
				
				if(@invtran = 1 and @apinvoi = 1)
				begin
					update {$this->MAuth->getdb('INVTRAN')} set CRDAMT = CRDAMT + '".$TOTAMT."' where STRNO = '".$STRNO."' 
					and RECVNO = '".$RECVNO."'
					
					update {$this->MAuth->getdb('APINVOI')} set KANG = KANG - '".$TOTAMT."',RTNAMT = '".$TOTAMT."'
					,RTNDATE = '".$TAXDT."' where LOCAT = '".$LOCAT."' and RECVNO = '".$RECVNO."'
					
					begin
						if(@lastno = 0)
							insert into {$this->MAuth->getdb('LASTNO')} (
								LOCAT,CR_YEAR,CR_MONTH,L_TCBUY
							)values(
								'".$LOCAT."',@taxdt,@month,'1'
							)
						else
							update {$this->MAuth->getdb('LASTNO')} set L_TCBUY = L_TCBUY+1 where LOCAT = '".$LOCAT."' 
							and CR_YEAR = @taxdt
					end
					begin
						insert into {$this->MAuth->getdb('TAXBUY')} (
							LOCAT,TAXNO,TAXDT,CUSCOD,SNAM,NAME1,NAME2,REFNO,REFDT,STRNO,VATRT
							,NETAMT,VATAMT,TOTAMT,DESCP,TAXTYP,FLAG,CANID,CANDT,INPDT,USERID,TAXFLG
						)values(
							'".$LOCAT."','".$DEBTNO."','".$TAXDT."','".$arrs['CUSCOD']."','".$arrs['SNAM']."'
							,'".$arrs['NAME1']."','".$arrs['NAME2']."','".$TAXNO."','".$REFDT."','".$STRNO."'
							,'".$arrs['VATRT']."','".$NETAMT."','".$VATAMT."','".$TOTAMT."','ใบลดหนี้ซื้อรถบางส่วน','".$TAXTYP."'
							,'".$TAXTYP."',null,null,getdate(),'".$USERID."','".$arrs['TAXFLG']."'
						)
						insert into #AddReduceMoney select 'Y' as id,'สำเร็จ บันทึกข้อมูลเรียบร้อยแล้ว' as msg;
						commit tran AddReduce;
					end	
				end
				else
				begin
					rollback tran AddReduce;
					insert into #AddReduceMoney select 'N' as id,'ออกใบลดหนี้มากกว่ายอดเงินในใบกำกับภาษี' as msg;
					return;
				end
			end try
			begin catch
				rollback tran AddReduce;
				insert into #AddReduceMoney select 'N' as id,'บันทึกข้อมูลไม่สำเร็จ : กรุณาติดต่อฝ่ายไอที' as msg;
				return;
			end catch
		";
		//echo $sql; exit;
		$this->db->query($sql);
		$sql = "
			select * from #AddReduceMoney
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
	function Del_reducemoney(){
		$LOCAT  = $_REQUEST['LOCAT'];
		$REFNO  = $_REQUEST['REFNO'];
		$STRNO  = $_REQUEST['STRNO'];
		$TAXNO  = $_REQUEST['TAXNO'];
		$RECVNO = $_REQUEST['RECVNO'];
		$TOTAMT = str_replace(',','',$_REQUEST['TOTAMT']);
		$USERID = $this->sess["USERID"];
		$sql = "
			if object_id('tempdb..#DelReduceMoney') is not null drop table #DelReduceMoney;
			create table #DelReduceMoney (id varchar(1),msg varchar(max));

			begin tran DelReduce
			begin try
				declare @taxbuy varchar(1) = (select COUNT(*) from {$this->MAuth->getdb('TAXBUY')} 
				where LOCAT = '".$LOCAT."' and TAXNO = '".$TAXNO."' and REFNO = '".$REFNO."' 
				and STRNO = '".$STRNO."' and TAXTYP = '2' and FLAG = '2');
				
				declare @apinvoi varchar(1) = (select COUNT(*) from {$this->MAuth->getdb('APINVOI')} 
				where LOCAT = '".$LOCAT."' and TAXNO = '".$REFNO."'
				and RTNAMT <> '0.00');
				
				if(@taxbuy = 1 and @apinvoi = 1)
				begin
					update {$this->MAuth->getdb('APINVOI')} set KANG = KANG + '".$TOTAMT."',RTNAMT = RTNAMT - '".$TOTAMT."'
					where LOCAT = '".$LOCAT."' and TAXNO = '".$REFNO."' and RTNAMT <> '0.00'
					
					update {$this->MAuth->getdb('TAXBUY')} set FLAG = 'C',CANID = '".$USERID."',CANDT = GETDATE()
					where LOCAT = '".$LOCAT."' and TAXTYP = '2' and TAXNO = '".$TAXNO."' 
					and REFNO = '".$REFNO."' and STRNO = '".$STRNO."'
					
					insert into #DelReduceMoney select 'Y' as id,'สำเร็จ ลบข้อมูลเรียบร้อยแล้ว' as msg;
					commit tran DelReduce;
				end
				else
				begin
					rollback tran DelReduce;
					insert into #DelReduceMoney select 'N' as id,'ไม่สำเร็จ' as msg;
					return;
				end
			end try
			begin catch
				rollback tran DelReduce;
				insert into #DelReduceMoney select 'N' as id,'บันทึกข้อมูลไม่สำเร็จ : กรุณาติดต่อฝ่ายไอที' as msg;
				return;
			end catch
		";
		//echo $sql; exit;
		$this->db->query($sql);
		$sql = "
			select * from #DelReduceMoney
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