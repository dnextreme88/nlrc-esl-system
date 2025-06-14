import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm';
import axios from 'axios';
import Clipboard from '@ryangjchandler/alpine-clipboard';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
import '../../vendor/masmerise/livewire-toaster/resources/js';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allow your team to quickly build robust real-time web applications.
 */
window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
});

window.darkModeSwitcher = function() {
    return {
        switchOn: JSON.parse(localStorage.getItem('nlrcEslProjectIsDarkMode')) || false,
        switchTheme() {
            if (this.switchOn) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }

            localStorage.setItem('nlrcEslProjectIsDarkMode', this.switchOn);

            console.log('Dark mode:', this.switchOn);
        },
        init() {
            this.switchTheme();
        }
    }
}

// REF: https://livewire.laravel.com/docs/installation#manually-bundling-livewire-and-alpine
Alpine.plugin(Clipboard);
Livewire.start();
