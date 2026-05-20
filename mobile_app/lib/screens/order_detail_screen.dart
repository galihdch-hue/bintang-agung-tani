import 'package:flutter/material.dart';
import 'package:phosphor_flutter/phosphor_flutter.dart';
import 'package:qr_flutter/qr_flutter.dart';
import '../theme.dart';
import '../models/models.dart';

class OrderDetailScreen extends StatelessWidget {
  final Order order;

  const OrderDetailScreen({super.key, required this.order});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('Detail Pesanan #${order.id}'),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(20),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            _buildStatusCard(),
            const SizedBox(height: 24),
            _buildQRSection(),
            const SizedBox(height: 24),
            _buildSectionTitle('Informasi Pengiriman'),
            const SizedBox(height: 12),
            _buildAddressCard(),
            const SizedBox(height: 24),
            _buildSectionTitle('Daftar Produk'),
            const SizedBox(height: 12),
            ...order.items.map((item) => _buildOrderItem(item)),
            const SizedBox(height: 24),
            _buildSectionTitle('Rincian Pembayaran'),
            const SizedBox(height: 12),
            _buildPaymentSummary(),
            const SizedBox(height: 40),
          ],
        ),
      ),
    );
  }

  Widget _buildSectionTitle(String title) {
    return Text(
      title,
      style: const TextStyle(fontSize: 16, fontWeight: FontWeight.bold),
    );
  }

  Widget _buildStatusCard() {
    final statusLabel = _getStatusLabel(order.status);
    final statusColor = _getStatusColor(order.status);
    final statusIcon = _getStatusIcon(order.status);

    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: statusColor.withValues(alpha: 0.05),
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: statusColor.withValues(alpha: 0.2)),
      ),
      child: Row(
        children: [
          Icon(statusIcon, color: statusColor),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  statusLabel,
                  style: TextStyle(fontWeight: FontWeight.bold, color: statusColor),
                ),
                Text(
                  '${order.date.day}/${order.date.month}/${order.date.year}',
                  style: const TextStyle(color: AppColors.textSecondary, fontSize: 12),
                ),
              ],
            ),
          ),
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
            decoration: BoxDecoration(
              color: statusColor.withValues(alpha: 0.1),
              borderRadius: BorderRadius.circular(20),
            ),
            child: Text(
              statusLabel,
              style: TextStyle(color: statusColor, fontSize: 11, fontWeight: FontWeight.bold),
            ),
          ),
        ],
      ),
    );
  }

  String _getStatusLabel(String status) {
    switch (status) {
      case 'pending': return 'Belum Bayar';
      case 'menunggu_verifikasi': return 'Menunggu Verifikasi';
      case 'processing': return 'Diproses';
      case 'completed': return 'Selesai';
      case 'cancelled': return 'Dibatalkan';
      default: return status.toUpperCase();
    }
  }

  Color _getStatusColor(String status) {
    switch (status) {
      case 'pending': return Colors.orange;
      case 'menunggu_verifikasi': return Colors.amber[700]!;
      case 'processing': return Colors.blue;
      case 'completed': return Colors.green;
      case 'cancelled': return Colors.red;
      default: return AppColors.textMuted;
    }
  }

  IconData _getStatusIcon(String status) {
    switch (status) {
      case 'pending': return PhosphorIcons.clock();
      case 'menunggu_verifikasi': return PhosphorIcons.hourglass();
      case 'processing': return PhosphorIcons.package();
      case 'completed': return PhosphorIcons.checkCircle();
      case 'cancelled': return PhosphorIcons.xCircle();
      default: return PhosphorIcons.clock();
    }
  }

  Widget _buildQRSection() {
    if (order.status == 'pending') {
      return _buildInfoCard(
        icon: PhosphorIcons.clock(),
        color: Colors.orange,
        title: 'Menunggu Pembayaran',
        message: 'Silakan lakukan pembayaran untuk melanjutkan pesanan.',
      );
    }

    if (order.status == 'menunggu_verifikasi') {
      return _buildInfoCard(
        icon: PhosphorIcons.hourglass(),
        color: Colors.amber[700]!,
        title: 'Menunggu Verifikasi',
        message: 'Bukti pembayaran Anda sedang diverifikasi oleh admin. Barcode QR akan tersedia setelah verifikasi selesai.',
      );
    }

    if (order.status == 'cancelled') {
      return _buildInfoCard(
        icon: PhosphorIcons.xCircle(),
        color: Colors.red,
        title: 'Pesanan Dibatalkan',
        message: 'Pesanan ini telah dibatalkan.',
      );
    }

    // processing or completed - show QR
    return _buildQRCard();
  }

  Widget _buildInfoCard({required IconData icon, required Color color, required String title, required String message}) {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: color.withValues(alpha: 0.05),
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: color.withValues(alpha: 0.2)),
      ),
      child: Column(
        children: [
          Icon(icon, color: color, size: 48),
          const SizedBox(height: 12),
          Text(title, style: TextStyle(fontWeight: FontWeight.bold, color: color, fontSize: 16)),
          const SizedBox(height: 8),
          Text(message, textAlign: TextAlign.center, style: const TextStyle(color: AppColors.textSecondary, fontSize: 13)),
        ],
      ),
    );
  }

  Widget _buildQRCard() {
    final qrData = order.qrCodeData ?? order.orderNumber;

    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: AppColors.border),
      ),
      child: Column(
        children: [
          const Text(
            'QR Code Pesanan',
            style: TextStyle(fontWeight: FontWeight.bold),
          ),
          const SizedBox(height: 8),
          const Text(
            'Tunjukkan kode ini saat pengambilan barang',
            style: TextStyle(color: AppColors.textSecondary, fontSize: 12),
            textAlign: TextAlign.center,
          ),
          const SizedBox(height: 20),
          QrImageView(
            data: qrData,
            version: QrVersions.auto,
            size: 200.0,
            backgroundColor: Colors.white,
            eyeStyle: const QrEyeStyle(eyeShape: QrEyeShape.square, color: Colors.black),
            dataModuleStyle: const QrDataModuleStyle(dataModuleShape: QrDataModuleShape.square, color: Colors.black),
          ),
          const SizedBox(height: 12),
          Text(
            order.orderNumber.isNotEmpty ? order.orderNumber : order.id,
            style: const TextStyle(fontWeight: FontWeight.bold, letterSpacing: 2),
          ),
        ],
      ),
    );
  }

  Widget _buildAddressCard() {
    final addr = order.address;
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: AppColors.border),
      ),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Icon(PhosphorIcons.mapPin(), color: AppColors.primary, size: 20),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Text('Alamat Pengiriman', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 14)),
                const SizedBox(height: 4),
                Text(addr != null ? '${addr.title} - ${addr.name}' : 'No Address Info', style: const TextStyle(fontWeight: FontWeight.w500, fontSize: 13)),
                if (addr != null) ...[
                  Text(addr.phone, style: const TextStyle(color: AppColors.textSecondary, fontSize: 12)),
                  Text(addr.detail, style: const TextStyle(color: AppColors.textSecondary, fontSize: 12)),
                ],
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildOrderItem(OrderItem item) {
    return Container(
      margin: const EdgeInsets.only(bottom: 12),
      child: Row(
        children: [
          Container(
            width: 50,
            height: 50,
            decoration: BoxDecoration(
              color: AppColors.background,
              borderRadius: BorderRadius.circular(8),
            ),
            child: item.image != null
                ? ClipRRect(
                    borderRadius: BorderRadius.circular(8),
                    child: Image.network(
                      item.image!,
                      fit: BoxFit.cover,
                      errorBuilder: (context, error, stackTrace) {
                        return Icon(PhosphorIcons.package(), color: AppColors.textMuted);
                      },
                    ),
                  )
                : Icon(PhosphorIcons.package(), color: AppColors.textMuted),
          ),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  item.name,
                  style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 14),
                ),
                Text(
                  '${item.quantity} x Rp ${formatCurrency(item.price)}',
                  style: const TextStyle(color: AppColors.textSecondary, fontSize: 12),
                ),
              ],
            ),
          ),
          Text(
            'Rp ${formatCurrency(item.price * item.quantity)}',
            style: const TextStyle(fontWeight: FontWeight.bold),
          ),
        ],
      ),
    );
  }

  Widget _buildPaymentSummary() {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: AppColors.border),
      ),
      child: Column(
        children: [
          _buildSummaryRow('Subtotal', 'Rp ${formatCurrency(order.total)}'),
          const Padding(
            padding: EdgeInsets.symmetric(vertical: 12),
            child: Divider(color: AppColors.border),
          ),
          _buildSummaryRow('Total Pembayaran', 'Rp ${formatCurrency(order.total)}', isBold: true),
        ],
      ),
    );
  }

  Widget _buildSummaryRow(String label, String value, {bool isBold = false}) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        Text(
          label,
          style: TextStyle(
            color: isBold ? AppColors.textMain : AppColors.textSecondary,
            fontWeight: isBold ? FontWeight.bold : FontWeight.normal,
          ),
        ),
        Text(
          value,
          style: TextStyle(
            color: isBold ? AppColors.primary : AppColors.textMain,
            fontWeight: isBold ? FontWeight.bold : FontWeight.w600,
            fontSize: isBold ? 16 : 14,
          ),
        ),
      ],
    );
  }
}
