<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cautotransferscars extends MY_Controller {
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
							สาขารับโอน
							<select id='RVLOCAT' class='form-control input-sm'>
								 <option value='THคน7' selected>THคน7</option>								
							</select>
						</div>
					</div>
					<div class='col-sm-2'>	
						<div class='form-group'>
							วันที่รับรถ
							<input type='text' id='RECVDT' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' placeholder='วันที่รับรถ'>
						</div>
					</div>
					<div class='col-sm-2'>	
						<div class='form-group'>
							เลขที่บิลรับรถ
							<select id='RECVNO' class='form-control input-sm'></select>
						</div>
					</div>
					<div class='col-sm-1 col-sm-offset-5'>	
						<div class='form-group'>
							<br>
							<input type='button' id='btnt1search' class='btn btn-primary btn-sm' value='แสดง' style='width:100%'>
						</div>
					</div>
				</div>
				<div id='resultt1received' style='height:calc(100% - 65px);overflow:auto;background-color:white;'></div>
			</div>		
		";
		
		$html.= "<script src='".base_url('public/js/SYS02/Cautotransferscars.js')."'></script>";
		echo $html;
	}
	
	function search_old(){
		$html='';
		$colhead='';
		$arrs;
		$arrs['RECVNO'] = $_REQUEST['RECVNO'];
		$arrs['RECVDT'] = $this->Convertdate(1,$_REQUEST['RECVDT']);
		$arrs['RVLOCAT'] = $_REQUEST['RVLOCAT'];
		
		$cond='';
		if($arrs['RECVNO'] != ''){
			$cond .= " and RECVNO like '".$arrs['RECVNO']."%'";
		}else{
			$response = array('html'=>'โปรดระบุเลขที่บิลรับรถด้วยครับ','status'=>false);
			echo json_encode($response); exit;
		}
		
		if($arrs['RECVDT'] != ''){
			$cond .= " and convert(varchar(8),RECVDT,112) = '".$arrs['RECVDT']."'";
		}
		
		if($arrs['RVLOCAT'] != ''){
			$cond .= " and RVLOCAT = '".$arrs['RVLOCAT']."'";
		}
		
		$sql = "
			if OBJECT_ID('tempdb..#temp') is not null drop table #temp;

			select MODEL,count(MODEL) r into #temp from {$this->MAuth->getdb('INVTRAN')} 
			where 1=1 ".$cond."
			group by MODEL
		";
		//echo $sql; //exit;
		$this->db->query($sql);
		
		$sql = "
			declare @col varchar(max),@query nvarchar(max);
			select @col=replace(ISNULL(@col,'') + QUOTENAME(MODEL),'][','],[')  from #temp

			set @query = (N'
				select pv.*,std.MaxStock,std.MaxStore,st.r2,(std.MaxStock+std.MaxStore) - st.r empty from (
					select ''ฮSend'' LOCAT,MODEL,cast(r as varchar) r from #temp
					union all
					
					select a.LOCATCD,a.MODELCOD
						,cast(isnull(c.r,0) as varchar)
							+''/''+cast(isnull(d.r,0) as varchar)
							+''/''+cast(isnull(e.r,0) as varchar)
							+''/''+cast(isnull(b.r,0) as varchar)
						as r
					from (
						select LOCATCD,b.MODELCOD from {$this->MAuth->getdb('INVLOCAT')} a
						left join {$this->MAuth->getdb('SETMODEL')} b on 1=1 and b.MODELCOD in (select MODEL from #temp)
						where a.LOCATCD in (
							select c.LOCAT collate Thai_CS_AS from YTKManagement.dbo.std_locatWarehouse a
							left join YTKManagement.dbo.std_locatZone b on a.LOCAT=b.LOCATWH
							left join YTKManagement.dbo.std_locatStock c on b.Prov=c.Prov
							where a.LOCAT=''YLลท1'' and a.WHStatus=''Y''
						)
					) a	
					left join (
						select LOCAT,MODEL,COUNT(MODEL) r from {$this->MAuth->getdb('ARRESV')} 
						where MODEL in (select MODEL from #temp) and STAT=''N'' and ISNULL(SDATE,'''')=''''
						group by LOCAT,MODEL
					) as b on a.LOCATCD=b.LOCAT and a.MODELCOD=b.MODEL
					left join (
						select CRLOCAT,MODEL,count(MODEL) r from {$this->MAuth->getdb('INVTRAN')} 
						where FLAG=''D'' and isnull(SDATE,'''')='''' and isnull(RESVNO,'''')='''' 
							and MODEL in (select MODEL from #temp) 
						group by CRLOCAT,MODEL
					) as c on a.LOCATCD=c.CRLOCAT and a.MODELCOD=c.MODEL
					left join (
						select CRLOCAT,MODEL,count(MODEL) r from {$this->MAuth->getdb('INVTRAN')} 
						where FLAG=''D'' and isnull(SDATE,'''')='''' and isnull(RESVNO,'''')='''' 
							and STAT=''N'' and MODEL in (select MODEL from #temp) 
						group by CRLOCAT,MODEL
					) as d on a.LOCATCD=d.CRLOCAT and a.MODELCOD=d.MODEL
					left join (
						select CRLOCAT,MODEL,count(MODEL) r from {$this->MAuth->getdb('INVTRAN')} 
						where FLAG=''D'' and isnull(SDATE,'''')='''' and isnull(RESVNO,'''')='''' 
							and STAT=''O'' and MODEL in (select MODEL from #temp) 
						group by CRLOCAT,MODEL
					) as e on a.LOCATCD=e.CRLOCAT and a.MODELCOD=e.MODEL
				) as data
				pivot (
					max(r) for MODEL in ('+@col+')
				) as pv
				left join YTKManagement.dbo.std_locatStock std on pv.LOCAT=std.LOCAT collate Thai_CI_AS
				left join (
					select CRLOCAT
						,cast(sum(case when STAT=''N'' then 1 else 0 end) as varchar)
							+''/''+cast(sum(case when STAT=''O'' then 1 else 0 end) as varchar) 
							+''/''+cast(count(STRNO) as varchar) as r2
						,count(STRNO) r
					from {$this->MAuth->getdb('INVTRAN')} 
					where FLAG=''D'' and isnull(SDATE,'''')='''' and isnull(RESVNO,'''')=''''
					group by CRLOCAT
				) as st on pv.LOCAT=st.CRLOCAT
				order by pv.LOCAT
			');

			exec sp_executesql @query;
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$NRow = 1;
		if($query->row()){
			foreach($query->result() as $row){
				if($NRow == 1){
					$colhead.= "<tr>";
					$c = '#388bff';
					foreach($row as $key => $value){
						switch($key){
							case 'LOCAT': $colhead.= "<th rowspan='2' style='vertical-align:middle;text-align:center;'>สาขา</th>";
							break; case 'MaxStock': $colhead.= "<th rowspan='2' style='vertical-align:middle;text-align:center;background-color:#0aff9d'>สต๊อก</th>";
							break; case 'MaxStore': $colhead.= "<th rowspan='2' style='vertical-align:middle;text-align:center;background-color:#85ffce'>คลัง</th>";
							break; case 'r2': $colhead.= "<th rowspan='2' style='vertical-align:middle;text-align:center;background-color:#0aff9d'>รถใน<br>สตีอก</th>";
							break; case 'empty': $colhead.= "<th rowspan='2' style='vertical-align:middle;text-align:center;background-color:#85ffce'>ว่าง</th>";
							break; default:
								$colhead.= "<th colspan='4' style='vertical-align:middle;text-align:center;background-color:".$c."'>".$key."</th>";
								$c = ($c == '#85b8ff' ? '#388bff':'#85b8ff');
							break;
						}
					}
					$colhead.="</tr>";
					$colhead.= "<tr>";
					$c = '#388bff';
					foreach($row as $key => $value){
						switch($key){
							case 'LOCAT':
							break; case 'MaxStock': 
							break; case 'MaxStore': 
							break; case 'r2': 
							break; case 'empty': 
							break; default:
								$colhead.= "
									<th style='vertical-align:middle;text-align:center;background-color:".$c."'>A</th>
									<th style='vertical-align:middle;text-align:center;background-color:".$c."'>N</th>
									<th style='vertical-align:middle;text-align:center;background-color:".$c."'>O</th>
									<th style='vertical-align:middle;text-align:center;background-color:".$c."'>R</th>
								";
								$c = ($c == '#85b8ff' ? '#388bff':'#85b8ff');
							break;
						}
					}
					$colhead.="</tr>";
				}
				
				
				$html.="<tr class='trow' seq=".$NRow.">";
				$c = '#388bff';
				foreach($row as $key => $value){
					switch($key){
						case 'LOCAT': $html .= "<td class='getit' seq=".$NRow++." style='min-width:80px;cursor:pointer;'>".($value=='ฮSend' ? '<b>จน.รถใหม่</b>' : $value)."</td>";
						break; case 'MaxStock': $html .= "<td style='text-align:right;'>".$value."</td>";
						break; case 'MaxStore': $html .= "<td style='text-align:right;'>".$value."</td>";
						break; case 'r2': $html .= "<td style='text-align:right;'>".$value."</td>";
						break; case 'empty': $html .= "<td style='text-align:right;'>".$value."</td>";
						break; case 'empty': $html .= "<td style='text-align:right;'>".$value."</td>";						
						break; default:
							if($row->LOCAT == 'ฮSend'){
								$html .= "<td colspan='4' style='text-align:center;color:black;background-color:".$c.";'><b>".$value."</b></td>";
							}else{
								if($value == ''){
									$html .= "<td colspan='4'></td>";
								}else{
									$ex = explode('/',$value);
									foreach($ex as $val){
										$html .= "<td style='text-align:center;color:".($val == 0 ? 'white':'black').";'>".$val."</td>";
									}
								}
							}
							$c = ($c == '#85b8ff' ? '#388bff':'#85b8ff');
						break;						
					}
				}
				$html.="</tr>";
			}
		}
		
		$html = "
			<div id='table-fixed-Cautotransferscars' class='col-sm-12' style='height:100%;width:100%;overflow:auto;'>
				<table id='table-Cautotransferscars' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='100%'>
					<thead>
						".$colhead."
					</thead>
					<tbody>
						".$html."
					</tbody>
				</table>
			</div>	
		";
		
		$response = array('html'=>$html,'status'=>true);
		echo json_encode($response);
	}
	
	
	function search(){
		$html='';
		$colhead='';
		$arrs;
		$arrs['RECVNO'] = $_REQUEST['RECVNO'];
		$arrs['RECVDT'] = $this->Convertdate(1,$_REQUEST['RECVDT']);
		$arrs['RVLOCAT'] = $_REQUEST['RVLOCAT'];
		
		$cond='';
		if($arrs['RECVNO'] == ''){
			$response = array('html'=>'โปรดระบุเลขที่บิลรับรถด้วยครับ','status'=>false);
			echo json_encode($response); exit;
		}else{
			$cond .= " and RECVNO like '".$arrs['RECVNO']."%'";
		}
		
		if($arrs['RECVDT'] != ''){
			$cond .= " and convert(varchar(8),RECVDT,112) = '".$arrs['RECVDT']."'";
		}
		
		if($arrs['RVLOCAT'] == ''){
			$response = array('html'=>'โปรดระบุสาขารับรถด้วยครับ','status'=>false);
			echo json_encode($response); exit;			
		}else{
			$cond .= " and RVLOCAT = '".$arrs['RVLOCAT']."'";
		}
		
		$declare = "
			declare @locat varchar(20) = '".$arrs['RVLOCAT']."';
			declare @recvno varchar(20) = '".$arrs['RECVNO']."';
		";
		
		$sql = "
			--หาว่ารถใหม่มีรุ่นไหน สีไหน อย่างละกี่คัน
			if OBJECT_ID('tempdb..#tempNEW') is not null drop table #tempNEW
			select MODEL,COLOR,COUNT(MODEL) rnd into #tempNEW from HIINCOME.dbo.INVTRAN
			where RECVNO=@recvno and RVLOCAT=@locat
			group by MODEL,COLOR
			order by MODEL,COUNT(MODEL) desc
		";
		$this->db->query($declare.$sql);
		
		$sql = "
			--สาขา ข้อมูล Max stock
			if OBJECT_ID('tempdb..#tempLOCATST') is not null drop table #tempLOCATST
			select a.LOCATCD,b.MaxStockN,b.MaxStockO,b.MaxStock,b.MaxStore into #tempLOCATST from HIINCOME.dbo.INVLOCAT a
			left join YTKManagement.dbo.std_locatStock b on a.LOCATCD=b.LOCAT collate Thai_CS_AS
			left join YTKManagement.dbo.std_locatZone c on b.Prov=c.Prov
			left join YTKManagement.dbo.std_locatWarehouse d on c.LOCATWH=d.LOCAT
			where d.WHStatus='Y' and c.LOCATWH=@locat
		";
		$this->db->query($declare.$sql);
		
		$sql = "
			--สต๊อกรถปัจจุบัน ของแต่ละสาขา
			if OBJECT_ID('tempdb..#tempStockALL') is not null drop table #tempStockALL
			select a.CRLOCAT
				,sum(case when a.STAT='N' then 1 else 0 end) locatstAllN 
				,sum(case when a.STAT='O' then 1 else 0 end) locatstAllO 
				,COUNT(a.MODEL) locatstAllA
				into #tempStockALL 
			from HIINCOME.dbo.INVTRAN a 
			where ISNULL(a.SDATE,'')='' and ISNULL(a.RESVNO,'')='' and a.FLAG='D'
			group by a.CRLOCAT
		";
		$this->db->query($declare.$sql);
		
		$sql = "
			--สต๊อกรถที่ตรงกับรุ่นที่เข้ามาใหม่
			if OBJECT_ID('tempdb..#tempStockModel') is not null drop table #tempStockModel
			select a.CRLOCAT,a.MODEL
				,sum(case when a.STAT='N' then 1 else 0 end) locatstModelN 
				,sum(case when a.STAT='O' then 1 else 0 end) locatstModelO 
				,COUNT(a.MODEL) locatstModelA
				into #tempStockModel
			from HIINCOME.dbo.INVTRAN a
			where ISNULL(a.SDATE,'')='' and ISNULL(a.RESVNO,'')='' and a.FLAG='D'
				and a.MODEL in (select distinct MODEL from #tempNEW)
			group by a.CRLOCAT,a.MODEL
		";		
		$this->db->query($declare.$sql);
		
		$sql = "
			--สต๊อกรถที่ตรงกับรุ่น และสีที่เข้ามาใหม่
			if OBJECT_ID('tempdb..#tempStock') is not null drop table #tempStock
			select a.CRLOCAT,a.MODEL,a.COLOR
				,sum(case when a.STAT='N' then 1 else 0 end) locatstN 
				,sum(case when a.STAT='O' then 1 else 0 end) locatstO 
				,COUNT(a.MODEL) locatstA
				into #tempStock
			from HIINCOME.dbo.INVTRAN a 
			left join #tempNEW b on a.MODEL=b.MODEL and a.COLOR=b.COLOR
			where ISNULL(a.SDATE,'')='' and ISNULL(a.RESVNO,'')='' and a.FLAG='D'
			group by a.CRLOCAT,a.MODEL,a.COLOR
		";
		$this->db->query($declare.$sql);
		
		$sql = "
			--ยอดจองย้อนหลัง 3 เดือน
			if OBJECT_ID('tempdb..#tempRESV') is not null drop table #tempRESV
			select a.LOCAT,a.MODEL,a.COLOR
				,sum(case when a.STAT='N' then 1 else 0 end) resvn
				,sum(case when a.STAT='O' then 1 else 0 end) resvo
				,COUNT(a.MODEL) resvall
				into #tempRESV
			from HIINCOME.dbo.ARRESV a
			left join #tempNEW b on a.MODEL=b.MODEL
			where ISNULL(a.STRNO,'')='' and ISNULL(SDATE,'')=''
				and convert(varchar(8),RESVDT,112) >= CONVERT(varchar(8),DATEADD(month,-3,getdate()),112)
			group by a.LOCAT,a.MODEL,a.COLOR
		";
		$this->db->query($declare.$sql);
		
		$sql = "
			--ยอดขายตามรุ่น สี รถที่มาใหม่ ของแต่ละสาขา ย้อนหลัง 3 เดือน
			if OBJECT_ID('tempdb..#tempSell') is not null drop table #tempSell
			select a.CRLOCAT,a.MODEL,a.COLOR
				,sum(case when a.STAT='N' then 1 else 0 end) locatsellN 
				,sum(case when a.STAT='O' then 1 else 0 end) locatsellO 
				,COUNT(a.MODEL) locatsellA
				,sum(case when a.TSALE='H' then 1 else 0 end) locatsell_H
				,sum(case when a.TSALE='C' then 1 else 0 end) locatsell_C
				into #tempSell
			from HIINCOME.dbo.INVTRAN a
			left join #tempNEW b on a.MODEL=b.MODEL and a.COLOR=b.COLOR
			where convert(varchar(8),SDATE,112) >= CONVERT(varchar(8),DATEADD(month,-3,getdate()),112)
			group by a.CRLOCAT,a.MODEL,a.COLOR
		";
		$this->db->query($declare.$sql);
		
		$sql = "
			--ยอดการรอรับโอนรถ ตามรุ่น สี รถที่มาใหม่ ของแต่ละสาขา
			if OBJECT_ID('tempdb..#tempTRAN') is not null drop table #tempTRAN
			select a.TRANSTO as LOCAT,c.MODEL,c.COLOR
				,sum(case when c.STAT='N' then 1 else 0 end) transn 
				,sum(case when c.STAT='O' then 1 else 0 end) transo
				,COUNT(c.MODEL) transa
				into #tempTRAN
			from YTKManagement.dbo.INVTransfers a
			left join YTKManagement.dbo.INVTransfersDetails b on a.TRANSNO=b.TRANSNO
			left join HIINCOME.dbo.INVTRAN c on b.STRNO=c.STRNO collate Thai_CS_AS
			left join #tempNEW d on c.MODEL=d.MODEL and c.COLOR=d.COLOR
			where ISNULL(b.MOVENO,'')=''
			group by a.TRANSTO,c.MODEL,c.COLOR
		";
		$this->db->query($declare.$sql);
		
		$sql = "
			--ยอดการรอรับโอนรถทั้งหมด ของแต่ละสาขา
			if OBJECT_ID('tempdb..#tempTRANAll') is not null drop table #tempTRANAll
			select a.TRANSTO as LOCAT
				,sum(case when c.STAT='N' then 1 else 0 end) transAlln 
				,sum(case when c.STAT='O' then 1 else 0 end) transAllo
				,COUNT(c.MODEL) transAlla
				into #tempTRANAll
			from YTKManagement.dbo.INVTransfers a
			left join YTKManagement.dbo.INVTransfersDetails b on a.TRANSNO=b.TRANSNO
			left join HIINCOME.dbo.INVTRAN c on b.STRNO=c.STRNO collate Thai_CS_AS
			where ISNULL(b.MOVENO,'')=''
			group by a.TRANSTO
		";
		$this->db->query($declare.$sql);
		
		$sql = "
			if OBJECT_ID('tempdb..#tempDATA') is not null drop table #tempDATA
			select a.MODEL,a.COLOR,a.rnd
				,b.LOCATCD,isnull(b.MaxStockN,0) MaxStockN,isnull(b.MaxStockO,0) MaxStockO,isnull(b.MaxStock,0) MaxStock,isnull(b.MaxStore,0) MaxStore
				,isnull(c.locatstAllN,0) locatstAllN,isnull(c.locatstAllO,0) locatstAllO,isnull(c.locatstAllA,0) locatstAllA
				,isnull(i.locatstModelN,0) locatstModelN,isnull(i.locatstModelO,0) locatstModelO,isnull(i.locatstModelA,0) locatstModelA
				,isnull(d.locatstN,0) locatstN,isnull(d.locatstO,0) locatstO,isnull(d.locatstA,0) locatstA
				,isnull(e.resvn,0) resvn,isnull(e.resvo,0) resvo,isnull(e.resvall,0) resvall
				,isnull(f.locatsellN,0) locatsellN,isnull(f.locatsellO,0) locatsellO,isnull(f.locatsellA,0) locatsellA,isnull(f.locatsell_H,0) locatsell_H,isnull(f.locatsell_C,0) locatsell_C
				,isnull(g.transn,0) transn,isnull(g.transo,0) transo,isnull(g.transa,0) transa
				,isnull(h.transAlln,0) transAlln,isnull(h.transAllo,0) transAllo,isnull(h.transAlla,0) transAlla
				into #tempDATA
			from #tempNEW a
			left join #tempLOCATST b on 1=1
			left join #tempStockALL c on b.LOCATCD=c.CRLOCAT
			left join #tempStockModel i on b.LOCATCD=i.CRLOCAT and a.MODEL=i.MODEL
			left join #tempStock d on b.LOCATCD=d.CRLOCAT and a.MODEL=d.MODEL and a.COLOR=d.COLOR
			left join #tempRESV e on b.LOCATCD=e.LOCAT and a.MODEL=e.MODEL and a.COLOR=e.COLOR
			left join #tempSell f on b.LOCATCD=f.CRLOCAT and a.MODEL=f.MODEL and a.COLOR=f.COLOR
			left join #tempTRAN g on b.LOCATCD=g.LOCAT collate Thai_CS_AS and a.MODEL=g.MODEL collate Thai_CS_AS and a.COLOR=g.COLOR collate Thai_CS_AS
			left join #tempTRANAll h on b.LOCATCD=h.LOCAT collate Thai_CS_AS 
			order by a.MODEL,a.COLOR,b.LOCATCD
		";
		$this->db->query($sql);
		
		$sql = "select * from #tempNEW";
		$query = $this->db->query($sql);
		
		$html = "";
		$NRow = 0;
		$arrs = array();
		if($query->row()){
			foreach($query->result() as $row){
				//$arrs[$NRow]["MODEL"] = $row->MODEL; //รุ่น
				//$arrs[$NRow]["COLOR"] = $row->COLOR; //สี
				$arrs[$NRow]["rnd"] = $row->rnd; //จำนวนที่รับ
				$arrs[$NRow]["rndSend"] = 0; //จำนวนที่กำหนดส่งให้สาขาแล้ว
				
				$sql= "
					/***ตรวจสอบว่ามีการจองหรือไม่ ถ้ามีให้สอบอีกว่าสาขานั้นมีสต๊อก + รอรับโอน รุ่น สีนี้ มากกว่ายอดจองหรือไม่ ถ้ามีสต๊อก+รอรับโอน > จอง จะไม่สนใจ*/
					select LOCATCD,MODEL,COLOR,(0-((locatstN+transn) - resvn)) as r from #tempDATA
					where MODEL='".$row->MODEL."' and COLOR='".$row->COLOR."' and ((locatstN+transn) - resvn) < 0
					order by ((locatstN+transn) - resvn) asc
					
					/*
					select a.LOCAT,a.MODEL,a.COLOR,(b.locatstN+c.transn) - a.resvn as r  from #tempRESV a 
					left join #tempStock b on a.LOCAT=b.CRLOCAT and a.MODEL=b.MODEL and a.COLOR=b.COLOR
					left join #tempTRAN c on a.LOCAT=c.LOCAT collate Thai_CS_AS and a.MODEL=c.MODEL collate Thai_CS_AS and a.COLOR=c.COLOR collate Thai_CS_AS
					where  a.MODEL='".$row->MODEL."' and a.COLOR='".$row->COLOR."' and ((b.locatstN+c.transn) - a.resvn) < 0
					order by (b.locatstN+c.transn) - a.resvn desc
					*/
				";
				$query2 = $this->db->query($sql);
				
				$sql = "create table #tempCheck (LOCAT varchar(20),MODEL varchar(30),COLOR varchar(30),size int);";
				$this->db->query($sql);
				
				$round = 0;
				if($query2->row()){
					foreach($query2->result() as $row2){
						if($arrs[$NRow]["rnd"] > $arrs[$NRow]["rndSend"]){ // จำนวนที่รับ ได้กำหนดส่งให้สาขาหมดแล้วหรือยัง
							$arrs[$NRow]["DATA"][$round]["LOCAT"] = $row2->LOCATCD;
							$arrs[$NRow]["DATA"][$round]["MODEL"] = $row2->MODEL;
							$arrs[$NRow]["DATA"][$round]["COLOR"] = $row2->COLOR;

							$checklimit = $arrs[$NRow]["rnd"] - ($arrs[$NRow]["rndSend"] + $row2->r);
							$checklimit = ($checklimit > 0 ? 0 : $checklimit);
							$arrs[$NRow]["DATA"][$round]["size"] = ($row2->r + $checklimit); //จำนวนที่จอง ลบสต๊อคและรอรับโอนแล้ว
							
							$sql = "
								insert into #tempCheck
								values ('".$row2->LOCATCD."','".$row2->MODEL."','".$row2->COLOR."',".($row2->r + $checklimit).");
							";
							$this->db->query($sql);							
							
							$arrs[$NRow]["rndSend"] = $arrs[$NRow]["rndSend"] + ($row2->r + $checklimit);
							$round++;
						}
					}
				}
				
				$sql = "
					select LOCATCD,MODEL,COLOR,(MaxStock + MaxStore) - (locatstAllA + transAlla) r ,locatstN + transn st,locatSellN from #tempDATA
					where MODEL='".$row->MODEL."' and COLOR='".$row->COLOR."'
						and locatstN + transn < 3 /***stock + รอรับโอน รุ่น สี ต้องน้อยกว่า 3 ถึงจะจัดรถให้*/
						and (MaxStock + MaxStore) - (locatstAllA + transAlla) > -10 /***พื้นที่จอง จะเกิน มากกว่า 10 คันไม่ได้ */
					order by  (MaxStock + MaxStore) - (locatstAllA + transAlla) desc,locatSellN desc
				";
				//echo $sql; exit;
				$query3 = $this->db->query($sql);
				
				if($query3->row()){
					foreach($query3->result() as $row3){
						if($arrs[$NRow]["rnd"] > $arrs[$NRow]["rndSend"]){ // จำนวนที่รับ ได้กำหนดส่งให้สาขาหมดแล้วหรือยัง
							$arrs[$NRow]["DATA"][$round]["LOCAT"] = $row3->LOCATCD;
							$arrs[$NRow]["DATA"][$round]["MODEL"] = $row3->MODEL;
							$arrs[$NRow]["DATA"][$round]["COLOR"] = $row3->COLOR;
							
							$checklimit = $arrs[$NRow]["rnd"] - ($arrs[$NRow]["rndSend"] + (3 - $row3->st));
							$checklimit = ($checklimit > 0 ? 0 : $checklimit);
							$arrs[$NRow]["DATA"][$round]["size"] = (3 - $row3->st) + $checklimit; //จำนวนที่จอง ลบสต๊อคและรอรับโอนแล้ว
							
							$sql = "
								insert into #tempCheck
								values ('".$row3->LOCATCD."','".$row3->MODEL."','".$row3->COLOR."',".((3 - $row3->st) + $checklimit).");
							";
							$this->db->query($sql);
							
							$arrs[$NRow]["rndSend"] = $arrs[$NRow]["rndSend"] + ((3 - $row3->st) + $checklimit);
							$round++;
						}
					}
				}
				
				$NRow++;
			}
		}
		
		//print_r($arrs); exit;
		$size = sizeof($arrs);
		for($i=0;$i<$size;$i++){
			foreach($arrs[$i] as $key => $val){
				switch($key){
					case 'DATA':
						for($j=0;$j<sizeof($arrs[$i][$key]);$j++){
							if(isset($arrs["check"][$arrs[$i][$key][$j]["LOCAT"]])){
								$s = sizeof($arrs["check"][$arrs[$i][$key][$j]["LOCAT"]]);
								$arrs["check"][$arrs[$i][$key][$j]["LOCAT"]][$s]["MODEL"] = $arrs[$i][$key][$j]["MODEL"];
								$arrs["check"][$arrs[$i][$key][$j]["LOCAT"]][$s]["COLOR"] = $arrs[$i][$key][$j]["COLOR"];
								$arrs["check"][$arrs[$i][$key][$j]["LOCAT"]][$s]["size"] = $arrs[$i][$key][$j]["size"];
							}else{
								$arrs["check"][$arrs[$i][$key][$j]["LOCAT"]][0]["MODEL"] = $arrs[$i][$key][$j]["MODEL"];
								$arrs["check"][$arrs[$i][$key][$j]["LOCAT"]][0]["COLOR"] = $arrs[$i][$key][$j]["COLOR"];
								$arrs["check"][$arrs[$i][$key][$j]["LOCAT"]][0]["size"] = $arrs[$i][$key][$j]["size"];
							}
						}
					break;
				}
			}
		}
		
		//print_r($arrs); exit;
		
		$sql = "select * from #tempCheck order by LOCAT,MODEL,size";
		$query4 = $this->db->query($sql);
		
		$html = "";
		if($query4->row()){
			foreach($query4->result() as $row4){
				$html .= "
					<tr>
						<td>".$row4->LOCAT."</td>
						<td>".$row4->MODEL."</td>
						<td>".$row4->COLOR."</td>
						<td>".$row4->size."</td>
					</tr>
				";
			}
		}
		
		$html = "
			<div id='table-fixed-Cautotransferscars' class='col-sm-12' style='height:100%;width:100%;overflow:auto;'>
				<table id='table-Cautotransferscars' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='100%'>
					<thead></thead>
					<tbody>
						".$html."
					</tbody>
				</table>
			</div>	
		";
		$response = array('html'=>$html,'status'=>true);
		echo json_encode($response);
	}
}




















