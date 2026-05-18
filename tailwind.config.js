/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
    "./node_modules/flowbite/**/*.js",
  ],
  theme: {
    extend: {
      colors: {
        leaf: {
          50:  '#f0fdf4',
          100: '#dcfce7',
          200: '#D8F3DC',
          300: '#A7F3D0',
          400: '#52B788',
          500: '#3BC117',
          600: '#2D6A4F',
          700: '#1B4332',
          800: '#14532d',
          900: '#052e16',
        },
      },
      fontFamily: {
        sans:   ['Inter', 'ui-sans-serif', 'system-ui'],
        serif:  ['"Playfair Display"', 'ui-serif', 'Georgia'],
      },
      borderRadius: {
        '2xl': '1rem',
        '3xl': '1.5rem',
      },
      animation: {
        'fade-in':    'fadeIn 0.3s ease',
        'slide-up':   'slideUp 0.4s ease',
        'bounce-slow':'bounce 2s infinite',
      },
      keyframes: {
        fadeIn:  { from: { opacity: 0 }, to: { opacity: 1 } },
        slideUp: { from: { opacity: 0, transform: 'translateY(20px)' }, to: { opacity: 1, transform: 'translateY(0)' } },
      },
    },
  },
  plugins: [
    require('@tailwindcss/typography'),
    require('flowbite/plugin'),
  ],
}
