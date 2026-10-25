import './bootstrap';

let qrcodePromise = null;

window.loadQRCode = function () {
    if (window.QRCode) {
        return Promise.resolve(window.QRCode);
    }
    if (!qrcodePromise) {
        qrcodePromise = import('qrcode')
            .then(function (module) {
                window.QRCode = module.default || module;
                return window.QRCode;
            })
            .catch(function (err) {
                qrcodePromise = null;
                throw err;
            });
    }
    return qrcodePromise;
};

import './shared/layout';
import './shared/universal-loading';

