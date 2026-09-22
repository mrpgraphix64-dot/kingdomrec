@extends('layouts.partner')

@section('title', 'Quotations | Kingdom Partner')

@section('content')
<div class="flex flex-col h-full max-h-full gap-6" x-data="quotationFilter()">
    <!-- Header -->
    <div class="flex-none flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white tracking-tight">Quotations</h1>
            <p class="text-sm text-slate-500 mt-2">View, manage, and track your staff bookings.</p>
        </div>
        <div class="flex items-center gap-3 w-full md:w-auto">
            <!-- Search Bar -->
            <div class="relative flex-1 md:flex-none">
                <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                <input type="text" x-model="search" placeholder="Search bookings..."
                    class="pl-9 pr-4 py-2 w-full md:w-60 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-kingdom-gold focus:ring-2 focus:ring-kingdom-gold/10 transition-all">
                <button x-show="search" @click="search = ''" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                    <i data-lucide="x" class="w-3.5 h-3.5"></i>
                </button>
            </div>
            <!-- Status Filter -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" @click.outside="open = false"
                    class="flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 rounded-xl text-sm font-semibold text-slate-600 hover:border-slate-300 hover:bg-slate-50 transition-all">
                    <i data-lucide="filter" class="w-4 h-4"></i>
                    <span x-text="statusFilter || 'All Status'"></span>
                    <i data-lucide="chevron-down" class="w-3.5 h-3.5" :class="{ 'rotate-180': open }"></i>
                </button>
                <div x-show="open" x-transition x-cloak class="absolute right-0 mt-1 z-30 w-40 bg-white border border-slate-200 rounded-xl shadow-xl py-1 overflow-hidden" style="display:none;">
                    <button @click="statusFilter = ''; open = false" class="w-full text-left px-4 py-2.5 text-sm hover:bg-slate-50 text-slate-600" :class="statusFilter === '' ? 'font-bold text-kingdom-gold' : ''">All Status</button>
                    @foreach(['Pending','Draft','Accepted','Approved','Confirmed','Expired','Cancelled'] as $s)
                    <button @click="statusFilter = '{{ $s }}'; open = false" class="w-full text-left px-4 py-2.5 text-sm hover:bg-slate-50 text-slate-600" :class="statusFilter === '{{ $s }}' ? 'font-bold text-kingdom-gold' : ''">{{ $s }}</button>
                    @endforeach
                </div>
            </div>
            <a href="{{ route('partner.book-staff') }}" class="flex items-center gap-2 bg-kingdom-gold hover:bg-yellow-600 text-white px-5 py-2 rounded-xl font-bold text-sm transition-all shadow-lg shadow-kingdom-gold/20 whitespace-nowrap">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                New Booking
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="flex-none bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-4 rounded-xl text-sm font-medium flex items-center gap-3">
            <i data-lucide="check-circle" class="text-[20px] w-5 h-5"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="flex-none bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-xl text-sm font-medium flex items-center gap-3">
            <i data-lucide="alert-circle" class="text-[20px] w-5 h-5"></i>
            {{ session('error') }}
        </div>
    @endif

    <!-- Summary Row -->
    @php
        $pendingCount = $eventBookings->whereIn('status', ['Pending', 'Draft'])->count();
        $acceptedCount = $eventBookings->whereIn('status', ['Accepted', 'Approved', 'Confirmed'])->count();
        $totalValue = $eventBookings->sum(function($eb) {
            $amount = preg_replace('/[^0-9.]/', '', $eb->total_amount ?? '0');
            return floatval($amount);
        });
    @endphp
    <div class="flex-none grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="card-interactive bg-gradient-to-br from-[#0F1D33] to-[#1a2b4c] rounded-[14px] p-4 text-white relative overflow-hidden">
            <div class="absolute -right-6 -top-6 w-24 h-24 bg-kingdom-gold/10 rounded-full blur-2xl"></div>
            <p class="text-[10px] uppercase tracking-widest text-kingdom-gold font-bold mb-1">Pending Review</p>
            <p class="text-2xl font-black">{{ $pendingCount }}</p>
        </div>
        <div class="card-interactive bg-white dark:bg-slate-900 rounded-[14px] p-4 border border-slate-100 dark:border-slate-800 shadow-sm">
            <p class="text-[10px] uppercase tracking-widest text-emerald-500 font-bold mb-1">Accepted</p>
            <p class="text-2xl font-black text-slate-900 dark:text-white">{{ $acceptedCount }}</p>
        </div>
        <div class="card-interactive bg-white dark:bg-slate-900 rounded-[14px] p-4 border border-slate-100 dark:border-slate-800 shadow-sm">
            <p class="text-[10px] uppercase tracking-widest text-slate-400 font-bold mb-1">Total Value</p>
            <p class="text-2xl font-black text-slate-900 dark:text-white">£{{ number_format($totalValue, 0) }}</p>
        </div>
    </div>

    @if($eventBookings->count() > 0 || $orphanedQuotations->count() > 0)
    <!-- Event Booking Cards -->
    <div class="flex-1 min-h-0 overflow-y-auto custom-scrollbar space-y-4 pr-2 pb-4">
        <!-- No results state -->
        <div x-show="visibleCount === 0" class="bg-white rounded-2xl border border-slate-100 shadow-sm p-12 text-center h-full flex flex-col items-center justify-center" x-cloak>
            <div class="flex flex-col items-center gap-3">
                <div class="w-14 h-14 rounded-full bg-slate-100 flex items-center justify-center">
                    <i data-lucide="search-x" class="w-6 h-6 text-slate-400"></i>
                </div>
                <h3 class="font-bold text-slate-700">No bookings match your search</h3>
                <button @click="search = ''; statusFilter = ''" class="text-kingdom-gold font-bold text-sm hover:underline">Clear filters</button>
            </div>
        </div>
        
        @foreach($eventBookings as $eb)
        @php
            $statusStyles = [
                'Pending' => ['bg' => 'bg-[#FFFBEB] text-[#D97706]', 'dot' => 'bg-[#F59E0B]'],
                'Draft' => ['bg' => 'bg-[#F8FAFC] text-[#64748B]', 'dot' => 'bg-[#94A3B8]'],
                'Accepted' => ['bg' => 'bg-[#ECFDF5] text-[#059669]', 'dot' => 'bg-[#10B981]'],
                'Approved' => ['bg' => 'bg-[#ECFDF5] text-[#059669]', 'dot' => 'bg-[#10B981]'],
                'Confirmed' => ['bg' => 'bg-[#ECFDF5] text-[#059669]', 'dot' => 'bg-[#10B981]'],
                'Expired' => ['bg' => 'bg-[#F8FAFC] text-[#475569]', 'dot' => 'bg-[#94A3B8]'],
                'Cancelled' => ['bg' => 'bg-[#FEF2F2] text-[#DC2626]', 'dot' => 'bg-[#EF4444]'],
            ];
            $style = $statusStyles[$eb->status] ?? ['bg' => 'bg-slate-100 text-slate-500', 'dot' => 'bg-slate-400'];
            $shiftCount = $eb->shifts->count();
            $totalVal = preg_replace('/[^0-9.]/', '', $eb->total_amount ?? '0');
        @endphp
        <div class="group bg-[#FFFFFF] rounded-[14px] border border-[#E2E8F0] shadow-[0_3px_10px_rgba(0,0,0,0.05)] hover:shadow-[0_6px_15px_rgba(0,0,0,0.08)] transition-all duration-300 overflow-hidden"
             x-data="{ expanded: false }"
             x-show="matchesQuotation('{{ addslashes($eb->booking_ref ?? '') }}', '{{ addslashes($eb->event_name ?? '') }}', '{{ addslashes($eb->client ?? '') }}', '{{ $eb->status }}')">
            
            <!-- Event Header (Always Visible) -->
            <div class="p-4 flex flex-col lg:flex-row lg:items-center gap-4 cursor-pointer" @click="expanded = !expanded">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-2 flex-wrap">
                        <span class="px-2 py-1 bg-[#0F1C3F] text-[#FFFFFF] rounded-[6px] text-[10px] font-bold tracking-wide uppercase">{{ $eb->booking_ref ?? 'N/A' }}</span>
                        <span class="flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase {{ $style['bg'] }}">
                            <span class="w-1.5 h-1.5 rounded-full opacity-80 {{ $style['dot'] }}"></span>
                            {{ $eb->status }}
                        </span>
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-indigo-50 text-indigo-600">
                            {{ $shiftCount }} {{ Str::plural('Shift', $shiftCount) }}
                        </span>
                        @if($eb->isEditable())
                            <span class="flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] uppercase font-bold bg-[#EFF6FF] text-[#2563EB]">
                                <i data-lucide="pencil" class="w-3.5 h-3.5"></i> Editable
                            </span>
                        @endif
                    </div>
                    <h3 class="text-[14px] font-[600] text-[#0F172A] group-hover:text-[#0F1C3F] transition-colors">{{ $eb->event_name }}</h3>
                    <p class="text-[12px] text-[#64748B] mt-1">
                        {{ $eb->client }} · {{ $eb->start_date?->format('M d, Y') ?? $eb->created_at?->format('M d, Y') }}
                        @if($eb->venue) · {{ $eb->venue }} @endif
                    </p>
                </div>

                <div class="flex items-center gap-6">
                    <div class="text-center">
                        <p class="text-[9px] uppercase tracking-widest text-slate-400 font-bold mb-1">Total</p>
                        <p class="text-[15px] font-black text-kingdom-gold tabular-nums">{{ $eb->total_amount ?? '£0.00' }}</p>
                    </div>
                    <!-- Actions -->
                    <div class="flex items-center gap-2" @click.stop>
                        @if($eb->isEditable())
                            <a href="{{ route('partner.booking.edit', $eb->id) }}" class="px-3 py-1.5 bg-[#FFFFFF] hover:bg-[#F1F5F9] border border-[#E2E8F0] text-[#475569] hover:text-[#0F1C3F] rounded-[8px] font-bold text-[12px] transition-colors whitespace-nowrap flex items-center gap-1.5">
                                <i data-lucide="pencil" class="w-4 h-4"></i> Edit
                            </a>
                            <form action="{{ route('partner.booking.destroy', $eb->id) }}" method="POST" class="m-0" x-data @submit.prevent="$dispatch('open-confirm-modal', { title: 'Delete Event', message: 'Delete this event and all its shifts? This cannot be undone.', onConfirm: () => submitDeleteForm('{{ route('partner.booking.destroy', $eb->id) }}') })">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-1.5 bg-[#FFFFFF] hover:bg-red-50 border border-[#E2E8F0] hover:border-red-200 text-slate-400 hover:text-red-500 rounded-[8px] transition-colors flex items-center justify-center" title="Delete Event">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>
                        @elseif($eb->status === 'Pending')
                            <a href="{{ route('partner.messages') }}" class="px-3 py-1.5 bg-[#FFFFFF] hover:bg-[#F1F5F9] border border-[#E2E8F0] text-[#475569] rounded-[8px] font-bold text-[12px] transition-colors flex items-center gap-1.5">
                                <i data-lucide="message-square" class="w-4 h-4"></i> Contact
                            </a>
                        @elseif(in_array($eb->status, ['Accepted', 'Approved', 'Confirmed']))
                            <span class="px-3 py-1.5 bg-[#ECFDF5] text-[#059669] rounded-[8px] font-bold text-[12px] flex items-center gap-1.5 border border-[#A7F3D0]">
                                <i data-lucide="check-circle" class="w-4 h-4"></i> Confirmed
                            </span>
                        @endif
                    </div>
                    <!-- Expand Toggle -->
                    <button class="p-2 text-slate-400 hover:text-slate-700 rounded-lg transition-transform duration-200" :class="expanded ? 'rotate-180' : ''">
                        <i data-lucide="chevron-down" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>

            <!-- Expandable Shifts Accordion -->
            <div x-show="expanded" x-collapse x-cloak>
                <div class="border-t border-slate-100 bg-slate-50/50">
                    @forelse($eb->shifts as $shift)
                    <div class="px-5 py-3 flex flex-col sm:flex-row sm:items-center justify-between gap-3 {{ !$loop->last ? 'border-b border-slate-100' : '' }}">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-kingdom-gold/10 flex items-center justify-center">
                                <i data-lucide="briefcase" class="w-4 h-4 text-kingdom-gold"></i>
                            </div>
                            <div>
                                <p class="text-[13px] font-semibold text-slate-800">{{ $shift->sub_category }}</p>
                                <p class="text-[11px] text-slate-400">{{ $shift->shift_label ?? 'Standard' }} · {{ $shift->start_time ?? '—' }} – {{ $shift->end_time ?? '—' }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-5 text-center">
                            <div><p class="text-[9px] uppercase text-slate-400 font-bold">Qty</p><p class="text-[13px] font-black text-slate-900">{{ $shift->quantity }}</p></div>
                            <div><p class="text-[9px] uppercase text-slate-400 font-bold">Hours</p><p class="text-[13px] font-bold text-slate-700">{{ intval($shift->shift_hours ?? 8) }}h</p></div>
                            <div><p class="text-[9px] uppercase text-slate-400 font-bold">Rate</p><p class="text-[13px] font-bold text-slate-700">£{{ number_format($shift->rate ?? 0, 2) }}/hr</p></div>
                            <div><p class="text-[9px] uppercase text-slate-400 font-bold">Subtotal</p><p class="text-[13px] font-black text-kingdom-gold">{{ $shift->amount }}</p></div>
                        </div>
                    </div>
                    @empty
                    <div class="px-5 py-4 text-center text-sm text-slate-400">No shifts configured for this event.</div>
                    @endforelse
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <!-- Empty State -->
    <div class="flex-1 min-h-0 bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm p-12 text-center flex flex-col items-center justify-center mb-0">
        <div class="flex flex-col items-center gap-4">
            <div class="w-16 h-16 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center">
                <i data-lucide="file-text" class="text-3xl text-slate-400 w-5 h-5"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-700 dark:text-slate-300">No Bookings Yet</h3>
            <p class="text-sm text-slate-400 max-w-md">You haven't made any staff bookings. Create your first booking to get started.</p>
            <a href="{{ route('partner.book-staff') }}" class="mt-2 px-6 py-3 bg-kingdom-gold hover:bg-yellow-600 text-white rounded-xl font-bold text-sm transition-all shadow-lg shadow-kingdom-gold/20">
                <span class="flex items-center gap-2">
                    <i data-lucide="plus-circle" class="w-5 h-5"></i>
                    Create Booking
                </span>
            </a>
        </div>
    </div>
    @endif

<script>
window.quotationFilter = function quotationFilter() {
    return {
        search: '',
        statusFilter: '',
        get visibleCount() {
            return document.querySelectorAll('[x-show].group').length;
        },
        matchesQuotation(ref, eventName, client, status) {
            const q = this.search.toLowerCase();
            const matchText = !q ||
                ref.toLowerCase().includes(q) ||
                eventName.toLowerCase().includes(q) ||
                client.toLowerCase().includes(q) ||
                status.toLowerCase().includes(q);
            const matchStatus = !this.statusFilter || status === this.statusFilter;
            return matchText && matchStatus;
        }
    };
}
</script>

    <!-- Cancellation Policy Footer -->
    <div class="flex-none bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-2xl p-4 transition-all duration-300" x-data="{ policyExpanded: false }">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
            <!-- Main visible line -->
            <div class="flex flex-col md:flex-row md:items-center gap-3">
                <h4 class="text-sm font-bold text-amber-800 dark:text-amber-300 flex items-center gap-2 whitespace-nowrap">
                    <i data-lucide="gavel" class="text-[18px] w-5 h-5"></i>
                    Cancellation & Update Policy
                </h4>
                <div class="hidden md:block w-px h-4 bg-amber-300 dark:bg-amber-700"></div>
                <div class="flex items-center gap-2 text-xs text-amber-700 dark:text-amber-400">
                    <i data-lucide="alert-triangle" class="text-[14px] text-red-500 w-4 h-4"></i>
                    <span>Cancellations within <strong>12 hours</strong> require <strong>minimum 50% payment</strong> of staff salary.</span>
                </div>
            </div>
            
            <button @click="policyExpanded = !policyExpanded" class="text-xs font-bold text-amber-600 dark:text-amber-400 hover:text-amber-800 dark:hover:text-amber-200 flex items-center gap-1 transition-colors whitespace-nowrap mr-16 lg:mr-20">
                <span x-text="policyExpanded ? 'Hide Details' : 'Read More'"></span>
                <i data-lucide="chevron-down" class="w-4 h-4 transition-transform duration-300" :class="{ 'rotate-180': policyExpanded }"></i>
            </button>
        </div>

        <div x-show="policyExpanded" x-collapse x-cloak>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs text-amber-700 dark:text-amber-400 mt-4 pt-4 border-t border-amber-200 dark:border-amber-800/50">
                <div class="flex items-start gap-2">
                    <i data-lucide="calendar-clock" class="text-[14px] mt-0.5 text-amber-500 w-4 h-4 flex-shrink-0"></i>
                    <span>Client can <strong>update or cancel up to 24 hours</strong> before the event.</span>
                </div>
                <div class="flex items-start gap-2">
                    <i data-lucide="handshake" class="text-[14px] mt-0.5 text-amber-500 w-4 h-4 flex-shrink-0"></i>
                    <span>If mutually agreed, cancellation allowed up to <strong>12 hours before</strong>.</span>
                </div>
                <div class="flex items-start gap-2">
                    <i data-lucide="help-circle" class="text-[14px] mt-0.5 text-blue-500 w-4 h-4 flex-shrink-0"></i>
                    <span>Contact admin via <a href="{{ route('partner.messages') }}" class="text-kingdom-gold font-bold hover:underline">Notes & Messages</a> for post-window changes.</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
