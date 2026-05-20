<?php

namespace App\Services;

use App\Models\Order;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Storage;

class QRCodeService
{
    private const QR_CODE_SIZE = 300;

    private const QR_CODE_MARGIN = 10;

    /**
     * Generate a QR code for an order and save it to storage.
     *
     * @return array{path: string, url: string, data: string}
     */
    public function generateForOrder(Order $order): array
    {
        // Generate QR code data
        $qrData = $order->generateQrCodeData();

        // Create filename based on order number
        $filename = 'qr-codes/'.$order->order_number.'-'.time().'.png';

        // Generate QR code using endroid/qr-code v6.x
        $builder = new Builder(
            writer: new PngWriter,
            data: $qrData,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            size: self::QR_CODE_SIZE,
            margin: self::QR_CODE_MARGIN
        );

        $result = $builder->build();

        // Save to storage disk instead of direct filesystem
        Storage::disk('public')->put($filename, $result->getString());

        // Update order with QR code info
        $order->qr_code_path = $filename;
        $order->qr_code_data = $qrData;
        $order->save();

        return [
            'path' => $filename,
            'url' => Storage::disk('public')->url($filename),
            'data' => $qrData,
        ];
    }

    /**
     * Regenerate QR code for an order (deletes old one first).
     *
     * @return array{path: string, url: string, data: string}
     */
    public function regenerateForOrder(Order $order): array
    {
        // Delete existing QR code if any
        $this->deleteQrCode($order);

        // Generate new QR code
        return $this->generateForOrder($order);
    }

    /**
     * Validate if QR code data matches the given order.
     */
    public function isValidOrderData(string $qrData, Order $order): bool
    {
        try {
            $data = json_decode($qrData, true, 512, JSON_THROW_ON_ERROR);

            return isset($data['order_id']) && (int) $data['order_id'] === (int) $order->id
                && isset($data['order_number']) && $data['order_number'] === $order->order_number;
        } catch (\JsonException $e) {
            return false;
        }
    }

    /**
     * Get order from QR code data.
     */
    public function getOrderFromQrData(string $qrData): ?Order
    {
        try {
            $data = json_decode($qrData, true, 512, JSON_THROW_ON_ERROR);

            if (! isset($data['order_id'])) {
                return null;
            }

            return Order::find($data['order_id']);
        } catch (\JsonException $e) {
            // Fallback: try as order_number string (e.g. "BAT-20260511-XXXX")
            $order = Order::where('order_number', trim($qrData))->first();
            if ($order) {
                return $order;
            }

            return null;
        }
    }

    /**
     * Delete QR code file and clear order columns.
     */
    public function deleteQrCode(Order $order): void
    {
        // Delete file if exists
        if (! empty($order->qr_code_path) && Storage::disk('public')->exists($order->qr_code_path)) {
            Storage::disk('public')->delete($order->qr_code_path);
        }

        // Clear order columns
        $order->qr_code_path = null;
        $order->qr_code_data = null;
        $order->save();
    }
}
