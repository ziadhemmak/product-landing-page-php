
// Show success message if redirected from order processing
if (isset($_GET['message']) && $_GET['message'] == 'success') {
    echo '<div class="alert alert-success alert-dismissible fade show" role="alert" style="margin: 0; border-radius: 0;">
            تم تقديم الطلب بنجاح! سنتصل بك قريبًا
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>';
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="assets/img/logo3.gif" type="image/x-icon">
    <title>مارينا شوب - مدفأة كهربائية 360° من كوبرا</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- Page Header -->
    <header class="page-header">
        <div class="container">
            <h1>مارينا شوب</h1>
            <p>الأجهزة المنزلية عالية الجودة بأسعار مناسبة</p>
        </div>
    </header>

    <!-- Main Container -->
    <div class="container main-content">
        <div class="row g-4">
            <!-- Images Section - LEFT SIDE (FIXED) -->
            <div class="col-lg-6">
                <div class="images-section">
                    <div class="image-header">
                        <div class="product-brand">
                            <img src="assets/img/cobralogo.png" alt="كوبرا" class="brand-logo">
                            <h2 class="image-title">مدفأة كهربائية 360° بخمس واجهات من كوبرا</h2>
                        </div>
                        <div class="image-price">
                            <span class="original-price">5,000 دج</span>
                            <span class="discounted-price">4,000 دج</span>
                            <span class="discount-badge">وفر 20%</span>
                        </div>
                    </div>
                    
                    <div class="main-image-container">
                        <img src="assets/img/img1.JPG" class="main-image" alt="مدفأة كهربائية 360° من كوبرا" id="mainImage">
                    </div>
                    <div class="thumbnail-container">
                        <img src="assets/img/img1.JPG" class="thumbnail active" data-image="assets/img/img1.JPG" alt="مدفأة كهربائية 360° من كوبرا">
                        <img src="assets/img/img2.JPG" class="thumbnail" data-image="assets/img/img2.JPG" alt="مدفأة كهربائية 360° من الداخل - كوبرا">
                        <img src="assets/img/img3.JPG" class="thumbnail" data-image="assets/img/img3.JPG" alt="مدفأة كهربائية 360° من الداخل - كوبرا">
                    </div>
                </div>
            </div>
            
            <!-- Order Form & Details Section - RIGHT SIDE (SCROLLABLE) -->
            <div class="col-lg-6">
                <div class="scrollable-content">
                    <!-- Order Form Section -->
                    <div class="order-form-section">
                        <div class="order-form">
                            <div class="form-header">
                                <h2>اطلب الآن واحصل على المدفأة</h2>
                                <div class="urgency-badge">
                                    <i class="fas fa-bolt me-1"></i>عرض محدود
                                </div>
                            </div>
                            <form id="purchaseForm" action="process_order.php" method="POST">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="firstName" class="form-label">الاسم *</label>
                                        <input type="text" class="form-control" id="firstName" name="first_name" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="lastName" class="form-label">اللقب *</label>
                                        <input type="text" class="form-control" id="lastName" name="last_name" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="phone" class="form-label">رقم الهاتف *</label>
                                    <input type="tel" class="form-control" id="phone" name="phone_number" placeholder="مثال: 0772750151" required>
                                </div>
                                <div class="mb-3">
                                   <label for="wilaya" class="form-label">اختر ولايتك *</label>
                                   <select class="form-select" id="wilaya" name="wilaya" required>
                                    <option value="" selected disabled>-- اختر ولاية --</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                   <label for="commune" class="form-label">اختر بلديتك *</label>
                                   <select class="form-select" id="commune" name="commune" required>
                                   <option value="" selected disabled>-- اختر ولاية أولاً --</option>
                                    </select>
                                </div>

                                <!-- 🔥 COLOR OPTION REMOVED -->

                                <div class="mb-4 delivery-options">
                                    <label class="form-label">خيار التوصيل *</label>

                                    <!-- Home Delivery -->
                                    <label class="delivery-card">
                                        <input type="radio" name="delivery_option" id="homeDelivery" value="home_delivery" required>
                                        <div class="content">
                                            <div class="title">🚚 التوصيل إلى المنزل</div>
                                            <div class="price">750 – 1,200 دج</div>
                                            <div class="note">قد ترتفع في ولايات الجنوب حتى 1,400 – 1,600 دج</div>
                                        </div>
                                    </label>

                                    <!-- Stop Desk -->
                                    <label class="delivery-card">
                                        <input type="radio" name="delivery_option" id="companyDelivery" value="company_pickup">
                                        <div class="content">
                                            <div class="title">🏢 الاستلام من مكتب ياليدين (Stop Desk)</div>
                                            <div class="price">400 – 750 دج</div>
                                            <div class="note">قد ينخفض إلى 350 دج في بعض المكاتب</div>
                                        </div>
                                    </label>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-fire me-2"></i>تأكيد الطلب والحصول على المدفأة
                                </button>
                                
                                <div class="security-badges mt-3">
                                    <div class="badge-item">
                                        <i class="fas fa-shield-alt"></i>
                                        <span>دفع آمن</span>
                                    </div>
                                    <div class="badge-item">
                                        <i class="fas fa-truck"></i>
                                        <span>توصيل سريع</span>
                                    </div>
                                    <div class="badge-item">
                                        <i class="fas fa-medal"></i>
                                        <span>ضمان الجودة</span>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Description Section -->
                    <div class="description-section">
                        <h3>مدفأة كهربائية 360° بخمس واجهات من كوبرا 💨</h3>
                        <p class="product-description">
                            قوة 2000W وتستهلك قليل 🔋<br>تسخّن الغرفة كاملة فـ وقت قصير 👌
                        </p>
                        
                        <div class="features-grid">
                            <div class="feature-card">
                                <div class="feature-icon">🔥</div>
                                <div class="feature-text">تدفئة سريعة بـ 5 واجهات</div>
                            </div>
                            <div class="feature-card">
                                <div class="feature-icon">🌿</div>
                                <div class="feature-text">استهلاك اقتصادي للطاقة</div>
                            </div>
                            <div class="feature-card">
                                <div class="feature-icon">🔇</div>
                                <div class="feature-text">تشغيل هادئ وآمن</div>
                            </div>
                            <div class="feature-card">
                                <div class="feature-icon">💡</div>
                                <div class="feature-text">تصميم أنيق ومحمول</div>
                            </div>
                        </div>
                        
                        <p class="text-center mb-0 lead">
                            <strong>🚚 التوصيل متوفر فـ كامل الولايات 🇩🇿</strong><br>
                        </p>
                    </div>
                    
                    <!-- Product Details Section -->
                    <section class="detail-section">
                        <div class="row">
                            <div class="col-lg-12">
                                <h3>تفاصيل المنتج</h3>
                                <p class="product-description">
                                    <strong>Chauffage Electrique et Gril 360 Degrés Eco Faible Consommation 5 façades 2000 W</strong><br><br>
                                    المدفأة الكهربائية 360° من كوبرا هي الحل الأمثل للشتاء القادم! بفضل تصميمها الفريد بخمس واجهات، 
                                    توفر تدفئة متساوية في كل أركان الغرفة. الجهاز مصمم بتقنية اقتصادية في استهلاك الطاقة مع الحفاظ على كفاءة عالية في التدفئة.
                                </p>
                                
                                <h3>مميزات إضافية</h3>
                                <p class="product-description">
                                    • نظام أمان متكامل ضد السخونة الزائدة<br>
                                    • تشغيل هادئ لا يزعج أثناء النوم<br>
                                    • تحكم سهل في درجة الحرارة<br>
                                    • تصميم محمول يمكن نقله بسهولة بين الغرف<br>
                                    • مناسب للاستخدام في المنازل والمكاتب
                                </p>
                                
                                <h3>لماذا تختار مدفأتنا؟</h3>
                                <p class="product-description">
                                    تم تصميم المدفأة الكهربائية من كوبرا بأعلى معايير الجودة والسلامة الأوروبية. 
                                    الجهاز سهل الاستخدام ويأتي بضمان لمدة عام، مما يضمن لك شتاءً دافئاً وآمناً.
                                </p>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5>مارينا شوب</h5>
                    <p>نسعى لتقديم أفضل الأجهزة المنزلية بجودة عالية وأسعار مناسبة للجميع.</p>
                </div>
                <div class="col-md-4">
                    <h5>معلومات التواصل</h5>
                    <div class="contact-info">
                        <p><i class="fas fa-phone me-2"></i> 0772750151</p>
                        <p><i class="fas fa-envelope me-2"></i> Seifsaib16@gmail.com</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <h5>تابعنا على</h5>
                    <div class="d-flex gap-3 fs-4">
                        <a href="https://www.facebook.com/share/1Erp4ikELT" target="_blank"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-tiktok"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap & jQuery JS -->
    <script src="https://code.jquery.com/jquery-3.7.0/min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom JavaScript -->
    <script>
        // Image gallery functionality
        document.querySelectorAll('.thumbnail').forEach(thumb => {
            thumb.addEventListener('click', function() {
                // Update main image
                document.getElementById('mainImage').src = this.getAttribute('data-image');
                
                // Update active thumbnail
                document.querySelectorAll('.thumbnail').forEach(t => {
                    t.classList.remove('active');
                });
                this.classList.add('active');
            });
        });

        // Form validation
        document.getElementById('purchaseForm').addEventListener('submit', function(e) {
            const firstName = document.getElementById('firstName').value;
            const lastName = document.getElementById('lastName').value;
            const phone = document.getElementById('phone').value;
            const wilaya = document.getElementById('wilaya').value;
            const commune = document.getElementById('commune').value;
            const deliveryOption = document.querySelector('input[name="delivery_option"]:checked');

             if (!firstName || !lastName || !phone || !wilaya || !commune || !deliveryOption) {
                 e.preventDefault();
                 alert('يرجى ملء جميع الحقول الإلزامية.');
               return;
                }
            
            // Phone validation
            const phoneRegex = /^0[5-7][0-9]{8}$/;
            if (!phoneRegex.test(phone)) {
                e.preventDefault();
                alert('يرجى إدخال رقم هاتف جزائري صحيح (10 أرقام تبدأ بـ 05، 06، أو 07).');
                return;
            }
        });

        // Ensure only one delivery option can be selected
        const deliveryOptions = document.querySelectorAll('input[name="delivery_option"]');
        deliveryOptions.forEach(option => {
            option.addEventListener('change', () => {
                if (option.checked) {
                    deliveryOptions.forEach(otherOption => {
                        if (otherOption !== option) {
                            otherOption.checked = false;
                        }
                    });
                }
            });
        });

        // Load wilayas
        fetch("get_wilayas.php")
          .then(response => response.json())
          .then(data => {
            let wilayaSelect = document.getElementById("wilaya");
            data.forEach(w => {
              let option = document.createElement("option");
              option.value = w.id;
              option.textContent = w.willaya + " - " + w.ar_name;
              wilayaSelect.appendChild(option);
            });
          });

        // Load communes when wilaya changes
        document.getElementById("wilaya").addEventListener("change", function() {
          let wilayaId = this.value;
          let communeSelect = document.getElementById("commune");
          communeSelect.innerHTML = '<option selected disabled>جاري التحميل...</option>';

          fetch("get_communes.php?wilaya_id=" + wilayaId)
            .then(response => response.json())
            .then(data => {
              communeSelect.innerHTML = '<option value="" selected disabled>-- اختر بلدية --</option>';
              data.forEach(c => {
                let option = document.createElement("option");
                option.value = c.id;
                option.textContent = c.name + " - " + c.ar_name;
                communeSelect.appendChild(option);
              });
            })
            .catch(error => {
              communeSelect.innerHTML = '<option disabled>خطأ في تحميل البلديات</option>';
              console.error("Error loading communes:", error);
            });
        });
    </script>

</body>
</html>