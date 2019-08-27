/********************************************************
             ______@--/02/2018______
            / / _ _   _ _     __ 
           / // __ \ / __ \ / __ \
       _ _/ // /_/ // / / // /_/ /
     /_ _ _/ \_ _ //_/ /_/ \__  /
                          _ _/ /
                         /___ /
********************************************************/
var _locat  = $('.tab1[name="home"]').attr('locat');
var _insert = $('.tab1[name="home"]').attr('cin');
var _update = $('.tab1[name="home"]').attr('cup');
var _delete = $('.tab1[name="home"]').attr('cdel');
var _level  = $('.tab1[name="home"]').attr('clev');
var _ugroup = $('.tab1[name="home"]').attr('usergroup');


$('#inpGCODES').select2({
	placeholder: 'เลือก',
	ajax: {
		url: '../Cselect2b/getGCode_typecar',
		data: function (params) {
			dataToPost = new Object();
			dataToPost.ugroup = _ugroup;
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
	allowClear: true,
	multiple: false,
	dropdownParent: $(".btab1"),
	//disabled: true,
	//theme: 'classic',
	width: '100%'
});

$('#inpCUSCOD').select2({
	placeholder: 'เลือก',
	ajax: {
		url: '../Cselect2b/getCUSTOMERS',
		data: function (params) {
			dataToPost = new Object();
			//dataToPost.now = $('#add_cuscod').find(':selected').val();
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
	allowClear: true,
	multiple: false,
	dropdownParent: $(".btab1"),
	//disabled: true,
	//theme: 'classic',
	width: '100%'
});

$('#search_TypeCar').click(function(){ 
	search_TypeCar(); 
});

function search_TypeCar(){
	dataToPost = new Object();
	dataToPost.inpLOCAT = $('#inpLOCAT').val();
	dataToPost.inpCONTNO = $('#inpCONTNO').val();
	dataToPost.inpSTRNO = $('#inpSTRNO').val();
	dataToPost.inpCUSCOD = $('#inpCUSCOD').val();
	//dataToPost.inpCUSNAME = $('#inpCUSNAME').val();
	dataToPost.inpGCODE = $('#inpGCODES').val();
	dataToPost.ugroup = _ugroup;
	
	var spinner = $('body>.spinner').clone().removeClass('hide');
    $('#result_TypeCar').html('');
	$('#result_TypeCar').append(spinner);
	
	$.ajax({
		url:'../CHomenew/getTypeCar',
		data:dataToPost,
		type:'POST',
		dataType:'json',
		success:function(data){
			$('#result_TypeCar').find('.spinner, .spinner-backdrop').remove();
			$('#result_TypeCar').html(data.html);
			$('#data-table-example2').on('draw.dt',function(){ afterSearch(); });
			fn_datatables('data-table-example2',1,360);
			//afterSearch();	
		}
	});
}


function afterSearch(){
	document.getElementById("test").addEventListener("scroll", function(){
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
		dataToPost = new Object();
		dataToPost.STRNO = $(this).attr('STRNO');
		dataToPost.ugroup = _ugroup;
		var GCODES = $(this).attr('GCODES');	
		dataToPost.GCODES = GCODES
		
		var spinner = $('body>.spinner').clone().removeClass('hide');
		$('#tab2_main').html('');
		$('#tab2_main').append(spinner);
		
		$('.tab1').hide();
		$('.tab2').show();

		$.ajax({
			url:'../CHomenew/getFormChangeTypeCar',
			data:dataToPost,
			type:'POST',
			dataType:'json',
			success:function(data){
				$('#tab2_main').find('.spinner, .spinner-backdrop').remove();
				$('#tab2_main').html(data.html);
				
				$('#t2inpGCODENEW').select2({
					placeholder: 'เลือก',
					ajax: {
						url: '../Cselect2b/getGCode_typecar2',
						data: function (params) {
							dataToPost = new Object();
							dataToPost.ugroup = _ugroup;
							dataToPost.GCODES = GCODES;
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
					allowClear: true,
					multiple: false,
					dropdownParent: $(".tbchangetypecode"),
					//disabled: true,
					//theme: 'classic',
					width: '100%'
				});
				
				if($('.tab1[name="home"]').attr('cup') == 'T'){
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
			msg: "คุณต้องการบันทึกการเปลี่ยนกลุ่มรถ<br>จาก "+$('#t2inpGCODE').attr('data-value')+" เป็น "+$('#t2inpGCODENEW').val()+" ?",
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
					dataToPost.STRNO = $('#t2inpSTRNO').val();
					dataToPost.GCODE = $('#t2inpGCODENEW').val();
					
					$.ajax({
						url:'../CHomenew/setTypecars',
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
								
								$('#inpCONTNO').val('');
								$('#inpSTRNO').val($('#t2inpSTRNO').val());
								$('#inpLOCAT').val('');
								$('#inpCUSCOD').val('');
								//$('#inpCUSNAME').val('');
								$('#inpGCODES').val('');
								
								search_TypeCar();
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
						msg: 'ยกเลิกการเปลี่ยนสถานะกลุ่มรถ เลขตัวถัง ' + $('#t2inpSTRNO').val() + ' แล้ว'
					});
				}
			}
		});
	});
}
















