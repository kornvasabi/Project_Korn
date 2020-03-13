<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/********************************************************
             ______@27/08/2019______
            / / _ _   _ _     __ 
           / // __ \ / __ \ / __ \
       _ _/ // /_/ // / / // /_/ /
     /_ _ _/ \_ _ //_/ /_/ \__  /
                          _ _/ /
                         /___ /
********************************************************/
class Analyze extends MY_Controller {
	private $sess = array(); 
	
	function __construct(){
		parent::__construct();
		/*Additional code which you want to run automatically in every function call */
		$sess = $this->session->userdata('cbjsess001');
		if(!$sess){ 
			redirect(base_url("welcome/"),"_parent"); 
		}else{ foreach ($sess as $key => $value) { $this->sess[$key] = $value; } }
	}
	
	function index(){		
		$claim = $this->MLogin->getclaim(uri_string());
		if($claim['m_access'] != "T"){ echo "<div align='center' style='color:red;font-size:16pt;width:100%;'>ขออภัย คุณยังไม่มีสิทธิเข้าใช้งานหน้านี้ครับ</div>"; exit; }
		// style='height:calc(100vh - 132px);overflow:auto;background-color:white;'
		// $this->load->library('user_agent'); print_r($this->agent); exit;
		//echo $this->sess['is_mobile']; exit;
		$html = "
			<div class='tab1' name='home' locat='{$this->sess['branch']}' is_mobile='{$this->sess['is_mobile']}' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}'>
				<div class='divcondition col-sm-12'>
					<div class='row'>
						<div class='col-sm-2'>	
							<div class='form-group'>
								เลขตัวถัง
								<input type='text' id='SSTRNO' class='form-control input-sm' placeholder='เลขตัวถัง' >
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								รุ่น
								<input type='text' id='SMODEL' class='form-control input-sm' placeholder='รุ่น' >
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								วันที่สร้าง จาก
								<input type='text' id='SCREATEDATEF' class='form-control input-sm' placeholder='จาก'  data-provide='datepicker' data-date-language='th-th' value='".$this->today('startofmonthB1')."'>
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								วันที่สร้าง ถึง
								<input type='text' id='SCREATEDATET' class='form-control input-sm' placeholder='ถึง'  data-provide='datepicker' data-date-language='th-th' value='".$this->today('endofmonth')."'>
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								วันที่อนุมัติ จาก
								<input type='text' id='SAPPROVEF' class='form-control input-sm' placeholder='จาก'  data-provide='datepicker' data-date-language='th-th' value=''>
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								วันที่อนุมัติ ถึง
								<input type='text' id='SAPPROVET' class='form-control input-sm' placeholder='ถึง'  data-provide='datepicker' data-date-language='th-th' value=''>
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								เลขที่บิลจอง
								<input type='text' id='SRESVNO' class='form-control input-sm' placeholder='เลขที่บิลจอง' >
							</div>
						</div>	
						
						<div class='col-sm-2'>	
							<div class='form-group'>
								ชื่อ-สกุล ผู้เช่าซื้อ
								<input type='text' id='SCUSNAME' class='form-control input-sm' placeholder='ชื่อ-สกุล ผู้เช่าซื้อ' >
							</div>
						</div>
						
						<div class='col-sm-2'>	
							<div class='form-group'>
								สถานะใบวิเคราะห์
								<select id='SANSTAT' class='form-control input-sm select2'>
									<option value=''>ทั้งหมด</option>
									<option value='A'>อนุมัติ</option>
									<option value='N'>ไม่อนุมัติ</option>
									<option value=\"P','PP\">รออนุมัติ</option>
									<option value='I'>สร้างคำร้อง</option>
									<option value='C'>ยกเลิก</option>
								</select>	
							</div>
						</div>
						
						<div class='col-sm-2'>	
							<div class='form-group'>
								สาขา
								<select id='LOCAT' class='form-control' title='เลือก'  multiple data-actions-box='true' data-size='8' data-live-search='true'></select>
							</div>
						</div>
					</div>
					<div class='row'>
						<div class='col-sm-6'>	
							<div class='form-group'>
								<button id='btnt1createappr' class='btn btn-cyan btn-block'><span class='glyphicon glyphicon-pencil'> สร้างรายการขออนุมัติ</span></button>
							</div>
						</div>
						<div class='col-sm-6'>	
							<div class='form-group'>
								<button id='btnt1search' class='btn btn-primary btn-block'><span class='glyphicon glyphicon-search'> ค้นหา</span></button>
							</div>
						</div>
					</div>
					
					<div id='result'></div>
				</div>
			</div>
		";
		
		$html.= "<script src='".base_url('public/js/SYS04/Analyze.js?tm='.date("his"))."'></script>";
		echo $html;
	}
	
	// function search(){
		// $url = "http://192.168.1.30:81/public/images/{$_POST['SSTRNO']}.jpg";
		// $handle = curl_init($url);
		// curl_setopt($handle,  CURLOPT_RETURNTRANSFER, TRUE);

		// /* Get the HTML or whatever is linked in $url. */
		// $response = curl_exec($handle);

		// /* Check for 404 (file not found). */
		// $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
		// if($httpCode == 404) {
			// echo json_encode(array("html"=>"not found"));
		// }else{
			// echo json_encode(array("html"=>"<image src='$url'/>"));
		// }
		
		// curl_close($handle);
	// }
	
	function search(){
		$arrs = array();
		
		$arrs['SSTRNO']	= $_POST['SSTRNO'];
		$arrs['SMODEL']	= $_POST['SMODEL'];
		$arrs['SCREATEDATEF']	= $this->Convertdate(1,$_POST['SCREATEDATEF']);
		$arrs['SCREATEDATET']	= $this->Convertdate(1,$_POST['SCREATEDATET']);
		$arrs['SAPPROVEF']	= $this->Convertdate(1,$_POST['SAPPROVEF']);
		$arrs['SAPPROVET']	= $this->Convertdate(1,$_POST['SAPPROVET']);
		$arrs['SRESVNO']	= $_POST['SRESVNO'];
		$arrs['SCUSNAME']	= $_POST['SCUSNAME'];
		$arrs['SANSTAT']	= $_POST['SANSTAT'];
		$arrs['LOCAT']		= $_POST['LOCAT'];
		
		$cond = "";
		$condDesc = "";
		$condcnd = 0;
		if($arrs['SSTRNO'] != ""){			
			$cond .= " and a.STRNO like '".$arrs['SSTRNO']."%'";
			$condDesc .= " [เลขตัวถัง :: ".$arrs['SSTRNO']."]";
		}
		if($arrs['SMODEL'] != ""){
			$cond .= " and a.MODEL like '".$arrs['SMODEL']."%'";
			$condDesc .= " [รุ่น :: ".$arrs['SMODEL']."]";
		}
		
		if($arrs['SCREATEDATEF'] != "" and $arrs['SCREATEDATET'] != ""){
			$cond .= " and convert(varchar(8),a.CREATEDATE,112) between '".$arrs['SCREATEDATEF']."' and '".$arrs['SCREATEDATET']."' ";
			$condDesc .= " [วันที่สร้าง :: ".$_POST['SCREATEDATEF']." - ".$_POST['SCREATEDATET']."]";
		}else if($arrs['SCREATEDATEF'] != "" and $arrs['SCREATEDATET'] == ""){
			$cond .= " and convert(varchar(8),a.CREATEDATE,112) = '".$arrs['SCREATEDATEF']."'";
			$condDesc .= " [วันที่สร้าง :: ".$_POST['SCREATEDATEF']."]";
		}else if($arrs['SCREATEDATEF'] == "" and $arrs['SCREATEDATET'] != ""){
			$cond .= " and convert(varchar(8),a.CREATEDATE,112) = '".$arrs['SCREATEDATET']."'";
			$condDesc .= " [วันที่สร้าง :: ".$_POST['SCREATEDATET']."]";
		}
		
		if($arrs['SAPPROVEF'] != "" and $arrs['SAPPROVET'] != ""){
			$cond .= " and convert(varchar(8),c.APPRDT,112) between '".$arrs['SAPPROVEF']."' and '".$arrs['SAPPROVET']."' ";
			$condDesc .= " [วันที่อนุมัติ :: ".$_POST['SAPPROVEF']." - ".$_POST['SAPPROVET']."]";
		}else if($arrs['SAPPROVEF'] != "" and $arrs['SAPPROVET'] == ""){
			$cond .= " and convert(varchar(8),c.APPRDT,112) = '".$arrs['SAPPROVEF']."'";
			$condDesc .= " [วันที่อนุมัติ :: ".$_POST['SAPPROVEF']."]";
		}else if($arrs['SAPPROVEF'] == "" and $arrs['SAPPROVET'] != ""){
			$cond .= " and convert(varchar(8),c.APPRDT,112) = '".$arrs['SAPPROVET']."'";
			$condDesc .= " [วันที่อนุมัติ :: ".$_POST['SAPPROVET']."]";
		}
		
		if($arrs['SRESVNO'] != ""){
			$cond .= " and a.RESVNO like '".$arrs['SRESVNO']."%'";
			$condDesc .= " [เลขที่บิลจอง :: ".$arrs['SRESVNO']."]";
		}
		
		if($arrs['SANSTAT'] != ""){
			$cond .= " and a.ANSTAT in ('".$arrs['SANSTAT']."')";
			$condDesc .= " [สถานะใบวิเคราะห์ :: ".$arrs['SANSTAT']."]";
		}
		
		if(is_array($arrs['LOCAT'])){
			$locat_size = sizeof($arrs['LOCAT']);
			if($locat_size > 0){
				$locat_all = "";
				for($i=0;$i<$locat_size;$i++){
					if($locat_all != ""){ $locat_all .= ","; }
					$locat_all .= "'".$arrs['LOCAT'][$i]."'";
				}
				$cond .= " and a.LOCAT collate thai_cs_as in ({$locat_all})";
			}
		}
		
		$sql = "
			select ".($cond == "" ? "top 20":"")." * from (
				select a.ID,(
						select CUSCOD+'<br>'+SNAM+NAME1+' '+NAME2 from {$this->MAuth->getdb('CUSTMAST')}
						where CUSCOD collate thai_cs_as in (
							select CUSCOD from {$this->MAuth->getdb('ARANALYZEREF')} where ID=a.ID and CUSTYPE=0
						)
					) as CUSNAME
					,a.STRNO,a.MODEL,a.BAAB,a.COLOR,a.STAT
					,CONVERT(varchar(8),a.CREATEDATE,112) as CREATEDATE
					,CONVERT(varchar(5),a.CREATEDATE,108) as CREATETM
					,a.LOCAT,isnull(a.RESVNO,'-') as RESVNO,a.NOPAY
					,a.ANSTAT
					,case when a.ANSTAT='I' then 'สร้างคำร้อง' 
						when a.ANSTAT='P' then 'รออนุมัติ' 
						when a.ANSTAT='PP' then 'รออนุมัติ(2)' 
						when a.ANSTAT='A' then 'อนุมัติ' 
						when a.ANSTAT='N' then 'ไม่อนุมัติ' 
						when a.ANSTAT='C' then 'ยกเลิก'  end as ANSTATDESC
					,CONVERT(varchar(8),c.APPRDT,112) as APPROVEDT
					,CONVERT(varchar(5),c.APPRDT,108) as APPROVETM
					,(select groupCode from {$this->MAuth->getdb('hp_mapusers')}
						where IDNo='{$this->sess["IDNo"]}' 
							and employeeCode='{$this->sess["employeeCode"]}' 
							and USERID='{$this->sess["USERID"]}'
					 ) as groupCode
					,isnull(a.CONTNO,'-') as CONTNO
				from {$this->MAuth->getdb('ARANALYZE')} a 
				left join {$this->MAuth->getdb('ARANALYZEAPPR')} c on a.ID=c.ID collate thai_cs_as
				where 1=1 ".$cond."
			) as data
			where data.CUSNAME like '%".$arrs['SCUSNAME']."%'
			order by data.ID
		";
		//echo $sql; exit;		
		$query = $this->db->query($sql);
		//jt2092883177
		$html = "";
		$NRow = 1;
		if($query->row()){
			foreach($query->result() as $row){
				$css = "color:black";
				if($row->ANSTAT == "A"){
					$css = "color:green";
				}else if(in_array($row->ANSTAT,array('N','C'))){
					$css = "color:red";
				}else if($row->ANSTAT == "P" || $row->ANSTAT == "PP"){
					$css = "color:#666";
				}
				
				$button = "";
				if($row->groupCode == "AL"){
					$disabled = (in_array($row->ANSTAT,array('P','PP','A','N'))? "":"disabled");
					$button = "
						<button {$disabled} class='andetail btn btn-xs btn-success glyphicon glyphicon-zoom-in'
							ANID='".$row->ID."'  
							data-toggle='tooltip'
							data-placement='top'
							data-html='true'
							data-original-title='<b style=\"padding-left:30px;\">รายละเอียด</b>'
							style='cursor:pointer;'></button>
					";
				}else{
					$disabled = (in_array($row->ANSTAT,array('I','P'))? "":"disabled");
					$button = "
						<button {$disabled} class='ansend btn btn-xs btn-primary glyphicon glyphicon-ok' 
							ANID='".$row->ID."' 
							data-toggle='tooltip'
							data-placement='top'
							data-html='true'
							data-original-title='ส่ง<br>คำร้อง'	
							style='cursor:pointer;'></button>
						<button {$disabled} class='andetail_edit btn btn-xs btn-warning glyphicon glyphicon-edit' 
							ANID='".$row->ID."' 
							data-toggle='tooltip'
							data-placement='top'
							data-html='true'
							data-original-title='แก้ไข'	
							style='cursor:pointer;'></button>
					";
				}
				
				$html .= "
					<tr style='{$css}'>
						<td>{$button}</td>
						<td>".$row->ID."</td>
						<td>".$row->CUSNAME."</td>
						<td>
							".$row->STRNO."<br>
							".$row->STAT."
						</td>
						<td>
							".$row->MODEL."<br>
							".$row->BAAB."<br>
							".$row->COLOR."
						</td>
						<td>".$row->LOCAT."</td>
						<td>
							".$row->RESVNO."<br>
							".$row->CONTNO."
						</td>
						<td>".$row->NOPAY."</td>
						<td>".$row->ANSTATDESC."</td>
						<td>
							".$this->Convertdate(2,$row->CREATEDATE)." ".$row->CREATETM."<br>
							".$this->Convertdate(2,$row->APPROVEDT)." ".$row->APPROVETM."
						</td>
					</tr>
				";
				$NRow++;
			}
		}
		
		$html = "
			<div id='table-fixed-Analyze' class='col-sm-12' style='height:calc(100% - 30px);width:100%;overflow:auto;font-size:8pt;'>
				<table id='table-Analyze' class='table table-bordered' cellspacing='0' width='calc(100% - 1px)' style='background: rgba(0, 0, 0, 0) url(&#39;../public/lobiadmin-master/version/1.0/ajax/img/bg/bg4.png&#39;) repeat scroll 0% 0%;'>
					<thead style='background: rgba(0, 0, 0, 0) url(&#39;../public/lobiadmin-master/version/1.0/ajax/img/bg/bg6.png&#39;) repeat scroll 0% 0%;'>
						<tr align='center' style='line-height:20px;'>
							<th style='vertical-align:middle;text-align:center;font-size:8pt;' colspan='14'>
								เงื่อนไข :: ".$condDesc."
							</th>
						</tr>
						<tr align='center'>
							<th>###</th>
							<th style='vertical-align:middle;'>เลขที่<br>ใบวิเคราะห์</th>
							<th style='vertical-align:middle;'>ผู้เช่าซื้อ</th>
							<th style='vertical-align:middle;'>เลขตัวถัง<br>สถานะรถ</th>
							<th style='vertical-align:middle;'>รุ่น<br>แบบ<br>สี</th>
							<th style='vertical-align:middle;'>สาขา</th>
							<th style='vertical-align:middle;'>เลขที่บิลจอง<br>เลขที่สัญญา</th>
							<th style='vertical-align:middle;'>จำนวนงวด</th>
							<th style='vertical-align:middle;'>สถานะ<br>ใบวิเคราะห์</th>
							<th style='vertical-align:middle;'>วันที่สร้าง<br>วันที่อนุมัติ</th>
						</tr>
					</thead>	
					<tbody>
						".$html."
					</tbody>
				</table>
			</div>
			<div id='table-fixed-Analyze-detail' class='col-sm-12' style='height:calc(100% - 30px);width:100%;overflow:auto;font-size:8pt;'>
			
			</div>
		";
		
		$response = array("html"=>$html,"status"=>true);
		echo json_encode($response);
	}
	
	function searchDetail(){
		$ANID = $_POST["ANID"];
		
		$sql = "
			if object_id('tempdb..#transaction') is not null drop table #transaction;
			create table #transaction (error varchar(1),id varchar(12),msg varchar(max));

			begin tran upd
			begin try
				if exists (
					select * from {$this->MAuth->getdb('ARANALYZE')} 
					where ID='{$ANID}' and ANSTAT in ('P','PP')
				)
				begin
					update {$this->MAuth->getdb('ARANALYZE')} 
					set ANSTAT='PP'
					where ID='{$ANID}'
				end 
				else if not exists (
					select * from {$this->MAuth->getdb('ARANALYZE')} 
					where ID='{$ANID}' and ANSTAT in ('P','PP','A','N')
				)
				begin
					rollback tran upd;
					insert into #transaction select 'y' as error,'' as id,'ผิดพลาด ใบวิเคราะห์สินเชื่อ เลขที่ ".$ANID." ไม่ได้อยู่ในสถานะรออนุมัติ' as msg;
					return;
				end
				
				insert into {$this->MAuth->getdb('hp_UserOperationLog')} (userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
				values ('".$this->sess["IDNo"]."','SYS04::ส่งคำร้องขออนุมัติใบวิเคราะห์สินเชื่อ เลขที่ ".$ANID."','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
				
				insert into #transaction select 'n' as error,'".$ANID."' as id,'ส่งคำร้องขออนุมัติใบวิเคราะห์สินเชื่อ <br>เลขที่ใบวิเคราะห์สินเชื่อ ".$ANID." แล้ว' as msg;
				commit tran upd;
			end try
			begin catch
				rollback tran upd;
				insert into #transaction select 'y' as error,'' as id,ERROR_MESSAGE() as msg;
			end catch
		";
		$this->db->query($sql);
		
		$sql 	= "select * from #transaction";
		$query 	= $this->db->query($sql);
		
		$stat 	= true;
		$msg  	= '';
		$ARANALYZE_ID  = '';
		
		if($query->row()) {
			foreach ($query->result() as $row) {
				if($row->error == "y"){
					$response = array("html"=>$row->msg,"error"=>true);
					echo json_encode($response); exit;
				}
			}
		}else{
			$msg = "ผิดพลาด :: ไม่สามารถทำรายการได้ในขณะนี้ โปรดลองทำรายการใหม่ภายหลัง";
			$response = array("html"=>$msg,"error"=>true);
			echo json_encode($response); exit;
		}
		
		$sql = "
			declare @filePath varchar(250) = (
				select filePath from {$this->MAuth->getdb('config_fileupload')}
				where refno = '".$this->sess["db"]."' and ftpfolder like 'Senior/%/ANALYZE' and ftpstatus = 'Y'
			);
			
			select a.ID,a.LOCAT
				,CONVERT(varchar(8),a.CREATEDATE,112) as CREATEDATE
				,CONVERT(varchar(5),a.CREATEDATE,108) as CREATETIME
				,a.RESVNO
				,a.RESVAMT as M_RESVAMT,a.DWN as M_DWN
				,a.DWN_INSURANCE as M_DWN_INSURANCE,a.NOPAY
				,a.STRNO,a.MODEL+' ('+a.BAAB+')' as MODEL,a.COLOR
				,case when a.STAT='N' then 'รถใหม่' else 'รถเก่า' end as STAT
				,CONVERT(varchar(8),a.SDATE,112) as SDATE
				,CONVERT(varchar(8),a.YDATE,112) as YDATE
				,a.PRICE as M_PRICE,a.ANSTAT,a.INSBY
				,CONVERT(varchar(8),a.INSDT,112) as INSDT
				,CONVERT(varchar(5),a.INSDT,108) as INSTM
				,CONVERT(varchar(8),c.APPRDT,112) as APPDT
				,CONVERT(varchar(5),c.APPRDT,108) as APPTM
				,case when a.ANSTAT='I' then 'รออนุมัติ' 
					when a.ANSTAT='A' then 'อนุมัติ' 
					when a.ANSTAT='N' then 'ไม่อนุมัติ' 
					when a.ANSTAT='C' then 'ยกเลิก'  end as ANSTATDESC
				,a.STDID
				,a.SUBID
				,isnull(@filePath+b.EVIDENCE,'(none)') as filePathEVIDENCE
			from {$this->MAuth->getdb('ARANALYZE')} a
			left join {$this->MAuth->getdb('ARANALYZEDATA')} b on a.ID=b.ID
			left join {$this->MAuth->getdb('ARANALYZEAPPR')} c on a.ID=c.ID collate thai_cs_as
			where a.ID='".$ANID."'
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$arrs = array("null"=>"");
		$NRow = 1;
		if($query->row()){
			foreach($query->result() as $row){
				foreach($row as $key => $val){
					switch($key){
						case 'CREATEDATE': 
							$arrs[$key] = $this->Convertdate(2,$val); 
							break;
						case 'SDATE': 
							$arrs[$key] = $this->Convertdate(2,$val); 
							break;
						case 'APPDT': 
							$arrs[$key] = $this->Convertdate(2,$val); 
							break;
						default: 
							if(substr($key,0,2) == "M_"){
								$arrs[str_replace("M_","",$key)] = number_format($val,2);
							}else{
								$arrs[$key] = $val;
							}
							break;
					}
				}
			}
		}
		
		$sql = "
			declare @filePath varchar(250) = (
				select filePath from {$this->MAuth->getdb('config_fileupload')}
				where refno = '".$this->sess["db"]."' and ftpfolder like 'Senior/%/ANALYZE' and ftpstatus = 'Y'
			);
			select a.CUSTYPE,a.CUSCOD
				,b.SNAM+b.NAME1+' '+b.NAME2 as CUSNAME 
				,b.IDNO as CUSIDNO
				,convert(varchar(10),b.BIRTHDT,103) as CUSBIRTH
				,convert(varchar(10),b.EXPDT,103) as CUSEXPDT
				,(select y from {$this->MAuth->getdb('FN_009_datediffYearMonthDay')}(b.BIRTHDT,GETDATE())) as CUSAGE
				,case when a.CUSSTAT=1 then 'โสด'
					when a.CUSSTAT=2 then 'สมรส'
					when a.CUSSTAT=3 then 'หม้าย'
					when a.CUSSTAT=4 then 'หย่า'
					when a.CUSSTAT=5 then 'แยกกันอยู่' end as CUSSTAT
				,a.CUSBABY
				,c.ADDR1+' '+c.ADDR2+' '+c.TUMB+' '+d.AUMPDES+' '+e.PROVDES+' '+c.ZIP as CUSADDR1
				,f.ADDR1+' '+f.ADDR2+' '+f.TUMB+' '+g.AUMPDES+' '+h.PROVDES+' '+f.ZIP as CUSADDR2
				,a.CAREER,a.CAREERADDR,a.CAREERTEL,a.SOCAILSECURITY
				,b.MREVENU as M_MREVENU,b.MOBILENO,a.HOSTNAME,a.HOSTIDNO,a.HOSTTEL
				,a.HOSTRELATION
				,(select sa.titleName+sa.firstName+' '+sa.lastName from {$this->MAuth->getdb('hp_vusers_all')} sa  where sa.IDNO != '' and sa.IDNO=a.EMPRELATION collate thai_cs_as) as EMPRELATION
				,a.REFERANT
				,b.GRADE
				,isnull(@filePath+convert(varchar(30),cast(i.PICTUR as varbinary)),'(none)') as filePath
			from {$this->MAuth->getdb('ARANALYZEREF')} a
			left join {$this->MAuth->getdb('CUSTMAST')} b on a.CUSCOD=b.CUSCOD collate thai_ci_as
			left join {$this->MAuth->getdb('CUSTADDR')} c on cast(a.ADDRNO as varchar)=c.ADDRNO and a.CUSCOD=c.CUSCOD collate thai_ci_as
			left join {$this->MAuth->getdb('SETAUMP')} d on c.AUMPCOD=d.AUMPCOD
			left join {$this->MAuth->getdb('SETPROV')} e on d.PROVCOD=e.PROVCOD
			left join {$this->MAuth->getdb('CUSTADDR')} f on cast(a.ADDRDOCNO as varchar)=f.ADDRNO and a.CUSCOD=f.CUSCOD collate thai_ci_as 
			left join {$this->MAuth->getdb('SETAUMP')} g on f.AUMPCOD=g.AUMPCOD
			left join {$this->MAuth->getdb('SETPROV')} h on g.PROVCOD=h.PROVCOD
			left join {$this->MAuth->getdb('CUSTPICT')} i on i.CUSCOD=a.CUSCOD collate thai_cs_as
			where a.ID='".$ANID."'
			order by a.CUSTYPE
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		if($query->row()){
			foreach($query->result() as $row){
				foreach($row as $key => $val){
					switch($key){
						default: 
							if(substr($key,0,2) == "M_"){
								$arrs[str_replace("M_","",$key)][$row->CUSTYPE] = ($val == "" ? "-":number_format($val,2));
							}else{
								$arrs[$key][$row->CUSTYPE] = ($val == "" ? "-":$val);
							}
							break;
					}
				}
			}
		}
		
		$sql = "
			select a.EMP,isnull('คุณ'+b.firstName+' '+b.lastName,a.EMP) as EMPNAME,a.EMPTEL
				,a.MNG,isnull('คุณ'+c.firstName+' '+c.lastName,a.MNG) as MNGNAME,a.MNGTEL
				,z.APPRBY as APPROVE,'คุณ'+d.firstName+' '+d.lastName as APPROVENAME,z.APPRTEL as APPROVETEL
				,z.COMMENTS as comment
			from {$this->MAuth->getdb('ARANALYZEDATA')} a
			left join {$this->MAuth->getdb('ARANALYZEAPPR')} z on a.ID=z.ID collate thai_cs_as
			left join {$this->MAuth->getdb('hp_vusers')} b on a.EMP=b.IDNo collate thai_cs_as
			left join {$this->MAuth->getdb('hp_vusers')} c on a.MNG=c.IDNo collate thai_cs_as
			left join {$this->MAuth->getdb('hp_vusers')} d on z.APPRBY=d.IDNo collate thai_cs_as
			where a.ID='".$ANID."'
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		if($query->row()){
			foreach($query->result() as $row){
				foreach($row as $key => $val){
					$arrs[$key] = ($val == "" ? "-":$val);
				}
			}
		}
		
		$css = "";
		if($arrs["ANSTAT"] == "A"){
			$css = "color:green";
		}else if($arrs["ANSTAT"] == "N"){
			$css = "color:red";
		}
		
		$csscus0 = "";
		$csscus1 = "";
		$csscus2 = "";
		$csscus3 = "";
		if(in_array($arrs["GRADE"][0],array('F','FF'))){ $csscus0 = "color:red;"; }
		if(in_array($arrs["GRADE"][1],array('F','FF'))){ $csscus1 = "color:red;"; }
		if(isset($arrs["GRADE"][2])){
			if(in_array($arrs["GRADE"][2],array('F','FF'))){ $csscus2 = "color:red;"; }			
		}
		if(isset($arrs["GRADE"][3])){
			if(in_array($arrs["GRADE"][3],array('F','FF'))){ $csscus3 = "color:red;"; }			
		}
		 
		$html = "
			<div class='col-sm-12' style='border:1px dotted #aaa;{$css}'>
				<div class='col-sm-10 col-sm-offset-1'>	
					<table  style='width:100%;font-size:10pt;'>
						<!-- tr style='background-color:#1fecff;' -->
						<tr style='background: rgba(0, 0, 0, 0) url(&#39;../public/lobiadmin-master/version/1.0/ajax/img/bg/bg6.png&#39;) repeat scroll 0% 0%;'>
							<td>
								<div class='col-sm-4'><b>เลขที่ใบวิเคราะห์ :: </b>".$arrs["ID"]."</div>
								<div class='col-sm-4 col-sm-offset-4'><b>วันที่ขออนุมัติ :: </b> ".$arrs["CREATEDATE"]." ".$arrs["CREATETIME"]."</div>
							</td>
						</tr>
						<!-- tr style='background-color:#1fecff;' -->
						<tr style='background: rgba(0, 0, 0, 0) url(&#39;../public/lobiadmin-master/version/1.0/ajax/img/bg/bg6.png&#39;) repeat scroll 0% 0%;'>
							<td>
								<div class='col-sm-4'><b>สาขา :: </b>".$arrs["LOCAT"]."</div>
								<div class='col-sm-4 col-sm-offset-4'><b>วันที่อนุมัติ :: </b> ".$arrs["APPDT"]." ".$arrs["APPTM"]."</div>
							</td>
						</tr>
						<!-- tr style='background-color:#1fecff;' -->
						<tr style='background: rgba(0, 0, 0, 0) url(&#39;../public/lobiadmin-master/version/1.0/ajax/img/bg/bg6.png&#39;) repeat scroll 0% 0%;'>
							<td>
								<div class='col-sm-4'><b>รุ่น :: </b>".$arrs["MODEL"]."</div>
								<div class='col-sm-4'><b>สถานะรถ :: </b> ".$arrs["STAT"]."</div>
								<div class='col-sm-4'><b>เลขตัวถัง :: </b> ".$arrs["STRNO"]."</div>
							</td>
						</tr>
						<!-- tr style='background-color:#1fecff;' -->
						<tr style='background: rgba(0, 0, 0, 0) url(&#39;../public/lobiadmin-master/version/1.0/ajax/img/bg/bg6.png&#39;) repeat scroll 0% 0%;'>
							<td>
								<div class='col-sm-4'><b>เงินดาวน์รถ :: </b>".$arrs["DWN"]."</div>
								<div class='col-sm-4'><b>เงินจอง :: </b> ".$arrs["RESVAMT"]."</div>
								<div class='col-sm-4'><b>วันที่ขายล่าสุด :: </b> ".$arrs["SDATE"]."</div>
							</td>
						</tr>
						<!-- tr style='background-color:#1fecff;' -->
						<tr style='background: rgba(0, 0, 0, 0) url(&#39;../public/lobiadmin-master/version/1.0/ajax/img/bg/bg6.png&#39;) repeat scroll 0% 0%;'>
							<td>
								<div class='col-sm-4'><b>เงินดาวน์ ป.1 :: </b>".$arrs["DWN_INSURANCE"]."</div>
								<div class='col-sm-4'><b>จำนวนงวด :: </b> ".$arrs["NOPAY"]."</div>
								<div class='col-sm-4'><b>วันที่ยึด :: </b> ".$arrs["YDATE"]."</div>
							</td>
						</tr>
						<tr style='background: rgba(0, 0, 0, 0) url(&#39;../public/lobiadmin-master/version/1.0/ajax/img/bg/bg6.png&#39;) repeat scroll 0% 0%;'>
							<td>
								<div class='col-sm-4'><b>บิล std :: </b>".$arrs["STDID"]."</div>
								<div class='col-sm-4'></div>
								<div class='col-sm-4'></div>
							</td>
						</tr>
						<tr style='background: rgba(0, 0, 0, 0) url(&#39;../public/lobiadmin-master/version/1.0/ajax/img/bg/bg6.png&#39;) repeat scroll 0% 0%;'>
							<td>
								<hr>
							</td>
						</tr>
						<!-- tr style='background-color:#f2ff8f;' -->
						<tr style='background: rgba(0, 0, 0, 0) url(&#39;../public/lobiadmin-master/version/1.0/ajax/img/bg/bg4.png&#39;) repeat scroll 0% 0%;'>
							<td>
								<table style='width:100%;font-size:10pt;'>
									<tr>
										<th></th>
										<th>ผู้เช่าซื้อ</th>
										<th>ผู้ค้ำประกัน 1</th>
										<th>ผู้ค้ำประกัน 2</th>
										<th>ผู้ยินยอม</th>
									</tr>
									<tr>
										<td>".(!isset($arrs["filePathEVIDENCE"])?"": ($arrs["filePathEVIDENCE"] == "(none)" ? "(none)":"<image src='".$arrs["filePathEVIDENCE"]."' style='width:180px;height:auto;'>"))."</td>
										<td>".(!isset($arrs["filePath"][0])?"": ($arrs["filePath"][0] == "(none)" ? "(none)":"<image src='".$arrs["filePath"][0]."?r=".rand()."' style='width:180px;height:auto;'>"))."</td>
										<td>".(!isset($arrs["filePath"][1])?"": ($arrs["filePath"][1] == "(none)" ? "(none)":"<image src='".$arrs["filePath"][1]."?r=".rand()."' style='width:180px;height:auto;'>"))."</td>
										<td>".(!isset($arrs["filePath"][2])?"": ($arrs["filePath"][2] == "(none)" ? "(none)":"<image src='".$arrs["filePath"][2]."?r=".rand()."' style='width:180px;height:auto;'>"))."</td>
										<td>".(!isset($arrs["filePath"][3])?"": ($arrs["filePath"][3] == "(none)" ? "(none)":"<image src='".$arrs["filePath"][3]."?r=".rand()."' style='width:180px;height:auto;'>"))."</td>
									</tr>									
									<tr>
										<th style='text-align:right;padding-right:20px;'>ชื่อ-สกุล</th>
										<td style='".$csscus0."'>".(isset($arrs["CUSNAME"][0]) ? $arrs["CUSNAME"][0]:"-")."</td>
										<td style='".$csscus1."'>".(isset($arrs["CUSNAME"][1]) ? $arrs["CUSNAME"][1]:"-")."</td>
										<td style='".$csscus2."'>".(isset($arrs["CUSNAME"][2]) ? $arrs["CUSNAME"][2]:"-")."</td>
										<td style='".$csscus3."'>".(isset($arrs["CUSNAME"][3]) ? $arrs["CUSNAME"][3]:"-")."</td>
									</tr>
									<tr>
										<th style='text-align:right;padding-right:20px;'>เลขที่บัตรประชาชน</th>
										<td style='".$csscus0."'>".(isset($arrs["CUSIDNO"][0]) ? $arrs["CUSIDNO"][0]:"-")."</td>
										<td style='".$csscus1."'>".(isset($arrs["CUSIDNO"][1]) ? $arrs["CUSIDNO"][1]:"-")."</td>
										<td style='".$csscus2."'>".(isset($arrs["CUSIDNO"][2]) ? $arrs["CUSIDNO"][2]:"-")."</td>
										<td style='".$csscus3."'>".(isset($arrs["CUSIDNO"][3]) ? $arrs["CUSIDNO"][3]:"-")."</td>
									</tr>
									<tr>
										<th style='text-align:right;padding-right:20px;'>ว.ด.ป.เกิด (คศ)</th>
										<td style='".$csscus0."'>".(isset($arrs["CUSBIRTH"][0]) ? $arrs["CUSBIRTH"][0]:"-")."</td>
										<td style='".$csscus1."'>".(isset($arrs["CUSBIRTH"][1]) ? $arrs["CUSBIRTH"][1]:"-")."</td>
										<td style='".$csscus2."'>".(isset($arrs["CUSBIRTH"][2]) ? $arrs["CUSBIRTH"][2]:"-")."</td>
										<td style='".$csscus3."'>".(isset($arrs["CUSBIRTH"][3]) ? $arrs["CUSBIRTH"][3]:"-")."</td>
									</tr>
									<tr>
										<th style='text-align:right;padding-right:20px;'>ว.ด.ป.บัตรหมดอายุ (คศ)</th>
										<td style='".$csscus0."'>".(isset($arrs["CUSEXPDT"][0]) ? $arrs["CUSEXPDT"][0]:"-")."</td>
										<td style='".$csscus1."'>".(isset($arrs["CUSEXPDT"][1]) ? $arrs["CUSEXPDT"][1]:"-")."</td>
										<td style='".$csscus2."'>".(isset($arrs["CUSEXPDT"][2]) ? $arrs["CUSEXPDT"][2]:"-")."</td>
										<td style='".$csscus3."'>".(isset($arrs["CUSEXPDT"][3]) ? $arrs["CUSEXPDT"][3]:"-")."</td>
									</tr>
									<tr>
										<th style='text-align:right;padding-right:20px;'>อายุ</th>
										<td style='".$csscus0."'>".(isset($arrs["CUSAGE"][0]) ? $arrs["CUSAGE"][0]:"-")."</td>
										<td style='".$csscus1."'>".(isset($arrs["CUSAGE"][1]) ? $arrs["CUSAGE"][1]:"-")."</td>
										<td style='".$csscus2."'>".(isset($arrs["CUSAGE"][2]) ? $arrs["CUSAGE"][2]:"-")."</td>
										<td style='".$csscus3."'>".(isset($arrs["CUSAGE"][3]) ? $arrs["CUSAGE"][3]:"-")."</td>
									</tr>
									<tr>
										<th style='text-align:right;padding-right:20px;'>สถานะภาพการสมรส</th>
										<td style='".$csscus0."'>".(isset($arrs["CUSSTAT"][0]) ? $arrs["CUSSTAT"][0]:"-")."</td>
										<td style='".$csscus1."'>".(isset($arrs["CUSSTAT"][1]) ? $arrs["CUSSTAT"][1]:"-")."</td>
										<td style='".$csscus2."'>".(isset($arrs["CUSSTAT"][2]) ? $arrs["CUSSTAT"][2]:"-")."</td>
										<td style='".$csscus3."'>".(isset($arrs["CUSSTAT"][3]) ? $arrs["CUSSTAT"][3]:"-")."</td>
									</tr>
									<tr>
										<th style='text-align:right;padding-right:20px;'>จำนวนบุตร</th>
										<td style='".$csscus0."'>".(isset($arrs["CUSBABY"][0]) ? $arrs["CUSBABY"][0]:"-")."</td>
										<td style='".$csscus1."'>".(isset($arrs["CUSBABY"][1]) ? $arrs["CUSBABY"][1]:"-")."</td>
										<td style='".$csscus2."'>".(isset($arrs["CUSBABY"][2]) ? $arrs["CUSBABY"][2]:"-")."</td>
										<td style='".$csscus3."'>".(isset($arrs["CUSBABY"][3]) ? $arrs["CUSBABY"][3]:"-")."</td>
									</tr>
									<tr>
										<th style='text-align:right;padding-right:20px;'>ที่อยู่ตาม ทบ.บ้าน</th>
										<td style='".$csscus0."max-width:150px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;'>".(isset($arrs["CUSADDR1"][0]) ? $arrs["CUSADDR1"][0]:"-")."</td>
										<td style='".$csscus1."max-width:150px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;'>".(isset($arrs["CUSADDR1"][1]) ? $arrs["CUSADDR1"][1]:"-")."</td>
										<td style='".$csscus2."max-width:150px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;'>".(isset($arrs["CUSADDR1"][2]) ? $arrs["CUSADDR1"][2]:"-")."</td>
										<td style='".$csscus3."max-width:150px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;'>".(isset($arrs["CUSADDR1"][3]) ? $arrs["CUSADDR1"][3]:"-")."</td>
									</tr>
									<tr>
										<th style='text-align:right;padding-right:20px;'>ที่อยู่ส่งเอกสาร</th>
										<td style='".$csscus0."max-width:150px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;'>".(isset($arrs["CUSADDR2"][0]) ? $arrs["CUSADDR2"][0]:"-")."</td>
										<td style='".$csscus1."max-width:150px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;'>".(isset($arrs["CUSADDR2"][1]) ? $arrs["CUSADDR2"][1]:"-")."</td>
										<td style='".$csscus2."max-width:150px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;'>".(isset($arrs["CUSADDR2"][2]) ? $arrs["CUSADDR2"][2]:"-")."</td>
										<td style='".$csscus3."max-width:150px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;'>".(isset($arrs["CUSADDR2"][3]) ? $arrs["CUSADDR2"][3]:"-")."</td>
									</tr>
									<tr>
										<th style='text-align:right;padding-right:20px;'>อาชีพ</th>
										<td style='".$csscus0."'>".(isset($arrs["CAREER"][0]) ? $arrs["CAREER"][0]:"-")."</td>
										<td style='".$csscus1."'>".(isset($arrs["CAREER"][1]) ? $arrs["CAREER"][1]:"-")."</td>
										<td style='".$csscus2."'>".(isset($arrs["CAREER"][2]) ? $arrs["CAREER"][2]:"-")."</td>
										<td style='".$csscus3."'>".(isset($arrs["CAREER"][3]) ? $arrs["CAREER"][3]:"-")."</td>
									</tr>
									<tr>
										<th style='text-align:right;padding-right:20px;'>สถานที่ทำงาน/ที่อยู่</th>
										<td style='".$csscus0."max-width:150px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;'>".(isset($arrs["CAREERADDR"][0]) ? $arrs["CAREERADDR"][0]:"-")."</td>
										<td style='".$csscus1."max-width:150px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;'>".(isset($arrs["CAREERADDR"][1]) ? $arrs["CAREERADDR"][1]:"-")."</td>
										<td style='".$csscus2."max-width:150px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;'>".(isset($arrs["CAREERADDR"][2]) ? $arrs["CAREERADDR"][2]:"-")."</td>
										<td style='".$csscus3."max-width:150px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;'>".(isset($arrs["CAREERADDR"][3]) ? $arrs["CAREERADDR"][3]:"-")."</td>
									</tr>
									<tr>
										<th style='text-align:right;padding-right:20px;'>เบอร์ติดต่อได้.ที่ทำงาน</th>
										<td style='".$csscus0."'>".(isset($arrs["CAREERTEL"][0]) ? $arrs["CAREERTEL"][0]:"-")."</td>
										<td style='".$csscus1."'>".(isset($arrs["CAREERTEL"][1]) ? $arrs["CAREERTEL"][1]:"-")."</td>
										<td style='".$csscus2."'>".(isset($arrs["CAREERTEL"][2]) ? $arrs["CAREERTEL"][2]:"-")."</td>
										<td style='".$csscus3."'>".(isset($arrs["CAREERTEL"][3]) ? $arrs["CAREERTEL"][3]:"-")."</td>
									</tr>
									<tr>
										<th style='text-align:right;padding-right:20px;'>ประกันสังคม (มี/ไม่มี)</th>
										<td style='".$csscus0."'>".(isset($arrs["SOCAILSECURITY"][0]) ? $arrs["SOCAILSECURITY"][0]:"-")."</td>
										<td style='".$csscus1."'>".(isset($arrs["SOCAILSECURITY"][1]) ? $arrs["SOCAILSECURITY"][1]:"-")."</td>
										<td style='".$csscus2."'>".(isset($arrs["SOCAILSECURITY"][2]) ? $arrs["SOCAILSECURITY"][2]:"-")."</td>
										<td style='".$csscus3."'>".(isset($arrs["SOCAILSECURITY"][3]) ? $arrs["SOCAILSECURITY"][3]:"-")."</td>
									</tr>
									<tr>
										<th style='text-align:right;padding-right:20px;'>รายได้ต่อเดือน</th>
										<td style='".$csscus0."'>".(isset($arrs["MREVENU"][0]) ? $arrs["MREVENU"][0]:"-")."</td>
										<td style='".$csscus1."'>".(isset($arrs["MREVENU"][1]) ? $arrs["MREVENU"][1]:"-")."</td>
										<td style='".$csscus2."'>".(isset($arrs["MREVENU"][2]) ? $arrs["MREVENU"][2]:"-")."</td>
										<td style='".$csscus3."'>".(isset($arrs["MREVENU"][3]) ? $arrs["MREVENU"][3]:"-")."</td>
									</tr>
									<tr>
										<th style='text-align:right;padding-right:20px;'>เบอร์โทรศัทพ์ติดต่อ.ลูกค้า</th>
										<td style='".$csscus0."'>".(isset($arrs["MOBILENO"][0]) ? $arrs["MOBILENO"][0]:"-")."</td>
										<td style='".$csscus1."'>".(isset($arrs["MOBILENO"][1]) ? $arrs["MOBILENO"][1]:"-")."</td>
										<td style='".$csscus2."'>".(isset($arrs["MOBILENO"][2]) ? $arrs["MOBILENO"][2]:"-")."</td>
										<td style='".$csscus3."'>".(isset($arrs["MOBILENO"][3]) ? $arrs["MOBILENO"][3]:"-")."</td>
									</tr>
									<tr>
										<th style='text-align:right;padding-right:20px;'>ชื่อ-สกุล (เจ้าบ้านตาม ทบ.บ้าน )</th>
										<td style='".$csscus0."'>".(isset($arrs["HOSTNAME"][0]) ? $arrs["HOSTNAME"][0]:"-")."</td>
										<td style='".$csscus1."'>".(isset($arrs["HOSTNAME"][1]) ? $arrs["HOSTNAME"][1]:"-")."</td>
										<td style='".$csscus2."'>".(isset($arrs["HOSTNAME"][2]) ? $arrs["HOSTNAME"][2]:"-")."</td>
										<td style='".$csscus3."'>".(isset($arrs["HOSTNAME"][3]) ? $arrs["HOSTNAME"][3]:"-")."</td>
									</tr>
									<tr>
										<th style='text-align:right;padding-right:20px;'>เลขที่บัตรประชาชน (เจ้าบ้าน)</th>
										<td style='".$csscus0."'>".(isset($arrs["HOSTIDNO"][0]) ? $arrs["HOSTIDNO"][0]:"-")."</td>
										<td style='".$csscus1."'>".(isset($arrs["HOSTIDNO"][1]) ? $arrs["HOSTIDNO"][1]:"-")."</td>
										<td style='".$csscus2."'>".(isset($arrs["HOSTIDNO"][2]) ? $arrs["HOSTIDNO"][2]:"-")."</td>
										<td style='".$csscus3."'>".(isset($arrs["HOSTIDNO"][3]) ? $arrs["HOSTIDNO"][3]:"-")."</td>
									</tr>
									<tr>
										<th style='text-align:right;padding-right:20px;'>เบอร์โทรศัทพ์ติดต่อ (เจ้าบ้าน)</th>
										<td style='".$csscus0."'>".(isset($arrs["HOSTTEL"][0]) ? $arrs["HOSTTEL"][0]:"-")."</td>
										<td style='".$csscus1."'>".(isset($arrs["HOSTTEL"][1]) ? $arrs["HOSTTEL"][1]:"-")."</td>
										<td style='".$csscus2."'>".(isset($arrs["HOSTTEL"][2]) ? $arrs["HOSTTEL"][2]:"-")."</td>
										<td style='".$csscus3."'>".(isset($arrs["HOSTTEL"][3]) ? $arrs["HOSTTEL"][3]:"-")."</td>
									</tr>
									<tr>
										<th style='text-align:right;padding-right:20px;'>ความสัมพันธ์กับ  เจ้าบ้าน</th>
										<td style='".$csscus0."'>".(isset($arrs["HOSTRELATION"][0]) ? $arrs["HOSTRELATION"][0]:"-")."</td>
										<td style='".$csscus1."'>".(isset($arrs["HOSTRELATION"][1]) ? $arrs["HOSTRELATION"][1]:"-")."</td>
										<td style='".$csscus2."'>".(isset($arrs["HOSTRELATION"][2]) ? $arrs["HOSTRELATION"][2]:"-")."</td>
										<td style='".$csscus3."'>".(isset($arrs["HOSTRELATION"][3]) ? $arrs["HOSTRELATION"][3]:"-")."</td>
									</tr>
									<tr>
										<th style='text-align:right;padding-right:20px;'>ความสัมพันธ์กับ พนักงาน</th>
										<td style='".$csscus0."'>".(isset($arrs["EMPRELATION"][0]) ? $arrs["EMPRELATION"][0]:"-")."</td>
										<td style='".$csscus1."'>".(isset($arrs["EMPRELATION"][1]) ? $arrs["EMPRELATION"][1]:"-")."</td>
										<td style='".$csscus2."'>".(isset($arrs["EMPRELATION"][2]) ? $arrs["EMPRELATION"][2]:"-")."</td>
										<td style='".$csscus3."'>".(isset($arrs["EMPRELATION"][3]) ? $arrs["EMPRELATION"][3]:"-")."</td>
									</tr>
									<tr>
										<th style='text-align:right;padding-right:20px;'>บุคคลอ้างอิง</th>
										<td style='".$csscus0."'>".(isset($arrs["REFERANT"][0]) ? $arrs["REFERANT"][0]:"-")."</td>
										<td style='".$csscus1."'>".(isset($arrs["REFERANT"][1]) ? $arrs["REFERANT"][1]:"-")."</td>
										<td style='".$csscus2."'>".(isset($arrs["REFERANT"][2]) ? $arrs["REFERANT"][2]:"-")."</td>
										<td style='".$csscus3."'>".(isset($arrs["REFERANT"][3]) ? $arrs["REFERANT"][3]:"-")."</td>
									</tr>
									<tr>
										<th style='text-align:right;padding-right:20px;'>อยู่ในกลุ่มเสี่ยงหรือไม่</th>
										<td style='".$csscus0."'>".(isset($arrs["GRADE"][0]) ? $arrs["GRADE"][0]:"-")."</td>
										<td style='".$csscus1."'>".(isset($arrs["GRADE"][1]) ? $arrs["GRADE"][1]:"-")."</td>
										<td style='".$csscus2."'>".(isset($arrs["GRADE"][2]) ? $arrs["GRADE"][2]:"-")."</td>
										<td style='".$csscus3."'>".(isset($arrs["GRADE"][3]) ? $arrs["GRADE"][3]:"-")."</td>
									</tr>									
									<tr>
										<th style='text-align:right;padding-right:20px;'></th>
										<td style='".$csscus0."'>
											".(isset($arrs["CUSCOD"][0]) ? "<input type='button' class='cushistory' cuscod='".$arrs["CUSCOD"][0]."' class='btn btn-xs btn-block' value='ประวัติการซื้อ'>":"")."
										</td>
										<td style='".$csscus1."'>										
											".(isset($arrs["CUSIDNO"][1]) ? "<input type='button' class='cushistory' cuscod='".$arrs["CUSCOD"][1]."' class='btn btn-xs btn-block' value='ประวัติการซื้อ'>":"")."
										</td>
										<td style='".$csscus2."'>
											".(isset($arrs["CUSIDNO"][2]) ? "<input type='button' class='cushistory' cuscod='".$arrs["CUSCOD"][2]."' class='btn btn-xs btn-block' value='ประวัติการซื้อ'>":"")."
										</td>
										<td style='".$csscus3."'>
											".(isset($arrs["CUSIDNO"][3]) ? "<input type='button' class='cushistory' cuscod='".$arrs["CUSCOD"][3]."' class='btn btn-xs btn-block' value='ประวัติการซื้อ'>":"")."
										</td>
									</tr>									
								</table>
							</td>
						</tr>
						
						<!-- tr style='background-color:#1fecff;' -->
						<tr style='background: rgba(0, 0, 0, 0) url(&#39;../public/lobiadmin-master/version/1.0/ajax/img/bg/bg6.png&#39;) repeat scroll 0% 0%;'>
							<td>
								<div class='col-sm-4'>
									<div class='form-group'>
										พนักงาน
										<span class='form-control'>".(!isset($arrs["EMPNAME"]) ?"":$arrs["EMPNAME"])." ".(!isset($arrs["EMPTEL"]) ?"":$arrs["EMPTEL"])."</span>
									</div>
								</div>
								<div class='col-sm-4'>
									<div class='form-group'>
										ผู้จัดการสาขา
										<span class='form-control'>".(!isset($arrs["MNGNAME"]) ?"":$arrs["MNGNAME"])." ".(!isset($arrs["MNGTEL"]) ?"":$arrs["MNGTEL"])."</span>
									</div>
								</div>
								<div class='col-sm-4'>
									<div class='form-group'>
										ผู้อนุมัติ
										<span class='form-control'>".(!isset($arrs["APPROVENAME"]) ?"":$arrs["APPROVENAME"])." ".(!isset($arrs["APPROVETEL"]) ?"":$arrs["APPROVETEL"])." ".$arrs["ANSTATDESC"]."</span>
									</div>
								</div>
							</td>
						</tr>
						<tr style='background: rgba(0, 0, 0, 0) url(&#39;../public/lobiadmin-master/version/1.0/ajax/img/bg/bg6.png&#39;) repeat scroll 0% 0%;'>
							<td>
								<div class='col-sm-12'>
									<div class='form-group'>
										หมายเหตุ
										<textarea class='form-control' rows=5 readonly>".(!isset($arrs["comment"]) ?"":$arrs["comment"])."</textarea>
									</div>
								</div>
							</td>
						</tr>								
					</table>
				</div>
				
				<div class='col-sm-12'><div class='row'>&emsp;</div></div>
				<div class='col-sm-10 col-sm-offset-1'>	
					<div class='row'>
						<div class='col-sm-2'>	
							<button id='back' class='btn btn-sm btn-danger btn-block'><span class='glyphicon glyphicon-step-backward'> ย้อนกลับ</span></button>							
						</div>
						
						<div class='col-sm-2 col-sm-offset-8'>	
							<button id='approve' class='btn btn-sm btn-primary btn-block'><span class='glyphicon glyphicon-thumbs-up'> อนุมัติ</span></button>							
						</div>
					</div>
				</div>
				<div class='col-sm-12'><div class='row'>&emsp;</div></div>
			</div>
		";
		
		$response = array("html"=>$html,"error"=>false);
		echo json_encode($response);
	}
	
	function loadform(){
		$html = "
			<div id='panel'>
				".$this->_formCAR()."
				".$this->_formCUS()."
				".$this->_formGRT1()."
				".$this->_formGRT2()."
				".$this->_formGRT3()."
				".$this->_formEMP()."
				
				<div class='row' style='padding-top:30px;padding-bottom:30px;'>
					<div class='col-sm-2 col-sm-offset-1'>
						<button id='deleted' class='btn btn-danger btn-block'><span class='glyphicon glyphicon-floppy-disk'> ยกเลิก</span></button>
					</div>
					<div class='col-sm-2 col-sm-offset-6'>
						<button id='save' class='btn btn-primary btn-block'><span class='glyphicon glyphicon-floppy-disk'> บันทึก</span></button>
					</div>
				</div>
			</div>
		";
		
		$response = array("html"=>$html);
		echo json_encode($response);
	}
	
	function _formCAR(){
		$html = "
			<div class='row' style='border:1px dotted #aaa;background-color:#d5f2ba;'>
				<h3>
					<div class='col-sm-10 col-sm-offset-1 text-primary'>
						<span class='toggleData glyphicon glyphicon-minus' thisc='toggleData1' style='cursor:pointer;'>&emsp;ข้อมูลรถ</span>
					</div>
				</h3>
				<div class='toggleData1' isshow=1>
					<div class='row'>							
						<div class='col-sm-2 col-sm-offset-1'>	
							<div class='form-group'>
								เลขที่ใบวิเคราะห์
								<input type='text' id='anid' class='form-control input-sm' readonly value='Auto Genarate'>
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								สาขา
								<select id='locat' class='form-control input-sm select2'>
									<option value='".$this->sess['branch']."'>".$this->sess['branch']."</option>
								</select>
							</div>
						</div>
						<div class='col-sm-2 col-sm-offset-2'>	
							<div class='form-group'>
								กิจกรรมการขาย
								<select id='acticod' class='form-control input-sm select2'></select>
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								วันที่
								<input type='text' id='createDate' class='form-control input-sm' value='".$this->today('today')."' disabled>
							</div>
						</div>
					</div>
					<div class='row'>
						<div class='col-sm-2 col-sm-offset-1'>	
							<div class='form-group'>
								เงินดาวน์รถ
								<input type='number' id='dwnAmt' class='form-control input-sm jzAllowNumber'>
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								เงินดาวน์ ป.1 &emsp;
								<div class='radio-inline' style='margin-top:-5px'>
									<label>
										<input type='radio' class='insuranceType' name='insuranceType' value='Y' checked>รวม
									</label>
								</div>
								<div class='radio-inline' style='margin-top:-5px'>
									<label>
										<input type='radio' class='insuranceType' name='insuranceType' value='N'>แยก
									</label>
								</div>
								<input type='number' id='insuranceAmt' class='form-control input-sm jzAllowNumber'>
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								จำนวนงวด
								<input type='number' id='nopay' class='form-control input-sm jzAllowNumber' maxlength=2>
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								เลขที่บิลจอง
								<select id='resvno' class='form-control input-sm select2'></select>	
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								เงินจอง
								<input type='number' id='resvAmt' class='form-control input-sm' value='' disabled>
							</div>
						</div>
					</div>
					<div class='row'>
						<div class='col-sm-2 col-sm-offset-1'>	
							<div class='form-group'>
								เลขตัวถัง
								<select id='strno' class='form-control input-sm select2'></select>	
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								รุ่น
								<select id='model' class='form-control input-sm select2'></select>	
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								แบบ
								<select id='baab' class='form-control input-sm select2'></select>	
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								สี
								<select id='color' class='form-control input-sm select2'></select>	
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								สถานะรถ
								<input type='text' id='stat' class='form-control input-sm' value='' disabled>
							</div>
						</div>
					</div>
					<div class='row'>
						<div class='col-sm-2 col-sm-offset-1'>	
							<div class='form-group'>
								วันที่ซื้อ ก่อนยึด
								<input type='text' id='sdateold' class='form-control input-sm' value='' disabled>
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								วันที่ยึด
								<input type='text' id='ydate' class='form-control input-sm' value='' disabled>
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								<span class='text-red'>+ ราคารถภายใน 1 ปี</span>
								<input type='number' id='price_add' class='form-control input-sm jzAllowNumber'> 
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								ราคารถ(สด) ก่อนหักส่วนลด
								<input type='number' id='price' class='form-control input-sm jzAllowNumber' stdid='' subid=''> 
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								อัตราดอกเบี้ยต่อเดือน
								<input type='number' id='interatert' class='form-control input-sm jzAllowNumber'>
							</div>
						</div>
					</div>
				</div>
			</div>
		";
		
		return $html;
	}
	
	function _formCUS(){
		$html = "
			<div class='row' style='border:1px dotted #aaa;background-color:#eff2ba;'>
				<h3>
					<div class='col-sm-10 col-sm-offset-1 text-primary'>
						<span class='toggleData glyphicon glyphicon-minus' thisc='toggleData2' style='cursor:pointer;'>&emsp;ผู้เช่าซื้อ</span>
					</div>
				</h3>
				<div class='toggleData2' isshow=1>
					<div class='row'>
						<div class='col-sm-2 col-sm-offset-1'>	
							<div class='form-group'>
								ชื่อ-สกุล ลูกค้า
								<!-- select id='cuscod' class='form-control input-sm select2'></select-->	
								<div class='input-group'>
								   <input type='text' id='cuscod' CUSCOD='' tags='' class='form-control input-sm' placeholder='ลูกค้า'  value=''>
								   <span class='input-group-btn'>
								   <button id='cuscod_removed' tags='' class='btn btn-danger btn-sm' type='button'>
										<span class='glyphicon glyphicon-remove' aria-hidden='true'></span></button>
								   </span>
								</div>
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								เลข ปชช.
								<input type='text' id='idno' class='form-control input-sm' value='' maxlength=20>
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								วันเกิด
								<input type='text' id='idnoBirth' class='form-control input-sm datepicker' maxlength=10>
							</div>
						</div>
						<div class='col-sm-2'>
							<div class='form-group'>
								วันหมดอายุบัตร
								<input type='text' id='idnoExpire' class='form-control input-sm datepicker' maxlength=10>
							</div>
						</div>
						<div class='col-sm-2'>
							<div class='row'>
								<div class='col-sm-6'>
									<div class='form-group'>
										อายุ
										<input type='text' id='idnoAge' class='form-control input-sm jzAllowNumber' maxlength=3>
									</div>
								</div>
								<div class='col-sm-6'>
									<div class='form-group'>
										สถานะ
										<select id='idnoStat' class='form-control input-sm select2'>
											<option value='1'>โสด</option>
											<option value='2'>สมรส</option>
											<option value='3'>หม้าย</option>
											<option value='4'>หย่า</option>
											<option value='5'>แยกกันอยู่</option>
										</select>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class='row'>
						<div class='col-sm-3 col-sm-offset-1'>	
							<div class='form-group'>
								ที่อยู่ตาม ทบ.บ้าน
								<select id='addr1' data-jd-tags='' class='select2_addrno form-control input-sm select2'></select>	
							</div>
						</div>
						<div class='col-sm-3'>
							<div class='form-group'>
								ที่อยู่ส่งเอกสาร
								<select id='addr2' data-jd-tags='' class='select2_addrno form-control input-sm select2'></select>	
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								เบอร์ติดต่อ
								<input type='text' id='phoneNumber' class='form-control input-sm jzAllowNumber' maxlength=10>
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								จำนวนบุตร
								<input type='number' id='baby' class='form-control input-sm jzAllowNumber' min=0 maxlength=2>
							</div>
						</div>
					</div>
					<div class='row'>
						<div class='col-sm-2 col-sm-offset-1'>	
							<div class='form-group'>
								ประกันสังคม
								<input type='text' id='socialSecurity' class='form-control input-sm' maxlength=20>
							</div>
						</div>
						<div class='col-sm-3'>	
							<div class='form-group'>
								อาชีพ
								<input type='text' id='career' class='form-control input-sm' maxlength=100>
							</div>
						</div>
						<div class='col-sm-5'>
							<div class='form-group'>
								ที่อยู๋ที่ทำงาน
								<input type='text' id='careerOffice' class='form-control input-sm' maxlength=250>
							</div>
						</div>
					</div>
					<div class='row'>
						<div class='col-sm-2 col-sm-offset-1'>	
							<div class='form-group'>
								เบอร์ติดต่อที่ทำงาน
								<input type='text' id='careerPhone' class='form-control input-sm jzAllowNumber' maxlength=10>
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								รายได้/เดือน
								<input type='number' id='income' class='form-control input-sm jzAllowNumber' maxlength=13>
							</div>
						</div>
						<div class='col-sm-3'>
							<div class='form-group'>
								ชื่อ-สกุล (เจ้าบ้านตาม ทบ.บ้าน)
								<input type='text' id='hostName' class='form-control input-sm' maxlength=35>
							</div>
						</div>
						<div class='col-sm-3'>
							<div class='form-group'>
								เลข ปชช.(เจ้าบ้าน)
								<input type='text' id='hostIDNo' class='form-control input-sm' maxlength=20>
							</div>
						</div>
					</div>
					<div class='row'>
						<div class='col-sm-2 col-sm-offset-1'>	
							<div class='form-group'>
								เบอร์ติดต่อที่ (เจ้าบ้าน)
								<input type='text' id='hostPhone' class='form-control input-sm jzAllowNumber' maxlength=10>
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								ความสัมพันธ์กับเจ้าบ้าน
								<input type='text' id='hostRelation' class='form-control input-sm' maxlength=100>
							</div>
						</div>
						<div class='col-sm-3'>
							<div class='form-group'>
								ความสัมพันธ์กับพนักงาน
								<select id='empRelation' data-jd-tags='' class='select2_empRelation form-control input-sm select2'></select>	
							</div>
						</div>
						<div class='col-sm-3'>
							<div class='form-group'>
								บุคคลอ้างอิง
								<input type='text' id='reference' class='form-control input-sm' maxlength=250>
							</div>
						</div>
						<div class='col-sm-2 col-sm-offset-1'>	
							<div class='form-group'>
								แนบรูป
								<div class='input-group'>
									<input type='text' id='picture' class='form-control input-sm' readonly='' 
										data-toggle='tooltip'
										data-placement='top'
										data-html='true'
										data-original-title=''										
										style='background-color: rgb(255, 255, 255); color: rgb(0, 0, 0); cursor: default;'>
									<span id='picture_form' data-tags='' class='jd-upload-an input-group-addon btn-default text-info'>
										<span class='glyphicon glyphicon-picture'></span>
									</span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		";
		return $html;
	}
	
	function _formGRT1(){
		$html = "
			<div class='row' style='border:1px dotted #aaa;background-color:#f2cdba;'>
				<h3>
					<div class='col-sm-10 col-sm-offset-1 text-primary'>
						<span class='toggleData glyphicon glyphicon-plus' thisc='toggleData3' style='cursor:pointer;'>&emsp;ผู้ค้ำประกัน 1</span>
					</div>
				</h3>
				<div class='toggleData3' isshow=0 hidden>
					<div class='row'>
						<div class='col-sm-2 col-sm-offset-1'>	
							<div class='form-group'>
								ชื่อ-สกุล ลูกค้า
								<div class='input-group'>
								   <input type='text' id='is1_cuscod' CUSCOD='' tags='is1_' class='form-control input-sm' placeholder='ลูกค้า'  value=''>
								   <span class='input-group-btn'>
								   <button id='is1_cuscod_removed' tags='is1_' class='btn btn-danger btn-sm' type='button'>
										<span class='glyphicon glyphicon-remove' aria-hidden='true'></span></button>
								   </span>
								</div>
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								เลข ปชช.
								<input type='text' id='is1_idno' class='form-control input-sm' value='' maxlength=20>
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								วันเกิด
								<input type='text' id='is1_idnoBirth' class='form-control input-sm datepicker' maxlength=10>
							</div>
						</div>
						<div class='col-sm-2'>
							<div class='form-group'>
								วันหมดอายุบัตร
								<input type='text' id='is1_idnoExpire' class='form-control input-sm datepicker' maxlength=10>
							</div>
						</div>
						<div class='col-sm-2'>
							<div class='row'>
								<div class='col-sm-6'>
									<div class='form-group'>
										อายุ
										<input type='text' id='is1_idnoAge' class='form-control input-sm' maxlength=3>
									</div>
								</div>
								<div class='col-sm-6'>
									<div class='form-group'>
										สถานะ
										<select id='is1_idnoStat' class='form-control input-sm select2'>
											<option value='1'>โสด</option>
											<option value='2'>สมรส</option>
											<option value='3'>หม้าย</option>
											<option value='4'>หย่า</option>
											<option value='5'>แยกกันอยู่</option>
										</select>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class='row'>
						<div class='col-sm-3 col-sm-offset-1'>	
							<div class='form-group'>
								ที่อยู่ตาม ทบ.บ้าน
								<select id='is1_addr1' data-jd-tags='is1_' class='select2_addrno form-control input-sm select2'></select>	
							</div>
						</div>
						<div class='col-sm-3'>
							<div class='form-group'>
								ที่อยู่ส่งเอกสาร
								<select id='is1_addr2' data-jd-tags='is1_' class='select2_addrno form-control input-sm select2'></select>	
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								เบอร์ติดต่อ
								<input type='text' id='is1_phoneNumber' class='form-control input-sm jzAllowNumber' maxlength=10>
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								จำนวนบุตร
								<input type='number' id='is1_baby' class='form-control input-sm jzAllowNumber' min=0 maxlength=3>
							</div>
						</div>
					</div>
					<div class='row'>
						<div class='col-sm-2 col-sm-offset-1'>	
							<div class='form-group'>
								ประกันสังคม
								<input type='text' id='is1_socialSecurity' class='form-control input-sm' maxlength=20>
							</div>
						</div>
						<div class='col-sm-3'>	
							<div class='form-group'>
								อาชีพ
								<input type='text' id='is1_career' class='form-control input-sm' maxlength=100>
							</div>
						</div>
						<div class='col-sm-5'>
							<div class='form-group'>
								ที่อยู๋ที่ทำงาน
								<input type='text' id='is1_careerOffice' class='form-control input-sm' maxlength=250>
							</div>
						</div>
					</div>
					<div class='row'>
						<div class='col-sm-2 col-sm-offset-1'>	
							<div class='form-group'>
								เบอร์ติดต่อที่ทำงาน
								<input type='text' id='is1_careerPhone' class='form-control input-sm jzAllowNumber' maxlength=10>
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								รายได้/เดือน
								<input type='number' id='is1_income' class='form-control input-sm jzAllowNumber' maxlength=13>
							</div>
						</div>
						<div class='col-sm-3'>
							<div class='form-group'>
								ชื่อ-สกุล (เจ้าบ้านตาม ทบ.บ้าน)
								<input type='text' id='is1_hostName' class='form-control input-sm' maxlength=100>
							</div>
						</div>
						<div class='col-sm-3'>
							<div class='form-group'>
								เลข ปชช.(เจ้าบ้าน)
								<input type='number' id='is1_hostIDNo' class='form-control input-sm' maxlength=20>
							</div>
						</div>
					</div>
					<div class='row'>
						<div class='col-sm-2 col-sm-offset-1'>	
							<div class='form-group'>
								เบอร์ติดต่อที่ (เจ้าบ้าน)
								<input type='text' id='is1_hostPhone' class='form-control input-sm jzAllowNumber' maxlength=10>
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								ความสัมพันธ์กับเจ้าบ้าน
								<input type='text' id='is1_hostRelation' class='form-control input-sm' maxlength=250>
							</div>
						</div>
						<div class='col-sm-3'>
							<div class='form-group'>
								ความสัมพันธ์กับพนักงาน
								<select id='is1_empRelation' data-jd-tags='is1_' class='select2_empRelation form-control input-sm select2'></select>	
							</div>
						</div>
						<div class='col-sm-3'>
							<div class='form-group'>
								บุคคลอ้างอิง
								<input type='text' id='is1_reference' class='form-control input-sm' maxlength=250>
							</div>
						</div>
						
						<div class='col-sm-2 col-sm-offset-1'>	
							<div class='form-group'>
								แนบรูป
								<div class='input-group'>
									<input type='text' id='is1_picture' class='form-control input-sm' readonly='' 
										data-toggle='tooltip'
										data-placement='top'
										data-html='true'
										data-original-title=''	
										style='background-color: rgb(255, 255, 255); color: rgb(0, 0, 0); cursor: default;'>
									<span id='is1_picture_form' data-tags='is1_' class='jd-upload-an input-group-addon btn-default text-info'>
										<span class='glyphicon glyphicon-picture'></span>
									</span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		";
		
		return $html;
	}
	
	function _formGRT2(){
		$html = "
			<div class='row' style='border:1px dotted #aaa;background-color:#f2baba;'>
				<h3>
					<div class='col-sm-10 col-sm-offset-1 text-primary'>
						<span class='toggleData glyphicon glyphicon-plus' thisc='toggleData4' style='cursor:pointer;'>&emsp;ผู้ค้ำประกัน 2</span>
					</div>
				</h3>
				<div class='toggleData4' isshow=0 hidden>
					<div class='row'>
						<div class='col-sm-2 col-sm-offset-1'>	
							<div class='form-group'>
								ชื่อ-สกุล ลูกค้า
								<!--select id='is2_cuscod' class='form-control input-sm select2'></select-->	
								<div class='input-group'>
								   <input type='text' id='is2_cuscod' CUSCOD='' tags='is2_' class='form-control input-sm' placeholder='ลูกค้า'  value=''>
								   <span class='input-group-btn'>
								   <button id='is2_cuscod_removed' tags='is2_' class='btn btn-danger btn-sm' type='button'>
										<span class='glyphicon glyphicon-remove' aria-hidden='true'></span></button>
								   </span>
								</div>
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								เลข ปชช.
								<input type='text' id='is2_idno' class='form-control input-sm' value='' maxlength=20>
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								วันเกิด
								<input type='text' id='is2_idnoBirth' class='form-control input-sm datepicker' maxlength=10>
							</div>
						</div>
						<div class='col-sm-2'>
							<div class='form-group'>
								วันหมดอายุบัตร
								<input type='text' id='is2_idnoExpire' class='form-control input-sm datepicker' maxlength=10>
							</div>
						</div>
						<div class='col-sm-2'>
							<div class='row'>
								<div class='col-sm-6'>
									<div class='form-group'>
										อายุ
										<input type='text' id='is2_idnoAge' class='form-control input-sm'>
									</div>
								</div>
								<div class='col-sm-6'>
									<div class='form-group'>
										สถานะ
										<select id='is2_idnoStat' class='form-control input-sm select2'>
											<option value='1'>โสด</option>
											<option value='2'>สมรส</option>
											<option value='3'>หม้าย</option>
											<option value='4'>หย่า</option>
											<option value='5'>แยกกันอยู่</option>
										</select>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class='row'>
						<div class='col-sm-3 col-sm-offset-1'>	
							<div class='form-group'>
								ที่อยู่ตาม ทบ.บ้าน
								<select id='is2_addr1' data-jd-tags='is2_' class='select2_addrno form-control input-sm select2'></select>	
							</div>
						</div>
						<div class='col-sm-3'>
							<div class='form-group'>
								ที่อยู่ส่งเอกสาร
								<select id='is2_addr2' data-jd-tags='is2_' class='select2_addrno form-control input-sm select2'></select>	
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								เบอร์ติดต่อ
								<input type='text' id='is2_phoneNumber' class='form-control input-sm jzAllowNumber' maxlength=10>
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								จำนวนบุตร
								<input type='number' id='is2_baby' class='form-control input-sm jzAllowNumber' min=0 maxlength=3>
							</div>
						</div>
					</div>
					<div class='row'>
						<div class='col-sm-2 col-sm-offset-1'>	
							<div class='form-group'>
								ประกันสังคม
								<input type='text' id='is2_socialSecurity' class='form-control input-sm' maxlength=20>
							</div>
						</div>
						<div class='col-sm-3'>	
							<div class='form-group'>
								อาชีพ
								<input type='text' id='is2_career' class='form-control input-sm' maxlength=100>
							</div>
						</div>
						<div class='col-sm-5'>
							<div class='form-group'>
								ที่อยู๋ที่ทำงาน
								<input type='text' id='is2_careerOffice' class='form-control input-sm' maxlength=250>
							</div>
						</div>
					</div>
					<div class='row'>
						<div class='col-sm-2 col-sm-offset-1'>	
							<div class='form-group'>
								เบอร์ติดต่อที่ทำงาน
								<input type='text' id='is2_careerPhone' class='form-control input-sm jzAllowNumber' maxlength=10>
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								รายได้/เดือน
								<input type='number' id='is2_income' class='form-control input-sm jzAllowNumber' maxlength=13>
							</div>
						</div>
						<div class='col-sm-3'>
							<div class='form-group'>
								ชื่อ-สกุล (เจ้าบ้านตาม ทบ.บ้าน)
								<input type='text' id='is2_hostName' class='form-control input-sm' maxlength=250>
							</div>
						</div>
						<div class='col-sm-3'>
							<div class='form-group'>
								เลข ปชช.(เจ้าบ้าน)
								<input type='number' id='is2_hostIDNo' class='form-control input-sm' maxlength=20>
							</div>
						</div>
					</div>
					<div class='row'>
						<div class='col-sm-2 col-sm-offset-1'>	
							<div class='form-group'>
								เบอร์ติดต่อที่ (เจ้าบ้าน)
								<input type='text' id='is2_hostPhone' class='form-control input-sm jzAllowNumber' maxlength=10>
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								ความสัมพันธ์กับเจ้าบ้าน
								<input type='text' id='is2_hostRelation' class='form-control input-sm' maxlength=250>
							</div>
						</div>
						<div class='col-sm-3'>
							<div class='form-group'>
								ความสัมพันธ์กับพนักงาน
								<select id='is2_empRelation' data-jd-tags='is2_' class='select2_empRelation form-control input-sm select2'></select>	
							</div>
						</div>
						<div class='col-sm-3'>
							<div class='form-group'>
								บุคคลอ้างอิง
								<input type='text' id='is2_reference' class='form-control input-sm' maxlength=250>
							</div>
						</div>
						
						<div class='col-sm-2 col-sm-offset-1'>	
							<div class='form-group'>
								แนบรูป
								<div class='input-group'>
									<input type='text' id='is2_picture' class='form-control input-sm' readonly=''
										data-toggle='tooltip'
										data-placement='top'
										data-html='true'
										data-original-title=''	
										style='background-color: rgb(255, 255, 255); color: rgb(0, 0, 0); cursor: default;'>
									<span id='is2_picture_form' data-tags='is2_' class='jd-upload-an input-group-addon btn-default text-info'>
										<span class='glyphicon glyphicon-picture'></span>
									</span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		";
		
		return $html;
	}
	
	//Grantor	 ผู้ยินยอม
	function _formGRT3(){
		$html = "
			<div class='row' style='border:1px dotted #aaa;background-color:#c8baf2;'>
				<h3>
					<div class='col-sm-10 col-sm-offset-1 text-primary'>
						<span class='toggleData glyphicon glyphicon-plus' thisc='toggleData5' style='cursor:pointer;'>&emsp;ผู้ยินยอม</span>
					</div>
				</h3>
				<div class='toggleData5' isshow=0 hidden>
					<div class='row'>
						<div class='col-sm-2 col-sm-offset-1'>	
							<div class='form-group'>
								ชื่อ-สกุล ลูกค้า
								<div class='input-group'>
								   <input type='text' id='is3_cuscod' CUSCOD='' tags='is3_' class='form-control input-sm' placeholder='ลูกค้า'  value=''>
								   <span class='input-group-btn'>
								   <button id='is3_cuscod_removed' tags='is3_' class='btn btn-danger btn-sm' type='button'>
										<span class='glyphicon glyphicon-remove' aria-hidden='true'></span></button>
								   </span>
								</div>
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								เลข ปชช.
								<input type='text' id='is3_idno' class='form-control input-sm' value='' maxlength=20>
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								วันเกิด
								<input type='text' id='is3_idnoBirth' class='form-control input-sm datepicker' maxlength=10>
							</div>
						</div>
						<div class='col-sm-2'>
							<div class='form-group'>
								วันหมดอายุบัตร
								<input type='text' id='is3_idnoExpire' class='form-control input-sm datepicker' maxlength=10>
							</div>
						</div>
						<div class='col-sm-2'>
							<div class='row'>
								<div class='col-sm-6'>
									<div class='form-group'>
										อายุ
										<input type='text' id='is3_idnoAge' class='form-control input-sm' maxlength=3>
									</div>
								</div>
								<div class='col-sm-6'>
									<div class='form-group'>
										สถานะ
										<select id='is3_idnoStat' class='form-control input-sm select2'>
											<option value='1'>โสด</option>
											<option value='2'>สมรส</option>
											<option value='3'>หม้าย</option>
											<option value='4'>หย่า</option>
											<option value='5'>แยกกันอยู่</option>
										</select>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class='row'>
						<div class='col-sm-3 col-sm-offset-1'>	
							<div class='form-group'>
								ที่อยู่ตาม ทบ.บ้าน
								<select id='is3_addr1' data-jd-tags='is3_' class='select2_addrno form-control input-sm select2'></select>	
							</div>
						</div>
						<div class='col-sm-3'>
							<div class='form-group'>
								ที่อยู่ส่งเอกสาร
								<select id='is3_addr2' data-jd-tags='is3_' class='select2_addrno form-control input-sm select2'></select>	
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								เบอร์ติดต่อ
								<input type='text' id='is3_phoneNumber' class='form-control input-sm jzAllowNumber' maxlength=10>
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								จำนวนบุตร
								<input type='number' id='is3_baby' class='form-control input-sm jzAllowNumber' min=0 maxlength=3>
							</div>
						</div>
					</div>
					<div class='row'>
						<div class='col-sm-2 col-sm-offset-1'>	
							<div class='form-group'>
								ประกันสังคม
								<input type='text' id='is3_socialSecurity' class='form-control input-sm' maxlength=20>
							</div>
						</div>
						<div class='col-sm-3'>	
							<div class='form-group'>
								อาชีพ
								<input type='text' id='is3_career' class='form-control input-sm' maxlength=100>
							</div>
						</div>
						<div class='col-sm-5'>
							<div class='form-group'>
								ที่อยู๋ที่ทำงาน
								<input type='text' id='is3_careerOffice' class='form-control input-sm' maxlength=250>
							</div>
						</div>
					</div>
					<div class='row'>
						<div class='col-sm-2 col-sm-offset-1'>	
							<div class='form-group'>
								เบอร์ติดต่อที่ทำงาน
								<input type='text' id='is3_careerPhone' class='form-control input-sm jzAllowNumber' maxlength=10>
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								รายได้/เดือน
								<input type='number' id='is3_income' class='form-control input-sm jzAllowNumber' maxlength=13>
							</div>
						</div>
						<div class='col-sm-3'>
							<div class='form-group'>
								ชื่อ-สกุล (เจ้าบ้านตาม ทบ.บ้าน)
								<input type='text' id='is3_hostName' class='form-control input-sm' maxlength=250>
							</div>
						</div>
						<div class='col-sm-3'>
							<div class='form-group'>
								เลข ปชช.(เจ้าบ้าน)
								<input type='number' id='is3_hostIDNo' class='form-control input-sm' maxlength=20>
							</div>
						</div>
					</div>
					<div class='row'>
						<div class='col-sm-2 col-sm-offset-1'>	
							<div class='form-group'>
								เบอร์ติดต่อที่ (เจ้าบ้าน)
								<input type='text' id='is3_hostPhone' class='form-control input-sm jzAllowNumber' maxlength=10>
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								ความสัมพันธ์กับเจ้าบ้าน
								<input type='text' id='is3_hostRelation' class='form-control input-sm' maxlength=100>
							</div>
						</div>
						<div class='col-sm-3'>
							<div class='form-group'>
								ความสัมพันธ์กับพนักงาน
								<select id='is3_empRelation' data-jd-tags='is3_' class='select2_empRelation form-control input-sm select2'></select>	
							</div>
						</div>
						<div class='col-sm-3'>
							<div class='form-group'>
								บุคคลอ้างอิง
								<input type='text' id='is3_reference' class='form-control input-sm' maxlength=100>
							</div>							
						</div>
						
						<div class='col-sm-2 col-sm-offset-1'>	
							<div class='form-group'>
								แนบรูป
								<div class='input-group'>
									<input type='text' id='is3_picture' class='form-control input-sm' readonly=''
										data-toggle='tooltip'
										data-placement='top'
										data-html='true'
										data-original-title=''	
										style='background-color: rgb(255, 255, 255); color: rgb(0, 0, 0); cursor: default;'>
									<span id='is3_picture_form' data-tags='is3_' class='jd-upload-an input-group-addon btn-default text-info'>
										<span class='glyphicon glyphicon-picture'></span>
									</span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		";
		
		return $html;
	}
	
	function _formEMP(){
		$html = "
			<div class='row' style='border:1px dotted #aaa;background-color:#baeff2;'>
				<h3>
					<div class='col-sm-10 col-sm-offset-1 text-primary'>
						<span class='toggleData glyphicon glyphicon-minus' thisc='toggleData6' style='cursor:pointer;'>&emsp;สาขา</span>
					</div>
				</h3>
				<div class='toggleData6' isshow=1>
					<div class='row'>
						<div class='col-sm-3 col-sm-offset-1'>	
							<div class='form-group'>
								พนักงาน
								<select id='empIDNo' class='form-control input-sm select2'></select>	
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								เบอร์ติดต่อ
								<input type='text' id='empTel' class='form-control input-sm jzAllowNumber' maxlength=10>
							</div>
						</div>
						<div class='col-sm-3'>	
							<div class='form-group'>
								ผู้จัดการสาขา
								<select id='mngIDNo' class='form-control input-sm select2'></select>	
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								เบอร์ติดต่อ
								<input type='text' id='mngTel' class='form-control input-sm jzAllowNumber' maxlength=10>
							</div>
						</div>
						
						<div class='col-sm-2 col-sm-offset-1'>	
							<div class='form-group'>
								รูปประกอบ
								<div class='input-group'>
									<input type='text' id='analyze_picture' class='form-control input-sm' readonly=''
										data-toggle='tooltip'
										data-placement='top'
										data-html='true'
										data-original-title=''	
										style='background-color: rgb(255, 255, 255); color: rgb(0, 0, 0); cursor: default;'>
									<span id='analyze_picture_form' data-tags='analyze_' class='jd-upload-an input-group-addon btn-default text-info'>
										<span class='glyphicon glyphicon-picture'></span>
									</span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		";
		
		return $html;
	}
	
	function dataResv(){
		$response = array("html"=>"","error"=>false,"msg"=>"");
		$dwnAmt	  = str_replace(",","",$_POST["dwnAmt"]);
		$resvno   = $_POST["resvno"];
		$acticod  = $_POST["acticod"];
		
		if($dwnAmt == ""){
			$response["error"] = true;
			$response["msg"] = "ผิดพลาด :: โปรดระบุเงินดาวน์ก่อนครับ";
			echo json_encode($response); exit;
		}else if(!is_numeric($dwnAmt)){
			$response["error"] = true;
			$response["msg"] = "ผิดพลาด :: โปรดระบุเงินดาวน์ให้ถูกต้อง";
			echo json_encode($response); exit;
		}
		
		/*
		if($acticod == "ALL"){
			$response["error"] = true;
			$response["msg"] = "ผิดพลาด :: โปรดระบุกิจกรรมการขายก่อนครับ";
			echo json_encode($response); exit;
		}
		*/
		
		$sql = "
			select a.RESVNO,a.RESPAY,a.STRNO,a.MODEL,a.BAAB,a.COLOR
				,case when a.STAT='N' then 'รถใหม่'  else 'รถเก่า' end as STAT
				,a.STAT as STATEN
				,a.LOCAT				
				,convert(varchar(8),a.RESVDT,112) as RESVDT
				,convert(varchar(8),b.SDATE,112) as SDATE
				,convert(varchar(8),b.YDATE,112) as YDATE
				,b.CONTNO
				,datediff(day,b.SDATE,b.YDATE) as daysy
				,a.CUSCOD
				,c.SNAM+c.NAME1+' '+c.NAME2+' ('+c.CUSCOD+')-'+c.GRADE as CUSNAME
				,c.IDNO
				,convert(varchar(8),c.BIRTHDT,112) as BIRTHDT
				,convert(varchar(8),c.EXPDT,112) as EXPDT
				,DATEDIFF(YEAR,c.BIRTHDT,GETDATE()) as AGE
				,c.ADDRNO
				,'('+c.ADDRNO+') '+d.ADDR1+' '+d.ADDR2+' ต.'+d.TUMB+' อ.'+e.AUMPDES+' จ.'+f.PROVDES+' '+d.ZIP as ADDR
				,c.MOBILENO
				,isnull(c.MREVENU,0) as MREVENU
				,g.ACTICOD
				,'('+g.ACTICOD+') '+h.ACTIDES collate thai_cs_as as ACTIDES
				,case when i.price is null then a.PRICE else i.price end as price
				,isnull(g.STDID,'') as STDID
				,isnull(cast(g.SUBID as varchar),'') as SUBID
				,a.RESPAY - (isnull(a.SMPAY,0) + isnull(a.SMCHQ,0)) as BALANCE
			from {$this->MAuth->getdb('ARRESV')} a 
			left join (
				select ROW_NUMBER() over(partition by STRNO order by STRNO,sdate desc) r,* 
				from {$this->MAuth->getdb('ARHOLD')}
			) as b on a.STRNO=b.STRNO and b.r=1
			left join {$this->MAuth->getdb('CUSTMAST')} c on a.CUSCOD=c.CUSCOD
			left join {$this->MAuth->getdb('CUSTADDR')} d on c.CUSCOD=d.CUSCOD and c.ADDRNO=d.ADDRNO
			left join {$this->MAuth->getdb('SETAUMP')} e on d.AUMPCOD=e.AUMPCOD
			left join {$this->MAuth->getdb('SETPROV')} f on e.PROVCOD=f.PROVCOD
			left join {$this->MAuth->getdb('ARRESVOTH')} g on a.RESVNO=g.RESVNO collate thai_cs_as
			left join {$this->MAuth->getdb('SETACTI')} h on g.ACTICOD=h.ACTICOD collate thai_cs_as
			left join {$this->MAuth->getdb('STDVehiclesPRICE')} i on g.STDID=i.STDID and g.SUBID=i.SUBID 
			where a.RESVNO='".$resvno."'
		";
		//echo $sql; //exit;
		$query = $this->db->query($sql);
		
		$data = array();
		if($query->row()){
			foreach($query->result() as $row){
				foreach($row as $key => $val){
					switch($key){
						case 'SDATE': $data[$key] = $this->Convertdate(2,$val); break;
						case 'YDATE': $data[$key] = $this->Convertdate(2,$val); break;
						case 'BIRTHDT': $data[$key] = $this->Convertdate(2,$val); break;
						case 'EXPDT': $data[$key] = $this->Convertdate(2,$val); break;
						case 'MREVENU': $data[$key] = number_format($val,2); break;
						case 'ACTICOD': $data[$key] = str_replace(chr(0),"",$val); break;
						case 'ACTIDES': $data[$key] = str_replace(chr(0),"",$val); break;
						case 'RESVDT': $data["DT"] = $val; break;
						default:  $data[$key] = $val; break;
					}
				}
			}
		}
		
		if($data["BALANCE"] > 0)
		{
			$response["error"] = true;
			$response["msg"] = "ผิดพลาด :: โปรดเลขที่บิลจอง ".$data["RESVNO"]." ยังไม่ได้ชำระค่าจองทีครับ";
			echo json_encode($response); exit;	
		}	
		
		$sql = "
			declare @filePath varchar(250) = (
				select filePath from {$this->MAuth->getdb('config_fileupload')}
				where ftpfolder like 'Senior/%/ANALYZE' and ftpstatus='Y' and refno='".$this->sess["db"]."'
			);
			set @filePath = (
				select @filePath + convert(varchar(30),cast([PICTUR] as varbinary))
				from {$this->MAuth->getdb('CUSTPICT')} 
				where CUSCOD='".$data["CUSCOD"]."'
			)
			
			select b.CUSSTAT,b.CUSBABY,b.ADDRNO as ADDRNOLAST,b.ADDRDOCNO as ADDRDOCNOLAST
				,b.SOCAILSECURITY,b.CAREER,b.CAREERADDR,b.CAREERTEL
				,b.HOSTNAME,b.HOSTIDNO,b.HOSTTEL,b.HOSTRELATION
				,b.EMPRELATION
				,isnull(( 
					select top 1 employeeCode+' :: '+firstName+' '+LastName as Name 
					from {$this->MAuth->getdb('hp_vusers')} as sa
					where sa.IDNo = b.EMPRELATION collate thai_cs_as
				),'') as EMPRELATIONNAME
				,b.REFERANT,isnull(@filePath,'(none)') as filePath
			from {$this->MAuth->getdb('ARANALYZE')} a
			left join {$this->MAuth->getdb('ARANALYZEREF')} b on a.ID=b.ID
			left join (
				select MAX(a.CREATEDATE) as CREATEDATE,b.CUSCOD from {$this->MAuth->getdb('ARANALYZE')} a
				left join {$this->MAuth->getdb('ARANALYZEREF')} b on a.ID=b.ID
				where b.CUSCOD='".$data["CUSCOD"]."' and a.ANSTAT != 'C' collate thai_ci_as
				group by b.CUSCOD
			) c on a.CREATEDATE=c.CREATEDATE and b.CUSCOD=c.CUSCOD
			where c.CUSCOD is not null
		";
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				foreach($row as $key => $val){
					switch($key){
						default:  $data[$key] = $val; break;
					}
				}
			}
		}
	
		$sql = "select * from {$this->MAuth->getdb('fn_STDVehicles')}('{$data["MODEL"]}','{$data["BAAB"]}','{$data["COLOR"]}','{$data["STATEN"]}','{$data["ACTICOD"]}','{$data["LOCAT"]}','{$data["DT"]}')";
		//echo $sql; exit;
		$query = $this->db->query($sql);
			
		$data["mainstdid"] = "";
		$data["mainsubid"] = "";
		if($query->row()){
			foreach($query->result() as $row){
				$data["mainstdid"] = $row->STDID;
				$data["mainsubid"] = $row->SUBID;
				
				if($row->STAT == "N"){
					$sql = "
						select STDID,SUBID,0 as SHCID,PRICE,0 as PRICE_ADD from {$this->MAuth->getdb('STDVehiclesPRICE')}
						where STDID='".$row->STDID."' and SUBID='".$row->SUBID."'
					";
				}else{
					$sql = "
						declare @CONTNO varchar(12) = '".$data["CONTNO"]."';
						declare @STAT varchar(1)  = (case when @CONTNO = '' 
							then 'O' else (select STAT from {$this->MAuth->getdb('HINVTRAN')} where CONTNO=@CONTNO) end);
						declare @price_add decimal(18,2) = (
							select price_add from {$this->MAuth->getdb('config_addpricesale')}
							where getdate() between event_st and isnull(event_ed,getdate()) 
								and '".$data["daysy"]."' between in_sday and in_eday
						);
						
						select '".$row->STDID."' as STDID
							,'".$row->SUBID."' as SUBID
							,a.ID as SHCID,b.OPRICE as PRICE
							,case when @STAT = 'N'
								then (case when @price_add is null then 0 else @price_add end) else 0 end as PRICE_ADD
						from {$this->MAuth->getdb('STDSHCAR')} a
						left join {$this->MAuth->getdb('STDSHCARDetails')} b on a.ID=b.ID
						left join {$this->MAuth->getdb('STDSHCARColors')} c on a.ID=c.ID
						left join {$this->MAuth->getdb('STDSHCARLocats')} d on a.ID=d.ID
						where b.ACTIVE='yes' collate thai_ci_as 
							and a.MODEL='".$row->MODEL."' collate thai_cs_as
							and a.BAAB='".$row->BAAB."' collate thai_cs_as 
							and (case when c.COLOR = 'ALL' then '".$row->COLOR."' else c.COLOR end) = '".$row->COLOR."' collate thai_cs_as 
							and (case when d.LOCAT = 'ALL' then '".$row->LOCAT."' else d.LOCAT end) = '".$row->LOCAT."' collate thai_cs_as
							and a.GCODE='".$data["GCODE"]."'
					";
				}
				//echo $sql; exit;
				$query = $this->db->query($sql);
				
				if($query->row()){
					foreach($query->result() as $row){
						$data["STDID"] = $row->STDID;
						$data["SUBID"] = $row->SUBID;
						$data["SHCID"] = $row->SHCID;
						$data["PRICE"] = $row->PRICE;
						$data["PRICE_ADD"] = $row->PRICE_ADD;
					}
				}else{
					$response["error"] = true;
					$response["msg"] = "
						ผิดพลาด ไม่พบราคาในสแตนดาร์ด <br>โปรดติดต่อฝ่ายเช่าซื้อ/ฝ่ายวิเคราะห์ เพื่อกำหนดราคาขายก่อนครับ<br><br>
						รุ่น :: ".$data["MODEL"]."<br>
						แบบ :: ".$data["BAAB"]."<br>
						สี :: ".$data["COLOR"]."<br>
						กิจกรรมการขาย :: ".$data["ACTIDES"]."
					";
				}
				
				/*
					$data["stdid"] 		= $row->id;
					$data["stdplrank"] 	= $row->plrank;
					$data["price"] 		= $row->price;
				}
				*/
			}
		}else{
			$response["error"] = true;
			$response["msg"] = "
				ผิดพลาด ไม่พบราคาขายรถใหม่ โปรดติดต่อฝ่ายเช่าซื้อ/ฝ่ายวิเคราะห์ เพื่อกำหนดราคาขายก่อนครับ<br><br>
				รุ่น :: ".$data["MODEL"]."<br>
				แบบ :: ".$data["BAAB"]."<br>
				สี :: ".$data["COLOR"]."<br>
				วันที่ขออนุมัติ :: ".$this->Convertdate(2,$data["DT"])."
			";
			echo json_encode($response); exit;
		}
		
		$sql = "
			select * from {$this->MAuth->getdb('STDVehiclesDown')} a
			where STDID='".$data["mainstdid"]."' 
				and SUBID='".$data["mainsubid"]."' and '".$dwnAmt."' between DOWNS and isnull(DOWNE,'".$dwnAmt."')
				and '".$data["PRICE"]."' between PRICES and isnull(PRICEE,'".$data["PRICE"]."')
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		if($query->row()){
			foreach($query->result() as $row){
				$data["interest_rate"] 	= $row->INTERESTRT;
				$data["interest_rate2"]	= $row->INTERESTRT_GVM;
			}
		}else{
			$response["error"] = true;
			$response["msg"] = "
				ผิดพลาด ไม่พบขั้นเงินดาวน์ที่ระบุมา โปรดตรวจสอบข้อมูลใหม่อีกครั้ง<br><br>
				รุ่น :: ".$data["MODEL"]."<br>
				แบบ :: ".$data["BAAB"]."<br>
				สี :: ".$data["COLOR"]."<br>
				สถานะรถ :: ".$data["STAT"]."<br>
				กิจกรรมการขาย :: ".$data["ACTICOD"]."<br>
				วันที่ขออนุมัติ :: ".$this->Convertdate(2,$data["DT"])."
			";
			
			echo json_encode($response); exit;
		}
		
		$response["html"] = $data;
		echo json_encode($response);
	}
	
	function dataSTR(){
		$response   = array("html"=>"","error"=>false,"msg"=>"");
		$dwnAmt	    = $_POST["dwnAmt"];
		$createDate = $this->Convertdate(1,$_POST["createDate"]);
		$strno 	    = $_POST["strno"];
		$acticod    = $_POST["acticod"];
		
		if($acticod == "ALL"){
			$response["error"] = true;
			$response["msg"] = "ผิดพลาด :: โปรดระบุกิจกรรมการขายก่อนครับ";
			echo json_encode($response); exit;
		}
		
		$sql = "
			declare @STRNO_USE int = (
				select count(*) from {$this->MAuth->getdb('ARANALYZE')}
				where STRNO = '{$strno}' and ANSTAT != 'C' collate thai_ci_as
			);
			select a.STRNO,@STRNO_USE as STRNO_USE,a.MODEL,a.BAAB,a.COLOR
				,case when a.STAT='N' then 'รถใหม่'  else 'รถเก่า' end as STAT
				,a.STAT as STATEN
				,convert(varchar(8),b.SDATE,112) as SDATE
				,convert(varchar(8),b.YDATE,112) as YDATE
				,a.CRLOCAT as LOCAT
				,a.GCODE
				,datediff(day,b.SDATE,b.YDATE) as daysy
				,b.CONTNO
			from {$this->MAuth->getdb('INVTRAN')} a
			left join (
				select ROW_NUMBER() over(partition by STRNO order by STRNO,sdate desc) r,* 
				from {$this->MAuth->getdb('ARHOLD')}
			) as b on a.STRNO=b.STRNO and b.r=1
			where a.STRNO='".$strno."'
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$data = array();
		$data["ACTICOD"] = $acticod;
		$data["DT"] 	 = $createDate;
		if($query->row()){
			foreach($query->result() as $row){
				foreach($row as $key => $val){
					switch($key){
						case 'SDATE': $data[$key] = $this->Convertdate(2,$val); break;
						case 'YDATE': $data[$key] = $this->Convertdate(2,$val); break;
						default:  $data[$key] = $val; break;
					}
				}
			}
		}
		
		if($data[$key] > 0){
			$response["error"] = true;
			$response["msg"] = "ผิดพลาด :: เลขตัวถังนี้ ได้ขออนุมัติใบวิเคราะห์แล้วครับ";
			echo json_encode($response); exit;
		}
		
		$data["interest_rate"] 	= "";
		$data["interest_rate2"]	= "";
		
		$sql = "select * from {$this->MAuth->getdb('fn_STDVehicles')}('{$data["MODEL"]}','{$data["BAAB"]}','{$data["COLOR"]}','{$data["STATEN"]}','{$data["ACTICOD"]}','{$data["LOCAT"]}','{$data["DT"]}')";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$data["mainstdid"] = "";
		$data["mainsubid"] = "";
		if($query->row()){
			foreach($query->result() as $row){
				$data["mainstdid"] = $row->STDID;
				$data["mainsubid"] = $row->SUBID;
				
				if($row->STAT == "N"){
					$sql = "
						select STDID,SUBID,0 as SHCID,PRICE,0 as PRICE_ADD from {$this->MAuth->getdb('STDVehiclesPRICE')}
						where STDID='".$row->STDID."' and SUBID='".$row->SUBID."'
					";
				}else{
					$sql = "
						declare @CONTNO varchar(12) = '".$data["CONTNO"]."';
						declare @STAT varchar(1)  = (case when @CONTNO = '' 
							then 'O' else (select STAT from {$this->MAuth->getdb('HINVTRAN')} where CONTNO=@CONTNO) end);
						declare @price_add decimal(18,2) = (
							select price_add from {$this->MAuth->getdb('config_addpricesale')}
							where getdate() between event_st and isnull(event_ed,getdate()) 
								and '".$data["daysy"]."' between in_sday and in_eday
						);
						
						select '{$row->STDID}' as STDID
							,'{$row->SUBID}' as SUBID
							,a.ID as SHCID
						
							,b.OPRICE as PRICE
							,case when @STAT = 'N'
								then (case when @price_add is null then 0 else @price_add end) else 0 end as PRICE_ADD
						from {$this->MAuth->getdb('STDSHCAR')} a
						left join {$this->MAuth->getdb('STDSHCARDetails')} b on a.ID=b.ID
						left join {$this->MAuth->getdb('STDSHCARColors')} c on a.ID=c.ID
						left join {$this->MAuth->getdb('STDSHCARLocats')} d on a.ID=d.ID
						where b.ACTIVE='yes' collate thai_ci_as 
							and a.MODEL='".$row->MODEL."' collate thai_cs_as
							and a.BAAB='".$row->BAAB."' collate thai_cs_as 
							and (case when c.COLOR = 'ALL' then '".$row->COLOR."' else c.COLOR end) = '".$row->COLOR."' collate thai_cs_as 
							and (case when d.LOCAT = 'ALL' then '".$row->LOCAT."' else d.LOCAT end) = '".$row->LOCAT."' collate thai_cs_as
							and a.GCODE='".$data["GCODE"]."'
					";
				}
				//echo $sql; exit;
				$query = $this->db->query($sql);
				
				if($query->row()){
					foreach($query->result() as $row){
						$data["STDID"] = $row->STDID;
						$data["SUBID"] = $row->SUBID;
						$data["SHCID"] = $row->SHCID;
						$data["PRICE"] = ($row->PRICE+$row->PRICE_ADD);
						$data["PRICE_ADD"] = number_format($row->PRICE_ADD,2);
					}
				}else{
					$response["error"] = true;
					$response["msg"] = "
						ผิดพลาด ไม่พบราคาในสแตนดาร์ด <br>โปรดติดต่อฝ่ายเช่าซื้อ/ฝ่ายวิเคราะห์ เพื่อกำหนดราคาขายก่อนครับ<br><br>
						รุ่น :: ".$data["MODEL"]."<br>
						แบบ :: ".$data["BAAB"]."<br>
						สี :: ".$data["COLOR"]."<br>
						สถานะรถ :: ".$data["STAT"]."<br>
						กิจกรรมการขาย :: ".$data["ACTICOD"]."<br>
						วันที่ขออนุมัติ :: ".$this->Convertdate(2,$createDate)."
					";
					echo json_encode($response); exit;
				}
			}
		}else{
			$response["error"] = true;
			$response["msg"] = "
				ผิดพลาด ไม่พบราคาขายรถ <br>โปรดติดต่อฝ่ายเช่าซื้อ/ฝ่ายวิเคราะห์ เพื่อกำหนดราคาขายก่อนครับ<br><br>
				รุ่น :: ".$data["MODEL"]."<br>
				แบบ :: ".$data["BAAB"]."<br>
				สี :: ".$data["COLOR"]."<br>
				สถานะรถ :: ".$data["STAT"]."<br>
				กิจกรรมการขาย :: ".$data["ACTICOD"]."<br>
				วันที่ขออนุมัติ :: ".$this->Convertdate(2,$createDate)."
			";
			echo json_encode($response); exit;
		}
		
		$sql = "
			select * from {$this->MAuth->getdb('STDVehiclesDown')} a
			where STDID='".$data["mainstdid"]."' 
				and SUBID='".$data["mainsubid"]."' and '".$dwnAmt."' between DOWNS and isnull(DOWNE,'".$dwnAmt."')
				and '".$data["PRICE"]."' between PRICES and isnull(PRICEE,'".$data["PRICE"]."')
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		if($query->row()){
			foreach($query->result() as $row){
				$data["interest_rate"] 	= $row->INTERESTRT;
				$data["interest_rate2"]	= $row->INTERESTRT_GVM;
			}
		}else{
			$response["error"] = true;
			$response["msg"] = "
				ผิดพลาด ไม่พบขั้นเงินดาวน์ที่ระบุมา โปรดตรวจสอบข้อมูลใหม่อีกครั้ง<br><br>
				รุ่น :: ".$data["MODEL"]."<br>
				แบบ :: ".$data["BAAB"]."<br>
				สี :: ".$data["COLOR"]."<br>
				สถานะรถ :: ".$data["STAT"]."<br>
				กิจกรรมการขาย :: ".$data["ACTICOD"]."<br>
				วันที่ขออนุมัติ :: ".$this->Convertdate(2,$createDate)."
			";
			
			echo json_encode($response); exit;
		}
		
		
		$response["html"] = $data;
		echo json_encode($response);
	}
	
	function dataCUS(){
		$cuscod = $_POST["cuscod"];
		
		$data = array();
		$sql = "
			select b.CUSSTAT,b.CUSBABY,b.ADDRNO as ADDRNOLAST,b.ADDRDOCNO as ADDRDOCNOLAST
				,b.SOCAILSECURITY,b.CAREER,b.CAREERADDR,b.CAREERTEL
				,b.HOSTNAME,b.HOSTIDNO,b.HOSTTEL,b.HOSTRELATION
				,b.EMPRELATION
				,isnull(( 
					select top 1 employeeCode+' :: '+firstName+' '+LastName as Name 
					from {$this->MAuth->getdb('hp_vusers')} as sa
					where sa.IDNo = b.EMPRELATION collate thai_cs_as
				),'') as EMPRELATIONNAME
				,b.REFERANT
			from {$this->MAuth->getdb('ARANALYZE')} a
			left join {$this->MAuth->getdb('ARANALYZEREF')} b on a.ID=b.ID
			left join (
				select MAX(a.CREATEDATE) as CREATEDATE,b.CUSCOD from {$this->MAuth->getdb('ARANALYZE')} a
				left join {$this->MAuth->getdb('ARANALYZEREF')} b on a.ID=b.ID
				where b.CUSCOD='".$cuscod."' and a.ANSTAT != 'C' collate thai_ci_as
				group by b.CUSCOD
			) c on a.CREATEDATE=c.CREATEDATE and b.CUSCOD=c.CUSCOD
			where c.CUSCOD is not null
		";
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				foreach($row as $key => $val){
					switch($key){
						default:  $data[$key] = $val; break;
					}
				}
			}
		}
		
		$sql = "
			declare @filePath varchar(250) = (
				select filePath from {$this->MAuth->getdb('config_fileupload')}
				where ftpfolder like 'Senior/%/ANALYZE' and ftpstatus='Y' and refno='".$this->sess["db"]."'
			);
			set @filePath = (
				select @filePath + convert(varchar(30),cast([PICTUR] as varbinary))
				from {$this->MAuth->getdb('CUSTPICT')} 
				where CUSCOD='".$cuscod."'
			)
			select a.CUSCOD
				,a.SNAM+a.NAME1+' '+a.NAME2+' ('+a.CUSCOD+')-'+a.GRADE as CUSNAME
				,a.IDNO,convert(varchar(8),a.BIRTHDT,112) as BIRTHDT,convert(varchar(8),a.EXPDT,112) as EXPDT
				,(select y from {$this->MAuth->getdb('FN_009_datediffYearMonthDay')}(a.BIRTHDT,GETDATE())) as AGE
				,a.ADDRNO
				,'('+a.ADDRNO+') '+b.ADDR1+' '+b.ADDR2+' ต.'+b.TUMB+' อ.'+c.AUMPDES+' จ.'+d.PROVDES+' '+b.ZIP as ADDR
				,a.MOBILENO,isnull(a.MREVENU,0) as MREVENU
				,a.OCCUP,a.OFFIC,a.GRADE,isnull(@filePath,'(none)') as filePath
			from {$this->MAuth->getdb('CUSTMAST')} a 
			left join {$this->MAuth->getdb('CUSTADDR')} b on a.CUSCOD=b.CUSCOD and a.ADDRNO=b.ADDRNO
			left join {$this->MAuth->getdb('SETAUMP')} c on b.AUMPCOD=c.AUMPCOD
			left join {$this->MAuth->getdb('SETPROV')} d on c.PROVCOD=d.PROVCOD
			where a.CUSCOD='".$cuscod."'
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		if($query->row()){
			foreach($query->result() as $row){
				foreach($row as $key => $val){
					switch($key){
						case 'BIRTHDT': $data[$key] = $this->Convertdate(2,$val); break;
						case 'EXPDT': $data[$key] = $this->Convertdate(2,$val); break;
						default:  $data[$key] = $val; break;
					}
				}
			}
		}
		
		$sql = "
			select (
				select count(*) r from {$this->MAuth->getdb('ARMAST')}
				where CUSCOD='".$cuscod."' and SDATE between convert(varchar(8),dateadd(day,-7,getdate()),112) and convert(varchar(8),getdate(),112)
			) as ARM
			,(
				select count(*) r from {$this->MAuth->getdb('ARRESV')}
				where CUSCOD='".$cuscod."' and SDATE is null and RESVDT between convert(varchar(8),dateadd(day,-7,getdate()),112) and convert(varchar(8),getdate(),112)					
			) as ARR
		";
		$query = $this->db->query($sql);
		
		if($query->row()){
			foreach($query->result() as $row){
				foreach($row as $key => $val){
					switch($key){
						default:  $data[$key] = $val; break;
					}
				}
			}
		}
		
		
		$response = array("html"=>$data);
		echo json_encode($response);
	}
	
	/*
	function save_picture_old($picture){
		//print_r($picture); exit;
		foreach($picture as $key => $arrs){
			if($arrs["name"] != ""){
				if(isset($arrs["anid"])){
					$ex = explode(".",$arrs["name"]);
					$picture_name = $arrs["anid"].".".$ex[sizeof($ex)-1];
				}else{
					$picture_name = $arrs["name"];
				}
				
				$img  = $arrs["tmp"];
				$img  = str_replace('data:image/tmp;base64,', '', $img);
				$img  = str_replace(' ', '+', $img);
				$data = base64_decode($img);
				file_put_contents('public/images/analyze/'.$picture_name, $data);
				
				$sql = "
					select ftpserver, ftpuser, ftppass, ftpfolder, filePath
					from {$this->MAuth->getdb('config_fileupload')}
					where refno = '".$this->sess["db"]."' and ftpfolder like 'Senior/%/ANALYZE' and ftpstatus = 'Y'
				";
				$query = $this->db->query($sql);
				
				$ftp_server 	= "";
				$ftp_user_name 	= "";
				$ftp_user_pass 	= "";
				
				$arrsResult =  array();
				if($query->row()){
					foreach($query->result() as $row){
						foreach($row as $key => $val){
							$arrsResult[$key] = $val;
						}
					}
				}
				
				$ftp_server 	= $arrsResult['ftpserver'];
				$ftp_user_name 	= $arrsResult['ftpuser'];
				$ftp_user_pass 	= $arrsResult['ftppass'];
				
				$conn_id 		= ftp_connect($ftp_server);		
				$login_result 	= ftp_login($conn_id, $ftp_user_name, $ftp_user_pass); 
				ftp_chdir($conn_id,$arrsResult['ftpfolder']);
				
				if ((!$conn_id) || (!$login_result)) {
					$response["error"] = true;
					$response["msg"][] = "FTP connection has failed!<br/>Attempted to connect to server for user {$ftp_user_name}";
					echo json_encode($response); exit;
				}
				
				$output_dir = "/".$arrsResult['ftpfolder']."/";
				$destination_file = $output_dir.$picture_name;
				$source_file	  = 'public/images/analyze/'.$picture_name;
				if(ftp_put($conn_id, $destination_file, $source_file, FTP_BINARY)){
					if(isset($arrs["anid"])){
						$sql = "
							update {$this->MAuth->getdb('ARANALYZEDATA')}
							set EVIDENCE='{$picture_name}'
							where ID='{$arrs["anid"]}'
						";
					}else{
						$sql = "
							begin
								insert into {$this->MAuth->getdb('CUSTPICT')} (CUSCOD,PICTUR)
								select '{$arrs["cuscod"]}','{$picture_name}'
							end
						";
					}
					$this->db->query($sql);
					
					unlink('public/images/analyze/'.$picture_name); //ลบรูปที่บันทึกแล้วออก
				}
				ftp_close($conn_id);
			}
		}
	}
	*/
	
	function save_picture($picture){
		//print_r($picture); exit;
		foreach($picture as $key => $arrs){
			if($arrs["name"] != ""){
				if(isset($arrs["anid"])){
					$ex = explode(".",$arrs["name"]);
					$picture_name = $arrs["anid"].".".$ex[sizeof($ex)-1];
				}else{
					$picture_name = $arrs["name"];
				}
				
				$sql = "
					select ftpserver, ftpuser, ftppass, ftpfolder, filePath
					from {$this->MAuth->getdb('config_fileupload')}
					where refno = '".$this->sess["db"]."' and ftpfolder like 'Senior/%/ANALYZE' and ftpstatus = 'Y'
				";
				$query = $this->db->query($sql);
				
				$ftp_server 	= "";
				$ftp_user_name 	= "";
				$ftp_user_pass 	= "";
				
				$arrsResult =  array();
				if($query->row()){
					foreach($query->result() as $row){
						foreach($row as $key => $val){
							$arrsResult[$key] = $val;
						}
					}
				}
				
				$ftp_server 	= $arrsResult['ftpserver'];
				$ftp_user_name 	= $arrsResult['ftpuser'];
				$ftp_user_pass 	= $arrsResult['ftppass'];
				
				$conn_id 		= ftp_connect($ftp_server);		
				$login_result 	= ftp_login($conn_id, $ftp_user_name, $ftp_user_pass); 
				ftp_chdir($conn_id,$arrsResult['ftpfolder']);
				
				if ((!$conn_id) || (!$login_result)) {
					$response["error"] = true;
					$response["msg"][] = "FTP connection has failed!<br/>Attempted to connect to server for user {$ftp_user_name}";
					echo json_encode($response); exit;
				}
				
				$img  = $arrs["tmp"];
				$img  = str_replace('data:image/tmp;base64,', '', $img);
				$img  = str_replace(' ', '+', $img);
				$data = base64_decode($img);
				
				
				
				// Initializing new session 
				$ch = curl_init("http://".$ftp_server.'/'.$arrsResult['ftpfolder'].'/'.$picture_name); 
				// Request method is set 
				curl_setopt($ch, CURLOPT_NOBODY, true); 
				// Executing cURL session 
				curl_exec($ch); 
				// Getting information about HTTP Code 
				$retcode = curl_getinfo($ch, CURLINFO_HTTP_CODE); 				  
				// Testing for 200
				if($retcode == 200) {
					ftp_delete($conn_id, '/'.$arrsResult['ftpfolder'].'/'.$picture_name);
				}
				
				if(file_put_contents('ftp://'.$ftp_user_name.':'.$ftp_user_pass.'@'.$ftp_server.'/'.$arrsResult['ftpfolder'].'/'.$picture_name, $data)){
					if(isset($arrs["anid"])){
						$sql = "
							update {$this->MAuth->getdb('ARANALYZEDATA')}
							set EVIDENCE='{$picture_name}'
							where ID='{$arrs["anid"]}'
						";
					}else{
						$sql = "
							if exists (select * from {$this->MAuth->getdb('CUSTPICT')} where CUSCOD='{$arrs["cuscod"]}')
							begin
								update {$this->MAuth->getdb('CUSTPICT')} 
								set PICTUR='{$picture_name}'
								where CUSCOD='{$arrs["cuscod"]}'
							end 
							else 
							begin
								insert into {$this->MAuth->getdb('CUSTPICT')} (CUSCOD,PICTUR)
								select '{$arrs["cuscod"]}','{$picture_name}'
							end
						";
					}
					$this->db->query($sql);					
				}
				
				ftp_close($conn_id);			
			}
		}
	}
	
	function save(){
		$this->saveCheck();
		$response = array("error"=>false,"msg"=>array());
		
		$arrs = array();
		$arrs["anid"] 		= $_POST["anid"];
		$arrs["locat"] 		= "'".$_POST["locat"]."'";
		$arrs["acticod"] 	= "'".$_POST["acticod"]."'";
		$arrs["resvno"] 	= ($_POST["resvno"] == "" ? "NULL":"'".$_POST["resvno"]."'");
		$arrs["resvAmt"] 	= ($_POST["resvAmt"] == "" ? "NULL":"'".$_POST["resvAmt"]."'");
		$arrs["dwnAmt"] 	= ($_POST["dwnAmt"] == "" ? "NULL":"'".$_POST["dwnAmt"]."'");
		$arrs["insuranceType"] = ($_POST["insuranceType"] == "" ? "NULL":"'".$_POST["insuranceType"]."'");
		$arrs["insuranceAmt"]  = ($_POST["insuranceAmt"] == "" ? "NULL":"'".$_POST["insuranceAmt"]."'");
		$arrs["nopay"] 		= "'".$_POST["nopay"]."'";
		$arrs["strno"] 		= "'".$_POST["strno"]."'";
		$arrs["model"]		= "'".$_POST["model"]."'";
		$arrs["baab"] 		= "'".$_POST["baab"]."'";
		$arrs["color"] 		= "'".$_POST["color"]."'";
		$arrs["stat"] 		= "'".$_POST["stat"]."'";
		$arrs["sdateold"] 	= ($_POST["sdateold"] == "" ? "NULL":"'".$this->Convertdate(1,$_POST["sdateold"])."'");
		$arrs["ydate"] 		= ($_POST["ydate"] == "" ? "NULL":"'".$this->Convertdate(1,$_POST["ydate"])."'");
		$arrs["price"] 		= "'".$_POST["price"]."'";
		$arrs["stdid"] 		= "'".$_POST["stdid"]."'";
		$arrs["subid"]		= "'".$_POST["subid"]."'";
		$arrs["shcid"]		= "'".$_POST["shcid"]."'";
		$arrs["interatert"]	= "'".$_POST["interatert"]."'";
		
		$arrs["cuscod"] 	= "'".$_POST["cuscod"]."'";
		$arrs["idno"] 		= "'".$_POST["idno"]."'";
		$arrs["idnoBirth"] 	= ($_POST["idnoBirth"] == "" ? "NULL":"'".$_POST["idnoBirth"]."'");
		$arrs["idnoExpire"] = ($_POST["idnoExpire"] == "" ? "NULL":"'".$_POST["idnoExpire"]."'");
		$arrs["idnoAge"] 	= "'".$_POST["idnoAge"]."'";
		$arrs["idnoStat"] 	= "'".$_POST["idnoStat"]."'";
		$arrs["addr1"] 		= "'".$_POST["addr1"]."'";
		$arrs["addr2"] 		= "'".$_POST["addr2"]."'";
		$arrs["phoneNumber"] 	= "'".$_POST["phoneNumber"]."'";
		$arrs["baby"] 			= "'".$_POST["baby"]."'";		
		$arrs["socialSecurity"] = "'".$_POST["socialSecurity"]."'";
		$arrs["career"] 		= "'".$_POST["career"]."'";
		$arrs["careerOffice"] 	= "'".$_POST["careerOffice"]."'";
		$arrs["careerPhone"] 	= "'".$_POST["careerPhone"]."'";
		$arrs["income"] 		= "'".str_replace(",","",$_POST["income"])."'";
		$arrs["hostName"] 		= "'".$_POST["hostName"]."'";
		$arrs["hostIDNo"] 		= "'".$_POST["hostIDNo"]."'";
		$arrs["hostPhone"] 		= "'".$_POST["hostPhone"]."'";
		$arrs["hostRelation"] 	= "'".$_POST["hostRelation"]."'";
		$arrs["empRelation"] 	= "'".$_POST["empRelation"]."'";
		$arrs["reference"] 		= "'".$_POST["reference"]."'";
		
		$arrs["is1_cuscod"] 	= "'".$_POST["is1_cuscod"]."'";
		$arrs["is1_idno"] 		= "'".$_POST["is1_idno"]."'";
		$arrs["is1_idnoBirth"] 	= ($_POST["is1_idnoBirth"] == "" ? "NULL":"'".$_POST["is1_idnoBirth"]."'");
		$arrs["is1_idnoExpire"] = ($_POST["is1_idnoExpire"] == "" ? "NULL":"'".$_POST["is1_idnoExpire"]."'");
		$arrs["is1_idnoAge"] 	= "'".$_POST["is1_idnoAge"]."'";
		$arrs["is1_idnoStat"] 	= "'".$_POST["is1_idnoStat"]."'";
		$arrs["is1_addr1"] 		= "'".$_POST["is1_addr1"]."'";
		$arrs["is1_addr2"] 		= "'".$_POST["is1_addr2"]."'";
		$arrs["is1_phoneNumber"] 	= "'".$_POST["is1_phoneNumber"]."'";
		$arrs["is1_baby"] 			= "'".$_POST["is1_baby"]."'";		
		$arrs["is1_socialSecurity"] = "'".$_POST["is1_socialSecurity"]."'";
		$arrs["is1_career"] 		= "'".$_POST["is1_career"]."'";
		$arrs["is1_careerOffice"] 	= "'".$_POST["is1_careerOffice"]."'";
		$arrs["is1_careerPhone"] 	= "'".$_POST["is1_careerPhone"]."'";
		$arrs["is1_income"] 		= "'".str_replace(",","",$_POST["is1_income"])."'";
		$arrs["is1_hostName"] 		= "'".$_POST["is1_hostName"]."'";
		$arrs["is1_hostIDNo"] 		= "'".$_POST["is1_hostIDNo"]."'";
		$arrs["is1_hostPhone"] 		= "'".$_POST["is1_hostPhone"]."'";
		$arrs["is1_hostRelation"] 	= "'".$_POST["is1_hostRelation"]."'";
		$arrs["is1_empRelation"] 	= "'".$_POST["is1_empRelation"]."'";
		$arrs["is1_reference"] 		= "'".$_POST["is1_reference"]."'";
		
		$arrs["is2_cuscod"] 	= "'".$_POST["is2_cuscod"]."'";
		$arrs["is2_idno"] 		= "'".$_POST["is2_idno"]."'";
		$arrs["is2_idnoBirth"] 	= ($_POST["is2_idnoBirth"] == "" ? "NULL":"'".$_POST["is2_idnoBirth"]."'");
		$arrs["is2_idnoExpire"] = ($_POST["is2_idnoExpire"] == "" ? "NULL":"'".$_POST["is2_idnoExpire"]."'");
		$arrs["is2_idnoAge"] 	= "'".$_POST["is2_idnoAge"]."'";
		$arrs["is2_idnoStat"] 	= "'".$_POST["is2_idnoStat"]."'";
		$arrs["is2_addr1"] 		= "'".$_POST["is2_addr1"]."'";
		$arrs["is2_addr2"] 		= "'".$_POST["is2_addr2"]."'";
		$arrs["is2_phoneNumber"] 	= "'".$_POST["is2_phoneNumber"]."'";
		$arrs["is2_baby"] 			= "'".$_POST["is2_baby"]."'";		
		$arrs["is2_socialSecurity"] = "'".$_POST["is2_socialSecurity"]."'";
		$arrs["is2_career"] 		= "'".$_POST["is2_career"]."'";
		$arrs["is2_careerOffice"] 	= "'".$_POST["is2_careerOffice"]."'";
		$arrs["is2_careerPhone"] 	= "'".$_POST["is2_careerPhone"]."'";
		$arrs["is2_income"] 		= "'".str_replace(",","",$_POST["is2_income"])."'";
		$arrs["is2_hostName"] 		= "'".$_POST["is2_hostName"]."'";
		$arrs["is2_hostIDNo"] 		= "'".$_POST["is2_hostIDNo"]."'";
		$arrs["is2_hostPhone"] 		= "'".$_POST["is2_hostPhone"]."'";
		$arrs["is2_hostRelation"] 	= "'".$_POST["is2_hostRelation"]."'";
		$arrs["is2_empRelation"] 	= "'".$_POST["is2_empRelation"]."'";
		$arrs["is2_reference"] 		= "'".$_POST["is2_reference"]."'";
		
		$arrs["is3_cuscod"] 	= "'".$_POST["is3_cuscod"]."'";
		$arrs["is3_idno"] 		= "'".$_POST["is3_idno"]."'";
		$arrs["is3_idnoBirth"] 	= ($_POST["is3_idnoBirth"] == "" ? "NULL":"'".$_POST["is3_idnoBirth"]."'");
		$arrs["is3_idnoExpire"] = ($_POST["is3_idnoExpire"] == "" ? "NULL":"'".$_POST["is3_idnoExpire"]."'");
		$arrs["is3_idnoAge"] 	= "'".$_POST["is3_idnoAge"]."'";
		$arrs["is3_idnoStat"] 	= "'".$_POST["is3_idnoStat"]."'";
		$arrs["is3_addr1"] 		= "'".$_POST["is3_addr1"]."'";
		$arrs["is3_addr2"] 		= "'".$_POST["is3_addr2"]."'";
		$arrs["is3_phoneNumber"] 	= "'".$_POST["is3_phoneNumber"]."'";
		$arrs["is3_baby"] 			= "'".$_POST["is3_baby"]."'";		
		$arrs["is3_socialSecurity"] = "'".$_POST["is3_socialSecurity"]."'";
		$arrs["is3_career"] 		= "'".$_POST["is3_career"]."'";
		$arrs["is3_careerOffice"] 	= "'".$_POST["is3_careerOffice"]."'";
		$arrs["is3_careerPhone"] 	= "'".$_POST["is3_careerPhone"]."'";
		$arrs["is3_income"] 		= "'".str_replace(",","",$_POST["is3_income"])."'";
		$arrs["is3_hostName"] 		= "'".$_POST["is3_hostName"]."'";
		$arrs["is3_hostIDNo"] 		= "'".$_POST["is3_hostIDNo"]."'";
		$arrs["is3_hostPhone"] 		= "'".$_POST["is3_hostPhone"]."'";
		$arrs["is3_hostRelation"] 	= "'".$_POST["is3_hostRelation"]."'";
		$arrs["is3_empRelation"] 	= "'".$_POST["is3_empRelation"]."'";
		$arrs["is3_reference"] 		= "'".$_POST["is3_reference"]."'";
		
		$arrs["empIDNo"] 	= "'".$_POST["empIDNo"]."'";
		$arrs["empTel"] 	= "'".$_POST["empTel"]."'";
		$arrs["mngIDNo"] 	= "'".$_POST["mngIDNo"]."'";
		$arrs["mngTel"] 	= "'".$_POST["mngTel"]."'";
		
		$picture = array();
		$picture[0]["tmp"] 	= (isset($_POST["picture"])?$_POST["picture"]:'');
		$picture[0]["name"] = (isset($_POST["picture_name"])?$_POST["picture_name"]:'');
		$picture[0]["cuscod"] = $_POST["cuscod"];
		
		$picture[1]["tmp"] 	= (isset($_POST["is1_picture"])?$_POST["is1_picture"]:'');
		$picture[1]["name"] = (isset($_POST["is1_picture_name"])?$_POST["is1_picture_name"]:'');
		$picture[1]["cuscod"] = $_POST["is1_cuscod"];
		
		$picture[2]["tmp"] 	= (isset($_POST["is2_picture"])?$_POST["is2_picture"]:'');
		$picture[2]["name"] = (isset($_POST["is2_picture_name"])?$_POST["is2_picture_name"]:'');
		$picture[2]["cuscod"] = $_POST["is2_cuscod"];
		
		$picture[3]["tmp"] 	= (isset($_POST["is3_picture"])?$_POST["is3_picture"]:'');
		$picture[3]["name"] = (isset($_POST["is3_picture_name"])?$_POST["is3_picture_name"]:'');
		$picture[3]["cuscod"] = $_POST["is3_cuscod"];

		$picture[4]["tmp"] 	= (isset($_POST["analyze_picture"])?$_POST["analyze_picture"]:'');
		$picture[4]["name"] = (isset($_POST["analyze_picture_name"])?$_POST["analyze_picture_name"]:'');
		$picture[4]["cuscod"] = $_POST["is3_cuscod"];
		$picture[4]["anid"] = "";
		
		if($arrs["anid"] == "Auto Genarate"){
			$sql = "
				if object_id('tempdb..#transaction') is not null drop table #transaction;
				create table #transaction (error varchar(1),id varchar(12),msg varchar(max));

				begin tran ins
				begin try
					/* @symbol = สัญลักษณ์แทนประเภทของเลขที่ นั้นๆ */
					declare @symbol varchar(10) = (select H_ANALYZE from {$this->MAuth->getdb('CONDPAY')});
					/* @rec = รหัสพื้นฐาน */
					declare @rec varchar(10) = (select SHORTL+@symbol+'-'+right(left(convert(varchar(8),GETDATE(),112),6),4) from {$this->MAuth->getdb('INVLOCAT')} where LOCATCD=".$arrs['locat'].");
					/* @ANID = รหัสที่จะใช้ */
					declare @ANID varchar(12) = (select isnull(MAX(ID),@rec+'0000') from ( 
						select ID collate Thai_CS_AS as ID from {$this->MAuth->getdb('ARANALYZE')} where ID like ''+@rec+'%' collate thai_cs_as
					) as a);
					set @ANID = left(@ANID ,8)+right(right(@ANID ,4)+10001,4);
					
					declare @ANID_CHECK varchar(12) = isnull((select ID from {$this->MAuth->getdb('ARANALYZE')} where RESVNO=".$arrs["resvno"]." and ANSTAT != 'C'),'');
					if(@ANID_CHECK != '')
					begin
						rollback tran ins;
						insert into #transaction select 'y' as error,'' as id,'ผิดพลาด เลขที่บิลจอง {$_POST["resvno"]} ถูกบันทึกในใบวิเคราะห์เลขที่ '+@ANID_CHECK+' แล้ว ไม่สามารถเพิ่มซ้ำได้ครับ' as msg;
						return;
					end
					
					if not exists (
						select * from {$this->MAuth->getdb('STDVehiclesDetail')}
						where STDID=".$arrs["stdid"]." and SUBID=".$arrs["subid"]."
					)
					begin
						rollback tran ins;
						insert into #transaction select 'y' as error,'' as id,'ผิดพลาด ไม่พบสแตนดาร์ด โปรดตรวจสอบใหม่อีกครั้งครับ' as msg;
						return;
					end
					
					
					declare @id bigint;
					insert into {$this->MAuth->getdb('ARANALYZE')} (
						ID,LOCAT,ACTICOD,RESVNO,RESVAMT,DWN,INSURANCE_TYP,DWN_INSURANCE,INTEREST_RT,NOPAY,STRNO,MODEL
						,BAAB,COLOR,STAT,SDATE,YDATE,PRICE,ANSTAT,STDID,SUBID,SHCID,INSBY,INSDT
					) 
					select @ANID,".$arrs["locat"].",".$arrs["acticod"].",".$arrs["resvno"].",".$arrs["resvAmt"].",".$arrs["dwnAmt"].",".$arrs["insuranceType"]."
						,".$arrs["insuranceAmt"].",".$arrs["interatert"].",".$arrs["nopay"].",".$arrs["strno"].",".$arrs["model"]."
						,".$arrs["baab"].",".$arrs["color"].",".$arrs["stat"].",".$arrs["sdateold"].",".$arrs["ydate"]."
						,".$arrs["price"].",'I',".$arrs["stdid"].",".$arrs["subid"].",".$arrs["shcid"].",'".$this->sess["IDNo"]."',getdate();
					
					insert into {$this->MAuth->getdb('ARANALYZEREF')} (
						ID,CUSCOD,CUSTYPE,CUSSTAT,CUSBABY,ADDRNO,ADDRDOCNO,SOCAILSECURITY,CAREER,CAREERADDR,
						CAREERTEL,HOSTNAME,HOSTIDNO,HOSTTEL,HOSTRELATION,EMPRELATION,REFERANT
					)
					select @ANID,".$arrs["cuscod"].",0,".$arrs["idnoStat"].",".$arrs["baby"].",".$arrs["addr1"]."
						,".$arrs["addr2"].",".$arrs["socialSecurity"].",".$arrs["career"].",".$arrs["careerOffice"]."
						,".$arrs["careerPhone"].",".$arrs["hostName"].",".$arrs["hostIDNo"]."
						,".$arrs["hostPhone"].",".$arrs["hostRelation"].",".$arrs["empRelation"]."
						,".$arrs["reference"]."
					union all
					select @ANID,".$arrs["is1_cuscod"].",1,".$arrs["is1_idnoStat"].",".$arrs["is1_baby"].",".$arrs["is1_addr1"]."
						,".$arrs["is1_addr2"].",".$arrs["is1_socialSecurity"].",".$arrs["is1_career"].",".$arrs["is1_careerOffice"]."
						,".$arrs["is1_careerPhone"].",".$arrs["is1_hostName"].",".$arrs["is1_hostIDNo"]."
						,".$arrs["is1_hostPhone"].",".$arrs["is1_hostRelation"].",".$arrs["is1_empRelation"]."
						,".$arrs["is1_reference"]."
					
					update {$this->MAuth->getdb('CUSTMAST')}
					set MOBILENO=".$arrs["phoneNumber"]."
						,OCCUP=".$arrs["career"]."
						,OFFIC=".$arrs["careerOffice"]."
						,AGE=".$arrs["idnoAge"]."
						,MREVENU=".$arrs["income"]."
					where CUSCOD=".$arrs["cuscod"]."
					
					update {$this->MAuth->getdb('CUSTMAST')}
					set MOBILENO=".$arrs["phoneNumber"]."
						,OCCUP=".$arrs["career"]."
						,OFFIC=".$arrs["careerOffice"]."
						,AGE=".$arrs["idnoAge"]."
						,MREVENU=".$arrs["income"]."
					where CUSCOD=".$arrs["cuscod"]."
					update {$this->MAuth->getdb('CUSTMAST')}
					set MOBILENO=".$arrs["is1_phoneNumber"]."
						,OCCUP=".$arrs["is1_career"]."
						,OFFIC=".$arrs["is1_careerOffice"]."
						,AGE=".$arrs["is1_idnoAge"]."
						,MREVENU=".$arrs["is1_income"]."
					where CUSCOD=".$arrs["is1_cuscod"]."
					
					if(".$arrs["is2_cuscod"]." <> '')
					begin 
						insert into {$this->MAuth->getdb('ARANALYZEREF')} (
							ID,CUSCOD,CUSTYPE,CUSSTAT,CUSBABY,ADDRNO,ADDRDOCNO,SOCAILSECURITY,CAREER,CAREERADDR,
							CAREERTEL,HOSTNAME,HOSTIDNO,HOSTTEL,HOSTRELATION,EMPRELATION,REFERANT
						)
						select @ANID,".$arrs["is2_cuscod"].",2,".$arrs["is2_idnoStat"].",".$arrs["is2_baby"].",".$arrs["is2_addr1"]."
							,".$arrs["is2_addr2"].",".$arrs["is2_socialSecurity"].",".$arrs["is2_career"].",".$arrs["is2_careerOffice"]."
							,".$arrs["is2_careerPhone"].",".$arrs["is2_hostName"].",".$arrs["is2_hostIDNo"]."
							,".$arrs["is2_hostPhone"].",".$arrs["is2_hostRelation"].",".$arrs["is2_empRelation"]."
							,".$arrs["is2_reference"].";
							
						update {$this->MAuth->getdb('CUSTMAST')}
						set MOBILENO=".$arrs["is2_phoneNumber"]."
							,OCCUP=".$arrs["is2_career"]."
							,OFFIC=".$arrs["is2_careerOffice"]."
							,AGE=".$arrs["is2_idnoAge"]."
							,MREVENU=".$arrs["is2_income"]."
						where CUSCOD=".$arrs["is2_cuscod"]."
					end	

					if(".$arrs["is3_cuscod"]." <> '')
					begin 
						insert into {$this->MAuth->getdb('ARANALYZEREF')} (
							ID,CUSCOD,CUSTYPE,CUSSTAT,CUSBABY,ADDRNO,ADDRDOCNO,SOCAILSECURITY,CAREER,CAREERADDR,
							CAREERTEL,HOSTNAME,HOSTIDNO,HOSTTEL,HOSTRELATION,EMPRELATION,REFERANT
						)
						select @ANID,".$arrs["is3_cuscod"].",3,".$arrs["is3_idnoStat"].",".$arrs["is3_baby"].",".$arrs["is3_addr1"]."
							,".$arrs["is3_addr2"].",".$arrs["is3_socialSecurity"].",".$arrs["is3_career"].",".$arrs["is3_careerOffice"]."
							,".$arrs["is3_careerPhone"].",".$arrs["is3_hostName"].",".$arrs["is3_hostIDNo"]."
							,".$arrs["is3_hostPhone"].",".$arrs["is3_hostRelation"].",".$arrs["is3_empRelation"]."
							,".$arrs["is3_reference"].";
							
						update {$this->MAuth->getdb('CUSTMAST')}
						set MOBILENO=".$arrs["is3_phoneNumber"]."
							,OCCUP=".$arrs["is3_career"]."
							,OFFIC=".$arrs["is3_careerOffice"]."
							,AGE=".$arrs["is3_idnoAge"]."
							,MREVENU=".$arrs["is3_income"]."
						where CUSCOD=".$arrs["is3_cuscod"]."
					end		
					
					if exists(select * from {$this->MAuth->getdb('ARANALYZEDATA')} where ID=@ANID)
					begin 
						update {$this->MAuth->getdb('ARANALYZEDATA')}
						set EMP=".$arrs["empIDNo"]."
							,EMPTEL=".$arrs["empTel"]."
							,MNG=".$arrs["mngIDNo"]."
							,MNGTEL=".$arrs["mngTel"]."					
						where ID=@ANID
					end
					else 
					begin
						insert into {$this->MAuth->getdb('ARANALYZEDATA')}(ID,EMP,EMPTEL,MNG,MNGTEL)
						select @ANID,".$arrs["empIDNo"].",".$arrs["empTel"].",".$arrs["mngIDNo"].",".$arrs["mngTel"].";
					end
					
					insert into {$this->MAuth->getdb('STDUSAGE')} (CONTNO,TSALE,STDID,SUBID,SHCID)
					select @ANID,'V',".$arrs["stdid"].",".$arrs["subid"].",".$arrs["shcid"]."
					
					insert into {$this->MAuth->getdb('hp_UserOperationLog')} (userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
					values ('".$this->sess["IDNo"]."','SYS04::สร้างใบวิเคราะห์สินเชื่อ',@ANID+' ".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
					
					insert into #transaction select 'n' as error,@ANID as id,'สร้างใบวิเคราะห์สินเชื่อเสร็จแล้วครับ <br>เลขที่ใบวิเคราะห์สินเชื่อ '+@ANID+' ' as msg;
					commit tran ins;
				end try
				begin catch
					rollback tran ins;
					insert into #transaction select 'y' as error,'' as id,ERROR_MESSAGE() as msg;
				end catch		
			";
			$this->db->query($sql);
			//echo $sql; exit;
		}else{
			$sql = "
				if object_id('tempdb..#transaction') is not null drop table #transaction;
				create table #transaction (error varchar(1),id varchar(12),msg varchar(max));

				begin tran ins
				begin try
					declare @ANID varchar(12) = '{$arrs["anid"]}';
					
					if exists (select * from {$this->MAuth->getdb('ARANALYZE')} where ID=@ANID)
					begin
						update {$this->MAuth->getdb('ARANALYZE')}
						set ACTICOD=".$arrs["acticod"]."
							,DWN=".$arrs["dwnAmt"]."
							,INSURANCE_TYP=".$arrs["insuranceType"]."
							,DWN_INSURANCE=".$arrs["insuranceAmt"]."
							,NOPAY=".$arrs["nopay"]."
							,PRICE=".$arrs["price"]."
							,INTEREST_RT=".$arrs["interatert"]."
						where ID=@ANID
					end
					
					if exists (select * from {$this->MAuth->getdb('ARANALYZEREF')} where ID=@ANID and CUSTYPE=0)
					begin
						update {$this->MAuth->getdb('ARANALYZEREF')}
						set CUSCOD=".$arrs["cuscod"]."
							,CUSSTAT=".$arrs["idnoStat"]."
							,CUSBABY=".$arrs["baby"]."
							,ADDRNO=".$arrs["addr1"]."
							,ADDRDOCNO=".$arrs["addr2"]."
							,SOCAILSECURITY=".$arrs["socialSecurity"]."
							,CAREER=".$arrs["career"]."
							,CAREERADDR=".$arrs["careerOffice"]."
							,CAREERTEL=".$arrs["careerPhone"]."
							,HOSTNAME=".$arrs["hostName"]."
							,HOSTIDNO=".$arrs["hostIDNo"]."
							,HOSTTEL=".$arrs["hostPhone"]."
							,HOSTRELATION=".$arrs["hostRelation"]."
							,EMPRELATION=".$arrs["empRelation"]."
							,REFERANT=".$arrs["reference"]."
						where ID=@ANID and CUSTYPE=0
						
						update {$this->MAuth->getdb('CUSTMAST')}
						set MOBILENO=".$arrs["phoneNumber"]."
							,OCCUP=".$arrs["career"]."
							,OFFIC=".$arrs["careerOffice"]."
							,AGE=".$arrs["idnoAge"]."
							,MREVENU=".$arrs["income"]."
						where CUSCOD=".$arrs["cuscod"]."
					end
					
					if exists (select * from {$this->MAuth->getdb('ARANALYZEREF')} where ID=@ANID and CUSTYPE=1)
					begin
						update {$this->MAuth->getdb('ARANALYZEREF')}
						set CUSCOD=".$arrs["is1_cuscod"]."
							,CUSSTAT=".$arrs["is1_idnoStat"]."
							,CUSBABY=".$arrs["is1_baby"]."
							,ADDRNO=".$arrs["is1_addr1"]."
							,ADDRDOCNO=".$arrs["is1_addr2"]."
							,SOCAILSECURITY=".$arrs["is1_socialSecurity"]."
							,CAREER=".$arrs["is1_career"]."
							,CAREERADDR=".$arrs["is1_careerOffice"]."
							,CAREERTEL=".$arrs["is1_careerPhone"]."
							,HOSTNAME=".$arrs["is1_hostName"]."
							,HOSTIDNO=".$arrs["is1_hostIDNo"]."
							,HOSTTEL=".$arrs["is1_hostPhone"]."
							,HOSTRELATION=".$arrs["is1_hostRelation"]."
							,EMPRELATION=".$arrs["is1_empRelation"]."
							,REFERANT=".$arrs["is1_reference"]."
						where ID=@ANID and CUSTYPE=1
						
						update {$this->MAuth->getdb('CUSTMAST')}
						set MOBILENO=".$arrs["is1_phoneNumber"]."
							,OCCUP=".$arrs["is1_career"]."
							,OFFIC=".$arrs["is1_careerOffice"]."
							,AGE=".$arrs["is1_idnoAge"]."
							,MREVENU=".$arrs["is1_income"]."
						where CUSCOD=".$arrs["is1_cuscod"]."
					end
					else if(".$arrs["is1_cuscod"]." <> '')
					begin 
						insert into {$this->MAuth->getdb('ARANALYZEREF')} (
							ID,CUSCOD,CUSTYPE,CUSSTAT,CUSBABY,ADDRNO,ADDRDOCNO,SOCAILSECURITY,CAREER,CAREERADDR,
							CAREERTEL,HOSTNAME,HOSTIDNO,HOSTTEL,HOSTRELATION,EMPRELATION,REFERANT
						)
						select @ANID,".$arrs["is1_cuscod"].",1,".$arrs["is1_idnoStat"].",".$arrs["is1_baby"].",".$arrs["is1_addr1"]."
							,".$arrs["is1_addr2"].",".$arrs["is1_socialSecurity"].",".$arrs["is1_career"].",".$arrs["is1_careerOffice"]."
							,".$arrs["is1_careerPhone"].",".$arrs["is1_hostName"].",".$arrs["is1_hostIDNo"]."
							,".$arrs["is1_hostPhone"].",".$arrs["is1_hostRelation"].",".$arrs["is1_empRelation"]."
							,".$arrs["is1_reference"].";
							
						update {$this->MAuth->getdb('CUSTMAST')}
						set MOBILENO=".$arrs["is1_phoneNumber"]."
							,OCCUP=".$arrs["is1_career"]."
							,OFFIC=".$arrs["is1_careerOffice"]."
							,AGE=".$arrs["is1_idnoAge"]."
							,MREVENU=".$arrs["is1_income"]."
						where CUSCOD=".$arrs["is1_cuscod"]."
					end	
					
					if exists (select * from {$this->MAuth->getdb('ARANALYZEREF')} where ID=@ANID and CUSTYPE=2)
					begin
						update {$this->MAuth->getdb('ARANALYZEREF')}
						set CUSCOD=".$arrs["is2_cuscod"]."
							,CUSSTAT=".$arrs["is2_idnoStat"]."
							,CUSBABY=".$arrs["is2_baby"]."
							,ADDRNO=".$arrs["is2_addr1"]."
							,ADDRDOCNO=".$arrs["is2_addr2"]."
							,SOCAILSECURITY=".$arrs["is2_socialSecurity"]."
							,CAREER=".$arrs["is2_career"]."
							,CAREERADDR=".$arrs["is2_careerOffice"]."
							,CAREERTEL=".$arrs["is2_careerPhone"]."
							,HOSTNAME=".$arrs["is2_hostName"]."
							,HOSTIDNO=".$arrs["is2_hostIDNo"]."
							,HOSTTEL=".$arrs["is2_hostPhone"]."
							,HOSTRELATION=".$arrs["is2_hostRelation"]."
							,EMPRELATION=".$arrs["is2_empRelation"]."
							,REFERANT=".$arrs["is2_reference"]."
						where ID=@ANID and CUSTYPE=2
						
						update {$this->MAuth->getdb('CUSTMAST')}
						set MOBILENO=".$arrs["is2_phoneNumber"]."
							,OCCUP=".$arrs["is2_career"]."
							,OFFIC=".$arrs["is2_careerOffice"]."
							,AGE=".$arrs["is2_idnoAge"]."
							,MREVENU=".$arrs["is2_income"]."
						where CUSCOD=".$arrs["is2_cuscod"]."
					end
					else if(".$arrs["is2_cuscod"]." <> '')
					begin 
						insert into {$this->MAuth->getdb('ARANALYZEREF')} (
							ID,CUSCOD,CUSTYPE,CUSSTAT,CUSBABY,ADDRNO,ADDRDOCNO,SOCAILSECURITY,CAREER,CAREERADDR,
							CAREERTEL,HOSTNAME,HOSTIDNO,HOSTTEL,HOSTRELATION,EMPRELATION,REFERANT
						)
						select @ANID,".$arrs["is2_cuscod"].",2,".$arrs["is2_idnoStat"].",".$arrs["is2_baby"].",".$arrs["is2_addr1"]."
							,".$arrs["is2_addr2"].",".$arrs["is2_socialSecurity"].",".$arrs["is2_career"].",".$arrs["is2_careerOffice"]."
							,".$arrs["is2_careerPhone"].",".$arrs["is2_hostName"].",".$arrs["is2_hostIDNo"]."
							,".$arrs["is2_hostPhone"].",".$arrs["is2_hostRelation"].",".$arrs["is2_empRelation"]."
							,".$arrs["is2_reference"].";
							
						update {$this->MAuth->getdb('CUSTMAST')}
						set MOBILENO=".$arrs["is2_phoneNumber"]."
							,OCCUP=".$arrs["is2_career"]."
							,OFFIC=".$arrs["is2_careerOffice"]."
							,AGE=".$arrs["is2_idnoAge"]."
							,MREVENU=".$arrs["is2_income"]."
						where CUSCOD=".$arrs["is2_cuscod"]."
					end	
					
					if exists (select 1 from {$this->MAuth->getdb('ARANALYZEREF')} where ID=@ANID and CUSTYPE=3)
					begin
						update {$this->MAuth->getdb('ARANALYZEREF')}
						set CUSCOD=".$arrs["is3_cuscod"]."
							,CUSSTAT=".$arrs["is3_idnoStat"]."
							,CUSBABY=".$arrs["is3_baby"]."
							,ADDRNO=".$arrs["is3_addr1"]."
							,ADDRDOCNO=".$arrs["is3_addr2"]."
							,SOCAILSECURITY=".$arrs["is3_socialSecurity"]."
							,CAREER=".$arrs["is3_career"]."
							,CAREERADDR=".$arrs["is3_careerOffice"]."
							,CAREERTEL=".$arrs["is3_careerPhone"]."
							,HOSTNAME=".$arrs["is3_hostName"]."
							,HOSTIDNO=".$arrs["is3_hostIDNo"]."
							,HOSTTEL=".$arrs["is3_hostPhone"]."
							,HOSTRELATION=".$arrs["is3_hostRelation"]."
							,EMPRELATION=".$arrs["is3_empRelation"]."
							,REFERANT=".$arrs["is3_reference"]."
						where ID=@ANID and CUSTYPE=3
						
						update {$this->MAuth->getdb('CUSTMAST')}
						set MOBILENO=".$arrs["is3_phoneNumber"]."
							,OCCUP=".$arrs["is3_career"]."
							,OFFIC=".$arrs["is3_careerOffice"]."
							,AGE=".$arrs["is3_idnoAge"]."
							,MREVENU=".$arrs["is3_income"]."
						where CUSCOD=".$arrs["is3_cuscod"]."
					end
					else if(".$arrs["is3_cuscod"]." <> '')
					begin 
						insert into {$this->MAuth->getdb('ARANALYZEREF')} (
							ID,CUSCOD,CUSTYPE,CUSSTAT,CUSBABY,ADDRNO,ADDRDOCNO,SOCAILSECURITY,CAREER,CAREERADDR,
							CAREERTEL,HOSTNAME,HOSTIDNO,HOSTTEL,HOSTRELATION,EMPRELATION,REFERANT
						)
						select @ANID,".$arrs["is3_cuscod"].",3,".$arrs["is3_idnoStat"].",".$arrs["is3_baby"].",".$arrs["is3_addr1"]."
							,".$arrs["is3_addr2"].",".$arrs["is3_socialSecurity"].",".$arrs["is3_career"].",".$arrs["is3_careerOffice"]."
							,".$arrs["is3_careerPhone"].",".$arrs["is3_hostName"].",".$arrs["is3_hostIDNo"]."
							,".$arrs["is3_hostPhone"].",".$arrs["is3_hostRelation"].",".$arrs["is3_empRelation"]."
							,".$arrs["is3_reference"].";
							
						update {$this->MAuth->getdb('CUSTMAST')}
						set MOBILENO=".$arrs["is3_phoneNumber"]."
							,OCCUP=".$arrs["is3_career"]."
							,OFFIC=".$arrs["is3_careerOffice"]."
							,AGE=".$arrs["is3_idnoAge"]."
							,MREVENU=".$arrs["is3_income"]."
						where CUSCOD=".$arrs["is3_cuscod"]."
					end		
					
					if exists(select * from {$this->MAuth->getdb('ARANALYZEDATA')} where ID=@ANID)
					begin 
						update {$this->MAuth->getdb('ARANALYZEDATA')}
						set EMP=".$arrs["empIDNo"]."
							,EMPTEL=".$arrs["empTel"]."
							,MNG=".$arrs["mngIDNo"]."
							,MNGTEL=".$arrs["mngTel"]."					
						where ID=@ANID
					end
					else 
					begin
						insert into {$this->MAuth->getdb('ARANALYZEDATA')}(ID,EMP,EMPTEL,MNG,MNGTEL)
						select @ANID,".$arrs["empIDNo"].",".$arrs["empTel"].",".$arrs["mngIDNo"].",".$arrs["mngTel"].";
					end
					
					if exists (
						select * from {$this->MAuth->getdb('STDUSAGE')}
						where TSALE='V' and CONTNO=@ANID
					)
					begin
						update {$this->MAuth->getdb('STDUSAGE')} 
						set STDID=".$arrs["stdid"]."
							,SUBID=".$arrs["subid"]."
							,SHCID=".$arrs["shcid"]."
						where TSALE='V' and CONTNO=@ANID
					end
					else 
					begin
						insert into {$this->MAuth->getdb('STDUSAGE')} (CONTNO,TSALE,STDID,SUBID,SHCID)
						select @ANID,'V',".$arrs["stdid"].",".$arrs["subid"].",".$arrs["shcid"]."
					end
					
					if exists(select * from {$this->MAuth->getdb('ARANALYZE')} where ID=@ANID and ANSTAT='C')
					begin
						delete from {$this->MAuth->getdb('STDUSAGE')}
						where TSALE='V' and CONTNO=@ANID
					end
					
					insert into {$this->MAuth->getdb('hp_UserOperationLog')} (userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
					values ('".$this->sess["IDNo"]."','SYS04::แก้ไข ใบวิเคราะห์สินเชื่อ',@ANID+' ".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
					
					insert into #transaction select 'n' as error,@ANID as id,'แก้ไข ใบวิเคราะห์สินเชื่อแล้ว<br>เลขที่ใบวิเคราะห์สินเชื่อ '+@ANID+' ' as msg;
					commit tran ins;
				end try
				begin catch
					rollback tran ins;
					insert into #transaction select 'y' as error,'' as id,ERROR_MESSAGE() as msg;
				end catch		
			";
			//echo $sql; exit;
			$this->db->query($sql);
		}
		//echo $sql; exit;
		$sql   = "select * from #transaction";
		$query = $this->db->query($sql);
		
		$stat  = true;
		$msg   = '';
		$ARANALYZE_ID  = '';
		
		if($query->row()) {
			foreach ($query->result() as $row) {
				$stat = true;
				if($row->error == "n"){
					$picture[4]["anid"] = $row->id;
					$this->save_picture($picture);					
					$stat = false;
					$stat = true;
				}
				$ARANALYZE_ID = $row->id;
				$msg = $row->msg;
			}
		}else{
			$stat = false;
			$msg = "ผิดพลาด :: ไม่สามารถทำรายการได้ในขณะนี้ โปรดลองทำรายการใหม่ภายหลัง";
		}
		
		$response = array();
		$response['error'] = $stat;
		$response['msg'][] = $msg;
		$response['ARANALYZE_ID'] = $ARANALYZE_ID;
		
		echo json_encode($response); exit;
	}
	
	function saveCheck(){
		$response = array("error"=>false,"msg"=>array());
		
		// ข้อมูลรถ
		if($_POST["strno"] == ""){ 
			$response["error"] = true; 
			$response["msg"][] = "คุณยังไม่ระบุเลขตัวถัง"; 
		}
		if($_POST["dwnAmt"] == ""){ 
			$response["error"] = true; 
			$response["msg"][] = "คุณยังไม่ระบุเงินดาวน์รถ"; 
		}else if(!is_numeric(str_replace(",","",$_POST["dwnAmt"]))){ 
			$response["error"] = true; 
			$response["msg"][] = "คุณระบุเงินดาวน์รถไม่ถูกต้อง"; 
		}
		
		if($_POST["insuranceAmt"] == ""){ 
			$response["error"] = true; 
			$response["msg"][] = "คุณยังไม่ระบุเงินดาวน์ ป1"; 
		}else if(!is_numeric(str_replace(",","",$_POST["insuranceAmt"]))){
			$response["error"] = true; 
			$response["msg"][] = "คุณระบุเงินดาวน์ ป1 ไม่ถูกต้อง"; 
		}
		if($_POST["nopay"] == ""){ 
			$response["error"] = true; 
			$response["msg"][] = "คุณยังไม่ระบุจำนวนงวด"; 
		}else if(!is_numeric(str_replace(",","",$_POST["nopay"]))){
			$response["error"] = true; 
			$response["msg"][] = "คุณระบุจำนวนงวดไม่ถูกต้อง"; 
		}
		if($_POST["price"] == ""){ 
			$response["error"] = true; 
			$response["msg"][] = "คุณยังไม่ระบุราคารถ(สด) ก่อนหักส่วนลด"; 
		}else if(!is_numeric(str_replace(",","",$_POST["price"]))){
			$response["error"] = true; 
			$response["msg"][] = "คุณระบุราคารถ(สด) ก่อนหักส่วนลด ไม่ถูกต้อง"; 
		}
		
		// ลูกค้า
		if($_POST["cuscod"] == ""){ 
			$response["error"] = true; 
			$response["msg"][] = "คุณยังไม่ระบุผู้เช่าซื้อ"; 
		}else{
			if($_POST["idnoAge"] < 20 &&  $_POST["is3_cuscod"] == ""){ 
				$response["error"] = true; 
				$response["msg"][] = "ลูกค้าอายุไม่ถึง 20 ปี โปรดระบุผู้ยินยอมด้วยครับ"; 
			}
			
			if($_POST["idnoStat"] == ""){ 
				$response["error"] = true; 
				$response["msg"][] = "คุณยังไม่ระบุสถานะ ผู้เช่าซื้อ"; 
			}
			if($_POST["addr1"] == ""){ 
				$response["error"] = true; 
				$response["msg"][] = "คุณยังไม่ระบุที่อยู่ตาม ทบ.บ้าน"; 
			}
			if($_POST["addr2"] == ""){ 
				$response["error"] = true; 
				$response["msg"][] = "คุณยังไม่ระบุที่อยู่ส่งเอกสาร"; 
			}
			if($_POST["phoneNumber"] == ""){ 
				$response["error"] = true; 
				$response["msg"][] = "คุณยังไม่ระบุเบอร์ติดต่อ"; 
			}
			if($_POST["career"] == ""){ 
				$response["error"] = true; 
				$response["msg"][] = "คุณยังไม่ระบุอาชีพของผู้เช่าซื้อ"; 
			}
			if($_POST["careerOffice"] == ""){ 
				$response["error"] = true; 
				$response["msg"][] = "คุณยังไม่ระบุสถานที่ทำงาน ของผู้เช่าซื้อ"; 
			}
			if($_POST["careerPhone"] == ""){ 
				$response["error"] = true; 
				$response["msg"][] = "คุณยังไม่ระบุเบอร์ติดต่อที่ทำงาน ของผู้เช่าซื้อ"; 
			}
			if($_POST["income"] == ""){ 
				$response["error"] = true; 
				$response["msg"][] = "คุณยังไม่ระบุรายได้/เดือน ของผู้เช่าซื้อ"; 
			}else if(!is_numeric(str_replace(",","",$_POST["income"]))){
				$response["error"] = true; 
				$response["msg"][] = "คุณระบุรายได้/เดือน ของผู้เช่าซื้อ ไม่ถูกต้อง"; 
			}
		}
		
		// คนค้ำประกัน 1 กรณีรถใหม่ต้องมีคนค้ำ / รถเก่ามีหรือไม่มีก็ได้
		if($_POST["is1_cuscod"] == "" && $_POST["stat"] == "รถใหม่"){ 
			$response["error"] = true; 
			$response["msg"][] = "คุณยังไม่ระบุผู้ค้ำประกัน 1 กรณีขายรถใหม่ต้องมีผู้คำประกันอย่างน้อย 1 คน"; 
		}else if($_POST["is1_cuscod"] != ""){
			if($_POST["is1_idnoStat"] == ""){ 
				$response["error"] = true; 
				$response["msg"][] = "คุณยังไม่ระบุสถานะ ผู้ค้ำประกัน 1"; 
			}
			if($_POST["is1_addr1"] == ""){ 
				$response["error"] = true; 
				$response["msg"][] = "คุณยังไม่ระบุที่อยู่ตาม ทบ.บ้าน"; 
			}
			if($_POST["is1_addr2"] == ""){ 
				$response["error"] = true; 
				$response["msg"][] = "คุณยังไม่ระบุที่อยู่ส่งเอกสาร"; 
			}
			if($_POST["is1_phoneNumber"] == ""){ 
				$response["error"] = true; 
				$response["msg"][] = "คุณยังไม่ระบุเบอร์ติดต่อ"; 
			}
			if($_POST["is1_career"] == ""){ 
				$response["error"] = true; 
				$response["msg"][] = "คุณยังไม่ระบุอาชีพของผู้ค้ำประกัน 1"; 
			}
			if($_POST["is1_careerOffice"] == ""){ 
				$response["error"] = true; 
				$response["msg"][] = "คุณยังไม่ระบุสถานที่ทำงาน ของผู้ค้ำประกัน 1"; 
			}
			if($_POST["is1_careerPhone"] == ""){ 
				$response["error"] = true; 
				$response["msg"][] = "คุณยังไม่ระบุเบอร์ติดต่อที่ทำงาน ของผู้ค้ำประกัน 1"; 
			}
			if($_POST["is1_income"] == ""){ 
				$response["error"] = true; 
				$response["msg"][] = "คุณยังไม่ระบุรายได้/เดือน ของผู้ค้ำประกัน 1"; 
			}else if(!is_numeric(str_replace(",","",$_POST["is1_income"]))){
				$response["error"] = true; 
				$response["msg"][] = "คุณระบุรายได้/เดือน ของผู้ค้ำประกัน 1 ไม่ถูกต้อง"; 
			}
		}
		
		// คนค้ำประกัน 2
		if($_POST["is2_cuscod"] != ""){
			if($_POST["is2_idnoStat"] == ""){ 
				$response["error"] = true; 
				$response["msg"][] = "คุณยังไม่ระบุสถานะ ผู้ค้ำประกัน 2"; 
			}
			if($_POST["is2_addr1"] == ""){ 
				$response["error"] = true; 
				$response["msg"][] = "คุณยังไม่ระบุที่อยู่ตาม ทบ.บ้าน"; 
			}
			if($_POST["is2_addr2"] == ""){ 
				$response["error"] = true; 
				$response["msg"][] = "คุณยังไม่ระบุที่อยู่ส่งเอกสาร"; 
			}
			if($_POST["is2_phoneNumber"] == ""){ 
				$response["error"] = true; 
				$response["msg"][] = "คุณยังไม่ระบุเบอร์ติดต่อ"; 
			}
			if($_POST["is2_career"] == ""){ 
				$response["error"] = true; 
				$response["msg"][] = "คุณยังไม่ระบุอาชีพของผู้ค้ำประกัน 2"; 
			}
			if($_POST["is2_careerOffice"] == ""){ 
				$response["error"] = true; 
				$response["msg"][] = "คุณยังไม่ระบุสถานที่ทำงาน ของผู้ค้ำประกัน 2"; 
			}
			if($_POST["is2_careerPhone"] == ""){ 
				$response["error"] = true; 
				$response["msg"][] = "คุณยังไม่ระบุเบอร์ติดต่อที่ทำงาน ของผู้ค้ำประกัน 2"; 
			}
			if($_POST["is2_income"] == ""){ 
				$response["error"] = true; 
				$response["msg"][] = "คุณยังไม่ระบุรายได้/เดือน ของผู้ค้ำประกัน 2"; 
			}else if(!is_numeric(str_replace(",","",$_POST["is2_income"]))){
				$response["error"] = true; 
				$response["msg"][] = "คุณระบุรายได้/เดือน ของผู้ค้ำประกัน 2 ไม่ถูกต้อง"; 
			}
		}
		
		// ผู้ยินยอม
		if($_POST["is3_cuscod"] != ""){
			if($_POST["is3_idnoStat"] == ""){ 
				$response["error"] = true; 
				$response["msg"][] = "คุณยังไม่ระบุสถานะ ของผู้ยินยอม"; 
			}
			if($_POST["is3_addr1"] == ""){ 
				$response["error"] = true; 
				$response["msg"][] = "คุณยังไม่ระบุที่อยู่ตาม ทบ.บ้าน"; 
			}
			if($_POST["is3_addr2"] == ""){ 
				$response["error"] = true; 
				$response["msg"][] = "คุณยังไม่ระบุที่อยู่ส่งเอกสาร"; 
			}
			if($_POST["is3_phoneNumber"] == ""){ 
				$response["error"] = true; 
				$response["msg"][] = "คุณยังไม่ระบุเบอร์ติดต่อ"; 
			}
			if($_POST["is3_career"] == ""){ 
				$response["error"] = true; 
				$response["msg"][] = "คุณยังไม่ระบุอาชีพของผู้ยินยอม"; 
			}
			if($_POST["is3_careerOffice"] == ""){ 
				$response["error"] = true; 
				$response["msg"][] = "คุณยังไม่ระบุสถานที่ทำงาน ของผู้ยินยอม"; 
			}
			if($_POST["is3_careerPhone"] == ""){ 
				$response["error"] = true; 
				$response["msg"][] = "คุณยังไม่ระบุเบอร์ติดต่อที่ทำงาน ของผู้ยินยอม"; 
			}
			if($_POST["is3_income"] == ""){ 
				$response["error"] = true; 
				$response["msg"][] = "คุณยังไม่ระบุรายได้/เดือน ของผู้ยินยอม"; 
			}else if(!is_numeric(str_replace(",","",$_POST["is3_income"]))){
				$response["error"] = true; 
				$response["msg"][] = "คุณระบุรายได้/เดือน ของผู้ยินยอม  ไม่ถูกต้อง"; 
			}
		}
		
		$analyze_picture = (isset($_POST["analyze_picture"])?$_POST["analyze_picture"]:'');
		if($_POST["is1_cuscod"] == "" && $_POST["is2_cuscod"] == "" && $_POST["is3_cuscod"] == "" && $analyze_picture == ""){
			$response["error"] = true; 
			$response["msg"][] = "คุณไม่ได้ระบุคนค้ำ/ผู้ยินยอมเลย ต้องแนบภาพประกอบด้วยครับ"; 
		}
		
		if($_POST["empIDNo"] == ""){ 
			$response["error"] = true; 
			$response["msg"][] = "คุณยังไม่ระบุพนักงานสาขา"; 
		}
		
		if($_POST["empTel"] == ""){ 
			$response["error"] = true; 
			$response["msg"][] = "คุณยังไม่ระบุเบอร์ติดต่อพนักงานสาขา"; 
		}
		
		if($_POST["mngIDNo"] == ""){ 
			$response["error"] = true; 
			$response["msg"][] = "คุณยังไม่ระบุผจก/ว่าที่ ผจก.สาขา"; 
		}
		
		if($_POST["mngTel"] == ""){ 
			$response["error"] = true; 
			$response["msg"][] = "คุณยังไม่ระบุเบอร์ติดต่อผจก/ว่าที่ ผจก.สาขา";
		}
		
		if($_POST["idnoAge"] < 20 && $_POST["is3_cuscod"] == ""){
			$response["error"] = true; 
			$response["msg"][] = "ผู้ซื้ออายุน้อยกว่า 20 ปี โปรดระบุผู้ยินยอมด้วยครับ";
		}
		
		
		
		if($response["error"]){ echo json_encode($response); exit; }
	}
	
	function Send_Analyze(){
		$anid = $_POST["ANID"];
		
		$sql = "
			if object_id('tempdb..#transaction') is not null drop table #transaction;
			create table #transaction (error varchar(1),id varchar(12),msg varchar(max),locat varchar(5));
			
			declare @locat varchar(5) = (select LOCAT from {$this->MAuth->getdb('ARANALYZE')} where ID='{$anid}');
			
			begin tran upd
			begin try
				if exists (
					select * from {$this->MAuth->getdb('ARANALYZE')} 
					where ID='{$anid}' and ANSTAT='I'
				)
				begin
					update {$this->MAuth->getdb('ARANALYZE')} 
					set ANSTAT='P'
					where ID='{$anid}'
				end 
				else 
				begin
					rollback tran upd;
					insert into #transaction select 'y' as error,'' as id,'ผิดพลาด ส่งคำร้องไม่สำเร็จ เนื่องจากสถานะใบวิเคราะห์สินเชื่อ เลขที่ ".$anid." ไม่ได้อยู่ในสถานะสร้างคำร้อง' as msg,@locat;
					return;
				end
				
				insert into {$this->MAuth->getdb('hp_UserOperationLog')} (userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
				values ('".$this->sess["IDNo"]."','SYS04::ส่งคำร้องขออนุมัติใบวิเคราะห์สินเชื่อ เลขที่ ".$anid."','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
				
				insert into #transaction select 'n' as error,'".$anid."' as id,'ส่งคำร้องขออนุมัติใบวิเคราะห์สินเชื่อ <br>เลขที่ใบวิเคราะห์สินเชื่อ ".$anid." แล้ว' as msg,@locat;
				commit tran upd;
			end try
			begin catch
				rollback tran upd;
				insert into #transaction select 'y' as error,'' as id,ERROR_MESSAGE() as msg,@locat;
			end catch
		";
		$this->db->query($sql);
		
		$sql 	= "select * from #transaction";   
		$query 	= $this->db->query($sql);
		
		$stat 	= true;
		$msg  	= '';
		$ARANALYZE_ID  = '';
		
		if($query->row()) {
			foreach ($query->result() as $row) {
				$stat = ($row->error == "y" ? true : false);
				$ARANALYZE_ID = $row->id;
				$msg = $row->msg;
				
				if(!$stat){
					#แจ้งเตือนไปกลุ่ม Line 
					$token = "vOaP9LwtP38FNLvh6VIA942P5qoBcDhTIAOpJSxDEu2";
					$line_msg = "รายการขออนุมัติวิเคราะห์สินเชื่อ (ใหม่)\nสาขา :: ".$row->locat."\nเลขที่ใบวิเคราะห์ :: {$anid}";
					$data = array(
						"message" => $line_msg,
						// "imageThumbnail"=>$imagePath0240,
						// "imageFullsize"=>$imagePath1024,
						// "stickerPackageId"=>2,
						// "stickerId"=>30,
					);
					$this->MMAIN->send_notify_line($token,$data);
				}
			}
		}else{
			$stat = false;
			$msg = "ผิดพลาด :: ไม่สามารถทำรายการได้ในขณะนี้ โปรดลองทำรายการใหม่ภายหลัง";
		}
		
		$response = array();
		$response['error'] = $stat;
		$response['msg'][] = $msg;
		$response['ARANALYZE_ID'] = $ARANALYZE_ID;
		
		echo json_encode($response); exit;
	}
	
	function Edit_Analyze(){
		$anid = $_POST["ANID"]; 
		
		$sql = "
			if object_id('tempdb..#transaction') is not null drop table #transaction;
			create table #transaction (error varchar(1),id varchar(12),msg varchar(max),lineNotify varchar(1000));
			
			declare @lineNotify varchar(1000);
			declare @ANID varchar(12) = '{$anid}';
			declare @locat varchar(5) = (
				select case when ANSTAT != 'I' then LOCAT else '' end 
				from {$this->MAuth->getdb('ARANALYZE')} 
				where ID=@ANID
			);
			
			begin tran upd
			begin try
				if exists (
					select * from {$this->MAuth->getdb('ARANALYZE')} 
					where ID=@ANID and ANSTAT in ('I','P')
				)
				begin
					update {$this->MAuth->getdb('ARANALYZE')} 
					set ANSTAT='I'
					where ID=@ANID
				end 
				else 
				begin
					rollback tran upd;
					insert into #transaction select 'y' as error,'' as id,'ผิดพลาด ดึงคำร้องไม่สำเร็จ เนื่องจากสถานะใบวิเคราะห์สินเชื่อ เลขที่ '+@ANID+' ไม่ได้อยู่ในสถานะสร้างคำร้อง/รออนุมัติ' as msg,'';
					return;
				end
				
				insert into {$this->MAuth->getdb('hp_UserOperationLog')} (userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
				values ('".$this->sess["IDNo"]."','SYS04::ดึงคำร้องขออนุมัติใบวิเคราะห์สินเชื่อ เลขที่ ".$anid."','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
				
				set @lineNotify = 'แก้ไขคำร้องขออนุมัติวิเคราะห์สินเชื่อ\nสาขา :: '+@locat+'\nเลขที่ใบวิเคราะห์ :: '+@ANID;
				insert into #transaction select 'n' as error,@ANID as id,'ดึงคำร้องขออนุมัติใบวิเคราะห์สินเชื่อ <br>เลขที่ใบวิเคราะห์สินเชื่อ '+@ANID+' แล้ว' as msg,@lineNotify;
				commit tran upd;
			end try
			begin catch
				rollback tran upd;
				insert into #transaction select 'y' as error,'' as id,ERROR_MESSAGE() as msg,'';
			end catch
		";
		$this->db->query($sql);
		
		$sql 	= "select * from #transaction";   
		$query 	= $this->db->query($sql);
		
		$msg = "";
		if($query->row()) {
			foreach ($query->result() as $row) {
				$stat = false;
				$msg = $row->msg;
				
				if($row->error == "y"){
					$stat = true;
					
					$response = array();
					$response['error'] = $stat;
					$response['msg']   = $msg;
					echo json_encode($response); exit;
				}
				
				if(!$stat && $row->lineNotify != ""){
					#แจ้งเตือนไปกลุ่ม Line 
					$token = "vOaP9LwtP38FNLvh6VIA942P5qoBcDhTIAOpJSxDEu2";
					$line_msg = $row->lineNotify;
					
					$data = array(
						"message" => $line_msg,
						// "imageThumbnail"=> "data:image/png;base64,%s".base64_encode(ob_get_clean()),
						// "imageFullsize"=> "data:image/png;base64,%s".base64_encode(ob_get_clean()),
						"stickerPackageId"=>2,
						"stickerId"=>43,
					);
					$this->MMAIN->send_notify_line($token,$data);
				}
			}
		}
		
		$sql = "
			declare @filePath varchar(250) = (
				select filePath from {$this->MAuth->getdb('config_fileupload')}
				where refno = '{$this->sess["db"]}' and ftpfolder like 'Senior/%/ANALYZE' and ftpstatus = 'Y'
			);
			select a.ID,a.LOCAT,a.ACTICOD
				,(select '('+aa.ACTICOD+') '+aa.ACTIDES from {$this->MAuth->getdb('SETACTI')} aa where aa.ACTICOD=a.ACTICOD collate thai_cs_as) as ACTIDES
				,a.CREATEDATE
				,a.DWN,a.INSURANCE_TYP,a.DWN_INSURANCE,a.NOPAY
				,a.RESVNO,a.RESVAMT,a.STRNO,a.MODEL,a.BAAB,a.COLOR,a.STAT
				,a.SDATE,a.YDATE
				,a.STDID,a.SUBID,a.SHCID
				,a.PRICE_ADD,a.PRICE,a.INTEREST_RT
				,b.EMP
				,isnull((select sss.titleName+sss.firstName+' '+sss.lastName from {$this->MAuth->getdb('hp_vusers_all')} sss where sss.IDNO != '' and sss.IDNo=b.EMP collate thai_cs_as),b.EMP) as EMPNAME
				,b.EMPTEL
				,b.MNG
				,isnull((select sss.titleName+sss.firstName+' '+sss.lastName from {$this->MAuth->getdb('hp_vusers_all')} sss where sss.IDNO != '' and sss.IDNo=b.MNG collate thai_cs_as),b.MNG) as MNGNAME
				,b.MNGTEL
				,c.APPRBY as APPROVE
				,c.APPRTEL as APPROVETEL
				,isnull(@filePath+b.EVIDENCE,'(none)') as EVIDENCE,c.COMMENTS as COMMENT
			from {$this->MAuth->getdb('ARANALYZE')} a
			left join {$this->MAuth->getdb('ARANALYZEDATA')} b on a.ID=b.ID
			left join {$this->MAuth->getdb('ARANALYZEAPPR')} c on a.ID=c.ID collate thai_cs_as
			where a.ID='{$anid}'
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$data = array();
		if($query->row()){
			foreach($query->result() as $row){
				foreach($row as $key => $val){
					switch($key){
						case 'CREATEDATE': 
						case 'SDATE': 
						case 'YDATE': $data[$key] = $this->Convertdate(103,$val); break;
						//case 'RESVAMT':
						case 'DWN':
						case 'DWN_INSURANCE': 
						case 'PRICE_ADD': 
						case 'PRICE': $data[$key] = str_replace(",","",number_format(($val==""?0:$val),2)); break;
						default: $data[$key] = $val; break;
					}
				}
			}
		}
		
		$sql = "
			declare @filePath varchar(250) = (
				select filePath from {$this->MAuth->getdb('config_fileupload')}
				where refno = '{$this->sess["db"]}' and ftpfolder like 'Senior/%/ANALYZE' and ftpstatus = 'Y'
			);
			select a.ID,a.CUSTYPE,a.CUSCOD
				,c.SNAM+c.NAME1+' '+c.NAME2+' ('+c.CUSCOD+')' as CUSNAME
				,c.IDNO,c.BIRTHDT,c.EXPDT
				,(select y from {$this->MAuth->getdb('FN_009_datediffYearMonthDay')}(c.BIRTHDT,GETDATE())) as CUSAGE
				,a.CUSSTAT
				,a.ADDRNO,a.ADDRDOCNO
				,(
					select '('+cast(a.ADDRNO as varchar)+') '+bb.ADDR1+' '+bb.ADDR2+' ต.'+bb.TUMB
						+' อ.'+cc.AUMPDES+' จ.'+dd.PROVDES+' '+bb.ZIP as ADDR
					from {$this->MAuth->getdb('CUSTADDR')} bb 
					left join {$this->MAuth->getdb('SETAUMP')} cc on bb.AUMPCOD=cc.AUMPCOD
					left join {$this->MAuth->getdb('SETPROV')} dd on cc.PROVCOD=dd.PROVCOD
					where bb.CUSCOD=a.CUSCOD collate thai_cs_as and isnull(bb.ADDRNO,0)=isnull(a.ADDRNO,0)
				) as ADDRNO_Detail
				,(
					select '('+cast(a.ADDRDOCNO as varchar)+') '+bb.ADDR1+' '+bb.ADDR2+' ต.'+bb.TUMB
						+' อ.'+cc.AUMPDES+' จ.'+dd.PROVDES+' '+bb.ZIP as ADDR
					from {$this->MAuth->getdb('CUSTADDR')} bb 
					left join {$this->MAuth->getdb('SETAUMP')} cc on bb.AUMPCOD=cc.AUMPCOD
					left join {$this->MAuth->getdb('SETPROV')} dd on cc.PROVCOD=dd.PROVCOD
					where bb.CUSCOD=a.CUSCOD collate thai_cs_as and isnull(bb.ADDRNO,0)=isnull(a.ADDRDOCNO,0)
				) as ADDRDOCNO_Detail
				,c.MOBILENO,a.CUSBABY
				,a.SOCAILSECURITY,a.CAREER,a.CAREERADDR,a.CAREERTEL
				,isnull(c.MREVENU,0) as MREVENU
				,a.HOSTNAME,a.HOSTIDNO,a.HOSTTEL,a.HOSTRELATION
				,a.EMPRELATION,a.REFERANT
				,isnull(@filePath+convert(varchar(30),cast(b.PICTUR as varbinary))+'?x='+cast((rand()*1000) as varchar),'(none)') as filePath
			from {$this->MAuth->getdb('ARANALYZEREF')} a
			left join {$this->MAuth->getdb('CUSTPICT')} b on a.CUSCOD=b.CUSCOD collate thai_cs_as
			left join {$this->MAuth->getdb('CUSTMAST')} c on a.CUSCOD=c.CUSCOD collate thai_cs_as
			where a.ID='{$anid}'
		";
		$query = $this->db->query($sql);
		if($query->row()){
			$i=0;
			foreach($query->result() as $row){
				foreach($row as $key => $val){
					switch($key){
						case 'EXPDT':
						case 'BIRTHDT': $data["REF".$i][$key] = $this->Convertdate(103,$val); break;
						default: $data["REF".$i][$key] = $val; break;
					}
				}
				$i++;
			}
		}
		
		$response = array();
		$response['error'] = $stat;
		$response['msg']   = $msg;
		$response['data']  = $data;
		echo json_encode($response);
	}
	
	function approved(){
		$anid 	 = $_POST["ANID"];
		$apptype = $_POST["apptype"];
		$comment = $_POST["comment"];
		
		$optmast = $_POST["optmast"];
		$dwn 	 = $_POST["dwn"];
		$nopay 	 = $_POST["nopay"];
		$inrt 	 = $_POST["inrt"];
		
		$response = array('error'=>true,'msg'=>'','ARANALYZE_ID'=>'');
		if($anid == ""){
			$response['msg'] = "ผิดพลาดคุณยังไม่ได้ระบุ รายการขออนุมัติ";
			echo json_encode($response); exit;
		}
		
		if($apptype == ""){
			$response['msg'] = "ผิดพลาดคุณยังไม่ได้ระบุ รายการอนุมัติ";
			echo json_encode($response); exit;
		}
		
		if(in_array($apptype,array('A','N')) && $comment == ""){
			$apprv = "อนุมัติ";
			if($apptype != "A"){ $apprv = "ไม่อนุมัติ"; }
		$response['msg'] = "ผิดพลาด รายการ{$apprv} คุณต้องระบุผลการวิเคราะห์ด้วยครับ";
			echo json_encode($response); exit;
		}
		//echo $inrt; exit;
		if(in_array($apptype,array('A')) && $optmast == "NOTUSE"){
			$sql = "
				select * from  {$this->MAuth->getdb('ARANALYZE')} where ID='".$anid."'
			";
			$query = $this->db->query($sql);
			
			if($query->row()){
				foreach($query->result() as $row){
					if($row->STAT == "รถใหม่"){
						$response['msg'] = "ผิดพลาด รายการอนุมัติ คุณจำเป็นต้องระบุประกันด้วยครับ";
						echo json_encode($response); exit;
					}
				}
			}
			
		}
		 //exit;
		
		$sql = "
			if object_id('tempdb..#transaction') is not null drop table #transaction;
			create table #transaction (error varchar(1),id varchar(12),msg varchar(max));

			begin tran upd
			begin try
				if exists(select * from {$this->MAuth->getdb('ARANALYZE')} where ID='".$anid."' and ANSTAT not in ('P','A','N'))
				begin
					rollback tran upd;
					insert into #transaction select 'y' as error,'".$anid."' as id,'ผิดพลาด ใบวิเคราะห์เลขที่  ".$anid."  ไม่ได้อยู่ในสถานะที่อนุมัติได้' as msg;
					return;
				end
				else if exists(select * from {$this->MAuth->getdb('ARANALYZE')} where ID='".$anid."' and isnull(CONTNO,'')!='')
				begin
					rollback tran upd;
					insert into #transaction select 'y' as error,'".$anid."' as id,'ผิดพลาด ใบวิเคราะห์เลขที่  ".$anid." ถูกนำไปคีย์ขายแล้ว' as msg;
					return;
				end
				else
				begin 
					update {$this->MAuth->getdb('ARANALYZE')} 
					set ANSTAT='".$apptype."'
					where ID='".$anid."'
				end
				
				if exists(select * from {$this->MAuth->getdb('ARANALYZEAPPR')} where ID='".$anid."')
				begin 
					update {$this->MAuth->getdb('ARANALYZEAPPR')} 
					set APPROVE		 = '".$apptype."'
						,COMMENTS	 = '".$comment."'
						,DWN		 = ".($dwn == "" ?"null":$dwn)."
						,NOPAY		 = ".($nopay == "" ?"null":$nopay)."
						,INTEREST_RT = ".($inrt == "" ?"null":$inrt)."        
						,OPTCODE	 = '".$optmast."'
						,APPRBY		 = '".$this->sess["IDNo"]."'
						,APPRDT		 = getdate()
					where ID='".$anid."'
				end
				else
				begin
					insert into {$this->MAuth->getdb('ARANALYZEAPPR')} (ID,APPROVE,COMMENTS,DWN,NOPAY,INTEREST_RT,OPTCODE,APPRBY,APPRDT)
					select '".$anid."','".$apptype."','".$comment."',".($dwn == "" ?"null":$dwn)."
						,".($nopay == "" ?"null":$nopay).",".($inrt == "" ?"null":$inrt)."
						,'".$optmast."','".$this->sess["IDNo"]."',getdate();
				end
				
				insert into {$this->MAuth->getdb('hp_UserOperationLog')} (userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
				values ('".$this->sess["IDNo"]."','SYS04::อนุมัติใบวิเคราะห์สินเชื่อ ".$anid."','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
				
				insert into #transaction select 'n' as error,'".$anid."' as id,'อนุมัติรายการวิเคราะห์สินเชื่อเสร็จแล้ว <br>เลขที่ใบวิเคราะห์สินเชื่อ ".$anid."' as msg;
				commit tran upd;
			end try
			begin catch
				rollback tran upd;
				insert into #transaction select 'y' as error,'' as id,ERROR_MESSAGE() as msg;
			end catch
		";
		$this->db->query($sql);
		
		$sql 	= "select * from #transaction";   
		$query 	= $this->db->query($sql);
		
		$stat 	= true;
		$msg  	= '';
		$ARANALYZE_ID  = '';
		
		if($query->row()) {
			foreach ($query->result() as $row) {
				$stat = ($row->error == "y" ? true : false);
				$msg = $row->msg;
				$ARANALYZE_ID = $row->id;
			}
		}else{
			$stat = false;
			$msg = "ผิดพลาด :: ไม่สามารถทำรายการได้ในขณะนี้ โปรดลองทำรายการใหม่ภายหลัง";
		}
		
		$response['error'] = $stat;
		$response['msg']  = $msg;
		$response['ARANALYZE_ID'] = $ARANALYZE_ID;
		echo json_encode($response); 
	}
	
	function formApproved(){
		$ANID = $_POST["ANID"];
		
		$sql = "
			select a.LOCAT,a.INTEREST_RT,a.NOPAY,a.DWN,a.ANSTAT 
				,b.APPROVE,b.COMMENTS,b.DWN as DWN2,b.NOPAY as NOPAY2,b.INTEREST_RT as INTEREST_RT2,b.OPTCODE
			from {$this->MAuth->getdb('ARANALYZE')} a
			left join {$this->MAuth->getdb('ARANALYZEAPPR')} b on a.ID=b.ID collate thai_cs_as
			where a.ID='{$ANID}'
		";
		//echo $sql; //exit;
		$query = $this->db->query($sql);
		
		$arrs = array();
		if($query->row()){
			foreach($query->result() as $row){
				foreach($row as $key => $val){
					$arrs[$key] = $val;
				}
			}
		}
		
		$sql = "
			select OPTCODE,OPTCODE+' '+OPTNAME as OPTNAME from {$this->MAuth->getdb('OPTMAST')}
			where LOCAT='{$arrs["LOCAT"]}' and isnull(OPTCODE,'') <> ''
			order by OPTCODE
		";
		$query = $this->db->query($sql);
		
		$arrs["optmast"] = "<option value='NOTUSE'>เลือก</option>";
		if($query->row()){
			foreach($query->result() as $row){
				$arrs["optmast"] .= "<option value='".$row->OPTCODE."' ".($arrs["OPTCODE"] == $row->OPTCODE ? "selected":"").">".$row->OPTNAME."</option>";
			}
		}
		
		$html = "
			<div class='row'><div class='col-sm-12'>
				<div class='form-group'>
					รายการอนุมัติ 
					<select id='APPTYPE' class='form-control'>
						<option value='P' ".($arrs["ANSTAT"] == "P" ? "selected":"").">รออนุมัติ</option>
						<option value='A' ".($arrs["ANSTAT"] == "A" ? "selected":"").">อนุมัติ</option>
						<option value='N' ".($arrs["ANSTAT"] == "N" ? "selected":"").">ไม่อนุมัติ</option>
					</select>
					
					ผลการวิเคราะห์ 
					<textarea id='APPCOMMENT' class='form-control' rows='6' style='color:".($arrs["ANSTAT"] == "N" ? "red":"green").";'>{$arrs["COMMENTS"]}</textarea>
					
				</div>
				<div class='form-group col-sm-6'>
					เงินดาวน์
					<input type='text' id='' class='input-xs form-control' value='{$arrs["DWN"]}' disabled>
				</div>	
				<div class='form-group col-sm-6'>
					เปลี่ยนแปลงเงินดาวน์
					<input type='text' id='APPDWN' class='input-xs form-control' value='{$arrs["DWN2"]}'>
				</div>
				<div class='form-group col-sm-6'>
					จำนวนงวด
					<input type='text' id='' class='input-xs form-control' value='{$arrs["NOPAY"]}' disabled>
				</div>	
				<div class='form-group col-sm-6'>
					เปลี่ยนแปลงจำนวนงวด
					<input type='text' id='APPNOPAY' class='input-xs form-control' value='{$arrs["NOPAY2"]}'>
				</div>
				<div class='form-group col-sm-6'>
					อัตราดอกผลเช่าซื้อ
					<input type='text' id='' class='input-xs form-control' value='{$arrs["INTEREST_RT"]}' disabled>
				</div>	
				<div class='form-group col-sm-6'>
					เปลี่ยนแปลงอัตราดอกผลเช่าซื้อ
					<input type='text' id='APPInRT' class='input-xs form-control' value='{$arrs["INTEREST_RT2"]}'>
				</div>
				<div class='form-group col-sm-12'>
					ประกัน
					<select id='APPOPTMAST' class='form-control input-sm select2'>{$arrs["optmast"]}</select>	
				</div>
			</div>
		";						
		$response = array("html" => $html);
		echo json_encode($response);
	}
	
	function getCusHistory(){
		$cuscod = $_POST["cuscod"];
		$tableid = md5($cuscod);
		
		$sql = "
			declare @cuscod varchar(12) = '".$cuscod."';
			SELECT A.CONTNO,A.CUSCOD,A.TYPESALE,A.LOCAT
				,convert(varchar(8),A.SDATE,112) as SDATE
				,A.TOTPRC,A.SMPAY,A.BALANCE,A.SMCHQ
				,A.TKANG,A.STRNO,A.RESVNO,A.TSALE,A.FL  
			FROM (
				SELECT CONTNO,CUSCOD,'ขายผ่อน      ' AS TYPESALE
					,LOCAT,SDATE,TOTPRC,SMPAY,(TOTPRC-SMPAY) AS BALANCE,SMCHQ
					,TKANG,STRNO,RESVNO,TSALE,'' AS FL 
				FROM {$this->MAuth->getdb('ARMAST')}
				WHERE DELDT IS NULL AND CUSCOD = @cuscod
				UNION 
				SELECT CONTNO,CUSCOD,'ขายผ่อน      ' AS TYPESALE,LOCAT,SDATE,TOTPRC
					,SMPAY,(TOTPRC-SMPAY) AS BALANCE,SMCHQ,TKANG,STRNO,RESVNO,TSALE,'*' AS FL 
				FROM {$this->MAuth->getdb('HARMAST')}  
				WHERE DELDT IS NULL AND CUSCOD = @cuscod
				UNION 
				SELECT CONTNO,CUSCOD,'ขายสด        ' AS TYPESALE,LOCAT,SDATE,TOTPRC,SMPAY
					,(TOTPRC-SMPAY) AS BALANCE,SMCHQ,TKANG,STRNO,RESVNO,TSALE,'' AS FL 
				FROM {$this->MAuth->getdb('ARCRED')}
				WHERE DELDT IS NULL AND CUSCOD = @cuscod
				UNION 
				SELECT CONTNO,CUSCOD,'ขายสด        ' AS TYPESALE,LOCAT,SDATE,TOTPRC,SMPAY
					,(TOTPRC-SMPAY) AS BALANCE,SMCHQ,TKANG,STRNO,RESVNO,TSALE,'*' AS FL 
				FROM {$this->MAuth->getdb('HARCRED')}  
				WHERE DELDT IS NULL and CUSCOD = @cuscod
				UNION 
				SELECT CONTNO,CUSCOD,'ขายไฟแนนซ์   ' AS TYPESALE,LOCAT,SDATE,TOTPRC,SMPAY
					,(TOTPRC-SMPAY) AS BALANCE,SMCHQ,TKANG,STRNO,RESVNO,TSALE,'' AS FL 
				FROM {$this->MAuth->getdb('ARFINC')}
				WHERE DELDT IS NULL and CUSCOD = @cuscod
				UNION 
				SELECT CONTNO,CUSCOD,'ขายไฟแนนซ์   ' AS TYPESALE,LOCAT,SDATE,TOTPRC,SMPAY
					,(TOTPRC-SMPAY) AS BALANCE,SMCHQ,TKANG,STRNO,RESVNO,TSALE,'*' AS FL 
				FROM {$this->MAuth->getdb('HARFINC')}
				WHERE DELDT IS NULL and CUSCOD = @cuscod
				UNION
				SELECT A.CONTNO,A.CUSCOD,'ขายส่งเอเยนต์' AS TYPESALE,A.LOCAT,A.SDATE,A.TOTPRC,A.SMPAY
					,(A.TOTPRC-A.SMPAY) AS BALANCE,A.SMCHQ,0 AS TKANG,B.STRNO ,'' AS RESVNO,A.TSALE,'' AS FL 
				FROM {$this->MAuth->getdb('AR_INVOI')} A,{$this->MAuth->getdb('INVTRAN')} B  
				WHERE A.DELDT IS NULL AND A.CONTNO=B.CONTNO  AND A.CUSCOD = @cuscod
				UNION 
				SELECT A.CONTNO,A.CUSCOD,'ขายส่งเอเยนต์' AS TYPESALE,A.LOCAT,A.SDATE,A.TOTPRC,A.SMPAY
					,(A.TOTPRC-A.SMPAY) AS BALANCE,A.SMCHQ,0 AS TKANG,B.STRNO,'*' AS RESVNO,A.TSALE,'' AS FL 
				FROM {$this->MAuth->getdb('HAR_INVO')} A,{$this->MAuth->getdb('HINVTRAN')} B  
				WHERE A.DELDT IS NULL AND A.CONTNO=B.CONTNO  AND A.CUSCOD = @cuscod
				UNION 
				SELECT A.ARCONT AS CONTNO,A.CUSCOD,'ลูกหนี้อื่น  ' AS TYPESALE,A.LOCAT,A.ARDATE AS SDATE,A.PAYAMT AS TOTPRC
					,A.SMPAY,(A.PAYAMT-A.SMPAY) AS BALANCE,A.SMCHQ,0 AS TKANG,'' AS STRNO,'' AS RESVNO,A.TSALE,'' AS FL 
				FROM {$this->MAuth->getdb('AROTHR')} A  
				WHERE A.CUSCOD = @cuscod
			) AS A 
			LEFT OUTER JOIN {$this->MAuth->getdb('REGTAB')} B ON A.STRNO=B.STRNO  
			WHERE (B.REGNO LIKE '%' OR (B.REGNO IS NULL) ) AND A.STRNO LIKE '%' 
			ORDER BY TYPESALE,SDATE DESC
		";
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$html .= "
					<tr style='color:".($row->FL == "*"?"red":"").";'>
						<td>".$row->CONTNO."</td>
						<td>".$row->TYPESALE."</td>
						<td>".$row->LOCAT."</td>
						<td>".$this->Convertdate(2,$row->SDATE)."</td>
						<td align='right'>".number_format($row->TOTPRC,2)."</td>
						<td align='right'>".number_format($row->SMPAY,2)."</td>
						<td align='right'>".number_format($row->BALANCE,2)."</td>						
						<td>".$row->FL."</td>
					</tr>
				";
			}
		}
		
		
		$sql = "
			declare @cuscod varchar(12) = '".$cuscod."'		
			
			select * from (
				select B.LOCAT,B.CUSCOD,E.NAME1+' '+E.NAME2 as CUSNAME,B.CONTNO,'ขายผ่อน' as TSALE,B.STRNO
					,convert(varchar(8),B.SDATE,112) as SDATE,D.RELATN AS SALCOD
					,B.TOTPRC,B.SMPAY
					,(B.TOTPRC-B.SMPAY) AS BALANCE ,D.CUSCOD AS GARCODE ,'' as OLD
				from {$this->MAuth->getdb('ARMAST')} B
				left join {$this->MAuth->getdb('ARMGAR')} D on B.CONTNO = D.CONTNO AND B.LOCAT = D.LOCAT AND B.TSALE = D.TSALE
				left join {$this->MAuth->getdb('CUSTMAST')} E on B.CUSCOD=E.CUSCOD  
				WHERE D.CUSCOD LIKE @cuscod 

				UNION 
				select B.LOCAT,B.CUSCOD,E.NAME1+' '+E.NAME2 as CUSNAME,B.CONTNO,'ขายผ่อน' as TSALE,B.STRNO
					,convert(varchar(8),B.SDATE,112) as SDATE,D.RELATN AS SALCOD
					,B.TOTPRC,B.SMPAY
					,(B.TOTPRC-B.SMPAY) AS BALANCE ,D.CUSCOD AS GARCODE ,'*' as OLD
				from {$this->MAuth->getdb('HARMAST')} B
				left join {$this->MAuth->getdb('ARMGAR')} D on B.CONTNO = D.CONTNO AND B.LOCAT = D.LOCAT AND B.TSALE = D.TSALE
				left join {$this->MAuth->getdb('CUSTMAST')} E on B.CUSCOD=E.CUSCOD  
				WHERE D.CUSCOD LIKE @cuscod 
			) as data
			order by SDATE
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html_ins = "";
		if($query->row()){
			foreach($query->result() as $row){
				$html_ins .= "
					<tr style='color:".($row->OLD == "*" ? "red":"black").";'>
						<td>".$row->CONTNO."</td>
						<td>".$row->LOCAT."</td>
						<td>".$row->CUSCOD."</td>
						<td>".$row->CUSNAME."</td>
						<td>".$row->TSALE."</td>
						<!--td>".$row->STRNO."</td -->
						<td>".$this->Convertdate(2,$row->SDATE)."</td>
						<td align='right'>".number_format($row->TOTPRC,2)."</td>
						<td align='right'>".number_format($row->SMPAY,2)."</td>
						<td align='right'>".number_format($row->BALANCE,2)."</td>
						<!-- td>".$row->SALCOD."</td -->
						<td>".$row->OLD."</td>
					</tr>
				"; 
			}
		}
		
		$html = "
			<div class='col-sm-12'>
				<table id='".$tableid."' class='table table-bordered' cellspacing='0' width='calc(100% - 1px)'>			
					<thead>
						<tr>
							<th colspan='8'>การซื้อ</th>
						</tr>
						<tr>
							<th>เลขที่สัญญา</th>
							<th>ประเภท ล/น.</th>
							<th>สาขา</th>
							<th>วันที่ทำสัญญา</th>
							<th>ราคา</th>
							<th>ชำระแล้ว</th>
							<th>ล/น. คงเหลือ</th>
							<th>*</th>
						</tr>
					</thead>
					<tbody>
						".$html."
					</tbody>
				</table>
			</div>
			<div class='col-sm-12'>
				<table id='ins_".$tableid."' class='table table-bordered' cellspacing='0' width='calc(100% - 1px)'>			
					<thead>
						<tr>
							<th colspan='8'>การค้ำประกัน</th>
						</tr>
						<tr>
							<th>เลขที่สัญญา</th>
							<th>สาขา</th>
							<th>รหัสลูกค้า</th>
							<th>ชื่อ-สกุล</th>
							<th>ประเภท ล/น.</th>
							<!-- th>เลขตัวถัง</th -->
							<th>วันที่ทำสัญญา</th>
							<th>ราคา</th>
							<th>ชำระแล้ว</th>
							<th>ล/น. คงเหลือ</th>
							<th>*</th>
						</tr>
					</thead>
					<tbody>
						".$html_ins."
					</tbody>				
				</table>
			</div>
		";
		
		$response = array("html"=>$html,"tableName"=>$tableid);
		echo json_encode($response);
	}
	
	function picture_receipt(){
		$file 		 = $_FILES["myfile"];
		//print_r($file); exit;
		$fileName    = explode(".",$_FILES["myfile"]["name"]);
		$fileName	 = ($_POST["IDNO"]==""?"ภาพประกอบ":$_POST["IDNO"]).'.'.$fileName[sizeof($fileName)-1];
		$targetFile  = $file["tmp_name"];		
		$size		 = GetimageSize($targetFile);
		$width		 = 200;
		$height	  	 = round($width*$size[1]/$size[0]);
		
		$path = $targetFile;
		$type = pathinfo($path, PATHINFO_EXTENSION);
		$data = file_get_contents($path);
		$base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
		echo json_encode(array("source"=>$base64,"name"=>$fileName));
	}
	
	function fn_checkstd(){
		$response = array("html"=>"","error"=>false,"msg"=>"");
		$data = array();
		$data["STRNO"] 	 = $_POST["STRNO"];
		$data["MODEL"] 	 = $_POST["MODEL"];
		$data["BAAB"] 	 = $_POST["BAAB"];
		$data["COLOR"] 	 = $_POST["COLOR"];
		$data["STAT"] 	 = $_POST["STAT"];
		$data["STATEN"]  = ($data["STAT"] == "รถใหม่" ? "N":($data["STAT"] == "รถเก่า" ? "O":""));
		$data["ACTICOD"] = $_POST["ACTICOD"];
		$data["dwnAmt"]  = str_replace(",","",$_POST["dwnAmt"]);
		$data["LOCAT"] 	 = $_POST["LOCAT"];
		$data["DT"] 	 = $this->Convertdate(1,$_POST["DT"]);
		
		if($data["STRNO"] == ""){ 
			$response["msg"] = "CANTSTRNO";
			echo json_encode($response); exit;
		}
		
		if($data["dwnAmt"] != "" && !is_numeric($data["dwnAmt"])){
			$response["error"] = true;
			$response["msg"] = "ผิดพลาด คุณระบุเงินดาวน์ ไม่ถูกต้อง";
			echo json_encode($response); exit;
		}
		
		$sql = "
			declare @STRNO_USE int = (
				select count(*) from {$this->MAuth->getdb('ARANALYZE')}
				where STRNO = '{$data["STRNO"]}' and ANSTAT != 'C' collate thai_ci_as
			);
			select a.STRNO,@STRNO_USE as STRNO_USE,a.MODEL,a.BAAB,a.COLOR
				,case when a.STAT='N' then 'รถใหม่'  else 'รถเก่า' end as STAT
				,a.STAT as STATEN
				,convert(varchar(8),b.SDATE,112) as SDATE
				,convert(varchar(8),b.YDATE,112) as YDATE
				,a.CRLOCAT as LOCAT
				,a.GCODE
				,datediff(day,b.SDATE,b.YDATE) as daysy
				,b.CONTNO
			from {$this->MAuth->getdb('INVTRAN')} a
			left join (
				select ROW_NUMBER() over(partition by STRNO order by STRNO,sdate desc) r,* 
				from {$this->MAuth->getdb('ARHOLD')}
			) as b on a.STRNO=b.STRNO and b.r=1
			where a.STRNO='".$data["STRNO"]."'
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		if($query->row()){
			foreach($query->result() as $row){
				foreach($row as $key => $val){
					switch($key){
						case 'SDATE': $data[$key] = $this->Convertdate(2,$val); break;
						case 'YDATE': $data[$key] = $this->Convertdate(2,$val); break;
						default:  $data[$key] = $val; break;
					}
				}
			}
		}
		
		$sql = "select * from {$this->MAuth->getdb('fn_STDVehicles')}('{$data["MODEL"]}','{$data["BAAB"]}','{$data["COLOR"]}','{$data["STATEN"]}','{$data["ACTICOD"]}','{$data["LOCAT"]}','{$data["DT"]}')";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$data["mainstdid"] = "";
		$data["mainsubid"] = "";
		if($query->row()){
			foreach($query->result() as $row){
				$data["mainstdid"] = $row->STDID;
				$data["mainsubid"] = $row->SUBID;
				
				if($row->STAT == "N"){
					$sql = "
						select STDID,SUBID,PRICE,0 as PRICE_ADD from {$this->MAuth->getdb('STDVehiclesPRICE')}
						where STDID='".$row->STDID."' and SUBID='".$row->SUBID."'
					";
				}else{
					$sql = "
						declare @CONTNO varchar(12) = '".$data["CONTNO"]."';
						declare @STAT varchar(1)  = (case when @CONTNO = '' 
							then 'O' else (select STAT from {$this->MAuth->getdb('HINVTRAN')} where CONTNO=@CONTNO) end);
						declare @price_add decimal(18,2) = (
							select price_add from {$this->MAuth->getdb('config_addpricesale')}
							where getdate() between event_st and isnull(event_ed,getdate()) 
								and '".$data["daysy"]."' between in_sday and in_eday
						);
						
						select '{$row->STDID}' as STDID
							,'{$row->SUBID}' as SUBID
							,a.ID as SHCID
						
							,b.OPRICE as PRICE
							,case when @STAT = 'N'
								then (case when @price_add is null then 0 else @price_add end) else 0 end as PRICE_ADD
						from {$this->MAuth->getdb('STDSHCAR')} a
						left join {$this->MAuth->getdb('STDSHCARDetails')} b on a.ID=b.ID
						left join {$this->MAuth->getdb('STDSHCARColors')} c on a.ID=c.ID
						left join {$this->MAuth->getdb('STDSHCARLocats')} d on a.ID=d.ID
						where b.ACTIVE='yes' collate thai_ci_as 
							and a.MODEL='".$row->MODEL."' collate thai_cs_as
							and a.BAAB='".$row->BAAB."' collate thai_cs_as 
							and (case when c.COLOR = 'ALL' then '".$row->COLOR."' else c.COLOR end) = '".$row->COLOR."' collate thai_cs_as 
							and (case when d.LOCAT = 'ALL' then '".$row->LOCAT."' else d.LOCAT end) = '".$row->LOCAT."' collate thai_cs_as
							and a.GCODE='".$data["GCODE"]."'
					";
				}
				//echo $sql; exit;
				$query = $this->db->query($sql);
				
				if($query->row()){
					foreach($query->result() as $row){
						$data["STDID"] = $row->STDID;
						$data["SUBID"] = $row->SUBID;
						$data["SHCID"] = $row->SHCID;
						$data["PRICE"] = ($row->PRICE+$row->PRICE_ADD);
						$data["PRICE_ADD"] = number_format($row->PRICE_ADD,2);
					}
				}else{
					$response["error"] = true;
					$response["msg"] = "
						ผิดพลาด ไม่พบราคาในสแตนดาร์ด <br>โปรดติดต่อฝ่ายเช่าซื้อ/ฝ่ายวิเคราะห์ เพื่อกำหนดราคาขายก่อนครับ<br><br>
						รุ่น :: ".$data["MODEL"]."<br>
						แบบ :: ".$data["BAAB"]."<br>
						สี :: ".$data["COLOR"]."<br>
						สถานะรถ :: ".$data["STAT"]."<br>
						กิจกรรมการขาย :: ".$data["ACTICOD"]."<br>
						วันที่ขออนุมัติ :: ".$this->Convertdate(2,$data["DT"])."
					";
					echo json_encode($response); exit;
				}
			}
		}else{
			$response["error"] = true;
			$response["msg"] = "
				ผิดพลาด ไม่พบราคาขายรถ <br>โปรดติดต่อฝ่ายเช่าซื้อ/ฝ่ายวิเคราะห์ เพื่อกำหนดราคาขายก่อนครับ<br><br>
				รุ่น :: ".$data["MODEL"]."<br>
				แบบ :: ".$data["BAAB"]."<br>
				สี :: ".$data["COLOR"]."<br>
				สถานะรถ :: ".$data["STAT"]."<br>
				กิจกรรมการขาย :: ".$data["ACTICOD"]."<br>
				วันที่ขออนุมัติ :: ".$this->Convertdate(2,$data["DT"])."
			";
			echo json_encode($response); exit;
		}
		
		$sql = "
			select * from {$this->MAuth->getdb('STDVehiclesDown')} a
			where STDID='".$data["mainstdid"]."' 
				and SUBID='".$data["mainsubid"]."' and '".$data["dwnAmt"]."' between DOWNS and isnull(DOWNE,'".$data["dwnAmt"]."')
				and '".$data["PRICE"]."' between PRICES and isnull(PRICEE,'".$data["PRICE"]."')
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		if($query->row()){
			foreach($query->result() as $row){
				$data["interest_rate"] 	= $row->INTERESTRT;
				$data["interest_rate2"]	= $row->INTERESTRT_GVM;
			}
		}else{
			$response["error"] = true;
			$response["msg"] = "
				ผิดพลาด ไม่พบขั้นเงินดาวน์ที่ระบุมา โปรดตรวจสอบข้อมูลใหม่อีกครั้ง<br><br>
				รุ่น :: ".$data["MODEL"]."<br>
				แบบ :: ".$data["BAAB"]."<br>
				สี :: ".$data["COLOR"]."<br>
				สถานะรถ :: ".$data["STAT"]."<br>
				กิจกรรมการขาย :: ".$data["ACTICOD"]."<br>
				วันที่ขออนุมัติ :: ".$this->Convertdate(2,$data["DT"])."
			";
			
			echo json_encode($response); exit;
		}
		
		$response["html"] = $data;
		echo json_encode($response);
	}
	
	function an_cancel(){
		$response = array("html"=>"","error"=>false,"msg"=>"");
		
		$anid 		= $_POST["anid"];
		$cancel_msg = trim($_POST["cancel_msg"]);
		
		if($anid == "Auto Genarate"){
			$response["error"] = true;
			$response["msg"]   = "ผิดพลาด ไม่สามารถยกเลิกเลขที่ใบวิเคราะห์ ".$anid." ได้";
			echo json_encode($response); exit;
		}
		
		if($cancel_msg == ""){
			$response["error"] = true;
			$response["msg"]   = "ผิดพลาด คุณยังไม่ได้ระบุสาเหตุที่ยกเลิกใบวิเคราะห์";
			echo json_encode($response); exit;
		}
		
		$sql = "
			if object_id('tempdb..#transaction') is not null drop table #transaction;
			create table #transaction (error varchar(1),id varchar(12),msg varchar(max));
			
			declare @ANID varchar(12) = '{$anid}';
			declare @MASSAGE varchar(max) = '{$cancel_msg}';
			
			begin tran transup
			begin try 
				if exists (
					select * from {$this->MAuth->getdb('ARANALYZE')}
					where ANSTAT in ('I','P') and ID=@ANID
				)
				begin 
					update {$this->MAuth->getdb('ARANALYZE')}
					set ANSTAT='C'
					where ANSTAT in ('I','P') and ID=@ANID
					
					if exists (
						select * from {$this->MAuth->getdb('ARANALYZEAPPR')}
						where ID=@ANID
					)
					begin 
						update {$this->MAuth->getdb('ARANALYZEAPPR')}
						set APPROVE='C'
							,COMMENTS=@MASSAGE
							,APPRBY='".$this->sess["IDNo"]."'
							,APPRDT=getdate()
						where ID=@ANID
					end 
					else 
					begin
						insert into {$this->MAuth->getdb('ARANALYZEAPPR')} (ID,APPROVE,COMMENTS,APPRBY,APPRDT)
						select @ANID,'C',@MASSAGE,'".$this->sess["IDNo"]."',getdate()
					end
				end
				else 
				begin
					rollback tran transup;
					insert into #transaction select 'y',@ANID as id,'ผิดพลาด ไม่สามารถยกเลิกใบวิเคราะห์ '+@ANID+' ได้โปรดตรวจสอบข้อมูลใหม่อีกครั้ง';
					return;
				end
				
				insert into {$this->MAuth->getdb('hp_UserOperationLog')} (userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
				values ('".$this->sess["IDNo"]."','SYS04::ยกเลิก ใบวิเคราะห์สินเชื่อ',@ANID+' ".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
				
				insert into #transaction select 'n' as error,@ANID as id,'แก้ไข ใบวิเคราะห์สินเชื่อแล้ว<br>เลขที่ใบวิเคราะห์สินเชื่อ '+@ANID+' ' as msg;
				commit tran transup;
			end try
			begin catch
				rollback tran transup;
				insert into #transaction select 'y',@ANID as id,ERROR_MESSAGE();
			end catch
		";
		$this->db->query($sql);
		$sql   = "select * from #transaction";
		$query = $this->db->query($sql);
		
		if($query->row()) {
			foreach ($query->result() as $row) {
				$response["error"] = ($row->error == "n" ? false:true);
				$response["msg"] = $row->msg;
			}
		}else{
			$response["error"] = true;
			$response["msg"] = "ผิดพลาด :: ไม่สามารถทำรายการได้ในขณะนี้ โปรดลองทำรายการใหม่ภายหลัง";
		}
		
		echo json_encode($response);
	}
	
	function changeANSTAT(){
		$ANID = $_POST["ANID"];
		$sql = "
			if object_id('tempdb..#transaction') is not null drop table #transaction;
			create table #transaction (error varchar(1),id varchar(12),msg varchar(max));

			begin tran upd
			begin try
				if exists (
					select * from {$this->MAuth->getdb('ARANALYZE')} 
					where ID='{$ANID}' and ANSTAT in ('P','PP')
				)
				begin
					update {$this->MAuth->getdb('ARANALYZE')} 
					set ANSTAT='P'
					where ID='{$ANID}'
				end 
				else 
				begin
					rollback tran upd;
					insert into #transaction select 'y' as error,'' as id,'ผิดพลาด ใบวิเคราะห์สินเชื่อ เลขที่ ".$ANID." ไม่ได้อยู่ในสถานะรออนุมัติ' as msg;
					return;
				end
				
				insert into {$this->MAuth->getdb('hp_UserOperationLog')} (userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
				values ('".$this->sess["IDNo"]."','SYS04::ส่งคำร้องขออนุมัติใบวิเคราะห์สินเชื่อ เลขที่ ".$ANID."','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
				
				insert into #transaction select 'n' as error,'".$ANID."' as id,'ส่งคำร้องขออนุมัติใบวิเคราะห์สินเชื่อ <br>เลขที่ใบวิเคราะห์สินเชื่อ ".$ANID." แล้ว' as msg;
				commit tran upd;
			end try
			begin catch
				rollback tran upd;
				insert into #transaction select 'y' as error,'' as id,ERROR_MESSAGE() as msg;
			end catch
		";
		$this->db->query($sql);
	}
	
}




















