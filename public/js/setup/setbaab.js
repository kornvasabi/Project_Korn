 /********************************************************
             ______@29/12/2019______
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

$(function(){
	if($('.tab1[name="home"]').attr('cin') == 'T'){
		$('#add_baab').attr('disabled',false);	
	}else{
		$('#add_baab').attr('disabled',true);	
	}
});

var jdsearch_baab=null;
$('#search_baab').click(function(){
	search();
});

function search(){
	dataToPost = new Object();
	dataToPost.TYPECOD 	= $('#TYPECOD').val();	
	dataToPost.MODELCOD = $('#MODELCOD').val();	
	dataToPost.BAABCOD 	= $('#BAABCOD').val();	
	
	$('#loadding').fadeIn(200);
	jdsearch_baab = $.ajax({
		url: '../setup/CStock/baabSearch',
		data:dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			$('#setbaabResult').html(data.html);
			afterSearch();
			
			jdsearch_baab = null;
			$('#loadding').fadeOut(200);			
		},
		beforeSend: function(){ if(jdsearch_baab !== null){ jdsearch_baab.abort(); } },
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
	
	
	var jdgetit = null;
	$('.getit').click(function(){
		dataToPost = new Object();
		dataToPost.TYPECOD 	= $(this).attr('TYPECOD');
		dataToPost.MODELCOD = $(this).attr('MODELCOD');
		dataToPost.BAABCOD 	= $(this).attr('BAABCOD');
		
		$('#loadding').fadeIn(200);
		jdgetit = $.ajax({
			url: '../setup/CStock/baabGetFormAE',
			data: dataToPost,
			type: 'POST',
			dataType: 'json',
			success:function(data){
				$('#tab2_main').html(data.html);
				$('#t2gcode').attr('readonly',true);
				
				$('#tab2save').attr('action','edit');
				if(_update == 'T'){
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
				
				jdgetit = null;
				$('#loadding').fadeOut(200);
			},
			beforeSend: function(){ if(jdgetit !== null){ jdgetit.abort(); } },
			error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
		});
	});
}


var jdadd_baab=null;
$('#add_baab').click(function(){
	dataToPost = new Object();
	dataToPost.TYPECOD = '';
	dataToPost.MODELCOD = '';
	dataToPost.BAABCOD = '';
	
	$('#loadding').fadeIn(200);
	jdadd_baab = $.ajax({
		url: '../setup/CStock/baabGetFormAE',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success:function(data){
			$('#tab2_main').html(data.html);
			$('#tab2save').attr('action','add');
			if(_insert == 'T'){
				$('#tab2save').attr('disabled',false);	
			}else{
				$('#tab2save').attr('disabled',true);	
			}
			$('#tab2del').attr('disabled',true);
			
			afterSelect();
			jdadd_baab = null;
		},
		beforeSend: function(){ if(jdadd_baab !== null){ jdadd_baab.abort(); } },
		error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
	});
});

function afterSelect(){
	$('.tab1').hide();
	$('.tab2').show();
	
	dataToPost = new Object();
	dataToPost.q = '';
	dataToPost.now = (typeof $('#t2TYPECOD').find(':selected').val() === "undefined" ? "":$('#t2TYPECOD').find(':selected').val());
	
	$('#loadding').fadeIn(200);
	$.ajax({
		url:'../Cselect2/getTYPES',
		data:dataToPost,
		type:'POST',
		dataType:'json',
		success:function(data){
			$t2TYPECOD = $('#t2TYPECOD');
			for($i=0;$i<data.length;$i++){
				if($i==0){ $t2TYPECOD.empty(); } // clear
				$t2TYPECOD.append('<option value="'+data[$i].id+'"  '+(data[$i].id == dataToPost.now ? "selected":"")+'  >'+data[$i].text+'</option>');
			}
			$t2TYPECOD.select2({
				width: '100%'
			});
			$('#loadding').fadeOut(200);
		},
		error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }		
	});
	
	$('#t2TYPECOD').on("select2:select",function(){
		$('#t2MODEL').val(null).trigger('change');
	});
	
	$t2MODEL = $('#t2MODEL');
	$t2MODEL.select2({
		placeholder: 'เลือก',
		ajax: {
			url: '../Cselect2/getMODEL',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.q 		= '';
				dataToPost.now 		= (typeof $('#t2MODEL').find(':selected').val() === "undefined" ? "":$('#t2MODEL').find(':selected').val());
				dataToPost.TYPECOD 	= (typeof $('#t2TYPECOD').find(':selected').val() === "undefined" ? "":$('#t2TYPECOD').find(':selected').val());
				
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
		dropdownParent: $("body"),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	
	$('#tab2back').click(function(){
		$('.tab1').show();
		$('.tab2').hide();
	});
	
	var jdtab2save=null;
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
					dataToPost.TYPECOD 	= $('#t2TYPECOD').val();
					dataToPost.MODEL 	= $('#t2MODEL').val();
					dataToPost.BAAB 	= $('#t2BAAB').val();
					dataToPost.TYPECOD_OLD 	= $('#t2TYPECOD').attr('TYPECOD');
					dataToPost.MODEL_OLD 	= $('#t2MODEL').attr('MODELCOD');
					dataToPost.BAAB_OLD 	= $('#t2BAAB').attr('BAABCOD');
					dataToPost.MEMO1 	= $('#t2MEMO1').val();
					dataToPost.action 	= $('#tab2save').attr('action');
					
					$('#loadding').fadeIn(200);
					jdtab2save = $.ajax({
						url:'../setup/CStock/baabSave',
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
							
							jdtab2save=null;
							$('#loadding').fadeOut(200);
						},
						beforeSend: function(){ if(jdtab2save !== null){ jdtab2save.abort(); } },
						error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
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
	
	var jdtab2del= null;
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
					dataToPost.TYPECOD 	= $('#t2TYPECOD').val();
					dataToPost.MODEL 	= $('#t2MODEL').val();
					dataToPost.BAAB 	= $('#t2BAAB').val();
					dataToPost.TYPECOD_OLD 	= $('#t2TYPECOD').attr('TYPECOD');
					dataToPost.MODEL_OLD 	= $('#t2MODEL').attr('MODELCOD');
					dataToPost.BAAB_OLD 	= $('#t2BAAB').attr('BAABCOD');
					dataToPost.MEMO1 	= $('#t2MEMO1').val();
					dataToPost.action 	= $('#tab2save').attr('action');
					
					$('#loadding').fadeIn(200);
					jdtab2del = $.ajax({
						url:'../setup/CStock/baabDel',
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
							
							jdtab2del = null;
							$('#loadding').fadeOut(200);
						},
						beforeSend: function(){ if(jdtab2del !== null){ jdtab2del.abort(); } },
						error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
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












