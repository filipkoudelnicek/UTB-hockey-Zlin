(function () {
    var STORAGE_KEY = 'cc_prefs';
    var FORGET_KEY  = 'cc_forget';
    var VERSION     = parseInt(window._ccVersion || 1, 10);
    var MAX_AGE_MS  = window._ccMaxAgeMs || 12 * 30.4375 * 864e5;
    var VISITOR_COOKIE = 'utb_vid';
    var box      = document.getElementById('cc-box');
    var toggle   = document.getElementById('cc-toggle');
    var cats     = document.getElementById('cc-cats');
    var btnsMain = document.getElementById('cc-btns-main');
    var btnsPrefs= document.getElementById('cc-btns-prefs');
    var chkAna   = document.getElementById('cc-chk-analytics');
    var thirdPartyLoaded = false;
    var tracked = false;

    function readStored() {
        try { return JSON.parse(localStorage.getItem(STORAGE_KEY)); } catch(e) { return null; }
    }
    // Souhlas s analytikou udělený před zavedením vlastního měření (jiná verze) neplatí a je nutné jej získat znovu.
    // Odmítnutí zůstává platné.
    function getPrefs() {
        var p = readStored();
        if (!p || typeof p !== 'object') return null;
        // Volba (souhlas i odmítnutí) platí nejvýše 12 měsíců; záznam bez časové značky je neplatný.
        if (typeof p.ts !== 'number' || Date.now() - p.ts >= MAX_AGE_MS) return null;
        if (p.analytics === true && p.v !== VERSION) return null;
        return p;
    }
    function savePrefs(analytics) {
        localStorage.setItem(STORAGE_KEY, JSON.stringify({ analytics: !!analytics, v: VERSION, ts: Date.now() }));
    }
    function hasAnalyticsConsent() {
        var p = getPrefs();
        return !!(p && p.analytics === true);
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

    function hasVisitorCookie() {
        return document.cookie.split(';').some(function (c) { return c.trim().indexOf(VISITOR_COOKIE + '=') === 0; });
    }
    function expireCookie(name) {
        var host = location.hostname.split('.');
        var domains = [null];
        for (var i = 0; i < host.length - 1; i++) { domains.push('.' + host.slice(i).join('.')); }
        domains.forEach(function (d) {
            document.cookie = name + '=; Max-Age=0; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/' + (d ? '; domain=' + d : '');
        });
    }

    // Smaže vlastní analytický identifikátor i data uložená na serveru.
    function forgetVisitor() {
        var pending = false;
        try { pending = !!localStorage.getItem(FORGET_KEY); } catch (e) {}
        if (!hasVisitorCookie() && !pending) return;
        var url = window._analyticsForgetUrl;
        try { localStorage.setItem(FORGET_KEY, '1'); } catch (e) {}
        if (!url) { expireCookie(VISITOR_COOKIE); return; }
        fetch(url, { method: 'POST', credentials: 'same-origin', keepalive: true })
            .then(function (r) { if (r.ok) { try { localStorage.removeItem(FORGET_KEY); } catch (e) {} } })
            .catch(function () {})
            .then(function () { expireCookie(VISITOR_COOKIE); });
    }

    function stopThirdParty() {
        if (window._gaId) window['ga-disable-' + window._gaId] = true;
        if (thirdPartyLoaded && window.dataLayer) {
            window.dataLayer.push(['consent', 'update', { analytics_storage: 'denied', ad_storage: 'denied' }]);
        }
        document.cookie.split(';').forEach(function (c) {
            var name = c.split('=')[0].trim();
            if (/^(_ga|_gid|_gat|_gcl_|_gac_)/.test(name)) expireCookie(name);
        });
    }

    function loadThirdParty() {
        if (thirdPartyLoaded) return;
        thirdPartyLoaded = true;
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

    // Vlastní měření: jen po platném souhlasu, jednou za načtení stránky, bez botů a vložených náhledů.
    function trackPageview() {
        var cfg = window._analytics;
        if (tracked || !cfg || !cfg.token || !hasAnalyticsConsent()) return;
        if (navigator.webdriver || window.top !== window.self) return;
        if (document.prerendering) {
            document.addEventListener('prerenderingchange', trackPageview, { once: true });
            return;
        }
        tracked = true;
        fetch(cfg.collect, {
            method: 'POST',
            credentials: 'same-origin',
            keepalive: true,
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({ t: cfg.token })
        }).catch(function () {});
    }

    function startAnalytics() {
        loadThirdParty();
        trackPageview();
    }

    function revokeAnalytics() {
        tracked = true; // do konce načtení stránky už nic neodesílat
        stopThirdParty();
        forgetVisitor();
    }

    function applyChoice(analytics) {
        var wasEnabled = hasAnalyticsConsent();
        savePrefs(analytics);
        if (analytics) { startAnalytics(); }
        else if (wasEnabled || hasVisitorCookie()) { revokeAnalytics(); }
    }

    document.querySelectorAll('.cc-cat-header').forEach(function (h) {
        h.addEventListener('click', function () { h.closest('.cc-cat-row').classList.toggle('cc-open'); });
    });

    var saved = getPrefs();
    if (saved !== null) {
        box.style.display = 'none';
        toggle.style.display = 'flex';
        if (saved.analytics) { startAnalytics(); } else { forgetVisitor(); }
    } else {
        // Žádný platný souhlas: nic se nespouští a případný starý identifikátor se odstraní.
        forgetVisitor();
    }

    ['cc-accept-all','cc-accept-all-2'].forEach(function (id) {
        document.getElementById(id).addEventListener('click', function () { applyChoice(true); hide(); });
    });
    ['cc-reject-all','cc-reject-all-2'].forEach(function (id) {
        document.getElementById(id).addEventListener('click', function () { applyChoice(false); hide(); });
    });
    document.getElementById('cc-show-prefs').addEventListener('click', function () {
        chkAna.checked = hasAnalyticsConsent();
        cats.style.display = 'block'; btnsMain.style.display = 'none'; btnsPrefs.style.display = 'flex';
    });
    document.getElementById('cc-save-prefs').addEventListener('click', function () {
        applyChoice(chkAna.checked); hide();
    });
    toggle.addEventListener('click', function () {
        chkAna.checked = hasAnalyticsConsent(); show(true);
    });
})();
