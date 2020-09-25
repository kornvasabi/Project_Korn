/********************************************************
             ______@07/09/2020______
            / / _ _   _ _     __ 
           / // __ \ / __ \ / __ \
       _ _/ // /_/ // / / // /_/ /
     /_ _ _/ \_ _ //_/ /_/ \__  /
                          _ _/ /
                         /___ /
********************************************************/

var _insert = $('.tab1[name="home"]').attr('cin');
var _update = $('.tab1[name="home"]').attr('cup');
var _delete = $('.tab1[name="home"]').attr('cdel');
var _level  = $('.tab1[name="home"]').attr('clev');
 
$(function(){
	if($('.tab1[name="home"]').attr('cin') == 'T'){
		$('#add_fin').attr('disabled',false);	
	}else{
		$('#add_fin').attr('disabled',true);	
	}
});

var jdsearch_gar = null;
$('#search_fin').click(function(){
	search();
});

function search(){
	dataToPost = new Object();
	dataToPost.fincode = $('#fincode').val();
	dataToPost.finname = $('#finname').val();
	
	$('#loadding').fadeIn(200);
	jdsearch_gar = $.ajax({
		url: '../setup/Company/FNCorpSearch',
		data:dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			$('#setFNCorpResult').html(data.html);
			afterSearch();
			jdsearch_gar = null;
			$('#loadding').fadeOut(200);
		},
		beforeSend: function(){ if(jdsearch_gar !== null){ jdsearch_gar.abort(); } },
		error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
	});
} 


function afterSearch(){
	document.getElementById("tbScroll").addEventListener("scroll", function(){
		var translate = "translate(0,"+(this.scrollTop - 1)+"px)";
		this.querySelector("thead").style.transform = translate;						
	});	
	
	$('.getit').hover(function(){
		$(this).css({'background-color':'yellow'});
		$('.trow[seq='+$(this).attr('seq')+']').css({'background-color':'#f9f9a9'});
	},function(){
		$(this).css({'background-color':''});
		$('.trow[seq='+$(this).attr('seq')+']').css({'background-color':''});
	});
	
	
	$('.getit').click(function(){
		dataToPost = new Object();
		dataToPost.FINCODE = $(this).attr('fincode');
		
		var spinner = $('body>.spinner').clone().removeClass('hide');
		$('#tab2_main').html('');
		$('#tab2_main').append(spinner);
		
		$('.tab1').hide();
		$('.tab2').show();
		
		$.ajax({
			url:'../setup/Company/FNCorpGetFormAE',
			data:dataToPost,
			type:'POST',
			dataType:'json',
			success:function(data){
				$('#tab2save').attr('action','edit');
				
				$('#tab2_main').find('.spinner, .spinner-backdrop').remove();
				$('#tab2_main').html(data.html);
				
				$('#t2FINCODE').attr('readonly',true);
				
				if($('.tab1[name="home"]').attr('cup') == 'T'){
					$('#tab2save').attr('disabled',false);	
				}else{
					$('#tab2save').attr('disabled',true);	
				}
				
				if($('.tab1[name="home"]').attr('cdel') == 'T'){
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
					var dataToPost = new Object();
					dataToPost.FINCODE = $('#t2FINCODE').val();
					dataToPost.FINNAME = $('#t2FINNAME').val();
					dataToPost.FINADDR1 = $('#t2FINADDR1').val();
					dataToPost.FINADDR2 = $('#t2FINADDR2').val();
					dataToPost.FINTELP = $('#t2FINTELP').val();
					dataToPost.MEMO1 = $('#t2MEMO1').val();
					
					dataToPost.action = $('#tab2save').attr('action');
					
					$.ajax({
						url:'../setup/Company/FNCorpSave',
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
					dataToPost.FINCODE = $('#t2FINCODE').val();
					dataToPost.FINNAME = $('#t2FINNAME').val();
					
					$.ajax({
						url:'../setup/Company/FNCorpDel',
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



var jdadd_group= null;
$('#add_fin').click(function(){
	dataToPost = new Object();
	dataToPost.FINCODE = '';
	
	$('.tab1').hide();
	$('.tab2').show();		

	$('#loadding').fadeIn(200);
	jdadd_group = $.ajax({
		url: '../setup/Company/FNCorpGetFormAE',
		data:dataToPost,
		type:'POST',
		dataType:'json',
		success:function(data){
			$('#tab2_main').find('.spinner, .spinner-backdrop').remove();
			$('#tab2_main').html(data.html);
			
			$('#t2FINCODE').val('');
			$('#t2FINNAME').val('');
			$('#t2FINADDR1').val('');
			$('#t2FINADDR2').val('');
			$('#t2FINTELP').val('');
			$('#t2memo1').val('');
			
			$('#tab2save').attr('action','add');
			$('#tab2del').attr('disabled',true);
			$('#t2gcode').attr('readonly',false);
			afterSelect();
			jdadd_group = null;
			$('#loadding').fadeOut(200);
		},
		beforeSend: function(){ if(jdadd_group !== null){ jdadd_group.abort(); } },
		error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
	});
});



















