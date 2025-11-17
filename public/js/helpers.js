export const API_URL = window.location.origin;
export const apiCall = async (endpoint, method = 'GET', data = null) => {
    const options = {
        method,
        headers: {
            'Content-Type': 'application/json',
        }
    };

    let authToken = localStorage.getItem('authToken');
    if (authToken) {
        options.headers['Authorization'] = `Bearer ${authToken}`;
    }

    if (data && (method === 'POST' || method === 'PUT' || method === 'PATCH')) {
        options.body = JSON.stringify(data);
    }

    try {
        const response = await fetch(`${API_URL}${endpoint}`, options);
        const result = await response.json();
        
        if (!response.ok) {
            throw new Error(result.message || 'Error en la petición');
        }
        
        return result;
    } catch (error) {
        console.error('API Error:', error);
        throw error;
    }
}