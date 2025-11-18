import { ref, onMounted } from '../../dist/vue.esm-browser.js';
import { apiCall, API_URL } from '../../helpers.js';
const res = await fetch(new URL('ShopWireAccounts.html', import.meta.url));
const html = await res.text();

export default {
    template: html,
    emits: ['showDashboard', 'showConnectAccountForm', 'showToast'],
    setup(props, { emit }) {
        const accounts = ref([]);
        const getAccounts = async () => {
            try {
                const result = await apiCall('/api/shopwired/accounts', 'GET');
                if (result.success) {
                    let data = result.data.map(data => {
                        return {
                            ...data,
                            webhookUrl: `${API_URL}/api/raffles/webhook/${data.id}`
                        }
                    })
                    accounts.value = data;
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
        const copyToClipboard = async (url) => {
            try {
                await navigator.clipboard.writeText(url);
                emit('showToast', { 'message': 'URL copied to clipboard!', 'type': 'success' });
            } catch (error) {
                console.error('Failed to copy:', error);
                emit('showToast', { 'message': 'Failed to copy URL', 'type': 'error' });
            }
        }
        onMounted(() => {
            getAccounts();
        });
        return {
            emit,
            accounts,
            getAccounts,
            disconnectAccount,
            copyToClipboard,
        }
    }
}