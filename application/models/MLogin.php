<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MLogin extends CI_Model {
	
	public function vertifylogin($username,$password){
		$sql = "
			select * from HIC2SHORTL.dbo.hp_vusers 
			where (employeeCode='".$username."' or IDNO='".$username."')
				and (positionName like '%ผู้จัดการ%' or positionName like '%โปรแกรมเมอร์%') 
				and corpName <> 'ทีแอลแอล โลจิสติกส์ บจก.'
				and em_status in ('W','P','RR')
			
		";
		//echo $sql; exit;
		return $this->db->query($sql);
	}
	
	public function getmenuclaim(){
		
	}
}
