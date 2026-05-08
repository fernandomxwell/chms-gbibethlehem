<div class="mb-3">
    <label for="name" class="form-label">@lang('name'):</label>
    <input type="text"
        id="name"
        name="name"
        value="{{ old('name', $role?->name ?? '') }}"
        class="form-control @error('name') is-invalid @enderror"
        maxlength="255"
        required
        autofocus>
    @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

@include('roles.partials.permissions', ['selectedPermissions' => $role?->permissions ?? []])
