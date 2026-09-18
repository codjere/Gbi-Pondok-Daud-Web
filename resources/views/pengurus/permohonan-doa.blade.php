@extends('layouts.app')

@section('title', 'Permohonan Doa')
@section('breadcrumb', 'Pengurus / Permohonan Doa')

@section('content')
<div class="mx-auto max-w-6xl pattern-dots">
    @include('partials.flash')

    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-xl font-extrabold text-forest dark:text-cyan-400">Permohonan Doa</h2>
            <p class="mt-1 text-sm text-stone-600 dark:text-slate-400">Kelola permohonan doa dari jemaat</p>
        </div>
        <div class="flex gap-2">
            <button onclick="filterStatus('all')" class="rounded-xl bg-gradient-to-r from-forest to-forest-mid px-4 py-2 text-sm font-bold text-white shadow-md hover:shadow-lg transition hover:from-cyan-600 hover:to-sky-600">
                Semua
            </button>
            <button onclick="filterStatus('pending')" class="rounded-xl bg-gradient-to-r from-amber-50 to-orange-50 px-4 py-2 text-sm font-bold text-amber-700 hover:from-amber-100 hover:to-orange-100 dark:from-slate-600 dark:to-slate-500 dark:text-amber-400">
                Pending
            </button>
            <button onclick="filterStatus('diproses')" class="rounded-xl bg-gradient-to-r from-cyan-50 to-sky-50 px-4 py-2 text-sm font-bold text-cyan-700 hover:from-cyan-100 hover:to-sky-100 dark:from-slate-600 dark:to-slate-500 dark:text-cyan-400">
                Diproses
            </button>
            <button onclick="filterStatus('selesai')" class="rounded-xl bg-gradient-to-r from-emerald-50 to-green-50 px-4 py-2 text-sm font-bold text-emerald-700 hover:from-emerald-100 hover:to-green-100 dark:from-slate-600 dark:to-slate-500 dark:text-emerald-400">
                Selesai
            </button>
        </div>
    </div>

    <div class="rounded-2xl bg-white shadow-md ring-1 ring-cyan-200/60 dark:bg-slate-700 dark:ring-slate-600">
        <div class="divide-y divide-cyan-200/50 dark:divide-slate-600">
            @forelse($permohonanDoa as $permohonan)
                <div class="p-6 data-row" data-status="{{ $permohonan->status }}">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1">
                            {{-- Header --}}
                            <div class="mb-3 flex items-center gap-3">
                                @if($permohonan->is_anonymous)
                                    <span class="rounded-full bg-gradient-to-r from-stone-100 to-slate-100 px-3 py-1 text-xs font-bold text-stone-600 dark:from-slate-600 dark:to-slate-500 dark:text-slate-400">
                                        <i class="fa-solid fa-user-secret mr-1"></i> Anonim
                                    </span>
                                @else
                                    <span class="rounded-full bg-gradient-to-r from-gold-soft to-amber-100 px-3 py-1 text-xs font-bold text-gold dark:from-slate-600 dark:to-slate-500 dark:text-amber-400">
                                        <i class="fa-solid fa-user mr-1"></i> {{ $permohonan->user->name }}
                                    </span>
                                @endif
                                
                                <span class="rounded-full bg-gradient-to-r from-{{ $permohonan->statusColor() }}-soft to-{{ $permohonan->statusColor() }}-100 px-3 py-1 text-xs font-bold text-{{ $permohonan->statusColor() }} dark:from-slate-600 dark:to-slate-500 dark:text-{{ $permohonan->statusColor() }}-400">
                                    {{ ucfirst($permohonan->status) }}
                                </span>
                                
                                <span class="text-xs text-stone-500 dark:text-slate-500">
                                    {{ $permohonan->created_at->format('d M Y, H:i') }}
                                </span>
                            </div>

                            {{-- Content --}}
                            <p class="text-sm text-stone-700 leading-relaxed dark:text-slate-300">
                                {{ $permohonan->pokok_doa }}
                            </p>

                            {{-- User Info (if not anonymous) --}}
                            @if(!$permohonan->is_anonymous && $permohonan->user)
                                <div class="mt-3 flex items-center gap-4 text-xs text-stone-600 dark:text-slate-400">
                                    <span><i class="fa-solid fa-envelope mr-1"></i> {{ $permohonan->user->email }}</span>
                                    @if($permohonan->user->profile)
                                        <span><i class="fa-solid fa-phone mr-1"></i> {{ $permohonan->user->profile->nomor_telepon ?? '-' }}</span>
                                    @endif
                                </div>
                            @endif
                        </div>

                        {{-- Actions --}}
                        <div class="flex shrink-0 flex-col gap-2">
                            {{-- Status Update --}}
                            <form action="{{ route('pengurus.permohonan-doa.update', $permohonan) }}" method="POST">
                                @method('PUT')
                                @csrf
                                <select name="status" onchange="this.form.submit()" 
                                        class="rounded-lg bg-gradient-to-r from-cyan-50 to-sky-50 px-3 py-2 text-xs font-semibold text-forest shadow-sm ring-1 ring-cyan-200/60 outline-none focus:ring-2 focus:ring-cyan-500/40 dark:from-slate-600 dark:to-slate-500 dark:text-cyan-400 dark:ring-slate-500">
                                    <option value="pending" {{ $permohonan->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="diproses" {{ $permohonan->status === 'diproses' ? 'selected' : '' }}>Diproses</option>
                                    <option value="selesai" {{ $permohonan->status === 'selesai' ? 'selected' : '' }}>Selesai</option>
                                </select>
                            </form>

                            {{-- Delete Button --}}
                            <form action="{{ route('pengurus.permohonan-doa.destroy', $permohonan) }}" method="POST"
                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus permohonan doa ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded-lg bg-gradient-to-r from-red-50 to-orange-50 px-3 py-2 text-xs font-bold text-red-600 hover:from-red-100 hover:to-orange-100 dark:from-slate-600 dark:to-slate-500 dark:text-red-400">
                                    <i class="fa-solid fa-trash mr-1"></i> Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-10 text-center">
                    <i class="fa-solid fa-hands-praying text-3xl text-stone-300"></i>
                    <p class="mt-3 text-sm text-stone-500">Belum ada permohonan doa</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function filterStatus(status) {
    const rows = document.querySelectorAll('.data-row');
    
    rows.forEach(row => {
        if (status === 'all') {
            row.style.display = 'block';
        } else {
            if (row.dataset.status === status) {
                row.style.display = 'block';
            } else {
                row.style.display = 'none';
            }
        }
    });
}
</script>
@endpush
