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
            div {
				text-align: center;
				background-color: rgb(211, 234, 253);
			}

			input {
        		width: 90%;
        		padding: 12px 20px;
        		margin: 8px 0;
        		box-sizing: border-box;
				text-align: center;
            }
        </style>
    </head>
    <body>
        <p><a href="mailroom_admin.php">Goto admin page.</a></p>
        <!-- %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%% -->
        <!-- Ignore - this was part of double submit -->
        <!-- <form name='get-packages' method='post'>
            <p>Enter Student MSC&#x23;: 
            <input type='text' name='msc-num'></p>
            <p><input type="submit" value="Show Packages"/><span id="load-outcome"></span></p><p id="load-outcome-text"></p>
        </form> -->
        <!-- %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%% -->
        <div id='form'>
            <form name='choose-package' method='post'>
                <p>Enter Student MSC&#x23;: 
                    <input type='text' name='msc-num'>
                <!-- <p>Choose package(s)</p> -->
                <p><input type='submit' value='Check-in'/><span id='load-outcome'></span></p><p id='load-outcome-text'></p>
            </form>
        </div>

        <?php
            if (isset($_POST['msc-num']) and $_POST['msc-num'] != '') {
                $mscNum = $_POST['msc-num'];
                $timein = date("H:i:s");

                // %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
                // This works, but not using it
                // $pkgQuery = "SELECT * FROM PACKAGES AS P WHERE P.mscNum =".$mscNum;
                // $pkgList = $db->query($pkgQuery);
                // // echo "<option>".gettype($pkgList)."</option>";

                // foreach ($pkgList as $pkg) {
                //     echo "<input type='checkbox' name='pkg-select' id=".$pkg['location']." value=".$pkg['location'].">
                //             <label for=".$pkg['location'].">".$pkg['location']."</label><br/>";
                // }
                // %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

                $verifyQuery = "SELECT * FROM PACKAGES WHERE mscNum = :mscNum";
                $verify = $db->prepare($verifyQuery);
                // $verify->execeute(['mscNum' => $mscNum]);
                $verify->bindValue('mscNum', $mscNum, PDO::PARAM_INT);
                $verify->execute();
                $verified = $verify->fetch();
                if ($verified) {
                    // $doc->getElementByID("load-outcome-text")->innerHTML = "You're checked in!";
                    echo '<script type="text/javascript">document.getElementById("load-outcome").innerHTML = "\tYou&apos;re checked in!";</script>';
                    // echo '<script type="text/javascript">document.write("Hello");</script>';
                } else {
                    // echo "<p id='load-outcome'>Please try again!</p>";
                    echo '<script type="text/javascript">document.getElementById("load-outcome").innerHTML = "\tSomething went wrong, try again!";</script>';
                }

                $checkIn = "UPDATE PACKAGES SET checkedin = 'yes', time_in = :timein WHERE mscNum = :mscNum";
                $updateDB = $db->prepare($checkIn);
                $updateDB->bindValue('timein', $timein, PDO::PARAM_STR);
                $updateDB->bindValue('mscNum', $mscNum, PDO::PARAM_INT);
                $updateDB->execute();
                // $updateDB->execute([
                //     'timein' => $timein,
                //     'mscNum' => $mscNum
                // ]);
                
                
                $waitTimeQuery = "SELECT COUNT(*) FROM PACKAGES AS P WHERE P.checkedin = 'yes'";
                // $waitTimeQuery = "SELECT COUNT(*) FROM PACKAGES";
                $wtPDOQ = $db->query($waitTimeQuery);
                $waitTime = $wtPDOQ->fetch(PDO::FETCH_ASSOC);

                // echo $waitTime[0];
                foreach ($waitTime as $in) {
                    // echo $doc->getElementByID("load-outcome-text")->innerHTML += "\nEstimated wait time: ".($in * 30)." seconds </p>";
                    echo '<script  type="text/javascript">document.getElementById("load-outcome-text").innerHTML = "Estimated wait time: '.($in * 30).' seconds";</script>';
                }
                
        

                // if ($outcome) {
                //     echo "worked";
                //     // $doc->getElementByID("load-outcome-text")->innerHTML = "You're checked in!";
                // } else {
                //     echo "huh";
                //     // $doc->getElementByID("load-outcome-text")->innerHTML = "Please try again!";
                // }
                // $updateDB->bindValue('mscNum', $mscNum);
                // $updateDB->bindValue('timein', $timein);
                // $updateDB->execute();
            }
        ?>
            
            <!-- %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%% -->
            <!-- This section does NOT work -->
            <!-- <p><input type='submit' value='Retrieve'/><span id='load-outcome'></span></p><p id='load-outcome-text'></p>
        </form>
        <?php
            // echo isset($_POST['msc-num']) ? 'checked in' : 'didnt update';
            $packages = $_POST['pkg-select'];
            if(empty($pakages)) {
                echo $mscNum;
                echo "Please select a package and hit 'Retrieve'!";
            } else {
                $mscNum = $_POST['msc-num'];
                echo count($packages);
                // for($packages as $pkg) {
                //     echo $pkg['location'];
                // }  

                // $doc->getElementByID("load-outcome-text")->innerHTML = "worked";
                // echo "worked";
                // $checkIn = "UPDATE PACKAGES SET checkedin = 'yes' WHERE mscNum = :mscNum";
                // $updateDB = $db->prepare($checkIn);
                // // $updateDB->execute([
                // //     'mscNum' => $mscNum
                // // ]);
                // $updateDB->bindValue('mscNum', $mscNum);
                // $updateDB->execute();
            }
            // if (isset($_POST['msc-num']) and $_POST['msc-num'] != '') {
                
            // }
        ?> -->
        <!-- %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%% -->

        <p><a href="mailroom_admin.php">Goto admin page.</a></p>
    </body>
</html>