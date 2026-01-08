@extends('layout.customer.app')

@section('title', 'تحميل التطبيقات')

@push('css')
<style>
    :root {
        --primary: #ECC631;
        --secondary: #333127;
        --text: #222222;
        --bg: #f0f0f0;
    }

    body {
        font-family: 'Tajawal', sans-serif;
        background-color: #f9fafb;
    }

    .app-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        border: 2px solid transparent;
        overflow: hidden;
    }

    .app-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        border-color: var(--primary);
    }

    .app-icon {
        width: 80px;
        height: 80px;
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        font-size: 36px;
        color: white;
    }

    .pos-icon {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .scanner-icon {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }

    .download-btn {
        background: var(--primary);
        color: white;
        padding: 12px 32px;
        border-radius: 12px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        transition: all 0.3s ease;
        text-decoration: none;
        border: 2px solid transparent;
    }

    .download-btn:hover {
        background: white;
        color: var(--primary);
        border-color: var(--primary);
        transform: scale(1.05);
    }

    .file-info {
        background: #f8fafc;
        border-radius: 12px;
        padding: 15px;
        margin-top: 20px;
        border-right: 4px solid var(--primary);
    }

    .info-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 0;
        border-bottom: 1px solid #e5e7eb;
    }

    .info-item:last-child {
        border-bottom: none;
    }

    .info-label {
        color: #6b7280;
        font-weight: 500;
    }

    .info-value {
        color: #1f2937;
        font-weight: 600;
    }

    .version-badge {
        background: var(--primary);
        color: white;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        display: inline-block;
    }

    .feature-list {
        list-style: none;
        padding: 0;
        margin: 20px 0;
    }

    .feature-list li {
        padding: 8px 0;
        padding-right: 30px;
        position: relative;
        color: #4b5563;
    }

    .feature-list li:before {
        content: "✓";
        position: absolute;
        right: 0;
        color: var(--primary);
        font-weight: bold;
    }

    .qr-section {
        background: white;
        border-radius: 16px;
        padding: 30px;
        text-align: center;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        margin-top: 40px;
    }

    .qr-code {
        width: 200px;
        height: 200px;
        margin: 20px auto;
        padding: 10px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .installation-steps {
        background: white;
        border-radius: 16px;
        padding: 30px;
        margin-top: 40px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
    }

    .step {
        display: flex;
        align-items: flex-start;
        gap: 20px;
        margin-bottom: 25px;
        padding-bottom: 25px;
        border-bottom: 1px solid #e5e7eb;
    }

    .step:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }

    .step-number {
        width: 40px;
        height: 40px;
        background: var(--primary);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        flex-shrink: 0;
    }

    .step-content h4 {
        color: #1f2937;
        margin-bottom: 8px;
        font-weight: 600;
    }

    .step-content p {
        color: #6b7280;
        margin: 0;
    }

    @media (max-width: 768px) {
        .app-icon {
            width: 70px;
            height: 70px;
            font-size: 30px;
        }

        .download-btn {
            width: 100%;
            padding: 14px 24px;
        }

        .qr-section {
            padding: 20px;
        }

        .qr-code {
            width: 180px;
            height: 180px;
        }
    }

    /* أنيميشن للتنزيل */
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }

    .downloading {
        animation: pulse 1s infinite;
    }

    /* تنسيقات إضافية */
    .section-title {
        position: relative;
        padding-bottom: 15px;
        margin-bottom: 30px;
    }

    .section-title:after {
        content: '';
        position: absolute;
        bottom: 0;
        right: 0;
        width: 100px;
        height: 4px;
        background: var(--primary);
        border-radius: 2px;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-top: 30px;
    }

    .stat-card {
        background: white;
        padding: 20px;
        border-radius: 12px;
        text-align: center;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        border-top: 4px solid var(--primary);
    }

    .stat-card .number {
        font-size: 32px;
        font-weight: bold;
        color: var(--primary);
        margin: 10px 0;
    }

    .stat-card .label {
        color: #6b7280;
        font-size: 14px;
    }
</style>
@endpush

@section('content')
<div dir="rtl" class="container mx-auto px-4 py-8">
    <!-- العنوان الرئيسي -->
    <div class="text-center mb-12">
        <h1 class="text-3xl sm:text-4xl font-bold text-gray-800 mb-4">
            <i class="fas fa-mobile-alt ml-2 text-yellow-500"></i>
            تطبيقات نظام إدارة الملحمه
        </h1>
        {{-- <p class="text-gray-600 text-lg max-w-3xl mx-auto">
            حمّل التطبيقات الخاصة بنظام إدارة الملحمه على أجهزتك الذكية
        </p> --}}
    </div>

    <!-- بطاقات التطبيقات -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
        <!-- تطبيق نقطة البيع -->
        <div class="app-card p-8">
            <div class="text-center mb-6">
                <div class="app-icon pos-icon">
                    <i class="fas fa-cash-register"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-800 mb-2">Meat POS</h2>
                <p class="text-gray-600 mb-4">نظام نقطة البيع للملحمه</p>
                <span class="version-badge">الإصدار 1.0.0</span>
            </div>

            <ul class="feature-list mb-6">
                <li>إدارة المبيعات اليومية</li>
                <li>طباعة الفواتير</li>
                <li>إدارة العملاء</li>
                <li>تقارير المبيعات الفورية</li>
                <li>مزامنة مع النظام الرئيسي</li>
            </ul>

            <div class="file-info">
                <div class="info-item">
                    <span class="info-label">حجم الملف:</span>
                    <span class="info-value">42 MB</span>
                </div>
                <div class="info-item">
                    <span class="info-label">آخر تحديث:</span>
                    <span class="info-value">{{ date('Y-m-d') }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">التوافق:</span>
                    <span class="info-value">Android 8.0+</span>
                </div>
            </div>

            <div class="text-center mt-8">
                <a href="{{ asset('public/assets/app/meat-pos.apk') }}"
                   class="download-btn"
                   download="meat-pos.apk"
                   onclick="startDownload('meat-pos')">
                    <i class="fas fa-download ml-2"></i>
                    تحميل التطبيق
                </a>
            </div>
        </div>

        <!-- تطبيق الماسح الضوئي -->
        <div class="app-card p-8">
            <div class="text-center mb-6">
                <div class="app-icon scanner-icon">
                    <i class="fas fa-barcode"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-800 mb-2">Meat Scanner</h2>
                <p class="text-gray-600 mb-4">ماسح الباركود للمخزون</p>
                <span class="version-badge">الإصدار 1.0.0</span>
            </div>

            <ul class="feature-list mb-6">
                <li>مسح الباركود السريع</li>
                <li>تحديث المخزون تلقائياً</li>
                <li>جرد المخزون</li>
                <li>قراءة باركود المنتجات</li>
                <li>مزامنة فورية مع النظام</li>
            </ul>

            <div class="file-info">
                <div class="info-item">
                    <span class="info-label">حجم الملف:</span>
                    <span class="info-value">112 MB</span>
                </div>
                <div class="info-item">
                    <span class="info-label">آخر تحديث:</span>
                    <span class="info-value">{{ date('Y-m-d') }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">التوافق:</span>
                    <span class="info-value">Android 8.0+</span>
                </div>
            </div>

            <div class="text-center mt-8">
                <a href="{{ asset('public/assets/app/meat-scanner.apk') }}"
                   class="download-btn"
                   download="meat-scanner.apk"
                   onclick="startDownload('meat-scanner')">
                    <i class="fas fa-download ml-2"></i>
                    تحميل التطبيق
                </a>
            </div>
        </div>
    </div>

    <!-- إحصائيات التنزيل -->
    <div class="stats-grid mb-12">
        <div class="stat-card">
            <i class="fas fa-download text-2xl text-yellow-500"></i>
            <div class="number" id="totalDownloads">1,247</div>
            <div class="label">إجمالي التنزيلات</div>
        </div>
        <div class="stat-card">
            <i class="fas fa-users text-2xl text-yellow-500"></i>
            <div class="number" id="activeUsers">856</div>
            <div class="label">مستخدم نشط</div>
        </div>
        <div class="stat-card">
            <i class="fas fa-star text-2xl text-yellow-500"></i>
            <div class="number">4.8</div>
            <div class="label">تقييم التطبيق</div>
        </div>
        <div class="stat-card">
            <i class="fas fa-sync text-2xl text-yellow-500"></i>
            <div class="number">99.8%</div>
            <div class="label">معدل التشغيل</div>
        </div>
    </div>

    <!-- خطوات التثبيت -->
    <div class="installation-steps">
        <h3 class="text-2xl font-bold text-gray-800 mb-6 section-title">
            <i class="fas fa-cogs ml-2"></i>
            خطوات تثبيت التطبيقات
        </h3>

        <div class="steps">
            <div class="step">
                <div class="step-number">1</div>
                <div class="step-content">
                    <h4>تفعيل التثبيت من مصادر غير معروفة</h4>
                    <p>اذهب إلى إعدادات الهاتف → الأمان → وقم بتفعيل خيار "مصادر غير معروفة" أو "Install unknown apps"</p>
                </div>
            </div>

            <div class="step">
                <div class="step-number">2</div>
                <div class="step-content">
                    <h4>تنزيل ملف التطبيق</h4>
                    <p>انقر على زر التحميل المناسب وسيبدأ تحميل ملف APK على جهازك</p>
                </div>
            </div>

            <div class="step">
                <div class="step-number">3</div>
                <div class="step-content">
                    <h4>فتح ملف التثبيت</h4>
                    <p>بعد انتهاء التحميل، افتح الملف من مجلد التنزيلات أو الإشعارات</p>
                </div>
            </div>

            <div class="step">
                <div class="step-number">4</div>
                <div class="step-content">
                    <h4>متابعة التثبيت</h4>
                    <p>اتبع التعليمات الظاهرة على الشاشة لإكمال عملية التثبيت</p>
                </div>
            </div>

            <div class="step">
                <div class="step-number">5</div>
                <div class="step-content">
                    <h4>تسجيل الدخول</h4>
                    <p>افتح التطبيق وقم بتسجيل الدخول باستخدام بيانات حسابك في النظام</p>
                </div>
            </div>
        </div>
    </div>

    <!-- قسم المساعدة -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-12">
        <div class="bg-yellow-50 p-6 rounded-xl border border-yellow-200">
            <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-question-circle text-yellow-600 text-xl"></i>
                </div>
                <h4 class="font-bold text-gray-800">الدعم الفني</h4>
            </div>
            <p class="text-gray-600 text-sm">لديك استفسار؟ فريق الدعم جاهز لمساعدتك</p>
            <a href="#" class="text-yellow-600 font-medium text-sm mt-3 inline-block">
                <i class="fas fa-headset ml-2"></i>
                تواصل مع الدعم
            </a>
        </div>

        <div class="bg-blue-50 p-6 rounded-xl border border-blue-200">
            <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-book text-blue-600 text-xl"></i>
                </div>
                <h4 class="font-bold text-gray-800">دليل المستخدم</h4>
            </div>
            <p class="text-gray-600 text-sm">تعرف على كيفية استخدام التطبيقات بكفاءة</p>
            <a href="#" class="text-blue-600 font-medium text-sm mt-3 inline-block">
                <i class="fas fa-download ml-2"></i>
                تحميل الدليل
            </a>
        </div>

        <div class="bg-green-50 p-6 rounded-xl border border-green-200">
            <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-sync-alt text-green-600 text-xl"></i>
                </div>
                <h4 class="font-bold text-gray-800">تحديثات دورية</h4>
            </div>
            <p class="text-gray-600 text-sm">نتابع التطوير المستمر لإضافة مميزات جديدة</p>
            <a href="#" class="text-green-600 font-medium text-sm mt-3 inline-block">
                <i class="fas fa-bell ml-2"></i>
                اشترك في التحديثات
            </a>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
    // مؤشرات التنزيل الوهمية (يمكن استبدالها ببيانات حقيقية من قاعدة البيانات)
    const downloadStats = {
        'meat-pos': 742,
        'meat-scanner': 505
    };

    function startDownload(appName) {
        // إضافة تأثير التنزيل
        const button = event.target.closest('.download-btn');
        button.classList.add('downloading');
        button.innerHTML = '<i class="fas fa-spinner fa-spin ml-2"></i> جاري التنزيل...';

        // تحديث الإحصائيات (محاكاة)
        downloadStats[appName]++;
        updateDownloadStats();

        // إعادة الزر لحالته الأصلية بعد 3 ثوان (محاكاة)
        setTimeout(() => {
            button.classList.remove('downloading');
            button.innerHTML = '<i class="fas fa-check ml-2"></i> تم التنزيل بنجاح';

            setTimeout(() => {
                button.innerHTML = '<i class="fas fa-download ml-2"></i> تحميل التطبيق';
            }, 2000);
        }, 3000);

        // يمكن هنا إضافة تتبع حقيقي للتنزيلات
        trackDownload(appName);
    }

    function updateDownloadStats() {
        const total = downloadStats['meat-pos'] + downloadStats['meat-scanner'];
        document.getElementById('totalDownloads').textContent = total.toLocaleString();
    }

    function trackDownload(appName) {
        // يمكن إضافة كود Ajax هنا لتتبع التنزيلات في قاعدة البيانات
        console.log(`تم تحميل ${appName}`);

        // مثال على طلب Ajax:
        /*
        $.ajax({
            url: '/api/track-download',
            method: 'POST',
            data: {
                app: appName,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                console.log('تم تتبع التنزيل');
            }
        });
        */
    }

    // تهيئة الصفحة
    $(document).ready(function() {
        updateDownloadStats();

        // تحديث عدد المستخدمين النشطين (محاكاة)
        setInterval(() => {
            const activeUsers = Math.floor(Math.random() * 50) + 800;
            document.getElementById('activeUsers').textContent = activeUsers;
        }, 10000);
    });

    // إضافة تأثيرات تفاعلية
    document.querySelectorAll('.app-card').forEach(card => {
        card.addEventListener('mouseenter', () => {
            const icon = card.querySelector('.app-icon');
            icon.style.transform = 'scale(1.1) rotate(5deg)';
        });

        card.addEventListener('mouseleave', () => {
            const icon = card.querySelector('.app-icon');
            icon.style.transform = 'scale(1) rotate(0deg)';
        });
    });
</script>
@endpush
