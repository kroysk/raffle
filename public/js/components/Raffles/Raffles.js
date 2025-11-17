import { ref, onMounted } from '../../dist/vue.esm-browser.js';
import { apiCall } from '../../helpers.js';
const res = await fetch(new URL('Raffles.html', import.meta.url));
const html = await res.text();

export default {
    template: html,
    emits: ['showCreateRaffleForm', 'showRaffleDetails'],
    setup(props, { emit }) {
        const raffles = ref([]);
        const getRaffles = async () => {
            try {
                const result = await apiCall('/api/raffles', 'GET');
                if (result.success) {
                    raffles.value = result.data;
                }
            } catch (error) {
                console.log(error);
            }
        }
        const getStatusClass = (status) => {
            switch (status) {
                case 'active':
                    return 'bg-green-500 text-white';
                case 'completed':
                    return 'bg-blue-500 text-white';
                default:
                    return 'bg-gray-500 text-white';
            }
        }
        const showRaffleDetails = (id) => {
            emit('showRaffleDetails', id);
        }
        onMounted(() => {
            getRaffles();
        });
        return {
            emit,
            raffles,
            getRaffles,
            getStatusClass,
            showRaffleDetails,
        };
    }
}