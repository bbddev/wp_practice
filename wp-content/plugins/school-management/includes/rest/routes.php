<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Register REST API routes
 */
function register_school_management_routes()
{
    register_rest_route('school-management/v1', '/schools', array(
        'methods' => 'GET',
        'callback' => 'get_schools',
    ));
}

/**
 * Get schools data for REST API
 */
function get_schools()
{
    // Sample data - replace with actual data retrieval logic
    $schools = array(
        array('id' => 1, 'name' => 'School A'),
        array('id' => 2, 'name' => 'School B'),
    );
    return $schools;
}