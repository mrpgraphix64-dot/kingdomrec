<!-- Unified Toast Notification System -->
<div x-data="{
        toasts: [],
        addToast(message, type = 'success') {
            const id = Date.now() + Math.random();
            this.toasts.push({ id, message, type, show: true });
            setTimeout(() => {
                const t = this.toasts.find(t => t.id === id);
                if (t) t.show = false;
                setTimeout(() => {
                    this.toasts = this.toasts.filter(t => t.id !== id);
                }, 300);
            }, 5000);
        }
     }"
     x-init="
        @if(session()->has('success'))
            addToast('{{ addslashes(session('success')) }}', 'success');
        @endif
        @if(session()->has('error'))
            addToast('{{ addslashes(session('error')) }}', 'error');
        @endif
     "
     @toast-show.window="addToast($event.detail.message, $event.detail.type || 'success')"
     class="fixed bottom-6 right-6 z-[200] flex flex-col-reverse gap-2 pointer-events-none"
     style="max-height: calc(100vh - 3rem);">

    <template x-for="toast in toasts" :key="toast.id">
        <div x-show="toast.show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-x-8 scale-95"
             x-transition:enter-end="opacity-100 translate-x-0 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-x-0 scale-100"
             x-transition:leave-end="opacity-0 translate-x-8 scale-95"
             class="pointer-events-auto flex items-center gap-3 w-full max-w-sm px-5 py-4 bg-[#0F1D33] text-white rounded-2xl shadow-2xl shadow-black/20 border"
             :class="toast.type === 'success' ? 'border-white/10' : 'border-red-500/30'"
             role="alert">

            <div class="flex-shrink-0 w-9 h-9 flex items-center justify-center rounded-xl"
                 :class="toast.type === 'success' ? 'bg-emerald-500/20' : 'bg-red-500/20'">
                <span class="material-symbols-outlined text-[20px]"
                      :class="toast.type === 'success' ? 'text-emerald-400' : 'text-red-400'"
                      x-text="toast.type === 'success' ? 'check_circle' : 'error'"></span>
            </div>

            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold !text-white" x-text="toast.type === 'success' ? 'Success' : 'Error'"></p>
                <p class="text-xs mt-0.5 truncate" :class="toast.type === 'success' ? '!text-slate-300' : '!text-red-200'" x-text="toast.message"></p>
            </div>

            <button type="button" @click="toast.show = false" class="flex-shrink-0 p-1 rounded-lg hover:bg-white/10 text-slate-400 hover:text-white transition-all pointer-events-auto" aria-label="Dismiss notification">
                <span class="material-symbols-outlined text-[16px]">close</span>
            </button>
        </div>
    </template>
</div>
