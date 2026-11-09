<?php
namespace App\Services;

use App\Core\Database;

class MPesaService
{
    private array $config;

    public function __construct()
    {
        $appConfig = require __DIR__ . '/../config.php';
        $this->config = $appConfig['mpesa'] ?? [];
    }

    /**
     * Trigger Lipa na M-Pesa Online (STK Push)
     * In this prototype, it provides a realistic interactive flow that completes payment
     */
    public function initiateStkPush(int $invoiceId, string $phoneNumber, float $amount): array
    {
        $invoice = Database::fetchOne("SELECT * FROM invoices WHERE id = ?", [$invoiceId]);
        if (!$invoice) {
            throw new \InvalidArgumentException("Invoice #{$invoiceId} not found");
        }

        // Clean phone number format to 2547XXXXXXXX or 07XXXXXXXX
        $cleanPhone = preg_replace('/[^0-9]/', '', $phoneNumber);
        if (str_starts_with($cleanPhone, '0')) {
            $formattedPhone = '254' . substr($cleanPhone, 1);
        } else {
            $formattedPhone = $cleanPhone;
        }

        $checkoutRequestId = 'ws_CO_' . date('dmYHis') . rand(1000, 9999);
        $receipt = 'QRT' . strtoupper(substr(md5($checkoutRequestId . $invoiceId), 0, 7));

        // Record the payment in payments table
        Database::beginTransaction();
        try {
            Database::query(
                "INSERT INTO payments (invoice_id, consumer_id, amount, payment_method, mpesa_receipt, phone_number, status)
                 VALUES (?, ?, ?, 'MPESA_STK', ?, ?, 'COMPLETED')",
                [
                    $invoiceId,
                    $invoice['consumer_id'],
                    $amount,
                    $receipt,
                    $phoneNumber
                ]
            );

            // Update invoice status
            $newPaid = (float)$invoice['amount_paid'] + $amount;
            $newStatus = ($newPaid >= (float)$invoice['total_bill']) ? 'PAID' : 'PARTIAL';

            Database::query(
                "UPDATE invoices SET amount_paid = ?, status = ? WHERE id = ?",
                [$newPaid, $newStatus, $invoiceId]
            );

            // Log a payment confirmation entry in notifications for the tenant's activity log
            $balanceRemaining = max(0.0, (float)$invoice['total_bill'] - $newPaid);
            $notifMsg = sprintf(
                "M-Pesa Payment Confirmed (Ref: %s). KSh %s received for Invoice %s (%s). Remaining balance: KSh %s.",
                $receipt,
                number_format($amount, 2),
                $invoice['invoice_number'],
                $invoice['billing_month'],
                number_format($balanceRemaining, 2)
            );
            Database::query(
                "INSERT INTO notifications (consumer_id, phone_number, message, type, status)
                 VALUES (?, ?, ?, 'PAYMENT_RECEIPT', 'SENT')",
                [$invoice['consumer_id'], $phoneNumber, $notifMsg]
            );

            Database::commit();

            return [
                'success' => true,
                'checkout_request_id' => $checkoutRequestId,
                'mpesa_receipt' => $receipt,
                'amount' => $amount,
                'phone' => $formattedPhone,
                'invoice_number' => $invoice['invoice_number'],
                'new_status' => $newStatus,
                'message' => "Payment of KSh " . number_format($amount, 2) . " processed successfully via M-Pesa.",
            ];
        } catch (\Exception $e) {
            Database::rollBack();
            throw $e;
        }
    }
}
