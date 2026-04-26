import './bootstrap';

import ApexCharts from 'apexcharts';
import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

import '../css/app.css';

window.ApexCharts = ApexCharts;

const appName = import.meta.env.VITE_APP_NAME || 'Nezor';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });
        
        app.use(plugin);
        app.use(ZiggyVue);
        
        app.config.errorHandler = (err, instance, info) => {
            console.error('Vue Error:', err);
            console.error('Component:', instance);
            console.error('Info:', info);
        };
        
        app.mount(el);
        
        return app;
    },
    progress: {
        color: '#01696F',
    },
});