<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/********************************************************
             ______@01/03/2019______
            / / _ _   _ _     __ 
           / // __ \ / __ \ / __ \
       _ _/ // /_/ // / / // /_/ /
     /_ _ _/ \_ _ //_/ /_/ \__  /
                          _ _/ /
                         /___ /
********************************************************/
class CReport002 extends MY_Controller {
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
				<div class='col-sm-12' style='overflow:auto;'>					
					<div class='row'>
						<div class='col-xs-2 col-sm-3'>	
							<div class='form-group'>
								รหัสสาขา
								<select id='LOCAT' class='form-control input-sm chosen-select' data-placeholder='รหัสสาขา'>
									<option value='".$this->sess['branch']."'>".$this->sess['branch']."</option>
								</select>
							</div>
						</div>
						<div class='col-xs-2 col-sm-3'>	
							<div class='form-group'>
								กลุ่มสินค้า
								<select id='GCODE' class='form-control input-sm chosen-select' data-placeholder='กลุ่มสินค้า'></select>
							</div>
						</div>
						<div class='col-xs-2 col-sm-3'>	
							<div class='form-group'>
								พนักงานเก็บเงิน
								<select id='BILLCOLL' class='form-control input-sm chosen-select' data-placeholder='พนักงานเก็บเงิน'></select>
							</div>
						</div>
						<div class='col-xs-2 col-sm-3'>	
							<div class='form-group'>
								เลขที่สัญญา
								<input type='text' id='CONTNO' class='form-control input-sm' placeholder='เลขที่สัญญา' >
							</div>
						</div>
					</div>
					<div class='row'>	
						<div class='col-xs-2 col-sm-3'>	
							<div class='form-group'>
								ลูกหนี้ ณ สิ้นเดือน
								<input type='text' id='TPAYDT' class='form-control input-sm' placeholder='ถึงวันที่ชำระ' data-provide='datepicker' data-date-language='th-th' value='".$this->today('endofmonth')."'>
							</div>
						</div>
						<div class='col-xs-2 col-sm-3'>	
							<div class='form-group'>
								เรียงข้อมูลตาม
								<select id='ORDERBY' class='form-control input-sm chosen-select' data-placeholder='เรียงข้อมูลตาม'>
									<option value='CONTNO'selected>เลขที่สัญญา</option>
									<option value='CUSCOD'>รหัสลูกค้า</option>
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
			</div>
		";
		
		$html.= "<script src='".base_url('public/js/SYS12/CReport002.js')."'></script>";
		echo $html;
	}
	
	function search(){
		$arrs = array();
		$arrs['LOCAT'] 	  = $_REQUEST['LOCAT'];
		$arrs['GCODE'] 	  = $_REQUEST['GCODE'];
		$arrs['BILLCOLL'] = $_REQUEST['BILLCOLL'];
		$arrs['CONTNO']   = $_REQUEST['CONTNO'];
		$arrs['TPAYDT']   = $this->Convertdate(1,$_REQUEST['TPAYDT']);		
		$arrs['ORDERBY']  = $_REQUEST['ORDERBY'];
		
		if($arrs['TPAYDT'] == ""){
			$html = "ผิดพลาด โปรดระบุ ลูกหนี้ ณ สิ้นเดือน ด้วยครับ";
			$response = array("html"=>$html,"status"=>false);
			echo json_encode($response); exit;
		}
		
		$sql = "
			declare @date varchar(8) = '".$arrs['TPAYDT']."';
			if OBJECT_ID('tempdb..#tempdata01') is not null drop table #tempdata01;
			select CONTNO,LOCAT,MAX(NOPAY) as NOPAY
				,SUM(N_DAMT - EFFPROF) as DAMT
				,SUM(EFFPROF) as NINSTAL
				,SUM(V_DAMT) as V_DAMT
				,SUM(DAMT) as NPROF
				,SUM(DAMT+V_DAMT) as NPROF2
				into #tempdata01
			from (
				select 	CONTNO,LOCAT,DDATE,NOPAY,N_DAMT,EFFPROF,V_DAMT,DAMT from {$this->MAuth->getdb('ARPAY')}
				union
				select 	CONTNO,LOCAT,DDATE,NOPAY,N_DAMT,EFFPROF,V_DAMT,DAMT from {$this->MAuth->getdb('HARPAY')}
			) as data
			where CONTNO like '".$arrs['CONTNO']."%' and LOCAT like '".$arrs['LOCAT']."%'
			group by CONTNO,LOCAT
		";
		//echo $sql; exit;
		$this->db->query($sql);
			
			/*
				select CONTNO,LOCAT,MAX(NOPAY) as NOPAY
					,SUM(DAMT - EFFPROF) as DAMT
					,SUM(EFFPROF) as NINSTAL
					,SUM(V_DAMT) as V_DAMT
					,SUM(DAMT) as NPROF
					,SUM(N_DAMT) as NPROF2
					into #tempdata02
				from (
					select 	CONTNO,LOCAT,DDATE,NOPAY,N_DAMT,EFFPROF,V_DAMT,DAMT from {$this->MAuth->getdb('ARPAY')}
					union
					select 	CONTNO,LOCAT,DDATE,NOPAY,N_DAMT,EFFPROF,V_DAMT,DAMT from {$this->MAuth->getdb('HARPAY')}
				) as data
				where CONTNO like '".$arrs['CONTNO']."%' and LOCAT like '".$arrs['LOCAT']."%'
					and convert(varchar(6),DDATE,112) < left(@date,6)
				group by CONTNO,LOCAT
				
				
				if OBJECT_ID('tempdb..#tempdata02') is not null drop table #tempdata02;
				select CONTNO,LOCATRECV as LOCAT,sum(PAYAMT - PROFEFF) as DAMT
					,sum(PROFEFF)					as NINSTAL
					,sum(PAYAMT_V)					as V_DAMT
					,sum(PAYAMT)					as NPROF
					,sum(PAYAMT_N)					as NPROF2
					into #tempdata02
				from {$this->MAuth->getdb('CHQTRAN')}
				where CONTNO like '".$arrs['CONTNO']."%' and PAYFOR in ('006','007') and FLAG <>'C' 
					and convert(varchar(6),TMBILDT,112) < left(@date,6)
				group by CONTNO,LOCATRECV
				
				
				select  a.CONTNO
					,sum(b.PAYAMT-b.PROFEFF) as PAYAMTEFF
					,sum(b.PROFEFF)
					,sum(b.PAYAMT)
					,sum(b.PAYAMT_V)
					,sum(b.PAYAMT_N)
				from (
					select CONTNO,MAX(DATE1) as DATE1 from {$this->MAuth->getdb('ARPAY')}
					where CONTNO like 'นHP-1306001%' and convert(varchar(6),DDATE,112) < left(@date,6)
						and convert(varchar(6),DATE1,112) <= left(@date,6)
					group by CONTNO
					union 
					select CONTNO,MAX(DATE1) as DATE1 from {$this->MAuth->getdb('HARPAY')}
					where CONTNO like 'นHP-1306001%' and convert(varchar(6),DDATE,112) < left(@date,6)
						and convert(varchar(6),DATE1,112) <= left(@date,6)
					group by CONTNO
				) as a
				left join {$this->MAuth->getdb('CHQTRAN')} b on a.CONTNO=b.CONTNO and a.DATE1>=b.TMBILDT
				where b.FLAG <> 'C'
				group by a.CONTNO

			if OBJECT_ID('tempdb..#tempdata02') is not null drop table #tempdata02;
			select  a.CONTNO,a.LOCAT
				,sum(b.PAYAMT-b.PROFEFF) as DAMT
				,sum(b.PROFEFF)				as NINSTAL
				,sum(b.PAYAMT_V)			as V_DAMT
				,sum(b.PAYAMT)				as NPROF
				,sum(b.PAYAMT_N)			as NPROF2
				into #tempdata02
			from (
				select CONTNO,LOCAT,MAX(DATE1) as DATE1 from {$this->MAuth->getdb('ARPAY')}
				where CONTNO like '".$arrs['CONTNO']."%' and convert(varchar(6),DDATE,112) < left(@date,6)
					and convert(varchar(6),DATE1,112) <= left(@date,6)
				group by CONTNO,LOCAT
				union 
				select CONTNO,LOCAT,MAX(DATE1) as DATE1 from {$this->MAuth->getdb('HARPAY')}
				where CONTNO like '".$arrs['CONTNO']."%' and convert(varchar(6),DDATE,112) < left(@date,6)
					and convert(varchar(6),DATE1,112) <= left(@date,6)
				group by CONTNO,LOCAT
			) as a
			left join {$this->MAuth->getdb('CHQTRAN')} b on a.CONTNO=b.CONTNO and a.DATE1>=b.TMBILDT
			where b.FLAG <> 'C' and b.PAYFOR in ('006','007')
			group by a.CONTNO,a.LOCAT
			*/
		
		$sql = "
			declare @date varchar(8) = '".$arrs['TPAYDT']."';
			if OBJECT_ID('tempdb..#tempdata02') is not null drop table #tempdata02;
			select CONTNO,LOCAT,sum(DAMT-EFFPROF) as DAMT
				,sum(EFFPROF) 	as NINSTAL
				,sum(V_DAMT) 	as V_DAMT
				,sum(DAMT) 	as NPROF
				,sum(N_DAMT) 	as NPROF2	
				into #tempdata02
			from (
				select 	CONTNO,LOCAT,DDATE,DATE1,NOPAY,N_DAMT,EFFPROF,V_DAMT,DAMT,PAYMENT,V_PAYMENT,N_PAYMENT 
				from {$this->MAuth->getdb('ARPAY')}
				union
				select 	CONTNO,LOCAT,DDATE,DATE1,NOPAY,N_DAMT,EFFPROF,V_DAMT,DAMT,PAYMENT,V_PAYMENT,N_PAYMENT 
				from {$this->MAuth->getdb('HARPAY')}
			) as data
			where CONTNO like '".$arrs['CONTNO']."%' and LOCAT like '".$arrs['LOCAT']."%' 
				and convert(varchar(6),DDATE,112) < left(@date,6)
				and convert(varchar(8),DATE1,112) <= @date
			group by CONTNO,LOCAT
		";
		//echo $sql; exit;
		$this->db->query($sql);
		
		$sql = "
			declare @date varchar(8) = '".$arrs['TPAYDT']."';
			if OBJECT_ID('tempdb..#tempdata03') is not null drop table #tempdata03;
			select CONTNO,LOCAT,(DAMT-EFFPROF) as DAMT
				,(EFFPROF) 	as NINSTAL
				,(V_DAMT) 	as V_DAMT
				,(DAMT) 	as NPROF
				,(N_DAMT) 	as NPROF2	
				into #tempdata03
			from (
				select 	CONTNO,LOCAT,DDATE,DATE1,NOPAY,N_DAMT,EFFPROF,V_DAMT,DAMT,PAYMENT,V_PAYMENT,N_PAYMENT 
				from {$this->MAuth->getdb('ARPAY')}
				union
				select 	CONTNO,LOCAT,DDATE,DATE1,NOPAY,N_DAMT,EFFPROF,V_DAMT,DAMT,PAYMENT,V_PAYMENT,N_PAYMENT 
				from {$this->MAuth->getdb('HARPAY')}
			) as data
			where CONTNO like '".$arrs['CONTNO']."%' and LOCAT like '".$arrs['LOCAT']."%' 
				and convert(varchar(6),DDATE,112) = left(@date,6)
				and convert(varchar(8),DATE1,112) <= @date
		";
		//echo $sql; exit;
		$this->db->query($sql);
		
		/*
		$sql = "
			declare @date varchar(8) = '".$arrs['TPAYDT']."';
			if OBJECT_ID('tempdb..#tempdata04') is not null drop table #tempdata04;
			select CONTNO,LOCAT,MAX(NOPAY) as NOPAY
				,SUM(DAMT - EFFPROF) as DAMT
				,SUM(EFFPROF) 	as NINSTAL
				,SUM(V_DAMT) 	as V_DAMT
				,SUM(DAMT) 		as NPROF
				,SUM(N_DAMT) 	as NPROF2
				into #tempdata04
			from (
				select 	CONTNO,LOCAT,DDATE,NOPAY,N_DAMT,EFFPROF,V_DAMT,DAMT from {$this->MAuth->getdb('ARPAY')}
				union
				select 	CONTNO,LOCAT,DDATE,NOPAY,N_DAMT,EFFPROF,V_DAMT,DAMT from {$this->MAuth->getdb('HARPAY')}
			) as data
			where CONTNO like '".$arrs['CONTNO']."%' and LOCAT like '".$arrs['LOCAT']."%' 
				and convert(varchar(8),DDATE,112) > @date
			group by CONTNO,LOCAT
		";		
		//echo $sql;
		$this->db->query($sql);
		*/
		
		$sql = "
			declare @date varchar(8) = '".$arrs['TPAYDT']."';			
			if OBJECT_ID('tempdb..#tempdata05') is not null drop table #tempdata05;
			select CONTNO,LOCATPAY
				,sum(PAYAMT-PROFEFF) as DAMT
				,sum(PROFEFF) 		 as NINSTAL
				,sum(PAYAMT_V) 		 as PAYAMT_V				
				,sum(PAYAMT_N) 		 as PAYAMT_N
				into #tempdata05
			from {$this->MAuth->getdb('CHQTRAN')}
			where (FLAG <>'C') AND LOCATPAY like '".$arrs['LOCAT']."%' AND CONTNO like '".$arrs['CONTNO']."%'
				and PAYFOR in ('006','007') and convert(varchar(8),PAYDT,112) <= @date
			group by CONTNO,LOCATPAY
		";		
		//echo $sql;
		$this->db->query($sql);	
		
		$sql = "
			declare @date varchar(8) = '".$arrs['TPAYDT']."';
			
			if OBJECT_ID('tempdb..#tempResults') is not null drop table #tempResults;
			select a.LOCAT,a.CONTNO,a.CUSCOD,c.CUSNAME,d.NOPAY
				,(
					select MAX(NOPAY) from (
						select (NOPAY) from {$this->MAuth->getdb('ARPAY')} 
						where LOCAT=a.LOCAT and CONTNO=a.CONTNO and left(@date,6) >= convert(varchar(6),DDATE,112)
						union 
						select (NOPAY) from {$this->MAuth->getdb('HARPAY')} 
						where LOCAT=a.LOCAT and CONTNO=a.CONTNO and left(@date,6) >= convert(varchar(6),DDATE,112)
					) data
				)  as NOPAY2
				,convert(varchar(8),a.SDATE,112) 			as SDATE
				,(
					select DDATE from (
						select convert(varchar(8),DDATE,112) DDATE from {$this->MAuth->getdb('ARPAY')} 
						where LOCAT=a.LOCAT and CONTNO=a.CONTNO and NOPAY=1
						union 
						select convert(varchar(8),DDATE,112) DDATE from {$this->MAuth->getdb('HARPAY')} 
						where LOCAT=a.LOCAT and CONTNO=a.CONTNO and NOPAY=1
					) data
				) as DDATE
				,a.EFFRT_AFADJ
				,isnull(d.DAMT,0) 							as temp1AMT
				,isnull(d.NINSTAL,0)						as temp1NINSTAL	
				,isnull(d.V_DAMT,0)							as temp1VAMT
				,isnull(d.NPROF,0)							as temp1NPROF
				,isnull(d.NPROF2,0)							as temp1total
			/*
				,isnull(e.DAMT,0)-isnull(f.DAMT,0) 			as temp2AMT
				,isnull(e.NINSTAL,0)-isnull(f.NINSTAL,0)	as temp2NINSTAL
				,isnull(e.V_DAMT,0)-isnull(f.V_DAMT,0)		as temp2VAMT
				,isnull(e.NPROF,0)-isnull(f.NPROF,0)		as temp2NPROF
				,isnull(e.NPROF2,0)-isnull(f.NPROF2,0)		as temp2total
			*/
				,isnull(e.DAMT,0)							as temp2AMT
				,isnull(e.NINSTAL,0)						as temp2NINSTAL
				,isnull(e.V_DAMT,0)							as temp2VAMT
				,isnull(e.NPROF,0)							as temp2NPROF
				,isnull(e.NPROF2,0)							as temp2total
				
				,isnull(f.DAMT,0) 							as temp3AMT
				,isnull(f.NINSTAL,0)						as temp3NINSTAL
				,isnull(f.V_DAMT,0)							as temp3VAMT
				,isnull(f.NPROF,0)							as temp3NPROF
				,isnull(f.NPROF2,0)							as temp3total
			/*
				,isnull(g.DAMT,0) 							as temp4AMT
				,isnull(g.NINSTAL,0)						as temp4NINSTAL
				,isnull(g.V_DAMT,0)							as temp4VAMT	
				,isnull(g.NPROF,0)							as temp4NPROF
				,isnull(g.NPROF2,0)							as temp4total
			*/
				,isnull(d.DAMT,0) - isnull(e.DAMT,0) - isnull(f.DAMT,0) 			as temp4AMT
				,isnull(d.NINSTAL,0) - isnull(e.NINSTAL,0) - isnull(f.NINSTAL,0) 	as temp4NINSTAL		
				,isnull(d.V_DAMT,0) - isnull(e.V_DAMT,0) - isnull(f.V_DAMT,0) 		as temp4VAMT	
				,isnull(d.NPROF,0) - isnull(e.NPROF,0) - isnull(f.NPROF,0) 			as temp4NPROF
				,isnull(d.NPROF2,0) - isnull(e.NPROF2,0) - isnull(f.NPROF2,0) 		as temp4total	
				
				
				,isnull(d.DAMT,0)-isnull(h.DAMT,0) 			as temp5AMT
				,isnull(d.NINSTAL,0)-isnull(h.NINSTAL,0)	as temp5NINSTAL
				,isnull(d.V_DAMT,0)-isnull(h.PAYAMT_V,0)	as temp5VAMT
				,isnull(d.NPROF,0)-isnull(h.PAYAMT_N,0)		as temp5NPROF
				,isnull(d.NPROF2,0)-isnull(h.PAYAMT_N,0)	as temp5total
				
				into #tempResults
			from (
				select LOCAT,CONTNO,EFFRT_AFADJ,TOTPRC,TOTPRES,CUSCOD,SDATE,TOTDWN,NPAYRES,NKANG,LPAYD,BILLCOLL
						,TOT_UPAY,EXP_AMT,SMPAY,EXP_FRM,EXP_TO,NDAWN,PAYDWN,LDATE ,FDATE,T_NOPAY,VATDWN,NPROFIT,VKANG
						,TKANG,VATPRC, OPTCST,NCARCST,NCSHPRC,NPRICE,STRNO,TSALE,YDATE,STOPPROF_DT,PROF_METHOD,CLOSDT 
				from {$this->MAuth->getdb('ARMAST')} a
				union 
				select LOCAT,CONTNO,EFFRT_AFADJ,TOTPRC,TOTPRES,CUSCOD,SDATE,TOTDWN,NPAYRES,NKANG,LPAYD,BILLCOLL
						,TOT_UPAY,EXP_AMT,SMPAY,EXP_FRM,EXP_TO,NDAWN,PAYDWN,LDATE ,FDATE,T_NOPAY,VATDWN,NPROFIT,VKANG
						,TKANG,VATPRC, OPTCST,NCARCST,NCSHPRC,NPRICE,STRNO,TSALE,YDATE,STOPPROF_DT,PROF_METHOD,CLOSDT 
				from {$this->MAuth->getdb('HARMAST')} a
				where convert(varchar(8),CLOSDT,112) >= @date
			) as a
			left join (
				SELECT CONTNO,STRNO,TSALE,GCODE,CRCOST,PRICE FROM {$this->MAuth->getdb('INVTRAN')} 
				UNION  
				SELECT CONTNO,STRNO,TSALE,GCODE,CRCOST,PRICE FROM {$this->MAuth->getdb('HINVTRAN')} 
			) as b on a.STRNO=b.STRNO and a.CONTNO=b.CONTNO and a.TSALE=b.TSALE
			left join (
				select CUSCOD,SNAM+NAME1+' '+NAME2 as CUSNAME from {$this->MAuth->getdb('CUSTMAST')} 
			) as c on a.CUSCOD=c.CUSCOD
			left join #tempdata01 as d on a.LOCAT=d.LOCAT and a.CONTNO=d.CONTNO
			left join #tempdata02 as e on a.LOCAT=e.LOCAT and a.CONTNO=e.CONTNO
			left join #tempdata03 as f on a.LOCAT=f.LOCAT and a.CONTNO=f.CONTNO
			--left join #tempdata04 as g on a.LOCAT=g.LOCAT and a.CONTNO=g.CONTNO
			left join #tempdata05 as h on a.LOCAT=h.LOCATPAY and a.CONTNO=h.CONTNO
			where a.LOCAT LIKE '".$arrs['LOCAT']."%' 
				and a.CONTNO LIKE '".$arrs['CONTNO']."%' 
				and (b.GCODE like '".$arrs['GCODE']."%' or b.GCODE is null)
				and a.PROF_METHOD='EFF' 
				and convert(varchar(8),a.SDATE,112) >= (select top 1 convert(varchar(8),STARTDT,112) from {$this->MAuth->getdb('SET_EFFSTART')} order by STARTDT desc)
				and convert(varchar(8),a.SDATE,112) <= @date
				and ((a.TOTPRC > a.SMPAY AND (a.CLOSDT IS NULL OR a.CLOSDT>=(
							SELECT DDATE FROM {$this->MAuth->getdb('HARPAY')}  WHERE CONTNO=a.CONTNO AND LOCAT=a.LOCAT AND convert(varchar(6),DDATE,112) = LEFT(@date,6)
						) or convert(varchar(6),a.sdate,112) = LEFT(@date,6) and a.CLOSDT>=sdate  
					)) OR (a.TOTPRC = a.SMPAY AND (
							SELECT MAX(convert(varchar(8),ct.PAYDT,112)) FROM {$this->MAuth->getdb('CHQTRAN')} ct
							WHERE a.CONTNO=ct.CONTNO AND ct.LOCATPAY=a.LOCAT AND ct.FLAG <> 'C' AND ct.PAYFOR IN ('006','007')  AND ct.PAYAMT>0 
						) >= @date)) 
				and a.TOTPRC > 0
				and a.BILLCOLL LIKE '".$arrs['BILLCOLL']."%' 
				and (a.STOPPROF_DT IS NULL OR convert(varchar(8),a.STOPPROF_DT,112) > @date OR (
						EXISTS(
							SELECT 1 FROM {$this->MAuth->getdb('ARPAY')} 
							WHERE CONTNO=a.CONTNO AND LOCAT=a.LOCAT AND convert(varchar(6),DDATE,112) = LEFT(@date,6) AND a.STOPPROF_DT>DDATE
						) 
					))  
				and ((a.CLOSDT IS NULL)  OR (
						EXISTS(
							SELECT 1 FROM {$this->MAuth->getdb('HARPAY')}  
							WHERE CONTNO=A.CONTNO AND LOCAT=A.LOCAT AND convert(varchar(6),DDATE,112) = LEFT(@date,6) AND a.CLOSDT>=DDATE
						)
					) or convert(varchar(6),a.sdate,112) = LEFT(@date,6) and a.CLOSDT >= sdate)  
			order by a.CONTNO
		";
		//echo $sql;
		$this->db->query($sql);
		
		$sql = "
			select * from (
				select * from #tempResults
				union all
				select 'ZZZZ' as LOCAT,'ZZZZ' as CONTNO,'' as CUSCOD,'' as CUSNAME
					,null as NOPAY,null as NOPAY2,'' as SDATE,'' as DATE,null as EFFRT_AFADJ
					,sum(temp1AMT)		temp1AMT
					,sum(temp1NINSTAL) 	temp1NINSTAL
					,sum(temp1VAMT) 	temp1VAMT
					,sum(temp1NPROF) 	temp1NPROF
					,sum(temp1total) 	temp1total
					,sum(temp2AMT) 		temp2AMT
					,sum(temp2NINSTAL) 	temp2NINSTAL
					,sum(temp2VAMT) 	temp2VAMT
					,sum(temp2NPROF) 	temp2NPROF
					,sum(temp2total) 	temp2total
					,sum(temp3AMT) 		temp3AMT
					,sum(temp3NINSTAL)	temp3NINSTAL
					,sum(temp3VAMT) 	temp3VAMT
					,sum(temp3NPROF) 	temp3NPROF
					,sum(temp3total)	temp3total
					,sum(temp4AMT) 		temp4AMT
					,sum(temp4NINSTAL) 	temp4NINSTAL
					,sum(temp4VAMT)	 	temp4VAMT
					,sum(temp4NPROF) 	temp4NPROF
					,sum(temp4total) 	temp4total
					,sum(temp5AMT) 		temp5AMT
					,sum(temp5NINSTAL) 	temp5NINSTAL
					,sum(temp5VAMT)	 	temp5VAMT
					,sum(temp5NPROF) 	temp5NPROF
					,sum(temp5total) 	temp5total
				from #tempResults
			) as data
			order by LOCAT asc
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		$NRow = 1;
		if($query->row()){
			foreach($query->result() as $row){
				if($row->CONTNO == "ZZZZ" and $row->LOCAT == "ZZZZ"){
					$html .= "<tr><th colspan='5'>รวม</th>";
				}else{
					$html .= "
						<tr>
							<td>".$NRow."</td>
							<td>".$row->LOCAT."</td>
							<td>".$row->CONTNO."</td>
							<td>
								".$row->CUSCOD."<br>
								".$row->CUSNAME."<br>
								จน.งวด ".$row->NOPAY." งวดที่ ".$row->NOPAY2."
							</td>
							<td>
								".$this->Convertdate(2,$row->SDATE)."<br>
								".$this->Convertdate(2,$row->DDATE)."<br>
								".$row->EFFRT_AFADJ."
								
							</td>
					";
				}
				$html .= "
						<td align='right'>
							".number_format($row->temp1AMT,2)."<br>
							".number_format($row->temp1NINSTAL,2)."<br>
							".number_format($row->temp1VAMT,2)."
						</td>
						<td align='right'>
							<br>
							".number_format($row->temp1NPROF,2)."<br>
							".number_format($row->temp1total,2)."
						</td>
						<td align='right'>
							".number_format($row->temp2AMT,2)."<br>
							".number_format($row->temp2NINSTAL,2)."<br>
							".number_format($row->temp2VAMT,2)."
						</td>
						<td align='right'>
							<br>
							".number_format($row->temp2NPROF,2)."<br>
							".number_format($row->temp2total,2)."
						</td>
						<td align='right'>
							".number_format($row->temp3AMT,2)."<br>
							".number_format($row->temp3NINSTAL,2)."<br>
							".number_format($row->temp3VAMT,2)."
						</td>
						<td align='right'>
							<br>
							".number_format($row->temp3NPROF,2)."<br>
							".number_format($row->temp3total,2)."
						</td>
						<td align='right'>
							".number_format($row->temp4AMT,2)."<br>
							".number_format($row->temp4NINSTAL,2)."<br>
							".number_format($row->temp4VAMT,2)."
						</td>
						<td align='right'>
							<br>
							".number_format($row->temp4NPROF,2)."<br>
							".number_format($row->temp4total,2)."
						</td>
						<td align='right'>
							".number_format($row->temp5AMT,2)."<br>
							".number_format($row->temp5NINSTAL,2)."<br>
							".number_format($row->temp5VAMT,2)."
						</td>
						<td align='right'>
							<br>
							".number_format($row->temp5NPROF,2)."<br>
							".number_format($row->temp5total,2)."
						</td>
					</tr>				
				";
				
				$NRow++;
			}
		}
		
		$sql = "select * from {$this->MAuth->getdb('CONDPAY')}";
		$condpay = $this->db->query($sql);
		$condpay = $condpay->row();
		
		$html = "
			<div id='table-fixed-CReport002' class='col-sm-12' style='height:calc(100% - 30px);width:100%;overflow:auto;font-size:8pt;'>
				<table id='table-CReport002' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='100%' border=1>
					<thead>
						<tr style='line-height:20px;'>
							<th style='vertical-align:middle;background-color:#ccc;text-align:center;font-size:14pt;' colspan='15'>
								{$condpay->COMP_NM}
							</th>
						</tr>
						<tr style='line-height:20px;'>
							<th style='vertical-align:middle;background-color:#ccc;text-align:center;font-size:12pt;' colspan='15'>
								รายงานการรับรู้รายได้เกณฑ์สิทธิ์ประจำเดือน {$this->thaiLongMonthArray[(int)substr($arrs['TPAYDT'],4,2)]}  ปี ".((int)substr($arrs['TPAYDT'],0,4) + 543)." แบบ Effective
							</th>
						</tr>
						<tr style='line-height:20px;'>
							<td style='vertical-align:middle;background-color:#ccc;text-align:center;font-size:8pt;' colspan='15'>
								สาขา ".$arrs["LOCAT"]." กลุ่มสินค้า  ".$arrs["GCODE"]." รหัส Billcolector ".$arrs["BILLCOLL"]." เลขที่สัญญา  ".$arrs["CONTNO"]." 
							</td>
						</tr>
						<tr>
							<th style='vertical-align:middle;background-color:#ccc;' rowspan='2'>NO.</th>
							<th style='vertical-align:middle;background-color:#ccc;' rowspan='2'>สาขา</th>
							<th style='vertical-align:middle;background-color:#ccc;' rowspan='2'>เลขที่สัญญา</th>
							<th style='vertical-align:middle;background-color:#ccc;' rowspan='2'>ชื่อ-สกุล</th>
							<th style='vertical-align:middle;background-color:#ccc;max-width:120px;' rowspan='2'>								
								วันที่ทำสัญญา<br>
								วันดิวงวดแรก<br>
								EFFECTIVE_RATE
							</th>
							<th style='vertical-align:middle;background-color:#ccc;text-align:center;' colspan='2'>ทั้งสัญญา</th>
							<th style='vertical-align:middle;background-color:#ccc;text-align:center;' colspan='2'>บันทึกบัญชีถึงงวดก่อน</th>
							<th style='vertical-align:middle;background-color:#ccc;text-align:center;' colspan='2'>บันทึกบัญชีงวดนี้</th>
							<th style='vertical-align:middle;background-color:#ccc;text-align:center;' colspan='2'>ยอดคงเหลือทางบัญชี</th>
							<th style='vertical-align:middle;background-color:#ccc;text-align:center;' colspan='2'>ลูกหนี้คงเหลือจริง</th>
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




















