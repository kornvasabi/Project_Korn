<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/********************************************************
             ______@29/07/2020______
			 Pasakorn Boonded

********************************************************/
class ReportSalesAsLocat extends MY_Controller {
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
			<div class='k_tab1' name='home' locat='{$this->sess['branch']}' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}' today='".$this->today('today')."' style='height:calc(100vh - 132px);overflow:auto;background-color:white;'>
				<div class='col-sm-12 col-xs-12' style='background-color:#0480E0;border:5px solid white;height:75px;text-align:center;font-size:12pt;color:white;font-weight:bold;'>	
					<br>รายงานสรุปการขายตามสาขา<br>
				</div>
				<div class='col-sm-12 col-xs-12'>
					<div class='col-sm-4 col-xs-4'>
						<div class='form-group'>
							<div class='col-sm-12'>	
								<div class='form-group'>
									ทำสัญญาที่สาขา
									<select id='CRLOCAT' class='form-control input-sm'></select>
								</div>
							</div>
							<div class='col-sm-12'>	
								<div class='form-group'>
									จากวันที่
									<input type='text' id='SDATE_F' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' value='".$this->today('today')."' styl='font-size:10.5pt;'>
								</div>
							</div>
							<div class='col-sm-12'>	
								<div class='form-group'>
									ถึงวันที่
									<input type='text' id='SDATE_T' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' value='".$this->today('today')."' styl='font-size:10.5pt;'>
								</div>
							</div>	
							<div class='col-sm-12'>	
								<div class='form-group'>
									สถานะสินค้า
									<select id='STAT' class='form-control input-sm'>
										<option></option>
										<option value='N'>รถใหม่ (N)</option>
										<option value='O'>รถเก่า (O)</option>
										<option value=''>รวมทั้งหมด</option>
									</select>
								</div>
							</div>
							<div class='col-sm-12'>	
								<div class='form-group'>
									แบบ
									<select id='BAAB' class='form-control input-sm'></select>
								</div>
							</div>							
						</div>
					</div>
					<div class='col-sm-4 col-xs-4'>
						<div class='form-group'>
							<div class='col-sm-12'>	
								<div class='form-group'>
									กลุ่มสินค้า
									<select id='GCODE' class='form-control input-sm'></select>
								</div>
							</div>
							<div class='col-sm-12'>	
								<div class='form-group'>
									ยี่ห้อ
									<select id='TYPE' class='form-control input-sm'></select>
								</div>
							</div>
							<div class='col-sm-12'>	
								<div class='form-group'>
									รุ่นสินค้า
									<select id='MODEL' class='form-control input-sm'></select>
								</div>
							</div>
							<div class='col-sm-12'>	
								<div class='form-group'>
									พนักงานขาย
									<select id='SALCOD' class='form-control input-sm'></select>
								</div>
							</div>
							<div class='col-sm-12'>	
								<div class='form-group'>
									สี
									<select id='COLOR' class='form-control input-sm'></select>
								</div>
							</div>							
						</div>
					</div>
					<div class='col-sm-4 col-xs-4'>
						<div class='form-group'>
							รูปแบบข้อมูลรายงาน
							<div class='col-sm-12 col-xs-12' style='border:0.1px dotted #d6d6d6;height:200px;'>
								<div class='col-sm-12'>
									<label>
										<input type= 'radio' id='R1' name='report' checked> แยกตามสาขา
									</label><br><br>
									<label>
										<input type= 'radio' id='R2' name='report'> รวมแต่ละสาขา
									</label><br><br>
									<label>
										<input type= 'radio' id='R3' name='report'> รวมทุกสาขาที่ระบุ
									</label><br><br>
									<span>
										สถานะ : N := รถใหม่ , O := รถเก่า ,All := ทั้งหมด
									</span>
								</div>
							</div>
							<div class='col-sm-12 col-xs-12'>
								<br>
								<button id='btnreport' type='button' class='btn btn-info btn-outline btn-block' style='width:100%;height:70px;'><span class='fa fa-folder-open'><b>แสดง</b></span></button>
							</div>
						</div>
					</div>
				</div>
				<div class='col-sm-12'>
					<div class='col-sm-12'>
						<div id='result'></div>
					</div>
				</div>
			</div>
		";
		$html .="<script src='".base_url('public/js/SYS09/ReportSalesAsLocat.js')."'></script>";
		echo $html;
	}
	function conditiontopdf(){
		$data 	= 	array();
		$data[] = 	urlencode(
			$_REQUEST["CRLOCAT"].'||'.$_REQUEST["SDATE_F"].'||'.$_REQUEST["SDATE_T"].'||'.
			$_REQUEST["STAT"].'||'.$_REQUEST["BAAB"].'||'.$_REQUEST["GCODE"].'||'.
			$_REQUEST["TYPE"].'||'.$_REQUEST["MODEL"].'||'.$_REQUEST["SALCOD"].'||'.
			$_REQUEST["COLOR"].'||'.$_REQUEST['SReport']
		);
		echo json_encode($this->generateData($data,"encode"));
	}
	function pdf(){
		$data 	= array();
		$data[] = $_GET["condpdf"];
		$arrs 	= $this->generateData($data,"decode");
		$arrs[0]= urldecode($arrs[0]);
		$tx 	= explode("||",$arrs[0]);
		
		$CRLOCAT	= $tx[0];
		$SDATE_F	= $this->Convertdate(1,$tx[1]);
		$SDATE_T	= $this->Convertdate(1,$tx[2]);
		$STAT		= $tx[3];
		$BAAB		= $tx[4];
		$GCODE		= $tx[5];
		$TYPE		= $tx[6];
		$MODEL		= $tx[7];
		$SALCOD		= $tx[8];
		$COLOR		= $tx[9];
		$SReport    = $tx[10];
		
		$sql = "
			select COMP_NM from {$this->MAuth->getdb('CONDPAY')}
		";
		$query = $this->db->query($sql);
		$row1		= $query->row();
		$COMP_NM 	= $row1->COMP_NM;
		
		$sql = "
			if object_id('tempdb..#tempINVTRAN') is not null drop table #tempINVTRAN;
			create table #tempINVTRAN (MODEL varchar(20),BAAB varchar(20),COLOR varchar(20)
			,CRLOCAT varchar(20),COUNTSTRNO int);
			insert into #tempINVTRAN
			select MODEL,BAAB,COLOR,CRLOCAT,COUNT(STRNO) as korn from (
				select  
					MODEL,BAAB,COLOR,CRLOCAT,STRNO  
				from {$this->MAuth->getdb('INVTRAN')} 
				where (SDATE between '".$SDATE_F."' and '".$SDATE_T."') and CRLOCAT like '".$CRLOCAT."%' 
				and (GCODE like '".$GCODE."%' or GCODE is null) and (TYPE like '".$TYPE."%' or TYPE is null) 
				and (MODEL like '".$MODEL."%' or MODEl is null) and (STAT like '".$STAT."%' or STAT is null) 
				and (BAAB like '".$BAAB."%' or BAAB is null) and (COLOR like '".$COLOR."%' or COLOR is null) 
				union  
				select  
					MODEL,BAAB,COLOR,CRLOCAT,STRNO  
				from {$this->MAuth->getdb('HINVTRAN')} 
				where (SDATE between '".$SDATE_F."' and '".$SDATE_T."') and CRLOCAT like '".$CRLOCAT."%' 
				and (GCODE like '".$GCODE."%' or GCODE is null) and (TYPE like '".$TYPE."%' or TYPE is null) 
				and (MODEL like '".$MODEL."%' or MODEl is null) and (STAT like '".$STAT."%' or STAT is null) 
				and (BAAB like '".$BAAB."%' or BAAB is null) and (COLOR like '".$COLOR."%' or COLOR is null) 
			) as D group by MODEL,BAAB,COLOR,CRLOCAT 
			having COUNT(STRNO) > 0 order by CRLOCAT,MODEL,BAAB,COLOR
		";
		//echo $sql;
		$this->db->query($sql);
		$sql = "
			IF OBJECT_ID('tempdb..#ARC') IS NOT NULL DROP TABLE #ARC
			select *
			into #ARC
			FROM(
				select LOCAT,MODEL,BAAB,COLOR
					,CAST(COUNT(MODEL) as int) as countMODEL
					,CAST(SUM(TOTPRC) as int) as TOTPRC 
				from (
					--ขายสด  ->> C
					select 
						A.MODEL,A.BAAB,A.COLOR,B.LOCAT,B.TOTPRC,A.STRNO  
					from {$this->MAuth->getdb('INVTRAN')} A,{$this->MAuth->getdb('ARCRED')} B 
					where A.STRNO = B.STRNO and A.TSALE = B.TSALE 
					and B.SDATE between '".$SDATE_F."' and '".$SDATE_T."' 
					and B.LOCAT like '".$CRLOCAT."%' and (A.GCODE like '".$GCODE."%' or A.GCODE is null) 
					and (A.TYPE like '".$TYPE."%') and (A.MODEL like '".$MODEL."%' or A.MODEL is null) 
					and (A.TSALE = 'C') and (A.STAT like '".$STAT."%' or A.STAT is null) 
					and (B.SALCOD like '".$SALCOD."%' or B.SALCOD is null) and B.TOTPRC > 0  
					union  
					select 
						A.MODEL,A.BAAB,A.COLOR,B.LOCAT,B.TOTPRC,A.STRNO  
					from {$this->MAuth->getdb('HINVTRAN')} A,{$this->MAuth->getdb('HARCRED')} B 
					where A.STRNO = B.STRNO and A.TSALE = B.TSALE 
					and B.SDATE between '".$SDATE_F."' and '".$SDATE_T."' 
					and B.LOCAT like '".$CRLOCAT."%' and (A.GCODE like '".$GCODE."%' or A.GCODE is null) 
					and (A.TYPE like '".$TYPE."%') and (A.MODEL like '".$MODEL."%' or A.MODEL is null) 
					and (A.TSALE = 'C') and (A.STAT like '".$STAT."%' or A.STAT is null) 
					and (B.SALCOD like '".$SALCOD."%' or B.SALCOD is null) and B.TOTPRC > 0 
				)a group by MODEL,BAAB,COLOR,LOCAT
			)ARC
		";
		//echo $sql;
		$this->db->query($sql);
		$sql = "
			IF OBJECT_ID('tempdb..#ARF') IS NOT NULL DROP TABLE #ARF
			select *
			into #ARF
			FROM(
				select LOCAT,MODEL,BAAB,COLOR
					,CAST(COUNT(MODEL) as int) as countMODEL
					,CAST(SUM(TOTPRC) as int) as TOTPRC
				from (
					--ขายส่งไฟแนนช์ TSALE ->> F
					select 
						A.MODEL,A.BAAB,A.COLOR,B.LOCAT,B.TOTPRC,A.STRNO  
					from {$this->MAuth->getdb('INVTRAN')} A,{$this->MAuth->getdb('ARFINC')} B 
					where A.STRNO = B.STRNO and A.TSALE = B.TSALE 
					and B.SDATE between '".$SDATE_F."' and '".$SDATE_T."' 
					and B.LOCAT like '".$CRLOCAT."%' and (A.GCODE like '".$GCODE."%' or A.GCODE is null) 
					and (A.TYPE like '".$TYPE."%') and (A.MODEL like '".$MODEL."%' or A.MODEL is null) 
					and (A.TSALE = 'F') and (A.STAT like '".$STAT."%' or A.STAT is null) 
					and (B.SALCOD like '".$SALCOD."%' or B.SALCOD is null) and B.TOTPRC > 0  
					union  
					select 
						A.MODEL,A.BAAB,A.COLOR,B.LOCAT,B.TOTPRC,A.STRNO  
					from {$this->MAuth->getdb('HINVTRAN')} A,{$this->MAuth->getdb('HARFINC')} B 
					where A.STRNO = B.STRNO and A.TSALE = B.TSALE 
					and B.SDATE between '".$SDATE_F."' and '".$SDATE_T."' 
					and B.LOCAT like '".$CRLOCAT."%' and (A.GCODE like '".$GCODE."%' or A.GCODE is null) 
					and (A.TYPE like '".$TYPE."%') and (A.MODEL like '".$MODEL."%' or A.MODEL is null) 
					and (A.TSALE = 'F') and (A.STAT like '".$STAT."%' or A.STAT is null) 
					and (B.SALCOD like '".$SALCOD."%' or B.SALCOD is null) and B.TOTPRC > 0 
				)a group by MODEL,BAAB,COLOR,LOCAT
			)ARF
		";
		//echo $sql;
		$this->db->query($sql);
		$sql = "
			IF OBJECT_ID('tempdb..#ARM') IS NOT NULL DROP TABLE #ARM
			select *
			into #ARM
			FROM(
				--ขายผ่อนเช่าซื้อ TSALE ->> H
				select LOCAT,MODEL,BAAB,COLOR
					,CAST(COUNT(MODEL) as int) as countMODEL
					,CAST(SUM(TOTPRC) as int) as TOTPRC 
				from (
					select 
						A.MODEL,A.BAAB,A.COLOR,B.LOCAT,B.TOTPRC,A.STRNO 
					from {$this->MAuth->getdb('INVTRAN')} A,{$this->MAuth->getdb('ARMAST')} B 
					where A.STRNO = B.STRNO and A.TSALE = B.TSALE 
					and B.SDATE between '".$SDATE_F."' and '".$SDATE_T."' 
					and B.LOCAT like '".$CRLOCAT."%' and (A.GCODE like '".$GCODE."%' or A.GCODE is null) 
					and (A.TYPE like '".$TYPE."%') and (A.MODEL like '".$MODEL."%' or A.MODEL is null) 
					and (A.TSALE = 'H') and (A.STAT like '".$STAT."%' or A.STAT is null) 
					and (B.SALCOD like '".$SALCOD."%' or B.SALCOD is null) and B.TOTPRC > 0  
					union  
					select 
						A.MODEL,A.BAAB,A.COLOR,B.LOCAT,B.TOTPRC,A.STRNO 
					from {$this->MAuth->getdb('HINVTRAN')} A,{$this->MAuth->getdb('HARMAST')} B 
					where A.STRNO = B.STRNO and A.TSALE = B.TSALE 
					and B.SDATE between '".$SDATE_F."' and '".$SDATE_T."' 
					and B.LOCAT like '".$CRLOCAT."%' and (A.GCODE like '".$GCODE."%' or A.GCODE is null) 
					and (A.TYPE like '".$TYPE."%') and (A.MODEL like '".$MODEL."%' or A.MODEL is null) 
					and (A.TSALE = 'H') and (A.STAT like '".$STAT."%' or A.STAT is null) 
					and (B.SALCOD like '".$SALCOD."%' or B.SALCOD is null) and B.TOTPRC > 0 
				)a group by MODEL,BAAB,COLOR,LOCAT
			)ARM
		";
		//echo $sql;
		$this->db->query($sql);
		$sql = "
			IF OBJECT_ID('tempdb..#AR_T') IS NOT NULL DROP TABLE #AR_T
			select *
			into #AR_T
			FROM(
				select LOCAT,MODEL,BAAB,COLOR
					,CAST(COUNT(MODEL) as int) as countMODEL
					,CAST(SUM(TOTPRC) as int) as TOTPRC 
				from (
					--ขายส่งเอเย่นต์ TSALE ->> A
					select 
						A.MODEL,A.BAAB,A.COLOR,B.LOCAT,B.TOTPRC,A.STRNO  
					from {$this->MAuth->getdb('INVTRAN')} A,{$this->MAuth->getdb('AR_TRANS')} B
					,{$this->MAuth->getdb('AR_INVOI')} C 
					where A.STRNO = B.STRNO and B.CONTNO = C.CONTNO and A.TSALE = B.TSALE 
					and C.SDATE between '".$SDATE_F."' and '".$SDATE_T."' 
					and B.LOCAT like '".$CRLOCAT."%' and (A.GCODE like '".$GCODE."%' or A.GCODE is null) 
					and (A.TYPE like '".$TYPE."%' or A.TYPE is null) and (A.MODEL like '".$MODEL."%' or A.MODEL is null) 
					and (A.TSALE = 'A') and (A.STAT like '".$STAT."%' or A.STAT is null) 
					and (C.SALCOD like '".$SALCOD."%' or C.SALCOD is null) and B.TOTPRC > 0 
					union
					select 
						A.MODEL,A.BAAB,A.COLOR,B.LOCAT,B.TOTPRC,A.STRNO  
					from {$this->MAuth->getdb('HINVTRAN')} A,{$this->MAuth->getdb('HAR_TRNS')} B
					,{$this->MAuth->getdb('HAR_INVO')} C 
					where A.STRNO = B.STRNO and B.CONTNO = C.CONTNO and A.TSALE = B.TSALE 
					and C.SDATE between '".$SDATE_F."' and '".$SDATE_T."' 
					and B.LOCAT like '".$CRLOCAT."%' and (A.GCODE like '".$GCODE."%' or A.GCODE is null) 
					and (A.TYPE like '".$TYPE."%' or A.TYPE is null) and (A.MODEL like '".$MODEL."%' or A.MODEL is null) 
					and (A.TSALE = 'A') and (A.STAT like '".$STAT."%' or A.STAT is null) 
					and (C.SALCOD like '".$SALCOD."%' or C.SALCOD is null) and B.TOTPRC > 0
				)a group by MODEL,BAAB,COLOR,LOCAT
			)AR_T
		";
		//echo $sql;
		$this->db->query($sql);
		
		$head = ""; $html = ""; $i=0;
		if($SReport == "R1"){
			$HeadReport = "
				<th width='100px' align='left'   style='border-top:0.1px solid black;vertical-align:top;'>รุ่น</th>
				<th width='80px'  align='left' 	 style='border-top:0.1px solid black;vertical-align:top;'>แบบ</th>
				<th width='100px' align='left'	 style='border-top:0.1px solid black;vertical-align:top;'>สี</th> 
			";
			$sql = "
				select inv.*
					,ISNULL(c.countMODEL,0) as COUNTMODELC,ISNULL(c.TOTPRC,0) as TOTPRCC 
					,ISNULL(f.countMODEL,0) as COUNTMODELF,ISNULL(f.TOTPRC,0) as TOTPRCF 
					,ISNULL(h.countMODEL,0) as COUNTMODELH,ISNULL(h.TOTPRC,0) as TOTPRCH 
					,ISNULL(a.countMODEL,0) as COUNTMODELA,ISNULL(a.TOTPRC,0) as TOTPRCA
					,(ISNULL(c.countMODEL,0) + ISNULL(f.countMODEL,0))+(ISNULL(h.countMODEL,0) + ISNULL(a.countMODEL,0)) as TOTCAR
					,(ISNULL(c.TOTPRC,0) + ISNULL(f.TOTPRC,0))+(ISNULL(h.TOTPRC,0) + ISNULL(a.TOTPRC,0)) TOTPRICE  
				from #tempINVTRAN inv
				left join #ARC c on inv.CRLOCAT = c.LOCAT collate Thai_CI_AS and inv.MODEL = c.MODEL collate Thai_CI_AS 
				and inv.BAAB = c.BAAB collate Thai_ci_as and inv.COLOR = c.COLOR collate Thai_ci_as
				left join #ARF f  on inv.CRLOCAT = f.LOCAT collate Thai_CI_AS and inv.MODEL = f.MODEL collate Thai_CI_AS 
				and inv.BAAB = f.BAAB collate Thai_ci_as and inv.COLOR = f.COLOR collate Thai_ci_as
				left join #ARM h  on inv.CRLOCAT = h.LOCAT collate Thai_CI_AS and inv.MODEL = h.MODEL collate Thai_CI_AS 
				and inv.BAAB = h.BAAB collate Thai_ci_as and inv.COLOR = h.COLOR collate Thai_ci_as
				left join #AR_T a on inv.CRLOCAT = a.LOCAT collate Thai_CI_AS and inv.MODEL = a.MODEL collate Thai_CI_AS 
				and inv.BAAB = a.BAAB collate Thai_ci_as and inv.COLOR = a.COLOR collate Thai_ci_as
			";	
		}else if($SReport == "R2"){
			$HeadReport = "
				<th width='100px' align='left'   style='border-top:0.1px solid black;vertical-align:top;' colspan='3'>ชื่อสาขา</th> 
			";
			$sql = "
				select aa.*,b.LOCATNM from (
					select INV.CRLOCAT
						,ISNULL(SUM(c.countMODEL),0) as COUNTMODELC,ISNULL(sum(c.TOTPRC),0) as TOTPRCC 
						,ISNULL(SUM(f.countMODEL),0) as COUNTMODELF,ISNULL(SUM(f.TOTPRC),0) as TOTPRCF 
						,ISNULL(SUM(h.countMODEL),0) as COUNTMODELH,ISNULL(SUM(h.TOTPRC),0) as TOTPRCH 
						,ISNULL(SUM(a.countMODEL),0) as COUNTMODELA,ISNULL(SUM(a.TOTPRC),0) as TOTPRCA
						,(ISNULL(SUM(c.countMODEL),0) + ISNULL(SUM(f.countMODEL),0))+(ISNULL(sum(h.countMODEL),0) + ISNULL(sum(a.countMODEL),0)) as TOTCAR
						,(ISNULL(sum(c.TOTPRC),0) + ISNULL(sum(f.TOTPRC),0))+(ISNULL(sum(h.TOTPRC),0) + ISNULL(sum(a.TOTPRC),0)) TOTPRICE
					from (
						select distinct CRLOCAT from #tempINVTRAN 
					)inv
					left join #ARC  c  on inv.CRLOCAT = c.LOCAT collate Thai_CI_AS
					left join #ARF  f  on inv.CRLOCAT = f.LOCAT collate Thai_CI_AS 
					left join #ARM  h  on inv.CRLOCAT = h.LOCAT collate Thai_CI_AS 
					left join #AR_T a  on inv.CRLOCAT = a.LOCAT collate Thai_CI_AS 
					group by inv.CRLOCAT
				)aa left join {$this->MAuth->getdb('INVLOCAT')} b on aa.CRLOCAT = b.LOCATCD collate Thai_CI_AS 
			";
		}else if($SReport == "R3"){
			$HeadReport = "
				<th width='100px' align='left'   style='border-top:0.1px solid black;vertical-align:top;'>รุ่น</th>
				<th width='80px'  align='left' 	 style='border-top:0.1px solid black;vertical-align:top;'>แบบ</th>
				<th width='100px' align='left'	 style='border-top:0.1px solid black;vertical-align:top;'>สี</th> 
			";
			$sql = "
				select 'ทุกสาขา' as CRLOCAT,inv.MODEL,inv.BAAB,inv.COLOR
					,ISNULL(SUM(c.countMODEL),0) as COUNTMODELC,ISNULL(sum(c.TOTPRC),0) as TOTPRCC 
					,ISNULL(SUM(f.countMODEL),0) as COUNTMODELF,ISNULL(SUM(f.TOTPRC),0) as TOTPRCF 
					,ISNULL(SUM(h.countMODEL),0) as COUNTMODELH,ISNULL(SUM(h.TOTPRC),0) as TOTPRCH 
					,ISNULL(SUM(a.countMODEL),0) as COUNTMODELA,ISNULL(SUM(a.TOTPRC),0) as TOTPRCA
					,(ISNULL(SUM(c.countMODEL),0) + ISNULL(SUM(f.countMODEL),0))+(ISNULL(sum(h.countMODEL),0) + ISNULL(sum(a.countMODEL),0)) as TOTCAR
					,(ISNULL(sum(c.TOTPRC),0) + ISNULL(sum(f.TOTPRC),0))+(ISNULL(sum(h.TOTPRC),0) + ISNULL(sum(a.TOTPRC),0)) TOTPRICE
				from (
					select distinct MODEL,BAAB,COLOR from #tempINVTRAN 
				)inv
				left join #ARC  c  on inv.MODEL = c.MODEL collate Thai_CI_AS
				and inv.BAAB = c.BAAB collate Thai_CI_AS and inv.COLOR = c.COLOR collate Thai_CI_AS
				left join #ARF  f  on inv.MODEL = f.MODEL collate Thai_CI_AS 
				and inv.BAAB = f.BAAB collate Thai_CI_AS and inv.COLOR = f.COLOR collate Thai_CI_AS
				left join #ARM  h  on inv.MODEL = h.MODEL collate Thai_CI_AS 
				and inv.BAAB = h.BAAB collate Thai_CI_AS and inv.COLOR = h.COLOR collate Thai_CI_AS
				left join #AR_T a  on inv.MODEL = a.MODEL collate Thai_CI_AS 
				and inv.BAAB = a.BAAB collate Thai_CI_AS and inv.COLOR = a.COLOR collate Thai_CI_AS
				group by inv.MODEL,inv.BAAB,inv.COLOR
			";
		}
		$head = "
			<tr>
				<th width='40px'  align='center' style='border-top:0.1px solid black;vertical-align:top;'>No.</th>
				<th width='60px'  align='left'	 style='border-top:0.1px solid black;vertical-align:top;'>สาขา</th>
				".$HeadReport."
				<th width='180px' align='center' style='border-top:0.1px solid black;vertical-align:top;'colspan='2'>ขายสด</th>
				<th width='180px' align='center' style='border-top:0.1px solid black;vertical-align:top;'colspan='2'>ขายส่งไฟแนนซ์</th>
				<th width='180px' align='center' style='border-top:0.1px solid black;vertical-align:top;'colspan='2'>ขายผ่อนเช่าซื้อ</th>
				<th width='180px' align='center' style='border-top:0.1px solid black;vertical-align:top;'colspan='2'>ขายส่งเอเย่นต์</th>
				<th width='180px' align='center' style='border-top:0.1px solid black;vertical-align:top;'colspan='2'>รวมทั้งสิ้น</th>
			</tr>
			<tr>
				<th width='40px'  align='center' style='border-bottom:0.1px solid black;vertical-align:top;'></th>
				<th width='60px'  align='left'	 style='border-bottom:0.1px solid black;vertical-align:top;'></th>
				<th width='100px' align='left'   style='border-bottom:0.1px solid black;vertical-align:top;'></th>
				<th width='80px'  align='left' 	 style='border-bottom:0.1px solid black;vertical-align:top;'></th>
				<th width='100px' align='left'	 style='border-bottom:0.1px solid black;vertical-align:top;'></th>
				<th width='90px'  align='right' style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;'>จำนวนคัน</th>
				<th width='90px'  align='right' style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;'>รวมราคาขาย</th>
				<th width='90px'  align='right' style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;'>จำนวนคัน</th>
				<th width='90px'  align='right' style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;'>รวมราคาขาย</th>
				<th width='90px'  align='right' style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;'>จำนวนคัน</th>
				<th width='90px'  align='right' style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;'>รวมราคาขาย</th>
				<th width='90px'  align='right' style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;'>จำนวนคัน</th>
				<th width='90px'  align='right' style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;'>รวมราคาขาย</th>
				<th width='90px'  align='right' style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;'>จำนวนคัน</th>
				<th width='90px'  align='right' style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;'>รวมราคาขาย</th>
			</tr>
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){$i++;
				if($SReport == "R1" or $SReport == "R3"){
					$DATAReport = "
						<td width='100px' height='40px' align='left'   >".$row->MODEL."</td>
						<td width='80px'  height='40px'	align='left'   >".$row->BAAB."</td>
						<td width='100px' height='40px'	align='left'   >".$row->COLOR."</td>
					";
				}else if($SReport == "R2"){
					$DATAReport = "
						<td width='100px' height='40px' align='left'   colspan='3'>".$row->LOCATNM."</td>
					";
				}
				$arrs['COUNTMODELC'][] = $row->COUNTMODELC;
				$arrs['TOTPRCC'][]     = $row->TOTPRCC;
				$arrs['COUNTMODELF'][] = $row->COUNTMODELF;
				$arrs['TOTPRCF'][]     = $row->TOTPRCF;
				$arrs['COUNTMODELH'][] = $row->COUNTMODELH;
				$arrs['TOTPRCH'][]     = $row->TOTPRCH;
				$arrs['COUNTMODELA'][] = $row->COUNTMODELA;
				$arrs['TOTPRCA'][]     = $row->TOTPRCA;
				$arrs['TOTCAR'][]      = $row->TOTCAR;
				$arrs['TOTPRICE'][]    = $row->TOTPRICE;
				
				$html .="
					<tr class='trow'>
						<td width='40px'  height='40px'	align='center' >".$i.".</td>
						<td width='60px'  height='40px'	align='left'   >".$row->CRLOCAT."</td>
						".$DATAReport."
						<td width='90px'  height='40px'	align='right'  >".$row->COUNTMODELC."</td>
						<td width='90px'  height='40px'	align='right'  >".number_format($row->TOTPRCC,2)."</td>
						<td width='90px'  height='40px'	align='right'  >".$row->COUNTMODELF."</td>
						<td width='90px'  height='40px'	align='right'  >".number_format($row->TOTPRCF,2)."</td>
						<td width='90px'  height='40px'	align='right'  >".$row->COUNTMODELH."</td>
						<td width='90px'  height='40px'	align='right'  >".number_format($row->TOTPRCH,2)."</td>
						<td width='90px'  height='40px'	align='right'  >".$row->COUNTMODELA."</td>
						<td width='90px'  height='40px'	align='right'  >".number_format($row->TOTPRCA,2)."</td>
						<td width='90px'  height='40px'	align='right'  >".$row->TOTCAR."</td>
						<td width='90px'  height='40px'	align='right'  >".number_format($row->TOTPRICE,2)."</td>
					</tr>
				";	
			}
		}
		if($i > 0){
			$html .="
				<tr class='trow' style='background-color:#ebebeb;'>
					<th width='100px' height='40px'	align='center' style='border-top:0.1px solid black;vertical-text:center;border-bottom:0.1px solid black;' colspan='2'>รวมทั้งสิ้น</th>
					<td width='100px' height='40px'	align='center' style='border-top:0.1px solid black;vertical-text:center;border-bottom:0.1px solid black;' >".$i."</td>
					<th width='180px' height='40px'	align='center' style='border-top:0.1px solid black;vertical-text:center;border-bottom:0.1px solid black;' colspan='2'>รายการ</th>
					<td width='90px'  height='40px'	align='right'  style='border-top:0.1px solid black;vertical-text:center;border-bottom:0.1px solid black;' >".array_sum($arrs['COUNTMODELC'])."</td>
					<td width='90px'  height='40px'	align='right'  style='border-top:0.1px solid black;vertical-text:center;border-bottom:0.1px solid black;' >".number_format(array_sum($arrs['TOTPRCC']),2)."</td>
					<td width='90px'  height='40px'	align='right'  style='border-top:0.1px solid black;vertical-text:center;border-bottom:0.1px solid black;' >".array_sum($arrs['COUNTMODELF'])."</td>
					<td width='90px'  height='40px'	align='right'  style='border-top:0.1px solid black;vertical-text:center;border-bottom:0.1px solid black;' >".number_format(array_sum($arrs['TOTPRCF']),2)."</td>
					<td width='90px'  height='40px'	align='right'  style='border-top:0.1px solid black;vertical-text:center;border-bottom:0.1px solid black;' >".array_sum($arrs['COUNTMODELH'])."</td>
					<td width='90px'  height='40px'	align='right'  style='border-top:0.1px solid black;vertical-text:center;border-bottom:0.1px solid black;' >".number_format(array_sum($arrs['TOTPRCH']),2)."</td>
					<td width='90px'  height='40px'	align='right'  style='border-top:0.1px solid black;vertical-text:center;border-bottom:0.1px solid black;' >".array_sum($arrs['COUNTMODELA'])."</td>
					<td width='90px'  height='40px'	align='right'  style='border-top:0.1px solid black;vertical-text:center;border-bottom:0.1px solid black;' >".number_format(array_sum($arrs['TOTPRCA']),2)."</td>
					<td width='90px'  height='40px'	align='right'  style='border-top:0.1px solid black;vertical-text:center;border-bottom:0.1px solid black;' >".array_sum($arrs['TOTCAR'])."</td>
					<td width='90px'  height='40px'	align='right'  style='border-top:0.1px solid black;vertical-text:center;border-bottom:0.1px solid black;' >".number_format(array_sum($arrs['TOTPRICE']),2)."</td>
				</tr>
				<tr>
					<th width='100px' height='40px'	align='left' style='border-top:0.1px solid black;vertical-text:center;border-bottom:0.1px solid black;color:red;' colspan='15'>***หมายเหตุไม่รวมรถยึดเปลี่ยนเป็นรถเก่า***</th>
				</tr>
			";
		}
		
		$body = "<table class='fs9' cellspacing='0'>".$html."</table>";
		
		$mpdf = new \Mpdf\Mpdf([
			'mode' => 'utf-8', 
			'format' => 'A4-L',
			'margin_top' => 50, 	//default = 16
			'margin_left' => 10, 	//default = 15
			'margin_right' => 10, 	//default = 15
			'margin_bottom' => 9, 	//default = 16
			'margin_header' => 9, 	//default = 9
			'margin_footer' => 9, 	//default = 9
		]);
		
		$stylesheet = "
			<style>
				body { font-family: garuda;font-size:9pt; }
				.wf { width:100%; }
				.fs9 { font-size:9pt; }
				.h30 { height:30px; }
				.bor { border-top:0.1px solid black;border-bottom:0.1px solid black;}
			</style>
		";
		
		if($i > 0){
			$header = "
				<table class='wf fs9' cellspacing='0' style='border-collapse:collapse;line-height:23px;overflow:wrap;vertical-align:text-top;'>
					<tr>
						<th colspan='15' style='font-size:11pt;' align='center'>".$COMP_NM."<br>สรุปการขายรถ</th>
					</tr>
					<tr>
						<th colspan='15' style='font-size:11pt;' align='center'>
							<b>สาขา</b>&nbsp;&nbsp;".$CRLOCAT."&nbsp;&nbsp;
							<b>จากวันที่</b>&nbsp;&nbsp;".$this->Convertdate(2,$SDATE_F)."&nbsp;&nbsp;
							<b>ถึงวันที่</b>&nbsp;&nbsp;".$this->Convertdate(2,$SDATE_T)."&nbsp;&nbsp;
							<b>ประเภทสินค้า</b>&nbsp;&nbsp;".$GCODE."&nbsp;&nbsp;
							<b>ยี่ห้อ</b>&nbsp;&nbsp;".$TYPE."&nbsp;&nbsp;
							<b>รุ่น</b>&nbsp;&nbsp;".$MODEL."&nbsp;&nbsp;
							<b>สถานะ</b>&nbsp;&nbsp;".$STAT."&nbsp;&nbsp;
							<b>พนักงานขาย</b>&nbsp;&nbsp;".$SALCOD."&nbsp;&nbsp;
						</th>
					</tr>
					<tr>
						<th colspan='15' style='font-size:11pt;' align='right'>
							RpAsA10,11
						</th>
					</tr>
					<tr>
						<td colspan='2' align='left'>วันที่พิมพ์รายงาน</td>
						<td colspan='3' align='left'>".date('d/m/').(date('Y')+543)." ".date('H:i')."</td>
						<td colspan='10' align='right'>หน้าที่ : {PAGENO} / {nb} &emsp;&emsp;</td>
					</tr>
					".$head."
				</table>
			";	
		}else{
			$header = "<div style='color:red;font-size:16pt;'>ไม่พบข้อมูลตามเงื่อนไขครับ</div>";
		}	
		$mpdf->SetHTMLHeader($header);
		$mpdf->WriteHTML($body.$stylesheet);
		$mpdf->fontdata['qanela'] = array('R' => "QanelasSoft-Regular.ttf",'B' => "QanelasSoft-Bold.ttf",); //แก้ปริ้นแล้วอ่านไม่ออก
		$mpdf->Output();
	}
}