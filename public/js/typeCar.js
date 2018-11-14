/*
2018/11/12 - JONG
*/

$('#search_TypeCar').click(function(){ search_TypeCar(); });
function search_TypeCar(){
	dataToPost = new Object();
	dataToPost.inpLOCAT = $('#inpLOCAT').val();
	dataToPost.inpCONTNO = $('#inpCONTNO').val();
	dataToPost.inpSTRNO = $('#inpSTRNO').val();
	dataToPost.inpCUSCOD = $('#inpCUSCOD').val();
	dataToPost.inpCUSNAME = $('#inpCUSNAME').val();
	dataToPost.inpGCODE = $('#inpGCODE').val();
	
	var spinner = $('body>.spinner').clone().removeClass('hide');
    $('#result_TypeCar').html('');
	$('#result_TypeCar').append(spinner);
	
	$.ajax({
		url:'../CHomenew/getTypeCar',
		data:dataToPost,
		type:'POST',
		dataType:'json',
		success:function(data){
			$('#result_TypeCar').find('.spinner, .spinner-backdrop').remove();
			$('#result_TypeCar').html(data.html);
			afterSearch();	
		}
	});
}
function afterSearch(){
	document.getElementById("test").addEventListener("scroll", function(){
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
	
	
	$('.getit').click(function(){
		dataToPost = new Object();
		dataToPost.STRNO = $(this).attr('STRNO');
		
		var spinner = $('body>.spinner').clone().removeClass('hide');
		$('#tab2_main').html('');
		$('#tab2_main').append(spinner);
		
		$('.tab1').hide();
		$('.tab2').show();
		
		$.ajax({
			url:'../CHomenew/getFormChangeTypeCar',
			data:dataToPost,
			type:'POST',
			dataType:'json',
			success:function(data){
				$('#tab2_main').find('.spinner, .spinner-backdrop').remove();
				$('#tab2_main').html(data.html);
				afterSelect();
			}
		});
	});
}


function afterSelect(){
	$('#tab2back').click(function(){
		$('.tab1').show();
		$('.tab2').hide();
	});
	
	$('#tab2save').click(function(){
		Lobibox.confirm({
			title: 'ยืนยันการทำรายการ',
			iconClass: false,
			msg: "คุณต้องการบันทึกการเปลี่ยนกลุ่มรถ<br>จาก "+$('#t2inpGCODE').attr('data-value')+" เป็น "+$('#t2inpGCODENEW').val()+" ?",
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
					dataToPost.STRNO = $('#t2inpSTRNO').val();
					dataToPost.GCODE = $('#t2inpGCODENEW').val();
					
					$.ajax({
						url:'../CHomenew/setTypecars',
						data:dataToPost,
						type:'POST',
						dataType:'json',
						success:function(data){
							if(data.stat){
								Lobibox.notify('success', {
									title: 'สำเร็จ',
									closeOnClick: false,
									delay: 8000,
									pauseDelayOnHover: true,
									continueDelayOnInactiveTab: false,
									icon: false,
									messageHeight: '90vh',
									soundPath: $("#maincontents").attr("baseurl")+'public/lobibox-master/sounds/',   // The folder path where sounds are located
									soundExt: '.ogg',
									msg: data.msg
								});
								
								$('.tab1').show();
								$('.tab2').hide();
								
								$('#inpCONTNO').val('');
								$('#inpSTRNO').val($('#t2inpSTRNO').val());
								$('#inpLOCAT').val('');
								$('#inpCUSCOD').val('');
								$('#inpCUSNAME').val('');
								$('#inpGCODE').val('');
								
								search_TypeCar();
							}else{
								Lobibox.notify('error', {
									title: 'แจ้งเตือน',
									closeOnClick: true,
									delay: 5000,
									pauseDelayOnHover: true,
									continueDelayOnInactiveTab: false,
									icon: false,
									messageHeight: '90vh',
									soundPath: $("#maincontents").attr("baseurl")+'public/lobibox-master/sounds/',   // The folder path where sounds are located
									soundExt: '.ogg',
									msg: data.msg
								});
							}
						}
					});
				}else{
					Lobibox.notify('error', {
						title: 'แจ้งเตือน',
						closeOnClick: true,
						delay: 5000,
						pauseDelayOnHover: true,
						continueDelayOnInactiveTab: false,
						icon: false,
						messageHeight: '90vh',
						soundPath: $("#maincontents").attr("baseurl")+'public/lobibox-master/sounds/',   // The folder path where sounds are located
						soundExt: '.ogg',
						msg: 'ยกเลิกการเปลี่ยนสถานะกลุ่มรถ เลขตัวถัง ' + $('#t2inpSTRNO').val() + ' แล้ว'
					});
				}
			}
		});
	});
}
















