<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Session Interface
 * Định nghĩa các phương thức cần thiết cho quản lý session
 */
interface SessionInterface
{
    /**
     * Kiểm tra trạng thái session hiện tại
     * 
     * @return array Thông tin session
     */
    public static function checkSession();

    /**
     * Đăng nhập user
     * 
     * @param string $username Username
     * @param string $password Password
     * @return array Kết quả đăng nhập
     */
    public static function login($username, $password);

    /**
     * Đăng xuất user
     * 
     * @return array Kết quả đăng xuất
     */
    public static function logout();

    /**
     * Lấy thông tin user hiện tại
     * 
     * @return array|null Thông tin user
     */
    public static function getCurrentStudent();
}