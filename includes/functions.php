<?php
session_start();
require_once 'database.php';

// User Authentication Functions
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isLandlord() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'landlord';
}

function isTenant() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'tenant';
}

// Image Handling Functions
function uploadImage($file) {
    if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
        return false;
    }
    
    $target_dir = $_SERVER['DOCUMENT_ROOT'] . '/renttact/' . UPLOAD_PATH;
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $file_name = uniqid() . '.' . $file_extension;
    $target_file = $target_dir . $file_name;
    
    if (!in_array($file['type'], ALLOWED_IMAGE_TYPES)) {
        return false;
    }
    
    if ($file['size'] > MAX_IMAGE_SIZE) {
        return false;
    }
    
    if (move_uploaded_file($file['tmp_name'], $target_file)) {
        return UPLOAD_PATH . $file_name; // Return relative path for database storage
    }
    
    return false;
}

// Utility Functions
function redirect($path) {
    header("Location: $path");
    exit();
}

function sanitize($input) {
    return htmlspecialchars(strip_tags($input));
}
?>
