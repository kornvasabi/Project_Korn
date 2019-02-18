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
class CReport011 extends MY_Controller {
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
				<div class='col-sm-12' style='height:130px;overflow:auto;'>					
					<div class='row'>
						<div class='col-xs-2 col-sm-2'>	
							<div class='form-group'>
								รหัสสาขา
								<select id='LOCAT' class='form-control input-sm chosen-select' data-placeholder='รหัสสาขา'>
									<option value='".$this->sess['branch']."'>".$this->sess['branch']."</option>
								</select>
							</div>
						</div>
						<div class='col-xs-2 col-sm-2'>	
							<div class='form-group'>
								เลขที่สัญญา
								<input type='text' id='CONTNO' class='form-control input-sm' placeholder='เลขที่สัญญา' >
							</div>
						</div>
						<div class='col-xs-2 col-sm-2'>	
							<div class='form-group'>
								จากวันที่ชำระ
								<input type='text' id='FPAYDT' class='form-control input-sm' placeholder='จากวันที่ชำระ' data-provide='datepicker' data-date-language='th-th'>
							</div>
						</div>
						<div class='col-xs-2 col-sm-2'>	
							<div class='form-group'>
								ถึงวันที่ชำระ
								<input type='text' id='TPAYDT' class='form-control input-sm' placeholder='ถึงวันที่ชำระ' data-provide='datepicker' data-date-language='th-th'>
							</div>
						</div>
						<div class='col-xs-2 col-sm-2'>	
							<div class='form-group'>
								พนักงานเก็บเงิน
								<select id='BILLCOLL' class='form-control input-sm chosen-select' data-placeholder='พนักงานเก็บเงิน'></select>
							</div>
						</div>						
						<div class='col-xs-2 col-sm-2'>	
							<div class='form-group'>
								กลุ่มสินค้า
								<select id='GCODE' class='form-control input-sm chosen-select' data-placeholder='กลุ่มสินค้า'></select>
							</div>
						</div>
					</div>
					<div class='row'>	
						<div class='col-xs-2 col-sm-2'>	
							<div class='form-group'>
								เรียงข้อมูลตาม
								<select id='ORDERBY' class='form-control input-sm chosen-select' data-placeholder='เรียงข้อมูลตาม'>
									<option value='LOCAT' selected>รหัสสาขา</option>
									<option value='CONTNO'>เลขที่สัญญา</option>
									<option value='CUSCOD'>รหัสลูกค้า</option>
									<option value='SDATE'>วันที่ทำสัญญา</option>
								</select>
							</div>
						</div>
						<div class='col-xs-2 col-sm-2'>	
							<div class='form-group'>
								วิธีรับรู้
								<select id='WAY' class='form-control input-sm chosen-select' data-placeholder='เรียงข้อมูลตาม'>
									<option value='1'>สัญญาก่อน 01/01/2551 อายุสัญญา <= 4 ปี</option>
									<option value='2'>สัญญาก่อน 01/01/2551 อายุสัญญา > 4 ปี</option>
									<option value='3'>สัญญาตั้งแต่ 01/01/2551</option>
									<option value='4' selected>สัญญาตั้งแต่ 01/10/2561</option>
								</select>
							</div>
						</div>
						<div class='col-xs-2 col-sm-1'>	
							<div class='form-group'>
								<br>
								<input type='button' id='btnt1search' class='btn btn-primary btn-sm' value='แสดง' style='width:100%'>
							</div>
						</div>
					</div>		
				</div>
				<div class='col-sm-12' id='resultt1users' style='height:calc(100% - 130px);overflow:auto;background-color:white;'></div>
			</div>
		";
		
		$html.= "<script src='".base_url('public/js/SYS12/CReport011.js')."'></script>";
		echo $html;
	}
	
	function search(){
		$arrs = array();
		$arrs['LOCAT'] 	= $_REQUEST['LOCAT'];
		$arrs['CONTNO'] = $_REQUEST['CONTNO'];
		$arrs['FPAYDT'] = $this->Convertdate(1,$_REQUEST['FPAYDT']);
		$arrs['TPAYDT'] = $this->Convertdate(1,$_REQUEST['TPAYDT']);
		$arrs['BILLCOLL'] = $_REQUEST['BILLCOLL'];
		$arrs['GCODE'] 	 = $_REQUEST['GCODE'];
		$arrs['ORDERBY'] = $_REQUEST['ORDERBY'];
		$arrs['WAY'] 	 = $_REQUEST['WAY'];
		
		$cond = "";
		if($arrs['LOCAT'] != ""){
			$cond .= " and A.LOCAT like '".$arrs['LOCAT']."%'";
		}
		
		if($arrs['CONTNO'] != ""){
			$cond .= " and A.CONTNO like '%".$arrs['CONTNO']."%'";
		}
		
		if($arrs['FPAYDT'] == ""){
			$response = array("html"=>"โปรดระบุ วันที่ชำระให้ถูกต้อง","status"=>false);
			echo json_encode($response); exit;
		}
		
		if($arrs['TPAYDT'] == ""){
			$response = array("html"=>"โปรดระบุ วันที่ชำระให้ถูกต้อง","status"=>false);
			echo json_encode($response); exit;
		}
		$cond .= " and convert(varchar(8),D.PAYDT,112) between '".$arrs['FPAYDT']."' and '".$arrs['TPAYDT']."'";
		
		if($arrs['WAY'] == 1){
			$cond .= " 
			and A.PROF_METHOD = 'SYD' and A.SDATE < '2008-1-1' AND (A.TOTPRC > 0) AND (A.NPROFIT > 0) ";
		}else if($arrs['WAY'] == 2){
			$cond .= " 
			and A.PROF_METHOD = 'EFF' and A.SDATE < '2008-1-1' AND (A.TOTPRC > 0) AND (A.NPROFIT > 0) ";
		}else if($arrs['WAY'] == 3){
			$cond .= " 
			and A.PROF_METHOD = 'EFF' and A.SDATE >= '2008-1-1' AND (A.TOTPRC > 0) AND (A.NPROFIT > 0) ";
		}else{
			$cond .= " 
			and A.PROF_METHOD = 'EFF' and A.SDATE >= '2018-10-01' AND (A.TOTPRC > 0) AND (A.NPROFIT > 0) ";
		}
		
		if($arrs['BILLCOLL'] != ""){
			$cond .= " and 
			A.BILLCOLL like '%".$arrs['Name']."%'";
		}
		
		$cond .= " and ((convert(varchar(8),A.YDATE,112) > '".$arrs['FPAYDT']."') OR (A.YDATE IS NULL))";
		
		if($arrs['GCODE'] != ""){
			$cond .= " and C.GCODE = '".$arrs['GCODE']."'";
		}
		
		$sql = "
			if OBJECT_ID('tempdb..#tempREPORT11') is not null drop table #tempREPORT11
			select A.LOCAT,A.CONTNO,A.EFFRT_AFADJ,A.TOTPRC,A.TOTPRES
				,A.CUSCOD,convert(varchar(8),A.SDATE,112) SDATE,A.NDAWN
				,RTRIM(B.SNAM)+' '+RTRIM(B.NAME1)+'  '+RTRIM(B.NAME2) AS CNAME
				,A.TOTDWN,A.NPAYRES,A.NKANG,convert(varchar(8),A.LPAYD,112) LPAYD
				,A.BILLCOLL,A.TOT_UPAY,A.EXP_AMT,A.VATDWN,A.SMPAY,A.EXP_FRM,A.EXP_TO,A.PAYDWN
				,convert(varchar(8),A.LDATE,112) LDATE,C.CRCOST,convert(varchar(8),A.FDATE,112) FDATE
				,C.PRICE,A.T_NOPAY,A.OPTCST,A.NCARCST,A.NCSHPRC,A.NPRICE,A.NPROFIT
				,A.VKANG,A.TKANG,A.VATPRC,A.PROF_METHOD 
				into #tempREPORT11
			from {$this->MAuth->getdb('ARMAST')} A  
			LEFT OUTER JOIN {$this->MAuth->getdb('CUSTMAST')} B ON A.CUSCOD =B.CUSCOD  
			LEFT OUTER JOIN {$this->MAuth->getdb('INVTRAN')} C ON A.STRNO=C.STRNO AND A.CONTNO=C.CONTNO AND (A.TSALE = C.TSALE)  
			LEFT OUTER JOIN {$this->MAuth->getdb('CHQTRAN')} D ON A.CONTNO=D.CONTNO AND D.LOCATPAY=A.LOCAT AND D.FLAG <> 'C' 
			where 1=1 ".$cond."
			Group By  A.LOCAT,A.CONTNO,A.EFFRT_AFADJ,A.TOTPRC,A.TOTPRES,A.CUSCOD,A.SDATE,A.NDAWN,B.SNAM,B.NAME1,B.NAME2
				,A.TOTDWN,A.NPAYRES,A.NKANG,A.LPAYD,A.BILLCOLL,A.TOT_UPAY,A.EXP_AMT,A.VATDWN,A.SMPAY,A.EXP_FRM,A.EXP_TO
				,A.PAYDWN ,A.LDATE,C.CRCOST,A.FDATE,  C.PRICE,A.T_NOPAY,A.OPTCST,A.NCARCST,A.NCSHPRC,A.NPRICE,A.NPROFIT
				,A.VKANG,A.TKANG,A.VATPRC,A.PROF_METHOD  
			union
			select A.LOCAT,A.CONTNO,A.EFFRT_AFADJ,A.TOTPRC,A.TOTPRES
				,A.CUSCOD,convert(varchar(8),A.SDATE,112) SDATE,A.NDAWN,RTRIM(B.SNAM)+' '+RTRIM(B.NAME1)+'  '+RTRIM(B.NAME2) AS CNAME
				,A.TOTDWN,A.NPAYRES,A.NKANG,convert(varchar(8),A.LPAYD,112) LPAYD,A.BILLCOLL,A.TOT_UPAY,A.EXP_AMT,A.VATDWN
				,A.SMPAY,A.EXP_FRM,A.EXP_TO,A.PAYDWN ,convert(varchar(8),A.LDATE,112) LDATE,C.CRCOST,convert(varchar(8),A.FDATE,112) FDATE
				,C.PRICE,A.T_NOPAY,A.OPTCST,A.NCARCST,A.NCSHPRC,A.NPRICE,A.NPROFIT,A.VKANG,A.TKANG,A.VATPRC,A.PROF_METHOD  
			from {$this->MAuth->getdb('HARMAST')} A  
			LEFT OUTER JOIN {$this->MAuth->getdb('CHGAR_VIEW')} B ON (A.CONTNO = B.CONTNO) AND (A.LOCAT = B.LOCAT)  
			LEFT OUTER JOIN {$this->MAuth->getdb('HINVTRAN')} C ON (A.STRNO = C.STRNO) AND (A.CONTNO = C.CONTNO)  
			LEFT OUTER JOIN {$this->MAuth->getdb('CHQTRAN')} D ON A.CONTNO=D.CONTNO AND D.LOCATPAY=A.LOCAT AND D.FLAG <> 'C' 
			where 1=1 ".$cond."
			Group By  A.LOCAT,A.CONTNO,A.EFFRT_AFADJ,A.TOTPRC,A.TOTPRES,A.CUSCOD,A.SDATE,A.NDAWN,B.SNAM,B.NAME1,B.NAME2
				,A.TOTDWN,A.NPAYRES,A.NKANG,A.LPAYD,A.BILLCOLL,A.TOT_UPAY,A.EXP_AMT,A.VATDWN,A.SMPAY,A.EXP_FRM,A.EXP_TO
				,A.PAYDWN ,A.LDATE,C.CRCOST,A.FDATE,  C.PRICE,A.T_NOPAY,A.OPTCST,A.NCARCST,A.NCSHPRC,A.NPRICE,A.NPROFIT
				,A.VKANG,A.TKANG,A.VATPRC,A.PROF_METHOD
		";
		//echo $sql; 
		$this->db->query($sql);
		$sql = "		
			if OBJECT_ID('tempdb..#tempREPORT11_2') is not null drop table #tempREPORT11_2
			select a.*
				,b.LPAYAMT_N,b.LPAYAMT_V,b.LPROFEFF,b.LPAYAMT
				,c.CPAYAMT_N,c.CPAYAMT_V,c.CPROFEFF,c.CPAYAMT,c.NOFROM,c.NOTO
				into #tempREPORT11_2
			from #tempREPORT11 a
			left join (
				select CONTNO,sum(CASE WHEN ((convert(varchar(8),A.PAYDT,112) < '".$arrs['FPAYDT']."' AND A.PAYDT IS NOT NULL) AND ((A.PAYFOR = '006') OR (A.PAYFOR = '007')))  THEN  ROUND(A.PAYAMT_N,2) ELSE 0 END) AS LPAYAMT_N 
					,  sum(CASE WHEN ((convert(varchar(8),A.PAYDT,112) < '".$arrs['FPAYDT']."' AND A.PAYDT IS NOT NULL) AND ((A.PAYFOR = '006') OR (A.PAYFOR = '007')))  THEN  ROUND(A.PAYAMT_V,2) ELSE 0 END) AS LPAYAMT_V
					,  sum(CASE WHEN ((convert(varchar(8),A.PAYDT,112) < '".$arrs['FPAYDT']."' AND A.PAYDT IS NOT NULL) AND ((A.PAYFOR = '006') OR (A.PAYFOR = '007')))  THEN  ROUND(A.PROFEFF,2) ELSE 0 END) AS LPROFEFF
					,  sum(CASE WHEN ((convert(varchar(8),A.PAYDT,112) < '".$arrs['FPAYDT']."' AND A.PAYDT IS NOT NULL) AND ((A.PAYFOR = '006') OR (A.PAYFOR = '007')))  THEN  ROUND(A.PAYAMT,2) ELSE 0 END) AS LPAYAMT  
				FROM {$this->MAuth->getdb('CHQTRAN')} A 
				WHERE (A.FLAG <>'C') and A.CONTNO in (select CONTNO from #tempREPORT11)
				group by CONTNO
			) b on a.CONTNO=b.CONTNO
			left join (
				select CONTNO,sum(CASE WHEN (((convert(varchar(8),A.PAYDT,112) BETWEEN '".$arrs['FPAYDT']."' AND '".$arrs['TPAYDT']."' ) AND A.PAYDT IS NOT NULL)  AND ((A.PAYFOR = '006') OR (A.PAYFOR = '007'))) 
					THEN  ROUND(A.PAYAMT_N,2) ELSE 0 END) AS CPAYAMT_N 
					,  sum(CASE WHEN (((convert(varchar(8),A.PAYDT,112) BETWEEN '".$arrs['FPAYDT']."' AND '".$arrs['TPAYDT']."') AND A.PAYDT IS NOT NULL)  AND ((A.PAYFOR = '006') OR (A.PAYFOR = '007'))) 
					THEN  ROUND(A.PAYAMT_V,2) ELSE 0 END) AS CPAYAMT_V
					,  sum(CASE WHEN (((convert(varchar(8),A.PAYDT,112) BETWEEN '".$arrs['FPAYDT']."' AND '".$arrs['TPAYDT']."') AND A.PAYDT IS NOT NULL)  AND ((A.PAYFOR = '006') OR (A.PAYFOR = '007'))) 
					THEN  ROUND(A.PROFEFF,2) ELSE 0 END) AS CPROFEFF
					,  sum(CASE WHEN (((convert(varchar(8),A.PAYDT,112) BETWEEN '".$arrs['FPAYDT']."' AND '".$arrs['TPAYDT']."') AND A.PAYDT IS NOT NULL)  AND ((A.PAYFOR = '006') OR (A.PAYFOR = '007'))) 
					THEN  ROUND(A.PAYAMT,2) ELSE 0 END) AS CPAYAMT
					, MIN(F_PAY) AS NOFROM
					, MAX(L_PAY) AS NOTO  
				FROM {$this->MAuth->getdb('CHQTRAN')} A 
				WHERE (A.FLAG <>'C') and A.CONTNO in (select CONTNO from #tempREPORT11)
				group by CONTNO
			) c on a.CONTNO=c.CONTNO
		";	
		//echo $sql; 
		$this->db->query($sql);
		
		$sql = "
			if OBJECT_ID('tempdb..#tempREPORT11_3') is not null drop table #tempREPORT11_3
			select LOCAT,CONTNO,CUSCOD,CNAME,T_NOPAY,NOFROM,NOTO
				,SDATE,FDATE,EFFRT_AFADJ
				,TKANG - (NPROFIT + VKANG) as c1i,NPROFIT as c1ii,VKANG as c1iii
				,TKANG - VKANG as c2ii,TKANG as c2iii
				,LPAYAMT - (LPROFEFF + LPAYAMT_V) as c3i,LPROFEFF as c3ii,LPAYAMT_V as c3iii
				,LPAYAMT_N as c4ii,LPAYAMT as c4iii
				,CPAYAMT - (CPROFEFF + CPAYAMT_V) as c5i,CPROFEFF as c5ii,CPAYAMT_V as c5iii
				,CPAYAMT_N as c6ii,CPAYAMT as c6iii
				,case when EXP_FRM=0 and EXP_TO=0 and LDATE <= '".$arrs['TPAYDT']."' then 0 else (TKANG - (NPROFIT + VKANG)) - (LPAYAMT - (LPROFEFF + LPAYAMT_V)) - (CPAYAMT - (CPROFEFF + CPAYAMT_V)) end as c7i
				,case when EXP_FRM=0 and EXP_TO=0 and LDATE <= '".$arrs['TPAYDT']."' then 0 else NPROFIT - LPROFEFF - CPROFEFF end as c7ii
				,case when EXP_FRM=0 and EXP_TO=0 and LDATE <= '".$arrs['TPAYDT']."' then 0 else VKANG - LPAYAMT_V - CPAYAMT_V end as c7iii
				,case when EXP_FRM=0 and EXP_TO=0 and LDATE <= '".$arrs['TPAYDT']."' then 0 else (TKANG - VKANG) - LPAYAMT_N - CPAYAMT_N end as c8ii
				,case when EXP_FRM=0 and EXP_TO=0 and LDATE <= '".$arrs['TPAYDT']."' then 0 else TKANG - LPAYAMT - CPAYAMT end as c8iii
				into #tempREPORT11_3
			from #tempREPORT11_2
		";
		$this->db->query($sql);
		
		$sql = "
			select LOCAT,CONTNO,CUSCOD,CNAME,T_NOPAY,NOFROM,NOTO,SDATE,FDATE,EFFRT_AFADJ
				,c1i,c1ii,c1iii,c2ii,c2iii,c3i,c3ii,c3iii,c4ii,c4iii
				,c5i,c5ii,c5iii,c6ii,c6iii
				,case when c7i < 0 then 0 else c7i end c7i
				,case when c7i < 0 then 0 else c7ii end c7ii
				,case when c7i < 0 then 0 else c7iii end c7iii
				,case when c7i < 0 then 0 else c8ii end c8ii
				,case when c7i < 0 then 0 else c8iii end c8iii
			from #tempREPORT11_3
			union all 
			
			select 'ฮZZZ' LOCAT,'ฮZZZ' CONTNO,'ฮZZZ' CUSCOD,NULL CNAME,NULL T_NOPAY,NULL NOFROM,NULL NOTO,NULL SDATE,NULL FDATE,NULL EFFRT_AFADJ
				,sum(c1i) c1i,sum(c1ii) c1ii,sum(c1iii) c1iii,sum(c2ii) c2ii,sum(c2iii) c2iii,sum(c3i) c3i,sum(c3ii) c3ii,sum(c3iii) c3iii,sum(c4ii) c4ii,sum(c4iii) c4iii
				,sum(c5i) c5i,sum(c5ii) c5ii,sum(c5iii) c5iii,sum(c6ii) c6ii,sum(c6iii) c6iii
				,sum(case when c7i < 0 then 0 else c7i end) c7i
				,sum(case when c7i < 0 then 0 else c7ii end) c7ii
				,sum(case when c7i < 0 then 0 else c7iii end) c7iii
				,sum(case when c7i < 0 then 0 else c8ii end) c8ii
				,sum(case when c7i < 0 then 0 else c8iii end) c8iii
			from #tempREPORT11_3
			order by ".$arrs['ORDERBY']."
		";
		//echo $sql;  exit;
		$query = $this->db->query($sql);
		
		$html = "";
		$NRow = 1;
		if($query->row()){
			foreach($query->result() as $row){
				if($row->LOCAT == "ฮZZZ"){
					$html .= "
					<tr class='trow' seq=".$NRow.">						
						<th class='getit' seq=".$NRow." colspan='5' style='width:50px;cursor:pointer;text-align:center;vertical-align:middle;'>
							รวม
						</th>
					";
				}else{
					$html .= "
					<tr class='trow' seq=".$NRow.">						
						<td class='getit' seq=".$NRow." style='width:50px;cursor:pointer;text-align:center;vertical-align:middle;'>
							".$NRow."
						</td>
						<td style='vertical-align:middle;'>".$row->LOCAT."</td>
						<td style='vertical-align:middle;'>".$row->CONTNO."</td>
						<td style='vertical-align:middle;'>
							".$row->CUSCOD."<br/>
							".$row->CNAME."<br/>
							จน.งวด ".$row->T_NOPAY."  งวดที่  ".($row->NOFROM == "" ? 0 : $row->NOFROM)."-".($row->NOTO == "" ? 0 : $row->NOTO)."
						</td>
						<td style='vertical-align:middle;'>
							".$this->Convertdate(2,$row->SDATE)."<br/>
							".$this->Convertdate(2,$row->FDATE)."<br/>
							".number_format($row->EFFRT_AFADJ,6)."<br/>
						</td>
					";
				}
				$html .= "
						<td align='right'>
							".number_format($row->c1i,2)."<br/>
							".number_format($row->c1ii,2)."<br/>
							".number_format($row->c1iii,2)."
						</td>
						<td align='right'>
							<br/>
							".number_format($row->c2ii,2)."<br/>
							".number_format($row->c2iii,2)."
						</td>
						<td align='right'>
							".number_format($row->c3i,2)."<br/>
							".number_format($row->c3ii,2)."<br/>
							".number_format($row->c3iii,2)."
						</td>
						<td align='right'>
							<br/>
							".number_format($row->c4ii,2)."<br/>
							".number_format($row->c4iii,2)."
						</td>
						<td align='right'>
							".number_format($row->c5i,2)."<br/>
							".number_format($row->c5ii,2)."<br/>
							".number_format($row->c5iii,2)."
						</td>
						<td align='right'>
							<br/>
							".number_format($row->c6ii,2)."<br/>
							".number_format($row->c6iii,2)."
						</td>
						<td align='right'>
							".number_format($row->c7i,2)."<br/>
							".number_format($row->c7ii,2)."<br/>
							".number_format($row->c7iii,2)."
						</td>
						<td align='right'>
							<br/>
							".number_format($row->c8ii,2)."<br/>
							".number_format($row->c8iii,2)."
						</td>
					</tr>
				";
				
				$NRow++;
			}
		}
		
		$html = "
			<div id='table-fixed-CReport011' class='col-sm-12' style='height:calc(100% - 30px);width:100%;overflow:auto;'>
				<table id='table-CReport011' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='100%' border=1>
					<thead>
						<tr>
							<th style='vertical-align:middle;background-color:#ccc;' rowspan='2'>NO.</th>
							<th style='vertical-align:middle;background-color:#ccc;' rowspan='2'>สาขา</th>
							<th style='vertical-align:middle;background-color:#ccc;' rowspan='2'>เลขที่สัญญา</th>
							<th style='vertical-align:middle;background-color:#ccc;' rowspan='2'>ชื่อ-สกุล</th>
							<th style='vertical-align:middle;background-color:#ccc;max-width:120px;' rowspan='2'>
								<table>
									<tr><th>วันที่ทำสัญญา</th></tr>
									<tr><th>วันดิวงวดแรก</th></tr>
									<tr><th>EFFECTIVE_RATE</th></tr>
								</table>
								
							</th>
							<th style='vertical-align:middle;background-color:#ccc;text-align:center;' colspan='2'>ทั้งสัญญา</th>
							<th style='vertical-align:middle;background-color:#ccc;text-align:center;' colspan='2'>รับชำระเงินถึงงวดก่อน</th>
							<th style='vertical-align:middle;background-color:#ccc;text-align:center;' colspan='2'>รับชำระเงินงวดนี้</th>
							<th style='vertical-align:middle;background-color:#ccc;text-align:center;' colspan='2'>ยอดลูกหนี้คงเหลือ</th>
						</tr>
						<tr>
							<th style='vertical-align:middle;background-color:#ccc;text-align:center;'>เงินต้น<br>ดอกผล<br>ภาษี</th>
							<th style='vertical-align:middle;background-color:#ccc;text-align:center;'>รวม</th>
							<th style='vertical-align:middle;background-color:#ccc;text-align:center;'>เงินต้น<br>ดอกผล<br>ภาษี</th>
							<th style='vertical-align:middle;background-color:#ccc;text-align:center;'>รวม</th>
							<th style='vertical-align:middle;background-color:#ccc;text-align:center;'>เงินต้น<br>ดอกผล<br>ภาษี</th>
							<th style='vertical-align:middle;background-color:#ccc;text-align:center;'>รวม</th>
							<th style='vertical-align:middle;background-color:#ccc;text-align:center;'>เงินต้น<br>ดอกผล<br>ภาษี</th>
							<th style='vertical-align:middle;background-color:#ccc;text-align:center;'>รวม</th>
						</tr>						
					</thead>	
					<tbody>						
						".$html."
					</tbody>
				</table>
			</div>
			<div>
				<img src='".base_url("/public/images/excel.png")."'  onclick=\"tableToExcel('table-CReport011', 'exporttoexcell');\" style='width:25px;height:25px;cursor:pointer;'/>
			</div>
		";
		
		$response = array("html"=>$html,"status"=>true);
		echo json_encode($response);
	}
}




















