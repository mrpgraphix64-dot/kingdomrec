<div id="website-loader"
    class="fixed inset-0 z-[200] bg-white dark:bg-gray-900 flex items-center justify-center transition-opacity duration-700 ease-in-out">
    <div class="relative w-full max-w-lg px-4">
        <!-- Reverting to the Inline SVG structure from Loader.tsx -->
        <!-- Adjusted viewBox to ensure text fits well. -->
        <svg viewBox="0 0 340 100" class="w-full h-auto overflow-visible">
            <!-- Golden Arc -->
            <path 
              d="M12 85C-5 60 5 15 60 5" 
              stroke="#d4af37" 
              stroke-width="5" 
              stroke-linecap="round" 
              fill="none"
              class="animate-logo-draw"
            />
            
            <!-- Figures Group -->
            <!-- Left Blue Figure -->
            <g class="animate-logo-pop-1 origin-bottom">
              <circle cx="28" cy="42" r="6" fill="#0f2d5c" />
              <path d="M28 52C20 65 18 88 18 90H38C38 88 36 65 28 52Z" fill="#0f2d5c" />
            </g>

            <!-- Center Gold Figure -->
            <g class="animate-logo-pop-2 origin-bottom">
              <circle cx="55" cy="32" r="8" fill="#d4af37" />
              <path d="M55 44C42 60 40 88 40 90H70C70 88 68 60 55 44Z" fill="#d4af37" />
            </g>
            
            <!-- Right Blue Figure -->
            <g class="animate-logo-pop-3 origin-bottom">
              <circle cx="82" cy="42" r="6" fill="#0f2d5c" />
              <path d="M82 52C74 65 72 88 72 90H92C92 88 90 65 82 52Z" fill="#0f2d5c" />
            </g>

            <!-- Text Group -->
            <g class="animate-logo-text">
              <!-- Adjusted positions for better alignment -->
              <text x="110" y="52" fill="#0f2d5c" font-family="Manrope, sans-serif" font-weight="800" font-size="46">Kingdom</text>
              <text x="112" y="82" fill="#d4af37" font-family="Manrope, sans-serif" font-weight="300" font-size="22" letter-spacing="0.05em">Recruitments</text>
            </g>
        </svg>
    </div>
</div>
