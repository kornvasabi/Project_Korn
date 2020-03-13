/********************************************************
             ______@26/02/2019______
            / / _ _   _ _     __ 
           / // __ \ / __ \ / __ \
       _ _/ // /_/ // / / // /_/ /
     /_ _ _/ \_ _ //_/ /_/ \__  /
                          _ _/ /
                         /___ /
********************************************************/

$(function(){
	$('.tab1').show();
	$('.tab2').hide();	
	
	$('#dblocat').select2({
		dropdownAutoWidth : true,
		width: '100%'
	});
	
});

$('#btnt1search').click(function(){
	dataToPost = new Object();
	dataToPost.dblocat = $('#dblocat').val();
	dataToPost.groupCode = $('#groupCode').val();
	dataToPost.groupName = $('#groupName').val();
	
	$('#resultt1group').html('');
	$('#loadding').fadeIn(200);
	$.ajax({
		url:'../SYS99/CGroup/search',
		data:dataToPost,
		type:'POST',
		dataType:'json',
		success:function(data){
			$('#resultt1group').html(data.html);
			
			document.getElementById("table-fixed-CGroup").addEventListener("scroll", function(){
				var translate = "translate(0,"+(this.scrollTop - 1)+"px)";
				this.querySelector("thead").style.transform = translate;						
				this.querySelector("thead").style.zIndex = 999; 
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
				dataToPost.dblocat   = $('#dblocat').val();
				dataToPost.groupCode = $(this).attr('groupCode');
				
				getMenuSearch(dataToPost);
			});
			
			$('#loadding').fadeOut(200);
		}
	});
});

function getMenuSearch(dataToPost){
	//$("#loadding").show();
	var menustat = $('#menustat').find(':selected').val();
	var keyword = $('#keyword').val();
	
	dataToPost.keyword = ((typeof keyword === 'undefined') ? '' : keyword);
	dataToPost.menustat = ((typeof menustat === 'undefined') ? 'Y' : menustat);
	
	$('#loadding').fadeIn(200);
	$.ajax({
		url:'../SYS99/CGroup/getClaimGroup',
		data:dataToPost,
		type:'POST',
		dataType:'json',
		success:function(data){
			$("#loadding").hide();
			$('.tab1').hide();
			$('.tab2').show();
			
			$('#resultt2group').html(data.html);
			
			document.getElementById("table-fixed-CGroupDetail").addEventListener("scroll", function(){
				var translate = "translate(0,"+(this.scrollTop - 1)+"px)";
				this.querySelector("thead").style.transform = translate;						
				this.querySelector("thead").style.zIndex = 999; 
			});
			
			$('#table-fixed-CGroupDetail tbody tr').hover(function(){
				$(this).css({
					'background-color':'yellow'
				});
			},function(){
				$(this).css({
					'background-color':'white'
				});
			});
			
			$('#btnt2save').unbind('click');
			$('#btnt2save').click(function(){
				
				var data = [];
				$('.access').each(function(){
					if($(this).attr('default') != "X"){
						var menuid = $(this).attr('menuid');
						var data_access = ($(this).is(':checked') ? "T" : "F");
						var data_insert = "";
						var data_update = "";
						var data_delete = "";
						var data_access_d = $(this).attr('default');
						var data_insert_d = "";
						var data_update_d = "";
						var data_delete_d = "";
						
						$('.insert').each(function(){
							if(menuid == $(this).attr('menuid')){
								data_insert = ($(this).is(':checked') ? "T" : "F");
								data_insert_d = $(this).attr('default');
							}
						});
						
						$('.update').each(function(){
							if(menuid == $(this).attr('menuid')){
								data_update = ($(this).is(':checked') ? "T" : "F");
								data_update_d = $(this).attr('default');
							}
						});
						
						$('.delete').each(function(){
							if(menuid == $(this).attr('menuid')){
								data_delete = ($(this).is(':checked') ? "T" : "F");
								data_delete_d = $(this).attr('default');
							}
						});
						
						data.push([menuid,data_access,data_insert,data_update,data_delete,data_access_d,data_insert_d,data_update_d,data_delete_d]);
					}
				});
				
				dataToPost = new Object();				
				dataToPost.dblocat   = $('#tab2dbData').attr('dblocat');
				dataToPost.groupCode = $('#tab2dbData').attr('groupCode');
				dataToPost.data 	 = data;
				
				$('#loadding').fadeIn(200);
				$.ajax({
					url:'../SYS99/CGroup/setClaim_Groupusers',
					data:dataToPost,
					type:'POST',
					dataType:'json',
					success:function(data){
						Lobibox.notify('info', {
							title: 'สำเร็จ',
							size: 'mini',
							closeOnClick: false,
							delay: 8000,
							pauseDelayOnHover: true,
							continueDelayOnInactiveTab: false,
							icon: true,
							messageHeight: '90vh',
							soundPath: $(".menu-fixed").attr("baseUrl")+'public/lobibox-master/version/1.0/ajax/sound/lobibox/',   // The folder path where sounds are located
							soundExt: '.ogg',
							msg: data.success
						});
						$('#loadding').fadeOut(200);
					}
				});
			});
			
			$('.access').change(function(){
				var menuid = $(this).attr('menuid');
				if(!$(this).is(':checked')){
					$('.insert').each(function(){
						if(menuid == $(this).attr('menuid')){
							$(this).prop('checked',false);
						}
					});
					
					$('.update').each(function(){
						if(menuid == $(this).attr('menuid')){
							$(this).prop('checked',false);
						}
					});
					
					$('.delete').each(function(){
						if(menuid == $(this).attr('menuid')){
							$(this).prop('checked',false);
						}
					});
				}
			});
			
			$('.insert').change(function(){
				var menuid = $(this).attr('menuid');
				var checked = 0;
				if($(this).is(':checked')){
					$('.access').each(function(){
						if(menuid == $(this).attr('menuid')){
							$(this).prop('checked',true);
						}
					});
				}else{
					$('.update').each(function(){
						if(menuid == $(this).attr('menuid')){
							if($(this).is(':checked')){
								checked++;
							}
						}
					});
					
					$('.delete').each(function(){
						if(menuid == $(this).attr('menuid')){
							if($(this).is(':checked')){
								checked++;
							}
						}
					});
					
					if(checked == 0){
						$('.access').each(function(){
							if(menuid == $(this).attr('menuid')){
								$(this).prop('checked',false);
							}
						});
					} 
				}
			});
			
			$('.update').change(function(){
				var menuid = $(this).attr('menuid');
				var checked = 0;
				if($(this).is(':checked')){
					var menuid = $(this).attr('menuid');
					$('.access').each(function(){
						if(menuid == $(this).attr('menuid')){
							$(this).prop('checked',true);
						}
					});
				}else{
					$('.insert').each(function(){
						if(menuid == $(this).attr('menuid')){
							if($(this).is(':checked')){
								checked++;
							}
						}
					});
					
					$('.delete').each(function(){
						if(menuid == $(this).attr('menuid')){
							if($(this).is(':checked')){
								checked++;
							}
						}
					});
					
					if(checked == 0){
						$('.access').each(function(){
							if(menuid == $(this).attr('menuid')){
								$(this).prop('checked',false);
							}
						});
					} 
				}
			});
						
			$('.delete').change(function(){
				var menuid = $(this).attr('menuid');
				var checked = 0;
				if($(this).is(':checked')){
					var menuid = $(this).attr('menuid');
					$('.access').each(function(){
						if(menuid == $(this).attr('menuid')){
							$(this).prop('checked',true);
						}
					});
				}else{
					$('.insert').each(function(){
						if(menuid == $(this).attr('menuid')){
							if($(this).is(':checked')){
								checked++;
							}
						}
					});
					
					$('.update').each(function(){
						if(menuid == $(this).attr('menuid')){
							if($(this).is(':checked')){
								checked++;
							}
						}
					});					
					
					if(checked == 0){
						$('.access').each(function(){
							if(menuid == $(this).attr('menuid')){
								$(this).prop('checked',false);
							}
						});
					} 
				}
			});
			
			var delay;
			$('#keyword').keyup(function(e){
				if(e.keyCode === 13){
					getMenuSearch(dataToPost);
				}
				/*
				clearTimeout(delay);
				delay = setTimeout(function(){
					getMenuSearch(dataToPost);
				},1500);
				*/
			});
			
			$('#menustat').change(function(){
				getMenuSearch(dataToPost);
			});
			
			$('#btnt2addClaim').click(function(){
				dataToPost = new Object();
				dataToPost.dblocat   = $('#tab2dbData').attr('dblocat');
				dataToPost.groupCode = $('#tab2dbData').attr('groupCode');
				
				$('#loadding').fadeIn(200);
				$.ajax({
					url:'../SYS99/CGroup/getFormClaimADD',
					data:dataToPost,
					type:'POST',
					dataType:'json',
					success:function(data){
						Lobibox.window({
							title: 'Form Search..',
							width: setwidth,
							height: setheight,
							content: data.html,
							closeOnEsc: false,
							shown: function($this){
								$('#btnw1search').click(function(){
									dataToPost = new Object();
									dataToPost.dblocat   = $('#tab2dbData').attr('dblocat');
									dataToPost.groupCode = $('#tab2dbData').attr('groupCode');
									dataToPost.menuid    = $('#w1menuid').val();
									dataToPost.menuname  = $('#w1menuname').val();
									
									$('#loadding').fadeIn(200);
									$.ajax({
										url:'../SYS99/CGroup/getMenu',
										data:dataToPost,
										type:'POST',
										dataType:'json',
										success:function(data){
											$("#w1resultSearch").html(data.html);
											$('#loadding').fadeOut(200);
										}
									});	
								});
							}
						});	
						
						$('#loadding').fadeOut(200);
					}
				});
			});
			
			$('#loadding').fadeOut(200);
		}
	});
}

$('#btnt2home').click(function(){
	$('.tab1').show();
	$('.tab2').hide();	
});











