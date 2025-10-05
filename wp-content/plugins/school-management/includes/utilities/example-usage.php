<?php
/**
 * Example usage của StudentSessionManager
 * File này demo các cách sử dụng phổ biến của SessionManager
 */

// Include SessionManager (adjust path accordingly)
require_once __DIR__ . '/session-manager.php';

/**
 * Example 1: Basic Login Flow
 */
function example_login_flow()
{
    echo "<h2>Example 1: Basic Login Flow</h2>";

    // Simulate login attempt
    $username = "student001";
    $password = "password123";

    echo "Attempting login with username: $username<br>";

    $result = StudentSessionManager::login($username, $password);

    if ($result['success']) {
        echo "✅ Login successful!<br>";
        echo "Student ID: " . $result['student_id'] . "<br>";
        echo "Student Name: " . $result['student_name'] . "<br>";
    } else {
        echo "❌ Login failed: " . $result['message'] . "<br>";
        if (isset($result['error_code'])) {
            echo "Error Code: " . $result['error_code'] . "<br>";
        }
    }
}

/**
 * Example 2: Check Current Session
 */
function example_check_session()
{
    echo "<h2>Example 2: Check Current Session</h2>";

    $session = StudentSessionManager::checkSession();

    if ($session['logged_in']) {
        echo "✅ Student is logged in<br>";
        echo "Student ID: " . $session['student_id'] . "<br>";
        echo "Student Name: " . $session['student_name'] . "<br>";
    } else {
        echo "❌ No student logged in<br>";
    }
}

/**
 * Example 3: Get Current Student Details
 */
function example_get_current_student()
{
    echo "<h2>Example 3: Get Current Student Details</h2>";

    $student = StudentSessionManager::getCurrentStudent();

    if ($student) {
        echo "✅ Current student details:<br>";
        echo "ID: " . $student['id'] . "<br>";
        echo "Name: " . $student['name'] . "<br>";
        echo "Username: " . $student['username'] . "<br>";
        echo "Post Date: " . $student['post_date'] . "<br>";
        echo "Status: " . $student['post_status'] . "<br>";

        // Show additional meta fields if available
        foreach (['student_email', 'student_phone', 'student_class'] as $field) {
            if (isset($student[$field])) {
                echo ucfirst(str_replace('student_', '', $field)) . ": " . $student[$field] . "<br>";
            }
        }
    } else {
        echo "❌ No student details available<br>";
    }
}

/**
 * Example 4: Check Access to Resource
 */
function example_check_access()
{
    echo "<h2>Example 4: Check Access to Resource</h2>";

    $class_id = 123; // Example class ID

    $hasAccess = StudentSessionManager::hasAccessToResource($class_id, 'class');

    if ($hasAccess) {
        echo "✅ Student has access to class $class_id<br>";
    } else {
        echo "❌ Student does not have access to class $class_id<br>";
    }
}

/**
 * Example 5: Session Statistics (for debugging)
 */
function example_session_stats()
{
    echo "<h2>Example 5: Session Statistics</h2>";

    $stats = StudentSessionManager::getSessionStats();

    echo "<pre>";
    print_r($stats);
    echo "</pre>";
}

/**
 * Example 6: Logout
 */
function example_logout()
{
    echo "<h2>Example 6: Logout</h2>";

    $result = StudentSessionManager::logout();

    if ($result['success']) {
        echo "✅ Logout successful: " . $result['message'] . "<br>";
    } else {
        echo "❌ Logout failed<br>";
    }
}

/**
 * Example 7: Protected Page Simulation
 */
function example_protected_page()
{
    echo "<h2>Example 7: Protected Page</h2>";

    // Check if student is required to login
    $isLoggedIn = StudentSessionManager::requireLogin(); // No redirect in this example

    if ($isLoggedIn) {
        echo "✅ Access granted! This is protected content.<br>";

        $student = StudentSessionManager::getCurrentStudent();
        echo "Welcome back, " . $student['name'] . "!<br>";
    } else {
        echo "❌ Access denied. Please login first.<br>";
    }
}

/**
 * Example 8: Custom Error Handling
 */
function example_error_handling()
{
    echo "<h2>Example 8: Custom Error Handling</h2>";

    // Test with invalid credentials
    $result = StudentSessionManager::login("invalid_user", "wrong_password");

    if (!$result['success']) {
        switch ($result['error_code']) {
            case 'INVALID_INPUT':
                echo "🔴 Please provide both username and password<br>";
                break;
            case 'INVALID_CREDENTIALS':
                echo "🔴 Username or password is incorrect<br>";
                break;
            default:
                echo "🔴 An unknown error occurred<br>";
        }
    }
}

/**
 * Example 9: REST API Integration
 */
function example_rest_api_usage()
{
    echo "<h2>Example 9: REST API Integration</h2>";

    echo "REST API Endpoints:<br>";
    echo "• GET /wp-json/school-management/v1/check-student-session<br>";
    echo "• POST /wp-json/school-management/v1/student-login (username, password)<br>";
    echo "• POST /wp-json/school-management/v1/student-logout<br><br>";

    echo "Example JavaScript usage:<br>";
    echo "<pre>";
    echo "// Check session
fetch('/wp-json/school-management/v1/check-student-session')
  .then(response => response.json())
  .then(data => {
    if (data.logged_in) {
      console.log('Student logged in:', data.student_name);
    }
  });

// Login
fetch('/wp-json/school-management/v1/student-login', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({
    username: 'student001',
    password: 'password123'
  })
})
.then(response => response.json())
.then(data => {
  if (data.success) {
    console.log('Login successful:', data.student_name);
  } else {
    console.error('Login failed:', data.message);
  }
});

// Logout
fetch('/wp-json/school-management/v1/student-logout', {
  method: 'POST'
})
.then(response => response.json())
.then(data => {
  if (data.success) {
    console.log('Logout successful');
  }
});";
    echo "</pre>";
}

// Run examples if this file is accessed directly
if (basename(__FILE__) == basename($_SERVER['SCRIPT_NAME'])) {
    echo "<h1>StudentSessionManager Examples</h1>";

    // Run all examples
    example_login_flow();
    echo "<hr>";

    example_check_session();
    echo "<hr>";

    example_get_current_student();
    echo "<hr>";

    example_check_access();
    echo "<hr>";

    example_session_stats();
    echo "<hr>";

    example_protected_page();
    echo "<hr>";

    example_error_handling();
    echo "<hr>";

    example_rest_api_usage();
    echo "<hr>";

    // Logout at the end to clean up
    example_logout();
}