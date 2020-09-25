<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/********************************************************
             ______@06/08/2020______
			 Pasakorn Boonded

********************************************************/
class ReportDebtorAsLocat extends MY_Controller {
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
					<br>รายงานลูกหนี้คงเหลือตามสาขา<br>
				</div>
				<div class='col-sm-8 col-xs-8 col-sm-offset-2'>
					<div class='col-sm-6 col-xs-6'>
						<div class='form-group'>
							<div class='col-sm-12'>	
								<div class='form-group'>
									สาขา
									<div class='input-group'>
										<input type='text' id='add_locat' LOCAT='' class='form-control input-sm' placeholder='สาขา' >
										<span class='input-group-btn'>
											<button id='btnaddlocat' class='btn btn-info btn-sm' type='button'>
												<span class='glyphicon glyphicon-hand-up' aria-hidden='true'></span>
											</button>
											<button id='removeadd' class='btn btn-danger btn-sm' type='button'>
												<span class='glyphicon glyphicon-remove' aria-hidden='true'></span>
											</button>
										</span>
									</div>
								</div>
							</div>
							<div class='col-sm-12'>	
								<div class='form-group'>
									รหัสพนักงานเก็บเงิน
									<select id='GCODE' class='form-control input-sm'></select>
								</div>
							</div>						
						</div>
					</div>
					<div class='col-sm-6 col-xs-6'>
						<div class='form-group'>
							<div class='col-sm-12'>	
								<div class='form-group'>
									ลูกหนี้ ณ วันที่
									<input type='text' id='ATDATE' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' value='".$this->today('today')."' styl='font-size:10.5pt;'>
								</div>
							</div>
							<div class='col-sm-12'>	
								<div class='form-group'>
									หมวดสัญญา
									<input type='text' id='CONTNO' class='form-control input-sm'>
								</div>
							</div>					
						</div>
					</div>
				</div>
				<div class='col-sm-8 col-xs-8 col-sm-offset-2'>
					<br>
					<button id='btnreport' type='button' class='btn btn-info btn-outline btn-block' style='height:40px;'><span class='fa fa-folder-open'><b>แสดง</b></span></button>
				</div>
				<div class='col-sm-12'>
					<div class='col-sm-12'>
						<div id='result'></div>
					</div>
				</div>
			</div>
		";
		$html .="<script src='".base_url('public/js/SYS09/ReportDebtorAsLocat.js')."'></script>";
		echo $html;
	}
	function conditiontopdf(){
		$data   = array();
		$data[] = urlencode(
			$_REQUEST["LOCAT"].'||'.$_REQUEST["GCODE"].'||'.
			$_REQUEST["ATDATE"].'||'.$_REQUEST["CONTNO"]
		);
		echo json_encode($this->generateData($data,"encode"));
	}
	function pdf(){
		$data 	= array();
		$data[] = $_GET["condpdf"];
		$arrs 	= $this->generateData($data,"decode");
		$arrs[0]= urldecode($arrs[0]);
		$tx 	= explode("||",$arrs[0]);
		
		$LOCAT	    = $tx[0];
		$GCODE	    = $tx[1];
		$ATDATE	    = $this->Convertdate(1,$tx[2]);
		$CONTNO	    = $tx[3];
		
		$sql = "
			select COMP_NM from {$this->MAuth->getdb('CONDPAY')}
		";
		$query    = $this->db->query($sql);
		$row1	  = $query->row();
		$COMP_NM  = $row1->COMP_NM;
		
		$head = ""; $html = "";
		
		$head = "
			<tr>
				<th width='40px'  align='center' style='border-top:0.1px solid black;vertical-align:top;'>No.</th>
				<th width='60px'  align='left'	 style='border-top:0.1px solid black;vertical-align:top;'>รหัสสาขา</th>
				<th width='100px' align='left'   style='border-top:0.1px solid black;vertical-align:top;'>ชื่อสาขา</th>
				<th width='180px' align='center' style='border-top:0.1px solid black;vertical-align:top;'colspan='3'>ขายสด</th>
				<th width='180px' align='center' style='border-top:0.1px solid black;vertical-align:top;'colspan='3'>ขายส่งไฟแนนซ์</th>
				<th width='180px' align='center' style='border-top:0.1px solid black;vertical-align:top;'colspan='3'>ขายผ่อนเช่าซื้อ</th>
				<th width='180px' align='center' style='border-top:0.1px solid black;vertical-align:top;'colspan='3'>ขายส่งเอเย่นต์</th>
				<th width='180px' align='center' style='border-top:0.1px solid black;vertical-align:top;'colspan='3'>ขายอุปกรณ์เสริม</th>
			</tr>
			<tr>
				<th width='40px'  align='center' style='border-bottom:0.1px solid black;vertical-align:top;'></th>
				<th width='60px'  align='left'	 style='border-bottom:0.1px solid black;vertical-align:top;'></th>
				<th width='100px' align='left'   style='border-bottom:0.1px solid black;vertical-align:top;'></th>
				<th width='90px'  align='right' style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;'>จนสัญญา</th>
				<th width='90px'  align='right' style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;'>รวมราคาขาย</th>
				<th width='90px'  align='right' style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;'>ลูกหนี้คงเหลือ</th>
				<th width='90px'  align='right' style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;'>จนสัญญา</th>
				<th width='90px'  align='right' style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;'>รวมราคาขาย</th>
				<th width='90px'  align='right' style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;'>ลูกหนี้คงเหลือ</th>
				<th width='90px'  align='right' style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;'>จนสัญญา</th>
				<th width='90px'  align='right' style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;'>รวมราคาขาย</th>
				<th width='90px'  align='right' style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;'>ลูกหนี้คงเหลือ</th>
				<th width='90px'  align='right' style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;'>จนสัญญา</th>
				<th width='90px'  align='right' style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;'>รวมราคาขาย</th>
				<th width='90px'  align='right' style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;'>ลูกหนี้คงเหลือ</th>
				<th width='90px'  align='right' style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;'>จนสัญญา</th>
				<th width='90px'  align='right' style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;'>รวมราคาขาย</th>
				<th width='90px'  align='right' style='border-top:0.1px solid black;border-bottom:0.1px solid black;vertical-align:top;'>ลูกหนี้คงเหลือ</th>
			</tr>
		";
		$sql = "
			IF OBJECT_ID('tempdb..#ARCRED') IS NOT NULL DROP TABLE #ARCRED
			select *
			into #ARCRED
			FROM(
				--ขายสด
				select 
					A.LOCAT,A.CONTNO,A.TOTPRC,A.TOTPRES, SUM(B.PAYAMT) as P001  
				from {$this->MAuth->getdb('ARCRED')} A,{$this->MAuth->getdb('CHQTRAN')} B 
				,{$this->MAuth->getdb('INVTRAN')} C  
				where A.CONTNO = B.CONTNO and A.LOCAT = B.LOCATPAY and A.STRNO = C.STRNO and A.SDATE <= '".$ATDATE."' 
				and A.LOCAT like '".$LOCAT."%' and B.PAYFOR = '001' and B.FLAG <> 'C' and B.PAYDT <= '".$ATDATE."' 
				and A.TOTPRC > 0 and (A.TOTPRC > A.SMPAY  OR (A.TOTPRC = A.SMPAY  and A.LPAYDT > '".$ATDATE."')) 
				and C.GCODE like '".$CONTNO."%' group by A.LOCAT,A.LOCAT,A.CONTNO,A.TOTPRC,A.TOTPRES 
				having A.TOTPRC > SUM(B.PAYAMT)+A.TOTPRES  
				union 
				select 
					A.LOCAT,A.CONTNO,A.TOTPRC,A.TOTPRES, SUM(B.PAYAMT) as P001  
				from {$this->MAuth->getdb('HARCRED')} A,{$this->MAuth->getdb('CHQTRAN')} B
				,{$this->MAuth->getdb('HINVTRAN')} C  
				where A.CONTNO = B.CONTNO and A.LOCAT = B.LOCATPAY and A.STRNO = C.STRNO and A.SDATE <= '".$ATDATE."' 
				and A.LOCAT like '".$LOCAT."%' and B.PAYFOR = '001' and B.FLAG <> 'C' and B.PAYDT <= '".$ATDATE."' 
				and A.TOTPRC > 0 and A.LPAYDT > '".$ATDATE."' and C.GCODE like '".$CONTNO."%'    
				group by A.LOCAT,A.LOCAT,A.CONTNO,A.TOTPRC,A.TOTPRES 
				having A.TOTPRC > SUM(B.PAYAMT)+A.TOTPRES  
				union 
				select A.LOCAT,A.CONTNO,A.TOTPRC,A.TOTPRES,0 as P001  
				from {$this->MAuth->getdb('ARCRED')} A,{$this->MAuth->getdb('INVTRAN')} C 
				where  A.STRNO = C.STRNO and A.CONTNO NOT IN ( 
					select CONTNO from {$this->MAuth->getdb('CHQTRAN')} where FLAG <> 'C' and PAYFOR = '001' 
					and PAYDT <= '".$ATDATE."' 
				) and A.TOTPRC > 0 and A.SDATE <= '".$ATDATE."' and  A.LOCAT like '".$LOCAT."%' 
				and C.GCODE like '".$CONTNO."%'  
				group by A.LOCAT,A.CONTNO,A.TOTPRC,A.TOTPRES
			)ARCRED
		";
		//echo $sql;
		$this->db->query($sql);
		
		$sql = "
			IF OBJECT_ID('tempdb..#ARFINC') IS NOT NULL DROP TABLE #ARFINC
			select *
			into #ARFINC
			FROM(
				--ขายส่งไฟแนนซ์
				select 
					A.LOCAT,A.CONTNO,A.TOTPRC,A.TOTPRES, SUM(B.PAYAMT) as P001  
				from {$this->MAuth->getdb('ARFINC')} A,{$this->MAuth->getdb('CHQTRAN')} B
				,{$this->MAuth->getdb('INVTRAN')} C  
				where  A.CONTNO = B.CONTNO and A.LOCAT = B.LOCATPAY and A.STRNO = C.STRNO 
				and  A.SDATE <= '".$ATDATE."' and A.LOCAT like '".$LOCAT."%'  and (B.PAYFOR = '003' OR B.PAYFOR = '004') 
				and B.FLAG <> 'C' and B.PAYDT <= '".$ATDATE."' and A.TOTPRC > 0  and (A.TOTPRC > A.SMPAY  OR (A.TOTPRC = SMPAY  
				and A.LPAYD > '".$ATDATE."')) and C.GCODE like '".$CONTNO."%' 
				group by A.LOCAT,A.CONTNO,A.TOTPRC,A.TOTPRES 
				having A.TOTPRC > SUM(B.PAYAMT)+A.TOTPRES  
				union 
				select 
					A.LOCAT,A.CONTNO,A.TOTPRC,A.TOTPRES, SUM(B.PAYAMT) as P001  
				from {$this->MAuth->getdb('HARFINC')} A,{$this->MAuth->getdb('CHQTRAN')} B,{$this->MAuth->getdb('HINVTRAN')} C  
				where  A.CONTNO = B.CONTNO  and A.LOCAT = B.LOCATPAY and A.STRNO = C.STRNO and A.SDATE <= '".$ATDATE."' 
				and A.LOCAT like '".$LOCAT."%' and (B.PAYFOR = '003' OR B.PAYFOR = '004') and B.FLAG <> 'C' 
				and B.PAYDT <= '".$ATDATE."' and A.TOTPRC > 0 and A.LPAYD > '".$ATDATE."' and C.GCODE like '".$CONTNO."%'  
				group by A.LOCAT,A.CONTNO,A.TOTPRC,A.TOTPRES 
				having A.TOTPRC > SUM(B.PAYAMT)+A.TOTPRES  
				union 
				select 
					A.LOCAT,A.CONTNO,A.TOTPRC,A.TOTPRES,0 as P001 
				from {$this->MAuth->getdb('ARFINC')} A,{$this->MAuth->getdb('INVTRAN')} C 
				where A.STRNO = C.STRNO and A.CONTNO NOT IN ( 
					select CONTNO from {$this->MAuth->getdb('CHQTRAN')} where FLAG <> 'C'  
					and (PAYFOR = '003' OR PAYFOR = '004') 
					and PAYDT <= '".$ATDATE."' 
				) and A.TOTPRC > 0 and A.LOCAT like '".$LOCAT."%' and A.SDATE <= '".$ATDATE."' and C.GCODE like '".$CONTNO."%'  
				group by A.LOCAT,A.CONTNO,A.TOTPRC,A.TOTPRES
			)ARFINC
		";
		//echo $sql;
		$this->db->query($sql);
		
		$sql = "
			IF OBJECT_ID('tempdb..#ARMAST') IS NOT NULL DROP TABLE #ARMAST
			select *
			into #ARMAST
			FROM(
				--ขายผ่อน--
				select 
					A.LOCAT,A.CONTNO,A.TOTPRC,A.TOTPRES
					,(
						select case when sum(payamt) is null then 0 else sum(payamt) end 
						from  {$this->MAuth->getdb('CHQTRAN')} where contno=a.contno and a.locat=locatpay and flag <> 'C' 
						and  payfor in ('002','006','007') and paydt < = '".$ATDATE."'
					) as P001   
				from {$this->MAuth->getdb('ARMAST')} A 
				left join (
					select strno,gcode,contno from {$this->MAuth->getdb('INVTRAN')} 
					union 
					select strno,gcode,contno from {$this->MAuth->getdb('HINVTRAN')}
				) as D ON A.STRNO = D.STRNO and A.CONTNO=D.CONTNO   
				where A.LOCAT like '".$LOCAT."%' and A.SDATE <= '".$ATDATE."' and A.TOTPRC > 0  
				and (A.BILLCOLL like '".$GCODE."%' OR A.BILLCOLL IS NULL) 
				and (d.GCODE like '%' OR D.GCODE IS NULL) and a.contno like '".$CONTNO."%' 
				and totprc-totpres-(
					select case when sum(payamt) is null then 0 else sum(payamt) end 
					from  {$this->MAuth->getdb('CHQTRAN')} where contno = a.contno and a.locat=locatpay and flag<>'C' and 
					payfor in ('002','006','007') and paydt <= '".$ATDATE."' 
				) > 0 group by A.LOCAT,A.CONTNO,A.TOTPRC,A.TOTPRES,a.smpay 

				union                         
				select A.LOCAT,A.CONTNO,A.TOTPRC,A.TOTPRES,0 as P001 from {$this->MAuth->getdb('ARMAST')} A   
				left join (
					select strno,gcode,CONTNO from {$this->MAuth->getdb('INVTRAN')} 
					union 
					select strno,gcode,CONTNO from {$this->MAuth->getdb('HINVTRAN')}
				) as D ON A.STRNO = D.STRNO and A.CONTNO = D.CONTNO   
				where  A.CONTNO NOT IN ( 
					select CONTNO from {$this->MAuth->getdb('CHQTRAN')} where FLAG <> 'C' and (PAYFOR = '002' OR PAYFOR = '006' 
					OR PAYFOR = '007') and PAYDT <= '".$ATDATE."'
				) and A.TOTPRC > 0 and A.SDATE <= '".$ATDATE."' and A.LOCAT like '".$LOCAT."%' 
				and (A.BILLCOLL like '".$GCODE."%' OR A.BILLCOLL IS NULL) 
				and (d.GCODE like '%' OR D.GCODE IS NULL) and a.contno like '".$CONTNO."%' 
				and totprc-totpres-(
					select case when sum(payamt) is null then 0 else sum(payamt) end 
					from {$this->MAuth->getdb('CHQTRAN')} where contno=a.contno and a.locat=locatpay and flag<>'C' 
					and payfor in ('002','006','007') and paydt <= '".$ATDATE."' 
				) > 0 group by A.LOCAT,A.CONTNO,A.TOTPRC,A.TOTPRES 

				union 
				select 
					A.LOCAT,A.CONTNO,A.TOTPRC,A.TOTPRES,(
						select case when sum(payamt) is null then 0 else sum(payamt) end 
						from  {$this->MAuth->getdb('CHQTRAN')} where contno=a.contno and a.locat=locatpay and flag<>'C' and 
						payfor in ('002','006','007') and paydt<= '".$ATDATE."'
					) as P001 
				from {$this->MAuth->getdb('HARMAST')} A 
				left join {$this->MAuth->getdb('CHGAR_VIEW')} B ON A.CONTNO = B.CONTNO and A.LOCAT = B.LOCAT  
				left join (
					select strno,gcode,CONTNO from {$this->MAuth->getdb('INVTRAN')} 
					union 
					select strno,gcode,CONTNO from {$this->MAuth->getdb('HINVTRAN')}
				) as D ON A.STRNO = D.STRNO and A.CONTNO=D.CONTNO 
				where B.DATE1 > '".$ATDATE."' and A.LOCAT like '".$LOCAT."%' and A.TOTPRC > 0 
				and (A.BILLCOLL like '".$GCODE."%' OR A.BILLCOLL IS NULL) 
				and (D.GCODE like '%' OR D.GCODE IS NULL) and A.SDATE <= '".$ATDATE."' and a.contno like '".$CONTNO."%'  
				and totprc - totpres - (
					select case when sum(payamt) is null then 0 else sum(payamt) end from {$this->MAuth->getdb('CHQTRAN')} where contno=a.contno 
					and a.locat=locatpay and flag <> 'C' and  payfor in ('002','006','007') and paydt <= '".$ATDATE."' 
				) > 0 
				group by A.LOCAT,A.CONTNO,A.TOTPRC,A.TOTPRES,a.smpay 

				union   
				select 
					A.LOCAT,A.CONTNO,A.TOTPRC,A.TOTPRES,0 as P001 
				from {$this->MAuth->getdb('HARMAST')} A  
				left join {$this->MAuth->getdb('CHGAR_VIEW')} B ON A.CONTNO = B.CONTNO and A.LOCAT = B.LOCAT  
				left join (
					select strno,gcode,CONTNO from {$this->MAuth->getdb('INVTRAN')} 
					union 
					select strno,gcode,CONTNO from {$this->MAuth->getdb('HINVTRAN')}
				) as C ON A.STRNO = C.STRNO and A.CONTNO = C.CONTNO 
				where A.CONTNO NOT IN ( 
					select CONTNO from {$this->MAuth->getdb('CHQTRAN')} where FLAG <> 'C' and (PAYFOR = '002' OR PAYFOR = '006'  OR PAYFOR = '007')  
					and PAYDT <= '".$ATDATE."' 
				) and A.TOTPRC > 0 and B.DATE1 > '".$ATDATE."' and  A.LOCAT like '".$LOCAT."%' 
				and (A.BILLCOLL like '".$GCODE."%' OR A.BILLCOLL IS NULL) 
				and (C.GCODE like '%' OR C.GCODE IS NULL) and A.SDATE <= '".$ATDATE."' and a.contno like '".$CONTNO."%' 
				and totprc-totpres-(
					select case when sum(payamt) is null then 0 else sum(payamt) end from  {$this->MAuth->getdb('CHQTRAN')} 
					where contno=a.contno and a.locat=locatpay and flag<>'C' and   payfor in ('002','006','007') 
					and paydt<= '".$ATDATE."'
				) > 0 group by A.LOCAT,A.CONTNO,A.TOTPRC,A.TOTPRES 
			)ARMAST
		";
		//echo $sql;
		$this->db->query($sql);
		
		$sql = "
			IF OBJECT_ID('tempdb..#AR_INVOI') IS NOT NULL DROP TABLE #AR_INVOI
			select *
			into #AR_INVOI
			FROM(
				--ขายส่งเอเยนต์
				select 
					A.LOCAT,A.CONTNO,A.TOTPRC, SUM(B.PAYAMT) as P001  
				from {$this->MAuth->getdb('AR_INVOI')} A,{$this->MAuth->getdb('CHQTRAN')} B 
				where A.CONTNO = B.CONTNO and A.LOCAT = B.LOCATPAY and A.SDATE <= '".$ATDATE."' and A.LOCAT like '".$LOCAT."%' 
				and ( B.PAYFOR = '009' ) and B.FLAG <> 'C' and B.PAYDT <= '".$ATDATE."' and A.TOTPRC > 0  
				and (A.TOTPRC > A.SMPAY OR (A.TOTPRC = A.SMPAY and A.LPAYDT > '".$ATDATE."'))  
				group by A.LOCAT,A.CONTNO,A.TOTPRC  
				having A.TOTPRC > SUM(B.PAYAMT) 
				union 
				select 
					A.LOCAT,A.CONTNO,A.TOTPRC, SUM(B.PAYAMT) as P001  
				from {$this->MAuth->getdb('HAR_INVO')} A,{$this->MAuth->getdb('CHQTRAN')} B 
				where   A.CONTNO = B.CONTNO and A.LOCAT = B.LOCATPAY and A.SDATE <= '".$ATDATE."' and 
				A.LOCAT like '".$LOCAT."%' and ( B.PAYFOR = '009' ) and B.FLAG <> 'C' and B.PAYDT <= '".$ATDATE."' and A.TOTPRC > 0  
				and A.LPAYDT > '".$ATDATE."'  
				group by A.LOCAT,A.CONTNO,A.TOTPRC 
				having A.TOTPRC > SUM(B.PAYAMT)  
				union 
				select 
					A.LOCAT,A.CONTNO,A.TOTPRC,0 as P001 
				from {$this->MAuth->getdb('AR_INVOI')} A 
				where A.CONTNO NOT IN ( 
					select CONTNO  from {$this->MAuth->getdb('CHQTRAN')} 
					where FLAG <> 'C' and (PAYFOR = '009' ) and PAYDT <= '".$ATDATE."' 
				) and A.TOTPRC > 0  and A.SDATE <= '".$ATDATE."' and  A.LOCAT like '".$LOCAT."%'  
				group by A.LOCAT,A.CONTNO,A.TOTPRC
			)AR_INVOI
		";
		//echo $sql; 
		$this->db->query($sql);
		
		$sql = "
			IF OBJECT_ID('tempdb..#AROPTMST') IS NOT NULL DROP TABLE #AROPTMST
			select *
			into #AROPTMST
			FROM(
				--ขายอุปกรณ์เสริม
				select 
					A.LOCAT,A.CONTNO,A.OPTPTOT as TOTPRC, SUM(B.PAYAMT) as P001 
				from {$this->MAuth->getdb('AROPTMST')} A,{$this->MAuth->getdb('CHQTRAN')} B  
				where A.CONTNO = B.CONTNO and A.SDATE <= '".$ATDATE."' and A.LOCAT like '".$LOCAT."%'  
				and (B.PAYFOR = '005') and B.FLAG <> 'C' and B.PAYDT <= '".$ATDATE."' and A.OPTPTOT > 0 
				and (A.OPTPTOT > A.SMPAY OR (A.OPTPTOT = SMPAY  and A.LPAYDT > '".$ATDATE."'))  
				group by A.LOCAT,A.CONTNO,A.OPTPTOT 
				having A.OPTPTOT > SUM(B.PAYAMT)  
				union 
				select 
					A.LOCAT,A.CONTNO,A.OPTPTOT as TOTPRC, SUM(B.PAYAMT) as P001  
				from {$this->MAuth->getdb('HAROPMST')} A,{$this->MAuth->getdb('CHQTRAN')} B  
				where A.CONTNO = B.CONTNO and A.SDATE <= '".$ATDATE."' and A.LOCAT like '".$LOCAT."%' 
				and (B.PAYFOR = '005' ) and B.FLAG <> 'C' and B.PAYDT <= '".$ATDATE."' and A.OPTPTOT > 0 
				and A.LPAYDT > '".$ATDATE."' group by A.LOCAT,A.CONTNO,A.OPTPTOT 
				having A.OPTPTOT > SUM(B.PAYAMT)  
				union 
				select 
					LOCAT,CONTNO,OPTPTOT as TOTPRC ,0 as P001  
				from {$this->MAuth->getdb('AROPTMST')} 
				where CONTNO NOT IN ( 
					select CONTNO from {$this->MAuth->getdb('CHQTRAN')} where FLAG <> 'C' and (PAYFOR = '005') 
					and PAYDT <= '".$ATDATE."'
				) and OPTPTOT > 0  and SDATE <= '".$ATDATE."' and  LOCAT like '".$LOCAT."%'  
				group by LOCAT,CONTNO,OPTPTOT
			)AROPTMST
		";
		//echo $sql;
		$this->db->query($sql);
		
		$sql = "
			select LOCATCD,LOCATNM 
			from {$this->MAuth->getdb('INVLOCAT')} 
			where LOCATCD like '".$LOCAT."%' and SHORTL like '".$CONTNO."%' 
			ORDER BY LOCATCD
		";
		
		$query = $this->db->query($sql);
		$i = 0; 
		if($query->row()){
			foreach($query->result() as $row){
				$sql2 = "
					select 
						--ขายสด
						 ISNULL((select COUNT(CONTNO) from #ARCRED where LOCAT = '".$row->LOCATCD."'),0) ARC1 
						,ISNULL((select SUM(TOTPRC) from #ARCRED where LOCAT = '".$row->LOCATCD."'),0) ARC2
						,ISNULL((select SUM(TOTPRC) - (SUM(TOTPRES) + SUM(P001)) from #ARCRED where LOCAT = '".$row->LOCATCD."'),0) ARC3
						--ขายส่งไฟแนนซ์
						,ISNULL((select COUNT(CONTNO) from #ARFINC where LOCAT = '".$row->LOCATCD."'),0) ARF1 
						,ISNULL((select SUM(TOTPRC)from #ARFINC where LOCAT = '".$row->LOCATCD."'),0) ARF2
						,ISNULL((select SUM(TOTPRC) - (SUM(TOTPRES) + SUM(P001)) from #ARFINC where LOCAT = '".$row->LOCATCD."'),0) ARF3
						--ขายผ่อน
						,ISNULL((select COUNT(CONTNO) from #ARMAST where LOCAT = '".$row->LOCATCD."'),0) ARM1 
						,ISNULL((select SUM(TOTPRC) from #ARMAST where LOCAT = '".$row->LOCATCD."'),0) ARM2
						,ISNULL((select SUM(TOTPRC) - (SUM(TOTPRES) + SUM(P001)) from #ARMAST where LOCAT = '".$row->LOCATCD."'),0) ARM3
						--ขายส่งเอเยนต์
						,ISNULL((select COUNT(CONTNO) from #AR_INVOI where LOCAT = '".$row->LOCATCD."'),0) AR_I1
						,ISNULL((select SUM(TOTPRC) from #AR_INVOI where LOCAT = '".$row->LOCATCD."'),0) AR_I2
						,ISNULL((select SUM(TOTPRC) - sum(P001) from #AR_INVOI where LOCAT = '".$row->LOCATCD."'),0) AR_I3
						--ขายอุปกรณ์เสริม
						,ISNULL((select COUNT(CONTNO) from #AROPTMST where LOCAT = '".$row->LOCATCD."'),0) ARO1 
						,ISNULL((select SUM(TOTPRC) from #AROPTMST where LOCAT = '".$row->LOCATCD."'),0) ARO2
						,ISNULL((select SUM(TOTPRC) - sum(P001) from #AROPTMST where LOCAT = '".$row->LOCATCD."'),0) ARO3
				";
				//echo $sql2; exit;
				$query2 = $this->db->query($sql2);
				if($query2->row()){
					foreach($query2->result() as $row2){
						$arrs['ARC1'][]  = $row2->ARC1;
						$arrs['ARC2'][]  = $row2->ARC2;
						$arrs['ARC3'][]  = $row2->ARC3;
						
						$arrs['ARF1'][]  = $row2->ARF1;
						$arrs['ARF2'][]  = $row2->ARF2;
						$arrs['ARF3'][]  = $row2->ARF3;
						
						$arrs['ARM1'][]  = $row2->ARM1;
						$arrs['ARM2'][]  = $row2->ARM2;
						$arrs['ARM3'][]  = $row2->ARM3;
						
						$arrs['AR_I1'][] = $row2->AR_I1;
						$arrs['AR_I2'][] = $row2->AR_I2;
						$arrs['AR_I3'][] = $row2->AR_I3;
						
						$arrs['ARO1'][]  = $row2->ARO1;
						$arrs['ARO2'][]  = $row2->ARO2;
						$arrs['ARO3'][]  = $row2->ARO3;
						if($row2->ARM1 > 0){$i++;
							$html .="
								<tr class='trow'>
									<td width='40px'  height='40px'	align='center'  >".$i."</td>
									<td width='60px'  height='40px'	align='left'  >".$row->LOCATCD."</td>
									<td width='100px' height='40px'	align='left'  >".$row->LOCATNM."</td>
									<td width='90px'  height='40px'	align='right'  >".$row2->ARC1."</td>
									<td width='90px'  height='40px'	align='right'  >".number_format($row2->ARC2,2)."</td>
									<td width='90px'  height='40px'	align='right'  >".number_format($row2->ARC3,2)."</td>
									<td width='90px'  height='40px'	align='right'  >".$row2->ARF1."</td>
									<td width='90px'  height='40px'	align='right'  >".number_format($row2->ARF2,2)."</td>
									<td width='90px'  height='40px'	align='right'  >".number_format($row2->ARF3,2)."</td>
									<td width='90px'  height='40px'	align='right'  >".$row2->ARM1."</td>
									<td width='90px'  height='40px'	align='right'  >".number_format($row2->ARM2,2)."</td>
									<td width='90px'  height='40px'	align='right'  >".number_format($row2->ARM3,2)."</td>
									<td width='90px'  height='40px'	align='right'  >".$row2->AR_I1."</td>
									<td width='90px'  height='40px'	align='right'  >".number_format($row2->AR_I2,2)."</td>
									<td width='90px'  height='40px'	align='right'  >".number_format($row2->AR_I3,2)."</td>
									<td width='90px'  height='40px'	align='right'  >".$row2->ARO1."</td>
									<td width='90px'  height='40px'	align='right'  >".number_format($row2->ARO2,2)."</td>
									<td width='90px'  height='40px'	align='right'  >".number_format($row2->ARO3,2)."</td>
								</tr>
							";	
						}
					}
				}
			}
		}
		//echo $i; exit;
		if($i > 0){
			$html .="
				<tr class='trow' style='background-color:#ebebeb;'>
					<td width='40px'  height='40px'	align='center' style='border-top:0.1px solid black;vertical-align:top;border-bottom:0.1px solid black;' colspan='3'>รวมทั้งสิ้น</td>
					<td width='90px'  height='40px'	align='right'  style='border-top:0.1px solid black;vertical-align:top;border-bottom:0.1px solid black;' >".array_sum($arrs["ARC1"])."</td>
					<td width='90px'  height='40px'	align='right'  style='border-top:0.1px solid black;vertical-align:top;border-bottom:0.1px solid black;' >".number_format(array_sum($arrs["ARC2"]),2)."</td>
					<td width='90px'  height='40px'	align='right'  style='border-top:0.1px solid black;vertical-align:top;border-bottom:0.1px solid black;' >".number_format(array_sum($arrs["ARC3"]),2)."</td>
					<td width='90px'  height='40px'	align='right'  style='border-top:0.1px solid black;vertical-align:top;border-bottom:0.1px solid black;' >".array_sum($arrs["ARF1"])."</td>
					<td width='90px'  height='40px'	align='right'  style='border-top:0.1px solid black;vertical-align:top;border-bottom:0.1px solid black;' >".number_format(array_sum($arrs["ARF2"]),2)."</td>
					<td width='90px'  height='40px'	align='right'  style='border-top:0.1px solid black;vertical-align:top;border-bottom:0.1px solid black;' >".number_format(array_sum($arrs["ARF3"]),2)."</td>
					<td width='90px'  height='40px'	align='right'  style='border-top:0.1px solid black;vertical-align:top;border-bottom:0.1px solid black;' >".array_sum($arrs["ARM1"])."</td>
					<td width='90px'  height='40px'	align='right'  style='border-top:0.1px solid black;vertical-align:top;border-bottom:0.1px solid black;' >".number_format(array_sum($arrs["ARM2"]),2)."</td>
					<td width='90px'  height='40px'	align='right'  style='border-top:0.1px solid black;vertical-align:top;border-bottom:0.1px solid black;' >".number_format(array_sum($arrs["ARM3"]),2)."</td>
					<td width='90px'  height='40px'	align='right'  style='border-top:0.1px solid black;vertical-align:top;border-bottom:0.1px solid black;' >".array_sum($arrs["AR_I1"])."</td>
					<td width='90px'  height='40px'	align='right'  style='border-top:0.1px solid black;vertical-align:top;border-bottom:0.1px solid black;' >".number_format(array_sum($arrs["AR_I2"]),2)."</td>
					<td width='90px'  height='40px'	align='right'  style='border-top:0.1px solid black;vertical-align:top;border-bottom:0.1px solid black;' >".number_format(array_sum($arrs["AR_I3"]),2)."</td>
					<td width='90px'  height='40px'	align='right'  style='border-top:0.1px solid black;vertical-align:top;border-bottom:0.1px solid black;' >".array_sum($arrs["ARO1"])."</td>
					<td width='90px'  height='40px'	align='right'  style='border-top:0.1px solid black;vertical-align:top;border-bottom:0.1px solid black;' >".number_format(array_sum($arrs["ARO2"]),2)."</td>
					<td width='90px'  height='40px'	align='right'  style='border-top:0.1px solid black;vertical-align:top;border-bottom:0.1px solid black;' >".number_format(array_sum($arrs["ARO3"]),2)."</td>
				</tr>
			";
		}
		
		$body = "<table class='fs9' cellspacing='0'>".$html."</table>";
		$mpdf = new \Mpdf\Mpdf([
			'mode' => 'utf-8', 
			'format' => 'A4-L',
			'margin_top' => 40, 	//default = 16
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
						<th colspan='18' style='font-size:11pt;' align='center'>".$COMP_NM."<br>รายงานสรุปลูกหนี้คงเหลือ</th>
					</tr>
					<tr>
						<th colspan='18' style='font-size:11pt;' align='center'>
							สาขา &nbsp;&nbsp;".$LOCAT."&nbsp;&nbsp;
							ณ วันที่ &nbsp;&nbsp;".$this->Convertdate(2,$ATDATE)."&nbsp;&nbsp;
							พนักงานเก็บเงิน &nbsp;&nbsp;".$LOCATPAY."&nbsp;&nbsp;
							เลขที่สัญญา &nbsp;&nbsp;".$CONTNO."&nbsp;&nbsp;
						</th>
					</tr>
					<tr>
						<th colspan='18' style='font-size:11pt;' align='right'>
							RpAsA40,41
						</th>
					</tr>
					<tr>
						<td colspan='2' align='left'>วันที่พิมพ์รายงาน</td>
						<td colspan='5' align='left'>".date('d/m/').(date('Y')+543)." ".date('H:i')."</td>
						<td colspan='11' align='right'>หน้าที่ : {PAGENO} / {nb} &emsp;&emsp;</td>
					</tr>
					".$head."
				</table>
			";	
		}else{
			$header = "<span style='color:red;font-size:16pt;'>ไม่พบข้อมูลตามเงื่อนไขครับ</span>";
		}	
		$mpdf->SetHTMLHeader($header);
		$mpdf->WriteHTML($body.$stylesheet);
		$mpdf->fontdata['qanela'] = array('R' => "QanelasSoft-Regular.ttf",'B' => "QanelasSoft-Bold.ttf",); //แก้ปริ้นแล้วอ่านไม่ออก
		$mpdf->Output();
	}
}