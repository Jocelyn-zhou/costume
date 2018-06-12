<?php
$file_path = "/var/www/html/error_log";
//$file_path = "/var/log/httpd/error_log";
$contents='';
 if(file_exists($file_path)){
     $fp = fopen($file_path,"r");
     $str = fread($fp,filesize($file_path));//指定读取大小，这里把整个文件内容读取出来
     $contents = str_replace("\n","<br/><br />  ",$str);
     //var_dump($str);
 }else
     $contents =  'no file';
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<title>errors</title>
<meta name="description" content="">
<meta name="keywords" content="">
<link href="css/index.css" rel="stylesheet">
<script src="js/layer/mobile/layer.js"></script>
</head>
<body >
    <div style='font-size:14px; margin:2px; background-color:#C7EDCC'>
<?php 
    echo $contents; 
?>
</div>
<a name="error"></a><!--锚点处-->
 </body>
 <script>
window.location ="#error";//自动跳转到锚点处
</script>
</html>