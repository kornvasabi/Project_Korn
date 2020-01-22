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
			$query = $this->db->query($sql);
			
			if($query->row()){
				foreach($query->result() as $row){
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
		
		$html = "
			<div class='col-sm-12 col-md-12 col-lg-8 col-lg-offset-2'>
				<div class='row' style='height:calc(100vh - 115px);border:0px solid red;overflow:auto;'>
						<div class='col-md-3 col-sm-4 col-xs-6'>
							<div class='form-group'>
								รุ่น
								<select id='shc_model' class='form-control'>{$arrs["shc_model"]}</select>
							</div>
						</div>
						<div class='col-md-3 col-md-offset-6 col-sm-4 col-sm-offset-4 col-xs-6'>
							<div class='form-group'>
								แบบ
								<select id='shc_baab' class='form-control'>{$arrs["shc_baab"]}</select>
							</div>
						</div>
						<div class='col-xs-12 col-sm-6'>
							<div class='form-group'>
								สี
								<select id='shc_color' multiple='multiple' size='10' name='duallistbox_demo1[]'>{$arrs["shc_color"]}</select>
							</div>
						</div>			
						<div class='col-xs-12 col-sm-6'>
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
					<div class='col-sm-2 col-sm-offset-10'>	
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
						<td><span style='max-height:150px;overflow:auto;'>".str_replace(",","<br>",$row->COLOR)."</span></td>
						<td><span style='max-height:150px;overflow:auto;'>".str_replace(",","<br>",$row->LOCAT)."</span></td>
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
					/*
					declare @ID bigint,@COLORcnt int,@LOCATcnt int;
					declare @datetime datetime = getdate();
					
					set @ID=null;set @COLORcnt=null;set @LOCATcnt=null;
					set @COLORcnt = (
						select count(*) from {$this->MAuth->getdb('STDSHCARColors')} b 
						where b.ID in(
							select ID from {$this->MAuth->getdb('STDSHCAR')}
							where MODEL='{$model}' and BAAB='{$baab}' and MANUYR='{$year}' and GCODE='{$gcode}'
						) and b.COLOR in ('".($color == "" ? "ALL":str_replace(",","','",$color))."')
					);
					set @LOCATcnt = (
						select count(*) from {$this->MAuth->getdb('STDSHCARLocats')} b 
						where b.ID in(
							select ID from {$this->MAuth->getdb('STDSHCAR')}
							where MODEL='{$model}' and BAAB='{$baab}' and MANUYR='{$year}' and GCODE='{$gcode}'
						) and b.LOCAT in ('".($locat == "" ? "ALL":str_replace(",","','",$locat))."')
					);
					
					if(isnull(@COLORcnt,0) = 0 or isnull(@LOCATcnt,0) = 0)
					*/
				
					declare @ID bigint,@COLORcnt int,@LOCATcnt int,@seem int = 0;
					declare @datetime datetime = getdate();
					
					declare Datatable cursor for
					select ID from {$this->MAuth->getdb('STDSHCAR')}
					where MODEL='{$model}' and BAAB='{$baab}' and MANUYR='{$year}' and GCODE='{$gcode}'
					
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
					where MODEL='{$model}' and BAAB='{$baab}' and MANUYR='{$year}' and GCODE='{$gcode}'
					
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
							where ID='{$stdid}' and (NPRICE<>'{$nprice}' or OPRICE<>'{$oprice}'))
						begin
							update {$this->MAuth->getdb('STDSHCARDetails')} 
							set NPRICE='{$nprice}'
								,OPRICE='{$oprice}'
								,UPDBY='{$this->sess["IDNo"]}'
								,UPDDT=getdate()
							where ID='{$stdid}'
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
						case 'H': $arr_data[$arrs["now"]][$column] = explode("\n",$data_value); break;
						case 'I': $arr_data[$arrs["now"]][$column] = explode("\n",$data_value); break;
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
	
	function import_save(){
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
							select * from {$this->MAuth->getdb('STDSHCAR')}
							where INSBY='{$this->sess["IDNo"]}' and INSDT='{$row->msg}'
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
										<td>".$row->GCODE."</td>
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
	
}

































