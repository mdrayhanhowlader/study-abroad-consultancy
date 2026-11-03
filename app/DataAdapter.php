<?php
declare(strict_types=1);

namespace App;

final class DataAdapter
{
    public static function site(): array
    {
        $fallback = require __DIR__ . '/../data/site.php';
        $s = Settings::getJson('social_json', $fallback['social'] ?? [
            ['label' => 'Facebook',  'href' => 'https://facebook.com/',  'icon' => 'facebook'],
            ['label' => 'Instagram', 'href' => 'https://instagram.com/', 'icon' => 'instagram'],
            ['label' => 'YouTube',   'href' => 'https://youtube.com/',   'icon' => 'youtube'],
        ]);
        $logoUrl    = Content::mediaUrl((int) Settings::get('logo_image_id')) ?: null;
        $faviconUrl = Content::mediaUrl((int) Settings::get('favicon_image_id')) ?: null;
        $appleUrl   = Content::mediaUrl((int) Settings::get('apple_touch_icon_image_id')) ?: null;
        $ogImageId  = (int) Settings::get('og_image_id');
        $ogImage    = $ogImageId ? (Content::mediaUrl($ogImageId) ?: Settings::get('og_image', $fallback['brand']['og_image'] ?? '')) : Settings::get('og_image', $fallback['brand']['og_image'] ?? '');
        $address = Settings::get('contact_address', $fallback['contact']['address'] ?? 'House 12, Road 7, Dhanmondi, Dhaka 1205, Bangladesh');
        $address = preg_replace('/,\s*Bangladesh,\s*(\d{4})$/i', ', $1, Bangladesh', $address) ?? $address;
        $address = preg_replace('/\s+-\s+(\d{4})\s+Bangladesh$/i', ', $1, Bangladesh', $address) ?? $address;
        $nav = Settings::getJson('nav_json', $fallback['nav'] ?? []);
        if (!is_array($nav)) {
            $nav = $fallback['nav'] ?? [];
        }
        $faqSectionEnabled = self::faqSectionEnabled();
        $nav = array_values(array_filter(
            $nav,
            static fn($item): bool => is_array($item)
                && !in_array($item['key'] ?? '', ['quick_order', 'courses'], true)
                && ($faqSectionEnabled || ($item['key'] ?? '') !== 'faq')
        ));
        $destinationItems = [
            ['key' => 'japan', 'href' => 'country/japan'],
            ['key' => 'malaysia', 'href' => 'country/malaysia'],
            ['key' => 'australia', 'href' => 'country/australia'],
            ['key' => 'south_korea', 'href' => 'country/south-korea'],
        ];
        $hasDestinations = false;
        foreach ($nav as &$item) {
            if (($item['key'] ?? '') === 'books_courses') {
                $item['href'] = 'books.php';
            } elseif (($item['key'] ?? '') === 'study_abroad') {
                $item['children'] = array_values(array_filter(
                    $item['children'] ?? [],
                    static fn($child): bool => is_array($child)
                        && ($child['key'] ?? '') !== 'destinations'
                        && !in_array($child['key'] ?? '', ['japan', 'malaysia', 'australia', 'south_korea'], true)
                ));
            } elseif (($item['key'] ?? '') === 'destinations') {
                $item['children'] = $destinationItems;
                unset($item['href']);
                $hasDestinations = true;
            }
        }
        unset($item);
        if (!$hasDestinations) {
            $nav[] = ['key' => 'destinations', 'children' => $destinationItems];
        }
        return [
            'brand' => [
                'name'          => Settings::get('brand_name', $fallback['brand']['name'] ?? 'Manami'),
                'tagline'       => Settings::get('brand_tagline', $fallback['brand']['tagline'] ?? 'EDUCATION'),
                'full_name'     => (Settings::get('brand_name', $fallback['brand']['name'] ?? 'Manami')) . ' ' . (Settings::get('brand_tagline', $fallback['brand']['tagline'] ?? 'EDUCATION')),
                'slogan'        => Settings::get('brand_slogan', $fallback['brand']['slogan'] ?? 'Manami Leads, You Succeed...'),
                'logo_image_id' => Settings::get('logo_image_id') ?: null,
                'logo_url'      => $logoUrl,
                'favicon_url'   => $faviconUrl,
                'apple_touch_url' => $appleUrl,
                'domain'        => Settings::get('domain', $fallback['brand']['domain'] ?? 'https://manamieducation.com.bd'),
                'og_image'      => $ogImage,
            ],
            'contact' => [
                'phone'        => Settings::get('contact_phone', $fallback['contact']['phone'] ?? '+880 1700 000000'),
                'phone_tel'    => Settings::get('contact_phone_tel', $fallback['contact']['phone_tel'] ?? '+8801700000000'),
                'email'        => Settings::get('contact_email', $fallback['contact']['email'] ?? 'office@manami.education'),
                'address'      => $address,
                'address_link' => Settings::get('contact_address_link', $fallback['contact']['address_link'] ?? 'https://maps.google.com/?q=House+12+Road+7+Dhanmondi+Dhaka'),
                'map_embed_url' => Settings::get('contact_map_embed_url', $fallback['contact']['map_embed_url'] ?? ''),
            ],
            'event' => [
                'title'    => Settings::get('event_title', $fallback['event']['title'] ?? 'Manami Study Japan Expo 2026'),
                'date_iso' => Settings::get('event_date', date('Y-m-d', strtotime('+21 days'))) ?: date('Y-m-d', strtotime('+21 days')),
            ],
            'social'  => $s,
            'nav'     => $nav,
        ];
    }

    public static function countries(): array
    {
        $rows = Content::countries();
        $out = [];
        foreach ($rows as $r) {
            $codeSlug = strtolower($r['code']);
            $nameSlug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $r['name']));
            $out[] = [
                'slug'        => $nameSlug,
                'code'        => $r['code'],
                'name'        => $r['name'],
                'href'        => 'country/' . $nameSlug,
                'flag'        => $r['flag_css'] ?: 'flag-' . $codeSlug,
                'description' => $r['description'] ?? '',
                'featured'    => (bool) $r['featured'],
                'active'      => (bool) $r['active'],
                'image_url'   => Content::mediaUrl(isset($r['image_id']) ? (int) $r['image_id'] : null),
                'img1'        => self::countryImage($codeSlug, 1),
                'img2'        => self::countryImage($codeSlug, 2),
            ];
        }
        return $out;
    }

    private static function countryImage(string $slug, int $which): string
    {
        $map = [
            'jp' => ['1493780474015-ba834fd0ce2f', '1542051841857-5f90071e7989'],
            'au' => ['1533422902779-aff35862e462', '1493246507139-91e8fad9978e'],
            'ca' => ['1503614472-8c93d56e92ce', '1517292987719-0369a794ec0f'],
            'gb' => ['1513635269975-59663e0ac1ad', '1526129318478-5ed909f7b1c7'],
            'us' => ['1485871981521-5b1fd3805eee', '1499092346589-b9b6be3e94b2'],
            'eu' => ['1502602898657-3e91760cbb34', '1467269204594-9661b134dd2b'],
            'my' => ['1598928506311-c55ded91a20c', '1504214208698-ea1916a2195a'],
            'kr' => ['1548115184-bc6544d06a58', '1528360983277-13d401cdc186'],
        ];
        $id = $map[$slug][$which - 1] ?? '1493780474015-ba834fd0ce2f';
        return 'photo-' . $id;
    }

    public static function services(): array
    {
        $rows = Content::services();
        $out = [];
        foreach ($rows as $r) {
            $title = match ((string) $r['title']) {
                'Japan Student Visa (Ryugaku)', 'Student Visa (Ryugaku)' => 'Student Visa Support',
                'SSW — Specified Skilled Worker', 'SSW (Specified Skilled Worker)' => 'Skilled Worker Programme (SSW)',
                'TITP — Technical Intern Training Program', 'TITP (Technical Intern Training)' => 'Technical Intern Training (TITP)',
                'Japanese Language (JLPT N5–N1)' => 'Japanese Language Courses (JLPT N5–N1)',
                default => $r['title'],
            };
            $out[] = [
                'id'      => (int) $r['id'],
                'slug'    => $r['slug'],
                'icon'    => $r['icon'] ?: 'globe',
                'title'   => $title,
                'desc'    => $r['summary'] ?? '',
                'body'    => $r['body'] ?? '',
                'cta_text'=> $r['cta_text'] ?? '',
                'cta_href'=> $r['cta_href'] ?? '',
            ];
        }
        return $out;
    }

    public static function testimonials(): array
    {
        $rows = Content::testimonials();
        $out = [];
        foreach ($rows as $r) {
            $out[] = [
                'name'    => $r['name'],
                'role'    => $r['country'] ? 'Study in ' . $r['country'] : 'Student',
                'quote'   => $r['body'],
                'rating'  => (int) $r['rating'],
                'source'  => 'student',
            ];
        }
        foreach (Content::googleReviews() as $r) {
            $out[] = [
                'name'   => $r['reviewer_name'],
                'role'   => t('testimonials.google_review'),
                'quote'  => $r['comment'] !== null && $r['comment'] !== ''
                    ? $r['comment']
                    : t('testimonials.rating_only'),
                'rating' => (int) $r['rating'],
                'source' => 'google',
            ];
        }
        return $out;
    }

    public static function faqs(): array
    {
        $rows = Content::faqs();
        $out = [];
        foreach ($rows as $r) {
            $out[] = [
                'q' => $r['question'],
                'a' => $r['answer'],
            ];
        }
        return $out;
    }

    public static function faqSectionEnabled(): bool
    {
        return Settings::get('faq_section_enabled', '1') === '1';
    }

    public static function posts(int $limit = 3): array
    {
        $rows = Content::recentPosts($limit);
        $out = [];
        foreach ($rows as $r) {
            $ts = $r['published_at'] ? strtotime($r['published_at']) : time();
            $out[] = [
                'day'   => date('d', $ts),
                'mo'    => date('M', $ts),
                'date'  => date('F j, Y', $ts),
                'tag'   => ucfirst($r['type']),
                'title' => $r['title'],
                'excerpt' => trim((string) ($r['excerpt'] ?? '')),
                'cover' => !empty($r['cover_id']) ? Content::mediaUrl((int) $r['cover_id']) : null,
                'href'  => 'blog/' . $r['slug'],
                'slug'  => $r['slug'],
                'type'  => $r['type'],
            ];
        }
        return $out;
    }

    public static function stats(): array
    {
        $raw = Content::stats();
        $out = [];
        foreach ($raw as $s) {
            $value  = (string) ($s['value'] ?? '');
            $suffix = '';
            if (preg_match('/^(\d+)(.*)$/', $value, $m)) {
                $value  = $m[1];
                $suffix = $m[2];
            }
            $out[] = [
                'value'  => $value,
                'label'  => (string) ($s['label'] ?? ''),
                'suffix' => $suffix,
            ];
        }
        return $out;
    }
}
