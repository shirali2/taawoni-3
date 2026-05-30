<?php
//تبدیل عدد به حروف فارسی


namespace App;

class Convert_Number_To_Persian_Words{
  private $digit1 = array(
            0 => 'صفر',
            1 => 'یک',
            2 => 'دو',
            3 => 'سه',
            4 => 'چهار',
            5 => 'پنج',
            6 => 'شش',
            7 => 'هفت',
            8 => 'هشت',
            9 => 'نه'
          ),
          $digit1_5 = array(
            1 => 'یازده',
            2 => 'دوازده',
            3 => 'سیزده',
            4 => 'چهارده',
            5 => 'پانزده',
            6 => 'شانزده',
            7 => 'هفده',
            8 => 'هجده',
            9 => 'نوزده'
          ),
          $digit2 = array(
            1 => 'ده',
            2 => 'بیست',
            3 => 'سی',
            4 => 'چهل',
            5 => 'پنجاه',
            6 => 'شصت',
            7 => 'هفتاد',
            8 => 'هشتاد',
            9 => 'نود'
          ),
          $digit3 = array(
            1 => 'صد',
            2 => 'دویست',
            3 => 'سیصد',
            4 => 'چهارصد',
            5 => 'پانصد',
            6 => 'ششصد',
            7 => 'هفتصد',
            8 => 'هشتصد',
            9 => 'نهصد'
          ),
          $steps = array(
            1 => 'هزار',
            2 => 'میلیون',
            3 => 'بیلیون',
            4 => 'تریلیون',
            5 => 'کادریلیون',
            6 => 'کوینتریلیون',
            7 => 'سکستریلیون',
            8 => 'سپتریلیون',
            9 => 'اکتریلیون',
            10 => 'نونیلیون',
            11 => 'دسیلیون'
          );

  function number_format($number, $decimal_precision = 0, $decimals_separator = '.', $thousands_separator = ','){
    $number = explode('.', str_replace(' ', '', $number));
    $number[0] = str_split(strrev($number[0]), 3);
    $total_segments = count($number[0]);
    for($i = 0; $i < $total_segments; $i++) $number[0][$i] = strrev($number[0][$i]);
    $number[0] = implode($thousands_separator, array_reverse($number[0]));
    if(!empty($number[1])) $number[1] = round($number[1], $decimal_precision);

    return implode($decimals_separator, $number);
  }

  protected function groupToWords($group){
    $d3 = floor($group / 100);
    $d2 = floor(($group - $d3 * 100) / 10);
    $d1 = $group - $d3 * 100 - $d2 * 10;

    $group_array = array();

    if($d3 != 0) $group_array[] = $this->digit3[$d3];

    if($d2 == 1 && $d1 != 0): $group_array[] = $this->digit1_5[$d1]; //11-19
    elseif($d2 != 0 && $d1 == 0): $group_array[] = $this->digit2[$d2]; //10-20-...-90
    elseif ($d2 == 0 && $d1 == 0): //00
    elseif($d2 == 0 && $d1 != 0): $group_array[] = $this->digit1[$d1]; //1-9
    else: //Others
      $group_array[] = $this->digit2[$d2];
      $group_array[] = $this->digit1[$d1];
    endif;

    if(!count($group_array)) return false;

    return $group_array;
  }

  public function get($number){
    $formated = $this->number_format($number, 0, '.', ',');
    $groups = explode(',', $formated);

    $steps = count($groups);

    $parts = array();
    foreach($groups as $step => $group):
      $group_words = self::groupToWords($group);
      if($group_words):
        $part = implode(' و ', $group_words);
        if (isset($this->steps[$steps - $step - 1])) $part .= ' ' . $this->steps[$steps - $step - 1];
        $parts[] = $part;
      endif;
    endforeach;

    return implode(' و ', $parts);
  }
}