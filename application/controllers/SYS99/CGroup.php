<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/********************************************************
             ______@26/02/2019______
            / / _ _   _ _     __ 
           / // __ \ / __ \ / __ \
       _ _/ // /_/ // / / // /_/ /
     /_ _ _/ \_ _ //_/ /_/ \__  /
                          _ _/ /
                         /___ /
********************************************************/
class CGroup extends MY_Controller {
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
		
		$getdb = $this->param('database');
		$selectdb = "";
		
		for($i=0;$i<sizeof($getdb);$i++){
			for($j=0;$j<sizeof($getdb[$i]);$j++){
				$selectdb .= "<option value='".$getdb[$i][$j]."' ".($getdb[$i][$j] == $this->sess['db'] ? 'selected':'').">".$getdb[$i][$j]."</option>";
			}
		}
		$selectdb .= "<option value='YTKManagement'>YTKManagement</option>";
		
		$html = "
			<div class='tab1' name='home' locat='{$this->sess['branch']}' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}' style='height:calc(100vh - 132px);overflow:auto;background-color:white;'>
				<div style='height:65px;overflow:auto;'>					
					<div class='col-xs-2 col-sm-2'>	
						<div class='form-group'>
							ห้อง
							<select id='dblocat' class='form-control input-sm chosen-select' data-placeholder='สถานะ'>
								".$selectdb."
							</select>
						</div>
					</div>
					<div class='col-xs-2 col-sm-2'>	
						<div class='form-group'>
							รหัสกลุ่ม
							<input type='text' id='groupCode' class='form-control input-sm' placeholder='รหัสกลุ่ม' >
						</div>
					</div>
					
					<div class='col-xs-2 col-sm-2'>	
						<div class='form-group'>
							ชื่อกลุ่ม
							<input type='text' id='groupName' class='form-control input-sm' placeholder='ชื่อกลุ่ม' >
						</div>
					</div>
					
					<div class='col-xs-2 col-sm-1'>	
						<div class='form-group'>
							<br>
							<input type='button' id='btnt1search' class='btn btn-primary btn-sm' value='แสดง' style='width:100%'>
						</div>
					</div>
					<div class='col-xs-2 col-sm-1'>	
						<div class='form-group'>
							<br>
							<input type='button' id='btnt1addUsers' class='btn btn-cyan btn-sm' value='เพิ่ม' style='width:100%'>
						</div>
					</div>
				</div>
				<div id='resultt1group' style='height:calc(100% - 65px);overflow:auto;background-color:white;'></div>
			</div>
			<div class='tab2' style='height:calc(100vh - 132px);width:100%;overflow:auto;background-color:white;'>
				<div id='resultt2group' class='col-sm-12' style='height:calc(100% - 65px);overflow:auto;'></div>
				<div id='resultt2footer' class='col-sm-12' style='height:30px;'>
					<div class='row'>
						<div class='col-sm-1'>	
							<div class='form-group'>
								<br>
								<input type='button' id='btnt2home' class='btn btn-inverse btn-sm' value='หน้าแรก' style='width:100%'>
							</div>
						</div>
						<div class='col-sm-1 col-sm-offset-10'>	
							<div class='form-group'>
								<br>
								<input type='button' id='btnt2save' class='btn btn-primary btn-sm' value='บันทึก' style='width:100%'>
							</div>
						</div>
					</div>
				</div>
			</div>
		";
		
		$html.= "<script src='".base_url('public/js/SYS99/CGroup.js')."'></script>";
		echo $html;
	}
	
	function search(){
		$arrs = array();
		$arrs['dblocat'] = $_REQUEST['dblocat'];
		$arrs['groupCode'] = $_REQUEST['groupCode'];
		$arrs['groupName'] = $_REQUEST['groupName'];
		
		$cond = "";
		if($arrs['groupCode'] != ""){
			$cond .= " and groupCode like '%".$arrs['groupCode']."%'";
		}
		
		if($arrs['groupName'] != ""){
			$cond .= " and groupName like '%".$arrs['groupName']."%'";
		}
		
		
		$sql = "
			select * from YTKManagement.dbo.hp_groupuser			
			where 1=1 ".$cond."
			order by groupCode desc
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		$NRow = 1;
		if($query->row()){
			foreach($query->result() as $row){
				$html .= "
					<tr class='trow' seq=".$NRow.">						
						<td class='getit' seq=".$NRow++." groupCode='".$row->groupCode."' style='width:50px;cursor:pointer;text-align:center;'>
							<b><i class='glyphicon glyphicon-check' style='z-index:20;'></i></b>
						</td>
						<td>".$row->groupCode."</td>
						<td>".$row->groupName."</td>
					</tr>
				";
			}
		}
		
		$html = "
			<div id='table-fixed-CGroup' class='col-sm-12' style='height:100%;width:100%;overflow:auto;'>
				<table id='table-CGroup' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='100%'>
					<thead>
						<tr><th id='tab1dblocat' dblocat='".$arrs['dblocat']."' colspan='6'>".$arrs['dblocat']."</th></tr>
						<tr>
							<th style='vertical-align:middle;'>#</th>
							<th style='vertical-align:middle;'>รหัสกลุ่ม</th>
							<th style='vertical-align:middle;'>ชื่อกลุ่ม</th>
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
	
	public function getClaimGroup(){
		$arrs = array();
		$arrs['dblocat'] 	= $_REQUEST['dblocat'];
		$arrs["groupCode"] 	= $_REQUEST["groupCode"];
		$arrs["keyword"] 	= ($_REQUEST["keyword"] == "" ? "%" : $_REQUEST["keyword"]);
		$arrs["menustat"]	= $_REQUEST["menustat"];
		
		$sql = "
			select a.menuid,a.menuname,a.menulevel,a.menuicon,a.menulink 
				,isnull(b.m_access,'X') as maccess
				,isnull(b.m_insert,'X') as minsert
				,isnull(b.m_update,'X') as mupdate
				,isnull(b.m_delete,'X') as mdelete
				,isnull(c.m_access,'X') as m_access
				,isnull(c.m_insert,'X') as m_insert
				,isnull(c.m_update,'X') as m_update
				,isnull(c.m_delete,'X') as m_delete
				,c.groupCode
				,c.dblocat
			from YTKManagement.dbo.hp_menu a
			left join YTKManagement.dbo.hp_groupuser_detail b on a.menuid=b.menuid and b.groupCode='MOD' and b.dblocat='HIC2SHORTL'
			left join YTKManagement.dbo.hp_groupuser_detail c on a.menuid=c.menuid and c.groupCode='".$arrs["groupCode"]."' and c.dblocat='".$arrs['dblocat']."'
			where a.menuid like 'SYS%' ".($arrs["menustat"] == "Y" ? " and b.menuid is not null" : ($arrs["menustat"] == "N" ? " and b.menuid is null" : ""))."
				and (a.menuid like '".$arrs["keyword"]."' or a.menuname like '".$arrs["keyword"]."')
			order by a.menurank
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$html .= "
					<tr>
						<td>".$row->menuid."</td>
						<td style='padding-left:".($row->menulevel * 2)."em;'><i class='".$row->menuicon."' aria-hidden='true'>&nbsp;".$row->menuname."</i></td>
						<td align='center'>".$row->menulevel."</td>
						<td align='center'>
							<input type='checkbox' class='access' 
								groupCode='".$row->groupCode."' 
								menuid='".$row->menuid."' 
								default='".($row->maccess == "X" ? "X" : "F")."' 
								".($row->maccess == "X" ? "disabled":"")."
								".($row->m_access == "T" ? "checked" : "").">
						</td>
						<td align='center'>
							<input type='checkbox' class='insert' 
								groupCode='".$row->groupCode."' 
								menuid='".$row->menuid."' 
								default='".($row->minsert == "X" ? "X" : "F")."' 
								".($row->minsert == "X" ? "disabled":"")."
								".($row->m_insert == "T" ? "checked" : "").">
						</td>
						<td align='center'>
							<input type='checkbox' class='update' 
								groupCode='".$row->groupCode."' 
								menuid='".$row->menuid."' 
								default='".($row->mupdate == "X" ? "X" : "F")."'
								".($row->mupdate == "X" ? "disabled":"")."
								".($row->m_update == "T" ? "checked" : "").">								
						</td>
						<td align='center'>
							<input type='checkbox' class='delete' 
								groupCode='".$row->groupCode."' 
								menuid='".$row->menuid."' 
								default='".($row->mdelete == "X" ? "X" : "F")."' 
								".($row->mdelete == "X" ? "disabled":"")."
								".($row->m_delete == "T" ? "checked" : "").">
						</td>
					</tr>
				";
			}
		}else{
			$html .= "
				<tr>
					<td colspan='8' class='text-center'>ไม่พบข้อมูลตามเงื่อนไข</td>
				</tr>
			";
		}
		
		$html = "
			<div id='table-fixed-CGroupDetail' class='col-sm-12' style='height:100%;width:100%;overflow:auto;'>
				<table id='table-CGroupDetail' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='100%'>
					<thead>
						<tr>
							<th id='tab2dbData' dblocat='".$arrs['dblocat']."' groupCode='".$arrs['groupCode']."' colspan='8'>
								<div class='col-sm-2'>
									".$arrs['groupCode']."/".$arrs['dblocat']."
								</div>
								<div class='col-sm-8'>
									<input type='text' id='keyword' class='form-control' value='".($arrs["keyword"] == "%" ? "" : $arrs["keyword"])."' placeholder='รหัสเมนู/ชื่อเมนู'>
								</div>	
								<div class='col-sm-2'>
									<select id='menustat' class='form-control'>
										<option value='A' ".($arrs["menustat"] == "A" ? "selected":"").">ทั้งหมด</option>
										<option value='Y' ".($arrs["menustat"] == "Y" ? "selected":"").">ใช้งาน</option>
										<option value='N' ".($arrs["menustat"] == "N" ? "selected":"").">ไม่ใช้งาน</option>
									</select>
								</div>	
							</th>
						</tr>
						<tr>
							<th style='vertical-align:middle;'>รหัสเมนู</th>
							<th style='vertical-align:middle;'>ชื่อเมนู</th>
							<th style='vertical-align:middle;'>level</th>
							<th style='vertical-align:middle;'>มีสิทธิ์เข้าถึง</th>
							<th style='vertical-align:middle;'>มีสิทธิ์เพิ่มข้อมูล</th>
							<th style='vertical-align:middle;'>มีสิทธิ์แก้ไข</th>
							<th style='vertical-align:middle;'>มีสิทธิ์ลบ</th>
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
	
	public function getFormClaimADD(){
		$arrs = array();
		$arrs["dblocat"]   = $_REQUEST["dblocat"];
		$arrs["groupCode"] = $_REQUEST["groupCode"];
		
		$html = "
			<div style='background-color:#ccc;height:calc(100vh - 120px);'>
				<div style='height:70px;overflow:auto;'>
					<div class='row col-sm-12'>
						<div class='col-sm-4'>	
							<div class='form-group'>
								รหัสเมนู
								<input type='text' id='w1menuid' class='form-control input-sm' placeholder='รหัสเมนู'>
							</div>
						</div>
						<div class='col-sm-6'>	
							<div class='form-group'>
								ชื่อเมนู
								<input type='text' id='w1menuname' class='form-control input-sm' placeholder='ชื่อเมนู'>
							</div>
						</div>
						
						<div class='col-sm-2 col-sm-offset-0'>	
							<div class='form-group'>
								<br>
								<button id='btnw1search' class='btn btn-primary btn-sm' style='width:100%'>แสดง</button>
							</div>
						</div>
					</div>
				</div>
				
				<div id='w1resultSearch' style='height:calc(100% - 150px);overflow:auto;background-color:white;'></div>
			</div>
		";
		
		$response = array("html"=>$html);
		echo json_encode($response);
	}
	
	public function getMenu(){
		$arrs = array();
		$arrs["dblocat"]   = $_REQUEST["dblocat"];
		$arrs["groupCode"] = $_REQUEST["groupCode"];
		$arrs["menuid"]    = $_REQUEST["menuid"];
		$arrs["menuname"]  = $_REQUEST["menuname"];
		
		$sql = "
			select a.menuid,a.menuname,a.menulevel,a.menuicon
				,b.menuid
			from YTKManagement.dbo.hp_menu a
			left join YTKManagement.dbo.hp_groupuser_detail b on a.menuid=b.menuid and b.groupCode='".$arrs["groupCode"]."' and b.dblocat='".$arrs["dblocat"]."'
			where a.menuid like '".$arrs["menuid"]."%' and a.menuname like '".$arrs["menuname"]."%'
			order by a.menurank
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$html .= "
					<tr>
						<td></td>
						<td>".$row->menuid."</td>
						<td style='padding-left:".($row->menulevel * 2)."em;'><i class='".$row->menuicon."' aria-hidden='true'>&nbsp;".$row->menuname."</i></td>
					</tr>
				";
			}
		}else{
			$html .= "
				<tr>
					<td colspan='3' class='text-center'>ไม่พบข้อมูลตามเงื่อนไข</td>
				</tr>
			";
		}
		
		$html = "
			<div id='table-fixed-CGroupDetail' class='col-sm-12' style='height:100%;width:100%;overflow:auto;'>
				<table id='table-CGroupDetail' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='100%'>
					<thead>
						<tr>
							<th style='vertical-align:middle;'>#</th>
							<th style='vertical-align:middle;'>รหัสกลุ่ม</th>
							<th style='vertical-align:middle;'>รหัสเมนู</th>
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
	
	public function setClaim_Groupusers(){
		$arrs = array();
		$arrs["dblocat"]    = $_REQUEST["dblocat"];
		$arrs["groupCode"]  = $_REQUEST["groupCode"];
		$arrs["data"]  		= $_REQUEST["data"];
		
		$size = sizeof($arrs["data"]);
		$q 	  = "";
		$success = 0;
		$fail = 0;
		for($i=0;$i < $size;$i++){
			if($arrs["data"][$i][5] != 'X'){
				$q = "
					if object_id('tempdb..#tempClaim') is not null drop table #tempClaim;
					create table #tempClaim (id varchar(5),msg varchar(max));
					
					begin tran ins
					begin try
						declare @menu varchar(30)		= '".$arrs["data"][$i][0]."';
						declare @access varchar(30)		= '".($arrs["data"][$i][5] == "X" ? $arrs["data"][$i][5] : $arrs["data"][$i][1])."';
						declare @insert varchar(30)		= '".($arrs["data"][$i][6] == "X" ? $arrs["data"][$i][6] : $arrs["data"][$i][2])."';
						declare @update varchar(30)		= '".($arrs["data"][$i][7] == "X" ? $arrs["data"][$i][7] : $arrs["data"][$i][3])."';
						declare @delete varchar(30)		= '".($arrs["data"][$i][8] == "X" ? $arrs["data"][$i][8] : $arrs["data"][$i][4])."';
						declare @dblocat varchar(30)	= '".$arrs["dblocat"]."';
						declare @groupCode varchar(30)	= '".$arrs["groupCode"]."';
						
						if ((
							select count(*) from YTKManagement.dbo.hp_groupuser_detail 
							where groupCode=@groupCode and dblocat=@dblocat and menuid=@menu
						) > 0 )
						begin
							update YTKManagement.dbo.hp_groupuser_detail 
							set m_access=@access
								,m_insert=@insert
								,m_update=@update
								,m_delete=@delete
							where groupCode=@groupCode and dblocat=@dblocat and menuid=@menu
						end
						else 
						begin
							begin
								insert into YTKManagement.dbo.hp_groupuser_detail (groupCode,dblocat,menuid,m_access,m_insert,m_update,m_delete)
								select @groupCode,@dblocat,@menu,@access,@insert,@update,@delete;
							end
						end
						
						insert into #tempClaim select 'Y','แก้ไขสิทธิ์เรียบร้อยแล้วครับ';
						commit tran ins;
					end try
					begin catch
						rollback tran ins;
						insert into #tempClaim select 'N',ERROR_MESSAGE();
					end catch
				";
				
				//echo $q;
				$this->db->query($q);
				
				$sql = "select * from #tempClaim";
				$query = $this->db->query($sql);
				
				if($query->row()){
					foreach($query->result() as $row){
						if($row->id == "Y"){
							$success++;
						}else{
							$fail++;
						}
					}
				}else{
					$fail++;
				}
			}
		}
		//exit;
		$response = array();
		$response["success"] = 'สำเร็จ ('.$success.' - '.$fail.') แก้ไขสิทธิ์เรียบร้อยแล้วครับ';		
		echo json_encode($response);
	}
}




















