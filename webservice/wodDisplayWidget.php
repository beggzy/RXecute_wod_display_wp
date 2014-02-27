<?php

	// Variables from POST
	$userBoxCode = $_POST["userBoxCode"];


	// Search the affiliateGyms Table to pull out the gymID
	$queryAffiliates="SELECT id FROM affiliateGyms WHERE boxCode='$userBoxCode' LIMIT 1";
	$gymResult=mysql_query($queryAffiliates, $dbMaster);

	// the number of results returned
	$numGyms = mysql_numrows($gymResult);

	if ($numGyms == 0) {
	    // No gyms have the given boxCode, Send error message back to the app
		echo "<p>Connection Error.</p>";
	}
	else
	{
		//Get the GYM ID for the new db connection
		$dbGymID = mysql_result($gymResult, 0);


		// Close the connection once we are finished using them
		mysql_close($dbMaster);


		$wodDate = date("Y-m-d");

		mysql_select_db($databaseClient, $dbClient) OR die("Unable to select database");

		// Need to comment here and add in the dateID
		$query = "SELECT * FROM wodComponentsForDateTable WHERE date='$wodDate'";
		$result = mysql_query($query,$dbClient);

		// Get the number of rows from the search query
		$num = mysql_numrows($result);

		if ($num == 0)
		{
			// This will need to be changed into an array to return to the app with this information
			echo "<h5>No WOD Posted</h5>";
		}
		else
		{
			// wodIDs are stored as a JSON object in the field
			$jsonResult = mysql_result($result,0,'wodIDsForDate');
			$wodDateID = mysql_result($result,0,'dateID');
			// This is the array php can now use
			$wodIDsForDate = json_decode($jsonResult);

			$numberOfComponents = count($wodIDsForDate);
			if ($numberOfComponents == 0)
			{
				echo "<h5>No WOD Posted</h5>";
			}
			else
			{
				// This is an array for the WODComponentTypeName
				$wodComponentTypeNameArray = array("Warm up","Strength","Conditioning","Skills","Core","Tabata","Supplemental","Stretching", "No WOD Posted", "Personal Notes");

				for ($i = 0; $i < $numberOfComponents-1; $i++)
				{
					$wodID = $wodIDsForDate[$i];

					// Search query for the Wod to get the components
					$query = "SELECT * FROM wodComponentBinTable WHERE wodID=$wodID";
					$result=mysql_query($query);

					// Making the assumption here that the wodID does exist, probably need to work an error here
					$wodComponentTypeID = mysql_result($result,0,'wodComponentTypeID');
					$wodComponentName = mysql_result($result,0,'wodComponentName');
					$wodAdminComments = mysql_result($result,0,'wodAdminComments');

					$wodComponentTypeName = $wodComponentTypeNameArray[$wodComponentTypeID];


					if ($wodComponentTypeName){echo "<h5>$wodComponentTypeName</h5>";}
					if ($wodComponentName){echo "<h5>$wodComponentName</h5>";}
					if ($wodAdminComments){echo "<p>$wodAdminComments</p>";}
				}
			}
		}
	mysql_close($dbClient);
	}

?>