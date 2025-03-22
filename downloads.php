<?php
if (isset($_REQUEST['files'])){
$file = "pan_doc/".str_ireplace("../","",$_REQUEST['files']).""; 
$allowedExts = array('gif', 'png', 'jpg', 'jpeg', 'pdf', 'exe', 'xlsx', 'svg');
$ext = pathinfo($_REQUEST['files'], PATHINFO_EXTENSION);
if(file_exists($file)==1 && in_array($ext, $allowedExts)){
if($ext=='pdf'){
header('Content-type: application/pdf');	
}else{
header('Content-type:image/png');		
}

header("Content-Disposition: inline; filename=\"". basename($file) ."\""); 
header('Content-Transfer-Encoding: binary');
header('Accept-Ranges: bytes');

@readfile ($file);
exit(); 	
	
} else { 
header("Location: pan_doc/docs.html");
exit(); 
}	
	


} else { 
header("Location: pan_doc/docs.html");
exit(); 
}

?>