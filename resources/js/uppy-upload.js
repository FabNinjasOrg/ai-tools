import { Uppy } from '@uppy/core';
import Dashboard from '@uppy/dashboard';

class UppyUploadManager {
    constructor(config = {}) {
        const {
            container,
            inline = false,
            hideUploadButton = null,
            onUpload = null,
            onSuccess = null,
            onError = null,
            height = null
        } = config;

        this.container = container;
        this.hideUploadButton = hideUploadButton;
        this.onUpload = onUpload;
        this.onSuccess = onSuccess;
        this.onError = onError;
        this.height = height;
        this.uppy = null;
        this.isUploading = false;

        this.init(inline);
    }

    init(inline) {
        const restrictions = {
            maxFileSize: 1024 * 1024 * 1024, // 1 GB max per file
            maxNumberOfFiles: null, // No limit on number of files
            allowedFileTypes: ['.zip', 'image/jpeg', 'image/jpg', 'image/png', 'image/webp']
        };

        // Create Uppy instance
        this.uppy = new Uppy({
            autoProceed: false,
            restrictions
        });

        // Configure Dashboard
        const shouldHideUploadButton = this.hideUploadButton !== null
            ? this.hideUploadButton
            : (this.onUpload ? false : true);

        this.uppy.use(Dashboard, {
            target: this.container,
            inline: inline,
            width: '100%',
            height: this.height !== null ? this.height : (inline ? 400 : 450),
            hideUploadButton: shouldHideUploadButton,
            proudlyDisplayPoweredByUppy: false,
            showProgressDetails: true,
            note: 'Upload ZIP files or photos (png, jpg, jpeg, webp). Max 1 GB per file.',
            trigger: inline ? null : null,
            closeModalOnClickOutside: !inline,
            closeAfterFinish: false
        });

        if (this.onUpload) {
            this.uppy.on('upload', () => {
                // Prevent duplicate upload triggers
                if (this.isUploading) return;
                this.isUploading = true;
                try {
                    this.onUpload(this.getFiles());
                } catch (e) {
                    // Reset flag on error so user can try again
                    this.isUploading = false;
                    if (this.onError) {
                        this.onError(e?.message || 'Upload failed.');
                    }
                }
            });
        }

        this.uppy.on('restriction-failed', (file, error) => {
            if (this.onError) {
                this.onError(error.message || 'File does not meet requirements.');
            }
        });

        this.uppy.on('error', (error) => {
            if (this.onError && error) {
                this.onError(error.message || 'Something went wrong.');
            }
        });
    }

    // Get all files
    getFiles() {
        return this.uppy ? Object.values(this.uppy.getFiles()) : [];
    }

    // Open modal
    openModal() {
        if (this.uppy) {
            const plugin = this.uppy.getPlugin('Dashboard');
            if (plugin) {
                plugin.openModal();
            }
        }
    }

    // Close modal
    closeModal() {
        if (this.uppy) {
            const plugin = this.uppy.getPlugin('Dashboard');
            if (plugin) {
                plugin.closeModal();
            }
        }
    }

    // Reset uppy
    reset() {
        if (this.uppy) {
            this.uppy.cancelAll();
            // Remove all files from the UI
            const fileIds = Object.keys(this.uppy.getFiles());
            fileIds.forEach(fileId => {
                this.uppy.removeFile(fileId);
            });
        }
        this.isUploading = false;
    }

    // Destroy uppy instance
    destroy() {
        if (this.uppy) {
            this.uppy.close();
            this.uppy = null;
        }
        this.isUploading = false;
    }
}

// Export for use in modules
export default UppyUploadManager;

// Also make it available globally for inline scripts
window.UppyUploadManager = UppyUploadManager;

