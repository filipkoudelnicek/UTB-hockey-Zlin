(function () {
    var STORAGE_KEY = 'cc_prefs';
    var box      = document.getElementById('cc-box');
    var toggle   = document.getElementById('cc-toggle');
    var cats     = document.getElementById('cc-cats');
    var btnsMain = document.getElementById('cc-btns-main');
    var btnsPrefs= document.getElementById('cc-btns-prefs');
    var chkAna   = document.getElementById('cc-chk-analytics');

    function getPrefs() {
        try { return JSON.parse(localStorage.getItem(STORAGE_KEY)); } catch(e) { return null; }
    }
    function savePrefs(analytics) {
        localStorage.setItem(STORAGE_KEY, JSON.stringify({ analytics: !!analytics, ts: Date.now() }));
    }

    function hide() {
        box.classList.add('cc-hiding');
        setTimeout(function () {
            box.style.display = 'none';
            toggle.style.display = 'flex';
        }, 380);
    }

    function show(openPrefs) {
        cats.style.display      = openPrefs ? 'block' : 'none';
        btnsMain.style.display  = openPrefs ? 'none'  : 'flex';
        btnsPrefs.style.display = openPrefs ? 'flex'  : 'none';
        box.style.display = 'block';
        box.classList.remove('cc-hiding');
        toggle.style.display = 'none';
    }

    function loadAnalytics() {
        if (window._gtmId) {
            (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
            new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
            'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
            })(window,document,'script','dataLayer',window._gtmId);
        }
        if (window._gaId) {
            var s = document.createElement('script');
            s.async = true; s.src = 'https://www.googletagmanager.com/gtag/js?id=' + window._gaId;
            document.head.appendChild(s);
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date()); gtag('config', window._gaId);
        }
    }

    document.querySelectorAll('.cc-cat-header').forEach(function (h) {
        h.addEventListener('click', function () { h.closest('.cc-cat-row').classList.toggle('cc-open'); });
    });

    var saved = getPrefs();
    if (saved !== null) {
        box.style.display = 'none';
        toggle.style.display = 'flex';
        if (saved.analytics) loadAnalytics();
    }

    ['cc-accept-all','cc-accept-all-2'].forEach(function (id) {
        document.getElementById(id).addEventListener('click', function () { savePrefs(true); loadAnalytics(); hide(); });
    });
    ['cc-reject-all','cc-reject-all-2'].forEach(function (id) {
        document.getElementById(id).addEventListener('click', function () { savePrefs(false); hide(); });
    });
    document.getElementById('cc-show-prefs').addEventListener('click', function () {
        chkAna.checked = (getPrefs() || {}).analytics || false;
        cats.style.display = 'block'; btnsMain.style.display = 'none'; btnsPrefs.style.display = 'flex';
    });
    document.getElementById('cc-save-prefs').addEventListener('click', function () {
        savePrefs(chkAna.checked); if (chkAna.checked) loadAnalytics(); hide();
    });
    toggle.addEventListener('click', function () {
        chkAna.checked = (getPrefs() || {}).analytics || false; show(true);
    });
})();
