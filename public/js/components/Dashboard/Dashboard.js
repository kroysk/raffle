import Raffles from '../Raffles/Raffles.js';
const res = await fetch(new URL('Dashboard.html', import.meta.url));
const html = await res.text();

export default {
    template: html,
    components: {
        Raffles,
    },
    emits: ['showShopWiredAccounts', 'showCreateRaffleForm', 'showRaffleDetails'],
    setup(props, { emit }) {
        const showRaffleDetails = (id) => {
            emit('showRaffleDetails', id);
        }
        return {
            emit,
            showRaffleDetails,
        }
    }
}