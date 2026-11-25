/** Menyiapkan klien HTTP dengan penanda request AJAX dan dukungan CSRF same-origin. */
import axios from 'axios';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
