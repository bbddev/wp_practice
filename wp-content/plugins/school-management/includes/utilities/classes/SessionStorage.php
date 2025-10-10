<?php

if (!defined('ABSPATH')) {
    exit;
}

class SessionStorage
{
    public static function generateSessionToken()
    {
        return wp_generate_password(32, false) . '_' . time();
    }

    public static function saveActiveSession($student_id, $session_token, $user_ip, $device_info, $user_agent)
    {
        $active_sessions_key = 'student_active_sessions_' . $student_id;

        $session_data = array(
            'token' => $session_token,
            'ip' => $user_ip,
            'user_agent' => $user_agent,
            'browser' => $device_info['browser'],
            'os' => $device_info['os'],
            'platform' => $device_info['platform'],
            'login_time' => time(),
            'last_activity' => time()
        );

        $active_sessions = array($session_token => $session_data);
        update_option($active_sessions_key, $active_sessions);
    }

    public static function invalidateOtherSessions($student_id, $current_token)
    {
        $active_sessions_key = 'student_active_sessions_' . $student_id;
        $active_sessions = get_option($active_sessions_key, array());

        if (!empty($active_sessions)) {
            delete_option($active_sessions_key);
        }
    }

    public static function isValidSession($student_id, $session_token)
    {
        if (empty($session_token)) {
            return false;
        }

        $active_sessions_key = 'student_active_sessions_' . $student_id;
        $active_sessions = get_option($active_sessions_key, array());

        return isset($active_sessions[$session_token]);
    }

    public static function updateSessionActivity($student_id, $session_token)
    {
        $active_sessions_key = 'student_active_sessions_' . $student_id;
        $active_sessions = get_option($active_sessions_key, array());

        if (isset($active_sessions[$session_token])) {
            $active_sessions[$session_token]['last_activity'] = time();
            update_option($active_sessions_key, $active_sessions);
        }
    }

    public static function removeSessionFromDatabase($student_id)
    {
        $active_sessions_key = 'student_active_sessions_' . $student_id;
        delete_option($active_sessions_key);
    }

    public static function getSessionData($student_id, $session_token)
    {
        $active_sessions_key = 'student_active_sessions_' . $student_id;
        $active_sessions = get_option($active_sessions_key, array());

        return isset($active_sessions[$session_token]) ? $active_sessions[$session_token] : null;
    }
}