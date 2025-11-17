import { ref, onMounted } from '../../../dist/vue.esm-browser.js';
import { apiCall, API_URL } from '../../../helpers.js';
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
        const entries = ref([]);
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
        const getRaffle = async () => {
            try {
                const result = await apiCall(`/api/raffles/${props.raffleId}`, 'GET');
                if (result.success) {
                    raffle.value = result.data;
                }
            } catch (error) {
                console.log(error);
            }
        }
        const getEntries = async () => {
            try {
                const result = await apiCall(`/api/raffles/${props.raffleId}/entries`, 'GET');
                if (result.success) {
                    entries.value = result.data;
                }
            } catch (error) {
                console.log(error);
            }
        }
        const exportCsv = async () => {
            try {
                // Obtener el token de autenticación del localStorage
                const token = localStorage.getItem('authToken');
                
                // Hacer fetch para obtener el CSV
                const response = await fetch(`${API_URL}/api/raffles/${props.raffleId}/entries/export`, {
                    method: 'GET',
                    headers: {
                        'Authorization': `Bearer ${token}`
                    }
                });

                if (!response.ok) {
                    throw new Error('Error al exportar CSV');
                }

                // Convertir la respuesta a blob
                const blob = await response.blob();
                
                // Crear un URL temporal para el blob
                const url = window.URL.createObjectURL(blob);
                
                // Crear un elemento <a> temporal para descargar el archivo
                const a = document.createElement('a');
                a.href = url;
                a.download = 'raffle_entries.csv';
                document.body.appendChild(a);
                a.click();
                
                // Limpiar
                document.body.removeChild(a);
                window.URL.revokeObjectURL(url);
            } catch (error) {
                console.log('Error al exportar CSV:', error);
            }
        }
        onMounted(() => {
            // getRaffle();
            getRaffle();
            getEntries();
        });
        return {
            emit,
            raffle,
            entries,
            getRaffle,
            getEntries,
            getStatusClass,
            exportCsv,
        };
    }
}