/********************************************************
             ______@27/08/2020______
			 Pasakorn Boonded

********************************************************/
var _locat  = $('.k_tab1[name="home"]').attr('locat');
var _insert = $('.k_tab1[name="home"]').attr('cin');
var _update = $('.k_tab1[name="home"]').attr('cup');
var _delete = $('.k_tab1[name="home"]').attr('cdel');
var _level  = $('.k_tab1[name="home"]').attr('clev');
var _today  = $('.k_tab1[name="home"]').attr('today');
$(function(){
	if(_insert == "T"){
		$('#btnaddoptinv').attr('disabled',false);
	}else{
		$('#btnaddoptinv').attr('disabled',true);
	}
	$('#btnsearchlist').click(function(){
		fn_searchresult();
	});
	$('#btnaddoptinv').click(function(){
		fn_formaddoptinv('add','');
	});
});
var kb_searchresult = null;
function fn_searchresult(){
	dataToPost = new Object();
	dataToPost.RECVNO  = $('#RECVNO').val();
	dataToPost.RECVDT  = $('#RECVDT').val();
	dataToPost.RVLOCAT = $('#RVLOCAT').val();
	dataToPost.INVNO   = $('#INVNO').val();
	$('#loadding').fadeIn(200);
	
	kb_searchresult = $.ajax({
		url: '../SYS02/ReceiveOPTINV/Search',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			
			$('#loadding').fadeOut(200);
			$('#resultOptinv').html(data.html);
			
			$('#table-Receiveoptinv').on('draw.dt',function(){ redraw(); });
			fn_datatables('table-Receiveoptinv',1,275);
			
			$('.getit').unbind("click");
			$('.getit').click(function(){
				$('#loadding').fadeIn(200);
				var recvno = $(this).attr('RECVNO');
				fn_formeditoptinv(recvno);
			});
			kb_searchresult = null;	
			
		},
		beforeSend: function(){
			if(kb_searchresult !== null){kb_searchresult.abort();}
		}
	});
}
var kb_loadoptinv = null;
function fn_formeditoptinv($recvno){
	dataToPost = new Object();
	dataToPost.recvno  = $recvno;
	kb_loadoptinv = $.ajax({
		url: '../SYS02/ReceiveOPTINV/loadFormOPT',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			fn_formaddoptinv('edit',data);
			kb_loadoptinv = null;	
		},
		beforeSend: function(){
			if(kb_loadoptinv !== null){kb_loadoptinv.abort();}
		}
	});	
}
function redraw(){
	$('.dataTables_scrollBody').css({'height':'calc(-380px + 100vh)'});
	
	$('.getit').hover(function(){
		$(this).css({'background-color':'#a9a9f9'});
		$('.trow[seq='+$(this).attr('seq')+']').css({'background-color':'#a9f9f9'});
	},function(){
		$(this).css({'background-color':''});
		$('.trow[seq='+$(this).attr('seq')+']').css({'background-color':''});
	});
}
var kb_addoptinv = null;
function fn_formaddoptinv($param,$data){
	$('#loadding').fadeIn(200);
	kb_addoptinv = $.ajax({
		url:'../SYS02/ReceiveOPTINV/FromAddStockASC',
		//data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			$('#loadding').fadeOut(200);
			Lobibox.window({
				title: 'บันทึกอุปกรณ์เสริมเข้าสต๊อก',
				width: $(window).width(),
				height: $(window).height(),
				content: data.html,
				draggable: false,
				closeOnEsc: false,
				shown: function($this){
					wizard($param,$data,$this);
				}
			});
			kb_addoptinv = null;
		},
		beforeSend: function(){  if(kb_addoptinv !== null){ kb_addoptinv.abort(); } }
		//,error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
	});
}

function wizard($param,$dataLoad,$thisWindow){
	LobiAdmin.loadScript([
		'../public/lobiadmin-master/version/1.0/ajax/js/plugin/bootstrap-wizard/jquery.bootstrap.wizard.js',
		'../public/lobiadmin-master/version/1.0/ajax/js/plugin/jquery-validation/jquery.validate.min.js',
		'../public/lobiadmin-master/version/1.0/ajax/js/plugin/jquery-validation/additional-methods.min.js',
		'../public/lobiadmin-master/version/1.0/ajax/js/plugin/jasny-bootstrap/jasny-bootstrap.min.js'
	], initPage);
	
	function initPage(){
		$('#wizard-optinv').bootstrapWizard({
			onTabClick: function(li, ul, ind, ind2, xxx){
				var beforeChanged = 0; 
				var index = 0; //tab ก่อนเปลี่ยน 
				$('.wizard-tabs li').each(function(){
					//ลบ wizard ที่ active อยู่ทั้งหมด
					if($(this).hasClass('active')){
						index = beforeChanged;
					}
					
					beforeChanged = beforeChanged + 1;
				});
				var invno = $('#add_invno').val(); //เลขที่ใบส่งสินค้า
				var invdt = $('#add_invdt').val(); //วันที่ใบส่งสินค้า
				
				switch(index){
					case 0: //tab1		
						var msg = "";
						if(invno == ''){ msg = "กรุณากรอกเลขที่ใบส่งสินค้าก่อนครับ"; }
						if(invdt == ''){ msg = "กรุณากรอกวันที่ใบส่งสินค้าก่อนครับ"; }
						
						if(msg != ''){
							Lobibox.notify('warning', {
								title: 'แจ้งเตือน',
								size: 'mini',
								closeOnClick: false,
								delay: 9000,
								pauseDelayOnHover: true,
								continueDelayOnInactiveTab: false,
								icon: true,
								messageHeight: '90vh',
								msg: msg
							});
							
							return false;
						}else{
							nextTab(ind2);	
						} 
						break;
					case 1: //tab2
						nextTab(ind2); 
						break;
				}
			}
		});
	}
	
	function nextTab(ind2){
		$('.wizard-tabs li').each(function(){
			//ลบ wizard ที่ active อยู่ทั้งหมด
			$('.wizard-tabs li').removeClass('active');
		});
		if(!$('.wizard-tabs li').eq(ind2).hasClass('active')){
			// active tab ถัดไป
			$('.wizard-tabs li').eq(ind2).addClass('active');
		}
		
		var $id = $('.wizard-tabs li').eq(ind2).find('a').attr('href').replace('#','');
		var $tabContent = $('.tab-content');
		$tabContent.find('.tab-pane').removeClass('active');
		$tabContent.find('.tab-pane[name='+$id+']').addClass('active');	
		
		return true;					
	}
	
	//tab11
	$('#add_recvno').attr('readonly',true);
	$('#add_recvno').val('Auto Genarate');
	
	$('#add_locat').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2K/getLOCAT',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now = $('#add_locat').find(':selected').val();
				dataToPost.q = (typeof params.term === 'undefined' ? '' : params.term);
				
				return dataToPost;				
			},
			dataType: 'json',
			delay: 1000,
			processResults: function (data) {
				return {
					results: data
				};
			},
			cache: true
        },
		allowClear: false,
		multiple: false,
		dropdownParent: $('#add_locat').parent().parent(),
		disabled: (_locat == 'OFFยน' ? false : true),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	$('#add_apcode').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2K/getAPCODE',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now = $('#add_apcode').find(':selected').val();
				dataToPost.q = (typeof params.term === 'undefined' ? '' : params.term);
				
				return dataToPost;				
			},
			dataType: 'json',
			delay: 1000,
			processResults: function (data) {
				return {
					results: data
				};
			},
			cache: true
        },
		allowClear: false,
		multiple: false,
		dropdownParent: $('#add_apcode').parent().parent(),
		//disabled: (_locat == 'OFFยน' ? false : true),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	$('#add_rvcode').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2K/getOFFICER',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now = $('#add_rvcode').find(':selected').val();
				dataToPost.q = (typeof params.term === 'undefined' ? '' : params.term);
				
				return dataToPost;				
			},
			dataType: 'json',
			delay: 1000,
			processResults: function (data) {
				return {
					results: data
				};
			},
			cache: true
        },
		allowClear: false,
		multiple: false,
		dropdownParent: $('#add_rvcode').parent().parent(),
		//disabled: (_locat == 'OFFยน' ? false : true),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	$('#add_fltax').select2({
		placeholder: 'เลือก',
		minimumResultsForSearch: -1,
		dropdownParent: $('#add_fltax').parent().parent(),
		allowClear: false,
		width: '100%'
	});
	//tab2
	
	$('#btn_optcode').click(function(){
		fn_addOptcode();
	});
	
	$('#btn_save').click(function(){ //save
		Lobibox.confirm({
			title: 'ยืนยันการทำรายการ',
			iconClass: false,
			closeButton: false,
			msg: 'คุณต้องการบันทึกรายการขายอุปกรณ์เสริมเข้าสู่สต๊อกหรือไม่ ?',
			buttons: {
				ok : {
					'class': 'btn btn-primary glyphicon glyphicon-ok',
					text: ' ยืนยัน',
					closeOnClick: true,
				},
				cancel : {
					'class': 'btn btn-danger glyphicon glyphicon-cancel',
					text: ' ยกเลิก',
					closeOnClick: true
				},
			},
			onShow: function(lobibox){ $('body').append(jbackdrop); },
			callback: function(lobibox, type){
				if (type === 'ok'){ fn_save($thisWindow); }
				$('.jbackdrop')[($('.jbackdrop').length)-1].remove();
			}
		});
	});
	if($param == "add"){
		$('#btn_del').hide();
	}else{
		$('#btn_del').show();
		loadData($dataLoad);
		if(_update == "T"){
			$('#btn_save').attr('disabled',false);
		}else{
			$('#btn_save').attr('disabled',true);
		}
		if(_delete == "T"){
			$('#btn_del').attr('disabled',false);
		}else{
			$('#btn_del').attr('disabled',true);
		}
		
		var recvno = $('#add_recvno').val();	
		$('#btn_del').click(function(){
			Lobibox.confirm({
				title: 'ยืนยันการทำรายการ',
				iconClass: false,
				closeButton: false,
				msg: 'คุณต้องลบรายการขายอุปกรณ์เสริมใบรับสินค้าเลขที่ : <span style="color:red;">'+recvno+'</span> ?',
				buttons: {
					ok : {
						'class': 'btn btn-primary glyphicon glyphicon-ok',
						text: ' ยืนยัน',
						closeOnClick: true,
					},
					cancel : {
						'class': 'btn btn-danger glyphicon glyphicon-cancel',
						text: ' ยกเลิก',
						closeOnClick: true
					},
				},
				onShow: function(lobibox){ $('body').append(jbackdrop); },
				callback: function(lobibox, type){
					if (type === 'ok'){ fn_delete($thisWindow); }
					$('.jbackdrop')[($('.jbackdrop').length)-1].remove();
				}
			});
		});
	}
}
function loadData($data){
	$('#add_locat').attr('disabled',true);
	$('#add_recvno').attr('disabled',true);
	$('#add_recvdt').attr('disabled',true);
	$('#add_credit').attr('disabled',true);
	
	$('#add_vatrt').attr('disabled',true);
	$('#add_taxno').attr('disabled',true);
	$('#add_taxdt').attr('disabled',true);
	$('#add_duedt').attr('disabled',true);
	
	
	var newOption = new Option($data.RVLOCAT, $data.RVLOCAT, true, true);
	$('#add_locat').empty().append(newOption).trigger('change');
	$('#add_recvno').val($data.RECVNO);	
	$('#add_recvdt').val($data.RECVDT);
	var newOption = new Option($data.APNAME, $data.APCODE, true, true);
	$('#add_apcode').empty().append(newOption).trigger('change');
	$('#add_credit').val($data.CREDIT);
	$('#add_vatrt').val($data.VATRT);
	$('#add_invno').val($data.INVNO);
	$('#add_invdt').val($data.INVDT);
	$('#add_taxno').val($data.TAXNO);
	$('#add_taxdt').val($data.TAXDT);
	var newOption = new Option($data.RVNAME, $data.RVCODE, true, true);
	$('#add_rvcode').empty().append(newOption).trigger('change');
	$('#add_duedt').val($data.DUEDT);
	$('#add_fltax').val($data.FLTAX).trigger('change');
	$('#add_descp').val($data.DESCP);
	
	//tab2
	$('#dataTables-asc tbody').append($data.listopt);
	$('#get_netcst').val($data.NETCST);
	$('#get_netvat').val($data.NETVAT);
	$('#get_nettot').val($data.NETTOT);
	
	
	$('.acslistdel').click(function(){
		var del = $(this);
		del.parent().parent().remove();
		
		var atnetcst = 0; 
		var vatrt = 0; 
		$('.acslistdel').each(function(){
			atnetcst += parseFloat($(this).attr('NETCST'));
			vatrt     = parseFloat($(this).attr('VATRT'));
		});
		
		var netcst = atnetcst;
		var netvat = parseFloat((atnetcst * vatrt) / 100);
		var nettot = parseFloat(netcst + netvat);
		
		$('#get_netcst').val(netcst.toFixed(2));
		$('#get_netvat').val(netvat.toFixed(2));
		$('#get_nettot').val(nettot.toFixed(2));
	});
}
	
function fn_addOptcode(){
	var html = " \
		<div class='row'> \
			<div class='col-sm-6'> \
				<div class='form-group'> \
					รหัสอุปกรณ์ \
					<select id='optcode' class='form-control input-sm'></select> \
				</div> \
			</div>	\
			<div class='col-sm-6'> \
				<div class='form-group'> \
					จำนวนรับ \
					<input type='text' id='qty' class='form-control jzAllowNumber' style='text-align:right;'> \
				</div> \
			</div> \
			<div class='col-sm-6'> \
				<div class='form-group'> \
					ราคา/หน่วย \
					<input type='text' id='unitcst' class='form-control jzAllowNumber' style='text-align:right;'> \
				</div> \
			</div> \
			<div class='col-sm-6'> \
				<div class='form-group'> \
					จำนวนเงิน \
					<input type='text' id='totcst' class='form-control' disabled style='text-align:right;'> \
				</div> \
			</div> \
			<div class='col-sm-6'> \
				<div class='form-group'> \
					ส่วนลด \
					<input type='text' id='dscamt' class='form-control jzAllowNumber' style='text-align:right;'> \
				</div> \
			</div> \
			<div class='col-sm-6'> \
				<div class='form-group'> \
					ยอดรวมสุทธิ \
					<input type='text' id='netcst' class='form-control' disabled style='text-align:right;'> \
				</div> \
			</div> \
			<div class='col-sm-12'> \
				<button id='btn_addOptcode' class='btn btn-primary btn-block'><span class='glyphicon glyphicon-add'>เพิ่ม</span></button> \
			</div> \
			<br> \
			<div id='locat_result' class='col-sm-12'></div> \
		</div> \
	";
	Lobibox.window({
		title: 'เพิ่มอุปกรณ์เสริม',
		width: 600,
		height: 400,
		draggable: true,
		content: html,
		closeOnEsc: true,
		onShow: function(lobibox){ $('body').append(jbackdrop); },
		shown: function($this){
			fn_formaddoptcode($this);
		},
		beforeClose: function(){
			$('.jbackdrop')[($('.jbackdrop').length)-1].remove();
		}
	});
}
function fn_formaddoptcode($thiswindow){
	$('#optcode').select2({
		placeholder: 'เลือก',
		ajax: {
			url: '../Cselect2K/getOPTCODE_ACS',
			data: function (params){
				dataToPost = new Object();
				dataToPost.q = (typeof params.term === 'undefined' ? '' : params.term);
				dataToPost.add_locat = (typeof $('#add_locat').find(':selected').val() === 'undefined' ? "":$('#add_locat').find(':selected').val());	
				
				return dataToPost;
			},
			dataType: 'json',
			delay: 1000,
			processResults: function (data){
				return {
					results: data
				};
			},
			cache: true
		},
		allowClear: true,
		multiple: false,
		dropdownParent: $(".k_tab1"),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	$('#unitcst').keyup(function(){
		var qty     = parseInt($('#qty').val());
		var unitcst = parseInt($('#unitcst').val());
		var totcst  = parseFloat(qty * unitcst) || 0; 
		$('#totcst').val(totcst);
		$('#netcst').val(totcst);
	});
	$('#dscamt').keyup(function(){
		var qty     = parseInt($('#qty').val());
		var unitcst = parseInt($('#unitcst').val());
		var totcst  = parseFloat(qty * unitcst) || 0;
		var dscamt  = parseInt($('#dscamt').val());
		var total   = parseFloat(totcst - dscamt) || totcst;
		
		$('#netcst').val(total);
	});
	$('#btn_addOptcode').click(function(){
		dataToPost = new Object();
		dataToPost.optcode   = (typeof $('#optcode').find(':selected').val() === 'undefined' ? '':$('#optcode').find(':selected').val());
		dataToPost.qty       = $('#qty').val();
		dataToPost.unitcst   = $('#unitcst').val();
		dataToPost.totcst    = $('#totcst').val();
		dataToPost.dscamt    = $('#dscamt').val();
		dataToPost.netcst    = $('#netcst').val();
		dataToPost.add_vatrt = $('#add_vatrt').val();
		dataToPost.locat     = (typeof $('#add_locat').find(':selected').val() === 'undefined' ? '':$('#add_locat').find(':selected').val());
		$('.acslistdel').each(function(){
			netcst += parseFloat($(this).attr('NETCST'));
		});
		
		$('#loadding').fadeIn(200);
		$.ajax({
			url:'../SYS02/ReceiveOPTINV/Addlistdetail_opt',
			data: dataToPost,
			type: 'POST',
			dataType: 'json',
			success: function(data){
				$('#loadding').fadeOut(200);
				if(data.error){
					Lobibox.notify('warning', {
						title: 'แจ้งเตือน',
						size: 'mini',
						closeOnClick: false,
						delay: 5000,
						pauseDelayOnHover: true,
						continueDelayOnInactiveTab: false,
						soundPath: '../public/lobiadmin-master/version/1.0/ajax/sound/lobibox/',   // The folder path where sounds are located
						soundExt: '.ogg',
						icon: true,
						messageHeight: '90vh',
						msg: data.msg
					});
				}else{
					$('#dataTables-asc tbody').append(data.html);
					$thiswindow.destroy();
					
					var atnetcst = 0;
					$('.acslistdel').each(function(){
						atnetcst += parseFloat($(this).attr('NETCST'));
					});
					var netcst = atnetcst;
					var vatrt  = data.vatrt;
					var netvat = parseFloat((atnetcst * vatrt) / 100);
					var nettot = parseFloat(netcst + netvat);
					
					$('#get_netcst').val(netcst.toFixed(2));
					$('#get_netvat').val(netvat.toFixed(2));
					$('#get_nettot').val(nettot.toFixed(2));
					
					$('.acslistdel').unbind("click");
					$('.acslistdel').click(function(){
						var del = $(this);
						del.parent().parent().remove();
						
						var atnetcst = 0;
						$('.acslistdel').each(function(){
							atnetcst += parseFloat(del.attr('NETCST'));
						});
						var netcst = atnetcst;
						var vatrt  = data.vatrt;
						var netvat = parseFloat((atnetcst * vatrt) / 100);
						var nettot = parseFloat(netcst + netvat);
						
						$('#get_netcst').val(netcst.toFixed(2));
						$('#get_netvat').val(netvat.toFixed(2));
						$('#get_nettot').val(nettot.toFixed(2));
					});
				}
			}
			,error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
		});
	});
	$('#optcode').change(function(){
		var erroropt = false;
		$('.acslistdel').each(function(){
			var optlist = $(this).attr('OPTCODE');
			var addopt  = $('#optcode').val();
			if(optlist == addopt){
				erroropt = true;
			}
		});	
		if(erroropt){
			Lobibox.notify('warning', {
				title: 'แจ้งเตือน',
				size: 'mini',
				closeOnClick: false,
				delay: 3000,
				pauseDelayOnHover: true,
				continueDelayOnInactiveTab: false,
				icon: true,
				messageHeight: '90vh',
				msg: "มีรหัสอุปกรณ์แล้วครับ"
			});
			$('#optcode').empty();
		}
	});
}

function removelist(){
	
}
function fn_save($thisWindow){
	dataToPost = new Object();
	dataToPost.LOCAT   = (typeof $('#add_locat').find(':selected').val() === 'undefined' ? '':$('#add_locat').find(':selected').val());
	dataToPost.RECVNO  = $('#add_recvno').val();
	dataToPost.RECVDT  = $('#add_recvdt').val();
	dataToPost.APCODE  = (typeof $('#add_apcode').find(':selected').val() === 'undefined' ? '':$('#add_apcode').find(':selected').val());
	dataToPost.CREDIT  = $('#add_credit').val();
	dataToPost.VATRT   = $('#add_vatrt').val();
	dataToPost.INVNO   = $('#add_invno').val();
	dataToPost.INVDT   = $('#add_invdt').val();
	dataToPost.TAXNO   = $('#add_taxno').val();
	dataToPost.TAXDT   = $('#add_taxdt').val();
	dataToPost.RVCODE  = $('#add_rvcode').val();
	dataToPost.DUEDT   = $('#add_duedt').val();
	dataToPost.FLTAX   = (typeof $('#add_fltax').find(':selected').val() === 'undefined' ? '':$('#add_fltax').find(':selected').val());
	dataToPost.DESCP   = $('#add_descp').val();
	
	var listopt = [];
	$('.acslistdel').each(function(){
		var list = [];
		list.push($(this).attr('OPTCODE'));
		list.push($(this).attr('OPTNAME'));
		list.push($(this).attr('QTY'));
		list.push($(this).attr('UNITCST'));
		list.push($(this).attr('TOTCST'));
		list.push($(this).attr('DSCAMT'));
		list.push($(this).attr('NETCST'));
		
		listopt.push(list);
	});
	dataToPost.listopt  = (listopt == "" ? "nolist":listopt);
	dataToPost.NETCST   = $('#get_netcst').val();
	dataToPost.NETVAT   = $('#get_netvat').val();
	dataToPost.NETTOT   = $('#get_nettot').val();
	
	$('#loadding').fadeIn(200);
	$.ajax({
		url: '../SYS02/ReceiveOPTINV/Save',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			$('#loadding').fadeOut(200);
			if(data.error == "N"){
				Lobibox.notify('warning', {
					title: 'แจ้งเตือน',
					size: 'mini',
					closeOnClick: false,
					delay: 3000,
					pauseDelayOnHover: true,
					continueDelayOnInactiveTab: false,
					icon: true,
					messageHeight: '90vh',
					msg: data.msg
				});
			}else if(data.error == "Y"){
				Lobibox.notify('success', {
					title: 'แจ้งเตือน',
					size: 'mini',
					closeOnClick: false,
					delay: 3000,
					pauseDelayOnHover: true,
					continueDelayOnInactiveTab: false,
					icon: true,
					messageHeight: '90vh',
					msg: data.msg
				});
				$thisWindow.destroy();
				fn_searchresult();
			}else{
				Lobibox.notify('error', {
					title: 'แจ้งเตือน',
					size: 'mini',
					closeOnClick: false,
					delay: 3000,
					pauseDelayOnHover: true,
					continueDelayOnInactiveTab: false,
					icon: true,
					messageHeight: '90vh',
					msg: data.msg
				});
			}	
		}
		,error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
	});
}
function fn_delete($thisWindow){
	dataToPost = new Object();
	dataToPost.LOCAT   = (typeof $('#add_locat').find(':selected').val() === 'undefined' ? '':$('#add_locat').find(':selected').val());
	dataToPost.RECVNO  = $('#add_recvno').val();
	dataToPost.INVNO   = $('#add_invno').val();
	
	var listopt = [];
	$('.acslistdel').each(function(){
		var list = [];
		list.push($(this).attr('OPTCODE'));
		list.push($(this).attr('QTY'));
		
		listopt.push(list);
	});
	dataToPost.listopt  = (listopt == "" ? "nolist":listopt);
	
	$('#loadding').fadeIn(200);
	$.ajax({
		url: '../SYS02/ReceiveOPTINV/Delopt',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			$('#loadding').fadeOut(200);
			if(data.error == "N"){
				Lobibox.notify('warning', {
					title: 'แจ้งเตือน',
					size: 'mini',
					closeOnClick: false,
					delay: 3000,
					pauseDelayOnHover: true,
					continueDelayOnInactiveTab: false,
					icon: true,
					messageHeight: '90vh',
					msg: data.msg
				});
			}else if(data.error == "Y"){
				Lobibox.notify('success', {
					title: 'แจ้งเตือน',
					size: 'mini',
					closeOnClick: false,
					delay: 3000,
					pauseDelayOnHover: true,
					continueDelayOnInactiveTab: false,
					icon: true,
					messageHeight: '90vh',
					msg: data.msg
				});
				$thisWindow.destroy();
				fn_searchresult();
			}else{
				Lobibox.notify('danger', {
					title: 'แจ้งเตือน',
					size: 'mini',
					closeOnClick: false,
					delay: 3000,
					pauseDelayOnHover: true,
					continueDelayOnInactiveTab: false,
					icon: true,
					messageHeight: '90vh',
					msg: data.msg
				});
			}	
		}
		,error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
	});
}