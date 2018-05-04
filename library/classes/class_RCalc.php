<?php

class RCalc {

    public static function sumArray($array1 = null, $array2 = null, $array3 = null, $array4 = null, $array5 = null) {
        $sum = 0;

        if (is_array($array1)) {
            foreach ($array1 as $a) {
                $sum = $sum + $a;
            }
        }
        if (is_array($array2)) {
            foreach ($array2 as $a) {
                $sum = $sum + $a;
            }
        }
        if (is_array($array3)) {
            foreach ($array3 as $a) {
                $sum = $sum + $a;
            }
        }
        if (is_array($array4)) {
            foreach ($array4 as $a) {
                $sum = $sum + $a;
            }
        }
        if (is_array($array5)) {
            foreach ($array5 as $a) {
                $sum = $sum + $a;
            }
        }

        return $sum;
    }

    public static function avgArray($array = null) {
        $sum = 0;
        $antal = 0;
        if (is_array($array)) {
            foreach ($array as $a) {
                $sum = $sum + $a;
                $antal++;
            }
        }

        if ($antal > 0) {
            return $sum / $antal;
        } else {
            return 0;
        }
    }

}