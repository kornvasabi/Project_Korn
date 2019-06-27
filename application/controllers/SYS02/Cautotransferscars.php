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
				<div id='demo-wizard2' class='wizard-wrapper'>    
					<div class='wizard'>
						<form id='demo-form2' action='' class='lobi-form' novalidate='novalidate'>
							<ul class='wizard-tabs wizard-tab-balls nav-justified nav nav-pills'>
								<li class='active'>
									<a href='#tab11' prev='#' data-toggle='tab' aria-expanded='true'>
										<span class='step'>1</span>
										<span class='title'>การจัดรถ</span>
									</a>
								</li>
								<li>
									<a href='#tab22' prev='#tab11' data-toggle='tab'>
										<span class='step'>2</span>
										<span class='title'>ตรวจสอบ/ยืนยัน</span>
									</a>
								</li>
								<li>
									<a href='#tab33' prev='#tab22' data-toggle='tab'>
										<span class='step'>3</span>
										<span class='title'>แสดงผล</span>
									</a>
								</li>
							</ul>
							<div class='tab-content bg-white'>
								<div class='tab-pane active' name='tab11' style='height:calc(100vh - 260px);overflow:auto;'>
									<fieldset>
										<div class='col-sm-8'>
											<div style='border:0.1px dotted red;background-color:white;height:calc(100vh - 320px);'>
												<div id='table-fixed-choose' class='col-sm-12' style='height:100%;width:100%;overflow:auto;'>
													<table id='table-choose' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' style='width:100%;background-color:white;'>
														<thead>
															<tr>
																<th style='width:100px;'><button id='addSTRNO' LOCAT='' class='btn btn-sm btn-primary btn-block'>เพิ่มรถ</button></th>
																<th>เลขตัวถัง</th>
																<th>รุ่น</th>
																<th>แบบ</th>
																<th>สี</th>
																<th>สถานะ</th>
																<th>สาขา</th>
															</tr>
														</thead>
														<tbody>
															
														</tbody>
													</table>
												</div>
											</div>
										</div>
										<div class='col-sm-4'>	
											<div style='border:0.1px dotted red;height:calc(100vh - 320px);overflow:auto;'>
												<table class='col-sm-12' >
													<tr>
														<th colspan='2' style='font-size:12pt;text-align:center;'>เงื่อนไข<hr></th>
													</tr>
													<tr>
														<th style='text-align:center;'>จังหวัด</th>
														<td>
															<div class='col-sm-12'>
																<div class='col-sm-6'>
																	<input class='form-check-input' style='cursor:pointer;max-width:20px;max-height:10px;' type='checkbox' id='tab11prov1' value='ตรัง'  >
																	<label class='form-check-label' style='cursor:pointer;' for='tab11prov1'>ตรัง</label>
																</div>
																<div class='col-sm-6'>
																	<input class='form-check-input' style='cursor:pointer;max-width:20px;max-height:10px;' type='checkbox' id='tab11prov2' value='สุราษฎร์ธานี' >
																	<label class='form-check-label' style='cursor:pointer;' for='tab11prov2'>สุราษฎร์ธานี</label>
																</div>
																<div class='col-sm-6'>
																	<input class='form-check-input' style='cursor:pointer;max-width:20px;max-height:10px;' type='checkbox' id='tab11prov3' value='ชุมพร' >
																	<label class='form-check-label' style='cursor:pointer;' for='tab11prov3'>ชุมพร</label>																			
																</div>
																<div class='col-sm-6'>
																	<input class='form-check-input' style='cursor:pointer;max-width:20px;max-height:10px;' type='checkbox' id='tab11prov4' value='กระบี่' >
																	<label class='form-check-label' style='cursor:pointer;' for='tab11prov4'>กระบี่</label>
																</div>
																<div class='col-sm-6'>
																	<input class='form-check-input' style='cursor:pointer;max-width:20px;max-height:10px;' type='checkbox' id='tab11prov5' value='พังงา' >
																	<label class='form-check-label' style='cursor:pointer;' for='tab11prov5'>พังงา</label>
																</div>																
															</div>
														</td>
													</tr>
													<!-- tr>
														<th style='text-align:center;'>วิธีคำนวณ</th>
														<td>
															<div class='col-sm-12'>
																<div class='col-sm-6'>
																	<label class='radio lobiradio lobiradio-info'>
																		<input type='radio' name='ship_info' value='1' checked> 
																		<i></i> กระจายสาขา
																	</label>
																</div>
																<div class='col-sm-6'>
																	<label class='radio lobiradio lobiradio-info'>
																		<input type='radio' name='ship_info' value='2'> 
																		<i></i> ลงสาขาสูงสุด
																	</label>
																</div>
															</div>
														</td>
													</tr -->
													<tr>
														<th class='col-sm-3' style='text-align:center;'>พท.ว่าง <br>ที่จะจัดรถ</th>
														<td>
															<div class='col-sm-6'>
																<input type='number' id='condStockEmpty' class='input input-sm' min='-20' max='20' value='0'>
															</div>
														</td>
													</tr>
													<tr>
														<th class='col-sm-3' style='text-align:center;' title='จำนวนรถสูงสุดที่จะสต๊อกสำหรับสาขา ตามรุ่น สี'>limit</th>
														<td>
															<div class='col-sm-6'>
																<input type='number' id='condMaxLimit' class='input input-sm' min='1' max='20' value='1'>
															</div>
														</td>
													</tr>
												</table>
												
											</div>
										</div>
																	
										<div class='col-sm-12'>
											<div class='row'>
												<div class='col-sm-2 col-sm-offset-8'>
													<button id='tab11Clear' name='tab11' class='btn btn-sm btn-default btn-block'>เคลียร์</button>
												</div>
												<div class='col-sm-2 '>													
													<button id='tab11processCar' name='tab11' class='btn btn-sm btn-primary btn-block'>ถัดไป</button>
												</div>
											</div>
										</div>
									</fieldset>
								</div>
								<div class='tab-pane' name='tab22' style='height:calc(100vh - 260px);overflow:auto;'>
									<fieldset>
										<div class='col-sm-12' id='tab22Body' style='height:calc(100vh - 320px);background-color:#fff;'></div>
										<div class='col-sm-12'>
											<div class='row'>
												<div class='col-sm-2'>
													<button id='tab22Back' name='tab22' class='btn btn-sm btn-danger btn-block'>ย้อนกลับ</button>
												</div>
												<div class='col-sm-2 col-sm-offset-8'>
													<button id='tab22processCar' name='tab22' class='btn btn-sm btn-primary btn-block'>ถัดไป</button>
												</div>
											</div>
										</div>
									</fieldset>
								</div>
								<div class='tab-pane' name='tab33' style='height:calc(100vh - 260px);overflow:auto;'>
									<fieldset>
										<div class='col-sm-12' id='tab33Body' style='height:calc(100vh - 320px);background-color:#fff;'></div>
										<div class='col-sm-12'>
											<div class='row'>
												<div class='col-sm-2'>
													<!-- button id='tab33Back' name='tab33' class='btn btn-sm btn-danger btn-block'>ย้อนกลับ</button -->
												</div>
												<div class='col-sm-2 col-sm-offset-8'>
													<button id='tab33processCar' name='tab33' class='btn btn-sm btn-primary btn-block'>ทำรายการเพิ่มเติม</button>
												</div>
											</div>
										</div>
									</fieldset>
								</div>
								
								<!-- ul class='pager'>
									<li class='previous first disabled' style='display:none;'><a href='javascript:void(0)'>First</a></li>
									<li class='previous disabled'><a href='javascript:void(0)'>ย้อนกลับ</a></li>
									<li class='next last' style='display:none;'><a href='javascript:void(0)'>Last</a></li>
									<li class='next'><a href='javascript:void(0)'>ถัดไป</a></li>
								</ul -->
							</div>
						</form>
					</div>
				</div>
			</div>
			
			<script>
				$('.select2-demo').select2();
				$('.panel').lobiPanel({					
					reload: false,
					close: false,
					isOnFullScreen: false,
					unpin: false,
					minimize: false,
					editTitle: false
				});
			</script>
		";
		
		$html.= "<script src='".base_url('public/js/SYS02/Cautotransferscars2.js')."'></script>";
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
	
	
	function search_old2(){
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
	
	function getFormSTRNO(){
		$LOCAT = $_REQUEST["LOCAT"];
		
		$html = "
			<div style='background-color:#ccc;height:calc(100vh - 120px);'>
				<div style='height:150px;overflow:auto;'>
					<div class='row col-sm-12'>
						<div class='col-sm-3'>	
							<div class='form-group'>
								สาขา
								<select id='t1LOCAT' class='form-control input-sm' ".($LOCAT == "" ? "":"disabled").">
									<option value='".($LOCAT == "" ? $this->sess["branch"]:$LOCAT)."'>".($LOCAT == "" ? $this->sess["branch"]:$LOCAT)."</option>
								</select>
							</div>
						</div>
						<div class='col-sm-3'>	
							<div class='form-group'>
								เลขที่บิลรับรถ
								<select id='t1RECVNO' class='form-control input-sm'></select>
							</div>
						</div>
						<div class='col-sm-3'>	
							<div class='form-group'>
								รุ่นรถ
								<select id='t1MODEL' class='form-control input-sm'></select>
							</div>
						</div>
						<div class='col-sm-3'>	
							<div class='form-group'>
								สี
								<select id='t1COLOR' class='form-control input-sm'></select>
							</div>
						</div>
					</div>
					
					<div class='row col-sm-12'>
						<div class='col-sm-3'>	
							<div class='form-group'>
								เลขตัวถัง
								<input type='text' id='t1STRNO' class='form-control input-sm' placeholder='เลขตัวถัง'>
							</div>
						</div>
						<div class='col-sm-2' style='display:none;'>	
							<div class='form-group'>
								สถานะรถ
								<select id='t1STAT' class='form-control input-sm'>
									<option value=''>ทั้งหมด</option>
									<option value='N'>รถใหม่</option>
									<option value='O'>รถเก่า</option>
								</select>
							</div>
						</div>
						<div class='col-sm-1 col-sm-offset-0'>	
							<div class='form-group'>
								<br>
								<button id='btnt1search' class='btn btn-primary btn-sm' style='width:100%'>แสดง</button>
							</div>
						</div>
					</div>
				</div>
				
				<div id='resultSearcht1' style='height:calc(100% - 150px);overflow:auto;background-color:white;'></div>
			</div>
		";
		
		$response = array('html'=>$html,'status'=>true);
		echo json_encode($response);
	}
	
	function getSearchSTRNO_line(){
		//$this->load->library('line/LineNotify');
		require_once './vendor/autoload.php';
		$token = 'ty2KVy9r71JadnSp154CbpFqY7ARjw38VBiTqoekRjV';
		
		$arrs = array();
		$arrs["LOCAT"]  = $_REQUEST["LOCAT"];
		$arrs["RECVNO"] = $_REQUEST["RECVNO"];
		$arrs["STRNO"]  = $_REQUEST["STRNO"];
		$arrs["MODEL"]  = $_REQUEST["MODEL"];
		$arrs["COLOR"]  = $_REQUEST["COLOR"];
		$arrs["STAT"]   = $_REQUEST["STAT"];
		
		$token = 'ty2KVy9r71JadnSp154CbpFqY7ARjw38VBiTqoekRjV';
		//$token = 'lgNu4brRfcULq36GyhyGiCUeBamrzQSqiK1X4Ic1xxs';
		//$token = '5j6NbypUuW679JrS4bsi6DD8DrpR8xaITXdZXTMlgZg';
		$token = 'PbLZuNDLa6cEWM9kgsxBUDWVcZsj2Ed7V4wDz7S37nU';
		$lineapi = $token; // ใส่ token key ที่ได้มา
		$mms =  "พร้อมเที่ยวกันยัง".trim($arrs["STRNO"])." "; // ข้อความที่ต้องการส่ง
		date_default_timezone_set("Asia/Bangkok");
		$chOne = curl_init(); 
		curl_setopt( $chOne, CURLOPT_URL, "https://notify-api.line.me/api/notify"); 
		// SSL USE 
		curl_setopt( $chOne, CURLOPT_SSL_VERIFYHOST, 0); 
		curl_setopt( $chOne, CURLOPT_SSL_VERIFYPEER, 0); 
		//POST 
		curl_setopt( $chOne, CURLOPT_POST, 1);
		curl_setopt( $chOne, CURLOPT_POSTFIELDS, "message=$mms");
		curl_setopt( $chOne, CURLOPT_FOLLOWLOCATION, 1);
		
		$headers = array( 'Content-type: application/x-www-form-urlencoded', 'Authorization: Bearer '.$lineapi.'', ); 
		
		curl_setopt($chOne, CURLOPT_HTTPHEADER, $headers); 
		
		curl_setopt( $chOne, CURLOPT_RETURNTRANSFER, 1); 
		$result = curl_exec( $chOne ); 
		//Check error 
		if(curl_error($chOne)) { 
			echo 'error:' . curl_error($chOne); 
		} else { 
			$result_ = json_decode($result, true); 
			echo "status : ".$result_['status']; 
			echo "message : ". $result_['message'];
		}
		curl_close( $chOne ); 
	}	
	
	function getSearchSTRNO(){
		$arrs = array();
		$arrs["LOCAT"]  = $_REQUEST["LOCAT"];
		$arrs["RECVNO"] = $_REQUEST["RECVNO"];
		$arrs["STRNO"]  = $_REQUEST["STRNO"];
		$arrs["MODEL"]  = $_REQUEST["MODEL"];
		$arrs["COLOR"]  = $_REQUEST["COLOR"];
		$arrs["STAT"]   = $_REQUEST["STAT"];
		
		$cond = "";
		if($arrs["LOCAT"] != ""){
			$cond .= " and CRLOCAT='".$arrs["LOCAT"]."'";
		}else{
			$response = array('html'=>"โปรดระบุสาขาที่จะทำรายการกระจายรถด้วยครับ",'status'=>false);
			echo json_encode($response); exit;
		}
		if($arrs["RECVNO"] != ""){
			$cond .= " and RECVNO='".$arrs["RECVNO"]."'";
		}
		if($arrs["STRNO"] != ""){
			$cond .= " and STRNO like '".$arrs["STRNO"]."%'";
		}
		if($arrs["MODEL"] != ""){
			$cond .= " and MODEL='".$arrs["MODEL"]."'";
		}
		if($arrs["COLOR"] != ""){
			$cond .= " and COLOR='".$arrs["COLOR"]."'";
		}
		if($arrs["STAT"] != ""){
			$cond .= " and STAT='".$arrs["STAT"]."'";
		}
		
		$sql = "
			select * from {$this->MAuth->getdb('INVTRAN')}
			where 1=1 and ISNULL(SDATE,'')='' and ISNULL(RESVNO,'')='' and FLAG='D' ".$cond."
			order by CRLOCAT,STAT,MODEL,COLOR,STRNO
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
							MODEL='".$row->MODEL."' 
							BAAB='".$row->BAAB."' 
							COLOR='".$row->COLOR."' 
							STAT=".($row->STAT == "N" ? "รถใหม่" : "รถเก่า")."
							LOCAT='".$row->CRLOCAT."' 
							style='width:50px;cursor:pointer;text-align:center;'>
							<b><i class='glyphicon glyphicon-check' style='z-index:20;'></i></b>
							
						</td>
						<td>".$row->STRNO."</td>
						<td>".$row->MODEL."</td>
						<td>".$row->BAAB."</td>
						<td>".$row->COLOR."</td>
						<td>".($row->STAT == "N" ? "รถใหม่" : "รถเก่า")."</td>
						<td>".$row->CRLOCAT."</td>
						<td>".$row->RVLOCAT."</td>
					</tr>
				";
			}
		}else{
			$html .= "
				<tr>
					<td colspan='6'>ไม่พบข้อมูลสต๊อกรถ</td>
				</tr>
			";
		}
		
		$html = "
			<div id='table-fixed-SearchSTRNO' class='col-sm-12' style='height:100%;width:100%;overflow:auto;'>
				<table id='table-SearchSTRNO' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='100%'>
					<thead>
						<tr style='background-color:#ddd'>
							<th id='getithead' style='width:50px;cursor:pointer;text-align:center;'>
								<i class='glyphicon glyphicon-hand-down'></i>
							</th>
							<th>เลขตัวถัง</th>
							<th>รุ่น</th>
							<th>แบบ</th>
							<th>สี</th>
							<th>สถานะรถ</th>
							<th>ที่อยู่สาขา</th>
							<th>สถานที่รับรถ</th>
						</tr>
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
		
		$fn = $this->MAuth->getdb('fn_autocars');
		$sql = "select LOCAT,MODEL,COLOR,STORDER,MEMO1 from {$fn}('".$arrs['RVLOCAT']."','".$arrs['RECVNO']."')";
		//echo $sql; exit;
		$query4 = $this->db->query($sql);
		
		$html = "";
		$NRow = 1;
		if($query4->row()){
			foreach($query4->result() as $row4){
				$html .= "
					<tr>
						<td>".$NRow++."</td>
						<td>".$row4->LOCAT."</td>
						<td>".$row4->MODEL."</td>
						<td>".$row4->COLOR."</td>
						<td>".$row4->STORDER."</td>
						<td>".$row4->MEMO1."</td>
					</tr>
				";
			}
		}else{
			$html .= "
				<tr>
					<td colspan='5' style='color:red;'>เลขที่บิลรับรถ ".$arrs['RECVNO']." ไม่มีรถที่สามารถโอนย้าย </td>					
				</tr>
			";
		}
		
		$html = "
			<div id='table-fixed-Cautotransferscars' class='col-sm-12' style='height:100%;width:100%;overflow:auto;'>
				<table id='table-Cautotransferscars' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='100%'>
					<thead>
						<tr>
							<th>#</th>
							<th>สาขา</th>
							<th>รุ่น</th>
							<th>สี</th>
							<th>จำนวน</th>
							<th>จัดแบบ</th>
						</tr>
					</thead>
					<tbody>
						".$html."
					</tbody>
				</table>
			</div>	
		";
		
		$sql = "
			select a.RECVNO,a.STRNO,a.GCODE,b.GDESC,a.STAT,a.TYPE,a.MODEL,a.BAAB,a.COLOR,a.CC,a.CRLOCAT,a.RVLOCAT from {$this->MAuth->getdb('INVTRAN')} a
			left join {$this->MAuth->getdb('SETGROUP')} b on a.GCODE=b.GCODE
			where a.RECVNO='".$arrs['RECVNO']."'
		";
		$query = $this->db->query($sql);
		$htmlRECV  = "";
		
		$NRow = 1;
		if($query->row()){
			foreach($query->result() as $row){
				$color = "red;";
				if($row->CRLOCAT == $row->RVLOCAT){
					$color = "black;";
				}
				
				$htmlRECV .= "
					<tr style='color:".$color."'>
						<td>".$NRow++."</td>
						<td>".$row->RECVNO."</td>
						<td>".$row->STRNO."</td>
						<td title='".$row->GDESC."'>".$row->GCODE." </td>
						<td>".$row->STAT."</td>
						<td>".$row->TYPE."</td>
						<td>".$row->MODEL."</td>
						<td>".$row->BAAB."</td>
						<td>".$row->COLOR."</td>
						<td>".$row->CC."</td>
						<td>".$row->CRLOCAT."</td>
						<td>".$row->RVLOCAT."</td>
					</tr>
				";
			}
		}else{
			$htmlRECV .= "
				<tr>
					<td colspan='11' style='color:red;'>ไม่พบเลขที่บิลรับรถ ".$arrs['RECVNO']." ในระบบ</td>					
				</tr>
			";
		}
		
		$htmlRECV = "
			<div id='table-fixed-CautotransferscarsRECV' class='col-sm-12' style='height:100%;width:100%;overflow:auto;'>
				<table id='table-CautotransferscarsRECV' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='100%'>
					<thead>
						<tr>
							<th>#</th>
							<th>เลขที่บิลรับ</th>
							<th>เลขตัวถัง</th>
							<th>กลุ่มรถ</th>
							<th>สถานะ</th>
							<th>ยี่ห้อ</th>
							<th>รุ่น</th>
							<th>แบบ</th>
							<th>สี</th>
							<th>ขนาด</th>
							<th>รถอยู่ที่</th>
							<th>สถานที่รับ</th>
						</tr>
					</thead>
					<tbody>
						".$htmlRECV."
					</tbody>
				</table>
			</div>	
		";
		
		$response = array('htmlRECV'=>$htmlRECV,'html'=>$html,'status'=>true);
		echo json_encode($response);
	}
		
	function confirmResultt1AT(){
		$arrs = array(
			"LOCAT" => $_REQUEST["LOCAT"],
			"STRNOChoose" => $_REQUEST["STRNOChoose"],
			"STRNO" => $_REQUEST["STRNO"]
		);
		
		$size = sizeof($arrs["STRNO"]);
		
		$tempAuto = "insert into #tempAuto ";
		for($i=0;$i<$size;$i++){
			$dsize = sizeof($arrs["STRNO"][$i]);
			
			$tempAuto .= ($tempAuto == "insert into #tempAuto " ? " select " : " union all select ");
			
			for($j=0;$j<$dsize;$j++){
				if($j!=0){ $tempAuto .= ","; }
				$tempAuto .= "'".$arrs["STRNO"][$i][$j]."'";
			}
		}
		
		
		$size = sizeof($arrs["STRNOChoose"]);
		
		$tempChoose = "insert into #tempChoose ";
		for($i=0;$i<$size;$i++){
			$dsize = sizeof($arrs["STRNOChoose"][$i]);
			
			$tempChoose .= ($tempChoose == "insert into #tempChoose " ? "select " : " union all select ");
			
			for($j=0;$j<$dsize;$j++){
				if($j!=0){ $tempChoose .= ","; }
				if($j==4){ 
					$tempChoose .= "'".($arrs["STRNOChoose"][$i][$j] == "รถใหม่" ? "N" : "O")."'";
				}else{
					$tempChoose .= "'".$arrs["STRNOChoose"][$i][$j]."'";
				}
			}
		}
		
		$sql = "
			
			if OBJECT_ID('tempdb..#tempChoose') is not null drop table #tempChoose;
			create table #tempChoose (STRNO varchar(20),MODEL varchar(30),BAAB varchar(20),COLOR varchar(30),STAT varchar(5),FRM varchar(20));
			".$tempChoose."
		";
		//echo $sql; //exit;
		$this->db->query($sql);
		
		$sql = "		
			
			if OBJECT_ID('tempdb..#tempAuto') is not null drop table #tempAuto;
			create table #tempAuto (LOCAT varchar(20),MODEL varchar(30),BAAB varchar(20),COLOR varchar(30),CN int,FRM varchar(100));
			".$tempAuto."
		";
		//echo $sql; //exit;
		$this->db->query($sql);
		
		$sql = "		
			
			if OBJECT_ID('tempdb..#temptran') is not null drop table #temptran;
			create table #temptran (id varchar(20),msg varchar(max));
			
			begin tran transaction1
			begin try
				declare @LOCAT varchar(20);
				declare @LOCATOLD varchar(20) = '';
				declare @MODEL varchar(30);
				declare @BAAB varchar(20);
				declare @COLOR varchar(30);
				declare @CN int; --จำนวนรถใหม้ จำแนกตามรุ่น สี
				declare @TRANSNO varchar(12); --เลขที่ใบโอนย้าย
				declare @CRLOCAT varchar(20) = '".$arrs["LOCAT"]."';
				declare @STRNO varchar(30);
				declare @TRANSLOG varchar(max);
				
				declare cs_stock cursor for 
					select LOCAT,MODEL,BAAB,COLOR,sum(CN) as CN from #tempAuto 
					group by LOCAT,MODEL,BAAB,COLOR 
					order by LOCAT,MODEL,BAAB,COLOR;
					
				open cs_stock 
				fetch next from cs_stock into @LOCAT,@MODEL,@BAAB,@COLOR,@CN

				while @@FETCH_STATUS = 0  
				begin 
					
					IF @LOCATOLD <> @LOCAT
					BEGIN
						/* @symbol = สัญลักษณ์แทนประเภทของเลขที่ นั้นๆ */
						declare @symbol varchar(10) = (select H_TFCAR from {$this->MAuth->getdb('CONDPAY')});
						/* @rec = รหัสพื้นฐาน */
						declare @rec varchar(10) = (select SHORTL+@symbol+'-'+right(left(convert(varchar(8),GETDATE(),112),6),4) from {$this->MAuth->getdb('INVLOCAT')} where LOCATCD=@CRLOCAT);
						/* @TRANSNO = รหัสที่จะใช้ */		
						set @TRANSNO = (select isnull(MAX(TRANSNO),@rec+'0000') from ( 
							select TRANSNO collate Thai_CS_AS as TRANSNO from {$this->MAuth->getdb('INVTransfers')} where TRANSNO like ''+@rec+'%' collate thai_cs_as
							union select moveno collate Thai_CS_AS as moveno from {$this->MAuth->getdb('INVMOVM')} where MOVENO like ''+@rec+'%' collate thai_cs_as
						) as a);
						set @TRANSNO = left(@TRANSNO ,8)+right(right(@TRANSNO ,4)+10001,4);
											
						insert into {$this->MAuth->getdb('INVTransfers')} (
							TRANSNO,TRANSDT,TRANSFM,TRANSTO,EMPCARRY,APPROVED,
							TRANSQTY,TRANSSTAT,MEMO1,SYSTEM,INSERTBY,INSERTDT
						) values (
							@TRANSNO,CONVERT(varchar(8),getdate(),112),@CRLOCAT,@LOCAT,NULL,'".$this->sess["IDNo"]."'
							,0,'Sendding','auto','AT','".$this->sess["IDNo"]."',getdate()
						);					
						
						select @TRANSLOG = replace(isnull(@TRANSLOG,'')+QUOTENAME(@TRANSNO),'][',',');
					END
					
					while (@CN > 0)
					begin
						declare @getSTRNO varchar(30) = isnull((
							select top 1 b.STRNO from {$this->MAuth->getdb('INVTRAN')} a
							left join #tempChoose b on a.STRNO=b.STRNO collate Thai_CS_AS and a.MODEL=b.MODEL collate Thai_CS_AS and a.COLOR=b.COLOR collate Thai_CS_AS and a.CRLOCAT=b.FRM collate Thai_CS_AS 
							where b.STRNO is not null and a.FLAG='D' and a.RESVDT is null and a.SDATE is null
								and a.CRLOCAT=@CRLOCAT and a.MODEL=@MODEL and a.COLOR=@COLOR
						),'');
						
						if (@getSTRNO <> '')
						begin
							insert into {$this->MAuth->getdb('INVTransfersDetails')}
							select @TRANSNO
								,isnull((select max(TRANSITEM)+1 from {$this->MAuth->getdb('INVTransfersDetails')} where TRANSNO=@TRANSNO),1)
								,@getSTRNO,NULL,NULL,NULL,NULL,NULL,'".$this->sess["IDNo"]."',GETDATE()
											
							update {$this->MAuth->getdb('INVTransfers')}
							set TRANSQTY=TRANSQTY+1
							where TRANSNO=@TRANSNO
							
							update {$this->MAuth->getdb('INVTRAN')}
							set CRLOCAT='TRANS'
							where STRNO = @getSTRNO
							
							--ลบรายการรถที่จัดให้แล้วออก
							delete from #tempChoose
							where STRNO = @getSTRNO;
							
							set @CN = @CN - 1;	
						end	
					end	
				
					set @LOCATOLD = @LOCAT;
					fetch next from cs_stock into @LOCAT,@MODEL,@BAAB,@COLOR,@CN
				end
				
				close cs_stock;
				deallocate cs_stock;	
					
				insert into {$this->MAuth->getdb('hp_UserOperationLog')} (userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
				values ('".$this->sess["IDNo"]."','SYS02::บันทึก โอนย้ายรถ AT ',isnull(@TRANSLOG,'')+' ".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');

				insert into #temptran select 'Y',@TRANSLOG;
				commit tran transaction1;
			end try
			begin catch
				rollback tran transaction1;
				insert into #temptran select 'N',ERROR_MESSAGE();
			end catch
		";
		//echo $sql; exit;
		$this->db->query($sql);		
				
		$sql = "select * from #temptran";
		$query = $this->db->query($sql);
		
		/*
		$query = $query->row();
		print_r($query); exit;
		*/
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				if($row->id == "Y"){
					$TRANSNO = str_replace(",","','",(str_replace(array("[","]"),"'",$row->msg)));
					
					$sql = "
						if OBJECT_ID('tempdb..#tempChoose') is not null drop table #tempChoose;
						create table #tempChoose (STRNO varchar(20),MODEL varchar(30),BAAB varchar(20),COLOR varchar(30),STAT varchar(5),FRM varchar(20));
						".$tempChoose."
					";
					//echo $sql; exit;
					$this->db->query($sql);		
					
					$sql = "
						select b.STRNO,b.MODEL,b.COLOR,b.STAT,a.TRANSNO,a.TRANSFM,a.TRANSTO from (
							select b.*,a.TRANSFM,a.TRANSTO
							from {$this->MAuth->getdb('INVTransfers')} a
							left join {$this->MAuth->getdb('INVTransfersDetails')} b on a.TRANSNO=b.TRANSNO
							where a.TRANSNO in (".$TRANSNO.")
						) as a
						right join #tempChoose b on a.STRNO=b.STRNO collate thai_cs_as 
						order by a.TRANSNO
					";
					
					//echo $sql; exit;
					//$sql = "select * from #tempChoose";
					$q = $this->db->query($sql);
					$NRow = 1;
					if($q->row()){
						foreach($q->result() as $row_tran){
							$css="color:black;";
							if($row_tran->TRANSNO == ""){
								$css="color:Red;";
							}
							$html .= "
								<tr style='".$css."'>
									<td>".$NRow++."</td>
									<td>".$row_tran->STRNO."</td>
									<td>".$row_tran->MODEL."</td>
									<td>".$row_tran->COLOR."</td>
									<td>".$row_tran->STAT."</td>
									<td>".$row_tran->TRANSNO."</td>
									<td>".$row_tran->TRANSFM."</td>
									<td>".$row_tran->TRANSTO."</td>
								</tr>
							";
						}
					}
				}else{
					$html .= "
						<tr>
							<td colspan='8'>{$row->msg}</td>
						</tr>
					";
				}
			}
		}else{
			$html .= "
				<tr>
					<td colspan='8'>ไม่พบข้อมูล</td>
				</tr>
			";
		}
		
		$html = "
			<div id='table-fixed-tab33' class='col-sm-12' style='height:100%;width:100%;overflow:auto;'>
				<table id='table-tab33' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='100%'>
					<thead>
						<tr>
							<th>#</th>
							<th>เลขตัวถัง</th>
							<th>รุ่น</th>
							<th>สี</th>
							<th>สถานะรถ</th>
							<th>เลขที่บิลโอน</th>
							<th>ต้นทาง</th>
							<th>ปลายทาง</th>
						</tr>
					</thead>
					".$html."
				</table>
			</div>
		";
		
		echo json_encode(array("html"=>$html));
	}
	
	function calcurate(){
		$arrs = array();
		$arrs['STRNO'] 			= $_REQUEST['STRNO'];
		$arrs['tab11prov1'] 	= $_REQUEST['tab11prov1'];
		$arrs['tab11prov2'] 	= $_REQUEST['tab11prov2'];
		$arrs['tab11prov3'] 	= $_REQUEST['tab11prov3'];
		$arrs['tab11prov4'] 	= $_REQUEST['tab11prov4'];
		$arrs['tab11prov5'] 	= $_REQUEST['tab11prov5'];
		$arrs['condStockEmpty'] = $_REQUEST['condStockEmpty'];
		$arrs['condMaxLimit'] 	= $_REQUEST['condMaxLimit'];
		
		
		$strsize = sizeof($arrs['STRNO']);
		$str = "";
		for($i=0;$i<$strsize;$i++){
			$dstrsize = sizeof($arrs['STRNO'][$i]);
			for($j=0;$j<$dstrsize;$j++){
				if($j == 0){
					//เริ่มต้น select ข้อมูล
					if($i != 0){ 
						//กรณี มีการจัดรถมากกว่า 1 คัน 
						$str .= " union all "; 
					}
					$str .= " select ";
				}else{
					$str .= ",";
				}
				
				if($j == 4){
					$str .= ($arrs['STRNO'][$i][$j] == "รถใหม่" ? "'N'" : "'O'");
				}else{
					$str .= "'".$arrs['STRNO'][$i][$j]."'";					
				}
			}
		}
		if($str == ""){
			$response = array('html'=>"คุณยังไม่ได้ระบุรถที่ต้องการกระจายออกเลยที่ต้องการจัดรถ",'status'=>false);
			echo json_encode($response); exit;
		}
		
		// $locat เลือกจัดรถไปสาขาในจังหวัดไหนบ้าง
		$locat = "";
		if($arrs['tab11prov1'][0] == "true"){
			$locat .= "select '".$arrs['tab11prov1'][1]."'";
		}
		if($arrs['tab11prov2'][0] == "true"){
			$locat .= "select '".$arrs['tab11prov2'][1]."'";
		}
		if($arrs['tab11prov3'][0] == "true"){
			$locat .= "select '".$arrs['tab11prov3'][1]."'";
		}
		if($arrs['tab11prov4'][0] == "true"){
			$locat .= "select '".$arrs['tab11prov4'][1]."'";
		}
		if($arrs['tab11prov5'][0] == "true"){
			$locat .= "select '".$arrs['tab11prov5'][1]."'";
		}
		
		if($locat != ""){
			$locat = str_replace("'select","' union all select ",$locat);			
		}else{
			$response = array('html'=>"คุณยังไม่ได้ระบุจังหวัดที่ต้องการจัดรถ",'status'=>false);
			echo json_encode($response); exit;
		}
		
		if(!is_numeric($arrs['condStockEmpty'])  or !is_numeric($arrs['condMaxLimit'])){
			$response = array('html'=>"ผิดพลาด พท.ว่าง ที่จะจัดรถหรือ limit ต้องเป็นตัวเลขเท่านั้น",'status'=>false);
			echo json_encode($response); exit;
		}
		
		if($arrs['condMaxLimit'] < 1){
			$response = array('html'=>"ผิดพลาด limit ต้องมากกว่า 0 ",'status'=>false);
			echo json_encode($response); exit;
		}
		
		$sql = "
			use YTKManagement;
			
			declare @str listSTRNo;
			insert into @str ".$str."
			
			declare @lc listLOCAT;
			insert into @lc ".$locat."	
						
			select * into #temp from YTKManagement.dbo.fn_autocars_beta2(@str,@lc,".$arrs['condStockEmpty'].",".$arrs['condMaxLimit'].");
		";
		//echo $sql; exit;
		$this->db->query($sql);
				
		$sql = "
			use YTKManagement;
			
			declare @str listSTRNo;
			insert into @str ".$str."
			
			select * from #temp
			union all
			select a.LOCAT,a.BAAB,a.MODEL,a.COLOR,a.STAT,a.s-isnull(b.STORDER,0) r,a.MEMO1 from (
				select '' LOCAT,MODEL,COLOR,STAT,COUNT(MODEL) s,'ไม่สามารถจัดรถได้' MEMO1 from @str
				group by MODEL,COLOR,STAT
			) as a
			left join (
				select MODEL,BAAB,COLOR,STAT,sum(STORDER) STORDER from #temp 
				group by MODEL,COLOR,STAT
			) as b on a.MODEL=b.MODEL  and a.COLOR=b.COLOR and a.STAT=b.STAT
			where a.s-isnull(b.STORDER,0) > 0
		";
		//echo $sql; exit;
		$sql = "select * from #temp";
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			$NRow = 1;
			foreach($query->result() as $row){
				$html .= "
					<tr class='trow' seq=".$NRow.">
						<td class='getit' seq=".$NRow++." 
							MODEL='".$row->MODEL."' 
							BAAB='".$row->BAAB."' 
							COLOR='".$row->COLOR."' 
							style='width:50px;cursor:pointer;text-align:center;color:red;'
						>
							<b><i class='glyphicon glyphicon-trash' style='z-index:20;'></i></b>
						</td>
						<td>".$row->LOCAT."</td>
						<td>".$row->MODEL."</td>
						<td>".$row->BAAB."</td>
						<td>".$row->COLOR."</td>
						<td>".$row->STORDER."</td>
						<td>".$row->MEMO1."</td>
					</tr>
				";
			}
		}else{
			$html = "<tr><td colspan='7' class='text-center'>ไม่สามารถจัดรถ ตามเงื่อนไขได้ครับ</td></tr>";
		}
		
		if ($html != ""){
			$html = "
				<div id='table-fixed-tab22' class='col-sm-12' style='height:100%;width:100%;overflow:auto;'>
					<table id='table-tab22' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='100%'>
						<thead>
							<tr>
								<th>#</th>
								<th>สาขา</th>
								<th>รุ่น</th>
								<th>แบบ</th>
								<th>สี</th>
								<th>จำนวน</th>
								<th>จัดแบบ</th>
							</tr>
						</thead>
						<tbody style='background-color:white;'>
							".$html."
						</tbody>
					</table>
				</div>	
			";
		}
		
		
		$response = array('html'=>$html,'status'=>true);
		echo json_encode($response); exit;
	}
	
	function checkprov(){
		$locat = $_REQUEST["locat"];
		
		$sql = "
			select * from {$this->MAuth->getdb('std_locatStock')} 
			where LOCAT='".$locat."'
		";
		$query = $this->db->query($sql);
		
		$html = "";
		$locatLine = array("ตรัง"=>1,"สุราษฎร์ธานี"=>1,"ชุมพร"=>1,"กระบี่"=>2,"พังงา"=>2);
		if($query->row()){
			foreach($query->result() as $row){
				$html = $locatLine[$row->Prov];
			}
		}
		
		echo json_encode(array("html"=>$html));
	}
}




















