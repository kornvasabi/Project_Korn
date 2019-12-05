/********************************************************
             ______@--/--/2018______
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
	document.getElementById("table-fixed-choose").addEventListener("scroll", function(){
		var translate = "translate(0,"+(this.scrollTop - 1)+"px)";
		this.querySelector("thead").style.transform = translate;						
		this.querySelector("thead").style.zIndex = 1000;
	});
	
	if(_insert == "T"){
		$('#addSTRNO').attr('disabled',false);
		$('#tab11processCar').attr('disabled',false);
	}else{
		$('#addSTRNO').attr('disabled',true);
		$('#tab11processCar').attr('disabled',true);
	}
});

var setwidth = $(window).width();
var setheight = $(window).height();
if(setwidth > 1000){
	setwidth = 1000;
}else{
	setwidth = setwidth - 50;
}

if(setheight > 800){
	setheight = 800;
}else{
	setheight = setheight - 50;
}

var generate = 1;
$('#addSTRNO').click(function(){
	dataToPost = new Object();
	dataToPost.LOCAT = $(this).attr('LOCAT');
	$.ajax({
		url:'../SYS02/Cautotransferscars/getFormSTRNO',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			Lobibox.window({
				title: 'Form Search..',
				width: setwidth,
				height: setheight,
				content: data.html,
				closeOnEsc: false,
				shown: function($this){
					
					if(_level != 1){
						$('#t1LOCAT').attr("disabled",true);
					}
					
					$('#t1LOCAT').select2({
						placeholder: 'เลือก',
						ajax: {
							url: '../Cselect2/getLOCAT',
							dataType: 'json',
							delay: 1000,
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
					
					$('#t1RECVNO').select2({
						placeholder: 'เลือก',
						ajax: {
							url: '../Cselect2/getINVINVO',
							data: function (params) {
								return {
									q: params.term, // search term
									RECVDT: '',
									RVLOCAT: $('#t1LOCAT').val()
								};
							},
							dataType: 'json',
							delay: 1000,
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
					
					$('#t1MODEL').select2({
						placeholder: 'เลือก',
						ajax: {
							url: '../Cselect2/getMODEL',
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
					
					$('#t1COLOR').select2({
						placeholder: 'เลือก',
						ajax: {
							url: '../Cselect2/getCOLOR',
							dataType: 'json',
							delay: 1000,
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
					
					$('#btnt1search').click(function(){
						dataToPost = new Object();
						dataToPost.LOCAT  = $('#t1LOCAT').val();
						dataToPost.RECVNO = $('#t1RECVNO').val();
						dataToPost.STRNO  = $('#t1STRNO').val();
						dataToPost.MODEL  = $('#t1MODEL').val();
						dataToPost.COLOR  = $('#t1COLOR').val();
						dataToPost.STAT   = $('#t1STAT').val();
						
						var spinner = $('body>.spinner').clone().removeClass('hide');
						$('#resultSearcht1').html('');
						$('#resultSearcht1').append(spinner);
						
						$.ajax({
							url:'../SYS02/Cautotransferscars/getSearchSTRNO',
							data: dataToPost,
							type: 'POST',
							dataType: 'json',
							success: function(data){
								$('#resultSearcht1').find('.spinner, .spinner-backdrop').remove();
								
								if(data.status){
									$("#resultSearcht1").html(data.html);
									
									document.getElementById("table-fixed-SearchSTRNO").addEventListener("scroll", function(){
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
									
									$('#getithead').hover(function(){
										$('.getit').css({'background-color':'yellow'});
										$('.trow').css({'background-color':'#f9f9a9'});
									},function(){
										$('.getit').css({'background-color':'white'});
										$('.trow').css({'background-color':'white'});
									});
									
									/*เลือกทั้งหมด*/
									var checkprov;
									$('#getithead').click(function(){
										$('#table-SearchSTRNO tbody tr').each(function() {
											var STRNO = this.cells[1].innerHTML;											
											var MODEL = this.cells[2].innerHTML;
											var BAAB = this.cells[3].innerHTML;
											var COLOR = this.cells[4].innerHTML;
											var STAT  = this.cells[5].innerHTML;
											var LOCAT = this.cells[6].innerHTML;
											checkprov = LOCAT;
											
											var STATUS = false; //ตรวจสอบว่ามีอยู่ในรายการแล้วหรือยัง
											$('#table-choose tbody tr').each(function() {
												if(this.cells[1].innerHTML == STRNO){ STATUS=true; }
											});
											
											//check
											//fn_checkprov(checkprov);
											
											if(STATUS){
												Lobibox.notify('error', {
													title: 'ผิดพลาด',
													size: 'mini',
													closeOnClick: false,
													delay: 5000,
													pauseDelayOnHover: true,
													continueDelayOnInactiveTab: false,
													icon: true,
													messageHeight: '90vh',
													msg: 'เลขตัวถัง '+STRNO+' มีอยู่ในรายการแล้ว'
												});
											}else{
												var display = $('#add_EMPCARRY').find(':selected').text();
												var valued = $('#add_EMPCARRY').find(':selected').val();	
												
												
												
												var row = '<tr seq="new'+generate+'">';
												//row += '<td><button class="delSTRNO btn btn-xs btn-danger btn-block" seq="new'+generate+'">ยกเลิก</button></td>';
												row += '<td class="delSTRNO" seq="new'+generate+'" align="center" style="cursor:pointer;color:red;"><!-- b><i class=\'glyphicon glyphicon-trash\' style=\'z-index:20;\'></i></b-->   <button type="button" class="btn btn-labeled btn-danger btn-block btn-xs"><span class="btn-label"><i class="glyphicon glyphicon-trash"></i></span>ลบ</button></td>';
												row += '<td>'+STRNO+'</td>';
												row += '<td>'+MODEL+'</td>';
												row += '<td>'+BAAB+'</td>';
												row += '<td>'+COLOR+'</td>';
												row += '<td>'+STAT+'</td>';
												row += '<td>'+LOCAT+'</td>';
												row += '</tr>';
												  
												$('#table-choose tbody').append(row);
												generate = generate+1;
												
												$('#addSTRNO').attr('LOCAT',LOCAT);
												delSTRNO();
												
												$this.destroy();											
											}
										});
									});
									
									/*เลือกทีละคัน*/
									$('.getit').click(function(){
										var STRNO = $(this).attr('STRNO');
										var MODEL = $(this).attr('MODEL');
										var BAAB  = $(this).attr('BAAB');
										var COLOR = $(this).attr('COLOR');
										var STAT  = $(this).attr('STAT');
										var LOCAT = $(this).attr('LOCAT');
										checkprov = LOCAT;
										
										var STATUS = false; //ตรวจสอบว่ามีอยู่ในรายการแล้วหรือยัง
										$('#table-choose tbody tr').each(function() {
											if(this.cells[1].innerHTML == STRNO){ STATUS=true; }
										});
										
										//check
										//fn_checkprov(checkprov);
										
										if(STATUS){
											Lobibox.notify('error', {
												title: 'ผิดพลาด',
												size: 'mini',
												closeOnClick: false,
												delay: 5000,
												pauseDelayOnHover: true,
												continueDelayOnInactiveTab: false,
												icon: true,
												messageHeight: '90vh',
												msg: 'เลขตัวถัง '+STRNO+' มีอยู่ในรายการแล้ว'
											});
										}else{
											var display = $('#add_EMPCARRY').find(':selected').text();
											var valued = $('#add_EMPCARRY').find(':selected').val();	
											
											var row = '<tr seq="new'+generate+'">';
											//row += '<td><button class="delSTRNO btn btn-xs btn-danger btn-block" seq="new'+generate+'">ยกเลิก</button></td>';
											row += '<td class="delSTRNO" seq="new'+generate+'" align="center" style="cursor:pointer;color:red;"><!-- b><i class=\'glyphicon glyphicon-trash\' style=\'z-index:20;\'></i></b-->   <button type="button" class="btn btn-labeled btn-danger btn-block btn-xs"><span class="btn-label"><i class="glyphicon glyphicon-trash"></i></span>ลบ</button></td>';
											row += '<td>'+STRNO+'</td>';
											row += '<td>'+MODEL+'</td>';
											row += '<td>'+BAAB+'</td>';
											row += '<td>'+COLOR+'</td>';
											row += '<td>'+STAT+'</td>';
											row += '<td>'+LOCAT+'</td>';
											row += '</tr>';
											  
											$('#table-choose tbody').append(row);
											generate = generate+1;
											
											$('#addSTRNO').attr('LOCAT',LOCAT);
											delSTRNO();
											
											$this.destroy();											
										}
									});
									
									//เลือกรถสาขาไหน ให้ไปเลือกจังหวัดในสายนั้นด้วย
									function fn_checkprov(param){
										$.ajax({
											url:'../SYS02/Cautotransferscars/checkprov',
											data: { locat:param },
											type: 'POST',
											dataType: 'json',
											success: function(data){
												if(data.html == 1){
													$('#tab11prov1').prop('checked',true).trigger('change');
													$('#tab11prov2').prop('checked',true).trigger('change');
													$('#tab11prov3').prop('checked',true).trigger('change');
													$('#tab11prov4').prop('checked',false).trigger('change');
													$('#tab11prov5').prop('checked',false).trigger('change');
												}else if(data.html == 2){
													$('#tab11prov1').prop('checked',false).trigger('change');
													$('#tab11prov2').prop('checked',false).trigger('change');
													$('#tab11prov3').prop('checked',false).trigger('change');
													$('#tab11prov4').prop('checked',true).trigger('change');
													$('#tab11prov5').prop('checked',true).trigger('change');
												}else{
													$('#tab11prov1').prop('checked',false).trigger('change');
													$('#tab11prov2').prop('checked',false).trigger('change');
													$('#tab11prov3').prop('checked',false).trigger('change');
													$('#tab11prov4').prop('checked',false).trigger('change');
													$('#tab11prov5').prop('checked',false).trigger('change');
												}
											}											
										});
									}
									
								}else{
									Lobibox.notify('error', {
										title: 'ผิดพลาด',
										size: 'mini',
										closeOnClick: false,
										delay: 5000,
										pauseDelayOnHover: true,
										continueDelayOnInactiveTab: false,
										icon: true,
										messageHeight: '90vh',
										msg: data.html
									});
								}
							},
							error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
						});
					});
				}
			});
		},
		error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
	});
});

$('#tab11Clear').click(function(){
	$('#table-choose tbody tr').each(function() {
		$(this).closest('tr').remove(); 
	});
	
	$('#addSTRNO').attr("locat","");
	
	$('#tab11prov1').prop('checked',false);
	$('#tab11prov2').prop('checked',false);
	$('#tab11prov3').prop('checked',false);
	$('#tab11prov4').prop('checked',false);
	$('#tab11prov5').prop('checked',false);
	
	$('#condStockEmpty').val('0');
	$('#condMaxLimit').val('1');
	
	$('#tab22Body').html('');
	$('#tab33Body').html('');	
});

function delSTRNO(){
	/*
	$('.delSTRNO').click(function(){
		$('#table-STRNOTRANS tr[seq="'+($(this).attr('seq'))+'"]').remove();
	});  
	*/
	$('.delSTRNO').click(function(){ 
		$(this).closest('tr').remove(); 
		
		var stat = false; //ตรวจสอบว่ามีอยู่ในรายการแล้วหรือยัง
		$('#table-choose tbody tr').each(function() {
			//เช็คว่ามีข้อมูลรถแล้วหรือไม่	
			stat=true;
		});
		
		if(stat){  }else{ 
			$('#addSTRNO').attr('locat',''); 
			$('#tab11prov1').prop('checked',false);
			$('#tab11prov2').prop('checked',false);
			$('#tab11prov3').prop('checked',false);
			$('#tab11prov4').prop('checked',false);
			$('#tab11prov5').prop('checked',false);
		}	
	});
}


LobiAdmin.loadScript([
	'../public/lobiadmin-master/version/1.0/ajax/js/plugin/bootstrap-wizard/jquery.bootstrap.wizard.js',
	'../public/lobiadmin-master/version/1.0/ajax/js/plugin/jquery-validation/jquery.validate.min.js',
	'../public/lobiadmin-master/version/1.0/ajax/js/plugin/jquery-validation/additional-methods.min.js',
	'../public/lobiadmin-master/version/1.0/ajax/js/plugin/jasny-bootstrap/jasny-bootstrap.min.js'
], initPage);

function initPage(){
	$('#demo-wizard2').bootstrapWizard({
		onTabClick: function(li, ul, ind, ind2){
			/*
			var $newli = ul.find('li').eq(ind2);
			if ($newli.hasClass('complete')){
				return true;
			}else{
				return false;
			}
			*/
			return false;
		}
	});
	
	$('#tab11processCar[name=tab11]').click(function(){
		var stat = false; //ตรวจสอบว่ามีอยู่ในรายการแล้วหรือยัง
		$('#table-choose tbody tr').each(function() {
			//เช็คว่ามีข้อมูลรถแล้วหรือไม่	
			stat=true;
		});
		
		if(stat){
			var STRNO = [];	
			$('#table-choose tr').each(function() {
				if (!this.rowIndex) return; // skip first row header
				
				//ดึงข้อมูลใน table-choose เก็บใน array
				var len = this.cells.length;
				var r = [];
				for(var i=0;i<len;i++){
					if(i == 0){
						//ไม่เอาปุ่มยกเลิก	
					}else{
						r.push(this.cells[i].innerHTML);
					}
				}	
				STRNO.push(r);
			});
			
			dataToPost = new Object();
			dataToPost.STRNO = STRNO;
			dataToPost.tab11prov1 = [$('#tab11prov1').is(':checked'),$('#tab11prov1').val()];
			dataToPost.tab11prov2 = [$('#tab11prov2').is(':checked'),$('#tab11prov2').val()];
			dataToPost.tab11prov3 = [$('#tab11prov3').is(':checked'),$('#tab11prov3').val()];
			dataToPost.tab11prov4 = [$('#tab11prov4').is(':checked'),$('#tab11prov4').val()];
			dataToPost.tab11prov5 = [$('#tab11prov5').is(':checked'),$('#tab11prov5').val()];
			dataToPost.condStockEmpty = $('#condStockEmpty').val();
			dataToPost.condMaxLimit = $('#condMaxLimit').val();
			
			if (!$('#tab22Body').is(':empty')){
				Lobibox.confirm({
					title: 'คำนวนรายการจัดส่ง/โยกย้ายรถ',
					iconClass: false,
					msg: "คุณต้องการคำนวนใหม่ ?",
					buttons: {
						cancel : {
							'class': 'btn btn-danger',
							text: 'ใช้ข้อมูลเดิม',
							closeOnClick: true
						},
						ok : {
							'class': 'btn btn-primary',
							text: 'คำนวนใหม่',
							closeOnClick: true,
						},
					},
					callback: function(lobibox, type){
						var btnType;
						if (type === 'ok'){
							calculate();
						}else if (type === 'cancel'){
							//มีข้อมูลแล้ว ไปขั้นตอนถัดไป
							$('.wizard-tabs li').each(function(){
								//ลบ wizard ที่ active อยู่ทั้งหมด	
								$('.wizard-tabs li').removeClass('active');
							});
							
							if(!$('.wizard-tabs li').eq(1).hasClass('active')){
								// active tab ถัดไป
								$('.wizard-tabs li').eq(1).addClass('active');
							}
							
							var $id = $('.wizard-tabs li').eq(1).find('a').attr('href').replace('#','');
							var $tabContent = $('.tab-content');
							$tabContent.find('.tab-pane').removeClass('active');
							$tabContent.find('.tab-pane[name='+$id+']').addClass('active');	
						}
					}
				});
			}else{
				calculate();
			}
			
			function calculate(){
				$('#tab22Body').html('');				
				$('#loadding').show();
				
				$.ajax({
					url:'../SYS02/Cautotransferscars/calcurate',
					data: dataToPost,
					type: 'POST',
					dataType: 'json',
					success: function(data) {
						$('#loadding').hide();
						if( data.status ){
							$('#tab22Body').html(data.html);
							
							document.getElementById("table-fixed-tab22").addEventListener("scroll", function(){
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
							
							$('.getit').click(function(){
								$(this).closest('tr').remove(); 
							});
							
							//มีข้อมูลแล้ว ไปขั้นตอนถัดไป
							$('.wizard-tabs li').each(function(){
								//ลบ wizard ที่ active อยู่ทั้งหมด	
								$('.wizard-tabs li').removeClass('active');
							});
							
							if(!$('.wizard-tabs li').eq(1).hasClass('active')){
								// active tab ถัดไป
								$('.wizard-tabs li').eq(1).addClass('active');
							}
							
							var $id = $('.wizard-tabs li').eq(1).find('a').attr('href').replace('#','');
							var $tabContent = $('.tab-content');
							$tabContent.find('.tab-pane').removeClass('active');
							$tabContent.find('.tab-pane[name='+$id+']').addClass('active');	
							
						}else{
							Lobibox.notify('error', {
								title: 'ผิดพลาด',
								size: 'mini',
								closeOnClick: false,
								delay: 5000,
								pauseDelayOnHover: true,
								continueDelayOnInactiveTab: false,
								icon: true,
								messageHeight: '90vh',
								msg: data.html
							});
						}
						
					},
					error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
				});
			}
		}else{
			Lobibox.notify('error', {
				title: 'ผิดพลาด',
				size: 'mini',
				closeOnClick: false,
				delay: 5000,
				pauseDelayOnHover: true,
				continueDelayOnInactiveTab: false,
				icon: true,
				messageHeight: '90vh',
				msg: 'ไม่พบรายการรถที่ต้องการจัดลงสาขา'
			});
		}
		
	});
	
	$('#tab22processCar[name=tab22]').click(function(){
		Lobibox.confirm({
			title: 'คำนวนรายการจัดส่ง/โยกย้ายรถ',
			iconClass: false,
			msg: "คุณต้องการบันทึกรายการโอนย้ายรถ ?",
			buttons: {
				ok : {
					'class': 'btn btn-primary',
					text: 'บันทึกบิลโอนรถ',
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
					var STRNOChoose = [];
					
					$('#table-choose tbody tr').each(function() {
						if (!this.rowIndex) return; // skip first row header
						//ดึงข้อมูลใน table-choose เก็บใน array
						var len = this.cells.length;
						var r = [];
						for(var i=0;i<len;i++){
							if(i == 0){
								//ไม่เอาปุ่มยกเลิก	
							}else{
								r.push(this.cells[i].innerHTML);
							}
						}	
						STRNOChoose.push(r);
					});
					
					var STRNO = [];
					var s = 0;
					$('#table-tab22 tbody tr').each(function() {
						s++;
						if (!this.rowIndex) return; // skip first row header
						
						//ดึงข้อมูลใน table-choose เก็บใน array
						var len = this.cells.length;
						var r = [];
						for(var i=0;i<len;i++){
							if(i == 0){
								//ไม่เอาปุ่มยกเลิก	
							}else{
								r.push(this.cells[i].innerHTML);
							}
						}	
						STRNO.push(r);
					});
					
					
					
					if(STRNO.length > 0){
						$('#loadding').show();
						
						dataToPost = new Object();
						//dataToPost.STRNO = STRNO;
						dataToPost.LOCAT = $('#addSTRNO').attr('LOCAT');
						dataToPost.STRNOChoose = STRNOChoose;
						dataToPost.STRNO = STRNO;
						
						$.ajax({
							url: '../SYS02/Cautotransferscars/confirmResultt1AT',
							data: dataToPost,
							type: 'POST',
							dataType: 'json',
							success: function(data){
								$('#loadding').hide();
								$('#tab33Body').html(data.html);
								
								document.getElementById("table-fixed-tab33").addEventListener("scroll", function(){
									var translate = "translate(0,"+(this.scrollTop - 1)+"px)";
									this.querySelector("thead").style.transform = translate;
									this.querySelector("thead").style.zIndex = 100;
								});
								
								$('.wizard-tabs li').each(function(){
									$('.wizard-tabs li').removeClass('active');
								});
								if(!$('.wizard-tabs li').eq(2).hasClass('active')){
									$('.wizard-tabs li').eq(2).addClass('active');
								}
								var $id = $('.wizard-tabs li').eq(2).find('a').attr('href').replace('#','');
								var $tabContent = $('.tab-content');
								$tabContent.find('.tab-pane').removeClass('active');
								$tabContent.find('.tab-pane[name='+$id+']').addClass('active');
							},
							error: function (jqXHR, exception) {
								setTimeout(function(){
									$('#loadding').hide();
								},3000);
								
								fnAjaxERROR(jqXHR,exception);
							}
						});		
						
					}else{
						Lobibox.notify('info', {
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
							msg: 'ขออภัย ไม่พบข้อมูลที่จะทำการโอนย้าย'
						});
					}
				}
			}
		});
		
		
		/*
		$('.wizard-tabs li').each(function(){
			$('.wizard-tabs li').removeClass('active');
		});
		if(!$('.wizard-tabs li').eq(2).hasClass('active')){
			$('.wizard-tabs li').eq(2).addClass('active');
		}
		var $id = $('.wizard-tabs li').eq(2).find('a').attr('href').replace('#','');
		var $tabContent = $('.tab-content');
		$tabContent.find('.tab-pane').removeClass('active');
		$tabContent.find('.tab-pane[name='+$id+']').addClass('active');
		*/
	});
	
	$('#tab22Back[name=tab22]').click(function(){
		$('.wizard-tabs li').each(function(){
			$('.wizard-tabs li').removeClass('active');
		});
		if(!$('.wizard-tabs li').eq(0).hasClass('active')){
			$('.wizard-tabs li').eq(0).addClass('active');
		}
		
		var $id = $('.wizard-tabs li').eq(1).find('a').attr('prev').replace('#','');
		var $tabContent = $('.tab-content');
		$tabContent.find('.tab-pane').removeClass('active');
		$tabContent.find('.tab-pane[name='+$id+']').addClass('active');
	});
	
	$('#tab33processCar[name=tab33]').click(function(){
		$('.wizard-tabs li').each(function(){
			$('.wizard-tabs li').removeClass('active');
		});
		if(!$('.wizard-tabs li').eq(0).hasClass('active')){
			$('.wizard-tabs li').eq(0).addClass('active');
		}
		
		/*clear input table*/
		$('#table-choose tbody tr').each(function() {
			$(this).closest('tr').remove(); 
		});
		
		$('#addSTRNO').attr("locat","");
		
		$('#tab11prov1').prop('checked',false);
		$('#tab11prov2').prop('checked',false);
		$('#tab11prov3').prop('checked',false);
		$('#tab11prov4').prop('checked',false);
		$('#tab11prov5').prop('checked',false);
		
		$('#condStockEmpty').val('0');
		$('#condMaxLimit').val('1');
		
		$('#tab22Body').html('');
		$('#tab33Body').html('');
		/*******************/
		
		var $id = $('.wizard-tabs li').eq(1).find('a').attr('prev').replace('#','');
		var $tabContent = $('.tab-content');
		$tabContent.find('.tab-pane').removeClass('active');
		$tabContent.find('.tab-pane[name='+$id+']').addClass('active');
	});
}