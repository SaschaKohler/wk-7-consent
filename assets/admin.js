(function($){
  'use strict';

  function collectOptions(){
    // Read current (unsaved) form values
    function val(name){ return ($('[name="wk7_consent_options['+name+']"]').val() || '').trim(); }
    function checked(name){ return $('[name="wk7_consent_options['+name+']"]').is(':checked'); }
    function pick(name, allowed, fallback){ var v = val(name); return allowed.indexOf(v) >= 0 ? v : fallback; }

    var avatarUrl = $('#wk7-fab-avatar-field .wk7-fab-avatar-preview img').attr('src') || '';
    return {
      policyUrl: val('policy_url') || '#',
      imprintUrl: val('imprint_url') || '#',
      i18n: {
        title: val('txt_title') || 'Wir respektieren deine Privatsphäre',
        text: val('txt_text') || 'Wir verwenden Cookies, um unsere Website zu verbessern. Du kannst selbst entscheiden, welche Kategorien du zulassen möchtest.',
        btnAcceptAll: val('txt_btn_accept_all') || 'Alle akzeptieren',
        btnRejectAll: val('txt_btn_reject_all') || 'Nur Notwendige',
        btnSave: val('txt_btn_save') || 'Auswahl speichern',
        linkPolicy: val('txt_link_policy') || 'Datenschutzerklärung',
        linkImprint: val('txt_link_imprint') || 'Impressum',
        categories: {
          necessary: val('lbl_necessary') || 'Notwendig',
          analytics: val('lbl_analytics') || 'Statistiken',
          marketing: val('lbl_marketing') || 'Marketing',
          functional: val('lbl_functional') || 'Funktional'
        },
        desc: {
          necessary: val('desc_necessary') || 'Erforderlich für die Grundfunktionen der Website.',
          analytics: val('desc_analytics') || 'Hilft uns zu verstehen, wie Besucher die Website nutzen (z. B. GA4).',
          marketing: val('desc_marketing') || 'Wird verwendet, um personalisierte Werbung anzuzeigen.',
          functional: val('desc_functional') || 'Verbessert Funktionen, z. B. Einbettungen.'
        }
      },
      ui: {
        position: pick('ui_position', ['bottom','top'], 'bottom'),
        primaryColor: val('ui_primary_color') || '#ff6b35',
        textColor: val('ui_text_color') || '#ededed',
        backgroundColor: val('ui_background_color') || '#0a0a0a',
        template: pick('ui_template', ['template1','template2','template3','template4'], 'template1')
      },
      fab: {
        show: checked('show_fab'),
        label: val('fab_label') || 'Cookie-Einstellungen',
        position: pick('fab_position', ['left','right'], 'left'),
        avatar: avatarUrl
      },
      showFooterLink: checked('show_footer_link')
    };
  }

  function el(tag, attrs, children){
    var n = document.createElement(tag);
    if (attrs) Object.keys(attrs).forEach(function(k){
      if (k === 'class') n.className = attrs[k];
      else if (k === 'for') n.htmlFor = attrs[k];
      else if (k === 'html') n.innerHTML = attrs[k];
      else n.setAttribute(k, attrs[k]);
    });
    (children || []).forEach(function(c){ if (typeof c === 'string') n.appendChild(document.createTextNode(c)); else if (c) n.appendChild(c); });
    return n;
  }

  function renderPreview(){
    var settings = collectOptions();
    var holder = document.getElementById('wk7-consent-admin-preview');
    if (!holder) return;
    holder.innerHTML = '';

    var root = el('div', { id:'wk7-consent-banner-root', class:'wk7-visible' });
    // Apply colors to preview root
    root.style.setProperty('--wk7-primary', settings.ui.primaryColor);
    root.style.setProperty('--wk7-text', settings.ui.textColor);
    root.style.setProperty('--wk7-bg', settings.ui.backgroundColor);

    var containerClass = 'wk7-consent-container' + (settings.ui.position === 'top' ? ' top' : '') + ' tpl-' + settings.ui.template;
    var container = el('div', { class: containerClass, role:'dialog', 'aria-modal':'true', 'aria-labelledby':'wk7-consent-title' }, [
      el('div', { class:'wk7-consent-inner' }, [
        el('h3', { id:'wk7-consent-title', class:'wk7-consent-title' }, [ settings.i18n.title ]),
        el('p', { class:'wk7-consent-text' }, [ settings.i18n.text ]),
        el('div', { class:'wk7-consent-links' }, [
          el('a', { href: settings.policyUrl || '#', target:'_blank', rel:'noopener' }, [ settings.i18n.linkPolicy ]), document.createTextNode(' · '),
          el('a', { href: settings.imprintUrl || '#', target:'_blank', rel:'noopener' }, [ settings.i18n.linkImprint ])
        ]),
        el('div', { class:'wk7-consent-categories' }, [
          // not interactive in preview
          el('div', { class:'wk7-consent-cat' }, [
            el('div', { class:'wk7-consent-toggle' }, [ el('input', { type:'checkbox', disabled:'disabled', checked:'checked' }), el('label', {}, [ settings.i18n.categories.necessary ]) ]),
            el('p', null, [ settings.i18n.desc.necessary ])
          ]),
          el('div', { class:'wk7-consent-cat' }, [
            el('div', { class:'wk7-consent-toggle' }, [ el('input', { type:'checkbox', disabled:'disabled', checked:'checked' }), el('label', {}, [ settings.i18n.categories.functional ]) ]),
            el('p', null, [ settings.i18n.desc.functional ])
          ]),
          el('div', { class:'wk7-consent-cat' }, [
            el('div', { class:'wk7-consent-toggle' }, [ el('input', { type:'checkbox', disabled:'disabled' }), el('label', {}, [ settings.i18n.categories.analytics ]) ]),
            el('p', null, [ settings.i18n.desc.analytics ])
          ]),
          el('div', { class:'wk7-consent-cat' }, [
            el('div', { class:'wk7-consent-toggle' }, [ el('input', { type:'checkbox', disabled:'disabled' }), el('label', {}, [ settings.i18n.categories.marketing ]) ]),
            el('p', null, [ settings.i18n.desc.marketing ])
          ])
        ]),
        el('div', { class:'wk7-consent-actions' }, [
          el('button', { class:'wk7-btn wk7-btn-outline', type:'button' }, [ settings.i18n.btnRejectAll ]),
          el('button', { class:'wk7-btn', type:'button' }, [ settings.i18n.btnSave ]),
          el('button', { class:'wk7-btn wk7-btn-primary', type:'button' }, [ settings.i18n.btnAcceptAll ])
        ])
      ])
    ]);

    root.appendChild(container);
    holder.appendChild(root);

    // FAB preview
    if (settings.fab.show) {
      var fabButtonClasses = 'wk7-fab-primary';
      var fabButtonChildren = [];

      if (settings.fab.avatar) {
        fabButtonClasses += ' wk7-fab-avatar-only';
        fabButtonChildren.push(el('img', { class:'wk7-fab-avatar', src: settings.fab.avatar, alt:'' }));
      } else {
        fabButtonChildren.push(document.createTextNode(settings.fab.label));
      }

      var fab = el('div', { class:'wk7-consent-fab ' + (settings.fab.position === 'right' ? 'wk7-consent-fab-right' : 'wk7-consent-fab-left') }, [
        el('button', { class: fabButtonClasses, type:'button' }, fabButtonChildren)
      ]);
      holder.appendChild(fab);
    }
  }

  function bindLivePreview(){
    var deb;
    function queue(){ clearTimeout(deb); deb = setTimeout(renderPreview, 50); }
    // All inputs in our form
    $(document).on('input change', '[name^="wk7_consent_options["]', queue);
    // Color picker events
    if (typeof $.fn.wpColorPicker === 'function') {
      $('.wk7-color').wpColorPicker({
        change: function(){ queue(); },
        clear: function(){ queue(); }
      });
    }
  }

  $(function(){
    bindLivePreview();
    renderPreview();

    // Media uploader for FAB avatar
    var frame;
    $(document).on('click', '.wk7-upload-avatar', function(e){
      e.preventDefault();
      if (frame) { frame.open(); return; }
      frame = wp.media({
        title: 'Avatar auswählen',
        button: { text: 'Verwenden' },
        multiple: false
      });
      frame.on('select', function(){
        var attachment = frame.state().get('selection').first().toJSON();
        var id = attachment.id;
        var url = (attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url);
        var holder = $('#wk7-fab-avatar-field');
        holder.find('input[type="hidden"]').val(id);
        holder.find('.wk7-fab-avatar-preview').html('<img src="'+url+'" alt="" style="width:48px;height:48px;border-radius:50%;object-fit:cover;border:1px solid #ccc;" />');
        holder.find('.wk7-remove-avatar').prop('disabled', false);
        renderPreview();
      });
      frame.open();
    });

    $(document).on('click', '.wk7-remove-avatar', function(e){
      e.preventDefault();
      var holder = $('#wk7-fab-avatar-field');
      holder.find('input[type="hidden"]').val('');
      holder.find('.wk7-fab-avatar-preview').html('<span style="display:inline-block;width:48px;height:48px;border-radius:50%;background:#eee;border:1px dashed #ccc;"></span>');
      $(this).prop('disabled', true);
      renderPreview();
    });
  });
})(jQuery);
