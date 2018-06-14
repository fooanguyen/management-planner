<?php
	/**
     * Project Planner
     * Users/View.php
     * Displays details of a specified user
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
    require_once(realpath(dirname(__FILE__)) . "/../../classes/Session.class.php");
	require_once(realpath(dirname(__FILE__)) . "/../../classes/User.class.php");
    Session::Start();
    
    // This page should be inaccessible if a user is not logged in
	if (!Session::UserLoggedIn())
	{
		header("Location: ../login.php");
    }
    
    // Attempt DB connection
    $conn = mysqli_connect($_SESSION["SERVER"], $_SESSION["DBUSER"], $_SESSION["DBPASS"], 
                            $_SESSION["DATABASE"]);
	if (!$conn)
	{
        // Output error details
		die('Unable to connect.  Error: ' . mysqli_error($conn));
    }

    // Display specified user account
    // If none specified, display the details of the user currently logged in
    if (isset($_GET['uid']) && !empty($_GET) && $_SESSION['CURRENT_USER']->getUserRole() == 1)
    {
        $sql = "SELECT * FROM Users WHERE User_ID = " . mysqli_real_escape_string($conn, $_GET['uid']);
    }
    else
    {
        $sql = "SELECT * FROM Users WHERE User_ID = " . $_SESSION['CURRENT_USER']->getUserID();
    }
    if ($result = mysqli_query($conn, $sql))
    {
        $count = mysqli_num_rows($result);
        $user = mysqli_fetch_array($result);
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset=utf-8 />
        <link href ="../style.css" rel="stylesheet">
        <script type="text/JavaScript">
            // ConfirmDelete()
			// Prevents accidental user deletion
			function ConfirmDelete(uid)
			{
				if (confirm("Are you sure you want to delete this user?"))
				{
                    if (confirm("This will seriously delete all this user's information.  This cannot be undone.  "
                                    + "Are you absolutely sure?"))
                    {
                        <?php
                            /**
                             * Cancels deletion if only one manager account remains
                             * Otherwise, redirects to the Delete script
                             */
                            $sql = "SELECT * FROM Users WHERE User_Role = 1";
                            if ($result = mysqli_query($conn, $sql))
                            {
                                if (mysqli_num_rows($result) == 1 && $user['User_ID'] == mysqli_fetch_array($result)['User_ID'])
                                {
                                    echo "alert('You cannot delete the only existing manager account!');";
                                }
                                else
                                {
                                    echo "window.location.href='./Delete.php?uid=" . $user['User_ID'] 
                                            . "&d=" . password_hash($user['User_ID'] . "delete" 
                                            . $user['User_ID'], PASSWORD_BCRYPT) . "';";
                                }
                            }
                        ?>
                    }
				}
			}
		</script>
    </head>
    <body>
		<div class="w3-top w3-card w3-white" style="height:10%">
			<div class="w3-bar w3-padding">
				<a class="w3-bar-item" href="../home.php"><h1>Project Planner</h1></a>
				<div class="w3-right">
                    <a class="w3-bar-item" href="./View.php">Logged in as <?php echo $_SESSION['CURRENT_USER']->GetFirstName() . " " . $_SESSION['CURRENT_USER']->GetLastName() . " (" . $_SESSION['CURRENT_USER']->GetUsername() . ")";?></a>
					<a href="../logout.php"><button class="w3-bar-item w3-button w3-red">Sign Out</button></a>
				</div>
			</div>
		</div>
        <div class="w3-container" style="top:80%">
			<div class="w3-container w3-display-topmiddle" style="width:50%;top:12%">
                <a href="../home.php"><button class="w3-button w3-green">Return to Home</button></a>
                <div class="w3-border w3-padding">
                    <?php
                        /**
                         * Displays User details and manager functions
                         */
                        if ($count == 1)
                        {
                            // Display user's full name and role
                            echo "<h2>User: " . $user['User_Firstname'] . " " . $user['User_Lastname'] . "</h2>";
                            echo "<p>Username: " . $user["User_Name"] . "</p>";
                            echo "<p>Role: " . ($user['User_Role'] == 1 ? "Manager" : "Employee") . "</p></br>";
                    
                            // Display user's ID and censored password
                            echo "<p>User ID#: " . $user['User_ID'] . "</p>";
                            echo "<p>Password: *********</p></br>";

                            // Display user's remaining details
                            echo "<p>Date of Birth: " . $user['User_Birthdate'] . "</p>";
                            echo "<p>Address: " . $user['User_Street'] . ", " . $user['User_City'] . ", " 
                                    . $user['User_State'] . " " . $user['User_Zipcode'] . "</p>";
                            echo "<p>Email Address: " . $user['User_Email'] . "</p>";
                            echo "<p>Phone Number: " . $user['User_Phone'] . "</p>";
                        }

                        // If the user account is a manager or matches the user displayed,
                        // display Edit button
                        if ($_SESSION['CURRENT_USER']->GetUserRole() == 1 
                            || $user['User_ID'] == $_SESSION['CURRENT_USER']->GetUserID())
                        {
                            echo "<a href='./Edit.php" . (isset($_GET['uid']) ? "?uid=" 
                                    . $_GET['uid'] : "") . "'><button class='w3-button w3-green'>Edit User Account"
                                    . "</button></a> ";
                        }

                        // If the user account matches the user displayed,
                        // display Delete button
                        if ($user['User_ID'] == $_SESSION['CURRENT_USER']->GetUserID())
                        {
                            echo "<button class='w3-button w3-red' onclick='ConfirmDelete(" . $user['User_ID'] 
                                    . ")'>Delete User</button>";
                        }
                    ?>
                </div>
            </br>
            <div class="w3-border w3-padding">
                <?php
                    /**
                     * Displays list of all users to view details
                     * only if a manager is logged in
                     */
                    if ($_SESSION['CURRENT_USER']->GetUserRole() == 1)
                    {
                        echo "<h3>Manage Users</h3>";

                        echo "<nav><ul class='w3-ul w3-hoverable'>";
                
                        // Retrieve and display all users
                        $sql = "SELECT * FROM Users";
                        if ($result = mysqli_query($conn, $sql))
                        {
                            while ($user = mysqli_fetch_array($result))
                            {
                                if ($user['User_ID'] != 0)
                                {
                                    echo "<li style='cursor:pointer' onclick='window.location.href=\"./View.php?uid=" 
                                            . $user['User_ID'] . "\";'>" . $user['User_Firstname'] . " " 
                                            . $user['User_Lastname'] . " (" . $user['User_Name'] . ")</a></li>";
                                }
                            }
                        }
                        echo "</ul></nav>";

                        // Display Create User button
                        echo "<a href='./Create.php" . (isset($_GET['uid']) ? "?ret=" . $_GET['uid'] : "") 
                                . "'><button class='w3-button w3-green'>Create New User</button></a>";
                    }
                    mysqli_close($conn);
                ?>
            </div>
        </div>
    </body>
</html>