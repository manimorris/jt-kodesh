<html>
<div class="wrap">
<h1>Jewtech Kodesh Plugin</h1>

<?php

    $options = get_option("next_kodesh_time");
	
	$next_date = $options["next_kodeshDay_starts"];
	//echo "<h2>Next start time is: " . date("D d-m-Y  H:i:s", $next_date) ."</h2>";


	$day_name = $options["next_kodeshDay_name"];
	//echo "<h2>Next Kodesh day is: $day_name</h2>";

	
	$day_end = $options["next_kodeshDay_ends"];
	//echo "<h2>The term ends in: " . date("D d-m-Y H:i:s", $day_end) ."</h2>";

	//remove_next_kodesh_option();  // Should be removed. for testing purpose.

	
	// Run popup test when called
	if ( isset($_GET["jtk-test"] )) {
		echo do_shortcode( '[jtk-test]' );
	}
	 
	
	

?>

	
	<HR>	
		
		<button id="testBlocker" class='btn' onclick="addUrlParameter('jtk-test', '1')">Test Blocker Page</button>
	<HR>
	
	
	
    
</div>

<script>
	function addUrlParameter(name, value) {
	  var searchParams = new URLSearchParams(window.location.search)
	  searchParams.set(name, value)
	  window.location.search = searchParams.toString()
	}
</script>


</html>




