import './bootstrap';
import 'notyf/notyf.min.css';
import 'notiflix/dist/notiflix-3.3.0.min.css';
import Notiflix from 'notiflix';
import { Notyf } from 'notyf';

// Expose globally
window.Notiflix = Notiflix;
window.Notyf = Notyf;

// Inisialisasi Notyf Toast (Modern, responsif, minimalis)
const notyf = new Notyf({
    duration: 3800,
    position: { x: 'right', y: 'top' },
    ripple: true,
    dismissible: true,
    types: [
        {
            type: 'info',
            background: '#1E293B',
            icon: {
                className: 'notyf-info-icon',
                tagName: 'span',
                text: 'ℹ'
            }
        },
        {
            type: 'warning',
            background: '#D97706',
            icon: {
                className: 'notyf-warn-icon',
                tagName: 'span',
                text: '⚠'
            }
        },
        {
            type: 'success',
            background: '#059669',
        },
        {
            type: 'error',
            background: '#6D0808',
        }
    ]
});
window.notyf = notyf;

// Konfigurasi Notiflix (Popups, Modal Reports & Confirm Dialogs)
Notiflix.Report.init({
    borderRadius: '16px',
    fontFamily: 'Plus Jakarta Sans, sans-serif',
    svgSize: '54px',
    plainText: false,
    titleFontSize: '16px',
    messageFontSize: '13px',
    buttonFontSize: '13px',
    success: {
        svgColor: '#059669',
        titleColor: '#059669',
        buttonBackground: '#059669',
        buttonColor: '#FFFFFF',
    },
    failure: {
        svgColor: '#6D0808',
        titleColor: '#6D0808',
        buttonBackground: '#6D0808',
        buttonColor: '#FFFFFF',
    },
    warning: {
        svgColor: '#D97706',
        titleColor: '#D97706',
        buttonBackground: '#D97706',
        buttonColor: '#FFFFFF',
    },
    info: {
        svgColor: '#1E293B',
        titleColor: '#1E293B',
        buttonBackground: '#6D0808',
        buttonColor: '#FFFFFF',
    }
});

Notiflix.Confirm.init({
    borderRadius: '16px',
    fontFamily: 'Plus Jakarta Sans, sans-serif',
    titleColor: '#6D0808',
    okButtonBackground: '#6D0808',
    okButtonColor: '#FFFFFF',
    cancelButtonBackground: '#E4E4E7',
    cancelButtonColor: '#27272A',
    buttonsFontSize: '12px',
    titleFontSize: '15px',
    messageFontSize: '13px',
});

Notiflix.Loading.init({
    className: 'kaiden-notiflix-loading',
    svgColor: '#6D0808',
    messageColor: '#FFFFFF',
    fontFamily: 'Plus Jakarta Sans, sans-serif',
    fontSize: '13px',
});

// Universal Notification Interface (Kombinasi Notiflix + Notyf)
window.kaidenToast = {
    success: (msg) => notyf.success(msg),
    error: (msg) => notyf.error(msg),
    warning: (msg) => notyf.open({ type: 'warning', message: msg }),
    info: (msg) => notyf.open({ type: 'info', message: msg }),
};

window.kaidenAlert = {
    success: (title, msg, btnText = 'Tutup') => Notiflix.Report.success(title, msg, btnText),
    error: (title, msg, btnText = 'Mengerti') => Notiflix.Report.failure(title, msg, btnText),
    warning: (title, msg, btnText = 'OK') => Notiflix.Report.warning(title, msg, btnText),
    info: (title, msg, btnText = 'OK') => Notiflix.Report.info(title, msg, btnText),
    confirm: (title, msg, onConfirm, onCancel = null, okText = 'Ya, Lanjutkan', cancelText = 'Batal') => {
        Notiflix.Confirm.show(title, msg, okText, cancelText, onConfirm, onCancel || function() {});
    }
};

window.kaidenLoading = {
    show: (msg = 'Memproses data...') => Notiflix.Loading.standard(msg),
    hide: () => Notiflix.Loading.remove()
};
