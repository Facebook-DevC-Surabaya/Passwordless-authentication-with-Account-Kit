<?php

if(isset($_POST["code"])){
  session_start();
  $_SESSION["code"] = $_POST["code"];
  $_SESSION["csrf_nonce"] = $_POST["csrf_nonce"];
  $ch = curl_init();
  // Set url elements
  $fb_app_id = '501238553541150';
  $ak_secret = '30fb79ee5d79de9dbefba02354113805';
  $token = 'AA|'.$fb_app_id.'|'.$ak_secret;

  // Get access token
  $url = 'https://graph.accountkit.com/v1.0/access_token?grant_type=authorization_code&code='.$_POST["code"].'&access_token='.$token;
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_URL,$url);
  $result=curl_exec($ch);
  $info = json_decode($result);
  // Get account information
  $url = 'https://graph.accountkit.com/v1.0/me/?access_token='.$info->access_token;
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_URL,$url);
  $result=curl_exec($ch);
  curl_close($ch);
  $final = json_decode($result);  
  
  $method = '';
  $identity = '';
  if (isset($final->phone)) {
  	$method = "SMS";
  	$identity = $final->phone->number;
  }

  if (isset($final->email)) {
  	$method = "EMAIL";
  	$identity = $final->email->address;
  }
}else{
	header("Location: index.php");
}

?>

<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>AccountKitJS App</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
		<link rel="stylesheet" href="https://code.getmdl.io/1.1.3/material.indigo-pink.min.css">
		<style>
			body {
				text-align: center;
				background: #EEE;
			}
			.mdl-button {
				width: 100%;
			}
		</style>
		<script defer src="https://code.getmdl.io/1.1.3/material.min.js"></script>
</head>

<body>
  <div class="mdl-grid">
	  <div class="mdl-cell mdl-cell--4-col mdl-cell--4-offset">
	  	<div class="mdl-card mdl-shadow--2dp">
	  	<div class="mdl-card__supporting-text">
	  	<h1 class="mdl-typography--title">Passwordless Authentication</h1>
	  	<h2 class="mdl-typography--subhead">You're In!</h2>
	  	<p><strong>Details:</strong></p>
		  	<ul class="demo-list-icon mdl-list">
			  <li class="mdl-list__item">
			    <span class="mdl-list__item-primary-content">
			      <i class="material-icons mdl-list__item-icon">fingerprint</i>
    			  <span id="token"> <?php echo $method ?> </span>
			    </span>
			  </li>
			  <li class="mdl-list__item">
			    <span class="mdl-list__item-primary-content">
			      <i class="material-icons mdl-list__item-icon">face</i>
    			  <span id="nickname"><?php echo $identity; ?></span>
			    </span>
			  </li>
			  <li class="mdl-list__item">
			    <span class="mdl-list__item-primary-content">
			      <i class="material-icons mdl-list__item-icon">person</i>
    			  <span id="user_id"><?php echo $final->id ?></span>
			    </span>
			  </li>
			  <li class="mdl-list__item">
			    <span class="mdl-list__item-primary-content">
			      <button onclick="goToLogin()" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--colored mdl-typography--text-center">Try Another</button>
			    </span>
			  </li>
			</ul>
	  	</div>
	  	</div>
	  </div>
	</div>

	<script>
      function goToLogin(){
	    window.location.href = "/";
	  }
	</script>

</body>
</html>