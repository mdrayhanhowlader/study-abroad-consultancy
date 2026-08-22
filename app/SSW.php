<?php
declare(strict_types=1);

namespace App;

final class SSW
{
    public const STAGES = [
        'assessment',
        'jlpt_prep',
        'jlpt_registered',
        'jlpt_passed',
        'skills_test_prep',
        'skills_test_passed',
        'employer_matching',
        'employer_matched',
        'coa_application',
        'coa_approved',
        'visa_application',
        'visa_approved',
        'departure_prep',
        'deployed',
        'onboarded',
    ];

    public const STAGE_LABELS = [
        'assessment'         => 'Initial Assessment',
        'jlpt_prep'          => 'JLPT Preparation',
        'jlpt_registered'    => 'JLPT Registered',
        'jlpt_passed'        => 'JLPT Passed',
        'skills_test_prep'   => 'Skills Test Prep',
        'skills_test_passed' => 'Skills Test Passed',
        'employer_matching'  => 'Employer Matching',
        'employer_matched'   => 'Employer Matched',
        'coa_application'    => 'COA Application',
        'coa_approved'       => 'COA Approved',
        'visa_application'   => 'Visa Application',
        'visa_approved'      => 'Visa Approved',
        'departure_prep'     => 'Departure Preparation',
        'deployed'           => 'Deployed',
        'onboarded'          => 'Onboarded',
    ];

    public static function all(?string $stage = null, ?int $assignedTo = null, int $limit = 200): array
    {
        $where = [];
        $params = [];
        if ($stage) { $where[] = 'current_stage=?'; $params[] = $stage; }
        if ($assignedTo !== null) { $where[] = 'assigned_to=?'; $params[] = $assignedTo; }
        $sql = 'SELECT s.*, l.name AS lead_name, u.name AS assigned_name
                FROM ssw_applications s
                LEFT JOIN leads l ON l.id=s.lead_id
                LEFT JOIN users u ON u.id=s.assigned_to';
        if ($where) $sql .= ' WHERE ' . implode(' AND ', $where);
        $sql .= ' ORDER BY s.id DESC LIMIT ' . (int) $limit;
        return Database::fetchAll($sql, $params);
    }

    public static function find(int $id): ?array
    {
        return Database::fetchOne(
            'SELECT s.*, l.name AS lead_name, u.name AS assigned_name
             FROM ssw_applications s
             LEFT JOIN leads l ON l.id=s.lead_id
             LEFT JOIN users u ON u.id=s.assigned_to
             WHERE s.id=?',
            [$id]
        );
    }

    public static function create(array $data): int
    {
        $pdo = Database::pdo();
        $pdo->beginTransaction();
        try {
            $id = Database::insert('ssw_applications', [
                'lead_id'            => $data['lead_id'] ?? null,
                'order_id'           => $data['order_id'] ?? null,
                'applicant_name'     => $data['applicant_name'],
                'applicant_phone'    => $data['applicant_phone'],
                'applicant_email'    => $data['applicant_email'] ?? null,
                'passport_number'    => $data['passport_number'] ?? null,
                'jlpt_level'         => $data['jlpt_level'] ?? 'none',
                'industry'           => $data['industry'] ?? null,
                'preferred_prefecture' => $data['preferred_prefecture'] ?? null,
                'current_stage'      => 'assessment',
                'notes'              => $data['notes'] ?? null,
                'assigned_to'        => $data['assigned_to'] ?? null,
            ]);
            Content::log(null, 'ssw.created', 'ssw_application', $id);
            $pdo->commit();
            return $id;
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public static function advanceStage(int $id, string $newStage, ?int $userId = null, ?string $notes = null): void
    {
        $app = self::find($id);
        if (!$app) throw new \RuntimeException('SSW application not found.');
        if (!in_array($newStage, self::STAGES, true)) throw new \RuntimeException('Invalid stage.');

        $oldStage = $app['current_stage'];
        Database::update('ssw_applications', ['current_stage' => $newStage], 'id=:id', ['id' => $id]);

        Database::insert('ssw_stage_history', [
            'application_id' => $id,
            'from_stage'     => $oldStage,
            'to_stage'       => $newStage,
            'changed_by'     => $userId,
            'notes'          => $notes,
        ]);

        Content::log($userId, 'ssw.stage_change', 'ssw_application', $id, [
            'from' => $oldStage, 'to' => $newStage,
        ]);
    }

    public static function updateDetails(int $id, array $data): void
    {
        Database::update('ssw_applications', $data, 'id=:id', ['id' => $id]);
    }

    public static function history(int $applicationId): array
    {
        return Database::fetchAll(
            'SELECT sh.*, u.name AS user_name FROM ssw_stage_history sh
             LEFT JOIN users u ON u.id=sh.changed_by
             WHERE sh.application_id=? ORDER BY sh.id DESC',
            [$applicationId]
        );
    }

    public static function countByStage(): array
    {
        $rows = Database::fetchAll(
            'SELECT current_stage, COUNT(*) AS cnt FROM ssw_applications GROUP BY current_stage'
        );
        $out = [];
        foreach (self::STAGES as $s) $out[$s] = 0;
        foreach ($rows as $r) $out[$r['current_stage']] = (int) $r['cnt'];
        return $out;
    }

    public static function totalCount(): int
    {
        return (int) Database::fetchValue('SELECT COUNT(*) FROM ssw_applications');
    }
}
