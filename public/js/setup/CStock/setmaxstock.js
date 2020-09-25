/********************************************************
             ______@--/02/2018______
            / / _ _   _ _     __ 
           / // __ \ / __ \ / __ \
       _ _/ // /_/ // / / // /_/ /
     /_ _ _/ \_ _ //_/ /_/ \__  /
                          _ _/ /
                         /___ /
********************************************************/

var _insert = $('.tab1[name="home"]').attr('cin');
var _update = $('.tab1[name="home"]').attr('cup');
var _delete = $('.tab1[name="home"]').attr('cdel');
var _level  = $('.tab1[name="home"]').attr('clev');

$(function(){
	if(_insert == "T"){
		$('#add_group').attr('disabled',false);
	}else{
		$('#add_group').attr('disabled',true);
	}
});

$('#search_group').click(function(){
	search();
});

function search(){
	dataToPost = new Object();
	dataToPost.locat = $('#ms_locat').val();
	dataToPost.prov  = $('#ms_prov').val();
	dataToPost.canup = _update;
	
	$('#setmaxstockResult').html("");
	
	$.ajax({
		url:'../setup/CStock/maxstock_search',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			$('#setmaxstockResult').html(data.html);
			
			document.getElementById("table-fixed-maxstockSearch").addEventListener("scroll", function(){
				var translate = "translate(0,"+(this.scrollTop - 1)+"px)";
				this.querySelector("thead").style.transform = translate;
				this.querySelector("thead").style.zIndex = 100;
			});
			
			$('.getit').hover(function(){
				$(this).css({'background-color':'yellow'});
				$('.trow[seq='+$(this).attr('seq')+']').css({'background-color':'#f9f9a9'});
			},function(){
				$(this).css({'background-color':'white'});
				$('.trow[seq='+$(this).attr('seq')+']').css({'background-color':'white'});
			});
			
			$('.mst-edit').click(function(){
				if(_update == "T"){
					dataToPost = new Object();
					dataToPost.LOCAT = $(this).attr('LOCAT');
					
					$.ajax({
						url:'../setup/CStock/maxstock_formedit',
						data: dataToPost,
						typr: 'POST',
						dataType: 'json',
						success: function(edata){
							Lobibox.window({
								title: 'Form Edit..',
								width: setwidth,
								height: setheight,
								content: edata.html,
								closeOnEsc: false,
								shown: function($this){
									$('#fa_edit').click(function(){
										Lobibox.confirm({
											title: 'พื้นที่จอดรถสาขา',
											iconClass: false,
											msg: "คุณต้องการแก้ไขพื้นที่จอดรถสาขา ?",	
											buttons: {
												ok : {
													'class': 'btn btn-primary',
													text: 'บันทึก',
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
													dataToPost.locat = $('#fa_locat').val();
													dataToPost.prov = $('#fa_prov').val();
													dataToPost.line = $('#fa_line').val();
													dataToPost.area = $('#fa_area').val();
													dataToPost.maxn = $('#fa_maxn').val();
													dataToPost.maxo = $('#fa_maxo').val();
													dataToPost.maxs = $('#fa_maxs').val();
													dataToPost.locatStatus = $('#fa_locatStatus').val();
													
													$.ajax({
														url:'../setup/CStock/maxstock_edit',
														data: dataToPost,
														type: 'POST',
														dataType: 'json',
														success: function(data){
															if(data.status){
																Lobibox.notify('success', {
																	title: 'ข้อมูล',
																	size: 'mini',
																	closeOnClick: true,
																	delay: 10000,
																	pauseDelayOnHover: true,
																	continueDelayOnInactiveTab: false,
																	icon: true,
																	messageHeight: '90vh',
																	soundPath: $("body").attr("baseUrl")+'public/lobibox-master/sounds/',   // The folder path where sounds are located
																	soundExt: '.ogg',
																	msg: data.msg
																});
																
																$this.destroy();
																search();
															}else{
																Lobibox.notify('error', {
																	title: 'ข้อมูล',
																	size: 'mini',
																	closeOnClick: true,
																	delay: 10000,
																	pauseDelayOnHover: true,
																	continueDelayOnInactiveTab: false,
																	icon: true,
																	messageHeight: '90vh',
																	soundPath: $("body").attr("baseUrl")+'public/lobibox-master/sounds/',   // The folder path where sounds are located
																	soundExt: '.ogg',
																	msg: data.msg
																});
															}
														}
													});
												}
											}
										});
									});
								}
							});
						}
					});
				}else{
					Lobibox.notify('error', {
						title: 'ข้อมูล',
						size: 'mini',
						closeOnClick: true,
						delay: 10000,
						pauseDelayOnHover: true,
						continueDelayOnInactiveTab: false,
						icon: true,
						messageHeight: '90vh',
						soundPath: $("body").attr("baseUrl")+'public/lobibox-master/sounds/',   // The folder path where sounds are located
						soundExt: '.ogg',
						msg: 'ผิดพลาด คุณไม่มีสิทธิ์แก้ไขข้อมูลพื้นที่สต๊อกรถครับ'
					});
				}
			});
		}
	});
}



$('#add_group').click(function(){	
	
	$.ajax({
		url:'../setup/CStock/maxstock_formadd',
		//data:'',
		type:'POST',
		dataType:'json',
		success: function(data){
			Lobibox.window({
				title: 'Form Search..',
				width: setwidth,
				height: setheight,
				content: data.html,
				closeOnEsc: false,
				shown: function($this){
					$('#fa_save').attr('disabled',true);
					$('#fa_locat').select2({
						placeholder: 'เลือก',
						ajax: {
							url: '../Cselect2/getLOCAT',
							data: function (params) {
								dataToPost = new Object();
								dataToPost.q = params.term;
								dataToPost.TYPECOD = 'HONDA';
								
								return dataToPost;
							},
							dataType: 'json',
							delay: 2000,
							processResults: function (data) {
								return {
									results: data
								};
							},
							cache: true
						},
						allowClear: true,
						multiple: false,
						//theme: 'classic',
						dropdownParent: $(".lobibox-body"),
						width: '100%'
					});
					
					$('#fa_locat').change(function(){
						var locat = $(this).find(':selected').val();
						$.ajax({
							url: '../setup/CStock/maxstock_checkaddLOCAT',
							data: { locat: locat },
							type: 'POST',
							dataType: 'json',
							success: function(data){
								if(data.html > 0){
									$('#fa_save').attr('disabled',true);
									Lobibox.notify('error', {
										title: 'ข้อมูล',
										size: 'mini',
										closeOnClick: true,
										delay: 10000,
										pauseDelayOnHover: true,
										continueDelayOnInactiveTab: false,
										icon: true,
										messageHeight: '90vh',
										soundPath: $("body").attr("baseUrl")+'public/lobibox-master/sounds/',   // The folder path where sounds are located
										soundExt: '.ogg',
										msg: 'ผิดพลาด สาขา '+locat+' มีข้อมูลพื้นที่หน้าร้านแล้วครับ'
									});
								}else if(locat == ''){
									$('#fa_save').attr('disabled',true);
								}else{
									$('#fa_save').attr('disabled',false);
								}
							}
						});
					});
					
					$('#fa_prov').select2({dropdownParent: $(".lobibox-body"),width: '100%'});
					$('#fa_locatStatus').select2({dropdownParent: $(".lobibox-body"),width: '100%'});
					
					$('#fa_save').click(function(){
						Lobibox.confirm({
							title: 'พื้นที่จอดรถสาขา',
							iconClass: false,
							msg: "คุณต้องการบันทึกพื้นที่จอดรถสาขา ?",
							buttons: {
								ok : {
									'class': 'btn btn-primary',
									text: 'บันทึก',
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
									dataToPost.locat = $('#fa_locat').val();
									dataToPost.prov = $('#fa_prov').val();
									dataToPost.line = $('#fa_line').val();
									dataToPost.area = $('#fa_area').val();
									dataToPost.maxn = $('#fa_maxn').val();
									dataToPost.maxo = $('#fa_maxo').val();
									dataToPost.maxs = $('#fa_maxs').val();
									dataToPost.locatStatus = $('#fa_locatStatus').val();
									
									$.ajax({
										url:'../setup/CStock/maxstock_add',
										data: dataToPost,
										type: 'POST',
										dataType: 'json',
										success: function(data){
											if(data.status){
												Lobibox.notify('success', {
													title: 'ข้อมูล',
													size: 'mini',
													closeOnClick: true,
													delay: 10000,
													pauseDelayOnHover: true,
													continueDelayOnInactiveTab: false,
													icon: true,
													messageHeight: '90vh',
													soundPath: $("body").attr("baseUrl")+'public/lobibox-master/sounds/',   // The folder path where sounds are located
													soundExt: '.ogg',
													msg: data.msg
												});
												
												$this.destroy();
											}else{
												Lobibox.notify('error', {
													title: 'ข้อมูล',
													size: 'mini',
													closeOnClick: true,
													delay: 10000,
													pauseDelayOnHover: true,
													continueDelayOnInactiveTab: false,
													icon: true,
													messageHeight: '90vh',
													soundPath: $("body").attr("baseUrl")+'public/lobibox-master/sounds/',   // The folder path where sounds are located
													soundExt: '.ogg',
													msg: data.msg
												});
											}
										}
									});
								}
							}
						});						
					});
				}
			});
		}
	});
});





























