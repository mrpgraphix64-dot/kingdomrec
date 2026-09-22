@extends('layouts.candidate')

@section('title', 'My Applications - ' . config('app.name', 'Kingdom Recruitments'))

@section('content')
    <div class="font-manrope max-w-4xl mx-auto flex flex-col gap-1">

        <div>
            <h1 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white tracking-tight">My Applications</h1>
            <p class="text-slate-500 dark:text-slate-400 text-xs mt-0.5">Track the status of all your job applications.</p>
        </div>

        <div class="mt-4">
            @if($applications->isEmpty())
                <div class="flex flex-col items-center justify-center py-20 bg-white dark:bg-navy-dark rounded-2xl border border-slate-100 dark:border-gray-700 shadow-sm text-center">
                    <div class="bg-slate-50 dark:bg-slate-800 p-6 rounded-full mb-4">
                        <i data-lucide="inbox" class="text-slate-300 dark:text-gray-500 w-12 h-12"></i>
                    </div>
                    <h3 class="text-xl font-bold text-kingdom-navy dark:text-white mb-2">No applications yet</h3>
                    <p class="text-slate-500 dark:text-gray-400 max-w-xs mb-6">Start browsing jobs and apply with a single click.</p>
                    <a href="{{ route('jobs.index') }}" class="px-6 py-2.5 bg-kingdom-red hover:bg-red-700 text-white font-bold rounded-xl transition-all shadow-lg">
                        Browse Jobs
                    </a>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($applications as $app)
                        @php
                            $statusConfig = [
                                'Pending' => ['bg' => 'bg-amber-100 dark:bg-amber-900/30', 'text' => 'text-amber-700 dark:text-amber-400', 'border' => 'border-amber-200 dark:border-amber-700', 'icon' => 'clock'],
                                'Reviewed' => ['bg' => 'bg-blue-100 dark:bg-blue-900/30', 'text' => 'text-blue-700 dark:text-blue-400', 'border' => 'border-blue-200 dark:border-blue-700', 'icon' => 'eye'],
                                'Qualified' => ['bg' => 'bg-green-100 dark:bg-green-900/30', 'text' => 'text-green-700 dark:text-green-400', 'border' => 'border-green-200 dark:border-green-700', 'icon' => 'check-circle'],
                                'Rejected' => ['bg' => 'bg-red-100 dark:bg-red-900/30', 'text' => 'text-red-700 dark:text-red-400', 'border' => 'border-red-200 dark:border-red-700', 'icon' => 'x-circle'],
                            ];
                            $sc = $statusConfig[$app->status] ?? $statusConfig['Pending'];
                        @endphp

                        <div class="bg-white dark:bg-navy-dark rounded-2xl p-5 border border-slate-100 dark:border-gray-700 shadow-sm hover:shadow-md transition-all group">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div class="flex-grow min-w-0">
                                    <div class="flex items-center gap-3 mb-2">
                                        <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-500 dark:text-slate-400 shrink-0">
                                            <i data-lucide="briefcase" class="w-5 h-5"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <h3 class="font-bold text-kingdom-navy dark:text-white text-base truncate group-hover:text-kingdom-red transition-colors">
                                                {{ $app->jobPost?->sub_category ?? 'Position No Longer Available' }}
                                            </h3>
                                            <p class="text-xs text-slate-500 dark:text-gray-400 truncate">
                                                {{ $app->jobPost?->company_name ?? '' }}
                                                @if($app->jobPost?->location) · {{ $app->jobPost->location }} @endif
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex flex-wrap items-center gap-3 ml-13 pl-0.5">
                                        @if($app->jobPost?->salary_from && $app->jobPost?->salary_to)
                                            <span class="text-xs text-slate-500 dark:text-gray-400 flex items-center gap-1">
                                                <i data-lucide="pound-sterling" class="w-3 h-3"></i>
                                                £{{ number_format($app->jobPost->salary_from) }} – £{{ number_format($app->jobPost->salary_to) }}
                                            </span>
                                        @endif
                                        <span class="text-xs text-slate-400 flex items-center gap-1">
                                            <i data-lucide="calendar" class="w-3 h-3"></i>
                                            Applied {{ $app->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                </div>

                                <div class="flex items-center gap-3 shrink-0">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold border {{ $sc['bg'] }} {{ $sc['text'] }} {{ $sc['border'] }}">
                                        <i data-lucide="{{ $sc['icon'] }}" class="w-3.5 h-3.5"></i>
                                        {{ $app->status }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection
