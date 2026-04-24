<div class="form-row">
    <div class="form-group">
        <label class="label label--required" for="name">Ad</label>
        <input type="text" id="name" name="name" class="input @error('name') is-invalid @enderror"
               value="{{ old('name', $user->name) }}" required>
        @error('name') <span class="error">{{ $message }}</span> @enderror
    </div>

    <div class="form-group">
        <label class="label label--required" for="email">Email</label>
        <input type="email" id="email" name="email" class="input @error('email') is-invalid @enderror"
               value="{{ old('email', $user->email) }}" required>
        @error('email') <span class="error">{{ $message }}</span> @enderror
    </div>
</div>

<div class="form-group">
    <label class="label @if(!$user->exists) label--required @endif" for="password">
        Şifrə
        @if ($user->exists) <span class="text-muted text-xs">(boş qoysanız dəyişmir)</span> @endif
    </label>
    <input type="password" id="password" name="password" class="input @error('password') is-invalid @enderror"
           @unless($user->exists) required @endunless minlength="6">
    @error('password') <span class="error">{{ $message }}</span> @enderror
</div>

@php $isSelf = $user->exists && $user->id === auth()->id(); @endphp

<div class="form-group">
    <label class="label">Rollar</label>
    @if ($isSelf)
        <div class="alert alert--warning">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><path d="M12 9v4"/><path d="M12 17h.01"/>
            </svg>
            <div>
                <strong>Öz rolunuzu dəyişə bilməzsiniz.</strong> Cari rollar:
                @foreach ($userRoles as $r)
                    <span class="badge badge--{{ $r === \App\Models\User::ROLE_SUPER_ADMIN ? 'destructive' : ($r === \App\Models\User::ROLE_ADMIN ? 'info' : 'secondary') }}">{{ $r }}</span>
                @endforeach
            </div>
        </div>
    @else
        <div class="flex flex-col gap-2">
            @foreach ($roles as $role)
                <label class="checkbox-wrap">
                    <input type="checkbox" name="roles[]" value="{{ $role->name }}"
                           @checked(in_array($role->name, old('roles', $userRoles)))>
                    <span>
                        {{ $role->name }}
                        @if ($role->name === \App\Models\User::ROLE_SUPER_ADMIN)
                            <span class="badge badge--destructive">bütün icazələr</span>
                        @endif
                    </span>
                </label>
            @endforeach
        </div>
    @endif
</div>
