$(function(){
	$('#RECVNO').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getINVINVO',
			data: function (params) {
				return {
					q: params.term, // search term
					RECVDT: $('#RECVDT').val(),
					RVLOCAT: $('#RVLOCAT').val()
				};
			},
			dataType: 'json',
			delay: 300,
			processResults: function (data) {
				return {
					results: data
				};
			},
			cache: true
        },
		allowClear: true,
		multiple: false,
		theme: 'classic',
		width: '100%'
	});
	
	$('#RVLOCAT').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2/getWarehouse',
			dataType: 'json',
			delay: 300,
			processResults: function (data) {
				return {
					results: data
				};
			},
			cache: true
        },
		allowClear: false,
		multiple: false,
		tags: true,
		theme: 'classic',
		width: '100%'		
	});
});

$('#btnt1search').click(function(){ search(); });
//$('#RECVNO').change(function(){ if($(this).val() != ''){search();} });
$('#RVLOCAT').change(function(){  $('#RECVNO').val('').trigger('change'); });

function search(){
	dataToPost = new Object();
	dataToPost.RECVNO = $('#RECVNO').val();
	dataToPost.RECVDT = $('#RECVDT').val();
	dataToPost.RVLOCAT = $('#RVLOCAT').val();
	
	var spinner = $('body>.spinner').clone().removeClass('hide');
    $('#resultt1received').html('');
	$('#resultt1received').append(spinner);
	
	$.ajax({
		url:'../SYS02/Cautotransferscars/search',
		data:dataToPost,
		type:'POST',
		dataType:'json',
		success:function(data){
			if(!data.status){
				$('#resultt1received').find('.spinner, .spinner-backdrop').remove();
				Lobibox.notify('error', {
					title: 'แจ้งเตือน',
					closeOnClick: true,
					delay: 5000,
					pauseDelayOnHover: true,
					continueDelayOnInactiveTab: false,
					icon: false,
					messageHeight: '90vh',
					msg: data.html
				});
			}else{
				$('#resultt1received').find('.spinner, .spinner-backdrop').remove();
				$('#resultt1received').html(data.html);
				
				document.getElementById("table-fixed-Cautotransferscars").addEventListener("scroll", function(){
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
			}
		}
	});
}