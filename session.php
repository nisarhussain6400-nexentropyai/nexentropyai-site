<?php
/**
 * session.php
 * The page a visitor lands on after clicking their emailed invite link.
 * Verifies the token, then shows the live consult window.
 * The actual avatar (Tavus/D-ID/HeyGen) embed goes in the marked spot below.
 */

$token = $_GET['token'] ?? '';
$token = preg_replace('/[^a-f0-9]/', '', $token); // only allow hex chars

$leadFile = __DIR__ . '/leads/' . $token . '.json';
$valid = false;
$lead = null;

if ($token && file_exists($leadFile)) {
    $lead = json_decode(file_get_contents($leadFile), true);
    if ($lead) {
        $valid = true;
        // Optional: mark as used / expire after first click
        // $lead['used'] = true;
        // file_put_contents($leadFile, json_encode($lead, JSON_PRETTY_PRINT));
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Your consult — NexEntropyAI</title>
<style>
  :root{
    --ink:#0D1117; --paper:#F4F5F1; --teal:#2A9D8F; --line:#DADCD3;
  }
  body{
    margin:0; background:var(--ink); color:var(--paper);
    font-family:'IBM Plex Sans', sans-serif;
    display:flex; align-items:center; justify-content:center;
    min-height:100vh; padding:24px;
  }
  .panel{ max-width:720px; width:100%; text-align:center; }
  h1{ font-family:'Space Grotesk', sans-serif; font-size:1.6rem; margin-bottom:12px; }
  p{ color:#9AA1AC; font-size:1rem; margin-bottom:28px; }
  .call-window{
    aspect-ratio:16/9; background:#161B22; border:1px solid #232A36;
    border-radius:6px; display:flex; align-items:center; justify-content:center;
    color:#4B5563; font-size:0.95rem;
  }
  .error{ color:#D5572B; }
</style>
</head>
<body>
<div class="panel">
<?php if ($valid): ?>
  <h1>Welcome, <?= htmlspecialchars($lead['name']) ?></h1>
  <p>Your live consult is ready to start. Click below to connect with your AI consultant.</p>

  <div class="call-window" id="avatarCallWindow">
    <!--
      AVATAR EMBED GOES HERE.
      This is the plug-in point for your chosen video-avatar provider
      (Tavus / D-ID / HeyGen conversational API).
      Their SDK will typically give you a <script> snippet or an
      embeddable iframe that you drop in right here, initialized
      with this visitor's name/email/industry as context for the
      avatar's opening line.
    -->
    Live avatar call will load here
  </div>

<?php else: ?>
  <h1 class="error">Link not valid</h1>
  <p>This invite link is invalid or has expired. Please book a new consult.</p>
<?php endif; ?>
</div>
</body>
</html>
