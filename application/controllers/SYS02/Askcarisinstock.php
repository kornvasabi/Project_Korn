<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/********************************************************
             ______@22/07/2020______
			 Pasakorn Boonded

********************************************************/
class Askcarisinstock extends MY_Controller {
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
				<div class='col-sm-12'>
					<div class='col-sm-3'>	
						<div class='form-group'>
							ณ วันที่
							<input type='text' id='TDATE' class='form-control input-sm' placeholder='ถึง'  data-provide='datepicker' data-date-language='th-th' value='".$this->today('today')."'>
						</div>
					</div>
					<div class='col-sm-3'>	
						<div class='form-group'>
							ยี่ห้อ
							<select id='TYPE' class='form-control input-sm'></select>
						</div>
					</div>
					<div class='col-sm-3'>	
						<div class='form-group'>
							รุ่น
							<select id='MODEL' class='form-control input-sm'></select>
						</div>
					</div>
					<div class=' col-sm-3'>	
						<div class='form-group'>
							แบบ
							<select id='BAAB' class='form-control input-sm'></select>
						</div>
					</div>
					<div class=' col-sm-2'>	
						<div class='form-group'>
							สี
							<select id='COLOR' class='form-control input-sm'></select>
						</div>
					</div>
					<div class='col-sm-2'>	
						<div class='form-group'>
							สถานที่เก็บ
							<select id='RVLOCAT' class='form-control input-sm'></select>
						</div>
					</div>
					<div class='col-sm-2'>	
						<div class='form-group'>
							สถานะ (N,O)
							<input id='STAT' class='form-control input-sm'>
						</div>
					</div>
					<div class='col-sm-2'>	
						<div class='form-group'>
							รวม
							<div class='input-group'>
								<input id='C_CAR' class='form-control input-sm'>
								<span class='input-group-addon'>คัน</span>
							</div>
						</div>
					</div>
					<div class='col-sm-2'>	
						<div class='form-group'>
							<br>
							<div style='text-align:center;font-size:14pt;'>***รวมรถจอง***</div>
						</div>
					</div>
					<div class=' col-sm-2'>	
						<div class='form-group'>
							<br>
							<button id='btnsearch' class='btn btn-cyan btn-block'><span class='glyphicon glyphicon-search'> ค้นหา</span></button>
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
		$html .="<script src='".base_url('public/js/SYS02/Askcarisinstock.js')."'></script>";
		echo $html;
	}
	function Search(){
		$response = array();
		$TDATE    = $this->Convertdate(1,$_REQUEST['TDATE']);
		$TYPE     = $_REQUEST['TYPE'];
		$MODEL    = $_REQUEST['MODEL'];
		$BAAB     = $_REQUEST['BAAB'];
		$COLOR    = $_REQUEST['COLOR'];
		
		$RVLOCAT  = $_REQUEST['RVLOCAT'];
		$STAT     = $_REQUEST['STAT'];
		
		$sql = "
			select V.RECVNO,convert(varchar(8),V.RECVDT,112) as RECVDT,V.inVNO,V.inVDT,V.TAXNO,V.TAXDT,T.GCODE,T.MOVENO
				,convert(varchar(8),T.MOVEDT,112) as MOVEDT,T.TYPE,T.MODEL,T.BAAB,T.COLOR,T.CC, T.STRNO,T.ENGNO,T.KEYNO
				,T.STAT,T.RVLOCAT,T.CRLOCAT,T.MILERT,T.NETCOST,T.CRVAT,T.TOTCOST
				,T.TADDCOST,T.BONUS 
			from {$this->MAuth->getdb('INVINVO')} V,{$this->MAuth->getdb('INVTRAN')} T 
			where (V.RECVNO = T.RECVNO) and (V.LOCAT = T.RVLOCAT) and 'A' = 'A' 
			and (T.SDATE > '".$TDATE."' or T.SDATE is null) and not exists(
				select STRNO from (
					select STRNO,YDATE as MOVEDT,YLOCAT as MOVETO,SDATE 
					from {$this->MAuth->getdb('ARHOLD')}  
					union 
					select STRNO,YDATE as MOVEDT,YLOCAT as MOVETO,SDATE 
					from {$this->MAuth->getdb('ARCHAG')}
				) as C 
				where C.STRNO = T.STRNO and C.MOVEDT >= V.RECVDT and '".$TDATE."' >= C.SDATE 
				and '".$TDATE."' < C.MOVEDT
			) and (T.TYPE like '".$TYPE."%') and (T.MODEL like '".$MODEL."%') and (T.BAAB like '".$BAAB."%') 
			and (T.COLOR like '".$COLOR."%')  
			and (T.RVLOCAT like '".$RVLOCAT."%') and (T.STAT like '".$STAT."%') and (V.RECVDT <= '".$TDATE."') 
			group by T.STRNO,T.RECVNO,V.RECVDT,T.RVLOCAT,T.TOTCOST,T.TADDCOST,T.BONUS,T.CRVAT,T.NETCOST,T.MILERT
			,T.CRLOCAT,T.STAT,T.KEYNO,T.ENGNO,T.CC,T.COLOR,T.MOVENO,T.MOVEDT,T.TYPE,T.MODEL,T.BAAB, 
			V.INVDT,V.TAXNO,V.TAXDT,T.GCODE,V.INVNO,V.inVDT,V.RECVNO
			,V.RECVDT HAVING (
				'".$TDATE."' < (
					select  Min(B.MOVEDT) from (
						select STRNO,MOVEDT,MOVETO 
						from {$this->MAuth->getdb('INVMOVT')} 
						union 
						select STRNO,YDATE as MOVEDT,YLOCAT as MOVETO 
						from {$this->MAuth->getdb('ARHOLD')}       
						union 
						select STRNO,YDATE as MOVEDT,YLOCAT as MOVETO 
						from {$this->MAuth->getdb('ARCHAG')}
					) as B where T.STRNO = B.STRNO and B.MOVEDT >= V.RECVDT
				) or 
				T.STRNO not in (
					select STRNO  from (
						select STRNO,MOVEDT,MOVETO 
						from {$this->MAuth->getdb('INVMOVT')}       
						union 
						select STRNO,YDATE as MOVEDT,YLOCAT as MOVETO 
						from {$this->MAuth->getdb('ARHOLD')}       
						union 
						select STRNO,YDATE as MOVEDT,YLOCAT as MOVETO 
						from {$this->MAuth->getdb('ARCHAG')}
					) as C where C.STRNO = T.STRNO and C.MOVEDT >= V.RECVDT
				)
			) 
			union
			select V.RECVNO,convert(varchar(8),V.RECVDT,112) as RECVDT,V.inVNO,V.inVDT,V.TAXNO,V.TAXDT,T.GCODE,T.MOVENO
				,convert(varchar(8),T.MOVEDT,112) as MOVEDT,T.TYPE,T.MODEL,T.BAAB,T.COLOR,T.CC, T.STRNO,T.ENGNO,T.KEYNO
				,T.STAT,T.RVLOCAT,T.CRLOCAT,T.MILERT, T.NETCOST,T.CRVAT,T.TOTCOST
				,T.TADDCOST,T.BONUS 
			from {$this->MAuth->getdb('INVINVO')} V,{$this->MAuth->getdb('INVTRAN')} T,(
				select STRNO,MOVEDT,MOVETO,MOVSEQ from {$this->MAuth->getdb('INVMOVT')}       
				union 
				select STRNO,YDATE as MOVEDT,YLOCAT as MOVETO,10000 as MOVSEQ 
				from {$this->MAuth->getdb('ARHOLD')}       
				union 
				select STRNO,YDATE as MOVEDT,YLOCAT as MOVETO,10000 as MOVSEQ 
				from {$this->MAuth->getdb('ARCHAG')}
			) as E 
			where (V.RECVNO=T.RECVNO) and (V.LOCAT = T.RVLOCAT) and (T.STRNO = E.STRNO) 
			and 'A' = 'A' and (T.SDATE > '".$TDATE."' or T.SDATE is null) and not exists(
				select STRNO from (
					select STRNO,YDATE as MOVEDT,YLOCAT as MOVETO,SDATE 
					from {$this->MAuth->getdb('ARHOLD')}  
					union 
					select STRNO,YDATE as MOVEDT,YLOCAT as MOVETO,SDATE 
					from {$this->MAuth->getdb('ARCHAG')}
				) as C where C.STRNO = T.STRNO and C.MOVEDT >= V.RECVDT 
				and '".$TDATE."' >= C.SDATE and '".$TDATE."' < C.MOVEDT
			) and (T.TYPE like '".$TYPE."%') and (T.MODEL like '".$MODEL."%') 
			and (T.BAAB like '".$BAAB."%') and (T.COLOR like '".$COLOR."%') 
			and (E.MOVETO like '".$RVLOCAT."%') and (T.STAT like '".$STAT."%')   
			and (
				E.MOVSEQ = (
					select MAX(MOVSEQ) from {$this->MAuth->getdb('INVMOVT')} 
					where STRNO = E.STRNO and MOVEDT <= '".$TDATE."'
				) 
				or (E.MOVSEQ=0) or (
					(E.MOVSEQ = 10000) and E.STRNO not in (
						select STRNO from {$this->MAuth->getdb('INVMOVT')} 
						where STRNO = E.STRNO and E.MOVEDT = MOVEDT
					)
				)  
			) and (E.MOVEDT <= '".$TDATE."') and (V.RECVDT <= E.MOVEDT) 
			group by T.STRNO,T.RECVNO,V.RECVDT,T.RVLOCAT,T.TOTCOST,T.TADDCOST,T.BONUS,T.CRVAT
			,T.NETCOST,T.MILERT,T.CRLOCAT,T.STAT,T.KEYNO,T.ENGNO,T.CC,T.COLOR,T.MOVENO,T.MOVEDT
			,T.TYPE,T.MODEL,T.BAAB,V.INVDT,V.TAXNO,V.TAXDT,T.GCODE,V.INVNO,V.INVDT,V.RECVNO
			,V.RECVDT,E.STRNO,E.MOVEDT HAVING (
				'".$TDATE."' < (
					select Min(C.MOVEDT) from (
						select STRNO,MOVEDT,MOVETO 
						from {$this->MAuth->getdb('INVMOVT')} 
						union 
						select STRNO,YDATE as MOVEDT,YLOCAT as MOVETO 
						from {$this->MAuth->getdb('ARHOLD')}      
						union 
						select STRNO,YDATE as MOVEDT,YLOCAT as MOVETO 
						from {$this->MAuth->getdb('ARCHAG')}
					) as C where C.STRNO = E.STRNO and C.MOVEDT > E.MOVEDT
				) or E.STRNO  not in (
					select STRNO  from (
						select STRNO,MOVEDT,MOVETO 
						from {$this->MAuth->getdb('INVMOVT')}      
						union 
						select STRNO,YDATE as MOVEDT,YLOCAT as MOVETO 
						from {$this->MAuth->getdb('ARHOLD')}       
						union 
						select STRNO,YDATE as MOVEDT,YLOCAT as MOVETO 
						from {$this->MAuth->getdb('ARCHAG')}
					) as C where C.STRNO = E.STRNO 
					and C.MOVEDT > E.MOVEDT
				)
			) 
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		$html = ""; $i = "";
		if($query->row()){
			foreach($query->result() as $row){$i++;
				$html .="
					<tr>
						<td style='vertical-align:middle;'>".$row->TYPE."</td>
						<td style='vertical-align:middle;'>".$row->MODEL."</td>
						<td style='vertical-align:middle;'>".$row->BAAB."</td>
						<td style='vertical-align:middle;'>".$row->COLOR."</td>
						<td style='vertical-align:middle;'>".$row->CC."</td>
						<td style='vertical-align:middle;'>".$row->STAT."</td>
						<td style='vertical-align:middle;'>".$row->STRNO."</td>
						<td style='vertical-align:middle;'>".$row->ENGNO."</td>
						<td style='vertical-align:middle;'>".$row->KEYNO."</td>
						<td style='vertical-align:middle;'>".$row->MOVENO."</td>
						<td style='vertical-align:middle;'>".$this->Convertdate(2,$row->MOVEDT)."</td>
						<td style='vertical-align:middle;'>".$row->CRLOCAT."</td>
						<td style='vertical-align:middle;'>".$row->RVLOCAT."</td>
						<td style='vertical-align:middle;'></td>
						<td style='vertical-align:middle;'>".$row->RECVNO."</td>
						<td style='vertical-align:middle;'>".$this->Convertdate(2,$row->RECVDT)."</td>
						<td style='vertical-align:middle;'></td>
						<td style='vertical-align:middle;'></td>
						<td style='vertical-align:middle;'></td>
						<td style='vertical-align:middle;'></td>
					</tr>
				";	
			}
		}
		$html = "
			<table id='table-carisstock' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='99.99%' border=1 style='font-size:8pt;'>
				<thead style='background: rgba(0, 0, 0, 0) url(&#39;../public/lobiadmin-master/version/1.0/ajax/img/bg/bg6.png&#39;) repeat scroll 0% 0%;'>
					<tr style='line-height:20px;'>
						<td style='vertical-align:middle;text-align:center;font-size:8pt;' colspan='20'>
							เงื่อนไข :: 
						</td>
					</tr>
					<tr>
						<th style='vertical-align:middle;'>ยี่ห้อ</th>
						<th style='vertical-align:middle;'>รุ่น</th>
						<th style='vertical-align:middle;'>แบบ</th>
						<th style='vertical-align:middle;'>สี</th>
						<th style='vertical-align:middle;'>CC</th>
						<th style='vertical-align:middle;'>สถานะ</th>
						<th style='vertical-align:middle;'>หมายเลขถัง</th>
						<th style='vertical-align:middle;'>เลขเครื่อง</th>
						<th style='vertical-align:middle;'>เลขกุญแจ</th>
						<th style='vertical-align:middle;'>เลขที่แจ้งย้าย</th>
						<th style='vertical-align:middle;'>วันที่แจ้งย้าย</th>
						<th style='vertical-align:middle;'>สถานที่นับ</th>
						<th style='vertical-align:middle;'>สถานที่รับ</th>
						<th style='vertical-align:middle;'>รหัสผู้รับ</th>
						<th style='vertical-align:middle;'>เลขที่รับ</th>
						<th style='vertical-align:middle;'>วันที่รับ</th>
						<th style='vertical-align:middle;'>วันที่ขาย</th>
						<th style='vertical-align:middle;'>ประเภทขาย</th>
						<th style='vertical-align:middle;'>เลขที่สัญญา</th>
						<th style='vertical-align:middle;'>หมายเหตุ</th>
					</tr>
				</thead>	
				<tbody>						
					".$html."
				</tbody>
			</table>
		";
		
		$response = array("html"=>$html,"status"=>true);
		$response['C_CAR'] = $i;
		echo json_encode($response);
	}
}