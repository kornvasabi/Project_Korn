<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/********************************************************
             _______________________
            / / _ _   _ _     __ 
           / // __ \ / __ \ / __ \
       _ _/ // /_/ // / / // /_/ /
     /_ _ _/ \_ _ //_/ /_/ \__  /
                          _ _/ /
                         /___ /
********************************************************/
class Welcome extends MY_Controller {
	private $sess = array();
	
	function __construct()
	{
		parent::__construct();
		/*Additional code which you want to run automatically in every function call */
		$sess = $this->session->userdata('cbjsess001');
		if(!$sess){ 
			//$data["user"] = $this->username;
			//$this->load->view('lobiLock',$data); 
			$html = "
				<span id='sessTimeout'>
					คุณทิ้งระบบไว้นานเกินไป โปรดเข้าสู่ระบบใหม่อีกครั้ง <br><a href='".base_url("/clogin/index")."'>เข้าสู่ระบบ</a>
				</span>	
				
				<style>
					#sessTimeout {
						position:fixed;
						top: 40%;
						left: 40%;
						font-size: 12pt;
						text-align: center;
						color: red;
					}
					
					#sessTimeout a {
						font-size: 15pt;
					}
				</style>
			";
			echo $html; exit;
		}else{
			foreach ($sess as $key => $value) {
				if($key == "lock" and $value == "yes"){
					redirect(base_url("clogout/lock"),"_parent");
				}
				
                $this->sess[$key] = $value;
            }
		}
	}
	
	public function index()
	{
		$this->load->model('MLogin');
		
		$data = array();
		$data["menu"] = $this->MLogin->getmenuclaim();
		$data["branch"] = $this->sess["branch"];
		$data["name"] = $this->sess["employeeCode"].'<br>'.$this->sess["name"];
		$data["db"] = $this->sess["db"];
		$data["baseUrl"] = base_url();
		//echo base_url(); exit;
		
		$this->load->view('lobiView',$data);
	}	
	
	function getmenu(){
		$mid = $_REQUEST['mid'];
		return json_encode($this->MLogin->getclaim($mid));
	}
	
	function pages(){
		
		$html = '
			<!--Author      : @arboshiki-->
			<div class="error-page error-404">
				<h1 class="error-page-code animated pulse"><i class="fa fa-warning"></i> Error 404</h1>
				<h1 class="error-page-text">Page Not Found</h1>
				<p class="error-page-subtext">The page you requested was not found. We are hardly working to fix this issue.</p>
				<p class="error-page-subtext">Try again in several minutes or <a href="javascript:void(0)">contact the administrator</a>.</p>
				<ul class="error-page-actions">
					<li>
						<a href="javascript:void(0)" data-func="go-back" class="btn btn-primary btn-outline">
							<i class="fa fa-arrow-left"></i>
							Go Back!</a>
					</li>
					<li>
						<a href="#dashboard" class="btn btn-primary btn-outline">
							<i class="fa fa-home"></i>
							Dashboard</a>
					</li>
					<li>
						<a href="javascript:void(0)" class="btn btn-primary btn-outline">
							<i class="fa fa-envelope-o"></i>
							Contact
						</a>
					</li>
					<li>
						<a href="javascript:void(0)" class="btn btn-primary btn-outline">
							<i class="fa fa-bug"></i>
							Report
						</a>
					</li>
				</ul>
			</div>			
		';
		
		$html.= "
			<script>
				$('.error-page [data-func=\"go-back\"]').click(function(ev){
					window.history.back();
				});
			</script>
		";
		
		echo $html;
	}
	
	function dashboard(){
		$html = "
			<div class='col-md-12' style='height:calc(100vh - 200px);background-image: url(\"../public/images/KOI FISHx.gif\"); background-position: center;' align='center'>
				<img class='img-responsive' src='../public/images/Untitled.png' style='width:80%;height:auto;border-radius: 0%;opacity: 0.8;'/>
			</div>
		";
		
		echo $html;
	}
	
	function lock(){
		$this->load->view('lobiLock');
	}
}














