<?php

// Unsets key if there is an empty value
if(!function_exists('empty_array_setter')){

    function empty_array_setter($playerlist){
        foreach ($playerlist as $key => $value) {
            if (empty($value)) {
            unset($playerlist[$key]);
            }
        }
        return $playerlist;
        // if (empty($playerlist)) {
        // //empty array
        // }
    }

}

// Compares 2 values if they are not empty
if(!function_exists('compare_not_empty')){

    function compare_not_empty($str1, $str2){
        if(empty($str1) && empty($str2)){
            return false;
        } else if($str1 == $str2){
            return true;
        } else {
            return false;
        }
    }

}

?>