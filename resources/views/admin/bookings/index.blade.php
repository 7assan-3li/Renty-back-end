@extends('layouts.admin')

@section('content')
    <div class="page-header">
        <h1>{{ __('bookingsManagement') }}</h1>
    </div>

    <div class="card">
        <div class="card-header">
            <h3>{{ __('recentBookings') }}</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>{{ __('opNumber') }}</th>
                            <th>{{ __('user') }}</th>
                            <th>{{ __('vehicle') }}</th>
                            <th>{{ __('dates') }}</th>
                            <th>{{ __('totalPrice') }}</th>
                            <th>{{ __('status') }}</th>
                            <th>{{ __('payment') }}</th>
                            <th>{{ __('finished') }}</th>
                            <th>{{ __('actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bookings as $booking)
                            <tr>
                                <td>#{{ $booking->id }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($booking->user->image)
                                            <img src="{{ $booking->user->image }}" alt="" class="avatar-sm me-2">
                                        @else
                                            <div class="avatar-sm me-2 bg-secondary text-white d-flex align-items-center justify-content-center rounded-circle">
                                                {{ substr($booking->user->name, 0, 1) }}
                                            </div>
                                        @endif
                                        <div>
                                            <div class="fw-bold">{{ $booking->user->name }}</div>
                                            <div class="text-muted small">{{ $booking->user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($booking->car)
                                        <div class="d-flex align-items-center">
                                            @php
                                                $carImages = $booking->car->images_urls;
                                                $carThumbnail = is_array($carImages) ? ($carImages['thumbnail'] ?? $carImages['original'] ?? '') : asset('storage/' . $booking->car->image);
                                            @endphp
                                            <img src="{{ $carThumbnail }}" alt="" class="img-thumbnail me-2" style="width: 50px; height: 50px; object-fit: cover;">
                                            <div>
                                                <div class="fw-bold">{{ $booking->car->name }}</div>
                                                <div class="text-muted small">{{ $booking->car->model }}</div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-danger">{{ __('carDeleted') }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div>{{ \Carbon\Carbon::parse($booking->start_date)->format('M d, Y') }}</div>
                                    <div class="text-muted small">{{ __('to') }} {{ \Carbon\Carbon::parse($booking->end_date)->format('M d, Y') }}</div>
                                </td>
                                <td>${{ number_format($booking->total_price, 2) }}</td>
                                <td>
                                    @if($booking->status == 'pending')
                                        <span class="badge bg-warning text-dark">{{ __('pending') }}</span>
                                    @elseif($booking->status == 'confirmed')
                                        <span class="badge bg-success">{{ __('completed') }}</span> <!-- Wait, confirmed is confusing with completed? No, confirmed is confirmed. -->
                                    @elseif($booking->status == 'cancelled')
                                        <span class="badge bg-danger">{{ __('cancelled') }}</span>
                                    @elseif($booking->status == 'completed')
                                        <span class="badge bg-info">{{ __('completed') }}</span>
                                    @else
                                        <span class="badge bg-secondary">{{ __('status') }}: {{ ucfirst($booking->status) }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($booking->payment)
                                        @if($booking->payment->status == 'succeeded')
                                            <span class="badge bg-success">{{ __('paid') }}</span>
                                        @else
                                            <span class="badge bg-warning text-dark">{{ ucfirst($booking->payment->status) }}</span>
                                        @endif
                                    @else
                                        <span class="badge bg-secondary">{{ __('unpaid') }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($booking->finished == 'Yes')
                                        <span class="badge bg-success">{{ __('yes') }}</span>
                                    @else
                                        <span class="badge bg-secondary">{{ __('no') }}</span>
                                    @endif
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" onclick="showBookingDetails({{ $booking }})">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                    
                                    @if($booking->status == 'pending')
                                        <form action="{{ route('admin.bookings.updateStatus', $booking->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="status" value="confirmed">
                                            <button type="submit" class="btn btn-sm btn-success" title="{{ __('approve') }}">
                                                <i class="fa-solid fa-check"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.bookings.updateStatus', $booking->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="status" value="cancelled">
                                            <button type="submit" class="btn btn-sm btn-danger" title="{{ __('cancel') }}">
                                                <i class="fa-solid fa-xmark"></i>
                                            </button>
                                        </form>
                                    @elseif($booking->status == 'confirmed')
                                        <form action="{{ route('admin.bookings.updateStatus', $booking->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="status" value="completed">
                                            <button type="submit" class="btn btn-sm btn-info text-white" title="{{ __('markCompleted') }}">
                                                <i class="fa-solid fa-flag-checkered"></i>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">{{ __('noBookingsFound') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $bookings->links() }}
            </div>
        </div>
    </div>

    <!-- Booking Details Modal -->
    <div id="bookingModal" class="modal-overlay">
        <div class="modal">
            <div class="modal-header">
                <h3>{{ __('bookingDetails') }} <span id="modalBookingId"></span></h3>
                <span class="close-modal" onclick="closeBookingModal()">&times;</span>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>{{ __('user') }}</label>
                        <input type="text" id="modalUser" readonly>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>{{ __('vehicle') }}</label>
                        <input type="text" id="modalCar" readonly>
                    </div>
                </div>
                <div class="row" style="margin-top:10px">
                    <div class="col-md-6 form-group">
                        <label>{{ __('startDate') }}</label>
                        <input type="text" id="modalStart" readonly>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>{{ __('endDate') }}</label>
                        <input type="text" id="modalEnd" readonly>
                    </div>
                </div>
                <div class="row" style="margin-top:10px">
                     <div class="col-md-6 form-group">
                        <label>{{ __('totalPrice') }}</label>
                        <input type="text" id="modalPrice" readonly>
                    </div>
                    <div class="col-md-4 form-group">
                        <label>{{ __('status') }}</label>
                        <input type="text" id="modalStatus" readonly>
                    </div>
                    <div class="col-md-4 form-group">
                        <label>{{ __('finished') }}</label>
                        <input type="text" id="modalFinished" readonly>
                    </div>
                </div>
                <!-- You can add more details like Payment info here -->
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline" onclick="closeBookingModal()">{{ __('close') }}</button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function showBookingDetails(booking) {
            document.getElementById('modalBookingId').innerText = '#' + booking.id;
            document.getElementById('modalUser').value = booking.user.name;
            document.getElementById('modalCar').value = booking.car ? booking.car.name : 'Deleted Car';
            document.getElementById('modalStart').value = booking.start_date;
            document.getElementById('modalEnd').value = booking.end_date;
            document.getElementById('modalPrice').value = '$' + booking.total_price;
            document.getElementById('modalStatus').value = booking.status;
            document.getElementById('modalFinished').value = booking.finished;
            
            document.getElementById('bookingModal').classList.add('active');
        }

        function closeBookingModal() {
            document.getElementById('bookingModal').classList.remove('active');
        }
    </script>
    @endpush
@endsection
