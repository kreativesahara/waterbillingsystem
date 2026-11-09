<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\Database;
use App\Core\Response;
use App\Services\BillingService;

class AdminController
{
    private BillingService $billingService;

    public function __construct()
    {
        $this->billingService = new BillingService();
    }

    public function dashboard(): void
    {
        $metrics = $this->billingService->getExecutiveMetrics();

        // Recent 10 invoices
        $recentInvoices = Database::fetchAll(
            "SELECT i.*, h.house_number, c.firstname, c.lastname, c.phonenumber
             FROM invoices i
             JOIN houses h ON i.house_id = h.id
             JOIN consumers c ON i.consumer_id = c.id
             ORDER BY i.created_at DESC LIMIT 10"
        );

        Response::view('admin/dashboard', [
            'user' => Auth::user(),
            'metrics' => $metrics,
            'recentInvoices' => $recentInvoices,
            'activeNav' => 'dashboard',
        ], 'main');
    }

    public function meterEntry(): void
    {
        // Fetch all occupied houses with current tenant and latest reading
        $houses = Database::fetchAll(
            "SELECT h.*, c.id as tenant_id, c.firstname, c.lastname, c.phonenumber
             FROM houses h
             LEFT JOIN consumers c ON h.id = c.house_id AND c.role = 'TENANT'
             ORDER BY CAST(h.house_number AS UNSIGNED) ASC"
        );

        $defaultMonth = date('Y-m');
        $defaultDate = date('Y-m-d');

        Response::view('admin/meter_entry', [
            'user' => Auth::user(),
            'houses' => $houses,
            'defaultMonth' => $defaultMonth,
            'defaultDate' => $defaultDate,
            'activeNav' => 'meter_entry',
        ], 'main');
    }

    public function records(): void
    {
        $filterMonth = $_GET['month'] ?? '';
        $filterStatus = $_GET['status'] ?? '';
        $filterUnit = ctype_digit($_GET['unit'] ?? '') ? (int)$_GET['unit'] : 0; // houses.id
        $search = trim($_GET['search'] ?? '');

        $sql = "SELECT i.*, h.house_number, h.meter_number, c.firstname, c.lastname, c.phonenumber
                FROM invoices i
                JOIN houses h ON i.house_id = h.id
                JOIN consumers c ON i.consumer_id = c.id
                WHERE 1=1";
        $params = [];

        if (!empty($filterMonth)) {
            $sql .= " AND i.billing_month = ?";
            $params[] = $filterMonth;
        }

        if (!empty($filterStatus)) {
            $sql .= " AND i.status = ?";
            $params[] = $filterStatus;
        }

        if ($filterUnit > 0) {
            $sql .= " AND i.house_id = ?";
            $params[] = $filterUnit;
        }

        if (!empty($search)) {
            $sql .= " AND (h.house_number LIKE ? OR c.firstname LIKE ? OR c.lastname LIKE ? OR c.phonenumber LIKE ?)";
            $term = "%$search%";
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
        }

        $sql .= " ORDER BY i.id DESC";

        $records = Database::fetchAll($sql, $params);

        // Fetch distinct available months for dropdown
        $months = Database::fetchAll("SELECT DISTINCT billing_month FROM invoices ORDER BY billing_month DESC");

        // Units for the dropdown
        $units = Database::fetchAll("SELECT id, house_number FROM houses ORDER BY CAST(house_number AS UNSIGNED) ASC, house_number ASC");

        Response::view('admin/records', [
            'user' => Auth::user(),
            'records' => $records,
            'months' => array_column($months, 'billing_month'),
            'units' => $units,
            'filterMonth' => $filterMonth,
            'filterStatus' => $filterStatus,
            'filterUnit' => $filterUnit,
            'search' => $search,
            'activeNav' => 'records',
        ], 'main');
    }

    public function tenants(): void
    {
        $units = Database::fetchAll(
            "SELECT h.*, c.id as consumer_id, c.firstname, c.lastname, c.email, c.phonenumber, c.national_id,
                    (SELECT current_reading FROM meter_readings WHERE house_id = h.id ORDER BY id DESC LIMIT 1) as last_reading,
                    (SELECT SUM(total_bill - amount_paid) FROM invoices WHERE house_id = h.id AND status != 'PAID') as current_arrears
             FROM houses h
             LEFT JOIN consumers c ON h.id = c.house_id AND c.role = 'TENANT'
             ORDER BY CAST(h.house_number AS UNSIGNED) ASC"
        );

        Response::view('admin/tenants', [
            'user' => Auth::user(),
            'units' => $units,
            'activeNav' => 'tenants',
        ], 'main');
    }
}
