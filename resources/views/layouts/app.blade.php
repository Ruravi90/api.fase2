<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Fase2Spa') }}</title>
    
    <link href="{{ asset('img/favicon.png') }}" rel="icon" type="image/x-icon" />

    @include('layouts.styles')
    @yield('styles')
</head>
<body class="hold-transition skin-black sidebar-mini" ng-app="App" ng-controller="MainController" ng-cloak>
    <div class="wrapper">
    @guest
        @yield('content')
    @else
      <header class="main-header">
        <!-- Logo -->
        <a href="/" class="logo">
          <span class="logo-mini">
            <img width="55%" src="{{ asset('img/favicon.png') }}">
          </span>
          <!-- logo for regular state and mobile devices -->
          <span class="logo-lg">
            <img width="55%" src="{{ asset('img/favicon.png') }}">
          </span>
        </a>
        <!-- Header Navbar: style can be found in header.less -->
        <nav class="navbar navbar-fixed-top">
          <!-- Sidebar toggle button-->
          <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
            <span class="sr-only">Toggle navigation</span>
          </a>  
          <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
                @guest
                    <!--<li><a href="{{ route('login') }}">Login</a></li>
                    <li><a href="{{ route('register') }}">Register</a></li>-->
                @else
                    <li><a>{{ Auth::user()->name }}</a></li>
                    <li>
                        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('frm-logout').submit();">
                            Cerrar sesión
                        </a>  
                        <form id="frm-logout" action="{{ route('logout') }}" method="POST" style="display: none;">
                            {{ csrf_field() }}
                        </form>
                    </li>
                @endguest
            </ul>
          </div>
        </nav>
      </header>

      <!-- Left side column. contains the logo and sidebar -->
      <aside class="main-sidebar">
        <!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar">
          <!-- search form-->
          <form action="#" method="get" class="sidebar-form">
            <div class="input-group">
              <input type="text" name="q" class="form-control" placeholder="Búscar..." ng-model="anySearch">
              <span class="input-group-btn ng-cloak">
                  <a name="search" class="btn btn-flat" ng-click="anySearch = ''">
                    <i ng-if="anySearch == ''" class="fa fa-search"></i>
                    <i ng-if="anySearch != ''" class="fa fa-times"></i>
                  </a>
              </span>
            </div>
          </form>
          <!-- /.search form -->
          <!-- sidebar menu: : style can be found in sidebar.less -->
          <ul class="sidebar-menu" data-widget="tree">

            @can('module_schedule')
            <li><a href="{{ route('schedule_path') }}"><i class="fa fa-calendar"></i><span>Agenda</span></a></li>
            @endcan
            @can('module_client')
            <li><a href="{{ route('clients_path') }}"><i class="fa fa-group"></i><span>Clientes</span> </a></li>
            @endcan
            @can('module_sale')
            <li><a href="{{ route('sale_path') }}"><i class="fa fa-shopping-bag"></i><span>Ventas</span></a></li>
            @endcan
            @can('module_package')
            <li><a href="{{ route('package_path') }}"><i class="fa fa-cubes"></i><span>Paquetes</span></a></li>
            @endcan       
            
            @role('admin')
                <li><a href="{{ route('box_path') }}">
                  <i class="fa fa-balance-scale"></i><span>Balance</span></a>
                </li>
                <li class="treeview">
                  <a href="#">
                    <i class="fa fa-dashboard"></i> <span>Compras</span>
                    <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                  </a>
                  <ul class="treeview-menu"> 
                    <li>
                      <a href="{{ route('purchases_path') }}"><i class="fa fa-circle-o"></i><span>Generar compras</span></a>
                    </li>
                    <li>
                      <a href="{{ route('purchases_pending_path') }}"><i class="fa fa-circle-o"></i>Compras por pagar</a>
                    </li>
                  </ul>
                </li>
                <li class="treeview">
                  <a href="#">
                    <i class="fa fa-dashboard"></i> <span>Administración</span>
                    <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                  </a>
                  <ul class="treeview-menu">
                    <li><a href="{{ route('department_path') }}"><i class="fa fa-circle-o"></i>Departamentos</a></li>
                    <li><a href="{{ route('provider_path') }}"><i class="fa fa-circle-o"></i>Proveedores/Acreedores</a></li>
                    <li><a href="{{ route('products_inventory_path') }}"><i class="fa fa-circle-o"></i>Inventario de productos</a></li>
                    <li><a href="{{ route('pills_inventory_path') }}"><i class="fa fa-circle-o"></i>Inventario de pastillas</a></li>
                    <li><a href="{{ route('user_path') }}"><i class="fa fa-circle-o"></i>Usuarios</a></li>
                    <li><a href="{{ route('agent_path') }}"><i class="fa fa-circle-o"></i>Agentes</a></li>
                    @role('super_admin')
                    <li><a href="{{ route('rol_path') }}"><i class="fa fa-circle-o"></i>Roles y permisos</a></li>
                    @endrole
                  </ul>
                </li>
                <li class="treeview">
                    <a href="#">
                      <i class="fa fa-th-list"></i> <span>Catalogos</span>
                      <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                    </a>
                    <ul class="treeview-menu">
                      <li><a href="{{ route('cat_reference_path') }}"><i class="fa fa-circle-o"></i>Referencias</a></li>
                      <li><a href="{{ route('cat_package_path') }}"><i class="fa fa-circle-o"></i>Paquetes</a></li>
                      <li><a href="{{ route('cat_product_path') }}"><i class="fa fa-circle-o"></i>Productos</a><li>
                      <li><a href="{{ route('cat_service_path') }}"><i class="fa fa-circle-o"></i>Servicios</a><li>
                      <li><a href="{{ route('cat_pill_path') }}"><i class="fa fa-circle-o"></i>Pastillas</a><li>
                    </ul>
                </li>
            @endcan
          </ul>
        </section>
        <!-- /.sidebar -->
      </aside>

      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Main content -->
        <section class="content">
            @yield('content')
        </section>
        <!-- /.content -->
      </div>
      <!-- /.content-wrapper -->
      <footer class="main-footer">
        <div class="pull-right hidden-xs">
          <b>Version</b> 0.0.1 power by Ruravi.App
        </div>
        <strong>Copyright &copy; 2014-2016 <a href="#"></a>.</strong> All rights reserved.
      </footer>
    @endguest
    </div>
    <!-- ./wrapper -->
    @include('layouts.javascripts')
    @include('layouts.angular')
    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    @yield('scripts')
</body>
</html>
