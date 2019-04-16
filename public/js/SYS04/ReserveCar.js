/********************************************************
             ______@08/03/2019______
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
	$('#CUSCOD').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getCUSTOMERS',
			data: function (params) {
				dataToPost = new Object();
				dataToPost.now = $('#CUSCOD').find(':selected').val();
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
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	
	test();
});

function test(){
	$('#CUSCOD').select2({ disabled: true,dropdownParent: true });
	
	newOption = new Option('xxx', 'yyy', false, false);
	$('#CUSCOD').empty();
	$('#CUSCOD').append(newOption).trigger('change');
}


$('#btnt1search').click(function(){
	dataToPost = new Object()
	dataToPost.RESVNO = $("#RESVNO").val();
	dataToPost.SRESVDT = $("#SRESVDT").val();
	dataToPost.ERESVDT = $("#ERESVDT").val();
	dataToPost.STRNO  = $("#STRNO").val();
	dataToPost.CUSCOD = $("#CUSCOD").val();
	
	$.ajax({
		url:'../SYS04/ReserveCar/search',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			Lobibox.window({
				title: 'Form Search..',
				width: $(window).width(),
				height: $(window).height(),
				content: data.html,
				draggable: false,
				closeOnEsc: false,
				shown: function($this){
					document.getElementById("table-fixed-ReserveCar").addEventListener("scroll", function(){
						var translate = "translate(0,"+(this.scrollTop - 1)+"px)";
						this.querySelector("thead").style.transform = translate;						
					});
					
					$('.resvnoClick').click(function(){
						Lobibox.window({
							title: 'Form Search..',
							closeOnEsc: false,
							content: 'hi'
						});
					});
					
					$('.panel').lobiPanel({
						//Options go here
						reload: false,
						close: false,
						editTitle: false,
						unpin: false,
						toFullScreen: false
					});
				}
			});
		}
	});
});

$('#btnt1reserve').click(function(){
	$.ajax({
		url:'../SYS04/ReserveCar/getfromReserve',
		type: 'POST',
		dataType: 'json',
		success: function(data){
			Lobibox.window({
				title: 'รายการจองรถ',
				width: $(window).width(),
				height: $(window).height(),
				content: data.html,
				draggable: false,
				closeOnEsc: false,
				shown: function($this){
					//$this.destroy();
					$('.panel').lobiPanel({
						//Options go here
						reload: false,
						close: false,
						editTitle: false,
						unpin: false
					});
					
				}
			});			
		}
	});
});































