<?php
	/**
     * Project Planner
     * Users/Change_Role.php
     * Promotes an employee to Manager or demotes a manager to Employee
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
	    echo "Unable to connect.  Error: " . mysqli_error($conn);
    }

    // Retrieve command (1 = promote user, 0 = demote user)
    $command = $_REQUEST['com'];
    $uid = mysqli_real_escape_string($conn, $_REQUEST['uid']);
    
    if ($command == 1)
    {
        $sql = "UPDATE Users SET User_Role = 1 WHERE User_ID = '$uid'";
        mysqli_query($conn, $sql);
    }
    else
    {
        $sql = "UPDATE Users SET User_Role = 0 WHERE User_ID = '$uid'";
        mysqli_query($conn, $sql);
    }
    mysqli_close($conn);
?>