<?php
function isJewishLeapYear($year) {
  if ($year % 19 == 0 || $year % 19 == 3 || $year % 19 == 6 ||
      $year % 19 == 8 || $year % 19 == 11 || $year % 19 == 14 ||
      $year % 19 == 17)
    return true;
  else
    return false;
}

function getJewishMonthName($jewishMonth, $jewishYear) {
  $jewishMonthNamesLeap = array("Tishri", "Heshvan", "Kislev", "Tevet",
                                "Shevat", "Adar I", "Adar II", "Nisan",
                                "Iyar", "Sivan", "Tammuz", "Av", "Elul");
  $jewishMonthNamesNonLeap = array("Tishri", "Heshvan", "Kislev", "Tevet",
                                   "Shevat", "", "Adar", "Nisan",
                                   "Iyar", "Sivan", "Tammuz", "Av", "Elul");
  if (isJewishLeapYear($jewishYear))
    return $jewishMonthNamesLeap[$jewishMonth-1];
  else
    return $jewishMonthNamesNonLeap[$jewishMonth-1];
}

function getJewishHoliday($jdCurrent, $isDiaspora) {
  $result = "";

  $TISHRI = 1;
  $HESHVAN = 2;
  $KISLEV = 3;
  $TEVET = 4;
  $SHEVAT = 5;
  $ADAR_I = 6;
  $ADAR_II = 7;
  $ADAR = 7;
  $NISAN = 8;
  $IYAR = 9;
  $SIVAN = 10;
  $TAMMUZ = 11;
  $AV = 12;
  $ELUL = 13;

  $SUNDAY = 0;
  $MONDAY = 1;
  $TUESDAY = 2;
  $WEDNESDAY = 3;
  $THURSDAY = 4;
  $FRIDAY = 5;
  $SATURDAY = 6;

  $jewishDate = jdtojewish($jdCurrent);
  list($jewishMonth, $jewishDay, $jewishYear) = explode('/', $jewishDate);
  
  
   // Test (today)
	//if ($jewishDay == 26 && $jewishMonth == $TAMMUZ)
    //$result = "TEST HOLIDAY";

  // Kodesh days in Elul 
  // NONE

  // Kodesh days in Tishri
  if ($jewishDay == 1 && $jewishMonth == $TISHRI)
    $result = "Rosh Hashanah I";
  
  if ($jewishDay == 2 && $jewishMonth == $TISHRI)
    $result = "Rosh Hashanah II";
  
  if ($jewishDay == 10 && $jewishMonth == $TISHRI)
    $result = "Yom Kippur";
 
  if ($jewishDay == 15 && $jewishMonth == $TISHRI)
    $result = "Sukkot I";
  
  if ($jewishDay == 16 && $jewishMonth == $TISHRI && $isDiaspora)
    $result = "Sukkot II";
  
  if ($isDiaspora) {
    if ($jewishDay == 22 && $jewishMonth == $TISHRI)
      $result = "Shemini Azeret";
    if ($jewishDay == 23 && $jewishMonth == $TISHRI)
      $result = "Simchat Torah";
  } else {
    if ($jewishDay == 22 && $jewishMonth == $TISHRI)
      $result = "Shemini Azeret/Simchat Torah";
  }

  // Kodesh days in Kislev & Tevet & Shevat & Adar
  // NONE

  // Holidays in Nisan
  if ($jewishDay == 15 && $jewishMonth == $NISAN)
    $result = "Pesach I";
  
  if ($jewishDay == 16 && $jewishMonth == $NISAN && $isDiaspora)
    $result = "Pesach II";
  
  if ($jewishDay == 21 && $jewishMonth == $NISAN)
    $result = "Pesach VII";
 
  if ($jewishDay == 22 && $jewishMonth == $NISAN && $isDiaspora)
    $result = "Pesach VIII";
  

  // Holidays in Iyar
  // NONE


  // Holidays in Sivan

  if ($jewishDay == 6 && $jewishMonth == $SIVAN)
    $result = "Shavuot I";
  
  if ($jewishDay == 7 && $jewishMonth == $SIVAN && $isDiaspora)
    $result = "Shavuot II";
  

  // Holidays in Tammuz & Av
  // NONE
 


  return $result;
}
?>