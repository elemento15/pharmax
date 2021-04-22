<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" ng-app="mainApp">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    @if (!Auth::user())
        <!-- Scripts -->
        <script src="{{ asset('js/app.js') }}" defer></script>

        <!-- Fonts -->
        <link rel="dns-prefetch" href="//fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

        <!-- Styles -->
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @else
        <link rel="stylesheet" type="text/css" href="/css/bootstrap.min.css" />
        <link rel="stylesheet" type="text/css" href="/css/angular-toastr.min.css" />
        <link rel="stylesheet" type="text/css" href="/css/angular-confirm.min.css" />
        <link rel="stylesheet" type="text/css" href="/css/ui-bootstrap-2.5.0-csp.css" />
        <link rel="stylesheet" type="text/css" href="/css/select.min.css" />
        <link rel="stylesheet" type="text/css" href="/css/styles.css" />

        <script src="/libs/jquery.js"></script>
        <script src="/libs/bootstrap.min.js"></script>
        <script src="/libs/angular.min.js"></script>
        <script src="/libs/angular-route.min.js"></script>
        <!-- https://github.com/Foxandxss/angular-toastr -->
        <script src="/libs/angular-toastr.tpls.min.js"></script>
        <!-- https://craftpip.github.io/angular-confirm/ -->
        <script src="/libs/angular-confirm.min.js"></script>
        <!-- https://github.com/angular-ui/ui-select/wiki -->
        <script src="/libs/select.min.js"></script>
        <!-- -->
        <!--<script src="/libs/ui-bootstrap-2.5.0.min.js"></script>-->
        <script src="/libs/ui-bootstrap-tpls-2.5.0.min.js"></script>
    @endif
</head>
<body>
    @if (!Auth::user())
        <div id="app">
            <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
                <div class="container">
                    <a class="navbar-brand" href="{{ url('/') }}">
                        {{ config('app.name', 'Laravel') }}
                    </a>
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <!-- Left Side Of Navbar -->
                        <ul class="navbar-nav mr-auto">

                        </ul>

                        <!-- Right Side Of Navbar -->
                        <ul class="navbar-nav ml-auto">
                            <!-- Authentication Links -->
                            @guest
                                @if (Route::has('login'))
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                    </li>
                                @endif
                                
                                @if (Route::has('register'))
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                    </li>
                                @endif
                            @else
                                <li class="nav-item dropdown">
                                    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                        {{ Auth::user()->name }}
                                    </a>

                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                        <a class="dropdown-item" href="{{ route('logout') }}"
                                           onclick="event.preventDefault();
                                                         document.getElementById('logout-form').submit();">
                                            {{ __('Logout') }}
                                        </a>

                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                            @csrf
                                        </form>
                                    </div>
                                </li>
                            @endguest
                        </ul>
                    </div>
                </div>
            </nav>

            <main class="py-4">
                @yield('content')
            </main>
        </div>
    @else
        @yield('content')

        <!-- general scripts -->
        <script src="/app/app.js"></script>
        <script src="/app/ajax.js"></script>

        <!-- components -->
        <script src="/app/components/modal_products_search.js"></script>
        <script src="/app/components/modal_change_status.js"></script>
        <script src="/app/components/modal_payment.js"></script>
        
        <!-- controllers -->
        <script src="/app/controllers/home_controller.js"></script>
        <script src="/app/controllers/base_controller.js"></script>
        <script src="/app/controllers/customers_controller.js"></script>
        <script src="/app/controllers/vendors_controller.js"></script>
        <script src="/app/controllers/vendors_prices_controller.js"></script>
        <script src="/app/controllers/products_controller.js"></script>
        <script src="/app/controllers/products_history_controller.js"></script>
        <script src="/app/controllers/products_compare_controller.js"></script>
        <script src="/app/controllers/purchase_orders_controller.js"></script>
        <script src="/app/controllers/cotizations_controller.js"></script>
    @endif
</body>
</html>
