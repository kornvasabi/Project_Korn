<?php
defined('BASEPATH') OR exit('No direct script access allowed');
include "public/phpqrcode/qrlib.php";
/********************************************************
             ______@04/03/2020______
            / / _ _   _ _     __ 
           / // __ \ / __ \ / __ \
       _ _/ // /_/ // / / // /_/ /
     /_ _ _/ \_ _ //_/ /_/ \__  /
                          _ _/ /
                         /_ _ /
********************************************************/
class RevPayment extends MY_Controller {
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
		
		$this->config_db['database'] = $this->sess["db"];
		$this->connect_db = $this->load->database($this->config_db,true);
	}
	
	function index(){
		$claim = $this->MLogin->getclaim(uri_string());
		//print_r($claim); exit;
		if($claim['m_access'] != "T"){ echo "<div align='center' style='color:red;font-size:16pt;width:100%;'>ขออภัย คุณยังไม่มีสิทธิเข้าใช้งานหน้านี้ครับ</div>"; exit; }
		
		$html = "
			<div class='tab1' name='home' groupType='{$claim["groupType"]}' locat='{$this->sess['branch']}' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}'>
				<div>
					<div class='row'>
						<div class='col-sm-2'>	
							<div class='form-group'>
								ใบรับชั่วคราว
								<input type='text' id='sch_tmbill' class='form-control input-sm' placeholder='ใบรับชั่วคราว' >
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								เลขที่ใบเสร็จ
								<input type='text' id='sch_billno' class='form-control input-sm' placeholder='เลขที่ใบเสร็จ' >
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								สาขา
								<select id='sch_locatrecv' class='form-control' title='เลือก'  multiple data-actions-box='true' data-size='8' data-live-search='true'>
									".$this->MMAIN->Option_get_locat($this->sess["branch"])."
								</select>
							</div>
						</div>
						<div class='col-sm-2'>	
							ลูกค้า
							<div class='input-group'>
								<input type='text' id='sch_cuscod' CUSCOD='' class='form-control input-sm' placeholder='ลูกค้า' >
								<span class='input-group-btn'>
								<button id='sch_cuscod_removed' class='btn btn-danger btn-sm' type='button'>
									<span class='glyphicon glyphicon-remove' aria-hidden='true'></span></button>
								</span>
							</div>
						</div>	
						<div class='col-sm-2'>	
							<div class='form-group'>
								วันที่รับ จาก
								<input type='text' id='sch_stmbildt' class='form-control input-sm' placeholder='จาก' data-provide='datepicker' data-date-language='th-th' value='".$this->today('startofmonthB1')."'>
							</div>
						</div>	
						<div class='col-sm-2'>	
							<div class='form-group'>
								วันที่รับ ถึง
								<input type='text' id='sch_etmbildt' class='form-control input-sm' placeholder='ถึง' data-provide='datepicker' data-date-language='th-th' value='".$this->today('endofmonth')."'>
							</div>
						</div>	
					</div>
					<!-- div class='row'>
						<div class='col-sm-2 col-sm-offset-4'>
							<b>รายงาน</b>
							<div class='radio'><label><input type='radio' class='sort' name='REPORT' value='1' checked=''>วันที่รับ</label></div>
							<div class='radio'><label><input type='radio' class='sort' name='REPORT' value='2'>วันที่ใบกำกับภาษี</label></div>
						</div>
						
						<div class='col-sm-2'>
							<b>สินค้าและวัตถุดิบ</b>
							<div class='radio'><label><input type='radio' class='sort' name='turnover' value='Y' checked=''>มีการเคลื่อนไหว</label></div>
							<div class='radio'><label><input type='radio' class='sort' name='turnover' value='N'>ทั้งหมด</label></div>
						</div>
					</div -->
					<div class='row'>
						<div class='col-sm-6'>	
							<div class='form-group'>
								<button id='btnt1revpayment' class='btn btn-cyan btn-block'>
									<span class='glyphicon glyphicon-pencil'> รับชำระ</span>
								</button>
							</div>
						</div>
						<div class='col-sm-6'>	
							<div class='form-group'>
								<button id='btnt1search' class='btn btn-primary btn-block'>
									<span class='glyphicon glyphicon-search'> แสดง</span>
								</button>
							</div>
						</div>
					</div>
				</div>
				<div id='result'></div>
			</div>
		";
		
		$html.= "<script src='".base_url('public/js/SYS06/RevPayment.js')."'></script>";
		echo $html;
	}
	
	
	function get_form_received(){
		$action = $_POST['action'];
		$data = array();
		if($action == 'new'){
			$data["add_disabled"]	= "";	
			$data["add_TMBILL"] 	= "Auto Genarate";
			$data["add_LOCATRECV"] 	= $this->sess["branch"];
			$data["add_PAYTYP"] 	= "";
			$data["add_CUSCOD"] 	= "";
			$data["add_CUSNAME"]	= "";
			$data["add_REFNO"] 		= "";
			$data["add_CHQNO"] 		= "";
			$data["add_CHQDT"] 		= "";
			$data["add_CHQAMT"] 	= "";
			$data["add_CHQBK"] 		= "";
			$data["add_CHQBR"]		= "";
			$data["add_BILLNO"]		= "";
			$data["add_BILLDT"]		= "";
			$data["add_CHQTMP"]		= ""; // รวม
			$data["add_CANDT"]  	= "";
			
			$data["add_dataTable"]  = "";
		}else{
			$data["add_disabled"]	= " disabled ";
			$TMBILL = $_POST['TMBILL'];
			
			$sql = "
				select a.TMBILL,a.TMBILDT,a.LOCATRECV,a.PAYTYP
					,(select '('+sa.PAYCODE+') '+sa.PAYDESC as PAYDESC 
						from {$this->MAuth->getdb('PAYTYP')} sa 
						where sa.PAYCODE=a.PAYTYP
					 ) as PAYDESC
					,a.CUSCOD
					,(select sa.SNAM+NAME1+' '+sa.NAME2+' ('+sa.CUSCOD+') - '+sa.GRADE as CUSNAME 
						from {$this->MAuth->getdb('CUSTMAST')} sa 
						where sa.CUSCOD=a.CUSCOD
					 ) as CUSNAME
					,a.REFNO,a.CHQNO,a.CHQDT,a.CHQAMT
					,a.CHQBK,a.CHQBR,a.BILLNO,a.BILLDT
					,a.CHQTMP
					,a.CANDT
				from {$this->MAuth->getdb('CHQMAS')} a
				where TMBILL='".$TMBILL."'
			";
			$query = $this->db->query($sql);
			
			if($query->row()){
				foreach($query->result() as $row){
					$data["add_TMBILL"] 	= $row->TMBILL;
					$data["add_TMBILDT"] 	= $this->Convertdate(103,$row->TMBILDT);
					$data["add_LOCATRECV"] 	= $row->LOCATRECV;
					$data["add_PAYTYP"] 	= $row->PAYTYP;
					$data["add_PAYDESC"] 	= $row->PAYDESC;
					$data["add_CUSCOD"] 	= str_replace(chr(0),"",$row->CUSCOD);
					$data["add_CUSNAME"] 	= str_replace(chr(0),"",$row->CUSNAME);
					
					$data["add_REFNO"] 	= $row->REFNO;
					$data["add_CHQNO"] 	= $row->CHQNO;
					$data["add_CHQDT"] 	= $this->Convertdate(103,$row->CHQDT);
					$data["add_CHQAMT"] = number_format($row->CHQAMT,2);
					$data["add_CHQBK"] 	= $row->CHQBK;
					$data["add_CHQBR"] 	= $row->CHQBR;
					$data["add_BILLNO"] = $row->BILLNO;
					$data["add_BILLDT"] = $this->Convertdate(103,$row->BILLDT);
					
					$data["add_CHQTMP"] = number_format($row->CHQTMP,2);
					$data["add_CANDT"]  = $this->Convertdate(103,$row->CANDT);
				}
			}
			
			$sql = "
				select a.PAYFOR as FORCODE
					,(select '('+sa.FORCODE+') '+sa.FORDESC as FORDESC 
						from {$this->MAuth->getdb('PAYFOR')} sa 
						where sa.FORCODE=a.PAYFOR
					 ) as FORDESC
					,a.CONTNO,a.LOCATPAY,a.PAYAMT,a.DISCT,a.PAYINT,a.DSCINT,a.NETPAY
				from {$this->MAuth->getdb('CHQTRAN')} a
				where 1=1 and TMBILL='".$TMBILL."' 
			";
			//echo $sql; exit;
			$query = $this->db->query($sql);
			
			$data["add_dataTable"] = "";
			if($query->row()){
				foreach($query->result() as $row){
					$data["add_dataTable"] .= "
						<tr>
							<td>
								<button class='del_payment btn btn-xs btn-danger glyphicon glyphicon-trash' 
									opt_payfor='".$row->FORCODE."'
									opt_contno='".$row->CONTNO."'
									opt_payamt='".$row->PAYAMT."'
									opt_disct='".$row->DISCT."'
									opt_payint='".$row->PAYINT."'
									opt_dscint='".$row->DSCINT."'
									opt_netpay='".$row->NETPAY."'
									style='cursor:pointer;' ".$data["add_disabled"]."> ลบ </button>
							</td>
							<td>".$row->FORDESC."</td>
							<td class='LISTCONTNO' CONTNO='".$row->CONTNO."' LOCAT='".$row->LOCATPAY."' style='cursor:pointer;'>".$row->CONTNO."</td>
							<td align='right'>".number_format($row->PAYAMT,2)."</td>
							<td align='right'>".number_format($row->DISCT,2)."</td>
							<td class='text-red' align='right'>".number_format($row->PAYINT,2)."</td>
							<td align='right'>".number_format($row->DSCINT,2)."</td>
							<td align='right'>".number_format($row->NETPAY,2)."</td>
						</tr>
					";
				}
			}
		}
		
		$html = "
			<div style='height:calc(100vh - 170px);overflow:auto;border:0.5px dotted black;background-color:".($data["add_CANDT"] == "" ? "#fff;":"#f3d8d8;")."'>
				<div class='col-sm-2'>	
					<div class='form-group'>
						ใบรับชั่วคราว
						<input type='text' id='add_TMBILL' class='form-control input-sm text-red' value='{$data["add_TMBILL"]}' style='font-size:12pt;' readonly>
					</div>
				</div>
				<div class='col-sm-2'>	
					<div class='form-group'>
						วันที่รับเงิน
						<input type='text' id='add_TMBILDT' class='form-control input-sm' data-provide='datepicker' {$data["add_disabled"]} data-date-language='th-th' value='".($action=="EDIT"?$data["add_TMBILDT"]:$this->today('today'))."' maxlength=10>
					</div>
				</div>
				
				<div class='col-sm-2'>	
					<div class='form-group'>
						สาขา
						<select id='add_LOCATRECV' class='form-control input-sm chosen-select' data-placeholder='สาขา'  {$data["add_disabled"]}>
							".$this->MMAIN->Option_get_locat($data["add_LOCATRECV"])."
						</select>
					</div>
				</div>
				<div class='col-sm-2'>	
					<div class='form-group'>
						ชำระโดย
						<select id='add_PAYTYP' class='form-control input-sm chosen-select' data-placeholder='ชำระโดย'  {$data["add_disabled"]}>
							".$this->MMAIN->Option_get_paytyp($data["add_PAYTYP"])."
						</select>
					</div>
				</div>
				<div class='col-sm-4'>	
					<div class='form-group'>
						ชื่อสกุล-ลูกค้า
						<div class='input-group'>
						   <input type='text' id='add_CUSCOD' CUSCOD='".$data["add_CUSCOD"]."'  {$data["add_disabled"]} class='form-control input-sm' placeholder='ลูกค้า'  value='".$data["add_CUSNAME"]."'>
						   <span class='input-group-btn'>
						   <button id='add_CUSCOD_removed' class='btn btn-danger btn-sm' type='button'  {$data["add_disabled"]}>
								<span class='glyphicon glyphicon-remove' aria-hidden='true'></span></button>
						   </span>
						</div>
					</div>
				</div>
				
				
				<div class='col-sm-2'>	
					<div class='form-group'>
						เลขที่อ้างอิง
						<input type='text' id='add_REFNO' class='form-control input-sm' value='".$data["add_REFNO"]."' {$data["add_disabled"]} style='font-size:12pt;'>
					</div>
				</div>
				<div class='col-sm-2'>	
					<div class='form-group'>
						เลขที่เช็ค
						<input type='text' id='add_CHQNO' class='form-control input-sm' value='".$data["add_CHQNO"]."' {$data["add_disabled"]} style='font-size:12pt;'>
					</div>
				</div>
				<div class='col-sm-2'>	
					<div class='form-group'>
						วันที่เช็ค
						<input type='text' id='add_CHQDT' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' value='".$data["add_CHQDT"]."' {$data["add_disabled"]} maxlength=10>
					</div>
				</div>
				<div class='col-sm-2'>	
					<div class='form-group'>
						จำนวนเงิน
						<input type='text' id='add_CHQAMT' class='form-control input-sm text-blue' value='".$data["add_CHQAMT"]."' {$data["add_disabled"]} style='font-size:12pt;'>
					</div>
				</div>
				<div class='col-sm-2'>	
					<div class='form-group'>
						ธนาคาร
						<select id='add_CHQBK' class='form-control input-sm chosen-select' data-placeholder='สาขา'  {$data["add_disabled"]}>
							".$this->MMAIN->Option_get_bkmast($data["add_CHQBK"])."
						</select>
					</div>
				</div>
				<div class='col-sm-2'>	
					<div class='form-group'>
						สาขาธนาคาร
						<input type='text' id='add_CHQBR' class='form-control input-sm' value='".$data["add_CHQBR"]."' {$data["add_disabled"]} style='font-size:12pt;'>
					</div>
				</div>
				
				<div class='col-sm-2 col-sm-offset-4'>	
					<div class='form-group'>
						เลขที่ใบเสร็จ
						<input type='text' id='add_BILLNO' class='form-control input-sm text-blue' value='".$data["add_BILLNO"]."' style='font-size:12pt;' readonly>
					</div>
				</div>
				<div class='col-sm-2'>	
					<div class='form-group'>
						วันที่ใบเสร็จ
						<input type='text' id='add_BILLDT' class='form-control input-sm text-blue' data-provide='datepicker' data-date-language='th-th' value='".$data["add_BILLDT"]."' maxlength=10 disabled>
					</div>
				</div>
				<div class='col-sm-4'>	
					<div class='text-red' align='right'>
						<h1>".($data["add_CANDT"] == "" ? "":"*** ยกเลิก ***")."</h1>
					</div>
				</div>
				
				<div class='col-sm-12'>	
					<div class='row' style='width:100%;height:100%;padding-left:30px;background-color:#269da1;'>
						<div class='form-group col-sm-12' style='height:100%;'>
							<span style='color:#efff14;'>ชำระค่า</span>
							<div id='dataTable_fixed_ARMGAR' class='dataTables_wrapper dt-bootstrap4 table-responsive' style='max-height:calc(100% - 50px);height:calc(100% - 30px);overflow:auto;border:1px dotted black;background-color:white;'>
								<table id='dataTable_ARMGAR' class='table table-bordered dataTable table-hover table-secondary' id='dataTables_ARMGAR' stat='' role='grid' aria-describedby='dataTable_info' style='width:100%;line-height:10px;white-space:nowrap;text-overflow:ellipsis;overflow:hidden;' cellspacing='0'>
									<thead class='thead-dark' style='width:100%;'>
										<tr role='row'>
											<th style='width:40px'>
												<i id='add_payment' 
													class='btn btn-xs btn-success glyphicon glyphicon-plus' 
													opt_payfor = ''
													opt_contno = ''
													opt_payamt = ''
													opt_disct  = ''
													opt_payint = ''
													opt_dscint = ''
													opt_netpay = ''
													style='cursor:pointer;' ".($action=="EDIT"?"disabled":"").">
													เพิ่ม  
												</i>
											</th>
											<th>ชำระค่า</th>
											<th>เลขที่สัญญา</th>
											<th>จำนวนชำระ</th>
											<th>ส่วนลด</th>
											<th>ค่าเบี้ยปรับ</th>
											<th>ส่วนลดเบี้ยปรับ</th>
											<th>ยอดรับสุทธิ</th>
										</tr>
									</thead>
									<tbody style='white-space: nowrap;'>".$data["add_dataTable"]."</tbody>
								</table>
							</div>
						</div>
						
						<div class='col-sm-2 col-sm-offset-10'>	
							<div class='form-group'>
								<span style='color:#efff14;'>รวม</span>
								<input type='text' id='add_CHQTMP' class='form-control input-sm' value='".$data["add_CHQTMP"]."' style='font-size:12pt;' readonly>
							</div>
						</div>
					</div>
				</div>
			</div>
			
			<div class='row' style='height:50px;'>
				<div class='col-sm-2 col-sm-offset-2'>	
					<div class='form-group'>
						<span>เบี้ยปรับ</span>
						<input type='text' id='add_CHQTMP' class='form-control input-sm text-red' value='' readonly>
					</div>
				</div>
				<div class='col-sm-2'>	
					<div class='form-group'>
						<span>ชำระแล้ว</span>
						<input type='text' id='add_CHQTMP' class='form-control input-sm text-blue' value='' readonly>
					</div>
				</div>
				
				<div class='col-sm-2'>	
					<div class='form-group'>
						<span>ส่วนลด</span>
						<input type='text' id='add_CHQTMP' class='form-control input-sm text-red' value='' readonly>
					</div>
				</div>
				<div class='col-sm-2'>	
					<div class='form-group'>
						<span>ยอดคงเหลือ</span>
						<input type='text' id='add_CHQTMP' class='form-control input-sm text-blue' value='' readonly>
					</div>
				</div>
			</div>
			
			<div class='row' style='height:50px;'>
				<div class='col-sm-10'>	
					<div class='btn-group btn-group-sm dropup'>
						<button type='button' id='btnDocument' class='btn btn-sm btn-info'>
							ดำเนินการ
						</button>
						<button type='button' id='btnDocumentOption' class='btn btn-sm btn-info dropdown-toggle' data-toggle='dropdown' aria-expanded='false'>
							<i class='fa fa-cog'></i>
							<span class='sr-only'>Toggle Dropdown</span>
						</button>
						<ul class='dropdown-menu'>
							<span id='add_btnAROther' style='text-align:left;' class='btn btn-default btn-sm btn-block text-left'>1.ลูกหนี้อื่น</span>
							<span id='add_btnARPAY' style='text-align:left;' class='btn btn-default btn-sm btn-block text-left'>2.ตารางสัญญา</span>
							<span id='add_btnListPayment' style='text-align:left;' class='btn btn-default btn-sm btn-block text-left'>3.รายการชำระเงิน</span>
							<span id='add_btnCalC' style='text-align:left;' class='btn btn-default btn-sm btn-block text-left'>4.ยอดค้าง (คำนวณเบี้ยปรับ)</span>
							<span id='add_btnFORMSETAlert' style='text-align:left;' class='btn btn-default btn-sm btn-block text-left'>5.บันทึกข้อความเตือน</span>
						</ul>
					</div>
				
				
					<i id='add_btnPrint' class='btn btn-sm btn-default glyphicon glyphicon-print' style='cursor:pointer;'> พิมพ์เอกสาร </i>
					<!-- i id='add_btnBillFN' class='btn btn-sm btn-default glyphicon glyphicon-print' style='cursor:pointer;'> พิมพ์ใบเสร็จรับเงิน </i -->
					<i id='add_btnAlert' CONTNO='' LOCAT='' class='btn btn-sm btn-warning glyphicon glyphicon-alert' style='cursor:pointer;'> แสดงข้อความเตือน </i>
				</div>
				<div class='col-sm-2'>	
					".($action == 'new' ? "
						<button id='add_btnSave' class='btn btn-sm btn-primary btn-block' style='cursor:pointer;'>
							<span class='glyphicon glyphicon-floppy-disk'> บันทึก</span>
						</button>
					":"
						<button id='add_btnCanC' class='btn btn-sm btn-danger btn-block' style='cursor:pointer;'>
							<span class='glyphicon glyphicon-remove'> ยกเลิกบิล</span>
						</button>
					")."
				</div>
			</div>
		";
		
		$response = array("html"=>$html);
		echo json_encode($response);
	}
	
	function get_form_payment(){
		$html = "
			<div>
				<div class='col-sm-12'>	
					<div class='form-group'>
						ชำระค่า
						<select id='add_PAYFOR' class='form-control input-sm' data-placeholder='ชำระค่า'></select>
					</div>
				</div>
				<div class='col-sm-12'>	
					<div class='form-group'>
						เลขที่สัญญา
						<!-- select id='add_CONTNO' class='form-control input-sm' data-placeholder='เลขที่สัญญา'></select -->
						<div class='input-group'>
							<span class='input-group-btn'>
								<button id='add_CONTNO_detail' class='btn btn-info btn-sm' type='button'>
									<span class='glyphicon glyphicon-zoom-in' aria-hidden='true'></span>
								</button>
							</span>	
							<input type='text' id='add_CONTNO' CUSCOD='' class='form-control input-sm' placeholder='เลขที่สัญญา'  value=''>
							<span class='input-group-btn'>
								<button id='add_CONTNO_removed' class='btn btn-danger btn-sm' type='button'>
									<span class='glyphicon glyphicon-remove' aria-hidden='true'></span>
								</button>	
							</span>
						</div>
					</div>
				</div>
				<div class='col-sm-12'>	
					<div class='form-group'>
						จำนวนชำระ
						<input type='text' id='add_PAYAMT' class='form-control input-sm' value='' style='font-size:12pt;' >
					</div>
				</div>
				<div class='col-sm-12'>	
					<div class='form-group'>
						ส่วนลด
						<input type='text' id='add_DISCT' class='form-control input-sm' value='' style='font-size:12pt;' >
					</div>
				</div>
				<div class='col-sm-12'>	
					<div class='form-group'>
						ค่าเบี้ยปรับ
						<input type='text' id='add_PAYINT' class='form-control input-sm text-red' value='' style='font-size:12pt;' readonly>
					</div>
				</div>
				<div class='col-sm-12'>	
					<div class='form-group'>
						ส่วนลดเบี้ยปรับ
						<input type='text' id='add_DSCINT' class='form-control input-sm' value='' style='font-size:12pt;' {$this->MMAIN->Allow_payment_discount_intamt($this->sess["IDNo"])}>
					</div>
				</div>
				<div class='col-sm-12'>	
					<div class='form-group'>
						ยอดรับสุทธิ
						<input type='text' id='add_NETPAY' class='form-control input-sm' value='' style='font-size:12pt;' readonly>
					</div>
				</div>
				<div class='col-sm-6'>	
					<i id='btn_DATACalc' class='btn btn-xs btn-warning btn-block glyphicon glyphicon-refresh' style='cursor:pointer;'> คำนวณ  </i>
				</div>
				<div class='col-sm-6'>	
					<i id='btn_DATAPayment' class='btn btn-xs btn-primary btn-block glyphicon glyphicon-plus' style='cursor:pointer;' disabled> เพิ่ม  </i>
				</div>
			</div>
		";
		
		$response = array("html"=>$html);
		echo json_encode($response);
	}
	
	function getREBUILDING(){
		$cuscod 	= $_POST["cuscod"];
		$cusname 	= $_POST["cusname"];
		$contno 	= $_POST["contno"];
		$total 		= $_POST["total"];
		$error 		= $_POST["error"];
		$payfor 	= $_POST["payfor"];
		
		$tmbildt 	= $this->Convertdate(1,$_POST["tmbildt"]);
				
		$data = array();
		$data["PAYAMT"] = number_format(0,2);
		$data["DISCT"]  = number_format(0,2);
		$data["PAYINT"] = number_format(0,2);
		$data["DSCINT"] = number_format(0,2);
		$data["NETPAY"] = number_format(0,2);
		
		$sql = "";
		if($payfor == "001"){
			$sql = "
				declare @contno varchar(13) = '{$contno}';
				--select @contno as CONTNO,0 as DAMT,0 as INTAMT,0 as NETPAY
				
				SELECT CONTNO
					,TOTPRC-SMPAY-SMCHQ as DAMT
					,0 as DISCT
					,0 as INTAMT
					,(TOTPRC-SMPAY-SMCHQ) as NETPAY
					--,LOCAT,VATRT,INCLVAT,TOTPRC,SMPAY,SMCHQ
					--,TOTPRC-SMPAY-SMCHQ AS BALANCE,CUSCOD,SDATE 
				FROM {$this->MAuth->getdb('ARCRED')}
				WHERE CONTNO= @contno
			";
		}else if($payfor == "002"){	
			$sql = "
				declare @contno varchar(13) = '{$contno}';
				select a.CONTNO
					,a.TOTDWN-isnull(b.PAYAMT,0) as DAMT
					,0 as DISCT
					,0 as INTAMT
					,a.TOTDWN-isnull(b.PAYAMT,0) as NETPAY 
				from (
					select CONTNO,TOTDWN,PAYDWN from {$this->MAuth->getdb('ARMAST')}
					where TOTDWN<>PAYDWN and CONTNO=@contno
				) as a
				left join (
					select CONTNO,sum(PAYAMT) as PAYAMT,sum(DISCT) as DISCT from {$this->MAuth->getdb('CHQTRAN')}
					where PAYFOR='002' and FLAG!='C'
					group by CONTNO
				) as b on a.CONTNO=b.CONTNO
			";
		}else if($payfor == "003"){	
			$sql = "
				declare @contno varchar(13) = '{$contno}';
				select a.CONTNO
					,a.TOTDWN-isnull(b.PAYAMT,0) as DAMT
					,0 as DISCT
					,0 as INTAMT
					,a.TOTDWN-isnull(b.PAYAMT,0) as NETPAY 
				from (
					select CONTNO,TOTDWN,PAYDWN
					from {$this->MAuth->getdb('ARFINC')}
					where TOTDWN<>PAYDWN and CONTNO=@contno
				) as a
				left join (
					select CONTNO,sum(PAYAMT) as PAYAMT,sum(DISCT) as DISCT from {$this->MAuth->getdb('CHQTRAN')}
					where PAYFOR='003' and FLAG!='C'
					group by CONTNO
				) as b on a.CONTNO=b.CONTNO
			";
			//echo $sql; exit;
		}else if($payfor == "004"){	
			$sql = "
				declare @contno varchar(13) = '{$contno}';
				select a.CONTNO
					,a.TOTFIN-isnull(b.PAYAMT,0) as DAMT
					,0 as DISCT
					,0 as INTAMT
					,a.TOTFIN-isnull(b.PAYAMT,0) as NETPAY 
				from (
					select CONTNO,TOTFIN,PAYFIN
					from {$this->MAuth->getdb('ARFINC')}
					where CONTNO=@contno
				) as a
				left join (
					select CONTNO,sum(PAYAMT) as PAYAMT,sum(DISCT) as DISCT from {$this->MAuth->getdb('CHQTRAN')}
					where PAYFOR='004' and FLAG!='C'
					group by CONTNO
				) as b on a.CONTNO=b.CONTNO
			";
			//echo $sql; exit;
		}else if($payfor == "006"){
			$sql = "
				begin 
					declare @today varchar(8) = '{$tmbildt}';
					exec {$this->MAuth->getdb('FN_JD_LatePenalty')} @contno ='".$contno."',@dt = @today;
					
					insert into {$this->MAuth->getdb('hp_UserOperationLog')} (userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
					values ('".$this->sess["IDNo"]."','SYS06::ปรับปรุงเบี้ยปรับล่าช้า',' ".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
				end
			";
			//echo $sql; exit;
			$this->db->query($sql);
			
			$sql = "
				declare @contno varchar(13) = '{$contno}';
				
				select a.CONTNO
					,0 as DAMT
					,0 as DISCT
					,a.INTAMT-isnull(b.PAYINT,0) as INTAMT 
					,a.INTAMT-isnull(b.PAYINT,0) as NETPAY
				from (
					select CONTNO,SUM(DAMT) as DAMT,SUM(INTAMT) as INTAMT from {$this->MAuth->getdb('ARPAY')}
					where CONTNO=@contno and convert(varchar(8),DDATE,112) <= convert(varchar(8),GETDATE(),112)
					group by CONTNO
				) as a
				left join (
					select CONTNO,SUM(PAYAMT) as PAYAMT,SUM(PAYINT) as PAYINT from {$this->MAuth->getdb('CHQTRAN')}
					where FLAG!='C' and PAYFOR in ('006','007') and CONTNO=@contno
					group by CONTNO
				) as b on a.CONTNO=b.CONTNO
			";
			//echo $sql; exit;
		}else if($payfor == '007'){
			$sql = "
				begin 
					declare @today varchar(8) = '{$tmbildt}';
					exec {$this->MAuth->getdb('FN_JD_LatePenalty')} @contno ='".$contno."',@dt = @today;
					
					insert into {$this->MAuth->getdb('hp_UserOperationLog')} (userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
					values ('".$this->sess["IDNo"]."','SYS06::ปรับปรุงเบี้ยปรับล่าช้า',' ".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
				end
			";
			//echo $sql; exit;
			$this->db->query($sql);
			
			$caldscPERD = $this->MMAIN->getCALDSC($contno);
			
			$sql = "
				declare @contno varchar(13) = '{$contno}';
				declare @caldscPERD decimal(18,2) = {$caldscPERD};
				
				declare @NPROF decimal(8,2)	= isnull((
					select sum(NPROF) as NPROF from(
						select case when PAYMENT > 0 then (NPROF/DAMT)*PAYMENT else NPROF end as  NPROF  
						from {$this->MAuth->getdb('ARPAY')}
						where CONTNO = @contno and PAYMENT < DAMT and DDATE >= '{$tmbildt}'
					) as A
				),0);
				
				select a.CONTNO
					,a.DAMT - b.PAYAMT as DAMT
					,cast(@NPROF * @caldscPERD as decimal(18,0)) as DISCT
					,isnull(a.INTAMT,0)-isnull(b.PAYINT,0) as INTAMT 
					,((a.DAMT - b.PAYAMT) - (cast(@NPROF * @caldscPERD as decimal(18,0)))) 
						+	a.INTAMT-isnull(b.PAYINT,0) as NETPAY
				from (
					select CONTNO,SUM(DAMT) as DAMT,SUM(INTAMT) as INTAMT from {$this->MAuth->getdb('ARPAY')}
					where CONTNO=@contno --and convert(varchar(8),DDATE,112) <= convert(varchar(8),GETDATE(),112)
					group by CONTNO
				) as a
				left join (
					select CONTNO,SUM(PAYAMT) as PAYAMT,SUM(PAYINT) as PAYINT from {$this->MAuth->getdb('CHQTRAN')}
					where FLAG!='C' and PAYFOR in ('006','007') and CONTNO=@contno
					group by CONTNO
				) as b on a.CONTNO=b.CONTNO
			";
			//echo $sql; exit;
		}else if($payfor == '008'){
			$sql = "
				declare @contno varchar(13) = '{$contno}';
				select a.CONTNO
					,a.DAMT-isnull(b.PAYAMT,0) as DAMT
					,0 as DISCT
					,a.INTAMT-isnull(b.PAYINT,0) as INTAMT 
					,(a.DAMT-isnull(b.PAYAMT,0)) + (a.INTAMT-isnull(b.PAYINT,0)) as NETPAY
				from (
					select RESVNO as CONTNO
						,SUM(RESPAY) as DAMT
						,0 as INTAMT 
					from {$this->MAuth->getdb('ARRESV')}
					where RESVNO=@contno
					group by RESVNO
				) as a
				left join (
					select CONTNO,SUM(PAYAMT) as PAYAMT,SUM(PAYINT) as PAYINT from {$this->MAuth->getdb('CHQTRAN')}
					where FLAG!='C' and PAYFOR ='008' and CONTNO=@contno
					group by CONTNO
				) as b on a.CONTNO=b.CONTNO
			";
		}else{
			$sql = "
				declare @contno varchar(13) = '{$contno}';
				select @contno as CONTNO,0 as DAMT,0 as INTAMT,0 as NETPAY
			";
		}
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		if($query->row()){
			foreach($query->result() as $row){
				$data["PAYAMT"] = number_format($row->DAMT,2);
				$data["DISCT"]  = number_format($row->DISCT,2);
				$data["PAYINT"] = number_format($row->INTAMT,2);
				$data["DSCINT"] = number_format(0,2);
				$data["NETPAY"] = number_format($row->NETPAY,2);
			}
		}
		
		echo json_encode($data);
	}
	
	function Search(){
		$TMBILL = $_POST["TMBILL"];
		$BILLNO = $_POST["BILLNO"];
		$LOCATRECV = $_POST["LOCATRECV"];
		$CUSCOD = $_POST["CUSCOD"];
		$STMBILDT = $this->Convertdate(1,$_POST["STMBILDT"]);
		$ETMBILDT = $this->Convertdate(1,$_POST["ETMBILDT"]);
		
		$cond = "";
		if($TMBILL != ""){
			$cond .= " and a.TMBILL like '%".$TMBILL."%'";
		}
		
		if($BILLNO != ""){
			$cond .= " and a.BILLNO like '%".$BILLNO."%'";
		}
		
		if($LOCATRECV != ""){
			$s = sizeof($LOCATRECV);
			$locat = "";
			for($i=0;$i<$s;$i++){
				$locat .= "'".$LOCATRECV[$i]."'";
			}
			
			if($locat != ""){
				$cond .= " and a.LOCATRECV in (".str_replace("''","','",$locat).")";				
			}
		}
		
		if($CUSCOD != ""){
			$cond .= " and a.CUSCOD like '%".$CUSCOD."%'";
		}
		
		if($STMBILDT != "" && $ETMBILDT != ""){
			$cond .= " and a.TMBILDT between '".$STMBILDT."' and '".$ETMBILDT."' ";
		}else if ($STMBILDT != "" && $ETMBILDT == ""){
			$cond .= " and a.TMBILDT = '".$STMBILDT."' ";
		}else if ($STMBILDT == "" && $ETMBILDT != ""){
			$cond .= " and a.TMBILDT = '".$ETMBILDT."' ";
		}
		
		$sql = "
			select top 100 a.TMBILL,a.TMBILDT,a.LOCATRECV,a.BILLNO,a.BILLDT
				,a.CUSCOD,a.PAYTYP,b.PAYFOR,b.CONTNO,b.PAYAMT,b.DISCT,b.PAYINT,b.DSCINT,b.NETPAY
				,b.FLAG
			from {$this->MAuth->getdb("CHQMAS")} a
			left join {$this->MAuth->getdb("CHQTRAN")} b on a.TMBILL=b.TMBILL
			where 1=1 ".$cond." and b.TMBILL is not null
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$html .= "
					<tr class='".($row->FLAG == "C" ? "text-red":"")."'>
						<td class='text-blue'>
							<input type='button' class='billDetails btn btn-xs btn-info' TMBILL='".$row->TMBILL."' value='รายละเอียด'>
						</td>
						<td class='text-blue'>".$row->TMBILL."</td>
						<td>".$this->Convertdate(103,$row->TMBILDT)."</td>
						<td>".$row->LOCATRECV."</td>
						<td>".$row->BILLNO."</td>
						<td>".$this->Convertdate(103,$row->BILLDT)."</td>
						<td>".$row->CUSCOD."</td>
						<td>".$row->PAYTYP."</td>
						<td>".$row->PAYFOR."</td>
						<td>".$row->CONTNO."</td>
						<td align='right'>".number_format($row->PAYAMT,2)."</td>
						<td align='right'>".number_format($row->DISCT,2)."</td>
						<td align='right'>".number_format($row->PAYINT,2)."</td>
						<td align='right'>".number_format($row->DSCINT,2)."</td>
						<td align='right'>".number_format($row->NETPAY,2)."</td>
					</tr>
				";
			}
		}
		
		$html = "
			<table id='table-payment' border=1 width='100%'>
				<thead>
					<tr>
						<th>#</th>
						<th>ใบรับชั่วคราว</th>
						<th>วันที่รับ</th>
						<th>สาขา</th>
						<th>เลขที่ใบเสร็จ</th>
						<th>วันที่ใบเสร็จ</th>
						<th>รหัสลูกค้า</th>
						<th>ประเภทชำระ</th>
						<th>ชำระค่า</th>
						<th>เลขที่สัญญา</th>
						<th>จำนวน</th>
						<th>ส่วนลด</th>
						<th>เบี้ยปรับ</th>
						<th>ส่วนลดเบี้ยปรับ</th>
						<th>รวมชำระ</th>
					</tr>
				</thead>
				<tbody>".$html."</tbody>
			</table>
		";
		
		
		$response = array();
		$response["html"] = $html;
		echo json_encode($response);
	}
	
	function SearchBillDetails(){
		$TMBILL = $_POST["TMBILL"];
		
		$sql = "";
	}
	
	function getDataCalC(){
		$response = array();
		$response["error"] = false;
		
		$PAYAMT = str_replace(",","",($_POST["PAYAMT"]==""?0:$_POST["PAYAMT"]));
		$DISCT  = str_replace(",","",($_POST["DISCT"]==""?0:$_POST["DISCT"]));
		$PAYINT = str_replace(",","",($_POST["PAYINT"]==""?0:$_POST["PAYINT"]));
		$DSCINT = str_replace(",","",($_POST["DSCINT"]==""?0:$_POST["DSCINT"]));
		$NETPAY = str_replace(",","",($_POST["NETPAY"]==""?0:$_POST["NETPAY"]));
		
		$NETPAY  = ($PAYAMT + $PAYINT) - ($DISCT + $DSCINT);
		
		if($PAYAMT == 0 && $PAYINT == 0){
			$response["error"] = true;
			$response["errorMessage"] = "ผิดพลาด คุณยังไม่ระบุยอดรับชำระหรือค่าเบี้ยปรับ";
		}else if($PAYAMT < $DISCT){
			$response["error"] = true;
			$response["errorMessage"] = "ผิดพลาด ส่วนลดมากกว่ายอดรับชำระ  <br>โปรดตรวจสอบข้อมูลใหม่อีกครั้ง";
		}else if($PAYINT < $DSCINT){
			$response["error"] = true;
			$response["errorMessage"] = "ผิดพลาด ส่วนลดเบี้ยปรับมากกว่าค่าเบี้ยปรับ<br>โปรดตรวจสอบข้อมูลใหม่อีกครั้ง";
		}
		
		
		
		$response["PAYAMT"] = number_format($PAYAMT,2);
		$response["DISCT"]  = number_format($DISCT,2);
		$response["PAYINT"] = number_format($PAYINT,2);
		$response["DSCINT"] = number_format($DSCINT,2);
		$response["NETPAY"] = number_format($NETPAY,2);
		echo json_encode($response);
	}
	
	function getFORMSETAlertData(){
		$html = "
			<div class='row'>
				<div class='col-sm-3'>
					<div class='form-group'>
						เลขที่สัญญา
						<input type='text' id='alert_contno' class='form-control input input-sm'>
					</div>	
				</div>
				<div class='col-sm-3'>
					<div class='form-group'>
						สาขา
						<select id='alert_locat' class='form-control' title='เลือก'   data-actions-box='true' data-size='8' data-live-search='true'>
							".$this->MMAIN->Option_get_locat($this->sess["branch"])."
						</select>
					</div>	
				</div>
				<div class='col-sm-3'>
					<div class='form-group'>
						วันที่แจ้งเตือน จาก
						<input type='text' id='alert_sdate' class='form-control input-sm' placeholder='จาก' data-provide='datepicker' data-date-language='th-th' value='".$this->today('today')."'>
					</div>	
				</div>
				<div class='col-sm-3'>
					<div class='form-group'>
						วันที่แจ้งเตือน ถึง
						<input type='text' id='alert_edate' class='form-control input-sm' placeholder='ถึง' data-provide='datepicker' data-date-language='th-th' value=''>
					</div>	
				</div>				
			</div>
			
			<div class='row'>
				<div class='col-sm-6'>	
					<div class='form-group'>
						<button id='alert_btnSET' class='btn btn-cyan btn-block'>
							<span class='glyphicon glyphicon-pencil'> เพิ่มรายการใหม่</span>
						</button>
					</div>
				</div>
				<div class='col-sm-6'>	
					<div class='form-group'>
						<button id='alert_btnSearch' class='btn btn-primary btn-block'>
							<span class='glyphicon glyphicon-search'> แสดง</span>
						</button>
					</div>
				</div>
			</div>
			
			<div id='result_alert' style='height:calc(100vh - 190px);overflow:auto;'></div>
		";
		
		$response = array();
		$response["html"] = $html;
		echo json_encode($response);
	}
	
	function getSearchAlert(){
		$contno = $_POST["CONTNO"];
		$locat  = $_POST["LOCAT"];
		$sdate  = $this->Convertdate(1,$_POST["SDATE"]);
		$edate  = $this->Convertdate(1,$_POST["EDATE"]);
		
		$cond = "";
		if($contno != ""){
			$cond .= " and CONTNO like '%{$contno}%'";
		}
		if($locat != ""){
			$cond .= " and LOCAT like '%{$locat}%'";
		}
		
		if($sdate != "" && $edate != ""){
			$cond .= " and (('{$sdate}' BETWEEN STARTDT AND ENDDT) or ('{$edate}' BETWEEN STARTDT AND ENDDT))";
		}else if($sdate != "" && $edate == ""){
			$cond .= " and '{$sdate}' BETWEEN STARTDT AND ENDDT";
		}else if($sdate == "" && $edate != ""){
			$cond .= " and '{$edate}' BETWEEN STARTDT AND ENDDT";
		}
		
		$sql = "
			select top 100 CONTNO,LOCAT,STARTDT,ENDDT,MEMO1,INPDT,USERID 
			from {$this->MAuth->getdb('ALERTMSG')} 
			where 1=1 ".$cond."
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$html .= "<tr>";
				$html .= "<td><input type='button' class='btn btn-xs btn-warning' value='แก้ไข'></td>";
				foreach($row as $key => $val){
					switch($key){
						case 'STARTDT':
						case 'ENDDT':
							$html .= "<td>".$this->Convertdate(103,$val)."</td>";
							break;
						case 'INPDT':
							$html .= "<td>".$this->Convertdate(103,$val)."</td>";
							$html .= "<td>".$this->Convertdate(108,$val)."</td>";
							break;
						case 'MEMO1':
							$html .= "<td style='max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;'>".$val."</td>";
							break;
						default:
							$html .= "<td>".$val."</td>";
							break;
					}
				}
				$html .= "</tr>";
			}
		}
		
		$html = "
			<div class='col-sm-12'>
				<table border=1 width='100%'>
					<thead>
						<tr>
							<th></th>
							<th>เลขที่สัญญา</th>
							<th>สาขา</th>
							<th>วันที่แจ้งเตือน</th>
							<th>สิ้นสุดแจ้งเตือน</th>
							<th>ข้อความ</th>
							<th>วันที่บันทึก</th>
							<th>เวลา</th>
							<th>ผู้บันทึก</th>
						</tr>
					</thead>
					<tbody>{$html}</tbody>
				</table>
			</div>
		";
		
		$response = array();
		$response["html"] = $html;
		echo json_encode($response);
	}
	
	function getSETAlertData(){
		
		$html = "
			<div class='row'>
				<div class='col-sm-12'>
					<div class='form-group'>
						เลขที่สัญญา
						<input type='text' id='alert_ae_contno' class='form-control input input-sm'>
					</div>	
				</div>
			</div>
			
			<div class='row'>
				<div class='col-sm-6'>
					<div class='form-group'>
						วันที่เริ่มแจ้งเตือน
						<input type='text' id='alert_ae_sdate' class='form-control input-sm' placeholder='จาก' data-provide='datepicker' data-date-language='th-th' value='".$this->today('today')."'>
					</div>	
				</div>
				<div class='col-sm-6'>
					<div class='form-group'>
						วันที่สิ้นสุด
						<input type='text' id='alert_ae_edate' class='form-control input-sm' placeholder='ถึง' data-provide='datepicker' data-date-language='th-th' value=''>
					</div>	
				</div>
			</div>
			
			<div class='row'>
				<div class='col-sm-12'>
					<div class='form-group'>
						ข้อความแจ้งเตือน
						<textarea id='alert_ae_memo1' class='form-control' style='height:200px;'></textarea>
					</div>
				</div>	
			</div>			
			
			<div class='row'>
				<div class='col-sm-12'>
					<label class='radio-inline lobiradio-danger lobiradio'>
						<input type='radio' name='radioAlert' value='VIEW' > 
						<i></i> <span class='text-red'>ผู้อ่านแก้ไขข้อความไม่ได้ (สีแดง)</span>
					</label>
				</div>	
				<div class='col-sm-12'>	
					<label class='radio-inline lobiradio'>
						<input type='radio' name='radioAlert' value='EDIT' checked>
						<i></i> <span class='text-blue'>ผู้อ่านแก้ไขข้อความได้ (สีน้ำเงิน)</span>
					</label>
				</div>
			</div>
			
			
			
			<div class='col-sm-6 col-sm-offset-6'>	
				<i id='alert_ae_save' class='btn btn-sm btn-primary btn-block glyphicon glyphicon-floppy-disk' style='cursor:pointer;'> บันทึก  </i>
			</div>
			
		";
		
		$response = array();
		$response["html"] = $html;
		echo json_encode($response);
	}
	
	function getAlertSAVE(){
		$CONTNO = $_POST["CONTNO"];
		$SDATE  = $this->Convertdate(1,$_POST["SDATE"]);
		$EDATE  = $this->Convertdate(1,$_POST["EDATE"]);
		$MEMO1  = $_POST["MEMO1"];
		$CLAIM  = $_POST["CLAIM"];
		
		$response = array("error"=>false);
		if($SDATE == "" || $EDATE == ""){
			$response["error"] = true;
			$response["errorMessage"] = "โปรดระบุวันที่ แจ้งเตือนจาก -ถึง ให้ถูกต้อง";
			echo json_encode($response); exit;
		}
		
		if($MEMO1 == ""){
			$response["error"] = true;
			$response["errorMessage"] = "โปรดระบุข้อความที่ต้องการแจ้งเตือน";
			echo json_encode($response); exit;			
		}
		
		$sql = "
			if object_id('tempdb..#tempresult') is not null drop table #tempresult;
			create table #tempresult (id varchar(20),contno varchar(20),msg varchar(max));
			
			SET NOCOUNT ON;
			begin tran alertTran
			begin try
				declare @contno varchar(20) = '{$CONTNO}'
				declare @sdate varchar(8) = '{$SDATE}'
				declare @edate varchar(8) = '{$EDATE}'
				declare @memo1 varchar(max) = '{$MEMO1}'
				declare @claim varchar(4) = '{$CLAIM}'
				
				if (@sdate > @edate)
				begin
					rollback tran alertTran;
					insert into #tempresult select 'E','','วันที่เริ่มแจ้งเตือน มากกว่าวันที่สิ้นสุด';
					return;
				end
				
				if not exists (
					select * from {$this->MAuth->getdb('ARMAST')} 
					where CONTNO=@contno
				)
				begin
					rollback tran alertTran;
					insert into #tempresult select 'E','','ไม่พบเลขที่สัญญาในระบบ';
					return;
				end 
				
				if exists (
					select * from {$this->MAuth->getdb('ALERTMSG')} 
					where CONTNO=@contno and ((@sdate BETWEEN STARTDT AND ENDDT) or (@edate BETWEEN STARTDT AND ENDDT))
				)
				begin
					rollback tran alertTran;
					insert into #tempresult select 'E','','วันที่เริ่มแจ้งเตือน หรือวันที่สิ้นสุดแจ้งเตือน ซ้ำซ้อนกับข้อมูลที่มีอยู่ในระบบ';
					return;
				end 
				
				declare @locat varchar(5) = (select LOCAT from {$this->MAuth->getdb('ARMAST')} where CONTNO=@contno);
				insert into {$this->MAuth->getdb('ALERTMSG')} (CONTNO,LOCAT,CREATEDT,STARTDT,ENDDT,MEMO1,INPDT,USERID)
				select @contno,@locat,convert(varchar(8),getdate(),112),@sdate,@edate,@memo1,getdate(),'{$this->sess["USERID"]}'
				
				insert into {$this->MAuth->getdb('hp_UserOperationLog')} (userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
				values ('".$this->sess["IDNo"]."','SYS06::บันทึกข้อความเตือน',' ".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');

				insert into #tempresult select 'S',@contno,'บันทึกแจ้งเตือน สัญญา '+ @contno +' แล้ว'				
				commit tran alertTran;
			end try
			begin catch
				rollback tran alertTran;
				insert into #tempresult select 'E','',ERROR_MESSAGE();
			end catch
		";
		//echo $sql; exit;
		$this->db->query($sql);
		$sql = "select * from #tempresult";
		$query = $this->db->query($sql);
		
		if($query->row()){
			foreach($query->result() as $row){
				$response["error"] = ($row->id == "E" ? true : false);
				$response["contno"] = $row->contno;
				$response["errorMessage"] = $row->msg;
			}
		}else{
			$response["error"] = true;
			$response["errorMessage"] = 'ผิดพลาดไม่สามารถบันทึกข้อมูลได้ โปรดติดต่อฝ่ายไอที';
		}
		
		echo json_encode($response);		
	}
	
	function getAlertData(){
		$contno = $_POST["CONTNO"];
		
		$sql = "
			select * from {$this->MAuth->getdb('ALERTMSG')} 
			where CONTNO= '{$contno}' AND convert(varchar(8),getdate(),112) BETWEEN STARTDT AND ENDDT
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$message = "";
		$hasdata = "NULL";
		if($query->row()){
			foreach($query->result() as $row){
				$message = $row->MEMO1;
				$hasdata = "NOT NULL";
			}
		}
		
		$html = "
			<div class='col-sm-12'>
				<textarea class='form-control' style='height:200px;' readonly>{$message}</textarea>
			</div>
		";
		
		$response = array();
		$response["html"] = $html;
		$response["SHOW"] = $hasdata;
		echo json_encode($response);
	}
	
	function tmbillFormPrint(){
		$html = "เลขที่บิลไม่ถูกต้อง";
		if($_POST["TMBILL"] !== "Auto Genarate"){
			$sql = "
				select NOPRNTB,NOPRNBL from {$this->MAuth->getdb('CHQMAS')} 
				where TMBILL= '{$_POST["TMBILL"]}' 
			";
			$query = $this->db->query($sql);
			
			$data = array();
			if($query->row()){
				foreach($query->result() as $row){
					$data["NOPRNTB"] = $row->NOPRNTB;
					$data["NOPRNBL"] = $row->NOPRNBL;
				}
			}
			
			$html = "
				<div class='row'>
					<div class='col-sm-6'>
						<div class='form-group'>
							ใบรับชั่วคราว
							<div class='input-group'>
							  <input type='text' id='print_tmbill' NOPRNTB='{$data["NOPRNTB"]}' class='form-control input-sm' value='{$_POST["TMBILL"]}' readonly>
							  <span class='input-group-addon'>{$data["NOPRNTB"]}</span>
							</div>
						</div>
					</div>
					<div class='col-sm-6'>
						<div class='form-group'>
							ใบเสร็จรับเงิน
							<div class='input-group'>
							  <input type='text' id='print_billno' NOPRNBL='{$data["NOPRNBL"]}' class='form-control input-sm' value='{$_POST["BILLNO"]}' readonly>
							  <span class='input-group-addon'>{$data["NOPRNBL"]}</span>
							</div>
						</div>	
					</div>
				</div>

				<div class='row'>
					<div class='col-sm-12'>
						<div class='form-group'>
							<div class='row'>
								<div class='col-xs-6'>
									<label class='radio-inline lobiradio'>
										<input type='radio' name='print_type' value='tm' checked=''> 
										<i></i> ใบรับชั่วคราว
									</label>
								</div>	
								<div class='col-xs-6'>
									<label class='radio-inline lobiradio'>
										<input type='radio' name='print_type' value='bl'> 
										<i></i> ใบเสร็จรับเงิน
									</label>
								</div>
							</div>
						</div>
					</div>
					
					<div class='col-sm-12'>
						<div class='form-group'>
							ที่อยู่
							<div class='row'>
								<div class='col-xs-6'>
									<label class='radio-inline lobiradio'>
										<input type='radio' name='print_addr' value='contact' checked=''> 
										<i></i> ที่อยู่ที่ติดต่อ
									</label>
								</div>	
								<div class='col-xs-6'>
									<label class='radio-inline lobiradio'>
										<input type='radio' name='print_addr' value='contno'> 
										<i></i> ที่อยู่ตามสัญญา
									</label>
								</div>
							</div>
						</div>
					</div>
					<div class='row'>
						<div class='col-sm-6 col-sm-offset-6'>
							<i id='print_screen' CONTNO='' class='btn btn-sm btn-warning btn-block glyphicon glyphicon-search' style='cursor:pointer;'> SCREEN </i>
						</div>
					</div>
				</div>
			";
		}
		
		$response = array();
		$response["html"] = $html;
		echo json_encode($response);
	}
	
	function tmbillPDF(){
		$this->tmbillPDF2();
		
		$TMBILL   = $_POST["TMBILL"];
		$NOPRNTB  = $_POST["NOPRNTB"];
		$BILLNO   = $_POST["BILLNO"];
		$NOPRNBL  = $_POST["NOPRNBL"];
		$TMBILL   = $_POST["TMBILL"];
		$PRINTFOR = $_POST["PRINTFOR"];
		
		$data = array();
		$sql = "
			declare @data varchar(28) = '{$this->sess["IDNo"]}'
				+convert(varchar,getdate(),12)
				+replace(convert(varchar,getdate(),14),':','');
				
			select a.TMBILL,convert(varchar(8),a.TMBILDT,112) as TMBILDT
				,a.LOCATRECV
				,(select sa.LOCATNM from {$this->MAuth->getdb('INVLOCAT')} sa where sa.LOCATCD=a.LOCATRECV) LOCATNM
				,(select sa.LOCADDR2 from {$this->MAuth->getdb('INVLOCAT')} sa where sa.LOCATCD=a.LOCATRECV) LOCADDR2
				,a.CUSCOD
				,(select SNAM+NAME1+' '+NAME2 from {$this->MAuth->getdb('CUSTMAST')} sa where sa.CUSCOD=a.CUSCOD) as CUSNAME
				,convert(varchar(8),TMBILDT,112) as TMBILDT	
				,a.CHQAMT
				,a.NOPRNTB
				,a.NOPRNBL
				,YTKManagement.dbo.FN_CONVERTNUMBER('Y',@data) as CODE
			from {$this->MAuth->getdb('CHQMAS')} a
			where TMBILL='{$TMBILL}'
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		if($query->row()){
			foreach($query->result() as $row){
				$data["TMBILL"]   = $row->TMBILL;
				$data["TMBILDT"]  = $this->Convertdate(2,$row->TMBILDT);
				$data["LOCATNM"]  = $row->LOCATNM;
				$data["LOCADDR2"] = $row->LOCADDR2;
				$data["CUSNAME"]  = $row->CUSNAME;
				$data["CHQAMT"]   = $row->CHQAMT;
				
				if($PRINTFOR == "tm"){
					$data["btnPrint"] = ($row->NOPRNTB > 0 ? "disabled":"");
				}else{
					$data["btnPrint"] = ($row->NOPRNBL > 0 ? "disabled":"");
				}
				
				$data["CODE"] = $row->CODE;
			}
		}
		//$data["btnPrint"] = '';
		
		$sql = "
			select b.PAYFOR as FORCODE
				,(select FORDESC from {$this->MAuth->getdb('PAYFOR')} sa where sa.FORCODE=b.PAYFOR) + (
					case when b.PAYFOR in ('006','007') then 
						' งวดที่ '+CAST(F_PAY as varchar)+'-'+CAST(L_PAY as varchar)
						else '' 
					end) as FORDESC
				,b.PAYAMT
				,b.DISCT
				,b.PAYINT
				,b.DSCINT
				,b.NETPAY
				,b.CONTNO
			from {$this->MAuth->getdb('CHQMAS')} a
			left join {$this->MAuth->getdb('CHQTRAN')} b on a.TMBILL=b.TMBILL
			where a.TMBILL='{$TMBILL}'
		";
		$query = $this->db->query($sql);
		
		$data["body"] = "";
		if($query->row()){
			$data["DISCT"] = 0;
			$data["PAYINT"] = 0;
			$data["DSCINT"] = 0;
			
			$data["CONTNO"] = array();
			$data["H_CONTNO"] = "";
			$data["H_ENGNO"]  = "";
			$data["H_BAAB"]   = "";
			$data["H_COLOR"]  = "";
			
			foreach($query->result() as $row){
				$data["body"] .= "
					<tr>
						<td>{$row->FORDESC}</td>
						<td style='width:50px;text-align:right;'>".number_format($row->PAYAMT,2)."</td>
					</tr>
				";
				
				$data["DISCT"] += $row->DISCT;
				$data["PAYINT"] += $row->PAYINT;
				$data["DSCINT"] += $row->DSCINT;
				
				$data["FORCODE"]  = $row->FORCODE;
				$data["CONTNO"][] = $row->CONTNO;
			}
			
			$sql= "
				select count(*) as r from {$this->MAuth->getdb('ARMAST')} a
				left join {$this->MAuth->getdb('INVTRAN')} b on a.CONTNO=b.CONTNO and a.STRNO=b.STRNO
				where a.CONTNO in ('".implode("','",$data["CONTNO"])."')
			";
			$query = $this->db->query($sql);
			$query = $query->row();
			
			if($query->r != 0){
				$sql = "
					select a.CONTNO,b.STRNO,b.ENGNO,b.MODEL,b.BAAB,b.COLOR from {$this->MAuth->getdb('ARMAST')} a
					left join {$this->MAuth->getdb('INVTRAN')} b on a.CONTNO=b.CONTNO and a.STRNO=b.STRNO
					where a.CONTNO in ('".implode("','",$data["CONTNO"])."')
				";
				//echo $sql; exit;
				$query = $this->db->query($sql);
				
				if($query->row()){
					foreach($query->result() as $row){
						$data["H_CONTNO"] 	= $row->CONTNO;
						$data["H_ENGNO"] 	= $row->ENGNO;
						$data["H_BAAB"] 	= $row->BAAB;
						$data["H_COLOR"] 	= $row->COLOR;
					}
				}
			}
			
		}
		
		$data["body"] .= "
			<tr>
				<td>ส่วนลด</td>
				<td style='width:50px;text-align:right;'>".number_format($data["DISCT"],2)."</td>
			</tr>
			<tr>
				<td>เบี้ยปรับ</td>
				<td style='width:50px;text-align:right;'>".number_format($data["PAYINT"],2)."</td>
			</tr>
			<tr>
				<td>ส่วนลดเบี้ยปรับ</td>
				<td style='width:50px;text-align:right;'>".number_format($data["DSCINT"],2)."</td>
			</tr>
		";
		
		
		$imgPath = 'public/images/tmbill_temp/'.md5($TMBILL).'.png';
		//$link = "https://survey.myftp.org/questionnair/Assessment_client/Assessment_client.php?id=".$this->_base64_encrypt_qr($data["CODE"]);
		$link = "https://survey.myftp.org/questionnair/q/a.php?id=".$this->_base64_encrypt_qr($data["CODE"]);
		QRcode::png($link,$imgPath,'S','4',2);
		
		$content = "
			<table style='width:100%;font-size:10pt;'>
				<tr><th colspan='2'>{$data["LOCATNM"]}</th></tr>
				<tr><th colspan='2'>{$data["LOCADDR2"]}</th></tr>
				
				<tr>
					<td style='width:35%;'>ชื่อ-สกุล ลูกค้า </td>
					<td style='width:65%;'>{$data["CUSNAME"]}</td>
				</tr>
				<tr>
					<td style='width:35%;'>เลขที่ใบรับเงิน</td>
					<td style='width:65%;'>{$data["TMBILL"]}</td>
				</tr>
				<tr>
					<td style='width:35%;'>วดป.</td>
					<td style='width:65%;'>{$data["TMBILDT"]}</td>
				</tr>
				<tr>
					<td style='width:35%;'>เลขที่สัญญา</td>
					<td style='width:65%;'>{$data["H_CONTNO"]}</td>
				</tr>
				<tr>
					<td style='width:35%;'>เลขเครื่อง</td>
					<td style='width:65%;'>{$data["H_ENGNO"]}</td>
				</tr>
				<tr>
					<td style='width:35%;'>สี</td>
					<td style='width:65%;'>{$data["H_COLOR"]}</td>
				</tr>
				<tr>
					<td style='width:35%;'>เงื่อนไขการโอน</td>
					<td style='width:65%;'>{$data["H_BAAB"]}</td>
				</tr>
			</table>
			
			<div>&emsp;</div>
			<table border=0 style='width:100%;font-size:10pt;'>
				<tr><th colspan='2'><hr></th></tr>
				<tr class='borderTB' style='color:red;border-top:0.1px solid black;border-bottom:1px solid black;'>
					<th style='text-align:left;'>ชำระค่า</th>
					<th style='text-align:right;'>จำนวนเงิน</th>
				</tr>
				<tr><th colspan='2'><hr></th></tr>
				
				<tr>
					<td colspan='2'>
						<table border=0 style='width:100%;font-size:10pt;'>
							".$data["body"]."
						</table>
					</td>
				</tr>
				
				<tr><th colspan='2'><hr></th></tr>
				<tr class='borderTB' style='color:red;border-top:0.1px solid black;border-bottom:1px solid black;'>
					<th style='text-align:left;'>ยอดรับสุทธิ</th>
					<th style='text-align:right;'>".number_format($data["CHQAMT"],2)."</th>
				</tr>
				<tr><th colspan='2'><hr></th></tr>
				<tr><td colspan='2'>&emsp;</td></tr>
				<tr><td colspan='2' style='padding-left:10px;'>ลงชื่อ..............................................ผู้รับเงิน</td></tr>
				<tr><td colspan='2' style='line-height:10px;'>&emsp;</td></tr>
				<tr><td colspan='2' style='padding-left:10px;'>ลงชื่อ..............................................ลูกค้า</td></tr>
				<tr><td colspan='2'>&emsp;</td></tr>
				<tr><td colspan='2' align='center'><img src='../{$imgPath}' style='width:100px;height:100px;'/></td></tr>
				<tr><td colspan='2' style='text-align:center;'>(แบบประเมินความเพิ่งพอใจ)</td></tr>
				
				<tr><td colspan='2' style='text-align:center;color:red;line-height:20px;'>กรุณาตรวจสอบใบเสร็จฯทุกครั้ง<br>ใบเสร็จฯจะต้องไม่มีร่องรอยการแก้ไข</td></tr>
			</table>
		";
		
		$html = "
			<div align='center'>
				<div style='width:100%;height:calc(100vh - 300px);overflow:auto;background-color:#bbb;'>
					<div id='div_print_tm' style='max-width:74mm;height:auto;background-color:#fff;border:1px solid black;font-size:10pt;padding:10px;'>
						".$content."
					</div>
				</div>
				<br>&emsp;
				
				<input type='button' {$data["btnPrint"]} id='print_tm' code='".$data["CODE"]."' class='btn btn-primary' value='พิมพ์ใบรับชั่วคราว'>
			</div>
		";
		
		if($this->agent->browser() == "Firefox"){
			$response = array();
			$response["error"] = false;
			$response["html"] = $html;
			$response["OS"] = $this->agent->platform();
			echo json_encode($response);			
		}else{
			$response = array();
			$response["error"] = true;
			$response["html"] = "กรณีปริ้นใบรับชั่วคราว ให้เข้าใช้งานด้วย Firefox เท่านั้นครับ";
			$response["OS"] = $this->agent->platform();
			echo json_encode($response);
		}
	}
	
	function _base64_encrypt_qr($str,$passw=null){
            $r='';
            $md=$passw?substr(md5($passw),0,16):'';
            $str=base64_encode($md.$str);
            $abc='ABCDE1FGHIJKL3MNOP7QRSTUVW6XY2Zabcdef8ghijk4lmno5qrst9uvwx0yz';
            $a=str_split('+/='.$abc);
            $b=strrev('-_='.$abc);
            if($passw){
                    $b=$this->_mixing_passw_qr($b,$passw);
            }else{
                    $r=rand(10,65);
                    $b=mb_substr($b,$r).mb_substr($b,0,$r);
            }
            $s='';
            $b=str_split($b);
            $str=str_split($str);
            $lens=count($str);
            $lena=count($a);
            for($i=0;$i<$lens;$i++){
                    for($j=0;$j<$lena;$j++){
                            if($str[$i]==$a[$j]){
                                    $s.=$b[$j];
                            }
                    };
            };
            return $s.$r;
    }
	
    function _mixing_passw_qr($b,$passw){
            $s='';
            $c=$b;
            $b=str_split($b);
            $passw=str_split(sha1($passw));
            $lenp=count($passw);
            $lenb=count($b);
            for($i=0;$i<$lenp;$i++){
                    for($j=0;$j<$lenb;$j++){
                            if($passw[$i]==$b[$j]){
                                    $c=str_replace($b[$j],'',$c);
                                    if(!preg_match('/'.$b[$j].'/',$s)){
                                            $s.=$b[$j];
                                    }
                            }
                    };
            };
            return $c.''.$s;
    }
	
	function Append2Assessment(){
		$db = $this->load->database('ASSESSMENT',true);
		//print_r($this->sess); exit;
		$sql = "
			begin tran x
			begin try 
				insert into ASSESSMENT_DB.dbo.DATA_DOCUMENT_CHECK (DOCNo,EmpNo,CTM_RFN,Head,Status,date,Time,entity,Branch)
				select '".$_POST["code"]."'
					,'".$this->sess["employeeCode"]."'
					,'".$_POST["tmbill"]."'
					,24
					,'N'
					,convert(varchar(8),getdate(),112)
					,convert(varchar(8),getdate(),108)
					,'TJ_S'
					,'".$this->sess["branch"]."'
					
				commit tran x
			end try
			begin catch
				rollback tran x;
			end catch
		";
		$db->query($sql);
		
		$sql = "
			update {$this->MAuth->getdb('CHQMAS')}
			set NOPRNTB=isnull(NOPRNTB,0)+".($_POST["print_type"] == "tm" ? 1:0)."
				,NOPRNBL=isnull(NOPRNBL,0)+".($_POST["print_type"] == "bl" ? 1:0)."
			where TMBILL='{$_POST["tmbill"]}'
		";
		//echo $sql; exit;
		$this->db->query($sql);
		
		$imgPath = 'public/images/tmbill_temp/'.md5($_POST["tmbill"]).'.png';
		unlink($imgPath);
	}
	
	function getAROthers(){
		$CUSCOD = $_POST["CUSCOD"];
		$CONTNO = $_POST["CONTNO"];
		$LOCAT  = $_POST["LOCAT"];
		
		$sql = "
			select a.*,b.FORDESC from {$this->MAuth->getdb('AROTHR')} a
			left join {$this->MAuth->getdb('PAYFOR')} b on a.PAYFOR=b.FORCODE
			where a.CUSCOD='{$CUSCOD}'
		";
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$html .= "
					<tr>
						<td>".$row->ARCONT."</td>
						<td>".$row->TSALE."</td>
						<td>".$row->CONTNO."</td>
						<td>".$row->CUSCOD."</td>
						<td>".$row->LOCAT."</td>
						<td>(".$row->PAYFOR.") ".$row->FORDESC."</td>
						<td align='right'>".number_format($row->VATRT,2)."</td>
						<td align='right'>".number_format($row->PAYAMT,2)."</td>
						<td align='right'>".number_format($row->SMPAY,2)."</td>
					</tr>
				";
			}
		}
		
		$html = "
			<table class='table table-bordered'>
				<thead>
					<tr>
						<th>เลขที่สัญญาลูกหนี้อื่น</th>
						<th>ประเภทขาย</th>
						<th>เลขที่สัญญา</th>
						<th>รหัสลูกค้า</th>
						<th>สาขา</th>
						<th>ชำระค่า</th>
						<th>ภาษี(%)</th>
						<th>ยอดตั้งหนี้</th>
						<th>ชำระแล้ว</th>
					</tr>
				</thead>
				<tbody>".$html."</tbody>
			</table>
		";
		
		$response = array();
		$response["html"] = $html;
		echo json_encode($response);
	}
	
	function getARPAY(){
		$CUSCOD = $_POST["CUSCOD"];
		$CONTNO = $_POST["CONTNO"];
		$LOCAT  = $_POST["LOCAT"];
		
		$sql = "
			select NOPAY,DDATE,DAMT,VATRT,N_DAMT,V_DAMT,DATE1
				,PAYMENT,DELAY,ADVDUE,TAXINV,TAXDT,CONTNO,LOCAT 
			from {$this->MAuth->getdb('ARPAY')} a
			where a.CONTNO='{$CONTNO}'
			order by a.NOPAY
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$html .= "
					<tr>
						<td>".$row->NOPAY."</td>
						<td>".$this->Convertdate(103,$row->DDATE)."</td>
						<td align='right'>".number_format($row->DAMT,2)."</td>
						<td align='right'>".number_format($row->VATRT,2)."</td>
						<td align='right'>".number_format($row->N_DAMT,2)."</td>
						<td align='right'>".number_format($row->V_DAMT,2)."</td>
						<td>".$this->Convertdate(103,$row->DATE1)."</td>
						<td class='text-blue' align='right'>".number_format($row->PAYMENT,2)."</td>
						<td class='text-red' align='right'>".$row->DELAY."</td>
						<td align='right'>".$row->ADVDUE."</td>
						<td>".$row->TAXINV."</td>
						<td>".$row->TAXDT."</td>
						<td>".$row->CONTNO."</td>
						<td>".$row->LOCAT."</td>
					</tr>
				";
			}
		}
		
		$html = "
			<table class='table table-bordered'>
				<thead>
					<tr>
						<th>งวด</th>
						<th>ดิวเดต</th>
						<th>จำนวนเงิน</th>
						<th>ภาษี(%)</th>
						<th>จำนวนมูลค่า</th>
						<th>จำนวนภาษี</th>
						<th>วันที่ชำระ</th>
						<th>จำนวนชำระ</th>
						<th>#วันล่าช้า</th>
						<th>จ่ายล่วงหน้า</th>
						<th>ใบกำกับภาษี</th>
						<th>วันที่ใบกำกับภาษี</th>
						<th>เลขที่สัญญา</th>
						<th>สาขา</th>
					</tr>
				</thead>
				<tbody>".$html."</tbody>
			</table>
		";
		
		$response = array();
		$response["html"] = $html;
		echo json_encode($response);
	}
	
	function getListPayments(){
		$CUSCOD = $_POST["CUSCOD"];
		$CONTNO = $_POST["CONTNO"];
		$LOCAT  = $_POST["LOCAT"];
		
		$sql = "
			select a.CONTNO,a.LOCATPAY,a.TMBILL,a.TMBILDT 
				,a.PAYAMT,a.DISCT,a.PAYINT,a.DSCINT,a.NETPAY
				,a.PAYTYP,b.CHQNO,b.LOCATRECV,a.F_PAR,a.F_PAY,a.L_PAR,a.L_PAY
				,a.FLAG,a.CANDT,a.INPDT,a.USERID
			from {$this->MAuth->getdb('CHQTRAN')} a
			left join {$this->MAuth->getdb('CHQMAS')} b on a.TMBILL=b.TMBILL and a.TMBILDT=b.TMBILDT
			where a.CONTNO='{$CONTNO}' and a.PAYFOR in ('006','007')
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$html .= "
					<tr>
						<td>".$row->CONTNO."</td>
						<td>".$row->LOCATPAY."</td>
						<td>".$row->TMBILL."</td>
						<td>".$this->Convertdate(103,$row->TMBILDT)."</td>
						<td class='text-red' align='right'>".number_format($row->PAYAMT,2)."</td>
						<td align='right'>".number_format($row->DISCT,2)."</td>
						<td class='text-pink' align='right'>".number_format($row->PAYINT,2)."</td>
						<td align='right'>".number_format($row->DSCINT,2)."</td>
						<td class='text-red' align='right'>".number_format($row->NETPAY,2)."</td>
						<td>".$row->PAYTYP."</td>
						<td>".$row->CHQNO."</td>
						<td>".$row->LOCATRECV."</td>
						<td align='right'>".$row->F_PAR."</td>
						<td align='right'>".number_format($row->F_PAY,0)."</td>
						<td align='right'>".$row->L_PAR."</td>
						<td align='right'>".number_format($row->L_PAY,0)."</td>
						<td>".$row->FLAG."</td>
						<td>".$this->Convertdate(103,$row->CANDT)."</td>
						<td>".$this->Convertdate(103,$row->INPDT)."</td>
						<td>".$row->USERID."</td>
					</tr>
				";
			}
		}
		
		$html = "
			<table class='table table-bordered'>
				<thead>
					<tr>
						<th>เลขที่สัญญา</th>
						<th>สาขา</th>
						<th>บิลรับชั่วคราว</th>
						<th>วันที่</th>
						<th>จำนวนเงิน</th>
						<th>ส่วนลด</th>
						<th>เบี้ยปรับ</th>
						<th>ส่วนลดเบี้ยปรับ</th>
						<th>รับสุทธิ</th>
						<th>ประเภท<br>การชำระ</th>
						<th>เลขที่เช็ค</th>
						<th>รับที่สาขา</th>
						<th>F_PAR</th>
						<th>F_PAY</th>
						<th>L_PAR</th>
						<th>L_PAR</th>
						<th>C:ยกเลิก</th>
						<th>วันที่ยกเลิก</th>
						<th>วันที่บันทึก</th>
						<th>ผู้บันทึก</th>
					</tr>
				</thead>
				<tbody>".$html."</tbody>
			</table>
		";
		
		$response = array();
		$response["html"] = $html;
		echo json_encode($response);
	}
	
	function SavePayments(){
		$response = array("error"=>false,"errorMessage"=>"");
		
		$arrs 				= array();
		$arrs["TMBILL"] 	= $_POST["TMBILL"];
		$arrs["TMBILDT"] 	= $this->Convertdate(1,$_POST["TMBILDT"]);
		$arrs["LOCATRECV"] 	= $_POST["LOCATRECV"];
		$arrs["PAYTYP"] 	= $_POST["PAYTYP"];
		$arrs["CUSCOD"] 	= $_POST["CUSCOD"];
		$arrs["REFNO"] 		= trim($_POST["REFNO"]);
		$arrs["CHQNO"]		= trim($_POST["CHQNO"]);
		$arrs["CHQDT"] 		= $this->Convertdate(1,$_POST["CHQDT"]);
		$arrs["CHQAMT"] 	= $_POST["CHQAMT"];
		$arrs["CHQBK"] 		= ($_POST["CHQBK"] == 'nouse' ? '':$_POST["CHQBK"]);
		$arrs["CHQBR"] 		= trim($_POST["CHQBR"]);
		$arrs["BILLNO"] 	= $_POST["BILLNO"];
		$arrs["BILLDT"] 	= $this->Convertdate(1,$_POST["BILLDT"]);
		$data_payment = (isset($_POST["data_payment"]) ? json_decode($_POST["data_payment"],true) : array());
		
		if(sizeof($data_payment) == 0){
			$response["error"] = true;
			$response["errorMessage"] = "ยังไม่ได้ระบุรายการรับชำระ";
			echo json_encode($response); exit;
		}
		
		if($arrs["PAYTYP"] == "02" && $arrs["CHQNO"] == ""){
			$response["error"] = true;
			$response["errorMessage"] = "ยังไม่ได้ระบุเลขที่เช็ค";
			echo json_encode($response); exit;
		}else if($arrs["PAYTYP"] == "02" && $arrs["CHQDT"] == ""){
			$response["error"] = true;
			$response["errorMessage"] = "ยังไม่ได้ระบุวันที่เช็ค";
			echo json_encode($response); exit;
		}else if($arrs["PAYTYP"] == "02" && $arrs["CHQBK"] == ""){
			$response["error"] = true;
			$response["errorMessage"] = "ยังไม่ได้ระบุธนาคาร";
			echo json_encode($response); exit;
		}
		
		if($arrs["TMBILL"] == "Auto Genarate"){
			$data_payment_size = sizeof($data_payment);
			
			$CHQTRANQ  = '';
			$PAYFORTAX = '';
			for($i=0;$i<$data_payment_size;$i++){
				switch($data_payment[$i]["opt_payfor"]){
					case '001' : $CHQTRANQ .= $this->query001($arrs,$data_payment[$i]); break;
					case '002' : $CHQTRANQ .= $this->query002($arrs,$data_payment[$i]); break;
					case '003' : $CHQTRANQ .= $this->query003($arrs,$data_payment[$i]); break;
					case '004' : $CHQTRANQ .= $this->query004($arrs,$data_payment[$i]); break;
					case '005' : $CHQTRANQ .= $this->query005($arrs,$data_payment[$i]); break;
					case '006' : 
						//$PAYFORTAX = '006';
						$CHQTRANQ .= $this->query006($arrs,$data_payment[$i]); break;
					case '007' : 
						$PAYFORTAX = '007';
						$CHQTRANQ .= $this->query007($arrs,$data_payment[$i]); break;
					case '008' : $CHQTRANQ .= $this->query008($arrs,$data_payment[$i]); break;
					case '009' : $CHQTRANQ .= $this->query009($arrs,$data_payment[$i]); break;
					case '011' : $CHQTRANQ .= $this->query011($arrs,$data_payment[$i]); break;
					default : $CHQTRANQ .= $this->queryOTH($arrs,$data_payment[$i]); break;
				}
			}
			
			$sql = "
				if OBJECT_ID('tempdb..#paymentTemp') is not null drop table #paymentTemp;
				create table #paymentTemp (id varchar(20),tmbill varchar(20),msg varchar(max));
				
				SET NOCOUNT ON;
				begin tran transaction1
				begin try
					/* @symbol = สัญลักษณ์แทนประเภทของเลขที่ นั้นๆ */
					declare @symbol varchar(10) = (select H_TMPBILL from {$this->MAuth->getdb('CONDPAY')});
					/* @rec = รหัสพื้นฐาน */
					declare @rec varchar(10) = (select SHORTL+@symbol+'-'+right(left(convert(varchar(8),GETDATE(),112),6),4) from {$this->MAuth->getdb('INVLOCAT')} where LOCATCD='".$arrs["LOCATRECV"]."');
					/* @TMBILL = รหัสที่จะใช้ */
					declare @TMBILL varchar(12) = isnull((select MAX(TMBILL) from {$this->MAuth->getdb('CHQMAS')} where TMBILL like ''+@rec+'%'),@rec+'0000');
					set @TMBILL = left(@TMBILL ,8)+right(right(@TMBILL ,4)+10001,4);
					
					declare @symbol2 varchar(10) = (select H_BILLNO from {$this->MAuth->getdb('CONDPAY')});
					declare @rec2 varchar(10) = (select SHORTL+@symbol2+'-'+right(left(convert(varchar(8),GETDATE(),112),6),4) from {$this->MAuth->getdb('INVLOCAT')} where LOCATCD='".$arrs["LOCATRECV"]."');
					declare @BILLNO varchar(12) = isnull((select MAX(BILLNO) from {$this->MAuth->getdb('CHQMAS')} where BILLNO like ''+@rec2+'%'),@rec2+'0000');
					set @BILLNO = left(@BILLNO ,8)+right(right(@BILLNO ,4)+10001,4);
					
					declare @symbol3 varchar(10) = (select H_TXPAY from {$this->MAuth->getdb('CONDPAY')});
					declare @rec3 varchar(10) = (select SHORTL+@symbol3+'-'+right(left(convert(varchar(8),GETDATE(),112),6),4) from {$this->MAuth->getdb('INVLOCAT')} where LOCATCD='".$arrs["LOCATRECV"]."');
					declare @TAXNO varchar(12) = isnull((select MAX(TAXNO) from {$this->MAuth->getdb('TAXTRAN')} where TAXNO like ''+@rec3+'%'),@rec3+'0000');
					set @TAXNO = left(@TAXNO ,8)+right(right(@TAXNO,4)+10001,4);
					
					declare @LOCATRECV varchar(5) = '".$arrs["LOCATRECV"]."';
					declare @TMBILDT datetime = '".$arrs["TMBILDT"]."';
					declare @BILLDT datetime = @TMBILDT;
					declare @CUSCOD varchar(20) = '".$arrs["CUSCOD"]."';
					declare @PAYTYP varchar(2) = '".$arrs["PAYTYP"]."';
					declare @CHQNO varchar(14) = '';
					declare @CHQDT datetime = (case when @PAYTYP = '02' then '".$arrs["CHQDT"]."' else NULL end);
					declare @CHQBK varchar(12) = '".$arrs["CHQBK"]."';
					declare @CHQBR varchar(50) = '".$arrs["CHQBR"]."';
					declare @CHQAMT decimal(12,2) = ".str_replace(",","",$arrs["CHQAMT"]).";
					declare @ACCTNO varchar(12) = '';
					declare @TRAD decimal(12,2) = 0;
					declare @PAYINACC varchar(12) = '';
					declare @PAYDT datetime = (case when @PAYTYP != '02' then @TMBILDT else NULL end);
					declare @AMTPAID decimal(12,2) = 0;
					declare @CHQTMP decimal(12,2) = @CHQAMT;
					--declare @TAXNO varchar(12) = '';
					declare @TAXRT decimal(12,2);
					declare @TAXDT datetime = @TMBILDT;
					declare @RVPERCD varchar(8) = '';
					declare @RCHQCD varchar(8) = '';
					declare @FLAG varchar(1) = 'H';
					declare @YFLAG varchar(1) = 'N';
					declare @TAXFL varchar(1) = 'N';
					declare @MEMO1 varchar(max);
					declare @INPDT datetime = getdate();
					declare @USERID varchar(8) = '".$this->sess["USERID"]."';
					declare @DOSBILL varchar(15) = '';
					declare @NOPRNTB decimal(3,0) = 0;
					declare @NOPRNBL decimal(3,0) = 0;
					declare @REFNO varchar(35) = '".$arrs["REFNO"]."';
					declare @INPTIME datetime = @INPDT;
					declare @CANID varchar(8) = '';
					
					declare @PAYFOR varchar(3)
						,@CONTNO varchar(12)
						,@PAYAMT decimal(12,2)
						,@DISCT decimal(12,2)
						,@PAYINT decimal(12,2)
						,@DSCINT decimal(12,2)
						,@NETPAY decimal(12,2)
						,@PAYAMT_N decimal(12,2)
						,@PAYAMT_V decimal(12,2);
					
					declare @NOPAY int
						,@LOCATPAY varchar(5)
						,@BFPAY decimal(18,2)					
						,@FNOPAY int
						,@LNOPAY int
						,@PAYAMTCAL decimal(12,2)
						,@DELAY int 
						,@ADVDUE int
						,@TSALE varchar(1);
						
					declare @VATRT decimal(12,2);
					
					if(@PAYTYP = '02')  
					begin 
						/* รับชำระเงินแบบเช็ค */
						set @BILLNO = '';
						set @BILLDT = NULL;
					end
					
					if('{$PAYFORTAX}' != '007')
					begin
						set @TAXNO = '';
						set @TAXDT = null;
					end	
					
					insert into {$this->MAuth->getdb('CHQMAS')} (
						TMBILL, LOCATRECV, TMBILDT, BILLNO, BILLDT, 
						CUSCOD, PAYTYP, CHQNO, CHQDT, CHQBK, 
						CHQBR, CHQAMT, ACCTNO, TRAD, PAYINACC, 
						--PAYDT, AMTPAID, CHQTMP, TAXNO, RVPERCD, 
						PAYDT, AMTPAID, CHQTMP, RVPERCD, 
						RCHQCD, FLAG, TAXFL, MEMO1, INPDT, 
						USERID, DOSBILL, NOPRNTB, NOPRNBL, REFNO, 
						INPTIME, CANID
					) values (
						@TMBILL,@LOCATRECV,@TMBILDT,@BILLNO,@BILLDT,
						@CUSCOD,@PAYTYP,@CHQNO,@CHQDT,@CHQBK,
						@CHQBR,@CHQAMT,@ACCTNO,@TRAD,@PAYINACC, 
						--@PAYDT,@AMTPAID, @CHQTMP, @TAXNO, @RVPERCD, 
						@PAYDT,@AMTPAID, @CHQTMP, @RVPERCD, 
						@RCHQCD, @FLAG, @TAXFL, @MEMO1, @INPDT, 
						@USERID, @DOSBILL, @NOPRNTB, @NOPRNBL, @REFNO, 
						@INPTIME, @CANID
					);
					
					".$CHQTRANQ."
					
					if (@BILLNO != '')
					begin 
						insert into {$this->MAuth->getdb('BILLMAS')} (BILLNO, LOCATRECV, BILLDT, TMBILL, FLAG, INPDT, USERID)
						values (@BILLNO,@LOCATRECV,@BILLDT,@TMBILL,@FLAG,@INPDT,@USERID);
					end
					
					insert into #paymentTemp select 'S' as id,@TMBILL,'รับชำระเงินเรียบร้อยแล้ว <br>เลขที่บิลรับชำระ '+@TMBILL as msg;
					commit tran transaction1;
				end try
				begin catch	
					rollback tran transaction1;
					insert into #paymentTemp select 'E' as id,@TMBILL,cast(ERROR_LINE() as varchar)+'::'+ERROR_MESSAGE() as msg;
				end catch
			";
			//echo $sql; exit;
			$this->connect_db->query($sql);
			
			$sql = "select * from #paymentTemp";
			$query = $this->connect_db->query($sql);
			
			if($query->row()){
				foreach($query->result() as $row){
					$response["error"] = ($row->id == "S" ? false:true);
					$response["contno"] = $row->tmbill;
					$response["errorMessage"] = $row->msg;
				}
			}else{
				$response["error"] = false;
				$response["contno"] = '';
				$response["errorMessage"] = 'ผิดพลาดไม่สามารถบันทึกการรับชำระได้ โปรดติดต่อฝ่ายไอที';
			}
			
			echo json_encode($response);
		}
	}
	
	function CanCPayments(){
		$response = array("error"=>false,"errorMessage"=>"");
		
		$arrs = array();
		$arrs["TMBILL"] = $_POST["TMBILL"];
		if($_POST["action"] == "nopay"){
			$sql = "
				select a.TMBILL,convert(varchar(8),a.TMBILDT,112) as TMBILDT,a.LOCATRECV,a.PAYTYP
					,a.CUSCOD,a.REFNO,a.CHQNO,convert(varchar(8),a.CHQDT,112) as CHQDT,a.CHQAMT
					,a.CHQBK,a.CHQBR,a.BILLNO,convert(varchar(8),a.BILLDT,112) as BILLDT
					,b.PAYFOR,b.CONTNO,b.PAYAMT,b.DISCT,b.PAYINT,b.DSCINT,b.NETPAY
				from {$this->MAuth->getdb('CHQMAS')} a
				left join {$this->MAuth->getdb('CHQTRAN')} b on a.TMBILL=b.TMBILL and a.TMBILDT=b.TMBILDT
				where a.TMBILL='{$arrs["TMBILL"]}'
			";
			$query = $this->db->query($sql);
			
			if($query->row()){
				foreach($query->result() as $row){
					$arrs["TMBILDT"] 	= $row->TMBILDT;
					$arrs["LOCATRECV"] 	= $row->LOCATRECV;
					$arrs["PAYTYP"] 	= $row->PAYTYP;
					$arrs["CUSCOD"] 	= $row->CUSCOD;
					$arrs["REFNO"] 		= $row->REFNO;
					$arrs["CHQNO"]		= $row->CHQNO;
					$arrs["CHQDT"] 		= $row->CHQDT;
					$arrs["CHQAMT"] 	= $row->CHQAMT;
					$arrs["CHQBK"] 		= $row->CHQBK;
					$arrs["CHQBR"] 		= $row->CHQBR;
					$arrs["BILLNO"] 	= $row->BILLNO;
					$arrs["BILLDT"] 	= $row->BILLDT;
					$data_payment[] = array(
						"opt_payfor" => $row->PAYFOR
						,"opt_contno" => $row->CONTNO
						,"opt_payamt" => $row->PAYAMT
						,"opt_disct" => $row->DISCT
						,"opt_payint" => $row->PAYINT
						,"opt_dscint" => $row->DSCINT
						,"opt_netpay" => $row->NETPAY
					);
				}
			}
		}else{
			$arrs["TMBILDT"] 	= $this->Convertdate(1,$_POST["TMBILDT"]);
			$arrs["LOCATRECV"] 	= $_POST["LOCATRECV"];
			$arrs["PAYTYP"] 	= $_POST["PAYTYP"];
			$arrs["CUSCOD"] 	= $_POST["CUSCOD"];
			$arrs["REFNO"] 		= $_POST["REFNO"];
			$arrs["CHQNO"]		= $_POST["CHQNO"];
			$arrs["CHQDT"] 		= $this->Convertdate(1,$_POST["CHQDT"]);
			$arrs["CHQAMT"] 	= $_POST["CHQAMT"];
			$arrs["CHQBK"] 		= $_POST["CHQBK"];
			$arrs["CHQBR"] 		= $_POST["CHQBR"];
			$arrs["BILLNO"] 	= $_POST["BILLNO"];
			$arrs["BILLDT"] 	= $this->Convertdate(1,$_POST["BILLDT"]);
			$data_payment = (isset($_POST["data_payment"]) ? json_decode($_POST["data_payment"],true) : array());
			
			if(sizeof($data_payment) == 0){
				$response["error"] = true;
				$response["errorMessage"] = "ผิดพลาด ไม่พบรายการรับชำระ";
				echo json_encode($response); exit;
			}
		}
		
		//print_r($data_payment); exit;
		
		$SQLCANC = "";
		$data_payment_size = sizeof($data_payment);
		for($i=0;$i<$data_payment_size;$i++){
			switch($data_payment[$i]["opt_payfor"]){
				case '001' : $SQLCANC .= $this->query001($arrs,$data_payment[$i],"cancel"); break;
				case '002' : $SQLCANC .= $this->query002($arrs,$data_payment[$i],"cancel"); break;
				case '003' : $SQLCANC .= $this->query003($arrs,$data_payment[$i],"cancel"); break;
				case '004' : $SQLCANC .= $this->query004($arrs,$data_payment[$i],"cancel"); break;
				case '005' : $SQLCANC .= $this->query005($arrs,$data_payment[$i],"cancel"); break;
				case '006' : $SQLCANC .= $this->query006($arrs,$data_payment[$i],"cancel"); break;
				case '007' : $SQLCANC .= $this->query007($arrs,$data_payment[$i],"cancel"); break;
				case '008' : $SQLCANC .= $this->query008($arrs,$data_payment[$i],"cancel"); break;
				case '009' : $SQLCANC .= $this->query009($arrs,$data_payment[$i],"cancel"); break;
				case '011' : $SQLCANC .= $this->query011($arrs,$data_payment[$i],"cancel"); break;
				default : $SQLCANC .= $this->queryOTH($arrs,$data_payment[$i],"cancel"); break;
			}
		}
		
		$sql = "
			if OBJECT_ID('tempdb..#CanCPaymentTemp') is not null drop table #CanCPaymentTemp;
			create table #CanCPaymentTemp (id varchar(20),tmbill varchar(20),msg varchar(max));
			
			SET NOCOUNT ON;
			begin tran transaction1
			begin try
				declare @TMBILL varchar(13) = '{$arrs["TMBILL"]}';
				declare @LOCATRECV varchar(5) = '{$arrs["LOCATRECV"]}'; 
				
				/* ปิดสาขาแล้ว */
				if exists (
					select * from {$this->MAuth->getdb('INVLOCAT')} 
					where 1=1 and LOCATCD=@LOCATRECV and CLOSEDT is not null
				)
				begin 
					rollback tran transaction1;
					insert into #CanCPaymentTemp select 'E' as id,@TMBILL,'ปิดงวดบัญชีแล้ว ไม่สามารถยกเลิกบิลได้' as msg;
					return;
				end 
				
				/* รับชำระแบบเช็ค ทำรายการ PAYIN แล้ว */
				if exists (
					select * from {$this->MAuth->getdb('CHQMAS')} 
					where 1=1 and TMBILL=@TMBILL and LOCATRECV=@LOCATRECV and PAYTYP='02' and FLAG='B'
				)
				begin 
					rollback tran transaction1;
					insert into #CanCPaymentTemp select 'E' as id,@TMBILL,'ผิดพลาด บิลรับชำระ '+@TMBILL+' ทำรายการ PAYIN แล้ว' as msg;
					return;
				end 
				
				/* รับชำระแบบเช็ค ทำรายการบันทึกเช็คผ่านแล้ว */
				if exists (
					select * from {$this->MAuth->getdb('CHQMAS')} 
					where 1=1 and TMBILL=@TMBILL and LOCATRECV=@LOCATRECV and PAYTYP='02' and FLAG='P'
				)
				begin 
					rollback tran transaction1;
					insert into #CanCPaymentTemp select 'E' as id,@TMBILL,'ผิดพลาด บิลรับชำระ '+@TMBILL+' บันทึกเช็คผ่านแล้ว' as msg;
					return;
				end;
				
				{$SQLCANC}
				
				/*ยกเลิกใบเสร็จ*/
				if exists (
					select * from {$this->MAuth->getdb('BILLMAS')}
					WHERE 1=1 AND TMBILL=@TMBILL and BILLNO='{$arrs["BILLNO"]}' AND LOCATRECV=@LOCATRECV and FLAG='C'
				)
				begin
					rollback tran transaction1;
					insert into #CanCPaymentTemp select 'E' as id,@TMBILL,'เลขที่ใบเสร็จ {$arrs["BILLNO"]} ถูกยกเลิกไปแล้ว' as msg;
					return;
				end
				else 
				begin	
					UPDATE {$this->MAuth->getdb('BILLMAS')}
					SET FLAG='C' 
					WHERE 1=1 AND TMBILL=@TMBILL and BILLNO='{$arrs["BILLNO"]}' AND LOCATRECV=@LOCATRECV and FLAG!='C' 
				end 
				
				/*ยกเลิกบิลรับชั่วคราว*/
				if exists (
					select * from {$this->MAuth->getdb('CHQMAS')}
					WHERE 1=1 and TMBILL=@TMBILL AND LOCATRECV=@LOCATRECV and FLAG='C'
				)
				begin
					rollback tran transaction1;
					insert into #CanCPaymentTemp select 'E' as id,@TMBILL,'บิลรับชำระ '+@TMBILL+' ถูกยกเลิกไปแล้ว' as msg;
					return;
				end
				else 
				begin 
					UPDATE {$this->MAuth->getdb('CHQMAS')}
					SET FLAG='C'
						,CANDT=getdate()
						,CANID='".$this->sess["USERID"]."'
						,CANTIME=getdate()
					WHERE TMBILL=@TMBILL AND LOCATRECV=@LOCATRECV
				end
				
				insert into #CanCPaymentTemp select 'S' as id,@TMBILL,'ยกเลิกบิลรับชำระเงินเรียบร้อยแล้ว <br>เลขที่บิลรับชำระ '+@TMBILL as msg;
				commit tran transaction1;
			end try
			begin catch	
				rollback tran transaction1;
				insert into #CanCPaymentTemp select 'E' as id,@TMBILL,ERROR_MESSAGE() as msg;
			end catch
		";
		//echo $sql; exit;
		$this->connect_db->query($sql);
		
		$sql = "select * from #CanCPaymentTemp";
		$query = $this->connect_db->query($sql);
		
		if($query->row()){
			foreach($query->result() as $row){
				$response["error"] = ($row->id == "S" ? false:true);
				$response["contno"] = $row->tmbill;
				$response["errorMessage"] = $row->msg;
			}
		}else{
			$response["error"] = false;
			$response["contno"] = '';
			$response["errorMessage"] = 'ผิดพลาดไม่สามารถบันทึกการรับชำระได้ โปรดติดต่อฝ่ายไอที';
		}
		
		echo json_encode($response);
	}
	
	function query001($req,$datatable,$event = "save"){
		$CHQTRANQ_001 = "";
		if($event == "save"){
			$CHQTRANQ_001 = "
				set @CONTNO = '".$datatable["opt_contno"]."';
				
				DISABLE TRIGGER AFTINS_PAY001 ON {$this->MAuth->getdb('CHQTRAN')};
				
				set @VATRT = (select VATRT from {$this->MAuth->getdb('ARCRED')} where CONTNO=@CONTNO);
				insert into {$this->MAuth->getdb('CHQTRAN')} (
					TMBILL, LOCATRECV, TMBILDT, CHQNO, CHQDT, 
					TSALE, PAYFOR, CONTNO, LOCATPAY, CUSCOD, 
					PAYTYP, TAXRT, PAYAMT, PAYAMT_N, PAYAMT_V, 
					DISCT, PAYINT, DSCINT, NETPAY, PAYDT, 
					NOPAY, F_PAR, F_PAY, L_PAR, L_PAY, 
					TAXNO, TAXFL, INPDT, FLAG, YFLAG, 
					USERID, DOSBILL, BALANCE, INPTIME, CANID, SYSTVER
				) values (
					@TMBILL,'".$req["LOCATRECV"]."','".$req["TMBILDT"]."','".$req["CHQNO"]."',".($req["CHQDT"] == "" ? "null":"'".$req["CHQDT"]."'").",
					'C','".$datatable["opt_payfor"]."','".$datatable["opt_contno"]."',
					(select LOCAT from {$this->MAuth->getdb('ARCRED')} where CONTNO=@CONTNO),'".$req["CUSCOD"]."',
					'".$req["PAYTYP"]."',@VATRT,
					
					".str_replace(",","",$datatable["opt_payamt"]).",				
					cast(".str_replace(",","",$datatable["opt_payamt"])." / ((100+@VATRT)/100) as decimal(18,2)),
					".str_replace(",","",$datatable["opt_payamt"])." - (cast(".str_replace(",","",$datatable["opt_payamt"])." / ((100+@VATRT)/100) as decimal(18,2))),
					
					'".str_replace(",","",$datatable["opt_disct"])."','".str_replace(",","",$datatable["opt_payint"])."','".str_replace(",","",$datatable["opt_dscint"])."','".str_replace(",","",$datatable["opt_netpay"])."','".$req["TMBILDT"]."',
					0,'',0,'',0,
					'','N',getdate(),'H','',
					'".$this->sess["USERID"]."','',0,getdate(),'','YTKMini'
				);
										
				if('".$req["PAYTYP"]."'='02')
				begin
					update {$this->MAuth->getdb('ARCRED')}
					set SMCHQ=SMCHQ+@PAYAMT
						,LPAYDT=@TMBILDT
					where CONTNO=@CONTNO
				end
				else
				begin
					update {$this->MAuth->getdb('ARCRED')}
					set SMPAY=SMPAY+@PAYAMT
						,LPAYDT=@TMBILDT
					where CONTNO=@CONTNO
				end;
				
				ENABLE TRIGGER AFTINS_PAY001 ON {$this->MAuth->getdb('CHQTRAN')};
			";
		}else if($event == "cancel"){
			$CHQTRANQ_001 = "
				DISABLE TRIGGER AFTINS_PAY001 ON {$this->MAuth->getdb('CHQTRAN')};
				
				if exists (select * from {$this->MAuth->getdb('ARCRED')} where CONTNO='{$datatable["opt_contno"]}')
				begin
					if('".$req["PAYTYP"]."' = '02')
					begin
						update {$this->MAuth->getdb('ARCRED')}
						set SMCHQ=SMCHQ - ".str_replace(",","",$datatable["opt_payamt"])."
						where CONTNO='{$datatable["opt_contno"]}'
					end
					else
					begin
						update {$this->MAuth->getdb('ARCRED')}
						set SMPAY=SMPAY - ".str_replace(",","",$datatable["opt_payamt"])."
						where CONTNO='{$datatable["opt_contno"]}'
					end
				end
				
				update {$this->MAuth->getdb('CHQTRAN')}
				SET FLAG='C'
					,CANDT=getdate()
					,CANID='".$this->sess["USERID"]."'
					,CANTIME=getdate()
				where 1=1 and TMBILL='{$req["TMBILL"]}' and LOCATRECV='{$req["LOCATRECV"]}';  
				
				ENABLE TRIGGER AFTINS_PAY001 ON {$this->MAuth->getdb('CHQTRAN')};
			";
		}
		
		return $CHQTRANQ_001;
	}
	
	function query002($req,$datatable,$event = "save"){
		$CHQTRANQ_002 = "";
		if($event == "save"){
			$CHQTRANQ_002 = "
				set @CONTNO = '".$datatable["opt_contno"]."';
				
				DISABLE TRIGGER AFTINS_PAY002 ON {$this->MAuth->getdb('CHQTRAN')};
				
				declare @ch_down decimal(18,2) = (
					select TOTDWN from {$this->MAuth->getdb('ARMAST')}
					where CONTNO=@CONTNO
				)
				
				declare @ch_payment decimal(18,2) = (
					select sum(PAYAMT) as PAYAMT from {$this->MAuth->getdb('CHQTRAN')}
					where PAYFOR='002' and FLAG <> 'C' and CONTNO=@CONTNO
				)
				
				-- เงินดาวน์ น้อกว่า ที่รับชำระแล้ว + ที่กำลังจะชำระ
				if (@ch_down < (@ch_payment + ".str_replace(",","",$datatable["opt_payamt"])."))
				begin 
					rollback tran transaction1;
					insert into #paymentTemp select 'S' as id,@TMBILL,'ผิดพลาด รับชำระค่ารถเงินดาวน์ขายผ่อนครบแล้ว โปรดตรวจสอบข้อมูลใหม่อีกครั้ง'  as msg;
					return;
				end
				
				set @VATRT = (select VATRT from {$this->MAuth->getdb('ARMAST')} where CONTNO=@CONTNO);
				insert into {$this->MAuth->getdb('CHQTRAN')} (
					TMBILL, LOCATRECV, TMBILDT, CHQNO, CHQDT, 
					TSALE, PAYFOR, CONTNO, LOCATPAY, CUSCOD, 
					PAYTYP, TAXRT, PAYAMT, PAYAMT_N, PAYAMT_V, 
					DISCT, PAYINT, DSCINT, NETPAY, PAYDT, 
					NOPAY, F_PAR, F_PAY, L_PAR, L_PAY, 
					TAXNO, TAXFL, INPDT, FLAG, YFLAG, 
					USERID, DOSBILL, BALANCE, INPTIME, CANID, SYSTVER
				) values (
					@TMBILL,'".$req["LOCATRECV"]."','".$req["TMBILDT"]."','".$req["CHQNO"]."',".($req["CHQDT"] == "" ? "null":"'".$req["CHQDT"]."'").",
					'H','".$datatable["opt_payfor"]."','".$datatable["opt_contno"]."',
					(select LOCAT from {$this->MAuth->getdb('ARMAST')} where CONTNO=@CONTNO),'".$req["CUSCOD"]."',
					'".$req["PAYTYP"]."',@VATRT,
					
					".str_replace(",","",$datatable["opt_payamt"]).",				
					cast(".str_replace(",","",$datatable["opt_payamt"])." / ((100+@VATRT)/100) as decimal(18,2)),
					".str_replace(",","",$datatable["opt_payamt"])." - (cast(".str_replace(",","",$datatable["opt_payamt"])." / ((100+@VATRT)/100) as decimal(18,2))),
					
					'".str_replace(",","",$datatable["opt_disct"])."','".str_replace(",","",$datatable["opt_payint"])."','".str_replace(",","",$datatable["opt_dscint"])."','".str_replace(",","",$datatable["opt_netpay"])."','".$req["TMBILDT"]."',
					0,'',0,'',0,
					'','N',getdate(),'H','',
					'".$this->sess["USERID"]."','',0,getdate(),'','YTKMini'
				);
				
				insert into {$this->MAuth->getdb('DAWNTRN')} (
					LOCAT,CONTNO,TMBILL ,BILLNO, PAYDT,PAYAMT,PAYINT,DISCT, CHQNO,CHQDT,INPDT,USERID 
				)
				select '".$req["LOCATRECV"]."','".$datatable["opt_contno"]."',@TMBILL,'','".$req["TMBILDT"]."'
					,'".str_replace(",","",$datatable["opt_payamt"])."','".str_replace(",","",$datatable["opt_payint"])."'
					,'".str_replace(",","",$datatable["opt_disct"])."','".$req["CHQNO"]."',".($req["CHQDT"] == "" ? "null":"'".$req["CHQDT"]."'")."
					,getdate(),'".$this->sess["USERID"]."';
										
				if('".$req["PAYTYP"]."'='02')
				begin
					update {$this->MAuth->getdb('ARMAST')}
					set SMCHQ=SMCHQ+@PAYAMT
						,LPAYD='".$req["TMBILDT"]."'
						,LPAYA=".str_replace(",","",$datatable["opt_payamt"])."
					where CONTNO=@CONTNO
				end
				else
				begin
					update {$this->MAuth->getdb('ARMAST')}
					set SMPAY=SMPAY+@PAYAMT
						,LPAYD='".$req["TMBILDT"]."'
						,LPAYA=".str_replace(",","",$datatable["opt_payamt"])."
						,PAYDWN=PAYDWN+".str_replace(",","",$datatable["opt_payamt"])."
					where CONTNO=@CONTNO
				end;
				
				ENABLE TRIGGER AFTINS_PAY002 ON {$this->MAuth->getdb('CHQTRAN')};
			";
		}else if($event == "cancel"){
			$CHQTRANQ_002 = "
				DISABLE TRIGGER AFTINS_PAY002 ON {$this->MAuth->getdb('CHQTRAN')};
				
				update {$this->MAuth->getdb('DAWNTRN')} 
				SET CANDT=getdate()
				where CONTNO='{$datatable["opt_contno"]}'
				
				if exists (select * from {$this->MAuth->getdb('ARMAST')} where CONTNO='{$datatable["opt_contno"]}')
				begin
					if('".$req["PAYTYP"]."' = '02')
					begin
						update {$this->MAuth->getdb('ARMAST')}
						set SMCHQ=SMCHQ - ".str_replace(",","",$datatable["opt_payamt"])."
						where CONTNO='{$datatable["opt_contno"]}'
					end
					else
					begin
						update {$this->MAuth->getdb('ARMAST')}
						set SMPAY=SMPAY - ".str_replace(",","",$datatable["opt_payamt"])."
						where CONTNO='{$datatable["opt_contno"]}'
					end
				end
				
				update {$this->MAuth->getdb('CHQTRAN')}
				SET FLAG='C'
					,CANDT=getdate()
					,CANID='".$this->sess["USERID"]."'
					,CANTIME=getdate()
				where 1=1 and TMBILL='{$req["TMBILL"]}' and LOCATRECV='{$req["LOCATRECV"]}';  
				
				ENABLE TRIGGER AFTINS_PAY002 ON {$this->MAuth->getdb('CHQTRAN')};
			";
		}
		
		return $CHQTRANQ_002;
	}
	
	function query003($req,$datatable,$event = "save"){
		$CHQTRANQ_003 = "";
		if($event == "save"){
			$CHQTRANQ_003 = "
				set @CONTNO = '".$datatable["opt_contno"]."';
				
				DISABLE TRIGGER AFTINS_PAY003 ON {$this->MAuth->getdb('CHQTRAN')};
				
				set @VATRT = (select VATRT from {$this->MAuth->getdb('ARFINC')} where CONTNO=@CONTNO);
				insert into {$this->MAuth->getdb('CHQTRAN')} (
					TMBILL, LOCATRECV, TMBILDT, CHQNO, CHQDT, 
					TSALE, PAYFOR, CONTNO, LOCATPAY, CUSCOD, 
					PAYTYP, TAXRT, PAYAMT, PAYAMT_N, PAYAMT_V, 
					DISCT, PAYINT, DSCINT, NETPAY, PAYDT, 
					NOPAY, F_PAR, F_PAY, L_PAR, L_PAY, 
					TAXNO, TAXFL, INPDT, FLAG, YFLAG, 
					USERID, DOSBILL, BALANCE, INPTIME, CANID, SYSTVER
				) values (
					@TMBILL,'".$req["LOCATRECV"]."','".$req["TMBILDT"]."','".$req["CHQNO"]."',".($req["CHQDT"] == "" ? "null":"'".$req["CHQDT"]."'").",
					'F','".$datatable["opt_payfor"]."','".$datatable["opt_contno"]."',
					(select LOCAT from {$this->MAuth->getdb('ARFINC')} where CONTNO=@CONTNO),'".$req["CUSCOD"]."',
					'".$req["PAYTYP"]."',@VATRT,
					".str_replace(",","",$datatable["opt_payamt"]).",				
					cast(".str_replace(",","",$datatable["opt_payamt"])." / ((100+@VATRT)/100) as decimal(18,2)),
					".str_replace(",","",$datatable["opt_payamt"])." - (cast(".str_replace(",","",$datatable["opt_payamt"])." / ((100+@VATRT)/100) as decimal(18,2))),
					
					'".str_replace(",","",$datatable["opt_disct"])."','".str_replace(",","",$datatable["opt_payint"])."','".str_replace(",","",$datatable["opt_dscint"])."','".str_replace(",","",$datatable["opt_netpay"])."','".$req["TMBILDT"]."',
					0,'',0,'',0,
					'','N',getdate(),'H','',
					'".$this->sess["USERID"]."','',0,getdate(),'','YTKMini'
				);
				
				if('".$req["PAYTYP"]."'='02')
				begin
					update {$this->MAuth->getdb('ARFINC')}
					set SMCHQ=SMCHQ+@PAYAMT
						,LPAYD='".$req["TMBILDT"]."'
					where CONTNO=@CONTNO
				end
				else
				begin
					update {$this->MAuth->getdb('ARFINC')}
					set SMPAY=SMPAY+@PAYAMT
						,LPAYD='".$req["TMBILDT"]."'
						,PAYDWN=PAYDWN+".str_replace(",","",$datatable["opt_payamt"])."
					where CONTNO=@CONTNO
				end;
				
				ENABLE TRIGGER AFTINS_PAY003 ON {$this->MAuth->getdb('CHQTRAN')};
			";
		}else if($event == "cancel"){
			$CHQTRANQ_003 = "
				DISABLE TRIGGER AFTINS_PAY003 ON {$this->MAuth->getdb('CHQTRAN')};
								
				if exists (select * from {$this->MAuth->getdb('ARFINC')} where CONTNO='{$datatable["opt_contno"]}')
				begin
					if('".$req["PAYTYP"]."' = '02')
					begin
						update {$this->MAuth->getdb('ARFINC')}
						set SMCHQ=SMCHQ - ".str_replace(",","",$datatable["opt_payamt"])."
						where CONTNO='{$datatable["opt_contno"]}'
					end
					else
					begin
						update {$this->MAuth->getdb('ARFINC')}
						set SMPAY=SMPAY - ".str_replace(",","",$datatable["opt_payamt"])."
						where CONTNO='{$datatable["opt_contno"]}'
					end
				end
				
				update {$this->MAuth->getdb('CHQTRAN')}
				SET FLAG='C'
					,CANDT=getdate()
					,CANID='".$this->sess["USERID"]."'
					,CANTIME=getdate()
				where 1=1 and TMBILL='{$req["TMBILL"]}' and LOCATRECV='{$req["LOCATRECV"]}';  
				
				ENABLE TRIGGER AFTINS_PAY003 ON {$this->MAuth->getdb('CHQTRAN')};
			";
		}
		
		return $CHQTRANQ_003;
	}
	
	function query004($req,$datatable,$event = "save"){
		$CHQTRANQ_004 = "";
		if($event == "save"){
			$CHQTRANQ_004 = "
				set @CONTNO = '".$datatable["opt_contno"]."';
				
				DISABLE TRIGGER AFTINS_PAY004 ON {$this->MAuth->getdb('CHQTRAN')};
				
				set @VATRT = (select VATRT from {$this->MAuth->getdb('ARFINC')} where CONTNO=@CONTNO);
				insert into {$this->MAuth->getdb('CHQTRAN')} (
					TMBILL, LOCATRECV, TMBILDT, CHQNO, CHQDT, 
					TSALE, PAYFOR, CONTNO, LOCATPAY, CUSCOD, 
					PAYTYP, TAXRT, PAYAMT, PAYAMT_N, PAYAMT_V, 
					DISCT, PAYINT, DSCINT, NETPAY, PAYDT, 
					NOPAY, F_PAR, F_PAY, L_PAR, L_PAY, 
					TAXNO, TAXFL, INPDT, FLAG, YFLAG, 
					USERID, DOSBILL, BALANCE, INPTIME, CANID, SYSTVER
				) values (
					@TMBILL,'".$req["LOCATRECV"]."','".$req["TMBILDT"]."','".$req["CHQNO"]."',".($req["CHQDT"] == "" ? "null":"'".$req["CHQDT"]."'").",
					'F','".$datatable["opt_payfor"]."','".$datatable["opt_contno"]."',
					(select LOCAT from {$this->MAuth->getdb('ARFINC')} where CONTNO=@CONTNO),'".$req["CUSCOD"]."',
					'".$req["PAYTYP"]."',@VATRT,
					
					".str_replace(",","",$datatable["opt_payamt"]).",				
					cast(".str_replace(",","",$datatable["opt_payamt"])." / ((100+@VATRT)/100) as decimal(18,2)),
					".str_replace(",","",$datatable["opt_payamt"])." - (cast(".str_replace(",","",$datatable["opt_payamt"])." / ((100+@VATRT)/100) as decimal(18,2))),
					
					
					'".str_replace(",","",$datatable["opt_disct"])."','".str_replace(",","",$datatable["opt_payint"])."','".str_replace(",","",$datatable["opt_dscint"])."','".str_replace(",","",$datatable["opt_netpay"])."','".$req["TMBILDT"]."',
					0,'',0,'',0,
					'','N',getdate(),'H','',
					'".$this->sess["USERID"]."','',0,getdate(),'','YTKMini'
				);
				
				if('".$req["PAYTYP"]."'='02')
				begin
					update {$this->MAuth->getdb('ARFINC')}
					set SMCHQ=SMCHQ+@PAYAMT
						,LPAYD='".$req["TMBILDT"]."'
					where CONTNO=@CONTNO
				end
				else
				begin
					update {$this->MAuth->getdb('ARFINC')}
					set SMPAY=SMPAY+@PAYAMT
						,LPAYD='".$req["TMBILDT"]."'
						,PAYFIN=PAYFIN+".str_replace(",","",$datatable["opt_payamt"])."
					where CONTNO=@CONTNO
				end;
				
				ENABLE TRIGGER AFTINS_PAY004 ON {$this->MAuth->getdb('CHQTRAN')};				
			";
		}else if($event == "cancel"){
			$CHQTRANQ_004 = "
				DISABLE TRIGGER AFTINS_PAY004 ON {$this->MAuth->getdb('CHQTRAN')};
								
				if exists (select * from {$this->MAuth->getdb('ARFINC')} where CONTNO='{$datatable["opt_contno"]}')
				begin
					if('".$req["PAYTYP"]."' = '02')
					begin
						update {$this->MAuth->getdb('ARFINC')}
						set SMCHQ=SMCHQ - ".str_replace(",","",$datatable["opt_payamt"])."
						where CONTNO='{$datatable["opt_contno"]}'
					end
					else
					begin
						update {$this->MAuth->getdb('ARFINC')}
						set SMPAY=SMPAY - ".str_replace(",","",$datatable["opt_payamt"])."
							,PAYFIN=PAYFIN - ".str_replace(",","",$datatable["opt_payamt"])."
						where CONTNO='{$datatable["opt_contno"]}'
					end
				end
				
				update {$this->MAuth->getdb('CHQTRAN')}
				SET FLAG='C'
					,CANDT=getdate()
					,CANID='".$this->sess["USERID"]."'
					,CANTIME=getdate()
				where 1=1 and TMBILL='{$req["TMBILL"]}' and LOCATRECV='{$req["LOCATRECV"]}';  
				
				ENABLE TRIGGER AFTINS_PAY004 ON {$this->MAuth->getdb('CHQTRAN')};
			";
		}
		return $CHQTRANQ_004;
	}
	
	function query005($req,$datatable,$event = "save"){
		$CHQTRANQ_005 = "";
		if($event == "save"){
			$CHQTRANQ_005 = "
				set @CONTNO = '".$datatable["opt_contno"]."';
				
				DISABLE TRIGGER AFTINS_PAY005 ON {$this->MAuth->getdb('CHQTRAN')};
				
				set @VATRT = (select VATRT from {$this->MAuth->getdb('ARFINC')} where CONTNO=@CONTNO);
				insert into {$this->MAuth->getdb('CHQTRAN')} (
					TMBILL, LOCATRECV, TMBILDT, CHQNO, CHQDT, 
					TSALE, PAYFOR, CONTNO, LOCATPAY, CUSCOD, 
					PAYTYP, TAXRT, PAYAMT, PAYAMT_N, PAYAMT_V, 
					DISCT, PAYINT, DSCINT, NETPAY, PAYDT, 
					NOPAY, F_PAR, F_PAY, L_PAR, L_PAY, 
					TAXNO, TAXFL, INPDT, FLAG, YFLAG, 
					USERID, DOSBILL, BALANCE, INPTIME, CANID, SYSTVER
				) values (
					@TMBILL,'".$req["LOCATRECV"]."','".$req["TMBILDT"]."','".$req["CHQNO"]."',".($req["CHQDT"] == "" ? "null":"'".$req["CHQDT"]."'").",
					'R','".$datatable["opt_payfor"]."','".$datatable["opt_contno"]."','".$req["LOCATRECV"]."','".$req["CUSCOD"]."',
					'".$req["PAYTYP"]."',0,'".str_replace(",","",$datatable["opt_payamt"])."','".str_replace(",","",$datatable["opt_payamt"])."',0,
					'".str_replace(",","",$datatable["opt_disct"])."','".str_replace(",","",$datatable["opt_payint"])."','".str_replace(",","",$datatable["opt_dscint"])."','".str_replace(",","",$datatable["opt_netpay"])."','".$req["TMBILDT"]."',
					0,'',0,'',0,
					'','N',getdate(),'H','',
					'".$this->sess["USERID"]."','',0,getdate(),'','YTKMini'
				);
				
				if('".$req["PAYTYP"]."'='02')
				begin
					update {$this->MAuth->getdb('AROPTMST')}
					set SMCHQ=SMCHQ+@PAYAMT
						,LPAYDT=@TMBILDT
					where CONTNO=@CONTNO
				end
				else
				begin
					update {$this->MAuth->getdb('AROPTMST')}
					set SMPAY=SMPAY+@PAYAMT
						,LPAYDT=@TMBILDT
					where CONTNO=@CONTNO
				end;
				
				ENABLE TRIGGER AFTINS_PAY005 ON {$this->MAuth->getdb('CHQTRAN')};				
			";
		}else if($event == "cancel"){
			$CHQTRANQ_005 = "
				DISABLE TRIGGER AFTINS_PAY005 ON {$this->MAuth->getdb('CHQTRAN')};
								
				if exists (select * from {$this->MAuth->getdb('AROPTMST')} where CONTNO='{$datatable["opt_contno"]}')
				begin
					if('".$req["PAYTYP"]."' = '02')
					begin
						update {$this->MAuth->getdb('AROPTMST')}
						set SMCHQ=SMCHQ - ".str_replace(",","",$datatable["opt_payamt"])."
						where CONTNO='{$datatable["opt_contno"]}'
					end
					else
					begin
						update {$this->MAuth->getdb('AROPTMST')}
						set SMPAY=SMPAY - ".str_replace(",","",$datatable["opt_payamt"])."
							,PAYFIN=PAYFIN - ".str_replace(",","",$datatable["opt_payamt"])."
						where CONTNO='{$datatable["opt_contno"]}'
					end
				end
				
				update {$this->MAuth->getdb('CHQTRAN')}
				SET FLAG='C'
					,CANDT=getdate()
					,CANID='".$this->sess["USERID"]."'
					,CANTIME=getdate()
				where 1=1 and TMBILL='{$req["TMBILL"]}' and LOCATRECV='{$req["LOCATRECV"]}';  
				
				ENABLE TRIGGER AFTINS_PAY005 ON {$this->MAuth->getdb('CHQTRAN')};
			";
		}	
		
		return $CHQTRANQ_005;
	}
	
	function query006($req,$datatable,$event = "save"){
		$CHQTRANQ_006 = "";
		if($event == "save"){
			$CHQTRANQ_006 = "
				set @PAYFOR = '".$datatable["opt_payfor"]."';
				set @CONTNO = '".$datatable["opt_contno"]."';
				set @PAYAMT = ".str_replace(",","",$datatable["opt_payamt"]).";
				set @PAYAMTCAL = @PAYAMT;
				set @DISCT  = ".str_replace(",","",$datatable["opt_disct"]).";
				set @PAYINT = ".str_replace(",","",$datatable["opt_payint"]).";
				set @DSCINT = ".str_replace(",","",$datatable["opt_dscint"]).";
				set @NETPAY = ".str_replace(",","",$datatable["opt_netpay"]).";
				set @TSALE  = (select TSALE from {$this->MAuth->getdb('ARMAST')} where CONTNO=@CONTNO);
				set @VATRT  = (select VATRT from {$this->MAuth->getdb('ARMAST')} where CONTNO=@CONTNO);
				set @TAXRT  = @VATRT;
				set @LOCATPAY = (select LOCAT from {$this->MAuth->getdb('ARMAST')} where CONTNO=@CONTNO); 
				set @FNOPAY = (
						select top 1 NOPAY from {$this->MAuth->getdb('ARPAY')}
						where CONTNO=@CONTNO and DAMT != PAYMENT
						order by NOPAY
				);
				
				if exists(select * from {$this->MAuth->getdb('ARMAST')} where CONTNO=@CONTNO and LPAYD > @TMBILDT)
				begin 
					rollback tran transaction1;
					insert into #paymentTemp select 'E' as id,@TMBILL,'วันที่รับชำระเงินน้อยกว่าวันที่รับชำระล่าสุด<br>รับชำระเงินล่าสุดวันที่ '  
						+(select convert(varchar(6),LPAYD,103)+cast(year(LPAYD)+543 as varchar(4)) from {$this->MAuth->getdb('ARMAST')} where CONTNO=@CONTNO and LPAYD > @TMBILDT) as msg;
					return;
				end 
				
				if exists(
					select * from (
						select case when sum(DAMT - PAYMENT) > @PAYAMT then 1 else 0 end 
						from {$this->MAuth->getdb('ARPAY')} 
						where CONTNO=@CONTNO
					} as data
				)
				begin 
					rollback tran transaction1;
					insert into #paymentTemp select 'E' as id,@TMBILL,'วันที่รับชำระเงินน้อยกว่าวันที่รับชำระล่าสุด<br>รับชำระเงินล่าสุดวันที่ '  
						+(select convert(varchar(6),LPAYD,103)+cast(year(LPAYD)+543 as varchar(4)) from {$this->MAuth->getdb('ARMAST')} where CONTNO=@CONTNO and LPAYD > @TMBILDT) as msg;
					return;
				end 
				
				declare cs cursor for
					select NOPAY,DAMT-PAYMENT as BFPAY from {$this->MAuth->getdb('ARPAY')}
					where CONTNO=@CONTNO and DAMT != PAYMENT
					order by NOPAY

				open cs;
				fetch next from cs into @NOPAY,@BFPAY;

				while @@FETCH_STATUS = 0
				begin 
					if @PAYAMTCAL = 0 
					begin
						/* close cs; deallocate cs; return; */
						set @LNOPAY = @LNOPAY;
					end 
					else if @PAYAMTCAL > 0 and @BFPAY <= @PAYAMTCAL
					begin 
						-- select @NOPAY,@BFPAY,@BFPAY
						update {$this->MAuth->getdb('ARPAY')}
						set DATE1 		= @TMBILDT
							,PAYMENT 	= (PAYMENT + @BFPAY)
							,V_PAYMENT 	= (case when @VATRT=0 then 0 else (PAYMENT + @BFPAY) - ((PAYMENT + @BFPAY) / ((100+@VATRT) / 100.0)) end)
							,N_PAYMENT 	= (case when @VATRT=0 then (PAYMENT + @BFPAY) else ((PAYMENT + @BFPAY) / ((100+@VATRT) / 100.0)) end)
							,DELAY 		= case when datediff(day,DDATE,@TMBILDT) < 0 then 0 else datediff(day,DDATE,@TMBILDT) end
							,ADVDUE 	= case when datediff(day,DDATE,@TMBILDT) >= 0 then 0 else datediff(day,DDATE,@TMBILDT) * -1 end
						where CONTNO=@CONTNO and NOPAY=@NOPAY
						
						set @LNOPAY = @NOPAY;
						set @PAYAMTCAL = @PAYAMTCAL - @BFPAY;
					end
					else
					begin 
						-- select @NOPAY,@BFPAY,@BFPAY - (@BFPAY - @PAYAMTCAL)
						update {$this->MAuth->getdb('ARPAY')}
						set DATE1 		= @TMBILDT
							,PAYMENT 	= PAYMENT + (@BFPAY - (@BFPAY - @PAYAMTCAL))
							,V_PAYMENT 	= (case when @VATRT=0 then 0 else (PAYMENT + (@BFPAY - (@BFPAY - @PAYAMTCAL))) - ((PAYMENT + (@BFPAY - (@BFPAY - @PAYAMTCAL))) / ((100+@VATRT) / 100.0)) end)
							,N_PAYMENT 	= (case when @VATRT=0 then (PAYMENT + (@BFPAY - (@BFPAY - @PAYAMTCAL))) else ((PAYMENT + (@BFPAY - (@BFPAY - @PAYAMTCAL))) / ((100+@VATRT) / 100.0)) end)
							,DELAY 		= case when datediff(day,DDATE,@TMBILDT) < 0 then 0 else datediff(day,DDATE,@TMBILDT) end
							,ADVDUE 	= case when datediff(day,DDATE,@TMBILDT) >= 0 then 0 else datediff(day,DDATE,@TMBILDT) * -1 end
						where CONTNO=@CONTNO and NOPAY=@NOPAY
						
						set @LNOPAY = @NOPAY;
						set @PAYAMTCAL = 0;
					end
					
					fetch next from cs into @NOPAY,@BFPAY;
				end
				close cs;
				deallocate cs;

			
				DISABLE TRIGGER AFTINS_PAY006 ON {$this->MAuth->getdb('CHQTRAN')};
				set @PAYAMT_N = case when @VATRT=0 then @PAYAMT else (@PAYAMT / ((100+@VATRT) / 100.0)) end;
				set @PAYAMT_V = case when @VATRT=0 then 0 else (@PAYAMT - (@PAYAMT / ((100+@VATRT) / 100.0))) end;
				insert into {$this->MAuth->getdb('CHQTRAN')} (
					TMBILL, LOCATRECV, TMBILDT, CHQNO, CHQDT, 
					TSALE, PAYFOR, CONTNO, LOCATPAY, CUSCOD, 
					PAYTYP, TAXRT, PAYAMT, PAYAMT_N, PAYAMT_V, 
					DISCT, PAYINT, DSCINT, NETPAY, PAYDT, 
					NOPAY, F_PAR, F_PAY, L_PAR, L_PAY, 
					--TAXNO, TAXFL, INPDT, FLAG, YFLAG, 
					INPDT, FLAG, YFLAG, 
					USERID, DOSBILL, BALANCE, INPTIME, CANID, SYSTVER
				) values (
					@TMBILL,@LOCATRECV,@TMBILDT,@CHQNO,@CHQDT,
					@TSALE,@PAYFOR,@CONTNO,@LOCATPAY,@CUSCOD,
					@PAYTYP,@TAXRT,@PAYAMT,@PAYAMT_N,@PAYAMT_V,
					@DISCT,@PAYINT,@DSCINT,@NETPAY,@PAYDT,
					isnull((select max(NOPAY) from {$this->MAuth->getdb('CHQTRAN')} where CONTNO=@CONTNO and PAYFOR='006' and FLAG!='C'),0)+1,
					'*',@FNOPAY,'*',isnull(@LNOPAY,@FNOPAY),
					--'','N',@INPDT,@FLAG,@YFLAG,
					@INPDT,@FLAG,@YFLAG,
					@USERID,@DOSBILL,
						isnull((
							select SUM(DAMT-PAYMENT) from {$this->MAuth->getdb('ARPAY')}
							where CONTNO=@CONTNO AND LOCAT=@LOCATPAY AND DDATE <= @TMBILDT
						 ),0)
					,@INPTIME,@CANID,'YTKMini'
				);
				
				if(@PAYTYP='02')
				begin
					update {$this->MAuth->getdb('ARMAST')}
					set SMCHQ=SMCHQ+@PAYAMT
						,LPAYD=@TMBILDT
						,LPAYA=@PAYAMT
					where CONTNO=@CONTNO
				end
				else
				begin
					update {$this->MAuth->getdb('ARMAST')}
					set SMPAY=SMPAY+@PAYAMT
						,LPAYD=@TMBILDT
						,LPAYA=@PAYAMT
					where CONTNO=@CONTNO
				end;
				
				update {$this->MAuth->getdb('INVTRAN')}
				set FLAG='C'
					,CURSTAT='' 
				where STRNO IN (
					select STRNO from {$this->MAuth->getdb('ARMAST')}
					where CONTNO=@CONTNO AND LOCAT=@LOCATPAY
				);
				
				update a
				set a.YSTAT='N'
					,a.YDATE=NULL 
					,a.EXP_AMT=b.EXP_AMT
					,a.EXP_PRD=b.EXP_PRD
					,a.EXP_FRM=b.EXP_FRM
					,a.EXP_TO=b.EXP_TO
				--select * 
				from {$this->MAuth->getdb('ARMAST')} a
				left join (
					select CONTNO,COUNT(*) as EXP_PRD
						,SUM(isnull(DAMT,0)-isnull(PAYMENT,0)) as EXP_AMT
						,MIN(NOPAY) as EXP_FRM
						,MAX(NOPAY) as EXP_TO
					from {$this->MAuth->getdb('ARPAY')}
					where 1=1 and DAMT>PAYMENT and DDATE <= @TMBILDT
					group by CONTNO
				) as b on a.CONTNO=b.CONTNO
				where a.CONTNO=@CONTNO
				
				ENABLE TRIGGER AFTINS_PAY006 ON {$this->MAuth->getdb('CHQTRAN')};
			";
		}else if($event == "cancel"){
			$CHQTRANQ_006 = "
				-- เช็คว่ายกเลิกบิล จากงวดสุดท้ายหรือไม่
				if (
					select max(r) from (
						select row_number() over(order by TMBILDT) r,* from {$this->MAuth->getdb('CHQTRAN')}
						where CONTNO='{$datatable["opt_contno"]}' and FLAG!='C' and PAYFOR in ('006','007')
					) as data
				) != (
					select r from (
						select row_number() over(order by TMBILDT) r,* from {$this->MAuth->getdb('CHQTRAN')}
						where CONTNO='{$datatable["opt_contno"]}' and FLAG!='C' and PAYFOR in ('006','007')
					) as data
					where TMBILL='{$req["TMBILL"]}'
				)
				begin 
					rollback tran transaction1;
					insert into #CanCPaymentTemp select 'E' as id,'{$req["TMBILL"]}','การยกเลิกรับชำระ ต้องยกเลิกจากการรับชำระล่าสุด จะยกเลิกการรับชำระกลางงวดไม่ได้' as msg;
					return;
				end;
			
				DISABLE TRIGGER AFTINS_PAY006 ON {$this->MAuth->getdb('CHQTRAN')};
								
				if exists (select * from {$this->MAuth->getdb('ARMAST')} where CONTNO='{$datatable["opt_contno"]}')
				begin
					if('".$req["PAYTYP"]."' = '02')
					begin
						update {$this->MAuth->getdb('ARMAST')}
						set SMCHQ=SMCHQ - ".str_replace(",","",$datatable["opt_payamt"])."
						where CONTNO='{$datatable["opt_contno"]}'
					end
					else
					begin
						update {$this->MAuth->getdb('ARMAST')}
						set SMPAY=SMPAY - ".str_replace(",","",$datatable["opt_payamt"])."
						where CONTNO='{$datatable["opt_contno"]}'
					end
					
					update {$this->MAuth->getdb('INVTRAN')}
					set FLAG='D'
						,CURSTAT='Y'    
					where STRNO IN (
						select STRNO from {$this->MAuth->getdb('ARMAST')} a
						left join {$this->MAuth->getdb('CHQTRAN')} b on a.CONTNO=b.CONTNO and a.LOCAT=b.LOCATPAY 
						where b.YFLAG='Y' and a.CONTNO='{$datatable["opt_contno"]}'
					)
					
					update a
					set a.YSTAT='Y'
					from {$this->MAuth->getdb('ARMAST')} a
					left join {$this->MAuth->getdb('CHQTRAN')} b on a.CONTNO=b.CONTNO and a.LOCAT=b.LOCATPAY 
					where a.CONTNO='{$datatable["opt_contno"]}' and b.YFLAG='Y'
				end
				
				update {$this->MAuth->getdb('CHQTRAN')}
				SET FLAG='C'
					,CANDT=getdate()
					,CANID='".$this->sess["USERID"]."'
					,CANTIME=getdate()
				where 1=1 and TMBILL='{$req["TMBILL"]}' and LOCATRECV='{$req["LOCATRECV"]}';  
				
				update a
				set a.YSTAT='N'
					,a.YDATE=NULL 
					,a.EXP_AMT=b.EXP_AMT
					,a.EXP_PRD=b.EXP_PRD
					,a.EXP_FRM=b.EXP_FRM
					,a.EXP_TO=b.EXP_TO
				--select * 
				from {$this->MAuth->getdb('ARMAST')} a
				left join (
					select CONTNO,COUNT(*) as EXP_PRD
						,SUM(isnull(DAMT,0)-isnull(PAYMENT,0)) as EXP_AMT
						,MIN(NOPAY) as EXP_FRM
						,MAX(NOPAY) as EXP_TO
					from {$this->MAuth->getdb('ARPAY')}
					where 1=1 and DAMT>PAYMENT and DDATE <= '{$req["TMBILDT"]}'
					group by CONTNO
				) as b on a.CONTNO=b.CONTNO
				where a.CONTNO='{$datatable["opt_contno"]}';
				
				ENABLE TRIGGER AFTINS_PAY006 ON {$this->MAuth->getdb('CHQTRAN')};
			";
		}
		return $CHQTRANQ_006;
	}
	
	function query007($req,$datatable,$event = "save"){
		$CHQTRANQ_007 = "";
		if($event == "save"){
			$CHQTRANQ_007 = "
				set @PAYFOR = '".$datatable["opt_payfor"]."';
				set @CONTNO = '".$datatable["opt_contno"]."';
				set @PAYAMT = ".str_replace(",","",$datatable["opt_payamt"]).";
				set @PAYAMTCAL = @PAYAMT;
				set @DISCT  = ".str_replace(",","",$datatable["opt_disct"]).";
				set @PAYINT = ".str_replace(",","",$datatable["opt_payint"]).";
				set @DSCINT = ".str_replace(",","",$datatable["opt_dscint"]).";
				set @NETPAY = ".str_replace(",","",$datatable["opt_netpay"]).";
				set @TSALE  = (select TSALE from {$this->MAuth->getdb('ARMAST')} where CONTNO=@CONTNO);
				set @VATRT  = (select VATRT from {$this->MAuth->getdb('ARMAST')} where CONTNO=@CONTNO);
				set @TAXRT  = @VATRT;
				set @LOCATPAY = (select LOCAT from {$this->MAuth->getdb('ARMAST')} where CONTNO=@CONTNO); 
				set @FNOPAY = (
						select top 1 NOPAY from {$this->MAuth->getdb('ARPAY')}
						where CONTNO=@CONTNO and DAMT != PAYMENT
						order by NOPAY
				);
				
				if exists(select * from {$this->MAuth->getdb('ARMAST')} where CONTNO=@CONTNO and LPAYD > @TMBILDT)
				begin 
					rollback tran transaction1;
					insert into #paymentTemp select 'E' as id,@TMBILL,'วันที่รับชำระเงินน้อยกว่าวันที่รับชำระล่าสุด<br>รับชำระเงินล่าสุดวันที่ '  
						+(select convert(varchar(6),LPAYD,103)+cast(year(LPAYD)+543 as varchar(4)) from {$this->MAuth->getdb('ARMAST')} where CONTNO=@CONTNO and LPAYD > @TMBILDT) as msg;
					return;
				end 
				
				declare cs cursor for
					select NOPAY,DAMT-PAYMENT as BFPAY from {$this->MAuth->getdb('ARPAY')}
					where CONTNO=@CONTNO and DAMT != PAYMENT
					order by NOPAY

				open cs;
				fetch next from cs into @NOPAY,@BFPAY;

				while @@FETCH_STATUS = 0
				begin 
					if @PAYAMTCAL = 0 
					begin
						/* close cs; deallocate cs; return; */
						set @LNOPAY = @LNOPAY;
					end 
					else if @PAYAMTCAL > 0 and @BFPAY <= @PAYAMTCAL
					begin 
						-- select @NOPAY,@BFPAY,@BFPAY
						update {$this->MAuth->getdb('ARPAY')}
						set DATE1 		= @TMBILDT
							,PAYMENT 	= (PAYMENT + @BFPAY)
							,V_PAYMENT 	= (case when @VATRT=0 then 0 else (PAYMENT + @BFPAY) - ((PAYMENT + @BFPAY) / ((100+@VATRT) / 100.0)) end)
							,N_PAYMENT 	= (case when @VATRT=0 then (PAYMENT + @BFPAY) else ((PAYMENT + @BFPAY) / ((100+@VATRT) / 100.0)) end)
							--,DELAY 	= @DELAY
							--,ADVDUE 	= @ADVDUE
							,TAXPAY		= (PAYMENT + @BFPAY)
							,TAXAMT 	= (case when @VATRT=0 then 0 when @TAXNO='' then 0
											else (PAYMENT + @BFPAY) - ((PAYMENT + @BFPAY) / ((100+@VATRT) / 100.0)) end)
						where CONTNO=@CONTNO and NOPAY=@NOPAY
						
						set @LNOPAY = @NOPAY;
						set @PAYAMTCAL = @PAYAMTCAL - @BFPAY;
					end
					else
					begin 
						-- select @NOPAY,@BFPAY,@BFPAY - (@BFPAY - @PAYAMTCAL)
						update {$this->MAuth->getdb('ARPAY')}
						set DATE1 		= @TMBILDT
							,PAYMENT 	= PAYMENT + (@BFPAY - (@BFPAY - @PAYAMTCAL))
							,V_PAYMENT 	= (case when @VATRT=0 then 0 else (PAYMENT + (@BFPAY - (@BFPAY - @PAYAMTCAL))) - ((PAYMENT + (@BFPAY - (@BFPAY - @PAYAMTCAL))) / ((100+@VATRT) / 100.0)) end)
							,N_PAYMENT 	= (case when @VATRT=0 then (PAYMENT + (@BFPAY - (@BFPAY - @PAYAMTCAL))) else ((PAYMENT + (@BFPAY - (@BFPAY - @PAYAMTCAL))) / ((100+@VATRT) / 100.0)) end)
							--,DELAY 	= @DELAY
							--,ADVDUE 	= @ADVDUE
							,TAXPAY		= (PAYMENT + @BFPAY)
							,TAXAMT 	= (case when @VATRT=0 then 0 when @TAXNO='' then 0
											else (PAYMENT + (@BFPAY - (@BFPAY - @PAYAMTCAL))) - ((PAYMENT + (@BFPAY - (@BFPAY - @PAYAMTCAL))) / ((100+@VATRT) / 100.0)) end)
						where CONTNO=@CONTNO and NOPAY=@NOPAY
						
						set @LNOPAY = @NOPAY;
						set @PAYAMTCAL = 0;
					end
					
					fetch next from cs into @NOPAY,@BFPAY;
				end
				close cs;
				deallocate cs;
				
				DISABLE TRIGGER AFTINS_PAY007 ON {$this->MAuth->getdb('CHQTRAN')};
				set @PAYAMT_N = case when @VATRT=0 then @PAYAMT else (@PAYAMT / ((100+@VATRT) / 100.0)) end;
				set @PAYAMT_V = case when @VATRT=0 then 0 else (@PAYAMT - (@PAYAMT / ((100+@VATRT) / 100.0))) end;
				insert into {$this->MAuth->getdb('CHQTRAN')} (
					TMBILL, LOCATRECV, TMBILDT, CHQNO, CHQDT, 
					TSALE, PAYFOR, CONTNO, LOCATPAY, CUSCOD, 
					PAYTYP, TAXRT, PAYAMT, PAYAMT_N, PAYAMT_V, 
					DISCT, PAYINT, DSCINT, NETPAY, PAYDT, 
					NOPAY, F_PAR, F_PAY, L_PAR, L_PAY, 
					TAXNO, TAXFL, INPDT, FLAG, YFLAG, 
					USERID, DOSBILL, BALANCE, INPTIME, CANID, SYSTVER
				) values (
					@TMBILL,@LOCATRECV,@TMBILDT,@CHQNO,@CHQDT,
					@TSALE,@PAYFOR,@CONTNO,@LOCATPAY,@CUSCOD,
					@PAYTYP,@TAXRT,@PAYAMT,@PAYAMT_N,@PAYAMT_V,
					@DISCT,@PAYINT,@DSCINT,@NETPAY,@PAYDT,
					isnull((select max(NOPAY) from {$this->MAuth->getdb('CHQTRAN')} where CONTNO=@CONTNO and PAYFOR='006' and FLAG!='C'),0)+1,
					'*',@FNOPAY,'*',isnull(@LNOPAY,@FNOPAY),
					'','N',@INPDT,@FLAG,@YFLAG,
					@USERID,@DOSBILL,
						isnull((
							select SUM(DAMT-PAYMENT) from {$this->MAuth->getdb('ARPAY')}
							where CONTNO=@CONTNO AND LOCAT=@LOCATPAY AND DDATE <= @TMBILDT
						 ),0)
					,@INPTIME,@CANID,'YTKMini'
				);
										
				if('".$req["PAYTYP"]."'='02')
				begin
					update {$this->MAuth->getdb('ARMAST')}
					set SMCHQ=SMCHQ+@PAYAMT
						,LPAYD=@TMBILDT
						,LPAYA=@PAYAMT
					where CONTNO=@CONTNO
				end
				else
				begin
					update {$this->MAuth->getdb('ARMAST')}
					set SMPAY=SMPAY+@PAYAMT
						,LPAYD=@TMBILDT
						,LPAYA=@PAYAMT
					where CONTNO=@CONTNO
				end;
				
				UPDATE {$this->MAuth->getdb('ARMAST')}
				SET YSTAT='N',YDATE=NULL 
				where CONTNO=@CONTNO AND LOCAT=@LOCATPAY
				
				
				if (@VATRT > 0)
				begin
					declare @TOTAMT007 decimal(18,0) = (
						select sum(DAMT) from {$this->MAuth->getdb('ARPAY')} 
						where CONTNO=@CONTNO and LOCAT=@LOCATPAY and isnull(TAXINV,'')=''
					);
					
					insert into {$this->MAuth->getdb('TAXTRAN')} (
						LOCAT ,TAXNO ,TAXDT ,TSALE ,CONTNO ,CUSCOD ,SNAM ,NAME1 ,NAME2 ,STRNO ,VATRT 
						,NETAMT ,VATAMT ,TOTAMT ,DESCP ,FPAY ,LPAY ,INPDT ,TAXTYP ,TAXFLG ,USERID ,TMBILL 
					) values (
						@LOCATRECV,@TAXNO,@TAXDT,@TSALE,@CONTNO,@CUSCOD
						,(select SNAM from {$this->MAuth->getdb('CUSTMAST')} where CUSCOD=@CUSCOD)
						,(select NAME1 from {$this->MAuth->getdb('CUSTMAST')} where CUSCOD=@CUSCOD)
						,(select NAME2 from {$this->MAuth->getdb('CUSTMAST')} where CUSCOD=@CUSCOD)
						,(select STRNO from {$this->MAuth->getdb('ARMAST')} where CONTNO=@CONTNO and LOCAT=@LOCATPAY)
						,@VATRT
						
						,(case when @VATRT=0 then @TOTAMT007 else (@TOTAMT007 / ((100+@VATRT) / 100.0)) end)
						,(case when @VATRT=0 then 0 else (@TOTAMT007 - (@TOTAMT007 / ((100+@VATRT) / 100.0))) end)
						,@TOTAMT007,'รับชำระค่างวดตัดสด'
						,(select MIN(NOPAY) from {$this->MAuth->getdb('ARPAY')} 
							where CONTNO=@CONTNO and LOCAT=@LOCATPAY and isnull(TAXINV,'')='')
						,(select MAX(NOPAY) from {$this->MAuth->getdb('ARPAY')}
							where CONTNO=@CONTNO and LOCAT=@LOCATPAY and isnull(TAXINV,'')='')
						,@INPDT,'B','N',@USERID,@TMBILL
					);
					
					update {$this->MAuth->getdb('ARPAY')} 
					set TAXINV=@TAXNO
						,TAXDT=@TAXDT
						,TAXAMT= (DAMT / ((100+VATRT) / 100.0))
					where CONTNO=@CONTNO and LOCAT=@LOCATPAY and isnull(TAXINV,'')=''
					
					update {$this->MAuth->getdb('CHQTRAN')} 
					set TAXNO=@TAXNO
					where TMBILL=@TMBILL
				end;
				
				update a
				set a.YSTAT='N'
					,a.YDATE=NULL 
					,a.EXP_AMT=b.EXP_AMT
					,a.EXP_PRD=b.EXP_PRD
					,a.EXP_FRM=b.EXP_FRM
					,a.EXP_TO=b.EXP_TO
				--select * 
				from {$this->MAuth->getdb('ARMAST')} a
				left join (
					select CONTNO,COUNT(*) as EXP_PRD
						,SUM(isnull(DAMT,0)-isnull(PAYMENT,0)) as EXP_AMT
						,MIN(NOPAY) as EXP_FRM
						,MAX(NOPAY) as EXP_TO
					from {$this->MAuth->getdb('ARPAY')}
					where 1=1 and DAMT>PAYMENT and DDATE <= @TMBILDT
					group by CONTNO
				) as b on a.CONTNO=b.CONTNO
				where a.CONTNO=@CONTNO;
				
				ENABLE TRIGGER AFTINS_PAY007 ON {$this->MAuth->getdb('CHQTRAN')};				
			";
		}else if($event == "cancel"){
			$CHQTRANQ_007 = "
				-- เช็คว่ายกเลิกบิล จากงวดสุดท้ายหรือไม่
				if (
					select max(r) from (
						select row_number() over(order by TMBILDT) r,* from {$this->MAuth->getdb('CHQTRAN')}
						where CONTNO='{$datatable["opt_contno"]}' and FLAG!='C' and PAYFOR in ('006','007')
					) as data
				) != (
					select r from (
						select row_number() over(order by TMBILDT) r,* from {$this->MAuth->getdb('CHQTRAN')}
						where CONTNO='{$datatable["opt_contno"]}' and FLAG!='C'	and PAYFOR in ('006','007')	
					) as data
					where TMBILL='{$req["TMBILL"]}'
				)
				begin 
					rollback tran transaction1;
					insert into #CanCPaymentTemp select 'E' as id,'{$req["TMBILL"]}','การยกเลิกรับชำระ ต้องยกเลิกจากการรับชำระล่าสุด จะยกเลิกการรับชำระกลางงวดไม่ได้' as msg;
					return;
				end;
				
				DISABLE TRIGGER AFTINS_PAY007 ON {$this->MAuth->getdb('CHQTRAN')};
								
				if exists (select * from {$this->MAuth->getdb('ARMAST')} where CONTNO='{$datatable["opt_contno"]}')
				begin
					if('".$req["PAYTYP"]."' = '02')
					begin
						update {$this->MAuth->getdb('ARMAST')}
						set SMCHQ=SMCHQ - ".str_replace(",","",$datatable["opt_payamt"])."
						where CONTNO='{$datatable["opt_contno"]}'
					end
					else
					begin
						update {$this->MAuth->getdb('ARMAST')}
						set SMPAY=SMPAY - ".str_replace(",","",$datatable["opt_payamt"])."
						where CONTNO='{$datatable["opt_contno"]}'
					end
				end
				
				update {$this->MAuth->getdb('CHQTRAN')}
				SET FLAG='C'
					,CANDT=getdate()
					,CANID='".$this->sess["USERID"]."'
					,CANTIME=getdate()
				where 1=1 and TMBILL='{$req["TMBILL"]}' and LOCATRECV='{$req["LOCATRECV"]}';  
				
				ENABLE TRIGGER AFTINS_PAY007 ON {$this->MAuth->getdb('CHQTRAN')};
			";
		}
		
		return $CHQTRANQ_007;
	}
	
	function query008($req,$datatable,$event = "save"){
		$CHQTRANQ_008 = "";
		if($event == "save"){
			$opt_payamt = str_replace(",","",$datatable["opt_payamt"]);
		
			$CHQTRANQ_008 = "
				set @PAYFOR = '".$datatable["opt_payfor"]."';
				set @CONTNO = '".$datatable["opt_contno"]."';
				set @PAYAMT = ".str_replace(",","",$datatable["opt_payamt"]).";
				set @PAYAMTCAL = @PAYAMT;
				set @DISCT  = ".str_replace(",","",$datatable["opt_disct"]).";
				set @PAYINT = ".str_replace(",","",$datatable["opt_payint"]).";
				set @DSCINT = ".str_replace(",","",$datatable["opt_dscint"]).";
				set @NETPAY = ".str_replace(",","",$datatable["opt_netpay"]).";
				set @TSALE  = 'R';
				set @VATRT  = (select VATRT from {$this->MAuth->getdb('ARMAST')} where CONTNO=@CONTNO);
				set @TAXRT  = @VATRT;
				set @LOCATPAY = (select LOCAT from {$this->MAuth->getdb('ARRESV')} where RESVNO=@CONTNO); 
				set @NOPAY  = null;
				set @FNOPAY = null;
				set @LNOPAY = null;
				
				if((select RESPAY-(SMPAY+@PAYAMT) from {$this->MAuth->getdb('ARRESV')} where RESVNO=@CONTNO) < 0)
				begin 
					rollback tran transaction1;
					insert into #paymentTemp select 'n' as id,'','ผิดพลาด <br>เลขที่สัญญา ".$datatable["opt_contno"]."<br>รับชำระมากกว่าที่จำนวนที่จองไว้<br>โปรดตรวจสอบข้อมูลใหม่อีกครั้ง ' as msg;
					return;
				end
				else
				begin
					DISABLE TRIGGER AFTINS_PAY008 ON {$this->MAuth->getdb('CHQTRAN')};
					set @PAYAMT_N = case when @VATRT=0 then @PAYAMT else (@PAYAMT / ((100+@VATRT) / 100.0)) end;
					set @PAYAMT_V = case when @VATRT=0 then 0 else (@PAYAMT - (@PAYAMT / ((100+@VATRT) / 100.0))) end;
					
					insert into {$this->MAuth->getdb('CHQTRAN')} (
						TMBILL, LOCATRECV, TMBILDT, CHQNO, CHQDT, 
						TSALE, PAYFOR, CONTNO, LOCATPAY, CUSCOD, 
						PAYTYP, TAXRT, PAYAMT, PAYAMT_N, PAYAMT_V, 
						DISCT, PAYINT, DSCINT, NETPAY, PAYDT, 
						NOPAY, F_PAR, F_PAY, L_PAR, L_PAY, 
						TAXNO, TAXFL, INPDT, FLAG, YFLAG, 
						USERID, DOSBILL, BALANCE, INPTIME, CANID, SYSTVER
					) values (
						@TMBILL,@LOCATRECV,@TMBILDT,@CHQNO,@CHQDT,
						@TSALE,@PAYFOR,@CONTNO,@LOCATPAY,@CUSCOD,
						
						@PAYTYP,@TAXRT,@PAYAMT,@PAYAMT_N,@PAYAMT_V,
						@DISCT, @PAYINT, @DSCINT, @NETPAY, @PAYDT,
						@NOPAY,'',@FNOPAY,'',@LNOPAY,
						@TAXNO,@TAXFL,@INPDT,@FLAG,@YFLAG,
						@USERID,'',0,@INPTIME,@CANID,'YTKMini'
					);
					
					if('".$req["PAYTYP"]."'='02')
					begin
						update {$this->MAuth->getdb('ARRESV')}
						set SMCHQ=SMCHQ+@PAYAMT
						where RESVNO=@CONTNO
					end
					else
					begin
						update {$this->MAuth->getdb('ARRESV')}
						set SMPAY=SMPAY+@PAYAMT
						where RESVNO=@CONTNO
					end;
					
					ENABLE TRIGGER AFTINS_PAY008 ON {$this->MAuth->getdb('CHQTRAN')};
				end
			";
		}else if($event == "cancel"){
			$CHQTRANQ_008 = "
				DISABLE TRIGGER AFTINS_PAY008 ON {$this->MAuth->getdb('CHQTRAN')};
				
				if exists (select * from {$this->MAuth->getdb('ARRESV')} where RESVNO='{$datatable["opt_contno"]}')
				begin
					if('".$req["PAYTYP"]."' = '02')
					begin
						update {$this->MAuth->getdb('ARRESV')}
						set SMCHQ=SMCHQ - ".str_replace(",","",$datatable["opt_payamt"])."
						where RESVNO='{$datatable["opt_contno"]}'
					end
					else
					begin
						update {$this->MAuth->getdb('ARRESV')}
						set SMPAY=SMPAY - ".str_replace(",","",$datatable["opt_payamt"])."
						where RESVNO='{$datatable["opt_contno"]}'
					end
				end
				
				update {$this->MAuth->getdb('CHQTRAN')}
				SET FLAG='C'
					,CANDT=getdate()
					,CANID='".$this->sess["USERID"]."'
					,CANTIME=getdate()
				where 1=1 and TMBILL='{$req["TMBILL"]}' and LOCATRECV='{$req["LOCATRECV"]}';  
				
				ENABLE TRIGGER AFTINS_PAY008 ON {$this->MAuth->getdb('CHQTRAN')};
			";
		}	
		
		return $CHQTRANQ_008;
	}
	
	function query009($req,$datatable,$event = "save"){
		$CHQTRANQ_009 = "";
		if($event == "save"){
			$CHQTRANQ_009 = "
				DISABLE TRIGGER AFTINS_PAY009 ON {$this->MAuth->getdb('CHQTRAN')};
				
				insert into {$this->MAuth->getdb('CHQTRAN')} (
					TMBILL, LOCATRECV, TMBILDT, CHQNO, CHQDT, 
					TSALE, PAYFOR, CONTNO, LOCATPAY, CUSCOD, 
					PAYTYP, TAXRT, PAYAMT, PAYAMT_N, PAYAMT_V, 
					DISCT, PAYINT, DSCINT, NETPAY, PAYDT, 
					NOPAY, F_PAR, F_PAY, L_PAR, L_PAY, 
					TAXNO, TAXFL, INPDT, FLAG, YFLAG, 
					USERID, DOSBILL, BALANCE, INPTIME, CANID, SYSTVER
				) values (
					@TMBILL,'".$req["LOCATRECV"]."','".$req["TMBILDT"]."','".$req["CHQNO"]."',".($req["CHQDT"] == "" ? "null":"'".$req["CHQDT"]."'").",
					'R','".$datatable["opt_payfor"]."','".$datatable["opt_contno"]."','".$req["LOCATRECV"]."','".$req["CUSCOD"]."',
					'".$req["PAYTYP"]."',0,'".str_replace(",","",$datatable["opt_payamt"])."','".str_replace(",","",$datatable["opt_payamt"])."',0,
					'".str_replace(",","",$datatable["opt_disct"])."','".str_replace(",","",$datatable["opt_payint"])."','".str_replace(",","",$datatable["opt_dscint"])."','".str_replace(",","",$datatable["opt_netpay"])."','".$req["TMBILDT"]."',
					0,'',0,'',0,
					'','N',getdate(),'H','',
					'".$this->sess["USERID"]."','',0,getdate(),'','YTKMini'
				);
										
				if('".$req["PAYTYP"]."'='02')
				begin
					update {$this->MAuth->getdb('AR_INVOI')}
					set SMCHQ=SMCHQ+@PAYAMT
						,LPAYDT=@TMBILDT
					where CONTNO=@CONTNO and LOCAT='".$req["LOCATRECV"]."'
				end
				else
				begin
					update {$this->MAuth->getdb('AR_INVOI')}
					set SMPAY=SMPAY+@PAYAMT
						,LPAYDT=@TMBILDT
					where CONTNO=@CONTNO and LOCAT='".$req["LOCATRECV"]."'
				end;
				
				ENABLE TRIGGER AFTINS_PAY009 ON {$this->MAuth->getdb('CHQTRAN')};
			";
		}else if($event == "cancel"){
			$CHQTRANQ_009 = "
				DISABLE TRIGGER AFTINS_PAY009 ON {$this->MAuth->getdb('CHQTRAN')};
				
				if exists (select * from {$this->MAuth->getdb('AR_INVOI')} where CONTNO='{$datatable["opt_contno"]}')
				begin
					if('".$req["PAYTYP"]."' = '02')
					begin
						update {$this->MAuth->getdb('AR_INVOI')}
						set SMCHQ=SMCHQ - ".str_replace(",","",$datatable["opt_payamt"])."
						where CONTNO='{$datatable["opt_contno"]}'
					end
					else
					begin
						update {$this->MAuth->getdb('AR_INVOI')}
						set SMPAY=SMPAY - ".str_replace(",","",$datatable["opt_payamt"])."
						where CONTNO='{$datatable["opt_contno"]}'
					end
				end
				
				update {$this->MAuth->getdb('CHQTRAN')}
				SET FLAG='C'
					,CANDT=getdate()
					,CANID='".$this->sess["USERID"]."'
					,CANTIME=getdate()
				where 1=1 and TMBILL='{$req["TMBILL"]}' and LOCATRECV='{$req["LOCATRECV"]}';  
				
				ENABLE TRIGGER AFTINS_PAY009 ON {$this->MAuth->getdb('CHQTRAN')};
			";
		}
		return $CHQTRANQ_009;
	}
	
	function query011($req,$datatable,$event = "save"){
		$CHQTRANQ_011 = "";
		if($event == "save"){
			$CHQTRANQ_011 = "
				DISABLE TRIGGER AFTINS_PAY011 ON {$this->MAuth->getdb('CHQTRAN')};
				
				insert into {$this->MAuth->getdb('CHQTRAN')} (
					TMBILL, LOCATRECV, TMBILDT, CHQNO, CHQDT, 
					TSALE, PAYFOR, CONTNO, LOCATPAY, CUSCOD, 
					PAYTYP, TAXRT, PAYAMT, PAYAMT_N, PAYAMT_V, 
					DISCT, PAYINT, DSCINT, NETPAY, PAYDT, 
					NOPAY, F_PAR, F_PAY, L_PAR, L_PAY, 
					TAXNO, TAXFL, INPDT, FLAG, YFLAG, 
					USERID, DOSBILL, BALANCE, INPTIME, CANID, SYSTVER
				) values (
					@TMBILL,'".$req["LOCATRECV"]."','".$req["TMBILDT"]."','".$req["CHQNO"]."',".($req["CHQDT"] == "" ? "null":"'".$req["CHQDT"]."'").",
					'R','".$datatable["opt_payfor"]."','".$datatable["opt_contno"]."','".$req["LOCATRECV"]."','".$req["CUSCOD"]."',
					'".$req["PAYTYP"]."',0,'".str_replace(",","",$datatable["opt_payamt"])."','".str_replace(",","",$datatable["opt_payamt"])."',0,
					'".str_replace(",","",$datatable["opt_disct"])."','".str_replace(",","",$datatable["opt_payint"])."','".str_replace(",","",$datatable["opt_dscint"])."','".str_replace(",","",$datatable["opt_netpay"])."','".$req["TMBILDT"]."',
					0,'',0,'',0,
					'','N',getdate(),'H','',
					'".$this->sess["USERID"]."','',0,getdate(),'','YTKMini'
				);
				
				UPDATE {$this->MAuth->getdb('ARFINC')};  
				SET FINCOM=FINCOM+".str_replace(",","",$datatable["opt_payamt"])."
				WHERE @CONTNO and LOCAT='".$req["LOCATRECV"]."'
				
				ENABLE TRIGGER AFTINS_PAY011 ON {$this->MAuth->getdb('CHQTRAN')};
			";
		}else if($event == "cancel"){
			$CHQTRANQ_011 = "
				DISABLE TRIGGER AFTINS_PAY011 ON {$this->MAuth->getdb('CHQTRAN')};
				
				if exists (select * from {$this->MAuth->getdb('ARFINC')} where CONTNO='{$datatable["opt_contno"]}')
				begin
					update {$this->MAuth->getdb('ARFINC')}
					set FINCOM=FINCOM - ".str_replace(",","",$datatable["opt_payamt"])."
					where CONTNO='{$datatable["opt_contno"]}'
				end
				
				update {$this->MAuth->getdb('CHQTRAN')}
				SET FLAG='C'
					,CANDT=getdate()
					,CANID='".$this->sess["USERID"]."'
					,CANTIME=getdate()
				where 1=1 and TMBILL='{$req["TMBILL"]}' and LOCATRECV='{$req["LOCATRECV"]}';  
				
				ENABLE TRIGGER AFTINS_PAY011 ON {$this->MAuth->getdb('CHQTRAN')};
			";
		}	
		
		return $CHQTRANQ_011;
	}
	
	function queryOTH($req,$datatable,$event = "save"){
		$CHQTRANQ_OTH = "";
		if($event == "save"){
			$CHQTRANQ_OTH = "
				DISABLE TRIGGER AFTINS_PAYOTH ON {$this->MAuth->getdb('CHQTRAN')};
				
				insert into {$this->MAuth->getdb('CHQTRAN')} (
					TMBILL, LOCATRECV, TMBILDT, CHQNO, CHQDT, 
					TSALE, PAYFOR, CONTNO, LOCATPAY, CUSCOD, 
					PAYTYP, TAXRT, PAYAMT, PAYAMT_N, PAYAMT_V, 
					DISCT, PAYINT, DSCINT, NETPAY, PAYDT, 
					NOPAY, F_PAR, F_PAY, L_PAR, L_PAY, 
					TAXNO, TAXFL, INPDT, FLAG, YFLAG, 
					USERID, DOSBILL, BALANCE, INPTIME, CANID, SYSTVER
				) values (
					@TMBILL,'".$req["LOCATRECV"]."','".$req["TMBILDT"]."','".$req["CHQNO"]."',".($req["CHQDT"] == "" ? "null":"'".$req["CHQDT"]."'").",
					'T','".$datatable["opt_payfor"]."','".$datatable["opt_contno"]."','".$req["LOCATRECV"]."','".$req["CUSCOD"]."',
					'".$req["PAYTYP"]."',0,'".str_replace(",","",$datatable["opt_payamt"])."','".str_replace(",","",$datatable["opt_payamt"])."',0,
					'".str_replace(",","",$datatable["opt_disct"])."','".str_replace(",","",$datatable["opt_payint"])."','".str_replace(",","",$datatable["opt_dscint"])."','".str_replace(",","",$datatable["opt_netpay"])."','".$req["TMBILDT"]."',
					0,'',0,'',0,
					'','N',getdate(),'H','',
					'".$this->sess["USERID"]."','',0,getdate(),'','YTKMini'
				);
										
				if('".$req["PAYTYP"]."'='02')
				begin
					update {$this->MAuth->getdb('AROTHR')}
					set SMCHQ=SMCHQ+@PAYAMT
					where ARCONT='".$datatable["opt_contno"]."' and LOCAT='".$req["LOCATRECV"]."' and PAYFOR='".$datatable["opt_payfor"]."'
				end
				else
				begin
					update {$this->MAuth->getdb('AROTHR')}
					set SMPAY=SMPAY+@PAYAMT
					where ARCONT='".$datatable["opt_contno"]."' and LOCAT='".$req["LOCATRECV"]."' and PAYFOR='".$datatable["opt_payfor"]."'
				end;
				
				ENABLE TRIGGER AFTINS_PAYOTH ON {$this->MAuth->getdb('CHQTRAN')};
			";
		}else if($event == "cancel"){
			$CHQTRANQ_OTH = "
				DISABLE TRIGGER AFTINS_PAYOTH ON {$this->MAuth->getdb('CHQTRAN')};
				
				if exists (
					select * from {$this->MAuth->getdb('CHQMAS')} 
					where 1=1 and TMBILL='{$req["TMBILL"]}' and LOCATRECV='{$req["LOCATRECV"]}' and PAYTYP='02' and FLAG='B'
				)
				begin 
					rollback tran transaction1;
					insert into #CanCPaymentTemp select 'E' as id,@TMBILL,'ผิดพลาด บิลรับชำระ '+@TMBILL+' ทำรายการ PAYIN แล้ว' as msg;
					return;
				end 
				
				if exists (
					select * from {$this->MAuth->getdb('CHQMAS')} 
					where 1=1 and TMBILL='{$req["TMBILL"]}' and LOCATRECV='{$req["LOCATRECV"]}' and PAYTYP='02' and FLAG='P'
				)
				begin 
					rollback tran transaction1;
					insert into #CanCPaymentTemp select 'E' as id,@TMBILL,'ผิดพลาด บิลรับชำระ '+@TMBILL+' บันทึกเช็คผ่านแล้ว' as msg;
					return;
				end 
				
				if exists (select * from {$this->MAuth->getdb('AROTHR')} where ARCONT='{$datatable["opt_contno"]}')
				begin
					if('".$req["PAYTYP"]."' = '02')
					begin
						update {$this->MAuth->getdb('AROTHR')}
						set SMCHQ=SMCHQ - ".str_replace(",","",$datatable["opt_payamt"])."
						where ARCONT='{$datatable["opt_contno"]}'
					end
					else
					begin
						update {$this->MAuth->getdb('AROTHR')}
						set SMPAY=SMPAY - ".str_replace(",","",$datatable["opt_payamt"])."
						where ARCONT='{$datatable["opt_contno"]}'
					end
				end
				
				update {$this->MAuth->getdb('CHQTRAN')}
				SET FLAG='C'
					,CANDT=getdate()
					,CANID='".$this->sess["USERID"]."'
					,CANTIME=getdate()
				where 1=1 and TMBILL='{$req["TMBILL"]}' and LOCATRECV='{$req["LOCATRECV"]}';  
				
				ENABLE TRIGGER AFTINS_PAYOTH ON {$this->MAuth->getdb('CHQTRAN')};
			";
		}
		
		return $CHQTRANQ_OTH;
	}
	
	function getFormNOPAYCancel(){
		$response = array("error"=>false,"errorMessage"=>"");		
		$tmbill = $_POST["tmbill"];
		
		$sql = "
			declare @contno varchar(13)	= (
				select CONTNO from {$this->MAuth->getdb('CHQTRAN')}
				where TMBILL='{$tmbill}'
			);
			
			select a.CONTNO,a.STRNO
				,a.CUSCOD
				,b.SNAM+b.NAME1+' '+b.NAME2 as CUSNAME
				,a.LOCAT
				,'{$tmbill}' as thisTMBILL
			from {$this->MAuth->getdb('ARMAST')} a 
			left join {$this->MAuth->getdb('CUSTMAST')} b on a.CUSCOD=b.CUSCOD
			where a.CONTNO=@contno
		";
		$query = $this->db->query($sql);
		
		if($query->row()){
			foreach($query->result() as $row){
				foreach($row as $key => $val){
					$response[$key] = $val;
				}
			}
		}else{
			$response["error"] = true;
			$response["errorMessage"] = "ไม่พบข้อมูลตามเงื่อนไข";
		}
		
		$sql = "
			select a.TMBILL,a.TMBILDT,a.PAYFOR,a.TSALE,a.CANDT,a.FLAG
				,a.PAYAMT,a.DISCT,a.PAYINT,a.DSCINT,a.NETPAY,a.TAXNO
				,a.F_PAR,a.F_PAY,a.L_PAR,a.L_PAY,b.PAYTYP
			
			from {$this->MAuth->getdb('CHQTRAN')} a 
			left join {$this->MAuth->getdb('CHQMAS')} b on a.TMBILL=b.TMBILL and a.LOCATRECV=b.LOCATRECV
			where a.CONTNO='{$response["CONTNO"]}' and a.PAYFOR in ('006','007')
			order by TMBILDT asc
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		if($query->row()){
			$i = 0;
			foreach($query->result() as $row){
				$response["BILL"][$i]["FLAG"] = $row->FLAG;
				$response["BILL"][$i]["TMBILL"] = $row->TMBILL;
				$response["BILL"][$i]["TMBILDT"] = $this->Convertdate(103,$row->TMBILDT);
				$response["BILL"][$i]["PAYFOR"] = $row->PAYFOR;
				$response["BILL"][$i]["TSALE"] = $row->TSALE;
				$response["BILL"][$i]["CANDT"] = $this->Convertdate(103,$row->CANDT);
				
				$response["BILL"][$i]["PAYAMT"] = number_format($row->PAYAMT,0);
				$response["BILL"][$i]["DISCT"] = number_format($row->DISCT,0);
				$response["BILL"][$i]["PAYINT"] = number_format($row->PAYINT,0);
				$response["BILL"][$i]["DSCINT"] = number_format($row->DSCINT,0);
				$response["BILL"][$i]["NETPAY"] = number_format($row->NETPAY,0);
				
				$response["BILL"][$i]["TAXNO"] = $row->TAXNO;
				$response["BILL"][$i]["F_PAR"] = $row->F_PAR;
				$response["BILL"][$i]["F_PAY"] = $row->F_PAY;
				$response["BILL"][$i]["L_PAR"] = $row->L_PAR;
				$response["BILL"][$i]["L_PAY"] = $row->L_PAY;
				$response["BILL"][$i]["PAYTYP"] = $row->PAYTYP;
				
				$i++;
			}
		}
		
		echo json_encode($response);
	}
	
	function getOutstandingBalance(){
		$CONTNO = $_POST["CONTNO"];
		
		//ปรับปรุงเบี้ยปรับล่าช้า
		$sql = "
			declare @dts datetime = (convert(varchar(8),getdate(),112));
			exec {$this->MAuth->getdb('FN_JD_LatePenalty')} @contno='{$CONTNO}',@dt = @dts;
		";
		$this->db->query($sql);
		
		//ปรับปรุงยอดค้าง
		$sql = "
			update a
			set a.EXP_AMT=b.EXP_AMT
				,a.EXP_PRD=b.EXP_PRD
				,a.EXP_FRM=b.EXP_FRM
				,a.EXP_TO=b.EXP_TO
			--select * 
			from {$this->MAuth->getdb('ARMAST')} a
			left join (
				select CONTNO,COUNT(*) as EXP_PRD
					,SUM(isnull(DAMT,0)-isnull(PAYMENT,0)) as EXP_AMT
					,MIN(NOPAY) as EXP_FRM
					,MAX(NOPAY) as EXP_TO
				from {$this->MAuth->getdb('ARPAY')}
				where 1=1 and DAMT!=PAYMENT and DDATE <= convert(varchar(8),GETDATE(),112)
				group by CONTNO
			) as b on a.CONTNO=b.CONTNO
			where a.CONTNO='{$CONTNO}'
		";
		$this->db->query($sql);
		
		$sql = "	
			declare @INTAMT decimal(18,2) = (
				SELECT SUM(INTAMT) AS BALDUE FROM {$this->MAuth->getdb('ARPAY')}
				WHERE CONTNO='{$CONTNO}'
			);
			
			declare @PAYINT decimal(18,2) = (
				SELECT SUM(PAYINT) AS BALDUE FROM {$this->MAuth->getdb('CHQTRAN')}
				WHERE CONTNO='{$CONTNO}' and PAYFOR in ('006','007') and FLAG != 'C'
			);
			
			declare @OTHER decimal(18,2) = (
				SELECT SUM(PAYAMT-SMPAY) AS BALDUE FROM {$this->MAuth->getdb('AROTHR')}
				WHERE CUSCOD=isnull((select CUSCOD from {$this->MAuth->getdb('ARMAST')} where CONTNO='{$CONTNO}'),'{$CONTNO}')
			);
			
			declare @TOTNOPAY decimal(18,2) = (
				SELECT DAMT FROM {$this->MAuth->getdb('ARPAY')}
				WHERE CONTNO='{$CONTNO}' and convert(varchar(6),DDATE,112) = convert(varchar(6),getdate(),112)
			);
			
			if exists (
				select A.CONTNO,A.CUSCOD,A.SDATE,A.TOTPRC,A.TOTDWN,A.PAYDWN,A.SMPAY
					,A.TOTDWN-A.PAYDWN AS BALDWN,A.LPAYD,A.TOTPRC-A.SMPAY AS BALAR
					,A.EXP_PRD,A.EXP_AMT,A.CONTSTAT,A.EXP_FRM,A.EXP_TO,R.REGNO,R.REGEXP 
					,isnull(@INTAMT,0) - isnull(@PAYINT,0) as TOTINT
					,isnull(@OTHER,0) as TOTOTHER
					,isnull(@TOTNOPAY,0) as TOTNOPAY
					,isnull(EXP_AMT,0) + (isnull(@INTAMT,0) - isnull(@PAYINT,0)) as TOTPAYNOW
				FROM {$this->MAuth->getdb('ARMAST')} A 
				LEFT OUTER JOIN {$this->MAuth->getdb('REGTAB')} R ON A.STRNO=R.STRNO 
				WHERE A.CONTNO='{$CONTNO}'
			)
			begin 
				select A.CONTNO,A.CUSCOD,A.SDATE,A.TOTPRC,A.TOTDWN,A.PAYDWN,A.SMPAY
					,A.TOTDWN-A.PAYDWN AS BALDWN,A.LPAYD,A.TOTPRC-A.SMPAY AS BALAR
					,A.EXP_PRD,A.EXP_AMT,A.CONTSTAT,A.EXP_FRM,A.EXP_TO,R.REGNO,R.REGEXP 
					,isnull(@INTAMT,0) - isnull(@PAYINT,0) as TOTINT
					,isnull(@OTHER,0) as TOTOTHER
					,isnull(@TOTNOPAY,0) as TOTNOPAY
					,isnull(EXP_AMT,0) + (isnull(@INTAMT,0) - isnull(@PAYINT,0)) as TOTPAYNOW
				FROM {$this->MAuth->getdb('ARMAST')} A 
				LEFT OUTER JOIN {$this->MAuth->getdb('REGTAB')} R ON A.STRNO=R.STRNO 
				WHERE A.CONTNO='{$CONTNO}'
			end
			else
			begin 
				select '{$CONTNO}' CONTNO,'' CUSCOD,'' SDATE,0 TOTPRC,0 TOTDWN,0 PAYDWN,0 SMPAY
					,0 AS BALDWN,'' LPAYD,0 AS BALAR
					,0 EXP_PRD,0 EXP_AMT,'' CONTSTAT,0 EXP_FRM,0 EXP_TO,'' REGNO,'' REGEXP 
					,isnull(@INTAMT,0) - isnull(@PAYINT,0) as TOTINT
					,isnull(@OTHER,0) as TOTOTHER
					,isnull(@TOTNOPAY,0) as TOTNOPAY
					,(isnull(@INTAMT,0) - isnull(@PAYINT,0)) as TOTPAYNOW
			end
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$data = array();
		if($query->row()){
			foreach($query->result() as $row){
				foreach($row as $key => $val){
					$data[$key] = $val;
				}
			}
		}
		
		$html = $this->FORMOutstandingBalance($data);
				
		$response = array("html"=>$html);
		echo json_encode($response);
	}
	
	private function FORMOutstandingBalance($data){
		$html = "
			<div class='col-sm-4'>
				<div class='form-group'>
					เลขที่สัญญา
					<span class='form-control'>".($data["CONTNO"])."</span>
				</div>
			</div>
			<div class='col-sm-4'>
				<div class='form-group'>
					วันที่ทำสัญญา
					<span class='form-control'>".$this->Convertdate(103,$data["SDATE"])."</span>
				</div>
			</div>
			<div class='col-sm-4'>
				<div class='form-group'>
					ราคาขาย
					<span class='form-control text-right'>".number_format($data["TOTPRC"],2)."</span>
				</div>
			</div>
			
			<div class='col-sm-4'>
				<div class='form-group'>
					เงินดาวน์
					<span class='form-control text-right'>".number_format($data["TOTDWN"],2)."</span>
				</div>
			</div>
			<div class='col-sm-4'>
				<div class='form-group has-error'>
					ค้างดาวน์
					<span class='form-control text-right text-red'>".number_format(($data["BALDWN"]),2)."</span>
				</div>
			</div>
			<div class='col-sm-4'>
				<div class='form-group has-success'>
					ชำระเงินแล้ว
					<span class='form-control text-right  text-green text-bold'>".number_format($data["SMPAY"],2)."</span>
				</div>
			</div>
			
			<div class='col-sm-4'>
				<div class='form-group'>
					สถานะสัญญา
					<span class='form-control'>".($data["CONTSTAT"])."</span>
				</div>
			</div>
			<div class='col-sm-4'>
				<div class='form-group'>
					เลขทะเบียน
					<span class='form-control'>".($data["REGNO"])."</span>
				</div>
			</div>
			<div class='col-sm-4'>
				<div class='form-group'>
					ทะเบียนหมดอายุ
					<span class='form-control'>".($data["REGEXP"])."</span>
				</div>
			</div>
			
			<div class='col-sm-4'>
				<div class='form-group'>
					ลูกหนี้คงเหลือ
					<span class='form-control text-right'>".number_format($data["BALAR"],2)."</span>
				</div>
			</div>
			<div class='col-sm-4'>
				<div class='form-group has-error'>
					ค้างลูกหนี้อื่น
					<span class='form-control text-right text-red'>".number_format($data["TOTOTHER"],2)."</span>
				</div>
			</div>
			<div class='col-sm-4'>
				<div class='form-group has-error'>
					ค้างเบี้ยปรับ
					<span class='form-control text-right text-red'>".number_format($data["TOTINT"],2)."</span>
				</div>
			</div>
			
			
			<div class='col-sm-4'>
				<div class='form-group'>
					ค้าง (งวด)
					<span class='form-control text-right'>".number_format($data["EXP_PRD"],0)."</span>
				</div>
			</div>
			<div class='col-sm-4'>
				<div class='form-group'>
					ค้างงวด จาก-ถึง
					<span class='form-control text-right'>".number_format($data["EXP_FRM"],0)." - ".number_format($data["EXP_TO"],0)."</span>
				</div>
			</div>
			<div class='col-sm-4'>
				<div class='form-group has-error'>
					ค้างค่างวด
					<span class='form-control text-right text-red'>".number_format($data["EXP_AMT"],2)."</span>
				</div>
			</div>
			
			<div class='col-sm-4'>
				<div class='form-group'>
					ชำระล่าสุด
					<span class='form-control text-right text-yellow'>".$this->Convertdate(103,$data["LPAYD"])."</span>
				</div>
			</div>
			<div class='col-sm-4'>
				<div class='form-group has-warning'>
					ค่างวดเดือนนี้
					<span class='form-control text-right text-yellow'>".number_format($data["TOTNOPAY"],2)."</span>
				</div>
			</div>
			<div class='col-sm-4'>
				<div class='form-group has-error'>
					ยอดที่ต้องชำระ
					<span class='form-control text-right text-red'><b>".number_format($data["TOTPAYNOW"],2)."</b></span>
				</div>
			</div>
			
			<div class='col-sm-10 col-sm-offset-1'>&emsp;</div>
			<div class='col-sm-10 col-sm-offset-1'>&emsp;</div>
			<div class='col-sm-10 col-sm-offset-1'>
				<div class='col-sm-6'>
					<button id='btn_FCD' class='btn btn-warning btn-block'>
						<span class='glyphicon glyphicon-search'> ส่วนลดตัดสด</span>
					</button>
				</div>
				<div class='col-sm-6'>
					<button id='btn_FinePayment' class='btn btn-warning btn-block'>
						<span class='glyphicon glyphicon-search'> เบี้ยปรับ</span>
					</button>
				</div>
			</div>
		";
		
		return $html;
	}
	
	function tmbillPDF2(){
		$TMBILL  = $_POST["TMBILL"];
		//$TMBILL  = $_GET["TMBILL"];
		
		$imgPath = 'public/images/tmbill_temp/'.md5($TMBILL).'.png';
		QRcode::png('anutin',$imgPath,'L','4',2);
		
		$data = "<image src='".base_url($imgPath)."'></image>";
		//echo $data; exit;
		
		$overflow = 238;
		$row = 0;
		$sum = 0;
		for($i=1;$i<=2;$i++){
			$data .= "
				<!-- div class='wf pf lh' style='top:{$overflow};left:0;'>&emsp;</div -->
				<div class='pf lh' style='top:{$overflow};left:0;'>ชำระค่า {$i}</div>
				<div class='pf lh' style='top:{$overflow};right:0;'>100.00</div>
			";
			$overflow += 20;
			$row += 1;
			$sum += 100;
		}
		$overflow += 70;
		
		$content = "
			<div class='wf pf' style='top:0;left:0;'>กะปางธุรกิจ</div>
			<div class='wf pf' style='top:23;left:0;'>โทร 085959595959</div>
			<div class='wf pf' style='top:46;left:0;'>ชื่อ-สกุล ลูกค้า </div><div class='wf pf' style='top:46;left:83;'>นาย ยุรนันท์ หนูจันทร์แก้ว</div>
			<div class='wf pf' style='top:69;left:0;'>เลขที่ใบรับเงิน </div><div class='wf pf' style='top:69;left:83;'>๑๑L-20020001</div>
			<div class='wf pf' style='top:92;left:0;'>วดป. </div><div class='wf pf' style='top:92;left:83;'>01/02/2563</div>
			<div class='wf pf' style='top:115;left:0;'>เลขที่สัญญา</div><div class='wf pf' style='top:115;left:83;'>๑๑C-20020002</div>
			<div class='wf pf' style='top:138;left:0;'>เลขเครื่อง</div><div class='wf pf' style='top:138;left:83;'>PRM20200201001</div>
			<div class='wf pf' style='top:161;left:0;'>สี</div><div class='wf pf' style='top:161;left:83;'>BBU</div>
			<div class='wf pf' style='top:184;left:0;'>เงื่อนไขการโอน</div><div class='wf pf' style='top:184;left:83;'>TH</div>
			
			<div class='wf pf borTB lh' style='top:215;left:0;'></div>
			<div class='pf lh' style='top:215;left:0;color:red;font-weight:bold;'>ชำระค่า</div>
			<div class='pf lh' style='top:215;right:0;color:red;font-weight:bold;'>จำนวนเงิน</div>
			<div class='wf pf borTB lh' style='top:235;left:0;'></div>
			
			".$data."
			
			<div class='wf pf borTB lh' style='top:".($overflow+20).";left:0;'></div>
			<div class='pf lh' style='top:".($overflow+20).";left:0;color:red;font-weight:bold;'>ยอดรับสุทธิ</div>
			<div class='pf lh' style='top:".($overflow+20).";right:0;color:red;font-weight:bold;'>".number_format($sum,2)."</div>
			<div class='wf pf borTB lh' style='top:".($overflow+40).";left:0;'></div>
			
			<div class='wf pf' style='top:".($overflow+70).";left:10;'>ลงชื่อ..........................................................ผู้รับเงิน</div>
			<div class='wf pf' style='top:".($overflow+90).";left:10;'>ลงชื่อ..........................................................ลูกค้า</div>
			
			<div class='wf pf' style='top:".($overflow+120).";left:90;'><img src='{$imgPath}' /></div>
			<div class='wf pf tc' style='top:".($overflow+220).";left:0;'>(แบบประเมินความเพิ่งพอใจ)</div>
			
			<div class='wf pf' style='top:".($overflow+250).";left:10;color:red;line-height:20px;'>กรุณาตรวจสอบใบเสร็จฯทุกครั้ง<br>ใบเสร็จฯจะต้องไม่มีร่องรอยการแก้ไข</div>
		";
		
		
		$defaultConfig = (new Mpdf\Config\ConfigVariables())->getDefaults();		
		$fontDirs = $defaultConfig['fontDir'];

		$defaultFontConfig = (new Mpdf\Config\FontVariables())->getDefaults();
		$fontData = $defaultFontConfig['fontdata'];
		
		$mPDFWidth = 90;
		if($this->agent->platform() == "Windows XP"){
			$mPDFWidth = 75;
		}
		//echo $mPDFWidth; exit;
		
		$mpdf = new \Mpdf\Mpdf([
			'mode' => 'utf-8', 
			//'format' => 'A4-L',
			'format' => [$mPDFWidth, (180 + ($row*5.5))],
			'margin_top' => 5, 	//default = 16
			'margin_left' => 8, 	//default = 15
			'margin_right' => 8, 	//default = 15
			'margin_bottom' => 16, 	//default = 16
			'margin_header' => 9, 	//default = 9
			'margin_footer' => 9, 	//default = 9
			
			'fontDir' => array_merge($fontDirs, [__DIR__ . '/fonts/THSarabunNew',]),
			'fontdata' => $fontData + [
				'thsarabun' => [
					'R' => 'THSarabunNew.ttf',
					'I' => 'THSarabunNew Italic.ttf',
					'B' => 'THSarabunNew Bold.ttf',
				]
			],
			'default_font' => 'thsarabun'
		]);
		
		$stylesheet = "
			<style>
				body { font-family: garuda;font-size:9pt;font-weight:bold; }
				.wf { width:100%; }
				.h10 { height:10px; }
				.tc { text-align:center; }
				.pf { position:fixed; }
				.bor { border:0.1px solid black; }
				.bor2 { border:0.1px dotted black; }
				.borTB { border-top:0.1px dotted black;border-bottom:0.1px dotted black; }
				.data { background-color:#fff;font-size:9pt; }
				.lh { line-height:20px; }
				#download { display:none; }
			</style>
		";
		//$mpdf->SetDisplayPreferences('/HideMenubar/HideToolbar/DisplayDocTitle');
		//$mpdf->SetDisplayMode('HideToolbar');
		$mpdf->WriteHTML($content.$stylesheet);
		//$mpdf->SetHTMLFooter("<div class='wf pf' style='top:740;left:0;font-size:6pt;width:1000px;text-align:right;'>{$this->sess["name"]} ออกเอกสาร ณ วันที่ ".date('d/m/').(date('Y')+543)." ".date('H:i')."</div>");
		$mpdf->fontdata['qanela'] = array('R' => "QanelasSoft-Regular.ttf",'B' => "QanelasSoft-Bold.ttf",); //แก้ปริ้นแล้วอ่านไม่ออก
		//$mpdf->SetJS('this.print();');
		//$mpdf->SetProtection(array('copy','print'), 'xxx', 'MyPassword');
		//$mpdf->Output();
		$mpdf->Output('public/images/tmbill_temp/filename.pdf','F');
		
		//unlink($imgPath);
	}
	
	function tmbillPDF3(){
		$filename = base_url("/public/images/tmbill_temp/filename.pdf"); 
		//echo $filename; exit;  
		// Header content type 
		header("Content-type: application/pdf"); 
		  
		header("Content-Length: " . filesize($filename)); 
		  
		// Send the file to the browser. 
		readfile($filename); 
	}
}




















