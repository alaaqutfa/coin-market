<footer dir="rtl" class="bg-white border-t border-gray-200">
    <div class="mx-auto w-full max-w-screen-xl p-4 py-6 lg:py-8">
        <div class="md:flex md:justify-between">
            <div class="mb-6 md:mb-0 md:w-1/3">
                <a href="{{ url('/') }}" class="flex items-center mb-4">
                    <img src="{{ asset('public/assets/img/logo-light.png') }}" class="h-8 me-3" alt="Coin Market Logo" />
                    <span class="self-center text-2xl font-semibold whitespace-nowrap">
                        Coin
                        <span style="color: var(--primary);">
                            Market
                        </span>
                    </span>
                </a>
                <p class="text-gray-600 text-sm mb-4">
                    متجرنا الإلكتروني يوفر مجموعة متنوعة من المنتجات المميزة بأفضل الأسعار والجودة العالية.
                </p>
                <div class="flex gap-4">
                    <a href="https://www.facebook.com/profile.php?id=61580920731064" class="text-gray-500 hover:text-yellow-500 transition-colors duration-200">
                        <i class="fab fa-facebook-f text-lg"></i>
                    </a>
                    <a href="https://www.instagram.com/coin.market.zouk/" class="text-gray-500 hover:text-yellow-500 transition-colors duration-200">
                        <i class="fab fa-instagram text-lg"></i>
                    </a>
                    <a href="https://www.tiktok.com/@coin.market.zouk3" class="text-gray-500 hover:text-yellow-500 transition-colors duration-200">
                        <i class="fab fa-tiktok text-lg"></i>
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 gap-8 md:w-2/3">
                <!-- الفئات -->
                <div>
                    <h2 class="mb-6 text-sm font-semibold text-gray-900 uppercase">الفئات الرئيسية</h2>
                    <ul class="text-gray-600 font-medium space-y-2">
                        @php
                            $footerCategories = App\Models\Category::withCount('products')
                                ->orderBy('products_count', 'desc')
                                ->take(5)
                                ->get();
                        @endphp

                        @foreach ($footerCategories as $category)
                            <li>
                                <a href="?category={{ $category->id }}"
                                    class="hover:text-yellow-500 transition-colors duration-200 flex items-center gap-2">
                                    <i class="fas fa-folder text-xs"></i>
                                    {{ $category->name }}
                                    <span class="text-xs bg-gray-100 text-gray-600 px-1.5 py-0.5 rounded">
                                        {{ $category->products_count }}
                                    </span>
                                </a>
                            </li>
                        @endforeach
                        <li>
                            <a href="{{ url('/') }}"
                                class="text-yellow-500 hover:text-yellow-600 font-medium text-sm flex items-center gap-2">
                                <i class="fas fa-eye ml-1"></i>
                                عرض جميع الفئات
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- روابط سريعة -->
                <div>
                    <h2 class="mb-6 text-sm font-semibold text-gray-900 uppercase">روابط سريعة</h2>
                    <ul class="text-gray-600 font-medium space-y-2">
                        <li>
                            <a href="{{ url('/') }}"
                                class="hover:text-yellow-500 transition-colors duration-200 flex items-center gap-2">
                                <i class="fas fa-home text-xs"></i>
                                الصفحة الرئيسية
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('customer.product.show', 'latest') }}"
                                class="hover:text-yellow-500 transition-colors duration-200 flex items-center gap-2">
                                <i class="fas fa-star text-xs"></i>
                                المنتجات المميزة
                            </a>
                        </li>
                        <li>
                            <a href="?category="
                                class="hover:text-yellow-500 transition-colors duration-200 flex items-center gap-2">
                                <i class="fas fa-fire text-xs"></i>
                                الأكثر مبيعاً
                            </a>
                        </li>
                        <li>
                            <a href="?price=asc"
                                class="hover:text-yellow-500 transition-colors duration-200 flex items-center gap-2">
                                <i class="fas fa-tag text-xs"></i>
                                العروض والتخفيضات
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- معلومات الاتصال -->
                <div>
                    <h2 class="mb-6 text-sm font-semibold text-gray-900 uppercase">اتصل بنا</h2>
                    <ul class="text-gray-600 font-medium space-y-3">
                        <li class="flex items-start gap-3">
                            <i class="fas fa-map-marker-alt text-yellow-500 mt-1"></i>
                            <span>
                                لبنان, بيروت, زوق مكاييل, حي الخروبي, بالقرب من كنيسة سيدة
                                المعونات
                            </span>
                        </li>
                        <li class="flex items-center gap-3">
                            <i class="fas fa-phone text-yellow-500"></i>
                            <a href="tel:+96171349793" class="hover:text-yellow-500 transition-colors duration-200">
                                +961 71 349 793
                            </a>
                        </li>
                        <li class="flex items-center gap-3">
                            <i class="fas fa-envelope text-yellow-500"></i>
                            <a href="mailto:info@coin-market.store"
                                class="hover:text-yellow-500 transition-colors duration-200">
                                info@coin-market.store
                            </a>
                        </li>
                        <li class="flex items-center gap-3">
                            <i class="fab fa-whatsapp text-yellow-500"></i>
                            <a href="https://wa.me/96171349793" target="_blank"
                                class="hover:text-yellow-500 transition-colors duration-200">
                                واتساب: 96171349793
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <hr class="my-6 border-gray-200 sm:mx-auto lg:my-8" />

        <div class="sm:flex sm:items-center sm:justify-between">
            <span class="text-sm text-gray-500 sm:text-center">
                © {{ date('Y') }}
                <a href="{{ url('/') }}" class="hover:text-yellow-500 transition-colors duration-200">
                    Coin Market™
                </a>
                . جميع الحقوق محفوظة.
            </span>

            <div class="flex mt-4 gap-4 sm:justify-center sm:mt-0">
                <a href="https://www.facebook.com/profile.php?id=61580920731064" class="text-gray-400 hover:text-gray-900 transition-colors duration-200">
                    <i class="fab fa-facebook-f"></i>
                    <span class="sr-only">Facebook</span>
                </a>
                <a href="https://www.instagram.com/coin.market.zouk/" class="text-gray-400 hover:text-gray-900 transition-colors duration-200">
                    <i class="fab fa-instagram"></i>
                    <span class="sr-only">Instagram</span>
                </a>
                <a href="https://www.tiktok.com/@coin.market.zouk3" class="text-gray-400 hover:text-gray-900 transition-colors duration-200">
                    <i class="fab fa-tiktok"></i>
                    <span class="sr-only">TikTok</span>
                </a>
            </div>
        </div>
    </div>
</footer>
