const res = await fetch(new URL('Raffles.html', import.meta.url));
const html = await res.text();

export default {
    template: html,
    emits: ['showCreateRaffleForm'],
    setup(props, { emit }) {
        return {
            emit,
        };
    }
}