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
		/*
		echo "
			<div id='demo-wizard2' class='wizard-wrapper'>    
				<div class='wizard'>
					<form id='demo-form2' action='' class='lobi-form' novalidate='novalidate'>
						<ul class='wizard-tabs wizard-tab-balls nav-justified nav nav-pills'>
							<li class='active'>
								<a href='#tab11' data-toggle='tab' aria-expanded='true'>
									<span class='step'>1</span>
									<span class='title'>ข้อมูลส่วนตัว</span>
								</a>
							</li>
							<li>
								<a href='#tab22' data-toggle='tab'>
									<span class='step'>2</span>
									<span class='title'>ข้อมูลครอบครัว</span>
								</a>
							</li>
							<li>
								<a href='#tab33' data-toggle='tab'>
									<span class='step'>3</span>
									<span class='title'>ข้อมูลที่อยู่</span>
								</a>
							</li>
							<li>
								<a href='#tab44' data-toggle='tab'>
									<span class='step'>4</span>
									<span class='title'>ประวัติการศึกษา</span>
								</a>
							</li>
							<li>
								<a href='#tab55' data-toggle='tab'>
									<span class='step'>5</span>
									<span class='title'>ประวัติการทำงาน</span>
								</a>
							</li>
							<li>
								<a href='#tab66' data-toggle='tab'>
									<span class='step'>6</span>
									<span class='title'>ความสามารถพิเศษ</span>
								</a>
							</li>
							<li>
								<a href='#tab77' data-toggle='tab'>
									<span class='step'>7</span>
									<span class='title'>ทักษะการขับขี่</span>
								</a>
							</li>
							<li>
								<a href='#tab88' data-toggle='tab'>
									<span class='step'>8</span>
									<span class='title'>ข้อมูลอื่นๆ</span>
								</a>
							</li>
						</ul>
						<div class='tab-content bg-white'>
							<div class='tab-pane active' id='tab11' style='height:calc(100vh - 330px);overflow:auto;'>
								<fieldset>
									<header>ประวัติส่วนตัว</header>
									<div class='col-sm-2'>
										<div class='form-group'>
											คำนำหน้า
											<select class='form-control select2-demo' placeholder='คำนำหน้า'>
												<option value=''></option>
												<option value='นาย'>นาย</option>
												<option value='นาง'>นาง</option>
												<option value='นางสาว'>นางสาว</option>
											</select>
										</div>
									</div>
									<div class='col-sm-4'>
										<div class='form-group'>
											ชื่อ(ไทย)
											<input type='text' name='firstname' placeholder='ชื่อ(ไทย)'>
										</div>
									</div>
									<div class='col-sm-4'>
										<div class='form-group'>
											นามสกุล(ไทย)
											<input type='text' name='firstname' placeholder='นามสกุล(ไทย)'>
										</div>
									</div>
									<div class='col-sm-2'>
										<div class='form-group'>
											ชื่อเล่น(ไทย)
											<input type='text' name='firstname' placeholder='ชื่อเล่น(ไทย)'>
										</div>
									</div>
									
									<div class='col-sm-2'>
										<div class='form-group'>
											คำนำหน้า
											<select class='form-control select2-demo'>
												<option value=''></option>
												<option value='นาย'>Mr.</option>
												<option value='นาง'>Ms.</option>
												<option value='นางสาว'>Mrs.</option>
											</select>
										</div>
									</div>
									<div class='col-sm-4'>
										<div class='form-group'>
											ชื่อ(ไทย)
											<input type='text' name='firstname' placeholder='ชื่อ(ไทย)'>
										</div>
									</div>
									<div class='col-sm-4'>
										<div class='form-group'>
											นามสกุล(ไทย)
											<input type='text' name='firstname' placeholder='นามสกุล(ไทย)'>
										</div>
									</div>
									<div class='col-sm-2'>
										<div class='form-group'>
											ชื่อเล่น(ไทย)
											<input type='text' name='firstname' placeholder='ชื่อเล่น(ไทย)'>
										</div>
									</div>
									
									<div class='col-sm-3'>
										<div class='form-group'>
											เลขบัตรประชาชน
											<input type='text' name='firstname' placeholder='เลขบัตรประชาชน'>
										</div>
									</div>
									<div class='col-sm-3'>
										<div class='form-group'>
											บัตรออกให้ ณ
											<input type='text' name='firstname' placeholder='บัตรออกให้ ณ'>
										</div>
									</div>
									
									<div class='col-sm-2'>
										<div class='form-group'>
											จังหวัดที่ออกบัตร
											<select class='form-control select2-demo'>
												<option value=''></option>
												<option value='นาย'>Mr.</option>
												<option value='นาง'>Ms.</option>
												<option value='นางสาว'>Mrs.</option>
											</select>
										</div>
									</div>
									<div class='col-sm-2'>
										<div class='form-group'>
											วันที่ออกบัตร
											<input class='input-medium' type='text' data-provide='datepicker' data-date-language='th-th' placeholder='วันที่ออกบัตร'>
										</div>
									</div>
									<div class='col-sm-2'>
										<div class='form-group'>
											วันที่บัตรหมดอายุ
											<input class='input-medium' type='text' data-provide='datepicker' data-date-language='th-th' placeholder='วันที่บัตรหมดอายุ'>
										</div>
									</div>
									
									<div class='col-sm-2'>
										<div class='form-group'>
											วันเกิด
											<input class='input-medium' type='text' data-provide='datepicker' data-date-language='th-th' placeholder='ว/ด/ป เกิด'>
										</div>
									</div>
									<div class='col-sm-2'>
										<div class='form-group'>
											น้ำหนัก
											<input type='text' name='firstname' placeholder='น้ำหนัก'>
										</div>
									</div>
									<div class='col-sm-2'>
										<div class='form-group'>
											ส่วนสูง
											<input type='text' name='firstname' placeholder='ส่วนสูง'>
										</div>
									</div>
									
									<div class='col-sm-2'>
										<div class='form-group'>
											เชื้อชาติ
											<input type='text' name='firstname' placeholder='เชื้อชาติ'>
										</div>
									</div>
									
									<div class='col-sm-2'>
										<div class='form-group'>
											สัญชาติ
											<input type='text' name='firstname' placeholder='สัญชาติ'>
										</div>
									</div>
									
									<div class='col-sm-2'>
										<div class='form-group'>
											ศาสนา
											<input type='text' name='firstname' placeholder='ศาสนา'>
										</div>
									</div>
									
									<div class='col-sm-4'>
										<div class='form-group'>
											Email
											<input type='email' name='email' placeholder='Email Address'>
										</div>
									</div>
									
									<div class='col-sm-2'>
										<div class='form-group'>
											เบอร์โทร
											<input type='text' name='firstname' placeholder='เบอร์โทร'>
										</div>
									</div>
								</fieldset>
							
								<fieldset>
									<header>ข้อมูลด้านสุขภาพ</header>
									<div class='form-group'>
										<textarea id='textarea' class='form-control' maxlength='225' rows='4' placeholder='This textarea has a limit of 225 chars.'></textarea>
									</div>
								</fieldset>
							</div>
							<div class='tab-pane' id='tab22'>
								<fieldset>
									<header>Billing Information</header>
									<div class='row'>
										<div class='col-sm-6'>
											<div class='form-group'>
												<input type='text' name='firstname' placeholder='First name'>
											</div>
										</div>
										<div class='col-sm-6'>
											<div class='form-group'>
												<input type='text' name='lastname' placeholder='First name'>
											</div>
										</div>
									</div>
									<div class='form-group'>
										<input type='text' name='phone' class='form-control inputmask' data-mask='(999) - 99 99 99' data-mask-placeholder='x' placeholder='Phone'>
									</div>
									<div class='form-group'>
										<input type='text' name='company' placeholder='Company'>
									</div>
									<div class='row'>
										<div class='col-xxs-12 col-xs-6'>
											<div class='form-group'>
												<input type='text' name='address1' placeholder='Address 1'>
											</div>
										</div>
										<div class='col-xxs-12 col-xs-6'>
											<div class='form-group'>
												<input type='text' name='address2' placeholder='Address 2'>
											</div>
										</div>
									</div>
									<div class='row'>
										<div class='col-xxs-12 col-xs-6'>
											<div class='form-group'>
												<input type='text' name='city' placeholder='City'>
											</div>
										</div>
										<div class='col-xxs-12 col-xs-6'>
											<div class='form-group'>
												<select name='state'>
													<option value='1'>Alabama</option>
													<option value='2'>Alaska</option>
													<option value='3'>Arkansas</option>
													<option value='4'>Etc.</option>
												</select>
											</div>
										</div>
									</div>
									<div class='row'>
										<div class='col-xxs-12 col-xs-6'>
											<div class='form-group'>
												<input type='text' name='zipcode' class='inputmask' placeholder='Zip Code' data-mask='99999' data-mask-placeholder='*'>
											</div>
										</div>
										<div class='col-xxs-12 col-xs-6'>
											<div class='form-group'>
												<select name='country'>
													<option value='1'>United States</option>
													<option value='2'>United Kingdom</option>
													<option value='3'>Uganda</option>
													<option value='4'>Etc.</option>
												</select>
											</div>
										</div>
									</div>
								</fieldset>
							</div>
							<div class='tab-pane' id='tab33'>
								<fieldset>
									<header>Shipping Information</header>
									<div class='form-group'>
										<label class='radio lobiradio lobiradio-info'>
											<input type='radio' name='ship_info'> 
											<i></i> Standard Shipping $4.00
										</label>
										<label class='radio lobiradio lobiradio-info'>
											<input type='radio' name='ship_info'> 
											<i></i>  Express Shipping $8.00
										</label>
										<label class='radio lobiradio lobiradio-info'>
											<input type='radio' name='ship_info'> 
											<i></i>  Overnight Shipping $12.00
										</label>
									</div>
								</fieldset>
							</div>
							<div class='tab-pane' id='tab44'>
								<fieldset>
									<header>Payment Information</header>
									<div class='form-group'>
										<input type='text' name='credit_card' class='inputmask' data-mask='9999-9999-9999-9999' data-mask-placeholder='*' placeholder='Credit card number'>
									</div>
									<div class='form-group margin-bottom-no'>
										<label>Expiration Date</label>
									</div>
									<div class='row'>
										<div class='col-xxs-12 col-xs-6'>
											<div class='form-group'>
												<select name='exp_month'>
													<option value='1'>01 (Jan)</option>
													<option value='2'>02 (Feb)</option>
													<option value='3'>03 (Mar)</option>
													<option value='4'>04 (Apr)</option>
													<option value='5'>05 (May)</option>
													<option value='6'>06 (Jun)</option>
													<option value='7'>07 (Jul)</option>
													<option value='8'>08 (Aug)</option>
													<option value='9'>09 (Sep)</option>
													<option value='10'>10 (Oct)</option>
													<option value='11'>11 (Nov)</option>
													<option value='12'>12 (Dec)</option>
												</select>
											</div>
										</div>
										<div class='col-xxs-12 col-xs-6'>
											<div class='form-group'>
												<select name='exp_year'>
													<option value='1'>2015</option>
													<option value='2'>2016</option>
													<option value='3'>2017</option>
													<option value='4'>2018</option>
													<option value='5'>2019</option>
												</select>
											</div>
										</div>
									</div>
									<div class='form-group'>
										<input type='text' name='cvv' placeholder='CVV' class='inputmask' data-mask='999' data-mask-placeholder='*'>
									</div>
								</fieldset>
							</div>
							<div class='tab-pane' id='tab55'>
								<div class='alert alert-success'>
									<p>Congratulations! You have successfully submitted form</p>
								</div>
							</div>
							<div class='tab-pane' id='tab66'>
								<fieldset>
									<header>Email Address</header>
									<div class='form-group'>
										<input type='email' name='email' placeholder='Email Address'>
									</div>
								</fieldset>
							</div>
							
							<ul class='pager'>
								<li class='previous first disabled' style='display:none;'><a href='javascript:void(0)'>First</a></li>
								<li class='previous disabled'><a href='javascript:void(0)'>Previous</a></li>
								<li class='next last' style='display:none;'><a href='javascript:void(0)'>Last</a></li>
								<li class='next'><a href='javascript:void(0)'>Next</a></li>
							</ul>
						</div>
					</form>
				</div>
			</div>
			
			<script>
				$('.select2-demo').select2();
				$('.panel').lobiPanel({					
					reload: false,
					close: false,
					isOnFullScreen: false,
					unpin: false,
					minimize: false,
					editTitle: false
				});
			</script>
		";
		*/
		$html = "
			<!-- div class='col-md-12' align='center'>
				<img src='../public/images/background_home.jpg' style='max-width: 100%;height: 100%;border-radius: 0%;opacity: 0.8;'/>
			</div -->
			<div class='col-md-12' style='height:calc(100vh - 200px);background-image: url(\"../public/images/KOI FISHx.gif\"); background-position: center;' align='center'>
				<img class='img-responsive' src='../public/images/Untitled.png' style='width:80%;height:auto;border-radius: 0%;opacity: 0.8;'/>
			</div>
			
			<!-- div class='col-md-12' style='height:20vh;'>&emsp;</div>
			<div class='col-md-12' style='font-size:50pt;' align='center'>
				<div style='font-size:100pt;transform: rotate(0deg);width:100%;'>
					".
					chr(240).chr(159).chr(133).chr(166).
					chr(240).chr(159).chr(133).chr(148).
					chr(240).chr(159).chr(133).chr(155).
					chr(240).chr(159).chr(133).chr(146).
					chr(240).chr(159).chr(133).chr(158).
					chr(240).chr(159).chr(133).chr(156).
					chr(240).chr(159).chr(133).chr(148)
					."
				</div>
			</div -->
		";
		
		// $html = "
			// <div class='col-md-12' style='height:30vh;'>&emsp;</div>
			// <div class='col-md-12' style='font-size:50pt;' align='center'>
				// WELCOME
			// </div>
		// ";
		
		echo $html;
	}
	
	function lock(){
		$this->load->view('lobiLock');
	}
}
