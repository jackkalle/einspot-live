/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    // "./resources/**/*.vue", // Uncomment if using Vue components
    "./app/View/Components/**/*.php", // For Blade components if any
    "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php", // For Laravel paginator styling
  ],
  theme: {
    extend: {
      fontFamily: {
        poppins: ['Poppins', 'sans-serif'],
        inter: ['Inter', 'sans-serif'],
      },
      colors: {
        'einspot-red': {
          DEFAULT: '#E53935', // A general red, adjust to specific guideline hex if available
          '50': '#FDECEA',
          '100': '#FBDDE0',
          '200': '#F8BCC1',
          '300': '#F49BA3',
          '400': '#F07B84',
          '500': '#EC5A65',
          '600': '#E53935', // This is a common "red"
          '700': '#D32F2F',
          '800': '#C62828',
          '900': '#B71C1C',
        },
        // Example: if the guideline red was #FF0000, you'd put that as DEFAULT
        // For now, using a nice shade of red. The guideline mentioned "Red background"
        // The original React components used 'red-600' which is #dc2626
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms'), // Useful for styling forms consistently
    // require('@tailwindcss/typography'), // For styling blocks of prose/HTML from WYSIWYG
    // require('@tailwindcss/aspect-ratio'), // If needed for image/video aspect ratios
  ],
}
