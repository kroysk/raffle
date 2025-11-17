import { ref, onMounted } from '../../dist/vue.esm-browser.js';
import { apiCall } from '../../helpers.js';
const res = await fetch(new URL('ShopWireAccounts.html', import.meta.url));
const html = await res.text();

export default {
    template: html,
    emits: ['showDashboard', 'showConnectAccountForm'],
    setup(props, { emit }) {
        const accounts = ref([]);
        const getAccounts = async () => {
            try {
                const result = await apiCall('/api/shopwired/accounts', 'GET');
                if (result.success) {
                    accounts.value = result.data;
                }
            } catch (error) {
                console.log(error);
            }
        }
        const disconnectAccount = async (id) => {
            try {
                const result = await apiCall(`/api/shopwired/accounts/${id}`, 'DELETE');
                if (result.success) {
                    getAccounts();
                }
            } catch (error) {
                console.log(error);
                emit('showToast', { 'message': error.message, 'type': 'error' });
            }
        }
        onMounted(() => {
            getAccounts();
            console.log('ShopWireAccounts mounted');
        });
        return {
            emit,
            accounts,
            getAccounts,
            disconnectAccount,
        }
    }
}