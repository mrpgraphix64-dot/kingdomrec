@extends('layouts.candidate')

@section('title', 'My Profile - Kingdom Recruitments')

@section('content')
@php
    $hasBasicProfile = ($applicant instanceof \App\Models\Applicant && !empty($applicant->phone) && $applicant->phone !== 'Not provided' && !empty($applicant->location) && $applicant->location !== 'Not listed');
    $hasCv = ($applicant instanceof \App\Models\Applicant && !empty($applicant->cv_link) && (!isset($applicant->cv_exists) || $applicant->cv_exists));
    $hasExperience = ($experience && $experience->count() > 0);
    $hasEducation = ($education && $education->count() > 0);

    $completedCount = ($hasBasicProfile ? 1 : 0) + ($hasCv ? 1 : 0) + ($hasExperience ? 1 : 0) + ($hasEducation ? 1 : 0);
    $completionPercentage = $completedCount * 25;

    if (!function_exists('getApplicationStatusBadge')) {
        function getApplicationStatusBadge($status) {
            $status = strtolower($status);
            switch($status) {
                case 'pending':
                case 'review':
                    return 'bg-blue-500/10 text-blue-400 border-blue-500/20';
                case 'interviewing':
                case 'interview':
                    return 'bg-amber-500/10 text-amber-400 border-amber-500/20';
                case 'shortlisted':
                    return 'bg-purple-500/10 text-purple-400 border-purple-500/20';
                case 'offered':
                case 'approved':
                case 'accepted':
                    return 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20';
                case 'rejected':
                case 'declined':
                    return 'bg-rose-500/10 text-rose-400 border-rose-500/20';
                default:
                    return 'bg-slate-500/10 text-slate-400 border-slate-500/20';
            }
        }
    }
@endphp

<div class="max-w-7xl mx-auto flex flex-col gap-5">

    <div>
        <h1 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white tracking-tight">My Profile</h1>
        <p class="text-slate-500 dark:text-slate-400 text-xs mt-0.5">View your professional profile, details, and resume.</p>
    </div>

    <!-- Profile Summary Card -->
    <div class="w-full bg-[#0B1120] p-6 md:p-8 rounded-3xl shadow-lg border border-white/10 relative overflow-hidden">
        <div class="relative z-10 flex flex-col lg:flex-row items-center lg:items-start justify-between gap-8">
            <!-- Left Column: Avatar & Details -->
            <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6 w-full lg:w-1/2">
                <div class="relative w-28 h-28 rounded-full border-4 border-white/10 shadow-2xl overflow-hidden bg-white/5 flex items-center justify-center shrink-0">
                    <x-avatar :user="$applicant" class="w-full h-full object-cover" />
                </div>
                <div class="flex-1 text-center sm:text-left min-w-0">
                    <h2 class="text-2xl md:text-3xl font-black text-white tracking-wide truncate">
                        {{ $applicant->name }}
                    </h2>
                    <p class="text-amber-500 font-bold text-sm tracking-widest uppercase mb-4">
                        @if($applicant->role === 'Other / Not Listed' && !empty($applicant->other_category_description))
                            Other: {{ $applicant->other_category_description }}
                        @else
                            {{ $applicant->role ?? 'Applicant' }}
                        @endif
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-left">
                        <div class="flex items-center gap-3 text-slate-300 min-w-0">
                            <span class="material-icons text-slate-400 text-[18px] bg-white/5 border border-white/10 p-2 rounded-xl">email</span>
                            <span class="text-xs md:text-sm font-medium truncate w-full" title="{{ $applicant->email }}">{{ $applicant->email }}</span>
                        </div>
                        <div class="flex items-center gap-3 text-slate-300 min-w-0">
                            <span class="material-icons text-slate-400 text-[18px] bg-white/5 border border-white/10 p-2 rounded-xl">phone</span>
                            <span class="text-xs md:text-sm font-medium truncate">{{ $applicant->phone ?? 'Not provided' }}</span>
                        </div>
                        @if(!empty($applicant->location) && $applicant->location !== 'Not listed')
                            <div class="flex items-center gap-3 text-slate-300 min-w-0 sm:col-span-2">
                                <span class="material-icons text-slate-400 text-[18px] bg-white/5 border border-white/10 p-2 rounded-xl">location_on</span>
                                <span class="text-xs md:text-sm font-medium truncate">{{ $applicant->location }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Middle Column: Profile Completion -->
            <div class="w-full lg:w-[35%] flex flex-col gap-4">
                <div class="flex justify-between items-center">
                    <span class="text-sm font-bold text-white tracking-wide">Profile Completion</span>
                    <span class="text-sm font-extrabold text-amber-500 bg-amber-500/10 px-2.5 py-1 rounded-lg border border-amber-500/20">
                        {{ $completionPercentage }}%
                    </span>
                </div>

                <div class="w-full bg-white/5 h-2.5 rounded-full overflow-hidden border border-white/5">
                    <div class="h-full rounded-full transition-all duration-500 bg-gradient-to-r @if($completionPercentage <= 25) from-red-500 to-orange-500 @elseif($completionPercentage <= 75) from-orange-500 to-amber-500 @else from-amber-500 to-emerald-500 @endif" style="width: {{ $completionPercentage }}%"></div>
                </div>

                <div class="grid grid-cols-2 gap-x-4 gap-y-2">
                    <div class="flex items-center gap-2 text-xs">
                        @if($hasBasicProfile)
                            <span class="material-icons text-emerald-400 text-[16px]">check_circle</span>
                            <span class="text-slate-300 font-medium">Basic Profile</span>
                        @else
                            <span class="material-icons text-rose-500 text-[16px]">cancel</span>
                            <span class="text-slate-400">Basic Profile</span>
                        @endif
                    </div>
                    <div class="flex items-center gap-2 text-xs">
                        @if($hasCv)
                            <span class="material-icons text-emerald-400 text-[16px]">check_circle</span>
                            <span class="text-slate-300 font-medium">Resume Uploaded</span>
                        @else
                            <span class="material-icons text-rose-500 text-[16px]">cancel</span>
                            <span class="text-slate-400">Resume Uploaded</span>
                        @endif
                    </div>
                    <div class="flex items-center gap-2 text-xs">
                        @if($hasExperience)
                            <span class="material-icons text-emerald-400 text-[16px]">check_circle</span>
                            <span class="text-slate-300 font-medium">Work Experience</span>
                        @else
                            <span class="material-icons text-rose-500 text-[16px]">cancel</span>
                            <span class="text-slate-400">Work Experience</span>
                        @endif
                    </div>
                    <div class="flex items-center gap-2 text-xs">
                        @if($hasEducation)
                            <span class="material-icons text-emerald-400 text-[16px]">check_circle</span>
                            <span class="text-slate-300 font-medium">Education Details</span>
                        @else
                            <span class="material-icons text-rose-500 text-[16px]">cancel</span>
                            <span class="text-slate-400">Education Details</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Column: Action Button -->
            <div class="w-full lg:w-auto self-center lg:self-start flex shrink-0">
                <a href="{{ route('applicant.profile.edit') }}" class="w-full lg:w-auto py-3 px-6 bg-white/10 hover:bg-white/20 text-white font-bold rounded-2xl transition-all duration-300 flex items-center justify-center gap-2 border border-white/5 hover:border-white/20 shadow-lg hover:-translate-y-0.5">
                    <span class="material-icons text-sm">edit</span> Edit Profile
                </a>
            </div>
        </div>
    </div>

    <!-- Success/Error Alert Section -->
    @if(session('success'))
        <div class="bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500 p-4 rounded-r-xl w-full flex items-center gap-3">
            <span class="material-icons text-green-500">check_circle</span>
            <p class="font-medium text-green-800 dark:text-green-300">{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 p-4 rounded-r-xl w-full flex items-center gap-3">
            <span class="material-icons text-red-500">error</span>
            <p class="font-medium text-red-800 dark:text-red-300">{{ session('error') }}</p>
        </div>
    @endif

    <!-- 2x2 Dashboard Grid -->
    <div class="w-full grid grid-cols-1 md:grid-cols-2 gap-5 items-stretch">

        <!-- Card 1: Applications (Blue Accent) -->
        <div class="bg-[#0B1120] p-6 md:p-8 rounded-3xl shadow-lg border border-white/10 flex flex-col h-full relative overflow-hidden">
            <div class="absolute top-0 left-0 w-1.5 h-full bg-blue-500"></div>

            <div class="flex items-center justify-between mb-6 pb-4 border-b border-white/10">
                <h2 class="text-xl font-bold text-white flex items-center gap-3">
                    <span class="material-icons text-blue-400 p-2 bg-blue-500/10 border border-blue-500/20 rounded-xl">assignment</span> My Applications
                </h2>
            </div>

            @if($applications->count() > 0)
                <div class="flex-grow overflow-y-auto max-h-[300px] pr-2 space-y-4">
                    @foreach($applications as $app)
                        <div class="bg-white/5 hover:bg-white/10 transition-colors p-4 rounded-2xl border border-white/5 hover:border-white/10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div>
                                <h3 class="text-sm font-bold text-white tracking-wide">
                                    @if($app->jobPost)
                                        <a href="{{ route('jobs.show', $app->jobPost->id) }}" class="hover:text-blue-400 transition-colors">
                                            {{ $app->jobPost->sub_category }}
                                        </a>
                                    @else
                                        Position Unavailable
                                    @endif
                                </h3>
                                <p class="text-[11px] text-slate-400 font-medium mt-1">
                                    Applied {{ $app->created_at->diffForHumans() }}
                                </p>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="px-2.5 py-0.5 text-[10px] font-extrabold uppercase tracking-wider rounded-full border {{ getApplicationStatusBadge($app->status) }}">
                                    {{ $app->status }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="flex-grow flex flex-col items-center justify-center text-center p-6 bg-white/5 rounded-2xl border border-white/5 border-dashed min-h-[200px]">
                    <span class="material-icons text-blue-400 text-3xl mb-2 bg-blue-500/10 border border-blue-500/20 p-3 rounded-full">work_outline</span>
                    <p class="text-slate-300 font-semibold text-sm mb-3">No applications yet</p>
                    <a href="{{ route('jobs.index') }}" class="inline-flex items-center justify-center px-4 py-2 bg-blue-500 hover:bg-blue-600 rounded-xl text-white font-bold text-xs transition-all hover:-translate-y-0.5">
                        Browse Jobs
                    </a>
                </div>
            @endif
        </div>

        <!-- Card 2: Resume (Gold Accent) -->
        <div class="bg-[#0B1120] p-6 md:p-8 rounded-3xl shadow-lg border border-white/10 flex flex-col h-full relative overflow-hidden">
            <div class="absolute top-0 left-0 w-1.5 h-full bg-amber-500"></div>

            <div class="flex items-center justify-between mb-6 pb-4 border-b border-white/10">
                <h2 class="text-xl font-bold text-white flex items-center gap-3">
                    <span class="material-icons text-amber-400 p-2 bg-amber-500/10 border border-amber-500/20 rounded-xl">description</span> Resume
                </h2>
            </div>

            @if(!empty($applicant->cv_link) && $applicant->cv_exists)
                <div class="flex-grow flex flex-col justify-between min-h-[200px]">
                    <div class="flex items-center gap-4 p-4 bg-white/5 hover:bg-white/10 transition-colors rounded-2xl border border-white/5">
                        <div class="p-3 bg-red-500/20 rounded-xl">
                            <span class="material-icons text-red-400 text-2xl">picture_as_pdf</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-white truncate">{{ $applicant->cvName ?? 'Resume.pdf' }}</p>
                            <p class="text-xs text-green-400 font-bold flex items-center gap-1 mt-1">
                                <span class="material-icons text-[12px]">check_circle</span> Uploaded successfully
                            </p>
                        </div>
                    </div>
                    <div class="mt-6">
                        <a href="{{ route('applicant.view-resume') }}" target="_blank" class="w-full py-3 bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-2xl transition-all duration-300 flex items-center justify-center gap-2 text-sm hover:-translate-y-0.5">
                            <span class="material-icons text-sm">visibility</span> View Document
                        </a>
                    </div>
                </div>
            @else
                <div class="flex-grow flex flex-col items-center justify-center text-center p-6 bg-white/5 rounded-2xl border border-white/5 border-dashed min-h-[200px]">
                    <span class="material-icons text-amber-400 text-3xl mb-2 bg-amber-500/10 border border-amber-500/20 p-3 rounded-full">warning</span>
                    @if(!empty($applicant->cv_link))
                        <p class="text-slate-300 font-semibold text-sm mb-1">File Unavailable</p>
                        <p class="text-xs text-slate-400 mb-3 leading-relaxed">The uploaded file is unavailable on the server.</p>
                        <a href="{{ route('applicant.profile.edit') }}" class="inline-flex items-center justify-center px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs transition-all rounded-xl hover:-translate-y-0.5">
                            Re-upload Resume
                        </a>
                    @else
                        <p class="text-slate-300 font-semibold text-sm mb-1">No Resume Attached</p>
                        <p class="text-xs text-slate-400 mb-3 leading-relaxed">A resume is required to apply for jobs.</p>
                        <a href="{{ route('applicant.profile.edit') }}" class="inline-flex items-center justify-center px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs transition-all rounded-xl hover:-translate-y-0.5">
                            Upload Resume
                        </a>
                    @endif
                </div>
            @endif
        </div>

        <!-- Card 3: Work Experience (Green Accent) -->
        <div class="bg-[#0B1120] p-6 md:p-8 rounded-3xl shadow-lg border border-white/10 flex flex-col h-full relative overflow-hidden">
            <div class="absolute top-0 left-0 w-1.5 h-full bg-emerald-500"></div>

            <div class="flex items-center justify-between mb-6 pb-4 border-b border-white/10">
                <h2 class="text-xl font-bold text-white flex items-center gap-3">
                    <span class="material-icons text-emerald-400 p-2 bg-emerald-500/10 border border-emerald-500/20 rounded-xl">business_center</span> Work Experience
                </h2>
                @if($experience->count() > 0)
                    <a href="{{ route('applicant.profile.edit') }}" class="text-xs font-bold text-emerald-400 hover:text-emerald-300 bg-emerald-500/5 hover:bg-emerald-500/10 px-3 py-1.5 rounded-lg border border-emerald-500/10 hover:border-emerald-500/30 transition-all flex items-center gap-1">
                        <span class="material-icons text-xs">edit</span> Edit
                    </a>
                @endif
            </div>

            @if($experience->count() > 0)
                <div class="flex-grow overflow-y-auto max-h-[300px] pr-2 space-y-4">
                    @foreach($experience as $exp)
                        <div class="relative pl-6 before:absolute before:left-[9px] before:top-3 before:bottom-0 before:w-0.5 before:bg-white/10 last:before:bottom-auto last:before:h-3">
                            <div class="absolute left-0 top-2.5 w-5 h-5 rounded-full bg-[#0B1120] border-4 border-emerald-500 flex items-center justify-center"></div>
                            <div class="bg-white/5 hover:bg-white/10 transition-colors p-4 rounded-2xl border border-white/5 hover:border-white/10">
                                <h3 class="text-sm font-bold text-white tracking-wide">{{ $exp->job_title }}</h3>
                                <p class="text-emerald-400 font-bold text-xs mb-2 tracking-wide uppercase">{{ $exp->company }}</p>
                                <p class="text-[10px] text-slate-400 font-medium mb-3 flex items-center gap-1 bg-white/5 inline-flex px-2 py-1 rounded">
                                    <span class="material-icons text-[12px] text-slate-400">calendar_month</span>
                                    {{ $exp->start_date ? \Carbon\Carbon::parse($exp->start_date)->format('M Y') : 'N/A' }} -
                                    @if($exp->is_current)
                                        <span class="text-emerald-400 font-bold ml-0.5">Present</span>
                                    @else
                                        {{ $exp->end_date ? \Carbon\Carbon::parse($exp->end_date)->format('M Y') : 'N/A' }}
                                    @endif
                                </p>
                                @if($exp->description)
                                    <p class="text-xs text-slate-300 leading-relaxed line-clamp-3">{{ $exp->description }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="flex-grow flex flex-col items-center justify-center text-center p-6 bg-white/5 rounded-2xl border border-white/5 border-dashed min-h-[200px]">
                    <span class="material-icons text-emerald-400 text-3xl mb-2 bg-emerald-500/10 border border-emerald-500/20 p-3 rounded-full">work_off</span>
                    <p class="text-slate-300 font-semibold text-sm mb-3">No work experience listed</p>
                    <a href="{{ route('applicant.profile.edit') }}" class="inline-flex items-center justify-center px-4 py-2 bg-[#10B981] hover:bg-emerald-600 rounded-xl text-white font-bold text-xs transition-all hover:-translate-y-0.5">
                        Add Experience
                    </a>
                </div>
            @endif
        </div>

        <!-- Card 4: Education (Purple Accent) -->
        <div class="bg-[#0B1120] p-6 md:p-8 rounded-3xl shadow-lg border border-white/10 flex flex-col h-full relative overflow-hidden">
            <div class="absolute top-0 left-0 w-1.5 h-full bg-purple-500"></div>

            <div class="flex items-center justify-between mb-6 pb-4 border-b border-white/10">
                <h2 class="text-xl font-bold text-white flex items-center gap-3">
                    <span class="material-icons text-purple-400 p-2 bg-purple-500/10 border border-purple-500/20 rounded-xl">school</span> Education
                </h2>
                @if($education->count() > 0)
                    <a href="{{ route('applicant.profile.edit') }}" class="text-xs font-bold text-purple-400 hover:text-purple-300 bg-purple-500/5 hover:bg-purple-500/10 px-3 py-1.5 rounded-lg border border-purple-500/10 hover:border-purple-500/30 transition-all flex items-center gap-1">
                        <span class="material-icons text-xs">edit</span> Edit
                    </a>
                @endif
            </div>

            @if($education->count() > 0)
                <div class="flex-grow overflow-y-auto max-h-[300px] pr-2 space-y-4">
                    @foreach($education as $edu)
                        <div class="relative pl-6 before:absolute before:left-[9px] before:top-3 before:bottom-0 before:w-0.5 before:bg-white/10 last:before:bottom-auto last:before:h-3">
                            <div class="absolute left-0 top-2.5 w-5 h-5 rounded-full bg-[#0B1120] border-4 border-purple-500 flex items-center justify-center"></div>
                            <div class="bg-white/5 hover:bg-white/10 transition-colors p-4 rounded-2xl border border-white/5 hover:border-white/10">
                                <h3 class="text-sm font-bold text-white tracking-wide">{{ $edu->degree }}</h3>
                                <p class="text-purple-400 font-bold text-xs mb-2 tracking-wide uppercase">{{ $edu->institution }}</p>
                                <p class="text-[10px] text-slate-400 font-medium mb-3 flex items-center gap-1 bg-white/5 inline-flex px-2 py-1 rounded">
                                    <span class="material-icons text-[12px] text-slate-400">calendar_month</span>
                                    {{ $edu->start_date ? \Carbon\Carbon::parse($edu->start_date)->format('M Y') : 'N/A' }} -
                                    @if($edu->is_current)
                                        <span class="text-emerald-400 font-bold ml-0.5">Present</span>
                                    @else
                                        {{ $edu->end_date ? \Carbon\Carbon::parse($edu->end_date)->format('M Y') : 'N/A' }}
                                    @endif
                                </p>
                                @if($edu->description)
                                    <p class="text-xs text-slate-300 leading-relaxed line-clamp-3">{{ $edu->description }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="flex-grow flex flex-col items-center justify-center text-center p-6 bg-white/5 rounded-2xl border border-white/5 border-dashed min-h-[200px]">
                    <span class="material-icons text-purple-400 text-3xl mb-2 bg-purple-500/10 border border-purple-500/20 p-3 rounded-full">library_books</span>
                    <p class="text-slate-300 font-semibold text-sm mb-3">No education history listed</p>
                    <a href="{{ route('applicant.profile.edit') }}" class="inline-flex items-center justify-center px-4 py-2 bg-purple-500 hover:bg-purple-600 rounded-xl text-white font-bold text-xs transition-all hover:-translate-y-0.5">
                        Add Education
                    </a>
                </div>
            @endif
        </div>

    </div>
</div>
@endsection
