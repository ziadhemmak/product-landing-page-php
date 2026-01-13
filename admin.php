<?php
// Start session and check authentication
session_start();

// Simple password protection - change this password to something secure
$admin_password = "sibous"; // CHANGE THIS PASSWORD

// Check if we're processing a login form submission
$is_login_attempt = $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password']);

if (!isset($_SESSION['admin_logged_in'])) {
    if ($is_login_attempt) {
        if ($_POST['password'] === $admin_password) {
            $_SESSION['admin_logged_in'] = true;
            // Redirect to clear POST data
            header("Location: admin.php");
            exit();
        } else {
            $login_error = "كلمة المرور غير صحيحة";
        }
    }
    
    // Show login form if not authenticated
    showLoginForm($login_error ?? '');
    exit();
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin.php");
    exit();
}

// Include database configuration
require_once 'config.php';

// Define status options
$status_options = [
    'new' => '🆕 جديد',
    'confirmed' => '✅ مؤكد',
    'preparing' => '👨‍🍳 قيد التحضير',
    'shipped' => '🚚 تم الشحن',
    'delivered' => '📦 تم التسليم',
    'cancelled' => '❌ ملغي'
];

// Handle order deletion
if (isset($_GET['delete'])) {
    $order_id = intval($_GET['delete']);
    try {
        $stmt = $pdo->prepare("DELETE FROM orders WHERE id = :id");
        $stmt->bindParam(':id', $order_id, PDO::PARAM_INT);
        if ($stmt->execute()) {
            header("Location: admin.php?deleted=1");
            exit();
        }
    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    }
}

// Handle status update
if (isset($_POST['update_status'])) {
    $order_id = intval($_POST['order_id']);
    $new_status = $_POST['status'];
    $client_called = isset($_POST['client_called']) ? 1 : 0;
    $notes = $_POST['notes'] ?? '';
    
    try {
        // Check if columns exist, if not we'll need to alter the table
        $stmt = $pdo->prepare("UPDATE orders SET status = :status, client_called = :client_called, admin_notes = :notes WHERE id = :id");
        $stmt->bindParam(':status', $new_status);
        $stmt->bindParam(':client_called', $client_called);
        $stmt->bindParam(':notes', $notes);
        $stmt->bindParam(':id', $order_id, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            header("Location: admin.php?updated=1");
            exit();
        }
    } catch (PDOException $e) {
        // If columns don't exist, create them and try again
        if (strpos($e->getMessage(), 'status') !== false || strpos($e->getMessage(), 'client_called') !== false) {
            try {
                // Add the missing columns
                $pdo->exec("ALTER TABLE orders 
                    ADD COLUMN status VARCHAR(20) DEFAULT 'new',
                    ADD COLUMN client_called TINYINT(1) DEFAULT 0,
                    ADD COLUMN admin_notes TEXT");
                
                // Try the update again
                $stmt = $pdo->prepare("UPDATE orders SET status = :status, client_called = :client_called, admin_notes = :notes WHERE id = :id");
                $stmt->bindParam(':status', $new_status);
                $stmt->bindParam(':client_called', $client_called);
                $stmt->bindParam(':notes', $notes);
                $stmt->bindParam(':id', $order_id, PDO::PARAM_INT);
                
                if ($stmt->execute()) {
                    header("Location: admin.php?updated=1");
                    exit();
                }
            } catch (PDOException $e2) {
                die("Database error: " . $e2->getMessage());
            }
        } else {
            die("Database error: " . $e->getMessage());
        }
    }
}

try {
    // Get all orders from database
    $stmt = $pdo->query("
    SELECT o.*, w.willaya AS wilaya_name, c.name AS commune_name
    FROM orders o
    LEFT JOIN willaya w ON o.wilaya = w.id
    LEFT JOIN communes c ON o.commune = c.id
    ORDER BY o.order_date DESC
    ");
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

function showLoginForm($error = '') {
    echo '<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول للإدارة - مارينا شوب</title>
    <link rel="icon" href="assets/img/logo3.gif" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <div class="container">
        <div class="login-container">
            <h2 class="text-center mb-4">👑 تسجيل الدخول للإدارة</h2>';
            
            if (!empty($error)) {
                echo '<div class="alert alert-danger">' . htmlspecialchars($error) . '</div>';
            }
            
            echo '<form method="POST">
                <div class="mb-3">
                    <label for="password" class="form-label">🔐 كلمة المرور</label>
                    <input type="password" class="form-control" id="password" name="password" required placeholder="أدخل كلمة المرور">
                </div>
                <button type="submit" class="btn btn-primary w-100 py-2">
                    <i class="fas fa-sign-in-alt me-2"></i>تسجيل الدخول
                </button>
            </form>
        </div>
    </div>
</body>
</html>';
}

// Show success messages
if (isset($_GET['deleted'])) {
    echo '<div class="alert alert-success alert-dismissible fade show" role="alert" style="margin: 0; border-radius: 0;">
            ✅ تم حذف الطلب بنجاح.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>';
}

if (isset($_GET['updated'])) {
    echo '<div class="alert alert-success alert-dismissible fade show" role="alert" style="margin: 0; border-radius: 0;">
            ✅ تم تحديث حالة الطلب بنجاح.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>';
}

// Function to safely get order field with default value
function getOrderField($order, $field, $default = '') {
    return isset($order[$field]) ? $order[$field] : $default;
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة الطلبات - مارينا شوب</title>
    <link rel="icon" href="assets/img/logo3.gif" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="admin.css">
    <style>
        .status-badge {
            font-size: 0.8em;
            padding: 0.4em 0.8em;
        }
        .status-new { background-color: #17a2b8; color: white; }
        .status-confirmed { background-color: #28a745; color: white; }
        .status-preparing { background-color: #ffc107; color: black; }
        .status-shipped { background-color: #007bff; color: white; }
        .status-delivered { background-color: #6f42c1; color: white; }
        .status-cancelled { background-color: #dc3545; color: white; }
        .called-badge { background-color: #28a745; }
        .not-called-badge { background-color: #6c757d; }
        .status-select {
            font-size: 0.85em;
            padding: 0.25em 0.5em;
        }
        
        /* Mobile responsive table */
        @media (max-width: 768px) {
            .table-container {
                font-size: 14px;
            }
            .btn-sm {
                padding: 0.4rem 0.6rem;
                font-size: 0.9rem;
            }
            .action-buttons .btn {
                width: 40px;
                height: 40px;
                display: flex;
                align-items: center;
                justify-content: center;
            }
        }
    </style>
</head>
<body>

<header class="page-header">
    <div class="container">
        <a href="?logout=1" class="btn btn-danger logout-btn">
            <i class="fas fa-sign-out-alt me-2"></i>تسجيل الخروج
        </a>
        <h1>📊 إدارة الطلبات</h1>
        <p>لوحة التحكم لإدارة طلبات العملاء - مارينا شوب</p>
    </div>
</header>

<div class="container">

    <div class="row mb-4">
        <div class="col-md-2">
            <div class="stats-card">
                <div class="stats-number"><?php echo count($orders); ?></div>
                <div class="stats-label">📦 إجمالي الطلبات</div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stats-card">
                <div class="stats-number">
                    <?php 
                    $today = date('Y-m-d');
                    $today_orders = array_filter($orders, function($order) use ($today) {
                        return date('Y-m-d', strtotime($order['order_date'])) === $today;
                    });
                    echo count($today_orders);
                    ?>
                </div>
                <div class="stats-label">📅 طلبات اليوم</div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stats-card">
                <div class="stats-number">
                    <?php
                    $new_orders = array_filter($orders, function($order) {
                        $status = getOrderField($order, 'status', 'new');
                        return $status === 'new';
                    });
                    echo count($new_orders);
                    ?>
                </div>
                <div class="stats-label">🆕 طلبات جديدة</div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stats-card">
                <div class="stats-number">
                    <?php
                    $delivered_orders = array_filter($orders, function($order) {
                        $status = getOrderField($order, 'status', 'new');
                        return $status === 'delivered';
                    });
                    echo count($delivered_orders);
                    ?>
                </div>
                <div class="stats-label">📦 تم التسليم</div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stats-card">
                <div class="stats-number">
                    <?php
                    $called_clients = array_filter($orders, function($order) {
                        $called = getOrderField($order, 'client_called', 0);
                        return $called == 1;
                    });
                    echo count($called_clients);
                    ?>
                </div>
                <div class="stats-label">📞 تم الاتصال</div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stats-card">
                <div class="stats-number">
                    <?php
                    $cancelled_orders = array_filter($orders, function($order) {
                        $status = getOrderField($order, 'status', 'new');
                        return $status === 'cancelled';
                    });
                    echo count($cancelled_orders);
                    ?>
                </div>
                <div class="stats-label">❌ ملغية</div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h3 class="mb-4">🛒 جميع الطلبات</h3>

            <?php if (count($orders) > 0): ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>👤 العميل</th>
                            <th>📞 الهاتف</th>
                            <th>📍 الولاية</th>
                            <th>🏘️ البلدية</th> <!-- ADDED BACK THE COMMUNE COLUMN -->
                            <th>🚚 التوصيل</th>
                            <th>🛍️ المنتج</th>
                            <th>💰 السعر</th>
                            <th>📅 التاريخ</th>
                            <th>📊 الحالة</th>
                            <th>📞 الاتصال</th>
                            <th>⚙️ الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): 
                            $current_status = getOrderField($order, 'status', 'new');
                            $status_class = 'status-' . $current_status;
                            $client_called = getOrderField($order, 'client_called', 0);
                        ?>
                        <tr>
                            <td><strong>#<?php echo $order['id']; ?></strong></td>
                            <td><?php echo htmlspecialchars($order['first_name'] . ' ' . $order['last_name']); ?></td>
                            <td>
                                <a href="tel:<?php echo htmlspecialchars($order['phone_number']); ?>" class="text-decoration-none">
                                    <i class="fas fa-phone me-1"></i><?php echo htmlspecialchars($order['phone_number']); ?>
                                </a>
                            </td>
                            <td><?php echo htmlspecialchars($order['wilaya_name']); ?></td>
                            <td><?php echo htmlspecialchars($order['commune_name']); ?></td> <!-- ADDED BACK THE COMMUNE DATA -->
                            <td>
                                <span class="badge bg-<?php echo strpos($order['delivery_option'], 'home') !== false ? 'success' : 'info'; ?>">
                                    <?php echo strpos($order['delivery_option'], 'home') !== false ? '🚚 توصيل للمنزل' : '🏢 استلام من المتجر'; ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($order['product_name']); ?></td>
                            <td><strong><?php echo number_format($order['product_price'], 2); ?> دج</strong></td>
                            <td><?php echo date('Y-m-d H:i', strtotime($order['order_date'])); ?></td>
                            <td>
                                <span class="badge status-badge <?php echo $status_class; ?>">
                                    <?php echo $status_options[$current_status]; ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge status-badge <?php echo $client_called ? 'called-badge' : 'not-called-badge'; ?>">
                                    <?php echo $client_called ? '✅ تم الاتصال' : '❌ لم يتصل'; ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm action-buttons">
                                    <button class="btn btn-outline-primary view-order" data-bs-toggle="modal" data-bs-target="#orderModal" data-order='<?php echo json_encode($order); ?>' title="عرض التفاصيل">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-outline-warning edit-status" data-bs-toggle="modal" data-bs-target="#statusModal" data-order='<?php echo json_encode($order); ?>' title="تغيير الحالة">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <a href="?delete=<?php echo $order['id']; ?>" class="btn btn-outline-danger" onclick="return confirm('هل أنت متأكد من حذف هذا الطلب؟')" title="حذف الطلب">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="alert alert-info text-center py-4">
                <i class="fas fa-info-circle fa-2x mb-3"></i>
                <h5>لا توجد طلبات في قاعدة البيانات</h5>
                <p class="mb-0">سيظهر هنا الطلبات الجديدة عند تقديمها من قبل العملاء</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Order Detail Modal -->
<div class="modal fade" id="orderModal" tabindex="-1" aria-labelledby="orderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="orderModalLabel">
                    <i class="fas fa-file-invoice me-2"></i>تفاصيل الطلب
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-section">
                            <h6>👤 معلومات العميل</h6>
                            <p><strong>الاسم الكامل:</strong> <span id="modal-customer-name"></span></p>
                            <p><strong>رقم الهاتف:</strong> <span id="modal-customer-phone"></span></p>
                            <p><strong>الولاية:</strong> <span id="modal-customer-wilaya"></span></p>
                            <p><strong>البلدية:</strong> <span id="modal-customer-commune"></span></p>
                            <?php if (isset($order['color'])): ?>
                            <p><strong>اللون:</strong> <span id="modal-customer-color"></span></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-section">
                            <h6>🛒 معلومات الطلب</h6>
                            <p><strong>المنتج:</strong> <span id="modal-order-product"></span></p>
                            <p><strong>السعر:</strong> <span id="modal-order-price"></span></p>
                            <p><strong>طريقة التوصيل:</strong> <span id="modal-order-delivery"></span></p>
                            <p><strong>حالة الطلب:</strong> <span id="modal-order-status"></span></p>
                            <p><strong>تم الاتصال:</strong> <span id="modal-order-called"></span></p>
                            <p><strong>تاريخ الطلب:</strong> <span id="modal-order-date"></span></p>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="info-section">
                            <h6>📝 ملاحظات</h6>
                            <p id="modal-order-notes" class="mb-0"></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>إغلاق
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Status Update Modal -->
<div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="statusModalLabel">
                    <i class="fas fa-edit me-2"></i>تحديث حالة الطلب
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" id="statusForm">
                <div class="modal-body">
                    <input type="hidden" name="order_id" id="status-order-id">
                    
                    <div class="mb-3">
                        <label for="status" class="form-label">📊 حالة الطلب</label>
                        <select class="form-select" id="status" name="status" required>
                            <?php foreach ($status_options as $value => $label): ?>
                                <option value="<?php echo $value; ?>"><?php echo $label; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="client_called" name="client_called" value="1">
                            <label class="form-check-label" for="client_called">
                                ✅ تم الاتصال بالعميل
                            </label>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label">📝 ملاحظات إضافية</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="أضف ملاحظات حول الطلب..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>إلغاء
                    </button>
                    <button type="submit" name="update_status" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>حفظ التغييرات
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
// View order details
document.querySelectorAll('.view-order').forEach(button => {
    button.addEventListener('click', function() {
        const order = JSON.parse(this.getAttribute('data-order'));
        document.getElementById('modal-customer-name').textContent = order.first_name + ' ' + order.last_name;
        document.getElementById('modal-customer-phone').textContent = order.phone_number;
        document.getElementById('modal-customer-wilaya').textContent = order.wilaya_name;
        document.getElementById('modal-customer-commune').textContent = order.commune_name;
        
        // Show color if exists
        if (order.color) {
            document.getElementById('modal-customer-color').textContent = order.color;
        }
        
        document.getElementById('modal-order-product').textContent = order.product_name;
        document.getElementById('modal-order-price').textContent = numberWithCommas(order.product_price) + ' دج';

        // Translate delivery option
        let deliveryText = order.delivery_option;
        if (deliveryText.includes('home')) {
            deliveryText = '🚚 توصيل للمنزل';
        } else if (deliveryText.includes('company')) {
            deliveryText = '🏢 استلام من المتجر';
        }
        document.getElementById('modal-order-delivery').textContent = deliveryText;

        // Status and call info
        const statusOptions = {
            'new': '🆕 جديد',
            'confirmed': '✅ مؤكد',
            'preparing': '👨‍🍳 قيد التحضير',
            'shipped': '🚚 تم الشحن',
            'delivered': '📦 تم التسليم',
            'cancelled': '❌ ملغي'
        };
        
        const orderStatus = order.status || 'new';
        const clientCalled = order.client_called || 0;
        const adminNotes = order.admin_notes || '';
        
        document.getElementById('modal-order-status').textContent = statusOptions[orderStatus];
        document.getElementById('modal-order-called').textContent = clientCalled == 1 ? '✅ نعم' : '❌ لا';
        document.getElementById('modal-order-notes').textContent = adminNotes || 'لا توجد ملاحظات';
        document.getElementById('modal-order-date').textContent = new Date(order.order_date).toLocaleString('en-US');
    });
});

// Edit status
document.querySelectorAll('.edit-status').forEach(button => {
    button.addEventListener('click', function() {
        const order = JSON.parse(this.getAttribute('data-order'));
        document.getElementById('status-order-id').value = order.id;
        document.getElementById('status').value = order.status || 'new';
        document.getElementById('client_called').checked = (order.client_called == 1);
        document.getElementById('notes').value = order.admin_notes || '';
    });
});

// Format numbers with commas
function numberWithCommas(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

// Auto-dismiss alerts after 5 seconds
setTimeout(() => {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        const bsAlert = new bootstrap.Alert(alert);
        bsAlert.close();
    });
}, 5000);
</script>

</body>
</html>