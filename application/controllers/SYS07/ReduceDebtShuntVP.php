<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/********************************************************
             ______@03/03/2020______
			 Pasakorn Boonded

********************************************************/
class ReduceDebtShuntVP extends MY_Controller {
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
								<div class='form-group'>
									ประเภทใบลดหนี้
									<select id='TAXTYP' value='6' class='form-control input-sm' disabled><option>6</option></select>
								</div>
							</div>
							<div class='col-sm-3'>	
								<div class='form-group'>
									อ้างถึงใบกำกับเลขที่
									<select id='TAXNO' class='form-control input-sm' data-placeholder='เลือก'></select>
								</div>
							</div>
							<div class='col-sm-3'>	
								<div class='form-group'>
									เลขตัวถัง
									<input type='text' id='STRNO' class='form-control input-sm' disabled>
								</div>
							</div>
							<div class='col-sm-3'>	
								<div class='form-group'>
									เลขที่ใบลดหนี้
									<input type='text' id='TAXNO2' class='form-control input-sm'>
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
									<input type='text' id='REFDT' class='form-control input-sm' disabled>
								</div>
							</div>
							<div class='col-sm-3'>	
								<div class='form-group'>
									เลขที่สัญญา
									<input type='text' id='CONTNO' class='form-control input-sm' disabled>
								</div>
							</div>
							
							<div class='col-sm-4'>	
								<div class='form-group'>
									รหัสลูกค้า
									<input type='text' id='CUSCOD' class='form-control input-sm' disabled>
								</div>
							</div>
							<div class='col-sm-2'>	
								<div class='form-group'>
									<br>
									<input type='text' id='SNAM' class='form-control input-sm' disabled>
								</div>
							</div>
							<div class='col-sm-3'>	
								<div class='form-group'>
									<br>
									<input type='text' id='NAME1' class='form-control input-sm' disabled>
								</div>
							</div>
							<div class='col-sm-3'>	
								<div class='form-group'>
									<br>
									<input type='text' id='NAME2' class='form-control input-sm' disabled>
								</div>
							</div>
							<div class='col-sm-2'>	
								<div class='form-group'>
									ประเภทการขาย
									<input type='text' id='TSALE' class='form-control input-sm' disabled>
								</div>
							</div>
							<div class='col-sm-3'>	
								<div class='form-group'>
									รายการ
									<input type='text' id='DESCP' class='form-control input-sm' disabled>
								</div>
							</div>
							<div class='col-sm-3'>	
								<div class='form-group'>
									สาเหตุที่ออกใบลดหนี้
									<select id='RESONCD' class='form-control input-sm'></select>
								</div>
							</div>
							<div class='col-sm-4'>	
								<div class='form-group'>
									<br>
									<input type='text' id='RESNDES' class='form-control input-sm' disabled>
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
														<input type='text' id='NETAMT' class='form-control input-sm jzAllowNumber' style='text-align:right;' value='0.00' readonly>
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
														<input type='text' id='VATAMT' class='form-control input-sm jzAllowNumber text-primary' style='text-align:right;' value='0.00' readonly>
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
														<input type='text' id='TOTAMT' class='form-control input-sm jzAllowNumber text-danger' style='text-align:right;' value='0.00' readonly>
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
		$html .="<script src='".base_url('public/js/SYS07/ReduceDebtShuntVP.js')."'></script>";
		echo $html;
	}
	function getdetailTAXNO(){
		$TAXNO = $_REQUEST['TAXNO'];
		$LOCAT = $_REQUEST['LOCAT'];
		$response = array();
		$sql = "
			select REFNO from {$this->MAuth->getdb('TAXTRAN')} 
			where LOCAT = '".$LOCAT."' and REFNO <> '' and TSALE in ('C','F') 
		";
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				if($TAXNO == $row->REFNO){
					$response["error"] = true;
					$response["msg"] = "สัญญานี้ออกใบลดหนี้ทั้งรายการแล้ว";
					echo json_encode($response); exit;
				}
			}
		}
		$sql = "
			select TAXNO,convert(varchar(8),TAXDT,112) as TAXDT,STRNO,LOCAT,TSALE,CONTNO,CUSCOD
			,SNAM,NAME1,NAME2,TSALE,DESCP,NETAMT,VATAMT,TOTAMT 
			from {$this->MAuth->getdb('TAXTRAN')} where TSALE in ('C','F') and LOCAT = '".$LOCAT."' 
			and (FLAG <> 'C' or FLAG is null) and TAXNO = '".$TAXNO."'
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$response['TAXDT']   = $this->Convertdate(2,$row->TAXDT);
				$response['STRNO']   = $row->STRNO;
				$response['CONTNO']  = $row->CONTNO;
				$response['CUSCOD']  = $row->CUSCOD;
				$response['SNAM']    = $row->SNAM;
				$response['NAME1']   = $row->NAME1;
				$response['NAME2']	 = $row->NAME2;
				$response['TSALE']	 = $row->TSALE;
				$response['DESCP']	 = "ใบลดหนี้ขายรถทั้งคัน";
				$response['NETAMT']  = number_format($row->NETAMT,2);
				$response['VATAMT']  = number_format($row->VATAMT,2);
				$response['TOTAMT']  = number_format($row->TOTAMT,2);
			}
		}
		echo json_encode($response);
	}
	function getTAXNO(){
		$LOCAT  = $_REQUEST['LOCAT'];
		$TAXDT = $this->Convertdate(1,$_REQUEST['TAXDT']);
		//echo $TAXDT; exit;
		$response = array();
		if($LOCAT ==""){
			$response["error"] = true;
			$response["msg"] = "ไม่พบรหัสสาขา กรุณาเลือกรหัสสาขาก่อนครับ";
			echo json_encode($response); exit;
		}
		$sql = "
			declare @locat varchar(3) = (select SHORTL from {$this->MAuth->getdb('INVLOCAT')} where LOCATCD = '".$LOCAT."');
			declare @YearMonth varchar(4) = (select right(year('".$TAXDT."'),2)+right('0' + rtrim(month('".$TAXDT."')), 2) as DYM); 
			declare @month varchar(2) = (select right('0' + rtrim(month('".$TAXDT."')), 2));
			declare @year varchar(4) = (select YEAR('".$TAXDT."'));
			declare @tcby  varchar(4) = (select RIGHT('0000'+CAST(MAX(CAST(coalesce(L_TCSALE,0) as int) + 1) as nvarchar(4)), 4) 
			as TCBUY from {$this->MAuth->getdb('LASTNO')} where LOCAT = '".$LOCAT."' and CR_YEAR = @year and CR_MONTH = @month);

			declare @taxno varchar(1) = (select COUNT(*) from LASTNO where LOCAT = '".$LOCAT."' and CR_YEAR = @year and CR_MONTH = @month);
			if @taxno = 1
			begin
				select @locat+'Z'+'-'+@YearMonth+@tcby as TAXNO
			end
			else
			begin
				select @locat+'Z'+'-'+@YearMonth+'0001' as TAXNO
			end
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$response['TAXNO'] = $row->TAXNO;
			}
		}
		echo json_encode($response);
	}
	function getRESNDES(){
		$RESONCD = $_REQUEST['RESONCD'];
		$sql = "
			select RESONCD,RESNDES from {$this->MAuth->getdb('SETRESON')}
			where RESONCD = '".$RESONCD."'
		";
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$response['RESNDES'] = str_replace(chr(0),'',$row->RESNDES);
			}
		}else{
			$response['RESNDES'] = "";
		}
		echo json_encode($response);
	}
	function Save_VatPriceShunt(){
		$LOCAT 	 = $_REQUEST["LOCAT"];
		$TAXTYP  = $_REQUEST["TAXTYP"];
		$TAXNO 	 = $_REQUEST["TAXNO"];
		$STRNO 	 = $_REQUEST["STRNO"];
		$TAXNO2  = $_REQUEST["TAXNO2"];
		$TAXDT 	 = $this->Convertdate(1,$_REQUEST["TAXDT"]);
		$REFDT   = $this->Convertdate(1,$_REQUEST["REFDT"]);
		$CONTNO  = $_REQUEST["CONTNO"];
		$CUSCOD  = $_REQUEST["CUSCOD"];
		$SNAM 	 = $_REQUEST["SNAM"];
		$NAME1 	 = $_REQUEST["NAME1"];
		$NAME2   = $_REQUEST["NAME2"];
		$TSALE  = $_REQUEST["TSALE"];
		$DESCP  = $_REQUEST["DESCP"];
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
		$sql = "
			select TAXNO,TAXDT,VATRT,FPAR,FPAY,LPAR,LPAY,FLAG,TAXTYP,TAXFLG,TMBILL from {$this->MAuth->getdb('TAXTRAN')} 
			where TAXNO = '".$TAXNO."' and CONTNO = '".$CONTNO."' and CUSCOD = '".$CUSCOD."'
		";
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$sql2 = "
					if object_id('tempdb..#Savevatpriceshunt') is not null drop table #Savevatpriceshunt;
					create table #Savevatpriceshunt (id varchar(1),msg varchar(max));
					begin tran AddReduce
					begin try
						declare @arcred varchar(1) = (select COUNT(*) from {$this->MAuth->getdb('ARCRED')}
						where CONTNO = '".$CONTNO."' and CUSCOD = '".$CUSCOD."' and STRNO = '".$STRNO."'
						and CRDTXNO = '' and CRDAMT = 0);
						
						declare @invtran varchar(1) = (select COUNT(*) from {$this->MAuth->getdb('INVTRAN')} 
						where TSALE in ('C','F') and FLAG = 'C' and PRICE <> '0.00' and STRNO = '".$STRNO."' 
						and CONTNO = '".$CONTNO."');

						declare @arinopt varchar(1) =(select COUNT(*) from {$this->MAuth->getdb('ARINOPT')} 
						where CONTNO = '".$CONTNO."' and LOCAT = '".$LOCAT."');
						
						/*สร้าง TAXNO ในการบันทึกข้อมูลในแต่ละการบันทึก*/
						declare @taxdt  varchar(4) = (select YEAR('".$TAXDT."'));
						declare @month  varchar(2) = (select RIGHT('0' + RTRIM(MONTH('".$TAXDT."')), 2));
						
						declare @lastno varchar(1) = (select COUNT(*) from {$this->MAuth->getdb('LASTNO')} 
						where LOCAT = '".$LOCAT."' and CR_YEAR = @taxdt and CR_MONTH = @month);
						/*เงื่อนไขเช็คก่อนบันทึก*/
						if(@arcred = 1 and @invtran = 1 and @arinopt = 1) 
						begin		
							update {$this->MAuth->getdb('ARCRED')} set NKANG = NKANG - '".$NETAMT."',TKANG = TKANG - '".$TOTAMT."'
							,VKANG = VKANG - '".$VATAMT."',CRDTXNO = '".$TAXNO2."',CRDAMT = CRDAMT + '".$TOTAMT."',
							NPRICE = NPRICE - '".$NETAMT."',VATPRC = VATPRC - '".$VATAMT."',TOTPRC = TOTPRC - '".$TOTAMT."'
							where STRNO = '".$STRNO."' and CONTNO = '".$CONTNO."' and CUSCOD = '".$CUSCOD."'
							
							update {$this->MAuth->getdb('INVTRAN')} set SDATE = null,PRICE = PRICE - '".$TOTAMT."',TSALE = '',CONTNO =''
							,FLAG = 'D' where STRNO = '".$STRNO."'
							
							update {$this->MAuth->getdb('ARINOPT')} set RTNFLAG = 'R' where CONTNO = '".$CONTNO."'
							
							begin
								if(@lastno = 0)
									insert into {$this->MAuth->getdb('LASTNO')}(
										LOCAT,CR_YEAR,CR_MONTH,L_TCSALE
									)values(
										'".$LOCAT."',@taxdt,@month,'1'
									)
								else
									update {$this->MAuth->getdb('LASTNO')} set L_TCSALE = L_TCSALE+1 
									where LOCAT = '".$LOCAT."' and CR_YEAR = @taxdt
							end
							begin
								insert into TAXTRAN(
									[LOCAT],[TAXNO],[TAXDT],[TSALE],[CONTNO],[CUSCOD],[SNAM],[NAME1],[NAME2],[STRNO]
									,[REFNO],[REFDT],[VATRT],[NETAMT],[VATAMT],[TOTAMT],[DESCP],[FPAR],[FPAY],[LPAR]
									,[LPAY],[INPDT],[FLAG],[CANDT],[TAXTYP],[TAXFLG],[USERID],[FLCANCL],[TMBILL]
									,[RTNSTK],[FINCOD],[DOSTAX],[PAYFOR],[RESONCD],[INPTIME]
								)values(
									'".$LOCAT."','".$TAXNO2."','".$TAXDT."','".$TSALE."','".$CONTNO."','".$CUSCOD."'
									,'".$SNAM."','".$NAME1."','".$NAME2."','".$STRNO."','".$TAXNO."','".$REFDT."'
									,'".$row->VATRT."','".$NETAMT."','".$VATAMT."','".$TOTAMT."','".$DESCP."','','0'
									,'','0',getdate(),'','','".$TAXTYP."','".$row->TAXFLG."','".$USERID."','','','Y','',''
									,'','',null
								)
								insert into #Savevatpriceshunt select 'Y' as id,'สำเร็จ บันทึกข้อมูลเรียบร้อยแล้ว' as msg;
								commit tran AddReduce;
							end
						end
						else
						begin
							rollback tran AddReduce;
							insert into #Savevatpriceshunt select 'N' as id,'รอเงื่อนไข' as msg;
							return;
						end
					end try
					begin catch
						rollback tran AddReduce;
						insert into #Savevatpriceshunt select 'N' as id,'บันทึกข้อมูลไม่สำเร็จ : กรุณาติดต่อฝ่ายไอที' as msg;
						return;
					end catch
				";
				echo $sql2; exit;
			}
		}
		$response = array();
		$sql = "
			select * from #Savevatpriceshunt
		";
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$response['status'] = $row->id;
				$response['msg']    = $row->mag;
			}
		}else{
			$response['status'] = false;
			$response['msg']    = "ผิดพลาด";
		}
	}
	function Del_VatPriceShunt(){
		$LOCAT 	 = $_REQUEST["LOCAT"];
		$TAXTYP  = $_REQUEST["TAXTYP"];
		$TAXNO 	 = $_REQUEST["TAXNO"];
		$STRNO   = $_REQUEST["STRNO"];
		$TAXNO2  = $_REQUEST["TAXNO2"];
		$CONTNO  = $_REQUEST["CONTNO"];
		$CUSCOD  = $_REQUEST["CUSCOD"];
		$REFDT   = $this->Convertdate(1,$_REQUEST["REFDT"]);
		$TSALE   = $_REQUEST['TSALE'];
		
		$NETAMT  = str_replace(',','',$_REQUEST["NETAMT"]);
		$VATAMT  = str_replace(',','',$_REQUEST["VATAMT"]);
		$TOTAMT  = str_replace(',','',$_REQUEST["TOTAMT"]);
		$USERID  = $this->sess["USERID"];
		
		$sql = "
			if object_id('tempdb..#Delvatpriceshunt') is not null drop table #Delvatpriceshunt;
			create table #Delvatpriceshunt (id varchar(1),msg varchar(max));
			begin tran AddReduce
			begin try
				declare @taxtran varchar(1) = (select COUNT(*) from {$this->MAuth->getdb('TAXTRAN')} 
				where LOCAT = '".$LOCAT."' and TAXNO = '".$TAXNO2."' and CONTNO = '".$CONTNO."'
				and CUSCOD = '".$CUSCOD."' and REFNO = '".$TAXNO."' and TAXTYP = '".$TAXTYP."');
			
				declare @arcred varchar(1) = (select COUNT(*) from {$this->MAuth->getdb('ARCRED')}
				where CONTNO = '".$CONTNO."' and CUSCOD = '".$CUSCOD."' and STRNO = '".$STRNO."'
				and CRDTXNO = '".$TAXNO2."' and CRDAMT = '".$TOTAMT."');
				
				declare @invtran varchar(1) = (select COUNT(*) from {$this->MAuth->getdb('INVTRAN')} 
				where STRNO = '".$STRNO."' and CONTNO = '' and PRICE <> '".$TOTAMT."' and TSALE = '' and 
				FLAG = 'D');
				
				declare @arinopt varchar(1) =(select COUNT(*) from {$this->MAuth->getdb('ARINOPT')} 
				where CONTNO = '".$CONTNO."' and LOCAT = '".$LOCAT."' and RTNFLAG = 'R');
				
				/*เงื่อนไขเช็คก่อนบันทึก*/
				if(@taxtran = 1 and @arcred = 1 and @invtran = 1 and @arinopt = 1) 
				begin
					update {$this->MAuth->getdb('TAXTRAN')} set FLAG = 'C',CANDT = GETDATE(),FLCANCL = '".$USERID."' 
					where LOCAT = '".$LOCAT."' and TAXNO = '".$TAXNO2."' and CONTNO = '".$CONTNO."'
					and CUSCOD = '".$CUSCOD."' and REFNO = '".$TAXNO."' and TAXTYP = '".$TAXTYP."'
					
					update {$this->MAuth->getdb('ARCRED')} set NKANG = NKANG + '".$NETAMT."',TKANG = TKANG + '".$TOTAMT."'
					,VKANG = VKANG + '".$VATAMT."',CRDTXNO = '',CRDAMT = CRDAMT + '".$TOTAMT."',
					NPRICE = NPRICE + '".$NETAMT."',VATPRC = VATPRC + '".$VATAMT."',TOTPRC = TOTPRC + '".$TOTAMT."'
					where STRNO = '".$STRNO."' and CONTNO = '".$CONTNO."' and CUSCOD = '".$CUSCOD."'
					
					update {$this->MAuth->getdb('INVTRAN')} set SDATE = '".$REFDT."',PRICE = PRICE - '".$TOTAMT."'
					,TSALE = '".$TSALE."',CONTNO = '".$CONTNO."',FLAG = 'C' where STRNO = '".$STRNO."'
					
					update {$this->MAuth->getdb('ARINOPT')} set RTNFLAG = '' where CONTNO = '".$CONTNO."' 
					and LOCAT = '".$LOCAT."'
					
					insert into #Delvatpriceshunt select 'Y' as id,'สำเร็จ บันทึกข้อมูลเรียบร้อยแล้ว' as msg;
					commit tran AddReduce;
				
				end
				else
				begin
					rollback tran AddReduce;
					insert into #Delvatpriceshunt select 'N' as id,'รอเงื่อนไข' as msg;
					return;
				end
			end try
			begin catch
				rollback tran AddReduce;
				insert into #Delvatpriceshunt select 'N' as id,'บันทึกข้อมูลไม่สำเร็จ : กรุณาติดต่อฝ่ายไอที' as msg;
				return;
			end catch
		";
		echo $sql; exit;
	}
}