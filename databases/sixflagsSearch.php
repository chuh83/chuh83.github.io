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
            <h1>Ride Search</h1>
            <br/>
            <form method="post">
                                
                <p>Search for a ride by thrill level and ride type: 
                    <select name="thrill">
                        <option value="Mild">Mild</option>
                        <option value="Moderate">Moderate</option>
                        <option value="Max">Max</option>
                    </select>
		            <select name = "ride">
		      	        <option value="Kids">Kids</option>
                        <option value="Family">Family</option>
                        <option value="Thrill">Thrill</option>
	                </select>
                </p>
		
                <p> OR </p>		
                        
                <p> Search for a ride by name:
                    <input type="text" name="rideSearch"/>
                </p>
                <input type="submit" value="Search"/>


            </form>
            <br/>	   
            <table>	    
                <?php
                if (isset($_POST['rideSearch']) and $_POST['rideSearch'] != ''){
                    $rideSearched = $_POST['rideSearch'];
                    $rideInfoQuery = "SELECT rideID, rideName, thrillLevel, minHeight FROM RIDE_INFO AS R WHERE R.rideName ='".$rideSearched."'";
                    
                    $testQuery = "SELECT parkName from PARK_INFO WHERE parkID IN (SELECT parkID FROM PARK_RIDES WHERE rideID = (SELECT RI.rideID FROM RIDE_INFO AS RI WHERE RI.rideName ='".$rideSearched."'))";
                    $rideInfo = $db->query($rideInfoQuery);
                    $testInfo = $db->query($testQuery);
                    echo "<tr><th>Ride Name</th><th>Thrill Level</th><th>Minimum Height</td></tr>";
                    foreach($rideInfo as $row){
                        echo "<tr><td>".$row['rideName']."</td><td>".$row['thrillLevel']."</td><td>".$row['minHeight']."</td></tr>";
                        }
                    echo "<tr><th> </th><th> This ride is available at the following locations: </th><th> </th></tr>";
                    foreach ($testInfo as $row) { 
                        echo "<tr><td> </td><td>".$row['parkName']."</td><td> </td></tr>";
                    }	
                
                }		   
        
                else if (isset($_POST['thrill']) and isset($_POST['ride'])) {
                    $thrillLevelSelected = $_POST['thrill'];
                    $rideLevelSelected = $_POST['ride'];
        
                    $thrillQuery = "SELECT rideID, rideName, thrillLevel, minHeight FROM RIDE_INFO AS R WHERE R.thrillLevel = '".$thrillLevelSelected."' AND R.rideType = '".$rideLevelSelected."'";
        
                    $thrillInfo = $db->query($thrillQuery);
                    echo "<tr> Results for $thrillLevelSelected and $rideLevelSelected </tr>";	
                    echo "<tr><th>Ride Name</th><th>Thrill Level</th><th>Minimum Height</td></tr>";
                    foreach($thrillInfo as $row) {
                    echo "<tr><td>".$row['rideName']."</td><td>".$row['thrillLevel']."</td><td>".$row['minHeight']."&quot;</td></tr>";
                    }
                }	       
                    
                ?>	
            </table>
            
            <br/>
        
            <p><a href="sixflagsv2.php">Click here to return to the Home Page</a></p>
        </div>
    </body>
</html>
