import { ref, onMounted } from './dist/vue.esm-browser.js';
import { apiCall } from './helpers.js';
import Landing from './components/Landing/Landing.js';
import Login from './components/Login/Login.js';
import Register from './components/Register/Register.js';
import Dashboard from './components/Dashboard/Dashboard.js';
// import Navbar from './components/Navbar/Navbar.js';
import Toast from './components/Toast/Toast.js';

const res = await fetch(new URL('app.html', import.meta.url));
const html = await res.text();

export default {
    template: html,
    components: {
        Landing,
        Login,
        Register,
        Toast,
        Dashboard,
        // Navbar,
    },
    setup() {
        const loginView = ref(false);
        const registerView = ref(false);
        const dashboardView = ref(false);
        const navbarView = ref(false);
        const landingView = ref(true);

        const toast = ref({
            message: '',
            type: '',
            show: false,
        });


        function showToast(data){
            console.log('showToast', data);
            toast.value = {
                message: data.message,
                type: data.type,
                show: true,
            };
            setTimeout(() => {
                hideToast();
            }, 3000);
        }
        function hideToast(){
            toast.value = {
                message: '',
                type: '',
                show: false,
            };
        }

        const currentUser = ref(null)
        function setCurrentUser(user){
            currentUser.value = user;
        }
        // Navigation Funtions
        function hideAll(){
            loginView.value = false;
            registerView.value = false;
            dashboardView.value = false;
            navbarView.value = false;
            landingView.value = false;
        }
        function showLanding(){
            hideAll();
            hideNavbar();
            landingView.value = true;
        }
        function showLogin(){
            hideAll();
            hideNavbar();
            loginView.value = true;
        }
        function showRegister(){
            console.log('showRegister');
            hideAll();
            hideNavbar();
            registerView.value = true;
        }
        function showDashboard(){
            hideAll();
            hideNavbar();
            dashboardView.value = true;
        }
        function showNavbar(){
            navbarView.value = true;
        }
        function hideNavbar(){
            navbarView.value = false;
        }
        // Mount Functions
        const verifyToken = async () => {
            try {
                const result = await apiCall('/api/auth/me');
                
                if (result.success) {
                    currentUser.value = result.data;
                    // document.getElementById('userName').textContent = `Hola, ${result.data.name}`;
                    showDashboard();
                }
            } catch (error) {
                localStorage.removeItem('authToken');
                currentUser.value = null;
                showLanding();
            }
        }
        onMounted(() => {
            const authToken = localStorage.getItem('authToken');
            if (authToken) {
                verifyToken();
            } else {
                showLanding();
            }
        });
        return {
            loginView,
            registerView,
            dashboardView,
            navbarView,
            landingView,
            toast,
            showToast,
            hideToast,
            showLanding,
            showLogin,
            showRegister,
            showDashboard,
            setCurrentUser,
            showNavbar,
            hideNavbar,
        }
    }
}