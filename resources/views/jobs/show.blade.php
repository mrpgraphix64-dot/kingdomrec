@extends('layouts.app')

@section('title', $job['title'] . ' | Kingdom Recruitments')

@section('content')
<main class="bg-white dark:bg-background-dark min-h-[75vh] pt-28 md:pt-36 pb-12 transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Breadcrumbs & Back Button --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <nav class="flex text-sm font-semibold uppercase tracking-wider text-slate-500" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('home') }}" class="hover:text-kingdom-red flex items-center gap-1 transition-colors">
                            <span class="material-icons text-sm">home</span> Home
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <span class="material-icons text-sm mr-1">chevron_right</span>
                            <a href="{{ route('jobs.index') }}" class="hover:text-kingdom-red transition-colors">Jobs</a>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <span class="material-icons text-sm mr-1">chevron_right</span>
                            <span class="text-slate-400 select-none">{{ $job['title'] }}</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <a href="{{ route('jobs.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-kingdom-navy dark:text-white hover:text-kingdom-red transition-colors group">
                <span class="material-icons group-hover:-translate-x-1 transition-transform">arrow_back</span>
                Back to Job Listings
            </a>
        </div>

        {{-- Main Layout Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            {{-- Left Column: Job Description and Details --}}
            <div class="lg:col-span-8 space-y-5">
                
                {{-- Header Card --}}
                <div class="bg-slate-100 dark:bg-slate-800 rounded-2xl p-6 sm:p-8 shadow-md border-t-4 border-t-kingdom-navy border-l-2 border-l-kingdom-gold relative overflow-hidden transition-colors duration-300">
                    <div class="absolute inset-0 opacity-[0.02] pointer-events-none" style="background-image: radial-gradient(#0F1D33 1px, transparent 1px); background-size: 24px 24px;"></div>
                    
                    <div class="relative z-10">
                        <div class="flex flex-wrap items-center gap-2.5 mb-5">
                            @foreach($job['tags'] as $tag)
                                <span class="px-3 py-1 bg-red-50 dark:bg-red-900/10 text-kingdom-red dark:text-red-400 text-xs font-bold uppercase tracking-wider rounded border border-kingdom-red/10">
                                    {{ $tag }}
                                </span>
                            @endforeach
                            @if($job['isNew'])
                                <span class="px-3 py-1 bg-green-50 dark:bg-green-900/10 text-green-600 dark:text-green-400 text-xs font-bold uppercase tracking-wider rounded border border-green-500/10">
                                    New
                                </span>
                            @endif
                        </div>
                        
                        <h1 class="text-3xl sm:text-4xl lg:text-4xl font-extrabold text-kingdom-navy dark:text-white leading-tight mb-4 tracking-tight">
                            {{ $job['title'] }}
                        </h1>
                        
                        <div class="flex flex-wrap items-center gap-x-6 gap-y-3 text-slate-500 dark:text-slate-400 text-sm font-medium pt-4 mt-5 border-t border-slate-200/60 dark:border-slate-800/80">
                            <span class="flex items-center gap-2 text-kingdom-navy dark:text-slate-200">
                                <span class="material-icons text-lg text-kingdom-navy dark:text-slate-400">business</span>
                                <span class="font-semibold">{{ $job['company'] }}</span>
                            </span>
                            <span class="h-4 w-[1px] bg-slate-200 dark:bg-slate-700 hidden sm:inline-block"></span>
                            <span class="flex items-center gap-2">
                                <span class="material-icons text-lg text-slate-400">place</span>
                                <span>{{ $job['location'] }}</span>
                            </span>
                            <span class="h-4 w-[1px] bg-slate-200 dark:bg-slate-700 hidden sm:inline-block"></span>
                            <span class="flex items-center gap-2">
                                <span class="material-icons text-lg text-slate-400">schedule</span>
                                <span>Posted {{ $job['posted'] }}</span>
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Job Description --}}
                <div class="bg-slate-100 dark:bg-slate-800 rounded-2xl p-6 sm:p-10 shadow-md shadow-slate-900/5 border border-slate-200/60 dark:border-gray-700 transition-colors duration-300">
                    <div class="border-b border-slate-200/60 dark:border-slate-800/80 pb-4 mb-6">
                        <h2 class="text-xl font-bold text-kingdom-navy dark:text-white">Job Description</h2>
                    </div>
                    <div class="prose dark:prose-invert max-w-none text-slate-600 dark:text-slate-300 leading-relaxed text-sm sm:text-base space-y-4">
                        {!! nl2br(e($job['description'])) !!}
                    </div>
                </div>
            </div>

            {{-- Right Column: Sticky Quick Facts & Apply Card --}}
            <aside class="lg:col-span-4 sticky top-24 space-y-5">
                
                {{-- Quick Facts Card (Differentiated slate panel) --}}
                <div class="bg-slate-100 dark:bg-slate-800 rounded-2xl p-6 border-t-4 border-t-kingdom-gold border-l-2 border-l-kingdom-navy border-r border-b border-slate-200/80 dark:border-slate-800/80 shadow-md shadow-slate-900/5 transition-colors duration-300">
                    <div class="border-b border-slate-200 dark:border-slate-700 pb-3 mb-6">
                        <h3 class="text-lg font-bold text-kingdom-navy dark:text-white">Job Overview</h3>
                    </div>
                    
                    <ul class="space-y-6">
                        <li class="flex items-center gap-4">
                            <div class="w-11 h-11 rounded-xl bg-white dark:bg-slate-800 flex items-center justify-center text-kingdom-navy dark:text-kingdom-gold shrink-0 border border-slate-200/60 dark:border-slate-700/60 shadow-sm">
                                <span class="material-symbols-outlined text-xl">currency_pound</span>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400 dark:text-slate-500 font-semibold uppercase tracking-wider mb-1">Salary Range</p>
                                <p class="text-sm sm:text-base font-bold text-kingdom-navy dark:text-white">{{ $job['salaryRange'] }}</p>
                            </div>
                        </li>
                        
                        <li class="flex items-center gap-4">
                            <div class="w-11 h-11 rounded-xl bg-white dark:bg-slate-800 flex items-center justify-center text-kingdom-navy dark:text-kingdom-gold shrink-0 border border-slate-200/60 dark:border-slate-700/60 shadow-sm">
                                <span class="material-icons text-xl">place</span>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400 dark:text-slate-500 font-semibold uppercase tracking-wider mb-1">Location</p>
                                <p class="text-sm sm:text-base font-bold text-kingdom-navy dark:text-white">{{ $job['location'] }}</p>
                            </div>
                        </li>
                        
                        <li class="flex items-center gap-4">
                            <div class="w-11 h-11 rounded-xl bg-white dark:bg-slate-800 flex items-center justify-center text-kingdom-navy dark:text-kingdom-gold shrink-0 border border-slate-200/60 dark:border-slate-700/60 shadow-sm">
                                <span class="material-icons text-xl">work</span>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400 dark:text-slate-500 font-semibold uppercase tracking-wider mb-1">Job Type</p>
                                <p class="text-sm sm:text-base font-bold text-kingdom-navy dark:text-white">{{ $job['type'] }}</p>
                            </div>
                        </li>
 
                        @if($job['deadline'])
                            <li class="flex items-center gap-4">
                                <div class="w-11 h-11 rounded-xl bg-white dark:bg-slate-800 flex items-center justify-center text-kingdom-navy dark:text-kingdom-gold shrink-0 border border-slate-200/60 dark:border-slate-700/60 shadow-sm">
                                    <span class="material-icons text-xl">calendar_today</span>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-400 dark:text-slate-500 font-semibold uppercase tracking-wider mb-1">Deadline</p>
                                    <p class="text-sm sm:text-base font-bold text-kingdom-navy dark:text-white">{{ $job['deadline'] }}</p>
                                </div>
                            </li>
                        @endif
                    </ul>

                    {{-- Apply Section --}}
                    <div class="mt-6 pt-6 border-t border-slate-200/60 dark:border-slate-800">
                        @if($isExpired)
                            <div class="w-full bg-rose-50/40 dark:bg-rose-950/10 text-rose-700 dark:text-rose-400 border border-rose-200/30 dark:border-rose-900/20 py-3 px-4 rounded-xl font-semibold flex items-center justify-center gap-2 text-sm">
                                <span class="material-icons text-lg text-rose-600">error_outline</span>
                                <span>This position is no longer accepting applications</span>
                            </div>
                            <button disabled class="w-full mt-4 bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500 font-bold py-3.5 px-6 rounded-xl border border-slate-200/50 dark:border-slate-700/50 cursor-not-allowed flex items-center justify-center gap-2">
                                <span>Apply Now</span>
                                <span class="material-icons text-sm">lock</span>
                            </button>
                        @elseif($isApplied)
                            <div class="w-full bg-green-50/40 dark:bg-green-950/10 text-green-700 dark:text-green-400 border border-green-200/30 dark:border-green-900/20 py-3 px-4 rounded-xl font-semibold flex items-center justify-center gap-2 text-sm">
                                <span class="material-icons text-lg text-green-600">check_circle</span>
                                <span>Applied Successfully</span>
                            </div>
                        @elseif(auth()->guest() || auth()->user()->role === 'applicant' || auth()->user()->role === 'applicant')
                            @auth
                                @if(isset($profileCompleteness) && !$profileCompleteness['is_complete'])
                                    <div class="mb-4 p-4 bg-amber-50 dark:bg-amber-950/20 border border-amber-200/50 dark:border-amber-900/30 rounded-xl text-xs text-amber-700 dark:text-amber-400">
                                        <p class="font-bold flex items-center gap-1.5 mb-2 text-sm">
                                            <span class="material-icons text-base">warning</span>
                                            Profile Incomplete
                                        </p>
                                        <p class="mb-2 leading-relaxed">Please complete the following details in your profile before you can apply:</p>
                                        <ul class="list-disc list-inside space-y-1 mb-3 font-semibold font-body">
                                            @foreach($profileCompleteness['missing'] as $item)
                                                <li>{{ $item }}</li>
                                            @endforeach
                                        </ul>
                                        <a href="{{ route('applicant.profile.edit') }}" class="inline-flex items-center gap-1 font-bold text-amber-800 dark:text-amber-300 hover:underline">
                                            Complete Profile Now <span class="material-icons text-xs">arrow_forward</span>
                                        </a>
                                    </div>
                                    
                                    <button disabled class="w-full bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500 font-bold py-3.5 px-6 rounded-xl border border-slate-200/50 dark:border-slate-700/50 cursor-not-allowed flex items-center justify-center gap-2">
                                        <span>Apply Now</span>
                                        <span class="material-icons text-sm">lock</span>
                                    </button>
                                @else
                                    <button id="apply-btn" onclick="applyJob({{ $job['id'] }})" class="w-full bg-kingdom-red hover:bg-red-700 text-white font-bold py-3.5 px-6 rounded-xl transition-all duration-200 flex items-center justify-center gap-2 group shadow-sm">
                                        <span>Apply Now</span>
                                        <span class="material-icons text-sm group-hover:translate-x-1 transition-transform">arrow_forward</span>
                                    </button>
                                @endif
                            @else
                                <button id="apply-btn" onclick="applyJob({{ $job['id'] }})" class="w-full bg-kingdom-red hover:bg-red-700 text-white font-bold py-3.5 px-6 rounded-xl transition-all duration-200 flex items-center justify-center gap-2 group shadow-sm">
                                    <span>Apply Now</span>
                                    <span class="material-icons text-sm group-hover:translate-x-1 transition-transform">arrow_forward</span>
                                </button>
                            @endauth
                        @endif
                    </div>
                </div>

                {{-- Contact Support Card (Differentiated light panel with dark prominent button) --}}
                <div class="bg-slate-100 dark:bg-slate-800 rounded-2xl p-6 border border-slate-200/60 dark:border-gray-700 overflow-hidden relative group shadow-md shadow-slate-900/5 transition-colors duration-300">
                    <div class="absolute inset-0 opacity-[0.02] pointer-events-none" style="background-image: radial-gradient(#0F1D33 1px, transparent 1px); background-size: 20px 20px;"></div>
                    
                    <h4 class="text-base font-bold text-kingdom-navy dark:text-white mb-2 relative z-10">Need Assistance?</h4>
                    <p class="text-slate-500 dark:text-slate-400 text-xs mb-5 relative z-10 leading-relaxed">Our executive consultants are here to guide you through your application.</p>
                    <a href="{{ route('contact') }}" class="inline-flex w-full py-3.5 bg-kingdom-navy hover:bg-slate-800 text-white text-sm font-bold rounded-xl transition-all duration-200 items-center justify-center gap-2 relative z-10 shadow-sm">
                        <span class="material-icons text-sm">support_agent</span> Talk to a Consultant
                    </a>
                </div>
            </aside>
        </div>
    </div>
</main>

<script>
    window.applyJob = function(id) {
        @guest
            window.location.href = "{{ route('portal') }}#applicant-login";
            return;
        @endguest

        @auth
            @if(auth()->user()->role !== 'applicant' && auth()->user()->role !== 'applicant')
                Swal.fire({ icon: 'info', title: 'Not Allowed', text: 'Only applicants can apply for jobs.', confirmButtonColor: '#0F1D33' });
                return;
            @endif
        @endauth

        const applyBtn = document.getElementById('apply-btn');
        if (!applyBtn) return;

        // Disable button to prevent double submit
        applyBtn.disabled = true;
        applyBtn.classList.add('opacity-50', 'cursor-not-allowed');

        fetch(`/jobs/${id}/apply`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({})
        })
        .then(res => {
            return res.json().then(data => {
                if (!res.ok) {
                    throw new Error(data.error || 'Failed to submit application.');
                }
                return data;
            }).catch(err => {
                if (err instanceof SyntaxError) {
                    throw new Error('An unexpected server error occurred.');
                }
                throw err;
            });
        })
        .then(data => {
            // Success state
            Swal.fire({ 
                icon: 'success', 
                title: 'Applied Successfully!', 
                text: 'Your application has been submitted to the recruitment team.', 
                confirmButtonColor: '#22c55e' 
            });

            applyBtn.outerHTML = `
                <div class="w-full bg-green-50/40 dark:bg-green-950/10 text-green-700 dark:text-green-400 border border-green-200/30 dark:border-green-900/20 py-3 px-4 rounded-xl font-semibold flex items-center justify-center gap-2 text-sm">
                    <span class="material-icons text-lg text-green-600">check_circle</span>
                    <span>Applied Successfully</span>
                </div>
            `;
        })
        .catch(err => {
            console.error('Apply failed:', err);
            Swal.fire({ icon: 'error', title: 'Application Error', text: err.message, confirmButtonColor: '#0F1D33' });
            applyBtn.disabled = false;
            applyBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        });
    };
</script>
@endsection
