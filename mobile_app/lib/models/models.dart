class Category {
  final int id;
  final String name;
  final String? icon;
  final int? productCount;

  Category({
    required this.id,
    required this.name,
    this.icon,
    this.productCount,
  });

  factory Category.fromJson(Map<String, dynamic> json) {
    return Category(
      id: json['id'],
      name: json['name'],
      icon: json['icon'],
      productCount: json['products_count'],
    );
  }
}

class Product {
  final int id;
  final String name;
  final String description;
  final double price;
  final double? discountPrice;
  final String? image;
  final String category;
  final int stock;
  final String unit;
  final int totalSold;
  final double rating;

  Product({
    required this.id,
    required this.name,
    required this.description,
    required this.price,
    this.discountPrice,
    this.image,
    required this.category,
    required this.stock,
    required this.unit,
    this.totalSold = 0,
    this.rating = 4.8,
  });

  factory Product.fromJson(Map<String, dynamic> json) {
    return Product(
      id: json['id'],
      name: json['name'] ?? '',
      description: json['description'] ?? '',
      price: double.tryParse(json['price']?.toString() ?? '0') ?? 0.0,
      discountPrice: json['discount_price'] != null ? double.tryParse(json['discount_price'].toString()) : null,
      image: json['featured_image'],
      category: json['category'] != null ? json['category']['name'] : 'Umum',
      stock: json['stock'] ?? 0,
      unit: json['unit'] ?? 'pcs',
      totalSold: json['total_sold'] ?? 0,
      rating: json['rating'] != null ? double.tryParse(json['rating'].toString()) ?? 4.8 : 4.8,
    );
  }
}

class Order {
  final int numericId;
  final String id;
  final String orderNumber;
  final DateTime date;
  final String status;
  final double total;
  final List<OrderItem> items;
  final Address? address;
  final String? qrCodeData;

  Order({
    required this.numericId,
    required this.id,
    this.orderNumber = '',
    required this.date,
    required this.status,
    required this.total,
    required this.items,
    this.address,
    this.qrCodeData,
  });

  factory Order.fromJson(Map<String, dynamic> json) {
    var list = json['items'] as List? ?? [];
    List<OrderItem> itemsList = list.map((i) => OrderItem.fromJson(i)).toList();

    double totalAmount = 0.0;
    if (json['total_amount'] != null) {
      totalAmount = double.tryParse(json['total_amount'].toString()) ?? 0.0;
    }

    return Order(
      numericId: json['id'] is int ? json['id'] : int.tryParse(json['id'].toString()) ?? 0,
      id: json['order_number'] ?? json['id'].toString(),
      orderNumber: json['order_number']?.toString() ?? json['id'].toString(),
      date: DateTime.parse(json['created_at'] ?? DateTime.now().toIso8601String()),
      status: json['status'] ?? 'pending',
      total: totalAmount,
      items: itemsList,
      address: json['address'] != null ? Address.fromJson(json['address']) : null,
      qrCodeData: json['qr_code_data']?.toString(),
    );
  }
}

class OrderItem {
  final String name;
  final int quantity;
  final double price;
  final String? image;

  OrderItem({
    required this.name,
    required this.quantity,
    required this.price,
    this.image,
  });

  factory OrderItem.fromJson(Map<String, dynamic> json) {
    String? imageUrl;
    if (json['product'] != null && json['product']['featured_image'] != null) {
      imageUrl = json['product']['featured_image'];
    }

    return OrderItem(
      name: json['product_name'] ?? (json['product'] != null ? json['product']['name'] : 'Unknown'),
      quantity: json['quantity'] ?? 1,
      price: double.tryParse(json['unit_price']?.toString() ?? json['price']?.toString() ?? '0') ?? 0.0,
      image: imageUrl,
    );
  }
}

class Address {
  final int id;
  final String title;
  final String name;
  final String phone;
  final String detail;
  final bool isDefault;

  Address({
    required this.id,
    required this.title,
    required this.name,
    required this.phone,
    required this.detail,
    this.isDefault = false,
  });

  factory Address.fromJson(Map<String, dynamic> json) {
    return Address(
      id: json['id'],
      title: json['label'] ?? 'Alamat',
      name: json['receiver_name'] ?? '',
      phone: json['receiver_phone'] ?? '',
      detail: json['full_address'] ?? '',
      isDefault: json['is_default'] == 1 || json['is_default'] == true,
    );
  }
}

class CartItem {
  final Product product;
  int quantity;

  CartItem({required this.product, this.quantity = 1});
}
