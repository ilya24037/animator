import _ from 'lodash';
import axios from 'axios';

window._ = _;
window.axios = axios;

// Настройка axios
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// CSRF токен для защиты
let token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

// Опционально: базовый URL из .env
axios.defaults.baseURL = import.meta.env.VITE_APP_URL || '/';