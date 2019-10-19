<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//BEE+
class ARother extends MY_Controller {
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
			<div class='b_tab1' name='home' locat='{$this->sess['branch']}' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}' style='height:calc(100vh - 132px);overflow:auto;background-color:white;'>
				<div class='col-sm-12 col-xs-12' style='overflow:auto;'>					
					<div class='row'>
						<div class='col-sm-2 col-xs-2'>	
							<div class='form-group'>
								สาขา
								<select id='LOCATS' class='form-control input-sm' data-placeholder='สาขา'></select>
							</div>
						</div>
						<div class='col-sm-2 col-xs-2'>	
							<div class='form-group'>
								ลูกหนี้อื่น
								<input type='text' id='AROTHR' class='form-control input-sm' placeholder='ลูกหนี้อื่น' >
							</div>
						</div>
						<div class='col-sm-2 col-xs-2'>	
							<div class='form-group'>
								ชื่อลูกค้า
								<select id='CUSCOD' class='form-control input-sm' data-placeholder='ชื่อ-สกุล ผู้จอง'></select>
							</div>
						</div>
						<div class='col-sm-2 col-xs-2'>	
							<div class='form-group'>
								อ้างอิงเลขที่สัญญา
								<input type='text' id='CONTNO' class='form-control input-sm' placeholder='อ้างอิงเลขที่สัญญา' >
							</div>
						</div>
						<div class='col-sm-2 col-xs-2'>	
							<div class='form-group'>
								ประเภทการขาย
								<select id='TSALE' class='form-control input-sm' data-placeholder='ประเภทการขาย'></select>
							</div>
						</div>
						<div class='col-sm-2 col-xs-2'>	
							<div class='form-group'>
								ชำระค่า
								<select id='PAYFORS' class='form-control input-sm' data-placeholder='ชำระค่า'></select>
							</div>
						</div>
					</div>	
					<div class='row'>
						<div class='col-sm-6 col-xs-6'>	
							<div class='form-group'>
								<button id='btnt1search' class='btn btn-primary btn-sm' style='width:100%'><span class='glyphicon glyphicon-search'> สอบถาม</span></button>
							</div>
						</div>
						<div class='col-sm-6 col-xs-6'>	
							<div class='form-group'>
								<button id='bthARother' class='btn btn-cyan btn-sm'  style='width:100%'><span class='glyphicon glyphicon-pencil'> เพิ่มข้อมูล</span></button>
							</div>
						</div>
					</div>
					<div id='resultt_ARother' style='background-color:white;'></div>					
				</div>
			</div>
		";
		
		$html.= "<script src='".base_url('public/js/SYS05/ARother.js')."'></script>";
		echo $html;
	}
	
	//ฟอร์มบันทึกลูกหนี้อื่น
	function getfromAROTHER(){
		$level	= $_REQUEST["level"];
		$locat = $this->sess['branch'];
		$sql = "
			select * from {$this->MAuth->getdb('VATMAST')}
			where getdate() between FRMDATE and TODATE
		";
		$query = $this->db->query($sql);
		$row = $query->row();
		$data["vatrt"] = number_format($row->VATRT,2);

		$html = "
			<div class='b_add_arother' style='width:100%;height:calc(100vh - 132px);overflow:auto;background-color:white;'>
				<div style='float:left;height:100%;overflow:auto;' class='col-sm-6 col-sm-offset-3'>					
					<div class='row'>
						<div class='col-sm-12 col-xs-12' >	
							<div class='form-group'>
								เงื่อนไขการรับรู้
								<table style='height:33px;width:100%;border-right:1px solid #ddd;border-left:1px solid #ddd;border-top:1px solid #ddd;border-bottom:1px solid #ddd;'>
								<tr>
								<td style='width:50%;'><input type= 'radio' id='Products' name='revenue' style='width:30px;'>1. รับรู้รายได้ทันที (สินค้า)</td>
								<td style='width:50%;'><input type= 'radio' id='Services' name='revenue' style='width:30px;'>2. เมื่อรับเงิน (บริการ)</td>
								</tr>
								</table>
							</div>
						</div>
						<div class='col-sm-6 col-xs-6'>	
							<div class='form-group'>
								ลูกหนี้ที่สาขา
								<select id='LOCATS2' class='form-control input-sm' ".($level == 1 ? "":"disabled").">
									<option value='".$this->sess["branch"]."'>".$this->sess["branch"]."</option>
								</select>
							</div>
						</div>
						<div class='col-sm-6 col-xs-6'>	
							<div class='form-group'>
								วันที่ทำสัญญา
								<input type='text' id='cont_date' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' style='font-size:10.5pt' value='".$this->today('today')."'>
							</div>
						</div>
						<div class='col-sm-6 col-xs-6'>	
							<div class='form-group'>
								เลขที่สัญญาลูกหนี้อื่น
								<input type='text' id='AROTHRNO' class='form-control input-sm' style='font-size:10.5pt' value='Auto Genarate' disabled>
							</div>
						</div>
						<div class='col-sm-6 col-xs-6'>	
							<div class='form-group'>
								ประเภทการขาย
								<select id='TSALES' class='form-control input-sm' data-placeholder='ประเภทการขาย'></select>
							</div>
						</div>
						<div class='col-sm-6 col-xs-6'>	
							<div class='form-group'>
								เลขสัญญา
								<select id='CONTNOS' class='form-control input-sm BBB' data-placeholder='เลขสัญญา'></select>
							</div>
						</div>
						<div class='col-sm-6 col-xs-6'>	
							<div class='form-group'>
								รหัสลูกค้า
								<select id='CUSCODS' class='form-control input-sm BBB' data-placeholder='รหัสลูกค้า'></select>
							</div>
						</div>
						<div class='col-sm-6 col-xs-6'>	
							<div class='form-group'>
								ค้างชำระ
								<select id='PAYTYPS' class='form-control input-sm' data-placeholder='ค้างชำระ'></select>
							</div>
						</div>
						<div class='col-sm-6 col-xs-6'>	
							<div class='form-group'>
								UserId
								<input type='text' id='USERID' class='form-control input-sm' style='font-size:10.5pt' value='".$this->sess["name"]." (".$this->sess["USERID"].")' disabled>
							</div>
						</div>
						<div class='col-sm-4 col-xs-4'>	
							<div class='form-group'>
								จำนวนเงิน+Vat (บาท)
								<input type='text' id='AMOUNT' class='form-control input-sm' style='font-size:10.5pt' placeholder='0.00' >
							</div>
						</div>
						<div class='col-sm-4 col-xs-4'>	
							<div class='form-group'>
								อัตราภาษี (%)
								<input type='text' id='RATEVAT' class='form-control input-sm' style='font-size:10.5pt' placeholder='0.00' value='".$data["vatrt"]."' readonly>
							</div>
						</div>
						<div class='col-sm-4 col-xs-4'>	
							<div class='form-group'>
								ชำระแล้ว (บาท)
								<input type='text' id='PAYMENTS' class='form-control input-sm' style='font-size:10.5pt' placeholder='0.00' disabled>
							</div>
						</div>
						<div class='col-sm-4 col-xs-4 UPLOADPIC'>	
							<div class='form-group'>
								<span style='font-size:10.5pt;'>รูปที่ 1</span>
								<div class='input-group'>
									<input type='text' id='FILEPIC1' class='form-control input-sm' readonly>
									<span id='PIC1' class='input-group-addon btn-cyan'>เพิ่ม</span>
								</div>
							</div>
						</div>
						<div class='col-sm-4 col-xs-4 UPLOADPIC'>	
							<div class='form-group'>
								<span style='font-size:10.5pt;'>รูปที่ 2</span>
								<div class='input-group'>
									<input type='text' id='FILEPIC2' class='form-control input-sm' readonly>
									<span id='PIC2' class='input-group-addon btn-cyan'>เพิ่ม</span>
								</div>
							</div>
						</div>
						<div class='col-sm-4 col-xs-4 UPLOADPIC'>	
							<div class='form-group'>
								<span style='font-size:10.5pt;'>รูปที่ 3</span>
								<div class='input-group'>
									<input type='text' id='FILEPIC3' class='form-control input-sm' readonly>
									<span id='PIC3' class='input-group-addon btn-cyan'>เพิ่ม</span>
								</div>
							</div>
						</div>
						<div class=' col-sm-12 col-xs-12'>	
							<div class='form-group'>
								หมายเหตุ
								<textarea type='text' id='MEMO' rows='1' cols='20' class='form-control input-sm' style='font-size:10.5pt'></textarea>
							</div>
						</div>	
					</div>
					<div class='row'>
						<br>
						<div class=' col-sm-2 col-sm-offset-3'>	
							<div class='form-group'>
								<button id='btnsave_arother' class='btn btn-primary btn-block'><span class='glyphicon glyphicon-floppy-disk'> บันทึก</span></button>
							</div>
						</div>
						<div class=' col-sm-2'>	
							<div class='form-group'>
								<input type='button' id='btncancel_arother' class='btn btn-default btn-sm' value='เคลียร์' style='width:100%'>
							</div>
						</div>
						<div class=' col-sm-2'>	
							<div class='form-group'>
								<input type='button' id='btndelete_arother' class='btn btn-danger btn-sm' value='ลบ' style='width:100%'>
							</div>
						</div>
					</div>
				</div>
			</div>
		";
	
		$response = array("html"=>$html);
		echo json_encode($response);
	}
	
	//ฟังก์ชั่นบันทึกลูกหนี้อื่น
	function SAVE_AROTHER(){
		$INCFL 	= $_REQUEST["INCFL"];
		$LOCAT	= $_REQUEST["LOCAT"];
		$ARDATE	= $this->Convertdate(1,$_REQUEST["ARDATE"]);
		$ARCONT	= $_REQUEST["ARCONT"];
		$TSALE	= $_REQUEST["TSALE"];
		$CONTNO	= $_REQUEST["CONTNO"];
		$CUSCOD	= $_REQUEST["CUSCOD"];
		$PAYFOR	= $_REQUEST["PAYFOR"];
		$USERID	= $this->sess["USERID"];
		$PAYAMT	= str_replace(',','',$_REQUEST["PAYAMT"]);
		$VATRT	= $_REQUEST["VATRT"];
		$MEMO	= $_REQUEST["MEMO"];
		if($VATRT == ''){
			$VATRT = 0;
		}else{
			$VATRT = $VATRT;
		}
		if($MEMO == ''){
			$MEMO = 'NULL';
		}else{
			$MEMO = "'".$MEMO."'";
		}
		//echo $MEMO; exit;

		$sql = "
			if OBJECT_ID('tempdb..#AddAROTHTemp') is not null drop table #AddAROTHTemp;
			create table #AddAROTHTemp (id varchar(20),contno varchar(20),msg varchar(max));
			
			begin tran AddAROTHTemp
			begin try
			
				declare @symbol varchar(10) = (select H_AROTH from {$this->MAuth->getdb('CONDPAY')});
				declare @rec varchar(10) = (select SHORTL+@symbol+'-'+right(left(convert(varchar(8),GETDATE(),112),6),4) 
				from {$this->MAuth->getdb('INVLOCAT')} where LOCATCD='".$LOCAT."');
				declare @CONTNO varchar(12) = isnull((select MAX(ARCONT) 
				from {$this->MAuth->getdb('AROTHR')} where ARCONT like ''+@rec+'%' collate thai_cs_as),@rec+'0000');
				set @CONTNO = left(@CONTNO,8)+right(right(@CONTNO,4)+10001,4);
				
				if '".$CONTNO."' = ''
					begin
						insert into #AddAROTHTemp select 'W', '', 'กรุณาระบุสัญญา';
					end
				else
					begin
					INSERT INTO {$this->MAuth->getdb('AROTHR')} 
					(ARCONT, TSALE, CONTNO, CUSCOD, LOCAT, PAYFOR, PAYAMT, VATRT, TAXNO, ARDATE, SMPAY, SMCHQ, BALANCE, USERID, INPDT, MEMO1, INCFL)  
					VALUES 
					(@CONTNO, '".$TSALE."', '".$CONTNO."', '".$CUSCOD."', '".$LOCAT."', '".$PAYFOR."', '".$PAYAMT."', ".$VATRT.", '', convert(datetime,'".$ARDATE."'), 
					0, 0, 0, '".$USERID."', getdate(), ".$MEMO.", '".$INCFL."')
				
					insert into #AddAROTHTemp select 'S',@CONTNO,'บันทึกลูกหนี้อื่น เลขที่สัญญา '+@CONTNO+' เรียบร้อย';
					end
					
				commit tran AddAROTHTemp;
			end try
			begin catch
				rollback tran AddAROTHTemp;
				insert into #AddAROTHTemp select 'E','',ERROR_MESSAGE();
			end catch
		";
		//echo $sql; exit;
		
		$this->db->query($sql);
		$sql = "select * from #AddAROTHTemp";
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
			$response["msg"] = 'ผิดพลาดไม่สามารถบันทึกรายการลูกหนี้อื่นได้ โปรดติดต่อฝ่ายไอที';
		}
		
		echo json_encode($response);
	}
	
	function search(){
		$LOCATS	= $_REQUEST["LOCATS"];
		$AROTHR	= $_REQUEST["AROTHR"];
		$CUSCOD	= $_REQUEST["CUSCOD"];
		$CONTNO	= $_REQUEST["CONTNO"];
		$TSALE	= $_REQUEST["TSALE"];
		$PAYFOR	= $_REQUEST["PAYFORS"];
		
		$cond = "";
		if($LOCATS != ""){
			$cond .= " and a.LOCAT = '".$LOCATS."'";
		}
		
		if($AROTHR != ""){
			$cond .= " and a.ARCONT like '%".$AROTHR."%' collate thai_cs_as";
		}
		
		if($CUSCOD != ""){
			$cond .= " and a.CUSCOD = '".$CUSCOD."'";
		}
		
		if($CONTNO != ""){
			$cond .= " and a.CONTNO like '%".$CONTNO."%' collate thai_cs_as";
		}
		
		if($TSALE != ""){
			$cond .= " and a.TSALE = '".$TSALE."'";
		}
		
		if($PAYFOR != ""){
			$cond .= " and a.PAYFOR = '".$PAYFOR."'";
		}
		
		$top = "";
		if($LOCATS != '' || $TSALE != '' || $PAYFOR != ''){
			$top = "top 100";
		}else if($cond == ''){
			$top = "top 50";
		}
		$sql = "
			select ".$top." a.ARCONT, a.LOCAT, convert(nvarchar,dateadd(year,543,a.ARDATE),103) as ARDATE, a.CONTNO, b.SNAM+b.NAME1+' '+b.NAME2 as CUSNAME, 
			a.PAYFOR+' - '+c.FORDESC as PAYFORS, a.PAYFOR, c.FORDESC, a.PAYAMT, a.SMPAY, a.VATRT, a.TSALE, d.DESC1, a.INCFL, a.MEMO1,
			a.CUSCOD, ltrim(replace(replace(replace(replace(replace(e.USERNAME,'น.ส.',''),'นส.',''),'น.ส',''),'นาง',''),'นาย',''))+' ('+a.USERID+')' as USERID
			from {$this->MAuth->getdb('AROTHR')} a
			left join {$this->MAuth->getdb('CUSTMAST')} b on a.CUSCOD = b.CUSCOD
			left join {$this->MAuth->getdb('PAYFOR')} c on a.PAYFOR = c.FORCODE
			left join {$this->MAuth->getdb('TYPSALE')} d on a.TSALE = d.TSALE
			left join {$this->MAuth->getdb('PASSWRD')} e on a.USERID = e.USERID
			where 1=1 ".$cond."
			order by a.ARDATE desc, a.ARCONT desc
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		$NRow = 1;
		if($query->row()){
			foreach($query->result() as $row){
				$bgcolor="";
				//print_r($row->DESC1);
				$html .= "
					<tr class='trow' seq=".$NRow." style='".$bgcolor."'>
						<td class='getit' seq=".$NRow++."  style='cursor:pointer;text-align:center;'
						INCFL 	= '".$row->INCFL."' 
						LOCAT 	= '".$row->LOCAT."' 
						ARDATE 	= '".$row->ARDATE."' 
						ARCONT	= '".str_replace(chr(0),'',$row->ARCONT)."' 
						TSALE	= '".$row->TSALE."' 
						DESC1	= '".str_replace(chr(0),'',$row->DESC1)."'
						CONTNO	= '".str_replace(chr(0),'',$row->CONTNO)."' 
						CUSCOD 	= '".str_replace(chr(0),'',$row->CUSCOD)."'
						CUSNAME = '".str_replace(chr(0),'',$row->CUSNAME)."' 
						PAYFOR	= '".$row->PAYFOR."'
						FORDESC	= '".str_replace(chr(0),'',$row->FORDESC)."'
						USERID	= '".str_replace(chr(0),'',$row->USERID)."' 
						PAYAMT 	= '".number_format($row->PAYAMT,2)."' 
						VATRT	= '".number_format($row->VATRT,2)."' 
						SMPAY	= '".number_format($row->SMPAY,2)."' 
						MEMO1	= '".str_replace(chr(0),'',$row->MEMO1)."' 
						><b>เลือก</b></td>
						<td>".$row->ARCONT."</td>
						<td>".$row->LOCAT."</td>
						<td>".$row->ARDATE."</td>
						<td>".$row->CONTNO."</td>
						<td>".$row->CUSNAME."</td>
						<td>".$row->PAYFORS."</td>
						<td align='right'>".number_format($row->PAYAMT,2)."</td>
						<td align='right' style='color:".($row->SMPAY == '0.00' ? 'red' : 'black').";'>".number_format($row->SMPAY,2)."</td>
					</tr>
				";	
			}
		}
		
		$html = "
			<div id='table-fixed-ARother' class='col-sm-12' style='height:100%;width:100%;overflow:auto;'>
				<table id='table-ARother' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='calc(100% - 1px)'>
					<thead>
						<tr>
							<th style='vertical-align:middle;'>#</th>
							<th style='vertical-align:middle;'>เลขที่สัญญาลูกหนี้อื่น</th>
							<th style='vertical-align:middle;'>สาขา</th>
							<th style='vertical-align:middle;'>วันที่ตั้งลูกหนี้</th>
							<th style='vertical-align:middle;'>เลขที่สัญญาอ้างอิง</th>
							<th style='vertical-align:middle;'>ชื่อ-สุกล ลูกค้า</th>
							<th style='vertical-align:middle;'>ค้างชำระค่า</th>
							<th style='vertical-align:middle;'>จำนวนเงิน (บาท)</th>
							<th style='vertical-align:middle;'>ชำระแล้ว (บาท)</th>
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
	
	function Edit_AROTHER(){
		$ARCONT	= $_REQUEST["ARCONT"];
		$CONTNO	= $_REQUEST["CONTNO"];
		$CUSCOD	= $_REQUEST["CUSCOD"];
		$PAYFOR	= $_REQUEST["PAYFOR"];
		$PAYAMT	= str_replace(',','',$_REQUEST["PAYAMT"]);
		$MEMO	= $_REQUEST["MEMO"];
		
		$sql = "
			if OBJECT_ID('tempdb..#EditAROYHTemp') is not null drop table #EditAROYHTemp;
			create table #EditAROYHTemp (id varchar(20),contno varchar(20),msg varchar(max));
			
			begin tran EditAROYHTemp
			begin try
			
				declare @CONTNO varchar(max) = '".$ARCONT."';
				
				update {$this->MAuth->getdb('AROTHR')}
				set PAYFOR = '".$PAYFOR."', PAYAMT = '".$PAYAMT."', MEMO1 = '".$MEMO."'
				where ARCONT like '%".$ARCONT."%' and CONTNO like '%".$CONTNO."%' and CUSCOD like '%".$CUSCOD."%'
				
				insert into #EditAROYHTemp select 'S',@CONTNO,'แก้ไขลูกหนี้อื่น เลขที่สัญญา '+@CONTNO+' เรียบร้อย';
				
				commit tran EditAROYHTemp;
			end try
			begin catch
				rollback tran EditAROYHTemp;
				insert into #EditAROYHTemp select 'E','',ERROR_MESSAGE();
			end catch
		";
		//echo $sql; exit;
		
		$this->db->query($sql);
		$sql = "select * from #EditAROYHTemp";
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
			$response["msg"] = 'ผิดพลาดไม่สามารถแก้ไขรายการลูกหนี้อื่นได้ โปรดติดต่อฝ่ายไอที';
		}
		
		echo json_encode($response);
	}
	
	function Delete_AROTHER(){
		$LOCAT	= $_REQUEST["LOCAT"];
		$ARCONT	= $_REQUEST["ARCONT"];
		$CONTNO	= $_REQUEST["CONTNO"];
		$CUSCOD	= $_REQUEST["CUSCOD"];
		
		$sql = "
			if OBJECT_ID('tempdb..#DelAROYHTemp') is not null drop table #DelAROYHTemp;
			create table #DelAROYHTemp (id varchar(20),contno varchar(20),msg varchar(max));
			
			begin tran DelAROYHTemp
			begin try
			
				declare @CONTNO varchar(max) = '".$ARCONT."';
				delete {$this->MAuth->getdb('AROTHR')}
				where ARCONT = '".$ARCONT."' and CONTNO = '".$CONTNO."'
				and CUSCOD = '".$CUSCOD."' and LOCAT = '".$LOCAT."'
				
				insert into #DelAROYHTemp select 'S',@CONTNO,'ลบลูกหนี้อื่น เลขที่สัญญา '+@CONTNO+' เรียบร้อย';
				commit tran DelAROYHTemp;
			end try
			begin catch
				rollback tran DelAROYHTemp;
				insert into #DelAROYHTemp select 'E','',ERROR_MESSAGE();
			end catch
		";
		//echo $sql; exit;
		
		$this->db->query($sql);
		$sql = "select * from #DelAROYHTemp";
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
			$response["msg"] = 'ผิดพลาดไม่สามารถลบรายการลูกหนี้อื่นได้ โปรดติดต่อฝ่ายไอที';
		}
		
		echo json_encode($response);
	}
	
	function upload_SaleTarget(){
		$TargetMonth 	= $_REQUEST["TargetMonth"]; 
		$TargetYear 	= $_REQUEST["TargetYear"];
		
		$output_dir = $_SERVER['DOCUMENT_ROOT']."/reports/test/";
		//echo $output_dir; exit;
		
		if(isset($_FILES["myfile"])){
			$error = $_FILES["myfile"]["error"];
			if(!is_array($_FILES["myfile"]["name"])) {  //single file
				
				$exfile = explode(".",$_FILES["myfile"]["name"]);
				//$fileName = $_FILES["myfile"]["name"].".".$exfile[(sizeof($exfile)-1)];
				$fileName = $TargetMonth."_".$TargetYear.".".$exfile[(sizeof($exfile)-1)];
				
				$destination_file = $output_dir.$fileName;
				$source_file	  = $_FILES["myfile"]["tmp_name"];
				move_uploaded_file($source_file,$output_dir.$fileName);
				
				//$fileServer = str_replace($output_dir,"",$arrsResult['filePath']);
				$fileServer = $output_dir;
				$response["origin"][] = $_FILES["myfile"]["name"];
				$response["new"][] = $fileName;
				$response["locat"][] = $fileServer.$output_dir.$fileName;
				//$response = array('origin'=>$_FILES["myfile"]["name"],'new'=>$fileName,'locat'=>$fileServer.$output_dir.$fileName);

			}	
			echo json_encode($response);
		}
	}
}





















