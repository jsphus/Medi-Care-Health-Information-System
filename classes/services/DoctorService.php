<?php

require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../Auth.php';

class DoctorService {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getDashboardData($docId, Auth $auth): array {
        $data = [
            'doctor' => null,
            'profile_picture_url' => null,
            'stats' => [],
            'recent_appointments' => [],
            'today_appointments' => [],
            'today_schedule' => [],
            'recent_patients' => [],
            'recent_records' => [],
            'notifications' => [],
            'upcoming_appointments' => [],
            'patient_list' => [],
            'appointment_type_chart' => [
                'First visit' => 0,
                'Follow up' => 0,
                'Emergency' => 0
            ],
            'weekly_visits' => [0, 0, 0, 0],
            'new_appointments' => [],
            'chart_data' => [
                'appointments' => [10, 15, 20, 18, 25, 30, 28],
                'completed' => [8, 12, 18, 15, 22, 25, 24]
            ]
        ];

        try {
            $stmt = $this->db->prepare("
                SELECT d.*, s.spec_name 
                FROM doctors d
                LEFT JOIN specializations s ON d.doc_specialization_id = s.spec_id
                WHERE d.doc_id = :doc_id
            ");
            $stmt->execute(['doc_id' => $docId]);
            $data['doctor'] = $stmt->fetch(PDO::FETCH_ASSOC);

            $stmt = $this->db->prepare("SELECT profile_picture_url FROM users WHERE user_id = :user_id");
            $stmt->execute(['user_id' => $auth->getUserId()]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            $data['profile_picture_url'] = $user['profile_picture_url'] ?? null;
        } catch (PDOException $e) {
            // ignore
        }

        $stats = [
            'total_appointments' => 0,
            'today_appointments' => 0,
            'upcoming_appointments' => 0,
            'completed_appointments' => 0,
            'total_patients' => 0,
            'my_schedules' => 0,
            'all_schedules' => 0,
            'pending_lab_results' => 0,
            'active_doctors' => 0,
            'today_revenue' => 0,
            'admitted_patients' => 0
        ];

        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM appointments WHERE doc_id = :doc_id");
            $stmt->execute(['doc_id' => $docId]);
            $stats['total_appointments'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

            $stmt = $this->db->prepare("
                SELECT COUNT(*) as count 
                FROM appointments 
                WHERE doc_id = :doc_id AND appointment_date = CURRENT_DATE
            ");
            $stmt->execute(['doc_id' => $docId]);
            $stats['today_appointments'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

            $stmt = $this->db->prepare("
                SELECT COUNT(*) as count 
                FROM appointments 
                WHERE doc_id = :doc_id AND appointment_date > CURRENT_DATE
            ");
            $stmt->execute(['doc_id' => $docId]);
            $stats['upcoming_appointments'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

            $stmt = $this->db->prepare("
                SELECT COUNT(*) as count 
                FROM appointments a
                JOIN appointment_statuses s ON a.status_id = s.status_id
                WHERE a.doc_id = :doc_id AND LOWER(s.status_name) = 'completed'
            ");
            $stmt->execute(['doc_id' => $docId]);
            $stats['completed_appointments'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

            $stmt = $this->db->prepare("
                SELECT COUNT(DISTINCT pat_id) as count 
                FROM appointments 
                WHERE doc_id = :doc_id
            ");
            $stmt->execute(['doc_id' => $docId]);
            $stats['total_patients'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM schedules WHERE doc_id = :doc_id");
            $stmt->execute(['doc_id' => $docId]);
            $stats['my_schedules'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

            $stmt = $this->db->query("SELECT COUNT(*) as count FROM schedules");
            $stats['all_schedules'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

            $stmt = $this->db->prepare("
                SELECT COUNT(*) as count 
                FROM medical_records 
                WHERE doc_id = :doc_id 
                AND (follow_up_date IS NOT NULL AND follow_up_date >= CURRENT_DATE)
                AND diagnosis IS NOT NULL
            ");
            $stmt->execute(['doc_id' => $docId]);
            $stats['pending_lab_results'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

            $stmt = $this->db->query("SELECT COUNT(*) as count FROM doctors WHERE doc_status = 'active'");
            $stats['active_doctors'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

            $stmt = $this->db->prepare("
                SELECT COALESCE(SUM(p.payment_amount), 0) as total_revenue
                FROM payments p
                JOIN appointments a ON p.appointment_id = a.appointment_id
                JOIN payment_statuses ps ON p.payment_status_id = ps.payment_status_id
                WHERE a.doc_id = :doc_id 
                AND a.appointment_date = CURRENT_DATE
                AND LOWER(ps.status_name) = 'paid'
            ");
            $stmt->execute(['doc_id' => $docId]);
            $stats['today_revenue'] = $stmt->fetch(PDO::FETCH_ASSOC)['total_revenue'] ?? 0;

            $stmt = $this->db->prepare("
                SELECT COUNT(DISTINCT mr.pat_id) as count
                FROM medical_records mr
                WHERE mr.doc_id = :doc_id
                AND (mr.follow_up_date IS NOT NULL AND mr.follow_up_date >= CURRENT_DATE)
            ");
            $stmt->execute(['doc_id' => $docId]);
            $stats['admitted_patients'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
        } catch (PDOException $e) {
            // ignore
        }

        $data['stats'] = $stats;

        try {
            $stmt = $this->db->prepare("
                SELECT a.*, 
                       p.pat_first_name, p.pat_last_name, p.pat_date_of_birth, p.pat_email, p.pat_phone,
                       d.doc_first_name, d.doc_last_name,
                       s.status_name, s.status_color,
                       sv.service_name
                FROM appointments a
                JOIN patients p ON a.pat_id = p.pat_id
                JOIN doctors d ON a.doc_id = d.doc_id
                JOIN appointment_statuses s ON a.status_id = s.status_id
                LEFT JOIN services sv ON a.service_id = sv.service_id
                WHERE a.doc_id = :doc_id 
                AND (a.appointment_date = CURRENT_DATE OR a.appointment_date > CURRENT_DATE)
                ORDER BY a.appointment_date ASC, a.appointment_time ASC
                LIMIT 6
            ");
            $stmt->execute(['doc_id' => $docId]);
            $data['recent_appointments'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $data['recent_appointments'] = [];
        }

        try {
            $stmt = $this->db->prepare("
                SELECT a.*, 
                       p.pat_first_name, p.pat_last_name, p.pat_date_of_birth, p.pat_email, p.pat_phone,
                       s.status_name, s.status_color
                FROM appointments a
                JOIN patients p ON a.pat_id = p.pat_id
                JOIN appointment_statuses s ON a.status_id = s.status_id
                WHERE a.doc_id = :doc_id AND a.appointment_date = CURRENT_DATE
                ORDER BY a.appointment_time ASC
                LIMIT 10
            ");
            $stmt->execute(['doc_id' => $docId]);
            $data['today_appointments'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $data['today_appointments'] = [];
        }

        try {
            $stmt = $this->db->prepare("
                SELECT * FROM schedules 
                WHERE doc_id = :doc_id AND schedule_date = CURRENT_DATE
                ORDER BY start_time ASC
            ");
            $stmt->execute(['doc_id' => $docId]);
            $data['today_schedule'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $data['today_schedule'] = [];
        }

        try {
            $stmt = $this->db->prepare("
                SELECT a.*, 
                       p.pat_first_name, p.pat_last_name, p.pat_date_of_birth,
                       s.status_name, s.status_color
                FROM appointments a
                JOIN patients p ON a.pat_id = p.pat_id
                JOIN appointment_statuses s ON a.status_id = s.status_id
                WHERE a.doc_id = :doc_id AND a.appointment_date > CURRENT_DATE
                ORDER BY a.appointment_date ASC, a.appointment_time ASC
                LIMIT 10
            ");
            $stmt->execute(['doc_id' => $docId]);
            $data['upcoming_appointments'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $data['upcoming_appointments'] = [];
        }

        try {
            $stmt = $this->db->prepare("
                SELECT a.*, 
                       p.pat_first_name, p.pat_last_name, p.pat_date_of_birth,
                       s.status_name, s.status_color,
                       sv.service_name,
                       CASE 
                           WHEN EXISTS (
                               SELECT 1 FROM appointments a2 
                               WHERE a2.pat_id = a.pat_id 
                               AND a2.doc_id = a.doc_id 
                               AND a2.appointment_date < a.appointment_date
                           ) THEN 'Follow up'
                           ELSE 'First visit'
                       END as appointment_type,
                       CASE 
                           WHEN EXISTS (
                               SELECT 1 FROM medical_records mr 
                               WHERE mr.appointment_id = a.appointment_id
                           ) THEN 'Yes'
                           ELSE 'No'
                       END as has_report
                FROM appointments a
                JOIN patients p ON a.pat_id = p.pat_id
                JOIN appointment_statuses s ON a.status_id = s.status_id
                LEFT JOIN services sv ON a.service_id = sv.service_id
                WHERE a.doc_id = :doc_id
                ORDER BY a.appointment_date DESC, a.appointment_time DESC
                LIMIT 10
            ");
            $stmt->execute(['doc_id' => $docId]);
            $data['patient_list'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $data['patient_list'] = [];
        }

        try {
            $stmt = $this->db->prepare("
                WITH appointment_types AS (
                    SELECT 
                        a.appointment_id,
                        CASE 
                            WHEN EXISTS (
                                SELECT 1 FROM appointments a2 
                                WHERE a2.pat_id = a.pat_id 
                                AND a2.doc_id = a.doc_id 
                                AND a2.appointment_date < a.appointment_date
                            ) THEN 'Follow up'
                            ELSE 'First visit'
                        END as appointment_type
                    FROM appointments a
                    WHERE a.doc_id = :doc_id
                    AND a.appointment_date >= CURRENT_DATE - INTERVAL '30 days'
                )
                SELECT appointment_type, COUNT(*) as count
                FROM appointment_types
                GROUP BY appointment_type
            ");
            $stmt->execute(['doc_id' => $docId]);
            $appointment_type_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($appointment_type_data as $row) {
                $type = $row['appointment_type'];
                if (isset($data['appointment_type_chart'][$type])) {
                    $data['appointment_type_chart'][$type] = (int)$row['count'];
                }
            }
        } catch (PDOException $e) {
            // keep defaults
        }

        try {
            $weekly_visits = [];
            for ($i = 3; $i >= 0; $i--) {
                $week_start = date('Y-m-d', strtotime("-$i weeks monday"));
                $week_end = date('Y-m-d', strtotime("$week_start +6 days"));

                $stmt = $this->db->prepare("
                    SELECT COUNT(*) as count
                    FROM appointments
                    WHERE doc_id = :doc_id
                    AND appointment_date >= :week_start
                    AND appointment_date <= :week_end
                ");
                $stmt->execute([
                    'doc_id' => $docId,
                    'week_start' => $week_start,
                    'week_end' => $week_end
                ]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $weekly_visits[] = (int)($result['count'] ?? 0);
            }
            $data['weekly_visits'] = $weekly_visits;
        } catch (PDOException $e) {
            $data['weekly_visits'] = [0, 0, 0, 0];
        }

        try {
            $stmt = $this->db->prepare("
                SELECT a.*, 
                       p.pat_first_name, p.pat_last_name, p.pat_date_of_birth,
                       s.status_name, s.status_color,
                       sv.service_name,
                       CASE 
                           WHEN EXISTS (
                               SELECT 1 FROM appointments a2 
                               WHERE a2.pat_id = a.pat_id 
                               AND a2.doc_id = a.doc_id 
                               AND a2.appointment_date < a.appointment_date
                           ) THEN 'Follow up'
                           ELSE 'First Visit'
                       END as appointment_type
                FROM appointments a
                JOIN patients p ON a.pat_id = p.pat_id
                JOIN appointment_statuses s ON a.status_id = s.status_id
                LEFT JOIN services sv ON a.service_id = sv.service_id
                WHERE a.doc_id = :doc_id 
                AND a.appointment_date >= CURRENT_DATE
                ORDER BY a.appointment_date ASC, a.appointment_time ASC
                LIMIT 5
            ");
            $stmt->execute(['doc_id' => $docId]);
            $data['new_appointments'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $data['new_appointments'] = [];
        }

        try {
            $stmt = $this->db->prepare("
                SELECT DISTINCT ON (mr.pat_id) mr.*, 
                       p.pat_first_name, p.pat_last_name,
                       mr.record_date
                FROM medical_records mr
                JOIN patients p ON mr.pat_id = p.pat_id
                WHERE mr.doc_id = :doc_id
                ORDER BY mr.pat_id, mr.record_date DESC
                LIMIT 5
            ");
            $stmt->execute(['doc_id' => $docId]);
            $data['recent_patients'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $data['recent_patients'] = [];
        }

        try {
            $stmt = $this->db->prepare("
                SELECT mr.*, 
                       p.pat_first_name, p.pat_last_name
                FROM medical_records mr
                JOIN patients p ON mr.pat_id = p.pat_id
                WHERE mr.doc_id = :doc_id
                ORDER BY mr.record_date DESC
                LIMIT 5
            ");
            $stmt->execute(['doc_id' => $docId]);
            $data['recent_records'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $data['recent_records'] = [];
        }

        try {
            $stmt = $this->db->prepare("
                SELECT a.appointment_id, a.appointment_date, a.appointment_time,
                       p.pat_first_name, p.pat_last_name,
                       s.status_name
                FROM appointments a
                JOIN patients p ON a.pat_id = p.pat_id
                JOIN appointment_statuses s ON a.status_id = s.status_id
                WHERE a.doc_id = :doc_id AND a.appointment_date >= CURRENT_DATE
                ORDER BY a.appointment_date ASC, a.appointment_time ASC
                LIMIT 5
            ");
            $stmt->execute(['doc_id' => $docId]);
            $data['notifications'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $data['notifications'] = [];
        }

        return $data;
    }

    public function getAppointmentsForDoctor(int $doctorId, string $filter = 'all', array $options = []): array {
        $sortColumn = $options['sort'] ?? 'appointment_date';
        $sortOrder = strtoupper($options['order'] ?? 'DESC');
        $allowedColumns = ['appointment_date', 'appointment_time', 'appointment_id'];
        if (!in_array($sortColumn, $allowedColumns)) {
            $sortColumn = 'appointment_date';
        }
        $sortOrder = $sortOrder === 'ASC' ? 'ASC' : 'DESC';

        $where = ['a.doc_id = :doctor_id'];
        $params = ['doctor_id' => $doctorId];
        $today = date('Y-m-d');

        switch ($filter) {
            case 'today':
                $where[] = 'a.appointment_date = :today';
                $params['today'] = $today;
                break;
            case 'future':
                $where[] = 'a.appointment_date > :today';
                $params['today'] = $today;
                break;
            case 'previous':
                $where[] = 'a.appointment_date < :today';
                $params['today'] = $today;
                break;
            default:
                break;
        }

        $whereClause = 'WHERE ' . implode(' AND ', $where);

        try {
            $stmt = $this->db->prepare("
                SELECT a.*, 
                       p.pat_first_name, p.pat_last_name, p.pat_phone,
                       s.service_name,
                       st.status_name, st.status_color,
                       up.profile_picture_url as patient_profile_picture
                FROM appointments a
                LEFT JOIN patients p ON a.pat_id = p.pat_id
                LEFT JOIN services s ON a.service_id = s.service_id
                LEFT JOIN appointment_statuses st ON a.status_id = st.status_id
                LEFT JOIN users up ON up.pat_id = p.pat_id
                $whereClause
                ORDER BY a.$sortColumn $sortOrder
            ");
            $stmt->execute($params);
            $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $appointments = [];
        }

        $stats = [
            'today' => 0,
            'past' => 0,
            'future' => 0
        ];

        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM appointments WHERE doc_id = :doctor_id AND appointment_date = :today");
            $stmt->execute(['doctor_id' => $doctorId, 'today' => $today]);
            $stats['today'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM appointments WHERE doc_id = :doctor_id AND appointment_date < :today");
            $stmt->execute(['doctor_id' => $doctorId, 'today' => $today]);
            $stats['past'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM appointments WHERE doc_id = :doctor_id AND appointment_date > :today");
            $stmt->execute(['doctor_id' => $doctorId, 'today' => $today]);
            $stats['future'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        } catch (PDOException $e) {
            // keep defaults
        }

        return [
            'appointments' => $appointments,
            'stats' => $stats
        ];
    }

    public function getAppointmentsOverview(int $doctorId, array $options = []): array {
        $sortColumn = $options['sort'] ?? 'appointment_date';
        $sortOrder = strtoupper($options['order'] ?? 'DESC');
        $allowedColumns = ['appointment_date', 'appointment_time', 'appointment_id'];
        if (!in_array($sortColumn, $allowedColumns)) {
            $sortColumn = 'appointment_date';
        }
        if ($sortColumn === 'appointment_date') {
            $orderBy = "a.appointment_date $sortOrder, a.appointment_time $sortOrder";
        } else {
            $sortOrder = $sortOrder === 'ASC' ? 'ASC' : 'DESC';
            $orderBy = "a.$sortColumn $sortOrder";
        }

        $today = date('Y-m-d');
        $previous = [];
        $todayList = [];
        $upcoming = [];

        try {
            $stmt = $this->db->prepare("
                SELECT a.*, 
                       p.pat_first_name, p.pat_last_name, p.pat_phone,
                       s.service_name,
                       st.status_name, st.status_color,
                       up.profile_picture_url as patient_profile_picture,
                       pay.payment_status_id,
                       ps.status_name as payment_status_name,
                       ps.status_color as payment_status_color,
                       pay.payment_reference,
                       pay.payment_amount
                FROM appointments a
                LEFT JOIN patients p ON a.pat_id = p.pat_id
                LEFT JOIN services s ON a.service_id = s.service_id
                LEFT JOIN appointment_statuses st ON a.status_id = st.status_id
                LEFT JOIN users up ON up.pat_id = p.pat_id
                LEFT JOIN (
                    SELECT p1.appointment_id, p1.payment_status_id, p1.payment_reference, p1.payment_amount
                    FROM payments p1
                    INNER JOIN (
                        SELECT appointment_id, MAX(payment_date) as max_date
                        FROM payments
                        GROUP BY appointment_id
                    ) p2 ON p1.appointment_id = p2.appointment_id AND p1.payment_date = p2.max_date
                ) pay ON pay.appointment_id = a.appointment_id
                LEFT JOIN payment_statuses ps ON pay.payment_status_id = ps.payment_status_id
                WHERE a.doc_id = :doctor_id
                ORDER BY $orderBy
            ");
            $stmt->execute(['doctor_id' => $doctorId]);
            $all = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($all as $apt) {
                if ($apt['appointment_date'] < $today) {
                    $previous[] = $apt;
                } elseif ($apt['appointment_date'] === $today) {
                    $todayList[] = $apt;
                } else {
                    $upcoming[] = $apt;
                }
            }

            usort($todayList, function ($a, $b) {
                return strtotime($a['appointment_time']) <=> strtotime($b['appointment_time']);
            });
        } catch (PDOException $e) {
            // keep empty arrays
        }

        return [
            'previous' => $previous,
            'today' => $todayList,
            'upcoming' => $upcoming,
            'counts' => [
                'previous' => count($previous),
                'today' => count($todayList),
                'upcoming' => count($upcoming)
            ]
        ];
    }
}

