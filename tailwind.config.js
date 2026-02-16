module.exports = {
    darkMode: 'class',
    content: ["./templates/**/*.html.twig", "./assets/**/*.js", "./assets/**/*.vue"],
    theme: {
        extend: {
            colors: {
                primary: "#8a2ce2",
                charcoal: "#0a0a0c",
                "dark-gray": "#121214",
                "field-border": "#3f3f46",
                // Dark mode colors
                'background-dark': '#0a0a0a',
                'surface-dark': '#121212',
                'sidebar-bg': '#0a0a0c',
                'dashboard-bg': '#0f0f12',
                // Light mode colors
                'background-light': '#f8fafc',
                'surface-light': '#ffffff',
                'sidebar-light': '#ffffff',
                'dashboard-light': '#f1f5f9',
            },
            fontFamily: {
                display: ["Inter", "sans-serif"],
            },
            boxShadow: {
                'neon': '0 0 15px rgba(138, 44, 226, 0.4)',
                'neon-intense': '0 0 30px rgba(138, 44, 226, 0.6)',
            }
        },
    },
    plugins: [
        require("@tailwindcss/forms"),
        require("@tailwindcss/container-queries"),
    ],
};
