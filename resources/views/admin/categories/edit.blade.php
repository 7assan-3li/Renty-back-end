@extends('layouts.admin')

@section('content')
    <div class="section-header">
        <h2>{{ __('editCategory') }}</h2>
        <a href="{{ route('admin.categories.index') }}" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i> {{ __('back') }}
        </a>
    </div>

    <div class="card" style="display: block; max-width: 900px; margin: 0 auto;">
        <form action="{{ route('admin.categories.update', $category->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div style="display: grid; grid-template-columns: 1fr 300px; gap: 40px;">

                <!-- Left Column: Inputs -->
                <div>
                    <div class="form-group">
                        <label for="name">{{ __('catName') }} <span style="color: red;">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name', $category->name) }}"
                            placeholder="{{ __('categoryNamePlaceholder') }}" required
                            style="padding: 12px; font-size: 15px; background: #f9f9f9;">
                        @error('name')
                            <span style="color: red; font-size: 12px; display: block; margin-top: 5px;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="description">{{ __('description') }} ({{ __('optional') }})</label>
                        <textarea name="description" id="description" rows="6" placeholder="{{ __('writeDescription') }}"
                            style="padding: 12px; font-size: 15px; background: #f9f9f9; resize: vertical;">{{ old('description', $category->description) }}</textarea>
                        @error('description')
                            <span style="color: red; font-size: 12px; display: block; margin-top: 5px;">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Right Column: Image Upload -->
                <div>
                    <label style="display: block; margin-bottom: 10px; color: #555;">{{ __('categoryImage') }}</label>

                    <div class="image-upload-box" onclick="document.getElementById('image').click()"
                        style="border: 2px dashed #ddd; border-radius: 12px; padding: 20px; text-align: center; cursor: pointer; transition: 0.3s; background: #fafafa; min-height: 250px; display: flex; flex-direction: column; justify-content: center; align-items: center;">

                        <div id="image-preview"
                            style="{{ $category->image ? 'display: block;' : 'display: none;' }} width: 100%; height: 100%;">
                            @php
                                $categoryImages = $category->getImages();
                                $previewUrl = $categoryImages['original'] ?? '#';
                            @endphp
                            <img id="preview-img" src="{{ $previewUrl }}"
                                alt="Preview"
                                style="max-width: 100%; max-height: 200px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                        </div>

                        <div id="upload-placeholder" style="{{ $category->image ? 'display: none;' : 'display: block;' }}">
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
                    <i class="fas fa-save"></i> {{ __('updateCategory') }}
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
            } else {
                // If canceling selection, and there was an image before, we might want to revert or just show placeholder if we cleared it.
                // For simplicity, if no file is selected and no image exists, show placeholder. 
                // However, since this is edit, we usually want to keep showing the current image if the user cancels.
                // But input type file on cancel usually doesn't clear the previous selection unless specifically handled or if the value was empty.
            }
        }
    </script>
@endsection