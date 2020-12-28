<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MDATA extends CI_Model {
	
	function loadLocat(){
		$sess = $this->session->userdata('cbjsess001');
		
		$sql = "select LOCATCD from {$sess['db']}.dbo.INVLOCAT order by LOCATCD";
		$query = $this->db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				$html .= "<option value='".$row->LOCATCD."'>".$row->LOCATCD."</option>";
			}
		}else{
			$html .= "<option value=''>-</option>";		
		}
		
		return $html;
	}
	
	function sysdt(){
		$sql = "select convert(varchar(8),getdate() ,112) as sysd,convert(varchar(5),getdate() ,108) as syst";
		$sysdt = $this->db->query($sql);
		$sysdt = $sysdt->row();
		$sysdt = $this->Convertdate(2,$sysdt->sysd).' '.$sysdt->syst;
		
		return $sysdt;
	}
	
	function getCODE_ASSESSMENT(){
		$db = $this->load->database('ASSESSMENT');
		$sql = "SELECT DOCNo FROM ASSESSMENT_DB.dbo.vGENERATE_CODE";
		$query = $db->query($sql);
		
		$data = "";
		if($query->row()){
			foreach($query->result() as $row){
				$data = $row->DOCNo;
			}
		}
		
		return $data;
	}
	
	public function Convertdate($param,$date){
		// $param = 1 > to Database
		// $param = 2 > to User Interface
		if($date == ''){
			return '';
		}else{
			if($param == 1){
				$dd = substr($date, 0,2);
				$mm = substr($date, 3,2);
				$yy = substr($date, 6,4) - 543;
				return $yy.$mm.$dd;
			}else{
				$yy = substr($date, 0,4) + 543;
				$mm = substr($date, 4,2);
				$dd = substr($date, 6,2);
				return $dd."/".$mm."/".$yy;
			}
		}
	}
		
	public function getstandard($data){
		//print_r($data); exit;
		$response = array(
			"stdid" => "",
			"subid" => "",
			"shcid" => "",
			"sdate" => "",
			"ydate" => "",
			"price" => "",
			"price_add" => "",
			"price_spc" => "",
			"interest_rate" => "",
			"downappr" => "",
			"customer" => array(
				"cuscod" 	=> "",
				"cusname" 	=> "",
				"idno" 		=> "",
				"birthdt" 	=> "",
				"expdt" 	=> "",
				"age" 		=> "",
				"addrno" 	=> "",
				"addr" 		=> "",
				"mobile" 	=> "",
				"mrevenu" 	=> "",
			),
			"error" => false,
			"msg" => "",
			"insuranceTypeAMT" => 0
		);
		
		if(isset($data["RESVNO"]) and $data["RESVNO"] != ""){
			return $this->standard_analyze_resv($data,$response);
		}else{
			return $this->standard_analyze($data,$response);
		}
	}
	
	private function standard_analyze($data,$response){
		$sql = "
			select * from {$this->MAuth->getdb('fn_STDVehicles')}(
				'{$data["MODEL"]}'
				,'{$data["BAAB"]}'
				,'{$data["COLOR"]}'
				,'{$data["STAT"]}'
				,'{$data["ACTICOD"]}'
				,'{$data["LOCAT"]}'
				,'{$data["DT"]}'
			);
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		if($query->row()){
			foreach($query->result() as $row){
				$data["stdid"] = $row->STDID;
				$data["subid"] = $row->SUBID;
				
				if($row->STAT == "N"){
					$sql = "
						/* ราคาพิเศษ */
						declare @PRICE_SPECIAL decimal(18,2) = (
							select PRICE from {$this->MAuth->getdb('STDSpecial')} 
							where STRNO='".$data["STRNO"]."'
								and '".$data["DT"]."' between StartDT and isnull(EndDT,'".$data["DT"]."')
						);
						declare @SELLFOR varchar(1) = '".$data["SELLFOR"]."';
							
						select STDID,SUBID,'' as SHCID
							,case when '".$data["ACTICOD"]."' in ('37','38') then PRICE3 else PRICE2 end PRICE
							,0 as PRICE_ADD
							,@PRICE_SPECIAL as PRICE_SPC
							,null as Borrowed
							,NULL as SDATE
							,NULL as YDATE
						from {$this->MAuth->getdb('STDVehiclesPRICE')}
						where STDID='".$row->STDID."' and SUBID='".$row->SUBID."'
					";
				}else if($row->STAT == "O"){
					
					//print_r($data); exit;
					$sql = "
						declare @STRNO varchar(20) = '".$data["STRNO"]."';
						
						/* ราคาพิเศษ */
						declare @PRICE_SPECIAL decimal(18,2) = (
							select PRICE from {$this->MAuth->getdb('STDSpecial')} 
							where STRNO=@STRNO
								and '".$data["DT"]."' between StartDT and isnull(EndDT,'".$data["DT"]."')
						);
						
						/* มีสัญญาเดิมอยู่หรือไม่ */
						declare @CONTNO varchar(12);
						declare @daysy int ;
						declare @sdate datetime;
						declare @ydate datetime;
						
						select @CONTNO=CONTNO 
							,@daysy=datediff(D,SDATE,YDATE)
							,@sdate=SDATE
							,@ydate=YDATE
						from (
							select ROW_NUMBER() over(partition by STRNO order by STRNO,sdate desc) r,* 
							from {$this->MAuth->getdb('ARHOLD')} 
							where STRNO=@STRNO
						) as data
						where r=1
						
						/* ตรวจสอบสัญญาว่าเป็นการขายรถเก่า/ใหม่ หากเป็นรถใหม่ จะมีสแตนกดาร์ดเพิ่มราคารถ */
						declare @STAT varchar(1)  = (case when @CONTNO = '' 
							then 'O' else (select STAT from {$this->MAuth->getdb('HINVTRAN')} where CONTNO=@CONTNO) end);
						
						/*สแตนดาร์ดเพิ่มราคารถ*/
						declare @price_add decimal(18,2) = (
							select price_add from {$this->MAuth->getdb('config_addpricesale')}
							where getdate() between event_st and isnull(event_ed,getdate()) 
								and @daysy between in_sday and in_eday
						);
						
						select '{$row->STDID}' as STDID
							,'{$row->SUBID}' as SUBID
							,a.ID as SHCID
						
							,isnull(@PRICE_SPECIAL,b.OPRICE) as PRICE
							,case when @STAT = 'N'
								then (case when @price_add is null then 0 else @price_add end) 
								else 0 end as PRICE_ADD
							,@PRICE_SPECIAL as PRICE_SPC
							,null as Borrowed
							,convert(varchar(8),@sdate,112) as SDATE
							,convert(varchar(8),@ydate,112) as YDATE
						from {$this->MAuth->getdb('STDSHCAR')} a
						left join {$this->MAuth->getdb('STDSHCARDetails')} b on a.ID=b.ID
						left join {$this->MAuth->getdb('STDSHCARColors')} c on a.ID=c.ID
						left join {$this->MAuth->getdb('STDSHCARLocats')} d on a.ID=d.ID
						where b.ACTIVE='yes' collate thai_ci_as 
							and a.MODEL='".$row->MODEL."' collate thai_cs_as
							and (case when a.BAAB = 'all' then '".$row->BAAB."' else a.BAAB end) = '".$row->BAAB."' collate thai_cs_as 
							and (case when c.COLOR = 'ALL' then '".$row->COLOR."' else c.COLOR end) = '".$row->COLOR."' collate thai_cs_as 
							and (case when d.LOCAT = 'ALL' then '".$row->LOCAT."' else d.LOCAT end) = '".$row->LOCAT."' collate thai_cs_as
							and a.GCODE='".$data["GCODE"]."'
							and a.MANUYR=(select MANUYR from {$this->MAuth->getdb('INVTRAN')} where STRNO=@STRNO )
					";
				}else if($row->STAT == "F"){
					// เช็คข้อมูล ว่าก่อนปิดบัญชีค้างชำระกี่งวด
					$sql = "
						select * from ARMAST
					";
					
					$sql = "
						declare @Clearance int = 5;
						
						select '{$row->STDID}' as STDID
							,'{$row->SUBID}' as SUBID
							,a.FNCID as SHCID
							,'{$data["PRICE"]}' as PRICE --ลูกค้ากู้
							,0 as PRICE_ADD
							,null as PRICE_SPC
							,b.PRICE as Borrowed --วงเงิน
							,NULL as SDATE
							,NULL as YDATE
						from {$this->MAuth->getdb('STDFNCAR')} a
						left join {$this->MAuth->getdb('STDFNCARDetails')} b on a.FNCID=b.FNCID
						left join {$this->MAuth->getdb('STDFNCARColors')} c on a.FNCID=c.FNCID
						left join {$this->MAuth->getdb('STDFNCARLocats')} d on a.FNCID=d.FNCID
						where a.ACTIVE='yes' collate thai_ci_as and b.ACTIVE='yes' collate thai_ci_as 
							and a.MODEL='".$row->MODEL."' collate thai_cs_as
							and (case when a.BAAB = 'all' then '".$row->BAAB."' else a.BAAB end) = '".$row->BAAB."' collate thai_cs_as 
							and (case when c.COLOR = 'ALL' then '".$row->COLOR."' else c.COLOR end) = '".$row->COLOR."' collate thai_cs_as 
							and (case when d.LOCAT = 'ALL' then '".$row->LOCAT."' else d.LOCAT end) = '".$row->LOCAT."' collate thai_cs_as
							and (case when a.GCODE = 'ALL' then '".$data["GCODE"]."' else a.GCODE end) = '".$data["GCODE"]."' collate thai_cs_as
							and @Clearance between b.ClearanceFrom and b.ClearanceTo
					";
				}
				//echo $sql; exit;
				$query = $this->db->query($sql);
				
				if($query->row()){
					foreach($query->result() as $row2){
						if($row->STAT == "F" and $row2->PRICE > $row2->Borrowed){
							$response["error"] = true;
							$response["msg"] = "ผิดพลาด (J0) เกินวงเงินสแตนดาร์ด<br><p class='text-black'>วงเงิน ".number_format($row2->Borrowed,2)." บาท</p>";
							echo json_encode($response); exit;
						}else{
							$data["STDID"] = $row2->STDID;
							$data["SUBID"] = $row2->SUBID;
							$data["SHCID"] = $row2->SHCID;
							$data["PRICE"] = ($row2->PRICE+$row2->PRICE_ADD);
							$data["PRICE_ADD"] = number_format($row2->PRICE_ADD,2);
							
							$response["stdid"] = $row2->STDID;
							$response["subid"] = $row2->SUBID;
							$response["shcid"] = $row2->SHCID;
							
							$response["price"] = $row2->PRICE;
							$response["price_add"] = $row2->PRICE_ADD;
							$response["price_spc"] = $row2->PRICE_SPC;
							
							$response["sdate"] = $this->Convertdate(2,$row2->SDATE);
							$response["ydate"] = $this->Convertdate(2,$row2->YDATE);
						}
					}
				}else{
					$response["error"] = true;
					$response["msg"] = "
						ผิดพลาด ไม่พบราคาในสแตนดาร์ด (J1)<br>โปรดติดต่อฝ่ายเช่าซื้อ/ฝ่ายวิเคราะห์ เพื่อกำหนดราคาขายก่อนครับ<br><br>
						รุ่น :: ".$data["MODEL"]."<br>
						แบบ :: ".$data["BAAB"]."<br>
						สี :: ".$data["COLOR"]."<br>
						สถานะรถ :: ".$data["STAT"]."<br>
						กิจกรรมการขาย :: ".$data["ACTICOD"]."<br>
						วันที่ขออนุมัติ :: ".$this->Convertdate(2,$data["DT"])."
					";
					echo json_encode($response); exit;
				}
			}
		}else{
			$response["error"] = true;
			$response["msg"] = "
				ผิดพลาด ไม่พบราคาขายรถ  (J2)<br>โปรดติดต่อฝ่ายเช่าซื้อ/ฝ่ายวิเคราะห์ เพื่อกำหนดราคาขายก่อนครับ<br><br>
				รุ่น :: ".$data["MODEL"]."<br>
				แบบ :: ".$data["BAAB"]."<br>
				สี :: ".$data["COLOR"]."<br>
				สถานะรถ :: ".$data["STAT"]."<br>
				กิจกรรมการขาย :: ".$data["ACTICOD"]."<br>
				วันที่ขออนุมัติ :: ".$this->Convertdate(2,$data["DT"])."
			";
			echo json_encode($response); exit;
		}
		
		
		if($data["STAT"] == "N"){
			$sql = "
				select INSURANCEPAY from {$this->MAuth->getdb('STDVehiclesDown')} 
				where STDID='{$data["STDID"]}' and SUBID='{$data["SUBID"]}' and ACTIVE='yes'
			";
		}else if($data["STAT"] == "O"){
			$sql = "
				select INSURANCEPAY from {$this->MAuth->getdb('STDVehiclesDown')} 
				where STDID='{$data["STDID"]}' and SUBID='{$data["SUBID"]}' 
					and {$response["price"]} between PRICE2 and PRICE3
					and {$data["dwnAmt"]} between DOWNS and DOWNE
					and ACTIVE='yes'
			";			
		}else if($data["STAT"] == "F"){
			$sql = "
				select INSURANCEPAY from {$this->MAuth->getdb('STDVehiclesDown')} 
				where STDID='".$data["STDID"]."' and SUBID='".$data["SUBID"]."' 
					and '".$data["PRICE"]."' between PRICE2 and isnull(PRICE3,'".$data["PRICE"]."')
					and '".$data["nopay"]."' between DOWNS and isnull(DOWNE,'".$data["dwnAmt"]."')
					and ACTIVE='yes'	
			";			
		}
		//echo $sql; exit;
		$query = $this->connect_db->query($sql);
		
		$html = "";
		if($query->row()){
			foreach($query->result() as $row){
				if($data["insuranceType"] == 1){ //down ป.1
					$response["insuranceTypeAMT"] = 500;
				}else if($data["insuranceType"] == 2){ // จ่ายสด
					$response["insuranceTypeAMT"] = $row->INSURANCEPAY;					
				}else{ // ไม่ทำ ป.1
					$response["insuranceTypeAMT"] = 0;
				}
			}
		}
		
		if($data["STAT"] == "N"){
			$sql = "
				select * from {$this->MAuth->getdb('STDVehiclesDown')} a
				where STDID='".$data["STDID"]."' 
					and SUBID='".$data["SUBID"]."' and '".$data["dwnAmt"]."' between DOWNS and isnull(DOWNE,'".$data["dwnAmt"]."')
					and '".$data["PRICE"]."' between PRICE2 and isnull(PRICE3,'".$data["PRICE"]."')
					and ACTIVE='yes'
			";
		}else if($data["STAT"] == "O"){
			$sql = "
				select * from {$this->MAuth->getdb('STDVehiclesDown')} a
				where STDID='".$data["STDID"]."' and SUBID='".$data["SUBID"]."' 
					and '".$data["PRICE"]."' between PRICE2 and isnull(PRICE3,'".$data["PRICE"]."')
					and '".$data["dwnAmt"]."' between DOWNS and isnull(DOWNE,'".$data["dwnAmt"]."')
					and ACTIVE='yes'
			";
		}else if($data["STAT"] == "F"){
			$sql = "
				select * from {$this->MAuth->getdb('STDVehiclesDown')} a
				where STDID='".$data["STDID"]."' and SUBID='".$data["SUBID"]."' 
					and '".$data["PRICE"]."' between PRICE2 and isnull(PRICE3,'".$data["PRICE"]."')
					and '".$data["nopay"]."' between DOWNS and isnull(DOWNE,'".$data["dwnAmt"]."')
					and ACTIVE='yes'
			";
		}
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		if($query->row()){
			foreach($query->result() as $row){
				$response["downappr"] = $row->APPROVE;
				$response["interest_rate"] 	= ($data["SELLFOR"] == 1 ? $row->INTERESTRT : $row->INTERESTRT_GVM);
				return $response;
			}
		}else{
			$response["error"] = true;
			$response["msg"] = "
				ผิดพลาด (J3) ไม่พบขั้นเงินดาวน์ที่ระบุมา โปรดตรวจสอบข้อมูลใหม่อีกครั้ง<br><br>
				รุ่น :: ".$data["MODEL"]."<br>
				แบบ :: ".$data["BAAB"]."<br>
				สี :: ".$data["COLOR"]."<br>
				สถานะรถ :: ".$data["STAT"]."<br>
				กิจกรรมการขาย :: ".$data["ACTICOD"]."<br>
				วันที่ขออนุมัติ :: ".$this->Convertdate(2,$data["DT"])."
			";
			
			echo json_encode($response); exit;
		}
		
	}
	
	private function standard_analyze_resv($data,$response){
		$sql = "
			select a.RESVNO
				,a.LOCAT
				,convert(varchar(8),a.RESVDT,112) as RESVDT
				,convert(varchar(8),b.SDATE,112) as SDATE
				,convert(varchar(8),b.YDATE,112) as YDATE
				,b.CONTNO
				,datediff(day,b.SDATE,b.YDATE) as DAYSY
				,a.CUSCOD
				,c.SNAM+c.NAME1+' '+c.NAME2+' ('+c.CUSCOD+')-'+c.GRADE as CUSNAME
				,c.IDNO
				,convert(varchar(8),c.BIRTHDT,112) as BIRTHDT
				,convert(varchar(8),c.EXPDT,112) as EXPDT
				,DATEDIFF(YEAR,c.BIRTHDT,GETDATE()) as AGE
				,c.ADDRNO
				,'('+isnull(c.ADDRNO,'')+') '+isnull(d.ADDR1,'')
					+' '+isnull(d.ADDR2,'')+' ต.'+isnull(d.TUMB,'')
					+' อ.'+isnull(e.AUMPDES,'')+' จ.'+isnull(f.PROVDES,'')
					+' '+isnull(d.ZIP,'') as ADDR
				,c.MOBILENO
				,isnull(c.MREVENU,0) as MREVENU
				,g.ACTICOD
				,'('+g.ACTICOD+') '+h.ACTIDES collate thai_cs_as as ACTIDES
				,case when i.PRICE2 is null then a.PRICE else i.PRICE2 end as price
				,isnull(cast(g.STDID as varchar),'') as STDID
				,isnull(cast(g.SUBID as varchar),'') as SUBID
				,isnull(cast(g.SHCID as varchar),'') as SHCID
				,a.RESPAY - (isnull(a.SMPAY,0) + isnull(a.SMCHQ,0)) as BALANCE
			from {$this->MAuth->getdb('ARRESV')} a 
			left join (
				select ROW_NUMBER() over(partition by STRNO order by STRNO,sdate desc) r,* 
				from {$this->MAuth->getdb('ARHOLD')}
			) as b on a.STRNO=b.STRNO and b.r=1
			left join {$this->MAuth->getdb('CUSTMAST')} c on a.CUSCOD=c.CUSCOD
			left join {$this->MAuth->getdb('CUSTADDR')} d on c.CUSCOD=d.CUSCOD and c.ADDRNO=d.ADDRNO
			left join {$this->MAuth->getdb('SETAUMP')} e on d.AUMPCOD=e.AUMPCOD
			left join {$this->MAuth->getdb('SETPROV')} f on e.PROVCOD=f.PROVCOD
			left join {$this->MAuth->getdb('ARRESVOTH')} g on a.RESVNO=g.RESVNO collate thai_cs_as
			left join {$this->MAuth->getdb('SETACTI')} h on g.ACTICOD=h.ACTICOD collate thai_cs_as
			left join {$this->MAuth->getdb('STDVehiclesPRICE')} i on g.STDID=i.STDID and g.SUBID=i.SUBID 
			where a.RESVNO='".$data["RESVNO"]."'
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		if($query->row()){
			foreach($query->result() as $row){
				$data["DT"] 	= $row->RESVDT;
				$data["CONTNO"] = $row->CONTNO;
				$data["daysy"]	= $row->DAYSY;
				
				$response["stdid"] = $row->STDID;
				$response["subid"] = $row->SUBID;
				$response["shcid"] = $row->SHCID;
				$response["sdate"] = $this->Convertdate(2,$row->SDATE);
				$response["ydate"] = $this->Convertdate(2,$row->YDATE);
				
				$response["customer"]["cuscod"] 	= $row->CUSCOD;
				$response["customer"]["cusname"] 	= $row->CUSNAME;
				$response["customer"]["idno"] 		= $row->IDNO;
				$response["customer"]["birthdt"] 	= $this->Convertdate(2,$row->BIRTHDT);
				$response["customer"]["expdt"] 		= $this->Convertdate(2,$row->EXPDT);
				$response["customer"]["age"]		= $row->AGE;
				$response["customer"]["addrno"] 	= $row->ADDRNO;
				$response["customer"]["addr"] 		= $row->ADDR;
				$response["customer"]["mobile"] 	= $row->MOBILENO;
				$response["customer"]["mrevenu"] 	= $row->MREVENU;
			}
		}else{
			$response["error"] = true;
			$response["msg"] = "
				ผิดพลาด ไม่พบขั้นเงินดาวน์ที่ระบุมา โปรดตรวจสอบข้อมูลใหม่อีกครั้ง<br><br>
				รุ่น :: ".$data["MODEL"]."<br>
				แบบ :: ".$data["BAAB"]."<br>
				สี :: ".$data["COLOR"]."<br>
				สถานะรถ :: ".$data["STAT"]."<br>
				กิจกรรมการขาย :: ".$data["ACTICOD"]."<br>
				วันที่ขออนุมัติ :: ".$this->Convertdate(2,$data["DT"])."
			";
			echo json_encode($response); exit;
		}
		
		if(!$response["error"]){
			$sql = "
				select * from {$this->MAuth->getdb('fn_STDVehicles')}(
				 	 '{$data["MODEL"]}'
					,'{$data["BAAB"]}'
					,'{$data["COLOR"]}'
					,'{$data["STAT"]}'
					,'{$data["ACTICOD"]}'
					,'{$data["LOCAT"]}'
					,'{$data["DT"]}'
				);
			";
			//echo $sql; exit;
			$query = $this->db->query($sql);
			
			if($query->row()){
				foreach($query->result() as $row){
					/*
					กรณี สแตนดาร์ด ไม่ตรงกับตอนบันทึกจอง  20200430 ข้ามไปก่อน
					if($response["stdid"] != $row->STDID or $response["subid"] != $row->SUBID){
						$response["error"] = true;
						$response["msg"] = "";
					}
					*/
					
					if($row->STAT == "N"){
						$sql = "
							/* ราคาพิเศษ */
							declare @PRICE_SPECIAL decimal(18,2) = (
								select PRICE from {$this->MAuth->getdb('STDSpecial')} 
								where STRNO='".$data["STRNO"]."' and '".$data["DT"]."' between StartDT and isnull(EndDT,'".$data["DT"]."')
							);
							declare @SELLFOR varchar(1) = '".$data["SELLFOR"]."';
							
							select STDID,SUBID,'' as SHCID
								,case when '".$data["ACTICOD"]."' in ('37','38') then PRICE3 else PRICE2 end PRICE
								,0 as PRICE_ADD 
								,@PRICE_SPECIAL as PRICE_SPC
								
								,NULL as SDATE
								,NULL as YDATE
							from {$this->MAuth->getdb('STDVehiclesPRICE')}
							where STDID='".$row->STDID."' and SUBID='".$row->SUBID."'
						";
					}else if($row->STAT == "O"){
						$sql = "
							/* ราคาพิเศษ */
							declare @PRICE_SPECIAL decimal(18,2) = (
								select PRICE from {$this->MAuth->getdb('STDSpecial')} 
								where STRNO='".$data["STRNO"]."' and '".$data["DT"]."' between StartDT and isnull(EndDT,'".$data["DT"]."')
							);
							/* มีสัญญาเดิมอยู่หรือไม่ */
							-- declare @CONTNO varchar(12) = '".$data["CONTNO"]."';
							declare @CONTNO varchar(12);
							declare @daysy int ;
							declare @sdate datetime;
							declare @ydate datetime;
							
							select @CONTNO=CONTNO 
								,@daysy=datediff(D,SDATE,YDATE)
								,@sdate=SDATE
								,@ydate=YDATE
							from (
								select ROW_NUMBER() over(partition by STRNO order by STRNO,sdate desc) r,* 
								from {$this->MAuth->getdb('ARHOLD')} 
								where STRNO='".$data["STRNO"]."'
							) as data
							where r=1
							
							/* ตรวจสอบสัญญาว่าเป็นการขายรถเก่า/ใหม่ หากเป็นรถใหม่ จะมีสแตนกดาร์ดเพิ่มราคารถ */
							declare @STAT varchar(1)  = (
								case when @CONTNO = '' 
								then 'O' else (
									select top 1 STAT from (
										select STAT from {$this->MAuth->getdb('INVTRAN')} where CONTNO=@CONTNO
										union 
										select STAT from {$this->MAuth->getdb('HINVTRAN')} where CONTNO=@CONTNO
									) as data
								) end
							);
							/*สแตนดาร์ดเพิ่มราคารถ*/
							declare @price_add decimal(18,2) = (
								select price_add from {$this->MAuth->getdb('config_addpricesale')}
								where getdate() between event_st and isnull(event_ed,getdate()) 
									and @daysy between in_sday and in_eday
							);
							
							select '{$row->STDID}' as STDID
								,'{$row->SUBID}' as SUBID
								,a.ID as SHCID
							
								,b.OPRICE as PRICE
								,case when @STAT = 'N'
									then (case when @price_add is null then 0 else @price_add end) 
									else 0 end as PRICE_ADD
								,@PRICE_SPECIAL as PRICE_SPC
								
								,convert(varchar(8),@sdate,112) as SDATE
								,convert(varchar(8),@ydate,112) as YDATE
							from {$this->MAuth->getdb('STDSHCAR')} a
							left join {$this->MAuth->getdb('STDSHCARDetails')} b on a.ID=b.ID
							left join {$this->MAuth->getdb('STDSHCARColors')} c on a.ID=c.ID
							left join {$this->MAuth->getdb('STDSHCARLocats')} d on a.ID=d.ID
							where b.ACTIVE='yes' collate thai_ci_as 
								and a.MODEL='".$row->MODEL."' collate thai_cs_as
								and a.BAAB='".$row->BAAB."' collate thai_cs_as 
								and (case when c.COLOR = 'ALL' then '".$row->COLOR."' else c.COLOR end) = '".$row->COLOR."' collate thai_cs_as 
								and (case when d.LOCAT = 'ALL' then '".$row->LOCAT."' else d.LOCAT end) = '".$row->LOCAT."' collate thai_cs_as
								and a.GCODE='".$data["GCODE"]."'
								and a.MANUYR=(select MANUYR from {$this->MAuth->getdb('INVTRAN')} where STRNO='".$data["STRNO"]."')
						";
					}else if($row->STAT == "F"){
						$response["error"] = true;
						$response["msg"] = "ตั้งไฟแนนท์ แต่ระบุบิลจองมา";
						echo json_encode($response); exit;
					}
					//echo $sql; exit;
					$query2 = $this->db->query($sql);
					if($query2->row()){
						foreach($query2->result() as $row){
							$response["stdid"] = $row->STDID;
							$response["subid"] = $row->SUBID;
							$response["shcid"] = $row->SHCID;
							$response["price"] = $row->PRICE;
							$response["price_add"] = $row->PRICE_ADD;
							$response["price_spc"] = $row->PRICE_SPC;
							
							$response["sdate"] = $this->Convertdate(2,$row->SDATE);
							$response["ydate"] = $this->Convertdate(2,$row->YDATE);
						}
					}else{
						$response["error"] = true;
						$response["msg"] = "
							ผิดพลาด ไม่พบราคาในสแตนดาร์ด (J1)<br>โปรดติดต่อฝ่ายเช่าซื้อ/ฝ่ายวิเคราะห์ เพื่อกำหนดราคาขายก่อนครับ<br><br>
							รุ่น :: ".$data["MODEL"]."<br>
							แบบ :: ".$data["BAAB"]."<br>
							สี :: ".$data["COLOR"]."<br>
							สถานะรถ :: ".$data["STAT"]."<br>
							กิจกรรมการขาย :: ".$data["ACTICOD"]."<br>
							วันที่ขออนุมัติ :: ".$this->Convertdate(2,$data["DT"])."
						";
						echo json_encode($response); exit;
					}
				}
				
				if($data["STAT"] == "N"){
					$sql = "
						select INSURANCEPAY from {$this->MAuth->getdb('STDVehiclesDown')} 
						where STDID='{$response["stdid"]}' and SUBID='{$response["subid"]}' and ACTIVE='yes'
					";
				}else if($data["STAT"] == "O"){
					$sql = "
						select INSURANCEPAY from {$this->MAuth->getdb('STDVehiclesDown')} 
						where STDID='{$response["stdid"]}' and SUBID='{$response["subid"]}' 
							and ".($response["price"] < 0 ?$response["price_spc"]:$response["price"])." between PRICES and PRICEE
							and {$data["dwnAmt"]} between DOWNS and DOWNE
							and ACTIVE='yes'
					";			
				}
				//echo $sql; exit;
				$query = $this->connect_db->query($sql);
				
				$html = "";
				if($query->row()){
					foreach($query->result() as $row){
						if($data["insuranceType"] == 1){ //down ป.1
							$response["insuranceTypeAMT"] = 500;
						}else if($data["insuranceType"] == 2){ // จ่ายสด
							$response["insuranceTypeAMT"] = $row->INSURANCEPAY;					
						}else{ // ไม่ทำ ป.1
							$response["insuranceTypeAMT"] = 0;
						}
					}
				}
				
				if($data["STAT"] == "N"){
					$sql = "
						select * from {$this->MAuth->getdb('STDVehiclesDown')}
						where STDID='{$response["stdid"]}' and SUBID='{$response["subid"]}' 
							and '{$data["dwnAmt"]}' between DOWNS and isnull(DOWNE,{$data["dwnAmt"]})
							--and '".$response["price"]."' between PRICE2 and isnull(PRICE3,'".$response["price"]."')
							and ACTIVE='yes'
					";
				}else if($data["STAT"] == "O"){
					$sql = "
						select * from {$this->MAuth->getdb('STDVehiclesDown')} a
						where STDID='{$response["stdid"]}'
							and SUBID='{$response["subid"]}' 
							and {$data["dwnAmt"]} between DOWNS and isnull(DOWNE,{$data["dwnAmt"]})
							and ".($response["price"] < 0 ?$response["price_spc"]:$response["price"])." 
								between PRICE2 and isnull(PRICE3,".($response["price"] < 0 ?$response["price_spc"]:$response["price"]).")							
							and ACTIVE='yes'
					";
				}
				$query = $this->db->query($sql);
				
				if($query->row()){
					foreach($query->result() as $row){
						$response["interest_rate"] 	= ($data["SELLFOR"] == 1 ? $row->INTERESTRT : $row->INTERESTRT_GVM);
						return $response;
					}
				}else{
					$response["error"] = true;
					$response["msg"] = "
						แจ้งเตือน :: ขั้นเงินดาว์นไม่สอดคล้องกับสแตนดาร์ด<br>
						<table>
							<tr><th class='text-center'>รุ่น</th><th>&emsp;::&emsp;</th><td>".$data["MODEL"]."</td></tr>
							<tr><th class='text-center'>แบบ</th><th>&emsp;::&emsp;</th><td>".$data["BAAB"]."</td></tr>
							<tr><th class='text-center'>สี</th><th>&emsp;::&emsp;</th><td>".$data["COLOR"]."</td></tr>
							<tr><th class='text-center'>สถานะรถ</th><th>&emsp;::&emsp;</th><td>".$data["STAT"]."</td></tr>
							<tr><th class='text-center'>กิจกรรมการขาย</th><th>&emsp;::&emsp;</th><td>".$data["ACTICOD"]."</td></tr>
							<tr><th class='text-center'>วันที่ขออนุมัติ</th><th>&emsp;::&emsp;</th><td>".$this->Convertdate(2,$data["DT"])."</td></tr>
						</table>
					";
					echo json_encode($response); exit;
				}
				
				
			}else{
				$response["error"] = true;
				$response["msg"] = "
					ผิดพลาด :: ไม่พบข้อมูลสแตนดาร์ด<br>
					<table>
						<tr><th class='text-center'>รุ่น</th><th>&emsp;::&emsp;</th><td>".$data["MODEL"]."</td></tr>
						<tr><th class='text-center'>แบบ</th><th>&emsp;::&emsp;</th><td>".$data["BAAB"]."</td></tr>
						<tr><th class='text-center'>สี</th><th>&emsp;::&emsp;</th><td>".$data["COLOR"]."</td></tr>
						<tr><th class='text-center'>สถานะรถ</th><th>&emsp;::&emsp;</th><td>".$data["STAT"]."</td></tr>
						<tr><th class='text-center'>กิจกรรมการขาย</th><th>&emsp;::&emsp;</th><td>".$data["ACTICOD"]."</td></tr>
						<tr><th class='text-center'>วันที่ขออนุมัติ</th><th>&emsp;::&emsp;</th><td>".$this->Convertdate(2,$data["DT"])."</td></tr>
					</table>
				";
				echo json_encode($response); exit;
			}
		}
		
		return $response;
	}
}






















