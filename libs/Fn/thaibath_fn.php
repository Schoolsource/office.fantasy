<?php
/**
 * Number Thai class
 *
 * @package Number
 * @since 1.0
 * @author Vee W.
 * @license http://opensource.org/licenses/MIT
 *
 */
namespace Rundiz\Number;
/**
 * convert number such as -10, 0.034, 0.50, 1.34, 1000000, or more to text in Thai language.
 * 
 * @author Vee W.
 * @license MIT.
 * @link http://rundiz.com author's home page.
 * @uses to convert number to text, make a call to from this class to convertNumber() method. Example: $number_thai->convertNumber('109.99');
 */
class Thaibath_Fn 
{
    public $number = array('ศูนย์', 'หนึ่ง', 'สอง', 'สาม', 'สี่','ห้า', 'หก', 'เจ็ด', 'แปด', 'เก้า');
    public $number_scale = array('', 'สิบ', 'ร้อย', 'พัน', 'หมื่น', 'แสน', 'ล้าน');
    
    // arabic and thai number
    public $arabic_number = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
    public $thai_number = array('๐', '๑', '๒', '๓', '๔', '๕', '๖', '๗', '๘', '๙');
    /**
     * convert from arabic number to thai number.
     * 
     * @param string $input string that contain number to convert
     * @return string return string with converted number.
     */
    public function arabicToThaiNumber($input)
    {
        $input = strval($input);
        
        return str_replace($this->arabic_number, $this->thai_number, $input);
    }// arabicToThaiNumber
    /**
     * convert Thai Baht number to text.
     * 
     * @param number $num input the money number. negative or positive.
     * @param boolean $display_net display net (ถ้วน). true to display, false to not display.
     * @return string return converted number to Thai Baht and Satang string.
     */
    public function convertBaht($num, $display_net = true)
    {
        // make input as string.
        $num = strval($num);
        if (strpos($num, '.') !== false) {
            list($num, $dec) = explode('.', $num);
        } else {
            $dec = 0;
        }
        $output = '';
        if ($num{0} == '-') {
            $output .= 'ลบ';
            $num = ltrim($num, '-');
        } elseif ($num{0} == '+') {
            $output .= 'บวก';
            $num = ltrim($num, '+');
        }
        if ($num == '0') {
            $output .= 'ศูนย์';
        } else {
            $output .= $this->convertNumberWithScale($num);
        }
        $output .= 'บาท';
        if ($dec > 0) {
            // if there is decimal (.)
            $dec_str = '';
            if (strlen($dec) == 1) {
                $dec .= '0';
            }
            // convert number normally for decimal.
            $dec_str = $this->convertNumberWithScale($dec);
            if ($dec_str != null) {
                $output .= $dec_str . 'สตางค์';
            }
        }
        
        if ($display_net === true && (!isset($dec_str) || (isset($dec_str) && $dec_str == null))) {
            $output .= 'ถ้วน';
        }
        
        unset($dec, $dec_str);
        return $output;
    }// convertBaht
    /**
     * match number to text.
     * 
     * @param number $digit only one digit per request.
     * @return string return translated number for each digit requested.
     */
    public function convertDirectNum($digit)
    {
        if (isset($this->number[$digit])) {
            return $this->number[$digit];
        }
        return $digit;
    }// convertDirectNum
    /**
     * convert the number (and with dot).
     * 
     * @param number $num number integer or decimal. negative or positive.
     * @return string translated number to text in Thai language.
     */
    public function convertNumber($num)
    {
        // make input as string.
        $num = strval($num);
        if (strpos($num, '.') !== false) {
            list($num, $dec) = explode('.', $num);
        } else {
            $dec = 0;
        }
        $output = '';
        if ($num{0} == '-') {
            $output .= 'ลบ';
            $num = ltrim($num, '-');
        } elseif ($num{0} == '+') {
            $output .= 'บวก';
            $num = ltrim($num, '+');
        }
        if ($num == '0') {
            $output .= 'ศูนย์';
        } else {
            $output .= $this->convertNumberWithScale($num);
        }
        if ($dec > 0) {
            // if there is decimal (.)
            $output .= 'จุด';
            if ($dec{0} == '0') {
                // first digit after dot is zero. read number directly
                for ($i = 0; $i < strlen($dec); $i++) {
                    $output .= $this->convertDirectNum($dec{$i});
                }
            } else {
                // read number normally.
                $output .= $this->convertNumberWithScale($dec);
            }
        }
        return $output;
    }// convertNumber
    /**
     * convert the number to text with scale. (ten, hundred, thousand, ...) in Thai language.
     * 
     * @param string $digits number only. no negative or positive sign. no dot.
     * @return string
     */
    private function convertNumberWithScale($digits)
    {
        $digits = ltrim($digits, '0');// remove zero leading. example: 0212, 00213
        $length_digit = strlen($digits);
        $count = 1;
        $pos = 0;// หลักเลข 1=หน่วย, 2=สิบ, 3=ร้อย, ...
        $output = '';
        $tmp_output = '';
        $tmp_output_scale = '';
        for($i=$length_digit-1; $i > -1 ; --$i) {
            if ($pos == 7) {
                $pos = 1;
            }
            $tmp_output = $this->convertDirectNum($digits{$i});
            if ($pos >= 0 && $digits{$i} == 0 && $length_digit > $count) {
                // หากหลักมากกว่าหน่วย และตัวเลขที่เจอเป็นศูนย์ ไม่ให้แสดงตัวอักษรคำว่าศูนย์ เพราะไม่อ่านสิบศูนย์ หรือ ร้อยศูนย์ศูนย์
                $tmp_output = '';
            } elseif ($pos == 1 && $digits{$i} == 1) {
                // หากเป็นหลักสิบ และตัวเลขที่เจอเป็น 1 ไม่ให้แสดงตัวอักษร คำว่า หนึ่ง เนื่องจากเราจะไม่อ่านว่า หนึ่งสิบ
                $tmp_output = '';
            } elseif ($pos == 1 && $digits{$i} == 2) {
                // หน่วยสิบ เลขคือ 2
                $tmp_output = 'ยี่';
            } elseif (($pos == 0 || $pos == 6) && $digits{$i} == 1 && $length_digit > $count) {
                // หากเป็นหลักหน่วย หรือหลักล้าน และตัวเลขที่พบคือ 1 และยังมีหลักที่มากกว่าหลักหน่วยปัจจุบัน ให้แสดงเป็น เอ็ด แทน หนึ่ง
                $tmp_output = 'เอ็ด';
            }
            if (isset($this->number_scale[$pos])) {
                // generate number scale (สิบ ร้อย พัน ...)
                $tmp_output_scale = $this->number_scale[$pos];
            }
            if ($digits{$i} == 0 && $pos != 6) {
                // ถ้าตัวเลขที่พบเป็น 0 และไม่ใช่หลักล้าน ไม่ให้แสดงอักษรของหลัก
                $tmp_output_scale = '';
            }
            $output = $tmp_output . $tmp_output_scale . $output;
            $count++;
            $pos++;
            $tmp_output = '';
            $tmp_output_scale = '';
        }
        unset($count, $i, $length_digit, $pos, $tmp_output, $tmp_output_scale);
        return $output;
    }// convertNumberWithScale
    /**
     * convert from thai number to arabic number.
     * 
     * @param string $input input string that contain number to convert.
     * @return string return string with converted number
     */
    public function thaiToArabicNumber($input)
    {
        $input = strval($input);
        
        return str_replace($this->thai_number, $this->arabic_number, $input);
    }// thaiToArabicNumber
}