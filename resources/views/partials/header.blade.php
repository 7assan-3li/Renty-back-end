<header>
    <div style="display:flex; align-items:center; gap:15px;">

        <!-- Language Switcher -->
        @if(app()->getLocale() == 'ar')
            <a href="{{ route('lang.switch', 'en') }}" class="btn btn-outline"
                style="padding: 5px 10px; font-weight:bold;">ENG</a>
        @else
            <a href="{{ route('lang.switch', 'ar') }}" class="btn btn-outline"
                style="padding: 5px 10px; font-weight:bold;">عربي</a>
        @endif

        <!-- Theme Toggle -->
        <div style="cursor:pointer; width:35px; height:35px; display:flex; align-items:center; justify-content:center; background:var(--input-bg); border-radius:50%;"
            onclick="toggleTheme()">
            <i class="fa-solid fa-moon" id="theme-icon" style="color:var(--text-color);"></i>
        </div>

        <!-- User Profile -->
        <div style="cursor:pointer; display:flex; align-items:center; gap:10px;" onclick="openUserProfile()">
            @php
                $avatarUrls = auth()->user()->avatar_urls;
                $thumbnail = $avatarUrls['thumbnail'] ?? 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=008b96&color=fff';
            @endphp
            <img src="{{ $thumbnail }}"
                style="width:40px; height:40px; border-radius:50%; box-shadow: 0 2px 5px rgba(0,0,0,0.1); object-fit: cover;">
            <div style="display:flex; flex-direction:column; line-height:1.2;">
                <span
                    style="font-size:14px; font-weight:bold; color:var(--text-color);">{{ auth()->user()->name }}</span>
                <span style="font-size:11px; color:var(--primary-color);">{{ ucfirst(auth()->user()->role) }}</span>
            </div>
        </div>

        <!-- Notification -->
        {{-- <div style="position:relative; cursor:pointer">
            <i class="fa-regular fa-bell" style="font-size:20px; color:var(--text-color);"></i>
            <span
                style="position:absolute; top:-5px; right:-5px; background:red; width:8px; height:8px; border-radius:50%;"></span>
        </div> --}}


    </div>
</header>