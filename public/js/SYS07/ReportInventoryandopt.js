//BEE+
// หน้าแรก  
var _locat  = $('.b_tab1[name="home"]').attr('locat');
var _insert = $('.b_tab1[name="home"]').attr('cin');
var _update = $('.b_tab1[name="home"]').attr('cup');
var _delete = $('.b_tab1[name="home"]').attr('cdel');
var _level  = $('.b_tab1[name="home"]').attr('clev');
var _today  = $('.b_tab1[name="home"]').attr('today');
//หน้าแรก
$(function(){
	$('#LOCAT1').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2b/getLOCAT',
			data: function (params) {
				dataToPost = new Object();
				//dataToPost.now = $('#add_cuscod').find(':selected').val();
				dataToPost.q = (typeof params.term === 'undefined' ? '' : params.term);
				
				return dataToPost;				
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
		dropdownParent: $(".b_tab1"),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	
	$('#OPTCOCE1').select2({
		placeholder: 'เลือก',
        ajax: {
			url: '../Cselect2b/getOPTION',
			data: function (params) {
				dataToPost = new Object();
				//dataToPost.now = $('#add_cuscod').find(':selected').val();
				dataToPost.q = (typeof params.term === 'undefined' ? '' : params.term);
				
				return dataToPost;				
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
		dropdownParent: $(".b_tab1"),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
});

$('#btnt1search').click(function(){
	search();
});

var reportsearch = null;
function search(){
	
	var report = "";
	if($("#notsumtran").is(":checked")){ 
		report = "notsumtran";
	}else if($("#sumtran").is(":checked")){
		report = "sumtran";
	}

	dataToPost = new Object();
	dataToPost.LOCAT1 		= (typeof $('#LOCAT1').find(':selected').val() === 'undefined' ? '':$('#LOCAT1').find(':selected').val());
	dataToPost.OPTCOCE1 	= (typeof $('#OPTCOCE1').find(':selected').val() === 'undefined' ? '':$('#OPTCOCE1').find(':selected').val());
	dataToPost.FRMDATE 		= $('#FRMDATE').val();
	dataToPost.TODATE 		= $('#TODATE').val();
	dataToPost.report 		= report;
	
	$('#loadding').show();
	reportsearch = $.ajax({
		url: '../SYS07/ReportInventoryandopt/search',
		data: dataToPost,
		Type: 'POST',
		dataType:'json',
		success: function(data){	
			$('#loadding').hide();	
			Lobibox.window({
				title: 'รายงานสินค้าและวัตถุดิบ (อุปกรณ์)',
				content: data.html,
				height: $(window).height(),
				width: $(window).width(),
				closeOnEsc: false,
				draggable: false
			});
			
			$('.lobibox-body').empty().append(data.html);
			
			document.getElementById("table-fixed-ReportInventoryandopt").addEventListener("scroll", function(){
				var translate = "translate(0,"+(this.scrollTop - 7)+"px)";
				this.querySelector("thead").style.transform = translate;
				this.querySelector("thead").style.zIndex = 100;
			});
	
			$('#H_ReportInventoryandopt').prepend('<img id="print-Inventoryandopt" src="../public/images/print-icon.png" style="width:30px;height:30px;cursor:pointer;filter: contrast(100%);">');
			$("#print-Inventoryandopt").hover(function(){
				document.getElementById("print-Inventoryandopt").style.filter = "contrast(70%)";
			}, function() {
				document.getElementById("print-Inventoryandopt").style.filter = "contrast(100%)";
			});
			
			$('#H_ReportInventoryandopt').prepend('<img id="table-Inventoryandopt-excel" src="../public/images/excel-icon.png" style="width:30px;height:30px;cursor:pointer;filter: contrast(100%);">');
			$("#table-Inventoryandopt-excel").hover(function() {
				document.getElementById("table-Inventoryandopt-excel").style.filter = "contrast(70%)";
			}, function() {
				document.getElementById("table-Inventoryandopt-excel").style.filter = "contrast(100%)";
			});
			
			$("#table-Inventoryandopt-excel").click(function(){ 
				tableToExcel_Export(data.report,"sheet 1","รายงานสินค้าและวัตถุดิบ (อุปกรณ์) "+data.reporttoday); 
			});
			
			$('#print-Inventoryandopt').click(function(){
				printReport();
			});

			reportsearch = null;
		},
		beforeSend: function(){
			if(reportsearch !== null){
				reportsearch.abort();
			}
		}
	});
}

function printReport(){
	var layout = "";
	if($("#ver").is(":checked")){ 
		layout = "A4";
	}else if($("#hor").is(":checked")){
		layout = "A4-L";
	}
	
	var report = "";
	if($("#notsumtran").is(":checked")){ 
		report = "notsumtran";
	}else if($("#sumtran").is(":checked")){
		report = "sumtran";
	}

	dataToPost = new Object();
	dataToPost.LOCAT1 		= (typeof $('#LOCAT1').find(':selected').val() === 'undefined' ? '':$('#LOCAT1').find(':selected').val());
	dataToPost.OPTCOCE1 	= (typeof $('#OPTCOCE1').find(':selected').val() === 'undefined' ? '':$('#OPTCOCE1').find(':selected').val());
	dataToPost.FRMDATE 		= $('#FRMDATE').val();
	dataToPost.TODATE 		= $('#TODATE').val();
	dataToPost.report 		= report;
	dataToPost.layout 		= layout;
	
	$.ajax({
		url: '../SYS07/ReportInventoryandopt/conditiontopdf',
		data: dataToPost,
		type:'POST',
		dataType: 'json',
		success: function(data){
			//alert(data[0]);
			var baseUrl = $('body').attr('baseUrl');
			var url = baseUrl+'SYS07/ReportInventoryandopt/pdf?condpdf='+data[0];
			var content = "<iframe src='"+url+"' style='width:100%;height:100%;'></iframe>";
			Lobibox.window({
				title: 'พิมพ์รายงาน',
				content: content,
				closeOnEsc: false,
				height: $(window).height(),
				width: $(window).width()
			});
		}
	});
}