import { ref } from '../../dist/vue.esm-browser.js';
import { apiCall } from '../../helpers.js';
const res = await fetch(new URL('Register.html', import.meta.url));
const html = await res.text();

export default {
    template: html,
    emits: ['showLogin', 'showLanding', 'showDashboard', 'showToast', 'setCurrentUser'],
    setup(props, { emit }) {
        const registerForm = ref({
            nickname: '',
            email: '',
            password: '',
        });
        const register = async () => {
            try {
                const result = await apiCall('/api/auth/register', 'POST', { 
                    nickname: registerForm.value.nickname, 
                    email: registerForm.value.email, 
                    password: registerForm.value.password 
                });
                
                if (result.success) {
                    const authToken = result.data.token;
                    localStorage.setItem('authToken', authToken);
                    
                    emit('showToast', {'message': 'Cuenta creada exitosamente', 'type': 'success'});
                    
                    // Load user info
                    const userInfo = await apiCall('/api/auth/me');
                    emit('setCurrentUser', userInfo.data);
                    emit('showDashboard');
                }
            } catch (error) {
                console.log(error);
                emit('showToast', {'message': error.message, 'type': 'error'});
            }
        }
        return {
            emit,
            registerForm,
            register,
        };
    }
}