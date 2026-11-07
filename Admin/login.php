<?php
session_start(); require_once "database.php";
if(isset($_SESSION['user_id'])){header("Location: dashboard.php");exit;}
$error='';
$registered=isset($_GET['registered']) && $_GET['registered']==='1';
if($_SERVER['REQUEST_METHOD']==='POST'){
  $u=trim($_POST['username']??''); $p=$_POST['password']??'';
  $st=$conn->prepare("SELECT id,username,password,full_name FROM users WHERE username=? LIMIT 1");
  $st->bind_param("s",$u); $st->execute(); $r=$st->get_result()->fetch_assoc();
  if($r && password_verify($p,$r['password'])){
    $_SESSION['user_id']=$r['id']; $_SESSION['username']=$r['username']; $_SESSION['full_name']=$r['full_name'];
    header("Location: dashboard.php"); exit;
  }
  $error='Invalid username or password.';
}
?>
<!doctype html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Log In | SignagePro</title><link rel="stylesheet" href="style.css"></head>
<body class="login-page auth-futuristic">
<div class="login-box auth-card">
  <img class="login-logo-img auth-logo" src="bridge-logo.png" alt="Bridge Advertising & Printing">
  <h1 class="login-title">Welcome Back</h1>
  <div class="login-sub">Log in to manage your signage quotation business.</div>
  <?php if($registered):?><div class="alert auth-alert success">Account created successfully. You can log in now.</div><?php endif;?>
  <?php if($error):?><div class="alert auth-alert"><?=htmlspecialchars($error)?></div><?php endif;?>
  <form method="post" class="auth-form">
    <div class="auth-input"><input name="username" placeholder="Enter your username" autocomplete="username" required autofocus></div>
    <div class="auth-input password-wrap">
      <input id="password" type="password" name="password" placeholder="Enter your password" autocomplete="current-password" required>
      <button type="button" class="eye-btn" onclick="togglePassword('password','eyeIcon')" aria-label="Show or hide password">
        <svg id="eyeIcon" width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"/><circle cx="12" cy="12" r="2.5"/></svg>
      </button>
    </div>
    <div class="auth-forgot"><a href="#" onclick="return false;">Forgot Password?</a></div>
    <button class="btn primary login-btn auth-submit" type="submit">Log In</button>
  </form>
  <div class="auth-or"><span>OR</span></div>
  <div class="auth-note">Don't have an account? <a href="signup.php">Sign Up</a></div>
</div>
<script>
function togglePassword(id,iconId){const p=document.getElementById(id),icon=document.getElementById(iconId);p.type=p.type==='password'?'text':'password';icon.innerHTML=p.type==='text'?'<path d="M3 3l18 18"/><path d="M10.6 10.6a2.5 2.5 0 0 0 3.5 3.5"/><path d="M9.9 5.2A11.2 11.2 0 0 1 12 5c6.5 0 10 7 10 7a18.2 18.2 0 0 1-3.1 4.1M6.3 6.3C3.4 8.2 2 12 2 12s3.5 7 10 7c1.8 0 3.3-.5 4.7-1.2"/>':'<path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"/><circle cx="12" cy="12" r="2.5"/>'}
</script></body></html>
