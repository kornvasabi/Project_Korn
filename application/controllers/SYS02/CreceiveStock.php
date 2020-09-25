<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/********************************************************
             _______________________
            / / _ _   _ _     __ 
           / // __ \ / __ \ / __ \
       _ _/ // /_/ // / / // /_/ /
     /_ _ _/ \_ _ //_/ /_/ \__  /
                          _ _/ /
                         /___ /
********************************************************/
class CreceiveStock extends MY_Controller {
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
			<div class='tab1' name='home' groupType='{$claim["groupType"]}' locat='{$this->sess['branch']}' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}'>
				<div class='row'>
					<div class='col-sm-2'>	
						<div class='form-group'>
							เลขที่บิลโอน
							<input type='text' id='TRANSNO' class='form-control input-sm' placeholder='เลขที่โอน'>
						</div>
					</div>
					<div class='col-sm-2'>	
						<div class='form-group'>
							วันที่บิลโอน
							<input type='text' id='TRANSDT' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' placeholder='วันที่โอน'>
						</div>
					</div>
					<div class='col-sm-2'>	
						<div class='form-group'>
							สาขาต้นทาง
							<input type='text' id='TRANSFM' class='form-control input-sm' placeholder='สาขาต้นทาง' value='".$this->sess['branch']."'>
						</div>
					</div>
					<div class='col-sm-2'>	
						<div class='form-group'>
							สถานะ
							<select id='TRANSSTAT' class='form-control selcls input-sm chosen-select' data-placeholder='สถานะ' >
								<option value='' selected>ทุกสถานะ</option>
								<option value='Sendding'>อยู่ระหว่างการโอนย้ายรถ</option>
								<option value='Pendding'>รับโอนรถบางส่วน</option>
								<option value='Received'>รับโอนรถครบแล้ว</option>
								<option value='Cancel'>ยกเลิกบิลโอน</option>
							</select>
						</div>
					</div>
				</div>
				<div class='row'>
					<div class='col-sm-3'>	
						<button id='btnt1UploadStock' class='btn btn-warning btn-block'><span class='glyphicon glyphicon-pencil'> นำเข้า</span></button>
					</div>					
					<div class='col-sm-3'>	
						<button id='btnt1receiveStock' class='btn btn-cyan btn-block'><span class='glyphicon glyphicon-pencil'> รับรถเข้าสต๊อค</span></button>
					</div>					
					<div class='col-sm-3 col-sm-offset-3'>	
						<button id='btnt1search' class='btn btn-primary btn-block'><span class='glyphicon glyphicon-search'> ค้นหา</span></button>
					</div>					
				</div>
				<div class='col-sm-12'>
					<div id='resultt1receiveStock'></div>
				</div>
			</div>
		";
		
		$html.= "<script src='".base_url('public/js/SYS02/CreceiveStock.js')."'></script>";
		echo $html;
	}
	
	function FormUPLOAD(){
		$html = "
			<div class='row'>
				<input type='button' id='form_import' class='btn btn-info btn-sm' style='width:100%;' value='ดาวน์โหลดฟอร์มนำเข้า'>
			</div><hr>
			<div class='row'>
				<div id='form_std'></div>
			</div>
		";
		
		$response = array("html"=>$html);
		echo json_encode($response);
	}
	
	function checkFileUpload(){
		$this->load->library('excel');
		
		$file = $_FILES["myfile"]["tmp_name"];
		
		//read file from path
		$objPHPExcel = PHPExcel_IOFactory::load($file);
		
		//X ตรวจสอบว่ามีกี่ sheet
		//X $sheetCount = $objPHPExcel->getSheetCount();
		//X จะดึงข้อมูลแค่ sheet 1 เท่านั้น
		$sheetCount = 1; 
		for($sheetIndex=0;$sheetIndex<$sheetCount;$sheetIndex++){
			$objPHPExcel->setActiveSheetIndex($sheetIndex);
			//get only the Cell Collection
			$cell_collection = $objPHPExcel->getActiveSheet()->getCellCollection();
			 
			$arrs = array("now"=>1,"old"=>1); 
			//extract to a PHP readable array format			
			foreach ($cell_collection as $cell) {
				$column = $objPHPExcel->getActiveSheet()->getCell($cell)->getColumn();
				$row = $objPHPExcel->getActiveSheet()->getCell($cell)->getRow();
				$data_value = $objPHPExcel->getActiveSheet()->getCell($cell)->getCalculatedValue();				
				//echo $data_value; exit;
				if($arrs["old"] == 1){
					$arrs["now"] = 1;
				}else if($arrs["old"] == $row){
					$arrs["now"] = $arrs["now"];
				}else{
					$arrs["now"] += 1;
				}
				
				$arr_data[$arrs["now"]][$column] = $data_value;
				$arrs["old"] = $row;
			}
		}
		
		//$arrs = array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z","AA","AB","AC");
		$arrs = array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P");
		$datasize = sizeof($arr_data);
		for($i=1;$i<=$datasize;$i++){
			foreach($arrs as $key => $val){
				if(!isset($arr_data[$i][$val])){
					$arr_data[$i][$val] = '';
				}
			}
		}
		
		/*
			// หา F ถัดไป กรณีตั้งไฟแนนท์
			declare @strno varchar(20) = '4C9800-627788';
			select @strno+'F'+cast(isnull((
				select max(REPLACE(REPLACE(STRNO,@strno,''),'F',''))+1 STRNO from (
					select STRNO from HIINCOME.dbo.INVTRAN
					where STRNO like @strno+'%'
					union
					select STRNO from HIINCOME.dbo.HINVTRAN
					where STRNO like @strno+'%'
				) as data
			),1) as varchar(5)) as nextF
		*/
		
		//$this->response["error"] = true;
		//$this->response["errorMassage"] = "ทดสอบ";
		$this->response["data"] = $arr_data;
		echo json_encode($this->response);
	}
	
	function search(){
		$arrs = array();
		/*
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
		*/
		$cond = "";
		
		$sql = "
			select ".($cond == "" ? "top 20":"")." *
			from {$this->MAuth->getdb('INVTRAN')} 
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		$NRow = 1;
		if($query->row()){
			foreach($query->result() as $row){
				$html .= "
					<tr class='trow' seq=".$NRow." >
						<td class='getit' seq=".$NRow++." RECVNO='".$row->RECVNO."' STRNO='".$row->STRNO."' style='width:50px;cursor:pointer;text-align:center;'><b>เลือก</b></td>
						<td>".$row->RECVNO."</td>
						<td>".$this->Convertdate(2,$row->RECVDT)."</td>
						<td>
							".$row->STRNO."<br/>
							".$row->ENGNO."						
						</td>
						<td>".$row->MODEL."</td>
						<td>".$row->BAAB."</td>
						<td>".$row->COLOR."</td>
						<td>".$row->STAT."</td>
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
				,TRANSFM,TRANSTO,EMPCARRY,c.employeeCode+' :: '+c.USERNAME as EMPCARRYNM
				,APPROVED,b.employeeCode+' :: '+b.USERNAME as APPROVNM
				,case when TRANSSTAT='Sendding' then 'อยู่ระหว่างการโอนย้ายรถ'
					when TRANSSTAT='Pendding' then 'รับโอนรถบางส่วน'
					when TRANSSTAT='Received' then 'รับโอนรถครบแล้ว'
					when TRANSSTAT='Cancel' then 'ยกเลิกบิลโอน' end as TRANSSTATDesc
				,TRANSSTAT,MEMO1,SYSTEM
			from {$this->MAuth->getdb('INVTransfers')} a
			left join (
				select IDNo	collate Thai_CS_AS USERID
					,employeeCode collate Thai_CS_AS employeeCode
					,firstName+' '+lastName collate Thai_CS_AS USERNAME  
				from {$this->MAuth->getdb('hp_vusers')}
			) b on a.APPROVED=b.USERID
			left join (
				select IDNo	collate Thai_CS_AS USERID
					,employeeCode collate Thai_CS_AS employeeCode
					,firstName+' '+lastName collate Thai_CS_AS USERNAME  
				from {$this->MAuth->getdb('hp_vusers')}
			) c on a.EMPCARRY=c.USERID
			where a.TRANSNO='".$arrs['TRANSNO']."'
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = array();
		if($query->row()){
			foreach($query->result() as $row){
				$html['TRANSNO'] = $row->TRANSNO;
				$html['TRANSDT'] = $this->Convertdate(2,$row->TRANSDT);
				$html['TRANSFM'] = $row->TRANSFM;
				$html['TRANSTO'] = $row->TRANSTO;
				$html['EMPCARRY'] = $row->EMPCARRY;
				$html['EMPCARRYNM'] = $row->EMPCARRYNM;
				$html['APPROVED'] = $row->APPROVED;
				$html['APPROVNM'] = $row->APPROVNM;
				$html['TRANSSTAT'] = $row->TRANSSTAT;
				$html['TRANSSTATDesc'] = $row->TRANSSTATDesc;
				$html['MEMO1'] = $row->MEMO1;
				$html['SYSTEM'] = $row->SYSTEM;
			}
		}
		
		$sql = "
			select b.TRANSITEM,b.STRNO,c.TYPE,c.MODEL,c.BAAB,COLOR,CC,c.GCODE
				,case when  a.TRANSSTAT='Cancel' then 'ยกเลิกบิลโอน' when isnull(b.RECEIVEDT,'')='' then 'อยู่ระหว่างการโอนย้ายรถ' else 'รับโอนแล้ว' end as RECEIVED
				,b.EMPCARRY,d.employeeCode+' :: '+d.USERNAME as EMPCARRYNM
				,convert(varchar(8),b.TRANSDT,112) as TRANSDT 
			from {$this->MAuth->getdb('INVTransfers')} a
			left join {$this->MAuth->getdb('INVTransfersDetails')} b on a.TRANSNO=b.TRANSNO
			left join {$this->MAuth->getdb('INVTRAN')} c on b.STRNO=c.STRNO collate Thai_CS_AS
			left join (
				select IDNo collate Thai_CS_AS USERID
					,employeeCode collate Thai_CS_AS employeeCode
					,firstName+' '+lastName collate Thai_CS_AS USERNAME  
				from {$this->MAuth->getdb('hp_vusers')}
			) d on b.EMPCARRY=d.USERID
			where a.TRANSNO='".$arrs['TRANSNO']."'
			order by b.TRANSITEM
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		//$html = array();
		$NRow = 0;
		if($query->row()){
			foreach($query->result() as $row){
				$disabled = '';
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
				
				//if($row->RECEIVED == 'อยู่ระหว่างการโอนย้ายรถ' and $arrs['clev'] == 1){ }
				
				$html['STRNO'][$NRow][] = '
					<tr seq="old'.$NRow.'" style="'.($row->RECEIVED=="ยกเลิกบิลโอน" ? "color:red":"").'">
						<td>'.$row->TRANSITEM.'</td>
						<td>'.$row->STRNO.'</td>
						<td>'.$row->MODEL.'</td>
						<td>'.$row->BAAB.'</td>
						<td>'.$row->COLOR.'</td>
						<td>'.$row->GCODE.'</td>
						<td>'.$row->RECEIVED.'</td>
						<td><input type="text" STRNO="'.$row->STRNO.'" '.$disabled.' class="SETTRANSDT form-control input-sm" data-provide="datepicker" data-date-language="th-th" placeholder="วันที่โอน"  style="width:100px;" value="'.$this->Convertdate(2,$row->TRANSDT).'"></td>
						<td><select STRNO="'.$row->STRNO.'" '.$disabled.' class="SETEMPCARRY select2"><option value=\''.$row->EMPCARRY.'\'>'.$row->EMPCARRYNM.'</option></select></td>
					</tr>
				';
			}
		}
		
		$response = array("html"=>$html);
		echo json_encode($response);
	}
	
	function getfromReceived(){
		$action = $_POST["action"];
		$obj 	= ($_POST["obj"] == "" ? array():json_decode($_POST["obj"],true));
		
		$arrs = array(
			"locat"=>$this->sess['branch'],
			"recvdt"=>$this->today('today'),
			"apcode"=>"",
			"invno"=>"",
			"invdt"=>"",
			"credtm"=>"",
			"duedt"=>"",
			"fltax"=>"",
			"memo1"=>"",
			"data"=>""
		);
		
		$sql = "
			select VATRT from ".$this->MAuth->getdb('VATMAST')."
			where getdate() between FRMDATE and TODATE
		";
		$query = $this->db->query($sql);
		
		if($query->row()){
			foreach($query->result() as $row){
				$arrs["vatrt"] = $row->VATRT;
			}
		}
		
		$sizeof = sizeof($obj);
		if($sizeof > 7){
			$arrs["locat"]	= $obj[1]["C"];
			$arrs["recvdt"]	= $obj[1]["E"];
			$arrs["apcode"]	= $obj[2]["C"];
			$arrs["invno"]	= $obj[3]["C"];
			$arrs["invdt"]	= $obj[3]["E"];
			$arrs["credtm"]	= $obj[4]["C"];
			$arrs["duedt"]	= $obj[4]["E"];
			$arrs["fltax"]	= $obj[5]["C"];
			$arrs["memo1"]	= $obj[5]["E"];
			
			for($i=8;$i<=$sizeof;$i++){
				if($obj[$i]["G"] != ""){
					$arrs["data"] .= "
						<tr>
							<td>
								<i class='del_newcar btn btn-xs btn-danger glyphicon glyphicon-minus' 
									type	 = '".$obj[$i]["B"]."'
									model	 = '".$obj[$i]["C"]."'
									baab	 = '".$obj[$i]["D"]."'
									color	 = '".$obj[$i]["E"]."'
									cc		 = '".$obj[$i]["F"]."'
									strno	 = '".$obj[$i]["G"]."'
									engno	 = '".$obj[$i]["H"]."'
									keyno	 = ''
									rvcode 	 = '".$this->sess["USERID"]."'
									rvcodnam = '".$this->sess["name"]."'
									rvlocat	 = '".$obj[1]["C"]."'
									refno	 = ''
									milert	 = '".$obj[$i]["L"]."'
									stdprc	 = '".$obj[$i]["O"]."'
									crcost	 = '".$obj[$i]["M"]."'
									disct	 = '".$obj[$i]["N"]."'
									netcost	 = '".$obj[$i]["M"]."'
									vatrt	 = ''
									crvat	 = ''
									totcost	 = ''
									gcode	 = ''
									gdesc	 = ''
									menuyr	 = ''
									bonus	 = ''
									stat	 = ''
									statname = ''
									memo1	 = '".$obj[$i]["P"]."'
								style='cursor:pointer;'> ลบ  </i>
							</td>
							<td>{$obj[$i]["B"]}</td>
							<td>{$obj[$i]["C"]}</td>
							<td>{$obj[$i]["D"]}</td>
							<td>{$obj[$i]["E"]}</td>
							<td>{$obj[$i]["F"]}</td>
							<td>{$obj[$i]["G"]}</td>
							<td>{$obj[$i]["H"]}</td>
							<td></td>
							<td>".$this->sess["name"]." (".$this->sess["USERID"].")</td>
							<td>{$obj[1]["C"]}</td>
							<td></td>
							<td>{$obj[$i]["L"]}</td>
							<td>{$obj[$i]["O"]}</td>
							<td>{$obj[$i]["M"]}</td>
							<td>0</td>
							<td>{$obj[$i]["M"]}</td>
							<td></td>
							<td></td>
							<td></td>
							<td>{$obj[$i]["J"]}</td>
							<td>{$obj[$i]["K"]}</td>
							<td></td>
							<td>".($obj[$i]["I"] == "N"?"รถใหม่":"รถเก่า")."</td>
							<td>{$obj[$i]["P"]}</td>
						</tr>
					";
				}
			}
		}
		
		$html = "
			<div class='row'>	
				<div class='col-sm-2'>	
					<div class='form-group'>
						เลขที่บิลรับรถ
						<input type='text' id='fa_recvno' class='form-control input-sm' placeholder='เลขที่บิลรับรถ'>
					</div>
				</div>
				<div class='col-sm-2'>
					<div class='form-group'>
						วันที่รับรถ
						<input type='text' id='fa_recvdt' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' placeholder='วันที่รับรถ' value='{$arrs["recvdt"]}'>
					</div>
				</div>
				
				<div class='col-sm-2'>
					<div class='form-group'>
						สาขาที่รับ
						<select id='fa_locat' class='form-control input-sm' data-placeholder='ทำสัญญาขายที่สาขา'>
							".$this->MMAIN->Option_get_locat($arrs["locat"])."
						</select>
					</div>
				</div>
				
				<div class='col-sm-2'>
					<div class='form-group'>
						เจ้าหนี้
						<select id='fa_apmast' class='form-control input-sm' data-placeholder='เจ้าหนี้'>
							".$this->MMAIN->Option_get_apmast($arrs["apcode"])."
						</select>
					</div>
				</div>
				
				<div class='col-sm-2'>
					<div class='form-group'>
						เลขที่ใบส่งสินค้า
						<input type='text' id='fa_invno' class='form-control input-sm' placeholder='เลขที่ใบส่งสินค้า' value='{$arrs["invno"]}'>
					</div>
				</div>
				
				<div class='col-sm-2'>
					<div class='form-group'>
						วันที่ส่งสินค้า
						<input type='text' id='fa_invdt' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' placeholder='วันที่ส่งสินค้า'  value='{$arrs["invdt"]}'>
					</div>
				</div>
			</div>
			<div class='row'>
				<div class='col-sm-2'>
					<div class='form-group'>
						เลขที่ใบกำกับ
						<input type='text' id='fa_taxno' class='form-control input-sm' placeholder='เลขที่ใบกำกับ' disabled>
					</div>
				</div>
				<div class='col-sm-2'>
					<div class='form-group'>
						วันที่ใบกำกับ
						<input type='text' id='fa_taxdt' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' placeholder='วันที่ใบกำกับ'  disabled>
					</div>
				</div>
				<div class='col-sm-1'>
					<div class='form-group'>
						จำนวนเครดิต
						<input type='text' id='fa_credtm' class='form-control input-sm' placeholder='จำนวนเครดิต' value='{$arrs["credtm"]}'>
					</div>
				</div>
				<div class='col-sm-2'>
					<div class='form-group'>
						วันครบดิว
						<input type='text' id='fa_duedt' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' placeholder='วันที่รับรถ'  value='{$arrs["duedt"]}'>
					</div>
				</div>
				<div class='col-sm-2'>
					<div class='form-group'>
						คำอธิบายรายการ
						<input type='text' id='fa_descp' class='form-control input-sm' placeholder='คำอธิบายรายการ'>
					</div>
				</div>
				<div class='col-sm-1'>
					<div class='form-group'>
						อัตราภาษี
						<input type='text' id='fa_vatrt' class='form-control input-sm' placeholder='อัตราภาษี' value='{$arrs["vatrt"]}' readonly>
					</div>
				</div>
				<div class='col-sm-2'>
					<div class='form-group'>
						<br>
						<div class='checkbox'>
							<label><input type='checkbox' id='fa_fltax' value='' ".($arrs["fltax"] == "Y" ? "checked":"")."> เป็นใบกำกับภาษียื่นเพิ่มเติม</label>
						</div>
					</div>
				</div>
			</div>
			
			<div class='row'>
				<div class='col-sm-12'>
					<div class='form-group'>
						หมายเหตุ
						<textarea type='text' id='fa_memo1' class='form-control input-sm' placeholder='หมายเหตุ'  rows=4 style='resize:vertical;'>{$arrs["memo1"]}</textarea>
					</div>
				</div>
			</div>
			
			<div class='row'>
				<div style='float:left;height:100%;overflow:none;' class='col-sm-12'>
					<div class='row' style='width:100%;height:100%;padding-left:30px;background-color:#269da1;'>
						<div class='form-group col-sm-12' style='height:100%;'>
							<span style='color:#efff14;'>รายการเลขถัง</span>
							<div id='table-fixed-newstock' class='dataTables_wrapper dt-bootstrap4 table-responsive' style='max-height:calc(100% - 130px);height:calc(100% - 130px);overflow-x:hidden;overflow-y:auto;border:1px dotted black;background-color:#eee;'>
								<table id='table-newstock' class='table table-bordered dataTable table-hover' stat='' aria-describedby='dataTable_info' style='width:100%;line-height:10px;white-space:nowrap;text-overflow:ellipsis;overflow:hidden;' cellspacing='0'>
									<thead>
										<tr role='row'>
											<th style='width:40px'>
												<i id='add_newcar' class='btn btn-xs btn-success glyphicon glyphicon-plus' style='cursor:pointer;'> เพิ่ม  </i>
											</th>
											<th>ยี่ห้อ</th>
											<th>รุ่น</th>
											<th>แบบ</th>
											<th>สี</th>
											<th>ขนาด</th>
											<th>เลขตัวถัง</th>
											<th>เลขเครื่อง</th>
											<th>เลขกุญแจ</th>
											<th>ผู้รับรถ</th>
											<th>สถานที่รับรถ</th>
											<th>เลขที่อ้างอิง</th>
											<th>เลขไมล์</th>
											<th>ราคาขายหน้าร้าน+VAT</th>
											<th>ราคาต้นทุน</th>
											<th>ส่วนลด</th>
											<th>คงเหลือ</th>
											<th>อัตราภาษี</th>
											<th>ภาษีมูลค่าเพิ่ม</th>
											<th>ยอดคงเหลือสุทธิ</th>
											<th>กลุ่มรถ</th>
											<th>ปีผลิต</th>
											<th>ค่าส่งเสริมการขาย</th>
											<th>สถานภาพรถ</th>
											<th>หมายเหตุ</th>
										</tr>
									</thead>
									<tbody style='white-space: nowrap;'>{$arrs["data"]}</tbody>
								</table>
								
							</div>
								<div class='row' style='width:100%;padding-left:30px;background-color:#269da1;'>
									<div style='float:left;height:100%;overflow:auto;' class='col-sm-8 col-sm-offset-2'>
										<div class='form-group col-sm-4'>
											<label class='jzfs10' for='aaaaaaa' style='color:#efff14;'>มูลค่ารวม</label>
											<input type='text' id='aaaaaaa' class='form-control input-sm text-right' value='' disabled>
											<span id='error_add2_optcost' class='error text-danger jzError'></span>		
										</div>
										
										<div class='form-group col-sm-4'>
											<label class='jzfs10' for='aaa' style='color:#efff14;'>ภาษีรวม</label>
											<input type='text' id='aaa' class='form-control input-sm text-right' value='' disabled>
											<span id='error_add2_optcost' class='error text-danger jzError'></span>		
										</div>
										
										<div class='form-group col-sm-4'>
											<label class='jzfs10' for='aaaa' style='color:#efff14;'>ยอดรวมภาษี</label>
											<input type='text' id='aaaa' class='form-control input-sm text-right' value='' disabled>
											<span id='error_add2_optsell' class='error text-danger jzError'></span>		
										</div>												
									</div>
								</div>
							</div>	
						</div>
					</div>
				</div>
			</div>
			<hr>
			<div>
				<div class='col-sm-6 text-left'>
					<input type='button' id='btnArpay' class='btn btn-xs btn-info' style='width:100px;' value='ตาราง' disabled>
					<input type='button' id='btnSend' class='btn btn-xs btn-info' style='width:100px;' value='ใบส่งมอบ' disabled>
					<input type='button' id='btnTax' class='btn btn-xs btn-info' style='width:100px;' value='ใบกำกับ' disabled>
					<br>
					<input type='button' id='btnApproveSell' class='btn btn-xs btn-info' style='width:100px;' value='ใบอนุมัติขาย' disabled>
					<input type='button' id='btnContno' class='btn btn-xs btn-info' style='width:100px;' value='สัญญา' disabled>
					<input type='button' id='btnLock' class='btn btn-xs btn-info' style='width:100px;' value='Lock สัญญา' disabled>
				</div>
				<div class='col-sm-6 text-right'>
					<button id='add_delete' class='btn btn-xs btn-danger' style='width:100px;'><span class='glyphicon glyphicon-trash'> ลบ</span></button>
					<button id='add_save' class='btn btn-xs btn-primary' style='width:100px;'><span class='glyphicon glyphicon-floppy-disk'> บันทึก</span></button>
				</div>
			</div>
		";
		
		$response = array("html"=>$html);
		echo json_encode($response);
	}
	
	function getfromADDSTRNO(){
		$html = "
			<div class='row'>
				<div class='col-sm-2 col-sm-offset-1'>
					<div class='form-group'>
						ยี่ห้อ
						<select id='fc_type' class='form-control input-sm' data-placeholder='ยี่ห้อ'>
							<option value='HONDA'>HONDA</option>
						</select>
					</div>
				</div>
				<div class='col-sm-2'>
					<div class='form-group'>
						รุ่น
						<select id='fc_model' class='form-control input-sm' data-placeholder='รุ่น'></select>
					</div>
				</div>
				<div class='col-sm-2'>
					<div class='form-group'>
						แบบ
						<select id='fc_baab' class='form-control input-sm' data-placeholder='แบบ'></select>
					</div>
				</div>
				<div class='col-sm-2'>
					<div class='form-group'>
						สี
						<select id='fc_color' class='form-control input-sm' data-placeholder='สี'></select>
					</div>
				</div>
				<div class='col-sm-2'>
					<div class='form-group'>
						ขนาด
						<select id='fc_cc' class='form-control input-sm' data-placeholder='ขนาด'></select>
					</div>
				</div>
				<div class='col-sm-2'>
					<div class='form-group'>
						เลขตัวถัง
						<input type='text' id='fc_strno' class='form-control input-sm' placeholder='เลขตัวถัง' maxlength=20>
					</div>
				</div>
				<div class='col-sm-2'>
					<div class='form-group'>
						เลขเครื่อง
						<input type='text' id='fc_engno' class='form-control input-sm' placeholder='เลขเครื่อง' maxlength=20>
					</div>
				</div>
				<div class='col-sm-2'>
					<div class='form-group'>
						เลขกุญแจ
						<input type='text' id='fc_keyno' class='form-control input-sm' placeholder='เลขกุญแจ' maxlength=20>
					</div>
				</div>
				<div class='col-sm-2'>
					<div class='form-group'>
						ผู้รับรถ
						<select id='fc_rvcode' class='form-control input-sm' data-placeholder='ผู้รับรถ'>
							".$this->MMAIN->Option_get_snusers($this->sess["USERID"])."
						</select>
					</div>
				</div>
				<div class='col-sm-2'>
					<div class='form-group'>
						สถานที่รับรถ
						<select id='fc_rvlocat' class='form-control input-sm' data-placeholder='สถานที่รับรถ'>
							".$this->MMAIN->Option_get_locat($_POST["locat"])."
						</select>
					</div>
				</div>
				<div class='col-sm-2'>
					<div class='form-group'>
						เลขที่อ้างอิง
						<input type='text' id='fc_refno' class='form-control input-sm' placeholder='เลขที่อ้างอิง'>
					</div>
				</div>
				<div class='col-sm-2'>
					<div class='form-group'>
						เลขไมล์
						<input type='number' id='fc_milert' class='form-control input-sm' placeholder='เลขไมล์' min=0>
					</div>
				</div>
				<div class='col-sm-2'>
					<div class='form-group'>
						ราคาขายหน้าร้าน+VAT
						<input type='text' id='fc_stdprc' class='form-control input-sm' placeholder='ราคาขายหน้าร้าน+VAT'>
					</div>
				</div>
				<div class='col-sm-2'>
					<div class='form-group'>
						ราคาต้นทุน
						<input type='text' id='fc_crcost' class='form-control input-sm' placeholder='ราคาต้นทุน'>
					</div>
				</div>
				<div class='col-sm-2'>
					<div class='form-group'>
						ส่วนลด
						<input type='text' id='fc_disct' class='form-control input-sm' placeholder='ส่วนลด'>
					</div>
				</div>
				<div class='col-sm-2'>
					<div class='form-group'>
						คงเหลือ
						<input type='text' id='fc_netcost' class='form-control input-sm' placeholder='คงเหลือ'>
					</div>
				</div>
				<div class='col-sm-2'>
					<div class='form-group'>
						อัตราภาษี
						<input type='text' id='fc_vatrt' class='form-control input-sm' placeholder='อัตราภาษี'>
					</div>
				</div>
				<div class='col-sm-2'>
					<div class='form-group'>
						ภาษีมูลค่าเพิ่ม
						<input type='text' id='fc_crvat' class='form-control input-sm' placeholder='ภาษีมูลค่าเพิ่ม'>
					</div>
				</div>
				<div class='col-sm-2'>
					<div class='form-group'>
						ยอดคงเหลือสุทธิ
						<input type='text' id='fc_totcost' class='form-control input-sm' placeholder='ยอดคงเหลือสุทธิ'>
					</div>
				</div>
				<div class='col-sm-2'>
					<div class='form-group'>
						กลุ่มรถ
						<select id='fc_gcode' class='form-control input-sm' data-placeholder='กลุ่มรถ'></select>
					</div>
				</div>
				<div class='col-sm-2'>
					<div class='form-group'>
						ปีผลิต
						<input type='text' id='fc_menuyr' class='form-control input-sm' placeholder='ปีผลิต'  maxlength=4 min=2000 max=9999>
					</div>
				</div>
				<div class='col-sm-2'>
					<div class='form-group'>
						ค่าส่งเสริมการขาย
						<input type='text' id='fc_bonus' class='form-control input-sm' placeholder='ค่าส่งเสริมการขาย'>
					</div>
				</div>
				<div class='col-sm-2'>
					<div class='form-group'>
						สถานภาพรถ
						<select id='fc_stat' class='form-control input-sm' data-placeholder='สถานภาพรถ'>
							<option value='N'>รถใหม่</option>
							<option value='O'>รถเก่า</option>
						</select>
					</div>
				</div>
				<div class='col-sm-12'>
					<div class='form-group'>
						หมายเหตุ
						<textarea type='text' id='fc_memo1' class='form-control input-sm' placeholder='หมายเหตุ'  rows=4 style='resize:vertical;'></textarea>
					</div>
				</div>
				<div class='col-sm-2 col-sm-offset-10'><br>	
					<button id='fc_add' class='btn btn-primary btn-block'><span class='glyphicon glyphicon-check'> เพิ่ม</span></button>
				</div>
			</div>
		";
		$response = array("html"=>$html);
		echo json_encode($response);
	}
	
	function dataSTRNO(){
		$response = array();
		$response["error"] = false;
		$response["html"]  = "";
		
		if($_POST["model"] == ""){
			$response["error"] = true;
			$response["html"]  = "ไม่พบข้อมูล..รุ่น";
			echo json_encode($response); exit;
		}
		if($_POST["baab"] == ""){
			$response["error"] = true;
			$response["html"]  = "ไม่พบข้อมูล..แบบ";
			echo json_encode($response); exit;
		}
		if($_POST["color"] == ""){
			$response["error"] = true;
			$response["html"]  = "ไม่พบข้อมูล..สีรถ";
			echo json_encode($response); exit;
		}
		if($_POST["cc"] == ""){
			$response["error"] = true;
			$response["html"]  = "ไม่พบข้อมูล..ขนาดรถ";
			echo json_encode($response); exit;
		}
		
		$del = "
			<i class='del_newcar btn btn-xs btn-danger glyphicon glyphicon-minus' 
				type	 = '".$_POST["type"]."'
				model	 = '".$_POST["model"]."'
				baab	 = '".$_POST["baab"]."'
				color	 = '".$_POST["color"]."'
				cc		 = '".$_POST["cc"]."'
				strno	 = '".$_POST["strno"]."'
				engno	 = '".$_POST["engno"]."'
				keyno	 = '".$_POST["keyno"]."'
				rvcode 	 = '".$_POST["rvcode"]."'
				rvcodnam = '".$_POST["rvcodnam"]."'
				rvlocat	 = '".$_POST["rvlocat"]."'
				refno	 = '".$_POST["refno"]."'
				milert	 = '".$_POST["milert"]."'
				stdprc	 = '".$_POST["stdprc"]."'
				crcost	 = '".$_POST["crcost"]."'
				disct	 = '".$_POST["disct"]."'
				netcost	 = '".$_POST["netcost"]."'
				vatrt	 = '".$_POST["vatrt"]."'
				crvat	 = '".$_POST["crvat"]."'
				totcost	 = '".$_POST["totcost"]."'
				gcode	 = '".$_POST["gcode"]."'
				gdesc	 = '".$_POST["gdesc"]."'
				menuyr	 = '".$_POST["menuyr"]."'
				bonus	 = '".$_POST["bonus"]."'
				stat	 = '".$_POST["stat"]."'
				statname = '".$_POST["statname"]."'
				memo1	 = '".$_POST["memo1"]."'
			style='cursor:pointer;'> ลบ  </i>
		";
		
		$html = array();
		$html[0] = $del;
		$html[1] = $_POST["type"];
		$html[2] = $_POST["model"];
		$html[3] = $_POST["baab"];
		$html[4] = $_POST["color"];
		$html[5] = $_POST["cc"];
		$html[6] = $_POST["strno"];
		$html[7] = $_POST["engno"];
		$html[8] = $_POST["keyno"];
		$html[9] = $_POST["rvcodnam"];
		$html[10] = $_POST["rvlocat"];
		$html[11] = $_POST["refno"];
		$html[12] = $_POST["milert"];
		$html[13] = $_POST["stdprc"];
		$html[14] = $_POST["crcost"];
		$html[15] = $_POST["disct"];
		$html[16] = $_POST["netcost"];
		$html[17] = $_POST["vatrt"];
		$html[18] = $_POST["crvat"];
		$html[19] = $_POST["totcost"];
		$html[20] = $_POST["gdesc"];
		$html[21] = $_POST["menuyr"];
		$html[22] = $_POST["bonus"];
		$html[23] = $_POST["statname"];
		$html[24] = $_POST["memo1"];

		$response["html"] = $html;
		echo json_encode($response);
	}
	
	
	function saveTransferCAR(){
		//print_r($_REQUEST); exit;
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
		//print_r($arrs); exit;
		
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
			//print_r($arrs['STRNO'])	; exit;
		$sql = "";
		if($arrs['TRANSNO'] == 'Auto Generate'){
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
								TRANSNO,TRANSITEM,STRNO,EMPCARRY,TRANSDT,MOVENO,RECEIVEBY,RECEIVEDT,INSERTBY,INSERTDT
							) values (
								@TRANSNO,'".($i+1)."','".$arrs['STRNO'][$i][1]."','".$arrs['STRNO'][$i][8]."',".($this->Convertdate(1,$arrs['STRNO'][$i][7]) == "" ? "NULL" : "'".$this->Convertdate(1,$arrs['STRNO'][$i][7])."'").",null,null,null,'".$this->sess["IDNo"]."',getdate()
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
			//echo $sql; exit;
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
						TRANSQTY,TRANSSTAT,MEMO1,SYSTEM,INSERTBY,INSERTDT
					) values (
						@TRANSNO,'".$arrs['TRANSDT']."','".$arrs['TRANSFM']."','".$arrs['TRANSTO']."',".($arrs['EMPCARRY'] == '' ? "NULL" : "'".$arrs['EMPCARRY']."'").",'".$arrs['APPROVED']."',
						'".$TRANSQTY."','".$arrs['TRANSSTAT']."','".$arrs['MEMO1']."','MT','".$this->sess["IDNo"]."',getdate()
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
			//echo $sql; exit;
			$this->db->query($sql);
		
			$sql = "select * from #transaction";   
			$query = $this->db->query($sql);
			$stat = true;
			$msg  = '';
			if ($query->row()) {
				foreach ($query->result() as $row) {
					$stat = ($row->id == "y" ? true : false);
					$msg = $row->msg;
					$transno = str_replace("บันทึกการโอนรถแล้ว เลขที่บิลโอน ","",$row->msg);
				}
			}else{
				$stat = false;
				$msg = "ผิดพลาด :: ไม่สามารถทำรายการได้ในขณะนี้ โปรดลองทำรายการใหม่ภายหลัง";
				$transno = "";
			}
			
			$response = array();
			$response['status'] = $stat;
			$response['msg'] = $msg;
			$response['transno'] = $transno;
			echo json_encode($response); exit;
		}else{
			$STRNO = "";
			$sql = "";
			for($i=0;$i<sizeof($arrs['STRNO']);$i++){
				$sql .= "
					if ((select count(*) from {$this->MAuth->getdb('INVTransfersDetails')}
					where TRANSNO=@TRANSNO and STRNO='".$arrs['STRNO'][$i][1]."' and RECEIVEDT is null) > 0)
					begin
						update {$this->MAuth->getdb('INVTransfersDetails')}
						set EMPCARRY='".$arrs['STRNO'][$i][8]."',
							TRANSDT=".($this->Convertdate(1,$arrs['STRNO'][$i][7]) == "" ? "NULL" : "'".$this->Convertdate(1,$arrs['STRNO'][$i][7])."'")."
						where TRANSNO=@TRANSNO and STRNO='".$arrs['STRNO'][$i][1]."'
					end
				";				
			}
			
			$sql = "
				if object_id('tempdb..#transaction') is not null drop table #transaction;
				create table #transaction (id varchar(20),msg varchar(max));
				
				declare @TRANSNO varchar(12) = '".$arrs['TRANSNO']."';
				
				begin tran ins
				begin try					
					".$sql."
					
					declare @item int = (select count(*) from {$this->MAuth->getdb('INVTransfersDetails')} where TRANSNO = @TRANSNO);
					declare @itemRV int = (select count(*) from {$this->MAuth->getdb('INVTransfersDetails')} where TRANSNO = @TRANSNO and RECEIVEDT is null);
						
					update {$this->MAuth->getdb('INVTransfers')}
					set EMPCARRY = '".$arrs['EMPCARRY']."'
						,MEMO1 = '".$arrs['MEMO1']."'
						,TRANSQTY = (select count(*) from {$this->MAuth->getdb('INVTransfersDetails')} where TRANSNO = @TRANSNO)
						,TRANSSTAT = (case when @item=@itemRV then 'Sendding' when @itemRV>0 then 'Pendding' else 'Received' end)
						,INSERTBY = '".$this->sess["IDNo"]."'
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
			$response['transno'] = $arrs['TRANSNO'];
			echo json_encode($response); exit;
		}
	}
	
	function transcode(){
		$data = array();
		$data[] = $_REQUEST["TRANSNO"];
		
		echo json_encode($this->generateData($data,"encode"));
	}
	
	function checkdt(){
		$dt = $this->Convertdate(1,$_REQUEST['dt']);
		
		$sql = "select case when '".$dt."' > convert(varchar(8),getdate(),112) then 'T' else 'F' end as data";
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$html = $row->data;
			}
		}else{
			$html = 'F';
		}
		
		$response = array("html"=>$html);
		echo json_encode($response);
	}
	
	function cancelBill(){
		$TRANSNO = $_REQUEST["TRANSNO"];
		
		if($TRANSNO == ""){
			$response = array();
			$response['status'] = false;
			$response['msg'] = 'ไม่พบข้อมูลเลขที่บิลโอน โปรดตรวจสอบรายการใหม่อีกครั้ง';
			$response['transno'] = $TRANSNO;
			echo json_encode($response); exit;
		}
		
		$sql = "
			if object_id('tempdb..#cancelBill') is not null drop table #cancelBill;
			create table #cancelBill (id varchar(20),msg varchar(max));

			begin tran ins
			begin try
				declare @rec int = (
					select count(*) from {$this->MAuth->getdb('INVTransfersDetails')}
					where TRANSNO='".$TRANSNO."' and RECEIVEDT is not null 
				)
				
				if(@rec = 0)
				begin
					update {$this->MAuth->getdb('INVTransfers')}
					set TRANSSTAT='Cancel'
					where TRANSNO='".$TRANSNO."'
					
					update c 
					set c.CRLOCAT=a.TRANSFM
					from {$this->MAuth->getdb('INVTransfers')} a
					left join {$this->MAuth->getdb('INVTransfersDetails')} b on a.TRANSNO=b.TRANSNO
					left join {$this->MAuth->getdb('INVTRAN')} c on b.STRNO=c.STRNO collate thai_cs_as
					where a.TRANSNO='".$TRANSNO."' and c.STRNO is not null
				end
				else 
				begin
					rollback tran ins;
					insert into #cancelBill select 'n' as id,'ผิดพลาด ไม่สามารถยกเลิกบิลโอนรถได้ เนื่องจากมีรถบางคันถูกรับโอนแล้วครับ' as msg;	
					return;
				end
				
				insert into {$this->MAuth->getdb('hp_UserOperationLog')} (userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
				values ('".$this->sess["IDNo"]."','SYS02::ยกเลิก บิลโอนย้ายรถ',' ".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
					
				insert into #cancelBill select 'y' as id,'ยกเลิกบิลโอน ".$TRANSNO." แล้ว' as msg;
				commit tran ins;
			end try
			begin catch
				rollback tran ins;
				insert into #cancelBill select 'n' as id,ERROR_MESSAGE() as msg;
			end catch
		";
		$this->db->query($sql);

		$sql = "select * from #cancelBill";   
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
		$response['transno'] = $TRANSNO;
		echo json_encode($response); exit;
	}
	
	function pdf(){
		$data = array();
		$data[] = $_GET["transno"];
		
		$arrs = $this->generateData($data,"decode");
		
		$sql = "select top 1 COMP_NM from {$this->MAuth->getdb('CONDPAY')}";
		$query = $this->db->query($sql);
		$row = $query->row();
		$arrs["pdf_COMP_NM"] = $row->COMP_NM;
		
		$sql = "
			select a.TRANSFM+' '+g.LOCATNM collate Thai_CS_AS as TRANSFM,a.TRANSTO+' '+h.LOCATNM collate Thai_CS_AS as TRANSTO
				,convert(varchar(8),a.TRANSDT,112) TRANSDT,a.TRANSNO,d.employeeCode APPROVED,d.USERNAME,a.MEMO1 
				,b.STRNO,c.TYPE,c.MODEL,c.BAAB,c.COLOR,c.CC,case when c.STAT='N' then 'รถใหม่' else 'รถเก่า' end as STAT
				,e.USERNAME as EMPCARRY,convert(varchar(8),b.TRANSDT,112) TRANSDTDetail
				,f.USERNAME as EMPRC,convert(varchar(8),b.RECEIVEDT,112) RECEIVEDT
			from {$this->MAuth->getdb('INVTransfers')} a
			left join {$this->MAuth->getdb('INVTransfersDetails')} b on a.TRANSNO=b.TRANSNO
			left join {$this->MAuth->getdb('INVTRAN')} c on b.STRNO=c.STRNO collate Thai_CS_AS
			left join (
				select IDNo collate Thai_CS_AS USERID
					,employeeCode collate Thai_CS_AS employeeCode
					,'คุณ'+firstName+' '+lastName collate Thai_CS_AS USERNAME  
				from {$this->MAuth->getdb('hp_vusers')}
			) d on a.APPROVED=d.USERID
			left join (
				select IDNo collate Thai_CS_AS USERID
					,employeeCode collate Thai_CS_AS employeeCode
					,titleName+firstName+' '+lastName collate Thai_CS_AS USERNAME  
				from {$this->MAuth->getdb('hp_vusers')}
			) e on b.EMPCARRY=e.USERID
			left join (
				select IDNo collate Thai_CS_AS USERID
					,employeeCode collate Thai_CS_AS employeeCode
					,titleName+firstName+' '+lastName collate Thai_CS_AS USERNAME  
				from {$this->MAuth->getdb('hp_vusers')}
			) f on b.RECEIVEBY=f.USERID
			left join {$this->MAuth->getdb('INVLOCAT')} g on a.TRANSFM=g.LOCATCD collate Thai_CS_AS
			left join {$this->MAuth->getdb('INVLOCAT')} h on a.TRANSTO=h.LOCATCD collate Thai_CS_AS
			where a.TRANSNO='".$arrs[0]."'
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		$NRow = 1;
		if($query->row()){
			foreach($query->result() as $row){
				$cdt = 0;
				$EMPTRANS = "";
				if($row->EMPCARRY != "" and $row->TRANSDTDetail != ""){
					$EMPTRANS = $row->EMPCARRY.' ('.$this->Convertdate(2,$row->TRANSDTDetail).')';
				}else{
					$cdt = "color:#aaa;";
				}

				$EMPRC = "";
				if($row->EMPRC != "" and $row->RECEIVEDT != ""){
					$EMPRC = $row->EMPRC.' ('.$this->Convertdate(2,$row->RECEIVEDT).')';
					$cdt = "color:#aaa;";
				}	
								
				$html .= "
					<tr>
						<td class='bor2' align='center' style='".$cdt."max-width:29px;width:29px;background-color:white;'>".$NRow."</td>
						<td class='bor2' style='".$cdt."max-width:150px;width:150px;background-color:white;'>".$row->STRNO."</td>
						<td class='bor2' style='".$cdt."max-width:150px;width:150px;background-color:white;'>".$row->TYPE."<br/>".$row->MODEL."<br/>".$row->BAAB."</td>
						<td class='bor2' style='".$cdt."max-width:150px;width:150px;background-color:white;'>".$row->COLOR."<br/>".$row->CC."<br/>".$row->STAT."</td>
						<td class='bor2' style='".$cdt."max-width:250px;width:250px;background-color:white;'>".$EMPTRANS."<br/>".$EMPRC."<br/></td>
					</tr>
				";
				
				
				$arrs["pdf_TRANSFM"] = $row->TRANSFM;
				$arrs["pdf_TRANSTO"] = $row->TRANSTO;
				$arrs["pdf_TRANSDT"] = $this->Convertdate(2,$row->TRANSDT);
				$arrs["pdf_TRANSNO"] = $row->TRANSNO;
				$arrs["pdf_APPROVED"] = $row->USERNAME." (".$row->APPROVED.")";
				$arrs["pdf_APPROVEDNM"] = $row->USERNAME;
				$arrs["pdf_MEMO1"] = $row->MEMO1;
				$NRow++;
			}			
		}
		
		
		
		$mpdf = new \Mpdf\Mpdf([
			'mode' => 'utf-8', 
			'format' => 'A4',
			'margin_top' => 80, 	//default = 16
			'margin_left' => 15, 	//default = 15
			'margin_right' => 15, 	//default = 15
			'margin_bottom' => 40, 	//default = 16
			'margin_header' => 9, 	//default = 9
			'margin_footer' => 40, 	//default = 9
		]);
		
		$content = "
			<table class='wf' style='font-size:9pt;height:500px;border-collapse:collapse;background-color:red;line-height:23px;overflow:wrap;vertical-align:text-top;'>
				<tbody>
					{$html}
				</tbody>
			</table>
		";
		
		$stylesheet = "
			<style>
				body { font-family: garuda;font-size:10pt; }
				.wf { width:100%; }
				.h10 { height:10px; }
				.tc { text-align:center; }
				.pf { position:fixed; }
				.bor { border:0.1px solid black; }
				.bor2 { border:0.1px dotted black; }
			</style>
		";
		$content = $content.$stylesheet;
		
		$head = "
			<div class='wf pf tc' style='font-size:13pt;'><b>{$arrs["pdf_COMP_NM"]}</b></div>
			
			<div class='wf pf' style='top:35;'>โอนย้ายรถจากสาขา</div>
			<div class='pf' style='top:35;left:120;width:560px;height:20px;background-color:white;'>{$arrs["pdf_TRANSFM"]}</div>
			
			<div class='wf pf' style='top:60;'>ไปยังสาขา</div>
			<div class='pf' style='top:60;left:120;width:560px;height:20px;background-color:white;'>{$arrs["pdf_TRANSTO"]}</div>
			
			<div class='wf pf' style='top:85;'>วันที่โอนย้าย</div>
			<!-- div class='pf' style='top:85;left:320;'>เลขที่ใบโอนย้าย</div -->
			<div class='pf' style='top:85;left:120;width:200px;height:20px;background-color:white;'>{$arrs["pdf_TRANSDT"]}</div>
			<!-- div class='pf' style='top:85;left:430;width:250px;height:20px;background-color:white;'>{$arrs["pdf_TRANSNO"]}</div -->
			
			<div class='pf' style='top:110;left:0;'>เลขที่ใบโอนย้าย</div>
			<div class='pf' style='top:110;left:120;width:250px;height:20px;background-color:white;'>{$arrs["pdf_TRANSNO"]}</div>
			
			<div class='wf pf' style='top:135;'>ผู้อนุมัติการโอนย้าย</div>
			<div class='pf' style='top:135;left:120;width:300px;height:20px;background-color:white;'>{$arrs["pdf_APPROVED"]}</div>
			
			<div class='wf pf' style='top:160;max-height:70px;height:70px;background-color:white;text-indent:70px;'>{$arrs["pdf_MEMO1"]}</div>
			<div class='wf pf' style='top:160;'>หมายเหตุ</div>
			
			<div class='wf pf' style='top:201;'>
				<table class='wf' style='font-size:10pt;border-collapse:collapse;line-height:23px;overflow:wrap;vertical-align:middle;'>
					<thead>
						<tr>
							<th class='bor' align='center' style='max-width:29px;width:29px;background-color:white;'>No.</th>
							<th class='bor' style='max-width:150px;width:150px;background-color:white;'>หมายเลขตัวถัง</th>
							<th class='bor' style='max-width:150px;width:150px;background-color:white;'>ยี่ห้อ<br/>รุ่น<br/>แบบ</th>
							<th class='bor' style='max-width:150px;width:150px;background-color:white;'>สี<br/>ขนาด<br/>สถานะรถ</th>
							<th class='bor' style='max-width:250px;width:250px;background-color:white;'>พขร (วันที่โอนย้าย)<br/>ผู้รับสินค้า (วันที่รับ)</th>
						</tr>
					</thead>
				</table>
			</div>
			<div class='wf pf' style='top:1060;left:600;font-size:6pt;'>".date('d/m/').(date('Y')+543)." ".date('H:i')." หน้า {PAGENO} / {nbpg}</div>
		";
		//<div class='wf pf' style='top:1050;left:580;'>{DATE j-m-Y H:s}  {PAGENO} / {nbpg}</div>
		$mpdf->SetHTMLHeader($head);		
		$mpdf->WriteHTML($content);
		$mpdf->SetHTMLFooter("
			<div class='pf' style='top:930;'><hr></div>
			<div class='pf' style='top:955;left:40;'>.........................................................</div>
			<div class='pf' style='top:955;left:450;'>.........................................................</div>
			
			<div class='pf' style='top:980;left:40;'>ส่วนกลาง ".$arrs["pdf_APPROVEDNM"]."</div>
			<div class='pf' style='top:980;left:450;'>(&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;)</div>
			
			<div class='pf' style='top:1005;left:100;'>ผู้อนุมัติ</div>
			<div class='pf' style='top:1005;left:520;'>ผู้รับสินค้า</div>
		");		
		$mpdf->Output();
	}
	
	
	function Save(){
		$dataSTR 	= ($_POST["dataSTR"] == "" ? array():json_decode($_POST["dataSTR"],true));
		print_r($dataSTR);
	}
}




















