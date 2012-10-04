$(function() {
	/* $(".editable[id^='products/view_name_']").editable('index.php?moddule=products/edit&amp;do=editinline&amp;attribute=name&amp;id=1', { 
     loadurl : 'http://www.example.com/json.php',
     type   : 'select',
     submit : 'OK'
 	});*/
	 
	 $(".editableinline[id^='products/view_name_']").editable('index.php?moddule=products/edit&amp;do=editinline&amp;attribute=name&amp;id=1', { 
        indicator : '<img src="' + imagespath + '/loading.gif">',
		event     : "dblclick",
		style   : 'margin: 0px; display: block;'
     });
});