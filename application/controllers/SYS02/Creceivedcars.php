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
class Creceivedcars extends MY_Controller {
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
					<div class='col-sm-2'>	
						<div class='form-group'>
							เลขที่โอน
							<input type='text' id='TRANSNO' class='form-control input-sm' placeholder='เลขที่โอน'>
						</div>
					</div>
					<div class='col-sm-2'>	
						<div class='form-group'>
							วันที่โอน
							<input type='text' id='TRANSDT' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' placeholder='วันที่โอน'>
						</div>
					</div>
					<div class='col-sm-2'>	
						<div class='form-group'>
							วันที่รับ
							<input type='text' id='MOVEDT' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' placeholder='วันที่รับ'>
						</div>
					</div>
					<div class='col-sm-2'>	
						<div class='form-group'>
							โอนให้สาขา
							<input type='text' id='TRANSTO' class='form-control input-sm' placeholder='โอนให้สาขา' value='".$this->sess['branch']."'>
						</div>
					</div>
					<div class='col-sm-2'>	
						<div class='form-group'>
							สถานะ
							<select id='TRANSSTAT' class='form-control input-sm chosen-select' data-placeholder='สถานะ'>
								<option value='' selected>ทุกสถานะ</option>
								<option value='Sendding'>อยู่ระหว่างการโอนย้ายรถ</option>
								<option value='Pendding'>รับโอนรถบางส่วน</option>
								<option value='Received'>รับโอนรถครบแล้ว</option>
								<option value='Cancel'>ยกเลิกบิลโอน</option>
							</select>
						</div>
					</div>
					<div class='col-sm-2'>	
						<div class='form-group'>
							<br>
							<button id='btnt1search' class='btn btn-sm btn-primary btn-block'><span class='glyphicon glyphicon-search'> ค้นหา</span></button>
						</div>
					</div>
					<!-- div class='col-sm-1'>	
						<div class='form-group'>
							<br>
							<input type='button' id='btnt1received' class='btn btn-cyan btn-sm' value='รับโอนรถ' style='width:100%'>
						</div>
					</div -->
				</div>
				<!-- div id='resultt1received' style='height:calc(100% - 65px);overflow:auto;background-color:white;'></div -->
				<div id='resultt1received' style='background-color:white;'></div>
			</div>
			
			<div class='tab2' style='height:calc(100vh - 132px);width:100%;overflow:auto;background-color:white;'>
				<div class='col-sm-12'>
					<div class='row'>
						<div class='col-sm-2 col-sm-offset-2'>	
							<div class='form-group'>
								เลขที่โอน
								<select id='add_TRANSNO' class='form-control input-sm'></select>
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
								สาขาต้นทาง
								<input type='text' id='add_TRANSFM' class='form-control input-sm' placeholder='สาขาต้นทาง'>
							</div>
						</div>

						<div class='col-sm-2'>	
							<div class='form-group'>
								สาขาปลายทาง
								<input type='text' id='add_TRANSTO' class='form-control input-sm' placeholder='สาขาปลายทาง'>
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
								<input type='text' id='add_APPROVED' class='form-control input-sm' placeholder='ผู้อนุมัติ'>
							</div>
						</div>
						<div class='col-sm-2'>	
							<div class='form-group'>
								สถานะบิล
								<input type='text' id='add_TRANSSTAT' class='form-control input-sm' placeholder='สถานะบิล'>
							</div>
						</div>

						<div class='col-sm-2'>	
							<div class='form-group'>
								หมายเหตุ
								<input type='text' id='add_MEMO1' class='form-control input-sm' placeholder='หมายเหตุ'>
							</div>
						</div>
					</div>
					
					<!-- div class='row'>
						<div class='col-sm-2 col-sm-offset-2'>	
							<div class='form-group'>
								วันที่รับ
								<input type='text' id='add_MOVEDT' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' placeholder='วันที่โอน'>
							</div>
						</div>
					</div -->
					
					<div class='row'>
						<div class='col-sm-2'>	
							<div class='form-group'>
								<br>
								<button id='btnt2addSTRNO' class='btn btn-sm btn-primary btn-block'><span class='glyphicon glyphicon-plus'> เพิ่มเลขตัวถัง</span></button>
							</div>
						</div>
						<div class='col-sm-2 col-sm-offset-8'>	
							<div class='form-group'>
								<br>
								<input type='button' id='btnt2detail' class='btn btn-sm btn-primary btn-sm' value='&#9776;&emsp; ดูลำดับการโอนรถ' style='width:100%'>
							</div>
						</div>
					</div>
					<div class='row'>
						<div class='col-sm-12'>	
							<div id='table-fixed-option' class='col-sm-12' style='height:calc(100vh - 390px);width:100%;overflow:auto;background-color:white;'>
								<table id='table-option' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='100%'>
									<thead>
										<tr>
											<th>#</th>
											<th>เลขตัวถัง</th>
											<th>รุ่น</th>
											<th>แบบ</th>
											<th>สี</th>
											<th>กลุ่มรถ</th>
											<th>สถานะการโอน</th>
											<th>วันที่โอนย้าย<br>วันที่รับ</th>
											<th>พขร.<br>ผู้รับโอน</th>
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
								<button id='btnt2home' class='btn btn-sm btn-inverse btn-block'><span class='glyphicon glyphicon-home'> หน้าแรก</span></button>
							</div>
						</div>
						<div class='col-sm-1 col-sm-offset-10'>	
							<div class='form-group'>
								<br>
								<button id='btnt2save' class='btn btn-sm btn-primary btn-block'><span class='glyphicon glyphicon-floppy-disk'> บันทึก</span></button>
							</div>
						</div>
					</div>
				</div>
			</div>
		";
		
		$html.= "<script src='".base_url('public/js/SYS02/Creceivedcars.js')."'></script>";
		echo $html;
	}
	
	function search(){
		$arrs = array();
		$arrs['TRANSNO'] = $_REQUEST['TRANSNO'];
		$arrs['TRANSDT'] = $_REQUEST['TRANSDT'];
		$arrs['MOVEDT'] = $_REQUEST['MOVEDT'];
		$arrs['TRANSTO'] = $_REQUEST['TRANSTO'];
		$arrs['TRANSSTAT'] = $_REQUEST['TRANSSTAT'];
		
		
		$cond = "";
		if($arrs['TRANSNO'] != ""){
			$cond .= " and a.TRANSNO like '%".$arrs['TRANSNO']."%' collate thai_cs_as";
		}
		
		if($arrs['TRANSDT'] != ""){
			$cond .= " and CONVERT(varchar(8),a.TRANSDT,112) like '%".$this->Convertdate(1,$arrs['TRANSDT'])."%'";
		}
		
		if($arrs['MOVEDT'] != ""){
			$cond .= " and CONVERT(varchar(8),b.MOVEDT,112) like '%".$this->Convertdate(1,$arrs['MOVEDT'])."%'";
		}
		
		if($arrs['TRANSTO'] != ""){
			$cond .= " and a.TRANSTO = '".$arrs['TRANSTO']."'";
		}
		
		if($arrs['TRANSSTAT'] != ""){
			$cond .= " and a.TRANSSTAT = '".$arrs['TRANSSTAT']."'";
		}
		
		$sql = "
			select ".($cond == "" ? "top 20":"")." a.TRANSNO
				,CONVERT(varchar(8),a.TRANSDT,112) as TRANSDT
				,CONVERT(varchar(8),b.MOVEDT,112) as MOVEDT
				,a.TRANSFM,a.TRANSTO,a.TRANSQTY,a.TRANSSTAT
				,case when a.TRANSSTAT='Sendding' then 'อยู่ระหว่างการโอนย้ายรถ'
					when a.TRANSSTAT='Pendding' then 'รับโอนรถบางส่วน'
					when a.TRANSSTAT='Received' then 'รับโอนรถครบแล้ว'
					when a.TRANSSTAT='Cancel' then 'ยกเลิกบิลโอน'  end TRANSSTATDesc
			from {$this->MAuth->getdb('INVTransfers')} a
			left join {$this->MAuth->getdb('INVMOVM')} b on a.TRANSNO=b.MOVENO collate thai_cs_as
			where 1=1 ".$cond."
			order by a.TRANSTO,a.TRANSNO desc
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		$NRow = 1;
		if($query->row()){
			foreach($query->result() as $row){
				$html .= "
					<tr class='trow' seq=".$NRow." ".($row->TRANSSTAT == 'Cancel' ? 'style="color:red;"' : '').">
						<td class='getit' seq=".$NRow++." TRANSNO='".$row->TRANSNO."' style='width:50px;cursor:pointer;text-align:center;'><b>เลือก</b></td>
						<td>".$row->TRANSNO."</td>
						<td>".$this->Convertdate(2,$row->TRANSDT)."</td>
						<td>".$this->Convertdate(2,$row->MOVEDT)."</td>						
						<td>".$row->TRANSFM."</td>
						<td>".$row->TRANSTO."</td>
						<td align='center'>".$row->TRANSQTY."</td>
						<td style='color:".($row->TRANSSTAT == 'Sendding' ? 'black' : ($row->TRANSSTAT == 'Pendding' ? 'blue' : ($row->TRANSSTAT == 'Cancel' ? 'red' : 'green'))).";'>".$row->TRANSSTATDesc."</td>
					</tr>
				";
			}
		}
		
		$html = "
			<div id='table-fixed-Creceivedcars' class='col-sm-12' style='height:100%;width:100%;overflow:auto;'>
				<table id='table-Creceivedcars' class='table table-bordered' cellspacing='0' width='calc(100% - 1px)'>
					<thead>
						<tr>
							<th>#</th>
							<th>เลขที่โอน</th>
							<th>วันที่โอน</th>
							<th>วันที่รับ</th>
							<th>จากสาขา</th>
							<th>ไปสาขา</th>
							<th>จำนวน(คัน)</th>
							<th>สถานะ</th>
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
	
	function getReceivedDATA(){
		$arrs = array();
		$arrs['TRANSNO'] = $_REQUEST['TRANSNO'];
		
		$sql = "
			select a.TRANSNO
				,CONVERT(varchar(8),a.TRANSDT,112) as TRANSDT
				,a.TRANSFM
				,a.TRANSTO
				,c.employeeCode+' :: '+c.USERNAME as EMPCARRY
				,a.APPROVED
				,b.employeeCode+' :: '+b.USERNAME as APPNAME
				,a.TRANSSTAT
				,case when a.TRANSSTAT='Sendding' then 'อยู่ระหว่างการโอนย้ายรถ' 
					when a.TRANSSTAT='Pendding' then 'รับโอนรถบางส่วน' 
					when a.TRANSSTAT='Received' then 'รับโอนรถครบแล้ว' 
					when a.TRANSSTAT='Cancel' then 'ยกเลิกบิลโอน' 
				 end as TRANSSTATDesc
				,a.MEMO1
				,CONVERT(varchar(8),d.MOVEDT,112) as MOVEDT
			from {$this->MAuth->getdb('INVTransfers')} a 
			left join (
				select IDNo collate Thai_CS_AS USERID
					,employeeCode collate Thai_CS_AS employeeCode
					,'คุณ'+firstName+' '+lastName collate Thai_CS_AS USERNAME  
				from {$this->MAuth->getdb('hp_vusers')}
			) b on a.APPROVED=b.USERID
			left join (
				select IDNo collate Thai_CS_AS USERID
					,employeeCode collate Thai_CS_AS employeeCode
					,'คุณ'+firstName+' '+lastName collate Thai_CS_AS USERNAME  
				from {$this->MAuth->getdb('hp_vusers')}
			) c on a.EMPCARRY=c.USERID
			
			left join {$this->MAuth->getdb('INVMOVM')} d on a.TRANSNO=d.MOVENO collate Thai_CI_AS
			where a.TRANSNO='".$arrs['TRANSNO']."'  collate thai_cs_as
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = array();
		$NRow = 1;
		if($query->row()){
			foreach($query->result() as $row){
				$html['TRANSNO'] = $row->TRANSNO;
				$html['TRANSDT'] = $this->Convertdate(2,$row->TRANSDT);
				$html['TRANSFM'] = $row->TRANSFM;
				$html['TRANSTO'] = $row->TRANSTO;
				$html['EMPCARRY'] = $row->EMPCARRY;
				$html['APPROVED'] = $row->APPROVED;
				$html['APPNAME'] = str_replace(chr(0),'',$row->APPNAME);
				$html['TRANSSTAT'] = $row->TRANSSTAT;
				$html['TRANSSTATDesc'] = $row->TRANSSTATDesc;
				$html['MEMO1'] = $row->MEMO1;
				$html['MOVEDT'] = $this->Convertdate(2,$row->MOVEDT);
			}
		}
		
		$sql = "
			select  a.TRANSITEM
				,a.TRANSNO
				,a.STRNO
				,b.TYPE
				,b.MODEL
				,b.BAAB
				,b.COLOR
				,b.CC
				,b.GCODE
				,case when a.RECEIVEBY IS NULL then 'อยู่ระหว่างการโอนย้ายรถ' else 'รับโอนแล้ว' end as RECEIVED
				,convert(varchar(8),a.TRANSDT,112) as TRANSDT
				,a.EMPCARRY+' :: '+c.USERNAME+'' as EMPCARRYNM	
				,convert(varchar(8),a.RECEIVEDT,112) as RECEIVEDT				
				,a.RECEIVEBY+' :: '+d.USERNAME+'' as RECEIVEBY
			from {$this->MAuth->getdb('INVTransfersDetails')} a 
			left join {$this->MAuth->getdb('INVTRAN')} b on a.STRNO=b.STRNO collate Thai_CI_AS
			left join (
				select IDNo collate Thai_CS_AS USERID
					,employeeCode collate Thai_CS_AS employeeCode
					,firstName+' '+lastName collate Thai_CS_AS USERNAME  
				from {$this->MAuth->getdb('hp_vusers')}
			) c on a.EMPCARRY=c.USERID
			left join (
				select IDNo collate Thai_CS_AS USERID
					,employeeCode collate Thai_CS_AS employeeCode
					,firstName+' '+lastName collate Thai_CS_AS USERNAME  
				from {$this->MAuth->getdb('hp_vusers')}
			) d on a.RECEIVEBY=d.USERID
			where a.TRANSNO='".$arrs['TRANSNO']."' collate thai_cs_as and a.RECEIVEDT is not null
			order by a.TRANSITEM
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$NRow = 0;
		if($query->row()){
			foreach($query->result() as $row){
				$disabled = '';
				$receipt  = '';
				$button   = '';
				if($row->RECEIVED == 'รับโอนแล้ว'){ 
					$disabled = 'disabled'; 
					//$receipt = "style='text-decoration: line-through;color:blue;'";				
					$receipt = "style='color:blue;'";				
					$button = $row->TRANSITEM;
				}else{
					$button = '<input type="button" class="delSTRNO btn btn-xs btn-danger btn-block" seq="old'.$NRow.'" value="ยกเลิก" >';
				}
				
				$html['STRNO'][$NRow][] = '
					<tr seq="old'.$NRow.'" '.$receipt.'>
						<td style="text-decoration: initial;">'.$button.'</td>
						<td>'.$row->STRNO.'</td>
						<td>'.$row->MODEL.'</td>
						<td>'.$row->BAAB.'</td>
						<td>'.$row->COLOR.'</td>
						<td>'.$row->GCODE.'</td>
						<td>'.$row->RECEIVED.'</td>
						<td>'.$this->Convertdate(2,$row->TRANSDT).'<br>'.$this->Convertdate(2,$row->RECEIVEDT).'</td>
						<td>'.$row->EMPCARRYNM.'<br>'.$row->RECEIVEBY.'</td>
					</tr>
				';	
				$NRow++;
			}
		}
		
		$response = array("html"=>$html);
		echo json_encode($response);
	}
	
	function saveReceivedCAR(){
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
				
		if($arrs['STRNO'] == ''){
			$response = array();
			$response['status'] = false;
			$response['msg'] = 'ไม่พบข้อมูลรถที่จะรับโอน โปรดทำรายการใหม่อีกครั้ง';
			echo json_encode($response); exit;
		}
		//print_r($arrs['STRNO']); exit;
		$sql = "";
		for($i=0;$i<sizeof($arrs['STRNO']);$i++){
			//$arrs['STRNO'][$i][6] = 'รับโอนรถครบแล้ว'  แสดงว่ารับโอนแล้ว ให้ข้ามไปเลย
			
			if($arrs['STRNO'][$i][6] == 'อยู่ระหว่างการโอนย้ายรถ'){
				$sql .= "
					if (1 = (select count(*) from {$this->MAuth->getdb('INVTRAN')} where STRNO='".$arrs['STRNO'][$i][1]."' and CRLOCAT='TRANS'))
					begin
						set @getdt = getdate();
						update a
						set a.CRLOCAT=b.TRANSTO
							,a.MOVENO=b.TRANSNO 
							,a.MOVEDT=@getdt
						from {$this->MAuth->getdb('INVTRAN')} a
						left join (
							select b.STRNO,a.TRANSTO,a.TRANSNO from {$this->MAuth->getdb('INVTransfers')} a 
							left join {$this->MAuth->getdb('INVTransfersDetails')} b on a.TRANSNO=b.TRANSNO  collate thai_cs_as
							where a.TRANSNO='".$arrs['TRANSNO']."' collate thai_cs_as and b.STRNO='".$arrs['STRNO'][$i][1]."'	
						) b on a.STRNO=b.STRNO collate Thai_CI_AS
						where b.STRNO is not null
						
						insert into {$this->MAuth->getdb('INVMOVT')}
						select a.TRANSNO,b.STRNO,convert(varchar(8),@getdt,112),a.TRANSFM,a.TRANSTO
							,isnull((select max(MOVSEQ)+1 from {$this->MAuth->getdb('INVMOVT')} where STRNO='".$arrs['STRNO'][$i][1]."'),1),@getdt
						from {$this->MAuth->getdb('INVTransfers')} a
						left join {$this->MAuth->getdb('INVTransfersDetails')} b on a.TRANSNO=b.TRANSNO  collate thai_cs_as
						where a.TRANSNO='".$arrs['TRANSNO']."' collate thai_cs_as and b.STRNO='".$arrs['STRNO'][$i][1]."'
						
						update {$this->MAuth->getdb('INVTransfersDetails')}
						set MOVENO=TRANSNO
							,RECEIVEBY='".$this->sess["IDNo"]."'
							,RECEIVEDT=@getdt
						where TRANSNO='".$arrs['TRANSNO']."' collate thai_cs_as and STRNO='".$arrs['STRNO'][$i][1]."'
					end
					else if (1 = (select count(*) from {$this->MAuth->getdb('INVTRAN')} where STRNO='".$arrs['STRNO'][$i][1]."'))
					begin 
						rollback tran ins;
						insert into #transaction select 'n' as id,'ผิดพลาด เลขตัวถัง ".$arrs['STRNO'][$i][1]." เป็นรถในสต๊อคสาขาอยู่แล้ว' as msg;
						return;
					end
					else 
					begin 
						rollback tran ins;
						insert into #transaction select 'n' as id,'ผิดพลาด ไม่พบเลขตัวถัง ".$arrs['STRNO'][$i][1]." ในสต๊อครถ' as msg;
						return;
					end
				";				
			}			
		}
		
		if($sql == ""){
			$response = array();
			$response['status'] = false;
			$response['msg'] = 'ไม่บันทึก เนื่องจากรถในรายการถูกรับโอนทุกคันแล้วครับ';
			echo json_encode($response); exit;
		}
		//print_R($this->sess); exit;
		$sql = "
			if object_id('tempdb..#transaction') is not null drop table #transaction;
			create table #transaction (id varchar(20),msg varchar(max));
			
			declare @getdt datetime = getdate();
			
			begin tran ins
			begin try
				if((select count(*) from {$this->MAuth->getdb('INVMOVM')} where MOVENO='".$arrs['TRANSNO']."' collate thai_cs_as ) > 0)
				begin 
					".$sql."
				end
				else 
				begin 
					insert into {$this->MAuth->getdb('INVMOVM')}
					select a.TRANSNO,convert(varchar(8),getdate(),112)
						,(select USERID from YTKManagement.dbo.hp_mapusers where IDNo=a.INSERTBY and dblocat='".$this->sess["db"]."')
						,(select USERID from YTKManagement.dbo.hp_mapusers where IDNo=a.APPROVED and dblocat='".$this->sess["db"]."')
						,a.TRANSFM,a.TRANSTO,a.MEMO1,getdate() 
					from {$this->MAuth->getdb('INVTransfers')} a
					where a.TRANSNO='".$arrs['TRANSNO']."' collate thai_cs_as
					
					".$sql."
				end 
				
				declare @revcount int = (
					select sum(case when RECEIVEDT is null then 1 else 0 end)
					from {$this->MAuth->getdb('INVTransfersDetails')}
					where TRANSNO='".$arrs['TRANSNO']."' collate thai_cs_as
				)
				
				update {$this->MAuth->getdb('INVTransfers')}
				set TRANSSTAT = (case when TRANSQTY > @revcount then 
					(case when @revcount = 0 then 'Received' else 'Pendding' end) 
					else 'Sendding' end)
				where TRANSNO='".$arrs['TRANSNO']."' collate thai_cs_as
				
				insert into {$this->MAuth->getdb('hp_UserOperationLog')} (userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
				values ('".$this->sess["IDNo"]."','SYS02::บันทึก รับโอนรถ','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
					
				insert into #transaction select 'y' as id,'บันทึกการโอนรถแล้ว เลขที่บิลโอน ".$arrs['TRANSNO']."' as msg;
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
		
		$response = array();
		if($query->row()){
			foreach($query->result() as $row){
				$response['status'] = ($row->id == 'y' ? true : false);
				$response['msg'] = $row->msg;
			}
		}
		
		echo json_encode($response);
	}
	
	function addSTRNO(){
		$arrs = array();
		$arrs['TRANSNO'] = $_POST['TRANSNO'];
		$arrs['STRNO']   = (!isset($_POST['STRNO']) ? array():$_POST['STRNO']);
		
		$s = sizeof($arrs['STRNO']); 		
		$strno = "''";
		for($i=0;$i<$s;$i++){
			if($strno != ""){ $strno .= ","; }
			$strno .= "'".$arrs['STRNO'][$i]."'";
		}
		//echo $strno; exit;	
		
		$sql = "
			select a.STRNO,b.TYPE,b.MODEL,b.BAAB,b.COLOR,b.CC,b.GCODE
				,isnull(a.EMPCARRY,'') EMPCARRY
				,a.EMPCARRY+' :: '+c.USERNAME+'' as EMPCARRYNM	
				,isnull(convert(varchar(8),a.TRANSDT,112),'') TRANSDT
				,case when a.STRNO in (".$strno.") then 'y' else 'n' end as active
			from {$this->MAuth->getdb('INVTransfersDetails')} a 
			left join {$this->MAuth->getdb('INVTRAN')} b on a.STRNO=b.STRNO collate Thai_CI_AS
			left join (
				select IDNo collate Thai_CS_AS USERID
					,employeeCode collate Thai_CS_AS employeeCode
					,firstName+' '+lastName collate Thai_CS_AS USERNAME  
				from {$this->MAuth->getdb('hp_vusers')}
			) c on a.EMPCARRY=c.USERID
			where a.TRANSNO='".$arrs['TRANSNO']."' collate thai_cs_as and a.RECEIVEDT is null
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		$NRow = 1;
		if($query->row()){
			foreach($query->result() as $row){
				$td = "<td class='getit' seq=".$NRow." 
							STRNO='".$row->STRNO."' 
							MODEL='".$row->MODEL."' 
							BAAB='".$row->BAAB."' 
							COLOR='".$row->COLOR."'
							GCODE='".$row->GCODE."'
							TRANSDT='".$this->convertdate(2,$row->TRANSDT)."'
							EMPCARRYNM='".$row->EMPCARRYNM."'
							style='width:50px;cursor:pointer;text-align:center;'><b>เลือก</b></td>
				";
				if($row->EMPCARRY == "" or $row->TRANSDT == "" or $row->active == "y"){ $td = "<td></td>"; }
				
				$html .= "
					<tr class='trow' seq=".$NRow." style='".($row->active == "y" ? "color:blue;":"")."'>
						".$td."
						<td>".$row->STRNO."</td>
						<td>".$row->MODEL."</td>
						<td>".$row->BAAB."</td>
						<td>".$row->COLOR."</td>
						<td>".$row->GCODE."</td>
						<td>".$this->Convertdate(2,$row->TRANSDT)."</td>
						<td>".$row->EMPCARRY."</td>
					</tr>
				";
				
				$NRow++;
			}
		}
		
		$html = "
			
			<div id='table-fixed-addSTRNO' class='col-sm-12' style='height:100%;width:100%;overflow:auto;'>
				<span class='text-danger'>*** กรณีที่สาขาต้นทาง ยังไม่ได้ระบุวันที่โยกย้าย หรือพขร.  <b>สาขาปลายทางจะไม่สามารถรับรถนั้นได้</b></span>
				<table id='table-addSTRNO' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='100%'>
					<thead>
						<tr>
							<th>#</th>
							<th>เลขตัวถัง</th>
							<th>รุ่น</th>
							<th>แบบ</th>
							<th>สี</th>
							<th>กลุ่มรถ</th>
							<th>วันที่โอนย้าย</th>
							<th>พขร.</th>
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
}




















