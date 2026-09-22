{{-- Chatbot Widget — Floating Help Assistant --}}
{{-- Context-aware: shows different quick actions on public site vs partner vs admin --}}
@php
    $isPartner = request()->is('partner/*') || request()->is('partner');
    $isAdmin = request()->is('admin/*') || request()->is('admin');
    $isPublic = !$isPartner && !$isAdmin;

    $userType = $isPartner ? 'partner' : ($isAdmin ? 'admin' : 'visitor');
    $userName = auth()->check() ? auth()->user()->name : '';

    if ($isPartner) {
        $quickActions = [
            ['id' => 'book_staff', 'label' => 'Book Staff', 'icon' => '📋'],
            ['id' => 'view_quotations', 'label' => 'View Quotations', 'icon' => '📊'],
            ['id' => 'timesheets_help', 'label' => 'Timesheets Help', 'icon' => '⏰'],
            ['id' => 'rate_card_info', 'label' => 'Rate Card', 'icon' => '💰'],
            ['id' => 'contact_support', 'label' => 'Contact Support', 'icon' => '📞'],
        ];
    } elseif ($isAdmin) {
        $quickActions = [
            ['id' => 'contact_support', 'label' => 'Contact Support', 'icon' => '📞'],
        ];
    } else {
        $quickActions = [
            ['id' => 'find_jobs', 'label' => 'Find Jobs', 'icon' => '🔍'],
            ['id' => 'upload_cv', 'label' => 'Upload CV', 'icon' => '📄'],
            ['id' => 'need_staff', 'label' => 'Need Staff', 'icon' => '👥'],
            ['id' => 'apply_jobs', 'label' => 'How to Apply', 'icon' => '✅'],
            ['id' => 'contact_support', 'label' => 'Contact Us', 'icon' => '📞'],
        ];
    }
@endphp

<div x-data="chatbotWidget()" @open-chat.window="if(!isOpen) toggleChat()" x-cloak>

<style>
    .chatbot-greeting-shifted {
        bottom: 9rem !important;
    }
    @media (min-width: 640px) {
        .chatbot-greeting-shifted {
            bottom: 10rem !important;
        }
    }
</style>

    {{-- First-time greeting popup --}}
    <div x-show="showGreeting && !isOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0 scale-95"
         @click="toggleChat(); showGreeting = false"
         class="fixed bottom-[5rem] sm:bottom-[6rem] right-4 sm:right-6 z-[80] bg-white dark:bg-slate-800 rounded-2xl shadow-2xl p-4 max-w-[240px] cursor-pointer border border-[#E6EAF0] dark:border-slate-700 hover:shadow-3xl transition-shadow flex items-start gap-3 transition-all duration-300"
         :class="{ 'chatbot-greeting-shifted': hasStickyFooter }">
        
        {{-- Avatar/Icon --}}
        <div class="w-10 h-10 rounded-full bg-kingdom-gold/10 flex items-center justify-center shrink-0">
            <span class="text-xl">👋</span>
        </div>

        <div class="flex-1 mt-0.5">
            <p class="text-[13px] font-bold text-slate-900 dark:text-white">Need help?</p>
            <p class="text-[11px] text-slate-500 mt-1 leading-relaxed">{{ $isPartner ? 'Need help with bookings or quotes?' : ($isAdmin ? 'View support messages' : 'Finding jobs or need staff?') }}</p>
        </div>
        
        <button @click.stop="showGreeting = false" class="absolute -top-2 -right-2 h-6 w-6 rounded-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-400 hover:text-slate-600 dark:hover:text-white text-xs flex items-center justify-center hover:bg-slate-50 dark:hover:bg-slate-700 shadow-sm transition-colors">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>

        {{-- Speech Bubble Tail --}}
        <div class="absolute -bottom-2 right-6 sm:right-8 w-4 h-4 bg-white dark:bg-slate-800 border-b border-r border-[#E6EAF0] dark:border-slate-700 transform rotate-45 shadow-[2px_2px_2px_rgba(0,0,0,0.02)]"></div>
    </div>

    {{-- Chat Window --}}
    <div x-show="isOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-4 scale-95"
         @wheel.stop
         class="fixed bottom-0 right-0 sm:bottom-6 sm:right-6 z-[85] w-full sm:w-[370px] h-[100dvh] sm:h-[520px] bg-white dark:bg-slate-900 sm:rounded-3xl shadow-2xl border border-slate-200 dark:border-slate-700 flex flex-col overflow-hidden"
         style="overscroll-behavior: contain;">
        
        {{-- Header --}}
        <div class="bg-gradient-to-r from-[#0F1D33] to-[#1a2b4c] p-4 flex items-center gap-3 shrink-0">
            <div class="h-10 w-10 rounded-full bg-gradient-to-br from-[#B89955] to-[#d4b06a] flex items-center justify-center text-white text-sm font-bold shadow-lg">
                K
            </div>
            <div class="flex-1">
                <h3 class="text-white font-bold text-sm">Kingdom Assistant</h3>
                <div class="flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span class="text-emerald-300 text-[11px] font-medium">Online</span>
                </div>
            </div>
            <button @click="isOpen = false" class="text-white/60 hover:text-white transition-colors p-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Messages Area --}}
        <div x-ref="messagesContainer" class="flex-1 overflow-y-auto p-4 space-y-3 bg-slate-50/50 dark:bg-slate-950/30" style="scroll-behavior: smooth; overscroll-behavior: contain;">
            {{-- Messages rendered by Alpine --}}
            <template x-for="(msg, idx) in messages" :key="idx">
                <div>
                    {{-- Bot message --}}
                    <template x-if="msg.from === 'bot'">
                        <div class="flex items-end gap-2 max-w-[85%]">
                            <div class="h-7 w-7 rounded-full bg-gradient-to-br from-[#B89955] to-[#d4b06a] flex items-center justify-center text-white text-[9px] font-bold shrink-0 shadow-sm">K</div>
                            <div class="bg-white dark:bg-slate-800 rounded-2xl rounded-bl-md px-4 py-3 shadow-sm border border-slate-100 dark:border-slate-700">
                                <p class="text-sm text-slate-700 dark:text-slate-200 leading-relaxed whitespace-pre-line" x-html="formatMessage(msg.text)"></p>
                            </div>
                        </div>
                    </template>
                    {{-- User message --}}
                    <template x-if="msg.from === 'user'">
                        <div class="flex justify-end">
                            <div class="bg-[#0F1D33] rounded-2xl rounded-br-md px-4 py-3 max-w-[85%] shadow-sm">
                                <p class="text-sm text-white leading-relaxed" x-text="msg.text"></p>
                            </div>
                        </div>
                    </template>
                </div>
            </template>

            {{-- Typing indicator --}}
            <div x-show="isTyping" class="flex items-end gap-2 max-w-[85%]">
                <div class="h-7 w-7 rounded-full bg-gradient-to-br from-[#B89955] to-[#d4b06a] flex items-center justify-center text-white text-[9px] font-bold shrink-0">K</div>
                <div class="bg-white dark:bg-slate-800 rounded-2xl rounded-bl-md px-4 py-3 shadow-sm border border-slate-100 dark:border-slate-700">
                    <div class="flex gap-1.5">
                        <span class="w-2 h-2 bg-slate-400 rounded-full animate-bounce" style="animation-delay: 0ms"></span>
                        <span class="w-2 h-2 bg-slate-400 rounded-full animate-bounce" style="animation-delay: 150ms"></span>
                        <span class="w-2 h-2 bg-slate-400 rounded-full animate-bounce" style="animation-delay: 300ms"></span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div x-show="showQuickActions && messages.length <= 1" class="px-4 pb-2 shrink-0">
            <div class="flex flex-wrap gap-1.5">
                <template x-for="action in quickActions" :key="action.id">
                    <button @click="handleQuickAction(action)" 
                            class="px-3 py-1.5 bg-[#B89955]/10 hover:bg-[#B89955]/20 text-[#B89955] rounded-full text-xs font-bold border border-[#B89955]/20 hover:border-[#B89955]/40 transition-all hover:-translate-y-0.5">
                        <span x-text="action.icon + ' ' + action.label"></span>
                    </button>
                </template>
            </div>
        </div>

        {{-- Input Area --}}
        <div class="p-3 border-t border-slate-100 dark:border-slate-800 shrink-0 bg-white dark:bg-slate-900">
            <form @submit.prevent="sendMessage()" class="flex items-center gap-2">
                <input x-model="userInput" 
                       type="text" 
                       placeholder="Type your message..." 
                       class="flex-1 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm text-slate-700 dark:text-slate-200 outline-none focus:ring-2 focus:ring-[#B89955] focus:border-transparent transition-all placeholder-slate-400"
                       :disabled="isTyping"
                       x-ref="chatInput">
                <button type="submit" 
                        :disabled="!userInput.trim() || isTyping"
                        class="h-10 w-10 rounded-xl bg-[#B89955] hover:bg-[#a08846] text-white flex items-center justify-center transition-all shadow-lg shadow-[#B89955]/20 disabled:opacity-40 disabled:cursor-not-allowed hover:-translate-y-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                </button>
            </form>
            <p class="text-[10px] text-slate-400 text-center mt-2">Powered by Kingdom Recruitments</p>
        </div>
    </div>
</div>

<script>
window.chatbotWidget = function chatbotWidget() {
    return {
        isOpen: false,
        isTyping: false,
        userInput: '',
        showGreeting: false,
        showQuickActions: true,
        conversationId: null,
        userType: '{{ $userType }}',
        userName: '{{ $userName }}',
        messages: [],
        hasStickyFooter: false,

        // Context-aware quick actions
        quickActions: @json($quickActions),

        init() {
            this.conversationId = this.generateUUID();
            
            this.checkFooterVisibility();
            const observer = new MutationObserver(() => {
                this.checkFooterVisibility();
            });
            observer.observe(document.body, {
                childList: true,
                subtree: true,
                attributes: true,
                attributeFilter: ['style', 'class']
            });

            // Show greeting after 3 seconds. Uses a 1-hour cooldown instead of single-session
            // so it shows more reliably while testing pages.
            const lastGreeted = localStorage.getItem('chatbot_greeted_time');
            const now = new Date().getTime();
            
            // Show if never greeted, or if > 1 hour has passed
            if (!lastGreeted || now - parseInt(lastGreeted) > 3600000) {
                setTimeout(() => {
                    this.showGreeting = true;
                    localStorage.setItem('chatbot_greeted_time', now.toString());
                }, 3000);
            }
        },

        checkFooterVisibility() {
            const el = document.querySelector('.sticky.bottom-0, .fixed.bottom-0');
            if (el) {
                this.hasStickyFooter = el.offsetWidth > 0 && el.offsetHeight > 0;
            } else {
                this.hasStickyFooter = false;
            }
        },

        toggleChat() {
            this.isOpen = !this.isOpen;
            this.showGreeting = false;
            if (this.isOpen && this.messages.length === 0) {
                this.addBotMessage("Hello 👋 Welcome to Kingdom Recruitments!\n\nHow can I assist you today? Choose an option below or type your question.");
            }
            if (this.isOpen) {
                this.$nextTick(() => this.$refs.chatInput?.focus());
            }
        },

        handleQuickAction(action) {
            this.addUserMessage(action.icon + ' ' + action.label);
            this.showQuickActions = false;
            this.isTyping = true;
            this.scrollToBottom();

            fetch('/chatbot/respond', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ action: action.id })
            })
            .then(r => r.json())
            .then(data => {
                setTimeout(() => {
                    this.isTyping = false;
                    this.addBotMessage(data.reply);
                    this.showQuickActions = true;
                }, 800 + Math.random() * 500);
            })
            .catch(() => {
                this.isTyping = false;
                this.addBotMessage("Sorry, something went wrong. Please try again.");
            });
        },

        sendMessage() {
            const text = this.userInput.trim();
            if (!text) return;

            this.addUserMessage(text);
            this.userInput = '';
            this.isTyping = true;
            this.scrollToBottom();

            // Send to DB as support message
            fetch('/chatbot/message', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    message: text,
                    visitor_name: this.userName || 'Anonymous Visitor',
                    page_url: window.location.pathname,
                    conversation_id: this.conversationId,
                    user_type: this.userType,
                })
            })
            .then(r => r.json())
            .then(data => {
                setTimeout(() => {
                    this.isTyping = false;
                    this.addBotMessage(data.reply);
                }, 1000 + Math.random() * 500);
            })
            .catch(() => {
                this.isTyping = false;
                this.addBotMessage("Sorry, I couldn't send your message. Please try again.");
            });
        },

        addBotMessage(text) {
            this.messages.push({ from: 'bot', text: text });
            this.scrollToBottom();
        },

        addUserMessage(text) {
            this.messages.push({ from: 'user', text: text });
            this.scrollToBottom();
        },

        scrollToBottom() {
            this.$nextTick(() => {
                const c = this.$refs.messagesContainer;
                if (c) c.scrollTop = c.scrollHeight;
            });
        },

        formatMessage(text) {
            // Convert **bold** and [links](url) to HTML
            return text
                .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                .replace(/\[(.*?)\]\((.*?)\)/g, '<a href="$2" class="text-[#B89955] underline hover:text-[#a08846] font-semibold" target="_blank">$1</a>')
                .replace(/\n/g, '<br>');
        },

        generateUUID() {
            return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
                const r = Math.random() * 16 | 0;
                return (c === 'x' ? r : (r & 0x3 | 0x8)).toString(16);
            });
        }
    }
}
</script>
