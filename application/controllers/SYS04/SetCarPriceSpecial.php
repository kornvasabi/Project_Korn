<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/********************************************************
             ______@22/04/2020______
			 Pasakorn Boonded

********************************************************/
class SetCarPriceSpecial extends MY_Controller {
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
			<div class='k_tab1' name='home' locat='{$this->sess['branch']}' is_mobile='{$this->sess['is_mobile']}' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}' style='height:calc(100vh - 132px);overflow:auto;background-color:white;'>
				<div class='col-sm-12' >
					<div class='row'>
						<div class='col-sm-3'>	
							<div class='form-group'>
								รหัส
								<input type='text' id='IDS' class='form-control input-sm' placeholder='รหัส' >
							</div>
						</div>
						<div class='col-sm-3'>	
							<div class='form-group'>
								เลขตัวถัง
								<input type='text' id='STRNOS' class='form-control input-sm' placeholder='เลขถัง' >
							</div>
						</div>
						<div class='col-sm-3'>	
							<div class='form-group'>
								ราคา
								<input type='text' id='PRICES' class='form-control input-sm' placeholder='ราคา' >
							</div>
						</div>
						<div class='col-sm-3'>	
							<div class='form-group'>
								ประเภทรถ
								<input type='text' id='ISTYPES' class='form-control input-sm' placeholder='ประเภทรถ' >
							</div>
						</div>
						<div class='col-sm-4'>
							<div class='form-group'>
								<br>
								<button id='btninsert' type='button' class='btn btn-cyan' style='width:100%;'>
									<span class='glyphicon glyphicon-pencil'><b>เพิ่ม</b></span>
								</button>
							</div>
						</div>
						<div class='col-sm-4'>
							<div class='form-group'>
								<br>
								<button id='btn1import' type='button' class='btn btn-warning' style='width:100%;'>
									<span class='glyphicon glyphicon-import'><b>นำเข้า</b></span>
								</button>
							</div>
						</div>
						<div class='col-sm-4'>
							<div class='form-group'>
								<br>
								<button id='btnsearch' type='button' class='btn btn-primary' style='width:100%;'>
									<span class='fa fa-search'><b>ค้นหา</b></span>
								</button>
							</div>
						</div>
					</div>
				</div>
				<div id='result'></div>
			</div>
		";
		
		$html.= "<script src='".base_url('public/js/SYS04/SetCarPriceSpecial.js')."'></script>";
		echo $html;
	}
	function Search(){
		$ID     = $_REQUEST['ID'];
		$STRNO  = $_REQUEST['STRNO'];
		$PRICE  = str_replace(",","",$_REQUEST['PRICE']);
		$ISTYPE = $_REQUEST['ISTYPE'];
		$cond = ""; $condsearch = ""; $condtype = "";
		if($ID !== ""){
			$cond .= "and ID like '".$ID."%'";
			$condsearch .="  รหัส : ".$ID."";
		}
		if($STRNO !== ""){
			$cond .= "and STRNO like '".$STRNO."%'";
			$condsearch .="  เลขตัวถัง : ".$STRNO."";
		}
		if($PRICE !== ""){
			$cond .= "and PRICE like '".$PRICE."%'";
			$condsearch .="  ราคา : ".number_format($PRICE,2)."";
		}
		if($ISTYPE !== ""){
			$condtype .= "and ISTYPE like '".$ISTYPE."%' or ISTYPENM like '".$ISTYPE."%'";
			$condsearch .="  ประเภทรถ : ".$ISTYPE."";
		}
		$html = "";
		$sql = "
			select * from (
				select ".($cond == "" ? "top 500":"")." ID,STRNO,PRICE,ISTYPE
					,case 
						when ISTYPE = 1 then '(1) กลุ่มครอบครัว' 
						when ISTYPE = 2 then '(2) กลุ่มเอที' 
						when ISTYPE = 3 then '(3) กลุ่มสปอร์ต' 
						else 'กรุณาเพิ่มกลุ่มรถ' 
					end as ISTYPENM
					,case when CONTNO IS NULL then 'ยังไม่ขาย' else 'ขายแล้ว'+' ('+CONTNO+')' end as STATUSPRICE
					,convert(varchar(8),StartDT,112) StartDT,convert(varchar(8),EndDT,112) as EndDT,INSBY
					,convert(varchar(8),INSDT,112) INSDT 
				from {$this->MAuth->getdb('STDSpecial')}
				where 1=1 ".$cond."
			)a where 1=1 ".$condtype." order by ID
		";
		//echo $sql; exit;
		$NRow = 1;
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				if($row->STATUSPRICE == "ยังไม่ขาย"){
					$colors = "color:green;";
					$rowc = "";
					$status = "nosale";
				}else{
					$colors = "color:red;";
					$rowc = "color:red;";
					$status = "sale";
				}
				$html .="
					<tr style='{$rowc}' class='trow' seq=".$NRow.">
						<td class='getit IDClick' seq=".$NRow++." STRNO='".$row->STRNO."' STATUS = '".$status."'
							IDS = '".$row->ID."' style='cursor: pointer; text-align: center; background-color: rgb(255, 255, 255);'>
						<b>เลือก</b></td>
						<td>".$row->ID."</td>
						<td>".$row->STRNO."</td>
						<td>".number_format($row->PRICE,2)."</td>
						<td>".$row->ISTYPENM."</td>
						<td style='{$colors}'>".$row->STATUSPRICE."</td>
						<td> ".$row->INSBY."</td>
						<td>".$this->Convertdate(2,$row->StartDT)."</td>
						<td>".$this->Convertdate(2,$row->EndDT)."</td>
					</tr>
				";
				$NRow++;
			}
		}
		$html = "
			<div id='table-fixed-PriceSpecial' class='col-sm-12' style='height:calc(100% - 30px);width:100%;overflow:auto;font-size:8pt;'>
				<table id='table-PriceSpecial' class='table table-bordered' cellspacing='0' width='calc(100% - 1px)'>
					<thead style='background: rgba(0, 0, 0, 0) url(&#39;../public/lobiadmin-master/version/1.0/ajax/img/bg/bg6.png&#39;) repeat scroll 0% 0%;'>
						<tr style='line-height:20px;'>
							<td style='vertical-align:middle;text-align:center;font-size:8pt;' colspan='9'>
								<b>เงื่อนไข {$condsearch}</b>
							</td>
						</tr>
						<tr>
							<th style='vertical-align:middle;'>#</th>
							<th style='vertical-align:middle;'>id</th>
							<th style='vertical-align:middle;'>เลขตัวถัง</th>
							<th style='vertical-align:middle;'>ราคา</th>
							<th style='vertical-align:middle;'>ประเภทรถ</th>
							<th style='vertical-align:middle;'>สถานะการขาย</th>
							<th style='vertical-align:middle;'>เพิ่มโดย</th>
							<th style='vertical-align:middle;'>จากวันที่</th>
							<th style='vertical-align:middle;'>ถึงวันที่</th>
						</tr>
					</thead>	
					<tbody>
						".$html."
					</tbody>
				</table>
			</div>
		";
		$response = array("html"=>$html,"status"=>true);
		echo json_encode($response);
	}
	function getformSetCarPrice(){
		$ID    = $_REQUEST['ID'];
		$STRNO = $_REQUEST['STRNO'];
		$EVENT = $_REQUEST['EVENT'];
		$arrs = array();
		$arrs['ID']     = "Auto Genarate";
		$arrs['STRNO']  = "";
		$arrs['PRICE']  = "";
		$arrs['ISTYPE'] = "";
		$arrs['StartDT']= "";
		$arrs['EndDT']  = "";
		$arrs['INSBY']  = "";
		$sql = "
			select ID,STRNO,PRICE,ISTYPE,CONTNO
			,convert(varchar(8),StartDT,112) StartDT,convert(varchar(8),EndDT,112) as EndDT
			,INSBY,convert(varchar(8),INSDT,112) INSDT 
			from {$this->MAuth->getdb('STDSpecial')} where STRNO = '".$STRNO."' and ID = '".$ID."'
		";
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$arrs['ID']     = $row->ID;
				$arrs['STRNO']  = $row->STRNO;
				$arrs['PRICE']  = number_format($row->PRICE,2);
				$arrs['ISTYPE'] = $row->ISTYPE;
				$arrs['StartDT']= $this->Convertdate(2,$row->StartDT);
				$arrs['EndDT']  = $this->Convertdate(2,$row->EndDT);
				$arrs['INSBY']  = $row->INSBY;
			}
		}
		$html = "
			<div class='col-sm-10 col-sm-offset-1'>
				<div class='row'>
					<div class='col-sm-12'>
						รหัส
						<input type='text' id='ID' class='form-control input-sm' value='{$arrs['ID']}' readonly>
					</div>
					<div class='col-sm-12'>
						เลขตัวถัง
						<input type='text' id='STRNO' class='form-control input-sm' value='{$arrs['STRNO']}' placeholder='เลขตัวถัง' >
					</div>
					<div class='col-sm-12'>
						ราคา
						<input type='text' id='PRICE' class='form-control input-sm jzAllowNumber' value='{$arrs['PRICE']}' placeholder='ราคา' >
					</div>
					<div class='col-sm-12'>
						กลุ่มรถ
						<!--input type='text' id='ISTYPE' class='form-control input-sm' value='{$arrs['ISTYPE']}' placeholder='ประเภทรถ' -->
						<select type='text' class='form-control input-sm' id='ISTYPE' >
							<option></option>
							<option value='1' ".($arrs['ISTYPE'] == 1 ? "selected":"").">กลุ่มครอบครัว</option>
							<option value='2' ".($arrs['ISTYPE'] == 2 ? "selected":"").">กลุ่มเอที</option>
							<option value='3' ".($arrs['ISTYPE'] == 3 ? "selected":"").">กลุ่มสปอร์ตเล็ก ,สปอร์ตใหญ่</option>
						</select>
					</div>
					<div class='col-sm-12'>
						รหัสผู้บันทึก
						<input type='text' id='INSBY' class='form-control input-sm' value='".$this->sess["IDNo"]."' placeholder='' disabled>
					</div>
					<div class='col-sm-12'>
						จากวันที
						<input type='text' id='StartDT' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' value='".$this->today('today')."' placeholder='' >
					</div>
					<div class='col-sm-12'>
						ถึงวันที่
						<input type='text' id='EndDT' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' value='' placeholder='' >
					</div>
					<div class='col-sm-6'>
						<BR><BR>
						<button id='btnsave' type='button' class='btn btn-primary' style='width:100%;'>
							<span class='glyphicon glyphicon-save'><b>บันทึก</b></span>
						</button>
					</div><div class='col-sm-6'>
						<BR><BR>
						<button id='btnclear' type='button' class=btn btn-defualt btn-block' style='width:100%;'>
							<span class='glyphicon glyphicon-refresh'><b>clear</b></span>
						</button>
						<button id='btndelete' type='button' class='btn btn-danger btn-block' style='width:100%;'>
							<span class='glyphicon glyphicon-trash'><b>ลบ</b></span>
						</button>
					</div>
				</div>
			</div>
		";
		$response = array('html'=>$html,'EVENT'=>$EVENT,'status'=>true);
		$response["StartDT"] = $arrs['StartDT'];
		$response["INSBY"]   = $arrs["INSBY"];
		$response["EndDT"]   = $arrs['EndDT'];
		echo json_encode($response);
	}
	function SaveSCPS(){
		$ID      = $_REQUEST['ID'];
		$STRNO   = $_REQUEST['STRNO'];
		$PRICE   = str_replace(",","",$_REQUEST['PRICE']);
		$ISTYPE  = $_REQUEST['ISTYPE'];
		$StartDT = $this->Convertdate(1,$_REQUEST['StartDT']);
		$EndDT   = $this->Convertdate(1,$_REQUEST['EndDT']);
		//$EndDT   = !empty($_REQUEST['EndDT']) ? $this->Convertdate(1,$_REQUEST['EndDT']) : "null";
		$INSBY   = $_REQUEST['INSBY'];
		
		if($STRNO ==""){
			$response["error"] = true;
			$response["msg"] = "กรุณากรอกเลขตัวถังรถก่อนครับ";
			echo json_encode($response); exit;
		}
		if($PRICE ==""){
			$response["error"] = true;
			$response["msg"] = "กรุณากรอกราคารถก่อนครับ";
			echo json_encode($response); exit;
		}
		if($ISTYPE ==""){
			$response["error"] = true;
			$response["msg"] = "กรุณากรอกประเภทรถก่อนครับ";
			echo json_encode($response); exit;
		}
		if($StartDT ==""){
			$response["error"] = true;
			$response["msg"] = "กรุณากรอกข้อมูลวันเริ่มต้นกำหนดก่อนครับ";
			echo json_encode($response); exit;
		}
		if($ID == "Auto Genarate"){
			$sql = "
				if OBJECT_ID('tempdb..#setcarprice') is not null drop table #setcarprice;
				create table #setcarprice (id varchar(1),msg varchar(max));
				begin tran scps
				begin try 
					declare @getid varchar(15) = (select MAX(ID)+1 from {$this->MAuth->getdb('STDSpecial')})
					declare @isstrno int = (
						select COUNT(*) from {$this->MAuth->getdb('STDSpecial')}
						where STRNO = '".$STRNO."'
					)
					if(@isstrno = 0)
					begin
						insert into {$this->MAuth->getdb('STDSpecial')}(
							[ID],[STRNO],[PRICE],[ISTYPE],[CONTNO],[StartDT]
							,[EndDT],[INSBY],[INSDT])
						values(
							@getid,'".$STRNO."','".$PRICE."','".$ISTYPE."',null,'".$StartDT."'
							,nullif('".$EndDT."',''),'".$INSBY."',getdate()
						)
						insert into #setcarprice select 'Y' as id,'สำเร็จ : บันทึกรายการกำหนดรถราคาพิเศษ ID :'+@getid+' เรียบร้อยแล้วครับ' as msg;
						commit tran scps;
					end
					else
					begin
						declare @startDT varchar(8) = (
							select convert(varchar(8),MAX(startDT),112) from {$this->MAuth->getdb('STDSpecial')} 
							where STRNO = '".$STRNO."'
						)
						declare @startIS varchar(8) = (
							'".$StartDT."'
						)
						if(@startIS > @startDT)
						begin
							declare @id varchar(20) = (
								select MAX(id) from {$this->MAuth->getdb('STDSpecial')} where STRNO = '".$STRNO."'
							)
							update {$this->MAuth->getdb('STDSpecial')} 
							set EndDT = DATEADD(DAY,-1,'".$StartDT."')
							where STRNO = '".$STRNO."' and id = @id
							
							insert into {$this->MAuth->getdb('STDSpecial')}(
								[ID],[STRNO],[PRICE],[ISTYPE],[CONTNO],[StartDT]
								,[EndDT],[INSBY],[INSDT])
							values(
								@getid,'".$STRNO."','".$PRICE."','".$ISTYPE."',null,'".$StartDT."'
								,nullif('".$EndDT."',''),'".$INSBY."',getdate()
							)
							insert into #setcarprice select 'Y' as id,'สำเร็จ : บันทึกรายการกำหนดรถราคาพิเศษ ID :'+@getid+' ปรับเปลี่ยราคารถ เลขถัง ".$STRNO."  ณ วันที่  ".$this->Convertdate(2,$StartDT)." เรียบร้อยแล้วครับ' as msg;
							commit tran scps;
						end
						else
						begin
							rollback tran scps;
							insert into #setcarprice select 'N' as id,'บันทึกข้อมูลไม่สำเร็จ : วันที่เริ่มซ้ำหรือน้อยกว่าวันที่เริ่มของสแตนดาร์ดเดิม' as msg;
							return;
						end
					end
				end try
				begin catch
					rollback tran scps;
					insert into #setcarprice select 'N' as id,'บันทึกข้อมูลไม่สำเร็จ : กรุณาติดต่อฝ่ายไอที' as msg;
					return;
				end catch
			";
			//echo $sql; exit;
			$this->db->query($sql);
		}else{
			$sql = "
				if OBJECT_ID('tempdb..#setcarprice') is not null drop table #setcarprice;
				create table #setcarprice (id varchar(1),msg varchar(max));
				begin tran scps
				begin try
					declare @isstrno int = (
						select COUNT(*) from {$this->MAuth->getdb('STDSpecial')}
						where ID = '".$ID."'
					)
					if(@isstrno = 1)
					begin	
						update {$this->MAuth->getdb('STDSpecial')} set STRNO = '".$STRNO."',PRICE = '".$PRICE."'
						,ISTYPE = '".$ISTYPE."',StartDT = '".$StartDT."',EndDT = nullif('".$EndDT."',''),INSBY = '".$INSBY."'
						where ID = '".$ID."'
						insert into #setcarprice select 'Y' as id,'สำเร็จ : แก้ไขรายการกำหนดรถราคาพิเศษ ID :".$ID." เรียบร้อยแล้วครับ' as msg;
						commit tran scps;
					end
					else
					begin
						rollback tran scps;
						insert into #setcarprice select 'N' as id,'ไม่พบข้อมูล : โปรดตรวจสอบข้อมูลอีกครั้ง' as msg;
						return;
					end
				end try		
				begin catch
					rollback tran scps;
					insert into #setcarprice select 'N' as id,'บันทึกข้อมูลไม่สำเร็จ : กรุณาติดต่อฝ่ายไอที' as msg;
					return;
				end catch
			";
			//echo $sql; exit;
			$this->db->query($sql);
		}
		$sql = "
			select * from #setcarprice
		";
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$response['status'] = ($row->id == "Y" ? true:false);
				$response['msg']    = $row->msg;
			}
		}else{
			$response['status'] = false;
			$response['msg']    = "ผิดพลาด";
		}
		echo json_encode($response);
	}
	function DelSCPS(){
		$ID = $_REQUEST['ID'];
		
		$sql = "
			if OBJECT_ID('tempdb..#delcarprice') is not null drop table #delcarprice;
			create table #delcarprice (id varchar(1),msg varchar(max));
			begin tran scps
			begin try
				declare @isstrno int = (
					select COUNT(*) from {$this->MAuth->getdb('STDSpecial')}
					where ID = '".$ID."'
				)
				if(@isstrno = 1)
				begin	
					delete from {$this->MAuth->getdb('STDSpecial')} where ID = '".$ID."'
					
					insert into #delcarprice select 'Y' as id,'สำเร็จ : ลบรายการกำหนดรถราคาพิเศษ ID :".$ID." เรียบร้อยแล้วครับ' as msg;
					commit tran scps;
				end
				else
				begin
					rollback tran scps;
					insert into #delcarprice select 'N' as id,'ไม่พบข้อมูล : โปรดตรวจสอบข้อมูลอีกครั้ง' as msg;
					return;
				end
			end try		
			begin catch
				rollback tran scps;
				insert into #delcarprice select 'N' as id,'บันทึกข้อมูลไม่สำเร็จ : กรุณาติดต่อฝ่ายไอที' as msg;
				return;
			end catch
		";
		//echo $sql; exit;
		$this->db->query($sql);
		$sql = "
			select * from #delcarprice
		";
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$response['status'] = ($row->id == "Y" ? true:false);
				$response['msg']    = $row->msg;
			}
		}else{
			$response['status'] = false;
			$response['msg']    = "ผิดพลาด";
		}
		echo json_encode($response);
	}
	function import_setprice(){
		$this->load->library('excel');
		
		$file = $_FILES["myfile"]["tmp_name"];
		
		//read file from path อ่านไฟล์จากที่มา
		$objPHPExcel = PHPExcel_IOFactory::load($file);
		
		//X ตรวจสอบว่ามีกี่ sheet
		//X $sheetCount = $objPHPExcel->getSheetCount();
		//X จะดึงข้อมูลแค่ sheet 1 เท่านั้น
		$sheetCount = 1;
		for($sheetIndex = 0; $sheetIndex < $sheetCount; $sheetIndex++){
			$objPHPExcel->setActiveSheetIndex($sheetIndex);
			//get only the Cell Collection
			$cell_collection = $objPHPExcel->getActiveSheet()->getCellCollection();
			
			$arrs = array("now"=>1,"old"=>1);
			//extract to a PHP readable array format
			foreach($cell_collection as $cell){
				$column     = $objPHPExcel->getActiveSheet()->getCell($cell)->getColumn();
				$row        = $objPHPExcel->getActiveSheet()->getCell($cell)->getRow();
				$data_value = $objPHPExcel->getActiveSheet()->getCell($cell)->getCalculatedValue();	
				//echo $data_value; exit;
			
				if($arrs["old"] == 1){
					$arrs["now"] = 1;
				}else if($arrs["old"] == $row){
					$arrs["now"] = $arrs["now"];
				}else{
					$arrs["now"] +=1;
				}
				
				//The header will/should be in row 1 only. of course, this can be modified to suit your need.
				//ส่วนหัวอยู่แถวที่ 1 เท่านั้น
				if($row == 1 and $sheetIndex == 0){
					$header[$row][$column] = $data_value;
				}else {
					switch($column){
						case 'H': $arr_data[$arrs["now"]][$column] = $this->Convertdate(2,$data_value); break;
						case 'G': $arr_data[$arrs["now"]][$column] = number_format($data_value,2); break;
						default : $arr_data[$arrs["now"]][$column] = $data_value; break;
					}
				}
				
				$arrs["old"] = $row;
			}
		}
		$datasize = sizeof($arr_data);
		$html = "";
		for($i=1;$i<=$datasize;$i++){
			$sql = "
				select case when STRNO <> '' then 'จองแล้ว' end as status
				from {$this->MAuth->getdb('ARRESV')} where STRNO = '".$arr_data[$i]["F"]."' 
				union
				select case when STRNO <> '' then 'ขายผ่อน' end as status
				from {$this->MAuth->getdb('ARMAST')} where STRNO = '".$arr_data[$i]["F"]."' 
			";
			//echo $sql; exit;
			$query = $this->db->query($sql);
			$status = ""; $color = "";
			if($query->row()){
				foreach($query->result() as $row){
					$status = $row->status;
					$color = "style='background-color:#F1948A;'";
				}
			}else{
				$status = "ยังไม่ขาย";
				$color  = "";
			}
			$html .="
				<tr class='listSpecial' {$color}
					STRNO = '".$arr_data[$i]["F"]."' PRICE = '".$arr_data[$i]["G"]."'
					STARTDT = '".$arr_data[$i]["H"]."' MODEL = '".$arr_data[$i]["B"]."' 
					IDKEY = '".$arr_data[$i]["A"]."'
				>
					<td>".$arr_data[$i]["A"]."</td>
					<td>".$arr_data[$i]["B"]."</td>
					<td>".$arr_data[$i]["C"]."</td>
					<td>".$arr_data[$i]["D"]."</td>
					<td>".$arr_data[$i]["E"]."</td>
					<td>".$arr_data[$i]["F"]."</td>
					<td>".$arr_data[$i]["G"]."</td>
					<td>".$arr_data[$i]["H"]."</td>
					<td>".$status."</td>
				</tr>
			";
		}
		$html = "
			<div style='width:100%;height:calc(100% - 30px);overflow:auto;'>
				<table border=1 style='border-collapse:collapse;width:100%;'>
					<thead>
						<tr>
							<th>ลำดับ</th>
							<th>รุ่น</th>
							<th>แบบ</th>
							<th>สี</th>
							<th>ขนาด</th>
							<th>เลขตัวถัง</th>
							<th>ราคา</th>
							<th>ณ วันที่</th>
							<th>สถานะรถ</th>
						</tr>
					</thead>
					<tbody>
						{$html}
					</tbody>
				</table>
			</div>
			<div style='width:100%;height:30px;padding-top:10px;'>
				<div class='col-sm-2 col-sm-offset-10'>
					<button id='std_import'  class='btn btn-xs btn-primary btn-block'><span class='glyphicon glyphicon-import'> นำเข้า</span></button>
				</div>
			</div>
		";
		$response = array();
		$response["html"] 	  = $html;
		echo json_encode($response);
	}
	function import_save(){
		$LISTPS = $_REQUEST["LISTPS"];
		//print_r($LISTPS); exit;
		
		$sql = "select max(ID)+1 as idmax from {$this->MAuth->getdb('STDSpecial')}";
		$idmax = $this->db->query($sql);
		$idmax = $idmax->row();
		$idmax = $idmax->idmax;
		
		$insertdb = ""; $upenddt = ""; $insertTemp = ""; $startDT = ""; 
		$datasize = sizeof($LISTPS);
		for($i=0;$i<$datasize;$i++){
			$startDT = $this->Convertdate(1,$LISTPS[$i][2]);
			$sql = "
				select count(*) as strno from {$this->MAuth->getdb('STDSpecial')}
				where STRNO = '".$LISTPS[$i][0]."'
			";
			//echo $sql; exit;
			$query = $this->db->query($sql);
			if($query->row()){
				foreach($query->result() as $row){
					if($row->strno > 0){
						$upenddt .="
							update {$this->MAuth->getdb('STDSpecial')}
							set EndDT = DATEADD(DAY,-1,'".$this->Convertdate(1,$LISTPS[$i][2])."')
							where STRNO = '".$LISTPS[$i][0]."' and id in (
								select MAX(id) 
								from {$this->MAuth->getdb('STDSpecial')} where STRNO = '".$LISTPS[$i][0]."'
							)
						";
					}
				}
			}
			$numberid = $idmax + $i;
			$sql = "
				select 
				case		
					when '".$LISTPS[$i][3]."' like 'AFS%' then 1 
					when '".$LISTPS[$i][3]."' like 'NBC%' then 1
					when '".$LISTPS[$i][3]."' like 'CT%'  then 1
					
					when '".$LISTPS[$i][3]."' like 'ACB%' then 2 
					when '".$LISTPS[$i][3]."' like 'NCF%' then 2
					when '".$LISTPS[$i][3]."' like 'ACF%' then 2
					when '".$LISTPS[$i][3]."' like 'WW%'  then 2 
					when '".$LISTPS[$i][3]."' like 'NSS%' then 2
					when '".$LISTPS[$i][3]."' like 'ADV%' then 2
					when '".$LISTPS[$i][3]."' like 'ACG%' then 2
					
					when '".$LISTPS[$i][3]."' like 'MSX%' then 3
					when '".$LISTPS[$i][3]."' like 'CB%'  then 3 
					
					when '".$LISTPS[$i][3]."' like 'CBF%' then 3
					when '".$LISTPS[$i][3]."' like 'CBR%' then 3 
					when '".$LISTPS[$i][3]."' like 'CMX%' then 3
					when '".$LISTPS[$i][3]."' like 'CRF%' then 3
					else 0
				end	as ISTYPE
			";
			$query = $this->db->query($sql);
			if($query->row()){
				foreach($query->result() as $row){
					$insertTemp .="
						insert into #checkspecial(
							IDKEY,ID,STRNO,PRICE,ISTYPE,StartDT
						)values(
							".$LISTPS[$i][4].",".$numberid.",'".$LISTPS[$i][0]."','".str_replace(",","",$LISTPS[$i][1])."'
							,{$row->ISTYPE},'".$this->Convertdate(1,$LISTPS [$i][2])."'
						)
					";
					
					$insertdb .="
						insert into {$this->MAuth->getdb('STDSpecial')}(
							[ID],[STRNO],[PRICE],[ISTYPE],[CONTNO]
							,[StartDT],[EndDT],[INSBY],[INSDT]
						)values(
							".$numberid.",'".$LISTPS[$i][0]."','".str_replace(",","",$LISTPS[$i][1])."'
							,{$row->ISTYPE},null,'".$this->Convertdate(1,$LISTPS [$i][2])."',null
							,'{$this->sess["IDNo"]}',getdate()
						) 
					";
				}
			}
		}
		//echo $startDT; exit;
		$sql = "
			if OBJECT_ID('tempdb..#checkspecial') is not null drop table #checkspecial
			create table #checkspecial (
				IDKEY int,ID int,STRNO varchar(20),PRICE decimal(12,2)
				,ISTYPE int,StartDT varchar(8)
			); 
			".$insertTemp."
		";
		//echo $sql; 
 		$this->db->query($sql);
		
		$sql = "
			select distinct b.IDKEY from {$this->MAuth->getdb('STDSpecial')} a
			inner join #checkspecial b on a.STRNO = b.STRNO collate Thai_CI_AS
			where a.StartDT is not null 
			and a.StartDT >= b.StartDT
			order by b.IDKEY
		";
		//echo $sql; exit;
		$response = array("error"=>false,"msg"=>array());
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$response['error']  = true;
				$response['msg1']   = "กำหนดวันที่ไม่ถูกต้อง : วันที่เริ่มซ้ำหรือน้อยกว่าวันที่เริ่มของสแตนดาร์ดเดิม";
				$response['msg2'][] = "ลำดับที่ ".$row->IDKEY."";
			}
			echo json_encode($response); exit;
		}
		
		$sql = "
			if OBJECT_ID('tempdb..#resultimport') is not null drop table #resultimport;
			create table #resultimport (id varchar(1),msg varchar(max));
			begin tran importstd
			begin try
				".$upenddt."
				".$insertdb."
				insert into #resultimport select 'Y' as id,'สำเร็จ : บันทึกรายการกำหนดรถราคาพิเศษ ' as msg;
				commit tran importstd;			
			end try
			begin catch
				rollback tran importstd
				insert into #resultimport select 'N' as id,'บันทึกข้อมูลไม่สำเร็จ : กรุณาติดต่อฝ่ายไอที' as msg;
				return;
			end catch
		";
		//echo $sql; exit;
		$this->db->query($sql);
		
		$sql = "select * from #resultimport";
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$response['status'] = ($row->id == "Y" ? true:false);
				$response['msg']    = $row->msg;
			}
		}else{
			$response['status'] = false;
			$response['msg']    = "ผิดพลาด";
		}
		echo json_encode($response);
	}
}