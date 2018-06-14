<?php
	/**
     * Project Planner
     * Project/Phases/View.php
     * Displays details of a specified phase
     * 
     * Team Hailstorm
     * ===============
     * Hemang Bhatt (hb6@umbc.edu)
     * Aidan Gray (graidan1@umbc.edu)
     * Cameron Hensel (chensel1@umbc.edu)
     * Jacob Lutz (jlutz1@umbc.edu)
     * Phuoc Nguyen (ej77536@umbc.edu)
     * Nirav Pancholi (nirav3@umbc.edu)
     * 
     */
    require_once(realpath(dirname(__FILE__)) . "/../../../classes/Session.class.php");
	require_once(realpath(dirname(__FILE__)) . "/../../../classes/User.class.php");
	Session::Start();

	// This page should be inaccessible if a user is not logged in
	if (!Session::UserLoggedIn())
	{
		header("Location: ../../login.php");
	}

	// Do not proceed if no phase is specified
	if (empty($_GET))
	{
		header("Location: ../../home.php");
	}

	// Attempt DB connection
	$conn = mysqli_connect($_SESSION["SERVER"], $_SESSION["DBUSER"], $_SESSION["DBPASS"], 
							$_SESSION["DATABASE"]);
	if (!$conn)
	{
		// Output error details
		die('Unable to connect.  Error: ' . mysqli_error($conn));
	}

	// Prepare phase ID for SQL query
    $phid = mysqli_real_escape_string($conn, $_GET['phid']);
    $sql = "SELECT * FROM Phases WHERE Phase_ID = '$phid'";
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset=utf-8 />
		<link href ="../../style.css" rel="stylesheet">
		<script type="text/JavaScript">
			// ConfirmDelete()
			// Prevents accidental phase deletion
			function ConfirmDelete(pid)
			{
				// Confirm that the user actually wants to delete the phase
				if (confirm("Once you delete this phase, it cannot be recovered.  "
							+ "Additionally, all associated tasks will be deleted.  "
							+ "Are you absolutely sure?"))
				{
					// Pass phase ID and delkey to Delete script
					window.location.href="./Delete.php?prid=<?php echo $_GET['prid']; ?>&p=" + pid 
											+ "&d=" + "<?php echo password_hash($_GET['phid'] 
																	. "delete" . $_GET['phid'], PASSWORD_BCRYPT); ?>";
				}
			}
		</script>
    </head>
    <body>
		<div class="w3-top w3-card w3-white" style="height:10%">
			<div class="w3-bar w3-padding">
				<a class="w3-bar-item" href="../../home.php"><h1>Project Planner</h1></a>
				<div class="w3-right">
					<a class="w3-bar-item" href="../../Users/View.php">Logged in as <?php echo $_SESSION['CURRENT_USER']->GetFirstName() . " " . $_SESSION['CURRENT_USER']->GetLastName() . " (" . $_SESSION['CURRENT_USER']->GetUsername() . ")";?></a>
					<a href="../../logout.php"><button class="w3-bar-item w3-button w3-red">Sign Out</button></a>
				</div>
			</div>
		</div>
        <div class="w3-container" style="margin-top:10%">
			<div class="w3-container w3-display-middle" style="width:50%">
				<a href="../View.php?proj=<?php echo $_GET['prid'] ?>"><button class="w3-button w3-green">Return to Project</button></a>
				<div class="w3-border w3-padding">
					<?php
						/**
						 * Displays phase details and Manager functions
						 */
						if ($result = mysqli_query($conn, $sql))
						{
							$count = mysqli_num_rows($result);
							$phase = mysqli_fetch_array($result);
							if ($count == 1)
							{
								// Display phase name and ID
								echo "<h1>Phase Name: " . $phase['Phase_Name'] . "</h1>";
								echo "<p>Phase ID#: " . $phase['Phase_ID'] . "</p>";

								// Display details on the author of the phase
								$userSql = "SELECT * FROM Users WHERE User_ID = " 
												. $phase['User_ID_FK'];
								if ($user = mysqli_query($conn, $userSql))
								{
									if (mysqli_num_rows($user) == 1)
									{
										$user = mysqli_fetch_array($user);
										echo "<p>Created by: " . $user['User_Firstname'] . " " 
												. $user['User_Lastname'] . " (" . $user['User_Name'] 
												. ")</p>";
									}
								}

								// Display phase description
								echo "<p>Phase Description: " . $phase['Phase_Description'] . "</p>";

								// If a manager is logged in, display Edit and Delete buttons
								if ($_SESSION['CURRENT_USER']->getUserRole() == 1){
									echo "<a href='./Edit.php?prid=" . $_GET["prid"] 
											. "&phid=" . $_GET["phid"] . "'><button class='w3-button w3-green'>"
											. "Edit Phase</button></a>";
									echo " <button class='w3-button w3-red' onclick='ConfirmDelete(" 
											. $_GET["phid"] . ")'>Delete Phase</button>";
								}
								
								// Display Assigned Users list
								echo "<h3>Assigned Users: </h3>";
								echo "<nav><ul class='w3-ul'>";
								$sql = "SELECT * FROM User_Assignments WHERE Phase_ID_FK = " 
											. $phase['Phase_ID'];
								if ($result = mysqli_query($conn, $sql))
								{
									while ($assign = mysqli_fetch_array($result))
									{
										$userSql = "SELECT * FROM Users WHERE User_ID = " 
														. $assign['User_ID_FK'];
										if ($userResult = mysqli_query($conn, $userSql))
										{
											$user = mysqli_fetch_array($userResult);
											echo "<li>" . $user['User_Firstname'] . " " 
													. $user['User_Lastname'] . " (" 
													. $user['User_Name'] . ")</li>";
										}
									}
								}
								echo "</ul></nav>";
							}
						}
						mysqli_close($conn);
					?>
				</div>
			</div>
        </div>
    </body>
</html>