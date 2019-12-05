





var jdbtnt1search=null;
$('#btnt1search').click(function(){ 
	dataToPost = new Object();
	fnSearch(dataToPost); 
});

function fnSearch(data){
	jdbtnt1search = $.ajax({
		url:'../SYS06/RevPayment/Search',
		data: data,
		type:'POST',
		dataType:'json',
		success: function(data){
			Lobibox.window({
				title: 'File PDF',
				width: $(window).width(),
				height: $(window).height(),
				content: data.html,
				draggable: false,
				closeOnEsc: true,
				shown: function($thisPDF){
					
				}
			});
			
			jdbtnt1search = null;
		},
		beforeSend: function(){ if(jdbtnt1search !== null){ jdbtnt1search.abort(); } },
		error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
	});
}




var jdbtnt1revpayment=null;
$('#btnt1revpayment').click(function(){ 
	dataToPost = new Object();
	fnForm(dataToPost); 
});

function fnForm(data){
	
	jdbtnt1revpayment = $.ajax({
		url:'../SYS06/RevPayment/get_form_received',
		data: data,
		type:'POST',
		dataType:'json',
		success: function(data){
			Lobibox.window({
				title: 'แบบฟอร์มรับชำระ',
				width: $(window).width(),
				height: $(window).height(),
				content: data.html,
				draggable: false,
				closeOnEsc: true,
				shown: function($thisPDF){
					
				}
			});
			
			jdbtnt1revpayment = null;
		},
		beforeSend: function(){ if(jdbtnt1revpayment !== null){ jdbtnt1revpayment.abort(); } },
		error: function(jqXHR, exception){ fnAjaxERROR(jqXHR,exception); }
	});
}


