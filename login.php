<?php
@session_start();
include "imc/koneksi.php";

if(@$_SESSION['admin'] || @$_SESSION['operator'] || @$_SESSION['user']) {
	header("location: index.php");
} {
?>

<!DOCTYPE html>
<html>
<head>
	<title>Halaman Login</title>
</head>
<style type="text/css">
   body{
			margin: 0;
                        padding: 0;
                        background-image: url(img/oop.png);
                        background-size: cover;
                        background-position: center;
                        font-family: sans-serif;
		}

		#utama {
			width: 300px;
			margin: 0 auto;
			margin-top: 12%;
		}

		#judul {
			padding: 15px;
			text-align: left;
			color: #fff;
			font-size: 20px;
			background-color: #00FFFF;
			border-top-right-radius: 10px;
			border-top-left-radius: 10px;
			border-bottom: 3px solid #336666;
		}
                div.lock{
  
    background-position: center;
    background-size: 12px;
    display: inline-block;
    width: 20px;
    height: 20px;
    margin-top: 8px;
    float: left;
    margin-right: 10px;
}

		#inputan {
			background-color: #ccc; 
			padding: 20px;
			border-bottom-right-radius: 10px;
			border-bottom-left-radius: 10px;
                        color: #fff;
		}
		input {
			padding: 10px;
			border: 0;
		}
		.lg {
			width: 240px;
		}
		.btn {
			background-color: #00FFFF;
			border-radius: 10px;
			color: #fff;
			width: 260px;
		}
		.btn:hover {
			background-color: #336666;
			cursor: pointer;
		}
        </style>
<body>
<div id="utama">
	<div id="judul">
		<div class="lock"></div>
		<img src="img/lock.png">
		Halaman Login !!
	</div>

	<div id="inputan">
		<form action="" method="post">
			<div>
				<input type="text" name="user" placeholder="Username" class="lg" />
			</div>
			<div style="margin-top: 10px">
				<input type="password" name="pass" placeholder="Password" class="lg" />
			</div>
			<div style="margin-top: 10px">
				<input type="submit" name="login" value="Login" class="btn" />
			</div>
			
		</form>


		<?php
		$user = @$_POST['user'];
		$pass = @$_POST['pass'];
		$login= @$_POST['login']; 
		$batal= @$_POST['batal'];
		if($login) {
			if($user == "" || $pass == "" ) {
			?> <script type="text/javascript">alert("Username / Password tidak boleh kosong");</script><?php
			} else {
				$sql = mysql_query("select * from tb_login where username = '$user' and password = md5('$pass')") or die(mysql_error());
				$data = mysql_fetch_array($sql);
				$cek = mysql_num_rows($sql);
				if($cek >= 1) {
					if($data['level'] == "admin") {
						@$_SESSION['admin'] = $data['kode_user'];
						header("location: index.php"); 
				} else if($data['level'] == "operator") {
					@$_SESSION['operator'] = $data['kode_user'];
						header("location: index.php"); 
					
				} else if($data['level'] == "user") {
					@$_SESSION['user'] = $data['kode_user'];
						header("location: index.php"); 
					
				}
				} else {
					?> <script type="text/javascript">alert("Login gagal, username / password salah, Silahkan untuk mencoba lagi");</script><?php
				}
			}
		}
		?>
	</div>
</div>
</body>
</html>
<?php
}
?>