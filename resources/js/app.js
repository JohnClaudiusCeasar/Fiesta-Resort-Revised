import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import VueApexCharts from "vue3-apexcharts";
import '../css/admin.css';

createInertiaApp({
    resolve: (name) => {
        const pages = import.meta.glob('../pages/**/*.vue', { eager: true })
        return pages[`../pages/${name}.vue`]
    },
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(VueApexCharts)
            .mount(el)
    },
})
