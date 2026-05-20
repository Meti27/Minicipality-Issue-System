import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                muni: {
                    dark:   '#355872',
                    mid:    '#7AAACE',
                    light:  '#9CD5FF',
                    cream:  '#F7F8F0',
                    darker: '#2a4760',
                },
            },
        },
    },

    plugins: [forms],
};
