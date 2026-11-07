<?php
require_once "auth.php";
require_once "database.php";
$page_title="Company Settings";
$settings_file=__DIR__."/company.php";
$current=["name"=>"SIGNAGEPRO","phone"=>"","email"=>"","address"=>""];
$saved=false;$account_saved=false;$account_error="";
if(file_exists($settings_file)){ $x=require $settings_file; if(is_array($x))$current=array_merge($current,$x); }

if($_SERVER['REQUEST_METHOD']==='POST'){
  $action=$_POST['action']??'company';
  if($action==='company'){
    $current=[
      'name'=>trim($_POST['name']??''),
      'phone'=>trim($_POST['phone']??''),
      'email'=>trim($_POST['email']??''),
      'address'=>trim($_POST['address']??'')
    ];
    file_put_contents($settings_file,"<?php return ".var_export($current,true).";\n");
    $saved=true;
  }
  if($action==='account'){
    $userId=(int)$_SESSION['user_id'];
    $currentPassword=$_POST['current_password']??'';
    $newUsername=trim($_POST['new_username']??'');
    $newPassword=$_POST['new_password']??'';
    $confirmPassword=$_POST['confirm_password']??'';

    $st=$conn->prepare("SELECT username,password FROM users WHERE id=? LIMIT 1");
    $st->bind_param("i",$userId);$st->execute();$user=$st->get_result()->fetch_assoc();

    if(!$user || !password_verify($currentPassword,$user['password'])){
      $account_error='Current password is incorrect.';
    }elseif($newUsername===''){
      $account_error='Please enter a new username.';
    }elseif($newPassword!=='' && strlen($newPassword)<6){
      $account_error='New password must be at least 6 characters.';
    }elseif($newPassword!==$confirmPassword){
      $account_error='New password and confirmation do not match.';
    }else{
      $check=$conn->prepare("SELECT id FROM users WHERE username=? AND id<>? LIMIT 1");
      $check->bind_param("si",$newUsername,$userId);$check->execute();
      if($check->get_result()->fetch_assoc()){
        $account_error='That username is already in use.';
      }else{
        if($newPassword!==''){
          $hash=password_hash($newPassword,PASSWORD_DEFAULT);
          $up=$conn->prepare("UPDATE users SET username=?, password=? WHERE id=?");
          $up->bind_param("ssi",$newUsername,$hash,$userId);
        }else{
          $up=$conn->prepare("UPDATE users SET username=? WHERE id=?");
          $up->bind_param("si",$newUsername,$userId);
        }
        if($up->execute()){
          $_SESSION['username']=$newUsername;
          $account_saved=true;
        }else{
          $account_error='Could not update the account. Please try again.';
        }
      }
    }
  }
}

$userId=(int)$_SESSION['user_id'];
$ust=$conn->prepare("SELECT username,full_name FROM users WHERE id=? LIMIT 1");$ust->bind_param("i",$userId);$ust->execute();$account=$ust->get_result()->fetch_assoc() ?: ['username'=>'','full_name'=>''];
include "header.php";
?>
<h1 class="page-title">Company Settings</h1><p class="muted">Set your company information and account login details.</p>
<?php if($saved):?><div class="alert" style="background:#ecfdf3;color:#166534">Company information saved.</div><?php endif;?>
<?php if($account_saved):?><div class="alert" style="background:#ecfdf3;color:#166534">Login username/password updated successfully.</div><?php endif;?>
<?php if($account_error):?><div class="alert"><?=htmlspecialchars($account_error)?></div><?php endif;?>

<div class="card"><h2>Company Information</h2><p class="muted settings-help">These details are automatically shown on your quotation header.</p><form method="post"><input type="hidden" name="action" value="company"><div class="grid2">
<div class="field"><label>Company Name</label><input name="name" required value="<?=htmlspecialchars($current['name'])?>"></div>
<div class="field"><label>Phone</label><input name="phone" value="<?=htmlspecialchars($current['phone'])?>"></div>
<div class="field"><label>Email</label><input name="email" value="<?=htmlspecialchars($current['email'])?>"></div>
<div class="field"><label>Address</label><input name="address" value="<?=htmlspecialchars($current['address'])?>"></div>
</div><button class="btn primary">Save Company Info</button></form></div>

<div class="card"><h2>Login Account</h2><p class="muted settings-help">Change the username and password used on the Sign In page. Enter your current password to confirm the change.</p><form method="post"><input type="hidden" name="action" value="account"><div class="grid2">
<div class="field"><label>Current Username</label><input value="<?=htmlspecialchars($account['username'])?>" readonly></div>
<div class="field"><label>Current Password</label><input type="password" name="current_password" autocomplete="current-password" required></div>
<div class="field"><label>New Username</label><input name="new_username" value="<?=htmlspecialchars($account['username'])?>" autocomplete="username" required></div>
<div class="field"><label>New Password</label><input type="password" name="new_password" autocomplete="new-password" minlength="6" placeholder="Leave blank to keep current password"></div>
<div class="field"><label>Confirm New Password</label><input type="password" name="confirm_password" autocomplete="new-password" minlength="6" placeholder="Repeat the new password"></div>
</div><button class="btn primary">Update Login Details</button></form></div>
<?php include "footer.php";?>