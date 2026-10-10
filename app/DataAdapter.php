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
                'address'      => Settings::get('contact_address', $fallback['contact']['address'] ?? 'House 12, Road 7, Dhanmondi, Dhaka 1205, Bangladesh'),
                'address_link' => Settings::get('contact_address_link', $fallback['contact']['address_link'] ?? 'https://maps.google.com/?q=House+12+Road+7+Dhanmondi+Dhaka'),
            ],
            'event' => [
                'title'    => Settings::get('event_title', $fallback['event']['title'] ?? 'Manami Study Japan Expo 2026'),
                'date_iso' => Settings::get('event_date', date('Y-m-d', strtotime('+21 days'))) ?: date('Y-m-d', strtotime('+21 days')),
            ],
            'social'  => $s,
            'nav'     => Settings::getJson('nav_json', $fallback['nav'] ?? [
                ['key' => 'home', 'href' => 'index.php'],
                ['key' => 'contact', 'href' => 'contact.php'],
            ]),
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
            $out[] = [
                'id'      => (int) $r['id'],
                'slug'    => $r['slug'],
                'icon'    => $r['icon'] ?: 'globe',
                'title'   => $r['title'],
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

    public static function posts(int $limit = 3): array
    {
        $rows = Content::recentPosts($limit);
        $out = [];
        foreach ($rows as $r) {
            $ts = $r['published_at'] ? strtotime($r['published_at']) : time();
            $out[] = [
                'day'   => date('d', $ts),
                'mo'    => date('M', $ts),
                'tag'   => ucfirst($r['type']),
                'title' => $r['title'],
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
