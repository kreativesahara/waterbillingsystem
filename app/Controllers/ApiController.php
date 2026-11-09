<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\Response;
use App\Services\BillingService;
use App\Services\MPesaService;
use App\Services\SmsService;

class ApiController
{
    private BillingService $billingService;
    private MPesaService $mpesaService;
    private SmsService $smsService;

    public function __construct()
    {
        $this->billingService = new BillingService();
        $this->mpesaService = new MPesaService();
        $this->smsService = new SmsService();
    }

    /**
     * Real-time calculation preview
     * GET /api/calculate?house_id=1&current_reading=147
     */
    public function calculate(): void
    {
        $houseId = (int)($_GET['house_id'] ?? 0);
        $currentReading = (float)($_GET['current_reading'] ?? 0);

        if ($houseId <= 0) {
            Response::json(['error' => 'Invalid house_id'], 400);
        }

        $calc = $this->billingService->calculate($houseId, $currentReading);
        Response::json(['success' => true, 'data' => $calc]);
    }

    /**
     * Submit meter reading & generate invoice
     * POST /api/meter-reading
     */
    public function recordReading(): void
    {
        Auth::requireAdmin();

        $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;

        if (empty($data['house_id']) || !isset($data['current_reading']) || empty($data['billing_month'])) {
            Response::json(['error' => 'Missing required fields: house_id, current_reading, billing_month'], 400);
        }

        try {
            $data['recorded_by'] = Auth::user()['id'];
            $result = $this->billingService->recordReadingAndBill($data);
            Response::json($result);
        } catch (\InvalidArgumentException $e) {
            Response::json(['error' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            Response::json(['error' => 'Server error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Initiate M-Pesa STK Push payment
     * POST /api/pay/stk-push
     */
    public function stkPush(): void
    {
        $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;

        $invoiceId = (int)($data['invoice_id'] ?? 0);
        $phone = $data['phone'] ?? '';
        $amount = (float)($data['amount'] ?? 0);

        if ($invoiceId <= 0 || empty($phone) || $amount <= 0) {
            Response::json(['error' => 'Invalid payment parameters'], 400);
        }

        try {
            $res = $this->mpesaService->initiateStkPush($invoiceId, $phone, $amount);
            Response::json($res);
        } catch (\Exception $e) {
            Response::json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Dispatch SMS notification for an invoice
     * POST /api/send-sms
     */
    public function sendSms(): void
    {
        Auth::requireAdmin();

        $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $invoiceId = (int)($data['invoice_id'] ?? 0);

        if ($invoiceId <= 0) {
            Response::json(['error' => 'Invalid invoice_id'], 400);
        }

        try {
            $res = $this->smsService->sendBillNotification($invoiceId);
            Response::json($res);
        } catch (\Exception $e) {
            Response::json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get sharing details & pre-formatted text for a single invoice
     * GET /api/invoice-share-details?id=123
     */
    public function getInvoiceShareDetails(): void
    {
        Auth::requireAdmin();

        $invoiceId = (int)($_GET['id'] ?? 0);
        if ($invoiceId <= 0) {
            Response::json(['error' => 'Invalid invoice id'], 400);
        }

        $inv = \App\Core\Database::fetchOne(
            "SELECT i.*, h.house_number, h.meter_number, c.firstname, c.lastname, c.phonenumber
             FROM invoices i
             JOIN houses h ON i.house_id = h.id
             JOIN consumers c ON i.consumer_id = c.id
             WHERE i.id = ?",
            [$invoiceId]
        );

        if (!$inv) {
            Response::json(['error' => 'Invoice not found'], 404);
        }

        $formattedMsg = $this->billingService->formatInvoiceMessage($inv);

        // Normalize Kenyan phone number for WhatsApp E.164: 07XXXXXXXX -> 2547XXXXXXXX
        $rawPhone = preg_replace('/[^0-9]/', '', $inv['phonenumber']);
        if (str_starts_with($rawPhone, '0')) {
            $intPhone = '254' . substr($rawPhone, 1);
        } elseif (str_starts_with($rawPhone, '254')) {
            $intPhone = $rawPhone;
        } else {
            $intPhone = '254' . $rawPhone;
        }

        $whatsappUrl = 'https://wa.me/' . $intPhone . '?text=' . rawurlencode($formattedMsg);
        $smsUrl = 'sms:' . $inv['phonenumber'] . '?body=' . rawurlencode($formattedMsg);

        Response::json([
            'success' => true,
            'invoice' => $inv,
            'message' => $formattedMsg,
            'int_phone' => $intPhone,
            'whatsapp_url' => $whatsappUrl,
            'sms_url' => $smsUrl,
        ]);
    }

    /**
     * Get building-wide schedule and WhatsApp broadcast message
     * GET /api/building-summary?month=2026-10
     */
    public function getBuildingSummary(): void
    {
        Auth::requireAdmin();

        $month = $_GET['month'] ?? null;
        $summary = $this->billingService->generateBuildingSummary($month);

        $whatsappBroadcastUrl = 'https://wa.me/?text=' . rawurlencode($summary['building_message']);
        $whatsappReminderUrl = 'https://wa.me/?text=' . rawurlencode($summary['reminder_message']);

        $summary['whatsapp_broadcast_url'] = $whatsappBroadcastUrl;
        $summary['whatsapp_reminder_url'] = $whatsappReminderUrl;

        Response::json([
            'success' => true,
            'data' => $summary,
        ]);
    }

    /**
     * Dispatch bulk SMS to all pending tenants in a billing month
     * POST /api/bulk-sms
     */
    public function bulkSms(): void
    {
        Auth::requireAdmin();

        $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $month = $data['month'] ?? null;

        $summary = $this->billingService->generateBuildingSummary($month);
        $pending = $summary['pending_invoices'];

        if (empty($pending)) {
            Response::json(['success' => true, 'count' => 0, 'message' => 'No pending invoices found for this month. All bills are settled!']);
        }

        $sentCount = 0;
        $recipients = [];

        foreach ($pending as $inv) {
            try {
                $this->smsService->sendBillNotification((int)$inv['id']);
                $sentCount++;
                $recipients[] = "House {$inv['house_number']} ({$inv['firstname']})";
            } catch (\Exception $e) {
                // Continue with next
            }
        }

        Response::json([
            'success' => true,
            'count' => $sentCount,
            'recipients' => $recipients,
            'message' => "Successfully dispatched SMS bill reminders to {$sentCount} pending tenant(s).",
        ]);
    }
}
