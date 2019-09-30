<!DOCTYPE html>
<html>
<head>
	<title>Halaman Register</title>
	<link rel="stylesheet" href="css/style.css"/>
</head>
<body>

<div id="utama" style="margin-top: 10%;">
<div id="judul">
	Halaman Register
</div>

<div id="inputan">
	<form action="" method="post">
		<div>
			<input type="text" name="user" placeholder="Username" class="lg">
		</div>
		<div style="margin-top: 10px;">
			<input type="password" name="pass" placeholder="Password" class="lg">
		</div>
		<div style="margin-top: 10px;">
			<input type="email" name="email" placeholder="Email" class="lg">
		</div>
		<div style="margin-top: 10px;">
			<input type="text" name="nama_lengkap" placeholder="Nama Lengkap" class="lg">
		</div>
		<div style="margin-top: 10px;">
			<select name="jenis_kelamin">
				<option value="">- Pilih Jenis Kelamin -</option>
				<option value="Laki-laki">Laki-Laki</option>
				<option value="Perempuan">Perempuan</option>
			</select>
		</div>
		<div style="margin-top: 10px;">
			<textarea name="alamat" class="lg" placeholder="Alamat"></textarea>
		</div>

		<div style="margin-top: 10px;">
			<input type="submit" name="register" value="Register" class="btn"/>
		
				<a href="login.php" class="btn-right" style="float:right; margin-top: 5px">Login</a>
		</div>
</form>
<?php
include "imc/koneksi.php";
if (@$_POST['register']) {
	$user = @$_POST['user'];
	$pass = @$_POST['pass'];
	$email = @$_POST['email'];
	$nama_lengkap = @$_POST['nama_lengkap'];
	$jenis_kelamin = @$_POST['jenis_kelamin'];
	$alamat = @$_POST['alamat'];

	if($user == "" || $pass == "" || $email == "" || $nama_lengkap == "" || $jenis_kelamin == "" || $alamat == "") {
		?> <script type="text/javascript">alert('Data yang di input tidak boleh kosong');</script> <?php
	} else {
		$sql_insert = mysql_query("insert into tb_login values ('', '$user', md5('$pass'), '$email',
			'$nama_lengkap','$jenis_kelamin', '$alamat', 'user')") or die(mysql_error());
		if ($sql_insert) {
		?> <script type="text/javascript">alert('Pendaftaran Berhasil!!, Silahkan login');</script> <?php
		}
	}
}
?>
</div>
</div>
</body>
</html>