<?php

	require_once('classes/KodeshDays.php');
	require_once('classes/GeoIP.php');

	

#region Incoming traffic monitoring

	# Check first if client has a cookie set
	function get_client_kodeshDay_status() {

		$client_cookie = get_jt_cookie();
		
		if( $client_cookie ) {

			return $client_cookie["kodesh_status"];

		} else {

			/** Here the application will check if now is a kodesh day:
			 * 	1. Get client's IP
			 * 	2. With the IP, get the Geo Location.
			 * 		- if Location wasent detected - we use the websites default location.
			 * 	3. According to the clients location we check if it's a Kodesh day.
			 * 		- if YES then we call pop_site_block.
			 * 		- else the site is loaded.
			 * 	4. At last we create a COOKIE to store the result for the next page load.
			 * 	5. Retrun an array with: 'kodesh_status' and 'day_name'.
			* **/
			$clients_ip = get_the_client_ip();
			
			# Get clients location
			$client_geo = get_geo_by_ip($clients_ip);

			# Check if the current day is 'kodesh' 
			# according to the client's location.    
			$kodesh = new jcal\KodeshDays($client_geo);
			$kodeshStatus = $kodesh->kodesh_day_status();

			# Create a COOKIE with kodesh_status values
			add_jt_cookie($kodeshStatus);

			return $kodeshStatus;
		}
	}

#endregion



#region COOKIE HANDLER

function add_jt_cookie($values) {

	$lifetime = $values["status_ends"];
	
	$arr = array(
		"kodesh_status" => $values["kodesh_status"],
		"day_name" => $values["day_name"],
		"ends" => $lifetime
	);

	foreach($arr as $k => $v) {
		setcookie("jt_kodesh_pass[$k]", $v, $lifetime);
	}	

	return $arr;
}


function get_jt_cookie() {

	if (isset($_COOKIE['jt_kodesh_pass'])) {

		return $_COOKIE["jt_kodesh_pass"];
	} 
}


function delete_jt_cookie() {
	setcookie ("jt_kodesh_pass", "", time()-100 , '/' );
}

/// Cookie encrypt + decrypt (?).

#endregion



#region IP & Geo Location HANDLERS

function get_the_client_ip() {
    $keys=array('HTTP_CLIENT_IP','HTTP_X_FORWARDED_FOR','HTTP_X_FORWARDED','HTTP_FORWARDED_FOR','HTTP_FORWARDED','REMOTE_ADDR');
        foreach($keys as $k) {
			if (!empty($_SERVER[$k]) && filter_var($_SERVER[$k], FILTER_VALIDATE_IP)) {
				return $_SERVER[$k];
			}
        }
	return "UNKNOWN";
}



function get_geo_by_ip($clients_ip) {
	
	if(!$clients_ip || ($clients_ip == "UNKNOWN")){

		$tz_string = get_option( "timezone_string");

		$tz = timezone_open($tz_string);
		$tz_l = timezone_location_get($tz);
		
		$client_geo = array(
			"geoplugin_timezone" => $tz_string,
			"geoplugin_city" => "", //////////
			"geoplugin_countryCode" => $tz_l["country_code"],
			"geoplugin_longitude" => $tz_l["longitude"],
			"geoplugin_latitude" => $tz_l["latitude"]
		);

		return $client_geo;
	
	} else {

		// IP was located
		// Get client's location
		$geo =  new jcal\GeoIP;

		$geo->client_ip = $clients_ip;
	
		$client_geo = $geo->get_geo_vars();
	
		return $client_geo;
	}

}

#endregion

// function set_status_timeout($endTime, $timezone){
//     /** 
//      * Recives endTime as timestamps.
// 	 	* Recives timezone as TimeZone object.
//      * Returns difference between them is seconds.
//      **/
//     $tz = (array)$timezone;
//     date_default_timezone_set($tz["timezone"]);
//     $now = mktime();
    
//      return ($endTime - $now);
//   }




#region  NEXT KODESH TIME OPTION

function add_next_kodesh_option() {
	
	$next_kodesh_time = set_next_kodesh_time();

	add_option('next_kodesh_time', $next_kodesh_time);

	return $next_kodesh_time;
}



function update_next_kodesh_option() {
	
	$next_kodesh_time = set_next_kodesh_time();

	update_option('next_kodesh_time', $next_kodesh_time);

	return $next_kodesh_time;
}



function remove_next_kodesh_option() {

	delete_option('next_kodesh_time');
}



function get_next_kodesh_option() {

	return get_option('next_kodesh_time');	
}


function set_next_kodesh_time() {
		
	/** Create kodesh day Object and get the next day */
	$client_geo = array(
		"geoplugin_timezone" => "Asia/Jerusalem",  //symboly 
		"geoplugin_city" => "", //////////
		"geoplugin_countryCode" => "NY",  //in order to catch chag sheni
		"geoplugin_longitude" => 0,
		"geoplugin_latitude" => 0
	);

	$kd = new jcal\KodeshDays($client_geo);
	$next_kodesh_times = $kd->next_kodesh_days();

	// $result["next_kodeshDay_starts"];
	// $result["next_kodeshDay_name"];
    // $result["next_kodeshDay_ends"];

	return $next_kodesh_times;	
}


function next_kodesh_time() {

	if(!get_next_kodesh_option()) {

		$next_kodesh_time = add_next_kodesh_option();

	} else {

		$next_kodesh_time = get_next_kodesh_option();

	}

	return $next_kodesh_time;
}

#endregion




?>