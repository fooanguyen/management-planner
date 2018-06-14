<?php
	/**
     * Project Planner
     * Users/Delete.php
     * Deletes a user account and removes all mentions of the account
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
    
    // Do not proceed if no phase, project, and delkey are specified
    if (!isset($_GET))
    {
        header("Location: ../home.php");
    }

    // Attempt DB connection
    $conn = mysqli_connect($_SESSION["SERVER"], $_SESSION["DBUSER"], $_SESSION["DBPASS"], 
                            $_SESSION["DATABASE"]);
	if (!$conn)
	{
        // Output error details
		die('Unable to connect.  Error: ' . mysqli_error($conn));
    }
    
    // Get user ID and unique deletion key
    $uid = mysqli_real_escape_string($conn, $_GET['uid']);
    $delkey = $_GET['d'];

    // If delkey verified, proceed
    if (password_verify($uid . "delete" . $uid, $delkey))
    {

        // Retrieve and delete all user assignments with this user ID
        $sql = "SELECT * FROM User_Assignments WHERE User_ID_FK = '$uid'";
        if ($Result = mysqli_query($conn, $sql))
        {
            while ($assign = mysqli_fetch_array($Result))
            {
                $sql = "DELETE FROM User_Assignments WHERE Assignment_ID = " . $assign['Assignment_ID'];
                mysqli_query($conn, $sql);
            }
        }
        
        // Replace all mentions of this user with (User-Deleted) in all phase creator fields
        $sql = "SELECT * FROM Phases WHERE User_ID_FK = '$uid'";
        if ($Result = mysqli_query($conn, $sql))
        {
            while ($phase = mysqli_fetch_array($Result))
            {
                $sql = "UPDATE Phases SET User_ID_FK = 0 WHERE Phase_ID = " . $phase['Phase_ID'];
                mysqli_query($conn, $sql);
            }
        }
        
        // Replace all mentions of this user with (User-Deleted) in all task creator fields
        $sql = "SELECT * FROM Tasks WHERE User_ID_FK = '$uid'";
        if ($Result = mysqli_query($conn, $sql))
        {
            while ($task = mysqli_fetch_array($Result))
            {
                $sql = "UPDATE Tasks SET User_ID_FK = 0 WHERE Task_ID = " . $task['Task_ID'];
                mysqli_query($conn, $sql);
            }
        }

        // Delete the user account
        $sql = "SELECT * FROM Users WHERE User_ID = '$uid'";
        if ($Result = mysqli_query($conn, $sql))
        {
            $count = mysqli_num_rows($Result);
            if ($count == 1)
            {
                $sql = "DELETE FROM Users WHERE User_ID = '$uid'";
                mysqli_query($conn, $sql);
            }
        }
    }

    mysqli_close($conn);

    // If the account being deleted belonged to the user currently logged in, log out
    if ($_SESSION['CURRENT_USER']->GetUserID() == $uid)
    {
        $_SESSION['CURRENT_USER']->Logout();
    }
    header("Location: ./View.php");
?>