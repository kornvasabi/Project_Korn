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
						SELECT TOTPRC-SMPAY AS BALAR FROM ARMAST aa WHERE aa.CUSCOD=a.CUSCOD  
						UNION SELECT TOTPRC-SMPAY AS BALAR FROM ARCRED aa WHERE aa.CUSCOD=a.CUSCOD
						UNION SELECT TOTPRC-SMPAY AS BALAR FROM ARFINC aa WHERE aa.CUSCOD=a.CUSCOD
						UNION SELECT TOTPRC-SMPAY AS BALAR FROM AR_INVOI aa WHERE aa.CUSCOD=a.CUSCOD
						UNION SELECT PAYAMT-SMPAY AS BALAR FROM AROTHR aa WHERE aa.CUSCOD=a.CUSCOD
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
	
	function group_ins(){
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
	
	function cancelcont(){
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
	
	function outsyscont(){
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




















