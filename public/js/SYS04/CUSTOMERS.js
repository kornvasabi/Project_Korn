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
	search();
});

function search(){
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
			//$('#t2gcode').attr('readonly',false);
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
								search();
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
								
								search();
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
						soundPath: $("#maincontents").attr("baseurl")+'public/lobibox-master/sounds/',   // The folder path where sounds are located
						soundExt: '.ogg',
						msg: 'ยังไม่บันทึกรายการ'
					});
				}
			}
		});
	});
}

