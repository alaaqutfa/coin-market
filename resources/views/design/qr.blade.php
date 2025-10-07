<!DOCTYPE html>
<html lang="ar">

<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            font-family: Arial, Helvetica, sans-serif;
        }

        :root {
            --primary: #ECC631;
            --secondary: #333127;
            --text: #222222;
            --bg: #f0f0f0;
        }

        body {
            width: 100%;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 1.5rem;
            border: 5px solid var(--secondary);
        }

        .design-container {
            /* width: 302px;
            height: 208px; */
            width: 100%;
            height: 100%;
            background: white;
            border: 5px solid var(--primary);
            border-radius: 15px;
            display: flex;
            justify-content: space-evenly;
            align-items: center;
            gap: 20px;
        }

        .info-side {
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            gap: 10px;
            padding: 10px;
            text-align: center;
        }

        .info-side img {
            max-width: 250px;
            height: 100px;
            height: auto;
            object-fit: contain
        }

        .info-side h2 {
            font-size: 5rem;
            text-align: center;
            color: black;
            font-weight: 900;
        }
    </style>
</head>

<body>
    <div class="design-container">

        <div class="info-side">

            <img src="{{ asset('assets/img/logo-light.png') }}" alt="coin-market-logo" />

            <h2>
                {{ $employee['name'] }}
            </h2>

        </div>

        <div class="qr-side">
            {!! SimpleSoftwareIO\QrCode\Facades\QrCode::size(400)->generate($employee['employee_code']) !!}
        </div>

        <!-- يمكنك حفظ الكود كصورة إذا أردت -->
        {{-- {{ SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')->size(300)->generate($data, public_path('qr.png')) }} --}}

    </div>

    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>\
</body>

</html>
