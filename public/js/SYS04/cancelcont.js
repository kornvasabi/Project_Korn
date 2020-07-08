/********************************************************
             ______@14/05/2020______
			 Pasakorn Boonded

********************************************************/
var _locat  = $('.k_tab1[name="home"]').attr('locat');
var _insert = $('.k_tab1[name="home"]').attr('cin');
var _update = $('.k_tab1[name="home"]').attr('cup');
var _delete = $('.k_tab1[name="home"]').attr('cdel');
var _level  = $('.k_tab1[name="home"]').attr('clev');
var _today  = $('.k_tab1[name="home"]').attr('today');
$(function(){});

$('#btnsearch').click(function(){
	fn_Search();
});
var CanContSearch = null;
function fn_Search(){
	dataToPost = new Object();
	dataToPost.CONTNO  = $('#CONTNO').val();
	dataToPost.F_SDATE = $('#F_SDATE').val();
	dataToPost.T_SDATE = $('#T_SDATE').val();
	$('#loadding').fadeIn(250);
	CanContSearch = $.ajax({
		url: '../SYS04/Question/searchCanCelCont',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			$('#loadding').fadeOut(data.html);
			$('#ContCancel').html(data.html);
			fn_datatables('table-cancont',1,350,'NO');
			
			CanContSearch = null;
		},
		beforeSend: function(){
			if(CanContSearch !== null){CanContSearch.abort();}
		}
	});
}