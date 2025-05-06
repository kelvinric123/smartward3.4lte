@extends('adminlte::master')

@inject('layoutHelper', 'JeroenNoten\LaravelAdminLte\Helpers\LayoutHelper')
@inject('preloaderHelper', 'JeroenNoten\LaravelAdminLte\Helpers\PreloaderHelper')

@section('adminlte_css')
    <link rel="stylesheet" href="{{ asset('css/admin_custom.css') }}">
    @stack('css')
    @yield('css')
@stop

@section('classes_body', $layoutHelper->makeBodyClasses())

@section('body_data', $layoutHelper->makeBodyData())

@section('body')
    <div class="wrapper">

        {{-- Preloader Animation (fullscreen mode) --}}
        @if($preloaderHelper->isPreloaderEnabled())
            @include('adminlte::partials.common.preloader')
        @endif

        {{-- Top Navbar --}}
        @if($layoutHelper->isLayoutTopnavEnabled())
            @include('adminlte::partials.navbar.navbar-layout-topnav')
        @else
            @include('adminlte::partials.navbar.navbar')
        @endif

        {{-- Left Main Sidebar --}}
        @if(!$layoutHelper->isLayoutTopnavEnabled())
            @include('adminlte::partials.sidebar.left-sidebar')
        @endif

        {{-- Content Wrapper --}}
        @empty($iFrameEnabled)
            @include('adminlte::partials.cwrapper.cwrapper-default')
        @else
            @include('adminlte::partials.cwrapper.cwrapper-iframe')
        @endempty

        {{-- Footer --}}
        @hasSection('footer')
            @include('adminlte::partials.footer.footer')
        @endif

        {{-- Right Control Sidebar --}}
        @if($layoutHelper->isRightSidebarEnabled())
            @include('adminlte::partials.sidebar.right-sidebar')
        @endif

    </div>
@stop

@section('adminlte_js')
    <script>
        // Remove any left arrow elements that might be present
        document.addEventListener('DOMContentLoaded', function() {
            // Look for elements that might be the left arrow and remove them
            const possibleArrows = document.querySelectorAll('.arrow-left, [class*="arrow"], .wrapper > div:not(.main-sidebar):not(.content-wrapper):not(.control-sidebar)');
            
            possibleArrows.forEach(function(el) {
                // Check if it looks like an arrow (positioned left, has no content or just an icon)
                const style = window.getComputedStyle(el);
                const isPositionedLeft = (style.position === 'absolute' || style.position === 'fixed') && 
                                         (parseInt(style.left) < 50 || style.left.includes('%'));
                
                if (isPositionedLeft || el.classList.contains('arrow-left') || 
                   (el.tagName.toLowerCase() === 'svg' && el.classList.contains('arrow'))) {
                    el.remove();
                }
            });
        });
    </script>
    @stack('js')
    @yield('js')
@stop
