<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/********************************************************
             ______@06/09/2019______
            / / _ _   _ _     __ 
           / // __ \ / __ \ / __ \
       _ _/ // /_/ // / / // /_/ /
     /_ _ _/ \_ _ //_/ /_/ \__  /
                          _ _/ /
                         /___ /
********************************************************/
class MMAIN extends CI_Model {
	
	//public function SETACTI(){
	public function Option_get_acti($selected){	
		//กิจกรรมการขาย select
		$sql = "
			select ACTICOD,'('+ACTICOD+') '+ACTIDES as ACTIDES from {$this->MAuth->getdb('SETACTI')} 
			order by ACTICOD
		";
		$query = $this->db->query($sql);
		
		$opt = "";
		if($query->row()){
			foreach($query->result() as $row){
				$acticod = str_replace(chr(0),"",$row->ACTICOD);
				$actides = str_replace(chr(0),"",$row->ACTIDES);
				
				$opt .= "
					<option value='{$acticod}' ".(in_array($acticod,$selected) ? "selected":"").">
						{$actides}
					</option>
				";
			}
		}
		
		return $opt;
	}
	
	public function Option_get_type($selected){
		$opt = "";
		if(isset($selected[0])){
			$arrs = array(
				$selected[0] => $selected[0],
				$selected[0] => $selected[0]
			);
			foreach($arrs as $key => $val){
				$opt .= "
					<option value='{$key}' ".(in_array($key,$selected) ? "selected":"").">
						{$val}
					</option>
				";
			}
		}
		
		return $opt;
	}
	
	public function Option_get_model($selected){
		$opt = "";
		if(isset($selected[0])){
			$arrs = array(
				$selected[0] => $selected[0],
				$selected[0] => $selected[0]
			);
			foreach($arrs as $key => $val){
				$opt .= "
					<option value='{$key}' ".(in_array($key,$selected) ? "selected":"").">
						{$val}
					</option>
				";
			}
		}
		
		return $opt;
	}
	
	public function Option_get_modeldesc($selected){
		$opt = "<option value='nouse' selected>เลือก</option>";
		if(isset($selected)){
			$sql = "
				select MDDID,MODELDESC+'::'+MODELTYPE as MODELDESC from {$this->MAuth->getdb('SETMODELDESC')}				
				order by MDDID
			";
			//echo $sql; exit;
			$query = $this->db->query($sql);
			
			if($query->row()){
				foreach($query->result() as $row){
					$MDDID = str_replace(chr(0),"",$row->MDDID);
					$MODELDESC = str_replace(chr(0),"",$row->MODELDESC);
					
					$opt .= "
						<option value='{$MDDID}' ".($MDDID == $selected ? "selected":"").">
							{$MODELDESC}
						</option>
					";
				}
			}
		}
		
		return $opt;
	}
	
	public function Option_get_baab($selected){
		$opt = "";
		if(isset($selected["model"])){
			$sql = "
				select BAABCOD from {$this->MAuth->getdb('SETBAAB')}
				where MODELCOD='{$selected["model"]}'
				order by BAABCOD
			";
			//echo $sql; exit;
			$query = $this->db->query($sql);
			
			if($query->row()){
				foreach($query->result() as $row){
					$baabcod = str_replace(chr(0),"",$row->BAABCOD);				
					
					$opt .= "
						<option value='{$baabcod}' ".(in_array($baabcod,$selected["baab"]) ? "selected":"").">
							{$baabcod}
						</option>
					";
				}
			}
		}
		
		return $opt;
	}
	
	public function Option_get_color($selected,$model,$baab){
		//echo implode("','",$selected); exit;
		if(!is_array($model)){ $model = array('๛',$model); }
		if(!is_array($baab)){ $baab = array('๛',$baab); }
		
		$sql = "
			select COLORCOD from {$this->MAuth->getdb('JD_SETCOLOR')}
			where MODELCOD in ('".implode("','",$model)."') and BAABCOD in ('".implode("','",$baab)."') 
			order by COLORCOD
		";
		$query = $this->db->query($sql);
		
		$opt = "";
		if($query->row()){
			foreach($query->result() as $row){
				$colorcod = str_replace(chr(0),"",$row->COLORCOD);				
				
				$opt .= "
					<option value='{$colorcod}' ".(in_array($colorcod,$selected) ? "selected":"").">
						{$colorcod}
					</option>
				";
			}
		}
		
		return $opt;
	}
	
	public function Option_get_stat($selected){
		$arrs = array(
			"N" => "รถใหม่",
			"O" => "รถเก่า"
		);
		
		$opt = "";
		foreach($arrs as $key => $val){
			$opt .= "
				<option value='{$key}' ".(in_array($key,$selected) ? "selected":"").">
					{$val}
				</option>
			";
		}
		
		return $opt;
	}
	
	public function Option_get_locat($selected){	
		//กิจกรรมการขาย select
		$sql = "
			select LOCATCD,'('+LOCATCD+') '+LOCATNM as LOCATNM from {$this->MAuth->getdb('INVLOCAT')} 
			where LOCATCD<>'TRANS'
			order by LOCATCD
		";
		$query = $this->db->query($sql);
		
		$opt = "";
		if($query->row()){
			foreach($query->result() as $row){
				$locatcd = str_replace(chr(0),"",$row->LOCATCD);
				$locatnm = str_replace(chr(0),"",$row->LOCATNM);
				
				$opt .= "
					<option value='{$locatcd}' title='{$locatnm}' ".($locatcd == $selected ? "selected":"").">
						{$locatnm}
					</option>
				";
			}
		}
		
		return $opt;
	}
	
	public function Option_get_groupcode($selected){
		$opt = "";
		if(isset($selected)){
			$sql = "
				select GCODE,'('+GCODE+') '+GDESC as GDESC from {$this->MAuth->getdb('SETGROUP')} 
				order by GCODE
			";
			//echo $sql; exit;
			$query = $this->db->query($sql);
			
			if($query->row()){
				foreach($query->result() as $row){
					$gcode = str_replace(chr(0),"",$row->GCODE);
					$gdesc = str_replace(chr(0),"",$row->GDESC);
					
					$opt .= "
						<option value='{$gcode}' ".(in_array('G'.$gcode,$selected) ? "selected":"").">
							{$gdesc}
						</option>
					";
				}
			}
		}
		
		return $opt;
	}
	
	public function Option_get_paytyp($selected){
		$opt = "";
		if(isset($selected)){
			$sql = "
				select PAYCODE,'('+PAYCODE+') '+PAYDESC PAYDESC from {$this->MAuth->getdb('PAYTYP')}
				order by PAYCODE
			";
			//echo $sql; exit;
			$query = $this->db->query($sql);
			
			if($query->row()){
				foreach($query->result() as $row){
					$PAYCODE = str_replace(chr(0),"",$row->PAYCODE);
					$PAYDESC = str_replace(chr(0),"",$row->PAYDESC);
					
					$opt .= "
						<option value='{$PAYCODE}' ".($PAYCODE == $selected ? "selected":"").">
							{$PAYDESC}
						</option>
					";
				}
			}
		}
		
		return $opt;
	}
	
	public function Option_get_bkmast($selected){
		$opt = "<option value='nouse' selected>เลือก</option>";
		if(isset($selected)){
			$sql = "
				select BKCODE,'('+BKCODE+') '+BKNAME BKNAME from {$this->MAuth->getdb('BKMAST')}
				order by BKCODE
			";
			//echo $sql; exit;
			$query = $this->db->query($sql);
			
			if($query->row()){
				foreach($query->result() as $row){
					$BKCODE = str_replace(chr(0),"",$row->BKCODE);
					$BKNAME = str_replace(chr(0),"",$row->BKNAME);
					
					$opt .= "
						<option value='{$BKCODE}' ".($BKCODE == $selected ? "selected":"").">
							{$BKNAME}
						</option>
					";
				}
			}
		}
		
		return $opt;
	}
	
	public function Option_get_apmast($selected){
		$opt = "<option value='nouse' selected>เลือก</option>";
		if(isset($selected)){
			$sql = "
				select APCODE,'('+APCODE+') '+APNAME as APNAME from {$this->MAuth->getdb('APMAST')}
				order by APCODE
			";
			//echo $sql; exit;
			$query = $this->db->query($sql);
			
			if($query->row()){
				foreach($query->result() as $row){
					$APCODE = str_replace(chr(0),"",$row->APCODE);
					$APNAME = str_replace(chr(0),"",$row->APNAME);
					
					$opt .= "
						<option value='{$APCODE}' ".($APCODE == $selected ? "selected":"").">
							{$APNAME}
						</option>
					";
				}
			}
		}
		
		return $opt;
	}
	
	public function Option_get_snusers($selected){
		$opt = "<option value='nouse' selected>เลือก</option>";
		if(isset($selected)){
			$sql = "
				select USERID,USERNAME+' ('+USERID+')' as USERNAME from {$this->MAuth->getdb('PASSWRD')}
				order by USERID
			";
			//echo $sql; exit;
			$query = $this->db->query($sql);
			
			if($query->row()){
				foreach($query->result() as $row){
					$USERID = str_replace(chr(0),"",$row->USERID);
					$USERNAME = str_replace(chr(0),"",$row->USERNAME);
					
					$opt .= "
						<option value='{$USERID}' ".($USERID == $selected ? "selected":"").">
							{$USERNAME}
						</option>
					";
				}
			}
		}
		
		return $opt;
	}
	
	public function Option_get_APDoubleBillTP($selected){
		$opt = "<option value='nouse' selected>เลือก</option>";
		//echo $selected; exit;
		if(isset($selected)){
			$sql = "
				select TOPICID,'('+cast(TOPICID as varchar)+') '+TOPICName TOPICName from {$this->MAuth->getdb('JD_APDoubleBillTP')}
				order by TOPICID
			";
			//echo $sql; exit;
			$query = $this->db->query($sql);
			
			if($query->row()){
				foreach($query->result() as $row){
					$TOPICID = str_replace(chr(0),"",$row->TOPICID);
					$TOPICName = str_replace(chr(0),"",$row->TOPICName);
					
					$opt .= "
						<option value='{$TOPICID}' ".($TOPICID == $selected ? "selected":"").">
							{$TOPICName}
						</option>
					";
				}
			}
		}
		
		return $opt;
	}
	
	
	public function getCALDSC($CONTNO){
		$sql = "
			if exists(select * from {$this->MAuth->getdb('ARMAST')} where CONTNO='{$CONTNO}' and CALDSC=1) 
			begin 
				if exists(select T_NOPAY from {$this->MAuth->getdb('ARMAST')} where CONTNO='{$CONTNO}' and T_NOPAY between 1 and 10)
				begin
					select cast(PERD10/100.0 as decimal(18,2)) as PERD from {$this->MAuth->getdb('TABLE1')}
					where NOPAY = (select min(NOPAY) from {$this->MAuth->getdb('ARPAY')} where CONTNO='{$CONTNO}' and PAYMENT != DAMT)
				end
				else if exists(select T_NOPAY from {$this->MAuth->getdb('ARMAST')} where CONTNO='{$CONTNO}' and T_NOPAY between 11 and 12)
				begin
					select cast(PERD12/100.0 as decimal(18,2)) as PERD from {$this->MAuth->getdb('TABLE1')}
					where NOPAY = (select min(NOPAY) from {$this->MAuth->getdb('ARPAY')} where CONTNO='{$CONTNO}' and PAYMENT != DAMT)
				end
				else if exists(select T_NOPAY from {$this->MAuth->getdb('ARMAST')} where CONTNO='{$CONTNO}' and T_NOPAY between 13 and 18)
				begin
					select cast(PERD18/100.0 as decimal(18,2)) as PERD from {$this->MAuth->getdb('TABLE1')}
					where NOPAY = (select min(NOPAY) from {$this->MAuth->getdb('ARPAY')} where CONTNO='{$CONTNO}' and PAYMENT != DAMT)
				end
				else if exists(select T_NOPAY from {$this->MAuth->getdb('ARMAST')} where CONTNO='{$CONTNO}' and T_NOPAY between 19 and 24)
				begin
					select cast(PERD24/100.0 as decimal(18,2)) as PERD from {$this->MAuth->getdb('TABLE1')}
					where NOPAY = (select min(NOPAY) from {$this->MAuth->getdb('ARPAY')} where CONTNO='{$CONTNO}' and PAYMENT != DAMT)
				end
				else if exists(select T_NOPAY from {$this->MAuth->getdb('ARMAST')} where CONTNO='{$CONTNO}' and T_NOPAY between 25 and 30)
				begin
					select cast(PERD30/100.0 as decimal(18,2)) as PERD from {$this->MAuth->getdb('TABLE1')}
					where NOPAY = (select min(NOPAY) from {$this->MAuth->getdb('ARPAY')} where CONTNO='{$CONTNO}' and PAYMENT != DAMT)
				end
				else if exists(select T_NOPAY from {$this->MAuth->getdb('ARMAST')} where CONTNO='{$CONTNO}' and T_NOPAY between 31 and 36)
				begin
					select cast(PERD36/100.0 as decimal(18,2)) as PERD from {$this->MAuth->getdb('TABLE1')}
					where NOPAY = (select min(NOPAY) from {$this->MAuth->getdb('ARPAY')} where CONTNO='{$CONTNO}' and PAYMENT != DAMT)
				end
				else if exists(select T_NOPAY from {$this->MAuth->getdb('ARMAST')} where CONTNO='{$CONTNO}' and T_NOPAY between 37 and 42)
				begin
					select cast(PERD42/100.0 as decimal(18,2)) as PERD from {$this->MAuth->getdb('TABLE1')}
					where NOPAY = (select min(NOPAY) from {$this->MAuth->getdb('ARPAY')} where CONTNO='{$CONTNO}' and PAYMENT != DAMT)
				end
				else if exists(select T_NOPAY from {$this->MAuth->getdb('ARMAST')} where CONTNO='{$CONTNO}' and T_NOPAY between 43 and 48)
				begin
					select cast(PERD48/100.0 as decimal(18,2)) as PERD from {$this->MAuth->getdb('TABLE1')}
					where NOPAY = (select min(NOPAY) from {$this->MAuth->getdb('ARPAY')} where CONTNO='{$CONTNO}' and PAYMENT != DAMT)
				end
				else if exists(select T_NOPAY from {$this->MAuth->getdb('ARMAST')} where CONTNO='{$CONTNO}' and T_NOPAY between 49 and 54)
				begin
					select cast(PERD54/100.0 as decimal(18,2)) as PERD from {$this->MAuth->getdb('TABLE1')}
					where NOPAY = (select min(NOPAY) from {$this->MAuth->getdb('ARPAY')} where CONTNO='{$CONTNO}' and PAYMENT != DAMT)
				end
				else if exists(select T_NOPAY from {$this->MAuth->getdb('ARMAST')} where CONTNO='{$CONTNO}' and T_NOPAY between 55 and 60)
				begin
					select cast(PERD60/100.0 as decimal(18,2)) as PERD from {$this->MAuth->getdb('TABLE1')}
					where NOPAY = (select min(NOPAY) from {$this->MAuth->getdb('ARPAY')} where CONTNO='{$CONTNO}' and PAYMENT != DAMT)
				end
			end 
			else if exists(select * from {$this->MAuth->getdb('ARMAST')} where CONTNO='{$CONTNO}' and CALDSC=2) 
			begin 
				if exists(select T_NOPAY from {$this->MAuth->getdb('ARMAST')} where CONTNO='{$CONTNO}' and T_NOPAY between 1 and 10)
				begin
					select cast(PERD10/100.0 as decimal(18,2)) as PERD from {$this->MAuth->getdb('TABLE2')}
					where NOPAY = (select min(NOPAY) from {$this->MAuth->getdb('ARPAY')} where CONTNO='{$CONTNO}' and PAYMENT != DAMT)
				end
				else if exists(select T_NOPAY from {$this->MAuth->getdb('ARMAST')} where CONTNO='{$CONTNO}' and T_NOPAY between 11 and 12)
				begin
					select cast(PERD12/100.0 as decimal(18,2)) as PERD from {$this->MAuth->getdb('TABLE2')}
					where NOPAY = (select min(NOPAY) from {$this->MAuth->getdb('ARPAY')} where CONTNO='{$CONTNO}' and PAYMENT != DAMT)
				end
				else if exists(select T_NOPAY from {$this->MAuth->getdb('ARMAST')} where CONTNO='{$CONTNO}' and T_NOPAY between 13 and 18)
				begin
					select cast(PERD18/100.0 as decimal(18,2)) as PERD from {$this->MAuth->getdb('TABLE2')}
					where NOPAY = (select min(NOPAY) from {$this->MAuth->getdb('ARPAY')} where CONTNO='{$CONTNO}' and PAYMENT != DAMT)
				end
				else if exists(select T_NOPAY from {$this->MAuth->getdb('ARMAST')} where CONTNO='{$CONTNO}' and T_NOPAY between 19 and 24)
				begin
					select cast(PERD24/100.0 as decimal(18,2)) as PERD from {$this->MAuth->getdb('TABLE2')}
					where NOPAY = (select min(NOPAY) from {$this->MAuth->getdb('ARPAY')} where CONTNO='{$CONTNO}' and PAYMENT != DAMT)
				end
				else if exists(select T_NOPAY from {$this->MAuth->getdb('ARMAST')} where CONTNO='{$CONTNO}' and T_NOPAY between 25 and 30)
				begin
					select cast(PERD30/100.0 as decimal(18,2)) as PERD from {$this->MAuth->getdb('TABLE2')}
					where NOPAY = (select min(NOPAY) from {$this->MAuth->getdb('ARPAY')} where CONTNO='{$CONTNO}' and PAYMENT != DAMT)
				end
				else if exists(select T_NOPAY from {$this->MAuth->getdb('ARMAST')} where CONTNO='{$CONTNO}' and T_NOPAY between 31 and 36)
				begin
					select cast(PERD36/100.0 as decimal(18,2)) as PERD from {$this->MAuth->getdb('TABLE2')}
					where NOPAY = (select min(NOPAY) from {$this->MAuth->getdb('ARPAY')} where CONTNO='{$CONTNO}' and PAYMENT != DAMT)
				end
				else if exists(select T_NOPAY from {$this->MAuth->getdb('ARMAST')} where CONTNO='{$CONTNO}' and T_NOPAY between 37 and 42)
				begin
					select cast(PERD42/100.0 as decimal(18,2)) as PERD from {$this->MAuth->getdb('TABLE2')}
					where NOPAY = (select min(NOPAY) from {$this->MAuth->getdb('ARPAY')} where CONTNO='{$CONTNO}' and PAYMENT != DAMT)
				end
				else if exists(select T_NOPAY from {$this->MAuth->getdb('ARMAST')} where CONTNO='{$CONTNO}' and T_NOPAY between 43 and 48)
				begin
					select cast(PERD48/100.0 as decimal(18,2)) as PERD from {$this->MAuth->getdb('TABLE2')}
					where NOPAY = (select min(NOPAY) from {$this->MAuth->getdb('ARPAY')} where CONTNO='{$CONTNO}' and PAYMENT != DAMT)
				end
				else if exists(select T_NOPAY from {$this->MAuth->getdb('ARMAST')} where CONTNO='{$CONTNO}' and T_NOPAY between 49 and 54)
				begin
					select cast(PERD54/100.0 as decimal(18,2)) as PERD from {$this->MAuth->getdb('TABLE2')}
					where NOPAY = (select min(NOPAY) from {$this->MAuth->getdb('ARPAY')} where CONTNO='{$CONTNO}' and PAYMENT != DAMT)
				end
				else if exists(select T_NOPAY from {$this->MAuth->getdb('ARMAST')} where CONTNO='{$CONTNO}' and T_NOPAY between 55 and 60)
				begin
					select cast(PERD60/100.0 as decimal(18,2)) as PERD from {$this->MAuth->getdb('TABLE2')}
					where NOPAY = (select min(NOPAY) from {$this->MAuth->getdb('ARPAY')} where CONTNO='{$CONTNO}' and PAYMENT != DAMT)
				end
			end 
			else 
			begin 
				select 0 as PERD
			end
		";
		$query = $this->db->query($sql);
		
		if($query->row()){
			foreach($query->result() as $row){
				return str_replace(",","",number_format($row->PERD,2));
			}
		}
	}
	
	public function locat_claim($locat){
		$response = array();
		
		$sql = "
			select * from {$this->MAuth->getdb('INVLOCAT')}
			where LOCATCD='{$locat}'
		";
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$response["error"]  = false;
				$response["FLSALE"] = $row->FLSALE;
				$response["msg"] 	= "";
			}
		}else{
			$response["error"]  = true;
			$response["FLSALE"] = "E";
			$response["msg"]    = "ไม่พบข้อมูลสาขา ".$locat;
		}
		
		return $response;
	}
	
	public function Allow_payment_discount_intamt($IDNo){
		$response = " readonly "; // ไม่อนุญาติ
		
		$sql = "
			select count(*) r from {$this->MAuth->getdb('JALLLOW_KEY_DSCINT')}
			where IDNo='".$IDNo."' and GETDATE() between ALLOWFDT and ISNULL(ALLOWTDT,GETDATE())
		";
		
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				if($row->r > 0){ $response = ""; }
			}
		}
		
		return $response;
	}
	
	public function send_notify_line($token,$data){
		require_once './vendor/autoload.php';
		date_default_timezone_set("Asia/Bangkok");
		
		$headers = array( 'Content-type: application/x-www-form-urlencoded', 'Authorization: Bearer '.$token.'', ); 
		
		$chOne = curl_init(); 		
		curl_setopt( $chOne, CURLOPT_URL, "https://notify-api.line.me/api/notify"); 
		// SSL USE 
		curl_setopt( $chOne, CURLOPT_SSL_VERIFYHOST, 0); 
		curl_setopt( $chOne, CURLOPT_SSL_VERIFYPEER, 0); 
		//POST 
		curl_setopt( $chOne, CURLOPT_POST, 1);
		curl_setopt( $chOne, CURLOPT_POSTFIELDS, http_build_query($data));		
		curl_setopt( $chOne, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt( $chOne, CURLOPT_HTTPHEADER, $headers); 
		curl_setopt( $chOne, CURLOPT_RETURNTRANSFER, 1); 
		
		$result = curl_exec( $chOne ); 
		//Check error 
		$response = array();
		if(curl_error($chOne)) { 
			$response["status"] = false;
			$response["msg"]  = 'error:' . curl_error($chOne); 
		} else { 
			$result_ = json_decode($result, true); 
			$response["status"] = ($result_['status'] == 200 ? true : false);
			$response["msg"] = "status : ".$result_['status']."  ,message : ". $result_['message'];
		}
		curl_close( $chOne );
		
		return $response;
	}
	
}
















