<?php
	/*Scott Kinder
	Final website page
	*/
	//Includes common files for the page and writes the header.
	include("indexcommon.php");
	
	$db_host = "kinders.vergil.u.washington.edu";
	$db_username = "root";
	$db_pass = "sd1ee4an94";
	$db_name = "schoolcrime";

	$db = new PDO("mysql:dbname=$db_name;port=5794;host=$db_host", "$db_username", "$db_pass");

	//Select all states for option select's
	$allStateSql = "SELECT st.state_name, st.state_abbv
					FROM states st
					ORDER BY st.state_name";

	$stateStmt = $db->prepare($allStateSql);
	$stateStmt->execute();
	$allStates = $stateStmt->fetchAll();
	//end select states

	//Select the first state (always alabama), first year (10), and first location
	//type (on campus) total number of schools
	$firstState = "AL";
	$year = 10;
	$locationName = "oncampus";
	$firstSchoolSql = "SELECT count(*) AS totalschools, sum((ar.weapon + ar.drug + ar.liquor)) AS totalcrimes, sum(sc.num_of_men + sc.num_of_women) AS totalstudents
					FROM schools sc
						JOIN states st ON st.state_id = sc.state_id
						JOIN crime_records cr ON cr.school_id = sc.school_id
						JOIN arrest ar ON ar.record_id = cr.record_id
						JOIN location_type lt ON lt.location_type_id = cr.location_type_id
					WHERE st.state_abbv = :stateabbv
					AND ar.year = :year
					AND lt.type_name = :locationname
					GROUP BY (sc.state_id)";

	$firstSchoolStmt = $db->prepare($firstSchoolSql);
	$firstSchoolStmt->bindParam(':stateabbv', $firstState);
	$firstSchoolStmt->bindParam(':year', $year);
	$firstSchoolStmt->bindParam(':locationname', $locationName);
	$firstSchoolStmt->execute();
	$firstTotalSchools = $firstSchoolStmt->fetch();
	//end select states

	write_header();
?>
	<div id="maparea">
		<p>
			Click on the states to see all the colleges in that state. From there you can see more information about each individual college, or all about the state combined.
		</p>

		<img usemap="#Map" src="http://students.washington.edu/kinders/340final/usamap_1.jpg" alt="Map of the United States of America" border="0" height="344" width="550" />
		<map id="Map" name="Map">
			<area id="AL" class="tooltip" title="Alabama" shape="poly" coords="394,213,367,211,366,266,372,270,379,273,379,263,401,261,403,253,403,242" alt="Alabama" />

			<area id="AK" class="tooltip" title="Alaska" shape="poly" coords="108,247,119,253,132,259,142,261,136,283,136,310,161,327,170,338,161,340,148,324,130,311,118,305,111,310,102,316,100,325,78,325,48,330,88,312,79,304,72,295,72,285,88,279,80,270,84,263,92,254,100,249" alt="Alaska" />

			<area id="AZ" class="tooltip" title="Arizona" shape="poly" coords="103,169,153,177,144,248,121,244,82,221,88,222,86,209,93,205,93,192,93,177,100,178" alt="Arizona" />

			<area id="AR" class="tooltip" title="Arkansas" shape="poly" coords="300,197,304,238,306,238,310,240,335,239,337,230,348,214,352,203,352,201,357,193" alt="Arkansas" />

			<area id="CA" class="tooltip" title="California" shape="poly" coords="17,80,59,90,50,129,93,200,89,207,85,212,86,220,57,217,55,209,49,201,28,182,24,160,18,157,20,145,17,127,11,119,15,110,12,99,18,92" alt="California" />

			<area id="CO" class="tooltip" title="Colorado" shape="poly" coords="163,129,227,136,223,186,155,177" alt="Colorado" />

			<area id="CT" class="tooltip" title="Connecticut" shape="poly" coords="499,103,505,102,514,99,516,107,510,111,503,110,499,109" alt="Connecticut" />
			<area id="CT" class="tooltip" title="Connecticut" shape="rect" coords="523,151,544,166" alt="Connecticut" />

			<area id="DE" class="tooltip" title="Deleware" shape="poly" coords="492,154,496,154,497,149,491,142,487,138,488,147" alt="Deleware" />
			<area id="DE" class="tooltip" title="Deleware" shape="rect" coords="523,182,544,195" alt="Delaware" />

			<area id="DC" class="tooltip" title="District of Columbia" shape="poly" coords="472,152,478,153,480,157,475,156" alt="District of Columbia" />
			<area id="DC" class="tooltip" title="District of Columbia" shape="rect" coords="523,211,544,225" alt="District of Columbia" />

			<area id="FL" class="tooltip" title="Florida" shape="poly" coords="376,265,379,273,399,272,409,281,418,275,423,275,430,286,430,299,437,314,453,330,462,327,461,321,465,317,464,311,453,286,444,268,442,260,447,263,415,263,407,260" alt="Florida" />

			<area id="GA" class="tooltip" title="Georgia" shape="poly" coords="423,211,407,207,394,213,406,246,406,248,403,254,407,265,439,266,441,258,445,255,447,238" alt="Georgia" />

			<area id="HI" class="tooltip" title="Hawaii" shape="poly" coords="221,334,231,326,217,313,215,322,187,318,187,298,157,290,166,285,201,300,211,305" alt="Hawaii" />

			<area id="ID" class="tooltip" title="Idaho" shape="poly" coords="116,14,121,14,120,29,129,54,125,61,132,71,142,79,147,79,150,79,144,113,90,98,92,85,98,73,102,61,105,54,107,45" alt="Idaho" />

			<area id="IL" class="tooltip" title="Illinois" shape="poly" coords="343,117,342,125,336,130,330,148,345,166,345,173,345,177,353,183,360,191,362,181,365,177,369,165,367,129,358,117" alt="Illinois" />

			<area id="IA" class="tooltip" title="Iowa" shape="poly" coords="283,107,283,124,292,143,328,143,334,134,340,124,332,115,332,106" alt="Iowa" />

			<area id="IN" class="tooltip" title="Indiana" shape="poly" coords="394,125,367,128,368,170,371,176,384,171,397,160" alt="Indiana" />

			<area id="KS" class="tooltip" title="Kansas" shape="poly" coords="226,154,224,186,298,188,298,162,292,151,228,148,227,150" alt="Kansas" />

			<area id="KY" class="tooltip" title="Kentucky" shape="poly" coords="424,165,406,160,400,153,382,173,366,173,365,179,356,191,347,193,352,193,414,186,427,174,423,169" alt="Kentucky" />

			<area id="LA" class="tooltip" title="Louisiana" shape="poly" coords="303,240,339,237,341,246,335,266,353,268,356,274,357,277,357,283,356,282,351,286,335,285,328,278,322,281,313,279,309,283,312,270,307,254" alt="Louisiana" />

			<area id="ME" class="tooltip" title="Maine" shape="poly" coords="510,57,513,48,511,40,516,27,525,28,533,37,530,30,536,47,545,49,548,55,541,60,535,63,532,70,529,75,523,80,521,85" alt="Maine" />

			<area id="MD" class="tooltip" title="Maryland" shape="poly" coords="449,149,455,144,461,143,466,147,469,150,478,153,484,162,482,152,481,145,484,139,489,159,495,158,490,153,487,144,478,138,467,139,451,142,444,143" alt="Maryland" />
			<area id="MD" class="tooltip" title="Maryland" shape="rect" coords="523,197,543,210" alt="Maryland" />

			<area id="MA" class="tooltip" title="Massachusetts" shape="poly" coords="498,94,516,88,520,86,522,90,520,94,525,95,528,99,532,98,527,102,522,103,518,98,512,99,506,101,499,102" alt="Massachusetts" />
			<area id="MA" class="tooltip" title="Massachusetts" shape="rect" coords="528,109,550,122" alt="Massachusetts" />

			<area id="MI" class="tooltip" title="Michigan" shape="poly" coords="373,129,412,124,412,117,414,101,409,94,402,96,400,93,403,78,384,70,389,62,399,61,382,62,361,66,355,62,357,54,338,67,355,71,360,79,372,72,389,63,389,65,384,78,380,80,373,92,377,107" alt="Michigan" />

			<area id="MN" class="tooltip" title="Minnesota" shape="poly" coords="341,48,326,48,317,43,307,41,299,38,279,37,284,76,286,82,285,87,284,96,283,107,332,108,322,96,317,83,323,66" alt="Minnesota" />

			<area id="MS" class="tooltip" title="Mississippi" shape="poly" coords="366,214,349,211,338,227,342,249,335,267,358,269,357,272,368,272,365,242" alt="Mississippi" />

			<area id="MO" class="tooltip" title="Missouri" shape="poly" coords="288,140,297,157,298,160,300,186,302,194,353,195,349,197,355,191,355,187,344,171,340,160,332,148,328,141" alt="Missouri" />

			<area id="MT" class="tooltip" title="Montana" shape="poly" coords="122,17,219,33,214,82,152,74,151,80,134,77,129,66,125,59,127,51,128,43,119,26" alt="Montana" />

			<area id="NE" class="tooltip" title="Nebraska" shape="poly" coords="210,113,208,131,229,135,229,147,291,150,287,128,279,117,213,110" alt="Nebraska" />

			<area id="NV" class="tooltip" title="Nevada" shape="poly" coords="61,89,116,106,100,177,93,175,91,194,50,131" alt="Nevada" />

			<area id="NH" class="tooltip" title="New Hampshire" shape="poly" coords="506,61,509,58,520,85,505,90" alt="New Hampshire" />
			<area id="NH" class="tooltip" title="New Hampshire" shape="rect" coords="485,41,506,55" alt="New Hampshire" />

			<area id="NJ" class="tooltip" title="New Jersey" shape="poly" coords="490,116,497,118,496,123,498,131,495,139,489,137,488,134" alt="New Jersey" />
			<area id="NJ" class="tooltip" title="New Jersey" shape="rect" coords="523,167,544,181" alt="New Jersey" />

			<area id="NM" class="tooltip" title="New Mexico" shape="poly" coords="154,177,213,184,210,248,172,246,154,245,150,250,151,248,143,248" alt="New Mexico" />

			<area id="NY" class="tooltip" title="New York" shape="poly" coords="499,123,505,120,506,119,512,113,511,112,499,109,490,64,475,68,470,76,468,84,465,91,452,96,445,98,445,104,436,115,481,106,489,114" alt="New York" />

			<area id="NC" class="tooltip" title="North Carolina" shape="poly" coords="431,184,418,197,407,210,422,210,434,203,445,204,454,205,463,208,471,215,478,210,485,202,491,194,498,180,489,176" alt="North Carolina" />

			<area id="ND" class="tooltip" title="North Dakota" shape="poly" coords="219,32,278,37,283,76,216,71" alt="North Dakota" />

			<area id="OH" class="tooltip" title="Ohio" shape="poly" coords="432,121,422,126,412,126,393,126,397,156,404,156,408,163,420,165,431,147,434,143,437,114,425,125,413,125,396,126" alt="Ohio" />

			<area id="OK" class="tooltip" title="Oklahoma" shape="poly" coords="213,185,299,188,302,228,305,231,293,232,273,233,243,217,243,193,212,193" alt="Oklahoma" />

			<area id="OR" class="tooltip" title="Oregon" shape="poly" coords="41,33,19,77,23,78,29,82,90,99,97,79,98,72,106,52,90,45,62,47,49,40" alt="Oregon" />

			<area id="PA" class="tooltip" title="Pennsylvania" shape="poly" coords="442,114,437,117,436,135,434,144,486,134,489,128,489,121,489,114,480,108" alt="Pennsylvania" />

			<area id="PR" class="tooltip" title="Puerto Rico" shape="poly" coords="500,325,487,332,499,314,489,312,490,301,507,301,508,312,505,322,514,321,517,325,512,333,503,339,490,341,486,333" alt="Puerto Rico" />

			<area id="RI" class="tooltip" title="Rhode Island" shape="poly" coords="518,98,523,104,517,107,514,100" alt="Rhode Island" />
			<area id="RI" class="tooltip" title="Rhode Island" shape="rect" coords="523,129,544,142" alt="Rhode Island" />

			<area id="SC" class="tooltip" title="South Carolina" shape="poly" coords="421,209,450,239,464,222,470,215,461,206,446,208,445,203,429,204" alt="South Carolina" />

			<area id="SD" class="tooltip" title="South Dakota" shape="poly" coords="216,71,212,109,256,112,282,118,283,103,282,83,282,76" alt="South Dakota" />

			<area id="TN" class="tooltip" title="Tennessee" shape="poly" coords="356,194,431,187,412,203,401,210,350,213" alt="Tennessee" />

			<area id="TX" class="tooltip" title="Texas" shape="poly" coords="212,193,208,248,170,244,176,256,184,265,189,281,202,287,212,275,221,278,234,290,241,308,248,322,257,327,267,329,268,325,265,309,280,296,295,288,301,283,311,280,310,274,305,254,304,239,304,232,274,231,240,216,242,193,215,192" alt="Texas" />

			<area id="VI" class="tooltip" title="US Virgin Islands" shape="poly" coords="540,281,522,280,524,294,541,294,534,300,534,300,544,324,537,327,529,318,526,298,540,288,540,297" alt="US Virgin Islands" />

			<area id="UT" class="tooltip" title="Utah" shape="poly" coords="115,106,101,168,154,176,163,128,142,126,144,111" alt="Utah" />

			<area id="VT" class="tooltip" title="Vermont" shape="poly" coords="497,93,504,91,505,71,506,62,499,63,491,65" alt="Vermont" />
			<area id="VT" class="tooltip" title="Vermont" shape="rect" coords="459,42,480,55" alt="Vermont" />

			<area id="VA" class="tooltip" title="Virginia" shape="poly" coords="469,150,471,151,463,148,457,154,452,155,445,166,435,174,427,172,414,188,491,176,482,164,482,159,474,156" alt="Virginia" />

			<area id="WA" class="tooltip" title="Washington" shape="poly" coords="63,1,117,14,109,49,106,52,89,46,54,43,48,46,49,40,43,36,45,2,60,11,60,23,68,13" alt="Washington" />

			<area id="WV" class="tooltip" title="West Virginia" shape="poly" coords="446,144,436,144,443,149,438,147,438,142,431,148,423,160,419,167,429,173,433,173,443,167,450,158,459,155,461,149,464,147,458,144,450,150" alt="West Virginia" />

			<area id="WI" class="tooltip" title="Wisconsin" shape="poly" coords="333,63,322,67,318,80,318,95,331,105,332,115,338,119,363,117,360,102,369,81,356,81,366,76" alt="Wisconsin" />

			<area id="WY" class="tooltip" title="Wyoming" shape="poly" coords="153,75,214,82,208,135,142,124" alt="Wyoming" />

		</map>
	</div>
	<div id="results">
		<div id="container">
			<div id="selectors">
				<div id="statename" class="selector">
					<p class="selectorname">State Name: </p>
					<select id="statenameselect" class="selectorselect">
						<?php
							foreach ($allStates as $state) {
								?>
								<option value="<?= $state['state_abbv'] ?>"><?= $state['state_name'] ?></option>
								<?php
							}
						?>
					</select>
				</div>
				<div id="year" class="selector">
					<p class="selectorname">Year: </p>
					<select id="yearselect" class="selectorselect">
						<option value="10">10</option>
						<option value="11">11</option>
						<option value="12">12</option>
						<option value="allyears">All</option>
					</select>
				</div>
				<div id="locationtype" class="selector">
					<p class="selectorname">Location Type: </p>
					<select id="locationselect" class="selectorselect">
						<option value="oncampus">On Campus</option>
						<option value="noncampus">Off Campus</option>
						<option value="publicproperty">Public Property</option>
						<option value="residencehall">Residence Halls</option>
						<option value="alllocations">All</option>
					</select>
				</div>
				<div id="crimetype" class="selector">
					<p class="selectorname">Crime Type: </p>
					<select id="crimeselect" class="selectorselect">
						<option value="arrest">Arrest</option>
						<option value="discipline">Discipline</option>
						<option value="crime">Crime</option>
					</select>
				</div>
			</div>
			<div id="statestats">
				<div id="totalschoolsarea">
					<p>
						Total Schools: <span id="totalschools"><?= $firstTotalSchools['totalschools'] ?></span>
					</p>
				</div>
				<div id="totalstudentsarea">
					<p>
						Total Students: <span id="totalstudents"><?= number_format($firstTotalSchools['totalstudents'], 0, ".", ",") ?></span>
					</p>
				</div>
				<div id="bottomsubmitarea">
					<button id="buttomsubmit">Show Schools</button>
				</div>
				<div id="totalrowsarea">
					<select id="totalrows">
						<option value="10">10 Schools</option>
						<option value="25">25 Schools</option>
						<option value="50">50 Schools</option>
						<option value="100">100 Schools</option>
						<option value="200">200 Schools</option>
					</select>
				</div>
				<div id="totalcrimesarea">
					<p>
						Total Crimes: <span id="totalcrimes"><?= $firstTotalSchools['totalcrimes'] ?></span>
					</p>
				</div>
			</div>
			<div id="bottomtablearea">
			</div>
		</div>
	</div>

	<?php
	/*
	<p>
		<?php
			//$prepared = pg_prepare($db, "my_query", 'SELECT h.hashtag FROM hashtags h LIMIT 10');
			//$prepared = pg_execute($db, "my_query", array());
			$oneSql = "SELECT h.hashtag FROM hashtags h LIMIT 10";
			$stmt = $db->prepare($oneSql);
			$stmt->execute();
			$res = $stmt->fetch();
		?>
		<?= $res['hashtag'] ?>
	</p>
	*/
	?>
<?php
	//Writes the footer for the page
	write_footer();
	$db = null;
?>