/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./src/**/*.{js,ts,jsx,tsx}",
  ],
  theme: {
    extend: {
      colors: {
        'wp-blue': '#0073aa',
        'wp-blue-dark': '#005177',
        'wp-gray': '#f1f1f1',
        'wp-gray-dark': '#ddd',
      }
    },
  },
  plugins: [],
  corePlugins: {
    preflight: false, // Disable Tailwind's reset to avoid conflicts with WordPress admin styles
  }
}
