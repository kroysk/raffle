import { ref } from '../../../dist/vue.esm-browser.js';
import { apiCall } from '../../../helpers.js';
const res = await fetch(new URL('ConnectAccountForm.html', import.meta.url));
const html = await res.text();

export default {
    template: html,
    emits: ['showShopWiredAccounts'],
    setup(props, { emit }) {
        const ConnectAccountForm = ref({
            name: '',
            shopwiredApiKey: '',
            shopwiredApiSecret: '',
            shopwiredWebhooksSecret: '',
        });
        const connectAccount = async () => {
            try {
                const result = await apiCall('/api/shopwired/accounts', 'POST', {
                    name: ConnectAccountForm.value.name,
                    shopwired_api_key: ConnectAccountForm.value.shopwiredApiKey,
                    shopwired_api_secret: ConnectAccountForm.value.shopwiredApiSecret,
                    shopwired_webhooks_secret: ConnectAccountForm.value.shopwiredWebhooksSecret,
                });
                if (result.success) {
                    emit('showToast', {'message': 'Account connected successfully', 'type': 'success'});
                    emit('showShopWiredAccounts');
                }
            } catch (error) {
                console.log(error);
                emit('showToast', {'message': error.message, 'type': 'error'});
            }
        }
        return {
            ConnectAccountForm,
            emit,
            connectAccount,
        }
    }
}