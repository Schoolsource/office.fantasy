<?php

class Val {

    public function minlength($data, $arg) {
        if (strlen($data) < $arg)
            return "ต้องมีความยาวอย่างน้อย $arg ตัวอักษร";
    }

    public function maxlength($data, $arg) {
        if (strlen($data) > $arg)
            return "ต้องมีความยาวไม่เกิน $arg ตัวอักษร";
    }
    
    public function numless($num, $arg=0) {
        if (intval($num) > $arg) return "ต้องมีค่าน้อยกว่า $arg";
    }
    public function nummore($num, $arg=0) {
        if (intval($num) < $arg) return "ต้องมีค่ามากกว่า $arg";
    }

    // ช่องนี้เว้นว่างไว้ไม่ได้
    public function is_empty($data) {
        if (empty($data))
            return 'ช่องนี้เว้นว่างไว้ไม่ได้'; //"ช่องนี้เว้นว่างไว้ไม่ได้";
    }

    public function space($text) {
        if ($text=="") return "ช่องนี้เว้นว่างไว้ไม่ได้";
    }

    public function numeric($data) {
        if (is_numeric($data) == false && !empty($data))
            return "ต้องเป็นตัวเลขเท่านั้น";
    }

    /*public function email($data) {
        $pattern_email = "^[a-z][a-z0-9\_\-\.]*@[a-z][a-z0-9\_\-]*(\.[a-z][a-z0-9\_\-]*)+$";
        if (!@ereg($pattern_email, $data))
            return "อีเมลไม่ถูกต้อง";
    }*/

    public function email($data) {

        if (!filter_var($data, FILTER_VALIDATE_EMAIL)){
            return "นั่นไม่ใช่อีเมล์ที่ถูกต้อง";
        }

        /*$ext = explode("@", $data);
        if( !in_array($ext[1], array('gmail.com','hotmail.com')) ){
            return "ไม่สามารถใช่อีเมล์ @ นี้ได้! (สามารถใช้ @gmail.com และ @hotmail.com เท่านั้น)";
        }*/
    }

    public function username($text, $minlength = 4, $maxlength=15) {

        if (strlen($text) < $minlength){
            return "ชื่อผู้ใช้ไม่ถูกต้อง! ต้องมีความยาว {$minlength} ตัวอักษรขึ้นไป";
        }
        else if(strlen($text) > $maxlength){
            return "ชื่อผู้ใช้ไม่ถูกต้อง! ต้องมีความยาวน้อยกว่า {$maxlength} ตัวอักษร";
        }
        else if (!@ereg("^[[:alnum:]]([a-zA-Z0-9/.?]{3,14})$", $text)){
            return 'เว้นว่างไม่ได้ ต้องเป็นตัวอักษรภาษาอังกฤษและตัวเลขอารบิกเท่านั้น';
        }
    }

    public function name($string){

        // "^[ก-๙]+[[:space:]]{1}[ก-๙]+$"
        if (!@ereg("[a-zA-Zก-เ]+$", $string)){
            return 'ชื่อไม่ถูกต้อง';
        }
        elseif( preg_match('[\'\/~`\!@#\$%\^&\*\(\)_\-\+=\{\}\[\]\|;:"\<\>,\.\?\\\]', $string) ){
            return 'ไม่สามารถใส่ตัวอักษรพิเศษลงในชื่อได้';
        }
    }

    public function password($data, $arg = 6){
        if (strlen($data) < $arg) return "รหัสผ่านต้องมีความยาว $arg ตัวขึ้นไป";
    }

    public function nameLang($str, $lang = null) {
        switch ($lang) {
            case 'th':
                $pattern = "[ก-๙]+$";
                $lang = "ภาษาไทย";
                break;
            
            default:
                $pattern = "[a-zA-Z0-9]+$";
                $lang = "ภาษาอังกฤษ";
                break;
        }

        if (!@ereg($pattern, $str))
            return "ต้องเป็นตัวอักษร{$lang}เท่านั้น";
    }

    public function digit($data) {
        if (ctype_digit($data) == false)
            return "ต้องเป็นตัวเลขอารบิกเท่านั้น";
    }

    public function characters($str) {
        if (@eregi("[\~\!\`\#\%\^\$\&\*\+-,\;\/\@\{\}\\\'\"\:\<\>\(\)\?]|\]|\[|\||฿", $str))
            return "มีอักขระที่ไม่ถูกต้อง";
    }

    public function phone($str) {
        if (@eregi("^((\([0-9]{3}\) ?)|([0-9]{3}-))?[0-9]{3}-[0-9]{4}$", $str) === false)
            return "ไม่ใช่เบอร์โทรศัพท์ที่ถูกต้อง (ตัวอย่างที่ถูกต้อง 084-363-5952)";
    }

    public function phone_number($str){
        if (@eregi("^((\([0-9]{3}\) ?)|([0-9]{3}))?[0-9]{3}[0-9]{4}$", $str) === false)
            return "ไม่ใช่เบอร์โทรศัพท์ที่ถูกต้อง (ตัวอย่างที่ถูกต้อง 0843635952)";
    }

    function validationURL($url) {
        if (@ereg("^(http://www|www)[.]([a-z,A-Z,0-9]+)([-,_])([a-z,A-Z,0-9]+)[.]([a-z,A-Z]){2,3}[.]?(([a-z,A-Z]){2,3})[/]?[~]?([/,a-z,A-Z,0-9]+)?$", $url) === false)
            return "ไม่ใช่เว็บไซต์ที่ถูกต้อง";
    }

    public function __call($name, $arguments) {
        throw new Exception("$name does not exist inside of: " . __CLASS__);
    }

}