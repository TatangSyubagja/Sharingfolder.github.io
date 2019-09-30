<!DOCTYPE html>
	<?php
	@session_start();
	include "imc/koneksi.php";

	if(@$_SESSION['admin'] || @$_SESSION['operator'] || @$_SESSION['user']) {
	?>
	<?php
	if (@$_SESSION['admin']) {
		$sesi = $_SESSION['admin'];
	} else if (@$_SESSION['operator']) {
		$sesi = $_SESSION['operator'];
	} else if (@$_SESSION['user']) {
		$sesi = $_SESSION['user'];
	}
		$sql_profil = mysql_query("SELECT * FROM tb_login where kode_user= '$sesi'") or die(mysql_error());
		$data= mysql_fetch_array($sql_profil);
	?>
<html>
<head>
	<title>Edit Profile</title>
	<link rel="stylesheet" href="css/style.css"/>
</head>
		<body>
			<div id="utama" style="margin-top: 10%;">
<div id="judul">
	Edit Profile
</div>
<div id="inputan">
<form action="" method="POST">
				<label>Nama Lengkap</label>
				<div style="margin-top: 5px;"><input type="text" name="nama" value="<?php echo $data['nama_lengkap']; ?>"required></div><br>
				<label>Jenis Kelamin</label>
				<div style="margin-top: 10px;">
			<label><input type="radio" name="jk" value="Laki-laki" required
			<?php if($data['jenis_kelamin'] == 'Laki-laki'){ echo "checked"; } ?> />Laki-laki</label>
					<br>
			<label><input type="radio" name="jk" value="Perempuan"	required 
			<?php if($data['jenis_kelamin'] == 'Perempuan'){ echo "checked"; } ?> />Perempuan</label> 
			</div><br>
			<label>Alamat</label>
				<div style="margin-top: 10px;"><textarea type="text" name="alamat" rows="2"  required> <?php echo $data['alamat']; ?></textarea></div><br>
			<label>Username</label>
				<div style="margin-top: 10px;"><input type="text" name="user" value="<?php echo $data['username']; ?>" required>
			</div><br>
			<label>Password</label>
				<div style="margin-top: 10px;"><input type="text" name="pass" value="<?php echo $data['pass']; ?>" required>
			</div><br>
			<div style="margin-top: 10px;">
			<input type="submit" name="edit" value="Edit">
					<a href="index.php"  class="btn-right" style="float:right; margin-top: 5px">Kembali</a>
		</div>
	</form>
</div>
</div>
</body>
</html>
	<?php
	if (@$_POST['edit']) {
		$nama = mysql_real_escape_string($_POST['nama']);
		$jk = mysql_real_escape_string($_POST['jk']);
		$alamat = mysql_real_escape_string($_POST['alamat']);
		$user = mysql_real_escape_string($_POST['user']);
		$pass = mysql_real_escape_string($_POST['pass']);	
		mysql_query("UPDATE tb_login SET nama_lengkap = '$nama' , jenis_kelamin = '$jk', alamat = '$alamat', 
		username = '$user', pass = '$pass', password = md5('$pass') where kode_user= '$sesi'") or die(mysql_error());
	header("location: ?page=editprofile");
	}
	?>
</>
<?php
}