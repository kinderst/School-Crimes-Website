<?php
	/*Scott Kinder, CSE 154 Homework 5, Manvir Singh AG, 5/10/2014
	This is the Remember The Milk common code page, containing code that is used
	throughout the sites.
	*/

	//Writes the html contents of the header of the page
	function write_header() {
		?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title>Crime Across American Colleges</title>
		<link href="final.css" type="text/css" rel="stylesheet" />
		<link href="https://webster.cs.washington.edu/images/todolist/favicon.ico" 
			type="image/ico" rel="shortcut icon" />
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
		<script src="finaljquery.js" type="text/javascript"></script>
	</head>

	<body>
		<div id="headerarea" class="head">
			<div id="title">
				Crime Across American College Campuses 2010-2012
			</div>
			<div id="navbuttons">
				<a href="index.php" id="nav_home"></a>
			</div>
		</div>
	<?php }

	//Writes the html contents of the footer of the page
	function write_footer() {
		?>
		<div id="footerarea" class="foot">
			<p>
				Scott Kinder Final Project &copy;
			</p>

			<div id="w3c">
				<a href="https://webster.cs.washington.edu/validate-html.php">
					<img src="https://webster.cs.washington.edu/images/w3c-html.png" 
						alt="Valid HTML" /></a>
				<a href="https://webster.cs.washington.edu/validate-css.php">
					<img src="https://webster.cs.washington.edu/images/w3c-css.png" 
						alt="Valid CSS" /></a>
			</div>
		</div>
	</body>
</html>
	<?php }

	//corrects data
	function test_input($data) {
		$oldData = $data;
		$data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data);
		if (!($oldData == $data)) {
			//report error with ip address and data, and timestamp (Not oldData! :))
		}
		return $data;
	}
?>