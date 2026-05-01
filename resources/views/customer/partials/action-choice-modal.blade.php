<div id="actionChoiceModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 z-50 hidden items-center justify-center">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4 p-6 relative" dir="rtl">
        <h3 class="text-xl font-bold mb-4 text-gray-800">تمت إضافة المنتج إلى السلة</h3>
        <p class="text-gray-600 mb-6">ماذا تريد أن تفعل الآن؟</p>
        <div class="flex gap-3">
            <button id="proceedToCheckoutBtn"
                class="flex-1 bg-yellow-500 hover:bg-yellow-600 text-white py-3 rounded-lg font-semibold transition">
                إتمام الطلب الآن
            </button>
            <button id="continueShoppingBtn"
                class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 py-3 rounded-lg font-semibold transition">
                متابعة التسوق
            </button>
        </div>
    </div>
</div>

@push('script')
    <script>
        $(document).ready(function () {
            let pendingCheckoutUrl = null;

            window.showActionChoice = function (checkoutUrl) {
                pendingCheckoutUrl = checkoutUrl;
                $('#actionChoiceModal').removeClass('hidden').addClass('flex');
            };

            $('#proceedToCheckoutBtn').on('click', function () {
                if (pendingCheckoutUrl) {
                    window.location.href = pendingCheckoutUrl;
                }
                $('#actionChoiceModal').addClass('hidden').removeClass('flex');
            });

            $('#continueShoppingBtn, #actionChoiceModal').on('click', function (e) {
                if (e.target === this || $(e.target).is('#continueShoppingBtn')) {
                    $('#actionChoiceModal').addClass('hidden').removeClass('flex');
                }
            });
        });
    </script>
@endpush
