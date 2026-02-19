<div class="sidebar">
        <div class="brand"><i class="fa-solid fa-car-side"></i><span>Renty Admin</span></div>

        <div class="menu-label">{{ __('dashboard') }}</div>
        <a href="{{ route('admin.dashboard') }}"
                class="menu-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-pie"></i><span>{{ __('dashboard') }}</span>
        </a>
        <a href="{{ route('admin.cars.index') }}"
                class="menu-item {{ request()->routeIs('admin.cars.*') ? 'active' : '' }}">
                <i class="fa-solid fa-car"></i><span>{{ __('vehicles') }}</span>
        </a>
        <a href="{{ route('admin.categories.index') }}"
                class="menu-item {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                <i class="fa-solid fa-layer-group"></i><span>{{ __('categories') }}</span>
        </a>
        <a href="{{ route('admin.bookings.index') }}"
                class="menu-item {{ request()->routeIs('admin.bookings.*') ? 'active' : '' }}">
                <i class="fa-solid fa-calendar-check"></i><span>{{ __('bookings') }}</span>
        </a>
        <a href="{{ route('admin.users.index') }}"
                class="menu-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <i class="fa-solid fa-users"></i><span>{{ __('users') }}</span>
        </a>

        <div class="menu-label">{{ __('finance') }}</div>
        <a href="{{ route('admin.finance.index') }}"
                class="menu-item {{ request()->routeIs('admin.finance.*') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-line"></i><span>{{ __('financeOverview') }}</span>
        </a>
        {{-- <a href="{{ route('admin.accounts') }}"
                class="menu-item {{ request()->routeIs('admin.accounts') ? 'active' : '' }}">
                <i class="fa-solid fa-building-columns"></i><span>{{ __('accounts') }}</span>
        </a>
        <a href="{{ route('admin.credit_cards') }}"
                class="menu-item {{ request()->routeIs('admin.credit_cards') ? 'active' : '' }}">
                <i class="fa-solid fa-credit-card"></i><span>{{ __('creditCards') }}</span>
        </a>

        <div class="menu-label">{{ __('services') }}</div>
        <a href="{{ route('admin.services') }}"
                class="menu-item {{ request()->routeIs('admin.services') ? 'active' : '' }}">
                <i class="fa-solid fa-concierge-bell"></i><span>{{ __('services') }}</span>
        </a> --}}

        <!-- Logout -->
        <div style="margin-top: auto; padding-top: 20px; border-top: 1px solid rgba(255,255,255,0.1);">
                <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
                        @csrf
                </form>
                <a href="#" class="menu-item"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                        style="color: #ff6b6b;">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i>
                        <span>{{ __('logout') }}</span>
                </a>
        </div>
</div>