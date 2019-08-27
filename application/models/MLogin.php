<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MLogin extends CI_Model {
	
	public function vertifylogin($username,$dblocat){
		$sql = "			
			select a.*,b.USERID,c.LOCATCD,c.dblocat 
				,(select lower(allow) from itsupport.dbo.md5_topsecret where db='".$dblocat."') as allow
			from YTKManagement.dbo.hp_vusers a
			left join YTKManagement.dbo.hp_mapusers b on a.employeeCode=b.employeeCode and a.IDNo=b.IDNo
			left join YTKManagement.dbo.hp_maplocat c on b.USERID=c.USERID and b.dblocat=c.dblocat
			where (a.employeeCode = '".$username."' or a.IDNO='".$username."') and isnull(c.action,'T')='T'
				and a.em_status in ('W','P','RR') and b.dblocat='".$dblocat."'
		";
		//echo $sql; exit;
		
		return $this->db->query($sql);
	}
	
	public function getmenuclaim_dev(){
		$html = "
			<li>
				<a href='#welcome/dashboard'>
					<i class='fa fa-home menu-item-icon'></i>
					<span class='inner-text'>Dashboard</span>
				</a>
			</li>
			
			<li class='opened'>
				<a href='#'>
					<i class='fa fa-folder-o' aria-hidden='true'></i>
					<span class='inner-text'>MENU</span>
					<i class='menu-item-toggle-icon fa fa-chevron-circle-down'></i>
					<ul style='display:block;'>
						<li>
							<a href='#CHomenew/TypeCar'>
								<i class='fa fa-tags' aria-hidden='true'></i>
								<span class='inner-text'>ฟอร์มเปลี่ยนกลุ่มรถ</span>
							</a>
						</li>	
					</ul>
				</a>
			</li>
		";
		
		return $html;
	}
	
	public function getmenuclaim(){
		$sess = $this->session->userdata('cbjsess001');
		//print_r($sess); exit;
		$sql = "
			select e.* from {$sess["db"]}.dbo.PASSWRD a
			left join YTKManagement.dbo.hp_mapusers b on a.USERID=b.USERID collate Thai_CS_AS
			left join YTKManagement.dbo.hp_vusers c on b.employeeCode=c.employeeCode and b.IDNo=c.IDNo
			left join YTKManagement.dbo.hp_groupuser_detail d on b.groupCode=d.groupCode collate Thai_CS_AS and b.dblocat=d.dblocat collate Thai_CS_AS
			left join YTKManagement.dbo.hp_menu e on d.menuid=e.menuid 
			where b.IDNo is not null and b.USERID='".$sess["USERID"]."' and d.m_access='T' and e.menustatus='y'
				and b.dblocat='".$sess["db"]."'
			order by e.menurank asc
        ";
		//echo $sql; exit;
        $query = $this->db->query($sql);
		
		$html = "
			<li>
				<a href='#welcome/dashboard'>
					<i class='fa fa-home menu-item-icon'></i>
					<span class='inner-text' title='Dashboard'>Dashboard</span>
				</a>
			</li>
		";
		
		$lastmenu = 1;
		if($query->row()){
			foreach($query->result() as $row){
				if($row->menulevel == 1 and $row->menulevel > $lastmenu){
					if($row->menuhead == 'y'){
						$html .= "
							<li>
								<a href='#'>
								<i class='".$row->menuicon."' aria-hidden='true'></i>
								<span class='inner-text notifyMenu' menuname='".$row->menuname."' mid='".$row->menuid."'>".$row->menuname."</span>
							
								<ul>
						";
						
					}else{
						$html .= "
							<li class='notifyMenu' menuname='".$row->menuname."'>
								<a href='#".$row->menulink."'>
									<i class='".$row->menuicon." menu-item-icon'></i>
									<span class='inner-text' mid='".$row->menuid."'>".$row->menuname."</span>
								</a>
							</li>
						";
					}
				}else if($row->menulevel == 1 and $row->menulevel == $lastmenu){
					if($row->menuhead == 'y'){
						$html .= "
							<li>
								<a href='#'>
								<i class='".$row->menuicon."' aria-hidden='true'></i>
								<span class='inner-text notifyMenu' menuname='".$row->menuname."' mid='".$row->menuid."'>".$row->menuname."</span>
							
								<ul>
						";
						
					}else{
						$html .= "
							<li class='notifyMenu' menuname='".$row->menuname."'>
								<a href='#".$row->menulink."'>
									<i class='".$row->menuicon." menu-item-icon'></i>
									<span class='inner-text' mid='".$row->menuid."'>".$row->menuname."</span>
								</a>
							</li>
						";
					}
				}else if($row->menulevel == 1 and $row->menulevel < $lastmenu){
					if($row->menuhead == 'y'){
						$rage = $lastmenu - $row->menulevel;
						for($i=1;$i <= $rage; $i++){ $html .= "</ul></li>"; }
						$html .= "								
							<li>
								<a href='#'>
								<i class='".$row->menuicon."' aria-hidden='true'></i>
								<span class='inner-text notifyMenu' menuname='".$row->menuname."' mid='".$row->menuid."'>".$row->menuname."</span>
							
								<ul>
						";
						
					}else{
						$rage = $lastmenu - $row->menulevel;
						for($i=1;$i <= $rage; $i++){ $html .= "</ul></li>"; }
						
						$html .= "
							<li class='notifyMenu' menuname='".$row->menuname."'>
								<a href='#".$row->menulink."'>
									<i class='".$row->menuicon." menu-item-icon'></i>
									<span class='inner-text' mid='".$row->menuid."'>".$row->menuname."</span>
								</a>
							</li>
						";
					}
				}else if($row->menulevel == 2 and $row->menulevel > $lastmenu){
					if($row->menuhead == 'y'){
						$html .= "
							<li>
								<a href='#'>
								<i class='".$row->menuicon."' aria-hidden='true'></i>
								<span class='inner-text notifyMenu' menuname='".$row->menuname."' mid='".$row->menuid."'>".$row->menuname."</span>
							
								<ul>
						";
						
					}else{
						$html .= "
							<li class='notifyMenu' menuname='".$row->menuname."'>
								<a href='#".$row->menulink."'>
									<i class='".$row->menuicon." menu-item-icon'></i>
									<span class='inner-text' mid='".$row->menuid."'>".$row->menuname."</span>
								</a>
							</li>
						";
					}
				}else if($row->menulevel == 2 and $row->menulevel == $lastmenu){
					if($row->menuhead == 'y'){
						$html .= "
							<li>
								<a href='#'>
								<i class='".$row->menuicon."' aria-hidden='true'></i>
								<span class='inner-text notifyMenu' menuname='".$row->menuname."' mid='".$row->menuid."'>".$row->menuname."</span>
							
								<ul>
						";
						
					}else{
						$html .= "
							<li class='notifyMenu' menuname='".$row->menuname."'>
								<a href='#".$row->menulink."'>
									<i class='".$row->menuicon." menu-item-icon'></i>
									<span class='inner-text' mid='".$row->menuid."'>".$row->menuname."</span>
								</a>
							</li>
						";
					}
				}else if($row->menulevel == 2 and $row->menulevel < $lastmenu){
					if($row->menuhead == 'y'){
						$rage = $lastmenu - $row->menulevel;
						for($i=1;$i <= $rage; $i++){ $html .= "</ul></li>"; }
						$html .= "
							<li>
								<a href='#'>
								<i class='".$row->menuicon."' aria-hidden='true'></i>
								<span class='inner-text notifyMenu' menuname='".$row->menuname."' mid='".$row->menuid."'>".$row->menuname."</span>
							
								<ul>
						";
						
					}else{
						$rage = $lastmenu - $row->menulevel;
						for($i=1;$i <= $rage; $i++){ $html .= "</ul></li>"; }
						
						$html .= "
							<li class='notifyMenu' menuname='".$row->menuname."'>
								<a href='#".$row->menulink."'>
									<i class='".$row->menuicon." menu-item-icon'></i>
									<span class='inner-text' mid='".$row->menuid."'>".$row->menuname."</span>
								</a>
							</li>
						";
					}
				}else if($row->menulevel == 3 and $row->menulevel > $lastmenu){
					if($row->menuhead == 'y'){
						$html .= "
							<li>
								<a href='#'>
								<i class='".$row->menuicon."' aria-hidden='true'></i>
								<span class='inner-text notifyMenu' menuname='".$row->menuname."' mid='".$row->menuid."'>".$row->menuname."</span>
							
								<ul>
						";
						
					}else{
						$html .= "
							<li class='notifyMenu' menuname='".$row->menuname."'>
								<a href='#".$row->menulink."'>
									<i class='".$row->menuicon." menu-item-icon'></i>
									<span class='inner-text' mid='".$row->menuid."'>".$row->menuname."</span>
								</a>
							</li>
						";
					}
				}else if($row->menulevel == 3 and $row->menulevel == $lastmenu){
					if($row->menuhead == 'y'){
						$html .= "
							<li>
								<a href='#'>
								<i class='".$row->menuicon."' aria-hidden='true'></i>
								<span class='inner-text notifyMenu' menuname='".$row->menuname."' mid='".$row->menuid."'>".$row->menuname."</span>
							
								<ul>
						";
						
					}else{
						$html .= "
							<li class='notifyMenu' menuname='".$row->menuname."'>
								<a href='#".$row->menulink."'>
									<i class='".$row->menuicon." menu-item-icon'></i>
									<span class='inner-text' mid='".$row->menuid."'>".$row->menuname."</span>
								</a>
							</li>
						";
					}
				}else if($row->menulevel == 3 and $row->menulevel < $lastmenu){
					if($row->menuhead == 'y'){
						$rage = $lastmenu - $row->menulevel;
						for($i=1;$i <= $rage; $i++){ $html .= "</ul></li>"; }
						$html .= "
							<li>
								<a href='#'>
								<i class='".$row->menuicon."' aria-hidden='true'></i>
								<span class='inner-text notifyMenu' menuname='".$row->menuname."' mid='".$row->menuid."'>".$row->menuname."</span>
							
								<ul>
						";
						
					}else{
						$rage = $lastmenu - $row->menulevel;
						for($i=1;$i <= $rage; $i++){ $html .= "</ul></li>"; }
						
						$html .= "
							<li class='notifyMenu' menuname='".$row->menuname."'>
								<a href='#".$row->menulink."'>
									<i class='".$row->menuicon." menu-item-icon'></i>
									<span class='inner-text' mid='".$row->menuid."'>".$row->menuname."</span>
								</a>
							</li>
						";
					}
				}else if($row->menulevel == 4 and $row->menulevel > $lastmenu){
					if($row->menuhead == 'y'){
						$html .= "
							<li>
								<a href='#'>
								<i class='".$row->menuicon."' aria-hidden='true'></i>
								<span class='inner-text notifyMenu' menuname='".$row->menuname."' mid='".$row->menuid."'>".$row->menuname."</span>
							
								<ul>
						";
						
					}else{
						$html .= "
							<li class='notifyMenu' menuname='".$row->menuname."'>
								<a href='#".$row->menulink."'>
									<i class='".$row->menuicon." menu-item-icon'></i>
									<span class='inner-text' mid='".$row->menuid."'>".$row->menuname."</span>
								</a>
							</li>
						";
					}
				}else if($row->menulevel == 4 and $row->menulevel == $lastmenu){
					if($row->menuhead == 'y'){
						$html .= "
							<li>
								<a href='#'>
								<i class='".$row->menuicon."' aria-hidden='true'></i>
								<span class='inner-text notifyMenu' menuname='".$row->menuname."' mid='".$row->menuid."'>".$row->menuname."</span>
							
								<ul>
						";
						
					}else{
						$html .= "
							<li class='notifyMenu' menuname='".$row->menuname."'>
								<a href='#".$row->menulink."'>
									<i class='".$row->menuicon." menu-item-icon'></i>
									<span class='inner-text' mid='".$row->menuid."'>".$row->menuname."</span>
								</a>
							</li>
						";
					}
				}else if($row->menulevel == 4 and $row->menulevel < $lastmenu){
					if($row->menuhead == 'y'){
						$rage = $lastmenu - $row->menulevel;
						for($i=1;$i <= $rage; $i++){ $html .= "</ul></li>"; }
						$html .= "								
							<li>
								<a href='#'>
								<i class='".$row->menuicon."' aria-hidden='true'></i>
								<span class='inner-text notifyMenu' menuname='".$row->menuname."' mid='".$row->menuid."'>".$row->menuname."</span>
							
								<ul>
						";
						
					}else{
						$rage = $lastmenu - $row->menulevel;
						for($i=1;$i <= $rage; $i++){ $html .= "</ul></li>"; }
						
						$html .= "
							<li class='notifyMenu' menuname='".$row->menuname."'>
								<a href='#".$row->menulink."'>
									<i class='".$row->menuicon." menu-item-icon'></i>
									<span class='inner-text' mid='".$row->menuid."'>".$row->menuname."</span>
								</a>
							</li>
						";
					}
				}else if($row->menulevel == 5 and $row->menulevel > $lastmenu){
					if($row->menuhead == 'y'){
						$html .= "
							<li>
								<a href='#'>
								<i class='".$row->menuicon."' aria-hidden='true'></i>
								<span class='inner-text notifyMenu' menuname='".$row->menuname."' mid='".$row->menuid."'>".$row->menuname."</span>
							
								<ul>
						";
						
					}else{
						$html .= "
							<li class='notifyMenu' menuname='".$row->menuname."'>
								<a href='#".$row->menulink."'>
									<i class='".$row->menuicon." menu-item-icon'></i>
									<span class='inner-text' mid='".$row->menuid."'>".$row->menuname."</span>
								</a>
							</li>
						";
					}
				}else if($row->menulevel == 5 and $row->menulevel == $lastmenu){
					if($row->menuhead == 'y'){
						$html .= "
							<li>
								<a href='#'>
								<i class='".$row->menuicon."' aria-hidden='true'></i>
								<span class='inner-text notifyMenu' menuname='".$row->menuname."' mid='".$row->menuid."'>".$row->menuname."</span>
							
								<ul>
						";
						
					}else{
						$html .= "
							<li class='notifyMenu' menuname='".$row->menuname."'>
								<a href='#".$row->menulink."'>
									<i class='".$row->menuicon." menu-item-icon'></i>
									<span class='inner-text' mid='".$row->menuid."'>".$row->menuname."</span>
								</a>
							</li>
						";
					}
				}else if($row->menulevel == 5 and $row->menulevel < $lastmenu){
					if($row->menuhead == 'y'){
						$rage = $lastmenu - $row->menulevel;
						for($i=1;$i <= $rage; $i++){ $html .= "</ul></li>"; }
						$html .= "								
							<li>
								<a href='#'>
								<i class='".$row->menuicon."' aria-hidden='true'></i>
								<span class='inner-text notifyMenu' menuname='".$row->menuname."' mid='".$row->menuid."'>".$row->menuname."</span>
							
								<ul>
						";
						
					}else{
						$rage = $lastmenu - $row->menulevel;
						for($i=1;$i <= $rage; $i++){ $html .= "</ul></li>"; }
						
						$html .= "
							<li class='notifyMenu' menuname='".$row->menuname."'>
								<a href='#".$row->menulink."'>
									<i class='".$row->menuicon." menu-item-icon'></i>
									<span class='inner-text' mid='".$row->menuid."'>".$row->menuname."</span>
								</a>
							</li>
						";
					}
				}else if($row->menulevel == 6 and $row->menulevel > $lastmenu){
					if($row->menuhead == 'y'){
						$html .= "
							<li>
								<a href='#'>
								<i class='".$row->menuicon."' aria-hidden='true'></i>
								<span class='inner-text notifyMenu' menuname='".$row->menuname."' mid='".$row->menuid."'>".$row->menuname."</span>
							
								<ul>
						";
						
					}else{
						$html .= "
							<li class='notifyMenu' menuname='".$row->menuname."'>
								<a href='#".$row->menulink."'>
									<i class='".$row->menuicon." menu-item-icon'></i>
									<span class='inner-text' mid='".$row->menuid."'>".$row->menuname."</span>
								</a>
							</li>
						";
					}
				}else if($row->menulevel == 6 and $row->menulevel == $lastmenu){
					if($row->menuhead == 'y'){
						$html .= "
							<li>
								<a href='#'>
								<i class='".$row->menuicon."' aria-hidden='true'></i>
								<span class='inner-text notifyMenu' menuname='".$row->menuname."' mid='".$row->menuid."'>".$row->menuname."</span>
							
								<ul>
						";
						
					}else{
						$html .= "
							<li class='notifyMenu' menuname='".$row->menuname."'>
								<a href='#".$row->menulink."'>
									<i class='".$row->menuicon." menu-item-icon'></i>
									<span class='inner-text' mid='".$row->menuid."'>".$row->menuname."</span>
								</a>
							</li>
						";
					}
				}else if($row->menulevel == 6 and $row->menulevel < $lastmenu){
					if($row->menuhead == 'y'){
						$rage = $lastmenu - $row->menulevel;
						for($i=1;$i <= $rage; $i++){ $html .= "</ul></li>"; }
						$html .= "								
							<li>
								<a href='#'>
								<i class='".$row->menuicon."' aria-hidden='true'></i>
								<span class='inner-text notifyMenu' menuname='".$row->menuname."' mid='".$row->menuid."'>".$row->menuname."</span>
							
								<ul>
						";
					}else{
						$rage = $lastmenu - $row->menulevel;
						for($i=1;$i <= $rage; $i++){ $html .= "</ul></li>"; }
						
						$html .= "
							<li class='notifyMenu' menuname='".$row->menuname."'>
								<a href='#".$row->menulink."'>
									<i class='".$row->menuicon." menu-item-icon'></i>
									<span class='inner-text' mid='".$row->menuid."'>".$row->menuname."</span>
								</a>
							</li>
						";
					}
				}
				
				$lastmenu = $row->menulevel;
			}
			//echo $html; exit;
			return $html;
		}
	}
	
	function getclaim($mid){
		$sess = $this->session->userdata('cbjsess001');
		
		$sql = "
			select c.*,a.LEVEL_1 as level from {$sess["db"]}.dbo.PASSWRD a
			left join YTKManagement.dbo.hp_mapusers b on a.USERID=b.USERID collate Thai_CI_AS and b.dblocat='".$sess["db"]."'
			left join YTKManagement.dbo.hp_groupuser_detail c on b.groupCode=c.groupCode collate Thai_CI_AS and c.dblocat='".$sess["db"]."'
			left join YTKManagement.dbo.hp_menu d on c.menuid=d.menuid
			where a.USERID='".$sess["USERID"]."' and d.menulink = '".$mid."'
        ";
		//echo $sql; exit;
        $query = $this->db->query($sql);
		$data = array();
        if($query->row()){
			foreach($query->result() as $row){
				$data["groupCode"] = $row->groupCode;
				$data["menuid"] = $row->menuid;	
				$data["m_access"] = $row->m_access;	
				$data["m_insert"] = $row->m_insert;	
				$data["m_update"] = $row->m_update;	
				$data["m_delete"] = $row->m_delete;	
				$data["level"] = $row->level;	
			}            
        }else{
            $data = array("groupCode"=>"","menuid"=>"","m_access"=>"F","m_insert"=>"F","m_update"=>"F","m_delete"=>"F","level"=>"");
        }
		
		return $data;		
	}
}
