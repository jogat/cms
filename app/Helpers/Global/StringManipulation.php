<?php

function valid_email($email) {
    return preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $email);
}

/**
 * Pad string with leading zeros.
 *
 * @param string|int    $val
 * @param int           $length     The default is: 15
 *
 * @return string
 */
function pad($val, $length=15){
    return sprintf("%'.0{$length}d", $val);
}

function unPad($val){
    if(is_array($val)){
        $rtn = [];
        foreach($val as $key => $data){
            $rtn[unPad($key)] = unPad($data);
        }
        return $rtn;
    }
    return ltrim($val, 0);
}

function unPad_keys($array){
    $rtn = [];
    foreach($array as $key => $val){
        $rtn[unPad($key)] = $val;
    }
    return $rtn;
}

function unPad_values($array){
    $rtn = [];
    foreach($array as $key => $val){
        $rtn[$key] = unPad($key);
    }
    return $rtn;
}

function convert_from_latin1_to_utf8_recursively($dat)
{
    if(is_string($dat)) {
        return utf8_encode($dat);
    }

    if(is_array($dat)) {
        $ret = [];
        foreach ($dat as $i => $d) $ret[ $i ] = convert_from_latin1_to_utf8_recursively($d);
        return $ret;
    }

    if(is_object($dat)) {
        foreach ($dat as $i => $d) $dat->$i = convert_from_latin1_to_utf8_recursively($d);
        return $dat;
    }

    return $dat;

}

function format_sql_date($value){
    if($time = strtotime($value)){
        return date('Y-m-d', $time);
    }
    return false;
}
function format_sql_timestamp($value=false){
    if($value === false) {
        return date('Y-m-d H:i:s');
    }
    if(!empty($value) && $time = strtotime($value)){
        return date('Y-m-d H:i:s', $time);
    }
    return false;
}
function format_human_date($value){
    if($time = strtotime($value)){
        return date('F j, Y', $time);
    }
    return false;
}
function format_human_timestamp($value=false){
    if($value === false) {
        return date('F j, Y, g:i a');
    }
    if(!empty($value) && $time = strtotime($value)){
        return date('F j, Y, g:i a', $time);
    }
    return false;
}

function is_blank($value) {
    return empty($value) && !is_numeric($value);
}

function format_link($link){
    $rtn = [
        'type' => false,
        'url' => false,
    ];
    if(!empty($link)){
        $rtn['url'] = $link;
        if(strpos($link, '/') === 0){
            $rtn['type'] = 'internal';
        } else {
            $rtn['type'] = 'external';
        }
    }
    return $rtn;
}

/**
 * @param $value (Y or N)
 *
 * @return int|null Returns NULL if not set, 1 if 'Y' or 'y', 0 if anything else.
 */
function yn_to_int($value){
    if(!isset($value)){
        return NULL;
    }
    if($value==='Y' || $value==='y'){
        return 1;
    }
    return 0;
}

function get_human_time_between($start, $end) {
    $diff = $end - $start;
    $sec = (int)$diff;
    $micro = $diff - $sec;
    return strftime('%T', mktime(0, 0, $sec)) . str_replace('0.', '.', sprintf('%.3f', $micro));
}

function money($amount,$raw=false){
    setlocale(LC_MONETARY, 'en_US.UTF-8');
    if($raw){
        return money_format('%i', $amount);
    }
    return money_format('%.2n', $amount);
}

if(!function_exists('money_format')){
    function money_format($format, $number){
        $regex  = '/%((?:[\^!\-]|\+|\(|\=.)*)([0-9]+)?'.
            '(?:#([0-9]+))?(?:\.([0-9]+))?([in%])/';
        if (setlocale(LC_MONETARY, 0) == 'C') {
            setlocale(LC_MONETARY, '');
        }
        $locale = localeconv();
        preg_match_all($regex, $format, $matches, PREG_SET_ORDER);
        foreach ($matches as $fmatch) {
            $value = floatval($number);
            $flags = array(
                'fillchar'  => preg_match('/\=(.)/', $fmatch[1], $match) ?
                    $match[1] : ' ',
                'nogroup'   => preg_match('/\^/', $fmatch[1]) > 0,
                'usesignal' => preg_match('/\+|\(/', $fmatch[1], $match) ?
                    $match[0] : '+',
                'nosimbol'  => preg_match('/\!/', $fmatch[1]) > 0,
                'isleft'    => preg_match('/\-/', $fmatch[1]) > 0
            );
            $width      = trim($fmatch[2]) ? (int)$fmatch[2] : 0;
            $left       = trim($fmatch[3]) ? (int)$fmatch[3] : 0;
            $right      = trim($fmatch[4]) ? (int)$fmatch[4] : $locale['int_frac_digits'];
            $conversion = $fmatch[5];

            $positive = true;
            if ($value < 0) {
                $positive = false;
                $value  *= -1;
            }
            $letter = $positive ? 'p' : 'n';

            $prefix = $suffix = $cprefix = $csuffix = $signal = '';

            $signal = $positive ? $locale['positive_sign'] : $locale['negative_sign'];
            switch (true) {
                case $locale["{$letter}_sign_posn"] == 1 && $flags['usesignal'] == '+':
                    $prefix = $signal;
                    break;
                case $locale["{$letter}_sign_posn"] == 2 && $flags['usesignal'] == '+':
                    $suffix = $signal;
                    break;
                case $locale["{$letter}_sign_posn"] == 3 && $flags['usesignal'] == '+':
                    $cprefix = $signal;
                    break;
                case $locale["{$letter}_sign_posn"] == 4 && $flags['usesignal'] == '+':
                    $csuffix = $signal;
                    break;
                case $flags['usesignal'] == '(':
                case $locale["{$letter}_sign_posn"] == 0:
                    $prefix = '(';
                    $suffix = ')';
                    break;
            }
            if (!$flags['nosimbol']) {
                $currency = $cprefix .
                    ($conversion == 'i' ? $locale['int_curr_symbol'] : $locale['currency_symbol']) .
                    $csuffix;
            } else {
                $currency = '';
            }
            $space  = $locale["{$letter}_sep_by_space"] ? ' ' : '';

            $value = number_format($value, $right, $locale['mon_decimal_point'],
                $flags['nogroup'] ? '' : $locale['mon_thousands_sep']);
            $value = @explode($locale['mon_decimal_point'], $value);

            $n = strlen($prefix) + strlen($currency) + strlen($value[0]);
            if ($left > 0 && $left > $n) {
                $value[0] = str_repeat($flags['fillchar'], $left - $n) . $value[0];
            }
            $value = implode($locale['mon_decimal_point'], $value);
            if ($locale["{$letter}_cs_precedes"]) {
                $value = $prefix . $currency . $space . $value . $suffix;
            } else {
                $value = $prefix . $value . $space . $currency . $suffix;
            }
            if ($width > 0) {
                $value = str_pad($value, $width, $flags['fillchar'], $flags['isleft'] ?
                    STR_PAD_RIGHT : STR_PAD_LEFT);
            }

            $format = str_replace($fmatch[0], $value, $format);
        }
        return $format;
    }
}

function format_order_type($orderType){
    switch($orderType){
        case 'OB':
        case 'OL':
        case 'OP':
            return [
                'text'=>'Online Order',
                'online'=>true,
                'code' => $orderType
            ];
        case 'ST':
            return [
                'text'=>'Standard Order',
                'online'=>false,
                'code' => $orderType
            ];
        case 'SV':
            return [
                'text'=>'Service/Installation',
                'online'=>false,
                'code' => $orderType
            ];
        default:
            return [
                'text'=>'Unknown',
                'online'=>false,
                'code' => $orderType
            ];
    }
}

function format_order_status($statusNumber){
    switch($statusNumber){
        case '0': return    ['code'=>$statusNumber,'test'=>'Order In Process'];
        case '1': return    ['code'=>$statusNumber,'text'=>'Open Order'];
        case '2': return    ['code'=>$statusNumber,'text'=>'Open Backorder'];
        case '3': return    ['code'=>$statusNumber,'text'=>'Released Backorder'];
        case '4': return    ['code'=>$statusNumber,'text'=>'Warehouse'];
        case '8': return    ['code'=>$statusNumber,'text'=>'Released for Invoice'];
        case '9': return    ['code'=>$statusNumber,'text'=>'Completed'];
        case '*': return    ['code'=>$statusNumber,'text'=>'Aborted'];
        case '\\': return   ['code'=>$statusNumber,'text'=>'Cancelled'];
        case 'F': return    ['code'=>$statusNumber,'text'=>'Forwarded'];
        case 'S': return    ['code'=>$statusNumber,'text'=>'Open Order Pending'];
        default: return     ['code'=>$statusNumber,'text'=>'Unknown Status'];
    }
}
