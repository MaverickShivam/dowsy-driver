<?php
if(isset($_POST["blob"]))
{
	$img = str_replace('data:image/png;base64,', '', $_POST["blob"]);
	$img = str_replace(' ', '+', $img);
	$data = base64_decode($img);
	file_put_contents('image.png', $data);
	echo "updated";
}
else
{
	echo "failed";
}
?>