<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/********************************************************
             ______@20/07/2020______
			 Pasakorn Boonded

********************************************************/
class Asktypemodelbaabcolor extends MY_Controller {
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
					<div class=' col-sm-3'>	
						<div class='form-group'>
							ยี่ห้อ
							<select id='TYPE' class='form-control input-sm'></select>
						</div>
					</div>
					<div class=' col-sm-3'>	
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
					<div class=' col-sm-3'>	
						<div class='form-group'>
							สี
							<select id='COLOR' class='form-control input-sm'></select>
						</div>
					</div>
					<div class=' col-sm-3'>	
						<div class='form-group'>
							สถานที่เก็บ
							<select id='CRLOCAT' class='form-control input-sm'></select>
						</div>
					</div>
					<div class=' col-sm-2'>	
						<div class='form-group'>
							ขนาด CC >=
							<input id='CC' class='form-control input-sm'>
						</div>
					</div>
					<div class=' col-sm-2'>	
						<div class='form-group'>
							สถานะ (N,O)
							<input id='STAT' class='form-control input-sm'>
						</div>
					</div>
					<div class=' col-sm-2'>	
						<div class='form-group'>
							รวม
							<div class='input-group'>
								<input id='C_CAR' class='form-control input-sm'>
								<span class='input-group-addon'>คัน</span>
							</div>
						</div>
					</div>
					<div class=' col-sm-3'>	
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
		$html .="<script src='".base_url('public/js/SYS02/Asktypemodelbaabcolor.js')."'></script>";
		echo $html;
	}
	function Search(){
		$TYPE     = $_REQUEST['TYPE'];
		$MODEL    = $_REQUEST['MODEL'];
		$BAAB     = $_REQUEST['BAAB'];
		$COLOR    = $_REQUEST['COLOR'];
		$CRLOCAT  = $_REQUEST['CRLOCAT'];
		$CC       = $_REQUEST['CC'];
		$STAT  	  = $_REQUEST['STAT'];
		$html = ""; $cond = "";
		
		if($TYPE !== ""){
			$cond .=" and TYPE like '".$TYPE."%'";
		}
		if($MODEL !== ""){
			$cond .=" and MODEL like '".$MODEL."%'";
		}
		if($BAAB !== ""){
			$cond .=" and BAAB like '".$BAAB."%'";
		}
		if($COLOR !== ""){
			$cond .=" and COLOR like '".$COLOR."%'";
		}
		if($CC !== ""){
			$cond .=" and CC >= ".$CC."";
		}else{
			$cond .=" and CC >= 0";
		}
		if($CRLOCAT !== ""){
			$cond .=" and CRLOCAT like '".$CRLOCAT."%'";
		}
		if($STAT !== ""){
			$cond .=" and STAT like '".$STAT."%'";
		}
		
		$sql = "
			select  
				TYPE,MODEL,BAAB,COLOR,CC,STAT,STRNO,ENGNO,KEYNO,MOVENO
				,convert(varchar(8),MOVEDT,112) as MOVEDT
				,CRLOCAT,RVLOCAT,RVCODE,RECVNO,convert(varchar(8),RECVDT,112) as RECVDT 
				,convert(varchar(8),SDATE,112) as SDATE,TSALE,CONTNO,MEMO1
			from {$this->MAuth->getdb('INVTRAN')} 
			where (CURSTAT <> 'Y' or CURSTAT is null or RECVDT <= CONVERT(varchar(8),GETDATE(),112)) 
			and (SDATE > CONVERT(varchar(8),GETDATE(),112) or SDATE is null) 
			".$cond." order by TYPE,MODEL,BAAB,COLOR
		";
		//echo $sql; exit;
		$i = 0;
		$query = $this->db->query($sql);
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
						<td style='vertical-align:middle;'>".$row->RVCODE."</td>
						<td style='vertical-align:middle;'>".$row->RECVNO."</td>
						<td style='vertical-align:middle;'>".$this->Convertdate(2,$row->RECVDT)."</td>
						<td style='vertical-align:middle;'>".$this->Convertdate(2,$row->SDATE)."</td>
						<td style='vertical-align:middle;'>".$row->TSALE."</td>
						<td style='vertical-align:middle;'>".$row->CONTNO."</td>
						<td style='vertical-align:middle;'>".$row->MEMO1."</td>
					</tr>
				";
			}
		}
		$html = "
			<table id='table-stockreportcar' class='col-sm-12 display table table-striped table-bordered' cellspacing='0' width='99.99%' border=1 style='font-size:8pt;'>
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
		
		$response = array("html"=>$html, "status"=>true);
		$response['C_CAR'] = $i;
		echo json_encode($response);
	}
}