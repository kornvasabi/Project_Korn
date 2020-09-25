<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/********************************************************
             ______@26/12/2019______
            / / _ _   _ _     __ 
           / // __ \ / __ \ / __ \
       _ _/ // /_/ // / / // /_/ /
     /_ _ _/ \_ _ //_/ /_/ \__  /
                          _ _/ /
                         /___ /
********************************************************/
class StandardSHC extends MY_Controller {
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
		
		$this->load->model('MMAIN');
	}
	
	function index(){
		$claim = $this->MLogin->getclaim(uri_string());
		if($claim['m_access'] != "T"){ echo "<div align='center' style='color:red;font-size:16pt;width:100%;'>ขออภัย คุณยังไม่มีสิทธิเข้าใช้งานหน้านี้ครับ</div>"; exit; }
		
		$html = "
			<div class='tab1' name='home' locat='{$this->sess['branch']}' is_mobile='{$this->sess['is_mobile']}' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}' style='height:calc(100vh - 132px);overflow:auto;background-color:white;'>
				<div class='col-sm-12' >
					<div class='row'>
						<div class='col-sm-2'>	
							<div class='form-group'>
								รุ่น
								<input type='text' id='search_model' class='form-control input-sm' placeholder='รุ่น' >
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								แบบ
								<input type='text' id='search_baab' class='form-control input-sm' placeholder='แบบ' >
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								สี
								<input type='text' id='search_color' class='form-control input-sm' placeholder='สี' >
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								ปีรถ
								<input type='text' id='search_manuyr' class='form-control input-sm' placeholder='ปีรถ' value=''>
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								กลุ่มรถ
								<input type='text' id='search_gcode' class='form-control input-sm' placeholder='กลุ่มรถ' value=''>
							</div>
						</div>
						
						<div class='col-sm-2'>	
							<div class='form-group'>
								สาขา
								<input type='text' id='search_locat' class='form-control input-sm' placeholder='สาขา' value=''>
							</div>
						</div>
					</div>
					<div class='row'>
						<div class='col-sm-4'>	
							<div class='form-group'>
								<button id='btnt1import' class='btn btn-warning btn-block'><span class='glyphicon glyphicon-import'> นำเข้า</span></button>
							</div>
						</div>
						<div class='col-sm-4'>	
							<div class='form-group'>
								<button id='btnt1createStd' class='btn btn-cyan btn-block'><span class='glyphicon glyphicon-pencil'> สร้าง</span></button>
							</div>
						</div>
						<div class='col-sm-4'>	
							<div class='form-group'>
								<button id='btnt1search' class='btn btn-primary btn-block'><span class='glyphicon glyphicon-search'> ค้นหา</span></button>
							</div>
						</div>
					</div>
				</div>
			</div>
		";
		
		$html.= "<script src='".base_url('public/js/SYS04/StandardSHC.js')."'></script>";
		echo $html;
	}
	
	function loadform(){
		$arrs = array();
		
		if($_POST["event"] == "add"){
			/*
			$arrs["shc_model"] 	= $this->MMAIN->Option_get_model(array("ND125"));
			$arrs["shc_baab"] 	= $this->MMAIN->Option_get_baab(array('model'=>"ND125",'baab'=>array("A")));
			$arrs["shc_manuyr"] = '2530';
			$arrs["shc_gcode"] 	= $this->MMAIN->Option_get_groupcode(array('G021'));
			$arrs["shc_nprice"] = '50000';
			$arrs["shc_oprice"] = '30000';
			$arrs["shc_color"] 	= $this->MMAIN->Option_get_color(array(),"ND125","A");
			$arrs["shc_locat"]	= $this->MMAIN->Option_get_locat(array());
			*/
			$arrs["shc_type"] 	= $this->MMAIN->Option_get_type(array());
			$arrs["shc_model"] 	= $this->MMAIN->Option_get_model(array());
			$arrs["shc_baab"] 	= $this->MMAIN->Option_get_baab(array('model'=>"",'baab'=>array()));
			$arrs["shc_manuyr"] = '';
			$arrs["shc_gcode"] 	= $this->MMAIN->Option_get_groupcode(array());
			$arrs["shc_nprice"] = '';
			$arrs["shc_oprice"] = '';
			$arrs["shc_color"] 	= $this->MMAIN->Option_get_color(array(),"","");
			$arrs["shc_locat"]	= $this->MMAIN->Option_get_locat(array());
		}else{
			$sql = "
				select * 
					,STUFF((
						SELECT ',' + CONVERT(NVARCHAR(20), cc.COLOR) 
						FROM {$this->MAuth->getdb('STDSHCARColors')} cc 
						where a.ID=cc.ID FOR xml path('')
					 ), 1, 1, '') as COLOR
					,STUFF((
						SELECT ',' + CONVERT(NVARCHAR(20), dd.LOCAT) 
						FROM {$this->MAuth->getdb('STDSHCARLocats')} dd 
						where a.ID=dd.ID FOR xml path('')
					 ), 1, 1, '') as LOCAT
				from {$this->MAuth->getdb('STDSHCAR')} a
				left join {$this->MAuth->getdb('STDSHCARDetails')} b on a.ID=b.ID and b.ACTIVE='yes'
				where a.ID='{$_POST["ID"]}'
			";
			//echo $sql; exit;
			$query = $this->db->query($sql);
			
			if($query->row()){
				foreach($query->result() as $row){
					$arrs["shc_type"] 	= $this->MMAIN->Option_get_model(array($row->TYPECOD));
					$arrs["shc_model"] 	= $this->MMAIN->Option_get_model(array($row->MODEL));
					$arrs["shc_baab"] 	= $this->MMAIN->Option_get_baab(array('model'=>$row->MODEL,'baab'=>array($row->BAAB)));
					$arrs["shc_manuyr"] = $row->MANUYR;
					$arrs["shc_gcode"] 	= $this->MMAIN->Option_get_groupcode(array('G'.$row->GCODE));
					$arrs["shc_nprice"] = $row->NPRICE;
					$arrs["shc_oprice"] = $row->OPRICE;
					$arrs["shc_color"] 	= $this->MMAIN->Option_get_color($row->COLOR == "" ? array():explode(",",$row->COLOR),$row->MODEL,$row->BAAB);
					$arrs["shc_locat"]	= $this->MMAIN->Option_get_locat($row->LOCAT == "" ? array():explode(",",$row->LOCAT));
				}
			}
		}
		//print_r($arrs["shc_baab"]); exit;
		
		$html = "
			<div class='col-sm-12 col-md-12 col-lg-8 col-lg-offset-2'>
				<div class='row'>
					<div class='col-sm-3'>
						<div class='form-group'>
							ยี่ห้อ
							<select id='shc_type' class='form-control'>{$arrs["shc_type"]}</select>
						</div>
					</div>
					<div class='col-sm-3'>
						<div class='form-group'>
							รุ่น
							<select id='shc_model' class='form-control'>{$arrs["shc_model"]}</select>
						</div>
					</div>
					<div class='col-sm-3 col-sm-offset-3'>
						<div class='form-group'>
							แบบ
							<select id='shc_baab' class='form-control'>{$arrs["shc_baab"]}</select>
						</div>
					</div>
				</div>
				<div class='row' style='height:calc(100vh - 205px);border:0px solid red;overflow:auto;'>	
					<div class='col-sm-6'>
						<div class='form-group'>
							สี
							<select id='shc_color' multiple='multiple' size='10' name='duallistbox_demo1[]'>{$arrs["shc_color"]}</select>
						</div>
					</div>			
					<div class='col-sm-6'>
						<div class='form-group'>
							สาขา
							<select id='shc_locat' multiple='multiple' size='10' name='duallistbox_demo1[]'>{$arrs["shc_locat"]}</select>
						</div>
					</div>
					
					<div class='col-sm-3'>
						<div class='form-group'>
							ปี
							<input type='text' id='shc_manuyr' class='form-control' value='{$arrs["shc_manuyr"]}'>
						</div>
					</div>
					<div class='col-sm-3'>
						<div class='form-group'>
							กลุ่มรถ
							<select id='shc_gcode' class='form-control'>{$arrs["shc_gcode"]}</select>
						</div>
					</div>
					<div class='col-sm-3'>
						<div class='form-group'>
							ราคารถใหม่
							<input type='text' id='shc_nprice' class='form-control' value='{$arrs["shc_nprice"]}'>
						</div>
					</div>
					<div class='col-sm-3'>
						<div class='form-group'>
							ราคามือสอง
							<input type='text' id='shc_oprice' class='form-control' value='{$arrs["shc_oprice"]}'>
						</div>
					</div>
						
				</div>
				<div class='row'>
					<div class='col-sm-2'>	
						<div class='form-group'>
							<button id='btn_close' stdid='".(isset($_POST["ID"])?$_POST["ID"]:'')."' class='btn btn-xs btn-danger btn-block' ><span class='glyphicon glyphicon-edit'> ยกเลิก</span></button>
						</div>
					</div>
					<div class='col-sm-2 col-sm-offset-8'>	
						<div class='form-group'>
							<button id='btn_save' stdid='".(isset($_POST["ID"])?$_POST["ID"]:'')."' class='btn btn-xs btn-primary btn-block' ><span class='glyphicon glyphicon-floppy-disk'> บันทึก</span></button>
						</div>
					</div>
				</div>
			</div>
		";
		
		$response = array('html'=>$html,'status'=>true);
		echo json_encode($response);
	}
	
	public function stdshcFormUPLOAD(){
		$html = "
			<div class='row'>
				<input type='button' id='form_import' class='btn btn-info btn-sm' style='width:100%;' value='ดาวน์โหลดฟอร์มนำเข้า'>
			</div><hr>
			<div class='row'>
				<div id='form_stdshc'></div>
			</div>
		";
		
		$response = array("html"=>$html);
		echo json_encode($response);
	}
	
	function search(){
		$model 	= $_POST["model"];
		$baab 	= $_POST["baab"];
		$color 	= $_POST["color"];
		$manuyr = $_POST["manuyr"];
		$gcode 	= $_POST["gcode"];
		$locat 	= $_POST["locat"];
		
		$cond = "";
		$cond_color = "";
		$cond_locat = "";
		if($model != ""){ 	$cond .= " and MODEL like '{$model}%'"; }
		if($baab != ""){ 	$cond .= " and BAAB like '{$baab}%'"; }
		if($color != ""){ 	$cond .= " and COLOR like '{$color}%'"; }
		if($manuyr != ""){ 	$cond .= " and MANUYR like '{$manuyr}%'"; }
		if($gcode != ""){ 	$cond .= " and GCODE like '{$gcode}%'"; }
		if($locat != ""){ 	$cond .= " and LOCAT like '{$locat}%'"; }
	
		$sql = "
			select * from (
				select a.ID,a.MODEL,a.BAAB
					,STUFF((
						SELECT ',' + CONVERT(NVARCHAR(20), cc.COLOR) 
						FROM {$this->MAuth->getdb('STDSHCARColors')} cc 
						where a.ID=cc.ID FOR xml path('')
					 ), 1, 1, '') as COLOR
					,a.MANUYR,a.GCODE
					,'('+c.GCODE+') '+c.GDESC as GDESC
					,STUFF((
						SELECT ',' + CONVERT(NVARCHAR(20), dd.LOCAT) 
						FROM {$this->MAuth->getdb('STDSHCARLocats')} dd 
						where a.ID=dd.ID FOR xml path('')
					 ), 1, 1, '') as LOCAT
					,b.NPRICE
					,b.OPRICE
				from {$this->MAuth->getdb('STDSHCAR')} a
				left join {$this->MAuth->getdb('STDSHCARDetails')} b on a.ID=b.ID and b.ACTIVE='Yes'
				left join {$this->MAuth->getdb('SETGROUP')} c on a.GCODE=c.GCODE collate thai_cs_as
				where a.ACTIVE='Yes'
			) as data
			where 1=1 {$cond}
			order by ID
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$html.= "
					<tr style='vertical-align:text-top;'>
						<td>
							<button stdid='".$row->ID."' class='stdshc_edit btn btn-xs btn-warning' >
								<span class='glyphicon glyphicon-edit'> แก้ไข</span>
							</button>
						</td>
						<td>".$row->ID."</td>
						<td>".$row->MODEL."</td>
						<td>".$row->BAAB."</td>
						<td><div style='max-height:150px;overflow:auto;'>".str_replace(",","<br>",$row->COLOR)."</div></td>
						<td><div style='max-height:150px;overflow:auto;'>".str_replace(",","<br>",$row->LOCAT)."</div></td>
						<td>".$row->MANUYR."</td>
						<td>".$row->GDESC."</td>
						<td align='right'>".number_format($row->NPRICE,2)."</td>
						<td align='right'>".number_format($row->OPRICE,2)."</td>
					</tr>
				";
			}
		}
		
		$html = "
			<div>
				<table id='table_stdshc_search' border=1 style='border-collapse:collapse;width:100%;'>
					<thead style='background-color:yellow;'>
						<tr>
							<th>#</th>
							<th>สแตนดาร์ด</th>
							<th>รุ่น</th>
							<th>แบบ</th>
							<th>สี</th>
							<th>สาขา</th>
							<th>ปีรถ</th>
							<th>กลุ่มรถ</th>
							<th>ราคารถใหม่</th>
							<th>ราคามือสอง</th>
						</tr>
					</thead>
					<tbody>{$html}</tbody>
				</table>
			</div>
		";
		
		$response = array('html'=>$html,'status'=>true);
		echo json_encode($response);
	}
	
	function SHC_Save(){
		$stdid 	= $_POST["stdid"];
		$type 	= $_POST["type"];
		$model 	= $_POST["model"];
		$baab 	= $_POST["baab"];
		$year 	= $_POST["manuyr"];
		$gcode 	= $_POST["gcode"];
		$nprice = $_POST["nprice"];
		$oprice = $_POST["oprice"];
		$color 	= (is_array($_POST["color"]) ? implode(",",$_POST["color"]) : "ALL");
		$locat 	= (is_array($_POST["locat"]) ? implode(",",$_POST["locat"]) : "ALL");
		$event = $_POST["event"];
		
		$response = array();
		if($type == ""){
			$response["error"] 	  = true;
			$response["errorMsg"] = "ผิดพลาด คุณยังไม่ได้ระบุยี่ห้อ";
			echo json_encode($response); exit;
		}
		
		if($model == ""){
			$response["error"] 	  = true;
			$response["errorMsg"] = "ผิดพลาด คุณยังไม่ได้ระบุรุ่น";
			echo json_encode($response); exit;
		}
		
		if($baab == ""){
			$response["error"] 	  = true;
			$response["errorMsg"] = "ผิดพลาด คุณยังไม่ได้ระบุแบบ";
			echo json_encode($response); exit;
		}
		
		if($year == ""){
			$response["error"] 	  = true;
			$response["errorMsg"] = "ผิดพลาด คุณยังไม่ได้ระบุปีรถ";
			echo json_encode($response); exit;
		}
		
		if($gcode == ""){
			$response["error"] 	  = true;
			$response["errorMsg"] = "ผิดพลาด กลุ่มรถ";
			echo json_encode($response); exit;
		}
		
		if($nprice == ""){
			$response["error"] 	  = true;
			$response["errorMsg"] = "ผิดพลาด คุณยังไม่ได้ระบุราคารถใหม่";
			echo json_encode($response); exit;
		}
		
		if($oprice == ""){
			$response["error"] 	  = true;
			$response["errorMsg"] = "ผิดพลาด คุณยังไม่ได้ราคามือสอง";
			echo json_encode($response); exit;
		}
		
		if($event == "add"){
			$sql = "
				if object_id('tempdb..#tempResult') is not null drop table #tempResult;
				create table #tempResult (error varchar(1),msg varchar(max));
				
				begin tran mpstdshc
				begin try
					declare @ID bigint,@COLORcnt int,@LOCATcnt int,@seem int = 0;
					declare @datetime datetime = getdate();
					
					declare Datatable cursor for
					select ID from {$this->MAuth->getdb('STDSHCAR')}
					where TYPECOD='{$type}' and MODEL='{$model}' 
						and BAAB='{$baab}' and MANUYR='{$year}' 
						and GCODE='{$gcode}' and ACTIVE='Yes'
					
					open Datatable
					fetch next from Datatable into @ID;
					
					while @@FETCH_STATUS = 0
					begin
						set @COLORcnt = (
							select count(*) from {$this->MAuth->getdb('STDSHCARColors')} b 
							where b.ID=@ID and b.COLOR in ('".($color == "" ? "ALL":str_replace(",","','",$color))."')
						);
						
						set @LOCATcnt = (
							select count(*) from {$this->MAuth->getdb('STDSHCARLocats')} b 
							where b.ID=@ID and b.LOCAT in ('".($locat == "" ? "ALL":str_replace(",","','",$locat))."')
						);
						
						if(isnull(@COLORcnt,0) > 0 and isnull(@LOCATcnt,0) > 0)
						begin
							set @seem += 1;							
						end
						
						set @COLORcnt = null;
						set @LOCATcnt = null;
						fetch next from Datatable into @ID;
					end
					
					set @COLORcnt = (
						select count(*) from {$this->MAuth->getdb('STDSHCARColors')} b 
						where b.ID=@ID and b.COLOR in ('".($color == "" ? "ALL":str_replace(",","','",$color))."')
					);
					
					set @LOCATcnt = (
						select count(*) from {$this->MAuth->getdb('STDSHCARLocats')} b 
						where b.ID=@ID and b.LOCAT in ('".($locat == "" ? "ALL":str_replace(",","','",$locat))."')
					);
					
					if(isnull(@COLORcnt,0) > 0 and isnull(@LOCATcnt,0) > 0)
					begin
						set @seem += 1;
					end
					
					close Datatable;
					deallocate Datatable;
					
					declare @SHCID bigint = isnull((select max(ID) from {$this->MAuth->getdb('STDSHCAR')}),0)+1;
					if(@seem = 0)
					begin
						insert into {$this->MAuth->getdb('STDSHCAR')} (ID,TYPECOD,MODEL,BAAB,MANUYR,GCODE,ACTIVE,INSBY,INSDT)
						select @SHCID,'{$type}','{$model}','{$baab}','{$year}','{$gcode}','Yes','{$this->sess["IDNo"]}',@datetime
						
						set @ID=IDENT_CURRENT('{$this->MAuth->getdb('STDSHCAR')}');
						insert into {$this->MAuth->getdb('STDSHCARDetails')} (ID,NPRICE,OPRICE,ACTIVE,INSBY,INSDT)
						select @SHCID,'{$nprice}','{$oprice}','Yes','{$this->sess["IDNo"]}',@datetime
						
						insert into {$this->MAuth->getdb('STDSHCARColors')}(ID,COLOR)
						select @SHCID,'".str_replace(",","' union all select @SHCID,'",($color == "" ? "ALL":$color))."'
						
						insert into {$this->MAuth->getdb('STDSHCARLocats')}(ID,LOCAT)
						select @SHCID,'".str_replace(",","' union all select @SHCID,'",($locat == "" ? "ALL":$locat))."'
					end
					else
					begin
						rollback tran mpstdshc;
						insert into #tempResult select 'y','ผิดพลาด มีข้อมูลรถรุ่นนี้แล้ว ไม่สามารถเพิ่มใหม่ได้อีก';
						return;
					end
					
					insert into #tempResult select 'n','บันทึกข้อมูลเรียบร้อยแล้ว'
					commit tran mpstdshc;
				end try
				begin catch
					rollback tran mpstdshc;
					insert into #tempResult select 'y',ERROR_MESSAGE();
				end catch
			";
			//echo $sql; exit;
		}else{
			$sql = "
				if object_id('tempdb..#tempResult') is not null drop table #tempResult;
				create table #tempResult (error varchar(1),msg varchar(max));
				
				begin tran mpstdshc
				begin try
					declare @ID bigint,@COLORcnt int,@LOCATcnt int,@seem int = 0;
					declare @datetime datetime = getdate();
					
					declare Datatable cursor for
					select ID from {$this->MAuth->getdb('STDSHCAR')}
					where TYPECOD='{$type}' and MODEL='{$model}' 
						and BAAB='{$baab}' and MANUYR='{$year}' 
						and GCODE='{$gcode}' and ACTIVE='Yes'
					
					open Datatable
					fetch next from Datatable into @ID;
					
					while @@FETCH_STATUS = 0
					begin
						set @COLORcnt = (
							select count(*) from {$this->MAuth->getdb('STDSHCARColors')} b 
							where b.ID=@ID and b.ID<>'{$stdid}' and b.COLOR in ('".($color == "" ? "ALL":str_replace(",","','",$color))."')
						);
						
						set @LOCATcnt = (
							select count(*) from {$this->MAuth->getdb('STDSHCARLocats')} b 
							where b.ID=@ID and b.ID<>'{$stdid}' and b.LOCAT in ('".($locat == "" ? "ALL":str_replace(",","','",$locat))."')
						);
						
						if(isnull(@COLORcnt,0) > 0 and isnull(@LOCATcnt,0) > 0)
						begin
							set @seem += 1;							
						end
						
						set @COLORcnt = null;
						set @LOCATcnt = null;
						fetch next from Datatable into @ID;
					end
					
					set @COLORcnt = (
						select count(*) from {$this->MAuth->getdb('STDSHCARColors')} b 
						where b.ID=@ID and b.ID<>'{$stdid}' and b.COLOR in ('".($color == "" ? "ALL":str_replace(",","','",$color))."')
					);
					
					set @LOCATcnt = (
						select count(*) from {$this->MAuth->getdb('STDSHCARLocats')} b 
						where b.ID=@ID and b.ID<>'{$stdid}' and b.LOCAT in ('".($locat == "" ? "ALL":str_replace(",","','",$locat))."')
					);
					
					if(isnull(@COLORcnt,0) > 0 and isnull(@LOCATcnt,0) > 0)
					begin
						set @seem += 1;
					end
					
					close Datatable;
					deallocate Datatable;
					
					if(@seem = 0)
					begin
						if exists (
							select * from {$this->MAuth->getdb('STDSHCARDetails')} 
							where ID='{$stdid}'  and ACTIVE='Yes'
								and (NPRICE<>'{$nprice}' or OPRICE<>'{$oprice}')							
						)
						begin
							update {$this->MAuth->getdb('STDSHCARDetails')} 
							set UPDBY='{$this->sess["IDNo"]}',UPDDT=getdate(),ACTIVE='No'
							where ID='{$stdid}' and ACTIVE='Yes'
							
							insert into {$this->MAuth->getdb('STDSHCARDetails')} (ID,NPRICE,OPRICE,ACTIVE,INSBY,INSDT)
							select '{$stdid}','{$nprice}','{$oprice}','Yes','{$this->sess["IDNo"]}',@datetime
						end
						
						delete from {$this->MAuth->getdb('STDSHCARColors')} 						
						where ID='{$stdid}' and COLOR not in ('".($color == "" ? "ALL":str_replace(",","','",$color))."')
						
						insert into {$this->MAuth->getdb('STDSHCARColors')}(ID,COLOR)
						select * from (
							select '{$stdid}' as ID,'".str_replace(",","' as COLOR union all select '{$stdid}' as ID,'",($color == "" ? "ALL":$color))."' as COLOR
						) as data
						where COLOR not in (select COLOR from {$this->MAuth->getdb('STDSHCARColors')} where ID='{$stdid}')
						
						delete from {$this->MAuth->getdb('STDSHCARLocats')} 						
						where ID='{$stdid}' and LOCAT not in ('".($locat == "" ? "ALL":str_replace(",","','",$locat))."')
						
						insert into {$this->MAuth->getdb('STDSHCARLocats')}(ID,LOCAT)
						select * from (
							select '{$stdid}' as ID,'".str_replace(",","' as LOCAT union all select '{$stdid}' as ID,'",($locat == "" ? "ALL":$locat))."' as LOCAT
						) as data
						where LOCAT not in (select LOCAT from {$this->MAuth->getdb('STDSHCARLocats')} where ID='{$stdid}')
					end
					else
					begin
						rollback tran mpstdshc;
						insert into #tempResult select 'y','ผิดพลาด ไม่สามารถแก้ไขสีรถ หรือสาขาได้เนื่องจาก สีรถหรือสาขาที่ระบุมามีสแตนดาร์ดอยู่แล้ว โปรดตรวจสอบใหม่อีกครั้ง';
						return;
					end
					
					insert into #tempResult select 'n','บันทึกข้อมูลเรียบร้อยแล้ว'
					commit tran mpstdshc;
				end try
				begin catch
					rollback tran mpstdshc;
					insert into #tempResult select 'y',ERROR_MESSAGE();
				end catch
			";
		}
		//echo $sql; exit;
		$this->db->query($sql);
		$sql = "select * from #tempResult";
		$query = $this->db->query($sql);
		
		if($query->row()){
			foreach($query->result() as $row){
				$response["error"] 	  = ($row->error == 'n' ? false:true);
				$response["errorMsg"] = $row->msg;
			}
		}else{
			$response["error"] 	  = true;
			$response["errorMsg"] = "ผิดพลาด ไม่พบข้อมูลตามเงื่อนไข";
		}
		
		echo json_encode($response);
	}
	
	function import_stdshc_20200123(){
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
				$data_value = $objPHPExcel->getActiveSheet()->getCell($cell)->getValue();
				
				if($arrs["old"] == 1){
					$arrs["now"] = 1;
				}else if($arrs["old"] == $row){
					$arrs["now"] = $arrs["now"];
				}else{
					$arrs["now"] += 1;
				}
				//The header will/should be in row 1 only. of course, this can be modified to suit your need.
				if ($row == 1 and $sheetIndex == 0) {
					$header[$row][$column] = $data_value;
				} else {
					switch($column){
						case 'F':
							if($data_value != ''){
								$arr_data[$arrs["now"]][$column] = $data_value; 								
							}else{
								$response = array();
								$response["error"] 		= true;
								$response["errorMsg"] 	= "ผิดพลาด คุณยังไม่ได้ราคารถใหม่";
								echo json_encode($response); exit;
							}
							break;
						case 'G':
							if($data_value != ''){
								$arr_data[$arrs["now"]][$column] = $data_value; 								
							}else{
								$response = array();
								$response["error"] 		= true;
								$response["errorMsg"] 	= "ผิดพลาด คุณยังไม่ได้ราคามือสอง";
								echo json_encode($response); exit;
							}
							break;
						case 'H': $arr_data[$arrs["now"]][$column] = explode(",\n",$data_value); break;
						case 'I': $arr_data[$arrs["now"]][$column] = explode(",\n",$data_value); break;
						default: $arr_data[$arrs["now"]][$column] = $data_value; break;
					}
				}
				
				
				$arrs["old"] = $row;
			}
		}
		
		$column = array(
			"A"=>"RANK",
			"B"=>"MODEL",
			"C"=>"BAAB",
			"D"=>"YEAR",
			"E"=>"GCODE",
			"F"=>"NPRICE",
			"G"=>"OPRICE",
			"H"=>"COLOR",
			"I"=>"LOCAT"
		);
		$arrs = array("A","B","C","D","E","F","G","H","I");
		$datasize = sizeof($arr_data);
		for($i=1;$i<=$datasize;$i++){
			foreach($arrs as $key => $val){
				if(!isset($arr_data[$i][$val])){
					$arr_data[$i][$val] = '';
				}
			}
		}
		
		$sizeof = sizeof($arr_data);
		$table 	= "";
		for($i=1;$i<=$sizeof;$i++){
			$table_rows = "";
			$table_attr = "";
			foreach($arrs as $key => $val){
				switch($val){
					case 'H':
						$array = $arr_data[$i][$val];
						if(is_array($array)){
							$array_unique = array_unique($array); //ดึงข้อมูลที่ไม่ซ้ำกัน
							$ina_size = sizeof($array_unique);
							$ina_data = "";
							$ina_attr = "";
							for($j=0;$j<$ina_size;$j++){
								$sql = "
									select count(*) as r from {$this->MAuth->getdb('JD_SETCOLOR')}
									where MODELCOD='{$arr_data[$i]["B"]}' and BAABCOD='{$arr_data[$i]["C"]}' and COLORCOD='{$array_unique[$j]}'
								";
								//echo $sql; exit;
								$query = $this->db->query($sql);
								$r = $query->row();
								if($r->r > 0){
									$ina_attr .= ($ina_attr == "" ?"":",").$array_unique[$j];
									$ina_data .= ($ina_data == "" ?"":"<br>").$array_unique[$j];
								}else{
									$ina_data .= ($ina_data == "" ?"":"<br>")."<span style='color:red;'>".$array_unique[$j]."</span>";
								}
							}
							
							$table_attr .= " {$column[$val]}='{$ina_attr}'";
							$table_rows .= "<td><div style='max-height:200px;overflow:auto;'>".$ina_data."</div></td>";							
						}else{
							$table_attr .= " {$column[$val]}=''";
							$table_rows .= "<td><div style='max-height:200px;overflow:auto;'></div></td>";							
						}
						break;
					case 'I': 
						$array = $arr_data[$i][$val];
						if(is_array($array)){
							$array_unique = array_unique($array); //ดึงข้อมูลที่ไม่ซ้ำกัน
							$ina_size = sizeof($array_unique);
							$ina_data = "";
							$ina_attr = "";
							for($j=0;$j<$ina_size;$j++){
								$sql = "
									select count(*) as r from {$this->MAuth->getdb('INVLOCAT')}
									where LOCATCD='{$array_unique[$j]}'
								";
								$query = $this->db->query($sql);
								$r = $query->row();
								if($r->r > 0){
									$ina_attr .= ($ina_attr == "" ?"":",").$array_unique[$j];
									$ina_data .= ($ina_data == "" ?"":"<br>").$array_unique[$j];
								}else{
									$ina_data .= ($ina_data == "" ?"":"<br>")."<span style='color:red;'>".$array_unique[$j]."</span>";
								}
							}
							
							$table_attr .= " {$column[$val]}='{$ina_attr}'";
							$table_rows .= "<td><div style='max-height:200px;overflow:auto;'>".$ina_data."</div></td>";
						}else{
							$table_attr .= " {$column[$val]}=''";
							$table_rows .= "<td><div style='max-height:200px;overflow:auto;'></div></td>";							
						}
						break;
					case 'E':
						$GCODE 	= trim($arr_data[$i][$val]);
						$sql 	= "
							select * from {$this->MAuth->getdb('SETGROUP')}
							where GCODE='{$GCODE}'
						";
						$query = $this->db->query($sql);
						
						$GCODE = "";
						if($query->row()){
							foreach($query->result() as $row){
								$GCODE .= ($GCODE == "" ? "":"<br>")."(".$row->GCODE.") ".$row->GDESC;
								$table_attr .= " {$column[$val]}='".str_replace(chr(0),'',$row->GCODE)."'";
							}
						}else{
							$table_attr .= " {$column[$val]}=''";
							$GCODE .= "<span style='color:red;'>".trim($arr_data[$i][$val])."</span>";
						}
						
						$table_rows .= "<td>{$GCODE}</td>";	
						break;
					default: 
						$table_attr .= " {$column[$val]}='{$arr_data[$i][$val]}'";
						$table_rows .= "<td>{$arr_data[$i][$val]}</td>";
						break;
				}
			}
			
			$sql = "
				select count(*) as r from {$this->MAuth->getdb('SETBAAB')}
				where MODELCOD='{$arr_data[$i]["B"]}' and BAABCOD='{$arr_data[$i]["C"]}'
			";
			//echo $sql; exit;
			$query = $this->db->query($sql);
			$r = $query->row();
			if($r->r > 0){
				$table.= "<tr class='mp_stdshc' style='color:black;' {$table_attr}>{$table_rows}</tr>";				
			}else{
				$table.= "<tr style='color:red;'>{$table_rows}</tr>";				
			}
		}
		
		$table="
			<table border=1 width='100%'>
				<thead style='background-color:yellow;'>
					<tr>
						<th>ลำดับ</th>
						<th>รุ่น</th>
						<th>แบบ</th>
						<th>ปีรถ</th>
						<th>กลุ่มรถ</th>
						<th>รถราคารถใหม่</th>
						<th>รถราคามือสอง</th>
						<th>สี</th>
						<th>สาขา</th>
					</tr>
				</thead>
				<tbody style='vertical-align:text-top;'>{$table}</tbody>
			</table>
			* หมายเหตุ : <span style='color:red;'>ข้อมูลที่มีตัว<b>อักษรสีแดง</b></span> หมายถึง ไม่พบข้อมูลในระบบ <u>ซึ่งจะไม่ถูกเพิ่มในสแตนดาร์ด</u>
			<br>
			<div class='col-sm-12' align='right'>
				<button id='mp_save' class='btn btn-xs btn-primary'><span class='glyphicon glyphicon-upload'> นำเข้า</span></button>
			</div>
			&emsp;
		";
		
		$response = array();
		$response["error"] 		= false;
		$response["errorMsg"] 	= $table;
		echo json_encode($response); 
	}
	
	function import_stdshc(){
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
				$data_value = $objPHPExcel->getActiveSheet()->getCell($cell)->getValue();
				
				$check_column_a = "A".$row;
				$check_column_a_value = $objPHPExcel->getActiveSheet()->getCell($check_column_a)->getValue();
				
				if($arrs["old"] == 1){
					$arrs["now"] = 1;
				}else if($arrs["old"] == $row){
					$arrs["now"] = $arrs["now"];
				}else{
					if(trim($check_column_a_value) != "") $arrs["now"] += 1;
				}
				//The header will/should be in row 1 only. of course, this can be modified to suit your need.
				if ($row == 1 and $sheetIndex == 0) {
					$header[$row][$column] = $data_value;
				} else {
					if(trim($check_column_a_value) != ""){
						switch($column){
							case 'G':
								if($data_value != ''){
									$arr_data[$arrs["now"]][$column] = $data_value; 								
								}else{
									$response = array();
									$response["error"] 		= true;
									$response["errorMsg"] 	= $data_value."ผิดพลาด คุณยังไม่ได้ระบุราคารถใหม่".$objPHPExcel->getActiveSheet()->getCell('A'.$arrs["now"])->getValue();
									echo json_encode($response); exit;
								}
								break;
							case 'H':
								if($data_value != ''){
									$arr_data[$arrs["now"]][$column] = $data_value; 								
								}else{
									$response = array();
									$response["error"] 		= true;
									$response["errorMsg"] 	= "ผิดพลาด คุณยังไม่ได้ระบุราคามือสอง";
									echo json_encode($response); exit;
								}
								break;
							case 'I': $arr_data[$arrs["now"]][$column] = explode(",",$data_value); break;
							case 'J': $arr_data[$arrs["now"]][$column] = explode(",",$data_value); break;
							default: $arr_data[$arrs["now"]][$column] = $data_value; break;
						}
					}
				}
				
				$arrs["old"] = $row;
			}
		}
		//print_r($arr_data[5]); exit;
		
		$column = array(
			"A"=>"RANK",
			"B"=>"TYPECOD",
			"C"=>"MODEL",
			"D"=>"BAAB",
			"E"=>"YEAR",
			"F"=>"GCODE",
			"G"=>"NPRICE",
			"H"=>"OPRICE",
			"I"=>"COLOR",
			"J"=>"LOCAT"
		);
		
		$arrs = array("A","B","C","D","E","F","G","H","I","I");
		$datasize = sizeof($arr_data);
		for($i=1;$i<=$datasize;$i++){
			foreach($arrs as $key => $val){
				if(!isset($arr_data[$i][$val])){
					$arr_data[$i][$val] = " ";
				}
			}
		}
		//print_r($arr_data[5]); exit;
		//echo $arr_data[45]["D"]; exit;
		
		$sizeof = sizeof($arr_data);
		
		$sql = "
			if object_id('tempdb..#tempshc_declare') is not null drop table #tempshc_declare;
			create table #tempshc_declare (dt datetime);
			
			insert into #tempshc_declare select getdate();
		";
		//echo $sql; exit;
		$this->db->query($sql);
		
		$sql = "";
		//echo date('d-m-Y h:i:s')."-- นำข้อมูลใส่ STDSHCARTemp<br><br>";
		for($i=1;$i<=$sizeof;$i++){
			if( trim($arr_data[$i]["B"]) != "" ){
				$sql = "
					insert into {$this->MAuth->getdb('STDSHCARTemp')} (
						[keyid],[typecod],[model],[baab],[manuyr],[gcode]
						,[nprice],[oprice],[color],[locat],[insby],[insdt]
					)
					select '".$arr_data[$i]["A"]."',
					'".$arr_data[$i]["B"]."',
					'".$arr_data[$i]["C"]."',
					'".$arr_data[$i]["D"]."',
					'".$arr_data[$i]["E"]."',
					'".$arr_data[$i]["F"]."',
					'".$arr_data[$i]["G"]."',
					'".$arr_data[$i]["H"]."',
					'".(IS_ARRAY($arr_data[$i]["I"]) ? IMPLODE(",",array_unique($arr_data[$i]["I"])):$arr_data[$i]["I"])."',
					'".(IS_ARRAY($arr_data[$i]["J"]) ? IMPLODE(",",array_unique($arr_data[$i]["J"])):$arr_data[$i]["J"])."',
					'".$this->sess["IDNo"]."',
					(select dt from #tempshc_declare)
				";
				//echo $sql; exit;
				$this->db->query($sql);
			}			
		}
		//echo date('d-m-Y h:i:s')."<br><br>";
		
		if($sql != ""){			
			$sql = "
				if object_id('tempdb..#tempshc') is not null drop table #tempshc;
				create table #tempshc (error varchar(1),errorMsg varchar(max));
				
				begin tran ts_shctemp
				begin try
					delete from {$this->MAuth->getdb('STDSHCARTemp')} 
					where insby = '{$this->sess["IDNo"]}' and insdt = (select dt from #tempshc_declare) and nprice is null and oprice is null
					
					delete from {$this->MAuth->getdb('STDSHCARTemp')} 
					where insby = '{$this->sess["IDNo"]}' and insdt < (select dt from #tempshc_declare)
					
					if exists (
						select keyid 
						from {$this->MAuth->getdb('STDSHCARTemp')}  
						where insby = '{$this->sess["IDNo"]}' 
						group by keyid
						having count(*) > 1
					)
					begin
						insert into #tempshc select 'y','ผิดพลาด ข้อมูลนำเข้า ข้อมูลลำดับที่ซ้ำซ้อน โปรดตรวจสอบใหม่อีกครั้ง'
						commit tran ts_shctemp;
						return;
					end 
					
					insert into #tempshc select 'n','บันทึกข้อมูลแล้ว'
					commit tran ts_shctemp 					
				end try
				begin catch
					rollback tran ts_shctemp
					insert into #tempshc select 'y',CAST(ERROR_LINE() as varchar)+' :: '+ERROR_MESSAGE();
				end catch
			";
			//echo $sql; exit;
			$this->db->query($sql);
			$sql = "select * from #tempshc";
			$query = $this->db->query($sql);
			
			if($query->row()){
				foreach($query->result() as $row){
					if($row->error == 'y'){
						$response = array();
						$response["error"] 		= true;
						$response["errorMsg"] 	= $row->errorMsg;
						echo json_encode($response); exit;
					}
				}
			}else{
				$response = array();
				$response["error"] 		= true;
				$response["errorMsg"] 	= 'Not connect database';
				echo json_encode($response); exit;
			}
			
			$this->response_confirm(1,100);
		}else{
			$response = array();
			$response["error"] 		= true;
			$response["errorMsg"] 	= "ผิดพลาด ไม่พบข้อมูลที่นำเข้าได้";
			echo json_encode($response); 
		}
	}
	
	function loadSTDSHC(){
		$this->response_confirm($_POST["to"]+1,$_POST["to"]+100);
	}
	
	private function response_confirm_bak(){
		$sql = "
			select a.* 
				,isnull(z.TYPECOD,'NOT') as settype
				,isnull(b.MODELCOD,'NOT') as setmodel
				,isnull(c.BAABCOD,'NOT') as setbaab
				,isnull(d.GCODE,'NOT') as setgroup
			from {$this->MAuth->getdb('STDSHCARTemp')}  a
			left join {$this->MAuth->getdb('SETTYPE')} z on a.typecod=z.TYPECOD collate thai_cs_as 
			left join {$this->MAuth->getdb('SETMODEL')} b on a.model=b.MODELCOD collate thai_cs_as and b.TYPECOD=a.typecod collate thai_cs_as
			left join {$this->MAuth->getdb('SETBAAB')} c on a.model=c.MODELCOD collate thai_cs_as and a.baab=c.BAABCOD collate thai_cs_as
			left join {$this->MAuth->getdb('SETGROUP')} d on a.gcode=d.GCODE collate thai_cs_as				
			where insby = '{$this->sess["IDNo"]}'
			order by a.ID
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$table = "";			
		if($query->row()){
			$i = 1;
			//echo date('d-m-Y h:i:s')."Query ดึงข้อมูลมาแสดง<br><br>";
			foreach($query->result() as $row){
				//if($i++ == 500) exit;	
				$sql = "
					declare @xml xml = '<r>'+replace('".$row->color."',',','</r>,<r>')+'</r>';						
					select a.COLORCOD,isnull(b.COLORCOD,'NOT') indb from (
						select replace(replace(cast(t.col.query('.') as varchar(max)),'<r>',''),'</r>','') as COLORCOD
						from @xml.nodes('/r') t(col)
					) as a
					left join {$this->MAuth->getdb('JD_SETCOLOR')} b on a.COLORCOD=b.COLORCOD collate thai_cs_as
						and b.MODELCOD='{$row->model}' and b.BAABCOD in ('".(str_replace(",","','",$row->baab))."')
				";
				//echo $sql; exit;
				$query = $this->db->query($sql);
				
				$this_color = "";
				if($query->row()){
					//echo date('d-m-Y h:i:s')."Query ดึงข้อมูล ***สี*** มาแสดง<br><br>"; 
					foreach($query->result() as $row_locat){
						if($this_color != ""){ $this_color .= "<br>"; }
						$this_color .= "<span style='".($row_locat->indb == "NOT" ? "color:red;":"")."'>".$row_locat->COLORCOD."</span>";
					}
				}
				
				
				$sql = "
					declare @xml xml = '<r>'+replace('".$row->locat."',',','</r>,<r>')+'</r>';
					select top 1 a.LOCAT,isnull(b.LOCATCD,'NOT') indb from (
						select replace(replace(cast(t.col.query('.') as varchar(max)),'<r>',''),'</r>','') as LOCAT
						from @xml.nodes('/r') t(col)
					) as a
					left join {$this->MAuth->getdb('INVLOCAT')} b on a.LOCAT=b.LOCATCD collate thai_cs_as
				";
				//echo $sql; exit;
				$query = $this->db->query($sql);
				
				$this_locat = "";
				if($query->row()){
					//echo date('d-m-Y h:i:s')."Query ดึงข้อมูล ***สาขา*** มาแสดง<br><br>"; 
					foreach($query->result() as $row_locat){
						if($this_locat != ""){ $this_locat .= "<br>"; }
						$this_locat .= "<span style='".($row_locat->indb == "NOT" ? "color:red;":"")."'>".$row_locat->LOCAT."</span>";
					}
				}
				
				$r = "";
				/*
				if($row->settype == "NOT" or $row->setmodel == "NOT" or $row->setbaab == "NOT" or $row->setgroup == "NOT"){
					$r = "background-color:#fab7b7;color:white;";
				}
				*/
				
				$table .= "
					<tr style='{$r}'>
						<td>".$row->keyid."</td>
						<td style='".($row->settype == "NOT" ? "color:red;":"")."'>".$row->typecod."</td>
						<td style='".($row->setmodel == "NOT" ? "color:red;":"")."'>".$row->model."</td>
						<td style='".($row->setbaab == "NOT" ? "color:red;":"")."'>".$row->baab."</td>
						<td>".$row->manuyr."</td>
						<td style='".($row->setgroup == "NOT" ? "color:red;":"")."'>".$row->gcode."</td>
						<td>".$row->nprice."</td>
						<td>".$row->oprice."</td>
						<td>".$this_color."</td>
						<td><div style='max-height:150px;overflow:auto;'>".$this_locat."</div></td>
					</tr>
				";
			}
		}
		
		$table = "
			<table border=1 width='100%'>
				<thead style='background-color:yellow;'>
					<tr>
						<th>ลำดับ</th>
						<th>ยี่ห้อ</th>
						<th>รุ่น</th>
						<th>แบบ</th>
						<th>ปีรถ</th>
						<th>กลุ่มรถ</th>
						<th>รถราคารถใหม่</th>
						<th>รถราคามือสอง</th>
						<th>สี</th>
						<th>สาขา</th>
					</tr>
				</thead>
				<tbody style='vertical-align:text-top;'>{$table}</tbody>
			</table>
			* หมายเหตุ : <span style='color:red;'>ข้อมูลที่มีตัว<b>อักษรสีแดง</b></span> หมายถึง ไม่พบข้อมูลในระบบ <u>ซึ่งจะไม่ถูกเพิ่มในสแตนดาร์ด</u> <br>
			<span style='background-color:#fab7b7;color:white;'>พื้นหลังเป็นสีชมพู</span> หมายถึง ไม่พบข้อมูลยี่ห้อ รุ่น แบบ สี หรือกลุ่มรถ <u>ซึ่งจะไม่ถูกเพิ่มในสแตนดาร์ด ต้องแก้ไขข้อมูลใหม่อีกครั้ง</u>
			<br>
			<div class='col-sm-12' align='right'>
				<button id='mp_save' class='btn btn-xs btn-primary'><span class='glyphicon glyphicon-upload'> นำเข้า</span></button>
			</div>
			&emsp;
		";
		
		$response = array();
		$response["error"] 		= false;
		$response["errorMsg"] 	= $table;
		echo json_encode($response); 
	}
	
	private function response_confirm($s,$e){
		$data = array();
		$process_start = (date('H:i:s'));
		$sql = "
			select * from (
				select ROW_NUMBER() over(order by a.ID) r,a.* 
					,isnull(z.TYPECOD,'NOT') as settype
					,(case when a.BAAB = 'ทุกรุ่น' then 'all' else isnull(b.MODELCOD,'NOT') end) as setmodel
					,(case when a.BAAB = 'ทุกแบบ' then 'all' else isnull(c.BAABCOD,'NOT') end) as setbaab
					,isnull(d.GCODE,'NOT') as setgroup
				from {$this->MAuth->getdb('STDSHCARTemp')}  a
				left join {$this->MAuth->getdb('SETTYPE')} z on a.typecod=z.TYPECOD collate thai_cs_as 
				left join {$this->MAuth->getdb('SETMODEL')} b on a.model=b.MODELCOD collate thai_cs_as and b.TYPECOD=a.typecod collate thai_cs_as
				left join {$this->MAuth->getdb('SETBAAB')} c on a.model=c.MODELCOD collate thai_cs_as and a.baab=c.BAABCOD collate thai_cs_as
				left join {$this->MAuth->getdb('SETGROUP')} d on a.gcode=d.GCODE collate thai_cs_as				
				where insby = '{$this->sess["IDNo"]}'
			) as data
			where r between '{$s}' and '{$e}'
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		$process_query = (date('H:i:s'));
		
		$table = "";
		$load = 0;
		if($query->row()){
			$i = 1;
			foreach($query->result() as $row){
				$load					= $row->r;
				$data[$i]["keyid"] 		= $row->keyid;
				$data[$i]["settype"] 	= str_replace(chr(0),"",$row->settype);
				$data[$i]["setmodel"] 	= str_replace(chr(0),"",$row->setmodel);
				$data[$i]["setbaab"] 	= str_replace(chr(0),"",$row->setbaab);
				$data[$i]["setgroup"] 	= str_replace(chr(0),"",$row->setgroup);
				$data[$i]["manuyr"] 	= $row->manuyr;
				$data[$i]["typecod"] 	= str_replace(chr(0),"",$row->typecod);
				$data[$i]["model"] 		= str_replace(chr(0),"",$row->model);
				$data[$i]["baab"] 		= str_replace(chr(0),"",$row->baab);
				$data[$i]["gcode"] 		= str_replace(chr(0),"",$row->gcode);
				$data[$i]["nprice"] 	= $row->nprice;
				$data[$i]["oprice"] 	= $row->oprice;
				
				$sql = "
					declare @xml xml = '<r>'+replace('".$row->color."',',','</r>,<r>')+'</r>';						
					select a.COLORCOD,isnull(b.COLORCOD,'NOT') indb from (
						select replace(replace(cast(t.col.query('.') as varchar(max)),'<r>',''),'</r>','') as COLORCOD
						from @xml.nodes('/r') t(col)
					) as a
					left join {$this->MAuth->getdb('JD_SETCOLOR')} b on a.COLORCOD=b.COLORCOD collate thai_cs_as
						and b.MODELCOD='{$row->model}' and b.BAABCOD in ('".(str_replace(",","','",$row->baab))."')
				";
				$query = $this->db->query($sql);
				
				$this_color = "";
				if($query->row()){
					$i_color = 1;
					foreach($query->result() as $row_locat){
						$data[$i]["color"][$i_color]["indb"] 	= $row_locat->indb;
						$data[$i]["color"][$i_color]["color"] 	= $row_locat->COLORCOD;
						
						$i_color++;
					}
				}
				
				$sql = "
					declare @xml xml = '<r>'+replace('".$row->locat."',',','</r>,<r>')+'</r>';
					select a.LOCAT,isnull(b.LOCATCD,'NOT') indb from (
						select replace(replace(cast(t.col.query('.') as varchar(max)),'<r>',''),'</r>','') as LOCAT
						from @xml.nodes('/r') t(col)
					) as a
					left join {$this->MAuth->getdb('INVLOCAT')} b on a.LOCAT=b.LOCATCD collate thai_cs_as
				";
				//echo $sql; exit;
				$query = $this->db->query($sql);
				
				$this_locat = "";
				if($query->row()){
					$i_locat = 1;
					foreach($query->result() as $row_locat){
						$data[$i]["locat"][$i_locat]["indb"] 	= $row_locat->indb;
						$data[$i]["locat"][$i_locat]["locat"] 	= $row_locat->LOCAT;
						
						$i_locat++;
					}
				}
				
				$i++;
			}
		}
		$process_json = (date('H:i:s'));
		
		header('Access-Control-Allow-Origin: *'); 
		header('Content-Type: application/json');
		
		$response = array();
		$response["error"] 		= false;
		$response["errorMsg"] 	= "";
		$response["data"] 		= $data;
		$response["from"] 		= $s;
		$response["to"] 		= $e;
		$response["load"] 		= $load;
		$response["process"] 	= array(
			"process_start" =>  $process_start,
			"process_query" =>  $process_query,
			"process_json" =>  $process_json,
		);
		echo json_encode($response); 
	}
	
	function import_save_20200123(){
		$response = array();
		$data = $_POST["data"];
		if(is_array($data)){
			$sql 		= "";
			$data_size 	= sizeof($data);
			for($i=0;$i<$data_size;$i++){
				$model 	= $data[$i]["model"];
				$baab 	= $data[$i]["baab"];
				$year 	= $data[$i]["year"];
				$gcode 	= $data[$i]["gcode"];
				$nprice = $data[$i]["nprice"];
				$oprice = $data[$i]["oprice"];
				$color 	= $data[$i]["color"];
				$locat 	= $data[$i]["locat"];
				
				if($gcode != "" and $year != ""){
					$sql .= "
						set @ID=null;set @COLORcnt=null;set @LOCATcnt=null;set @seem=0;
						-- START ตรวจสอบความซ้ำซ้อนของข้อมูล
						declare Datatable{$i} cursor for
						select ID from {$this->MAuth->getdb('STDSHCAR')}
						where MODEL='{$model}' and BAAB='{$baab}' and MANUYR='{$year}' and GCODE='{$gcode}'
						
						open Datatable{$i}
						fetch next from Datatable{$i} into @ID;
						
						while @@FETCH_STATUS = 0
						begin
							set @COLORcnt = (
								select count(*) from {$this->MAuth->getdb('STDSHCARColors')} b 
								where b.ID=@ID and b.COLOR in ('".($color == "" ? "ALL":str_replace(",","','",$color))."')
							);
							set @LOCATcnt = (
								select count(*) from {$this->MAuth->getdb('STDSHCARLocats')} b 
								where b.ID=@ID and b.LOCAT in ('".($locat == "" ? "ALL":str_replace(",","','",$locat))."')
							);
							if(isnull(@COLORcnt,0) > 0 and isnull(@LOCATcnt,0) > 0) begin set @seem += 1; end
							
							set @COLORcnt = null; set @LOCATcnt = null;
							fetch next from Datatable{$i} into @ID;
						end
						
						set @COLORcnt = (
							select count(*) from {$this->MAuth->getdb('STDSHCARColors')} b 
							where b.ID=@ID and b.COLOR in ('".($color == "" ? "ALL":str_replace(",","','",$color))."')
						);
						
						set @LOCATcnt = (
							select count(*) from {$this->MAuth->getdb('STDSHCARLocats')} b 
							where b.ID=@ID and b.LOCAT in ('".($locat == "" ? "ALL":str_replace(",","','",$locat))."')
						);
						
						if(isnull(@COLORcnt,0) > 0 and isnull(@LOCATcnt,0) > 0) begin set @seem += 1; end
						close Datatable{$i};
						deallocate Datatable{$i};
						-- END ตรวจสอบความซ้ำซ้อนของข้อมูล 
						if(@seem = 0)					
						begin
							insert into {$this->MAuth->getdb('STDSHCAR')} (MODEL,BAAB,MANUYR,GCODE,ACTIVE,INSBY,INSDT)
							select '{$model}','{$baab}','{$year}','{$gcode}','Yes','{$this->sess["IDNo"]}',@datetime
							
							set @ID=IDENT_CURRENT('{$this->MAuth->getdb('STDSHCAR')}');
							insert into {$this->MAuth->getdb('STDSHCARDetails')} (ID,NPRICE,OPRICE,ACTIVE,INSBY,INSDT)
							select @ID,'{$nprice}','{$oprice}','Yes','{$this->sess["IDNo"]}',@datetime
							
							insert into {$this->MAuth->getdb('STDSHCARColors')}(ID,COLOR)
							select @ID,'".str_replace(",","' union all select @ID,'",($color == "" ? "ALL":$color))."'
							
							insert into {$this->MAuth->getdb('STDSHCARLocats')}(ID,LOCAT)
							select @ID,'".str_replace(",","' union all select @ID,'",($locat == "" ? "ALL":$locat))."'
						end
					";
				}
			}
			
			$sql = "
				if object_id('tempdb..#tempResult') is not null drop table #tempResult;
				create table #tempResult (error varchar(1),msg varchar(max));
				
				begin tran mpstdshc
				begin try
					declare @ID bigint,@COLORcnt int,@LOCATcnt int,@seem int = 0;
					declare @datetime datetime = getdate();
					
					{$sql}
					
					
					
					insert into #tempResult select 'n',convert(varchar,@datetime,121)
					commit tran mpstdshc;
				end try
				begin catch
					rollback tran mpstdshc;
					insert into #tempResult select 'y',ERROR_MESSAGE();
				end catch
			";
			//echo $sql; exit;
			$this->db->query($sql);
			$sql = "select * from #tempResult";
			$query = $this->db->query($sql);
			
			if($query->row()){
				foreach($query->result() as $row){
					if($row->error == "n"){
						$sql = "
							select a.*,b.GDESC 
								,c.NPRICE,c.OPRICE								
							from {$this->MAuth->getdb('STDSHCAR')} a
							left join {$this->MAuth->getdb('SETGROUP')} b on a.GCODE=b.GCODE collate thai_cs_as
							left join STDSHCARDetails c on a.ID=c.ID
							where a.INSBY='{$this->sess["IDNo"]}' and a.INSDT='{$row->msg}'
						";
						//echo $sql; exit;
						$query = $this->db->query($sql);
						
						$html = "";
						if($query->row()){
							foreach($query->result() as $row){
								$html .= "
									<tr>
										<td>".$row->ID."</td>
										<td>".$row->MODEL."</td>
										<td>".$row->BAAB."</td>
										<td>".$row->MANUYR."</td>
										<td>".$row->GCODE." ".$row->GDESC."</td>
										<td>".$row->NPRICE."</td>
										<td>".$row->OPRICE."</td>
									</tr>
								";
							}
						}else{
							$html .= "ไม่มีข้อมูลที่นำเข้าได้ โปรดตรวจสอบข้อมูลใหม่อีกครั้ง";
							$response["error"] = true;
							$response["errorMsg"] = $html;
							echo json_encode($response); exit;
						}
						
						$html = "
							<h3>นำเข้าสแตนดาร์ดรถมือสองแล้ว</h3>
							<div class='col-sm-12'>
								<table id='mp_result' class='col-sm-12' border=1 style='border-collapse:collapse;width:100%;'>
									<thead>
										<tr>
											<th>#</th>
											<th>รุ่น</th>
											<th>แบบ</th>
											<th>ปี</th>
											<th>กลุ่ม</th>
											<th>ราคารถใหม่</th>
											<th>ราคามือสอง</th>
										</tr>
									</thead>
									<tbody>{$html}</tbody>
								</table>
							</div>
						";
						$response["error"] = false;
						$response["html"] = $html;
					}else{
						$response["error"] = true;
						$response["errorMsg"] = $row->msg;
					}
				}
			}else{
				$response["error"] = true;
				$response["errorMsg"] = 'ผิดพลาดไม่สามารถบันทึกราคาขายได้ โปรดติดต่อฝ่ายไอที';
			}
		}else{
			$response["error"] 	  = true;
			$response["errorMsg"] = "ผิดพลาด ไม่พบข้อมูลตามเงื่อนไข";
		}
		
		echo json_encode($response); 
	}
	
	function import_save(){
		$response = array();
		
		
		$sql = "
			if object_id('tempdb..#tempResult') is not null drop table #tempResult;
			create table #tempResult (error varchar(1),msg varchar(max)
				,typecod varchar(20)
				,model varchar(20)
				,baab varchar(20)
				,gcode varchar(5)
				,cond int
			);
			
			SET NOCOUNT ON;
			begin tran import_transaction
			begin try
				declare @keyid int;
				declare @insby varchar(20) = '{$this->sess["IDNo"]}';
				declare @insdt varchar(20) = getdate();
				declare csimportshc cursor for (
					select * from (
						select distinct keyid from {$this->MAuth->getdb('STDSHCARTemp')}
						where insby=@insby
					) as data
				);
				open csimportshc;
				
				fetch next from csimportshc into @keyid
				while @@FETCH_STATUS = 0
				begin
					declare @datetime datetime    = getdate();
					declare @typecod varchar(20)  = (select typecod from {$this->MAuth->getdb('STDSHCARTemp')} where keyid=@keyid); 
					declare @model varchar(20)	  = (select model from {$this->MAuth->getdb('STDSHCARTemp')} where keyid=@keyid); 
					declare @baab varchar(20) 	  = (select baab from {$this->MAuth->getdb('STDSHCARTemp')} where keyid=@keyid);
					declare @manuyr varchar(4)	  = (select manuyr from {$this->MAuth->getdb('STDSHCARTemp')} where keyid=@keyid);
					declare @gcode varchar(5)	  = (select gcode from {$this->MAuth->getdb('STDSHCARTemp')} where keyid=@keyid);
					declare @nprice decimal(18,2) = (select nprice from {$this->MAuth->getdb('STDSHCARTemp')} where keyid=@keyid);
					declare @oprice decimal(18,2) = (select oprice from {$this->MAuth->getdb('STDSHCARTemp')} where keyid=@keyid);
					declare @color varchar(max)	  = (select replace(isnull(color,'ALL'),',','</r><r>') from {$this->MAuth->getdb('STDSHCARTemp')} where keyid=@keyid);
					declare @locat varchar(max)	  = (select replace(isnull(locat,'ALL'),',','</r><r>') from {$this->MAuth->getdb('STDSHCARTemp')} where keyid=@keyid);
					
					if not exists (
						select * from {$this->MAuth->getdb('SETTYPE')}
						where TYPECOD=@typecod collate thai_ci_as
					)
					begin 
						insert into {$this->MAuth->getdb('SETTYPE')}
						select @typecod,convert(varchar(8),GETDATE(),112);
					end
					
					if not exists (
						select * from {$this->MAuth->getdb('SETMODEL')}
						where TYPECOD=@typecod collate thai_ci_as and MODELCOD=@model collate thai_ci_as
					)
					begin 
						insert into {$this->MAuth->getdb('SETMODEL')} (TYPECOD,MODELCOD,MEMO1)
						select @typecod,@model,convert(varchar(8),GETDATE(),112);
					end
					
					if not exists (
						select * from {$this->MAuth->getdb('SETBAAB')}
						where TYPECOD=@typecod collate thai_ci_as and MODELCOD=@model collate thai_ci_as and BAABCOD=@baab collate thai_ci_as 
					)
					begin 
						if(@baab != 'ทุกแบบ')
						begin	
							insert into {$this->MAuth->getdb('SETBAAB')} (TYPECOD,MODELCOD,BAABCOD,MEMO1)
							select @typecod,@model,@baab,convert(varchar(8),GETDATE(),112);
						end
					end
					
					if not exists (
						select * from {$this->MAuth->getdb('SETGROUP')}
						where GCODE=@gcode
					)
					begin
						close csimportshc; deallocate csimportshc;
						rollback tran import_transaction
						insert into #tempResult select 'y','ผิดพลาด ไม่พบประเภทรถ '+@gcode+' ในลำดับที่ '+cast(@keyid as varchar)+' โปรดตรวจสอบใหม่อีกครั้ง'
							,@typecod,@model,@baab,@gcode,4;
						return;
					end
					begin
						declare @tb_color table (color varchar(20));
						declare @tb_locat table (locat varchar(20));
						
						set @color =  case when @color = '' then '<r>ALL</r>' else ('<r>'+@color+'</r>') end;
						set @locat =  case when @locat = '' then '<r>ALL</r>' else ('<r>'+@locat+'</r>') end;
						declare @xml_color xml = @color;					
						declare @xml_locat xml = @locat;					
						
						insert into @tb_color
						select replace(replace(cast(t.col.query('.') as varchar(max)),'<r>',''),'</r>','') color					
						from @xml_color.nodes('/r') t(col)
						
						if exists (
							select 1 from @tb_color
							where color collate thai_cs_as not in (
								select COLORCOD from {$this->MAuth->getdb('JD_SETCOLOR')}
								where MODELCOD=@model and BAABCOD=@baab
								union select 'ALL'
							)							
						)
						begin
							declare @color_issue varchar(max) = (
								stuff((select ','+cast(color as varchar(20)) from @tb_color
								where color collate thai_cs_as not in (
									select COLORCOD from {$this->MAuth->getdb('JD_SETCOLOR')}
									where MODELCOD=@model and BAABCOD=@baab
								) for xml path('')),1,1,'')
							);
							
							close csimportshc; deallocate csimportshc;
							rollback tran import_transaction
							insert into #tempResult select 'y','ผิดพลาด ไม่พบสี '+@color_issue+' ในลำดับที่ '+cast(@keyid as varchar)+' โปรดตรวจสอบใหม่อีกครั้ง'
								,@typecod,@model,@baab,@gcode,0;
							return;
						end
						
						if not exists(select * from @tb_color)
						begin
							insert into @tb_color select 'ALL'
						end
						
						insert into @tb_locat
						select replace(replace(cast(t.col.query('.') as varchar(max)),'<r>',''),'</r>','') locat					
						from @xml_locat.nodes('/r') t(col)
						
						if exists (
							select * from @tb_locat
							where locat collate thai_cs_as not in (select LOCATCD from {$this->MAuth->getdb('INVLOCAT')} union select 'ALL')
						)
						begin
							declare @locat_issue varchar(max) = (
								stuff((select ','+cast(LOCAT as varchar(20)) from @tb_locat
								where locat collate thai_cs_as not in (select LOCATCD from {$this->MAuth->getdb('INVLOCAT')})
								for xml path('')),1,1,'')
							);
							
							close csimportshc; deallocate csimportshc;
							rollback tran import_transaction
							insert into #tempResult select 'y','ผิดพลาด ไม่พบสาขา '+@locat_issue+' ในลำดับที่ '+cast(@keyid as varchar)+' โปรดตรวจสอบใหม่อีกครั้ง'
								,@typecod,@model,@baab,@gcode,0;
							return;
						end						
						
						if not exists(select * from @tb_locat)
						begin
							insert into @tb_locat select 'ALL'
						end
						
						if exists (
							select * from {$this->MAuth->getdb('STDSHCAR')}
							where MODEL=@model and BAAB=@baab and MANUYR=@manuyr and GCODE=@gcode
						)
						begin				
							declare @ID bigint;
							declare cs_intables cursor for (
								select ID from {$this->MAuth->getdb('STDSHCAR')}
								where MODEL=@model and BAAB=@baab and MANUYR=@manuyr and GCODE=@gcode
							);
							
							open cs_intables;
							fetch next from cs_intables into @ID;
							
							while @@FETCH_STATUS = 0
							begin 
								declare @COLORcnt int = (
									select count(*) from {$this->MAuth->getdb('STDSHCARColors')} b 
									where b.ID=@ID and b.COLOR collate thai_cs_as in (select * from @tb_color)
								);
								declare @LOCATcnt int = (
									select count(*) from {$this->MAuth->getdb('STDSHCARLocats')} b 
									where b.ID=@ID and b.LOCAT collate thai_cs_as in (select * from @tb_locat)
								);
								
								if(isnull(@COLORcnt,0) > 0 and isnull(@LOCATcnt,0) > 0) 
								begin	
									close cs_intables; deallocate cs_intables;
									close csimportshc; deallocate csimportshc;
									rollback tran import_transaction
									insert into #tempResult select 'y','ผิดพลาด ข้อมูลลำดับที่ '+cast(@keyid as varchar)+' ซ้ำซ้อนกับสแตนดาร์ดปัจจุบัน('+CAST(@ID as varchar)+') โปรดตรวจสอบใหม่อีกครั้ง'
										,@typecod,@model,@baab,@gcode,0;
									return;
								end
								fetch next from cs_intables into @ID;
							end
							close cs_intables; deallocate cs_intables;
						end
						
						begin
							set @ID=(isnull((select MAX(ID) from {$this->MAuth->getdb('STDSHCAR')}),0) + 1);
							
							insert into {$this->MAuth->getdb('STDSHCAR')} (ID,TYPECOD,MODEL,BAAB,MANUYR,GCODE,ACTIVE,INSBY,INSDT)
							select @ID,@typecod,@model,(case when @baab = 'ทุกแบบ' then 'all' else @baab end),@manuyr,@gcode,'Yes',@insby,@datetime
							
							insert into {$this->MAuth->getdb('STDSHCARDetails')} (ID,NPRICE,OPRICE,ACTIVE,INSBY,INSDT)
							select @ID,@nprice,@oprice,'Yes',@insby,@datetime
							
							insert into {$this->MAuth->getdb('STDSHCARColors')}(ID,COLOR)
							select @ID,* from @tb_color
							
							insert into {$this->MAuth->getdb('STDSHCARLocats')}(ID,LOCAT)
							select @ID,* from @tb_locat
							
							set @COLORcnt = null; 
							set @LOCATcnt = null;
						end
						
						delete from @tb_color
						delete from @tb_locat
					end
						
					fetch next from csimportshc into @keyid
				end
				close csimportshc; deallocate csimportshc;
				
				insert into #tempResult select 'n','บันทึกข้อมูลแล้ว',@typecod,@model,@baab,@gcode,0;
				commit tran import_transaction
			end try
			begin catch
				rollback tran import_transaction
				insert into #tempResult select 'y',cast(ERROR_LINE() as varchar)+'::'+ERROR_MESSAGE(),@typecod,@model,@baab,@gcode,0;
			end catch
		";
		//echo $sql; exit;
		
		$this->db->query($sql);
		$sql = "select * from #tempResult";
		$query = $this->db->query($sql);
		
		if($query->row()){
			foreach($query->result() as $row){
				if($row->error == "n"){
					$sql = "
						select a.*,b.GDESC 
							,c.NPRICE,c.OPRICE	
							,replace(stuff((select ','+cast(cc.COLOR as varchar(20)) 
								from {$this->MAuth->getdb('STDSHCARColors')} cc
								where cc.ID=a.ID for xml path('')),1,1,''),',','<br>') as COLOR
							,replace(stuff((select ','+cast(lc.LOCAT as varchar(20)) 
								from {$this->MAuth->getdb('STDSHCARLocats')} lc
								where lc.ID=a.ID for xml path('')),1,1,''),',','<br>') as LOCAT
						from {$this->MAuth->getdb('STDSHCAR')} a
						left join {$this->MAuth->getdb('SETGROUP')} b on a.GCODE=b.GCODE collate thai_cs_as
						left join {$this->MAuth->getdb('STDSHCARDetails')} c on a.ID=c.ID
						where a.INSBY='{$this->sess["IDNo"]}' 
					";
					//echo $sql; exit;
					$query = $this->db->query($sql);
					
					$html = "";
					if($query->row()){
						foreach($query->result() as $row){
							$html .= "
								<tr style='vertical-align:text-top;'>
									<td>".$row->ID."</td>
									<td>".$row->TYPECOD."</td>
									<td>".$row->MODEL."</td>
									<td>".$row->BAAB."</td>
									<td>".$row->COLOR."</td>
									<td>".$row->LOCAT."</td>
									<td>".$row->MANUYR."</td>
									<td>".$row->GCODE." ".$row->GDESC."</td>
									<td>".$row->NPRICE."</td>
									<td>".$row->OPRICE."</td>
								</tr>
							";
						}
					}else{
						$html .= "ไม่มีข้อมูลที่นำเข้าได้ โปรดตรวจสอบข้อมูลใหม่อีกครั้ง";
						$response["error"] = true;
						$response["errorMsg"] = $html;
						echo json_encode($response); exit;
					}
					
					$html = "
						<h3>นำเข้าสแตนดาร์ดรถมือสองแล้ว</h3>
						<div class='col-sm-12'>
							<table id='mp_result' class='col-sm-12' border=1 style='border-collapse:collapse;width:100%;'>
								<thead>
									<tr>
										<th>#</th>
										<th>ยี่ห้อ</th>
										<th>รุ่น</th>
										<th>แบบ</th>
										<th>สี</th>
										<th>สาขา</th>
										<th>ปี</th>
										<th>กลุ่ม</th>
										<th>ราคารถใหม่</th>
										<th>ราคามือสอง</th>
									</tr>
								</thead>
								<tbody>{$html}</tbody>
							</table>
						</div>
					";
					$response["error"] = false;
					$response["html"] = $html;
				}else{
					$response["error"] = true;
					$response["errorMsg"] = $row->typecod;
					$response["errorMsg"] = $row->model;
					$response["errorMsg"] = $row->baab;
					$response["errorMsg"] = $row->gcode;
					$response["errorMsg"] = $row->cond;
					$response["errorMsg"] = $row->msg;
					
				}
			}
		}else{
			$response["error"] = true;
			$response["errorMsg"] = 'ผิดพลาดไม่สามารถบันทึกราคาขายได้ โปรดติดต่อฝ่ายไอที';
		}
		
		echo json_encode($response); 
	}
	
}

































