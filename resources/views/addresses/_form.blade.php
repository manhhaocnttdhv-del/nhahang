<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Nhãn (tùy chọn)</label>
        <input type="text" class="form-control" name="label" value="{{ old('label', $address?->label) }}" placeholder="VD: Nhà, Cơ quan">
        @error('label')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Tên người nhận <span class="text-danger">*</span></label>
        <input type="text" class="form-control" name="recipient_name" value="{{ old('recipient_name', $address?->recipient_name) }}" required>
        @error('recipient_name')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Số điện thoại <span class="text-danger">*</span></label>
        <input type="text" class="form-control" name="phone" value="{{ old('phone', $address?->phone) }}" required>
        @error('phone')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Thành phố <span class="text-danger">*</span></label>
        <input type="text" class="form-control" name="city" value="{{ old('city', $address?->city) }}" required>
        @error('city')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Quận/Huyện</label>
        <input type="text" class="form-control" name="district" value="{{ old('district', $address?->district) }}">
        @error('district')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Phường/Xã</label>
        <input type="text" class="form-control" name="ward" value="{{ old('ward', $address?->ward) }}">
        @error('ward')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-12 mb-3">
        <label class="form-label fw-bold">Địa chỉ chi tiết <span class="text-danger">*</span></label>
        <input type="text" class="form-control" name="address_line1" value="{{ old('address_line1', $address?->address_line1) }}" required>
        @error('address_line1')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-12 mb-3">
        <label class="form-label fw-bold">Địa chỉ bổ sung</label>
        <input type="text" class="form-control" name="address_line2" value="{{ old('address_line2', $address?->address_line2) }}">
        @error('address_line2')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Mã bưu điện</label>
        <input type="text" class="form-control" name="postal_code" value="{{ old('postal_code', $address?->postal_code) }}">
        @error('postal_code')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Ghi chú</label>
        <input type="text" class="form-control" name="notes" value="{{ old('notes', $address?->notes) }}" placeholder="VD: Tầng 3, căn hộ 301">
        @error('notes')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-12 mb-3">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="is_default" id="is_default" value="1" {{ old('is_default', $address?->is_default) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_default">
                Đặt làm địa chỉ mặc định
            </label>
        </div>
    </div>
</div>

