/********************************************************
             _____15/08/2563______
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
var _locat  = $('.tab1[name="home"]').attr('locat');

$('#btnCheck').click(function(){ getinfo(); });
$('#STRNO').keypress(function(e){ if(e.keyCode == 13) getinfo(); });

var OBJgetinfo=null;
function getinfo(){
	var dataToPost = new Object()
	dataToPost.strno = $('#STRNO').val();
	
	$('#loadding').fadeIn(200);	
	OBJgetinfo = $.ajax({
		url:'../SYS02/DealerServices/getInfo',
		data:dataToPost,
		type:'POST',
		dataType:'json',
		beforeSend: function(){ if(OBJgetinfo !== null){ OBJgetinfo.abort(); }},
		success:function(data){
			OBJgetinfo = null;
			$('#result').html(data.html);
			$('#loadding').fadeOut(200);
		}
	});
}