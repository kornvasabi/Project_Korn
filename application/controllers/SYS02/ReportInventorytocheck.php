<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//BEE+
class ReportInventorytocheck extends MY_Controller {
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
			<div class='b_tab1' name='home' locat='{$this->sess['branch']}' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}' today='".$this->today('today')."' style='height:calc(100vh - 132px);overflow:auto;background-color:white;'>
				<div class='col-sm-12 col-xs-12' style='height:100%;overflow:auto;font-size:10.5pt;'>					
					<div class='row' style='height:90%;'>
						<div class='col-sm-12 col-xs-12' style='background-color:#0067a5;border:5px solid white;height:75px;text-align:center;font-size:12pt;color:white;font-weight:bold;'>	
							<br>รายงานสินค้าคงเหลือ (รถ) เพื่อเช็คสต๊อก<br>
						</div>
						<div class='col-sm-8 col-xs-8 col-sm-offset-2'>
							<br>
							<div class='col-sm-4 col-xs-4'>	
								<div class='form-group'>
									ณ วันที่
									<input type='text' id='FRMDATE' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' placeholder='ณ วันที่' value='".$this->today('today')."' style='font-size:10.5pt'>
								</div>
							</div>
							<div class='col-sm-4 col-xs-4'>	
								<div class='form-group'>
									สถานที่รับรถ
									<select id='LOCAT1' class='form-control input-sm' data-placeholder='สถานที่รับรถ'></select>
								</div>
							</div>
							<div class='col-sm-4 col-xs-4'>	
								<div class='form-group' >
									เจ้าหนี้
									<select id='APCODE1' class='form-control input-sm' data-placeholder='เจ้าหนี้'></select>
								</div>
							</div>
							<div class='col-sm-4 col-xs-4'>	
								<div class='form-group' >
									กลุ่มสินค้า
									<select id='GCOCE1' class='form-control input-sm' data-placeholder='กลุ่มสินค้า'></select>
								</div>
							</div>
							<div class='col-sm-4 col-xs-4'>	
								<div class='form-group' >
									ยี่ห้อ
									<select id='TYPE1' class='form-control input-sm' data-placeholder='ยี่ห้อ'></select>
								</div>
							</div>
							<div class='col-sm-4 col-xs-4'>	
								<div class='form-group' >
									รุ่น
									<select id='MODEL1' class='form-control input-sm' data-placeholder='รุ่น'></select>
								</div>
							</div>
							<div class='col-sm-4 col-xs-4'>	
								<div class='form-group' >
									แบบ
									<select id='BAAB1' class='form-control input-sm' data-placeholder='แบบ'></select>
								</div>
							</div>
							<div class='col-sm-4 col-xs-4'>	
								<div class='form-group' >
									สี
									<select id='COLOR1' class='form-control input-sm' data-placeholder='สี'></select>
								</div>
							</div>
							<div class='col-sm-4 col-xs-4'>	
								<div class='form-group' >
									ขนาด>=
									<select id='CC1' class='form-control input-sm' data-placeholder='ขนาด'></select>
								</div>
							</div>
						</div>
						<div class='col-sm-8 col-xs-8 col-sm-offset-2'>
							<br>
							<div class='col-sm-12 col-xs-12'>	
								<div class='form-group'>
									ข้อมูลรายงาน
									<div class='col-sm-12 col-xs-12' style='border:0.1px dotted #d6d6d6;'>	
										<div class='col-sm-4 col-xs-4'>
											<div class='form-group'>
												<br>
												<input type= 'radio' id='r_no' name='reserve' checked> ไม่รวมรถจอง
											</div>
										</div>
										<div class='col-sm-4 col-xs-4'>
											<div class='form-group'>
												<br>
												<input type= 'radio' id='r_yes' name='reserve'> รวมรถจอง
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class='col-sm-8 col-xs-8 col-sm-offset-2'>
							<br>
							<div class='col-sm-4 col-xs-4'>	
								<div class='form-group'>
									รูปแบบการพิมพ์
									<div class='col-sm-12 col-xs-12' style='border:0.1px dotted #d6d6d6;'>	
										<div class='col-sm-12 col-xs-12'>
											<div class='form-group'>
												<br>
												<input type= 'radio' id='hor' name='layout' checked> แนวนอน
												<br>
												<input type= 'radio' id='ver' name='layout'> แนวตั้ง
												<br><br><br>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class='col-sm-4 col-xs-4'>	
								<div class='form-group'>
									สถานะสินค้า
									<div class='col-sm-12 col-xs-12' style='border:0.1px dotted #d6d6d6;'>	
										<div class='col-sm-12 col-xs-12'>
											<div class='form-group'>
												<br>
												<input type= 'radio' id='new' name='stat'> ใหม่
												<br>
												<input type= 'radio' id='old' name='stat'> เก่า
												<br>
												<input type= 'radio' id='all' name='stat' checked> ทั้งหมด
												<br><br>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class='col-sm-4 col-xs-4'>
								<div class='form-group'>
									เรียงลำดับข้อมูล
									<div class='col-sm-12 col-xs-12' style='border:0.1px dotted #d6d6d6;'>	
										<div class='col-sm-12 col-xs-12'>
											<div class='form-group'>
												<br>
												<input type= 'radio' id='types' name='orderby' checked> ตาม รุ่น แบบ สี
												<br>
												<input type= 'radio' id='rcvdate' name='orderby'> ตามวันที่รับสินค้า
												<br>
												<input type= 'radio' id='strno' name='orderby'> ตามเลขตัวถัง
												<br>
												<input type= 'radio' id='gcode' name='orderby'> ตามกลุ่มสินค้า
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class='row' style='height:10%;'>
						<div class='col-sm-12 col-xs-12'><br>	
							<button id='btnt1search' class='btn btn-primary btn-sm' style='width:100%;font-size:10.5pt;'><span class='glyphicon glyphicon-search'> แสดง</span></button>
						</div>
					</div>
				</div>
			</div>
		";
		
		$html.= "<script src='".base_url('public/js/SYS02/ReportInventorytocheck.js')."'></script>";
		echo $html;
	}
	
	function search(){
		$LOCAT1		= $_REQUEST["LOCAT1"];
		$APCODE1 	= str_replace(chr(0),'',$_REQUEST["APCODE1"]);
		$TYPE1 		= str_replace(chr(0),'',$_REQUEST["TYPE1"]);
		$GCOCE1 	= str_replace(chr(0),'',$_REQUEST["GCOCE1"]);
		$BAAB1 		= str_replace(chr(0),'',$_REQUEST["BAAB1"]);
		$MODEL1 	= str_replace(chr(0),'',$_REQUEST["MODEL1"]);
		$CC1 		= str_replace(chr(0),'',$_REQUEST["CC1"]);
		$COLOR1 	= str_replace(chr(0),'',$_REQUEST["COLOR1"]);
		$FRMDATE 	= $this->Convertdate(1,$_REQUEST["FRMDATE"]);
		$stat 		= $_REQUEST["stat"];
		$reserve 		= $_REQUEST["reserve"];
		$orderby 	= $_REQUEST["orderby"];
		
		$cond = ""; $rpcond = "";
		
		if($LOCAT1 != ""){
			$rpcond .= "  สถานที่รับรถ ".$LOCAT1;
		}
		
		if($APCODE1 != ""){
			$cond .= " AND (V.APCODE LIKE '".$APCODE1."%') ";
			$rpcond .= "  รหัสเจ้าหนี้ ".$APCODE1;
		}
		
		if($TYPE1 != ""){
			$cond .= " AND (T.TYPE LIKE '".$TYPE1."%')";
		}
		
		if($GCOCE1 != ""){
			$cond .= " AND ( T.GCODE LIKE '".$GCOCE1."%') ";
		}
		
		if($BAAB1 != ""){
			$cond .= " AND (T.BAAB LIKE '".$BAAB1."%')";
		}
		
		if($MODEL1 != ""){
			$cond .= " AND (T.MODEL LIKE '".$MODEL1."%') ";
		}
		
		if($CC1 != ""){
			$cond .= " AND (T.CC >= ".$CC1." ) ";
		}else{
			$cond .= " AND (T.CC >= 0 ) ";
		}
		
		if($COLOR1 != ""){
			$cond .= " AND (T.COLOR LIKE '".$COLOR1."%') ";
		}
		
		if($stat != ""){
			$cond .= " AND (T.STAT LIKE '".$stat."%')";
		}
		
		if($reserve == "NO"){
			$cond .= " AND (T.RESVDT> '".$FRMDATE."' OR T.RESVDT IS NULL)";
			$rpcond .= "  ไม่รวมรถจอง";
		}else{
			$rpcond .= "  รวมรถจอง";
		}
		
		$sql = "
				IF OBJECT_ID('tempdb..#main') IS NOT NULL DROP TABLE #main
				select *
				into #main
				from(
					SELECT T.MODEL, T.BAAB, T.COLOR, T.CC, T.STRNO, T.ENGNO, V.RECVNO, V.RECVDT, convert(char,V.RECVDT,112) as RECVDTS, T.CRLOCAT,
					V.INVNO, convert(char,V.INVDT,112) as INVDT, T.NETCOST, T.CRVAT, T.TOTCOST, T.TADDCOST, T.BONUS, T.GCODE, T.RESVNO  
					FROM {$this->MAuth->getdb('INVINVO')} V
					LEFT JOIN {$this->MAuth->getdb('INVTRAN')} T ON  V.RECVNO=T.RECVNO AND V.LOCAT=T.RVLOCAT
					WHERE (T.SDATE>'".$FRMDATE."' OR T.SDATE IS NULL) AND not exists( 
						select STRNO from (
							select STRNO, YDATE as MOVEDT, YLOCAT as MOVETO, SDATE from {$this->MAuth->getdb('ARHOLD')}  
							union 
							select STRNO, YDATE as MOVEDT, YLOCAT as MOVETO, SDATE from {$this->MAuth->getdb('ARCHAG')}
						)as C where C.STRNO = T.STRNO and C.MOVEDT >= V.RECVDT and '".$FRMDATE."' >= C.SDATE and '".$FRMDATE."'< C.MOVEDT
					) AND (V.RECVDT <= '".$FRMDATE."') AND (T.RVLOCAT LIKE '".$LOCAT1."%') ".$cond."
					GROUP BY  V.RECVNO, V.RECVDT, V.INVNO, V.INVDT, V.TAXNO, V.TAXDT, T.GCODE, T.MOVENO, T.MOVEDT, T.TYPE, T.MODEL,
					T.BAAB, T.COLOR, T.CC, T.STRNO, T.ENGNO, T.KEYNO, T.STAT, T.RVLOCAT, T.CRLOCAT, T.MILERT, T.NETCOST, T.CRVAT,
					T.TOTCOST, T.TADDCOST, T.BONUS, T.RESVNO 
					HAVING ('".$FRMDATE."' < (select  min(b.movedt) 
					from (
						select strno, movedt, moveto from {$this->MAuth->getdb('INVMOVT')} 
						union 
						select strno, ydate as movedt, ylocat as moveto from {$this->MAuth->getdb('ARHOLD')}       
						union 
						select strno, ydate as movedt, ylocat as moveto from {$this->MAuth->getdb('ARCHAG')}
					) as b where t.strno = b.strno and b.movedt >= v.recvdt) 
					or t.strno not  in ( select strno from (
						select strno, movedt, moveto from {$this->MAuth->getdb('INVMOVT')} 
						union 
						select strno, ydate as movedt, ylocat as moveto from {$this->MAuth->getdb('ARHOLD')} 
						union 
						select strno, ydate as movedt, ylocat as moveto from {$this->MAuth->getdb('ARCHAG')}
					) as c where c.strno = t.strno and c.movedt >= v.recvdt)) 

					UNION 

					SELECT T.MODEL, T.BAAB, T.COLOR, T.CC, T.STRNO, T.ENGNO, V.RECVNO, V.RECVDT, convert(char,V.RECVDT,112) as RECVDTS, T.CRLOCAT,
					V.INVNO, convert(char,V.INVDT,112) as INVDT, T.NETCOST, T.CRVAT, T.TOTCOST, T.TADDCOST, T.BONUS, T.GCODE, T.RESVNO  
					FROM {$this->MAuth->getdb('INVINVO')} V 
					LEFT JOIN {$this->MAuth->getdb('INVTRAN')} T ON V.RECVNO=T.RECVNO AND V.LOCAT=T.RVLOCAT
					LEFT JOIN(
						select STRNO, MOVEDT, MOVETO, MOVSEQ from {$this->MAuth->getdb('INVMOVT')} 
						union 
						select STRNO, YDATE as MOVEDT, YLOCAT as MOVETO, 10000 as MOVSEQ from {$this->MAuth->getdb('ARHOLD')}       
						union 
						select STRNO, YDATE as MOVEDT, YLOCAT as MOVETO, 10000 as MOVSEQ from {$this->MAuth->getdb('ARCHAG')}
					)as E ON T.STRNO = E.STRNO
					WHERE ((T.CURSTAT <> 'Y' AND T.CURSTAT <> 'R') OR T.CURSTAT IS NULL OR T.CURSTAT <> '') 
					AND (T.SDATE>'".$FRMDATE."' OR T.SDATE IS NULL) AND not exists(
						select STRNO from (
							select STRNO, YDATE as MOVEDT, YLOCAT as MOVETO, SDATE from {$this->MAuth->getdb('ARHOLD')}  
							union 
							select STRNO, YDATE as MOVEDT, YLOCAT as MOVETO, SDATE from {$this->MAuth->getdb('ARCHAG')}
						)as C where C.STRNO = T.STRNO and C.MOVEDT >= V.RECVDT and '".$FRMDATE."' >= C.SDATE and '".$FRMDATE."'< C.MOVEDT
					)  AND (E.MOVETO LIKE '%".$LOCAT1."') ".$cond."
					AND (E.MOVSEQ = (select max(movseq) from {$this->MAuth->getdb('INVMOVT')} where strno=e.strno and movedt<='".$FRMDATE."') or (e.movseq=0) 
					or ((e.movseq=10000) and e.strno not in (select strno from {$this->MAuth->getdb('INVMOVT')} where strno=e.strno and e.movedt=movedt))) 
					and (E.MOVEDT <= '".$FRMDATE."') and (v.recvdt <= e.movedt) 
					GROUP BY  V.RECVNO, V.RECVDT, V.INVNO, V.INVDT, V.TAXNO, V.TAXDT, T.GCODE, T.MOVENO, T.MOVEDT, T.TYPE, T.MODEL,
					T.BAAB, T.COLOR, T.CC, T.STRNO, T.ENGNO, T.KEYNO, T.STAT, T.RVLOCAT, T.CRLOCAT, T.MILERT, T.NETCOST, T.CRVAT,
					T.TOTCOST, T.TADDCOST, T.BONUS, T.RESVNO, E.STRNO, E.MOVEDT  
					HAVING ('".$FRMDATE."' < ( select min(c.movedt) 
					from (
						select strno, movedt, moveto from {$this->MAuth->getdb('INVMOVT')}       
						union 
						select strno, ydate as movedt, ylocat as moveto from {$this->MAuth->getdb('ARHOLD')}       
						union 
						select strno, ydate as movedt, ylocat as moveto from {$this->MAuth->getdb('ARCHAG')}
					)as c where c.strno = e.strno and c.movedt > e.movedt) 
					or e.strno not in ( select strno from(
						select strno, movedt, moveto from {$this->MAuth->getdb('INVMOVT')}  
						union 
						select strno, ydate as movedt, ylocat as moveto from {$this->MAuth->getdb('ARHOLD')} 
						union 
						select strno, ydate as movedt, ylocat as moveto from {$this->MAuth->getdb('ARCHAG')}
					)as c where c.strno = e.strno and c.movedt > e.movedt)) 
				)main
		";//echo $sql; 
		$query = $this->db->query($sql);
		
		$sql = "
				select MODEL, BAAB, COLOR, CC, a.STRNO, ENGNO, RESVNO , RECVNO, RECVDTS, INVNO, INVDT,  
				case when CRLOCAT = 'TRANS' then isnull(TRANSTO,'')+'(รอรับโอน)' collate thai_ci_as else CRLOCAT end as LOCAT
				from #main a
				left join (
					 select b.STRNO, a.TRANSTO 
					 from YTKManagement.dbo.INVTransfers as a
					 left join YTKManagement.dbo.INVTransfersDetails as b on a.TRANSNO=b.TRANSNO collate thai_cs_as
					 where a.TRANSSTAT<>'Cancel' and isnull(b.MOVENO,'') = ''
				)b on a.STRNO = b.STRNO collate thai_ci_as
				order by MODEL, BAAB, COLOR
		";//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$sql = "select 'รวมทั้งหมด  '+convert(nvarchar,COUNT(STRNO))+' คัน' as Total from #main";//echo $sql; exit;
		$query2 = $this->db->query($sql);
		
		$head = ""; $html = ""; $head2 = "";  $report = ""; $sumreport = ""; $sumreport2 = ""; $i = 0; 
		
		$head = "<tr style='height:25px;'>
				<th style='display:none;'>#</th>
				<th style='vertical-align:top;'>รุ่น</th>
				<th style='vertical-align:top;'>แบบ</th>
				<th style='vertical-align:top;'>สี</th>
				<th style='vertical-align:top;text-align:center;'>ขนาด</th>
				<th style='vertical-align:top;'>เลขตัวถัง</th>
				<th style='vertical-align:top;'>เลขเครื่อง</th>
				<th style='vertical-align:top;'>เลขที่ใบจอง</th>
				<th style='vertical-align:top;'>เลขที่ใบรับ</th>
				<th style='vertical-align:top;text-align:center;'>วันที่รับ</th>
				<th style='vertical-align:top;'>เลขที่ใบส่ง</th>
				<th style='vertical-align:top;text-align:center;'>วันที่ใบส่ง</th>
				<th style='vertical-align:top;'>สาขาที่เก็บ</th>
				</tr>
		";
		
		$NRow = 1;
		if($query->row()){
			foreach($query->result() as $row){$i++;
				$html .= "
					<tr class='trow' seq=".$NRow.">
						<td seq=".$NRow++." style='display:none;'></td>
						<td>".$row->MODEL."</td>
						<td>".$row->BAAB."</td>
						<td>".$row->COLOR."</td>
						<td align='center'>".number_format($row->CC)."</td>
						<td>".$row->STRNO."</td>
						<td>".$row->ENGNO."</td>
						<td>".$row->RESVNO."</td>
						<td>".$row->RECVNO."</td>
						<td align='center'>".$this->Convertdate(2,$row->RECVDTS)."</td>
						<td>".$row->INVNO."</td>
						<td align='center'>".$this->Convertdate(2,$row->INVDT)."</td>
						<td>".$row->LOCAT."</td>
					</tr>
				";	
			}
		}
		
		$head2 = "<tr>
					<th style='vertical-align:middle;'>#</th>
					<th style='vertical-align:top;'>รุ่น</th>
					<th style='vertical-align:top;'>แบบ</th>
					<th style='vertical-align:top;'>สี</th>
					<th style='vertical-align:top;'>ขนาด</th>
					<th style='vertical-align:top;'>เลขตัวถัง</th>
					<th style='vertical-align:top;'>เลขเครื่อง</th>
					<th style='vertical-align:top;'>เลขที่ใบจอง</th>
					<th style='vertical-align:top;'>เลขที่ใบรับ</th>
					<th style='vertical-align:top;'>วันที่รับ</th>
					<th style='vertical-align:top;'>เลขที่ใบส่ง</th>
					<th style='vertical-align:top;'>วันที่ใบส่ง</th>
					<th style='vertical-align:top;'>สาขาที่เก็บ</th>
				</tr>
		";
		
		$No = 1;
		if($query->row()){
			foreach($query->result() as $row){
				$report .= "
					<tr class='trow'>
						<td style='mso-number-format:\"\@\";'>".$No++."</td>
						<td style='mso-number-format:\"\@\";'>".$row->MODEL."</td>
						<td style='mso-number-format:\"\@\";'>".$row->BAAB."</td>
						<td style='mso-number-format:\"\@\";'>".$row->COLOR."</td>
						<td style='mso-number-format:\"\@\";'>".number_format($row->CC)."</td>
						<td style='mso-number-format:\"\@\";'>".$row->STRNO."</td>
						<td style='mso-number-format:\"\@\";'>".$row->ENGNO."</td>
						<td style='mso-number-format:\"\@\";'>".$row->RESVNO."</td>
						<td style='mso-number-format:\"\@\";'>".$row->RECVNO."</td>
						<td style='mso-number-format:\"\@\";'>".$this->Convertdate(2,$row->RECVDTS)."</td>
						<td style='mso-number-format:\"\@\";'>".$row->INVNO."</td>
						<td style='mso-number-format:\"\@\";'>".$this->Convertdate(2,$row->INVDT)."</td>
						<td style='mso-number-format:\"\@\";'>".$row->LOCAT."</td>
					</tr>
				";	
			}
		}
		
		if($query2->row()){
			foreach($query2->result() as $row){
				$sumreport = "
					<tr style='height:25px;'>
						<th colspan='12' style='border-left:0px;border-bottom:0px;border-top:0px;vertical-align:middle;text-align:left;'>".$row->Total."</th>
					</tr>
				";	
			}
		}
		
		if($query2->row()){
			foreach($query2->result() as $row){
				$sumreport2 = "
					<tr class='trow'>
						<th style='mso-number-format:\"\@\";text-align:left;' colspan='13'>".$row->Total."</th>
					</tr>
				";	
			}
		}
		
		if($i>0){
			$html = "
				<div id='table-fixed-ReportInventorytocheck' class='col-sm-12' style='height:100%;width:100%;overflow:auto;font-size:9pt;'>
					<table id='table-ReportInventorytocheck' style='background-color:white;' class='col-sm-12 display table table-bordered' cellspacing='0' width='calc(100% - 1px)'>
						<thead>
						<tr style='height:40px;'>
							<th colspan='12' style='font-size:12pt;border:0px;vertical-align;middle;text-align:center;'>รายงานสินค้าคงเหลือ (รถ) เพื่อเช็คสต๊อก</th>
						</tr>
						<tr style='height:25px;'>
							<td colspan='12' style='border-bottom:1px solid #ddd;vertical-align;middle;text-align:center;'>คงเหลือ ณ วันที่  ".$_REQUEST["FRMDATE"]." ".$rpcond."  ออกรายงาน ณ วันที่ ".$this->today('today')."</td>
						</tr>
						".$head."
						</thead>	
						<tbody style='height: 10px !important; overflow: scroll;'>
						".$html."
						</tbody>	
						<tfoot>
						".$sumreport."
						</tfoot>
					</table>
				</div>
			";
		}else{
			$html="<font style='color:red;'>ไม่มีข้อมูล</font>";
		}
		
		$report = "
			<div id='table-fixed-ReportInventorytocheck2' class='col-sm-12' style='height:100%;width:100%;overflow:auto;'>
				<table id='table-ReportInventorytocheck2' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='100%'>
					<thead>
						<tr>
							<th colspan='13' style='font-size:12pt;border:0px;text-align:center;'>รายงานสินค้าคงเหลือ (รถ) เพื่อเช็คสต๊อก</th>
						</tr>
						<tr>
							<td colspan='13' style='border:0px;text-align:center;'>คงเหลือ ณ วันที่  ".$_REQUEST["FRMDATE"]." ".$rpcond."  ออกรายงาน ณ วันที่ ".$this->today('today')."</td>
						</tr>
						".$head2."
					</thead>	
					<tbody>
						".$report."
						".$sumreport2."
					</tbody>
				</table>
			</div>
		";
		
		$response = array("html"=>$html, "report"=>$report, "reporttoday"=>str_replace('/','-',$this->today('today')));
		echo json_encode($response);
	}
	
	function conditiontopdf(){
		$data 	= 	array();
		$data[] = 	urlencode(
						$_REQUEST["LOCAT1"].'||'.
						$_REQUEST["APCODE1"].'||'.
						$_REQUEST["TYPE1"].'||'.
						$_REQUEST["GCOCE1"].'||'.
						$_REQUEST["BAAB1"].'||'.
						$_REQUEST["MODEL1"].'||'.
						$_REQUEST["CC1"].'||'.
						$_REQUEST["COLOR1"].'||'.
						$_REQUEST["FRMDATE"].'||'.
						$_REQUEST["stat"].'||'.
						$_REQUEST["reserve"].'||'.
						$_REQUEST["orderby"].'||'.
						$_REQUEST["layout"]
					);
		echo json_encode($this->generateData($data,"encode"));
	}
	
	function pdf(){
		ini_set("memory_limit","-1");
		ini_set("pcre.backtrack_limit", "100000000");
		
		$data 	= array();
		$data[] = $_GET["condpdf"];
		$arrs 	= $this->generateData($data,"decode");
		$arrs[0]= urldecode($arrs[0]);
		$tx 	= explode("||",$arrs[0]);
		$LOCAT1		= $tx[0];
		$APCODE1 	= str_replace(chr(0),'',$tx[1]);
		$TYPE1 		= str_replace(chr(0),'',$tx[2]);
		$GCOCE1 	= str_replace(chr(0),'',$tx[3]);
		$BAAB1 		= str_replace(chr(0),'',$tx[4]);
		$MODEL1 	= str_replace(chr(0),'',$tx[5]);
		$CC1 		= str_replace(chr(0),'',$tx[6]);
		$COLOR1 	= str_replace(chr(0),'',$tx[7]);
		$FRMDATE 	= $this->Convertdate(1,$tx[8]);
		$stat 		= $tx[9];
		$reserve 	= $tx[10];
		$orderby 	= $tx[11];
		$layout 	= $tx[12];
		
		$cond = ""; $rpcond = "";
		
		if($LOCAT1 != ""){
			$rpcond .= "  สถานที่รับรถ ".$LOCAT1;
		}
		
		if($APCODE1 != ""){
			$cond .= " AND (V.APCODE LIKE '".$APCODE1."%') ";
			$rpcond .= "  รหัสเจ้าหนี้ ".$APCODE1;
		}
		
		if($TYPE1 != ""){
			$cond .= " AND (T.TYPE LIKE '".$TYPE1."%')";
		}
		
		if($GCOCE1 != ""){
			$cond .= " AND ( T.GCODE LIKE '".$GCOCE1."%') ";
		}
		
		if($BAAB1 != ""){
			$cond .= " AND (T.BAAB LIKE '".$BAAB1."%')";
		}
		
		if($MODEL1 != ""){
			$cond .= " AND (T.MODEL LIKE '".$MODEL1."%') ";
		}
		
		if($CC1 != ""){
			$cond .= " AND (T.CC >= ".$CC1." ) ";
		}else{
			$cond .= " AND (T.CC >= 0 ) ";
		}
		
		if($COLOR1 != ""){
			$cond .= " AND (T.COLOR LIKE '".$COLOR1."%') ";
		}
		
		if($stat != ""){
			$cond .= " AND (T.STAT LIKE '".$stat."%')";
		}
		
		if($reserve == "NO"){
			$cond .= " AND (T.RESVDT> '".$FRMDATE."' OR T.RESVDT IS NULL)";
			$rpcond .= "  ไม่รวมรถจอง";
		}else{
			$rpcond .= "  รวมรถจอง";
		}
		
		$sql = "
				IF OBJECT_ID('tempdb..#main') IS NOT NULL DROP TABLE #main
				select *
				into #main
				from(
					SELECT T.MODEL, T.BAAB, T.COLOR, T.CC, T.STRNO, T.ENGNO, V.RECVNO, V.RECVDT, convert(char,V.RECVDT,112) as RECVDTS, T.CRLOCAT,
					V.INVNO, convert(char,V.INVDT,112) as INVDT, T.NETCOST, T.CRVAT, T.TOTCOST, T.TADDCOST, T.BONUS, T.GCODE, T.RESVNO  
					FROM {$this->MAuth->getdb('INVINVO')} V
					LEFT JOIN {$this->MAuth->getdb('INVTRAN')} T ON  V.RECVNO=T.RECVNO AND V.LOCAT=T.RVLOCAT
					WHERE (T.SDATE>'".$FRMDATE."' OR T.SDATE IS NULL) AND not exists( 
						select STRNO from (
							select STRNO, YDATE as MOVEDT, YLOCAT as MOVETO, SDATE from {$this->MAuth->getdb('ARHOLD')}  
							union 
							select STRNO, YDATE as MOVEDT, YLOCAT as MOVETO, SDATE from {$this->MAuth->getdb('ARCHAG')}
						)as C where C.STRNO = T.STRNO and C.MOVEDT >= V.RECVDT and '".$FRMDATE."' >= C.SDATE and '".$FRMDATE."'< C.MOVEDT
					) AND (V.RECVDT <= '".$FRMDATE."') AND (T.RVLOCAT LIKE '".$LOCAT1."%') ".$cond."
					GROUP BY  V.RECVNO, V.RECVDT, V.INVNO, V.INVDT, V.TAXNO, V.TAXDT, T.GCODE, T.MOVENO, T.MOVEDT, T.TYPE, T.MODEL,
					T.BAAB, T.COLOR, T.CC, T.STRNO, T.ENGNO, T.KEYNO, T.STAT, T.RVLOCAT, T.CRLOCAT, T.MILERT, T.NETCOST, T.CRVAT,
					T.TOTCOST, T.TADDCOST, T.BONUS, T.RESVNO 
					HAVING ('".$FRMDATE."' < (select  min(b.movedt) 
					from (
						select strno, movedt, moveto from {$this->MAuth->getdb('INVMOVT')} 
						union 
						select strno, ydate as movedt, ylocat as moveto from {$this->MAuth->getdb('ARHOLD')}       
						union 
						select strno, ydate as movedt, ylocat as moveto from {$this->MAuth->getdb('ARCHAG')}
					) as b where t.strno = b.strno and b.movedt >= v.recvdt) 
					or t.strno not  in ( select strno from (
						select strno, movedt, moveto from {$this->MAuth->getdb('INVMOVT')} 
						union 
						select strno, ydate as movedt, ylocat as moveto from {$this->MAuth->getdb('ARHOLD')} 
						union 
						select strno, ydate as movedt, ylocat as moveto from {$this->MAuth->getdb('ARCHAG')}
					) as c where c.strno = t.strno and c.movedt >= v.recvdt)) 

					UNION 

					SELECT T.MODEL, T.BAAB, T.COLOR, T.CC, T.STRNO, T.ENGNO, V.RECVNO, V.RECVDT, convert(char,V.RECVDT,112) as RECVDTS, T.CRLOCAT,
					V.INVNO, convert(char,V.INVDT,112) as INVDT, T.NETCOST, T.CRVAT, T.TOTCOST, T.TADDCOST, T.BONUS, T.GCODE, T.RESVNO  
					FROM {$this->MAuth->getdb('INVINVO')} V 
					LEFT JOIN {$this->MAuth->getdb('INVTRAN')} T ON V.RECVNO=T.RECVNO AND V.LOCAT=T.RVLOCAT
					LEFT JOIN(
						select STRNO, MOVEDT, MOVETO, MOVSEQ from {$this->MAuth->getdb('INVMOVT')} 
						union 
						select STRNO, YDATE as MOVEDT, YLOCAT as MOVETO, 10000 as MOVSEQ from {$this->MAuth->getdb('ARHOLD')}       
						union 
						select STRNO, YDATE as MOVEDT, YLOCAT as MOVETO, 10000 as MOVSEQ from {$this->MAuth->getdb('ARCHAG')}
					)as E ON T.STRNO = E.STRNO
					WHERE ((T.CURSTAT <> 'Y' AND T.CURSTAT <> 'R') OR T.CURSTAT IS NULL OR T.CURSTAT <> '') 
					AND (T.SDATE>'".$FRMDATE."' OR T.SDATE IS NULL) AND not exists(
						select STRNO from (
							select STRNO, YDATE as MOVEDT, YLOCAT as MOVETO, SDATE from {$this->MAuth->getdb('ARHOLD')}  
							union 
							select STRNO, YDATE as MOVEDT, YLOCAT as MOVETO, SDATE from {$this->MAuth->getdb('ARCHAG')}
						)as C where C.STRNO = T.STRNO and C.MOVEDT >= V.RECVDT and '".$FRMDATE."' >= C.SDATE and '".$FRMDATE."'< C.MOVEDT
					)  AND (E.MOVETO LIKE '%".$LOCAT1."') ".$cond."
					AND (E.MOVSEQ = (select max(movseq) from {$this->MAuth->getdb('INVMOVT')} where strno=e.strno and movedt<='".$FRMDATE."') or (e.movseq=0) 
					or ((e.movseq=10000) and e.strno not in (select strno from {$this->MAuth->getdb('INVMOVT')} where strno=e.strno and e.movedt=movedt))) 
					and (E.MOVEDT <= '".$FRMDATE."') and (v.recvdt <= e.movedt) 
					GROUP BY  V.RECVNO, V.RECVDT, V.INVNO, V.INVDT, V.TAXNO, V.TAXDT, T.GCODE, T.MOVENO, T.MOVEDT, T.TYPE, T.MODEL,
					T.BAAB, T.COLOR, T.CC, T.STRNO, T.ENGNO, T.KEYNO, T.STAT, T.RVLOCAT, T.CRLOCAT, T.MILERT, T.NETCOST, T.CRVAT,
					T.TOTCOST, T.TADDCOST, T.BONUS, T.RESVNO, E.STRNO, E.MOVEDT  
					HAVING ('".$FRMDATE."' < ( select min(c.movedt) 
					from (
						select strno, movedt, moveto from {$this->MAuth->getdb('INVMOVT')}       
						union 
						select strno, ydate as movedt, ylocat as moveto from {$this->MAuth->getdb('ARHOLD')}       
						union 
						select strno, ydate as movedt, ylocat as moveto from {$this->MAuth->getdb('ARCHAG')}
					)as c where c.strno = e.strno and c.movedt > e.movedt) 
					or e.strno not in ( select strno from(
						select strno, movedt, moveto from {$this->MAuth->getdb('INVMOVT')}  
						union 
						select strno, ydate as movedt, ylocat as moveto from {$this->MAuth->getdb('ARHOLD')} 
						union 
						select strno, ydate as movedt, ylocat as moveto from {$this->MAuth->getdb('ARCHAG')}
					)as c where c.strno = e.strno and c.movedt > e.movedt)) 
				)main
		";//echo $sql; 
		$query = $this->db->query($sql);
		
		$sql = "
				select MODEL, BAAB, COLOR, CC, a.STRNO, ENGNO, RESVNO , RECVNO, RECVDTS, INVNO, INVDT,  
				case when CRLOCAT = 'TRANS' then isnull(TRANSTO,'')+'(รอรับโอน)' collate thai_ci_as else CRLOCAT end as LOCAT
				from #main a
				left join (
					 select b.STRNO, a.TRANSTO 
					 from YTKManagement.dbo.INVTransfers as a
					 left join YTKManagement.dbo.INVTransfersDetails as b on a.TRANSNO=b.TRANSNO collate thai_cs_as
					 where a.TRANSSTAT<>'Cancel' and isnull(b.MOVENO,'') = ''
				)b on a.STRNO = b.STRNO collate thai_ci_as
				order by MODEL, BAAB, COLOR
		";//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$sql = "select 'รวมทั้งหมด  '+convert(nvarchar,COUNT(STRNO))+' คัน' as Total from #main";//echo $sql; exit;
		
		$query2 = $this->db->query($sql);
		
		$head = ""; $html = ""; $i=0; 

		$head = "
				<tr>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>#</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>รุ่น</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>แบบ</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>สี</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:center;'>ขนาด</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>เลขตัวถัง</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>เลขเครื่อง</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>เลขที่ใบจอง</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>เลขที่ใบรับ</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:center;'>วันที่รับ</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>เลขที่ใบส่ง</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:center;'>วันที่ใบส่ง</th>
					<th style='border-bottom:0.1px solid black;vertical-align:top;text-align:left;'>สาขาที่เก็บ</th>
				</tr>
		";
		
		$No = 1;
		if($query->row()){
			foreach($query->result() as $row){	
				$html .= "
					<tr class='trow' seq=".$No.">
						<td style='width:30px;'>".$No++."</td>
						<td style='width:85px;'>".$row->MODEL."</td>
						<td style='width:85px;'>".$row->BAAB."</td>
						<td style='width:110px;'>".$row->COLOR."</td>
						<td style='width:50px;' align='center'>".number_format($row->CC)."</td>
						<td style='width:125px;'>".$row->STRNO."</td>
						<td style='width:120px;'>".$row->ENGNO."</td>
						<td style='width:80px;'>".$row->RESVNO."</td>
						<td style='width:80px;'>".$row->RECVNO."</td>
						<td style='width:80px;' align='center'>".$this->Convertdate(2,$row->RECVDTS)."</td>
						<td style='width:160px;'>".$row->INVNO."</td>
						<td style='width:80px;' align='center'>".$this->Convertdate(2,$row->INVDT)."</td>
						<td style='width:80px;'>".$row->LOCAT."</td>
					</tr>
				";	
			}
		}
		
		if($query2->row()){
			foreach($query2->result() as $row){	
				$html .= "
					<tr class='trow bor' style='background-color:#ebebeb;'>
						<th colspan='13' style='text-align:left;vertical-align:middle;'>".$row->Total."</th>
					</tr>
					
				";	
			}
		}
		
		$mpdf = new \Mpdf\Mpdf([
			'mode' => 'utf-8', 
			'format' => $layout,
			'margin_top' => 10, 	//default = 16
			'margin_left' => 8, 	//default = 15
			'margin_right' => 8, 	//default = 15
			'margin_bottom' => 10, 	//default = 16
			'margin_header' => 9, 	//default = 9
			'margin_footer' => 9, 	//default = 9
		]);
		
		$content = "
			<table class='wf' style='font-size:8pt;height:700px;border-collapse:collapse;line-height:23px;overflow:wrap;vertical-align:text-top;'>
				<tbody>
					<tr>
						<th colspan='13' style='font-size:10pt;'>รายงานสินค้าคงเหลือ (รถ) เพื่อเช็คสต๊อก</th>
					</tr>
					<tr>
						<td colspan='13' style='font-size:9pt;height:35px;border-bottom:0.1px solid black;text-align:center;'>คงเหลือ ณ วันที่ ".$tx[8]." ".$rpcond."</td>
					</tr>
					".$head."
					".$html."
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
			<div class='wf pf' style='".($layout == 'A4' ? 'top:1060;left:600;':'top:715;left:960;')." font-size:6pt;'>".date('d/m/').(date('Y')+543)." ".date('H:i')." หน้า {PAGENO} / {nbpg}</div>
		";
		//<div class='wf pf' style='top:1050;left:580;'>{DATE j-m-Y H:s}  {PAGENO} / {nbpg}</div>
		//$mpdf->AddPage('L');	
		$mpdf->SetHTMLHeader($head);	
		$mpdf->WriteHTML($content);	
		$mpdf->Output();
		
	}
}