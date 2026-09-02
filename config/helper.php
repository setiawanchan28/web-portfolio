<?php
/**
 * Global Helper & Security Functions
 */

require_once __DIR__ . '/config.php';

/**
 * Sanitise string output to prevent XSS
 */
function sanitize($data) {
    if (is_array($data)) {
        foreach ($data as $key => $value) {
            $data[$key] = sanitize($value);
        }
        return $data;
    }
    return htmlspecialchars(trim((string)$data), ENT_QUOTES, 'UTF-8');
}

/**
 * Generate clean URL Slugs
 */
function create_slug($string) {
    $string = strtolower(trim($string));
    $string = preg_replace('/[^a-z0-9-]/', '-', $string);
    $string = preg_replace('/-+/', '-', $string);
    return rtrim($string, '-');
}

/**
 * Check if user is logged in as Admin
 */
function is_logged_in() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Require authentication guard
 */
function require_login() {
    if (!is_logged_in()) {
        header("Location: " . ADMIN_URL . "login.php");
        exit;
    }
}

/**
 * Set flash alert message
 */
function set_flash($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type, // success, danger, warning, info
        'message' => $message
    ];
}

/**
 * Display flash alert message
 */
function display_flash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        echo '<div class="alert alert-' . sanitize($flash['type']) . ' alert-dismissible fade show" role="alert">
                ' . sanitize($flash['message']) . '
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>';
    }
}

/**
 * Secure file upload handler
 */
function upload_file($file_key, $subfolder = '', $allowed_ext = ['jpg', 'jpeg', 'png', 'webp']) {
    if (!isset($_FILES[$file_key]) || $_FILES[$file_key]['error'] !== UPLOAD_ERR_OK) {
        return false;
    }

    $file = $_FILES[$file_key];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed_ext)) {
        return false;
    }

    $target_dir = UPLOAD_PATH . trim($subfolder, '/') . '/';
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }

    $new_name = uniqid('img_', true) . '.' . $ext;
    $target_file = $target_dir . $new_name;

    if (move_uploaded_file($file['tmp_name'], $target_file)) {
        return (empty($subfolder) ? '' : trim($subfolder, '/') . '/') . $new_name;
    }

    return false;
}

/**
 * Handle base64 cropped image upload from Cropper.js
 */
function upload_base64_image($base64_string, $subfolder = '') {
    if (empty($base64_string)) return false;

    if (preg_match('/^data:image\/(\w+);base64,/', $base64_string, $type)) {
        $data = substr($base64_string, strpos($base64_string, ',') + 1);
        $type = strtolower($type[1]);

        if (!in_array($type, ['jpg', 'jpeg', 'png', 'webp'])) {
            $type = 'png';
        }

        $data = base64_decode($data);
        if ($data === false) return false;
    } else {
        return false;
    }

    $target_dir = UPLOAD_PATH . trim($subfolder, '/') . '/';
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }

    $new_name = uniqid('crop_', true) . '.' . $type;
    $target_file = $target_dir . $new_name;

    if (file_put_contents($target_file, $data)) {
        return (empty($subfolder) ? '' : trim($subfolder, '/') . '/') . $new_name;
    }

    return false;
}

/**
 * Send notification message to Telegram Bot
 */
function send_telegram_notification($name, $email, $phone, $subject, $message, $settings = null) {
    if (!$settings) {
        return false;
    }

    $bot_token = trim($settings['telegram_bot_token'] ?? '');
    $chat_id = trim($settings['telegram_chat_id'] ?? '');

    if (empty($bot_token) || empty($chat_id)) {
        return false;
    }

    $phone_text = !empty($phone) ? $phone : '-';
    $subject_text = !empty($subject) ? $subject : '(Tanpa Subjek)';

    $text = "📩 *PESAN BARU DARI PORTOFOLIO!*\n\n";
    $text .= "👤 *Nama:* " . $name . "\n";
    $text .= "📧 *Email:* " . $email . "\n";
    $text .= "📱 *No. HP / WA:* " . $phone_text . "\n";
    $text .= "📋 *Subjek:* " . $subject_text . "\n\n";
    $text .= "💬 *Pesan:*\n" . $message;

    $url = "https://api.telegram.org/bot" . $bot_token . "/sendMessage";
    $data = [
        'chat_id' => $chat_id,
        'text' => $text,
        'parse_mode' => 'Markdown'
    ];

    $options = [
        'http' => [
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data),
            'timeout' => 5
        ]
    ];

    $context  = stream_context_create($options);
    @file_get_contents($url, false, $context);
    return true;
}

