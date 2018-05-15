<?php 
// echo "HELLO THERE! FALSE HERE.";
//$iconurl = Mage::getBaseurl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . "images/unauthorized.jpg";
?>

<link href="https://fonts.googleapis.com/css?family=Lato:900" rel="stylesheet">

<style>

body.oneshop-unauthorized-index{
	background-color: #BBF5F4;
}

.unathorized-container{
	max-width: 940px;
	width: 100%;
	height: auto;
	margin: 0 auto;
}

.content{
    position: relative;
    margin-top: 10%;
    text-align: center;
}

.content h1{
    font-family: 'Lato', sans-serif;
    color: #004846;
    font-size: 33px;
    padding-bottom: 2%;
    line-height: 1.5;
}

.content a{
	 font-family: 'Lato', sans-serif;
	text-decoration: none;
    color: #FF002D;
    font-size: 25px;
}

</style>
<body style="background: #BBF5F4;">
	<div class="unathorized-container">
		<div class="content">
			<h1>YOU ARE NOT AUTHORIZED<br/> TO VIEW THIS SITE</h1>
			<img src="/media/you-are-not-authorized.png" alt="Not Authorized" border="0"/>

			<h4><a href="#" onclick="javascript:window.close();opener.window.focus();" >Exit to Window</a></h4>
			
		</div>
	</div>
</div>

<script language="javascript" type="text/javascript"> 
	function windowClose() { 
	window.open('','_parent',''); 
	window.close();
	} 
</script>