/*
 * camera-select.js — Helper pemilihan kamera untuk fitur face recognition.
 *
 * Dipakai bersama oleh: registrasi biometrik, login wajah, dan absen pengurus.
 *
 * Tujuan:
 *  - Kamera utama = webcam bawaan/USB biasa (perilaku default).
 *  - DroidCam (kamera HP sebagai virtual webcam) = OPSI KEDUA / fallback
 *    otomatis bila kamera utama gagal atau tidak terdeteksi.
 *  - Menyediakan dropdown agar pengguna bisa berpindah kamera manual.
 *
 * API global: window.CameraSelect
 *   create({ video, select, onReady, onError, constraints }) -> controller
 *     controller.start()            : mulai kamera (auto-pilih device)
 *     controller.stop()             : hentikan stream
 *     controller.useDevice(id)      : pindah ke deviceId tertentu
 *     controller.currentStream()    : MediaStream aktif (atau null)
 */
(function () {
    'use strict';

    function isDroidCam(label) {
        return /droidcam|droid cam|ip ?camera|iriun|epoccam/i.test(label || '');
    }

    // Urutkan device: webcam biasa dulu (opsi utama), DroidCam/virtual di belakang.
    function sortPreferMainCamera(devices) {
        return devices.slice().sort(function (a, b) {
            var da = isDroidCam(a.label) ? 1 : 0;
            var db = isDroidCam(b.label) ? 1 : 0;
            return da - db;
        });
    }

    function labelFor(device, index) {
        if (device.label) return device.label;
        return 'Kamera ' + (index + 1);
    }

    function create(opts) {
        var video = opts.video;
        var select = opts.select || null;
        var onReady = opts.onReady || function () {};
        var onError = opts.onError || function () {};
        var extraConstraints = opts.constraints || { width: { ideal: 640 }, height: { ideal: 480 } };

        var stream = null;
        var videoDevices = [];
        var selectedDeviceId = null;

        function stop() {
            if (stream) {
                stream.getTracks().forEach(function (t) { t.stop(); });
                stream = null;
            }
        }

        function buildConstraints(deviceId) {
            var videoConstraints = Object.assign({}, extraConstraints);
            if (deviceId) {
                // Kunci ke device tertentu bila dipilih/di-fallback.
                videoConstraints.deviceId = { exact: deviceId };
            } else {
                // Default: kamera depan/utama.
                videoConstraints.facingMode = 'user';
            }
            return { video: videoConstraints, audio: false };
        }

        // Isi dropdown kamera (butuh izin kamera dulu agar label muncul).
        function refreshDeviceList() {
            if (!navigator.mediaDevices || !navigator.mediaDevices.enumerateDevices) {
                return Promise.resolve([]);
            }
            return navigator.mediaDevices.enumerateDevices().then(function (devices) {
                videoDevices = devices.filter(function (d) { return d.kind === 'videoinput'; });
                if (select) {
                    populateSelect();
                }
                return videoDevices;
            });
        }

        function populateSelect() {
            if (!select) return;
            var ordered = sortPreferMainCamera(videoDevices);
            select.innerHTML = '';
            ordered.forEach(function (d, i) {
                var opt = document.createElement('option');
                opt.value = d.deviceId;
                var tag = isDroidCam(d.label) ? ' (DroidCam)' : '';
                opt.textContent = labelFor(d, i) + tag;
                if (d.deviceId === selectedDeviceId) opt.selected = true;
                select.appendChild(opt);
            });
            // Tampilkan dropdown hanya bila ada >1 kamera (ada pilihan nyata).
            var wrap = select.closest('[data-camera-select-wrap]');
            if (wrap) {
                if (ordered.length > 1) {
                    wrap.classList.remove('hidden');
                    wrap.classList.add('flex');
                } else {
                    wrap.classList.add('hidden');
                    wrap.classList.remove('flex');
                }
            }
        }

        // Pilih device fallback DroidCam dari daftar (bila ada).
        function findDroidCamId() {
            for (var i = 0; i < videoDevices.length; i++) {
                if (isDroidCam(videoDevices[i].label)) return videoDevices[i].deviceId;
            }
            return null;
        }

        function openStream(deviceId) {
            stop();
            return navigator.mediaDevices.getUserMedia(buildConstraints(deviceId)).then(function (s) {
                stream = s;
                selectedDeviceId = deviceId || selectedDeviceId;
                video.srcObject = s;
                return s;
            });
        }

        // Alur start: coba kamera utama -> refresh daftar -> kalau gagal, fallback DroidCam.
        function start() {
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                onError({ name: 'NotSupported' }, 'Browser tidak mendukung akses kamera.');
                return;
            }

            openStream(null)
                .then(function (s) {
                    // Setelah izin didapat, isi dropdown & tandai device aktif.
                    var track = s.getVideoTracks()[0];
                    if (track && track.getSettings) {
                        var settings = track.getSettings();
                        if (settings.deviceId) selectedDeviceId = settings.deviceId;
                    }
                    return refreshDeviceList().then(function () {
                        if (select) populateSelect();
                        onReady(s);
                    });
                })
                .catch(function (err) {
                    // Kamera utama gagal (tidak ada / ditolak / constraint). Coba DroidCam.
                    tryFallback(err);
                });
        }

        function tryFallback(originalErr) {
            // Perlu daftar device untuk mencari DroidCam. enumerateDevices tetap
            // bisa dipanggil walau getUserMedia gagal (label bisa kosong).
            refreshDeviceList().then(function () {
                var droidId = findDroidCamId();
                if (droidId) {
                    openStream(droidId)
                        .then(function (s) {
                            if (select) populateSelect();
                            onReady(s, { usedFallback: true });
                        })
                        .catch(function (err2) { reportError(err2); });
                } else {
                    reportError(originalErr);
                }
            }).catch(function () { reportError(originalErr); });
        }

        function reportError(err) {
            var msg;
            switch (err && err.name) {
                case 'NotAllowedError':
                case 'SecurityError':
                    msg = 'Akses kamera ditolak. Izinkan kamera di browser, lalu coba lagi.';
                    break;
                case 'NotFoundError':
                case 'OverconstrainedError':
                    msg = 'Kamera tidak terdeteksi. Hubungkan webcam atau aktifkan DroidCam, lalu coba lagi.';
                    break;
                case 'NotReadableError':
                    msg = 'Kamera sedang dipakai aplikasi lain. Tutup aplikasi itu lalu coba lagi.';
                    break;
                default:
                    msg = 'Kamera tidak dapat diakses.';
            }
            onError(err || {}, msg);
        }

        function useDevice(deviceId) {
            openStream(deviceId)
                .then(function (s) { onReady(s, { manual: true }); })
                .catch(function (err) { reportError(err); });
        }

        if (select) {
            select.addEventListener('change', function () {
                if (select.value) useDevice(select.value);
            });
        }

        return {
            start: start,
            stop: stop,
            useDevice: useDevice,
            currentStream: function () { return stream; },
            isDroidCam: isDroidCam,
        };
    }

    window.CameraSelect = { create: create };
})();
