import 'package:flutter/material.dart';
import 'package:phosphor_flutter/phosphor_flutter.dart';
import '../theme.dart';
import '../models/models.dart';
import '../services/api_service.dart';
import 'address_screen.dart';
import 'payment_method_screen.dart';

class CheckoutScreen extends StatefulWidget {
  final List<CartItem>? items;
  final double? total;

  const CheckoutScreen({super.key, this.items, this.total});

  @override
  State<CheckoutScreen> createState() => _CheckoutScreenState();
}

class _CheckoutScreenState extends State<CheckoutScreen> {
  bool _isLoading = false;
  bool _isLoadingAddresses = true;
  Address? _selectedAddress;

  @override
  void initState() {
    super.initState();
    _loadAddresses();
  }

  Future<void> _loadAddresses() async {
    try {
      final addresses = await ApiService.getAddresses();
      if (mounted) {
        setState(() {
          _isLoadingAddresses = false;
          if (addresses.isNotEmpty) {
            _selectedAddress = addresses.firstWhere((a) => a.isDefault, orElse: () => addresses.first);
          }
        });
      }
    } catch (e) {
      if (mounted) {
        setState(() => _isLoadingAddresses = false);
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Checkout'),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(20),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            _buildSectionTitle('Alamat Pengiriman'),
            const SizedBox(height: 12),
            _isLoadingAddresses
                ? const Center(child: CircularProgressIndicator())
                : _buildAddressCard(_selectedAddress ?? Address(id: 0, title: '', name: 'Belum ada alamat', phone: '', detail: 'Tambahkan alamat pengiriman', isDefault: false)),
            const SizedBox(height: 24),
            _buildSectionTitle('Pesanan Anda'),
            const SizedBox(height: 12),
            ...?widget.items?.map((item) => _buildOrderItem(item)),
            const SizedBox(height: 24),
            _buildSectionTitle('Ringkasan Pembayaran'),
            const SizedBox(height: 12),
            _buildPaymentSummary(),
            const SizedBox(height: 40),
          ],
        ),
      ),
      bottomNavigationBar: _buildBottomBar(),
    );
  }

  Widget _buildSectionTitle(String title) {
    return Text(
      title,
      style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
    );
  }

  Widget _buildAddressCard(Address address) {
    return InkWell(
      onTap: () async {
        final selected = await Navigator.push<Address>(
          context,
          MaterialPageRoute(builder: (context) => const AddressScreen(selectionMode: true)),
        );
        if (selected != null) {
          setState(() => _selectedAddress = selected);
        }
      },
      child: Container(
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(16),
          border: Border.all(color: AppColors.border),
        ),
        child: Row(
          children: [
            Icon(PhosphorIcons.mapPin(PhosphorIconsStyle.fill), color: AppColors.primary),
            const SizedBox(width: 16),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    '${address.name} (${address.title})',
                    style: const TextStyle(fontWeight: FontWeight.bold),
                  ),
                  Text(
                    address.phone,
                    style: const TextStyle(color: AppColors.textSecondary, fontSize: 13),
                  ),
                  Text(
                    address.detail,
                    style: const TextStyle(color: AppColors.textSecondary, fontSize: 13),
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                  ),
                ],
              ),
            ),
            Icon(PhosphorIcons.caretRight(), color: AppColors.textMuted, size: 16),
          ],
        ),
      ),
    );
  }

  Widget _buildOrderItem(CartItem item) {
    return Container(
      margin: const EdgeInsets.only(bottom: 12),
      child: Row(
        children: [
          Container(
            width: 60,
            height: 60,
            decoration: BoxDecoration(
              color: AppColors.background,
              borderRadius: BorderRadius.circular(12),
            ),
            child: item.product.image != null
                ? Image.network(
                    item.product.image!,
                    fit: BoxFit.cover,
                    errorBuilder: (context, error, stackTrace) =>
                        Icon(PhosphorIcons.package(), color: AppColors.textMuted),
                  )
                : Icon(PhosphorIcons.package(), color: AppColors.textMuted),
          ),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  item.product.name,
                  style: const TextStyle(fontWeight: FontWeight.w600),
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                ),
                Text(
                  '${item.quantity} x Rp ${formatCurrency(item.product.price)}',
                  style: const TextStyle(color: AppColors.textSecondary, fontSize: 13),
                ),
              ],
            ),
          ),
          Text(
            'Rp ${formatCurrency(item.product.price * item.quantity)}',
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
          _buildSummaryRow('Subtotal', 'Rp ${formatCurrency(widget.total ?? 0)}'),
          const Padding(
            padding: EdgeInsets.symmetric(vertical: 12),
            child: Divider(color: AppColors.border),
          ),
          _buildSummaryRow('Total Pembayaran', 'Rp ${formatCurrency(widget.total ?? 0)}', isBold: true),
        ],
      ),
    );
  }

  Widget _buildSummaryRow(String label, String value, {bool isBold = false, bool isNegative = false}) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        Text(
          label,
          style: TextStyle(
            color: isBold ? AppColors.textMain : AppColors.textSecondary,
            fontWeight: isBold ? FontWeight.bold : FontWeight.normal,
            fontSize: isBold ? 16 : 14,
          ),
        ),
        Text(
          value,
          style: TextStyle(
            color: isNegative ? AppColors.red : (isBold ? AppColors.primary : AppColors.textMain),
            fontWeight: isBold ? FontWeight.bold : FontWeight.w600,
            fontSize: isBold ? 18 : 14,
          ),
        ),
      ],
    );
  }

  Widget _buildBottomBar() {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: Colors.white,
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.05),
            blurRadius: 10,
            offset: const Offset(0, -5),
          ),
        ],
      ),
      child: SafeArea(
        child: _isLoading 
          ? const Center(child: CircularProgressIndicator())
          : ElevatedButton(
              onPressed: (_selectedAddress == null || _selectedAddress!.id == 0 || (widget.items == null || widget.items!.isEmpty))
                ? null 
                : _handleCheckout,
              child: const Text('Konfirmasi & Bayar'),
            ),
      ),
    );
  }

  Future<void> _handleCheckout() async {
    setState(() => _isLoading = true);
    
    try {
      final items = (widget.items ?? []).map((item) => {
        'product_id': item.product.id,
        'quantity': item.quantity,
      }).toList();

      final result = await ApiService.placeOrder(
        addressId: _selectedAddress!.id,
        items: items,
      );

      if (mounted) {
        final order = result['order'] as Map<String, dynamic>?;
        final orderId = order?['id'] as int? ?? 0;
        final orderNumber = order?['order_number']?.toString() ?? '';
        final totalAmount = (order?['total_amount'] as num?)?.toDouble() ?? widget.total ?? 0;

        Navigator.pushReplacement(
          context,
          MaterialPageRoute(
            builder: (context) => PaymentMethodScreen(
              orderId: orderId,
              orderNumber: orderNumber,
              totalAmount: totalAmount,
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
      if (mounted) {
        setState(() => _isLoading = false);
      }
    }
  }

}
