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







$('#btnt1reserve').click(function(){
	
});


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
				shown: function($this){
					document.getElementById("table-fixed-ReserveCar").addEventListener("scroll", function(){
						var translate = "translate(0,"+(this.scrollTop - 1)+"px)";
						this.querySelector("thead").style.transform = translate;						
					});
					
					$('.resvnoClick').click(function(){
						Lobibox.window({
							title: 'Form Search..',
							content: 'hi'
						});
					});
				}
			});
		}
	});
});