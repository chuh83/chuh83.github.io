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
        
        <title>Mailroom Admin</title>
        <h1>Mailroom Administrator Page</h1>

        <style>
			.grid-container{
				display: grid;
				grid-template-columns: auto auto auto;
				grid-gap: 10px;
				background-color: #2196F3;
				padding: 10px;
			}
			
			.grid-container > div {
				background-color: rgba(255, 255, 255, 0.8);
				text-align: center;
				padding: 20px 0;
				font-size: 30px;
			}
			
			.table{
				width: 100%;
                overflow-y: scroll;
				heightL: auto;
			}
			
			.item1{
				width: 100%;
				grid-column-start: 1;
				grid-row-start: 1;
				grid-row-end: 3;
			}

            #actionbuttons{
				align:
			}
			
			.item2{
				grid-row-start: 1;
				grid-column-start: 2;
			}
			
			.item3{
				grid-column-start: 2;
			}
	    </style>
    </head>
    <body>
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
        ?>
        <!-- %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%% -->
        <!-- %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%% -->
        <!-- %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%% -->
        <p><a href="mailroom.php">Goto student page.</a></p>
        <div class="grid-container">
    		<div class="item1">
                <form name="checkedin" method="post">
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

                    <input type="submit" name="checkout"  value="Not Delivered">
                    <input type="submit" name="remove" value="Delivered">
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
                    // echo isset($_POST['msc-num']) ? 'should be updated' : 'didnt update';
                    if (isset($_POST['msc-num']) and $_POST['msc-num'] != '' and isset($_POST['location']) and $_POST['location'] != '') {
                        $mscNum = $_POST['msc-num'];
                        $location = $_POST['location'];

                        // $doc->getElementByID("load-outcome-text")->innerHTML = "worked";
                        // echo "worked";
                        $populateDB = "INSERT INTO PACKAGES (mscNum, location, checkedin, time_in) values (:mscNum, :location, 'no', null)";
                        $updateDB = $db->prepare($populateDB);
                        $outcome = $updateDB->execute([
                            'mscNum' => $mscNum,
                            'location' => $location
                        ]);

                        if ($outcome) {
                            echo "<p id='load-outcome'>New package has been added!</p>";
                        } else {
                            echo "<p id='load-outcome'>Please try again!</p>";
                        }
                    }
                ?>
	    	</div>
	    	<div class="item3">
                <form method="post">
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
                    <input type="submit" name="remove"  value="Remove">
                    <input type="submit" name="email" value="Email Reminder">
                </form>
	    	</div>
		</div>

        <p><a href="mailroom.php">Goto student page.</a></p>

    </body>
</html>