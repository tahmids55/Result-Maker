import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    darkMode: 'class',
    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', 'Figtree', ...defaultTheme.fontFamily.sans],
                mono: ['JetBrains Mono', ...defaultTheme.fontFamily.mono],
                display: ['Outfit', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Preserve existing colors for backward compat
                primary: { DEFAULT: '#4F46E5', dark: '#4338CA', light: '#818CF8' },
                sidebar: '#0F1117',

                // Design system tokens
                surface: {
                    DEFAULT: '#FFFFFF',
                    raised:  '#F8F9FB',
                    sunken:  '#F1F3F5',
                },
                ink: {
                    DEFAULT: '#1A1D23',
                    muted:   '#6B7280',
                    faint:   '#9CA3AF',
                },
                accent: {
                    DEFAULT: '#4F46E5',
                    hover:   '#4338CA',
                    subtle:  '#EEF2FF',
                    ring:    'rgba(79,70,229,0.25)',
                },
            },
            borderRadius: {
                'sm': '6px',
                'md': '8px',
                'lg': '12px',
                'xl': '16px',
            },
            boxShadow: {
                'card': '0 1px 3px rgba(0,0,0,0.04), 0 1px 2px rgba(0,0,0,0.06)',
                'elevated': '0 4px 12px rgba(0,0,0,0.08)',
                'modal': '0 20px 60px rgba(0,0,0,0.15)',
            },
            keyframes: {
                'fade-in':    { from: { opacity: 0 }, to: { opacity: 1 } },
                'slide-up':   { from: { opacity: 0, transform: 'translateY(8px)' }, to: { opacity: 1, transform: 'translateY(0)' } },
                'pulse-save': { '0%,100%': { opacity: 1 }, '50%': { opacity: 0.5 } },
            },
            animation: {
                'fade-in':    'fade-in 0.2s ease-out',
                'slide-up':   'slide-up 0.25s ease-out',
                'pulse-save': 'pulse-save 1s ease-in-out infinite',
            },
        },
    },
    plugins: [],
};
