<?php
declare(strict_types=1);

namespace App;

use DateTimeImmutable;
use RuntimeException;

final class GoogleBusinessReviews
{
    public static function sync(): int
    {
        $accountId = self::requiredEnvironment('GBP_ACCOUNT_ID');
        $locationId = self::requiredEnvironment('GBP_LOCATION_ID');
        $clientId = self::requiredEnvironment('GOOGLE_OAUTH_CLIENT_ID');
        $clientSecret = self::requiredEnvironment('GOOGLE_OAUTH_CLIENT_SECRET');
        $refreshToken = self::requiredEnvironment('GOOGLE_OAUTH_REFRESH_TOKEN');

        $token = self::requestJson('https://oauth2.googleapis.com/token', 'POST', [
            'Content-Type: application/x-www-form-urlencoded',
        ], http_build_query([
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'refresh_token' => $refreshToken,
            'grant_type' => 'refresh_token',
        ]));
        $accessToken = $token['access_token'] ?? null;
        if (!is_string($accessToken) || $accessToken === '') {
            throw new RuntimeException('Google OAuth did not return an access token.');
        }

        $parent = 'accounts/' . rawurlencode($accountId) . '/locations/' . rawurlencode($locationId);
        $baseUrl = 'https://mybusiness.googleapis.com/v4/' . $parent . '/reviews';
        $syncStartedAt = (new DateTimeImmutable())->format('Y-m-d H:i:s.u');
        $pageToken = null;
        $seenPageTokens = [];
        $synced = 0;

        do {
            $query = ['pageSize' => 50, 'orderBy' => 'updateTime desc'];
            if ($pageToken !== null) {
                $query['pageToken'] = $pageToken;
            }
            $page = self::requestJson(
                $baseUrl . '?' . http_build_query($query),
                'GET',
                ['Authorization: Bearer ' . $accessToken]
            );
            $reviews = $page['reviews'] ?? [];
            if (!is_array($reviews)) {
                throw new RuntimeException('Google Business Profile returned an invalid reviews list.');
            }

            foreach ($reviews as $review) {
                if (!is_array($review)) {
                    throw new RuntimeException('Google Business Profile returned an invalid review.');
                }
                self::saveReview($review, $syncStartedAt);
                $synced++;
            }

            $nextPageToken = $page['nextPageToken'] ?? '';
            if (!is_string($nextPageToken)) {
                throw new RuntimeException('Google Business Profile returned an invalid page token.');
            }
            if ($nextPageToken !== '' && isset($seenPageTokens[$nextPageToken])) {
                throw new RuntimeException('Google Business Profile repeated a page token; sync was stopped.');
            }
            if ($nextPageToken !== '') {
                $seenPageTokens[$nextPageToken] = true;
            }
            $pageToken = $nextPageToken !== '' ? $nextPageToken : null;
        } while ($pageToken !== null);

        Database::execute('UPDATE google_reviews SET active=0 WHERE last_seen_at < ?', [$syncStartedAt]);
        return $synced;
    }

    private static function saveReview(array $review, string $syncStartedAt): void
    {
        $reviewId = $review['reviewId'] ?? null;
        if (!is_string($reviewId) || $reviewId === '' || strlen($reviewId) > 255) {
            throw new RuntimeException('Google Business Profile returned a review without a valid ID.');
        }

        $rating = self::rating($review['starRating'] ?? null);
        $reviewer = $review['reviewer'] ?? [];
        if (!is_array($reviewer)) {
            throw new RuntimeException('Google Business Profile returned an invalid reviewer.');
        }
        $reviewerName = $reviewer['displayName'] ?? 'Google user';
        if (!is_string($reviewerName)) {
            throw new RuntimeException('Google Business Profile returned an invalid reviewer name.');
        }

        $comment = $review['comment'] ?? null;
        if ($comment !== null && !is_string($comment)) {
            throw new RuntimeException('Google Business Profile returned an invalid review comment.');
        }

        Database::execute(
            'INSERT INTO google_reviews
                (review_id, reviewer_name, rating, comment, review_created_at, review_updated_at, last_seen_at, active)
             VALUES
                (:review_id, :reviewer_name, :rating, :comment, :review_created_at, :review_updated_at, :last_seen_at, 1)
             ON DUPLICATE KEY UPDATE
                reviewer_name=VALUES(reviewer_name),
                rating=VALUES(rating),
                comment=VALUES(comment),
                review_created_at=VALUES(review_created_at),
                review_updated_at=VALUES(review_updated_at),
                last_seen_at=VALUES(last_seen_at),
                active=1',
            [
                'review_id' => $reviewId,
                'reviewer_name' => mb_substr($reviewerName, 0, 255, 'UTF-8'),
                'rating' => $rating,
                'comment' => $comment,
                'review_created_at' => self::apiDateTime($review['createTime'] ?? null),
                'review_updated_at' => self::apiDateTime($review['updateTime'] ?? null),
                'last_seen_at' => $syncStartedAt,
            ]
        );
    }

    private static function rating(mixed $value): int
    {
        $ratings = ['ONE' => 1, 'TWO' => 2, 'THREE' => 3, 'FOUR' => 4, 'FIVE' => 5];
        if (is_string($value) && isset($ratings[$value])) {
            return $ratings[$value];
        }
        if (is_int($value) && $value >= 1 && $value <= 5) {
            return $value;
        }
        throw new RuntimeException('Google Business Profile returned an invalid review rating.');
    }

    private static function apiDateTime(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }
        if (!is_string($value)) {
            throw new RuntimeException('Google Business Profile returned an invalid review date.');
        }
        $timestamp = strtotime($value);
        if ($timestamp === false) {
            throw new RuntimeException('Google Business Profile returned an invalid review date.');
        }
        return date('Y-m-d H:i:s', $timestamp);
    }

    private static function requiredEnvironment(string $name): string
    {
        $value = getenv($name);
        if ($value === false || trim($value) === '') {
            throw new RuntimeException($name . ' must be configured before Google review sync can run.');
        }
        return trim($value);
    }

    private static function requestJson(string $url, string $method, array $headers, string $body = ''): array
    {
        $headers[] = 'Accept: application/json';
        $context = stream_context_create([
            'http' => [
                'method' => $method,
                'header' => implode("\r\n", $headers),
                'content' => $body,
                'ignore_errors' => true,
                'timeout' => 30,
            ],
        ]);
        $responseBody = @file_get_contents($url, false, $context);
        if ($responseBody === false) {
            throw new RuntimeException('Could not reach Google while syncing reviews.');
        }

        $status = 0;
        foreach ($http_response_header ?? [] as $header) {
            if (preg_match('/^HTTP\/\S+\s+(\d{3})/', $header, $matches)) {
                $status = (int) $matches[1];
            }
        }

        try {
            $response = json_decode($responseBody, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new RuntimeException('Google returned an invalid response while syncing reviews.', 0, $e);
        }
        if (!is_array($response)) {
            throw new RuntimeException('Google returned an invalid response while syncing reviews.');
        }
        if ($status < 200 || $status >= 300) {
            $message = $response['error']['message'] ?? 'request failed';
            if (!is_string($message)) {
                $message = 'request failed';
            }
            throw new RuntimeException('Google API error (' . $status . '): ' . $message);
        }
        return $response;
    }
}
