<?php // require ('includes/daycalc.php');
$page_title = 'Login - Whyzlist';
require('includes/header.html');
require_once __DIR__ . '/src/Facebook/autoload.php';


$fb = new Facebook\Facebook([
	'app_id' => '369981179863191',
	'app_secret' => 'c28e31f7362f6d61e1d857dde16605d2',
	'default_graph_version' => 'v2.5',
]);
/*$fb = new Facebook\Facebook([
	'app_id' => '1427902737452009',
	'app_secret' => '6b05d76acc6cb63c7a051401ab5c2cb1',
	'default_graph_version' => 'v2.5',
]);*/
$helper = $fb->getRedirectLoginHelper();
$permissions = ['email', 'user_likes']; // optional
$loginUrl = $helper->getLoginUrl('https://www.whyzlist.com/login-callback', $permissions);

require('includes/navbar.php');
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	require (MYSQL);
	// Validate the username
	if (!empty($_POST['email'])) {
		$email = mysqli_real_escape_string ($dbc, $_POST['email']);
	} else {
		$email = FALSE;
		$email_error = '<p class="error">You forgot to enter your Email</p>';
	}
	// Validate the password:
	if (!empty($_POST['pass'])) {
		$pass = mysqli_real_escape_string ($dbc, $_POST['pass']);
	} else {
		$pass = FALSE;
		$pass_error = '<p class="error">You forgot to enter your password</p>';
	}
	if ($email && $pass) { // Everything is OK.

		// Query the database
		$q = "SELECT * FROM users WHERE (email='$email' AND pass=SHA1('$pass')) and active IS NULL";
		$r = mysqli_query ($dbc, $q) or trigger_error("Query: $q\n<br>MySQL Error: " . mysqli_error($dbc));

		if (@mysqli_num_rows($r) == 1) {
			// A match was made.
				// Register the values:

				$_SESSION = mysqli_fetch_array ($r, MYSQLI_ASSOC);
				$_SESSION["IsLoggedIn"] = true;
				$_SESSION['timeout'] = time(604800);
				mysqli_free_result($r);
				mysqli_close($dbc);

				// Redirect the user:
				$url = BASE_URL . 'logged_in.php';

				// Define the URL.
				header("Location: $url");

				ob_end_clean(); // Delete the buffer.
				exit(); // Quit the script.
		} else { // No match was made.

			$mismatch = '<p class="error">Either the email address or the password entered do not match those on file or you have not yet activated your account.</p>';

		}
	} else { // If everything wasn't OK.
	$try_again =  '<p class="error">Please try again.</p>';
	}
	mysqli_close($dbc);
}

echo '<br /><br /><a href="facebook_log">Log in with Facebook!</a>';

?><a href="twitter_log"><img src="./img/twitter.jpg"/></a>
<a href="googel_log"><img src="./img/googel.jpg"/></a>
<div class="m-top-15" style="background: url('./img/backgrounds/triangles_blue.svg') center center rgba(255,255,255,0.025); background-size:150px,150px;">
<br>
<div class="container m-top-25">
	<div class="row">
		<div class="col-lg-offset-8 col-lg-4">
			<div class="panel p-15">
				<h1>Login</h1>
				<form action="login" method="post" class="clearfix">
					<div class="form-group">
						<label style="color:#000">Email</label>
						<?php if (isset($email_error)) { echo '<pre class="bg-warning">' . $email_error . '</pre>'; } else { NULL; } ?>
						<input class="form-control" type="text" name="email" maxlength="60" value="<?php if (isset($email)) { echo $email; } ?>"></label>
					</div>
					<div class="form-group <?php if (isset($missmatch)) { echo 'has-warning'; } ?>">
						<label style="color:#000">Password</label>
						<?php if (isset($pass_error)) { echo '<pre class="bg-warning">' . $pass_error . '</pre>'; } else { NULL; } ?>
						<input class="form-control" type="password" name="pass" maxlength="20">
					</div>
					<?php if (isset($mismatch)) { echo '<pre class="bg-warning">' . $mismatch . '</pre>'; } else { NULL; } ?>


					<input class="form-control btn btn-primary m-bottom-15" type="submit" name="submit" value="Login">
				</form>
				<div class="text-center"><a class="btn btn-success form-control" href="./register.php"><strong>Register</strong></a></div>

			</div>
    	</div>
    </div>

  </div>
</div>
</div>

<?php include ('includes/footer.html') ?>
