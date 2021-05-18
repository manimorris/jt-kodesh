<?php

namespace jcal;
use DateTime;
include('kodeshDays.inc'); 
//require_once(dirname(__FILE__) . "/jt_kodesh_config.php");



interface IkodeshDays {
  /**
   * The constructed Object will have: Timezone, City, Country, Longitude, Latitude.
   * All Hebrew date calculations are done in the included 'kodeshdays.inc' file.
   *    Therefore the date functions use Julian dates. the conversion is done in the inc file.
   * Hour function - sunset and ex. are done with Gregorian dates.
   **/
  public function set_current_datetime($datetime); // Sets the base gregorian date for all calculations.  Default will be NOW in given Timezone.
  public function kodesh_day_status(); // Returns array(day status, day name, status timeout =[havdala || candle lighting]).
  public function is_kodeshDay($juldate); 
  public function get_kodesh_end_date($juldate);  
  public function get_candle_lighting($gdate);
  public function get_havdala_time($gdate);
  public function get_relative_hours($sunrise, $sunset);
  public function next_kodesh_days(); // Returns start time&date, end time&date.
}


class KodeshDays implements IkodeshDays {
  // Object properties:

  /** Location Properteis */
  public $timeZone;
  public $gmtOffset;
  public $city;
  public $countryCode;
  public $longitude;
  public $latitude;

  public $isDiaspora;

  /** Date and Time Properteis */
  public $timestamp;
  public $gdate;
  public $jdCurrent;


  /** Kodesh time settings */
  public $CANDLE_MIN = 18;
  public $HAVDALA_MIN = 40;



  /****
   * Counstructor recives:
   *  Client's geo location parameters as array.
   ****/
  function __construct($geo_vars) {

    /** Set object geo location properties. */
    $this->timeZone = timezone_open($geo_vars["geoplugin_timezone"]);
    $this->city = $geo_vars["geoplugin_city"];
    $this->countryCode = $geo_vars["geoplugin_countryCode"];
    $this->longitude = $geo_vars["geoplugin_longitude"];
    $this->latitude = $geo_vars["geoplugin_latitude"];


    /** As default, set the current datetime of given timezone. */
    $clients_date = new DateTime("now", $this->timeZone);
    $this->set_current_datetime($clients_date);
    $this->gmtOfsset = timezone_offset_get($this->timeZone, $clients_date)  / 3600;
     

    /** Check if client is from Diaspora */
    $this->isDiaspora = $this->countryCode != "IL" ? true : false;  // מספיק בטוח??
    
  }

  public function set_current_datetime($datetime) {
    /** 
     * Recives a PHP DateTime object.
     * Set gdate(m-d-Y) jdCurrent(julian date) and now(timestamp).
     **/

    $this->gdate = date_format($datetime, "m-d-Y");   

    /** Convert Gregorian into Julian date */
    list($gmonth, $gday, $gyear) = explode('-', $this->gdate); 
    $this->jdCurrent = gregoriantojd($gmonth, $gday, $gyear);

    /** Set timestamp for halacha time calculations */
    $this->timestamp = date_format($datetime, "U"); 

  }


  public function kodesh_day_status() {
     /***
      Returns array(
        day status,
        day name, 
        status ends =[havdala || candle lighting]
        status timeout = differene between [users] now and end time).
    ***/
    $result = array();
    
    $today = $this->is_kodeshDay($this->jdCurrent, $this->isDiaspora);
    $tomorrow = $this->is_kodeshDay($this->jdCurrent + 1, $this->isDiaspora);  

    if (!$today && $tomorrow) {

      // Check if alresy passed candle lighting time
      $candleLightTime = $this->get_candle_lighting($this->timestamp);

      if ($this->timestamp >= $candleLightTime ){

        // Set KODESH status.
        $result["kodesh_status"] = "KODESH";

        // Get Kodesh day name
        $result["day_name"] = "Erev $tomorrow";

        // Check havdala time & date.
        $endDate = $this->get_kodesh_end_date($this->jdCurrent + 1);
        $havdalaTime = $this->get_havdala_time($endDate);

        $result["status_ends"] = $havdalaTime;

      } else {

        // NOT KODESH
        $result["kodesh_status"] = "CHOL";
        $result["day_name"] = "Erev $tomorrow";

        // Set timeout to candle light time
        $result["status_ends"] = $candleLightTime;

      }

    } elseif ($today) {

      // Get Havdala time
      $endDate = $this->get_kodesh_end_date($this->jdCurrent);
      $havdalaTime = $this->get_havdala_time($endDate);

      // If havdala time is greater then now = it's still kodesh day.
      if( $this->timestamp < $havdalaTime) {

        // result is Kodesh day. check ending.
        $result["kodesh_status"] = "KODESH";
        $result["day_name"] = $today;
        $result["status_ends"] = $havdalaTime;

      } else {
          // Mozaei Shabbat
        $result["kodesh_status"] = "CHOL";
        $result["day_name"] = false;
        $result["status_ends"] = strtotime("+ 12 hours" , time());
      }

    } else {

      // Yom Chol
      $result["kodesh_status"] = "CHOL";
      $result["day_name"] = false;
      $result["status_ends"] = strtotime("+ 12 hours" , time());

    }

    // Setting end_time to the clients current time.
    $result["status_ends"] += date_offset_get (new DateTime); // ($this->gmtOffset * 3600);

    return $result;
  }
   

  public function is_kodeshDay($juldate) {
  
    // Check if given date is a Kodesh day.
    $isHoliday = getJewishHoliday($juldate, $this->isDiaspora);

    // weekdayNo: 0 = Sunday, 6 = Shabbat.
    $isShabbat = jddayofweek($juldate, 0) == 6 ? "Shabbat": false;
    
    // returns a value only if the isShabat or isHoliday.
    if ($isHoliday && $isShabbat) {

      return "$isShabbat - $isHoliday";

    } elseif ($isShabbat){

      return $isShabbat;

    } elseif ($isHoliday){

      return $isHoliday;
    } 

  }


  public function get_kodesh_end_date($juldate) {
    /**
     * must start from Kodesh day.
     * returns a gregorian date.
     * Checks if tomorrow and the next day are Kodesh.
     */
      
    if ($this->is_kodeshDay($juldate) && $this->is_kodeshDay($juldate + 1 )) {
      
      if ($this->is_kodeshDay($juldate + 2 )) {
        $endDate =  jdtogregorian($juldate + 2);

      } else {
        $endDate = jdtogregorian($juldate + 1);
      }

    } elseif ($this->is_kodeshDay($juldate)) {
      $endDate = jdtogregorian($juldate);

    } else {
      return;
    }

    // return $endDate;
    list($month, $day, $year) = explode("/", $endDate);
        
    // Return a timestamp of the end date.
    return mktime(0, 0, 0, $month, $day, $year);
  }


  private function get_sunrise($gdate) { //NEEDS TESTING
    // zeinth..
    $zenith = 90 + 50/60;
    $utcOffset = $this->gmtOffset * 3600;

    return date_sunrise( $gdate , SUNFUNCS_RET_TIMESTAMP , $this->latitude, $this->longitude, $zenith, $utcOffset ) ;
  }


  private function get_sunset($gdate) { //NEEDS TESTING
    // zeinth..
    $zenith = 90 + 50/60;
    $utcOffset = $this->gmtOffset * 3600;
  
    return $this->sunset = date_sunset( $gdate , SUNFUNCS_RET_TIMESTAMP , $this->latitude, $this->longitude, $zenith, $utcOffset ) ;
  }


  public function get_candle_lighting($gdate){
    // Sunrise for given date (fromated as timestamp)
    $sunset = $this->get_sunset($gdate);
    $candleMin = $this->CANDLE_MIN;
    // Check config for minutes added.
    $candleLightTime = strtotime("-". $candleMin * 60 ." seconds", $sunset); // reduce ?? minutes in timestamp
        
    return $candleLightTime; 
  }


  public function get_havdala_time($gdate){

    $sunrise = $this->get_sunrise($gdate);
    
    $sunset = $this->get_sunset($gdate);

    $rel_min = $this->get_relative_hours($sunrise, $sunset);
    
    $havdalaMin = $this->HAVDALA_MIN * ($rel_min / 60 ); //HAVDALA_MIN from config file
    
    $havdalaTime = strtotime('+'. round($havdalaMin * 60) .' seconds', $sunset);

    return $havdalaTime;
  }


  public function get_relative_hours($sunrise, $sunset){
    /** 
     * Returns relative hours in minutes. 
     * This is calculated according to the "Baal Hatanya's" opinion.
     * */
    return  abs(($sunset - $sunrise) / 12 / 60);
  }




  public function next_kodesh_days() {
    $result = array();

    /** Set base hour to  00:00 */
    date_default_timezone_set("UTC");
    $dt = new DateTime(strtotime("", $this->timestamp));
    $dt = date_time_set($dt , 0 , 0);
    $this->timestamp = date_format($dt, "U");

    /** Get the start day */
    $startJulDate = "";

    $i = 0;
    do {

      $kodeshDay = $this->is_kodeshDay( $this->jdCurrent + $i );

      if( $kodeshDay ) {

        $result["next_kodeshDay_starts"] = strtotime("+ " .  ($i - 1) . " days", $this->timestamp);
        $result["next_kodeshDay_name"] = $kodeshDay;

        $startJulDate = $this->jdCurrent + $i;

        // exit the loop
        $i = 10;
      } else {

        $i++;
      }
    }
    while ($i < 8);


    /** Get the end day and time */
    $endDate = $this->get_kodesh_end_date($startJulDate);
    $result["next_kodeshDay_ends"] = strtotime("+ 36 hours", $endDate); 


    return $result;
  }



  public function holidays_by_gyear($gyear, $isDiaspora) {  //NOT NECESARY
    $result = array();
    for ($gmonth = 1; $gmonth <= 12; $gmonth++) {
      $lastGDay = cal_days_in_month(CAL_GREGORIAN, $gmonth, $gyear);
      
      for ($gday = 1; $gday <= $lastGDay; $gday++) {
        $jdCurrent = gregoriantojd($gmonth, $gday, $gyear); 
         
        $jewishD = jdtojewish($jdCurrent);
        list($jewishMonth, $jewishDay, $jewishYear) = explode('/', $jewishD);
        $jewishMonthName = getJewishMonthName($jewishMonth, $jewishYear);
        $holidays = getJewishHoliday($jdCurrent, $isDiaspora);
        
         if ($holidays) {     
          $gdate = implode("/", array( $gday, $gmonth, $gyear));
          $weekday = jddayofweek($jdCurrent, 1);
          $HebDate = jdtojewish($jdCurrent, false ,CAL_JEWISH_ADD_ALAFIM_GERESH);

          $result[] = array($holidays, "Date" => $gdate, "Jewish Date" => $HebDate , "Day" => $weekday);
        }
        
     }
    }
    return $result;
  }


}

 





?>
