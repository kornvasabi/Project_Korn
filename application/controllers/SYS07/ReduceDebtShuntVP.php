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
				$response['DESCP']	 = $row->DESCP;
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
}