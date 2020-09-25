Lobibox.window({
	title: 'ลายเซ็นต์',
	width: $(window).width(),
	height: $(window).height(),
	content: '<div id=\"lobi_main\">',
	draggable: false,
	closeOnEsc: false,
	shown: function($this){
		var width = $(window).width() - 45;
		var height = $(window).height() - 150;
		$('#lobi_main').append("<canvas id='signature-pad' class='signature-pad' width='"+width+"' height='"+height+"' style='border:0.1px dotted black;'></canvas>");
		
		$('#lobi_main').append("<input type='button' id='save' value='บันทึก' class='col-sm-4 btn btn-sm btn-primary'>");
		$('#lobi_main').append("<input type='button' id='undo' value='ย้อนกลับ' class='col-sm-4 btn btn-sm btn-info'>");
		$('#lobi_main').append("<input type='button' id='clear' value='ล้าง' class='col-sm-4 btn btn-sm btn-danger'>");
		
		
		var signaturePad = new SignaturePad(document.getElementById('signature-pad'), {
			backgroundColor: 'rgba(255, 255, 255, 0)',
			penColor: 'rgb(0, 0, 0)'
		});

		$('#save').click(function(){
			if (signaturePad.isEmpty()) {
				Lobibox.notify('info', {
					title: 'แจ้งเตือน',
					size: 'mini',
					closeOnClick: false,
					delay: false,
					pauseDelayOnHover: true,
					continueDelayOnInactiveTab: false,
					icon: true,
					messageHeight: '90vh',
					msg: 'เซ้นต์ก่อนตะหลวง'
				});
			} else {						
				var data = signaturePad.toDataURL('image/png');
				
				
				Lobibox.window({
					title: 'ลายเซ็นต์',
					width: $(window).width(),
					height: $(window).height(),
					content: '<img src=\"'+data+'\">',
					draggable: false,
					closeOnEsc: false							
				});
			}
		});

		$('#undo').click(function(){
			var data = signaturePad.toData();
			data.pop(); // remove the last dot or line
			signaturePad.fromData(data);
		});

		$('#clear').click(function(){
			signaturePad.clear();
		});
	}
});
