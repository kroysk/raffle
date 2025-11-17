const res = await fetch(new URL('CreateRaffleForm.html', import.meta.url));
const html = await res.text();

export default {
    template: html,
    emits: ['showDashboard'],
    setup(props, { emit }) {
        return {
            emit,
        };
    }
}