<?php

namespace App\Helpers;

class AdminHelper
{
    /**
     * Generate automatic reference like PRJ-2026-0001
     */
    public static function generateReference($db, $prefix, $year = null)
    {
        $year = $year ?? date('Y');
        $key = $prefix . '-' . $year;
        try {
            $row = $db->fetchOne("SELECT last_number FROM sequences WHERE prefix = ?", [$key]);
            if ($row) {
                $num = $row['last_number'] + 1;
                $db->execute("UPDATE sequences SET last_number = ? WHERE prefix = ?", [$num, $key]);
            } else {
                $num = 1;
                $db->execute("INSERT INTO sequences (prefix, last_number, year) VALUES (?, ?, ?)", [$key, $num, $year]);
            }
        } catch (\Exception $e) {
            // fallback without sequences table
            $counts = [
                'PRJ' => "SELECT COUNT(*) as c FROM projects",
                'LEAD' => "SELECT COUNT(*) as c FROM leads",
                'OPP' => "SELECT COUNT(*) as c FROM opportunities",
                'INV' => "SELECT COUNT(*) as c FROM investments",
                'REQ' => "SELECT COUNT(*) as c FROM requests",
                'TASK' => "SELECT COUNT(*) as c FROM tasks",
                'COMP' => "SELECT COUNT(*) as c FROM companies",
                'COM' => "SELECT COUNT(*) as c FROM commissions",
            ];
            $sql = $counts[$prefix] ?? "SELECT 1 as c";
            try {
                $res = $db->fetchOne($sql);
                $num = ($res['c'] ?? 0) + 1;
            } catch (\Exception $ex) {
                $num = rand(1, 999);
            }
        }
        return $prefix . '-' . $year . '-' . str_pad($num, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Calculate weighted value
     */
    public static function weightedValue($amount, $probability)
    {
        return round($amount * $probability / 100, 2);
    }

    /**
     * Calculate progression
     */
    public static function progression($raised, $objective)
    {
        if ($objective <= 0) return 0;
        return round($raised / $objective * 100, 2);
    }

    /**
     * Calculate commission
     */
    public static function commission($base, $rate)
    {
        return round($base * $rate / 100, 2);
    }

    /**
     * Log audit
     */
    public static function auditLog($db, $userId, $action, $module, $entityType = null, $entityId = null, $oldValue = null, $newValue = null, $description = null)
    {
        try {
            $userName = 'System';
            if ($userId) {
                $u = $db->fetchOne("SELECT first_name, last_name, email FROM users WHERE id = ?", [$userId]);
                if ($u) $userName = trim(($u['first_name'] ?? '') . ' ' . ($u['last_name'] ?? '')) ?: $u['email'];
            }
            $ip = $_SERVER['REMOTE_ADDR'] ?? null;
            $ua = $_SERVER['HTTP_USER_AGENT'] ?? null;
            $db->execute(
                "INSERT INTO audit_logs (user_id, user_name, action, module, entity_type, entity_id, old_value, new_value, description, ip_address, user_agent) VALUES (?,?,?,?,?,?,?,?,?,?,?)",
                [$userId, $userName, $action, $module, $entityType, $entityId, $oldValue ? json_encode($oldValue) : null, $newValue ? json_encode($newValue) : null, $description, $ip, $ua]
            );
        } catch (\Exception $e) {
            error_log("Audit log failed: " . $e->getMessage());
        }
    }

    /**
     * Check duplicate by email/phone/rccm etc
     */
    public static function checkDuplicate($db, $table, $field, $value, $excludeId = null)
    {
        if (empty($value)) return false;
        $sql = "SELECT id FROM $table WHERE $field = ? AND deleted_at IS NULL";
        $params = [$value];
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        $row = $db->fetchOne($sql, $params);
        return $row ? true : false;
    }

    /**
     * Financial coherence checks
     */
    public static function validateFinancial($data, &$errors)
    {
        if (isset($data['amount']) && $data['amount'] < 0) $errors[] = "Montant investi ne peut être négatif";
        if (isset($data['probability']) && ($data['probability'] < 0 || $data['probability'] > 100)) $errors[] = "Probabilité doit être entre 0 et 100%";
        if (isset($data['ticket_min']) && isset($data['ticket_max']) && $data['ticket_min'] > $data['ticket_max']) $errors[] = "Ticket minimum ne peut dépasser ticket maximum";
        if (isset($data['commission']) && isset($data['base_amount']) && $data['commission'] > $data['base_amount']) $errors[] = "Commission ne peut dépasser montant transaction";
        return empty($errors);
    }

    /**
     * Export helpers
     */
    public static function exportHeaders($type, $filename)
    {
        if ($type === 'csv') {
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="'.$filename.'.csv"');
        } elseif ($type === 'excel') {
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment; filename="'.$filename.'.xls"');
        } elseif ($type === 'pdf') {
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="'.$filename.'.pdf"');
        }
    }

    /**
     * Format helpers
     */
    public static function statusColor($status)
    {
        $map = [
            'valide' => 'vert', 'payé' => 'vert', 'gagne' => 'vert', 'approuvé' => 'vert', 'active' => 'vert', 'confirme' => 'vert', 'validée' => 'vert',
            'en_attente' => 'orange', 'en_cours' => 'orange', 'pending' => 'orange', 'suspendu' => 'orange', 'a_faire' => 'orange',
            'rejete' => 'rouge', 'annulé' => 'rouge', 'rejected' => 'rouge', 'expire' => 'rouge', 'perdu' => 'rouge',
            'nouveau' => 'bleu', 'information' => 'bleu',
            'archive' => 'gris', 'cloture' => 'gris', 'suspendue' => 'gris'
        ];
        $key = strtolower(str_replace([' ', '_'], '', $status));
        foreach ($map as $k=>$v) if (strpos($key, str_replace('_','',$k))!==false) return $v;
        return 'bleu';
    }

    public static function getStatusBadge($status, $label = null)
    {
        $label = $label ?: ucfirst(str_replace('_',' ', $status));
        $color = self::statusColor($status);
        $colors = [
            'vert' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
            'orange' => 'bg-amber-100 text-amber-700 border-amber-200',
            'rouge' => 'bg-red-100 text-red-700 border-red-200',
            'bleu' => 'bg-blue-100 text-blue-700 border-blue-200',
            'gris' => 'bg-slate-100 text-slate-600 border-slate-200'
        ];
        $cls = $colors[$color] ?? $colors['bleu'];
        return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border '.$cls.'">'.$label.'</span>';
    }
}
