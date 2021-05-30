<?php
    $db = new PDO('sqlite:mailroom.db') or die ("cannot open database");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>

<!Doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1"> <!-- This enables Bootstrap to be optimized for mobile devices -->    
        <!-- <script src="smtp.js"></script>
		<script src="SendEmail.js"></script>
		<script src="SendReminder.js"></script> -->
        <script src="mailroom_admin_js.js"></script>
        <title>Mailroom Admin</title>
        <h1 id="header">MAILROOM ADMINISTRATOR PAGE</h1>

        <style>
        	.page-body{
				background-color: white;
				height: 100%;
        	}
        	
        	#header{
        		font-family: Verdana, sans-serif;
        		padding-left: 10px;
        	}
        	
			.grid-container{
				display: grid;
				grid-template-columns: auto auto;
				grid-template-rows: 30vh, 30vh, 30vh;
				grid-gap: 10px;
				padding: 10px;
				height: 80vh;
			}
			
			.grid-container > div {
				background-color: #FCFCFC;
				text-align: center;
				font-size: 25px;
			}
			
			.table{
				font-family: Verdana, Helvetica, sans-serif;
				font-size: 100%;
				width: 100%;
				max-height: 100%;
			}
			
			.table tr:nth-child(even){background-color: #f2f2f2;}
			.table tr:hover {background-color: #ddd;}
			
			table, th, td {
				width: 25%;
			}
			
			table, th {
				position: sticky;
				top: 0;
			}
			
			.table th {
			  background-color: #003DA5;
			  color: white;
			  font-size: 110%;
			}
			
			.item1{
				display: grid;
				width: 100%;
				height: 80vh;
				grid-column-start: 1;
				grid-row-start: 1;
				grid-row-end: 20;
				border: 1px solid black;
				overflow-y: scroll;
			}
			
			.item2{
				width: 100%;
				grid-row-start: 1;
				grid-row-end: 2;
				grid-column-start: 2;
				border: 1px solid black;
				font-family: Verdana, Helvetica, sans-serif;
			}
			
			.item3{
				display: grid;
				width: 100%;
				height: 53vh;
				grid-column-start: 2;
				grid-row-start: 2;
				grid-row-end: 20;
				border: 1px solid black;
                overflow-y: scroll;
			}
			
			#subitem1{
				width: 100%;
				max-height: 97%;
				grid-row-start: 1;
				grid-row-end: 2;
			}
			
			#subitem2{
				width: 100%;
				grid-row-start: 1;
				grid-row-end: 8;
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
    <body class="page-body">
    <!-- %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%% -->
    <!-- %%%%%%%%%%%%%%%%%%%%%%%%% putting PHP here so page updates %%%%%%%%%%%%%%%%%%%%%%%%% -->
    <!-- %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%% -->
        <?php                     
            if (isset($_POST['checkout'])) {
                // update action
                if (isset($_POST['formAction'])) {
                    $selected = $_POST['formAction'];
                    // echo "<script>alert('hi')</script>";
                    $n = count($selected);
                    // echo $selected[($n-1)];
                    // foreach ($selected as $mscNum) {
                    for ($i = 0; $i < $n; $i++) {
                        // echo $i.": ".$selected[$i]."\n";
                        $checkOut = "UPDATE PACKAGES SET checkedin = 'no', time_in = NULL WHERE mscNum = :mscNum";
                        $updateDB = $db->prepare($checkOut);
                        $updateDB->bindValue('mscNum', $selected[$i], PDO::PARAM_INT);
                        $updateDB->execute();
                    }

                    // echo "<script>location.reload(true);</script>";
                } else {
                    echo "Nothing was selected!";
                }

            } else if (isset($_POST['remove'])) {
            // } else {
                if (isset($_POST['formAction'])) {
                    $selected = $_POST['formAction'];
                    $n = count($selected);
                    // foreach ($selected as $mscNum) {
                    for ($i = 0; $i < $n; $i++) {
                        // echo $selected[$i];
                        $remove = "DELETE FROM PACKAGES WHERE mscNum = :mscNum";
                        $updateDB = $db->prepare($remove);
                        $updateDB->bindValue('mscNum', $selected[$i], PDO::PARAM_INT);
                        $outcome = $updateDB->execute();
                    }
                } else {
                    echo "Nothing was selected!";
                }
            }
            else if (isset($_POST['email'])) {
            // } else {
                if (isset($_POST['formAction'])) {
                    $selected = $_POST['formAction'];
                    $n = count($selected);
                    // foreach ($selected as $mscNum) {
                    for ($i = 0; $i < $n; $i++) {
                        $emailsQuery = "SELECT * FROM STUDENTS WHERE mscNum = :mscNum AND email_address != ''";
						//$emailsQuery = "SELECT * FROM STUDENTS WHERE mscNum = 78";
						$emailsList = $db->prepare($emailsQuery);
                        $emailsList->bindValue('mscNum', $selected[$i], PDO::PARAM_INT);
                        $emailsList->execute();
						//echo $emailsList->debugDumpParams();
						//echo $emailsQuery."\n";
						//echo $emailsQuery1."\n";
						//echo $emailsList->fetch();
						$resultFound = 0;
						foreach ($emailsList as $emailEntry) {
							$resultFound = 1;
							echo "<p>Email sent to ".$emailEntry['email_address']." for MSC&#x23; ".$emailEntry['mscNum']."</p>";
							echo '<script type="text/javascript">',"sendReminder(\"".$emailEntry['name']."\", \"".$emailEntry['email_address']."\");",'</script>';
						}
						if($resultFound == 0){
							echo "<p>No Email found for MSC number ".$selected[$i]."</p>";
						}
                    }
                } else {
                    echo "Nothing was selected!";
                }
            }

        ?>
        <!-- %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%% -->
        <!-- %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%% -->
        <!-- %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%% -->
        <!-- <p><a href="mailroom2.php">Goto student page.</a></p> -->
        <div class="grid-container">
    		<div class="item1">
                <form name="checkedin" method="post">
                	<div class="subitem1">
                        <table id="checkedinT" class="table">
                            <tr><th>MSC Number</th><th>Location</th><th>Time In</th><th>Select</th></tr>
                            <?php
                                $checkedInQuery = "SELECT * FROM PACKAGES AS P WHERE P.checkedin = 'yes' ORDER BY time_in";
                                $inList = $db->query($checkedInQuery);
                                // echo "<option>".gettype($pkgList)."</option>";
    
                                foreach ($inList as $in) {
                                    echo "<tr>
                                            <td>".$in['mscNum']."</td>
                                            <td>".$in['location']."</td>
                                            <td>".$in['time_in']."</td>
                                            <td><input type='checkbox' name='formAction[]' value=".$in['mscNum']."</td>
                                        </tr>";
                                }
                            ?>
                        </table>
                    </div>
                    
                    <div class="actionbutton">
                        <input type="submit" name="checkout"  value="Not Delivered">
                        <input type="submit" name="remove" value="Delivered">
                    </div>
                </form>
    		</div>
	    	<div class="item2">
                <form name='insert-package' method='post'>
                    <p>Enter Student MSC&#x23;: 
                    <input type='text' name='msc-num'></p>
                    <p>Package Location: 
                    <input type='text' name='location'></p>
                    <p><input type="submit" value="Add"/><span id="load-outcome"></span></p><p id="load-outcome-text"></p>
                </form>
                <?php
                    if (isset($_POST['msc-num']) and $_POST['msc-num'] != '' and isset($_POST['location']) and $_POST['location'] != '') {
                        $mscNum = $_POST['msc-num'];
                        $location = $_POST['location'];

                        // validate integer
                        $mscNum = filter_var($mscNum, FILTER_VALIDATE_INT);

                        // if not int, dont do anything
                        if ($mscNum === false) {
                            echo '<script type="text/javascript">document.getElementById("load-outcome-text").innerHTML = "Please only use numbers when entering MSC&#x23;!"</script>';
                        } else {
                            $populateDB = "INSERT INTO PACKAGES (mscNum, location, checkedin, time_in) values (:mscNum, :location, 'no', null)";
                            $updateDB = $db->prepare($populateDB);
                            $outcome = $updateDB->execute([
                                'mscNum' => $mscNum,
                                'location' => $location
                            ]);

                            // if query executed
                            if ($outcome) {
                                echo '<script  type="text/javascript">document.getElementById("load-outcome-text").innerHTML = "New package has been added!"</script>';
                                $emailsQuery = "SELECT * FROM STUDENTS WHERE mscNum = ".$mscNum." AND email_address != ''";
    							//$emailsQuery = "SELECT * FROM STUDENTS WHERE mscNum = 78";
    							//echo $emailsQuery."\n";
    							//echo $emailsQuery1."\n";
    							$emailsList = $db->query($emailsQuery);
    							//echo $emailsList->fetch();
    							$resultFound = 0;
    							foreach ($emailsList as $emailEntry) {
    								$resultFound = 1;
    								echo "Email sent to ".$emailEntry['email_address']." for MSC&#x23; ".$emailEntry['mscNum']."";
    								echo '<script type="text/javascript">',
    								 "sendEmail(\"".$emailEntry['name']."\", \"".$emailEntry['email_address']."\", \"".$location."\");",
    								 '</script>';
    							}
    							if($resultFound == 0){
    								echo "No Email found for MSC number ".$mscNum;
    							}
                            } else {
                                echo '<script  type="text/javascript">document.getElementById("load-outcome-text").innerHTML = "Please try again!"</script>';
                            }
                        }
                    }
                ?>
	    	</div>
	    	<div class="item3">
                <form method="post">
                	<div class="subitem2">
                        <table id="pkgTable" class="table">
                            <tr><th>MSC</th><th>Location</th><th>Select</th></tr>
                            <?php
                                $pkgQuery = "SELECT * FROM PACKAGES AS P WHERE P.checkedin = 'no' ORDER BY mscNum";
                                $pkgList = $db->query($pkgQuery);
    
                                foreach ($pkgList as $pkg) {
                                    echo "<tr>
                                            <td>".$pkg['mscNum']."</td>
                                            <td>".$pkg['location']."</td>
                                            <td><input type='checkbox' name='formAction[]' value=".$pkg['mscNum']."</td>
                                        </tr>";
                                }
                            ?>
                        </table>
                    </div>
                    <div class="actionbuttons">
                        <input type="submit" name="remove"  value="Remove">
                        <input type="submit" name="email" value="Email Reminder">
                    </div>
                </form>
	    	</div>
		</div>

        <p><a href="mailroom2.php">Goto student page.</a></p>

    </body>
</html>
