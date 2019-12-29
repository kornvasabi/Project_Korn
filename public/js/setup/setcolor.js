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


var jdsearch_color=null;
$('#search_color').click(function(){
	search();
});

function search(){
	dataToPost = new Object();
	dataToPost.TYPECOD 	= $('#TYPECOD').val();	
	dataToPost.MODELCOD = $('#MODELCOD').val();	
	dataToPost.BAABCOD 	= $('#BAABCOD').val();	
	dataToPost.COLORCOD = $('#COLORCOD').val();	
	
	$('#loadding').fadeIn(200);
	jdsearch_color = $.ajax({
		url: '../setup/CStock/colorSearch',
		data:dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			$('#setcolorResult').html(data.html);
			afterSearch();
			
			jdsearch_color = null;
			$('#loadding').fadeOut(200);			
		},
		beforeSend: function(){ if(jdsearch_color !== null){ jdsearch_color.abort(); } },
		error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
	});
}