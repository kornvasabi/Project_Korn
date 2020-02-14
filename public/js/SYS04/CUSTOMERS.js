/********************************************************
             ______@04/11/2019______
			 Pasakorn

********************************************************/
var _locat  = $('.tab1[name="home"]').attr('locat');
var _insert = $('.tab1[name="home"]').attr('cin');
var _update = $('.tab1[name="home"]').attr('cup');
var _delete = $('.tab1[name="home"]').attr('cdel');
var _level  = $('.tab1[name="home"]').attr('clev');

$(function(){
	if(_insert == 'T'){
		$('#add_groupsn').attr('disabled',false);	
	}else{
		$('#add_groupsn').attr('disabled',true);	
	}
});

$('#search_groupsn').click(function(){
	searchsn();
});

function searchsn(){
	dataToPost = new Object();
	dataToPost.sircod = $('#sircod').val();
	dataToPost.sirnam = $('#sirnam').val();
	
	var spinner = $('body>.spinner').clone().removeClass('hide');
	$('#setgroupResult').html('');
	$('#setgroupResult').append(spinner);
	
	$.ajax({
		url: '../SYS04/CUSTOMERS/groupSearchsn',
		data:dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			$('#setgroupResult').find('.spinner, .spinner-backdrop').remove();
			$('#setgroupResult').html(data.html);
			afterSearch();
		}
	});
}

$('#add_groupsn').click(function(){
	var spinner = $('body>.spinner').clone().removeClass('hide');
	$('#tab2_main').html('');
	$('#tab2_main').append(spinner);
	
	$('.tab1').hide();
	$('.tab2').show();		
	dataToPost = new Object();
	$.ajax({
		url: '../SYS04/CUSTOMERS/groupGetFormSN',
		data:dataToPost,
		type:'POST',
		dataType:'json',
		success:function(data){
			$('#tab2_main').find('.spinner, .spinner-backdrop').remove();
			$('#tab2_main').html(data.html);
			
			$('#t2sircod').val('');
			$('#t2sirnam').val('');
			$('#tab2save').attr('action','add');
			$('#tab2del').attr('disabled',true);
			afterSelect();
		}
	});
});

function afterSearch(){
	document.getElementById("tbScroll").addEventListener("scroll", function(){
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
		dataToPost = new Object();
		dataToPost.SIRCOD = $(this).attr('SIRCOD');
	
		var spinner = $('body>.spinner').clone().removeClass('hide');
		$('#tab2_main').html('');
		$('#tab2_main').append(spinner);
		
		$('.tab1').hide();
		$('.tab2').show();
		
		$('#tab2save').attr('action','edit');
		
		$.ajax({
			url:'../SYS04/CUSTOMERS/groupGetFormSN',
			data:dataToPost,
			type:'POST',
			dataType:'json',
			success:function(data){
				$('#tab2_main').find('.spinner, .spinner-backdrop').remove();
				$('#tab2_main').html(data.html);
				
				$('#t2gcode').attr('readonly',true);
				
				if(_insert == 'T'){
					$('#tab2save').attr('disabled',false);	
				}else{
					$('#tab2save').attr('disabled',true);	
				}
				
				if(_delete == 'T'){
					$('#tab2del').attr('disabled',false);	
				}else{
					$('#tab2del').attr('disabled',true);	
				}
				
				if(_update == 'T'){
					$('#tab2save').attr('disabled',false);	
				}else{
					$('#tab2save').attr('disabled',true);	
				}
				afterSelect();
			}
		});
	});
}

function afterSelect(){
	$('#tab2back').click(function(){
		$('.tab1').show();
		$('.tab2').hide();
	});
	$('#tab2save').click(function(){
		Lobibox.confirm({
			title: 'ยืนยันการทำรายการ',
			iconClass: false,
			msg: "คุณต้องการบันทึก ?",
			buttons: {
				ok : {
					'class': 'btn btn-primary',
					text: 'ยืนยัน',
					closeOnClick: true,
				},
				cancel : {
					'class': 'btn btn-danger',
					text: 'ยกเลิก',
					closeOnClick: true
				},
			},
			callback: function(lobibox, type){
				var btnType;
				if (type === 'ok'){
					dataToPost = new Object();
					dataToPost.sircod = $('#t2sircod').val();
					dataToPost.sirnam = $('#t2sirnam').val();
					dataToPost.action = $('#tab2save').attr('action');
					$.ajax({
						url:'../SYS04/CUSTOMERS/groupSave',
						data:dataToPost,
						type:'POST',
						dataType:'json',
						success:function(data){
							if(data.error){
								Lobibox.notify('warning', {
									title: 'แจ้งเตือน',
									size: 'mini',
									closeOnClick: false,
									delay: 5000,
									pauseDelayOnHover: true,
									continueDelayOnInactiveTab: false,
									icon: true,
									messageHeight: '90vh',
									msg: data.msg
								});
							}
							else if(data.stat){
								Lobibox.notify('success', {
									title: 'สำเร็จ',
									size: 'mini',
									closeOnClick: false,
									delay: 8000,
									pauseDelayOnHover: true,
									continueDelayOnInactiveTab: false,
									icon: true,
									messageHeight: '90vh',
									//soundPath: $("#maincontents").attr("baseurl")+'public/lobibox-master/sounds/',   // The folder path where sounds are located
									soundExt: '.ogg',
									msg: data.msg
								});
								
								$('.tab1').show();
								$('.tab2').hide();
								
								$('#tab2del').show();
								searchsn();
							}else{
								Lobibox.notify('error', {
									title: 'แจ้งเตือน',
									size: 'mini',
									closeOnClick: true,
									delay: 5000,
									pauseDelayOnHover: true,
									continueDelayOnInactiveTab: false,
									icon: true,
									messageHeight: '90vh',
									//soundPath: $("#maincontents").attr("baseurl")+'public/lobibox-master/sounds/',   // The folder path where sounds are located
									soundExt: '.ogg',
									msg: data.msg
								});
							}
						}
					});
				}else{
					Lobibox.notify('error', {
						title: 'แจ้งเตือน',
						size: 'mini',
						closeOnClick: true,
						delay: 5000,
						pauseDelayOnHover: true,
						continueDelayOnInactiveTab: false,
						icon: true,
						messageHeight: '90vh',
						soundPath: $("#maincontents").attr("baseurl")+'public/lobibox-master/sounds/',   // The folder path where sounds are located
						soundExt: '.ogg',
						msg: 'ยังไม่บันทึกรายการ'
					});
				}
			}
		});
	});
	$('#tab2del').attr('action','del');
	$('#tab2del').click(function(){
		Lobibox.confirm({
			title: 'ยืนยันการทำรายการ',
			iconClass: false,
			msg: "คุณต้องการลบ ?",
			buttons: {
				ok : {
					'class': 'btn btn-primary',
					text: 'ยืนยัน',
					closeOnClick: true,
				},
				cancel : {
					'class': 'btn btn-danger',
					text: 'ยกเลิก',
					closeOnClick: true
				},
			},
			callback: function(lobibox, type){
				var btnType;
				if (type === 'ok'){
					dataToPost = new Object();
					dataToPost.sircod = $('#t2sircod').val();
					dataToPost.sirnam = $('#t2sirnam').val();
					dataToPost.action = $('#tab2del').attr('action');
					
					$.ajax({
						url:'../SYS04/CUSTOMERS/groupDel',
						data:dataToPost,
						type:'POST',
						dataType:'json',
						success:function(data){
							if(data.stat){
								Lobibox.notify('success', {
									title: 'สำเร็จ',
									size: 'mini',
									closeOnClick: false,
									delay: 8000,
									pauseDelayOnHover: true,
									continueDelayOnInactiveTab: false,
									icon: true,
									messageHeight: '90vh',
									soundPath: $("#maincontents").attr("baseurl")+'public/lobibox-master/sounds/',   // The folder path where sounds are located
									soundExt: '.ogg',
									msg: data.msg
								});
								
								$('.tab1').show();
								$('.tab2').hide();
								
								searchsn();
							}else{
								Lobibox.notify('error', {
									title: 'แจ้งเตือน',
									size: 'mini',
									closeOnClick: true,
									delay: 5000,
									pauseDelayOnHover: true,
									continueDelayOnInactiveTab: false,
									icon: true,
									messageHeight: '90vh',
									soundPath: $("#maincontents").attr("baseurl")+'public/lobibox-master/sounds/',   // The folder path where sounds are located
									soundExt: '.ogg',
									msg: data.msg
								});
							}
						}
					});
				}else{
					Lobibox.notify('error', {
						title: 'แจ้งเตือน',
						size: 'mini',
						closeOnClick: true,
						delay: 5000,
						pauseDelayOnHover: true,
						continueDelayOnInactiveTab: false,
						icon: true,
						messageHeight: '90vh',
						//soundPath: $("#maincontents").attr("baseurl")+'public/lobibox-master/sounds/',   // The folder path where sounds are located
						//soundExt: '.ogg',
						msg: 'ยังไม่บันทึกรายการ'
					});
				}
			}
		});
	});
}


$('#search_groupcm').click(function(){
	searchcm();
});
CT_Search = null;
function searchcm(){
	$('#loadding').fadeIn(200);
	dataToPost = new Object();
	dataToPost.cuscod  = $('#cuscod').val();
	dataToPost.surname = $('#surname').val();
	dataToPost.address = $('#address').val();
	
	var spinner = $('body>.spinner').clone().removeClass('hide');
	$('#setgroupResult').html('');
	$('#setgroupResult').append(spinner);
	CT_Search = $.ajax({
		url: '../SYS04/CUSTOMERS/groupSearchcm',
		data:dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			if(data.error){
				Lobibox.notify('error', {
					title: 'แจ้งเตือน',
					size: 'mini',
					closeOnClick: false,
					delay: 5000,
					pauseDelayOnHover: true,
					continueDelayOnInactiveTab: false,
					icon: true,
					messageHeight: '90vh',
					msg: data.msg
				});
			}
			$('#loadding').fadeOut(300);
			$('#setgroupResult').find('.spinner, .spinner-backdrop').remove();
			$('#setgroupResult').html(data.html);
			
			fn_datatables('data-table-example2',1,360,'NO');
			
			afterSearchcm();
			
			//$('#tbScroll').on('draw.dt',function(){ afterSearchcm(); });
			
			//$('.btnDetail').unbind('click');
			$('.btnDetail').click(function(){
				fn_load_formeditcm($(this),'edit');
			});
			CT_Search = null;
		},
		beforeSend: function(){
			if(CT_Search !== null){
				CT_Search.abort();
			}
		}
	});
}
function afterSearchcm(){
	$('.getit').hover(function(){
		$(this).css({'background-color':'#c7c7ff'});
		$('.trow[seq='+$(this).attr('seq')+']').css({'background-color':'#c7ffff'});
	},function(){
		$(this).css({'background-color':''});
		$('.trow[seq='+$(this).attr('seq')+']').css({'background-color':''});
	});
}
function showAddrcm(){
	$('.btnshow_Addr').click(function(){
		dataToPost = new Object();
		dataToPost.CUSCOD = $(this).attr('CUSCOD');
		$('#loadding').fadeIn(500);
		$.ajax({
			url: '../SYS04/CUSTOMERS/groupShowca',
			data: dataToPost,
			type: 'POST',
			dataType: 'json',
			success: function(data){
				$('#loadding').fadeOut(100);
				Lobibox.window({
					title: 'SHOW ADDRESS',
					content:data.html,
					width: 1100,
					height: 300,
					shown:function($this){
						
					}
				});
			}
		});
	});
}
$("#add_custmast").click(function(){
	fn_load_formaddcm($(this),'add');
});
function fn_load_formaddcm($this,$event){
	dataToPost = new Object();
	dataToPost.CUSCOD = (typeof $this.attr('CUSCOD') === 'undefined' ? '':$this.attr('CUSCOD'));
	dataToPost.EVENT = $event;
	$('#loadding').fadeIn(250);
	$.ajax({
		url: '../SYS04/CUSTOMERS/groupGetFromCM',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			Lobibox.window({
				title:'Form CUSTOMER',
				width: $(window).width(),                
				height: $(window).height(),
				content: data.html,
				draggable: false,
				closeOnEsc: false,
				shown: function($this){
					//$this.destroy();
					
					$('#add_save').attr('action','add');
					$('#add_update').attr('action','edit');
					
					//$('#btn_Delete').attr('disabled',false);
					
					fn_loadPropoties($this)
					$('#loadding').fadeOut(100);
					
					fn_reactive_addr();
				}
			});
		}
	});
}
function fn_load_formeditcm($this,$event){
	dataToPost = new Object();
	dataToPost.CUSCOD = (typeof $this.attr('CUSCOD') === 'undefined' ? '':$this.attr('CUSCOD'));
	dataToPost.EVENT = $event;
	$('#loadding').fadeIn(250);
	$.ajax({
		url: '../SYS04/CUSTOMERS/groupGetFromCM',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			Lobibox.window({
				title:'Form CUSTOMER',
				width: $(window).width(),                
				height: $(window).height(),
				content: data.html,
				draggable: false,
				closeOnEsc: false,
				shown: function($this){
					//$this.destroy();
					/*
					$('#add_save').attr('action','add');
					$('#add_update').attr('action','edit');
					*/
					
					if(_insert == 'T'){
						$('#add_save').attr('disabled',false);	
					}else{
						$('#add_save').attr('disabled',true);	
					}
					if(_delete == 'T'){
						$('#btn_Delete').attr('disabled',false);	
					}else{
						$('#btn_Delete').attr('disabled',true);	
					}
					if(_update == 'T'){
						$('#add_update').attr('disabled',false);	
					}else{
						$('#add_update').attr('disabled',true);	
					}
					if(_insert == 'T'){
						$('#btnAddAddressFirst').attr('disabled',false);
					}else{
						$('#btnAddAddressFirst').attr('disabled',true);
					}
					if(_update == 'T'){
						$('#btnEditAddrTable').attr('disabled',false);
					}else{
						$('#btnEditAddrTable').attr('disabled',true);
					}
					if(_delete == 'T'){
						$('#btnDelAddrTable').attr('disabled',false);
					}else{
						$('#btnDelAddrTable').attr('disabled',true);
					}
					
					fn_loadPropoties($this)
					$('#loadding').fadeOut(100);
					
					fn_reactive_addr();
				}
			});
		}
	});
}
function fn_loadPropoties($window){
	var Age = null;
	$('#BIRTHDT').change(function(){
		dataToPost = new Object();
		dataToPost.BIRTHDT = $('#BIRTHDT').val();
		//alert(dataToPost.BIRTHDT);
		Age = $.ajax({
			url: '../SYS04/CUSTOMERS/getAge', 
			data: dataToPost,
			type: 'POST',
			dataType: 'json',
			success: function(data){
				$('#AGE').val(data.getdate);
				Age = null;
			},
			beforeSend: function(){
				if(Age !== null){
					Age.abort();
				}
			}
		});
	});
	$('#add_contno').val('Auto Genarate');
	$('#add_contno').attr('readonly',true);
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
		dropdownParent: (_level == 1 ? $("#wizard-sell") : true),
		disabled: (_level == 1 ? false : true),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	$('#GROUP1').select2({
		placeholder: 'เลือก',
		ajax: {
			url: '../Cselect2K/getGROUP1',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now  = (typeof $('#GROUP1').find(':selected').val() === 'undefined' ? "":$('#GROUP1').find(':selected').val()); 
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
		dropdownParent: $('#GROUP1').parent().parent(),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	$('#GRADE').select2({
		placeholder: 'เลือก',
		ajax: {
			url: '../Cselect2K/getGRADE',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now  = (typeof $('#GRADE').find(':selected').val() === 'undefined' ? "":$('#GRADE').find(':selected').val()); 
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
		dropdownParent: $('#GRADE').parent().parent(),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	$('#SNAM').select2({
		placeholder: 'เลือก',
		ajax: {
			url: '../Cselect2K/getSNAM',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now  = (typeof $('#SNAM').find(':selected').val() === 'undefined' ? "":$('#SNAM').find(':selected').val()); 
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
		dropdownParent: $('#SNAM').parent().parent(),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	$("#IDCARD").select2({
        placeholder: 'เลือก',		
        minimumResultsForSearch: -1,
        dropdownParent: $("#IDCARD").parent().parent(),
        width: '100%'
    });
	$("#NATION").select2({
        placeholder: 'เลิอก',		
        minimumResultsForSearch: -1,
        dropdownParent: $("#NATION").parent().parent(),
        width: '100%'
    });
	
	$("#addrno1").select2({
        placeholder: 'เลิอก',		
        minimumResultsForSearch: -1,
        dropdownParent: $("#addrno1").parent().parent(),
        width: '100%'
    });
    $("#addrno2").select2({
        placeholder: 'เลิอก',		
        minimumResultsForSearch: -1,
        dropdownParent: $("#addrno2").parent().parent(),
        width: '100%'
    });
    $("#addrno3").select2({
        placeholder: 'เลิอก',		
        minimumResultsForSearch: -1,
        dropdownParent: $('#addrno3').parent().parent(),
        width: '100%'
    });
	
	$('#btnAddAddressFirst').click(function(){
		fn_loadFromADDR('add',null);
	});
	
	$('#add_save').click(function(){
		Lobibox.confirm({
			title: 'ยืนยันการทำรายการ',
			iconClass: false,
			msg: "คุณต้องการบันทึก ?",
			buttons: {
				ok : {
					'class': 'btn btn-primary',
					text: 'ยืนยัน',
					closeOnClick: true,
				},
				cancel : {
					'class': 'btn btn-danger',
					text: 'ยกเลิก',
					closeOnClick: true
				},
			},
			callback: function(lobibox, type){
				var btnType;
				if (type === 'ok'){
					fn_save($window);
				}else{
					Lobibox.notify('error', {
						title: 'แจ้งเตือน',
						size: 'mini',
						closeOnClick: true,
						delay: 5000,
						pauseDelayOnHover: true,
						continueDelayOnInactiveTab: false,
						icon: true,
						messageHeight: '90vh',
						//soundPath: $("#maincontents").attr("baseurl")+'public/lobibox-master/sounds/',   // The folder path where sounds are located
						//soundExt: '.ogg',
						msg: 'ยังไม่บันทึกรายการ'
					});
				}
			}
		});
	});
	$('#add_update').click(function(){
		Lobibox.confirm({
			title: 'ยืนยันการทำรายการ',
			iconClass: false,
			msg: "คุณต้องการบันทึก ?",
			buttons: {
				ok : {
					'class': 'btn btn-primary',
					text: 'ยืนยัน',
					closeOnClick: true,
				},
				cancel : {
					'class': 'btn btn-danger',
					text: 'ยกเลิก',
					closeOnClick: true
				},
			},
			callback: function(lobibox, type){
				var btnType;
				if (type === 'ok'){
					fn_update($window);
				}else{
					Lobibox.notify('error', {
						title: 'แจ้งเตือน',
						size: 'mini',
						closeOnClick: true,
						delay: 5000,
						pauseDelayOnHover: true,
						continueDelayOnInactiveTab: false,
						icon: true,
						messageHeight: '90vh',
						//soundPath: $("#maincontents").attr("baseurl")+'public/lobibox-master/sounds/',   // The folder path where sounds are located
						//soundExt: '.ogg',
						msg: 'ยังไม่บันทึกรายการ'
					});
				}
			}
		});
	});
	$('#btn_Delete').click(function(){
		Lobibox.confirm({
			title: 'ยืนยันการทำรายการ',
			iconClass: false,
			msg: '<span style="color:red;font-size:18pt">คุณแน่ในหรือไม่ว่าต้องการลบประวัติลูกค้ารหัส <span><br>'+$("#CUSCOD").val()+' ?',
			buttons: {
				ok : {
					'class': 'btn btn-primary',
					text: 'ยืนยัน',
					closeOnClick: true,
				},
				cancel : {
					'class': 'btn btn-danger',
					text: 'ยกเลิก',
					closeOnClick: true
				},
			},
			callback: function(lobibox, type){
				var btnType;
				if (type === 'ok'){
					fn_delete($window);
				}else{
					Lobibox.notify('error', {
						title: 'แจ้งเตือน',
						size: 'mini',
						closeOnClick: true,
						delay: 5000,
						pauseDelayOnHover: true,
						continueDelayOnInactiveTab: false,
						icon: true,
						messageHeight: '90vh',
						//soundPath: $("#maincontents").attr("baseurl")+'public/lobibox-master/sounds/',   // The folder path where sounds are located
						//soundExt: '.ogg',
						msg: 'ยังไม่บันทึกรายการ'
					});
				}
			}
		});
	});
}
var OBJeditaddress = null;
function fn_loadFromADDR($action,$this){ 
	if($action == "edit"){ 						
		dataToPost = new Object();			//---------->		---------      --from แก้ไขfrom
		dataToPost.ADDRNO 	= $this.attr("ADDRNO");
		dataToPost.ADDR1 	= $this.attr("ADDR1");
		dataToPost.SWIN		= $this.attr("SWIN");
		dataToPost.SOI      = $this.attr("SOI");
		dataToPost.ADDR2 	= $this.attr("ADDR2");
		dataToPost.MOOBAN 	= $this.attr("MOOBAN");
		dataToPost.TUMB 	= $this.attr("TUMB");
		dataToPost.AUMPCOD 	= $this.attr("AUMPCOD");
		dataToPost.PROVCOD 	= $this.attr("PROVCOD");
		dataToPost.AUMPDES 	= $this.attr("AUMPDES");
		dataToPost.PROVDES 	= $this.attr("PROVDES");
		dataToPost.ZIP      = $this.attr("ZIP");
		dataToPost.TELP 	= $this.attr("TELP");
		dataToPost.MEMO1 	= $this.attr("MEMO1");	
	}else{
		dataToPost = new Object();			//---------->		---------      --form เพิ่ม from
	}
	
	dataToPost.ACTION = $action;
	
	$('#loadding').fadeIn(250);
	OBJeditaddress = $.ajax({
		url: '../SYS04/CUSTOMERS/getFormAddressCM', 
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			Lobibox.window({
				title: 'FORM ADDRESS',
				content:data.html,
				closeButton: false,
				shown:function($thiswindow){
					$('#loadding').fadeOut(100);
					fn_loadPropotiesAddr($thiswindow,$action,$this); //การกระทำในฟอร์มLobiwindow 
				},
				beforeClose : function(){
					if($action == "edit"){
						CloseLobiwindow($this,"cancel");
					}
				}
			});
			
			OBJeditaddress = null;
		},
		beforeSend: function(){
			if(OBJeditaddress !== null){
				OBJeditaddress.abort();
			}
		}
	});	
}

function fn_loadPropotiesAddr($window,$action,$this){
	$('#AUMPCOD').select2({        //อำเภอ
        placeholder: 'เลือก',
        ajax: {
            url: '../Cselect2K/getAUMPCOD',
            data: function (params) {
                dataToPost = new Object();
                dataToPost.now = (typeof $('#AUMPCOD').find(':selected').val() === 'undefined' ? "":$('#AUMPCOD').find(':selected').val());
                dataToPost.q = (typeof params.term === 'undefined' ? '' : params.term);
                dataToPost.provcod = (typeof $('#PROVCOD').find(':selected').val() === 'undefined' ? "":$('#PROVCOD').find(':selected').val()); //จังหวัด

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
        dropdownParent: $('#AUMPCOD').parent().parent(),
        //disabled: true,
        //theme: 'classic',
        width: '100%'
    });
    
    $('#PROVCOD').select2({      //จัดหวัด
        placeholder: 'เลือก',
        ajax: {
            url: '../Cselect2K/getPROVCOD',
            data: function (params) {
                dataToPost = new Object();
                dataToPost.now = (typeof $('#PROVCOD').find(':selected').val() === 'undefined' ? "":$('#PROVCOD').find(':selected').val()); 
                dataToPost.q = (typeof params.term === 'undefined' ? '' : params.term);
                dataToPost.aumpcod = (typeof $('#AUMPCOD').find(':selected').val() === 'undefined' ? "":$('#AUMPCOD').find(':selected').val()); //อำเภอ
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
        dropdownParent: $('#PROVCOD').parent().parent(),
        //disabled: true,
        //theme: 'classic',
        width: '100%'
    });
    
    $('#ZIP').select2({        //รหัสไปรษณีย์
        placeholder: 'เลือก',
        ajax: {
            url: '../Cselect2K/getZIP',
            data: function (params) {
                dataToPost = new Object();
                dataToPost.now = (typeof $('#ZIP').find(':selected').val() === 'undefined' ? "":$('#ZIP').find(':selected').val());
                dataToPost.q = (typeof params.term === 'undefined' ? '' : params.term);
                dataToPost.provcod = (typeof $('#PROVCOD').find(':selected').val() === 'undefined' ? "":$('#PROVCOD').find(':selected').val()); //จังหวัด

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
        dropdownParent: $('#ZIP').parent().parent(),
        //disabled: true,
        //theme: 'classic',
        width: '100%'
    });

    $('#PROVCOD').on("select2:select",function(){ //เลือกอำเภอโชว์จังหวัด
        $('#AUMPCOD').val(null).trigger('change');
    });
    
    var JDjaump = null;
    $('#AUMPCOD').on("select2:select",function(){
        dataToPost = new Object();
        dataToPost.aumpcod = (typeof $('#AUMPCOD').find(":selected").val() === "undefined" ? "":$('#AUMPCOD').find(":selected").val());
        JDjaump = $.ajax({
            url: '../Cselect2K/getProv',
            data: dataToPost,
            type: "POST",
            dataType: "json",
            success: function(data) {
                var newOption = new Option(data.PROVDES, data.PROVCOD, false, false);
                $('#PROVCOD').empty().append(newOption).trigger('change');
            },
            beforeSend: function(){
                if(JDjaump != null){
                    JDjaump.abort();
                }
            }
        });
    });
    
    $('#ZIP').on("select2:select",function(){ //เลือกอำเภอโชว์รหัสไปรษณีย์
        $('#AUMPCOD').val(null).trigger('change');
    });
    
    var Zip = null;
    $('#AUMPCOD').on("select2:select",function(){
        dataToPost = new Object();
        dataToPost.aumpcod1 = (typeof $('#AUMPCOD').find(":selected").val() === "undefined" ? "":$('#AUMPCOD').find(":selected").val());

        Zip = $.ajax({
            url: '../Cselect2K/getZipshow',
            data: dataToPost,
            type: "POST",
            dataType: "json",
            success: function(data) {
                var newOption = new Option(data.AUMPCOD, data.PROVCOD, false, false);
                $('#ZIP').empty().append(newOption).trigger('change');
            },
            beforeSend: function(){
                if(Zip != null){
                    Zip.abort();
                }
            }
        });
    });
	
	
/**********************************************************************************************************************/	
	
	var OBJbtnAddAddr = null;
	$('#btnAddTableHtml').click(function(){		//เพิ่ม
        dataToPost = new Object();
        dataToPost.CUSCOD   = $("#CUSCOD").val();
        dataToPost.ADDRNO 	= $("#ADDRNO").val();
        dataToPost.ADDR1 	= $("#ADDR1").val();
		dataToPost.SWIN		= $("#SWIN").val();
        dataToPost.SOI      = $("#SOI").val();
        dataToPost.ADDR2 	= $("#ADDR2").val();
        dataToPost.MOOBAN 	= $("#MOOBAN").val();
        dataToPost.TUMB 	= $("#TUMB").val();
        dataToPost.AUMPCOD 	= (typeof $("#AUMPCOD").find(":selected").val()     === "undefined" ? "": $("#AUMPCOD").find(":selected").val());
        dataToPost.PROVCOD 	= (typeof $("#PROVCOD").find(":selected").val()     === "undefined" ? "": $("#PROVCOD").find(":selected").val());
        dataToPost.AUMPDES 	= (typeof $("#AUMPCOD").find(":selected").text()    === "undefined" ? "": $("#AUMPCOD").find(":selected").text());
        dataToPost.PROVDES 	= (typeof $("#PROVCOD").find(":selected").text()    === "undefined" ? "": $("#PROVCOD").find(":selected").text());
        dataToPost.ZIP      = (typeof $("#ZIP").find(":selected").val()         === "undefined" ? "": $("#ZIP").find(":selected").val());
        dataToPost.TELP 	= $("#TELP").val();
        dataToPost.MEMO1 	= $("#MEMO1").val();

        OBJbtnAddAddr = $.ajax({
            url: '../SYS04/CUSTOMERS/SetAddr_TableHtml',			
            data: dataToPost,
            type: 'POST',
            dataType: 'json',
            success: function(data){
				if(data.error){
					Lobibox.notify('warning', {
						title: 'แจ้งเตือน',
						size: 'mini',
						closeOnClick: false,
						delay: 5000,
						pauseDelayOnHover: true,
						continueDelayOnInactiveTab: false,
						icon: true,
						messageHeight: '90vh',
						msg: data.msg
					});
				}else{
					$("#data-table-address tbody").append(data.tbody);

					fn_address("add");
					fn_reactive_addr(); // แก้ไขข้อมูลที่อยู่พนักงาน กรณีเพิ่มที่อยู่ใหม่
					
					$window.destroy();
				}
                OBJbtnAddAddr = null;
            },
            beforeSend: function(){
                if(OBJbtnAddAddr !== null){
                    OBJbtnAddAddr.abort();
                }
            }
        });
	});
	
	var OBJbtneditAdrr = null;
	$('#btneditTableHtml').click(function(){		//แก้ไข
		dataToPost = new Object();
        dataToPost.CUSCOD   = $("#CUSCOD").val();
        dataToPost.ADDRNO 	= $("#ADDRNO").val();
		dataToPost.SWIN 	= $("#SWIN").val();
        dataToPost.ADDR1 	= $("#ADDR1").val();
        dataToPost.SOI      = $("#SOI").val();
        dataToPost.ADDR2 	= $("#ADDR2").val();
        dataToPost.MOOBAN 	= $("#MOOBAN").val();
        dataToPost.TUMB 	= $("#TUMB").val();
        dataToPost.AUMPCOD 	= (typeof $("#AUMPCOD").find(":selected").val()     === "undefined" ? "": $("#AUMPCOD").find(":selected").val());
        dataToPost.PROVCOD 	= (typeof $("#PROVCOD").find(":selected").val()     === "undefined" ? "": $("#PROVCOD").find(":selected").val());
        dataToPost.AUMPDES 	= (typeof $("#AUMPCOD").find(":selected").text()    === "undefined" ? "": $("#AUMPCOD").find(":selected").text());
        dataToPost.PROVDES 	= (typeof $("#PROVCOD").find(":selected").text()    === "undefined" ? "": $("#PROVCOD").find(":selected").text());
        dataToPost.ZIP      = (typeof $("#ZIP").find(":selected").val()         === "undefined" ? "": $("#ZIP").find(":selected").val());
        dataToPost.TELP 	= $("#TELP").val();
        dataToPost.MEMO1 	= $("#MEMO1").val();

        OBJbtneditAdrr = $.ajax({
            url: '../SYS04/CUSTOMERS/SetAddr_TableHtml',			
            data: dataToPost,
            type: 'POST',
            dataType: 'json',
            success: function(data){				
                if(data.error){
					Lobibox.notify('warning', {
						title: 'แจ้งเตือน',
						size: 'mini',
						closeOnClick: false,
						delay: 5000,
						pauseDelayOnHover: true,
						continueDelayOnInactiveTab: false,
						icon: true,
						messageHeight: '90vh',
						msg: data.msg
					});
				}else{
					$("#data-table-address tbody").append(data.tbody);

					fn_address("add");
					fn_reactive_addr(); // แก้ไขข้อมูลที่อยู่พนักงาน กรณีเพิ่มที่อยู่ใหม่
					
					$window.destroy();
				}
				OBJbtneditAdrr = null;
            },
            beforeSend: function(){
                if(OBJbtneditAdrr !== null){
                    OBJbtneditAdrr.abort();
                }
            }
        });
	});
	
	$('#btnWACloseAdd').click(function(){
		$window.destroy();
	});
	var OBJbtnWAClose = null;			
	$("#btnWAClose").unbind('click');
	$("#btnWAClose").click(function(){
		if($action == "edit"){	
			dataToPost = new Object();
			dataToPost.ADDRNO 	= $this.attr("ADDRNO");
			dataToPost.ADDR1 	= $this.attr("ADDR1");
			dataToPost.SWIN		= $this.attr("SWIN");
			dataToPost.SOI     	= $this.attr("SOI");
			dataToPost.ADDR2 	= $this.attr("ADDR2");
			dataToPost.MOOBAN 	= $this.attr("MOOBAN");
			dataToPost.TUMB 	= $this.attr("TUMB");
			dataToPost.AUMPCOD 	= $this.attr("AUMPCOD");
			dataToPost.PROVCOD 	= $this.attr("PROVCOD");
			dataToPost.AUMPDES 	= $this.attr("AUMPDES");
			dataToPost.PROVDES 	= $this.attr("PROVDES");
			dataToPost.ZIP      = $this.attr("ZIP");
			dataToPost.TELP 	= $this.attr("TELP");
			dataToPost.MEMO1 	= $this.attr("MEMO1");
			
			OBJbtnWAClose = $.ajax({
				url: '../SYS04/CUSTOMERS/SetAddr_TableHtml_Cancel',		
				data: dataToPost,
				type: 'POST',
				dataType: 'json',
				success: function(data){
					if(!data.error){
						$("#data-table-address tbody").append(data.tbody);

						fn_address("edit");
						OBJbtnWAClose = null;
						$window.destroy();
						fn_reactive_addr();
					}
				},
				beforeSend: function(){
					if(OBJbtnWAClose !== null){
						OBJbtnWAClose.abort();
					}
				}
			});
		}
	});
}
function fn_reactive_addr(){
	OBJeditaddress = null;
	$('.btnEditAddrTable').unbind("click");
	$('.btnEditAddrTable').click(function(){  //แก้ไขที่อยู่ในตาราง html
		var btnthisedit = $(this); 	
		btnthisedit.parents('tr').remove();
		fn_loadFromADDR("edit",$(this)); //ฟอร์มกรอกที่อยู่ลูกค้า
	});
	$('.btnDelAddrTable').unbind("click");
	$('.btnDelAddrTable').click(function(){  //ลบที่อยู่ในตาราง html
		var btnthisdel = $(this);
		Lobibox.confirm({
			title: 'ยืนยันการทำรายการ',
			iconClass: false,
			msg: "คุณต้องการลบ ?",
			buttons: {
				ok : {
					'class': 'btn btn-primary',
					text: 'ยืนยัน',
					closeOnClick: true,
				},
				cancel : {
					'class': 'btn btn-danger',
					text: 'ยกเลิก',
					closeOnClick: true
				},
			},
			callback: function(lobibox, type){
				var btnType;
				if (type === 'ok'){
					btnthisdel.parents('tr').remove(); 
					fn_address();
				}
			}
		});
	});
}
function CloseLobiwindow(address_ae,event){
    var OBJbtncloseAddr = null;
    if(event != "cancel"){
        dataToPost = new Object();
        dataToPost.ADDRNO   = $("#ADDRNO").val();
        dataToPost.ADDR1    = $("#ADDR1").val();
		dataToPost.SWIN		= $("#SWIN").val();
        dataToPost.SOI      = $("#SOI").val();
        dataToPost.ADDR2    = $("#ADDR2").val();
        dataToPost.MOOBAN   = $("#MOOBAN").val();
        dataToPost.TUMB     = $("#TUMB").val();
        dataToPost.AUMPCOD  = (typeof $("#AUMPCOD").find(":selected").val()     === "undefined" ? "": $("#AUMPCOD").find(":selected").val());
        dataToPost.PROVCOD  = (typeof $("#PROVCOD").find(":selected").val()     === "undefined" ? "": $("#PROVCOD").find(":selected").val());
        dataToPost.AUMPDES  = (typeof $("#AUMPCOD").find(":selected").text()    === "undefined" ? "": $("#AUMPCOD").find(":selected").text());
        dataToPost.PROVDES  = (typeof $("#PROVCOD").find(":selected").text()    === "undefined" ? "": $("#PROVCOD").find(":selected").text());
        dataToPost.ZIP      = (typeof $("#ZIP").find(":selected").val()         === "undefined" ? "": $("#ZIP").find(":selected").val());
        dataToPost.TELP     = $("#TELP").val();
        dataToPost.MEMO1    = $("#MEMO1").val();
    }else{
        dataToPost = new Object();
        dataToPost.ADDRNO 	= address_ae.attr("ADDRNO");
        dataToPost.ADDR1 	= address_ae.attr("ADDR1");
		dataToPost.SWIN		= address_ae.attr("SWIN");
        dataToPost.SOI      = address_ae.attr("SOI");
        dataToPost.ADDR2 	= address_ae.attr("ADDR2");
        dataToPost.MOOBAN 	= address_ae.attr("MOOBAN");
        dataToPost.TUMB 	= address_ae.attr("TUMB");
        dataToPost.AUMPCOD 	= address_ae.attr("AUMPCOD");
        dataToPost.PROVCOD 	= address_ae.attr("PROVCOD");
        dataToPost.AUMPDES 	= address_ae.attr("AUMPDES");
        dataToPost.PROVDES 	= address_ae.attr("PROVDES");
        dataToPost.ZIP      = address_ae.attr("ZIP");
        dataToPost.TELP 	= address_ae.attr("TELP");
        dataToPost.MEMO1 	= address_ae.attr("MEMO1");
    }
    OBJbtncloseAddr = $.ajax({
        url: '../SYS04/CUSTOMERS/SetAddr_TableHtml',			
        data: dataToPost,
        type: 'POST',
        dataType: 'json',
        success: function(data){
            if(!data.error){
				fn_reactive_addr(); // แก้ไขข้อมูลที่อยู่พนักงาน กรณีเพิ่มที่อยู่ใหม่
                fn_address("edit");
                OBJbtncloseAddr = null;
            }
        },
        beforeSend: function(){
            if(OBJbtncloseAddr !== null){
                OBJbtncloseAddr.abort();
            }
        }
    });
}
function fn_address($action){
    $('#addrno1').empty().trigger('change');
    $('#addrno2').empty().trigger('change');
    $('#addrno3').empty().trigger('change');
    
    $(".btnEditAddrTable").each(function(){
        var newOption = new Option($(this).attr("ADDRNO"), $(this).attr("ADDRNO"), false, false);
        $('#addrno1').append(newOption).trigger('change');
        var newOption = new Option($(this).attr("ADDRNO"), $(this).attr("ADDRNO"), false, false);
        $('#addrno2').append(newOption).trigger('change');
        var newOption = new Option($(this).attr("ADDRNO"), $(this).attr("ADDRNO"), false, false);
        $('#addrno3').append(newOption).trigger('change');
    });
}
var KB_fn_save = null;
function fn_save($window){
	dataToPost = new Object();
	dataToPost.locat	= (typeof $('#add_locat').find(':selected').val() === 'undefined' ? '':$('#add_locat').find(':selected').val());
	dataToPost.CUSCOD   = $("#CUSCOD").val();
	dataToPost.GROUP1   = (typeof $('#GROUP1').find(':selected').val() === 'undefined' ? '':$('#GROUP1').find(':selected').val());
	dataToPost.GRADE    = (typeof $('#GRADE').find(':selected').val() === 'undefined' ? '':$('#GRADE').find(':selected').val());
	dataToPost.SNAM     = (typeof $('#SNAM').find(':selected').val() === 'undefined' ? '':$('#SNAM').find(':selected').val());
	dataToPost.NAME1    = $("#NAME1").val();
	dataToPost.NAME2    = $("#NAME2").val();
	dataToPost.NICKNM   = $("#NICKNM").val();
	dataToPost.BIRTHDT  = $("#BIRTHDT").val();
	dataToPost.ADDRNO	= $("#addrno1").val();
	dataToPost.IDCARD   = $("#IDCARD").val();
	dataToPost.IDNO     = $("#IDNO").val();
	dataToPost.ISSUBY   = $("#ISSUBY").val();
	dataToPost.ISSUDT   = $("#ISSUDT").val();
	dataToPost.EXPDT    = $("#EXPDT").val();
	dataToPost.AGE      = $("#AGE").val();
	dataToPost.NATION   = $("#NATION").val();
	dataToPost.OCCUP    = $("#OCCUP").val();
	dataToPost.OFFIC    = $("#OFFIC").val();
	dataToPost.MAXCRED  = $("#MAXCRED").val();
	dataToPost.MREVENU  = $("#MREVENU").val();
	dataToPost.YREVENU  = $("#YREVENU").val();
	dataToPost.MOBILENO = $("#MOBILENO").val();
	dataToPost.EMAIL1   = $("#EMAIL1").val();
	dataToPost.ADDRNO2  = $("#addrno2").val();
	dataToPost.ADDRNO3  = $("#addrno3").val();
	dataToPost.MEMOADD  = $("#MEMOADD").val();
	
	dataToPost.action = $('#add_save').attr('action');
	
	var ad = [];
	$(".btnEditAddrTable").each(function(){
		var adr =[];   
		adr.push($(this).attr('ADDRNO'));
		adr.push($(this).attr('ADDR1'));            
		adr.push($(this).attr('SOI'));
		adr.push($(this).attr('ADDR2'));
		adr.push($(this).attr('MOOBAN'));
		adr.push($(this).attr('TUMB'));
		adr.push($(this).attr('AUMPCOD'));
		adr.push($(this).attr('PROVCOD'));
		adr.push($(this).attr('ZIP'));
		adr.push($(this).attr('TELP'));
		adr.push($(this).attr('MEMO1'));
		adr.push($(this).attr('SWIN'));
		
		ad.push(adr);
	});
	$('#loadding').fadeIn(100);
	dataToPost.ADDR  = ad; 
	KB_fn_save = $.ajax({
		url:'../SYS04/CUSTOMERS/save',
		data:dataToPost,
		type:"POST",
		dataType: "json",
		success: function(data){
			if(data.error){
				Lobibox.notify('warning', {
					title: 'แจ้งเตือน',
					size: 'mini',
					closeOnClick: false,
					delay: 5000,
					pauseDelayOnHover: true,
					continueDelayOnInactiveTab: false,
					icon: true,
					messageHeight: '90vh',
					msg: data.msg
				});
			}
			else if(data.tablehtml == "K"){
				Lobibox.notify('warning', {
					title: 'แจ้งเตือน',
					size: 'mini',
					closeOnClick: false,
					delay: 5000,
					pauseDelayOnHover: true,
					continueDelayOnInactiveTab: false,
					icon: true,
					messageHeight: '90vh',
					msg: "คุณยังไม่ได้เพิ่มที่อยู่เลย กรุณาเพิ่มที่อยู่ด้วยครับ"
				});
			}
			else if(data.stat){
				Lobibox.notify('success', {
					title: 'สำเร็จ',
					size: 'mini',
					closeOnClick: false,
					delay: 8000,
					pauseDelayOnHover: true,
					continueDelayOnInactiveTab: false,
					icon: true,
					messageHeight: '90vh',
					//soundPath: $("#maincontents").attr("baseurl")+'public/lobibox-master/sounds/',   // The folder path where sounds are located
					//soundExt: '.ogg',
					msg: data.msg
				});
				
				$window.destroy();
				searchcm();
			}else{
				Lobibox.notify('error', {
					title: 'แจ้งเตือน',
					size: 'mini',
					closeOnClick: true,
					delay: 5000,
					pauseDelayOnHover: true,
					continueDelayOnInactiveTab: false,
					icon: true,
					messageHeight: '90vh',
					//soundPath: $("#maincontents").attr("baseurl")+'public/lobibox-master/sounds/',   // The folder path where sounds are located
					//soundExt: '.ogg',
					msg: data.msg
				});
			}
			KB_fn_save = null;
			$('#loadding').fadeOut(100);
		},
		beforeSend: function(){
			if(KB_fn_save !== null){
				KB_fn_save.abort();
			}
		}
	});
}
var KB_fn_update = null;
function fn_update($window){
	dataToPost = new Object();
	dataToPost.locat	= (typeof $('#add_locat').find(':selected').val() === 'undefined' ? '':$('#add_locat').find(':selected').val());
	dataToPost.CUSCOD   = $("#CUSCOD").val();
	dataToPost.GROUP1   = (typeof $('#GROUP1').find(':selected').val() === 'undefined' ? '':$('#GROUP1').find(':selected').val());
	dataToPost.GRADE    = (typeof $('#GRADE').find(':selected').val() === 'undefined' ? '':$('#GRADE').find(':selected').val());
	dataToPost.SNAM     = (typeof $('#SNAM').find(':selected').val() === 'undefined' ? '':$('#SNAM').find(':selected').val());
	dataToPost.NAME1    = $("#NAME1").val();
	dataToPost.NAME2    = $("#NAME2").val();
	dataToPost.NICKNM   = $("#NICKNM").val();
	dataToPost.BIRTHDT  = $("#BIRTHDT").val();
	dataToPost.ADDRNO	= $("#addrno1").val();
	dataToPost.IDCARD   = $("#IDCARD").val();
	dataToPost.IDNO     = $("#IDNO").val();
	dataToPost.ISSUBY   = $("#ISSUBY").val();
	dataToPost.ISSUDT   = $("#ISSUDT").val();
	dataToPost.EXPDT    = $("#EXPDT").val();
	dataToPost.AGE      = $("#AGE").val();
	dataToPost.NATION   = $("#NATION").val();
	dataToPost.OCCUP    = $("#OCCUP").val();
	dataToPost.OFFIC    = $("#OFFIC").val();
	dataToPost.MAXCRED  = $("#MAXCRED").val();
	dataToPost.MREVENU  = $("#MREVENU").val();
	dataToPost.YREVENU  = $("#YREVENU").val();
	dataToPost.MOBILENO = $("#MOBILENO").val();
	dataToPost.EMAIL1   = $("#EMAIL1").val();
	dataToPost.ADDRNO2  = $("#addrno2").val();
	dataToPost.ADDRNO3  = $("#addrno3").val();
	dataToPost.MEMOADD  = $("#MEMOADD").val();
	
	dataToPost.action   = $("#MEMOADD").val();
	
	var ad = [];
	$(".btnEditAddrTable").each(function(){
		var adr =[];   
		adr.push($(this).attr('ADDRNO'));
		adr.push($(this).attr('ADDR1'));            
		adr.push($(this).attr('SOI'));
		adr.push($(this).attr('ADDR2'));
		adr.push($(this).attr('MOOBAN'));
		adr.push($(this).attr('TUMB'));
		adr.push($(this).attr('AUMPCOD'));
		adr.push($(this).attr('PROVCOD'));
		adr.push($(this).attr('ZIP'));
		adr.push($(this).attr('TELP'));
		adr.push($(this).attr('MEMO1'));
		adr.push($(this).attr('SWIN'));
		ad.push(adr);
	});
	$('#loadding').fadeIn(100);
	dataToPost.ADDR  = ad; 
	KB_fn_update = $.ajax({
		url:'../SYS04/CUSTOMERS/save',
		data:dataToPost,
		type:"POST",
		dataType: "json",
		success: function(data){
			if(data.error){
				Lobibox.notify('warning', {
					title: 'แจ้งเตือน',
					size: 'mini',
					closeOnClick: false,
					delay: 5000,
					pauseDelayOnHover: true,
					continueDelayOnInactiveTab: false,
					icon: true,
					messageHeight: '90vh',
					msg: data.msg
				});
			}
			else if(data.tablehtml == "K"){
				Lobibox.notify('warning', {
					title: 'แจ้งเตือน',
					size: 'mini',
					closeOnClick: false,
					delay: 5000,
					pauseDelayOnHover: true,
					continueDelayOnInactiveTab: false,
					icon: true,
					messageHeight: '90vh',
					msg: "คุณยังไม่ได้เพิ่มที่อยู่เลย กรุณาเพิ่มที่อยู่ด้วยครับ"
				});
			}
			else if(data.stat){
				Lobibox.notify('success', {
					title: 'สำเร็จ',
					size: 'mini',
					closeOnClick: false,
					delay: 8000,
					pauseDelayOnHover: true,
					continueDelayOnInactiveTab: false,
					icon: true,
					messageHeight: '90vh',
					//soundPath: $("#maincontents").attr("baseurl")+'public/lobibox-master/sounds/',   // The folder path where sounds are located
					//soundExt: '.ogg',
					msg: data.msg
				});
				$window.destroy();
				searchcm(); 
			}else{
				Lobibox.notify('error', {
					title: 'แจ้งเตือน',
					size: 'mini',
					closeOnClick: true,
					delay: 5000,
					pauseDelayOnHover: true,
					continueDelayOnInactiveTab: false,
					icon: true,
					messageHeight: '90vh',
					//soundPath: $("#maincontents").attr("baseurl")+'public/lobibox-master/sounds/',   // The folder path where sounds are located
					//soundExt: '.ogg',
					msg: data.msg
				});
			}
			KB_fn_update = null;
			$('#loadding').fadeOut(100);
		},
		beforeSend: function(){
			if(KB_fn_update !== null){
				KB_fn_update.abort();
			}
		}
	});
}
var KB_fn_delete = null;
var korn = $(this);
function fn_delete($window){
	dataToPost = new Object();
	dataToPost.CUSCOD   = $("#CUSCOD").val();
	$('#loadding').fadeIn(100);
	KB_fn_delete = $.ajax({
		url:'../SYS04/CUSTOMERS/DeletedCUSCOD',
		data:dataToPost,
		type:"POST",
		dataType: "json",
		success: function(data){
			if(data.error){
				Lobibox.notify('warning', {
					title: 'แจ้งเตือน',
					size: 'mini',
					closeOnClick: false,
					delay: 5000,
					pauseDelayOnHover: true,
					continueDelayOnInactiveTab: false,
					icon: true,
					messageHeight: '90vh',
					msg: data.msg
				});
			}
			else if(data.stat){
				Lobibox.notify('success', {
					title: 'สำเร็จ',
					size: 'mini',
					closeOnClick: false,
					delay: 8000,
					pauseDelayOnHover: true,
					continueDelayOnInactiveTab: false,
					icon: true,
					messageHeight: '90vh',
					//soundPath: $("#maincontents").attr("baseurl")+'public/lobibox-master/sounds/',   // The folder path where sounds are located
					//soundExt: '.ogg',
					msg: data.msg
				});
				$window.destroy();
				searchcm();
				//location.reload();
				
			}else{
				Lobibox.notify('error', {
					title: 'แจ้งเตือน',
					size: 'mini',
					closeOnClick: true,
					delay: 5000,
					pauseDelayOnHover: true,
					continueDelayOnInactiveTab: false,
					icon: true,
					messageHeight: '90vh',
					//soundPath: $("#maincontents").attr("baseurl")+'public/lobibox-master/sounds/',   // The folder path where sounds are located
					//soundExt: '.ogg',
					msg: data.msg
				});
			}
			KB_fn_delete = null;
			$('#loadding').fadeOut(100);
		},
		beforeSend: function(){
			if(KB_fn_delete !== null){
				KB_fn_delete.abort();
			}
		}
	});
}


