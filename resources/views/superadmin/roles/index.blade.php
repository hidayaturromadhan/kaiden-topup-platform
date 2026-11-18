@extends('layouts.admin')

@section('title', 'Peran & Matriks Izin RBAC - KAIDEN')
@section('header_title', 'Tata Kelola RBAC (Role-Based Access Control)')

@push('styles')
<style>
/* Modern Tactile Pill Switch Sesuai Referensi Visual */
.kaiden-toggle-switch {
    position: relative;
    display: inline-block;
    width: 54px;
    height: 28px;
    cursor: pointer;
    user-select: none;
    vertical-align: middle;
}

.kaiden-toggle-switch .toggle-input {
    position: absolute;
    opacity: 0;
    width: 0;
    height: 0;
    pointer-events: none;
}

.kaiden-toggle-switch .toggle-track {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #E2E8F0;
    border-radius: 9999px;
    transition: background-color 0.22s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.22s ease;
    box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.12), inset 0 1px 2px rgba(0, 0, 0, 0.08);
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 8px;
    box-sizing: border-box;
}

.kaiden-toggle-switch .toggle-track .label-on {
    font-size: 9px;
    font-weight: 800;
    font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
    letter-spacing: 0.5px;
    color: #064E3B;
    text-shadow: 0 1px 0 rgba(255, 255, 255, 0.25);
    opacity: 0;
    transform: scale(0.85);
    transition: opacity 0.2s ease, transform 0.2s ease;
    user-select: none;
}

.kaiden-toggle-switch .toggle-track .label-off {
    font-size: 9px;
    font-weight: 800;
    font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
    letter-spacing: 0.5px;
    color: #94A3B8;
    text-shadow: 0 1px 0 rgba(255, 255, 255, 0.8);
    opacity: 1;
    transform: scale(1);
    transition: opacity 0.2s ease, transform 0.2s ease;
    margin-left: auto;
    user-select: none;
}

.kaiden-toggle-switch .toggle-knob {
    position: absolute;
    top: 2px;
    left: 2px;
    width: 24px;
    height: 24px;
    background: #FFFFFF;
    border-radius: 50%;
    box-shadow: 0 3px 6px rgba(0, 0, 0, 0.16), 0 1px 2px rgba(0, 0, 0, 0.1);
    transition: transform 0.22s cubic-bezier(0.4, 0, 0.2, 1);
    pointer-events: none;
}

/* Checked (ON) State */
.kaiden-toggle-switch .toggle-input:checked + .toggle-track {
    background-color: #10B981;
    box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.16), inset 0 1px 2px rgba(0, 0, 0, 0.1);
}

.kaiden-toggle-switch .toggle-input:checked + .toggle-track .label-on {
    opacity: 1;
    transform: scale(1);
}

.kaiden-toggle-switch .toggle-input:checked + .toggle-track .label-off {
    opacity: 0;
    transform: scale(0.85);
}

.kaiden-toggle-switch .toggle-input:checked ~ .toggle-knob {
    transform: translateX(26px);
}

.kaiden-toggle-switch .toggle-input:focus-visible + .toggle-track {
    outline: 2px solid #6D0808;
    outline-offset: 2px;
}
</style>
@endpush

@section('content')
<div class="space-y-5 pt-2 pb-16">

    <!-- 1. Role Selection Tabs (UX: Pilihan Peran Navigasi Cepat) -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        @foreach($roles as $index => $role)
            @php
                $roleIcon = match($role->name) {
                    'superadmin' => 'shield-check',
                    'admin' => 'shield',
                    default => 'user',
                };
            @endphp
            <button type="button" onclick="switchRoleTab('{{ $role->id }}')" id="role-tab-btn-{{ $role->id }}" class="role-tab-btn p-3.5 sm:p-4 rounded-2xl border text-left transition-all duration-200 cursor-pointer flex items-center justify-between {{ $index === 0 ? 'bg-white border-[#6D0808] shadow-sm ring-2 ring-[#6D0808]/10' : 'bg-white/60 hover:bg-white border-[#212121]/10' }}">
                <div class="flex items-center space-x-3 truncate">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 {{ $role->name === 'superadmin' ? 'bg-[#6D0808]/10 text-[#6D0808]' : ($role->name === 'admin' ? 'bg-[#212121]/10 text-[#212121]' : 'bg-emerald-50 text-emerald-700') }}">
                        <x-icon name="{{ $roleIcon }}" class="w-4 h-4" />
                    </div>
                    <div class="truncate">
                        <div class="text-xs font-bold text-[#212121] truncate">{{ $role->display_name }}</div>
                        <div class="text-[10px] font-mono text-[#212121]/50 truncate">{{ $role->name }}</div>
                    </div>
                </div>

                <div class="shrink-0 text-right ml-2">
                    <span id="tab-badge-{{ $role->id }}" class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-mono font-bold {{ $role->permissions->count() > 0 ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-600' }}">
                        {{ $role->permissions->count() }} Izin
                    </span>
                </div>
            </button>
        @endforeach
    </div>

    <!-- 2. Role Content Panels -->
    @foreach($roles as $index => $role)
        <div id="role-panel-{{ $role->id }}" class="role-panel rounded-2xl bg-white border border-[#212121]/10 shadow-xs overflow-hidden {{ $index === 0 ? '' : 'hidden' }}">
            
            <!-- Ergonomic Top Action Toolbar -->
            <div class="p-4 sm:p-5 border-b border-[#212121]/10 bg-[#F9F8F6] space-y-4">
                
                <!-- Baris 1: Identitas Peran & Tombol Simpan Utama -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <h3 class="text-base font-bold text-[#212121]">{{ $role->display_name }}</h3>
                            <span class="font-mono text-[11px] px-2 py-0.5 rounded-md bg-[#212121]/10 text-[#212121] font-semibold">
                                Role: {{ $role->name }}
                            </span>
                            <span class="text-xs font-mono font-bold text-[#6D0808] px-2.5 py-0.5 rounded-full bg-[#6D0808]/10 border border-[#6D0808]/20" id="role-counter-{{ $role->id }}">
                                {{ $role->permissions->count() }} Izin Terpasang
                            </span>
                        </div>
                        <p class="text-xs text-[#212121]/60 mt-1">{{ $role->description }}</p>
                    </div>

                    <!-- Tombol Simpan Manual (Header Atas) -->
                    <button type="button" onclick="submitRoleForm({{ $role->id }})" id="top-save-btn-{{ $role->id }}" class="inline-flex items-center justify-center space-x-1.5 px-4 py-2 rounded-xl text-xs font-semibold text-white bg-[#6D0808] hover:bg-[#540606] transition shadow-xs cursor-pointer shrink-0">
                        <x-icon name="check" class="w-3.5 h-3.5" />
                        <span>Simpan Perubahan</span>
                    </button>
                </div>

                <!-- Baris 2: Kolom Pencarian Izin & Tombol Kontrol Cepat -->
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 pt-3 border-t border-[#212121]/10">
                    <!-- Search Input Izin -->
                    <div class="relative w-full md:w-80">
                        <input type="text" onkeyup="filterPermissions(this.value)" placeholder="Cari nama izin / modul..." class="w-full pl-8 pr-3 py-1.5 rounded-xl border border-[#212121]/15 bg-white text-xs text-[#212121] focus:outline-none focus:ring-2 focus:ring-[#6D0808]/20 transition">
                        <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-[#212121]/40">
                            <x-icon name="search" class="w-3.5 h-3.5" />
                        </div>
                    </div>

                    <!-- Batch Controls & Auto-Save -->
                    <div class="flex flex-wrap items-center gap-2">
                        <!-- Auto-Save Switch -->
                        <label class="inline-flex items-center space-x-2 px-3 py-1.5 rounded-xl bg-white border border-[#212121]/10 text-xs text-[#212121] cursor-pointer shadow-2xs hover:bg-[#F9F8F6] transition select-none">
                            <input type="checkbox" id="autosave-toggle-{{ $role->id }}" checked class="rounded border-[#212121]/20 text-emerald-600 focus:ring-emerald-500">
                            <span class="font-medium text-[11px] flex items-center space-x-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block animate-pulse"></span>
                                <span>Simpan Otomatis</span>
                            </span>
                        </label>

                        <!-- Batch Buttons -->
                        <button type="button" onclick="batchToggleRolePermissions({{ $role->id }}, true)" class="inline-flex items-center space-x-1 px-3 py-1.5 rounded-xl text-xs font-semibold text-[#212121] bg-white border border-[#212121]/15 hover:bg-emerald-50 hover:text-emerald-800 hover:border-emerald-300 transition shadow-2xs cursor-pointer">
                            <x-icon name="check" class="w-3.5 h-3.5 text-emerald-600" />
                            <span>Aktifkan Semua</span>
                        </button>

                        <button type="button" onclick="batchToggleRolePermissions({{ $role->id }}, false)" class="inline-flex items-center space-x-1 px-3 py-1.5 rounded-xl text-xs font-semibold text-[#212121] bg-white border border-[#212121]/15 hover:bg-red-50 hover:text-red-800 hover:border-red-300 transition shadow-2xs cursor-pointer">
                            <x-icon name="x" class="w-3.5 h-3.5 text-red-600" />
                            <span>Matikan Semua</span>
                        </button>
                    </div>
                </div>

            </div>

            <!-- Form Matrix Izin -->
            <form action="{{ route('superadmin.roles.update', $role->id) }}" method="POST" id="role-form-{{ $role->id }}" class="p-4 sm:p-5 space-y-4">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3.5 sm:gap-4">
                    @foreach($permissions as $group => $perms)
                        <div class="permission-module-box p-3.5 rounded-xl border border-[#212121]/10 bg-[#F9F8F6]/40 space-y-2.5 text-xs transition" data-group="{{ strtolower($group) }}">
                            
                            <!-- Module Header with Module-level Quick Toggle -->
                            <div class="font-bold uppercase tracking-wider text-[11px] text-[#212121] pb-1.5 border-b border-[#212121]/10 flex items-center justify-between">
                                <span class="flex items-center space-x-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#6D0808]"></span>
                                    <span>Modul: {{ $group }}</span>
                                </span>
                                <div class="flex items-center space-x-2 text-[10px] font-semibold">
                                    <button type="button" onclick="toggleModulePermissions(this, true)" class="text-emerald-700 hover:underline cursor-pointer">Semua ON</button>
                                    <span class="text-[#212121]/20">|</span>
                                    <button type="button" onclick="toggleModulePermissions(this, false)" class="text-[#212121]/60 hover:text-red-700 hover:underline cursor-pointer">Semua OFF</button>
                                </div>
                            </div>

                            <!-- List Izin dalam Modul -->
                            <div class="space-y-2 pt-0.5">
                                @foreach($perms as $p)
                                    @php
                                        $isChecked = $role->permissions->contains('id', $p->id);
                                    @endphp
                                    <label class="permission-item-row flex items-center justify-between p-2.5 rounded-xl bg-white border border-[#212121]/10 hover:border-[#6D0808]/30 transition shadow-2xs cursor-pointer select-none group" data-perm-name="{{ strtolower($p->display_name . ' ' . $p->name) }}">
                                        <div class="pr-2 min-w-0">
                                            <div class="font-semibold text-[#212121] group-hover:text-[#6D0808] transition text-xs truncate">
                                                {{ $p->display_name }}
                                            </div>
                                            <div class="text-[10px] font-mono text-[#212121]/50 mt-0.5 truncate">
                                                {{ $p->name }}
                                            </div>
                                        </div>

                                        <!-- Tactile ON/OFF Toggle Switch -->
                                        <div class="kaiden-toggle-switch shrink-0 ml-2">
                                            <input type="checkbox" name="permissions[]" value="{{ $p->id }}" {{ $isChecked ? 'checked' : '' }} class="toggle-input" data-role-id="{{ $role->id }}" data-perm-id="{{ $p->id }}" data-perm-label="{{ $p->display_name }}" onchange="handlePermissionToggle(this)">
                                            <span class="toggle-track">
                                                <span class="label-on">ON</span>
                                                <span class="label-off">OFF</span>
                                            </span>
                                            <span class="toggle-knob"></span>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Bottom Action Footer (Tetap Ada Sebagai Cadangan) -->
                <div class="pt-4 border-t border-[#212121]/10 flex flex-col sm:flex-row items-center justify-between gap-3">
                    <div class="text-xs text-[#212121]/60 text-center sm:text-left">
                        Pastikan izin sesuai prinsip <em>Least Privilege</em>. Perubahan dicatat ke Audit Log.
                    </div>

                    <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center space-x-1.5 px-5 py-2.5 rounded-xl text-xs font-semibold text-white bg-[#6D0808] hover:bg-[#540606] transition shadow-xs cursor-pointer">
                        <x-icon name="check" class="w-3.5 h-3.5" />
                        <span>Simpan Matriks Izin '{{ $role->display_name }}'</span>
                    </button>
                </div>
            </form>
        </div>
    @endforeach

    <!-- 3. Floating Bottom Save Bar (UX: Muncul Saat Ada Perubahan Belum Disimpan Jika Auto-Save Mati) -->
    <div id="floating-save-bar" class="fixed bottom-5 inset-x-4 sm:inset-x-auto sm:right-8 sm:max-w-md z-40 bg-[#212121] text-white p-3.5 sm:p-4 rounded-2xl shadow-xl border border-white/10 flex items-center justify-between gap-3 transition-all duration-300 transform translate-y-24 opacity-0 pointer-events-none">
        <div class="flex items-center space-x-2.5 truncate">
            <div class="w-2.5 h-2.5 rounded-full bg-amber-400 shrink-0 animate-ping"></div>
            <div class="text-xs truncate">
                <span class="font-bold text-white">Ada Perubahan Izin!</span>
                <span class="text-white/70 block text-[11px] truncate" id="floating-bar-desc">Klik simpan untuk menerapkan ke sistem.</span>
            </div>
        </div>

        <div class="flex items-center space-x-2 shrink-0">
            <button type="button" onclick="submitActiveRoleForm()" class="px-3.5 py-1.5 rounded-xl text-xs font-bold bg-[#6D0808] text-white hover:bg-[#540606] transition shadow-xs cursor-pointer">
                Simpan
            </button>
            <button type="button" onclick="hideFloatingSaveBar()" class="p-1.5 rounded-lg text-white/50 hover:text-white transition cursor-pointer" title="Abaikan">
                <x-icon name="x" class="w-4 h-4" />
            </button>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    let activeRoleId = '{{ $roles->first()?->id ?? 1 }}';
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    // 1. Tab Navigasi Peran
    window.switchRoleTab = function(roleId) {
        activeRoleId = roleId;

        // Reset semua tab buttons
        document.querySelectorAll('.role-tab-btn').forEach(btn => {
            btn.classList.remove('border-[#6D0808]', 'shadow-sm', 'ring-2', 'ring-[#6D0808]/10');
            btn.classList.add('border-[#212121]/10', 'bg-white/60');
        });

        // Aktifkan tab yang dipilih
        const activeBtn = document.getElementById(`role-tab-btn-${roleId}`);
        if (activeBtn) {
            activeBtn.classList.add('border-[#6D0808]', 'shadow-sm', 'ring-2', 'ring-[#6D0808]/10');
            activeBtn.classList.remove('border-[#212121]/10', 'bg-white/60');
        }

        // Tampilkan panel yang bersangkutan
        document.querySelectorAll('.role-panel').forEach(panel => {
            panel.classList.add('hidden');
        });
        const activePanel = document.getElementById(`role-panel-${roleId}`);
        if (activePanel) {
            activePanel.classList.remove('hidden');
        }

        hideFloatingSaveBar();
    };

    // 2. Real-time Search Filter Izin
    window.filterPermissions = function(keyword) {
        const query = keyword.toLowerCase().trim();
        const rows = document.querySelectorAll('.permission-item-row');
        const modules = document.querySelectorAll('.permission-module-box');

        if (!query) {
            rows.forEach(r => r.classList.remove('hidden'));
            modules.forEach(m => m.classList.remove('hidden'));
            return;
        }

        modules.forEach(mod => {
            let hasVisibleRow = false;
            const groupName = mod.getAttribute('data-group') || '';
            const isGroupMatch = groupName.includes(query);

            const modRows = mod.querySelectorAll('.permission-item-row');
            modRows.forEach(row => {
                const text = row.getAttribute('data-perm-name') || '';
                if (isGroupMatch || text.includes(query)) {
                    row.classList.remove('hidden');
                    hasVisibleRow = true;
                } else {
                    row.classList.add('hidden');
                }
            });

            if (hasVisibleRow) {
                mod.classList.remove('hidden');
            } else {
                mod.classList.add('hidden');
            }
        });
    };

    // 3. Handle Toggle Permintaan Sakelar
    window.handlePermissionToggle = async function(input) {
        const roleId = input.getAttribute('data-role-id');
        const permId = input.getAttribute('data-perm-id');
        const permLabel = input.getAttribute('data-perm-label') || 'Izin';
        const isChecked = input.checked;
        const autoSaveCheckbox = document.getElementById(`autosave-toggle-${roleId}`);
        const isAutoSave = autoSaveCheckbox ? autoSaveCheckbox.checked : false;

        // Update Counter Tampilan Segera
        updateRoleDisplayCount(roleId);

        if (isAutoSave) {
            // Mode Auto-Save (Instan Asynchronous via AJAX)
            try {
                const response = await fetch(`/superadmin/roles/${roleId}/toggle-permission`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        permission_id: permId,
                        state: isChecked
                    })
                });

                const result = await response.json();
                if (result.success) {
                    if (window.notyf) {
                        window.notyf.success(`Izin '${permLabel}' ${isChecked ? 'diaktifkan' : 'dinonaktifkan'}.`);
                    }
                    updateRoleDisplayCount(roleId, result.permission_count);
                } else {
                    throw new Error(result.message || 'Gagal mengubah izin');
                }
            } catch (err) {
                // Kembalikan sakelar jika gagal
                input.checked = !isChecked;
                updateRoleDisplayCount(roleId);
                if (window.notyf) {
                    window.notyf.error(err.message || 'Gagal memperbarui izin');
                }
            }
        } else {
            // Mode Manual: Tampilkan Floating Save Bar
            showFloatingSaveBar(`Izin '${permLabel}' ${isChecked ? 'dinyalakan' : 'dimatikan'}`);
        }
    };

    // 4. Batch Toggle per Modul
    window.toggleModulePermissions = function(btn, state) {
        const moduleBox = btn.closest('.permission-module-box');
        if (!moduleBox) return;

        const inputs = moduleBox.querySelectorAll('input.toggle-input');
        inputs.forEach(input => {
            if (input.checked !== state) {
                input.checked = state;
                handlePermissionToggle(input);
            }
        });
    };

    // 5. Batch Toggle untuk Seluruh Peran
    window.batchToggleRolePermissions = function(roleId, state) {
        const form = document.getElementById(`role-form-${roleId}`);
        if (!form) return;

        const inputs = form.querySelectorAll('input.toggle-input');
        const autoSaveCheckbox = document.getElementById(`autosave-toggle-${roleId}`);
        const isAutoSave = autoSaveCheckbox ? autoSaveCheckbox.checked : false;

        inputs.forEach(input => {
            input.checked = state;
        });
        updateRoleDisplayCount(roleId);

        if (isAutoSave) {
            submitRoleFormAjax(roleId);
        } else {
            showFloatingSaveBar(`${state ? 'Seluruh izin dinyalakan' : 'Seluruh izin dimatikan'}`);
        }
    };

    // 6. Submit Form Role
    window.submitRoleForm = function(roleId) {
        const form = document.getElementById(`role-form-${roleId}`);
        if (form) {
            submitRoleFormAjax(roleId);
        }
    };

    // AJAX Form Submission
    window.submitRoleFormAjax = async function(roleId) {
        const form = document.getElementById(`role-form-${roleId}`);
        if (!form) return;

        const formData = new FormData(form);
        const submitBtn = document.getElementById(`top-save-btn-${roleId}`);
        const originalText = submitBtn ? submitBtn.innerHTML : '';

        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span>Menyimpan...</span>';
        }

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: formData
            });

            const result = await response.json();
            if (result.success) {
                if (window.notyf) {
                    window.notyf.success(result.message || 'Perubahan matriks izin berhasil disimpan!');
                }
                updateRoleDisplayCount(roleId, result.permission_count);
                hideFloatingSaveBar();
            } else {
                throw new Error(result.message || 'Gagal menyimpan');
            }
        } catch (e) {
            // Fallback: submit biasa jika JSON fetch gagal
            form.submit();
        } finally {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        }
    };

    // 7. Update Real-time Count Badges
    function updateRoleDisplayCount(roleId, directCount = null) {
        const form = document.getElementById(`role-form-${roleId}`);
        const roleCounter = document.getElementById(`role-counter-${roleId}`);
        const tabBadge = document.getElementById(`tab-badge-${roleId}`);

        const count = directCount !== null 
            ? directCount 
            : (form ? form.querySelectorAll('input.toggle-input:checked').length : 0);

        if (roleCounter) {
            roleCounter.textContent = `${count} Izin Terpasang`;
        }
        if (tabBadge) {
            tabBadge.textContent = `${count} Izin`;
            if (count > 0) {
                tabBadge.className = 'inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-emerald-100 text-emerald-800';
            } else {
                tabBadge.className = 'inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-gray-100 text-gray-600';
            }
        }
    }

    // 8. Floating Save Bar Controls
    function showFloatingSaveBar(desc) {
        const bar = document.getElementById('floating-save-bar');
        const descEl = document.getElementById('floating-bar-desc');
        if (!bar) return;

        if (desc && descEl) {
            descEl.textContent = desc;
        }
        bar.classList.remove('translate-y-24', 'opacity-0', 'pointer-events-none');
        bar.classList.add('translate-y-0', 'opacity-100', 'pointer-events-auto');
    }

    window.hideFloatingSaveBar = function() {
        const bar = document.getElementById('floating-save-bar');
        if (!bar) return;
        bar.classList.add('translate-y-24', 'opacity-0', 'pointer-events-none');
        bar.classList.remove('translate-y-0', 'opacity-100', 'pointer-events-auto');
    };

    window.submitActiveRoleForm = function() {
        submitRoleForm(activeRoleId);
    };
</script>
@endpush
