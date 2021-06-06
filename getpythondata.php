<?php
if(isset($_GET["status"]))
{
	echo file_get_contents('status.text');
}
else
{
	echo file_get_contents('color.text');
}
?>