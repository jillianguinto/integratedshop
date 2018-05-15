<?php
$filename = 'apiFolder/'.$_POST['filename'];


$htm = '';
$htm .= '<?php ';
$htm .= PHP_EOL;
$htm .= "define('token','".$_POST['token']."', true);";
$htm .= PHP_EOL;
$htm .= "define('bot_id','".$_POST['bot_id']."', true);";
$htm .= PHP_EOL;
$htm .= "include('viberapi.php');";
$htm .= PHP_EOL;
$htm .= '?>';

file_put_contents($filename,$htm);
chmod($filename,0777);
echo "success";
?>