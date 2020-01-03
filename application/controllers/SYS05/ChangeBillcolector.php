<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//BEE+
class ChangeBillcolector extends MY_Controller {
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
	
	//หน้าแรก
	function index(){
		$claim = $this->MLogin->getclaim(uri_string());
		if($claim['m_access'] != "T"){ echo "<div align='center' style='color:red;font-size:16pt;width:100%;'>ขออภัย คุณยังไม่มีสิทธิเข้าใช้งานหน้านี้ครับ</div>"; exit; }
		
		$html = "
			<div class='b_tab1' name='home' locat='{$this->sess['branch']}' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}' today='".$this->today('today')."' usergroup='{$claim['groupCode']}' style='height:calc(100vh - 132px);overflow:auto;background-color:white;'>
				<div class='col-sm-12 col-xs-12' style='overflow:auto;'>					
					<div class='row'>
						<div class='col-sm-2 col-xs-2'>	
							<div class='form-group'>
								<br>
								สาขาที่เปลี่ยน
								<select id='LOCAT1' class='form-control input-sm' data-placeholder='สาขา'></select>
							</div>
						</div>
						<div class='col-sm-2 col-xs-2'>	
							<div class='form-group'>
								เลขที่เอกสารการเปลี่ยน<br>
								พนักงานเก็บเงิน
								<input type='text' id='CHGNO1' class='form-control input-sm' placeholder='เลขที่เอกสาร'>
							</div>
						</div>
						<div class='col-sm-2 col-xs-2'>	
							<div class='form-group'>
								จากวันที่เปลี่ยน<br>
								พนักงานเก็บเงิน
								<input type='text' id='FROMDATECHG' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' placeholder='จากวันที่'>
							</div>
						</div>
						<div class='col-sm-2 col-xs-2'>	
							<div class='form-group'>
								ถึงวันที่เปลี่ยน<br>
								พนักงานเก็บเงิน
								<input type='text' id='TODATECHG' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' placeholder='ถึงวันที่'>
							</div>
						</div>
						<div class='col-sm-2 col-xs-2'>	
							<div class='form-group'>
								<br><br>
								<button id='btnt1search' class='btn btn-primary btn-sm' style='width:100%'><span class='glyphicon glyphicon-search'> สอบถาม</span></button>
							</div>
						</div>
						<div class='col-sm-2 col-xs-2'>	
							<div class='form-group'>
								<br><br>
								<button id='bth1add' class='btn btn-cyan btn-sm'  style='width:100%'><span class='glyphicon glyphicon-pencil'> เพิ่มข้อมูล</span></button>
							</div>
						</div>
					</div>
					<div id='resultt_ChangeBillcolector' style='background-color:white;'></div>
				</div>
			</div>
		";
		
		$html.= "<script src='".base_url('public/js/SYS05/ChangeBillcolector.js')."'></script>";
		echo $html;
	}
	
	//ฟอร์มบันทึกลูกหนี้อื่น
	function getfromChangeBillcolector(){
		$level	= $_REQUEST["level"];
		//$locat = $this->sess['branch'];

		$html = "
			<div class='b_ChangeContstat' style='width:100%;height:calc(100vh - 85px);overflow:auto;background-color:#eef4fc;'>
				<div style='float:left;height:100%;overflow:auto;' class='col-sm-12 col-xs-12'>
					<div class='col-sm-12 col-xs-12 >
						<div class='row' >
							<div class='col-sm-3 col-xs-3'><br>
								<div class='form-group col-sm-10 col-xs-10'>
									<div class='form-group'><br><br>
										<b>รหัสสาขาที่เปลี่ยน</b>
										<select id='LOCAT' class='form-control input-sm' data-placeholder='สาขา'><option value='".$this->sess['branch']."'>".$this->sess['branch']."</option></select>
										<br><br>
										<b>วันที่เปลี่ยน</b>
										<input type='text' id='DATECHG' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' style='font-size:10.5pt' value='".$this->today('today')."'>
										<br>
										<b>เลขที่เอกสาร</b>
										<input type='text' id='CHGNO' class='form-control input-sm' style='font-size:10.5pt' value='Auto Genarate' disabled>
									</div><br><br><br>
								</div>
							</div>
							<div class='col-sm-9 col-xs-9'>
								<b>เลือกสัญญาลูกหนี้จากเงื่อนไข</b>
								<div class='col-sm-12 col-xs-12' style='border:0.1px dotted #d6d6d6;'>
									<div class='row'>
										<div class='col-sm-4 col-xs-4'><br>
											เปลี่ยนตามพนักงานเก็บเงิน/สัญญา
											<div class='form-group col-sm-10 col-xs-10' style='border:0.1px solid #d6d6d6;'>
												<div class='form-group'>
													<br>
													เปลี่ยนจากพนักงานเก็บเงิน
													<select id='OLD_BILLC' class='form-control input-sm' data-placeholder='พนักงานเก็บเงินเดิม'></select>
													<br><br>
													เลขที่สัญญา (กรณีระบุสัญญา)
													<select id='CONTNO' class='form-control input-sm' data-placeholder='เลขที่สัญญา'></select>
												</div>
											</div>
										</div>
										<div class='col-sm-8 col-xs-8'><br>
											เปลี่ยนตามเขตพื้นที่
											<div class='form-group col-sm-12 col-xs-12' style='border:0.1px solid #ebebeb;'>
												<div class='col-sm-4 col-xs-4'>	
													<div class='form-group'>
														<br>
														หมู่บ้าน
														<input type='text' id='VILLAGE' class='form-control input-sm' style='font-size:10.5pt'>
														<br>
														จังหวัด
														<select id='PROVINCE' class='form-control input-sm AUMP' data-placeholder='จังหวัด'></select>
													</div>
												</div>
												<div class='col-sm-4 col-xs-4'>	
													<div class='form-group'>
														<br>
														ตำบล
														<input type='text' id='TAMBON' class='form-control input-sm' style='font-size:10.5pt'>
														<br>
														เฉพาะสาขา
														<select id='LOCAT2' class='form-control input-sm' data-placeholder='สาขา'></select>
													</div>
												</div>
												<div class='col-sm-4 col-xs-4'>	
													<div class='form-group'>
														<br>
														อำเภอ
														<select id='AMPHUR' class='form-control input-sm AUMP' data-placeholder='อำเภอ'></select>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class='row'>
										<div class='col-sm-12 col-xs-12'>
											ต้องการระบุขาดงวดตั้งแต่
											<div class='col-sm-12 col-xs-12' style='border:0.1px solid #ebebeb;'>
												<div class='form-group'>
													<br>
													<input type= 'radio' id='EXP_1' name='revenue' style='width:30px;'>ขาดตั้งแต่งวดที่ 1
													<input type= 'radio' id='EXP_X' name='revenue' style='width:30px;' checked>ขาดงวดใดๆ ภายใน X งวด  &nbsp;&nbsp;&nbsp;&nbsp;ค้างชำระตั้งแต่
													<input type='text' id='EXP_FRM' class='text-danger' style='width:25px;text-align:center;border:0.1px solid #bfbfbf;border-radius:2px;' value='0'> งวด &nbsp;&nbsp;&nbsp;ถึง
													<input type='text' id='EXP_TO' class='text-danger' style='width:25px;text-align:center;border:0.1px solid #bfbfbf;border-radius:2px;' value='60'> งวด
												</div>
											</div>
										</div>
									</div><br>
								</div>	
							</div>
						</div>
						<div class='row'>
							<div class=' col-sm-2 col-xs-2 col-sm-offset-10'>	
								<div class='form-group'>
									<button id='btnsearch' class='btn btn-sm' style='width:100%;background-color:#8aceff;border:0.1px solid #61bdff;pointer;filter:contrast(100%);'><span class='glyphicon glyphicon-search'> ค้นหารายการสัญญา</span></button>
								</div>
							</div>
						</div>
						<div class='row'>
							<div class='col-sm-3 col-xs-3'><font color='blue'><b>มอบหมายสัญญาลูกหนี้ที่เลือกให้</b></font>
								<div style='height:200px;border:0.1px solid #d6d6d6;background-color:#d8e7f8;'>
									<div class='col-sm-10 col-xs-10 col-sm-offset-1'>	
										<div class='form-group'>
											<br>
											เปลี่ยนเป็นพนักงานเก็บเงิน
											<select id='NEW_BILLC' class='form-control input-sm' data-placeholder='พนักงานเก็บเงินใหม่' ></select>
											<br>
											หมายเหตุ
											<textarea type='text' id='MEMO' rows='1' cols='20' class='form-control input-sm' style='font-size:10.5pt'></textarea>
										</div>
									</div>
								</div>
							</div>
							<div class='col-sm-9 col-xs-9'><br>
								<div id='resultt_Serch' style='height:200px;background-color:#d1d1d1;border:0.1px solid #d6d6d6;'></div>
							</div>
						</div>
						<div class='row'>
							<div class=' col-sm-2 col-xs-2 col-sm-offset-8'>	
								<div class='form-group'>
									<br>
									<input type='button' id='btnclr_changebillc' class='btn btn-default btn-sm' value='เคลียร์' style='width:100%' >
								</div>
							</div>
							<div class=' col-sm-2 col-xs-2>	
								<div class='form-group'>
									<br>
									<button id='btnsave_changebillc' class='btn btn-primary btn-block'><span class='glyphicon glyphicon-floppy-disk'> บันทึก</span></button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		";
	
		$response = array("html"=>$html);
		echo json_encode($response);
	}
	
	function search(){
		$LOCAT1	= $_REQUEST["LOCAT1"];
		$CHGNO1	= $_REQUEST["CHGNO1"];
		$FROMDATECHG = $_REQUEST["FROMDATECHG"];
		$TODATECHG = $_REQUEST["TODATECHG"];
		
		$cond = "";
		if($LOCAT1 != ""){
			$cond .= " and a.LOCAT = '".$LOCAT1."'";
		}
		
		if($CHGNO1 != ""){
			$cond .= " and a.CHGNO like '%".$CHGNO1."%' collate thai_cs_as";
		}
		
		if($FROMDATECHG != ""){
			$cond .= " and CHGDATE >= '".$this->Convertdate(1,$FROMDATECHG)."'";
		}
		
		if($TODATECHG != ""){
			$cond .= " and CHGDATE <= '".$this->Convertdate(1,$TODATECHG)."'";
		}
		
		$sql = "
			select a.CHGNO, SNAM+NAME1+' '+NAME2 as CUSTNAME, a.CONTNO, a.LOCAT, convert(nvarchar,dateadd(year,543,a.SDATE),103) as SDATE, a.EXP_PRD, 
			e.NAME as OLD_BILLCNAME, a.OLD_BILLC, f.NAME as NEW_BILLCNAME, a.NEW_BILLC, g.USERNAME, a.USERID, convert(nvarchar,dateadd(year,543,a.CHGDATE),103) as CHGDATES, 
			a.CHGLOCAT, a.MOOBAN, a.TUMBOL, c.AUMPDES, d.PROVDES
			from {$this->MAuth->getdb('CHG_BILLTR')} a 
			left join {$this->MAuth->getdb('CUSTMAST')} b on a.CUSCOD = b.CUSCOD 
			left join {$this->MAuth->getdb('SETAUMP')} c on a.AUMPCOD = c.AUMPCOD
			left join {$this->MAuth->getdb('SETPROV')} d on a.PROVCOD = d.PROVCOD
			left join {$this->MAuth->getdb('OFFICER')} e on a.OLD_BILLC = e.CODE 
			left join {$this->MAuth->getdb('OFFICER')} f on a.NEW_BILLC = f.CODE 
			left join {$this->MAuth->getdb('PASSWRD')} g on a.USERID = g.USERID
			where 1=1	".$cond."
			order by a.INPDT desc
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		$NRow = 1; $No = 1;
		if($query->row()){
			foreach($query->result() as $row){
				$bgcolor="";
				//print_r($row->DESC1);
				$html .= "
					<tr class='trow' seq=".$NRow." style='".$bgcolor."'>
						<td class='getit' seq=".$NRow++."  style='cursor:pointer;text-align:center;'
						CHGNO 	= '".$row->CHGNO."' 
						>".$No++."</td>
						<td>".$row->CHGNO."</td>
						<td>".$row->CUSTNAME."</td>
						<td>".$row->CONTNO."</td>
						<td>".$row->LOCAT."</td>
						<td>".$row->SDATE."</td>
						<td>".$row->EXP_PRD."</td>
						<td>".$row->OLD_BILLCNAME.' ('.$row->OLD_BILLC.')'."</td>
						<td>".$row->NEW_BILLCNAME.' ('.$row->NEW_BILLC.')'."</td>
						<td>".$row->USERNAME.' ('.str_replace(" ","",$row->USERID).')'."</td>
						<td>".$row->CHGDATES."</td>
						<td>".$row->CHGLOCAT."</td>
						<td>".$row->MOOBAN."</td>
						<td>".$row->TUMBOL."</td>
						<td>".$row->AUMPDES."</td>
						<td>".$row->PROVDES."</td>
					</tr>
				";	
			}
		}
		
		$html = "
			<div id='table-fixed-CHGBILLC' class='col-sm-12' style='height:100%;width:100%;overflow:auto;'>
				<table id='table-CHGBILLC' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='calc(100% - 1px)'>
					<thead>
						<tr style='height:30px;'>
							<th style='vertical-align:middle;'>#</th>
							<th style='vertical-align:middle;'>เลขที่เอกสาร</th>
							<th style='vertical-align:middle;'>ชื่อ-สุกล ลูกค้า</th>
							<th style='vertical-align:middle;'>เลขที่สัญญา</th>
							<th style='vertical-align:middle;'>สัญญาสาขา</th>
							<th style='vertical-align:middle;'>วันที่ทำสัญญา</th>
							<th style='vertical-align:middle;'>ค้าง(งวด)</th>
							<th style='vertical-align:middle;'>พนักงานเก็บเงินเดิม</th>
							<th style='vertical-align:middle;'>พนักงานเก็บเงินใหม่</th>
							<th style='vertical-align:middle;'>ผู้ทำรายการ</th>
							<th style='vertical-align:middle;'>วันที่เปลี่ยน</th>
							<th style='vertical-align:middle;'>สาขาที่เปลี่ยน</th>
							<th style='vertical-align:middle;'>หมู่บ้าน</th>
							<th style='vertical-align:middle;'>ตำบล</th>
							<th style='vertical-align:middle;'>อำเภอ</th>
							<th style='vertical-align:middle;'>จังหวัด</th>
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
	
	function SerchContnoDetail(){
		$AMPHUR		= $_REQUEST["AMPHUR"];
		$PROVINCE	= $_REQUEST["PROVINCE"];
		$LOCAT2		= $_REQUEST["LOCAT2"];
		$OLD_BILLC	= str_replace(chr(0),'',$_REQUEST["OLD_BILLC"]);
		$CONTNO		= $_REQUEST["CONTNO"];
		$VILLAGE	= $_REQUEST["VILLAGE"];
		$TAMBON		= $_REQUEST["TAMBON"];
		$EXP_FRM	= $_REQUEST["EXP_FRM"];
		$EXP_TO		= $_REQUEST["EXP_TO"];
		$EXP		= $_REQUEST["EXP"];
		
		if($EXP == '1'){
			$EXP = " and a.EXP_FRM = 1";
		}else{
			$EXP = "";
		}

		$cond = "";
		$sql = "
				select a.CONTNO, a.CUSCOD, NAME1+' '+NAME2 as CUSTNAME, convert(nvarchar,dateadd(year,543,a.SDATE),103) as SDATE, a.LOCAT, a.BILLCOLL as OLD_BILLC, '' as NEW_BILLC, EXP_FRM, EXP_PRD, 
				c.MOOBAN, c.TUMB, c.AUMPCOD, d.AUMPDES, c.PROVCOD, e.PROVDES
				from {$this->MAuth->getdb('ARMAST')} a 
				left outer join {$this->MAuth->getdb('CUSTMAST')} b on a.CUSCOD = b.CUSCOD 
				left outer join {$this->MAuth->getdb('CUSTADDR')} c on a.CUSCOD = c.CUSCOD and b.ADDRNO = c.ADDRNO
				left outer join {$this->MAuth->getdb('SETAUMP')} d on c.AUMPCOD = d.AUMPCOD
				left outer join {$this->MAuth->getdb('SETPROV')} e on c.PROVCOD = e.PROVCOD
				where	a.BILLCOLL  = '".$OLD_BILLC."' 
						and a.LOCAT like '".$LOCAT2."%' 
						and a.CONTNO like '".$CONTNO."%'
						and a.EXP_PRD between ".$EXP_FRM." and ".$EXP_TO."
						and c.TUMB like '".$TAMBON."%' 
						and c.AUMPCOD like '".$AMPHUR."%' 
						and c.PROVCOD like '".$PROVINCE."%'  
						and c.MOOBAN like '".$VILLAGE."%'   
						and a.TOTPRC > 0 and (a.TOTPRC-a.SMPAY) >0  
						".$EXP." 
				order by EXP_PRD 		
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		$NRow = 1; $No = 1;
		if($query->row()){
			foreach($query->result() as $row){
				//print_r($row->DESC1);
				$html .= "
					<tr class='trow' seq=".$NRow." style='background-color:white;border:0.1px solid #3399ff;'>
						<td class='getit' seq=".$NRow++."  style='cursor:pointer;text-align:center;'
						><input type= 'checkbox' class='ckecklist' 
							CONTNO='".$row->CONTNO."' 
							CUSCOD='".$row->CUSCOD."'
							CUSTNAME='".$row->CUSTNAME."'
							SDATE='".$row->SDATE."'
							LOCAT='".$row->LOCAT."'
							OLD_BILLC='".$row->OLD_BILLC."'
							EXP_PRD='".$row->EXP_PRD."'
							MOOBAN='".$row->MOOBAN."'
							TUMB='".$row->TUMB."'
							AUMPDES='".$row->AUMPCOD."'
							PROVDES='".$row->PROVCOD."'
							checktosave='F'
						></td>
						<td align='center'>".$No++.'.'."</td>
						<td style='color:green;'>".$row->CONTNO."</td>
						<td style='display:none;'>".$row->CUSCOD."</td>
						<td>".$row->CUSTNAME."</td>
						<td>".$row->SDATE."</td>
						<td>".$row->LOCAT."</td>
						<td>".$row->OLD_BILLC."</td>
						<td>".number_format($row->EXP_PRD)."</td>
						<td>".$row->MOOBAN."</td>
						<td>".$row->TUMB."</td>
						<td>".$row->AUMPDES."</td>
						<td>".$row->PROVDES."</td>
					</tr>
				";	
			}
		}
		
		$notserch = "<tr><td colspan='13' style='color:red;text-align:center;'>ไม่พข้อมูล</td></tr>";
		$html = "
			<div id='fixed-SerchContnoDetail' style='height:100%;width:100%;overflow:auto;'>
				<table id='SerchContnoDetail' cellspacing='0' width='100%'>
					<thead>
						<tr style='background-color:#3399ff;color:white;'>
							<th style='text-align:center;'>เลือกทั้งหมด<br><input type= 'checkbox' id='selectall'></th>
							<th style='vertical-align:top;'>&nbsp;&nbsp;&nbsp;#&nbsp;&nbsp;&nbsp;</th>
							<th style='vertical-align:top;'>เลขที่สัญญา&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
							<th style='display:none;'>รหัสลูกค้า</th>
							<th style='vertical-align:top;'>ชื่อ-สุกล ลูกค้า</th>
							<th style='vertical-align:top;'>วันที่ขาย</th>
							<th style='vertical-align:top;'>สาขา&nbsp;&nbsp;</th>
							<th style='vertical-align:top;'>ผู้รับผิดชอบ<br>สัญญาเดิม</th>
							<th style='vertical-align:top;'>ค้างชำระ<br> (งวด)</th>
							<th style='vertical-align:top;'>หมู่บ้าน</th>
							<th style='vertical-align:top;'>ตำบล</th>
							<th style='vertical-align:top;'>อำเภอ</th>
							<th style='vertical-align:top;'>จังหวัด</th>
						</tr>
					</thead>	
					<tbody>
						".($No > 1 ? $html:$notserch)."
					</tbody>
				</table>
			</div>
			<script>
				document.getElementById('fixed-SerchContnoDetail').addEventListener('scroll', function(){
					var translate = 'translate(0px,'+(this.scrollTop)+'px)';                          
					this.querySelector('thead').style.transform = translate;  
				});
			</script>
		";
		
		$response = array("html"=>$html);
		echo json_encode($response);
	}
	
	function Save_changebillc(){			
		$CHGLOCAT 	= $_REQUEST["LOCAT"];
		$AMPHUR 	= str_replace(chr(0),'',$_REQUEST["AMPHUR"]);
		$PROVINCE 	= str_replace(chr(0),'',$_REQUEST["PROVINCE"]);
		$LOCAT2 	= $_REQUEST["LOCAT2"];
		$OLD_BILLC 	= str_replace(chr(0),'',$_REQUEST["OLD_BILLC"]);
		$CONTNO 	= $_REQUEST["CONTNO"];
		$NEW_BILLC 	= str_replace(chr(0),'',$_REQUEST["NEW_BILLC"]);
		$DATECHG	= $this->Convertdate(1,$_REQUEST["DATECHG"]);
		$VILLAGE	= $_REQUEST["VILLAGE"];
		$TAMBON		= $_REQUEST["TAMBON"];
		$EXP_FRM	= $_REQUEST["EXP_FRM"];
		$MEMO		= $_REQUEST["MEMO"];
		$USERID		= $this->sess["USERID"];
		
		if($MEMO == ''){
			$MEMO = 'NULL';
		}else{
			$MEMO = "'".$MEMO."'";
		}
		
		$AdjuststockAll = $_REQUEST["AdjuststockAll"];
		$sizeArr = sizeof($AdjuststockAll);
		$data_value = ""; $BILLMS = ""; $BILLTR = ""; $checktosave = "";
		
		$cont = "
			declare @symbol varchar(10) = (select H_CGBC from {$this->MAuth->getdb('CONDPAY')});
			declare @rec varchar(10) = (select SHORTL+@symbol+'-'+right(left(convert(varchar(8),GETDATE(),112),6),4) 
			from {$this->MAuth->getdb('INVLOCAT')} where LOCATCD='".$CHGLOCAT."');
			declare @CHGNO varchar(12) = isnull((select MAX(CHGNO) 
			from {$this->MAuth->getdb('CHG_BILLMS')} where CHGNO like ''+@rec+'%' collate thai_cs_as),@rec+'0000');
			set @CHGNO = left(@CHGNO,8)+right(right(@CHGNO,4)+10001,4);
			select @CHGNO as CHGNO;
		";
		$query 	= $this->db->query($cont);
		$row1 	= $query->row();
		$CHGNO = $row1->CHGNO;
		
		$BILLMS = "
					insert into {$this->MAuth->getdb('CHG_BILLMS')} (CHGNO, CHGDATE, OLD_BILLC, NEW_BILLC, LOCAT, EXP_PRD, MEMO1, USERID, INPDT, CHGLOCAT)
					values(	'".$CHGNO."', 
							'".$DATECHG."', 
							".($OLD_BILLC == '' ? 'NULL':"'".$OLD_BILLC."'").",
							".($NEW_BILLC == '' ? 'NULL':"'".$NEW_BILLC."'").",
							".($LOCAT2 == '' ? 'NULL':"'".$LOCAT2."'").",
							".($EXP_FRM == '' ? 'NULL':"'".$EXP_FRM."'").",
							".$MEMO.",
							'".$USERID."',
							getdate(),
							".($CHGLOCAT == '' ? 'NULL':"'".$CHGLOCAT."'")."
					)
		";
		//echo $BILLMS; exit;
		
		for($j=0;$j<$sizeArr;$j++){
			$data_value = explode("<###>",$AdjuststockAll[$j]);
			//print_r($data_value); exit;
			$dt_contno 	= $data_value[0];
			$dt_cuscod 	= $data_value[1];
			$dt_cusname = $data_value[2];
			$dt_sdate 	= $data_value[3];
			$dt_locat 	= $data_value[4];
			$dt_oldbillc= str_replace(chr(0),'',$data_value[5]);
			$dt_exp 	= number_format($data_value[6]);
			$dt_mooban 	= $data_value[7];
			$dt_tambol 	= $data_value[8];
			$dt_aumphur = str_replace(chr(0),'',$data_value[9]);
			$dt_province= str_replace(chr(0),'',$data_value[10]);
			$dt_check	= $data_value[11];

			if($dt_check == 'T'){
				$BILLTR .= "
					insert into {$this->MAuth->getdb('CHG_BILLTR')} (CHGNO, CHGDATE, CONTNO, SDATE, CUSCOD, LOCAT, TUMBOL, AUMPCOD, PROVCOD, EXP_PRD, OLD_BILLC, NEW_BILLC, [CHECK], USERID, INPDT, CHGLOCAT, MOOBAN)
					values('".$CHGNO."', '".$DATECHG."', '".$dt_contno."', '".$this->Convertdate(1,$dt_sdate)."', '".$dt_cuscod."', '".$dt_locat."', '".$dt_tambol."',
					'".$dt_aumphur."', '".$dt_province."', '".$dt_exp."', '".$dt_oldbillc."', '".$NEW_BILLC."', 'Y', '".$USERID."', getdate(), '".$CHGLOCAT."', '".$dt_mooban."')	
					
					update {$this->MAuth->getdb('ARMAST')} set BILLCOLL = '".$NEW_BILLC."' where CONTNO = '".$dt_contno."' and LOCAT = '".$dt_locat."'
				";
			}
			
			$checktosave .= $dt_check;
		}//echo $checktosave; exit;

		$sql = "
			if OBJECT_ID('tempdb..#AddCHGBILLCTemp') is not null drop table #AddCHGBILLCTemp;
			create table #AddCHGBILLCTemp (id varchar(20),contno varchar(20),msg varchar(max));
			
			begin tran AddCHGBILLCTemp
			begin try
					declare @CHGNO varchar(20) = '".$CHGNO."'
					
					if '".$checktosave."' like '%T%'
					begin
						".$BILLMS."
						".$BILLTR."
						insert into #AddCHGBILLCTemp select 'S',@CHGNO,'บันทึกการเปลี่ยนแปลงพนักงานเก็บเงิน   เลขที่ '+@CHGNO+' เรียบร้อย';
					end
					else
					begin
						insert into #AddCHGBILLCTemp select 'W',@CHGNO, 'กรุณาเลือกรายการสัญญาที่ต้องการทำรายการ';
					end
					
				commit tran AddCHGBILLCTemp;
			end try
			begin catch
				rollback tran AddCHGBILLCTemp;
				insert into #AddCHGBILLCTemp select 'E','',ERROR_MESSAGE();
			end catch
		";
		//echo $sql; exit;
		
		$this->db->query($sql);
		$sql = "select * from #AddCHGBILLCTemp";
		$query = $this->db->query($sql);
	  
		if($query->row()){
			foreach($query->result() as $row){
				$response["status"] = $row->id;
				$response["contno"] = $row->contno;
				$response["msg"] = $row->msg;
			}
		}else{
			$response["status"] = false;
			$response["contno"] = '';
			$response["msg"] = 'ผิดพลาดไม่สามารถบันทึกเปลี่ยนแปลงพนักงานเก็บเงินได้ โปรดติดต่อฝ่ายไอที';
		}
		
		echo json_encode($response);
	}
}