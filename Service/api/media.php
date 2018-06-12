<?php
Header("Access-Control-Allow-Origin: * ");
Header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE");
include_once('../include/config.php');
include_once(SITE_ROOT.'/include/basehandler.hopback.php');
if(!isset($_FILES))output_json_error("No upload files detected!");
$media_type=RequestString('media_type','image');
$errors="";
$filepath="";
$fileurl="";
//$listname=array();
foreach ($_FILES as $names => $value) {
      //$listname[count($listname)]=$value['name'].'|'.$value['size'];
      $arr = explode(".", $value['name']);  
      $postfix = $arr[count($arr) - 1];  
      $tmpPath = $value['tmp_name'];  
      $tmpType = $value['type'];  
      $tmpSize = $value['size'];
        
      if ((($tmpType == "image/gif")
      || ($tmpType == "image/jpg")
      || ($tmpType == "image/jpeg")
      || ($tmpType == "image/png")
      || ($tmpType == "image/bmp")
      || ($tmpType == "image/pjpeg")))
      {
        if(($tmpSize > 2000000))$errors=$errors."文件最大限制为2M";
        if ($value["error"] > 0)
        {
          $errors=$errors."|Return Code: " . $value["error"];
        }
        else
        {
          $path="/"."$media_type/".date('Ym')."/";
          $name=date('dHis').rand(10000,99999).'.'.$postfix;
          $file_dir=dirname(dirname(dirname(__FILE__))).'/Media/hop'.$path;
          if(!file_exists($file_dir)) Create_folders($file_dir);
          move_uploaded_file($tmpPath,$file_dir.$name);
          $filepath=$filepath.HOP_MEDIA.$path.$name.",";
          $fileurl=$fileurl.$path.$name.",";
        }
      }
      else
      {
       $errors=$errors."What do you want to do with the uploaded file?";
      }
}
output_json(array('data'=>array('path'=>trim($filepath,','),'pathurl'=>trim($fileurl,','),'errors'=>$errors)));









// if(!isset($_FILES)||!isset($_FILES["file"]))output_json_error("No upload files detected!");
// $media_type=RequestString('media_type','image');
// if ((($_FILES["file"]["type"] == "image/gif")
// || ($_FILES["file"]["type"] == "image/jpg")
// || ($_FILES["file"]["type"] == "image/jpeg")
// || ($_FILES["file"]["type"] == "image/png")
// || ($_FILES["file"]["type"] == "image/bmp")
// || ($_FILES["file"]["type"] == "image/pjpeg")))
// {
//     if(($_FILES["file"]["size"] > 2000000))output_json_error("文件最大限制为2M");
//   if ($_FILES["file"]["error"] > 0)
//     {
//     output_json_error("Return Code: " . $_FILES["file"]["error"]);
//     }
//   else
//     {
//       $path="/"."jefferson/$media_type/".date('Ym')."/";
//       $name=date('dHis').rand(10000,99999).'.png';
//       $file_dir=dirname(dirname(dirname(__FILE__))).'/Media'.$path;
//       if(!file_exists($file_dir)) Create_folders($file_dir);
//       move_uploaded_file($_FILES["file"]["tmp_name"],$file_dir.$name);
//       output_json(array('data'=>array('path'=>PATH_MEDIA.$path.$name,'pathurl'=>$path.$name)));
//     }
//   }
// else
//   {
//     output_json_error("What do you want to do with the uploaded file?|||".$_FILES["file"]['type']);
//   }