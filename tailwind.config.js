module.exports = {
    darkMode: 'class',
    content: ["./templates/**/*.html.twig", "./assets/**/*.js", "./assets/**/*.vue"],
    theme: {
        extend: {
            colors: {
                primary: '#7C3AED',
                'background-dark': '#0a0a0a',
                'surface-dark': '#121212',
                'sidebar-bg': '#0a0a0c',
                'dashboard-bg': '#0f0f12',
                charcoal: '#0a0a0a',
                'dark-gray': '#121212',
            },
            fontFamily: {
                display: ['Outfit', 'sans-serif'],
                sans: ['Plus Jakarta Sans', 'sans-serif'],
            },
            boxShadow: {
                'neon': '0 0 15px rgba(124, 58, 237, 0.4)',
                'neon-intense': '0 0 30px rgba(124, 58, 237, 0.6)',
            }
        },
    },
    plugins: [
        require("@tailwindcss/forms"),
        require("@tailwindcss/container-queries"),
    ],
};
