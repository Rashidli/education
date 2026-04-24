@php $isSuperAdmin = $role->exists && $role->name === \App\Models\User::ROLE_SUPER_ADMIN; @endphp

<div class="form-group">
    <label class="label label--required" for="name">Rol adı</label>
    <input type="text" id="name" name="name" class="input @error('name') is-invalid @enderror"
           value="{{ old('name', $role->name) }}" @if($isSuperAdmin) readonly @endif required>
    @error('name') <span class="error">{{ $message }}</span> @enderror
</div>

<div class="form-group">
    <label class="label">İcazələr</label>
    @if ($isSuperAdmin)
        <div class="alert alert--info">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/>
            </svg>
            <span>Super Admin bütün icazələri <code>Gate::before</code> vasitəsilə avtomatik alır. Burada seçim lazım deyil.</span>
        </div>
    @else
        @foreach ($permissions as $group => $items)
            <div class="mt-3">
                <div class="flex items-center gap-2 mb-1">
                    <label class="checkbox-wrap">
                        <input type="checkbox" class="perm-group" data-group="{{ $group }}">
                        <strong style="text-transform:uppercase;font-size:0.75rem;color:var(--muted-foreground)">{{ $group }}</strong>
                    </label>
                </div>
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:0.375rem;padding-left:1.5rem">
                    @foreach ($items as $p)
                        <label class="checkbox-wrap">
                            <input type="checkbox" class="perm-item" data-group="{{ $group }}"
                                   name="permissions[]" value="{{ $p->name }}"
                                   @checked(in_array($p->name, old('permissions', $rolePermissions)))>
                            <span>{{ $permissionLabels[$p->name] ?? $p->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        @endforeach

        @push('scripts')
        <script>
            document.querySelectorAll('.perm-group').forEach(g => {
                const group = g.dataset.group;
                const items = document.querySelectorAll(`.perm-item[data-group="${group}"]`);
                const sync = () => {
                    const all = [...items].every(i => i.checked);
                    const some = [...items].some(i => i.checked);
                    g.checked = all;
                    g.indeterminate = !all && some;
                };
                g.addEventListener('change', () => items.forEach(i => i.checked = g.checked));
                items.forEach(i => i.addEventListener('change', sync));
                sync();
            });
        </script>
        @endpush
    @endif
</div>
