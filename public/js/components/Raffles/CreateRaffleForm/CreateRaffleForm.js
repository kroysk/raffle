import { ref, onMounted } from '../../../dist/vue.esm-browser.js';
import { apiCall } from '../../../helpers.js';
const res = await fetch(new URL('CreateRaffleForm.html', import.meta.url));
const html = await res.text();

export default {
    template: html,
    emits: ['showDashboard', 'showToast'],
    setup(props, { emit }) {
        const raffleForm = ref({
            accountId: '',
            title: '',
            maxEntries: '',
        });
        const shopwiredAccounts = ref([]);
        const createRaffle = async () => {
            try {
                const result = await apiCall('/api/raffles', 'POST', {
                    shopwired_account_id: raffleForm.value.accountId,
                    title: raffleForm.value.title,
                    max_entries: raffleForm.value.maxEntries,
                });
                if (result.success) {
                    emit('showToast', {'message': 'Raffle created successfully', 'type': 'success'});
                    emit('showDashboard');
                }
            } catch (error) {
                console.log(error);
                emit('showToast', {'message': error.message, 'type': 'error'});
            }
        }
        const getShopwiredAccounts = async () => {
            try {
                const result = await apiCall('/api/shopwired/accounts');
                if (result.success) {
                    shopwiredAccounts.value = result.data;
                }
            } catch (error) {
                console.log(error);
                emit('showToast', {'message': error.message, 'type': 'error'});
            }
        }
        onMounted(() => {
            getShopwiredAccounts();
        });
        return {
            emit,
            raffleForm,
            shopwiredAccounts,
            createRaffle,
            getShopwiredAccounts,
        };
    }
}