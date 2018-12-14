<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ctransferscars extends MY_Controller {
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
			<div class='tab1' name='home' locat='{$this->sess['branch']}' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}' style='height:calc(100vh - 132px);overflow:auto;background-color:white;'>
				<div style='height:65px;overflow:auto;'>					
					<div class='col-xs-2 col-sm-2'>	
						<div class='form-group'>
							เลขที่โอน
							<input type='text' id='TRANSNO' class='form-control input-sm' placeholder='เลขที่โอน'>
						</div>
					</div>
					<div class='col-xs-2 col-sm-2'>	
						<div class='form-group'>
							วันที่โอน
							<input type='text' id='TRANSDT' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' placeholder='วันที่โอน'>
						</div>
					</div>
					<div class='col-xs-2 col-sm-1'>	
						<div class='form-group'>
							โอนจากสาขา
							<input type='text' id='TRANSFM' class='form-control input-sm' placeholder='โอนจากสาขา' value='".$this->sess['branch']."'>
						</div>
					</div>
					<div class='col-xs-2 col-sm-2'>	
						<div class='form-group'>
							สถานะ
							<select id='TRANSSTAT' class='form-control input-sm chosen-select' data-placeholder='สถานะ'>
								<option value='' selected>ทุกสถานะ</option>
								<option value='Sendding'>อยู่ระหว่างการโอนย้ายรถ</option>
								<option value='Pendding'>รับโอนรถบางส่วน</option>
								<option value='Received'>รับโอนรถครบแล้ว</option>
							</select>
						</div>
					</div>
					<div class='col-xs-2 col-sm-1'>	
						<div class='form-group'>
							<br>
							<input type='button' id='btnt1search' class='btn btn-primary btn-sm' value='แสดง' style='width:100%'>
						</div>
					</div>
					<div class='col-xs-2 col-sm-1 col-sm-offset-3'>	
						<div class='form-group'>
							<br>
							<input type='button' id='btnt1transfers' class='btn btn-cyan btn-sm' value='โอนย้ายรถ' style='width:100%'>
						</div>
					</div>
				</div>
				<div id='resultt1transfers' style='height:calc(100% - 65px);overflow:auto;background-color:white;'></div>
			</div>
			<div class='tab2' style='height:calc(100vh - 132px);width:100%;overflow:auto;background-color:white;'>
				<div class='col-sm-12'>
					<div class='row'>
						<div class='col-sm-2 col-sm-offset-2'>	
							<div class='form-group'>
								เลขที่โอน
								<input type='text' id='add_TRANSNO' class='form-control input-sm' placeholder='เลขที่โอน'>
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								วันที่โอน
								<input type='text' id='add_TRANSDT' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' placeholder='วันที่โอน'>
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								โอนจากสาขา
								<select id='add_TRANSFM' class='form-control input-sm'><option value='".$this->sess['branch']."'>".$this->sess['branch']."</option></select>
							</div>
						</div>

						<div class='col-sm-2'>	
							<div class='form-group'>
								ย้ายไปสาขา
								<select id='add_TRANSTO' class='form-control input-sm'></select>
							</div>
						</div>
					</div>
				
					<div class='row'>
						<div class='col-sm-2 col-sm-offset-2'>	
							<div class='form-group'>
								พขร.
								<input type='text' id='add_EMPCARRY' class='form-control input-sm' placeholder='พขร.'>
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								ผู้อนุมัติ
								<select id='add_APPROVED' class='form-control input-sm'></select>
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								สถานะ
								<select id='add_TRANSSTAT' class='form-control input-sm chosen-select' data-placeholder='สถานะ'>
									<option value='Sendding' selected>อยู่ระหว่างการโอนย้ายรถ</option>
									<option value='Pendding'>รับโอนรถบางส่วน</option>
									<option value='Received'>รับโอนรถครบแล้ว</option>
								</select>
							</div>
						</div>

						<div class='col-sm-2'>	
							<div class='form-group'>
								หมายเหตุ
								<input type='text' id='add_MEMO1' class='form-control input-sm' placeholder='หมายเหตุ'>
							</div>
						</div>
					</div>
					
					<div class='row'>
						<div class='col-sm-1'>	
							<div class='form-group'>
								<br>
								<input type='button' id='btnt2addSTRNo' class='btn btn-primary btn-sm' value='เพิ่มเลขตัวถัง' style='width:100%'>
							</div>
						</div>
						<div class='col-sm-2 col-sm-offset-9'>	
							<div class='form-group'>
								<br>
								<input type='button' id='btnt1transfers' class='btn btn-primary btn-sm' value='&#9776;&emsp; ดูลำดับการโอนรถ' style='width:100%'>
							</div>
						</div>
					</div>
					<div class='row'>
						<div class='col-sm-12'>	
							<div id='table-fixed-STRNOTRANS' class='col-sm-12' style='height:calc(100vh - 400px);width:100%;overflow:auto;background-color:white;'>
								<table id='table-STRNOTRANS' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='100%'>
									<thead>
										<tr>
											<th>#</th>
											<th>เลขตัวถัง</th>
											<th>ยี่ห้อ</th>
											<th>รุ่น</th>
											<th>แบบ</th>
											<th>สี</th>
											<th>ขนาด (CC)</th>
											<th>สถานะ</th>
										</tr>
									</thead>
									<tbody></tbody>
								</table>
							</div>	
						</div>
					</div>
					
					<div class='row'>
						<div class='col-sm-1'>	
							<div class='form-group'>
								<br>
								<input type='button' id='btnt2home' class='btn btn-inverse btn-sm' value='หน้าแรก' style='width:100%'>
							</div>
						</div>
						<div class='col-sm-1 col-sm-offset-9'>	
							<div class='form-group'>
								<br>
								<input type='button' id='btnt2del' class='btn btn-danger btn-sm' value='ลบบิลโอน' style='width:100%'>
							</div>
						</div>
						
						<div class='col-sm-1'>	
							<div class='form-group'>
								<br>
								<input type='button' id='btnt2save' class='btn btn-primary btn-sm' value='บันทึก' style='width:100%'>
							</div>
						</div>
					</div>
				</div>
			</div>
		";
		
		$html.= "<script src='".base_url('public/js/SYS02/Ctransferscars.js')."'></script>";
		echo $html;
	}
	
	function search(){
		$arrs = array();
		$arrs['TRANSNO'] = $_REQUEST['TRANSNO'];
		$arrs['TRANSDT'] = $_REQUEST['TRANSDT'];
		$arrs['TRANSFM'] = $_REQUEST['TRANSFM'];
		$arrs['TRANSSTAT'] = $_REQUEST['TRANSSTAT'];
		
		
		$cond = "";
		if($arrs['TRANSNO'] != ""){
			$cond .= " and a.TRANSNO like '%".$arrs['TRANSNO']."%'";
		}
		
		if($arrs['TRANSDT'] != ""){
			$cond .= " and CONVERT(varchar(8),a.TRANSDT,112) like '%".$this->Convertdate(1,$arrs['TRANSDT'])."%'";
		}
		
		if($arrs['TRANSFM'] != ""){
			$cond .= " and a.TRANSFM = '".$arrs['TRANSFM']."'";
		}
		
		if($arrs['TRANSSTAT'] != ""){
			$cond .= " and a.TRANSSTAT = '".$arrs['TRANSSTAT']."'";
		}
		
		$sql = "
			select ".($cond == "" ? "top 20":"")." a.TRANSNO,convert(varchar(8),a.TRANSDT,112) as TRANSDT
				,a.TRANSFM,a.TRANSTO,a.EMPCARRY,a.TRANSQTY,a.TRANSSTAT
				,case when a.TRANSSTAT='Sendding' then 'อยู่ระหว่างการโอนย้ายรถ'
					when a.TRANSSTAT='Pendding' then 'รับโอนรถบางส่วน'
					when a.TRANSSTAT='Received' then 'รับโอนรถครบแล้ว' end TRANSSTATDesc
				,a.MEMO1
				,b.USERNAME
				,convert(varchar(8),a.INSERTDT,112) as INSERTDT
			from {$this->MAuth->getdb('INVTransfers')} a
			left join {$this->MAuth->getdb('PASSWRD')} b on a.INSERTBY=b.USERID collate Thai_CS_AS
			where 1=1 ".$cond."
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		$NRow = 1;
		if($query->row()){
			foreach($query->result() as $row){
				$html .= "
					<tr class='trow' seq=".$NRow.">
						<td class='getit' seq=".$NRow++." TRANSNO='".$row->TRANSNO."' style='width:50px;cursor:pointer;text-align:center;'><b>เลือก</b></td>
						<td>".$row->TRANSNO."</td>
						<td>".$this->Convertdate(2,$row->TRANSDT)."</td>
						<td>
							".$row->TRANSFM."<br/>
							".$row->TRANSTO."						
						</td>
						<td>".$row->EMPCARRY."</td>
						<td align='center'>".$row->TRANSQTY."</td>			
						<td>".$row->MEMO1."</td>			
						<td>".$row->USERNAME."<br/>".$this->Convertdate(2,$row->INSERTDT)."<br/><span style='color:".($row->TRANSSTAT == 'Sendding' ? 'black' : ($row->TRANSSTAT == 'Pendding' ? 'blue' : 'green')).";'>".$row->TRANSSTATDesc."</span></td>
					</tr>
				";
			}
		}
		
		$html = "
			<div id='table-fixed-Ctransferscars' class='col-sm-12' style='height:100%;width:100%;overflow:auto;'>
				<table id='table-Ctransferscars' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='100%'>
					<thead>
						<tr>
							<th style='vertical-align:middle;'>#</th>
							<th style='vertical-align:middle;'>เลขที่โอน</th>
							<th style='vertical-align:middle;'>วันที่โอน</th>
							<th style='vertical-align:middle;'>จากสาขา<br/>ไปสาขา</th>
							
							<th style='vertical-align:middle;'>ชื่อ พขร.</th>
							<th style='vertical-align:middle;'>จำนวนโอน</th>
							<th style='vertical-align:middle;'>คำอธิบาย</th>
							<th style='vertical-align:middle;'>ผู้ทำรายการ<br/>วันที่ทำรายการ<br/>สถานะ</th>
						</tr>
					</thead>	
					<tbody>
						".$html."
					</tbody>
				</table>
			</div>
		";
		
		$response = array("html"=>$html);
		echo json_encode($response);
	}
	
	function getDetails(){
		$arrs = array();
		$arrs['TRANSNO'] = $_REQUEST['TRANSNO'];
		$arrs['cup'] = $_REQUEST['cup'];
		$arrs['clev'] = $_REQUEST['clev'];
		
		$sql = "
			select TRANSNO,convert(varchar(8),TRANSDT,112) as TRANSDT 
				,TRANSFM,TRANSTO,EMPCARRY,APPROVED,USERNAME+' ('+APPROVED+')' as APPROVNM
				,case when TRANSSTAT='Sendding' then 'อยู่ระหว่างการโอนย้ายรถ'
					when TRANSSTAT='Pendding' then 'รับโอนรถบางส่วน'
					else 'รับโอนรถครบแล้ว' end as TRANSSTATDesc
				,TRANSSTAT,MEMO1
			from {$this->MAuth->getdb('INVTransfers')} a
			left join (
				select USERID collate Thai_CS_AS USERID
					,USERNAME collate Thai_CS_AS USERNAME  
				from {$this->MAuth->getdb('PASSWRD')}
			) b on a.APPROVED=b.USERID
			where a.TRANSNO='".$arrs['TRANSNO']."'
		";
		$query = $this->db->query($sql);
		
		$html = array();
		if($query->row()){
			foreach($query->result() as $row){
				$html['TRANSNO'] = $row->TRANSNO;
				$html['TRANSDT'] = $this->Convertdate(2,$row->TRANSDT);
				$html['TRANSFM'] = $row->TRANSFM;
				$html['TRANSTO'] = $row->TRANSTO;
				$html['EMPCARRY'] = $row->EMPCARRY;
				$html['APPROVED'] = $row->APPROVED;
				$html['APPROVNM'] = $row->APPROVNM;
				$html['TRANSSTAT'] = $row->TRANSSTAT;
				$html['TRANSSTATDesc'] = $row->TRANSSTATDesc;
				$html['MEMO1'] = $row->MEMO1;
			}
		}
		
		$sql = "
			select b.STRNO,c.TYPE,c.MODEL,c.BAAB,COLOR,CC
				,case when isnull(b.RECEIVEDT,'')='' then 'อยู่ระหว่างการโอนย้ายรถ' else 'รับโอนแล้ว' end as RECEIVED
			from {$this->MAuth->getdb('INVTransfers')} a
			left join {$this->MAuth->getdb('INVTransfersDetails')} b on a.TRANSNO=b.TRANSNO
			left join {$this->MAuth->getdb('INVTRAN')} c on b.STRNO=c.STRNO collate Thai_CS_AS
			where a.TRANSNO='".$arrs['TRANSNO']."'
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		//$html = array();
		$NRow = 0;
		if($query->row()){
			foreach($query->result() as $row){
				$disabled;
				if($row->RECEIVED == 'อยู่ระหว่างการโอนย้ายรถ'){ 
					if($arrs['cup'] == 'T'){
						if($html['TRANSFM'] == $this->sess['branch']){
							$disabled = '';
						}else{
							if($arrs['clev'] == 1){
								$disabled = '';
							}else{
								$disabled = 'disabled';
							}
						}
					}else{
						$disabled = 'disabled'; 
					}
				}else{
					$disabled = 'disabled'; 
				}
				
				$html['STRNO'][$NRow][] = '
					<tr seq="old'.$NRow.'">
						<td><input type="button" class="delSTRNO btn btn-xs btn-danger btn-block" seq="old'.$NRow.'" value="ยกเลิก" '.$disabled.'></td>
						<td>'.$row->STRNO.'</td>
						<td>'.$row->TYPE.'</td>
						<td>'.$row->MODEL.'</td>
						<td>'.$row->BAAB.'</td>
						<td>'.$row->COLOR.'</td>
						<td>'.$row->CC.'</td>
						<td>'.$row->RECEIVED.'</td>
					</tr>
				';
			}
		}
		
		$response = array("html"=>$html);
		echo json_encode($response);
	}
	
	function getSTRNoForm(){
		$html = "
			<div style='width:100%;height:60px;background-color:white;'>
				<div class='row'>	
					<div class='col-sm-2'>	
						<div class='form-group'>
							เลขตัวถัง
							<input type='text' id='fSTRNO' class='form-control input-sm' placeholder='เลขตัวถัง'>
						</div>
					</div>
					<div class='col-sm-2'>
						<div class='form-group'>
							รุ่น
							<input type='text' id='fMODEL' class='form-control input-sm' placeholder='รุ่น'>
						</div>
					</div>
					<div class='col-sm-2'>	
						<div class='form-group'>
							ที่อยู่รถ
							<select id='fCRLOCAT' class='form-control input-sm'><option value='".$_REQUEST['locat']."'>".$_REQUEST['locat']."</option></select>
						</div>
					</div>
					<div class='col-sm-2'>
						<div class='form-group'>
							<br>
							<input type='button' id='STRNOSearch' class='btn btn-primary btn-sm' value='แสดง' style='width:100%'>
						</div>
					</div>
				</div>
			</div>
			<div id='resultSTRNO' style='width:100%;height:calc(100% - 60px);background-color:white;'></div>
		";
		
		$response = array("html"=>$html);
		echo json_encode($response);
	}
	
	function getSTRNo(){
		$arrs = array();
		$arrs['fSTRNO'] = $_REQUEST['fSTRNO'];
		$arrs['fMODEL'] = $_REQUEST['fMODEL'];
		$arrs['fCRLOCAT'] = $_REQUEST['fCRLOCAT'];
		
		$cond = "";
		if($arrs['fSTRNO'] != ''){
			$cond .= " and STRNO like '".$arrs['fSTRNO']."%'";
		}
		
		if($arrs['fMODEL'] != ''){
			$cond .= " and MODEL like '".$arrs['fMODEL']."%'";
		}
		
		if($arrs['fCRLOCAT'] != ''){
			$cond .= " and CRLOCAT like '".$arrs['fCRLOCAT']."'";
		}
		
		$sql = "
			select STRNO,TYPE,MODEL,BAAB,COLOR,CC,CRLOCAT
			from {$this->MAuth->getdb('INVTRAN')}
			where isnull(SDATE,'')='' and isnull(TSALE,'') = '' and isnull(RESVNO,'') = ''
				and isnull(RESVDT,'') = '' and FLAG='D' ".$cond."
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		$NRow = 1;
		if($query->row()){
			foreach($query->result() as $row){
				$html .= "
					<tr class='trow' seq=".$NRow.">
						<td class='getit' seq=".$NRow++." 
							STRNO='".$row->STRNO."' 
							TYPE='".$row->TYPE."' 
							MODEL='".$row->MODEL."' 
							BAAB='".$row->BAAB."' 
							COLOR='".$row->COLOR."'
							CC='".$row->CC."'
							CRLOCAT='".$row->CRLOCAT."'	style='width:50px;cursor:pointer;text-align:center;'><b>เลือก</b></td>
						<td>".$row->STRNO."</td>
						<td>".$row->TYPE."</td>
						<td>".$row->MODEL."</td>
						<td>".$row->BAAB."</td>
						<td>".$row->COLOR."</td>
						<td>".$row->CC."</td>
						<td>".$row->CRLOCAT."</td>
					</tr>
				";
			}
		}
		
		$html = "
			<div id='table-fixed-getSTRNo' class='col-sm-12' style='height:100%;width:100%;overflow:auto;'>
				<table id='table-getSTRNo' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='100%'>
					<thead>
						<tr>
							<th>#</th>
							<th>เลขตัวถัง</th>
							<th>ยี่ห้อ</th>
							<th>รุ่น</th>
							<th>แบบ</th>
							<th>สี</th>
							<th>CC</th>
							<th>ที่อยู่รถ</th>
						</tr>
					</thead>	
					<tbody>
						".$html."
					</tbody>
				</table>
			</div>
		";
		
		$response = array("html"=>$html);
		echo json_encode($response);
	}
	
	function saveTransferCAR(){
		$arrs = array();
		$arrs['TRANSNO'] = $_REQUEST['TRANSNO'];
		$arrs['TRANSDT'] = $this->Convertdate(1,$_REQUEST['TRANSDT']);
		$arrs['TRANSFM'] = $_REQUEST['TRANSFM'];
		$arrs['TRANSTO'] = $_REQUEST['TRANSTO'];
		$arrs['EMPCARRY'] = $_REQUEST['EMPCARRY'];
		$arrs['APPROVED'] = $_REQUEST['APPROVED'];
		$arrs['TRANSSTAT'] = $_REQUEST['TRANSSTAT'];
		$arrs['MEMO1'] = $_REQUEST['MEMO1'];
		$arrs['STRNO'] = (!isset($_REQUEST['STRNO']) ? '':$_REQUEST['STRNO']);
		
		if($arrs['TRANSNO'] == ''){
			$response = array();
			$response['status'] = false;
			$response['msg'] = 'ไม่พบข้อมูลเลขที่โอน โปรดทำรายการใหม่อีกครั้ง';
			echo json_encode($response); exit;
		}
		
		if($arrs['TRANSDT'] == ''){
			$response = array();
			$response['status'] = false;
			$response['msg'] = 'ไม่พบข้อมูลวันที่โอน โปรดทำรายการใหม่อีกครั้ง';
			echo json_encode($response); exit;
		}
		
		if($arrs['TRANSFM'] == ''){
			$response = array();
			$response['status'] = false;
			$response['msg'] = 'ไม่พบข้อมูลโอนจากสาขา โปรดทำรายการใหม่อีกครั้ง';
			echo json_encode($response); exit;
		}
		
		if($arrs['TRANSTO'] == ''){
			$response = array();
			$response['status'] = false;
			$response['msg'] = 'ไม่พบข้อมูลย้ายไปสาขา โปรดทำรายการใหม่อีกครั้ง';
			echo json_encode($response); exit;
		}
		
		if($arrs['TRANSFM'] == $arrs['TRANSTO']){
			$response = array();
			$response['status'] = false;
			$response['msg'] = 'ไม่อนุญาติให้สาขาต้นทางและสาขาปลายทางเป็นที่เดียวกัน โปรดทำรายการใหม่อีกครั้ง';
			echo json_encode($response); exit;
		}
		
		if($arrs['APPROVED'] == ''){
			$response = array();
			$response['status'] = false;
			$response['msg'] = 'ไม่พบข้อมูลผู้อนุมัติ โปรดทำรายการใหม่อีกครั้ง';
			echo json_encode($response); exit;
		}
		
		if($arrs['TRANSSTAT'] == ''){
			$response = array();
			$response['status'] = false;
			$response['msg'] = 'ไม่พบข้อมูลสถานะโอน โปรดทำรายการใหม่อีกครั้ง';
			echo json_encode($response); exit;
		}
		
		if($arrs['STRNO'] == ''){
			$response = array();
			$response['status'] = false;
			$response['msg'] = 'ไม่พบข้อมูลรถที่จะโอน โปรดทำรายการใหม่อีกครั้ง';
			echo json_encode($response); exit;
		}
				
		if($arrs['TRANSNO'] == 'Auto Generate'){
			$sql = "";
			$TRANSQTY = 0;
			for($i=0;$i<sizeof($arrs['STRNO']);$i++){
				$sql .= "
					set @stat = (
						select count(*) from {$this->MAuth->getdb('INVTRAN')}
						where isnull(SDATE,'')='' and isnull(TSALE,'') = '' and isnull(RESVNO,'') = ''
							and isnull(RESVDT,'') = '' and FLAG='D' and STRNO='".$arrs['STRNO'][$i][1]."' and CRLOCAT='".$arrs['TRANSFM']."'
					);
					
					if (@stat = 1)
						begin
							update {$this->MAuth->getdb('INVTRAN')}
							set CRLOCAT='TRANS'
							where STRNO='".$arrs['STRNO'][$i][1]."'
							
							insert into {$this->MAuth->getdb('INVTransfersDetails')} (
								TRANSNO,TRANSITEM,STRNO,MOVENO,RECEIVEBY,RECEIVEDT,INSERTBY,INSERTDT
							) values (
								@TRANSNO,'".($i+1)."','".$arrs['STRNO'][$i][1]."',null,null,null,'".$this->sess["USERID"]."',getdate()
							);
						end
					else
						begin 
							rollback tran ins;
							insert into #transaction select 'n' as id,'ผิดพลาด เลขตัวถัง".$arrs['STRNO'][$i][1]." ไม่ได้อยู่ในสถานะที่จะโอนย้ายได้ โปรดตรวจสอบรายการใหม่อีกครั้ง' as msg;
							return;
						end
				";
				$TRANSQTY++;
			}
			
			$sql = "
				if object_id('tempdb..#transaction') is not null drop table #transaction;
				create table #transaction (id varchar(20),msg varchar(max));

				begin tran ins
				begin try
					/* @symbol = สัญลักษณ์แทนประเภทของเลขที่ นั้นๆ */
					declare @symbol varchar(10) = (select H_TFCAR from {$this->MAuth->getdb('CONDPAY')});
					/* @rec = รหัสพื้นฐาน */
					declare @rec varchar(10) = (select SHORTL+@symbol+'-'+right(left(convert(varchar(8),GETDATE(),112),6),4) from {$this->MAuth->getdb('INVLOCAT')} where LOCATCD='".$arrs['TRANSFM']."');
					/* @TRANSNO = รหัสที่จะใช้ */
					
					declare @TRANSNO varchar(12) = (select isnull(MAX(TRANSNO),@rec+'0000') from ( 
						select TRANSNO collate Thai_CS_AS as TRANSNO from {$this->MAuth->getdb('INVTransfers')} where TRANSNO like ''+@rec+'%' 
						union select moveno collate Thai_CS_AS as moveno from {$this->MAuth->getdb('INVMOVM')} where MOVENO like ''+@rec+'%'
					) as a);
					set @TRANSNO = left(@TRANSNO ,8)+right(right(@TRANSNO ,4)+10001,4);
					
					declare @stat int; 
					
					insert into {$this->MAuth->getdb('INVTransfers')} (
						TRANSNO,TRANSDT,TRANSFM,TRANSTO,EMPCARRY,APPROVED,
						TRANSQTY,TRANSSTAT,MEMO1,INSERTBY,INSERTDT
					) values (
						@TRANSNO,'".$arrs['TRANSDT']."','".$arrs['TRANSFM']."','".$arrs['TRANSTO']."','".$arrs['EMPCARRY']."','".$arrs['APPROVED']."',
						'".$TRANSQTY."','".$arrs['TRANSSTAT']."','".$arrs['MEMO1']."','".$this->sess["USERID"]."',getdate()
					);
					
					".$sql."
					
					insert into {$this->MAuth->getdb('hp_UserOperationLog')} (userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
					values ('".$this->sess["IDNo"]."','SYS02::บันทึก โอนย้ายรถ',@TRANSNO+' ".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
					
					insert into #transaction select 'y' as id,'บันทึกการโอนรถแล้ว เลขที่บิลโอน '+@TRANSNO as msg;
					commit tran ins;
				end try
				begin catch
					rollback tran ins;
					insert into #transaction select 'n' as id,ERROR_MESSAGE() as msg;
				end catch
			";
			
			$this->db->query($sql);
		
			$sql = "select * from #transaction";   
			$query = $this->db->query($sql);
			$stat = true;
			$msg  = '';
			if ($query->row()) {
				foreach ($query->result() as $row) {
					$stat = ($row->id == "y" ? true : false);
					$msg = $row->msg;
				}
			}else{
				$stat = false;
				$msg = "ผิดพลาด :: ไม่สามารถทำรายการได้ในขณะนี้ โปรดลองทำรายการใหม่ภายหลัง";
			}
			
			$response = array();
			$response['status'] = $stat;
			$response['msg'] = $msg;
			echo json_encode($response); exit;
		}else{
			$STRNO = "";
			$sql = "";
			for($i=0;$i<sizeof($arrs['STRNO']);$i++){
				if(strpos($arrs['STRNO'][$i][0],"new") > 0){
					$sql .= "
						set @stat = (
							select count(*) from {$this->MAuth->getdb('INVTRAN')} 
							where isnull(SDATE,'')='' and isnull(TSALE,'') = '' and isnull(RESVNO,'') = ''
								and isnull(RESVDT,'') = '' and FLAG='D' and STRNO='".$arrs['STRNO'][$i][1]."' and CRLOCAT='".$arrs['TRANSFM']."'
						);
						
						if (@stat = 1)
							begin
								update {$this->MAuth->getdb('INVTRAN')} 
								set CRLOCAT='TRANS'
								where STRNO='".$arrs['STRNO'][$i][1]."'
								
								insert into {$this->MAuth->getdb('INVTransfersDetails')}  (
									TRANSNO,TRANSITEM,STRNO,MOVENO,RECEIVEBY,RECEIVEDT,INSERTBY,INSERTDT
								) values (
									@TRANSNO,'".($i+1)."','".$arrs['STRNO'][$i][1]."',null,null,null,'".$this->sess["USERID"]."',getdate()
								);
							end
						else
							begin 
								rollback tran ins;
								insert into #transaction select 'n' as id,'ผิดพลาด เลขตัวถัง ".$arrs['STRNO'][$i][1]." ไม่ได้อยู่ในสถานะที่จะโอนย้ายได้ โปรดตรวจสอบรายการใหม่อีกครั้ง' as msg;
								return;
							end
					";
				}
				
				if($STRNO != ""){ $STRNO .= ","; }
				$STRNO .= "'".$arrs['STRNO'][$i][1]."'";
			}
			
			$sql = "
				if object_id('tempdb..#transaction') is not null drop table #transaction;
				create table #transaction (id varchar(20),msg varchar(max));
				
				declare @TRANSNO varchar(12) = '".$arrs['TRANSNO']."';
				
				begin tran ins
				begin try
					declare @stat int; 
					
					".$sql."
					
					if( /*ตรวจสอบว่ามีบางคันรับโอนแล้วหรือยัง*/
						(select sum(case when RECEIVEDT is null then 0 else 1 end) from {$this->MAuth->getdb('INVTransfersDetails')} 
						where TRANSNO = @TRANSNO and STRNO in (".$STRNO.")) = 0
					)
					begin
						
						update {$this->MAuth->getdb('INVTRAN')} 
						set CRLOCAT=(select TRANSFM from {$this->MAuth->getdb('INVTransfers')}  where TRANSNO=@TRANSNO)
						where STRNO in (
							select STRNO collate Thai_CS_AS from {$this->MAuth->getdb('INVTransfersDetails')}
							where TRANSNO = @TRANSNO and STRNO not in (".$STRNO.")
						);
						
						delete from {$this->MAuth->getdb('INVTransfersDetails')}
						where TRANSNO = @TRANSNO and STRNO not in (".$STRNO.");	
					end
					else
					begin
						rollback tran ins;
						insert into #transaction select 'n' as id,'ผิดพลาด ไม่สามารถบันทึกรายการได้ เนื่องจากรถบางคัน มีการรับโอนแล้ว' as msg;
						return;
					end
					
					declare @item int = (select count(*) from {$this->MAuth->getdb('INVTransfersDetails')} where TRANSNO = @TRANSNO);
					declare @itemRV int = (select count(*) from {$this->MAuth->getdb('INVTransfersDetails')} where TRANSNO = @TRANSNO and RECEIVEDT is null);
						
					update {$this->MAuth->getdb('INVTransfers')}
					set EMPCARRY = '".$arrs['EMPCARRY']."'
						,MEMO1 = '".$arrs['MEMO1']."'
						,TRANSQTY = (select count(*) from {$this->MAuth->getdb('INVTransfersDetails')} where TRANSNO = @TRANSNO)
						,TRANSSTAT = (case when @item=@itemRV then 'Sendding' when @itemRV>0 then 'Pendding' else 'Received' end)
						,INSERTBY = '".$this->sess["USERID"]."'
						,INSERTDT = getdate()
					where TRANSNO = @TRANSNO;
					
					insert into {$this->MAuth->getdb('hp_UserOperationLog')} (userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
					values ('".$this->sess["IDNo"]."','SYS02::บันทึก โอนย้ายรถ(แก้ไข)','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
					
					insert into #transaction select 'y' as id,'บันทึกการโอนรถแล้ว เลขที่บิลโอน '+@TRANSNO as msg;
					commit tran ins;
				end try
				begin catch
					rollback tran ins;
					insert into #transaction select 'n' as id,ERROR_MESSAGE() as msg;
				end catch
			";
			//echo $sql; exit;
			$this->db->query($sql);
		
			$sql = "select * from #transaction";   
			$query = $this->db->query($sql);
			$stat = true;
			$msg  = '';
			
			if($query->row()) {
				foreach ($query->result() as $row) {
					$stat = ($row->id == "y" ? true : false);
					$msg = $row->msg;
				}
			}else{
				$stat = false;
				$msg = "ผิดพลาด :: ไม่สามารถทำรายการได้ในขณะนี้ โปรดลองทำรายการใหม่ภายหลัง";
			}
			
			$response = array();
			$response['status'] = $stat;
			$response['msg'] = $msg;
			echo json_encode($response); exit;
		}
	}
}




















