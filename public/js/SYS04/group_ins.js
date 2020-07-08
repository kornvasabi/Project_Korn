/********************************************************
             ______@09/05/2020______
			 Pasakorn Boonded

********************************************************/
var _locat  = $('.k_tab1[name="home"]').attr('locat');
var _insert = $('.k_tab1[name="home"]').attr('cin');
var _update = $('.k_tab1[name="home"]').attr('cup');
var _delete = $('.k_tab1[name="home"]').attr('cdel');
var _level  = $('.k_tab1[name="home"]').attr('clev');
var _today  = $('.k_tab1[name="home"]').attr('today');

$(function(){
	$('#ARGCOD').select2({
		placeholder: 'เลือก',
		ajax: {
			url: '../Cselect2K/getGROUP1',
			data: function (params){
				dataToPost = new Object();
				dataToPost.q = (typeof params.term === 'undefined' ? '' : params.term);
				dataToPost.locat = (typeof $('#LOCAT').find(':selected').val() === 'undefined' ? "":$('#LOCAT').find(':selected').val());
				dataToPost.vatstop = "cancel";
				
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
	$('#GRDCOD').select2({
		placeholder: 'เลือก',
		ajax: {
			url: '../Cselect2K/getGRADE',
			data: function (params){
				dataToPost = new Object();
				dataToPost.q = (typeof params.term === 'undefined' ? '' : params.term);
				dataToPost.locat = (typeof $('#LOCAT').find(':selected').val() === 'undefined' ? "":$('#LOCAT').find(':selected').val());
				dataToPost.vatstop = "cancel";
				
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
	$('#ARGCOD').change(function(){
		$('#btnreport').attr('disabled',true);
	});
	$('#btnreport').attr('disabled',true);
});
$('#btnsearch').click(function(){
	fn_Search();
});
var searchHC = null;
function fn_Search(){
	dataToPost = new Object();
	dataToPost.ARGCOD    = (typeof $('#ARGCOD').find(":selected").val() === "undefined" ? "":$('#ARGCOD').find(':selected').val());
	dataToPost.GRDCOD    = (typeof $('#GRDCOD').find(":selected").val() === "undefined" ? "":$('#GRDCOD').find(':selected').val());
	dataToPost.AUMPCOD   = (typeof $('#AUMPCOD').find(":selected").val() === "undefined" ? "":$('#AUMPCOD').find(':selected').val());
	dataToPost.PROVCOD   = (typeof $('#PROVCOD').find(":selected").val() === "undefined" ? "":$('#PROVCOD').find(':selected').val());
	$('#loadding').fadeIn(250);
	searchHC = $.ajax({
		url: '../SYS04/Question/Search_group_ins',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			$('#loadding').fadeOut(250);
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
				$('#btnreport').attr('disabled',false);
				$('#HistroryCustomer').html(data.html);
				$('#table-hiscus').on('draw.dt',function(){ redraw(); });
				
				fn_datatables('table-hiscus',1,350,'NO');
				//fn_datatables('table-hiscus',1,350);
				
				$('.dataTables_scrollBody').css({'height':'calc(-400px + 100vh)'});
				
				$('#ADDRS').click(function(){
					dataToPost = new Object();
					dataToPost.cuscod = $(this).attr('cuscod');
					$('#loadding').fadeIn(250);
					$.ajax({
						url: '../SYS04/Question/Address',
						data: dataToPost,
						type: 'POST',
						dataType: 'json',
						success: function(data){
							$('#loadding').fadeOut(250);
							Lobibox.window({
								title: 'ADDRESS',
								width: 700,
								height: 300,
								content: data.html,
								draggable: false,
								closeOnEsc: false,
								shown: function($this){
									
								}
							});
						}
					});
				});
			}
			
			searchHC = null;
		},
		beforeSend: function(){
			if(searchHC !== null){searchHC.abort();}
		}
	});
}
function redraw(){
	$('.getit').hover(function(){
		$(this).css({'background-color':'#a9a9f9'});
		$('.trow[seq='+$(this).attr('seq')+']').css({'background-color':'#a9f9f9'});
	},function(){
		$(this).css({'background-color':'white'});
		$('.trow[seq='+$(this).attr('seq')+']').css({'background-color':'white'});
	});
	$('.getit').unbind("clikc");
	$('.getit').click(function(){
		var b = []; 
		var cuscod = $(this).attr('CUSCOD');
		var addrno = $(this).attr('ADDRNO');
		b.push(cuscod);
		b.push(addrno);
		//alert(b);
		dataToPost = new Object();
		dataToPost.ADDR = b;
		$('#loadding').fadeIn(250);
		$.ajax({
			url: '../SYS04/Question/ChangeAddr',
			data: dataToPost,
			type: 'POST',
			dataType: 'json',
			success: function(data){
				$('#loadding').fadeOut(250);
				$('#ADDRS').each(function(){
					$(this).attr('cuscod',data.CUSCOD);
				});
				$('#ADDR1').val(data.ADDR1);
				$('#ADDR2').val(data.ADDR2);
				$('#TUMB').val(data.TUMB);
				$('#ZIP').val(data.ZIP);
				$('#TELP').val(data.TELP);
				$('#AUMPDES').val(data.AUMPDES);
				$('#PROVDES').val(data.PROVDES);
			}
		});
	});
}
$('#btnreport').click(function(){
	printReport();
});
var PReport = null;
function printReport(){
	dataToPost = new Object();
	dataToPost.ARGCOD    = (typeof $('#ARGCOD').find(":selected").val() === "undefined" ? "":$('#ARGCOD').find(':selected').val());
	dataToPost.GRDCOD    = (typeof $('#GRDCOD').find(":selected").val() === "undefined" ? "":$('#GRDCOD').find(':selected').val());
	dataToPost.AUMPCOD   = (typeof $('#AUMPCOD').find(":selected").val() === "undefined" ? "":$('#AUMPCOD').find(':selected').val());
	dataToPost.PROVCOD   = (typeof $('#PROVCOD').find(":selected").val() === "undefined" ? "":$('#PROVCOD').find(':selected').val());
	$('#loadding').fadeIn(250);
	PReport = $.ajax({
		url: '../SYS04/Question/conditiontopdf_group_ins',
		data: dataToPost,
		type: 'POST',
		dataType: 'json',
		success: function(data){
			//var baseUrl = $('body').attr('baseUrl');
			var url = '../SYS04/Question/pdf_group_ins?condpdf='+data[0];
			var content = "<iframe src='"+url+"' style='width:100%;height:100%;'></iframe>";
			$('#loadding').hide();
			Lobibox.window({
				title: 'พิมพ์รายงาน',
				content: content,
				closeOnEsc: false,
				height: $(window).height(),
				width: $(window).width()
			});
			PReport = null;
		},
		beforeSend: function(){
			if(PReport !== null){PReport.abort();}
		}
	});
}
