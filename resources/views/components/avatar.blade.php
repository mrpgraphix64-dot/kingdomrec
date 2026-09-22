@props([
    'user' => null,
    'name' => null,
    'image' => null,
    'class' => 'w-12 h-12 rounded-full'
])

@php
    // Extract initials or name
    $resolvedName = $name;
    if (!$resolvedName && $user) {
        if (is_object($user)) {
            $resolvedName = $user->name ?? $user->company_name ?? null;
        } elseif (is_array($user)) {
            $resolvedName = $user['name'] ?? $user['company_name'] ?? null;
        }
    }

    $initials = '';
    if (!empty($resolvedName)) {
        $words = preg_split('/\s+/', trim($resolvedName));
        if (count($words) >= 2) {
            $initials = strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        } else if (count($words) >= 1) {
            $initials = strtoupper(substr($words[0], 0, 1));
        }
    }

    // Resolve Image URL
    $imageUrl = null;
    $imageSource = $image;
    if (!$imageSource && $user) {
        if (is_object($user)) {
            if (isset($user->profile_photo_url)) {
                $imageSource = $user->profile_photo_url;
            } elseif (isset($user->profile_photo_path)) {
                $imageSource = $user->profile_photo_path;
            } elseif (isset($user->image)) {
                $imageSource = $user->image;
            }
        } elseif (is_array($user)) {
            $imageSource = $user['profile_photo_url'] ?? $user['profile_photo_path'] ?? $user['image'] ?? $user['img'] ?? null;
        }
    }

    if (!empty($imageSource)) {
        // If it's a UI Avatar URL, treat it as empty/null to use our premium native initials instead
        if (strpos($imageSource, 'ui-avatars.com') !== false) {
            $imageSource = null;
        }
    }

    if (!empty($imageSource)) {
        if (str_starts_with($imageSource, 'http://') || str_starts_with($imageSource, 'https://')) {
            $imageUrl = $imageSource;
        } else {
            $path = ltrim($imageSource, '/');
            if (str_starts_with($path, 'storage/')) {
                $imageUrl = asset($path);
            } elseif (str_starts_with($path, 'media/')) {
                $imageUrl = asset($path);
            } else {
                if (str_starts_with($path, 'photos/')) {
                    $imageUrl = asset('media/' . $path);
                } else {
                    $imageUrl = asset('media/' . $path);
                }
            }
        }
    }

    // Ensure rounded-full and object-cover are present in img classes for premium presentation
    $imgClass = $class;
    if (strpos($imgClass, 'rounded-full') === false) {
        $imgClass .= ' rounded-full';
    }
    if (strpos($imgClass, 'object-cover') === false) {
        $imgClass .= ' object-cover';
    }

    // Fallback classes should have flex, items-center, justify-center, font-semibold, etc.
    $fallbackClass = $imgClass . ' bg-[#0F1D33] text-white flex items-center justify-center font-semibold select-none';
    
    // Check if name was totally unavailable
    $hasInitials = !empty($initials);
@endphp

@if ($imageUrl)
    <img src="{{ $imageUrl }}" 
         alt="{{ $resolvedName ?? 'Avatar' }}" 
         class="{{ $imgClass }}" 
         onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');" />
    
    <div class="hidden {{ $fallbackClass }}">
        @if ($hasInitials)
            {{ $initials }}
        @else
            <svg class="w-1/2 h-1/2 text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0 1 12.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 1 1-8 0 4 4 0 0 1 8 0z" />
            </svg>
        @endif
    </div>
@else
    <div class="{{ $fallbackClass }}">
        @if ($hasInitials)
            {{ $initials }}
        @else
            <svg class="w-1/2 h-1/2 text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0 1 12.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 1 1-8 0 4 4 0 0 1 8 0z" />
            </svg>
        @endif
    </div>
@endif
