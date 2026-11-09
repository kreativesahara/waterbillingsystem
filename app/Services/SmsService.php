<?php
namespace App\Services;

use App\Core\Database;

class SmsService
{
    /**
     * Send monthly water bill notification to tenant
     */
    public function sendBillNotification(int $invoiceId): array
    {
        $invoice = Database::fetchOne(
            "SELECT i.*, c.firstname, c.lastname, c.phonenumber, h.house_number
             FROM invoices i
             JOIN consumers c ON i.consumer_id = c.id
             JOIN houses h ON i.house_id = h.id
             WHERE i.id = ?",
            [$invoiceId]
        );

        if (!$invoice) {
            throw new \InvalidArgumentException("Invoice #{$invoiceId} not found");
        }

        $phone = $invoice['phonenumber'];
        $message = "Dear {$invoice['firstname']}, your water bill for House {$invoice['house_number']} ({$invoice['billing_month']}) is KSh " . number_format($invoice['total_bill'], 2) . ". Units consumed: {$invoice['units_consumed']} m3. Due date: {$invoice['due_date']}. Pay via Paybill 174379, Acc: H{$invoice['house_number']}. Thank you!";

        // Log notification in database
        Database::query(
            "INSERT INTO notifications (consumer_id, phone_number, message, type, status)
             VALUES (?, ?, ?, 'BILL_DISPATCH', 'SENT')",
            [$invoice['consumer_id'], $phone, $message]
        );

        return [
            'success' => true,
            'phone' => $phone,
            'message' => $message,
            'tenant' => $invoice['firstname'] . ' ' . $invoice['lastname'],
            'sent_at' => date('Y-m-d H:i:s'),
        ];
    }
}
