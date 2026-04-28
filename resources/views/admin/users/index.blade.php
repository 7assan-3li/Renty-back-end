@extends('layouts.admin')

@section('content')
    <section id="users" class="content-section active">
        @if(session('success'))
            <div class="alert alert-success"
                style="padding: 15px; background: #d4edda; color: #155724; border-radius: 8px; margin-bottom: 20px;">
                {{ session('success') }}
            </div>
        @endif

        <div class="section-header">
            <h2 data-i18n="userDatabase">{{ __('userDatabase') }}</h2>
        </div>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th data-i18n="name">{{ __('name') }}</th>
                        <th data-i18n="email">{{ __('email') }}</th>
                        <th data-i18n="phone">{{ __('phone') }}</th>
                        <th data-i18n="balance">{{ __('balance') }}</th>
                        <th data-i18n="tripsCount">{{ __('tripsCount') }}</th>
                        <th data-i18n="status">{{ __('status') }}</th>
                        <th data-i18n="edit">{{ __('edit') }}</th>
                    </tr>
                </thead>
                <tbody id="users-list">
                    @forelse($users as $user)
                        <tr>
                            <td style="font-weight:bold">
                                <div style="display:flex; align-items:center; gap:10px;">
                                    @php
                                        $avatarUrls = $user->avatar_urls;
                                        $thumbnail = $avatarUrls['thumbnail'] ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=random';
                                    @endphp
                                    <img src="{{ $thumbnail }}"
                                        style="width:30px; height:30px; border-radius:50%; object-fit:cover;">
                                    {{ $user->name }}
                                </div>
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->phone }}</td>
                            <td style="color:var(--primary-color)">${{ number_format($user->balance, 2) }}</td>
                            <td>{{ $user->bookings->count() }}</td>
                            <td>
                                @php
                                    $statusClass = 'bg-green';
                                    $statusKey = 'active';
                                    if ($user->status == 'inactive') {
                                        $statusClass = 'bg-red';
                                        $statusKey = 'inactive';
                                    }
                                    if ($user->status == 'vip') {
                                        $statusClass = 'bg-purple';
                                        $statusKey = 'vip';
                                    }
                                @endphp
                                <span class="status {{ $statusClass }}">{{ __($statusKey) }}</span>
                            </td>
                            <td>
                                <button class="btn btn-outline"
                                    onclick="openEditUser({{ $user->id }}, '{{ $user->name }}', '{{ $user->email }}', '{{ $user->phone }}', '{{ $user->balance }}', '{{ $user->status }}')"
                                    style="padding:5px 10px">
                                    <i class="fa-solid fa-user-pen"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align:center">{{ __('noUsersFound') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div style="padding: 20px;">
                {{ $users->links() }}
            </div>
        </div>
    </section>

    <!-- Edit User Modal -->
    <div class="modal-overlay" id="editUserModal">
        <div class="modal">
            <div class="modal-header">
                <h3 data-i18n="editUser">{{ __('editUser') }}</h3>
                <i class="fa-solid fa-times close-modal" onclick="closeEditUserModal()"></i>
            </div>
            <form id="editUserForm" method="POST" action="">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label data-i18n="name">{{ __('name') }}</label>
                    <input type="text" name="name" id="edit-username" required>
                </div>
                <div class="form-group">
                    <label data-i18n="email">{{ __('email') }}</label>
                    <input type="email" name="email" id="edit-email" required>
                </div>
                <div class="form-group">
                    <label data-i18n="phone">{{ __('phone') }}</label>
                    <input type="text" name="phone" id="edit-phone" required>
                </div>
                <div class="form-group">
                    <label data-i18n="balance">{{ __('balance') }}</label>
                    <input type="number" step="0.01" name="balance" id="edit-balance" required>
                </div>
                <div class="form-group">
                    <label data-i18n="status">{{ __('status') }}</label>
                    <select name="status" id="edit-status">
                        <option value="active">{{ __('active') }}</option>
                        <option value="inactive">{{ __('inactive') }}</option>
                        <option value="vip">{{ __('vip') }}</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" onclick="closeEditUserModal()"
                        data-i18n="cancel">{{ __('cancel') }}</button>
                    <button type="submit" class="btn" data-i18n="save">{{ __('save') }}</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEditUser(id, name, email, phone, balance, status) {
            document.getElementById('edit-username').value = name;
            document.getElementById('edit-email').value = email;
            document.getElementById('edit-phone').value = phone;
            document.getElementById('edit-balance').value = balance;
            document.getElementById('edit-status').value = status;

            let form = document.getElementById('editUserForm');
            form.action = `/admin/users/${id}`;

            document.getElementById('editUserModal').classList.add('active');
        }

        function closeEditUserModal() {
            document.getElementById('editUserModal').classList.remove('active');
        }
    </script>
@endsection