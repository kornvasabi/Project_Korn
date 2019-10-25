var jd_fn_search = null;
$(function(){
	fn_search();
});

$("#btnSearch").click(function(){
	fn_search();
});

function fn_search(){
	dataToPost = new Object();
	dataToPost.CUSCOD  = $('#CUSCOD').val();
	dataToPost.CUSNAME = $('#CUSNAME').val();
	
	$('#loadding').fadeIn(200);
	
	jd_fn_search = $.ajax({
		url:'../SYS04/Question/customer_search',
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
				$("#jd_result").html(data.html);				
			}
			fn_aftersearch();
			$("#loadding").fadeOut(300);
			jd_fn_search = null;
		},
		beforeSend: function(){
			if(jd_fn_search !== null){ jd_fn_search.abort(); }
		}
	});
}

function fn_aftersearch(){
	
	fn_datatables('table-HSearch',1,350,'NO');
	
	var jd_cusdetail = null;
	$('.cusdetail').click(function(){
		dataToPost = new Object();
		dataToPost.cuscod = $(this).attr('CUSCOD');
		
		$("#loadding").fadeIn(200);
		jd_cusdetail = $.ajax({
			url:'../SYS04/Question/customer_ins_detail',
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
					Lobibox.window({
						title: 'Window Form..',
						width: $(window).width(),
						height: $(window).height(),
						content: data.html,
						draggable: true,
						closeOnEsc: true,
						shown: function($thisFormCalNopay){
							
							//$('#table-unsale').on('draw.dt',function(){ redraw(); });
							fn_datatables('table-address',1,200,'yes');
							fn_datatables('table-ins',1,200,'yes');							
						}	
					});						
				}
				
				$("#loadding").fadeOut(300);
				jd_cusdetail = null;
			},
			beforeSend: function(){
				if(jd_cusdetail !== null){ jd_cusdetail.abort(); }
			}
		});
	});
}











