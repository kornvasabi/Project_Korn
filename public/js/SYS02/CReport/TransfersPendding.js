/********************************************************
             ______@--/02/2019______
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

var jdbtnt1transferPendding=null;
$('#btnt1transferPendding').click(function(){
	dataToPost = new Object();
	dataToPost.TRANSDTs  = $('#TRANSDTs').val();
	dataToPost.TRANSDTe  = $('#TRANSDTe').val();
	dataToPost.TRANSFM   = $('#TRANSFM').val();
	dataToPost.TRANSTO	 = $('#TRANSTO').val();
	dataToPost.TRANSSTAT = $('#TRANSSTAT').val();
	
	$('#loadding').fadeIn(200);

	jdbtnt1transferPendding = $.ajax({
		url: '../SYS02/CReport/TransfersPenddingSearch',
		data: dataToPost,
		Type: 'POST',
		dataType:'json',
		success: function(data){
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
			
			jdbtnt1transferPendding = null;
			$('#loadding').fadeOut(200);			
		},
		beforeSend: function(){ if(jdbtnt1transferPendding !== null){ jdbtnt1transferPendding.abort(); } },
		error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
	});
});