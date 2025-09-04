<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'لوحة التحكم') - SOFA Experience</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Droid+Arabic+Sans:wght@400;700&display=swap" rel="stylesheet">

    <!-- Bootstrap RTL -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

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
            background-color: var(--light-bg);
            color: var(--primary-dark);
        }

        .sidebar {
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary-light) 100%);
            min-height: 100vh;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }

        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            margin: 5px 15px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background-color: rgba(255,255,255,0.1);
            color: white;
            transform: translateX(-5px);
        }

        .main-content {
            background-color: #ffffff;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            margin: 20px;
            padding: 30px;
        }
        .border-bottom {
        border-bottom: 2px solid var(--secondary) !important;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary-light) 100%);
        border: none;
        border-radius: 8px;
        padding: 10px 25px;
        transition: all 0.3s ease;
    }


        .stats-card {
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary-light) 100%);
            border-radius: 15px;
            padding: 25px;
            color: white;
            margin-bottom: 20px;
            transition: transform 0.3s ease;
        }

        .stats-card:hover {
            transform: translateY(-5px);
        }

        .stats-card .icon {
            font-size: 2.5rem;
            opacity: 0.8;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary-light) 100%);
            border: none;
            border-radius: 8px;
            padding: 10px 25px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--primary-light) 0%, var(--primary-dark) 100%);
            transform: translateY(-2px);
        }

        .btn-secondary {
            background-color: var(--secondary);
            border: none;
            border-radius: 8px;
            padding: 10px 25px;
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            background-color: #9c855c;
            transform: translateY(-2px);
        }

        .table {
            border-radius: 10px;
            overflow: hidden;
        }

        .table thead th {
            background-color: var(--primary-dark);
            color: white;
            border: none;
            font-weight: 600;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(173, 153, 111, 0.1);
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--primary-dark);
        }

        h1, h2, h3, h4, h5, h6 {
            color: var(--primary-dark);
            font-weight: 700;
        }

        .text-primary {
            color: var(--primary-dark) !important;
        }

        .text-secondary {
            color: var(--secondary) !important;
        }

        .bg-primary {
            background-color: var(--primary-dark) !important;
        }

        .bg-secondary {
            background-color: var(--secondary) !important;
        }

        .border-primary {
            border-color: var(--primary-dark) !important;
        }

        .border-secondary {
            border-color: var(--secondary) !important;
        }

        .alert-primary {
            background-color: rgba(8, 32, 62, 0.1);
            border-color: var(--primary-dark);
            color: var(--primary-dark);
        }

        /* تخصيص عناصر النموذج */
        .form-control:focus {
            border-color: var(--secondary);
            box-shadow: 0 0 0 0.25rem rgba(173, 153, 111, 0.25);
        }

        /* تخصيص الـ pagination */
        .page-item.active .page-link {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
        }

        .page-link {
            color: var(--primary-dark);
        }

        .page-link:hover {
            color: var(--primary-light);
        }


    .btn-primary:hover {
        background: linear-gradient(135deg, var(--primary-light) 0%, var(--primary-dark) 100%);
        transform: translateY(-2px);
    }

    .form-control:focus {
        border-color: var(--secondary);
        box-shadow: 0 0 0 0.25rem rgba(173, 153, 111, 0.25);
    }

    .card-header {
        font-weight: 600;
    }
    .btn-outline-primary:hover {
        background-color: var(--primary-dark);
        border-color: var(--primary-dark);
        color: white;
    }

    .btn-outline-danger {
        color: #dc3545;
        border-color: #dc3545;
    }

    .btn-outline-danger:hover {
        background-color: #dc3545;
        border-color: #dc3545;
        color: white;
    }

    .table-dark {
        background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary-light) 100%) !important;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(173, 153, 111, 0.1);
    }

    /*.pagination .page-item.active .page-link {
        background-color: var(--primary-dark);
        border-color: var(--primary-dark);
    }*/

    .pagination .page-link {
        color: var(--primary-dark);
    }

    .pagination .page-link:hover {
        color: var(--primary-light);
    }

    .form-control:focus,
    .form-select:focus {
        border-color: var(--secondary);
        box-shadow: 0 0 0 0.25rem rgba(173, 153, 111, 0.25);
    }

    .form-label {
        font-weight: 600;
        color: var(--primary-dark);
        margin-bottom: 8px;
    }

    .form-text {
        font-size: 0.85rem;
        color: #6c757d;
    }

    .text-danger {
        color: #dc3545 !important;
    }
    .btn-primary:hover {
        background: linear-gradient(135deg, var(--primary-light) 0%, var(--primary-dark) 100%);
        transform: translateY(-2px);
    }

    .btn-outline-primary {
        color: var(--primary-dark);
        border-color: var(--primary-dark);
    }

    .btn-outline-primary:hover {
        background-color: var(--primary-dark);
        border-color: var(--primary-dark);
        color: white;
    }

    .btn-outline-danger {
        color: #dc3545;
        border-color: #dc3545;
    }

    .btn-outline-danger:hover {
        background-color: #dc3545;
        border-color: #dc3545;
        color: white;
    }

    .pagination .page-item.active .page-link {
    background-color: #eaecef;
    border-color: #d8dbde;
}
.form-check-input:checked {
        background-color: var(--primary-dark);
        border-color: var(--primary-dark);
    }
#testimonialsTable_filter {
    text-align: left !important; /* يحرك البحث لليسار */
}
#faqsTable_filter {
    text-align: left !important; /* يحرك البحث لليسار */
}

#heroSlidersTable_filter {
    text-align: left !important; /* يحرك البحث لليسار */
}

    </style>

    @stack('styles')
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="position-sticky pt-3">
                    <div class="text-center mb-4">
                        <img src="{{ asset('assets/images/logos/logo-white.svg') }}" alt="SOFA Experience" class="mb-2" style="max-width: 180px;">
                    </div>


                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                               href="{{ route('admin.dashboard') }}">
                                <i class="fas fa-tachometer-alt me-2"></i>
                                الرئيسية
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.hero-sliders.*') ? 'active' : '' }}"
                               href="{{ route('admin.hero-sliders.index') }}">
                                <i class="fas fa-image me-2"></i>
                                السلايدر
                            </a>
                        </li>


                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}"
                               href="{{ route('admin.categories.index') }}">
                                <i class="fas fa-tags me-2"></i>
                                التصنيفات
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}"
                               href="{{ route('admin.products.index') }}">
                                <i class="fas fa-couch me-2"></i>
                                المنتجات
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}"
                               href="{{ route('admin.orders.index') }}">
                                <i class="fas fa-shopping-cart me-2"></i>
                                الطلبات
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
                               href="{{ route('admin.users.index') }}">
                                <i class="fas fa-users me-2"></i>
                                المستخدمين
                            </a>
                        </li>


                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.testimonials.*') ? 'active' : '' }}"
                               href="{{ route('admin.testimonials.index') }}">
                                <i class="fas fa-comment-dots me-2"></i>
                                آراء العملاء
                            </a>
                        </li>


                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.faqs.*') ? 'active' : '' }}"
                            href="{{ route('admin.faqs.index') }}">
                                <i class="fas fa-comments me-2"></i>
                                الأسئلة المتكررة
                            </a>
                        </li>


                        <hr>

                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}"
                               href="{{ route('admin.settings.edit') }}">
                                <i class="fas fa-cogs me-2"></i>
                                إعدادات الموقع
                            </a>
                        </li>


                        <li class="nav-item">
                            <form method="POST" action="{{ route('admin.logout') }}">
                                @csrf
                                <a class="nav-link" href="#"
                                   onclick="event.preventDefault(); this.closest('form').submit();">
                                    <i class="fas fa-sign-out-alt me-2"></i>
                                    تسجيل الخروج
                                </a>
                            </form>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="main-content">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


    @stack('scripts')
</body>
</html>
