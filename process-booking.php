<?php
/**
 * process-booking.php
 * Receives the booking form, creates a unique session token,
 * stores the lead, and emails the invite link.
 *
 * Requirements on Hostinger:
 *  - PHP (any recent version, already included on shared hosting)
 *  - The "leads" folder must be writable (chmod 755 is usually fine)
 *  - Your domain's outgoing mail must be enabled (it is, by default, on Hostinger)
 */

header('Content-Type: application/json');

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

// ---- 1. Collect + sanitize form input ----
function clean($v) {
    return htmlspecialchars(trim($v ?? ''), ENT_QUOTES, 'UTF-8');
}

$company  = clean($_POST['company'] ?? '');
$name     = clean($_POST['name'] ?? '');
$email    = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
$phone    = clean($_POST['phone'] ?? '');
$industry = clean($_POST['industry'] ?? '');
$message  = clean($_POST['message'] ?? '');

if (!$email || !$name) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Name and a valid email are required.']);
    exit;
}

// ---- 2. Generate a unique session token ----
$token = bin2hex(random_bytes(16)); // 32-char random token
$createdAt = date('c');

// ---- 3. Store the lead (simple JSON file store — no DB setup needed) ----
$leadsDir = __DIR__ . '/leads';
if (!is_dir($leadsDir)) {
    mkdir($leadsDir, 0755, true);
}

$lead = [
    'token'     => $token,
    'company'   => $company,
    'name'      => $name,
    'email'     => $email,
    'phone'     => $phone,
    'industry'  => $industry,
    'message'   => $message,
    'createdAt' => $createdAt,
    'used'      => false,
];

$leadFile = $leadsDir . '/' . $token . '.json';
file_put_contents($leadFile, json_encode($lead, JSON_PRETTY_PRINT));

// Also append to a master log for your own reference (admin dashboard could read this later)
$logFile = $leadsDir . '/_all_leads.jsonl';
file_put_contents($logFile, json_encode($lead) . PHP_EOL, FILE_APPEND | LOCK_EX);

// ---- 4. Build the invite link ----
// Change this to your actual domain if different
$baseUrl = 'https://nexentropyai.ca';
$inviteLink = $baseUrl . '/session.php?token=' . $token;

// ---- 5. Send the invite email ----
$subject = "Your NexEntropyAI consult link — {$company}";

$body = <<<EOT
Hi {$name},

Thanks for booking a consult with NexEntropyAI.

Your live session link:
{$inviteLink}

This link is unique to you — click it whenever you're ready to start your consult.

— NexEntropyAI
EOT;

$headers = "From: NexEntropyAI <no-reply@nexentropyai.ca>\r\n";
$headers .= "Reply-To: no-reply@nexentropyai.ca\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

$mailSent = @mail($email, $subject, $body, $headers);

// ---- 6. Respond to the frontend ----
echo json_encode([
    'success'    => true,
    'mailSent'   => $mailSent,
    'inviteLink' => $inviteLink, // returned so the frontend can also show it as a fallback
]);
