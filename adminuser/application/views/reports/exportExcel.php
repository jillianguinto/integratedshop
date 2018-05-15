<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
</head>
<?php
$file="detailsreport.csv";
header("Content-Type: application/vnd.ms-excel;charset=UTF-8");
header("Content-Transfer-Encoding: binary");
header("Content-Language: en");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Content-Disposition: attachment; filename=$file");
echo $getData;
?>