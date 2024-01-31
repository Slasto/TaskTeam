/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ["./**/*.{php,html}","./**/**/*.{php,html}"],
  theme: {
    extend: {},
  },
  plugins: [
    require('@tailwindcss/forms'),
  ],
}