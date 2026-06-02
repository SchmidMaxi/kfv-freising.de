/** @type {import('tailwindcss').Config} */
export default {
  darkMode: 'class',
  content: [
    './Resources/Private/**/*.html',
    './Resources/Private/**/*.js',
    './ContentBlocks/**/*.html',
    '../feuerwehren/Resources/Private/**/*.html',
  ],
  theme: {
    container: {
      center: true,
      padding: '1.5rem',
      screens: {
        '2xl': '1320px',
      },
    },
    extend: {
      fontFamily: {
        heading: ['Oswald', 'sans-serif'],
        body: ['Roboto', 'sans-serif'],
        sans: [
          'Roboto',
          'system-ui',
          '-apple-system',
          'BlinkMacSystemFont',
          'Segoe UI',
          'Helvetica Neue',
          'Arial',
          'sans-serif',
        ],
        mono: [
          'SFMono-Regular',
          'Menlo',
          'Monaco',
          'Consolas',
          'Liberation Mono',
          'Courier New',
          'monospace',
        ],
      },
      colors: {
        border: 'hsl(var(--border))',
        input: 'hsl(var(--input))',
        ring: 'hsl(var(--ring))',
        background: 'hsl(var(--background))',
        foreground: 'hsl(var(--foreground))',
        primary: {
          DEFAULT: 'hsl(var(--primary))',
          foreground: 'hsl(var(--primary-foreground))',
        },
        secondary: {
          DEFAULT: 'hsl(var(--secondary))',
          foreground: 'hsl(var(--secondary-foreground))',
        },
        destructive: {
          DEFAULT: 'hsl(var(--destructive))',
          foreground: 'hsl(var(--destructive-foreground))',
        },
        muted: {
          DEFAULT: 'hsl(var(--muted))',
          foreground: 'hsl(var(--muted-foreground))',
        },
        accent: {
          DEFAULT: 'hsl(var(--accent))',
          foreground: 'hsl(var(--accent-foreground))',
        },
        card: {
          DEFAULT: 'hsl(var(--card))',
          foreground: 'hsl(var(--card-foreground))',
        },
        fire: {
          red: 'hsl(var(--fire-red))',
          'red-light': 'hsl(var(--fire-red-light))',
          'red-dark': 'hsl(var(--fire-red-dark))',
          orange: 'hsl(var(--fire-orange))',
          yellow: 'hsl(var(--fire-yellow))',
        },
        charcoal: {
          DEFAULT: 'hsl(var(--charcoal))',
          dark: 'hsl(var(--charcoal-dark))',
          light: 'hsl(var(--charcoal-light))',
          muted: 'hsl(var(--charcoal-muted))',
        },
        cream: 'hsl(var(--cream))',
        'warm-gray': 'hsl(var(--warm-gray))',
        gray: {
          100: 'hsl(var(--gray-100))',
          200: 'hsl(var(--gray-200))',
          300: 'hsl(var(--gray-300))',
          500: 'hsl(var(--gray-500))',
          900: 'hsl(var(--gray-900))',
        },
        'surface-dark': {
          DEFAULT: 'hsl(var(--surface-dark))',
          foreground: 'hsl(var(--surface-dark-foreground))',
        },
      },
      borderRadius: {
        lg: 'var(--radius)',
        md: 'calc(var(--radius) - 2px)',
        sm: 'calc(var(--radius) - 4px)',
      },
      keyframes: {
        'accordion-down': {
          from: { height: '0' },
          to: { height: 'var(--accordion-content-height)' },
        },
        'accordion-up': {
          from: { height: 'var(--accordion-content-height)' },
          to: { height: '0' },
        },
        'fade-in': {
          from: { opacity: '0', transform: 'translateY(20px)' },
          to: { opacity: '1', transform: 'translateY(0)' },
        },
        'slide-in-right': {
          from: { opacity: '0', transform: 'translateX(50px)' },
          to: { opacity: '1', transform: 'translateX(0)' },
        },
        pulse: {
          '0%, 100%': { opacity: '1' },
          '50%': { opacity: '0.5' },
        },
      },
      animation: {
        'accordion-down': 'accordion-down 0.2s ease-out',
        'accordion-up': 'accordion-up 0.2s ease-out',
        'fade-in': 'fade-in 0.6s ease-out forwards',
        'slide-in-right': 'slide-in-right 0.5s ease-out forwards',
        pulse: 'pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite',
      },
      boxShadow: {
        '2xs': 'var(--shadow-2xs)',
        xs: 'var(--shadow-xs)',
        sm: 'var(--shadow-sm)',
        DEFAULT: 'var(--shadow)',
        md: 'var(--shadow-md)',
        lg: 'var(--shadow-lg)',
        xl: 'var(--shadow-xl)',
        '2xl': 'var(--shadow-2xl)',
      },
    },
  },
  safelist: [
    {
      pattern: /grid-cols-[1-4]/,
      variants: ['md', 'xl'],
    },
    {
      pattern: /w-(1|2|3|4|5|6|7|8|9|10|11)\/(12)/,
      variants: ['md', 'lg', 'xl'],
    },
    {
      pattern: /w-(full|auto)/,
      variants: ['md', 'lg', 'xl'],
    },
    {
      pattern: /gap-(x|y)-(2|4|6|8|10|12)/,
    },
    {
      pattern: /items-(start|center|end)/,
    },
    'bg-white',
    'bg-secondary',
    'bg-accent/10',
    'bg-surface-dark',
    'bg-muted',
    'text-surface-dark-foreground',
  ],
  plugins: [],
};
