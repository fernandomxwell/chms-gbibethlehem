<div class="mb-3">
    <label for="honorific_title" class="form-label">@lang('honorific_title'):</label>
    <select class="form-select @error('honorific_title') is-invalid @enderror" id="honorific_title" name="honorific_title">
        <option value="">— @lang('none') —</option>
        @foreach(\App\Enums\HonorificTitle::cases() as $title)
            <option value="{{ $title->value }}" {{ old('honorific_title', $congregant?->honorific_title?->value ?? '') == $title->value ? 'selected' : '' }}>{{ $title->label() }}</option>
        @endforeach
    </select>
    @error('honorific_title')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="full_name" class="form-label">@lang('full_name'):</label>
    <input type="text" name="full_name" value="{{ old('full_name', $congregant?->full_name ?? '') }}" class="form-control @error('full_name') is-invalid @enderror" maxlength="100" required>
    @error('full_name')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="gender" class="form-label">@lang('gender'):</label>
    <select class="form-select @error('gender') is-invalid @enderror" id="gender" name="gender" required>
        <option value="male" {{ old('gender', $congregant?->gender ?? '') == 'male' ? 'selected' : '' }}>@lang('male')</option>
        <option value="female" {{ old('gender', $congregant?->gender ?? '') == 'female' ? 'selected' : '' }}>@lang('female')</option>
    </select>
    @error('gender')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="date_of_birth" class="form-label">@lang('date_of_birth'):</label>
    <input type="date" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', $congregant?->formatted_date_of_birth ?? '') }}" class="form-control @error('date_of_birth') is-invalid @enderror" max="{{ now()->format('Y-m-d') }}">
    @error('date_of_birth')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="phone_number" class="form-label">@lang('phone_number'):</label>
    <input type="text" name="phone_number" value="{{ old('phone_number', $congregant?->phone_number ?? '') }}" class="form-control @error('phone_number') is-invalid @enderror" placeholder="@lang('eg') +628123456789 @lang('or') 08123456789">
    @error('phone_number')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="email" class="form-label">@lang('email'):</label>
    <input type="email" name="email" value="{{ old('email', $congregant?->email ?? '') }}" class="form-control @error('email') is-invalid @enderror" maxlength="100">
    @error('email')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="date_of_baptism" class="form-label">@lang('date_of_baptism'):</label>
    <input type="date" id="date_of_baptism" name="date_of_baptism" value="{{ old('date_of_baptism', $congregant?->formatted_date_of_baptism ?? '') }}" class="form-control @error('date_of_baptism') is-invalid @enderror" max="{{ now()->format('Y-m-d') }}">
    @error('date_of_baptism')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="status" class="form-label">@lang('status'):</label>
    <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
        <option value="member" {{ old('status', $congregant?->status ?? '') == 'member' ? 'selected' : '' }}>@lang('member')</option>
        <option value="sympathizer" {{ old('status', $congregant?->status ?? '') == 'sympathizer' ? 'selected' : '' }}>@lang('sympathizer')</option>
    </select>
    @error('status')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
