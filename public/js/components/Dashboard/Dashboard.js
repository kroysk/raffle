import Raffles from '../Raffles/Raffles.js';
const res = await fetch(new URL('Dashboard.html', import.meta.url));
const html = await res.text();

export default {
    template: html,
    components: {
        Raffles,
    },
    emits: ['showShopWiredAccounts', 'showCreateRaffleForm'],
    setup(props, { emit }) {
        return {
            emit,
        }
    }
}