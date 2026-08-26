<div id="cc-box" role="dialog" aria-modal="true" aria-labelledby="cc-title">
    <div id="cc-box-inner">
        <h2 id="cc-title">Souhlas s cookies</h2>
        <p class="cc-text">{{ \App\Models\Setting::get('cookie_text') }}</p>

        <div class="cc-cats" id="cc-cats" style="display:none">
            <div class="cc-cat-row">
                <div class="cc-cat-header">
                    <span class="cc-cat-title">Nezbytné cookies</span>
                    <div class="cc-toggle-wrap">
                        <label class="cc-switch" onclick="event.stopPropagation()">
                            <input type="checkbox" id="cc-chk-necessary" checked disabled>
                            <span class="cc-switch-track"></span>
                            <span class="cc-switch-thumb"></span>
                        </label>
                        <svg class="cc-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                    </div>
                </div>
                <div class="cc-cat-desc">Relace, CSRF ochrana a základní funkce webu. Vždy aktivní.</div>
            </div>
            <div class="cc-cat-row">
                <div class="cc-cat-header">
                    <span class="cc-cat-title">Analytické cookies</span>
                    <div class="cc-toggle-wrap">
                        <label class="cc-switch" onclick="event.stopPropagation()">
                            <input type="checkbox" id="cc-chk-analytics">
                            <span class="cc-switch-track"></span>
                            <span class="cc-switch-thumb"></span>
                        </label>
                        <svg class="cc-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                    </div>
                </div>
                <div class="cc-cat-desc">Google Analytics / Tag Manager – sledování návštěvnosti. Slouží ke zlepšování obsahu webu.</div>
            </div>
        </div>

        <div class="cc-btns" id="cc-btns-main">
            <button class="cc-btn cc-btn-accept" id="cc-accept-all">Přijmout vše</button>
            <button class="cc-btn cc-btn-outline" id="cc-reject-all">Odmítnout</button>
            <button class="cc-btn cc-btn-outline" id="cc-show-prefs">Předvolby</button>
        </div>
        <div class="cc-btns" id="cc-btns-prefs" style="display:none">
            <button class="cc-btn cc-btn-accept" id="cc-accept-all-2">Přijmout vše</button>
            <button class="cc-btn cc-btn-outline" id="cc-reject-all-2">Odmítnout</button>
            <button class="cc-btn cc-btn-outline" id="cc-save-prefs">Uložit</button>
        </div>
    </div>
    <div class="cc-footer-links">
        @if(\App\Models\Setting::get('cookie_policy_url'))
            <a href="{{ \App\Models\Setting::get('cookie_policy_url') }}" target="_blank" rel="noopener">Zásady cookies</a>
        @endif
        @if(\App\Models\Setting::get('privacy_policy_url'))
            <a href="{{ \App\Models\Setting::get('privacy_policy_url') }}" target="_blank" rel="noopener">Zásady ochrany osobních údajů</a>
        @endif
    </div>
</div>

<button id="cc-toggle" title="Nastavení cookies" aria-label="Nastavení cookies">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#7400ff" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="12" cy="12" r="10"/>
        <circle cx="8.5" cy="9" r="1.3" fill="#7400ff" stroke="none"/>
        <circle cx="15" cy="8" r="1.3" fill="#7400ff" stroke="none"/>
        <circle cx="15.5" cy="15" r="1.3" fill="#7400ff" stroke="none"/>
        <circle cx="9" cy="15.5" r="1.3" fill="#7400ff" stroke="none"/>
    </svg>
</button>

<script src="/assets/js/cookies.js"></script>
