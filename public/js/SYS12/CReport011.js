/*****************************
	create date 13/12/2018
	create by jong
******************************/

$(function(){
	start();
});

function start(){
	$('#LOCAT').select2({
		placeholder: 'เลือก',
		ajax: {
			url: '../Cselect2/getLOCAT',
			data: function(params){
				dataToPost = new Object();
				dataToPost.q = $(this).find(':selected').val();
				
				if( undefined != params.term ){ dataToPost.q = params.term }
				
				return dataToPost
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
	
	$('#BILLCOLL').select2({
		placeholder: 'เลือก',
		ajax: {
			url: '../Cselect2/getUSERS',
			data: function(params){
				dataToPost = new Object();
				dataToPost.q = $(this).find(':selected').val();
				
				if( undefined !=params.term ){ 
					dataToPost.q = params.term
				}
				
				return dataToPost
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
	
	$('#GCODE').select2({
		placeholder: 'เลือก',
		ajax: {
			url: '../Cselect2/getGCode',
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
	
	$('#ORDERBY').select2({ minimumResultsForSearch: -1,width: '100%' });
	$('#WAY').select2({ minimumResultsForSearch: -1,width: '100%' });
}


$('#btnt1search').click(function(){
	dataToPost = new Object();
	dataToPost.LOCAT  	= $('#LOCAT').val();
	dataToPost.CONTNO 	= $('#CONTNO').val();
	dataToPost.FPAYDT 	= $('#FPAYDT').val();
	dataToPost.TPAYDT 	= $('#TPAYDT').val();
	dataToPost.BILLCOLL = $('#BILLCOLL').val();
	dataToPost.GCODE  	= $('#GCODE').val();
	dataToPost.ORDERBY  = $('#ORDERBY').val();
	dataToPost.WAY  	= $('#WAY').val();
	
	
	var spinner = $('body>.spinner').clone().removeClass('hide');
    $('#resultt1users').html('');
	$('#resultt1users').append(spinner);	
	
	$.ajax({
		url:'../SYS12/CReport011/search',
		data:dataToPost,
		type:'POST',
		dataType:'json',
		success:function(data){
			$('#resultt1users').find('.spinner, .spinner-backdrop').remove();
			
			if(data.status){
				$('#resultt1users').html(data.html);
				
				document.getElementById("table-fixed-CReport011").addEventListener("scroll", function(){
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
			}else{
				Lobibox.notify('error', {
					title: 'ผิดพลาด',
					closeOnClick: false,
					delay: 5000,
					pauseDelayOnHover: true,
					continueDelayOnInactiveTab: true,
					icon: false,
					messageHeight: '90vh',
					msg: data.html
				});
			}
		}
	});
});





























