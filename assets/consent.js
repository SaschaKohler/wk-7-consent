(function(){
  'use strict';

  var SETTINGS = window.WK7ConsentSettings || {};
  var STORAGE_KEY = SETTINGS.storageKey || 'wine_k7_consent';

  // Helpers
  function getStored() {
    try { return JSON.parse(localStorage.getItem(STORAGE_KEY) || 'null'); } catch(e){ return null; }
  }
  function setStored(v) {
    try { localStorage.setItem(STORAGE_KEY, JSON.stringify(v)); } catch(e){}
  }
  function hasConsent(){ return !!getStored(); }

  function toBool(val, fallback){
    if (typeof val === 'boolean') return val;
    if (typeof val === 'number') return val === 1;
    if (typeof val === 'string') {
      var lower = val.trim().toLowerCase();
      if (lower === 'true' || lower === '1' || lower === 'yes') return true;
      if (lower === 'false' || lower === '0' || lower === 'no') return false;
    }
    return !!fallback;
  }

  function normalizeCategories(raw){
    var src = raw || {};
    return {
      necessary: toBool(src.necessary, true),
      functional: toBool(src.functional, true),
      analytics: toBool(src.analytics, false),
      marketing: toBool(src.marketing, false)
    };
  }

  // Map categories to Consent Mode v2 keys
  function categoriesToConsent(cats){
    // Default denied for non-essential
    var update = {
      ad_storage: 'denied',
      analytics_storage: 'denied',
      ad_user_data: 'denied',
      ad_personalization: 'denied',
      functionality_storage: 'granted', // allow basic site functionality
      security_storage: 'granted'
    };
    if (cats.analytics) update.analytics_storage = cats.analytics ? 'granted' : 'denied';
    if (cats.marketing) {
      update.ad_storage = cats.marketing ? 'granted' : 'denied';
      update.ad_user_data = cats.marketing ? 'granted' : 'denied';
      update.ad_personalization = cats.marketing ? 'granted' : 'denied';
    }
    if (cats.functional) update.functionality_storage = cats.functional ? 'granted' : 'denied';
    return update;
  }

  function gtag() { (window.dataLayer = window.dataLayer || []).push(arguments); }

  function pushConsentUpdate(cats){
    var update = categoriesToConsent(cats);
    gtag('consent', 'update', update);
    gtag('event', 'jzl_consent_update', { consent: update });
  }

  // UI
  function el(tag, attrs, children){
    var n = document.createElement(tag);
    if (attrs) Object.keys(attrs).forEach(function(k){
      var val = attrs[k];
      if (val === null || typeof val === 'undefined' || val === false) {
        return;
      }
      if (k === 'class') n.className = val;
      else if (k === 'for') n.htmlFor = val;
      else if (k === 'html') n.innerHTML = val;
      else if (k === 'checked' || k === 'disabled') n[k] = !!val;
      else n.setAttribute(k, val);
    });
    (children || []).forEach(function(c){ if (typeof c === 'string') n.appendChild(document.createTextNode(c)); else if (c) n.appendChild(c); });
    return n;
  }

  function renderBanner() {
    var root = document.getElementById('wk7-consent-banner-root');
    if (!root) return;

    // Apply UI options (colors, position)
    var UI = SETTINGS.ui || {};
    if (UI.primaryColor) root.style.setProperty('--wk7-primary', UI.primaryColor);
    if (UI.textColor) root.style.setProperty('--wk7-text', UI.textColor);
    if (UI.backgroundColor) root.style.setProperty('--wk7-bg', UI.backgroundColor);

    var I = (SETTINGS.i18n || {});
    var title = I.title || 'Wir respektieren deine Privatsphäre';
    var text = I.text || 'Wir verwenden Cookies, um unsere Website zu verbessern. Du kannst selbst entscheiden, welche Kategorien du zulassen möchtest.';
    var btnAcceptAll = I.btnAcceptAll || 'Alle akzeptieren';
    var btnRejectAll = I.btnRejectAll || 'Nur Notwendige';
    var btnSave = I.btnSave || 'Auswahl speichern';
    var linkPolicy = I.linkPolicy || 'Datenschutzerklärung';
    var linkImprint = I.linkImprint || 'Impressum';

    var catLabels = I.categories || { necessary: 'Notwendig', analytics: 'Statistiken', marketing: 'Marketing', functional: 'Funktional' };
    var catDesc = I.desc || {
      necessary: 'Erforderlich für die Grundfunktionen der Website.',
      analytics: 'Hilft uns zu verstehen, wie Besucher die Website nutzen (z. B. GA4).',
      marketing: 'Wird verwendet, um personalisierte Werbung anzuzeigen.',
      functional: 'Verbessert Funktionen, z. B. Einbettungen.'
    };

    var current = getStored();
    var state = normalizeCategories(current && current.categories);

    var inputsByKey = {};
    function catToggle(key, disabled){
      var id = 'wk7-cat-' + key;
      var input = el('input', { type:'checkbox', id:id, checked: !!state[key], disabled: !!disabled });
      inputsByKey[key] = input;
      input.addEventListener('change', function(){ state[key] = !!input.checked; });
      return el('div', { class:'wk7-consent-cat' }, [
        el('div', { class:'wk7-consent-toggle' }, [ input, el('label', { for:id }, [ catLabels[key] || key ]) ]),
        el('p', null, [ catDesc[key] || '' ])
      ]);
    }

    var tpl = (UI.template && typeof UI.template === 'string') ? UI.template : 'template1';
    var containerClass = 'wk7-consent-container' + (UI.position === 'top' ? ' top' : '') + ' tpl-' + tpl;
    var container = el('div', { class: containerClass, role:'dialog', 'aria-modal':'true', 'aria-labelledby':'wk7-consent-title' }, [
      el('div', { class:'wk7-consent-inner' }, [
        el('h3', { id:'wk7-consent-title', class:'wk7-consent-title' }, [ title ]),
        el('p', { class:'wk7-consent-text' }, [ text ]),
        el('div', { class:'wk7-consent-links' }, [
          el('a', { href: SETTINGS.policyUrl || '#', target:'_blank', rel:'noopener' }, [ linkPolicy ]), document.createTextNode(' · '),
          el('a', { href: SETTINGS.imprintUrl || '#', target:'_blank', rel:'noopener' }, [ linkImprint ])
        ]),
        el('div', { class:'wk7-consent-categories' }, [
          catToggle('necessary', true),
          catToggle('functional', false),
          catToggle('analytics', false),
          catToggle('marketing', false)
        ]),
        el('div', { class:'wk7-consent-actions' }, [
          el('button', { class:'wk7-btn wk7-btn-outline', type:'button' }, [ btnRejectAll ]),
          el('button', { class:'wk7-btn', type:'button' }, [ btnSave ]),
          el('button', { class:'wk7-btn wk7-btn-primary', type:'button' }, [ btnAcceptAll ])
        ])
      ])
    ]);

    var btnReject = container.querySelector('.wk7-btn-outline');
    var btnSaveSel = container.querySelector('.wk7-btn:not(.wk7-btn-primary):not(.wk7-btn-outline)');
    var btnAccept = container.querySelector('.wk7-btn-primary');

    function syncUI(){
      Object.keys(inputsByKey).forEach(function(key){
        if (inputsByKey[key]) {
          inputsByKey[key].checked = !!state[key];
        }
      });
    }

    function setState(newState){
      state = normalizeCategories(newState || state);
      syncUI();
    }

    function saveAndClose(newState){
      if (newState) {
        setState(newState);
      }
      var toSave = { categories: normalizeCategories(state), ts: Date.now(), version: 'v1' };
      setStored(toSave);
      pushConsentUpdate(toSave.categories);
      close();
    }
    btnReject.addEventListener('click', function(){
      setState({ necessary: true, functional: true, analytics: false, marketing: false });
      saveAndClose();
    });
    btnSaveSel.addEventListener('click', function(){ saveAndClose(); });
    btnAccept.addEventListener('click', function(){
      setState({ necessary: true, functional: true, analytics: true, marketing: true });
      saveAndClose();
    });

    var backdrop = el('div', { class:'wk7-consent-backdrop', 'aria-hidden':'true' });

    function open(){
      root.classList.remove('wk7-hidden');
      root.classList.add('wk7-visible');
      root.appendChild(backdrop);
      root.appendChild(container);
    }
    function close(){
      root.classList.add('wk7-hidden');
      root.classList.remove('wk7-visible');
      try { root.innerHTML = ''; } catch(e){}
    }

    // Expose preferences API
    window.WK7Consent = window.WK7Consent || {};
    window.WK7Consent.openPreferences = open;

    open();
  }

  // Expose a lazy global so external buttons can always open preferences
  window.WK7Consent = window.WK7Consent || {};
  window.WK7Consent.openPreferences = function(){
    // Always render a fresh banner; renderBanner() will open it
    renderBanner();
  };

  function init(){
    // If no stored consent, render banner; if stored, push update to gtag on load
    if (!hasConsent()) {
      renderBanner();
    } else {
      var stored = getStored();
      if (stored && stored.categories) {
        pushConsentUpdate(stored.categories);
      }
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
