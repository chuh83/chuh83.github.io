<?php
    $db = new PDO('sqlite:sixflags.db') or die ("cannot open database");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>

<!DOCTYPE HTML>
<html>
    <head>
        <title>Six Flags</title>
        <link rel="icon" type="image/jpg" href="images/sixflags_icon.jpg">
        
        <style>
            .header {
                background-color: #A91A16;
            }
            
            #banner {
                display: grid;
                height: 100%;
                background-color: #A91A16;
            }
            
            #sfBanner {
                max-height: 100vh;
                max-width: 100%;
                margin: auto;
            }
            
            .content {
                display: block;
                justify-content: center;
                text-align: center;
                margin-left: auto;
                margin-right: auto;
                padding-top: 1%;
                padding-bottom: 1%;
                align-self: center;
                max-width: 1280px;
                background-color: #074579;
                opacity: 90%;
                color: white;
                font-family: sans-serif;
            }
            
            body {
                background-color: #A91A16;
/*
                background-image: url("candy2.jpg");
                background-repeat: no-repeat;
                background-attachment: fixed;
                background-position: center;
                background-size: contain; 
*/
            }

            table {
                width: 70%;
                margin-left: auto;
                margin-right: auto;
            }

            caption {
                font-size: 130%;
                font-weight: bold;
            }

            /* .tb {
                align-content: center;
                margin-left: auto;
                margin-right: auto;
            } */

            th, td {
                padding: 3%;
                border-bottom: 2px solid #ddd;
            }
        </style>
    </head>
    <body>
        <div class="header">
            <div id="logo">
                <a href="https://sixflags.com">
                    <img src="images/sixflags_logo.png"
                        width="256"
                        height="124"
                        alt="Six Flags"
                        title="Six Flags"></a>
            </div>
            <div id="banner">
                <img id="sfBanner"
                     src="images/sixflags_2020.png"
                     alt="Six Flags 2020"
                     title="Coming to Six Flags">
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
                        <option value="thrillLevel">Thrill Level</option>
                        <option value="rideType">Type</option>
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

                // $allCost = "SELECT * 
                //             FROM PARK_INFO AS P, COST_INFO AS C
                //             WHERE P.parkID =".$parkSelected." AND P.parkID = C.parkID";
                // $costInfo = $db->query($allCost);
                // foreach($costInfo as $row) {
                //     echo "<br/>";
                //     print_r($row);
                // }
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
                        $title = "SELECT parkName
                                  From PARK_INFO AS P
                                  WHERE P.parkID =".$parkSelected;
                        $titleQuery = $db->query($title);
                        foreach ($titleQuery as $titleName) {
                            echo $titleName['parkName'];
                        }
                    ?>
                </caption>
                <?php 
                    if($infoSelected == "parkInfo") {
                        $park = "SELECT *
                                 FROM PARK_INFO AS P
                                 WHERE P.parkID =".$parkSelected;
                        $parkInfo = $db->query($park);

                        echo "<tr><th>Location</th><th>Month Opens</th><th>Month Closes</th></tr>";
                        foreach($parkInfo as $row) {
                            echo "<tr><td>".$row['location']."</td><td>".$row['dateOpen']."</td><td>".$row['dateClosed']."</td></tr>";
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
                                 FROM RIDE_INFO AS R, PARK_RIDES AS P
                                 WHERE P.parkID =".$parkSelected." AND P.rideID = R.rideID
                                 ORDER BY ".$orderSelected;

                        $rideInfo = $db->query($ride);

                        echo "<tr><th>Name</th><th>Thrill Level</th><th>Type</th><th>Minimum Height Req.</th></tr>";
                        foreach($rideInfo as $row) {
                            echo "<tr><td>".$row['rideName']."</td><td>".$row['thrillLevel']."</td><td>".$row['rideType']."</td><td>".$row['minHeight']."</td></tr>";
                        }
                    } 
                    
                ?>
            </table>
        </div>
    </body>
</html>