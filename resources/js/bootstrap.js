import _ from 'lodash';
import axios from 'axios';

window._ = _;
window.axios = axios;

// Опционально: базовый URL из .env
axios.defaults.baseURL = import.meta.env.VITE_APP_URL || '/';
