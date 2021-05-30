<?php
    $db = new PDO('sqlite:sixflags.db') or die ("cannot open database");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>

<!DOCTYPE HTML>
<html>
    <head>
        <title>Six Flags</title>
        <link rel="icon" type="image/jpg" href="images/sixflags_icon.jpg">
        <link rel="stylesheet" type="text/css" href="sfStyle.css">
    </head>
    <body>
        <div class="header">
            <div id="banner">
                <a href="https://sixflags.com">
                    <img id="sfBanner"
                        src="images/sixflags_logo.png"
                        alt="Six Flags 2020"
                        title="Coming to Six Flags">
                </a>
            </div>
        </div>
        <div class="content">
            <h1>Welcome to Six Flags!</h1>
            <br/>
            <form method="post">
                <select name="park" id="parkSel" onchange="console.log(document.getElementById('parkSel').value)">
                    <?php
                        $sql = "SELECT * FROM PARK_INFO";
                        $result = $db->query($sql);
                        foreach($result as $row) {
                            echo "<option value=".$row['parkID'].">".$row['parkName'].", ".$row['location']."</option>";
                        }
                    ?>
                </select>
                <select name="info">
                    <option value="parkInfo">Park Information</options>
                    <option value="costInfo">Cost Information</options>
                    <option value="rideInfo">Ride Information</options>
                </select>
                <br/>
                <p>For Ordering Ride Info: 
                    <select name="order">
                        <option value="rideName">Name</option>
                        <option value="thrillLevelID">Thrill Level</option>
                        <option value="rideTypeID">Type</option>
                        <option value="minHeight">Height</option>
                    </select>
                </p>
                <input type="submit" value="GO!"/>
            </form>
            <?php 
                if (isset($_POST['park'])) {
                    $parkSelected = $_POST['park'];
                    // echo "<br/> Park Number: ".$parkSelected."<br/>";
                } else {
                    echo "<br/> Please Select a Park and Hit 'GO!'";
                }
                if (isset($_POST['info'])) {
                    $infoSelected = $_POST['info'];
                    // echo "<br/> What do you want? : ".$infoSelected."<br/>";
                }
                if (isset($_POST['order'])) {
                    $orderSelected = $_POST['order'];
                } else {
                    echo "<br/>Only choose order if you want to sort Ride Info.";
                }
            ?>
            <br/>
            <table>
                <caption>
                    <?php 
                        if (isset($parkSelected)) {
                            $title = "SELECT parkName
                                      From PARK_INFO AS P
                                      WHERE P.parkID =".$parkSelected;
                            $titleQuery = $db->query($title);
                            foreach ($titleQuery as $titleName) {
                                echo $titleName['parkName'];
                            }
                        }
                    ?>
                </caption>
                <?php 
                    if (isset($infoSelected)) {
                        if($infoSelected == "parkInfo") {
                            $park = "SELECT *
                                     FROM PARK_INFO AS P
                                     WHERE P.parkID =".$parkSelected;
                            $parkInfo = $db->query($park);
    
                            echo "<tr><th>Location</th><th>Month Opens</th><th>Month Closes</th><th>Size</th></tr>";
                            foreach($parkInfo as $row) {
                                echo "<tr><td>".$row['location']."</td><td>".$row['dateOpen']."</td><td>".$row['dateClosed']."</td><td>".$row['parkSize']." acres</td></tr>";
                            }
                        } else if ($infoSelected == "costInfo") {
                            $cost = "SELECT *
                                     FROM COST_INFO AS C
                                     WHERE C.parkID =".$parkSelected;
                            $costInfo = $db->query($cost);
    
                            echo "<tr><th>Day Pass</th><th>Season Pass</th><th>Membership</th><th>Dining Pass</th></tr>";
                            foreach($costInfo as $row) {
                                echo "<tr><td>$".$row['dayPassCost']."</td><td>$".$row['seasonPassCost']."</td><td>$".$row['membershipCost']."</td><td>$".$row['diningPassCost']."</td></tr>";
                            }
                        } else if ($infoSelected == "rideInfo") {
                            $ride = "SELECT *
                                     FROM RIDE_INFO AS R, PARK_RIDES AS P, RIDE_TYPES AS Tp, THRILL_LEVEL AS Th
                                     WHERE P.parkID =".$parkSelected." AND P.rideID = R.rideID AND R.thrillLevel = Th.thrillLevel AND R.rideType = Tp.rideType
                                     ORDER BY ".$orderSelected;

                            $rideInfo = $db->query($ride);
    
                            echo "<tr><th>Name</th><th>Thrill Level</th><th>Type</th><th>Minimum Height Req.</th></tr>";
                            foreach($rideInfo as $row) {
                                echo "<tr><td>".$row['rideName']."</td><td>".$row['thrillLevel']."</td><td>".$row['rideType']."</td><td>".$row['minHeight']."&quot;</td></tr>";
                            }
                        } 
                    }
                ?>
            </table>

            <br/>
            <p><a href="sixflagsSearch.php">Click here for the Ride Search Page</a></p>

        </div>
    </body>
</html>
