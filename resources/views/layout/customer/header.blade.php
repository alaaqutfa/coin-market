<nav class="bg-white border-gray-200">
    <div class="max-w-screen-xl flex flex-wrap items-center justify-between gap-4 mx-auto p-4">
        <div class="flex justify-center items-center gap-2">
            <a href="{{ route('customer.home') }}" class="flex items-center space-x-3 rtl:space-x-reverse">
                <img src="{{ asset('public/assets/img/logo-light.png') }}"
                    onerror="this.src='{{ asset('public/assets/img/place-holder.png') }}'" class="h-8"
                    alt="Coin Market Logo" />
                <span class="self-center text-2xl font-semibold whitespace-nowrap">Coin <span
                        style="color: var(--primary);">Market</span></span>
            </a>
        </div>
        <div class="actions flex justify-center items-center flex-wrap gap-4">
            <a href="{{ route('customer.home') }}" class="text-gray-700 hover:text-yellow-600">
                الصفحة الرئيسية <i class="fas fa-home ml-1"></i>
            </a>
            <a href="{{ route('customer.orders') }}" class="text-gray-700 hover:text-yellow-600">
                طلباتي <i class="fas fa-list-alt ml-1"></i>
            </a>
            <a href="{{ route('customer.cart.view') }}" class="relative text-gray-700 hover:text-yellow-600">
                سلتي <i class="fas fa-shopping-cart"></i>
                <span
                    class="cart-badge absolute -top-2 -right-2 bg-yellow-500 text-white rounded-full text-xs w-5 h-5 flex items-center justify-center">0</span>
            </a>
            {{-- @auth('customer')
                <span>{{ auth('customer')->user()->name }}</span>
                <form method="POST" action="{{ route('customer.logout') }}" class="inline">
                    @csrf
                    <button type="submit">تسجيل خروج</button>
                </form>
            @else
                <span>
                    <a href="{{ route('customer.login') }}">دخول</a> / <a href="{{ route('customer.register') }}">تسجيل</a>
                </span>
            @endauth --}}
        </div>
    </div>
</nav>
