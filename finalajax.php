<?php
	include('indexcommon.php');
	date_default_timezone_set('America/Los_Angeles');

	if (isset($_POST["type"])) {
		$sanitized = test_input($_POST["type"]);
		if ($sanitized == "statechange") {
			if (isset($_POST["state"]) && isset($_POST["locationname"]) && isset($_POST["year"]) && isset($_POST["crimetype"])) {
				$sanitized = test_input($_POST["state"]);
				if (strlen($sanitized) == 2) {
					$db_host = "kinders.vergil.u.washington.edu";
					$db_username = "root";
					$db_pass = "sd1ee4an94";
					$db_name = "schoolcrime";

					try {
					    $db = new PDO("mysql:dbname=$db_name;port=5794;host=$db_host", "$db_username", "$db_pass");
					} catch (PDOException $e) {
					    print "Error!: " . $e->getMessage() . "<br/>";
					    die();
					}

					$yearTimesThree = 1;
					$locationTimesFour = 1;

					$firstState = $_POST["state"];
					$year = $_POST["year"];
					$locationName = $_POST["locationname"];
					$crimeType = $_POST["crimetype"];
					if ($crimeType == "arrest") {
						$schoolSql = "SELECT count(*) AS totalschools, sum((ar.weapon + ar.drug + ar.liquor)) AS totalcrimes, sum(sc.num_of_men + sc.num_of_women) AS totalstudents
										FROM schools sc
											JOIN states st ON st.state_id = sc.state_id
											JOIN crime_records cr ON cr.school_id = sc.school_id
											JOIN arrest ar ON ar.record_id = cr.record_id
											JOIN location_type lt ON lt.location_type_id = cr.location_type_id
										WHERE st.state_abbv = :stateabbv";


						if ($year != "allyears") {
							$schoolSql .= " AND ar.year = :year ";
						} else {
							$yearTimesThree = 3;
						}
						if ($locationName != "alllocations") {
							$schoolSql .= " AND lt.type_name = :locationname ";
						} else {
							$locationTimesFour = 4;
						}
						$schoolSql .= " GROUP BY (sc.state_id)";
					} else if ($crimeType == "discipline") {
						$schoolSql = "SELECT count(*) AS totalschools, sum((ar.weapon + ar.drug + ar.liquor)) AS totalcrimes, sum(sc.num_of_men + sc.num_of_women) AS totalstudents
										FROM schools sc
											JOIN states st ON st.state_id = sc.state_id
											JOIN crime_records cr ON cr.school_id = sc.school_id
											JOIN discipline ar ON ar.record_id = cr.record_id
											JOIN location_type lt ON lt.location_type_id = cr.location_type_id
										WHERE st.state_abbv = :stateabbv";
						if ($year != "allyears") {
							$schoolSql .= " AND ar.year = :year ";
						} else {
							$yearTimesThree = 3;
						}
						if ($locationName != "alllocations") {
							$schoolSql .= " AND lt.type_name = :locationname ";
						} else {
							$locationTimesFour = 4;
						}
						$schoolSql .= " GROUP BY (sc.state_id)";
					} else {
						$schoolSql = "SELECT count(*) AS totalschools, sum((ar.murd + ar.negm + ar.forcib + ar.nonfor + ar.robbe + ar.agga + ar.burgla + ar.vehic + ar.arson)) AS totalcrimes, sum(sc.num_of_men + sc.num_of_women) AS totalstudents
										FROM schools sc
											JOIN states st ON st.state_id = sc.state_id
											JOIN crime_records cr ON cr.school_id = sc.school_id
											JOIN crime ar ON ar.record_id = cr.record_id
											JOIN location_type lt ON lt.location_type_id = cr.location_type_id
										WHERE st.state_abbv = :stateabbv";

						if ($year != "allyears") {
							$schoolSql .= " AND ar.year = :year ";
						} else {
							$yearTimesThree = 3;
						}
						if ($locationName != "alllocations") {
							$schoolSql .= " AND lt.type_name = :locationname ";
						} else {
							$locationTimesFour = 4;
						}
						$schoolSql .= " GROUP BY (sc.state_id)";
					}
					$stateUpdateStmt = $db->prepare($schoolSql);
					$stateUpdateStmt->bindParam(':stateabbv', $firstState);
					if ($year != "allyears") {
						$stateUpdateStmt->bindParam(':year', $year);
					}
					if ($locationName != "alllocations") {
						$stateUpdateStmt->bindParam(':locationname', $locationName);
					}
					$stateUpdateStmt->execute();
					$stateUpdate = $stateUpdateStmt->fetch();
					echo "" . $stateUpdate['totalschools'] / $yearTimesThree / $locationTimesFour . ":" . number_format($stateUpdate['totalcrimes'], 0, ".", ",") . ":" . number_format($stateUpdate['totalstudents'] / $yearTimesThree / $locationTimesFour, 0, ".", ",");
					$db = null;
				}
			}
		} 
		else if ($sanitized == "table") {
			if (isset($_POST["locationname"]) && isset($_POST["year"]) && isset($_POST["state"]) && isset($_POST["crimetype"]) && isset($_POST["totalrows"])) {
				$db_host = "kinders.vergil.u.washington.edu";
				$db_username = "root";
				$db_pass = "sd1ee4an94";
				$db_name = "schoolcrime";

				try {
				    $db = new PDO("mysql:dbname=$db_name;port=5794;host=$db_host", "$db_username", "$db_pass");
				} catch (PDOException $e) {
				    print "Error!: " . $e->getMessage() . "<br/>";
				    die();
				}

				$totalRows = $_POST["totalrows"];
				$locationName = $_POST["locationname"];
				$year = $_POST["year"];
				$state = $_POST["state"];
				$crimeType = $_POST["crimetype"];
				$groupby = false;

				
				if ($year != "allyears" && $locationName != "alllocations") { 
					if ($crimeType == "crime") {
						$schoolSql = "SELECT sc.school_name, (ar.murd + ar.negm + ar.forcib + ar.nonfor + ar.robbe + ar.agga + ar.burgla + ar.vehic + ar.arson) AS totalcrimes, ar.murd AS murder, ar.negm, ar.forcib, ar.nonfor, ar.robbe, ar.agga, ar.burgla, ar.vehic, ar.arson";
					} else {
						$schoolSql = "SELECT sc.school_name, (ar.weapon + ar.drug + ar.liquor) AS totalcrimes, ar.weapon, ar.drug, ar.liquor";
					}
				} else {
					$groupby = true;
					if ($crimeType == "crime") {
						$schoolSql = "SELECT sc.school_name, sum(ar.murd + ar.negm + ar.forcib + ar.nonfor + ar.robbe + ar.agga + ar.burgla + ar.vehic + ar.arson) AS totalcrimes, sum(ar.murd) AS murder, sum(ar.negm) AS negm, sum(ar.forcib) AS forcib, sum(ar.nonfor) AS nonfor, sum(ar.robbe) AS robbe, sum(ar.agga) AS agga, sum(ar.burgla) AS burgla, sum(ar.vehic) AS vehic, sum(ar.arson) AS arson";
					} else {
						$schoolSql = "SELECT sc.school_name, sum(ar.weapon + ar.drug + ar.liquor) AS totalcrimes, sum(ar.weapon) AS weapon, sum(ar.drug) AS drug, sum(ar.liquor) as liquor";
					}
				}

				$schoolSql .= " FROM schools sc
									JOIN states st ON st.state_id = sc.state_id
									JOIN crime_records cr ON cr.school_id = sc.school_id
									JOIN location_type lt ON lt.location_type_id = cr.location_type_id";

				if ($crimeType == "crime") {
					$schoolSql .= " JOIN crime ar ON ar.record_id = cr.record_id";
				} elseif ($crimeType == "arrest") {
					$schoolSql .= " JOIN arrest ar ON ar.record_id = cr.record_id";
				} else {
					$schoolSql .= " JOIN discipline ar ON ar.record_id = cr.record_id";
				}
				
				$schoolSql .= " WHERE st.state_abbv = :stateabbv ";

				if ($year != "allyears") {
					$schoolSql .= " AND ar.year = :year ";
				}
				if ($locationName != "alllocations") {
					$schoolSql .= " AND lt.type_name = :locationname ";
				}

				if ($groupby === true) {
					$schoolSql .= " GROUP BY sc.school_name ";
				}
				
				$schoolSql .= " ORDER BY totalcrimes DESC";

				$stateUpdateStmt = $db->prepare($schoolSql);
				$stateUpdateStmt->bindParam(':stateabbv', $state);
				if ($year != "allyears") {
					$stateUpdateStmt->bindParam(':year', $year);
				}
				if ($locationName != "alllocations") {
					$stateUpdateStmt->bindParam(':locationname', $locationName);
				}
				$stateUpdateStmt->execute();

				?>
					<div class="datagrid">
						<table>
							<thead>
								<tr id="columns">
									<th>#</th><th>School Name</th>
									<?php
										for ($i = 2; $i < $stateUpdateStmt->columnCount(); $i++) {
											?>
											<th><?= $stateUpdateStmt->getColumnMeta($i)['name'] ?></th>
											<?php
										}
									?>
									<th>total</th>
								</tr>
							</thead>
							<tbody>
							<?php
								$stateUpdate = $stateUpdateStmt->fetchAll();
								$num = 1;
								$totalRows = (int) $totalRows;
								foreach ($stateUpdate as $college) {
									if ($num <= $totalRows) {
										if ($num % 2 == 0) {
											?>
											<tr>
										<?php } else {
											?>
											<tr class="alt">
										<?php } 
											if ($crimeType == "crime") {
											?>
												<td><?= $num ?></td><td><?= $college['school_name'] ?></td>
												<td><?= $college['murder'] ?></td>
												<td><?= $college['negm'] ?></td>
												<td><?= $college['forcib'] ?></td>
												<td><?= $college['nonfor'] ?></td>
												<td><?= $college['robbe'] ?></td>
												<td><?= $college['agga'] ?></td>
												<td><?= $college['burgla'] ?></td>
												<td><?= $college['vehic'] ?></td>
												<td><?= $college['arson'] ?></td>
												<td><?= $college['totalcrimes'] ?></td>
											<?php 
										} else {
											?>
												<td><?= $num ?></td><td><?= $college['school_name'] ?></td>
												<td><?= $college['weapon'] ?></td>
												<td><?= $college['drug'] ?></td>
												<td><?= $college['liquor'] ?></td>
												<td><?= $college['totalcrimes'] ?></td>
											<?php
										}
									}
									?>
										</tr>
									<?php
									$num++;
								}
							?>
							</tbody>
						</table>
					</div>
				<?php
			}
		}
	}

?>