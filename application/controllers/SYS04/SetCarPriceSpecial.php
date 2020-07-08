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
						<div class='col-sm-2'>	
							<div class='form-group'>
								รหัส
								<input type='text' id='IDS' class='form-control input-sm' placeholder='รหัส' >
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								เลขตัวถัง
								<input type='text' id='STRNOS' class='form-control input-sm' placeholder='เลขถัง' >
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								ราคา
								<input type='text' id='PRICES' class='form-control input-sm' placeholder='ราคา' >
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								ประเภทรถ
								<input type='text' id='ISTYPES' class='form-control input-sm' placeholder='ประเภทรถ' >
							</div>
						</div>
						<div class='col-sm-2'>
							<div class='form-group'>
								<br>
								<button id='btnsearch' type='button' class='btn btn-primary' style='width:100%;'>
									<span class='fa fa-search'><b>ค้นหา</b></span>
								</button>
							</div>
						</div>
						<div class='col-sm-2'>
							<div class='form-group'>
								<br>
								<button id='btninsert' type='button' class='btn btn-cyan' style='width:100%;'>
									<span class='glyphicon glyphicon-pencil'><b>เพิ่ม</b></span>
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
		$PRICE  =  $_REQUEST['PRICE'];
		$ISTYPE = $_REQUEST['ISTYPE'];
		$cond = "";
		if($ID !== ""){
			$cond .= "and ID like '".$ID."%'";
		}
		if($STRNO !== ""){
			$cond .= "and STRNO like '".$STRNO."%'";
		}
		if($PRICE !== ""){
			$cond .= "and PRICE like '".$PRICE."%'";
		}
		if($ISTYPE !== ""){
			$cond .= "and ISTYPE like '".$ISTYPE."%'";
		}
		$html = "";
		$sql = "
			select ".($cond == "" ? "top 20":"")." ID,STRNO,PRICE,ISTYPE,case when CONTNO IS NULL then 'ยังไม่ขาย' else 'ขายแล้ว' end as STATUSPRICE
			,convert(varchar(8),StartDT,112) StartDT,convert(varchar(8),EndDT,112) as EndDT,INSBY
			,convert(varchar(8),INSDT,112) INSDT 
			from {$this->MAuth->getdb('STDSpecial')} where 1=1 ".$cond."
		";
		//echo $sql; exit;
		$NRow = 1;
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				if($row->STATUSPRICE == "ยังไม่ขาย"){
					$colors = "color:green;";
					$rowc = "";
					$status = "";
				}else{
					$colors = "color:red;";
					$rowc = "color:red;";
					$status = "sale";
				}
				$html .="
					<tr style='{$rowc}' class='trow' seq=".$NRow.">
						<td class='getit IDClick' seq=".$NRow++." STRNO='".$row->STRNO."' STATUS = '".$row->STATUSPRICE."'
						style='cursor: pointer; text-align: center; background-color: rgb(255, 255, 255);'>
						<b>เลือก</b></td>
						<td>".$row->ID."</td>
						<td>".$row->STRNO."</td>
						<td>".number_format($row->PRICE,2)."</td>
						<td>".$row->ISTYPE."</td>
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
								เงื่อนไข
							</td>
						</tr>
						<tr>
							<th style='vertical-align:middle;'>#</th>
							<th style='vertical-align:middle;'>id</th>
							<th style='vertical-align:middle;'>เลขตัวถัง</th>
							<th style='vertical-align:middle;'>ราคา</th>
							<th style='vertical-align:middle;'>ประเภทรถ</th>
							<th style='vertical-align:middle;'>สถานะการขาย</th>
							<th style='vertical-align:middle;'>INSBY</th>
							<th style='vertical-align:middle;'>StartDT</th>
							<th style='vertical-align:middle;'>EndDT</th>
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
		$STRNO = $_REQUEST['STRNO'];
		$EVENT = $_REQUEST['EVENT'];
		$arrs = array();
		$arrs['ID']     = "Auto Genarate";
		$arrs['STRNO']  = "";
		$arrs['PRICE']  = "";
		$arrs['ISTYPE'] = "";
		$arrs['StartDT']= $this->today('today');
		$arrs['EndDT']  = $this->today('today');
		$arrs['INSBY']  = "";
		$sql = "
			select ID,STRNO,PRICE,ISTYPE,CONTNO
			,convert(varchar(8),StartDT,112) StartDT,convert(varchar(8),EndDT,112) as EndDT
			,INSBY,convert(varchar(8),INSDT,112) INSDT 
			from {$this->MAuth->getdb('STDSpecial')} where STRNO = '".$STRNO."'
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
						ประเภทรถ
						<input type='text' id='ISTYPE' class='form-control input-sm' value='{$arrs['ISTYPE']}' placeholder='ประเภทรถ' >
					</div>
					<div class='col-sm-12'>
						INSBY
						<input type='text' id='INSBY' class='form-control input-sm' value='{$arrs['INSBY']}' placeholder='' >
					</div>
					<div class='col-sm-12'>
						StartDT
						<input type='text' id='StartDT' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' value='{$arrs['StartDT']}' placeholder='' >
					</div>
					<div class='col-sm-12'>
						EndDT
						<input type='text' id='EndDT' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' value='{$arrs['EndDT']}' placeholder='' >
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
		echo json_encode($response);
	}
	function SaveSCPS(){
		$ID      = $_REQUEST['ID'];
		$STRNO   = $_REQUEST['STRNO'];
		$PRICE   = str_replace(",","",$_REQUEST['PRICE']);
		$ISTYPE  = $_REQUEST['ISTYPE'];
		$StartDT = $this->Convertdate(1,$_REQUEST['StartDT']);
		$EndDT   = $this->Convertdate(1,$_REQUEST['EndDT']);
		$INSBY   = $_REQUEST['INSBY'];
		
		if($STRNO ==""){
			$response["error"] = true;
			$response["msg"] = "กรุณากรอกเลขตัวถังรถด้วยครับ";
			echo json_encode($response); exit;
		}
		if($PRICE ==""){
			$response["error"] = true;
			$response["msg"] = "กรุณากรอกราคารถด้วยครับ";
			echo json_encode($response); exit;
		}
		if($ISTYPE ==""){
			$response["error"] = true;
			$response["msg"] = "กรุณากรอกประเภทรถด้วยครับ";
			echo json_encode($response); exit;
		}
		if($StartDT ==""){
			$response["error"] = true;
			$response["msg"] = "กรุณากรอกข้อมูลให้ครบถ้วนด้วยครับ";
			echo json_encode($response); exit;
		}
		if($EndDT ==""){
			$response["error"] = true;
			$response["msg"] = "กรุณากรอกข้อมูลให้ครบถ้วนด้วยครับ";
			echo json_encode($response); exit;
		}
		if($INSBY ==""){
			$response["error"] = true;
			$response["msg"] = "กรุณากรอกข้อมูลให้ครบถ้วนด้วยครับ";
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
							,'".$EndDT."','".$INSBY."',getdate()
						)
						insert into #setcarprice select 'Y' as id,'สำเร็จ : บันทึกรายการกำหนดรถราคาพิเศษ ID :'+@getid+' เรียบร้อยแล้วครับ' as msg;
						commit tran scps;
					end
					else
					begin
						rollback tran scps;
						insert into #setcarprice select 'N' as id,'ไม่บันทึกข้อมูล : มีรหัสเลขตัวถัง : ".$STRNO." นี้อยู่แล้ว' as msg;
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
						,ISTYPE = '".$ISTYPE."',StartDT = '".$StartDT."',EndDT = '".$EndDT."',INSBY = '".$INSBY."'
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
}