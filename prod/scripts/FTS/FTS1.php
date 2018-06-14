<?php
    /**
     * Project Planner
     * FTS/FTS1.php
     * Creates the Project Planner database with the specified DB credentials
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
    Session::Start();

    // Custom error handling of SQL warnings
    set_error_handler(function($errno, $errstr, $errfile, $errline)
    {
        if ($errno === E_WARNING)
        {
            // Clear $_POST
            $_POST = array();
            
            // Display error message and reload
            echo "<script type='text/JavaScript'>
                    alert('Could not establish a connection to this database.  Error details: " . $errstr . "');
                    window.location.href = './FTS1.php';
                    </script>";
            return true;
        }
        else
        {
            // fallback to default php error handler
            return false;
        }
    });

    // This page should be inaccessible if a user already exists
	if (Session::UserLoggedIn())
	{
		header("Location: ../home.php");
    }
    
    // Set up the database once the input fields are filled
    if (isset($_POST['Submit']) && !empty($_POST))
    {
        $server = $_POST['Server'];     // Database server URL
        $dbuser = $_POST['DBuser'];     // DB username
        $dbpass = $_POST['DBpass'];     // DB password
        $database = $_POST['Database']; // DB name
        
        // Attempt to connect to the database.
        // If failed, reload the page.
        $conn = mysqli_connect($server, $dbuser, $dbpass);
        if (!$conn)
        {
            die("Unable to connect.  Error: " . mysqli_error($conn));
        }
        else
        {
            // If connected, make sure the user has sufficient privileges
            $result = mysqli_query($conn, "SHOW GRANTS FOR CURRENT_USER");
            while ($privs = mysqli_fetch_array($result))
            {
                // User needs privileges ALL or SELECT, UPDATE, INSERT, DELETE, CREATE, ALTER, USE
                if (strpos($privs[0], "ALL") != FALSE || (strpos($privs[0], "SELECT") != FALSE 
                                                        && strpos($privs[0], "UPDATE") != FALSE 
                                                        && strpos($privs[0], "INSERT") != FALSE 
                                                        && strpos($privs[0], "DELETE") != FALSE 
                                                        && strpos($privs[0], "CREATE") != FALSE 
                                                        && strpos($privs[0], "ALTER") != FALSE))
                {    
                    // If successful, save DB credentials in dbconfig.ini
                    $config = fopen("../dbconfig.ini", "w");
                    fwrite($config, "\"SERVER\" = " . $server . PHP_EOL);
                    fwrite($config, "\"DBUSER\" = " . $dbuser . PHP_EOL);
                    fwrite($config, "\"DBPASS\" = " . $dbpass . PHP_EOL);
                    fwrite($config, "\"DATABASE\" = " . $database . PHP_EOL);
                    fclose($config);
                   
                    // Create database if not already existing
                    $database = mysqli_real_escape_string($conn, $database);
				    $sql = "CREATE DATABASE IF NOT EXISTS $database";
				    mysqli_query($conn, $sql);
					
				    $sql = "USE $database";
				    mysqli_query($conn, $sql);
                    
                    // Create Clients table
                    $sql = "CREATE TABLE IF NOT EXISTS Clients (
                        Client_ID int(100) NOT NULL,
                        Client_CompanyName varchar(100) NOT NULL,
                        Client_Firstname varchar(100) NOT NULL,
                        Client_Lastname varchar(100) NOT NULL,
                        Client_Industry varchar(100) NOT NULL,
                        Client_Email varchar(100) NOT NULL,
                        Client_Phone varchar(10) NOT NULL,
                        Client_Street varchar(100) NOT NULL,
                        Client_City varchar(100) NOT NULL,
                        Client_State varchar(2) NOT NULL,
                        Client_Zipcode int(5) UNSIGNED NOT NULL,
                        Client_Country varchar(100) NOT NULL
                        ) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
                    $result = mysqli_query($conn, $sql);
                    
                    $sql = "ALTER TABLE Clients
                        ADD PRIMARY KEY (Client_ID);";
                    mysqli_query($conn, $sql);

                    $sql = "ALTER TABLE Clients
                        MODIFY Client_ID int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;";
                    mysqli_query($conn, $sql);
                
                    // Create Phases table
                    $sql = "CREATE TABLE IF NOT EXISTS Phases (
                        Phase_ID int(100) NOT NULL,
                        User_ID_FK int(100) NOT NULL,
                        Project_ID_FK int(100) NOT NULL,
                        Phase_Name varchar(100) NOT NULL,
                        Phase_Description varchar(500) NOT NULL,
                        Phase_Status varchar(100) NOT NULL,
                        Phase_TotalHours int(100) NOT NULL
                        ) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
                    mysqli_query($conn, $sql);

                    $sql = "ALTER TABLE Phases
                        ADD PRIMARY KEY (Phase_ID),
                        ADD KEY Project_ID (Project_ID_FK),
                        ADD KEY User_ID (User_ID_FK);";
                    mysqli_query($conn, $sql);

                    $sql = "ALTER TABLE Phases
                        MODIFY Phase_ID int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;";
                    mysqli_query($conn, $sql);
                
                    // Create Projects table
                    $sql = "CREATE TABLE IF NOT EXISTS Projects (
                        Project_ID int(100) NOT NULL,
                        Client_ID_FK int(100) DEFAULT NULL,
                        Project_Name varchar(100) NOT NULL,
                        Project_Description varchar(2000) NOT NULL,
                        Project_Status varchar(100) NOT NULL,
                        Project_StartDate date NOT NULL,
                        Project_EstimatedBudget float NOT NULL,
                        Project_RemainedBudget float NOT NULL,
                        Project_TotalHours int(100) NOT NULL
                        ) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
                    mysqli_query($conn, $sql);

                    $sql = "ALTER TABLE Projects
                        ADD PRIMARY KEY (Project_ID);";
                    mysqli_query($conn, $sql);

                    $sql = "ALTER TABLE Projects
                        MODIFY Project_ID int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;";
                    mysqli_query($conn, $sql);
                
                    // Create Tasks table
                    $sql = "CREATE TABLE IF NOT EXISTS Tasks (
                        Task_ID int(100) NOT NULL,
                        Project_ID_FK int(100) NOT NULL,
                        User_ID_FK int(100) NOT NULL,
                        Phase_ID_FK int(100) NOT NULL,
                        Task_Name varchar(100) NOT NULL,
                        Task_Description varchar(500) NOT NULL,
                        Task_EstimatedHours int(100) NOT NULL,
                        Task_EstimatedCost float NOT NULL,
                        Task_WorkedHours int(100) NOT NULL
                        ) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
                    mysqli_query($conn, $sql);

                    $sql = "ALTER TABLE Tasks
                        ADD PRIMARY KEY (Task_ID),
                        ADD KEY Phase_ID (Phase_ID_FK),
                        ADD KEY Project_ID (Project_ID_FK),
                        ADD KEY User_ID (User_ID_FK);";
                    mysqli_query($conn, $sql);

                    $sql = "ALTER TABLE Tasks
                        MODIFY Task_ID int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;";
                    mysqli_query($conn, $sql);
                
                    // Create Users table
                    $sql = "CREATE TABLE IF NOT EXISTS Users (
                        User_ID int(100) NOT NULL,
                        User_Name varchar(100) NOT NULL,
                        User_Password varchar(255) NOT NULL,
                        User_Firstname varchar(100) NOT NULL,
                        User_Lastname varchar(100) NOT NULL,
                        User_Role int(1) UNSIGNED NOT NULL,
                        User_Phone varchar(10) NOT NULL,
                        User_Email varchar(100) NOT NULL,
                        User_Street varchar(100) NOT NULL,
                        User_City varchar(100) NOT NULL,
                        User_State varchar(2) NOT NULL,
                        User_Zipcode int(5) UNSIGNED NOT NULL,
                        User_Birthdate date NOT NULL
                        ) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
                    mysqli_query($conn, $sql);

                    $sql = "ALTER TABLE Users
                        ADD PRIMARY KEY (User_ID),
                        ADD UNIQUE KEY User_Name (User_Name);";
                    mysqli_query($conn, $sql);

                    $sql = "ALTER TABLE Users
                        MODIFY User_ID int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;";
                    mysqli_query($conn, $sql);
                
                    // Create User_Assignments table
                    $sql = "CREATE TABLE IF NOT EXISTS User_Assignments (
                        Assignment_ID int(100) NOT NULL,
                        Project_ID_FK int(100) NOT NULL,
                        Phase_ID_FK int(100) NOT NULL,
                        User_ID_FK int(100) NOT NULL
                        ) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
                    mysqli_query($conn, $sql);

                    $sql = "ALTER TABLE User_Assignments
                        ADD PRIMARY KEY (Assignment_ID),
                        ADD KEY Phase_ID (Phase_ID_FK),
                        ADD KEY User_ID (User_ID_FK),
                        ADD KEY Project_ID (Project_ID_FK);";
                    mysqli_query($conn, $sql);
                
                    $sql = "ALTER TABLE User_Assignments
                        MODIFY Assignment_ID int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;";
                    mysqli_query($conn, $sql);
                

                    // Add Phases foreign keys
                    $sql = "ALTER TABLE Phases
                        ADD CONSTRAINT Project_ID FOREIGN KEY (Project_ID_FK) REFERENCES Projects (Project_ID),
                        ADD CONSTRAINT User_ID FOREIGN KEY (User_ID_FK) REFERENCES Users (User_ID);";
                    mysqli_query($conn, $sql);
                
                    // Add Tasks foreign Keys
                    $sql = "ALTER TABLE Tasks
                        ADD CONSTRAINT Phase_ID FOREIGN KEY (Phase_ID_FK) REFERENCES Phases (Phase_ID),
                        ADD CONSTRAINT Project_ID FOREIGN KEY (Project_ID_FK) REFERENCES Projects (Project_ID),
                        ADD CONSTRAINT User_ID FOREIGN KEY (User_ID_FK) REFERENCES Users (User_ID);";
                    mysqli_query($conn, $sql);
                
                    // Add User_Assignments foreign keys
                    $sql = "ALTER TABLE User_Assignments
                        ADD CONSTRAINT Phase_ID FOREIGN KEY (Phase_ID_FK) REFERENCES Phases (Phase_ID),
                        ADD CONSTRAINT Project_ID FOREIGN KEY (Project_ID_FK) REFERENCES Projects (Project_ID),
                        ADD CONSTRAINT User_ID FOREIGN KEY (User_ID_FK) REFERENCES Users (User_ID);";
                    mysqli_query($conn, $sql);
                
                    // Insert default User account
                    $sql = "INSERT INTO Users (User_Name, User_Password, User_Role) VALUES ('User-Deleted', 'No-Access', 0)";
                    mysqli_query($conn, $sql);
                    $sql = "UPDATE Users SET User_ID = 0 WHERE User_ID = 1";
                    mysqli_query($conn, $sql);
                    $sql = "ALTER TABLE Users
                        MODIFY User_ID int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;";
                    mysqli_query($conn, $sql);

                    // Insert default Client info
                    $sql = "INSERT INTO Clients(Client_CompanyName, Client_Firstname, Client_Lastname, 
                        Client_Industry, Client_Email, Client_Phone, 
                        Client_Street, Client_City, Client_State, 
                        Client_Zipcode, Client_Country) 
                        VALUES 
                        ('N/A', 'N/A', 'N/A', 'N/A', 'N/A', 0, 'N/A', 'N/A', 'N/A', 0, 'N/A')";
                    mysqli_query($conn, $sql);
                    $sql = "UPDATE Clients SET Client_ID = -1 WHERE Client_ID = 1";
                    mysqli_query($conn, $sql);
                    $sql = "ALTER TABLE Clients
                        MODIFY Client_ID int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;";
                    mysqli_query($conn, $sql);
                         
                    // Commit changes to DB
                    $sql = "COMMIT;";
                    mysqli_query($conn, $sql);

                    // Continue to next step of setup
                    mysqli_close($conn);
                    header("Location: ./FTS2.php");
                }
            }
            // If privilege check failed, output necessary privileges
            echo "<script type='text/JavaScript'>alert('The specified user account does not have required permissions.  
                    The account requires at least SELECT, UPDATE, INSERT, DELETE, CREATE, ALTER permissions.');</script>";
        }
    }
?>


<!DOCTYPE html>
<html>
    <head>
        <meta charset=utf-8 />
        <link href="../style.css" rel="stylesheet">
    </head>
    <body>
		<div class="w3-top w3-card w3-white" style="height:10%">
			<div class="w3-bar w3-padding">
				<a class="w3-bar-item"><h1>Project Planner</h1></a>
			</div>
		</div>
        <div class="w3-container" style="margin-top:10%">
			<div class="w3-container w3-display-middle" style="width:75%">
                <h1>First-time Setup: Establish Database Connection</h1>
				<div class="w3-border w3-padding">
                    <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" autocomplete="off">
                        <p>Database Server Host: <input type="text" name="Server" required /></p>
                        <p>Database Username: <input type="text" name="DBuser" required /></p>
                        <p>Database Password: <input type="password" name="DBpass" required /></p>
                        <p>Connect to Database Name: <input type="text" name="Database" required /></p>

                        <button class="w3-button w3-green" type="Submit" name="Submit">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>