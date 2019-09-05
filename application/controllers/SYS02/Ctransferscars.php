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
class Ctransferscars extends MY_Controller {
	private $sess = array();
	private $menu = "";

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
		$diunem = $this->generateData(array($claim["menuid"]),"encode");

		$html = "
			<div class='tab1' name='home' locat='{$this->sess['branch']}' diunem='{$diunem[0]}' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}' style='height:calc(100vh - 132px);overflow:auto;background-color:white;'>
				<div style='height:65px;overflow:auto;'>
					<div class='col-sm-2'>
						<div class='form-group'>
							เลขที่บิลโอน
							<input type='text' id='TRANSNO' class='form-control input-sm' placeholder='เลขที่โอน'>
						</div>
					</div>
					<div class='col-sm-2'>
						<div class='form-group'>
							วันที่บิลโอน
							<input type='text' id='TRANSDT' value='".$this->today("today")."' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' placeholder='วันที่โอน' >
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
					<div class='col-sm-1'>
						<div class='form-group'>
							<br>
							<button id='btnt1search' class='btn btn-sm btn-primary btn-block'><span class='glyphicon glyphicon-search'> ค้นหา</span></button>
						</div>
					</div>
					<div class='col-sm-1 col-sm-offset-2'>
						<div class='form-group'>
							<br>
							<button id='btnt1transfers' class='btn btn-sm btn-cyan btn-block'><span class='glyphicon glyphicon-pencil'> โอนย้ายรถ</span></button>
						</div>
					</div>
				</div>
				<!-- div id='resultt1transfers' style='height:calc(100% - 65px);overflow:auto;background-color:white;'></div -->
				<div id='resultt1transfers' style='background-color:white;'></div>
			</div>
			<div class='tab2' style='height:calc(100vh - 132px);width:100%;overflow:auto;background-color:white;'>
				<div class='col-sm-12'>
					<div class='row'>
						<div class='col-sm-2 col-sm-offset-2'>
							<div class='form-group'>
								เลขที่บิลโอน
								<input type='text' id='add_TRANSNO' class='form-control input-sm' placeholder='เลขที่โอน'>
							</div>
						</div>
						<div class='col-sm-2'>
							<div class='form-group'>
								วันที่บิลโอน
								<input type='text' id='add_TRANSDT' thisvalue='".$this->today("today")."' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' placeholder='วันที่โอน'  >
							</div>
						</div>
						<div class='col-sm-2'>
							<div class='form-group'>
								สาขาต้นทาง
								<select id='add_TRANSFM' class='form-control input-sm'><option value='".$this->sess['branch']."'>".$this->sess['branch']."</option></select>
							</div>
						</div>

						<div class='col-sm-2'>
							<div class='form-group'>
								สาขาปลายทาง
								<select id='add_TRANSTO' class='form-control input-sm'></select>
							</div>
						</div>
					</div>

					<div class='row'>
						<div class='col-sm-2 col-sm-offset-2'>
							<div class='form-group'>
								พขร.
								<!-- input type='text' id='add_EMPCARRY' class='form-control input-sm' placeholder='พขร.' -->
								<select id='add_EMPCARRY' class='form-control input-sm' placeholder='พขร.'></select>
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
								สถานะบิล
								<select id='add_TRANSSTAT' class='form-control input-sm chosen-select' data-placeholder='สถานะ'>
									<option value='Sendding' selected>อยู่ระหว่างการโอนย้ายรถ</option>
									<option value='Pendding'>รับโอนรถบางส่วน</option>
									<option value='Received'>รับโอนรถครบแล้ว</option>
									<option value='Cancel'>ยกเลิกบิลโอน</option>
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
						<div class='col-sm-2'>
							<div class='form-group'>
								<br>
								<button id='btnt2addSTRNo' class='btn btn-primary btn-block'><span class='glyphicon glyphicon-plus'> เพิ่มเลขตัวถัง</span></button>
							</div>
						</div>
						<div class='col-sm-2 col-sm-offset-8'>
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
											<th>รุ่น</th>
											<th>แบบ</th>
											<th>สี</th>
											<th>กลุ่มรถ</th>
											<th>สถานะการโอน</th>
											<th>วันที่โอนย้าย</th>
											<th>พขร.</th>
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
								<button id='btnt2home' class='btn btn-inverse btn-block'><span class='glyphicon glyphicon-home'> หน้าแรก</span></button>
							</div>
						</div>

						<!--div class='col-sm-1'>
							<div class='form-group'>
								<br>
								<input type='button' id='btnt2bill' class='btn btn-primary btn-sm' value='บิลโอน' style='width:100%'>
							</div>
						</div-->

						<div class='col-sm-2'>
							<br/>
							<div class='btn-group btn-group-sm dropup'>
								<button type='button' id='btnt2bill' class='btn btn-primary'>
									พิมพ์บิลโอน
								</button>
								<button type='button' id='btnt2billOption' class='btn btn-primary dropdown-toggle' data-toggle='dropdown' aria-expanded='false'>
									<i class='fa fa-cog'></i>
									<!-- span class='caret'></span -->
									<span class='sr-only'>Toggle Dropdown</span>
								</button>
								<ul class='dropdown-menu' role='menu'>
									<span id='btnt2billUnlock' class='btn btn-primary btn-sm'>ปลดล็อคบิลโอน</span>
								</ul>
							</div>
						</div>

						<div class='col-sm-1 col-sm-offset-7'>
							<div class='form-group'>
								<br>
								<input type='button' id='btnt2del' class='btn btn-danger btn-sm' value='ยกเลิกบิลโอน' style='width:100%'>
							</div>
						</div>

						<div class='col-sm-1'>
							<div class='form-group'>
								<br>
								<button id='btnt2save' class='btn btn-primary btn-block'><span class='glyphicon glyphicon-floppy-disk''> บันทึก</span></button>
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
			$cond .= " and a.TRANSNO like '%".$arrs['TRANSNO']."%'  collate thai_cs_as";
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
				,a.TRANSFM,a.TRANSTO,c.USERNAME as EMPCARRY,a.TRANSQTY,a.TRANSSTAT
				,case when a.TRANSSTAT='Sendding' then 'อยู่ระหว่างการโอนย้ายรถ'
					when a.TRANSSTAT='Pendding' then 'รับโอนรถบางส่วน'
					when a.TRANSSTAT='Received' then 'รับโอนรถครบแล้ว'
					when a.TRANSSTAT='Cancel' then 'ยกเลิกบิลโอน' end as TRANSSTATDesc
				,a.MEMO1
				,b.USERNAME
				,convert(varchar(8),a.INSERTDT,112) as INSERTDT
			from {$this->MAuth->getdb('INVTransfers')} a
			left join (
				select IDNo collate Thai_CS_AS USERID
					,employeeCode collate Thai_CS_AS employeeCode
					,titleName+firstName+' '+lastName+' ('+positionName+')' collate Thai_CS_AS USERNAME
				from {$this->MAuth->getdb('hp_vusers')}
			) b on a.INSERTBY=b.USERID
			left join (
				select IDNo collate Thai_CS_AS USERID
					,employeeCode collate Thai_CS_AS employeeCode
					,titleName+firstName+' '+lastName collate Thai_CS_AS USERNAME
				from {$this->MAuth->getdb('hp_vusers')}
			) c on a.EMPCARRY=c.USERID
			where 1=1 ".$cond."
			order by a.TRANSNO desc
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);

		$html = "";
		$NRow = 1;
		if($query->row()){
			foreach($query->result() as $row){
				$bgcolor="";
				if($row->TRANSSTAT == "Cancel"){
					$bgcolor = "color:red";
				}

				$html .= "
					<tr class='trow' seq=".$NRow." style='".$bgcolor."'>
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
						<td>".$row->USERNAME."<br/>".$this->Convertdate(2,$row->INSERTDT)."<br/><span style='color:".($row->TRANSSTAT == 'Sendding' ? 'black' : ($row->TRANSSTAT == 'Pendding' ? 'blue' : ($row->TRANSSTAT == 'Cancel' ? 'red' : 'green'))).";'>".$row->TRANSSTATDesc."</span></td>
					</tr>
				";
			}
		}

		$html = "
			<div id='table-fixed-Ctransferscars' class='col-sm-12' style='height:100%;width:100%;overflow:auto;'>
				<table id='table-Ctransferscars' class='table table-bordered' cellspacing='0' width='calc(100% - 1px)'>
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
			where a.TRANSNO='".$arrs['TRANSNO']."' collate thai_cs_as
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
			select b.TRANSITEM,rtrim(b.STRNO) as STRNO,c.TYPE,c.MODEL,c.BAAB,COLOR,CC,c.GCODE
				,case when  a.TRANSSTAT='Cancel' then 'ยกเลิกบิลโอน' when isnull(b.RECEIVEDT,'')='' then 'อยู่ระหว่างการโอนย้ายรถ' else 'รับโอนแล้ว' end as RECEIVED
				,b.EMPCARRY,d.employeeCode+' :: '+d.USERNAME as EMPCARRYNM
				,convert(varchar(8),b.TRANSDT,112) as TRANSDT
			from {$this->MAuth->getdb('INVTransfers')} a
			left join {$this->MAuth->getdb('INVTransfersDetails')} b on a.TRANSNO=b.TRANSNO  collate thai_cs_as
			left join {$this->MAuth->getdb('INVTRAN')} c on b.STRNO=c.STRNO collate Thai_CS_AS
			left join (
				select IDNo collate Thai_CS_AS USERID
					,employeeCode collate Thai_CS_AS employeeCode
					,firstName+' '+lastName collate Thai_CS_AS USERNAME
				from {$this->MAuth->getdb('hp_vusers')}
			) d on b.EMPCARRY=d.USERID
			where a.TRANSNO='".$arrs['TRANSNO']."' collate thai_cs_as
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
					<!-- div class='col-sm-2'>
						<div class='form-group'>
							ที่อยู่รถ
							<select id='fCRLOCAT' class='form-control input-sm'><option value='".$_REQUEST['locat']."'>".$_REQUEST['locat']."</option></select>
						</div>
					</div -->
					<div class='col-sm-2'>
						<div class='form-group'>
							กลุ่มรถ
							<input type='text' id='fGCODE' class='form-control input-sm' placeholder='กลุ่มรถ'>
						</div>
					</div>
					<div class='col-sm-2'>
						<div class='form-group'>
							<br>
							<button id='STRNOSearch' class='btn btn-primary btn-block'><span class='glyphicon glyphicon-search'> ค้นหา</span></button>
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
		$arrs['fGCODE'] = $_REQUEST['fGCODE'];

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

		if($arrs['fGCODE'] != ''){
			$cond .= " and GCODE like '".$arrs['fGCODE']."'";
		}

		$sql = "
			select STRNO,TYPE,MODEL,BAAB,COLOR,CC,CRLOCAT,GCODE
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
							GCODE='".$row->GCODE."'
							CRLOCAT='".$row->CRLOCAT."'	style='width:50px;cursor:pointer;text-align:center;'><b>เลือก</b></td>
						<td>".$row->STRNO."</td>
						<td>".$row->MODEL."</td>
						<td>".$row->BAAB."</td>
						<td>".$row->COLOR."</td>
						<td>".$row->GCODE."</td>
						<!-- td>".$row->CC."</td -->
						<td>".$row->CRLOCAT."</td>
					</tr>
				";
			}
		}else{
			$html .= "
				<tr>
					<td colspan='7'>ไม่พบข้อมูลตามเงื่อนไข</td>
				</tr>
			";
		}

		$html = "
			<div id='table-fixed-getSTRNo' class='col-sm-12' style='height:100%;width:100%;overflow:auto;'>
				<table id='table-getSTRNo' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='100%'>
					<thead>
						<tr>
							<th>#</th>
							<th>เลขตัวถัง</th>
							<th>รุ่น</th>
							<th>แบบ</th>
							<th>สี</th>
							<th>กลุ่มรถ</th>
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
						select TRANSNO collate Thai_CS_AS as TRANSNO from {$this->MAuth->getdb('INVTransfers')} where TRANSNO like ''+@rec+'%' collate thai_cs_as
						union select moveno collate Thai_CS_AS as moveno from {$this->MAuth->getdb('INVMOVM')} where MOVENO like ''+@rec+'%' collate thai_cs_as
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
					where TRANSNO=@TRANSNO collate thai_cs_as and STRNO='".$arrs['STRNO'][$i][1]."' and RECEIVEDT is null) > 0)
					begin
						update {$this->MAuth->getdb('INVTransfersDetails')}
						set EMPCARRY='".$arrs['STRNO'][$i][8]."',
							TRANSDT=".($this->Convertdate(1,$arrs['STRNO'][$i][7]) == "" ? "NULL" : "'".$this->Convertdate(1,$arrs['STRNO'][$i][7])."'")."
						where TRANSNO=@TRANSNO collate thai_cs_as and STRNO='".$arrs['STRNO'][$i][1]."'
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

					declare @item int = (select count(*) from {$this->MAuth->getdb('INVTransfersDetails')} where TRANSNO = @TRANSNO  collate thai_cs_as);
					declare @itemRV int = (select count(*) from {$this->MAuth->getdb('INVTransfersDetails')} where TRANSNO = @TRANSNO  collate thai_cs_as and RECEIVEDT is null);

					update {$this->MAuth->getdb('INVTransfers')}
					set EMPCARRY = '".$arrs['EMPCARRY']."'
						,MEMO1 = '".$arrs['MEMO1']."'
						,TRANSQTY = (select count(*) from {$this->MAuth->getdb('INVTransfersDetails')} where TRANSNO = @TRANSNO  collate thai_cs_as)
						,TRANSSTAT = (case when @item=@itemRV then 'Sendding' when @itemRV>0 then 'Pendding' else 'Received' end)
						,INSERTBY = '".$this->sess["IDNo"]."'
						,INSERTDT = getdate()
					where TRANSNO = @TRANSNO  collate thai_cs_as;

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
		$data[] = urlencode($_REQUEST["TRANSNO"]);
		//echo urlencode($_REQUEST["TRANSNO"]); exit;
		echo json_encode($this->generateData($data,"encode"));
	}

	function checkdt(){
		$dt = $this->Convertdate(1,$_REQUEST['dt']);

		$sql = "select case when '".$dt."' > convert(varchar(8),dateadd(day,3,getdate()),112) then 'T' else 'F' end as data";
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
					where TRANSNO='".$TRANSNO."' collate thai_cs_as and RECEIVEDT is not null
				)

				if(@rec = 0)
				begin
					update {$this->MAuth->getdb('INVTransfers')}
					set TRANSSTAT='Cancel'
					where TRANSNO='".$TRANSNO."' collate thai_cs_as

					update c
					set c.CRLOCAT=a.TRANSFM
					from {$this->MAuth->getdb('INVTransfers')} a
					left join {$this->MAuth->getdb('INVTransfersDetails')} b on a.TRANSNO=b.TRANSNO collate thai_cs_as
					left join {$this->MAuth->getdb('INVTRAN')} c on b.STRNO=c.STRNO collate thai_cs_as
					where a.TRANSNO='".$TRANSNO."' collate thai_cs_as and c.STRNO is not null
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

	function billunlock(){
		$arrs["user"]	 = $_REQUEST["user"];
		$arrs["pass"] 	 = $_REQUEST["pass"];
		$arrs["comments"]= $_REQUEST["comments"];
		$arrs["TRANSNO"] = $_REQUEST["TRANSNO"];
		$arrs["diunem"]	 = $this->generateData(array($_REQUEST["diunem"]),"decode");

		$query = $this->MLogin->vertifylogin($arrs["user"],$this->sess["db"]);
		if($query->row()){
			foreach($query->result() as $row){
				if($row->passwords == md5($arrs["pass"])){
					$sql = "
						begin tran tunlock
						begin try
							insert into {$this->MAuth->getdb('UNLOCKS')}
							select '".$arrs["diunem"][0]."','ขอปลดล็อคบิลโอน','".$this->sess["db"]."','".$arrs["TRANSNO"]."','".$arrs["comments"]."','".$this->sess["USERID"]."',getdate();

							commit tran tunlock;
						end try
						begin catch
							rollback tran tunlock;
						end catch
					";
					//echo $sql; exit;
					if($this->db->query($sql)){
						$response = array("error"=>false,"msg"=>"ปลดล็อคบิลโอนแล้ว");
					}else{
						$response = array("error"=>true,"msg"=>"ผิดพลาด ไม่สามารถปลดล็อครายการโอนนี้ได้");
					}
				}else{
					$response = array("error"=>true,"msg"=>"(1)รหัสผู้ใช้ หรือรหัสผ่านไม่ถูกต้อง โปรดลองใหม่อีกครั้ง");
				}
			}
		}else{
			$response = array("error"=>true,"msg"=>"(2)รหัสผู้ใช้ หรือรหัสผ่านไม่ถูกต้อง โปรดลองใหม่อีกครั้ง");
		}

		echo json_encode($response);
	}

	function pdf(){
		$data = array();
		$data[] = $_GET["transno"];

		$arrs = $this->generateData($data,"decode");
		$arrs[0] = urldecode($arrs[0]);

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
			left join {$this->MAuth->getdb('INVTransfersDetails')} b on a.TRANSNO=b.TRANSNO collate Thai_CS_AS
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
			where a.TRANSNO='".$arrs[0]."' collate Thai_CS_AS
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
}




















