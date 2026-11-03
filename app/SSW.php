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

    public const DOCUMENTS = [
        'passport' => 'Passport copy',
        'national_id' => 'National ID / birth certificate',
        'academic_records' => 'Academic certificates',
        'cv' => 'CV / resume',
        'jlpt_certificate' => 'JLPT certificate',
        'skills_certificate' => 'SSW skills test certificate',
        'medical_report' => 'Medical report',
        'police_clearance' => 'Police clearance',
        'employment_contract' => 'Employment contract',
        'coe' => 'Certificate of Eligibility (COE)',
        'visa' => 'Visa documents',
    ];

    public const DOCUMENT_STATUSES = [
        'not_started' => 'Not received',
        'received' => 'Received',
        'verified' => 'Verified',
        'not_applicable' => 'Not applicable',
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

    public static function create(array $data, ?int $userId = null): int
    {
        $pdo = Database::pdo();
        $pdo->beginTransaction();
        try {
            $id = self::insertApplication($data);
            self::recordInitialStage($id, $userId);
            Content::log($userId, 'ssw.created', 'ssw_application', $id);
            $pdo->commit();
            return $id;
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public static function createFromLead(int $leadId, ?int $userId = null): int
    {
        $pdo = Database::pdo();
        $pdo->beginTransaction();
        try {
            $lead = Database::fetchOne('SELECT * FROM leads WHERE id=? FOR UPDATE', [$leadId]);
            if (!$lead || $lead['service_interest'] !== 'ssw' || $lead['status'] === 'lost') {
                throw new \RuntimeException('Choose an active SSW lead to convert.');
            }
            if (Database::fetchValue('SELECT id FROM ssw_applications WHERE lead_id=? LIMIT 1', [$leadId])) {
                throw new \RuntimeException('This lead already has an SSW application.');
            }

            $id = self::insertApplication([
                'lead_id' => $leadId,
                'applicant_name' => $lead['name'],
                'applicant_phone' => $lead['phone'],
                'applicant_email' => $lead['email'],
                'notes' => $lead['message'],
                'assigned_to' => $lead['assigned_to'],
            ]);
            Database::update('leads', ['status' => 'converted'], 'id=:id', ['id' => $leadId]);
            self::recordInitialStage($id, $userId);
            Content::log($userId, 'ssw.created_from_lead', 'ssw_application', $id, ['lead_id' => $leadId]);
            $pdo->commit();
            return $id;
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public static function advanceStage(int $id, string $newStage, ?int $userId = null, ?string $notes = null): void
    {
        if (!in_array($newStage, self::STAGES, true)) throw new \RuntimeException('Invalid stage.');
        $pdo = Database::pdo();
        $pdo->beginTransaction();
        try {
            $app = Database::fetchOne('SELECT current_stage FROM ssw_applications WHERE id=? FOR UPDATE', [$id]);
            if (!$app) throw new \RuntimeException('SSW application not found.');
            $oldStage = $app['current_stage'];
            if (array_search($newStage, self::STAGES, true) <= array_search($oldStage, self::STAGES, true)) {
                throw new \RuntimeException('Choose a stage later than the current stage.');
            }

            Database::update('ssw_applications', ['current_stage' => $newStage], 'id=:id', ['id' => $id]);
            Database::insert('ssw_stage_history', [
                'application_id' => $id,
                'from_stage' => $oldStage,
                'to_stage' => $newStage,
                'changed_by' => $userId,
                'notes' => $notes,
            ]);
            Content::log($userId, 'ssw.stage_change', 'ssw_application', $id, [
                'from' => $oldStage,
                'to' => $newStage,
            ]);
            $pdo->commit();
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public static function updateDetails(int $id, array $data, ?int $userId = null): void
    {
        $pdo = Database::pdo();
        $pdo->beginTransaction();
        try {
            if (!Database::fetchValue('SELECT id FROM ssw_applications WHERE id=? FOR UPDATE', [$id])) {
                throw new \RuntimeException('SSW application not found.');
            }
            Database::update('ssw_applications', $data, 'id=:id', ['id' => $id]);
            Content::log($userId, 'ssw.details_update', 'ssw_application', $id);
            $pdo->commit();
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
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

    public static function documents(int $applicationId): array
    {
        $rows = Database::fetchAll(
            'SELECT document_key, status, notes FROM ssw_document_checklist WHERE application_id=?',
            [$applicationId]
        );
        $saved = [];
        foreach ($rows as $row) $saved[$row['document_key']] = $row;

        $documents = [];
        foreach (self::DOCUMENTS as $key => $label) {
            $documents[] = [
                'key' => $key,
                'label' => $label,
                'status' => $saved[$key]['status'] ?? 'not_started',
                'notes' => $saved[$key]['notes'] ?? '',
            ];
        }
        return $documents;
    }

    public static function updateDocuments(int $applicationId, array $documents, ?int $userId = null): void
    {
        $pdo = Database::pdo();
        $pdo->beginTransaction();
        try {
            if (!Database::fetchValue('SELECT id FROM ssw_applications WHERE id=? FOR UPDATE', [$applicationId])) {
                throw new \RuntimeException('SSW application not found.');
            }
            foreach (self::DOCUMENTS as $key => $_label) {
                $document = $documents[$key] ?? null;
                if (!is_array($document) || !isset($document['status'])) {
                    throw new \InvalidArgumentException('Complete the SSW document checklist.');
                }
                $status = (string) $document['status'];
                if (!isset(self::DOCUMENT_STATUSES[$status])) {
                    throw new \InvalidArgumentException('Choose a valid document status.');
                }
                $notes = trim((string) ($document['notes'] ?? ''));
                if (mb_strlen($notes) > 500) {
                    throw new \InvalidArgumentException('Document notes cannot exceed 500 characters.');
                }
                Database::execute(
                    'INSERT INTO ssw_document_checklist (application_id, document_key, status, notes, updated_by)
                     VALUES (?, ?, ?, ?, ?)
                     ON DUPLICATE KEY UPDATE status=VALUES(status), notes=VALUES(notes), updated_by=VALUES(updated_by)',
                    [$applicationId, $key, $status, $notes !== '' ? $notes : null, $userId]
                );
            }
            Content::log($userId, 'ssw.documents_update', 'ssw_application', $applicationId);
            $pdo->commit();
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
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

    private static function insertApplication(array $data): int
    {
        return Database::insert('ssw_applications', [
            'lead_id' => $data['lead_id'] ?? null,
            'order_id' => $data['order_id'] ?? null,
            'applicant_name' => $data['applicant_name'],
            'applicant_phone' => $data['applicant_phone'],
            'applicant_email' => $data['applicant_email'] ?? null,
            'passport_number' => $data['passport_number'] ?? null,
            'jlpt_level' => $data['jlpt_level'] ?? 'none',
            'industry' => $data['industry'] ?? null,
            'preferred_prefecture' => $data['preferred_prefecture'] ?? null,
            'current_stage' => 'assessment',
            'notes' => $data['notes'] ?? null,
            'assigned_to' => $data['assigned_to'] ?? null,
        ]);
    }

    private static function recordInitialStage(int $id, ?int $userId): void
    {
        Database::insert('ssw_stage_history', [
            'application_id' => $id,
            'from_stage' => null,
            'to_stage' => 'assessment',
            'changed_by' => $userId,
            'notes' => 'Application entered the SSW pipeline.',
        ]);
    }
}
