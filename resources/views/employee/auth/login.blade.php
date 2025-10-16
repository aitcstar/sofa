<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>تسجيل الدخول للموظف - لوحة التحكم</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Droid+Arabic+Sans:wght@400;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-dark: #08203e;
            --primary-light: #33415c;
            --secondary: #ad996f;
            --accent: #979dac;
            --light-bg: #f8f9fa;
        }

        body {
            font-family: 'Cairo', 'Droid Arabic Sans', sans-serif;
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary-light) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .login-header {
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary-light) 100%);
            color: white;
            padding: 40px;
            text-align: center;
        }
        .login-body {
            padding: 40px;
        }
        .form-control {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: var(--secondary);
            box-shadow: 0 0 0 0.2rem rgba(173, 153, 111, 0.25);
        }
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary-light) 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, var(--primary-light) 0%, var(--primary-dark) 100%);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(8, 32, 62, 0.4);
        }
        .form-check-input:checked {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
        }
        .alert-danger {
            background-color: rgba(220, 53, 69, 0.1);
            border-color: #dc3545;
            color: #dc3545;
        }
        a {
            color: var(--primary-dark);
            text-decoration: none;
            transition: all 0.3s ease;
        }
        a:hover {
            color: var(--secondary);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="login-card">
                    <div class="login-header">
                        <h2 class="mb-0"><i class="fas fa-cogs me-2"></i> تسجيل الدخول</h2>
                        <p class="mb-0 mt-2">لوحة تحكم للموظف </p>
                    </div>
                    <div class="login-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('employee.login') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="email" class="form-label">البريد الإلكتروني</label>
                                <input type="email" id="email" name="email" value="{{ old('email') }}" required
                                       class="form-control @error('email') is-invalid @enderror">
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">كلمة المرور</label>
                                <input type="password" id="password" name="password" required
                                       class="form-control @error('password') is-invalid @enderror">
                                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" id="remember" name="remember" class="form-check-input">
                                <label class="form-check-label" for="remember">تذكرني</label>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-sign-in-alt me-2"></i> دخول
                                </button>
                            </div>
                        </form>
                        <div class="text-center mt-3">
                            <a href="/" class="text-muted"><i class="fas fa-arrow-right me-1"></i> العودة للرئيسية</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
