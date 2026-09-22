@extends('layouts.partner')

@section('title', 'Contacts | Kingdom Partner')

@section('content')
<div class="flex flex-col h-full max-h-full gap-6">
    <!-- Header -->
    <div class="flex-none flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
        <div>
            <h1 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white tracking-tight">Contacts</h1>
            <p class="text-sm text-slate-500 mt-2">Your dedicated Kingdom Recruitments team contacts. <span class="font-semibold text-slate-400">({{ $contacts->count() }} contacts)</span></p>
        </div>
    </div>

    @if($contacts->count() > 0)
    <!-- Contacts Table -->
    <div class="flex-1 min-h-0 flex flex-col bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="flex-1 overflow-y-auto overflow-x-auto custom-scrollbar partner-table-container">
            <table class="w-full text-left border-collapse min-w-[550px]">
                <thead class="sticky top-0 z-20">
                    <tr class="bg-[#0f1f3d] text-white uppercase tracking-wide font-semibold text-xs">
                        <th class="px-4 py-4 w-1/3">Contact</th>
                        <th class="px-4 py-4 w-1/3">Contact Details</th>
                        <th class="px-4 py-4 text-right w-1/3">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-[#FFFFFF]">
                    @foreach($contacts as $contact)
                    @php
                        $initials = collect(explode(' ', $contact->name))->map(fn($w) => strtoupper(substr($w, 0, 1)))->take(2)->join('');
                        $colors = ['from-blue-400 to-blue-600', 'from-purple-400 to-purple-600', 'from-emerald-400 to-emerald-600', 'from-amber-400 to-amber-600', 'from-kingdom-gold to-amber-600', 'from-red-400 to-red-600'];
                        $colorIdx = crc32($contact->name) % count($colors);
                        $roleLabel = ucfirst($contact->role);
                    @endphp
                    <tr class="border-b border-slate-100 hover:bg-slate-50 transition-colors">
                        <!-- Avatar + Name & Role -->
                        <td class="px-4 py-4 whitespace-nowrap align-middle">
                            <div class="flex items-center gap-4">
                                <x-avatar :user="$contact" class="h-10 w-10 rounded-full shadow-md" />
                                <div>
                                    <h3 class="text-[14px] font-[600] text-[#0F172A]">{{ $contact->name }}</h3>
                                    <p class="text-[13px] text-[#475569]">{{ $roleLabel }}</p>
                                </div>
                            </div>
                        </td>

                        <!-- Contact Details -->
                        <td class="px-4 py-4 whitespace-nowrap align-middle">
                            <div class="flex flex-col gap-1.5">
                                @if($contact->phone)
                                <a href="tel:{{ $contact->phone }}" class="flex items-center gap-2 text-[13px] text-[#475569] hover:text-[#0F1C3F] transition-colors inline-flex w-fit tabular-nums">
                                    <i data-lucide="phone" class="text-[16px] text-[#94A3B8] w-4 h-4"></i>
                                    {{ $contact->phone }}
                                </a>
                                @else
                                <span class="text-[13px] text-slate-300 italic">No phone listed</span>
                                @endif
                                <a href="mailto:{{ $contact->email }}" class="flex items-center gap-2 text-[13px] text-[#475569] hover:text-[#0F1C3F] transition-colors inline-flex w-fit">
                                    <i data-lucide="mail" class="text-[16px] text-[#94A3B8] w-4 h-4"></i>
                                    {{ $contact->email }}
                                </a>
                            </div>
                        </td>

                        <!-- Actions -->
                        <td class="px-4 py-4 whitespace-nowrap text-right align-middle">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('partner.messages.new', $contact->id) }}" class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 bg-[#0F1D33] hover:bg-slate-800 text-white rounded-[8px] text-[11px] font-[600] transition-all shadow-sm">
                                    <i data-lucide="message-circle" class="text-[16px] w-4 h-4"></i>
                                    Chat
                                </a>
                                <a href="mailto:{{ $contact->email }}" class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 bg-[#FFFFFF] hover:bg-[#EEF2F7] text-[#475569] hover:text-[#0F1C3F] rounded-[8px] text-[11px] font-[500] transition-colors border border-[#E2E8F0]">
                                    <i data-lucide="mail" class="text-[16px] w-4 h-4"></i>
                                    Email
                                </a>
                                @if($contact->phone)
                                <a href="tel:{{ $contact->phone }}" class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 bg-[#FFFFFF] hover:bg-[#D1FAE5] text-[#475569] hover:text-[#059669] rounded-[8px] text-[11px] font-[500] transition-colors border border-[#E2E8F0]">
                                    <i data-lucide="phone" class="text-[16px] w-4 h-4"></i>
                                    Call
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @else
    <!-- Empty State -->
    <div class="flex-1 flex items-center justify-center">
        <div class="text-center p-8">
            <div class="w-20 h-20 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto mb-4">
                <i data-lucide="users" class="w-10 h-10 text-slate-300"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-900 mb-2">No Contacts Available</h3>
            <p class="text-sm text-slate-500 max-w-sm mx-auto">Your Kingdom Recruitments team contacts will appear here once they are set up by the administrator.</p>
        </div>
    </div>
    @endif
</div>
@endsection
