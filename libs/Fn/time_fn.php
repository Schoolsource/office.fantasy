<?php

class Time_Fn extends _function {
    
    public function live($timestamp, $style=false){
        $timestamp = strtotime($timestamp);
        $difference = time() - $timestamp;
        $periods = array("วินาที", "นาที", "ชั่วโมง");
        $ending = "ที่แล้ว";

        $dayName = array(0 => "วันอาทิตย์", "วันจันทร์", "วันอังคาร", "วันพุธ", "วันพฤหัสษบดี", "วันศุกร์", "วันเสาร์");
        $strDate = date("j", $timestamp) . " " . $this->month( date("n", $timestamp), $style);
        $strYear = date("Y", $timestamp) + 543;
        $strTimes = " เวลา " . date("H:i", $timestamp) . " น.";
        $dataDate = $this->day(date('w', $timestamp), true) . "ที่ $strDate $strYear $strTimes";

        if ($difference < 60) {
            $j = 0;
            $periods[$j].=($difference != 1) ? "" : "";
            $text = "$difference $periods[$j]$ending";
            $text = ($text == "0 วินาทีที่แล้ว") ? "ไม่กี่$periods[$j]$ending" : "$difference $periods[$j]$ending";
        } elseif ($difference < 3600) { // นาที
            $j = 1;
            $difference = round($difference / 60);
            $periods[$j].=($difference != 1) ? "" : "";
            $text = "$difference $periods[$j]$ending";
        } elseif ($difference < 86400) { // ชม
            $j = 2;
            $difference = round($difference / 3600);
            $periods[$j].=($difference != 1) ? "" : "";
            $difference = ($difference != 1) ? $difference : "ประมาณ 1";
            $text = "$difference $periods[$j]$ending";
        } elseif ($difference < 172800) {
            $difference = round($difference / 86400);
            $text = "เมื่อวานนี้ " . " เวลา " . date("H.i", $timestamp) . " น.";
        } elseif ($difference < 259200) {
            $difference = round($difference / 172800);
            $text = "เมื่อ" . $this->day(date("w", $timestamp), true) . " เวลา " . date("H.i", $timestamp) . " น.";
        } else {
            $text = $strDate;

            if ($timestamp < strtotime(date("Y-01-01 00:00:00")))
                $text .= " " . $strYear;
            $text .= $strTimes;
        }

        $text = '<abbr title="' . $dataDate . '" data-utime="' . $timestamp . '" class="timestamp livetimestamp">' . $text . '</abbr>';
        
        if($style == true){
            $text = '<abbr title="' . $dataDate . '" data-utime="' . $timestamp . '" class="timestamp livetimestamp">' . $dataDate . '</abbr>';
        }
        return $text;
    }

    public function stamp($timestamp=null){
        $timestamp = strtotime($timestamp);
        $difference = time() - $timestamp;
        $periods = array("วิ", "น.", "ชม.");

        $today = date('Y/m/d');
        $text = "";

        if ($difference < 60) { // วินาที
            $j = 0;
            $text = "$difference $periods[$j]";
        } elseif ($difference < 3600) { // นาที
            $j = 1;
            $difference = round($difference / 60);
            $text = "$difference $periods[$j]";
        } elseif ($difference < 86400) { // ชม
            $j = 2;
            $difference = round($difference / 3600);
            $text = "$difference $periods[$j]";
        }else{

            $deDate = date("j", $timestamp)." ".$this->month(date("n", $timestamp), false);
            $deDate .=( date("Y", strtotime($today))!=date('Y',$timestamp) )? " ".substr( (date('Y',$timestamp)+543), 2, 2): "";

            switch ($difference) {
                case '-86400':
                    $text = 'เมื่อวาน';
                    break;
                case 0:
                    $text = 'วันนี้';
                    break;
                case 86400:
                    $text = 'พรุ่งนี้';
                    break;
                
                default:
                    $text = $deDate;
                    break;
            }
        }

        $theDate = date("j", $timestamp);
        $theMonth = $this->month(date("n", $timestamp), true);
        $theYear = date("Y", $timestamp) + 543;
        $theTime = date("H.s", $timestamp);

        $title = "$theDate $theMonth $theYear เวลา {$theTime}น.";

        $text = '<span class="timestamp" data-time="'.$timestamp.'" title="'.$title.'">'.$text.'</span>';
        return $text;
    }

    public function day($length, $short=false, $lang='th'){

        $arr = $short
            ? array(
                'en' => array("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"),
                'th' => array("วันอาทิตย์", "วันจันทร์", "วันอังคาร", "วันพุธ", "วันพฤหัสษบดี", "วันศุกร์", "วันเสาร์"),
            )
            : array(
                'en' => array("Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"),
                'th' => array("อา.", "จ.", "อ.", "พ.", "พฤ.", "ศ.", "ส."),
            );

        return $arr[$lang][$length];
    }

    public function month($length, $short=false, $lang='th'){

       $arr = $short
            ? array(
                'en' => array(1=>"Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"),
                'th' => array(1=>"มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน", "กรกฎาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม"),
            )
            : array(
                'en' => array(1=>"January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"),
                'th' => array(1=>"ม.ค.", "ก.พ.", "มี.ค.", "เม.ย.", "พ.ค.", "มิ.ย.", "ก.ค.", "ส.ค.", "ก.ย.", "ต.ค.", "พ.ย.", "ธ.ค."),
            );   
        
        return $arr[$lang][$length];
    }

    public function full($timestamp,$short=false, $showyear=false,$showtime=true) { 
        
        $theDate = date("j", $timestamp);
        $theMonth = $this->month(date("n", $timestamp), $short);
        $theYear = date("Y", $timestamp)!=date("Y") || $showyear? date("Y", $timestamp) + 543: "";
        $theTime =  $showtime ? date("H.s", $timestamp) :'';
        $theTime = $showtime ?  'เวลา'. $theTime.' น.'  : '';
      
        return "$theDate $theMonth $theYear $theTime ";
    }

    public function birthday($date,$short=false, $showyear=false) { 
        
        $timestamp = strtotime($date);
        $theDate = date("j", $timestamp);
        $theMonth = $this->month(date("n", $timestamp), $short);
        $theYear = date("Y", $timestamp)!=date("Y") || $showyear? date("Y", $timestamp) + 543: "";

        return "$theDate $theMonth $theYear อายุ {$this->age($date)}";
    }

    public function normal($date, $lang='th') {

        $timestamp = strtotime($date);

        return $this->day(date('w', $timestamp), true, $lang).', '. date('j', $timestamp)." ". $this->month( date('n', $timestamp), true, $lang )." ". (date('Y', $timestamp)+543);
    }
    public function str_event_date($start, $end, $full=false, $lang='th'){
        $today = date('Y-m-d');
        $todayTime = strtotime($today);
        $todayYear = date('Y', $todayTime);

        $startTime = strtotime($start);
        $startDate = date('j', $startTime);
        $startMonth = date('n', $startTime);
        $startYear = date('Y', $startTime);

        if( $end=='0000-00-00 00:00:00' ){

            $theDate = date("j", $startTime);
            $difference = time() - $startTime;

            $theMonth = $this->month(date("n", $startTime), true);
            $theYear = date("Y", $startTime) + 543;
            $theTime = date("H.s", $startTime);

            $title = "{$theDate} {$theMonth} {$theYear} เวลา {$theTime}น.";

            if( $difference >= 0 && $difference < 86400){
                $theDate = 'วันนี้,';
            }
            $text = "{$theDate} {$theMonth} {$theYear}, {$theTime}น.";

            $text = '<span class="timestamp" data-time="'.$startTime.'" title="'.$title.'">'.$text.'</span>';
        }
        else{
            $endTime = strtotime($end);
            $endDate = date('j', $endTime);
            $endMonth = date('n', $endTime);
            $endYear = date('Y', $endTime);

            if( $startTime==$endTime || ($startDate==$endDate && $startMonth == $endMonth && $startYear == $endYear) ){
                $text = date('j', $startTime) . ' ' . $this->month( $startMonth, true, $lang );

                if( $todayYear!=$startYear || $full ){
                    $text .= ' '.($startYear);
                }
            }
            else if( ($startMonth == $endMonth && $startYear == $endYear) ){

                $text = date('j', $startTime) . ' - '.date('j', $endTime) . ' ' . $this->month( $startMonth, true, $lang );

                if( $todayYear!=$startYear || $full ){
                    $text .= ' '.$startYear;
                }
            }
            else if( $startYear == $endYear ){
                $text = date('j', $startTime) . ' ' . $this->month( $startMonth, true, $lang );
                $text .= ' - ';
                $text .= date('j', $endTime) . ' ' . $this->month( $endMonth, true, $lang );

                if( $todayYear!=$startYear || $full ){
                    $text .= ' '.$startYear;
                }
            }
            else{

                $text = date('j', $startTime) . ' ' . $this->month( $startMonth, true, $lang ) . ' ' .$startYear;
                $text .= ' - ';
                $text .= date('j', $endTime) . ' ' . $this->month( $endMonth, true, $lang ). ' ' .$endYear;

            }
        }

        return $text;
    }

    public function age($birthdayDate) {
        return floor((time() - strtotime($birthdayDate))/31556926);
    }


    public function getWeeks($date, $rollover='sunday') {
        $cut = substr($date, 0, 8);
        $daylen = 86400;

        $timestamp = strtotime($date);
        $first = strtotime($cut . "00");
        $elapsed = ($timestamp - $first) / $daylen;

        $weeks = 1;

        for ($i = 1; $i <= $elapsed; $i++)
        {
            $dayfind = $cut . (strlen($i) < 2 ? '0' . $i : $i);
            $daytimestamp = strtotime($dayfind);

            $day = strtolower(date("l", $daytimestamp));

            if($day == strtolower($rollover))  $weeks ++;
        }

        return $weeks;
    }

    public function theWeeks($theDate, $rollover='sunday') {
        $timestamp = strtotime($theDate);
        $week = date('w', $timestamp);

        if( $rollover=='monday' ){
            $week -= 1;
        }
        $arr['start'] = date('Y-m-d', strtotime("-{$week} days"));
        
        $week = 6-$week;
        $arr['end'] = date('Y-m-d',strtotime("+{$week} days"));

        return $arr;
    }

    #Fn By Ton
    public function DateDiff($strDate1,$strDate2){
        return (strtotime($strDate2) - strtotime($strDate1))/  ( 60 * 60 * 24 );
    }

    public function DayOfWeeks($week, $year=null){

        $year = !empty($year) ? $year : date("Y");
        
        $date = new DateTime();
        $date->setISODate($year,$week);
        $arr['start'] = $date->format("Y-m-d");
        $date->setISODate($year,$week,7);
        $arr['end'] = $date->format("Y-m-d");

        return $arr;
    }
}