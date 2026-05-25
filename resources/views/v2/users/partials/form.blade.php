@php
    $isEdit = $mode === 'edit';
    $selectedRoleName = $isEdit ? data_get($user, 'role') : '';
    $usesMultipleBranches = in_array($selectedRoleName, ['Regional Manager', 'Sale Manager']);
@endphp

<section class="rounded-lg border border-erp-line bg-white shadow-sm">
    <div class="flex flex-col gap-3 border-b border-erp-line px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-base font-bold text-erp-ink">Authorization</h2>
            <p class="mt-1 text-sm text-erp-mute">Assign company, branch scope, and role.</p>
        </div>
        <a href="{{ url('/usersDetails') }}" class="rounded-lg border border-erp-line px-4 py-2 text-sm font-bold text-erp-text transition hover:border-erp hover:text-erp-dark">Back to List</a>
    </div>

    <div class="grid gap-5 p-5 lg:grid-cols-12">
        <div class="lg:col-span-4">
            <label for="company" class="text-sm font-bold text-erp-ink">Company</label>
            <select name="company" id="company" class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                <option value="">Select Company</option>
                @foreach (($company ?? []) as $value)
                    <option value="{{ $value->company_id }}" {{ $isEdit && $value->company_id == data_get($user, 'company_id') ? 'selected' : '' }}>{{ $value->name }}</option>
                @endforeach
            </select>
            @error('company')<p class="mt-1 text-xs text-rose-600">Required field can not be blank.</p>@enderror
        </div>

        <div id="singleBranch" class="lg:col-span-4 {{ $usesMultipleBranches ? 'hidden' : '' }}">
            <label for="branch" class="text-sm font-bold text-erp-ink">Branch</label>
            <select name="branch" id="branch" class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                <option value="">Select Branch</option>
                @foreach (($branch ?? []) as $value)
                    <option value="{{ $value->branch_id }}" {{ $isEdit && $value->branch_name == data_get($user, 'branch_name') ? 'selected' : '' }}>{{ $value->branch_name }}</option>
                @endforeach
            </select>
            @error('branch')<p class="mt-1 text-xs text-rose-600">Required field can not be blank.</p>@enderror
        </div>

        <div id="multipleBranch" class="lg:col-span-4 {{ $usesMultipleBranches ? '' : 'hidden' }}">
            <label for="multiplebranches" class="text-sm font-bold text-erp-ink">Branches</label>
            <select name="{{ $isEdit ? 'branches[]' : 'branch[]' }}" id="multiplebranches" multiple class="mt-2 min-h-28 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                @foreach (($branch ?? []) as $value)
                    <option value="{{ $value->branch_id }}" {{ $isEdit && in_array($value->branch_id, $userBranches->toArray()) ? 'selected' : '' }}>{{ $value->branch_name }}</option>
                @endforeach
            </select>
            <p class="mt-1 text-xs text-erp-mute">Regional/Sale Manager can use multiple branches.</p>
        </div>

        <div class="lg:col-span-4">
            <div class="flex items-center justify-between gap-3">
                <label for="role" class="text-sm font-bold text-erp-ink">Role</label>
                @if (!$isEdit)
                    <button type="button" id="toggleRolePanel" class="text-xs font-bold uppercase tracking-[0.14em] text-erp-dark">Add Role</button>
                @endif
            </div>
            <select name="role" id="role" class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                <option value="">Select Role</option>
                @foreach (($role ?? []) as $value)
                    <option value="{{ $value->role_id }}" {{ $isEdit && $value->role == data_get($user, 'role') ? 'selected' : '' }}>{{ $value->role }}</option>
                @endforeach
            </select>
            @error('role')<p class="mt-1 text-xs text-rose-600">Required field can not be blank.</p>@enderror
        </div>

        @if (!$isEdit)
            <div id="rolePanel" class="hidden rounded-lg border border-erp-line bg-slate-50 p-4 lg:col-span-12">
                <label for="rolename" class="text-sm font-bold text-erp-ink">New Role Name</label>
                <div class="mt-2 flex flex-col gap-2 sm:flex-row">
                    <input type="text" id="rolename" class="h-10 flex-1 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                    <button type="button" id="btnAddRole" class="rounded-lg bg-erp px-4 py-2 text-sm font-bold text-white transition hover:bg-erp-dark">Create Role</button>
                </div>
            </div>
        @endif
    </div>
</section>

<section class="rounded-lg border border-erp-line bg-white shadow-sm">
    <div class="border-b border-erp-line px-5 py-4">
        <h2 class="text-base font-bold text-erp-ink">Profile Details</h2>
        <p class="mt-1 text-sm text-erp-mute">Contact, location, login credentials, and profile image.</p>
    </div>

    <div class="grid gap-5 p-5 lg:grid-cols-12">
        <div class="lg:col-span-4">
            <label for="fullname" class="text-sm font-bold text-erp-ink">Full Name</label>
            <input type="text" name="fullname" id="fullname" value="{{ old('fullname', $isEdit ? data_get($user, 'fullname') : '') }}" class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
            @error('fullname')<p class="mt-1 text-xs text-rose-600">Required field can not be blank.</p>@enderror
        </div>

        <div class="lg:col-span-4">
            <label for="email" class="text-sm font-bold text-erp-ink">Email</label>
            <input type="text" name="email" id="email" value="{{ old('email', $isEdit ? data_get($user, 'email') : '') }}" placeholder="something@gmail.com" class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
        </div>

        <div class="lg:col-span-4">
            <label for="contact" class="text-sm font-bold text-erp-ink">Contact Number</label>
            <input type="text" name="contact" id="contact" value="{{ old('contact', $isEdit ? data_get($user, 'contact') : '') }}" placeholder="0300-1234567" class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
        </div>

        <div class="lg:col-span-3">
            <label for="country" class="text-sm font-bold text-erp-ink">Country</label>
            <select name="country" id="country" class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                <option value="">Select Country</option>
                @foreach (($country ?? []) as $value)
                    <option value="{{ $value->country_id }}" {{ ($isEdit && $value->country_name == data_get($user, 'country_name')) || (!$isEdit && $value->country_name == 'Pakistan') ? 'selected' : '' }}>{{ $value->country_name }}</option>
                @endforeach
            </select>
            @error('country')<p class="mt-1 text-xs text-rose-600">Required field can not be blank.</p>@enderror
        </div>

        <div class="lg:col-span-3">
            <label for="city" class="text-sm font-bold text-erp-ink">City</label>
            <select name="city" id="city" class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                <option value="">Select City</option>
                @foreach (($city ?? []) as $value)
                    <option value="{{ $value->city_id }}" {{ ($isEdit && $value->city_name == data_get($user, 'city_name')) || (!$isEdit && $value->city_name == 'Karachi') ? 'selected' : '' }}>{{ $value->city_name }}</option>
                @endforeach
            </select>
            @error('city')<p class="mt-1 text-xs text-rose-600">Required field can not be blank.</p>@enderror
        </div>

        <div class="lg:col-span-6">
            <label for="address" class="text-sm font-bold text-erp-ink">Address</label>
            <textarea name="address" id="address" rows="4" class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">{{ old('address', $isEdit ? ltrim(data_get($user, 'address')) : '') }}</textarea>
        </div>

        <div class="lg:col-span-4">
            <label for="username" class="text-sm font-bold text-erp-ink">Username</label>
            <input type="text" name="username" id="username" value="{{ old('username', $isEdit ? data_get($user, 'username') : '') }}" class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
            @error('username')<p class="mt-1 text-xs text-rose-600">Required field can not be blank.</p>@enderror
        </div>

        <div class="lg:col-span-4">
            <label for="password" class="text-sm font-bold text-erp-ink">Password</label>
            <input type="{{ $isEdit ? 'text' : 'password' }}" name="password" id="password" value="{{ old('password', $isEdit ? data_get($user, 'show_password') : '') }}" class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
            @error('password')<p class="mt-1 text-xs text-rose-600">Required field can not be blank.</p>@enderror
        </div>

        <div class="lg:col-span-4">
            <label for="vdimg" class="text-sm font-bold text-erp-ink">User Image</label>
            <div class="mt-2 rounded-lg border border-dashed border-erp-line bg-slate-50 p-4">
                <img id="vdpimg"
                    src="{{ $isEdit ? asset('storage/images/users/' . (data_get($user, 'image') ?: 'placeholder.jpg')) : asset('storage/images/placeholder.jpg') }}"
                    class="h-28 w-28 rounded-lg object-cover ring-1 ring-slate-200" alt="User image preview">
                <input type="file" name="vdimg" id="vdimg" class="mt-4 block w-full text-sm text-erp-mute file:mr-3 file:rounded-lg file:border-0 file:bg-erp file:px-3 file:py-2 file:text-sm file:font-bold file:text-white">
            </div>
        </div>
    </div>

    <div class="flex flex-col gap-3 border-t border-erp-line px-5 py-4 sm:flex-row sm:justify-end">
        <a href="{{ url('/usersDetails') }}" class="rounded-lg border border-erp-line px-4 py-2 text-center text-sm font-bold text-erp-text transition hover:border-erp hover:text-erp-dark">Cancel</a>
        <button type="submit" class="rounded-lg bg-erp px-4 py-2 text-sm font-bold text-white transition hover:bg-erp-dark">{{ $submitLabel }}</button>
    </div>
</section>
