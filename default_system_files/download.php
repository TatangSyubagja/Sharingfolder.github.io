<?php
/***********************************************************************************************************
* File System using Ajax, Jquery and PHP v4.0 
* Written by Vasplus Programming Blog
* Website: www.vasplus.info
* Email: info@vasplus.info

**********************************Copyright Information*****************************************************
* This script has been released with the aim that it will be useful.
* Please, do not remove this copyright information from the top of this page 
  including the Powered by Vasplus Programming Blog Icon.
* If you want the copyright info including the Powered by Vasplus Programming Blog icon 
  to be removed from the script then you have to buy this script.
* This script must not be used for commercial purpose without the consent of Vasplus Programming Blog.
* This script must not be sold.
* All Copy Rights Reserved by Vasplus Programming Blog
*************************************************************************************************************/
session_start();
If(isset($_GET['vpb_download_item']))
{
	define('ALLOWED_REFERRER', '');
	define('BASE_DIR','../');
	define('LOG_DOWNLOADS',true);
	define('LOG_FILE','default_system_files/downloads.log');
	$allowed_ext = array (
	  // archives
	  'zip' => 'application/zip',
	
	  // documents
	  'pdf' => 'application/pdf',
	  'doc' => 'application/msword',
	  'docx' => 'application/msword',
	  'xls' => 'application/vnd.ms-excel',
	  'ppt' => 'application/vnd.ms-powerpoint',
	  'txt' => 'text/plain',
	  'php' => 'application/php',
	  'html' => 'application/html',
	  'htm' => 'application/htm',
	  'js' => 'application/js',
	  'css' => 'application/css',
	  
	  // executables
	  'exe' => 'application/octet-stream',
	
	  // images
	  'gif' => 'image/gif',
	  'png' => 'image/png',
	  'jpg' => 'image/jpeg',
	  'jpeg' => 'image/jpeg',
	
	  // audio
	  'mp3' => 'audio/mpeg',
	  'wav' => 'audio/x-wav',
	
	  // video
	  'mpeg' => 'video/mpeg',
	  'mpg' => 'video/mpeg',
	  'mpe' => 'video/mpeg',
	  'mov' => 'video/quicktime',
	  'avi' => 'video/x-msvideo'
	);
	
	
	// If hotlinking not allowed then make hackers think there are some server problems
	if (ALLOWED_REFERRER !== ''
	&& (!isset($_SERVER['HTTP_REFERER']) || strpos(strtoupper($_SERVER['HTTP_REFERER']),strtoupper(ALLOWED_REFERRER)) === false)
	) {
		echo '<html><head><title>vasPLUS Programming Blog</title></head><body><center>';
		
		echo "<div class='notice' style='font-family:Verdana, Geneva, sans-serif; font-size:13px; color:black;'>Sorry, Internal server error. Thanks...</div>";
		
		die('<p style="height:800px;">&nbsp;</p></center></body></html>');
	}
	
	// Make sure program execution doesn't time out
	// Set maximum script execution time in seconds (0 means no limit)
	set_time_limit(0);
	
	if (!isset($_GET['vpb_download_item']) || empty($_GET['vpb_download_item'])) 
	{
		echo '<html><head><title>vasPLUS Programming Blog</title></head><body><center>';
		
		echo "<div style='font-family:Verdana, Geneva, sans-serif; font-size:13px; color:black;'>Sorry, No file is specified for download. Thanks...</div>"; 
		
		die('<p style="height:800px;">&nbsp;</p></center></body></html>');
	}
	
	// Nullbyte hack fix
	if (strpos($_GET['vpb_download_item'], "\0") !== FALSE) die('');
	
	// Get real file name.
	// Remove any path info to avoid hacking by adding relative path, etc.
	$fname = basename($_GET['vpb_download_item']);
	
	// Check if the file exists
	// Check in subfolders too
	function find_file ($dirname, $fname, &$file_path) {
	
	  $dir = opendir($dirname);
	
	  while ($file = readdir($dir)) {
		if (empty($file_path) && $file != '.' && $file != '..') {
		  if (is_dir($dirname.'/'.$file)) {
			find_file($dirname.'/'.$file, $fname, $file_path);
		  }
		  else {
			if (file_exists($dirname.'/'.$fname)) {
			  $file_path = $dirname.'/'.$fname;
			  return;
			}
		  }
		}
	  }
	
	} // find_file
	
	// get full file path (including subfolders)
	$file_path = '';
	find_file(BASE_DIR, $fname, $file_path);
	
	if (!is_file($file_path)) 
	{
		echo '<html><head><title>vasPLUS Programming Blog</title></head><body><center>';
		
	  echo "<div class='notice' style='font-family:Verdana, Geneva, sans-serif; font-size:13px; color:black;'>Sorry, the file that you are about to download does not exist. Thanks...</div>";
	  
	  die('<p style="height:800px;">&nbsp;</p></center></body></html>'); 
	}
	
	// file size in bytes
	$fsize = filesize($file_path); 
	
	// file extension
	$fext = strtolower(substr(strrchr($fname,"."),1));
	
	// check if allowed extension
	if (!array_key_exists($fext, $allowed_ext)) 
	{
		echo '<html><head><title>vasPLUS Programming Blog</title></head><body><center>';
		
		echo "<div class='notice' style='font-family:Verdana, Geneva, sans-serif; font-size:13px; color:black;'>Sorry, that file is not allowed for download. Thanks...</div>"; 
		
		die('<p style="height:800px;">&nbsp;</p></center></body></html>');
	}
	
	// get mime type
	if ($allowed_ext[$fext] == '') {
	  $mtype = '';
	  // mime type is not set, get from server settings
	  if (function_exists('mime_content_type')) {
		$mtype = mime_content_type($file_path);
	  }
	  else if (function_exists('finfo_file')) {
		$finfo = finfo_open(FILEINFO_MIME); // return mime type
		$mtype = finfo_file($finfo, $file_path);
		finfo_close($finfo);  
	  }
	  if ($mtype == '') {
		$mtype = "application/force-download";
	  }
	}
	else {
	  // get mime type defined by admin
	  $mtype = $allowed_ext[$fext];
	}
	
	// Browser will try to save file with this filename, regardless original filename.
	// You can override it if needed.
	
	if (!isset($_GET['fc']) || empty($_GET['fc'])) {
	  $asfname = $fname;
	}
	else {
	  // remove some bad chars
	  $asfname = str_replace(array('"',"'",'\\','/'), '', $_GET['fc']);
	  if ($asfname === '') $asfname = 'NoName';
	}
	
	// set headers
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: public");
	header("Content-Description: File Transfer");
	header("Content-Type: $mtype");
	header("Content-Disposition: attachment; filename=\"$asfname\"");
	header("Content-Transfer-Encoding: binary");
	header("Content-Length: " . $fsize);
	
	// download
	// @readfile($file_path);
	$file = @fopen($file_path,"rb");
	if ($file) {
	  while(!feof($file)) {
		print(fread($file, 1024*8));
		flush();
		if (connection_status()!=0) {
		  @fclose($file);
		  die();
		}
	  }
	  @fclose($file);
	}
	
	// log downloads
	if (!LOG_DOWNLOADS) die();
	
	$f = @fopen(LOG_FILE, 'a+');
	if ($f) {
	  @fputs($f, date("m.d.Y g:ia")."  ".$_SERVER['REMOTE_ADDR']."  ".$fname."\n");
	  @fclose($f);
	}
}
else
{
	echo '<html><head><title>vasPLUS Programming Blog</title></head><body><center>';

	echo "<div style='font-family:Verdana, Geneva, sans-serif; font-size:13px; color:black;'>Sorry, something went wrong and your download was unsuccessful. Please try again. Thanks...</div>"; 
	
	die('<p style="height:800px;">&nbsp;</p></center></body></html>');
}
?>