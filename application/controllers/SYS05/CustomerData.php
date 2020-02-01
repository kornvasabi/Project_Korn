<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//BEE+
class CustomerData extends MY_Controller {
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
			<div class='b_tab1' name='home' locat='{$this->sess['branch']}' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}' today='".$this->today('today')."' style='height:calc(100vh - 132px);overflow:auto;background-color:#fcfcfc;'>
				<div class='col-sm-12 col-xs-12' style='overflow:auto;height:100%;border:4px solid white;'>					
					<div class='row' style='height:11%;background-color:#fef5c3;border:0.5px solid #eee;border-radius:8px;'>
						<div class='col-sm-3 col-xs-3'>	
							<div class='form-group text-primary'>
								<b>ลูกค้า</b>
								<select id='CUSCOD1' class='form-control input-sm' data-placeholder='ลูกค้า'></select>
							</div>
						</div>
						<div class='col-sm-6 col-xs-6'>	
							<div class='form-group text-primary'>
								รายละเอียดลูกค้า
								<input type='text' id='DESCRIPTION' class='form-control input-sm' placeholder='รายละเอียด' readonly>
							</div>
						</div>
						<div class='col-sm-3 col-xs-3'>	
							<div class='form-group'>
								<br>
								<button id='btnt1search' class='btn btn-primary btn-sm' style='width:100%'><span class='glyphicon glyphicon-search'> สอบถาม</span></button>
							</div>
						</div>
					</div>
					<br><b>การเป็นผู้ซื้อ</b>
					<div class='row' style='height:51%;border:0.1px dotted #bdbdbd;'>
						<div class='col-sm-12 col-xs-12' style='height:50%;border:5px solid white;'>
							<div id='dataTable-fixed-HCsale' class='dataTables_wrapper dt-bootstrap4 table-responsive' style='height:100%;width:100%;overflow:auto;'>
								<table id='dataTables-HCsale' class='table table-bordered dataTable table-hover' stat='' aria-describedby='dataTable_info' cellspacing='0' width='calc(100% - 1px)'>
									<thead>
										<tr role='row' style='height:25px;font-size:8pt;background-color:#2fa39d;color:white;'>
											<th width='6%' style='text-align:center;'>#</th>
											<th width='11%' style='text-align:center;'>สาขา</th>
											<th width='11%' style='text-align:center;'>เลขที่สัญญา</th>
											<th width='11%' style='text-align:center;'>วันขาย</th>
											<th width='11%' style='text-align:center;'>เลขตัวถัง</th>
											<th width='11%' style='text-align:center;'>ราคาขาย</th>
											<th width='11%' style='text-align:center;'>รับชำระแล้ว</th>
											<th width='11%' style='text-align:center;'>เช็ครอเรียกเก็บ</th>
											<th width='11%' style='text-align:center;'>พนักงานขาย</th>
											<th width='6%' style='text-align:center;'>Tsale</th>
										</tr>
									</thead>
									<tbody style='white-space:nowrap;background-color:white;font-size:9pt;'></tbody>
								</table>
							</div>
						</div>
						<div class='col-sm-12 col-xs-12' style='height:50%;;border:5px solid white;'>
							<div id='dataTable-fixed-AOsale' class='dataTables_wrapper dt-bootstrap4 table-responsive' style='height:100%;width:100%;overflow:auto;'>
								<table id='dataTables-AOsale' class='table table-bordered dataTable table-hover' stat='' aria-describedby='dataTable_info' cellspacing='0' width='calc(100% - 1px)'>
									<thead>
										<tr role='row' style='height:25px;font-size:8pt;background-color:#2fa39d;color:white;'>
											<th width='6%' style='text-align:center;'>#</th>
											<th width='11%' style='text-align:center;'>สาขา</th>
											<th width='11%' style='text-align:center;'>เลขที่สัญญา</th>
											<th width='11%' style='text-align:center;'>วันขาย</th>
											<th width='11%' style='text-align:center;'>เลขตัวถัง</th>
											<th width='11%' style='text-align:center;'>ราคาขาย</th>
											<th width='11%' style='text-align:center;'>รับชำระแล้ว</th>
											<th width='11%' style='text-align:center;'>เช็ครอเรียกเก็บ</th>
											<th width='11%' style='text-align:center;'>พนักงานขาย</th>
											<th width='6%' style='text-align:center;'>Tsale</th>
										</tr>
									</thead>
									<tbody style='white-space:nowrap;background-color:white;font-size:9pt;'></tbody>
								</table>
							</div>
						</div>
					</div>
					<br><b>การเป็นผู้ค้ำ</b>
					<div class='row' style='height:25%;border:0.1px dotted #bdbdbd;'>
						<div class='col-sm-12 col-xs-12' style='height:100%;border:5px solid white;'>
							<div id='dataTable-fixed-ARmgra' class='dataTables_wrapper dt-bootstrap4 table-responsive' style='height:100%;width:100%;overflow:auto;'>
								<table id='dataTables-ARmgra' class='table table-bordered dataTable table-hover' stat='' aria-describedby='dataTable_info' cellspacing='0' width='calc(100% - 1px)'>
									<thead>
										<tr role='row' style='height:25px;font-size:8pt;background-color:#1ba0b7;color:white;'>
											<th width='6%' style='text-align:center;'>#</th>
											<th width='11%' style='text-align:center;'>ผู้ค้ำคนที่</th>
											<th width='11%' style='text-align:center;'>สาขา</th>
											<th width='22%' style='text-align:center;'>เลขที่สัญญา</th>
											<th width='50%' style='text-align:center;'>ความสัมพันธ์กับผู้ซื้อ</th>
										</tr>
									</thead>
									<tbody style='white-space:nowrap;background-color:white;font-size:9pt;'></tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		";
		
		$html.= "<script src='".base_url('public/js/SYS05/CustomerData.js')."'></script>";
		echo $html;
	}
	
	function Customerdetail(){
		$cuscod	= $_REQUEST["cuscod"];

		$sql = "
				select a.CUSCOD , ADDR1+'  ต.'+TUMB+'  อ.'+AUMPDES+'  จ.'+PROVDES+'  '+ZIP+'  โทร: '+
				case when TELP IS NULL or TELP = '' then '-' else TELP end as CUSTADD
				from {$this->MAuth->getdb('CUSTADDR')} a
				left join {$this->MAuth->getdb('SETAUMP')} b on a.AUMPCOD = b.AUMPCOD
				left join {$this->MAuth->getdb('SETPROV')} c on a.PROVCOD = c.PROVCOD
				where ADDRNO = '1' and CUSCOD = '".$cuscod."'
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$response = array();
		if($query->row()){
			foreach($query->result() as $row){
				$response["CUSCOD"] 	= $row->CUSCOD;
				$response["CUSTADD"] 	= $row->CUSTADD;
			}
		}
		
		echo json_encode($response);
	}
	
	function search(){
		$CUSCOD1 = $_REQUEST["CUSCOD1"];
		
		$sql = "
				select LOCAT, CONTNO, TSALE, CUSCOD, SDATE, convert(nvarchar,SDATE,103) as SDATES, STRNO, SALCOD, TOTPRC, SMPAY, SMCHQ
				from {$this->MAuth->getdb('ARMAST')}
				where CUSCOD = '".$CUSCOD1."'
				union
				select LOCAT, CONTNO, TSALE, CUSCOD, SDATE, convert(nvarchar,SDATE,103) as SDATES, STRNO, SALCOD, TOTPRC, SMPAY, SMCHQ
				from {$this->MAuth->getdb('ARCRED')}
				where CUSCOD = '".$CUSCOD1."'
				union
				select LOCAT, CONTNO, TSALE, CUSCOD,SDATE, convert(nvarchar,SDATE,103) as SDATES, STRNO, SALCOD, TOTPRC, SMPAY, SMCHQ
				from {$this->MAuth->getdb('ARFINC')}
				where CUSCOD = '".$CUSCOD1."'
				order by LOCAT, CONTNO, SDATE desc
		";
		//echo $sql; 
		$query = $this->db->query($sql);
		
		$sql2 = "
				select a.LOCAT, a.CONTNO, a.TSALE, a.CUSCOD, a.SDATE, convert(nvarchar,a.SDATE,103) as SDATES, b.STRNO, a.SALCOD, a.TOTPRC, a.SMPAY, a.SMCHQ
				from {$this->MAuth->getdb('AR_INVOI')} a
				left join {$this->MAuth->getdb('AR_TRANS')} b on a.CONTNO = b.CONTNO and a.LOCAT = b.LOCAT
				where a.CUSCOD = '".$CUSCOD1."'
				union
				select a.LOCAT, a.CONTNO, a.TSALE, a.CUSCOD, a.SDATE, convert(nvarchar,a.SDATE,103) as SDATES, b.OPTCODE as STRNO, a.SALCOD, a.OPTPTOT as TOTPRC, 
				a.SMPAY, a.SMCHQ
				from {$this->MAuth->getdb('AROPTMST')} a
				left join {$this->MAuth->getdb('ARINOPT')} b on a.CONTNO = b.CONTNO and a.LOCAT = b.LOCAT and a.TSALE = b.TSALE
				where a.CUSCOD = '".$CUSCOD1."'
				order by LOCAT, CONTNO, SDATE desc
		";
		//echo $sql2; 
		$query2 = $this->db->query($sql2);
		
		$sql3 = "
				select LOCAT, CONTNO, GARNO, RELATN
				from {$this->MAuth->getdb('ARMGAR')}
				where CUSCOD = '".$CUSCOD1."'
				order by LOCAT, CONTNO
		";
		//echo $sql3; 
		$query3 = $this->db->query($sql3);
		
		$head = ""; $head2 = ""; 
	
		$head = "<tr style='background-color:#ebedff;'>
				<th style='text-align:center;'>#</th>
				<th style='text-align:center;'>สาขา</th>
				<th style='text-align:center;'>เลขที่สัญญา</th>
				<th style='text-align:center;'>วันขาย</th>
				<th style='text-align:center;'>เลขตัวถัง</th>
				<th style='text-align:center;'>ราคาขาย</th>
				<th style='text-align:center;'>รับชำระแล้ว</th>
				<th style='text-align:center;'>เช็ครอเรียกเก็บ</th>
				<th style='text-align:center;'>พนักงานขาย</th>
				<th style='text-align:center;'>Tsale</th>
				</tr>
		";
		
		$head2 = "
				<tr style='background-color:#dbf5f5;'>
				<th style='text-align:center;'>#</th>
				<th style='text-align:center;'>ผู้ค้ำคนที่</th>
				<th style='text-align:center;'>สาขา</th>
				<th style='text-align:center;'>เลขที่สัญญา</th>
				<th style='text-align:center;'>ความสัมพันธ์กับผู้ซื้อ</th>
				</tr>
		";

		$html = ""; $NRow = 1; $No = 1;
		if($query->row()){
			foreach($query->result() as $row){
				$html .= "
					<tr class='trow' seq=".$NRow." >
						<td align='center' seq=".$NRow++.">".$No++."</td>
						<td align='center'>".$row->LOCAT."</td>
						<td align='center'>".$row->CONTNO."</td>
						<td align='center'>".$row->SDATES."</td>
						<td align='center'>".$row->STRNO."</td>
						<td align='right'>".number_format($row->TOTPRC,2)."</td>
						<td align='right'>".number_format($row->SMPAY,2)."</td>
						<td align='right'>".number_format($row->SMCHQ,2)."</td>
						<td align='center'>".$row->SALCOD."</td>
						<td align='center'>".$row->TSALE."</td>
					</tr>
				";	
			}
		}else{
			$html .= "<tr class='trow text-gray' ><td colspan='10'>ไม่พบข้อมูล</td></tr>";	
		}
		
		$html2 = ""; $NRow2 = 1; $No2 = 1;
		if($query2->row()){
			foreach($query2->result() as $row2){
				$html2 .= "
					<tr class='trow' seq=".$NRow2." >
						<td align='center' seq=".$NRow2++.">".$No2++."</td>
						<td align='center'>".$row2->LOCAT."</td>
						<td align='center'>".$row2->CONTNO."</td>
						<td align='center'>".$row2->SDATES."</td>
						<td align='center'>".$row2->STRNO."</td>
						<td align='right'>".number_format($row2->TOTPRC,2)."</td>
						<td align='right'>".number_format($row2->SMPAY,2)."</td>
						<td align='right'>".number_format($row2->SMCHQ,2)."</td>
						<td align='center'>".$row2->SALCOD."</td>
						<td align='center'>".$row2->TSALE."</td>
					</tr>
				";	
			}
		}else{
			$html2 .= "<tr class='trow text-gray' ><td colspan='10'>ไม่พบข้อมูล</td></tr>";
		}
		
		$html3 = ""; $NRow3 = 1; $No3 = 1;
		if($query3->row()){
			foreach($query3->result() as $row3){
				$html3 .= "
					<tr class='trow' seq=".$NRow3." >
						<td align='center' seq=".$NRow3++.">".$No3++."</td>
						<td align='center'>".$row3->GARNO."</td>
						<td align='center'>".$row3->LOCAT."</td>
						<td align='center'>".$row3->CONTNO."</td>
						<td align='center'>".$row3->RELATN."</td>
					</tr>
				";	
			}
		}else{
			$html3 .= "<tr class='trow text-gray' ><td colspan='5'>ไม่พบข้อมูล</td></tr>";
		}

		$response = array("html1"=>$html, "html2"=>$html2, "html3"=>$html3);
		echo json_encode($response);
	}
}