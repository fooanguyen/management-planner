<?php
	/**
     * Project Planner
     * Project/Phases/User_Search.php
     * Searches the database for users with names containing the search text
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
	if(!Session::UserLoggedIn())
	{
		header("Location: ../../login.php");
    }
    
    // Do not proceed if no search text is specified
    if(empty($_REQUEST))
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

    // Get search text (lowercase only)
    $search = strtolower($_REQUEST['u']);
    $ret = Array();

    // Get all users
    $sql = "SELECT * FROM Users";
    if($result = mysqli_query($conn, $sql))
    {
        while ($user = mysqli_fetch_array($result))
        {
            // Searching for $search string within "firstname lastname (username)"
            $searchText = strtolower($user['User_Firstname'] . " " . $user['User_Lastname'] 
                                        . " (" . $user['User_Name'] . ")");

            // If found (and user is not the placeholder), push name to search results
            if ($user["User_ID"] != 0 && strpos($searchText,$search) !== FALSE)
            {
                array_push($ret, $user['User_Firstname'] . " " . $user['User_Lastname'] 
                            . " (" . $user['User_Name'] . ")");
                array_push($ret, $user['User_ID']);
            }
        }
    }

    // If none found, push an error message
    if (!(count($ret) >= 1))
    {
        array_push($ret, "No results found!");
    }

    mysqli_close($conn);
    // Return search results in JavaScript format
    echo json_encode($ret);
?>