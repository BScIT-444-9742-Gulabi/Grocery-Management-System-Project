<?php
include '../config/database.php';

// Helper functions for editable content
function getPageContent($page_name, $section_key, $default = '') {
    global $conn;
    $stmt = $conn->prepare("SELECT content FROM page_content WHERE page_name = ? AND section_key = ?");
    $stmt->bind_param("ss", $page_name, $section_key);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        return $row['content'];
    }
    return $default;
}

function getFeatures($limit = null) {
    global $conn;
    $sql = "SELECT * FROM features WHERE is_active = 1 ORDER BY display_order ASC";
    if ($limit) {
        $sql .= " LIMIT " . intval($limit);
    }
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getTeamMembers($limit = null) {
    global $conn;
    $sql = "SELECT * FROM team_members WHERE is_active = 1 ORDER BY display_order ASC";
    if ($limit) {
        $sql .= " LIMIT " . intval($limit);
    }
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getTestimonials($limit = null) {
    global $conn;
    $sql = "SELECT * FROM testimonials WHERE is_active = 1 ORDER BY display_order ASC";
    if ($limit) {
        $sql .= " LIMIT " . intval($limit);
    }
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getStats() {
    global $conn;
    $stats = [];
    $queries = [
        'customers' => "SELECT COUNT(DISTINCT user_id) as count FROM orders",
        'farms' => "SELECT COUNT(*) as count FROM farms",
        'satisfaction' => "SELECT AVG(rating) as avg_rating FROM testimonials WHERE rating IS NOT NULL"
    ];
    
    foreach ($queries as $key => $query) {
        $result = $conn->query($query);
        if ($result) {
            $stats[$key] = $result->fetch_assoc();
        } else {
            $stats[$key] = null;
        }
    }
    
    return [
        'customers' => ($stats['customers'] && isset($stats['customers']['count'])) ? $stats['customers']['count'] : '10,000+',
        'farms' => ($stats['farms'] && isset($stats['farms']['count'])) ? $stats['farms']['count'] : '50+',
        'satisfaction' => ($stats['satisfaction'] && isset($stats['satisfaction']['avg_rating'])) ? round($stats['satisfaction']['avg_rating'] * 20) . '%' : '99%'
    ];
}
?>