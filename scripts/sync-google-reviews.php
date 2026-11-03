<?php
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require dirname(__DIR__) . '/app/bootstrap.php';

try {
    $count = App\GoogleBusinessReviews::sync();
    fwrite(STDOUT, sprintf("[%s] Synced %d Google reviews.\n", date(DATE_ATOM), $count));
} catch (RuntimeException $e) {
    fwrite(STDERR, sprintf("[%s] Google review sync failed: %s\n", date(DATE_ATOM), $e->getMessage()));
    exit(1);
}
