/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./assets/**/*.js",
    "./templates/**/*.html.twig",
  ],
  theme: {
    extend: {
      colors: {
        primary: '#032b35',
        secondary: '#085669',
        light: '#f5f5e9',
        lightBlue:'#145261',
      },
      fontFamily: {
        title: ['"Special Elite"', 'cursive'],
        body: ['"Roboto Slab"', 'serif'],
      }
    },
  },
  plugins: [],
}
