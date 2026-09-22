/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    theme: {
        extend: {
            colors: {
                primary: "#0F1D33",
                "primary-dark": "#0A1324",
                "background-light": "#F9FAFB",
                "background-dark": "#0F172A",
                "surface-light": "#FFFFFF",
                "surface-dark": "#1E293B",
                "navy-dark": "#111827",
                "kingdom-red": "#0F1D33",
                "kingdom-navy": "#0F1D33",
                "kingdom-gold": "#F8B803",
                "kingdom-bg": "#F8FAFC",
            },
            fontFamily: {
                display: ["Inter", "sans-serif"],
                body: ["Roboto", "sans-serif"],
            },
            keyframes: {
                ring: {
                    '0%, 100%': { transform: 'rotate(0deg)' },
                    '10%, 30%, 50%, 70%, 90%': { transform: 'rotate(-15deg)' },
                    '20%, 40%, 60%, 80%': { transform: 'rotate(15deg)' },
                },
                ripple: {
                    '0%': { transform: 'scale(1)', opacity: '0.5' },
                    '100%': { transform: 'scale(2.5)', opacity: '0' },
                },
                'pop-in': {
                    '0%': { transform: 'scale(0.8)', opacity: '0' },
                    '60%': { transform: 'scale(1.1)', opacity: '1' },
                    '100%': { transform: 'scale(1)', opacity: '1' },
                },
                'fade-in-up': {
                    '0%': { opacity: '0', transform: 'translateY(1rem)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                'confetti-fall': {
                    '0%': { transform: 'translateY(-10vh) rotateZ(0deg)', opacity: 1 },
                    '100%': { transform: 'translateY(100vh) rotateZ(720deg)', opacity: 0 }
                }
            },
            animation: {
                'bell-ring': 'ring 1s cubic-bezier(0.36, 0.07, 0.19, 0.97) both',
                'sound-wave': 'ripple 1s cubic-bezier(0, 0, 0.2, 1) forwards',
                'pop-in': 'pop-in 0.5s cubic-bezier(0.25, 1, 0.5, 1.25) forwards',
                'fade-in-up': 'fade-in-up 0.5s ease-out forwards',
            },
        },
    },
    plugins: [
        require('@tailwindcss/forms'),
    ],
};
