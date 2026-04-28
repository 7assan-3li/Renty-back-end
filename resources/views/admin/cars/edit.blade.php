@extends('layouts.admin')

@section('content')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <div class="section-header">
        <h2>{{ __('editVehicle') }}</h2>
        <a href="{{ route('admin.cars.index') }}" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i> {{ __('back') }}
        </a>
    </div>

    <div class="card" style="display: block; max-width: 1000px; margin: 0 auto;">
        <form action="{{ route('admin.cars.update', $car->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div style="display: grid; grid-template-columns: 1fr 300px; gap: 40px;">

                <!-- Left Column: Inputs -->
                <div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div class="form-group">
                            <label for="name">{{ __('vehicleName') }} <span style="color: red;">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name', $car->name) }}" required
                                style="padding: 12px; font-size: 15px; background: #f9f9f9;">
                            @error('name')
                                <span
                                    style="color: red; font-size: 12px; display: block; margin-top: 5px;">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="model">Model <span style="color: red;">*</span></label>
                            <input type="text" name="model" id="model" value="{{ old('model', $car->model) }}" required
                                style="padding: 12px; font-size: 15px; background: #f9f9f9;">
                            @error('model')
                                <span
                                    style="color: red; font-size: 12px; display: block; margin-top: 5px;">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div class="form-group">
                            <label for="category_id">{{ __('category') }} <span style="color: red;">*</span></label>
                            <select name="category_id" id="category_id" required
                                style="padding: 12px; font-size: 15px; background: #f9f9f9;">
                                <option value="">{{ __('selectCategory') }}</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $car->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <span
                                    style="color: red; font-size: 12px; display: block; margin-top: 5px;">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="price_per_day">{{ __('pricePerDay') }} <span style="color: red;">*</span></label>
                            <input type="number" step="0.01" name="price_per_day" id="price_per_day"
                                value="{{ old('price_per_day', $car->price_per_day) }}" required
                                style="padding: 12px; font-size: 15px; background: #f9f9f9;">
                            @error('price_per_day')
                                <span
                                    style="color: red; font-size: 12px; display: block; margin-top: 5px;">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label>{{ __('location') }} <span style="color: red;">*</span> <small
                                style="color: #777;">({{ __('clickMap') }})</small></label>
                        <div id="map"
                            style="height: 300px; border-radius: 8px; margin-bottom: 10px; border: 1px solid #ddd;"></div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            <div class="form-group">
                                <label for="latitude" style="font-size: 12px; color: #777;">{{ __('latitude') }}</label>
                                <input type="text" name="latitude" id="latitude"
                                    value="{{ old('latitude', $car->latitude) }}" required readonly
                                    style="padding: 8px; font-size: 13px; background: #eee; cursor: not-allowed;">
                            </div>
                            <div class="form-group">
                                <label for="longitude" style="font-size: 12px; color: #777;">{{ __('longitude') }}</label>
                                <input type="text" name="longitude" id="longitude"
                                    value="{{ old('longitude', $car->longitude) }}" required readonly
                                    style="padding: 8px; font-size: 13px; background: #eee; cursor: not-allowed;">
                            </div>
                        </div>
                        @error('latitude')
                            <span style="color: red; font-size: 12px; display: block;">Location is required. Please click on the
                                map.</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="status">{{ __('status') }} <span style="color: red;">*</span></label>
                        <select name="status" id="status" required
                            style="padding: 12px; font-size: 15px; background: #f9f9f9;">
                            @foreach(\App\Constants\CarStatus::all() as $status)
                                <option value="{{ $status }}" {{ old('status', $car->status) == $status ? 'selected' : '' }}>
                                    {{ ucfirst($status) }}
                                </option>
                            @endforeach
                        </select>
                        @error('status')
                            <span style="color: red; font-size: 12px; display: block; margin-top: 5px;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="description">{{ __('description') }} ({{ __('optional') }})</label>
                        <textarea name="description" id="description" rows="4" placeholder="Write a short description..."
                            style="padding: 12px; font-size: 15px; background: #f9f9f9; resize: vertical;">{{ old('description', $car->description) }}</textarea>
                        @error('description')
                            <span style="color: red; font-size: 12px; display: block; margin-top: 5px;">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Right Column: Image Upload -->
                <div>
                    <label style="display: block; margin-bottom: 10px; color: #555;">{{ __('vehicleImage') }}</label>

                    <div class="image-upload-box" onclick="document.getElementById('image').click()"
                        style="border: 2px dashed #ddd; border-radius: 12px; padding: 20px; text-align: center; cursor: pointer; transition: 0.3s; background: #fafafa; min-height: 250px; display: flex; flex-direction: column; justify-content: center; align-items: center;">

                        <div id="image-preview"
                            style="{{ $car->image ? 'display: block;' : 'display: none;' }} width: 100%; height: 100%;">
                            @php
                                $carImages = $car->getImages();
                                $previewUrl = $carImages['original'] ?? '#';
                            @endphp
                            <img id="preview-img" src="{{ $previewUrl }}" alt="Preview"
                                style="max-width: 100%; max-height: 200px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                        </div>

                        <div id="upload-placeholder" style="{{ $car->image ? 'display: none;' : 'display: block;' }}">
                            <i class="fa-solid fa-cloud-arrow-up"
                                style="font-size: 40px; color: #ccc; margin-bottom: 15px;"></i>
                            <p style="color: #888; font-size: 14px; margin: 0;">{{ __('clickToChangeImage') }}</p>
                            <span style="font-size: 12px; color: #aaa;">{{ __('imageFormat') }}</span>
                        </div>

                        <input type="file" name="image" id="image" style="display: none;" accept="image/*"
                            onchange="previewImage(this)">
                    </div>
                    @error('image')
                        <span
                            style="color: red; font-size: 12px; display: block; margin-top: 5px; text-align: center;">{{ $message }}</span>
                    @enderror
                </div>

            </div>

            <div
                style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #eee; display: flex; justify-content: flex-end; gap: 15px;">
                <button type="button" class="btn btn-outline" style="border: none;"
                    onclick="window.history.back()">{{ __('cancel') }}</button>
                <button type="submit" class="btn" style="padding: 12px 30px; font-size: 16px;">
                    <i class="fas fa-save"></i> {{ __('updateVehicle') }}
                </button>
            </div>
        </form>
    </div>

    <script>
        function previewImage(input) {
            const previewBox = document.getElementById('image-preview');
            const placeholder = document.getElementById('upload-placeholder');
            const previewImg = document.getElementById('preview-img');

            if (input.files && input.files[0]) {
                const reader = new FileReader();

                reader.onload = function (e) {
                    previewImg.src = e.target.result;
                    previewBox.style.display = 'block';
                    placeholder.style.display = 'none';
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        // Leaflet Map Initialization
        document.addEventListener('DOMContentLoaded', function () {
            var lat = {{ $car->latitude }};
            var lng = {{ $car->longitude }};
            var map = L.map('map').setView([lat, lng], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            var marker = L.marker([lat, lng]).addTo(map);

            function onMapClick(e) {
                if (marker) {
                    map.removeLayer(marker);
                }
                marker = L.marker(e.latlng).addTo(map);
                document.getElementById('latitude').value = e.latlng.lat;
                document.getElementById('longitude').value = e.latlng.lng;
            }

            map.on('click', onMapClick);
        });
    </script>
@endsection