@inject('preloaderHelper', 'JeroenNoten\LaravelAdminLte\Helpers\PreloaderHelper')

<div class="{{ $preloaderHelper->makePreloaderClasses() }}" style="{{ $preloaderHelper->makePreloaderStyle() }}">

    @hasSection('preloader')

        {{-- Use a custom preloader content --}}
        @yield('preloader')

    @else

        {{-- Use the default preloader content --}}
        @php
            $logoPath = config('adminlte.preloader.img.path', 'vendor/adminlte/dist/img/AdminLTELogo.png');
            $isExternalUrl = strpos($logoPath, 'http') === 0;
        @endphp
        <img src="{{ $isExternalUrl ? $logoPath : asset($logoPath) }}"
             class="img-circle {{ config('adminlte.preloader.img.effect', 'animation__shake') }}"
             alt="{{ config('adminlte.preloader.img.alt', 'AdminLTE Preloader Image') }}"
             width="{{ config('adminlte.preloader.img.width', 60) }}"
             height="{{ config('adminlte.preloader.img.height', 60) }}"
             style="animation-iteration-count:infinite;">

    @endif

</div>
