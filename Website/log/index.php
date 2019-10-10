<!DOCTYPE html>
<html lang="en">

	<?php include( $_SERVER['DOCUMENT_ROOT'] . "/elements/head.php"); ?>

	<body>
		<?php include( $_SERVER['DOCUMENT_ROOT'] . "/elements/header.php"); ?>

		<div class="container">
			<br />

<?php
	if( isset($_POST['logPassword'])){
                // set this password for security
		if( $_POST['logPassword'] == "password"){
		
			require_once($_SERVER['DOCUMENT_ROOT'] . "/lib/build.class.php");
			$buildClass = new build;
			$buildClass->log();
			
		}
		else{ echo "<div class='error'><p>Incorrect password</p></div>"; }
	}
	else{
?>

		<form name="form" method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
			<div class="vi-input-wrap">
				<p class="label">Password:</p>
				<input class="vi-input" style="width: 200px;" type="password" name="logPassword" />
				<input type="submit" value="Submit" />
			</div>
		</form>

<?php
	}
?>

		</div> <!-- /.container -->
	</body>
</html>

