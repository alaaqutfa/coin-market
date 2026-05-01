<div id="registerModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 z-50 hidden items-center justify-center">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4 p-6 relative" dir="rtl"
        style="max-height: 90vh; overflow-y: auto;">
        <h3 class="text-xl font-bold mb-4 text-gray-800">إنشاء حساب جديد</h3>
        <p class="text-gray-600 text-sm mb-4">لإكمال عملية الشراء، يرجى إنشاء حساب.</p>
        <form id="registerForm">
            @csrf
            <input type="hidden" id="reg_product_id" name="product_id">
            <input type="hidden" id="reg_quantity" name="quantity" value="1">
            <input type="hidden" id="reg_action_type" name="action_type" value="cart"> <!-- cart أو whatsapp -->

            <div class="mb-4">
                <label class="block mb-1 text-gray-700">الاسم الكامل *</label>
                <input type="text" name="name" id="reg_name" required class="w-full border rounded-lg p-2">
            </div>
            <div class="mb-4">
                <label class="block mb-1 text-gray-700">رقم الهاتف (لبناني) *</label>
                <input type="tel" name="phone" id="reg_phone" required class="w-full border rounded-lg p-2"
                    autocomplete="off">
                <p class="text-gray-500 text-xs">مثال: 03457320 أو +9613457320</p>
                <input type="hidden" id="reg_phone_full" name="phone_full">
            </div>
            <div class="mb-4">
                <label class="block mb-1 text-gray-700">كلمة المرور *</label>
                <input type="password" name="password" id="reg_password" required class="w-full border rounded-lg p-2">
            </div>
            <div class="mb-4">
                <label class="block mb-1 text-gray-700">تأكيد كلمة المرور *</label>
                <input type="password" name="password_confirmation" id="reg_password_confirmation" required
                    class="w-full border rounded-lg p-2">
            </div>
            <div class="mb-4">
                <label class="block mb-1 text-gray-700">العنوان التفصيلي</label>
                <textarea name="address" id="reg_address" rows="2" class="w-full border rounded-lg p-2"></textarea>
            </div>
            <div class="mb-4">
                <label class="block mb-1 text-gray-700">رابط موقع الخريطة (Google Maps)</label>
                <input type="url" name="map_link" id="reg_map_link" class="w-full border rounded-lg p-2"
                    placeholder="https://maps.app.goo.gl/...">
                <button type="button" id="getLocationBtnReg" class="mt-2 text-yellow-600 text-sm"><i
                        class="fas fa-location-dot"></i> استخدام موقعي الحالي</button>
            </div>
            <div class="flex gap-3 mt-6">
                <button type="submit" class="bg-yellow-500 text-white px-4 py-2 rounded-lg flex-1">تسجيل وإنشاء
                    حساب</button>
                <button type="button" id="closeRegisterModalBtn"
                    class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg flex-1">إلغاء</button>
            </div>
        </form>
        <div class="mt-4 text-center">
            <p>لديك حساب بالفعل؟ <a href="#" id="showLoginLink" class="text-yellow-600">تسجيل دخول</a></p>
        </div>
    </div>
</div>

<!-- مودال تسجيل الدخول (اختياري للمستخدم الحالي) -->
<div id="loginModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 z-50 hidden items-center justify-center">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4 p-6 relative">
        <h3 class="text-xl font-bold mb-4 text-gray-800">تسجيل الدخول</h3>
        <form id="loginForm">
            @csrf
            <input type="hidden" id="login_product_id" name="product_id">
            <input type="hidden" id="login_quantity" name="quantity" value="1">
            <input type="hidden" id="login_action_type" name="action_type" value="cart">
            <div class="mb-4">
                <label class="block mb-1">رقم الهاتف *</label>
                <input type="tel" name="phone" id="login_phone" required class="w-full border rounded-lg p-2">
            </div>
            <div class="mb-4">
                <label class="block mb-1">كلمة المرور *</label>
                <input type="password" name="password" id="login_password" required
                    class="w-full border rounded-lg p-2">
            </div>
            <div class="flex gap-3 mt-6">
                <button type="submit" class="bg-yellow-500 text-white px-4 py-2 rounded-lg flex-1">دخول</button>
                <button type="button" id="closeLoginModalBtn"
                    class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg flex-1">إلغاء</button>
            </div>
        </form>
        <div class="mt-4 text-center">
            <p>ليس لديك حساب؟ <a href="#" id="showRegisterLink" class="text-yellow-600">إنشاء حساب</a></p>
        </div>
    </div>
</div>

@push('script')
    <script>
        $(document).ready(function () {
            let currentProductId = null;
            let currentQuantity = 1;
            let currentAction = 'cart'; // 'cart' or 'whatsapp'
            let pendingActionData = null;

            // تهيئة intl-tel-input للحقلين
            let itiReg = null, itiLogin = null;
            function initIntl() {
                const regPhone = document.querySelector("#reg_phone");
                if (regPhone && !itiReg) {
                    itiReg = window.intlTelInput(regPhone, {
                        initialCountry: "lb",
                        preferredCountries: ["lb"],
                        separateDialCode: true,
                        utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@23.5.0/build/js/utils.js",
                        nationalMode: false,
                        autoHideDialCode: false,
                        formatOnDisplay: true,
                        placeholderNumberType: "MOBILE"
                    });
                }
                const loginPhone = document.querySelector("#login_phone");
                if (loginPhone && !itiLogin) {
                    itiLogin = window.intlTelInput(loginPhone, {
                        initialCountry: "lb",
                        preferredCountries: ["lb"],
                        separateDialCode: true,
                        utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@23.5.0/build/js/utils.js",
                        nationalMode: false,
                        autoHideDialCode: false,
                    });
                }
            }
            initIntl();

            // إظهار/إخفاء المودالات
            function showRegisterModal(productId, quantity, action) {
                currentProductId = productId;
                currentQuantity = quantity;
                currentAction = action;
                $('#reg_product_id').val(productId);
                $('#reg_quantity').val(quantity);
                $('#reg_action_type').val(action);
                $('#registerModal').removeClass('hidden').addClass('flex');
                $('#loginModal').addClass('hidden').removeClass('flex');
            }
            function showLoginModal(productId, quantity, action) {
                currentProductId = productId;
                currentQuantity = quantity;
                currentAction = action;
                $('#login_product_id').val(productId);
                $('#login_quantity').val(quantity);
                $('#login_action_type').val(action);
                $('#loginModal').removeClass('hidden').addClass('flex');
                $('#registerModal').addClass('hidden').removeClass('flex');
            }
            function hideAllModals() {
                $('#registerModal, #loginModal').addClass('hidden').removeClass('flex');
            }

            // رابط التحويل بين المودالين
            $('#showLoginLink').on('click', function (e) {
                e.preventDefault();
                const prodId = $('#reg_product_id').val();
                const qty = $('#reg_quantity').val();
                const act = $('#reg_action_type').val();
                showLoginModal(prodId, qty, act);
            });
            $('#showRegisterLink').on('click', function (e) {
                e.preventDefault();
                const prodId = $('#login_product_id').val();
                const qty = $('#login_quantity').val();
                const act = $('#login_action_type').val();
                showRegisterModal(prodId, qty, act);
            });
            $('#closeRegisterModalBtn, #closeLoginModalBtn, .modal-overlay').on('click', function (e) {
                if (e.target === this) hideAllModals();
            });

            // زر الموقع
            $('#getLocationBtnReg').on('click', function () {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(pos => {
                        $('#reg_map_link').val(`https://www.google.com/maps?q=${pos.coords.latitude},${pos.coords.longitude}`);
                    }, () => showToast('تعذر الحصول على الموقع', 'error'));
                } else showToast('المتصفح لا يدعم تحديد الموقع', 'error');
            });

            // دالة مساعدة لتنسيق الرقم
            function getFormattedPhone(itiInstance) {
                if (!itiInstance) return null;
                const raw = itiInstance.getNumber();
                if (!raw) return null;
                if (!itiInstance.isValidNumber()) return null;
                return raw.replace(/^\+/, '');
            }

            // تسجيل حساب عبر AJAX
            $('#registerForm').on('submit', function (e) {
                e.preventDefault();
                const phone = getFormattedPhone(itiReg);
                if (!phone) {
                    showToast('رقم الهاتف غير صحيح', 'error');
                    return;
                }
                const formData = {
                    name: $('#reg_name').val(),
                    phone: phone,
                    password: $('#reg_password').val(),
                    password_confirmation: $('#reg_password_confirmation').val(),
                    address: $('#reg_address').val(),
                    map_link: $('#reg_map_link').val(),
                    product_id: $('#reg_product_id').val(),
                    quantity: $('#reg_quantity').val(),
                    action_type: $('#reg_action_type').val(),
                    _token: '{{ csrf_token() }}'
                };
                $.post('{{ route("customer.register.ajax") }}', formData, function (response) {
                    if (response.success) {
                        showToast(response.message, 'success');
                        hideAllModals();
                        // بعد التسجيل، ننفذ الإجراء المخزن
                        if (response.action_url) {
                            // تنفيذ الإجراء (إضافة للسلة أو واتساب) باستخدام بيانات العميل الجديد
                            $.post(response.action_url, response.action_data, function (res) {
                                if (res.success) {
                                    if (currentAction === 'whatsapp' && res.redirect) {
                                        window.open(res.redirect, '_blank');
                                    } else {
                                        showToast(res.message, 'success');
                                        if (res.cart_count !== undefined) $('.cart-badge').text(res.cart_count);
                                        if (typeof showActionChoice === 'function' && res.checkout_url) {
                                            showActionChoice(res.checkout_url);
                                        }
                                    }
                                } else showToast(res.message || 'حدث خطأ', 'error');
                            }).fail(() => showToast('خطأ في تنفيذ الإجراء', 'error'));
                        }
                    } else {
                        let errMsg = response.message || 'حدث خطأ';
                        if (response.errors) {
                            errMsg = Object.values(response.errors).flat().join('\n');
                        }
                        showToast(errMsg, 'error');
                    }
                }).fail(xhr => {
                    let msg = 'خطأ في الاتصال';
                    if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                    showToast(msg, 'error');
                });
            });

            // تسجيل الدخول عبر AJAX
            $('#loginForm').on('submit', function (e) {
                e.preventDefault();
                const phone = getFormattedPhone(itiLogin);
                if (!phone) {
                    showToast('رقم الهاتف غير صحيح', 'error');
                    return;
                }
                const formData = {
                    phone: phone,
                    password: $('#login_password').val(),
                    product_id: $('#login_product_id').val(),
                    quantity: $('#login_quantity').val(),
                    action_type: $('#login_action_type').val(),
                    _token: '{{ csrf_token() }}'
                };
                $.post('{{ route("customer.login.ajax") }}', formData, function (response) {
                    if (response.success) {
                        showToast(response.message, 'success');
                        hideAllModals();
                        if (response.action_url) {
                            $.post(response.action_url, response.action_data, function (res) {
                                if (res.success) {
                                    if (currentAction === 'whatsapp' && res.redirect) {
                                        window.open(res.redirect, '_blank');
                                    } else {
                                        showToast(res.message, 'success');
                                        if (res.cart_count !== undefined) $('.cart-badge').text(res.cart_count);
                                        if (typeof showActionChoice === 'function' && res.checkout_url) {
                                            showActionChoice(res.checkout_url);
                                        }
                                    }
                                } else showToast(res.message || 'حدث خطأ', 'error');
                            }).fail(() => showToast('خطأ في تنفيذ الإجراء', 'error'));
                        }
                    } else {
                        showToast(response.message || 'بيانات الدخول غير صحيحة', 'error');
                    }
                }).fail(() => showToast('خطأ في الاتصال', 'error'));
            });

            // عندما يضغط العميل على زر إضافة للسلة أو واتساب
            $(document).on('click', '.add-to-cart-btn, .whatsapp-order-btn', function (e) {
                e.preventDefault();
                const $btn = $(this);
                let quantity = 1;
                const $card = $btn.closest('.group, .product-card, .product-item, .relative, .bg-white');
                if ($card.length) {
                    let qtyInput = $card.find('.product-quantity-input');
                    if (qtyInput.length) quantity = parseInt(qtyInput.val()) || 1;
                } else {
                    let globalQty = $('.product-quantity-input').first();
                    if (globalQty.length) quantity = parseInt(globalQty.val()) || 1;
                }
                const productId = $btn.data('product-id');
                const action = $btn.hasClass('whatsapp-order-btn') ? 'whatsapp' : 'cart';

                // التحقق من حالة تسجيل الدخول
                $.get('{{ route("customer.checkCustomerAuth") }}', function (authData) {
                    if (authData.logged_in) {
                        // مسجل دخول => ننفذ الإجراء مباشرة مع بيانات العميل المخزنة
                        $.get('{{ route("customer.current") }}', function (customerData) {
                            console.clear();
                            console.log(customerData);

                            if (customerData) {
                                const formData = {
                                    product_id: productId,
                                    quantity: quantity,
                                    customer_name: customerData.name,
                                    customer_phone: customerData.phone,
                                    customer_address: customerData.address,
                                    map_link: customerData.map_link,
                                    _token: '{{ csrf_token() }}'
                                };
                                const actionUrl = action === 'cart' ? '{{ route("customer.cart.add") }}' : '{{ route("customer.order.whatsapp") }}';
                                $.post(actionUrl, formData, function (res) {
                                    if (res.success) {
                                        if (action === 'whatsapp' && res.redirect) window.open(res.redirect, '_blank');
                                        else {
                                            showToast(res.message, 'success');
                                            if (res.cart_count !== undefined) $('.cart-badge').text(res.cart_count);
                                            if (typeof showActionChoice === 'function' && res.checkout_url) {
                                                showActionChoice(res.checkout_url);
                                            }
                                        }
                                    } else showToast(res.message || 'حدث خطأ', 'error');
                                }).fail(() => showToast('خطأ في التنفيذ', 'error'));
                            }
                        });
                    } else {
                        // غير مسجل: نعرض مودال التسجيل
                        showRegisterModal(productId, quantity, action);
                    }
                }).fail(() => {
                    showRegisterModal(productId, quantity, action);
                });
            });
        });
    </script>
@endpush
