/********************************************************
             ______@15/09/2020______
			 Pasakorn Boonded

********************************************************/
var _locat  = $('.k_tab1[name="home"]').attr('locat');
var _insert = $('.k_tab1[name="home"]').attr('cin');
var _update = $('.k_tab1[name="home"]').attr('cup');
var _delete = $('.k_tab1[name="home"]').attr('cdel');
var _level  = $('.k_tab1[name="home"]').attr('clev');
var _today  = $('.k_tab1[name="home"]').attr('today');
$(function(){
	$('#btnsearch').click(function(){
		fn_searchresult();
	});
	$('#btnadd').click(function(){
		fn_formadd('add','');
	});
	if(_insert == "T"){
		$('#btnadd').attr('disabled',false);
	}else{
		$('#btnadd').attr('disabled',true);
	}
});
var kb_searchresult = null;
function fn_searchresult(){
	dataToPost = new Object();
	dataToPost.OPTCODE   = $('#OPTCODE').val();
	dataToPost.OPTNAME   = $('#OPTNAME').val();
	dataToPost.LOCAT     = $('#LOCAT').val();
	$('#loadding').fadeIn(200);
	kb_searchresult = $.ajax({
		url: '../setup/CStock/accessory_search',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			$('#loadding').fadeOut(200);
			$('#setoptresult').html(data.html);
			
			document.getElementById("table-fixed-accessory").addEventListener("scroll", function(){
				var translate = "translate(0,"+(this.scrollTop - 1)+"px)";
				this.querySelector("thead").style.transform = translate;						
			});	
			
			$('.getit').hover(function(){
				$(this).css({'background-color':'#a9a9f9'});
				$('.trow[seq='+$(this).attr('seq')+']').css({'background-color':'#a9f9f9'});
			},function(){
				$(this).css({'background-color':''});
				$('.trow[seq='+$(this).attr('seq')+']').css({'background-color':''});
			});
			
			$('.getit').click(function(){
				$('#loadding').fadeIn(200);	
				var optcode = $(this).attr("OPTCODE");
				var locat   = $(this).attr("LOCAT");
				fn_formloaddata(optcode,locat);
			});
			
			kb_searchresult = null;	
		},
		beforeSend: function(){
			if(kb_searchresult !== null){kb_searchresult.abort();}
		}
	});
}
var kb_loaddata = null;
function fn_formloaddata($optcode,$locat){
	dataToPost = new Object();
	dataToPost.optcode  = $optcode;
	dataToPost.locat    = $locat;
	kb_loaddata = $.ajax({
		url: '../setup/CStock/accessory_loaddata',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			fn_formadd('edit',data);
			kb_loaddata = null;	
		},
		beforeSend: function(){
			if(kb_loaddata !== null){kb_loaddata.abort();}
		}
	});	
}
var kb_formadd = null;
function fn_formadd($event,$dataload){
	dataToPost = new Object();
	$('#loadding').fadeIn(200);
	kb_formadd = $.ajax({
		url: '../setup/CStock/accessory_formsetopt',
		//data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			$('#loadding').fadeOut(200);
			Lobibox.window({
				title:'FormSetup',
				width: 700,                
				height: 450,
				content: data.html,
				draggable: false,
				closeOnEsc: false,
				shown: function($this){
					loadformadd($this,$event,$dataload);
				}
			});
			
			kb_formadd = null;	
		},
		beforeSend: function(){
			if(kb_formadd !== null){kb_formadd.abort();}
		}
	});
}
function loadformadd($thisWindow,$event,$dataload){
	$('#add_onhand').attr('disabled',true);
	
	$('#btn_addopt').click(function(){
		$('#loadding').fadeIn(200);
		$.ajax({
			url:'../Cselect2K/getformOPTCODE',
			type: 'POST',
			dataType: 'json',
			success: function(data){
				$('#loadding').fadeOut(200);
				Lobibox.window({
					title: 'FORM SEARCH',
					//width: $(window).width(),
					//height: $(window).height(),
					content: data.html,
					draggable: false,
					closeOnEsc: false,
					onShow: function(lobibox){ $('body').append(jbackdrop); },
					shown: function($this){
						$('#btn_searchopt').click(function(){ fnResultOPT(); });
						var kb_btnsearch = null;
						function fnResultOPT(){
							dataToPost = new Object();
							dataToPost.optcode  = $('#optcode').val();
							dataToPost.optname  = $('#optname').val();
							dataToPost.locat    = $('#locat').val();
							$('#loadding').fadeIn(200);
							kb_btnsearch = $.ajax({
								url:'../Cselect2K/getSearchOPTCODE',
								data:dataToPost,
								type: 'POST',
								dataType: 'json',
								success: function(data){
									$('#loadding').fadeOut(200);
									$('#opt_result').html(data.html);
									
									$('.getitopt').hover(function(){
										$(this).css({'background-color':'#a9a9f9'});
										$('.trows[seqs='+$(this).attr('seqs')+']').css({'background-color':'#a9f9f9'});
									},function(){
										$(this).css({'background-color':''});
										$('.trows[seqs='+$(this).attr('seqs')+']').css({'background-color':''});
									});
									
									$('.getitopt').unbind('click');
									$('.getitopt').click(function(){
										attr = new Object();
										attr.optcode  = $(this).attr('OPTCODE');
										attr.optname  = $(this).attr('OPTNAME');
										$('#add_optcode').val(attr.optcode);
										$('#add_optname').val(attr.optname);
										$this.destroy();
									});
									kb_btnsearch = null;
								},
								beforeSend: function(){
									if(kb_btnsearch !== null){ kb_btnsearch.abort(); }
								}
							});
						}
					},
					beforeClose : function(){
						$('.jbackdrop')[($('.jbackdrop').length)-1].remove();
					}
				});
			}
		});
	});
	$('#add_locat').select2({
		placeholder: 'เลือก',
		ajax: {
			url: '../Cselect2K/getLOCAT',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now  = (typeof $('#add_locat').find(':selected').val() === 'undefined' ? "":$('#add_locat').find(':selected').val()); 
				dataToPost.q    = (typeof params.term === 'undefined' ? '' : params.term);

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
		allowClear: true,
		multiple: false,
		dropdownParent: $('#add_locat').parent().parent(),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	if($event == "add"){
		$('#add_locat').change(function(){
			dataToPost = new Object();
			dataToPost.locat = (typeof $('#add_locat').find(':selected').val() === 'undefined' ? '':$('#add_locat').find(':selected').val());
			//$('#loadding').fadeIn(200);
			$.ajax({
				url: '../Cselect2K/changeLOCAT',
				data: dataToPost,
				type: 'POST',
				dataType: 'json',
				success: function(data){
					//$('#loadding').fadeOut(200);
					$('#add_locatnm').val(data.locatnm);	
				}
			});
		});	
	}
	$('#btn_save').click(function(){
		var optcode = $('#add_optcode').val();
		Lobibox.confirm({
			title: 'ยืนยันการทำรายการ',
			iconClass: false,
			closeButton: false,
			msg: 'คุณต้องการบันทึกรหัสอุปกรณ์เสริม  '+optcode+'?',
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
				if (type === 'ok'){ fn_save($thisWindow,$event); }
				$('.jbackdrop')[($('.jbackdrop').length)-1].remove();
			}
		});
	});
	
	$('#btn_del').click(function(){
		var optcode = $('#add_optcode').val();
		Lobibox.confirm({
			title: 'ยืนยันการทำรายการ',
			iconClass: false,
			closeButton: false,
			msg: 'คุณต้องการลบรหัสอุปกรณ์เสริม  '+optcode+'?',
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
	if($event == "add"){
		$('#btn_del').hide();
	}else{
		fn_loaddata($dataload);
		//$('#add_onhand').attr('disabled',false);
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
	}
}
function fn_loaddata($data){
	$('#add_optcode').attr("disabled",true);
	$('#add_optname').attr("disabled",true);
	$('#btn_addopt').attr("disabled",true);
	$('#add_locat').attr("disabled",true);
	$('#btn_locatnm').attr("disabled",true);
	
	
	$('#add_optcode').val($data.OPTCODE);
	$('#add_optname').val($data.OPTNAME);
	var newOption = new Option($data.LOCAT, $data.LOCAT, true, true);
	$('#add_locat').empty().append(newOption).trigger('change');
	$('#add_locatnm').val($data.LOCATNM);
	$('#add_unitprc').val($data.UNITPRC);
	$('#add_unitcst').val($data.UNITCST);
	$('#add_onhand').val($data.ONHAND);
}

function fn_save($thisWindow,$event){
	dataToPost = new Object();
	dataToPost.OPTCODE = $('#add_optcode').val();
	dataToPost.OPTNAME = $('#add_optname').val();
	dataToPost.LOCAT   = (typeof $('#add_locat').find(':selected').val() === 'undefined' ? '':$('#add_locat').find(':selected').val());
	dataToPost.UNITPRC = $('#add_unitprc').val();
	dataToPost.UNITCST = $('#add_unitcst').val();
	dataToPost.ONHAND  = $('#add_onhand').val();
	dataToPost.EVENT   = $event;
	$('#loadding').fadeIn(200);
	$.ajax({
		url: '../setup/CStock/accessory_save',
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
	dataToPost.OPTCODE = $('#add_optcode').val();
	dataToPost.LOCAT   = (typeof $('#add_locat').find(':selected').val() === 'undefined' ? '':$('#add_locat').find(':selected').val());
	
	$('#loadding').fadeIn(200);
	$.ajax({
		url: '../setup/CStock/accessory_del',
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