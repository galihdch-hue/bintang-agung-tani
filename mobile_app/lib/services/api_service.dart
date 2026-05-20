import 'dart:convert';
import 'dart:typed_data';

import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';

import '../models/models.dart' as models;

class ApiService {
  static const String baseUrl = 'http://192.168.1.43:8000/api';

  static Future<String?> getToken() async {
    final prefs = await SharedPreferences.getInstance();
    return prefs.getString('auth_token');
  }

  static Future<void> saveToken(String token) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString('auth_token', token);
  }

  static Future<void> removeToken() async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove('auth_token');
  }

  static Future<Map<String, String>> _getHeaders() async {
    final token = await getToken();
    return {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      if (token != null) 'Authorization': 'Bearer $token',
    };
  }

  // Auth Methods
  static Future<Map<String, dynamic>> login(
      String email, String password) async {
    final response = await http.post(
      Uri.parse('$baseUrl/login'),
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: jsonEncode({'email': email, 'password': password}),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      await saveToken(data['access_token']);
      return data;
    } else {
      final error = jsonDecode(response.body);
      throw Exception(error['message'] ?? 'Login gagal');
    }
  }

  static Future<Map<String, dynamic>> register(String name, String email,
      String password, String passwordConfirmation, String? phone) async {
    final response = await http.post(
      Uri.parse('$baseUrl/register'),
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: jsonEncode({
        'name': name,
        'email': email,
        'password': password,
        'password_confirmation': passwordConfirmation,
        'phone': phone,
      }),
    );

    if (response.statusCode == 201) {
      final data = jsonDecode(response.body);
      await saveToken(data['access_token']);
      return data;
    } else {
      final error = jsonDecode(response.body);
      throw Exception(error['message'] ?? 'Registrasi gagal');
    }
  }

  static Future<void> logout() async {
    final headers = await _getHeaders();
    await http.post(Uri.parse('$baseUrl/logout'), headers: headers);
    await removeToken();
  }

  // Product Methods
  static Future<List<models.Product>> getProducts({
    String? query,
    String? search,
    int? categoryId,
    String? categorySlug,
    String? sort,
    double? minPrice,
    double? maxPrice,
    bool featured = false,
    bool inStock = false,
    int limit = 12,
  }) async {
    final params = <String, String>{
      'limit': limit.toString(),
      if (query != null && query.isNotEmpty) 'search': query,
      if (search != null && search.isNotEmpty) 'search': search,
      if (categoryId != null) 'category_id': categoryId.toString(),
      if (categorySlug != null && categorySlug.isNotEmpty)
        'kategori': categorySlug,
      if (sort != null && sort.isNotEmpty) 'sort': sort,
      if (minPrice != null) 'min_price': minPrice.toString(),
      if (maxPrice != null) 'max_price': maxPrice.toString(),
      if (featured) 'featured': 'true',
      if (inStock) 'in_stock': 'true',
    };

    final uri = Uri.parse('$baseUrl/products')
        .replace(queryParameters: params.isEmpty ? null : params);
    final response = await http.get(uri, headers: await _getHeaders());

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      final List<dynamic> body = data is Map<String, dynamic>
          ? (data['data'] as List<dynamic>? ?? const [])
          : (data as List<dynamic>);

      return body.map((item) => models.Product.fromJson(item)).toList();
    }

    throw Exception('Gagal memuat produk');
  }

  static Future<models.Product> getProductBySlug(String slug) async {
    final response = await http.get(Uri.parse('$baseUrl/products/slug/$slug'),
        headers: await _getHeaders());

    if (response.statusCode == 200) {
      return models.Product.fromJson(jsonDecode(response.body));
    }

    throw Exception('Gagal memuat detail produk');
  }

  static Future<List<models.Product>> getFeaturedProducts({int limit = 8}) {
    return getProducts(featured: true, limit: limit);
  }

  static Future<List<models.Category>> getCategories() async {
    final response = await http.get(Uri.parse('$baseUrl/categories'),
        headers: await _getHeaders());

    if (response.statusCode == 200) {
      List<dynamic> body = jsonDecode(response.body);
      return body.map((item) => models.Category.fromJson(item)).toList();
    } else {
      throw Exception('Gagal memuat kategori');
    }
  }

  static Future<Map<String, dynamic>> getDashboard() async {
    final response = await http.get(Uri.parse('$baseUrl/dashboard'),
        headers: await _getHeaders());

    if (response.statusCode == 200) {
      return jsonDecode(response.body) as Map<String, dynamic>;
    }

    throw Exception('Gagal memuat dashboard');
  }

  static Future<Map<String, dynamic>> getCart() async {
    final response = await http.get(Uri.parse('$baseUrl/cart'),
        headers: await _getHeaders());

    if (response.statusCode == 200) {
      return jsonDecode(response.body) as Map<String, dynamic>;
    }

    throw Exception('Gagal memuat keranjang');
  }

  static Future<int> getCartCount() async {
    final response = await http.get(Uri.parse('$baseUrl/cart/count'),
        headers: await _getHeaders());

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body) as Map<String, dynamic>;
      return (data['count'] as num?)?.toInt() ?? 0;
    }

    throw Exception('Gagal memuat jumlah keranjang');
  }

  static Future<Map<String, dynamic>> addToCart(int productId, int quantity,
      {String? notes}) async {
    final response = await http.post(
      Uri.parse('$baseUrl/cart'),
      headers: await _getHeaders(),
      body: jsonEncode({
        'product_id': productId,
        'quantity': quantity,
        if (notes != null && notes.isNotEmpty) 'notes': notes,
      }),
    );

    if (response.statusCode == 201) {
      return jsonDecode(response.body) as Map<String, dynamic>;
    }

    final error = jsonDecode(response.body);
    throw Exception(error['message'] ?? 'Gagal menambahkan ke keranjang');
  }

  static Future<Map<String, dynamic>> updateCartItem(
      int cartItemId, int quantity) async {
    final response = await http.put(
      Uri.parse('$baseUrl/cart/$cartItemId'),
      headers: await _getHeaders(),
      body: jsonEncode({'quantity': quantity}),
    );

    if (response.statusCode == 200) {
      return jsonDecode(response.body) as Map<String, dynamic>;
    }

    final error = jsonDecode(response.body);
    throw Exception(error['message'] ?? 'Gagal memperbarui keranjang');
  }

  static Future<void> removeCartItem(int cartItemId) async {
    final response = await http.delete(Uri.parse('$baseUrl/cart/$cartItemId'),
        headers: await _getHeaders());

    if (response.statusCode != 200) {
      final error = jsonDecode(response.body);
      throw Exception(error['message'] ?? 'Gagal menghapus item keranjang');
    }
  }

  static Future<Map<String, dynamic>> clearCart() async {
    final response = await http.delete(Uri.parse('$baseUrl/cart'),
        headers: await _getHeaders());

    if (response.statusCode == 200) {
      return jsonDecode(response.body) as Map<String, dynamic>;
    }

    final error = jsonDecode(response.body);
    throw Exception(error['message'] ?? 'Gagal mengosongkan keranjang');
  }

  // User Methods
  static Future<Map<String, dynamic>> getProfile() async {
    final response = await http.get(Uri.parse('$baseUrl/profile'),
        headers: await _getHeaders());

    if (response.statusCode == 200) {
      return jsonDecode(response.body);
    } else if (response.statusCode == 401) {
      throw Exception('Unauthenticated');
    } else {
      throw Exception('Gagal memuat profil');
    }
  }

  static Future<Map<String, dynamic>> updateProfile({
    required String name,
    required String email,
    String? phone,
    String? address,
    String? city,
    String? postalCode,
    String? profilePhotoPath,
    Uint8List? profilePhotoBytes,
  }) async {
    final request =
        http.MultipartRequest('POST', Uri.parse('$baseUrl/profile'));
    final headers = await _getHeaders();
    headers.remove('Content-Type');
    request.headers.addAll(headers);
    request.fields['name'] = name;
    request.fields['email'] = email;
    if (phone != null) request.fields['phone'] = phone;
    if (address != null) request.fields['address'] = address;
    if (city != null) request.fields['city'] = city;
    if (postalCode != null) request.fields['postal_code'] = postalCode;
    if (profilePhotoPath != null && profilePhotoPath.isNotEmpty) {
      if (profilePhotoBytes != null) {
        final filename = profilePhotoPath.split('/').last.split('\\').last;
        request.files.add(http.MultipartFile.fromBytes(
          'profile_photo',
          profilePhotoBytes,
          filename: filename,
        ));
      } else {
        request.files.add(
            await http.MultipartFile.fromPath('profile_photo', profilePhotoPath));
      }
    }

    final streamed = await request.send();
    final response = await http.Response.fromStream(streamed);

    if (response.statusCode == 200) {
      return jsonDecode(response.body) as Map<String, dynamic>;
    }

    final error = jsonDecode(response.body);
    throw Exception(error['message'] ?? 'Gagal memperbarui profil');
  }

  static Future<void> deleteProfilePhoto() async {
    final response = await http.delete(Uri.parse('$baseUrl/profile/photo'),
        headers: await _getHeaders());

    if (response.statusCode != 200) {
      final error = jsonDecode(response.body);
      throw Exception(error['message'] ?? 'Gagal menghapus foto profil');
    }
  }

  static Future<List<models.Address>> getAddresses() async {
    final response = await http.get(Uri.parse('$baseUrl/addresses'),
        headers: await _getHeaders());

    if (response.statusCode == 200) {
      List<dynamic> body = jsonDecode(response.body);
      return body.map((item) => models.Address.fromJson(item)).toList();
    } else {
      throw Exception('Gagal memuat alamat');
    }
  }

  static Future<models.Address> createAddress({
    required String label,
    required String recipientName,
    required String phone,
    required String address,
    required String city,
    required String province,
    required String postalCode,
    bool isDefault = false,
  }) async {
    final response = await http.post(
      Uri.parse('$baseUrl/addresses'),
      headers: await _getHeaders(),
      body: jsonEncode({
        'label': label,
        'recipient_name': recipientName,
        'phone': phone,
        'full_address': address,
        'city': city,
        'province': province,
        'postal_code': postalCode,
        'is_default': isDefault,
      }),
    );

    if (response.statusCode == 201) {
      final data = jsonDecode(response.body) as Map<String, dynamic>;
      return models.Address.fromJson(data['address'] ?? data);
    }

    final error = jsonDecode(response.body);
    throw Exception(error['message'] ?? 'Gagal menambahkan alamat');
  }

  static Future<models.Address> updateAddress(
    int addressId, {
    required String label,
    required String recipientName,
    required String phone,
    required String address,
    required String city,
    required String province,
    required String postalCode,
    bool isDefault = false,
  }) async {
    final response = await http.put(
      Uri.parse('$baseUrl/addresses/$addressId'),
      headers: await _getHeaders(),
      body: jsonEncode({
        'label': label,
        'recipient_name': recipientName,
        'phone': phone,
        'full_address': address,
        'city': city,
        'province': province,
        'postal_code': postalCode,
        'is_default': isDefault,
      }),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body) as Map<String, dynamic>;
      return models.Address.fromJson(data['address'] ?? data);
    }

    final error = jsonDecode(response.body);
    throw Exception(error['message'] ?? 'Gagal memperbarui alamat');
  }

  static Future<void> deleteAddress(int addressId) async {
    final response = await http.delete(
        Uri.parse('$baseUrl/addresses/$addressId'),
        headers: await _getHeaders());

    if (response.statusCode != 200) {
      final error = jsonDecode(response.body);
      throw Exception(error['message'] ?? 'Gagal menghapus alamat');
    }
  }

  static Future<void> setDefaultAddress(int addressId) async {
    final response = await http.patch(
        Uri.parse('$baseUrl/addresses/$addressId/default'),
        headers: await _getHeaders());

    if (response.statusCode != 200) {
      final error = jsonDecode(response.body);
      throw Exception(error['message'] ?? 'Gagal mengatur alamat utama');
    }
  }

  // Order Methods
  static Future<List<models.Order>> getOrders() async {
    final response = await http.get(Uri.parse('$baseUrl/orders'),
        headers: await _getHeaders());

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      List<dynamic> body = data is Map ? (data['data'] ?? []) : data;
      return body.map((item) => models.Order.fromJson(item)).toList();
    } else {
      throw Exception('Gagal memuat riwayat pesanan');
    }
  }

  static Future<Map<String, dynamic>> cancelOrder(int id) async {
    final response = await http.post(Uri.parse('$baseUrl/orders/$id/cancel'),
        headers: await _getHeaders());

    if (response.statusCode == 200) {
      return jsonDecode(response.body) as Map<String, dynamic>;
    }

    final error = jsonDecode(response.body);
    throw Exception(error['message'] ?? 'Gagal membatalkan pesanan');
  }

  static Future<models.Order> getOrderDetail(int id) async {
    final response = await http.get(Uri.parse('$baseUrl/orders/$id'),
        headers: await _getHeaders());

    if (response.statusCode == 200) {
      return models.Order.fromJson(jsonDecode(response.body));
    } else {
      throw Exception('Gagal memuat detail pesanan');
    }
  }

  static Future<Map<String, dynamic>> placeOrder({
    required int addressId,
    required List<Map<String, dynamic>> items,
    int? paymentMethodId,
  }) async {
    final response = await http.post(
      Uri.parse('$baseUrl/orders'),
      headers: await _getHeaders(),
      body: jsonEncode({
        'address_id': addressId,
        'items': items,
        'payment_method_id': paymentMethodId,
      }),
    );

    if (response.statusCode == 201) {
      return jsonDecode(response.body);
    } else {
      final error = jsonDecode(response.body);
      throw Exception(error['message'] ?? 'Gagal membuat pesanan');
    }
  }

  static Future<void> changePassword({
    required String currentPassword,
    required String newPassword,
    required String newPasswordConfirmation,
  }) async {
    final response = await http.post(
      Uri.parse('$baseUrl/profile/password'),
      headers: await _getHeaders(),
      body: jsonEncode({
        'current_password': currentPassword,
        'password': newPassword,
        'password_confirmation': newPasswordConfirmation,
      }),
    );

    if (response.statusCode != 200) {
      final error = jsonDecode(response.body);
      throw Exception(error['message'] ?? 'Gagal mengubah password');
    }
  }

  // Payment Methods
  static Future<List<Map<String, dynamic>>> getPaymentMethods() async {
    final response = await http.get(Uri.parse('$baseUrl/payment-methods'),
        headers: await _getHeaders());

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data is List) {
        return data.cast<Map<String, dynamic>>();
      }
      return (data['data'] as List?)?.cast<Map<String, dynamic>>() ?? [];
    }

    throw Exception('Gagal memuat metode pembayaran');
  }

  static Future<Map<String, dynamic>> selectPaymentMethod(int orderId, int paymentMethodId) async {
    final response = await http.post(
      Uri.parse('$baseUrl/orders/$orderId/payment-method'),
      headers: await _getHeaders(),
      body: jsonEncode({'payment_method_id': paymentMethodId}),
    );

    if (response.statusCode == 200) {
      return jsonDecode(response.body) as Map<String, dynamic>;
    }

    final error = jsonDecode(response.body);
    throw Exception(error['message'] ?? 'Gagal memilih metode pembayaran');
  }

  static Future<Map<String, dynamic>> uploadPaymentProof(int orderId, String imagePath, {String? notes, Uint8List? imageBytes}) async {
    final request = http.MultipartRequest('POST', Uri.parse('$baseUrl/orders/$orderId/upload-proof'));
    final headers = await _getHeaders();
    headers.remove('Content-Type');
    request.headers.addAll(headers);

    if (imageBytes != null) {
      final filename = imagePath.split('/').last.split('\\').last;
      request.files.add(http.MultipartFile.fromBytes(
        'proof_image',
        imageBytes,
        filename: filename,
      ));
    } else {
      request.files.add(await http.MultipartFile.fromPath('proof_image', imagePath));
    }

    if (notes != null && notes.isNotEmpty) {
      request.fields['notes'] = notes;
    }

    final streamed = await request.send();
    final response = await http.Response.fromStream(streamed);

    if (response.statusCode == 200) {
      return jsonDecode(response.body) as Map<String, dynamic>;
    }

    final error = jsonDecode(response.body);
    throw Exception(error['message'] ?? 'Gagal upload bukti pembayaran');
  }
}