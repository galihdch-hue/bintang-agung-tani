import 'package:flutter/material.dart';
import 'package:phosphor_flutter/phosphor_flutter.dart';
import '../theme.dart';
import '../services/api_service.dart';
import 'upload_proof_screen.dart';

class PaymentMethodScreen extends StatefulWidget {
  final int orderId;
  final String orderNumber;
  final double totalAmount;

  const PaymentMethodScreen({
    super.key,
    required this.orderId,
    required this.orderNumber,
    required this.totalAmount,
  });

  @override
  State<PaymentMethodScreen> createState() => _PaymentMethodScreenState();
}

class _PaymentMethodScreenState extends State<PaymentMethodScreen> {
  late Future<List<Map<String, dynamic>>> _paymentMethods;
  int? _selectedMethodId;
  bool _isLoading = false;

  String _formatPrice(double price) {
    return 'Rp ${formatCurrency(price)}';
  }

  @override
  void initState() {
    super.initState();
    _paymentMethods = ApiService.getPaymentMethods();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Pilih Metode Pembayaran'),
      ),
      body: FutureBuilder<List<Map<String, dynamic>>>(
        future: _paymentMethods,
        builder: (context, snapshot) {
          if (snapshot.connectionState == ConnectionState.waiting) {
            return const Center(child: CircularProgressIndicator());
          }

          if (snapshot.hasError) {
            return Center(child: Text('Gagal memuat: ${snapshot.error}'));
          }

          final methods = snapshot.data ?? [];

          return ListView(
            padding: const EdgeInsets.all(20),
            children: [
              _buildOrderSummary(),
              const SizedBox(height: 24),
              const Text(
                'Metode Pembayaran Tersedia',
                style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold),
              ),
              const SizedBox(height: 16),
              ...methods.map((method) => _buildPaymentMethodCard(method)),
              const SizedBox(height: 32),
              ElevatedButton(
                onPressed: _selectedMethodId == null || _isLoading ? null : _handleSelectMethod,
                child: _isLoading
                    ? const SizedBox(width: 20, height: 20, child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white))
                    : const Text('Lanjut Upload Bukti'),
              ),
              const SizedBox(height: 16),
              OutlinedButton(
                onPressed: () => Navigator.pop(context),
                style: OutlinedButton.styleFrom(
                  minimumSize: const Size(double.infinity, 54),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                ),
                child: const Text('Batal'),
              ),
            ],
          );
        },
      ),
    );
  }

  Widget _buildOrderSummary() {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: AppColors.primaryBg,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: AppColors.primary.withValues(alpha: 0.2)),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            'Order: ${widget.orderNumber}',
            style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 14),
          ),
          const SizedBox(height: 8),
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              const Text('Total Pembayaran', style: TextStyle(color: AppColors.textSecondary)),
              Text(
                _formatPrice(widget.totalAmount),
                style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 16, color: AppColors.primary),
              ),
            ],
          ),
          const SizedBox(height: 8),
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
            decoration: BoxDecoration(
              color: Colors.orange.withValues(alpha: 0.1),
              borderRadius: BorderRadius.circular(20),
            ),
            child: const Text(
              'Belum Bayar',
              style: TextStyle(color: Colors.orange, fontSize: 12, fontWeight: FontWeight.bold),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildPaymentMethodCard(Map<String, dynamic> method) {
    final id = method['id'] as int;
    final isSelected = _selectedMethodId == id;

    return GestureDetector(
      onTap: () => setState(() => _selectedMethodId = id),
      child: Container(
        margin: const EdgeInsets.only(bottom: 12),
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(16),
          border: Border.all(
            color: isSelected ? AppColors.primary : AppColors.border,
            width: isSelected ? 2 : 1,
          ),
        ),
        child: Row(
          children: [
            Container(
              width: 48,
              height: 48,
              decoration: BoxDecoration(
                color: isSelected ? AppColors.primaryBg : AppColors.background,
                borderRadius: BorderRadius.circular(12),
              ),
              child: Icon(PhosphorIcons.bank(), color: isSelected ? AppColors.primary : AppColors.textMuted),
            ),
            const SizedBox(width: 16),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    method['bank_name'] ?? method['name'] ?? 'Bank',
                    style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 15),
                  ),
                  const SizedBox(height: 4),
                  Text(
                    method['account_number'] ?? '',
                    style: const TextStyle(color: AppColors.textSecondary, fontSize: 13),
                  ),
                  Text(
                    'a.n. ${method['account_name'] ?? ''}',
                    style: const TextStyle(color: AppColors.textSecondary, fontSize: 12),
                  ),
                ],
              ),
            ),
            if (isSelected)
              Icon(PhosphorIcons.checkCircle(PhosphorIconsStyle.fill), color: AppColors.primary),
          ],
        ),
      ),
    );
  }

  Future<void> _handleSelectMethod() async {
    setState(() => _isLoading = true);

    try {
      await ApiService.selectPaymentMethod(widget.orderId, _selectedMethodId!);

      final methods = await _paymentMethods;
      final selectedMethod = methods.firstWhere((m) => m['id'] == _selectedMethodId);

      if (mounted) {
        Navigator.pushReplacement(
          context,
          MaterialPageRoute(
            builder: (context) => UploadProofScreen(
              orderId: widget.orderId,
              orderNumber: widget.orderNumber,
              totalAmount: widget.totalAmount,
              paymentMethod: selectedMethod,
            ),
          ),
        );
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text(e.toString()), backgroundColor: AppColors.red),
        );
      }
    } finally {
      if (mounted) setState(() => _isLoading = false);
    }
  }
}
