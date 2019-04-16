/********************************************************
             ______@25/02/2019______
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

$('#btnt1transferPendding').click(function(){
	dataToPost = new Object();
	dataToPost.tab11prov1 = [$('#tab11prov1').is(':checked'),$('#tab11prov1').val()];
	dataToPost.tab11prov2 = [$('#tab11prov2').is(':checked'),$('#tab11prov2').val()];
	dataToPost.tab11prov3 = [$('#tab11prov3').is(':checked'),$('#tab11prov3').val()];
	dataToPost.tab11prov4 = [$('#tab11prov4').is(':checked'),$('#tab11prov4').val()];
	dataToPost.tab11prov5 = [$('#tab11prov5').is(':checked'),$('#tab11prov5').val()];
	dataToPost.LOCAT  	= $('#LOCAT').val();
	dataToPost.MODEL	= $('#MODEL').val();
	dataToPost.BAAB   	= $('#BAAB').val();
	dataToPost.COLOR	= $('#COLOR').val();
	dataToPost.CONDCal	= $('#CONDCal').find(':selected').val();
	dataToPost.CONDSort	= $('#CONDSort').find(':selected').val();
	dataToPost.sort 	= $('.sort[name=maxmin]:checked').val();
	
	$('#loadding').show();

	$.ajax({
		url: '../SYS02/CReport/MaxstockCompareSearch',
		data: dataToPost,
		Type: 'POST',
		dataType:'json',
		success: function(data){
			$('#loadding').hide();			
			
			Lobibox.window({
				title: 'รายงานการโอนย้ายรถ',
				content: data.html,
				height: $(window).height(),
				width: $(window).width(),
				closeOnEsc: false,
				draggable: false
			});
			
			document.getElementById("table-fixed-TransfersPenddingSearch").addEventListener("scroll", function(){
				var translate = "translate(0,"+(this.scrollTop - 1)+"px)";
				this.querySelector("thead").style.transform = translate;						
			});
			
			$('#table-TransfersPenddingSearch tbody tr').hover(function(){
				$(this).css({
					'color':'blue',
					'background-color':'#ccc',
					'font-weight':'bold',
					'cursor':'pointer'
					
				});
			},function(){
				$(this).css({
					'color':'#000',
					'background-color':'#fff',
					'font-weight':'normal',
					'cursor':'default'
				});
			});
		}
	});
});