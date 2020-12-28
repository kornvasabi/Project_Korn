<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/********************************************************
             ______@26/10/2020______
            pasakorn boonded
********************************************************/
class GrageCont extends MY_Controller {
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
		//echo "เมนูยังไม่เสร็จ กำลังพัฒนาต่อครับ"; exit;
		$html = "
			<div class='tab1' name='home' locat='{$this->sess['branch']}' cin='{$claim['m_insert']}' cup='{$claim['m_update']}' cdel='{$claim['m_delete']}' clev='{$claim['level']}' style='height:calc(100vh - 132px);overflow:auto;background-color:white;'>
				<div class='col-sm-12' style='overflow:auto;'>					
					<div class='row'>
						<div class=' col-sm-6 col-sm-offset-3'>	
							<div class='col-sm-12'>
								<div class='form-group'>
									<font style='font-size:12pt;'>คำนวณเกรดสัญญาเช่าซื้อ ณ วันที่  ".$this->today('today')."</font>
								</div>	
							</div>
							<div class='col-sm-12'>
								<div class='form-group'>
									รหัสสาขา
									<select id='add_locat' class='form-control input-sm'>
									</select>
								</div>	
							</div>
							<div class='col-sm-12'>
								<div class='form-group'>
									เลขที่สัญญา
									<input type='text' id='add_contno' class='form-control input-sm' placeholder='วันที่ทำสัญญา'>
								</div>	
							</div>
							<div class='col-sm-12'>
								<div class='form-group'>
									<br>
									<input class='form-check-input' style='cursor:pointer;max-width:20px;max-height:10px;' type='checkbox' id='debtor' value='*' checked>
									<label class='form-check-label' style='cursor:pointer;' for='debtor'> เฉพาะลูกหนี้คงเหลือ</label>
								</div>	
							</div>
							<div class='col-sm-12'>
								<div class='form-group'>
									<br>
									<input type='button' id='btn_calc' class='btn btn-sm btn-primary right' style='width:100%;' value='คำนวณ' >
								</div>	
							</div>
						</div>
					</div>
				</div>
			</div>
		";
		$html .= "<script src='".base_url('public/js/SYS06/GrageCont.js')."'></script>";
		echo $html;
	}
	function Calculator(){
		$locat   = $_REQUEST["locat"];
		$contno  = $_REQUEST["contno"];
		$debtor  = $_REQUEST["debtor"];
		
		$cond = ""; $cond2 = "";
		if($debtor == "Y"){
			$cond .=" and CONVERT(varchar(8),DDATE,112) < convert(varchar(8),getdate(),112)";
			$cond2 .=" and TOTPRC > SMPAY";
		}
		
		$sql = "
			select * 
				,(
					case when MONTHD <= 0 then '09'
						 when MONTHD <= 1 then '19'
						 when MONTHD <= 2 then '29'
						 when MONTHD <= 3 then '39'
						 when MONTHD <= 4 then '49'
						 when MONTHD <= 5 then '59'
						 when MONTHD <= 6 then '69'
						 when MONTHD <= 24 then '79'
						 when MONTHD >  24 then '89'
					end
				) as GRDCODU
			from (
				select CONTNO,NOPAY,GRDCOD
					,DATEDIFF(MONTH,DDATE,ISNULL(DATE1,getdate())) as MONTHD
				from {$this->MAuth->getdb('ARPAY')} where CONTNO = '".$contno."'".$cond."
			)a
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		$calc = "";
		if($query->row()){
			foreach($query->result() as $row){
				$calc .="
					update {$this->MAuth->getdb('ARPAY')}
						set GRDCOD = '".$row->GRDCODU."' 
					where CONTNO = '".$row->CONTNO."' and NOPAY = '".$row->NOPAY."'
				";
			}
		}
		
		$sql = "
			if OBJECT_ID('tempdb..#tempsave') is not null drop table #tempsave;
			create table #tempsave (id varchar(1),msg varchar(max));
			begin tran savecloseacc
			begin try
				begin
					declare @armast int = (
						select count(*) from {$this->MAuth->getdb('ARMAST')}
						where CONTNO = '".$contno."' and LOCAT = '".$locat."'
						".$cond2."
					)
					if(@armast = 1)
						begin
							".$calc."
							update {$this->MAuth->getdb('ARMAST')} 
								set GRDCOD = (
									select MAX(GRDCOD) 
									from {$this->MAuth->getdb('ARPAY')} 
									where CONTNO = '".$contno."' and LOCAT = '".$locat."'
								)
							where CONTNO = '".$contno."' and LOCAT = '".$locat."'
							
							insert into #tempsave select 'Y' as id,'สำเร็จ บันทึกข้อมูลเรียบร้อยแล้ว' as msg;
							commit tran savecloseacc;	
						end
					else
						begin 
							rollback tran savecloseacc;
							insert into #tempsave select 'N' as id,'สัญญานี้ ไม่มีลูกหนี้คงเเหลือครับ' as msg;
							return;
						end	
				end
			end try
			begin catch
				rollback tran savecloseacc;
				insert into #tempsave select 'E' as id,'บันทึกข้อมูลไม่สำเร็จ : กรุณาติดต่อฝ่ายไอที' as msg;
				return;
			end catch
		";
		//echo $sql; exit;
		$this->db->query($sql);
		
		$sql = "select * from #tempsave";
		$query = $this->db->query($sql);
		if($query->row()){
			foreach($query->result() as $row){
				$response["error"] = $row->id;
				$response["msg"]   = $row->msg;
			}
		}else{
			$response["error"] = "error";
			$response["msg"]   = "ผิดพลาด กรุณาติดต่อฝ่ายไอที";
		}
		echo json_encode($response); 
	}
}




















