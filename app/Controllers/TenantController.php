<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\Database;
use App\Core\Response;

class TenantController
{
    public function dashboard(): void
    {
        $user = Auth::user();

        // Get tenant's assigned house
        $house = null;
        if ($user['house_id']) {
            $house = Database::fetchOne("SELECT * FROM houses WHERE id = ?", [$user['house_id']]);
        }

        // Get current active/latest invoice
        $currentInvoice = Database::fetchOne(
            "SELECT i.*, h.house_number, h.meter_number
             FROM invoices i
             JOIN houses h ON i.house_id = h.id
             WHERE i.consumer_id = ?
             ORDER BY i.created_at DESC LIMIT 1",
            [$user['id']]
        );

        // Get total outstanding arrears for this tenant
        $arrearsRow = Database::fetchOne(
            "SELECT SUM(total_bill - amount_paid) as total_due
             FROM invoices
             WHERE consumer_id = ? AND status IN ('PENDING', 'PARTIAL', 'OVERDUE')",
            [$user['id']]
        );
        $totalOutstanding = (float)($arrearsRow['total_due'] ?? 0.0);

        // Get last 6 months consumption history for the trend bar chart
        $history = Database::fetchAll(
            "SELECT billing_month, units_consumed, water_cost, total_bill, status
             FROM invoices
             WHERE consumer_id = ?
             ORDER BY billing_month ASC LIMIT 6",
            [$user['id']]
        );

        // Get recent payments
        $payments = Database::fetchAll(
            "SELECT p.*, i.invoice_number, i.billing_month
             FROM payments p
             JOIN invoices i ON p.invoice_id = i.id
             WHERE p.consumer_id = ?
             ORDER BY p.created_at DESC LIMIT 5",
            [$user['id']]
        );

        Response::view('tenant/dashboard', [
            'user' => $user,
            'house' => $house,
            'currentInvoice' => $currentInvoice,
            'totalOutstanding' => $totalOutstanding,
            'history' => $history,
            'payments' => $payments,
            'activeNav' => 'tenant_dashboard',
        ], 'main');
    }

    public function statement(): void
    {
        $user = Auth::user();
        $invoiceId = (int)($_GET['id'] ?? 0);

        $invoice = null;
        if ($invoiceId > 0) {
            $invoice = Database::fetchOne(
                "SELECT i.*, h.house_number, h.meter_number, c.firstname, c.lastname, c.phonenumber, c.email
                 FROM invoices i
                 JOIN houses h ON i.house_id = h.id
                 JOIN consumers c ON i.consumer_id = c.id
                 WHERE i.id = ? AND (i.consumer_id = ? OR ? = 'ADMIN')",
                [$invoiceId, $user['id'], $user['role']]
            );
        } else {
            // Get latest
            $invoice = Database::fetchOne(
                "SELECT i.*, h.house_number, h.meter_number, c.firstname, c.lastname, c.phonenumber, c.email
                 FROM invoices i
                 JOIN houses h ON i.house_id = h.id
                 JOIN consumers c ON i.consumer_id = c.id
                 WHERE i.consumer_id = ?
                 ORDER BY i.created_at DESC LIMIT 1",
                [$user['id']]
            );
        }

        if (!$invoice) {
            Response::redirect('/tenant/dashboard');
        }

        // Get payments made for this invoice
        $payments = Database::fetchAll(
            "SELECT * FROM payments WHERE invoice_id = ? ORDER BY created_at ASC",
            [$invoice['id']]
        );

        Response::view('tenant/statement', [
            'user' => $user,
            'invoice' => $invoice,
            'payments' => $payments,
            'activeNav' => 'tenant_statement',
        ], 'main');
    }

    /**
     * Personalized Tenant Payments & Activity Log Page
     */
    public function payments(): void
    {
        $user = Auth::user();
        $isAdmin = ($user['role'] === 'ADMIN');

        if ($isAdmin) {
            $tenantId = (int)($_GET['tenant_id'] ?? 0);
            if ($tenantId <= 0) {
                $firstTenant = Database::fetchOne("SELECT id FROM consumers WHERE role = 'TENANT' ORDER BY id ASC LIMIT 1");
                if (!$firstTenant) {
                    Response::redirect('/admin/tenants');
                }
                $tenantId = (int)$firstTenant['id'];
            }
        } else {
            $tenantId = (int)$user['id'];
        }

        // Fetch target tenant record along with their house info
        $tenant = Database::fetchOne(
            "SELECT c.*, h.house_number, h.meter_number, h.status as house_status
             FROM consumers c
             LEFT JOIN houses h ON c.house_id = h.id
             WHERE c.id = ? AND c.role = 'TENANT'",
            [$tenantId]
        );

        if (!$tenant) {
            Response::redirect($isAdmin ? '/admin/tenants' : '/tenant/dashboard');
        }

        // Filter parameters
        $filterMonth = trim($_GET['month'] ?? '');
        $filterType = strtoupper(trim($_GET['type'] ?? 'ALL')); // ALL, PAYMENT, INVOICE, NOTIFICATION
        $search = trim($_GET['search'] ?? '');

        // Fetch all payments for this tenant (filtered if search/month specified)
        $paymentSql = "SELECT p.*, i.invoice_number, i.billing_month, i.total_bill, i.amount_paid, i.status as invoice_status, h.house_number, h.meter_number
                       FROM payments p
                       JOIN invoices i ON p.invoice_id = i.id
                       JOIN houses h ON i.house_id = h.id
                       WHERE p.consumer_id = ?";
        $paymentParams = [$tenantId];

        if ($filterMonth !== '') {
            $paymentSql .= " AND i.billing_month = ?";
            $paymentParams[] = $filterMonth;
        }

        if ($search !== '') {
            $paymentSql .= " AND (p.mpesa_receipt LIKE ? OR i.invoice_number LIKE ? OR p.phone_number LIKE ? OR i.billing_month LIKE ?)";
            $term = "%{$search}%";
            array_push($paymentParams, $term, $term, $term, $term);
        }

        $paymentSql .= " ORDER BY p.created_at DESC, p.id DESC";
        $payments = Database::fetchAll($paymentSql, $paymentParams);

        // Fetch all invoices for this tenant
        $invoiceSql = "SELECT i.*, h.house_number, h.meter_number
                       FROM invoices i
                       JOIN houses h ON i.house_id = h.id
                       WHERE i.consumer_id = ?";
        $invoiceParams = [$tenantId];

        if ($filterMonth !== '') {
            $invoiceSql .= " AND i.billing_month = ?";
            $invoiceParams[] = $filterMonth;
        }

        if ($search !== '') {
            $invoiceSql .= " AND (i.invoice_number LIKE ? OR i.billing_month LIKE ?)";
            $term = "%{$search}%";
            array_push($invoiceParams, $term, $term);
        }

        $invoiceSql .= " ORDER BY i.billing_month DESC, i.id DESC";
        $invoices = Database::fetchAll($invoiceSql, $invoiceParams);

        // Fetch notifications for this tenant
        $notifSql = "SELECT * FROM notifications WHERE consumer_id = ?";
        $notifParams = [$tenantId];

        if ($search !== '') {
            $notifSql .= " AND (message LIKE ? OR phone_number LIKE ? OR type LIKE ?)";
            $term = "%{$search}%";
            array_push($notifParams, $term, $term, $term);
        }

        $notifSql .= " ORDER BY created_at DESC, id DESC";
        $notifications = Database::fetchAll($notifSql, $notifParams);

        // Compute lifetime KPIs (unfiltered for accurate account summary)
        $allPaymentsSummary = Database::fetchOne(
            "SELECT COUNT(*) as payment_count, COALESCE(SUM(amount), 0) as total_paid
             FROM payments
             WHERE consumer_id = ? AND status = 'COMPLETED'",
            [$tenantId]
        );

        $allInvoicesSummary = Database::fetchOne(
            "SELECT COUNT(*) as total_invoices,
                    SUM(CASE WHEN status = 'PAID' THEN 1 ELSE 0 END) as paid_invoices,
                    COALESCE(SUM(CASE WHEN status IN ('PENDING', 'PARTIAL', 'OVERDUE') THEN (total_bill - amount_paid) ELSE 0 END), 0) as outstanding_balance
             FROM invoices
             WHERE consumer_id = ?",
            [$tenantId]
        );

        $lastPayment = Database::fetchOne(
            "SELECT p.*, i.invoice_number, i.billing_month
             FROM payments p
             JOIN invoices i ON p.invoice_id = i.id
             WHERE p.consumer_id = ?
             ORDER BY p.created_at DESC, p.id DESC LIMIT 1",
            [$tenantId]
        );

        $activeInvoice = Database::fetchOne(
            "SELECT * FROM invoices
             WHERE consumer_id = ? AND status IN ('PENDING', 'PARTIAL', 'OVERDUE')
             ORDER BY created_at DESC, id DESC LIMIT 1",
            [$tenantId]
        );

        // Distinct billing months for this tenant for the filter dropdown
        $monthRows = Database::fetchAll(
            "SELECT DISTINCT billing_month FROM invoices WHERE consumer_id = ? ORDER BY billing_month DESC",
            [$tenantId]
        );
        $availableMonths = array_column($monthRows, 'billing_month');

        // Build unified chronological activity log
        $activityLog = [];

        if ($filterType === 'ALL' || $filterType === 'PAYMENT') {
            foreach ($payments as $p) {
                $activityLog[] = [
                    'type' => 'PAYMENT',
                    'timestamp' => $p['created_at'],
                    'title' => 'M-Pesa Payment Received — KSh ' . number_format((float)$p['amount'], 2),
                    'subtitle' => sprintf(
                        'Receipt: %s • Applied to Invoice %s (%s) • Paid from %s',
                        $p['mpesa_receipt'] ?: 'N/A',
                        $p['invoice_number'],
                        $p['billing_month'],
                        $p['phone_number']
                    ),
                    'badge' => $p['status'],
                    'badge_color' => 'emerald',
                    'amount' => (float)$p['amount'],
                    'invoice_id' => (int)$p['invoice_id'],
                    'receipt' => $p['mpesa_receipt'],
                    'raw' => $p,
                ];
            }
        }

        if ($filterType === 'ALL' || $filterType === 'INVOICE') {
            foreach ($invoices as $inv) {
                $activityLog[] = [
                    'type' => 'INVOICE',
                    'timestamp' => $inv['created_at'] ?: ($inv['date_of_reading'] . ' 09:00:00'),
                    'title' => 'Water Bill Generated (' . $inv['billing_month'] . ') — KSh ' . number_format((float)$inv['total_bill'], 2),
                    'subtitle' => sprintf(
                        'Invoice %s • Meter Dial: %s → %s (%s m³) • Due: %s',
                        $inv['invoice_number'],
                        number_format((float)$inv['previous_reading'], 1),
                        number_format((float)$inv['current_reading'], 1),
                        number_format((float)$inv['units_consumed'], 1),
                        $inv['due_date']
                    ),
                    'badge' => $inv['status'],
                    'badge_color' => $inv['status'] === 'PAID' ? 'emerald' : ($inv['status'] === 'PARTIAL' ? 'amber' : 'rose'),
                    'amount' => (float)$inv['total_bill'],
                    'invoice_id' => (int)$inv['id'],
                    'receipt' => null,
                    'raw' => $inv,
                ];
            }
        }

        if ($filterType === 'ALL' || $filterType === 'NOTIFICATION') {
            foreach ($notifications as $n) {
                if ($filterMonth !== '' && !str_contains($n['message'], $filterMonth)) {
                    continue;
                }
                $isReceiptSms = ($n['type'] === 'PAYMENT_RECEIPT');
                $activityLog[] = [
                    'type' => 'NOTIFICATION',
                    'timestamp' => $n['created_at'],
                    'title' => $isReceiptSms ? 'Payment Confirmation SMS Sent' : 'Monthly Bill SMS Dispatched',
                    'subtitle' => 'To ' . $n['phone_number'] . ': "' . $n['message'] . '"',
                    'badge' => $n['status'],
                    'badge_color' => 'sky',
                    'amount' => null,
                    'invoice_id' => null,
                    'receipt' => null,
                    'raw' => $n,
                ];
            }
        }

        // Sort activity log newest first
        usort($activityLog, function (array $a, array $b): int {
            return strcmp((string)$b['timestamp'], (string)$a['timestamp']);
        });

        // If admin is viewing, fetch list of all tenants for the quick switcher
        $allTenants = [];
        if ($isAdmin) {
            $allTenants = Database::fetchAll(
                "SELECT c.id, c.firstname, c.lastname, c.phonenumber, h.house_number
                 FROM consumers c
                 LEFT JOIN houses h ON c.house_id = h.id
                 WHERE c.role = 'TENANT'
                 ORDER BY CAST(h.house_number AS UNSIGNED) ASC, c.id ASC"
            );
        }

        Response::view('tenant/payments', [
            'user' => $user,
            'isAdmin' => $isAdmin,
            'tenant' => $tenant,
            'allTenants' => $allTenants,
            'payments' => $payments,
            'invoices' => $invoices,
            'activityLog' => $activityLog,
            'kpis' => [
                'total_paid' => (float)($allPaymentsSummary['total_paid'] ?? 0),
                'payment_count' => (int)($allPaymentsSummary['payment_count'] ?? 0),
                'outstanding_balance' => (float)($allInvoicesSummary['outstanding_balance'] ?? 0),
                'total_invoices' => (int)($allInvoicesSummary['total_invoices'] ?? 0),
                'paid_invoices' => (int)($allInvoicesSummary['paid_invoices'] ?? 0),
                'last_payment' => $lastPayment,
            ],
            'activeInvoice' => $activeInvoice,
            'availableMonths' => $availableMonths,
            'filterMonth' => $filterMonth,
            'filterType' => $filterType,
            'search' => $search,
            'activeNav' => $isAdmin ? 'tenants' : 'tenant_payments',
            'pageTitle' => $tenant['firstname'] . ' ' . $tenant['lastname'] . ' - Payments & Activity Log',
        ], 'main');
    }
}
