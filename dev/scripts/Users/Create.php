<?php
	/**
     * Project Planner
     * Users/Create.php
     * Creates a new user
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
    
    // Attempt DB connection
    $conn = mysqli_connect($_SESSION["SERVER"], $_SESSION["DBUSER"], $_SESSION["DBPASS"], 
                            $_SESSION["DATABASE"]);
	if (!$conn)
	{
        // Output error details
		die('Unable to connect.  Error: ' . mysqli_error($conn));
    }

    // Create the user only once the input fields are filled
    if (isset($_POST['UserSubmit']) && !empty($_POST))
    {
        // Prepare user details for SQL query
        $firstName = mysqli_real_escape_string($conn, $_POST['Firstname']);
        $lastName = mysqli_real_escape_string($conn, $_POST['Lastname']);
        $username = mysqli_real_escape_string($conn, $_POST['Username']);
        $password = password_hash(mysqli_real_escape_string($conn, $_POST['Password']), PASSWORD_BCRYPT);
        $birthDate = $_POST['Birthdate'];
        $street = mysqli_real_escape_string($conn, $_POST['Street']);
        $city = mysqli_real_escape_string($conn, $_POST['City']);
        $state = mysqli_real_escape_string($conn, $_POST['State']);
        $zipCode = $_POST['Zipcode'];
        $email = mysqli_real_escape_string($conn, $_POST['Email']);
        $phone = mysqli_real_escape_string($conn, $_POST['Phone']);

        $sql = "INSERT INTO Users (User_Firstname, User_Lastname, User_Name, User_Password, 
                User_Birthdate, User_Street, User_City, User_State, User_Zipcode, User_Email, User_Phone)
                VALUES ('$firstName', '$lastName', '$username', '$password', '$birthDate', 
                '$street', '$city', '$state', '$zipCode', '$email', '$phone')";
        mysqli_query($conn, $sql);
        
        // if a user is already logged in, return to the users view page
        if (Session::UserLoggedIn())
        {
            mysqli_close($conn);
            header("Location: ./View.php" . (isset($_GET['ret']) ? "?uid=" . $_GET['ret'] : ""));
        }
        mysqli_close($conn);
        header("Location: ../login.php");
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset=utf-8 />
        <link href ="../style.css" rel="stylesheet">
    </head>
    <body>
		<div class="w3-top w3-card w3-white" style="height:10%">
			<div class="w3-bar w3-padding">
				<a class="w3-bar-item" href="../home.php"><h1>Project Planner</h1></a>
				<div class="w3-right">
                    <?php
                        /**
                         * Displays User information if one is already logged in
                         * Otherwise, masks user details
                         */
                        if (Session::UserLoggedIn())
                        {
                            echo "<a class='w3-bar-item' href='./View.php'>Logged in as " 
                                    . $_SESSION['CURRENT_USER']->GetFirstName() . " " 
                                    . $_SESSION['CURRENT_USER']->GetLastName() . " (" 
                                    . $_SESSION['CURRENT_USER']->GetUsername() . ")</a>";
                            echo "<a href='../logout.php'><button class='w3-bar-item w3-button w3-red'>"
                                    . "Sign Out</button></a>";
                        }
                    ?>
				</div>
			</div>
		</div>
        <div class="w3-container" style="margin-top:10%">
			<div class="w3-container w3-display-middle" style="width:60%">
                <a href="./View.php<?php echo (isset($_GET['ret']) ? "?uid=" . $_GET['ret'] : ""); ?>"><button class="w3-button w3-red" type="cancel" name="cancel">Cancel</button></a>
                <div class="w3-border w3-padding">
                    <h2>Create User Account</h2>
                    <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'] . (isset($_GET['ret']) ? "?ret=" . $_GET['ret'] : "")); ?>" autocomplete="off">
                        <p>User: <input type='text' name='Firstname' placeholder='First Name' required />   <input type='text' name='Lastname' placeholder='Last Name' required /></p>
                        <p>Username: <input type='text' name='Username' placeholder='Username' required /></p>
                        <?php 
                            /**
                             * Displays a message if a user is already logged in
                             */
                            if (Session::UserLoggedIn())
                            {
                                echo "<p>If this account is for a manager, please grant manager functions "
                                        . "to this account after it has been created.</p>";
                            }
                            else
                            {
                                echo "<p>If you are a manager, please request to receive manager functions "
                                        . "once your account is created.</p>";
                            }
                            mysqli_close($conn);
                        ?></br>
                    
                        <p>Password: <input type='password' name='Password' placeholder='Input password...' required /></p></br>

                        <p>Date of Birth: <input type='date' name='Birthdate' required /></p>
                        <p>Address: <input type='text' name='Street' placeholder='Street' required />, <input type='text' name='City' placeholder='City' required />, <input type='text' maxlength = "2" name='State' placeholder='State' required /> <input type='number' maxlength = "5" name='Zipcode' placeholder='Zipcode' required /></p>
                        <p>Email Address: <input type='email' name='Email' placeholder='Email' required /></p>
                        <p>Phone Number: <input type='tel' maxlength="10" name='Phone' placeholder='Phone Number' required /></p>

                        <button class="w3-button w3-green" type="submit" name="UserSubmit">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>