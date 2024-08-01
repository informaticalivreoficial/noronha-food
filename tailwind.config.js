/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
    'node_modules/preline/dist/*.js',
  ],
  darkMode: 'class', // or 'media' or 
  theme: {
    extend: {},
  },
  plugins: [
    require('preline/plugin'),
  ],
}

