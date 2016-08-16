<?php
function randomPassword() {
    $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < 8; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass); //turn the array into a string
}
$config = array(
   "base_url" => "https://www.whyzlist.com/hybridauth-master/hybridauth/",
       "providers" => array (
           "Facebook" => array (
          "enabled" => true,
          "keys"    => array ( "id" => "369981179863191", "secret" => "c28e31f7362f6d61e1d857dde16605d2" ),
          "scope"   => "email, user_about_me, user_birthday, user_hometown", // optional
          "display" => "popup" // optional
    ),
       /*"Facebook" => array (
           "enabled" => true, "keys" => array ( "key" => "1427902737452009", "secret" => "6b05d76acc6cb63c7a051401ab5c2cb1" )
       ),*/
      
   )
);
   require_once( "hybridauth-master/hybridauth/Hybrid/Auth.php" );
 include_once 'includes/mysqli_connect.php';
     $hybridauth = new Hybrid_Auth( $config );
 
  // try to authenticate with twitter
  $adapter = $hybridauth->authenticate( "Facebook" );
 
  // return Hybrid_User_Profile object intance
  $user_profile = $adapter->getUserProfile();
  //print_r($user_profile);exit;
   // echo "Hi there! " . $user_profile->displayName;
$q = "SELECT email FROM users WHERE email='$user_profile->email'";
  $r = mysqli_query ($dbc, $q) or trigger_error("Query: $q\n<br />MySQL Error: " . mysqli_error($dbc));
  if (mysqli_num_rows($r) == 0) {
      $pass=randomPassword();
    $a = md5(uniqid(rand(), false));
    $q = "INSERT INTO users (email,first_name,last_name, pass, active, registration_date, location, user_level) VALUES ('$user_profile->email','$user_profile->firstName','$user_profile->lastName', SHA1('$pass'), '$a', NOW(), '$user_profile->city', 101 )";
    $r = mysqli_query ($dbc, $q) or trigger_error("Query:$q\n<br />MySQL Error: " . mysqli_error($dbc));
  }
      $q = "SELECT * FROM users WHERE email='$user_profile->email'";
		$r = mysqli_query ($dbc, $q) or trigger_error("Query: $q\n<br>MySQL Error: " . mysqli_error($dbc));
//print_r($r);
		if (@mysqli_num_rows($r) == 1) {
			// A match was made.
				// Register the values:

				$_SESSION = mysqli_fetch_array ($r, MYSQLI_ASSOC);
				$_SESSION["IsLoggedIn"] = true;
				$_SESSION['timeout'] = time(604800);
                                //print_r($_SESSION);exit;
				mysqli_free_result($r);
				mysqli_close($dbc);

				// Redirect the user:
                                // 
				//$url = BASE_URL . 'control_panel';
                                $day=date('D');
$url="https://www.whyzlist.com/main?country=$user_profile->country&city=$user_profile->city&day=$day";
				// Define the URL.
				header("Location: $url");

				ob_end_clean(); // Delete the buffer.
				exit(); // Quit the script.
		}
  
  // print_r($user_profile);exit;
   /* $hybridauth = new Hybrid_Auth( $config );
 
    $adapter = $hybridauth->authenticate( "Facebook" );
 
    $user_profile = $adapter->getUserProfile();*/
