import { ref, onMounted } from './dist/vue.esm-browser.js';
import { apiCall } from './helpers.js';
import Landing from './components/Landing/Landing.js';
import Login from './components/Login/Login.js';
import Register from './components/Register/Register.js';
import Dashboard from './components/Dashboard/Dashboard.js';
import Navbar from './components/Navbar/Navbar.js';
import Toast from './components/Toast/Toast.js';
import ShopWiredAccounts from './components/ShopWireAccounts/ShopWireAccounts.js';
import ConnectAccountForm from './components/ShopWireAccounts/ConnectAccountForm/ConnectAccountForm.js';
import CreateRaffleForm from './components/Raffles/CreateRaffleForm/CreateRaffleForm.js';
import RaffleDetails from './components/Raffles/RaffleDetails/RaffleDetails.js';
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
        Navbar,
        ShopWiredAccounts,
        ConnectAccountForm,
        CreateRaffleForm,
        RaffleDetails,
    },
    setup() {
        const loginView = ref(false);
        const registerView = ref(false);
        const dashboardView = ref(false);
        const navbarView = ref(false);
        const landingView = ref(true);
        const shopWiredAccountsView = ref(false);
        const connectAccountFormView = ref(false);
        const createRaffleFormView = ref(false);
        const raffleDetailsView = ref(false);
        const currentRaffleId = ref(null);
        const toast = ref({
            message: '',
            type: '',
            show: false,
        });


        const showToast = (data) => {
            toast.value = {
                message: data.message,
                type: data.type,
                show: true,
            };
            setTimeout(() => {
                hideToast();
            }, 3000);
        }
        const hideToast = () => {
            toast.value = {
                message: '',
                type: '',
                show: false,
            };
        }

        const currentUser = ref(null)
        const nickname = ref('');
        const setCurrentUser = (user) => {
            currentUser.value = user;
            nickname.value = user.nickname;
        }
        const logout = () => {
            localStorage.removeItem('authToken');
            currentUser.value = null;
            nickname.value = '';
            showToast({ message: 'Session closed successfully', type: 'success' });
            showLanding();
        }
        // Navigation Funtions
        const hideAll = () => {
            loginView.value = false;
            registerView.value = false;
            dashboardView.value = false;
            navbarView.value = false;
            landingView.value = false;
            shopWiredAccountsView.value = false;
            connectAccountFormView.value = false;
            createRaffleFormView.value = false;
            raffleDetailsView.value = false;
        }
        const showLanding = () => {
            hideAll();
            hideNavbar();
            landingView.value = true;
        }
        const showLogin = () => {
            hideAll();
            hideNavbar();
            loginView.value = true;
        }
        const showRegister = () => {
            hideAll();
            hideNavbar();
            registerView.value = true;
        }
        const showDashboard = () => {
            hideAll();
            showNavbar();
            dashboardView.value = true;
        }
        const showNavbar = () => {
            navbarView.value = true;
        }
        const hideNavbar = () => {
            navbarView.value = false;
        }
        const showShopWiredAccounts = () => {
            hideAll();
            showNavbar();
            shopWiredAccountsView.value = true;
        }
        const showConnectAccountForm = () => {
            hideAll();
            showNavbar();
            connectAccountFormView.value = true;
        }
        const showCreateRaffleForm = () => {
            hideAll();
            showNavbar();
            createRaffleFormView.value = true;
        }
        const showRaffleDetails = (id) => {
            hideAll();
            showNavbar();
            currentRaffleId.value = id;
            raffleDetailsView.value = true;
        }
        // Mount Functions
        const verifyToken = async () => {
            try {
                const result = await apiCall('/api/auth/me');

                if (result.success) {
                    setCurrentUser(result.data);
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
            shopWiredAccountsView,
            connectAccountFormView,
            createRaffleFormView,
            raffleDetailsView,
            currentRaffleId,
            toast,
            showToast,
            hideToast,
            showLanding,
            showLogin,
            showRegister,
            showDashboard,
            showShopWiredAccounts,
            showConnectAccountForm,
            showCreateRaffleForm,
            showRaffleDetails,
            setCurrentUser,
            showNavbar,
            hideNavbar,
            nickname,
            logout,
        }
    }
}