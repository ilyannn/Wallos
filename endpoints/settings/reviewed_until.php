<?php
require_once '../../includes/connect_endpoint.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    die(json_encode([
        "success" => false,
        "message" => translate('session_expired', $i18n)
    ]));
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $postData = file_get_contents("php://input");
    $data = json_decode($postData, true);

    $reviewed_until = $data['value'] ?? null;

    // Validate: must be a date string (YYYY-MM-DD) or null
    if ($reviewed_until !== null && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $reviewed_until)) {
        die(json_encode([
            "success" => false,
            "message" => translate("error", $i18n)
        ]));
    }

    $stmt = $db->prepare('UPDATE settings SET reviewed_until = :reviewed_until WHERE user_id = :userId');
    $stmt->bindValue(':reviewed_until', $reviewed_until, $reviewed_until === null ? SQLITE3_NULL : SQLITE3_TEXT);
    $stmt->bindParam(':userId', $userId, SQLITE3_INTEGER);

    if ($stmt->execute()) {
        die(json_encode([
            "success" => true,
            "message" => translate("success", $i18n)
        ]));
    } else {
        die(json_encode([
            "success" => false,
            "message" => translate("error", $i18n)
        ]));
    }
}

?>
