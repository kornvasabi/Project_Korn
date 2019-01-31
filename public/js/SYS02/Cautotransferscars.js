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
	
	$('#confirm_resultt1AT').attr('disabled',true);
	$('#confirm_resultt1AT').css({'cursor':'not-allowed'});
});

$('#btnt1search').click(function(){ search(); });
//$('#RECVNO').change(function(){ if($(this).val() != ''){search();} });
$('#RVLOCAT').change(function(){  $('#RECVNO').val('').trigger('change'); });

function search(){
	dataToPost = new Object();
	dataToPost.RECVNO = $('#RECVNO').val();
	dataToPost.RECVDT = $('#RECVDT').val();
	dataToPost.RVLOCAT = $('#RVLOCAT').val();
	
	$('#confirm_resultt1AT').attr('disabled',false);
	$('#confirm_resultt1AT').css({'cursor':'pointer'});
	
	var spinner = $('body>.spinner').clone().removeClass('hide');
    $('#resultt1received').html('');
	$('#resultt1received').append(spinner);
	$('#resultRECV').html('');
	$('#resultRECV').append(spinner);
	
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
				$('#resultRECV').html(data.htmlRECV);
				
				$('#confirm_resultt1AT').attr({
					LOCAT:$('#RVLOCAT').val(), 
					RECVNO:$('#RECVNO').val()
				});
				
				document.getElementById("table-fixed-Cautotransferscars").addEventListener("scroll", function(){
					var translate = "translate(0,"+(this.scrollTop - 1)+"px)";
					this.querySelector("thead").style.transform = translate;						
				});
				
				document.getElementById("table-fixed-CautotransferscarsRECV").addEventListener("scroll", function(){
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

$('#confirm_resultt1AT').hover(function(){
	$(this).css({'color':'white','background-color':'linear-gradient(to right, #0033cc 40%, #3399ff 100%)'});
},function(){
	$(this).css({'color':'black','background-color':'#ddd'});
});


$('#confirm_resultt1AT').click(function(){
	Lobibox.confirm({
		title: 'ยืนยันการทำรายการ',
		iconClass: false,
		msg: "คุณต้องการบันทึกการโอนย้ายรถ ?",
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
				$('#confirm_resultt1AT').attr('disabled',true);
				
				var DATATABLE = [];	
				$('#table-fixed-Cautotransferscars tr').each(function() {
					if (!this.rowIndex) return; // skip first row
					
					var len = this.cells.length;
					var r = [];
					for(var i=0;i<len;i++){
						r.push(this.cells[i].innerHTML);
					}	
					DATATABLE.push(r);
				});
				dataToPost = new Object();
				dataToPost.LOCAT = $('#confirm_resultt1AT').attr('LOCAT');
				dataToPost.RECVNO = $('#confirm_resultt1AT').attr('RECVNO');
				dataToPost.DATATABLE = DATATABLE;
				
				var spinner = $('body>.spinner').clone().removeClass('hide');
				$('#resultt1received2').html('');
				$('#resultt1received2').append(spinner);
				
				$.ajax({
					url:'../SYS02/Cautotransferscars/confirmResultt1AT',
					data:dataToPost,
					type:'POST',
					dataType:'json',
					success:function(data){
						$('#resultt1received').find('.spinner, .spinner-backdrop').remove();
						$('#resultt1received2').html(data.html);
					}
				});
			}
		}
	});
});




























