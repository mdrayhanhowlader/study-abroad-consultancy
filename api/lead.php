<?php
require_once __DIR__ . '/../app/bootstrap.php';

header('Content-Type: application/json');

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'message' => 'Method not allowed']);
    exit;
}

App\Csrf::verifyRequest();

try {
    $name    = App\Validation::str((string) ($_POST['name'] ?? ''), 2, 120);
    $phone   = App\Validation::phone((string) ($_POST['phone'] ?? ''));
    $email   = trim((string) ($_POST['email'] ?? ''));
    if ($email !== '') $email = App\Validation::email($email);
    $destination = trim((string) ($_POST['destination'] ?? '')) ?: null;
    $studyLevel  = trim((string) ($_POST['study_level'] ?? '')) ?: null;
    $serviceInterest = trim((string) ($_POST['service_interest'] ?? '')) ?: null;
    $message = trim((string) ($_POST['message'] ?? '')) ?: null;
    $source = trim((string) ($_POST['source'] ?? 'contact_form'));

    $validSources = ['contact_form', 'cta_form', 'book_page', 'course_page', 'facebook_ad', 'organic'];
    if (!in_array($source, $validSources, true)) $source = 'contact_form';

    $validServices = ['student-visa', 'ssw', 'titp', 'jlpt', 'ielts', 'career-counselling', 'other'];
    if ($serviceInterest && !in_array($serviceInterest, $validServices, true)) $serviceInterest = null;

    $fbclid = trim((string) ($_POST['fbclid'] ?? $_GET['fbclid'] ?? '')) ?: null;
    $gclid  = trim((string) ($_POST['gclid'] ?? $_GET['gclid'] ?? '')) ?: null;

    $leadId = App\Lead::create([
        'name'             => $name,
        'phone'            => $phone,
        'email'            => $email ?: null,
        'destination'      => $destination,
        'study_level'      => $studyLevel,
        'service_interest' => $serviceInterest,
        'message'          => $message,
        'source'           => $source,
        'fbclid'           => $fbclid,
        'gclid'            => $gclid,
    ]);

    $site = App\DataAdapter::site();
    $cfg  = require __DIR__ . '/../config/config.php';

    $mailBody  = '<p>New lead from the Manami website.</p>';
    $mailBody .= '<p><strong>Name:</strong> ' . h($name) . '<br><strong>Phone:</strong> ' . h($phone);
    if ($email) $mailBody .= '<br><strong>Email:</strong> ' . h($email);
    if ($destination) $mailBody .= '<br><strong>Destination:</strong> ' . h($destination);
    if ($studyLevel) $mailBody .= '<br><strong>Study Level:</strong> ' . h($studyLevel);
    if ($serviceInterest) $mailBody .= '<br><strong>Service:</strong> ' . h($serviceInterest);
    if ($message) $mailBody .= '<br><strong>Message:</strong><br>' . nl2br(h($message));
    $mailBody .= '<br><br><strong>Source:</strong> ' . h($source);
    $mailBody .= '</p>';

    App\Mail::send(
        $site['contact']['email'],
        $site['brand']['full_name'],
        'New lead: ' . $name . ' (' . $source . ')',
        App\Mail::wrap('New Lead', $mailBody)
    );

    App\Pixel::logEvent('Lead', ['source' => $source, 'lead_id' => $leadId]);

    echo json_encode(['ok' => true, 'lead_id' => $leadId]);
} catch (Throwable $e) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'message' => $e->getMessage()]);
}
