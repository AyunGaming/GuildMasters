/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
      "./app/Templates/**/*.{twig,js}",
      "./node_modules/flowbite/*/.js"
  ],
  theme: {
    extend: {
      colors: {
        dark: '#212529',
        clifford: '#da373d',
      }
    },
    height: {
        '100': '10rem'
    }
  },
  plugins: [
      require('flowbite-typography'),
      require('@tailwindcss/typography'),
      require('flowbite/plugin')({
          wysiwyg: true,
      }),
  ],
    darkMode: 'selector'
}

