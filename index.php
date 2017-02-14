<?php 
// app variables
require_once('appvars.php');
require_once('connection_vars.php');

// database connection
$dbc = mysqli_connect(IPG_HOST, IPG_USER, IPG_PASSWORD, IPG_DATABASE);

?>

<!DOCTYPE html>
	<html>
	<head>
		<title></title>
	</head>
	<body>
	<?php
	$showForm = true; //shows form
	$showPreview = false; // shows preview 

	if( isset( $_REQUEST["submitSignup"] ) ){

		$name = $_REQUEST["firstname"];
		$email = $_REQUEST["email"];
		$photo = $_FILES;
		$photoName = $_FILES["file"]["name"];
		$showForm = false;

		if( !empty($name) && !empty($email) && !empty($photo) ){ 

			$fileType = $_FILES["file"]["type"];
			if( $fileType == "image/png" || $fileType == "image/gif" || 
				$fileType == "image/jpeg" || $fileType == "image/pjpeg"
			) {
				$target = IPG_UPLOADPATH . $photoName;

				if( move_uploaded_file($_FILES["file"]["tmp_name"], $target) ){

					$insertQuery = "INSERT INTO `ipg_table` VALUES (0, '".$name."', '".$email."', '".$photoName."', NOW());";
					$result = mysqli_query($dbc, $insertQuery);

					if( $result == 1){
						echo '<p class="success"> Photo upload is successful!</p>';
						$showPreview = true;
					} else{
						echo '<p class="error"> Something went wrong while uploading :( </p>';
					}
				}
			}

			//restore data to clean form
			$name = "";
			$email = "";
			$photo = "";
		} else{
			// validation errors
			echo '<p class="error"> Please enter all details for a successful signup</p>';
			$showForm = true;
		}
	}

	//close mysql connection to db
	

	if($showForm){
	?>	
		<div class="formContainer">
			<form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post" enctype="multipart/form-data">
				
			<div class="signupTxt">Sign up</div>
			<div class="elems">
				<label for="firstname">Name:</label>
				<input type="text" id="firstname" name="firstname" value="<?php if(!empty($name)) echo $name; ?>">
			</div>

			<div class="elems">
				<label for="email">Email:</label>
				<input type="email" id="email" name="email" value="<?php if(!empty($email)) echo $email; ?>">
			</div>

			<div class="elems">
				<label for="file">Upload photo</label>
				<input type="file" id="file" name="file" value="<?php if(!empty($photo)) echo $photo; ?>">
			</div>

			<div class="elems">
				<input type="submit" name="submitSignup">
			</div>

			</form>
		</div>
	<?php	
	}
	?>

	<?php 
		if( $showPreview ){
			$select = "SELECT * FROM ipg_table ORDER BY `date` desc";
			$result = mysqli_query($dbc, $select);
			$result = mysqli_fetch_all($result);
	?>	
		<label>Here is your preview - </label>
		<div class="previewContainer">
	<?php
			foreach ($result as $key => $value) {
	?>
		<div class="previewBlock">
			<section class="align-left"><?php echo $result[$key][2] ?></section>
			<img src="<?php echo IPG_UPLOADPATH . $result[$key][3] ?>" class="imageUploaded">
			<section>
				<label class="align-right"><?php echo $result[$key][4] ?></label>
			</section>
		</div>

	<?php 

		} //foreach

		} //showpreview
	?>
		</div>

	<?php 
		mysqli_close($dbc);
	 ?>
	</body>
	<style>
	.formContainer{
		padding: 10px;
	}
	.signupTxt{
		margin-bottom: 10px;
	}
	.elems{
		margin: 10px;
	}
	.error{
		color: red;
	}
	.success{
		color: green;
	}
	.previewBlock{
		width: 500px;
		height: 500px;
	}
	.previewBlock *{
		float: left;
		width: 100%;
	}
	.align-left{
		text-align: left;
		font-weight: bold;
		font-size: 15px;
	}
	.align-right{
		text-align: right;
		color: gray;
		font-size: 13px;
	}
	.previewContainer{
		padding: 10px;
	}
	</style>
	</html>