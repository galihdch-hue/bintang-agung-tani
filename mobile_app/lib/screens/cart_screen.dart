import 'package:flutter/material.dart';
import 'package:phosphor_flutter/phosphor_flutter.dart';

import '../models/models.dart';
import '../services/api_service.dart';
import '../theme.dart';
import 'checkout_screen.dart';

class CartScreen extends StatefulWidget {
  const CartScreen({super.key});

  @override
  CartScreenState createState() => CartScreenState();
}

class CartScreenState extends State<CartScreen> with WidgetsBindingObserver {
  late Future<Map<String, dynamic>> _cartFuture;
  bool _isMutating = false;

  void refresh() => _refreshCart();

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addObserver(this);
    _refreshCart();
  }

  @override
  void dispose() {
    WidgetsBinding.instance.removeObserver(this);
    super.dispose();
  }

  @override
  void didChangeAppLifecycleState(AppLifecycleState state) {
    if (state == AppLifecycleState.resumed) {
      _refreshCart();
    }
  }

  String _formatPrice(double price) {
    return 'Rp ${formatCurrency(price)}';
  }

  void _refreshCart() {
    setState(() {
      _cartFuture = ApiService.getCart();
    });
  }

  List<CartItem> _buildCheckoutItems(List<dynamic> items) {
    return items.map((item) {
      final product = Product(
        id: item['product_id'] as int? ?? 0,
        name: item['name']?.toString() ?? 'Produk',
        description: '',
        price: double.tryParse(item['unit_price']?.toString() ?? '0') ?? 0.0,
        discountPrice: item['original_price'] != null
            ? double.tryParse(item['original_price'].toString())
            : null,
        image: item['image']?.toString(),
        category: 'Produk',
        stock: item['max_quantity'] as int? ?? 0,
        unit: 'pcs',
      );

      return CartItem(
        product: product,
        quantity: item['quantity'] as int? ?? 1,
      );
    }).toList();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Keranjang Belanja'),
        actions: [
          TextButton(
            onPressed: _isMutating
                ? null
                : () async {
                    setState(() => _isMutating = true);
                    try {
                      await ApiService.clearCart();
                      _refreshCart();
                    } finally {
                      if (mounted) {
                        setState(() => _isMutating = false);
                      }
                    }
                  },
            child: const Text('Hapus Semua',
                style: TextStyle(color: AppColors.red)),
          ),
        ],
      ),
      body: FutureBuilder<Map<String, dynamic>>(
        future: _cartFuture,
        builder: (context, snapshot) {
          if (snapshot.connectionState == ConnectionState.waiting) {
            return const Center(child: CircularProgressIndicator());
          }

          if (snapshot.hasError) {
            return Center(
                child: Text('Gagal memuat keranjang: ${snapshot.error}'));
          }

          final cart = snapshot.data ?? const {};
          final items = (cart['items'] as List<dynamic>? ?? const []);

          if (items.isEmpty) {
            return _buildEmptyCart();
          }

          return Column(
            children: [
              Expanded(
                child: ListView.separated(
                  padding: const EdgeInsets.all(16),
                  itemCount: items.length,
                  separatorBuilder: (context, index) =>
                      const SizedBox(height: 16),
                  itemBuilder: (context, index) {
                    final item = items[index] as Map<String, dynamic>;
                    return _buildCartItem(item);
                  },
                ),
              ),
              _buildCartSummary(
                  (cart['total'] as num?)?.toDouble() ?? 0, items),
            ],
          );
        },
      ),
    );
  }

  Widget _buildEmptyCart() {
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Icon(PhosphorIcons.shoppingCart(),
              size: 80, color: AppColors.textMuted),
          const SizedBox(height: 16),
          const Text(
            'Keranjang Kosong',
            style: TextStyle(
                fontSize: 18,
                fontWeight: FontWeight.bold,
                color: AppColors.textMain),
          ),
          const SizedBox(height: 8),
          const Text(
            'Yuk, mulai belanja produk pertanian berkualitas!',
            style: TextStyle(color: AppColors.textSecondary),
          ),
          const SizedBox(height: 24),
          ElevatedButton(
            onPressed: () => Navigator.pop(context),
            style: ElevatedButton.styleFrom(minimumSize: const Size(200, 48)),
            child: const Text('Mulai Belanja'),
          ),
        ],
      ),
    );
  }

  Widget _buildCartItem(Map<String, dynamic> item) {
    return Container(
      padding: const EdgeInsets.all(12),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: AppColors.border),
      ),
      child: Row(
        children: [
          // Item Image Placeholder
          Container(
            width: 80,
            height: 80,
            decoration: BoxDecoration(
              color: AppColors.background,
              borderRadius: BorderRadius.circular(12),
            ),
            child: item['image'] != null
                ? ClipRRect(
                    borderRadius: BorderRadius.circular(12),
                    child: Image.network(
                      item['image'],
                      fit: BoxFit.cover,
                      errorBuilder: (context, error, stackTrace) =>
                          Icon(PhosphorIcons.image(), color: AppColors.textMuted),
                    ),
                  )
                : Icon(PhosphorIcons.image(), color: AppColors.textMuted),
          ),
          const SizedBox(width: 16),

          // Item Details
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  (item['slug'] ?? 'Produk').toString().toUpperCase(),
                  style: const TextStyle(
                      color: AppColors.primary,
                      fontSize: 10,
                      fontWeight: FontWeight.bold),
                ),
                Text(
                  item['name'],
                  style: const TextStyle(
                      fontWeight: FontWeight.bold, fontSize: 14),
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                ),
                const SizedBox(height: 8),
                Text(
                  _formatPrice(double.tryParse(item['unit_price']?.toString() ?? '0') ?? 0.0),
                  style: const TextStyle(
                      color: AppColors.primary, fontWeight: FontWeight.bold),
                ),
              ],
            ),
          ),

          // Quantity Control
          Column(
            crossAxisAlignment: CrossAxisAlignment.end,
            children: [
              IconButton(
                icon:
                    Icon(PhosphorIcons.trash(), color: AppColors.red, size: 20),
                onPressed: _isMutating
                    ? null
                    : () async {
                        setState(() => _isMutating = true);
                        try {
                          await ApiService.removeCartItem(item['id'] as int);
                          _refreshCart();
                        } finally {
                          if (mounted) {
                            setState(() => _isMutating = false);
                          }
                        }
                      },
              ),
              Row(
                children: [
                  _buildQtyBtn(PhosphorIcons.minus(), () async {
                    final currentQuantity = item['quantity'] as int;
                    if (currentQuantity > 1) {
                      await _updateQuantity(item, currentQuantity - 1);
                    }
                  }),
                  Padding(
                    padding: const EdgeInsets.symmetric(horizontal: 12),
                    child: Text(
                      item['quantity'].toString(),
                      style: const TextStyle(fontWeight: FontWeight.bold),
                    ),
                  ),
                  _buildQtyBtn(PhosphorIcons.plus(), () async {
                    final currentQuantity = item['quantity'] as int;
                    await _updateQuantity(item, currentQuantity + 1);
                  }),
                ],
              ),
            ],
          ),
        ],
      ),
    );
  }

  Future<void> _updateQuantity(Map<String, dynamic> item, int quantity) async {
    setState(() => _isMutating = true);
    try {
      await ApiService.updateCartItem(item['id'] as int, quantity);
      _refreshCart();
    } finally {
      if (mounted) {
        setState(() => _isMutating = false);
      }
    }
  }

  Widget _buildQtyBtn(IconData icon, Future<void> Function() onTap) {
    return GestureDetector(
      onTap: () {
        onTap();
      },
      child: Container(
        padding: const EdgeInsets.all(4),
        decoration: BoxDecoration(
          border: Border.all(color: AppColors.border),
          borderRadius: BorderRadius.circular(8),
        ),
        child: Icon(icon, size: 14, color: AppColors.textMain),
      ),
    );
  }

  Widget _buildCartSummary(double totalPrice, List<dynamic> items) {
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
        child: Column(
          children: [
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                const Text(
                  'Total Pembayaran',
                  style:
                      TextStyle(fontSize: 16, color: AppColors.textSecondary),
                ),
                Text(
                  _formatPrice(totalPrice),
                  style: const TextStyle(
                      fontSize: 20,
                      fontWeight: FontWeight.bold,
                      color: AppColors.primary),
                ),
              ],
            ),
            const SizedBox(height: 16),
            ElevatedButton(
              onPressed: _isMutating
                  ? null
                  : () async {
                      await Navigator.push(
                        context,
                        MaterialPageRoute(
                          builder: (context) => CheckoutScreen(
                            items: _buildCheckoutItems(items),
                            total: totalPrice,
                          ),
                        ),
                      );
                      _refreshCart();
                    },
              child: const Text('Lanjut ke Checkout'),
            ),
          ],
        ),
      ),
    );
  }
}
