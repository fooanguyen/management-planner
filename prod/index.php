<?php
	/**
     * Project Planner
     * index.php
     * Checks DB credentials and redirects to either First-time setup
     * or login.php, as necessary
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
    $config = fopen("./scripts/dbconfig.ini", "r");
    $server = trim(explode(" ", fgets($config))[2]);
    $dbuser = trim(explode(" ", fgets($config))[2]);
    $dbpass = trim(explode(" ", fgets($config))[2]);
    $database = trim(explode(" ", fgets($config))[2]);
    fclose($config);

    // Attempt DB connection
    $conn = mysqli_connect($server, $dbuser, $dbpass, $database);
    if(!$conn)
    {
        // If failed, go to First-time Setup
        header("Location: scripts/FTS/FTS1.php");
    }
    else
    {
        // If succeeded, check if a user exists
        $sql = "SELECT * FROM Users WHERE User_Role = 1";
        if($result = mysqli_query($conn, $sql))
        {
            if(mysqli_num_rows($result) >= 1)
            {
                // If a user exists, go to login
                header("Location: scripts/login.php");
            }
            else
            {
                // If failed, go to First-time setup, step 2
                header("Location: scripts/FTS/FTS2.php");
            }
        } 
    }
?>
