<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/********************************************************
             ______@08/03/2019______
            / / _ _   _ _     __ 
           / // __ \ / __ \ / __ \
       _ _/ // /_/ // / / // /_/ /
     /_ _ _/ \_ _ //_/ /_/ \__  /
                          _ _/ /
                         /___ /
********************************************************/
class Question extends MY_Controller {
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
	
	function customers(){
		$claim = $this->MLogin->getclaim(uri_string());
		if($claim['m_access'] != "T"){ echo "<div align='center' style='color:red;font-size:16pt;width:100%;'>ขออภัย คุณยังไม่มีสิทธิเข้าใช้งานหน้านี้ครับ</div>"; exit; }
		
		$data = "";
		$html = "
			<div class='tab1' name='home' locat='{$this->sess['branch']}' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}'>
				<div class='col-sm-12'>
					<div class='row'>
						<div class=' col-sm-2'>	
							<div class='form-group'>
								รหัสลูกค้า
								<input type='text' id='CUSCOD' class='form-control input-sm' placeholder='รหัสลูกค้า' >
							</div>
						</div>
						<div class=' col-sm-4'>	
							<div class='form-group'>
								ชื่อ-สกุล ลูกค้า
								<input type='text' id='CUSNAME' class='form-control input-sm' placeholder='ชื่อ-สกุล ลูกค้า' >
							</div>
						</div>
						
						<div class='col-sm-2 col-sm-offset-4'>	
							<div class='form-group'><br>
								<button id='btnSearch' class='btn btn-primary btn-block'><span class='glyphicon glyphicon-search'> ค้นหา</span></button>
							</div>
						</div>
					</div>
					
					<div class='row'>	
						<div id='jd_result' class='col-sm-12'></div>
					</div>				
				</div>
			</div>
		";
		
		$html.= "<script src='".base_url('public/js/SYS04/QuestionCustomers.js')."'></script>";
		echo $html;
	}	
	
	function customer_search(){
		$arrs = array();
		$arrs["CUSCOD"]  = $_POST["CUSCOD"];
		$arrs["CUSNAME"] = $_POST["CUSNAME"];
		
		$cond = "";
		if($arrs["CUSCOD"] != ""){
			$cond .= " and a.CUSCOD like '".$arrs["CUSCOD"]."'";
		}
		if($arrs["CUSNAME"] != ""){
			$cond .= " and a.NAME1+' '+a.NAME2 like '".$arrs["CUSNAME"]."'";
		}
		
		$sql = "
			select top 20 a.CUSCOD,a.SNAM+a.NAME1+' '+a.NAME2 as CUSNAME,MAXCRED,MOBILENO
			from {$this->MAuth->getdb('CUSTMAST')} a
			where 1=1 ".$cond."
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$html .= "
					<tr>
						<td>
							<i class='cusdetail btn btn-xs btn-success glyphicon glyphicon-zoom-in' CUSCOD='".$row->CUSCOD."' style='cursor:pointer;'> รายละเอียด  </i>
						</td>
						<td>".$row->CUSCOD."</td>
						<td>".$row->CUSNAME."</td>
						<td align='right'>".number_format($row->MAXCRED,2)."</td>
						<td>".$row->MOBILENO."</td>
					</tr>
				";
			}
		}
		
		$html = "
			<div id='table-fixed-HSearch' class='col-sm-12' style='height:calc(100% - 30px);width:100%;overflow:auto;font-size:8pt;'>
				<table id='table-HSearch' class='table table-bordered' cellspacing='0' width='calc(100% - 1px)' style='background: rgba(0, 0, 0, 0) url(&#39;../public/lobiadmin-master/version/1.0/ajax/img/bg/bg4.png&#39;) repeat scroll 0% 0%;'>
					<thead style='background: rgba(0, 0, 0, 0) url(&#39;../public/lobiadmin-master/version/1.0/ajax/img/bg/bg6.png&#39;) repeat scroll 0% 0%;'>
						<tr align='center' style='line-height:20px;'>
							<th style='vertical-align:middle;text-align:center;font-size:8pt;' colspan='14'>
								เงื่อนไข 
							</th>
						</tr>
						<tr align='center'>
							<th>###</th>
							<th style='vertical-align:middle;'>รหัสลูกค้า</th>
							<th style='vertical-align:middle;'>ลูกค้า</th>
							<th style='vertical-align:middle;'>วงเงินเครดิต</th>
							<th style='vertical-align:middle;'>เบอร์ติดต่อ</th>
						</tr>
					</thead>	
					<tbody>
						".$html."
					</tbody>
				</table>
			</div>
			<div id='table-fixed-HSearch-detail' class='col-sm-12' style='height:calc(100% - 30px);width:100%;overflow:auto;font-size:8pt;'>
			
			</div>
		";
		
		$response = array("html"=>$html,"status"=>true);
		echo json_encode($response);
	}
	
	function customer_detail(){
		$cusdetail 	= $this->cusdetail();
		$contno 	= $this->cuscontno();
		$uncontno 	= $this->cusuncontno();
		
		$html = "
			<div class='col-sm-12'>
				<div class='row'>
					<div class='col-sm-12' style='min-height:calc(50vh - 80px);'>
						{$cusdetail}
					</div>
				</div>
				<div class='row'>
					<div class='col-sm-6' style='max-height:calc(50vh);min-height:calc(50vh);overflow:auto;'>
						<table id='table-sale' class='table table-bordered' cellspacing='0' style='font-size:8pt;background: rgba(0, 0, 0, 0) url(&#39;../public/lobiadmin-master/version/1.0/ajax/img/bg/bg4.png&#39;) repeat scroll 0% 0%;'>
							<thead style='background: rgba(0, 0, 0, 0) url(&#39;../public/lobiadmin-master/version/1.0/ajax/img/bg/bg6.png&#39;) repeat scroll 0% 0%;'>
								<tr align='center'>
									<th class='text-primary' colspan='8' style='vertical-align:middle;text-align:center;'>
										รายการบัญชีที่ซื้อ
									</th>
								</tr>
								<tr align='center'>
									<th style='vertical-align:middle;'>เลขที่สัญญา</th>
									<th style='vertical-align:middle;'>สาขา</th>
									<th style='vertical-align:middle;'>ประเภทการขาย</th>
									<th style='vertical-align:middle;'>เลขตัวถัง</th>
									<th style='vertical-align:middle;'>วดป.ขาย</th>
									<th style='vertical-align:middle;'>ลูกหนี้คงเหลือ</th>
								</tr>
							</thead>	
							<tbody>
								{$contno}
							</tbody>
						</table>
					</div>
					<div class='col-sm-6' style='max-height:calc(50vh);min-height:calc(50vh);overflow:auto;'>
						<table id='table-unsale' class='table table-bordered' cellspacing='0' style='font-size:8pt;background: rgba(0, 0, 0, 0) url(&#39;../public/lobiadmin-master/version/1.0/ajax/img/bg/bg4.png&#39;) repeat scroll 0% 0%;'>
							<thead style='background: rgba(0, 0, 0, 0) url(&#39;../public/lobiadmin-master/version/1.0/ajax/img/bg/bg6.png&#39;) repeat scroll 0% 0%;'>
								<tr align='center'>
									<th class='text-danger' colspan='8' style='vertical-align:middle;text-align:center;'>
										บัญชีที่เลิกใช้แล้ว
									</th>
								</tr>
								<tr align='center'>
									<th style='vertical-align:middle;'>เลขที่สัญญา</th>
									<th style='vertical-align:middle;'>สาขา</th>
									<th style='vertical-align:middle;'>ประเภทการขาย</th>
									<th style='vertical-align:middle;'>เลขตัวถัง</th>
									<th style='vertical-align:middle;'>วดป.ขาย</th>
									<th style='vertical-align:middle;'>วดป.เลิกใช้</th>
									<th style='vertical-align:middle;'>เลิกใช้โดย</th>
									<th style='vertical-align:middle;'>สาเหตุเลิกใช้</th>
								</tr>
							</thead>	
							<tbody>
								{$uncontno}
							</tbody>
						</table>
					</div>
				</div>
			</div>
			
			<style>
				.height25 {
					height: 25px;
				}
			</style>
		";
		$response = array("html"=>$html,"status"=>true);
		echo json_encode($response);
	}
	
	function cusdetail(){
		$cuscod = @$_POST["cuscod"];
		
		$sql = "
			select top 20 a.CUSCOD,a.SNAM+a.NAME1+' '+a.NAME2+(case when a.NICKNM='' then '' else ' ('+a.NICKNM+')' end) as CUSNAME
				,convert(varchar(8),a.BIRTHDT,112) as BIRTHDT
				,datediff(year,a.BIRTHDT,getdate()) as AGE
				,a.IDCARD,a.IDNO,'['+a.GRADE+'] '+b.GRDDES as GRADE
				,a.MAXCRED,(
					select SUM(BALAR) as TOTAL from (
						SELECT TOTPRC-SMPAY AS BALAR FROM {$this->MAuth->getdb('ARMAST')} aa WHERE aa.CUSCOD=a.CUSCOD  
						UNION SELECT TOTPRC-SMPAY AS BALAR FROM {$this->MAuth->getdb('ARCRED')} aa WHERE aa.CUSCOD=a.CUSCOD
						UNION SELECT TOTPRC-SMPAY AS BALAR FROM {$this->MAuth->getdb('ARFINC')} aa WHERE aa.CUSCOD=a.CUSCOD
						UNION SELECT TOTPRC-SMPAY AS BALAR FROM {$this->MAuth->getdb('AR_INVOI')} aa WHERE aa.CUSCOD=a.CUSCOD
						UNION SELECT PAYAMT-SMPAY AS BALAR FROM {$this->MAuth->getdb('AROTHR')} aa WHERE aa.CUSCOD=a.CUSCOD
					) as data
				) as USECRED
			from {$this->MAuth->getdb('CUSTMAST')} a
			left join {$this->MAuth->getdb('SETGRADCUS')} b on a.GRADE=b.GRDCOD			
			where 1=1 and a.CUSCOD='".$cuscod."'
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				
				$sql = "
					select a.ADDRNO 
						,'บ้านเลขที่ '+a.ADDR1
							+' ถนน'+a.ADDR2
							+' ตำบล'+a.TUMB
							+' อำเภอ'+b.AUMPDES
							+' จังหวัด'+c.PROVDES
							+' รหัสไปรษณีย์ '+a.ZIP
							+' โทรศัพท์ '+a.TELP as ADDRDetail
					from {$this->MAuth->getdb('CUSTADDR')} a
					left join {$this->MAuth->getdb('SETAUMP')} b on a.AUMPCOD=b.AUMPCOD
					left join {$this->MAuth->getdb('SETPROV')} c on b.PROVCOD=c.PROVCOD
					where 1=1 and a.CUSCOD='".$row->CUSCOD."'
					order by ADDRNO
				";
				$query = $this->db->query($sql);
				
				$address = "";
				if($query->row()){
					foreach($query->result() as $row_addr){
						$address .= "
							<tr>
								<td>".$row_addr->ADDRNO."</td>
								<td>".$row_addr->ADDRDetail."</td>
							</tr>
						";
					}
				}
				
				$html .= "
					<div class='col-sm-6'>
						<div class='row'>
							<div class='col-sm-4 form-group'>
								รหัสลูกค้า
								<div class='form-control height25'>".$row->CUSCOD."</div>
							</div>
							<div class='col-sm-8 form-group'>
								ชื่อ-สกุล ลูกค้า
								<div class='form-control height25'>".$row->CUSNAME."</div>
							</div>
						</div>
						<div class='row'>
							<div class='col-sm-4 form-group'>
								วดป.เกิด
								<div class='form-control height25'>".$this->Convertdate(2,$row->BIRTHDT)." ".($row->AGE == "" ? "":"(".$row->AGE." ปี)")."</div>
							</div>
							<div class='col-sm-4 form-group'>
								".$row->IDCARD."
								<div class='form-control height25'>".$row->IDNO."</div>
							</div>
							<div class='col-sm-4 form-group'>
								เกรดลูกค้า
								<div class='form-control height25'>".$row->GRADE."</div>
							</div>
						</div>
						<div class='row'>
							<div class='col-sm-4 form-group'>
								วงเงินเครดิต
								<div class='form-control height25'>".number_format($row->MAXCRED,2)."</div>
							</div>
							<div class='col-sm-4 form-group'>
								ใชไปแล้ว
								<div class='form-control height25'>".number_format($row->USECRED,2)."</div>
							</div>
							<div class='col-sm-4 form-group'>
								คงเหลือเครดิต
								<div class='form-control height25'>".number_format(($row->MAXCRED - $row->USECRED),2)."</div>
							</div>
						</div>
					</div>
					<div class='col-sm-6' style='overflow:auto;'>
						<table id='table-address' class='table table-bordered' cellspacing='0' style='font-size:8pt;background: rgba(0, 0, 0, 0) url(&#39;../public/lobiadmin-master/version/1.0/ajax/img/bg/bg2.png&#39;) repeat scroll 0% 0%;'>
							<thead style='background: rgba(0, 0, 0, 0) url(&#39;../public/lobiadmin-master/version/1.0/ajax/img/bg/bg6.png&#39;) repeat scroll 0% 0%;'>
								<tr align='center'>
									<th style='vertical-align:middle;'>ลำดับ</th>
									<th style='vertical-align:middle;'>ที่อยู่</th>
								</tr>
							</thead>	
							<tbody>
								".$address."
							</tbody>
						</table>
					</div>
				";
			}
		}
		
		return $html;
	}
	
	function cuscontno(){
		$cuscod = @$_POST["cuscod"];
		$sql = "
			declare @cuscod varchar(12) = '".$cuscod."'
			select * from (
				select B.LOCAT,B.CUSCOD,B.CONTNO,'ขายผ่อน' as TSALE,B.STRNO
					,convert(varchar(8),B.SDATE,112) as SDATE,(B.TOTPRC-B.SMPAY) AS TOTPRC  
				from {$this->MAuth->getdb('ARMAST')} B
				left join {$this->MAuth->getdb('INVTRAN')} C on B.STRNO = C.STRNO
				WHERE  B.CUSCOD LIKE @cuscod AND B.CONTNO LIKE '%' AND B.STRNO LIKE '%'

				UNION 
				select B.LOCAT,B.CUSCOD,B.CONTNO,'ขายส่งไฟแนนซ์' as TSALE,B.STRNO
					,convert(varchar(8),B.SDATE,112) as SDATE,(B.TOTPRC-B.SMPAY) AS TOTPRC  
				from {$this->MAuth->getdb('ARFINC')} B
				left join {$this->MAuth->getdb('INVTRAN')} C on B.STRNO = C.STRNO
				WHERE B.CUSCOD LIKE @cuscod AND B.CONTNO LIKE '%' AND B.STRNO LIKE '%'

				UNION 
				select B.LOCAT,B.CUSCOD,B.CONTNO, 'ขายสด' as TSALE,B.STRNO
					,convert(varchar(8),B.SDATE,112) as SDATE,(B.TOTPRC-B.SMPAY) AS TOTPRC  
				from {$this->MAuth->getdb('ARCRED')} B
				left join {$this->MAuth->getdb('INVTRAN')} C on B.STRNO = C.STRNO
				WHERE B.CUSCOD LIKE @cuscod AND B.CONTNO LIKE '%' AND B.STRNO LIKE '%'

				UNION 
				select B.LOCAT,A.CUSCOD,B.CONTNO, 'ขายส่งเอเย่นต์' as TSALE,B.STRNO
					,convert(varchar(8),A.SDATE,112) as SDATE,(B.TOTPRC-B.SMPAY) AS TOTPRC  
				from {$this->MAuth->getdb('AR_INVOI')} A
				left join {$this->MAuth->getdb('AR_TRANS')} B on A.CONTNO = B.CONTNO
				left join {$this->MAuth->getdb('INVTRAN')} C on B.STRNO = C.STRNO
				WHERE A.CUSCOD LIKE @cuscod AND B.CONTNO LIKE '%' AND B.STRNO LIKE '%' 

				UNION  
				select B.LOCAT,B.CUSCOD,B.CONTNO, 'ลูกหนี้อื่น' as TSALE,'' AS STRNO
					,convert(varchar(8),B.ARDATE,112) as SDATE,B.BALANCE AS TOTPRC 
				from {$this->MAuth->getdb('AROTHR')} B   
				WHERE  B.CUSCOD LIKE @cuscod AND B.CONTNO LIKE '%' AND B.USERID LIKE '%'
				
				UNION  
				select B.LOCAT,B.CUSCOD,B.CONTNO, 'ขายอุปกรณ์เสริม' as TSALE,'' AS STRNO
					,convert(varchar(8),B.SDATE,112) as SDATE,(B.OPTCVT - B.SMPAY)  AS TOTPRC  
				from {$this->MAuth->getdb('AROPTMST')} B   
				WHERE  B.CUSCOD LIKE @cuscod AND B.CONTNO LIKE '%' AND B.USERID LIKE '%'
			) as data
			order by SDATE
		";
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$html .= "
					<tr>
						<td>".$row->CONTNO."</td>
						<td>".$row->LOCAT."</td>
						<td>".$row->TSALE."</td>
						<td>".$row->STRNO."</td>
						<td>".$this->Convertdate(2,$row->SDATE)."</td>
						<td class='text-right'>".number_format($row->TOTPRC,2)."</td>
					</tr>
				"; 
			}
		}
		
		return $html;
	}
	
	function cusuncontno(){
		$cuscod = @$_POST["cuscod"];
		$sql = "
			declare @cuscod varchar(12) = '".$cuscod."'		
			select * from (
				select B.LOCAT,B.CUSCOD,B.CONTNO,'ขายผ่อน' as TSALE,B.STRNO
					,convert(varchar(8),B.SDATE,112) as SDATE
					,convert(varchar(8),D.CLDATE,112) as DELDT
					,D.USERID AS DELID   ,'ชำระเงินครบ' as CLOSEAR 
				from {$this->MAuth->getdb('HARMAST')} B
				left join {$this->MAuth->getdb('HINVTRAN')} C on B.STRNO = C.STRNO
				left join {$this->MAuth->getdb('ARCLOSE')} D on B.CONTNO = D.CONTNO
				WHERE B.CUSCOD LIKE @cuscod AND B.CONTNO LIKE '%' AND B.STRNO LIKE '%'

				UNION 
				select B.LOCAT,B.CUSCOD,B.CONTNO,'ขายผ่อน' as TSALE,B.STRNO
					,convert(varchar(8),B.SDATE,112) as SDATE
					,convert(varchar(8),D.YDATE,112) as DELDT					
					,B.DELID  ,'ยึดเปลี่ยนสภาพ' as CLOSEAR 
				from {$this->MAuth->getdb('HARMAST')} B
				left join {$this->MAuth->getdb('INVTRAN')} C on B.STRNO = C.STRNO
				left join {$this->MAuth->getdb('ARHOLD')} D on B.CONTNO = D.CONTNO
				WHERE B.CUSCOD LIKE @cuscod AND B.CONTNO LIKE '%' AND B.STRNO LIKE '%'

				UNION 
				select B.LOCAT,B.CUSCOD,B.CONTNO,'ขายผ่อน' as TSALE,B.STRNO
					,convert(varchar(8),B.SDATE,112) as SDATE
					,convert(varchar(8),D.YDATE,112) as DELDT					
					,D.CHECKER  As DELID ,'แลกเปลี่ยน' as CLOSEAR 
				from {$this->MAuth->getdb('HARMAST')} B
				left join {$this->MAuth->getdb('INVTRAN')} C on B.STRNO = C.STRNO
				left join {$this->MAuth->getdb('ARCHAG')} D on B.CONTNO = D.CONTNO
				WHERE B.CUSCOD LIKE @cuscod AND B.CONTNO LIKE '%' AND B.STRNO LIKE '%'      

				UNION 
				select B.LOCAT,B.CUSCOD,B.CONTNO,'ขายผ่อน' as TSALE,B.STRNO
					,convert(varchar(8),B.SDATE,112) as SDATE
					,convert(varchar(8),D.LOSTDT,112) as DELDT
					,D.LOSTCOD AS DELID  ,'หนี้สูญ' as CLOSEAR 
				from {$this->MAuth->getdb('HARMAST')} B
				left join {$this->MAuth->getdb('INVTRAN')} C on B.STRNO = C.STRNO
				left join {$this->MAuth->getdb('ARLOST')} D on B.CONTNO = D.CONTNO
				WHERE B.CUSCOD LIKE @cuscod AND B.CONTNO LIKE '%' AND B.STRNO LIKE '%'      

				UNION 
				select B.LOCAT,B.CUSCOD,B.CONTNO,'ขายส่งไฟแนนซ์' as TSALE,B.STRNO
					,convert(varchar(8),B.SDATE,112) as SDATE
					,convert(varchar(8),D.CLDATE,112) as DELDT
					,D.USERID AS DELID ,'ชำระเงินครบ' as CLOSEAR 
				from {$this->MAuth->getdb('HARFINC')} B
				left join {$this->MAuth->getdb('HINVTRAN')} C on B.STRNO = C.STRNO
				left join {$this->MAuth->getdb('ARCLOSE')} D on B.CONTNO = D.CONTNO
				WHERE B.CUSCOD LIKE @cuscod AND B.CONTNO LIKE '%' AND B.STRNO LIKE '%'     

				UNION 
				select B.LOCAT,B.CUSCOD,B.CONTNO, 'ขายสด' as TSALE,B.STRNO
					,convert(varchar(8),B.SDATE,112) as SDATE
					,convert(varchar(8),D.CLDATE,112) as DELDT
					,D.USERID AS DELID  ,'ชำระเงินครบ' as CLOSEAR 
				from {$this->MAuth->getdb('HARCRED')} B
				left join {$this->MAuth->getdb('HINVTRAN')} C on B.STRNO = C.STRNO
				left join {$this->MAuth->getdb('ARCLOSE')} D on B.CONTNO = D.CONTNO
				WHERE B.CUSCOD LIKE @cuscod AND B.CONTNO LIKE '%' AND B.STRNO LIKE '%'    

				UNION 
				select B.LOCAT,A.CUSCOD,B.CONTNO, 'ขายส่งเอเย่นต์' as TSALE,B.STRNO
					,convert(varchar(8),A.SDATE,112) as SDATE
					,convert(varchar(8),D.CLDATE,112) as DELDT
					,D.USERID AS DELID  ,'ชำระเงินครบ' as CLOSEAR
				from {$this->MAuth->getdb('HAR_INVO')} A
				left join {$this->MAuth->getdb('HAR_TRNS')} B on A.CONTNO = B.CONTNO
				left join {$this->MAuth->getdb('HINVTRAN')} C on B.STRNO = C.STRNO
				left join {$this->MAuth->getdb('ARCLOSE')} D on A.CONTNO = D.CONTNO 
				WHERE A.CUSCOD LIKE @cuscod AND B.CONTNO LIKE '%' AND B.STRNO LIKE '%' 
			) as data
			where DELDT is not null
			order by SDATE
		";
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$html .= "
					<tr>
						<td>".$row->CONTNO."</td>
						<td>".$row->LOCAT."</td>
						<td>".$row->TSALE."</td>
						<td>".$row->STRNO."</td>
						<td>".$this->Convertdate(2,$row->SDATE)."</td>
						<td>".$this->Convertdate(2,$row->DELDT)."</td>
						<td>".$row->DELID."</td>
						<td>".$row->CLOSEAR."</td>
					</tr>
				"; 
			}
		}
		
		return $html;
	}
	
	function customers_ins(){
		$claim = $this->MLogin->getclaim(uri_string());
		if($claim['m_access'] != "T"){ echo "<div align='center' style='color:red;font-size:16pt;width:100%;'>ขออภัย คุณยังไม่มีสิทธิเข้าใช้งานหน้านี้ครับ</div>"; exit; }
		
		$html = "
			<div class='tab1' name='home' locat='{$this->sess['branch']}' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}'>
				<div class='col-sm-12'>
					<div class='row'>
						<div class=' col-sm-2'>	
							<div class='form-group'>
								รหัสลูกค้า
								<input type='text' id='CUSCOD' class='form-control input-sm' placeholder='รหัสลูกค้า' >
							</div>
						</div>
						<div class=' col-sm-4'>	
							<div class='form-group'>
								ชื่อ-สกุล ลูกค้า
								<input type='text' id='CUSNAME' class='form-control input-sm' placeholder='ชื่อ-สกุล ลูกค้า' >
							</div>
						</div>
						
						<div class='col-sm-2 col-sm-offset-4'>	
							<div class='form-group'><br>
								<button id='btnSearch' class='btn btn-primary btn-block'><span class='glyphicon glyphicon-search'> ค้นหา</span></button>
							</div>
						</div>
					</div>
					
					<div class='row'>	
						<div id='jd_result' class='col-sm-12'></div>
					</div>				
				</div>
			</div>
		";
		
		$html.= "<script src='".base_url('public/js/SYS04/QuestionCustomersINS.js')."'></script>";
		echo $html;
	}
	function customer_ins_detail(){
		$cusdetail 	= $this->cusdetail();
		$cusins 	= $this->cusins();		
		
		$html = "
			<div class='col-sm-12'>
				<div class='row'>
					<div class='col-sm-12' style='min-height:calc(50vh - 80px);'>
						{$cusdetail}
					</div>
				</div>
				<div class='row'>
					<div class='col-sm-6' style='max-height:calc(50vh);min-height:calc(50vh);overflow:auto;'>
						<table id='table-ins' class='table table-bordered' cellspacing='0' style='font-size:8pt;background: rgba(0, 0, 0, 0) url(&#39;../public/lobiadmin-master/version/1.0/ajax/img/bg/bg4.png&#39;) repeat scroll 0% 0%;'>
							<thead style='background: rgba(0, 0, 0, 0) url(&#39;../public/lobiadmin-master/version/1.0/ajax/img/bg/bg6.png&#39;) repeat scroll 0% 0%;'>
								<tr align='center'>
									<th class='text-primary' colspan='9' style='vertical-align:middle;text-align:center;'>
										บัญชีที่ค้ำประกัน
									</th>
								</tr>
								<tr align='center'>
									<th style='vertical-align:middle;'>เลขที่สัญญา</th>
									<th style='vertical-align:middle;'>สาขา</th>
									<th style='vertical-align:middle;'>รหัสลูกค้า</th>
									<th style='vertical-align:middle;'>ชื่อ-สกุล</th>
									<th style='vertical-align:middle;'>ประเภทการขาย</th>
									<th style='vertical-align:middle;'>เลขตัวถัง</th>
									<th style='vertical-align:middle;'>วดป.ขาย</th>
									<th style='vertical-align:middle;'>ลูกหนี้คงเหลือ</th>
									<th style='vertical-align:middle;'>ความสัมพันธ์</th>
								</tr>
							</thead>	
							<tbody>
								{$cusins}
							</tbody>
						</table>
					</div>
				</div>
			</div>
			
			<style>
				.height25 {
					height: 25px;
				}
			</style>
		";
		$response = array("html"=>$html,"status"=>true);
		echo json_encode($response);
	}
	
	function cusins(){
		$cuscod = @$_POST["cuscod"];
		$sql = "
			declare @cuscod varchar(12) = '".$cuscod."'		
			
			select * from (
				select B.LOCAT,B.CUSCOD,E.NAME1+' '+E.NAME2 as CUSNAME,B.CONTNO,'ขายผ่อน' as TSALE,B.STRNO
					,convert(varchar(8),B.SDATE,112) as SDATE,D.RELATN AS SALCOD
					,(B.TOTPRC-B.SMPAY) AS TOTPRC ,D.CUSCOD AS GARCODE 
				from {$this->MAuth->getdb('ARMAST')} B
				left join {$this->MAuth->getdb('ARMGAR')} D on B.CONTNO = D.CONTNO AND B.LOCAT = D.LOCAT AND B.TSALE = D.TSALE
				left join {$this->MAuth->getdb('CUSTMAST')} E on B.CUSCOD=E.CUSCOD  
				WHERE D.CUSCOD LIKE @cuscod 

				UNION 
				select B.LOCAT,B.CUSCOD,E.NAME1+' '+E.NAME2 as CUSNAME,B.CONTNO,'ขายผ่อน' as TSALE,B.STRNO
					,convert(varchar(8),B.SDATE,112) as SDATE,D.RELATN AS SALCOD
					,(B.TOTPRC-B.SMPAY) AS TOTPRC ,D.CUSCOD AS GARCODE 
				from {$this->MAuth->getdb('HARMAST')} B
				left join {$this->MAuth->getdb('ARMGAR')} D on B.CONTNO = D.CONTNO AND B.LOCAT = D.LOCAT AND B.TSALE = D.TSALE
				left join {$this->MAuth->getdb('CUSTMAST')} E on B.CUSCOD=E.CUSCOD  
				WHERE D.CUSCOD LIKE @cuscod 
			) as data
			order by SDATE
		";
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$html .= "
					<tr>
						<td>".$row->CONTNO."</td>
						<td>".$row->LOCAT."</td>
						<td>".$row->CUSCOD."</td>
						<td>".$row->CUSNAME."</td>
						<td>".$row->TSALE."</td>
						<td>".$row->STRNO."</td>
						<td>".$this->Convertdate(2,$row->SDATE)."</td>
						<td>".number_format($row->TOTPRC,2)."</td>
						<td>".$row->SALCOD."</td>
					</tr>
				"; 
			}
		}
		
		return $html;
	}
	/*
	function group_ins(){
		echo "อยู่ระหว่างการพัฒนาโปรแกรม"; exit;
		$claim = $this->MLogin->getclaim(uri_string());
		if($claim['m_access'] != "T"){ echo "<div align='center' style='color:red;font-size:16pt;width:100%;'>ขออภัย คุณยังไม่มีสิทธิเข้าใช้งานหน้านี้ครับ</div>"; exit; }
		
		$html = "
			<div class='tab1' name='home' locat='{$this->sess['branch']}' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}'>
				<div class='col-sm-12'>
					<div class='row'>
						<div class=' col-sm-2'>	
							<div class='form-group'>
								เลขที่สัญญา
								<input type='text' id='CONTNO' class='form-control input-sm' placeholder='เลขที่สัญญา' >
							</div>
						</div>
						<div class=' col-sm-2'>	
							<div class='form-group'>
								วันที่ทำสัญญา
								<input type='text' id='SDATEFRM' class='form-control input-sm' placeholder='จาก' data-provide='datepicker' data-date-language='th-th' value='".$this->today('startofmonth')."'>
							</div>
						</div>	
						<div class=' col-sm-2'>	
							<div class='form-group'>
								ถึง
								<input type='text' id='SDATETO' class='form-control input-sm' placeholder='ถึง' data-provide='datepicker' data-date-language='th-th' value='".$this->today('endofmonth')."'>
							</div>
						</div>	
						<div class=' col-sm-2'>	
							<div class='form-group'>
								สาขา
								<input type='text' id='LOCAT' class='form-control input-sm' placeholder='สาขา'  value='".$this->sess['branch']."'>
							</div>
						</div>
						<div class=' col-sm-2'>	
							<div class='form-group'>
								เลขตัวถัง
								<input type='text' id='STRNO' class='form-control input-sm' placeholder='เลขตัวถัง' >
							</div>
						</div>
						<div class=' col-sm-2'>	
							<div class='form-group'>
								ชื่อ-สกุล ผู้จอง
								<select id='CUSCOD' class='form-control input-sm' data-placeholder='ชื่อ-สกุล ผู้จอง'></select>
							</div>
						</div>
					</div>
					<div class='row'>
						<div class=' col-sm-6'>	
							<div class='form-group'>
								<button id='btnt1leasing' class='btn btn-cyan btn-block'><span class='glyphicon glyphicon-pencil'> ทำรายการขายผ่อน</span></button>
							</div>
						</div>
						<div class=' col-sm-6'>	
							<div class='form-group'>
								<button id='btnt1search' class='btn btn-primary btn-block'><span class='glyphicon glyphicon-search'> ค้นหา</span></button>
							</div>
						</div>
					</div>
					<div class='row'>	
						<div id='jd_result' class='col-sm-12'></div>
					</div>				
				</div>
			</div>
		";
		
		$html.= "<script src='".base_url('public/js/SYS04/Leasing.js')."'></script>";
		echo $html;
	}
	*/
	function group_ins(){
		/*********************************KORN*******************************************/
		$claim = $this->MLogin->getclaim(uri_string());
		if($claim['m_access'] != "T"){ echo "<div align='center' style='color:red;font-size:16pt;width:100%;'>ขออภัย คุณยังไม่มีสิทธิเข้าใช้งานหน้านี้ครับ</div>"; exit; }
		
		$html = "
			<div class='k_tab1' name='home' locat='{$this->sess['branch']}' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}' style='height:calc(100vh - 132px);background-color:white;'>
				<!--div class='col-sm-12 col-xs-12' style='background-color:#39a6d0;border:5px solid white;height:75px;text-align:center;font-size:12pt;color:white;font-weight:bold;'>	
					<br>สอบถามกลุ่มลูกค้า<br>
				</div><br-->
				<div class='col-sm-12' style='height:calc(95vh - 95px);overflow:auto;background-color:white;'>
					<div div class='col-sm-12'>
						<b>เงื่อนไข</b>
						<div class='col-sm-12' style='height:100%;width:100%;border:1px dotted #aaa;;'>
							<div class='col-sm-10'>
								<div class='col-sm-3'>
									<div class='form-group'>
										รหัสกลุ่มลูกค้า
										<select id='ARGCOD' class='form-control input-sm'></select>									</select>
									</div>
								</div>
								<div class='col-sm-3'>
									<div class='form-group'>
										เกรดลูกค้า
										<select id='GRDCOD' class='form-control input-sm'></select>								
									</div>
								</div>
								<div class='col-sm-3'>
									<div class='form-group'>
										รหัสอำเภอ
										<select id='AUMPCOD' class='form-control input-sm'></select>									
									</div>
								</div>
								<div class='col-sm-3'>
									<div class='form-group'>
										รหัสจังหวัด
										<select id='PROVCOD' class='form-control input-sm'></select>									
									</div>
								</div>
							</div>
							<div class='col-sm-2'>
								<div class='col-sm-12'>
									<br>
									<button id='btnsearch' style='width:100%;' class='btn btn-primary btn-sm'><span class='glyphicon glyphicon-search'>สอบถาม</span></button>
								</div>
								<div class='col-sm-12'>
									<br>
									<button id='btnreport' style='width:100%;' class='btn btn-cyan btn-sm'><span class='fa fa-file-text'>SCREEN</span></button>
								</div>
							</div>
						</div>
						<div style='height:120px;'></div>
						<div class='col-sm-12'>
							<div id='HistroryCustomer'></div>
						</div>
					</div>
				<div>
			</div>
		";
		$html.= "<script src='".base_url('public/js/SYS04/group_ins.js')."'></script>";
		echo $html;
	}
	function Search_group_ins(){
		$ARGCOD  = $_REQUEST['ARGCOD'];
		$GRDCOD  = $_REQUEST['GRDCOD'];
		$AUMPCOD = $_REQUEST['AUMPCOD'];
		$PROVCOD = $_REQUEST['PROVCOD'];
		
		$cond = "";
		if($ARGCOD !== ""){
			$cond .=" and (A.GROUP1 like '".$ARGCOD."%' or A.GROUP1 is null)";
		}else{
			$response['error'] = true;
			$response['msg'] = "กรุณาเลือกรหัสกลุ่มลูกค้าก่อนที่จะค้นหาครับ";
			echo json_encode($response); exit;
		}
		if($GRDCOD !== ""){
			$cond .=" and (A.GRADE = '".$GRDCOD."')";
		}
		if($AUMPCOD !== ""){
			$cond .=" and (B.AUMPCOD like '".$AUMPCOD."%' or B.AUMPCOD is null)";
		}
		if($PROVCOD !== ""){
			$cond .=" and (B.PROVCOD like '".$PROVCOD."%' or B.PROVCOD is null)";
		}
		$html = "";
		$sql = "
			IF OBJECT_ID('tempdb..#HCUS') IS NOT NULL DROP TABLE #HCUS
			select CUSCOD,SNAM,NAME1,NAME2,NICKNM,BIRTHDT,IDCARD,IDNO,ISSUBY
				,ISSUDT,EXPDT,AGE,NATION,OCCUP,OFFIC,BOSSNM,GRADE,GROUP1,ADDRNO
			into #HCUS
			FROM(
				select ".($cond == "" ? "top 100":"")." A.CUSCOD,A.SNAM,A.NAME1,A.NAME2,A.NICKNM,CONVERT(varchar(8)
					,A.BIRTHDT,112) as BIRTHDT,A.IDCARD,A.IDNO,A.ISSUBY
					,CONVERT(varchar(8),A.ISSUDT,112) as ISSUDT,CONVERT(varchar(8),A.EXPDT,112) as EXPDT
					,A.AGE,A.NATION,A.OCCUP,A.OFFIC,A.BOSSNM,A.GRADE,A.GROUP1,A.ADDRNO
				from {$this->MAuth->getdb('CUSTMAST')} A
				left join {$this->MAuth->getdb('CUSTADDR')} B on A.CUSCOD = B.CUSCOD and A.ADDRNO = B.ADDRNO
				left join {$this->MAuth->getdb('SETAUMP')} C on B.AUMPCOD = C.AUMPCOD
				left join {$this->MAuth->getdb('SETPROV')} D on B.PROVCOD = D.PROVCOD
				where 1=1 ".$cond."
			)HCUS
		";
		$this->db->query($sql);
		//echo $sql; exit;
		$sql = "
			select 
				CUSCOD,SNAM,NAME1,NAME2,NICKNM,BIRTHDT,IDCARD,IDNO,ISSUBY
				,ISSUDT,EXPDT,AGE,NATION,OCCUP,OFFIC,BOSSNM,GRADE,GROUP1,ADDRNO
			from #HCUS order by GROUP1,CUSCOD 
		";
		//echo $sql; exit;
		$NRow = 1; $i = 0;
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){$i++;
				$html .="
					<tr class='trow' style='height:20px;' seq=".$NRow.">
						<td style='text-align:center;'>".$i."</td>
						<td class='getit' seq=".$NRow++." CUSCOD='".$row->CUSCOD."' ADDRNO='".$row->ADDRNO."' style='vertical-align:middle;cursor:pointer;color:blue;'>".$row->CUSCOD."</td>
						<td style='vertical-align:middle;'>".$row->SNAM."</td>
						<td style='vertical-align:middle;'>".$row->NAME1."</td>
						<td style='vertical-align:middle;'>".$row->NAME2."</td>
						<td style='vertical-align:middle;'>".$row->NICKNM."</td>
						<td style='vertical-align:middle;'>".$this->Convertdate(2,$row->BIRTHDT)."</td>
						<td style='vertical-align:middle;'>".$row->IDCARD."</td>
						<td style='vertical-align:middle;'>".$row->IDNO."</td>
						<td style='vertical-align:middle;'>".$row->ISSUBY."</td>
						<td style='vertical-align:middle;'>".$this->Convertdate(2,$row->ISSUDT)."</td>
						<td style='vertical-align:middle;'>".$this->Convertdate(2,$row->EXPDT)."</td>
						<td style='vertical-align:middle;'>".$row->AGE."</td>
						<td style='vertical-align:middle;'>".$row->NATION."</td>
						<td style='vertical-align:middle;'>".$row->OCCUP."</td>
						<td style='vertical-align:middle;'>".$row->OFFIC."</td>
						<td style='vertical-align:middle;'>".$row->BOSSNM."</td>
						<td style='vertical-align:middle;'>".$row->GRADE."</td>
						<td style='vertical-align:middle;'>".$row->GROUP1."</td>
					</tr>
				";
				//$NRow++;
			}
		}
		$arrs = array();
		$arrs['CUSCOD']  = "";
		$arrs['ADDR1']   = "";
		$arrs['ADDR2']   = "";
		$arrs['TUMB']    = "";
		$arrs['ZIP']     = "";
		$arrs['TELP']    = "";
		$arrs['AUMPDES'] = "";
		$arrs['PROVDES'] = "";
		$sql = "
			select B.CUSCOD,B.ADDR1,B.ADDR2,B.TUMB,B.ZIP,B.TELP,C.AUMPDES,D.PROVDES  
			from {$this->MAuth->getdb('CUSTADDR')} B
			left join {$this->MAuth->getdb('SETAUMP')} C on B.AUMPCOD = C.AUMPCOD
			left join {$this->MAuth->getdb('SETPROV')} D on B.PROVCOD = D.PROVCOD 
			where B.CUSCOD in (select top 1 CUSCOD from #HCUS order by CUSCOD)
			AND B.ADDRNO in (select top 1 ADDRNO from #HCUS order by CUSCOD)  
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$arrs['CUSCOD']  = $row->CUSCOD;
				$arrs['ADDR1']   = $row->ADDR1;
				$arrs['ADDR2']   = $row->ADDR2;
				$arrs['TUMB']    = $row->TUMB;
				$arrs['ZIP']     = $row->ZIP;
				$arrs['TELP']    = $row->TELP;
				$arrs['AUMPDES'] = $row->AUMPDES;
				$arrs['PROVDES'] = $row->PROVDES;
			}
		}
		$html = "
			<div id='table-histrory-customer' class='col-sm-12' style='height:calc(100% - 30px);width:100%;overflow:auto;font-size:8pt;'>
				<table id='table-hiscus' class='col-sm-12 table-bordered table table-hover' cellspacing='0' width='100%' border=1>
					<thead style='background: rgba(0, 0, 0, 0) url(&#39;../public/lobiadmin-master/version/1.0/ajax/img/bg/bg6.png&#39;) repeat scroll 0% 0%;'>						
						<tr style='line-height:20px;'>
							<td style='vertical-align:middle;text-align:center;font-size:8pt;' colspan='20'>
								เงื่อนไข 
							</td>
						</tr>
						<tr>
							<th>#</th>
							<th style='vertical-align:middle; width:'>รหัสลูกค้า</th>
							<th style='vertical-align:middle; width:'>คำนำหน้า</th>
							<th style='vertical-align:middle; width:'>ชื่อ</th>
							<th style='vertical-align:middle; width:'>สกุล</th>
							<th style='vertical-align:middle; width:'>ชื่อเล่น</th>
							<th style='vertical-align:middle; width:'>วัน/เดือน/ปี เกิด</th>
							<th style='vertical-align:middle; width:'>บัตรที่ใช้</th>
							<th style='vertical-align:middle; width:'>เลขที่บัตร</th>
							<th style='vertical-align:middle; width:'>ออกให้โดย</th>
							<th style='vertical-align:middle; width:'>ออก ณ วันที่</th>
							<th style='vertical-align:middle; width:'>บัตรหมดอายุ</th>
							<th style='vertical-align:middle; width:'>อายุ</th>
							<th style='vertical-align:middle; width:'>สัญชาติ</th>
							<th style='vertical-align:middle; width:'>อาชีพ</th>
							<th style='vertical-align:middle; width:'>ที่ทำงาน</th>
							<th style='vertical-align:middle; width:'>หัวหน้างาน</th>
							<th style='vertical-align:middle; width:'>เกรดลูกค้า</th>
							<th style='vertical-align:middle; width:'>กลุ่มลูกค้า</th>
						</tr>
					</thead>
					<tbody>
						".$html."
					</tbody>
				</table>
			</div>
			
			<b>ที่อยู่</b>
			<div class='col-sm-12' style='height:100%;width:100%;border:1px dotted #aaa;background-color:#dedede;'>
				<div class='col-sm-3'>
					<div class='form-group'>
						บ้านเลขที่
						<input type='text' id='ADDR1' class='form-control input-sm' value='".$arrs['ADDR1']."'>
					</div>
				</div>
				<div class='col-sm-3'>
					<div class='form-group'>
						ถนน
						<input type='text' id='ADDR2' class='form-control input-sm' value='".$arrs['ADDR2']."'>									
					</div>
				</div>
				<div class='col-sm-3'>
					<div class='form-group'>
						ตำบล
						<input type='text' id='TUMB' class='form-control input-sm' value='".$arrs['TUMB']."'>										
					</div>
				</div>
				<div class='col-sm-3'>
					<div class='form-group'>
						ชื่ออำเภอ
						<input type='text' id='AUMPDES' class='form-control input-sm' value='".$arrs['AUMPDES']."'>									
					</div>
				</div>
				<div class='col-sm-3'>
					<div class='form-group'>
						ชื่อจังหวัด
						<input type='text' id='PROVDES' class='form-control input-sm' value='".$arrs['PROVDES']."'>							
					</div>
				</div>
				<div class='col-sm-3'>
					<div class='form-group'>
						รหัสไปรษณีย์
						<input type='text' id='ZIP' class='form-control input-sm' value='".$arrs['ZIP']."'>								
					</div>
				</div>
				<div class='col-sm-3'>
					<div class='form-group'>
						เบอร์โทรศัพท์
						<input type='text' id='TELP' class='form-control input-sm' value='".$arrs['TELP']."'>										
					</div>
				</div>
				<div id='addrsbtn' class='col-sm-3'>
					<div class='form-group'>
						<br>
						<button cuscod='".$arrs['CUSCOD']."' id='ADDRS' style='width:100%;' class='btn btn-cyan btn-sm'><span class='fa fa-file-text'>ที่อยู่อื่น</span></button>									
					</div>
				</div>
			</div>
		";
		$response = array("html"=>$html);
		echo json_encode($response);
	}
	function ChangeAddr(){
		$response = array();
		$ADDR = $_REQUEST['ADDR'];
		
		$sql = "
			select B.CUSCOD,B.ADDR1,B.ADDR2,B.TUMB,B.ZIP,B.TELP,C.AUMPDES,D.PROVDES  
			from {$this->MAuth->getdb('CUSTADDR')} B
			left join {$this->MAuth->getdb('SETAUMP')} C on B.AUMPCOD = C.AUMPCOD
			left join {$this->MAuth->getdb('SETPROV')} D on B.PROVCOD = D.PROVCOD 
			where B.CUSCOD = '".$ADDR[0]."'
			AND B.ADDRNO = '".$ADDR[1]."'  
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$response['CUSCOD']  = $row->CUSCOD;	
				$response['ADDR1']   = $row->ADDR1;
				$response['ADDR2']   = $row->ADDR2;
				$response['TUMB']    = $row->TUMB;
				$response['ZIP']     = $row->ZIP;
				$response['TELP']    = $row->TELP;
				$response['AUMPDES'] = $row->AUMPDES;
				$response['PROVDES'] = $row->PROVDES;
			}
		}
		echo json_encode($response);
	}
	function Address(){
		$cuscod = $_REQUEST['cuscod'];
		
		$sql = "
			select B.CUSCOD,B.ADDR1,B.ADDR2,B.TUMB,B.ZIP,B.TELP,C.AUMPDES,D.PROVDES  
			from {$this->MAuth->getdb('CUSTADDR')} B
			left join {$this->MAuth->getdb('SETAUMP')} C on B.AUMPCOD = C.AUMPCOD
			left join {$this->MAuth->getdb('SETPROV')} D on B.PROVCOD = D.PROVCOD 
			where B.CUSCOD = '".$cuscod."'
		";
		$query = $this->db->query($sql);
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$html .="
					<tr class='trow' style='height:20px;'>
						<td style='vertical-align:middle;'>".$row->CUSCOD."</td>
						<td style='vertical-align:middle;'>".$row->ADDR1."</td>
						<td style='vertical-align:middle;'>".$row->ADDR2."</td>
						<td style='vertical-align:middle;'>".$row->TUMB."</td>
						<td style='vertical-align:middle;'>".$row->AUMPDES."</td>
						<td style='vertical-align:middle;'>".$row->PROVDES."</td>
						<td style='vertical-align:middle;'>".$row->ZIP."</td>
						<td style='vertical-align:middle;'>".$row->TELP."</td>
					</tr>
				";
			}
		}
		$html = "
			<div id='table-address' class='col-sm-12' style='height:calc(100% - 30px);width:100%;overflow:auto;font-size:8pt;'>
				<table id='table-address' class='table-bordered table table-hover' cellspacing='0' width='100%' border=1>
					<thead style='background: rgba(0, 0, 0, 0) url(&#39;../public/lobiadmin-master/version/1.0/ajax/img/bg/bg6.png&#39;) repeat scroll 0% 0%;'>						
						<tr style='line-height:20px;'>
							<td style='vertical-align:middle;text-align:center;font-size:8pt;' colspan='9'>
								เงื่อนไข 
							</td>
						</tr>
						<tr>
							<th style='vertical-align:middle; width:'>รหัสลูกค้า</th>
							<th style='vertical-align:middle; width:'>บ้านเลขที่ หมู่ที่</th>
							<th style='vertical-align:middle; width:'>ถนน ซอย</th>
							<th style='vertical-align:middle; width:'>ตำบล</th>
							<th style='vertical-align:middle; width:'>อำเภอ</th>
							<th style='vertical-align:middle; width:'>จังหวัด</th>
							<th style='vertical-align:middle; width:'>รหัสไปรษณีย์</th>
							<th style='vertical-align:middle; width:'>เบอร์โทรศัพท์</th>
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
	function conditiontopdf_group_ins(){
		$data = array();
		$data[] = urlencode($_REQUEST["ARGCOD"].'||'.$_REQUEST["GRDCOD"]
		.'||'.$_REQUEST["AUMPCOD"].'||'.$_REQUEST["PROVCOD"]);
		echo json_encode($this->generateData($data,"encode"));
	}
	function pdf_group_ins(){
		$data = array();
		$data[] = $_GET["condpdf"];
		$arrs = $this->generateData($data,"decode");
		$arrs[0] = urldecode($arrs[0]);
		
		$tx = explode("||",$arrs[0]);
		$ARGCOD 	= $tx[0];
		$GRDCOD 	= $tx[1];
		$AUMPCOD	= $tx[2];
		$PROVCOD 	= $tx[3];
		//print_r($tx); exit;
		$cond = "";
		if($ARGCOD !== ""){
			$cond .=" and (A.GROUP1 like '".$ARGCOD."%' or A.GROUP1 is null)";
		}
		if($GRDCOD !== ""){
			$cond .=" and (A.GRADE = '".$GRDCOD."')";
		}
		if($AUMPCOD !== ""){
			$cond .=" and (B.AUMPCOD like '".$AUMPCOD."%' or B.AUMPCOD is null)";
		}
		if($PROVCOD !== ""){
			$cond .=" and (B.PROVCOD like '".$PROVCOD."%' or B.PROVCOD is null)";
		}
		$html = "";
		$sql = "
			IF OBJECT_ID('tempdb..#HCUS') IS NOT NULL DROP TABLE #HCUS
			select CUSCOD,SNAM,NAME1,NAME2,NICKNM,BIRTHDT,IDCARD,IDNO,ISSUBY
				,ISSUDT,EXPDT,AGE,NATION,OCCUP,OFFIC,BOSSNM,GRADE,GROUP1,ADDRNO
			into #HCUS
			FROM(
				select ".($cond == "" ? "top 100":"")." A.CUSCOD,A.SNAM,A.NAME1,A.NAME2,A.NICKNM,CONVERT(varchar(8)
					,A.BIRTHDT,112) as BIRTHDT,A.IDCARD,A.IDNO,A.ISSUBY
					,CONVERT(varchar(8),A.ISSUDT,112) as ISSUDT,CONVERT(varchar(8),A.EXPDT,112) as EXPDT
					,A.AGE,A.NATION,A.OCCUP,A.OFFIC,A.BOSSNM,A.GRADE,A.GROUP1,A.ADDRNO
				from {$this->MAuth->getdb('CUSTMAST')} A
				left join {$this->MAuth->getdb('CUSTADDR')} B on A.CUSCOD = B.CUSCOD and A.ADDRNO = B.ADDRNO
				left join {$this->MAuth->getdb('SETAUMP')} C on B.AUMPCOD = C.AUMPCOD
				left join {$this->MAuth->getdb('SETPROV')} D on B.PROVCOD = D.PROVCOD
				where 1=1 ".$cond."
			)HCUS
		";
		$this->db->query($sql);
		
		$sql = "
			select A.CUSCOD,A.SNAM+A.NAME1+' '+A.NAME2 as CUSNAME,A.GRADE,A.GROUP1
				,B.ADDR1+' ถนน'+B.ADDR2+' ซอย'+B.SOI+' ตำบล'+B.TUMB+' อำเภอ'+C.AUMPDES+
				' จังหวัด'+D.PROVDES+' รหัสไปรษณีย์'+B.ZIP+' เบอร์โทร'+B.TELP as ADDR
			from {$this->MAuth->getdb('CUSTMAST')} A
			left join {$this->MAuth->getdb('CUSTADDR')} B on A.CUSCOD = B.CUSCOD and A.ADDRNO = B.ADDRNO
			left join {$this->MAuth->getdb('SETAUMP')} C on B.AUMPCOD = C.AUMPCOD
			left join {$this->MAuth->getdb('SETPROV')} D on B.PROVCOD = D.PROVCOD
			where A.CUSCOD in (select CUSCOD from #HCUS)
		";
		$head = ""; $html = ""; $i = 0;
		$head = "
			<tr class='wm'>
				<td class='wf pd' style='height:1px;border-top:0.1px solid black;' colspan='6'></td>
			</tr>
			<tr>
				<th style='border-bottom:0.1px solid black;text-align:center;'>No.</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>รหัสลูกค้า</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>ชื่อ - สกุล</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>รหัสกลุ่ม</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>เกรด</th>
				<th style='border-bottom:0.1px solid black;text-align:left;'>ที่อยู่</th>
			</tr>
			<tr class='wm'>
				<td class='wf pd' style='height:1px;border-top:0.1px solid black;' colspan='6'></td>
			</tr>
		";
		$num = array();
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){$i++;
				$num[]= $i;
				$html .="
					<tr class='trow'>
						<td style='width:30px;text-align:center;'>".$i."</td>
						<td style='width:40px;text-align:left;'>".$row->CUSCOD."</td>
						<td style='width:40px;text-align:left;'>".$row->CUSNAME."</td>
						<td style='width:30px;text-align:left;'>".$row->GROUP1."</td>
						<td style='width:30px;text-align:left;'>".$row->GRADE."</td>
						<td style='width:100px;text-align:left;'>".$row->ADDR."</td>
					</tr>
				";
			}
		}
		$html .="
			<tr class='wm'>
				<td class='wf pd' style='height:1px;border-top:0.1px solid black;' colspan='6'></td>
			</tr>
			<tr class='trow'>
				<td style='width:30px;text-align:center;' colspan='2'><b>รวมทั้งสิ้น</b></td>
				<td style='width:30px;text-align:center;' colspan='2'>".max($num)."</td>
				<td style='width:30px;text-align:left;' colspan='2'><b>รายการ</b></td>
			</tr>
			<tr class='wm'>
				<td class='wf pd' style='height:1px;border-top:0.1px solid black;' colspan='6'></td>
			</tr>
		";
		$sql = "
			select ARGCOD,ARGDES from {$this->MAuth->getdb('ARGROUP')}
			where ARGCOD = '".$ARGCOD."'
		";
		$ARGDES = "";
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$ARGDES = $row->ARGDES;	
			}
		}
		$mpdf = new \Mpdf\Mpdf([
			'mode' => 'utf-8', 
			'format' =>'A4-L',
			'margin_top' => 10, 	//default = 16
			'margin_left' => 10, 	//default = 15
			'margin_right' => 10, 	//default = 15
			'margin_bottom' => 10, 	//default = 16
			'margin_header' => 9, 	//default = 9
			'margin_footer' => 9, 	//default = 9
		]);
		if($i > 0){
			$content = "
				<table class='wf' style='font-size:7.5pt;height:700px;border-collapse:collapse;line-height:23px;overflow:wrap;vertical-align:text-top;'>
					<tbody>
						<tr>
							<th colspan='6' style='font-size:10pt;'>บริษัท ตั้งใจพัฒนายานยนต์ จำกัด</th>
						</tr>
						<tr>
							<th colspan='6' style='font-size:9pt;'>รายงานลูกค้าตามกลุ่ม</th>
						</tr>
						<tr>
							<td  colspan='6' style='font-size:9pt;text-align:center;'>
								<b>รหัสกลุ่ม</b>&nbsp;&nbsp;".$ARGCOD."&nbsp;&nbsp;&nbsp;&nbsp;<b>ชื่อกลุ่ม</b>&nbsp;&nbsp;".$ARGDES."&nbsp;&nbsp;
							</td>
						</tr>
						".$head."
						".$html."
					</tbody>
				</table>
			";
			$head = "
				<div class='wf pf' style='top:1060;left:600;top:715;left:880; font-size:6pt;'>วันที่พิมพ์รายงาน : ".date('d/m/').(date('Y')+543)." ".date('H:i')." หน้า {PAGENO} / {nbpg}</div>
			";
		}else{
			$content = "<font style='color:red;'>ไม่พบข้อมูลตามเงื่อนไข</font>";
			$head = "
				<div class='wf pf' style='top:1060;left:600;top:715;left:880; font-size:6pt;'></div>
			";
		}
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
		$mpdf->SetHTMLHeader($head);	
		$mpdf->WriteHTML($content);	
		$mpdf->Output();
	}
	/*
	function cancelcont(){
		echo "อยู่ระหว่างการพัฒนาโปรแกรม"; exit;
		$claim = $this->MLogin->getclaim(uri_string());
		if($claim['m_access'] != "T"){ echo "<div align='center' style='color:red;font-size:16pt;width:100%;'>ขออภัย คุณยังไม่มีสิทธิเข้าใช้งานหน้านี้ครับ</div>"; exit; }
		
		$html = "
			<div class='tab1' name='home' locat='{$this->sess['branch']}' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}'>
				<div class='col-sm-12'>
					<div class='row'>
						<div class=' col-sm-2'>	
							<div class='form-group'>
								เลขที่สัญญา
								<input type='text' id='CONTNO' class='form-control input-sm' placeholder='เลขที่สัญญา' >
							</div>
						</div>
						<div class=' col-sm-2'>	
							<div class='form-group'>
								วันที่ทำสัญญา
								<input type='text' id='SDATEFRM' class='form-control input-sm' placeholder='จาก' data-provide='datepicker' data-date-language='th-th' value='".$this->today('startofmonth')."'>
							</div>
						</div>	
						<div class=' col-sm-2'>	
							<div class='form-group'>
								ถึง
								<input type='text' id='SDATETO' class='form-control input-sm' placeholder='ถึง' data-provide='datepicker' data-date-language='th-th' value='".$this->today('endofmonth')."'>
							</div>
						</div>	
						<div class=' col-sm-2'>	
							<div class='form-group'>
								สาขา
								<input type='text' id='LOCAT' class='form-control input-sm' placeholder='สาขา'  value='".$this->sess['branch']."'>
							</div>
						</div>
						<div class=' col-sm-2'>	
							<div class='form-group'>
								เลขตัวถัง
								<input type='text' id='STRNO' class='form-control input-sm' placeholder='เลขตัวถัง' >
							</div>
						</div>
						<div class=' col-sm-2'>	
							<div class='form-group'>
								ชื่อ-สกุล ผู้จอง
								<select id='CUSCOD' class='form-control input-sm' data-placeholder='ชื่อ-สกุล ผู้จอง'></select>
							</div>
						</div>
					</div>
					<div class='row'>
						<div class=' col-sm-6'>	
							<div class='form-group'>
								<button id='btnt1leasing' class='btn btn-cyan btn-block'><span class='glyphicon glyphicon-pencil'> ทำรายการขายผ่อน</span></button>
							</div>
						</div>
						<div class=' col-sm-6'>	
							<div class='form-group'>
								<button id='btnt1search' class='btn btn-primary btn-block'><span class='glyphicon glyphicon-search'> ค้นหา</span></button>
							</div>
						</div>
					</div>
					<div class='row'>	
						<div id='jd_result' class='col-sm-12'></div>
					</div>				
				</div>
			</div>
		";
		
		$html.= "<script src='".base_url('public/js/SYS04/Leasing.js')."'></script>";
		echo $html;
	}
	*/
	function cancelcont(){
		/*********************************KORN*******************************************/
		$claim = $this->MLogin->getclaim(uri_string());
		if($claim['m_access'] != "T"){ echo "<div align='center' style='color:red;font-size:16pt;width:100%;'>ขออภัย คุณยังไม่มีสิทธิเข้าใช้งานหน้านี้ครับ</div>"; exit; }
		
		$html = "
			<div class='k_tab1' name='home' locat='{$this->sess['branch']}' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}' style='height:calc(100vh - 132px);background-color:white;'>
				<div class='col-sm-12 col-xs-12' style='background-color:#39a6d0;border:5px solid white;height:75px;text-align:center;font-size:12pt;color:white;font-weight:bold;'>	
					<br>สอบถามสัญญาที่ถูกยกเลิก<br>
				</div><br>
				<div class='col-sm-12' style='height:calc(80vh - 80px);overflow:auto;background-color:white;'>
					<div div class='col-sm-12'>
						<b>เงื่อนไข</b>
						<div class='col-sm-12' style='height:100%;width:100%;border:1px dotted #aaa;;'>
							<div class='col-sm-10'>
								<div class='col-sm-4'>
									<div class='form-group'>
										เลขที่สัญญา
										<input type='text' id='CONTNO' class='form-control input-sm' placeholder='เลขที่สัญญา' >
									</div>
								</div>
								<div class='col-sm-4'>
									<div class='form-group'>
										จากวันที่ขาย
										<input type='text' id='F_SDATE' class='form-control input-sm'  data-provide='datepicker' data-date-language='th-th' value='".$this->today('startofmonthB1')."' placeholder='จากวันที่ขาย' >								
									</div>
								</div>
								<div class='col-sm-4'>
									<div class='form-group'>
										ถึงวันที่ขาย
										<input type='text' id='T_SDATE' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' value='".$this->today('endofmonth')."' placeholder='ถึงวันที่ขาย' >									
									</div>
								</div>
							</div>
							<div class='col-sm-2'>
								<div class='col-sm-12'>
									<br>
									<button id='btnsearch' style='width:100%;' class='btn btn-primary btn-sm'><span class='glyphicon glyphicon-search'>สอบถาม</span></button>
								</div>
							</div>
						</div>
						<div style='height:120px;'></div>
						<div class='col-sm-12'>
							<div id='ContCancel'></div>
						</div>
					</div>
				<div>
			</div>
		";
		$html.= "<script src='".base_url('public/js/SYS04/cancelcont.js')."'></script>";
		echo $html;
	}
	function searchCanCelCont(){
		$CONTNO  = $_REQUEST['CONTNO'];
		$F_SDATE = $this->Convertdate(1,$_REQUEST['F_SDATE']);
		$T_SDATE = $this->Convertdate(1,$_REQUEST['T_SDATE']);
		$html = "";
		$sql = "
			select B.LOCAT,B.CUSCOD,B.CONTNO,'ขายผ่อน' as TSALE,B.STRNO
			,convert(varchar(8),B.SDATE,112) as SDATE,convert(varchar(8),B.DELDT,112) as DELDT,B.DELID  
			from {$this->MAuth->getdb('CANARMST')} B where B.CONTNO like '".$CONTNO."%' 
			and B.SDATE between '".$F_SDATE."' and '".$T_SDATE."'    
			union 
			select B.LOCAT,B.CUSCOD,B.CONTNO,'ขายส่งไฟแนนซ์' as TSALE,B.STRNO
			,convert(varchar(8),B.SDATE,112) as SDATE,convert(varchar(8),B.DELDT,112) as DELDT,B.DELID
			from {$this->MAuth->getdb('CANFINC')} B where B.CONTNO like '".$CONTNO."%' 
			and B.SDATE between '".$F_SDATE."' and '".$T_SDATE."'  
			union 
			select B.LOCAT,B.CUSCOD,B.CONTNO,'ขายสด' as TSALE,B.STRNO
			,convert(varchar(8),B.SDATE,112) as SDATE,convert(varchar(8),B.DELDT,112) as DELDT,B.DELID
			from {$this->MAuth->getdb('CANCRED')} B where B.CONTNO like '".$CONTNO."%' 
			and B.SDATE between '".$F_SDATE."' and '".$T_SDATE."'   
			union 
			select B.LOCAT,B.CUSCOD,B.CONTNO,'ขายอุปกรณ์เสริม' as TSALE,'' as STRNO
			,convert(varchar(8),B.SDATE,112) as SDATE,convert(varchar(8),B.DELDT,112) as DELDT,B.DELID
			from {$this->MAuth->getdb('CANOPMST')} B where B.CONTNO like '".$CONTNO."%' 
			and B.SDATE between '".$F_SDATE."' and '".$T_SDATE."'
			union 
			select B.LOCAT,B.CUSCOD,B.CONTNO,'ขายส่งเอเย่นต์' as TSALE,'' as STRNO
			,convert(varchar(8),B.SDATE,112) as SDATE,convert(varchar(8),B.DELDT,112) as DELDT,B.DELID
			from {$this->MAuth->getdb('CANINVO')} B where B.CONTNO like '".$CONTNO."%' 
			and B.SDATE between '".$F_SDATE."' and '".$T_SDATE."'
		";
		//echo $sql;
		$i = 0;
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){$i++;
				$html .="
					<tr class='trow' style='height:20px;'>
						<td style='vertical-align:middle; text-align:center;'>".$i."</td>
						<td style='vertical-align:middle;'>".$row->CONTNO."</td>
						<td style='vertical-align:middle;'>".$row->LOCAT."</td>
						<td style='vertical-align:middle;'>".$row->CUSCOD."</td>
						<td style='vertical-align:middle;'>".$row->TSALE."</td>
						<td style='vertical-align:middle;'>".$row->STRNO."</td>
						<td style='vertical-align:middle;'>".$this->Convertdate(2,$row->SDATE)."</td>
						<td style='vertical-align:middle;'>".$this->Convertdate(2,$row->DELDT)."</td>
						<td style='vertical-align:middle;'>".$row->DELID."</td>
					</tr>
				";
			}
		}
		$html = "
			<div id='table-cancelcont' class='col-sm-12' style='height:calc(100% - 30px);width:100%;overflow:auto;font-size:8pt;'>
				<table id='table-cancont' class='col-sm-12 table-bordered table table-hover' cellspacing='0' width='100%' border=1>
					<thead style='background: rgba(0, 0, 0, 0) url(&#39;../public/lobiadmin-master/version/1.0/ajax/img/bg/bg6.png&#39;) repeat scroll 0% 0%;'>						
						<tr style='line-height:20px;'>
							<td style='vertical-align:middle;text-align:center;font-size:8pt;' colspan='20'>
								เงื่อนไข 
							</td>
						</tr>
						<tr>
							<th style='vertical-align:middle; width:5%'>#</th>
							<th style='vertical-align:middle; width:10%'>เลขที่สัญญา</th>
							<th style='vertical-align:middle; width:5%'>สาขา</th>
							<th style='vertical-align:middle; width:10%'>รหัสลูกค้า</th>
							<th style='vertical-align:middle; width:10%'>ประเภทการขาย</th>
							<th style='vertical-align:middle; width:20%'>เลขตัวถัง</th>
							<th style='vertical-align:middle; width:15%'>วัน/เดือน/ปี ที่ขาย</th>
							<th style='vertical-align:middle; width:15%'>วันที่ลบรายการ</th>
							<th style='vertical-align:middle; width:10%'>รหัสผู้ลบรายการ</th>
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
	/*
	function outsyscont(){
		echo "อยู่ระหว่างการพัฒนาโปรแกรม"; exit;
		$claim = $this->MLogin->getclaim(uri_string());
		if($claim['m_access'] != "T"){ echo "<div align='center' style='color:red;font-size:16pt;width:100%;'>ขออภัย คุณยังไม่มีสิทธิเข้าใช้งานหน้านี้ครับ</div>"; exit; }
		
		$html = "
			<div class='tab1' name='home' locat='{$this->sess['branch']}' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}'>
				<div class='col-sm-12'>
					<div class='row'>
						<div class=' col-sm-2'>	
							<div class='form-group'>
								เลขที่สัญญา
								<input type='text' id='CONTNO' class='form-control input-sm' placeholder='เลขที่สัญญา' >
							</div>
						</div>
						<div class=' col-sm-2'>	
							<div class='form-group'>
								วันที่ทำสัญญา
								<input type='text' id='SDATEFRM' class='form-control input-sm' placeholder='จาก' data-provide='datepicker' data-date-language='th-th' value='".$this->today('startofmonth')."'>
							</div>
						</div>	
						<div class=' col-sm-2'>	
							<div class='form-group'>
								ถึง
								<input type='text' id='SDATETO' class='form-control input-sm' placeholder='ถึง' data-provide='datepicker' data-date-language='th-th' value='".$this->today('endofmonth')."'>
							</div>
						</div>	
						<div class=' col-sm-2'>	
							<div class='form-group'>
								สาขา
								<input type='text' id='LOCAT' class='form-control input-sm' placeholder='สาขา'  value='".$this->sess['branch']."'>
							</div>
						</div>
						<div class=' col-sm-2'>	
							<div class='form-group'>
								เลขตัวถัง
								<input type='text' id='STRNO' class='form-control input-sm' placeholder='เลขตัวถัง' >
							</div>
						</div>
						<div class=' col-sm-2'>	
							<div class='form-group'>
								ชื่อ-สกุล ผู้จอง
								<select id='CUSCOD' class='form-control input-sm' data-placeholder='ชื่อ-สกุล ผู้จอง'></select>
							</div>
						</div>
					</div>
					<div class='row'>
						<div class=' col-sm-6'>	
							<div class='form-group'>
								<button id='btnt1leasing' class='btn btn-cyan btn-block'><span class='glyphicon glyphicon-pencil'> ทำรายการขายผ่อน</span></button>
							</div>
						</div>
						<div class=' col-sm-6'>	
							<div class='form-group'>
								<button id='btnt1search' class='btn btn-primary btn-block'><span class='glyphicon glyphicon-search'> ค้นหา</span></button>
							</div>
						</div>
					</div>
					<div class='row'>	
						<div id='jd_result' class='col-sm-12'></div>
					</div>				
				</div>
			</div>
		";
		
		$html.= "<script src='".base_url('public/js/SYS04/Leasing.js')."'></script>";
		echo $html;
	}
	*/
	function outsyscont(){
		/*********************************KORN*******************************************/
		$claim = $this->MLogin->getclaim(uri_string());
		if($claim['m_access'] != "T"){ echo "<div align='center' style='color:red;font-size:16pt;width:100%;'>ขออภัย คุณยังไม่มีสิทธิเข้าใช้งานหน้านี้ครับ</div>"; exit; }
		$html = "
			<div class='k_tab1' name='home' locat='{$this->sess['branch']}' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}' today='".$this->today('today')."' style='height:calc(100vh - 132px);overflow:auto;'>
				<div class='col-sm-12 col-xs-12' style='float:left;height:100%;overflow:auto;'>
					<div id='wizard-financedetail' class='wizard-wrapper'>    
						<div class='wizard'>
							<form id='demo-form2' action='' class='lobi-form' novalidate='novalidate'>
								<ul class='wizard-tabs nav-justified nav nav-pills' style='width:30%;height:100%;'>
									<li class='active' style='background-color:#83f0d6; solid #83f0d6;'>
										<a href='#tab11' prev='#' data-toggle='tab' aria-expanded='true'>
											<span class='step'></span>
											<span class='title'><b>รายการบัญชี</b></span>
										</a>
									</li>
									<li style='background-color:#83f0d6; solid #83f0d6;'>
										<a href='#tab22' prev='#tab11' data-toggle='tab'>
											<span class='step'></span>
											<span class='title'><b>ตารางสัญญา</b></span>
										</a>
									</li>
								</ul>
								<div class='tab-content bg-white'>
									".$this->getfromOutsysTab11()."
									".$this->getfromOutsysTab22()."
								</div>
							</form>
						</div>
					</div>				
				</div>
			</div>
		";
		
		$html .="<script src='".base_url('public/js/SYS04/outsyscont.js')."'></script>";
		echo ($html);
	}
	function getfromOutsysTab11(){
		$html = "
			<div class='tab-pane active' name='tab11' style='height:calc(100vh - 215px);overflow:auto;'>
				<fieldset style='height:100%'>
					<div style='float:left;height:100%;' class='col-sm-12 col-xs-12'>
						<b>เงื่อนไข</b>
						<div class='col-sm-12' style='width:100%;border:1px dotted #aaa;'>
							<div class='col-sm-10'>
								<div class='col-sm-4'>
									<div class='form-group'>
										รหัสลูกค้า
										<select id='CUSCOD' class='form-control input-sm'></select>									</select>
									</div>
								</div>
								<div class='col-sm-4'>
									<div class='form-group'>
										เลขที่สัญญา
										<input type='text' id='CONTNO' class='form-control input-sm' placeholder='เลขที่สัญญา' >							
									</div>
								</div>
								<div class='col-sm-4'>
									<div class='form-group'>
										เลขที่ตัวถัง
										<input type='text' id='STRNO' class='form-control input-sm' placeholder='เลขที่สัญญา' >									
									</div>
								</div>
								<div class='col-sm-4'>
									<div class='form-group'>
										จากวันที่ขาย
										<input type='text' id='F_SDATE' class='form-control input-sm'  data-provide='datepicker' data-date-language='th-th' value='' placeholder='จากวันที่ขาย' >								
									</div>
								</div>
								<div class='col-sm-4'>
									<div class='form-group'>
										ถึงวันที่ขาย
										<input type='text' id='T_SDATE' class='form-control input-sm' data-provide='datepicker' data-date-language='th-th' value='' placeholder='ถึงวันที่ขาย' >									
									</div>
								</div>
							</div>
							<div class='col-sm-2'>
								<div class='col-sm-12'>
									<br>
									<button id='btnsearch' style='width:100%;' class='btn btn-primary btn-sm'><span class='glyphicon glyphicon-search'>สอบถาม</span></button>
								</div>
							</div>
						</div>
						<div class='col-sm-12'></div>
						
						<div class='col-sm-12' style='height:60%;'>
							<div class='row' style='height:100%;border:0.1px solid #bdbdbd;background-color:#eee;'>
								<div class='col-sm-12 col-xs-12' style='height:100%;'>
									<div id='dataTable-fixed-outsyscont' class='dataTables_wrapper dt-bootstrap4 table-responsive' style='height:100%;width:100%;overflow:auto;'>
										<table id='dataTables-outsyscont' class='table table-bordered dataTable table-hover' stat='' aria-describedby='dataTable_info' cellspacing='0' width='calc(100% - 1px)'>
											<thead>
												<tr role='row' style='height:30px;font-size:8pt;background-color:#117a65;color:white;'>
													<th width='12.5%' style='vertical-align:middle;color:#f4d03f;'>เลขที่สัญญา</th>
													<th width='12.5%' style='vertical-align:middle;color:#f4d03f;'>สาขา</th>
													<th width='12.5%' style='vertical-align:middle;color:#f4d03f;'>รหัสลูกค้า</th>
													<th width='12.5%' style='vertical-align:middle;color:#f4d03f;'>ประเภทการขาย</th>
													<th width='12.5%' style='vertical-align:middle;color:#f4d03f;'>เลขตัวถัง</th>
													<th width='12.5%' style='vertical-align:middle;color:#f4d03f;'>วัน/เดือน/ปีที่ขาย</th>
													<th width='12.5%' style='vertical-align:middle;color:#f4d03f;'>วันที่ย้ายสัญญา</th>
													<th width='12.5%' style='vertical-align:middle;color:#f4d03f;'>รหัสผู้ย้าย</th>
													<th width='12.5%' style='vertical-align:middle;color:#f4d03f;'>สาเหตุการปิดบัญชี</th>
												</tr>
											</thead>
											<tbody style='white-space:nowrap;background-color:white;font-size:9pt;'></tbody>
										</table>
									</div>
								</div>
							</div>
						</div>
					</div>
				</fieldset>
			</div>	
		";
		return $html;
	}
	function getfromOutsysTab22(){
		$html = "
			<div class='tab-pane' name='tab22' style='height:calc(100vh - 215px);overflow:auto;'>
				<fieldset style='height:100%'>
					<div style='float:left;height:100%;' class='col-sm-12 col-xs-12'>
						<div class='col-sm-12' style='height:100%;'>
							<div class='row' style='height:100%;border:0.1px solid #bdbdbd;background-color:#eee;'>
								<div class='col-sm-12 col-xs-12' style='height:100%;'>
									<div id='dataTable-fixed-harpay' class='dataTables_wrapper dt-bootstrap4 table-responsive' style='height:100%;width:100%;overflow:auto;'>
										<table id='dataTables-harpay' class='table table-bordered dataTable table-hover' stat='' aria-describedby='dataTable_info' cellspacing='0' width='calc(100% - 1px)'>
											<thead>
												<tr role='row' style='height:30px;font-size:8pt;background-color:#117a65;color:white;'>
													<th width='12.5%' style='vertical-align:middle;color:#f4d03f;'>เลขที่สัญญา</th>
													<th width='12.5%' style='vertical-align:middle;color:#f4d03f;'>สาขา</th>
													<th width='12.5%' style='vertical-align:middle;color:#f4d03f;'>งวดที่</th>
													<th width='12.5%' style='vertical-align:middle;color:#f4d03f;'>วันดิว</th>
													<th width='12.5%' style='vertical-align:middle;color:#f4d03f;'>ค่างวดตามดิว</th>
													<th width='12.5%' style='vertical-align:middle;color:#f4d03f;'>ภาษีค่างวด</th>
													<th width='12.5%' style='vertical-align:middle;color:#f4d03f;'>มูลค่าค่างวด</th>
													<th width='12.5%' style='vertical-align:middle;color:#f4d03f;'>เงินต้น</th>
													<th width='12.5%' style='vertical-align:middle;color:#f4d03f;'>ดอกผลเช่าซื้อ</th>
													<th width='12.5%' style='vertical-align:middle;color:#f4d03f;'>วันที่ชำระ</th>
													<th width='12.5%' style='vertical-align:middle;color:#f4d03f;'>ชำระงวดนี้แล้ว</th>
													<th width='12.5%' style='vertical-align:middle;color:#f4d03f;'>ภาษีที่ชำระแล้ว</th>
													<th width='12.5%' style='vertical-align:middle;color:#f4d03f;'>มูลค่าที่ชำระแล้ว</th>
													<th width='12.5%' style='vertical-align:middle;color:#f4d03f;'>อัตราภาษี</th>
													<th width='12.5%' style='vertical-align:middle;color:#f4d03f;'>ชำระล่าช้า (วัน)</th>
													<th width='12.5%' style='vertical-align:middle;color:#f4d03f;'>ชำระล่วงหน้า(วัน)</th>
													<th width='12.5%' style='vertical-align:middle;color:#f4d03f;'>เลขที่ใบกำกับค่างวด</th>
													<th width='12.5%' style='vertical-align:middle;color:#f4d03f;'>วันที่ใบกำกับ</th>
												</tr>
											</thead>
											<tbody style='white-space:nowrap;background-color:white;font-size:9pt;'></tbody>
										</table>
									</div>
								</div>
							</div>
						</div>
					</div>
				</fieldset>
			</div>";
		return $html;
	}
	function Searchoutsys(){
		$CUSCOD   = $_REQUEST['CUSCOD'];
		$CONTNO   = $_REQUEST['CONTNO'];
		$STRNO    = $_REQUEST['STRNO'];
		$F_SDATE  = $this->Convertdate(1,$_REQUEST['F_SDATE']);
		$T_SDATE  = $this->Convertdate(1,$_REQUEST['T_SDATE']);
		$cond = "";
		if($F_SDATE !== "" and $T_SDATE !== ""){
			$cond = " and B.SDATE between '".$F_SDATE."' and '".$T_SDATE."'";
		}
		$sql = "
			IF OBJECT_ID('tempdb..#OUTSYS') IS NOT NULL DROP TABLE #OUTSYS
			select LOCAT,CUSCOD,CONTNO,TSALE,STRNO,convert(varchar(8),SDATE,112) as SDATE
				,convert(varchar(8),DELDT,112) as DELDT,DELID,CLOSEAR
			into #OUTSYS
			FROM(
				select B.LOCAT,B.CUSCOD,B.CONTNO,'ขายผ่อน' as TSALE,B.STRNO,B.SDATE
					,D.CLDATE as DELDT,D.USERID as DELID,'ชำระเงินครบ' as CLOSEAR 
				from  {$this->MAuth->getdb('HARMAST')} B,{$this->MAuth->getdb('HINVTRAN')} C
				,{$this->MAuth->getdb('ARCLOSE')} D    
				where B.STRNO = C.STRNO and B.CONTNO = D.CONTNO and  
				B.CUSCOD like '".$CUSCOD."%' and B.CONTNO like '".$CONTNO."%' 
				and B.STRNO like '".$STRNO."%' ".$cond."  
				union 
				select B.LOCAT,B.CUSCOD,B.CONTNO,'ขายผ่อน' as TSALE,B.STRNO,B.SDATE
					,D.YDATE as DELDT,B.DELID,'ยึดเปลี่ยนสภาพ' as CLOSEAR 
				from {$this->MAuth->getdb('HARMAST')} B,{$this->MAuth->getdb('INVTRAN')} C
				,{$this->MAuth->getdb('ARHOLD')} D  
				where B.STRNO = C.STRNO and B.CONTNO = D.CONTNO and  
				B.CUSCOD like '".$CUSCOD."%' and B.CONTNO like '".$CONTNO."%' 
				and B.STRNO like '".$STRNO."%' ".$cond."  
				union 
				select B.LOCAT,B.CUSCOD,B.CONTNO,'ขายผ่อน' as TSALE,B.STRNO,B.SDATE
					,D.YDATE as DELDT,D.CHECKER  as DELID ,'แลกเปลี่ยน' as CLOSEAR 
				from {$this->MAuth->getdb('HARMAST')} B,{$this->MAuth->getdb('INVTRAN')} C
				,{$this->MAuth->getdb('ARCHAG')} D    
				where B.STRNO = C.STRNO and B.CONTNO = D.CONTNO and  
				B.CUSCOD like '".$CUSCOD."%' and B.CONTNO like '".$CONTNO."%' 
				and B.STRNO like '".$STRNO."%' ".$cond."  
				union 
				select B.LOCAT,B.CUSCOD,B.CONTNO,'ขายผ่อน' as TSALE,B.STRNO,B.SDATE
					,D.LOSTDT as DELDT,D.LOSTCOD as DELID  
				,'หนี้สูญ' as CLOSEAR from {$this->MAuth->getdb('HARMAST')} B,{$this->MAuth->getdb('INVTRAN')} C
				,{$this->MAuth->getdb('ARLOST')} D   
				where B.STRNO = C.STRNO and B.CONTNO = D.CONTNO and  
				B.CUSCOD like '".$CUSCOD."%' and B.CONTNO like '".$CONTNO."%' 
				and B.STRNO like '".$STRNO."%' ".$cond."
				union 
				select B.LOCAT,B.CUSCOD,B.CONTNO,'ขายส่งไฟแนนซ์' as TSALE,B.STRNO,B.SDATE
					,D.CLDATE as DELDT,D.USERID as DELID
				,'ชำระเงินครบ' as CLOSEAR 
				from {$this->MAuth->getdb('HARFINC')} B,{$this->MAuth->getdb('HINVTRAN')} C
				,{$this->MAuth->getdb('ARCLOSE')} D  
				where B.STRNO = C.STRNO and B.CONTNO = D.CONTNO and  
				B.CUSCOD like '".$CUSCOD."%' and B.CONTNO like '".$CONTNO."%' 
				and B.STRNO like '".$STRNO."%' ".$cond."
				union
				select B.LOCAT,B.CUSCOD,B.CONTNO,'ขายสด' as TSALE,B.STRNO,B.SDATE
					,D.CLDATE as DELDT,D.USERID as DELID,'ชำระเงินครบ' as CLOSEAR 
				from {$this->MAuth->getdb('HARCRED')} B,{$this->MAuth->getdb('HINVTRAN')} C
				,{$this->MAuth->getdb('ARCLOSE')} D 
				where B.STRNO = C.STRNO and B.CONTNO = D.CONTNO and  
				B.CUSCOD like '".$CUSCOD."%' and B.CONTNO like '".$CONTNO."%' 
				and B.STRNO like '".$STRNO."%' ".$cond."     
				union 
				select B.LOCAT,A.CUSCOD,B.CONTNO,'ขายส่งเอเย่นต์' as TSALE,B.STRNO,A.SDATE
					,D.CLDATE as DELDT,D.USERID as DELID,'ชำระเงินครบ' as CLOSEAR 
				from {$this->MAuth->getdb('HAR_INVO')} A,{$this->MAuth->getdb('HAR_TRNS')} B
				,{$this->MAuth->getdb('HINVTRAN')} C,{$this->MAuth->getdb('ARCLOSE')} D
				where A.CONTNO = B.CONTNO and B.STRNO = C.STRNO and A.CONTNO = D.CONTNO and
				A.CUSCOD like '".$CUSCOD."%' and B.CONTNO like '".$CONTNO."%' 
				and B.STRNO like '".$STRNO."%' ".$cond."
			)OUTSYS
		";
		//echo $sql;
		$this->db->query($sql);
		$sql = "
			select top 1000 A.*,B.SNAM+B.NAME1+' '+B.NAME2+'('+B.CUSCOD+')' as CUSNAME from #OUTSYS A
			left join {$this->MAuth->getdb('CUSTMAST')} B on A.CUSCOD = B.CUSCOD
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		$outsys = ""; $NRow = "";
		$arrs = array();
		$response = array();
		if($query->row()){
			foreach($query->result() as $row){
				$arrs['cusid'][]    = $row->CUSCOD;
				$arrs['custext'][]  = $row->CUSNAME;
				$arrs['contno'][]   = $row->CONTNO;
				$arrs['strno'][]    = $row->STRNO;
				$outsys .="
					<tr class='trow' style='height:20px;' seq=".$NRow.">
						<td class='getit' seq=".$NRow++." CONTNO='".$row->CONTNO."' style='vertical-align:middle;cursor:pointer;color:blue;'>".$row->CONTNO."</td>
						<td style='vertical-align:middle;'>".$row->LOCAT."</td>
						<td style='vertical-align:middle;'>".$row->CUSCOD."</td>
						<td style='vertical-align:middle;'>".$row->TSALE."</td>
						<td style='vertical-align:middle;'>".$row->STRNO."</td>
						<td style='vertical-align:middle;'>".$this->Convertdate(2,$row->SDATE)."</td>
						<td style='vertical-align:middle;'>".$this->Convertdate(2,$row->DELDT)."</td>
						<td style='vertical-align:middle;'>".$row->DELID."</td>
						<td style='vertical-align:middle;'>".$row->CLOSEAR."</td>
					</tr>
				";
			}
		}else{
			$arrs['cusid'][]   = "";
			$arrs['custext'][] = "";
			$arrs['contno'][]  = "";
			$arrs['strno'][]   = "";
			$outsys = "<tr class='trow'><td colspan='9' style='color:red;height:100%;'>ไม่มีข้อมูล</td></tr>";
		}
		//echo $response['cuscod'][0];
		$response['cusid']    = $arrs['cusid'][0];
		$response['custext']  = $arrs['custext'][0];
		$response['contno']   = $arrs['contno'][0];
		$response['strno']    = $arrs['strno'][0];
		$response['outsys']   = $outsys;
		
		$sql = "
			select CONTNO,LOCAT,NOPAY,CONVERT(varchar(8),DDATE,112) as DDATE,DAMT,V_DAMT,N_DAMT
				,NINSTAL,NPROF,CONVERT(varchar(8),DATE1,112) as DATE1,PAYMENT,V_PAYMENT,N_PAYMENT
				,VATRT,DELAY,ADVDUE,TAXINV,CONVERT(varchar(8),TAXDT,112) as TAXDT 
			from {$this->MAuth->getdb('HARPAY')}
			where CONTNO = '".$arrs['contno'][0]."'
		";
		$query = $this->db->query($sql);
		$harpay = "";
		if($query->row()){
			foreach($query->result() as $row){
				$harpay .="
					<tr class='trow' style='height:20px;'>
						<td style='vertical-align:middle;'>".$row->CONTNO."</td>
						<td style='vertical-align:middle;'>".$row->LOCAT."</td>
						<td style='vertical-align:middle;'>".$row->NOPAY."</td>
						<td style='vertical-align:middle;'>".$this->Convertdate(2,$row->DDATE)."</td>
						<td style='vertical-align:middle;'>".number_format($row->DAMT,2)."</td>
						<td style='vertical-align:middle;'>".number_format($row->V_DAMT,0)."</td>
						<td style='vertical-align:middle;'>".number_format($row->N_DAMT,2)."</td>
						<td style='vertical-align:middle;'>".number_format($row->NINSTAL,2)."</td>
						<td style='vertical-align:middle;'>".number_format($row->NPROF,2)."</td>
						<td style='vertical-align:middle;'>".$this->Convertdate(2,$row->DATE1)."</td>
						<td style='vertical-align:middle;'>".number_format($row->PAYMENT,2)."</td>
						<td style='vertical-align:middle;'>".number_format($row->V_PAYMENT,2)."</td>
						<td style='vertical-align:middle;'>".number_format($row->N_PAYMENT,2)."</td>
						<td style='vertical-align:middle;'>".number_format($row->VATRT,0)."</td>
						<td style='vertical-align:middle;'>".number_format($row->DELAY,0)."</td>
						<td style='vertical-align:middle;'>".number_format($row->ADVDUE,0)."</td>
						<td style='vertical-align:middle;'>".$row->TAXINV."</td>
						<td style='vertical-align:middle;'>".$this->Convertdate(2,$row->TAXDT)."</td>
					</tr>
				";
			}
		}else{
			$harpay = "<tr class='trow'><td colspan='18' style='color:red;height:100%;'>ไม่มีข้อมูล</td></tr>";
		}
		//echo $sql; exit;
		$response['harpay'] = $harpay;
		echo json_encode($response);
	}
	function getHarpayDetail(){
		$CONTNO = $_REQUEST['CONTNO'];
		$response = array();
		$sql = "
			select CONTNO,LOCAT,NOPAY,CONVERT(varchar(8),DDATE,112) as DDATE,DAMT,V_DAMT,N_DAMT
				,NINSTAL,NPROF,CONVERT(varchar(8),DATE1,112) as DATE1,PAYMENT,V_PAYMENT,N_PAYMENT
				,VATRT,DELAY,ADVDUE,TAXINV,CONVERT(varchar(8),TAXDT,112) as TAXDT 
			from {$this->MAuth->getdb('HARPAY')}
			where CONTNO = '".$CONTNO."'
		";
		$query = $this->db->query($sql);
		$harpay = "";
		if($query->row()){
			foreach($query->result() as $row){
				$harpay .="
					<tr class='trow' style='height:20px;'>
						<td style='vertical-align:middle;'>".$row->CONTNO."</td>
						<td style='vertical-align:middle;'>".$row->LOCAT."</td>
						<td style='vertical-align:middle;'>".$row->NOPAY."</td>
						<td style='vertical-align:middle;'>".$this->Convertdate(2,$row->DDATE)."</td>
						<td style='vertical-align:middle;'>".number_format($row->DAMT,2)."</td>
						<td style='vertical-align:middle;'>".number_format($row->V_DAMT,0)."</td>
						<td style='vertical-align:middle;'>".number_format($row->N_DAMT,2)."</td>
						<td style='vertical-align:middle;'>".number_format($row->NINSTAL,2)."</td>
						<td style='vertical-align:middle;'>".number_format($row->NPROF,2)."</td>
						<td style='vertical-align:middle;'>".$this->Convertdate(2,$row->DATE1)."</td>
						<td style='vertical-align:middle;'>".number_format($row->PAYMENT,2)."</td>
						<td style='vertical-align:middle;'>".number_format($row->V_PAYMENT,2)."</td>
						<td style='vertical-align:middle;'>".number_format($row->N_PAYMENT,2)."</td>
						<td style='vertical-align:middle;'>".number_format($row->VATRT,0)."</td>
						<td style='vertical-align:middle;'>".number_format($row->DELAY,0)."</td>
						<td style='vertical-align:middle;'>".number_format($row->ADVDUE,0)."</td>
						<td style='vertical-align:middle;'>".$row->TAXINV."</td>
						<td style='vertical-align:middle;'>".$this->Convertdate(2,$row->TAXDT)."</td>
					</tr>
				";
			}
		}else{
			$harpay = "<tr class='trow'><td colspan='18' style='color:red;height:100%;'>ไม่มีข้อมูล</td></tr>";
		}
		//echo $sql; exit;
		$response['harpay'] = $harpay;
		echo json_encode($response);
	}
	function calculate(){
		$claim = $this->MLogin->getclaim(uri_string());
		if($claim['m_access'] != "T"){ echo "<div align='center' style='color:red;font-size:16pt;width:100%;'>ขออภัย คุณยังไม่มีสิทธิเข้าใช้งานหน้านี้ครับ</div>"; exit; }
		
		$sql = "
			select * from {$this->MAuth->getdb('VATMAST')}
			where getdate() between FRMDATE and TODATE
		";
		$query = $this->db->query($sql);
		
		$VATRT = "";
		if($query->row()){
			foreach($query->result() as $row){
				$VATRT .= number_format($row->VATRT,2);
			}
		}
		
		
		$html = "
			<div class='tab1' name='home' locat='{$this->sess['branch']}' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}'>
				<div class='col-sm-12'>
					<div class='row'>
						<div class='col-sm-2'>	
							<div class='form-group'>
								ราคารถ
								<input type='text' id='price' class='form-control input-sm jzAllowNumber' placeholder='ราคารถ' >
								<br>
								เงินดาวน์
								<input type='text' id='dwn' class='form-control input-sm jzAllowNumber' placeholder='เงินดาวน์' >
								<br>
								ภาษี
								<input type='text' id='vat' class='form-control input-sm jzAllowNumber' placeholder='ภาษี'  value='".$VATRT."'>
								<br>
								อุปกรณ์เสริม
								<input type='text' id='opt' class='form-control input-sm jzAllowNumber' placeholder='เงินดาวน์' >
								<br>
								อัตราดอกเบี้ยต่อเดือน
								<input type='text' id='intrt' class='form-control input-sm jzAllowNumber' placeholder='เงินดาวน์' >
								<br>
								งวด
								<input type='number' id='nopay' class='form-control input-sm jzAllowNumber' placeholder='งวด'  value='36'>
								<br>
								ปัดทศนิยม
								<input type='text' id='dcm' class='form-control input-sm jzAllowNumber' value='5' disabled>
								
								<br>
								<button id='calc' class='btn btn-primary btn-block'><span class='glyphicon glyphicon-search'> คำนวณ</span></button>
								
								<br>
								<button id='btn_penalty' class='btn btn-primary btn-block'><span class='glyphicon glyphicon-search'> ตรวจสอบเบี้ยปรับ</span></button>
							</div>
						</div>
						<div class='col-sm-10' id='result' style='border:0.1px dotted red;background-color:red;'></div>
					</div>
				</div>
			</div>
		";
		
		$html.= "<script src='".base_url('public/js/SYS04/QuestionCalcurate.js')."'></script>";
		echo $html;
	}
	
	function CalPenalty(){
		$CONTNO = $_POST["CONTNO"];
		$CALDT	= $this->Convertdate(1,$_POST["CALDT"]);
		
		$sql = "
			declare @CONTNO varchar(20) = '{$CONTNO}';
			declare @CALDT datetime = '{$CALDT}'
			
			select * from {$this->MAuth->getdb('fn_JDLatePenalty_20200930')}(@CONTNO,@CALDT)			
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		$NOPAY 	= 1;
		$bg 	= "style='background-color:#ddd;'";
		$DAMT	= 0;
		$sumINTAMT 	  = 0;
		$sumINTAMTCal = 0;
		if($query->row()){
			foreach($query->result() as $row){
				if($NOPAY == $row->NOPAY){
					if($bg == "style='background-color:#ddd;'"){
						$bg = "style='background-color:#ddd;'";
					}else{
						$bg = "style='background-color:#fff;'";
					}
					$NOPAY = $row->NOPAY;
				}else{
					if($bg == "style='background-color:#ddd;'"){
						$bg = "style='background-color:#fff;'";
					}else{
						$bg = "style='background-color:#ddd;'";
					}
					$NOPAY = $row->NOPAY;
				}
				
				if($row->NOPAY==1 && $row->rNOPAY==1){
					$DAMT = $row->DAMT;
				}
				
				if($DAMT != $row->DAMT){
					$bg = "style='background-color:yellow;'";
				}
				
				$html .= "<tr {$bg}>";
				foreach($row as $key => $val){
					switch($key){
						case 'NOPAY':
						case 'rNOPAY':
						case 'DELYRT':
						case 'DLDAY': $html .= "<td align='center'>".$val."</td>"; break;
						case 'DAMT': 
						case 'BILLPAY': 
						case 'bl': 
						case 'total': 
						case 'ddiff': 
						case 'INTAMTCal': $html .= "<td align='right'>".number_format($val,0)."</td>"; break;
						case 'INTAMTCalD': $html .= "<td align='right'>".number_format($val,3)."</td>"; break;
						case 'INTAMT': 
							$html .= "<td align='right'>".($row->rNOPAY == 1 ? number_format($val,0):"#####")."</td>";	
							break;
						case 'SYSTVER': $html .= "<td align='center'>".$val."</td>"; break;
						default: $html .= "<td>".$val."</td>"; break;
					}
				}
				$html .= "</tr>";
				
				$sumINTAMT += $row->INTAMT;
				$sumINTAMTCal += $row->INTAMTCal;
			}
		}
		
		$html = "
			<table id='tbCalPenalty' class='table table-border'>
				<thead>
					<tr>
						<th>สัญญา </th>
						<th>สาขา</th>
						<th>งวด</th>
						<th>งวด<br>ครั้งที่ชำระ</th>
						<th>ดอกเบี้ย</th>
						<th>ล่าช้า<br>ไม่เกิน<br>(วัน)</th>
						<th>ดิวเดต</th>
						<th>ค่างวด</th>
						<th>เลขที่บิล</th>
						<th>วันที่บิล</th>
						<th>ชำระ<br>ค่างวด</th>
						<th>ชำระ<br>ต่องวด</th>
						<th>เงินต้น<br>คงเหลือ</th>
						<th>ขาด<br>กตต.</th>
						<th>เบี้ยปรับ<br>คำนวณ<br>ทศนิยม</th>
						<th>เบี้ยปรับ<br>คำนวณ</th>
						<th>เบี้ยปรับ<br>senior</th>
						<th>ช่องทาง</th>
					</tr>
				</thead>
				<tbody>".$html."</tbody>
				<tfoot>
					<tr>
						<th colspan='15'></th>
						<th align='left'>".$sumINTAMTCal."</th>
						<th align='left'>".$sumINTAMT."</th>
						<th ></th>
					</tr>
				</tfoot>
			</table>			
		";
		
		$this->response["html"] = $html;
		echo json_encode($this->response);
	}
	
	function CalcuratePrice(){
		$arrs = array();
		$arrs["price"]  = (($_POST["price"] != "") ? $_POST["price"] : 0);
		$arrs["dwn"] 	= (($_POST["dwn"] != "") ? $_POST["dwn"] : 0);
		$arrs["vat"] 	= (($_POST["vat"] != "") ? $_POST["vat"] : 0);
		$arrs["opt"] 	= (($_POST["opt"] != "") ? $_POST["opt"] : 0);
		$arrs["intrt"]	= (($_POST["intrt"] != "") ? $_POST["intrt"] : 0);
		$arrs["nopay"] 	= (($_POST["nopay"] != "") ? $_POST["nopay"] : 0);
		$arrs["dcm"]	= (($_POST["dcm"] != "") ? $_POST["dcm"] : 0);
		//echo $arrs["dwn"]; exit; 
		if($arrs['price'] == ""){
			$response = array(
				"error"=>true,
				"msg"=>"ผิดพลาด คุณยังไม่ได้ระบุราคารถ"
			);
			echo json_encode($response); exit;
		}
		if($arrs['intrt'] == ""){
			$response = array(
				"error"=>true,
				"msg"=>"ผิดพลาด คุณยังไม่ได้ระบุอัตราดอกเบี้ยต่อเดือน"
			);
			echo json_encode($response); exit;
		}
		
		$sql = "
			select * 
				,cast((INTERAST_RATE / 12) as decimal(18,2)) as INTERAST_RATE_MONTH
				,PRICE_TOTAL - cast(OPTPERMONTH_AFTER_VAT_TOTAL * NOPAY as decimal(18,2)) as PRICEHP_AFTER_VAT_TOTAL				
				,cast(OPTPERMONTH_AFTER_VAT_TOTAL * NOPAY as decimal(18,2)) as OPT_AFTER_VAT_TOTAL
			from {$this->MAuth->getdb('fn_jd_calPriceForSale')}(
				'".str_replace(',','',$arrs['price'])."',
				'".str_replace(',','',$arrs['dwn'])."',
				'".str_replace(',','',$arrs['vat'])."',
				'".str_replace(',','',$arrs['opt'])."',
				'".str_replace(',','',$arrs['intrt'])."',
				'".str_replace(',','',$arrs['nopay'])."',
				'".str_replace(',','',$arrs['dcm'])."'
			)
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$html .= "
					<div class='col-sm-12'>
						<div class='row'>
							<div class='col-sm-3' style='background-color:#f6c6c6;'>	
								<div class='form-group'>
									ราคารถรวม VAT 
									<span class='form-control'>".number_format($row->PRICE_AFTER_VAT,2)."</span>
									<br>
									เงินดาวน์รวม VAT 
									<span class='form-control'>".number_format($row->DWN_AFTER_VAT,2)."</span>
									<br>
									ยอดตั้งลูกหนี้รวม VAT
									<span class='form-control'>".number_format($row->PRICEDOWN_AFTER_VAT,2)."</span>
									<br>
									อัตราดอกเบี้ยต่อปี
									<span class='form-control'>".number_format($row->INTERAST_RATE,2)."</span>
									<br>
									ดอกผลเช่าซื้อรวม VAT
									<span class='form-control'>".number_format($row->HP_AFTER_VAT,2)."</span>
									<br>
									 ราคาขายผ่อนก่อน VAT 
									<span class='form-control'>".number_format($row->PRICEHP_BEFORE_VAT,2)."</span>
									<br>
									 ราคาขายผ่อนรวม VAT 
									<span class='form-control'>".number_format($row->PRICEHP_AFTER_VAT,2)."</span>
									<br>
									 ราคาขายผ่อนสุทธิรวม VAT 
									<span class='form-control'>".number_format($row->PRICEHP_AFTER_VAT_TOTAL,2)."</span>
								</div>
							</div>
							<div class='col-sm-3' style='background-color:#f6c6c6;'>	
								<div class='form-group'>
									ราคารถก่อน VAT 
									<span class='form-control'>".number_format($row->PRICE_BEFORE_VAT,2)."</span>
									<br>
									 เงินดาวน์ก่อน VAT 
									<span class='form-control'>".number_format($row->DWN_BEFORE_VAT,2)."</span>
									<br>
									 ยอดตั้งลูกหนี้ก่อน VAT
									<span class='form-control'>".number_format($row->PRICEDOWN_BEFORE_VAT,2)."</span>
									<br>
									อัตราดอกเบี้ยต่อเดือน
									<span class='form-control'>".number_format($row->INTERAST_RATE_MONTH,2)."</span>
									<br>
									ดอกผลเช่าซื้อก่อน VAT
									<span class='form-control'>".number_format($row->HP_BEFORE_VAT,2)."</span>
									<br>
									ผ่อนงวดละรวม
									<span class='form-control'>".number_format($row->PERMONTH_BEFORE_VAT,2)."</span>
									<br>
									 ผ่อนงวดละรวม VAT 
									<span class='form-control'>".number_format($row->PERMONTH_AFTER_VAT,2)."</span>
									<br>
									  ผ่อนงวดละรวม VAT 
									<span class='form-control'>".number_format($row->PERMONTH_AFTER_VAT_TOTAL,2)."</span>
								</div>
							</div>
							
							<div class='col-sm-3' style='background-color:#c6e4f6;'>	
								<div class='form-group'>
									 ราคาขายสดรวม VAT 
									<span class='form-control'>".number_format($row->OPT_AFTER_VAT,2)."</span>
									<div style='min-height:296px;'></div>
									ดอกผลเช่าซื้อก่อน VAT
									<span class='form-control'>".number_format($row->OPT_BEFORE_VAT,2)."</span>
									<br>
									ดอกผลเช่าซื้อรวม VAT
									<span class='form-control'>".number_format($row->OPT_AFTER_VAT,2)."</span>
									<br>
									 ราคาขายผ่อนสุทธิรวม VAT 
									<span class='form-control'>".number_format($row->OPT_AFTER_VAT_TOTAL,2)."</span>
								</div>
							</div>
							<div class='col-sm-3' style='background-color:#c6e4f6;'>	
								<div class='form-group'>
									 ราคาขายสดก่อน VAT 
									<span class='form-control'>".number_format($row->OPT_BEFORE_VAT,2)."</span>
									<div style='min-height:296px;'></div>
									ผ่อนงวดละรวม
									<span class='form-control'>".number_format($row->OPTPERMONTH_AFTER_VAT,2)."</span>
									<br>
									 ผ่อนงวดละรวม VAT 
									<span class='form-control'>".number_format($row->OPTPERMONTH_AFTER_VAT,2)."</span>
									<br>
									  ผ่อนงวดละรวม VAT 
									<span class='form-control'>".number_format($row->OPTPERMONTH_AFTER_VAT_TOTAL,2)."</span>
								</div>
							</div>
						</div>
						
						<div class='row' style='background-color:#d7f6c6;'>
							<div class='col-sm-3'>	
								<div class='form-group'>
									<b>ดอกผลเช่าซื้อรวม VAT</b>
									<span class='form-control'>".number_format($row->HP_TOTAL,2)."</span>
								</div>	
							</div>
							<div class='col-sm-3 col-sm-offset-3'>	
								<div class='form-group'>
									<b>ผ่อนงวดละ+อุปกรณ์รวม VAT</b>
									<span class='form-control'>".number_format($row->PERMONTH_TOTAL,2)."</span>
								</div>	
							</div>
							<div class='col-sm-3'>	
								<div class='form-group'>
									<b>ราคาขายผ่อน+อุปกรณ์รวม VAT</b>
									<span class='form-control'>".number_format($row->PRICE_TOTAL,2)."</span>
								</div>	
							</div>
							
						</div>
					</div>
				";
			}
		}
		
		$response = array("html"=>$html);
		echo json_encode($response);
	}

}




















