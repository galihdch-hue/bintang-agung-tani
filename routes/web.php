<?php

/**
 * @var \Illuminate\Routing\Router $router
 *
 * The route file is loaded outside of a namespace and uses Laravel helpers
 * such as `redirect()` and the `Route` facade. Static analysis tools often
 * mark these as undefined even though they are provided by the framework.
 * The following annotations suppress those warnings.
 *
 * @noinspection PhpUndefinedFunctionInspection
 * @noinspection PhpUndefinedClassInspection
 */

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\PaymentMethodController;
use App\Http\Controllers\Admin\PaymentVerificationController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\QRScanController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\StockController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VerificationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\User\AddressController;
use App\Http\Controllers\User\CartController;
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use App\Http\Controllers\User\OrderController;
use App\Http\Controllers\User\ProductController as UserProductController;
use App\Http\Controllers\User\ProfileController;
use Illuminate\Support\Facades\Route;

// Redirect root to login
Route::get('/', function () {
  return redirect('/login');
});

// Authentication Routes (Guest only)
Route::middleware(['guest'])->group(function () {
  Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
  Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:auth');

  Route::get('/register', [AuthController::class, 'showRegister']);
  Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:auth');

  Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
  Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.email')->middleware('throttle:auth');

  Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
  Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update')->middleware('throttle:auth');
});

// Logout (Auth required)
Route::post('/logout', [AuthController::class, 'logout'])
  ->middleware('auth')
  ->name('logout');

// Midtrans Webhook (outside auth middleware)
Route::post('/webhook/midtrans', [\App\Http\Controllers\PaymentController::class, 'handleWebhook'])->name('webhook.midtrans');

// User Routes - add 'user' middleware
Route::prefix('user')->middleware(['auth', 'user'])->group(function () {
  Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard');
  Route::get('/produk', [UserProductController::class, 'index'])->name('user.produk.index');
  Route::get('/produk/{slug}', [UserProductController::class, 'show'])->name('user.produk.show');
  Route::get('/keranjang', [CartController::class, 'index'])->name('user.cart.index');
  Route::post('/cart/add', [CartController::class, 'add'])->name('user.cart.add');
  Route::patch('/cart/items/{cartItem}', [CartController::class, 'update'])->name('user.cart.update');
  Route::delete('/cart/items/{cartItem}', [CartController::class, 'remove'])->name('user.cart.remove');
  Route::delete('/cart/clear', [CartController::class, 'clear'])->name('user.cart.clear');
  Route::get('/api/cart/data', [CartController::class, 'getCartData']);
  Route::get('/api/cart/count', [CartController::class, 'getCount']);
  Route::get('/checkout', function () {
    return view('user.checkout');
  });
  Route::get('/upload-pembayaran', function () {
    return redirect()->route('user.orders.index');
  });

  // Redirect old status-pesanan route to order detail (consolidated)
  Route::get('/status-pesanan/{order?}', function (?\App\Models\Order $order = null) {
    if (! $order) {
      return redirect()->route('user.orders.index');
    }

    if ($order->user_id !== auth()->id()) {
      abort(403, 'Unauthorized');
    }

    return redirect()->route('user.orders.show', $order);
  })->name('user.status-pesanan');

  // Barcode/QR Code Route (consolidated)
  Route::get('/barcode-pesanan/{order}', function (\App\Models\Order $order) {
    if ($order->user_id !== auth()->id()) {
      abort(403, 'Unauthorized');
    }

    return view('user.barcode-pesanan', compact('order'));
  })->name('user.barcode-pesanan');

  // Redirect old routes to new consolidated route
  Route::get('/pesanan/barcode/{order?}', function (?\App\Models\Order $order = null) {
    if (! $order) {
      abort(404);
    }

    return redirect()->route('user.barcode-pesanan', $order);
  });

  // Order Routes
  Route::get('/riwayat', [OrderController::class, 'index'])->name('user.orders.index');
  Route::get('/riwayat/{order}', [OrderController::class, 'show'])->name('user.orders.show');
  Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('user.orders.cancel');

  // Checkout Routes - Rate limited for security
  Route::get('/checkout', [\App\Http\Controllers\User\CheckoutController::class, 'index'])->name('user.checkout.index');
  Route::post('/checkout', [\App\Http\Controllers\User\CheckoutController::class, 'store'])->name('user.checkout.store')->middleware('throttle:checkout');

  // Payment Routes - Rate limited for security
  Route::get('/payment/{order}', [\App\Http\Controllers\PaymentController::class, 'create'])->name('user.payment.create');
  Route::get('/orders/{order}/payment/status', [\App\Http\Controllers\PaymentController::class, 'checkStatus'])->name('user.payment.status');

  // Manual Payment Routes - Rate limited for security
  Route::get('/payments/{order}/method', [\App\Http\Controllers\User\PaymentController::class, 'selectMethod'])->name('user.payments.select-method');
  Route::post('/payments/{order}/method', [\App\Http\Controllers\User\PaymentController::class, 'storeMethod'])->name('user.payments.store-method')->middleware('throttle:payment');
  Route::get('/payments/{order}/upload', [\App\Http\Controllers\User\PaymentController::class, 'showUploadForm'])->name('user.payments.show-upload');
  Route::post('/payments/{order}/upload', [\App\Http\Controllers\User\PaymentController::class, 'uploadProof'])->name('user.payments.upload-proof')->middleware('throttle:payment');
  Route::get('/payments/{order}/qr-code', [\App\Http\Controllers\User\PaymentController::class, 'showQRCode'])->name('user.payments.qr-code');
  Route::get('/payments/{order}/download-qr', [\App\Http\Controllers\User\PaymentController::class, 'downloadQR'])->name('user.payments.download-qr');

  // Profile Routes
  Route::get('/profil', [ProfileController::class, 'show'])->name('user.profil.show');
  Route::put('/profil', [ProfileController::class, 'update'])->name('user.profil.update');
  Route::put('/profil/password', [ProfileController::class, 'updatePassword'])->name('user.profil.password');
  Route::delete('/profil/photo', [ProfileController::class, 'destroyPhoto'])->name('user.profil.photo.destroy');

  // Address Routes
  Route::get('/alamat', [AddressController::class, 'index'])->name('user.alamat.index');
  Route::post('/alamat', [AddressController::class, 'store'])->name('user.alamat.store');
  Route::put('/alamat/{address}', [AddressController::class, 'update'])->name('user.alamat.update');
  Route::delete('/alamat/{address}', [AddressController::class, 'destroy'])->name('user.alamat.destroy');
  Route::patch('/alamat/{address}/default', [AddressController::class, 'setDefault'])->name('user.alamat.default');

  Route::get('/ubah-password', function () {
    return view('user.ubah-password');
  })->name('user.profil.password.form');
  Route::get('/bantuan', function () {
    // generic help page
    return view('user.bantuan');
  })->name('user.bantuan');

  Route::get('/faq', function () {
    return view('user.bantuan', [
      'pageTitle' => 'FAQ',
      'heading' => 'Pertanyaan Umum',
      'body' => 'Temukan jawaban cepat seputar pemesanan, pembayaran, pengiriman, dan status pesanan Anda.',
    ]);
  });
  Route::get('/kontak', function () {
    return view('user.bantuan', [
      'pageTitle' => 'Kontak',
      'heading' => 'Hubungi Kami',
      'body' => 'Tim kami siap membantu Anda pada jam kerja Senin - Sabtu, 08.00 - 16.00 WIB.',
    ]);
  });
  Route::get('/syarat-ketentuan', function () {
    return view('user.bantuan', [
      'pageTitle' => 'Syarat & Ketentuan',
      'heading' => 'Syarat dan Ketentuan',
      'body' => 'Dengan menggunakan layanan ini, Anda menyetujui ketentuan penggunaan platform Bintang Agung Tani.',
    ]);
  });
  Route::get('/kebijakan-privasi', function () {
    return view('user.bantuan', [
      'pageTitle' => 'Kebijakan Privasi',
      'heading' => 'Kebijakan Privasi',
      'body' => 'Kami menjaga kerahasiaan data pengguna dan memproses data sesuai kebutuhan layanan.',
    ]);
  });
});

// Admin Routes - add 'admin' middleware
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
  Route::get('/', function () {
    return redirect('/admin/dashboard');
  });
  Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
  Route::get('/dashboard/export/pdf', [App\Http\Controllers\Admin\DashboardExportController::class, 'exportPDF'])->name('admin.dashboard.export.pdf');
  // Stock Management Routes
  Route::get('/stok', [StockController::class, 'index'])->name('admin.stock.index');
  Route::get('/stok/{product}', [StockController::class, 'show'])->name('admin.stock.show');
  Route::get('/stok/{product}/edit', [StockController::class, 'edit'])->name('admin.stock.edit');
  Route::patch('/stok/{product}', [StockController::class, 'update'])->name('admin.stock.update');
  Route::get('/stok-logs', [StockController::class, 'logs'])->name('admin.stock.logs');

  // Admin Order Routes
  Route::get('/pesanan', [AdminOrderController::class, 'index'])->name('admin.orders.index');
  Route::get('/pesanan/{order}', [AdminOrderController::class, 'show'])->name('admin.orders.show');
  Route::post('/orders/{order}/cancel', [AdminOrderController::class, 'cancel'])->name('admin.orders.cancel');
  Route::get('/orders/bulk-update', [AdminOrderController::class, 'bulkUpdateStatusPage'])->name('admin.orders.bulk-update-status.page');
  Route::patch('/orders/bulk-update', [AdminOrderController::class, 'bulkUpdateStatus'])->name('admin.orders.bulk-update-status');
  Route::patch('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('admin.orders.update-status');
  Route::post('/orders/{order}/tracking', [AdminOrderController::class, 'addTracking'])->name('admin.orders.add-tracking');
  Route::post('/orders/{order}/payment/confirm', [\App\Http\Controllers\PaymentController::class, 'manualConfirm'])->name('admin.orders.payment.confirm');

  // Verification Routes
  Route::get('/verifikasi', [VerificationController::class, 'index'])->name('admin.verifikasi.index');
  Route::get('/verifikasi/{payment}', [VerificationController::class, 'show'])->name('admin.verifikasi.show');
  Route::post('/verifikasi/{payment}/approve', [VerificationController::class, 'approve'])->name('admin.verifikasi.approve');
  Route::post('/verifikasi/{payment}/reject', [VerificationController::class, 'reject'])->name('admin.verifikasi.reject');

  // Payment Method Routes
  Route::get('/payment-methods', [PaymentMethodController::class, 'index'])->name('admin.payment-methods.index');
  Route::get('/payment-methods/create', [PaymentMethodController::class, 'create'])->name('admin.payment-methods.create');
  Route::post('/payment-methods', [PaymentMethodController::class, 'store'])->name('admin.payment-methods.store');
  Route::get('/payment-methods/{paymentMethod}/edit', [PaymentMethodController::class, 'edit'])->name('admin.payment-methods.edit');
  Route::put('/payment-methods/{paymentMethod}', [PaymentMethodController::class, 'update'])->name('admin.payment-methods.update');
  Route::delete('/payment-methods/{paymentMethod}', [PaymentMethodController::class, 'destroy'])->name('admin.payment-methods.destroy');
  Route::patch('/payment-methods/{paymentMethod}/toggle', [PaymentMethodController::class, 'toggleActive'])->name('admin.payment-methods.toggle');

  // Redirect old duplicate verification routes to consolidated route (GET only)
  Route::get('/payment-proofs', function () {
    return redirect()->route('admin.verifikasi.index');
  });
  Route::get('/payment-proofs/{paymentProof}', function () {
    return redirect()->route('admin.verifikasi.index');
  });

  // Keep PaymentProof POST routes for backward compatibility (functionality preserved)
  Route::post('/payment-proofs/{paymentProof}/verify', [PaymentVerificationController::class, 'verify'])->name('admin.payment-proofs.verify');
  Route::post('/payment-proofs/{paymentProof}/reject', [PaymentVerificationController::class, 'reject'])->name('admin.payment-proofs.reject');

  // QR Scan Routes
  Route::get('/scan-qr', [QRScanController::class, 'index'])->name('admin.scan-qr.index');
  Route::post('/scan-qr', [QRScanController::class, 'scan'])->name('admin.scan-qr.scan');

  Route::get('/verifikasi/detail', function () {
    return view('admin.detail-verifikasi');
  });
  Route::get('/scan', function () {
    $order = session()->has('scanned_order_id') ? \App\Models\Order::find(session()->get('scanned_order_id')) : null;

    return view('admin.scan-barcode', compact('order'));
  })->name('admin.scan');
  Route::get('/verifikasi-pembayaran', function () {
    return redirect('/admin/verifikasi');
  });

  // Alias Routes for Missing Pages
  Route::get('/detail-produk', function () {
    return view('admin.detail-produk');
  });
  Route::get('/edit-produk', function () {
    return view('admin.edit-produk');
  });
  Route::get('/detail-pesanan', function () {
    return redirect('/admin/pesanan');
  });
  Route::get('/tambah-produk', function () {
    return redirect()->route('admin.produk.create');
  });

  // User Management
  Route::get('/users', [UserController::class, 'index'])->name('admin.users.index');
  Route::get('/users/create', [UserController::class, 'create'])->name('admin.users.create');
  Route::post('/users', [UserController::class, 'store'])->name('admin.users.store');
  Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
  Route::put('/users/{user}', [UserController::class, 'update'])->name('admin.users.update');
  Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');

  // Category Management
  Route::get('/kategori', [CategoryController::class, 'index'])->name('admin.kategori.index');
  Route::get('/kategori/create', [CategoryController::class, 'create'])->name('admin.kategori.create');
  Route::post('/kategori', [CategoryController::class, 'store'])->name('admin.kategori.store');
  Route::get('/kategori/{category}/edit', [CategoryController::class, 'edit'])->name('admin.kategori.edit');
  Route::put('/kategori/{category}', [CategoryController::class, 'update'])->name('admin.kategori.update');
  Route::delete('/kategori/{category}', [CategoryController::class, 'destroy'])->name('admin.kategori.destroy');
  Route::patch('/kategori/{category}/toggle', [CategoryController::class, 'toggleActive'])->name('admin.kategori.toggle');

  // Product Management
  Route::get('/produk', [ProductController::class, 'index'])->name('admin.produk.index');
  Route::get('/produk/create', [ProductController::class, 'create'])->name('admin.produk.create');
  Route::post('/produk', [ProductController::class, 'store'])->name('admin.produk.store');
  Route::get('/produk/{product}', [ProductController::class, 'show'])->name('admin.produk.show');
  Route::get('/produk/{product}/edit', [ProductController::class, 'edit'])->name('admin.produk.edit');
  Route::put('/produk/{product}', [ProductController::class, 'update'])->name('admin.produk.update');
  Route::delete('/produk/{product}', [ProductController::class, 'destroy'])->name('admin.produk.destroy');
  Route::patch('/produk/{product}/toggle', [ProductController::class, 'toggleActive'])->name('admin.produk.toggle');
  Route::patch('/produk/{product}/featured', [ProductController::class, 'toggleFeatured'])->name('admin.produk.featured');
  Route::patch('/produk/{product}/stock', [ProductController::class, 'updateStock'])->name('admin.produk.stock');
  Route::patch('/produk/{product}/image', [ProductController::class, 'updateImage'])->name('admin.produk.image');
  Route::delete('/produk/{product}/image', [ProductController::class, 'deleteImage'])->name('admin.produk.image.delete');

  // Settings Routes
  Route::get('/settings', [SettingsController::class, 'index'])->name('admin.settings.index');
  Route::put('/settings', [SettingsController::class, 'update'])->name('admin.settings.update');
  Route::post('/settings/reset', [SettingsController::class, 'reset'])->name('admin.settings.reset');

  // Notification Routes
  Route::get('/api/notifications/count', [NotificationController::class, 'getUnreadCount'])->name('admin.notifications.count');
  Route::get('/notifications', [NotificationController::class, 'index'])->name('admin.notifications.index');
  Route::get('/notifications/unread', [NotificationController::class, 'unread'])->name('admin.notifications.unread');
  Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('admin.notifications.mark-all-read');
  Route::delete('/notifications/delete-all', [NotificationController::class, 'deleteAll'])->name('admin.notifications.delete-all');
  Route::get('/notifications/delete-all', function() {
      return redirect()->route('admin.notifications.index');
  });
  Route::patch('/notifications/{id}/mark-read', [NotificationController::class, 'markAsRead'])->name('admin.notifications.mark-read');
  Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('admin.notifications.destroy');

});
