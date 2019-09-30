<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
@session_start();
include "imc/koneksi.php";
ob_start();

if(@$_SESSION['admin'] || @$_SESSION['operator'] || @$_SESSION['user']) {
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Tugas Praktik Kerja Lapangan</title>


<!-- Required header file -->
<script type="text/javascript" src="default_system_files/jquery_1.5.2.js"></script>
<script type="text/javascript" src="default_system_files/file_uploads.js"></script>
<script type="text/javascript" src="default_system_files/default_system_js.js"></script>
<script type="text/javascript" src="default_system_files/post_watermarkinput.js"></script>
<link href="default_system_files/styles.css" rel="stylesheet" type="text/css">
<style>
	p.indent {
		font-size: 20px;
		color: #fff;
	}
	p.tang{
		padding-right: 10em;
	}
	body {
		margin: 0;
		padding: 0;
		background-size: cover;
		background-position: center;
		background-image: url(img/bsi.jpeg);
		font-family: sans-serif;
	}
	div.footer {
		color: #fff;
	}

</style>

</head>
<body>
<br clear="all">
<center>
	 &nbsp;
<i><b><div style="font-family:Verdana, Geneva, sans-serif; font-size:28px;width:1000px; color: #fff">Sharing Folder</div></b></i>

<p class="indent">
	<?php
	if (@$_SESSION['admin']) {
		$user_terlogin = @$_SESSION['admin'];
	} else if (@$_SESSION['operator']) {
		$user_terlogin = @$_SESSION['operator'];
	} else if(@$_SESSION['user']) {
		$user_terlogin = @$_SESSION['user'];
	}
	$sql_user = mysql_query("select * from tb_login where kode_user = '$user_terlogin'") or die(mysql_error());
	$data_user = mysql_fetch_array($sql_user);
	?>
	<a>Selamat Datang <?php echo $data_user['username']; ?>&nbsp;...</a></p>










<!-- Code Begins -->
<?php
function vpb_self_directory() 
{
   $vpb_dir_location = dirname($_SERVER['PHP_SELF']);
   $vpb_dir_position = strrpos($vpb_dir_location,'/') + 1;
   return substr($vpb_dir_location,$vpb_dir_position);
}
?>
<input type="hidden" id="vpb_current_directory_identifier" value="<?php echo vpb_self_directory(); ?>" />
<div id="vpb_file_system_contents">
</div>
<!-- Code Ends -->










&nbsp;
<div class="footer"> 
                Copyright © 2019 RPP WNK·|| SISTEM INFORMASI AKUNTANSI UBSI 
            </div>
<p style="margin-bottom:400px;">&nbsp;</p>
</center>
</body>
</html>
<?php
} else {
	header("location: login.php");
}
?>