<?php
error_reporting(E_STRICT);
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
class CReportGroup extends MY_Controller {
	private $sess = array();
	
	function __construct(){
		parent::__construct();
		
		$sess = $this->session->userdata('cbjsess001');
		if(!$sess){ redirect(base_url("welcome/"),"_parent"); }else{
			foreach ($sess as $key => $value) {
                $this->sess[$key] = $value;
            }
		}
		
		$this->load->model('MDATA');
	}
	
	function index(){
		$claim = $this->MLogin->getclaim(uri_string());
		if($claim['m_access'] != "T"){ echo "<div align='center' style='color:red;font-size:16pt;width:100%;'>ขออภัย คุณยังไม่มีสิทธิเข้าใช้งานหน้านี้ครับ</div>"; exit; }
		
		$html = "
			<div class='tab1' name='home' locat='{$this->sess['branch']}' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}'>
				<div>
					<div class='col-xs-6 col-sm-3'>	
						<div class='form-group'>
							เลขตัวถัง
							<input type='text' id='STRNO' class='form-control input-sm' placeholder='เลขตัวถัง'>
						</div>
					</div>
					<div class='col-xs-6 col-sm-3'>	
						<div class='form-group'>
							วันที่เปลี่ยน จาก
							<input type='text' id='SDATE' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' placeholder='วันที่เปลี่ยน จาก'>
						</div>
					</div>
					<div class='col-xs-6 col-sm-3'>	
						<div class='form-group'>
							วันที่เปลี่ยน ถึง
							<input type='text' id='TDATE' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' placeholder='วันที่เปลี่ยน ถึง'>
						</div>
					</div>
					<div class='col-xs-6 col-sm-3'>	
						<div class='form-group'>
							ผู้ทำรายการ
							<input type='text' id='USERS' class='form-control input-sm' placeholder='ผู้ทำรายการ' value=''>
						</div>
					</div>
					
					<div class='col-xs-12 col-sm-12'>	
						<button id='btnt1RpGroup' class='btn btn-sm btn-primary btn-block'><span class='glyphicon glyphicon-search'> ค้นหา</span></button>
					</div>
				</div>
				<div class='col-sm-12'>
					<br>
					<div id='result'></div>
				</div>
			</div>
		";

		$html.= "<script src='".base_url('public/js/SYS02/CReport/CReportGroup.js')."'></script>";
		echo $html;		
	}
	
	public function search(){
		$arrs = array();
		$arrs["STRNO"] 	= $_POST["STRNO"];
		$arrs["SDATE"] 	= $this->Convertdate(1,$_POST["SDATE"]);
		$arrs["TDATE"] 	= $this->Convertdate(1,$_POST["TDATE"]);
		$arrs["USERS"] 	= $_POST["USERS"];
				
		$cond = "";
		$condDesc = "";
		if($arrs["STRNO"] != ""){
			$condDesc .= " เลขตัวถัง ".$arrs["STRNO"];
			$cond .= " and a.STRNO like '".$arrs["STRNO"]."%'  collate thai_cs_as ";
		}else{
			$condDesc .= " เลขตัวถัง ทั้งหมด";
		}
		
		if($arrs["SDATE"] != "" and $arrs["TDATE"] != ""){
			$condDesc .= " วันที่เปลี่ยนกลุ่มรถ ระหว่างวันที่ ".$this->Convertdate(2,$arrs["SDATE"])." ถึงวันที่ ".$this->Convertdate(2,$arrs["TDATE"]);
			$cond .= " and a.dt between '".$arrs["SDATE"]."' and '".$arrs["TDATE"]."' ";
		}else if($arrs["SDATE"] != "" and $arrs["TDATE"] == ""){
			$condDesc .= " วันที่เปลี่ยนกลุ่มรถ วันที่ ".$this->Convertdate(2,$arrs["SDATE"]);
			$cond .= " and a.dt = '".$arrs["SDATE"]."' ";
		}else if($arrs["SDATE"] == "" and $arrs["TDATE"] != ""){
			$condDesc .= " วันที่เปลี่ยนกลุ่มรถ วันที่ ".$this->Convertdate(2,$arrs["SDATE"]);
			$cond .= " and a.dt = '".$arrs["TDATE"]."' ";
		}else{
			$condDesc .= " วันที่เปลี่ยนกลุ่มรถ ทั้งหมด";
		}
		
		if($arrs["USERS"] != ""){
			$condDesc .= " ผู้ทำรายการ ".$arrs["USERS"];
			$cond .= " and a.username like '%".$arrs["USERS"]."%'";
		}else{
			$condDesc .= " ผู้ทำรายการ ทั้งหมด";
		}
		
		$condDesc .= ($cond == "" ? " แสดงรายการ 5,000 อันดับแรก":"");
		
		$sql = "
			if OBJECT_ID('tempdb..#tempstr') is not null drop table #tempstr;

			select substring(STRNO,0,charindex(',',STRNO)) as STRNO
				,case when substring(GCODE,0,charindex(',',GCODE)) like '%.%'
					then substring((substring(GCODE,0,charindex(',',GCODE))),0,charindex('.',GCODE))
					else substring(GCODE,0,charindex(',',GCODE))
				 end as GCODE	
				,'คุณ'+us.firstName+' '+us.lastName username,userId
				,convert(varchar(8),dateTimeTried,112) as dt
				,convert(varchar(5),dateTimeTried,108) as tm
				,ipAddress
				,positionName
				into #tempstr
			from (
				select replace(postReq,substring(postReq,0,20),'') STRNO
					,replace(postReq,substring(postReq,0,charindex('GCODE',postReq)+9),'') GCODE
					,*
				from {$this->MAuth->getdb('hp_UserOperationLog')}
				--from YTKManagement.dbo.hp_UserOperationLog
				where functionName='CHomenew::setTypecars'
			) data
			left join {$this->MAuth->getdb('hp_vusers_all')} us on data.userId=us.IDNo 			
			order by STRNO asc,dateTimeTried asc
		";
		//echo $sql; //exit;
		$this->db->query($sql);
		$sql = "
			select top 5000 ROW_NUMBER() over(partition by a.STRNO order by a.STRNO,a.dt,a.tm) r,a.*,c.GDESC from #tempstr a 
			inner join {$this->MAuth->getdb('INVTRAN')} b on a.STRNO=b.STRNO collate thai_cs_as
			left join {$this->MAuth->getdb('SETGROUP')} c on a.GCODE=c.GCODE collate thai_cs_as
			where 1=1 ".$cond."
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		$NRow = 1;
		if($query->row()){
			foreach($query->result() as $row){
				$html .= "
					<tr>
						<td>".$row->STRNO."</td>
						<td>".$row->r."</td>
						<td>".$row->GCODE." ".$row->GDESC."</td>
						<td>".$row->username."</td>
						<td>".$row->userId."</td>
						<td>".$row->positionName."</td>
						<td>".$this->Convertdate(2,$row->dt)." ".$row->tm."</td>
						<td>".$row->ipAddress."</td>
					</tr>
				";
			}
		}
		
		$html = "
			<div id='table-fixed-reportgroup' class='col-sm-12' style='height:calc(100%);width:100%;overflow:auto;font-size:8pt;'>
				<table id='table-reportgroup' class='table table-bordered' cellspacing='0' width='calc(100% - 1px)'>
					<thead style='background: rgba(0, 0, 0, 0) url(&#39;../public/lobiadmin-master/version/1.0/ajax/img/bg/bg6.png&#39;) repeat scroll 0% 0%;'>
						<tr>
							<th colspan='8' class='text-center' style='font-size:12pt;border:0px;'> 
								รายงานการเปลี่ยนกลุ่มรถ
							</th>
						</tr>
						<tr>
							<th colspan='8' class='text-center' style='border:0px;'>
								ออกรายงานโดย ".$this->sess["name"]." &emsp; ณ วันที่ ".$this->MDATA->sysdt()."
							</th>
						</tr>
						<tr>
							<th colspan='8' class='text-center' style='border:0px;color:#666;'>
								เงื่อนไข :: ".$condDesc."
							</th>
						</tr>
						<tr>
							<th style='vertical-align:middle;border:0px;'>เลขตัวถัง</th>
							<th style='vertical-align:middle;border:0px;'>ครั้งที่</th>
							<th style='vertical-align:middle;border:0px;'>กลุ่ม</th>
							<th style='vertical-align:middle;border:0px;'>ผู้ทำรายการ</th>
							<th style='vertical-align:middle;border:0px;'>เลข ปชช.</th>
							<th style='vertical-align:middle;border:0px;'>ตำแหน่ง</th>
							<th style='vertical-align:middle;border:0px;'>วันที่ทำรายการ</th>
							<th style='vertical-align:middle;border:0px;'>เครื่อง</th>
						</tr>
					</thead>	
					<tbody style='height: 10px !important; overflow: scroll;'>
						".$html."
					</tbody>					
				</table>
			</div>
		";
		
		$response = array("html"=>$html);
		echo json_encode($response);
	}
	
	
}




















