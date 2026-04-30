import './bootstrap';

import Alpine from 'alpinejs';
import persist from '@alpinejs/persist';
import UppyUploadManager from './uppy-upload';
import '@uppy/core/css/style.css';
import '@uppy/dashboard/css/style.css';

window.Alpine = Alpine;
window.UppyUploadManager = UppyUploadManager;

Alpine.plugin(persist);
Alpine.start();
