<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>@yield('title', 'SmartWard')</title>
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}">
    <!-- Google Font: Source Sans Pro -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    @yield('css')
    <style>
        body {
            background-color: #f4f6f9;
            padding: 0;
            margin: 0;
        }
        .content-wrapper {
            margin-left: 0 !important;
            background-color: #f4f6f9;
        }
        .content-header {
            padding: 15px;
        }
    </style>
</head>
<body class="hold-transition iframe-mode">
    <div class="wrapper">
        <div class="content-wrapper">
            @yield('content')
        </div>
    </div>

    <!-- REQUIRED SCRIPTS -->
    <!-- jQuery -->
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <!-- Bootstrap 4 -->
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- AdminLTE iframe error prevention - must load before AdminLTE -->
    <script>
        // Prevent AdminLTE iframe errors in iframe context
        (function() {
            'use strict';
            console.log('Loading iframe-specific AdminLTE protection...');
            
            // Create AdminLTE object if it doesn't exist
            if (typeof window.AdminLTE === 'undefined') {
                window.AdminLTE = {};
            }
            
            // Override IFrame functionality completely for iframe context
            window.AdminLTE.IFrame = {
                _config: { autoIframeMode: false },
                _element: null,
                _init: function() { console.log('IFrame._init disabled in iframe'); return this; },
                _initFrameElement: function() { console.log('IFrame._initFrameElement disabled in iframe'); return this; },
                _jQueryInterface: function() { console.log('IFrame._jQueryInterface disabled in iframe'); return this; },
                autoIframeMode: false
            };
            
            // Disable PluginManager to prevent auto-initialization
            window.AdminLTE.PluginManager = {
                autoLoad: false,
                register: function() { return this; }
            };
            
            // Global error handler for iframe-specific errors
            window.addEventListener('error', function(e) {
                if (e.message && (
                    e.message.includes('autoIframeMode') || 
                    e.message.includes('_initFrameElement') ||
                    e.message.includes('IFrame')
                )) {
                    console.log('Blocked AdminLTE iframe error in iframe context:', e.message);
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    return false;
                }
            }, true);
            
            console.log('Iframe-specific AdminLTE protection loaded successfully');
        })();
    </script>
    
    <!-- AdminLTE App -->
    <script src="{{ asset('vendor/adminlte/dist/js/adminlte.min.js') }}"></script>
    
    <!-- Post-AdminLTE iframe protection -->
    <script>
        $(document).ready(function() {
            // Additional protection after AdminLTE loads
            console.log('Applying post-load iframe protection...');
            
            // Override jQuery IFrame plugin
            if ($.fn.IFrame) {
                $.fn.IFrame = function() { 
                    console.log('jQuery IFrame plugin disabled in iframe context');
                    return this; 
                };
            }
            
            // Remove iframe-related elements
            $('[data-widget="iframe"]').remove();
            
            console.log('Post-load iframe protection applied');
        });
    </script>
    
    @yield('js')
</body>
</html> 