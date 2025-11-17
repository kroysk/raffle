import { ref, onMounted } from '../../../dist/vue.esm-browser.js';
import { apiCall } from '../../../helpers.js';
const res = await fetch(new URL('RaffleDetails.html', import.meta.url));
const html = await res.text();

export default {
    template: html,
    props: {
        raffleId: {
            type: Number,
            required: true,
        },
    },
    emits: ['showDashboard'],
    setup(props, { emit }) {
        const raffle = ref({
            title: 'Test Raffle',
            status: 'active',
            max_entries: 100,
            total_entries: 42,
        });
        const entries = ref([
            {
                raffle_number: '1234567890',
                customer_name: 'John Doe',
                customer_address: '123 Main St, Anytown, USA',
                customer_email: 'john.doe@example.com',
            },
            {
                raffle_number: '1234567890',
                customer_name: 'Jane Doe',
                customer_address: '123 Main St, Anytown, USA',
                customer_email: 'jane.doe@example.com',
            },
            
            {
                raffle_number: '1234567890',
                customer_name: 'John Doe',
                customer_address: '123 Main St, Anytown, USA',
                customer_email: 'john.doe@example.com',
            },
            {
                raffle_number: '1234567890',
                customer_name: 'Jane Doe',
                customer_address: '123 Main St, Anytown, USA',
                customer_email: 'jane.doe@example.com',
            },
        ]);
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
        // const getRaffle = async () => {
        //     try {
        //         const result = await apiCall(`/api/raffles/${props.raffleId}`, 'GET');
        //         if (result.success) {
        //             raffle.value = result.data;
        //         }
        //     } catch (error) {
        //         console.log(error);
        //     }
        // }
        onMounted(() => {
            // getRaffle();
        });
        return {
            emit,
            raffle,
            getStatusClass,
            entries,
        };
    }
}