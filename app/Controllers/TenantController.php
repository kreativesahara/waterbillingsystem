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
}
