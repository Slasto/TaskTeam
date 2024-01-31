<?php
function is_valid_activity_title($title)
{
    $pattern = '/^[\w\s0-9]{1,255}$/';
    if (preg_match($pattern, $title))
        return true;
    return false;
}

function is_valid_activity_description($desc)
{
    $pattern = '/^[\p{Latin}\s!@#$\'%^*\-_|0-9]{0,255}$/u';
    if (preg_match($pattern, $desc))
        return true;
    return false;
}

function is_valid_activity_expire_date($date)
{
    if ($date === "" || $date == null)
        return true;
    $currentDate = new DateTime('now', new DateTimeZone('Europe/Rome'));
    $inputDate = DateTime::createFromFormat('Y-m-d', $date, new DateTimeZone('Europe/Rome'));
    return $inputDate >= $currentDate;
}

function is_valid_activity_status($stato){
    if ( $stato!=="Da fare"  || $stato!=="In corso" ||$stato!=="Fatto") {
        return false;
    }
    return true;
}