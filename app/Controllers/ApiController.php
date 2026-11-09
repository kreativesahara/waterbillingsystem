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
}
