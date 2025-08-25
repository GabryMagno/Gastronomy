<?php
class Sanitizer {

    private const ALLOWED_TAGS ='<em><strong><ul><li>';
    
    public static function SanitizeUsername($value) {
        $value = trim($value);
        $value = strip_tags($value);
        $value = str_replace(" ","",$value);
        return $value;
    }
    public static function SanitizeUserInput($value) {
        $value = trim($value);
        $value = strip_tags($value);
        $value = str_replace(" ","",$value);
        $value = htmlentities($value);
        return $value;
    }

    public static function SanitizeGenericInput($value) {
        $value = trim($value);
        $value = strip_tags($value);
        $value = htmlentities($value);
        return $value;
    }
    
    public static function SanitizeText($value) {
        $value = trim($value);
        $value = strip_tags($value, Sanitizer::ALLOWED_TAGS);
        return $value;
    }

    public static function IntFilter($value) {
        $res = filter_var($value, FILTER_VALIDATE_INT,
           array('options' => array('min_range' => 1)));
        if(!$res) {
            return 1; // default value
        }
        return $res;
    }

    public static function FloatFilter($value) {
        $res = filter_var($value, FILTER_VALIDATE_FLOAT,
           array('options' => array('min_range' => 1)));
        if(!$res) {
            return 1; // default value
        }
        return $res;
    }

    public static function SanitizeEmail($value) {
        $value = trim($value);
        $value = filter_var($value, FILTER_SANITIZE_EMAIL);
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return '';
        }
        return $value;
    }
}
?>