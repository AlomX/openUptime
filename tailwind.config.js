const defaultTheme = require('tailwindcss/defaultTheme');

/** @type {import('tailwindcss').Config} */
module.exports = {
    darkMode: 'class',
    
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            dropShadow: {
              'green-500': '0 0 5px rgb(34, 197, 94)',
              'orange-400': '0 0 5px rgb(251, 146, 60)',
              'red-600': '0 0 5px rgb(220, 38, 38)'
            }
        },
    },

    plugins: [require('@tailwindcss/forms')],
};
