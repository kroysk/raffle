import { ref } from '../../dist/vue.esm-browser.js';
import { apiCall } from '../../helpers.js';
const res = await fetch(new URL('Login.html', import.meta.url));
const html = await res.text();

export default {
    template: html,
    emits: ['showRegister', 'showLanding', 'showToast', 'showDashboard', 'setCurrentUser'],
    setup(props, { emit }) {
        const loginForm = ref({
            email: '',
            password: '',
        });
        const login = async () => {
            try {
                const result = await apiCall('/api/auth/login', 'POST', { 
                    email: loginForm.value.email, 
                    password: loginForm.value.password 
                });
                if (result.success) {
                    const authToken = result.data.token;
                    localStorage.setItem('authToken', authToken);
                    emit('setCurrentUser', result.data);
                    emit('showToast', {'message': 'Inicio de sesión exitoso', 'type': 'success'});
                    emit('showDashboard');
                }
            } catch (error) {
                console.log(error);
                emit('showToast', {'message': error.message, 'type': 'error'});
            }
        }
        return {
            loginForm,
            emit,
            login,
        };
    }
}