<?php
function is_valid_username($username)
{
    $pattern = '/^[\w\s]{1,32}$/';
    if (preg_match($pattern, $username))
        return true;
    return false;
}

function is_valid_email($email)
{
    $pattern = '/^([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}){1,50}$/';
    if (preg_match($pattern, $email))
        return true;
    return false;
}

function is_valid_password($password)
{
    $pattern = '/^[\w\s!@#$%^&*?.]{12,255}$/';
    if (preg_match($pattern, $password))
        return true;
    return false;
}