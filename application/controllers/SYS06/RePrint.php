<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/********************************************************
             ______@25/07/2020______
            / / _ _   _ _     __ 
           / // __ \ / __ \ / __ \
       _ _/ // /_/ // / / // /_/ /
     /_ _ _/ \_ _ //_/ /_/ \__  /
                          _ _/ /
                         /_ _ /
********************************************************/
class RePrint extends MY_Controller {
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
		
		//$this->config_db['database'] = $this->sess["db"];
		$this->config_db['database'] = $this->param("checkmaindb")[$this->sess['db']];
		$this->connect_db = $this->load->database($this->config_db,true);
	}
	
	function index_2(){
		$html = "<script src='".base_url('public/js/SYS06/JD_Signature.js')."'></script>";
		echo $html;
	}
	
	function index(){
		$claim = $this->MLogin->getclaim(uri_string());
		//print_r($claim); exit;
		if($claim['m_access'] != "T"){ echo "<div align='center' style='color:red;font-size:16pt;width:100%;'>ขออภัย คุณยังไม่มีสิทธิเข้าใช้งานหน้านี้ครับ</div>"; exit; }
		
		$html = "
			<div class='tab1' name='home' groupType='{$claim["groupType"]}' locat='{$this->sess['branch']}' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}' dbgroup='{$this->param("checkmaindb")[$this->sess['db']]}'>
				<div>
					<div class='row'>
						<div class='col-sm-3 col-sm-offset-3'>	
							<div class='form-group'>
								ใบรับชั่วคราว
								<input type='text' id='sch_tmbill' class='form-control input-sm' placeholder='ใบรับชั่วคราว' >
							</div>
						</div>
						<div class='col-sm-3'>	
							<div class='form-group'>
								เลขที่ใบเสร็จ
								<input type='text' id='sch_billno' class='form-control input-sm' placeholder='เลขที่ใบเสร็จ' >
							</div>
						</div>
					</div>	
					<div class='row'>	
						<div class='col-sm-3 col-sm-offset-3'>	
							<div class='form-group'>
								เลขที่สัญญา
								<input type='text' id='sch_contno' class='form-control input-sm' placeholder='เลขที่สัญญา' >
							</div>
						</div>
						<div class='col-sm-3'>	
							<div class='form-group'>
								สาขา
								<select id='sch_locatrecv' class='form-control' title='เลือก'  multiple data-actions-box='true' data-size='8' data-live-search='true'>
									".$this->MMAIN->Option_get_locat($this->sess["branch"])."
								</select>
							</div>
						</div>
					</div>	
					
					<div class='row'>	
						<div class='col-sm-3 col-sm-offset-3'>	
							<div class='form-group'>
								วันที่อนุมัติพิมพ์ซ้ำ จาก
								<input type='text' id='sch_stmbildt' class='form-control input-sm' placeholder='จาก' data-provide='datepicker' data-date-language='th-th' value='".$this->today('startofmonthB1')."'>
							</div>
						</div>	
						<div class='col-sm-3'>	
							<div class='form-group'>
								วันที่อนุมัติพิมพ์ซ้ำ ถึง
								<input type='text' id='sch_etmbildt' class='form-control input-sm' placeholder='ถึง' data-provide='datepicker' data-date-language='th-th' value='".$this->today('endofmonth')."'>
							</div>
						</div>	
					</div>
					
					<div class='row'>
						<div class='col-sm-3 col-sm-offset-3'>	
							<div class='form-group'>
								<button id='btnt1newallow' class='btn btn-cyan btn-block'>
									<span class='glyphicon glyphicon-pencil'> เพิ่มรายการใหม่</span>
								</button>
							</div>
						</div>
						<div class='col-sm-3'>	
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
		
		$html.= "<script src='".base_url('public/js/SYS06/RePrint.js')."'></script>";
		echo $html;
	}
	
	function Search(){
		$TMBILL = $_POST["TMBILL"];
		$BILLNO = $_POST["BILLNO"];
		$CONTNO = $_POST["CONTNO"];
		$LOCAT 	= $_POST["LOCAT"];
		$STMBILDT = $_POST["STMBILDT"];
		$ETMBILDT = $_POST["ETMBILDT"];
		
		$cond = "";
		if($TMBILL != ""){
			$cond .= " and a.TMBILL like '{$TMBILL}%'";
		}
		if($BILLNO != ""){
			$cond .= " and a.BILLNO like '{$BILLNO}%'";
		}
		if($CONTNO != ""){
			$cond .= " and b.CONTNO like '{$CONTNO}%'";
		}
		if(is_array($LOCAT)){
			$size = sizeof($LOCAT);
			$data = "";
			for($i=0;$i<$size;$i++){
				$data .= ($data == "" ? "":",");
				$data .= "'".$LOCAT[$i]."'";
			}
			
			$cond .= " and a.LOCATRECV in ({$data})";
		}
		
		if($STMBILDT != "" && $ETMBILDT != ""){
			$cond .= " and convert(varchar(8),a.INSDT,112) between '{$STMBILDT}' and '{$ETMBILDT}'";
		}else if($STMBILDT != "" && $ETMBILDT == ""){
			$cond .= " and convert(varchar(8),a.INSDT,112) = '{$STMBILDT}'";
		}else if($STMBILDT == "" && $ETMBILDT != ""){
			$cond .= " and convert(varchar(8),a.INSDT,112) = '{$ETMBILDT}'";
		}
	
		$sql = "
			select a.ID ,a.TMBILL ,a.BILLNO ,a.LOCATRECV
				,b.CONTNO
				,b.PAYFOR
				,b.CUSCOD
				,(	select sa.SNAM+sa.NAME1+' '+sa.NAME2 from {$this->MAuth->getdb('CUSTMAST')} sa
					where sa.CUSCOD=b.CUSCOD ) as CUSName
				
				,(	select '('+cast(sa.TOPICID as varchar)+') '+sa.TOPICName from {$this->MAuth->getdb('JD_APDoubleBillTP')} sa 
					where sa.TOPICID=a.TOPICID ) as TOPICName
				,a.MEMO1
				,a.ALTMB
				,a.ALBIL
				,a.INSBY
				,(	select top 1 sa.titleName+sa.firstName+' '+sa.lastName from {$this->MAuth->getdb('hp_vusers_all')} sa 
					where sa.IDNo = a.INSBY ) INSNM
				,a.INSDT
			from {$this->MAuth->getdb('JD_APDoubleBill')} a
			left join {$this->MAuth->getdb('CHQTRAN')} b on a.TMBILL=b.TMBILL collate thai_cs_as
				and a.LOCATRECV=b.LOCATRECV collate thai_cs_as
			where 1=1 {$cond}
		";
		$query = $this->db->query($sql);
		
		$html = "";
		$NRow = 1;
		if($query->row()){
			foreach($query->result() as $row){
				$html .= "
					<tr class='trow' seq=".$NRow." >
						<td>".$row->ID."</td>
						<td>".$row->TMBILL."</td>
						<td>".$row->BILLNO."</td>
						<td>".$row->LOCATRECV."</td>
						<td>".$row->CONTNO."</td>
						<td>".$row->PAYFOR."</td>
						<td>".$row->CUSName."</td>
						<td>".$row->TOPICName."</td>
						<td>".$row->MEMO1."</td>
						<td>".$row->ALTMB."</td>
						<td>".$row->ALBIL."</td>
						<td>".$row->INSNM."</td>
						<td>".$this->Convertdate(103,$row->INSDT)."</td>
						<td>".$this->Convertdate(108,$row->INSDT)."</td>
					</tr>
				";
			}
		}
		
		$html = "
			<div id='table-fixed-receiveStock' class='col-sm-12' style='height:100%;width:100%;overflow:auto;'>
				<table id='table-receiveStock' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='100%'>
					<thead>
						<tr>
							<th style='vertical-align:middle;'>#</th>
							<th style='vertical-align:middle;'>ใบรับชั่วคราว</th>
							<th style='vertical-align:middle;'>ใบเสร็จรับเงิน</th>
							<th style='vertical-align:middle;'>สาขา</th>
							
							<th style='vertical-align:middle;'>เลขที่สัญญา</th>
							<th style='vertical-align:middle;'>ชำระค่า</th>
							<th style='vertical-align:middle;'>ลูกค้า</th>
							<th style='vertical-align:middle;'>หัวข้อยกเลิก</th>
							<th style='vertical-align:middle;'>หมายเหตุ</th>
							<th style='vertical-align:middle;'>อนุมัติพิมพ์ใบรับ</th>
							<th style='vertical-align:middle;'>อนุมัติพิมพ์ใบเสร็จ</th>
							<th style='vertical-align:middle;'>ผู้อนุมัติ</th>
							<th style='vertical-align:middle;'>วันที่</th>
							<th style='vertical-align:middle;'>เวลา</th>
						</tr>
					</thead>	
					<tbody>
						".$html."
					</tbody>
				</table>
			</div>
		";
		
		$this->response["html"] = $html;
		echo json_encode($this->response);
	}
	
	function getFormAllow(){
		$html = "
			<ul id='search_tabs' class='nav nav-tabs'>
				<li class='active'><a data-toggle='tab' href='#app_bill_menu1'>ค้นหาบิล</a></li>
				<li class=''><a data-toggle='tab' href='#app_bill_menu2'>บันทึกรายการอนุมัติพิมพ์ซ้ำ</a></li>
				<li class=''><a data-toggle='tab' href='#app_bill_menu3'>ประวัติ</a></li>
			</ul>

			<div class='tab-content'>
				<div id='app_bill_menu1' class='tab-pane fade in active'>
					<div class='col-sm-2'>	
						<div class='form-group'>
							ใบรับชั่วคราว
							<input type='text' id='als_tmbill' class='form-control input-sm' placeholder='ใบรับชั่วคราว' >
						</div>
					</div>
					<div class='col-sm-2'>	
						<div class='form-group'>
							เลขที่ใบเสร็จ
							<input type='text' id='als_billno' class='form-control input-sm' placeholder='เลขที่ใบเสร็จ' >
						</div>
					</div>
					<div class='col-sm-2'>	
						<div class='form-group'>
							สาขา
							<select id='als_locat' class='form-control' title='เลือก'  multiple data-actions-box='true' data-size='8' data-live-search='true'>
								".$this->MMAIN->Option_get_locat($this->sess["branch"])."
							</select>
						</div>
					</div>
					<div class='col-sm-2'>
						<div class='form-group'>
							วันที่รับ จาก
							<input type='text' id='als_stmbildt' class='form-control input-sm' placeholder='จาก' data-provide='datepicker' data-date-language='th-th' value='".$this->today('today')."'>
						</div>
					</div>	
					<div class='col-sm-2'>
						<div class='form-group'>
							วันที่รับ ถึง
							<input type='text' id='als_etmbildt' class='form-control input-sm' placeholder='ถึง' data-provide='datepicker' data-date-language='th-th' value='".$this->today('today')."'>
						</div>
					</div>
					<div class='col-sm-2'>	
						<div class='form-group'><br>
							<button id='als_search' class='btn btn-sm btn-primary btn-block'>แสดง</button>
						</div>
					</div>
					<div id='als_results' class='col-sm-12'></div>
				</div>
				
				
				
				<div id='app_bill_menu2' class='tab-pane fade in'>
					<div class='col-sm-6 col-sm-offset-3'>	
						<div class='col-sm-4'>	
							<div class='form-group'>
								ใบรับชั่วคราว
								<input type='text' id='alf_tmbill' class='form-control input-sm' placeholder='ใบรับชั่วคราว'  readonly>
							</div>
						</div>
						<div class='col-sm-4'>	
							<div class='form-group'>
								เลขที่ใบเสร็จ
								<input type='text' id='alf_billno' class='form-control input-sm' placeholder='เลขที่ใบเสร็จ'  readonly>
							</div>
						</div>
						<div class='col-sm-4'>	
							<div class='form-group'>
								สาขา
								<select id='alf_locat' class='form-control' title='เลือก'  multiple data-actions-box='true' data-size='8' data-live-search='true' disabled>
									".$this->MMAIN->Option_get_locat(null)."
								</select>
							</div>
						</div>
						<div class='col-sm-12'>	
							<div class='form-group'>
								สาเหตุที่ขอปริ้นซ้ำ
								<select id='alf_topic' class='form-control input-sm'>
									".$this->MMAIN->Option_get_APDoubleBillTP(0)."
								</select>
							</div>
						</div>
						<div class='col-sm-12'>	
							<div class='form-group'>
								หมายเหตุ
								<textarea id='alf_memo1' class='form-control' placeholder='ไม่เกิน 250 ตัวอักษร' maxlength='250' style='resize:vertical;'></textarea>
							</div>
						</div>
						<div class='col-sm-12'>	
							<div class='form-group'>
								พิมพ์เอกสารซ้ำ
								<!-- select id='alf_forprint' class='form-control input-sm'>
									<option value='1'>1.ใบรับชั่วคราว</option>
									<option value='2'>2.ใบเสร็จรับเงิน</option>
									<option value='3'>3.ใบรับชั่วคราวและใบเสร็จรับเงิน</option>
								</select -->
								<div class='row'>
									<div class='col-sm-12'>
										<label class='radio-inline lobiradio-success lobiradio'>
											<input type='radio' name='alf_reprint' value='tm' checked=''> 
											<i></i> ใบรับชั่วคราว
										</label>
										<label class='radio-inline lobiradio'>
											<input type='radio' name='alf_reprint' value='bl'> 
											<i></i> ใบเสร็จรับเงิน
										</label>
										<!-- label class='radio-inline lobiradio-danger lobiradio'>
											<input type='radio' name='alf_reprint' value='all'> 
											<i></i> ใบรับชั่วคราวและใบเสร็จรับเงิน
										</label -->
									</div>
								</div>
							</div>
						</div>
						<div class='col-sm-12'>	
							<div class='form-group'><br>
								<button id='alf_save' class='btn btn-sm btn-primary btn-block'>บันทึก</button>
							</div>
						</div>
					</div>
				</div>
				<div id='app_bill_menu3' class='tab-pane fade in'>
					<div id='allog_results' class='col-sm-12'></div>
				</div>
			</div>
			
			
		";
		
		$this->response["html"] = $html;
		echo json_encode($this->response);
	}
	
	function getALSSearch(){
		$tmbill 	= $_POST["tmbill"];
		$billno 	= $_POST["billno"];
		$locat 		= (is_array($_POST["locat"]) ? $_POST["locat"]:array());
		$stmbildt 	= $this->Convertdate(1,$_POST["stmbildt"]);
		$etmbildt	= $this->Convertdate(1,$_POST["etmbildt"]);
		
		$cond = "";
		if($tmbill != ""){
			$cond .= " and a.TMBILL like '{$tmbill}%'";
		}
		if($billno != ""){
			$cond .= " and a.BILLNO like '{$billno}%'";
		}
		if(sizeof($locat) > 0){
			$data = "";
			foreach($locat as $key => $val){
				if($data != ""){ $data .= ","; }
				$data .= "'".$val."'";
			}
			$cond .= " and a.LOCATRECV in ({$data})";
		}
		if($stmbildt != "" && $etmbildt != ""){
			$cond .= " and convert(varchar(8),a.TMBILDT,112) between '{$stmbildt}' and '{$etmbildt}'";
		}else if($stmbildt != "" && $etmbildt == ""){
			$cond .= " and convert(varchar(8),a.TMBILDT,112) = '{$stmbildt}' ";
		}else if($stmbildt == "" && $etmbildt != ""){
			$cond .= " and convert(varchar(8),a.TMBILDT,112) = '{$etmbildt}' ";
		}
		
		$sql = "
			select top 100 a.TMBILL,a.TMBILDT,a.BILLNO,a.LOCATRECV,a.CUSCOD 
				,(select sa.SNAM+sa.NAME1+' '+sa.NAME2 from {$this->MAuth->getdb('CUSTMAST')} sa where sa.CUSCOD=a.CUSCOD) as CUSNAME
				,a.CHQAMT,a.NOPRNTB,a.NOPRNBL ,a.FLAG
			from (
				select a.TMBILL,a.TMBILDT,a.BILLNO,a.LOCATRECV,a.CUSCOD 				
					,a.CHQAMT,a.NOPRNTB,a.NOPRNBL ,a.FLAG
				from {$this->MAuth->getdb('CHQMAS')} a
				where 1=1 {$cond} 
			) as a
			--left join {$this->MAuth->getdb('CHQTRAN')} b on a.TMBILL=b.TMBILL and a.TMBILDT=b.TMBILDT
			--where b.TMBILL is not null
			order by a.LOCATRECV,a.TMBILL
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = array();
		if($query->row()){
			$i=0;
			foreach($query->result() as $row){
				foreach($row as $key => $val){
					switch($key){
						case 'TMBILDT':
							$html[$i][$key] = $this->Convertdate(103,$val);
							break;
						case 'CHQAMT':
							$html[$i][$key] = number_format($val,2);
							break;
						default:
							$html[$i][$key] = $val;
							break;
					}
				}
				$i++;
			}
		}
		
		$this->response["html"] = $html;
		echo json_encode($this->response);
	}
	
	function getBillLOG(){
		$TMBILL 	= $_POST["TMBILL"];
		$BILLNO 	= $_POST["BILLNO"];
		$LOCATRECV 	= $_POST["LOCATRECV"];
		$sql = "
			select a.ID,a.TMBILL,a.BILLNO
				,'('+cast(a.TOPICID as varchar)+') '+b.TOPICName as TOPICName
				,a.LOCATRECV,a.MEMO1,a.ALTMB,a.ALBIL
				,a.INSBY
				,a.INSDT
			from {$this->MAuth->getdb('JD_APDoubleBill')} a
			left join {$this->MAuth->getdb('JD_APDoubleBillTP')} b on a.TOPICID=b.TOPICID
			where a.TMBILL='{$TMBILL}' and a.BILLNO='{$BILLNO}' and a.LOCATRECV='{$LOCATRECV}'
			order by a.ID
		";
		$query = $this->db->query($sql);
		
		$html = array();
		if($query->row()){
			$i = 0;
			foreach($query->result() as $row){
				foreach($row as $key => $val){
					switch($key){
						case 'INSDT':
							$html[$i][$key] = $this->Convertdate(103,$val)." ".$this->Convertdate(108,$val);
							break;
						default:
							$html[$i][$key] = $val;
							break;
					}
				}
				$i++;
			}
		}
		
		$this->response["html"] = $html;
		echo json_encode($this->response);
	}
	
	function Save(){
		$tmbill  = $_POST["tmbill"];
		$billno  = $_POST["billno"];
		$locat   = $_POST["locat"];
		$topic   = $_POST["topic"];
		$memo1   = $_POST["memo1"];
		$reprint = $_POST["reprint"];
		
		if($tmbill == ""){
			$this->response["error"] = true;
			$this->response["errorMessage"] = "ไม่พบข้อมูล";
			echo json_encode($this->response); exit;
		}
		
		if($topic == "nouse"){
			$this->response["error"] = true;
			$this->response["errorMessage"] = "ยังไม่ระบุสาเหตุการพิมพ์ซ้ำ";
			echo json_encode($this->response); exit;
		}
		
		$ALTMB = 0;
		$ALBIL = 0;
		if($reprint == 'tm'){
			$ALTMB = 1;
		}else if($reprint == 'bl'){
			$ALBIL = 1;
		}else if($reprint == 'all'){
			$ALTMB = 1;
			$ALBIL = 1;
		}
		
		$sql = "
			if OBJECT_ID('tempdb..#allowTemp') is not null drop table #allowTemp;
			create table #allowTemp (id varchar(20),tmbill varchar(20),msg varchar(max));
			
			set NOCOUNT ON;
			begin tran transaction1
			begin try
				declare @TMBILL varchar(12) = '{$tmbill}';
				declare @BILLNO varchar(12) = '{$billno}';
				declare @LOCAT varchar(5) = '{$locat[0]}';
				declare @TOPICID int = {$topic};
				declare @MEMO1 varchar(250) = '{$memo1}';
				declare @ALTMB int = {$ALTMB};
				declare @ALBIL int = {$ALBIL};
				
				if exists( 
					select * from {$this->MAuth->getdb('CHQMAS')}
					where TMBILL=@TMBILL and BILLNO=@BILLNO  and LOCATRECV='{$locat[0]}' and FLAG='C'
				)
				begin
					rollback tran transaction1;
					insert into #allowTemp select 'E' as id,@TMBILL,'ERR1 เลขที่บิลรับชั่วคราว '+@TMBILL+' ถูกยกเลิกแล้ว<br>ไม่สามารถบันทึกรายการพิมพ์ซ้ำได้';
					return;
				end
				
				if exists( 
					select * from {$this->MAuth->getdb('CHQMAS')}
					where TMBILL=@TMBILL and BILLNO=@BILLNO and LOCATRECV=@LOCAT and FLAG!='C'
						and convert(varchar(8),TMBILDT,112) != convert(varchar(8),getdate(),112)
				)
				begin
					rollback tran transaction1;
					insert into #allowTemp select 'E' as id,@TMBILL,'ERR2 เลขที่บิลรับชั่วคราว '+@TMBILL+' ไม่ได้ตัดวันนี้<br>ไม่สามารถบันทึกรายการพิมพ์ซ้ำได้';
					return;
				end
				
				insert into {$this->MAuth->getdb('JD_APDoubleBill')} (ID,TMBILL,BILLNO,LOCATRECV,TOPICID,MEMO1,ALTMB,ALBIL,INSBY,INSDT)
				select isnull((select max(ID)+1 from {$this->MAuth->getdb('JD_APDoubleBill')}),1)
					,@TMBILL,@BILLNO,@LOCAT,@TOPICID,@MEMO1
					,isnull((select NOPRNTB from {$this->MAuth->getdb('CHQMAS')} where TMBILL=@TMBILL and BILLNO=@BILLNO and LOCATRECV=@LOCAT),0) + @ALTMB
					,isnull((select NOPRNBL from {$this->MAuth->getdb('CHQMAS')} where TMBILL=@TMBILL and BILLNO=@BILLNO and LOCATRECV=@LOCAT),0) + @ALBIL
					,'{$this->sess["IDNo"]}',getdate();
				
				insert into #allowTemp select 'S' as id,@TMBILL,'อนุมัติรายการพิมพ์ซ้ำแล้ว';
				
				insert into {$this->MAuth->getdb('hp_UserOperationLog')} (userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
				values ('".$this->sess["IDNo"]."','SYS06::อนุมัติพิมพ์บิลซ้ำ','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
				
				commit tran transaction1;				
			end try
			begin catch	
				rollback tran transaction1;
				insert into #allowTemp select 'E' as id,'{$tmbill}',cast(ERROR_LINE() as varchar)+'::'+ERROR_MESSAGE() as msg;
			end catch
		";
		//echo $sql; exit;
		$this->db->query($sql);
		
		$sql = "select * from #allowTemp";
		$query = $this->db->query($sql);
		
		if($query->row()){
			foreach($query->result() as $row){
				$this->response["error"] = ($row->id == "S" ? false:true);
				$this->response["contno"] = $row->tmbill;
				$this->response["errorMessage"] = $row->msg;
			}
		}else{
			$this->response["error"] = false;
			$this->response["contno"] = '';
			$this->response["errorMessage"] = 'ผิดพลาดไม่สามารถบันทึกการรับชำระได้ โปรดติดต่อฝ่ายไอที';
		}
		
		echo json_encode($this->response);
	}
	
}




















