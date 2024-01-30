<?php
function is_valid_activity_title($title)
{
    $pattern = '/^[\w\s0-9]{1,32}$/';
    if (preg_match($pattern, $title))
        return true;
    return false;
}

function is_valid_activity_description($desc)
{
    $pattern = "/[\\x{00C0}-\\x{017F}a-zA-Z\\s!@#$%^*\\-_|0-9]{0,255}/u";
    if (preg_match($pattern, $desc))
        return true;
    return false;
}

function is_valid_activity_expire_date($date)
{
    if ($date === "")
        return true;
    $currentDate = new DateTime('now', new DateTimeZone('Europe/Rome'));
    $inputDate = DateTime::createFromFormat('Y-m-d', $date, new DateTimeZone('Europe/Rome'));
    return $inputDate >= $currentDate;
}

function is_valid_activity_done_date($doneDate, $expireDate)
{
    $doneDate = DateTime::createFromFormat('Y-m-d', $doneDate, new DateTimeZone('Europe/Rome'));
    $expireDate = DateTime::createFromFormat('Y-m-d', $expireDate, new DateTimeZone('Europe/Rome'));

    return $expireDate >= $doneDate;
}
