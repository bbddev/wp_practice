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

    register_rest_route('school-management/v1', '/classes/(?P<school_id>\d+)', array(
        'methods' => 'GET',
        'callback' => 'get_classes_by_school',
        'args' => array(
            'school_id' => array(
                'validate_callback' => function($param, $request, $key) {
                    return is_numeric($param);
                }
            ),
        ),
    ));

    register_rest_route('school-management/v1', '/entities/(?P<class_id>\d+)', array(
        'methods' => 'GET',
        'callback' => 'get_entities_by_class',
        'args' => array(
            'class_id' => array(
                'validate_callback' => function($param, $request, $key) {
                    return is_numeric($param);
                }
            ),
        ),
    ));
}

/**
 * Get schools data for REST API
 */
function get_schools()
{
    // Sample data - replace with actual data retrieval logic
   $school_list = get_posts(array(
        'post_type' => 'school',
        'numberposts' => -1,
        'post_status' => 'publish'
    ));
    return $school_list;
}

/**
 * Get classes by school for REST API
 */
function get_classes_by_school($request)
{
    $school_id = $request['school_id'];
    $school_post = get_post($school_id);
    
    if (!$school_post || $school_post->post_type !== 'school') {
        return new WP_Error('invalid_school', 'Invalid school ID', array('status' => 404));
    }
    
    $school_name = $school_post->post_title;
    
    $classes = get_posts(array(
        'post_type' => 'class',
        'numberposts' => -1,
        'post_status' => 'publish',
        'meta_query' => array(
            array(
                'key' => 'Thuộc trường',
                'value' => $school_name,
                'compare' => '='
            )
        )
    ));
    
    return $classes;
}

/**
 * Get entities by class for REST API
 */
function get_entities_by_class($request)
{
    $class_id = $request['class_id'];
    $class_post = get_post($class_id);
    
    if (!$class_post || $class_post->post_type !== 'class') {
        return new WP_Error('invalid_class', 'Invalid class ID', array('status' => 404));
    }
    
    $class_name = $class_post->post_title;
    
    $entities = get_posts(array(
        'post_type' => 'entity',
        'numberposts' => -1,
        'post_status' => 'publish',
        'meta_query' => array(
            array(
                'key' => 'Thuộc lớp',
                'value' => $class_name,
                'compare' => '='
            )
        )
    ));
    
    // Enhance entities with meta data
    $enhanced_entities = array();
    foreach ($entities as $entity) {
        $enhanced_entity = array(
            'id' => $entity->ID,
            'title' => $entity->post_title,
            'content' => $entity->post_content,
            'link' => get_post_meta($entity->ID, 'Link khi click', true),
            'image' => get_post_meta($entity->ID, 'Hình', true)
        );
        $enhanced_entities[] = $enhanced_entity;
    }
    
    return $enhanced_entities;
}