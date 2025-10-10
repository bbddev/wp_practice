<?php

if (!defined('ABSPATH')) {
    exit;
}

interface SessionInterface
{
    public static function checkSession();

    public static function login($username, $password);

    public static function logout();

    public static function getCurrentStudent();
}