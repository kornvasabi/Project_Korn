var calc = null;
function search(){
	dataToPost = new Object();
	dataToPost.price = $('#price').val();
	dataToPost.dwn   = $('#dwn').val();
	dataToPost.vat   = $('#vat').val();
	dataToPost.opt   = $('#opt').val();
	dataToPost.intrt = $('#intrt').val();
	dataToPost.nopay = $('#nopay').val();
	dataToPost.dcm   = $('#dcm').val();
	$("#loadding").fadeIn(200);
	calc = $.ajax({
		url:'../SYS04/Question/CalcuratePrice',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			if(data.error){
				Lobibox.notify('warning', {
					title: 'แจ้งเตือน',
					size: 'mini',
					closeOnClick: false,
					delay: 5000,
					pauseDelayOnHover: true,
					continueDelayOnInactiveTab: false,
					icon: true,
					messageHeight: '90vh',
					msg: data.msg
				});
			}else{
				$("#result").html(data.html);				
			}
			$("#loadding").fadeOut(300);
			calc = null;
		},
		beforeSend: function(){
			if(calc !== null){ calc.abort(); }
		}
	});
}
$('#calc').click(function(){ search(); });

$('#price').keyup(function(e){ if(e.keyCode === 13){ $('#dwn').focus(); } });
$('#dwn').keyup(function(e){ if(e.keyCode === 13){ $('#opt').focus(); } });
$('#opt').keyup(function(e){ if(e.keyCode === 13){ $('#intrt').focus(); } });
$('#intrt').keyup(function(e){ if(e.keyCode === 13){ $('#nopay').focus(); } });
$('#nopay').keyup(function(e){ if(e.keyCode === 13){ $('#dwn').focus(); } });
$('#nopay').keyup(function(e){ if(e.keyCode === 13){ search();$('#price').focus(); } });