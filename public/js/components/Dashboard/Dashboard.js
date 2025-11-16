const res = await fetch(new URL('Dashboard.html', import.meta.url));
const html = await res.text();

export default {
    template: html,
    setup() {
        return {
            message: 'Dashboard'
        }
    }
}