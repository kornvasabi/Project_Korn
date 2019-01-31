$(function(){
	document.getElementById("table-fixed-choose").addEventListener("scroll", function(){
		var translate = "translate(0,"+(this.scrollTop - 1)+"px)";
		this.querySelector("thead").style.transform = translate;						
	});
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
				shown: function($this){
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
						$("#resultSearcht1").html('');
						$.ajax({
							url:'../SYS02/Cautotransferscars/getSearchSTRNO',
							data: dataToPost,
							type: 'POST',
							dataType: 'json',
							success: function(data){
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
									$('#getithead').click(function(){
										$('#table-SearchSTRNO tbody tr').each(function() {
											var STRNO = this.cells[1].innerHTML;											
											var MODEL = this.cells[2].innerHTML;
											var COLOR = this.cells[3].innerHTML;
											var STAT  = this.cells[4].innerHTML;
											var LOCAT = this.cells[5].innerHTML;
											
											var STATUS = false; //ตรวจสอบว่ามีอยู่ในรายการแล้วหรือยัง
											$('#table-choose tbody tr').each(function() {
												if(this.cells[1].innerHTML == STRNO){ STATUS=true; }
											});
										
											if(STATUS){
												Lobibox.notify('error', {
													title: 'ผิดพลาด',
													closeOnClick: false,
													delay: 5000,
													pauseDelayOnHover: true,
													continueDelayOnInactiveTab: false,
													icon: false,
													messageHeight: '90vh',
													msg: 'เลขตัวถัง '+STRNO+' มีอยู่ในรายการแล้ว'
												});
											}else{
												var display = $('#add_EMPCARRY').find(':selected').text();
												var valued = $('#add_EMPCARRY').find(':selected').val();	
												
												var row = '<tr seq="new'+generate+'">';
												row += '<td><button class="delSTRNO btn btn-xs btn-danger btn-block" seq="new'+generate+'">ยกเลิก</button></td>';
												row += '<td>'+STRNO+'</td>';
												row += '<td>'+MODEL+'</td>';
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
										var COLOR = $(this).attr('COLOR');
										var STAT  = $(this).attr('STAT');
										var LOCAT = $(this).attr('LOCAT');
										
										var STATUS = false; //ตรวจสอบว่ามีอยู่ในรายการแล้วหรือยัง
										$('#table-choose tbody tr').each(function() {
											if(this.cells[1].innerHTML == STRNO){ STATUS=true; }
										});
										
										if(STATUS){
											Lobibox.notify('error', {
												title: 'ผิดพลาด',
												closeOnClick: false,
												delay: 5000,
												pauseDelayOnHover: true,
												continueDelayOnInactiveTab: false,
												icon: false,
												messageHeight: '90vh',
												msg: 'เลขตัวถัง '+STRNO+' มีอยู่ในรายการแล้ว'
											});
										}else{
											var display = $('#add_EMPCARRY').find(':selected').text();
											var valued = $('#add_EMPCARRY').find(':selected').val();	
											
											var row = '<tr seq="new'+generate+'">';
											row += '<td><button class="delSTRNO btn btn-xs btn-danger btn-block" seq="new'+generate+'">ยกเลิก</button></td>';
											row += '<td>'+STRNO+'</td>';
											row += '<td>'+MODEL+'</td>';
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
								}else{
									Lobibox.notify('error', {
										title: 'ผิดพลาด',
										closeOnClick: false,
										delay: 5000,
										pauseDelayOnHover: true,
										continueDelayOnInactiveTab: false,
										icon: false,
										messageHeight: '90vh',
										msg: data.html
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
		
		if(stat){  }else{ $('#addSTRNO').attr('locat',''); }	
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
			
			$('#tab22Body').html('');
			
			$.ajax({
				url:'../SYS02/Cautotransferscars/calcurate',
				data: dataToPost,
				type: 'POST',
				dataType: 'json',
				success: function(data) {
					if( data.status ){
						$('#tab22Body').html(data.html);
						
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
							closeOnClick: false,
							delay: 5000,
							pauseDelayOnHover: true,
							continueDelayOnInactiveTab: false,
							icon: false,
							messageHeight: '90vh',
							msg: data.html
						});
					}
					
				}
			});
		}else{
			Lobibox.notify('error', {
				title: 'ผิดพลาด',
				closeOnClick: false,
				delay: 5000,
				pauseDelayOnHover: true,
				continueDelayOnInactiveTab: false,
				icon: false,
				messageHeight: '90vh',
				msg: 'ไม่พบรายการรถที่ต้องการจัดลงสาขา'
			});
		}
		
	});
	
	$('#tab22processCar[name=tab22]').click(function(){
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
	
	$('#tab33Back[name=tab33]').click(function(){
		$('.wizard-tabs li').each(function(){
			$('.wizard-tabs li').removeClass('active');
		});
		if(!$('.wizard-tabs li').eq(1).hasClass('active')){
			$('.wizard-tabs li').eq(1).addClass('active');
		}
		
		var $id = $('.wizard-tabs li').eq(2).find('a').attr('prev').replace('#','');
		var $tabContent = $('.tab-content');
		$tabContent.find('.tab-pane').removeClass('active');
		$tabContent.find('.tab-pane[name='+$id+']').addClass('active');
	});
}