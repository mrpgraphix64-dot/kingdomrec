@extends('layouts.admin')

@section('title', 'Chat Support | Kingdom Admin')

@section('content')
<div class="flex flex-col h-[calc(100vh-8rem)] max-h-full gap-6 overflow-hidden">
    <!-- Header -->
    <div class="flex-none flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
        <div>
            <h1 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white tracking-tight">Chat Support</h1>
            <p class="text-sm text-slate-500 mt-2">Manage incoming messages from partners, staff, and visitors.</p>
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
                    <input type="text" placeholder="Search conversations..." class="bg-transparent border-none outline-none text-sm text-slate-700 dark:text-slate-300 w-full placeholder-slate-400 focus:ring-0 p-0">
                </div>
            </div>
            
            <!-- Conversations -->
            <div class="flex-1 overflow-y-auto min-h-0 custom-scrollbar">
                @foreach($conversations as $convo)
                @php
                    $convoDisplayName = $convo['contact_name'] ?? 'Guest';
                    $convoInitials = collect(explode(' ', $convoDisplayName))->map(fn($w) => strtoupper(substr($w, 0, 1)))->take(2)->join('');
                    $isDirect = $convo['is_direct'];
                @endphp
                <a href="{{ route('admin.chat-support', ['conversation' => $convo['conversation_id']]) }}" 
                   class="block px-5 py-4 flex items-center gap-3 cursor-pointer transition-colors hover:bg-[#F6F8FB] dark:hover:bg-slate-800/50 {{ ($activeConversationId == $convo['conversation_id']) ? 'bg-blue-50/50 border-l-4 border-blue-500 pr-4' : 'border-l-4 border-transparent' }}">
                    <div class="h-10 w-10 rounded-full flex items-center justify-center text-white text-[13px] font-black shadow-md flex-shrink-0 border-2 border-white dark:border-slate-900 relative {{ $isDirect ? 'bg-gradient-to-br from-kingdom-gold to-yellow-600' : 'bg-gradient-to-br from-slate-400 to-slate-600' }}">
                        @if($isDirect)
                        {{ $convoInitials }}
                        @else
                        <i data-lucide="message-square" class="w-4 h-4"></i>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex justify-between items-center mb-0.5">
                            <p class="text-[13px] font-bold text-slate-900 dark:text-white truncate flex items-center gap-1.5">
                                {{ $convoDisplayName }}
                                @if($convo['unread'] > 0)
                                    <span class="w-2 h-2 rounded-full bg-red-500 inline-block shrink-0"></span>
                                @endif
                            </p>
                            <span class="text-[10px] font-bold text-slate-400 flex-shrink-0">{{ $convo['time'] }}</span>
                        </div>
                        <p class="text-[11px] text-slate-500 truncate">{{ Str::limit($convo['last_message'], 50) }}</p>
                    </div>
                </a>
                <div class="mx-5 h-px bg-[#F1F5F9] dark:bg-slate-800"></div>
                @endforeach
            </div>
        </div>

        <!-- Right: Chat Window -->
        <div class="flex-1 flex flex-col min-w-0 bg-[#F8FAFC] dark:bg-[#0A1120]">
            <!-- Chat Header -->
            @if($activeContactName)
            <div class="flex-shrink-0 px-6 py-4 bg-white dark:bg-slate-900 border-b border-[#E6EAF0] dark:border-slate-800 flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    @php
                        $activeInitials = collect(explode(' ', $activeContactName))->map(fn($w) => strtoupper(substr($w, 0, 1)))->take(2)->join('');
                    @endphp
                    <div class="h-9 w-9 rounded-full bg-gradient-to-br from-slate-400 to-slate-600 flex items-center justify-center text-white text-[11px] font-bold shadow-sm">
                        {{ $activeInitials }}
                    </div>
                    <div>
                        <p class="text-sm font-bold text-slate-900 dark:text-white">{{ $activeContactName }}</p>
                        <p class="text-[10px] text-emerald-500 font-bold flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block"></span> Chatting</p>
                    </div>
                </div>

                <!-- Resolve form -->
                @if($activeMessages->count() > 0 && $activeMessages->last()->status !== 'resolved')
                <form action="{{ route('admin.chat-support.resolve', $activeMessages->last()->id) }}" method="POST">
                    @csrf @method('PUT')
                    <button type="submit" class="flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-600 rounded-lg text-[11px] font-bold transition-colors">
                        <i data-lucide="check-circle-2" class="w-3.5 h-3.5"></i> Mark Resolved
                    </button>
                </form>
                @endif
            </div>
            @endif
            
            @if($activeMessages->count() > 0)
            <!-- Messages Area (Scrollable) -->
            <div class="flex-1 overflow-y-auto p-6 md:p-8 space-y-6 custom-scrollbar flex flex-col pt-auto">
                @foreach($activeMessages as $msg)
                
                @if(trim($msg->message))
                <!-- User/Partner Message -->
                <div class="flex items-end gap-3 max-w-[85%] group">
                    <div class="h-8 w-8 rounded-full bg-gradient-to-br from-slate-200 to-slate-300 flex items-center justify-center text-slate-600 text-[10px] font-black flex-shrink-0 shadow-sm mb-5">
                        <i data-lucide="user" class="w-4 h-4"></i>
                    </div>
                    <div class="flex flex-col gap-1 items-start">
                        <div class="flex items-center gap-2 px-1">
                            <span class="text-[11px] font-bold text-slate-700 dark:text-slate-300">{{ $msg->visitor_name ?? 'User' }}</span>
                            <span class="text-[10px] font-semibold text-slate-400">{{ $msg->created_at->format('h:i A') }}</span>
                        </div>
                        <div class="bg-white dark:bg-slate-800 rounded-2xl rounded-bl-sm px-4 py-3 shadow-sm border border-[#E6EAF0] dark:border-slate-700">
                            <p class="text-[13px] text-slate-700 dark:text-slate-300 leading-relaxed">{{ $msg->message }}</p>
                        </div>
                    </div>
                </div>
                @endif
                
                @if($msg->admin_reply)
                <!-- Admin Reply -->
                <div class="flex items-end gap-3 max-w-[85%] ml-auto flex-row-reverse group">
                    <div class="h-8 w-8 rounded-full bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center text-white text-[10px] font-black flex-shrink-0 shadow-md mb-5">
                        KR
                    </div>
                    <div class="flex flex-col gap-1 items-end">
                        <div class="flex items-center gap-2 px-1">
                            <span class="text-[10px] font-semibold text-slate-400">{{ $msg->replied_at ? $msg->replied_at->format('h:i A') : '' }}</span>
                            <span class="text-[11px] font-bold text-slate-700 dark:text-slate-300">You</span>
                        </div>
                        <div class="bg-blue-600 rounded-2xl rounded-br-sm px-4 py-3 shadow-[0_2px_8px_rgba(37,99,235,0.2)]">
                            <p class="text-[13px] text-white leading-relaxed">{{ $msg->admin_reply }}</p>
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
                    <p class="text-sm text-slate-400 font-medium">Select a conversation to view messages</p>
                </div>
            </div>
            @endif
            
            <!-- Message Input -->
            <div class="p-4 md:p-6 bg-white dark:bg-slate-900 border-t border-[#E6EAF0] dark:border-slate-800 flex-shrink-0 z-10 shadow-[0_-4px_10px_rgba(0,0,0,0.02)]">
                <form method="POST" action="{{ route('admin.chat-support.store') }}">
                    @csrf
                    <input type="hidden" name="conversation_id" value="{{ $activeConversationId }}">
                    <div class="flex items-end gap-3 bg-[#F8FAFC] dark:bg-slate-800 p-2 rounded-2xl border border-[#E6EAF0] dark:border-slate-700 focus-within:border-blue-500 transition-all shadow-inner">
                        <div class="flex-1 w-full relative min-h-[44px]">
                            <textarea name="message" placeholder="Type your reply to {{ $activeContactName }}..." rows="1" required 
                                @keydown.enter="if(!$event.shiftKey) { $event.preventDefault(); $el.closest('form').submit(); }"
                                class="w-full bg-transparent border-none outline-none text-[14px] text-slate-700 dark:text-slate-300 placeholder-slate-400 resize-none overflow-hidden pt-3 focus:ring-0"></textarea>
                        </div>
                        <div class="pb-1 pr-1">
                            <button type="submit" class="h-10 w-10 flex items-center justify-center bg-gradient-to-br from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 rounded-xl text-white transition-all shadow-md shadow-blue-500/30 group">
                                <i data-lucide="send" class="text-[18px] group-hover:-translate-y-0.5 group-hover:translate-x-0.5 transition-transform w-5 h-5"></i>
                            </button>
                        </div>
                    </div>
                </form>
                <div class="flex items-center justify-between mt-3 px-2">
                    <p class="text-[10px] font-semibold text-slate-400 flex items-center gap-1.5">Reply as Admin</p>
                    <p class="text-[10px] font-semibold text-slate-400">Press <kbd class="px-1.5 py-0.5 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded text-slate-500 font-mono">Enter</kbd> to send</p>
                </div>
            </div>
        </div>
    </div>
    @else
    <!-- Empty State -->
    <div class="flex-1 flex items-center justify-center bg-white dark:bg-slate-900 rounded-2xl border border-[#E6EAF0] dark:border-slate-800 shadow-[0_4px_20px_rgba(0,0,0,0.03)] p-10">
        <div class="text-center">
            <div class="w-20 h-20 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto mb-4">
                <i data-lucide="inbox" class="w-10 h-10 text-slate-300"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">No active conversations</h3>
            <p class="text-sm text-slate-500 max-w-sm mx-auto">Messages from partners, staff, or the website chatbot will appear here.</p>
        </div>
    </div>
    @endif
</div>
@endsection
