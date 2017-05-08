<?php
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Created by PhpStorm.
 * User: Waleed
 * Date: 5/5/2017
 * Time: 6:21 PM
 */
/**
 * Used to dump and die our code
 * @param null $var
 */
function dd($var = null) {
    dump($var);die;
}

/**
 * Will return readable format for the current time
 * @return DateTime
 */
function getTimeNow() {
    return date_create_from_format('Y-m-d H:i:s', date_format(date_create(), 'Y-m-d H:i:s'));
}

/**
 * Used to check if the array contain this value or not if yes will return the key of this value
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

/**
 * This function return total count of items has been added into the shopping cart
 * @return int
 */
function getTotalCountOfItems() {
    $session = new Session();
    return ($session->has('myCart')) ? array_sum($session->get('myCart')['count']) : 0;
}

/**
 * This function return associative array the key is productId and the value contain the count of times this product is requested from the current user
 * @param array $getResult
 * @param array $productId
 * @param array $countOfItems
 * @return array
 */
function getCurrentCart(Array $getResult, Array $productId, Array $countOfItems) {
    for ($i = 0, $len = count($getResult); $i < $len; $i++) {
        $arr[$getResult[$i]->getId()] = $countOfItems[getKeyOfArray($productId, $getResult[$i]->getId())];
    }
    return (isset($arr)) ? $arr : null;
}

/**
 * This function used a buildin function to re-index all keys in array after used unset
 * @param $array
 * @return array
 */
function getNewArrayAfterUnset($array) {
    return array_values($array);
}