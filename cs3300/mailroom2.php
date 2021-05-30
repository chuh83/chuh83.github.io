<?php
    $db = new PDO('sqlite:mailroom.db') or die ("cannot open database");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    date_default_timezone_set("America/Chicago");
?>

<!Doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1"> <!-- This enables Bootstrap to be optimized for mobile devices -->    
        
        <title>SLU Mailroom</title>
        <h1>Welcome to the Mailroom!</h1>

        <style>

			body{
				background-color: #003DA5;
				color: white;
			}
			
			h1{
				text-align: center;
				font-family: Verdana, Helvetica, sans-serif;
				fint-size: large;
			}
			
			img{
				width:100%;
			}
			
			#image{
				width: 25%;
				margin: auto;
			}
			
			#form{
				margin: auto;
				width: 30%;
				font-family: Verdana, Helvetica, sans-serif;
				font-color: white;
			}
			
			input[type=text]{
				border-radius: 5px;
				height: 30px;
				font-size: medium;
			}
			
			input[type=submit]{
            	background-color: grey;
            	color: white;
            	height: 30px;
            	border-radius: 5px;
            }
            
            input[type=submit]:hover {
				background-color: #003DA5;
			}
			
        </style>
    </head>
    <body>
        <!-- <p><a href="mailroom_admin2.php">Goto admin page.</a></p> -->
        <div id='form'>
            <form name='choose-package' method='post'>
                <p>Enter Student MSC&#x23;: 
                    <input type='text' name='msc-num'></p>
                <p><input type='submit' name="check-in" value='Check-in'/><span id='load-outcome'></span></p><p id='load-outcome-text'></p>
                
                <p>For scheduling a pick up time, enter your MSC&#x23; above and enter a time in 24 hour format:
                    <input type='text' placeholder="14:00" name='scheduled-time'></p>
                <p><input type='submit' name="check-in-later" value='Schedule Pick Up'/><span id='load-outcome2'></span></p><p id='load-outcome2-text'></p>
            </form>
        </div>

		<div id="image">
        	<img src="https://www.slu.edu/img/social/slu-social.png">
        </div>
        
        <div id='login'>
            <form name='login'>
                <p>Login to admin page: <input type='password' name='password'></p>
            </form>
            <p><input type='button' value='Log in' onclick='check()'></p><p id='load-outcome3-text'></p>
        </div>
        
        <script>
            function check() {
                var password = document.forms['login'].elements['password'].value;
                console.log(password);
                
                if (password == "admin") {
                    var link = document.createElement("a");
                    link.setAttribute("href", "mailroom_admin2.php");
                    link.click();
                } else {
                    document.getElementById('load-outcome3-text').innerHTML = "Wrong password, please try again!";
                }
            }
        </script>
        <?php
            $timein = date("H:i:s");

            // regualr check in time
            if (isset($_POST['check-in'])) {
                $timein = date("H:i:s");

            // schedule ahead of time
            } else if (isset($_POST['check-in-later']) and $_POST['scheduled-time'] != '') {
                if (isset($_POST['scheduled-time'])) {
                    $timein = $_POST['scheduled-time'];
                } else {
                    echo '<script type="text/javascript">document.getElementById("load-outcome2-text").innerHTML = "Please enter a valid time!";</script>';
                }
            }
            if (isset($_POST['msc-num']) and $_POST['msc-num'] != '') {
                $mscNum = $_POST['msc-num'];

                // echo($timein);

                $verifyQuery = "SELECT * FROM PACKAGES WHERE mscNum = :mscNum";
                $verify = $db->prepare($verifyQuery);
                // $verify->execeute(['mscNum' => $mscNum]);
                $verify->bindValue('mscNum', $mscNum, PDO::PARAM_INT);
                $verify->execute();
                $verified = $verify->fetch();
                if ($verified) {
                    // $doc->getElementByID("load-outcome-text")->innerHTML = "You're checked in!";
                    echo '<script type="text/javascript">document.getElementById("load-outcome").innerHTML = "\tChecking you in...";</script>';
                    // echo '<script type="text/javascript">document.write("Hello");</script>';

                    $checkIn = "UPDATE PACKAGES SET checkedin = 'yes', time_in = :timein WHERE mscNum = :mscNum";
                    $updateDB = $db->prepare($checkIn);
                    $updateDB->bindValue('timein', $timein, PDO::PARAM_STR);
                    $updateDB->bindValue('mscNum', $mscNum, PDO::PARAM_INT);
                    $outcome = $updateDB->execute();
                    // $updateDB->execute([
                    //     'timein' => $timein,
                    //     'mscNum' => $mscNum
                    // ]);

                    if ($outcome) {
                        $itemCountQuery = "SELECT COUNT(*) FROM PACKAGES AS P WHERE P.mscNum =".$mscNum;
                        $iCPDOQ = $db->query($itemCountQuery);
                        $itemCount = $iCPDOQ->fetch(PDO::FETCH_ASSOC);

                        // echo $waitTime[0];
                        foreach ($itemCount as $item) {
                            // echo $doc->getElementByID("load-outcome-text")->innerHTML += "\nEstimated wait time: ".($in * 30)." seconds </p>";
                            echo '<script  type="text/javascript">document.getElementById("load-outcome-text").innerHTML = "\tChecked in for '.($item).' package(s)!";</script>';
                            echo '<script type="text/javascript">document.getElementById("load-outcome").innerHTML = "";</script>';
                        }
                    }
                    
                    
                    $waitTimeQuery = "SELECT COUNT(*) FROM PACKAGES AS P WHERE P.checkedin = 'yes'";
                    // $waitTimeQuery = "SELECT COUNT(*) FROM PACKAGES";
                    $wtPDOQ = $db->query($waitTimeQuery);
                    $waitTime = $wtPDOQ->fetch(PDO::FETCH_ASSOC);

                    // echo $waitTime[0];
                    foreach ($waitTime as $in) {
                        // echo $doc->getElementByID("load-outcome-text")->innerHTML += "\nEstimated wait time: ".($in * 30)." seconds </p>";
                        echo '<script  type="text/javascript">document.getElementById("load-outcome-text").innerHTML += "\nEstimated wait time: '.($in * 30).' seconds";</script>';
                    }
                } else {
                    // echo "<p id='load-outcome'>Please try again!</p>";
                    echo '<script type="text/javascript">document.getElementById("load-outcome-text").innerHTML = "\nThis MSC&#x23; is not valid, try again!";</script>';
                }
            }
        ?>

        <!-- <p><a href="mailroom_admin2.php">Goto admin page.</a></p> -->
    </body>
</html>
