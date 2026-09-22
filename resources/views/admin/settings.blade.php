@extends('layouts.admin')

@section('title', 'System Settings | Kingdom Admin')

@section('content')
<div class="flex flex-col space-y-4" x-data="{ saving: false }">

  <!-- Page Header -->
  <div class="dashboard-header flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
    <div>
      <h1 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
        <i data-lucide="settings" class="w-5 h-5 text-kingdom-navy"></i>
        System Settings
      </h1>
      <p class="text-slate-500 text-xs mt-0.5">Manage global platform configurations and operational rules.</p>
    </div>
  </div>

  <!-- Settings Form -->
  <form method="POST" action="{{ route('admin.settings.update') }}" @submit="saving = true" class="space-y-4">
      @csrf
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 items-stretch">
          
          <!-- Operations Settings Block -->
          <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex flex-col justify-between">
              <div>
                  <h3 class="text-base font-bold text-kingdom-navy mb-3 flex items-center gap-2">
                      <i data-lucide="briefcase" class="w-4 h-4 text-kingdom-gold"></i>
                      Operational Rules
                  </h3>

                  <div class="space-y-4">
                      <!-- Quotation Edit Window -->
                      <div class="group">
                          <label for="quotation_edit_window_hours" class="block text-xs font-bold text-slate-700 mb-1">
                              Partner Quotation Edit Window (Hours)
                          </label>
                          <p class="text-xs text-slate-500 mb-2.5 leading-relaxed">
                              Determine how many hours a Partner can edit their staff booking after submission before it locks automatically. Set to '0' to disable editing entirely.
                          </p>
                          
                          <div class="relative max-w-xs">
                              <input type="number" 
                                     id="quotation_edit_window_hours" 
                                     name="quotation_edit_window_hours" 
                                     value="{{ old('quotation_edit_window_hours', $editWindowHours) }}" 
                                     class="w-full pl-4 pr-14 py-2 bg-slate-50 focus:bg-white rounded-xl border border-slate-200 focus:outline-none focus:border-kingdom-gold focus:ring-2 focus:ring-kingdom-gold/10 font-bold text-xs text-kingdom-navy transition-all @error('quotation_edit_window_hours') border-red-500 ring-red-500/10 @enderror"
                                     min="0"
                                     required>
                              <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                                  <span class="text-slate-400 font-bold text-xs uppercase tracking-wide">HRS</span>
                              </div>
                          </div>
                          
                          @error('quotation_edit_window_hours')
                              <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p>
                          @enderror
                      </div>
                  </div>
              </div>
          </div>

          <!-- Emergency Notice Settings Block -->
          <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex flex-col justify-between" x-data="{
              enabled: {{ $emergencyEnabled ? 'true' : 'false' }},
              msg1: '{{ addslashes($emergencyMessage1) }}',
              msg2: '{{ addslashes($emergencyMessage2) }}'
          }">
              <div>
                  <div class="flex items-center justify-between mb-2">
                      <h3 class="text-base font-bold text-kingdom-navy flex items-center gap-2">
                          <i data-lucide="alert-triangle" class="w-4 h-4 text-red-500"></i>
                          Emergency Notice
                      </h3>
                      <!-- Toggle Switch -->
                      <label class="relative inline-flex items-center cursor-pointer group shrink-0 ml-4">
                          <input type="checkbox" name="emergency_enabled" x-model="enabled" class="sr-only peer" value="1">
                          <div class="w-10 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-red-500 shadow-inner transition-colors"></div>
                          <span class="ml-2 text-xs font-bold uppercase tracking-wider" :class="enabled ? 'text-red-600' : 'text-slate-400'" x-text="enabled ? 'Active' : 'Off'"></span>
                      </label>
                  </div>
                  <p class="text-xs text-slate-500 mb-3 leading-relaxed">
                      Display a scrolling emergency notice bar across the Partner dashboard header. Partners will see this as a red alert banner.
                  </p>

                  <div x-show="enabled" x-collapse class="space-y-3">
                      <!-- Message 1 -->
                      <div>
                          <label class="block text-xs font-bold text-slate-700 mb-1">
                              Message 1 <span class="text-red-400">*</span>
                          </label>
                          <div class="relative">
                              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                  <span class="text-sm">🚨</span>
                              </div>
                              <input type="text" name="emergency_message_1" x-model="msg1"
                                  value="{{ old('emergency_message_1', $emergencyMessage1) }}"
                                  class="w-full pl-9 pr-3 py-2 bg-slate-50 focus:bg-white rounded-xl border border-slate-200 focus:outline-none focus:border-red-400 focus:ring-2 focus:ring-red-400/10 text-xs font-medium text-kingdom-navy transition-all placeholder:text-slate-400"
                                  placeholder="e.g. System maintenance tonight at 2:00 AM"
                                  maxlength="500">
                          </div>
                      </div>

                      <!-- Message 2 -->
                      <div>
                          <label class="block text-xs font-bold text-slate-700 mb-1">
                              Message 2 <span class="text-slate-400 font-normal">(Optional)</span>
                          </label>
                          <div class="relative">
                              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                  <span class="text-sm">⚠️</span>
                              </div>
                              <input type="text" name="emergency_message_2" x-model="msg2"
                                  value="{{ old('emergency_message_2', $emergencyMessage2) }}"
                                  class="w-full pl-9 pr-3 py-2 bg-slate-50 focus:bg-white rounded-xl border border-slate-200 focus:outline-none focus:border-red-400 focus:ring-2 focus:ring-red-400/10 text-xs font-medium text-kingdom-navy transition-all placeholder:text-slate-400"
                                  placeholder="e.g. London Bridge area closed — expect severe delays"
                                  maxlength="500">
                          </div>
                      </div>

                      <!-- Live Preview -->
                      <div x-show="msg1.length > 0" class="mt-3">
                          <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Live Preview</p>
                          <div class="bg-gradient-to-r from-red-700 via-rose-600 to-red-700 text-white overflow-hidden rounded-xl h-9 border border-red-500/50 flex items-center px-3 gap-2.5">
                              <div class="flex items-center gap-1 shrink-0 bg-white/20 px-2 py-0.5 rounded-full border border-white/30">
                                  <i data-lucide="alert-triangle" class="text-white animate-pulse w-3 h-3"></i>
                                  <span class="text-[9px] font-bold text-white uppercase tracking-wider">Emergency</span>
                              </div>
                              <div class="flex-1 overflow-hidden text-xs font-bold truncate">
                                  <span x-text="'🚨 ' + msg1"></span>
                                  <template x-if="msg2.length > 0">
                                      <span x-text="'  •  ⚠️ ' + msg2"></span>
                                  </template>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
      </div>

      <!-- Save Button -->
      <div class="pt-2 flex items-center gap-3">
          <button type="submit" 
                  class="bg-kingdom-navy text-white px-6 py-2.5 rounded-xl font-bold text-xs hover:bg-slate-800 transition-all shadow-md shadow-kingdom-navy/20 flex items-center gap-2 relative overflow-hidden"
                  :class="{'opacity-80 cursor-wait': saving}">
              <span x-show="!saving" class="flex items-center gap-2">
                  <i data-lucide="save" class="w-4 h-4"></i>
                  Save Settings
              </span>
              <span x-show="saving" x-cloak class="flex items-center gap-2">
                  <i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i>
                  Saving...
              </span>
          </button>
          
          @if(session('success'))
              <span class="text-green-600 font-bold text-xs bg-green-50 px-3.5 py-1.5 rounded-lg border border-green-100 flex items-center gap-1.5 animate-in fade-in zoom-in slide-in-from-left-4 duration-300">
                  <i data-lucide="check-circle-2" class="w-3.5 h-3.5"></i>
                  {{ session('success') }}
              </span>
          @endif
      </div>
  </form>
</div>
@endsection
