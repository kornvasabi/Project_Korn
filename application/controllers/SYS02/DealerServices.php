<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/********************************************************
             _____15/08/2563______
            / / _ _   _ _     __ 
           / // __ \ / __ \ / __ \
       _ _/ // /_/ // / / // /_/ /
     /_ _ _/ \_ _ //_/ /_/ \__  /
                          _ _/ /
                         /___ /						 
********************************************************/
class DealerServices extends MY_Controller {
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
			<div class='tab1' name='home' locat='{$this->sess['branch']}' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}'>
				<br>
				<div class='row col-sm-12'>
					<div class='row col-sm-6 col-sm-offset-3'>
						<input type='text' id='STRNO' class='form-control input-sm' placeholder='เลขตัวถัง/เลขเครื่อง' maxlength=30>
						<button id='btnCheck' class='btn btn-warning btn-block'><span class='glyphicon glyphicon-pencil'> ตรวจสอบ</span></button>
					</div>
					<div class='row col-sm-6 col-sm-offset-3'>
						<span style='font-size:7pt;color:red;'>เฉพาะรถยี่ห้อ HONDA ที่ผลิตตั้งแต่ปี 2004-ปัจจุบัน เท่านั้น</span>
					</div>
				</div>
				
				<div id='result' class='row col-sm-12'><div>
			</div>
		";
		
		$html.= "<script src='".base_url('public/js/SYS02/DealerServices.js')."'></script>";
		echo $html;
	}
	
	function getInfo(){
		$now = new DateTime();
		$strno = $_POST["strno"];
		
		$sql = "
			declare @strno varchar(30) = replace('".$strno."',' ','');
			select case when left(right(@strno,2),1) = 'F' and isnumeric(right(@strno,1)) = 1
				then substring(@strno,0,len(@strno)-1)
				else @strno 
				end as STRNO
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		$query = $query->row();
		$strno = $query->STRNO;
		
		$homepage = iconv('TIS-620','UTF-8',$this->getInfoSTRNO($strno));
		$ex = explode("|",$homepage);
		
		$vinno = (isset($ex[17]) ? $ex[17]:'');
		$strno = (isset($ex[2]) ? $ex[2]:'').(isset($ex[3]) ? $ex[3]:'');
		$engno = (isset($ex[0]) ? $ex[0]:'').(isset($ex[1]) ? $ex[1]:'');
		$yr    = (isset($ex[16]) ? $ex[16]:'');
		$yrb   = "";
		$yrex  = array();		
		if($yr != ""){
			$yrex  = explode("/",$yr);
			$yrb   = $yrex[2]."/".($yrex[2]+543);
		}
		
		$model = (isset($ex[13]) ? $ex[13]:'');
		$baab  = (isset($ex[14]) ? $ex[14]:'');
		$color = (isset($ex[15]) ? $ex[15]:'');
	
		$sql = "
			select RECVDT,STRNO,MANUYR,CONTNO,b.strno RGSTRNO,b.yearMade from {$this->MAuth->getdb('INVTRAN')} a 
			left join serviceweb.dbo.wb_regBookLogControl b on a.STRNO=b.strno
			where a.STRNO like '{$vinno}%'
			order by RECVDT
		";
		//echo $sql; exit;
		
		$query = $this->db->query($sql);
		
		$data_db = array();
		
		$data_db["RGSTRNO"] 	= "";
		$data_db["RGMANUYR"] 	= "";
		$data_db["STRNO"] 		= "";
		$data_db["MANUYR"] 		= "";
		$data_db["CONTNO"] 		= "";
		if($query->row()){
			foreach($query->result() as $row){
				$data_db["STRNO"] 	= $row->STRNO;
				$data_db["MANUYR"] 	= $row->MANUYR;
				$data_db["CONTNO"] 	= $row->CONTNO;
				$data_db["RGSTRNO"] = $row->RGSTRNO;
				$data_db["RGMANUYR"] = $row->yearMade;
			}
		}
		
		//print_r($data_db);
		$html = "
			<div class='col-sm-6 col-sm-offset-3' style='background-color:#86c6df;'>
				<div class='col-sm-12'><b><span>ข้อมูลสต๊อครถ-ปีผลิต AP</span></b></div>	
				<div class='col-sm-6'>
					<div class='form-group'>
						เลขตัวถัง 
						<span class='form-control'>".$vinno."</span>
					</div>	
				</div>
				<div class='col-sm-6'>
					<div class='form-group'>
						เลขเครื่อง
						<span class='form-control'>".$engno."</span>
					</div>
				</div>
				<div class='col-sm-6'>
					<div class='form-group'>
						รุ่น
						<span class='form-control'>".$model."</span>
					</div>
				</div>
				<div class='col-sm-6'>
					<div class='form-group'>
						แบบ
						<span class='form-control'>".$baab."</span>
					</div>
				</div>
				<div class='col-sm-12'>
					<div class='form-group'>
						สี
						<span class='form-control'>".$color."</span>
					</div>
				</div>
				<div class='col-sm-6'>
					<div class='form-group'>
						วันที่ผลิต
						<span class='form-control'>".$yr."</span>
					</div>
				</div>
				<div class='col-sm-6'>
					<div class='form-group'>
						ปีผลิต (ค.ศ./พ.ศ) 
						<span class='form-control'>".$yrb."</span>
					</div>
				</div>
			</div>
			
			<div class='col-sm-6 col-sm-offset-3' style='background-color:#9ac37f;'>
				<div class='col-sm-12'><b><span>ข้อมูลสต๊อครถ-ปีผลิต ฝ่ายทะเบียน</span></b></div>	
				<div class='col-sm-6'>
					<div class='form-group'>
						เลขตัวถัง 
						<span class='form-control'>".$data_db["RGSTRNO"]."</span>
					</div>	
				</div>
				<div class='col-sm-6'>
					<div class='form-group'>
						ปีผลิต
						<span class='form-control'>".$data_db["RGMANUYR"]."</span>
					</div>
				</div>
			</div>
			
			<div class='col-sm-6 col-sm-offset-3' style='background-color:#ccc;'>
				<div class='col-sm-12'><b><span>ข้อมูลสต๊อครถ-ปีผลิต ใน senior</span></b></div>	
				<div class='col-sm-4'>
					<div class='form-group'>
						เลขตัวถัง 
						<span class='form-control'>".$data_db["STRNO"]."</span>
					</div>	
				</div>
				<div class='col-sm-4'>
					<div class='form-group'>
						ปีผลิต
						<span class='form-control'>".$data_db["MANUYR"]."</span>
					</div>
				</div>
				<div class='col-sm-4'>
					<div class='form-group'>
						เลขที่สัญญา
						<span class='form-control'>".$data_db["CONTNO"]."</span>
					</div>
				</div>
			</div>
			
			<div class='col-sm-6 col-sm-offset-3' style='background-color:#fff;'>
				<div class='col-sm-12 text-red'><b><span>แก้ไขปีผลิตใน senior</span></b></div>	
				<div class='col-sm-6'>
					<div class='form-group'>
						ปีผลิต
						<input type='text' id='manuyr' class='input input-sm form-control' value='' yr='".(isset($yrex[2]) ? $yrex[2]:"")."'>
					</div>	
				</div>
				<div class='col-sm-6'>
					<div class='form-group'>
						<br>
						<button id='saveManuyr' strno='{$vinno}' class='btn btn-sm btn-primary'> บันทึกปีผลิต</button>
					</div>
				</div>
			</div>
		";
		
		$then = new DateTime();
		$diff = $now->diff($then);
		$alert = "";
		if($diff->format('%h') != 0){ $alert .= $diff->format('%h')." ชั่วโมง"; }
		if($diff->format('%i') != 0){ $alert .= $diff->format('%i')." นาที"; }
		$alert .= $diff->format('%s')." วินาที";
		
		$html .= "
			<div class='col-sm-6 col-sm-offset-3' style='background-color:#ccc;'>
				<span style='font-size:7pt;color:red;'>ระยะเวลาค้นหา  :: ".$alert."</span>
			</div>
		";		
		$this->response["html"] =  $homepage;
		$this->response["html"] =  $html;
		echo json_encode($this->response);
	}
	
	function saveYR(){
		$strno	= $_POST["strno"];
		$manuyr = $_POST["manuyr"];
		
		$sql = "
			if object_id('tempdb..#tempolary') is not null drop table #tempolary;
			create table #tempolary (id varchar(1),msg varchar(max));
			
			begin tran tsc
			begin try			
				if exists(
					select * from {$this->MAuth->getdb('INVTRAN')}
					where STRNO like '{$strno}%' and FLAG='D'
				)
				begin 
					update {$this->MAuth->getdb('INVTRAN')}
					set MANUYR='{$manuyr}'
					where STRNO like '{$strno}%' and FLAG='D'
				end else begin 
					rollback tran tsc;
					insert into #tempolary select 'N' as id,'ผิดพลาด : ไม่พบเลขถังในระบบที่อยู่ในสต๊อค' as msg;
					return;
				end
				
				insert into {$this->MAuth->getdb('hp_UserOperationLog')}(userId,descriptions,postReq,dateTimeTried,ipAddress,functionName)
				values ('".$this->sess["IDNo"]."','แก้ไขปีผลิตรถในสต๊อค','".str_replace("'","",var_export($_REQUEST, true))."',getdate(),'".$_SERVER["REMOTE_ADDR"]."','".(__METHOD__)."');
			
				insert into #tempolary select 'Y' as id,'สำเร็จ บันทึกข้อมูลเรียบร้อยแล้ว' as msg;
				commit tran tsc;
			end try
			begin catch
				rollback tran tsc;
				insert into #tempolary select 'N' as id,'Fail : '+ERROR_MESSAGE() as msg;
			end catch
		";
		$this->db->query($sql);
		$sql = "select * from #tempolary";
		$query = $this->db->query($sql);
		
		$response = array();
		if($query->row()){
			foreach($query->result() as $row){
				$response['stat'] = ($row->id == 'Y' ? true:false);
				$response['msg'] = $row->msg;
			}
		}else{
			$response['stat'] = false;
			$response['msg'] = 'ผิดพลาด';
		}
		
		echo json_encode($response);
	}
}




















