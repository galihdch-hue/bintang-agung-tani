import 'package:flutter/foundation.dart';
import 'package:flutter/material.dart';
import 'package:phosphor_flutter/phosphor_flutter.dart';
import 'package:image_picker/image_picker.dart';
import 'dart:typed_data';
import '../theme.dart';
import '../services/api_service.dart';
import 'order_detail_screen.dart';

class UploadProofScreen extends StatefulWidget {
  final int orderId;
  final String orderNumber;
  final double totalAmount;
  final Map<String, dynamic> paymentMethod;

  const UploadProofScreen({
    super.key,
    required this.orderId,
    required this.orderNumber,
    required this.totalAmount,
    required this.paymentMethod,
  });

  @override
  State<UploadProofScreen> createState() => _UploadProofScreenState();
}

class _UploadProofScreenState extends State<UploadProofScreen> {
  final _notesController = TextEditingController();
  XFile? _pickedFile;
  Uint8List? _imageBytes;
  bool _isLoading = false;

  String _formatPrice(double price) {
    return 'Rp ${formatCurrency(price)}';
  }

  @override
  void dispose() {
    _notesController.dispose();
    super.dispose();
  }

  Future<void> _pickImage() async {
    final picker = ImagePicker();
    final picked = await picker.pickImage(source: ImageSource.gallery, maxWidth: 1200);
    if (picked != null) {
      final bytes = await picked.readAsBytes();
      setState(() {
        _pickedFile = picked;
        _imageBytes = bytes;
      });
    }
  }

  Future<void> _takePhoto() async {
    final picker = ImagePicker();
    final picked = await picker.pickImage(source: ImageSource.camera, maxWidth: 1200);
    if (picked != null) {
      final bytes = await picked.readAsBytes();
      setState(() {
        _pickedFile = picked;
        _imageBytes = bytes;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Upload Bukti Pembayaran'),
      ),
      body: ListView(
        padding: const EdgeInsets.all(20),
        children: [
          _buildBankInfo(),
          const SizedBox(height: 24),
          _buildImagePicker(),
          const SizedBox(height: 20),
          const Text('Catatan (opsional)', style: TextStyle(fontWeight: FontWeight.bold)),
          const SizedBox(height: 8),
          TextField(
            controller: _notesController,
            maxLines: 3,
            maxLength: 500,
            decoration: InputDecoration(
              hintText: 'Tambahkan catatan jika diperlukan',
              border: OutlineInputBorder(borderRadius: BorderRadius.circular(16)),
            ),
          ),
          const SizedBox(height: 16),
          _buildInstructions(),
          const SizedBox(height: 32),
          ElevatedButton(
            onPressed: _pickedFile == null || _isLoading ? null : _handleUpload,
            child: _isLoading
                ? const SizedBox(width: 20, height: 20, child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white))
                : const Text('Upload Bukti Pembayaran'),
          ),
          const SizedBox(height: 12),
          OutlinedButton(
            onPressed: () => Navigator.pop(context),
            style: OutlinedButton.styleFrom(
              minimumSize: const Size(double.infinity, 54),
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
            ),
            child: const Text('Kembali'),
          ),
          const SizedBox(height: 40),
        ],
      ),
    );
  }

  Widget _buildBankInfo() {
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
          Row(
            children: [
              Icon(PhosphorIcons.bank(), color: AppColors.primary),
              const SizedBox(width: 12),
              Text(
                widget.paymentMethod['bank_name'] ?? widget.paymentMethod['name'] ?? 'Bank',
                style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 16),
              ),
            ],
          ),
          const SizedBox(height: 12),
          const Text('Transfer ke:', style: TextStyle(color: AppColors.textSecondary, fontSize: 13)),
          const SizedBox(height: 4),
          Text(
            widget.paymentMethod['account_number'] ?? '',
            style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 20, letterSpacing: 1),
          ),
          const SizedBox(height: 4),
          Text(
            'a.n. ${widget.paymentMethod['account_name'] ?? ''}',
            style: const TextStyle(color: AppColors.textSecondary, fontSize: 13),
          ),
          const SizedBox(height: 12),
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(12),
            ),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                const Text('Total Bayar', style: TextStyle(color: AppColors.textSecondary)),
                Text(
                  _formatPrice(widget.totalAmount),
                  style: const TextStyle(fontWeight: FontWeight.bold, color: AppColors.primary, fontSize: 16),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildImagePicker() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const Text('Gambar Bukti Pembayaran *', style: TextStyle(fontWeight: FontWeight.bold)),
        const SizedBox(height: 12),
        if (_imageBytes != null)
          Stack(
            children: [
              ClipRRect(
                borderRadius: BorderRadius.circular(16),
                child: Image.memory(
                  _imageBytes!,
                  width: double.infinity,
                  height: 200,
                  fit: BoxFit.cover,
                ),
              ),
              Positioned(
                top: 8,
                right: 8,
                child: GestureDetector(
                  onTap: () => setState(() {
                    _pickedFile = null;
                    _imageBytes = null;
                  }),
                  child: Container(
                    padding: const EdgeInsets.all(6),
                    decoration: const BoxDecoration(color: Colors.red, shape: BoxShape.circle),
                    child: const Icon(Icons.close, color: Colors.white, size: 16),
                  ),
                ),
              ),
            ],
          )
        else
          GestureDetector(
            onTap: _showImageSourceDialog,
            child: Container(
              width: double.infinity,
              height: 160,
              decoration: BoxDecoration(
                color: AppColors.background,
                borderRadius: BorderRadius.circular(16),
                border: Border.all(color: AppColors.border, style: BorderStyle.solid),
              ),
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Icon(PhosphorIcons.uploadSimple(), size: 40, color: AppColors.textMuted),
                  const SizedBox(height: 12),
                  const Text('Pilih file', style: TextStyle(color: AppColors.primary, fontWeight: FontWeight.bold)),
                  const SizedBox(height: 4),
                  const Text('PNG, JPG, JPEG up to 5MB', style: TextStyle(color: AppColors.textMuted, fontSize: 12)),
                ],
              ),
            ),
          ),
      ],
    );
  }

  void _showImageSourceDialog() {
    showModalBottomSheet(
      context: context,
      shape: const RoundedRectangleBorder(borderRadius: BorderRadius.vertical(top: Radius.circular(20))),
      builder: (context) => SafeArea(
        child: Padding(
          padding: const EdgeInsets.all(20),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              ListTile(
                leading: Icon(PhosphorIcons.image(), color: AppColors.primary),
                title: const Text('Pilih dari Galeri'),
                onTap: () {
                  Navigator.pop(context);
                  _pickImage();
                },
              ),
              ListTile(
                leading: Icon(PhosphorIcons.camera(), color: AppColors.primary),
                title: const Text('Ambil Foto'),
                onTap: () {
                  Navigator.pop(context);
                  _takePhoto();
                },
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildInstructions() {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.amber.withValues(alpha: 0.05),
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: Colors.amber.withValues(alpha: 0.3)),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Icon(PhosphorIcons.info(), color: Colors.amber[800], size: 18),
              const SizedBox(width: 8),
              Text('Petunjuk Upload:', style: TextStyle(fontWeight: FontWeight.bold, color: Colors.amber[800], fontSize: 13)),
            ],
          ),
          const SizedBox(height: 8),
          _buildInstruction('Pastikan bukti transfer terlihat jelas'),
          _buildInstruction('Nomor rekening tujuan dan jumlah transfer terbaca'),
          _buildInstruction('Format file: JPG, JPEG, atau PNG'),
          _buildInstruction('Ukuran maksimal: 5MB'),
        ],
      ),
    );
  }

  Widget _buildInstruction(String text) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 4),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text('• ', style: TextStyle(color: AppColors.textSecondary)),
          Expanded(child: Text(text, style: const TextStyle(color: AppColors.textSecondary, fontSize: 12))),
        ],
      ),
    );
  }

  Future<void> _handleUpload() async {
    setState(() => _isLoading = true);

    try {
      await ApiService.uploadPaymentProof(
        widget.orderId,
        _pickedFile!.path,
        notes: _notesController.text.isNotEmpty ? _notesController.text : null,
        imageBytes: _imageBytes,
      );

      if (mounted) {
        _showSuccessDialog();
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

  void _showSuccessDialog() {
    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (context) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(24)),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            const SizedBox(height: 16),
            Container(
              padding: const EdgeInsets.all(16),
              decoration: const BoxDecoration(color: AppColors.primaryBg, shape: BoxShape.circle),
              child: Icon(PhosphorIcons.check(PhosphorIconsStyle.bold), color: AppColors.primary, size: 48),
            ),
            const SizedBox(height: 24),
            const Text(
              'Bukti Berhasil Diupload!',
              style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
            ),
            const SizedBox(height: 8),
            const Text(
              'Bukti pembayaran berhasil diupload. Tim kami akan memverifikasi dalam 1x24 jam.',
              textAlign: TextAlign.center,
              style: TextStyle(color: AppColors.textSecondary, fontSize: 13),
            ),
            const SizedBox(height: 32),
            ElevatedButton(
              onPressed: () async {
                Navigator.of(context).pop();
                try {
                  final order = await ApiService.getOrderDetail(widget.orderId);
                  if (mounted) {
                    Navigator.of(this.context).pushAndRemoveUntil(
                      MaterialPageRoute(builder: (context) => OrderDetailScreen(order: order)),
                      (route) => route.isFirst,
                    );
                  }
                } catch (_) {
                  if (mounted) {
                    Navigator.of(this.context).popUntil((route) => route.isFirst);
                  }
                }
              },
              child: const Text('Lihat Detail Pesanan'),
            ),
          ],
        ),
      ),
    );
  }
}
