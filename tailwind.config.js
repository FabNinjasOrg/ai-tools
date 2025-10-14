import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './app/**/*.php',
    ],

    safelist: [
        // Priority colors
        'bg-red-300',
        'text-red-600',
        'bg-yellow-300',
        'text-yellow-600',
        'bg-green-300',
        'text-green-600',
        // Status colors (common ones)
        'bg-blue-100',
        'text-blue-800',
        'bg-yellow-100',
        'text-yellow-800',
        'bg-green-100',
        'text-green-800',
        'bg-gray-100',
        'text-gray-800',
        // Flash message colors
        'bg-green-50',
        'dark:bg-green-900/20',
        'border-green-400',
        'text-green-700',
        'dark:text-green-200',
        'text-green-400',
        'bg-red-50',
        'dark:bg-red-900/20',
        'border-red-400',
        'text-red-700',
        'dark:text-red-200',
        'text-red-400',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
