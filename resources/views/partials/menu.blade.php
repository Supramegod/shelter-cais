@foreach($menus as $menu)
    <li class="menu-item {{ request()->is(ltrim($menu['url'], '/')) ? 'active' : '' }}">
        <a href="{{ config('app.url') . ($menu['url'] ?? '') }}"
           class="menu-link {{ count($menu['children']) ? 'menu-toggle' : '' }}">
            @if(!empty($menu['icon']))
            <i class="menu-icon tf-icons {{ $menu['icon'] }}"></i>
            @endif
            <div>{{ $menu['nama'] }}</div>
        </a>
        @if(count($menu['children']))
            <ul class="menu-sub">
                @include('partials.menu', ['menus' => $menu['children']])
            </ul>
        @endif
    </li>
@endforeach
