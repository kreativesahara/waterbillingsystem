<?php
namespace App\Services;

use App\Core\Database;

class BillingService
{
    private float $defaultRate;
    private float $defaultRent;

    public function __construct()
    {
        $config = require __DIR__ . '/../config.php';
        $this->defaultRate = (float)($config['billing']['default_rate_per_unit'] ?? 150.00);
        $this->defaultRent = (float)($config['billing']['default_meter_rent'] ?? 50.00);
    }

    /**
     * Get latest recorded reading for a specific house
     */
    public function getLatestReading(int $houseId): float
    {
        $row = Database::fetchOne(
            "SELECT current_reading FROM meter_readings WHERE house_id = ? ORDER BY date_of_reading DESC, id DESC LIMIT 1",
            [$houseId]
        );

        return $row ? (float)$row['current_reading'] : 0.0;
    }

    /**
     * Get unpaid arrears (balance brought down) for a house
     */
    public function getArrears(int $houseId): float
    {
        $row = Database::fetchOne(
            "SELECT SUM(total_bill - amount_paid) as outstanding 
             FROM invoices 
             WHERE house_id = ? AND status IN ('PENDING', 'PARTIAL', 'OVERDUE')",
            [$houseId]
        );

        return $row && $row['outstanding'] ? max(0.0, (float)$row['outstanding']) : 0.0;
    }

    /**
     * Calculate bill components live
     */
    public function calculate(int $houseId, float $currentReading, ?float $rate = null, ?float $rent = null): array
    {
        $rate = $rate ?? $this->defaultRate;
        $rent = $rent ?? $this->defaultRent;
        $prevReading = $this->getLatestReading($houseId);
        $arrears = $this->getArrears($houseId);

        $unitsConsumed = max(0.0, $currentReading - $prevReading);
        $waterCost = $unitsConsumed * $rate;
        $totalBill = $waterCost + $rent + $arrears;

        $isNegative = $currentReading < $prevReading;
        $isAbnormal = $unitsConsumed > 40.0; // Flag high consumption warning

        return [
            'house_id' => $houseId,
            'previous_reading' => $prevReading,
            'current_reading' => $currentReading,
            'units_consumed' => $unitsConsumed,
            'rate_per_unit' => $rate,
            'water_cost' => $waterCost,
            'meter_rent' => $rent,
            'arrears' => $arrears,
            'total_bill' => $totalBill,
            'warnings' => [
                'negative_reading' => $isNegative,
                'high_consumption' => $isAbnormal,
            ],
        ];
    }

    /**
     * Persist meter reading and generate invoice atomically
     */
    public function recordReadingAndBill(array $data): array
    {
        $houseId = (int)$data['house_id'];
        $billingMonth = $data['billing_month']; // e.g. '2026-11'
        $dateOfReading = $data['date_of_reading'] ?? date('Y-m-d');
        $currentReading = (float)$data['current_reading'];
        $recordedBy = $data['recorded_by'] ?? 1;

        // Check if bill already generated for this house in this billing month
        $existing = Database::fetchOne(
            "SELECT id FROM invoices WHERE house_id = ? AND billing_month = ?",
            [$houseId, $billingMonth]
        );
        if ($existing) {
            throw new \InvalidArgumentException("Bill for House {$houseId} for month {$billingMonth} has already been generated.");
        }

        // Get tenant for this house
        $tenant = Database::fetchOne(
            "SELECT id, firstname, lastname, phonenumber FROM consumers WHERE house_id = ? AND role = 'TENANT' LIMIT 1",
            [$houseId]
        );
        if (!$tenant) {
            throw new \RuntimeException("No active tenant assigned to House #{$houseId}. Please assign a tenant first.");
        }

        $calc = $this->calculate($houseId, $currentReading);

        Database::beginTransaction();
        try {
            // 1. Insert meter reading
            Database::query(
                "INSERT INTO meter_readings (house_id, billing_month, date_of_reading, previous_reading, current_reading, units_consumed, recorded_by)
                 VALUES (?, ?, ?, ?, ?, ?, ?)",
                [
                    $houseId,
                    $billingMonth,
                    $dateOfReading,
                    $calc['previous_reading'],
                    $calc['current_reading'],
                    $calc['units_consumed'],
                    $recordedBy
                ]
            );

            // 2. Generate Invoice Number (e.g. INV-202611-H01)
            $invNumber = sprintf("INV-%s-H%02d-%d", str_replace('-', '', $billingMonth), $houseId, rand(100, 999));
            $dueDate = date('Y-m-d', strtotime($dateOfReading . ' + 10 days'));

            // 3. Insert Invoice
            Database::query(
                "INSERT INTO invoices (invoice_number, house_id, consumer_id, billing_month, date_of_reading, previous_reading, current_reading, units_consumed, cost_per_unit, water_cost, meter_rent, bbd, total_bill, amount_paid, status, due_date)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0.00, 'PENDING', ?)",
                [
                    $invNumber,
                    $houseId,
                    $tenant['id'],
                    $billingMonth,
                    $dateOfReading,
                    $calc['previous_reading'],
                    $calc['current_reading'],
                    $calc['units_consumed'],
                    $calc['rate_per_unit'],
                    $calc['water_cost'],
                    $calc['meter_rent'],
                    $calc['arrears'],
                    $calc['total_bill'],
                    $dueDate
                ]
            );
            $invoiceId = (int)Database::lastInsertId();

            Database::commit();

            return [
                'success' => true,
                'invoice_id' => $invoiceId,
                'invoice_number' => $invNumber,
                'house_id' => $houseId,
                'tenant_name' => $tenant['firstname'] . ' ' . $tenant['lastname'],
                'tenant_phone' => $tenant['phonenumber'],
                'total_bill' => $calc['total_bill'],
                'units_consumed' => $calc['units_consumed'],
            ];
        } catch (\Exception $e) {
            Database::rollBack();
            throw $e;
        }
    }

    /**
     * Retrieve executive KPI summary for admin dashboard
     */
    public function getExecutiveMetrics(): array
    {
        $currentMonth = date('Y-m');

        // Total Billed this month
        $billedRow = Database::fetchOne(
            "SELECT SUM(total_bill) as total_billed, SUM(amount_paid) as total_collected, SUM(units_consumed) as total_units
             FROM invoices WHERE billing_month = ?",
            [$currentMonth]
        );

        // Overall Arrears across all uncollected invoices
        $arrearsRow = Database::fetchOne(
            "SELECT SUM(total_bill - amount_paid) as outstanding FROM invoices WHERE status != 'PAID'"
        );

        // Occupancy count
        $occupancyRow = Database::fetchOne(
            "SELECT COUNT(*) as total_units, SUM(CASE WHEN status = 'OCCUPIED' THEN 1 ELSE 0 END) as occupied_units FROM houses"
        );

        $totalBilled = (float)($billedRow['total_billed'] ?? 0);
        $totalCollected = (float)($billedRow['total_collected'] ?? 0);
        $collectionRate = $totalBilled > 0 ? round(($totalCollected / $totalBilled) * 100, 1) : 0;

        return [
            'current_month' => $currentMonth,
            'total_billed' => $totalBilled,
            'total_collected' => $totalCollected,
            'total_units_consumed' => (float)($billedRow['total_units'] ?? 0),
            'outstanding_arrears' => (float)($arrearsRow['outstanding'] ?? 0),
            'total_houses' => (int)($occupancyRow['total_units'] ?? 18),
            'occupied_houses' => (int)($occupancyRow['occupied_units'] ?? 0),
            'collection_rate' => $collectionRate,
        ];
    }
}
