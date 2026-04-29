<!-- User Profile Modal -->
<div class="modal-overlay" id="userProfileModal">
    <div class="modal" style="padding:0; overflow:hidden; width:400px;">
        <div class="profile-header-bg">
            <div style="position:relative; display:inline-block;">
                @php
                    $avatarUrls = auth()->user()->avatar_urls;
                    $thumbnail = $avatarUrls['thumbnail'] ?? 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=008b96&color=fff';
                @endphp
                <img src="{{ $thumbnail }}"
                    class="profile-avatar" style="object-fit: cover;">
                <div class="profile-status"></div>
            </div>
            <h3 style="margin-top:10px;" id="admin-name-display">{{ auth()->user()->name }}</h3>
            <span style="font-size:13px; opacity:0.8;"
                id="admin-role-display">{{ ucfirst(auth()->user()->role) }}</span>
        </div>
        <div class="profile-actions">
            <div class="action-item" onclick="openEditAdminProfile()">
                <div style="display:flex; align-items:center; gap:10px;">
                    <div class="action-icon"><i class="fa-solid fa-user-gear"></i></div>
                    <span data-i18n="editProfile">{{ __('Edit Profile') }}</span>
                </div>
                <i class="fa-solid fa-chevron-left" style="font-size:12px; color:#ccc"></i>
            </div>
            <div class="action-item" onclick="openChangePassword()">
                <div style="display:flex; align-items:center; gap:10px;">
                    <div class="action-icon"><i class="fa-solid fa-lock"></i></div>
                    <span data-i18n="changePassword">{{ __('Change Password') }}</span>
                </div>
                <i class="fa-solid fa-chevron-left" style="font-size:12px; color:#ccc"></i>
            </div>
            <div class="action-item" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                style="color:var(--danger)">
                <div style="display:flex; align-items:center; gap:10px;">
                    <div class="action-icon" style="background:#ffebee; color:var(--danger)"><i
                            class="fa-solid fa-arrow-right-from-bracket"></i></div>
                    <span data-i18n="logout">{{ __('Logout') }}</span>
                </div>
            </div>
            <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </div>
        <div class="modal-footer" style="padding:10px; background:#f9f9f9; border-top:1px solid #eee;">
            <button class="btn btn-outline" style="width:100%; justify-content:center;"
                onclick="closeModal('userProfileModal')" data-i18n="close">{{ __('Close') }}</button>
        </div>
    </div>
</div>

<!-- Edit Admin Profile Modal -->
<div class="modal-overlay" id="editAdminProfileModal">
    <div class="modal">
        <div class="modal-header">
            <h3 data-i18n="editProfile">{{ __('Edit Profile') }}</h3><i class="fa-solid fa-times close-modal"
                onclick="closeModal('editAdminProfileModal')"></i>
        </div>
        <form id="editProfileForm" enctype="multipart/form-data">
            @csrf
            <div class="form-group"><label data-i18n="name">{{ __('Name') }}</label><input type="text" name="name"
                    id="admin-name-input" value="{{ auth()->user()->name }}"></div>
            <div class="form-group"><label data-i18n="email">{{ __('Email') }}</label><input type="email" name="email"
                    id="admin-email-input" value="{{ auth()->user()->email }}"></div>
            <div class="form-group"><label data-i18n="phone">{{ __('Phone') }}</label><input type="text" name="phone"
                    id="admin-phone-input" value="{{ auth()->user()->phone }}"></div>
            <div class="form-group"><label data-i18n="image">{{ __('Profile Image') }}</label><input type="file"
                    name="image" id="admin-image-input"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('editAdminProfileModal')"
                    data-i18n="cancel">{{ __('Cancel') }}</button>
                <button type="submit" class="btn" data-i18n="save">{{ __('Save') }}</button>
            </div>
        </form>
    </div>
</div>

<!-- Change Password Modal -->
<div class="modal-overlay" id="changePasswordModal">
    <div class="modal">
        <div class="modal-header">
            <h3 data-i18n="changePassword">{{ __('Change Password') }}</h3><i class="fa-solid fa-times close-modal"
                onclick="closeModal('changePasswordModal')"></i>
        </div>
        <form id="changePasswordForm">
            @csrf
            <div class="form-group"><label data-i18n="currentPassword">{{ __('Current Password') }}</label><input
                    type="password" name="current_password" required></div>
            <div class="form-group"><label data-i18n="newPassword">{{ __('New Password') }}</label><input
                    type="password" name="password" required></div>
            <div class="form-group"><label data-i18n="confirmPassword">{{ __('Confirm Password') }}</label><input
                    type="password" name="password_confirmation" required></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('changePasswordModal')"
                    data-i18n="cancel">{{ __('Cancel') }}</button>
                <button type="submit" class="btn" data-i18n="save">{{ __('Save') }}</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openUserProfile() {
        openModal('userProfileModal');
    }

    function openEditAdminProfile() {
        closeModal('userProfileModal');
        openModal('editAdminProfileModal');
    }

    function openChangePassword() {
        closeModal('userProfileModal');
        openModal('changePasswordModal');
    }

    // Handle Edit Profile Submission
    document.getElementById('editProfileForm').addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);

        fetch("{{ route('admin.profile.update') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload(); // Reload to update info
                } else {
                    alert('Error updating profile');
                    console.error(data);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred');
            });
    });

    // Handle Change Password Submission
    document.getElementById('changePasswordForm').addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);

        fetch("{{ route('admin.password.update') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    closeModal('changePasswordModal');
                } else {
                    alert('Error changing password');
                    console.error(data);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred');
            });
    });
</script>