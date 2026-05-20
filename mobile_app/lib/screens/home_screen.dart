import 'package:flutter/material.dart';
import 'package:phosphor_flutter/phosphor_flutter.dart';
import '../models/models.dart';
import '../services/api_service.dart';
import '../theme.dart';
import '../widgets/dashboard_widgets.dart';
import '../widgets/product_card.dart';

class HomeScreen extends StatefulWidget {
  const HomeScreen({super.key});

  @override
  State<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen> {
  late Future<Map<String, dynamic>> _dashboardData;
  late Future<List<Product>> _featuredProducts;
  late Future<List<Category>> _categories;

  @override
  void initState() {
    super.initState();
    _refreshData();
  }

  void _refreshData() {
    setState(() {
      _dashboardData = ApiService.getDashboard();
      _featuredProducts = ApiService.getProducts(featured: true);
      _categories = ApiService.getCategories();
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      body: Stack(
        children: [
          Container(
            decoration: const BoxDecoration(
              gradient: LinearGradient(
                begin: Alignment.topCenter,
                end: Alignment.bottomCenter,
                colors: [
                  Color(0xFFF9FAFB),
                  Color(0xFFECFDF5),
                  Color(0xFFF9FAFB),
                ],
              ),
            ),
          ),
          RefreshIndicator(
            onRefresh: () async => _refreshData(),
            color: AppColors.primary,
            child: CustomScrollView(
              slivers: [
                _buildAppBar(),
                SliverToBoxAdapter(
                  child: Padding(
                    padding: const EdgeInsets.all(20.0),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        _buildWelcomeBanner(),
                        const SizedBox(height: 24),
                        _buildStatsGrid(),
                        const SizedBox(height: 32),
                        _buildSectionHeader('Kategori Populer', onSeeAll: () {
                          ScaffoldMessenger.of(context).showSnackBar(
                            const SnackBar(content: Text('Lihat semua kategori di tab Produk')),
                          );
                        }),
                        const SizedBox(height: 16),
                        _buildCategoriesList(),
                        const SizedBox(height: 32),
                        _buildSectionHeader('Produk Unggulan', onSeeAll: () {}),
                      ],
                    ),
                  ),
                ),
                _buildFeaturedProductsGrid(),
                const SliverToBoxAdapter(child: SizedBox(height: 100)),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildAppBar() {
    return SliverAppBar(
      floating: true,
      backgroundColor: Colors.white,
      elevation: 0,
      title: Row(
        children: [
          Container(
            padding: const EdgeInsets.all(8),
            decoration: BoxDecoration(
              color: AppColors.primaryBg,
              borderRadius: BorderRadius.circular(12),
            ),
            child: Icon(PhosphorIcons.leaf(PhosphorIconsStyle.fill),
                color: AppColors.primary, size: 24),
          ),
          const SizedBox(width: 12),
          const Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text('Bintang Agung',
                  style: TextStyle(
                      color: AppColors.textMain,
                      fontSize: 16,
                      fontWeight: FontWeight.bold)),
              Text('Petani Sejahtera',
                  style:
                      TextStyle(color: AppColors.textSecondary, fontSize: 11)),
            ],
          ),
        ],
      ),
      actions: [
        IconButton(
          icon: Icon(PhosphorIcons.bell(), color: AppColors.textMain),
          onPressed: () {
            ScaffoldMessenger.of(context).showSnackBar(
              const SnackBar(content: Text('Tidak ada notifikasi baru')),
            );
          },
        ),
        const SizedBox(width: 8),
      ],
    );
  }

  Widget _buildWelcomeBanner() {
    return FutureBuilder<Map<String, dynamic>>(
      future: _dashboardData,
      builder: (context, snapshot) {
        final user = snapshot.data?['user'] as Map<String, dynamic>?;
        final userName = user?['name'] ?? 'Pengguna';
        final firstName = userName.toString().split(' ').first;

        return Container(
          width: double.infinity,
          padding: const EdgeInsets.all(24),
          decoration: BoxDecoration(
            borderRadius: BorderRadius.circular(24),
            boxShadow: [
              BoxShadow(
                  color: AppColors.primary.withValues(alpha: 0.3),
                  blurRadius: 20,
                  offset: const Offset(0, 10)),
            ],
          ),
          child: ClipRRect(
            borderRadius: BorderRadius.circular(24),
            child: Stack(
              children: [
                // Background image
                Positioned.fill(
                  child: Image.network(
                    'https://images.unsplash.com/photo-1500937386664-56d1dfef3854?w=600&q=70',
                    fit: BoxFit.cover,
                    errorBuilder: (_, __, ___) => Container(
                      decoration: const BoxDecoration(
                        gradient: LinearGradient(
                          colors: [AppColors.primary, Color(0xFF047857)],
                          begin: Alignment.topLeft,
                          end: Alignment.bottomRight,
                        ),
                      ),
                    ),
                  ),
                ),
                // Gradient overlay
                Positioned.fill(
                  child: Container(
                    decoration: BoxDecoration(
                      gradient: LinearGradient(
                        colors: [
                          AppColors.primary.withValues(alpha: 0.7),
                          AppColors.primaryDark.withValues(alpha: 0.5),
                        ],
                        begin: Alignment.topLeft,
                        end: Alignment.bottomRight,
                      ),
                    ),
                  ),
                ),
                // Content
                Padding(
                  padding: const EdgeInsets.all(24),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Row(
                        children: [
                          Container(
                            padding: const EdgeInsets.all(8),
                            decoration: BoxDecoration(
                              color: Colors.white.withValues(alpha: 0.2),
                              borderRadius: BorderRadius.circular(10),
                            ),
                            child: Icon(
                              PhosphorIcons.handWaving(),
                              color: Colors.white,
                              size: 20,
                            ),
                          ),
                          const SizedBox(width: 12),
                          Text(
                            'Selamat Datang Kembali',
                            style: TextStyle(
                              color: Colors.white.withValues(alpha: 0.9),
                              fontSize: 13,
                            ),
                          ),
                        ],
                      ),
                      const SizedBox(height: 12),
                      Text('Halo, $firstName!',
                          style: const TextStyle(
                              color: Colors.white,
                              fontSize: 20,
                              fontWeight: FontWeight.bold)),
                      const SizedBox(height: 4),
                      const Text(
                        'Temukan produk pertanian berkualitas untuk hasil panen terbaik.',
                        style: TextStyle(
                            color: Colors.white,
                            fontSize: 13)),
                      const SizedBox(height: 20),
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                        decoration: BoxDecoration(
                          color: Colors.white.withValues(alpha: 0.2),
                          borderRadius: BorderRadius.circular(20),
                        ),
                        child: Row(
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            Icon(PhosphorIcons.star(), color: Colors.amber, size: 16),
                            const SizedBox(width: 6),
                            const Text(
                              'Member Unggulan',
                              style: TextStyle(
                                color: Colors.white,
                                fontSize: 12,
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                          ],
                        ),
                      ),
                    ],
                  ),
                ),
              ],
            ),
          ),
        );
      },
    );
  }

  Widget _buildStatsGrid() {
    return FutureBuilder<Map<String, dynamic>>(
      future: _dashboardData,
      builder: (context, snapshot) {
        final data = snapshot.data ?? {};
        final cartCount = data['cart_count'] ?? 0;
        final pendingPayment = data['pending_payment_count'] ?? 0;

        return Row(
          children: [
            Expanded(
              child: StatCard(
                title: 'Produk di Keranjang',
                value: cartCount.toString(),
                icon: PhosphorIcons.shoppingCart(),
                color: AppColors.primary,
              ),
            ),
            const SizedBox(width: 16),
            Expanded(
              child: StatCard(
                title: 'Menunggu Bayar',
                value: pendingPayment.toString(),
                icon: PhosphorIcons.clock(),
                color: Colors.orange,
              ),
            ),
          ],
        );
      },
    );
  }

  Widget _buildSectionHeader(String title, {required VoidCallback onSeeAll}) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        Text(title,
            style: const TextStyle(
                fontSize: 18,
                fontWeight: FontWeight.bold,
                color: AppColors.textMain)),
        TextButton(
            onPressed: onSeeAll,
            child: const Text('Lihat Semua',
                style: TextStyle(
                    color: AppColors.primary, fontWeight: FontWeight.w600))),
      ],
    );
  }

  Widget _buildCategoriesList() {
    return FutureBuilder<List<Category>>(
      future: _categories,
      builder: (context, snapshot) {
        if (snapshot.connectionState == ConnectionState.waiting) {
          return const SizedBox(
              height: 100, child: Center(child: CircularProgressIndicator()));
        }
        if (snapshot.hasError) {
          return Text('Error: ${snapshot.error}');
        }
        final categories = snapshot.data ?? [];
        if (categories.isEmpty) {
          return const SizedBox(
              height: 100, child: Center(child: Text('Tidak ada kategori')));
        }
        return SizedBox(
          height: 100,
          child: ListView.separated(
            scrollDirection: Axis.horizontal,
            itemCount: categories.length,
            separatorBuilder: (context, index) => const SizedBox(width: 16),
            itemBuilder: (context, index) {
              return CategoryItem(
                title: categories[index].name,
                icon: PhosphorIcons.package(),
              );
            },
          ),
        );
      },
    );
  }

  Widget _buildFeaturedProductsGrid() {
    return FutureBuilder<List<Product>>(
      future: _featuredProducts,
      builder: (context, snapshot) {
        if (snapshot.connectionState == ConnectionState.waiting) {
          return const SliverToBoxAdapter(
              child: Center(child: CircularProgressIndicator()));
        }
        if (snapshot.hasError) {
          return SliverToBoxAdapter(
              child: Center(
                child: Text('Gagal memuat produk: ${snapshot.error}')));
        }
        final products = snapshot.data ?? [];
        if (products.isEmpty) {
          return const SliverToBoxAdapter(
              child: Center(child: Text('Tidak ada produk unggulan')));
        }
        return SliverPadding(
          padding: const EdgeInsets.symmetric(horizontal: 20),
          sliver: SliverGrid(
            gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
              crossAxisCount: 2,
              mainAxisSpacing: 16,
              crossAxisSpacing: 16,
              childAspectRatio: 0.7,
            ),
            delegate: SliverChildBuilderDelegate(
              (context, index) => ProductCard(product: products[index]),
              childCount: products.length,
            ),
          ),
        );
      },
    );
  }
}
