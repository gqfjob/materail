<?php
require "resizeimage.php"; 
// Define a destination
$targetFolder = '/assets/upload/doc'; // Relative to the root

//$verifyToken = md5('unique_salt' . $_POST['timestamp']);

//if (!empty($_FILES) && $_POST['token'] == $verifyToken) {
if (!empty($_FILES)) {
	$tempFile = $_FILES['Filedata']['tmp_name'];
	$targetPath = $_SERVER['DOCUMENT_ROOT'] . $targetFolder;
	//$targetFile = rtrim($targetPath,'/') . '/org/' . $_FILES['Filedata']['name'];
	$size = $_FILES['Filedata']['size'];
	//debug_log($_FILES['Filedata']);
	// Validate the file type
	$fileTypes = array('doc','zip','pdf','xls','ppt','txt','docx','pptx','xlsx','rar'); // File extensions
	$fileParts = pathinfo($_FILES['Filedata']['name']);
	//$orgName = mb_convert_encoding($fileParts['filename'],'UTF-8','GBK,gb2312');
	//$orgName = $fileParts['filename'];
	$orgName = $_FILES['Filedata']['name'];
	$filename = random_string('md5').'.'.$fileParts['extension'];
	$targetFile = rtrim($targetPath,'/') . '/' . $filename;
	if (in_array($fileParts['extension'],$fileTypes)) {
		move_uploaded_file($tempFile,$targetFile);
		$res['displays'] = $orgName;
		$res['realname'] = $filename;
		$res['filesize'] = $size;
		echo json_encode($res);
	} else {
		echo 'error';
	}
}
?>