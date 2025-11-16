const res = await fetch(new URL('Landing.html', import.meta.url));
const html = await res.text();

export default {
    template: html,
    emits: ['showLogin', 'showRegister'],
    setup(props, { emit }) {
        return {
            emit,
        };
    }
}