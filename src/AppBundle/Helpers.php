<?php
/**
 * Created by PhpStorm.
 * User: Waleed
 * Date: 5/5/2017
 * Time: 6:21 PM
 */
function dd($var = null) {
    dump($var);die;
}

/**
 * @return DateTime
 */
function getTimeNow() {
    return date_create_from_format('Y-m-d H:i:s', date_format(date_create(), 'Y-m-d H:i:s'));
}

/**
 * @param $array
 * @param $value
 * @return int
 */
function getKeyOfArray($array, $value) {
    for ($i = 0, $len = count($array); $i < $len; $i++) {
        if ($array[$i] == $value) {
            return $i;
        }
    }
}