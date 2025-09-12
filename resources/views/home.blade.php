<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Coin Market Social Stock') }}</title>

    <!-- Fonts -->

    <!-- Styles / Scripts -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>

<body>


    <div class="container mx-auto my-8 relative overflow-x-auto shadow-md sm:rounded-lg">

        <table class="w-full text-sm text-left rtl:text-right text-gray-500 ">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3">
                        <div class="flex justify-center items-center flex-col gap-2">
                            <span class="text-base">Barcode</span>
                            <input type="text" name="barcode"
                                class="filter-input block w-full p-2 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3">
                        <div class="flex justify-center items-center flex-col gap-2">
                            <span class="text-base">Name</span>
                            <input type="text" name="name"
                                class="filter-input block w-full p-2 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3">
                        <div class="flex justify-center items-center flex-col gap-2">
                            <span class="text-base">Price</span>
                            <input type="text" name="price"
                                class="filter-input block w-full p-2 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3">
                        <div class="flex justify-center items-center flex-col gap-2">
                            <span class="text-base">Weight</span>
                            <input type="text" name="weight"
                                class="filter-input block w-full p-2 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3">
                        <div class="flex justify-center items-center flex-col gap-2">
                            <span class="text-base">Action</span>
                        </div>
                    </th>
                </tr>
            </thead>
            <tbody id="products-table-body">
                @if (count($products) > 0)
                    @include('partials.products-table', ['products' => $products])
                @else
                    <tr>
                        <td colspan="5">
                            <center>
                                No items found
                            </center>
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
    <script>
        $(document).ready(function() {
            $(".filter-input").on("keyup change", function() {
                let data = {
                    barcode: $("input[name='barcode']").val(),
                    name: $("input[name='name']").val(),
                    price: $("input[name='price']").val(),
                    weight: $("input[name='weight']").val(),
                };

                $.ajax({
                    url: "{{ route('products.filter') }}",
                    type: "GET",
                    data: data,
                    success: function(response) {
                        $("#products-table-body").html(response);
                    }
                });
            });
        });
    </script>

</body>

</html>
