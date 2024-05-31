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
  },
  plugins: [
      require('flowbite/plugin')
  ],
}

