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



//This function show or displays the Move, Copy, Rename, Compress and Delete Files and Directories Pop-up Boxes
function vpb_directory_action(option_selected)
{
	var vpb_directory_or_file_name = $("#vpb_directory_or_file_name").val();
	var vpb_current_directory = $("#vpb_current_directory").val();
	
	if(option_selected == "move")
	{
		$("#directory_box").hide();
		$("#directory_box_move").show();
		$("#vpb_to_this_directory_move").val(vpb_current_directory);
		$("#vpb_this_directory_move").html(vpb_current_directory+vpb_directory_or_file_name.replace("/", ""));
		$("#vpb_processing_move_action").hide();
	}
	else if(option_selected == "copy")
	{
		$("#directory_box").hide();
		$("#directory_box_copy").show();
		$("#vpb_to_this_directory_copy").val(vpb_current_directory);
		$("#vpb_this_directory_copy").html(vpb_current_directory+vpb_directory_or_file_name.replace("/", ""));
		$("#vpb_processing_copy_action").hide();
	}
	else if(option_selected == "rename")
	{
		var fileExtensions = vpb_directory_or_file_name.replace("/", "").substring(vpb_directory_or_file_name.replace("/", "").lastIndexOf('.') + 1);
		$("#directory_box").hide();
		$("#directory_box_rename").show();
		$("#vpb_this_directory_rename").html(vpb_current_directory+vpb_directory_or_file_name.replace("/", ""));
		if(fileExtensions == vpb_directory_or_file_name.replace("/", ""))
		{
			$("#vpb_to_this_directory_rename").val(vpb_current_directory+'New-name');
		}
		else
		{
			$("#vpb_to_this_directory_rename").val(vpb_current_directory+'New-file-name.'+fileExtensions);
		}
		$("#vpb_processing_rename_action").hide();
	}
	else if(option_selected == "compress")
	{
		var new_compress_file_name = vpb_directory_or_file_name.replace("/", "");
		var fileExtensions = vpb_directory_or_file_name.replace("/", "").substring(vpb_directory_or_file_name.replace("/", "").lastIndexOf('.') + 1);
		
		$("#directory_box").hide();
		$("#directory_box_compress").show();
		$("#vpb_this_directory_compress").html(vpb_current_directory+vpb_directory_or_file_name.replace("/", ""));
		if(fileExtensions == vpb_directory_or_file_name.replace("/", ""))
		{
			$("#vpb_to_this_directory_compress").val(vpb_current_directory+new_compress_file_name+'.zip');
		}
		else
		{
			$("#vpb_to_this_directory_compress").val(vpb_current_directory+vpb_directory_or_file_name+'.zip');
		}
		$("#vpb_processing_compress_action").hide();
	}
	else if(option_selected == "delete")
	{
		$("#directory_box").hide();
		$("#directory_box_delete").show();
		$("#vpb_this_directory_delete").html(vpb_current_directory+vpb_directory_or_file_name.replace("/", ""));
		$("#directory_to_delete_name").html(vpb_current_directory+vpb_directory_or_file_name.replace("/", ""));
		$("#directory_or_file_to_delete_name").val(vpb_current_directory+vpb_directory_or_file_name.replace("/", ""));
		$("#vpb_processing_delete_action").hide();
	}
	else
	{
		//Unknown request brought
		$("#vpb_directory_options_box").hide(); //Hides the directory option box
		$("#vpb_file_system_background").fadeOut("slow"); //Hides the pop-up background
	}
}

//Submit Move, Copy, Rename, Compress, Delete and Create Files or Directories Actions
function vpb_directory_action_submission(option)
{
	var proceed = false;
	var vpb_directory_or_file_name = $("#vpb_directory_or_file_name").val();
	var vpb_current_directory = $("#vpb_current_directory").val();
	
	if(vpb_directory_or_file_name == "")
	{
		alert('The directory hidden input field is missing from the file default_system_index.php.\nPlease set a hidden input box in the file named default_system_index.php with an id="vpb_directory_or_file_name" to proceed.\nThanks...');
		return false;
	}
	else
	{
		if(option == "move")
		{
			proceed = true;
			var vpb_to_this_directory_move = $("#vpb_to_this_directory_move").val();
			
			var dataString = "page=move" + "&vpb_this_directory_or_file=" + vpb_current_directory+vpb_directory_or_file_name.replace("/", "") + "&vpb_to_this_directory=" + vpb_to_this_directory_move;
		}
		else if(option == "copy")
		{
			proceed = true;
			var vpb_to_this_directory_copy = $("#vpb_to_this_directory_copy").val();
			
			var dataString = "page=copy" + "&vpb_this_directory_or_file=" + vpb_current_directory+vpb_directory_or_file_name.replace("/", "") + "&vpb_to_this_directory=" + vpb_to_this_directory_copy;
		}
		else if(option == "rename")
		{
			proceed = true;
			var vpb_to_this_directory_rename = $("#vpb_to_this_directory_rename").val();
			
			var dataString = "page=rename" + "&vpb_this_directory_or_file=" + vpb_current_directory+vpb_directory_or_file_name.replace("/", "") + "&vpb_to_this_directory=" + vpb_to_this_directory_rename;
		}
		else if(option == "compress")
		{
			proceed = true;
			var vpb_to_this_directory_compress = $("#vpb_to_this_directory_compress").val();
			
			var dataString = "page=compress" + "&vpb_this_directory_or_file=" + vpb_current_directory+vpb_directory_or_file_name.replace("/", "") + "&vpb_to_this_directory=" + vpb_to_this_directory_compress;
		}
		else if(option == "delete")
		{
			proceed = true;
			var vpb_to_this_directory_delete = $("#vpb_to_this_directory_delete").val();
			var directory_or_file_to_delete_name = $("#directory_or_file_to_delete_name").val();
			
			var dataString = "page=delete" + "&vpb_this_directory_or_file=" + vpb_directory_or_file_name + "&vpb_to_this_directory=" + vpb_to_this_directory_delete + "&directory_or_file_to_delete_name=" + directory_or_file_to_delete_name;
		}
		else if(option == "create")
		{
			proceed = true;
			var vpb_to_this_directory_create = $("#vpb_to_this_directory_create").val();
			
			var dataString = "page=create" + "&vpb_this_directory_or_file=" + vpb_directory_or_file_name + "&vpb_to_this_directory=" + vpb_to_this_directory_create;
		}
		else
		{
			proceed = false;
			//Unknown Action
		}
		if(proceed == false)
		{
			alert('Sorry, we could not identify the action you were trying to perform. \nPlease try again or contact the site developer to report this error if the problem persist. \nThanks...');
			return false;
		}
		else
		{
			$.ajax({
				type: "POST",
				url: "default_system_actions.php",
				data: dataString, 
				cache: false,
				beforeSend: function() 
				{
					if(option == "move")
					{
						$("#vpb_processing_move_action").show().html('<div align="left" style="font-family:Arial, sans-serif; font-size:12px;">Please wait <img src="default_system_images/loading.gif" align="absmiddle" title="Loading..." /></div><br clear="all" />');
					}
					else if(option == "copy")
					{
						$("#vpb_processing_copy_action").show().html('<div align="left" style="font-family:Arial, sans-serif; font-size:12px;">Please wait <img src="default_system_images/loading.gif" align="absmiddle" title="Loading..." /></div><br clear="all" />');
					}
					else if(option == "rename")
					{
						$("#vpb_processing_rename_action").show().html('<div align="left" style="font-family:Arial, sans-serif; font-size:12px;">Please wait <img src="default_system_images/loading.gif" align="absmiddle" title="Loading..." /></div><br clear="all" />');
					}
					else if(option == "compress")
					{
						$("#vpb_processing_compress_action").show().html('<div align="left" style="font-family:Arial, sans-serif; font-size:12px;">Please wait <img src="default_system_images/loading.gif" align="absmiddle" title="Loading..." /></div><br clear="all" />');
					}
					else if(option == "delete")
					{
						$("#vpb_processing_delete_action").show().html('<div align="left" style="font-family:Arial, sans-serif; font-size:12px;">Please wait <img src="default_system_images/loading.gif" align="absmiddle" title="Loading..." /></div><br clear="all" />');
					}
					else if(option == "create")
					{
						$("#vpb_processing_create_action").show().html('<div align="left" style="font-family:Arial, sans-serif; font-size:12px;">Please wait <img src="default_system_images/loading.gif" align="absmiddle" title="Loading..." /></div><br clear="all" />');
					}
					else { /* Unknown Action */ }
				},
				success: function(response) 
				{
					if(option == "move")
					{
						var response_brought = response.indexOf('success');
						if(response_brought != -1)
						{
							$("#vpb_to_this_directory_move").val('');
							var vpb_new_directory_name = vpb_directory_or_file_name.replace("/", "");
							var df_without_extensions_or_slash = vpb_new_directory_name.substr(0, vpb_new_directory_name.lastIndexOf('.')) || vpb_new_directory_name;
							$("#vpb_processing_move_action").html(response);
							setTimeout(function() { $("#vpb_processing_move_action").hide(); vpb_hide_popup_options_boxes(); },3000);
							setTimeout(function() { $("#directory_id"+df_without_extensions_or_slash).fadeOut(); },3200);
						}
						else
						{
							$("#vpb_processing_move_action").html(response);
						}
					}
					else if(option == "copy")
					{
						var response_brought = response.indexOf('success');
						if(response_brought != -1)
						{
							$("#vpb_to_this_directory_copy").val('');
							$("#vpb_processing_copy_action").html(response);
							setTimeout(function() { $("#vpb_processing_copy_action").hide(); vpb_hide_popup_options_boxes(); },3000);
						}
						else
						{
							$("#vpb_processing_copy_action").html(response);
						}
					}
					else if(option == "rename")
					{
						var response_brought = response.indexOf('success');
						if(response_brought != -1)
						{
							var vpb_new_directory_name = vpb_directory_or_file_name.replace("/", "");
							var df_without_extensions_or_slash = vpb_new_directory_name.substr(0, vpb_new_directory_name.lastIndexOf('.')) || vpb_new_directory_name;
							
							$("#vpb_to_this_directory_rename").val('');
							$("#vpb_processing_rename_action").html('<div class="info" style="">Congrats, that was a success!<br><br><b>RENAMED:</b> '+vpb_current_directory+vpb_directory_or_file_name.replace("/", "")+'<br><br> <b>TO:</b> '+vpb_to_this_directory_rename+'</div><br clear="all">');
							
							setTimeout(function() { $("#vpb_processing_rename_action").hide(); vpb_hide_popup_options_boxes(); },3000);
							setTimeout(function() { $("#directory_id"+df_without_extensions_or_slash).fadeOut(); $("#renamed_files").prepend(response); },3200);
						}
						else
						{
							$("#vpb_processing_rename_action").html(response);
						}
					}
					else if(option == "compress")
					{
						var response_brought = response.indexOf('success');
						if(response_brought != -1)
						{	
							$("#vpb_to_this_directory_compress").val('');
							$("#vpb_processing_compress_action").html('<div class="info" style="">Congrats, that was a success!<br><br><b>ZIPPED:</b> '+vpb_current_directory+vpb_directory_or_file_name+'<br><br> <b>TO:</b> '+vpb_to_this_directory_compress+'</div><br clear="all">');
							
							setTimeout(function() { $("#vpb_processing_compress_action").hide(); vpb_hide_popup_options_boxes(); },3000);
							setTimeout(function() { $("#compressed_files").prepend(response); },3200);
						}
						else
						{
							$("#vpb_processing_compress_action").html(response);
						}
					}
					else if(option == "delete")
					{
						$("#vpb_processing_delete_action").hide();
						vpb_hide_popup_options_boxes();
						
						var vpb_new_directory_name = vpb_directory_or_file_name.replace("/", "");
						var df_without_extensions_or_slash = vpb_new_directory_name.substr(0, vpb_new_directory_name.lastIndexOf('.')) || vpb_new_directory_name;
						setTimeout(function() { $("#directory_id"+df_without_extensions_or_slash).fadeOut(); },1000);
						
					}
					else if(option == "create")
					{
						var response_brought = response.indexOf('success');
						if(response_brought != -1)
						{
							$("#vpb_to_this_directory_create").val('');
							if(vpb_directory_or_file_name == "File")
							{
								$("#vpb_processing_create_action").html('<div class="info" style="">Congrats, that was a success!<br><br>Created the file: <b>'+vpb_to_this_directory_create+'</b> successfully.</div><br clear="all">');
							}
							else if(vpb_directory_or_file_name == "Directory")
							{
								$("#vpb_processing_create_action").html('<div class="info" style="">Congrats, that was a success!<br><br>Created the directory: <b>'+vpb_to_this_directory_create+'</b> successfully.</div><br clear="all">');
							}
							else
							{
								$("#vpb_processing_create_action").html('<div class="info" style="">Congrats, that was a success!<br><br>Created: <b>'+vpb_to_this_directory_create+'</b> successfully.</div><br clear="all">');
							}
							setTimeout(function() { $("#vpb_processing_create_action").hide(); vpb_hide_popup_options_boxes(); },3000);
							setTimeout(function() { $("#created_files_or_directories").prepend(response); },3200);
						}
						else
						{
							$("#vpb_processing_create_action").html(response);
						}
					}
					else { /* Unknown Action */ }
					$("#vpb_empty_directory").hide();
				}
			});	
		}
	}
}

//This function displays the directory option pop-up box when called
function vpb_directory_options_box(directory_name)
{
	var vpb_current_directory = $("#vpb_current_directory").val();
	
	$("#vpb_file_system_background").css({
		"opacity": "0.2"
	});
	$("#vpb_file_system_background").fadeIn("slow");
	$("#vpb_directory_options_box").fadeIn('fast');
	$("#vpb_directory_or_file_name").val(directory_name);
	$("#vpb_options_for_this_directory").html(vpb_current_directory+directory_name.replace("/", ""));
	
	//Hide Option Selected
	$("#directory_box_move").hide();
	$("#directory_box_copy").hide();
	$("#directory_box_rename").hide();
	$("#directory_box_compress").hide();
	$("#directory_box_create_new_file_or_directory").hide();
	$("#vpb_upload_files").hide();
	$("#directory_box_delete").hide();
	$("#directory_box").show();
}

//This function displays the Create New File and Directory pop-up box when called
function vpb_directory_or_file_box(vpb_directory_or_file_name)
{
	$("#vpb_processing_create_action").hide();
	var vpb_current_directory = $("#vpb_current_directory").val();
	$("#vpb_file_system_background").css({
		"opacity": "0.2"
	});
	$("#vpb_file_system_background").fadeIn("slow");
	$("#vpb_directory_options_box").fadeIn('fast');
	$("#vpb_directory_or_file_name").val(vpb_directory_or_file_name);
	$("#vpb_this_directory_or_file").html(vpb_directory_or_file_name);
	$("#file_or_dir").html(vpb_directory_or_file_name);
	if(vpb_directory_or_file_name == "File")
	{
		$("#vpb_to_this_directory_create").val(vpb_current_directory+'New-file-name.txt');
	}
	else
	{
		$("#vpb_to_this_directory_create").val(vpb_current_directory+'New-folder-name');
	}
	
	//Hide Option Selected
	$("#directory_box_move").hide();
	$("#directory_box_copy").hide();
	$("#directory_box_rename").hide();
	$("#directory_box_compress").hide();
	$("#directory_box_delete").hide();
	$("#directory_box").hide();
	$("#vpb_upload_files").hide();
	$("#directory_box_create_new_file_or_directory").show();
}

//This function displays the upload files pop-up box when called
function vpb_upload_file_box()
{
	var vpb_current_directory = $("#vpb_current_directory").val();
	$("#vpb_file_system_background").css({
		"opacity": "0.2"
	});
	$("#vpb_file_system_background").fadeIn("slow");
	$("#vpb_directory_options_box").fadeIn('fast');
	//$("#vpb_directory_or_file_name").val(vpb_directory_or_file_name);
	
	//Hide Option Selected
	$("#directory_box_move").hide();
	$("#directory_box_copy").hide();
	$("#directory_box_rename").hide();
	$("#directory_box_compress").hide();
	$("#directory_box_delete").hide();
	$("#directory_box").hide();
	$("#directory_box_create_new_file_or_directory").hide();
	$("#vpb_upload_files").show();
}

//This function displays the file option box when called
function vpb_file_options_box()
{
	$("#vpb_file_system_background").css({
		"opacity": "0.4"
	});
	$("#vpb_file_system_background").fadeIn("slow");
	$("#vpb_file_options_box").fadeIn('fast');
	window.scroll(0,0);
}


//This function hides all pop-up Boxes when called
function vpb_hide_popup_options_boxes()
{
	$("#vpb_directory_options_box").hide(); //Hides the sign-up box when clicked outside the form
	$("#vpb_file_options_box").hide(); //Hides the login box when clicked outside the form
	$("#directory_box_create_new_file_or_directory").hide();
	$("#vpb_upload_status").hide();
	$("#vpb_upload_files").hide();
	$("#vpb_file_system_background").fadeOut("slow");
}


//This is the Upload Function
$('#vpb_browsed_file').live('change', function() 
{
	var vpb_current_directory = $("#vpb_current_directory").val();
	var dataString = "page=upload_files&vpb_current_directory=" + vpb_current_directory;
	
	$("#vpb_file_attachment_form").vPB({
		url: 'default_system_actions.php?'+dataString,
		cache: false,
		beforeSubmit: function() 
		{
			//Hide correct upload button and show the fake upload button during file upload
			$("#main_b").hide();
			$("#fake_b").show();
			
			$("#vpb_upload_status").html('<div style="font-family: Verdana, Geneva, sans-serif; font-size:12px; color:black;" align="center">Please wait <img src="default_system_files/loadings.gif" align="absmiddle" title="Upload...."/></div><br clear="all">');
		},
		success: function(response) 
		{
			//Hide fake upload button and show the correct button upon successful upload
			$("#fake_b").hide();
			$("#main_b").show();
			$("#vpb_empty_directory").hide();
			
			var response_brought = response.indexOf('success');
			if(response_brought != -1)
			{
				$("#vpb_file_uploaded_successful_and_displayed").prepend(response);
				setTimeout(function() { $("#vpb_upload_status").hide().fadeIn('slow').html('<div class="info" style=""><b>'+$('#vpb_browsed_file').val()+'</b> uploaded successfully.</div><br clear="all">');  },300);
				setTimeout(function() { $('#vpb_browsed_file').val(''); },500);
			}
			else
			{
				$("#vpb_upload_status").hide().fadeIn('slow').html(response);
				setTimeout(function() { $('#vpb_browsed_file').val(''); },500);
			}
		}
	}).submit(); 
});

//This function calls the content display function on page load
$(document).ready(function()
{
	vpb_file_system_displayer('');
});

//This is the function that displays all file system contents and when navigate to a specific directory
function vpb_file_system_displayer(vpb_director_name)
{
	if($("#vpb_current_directory_identifier").val() != "")
	{
		var vpb_current_directory_identifier = $("#vpb_current_directory_identifier").val();
	}
	else
	{
		var vpb_current_directory_identifier = "Undefined";
	}
	if(vpb_director_name == "")
	{
		var dataString = 'page=vpb_file_system_displayer&vpb_current_directory_identifier='+ vpb_current_directory_identifier;
	}
	else
	{
		var dataString = 'page=vpb_file_system_displayer&dir=' + vpb_director_name + '&vpb_current_directory_identifier='+ vpb_current_directory_identifier;
	}
	
	$.ajax({  
		type: "POST",  
		url: "default_system_actions.php",  
		data: dataString,
		cache: false,
		beforeSend: function() 
		{
			$("#vpb_file_system_search_displayer").hide();
			$("#vpb_file_system_contents").show().html('<br clear="all"><br clear="all"><div style="font-family:Verdana, Geneva, sans-serif; font-size:12px;">Please wait <img src="default_system_files/loadings.gif" align="absmiddle" alt="Loading..."></div><br clear="all">').addClass('vpb_file_system_main_wrapper');
		},  
		success: function(response)
		{
			$('#vpb_file_system_contents').show().html(response).removeClass('vpb_file_system_main_wrapper');
		}
	   
	}); 
}

//This is the function that exits the search
function vpb_file_system_search_box()
{
	vpb_file_system_displayer('');
}


//This is the function that searches the file system
function vpb_search_file_system(vpb_director_name)
{
	var file_system_search = $("#file_system_search").val();
	
	if($("#vpb_current_directory_identifier").val() != "")
	{
		var vpb_current_directory_identifier = $("#vpb_current_directory_identifier").val();
	}
	else
	{
		var vpb_current_directory_identifier = "Undefined";
	}
	if(vpb_director_name == "")
	{
		var dataString = 'page=vpb_file_system_displayer&vpb_current_directory_identifier='+ vpb_current_directory_identifier + '&file_system_search=' + file_system_search;
	}
	else
	{
		var dataString = 'page=vpb_file_system_displayer&dir=' + vpb_director_name + '&vpb_current_directory_identifier='+ vpb_current_directory_identifier + '&file_system_search=' + file_system_search;;
	}
	
	if(file_system_search == "" || file_system_search == "Search files")
	{
		alert('Please enter the name of the file or directory that you wish to search for in the required field to proceed.\n Thanks...');
		return false;
	}
	else
	{
		$.ajax({  
			type: "POST",  
			url: "default_system_actions.php",  
			data: dataString,
			cache: false,
			beforeSend: function() 
			{
				$("#vpb_file_system_contents").show().html('<br clear="all"><br clear="all"><div style="font-family:Verdana, Geneva, sans-serif; font-size:12px;">Please wait <img src="default_system_files/loadings.gif" align="absmiddle" alt="Loading..."></div><br clear="all">').addClass('vpb_file_system_main_wrapper');
			},  
			success: function(response)
			{
				$('#vpb_file_system_contents').show().html(response).removeClass('vpb_file_system_main_wrapper');
			}
		}); 
	}
}