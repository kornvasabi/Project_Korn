/********************************************************
             ______@16/04/2020______
			 Pasakorn Boonded

********************************************************/
var _locat  = $('.k_tab1[name="home"]').attr('locat');
var _insert = $('.k_tab1[name="home"]').attr('cin');
var _update = $('.k_tab1[name="home"]').attr('cup');
var _delete = $('.k_tab1[name="home"]').attr('cdel');
var _level  = $('.k_tab1[name="home"]').attr('clev');
var _today  = $('.k_tab1[name="home"]').attr('today');
$(function(){
	$('#invmovt').hide();
	$('#btnupdate').attr('disabled',true);
	$('#btnsearch').click(function(){
		fn_search();
	});
	var searchdetailcar = null;
	function fn_search(){
		dataToPost = new Object();
		dataToPost.STRNO = $('#STRNO').val();
		$('#dataTables-Invtran tbody').html('');
		$('#dataTables-Invtran tbody').html("<table width='100%' height='100%'><tr><td colspan='17'><img src='../public/images/loading-icon2.gif' style='width:50px;height:15px;'></td></tr></table>");
		searchdetailcar = $.ajax({
			url: '../SYS02/UpdateOrderMoveCar/Searchdetailcar',
			data: dataToPost,
			type: 'POST',
			dataType: 'json',
			success: function(data){
				//$('#btnupdate').attr('disabled',false);
				
				if(_update == "T"){
					$('#btnupdate').attr('disabled',false);
				}else{
					$('#btnupdate').attr('disabled',true);
				}
				
				$('#dataTables-Invtran tbody').empty().append(data.invtran);
				document.getElementById("dataTable-fixed-Invtran").addEventListener("scroll", function(){
					var translate = "translate(0,"+(this.scrollTop - 7)+"px)";
					this.querySelector("thead").style.transform = translate;
					this.querySelector("thead").style.zIndex = 100;
				});
				searchdetailcar = null;
			},
			beforeSend: function(){
				if(searchdetailcar !== null){searchdetailcar.abort();}
			}
		});
	}
});
$('#btnupdate').click(function(){	
	Lobibox.confirm({
		title: 'ยืนยันการทำรายการ',
		iconClass: false,
		msg: "คุณต้องปรับปรุงลำดับการโอนย้ายรถ ?",
		closeOnEsc: false,
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
				fn_updateorder();
			}
		}
	});
});
var updateorder = null;
function fn_updateorder(){
	dataToPost = new Object();
	dataToPost.STRNO = $('#STRNO').val();
	$('#loadding').fadeIn(1000);
	updateorder = $.ajax({
		url: '../SYS02/UpdateOrderMoveCar/Updateordercar',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			$('#loadding').fadeOut(1000);
			if(data.status == 'Y'){
				Lobibox.notify('success', {
					title: 'สำเร็จ',
					size: 'mini',
					closeOnClick: false,
					delay: 8000,
					pauseDelayOnHover: true,
					continueDelayOnInactiveTab: false,
					icon: true,
					messageHeight: '90vh',
					//soundPath: $("#maincontents").attr("baseurl")+'public/lobibox-master/sounds/',   // The folder path where sounds are located
					soundExt: '.ogg',
					msg: data.msg
				});
				$('#STRNO').val('');
				$('#dataTables-Invtran tbody').empty();
				$('#btnupdate').attr('disabled',true);
			}else{
				Lobibox.notify('error', {
					title: 'แจ้งเตือน',
					size: 'mini',
					closeOnClick: true,
					delay: 5000,
					pauseDelayOnHover: true,
					continueDelayOnInactiveTab: false,
					icon: true,
					messageHeight: '90vh',
					//soundPath: $("#maincontents").attr("baseurl")+'public/lobibox-master/sounds/',   // The folder path where sounds are located
					soundExt: '.ogg',
					msg: data.msg
				});
			}
			updateorder = null;	
		},
		beforeSend: function(){
			if(updateorder !== null){updateorder.abort();}
		}
	});
}