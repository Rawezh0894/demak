<?php
/**
 * Visitor Tracking System
 * Records website visits and generates statistics
 */

// Prevent tracking admin pages and bots
$excluded_paths = ['/pages/admin/', '/admin/', '/login.php'];
$current_path = $_SERVER['REQUEST_URI'] ?? '';

// Skip tracking for admin pages
foreach ($excluded_paths as $path) {
    if (strpos($current_path, $path) !== false) {
        return;
    }
}

// Skip tracking for bots
$user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
$bot_patterns = ['bot', 'crawler', 'spider', 'scraper', 'curl', 'wget', 'python'];
$is_bot = false;
foreach ($bot_patterns as $pattern) {
    if (stripos($user_agent, $pattern) !== false) {
        $is_bot = true;
        break;
    }
}

// Skip tracking bots
if ($is_bot) {
    return;
}

// Get database connection
if (!isset($pdo)) {
    require_once __DIR__ . '/../config/db_conected.php';
}

try {
    // Start session if not started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Get visitor information
    $ip_address = getUserIP();
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $referer = $_SERVER['HTTP_REFERER'] ?? null;
    $page_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    
    // Create unique visitor fingerprint (IP + User Agent hash)
    $visitor_fingerprint = md5($ip_address . $user_agent);
    
    // Use session ID if available, otherwise create one based on fingerprint
    $session_id = session_id() ?: $visitor_fingerprint;
    
    // Detect device type
    $device_type = 'unknown';
    if (preg_match('/mobile|android|iphone|ipad|ipod|blackberry|iemobile|opera mini/i', $user_agent)) {
        if (preg_match('/tablet|ipad|playbook|silk/i', $user_agent)) {
            $device_type = 'tablet';
        } else {
            $device_type = 'mobile';
        }
    } else {
        $device_type = 'desktop';
    }
    
    // Detect browser
    $browser = 'unknown';
    if (preg_match('/MSIE|Trident/i', $user_agent)) {
        $browser = 'Internet Explorer';
    } elseif (preg_match('/Edge/i', $user_agent)) {
        $browser = 'Edge';
    } elseif (preg_match('/Chrome/i', $user_agent)) {
        $browser = 'Chrome';
    } elseif (preg_match('/Firefox/i', $user_agent)) {
        $browser = 'Firefox';
    } elseif (preg_match('/Safari/i', $user_agent)) {
        $browser = 'Safari';
    } elseif (preg_match('/Opera|OPR/i', $user_agent)) {
        $browser = 'Opera';
    }
    
    // Detect OS
    $os = 'unknown';
    if (preg_match('/Windows/i', $user_agent)) {
        $os = 'Windows';
    } elseif (preg_match('/Mac OS|Macintosh/i', $user_agent)) {
        $os = 'macOS';
    } elseif (preg_match('/Linux/i', $user_agent)) {
        $os = 'Linux';
    } elseif (preg_match('/Android/i', $user_agent)) {
        $os = 'Android';
    } elseif (preg_match('/iOS|iPhone|iPad/i', $user_agent)) {
        $os = 'iOS';
    }
    
    $visit_date = date('Y-m-d');
    $visit_time = date('H:i:s');
    $visit_datetime = date('Y-m-d H:i:s');
    
    // Session-based tracking: Check if this is a returning visitor in the same session
    $session_key = 'visitor_tracked_' . $visit_date;
    $session_tracked = isset($_SESSION[$session_key]);
    
    // Time-based session check: If visitor returns within 30 minutes, it's the same session
    $session_timeout = 30 * 60; // 30 minutes in seconds
    $last_visit_time = isset($_SESSION['last_visit_time']) ? $_SESSION['last_visit_time'] : 0;
    $current_time = time();
    
    // Check if visitor has been tracked today and is within session timeout
    if ($session_tracked && ($current_time - $last_visit_time) < $session_timeout) {
        // Same visitor, same session - only count as page view, not unique visitor
        $is_unique = 0;
        $is_new_session = 0;
    } else {
        // Check database for unique visitor (same fingerprint + same day)
        // If visitor fingerprint exists today, it's not unique
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as count 
            FROM website_visitors 
            WHERE ip_address = ? 
            AND user_agent = ? 
            AND visit_date = ?
            AND visit_datetime >= DATE_SUB(NOW(), INTERVAL 30 MINUTE)
        ");
        $stmt->execute([$ip_address, $user_agent, $visit_date]);
        $recent_visits = $stmt->fetchColumn();
        
        if ($recent_visits > 0) {
            // Same visitor within 30 minutes - page view only
            $is_unique = 0;
            $is_new_session = 0;
        } else {
            // Check if this fingerprint visited today at all
            $stmt = $pdo->prepare("
                SELECT COUNT(*) as count 
                FROM website_visitors 
                WHERE ip_address = ? 
                AND user_agent = ? 
                AND visit_date = ?
            ");
            $stmt->execute([$ip_address, $user_agent, $visit_date]);
            $today_visits = $stmt->fetchColumn();
            
            $is_unique = ($today_visits == 0) ? 1 : 0;
            $is_new_session = 1;
        }
        
        // Mark session as tracked for today
        $_SESSION[$session_key] = true;
        $_SESSION['last_visit_time'] = $current_time;
        $_SESSION['visitor_fingerprint'] = $visitor_fingerprint;
    }
    
    // Always insert page view record (for detailed tracking)
    $stmt = $pdo->prepare("
        INSERT INTO website_visitors 
        (ip_address, user_agent, referer, page_url, device_type, browser, os, 
         visit_date, visit_time, visit_datetime, session_id, is_unique)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    $stmt->execute([
        $ip_address,
        $user_agent,
        $referer,
        $page_url,
        $device_type,
        $browser,
        $os,
        $visit_date,
        $visit_time,
        $visit_datetime,
        $session_id,
        $is_unique
    ]);
    
    // Update daily statistics
    // Only increment unique_visitors and device counts if this is a unique visitor
    // Always increment page_views
    $stmt = $pdo->prepare("
        INSERT INTO daily_visitor_stats 
        (visit_date, total_visits, unique_visitors, page_views, desktop_visits, mobile_visits, tablet_visits)
        VALUES (?, 1, ?, 1, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
            page_views = page_views + 1,
            unique_visitors = unique_visitors + ?,
            desktop_visits = desktop_visits + ?,
            mobile_visits = mobile_visits + ?,
            tablet_visits = tablet_visits + ?,
            updated_at = NOW()
    ");
    
    // Device counts should only increment for unique visitors
    $desktop_count = ($is_unique && $device_type == 'desktop') ? 1 : 0;
    $mobile_count = ($is_unique && $device_type == 'mobile') ? 1 : 0;
    $tablet_count = ($is_unique && $device_type == 'tablet') ? 1 : 0;
    
    $stmt->execute([
        $visit_date,
        $is_unique,
        $desktop_count,
        $mobile_count,
        $tablet_count,
        $is_unique, // Only increment unique_visitors if unique
        $desktop_count, // Only increment device counts if unique
        $mobile_count,
        $tablet_count
    ]);
    
} catch (Exception $e) {
    // Silently fail - don't break the website if tracking fails
    error_log("Visitor tracking error: " . $e->getMessage());
}

