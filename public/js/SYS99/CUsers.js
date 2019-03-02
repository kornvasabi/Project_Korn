/********************************************************
             ______@--/02/2018______
            / / _ _   _ _     __ 
           / // __ \ / __ \ / __ \
       _ _/ // /_/ // / / // /_/ /
     /_ _ _/ \_ _ //_/ /_/ \__  /
                          _ _/ /
                         /___ /
********************************************************/

$(function(){
	$('.tab1').show();
	$('.tab2').hide();	 
	
	$('#dblocat').select2({
		dropdownAutoWidth : true,
		width: '100%'
	});
	
	$('#groupCode').select2({
		placeholder: 'เลือก',
		ajax: {
			url: '../Cselect2/getGroupCode',
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
		dropdownAutoWidth : true,
		width: '100%'
	});	
});

$('#btnt1search').click(function(){
	dataToPost = new Object();
	dataToPost.dblocat = $('#dblocat').val();
	dataToPost.USERID = $('#USERID').val();
	dataToPost.IDNo = $('#IDNo').val();
	dataToPost.Name = $('#Name').val();
	dataToPost.groupCode = $('#groupCode').val();
	
	var spinner = $('body>.spinner').clone().removeClass('hide');
    $('#resultt1users').html('');
	$('#resultt1users').append(spinner);	
	
	$.ajax({
		url:'../SYS99/CUsers/search',
		data:dataToPost,
		type:'POST',
		dataType:'json',
		success:function(data){
			$('#resultt1users').find('.spinner, .spinner-backdrop').remove();
			$('#resultt1users').html(data.html);
			
			document.getElementById("table-fixed-CUsers").addEventListener("scroll", function(){
				var translate = "translate(0,"+(this.scrollTop - 1)+"px)";
				this.querySelector("thead").style.transform = translate;						
			});
			
			$('.getit').hover(function(){
				$(this).css({'background-color':'yellow'});
				$('.trow[seq='+$(this).attr('seq')+']').css({'background-color':'#f9f9a9'});
			},function(){
				$(this).css({'background-color':'white'});
				$('.trow[seq='+$(this).attr('seq')+']').css({'background-color':'white'});
			});
			
			$('.getit').click(function(){
				getDetailsFN($(this).attr('USERID'));
			});
		}
	});
});

function getDetailsFN($this){
	dataToPost = new Object();
	dataToPost.USERID = $this;
	dataToPost.dblocat = $('#tab1dblocat').attr('dblocat');
	dataToPost.cup = $('.tab1[name="home"]').attr('cup');
	dataToPost.clev = $('.tab1[name="home"]').attr('clev');
	
	var spinner = $('body>.spinner').clone().removeClass('hide');
	$('#resultt2users').html('');
	$('#resultt2users').append(spinner);
	
	$.ajax({
		url:'../SYS99/CUsers/getDetails',
		data:dataToPost,
		type:'POST',
		dataType:'json',
		success:function(data){
			$('#resultt2users').find('.spinner, .spinner-backdrop').remove();
			$('#resultt2users').html(data.html);
			
			$('#btnt2mapusers').click(function(){
				dataToPost = new Object();
				dataToPost.employeeCode = $('#t2mapusers').val();
				dataToPost.USERID 		= $('#t2USERID').val();
				dataToPost.groupCode 	= $('#t2groupCode').val();
				dataToPost.dblocat 		= $('#tab1dblocat').attr('dblocat');
				
				Lobibox.confirm({
					title: 'ยืนยันการทำรายการ',
					iconClass: false,
					msg: "คุณต้องการยกเลิก Map users ?",
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
							$.ajax({
								url:'../SYS99/CUsers/mapUsers',
								data:dataToPost,
								type:'POST',
								dataType:'json',
								success:function(data){
									if(data.status){
										$('#t2mapusers').attr('disabled',false);
										$('#btnt2mapusers').attr('disabled',false);
										getDetailsFN($('#t2USERID').val());
										
										Lobibox.notify('success', {
											title: 'สำเร็จ',
											size: 'mini',
											closeOnClick: false,
											delay: 8000,
											pauseDelayOnHover: true,
											continueDelayOnInactiveTab: false,
											icon: true,
											messageHeight: '90vh',
											soundPath: $(".menu-fixed").attr("baseUrl")+'/public/lobibox-master/sounds/',   // The folder path where sounds are located
											soundExt: '.ogg',
											msg: data.msg
										});
									}else{
										Lobibox.notify('error', {
											title: 'สำเร็จ',
											size: 'mini',
											closeOnClick: false,
											delay: 8000,
											pauseDelayOnHover: true,
											continueDelayOnInactiveTab: false,
											icon: true,
											messageHeight: '90vh',
											soundPath: $(".menu-fixed").attr("baseUrl")+'/public/lobibox-master/sounds/',   // The folder path where sounds are located
											soundExt: '.ogg',
											msg: data.msg
										});
									}
								}
							});
						}
					}
				});	
			});
			
			$('.getitclaim').click(function(){
				dataToPost = new Object();
				dataToPost.employeeCode = $(this).attr('employeeCode');
				dataToPost.USERID 		= $(this).attr('USERID');
				dataToPost.dblocat 		= $('#tab1dblocat').attr('dblocat');
				
				Lobibox.confirm({
					title: 'ยืนยันการทำรายการ',
					iconClass: false,
					msg: "คุณต้องการยกเลิก Map users ?",
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
							$.ajax({
								url:'../SYS99/CUsers/unmapUsers',
								data:dataToPost,
								type:'POST',
								dataType:'json',
								success:function(data){
									if(data.status){
										getDetailsFN($('#t2USERID').val()); //โหลดข้อมูลใหม่
										
										$('#t2mapusers').attr('disabled',false);
										$('#btnt2mapusers').attr('disabled',false);
										$('#t2groupCode').attr('disabled',false);
										
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
									}
								}
							});
						}
					}
				});	
			});
			
			
			$('#btnt2addlocat').click(function(){
				dataToPost = new Object();
				dataToPost.USERID  = $('#t2USERID').val();
				dataToPost.dblocat = $('#tab1dblocat').attr('dblocat');
				dataToPost.LOCATCD = $('#t2alocat').val();
				dataToPost.mainlocat = $('#t2amainlocat').val();
				
				Lobibox.confirm({
					title: 'ยืนยันการทำรายการ',
					iconClass: false,
					msg: "คุณต้องการเพิ่มสิทธิ์สาขา "+$('#t2alocat').val()+" ให้กับ USERID "+$('#t2USERID').val()+" ?",
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
							$.ajax({
								url:'../SYS99/CUsers/addLOCATUsers',
								data:dataToPost,
								type:'POST',
								dataType:'json',
								success:function(data){
									if(data.status){
										getDetailsFN($('#t2USERID').val()); //โหลดข้อมูลใหม่
										
										$('#t2mapusers').attr('disabled',false);
										$('#btnt2mapusers').attr('disabled',false);
										$('#t2groupCode').attr('disabled',false);
										
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
									}else{
										Lobibox.notify('error', {
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
									}
								}
							});
						}
					}
				});
			});
			
			$('.getitlocat').click(function(){
				dataToPost = new Object();
				dataToPost.USERID  = $(this).attr('USERID');
				dataToPost.dblocat = $(this).attr('dblocat');
				dataToPost.LOCATCD = $(this).attr('LOCATCD');
				
				Lobibox.confirm({
					title: 'ยืนยันการทำรายการ',
					iconClass: false,
					msg: "คุณต้องการลบสิทธิ์สาขา "+$(this).attr('LOCATCD')+" ของ USERID "+$(this).attr('USERID')+" ?",
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
							$.ajax({
								url:'../SYS99/CUsers/delLOCATUsers',
								data:dataToPost,
								type:'POST',
								dataType:'json',
								success:function(data){
									if(data.status){
										getDetailsFN($('#t2USERID').val()); //โหลดข้อมูลใหม่
										
										$('#t2mapusers').attr('disabled',false);
										$('#btnt2mapusers').attr('disabled',false);
										$('#t2groupCode').attr('disabled',false);
										
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
									}else{
										Lobibox.notify('error', {
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
									}
								}
							});
						}
					}
				});
			});
			
			
			
			$('#t2mapusers').select2({
				placeholder: 'เลือก',
				ajax: {
					url: '../Cselect2/getVUSER',
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
				dropdownAutoWidth : true,
				width: '100%'
			});	

			$('#t2groupCode').select2({
				placeholder: 'เลือก',
				ajax: {
					url: '../Cselect2/getGroupCode',
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
				dropdownAutoWidth : true,
				width: '100%'
			});	

			$('#t2alocat').select2({
				placeholder: 'เลือก',
				ajax: {
					url: '../SYS99/CUsers/getLOCAT',
					data: function (params) {
						return {
							q: params.term, // search term
							dblocat: $('#tab1dblocat').attr('dblocat')
						};
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
				dropdownAutoWidth : true,
				width: '100%'
			});	
			
			$('#t2amainlocat').select2({
				dropdownAutoWidth : true,
				width: '100%'
			});
								
			$('.tab1').hide();
			$('.tab2').show();
		}
	});
}


$('#btnt1addUsers').click(function(){
	
});

$('#btnt2home').click(function(){
	$('.tab1').show();
	$('.tab2').hide();
});