<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ app()->getLocale() == 'ar' ? 'جاري معالجة طلبك' : 'Processing Your Order' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            font-family: 'Cairo', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #ad996f 0%, #878526 100%);
            margin: 0;
        }

        .container {
            text-align: center;
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            max-width: 400px;
        }

        .loader {
            border: 8px solid #f3f3f3;
            border-top: 8px solid #08203e;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            animation: spin 1.5s linear infinite;
            margin: 0 auto 20px;
        }

        p {
            color: #555;
            font-size: 1.1em;
            margin-top: 20px;
            font-weight: 500;
        }

        .subtitle {
            color: #999;
            font-size: 0.9em;
            margin-top: 10px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @media (max-width: 600px) {
            .container {
                padding: 30px;
                margin: 20px;
            }

            p {
                font-size: 1em;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="loader"></div>
        <p>{{ app()->getLocale() == 'ar' ? 'جاري إتمام طلبك' : 'Processing Your Order' }}</p>
        <p class="subtitle">{{ app()->getLocale() == 'ar' ? 'يرجى الانتظار...' : 'Please wait...' }}</p>
    </div>

    <form id="auto-submit-form" action="{{ route('cart.placeOrder') }}" method="POST" style="display: none;">
        @csrf
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkoutData = @json(session()->pull('checkout_form_data', []));
            const form = document.getElementById('auto-submit-form');

            if (Object.keys(checkoutData).length > 0) {
                for (const key in checkoutData) {
                    if (checkoutData.hasOwnProperty(key)) {
                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = key;

                        if (typeof checkoutData[key] === 'object') {
                            hiddenInput.value = JSON.stringify(checkoutData[key]);
                        } else {
                            hiddenInput.value = checkoutData[key];
                        }

                        form.appendChild(hiddenInput);
                    }
                }

                setTimeout(function() {
                    form.submit();
                }, 1000);
            } else {
                setTimeout(function() {
                    window.location.href = "{{ route('home') }}";
                }, 3000);
            }
        });
    </script>
</body>
</html>
