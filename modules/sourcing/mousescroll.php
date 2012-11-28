<!--
Copyright © 2012 Orkila International Offshore, All Rights Reserved

[Provide Short Descption Here]
$id: mousescroll.html
Created:        @[user.name]          |  
Last Update:    @tony.assaad    Nov 27, 2012 | 10:21:44 AM
-->
<!DOCTYPE html>
<html>
    <head>
        <title></title>
		<script src="http://127.0.0.1/web/development/ocos//js/jquery-current.min.js" type="text/javascript"></script>
        <style>
            #scroll{
                width: 300px;
                border-color:cadetblue;
                border-radius:500px;
				border: 2px solid black;
                background-color: lightyellow;
                top: 100px;
                left: 50px;
                position:absolute;

            }

        </style>
		
		<script>
		window.onload = function()
{
    //adding the event listerner for Mozilla
    if(window.addEventListener)
        document.addEventListener('DOMMouseScroll', moveObject, false);
 
    //for IE/OPERA etc
    document.onmousewheel = moveObject;
}
function moveObject(event)
{
    var delta = 0;
 
    if (!event) event = window.event;
 
    // normalize the delta
    if (event.wheelDelta) {
 
        // IE and Opera
        delta = event.wheelDelta / 60;
  
    } else if (event.detail) {
 
        // W3C
        delta = -event.detail / 2;
    }
 
    var currPos= $("#scroll").css('top') ; 

    //calculating the next position of the object
    currPos=parseInt(currPos)-(delta*10);
	if(currPos<=-5){
		preventDefault();
		
	}

    //moving the position of the object
   	$("#scroll").css('top' ,currPos+"px") ; 
	$("#scroll").html ('wheel move= '+delta + " : " + event.detail) ;
}
	
		</script>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    </head>
    <body>
        <div id="scroll"> </div>
    </body>
</html>
