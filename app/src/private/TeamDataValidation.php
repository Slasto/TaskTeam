<?php
function is_valid_team_code($code)
{
    $pattern = '/^[a-zA-Z0-9!@#$%^*_|]{12}$/';
    if (preg_match($pattern, $code))
        return true;
    return false;
}

function is_valid_team_name($code)
{
    $pattern = '/^[\w\s!@#$%^*_|]{1,32}$/';
    if (preg_match($pattern, $code))
        return true;
    return false;
}

function is_valid_team_description($code)
{
    $pattern = '/^[a-zA-Z0-9!@#$%^*_|]{0,255}$/';
    if (preg_match($pattern, $code))
        return true;
    return false;
}