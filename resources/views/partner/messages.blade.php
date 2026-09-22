@extends('layouts.partner')

@section('title', 'Notes & Messages | Kingdom Partner')

@section('content')
<div class="flex flex-col h-full max-h-full gap-6 overflow-hidden" x-data="{ showNewChat: false }">
    <!-- Header -->
    <div class="flex-none flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
        <div>
            <h1 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white tracking-tight">Notes & Messages</h1>
            <p class="text-sm text-slate-500 mt-2">Communicate with your Kingdom Recruitments team.</p>
        </div>
        <button @click="showNewChat = !showNewChat" class="flex items-center gap-2 px-5 py-2.5 bg-[#0F1D33] hover:bg-slate-800 text-white rounded-xl shadow-sm font-bold text-sm transition-all">
            <i data-lucide="plus" class="w-4 h-4"></i> New Chat
        </button>
    </div>

    <!-- New Chat Panel (Slide Down) -->
    <div x-show="showNewChat" x-collapse x-cloak class="flex-none bg-white dark:bg-slate-900 rounded-2xl border border-[#E6EAF0] dark:border-slate-800 shadow-md p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <i data-lucide="users" class="w-4 h-4 text-kingdom-gold"></i> Start a conversation with a team member
            </h3>
            <button @click="showNewChat = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
            @foreach($teamContacts as $tc)
            @php
                $tcInitials = collect(explode(' ', $tc->name))->map(fn($w) => strtoupper(substr($w, 0, 1)))->take(2)->join('');
                $tcColors = ['from-blue-400 to-blue-600', 'from-purple-400 to-purple-600', 'from-emerald-400 to-emerald-600', 'from-amber-400 to-amber-600', 'from-kingdom-gold to-amber-600'];
                $tcColorIdx = crc32($tc->name) % count($tcColors);
            @endphp
            <a href="{{ route('partner.messages.new', $tc->id) }}" 
               class="flex items-center gap-3 p-3 rounded-xl border border-slate-100 hover:border-kingdom-gold/30 hover:bg-kingdom-gold/5 transition-all group">
                <x-avatar :user="$tc" class="h-9 w-9 rounded-full shadow-sm" />
                <div class="min-w-0">
                    <p class="text-[13px] font-bold text-slate-900 truncate group-hover:text-kingdom-gold transition-colors">{{ $tc->name }}</p>
                    <p class="text-[10px] text-slate-400 uppercase font-bold tracking-wider">{{ ucfirst($tc->role) }}</p>
                </div>
            </a>
            @endforeach
        </div>
    </div>

    @if($conversations->count() > 0)
    <!-- Messaging Interface -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-[#E6EAF0] dark:border-slate-800 shadow-[0_4px_20px_rgba(0,0,0,0.03)] overflow-hidden flex flex-1 min-h-0">
        
        <!-- Left: Conversation List -->
        <div class="w-80 border-r border-[#E6EAF0] dark:border-slate-800 flex flex-col flex-shrink-0">
            <!-- Search -->
            <div class="p-4 border-b border-[#E6EAF0] dark:border-slate-800 flex-shrink-0">
                <div class="flex items-center gap-2 bg-[#F6F8FB] dark:bg-slate-800 rounded-xl px-3 py-2.5 border border-transparent focus-within:border-[#E6EAF0] dark:focus-within:border-slate-700 transition-colors">
                    <i data-lucide="search" class="text-[18px] text-slate-400 w-5 h-5"></i>
                    <input type="text" placeholder="Search messages..." class="bg-transparent border-none outline-none text-sm text-slate-700 dark:text-slate-300 w-full placeholder-slate-400 focus:ring-0 p-0">
                </div>
            </div>
            
            <!-- Conversations -->
            <div class="flex-1 overflow-y-auto min-h-0 custom-scrollbar">
                @foreach($conversations as $convo)
                @php
                    $convoDisplayName = $convo['contact_name'] ?? 'Conversation';
                    $convoInitials = collect(explode(' ', $convoDisplayName))->map(fn($w) => strtoupper(substr($w, 0, 1)))->take(2)->join('');
                    $isDirect = str_starts_with($convo['conversation_id'], 'direct-');
                @endphp
                <a href="{{ route('partner.messages', ['conversation' => $convo['conversation_id']]) }}" 
                   class="block px-4 py-3 flex items-center gap-3 cursor-pointer transition-colors hover:bg-[#F6F8FB] dark:hover:bg-slate-800/50 {{ ($activeConversationId == $convo['conversation_id']) ? 'bg-kingdom-gold/5 border-l-4 border-kingdom-gold pr-3' : 'border-l-4 border-transparent' }}">
                    <div class="h-9 w-9 rounded-full flex items-center justify-center text-white text-[12px] font-black shadow-md flex-shrink-0 border-2 border-white dark:border-slate-900 relative {{ $isDirect ? 'bg-gradient-to-br from-blue-400 to-blue-600' : 'bg-gradient-to-br from-slate-400 to-slate-600' }}">
                        @if($isDirect)
                        {{ $convoInitials }}
                        @else
                        <i data-lucide="message-square" class="w-4 h-4"></i>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex justify-between items-center mb-0.5">
                            <p class="text-[12px] font-bold text-slate-900 dark:text-white truncate">
                                {{ $convoDisplayName }}
                            </p>
                            <span class="text-[9px] font-bold {{ $convo['unread'] > 0 ? 'text-kingdom-gold' : 'text-slate-400' }} flex-shrink-0">{{ $convo['time'] }}</span>
                        </div>
                        <p class="text-[10px] text-slate-500 truncate">{{ Str::limit($convo['last_message'], 50) }}</p>
                    </div>
                    @if($convo['unread'] > 0)
                    <span class="h-4 w-4 rounded-full bg-kingdom-gold text-white text-[9px] font-bold flex items-center justify-center flex-shrink-0 shadow-sm shadow-kingdom-gold/30">{{ $convo['unread'] }}</span>
                    @endif
                </a>
                <div class="mx-5 h-px bg-[#F1F5F9] dark:bg-slate-800"></div>
                @endforeach
            </div>
        </div>

        <!-- Right: Chat Window -->
        <div class="flex-1 flex flex-col min-w-0 bg-[#F8FAFC] dark:bg-[#0A1120]">
            <!-- Chat Header -->
            @if($activeContactName)
            <div class="flex-shrink-0 px-4 py-3 bg-white dark:bg-slate-900 border-b border-[#E6EAF0] dark:border-slate-800 flex items-center gap-3">
                @php
                    $activeInitials = collect(explode(' ', $activeContactName))->map(fn($w) => strtoupper(substr($w, 0, 1)))->take(2)->join('');
                @endphp
                <div class="h-8 w-8 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white text-[11px] font-bold shadow-sm">
                    {{ $activeInitials }}
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-900 dark:text-white">{{ $activeContactName }}</p>
                    <p class="text-[9px] text-emerald-500 font-bold flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block"></span> Kingdom Team</p>
                </div>
            </div>
            @endif
            
            @if($activeMessages->count() > 0)
            <!-- Messages Area (Scrollable) -->
            <div class="flex-1 overflow-y-auto p-4 md:p-6 space-y-4 custom-scrollbar">
                @foreach($activeMessages as $msg)
                @if($msg->admin_reply)
                <!-- Admin Reply -->
                <div class="flex items-end gap-3 max-w-[85%] group">
                    <div class="h-7 w-7 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white text-[9px] font-black flex-shrink-0 shadow-md mb-4">
                        KR
                    </div>
                    <div class="flex flex-col gap-1 items-start">
                        <div class="flex items-center gap-2 px-1">
                            <span class="text-[10px] font-bold text-slate-700 dark:text-slate-300">{{ $activeContactName ?? 'Admin' }}</span>
                            <span class="text-[9px] font-semibold text-slate-400">{{ $msg->replied_at ? $msg->replied_at->format('h:i A') : '' }}</span>
                        </div>
                        <div class="bg-white dark:bg-slate-800 rounded-2xl rounded-bl-sm px-3 py-2.5 shadow-sm border border-[#E6EAF0] dark:border-slate-700">
                            <p class="text-[12px] text-slate-700 dark:text-slate-300 leading-relaxed">{{ $msg->admin_reply }}</p>
                        </div>
                    </div>
                </div>
                @endif
                @if(trim($msg->message))
                <!-- Partner Message -->
                <div class="flex items-end gap-3 max-w-[85%] ml-auto flex-row-reverse group">
                    <div class="h-7 w-7 rounded-full bg-gradient-to-br from-kingdom-gold to-yellow-600 flex items-center justify-center text-white text-[9px] font-black flex-shrink-0 shadow-md mb-4">
                        You
                    </div>
                    <div class="flex flex-col gap-1 items-end">
                        <div class="flex items-center gap-2 px-1">
                            <span class="text-[9px] font-semibold text-slate-400">{{ $msg->created_at->format('h:i A') }}</span>
                            <span class="text-[10px] font-bold text-slate-700 dark:text-slate-300">You</span>
                        </div>
                        <div class="bg-[#0F1D33] rounded-2xl rounded-br-sm px-3 py-2.5 shadow-[0_2px_8px_rgba(15,29,51,0.2)]">
                            <p class="text-[12px] text-white leading-relaxed">{{ $msg->message }}</p>
                        </div>
                    </div>
                </div>
                @endif
                @endforeach
            </div>
            @else
            <div class="flex-1 flex items-center justify-center">
                <div class="text-center">
                    <div class="w-16 h-16 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto mb-3">
                        <i data-lucide="message-square" class="w-8 h-8 text-slate-300"></i>
                    </div>
                    <p class="text-sm text-slate-400 font-medium">{{ $activeContactName ? 'Send a message to start the conversation' : 'Select a conversation to view messages' }}</p>
                </div>
            </div>
            @endif
            
            <!-- Message Input -->
            <div class="p-4 md:p-6 bg-white dark:bg-slate-900 border-t border-[#E6EAF0] dark:border-slate-800 flex-shrink-0 z-10 shadow-[0_-4px_10px_rgba(0,0,0,0.02)]">
                <form method="POST" action="{{ route('partner.messages.store') }}">
                    @csrf
                    <input type="hidden" name="conversation_id" value="{{ $activeConversationId }}">
                    <div class="flex items-end gap-3 bg-[#F8FAFC] dark:bg-slate-800 p-2 rounded-2xl border border-[#E6EAF0] dark:border-slate-700 focus-within:border-kingdom-gold transition-all shadow-inner">
                        <div class="flex-1 w-full relative min-h-[44px]">
                            <textarea name="message" placeholder="Type your message here..." rows="1" required 
                                @keydown.enter="if(!$event.shiftKey) { $event.preventDefault(); $el.closest('form').submit(); }"
                                class="w-full bg-transparent border-none outline-none text-[14px] text-slate-700 dark:text-slate-300 placeholder-slate-400 resize-none overflow-hidden pt-3 focus:ring-0"></textarea>
                        </div>
                        <div class="pb-1 pr-1">
                            <button type="submit" class="h-10 w-10 flex items-center justify-center bg-gradient-to-br from-[#0F1D33] to-[#1a2b4c] hover:from-kingdom-gold hover:to-yellow-600 rounded-xl text-white transition-all shadow-md group">
                                <i data-lucide="send" class="text-[18px] group-hover:-translate-y-0.5 group-hover:translate-x-0.5 transition-transform w-5 h-5"></i>
                            </button>
                        </div>
                    </div>
                </form>
                <div class="flex items-center justify-between mt-3 px-2">
                    <p class="text-[10px] font-semibold text-slate-400 flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shadow-[0_0_5px_rgba(16,185,129,0.5)]"></span> Secure Connection</p>
                    <p class="text-[10px] font-semibold text-slate-400">Press <kbd class="px-1.5 py-0.5 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded text-slate-500 font-mono">Enter</kbd> to send</p>
                </div>
            </div>
        </div>
    </div>
    @else
    <!-- Empty State -->
    <div class="flex-1 flex items-center justify-center bg-white dark:bg-slate-900 rounded-2xl border border-[#E6EAF0] dark:border-slate-800 shadow-[0_4px_20px_rgba(0,0,0,0.03)]">
        <div class="text-center p-8">
            <div class="w-20 h-20 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto mb-4">
                <i data-lucide="message-square-dashed" class="w-10 h-10 text-slate-300"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">No Messages Yet</h3>
            <p class="text-sm text-slate-500 max-w-sm mx-auto mb-6">Start a conversation with your Kingdom Recruitments team. Click "New Chat" above or visit the contacts page.</p>
            <button @click="showNewChat = true" class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#0F1D33] hover:bg-slate-800 text-white rounded-xl font-bold text-sm shadow-sm transition-all">
                <i data-lucide="plus" class="w-4 h-4"></i>
                Start New Chat
            </button>
        </div>
    </div>
    @endif
</div>
@endsection
