/********************************************************
             ______@14/01/2020______
			 Pasakorn Boonded

********************************************************/
var _locat  = $('.k_tab1[name="home"]').attr('locat');
var _insert = $('.k_tab1[name="home"]').attr('cin');
var _update = $('.k_tab1[name="home"]').attr('cup');
var _delete = $('.k_tab1[name="home"]').attr('cdel');
var _level  = $('.k_tab1[name="home"]').attr('clev');
var _today  = $('.k_tab1[name="home"]').attr('today');

$(function(){
	$('#LOCATRECV').select2({
		placeholder: 'เลือก',
		ajax: {
			url: '../Cselect2K/getLOCAT',
			data: function (params){
				dataToPost = new Object();
				dataToPost.q = (typeof params.term === 'undefined' ? '' : params.term);
				
				return dataToPost;
			},
			dataType: 'json',
			delay: 1000,
			processResults: function (data){
				return {
					results: data
				};
			},
			cache: true
		},
		allowClear: true,
		multiple: false,
		dropdownParent: $(".k_tab1"),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	$('#LOCAT').select2({
		placeholder: 'เลือก',
		ajax: {
			url: '../Cselect2K/getLOCAT',
			data: function (params){
				dataToPost = new Object();
				dataToPost.q = (typeof params.term === 'undefined' ? '' : params.term);
				
				return dataToPost;
			},
			dataType: 'json',
			delay: 1000,
			processResults: function (data){
				return {
					results: data
				};
			},
			cache: true
		},
		allowClear: true,
		multiple: false,
		dropdownParent: $(".k_tab1"),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	$('#BILLCOLL').select2({
		placeholder: 'เลือก',
		ajax: {
			url: '../Cselect2K/getOFFICER',
			data: function (params){
				dataToPost = new Object();
				dataToPost.q = (typeof params.term === 'undefined' ? '' : params.term);
				
				return dataToPost;
			},
			dataType: 'json',
			delay: 1000,
			processResults: function (data){
				return {
					results: data
				};
			},
			cache: true
		},
		allowClear: true,
		multiple: false,
		dropdownParent: $(".k_tab1"),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	$('#PAYTYP').select2({
		placeholder: 'เลือก',
		ajax: {
			url: '../Cselect2K/getPAYTYP',
			data: function (params){
				dataToPost = new Object();
				dataToPost.q = (typeof params.term === 'undefined' ? '' : params.term);
				
				return dataToPost;
			},
			dataType: 'json',
			delay: 1000,
			processResults: function (data){
				return {
					results: data
				};
			},
			cache: true
		},
		allowClear: true,
		multiple: false,
		dropdownParent: $(".k_tab1"),
		//disabled: true,
		//theme: 'classic',
		width: '100%'
	});
	$('#AUMPCOD').select2({        //อำเภอ
        placeholder: 'เลือก',
        ajax: {
            url: '../Cselect2K/getAUMPCOD',
            data: function (params) {
                dataToPost = new Object();
                dataToPost.now = (typeof $('#AUMPCOD').find(':selected').val() === 'undefined' ? "":$('#AUMPCOD').find(':selected').val());
                dataToPost.q = (typeof params.term === 'undefined' ? '' : params.term);
                dataToPost.provcod = (typeof $('#PROVCOD').find(':selected').val() === 'undefined' ? "":$('#PROVCOD').find(':selected').val()); //จังหวัด

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
        dropdownParent: $('#AUMPCOD').parent().parent(),
        //disabled: true,
        //theme: 'classic',
        width: '100%'
    });
    $('#PROVCOD').select2({      //จัดหวัด
        placeholder: 'เลือก',
        ajax: {
            url: '../Cselect2K/getPROVCOD',
            data: function (params) {
                dataToPost = new Object();
                dataToPost.now = (typeof $('#PROVCOD').find(':selected').val() === 'undefined' ? "":$('#PROVCOD').find(':selected').val()); 
                dataToPost.q = (typeof params.term === 'undefined' ? '' : params.term);
                dataToPost.aumpcod = (typeof $('#AUMPCOD').find(':selected').val() === 'undefined' ? "":$('#AUMPCOD').find(':selected').val()); //อำเภอ
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
        dropdownParent: $('#PROVCOD').parent().parent(),
        //disabled: true,
        //theme: 'classic',
        width: '100%'
    });
	$('#PROVCOD').on("select2:select",function(){ //เลือกอำเภอโชว์จังหวัด
        $('#AUMPCOD').val(null).trigger('change');
    });
    var JDjaump = null;
    $('#AUMPCOD').on("select2:select",function(){
        dataToPost = new Object();
        dataToPost.aumpcod = (typeof $('#AUMPCOD').find(":selected").val() === "undefined" ? "":$('#AUMPCOD').find(":selected").val());
        JDjaump = $.ajax({
            url: '../Cselect2K/getProv',
            data: dataToPost,
            type: "POST",
            dataType: "json",
            success: function(data) {
                var newOption = new Option(data.PROVDES, data.PROVCOD, false, false);
                $('#PROVCOD').empty().append(newOption).trigger('change');
            },
            beforeSend: function(){
                if(JDjaump != null){
                    JDjaump.abort();
                }
            }
        });
    });
});
$('#btnreportBC').click(function(){
	printReport();
});
function printReport(){
	var order = "";
	if($('#OR1').is(':checked')){
		order = "CONTNO";
	}else if($('#OR2').is(':checked')){
		order = "LOCAT";
	}else if($('#OR3').is(':checked')){
		order = "CUSCOD";
	}else if($('#OR4').is(':checked')){
		order = "BILLCOLL"; 
	}else if($('#OR5').is(':checked')){
		order = "PAYDT"; 
	}else if($('#OR6').is(':checked')){
		order = "DDATE"; 
	}else if($('#OR7').is(":checked")){
		order = "LOCATRECV";
	}
	dataToPost = new Object();
	dataToPost.LOCATRECV = (typeof $('#LOCATRECV').find(':selected').val() === 'undefined' ? '':$('#LOCATRECV').find(':selected').val());
	dataToPost.LOCAT     = (typeof $('#LOCAT').find(':selected').val() === 'undefined' ? '':$('#LOCAT').find(':selected').val());	
	dataToPost.DATE1     = $('#DATE1').val();
	dataToPost.DATE2     = $('#DATE2').val();
	dataToPost.BILLCOLL  = (typeof $('#BILLCOLL').find(':selected').val() === 'undefined' ? '':$('#BILLCOLL').find(':selected').val());
	dataToPost.PAYTYP    = (typeof $('#PAYTYP').find(':selected').val() === 'undefined' ? '':$('#PAYTYP').find(':selected').val());
	dataToPost.TUMB		 = $('#TUMB').val();	
	dataToPost.AUMPCOD   = (typeof $('#AUMPCOD').find(':selected').val() === 'undefined' ? '':$('#AUMPCOD').find(':selected').val());
	dataToPost.PROVCOD   = (typeof $('#PROVCOD').find(':selected').val() === 'undefined' ? '':$('#PROVCOD').find(':selected').val());
	dataToPost.PERSEN    = $('#PERSEN').val();
	dataToPost.order	 = order;
	$.ajax({
		url: '../SYS06/ReportReceivedBC/conditiontopdf',
		data: dataToPost,
		type:'POST',
		dataType: 'json',
		success: function(data){
			var baseUrl = $('body').attr('baseUrl');
			var url = baseUrl+'/SYS06/ReportReceivedBC/pdf?condpdf='+data[0];
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