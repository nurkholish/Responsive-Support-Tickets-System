<?php

ini_set('session.auto_start', '0');
ini_set('session.save_path', '../php/config/session');
ini_set('session.hash_function', 'sha512');
ini_set('session.gc_maxlifetime', '1800');
ini_set('session.entropy_file', '/dev/urandom');
ini_set('session.entropy_length', '512');
ini_set('session.gc_probability', '20');
ini_set('session.gc_divisor', '100');
ini_set('session.cookie_httponly', '1');
ini_set('session.use_only_cookies', '1');
ini_set('session.use_trans_sid', '0');
session_name("RazorphynSupport");
if (isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
	ini_set('session.cookie_secure', '1');
}
if(isset($_COOKIE['RazorphynSupport']) && !is_string($_COOKIE['RazorphynSupport']) || !preg_match('/^[a-z0-9]{26,40}$/',$_COOKIE['RazorphynSupport'])){
	setcookie(session_name(),'invalid',time()-3600);
	header("location: ../index.php?e=invalid");
	exit();
}
session_start(); 

//Session Check
if(isset($_SESSION['time'])){
	header("location: ../index.php");
	exit();
}
else if(!isset($_GET['act']) || $_GET['act']!='resetpass' || !isset($_GET['key']) || strlen(trim(preg_replace('/\s+/','',$_GET['key'])))!=87){
	header("location: ../index.php"); 
	exit();
}
else{
$key=trim(preg_replace('/\s+/','',$_GET['key']));
$siteurl=dirname(dirname(curPageURL()));
$siteurl=explode('?',$siteurl);
$siteurl=$siteurl[0];
if(is_file('../php/config/setting.txt')) $setting=file('../php/config/setting.txt',FILE_IGNORE_NEW_LINES);
if(!isset($_SESSION['token']['act'])) $_SESSION['token']['act']=random_token(7);
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta name="robots" content="noindex,nofollow">
		<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
		<title><?php if(isset($setting[0])) echo $setting[0];?></title>

		<link rel="shortcut icon" type="image/x-icon" href="/favicon.ico">
		<link rel="stylesheet" type="text/css" href="<?php echo $siteurl.'/min/?g=css_i&amp;5259487' ?>"/>
		
		<!--[if lt IE 9]><script src="../js/html5shiv-printshiv.js"></script><![endif]-->
  </head>
	<body>
		<div class="container">
		<div class='daddy'>
			<div class="navbar navbar-fixed-top">
				<div class="navbar-inner">
					<div class="container">
						<a class="btn btn-navbar hidden-desktop" data-toggle="collapse" data-target=".nav-collapse">
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
						</a>
						<a class="brand"><?php if(isset($setting[0])) echo $setting[0];?></a>
						<div class="nav-collapse navbar-responsive-collapse collapse">
							<ul class="nav">
								<li class="active"><a href="#home"><i class="icon-home"></i>Home</a></li>
								<?php if(isset($setting[9]) && $setting[9]==1){?>
									<li><a href="faq.php"><i class="icon-flag"></i>FAQs</a></li>
								<?php }?>
							</ul>
						</div>
					</div>
				</div>
			</div>
			<hr>
			<div class="jumbotron" >
				<h1 class="muted pagefun"><a href='http://razorphyn.com'><img id='logo' src='../css/images/logo.png' alt='Razorphyn' title='Razorphyn'/></a></h1>
				<h3 class='pagefun'>Welcome to the support center</h3>
			</div>
			<hr>
			<div class='row-fluid main'>
				<form id='passwordform' class='login activesec'>
					<h2 class='titlesec'>Reset Password</h2>
					<div class='row-fluid'>
						<div class='span2'><label>Your Email</label></div>
						<div class='span3'><input type="text" id="rmail" placeholder="Email" autocomplete="off" required></div>
					</div>
					<div class='row-fluid'>
						<div class='span2'><label>New Password</label></div>
						<div class='span4'><input type="password" id="npwd" placeholder="New Password" autocomplete="off" required></div>
						<div class='span2'><label>Reapeat New Password</label></div>
						<div class='span4'><input type="password" id="rnpwd" placeholder="Repeat New Password" autocomplete="off" required></div>
					</div>
					<input type="submit" id='resetpass' onclick='javascript:return false;' class="btn btn-success" value='Update Password'/>
				</form>
			</div>
			<hr>
		</div>
	</div>
	<script type="text/javascript"  src="<?php echo $siteurl.'/min/?g=js_i&amp;5259487' ?>"></script>
	<script>
		$(document).ready(function() {
			$("#resetpass").click(function () {
				var a = $("#npwd").val(),
					b = $("#rnpwd").val(),
					c = $("#rmail").val();
				if("" != a.replace(/\s+/g, "") && a == b){
					$.ajax({
						type: "POST",
						url: "../php/function.php",
						data: {<?php echo $_SESSION['token']['act']; ?>: "reset_password",npass: a,rnpass: b,rmail: c,key: "<?php echo $key; ?>"},
						dataType: "json",
						success: function (a) {
							if("Updated" == a[0]
								window.location = "<?php echo dirname(curPageURL()); ?>"
							else if(a[0]=='sessionerror'){
								switch(a[1]){
									case 0:
										window.location.replace("<?php echo $siteurl.'?e=invalid'; ?>");
										break;
									case 1:
										window.location.replace("<?php echo $siteurl.'?e=expired'; ?>");
										break;
									case 2:
										window.location.replace("<?php echo $siteurl.'?e=local'; ?>");
										break;
									case 3:
										window.location.replace("<?php echo $siteurl.'?e=token'; ?>");
										break;
								}
							}
							else
								noty({text: a[0],type: "error",timeout: 9E3})
						}
					}).fail(function (a, b) {noty({text: b,type: "error",timeout: 9E3})})
				}
				else
					noty({text: "The passwords don't match",type: "error",timeout: 9E3})
			});	
		});
	
		function logout(){var request= $.ajax({type: 'POST',url: '../php/function.php',data: {<?php echo $_SESSION['token']['act']; ?>:'logout'},dataType : 'json',success : function (data) {if(data[0]=='logout') window.location.reload();else alert(data[0]);}});request.fail(function(jqXHR, textStatus){alert('Error: '+ textStatus);});}
	</script>
  </body>
</html>
<?php 
}
function curPageURL() {$pageURL = 'http';if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") $pageURL .= "s";$pageURL .= "://";if (isset($_SERVER["HTTPS"]) && $_SERVER["SERVER_PORT"] != "80") $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];else $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];return $pageURL;}
function random_token($length){$valid_chars='abcdefghilmnopqrstuvzkjwxyABCDEFGHILMNOPQRSTUVZKJWXYZ';$random_string = "";$num_valid_chars = strlen($valid_chars);for($i=0;$i<$length;$i++){$random_pick=mt_rand(1, $num_valid_chars);$random_char = $valid_chars[$random_pick-1];$random_string .= $random_char;}return $random_string;}

?>