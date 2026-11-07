<?php
session_start(); require_once "database.php";
if(isset($_SESSION['user_id'])){header("Location: dashboard.php");exit;}
$error=''; $username=''; $fullName='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  $username=trim($_POST['username']??''); $fullName=trim($_POST['full_name']??''); $password=$_POST['password']??''; $confirm=$_POST['confirm_password']??'';
  if($fullName==='') $error='Please enter your full name.';
  elseif($username==='') $error='Please enter a username.';
  elseif(strlen($username)<3) $error='Username must be at least 3 characters.';
  elseif(strlen($password)<6) $error='Password must be at least 6 characters.';
  elseif($password!==$confirm) $error='Passwords do not match.';
  else{
    $check=$conn->prepare("SELECT id FROM users WHERE username=? LIMIT 1"); $check->bind_param("s",$username); $check->execute();
    if($check->get_result()->fetch_assoc()) $error='That username is already in use. Please choose another one.';
    else{
      $hash=password_hash($password,PASSWORD_DEFAULT); $st=$conn->prepare("INSERT INTO users (username,password,full_name) VALUES (?,?,?)"); $st->bind_param("sss",$username,$hash,$fullName);
      if($st->execute()){header("Location: login.php?registered=1");exit;} $error='Could not create the account. Please try again.';
    }
  }
}
?>
<!doctype html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Sign Up | SignagePro</title><link rel="stylesheet" href="style.css"></head>
<body class="login-page auth-futuristic">
<div class="login-box auth-card signup-card">
  <img class="login-logo-img auth-logo" src="bridge-logo.png" alt="Bridge Advertising & Printing">
  <h1 class="login-title">Create your account</h1>
  <div class="login-sub">Create an account to manage your signage quotation business.</div>
  <?php if($error):?><div class="alert auth-alert"><?=htmlspecialchars($error)?></div><?php endif;?>
  <form method="post" class="auth-form">
    <div class="auth-input"><input name="full_name" value="<?=htmlspecialchars($fullName)?>" placeholder="Enter your full name" autocomplete="name" required autofocus></div>
    <div class="auth-input"><input name="username" value="<?=htmlspecialchars($username)?>" placeholder="Create a username" autocomplete="username" required></div>
    <div class="auth-input password-wrap">
      <input id="password" type="password" name="password" placeholder="Create a password" autocomplete="new-password" minlength="6" required>
      <button type="button" class="eye-btn" onclick="togglePassword('password','eyeIcon')" aria-label="Show or hide password"><svg id="eyeIcon" width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"/><circle cx="12" cy="12" r="2.5"/></svg></button>
    </div>
    <div class="auth-input password-wrap">
      <input id="confirm_password" type="password" name="confirm_password" placeholder="Confirm your password" autocomplete="new-password" minlength="6" required>
      <button type="button" class="eye-btn" onclick="togglePassword('confirm_password','confirmEyeIcon')" aria-label="Show or hide password"><svg id="confirmEyeIcon" width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"/><circle cx="12" cy="12" r="2.5"/></svg></button>
    </div>
    <button class="btn primary login-btn auth-submit" type="submit">Sign Up</button>
  </form>
  <div class="auth-or"><span>OR</span></div>
  <div class="auth-note">Already have an account? <a href="login.php">Log In</a></div>
</div>
<script>
function togglePassword(id,iconId){const p=document.getElementById(id),icon=document.getElementById(iconId);p.type=p.type==='password'?'text':'password';icon.innerHTML=p.type==='text'?'<path d="M3 3l18 18"/><path d="M10.6 10.6a2.5 2.5 0 0 0 3.5 3.5"/><path d="M9.9 5.2A11.2 11.2 0 0 1 12 5c6.5 0 10 7 10 7a18.2 18.2 0 0 1-3.1 4.1M6.3 6.3C3.4 8.2 2 12 2 12s3.5 7 10 7c1.8 0 3.3-.5 4.7-1.2"/>':'<path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"/><circle cx="12" cy="12" r="2.5"/>'}
</script></body></html>
