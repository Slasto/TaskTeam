<?php
function is_valid_team_code($code)
{
    $pattern = '/^[a-zA-Z0-9!@#$%^*_|]{12}$/';
    if (preg_match($pattern, $code))
        return true;
    return false;
}

function is_valid_team_name($name)
{
    $pattern = '/^[\w\s\-!@#$%^*_|]{1,32}$/';
    if (preg_match($pattern, $name))
        return true;
    return false;
}

function is_valid_team_description($desc)
{

    $pattern = "/[\\x{00C0}-\\x{017F}a-zA-Z\\s!@#$%^*\\-_|0-9]{0,255}/u";

    if (strlen($desc) <= 255 && preg_match($pattern, $desc))
        return true;
    return false;
}
