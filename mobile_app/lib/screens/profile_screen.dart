// ignore_for_file: use_build_context_synchronously
import 'package:flutter/material.dart';
import 'package:phosphor_flutter/phosphor_flutter.dart';
import '../theme.dart';
import '../services/api_service.dart';
import 'address_screen.dart';
import 'order_history_screen.dart';
import 'settings_screens.dart';
import 'auth/login_screen.dart';
import 'edit_profile_screen.dart';
import 'notification_screen.dart';

class ProfileScreen extends StatefulWidget {
  const ProfileScreen({super.key});

  @override
  State<ProfileScreen> createState() => _ProfileScreenState();
}

class _ProfileScreenState extends State<ProfileScreen> {
  late Future<Map<String, dynamic>> _profile;

  @override
  void initState() {
    super.initState();
    _refreshData();
  }

  void _refreshData() {
    setState(() {
      _profile = ApiService.getProfile();
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Profil Saya'),
      ),
      body: RefreshIndicator(
        onRefresh: () async => _refreshData(),
        color: AppColors.primary,
        child: FutureBuilder<Map<String, dynamic>>(
          future: _profile,
          builder: (context, snapshot) {
            if (snapshot.connectionState == ConnectionState.waiting) {
              return const Center(child: CircularProgressIndicator());
            }
            if (snapshot.hasError) {
              return Center(child: Text('Error: ${snapshot.error}'));
            }
            final profile = snapshot.data ?? {};
            return SingleChildScrollView(
              physics: const AlwaysScrollableScrollPhysics(),
              child: Column(
                children: [
                  const SizedBox(height: 20),
                  _buildProfileHeader(profile),
                  const SizedBox(height: 32),
                  _buildMenuSection(context),
                  const SizedBox(height: 32),
                  _buildLogoutButton(),
                  const SizedBox(height: 40),
                ],
              ),
            );
          },
        ),
      ),
    );
  }

  Widget _buildProfileHeader(Map<String, dynamic> profile) {
    final photoUrl = profile['profile_photo_url']?.toString() ?? '';
    final fullPhotoUrl = buildAssetUrl(photoUrl.isNotEmpty ? photoUrl : null)
        .isNotEmpty
            ? buildAssetUrl(photoUrl)
            : 'https://ui-avatars.com/api/?name=${Uri.encodeComponent(profile['name'] ?? 'User')}';

    return Column(
      children: [
        Stack(
          children: [
            CircleAvatar(
              radius: 50,
              backgroundColor: AppColors.primaryBg,
              backgroundImage: NetworkImage(fullPhotoUrl),
              onBackgroundImageError: (_, __) {},
            ),
            Positioned(
              bottom: 0,
              right: 0,
              child: Container(
                padding: const EdgeInsets.all(6),
                decoration: const BoxDecoration(
                  color: AppColors.primary,
                  shape: BoxShape.circle,
                ),
                child: Icon(PhosphorIcons.camera(PhosphorIconsStyle.fill), color: Colors.white, size: 16),
              ),
            ),
          ],
        ),
        const SizedBox(height: 16),
        Text(
          profile['name'] ?? 'Guest',
          style: const TextStyle(fontSize: 20, fontWeight: FontWeight.bold),
        ),
        Text(
          profile['email'] ?? '',
          style: const TextStyle(color: AppColors.textSecondary),
        ),
        const SizedBox(height: 8),
        Container(
          padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 4),
          decoration: BoxDecoration(
            color: AppColors.primaryBg,
            borderRadius: BorderRadius.circular(20),
          ),
          child: const Text(
            'Member',
            style: TextStyle(color: AppColors.primary, fontSize: 11, fontWeight: FontWeight.bold),
          ),
        ),
      ],
    );
  }

  Widget _buildMenuSection(BuildContext context) {
    return Container(
      margin: const EdgeInsets.symmetric(horizontal: 20),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: AppColors.border),
      ),
      child: Column(
        children: [
          _buildMenuItem(
            icon: PhosphorIcons.user(),
            title: 'Edit Profil',
            onTap: () async {
              final profile = await _profile;
              if (!mounted) return;
              Navigator.push(
                context,
                MaterialPageRoute(
                  builder: (context) => EditProfileScreen(profile: profile),
                ),
              ).then((result) {
                if (mounted && result == true) _refreshData();
              });
            },
          ),
          _buildMenuItem(
            icon: PhosphorIcons.mapPin(),
            title: 'Alamat Pengiriman',
            onTap: () {
              Navigator.push(context, MaterialPageRoute(builder: (context) => const AddressScreen()));
            },
          ),
          _buildMenuItem(
            icon: PhosphorIcons.receipt(),
            title: 'Riwayat Pesanan',
            onTap: () {
              Navigator.push(context, MaterialPageRoute(builder: (context) => const OrderHistoryScreen()));
            },
          ),
          _buildMenuItem(
            icon: PhosphorIcons.lockKey(),
            title: 'Ubah Password',
            onTap: () {
              Navigator.push(context, MaterialPageRoute(builder: (context) => const ChangePasswordScreen()));
            },
          ),
          _buildMenuItem(
            icon: PhosphorIcons.bell(),
            title: 'Notifikasi',
            onTap: () {
              Navigator.push(context, MaterialPageRoute(builder: (context) => const NotificationScreen()));
            },
          ),
          _buildMenuItem(
            icon: PhosphorIcons.question(),
            title: 'Pusat Bantuan',
            onTap: () {
              Navigator.push(context, MaterialPageRoute(builder: (context) => const HelpScreen()));
            },
            isLast: true,
          ),
        ],
      ),
    );
  }

  Widget _buildMenuItem({
    required IconData icon,
    required String title,
    required VoidCallback onTap,
    bool isLast = false,
  }) {
    return InkWell(
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(
          border: isLast ? null : const Border(bottom: BorderSide(color: AppColors.border)),
        ),
        child: Row(
          children: [
            Icon(icon, color: AppColors.primary, size: 22),
            const SizedBox(width: 16),
            Expanded(
              child: Text(
                title,
                style: const TextStyle(fontWeight: FontWeight.w500, fontSize: 15),
              ),
            ),
            Icon(PhosphorIcons.caretRight(), color: AppColors.textMuted, size: 16),
          ],
        ),
      ),
    );
  }

  Widget _buildLogoutButton() {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 20),
      child: OutlinedButton(
        onPressed: () async {
          final confirm = await showDialog<bool>(
            context: context,
            builder: (context) => AlertDialog(
              title: const Text('Keluar Akun'),
              content: const Text('Apakah Anda yakin ingin keluar?'),
              actions: [
                TextButton(onPressed: () => Navigator.pop(context, false), child: const Text('Batal')),
                TextButton(
                  onPressed: () => Navigator.pop(context, true),
                  style: TextButton.styleFrom(foregroundColor: AppColors.red),
                  child: const Text('Keluar'),
                ),
              ],
            ),
          );

          if (confirm == true) {
            try {
              await ApiService.logout();
              if (mounted) {
                Navigator.of(context).pushAndRemoveUntil(
                  MaterialPageRoute(builder: (context) => const LoginScreen()),
                  (route) => false,
                );
              }
            } catch (e) {
              if (mounted) {
                ScaffoldMessenger.of(context).showSnackBar(
                  SnackBar(content: Text(e.toString()), backgroundColor: AppColors.red),
                );
              }
            }
          }
        },
        style: OutlinedButton.styleFrom(
          foregroundColor: AppColors.red,
          side: const BorderSide(color: AppColors.red),
          minimumSize: const Size(double.infinity, 48),
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
        ),
        child: Row(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(PhosphorIcons.signOut(), size: 20),
            const SizedBox(width: 8),
            const Text('Keluar Akun'),
          ],
        ),
      ),
    );
  }
}
