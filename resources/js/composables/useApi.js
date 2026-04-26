import axios from 'axios';

export function useApi() {
    const client = axios.create({
        baseURL: '/api',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        },
        withCredentials: true,
    });

    // Reutiliza o CSRF token do meta tag
    const csrfToken = document.head.querySelector('meta[name="csrf-token"]')?.content;
    if (csrfToken) {
        client.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;
    }

    // Lê o cookie XSRF-TOKEN e envia como header X-XSRF-TOKEN
    const xsrfToken = document.cookie.split('; ').find(row => row.startsWith('XSRF-TOKEN='));
    if (xsrfToken) {
        client.defaults.headers.common['X-XSRF-TOKEN'] = decodeURIComponent(xsrfToken.split('=')[1]);
    }

    return {
        get: (url, config = {}) => client.get(url, config).then(r => r.data),
        post: (url, data = {}, config = {}) => client.post(url, data, config).then(r => r.data),
        put: (url, data = {}, config = {}) => client.put(url, data, config).then(r => r.data),
        patch: (url, data = {}, config = {}) => client.patch(url, data, config).then(r => r.data),
        delete: (url, config = {}) => client.delete(url, config).then(r => r.data),
    };
}

/**
 * Retorna a data local de hoje no formato YYYY-MM-DD.
 * new Date().toISOString() retorna UTC, o que causa deslocamento de fuso.
 */
export function localDateStr(date = new Date()) {
    const d = new Date(date);
    const year = d.getFullYear();
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const day = String(d.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}
