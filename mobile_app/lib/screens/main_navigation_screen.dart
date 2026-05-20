import 'package:flutter/material.dart';
import 'package:phosphor_flutter/phosphor_flutter.dart';

import '../theme.dart';
import 'cart_screen.dart';
import 'home_screen.dart';
import 'order_history_screen.dart';
import 'product_list_screen.dart';
import 'profile_screen.dart';

class MainNavigationScreen extends StatefulWidget {
  const MainNavigationScreen({super.key});

  @override
  State<MainNavigationScreen> createState() => _MainNavigationScreenState();
}

class _MainNavigationScreenState extends State<MainNavigationScreen> {
  int _currentIndex = 0;
  final _cartKey = GlobalKey<CartScreenState>();

  late final List<Widget> _screens;

  @override
  void initState() {
    super.initState();
    _screens = [
      const HomeScreen(),
      const ProductListScreen(),
      CartScreen(key: _cartKey),
      const OrderHistoryScreen(),
      const ProfileScreen(),
    ];
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: IndexedStack(
        index: _currentIndex,
        children: _screens,
      ),
      bottomNavigationBar: NavigationBar(
        selectedIndex: _currentIndex,
        onDestinationSelected: (index) {
          setState(() => _currentIndex = index);
          if (index == 2) {
            _cartKey.currentState?.refresh();
          }
        },
        labelBehavior: NavigationDestinationLabelBehavior.alwaysShow,
        backgroundColor: Colors.white,
        indicatorColor: AppColors.primaryBg,
        destinations: [
          NavigationDestination(
            icon: Icon(PhosphorIcons.house()),
            selectedIcon: Icon(PhosphorIcons.house(PhosphorIconsStyle.fill)),
            label: 'Beranda',
          ),
          NavigationDestination(
            icon: Icon(PhosphorIcons.squaresFour()),
            selectedIcon:
                Icon(PhosphorIcons.squaresFour(PhosphorIconsStyle.fill)),
            label: 'Produk',
          ),
          NavigationDestination(
            icon: Icon(PhosphorIcons.shoppingCart()),
            selectedIcon:
                Icon(PhosphorIcons.shoppingCart(PhosphorIconsStyle.fill)),
            label: 'Keranjang',
          ),
          NavigationDestination(
            icon: Icon(PhosphorIcons.receipt()),
            selectedIcon:
                Icon(PhosphorIcons.receipt(PhosphorIconsStyle.fill)),
            label: 'Pesanan',
          ),
          NavigationDestination(
            icon: Icon(PhosphorIcons.userCircle()),
            selectedIcon:
                Icon(PhosphorIcons.userCircle(PhosphorIconsStyle.fill)),
            label: 'Profil',
          ),
        ],
      ),
    );
  }
}
