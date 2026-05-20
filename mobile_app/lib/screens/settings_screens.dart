import 'package:flutter/material.dart';
import 'package:phosphor_flutter/phosphor_flutter.dart';
import 'package:url_launcher/url_launcher.dart';
import '../theme.dart';
import '../services/api_service.dart';

class HelpScreen extends StatefulWidget {
  const HelpScreen({super.key});

  @override
  State<HelpScreen> createState() => _HelpScreenState();
}

class _HelpScreenState extends State<HelpScreen> {
  int _activeIndex = -1;

  final List<Map<String, dynamic>> _faqItems = [
    {
      'question': 'Bagaimana cara mengetahui pesanan saya diproses?',
      'answer': 'Pesanan Anda akan langsung kami proses di sistem saat Anda telah mengunggah bukti pembayaran, kemudian admin memverifikasi keabsahan dana yang masuk. Anda bisa terus memantau tahapannya pada antarmuka "Lacak Pesanan" dalam Detail Transaksi.',
    },
    {
      'question': 'Apa bukti yang harus ditunjukkan saat mengambil pesanan di toko?',
      'answer': 'Saat pesanan bersatus "Siap Diambil", sistem kami akan menerbitkan Barcode QR Pengambilan di riwayat pesanan Anda. Silakan tunjukkan QR tersebut kepada admin toko untuk di-scan agar barang dapat diserahkan.',
    },
    {
      'question': 'Berapa lama batas waktu pembayaran via Transfer Bank?',
      'answer': 'Batas toleransi maksimal waktu pembayaran adalah 1 x 24 Jam semenjak invoice checkout terbit. Apabila Anda tidak segera mengunggah bukti pada rentang waktu ini, maka pesanan tersebut otomatis akan diarsipkan/dibatalkan oleh sistem kami untuk menghindari alokasi stok palsu.',
    },
    {
      'question': 'Bagaimana jika jumlah transfer saya keliru dibandingkan tagihan?',
      'answer': 'Silakan hubungi WhatsApp pada jam kerja dengan mencantumkan Nomor Invoice Anda, agar tim admin dapat mengecek langsung pada data rekening tabungan.',
    },
  ];

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Pusat Bantuan'),
      ),
      body: ListView(
        padding: const EdgeInsets.all(20),
        children: [
          _buildContactCard(
            icon: PhosphorIcons.whatsappLogo(),
            title: 'Hubungi via WhatsApp',
            subtitle: 'Solusi tercepat untuk kendala pesanan',
            color: Colors.green,
            onTap: () async {
              final whatsappUrl = Uri.parse('https://wa.me/6282123456789');
              if (await canLaunchUrl(whatsappUrl)) {
                await launchUrl(whatsappUrl, mode: LaunchMode.externalApplication);
              }
            },
          ),
          const SizedBox(height: 12),
          _buildContactCard(
            icon: PhosphorIcons.envelopeSimple(),
            title: 'Bantuan Email',
            subtitle: 'support@bintangtani.com',
            color: Colors.blue,
            onTap: () async {
              final emailUrl = Uri.parse('mailto:support@bintangtani.com');
              if (await canLaunchUrl(emailUrl)) {
                await launchUrl(emailUrl);
              }
            },
          ),
          const SizedBox(height: 12),
          _buildContactCard(
            icon: PhosphorIcons.clock(),
            title: 'Jam Operasional',
            subtitle: 'Senin - Jumat: 08.00 - 17.00',
            color: Colors.orange,
          ),
          const SizedBox(height: 32),
          const Text(
            'Pertanyaan Umum (FAQ)',
            style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
          ),
          const SizedBox(height: 16),
          ...List.generate(_faqItems.length, (index) {
            return _buildFaqItem(index);
          }),
          const SizedBox(height: 40),
        ],
      ),
    );
  }

  Widget _buildContactCard({
    required IconData icon,
    required String title,
    required String subtitle,
    required Color color,
    VoidCallback? onTap,
  }) {
    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(16),
      child: Container(
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(
          color: color.withValues(alpha: 0.05),
          borderRadius: BorderRadius.circular(16),
          border: Border.all(color: color.withValues(alpha: 0.2)),
        ),
        child: Row(
          children: [
            Container(
              padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(
                color: color.withValues(alpha: 0.1),
                shape: BoxShape.circle,
              ),
              child: Icon(icon, color: color, size: 28),
            ),
            const SizedBox(width: 16),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    title,
                    style: TextStyle(
                      color: color,
                      fontSize: 14,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                  Text(
                    subtitle,
                    style: const TextStyle(
                      color: AppColors.textSecondary,
                      fontSize: 12,
                    ),
                  ),
                ],
              ),
            ),
            if (onTap != null)
              Icon(PhosphorIcons.caretRight(), color: AppColors.textMuted, size: 16),
          ],
        ),
      ),
    );
  }

  Widget _buildFaqItem(int index) {
    final item = _faqItems[index];
    final isActive = _activeIndex == index;

    return Container(
      margin: const EdgeInsets.only(bottom: 12),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: AppColors.border),
      ),
      child: Column(
        children: [
          InkWell(
            onTap: () => setState(() {
              _activeIndex = isActive ? -1 : index;
            }),
            borderRadius: BorderRadius.circular(16),
            child: Padding(
              padding: const EdgeInsets.all(16),
              child: Row(
                children: [
                  Expanded(
                    child: Text(
                      item['question'],
                      style: const TextStyle(
                        fontWeight: FontWeight.w600,
                        fontSize: 14,
                      ),
                    ),
                  ),
                  Icon(
                    isActive ? PhosphorIcons.caretUp() : PhosphorIcons.caretDown(),
                    color: AppColors.textMuted,
                    size: 20,
                  ),
                ],
              ),
            ),
          ),
          if (isActive)
            Container(
              padding: const EdgeInsets.fromLTRB(16, 0, 16, 16),
              child: Text(
                item['answer'],
                style: const TextStyle(
                  color: AppColors.textSecondary,
                  fontSize: 13,
                  height: 1.5,
                ),
              ),
            ),
        ],
      ),
    );
  }
}

class ChangePasswordScreen extends StatefulWidget {
  const ChangePasswordScreen({super.key});

  @override
  State<ChangePasswordScreen> createState() => _ChangePasswordScreenState();
}

class _ChangePasswordScreenState extends State<ChangePasswordScreen> {
  final _formKey = GlobalKey<FormState>();
  final _currentPasswordController = TextEditingController();
  final _newPasswordController = TextEditingController();
  final _confirmPasswordController = TextEditingController();
  bool _isLoading = false;
  bool _obscureCurrent = true;
  bool _obscureNew = true;
  bool _obscureConfirm = true;

  @override
  void dispose() {
    _currentPasswordController.dispose();
    _newPasswordController.dispose();
    _confirmPasswordController.dispose();
    super.dispose();
  }

  Future<void> _handleSubmit() async {
    if (!_formKey.currentState!.validate()) return;
    if (_newPasswordController.text != _confirmPasswordController.text) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Konfirmasi password tidak cocok'),
          backgroundColor: AppColors.red,
        ),
      );
      return;
    }

    setState(() => _isLoading = true);
    try {
      await ApiService.changePassword(
        currentPassword: _currentPasswordController.text,
        newPassword: _newPasswordController.text,
        newPasswordConfirmation: _confirmPasswordController.text,
      );
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text('Password berhasil diubah'),
            backgroundColor: AppColors.primary,
          ),
        );
        Navigator.pop(context);
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

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Ubah Password'),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(24.0),
        child: Form(
          key: _formKey,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              const Text(
                'Password Saat Ini',
                style: TextStyle(fontWeight: FontWeight.bold),
              ),
              const SizedBox(height: 8),
              TextFormField(
                controller: _currentPasswordController,
                obscureText: _obscureCurrent,
                validator: (value) {
                  if (value == null || value.isEmpty) {
                    return 'Password saat ini wajib diisi';
                  }
                  return null;
                },
                decoration: InputDecoration(
                  hintText: 'Masukkan password saat ini',
                  suffixIcon: IconButton(
                    icon: Icon(
                      _obscureCurrent ? PhosphorIcons.eye() : PhosphorIcons.eyeSlash(),
                    ),
                    onPressed: () => setState(() => _obscureCurrent = !_obscureCurrent),
                  ),
                ),
              ),
              const SizedBox(height: 24),
              const Text(
                'Password Baru',
                style: TextStyle(fontWeight: FontWeight.bold),
              ),
              const SizedBox(height: 8),
              TextFormField(
                controller: _newPasswordController,
                obscureText: _obscureNew,
                validator: (value) {
                  if (value == null || value.isEmpty) {
                    return 'Password baru wajib diisi';
                  }
                  if (value.length < 8) {
                    return 'Password minimal 8 karakter';
                  }
                  return null;
                },
                decoration: InputDecoration(
                  hintText: 'Masukkan password baru',
                  suffixIcon: IconButton(
                    icon: Icon(
                      _obscureNew ? PhosphorIcons.eye() : PhosphorIcons.eyeSlash(),
                    ),
                    onPressed: () => setState(() => _obscureNew = !_obscureNew),
                  ),
                ),
              ),
              const SizedBox(height: 20),
              const Text(
                'Konfirmasi Password Baru',
                style: TextStyle(fontWeight: FontWeight.bold),
              ),
              const SizedBox(height: 8),
              TextFormField(
                controller: _confirmPasswordController,
                obscureText: _obscureConfirm,
                validator: (value) {
                  if (value == null || value.isEmpty) {
                    return 'Konfirmasi password wajib diisi';
                  }
                  return null;
                },
                decoration: InputDecoration(
                  hintText: 'Ulangi password baru',
                  suffixIcon: IconButton(
                    icon: Icon(
                      _obscureConfirm ? PhosphorIcons.eye() : PhosphorIcons.eyeSlash(),
                    ),
                    onPressed: () => setState(() => _obscureConfirm = !_obscureConfirm),
                  ),
                ),
              ),
              const SizedBox(height: 40),
              ElevatedButton(
                onPressed: _isLoading ? null : _handleSubmit,
                child: _isLoading
                    ? const SizedBox(
                        width: 20,
                        height: 20,
                        child: CircularProgressIndicator(
                          strokeWidth: 2,
                          color: Colors.white,
                        ),
                      )
                    : const Text('Simpan Perubahan'),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
