<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MAuth extends CI_Model {
	function getdb($table){
		/************************************************************************************************
			สร้างขึ้นมาเพื่อตรวจสอบว่าใน HIINCOME มี table หรือไม่ 
			ถ้าไม่มีให้ไปดึงจากฐาน YTKManagement เพราะ table ที่สร้างใหม่หากเป็นฐาน HIINCOME จะถูกสร้างที่ YTKManagement แทน
			แต่ฐานอื่น HIC2SHORTL,RJYN,TJHON,TJPAT,TJYL2556,TJYN,TJYN2004 สามารถเพิ่ม table ใหม่เข้าไปได้เลย
		************************************************************************************************/
		$sess = $this->session->userdata('cbjsess001');
		$sql = "
			select case when COUNT(*) > 0 then '{$sess['db']}' else 'YTKManagement' end as db				
			from {$sess['db']}.INFORMATION_SCHEMA.TABLES 
			where TABLE_NAME='".$table."'
		";
		//echo $sql; exit;
		$query = $this->db->query($sql);
		if($query->row()){ foreach($query->result() as $row){ return $row->db.'.dbo.'.$table; } }else{ return 'NODatabase'; }
	}
}
