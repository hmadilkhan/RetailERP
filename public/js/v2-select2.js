/*
 * v2 layout (master-tailwind): har <select> ko searchable select2 banata hai.
 *
 * - Page khud select2 lagaye (ajax / custom options) to wahi rehta hai: auto init page scripts ke baad
 *   chalta hai, aur page ki baad wali .select2() hamara instance khud destroy kar deti hai.
 * - Linked dropdowns: select2 ka change ab asli (native) 'change' event hai, isliye jQuery .on('change'),
 *   addEventListener('change'), onchange="" aur Livewire wire:model sab chalte hain.
 * - JS se value / options badlein (.value =, $(..).val(x), innerHTML / append) to select2 ka display khud update.
 * - Baad me aane wale selects (modal, nayi row, AJAX HTML) MutationObserver se.
 * - Livewire: morph se pehle auto select2 hatate hain, morph ke baad dobara (warna morph container tod deta).
 *
 * Opt-out: select pe class "no-select2" ya kisi parent pe data-no-select2.
 */
(function () {
    'use strict';

    var $ = window.jQuery;
    if (!$ || !$.fn || !$.fn.select2 || window.v2Select2) {
        return;
    }

    var SKIP_INSIDE = '[data-no-select2], .dataTables_length, .swal2-container, .sweet-alert, .select2-container, .bootstrap-tagsinput, template';
    var WIDTH_CLASS = /^((sm|md|lg|xl|2xl):)?(w-|min-w-|max-w-|flex-|grow|shrink|basis-)/;
    var SMALL_CLASS = /^(text-xs|py-0|py-0\.5|py-1|h-7|h-8|h-9)$/;

    function instanceOf(el) {
        var inst = $.data(el, 'select2');
        return inst && inst.$element && inst.$element[0] === el ? inst : null;
    }

    function eligible(el) {
        if (!el || el.tagName !== 'SELECT' || !el.isConnected) {
            return false;
        }
        if (el.classList.contains('no-select2') || el.closest(SKIP_INSIDE)) {
            return false;
        }
        // Listbox (size > 1) aur doosre plugin ka chhupaya hua select nahi
        if ((el.hasAttribute('size') && el.size > 1) || el.style.display === 'none') {
            return false;
        }
        // wire:ignore.self wale select ke options Livewire badalta hai, sibling container morph me toot jata
        if (el.hasAttribute('wire:ignore.self')) {
            return false;
        }
        return true;
    }

    function insideWireIgnore(el) {
        return !!el.closest('[wire\\:ignore]');
    }

    // .clone() / cloneNode se aaya select: class + container copy hue magar instance nahi
    function cleanStale(el) {
        if (instanceOf(el)) {
            return;
        }
        $.removeData(el, 'select2');
        el.classList.remove('select2-hidden-accessible');
        el.removeAttribute('aria-hidden');
        if (el.getAttribute('tabindex') === '-1') {
            el.removeAttribute('tabindex');
        }
        removeForeignContainers(el, null);
    }

    function removeForeignContainers(el, own) {
        var next = el.nextElementSibling;
        while (next && next.classList.contains('select2-container')) {
            var after = next.nextElementSibling;
            if (next !== own) {
                next.parentNode.removeChild(next);
            }
            next = after;
        }
    }

    function syncVisibility(el) {
        var inst = instanceOf(el);
        if (!inst) {
            return;
        }
        var hide = el.hidden || el.classList.contains('hidden') || el.style.display === 'none';
        inst.$container[0].style.display = hide ? 'none' : '';
    }

    function refresh(el) {
        var inst = instanceOf(el);
        if (!inst) {
            return;
        }
        inst.dataAdapter.current(function (data) {
            inst.trigger('selection:update', { data: data });
        });
    }

    function init(el) {
        if (!eligible(el)) {
            return;
        }
        var existing = instanceOf(el);
        if (existing) {
            removeForeignContainers(el, existing.$container[0]);
            return;
        }
        cleanStale(el);

        var $el = $(el);
        var classes = (el.getAttribute('class') || '').split(/\s+/);
        var widthClasses = classes.filter(function (c) { return WIDTH_CLASS.test(c); });
        var small = classes.some(function (c) { return SMALL_CLASS.test(c); });
        var $empty = $el.find('option[value=""]').first();
        var placeholder = $el.attr('data-placeholder') || ($empty.length ? $.trim($empty.text()) : '');
        if (!placeholder && $empty.length) {
            placeholder = 'Select';
        }
        var inlineWidth = /(^|;)\s*width\s*:/i.test(el.getAttribute('style') || '');
        var $modal = $el.closest('.modal');

        $el.addClass('v2-select2 v2-select2-auto' + (widthClasses.length || inlineWidth ? ' v2-select2-fit' : ''));
        $el.select2({
            width: inlineWidth ? 'style' : (widthClasses.length ? null : '100%'),
            dropdownCssClass: 'v2-select2-dropdown',
            dropdownParent: $modal.length ? $modal : $(document.body),
            placeholder: placeholder,
            allowClear: !el.multiple && !el.required && $empty.length > 0
        });

        var inst = instanceOf(el);
        if (!inst) {
            return;
        }
        if (widthClasses.length && !inlineWidth) {
            inst.$container.addClass(widthClasses.join(' '));
        }
        if (small) {
            inst.$container.addClass('v2-select2-sm');
        }
        syncVisibility(el);

        // × (clear) dabane pe select2 4.0.3 dropdown bhi khol deta hai — rok do
        $el.off('.v2auto')
            .on('select2:unselecting.v2auto', function () {
                el.__v2Clearing = true;
                setTimeout(function () { el.__v2Clearing = false; }, 0);
            })
            .on('select2:opening.v2auto', function (e) {
                if (el.__v2Clearing) {
                    e.preventDefault();
                }
            });
    }

    function safeInit(el) {
        try {
            init(el);
        } catch (e) {
            if (window.console && console.warn) {
                console.warn('v2-select2: init failed', el, e);
            }
        }
    }

    function scanNow(root) {
        if (!root) {
            return;
        }
        if (root.tagName === 'SELECT') {
            safeInit(root);
            return;
        }
        if (root.querySelectorAll) {
            Array.prototype.forEach.call(root.querySelectorAll('select'), safeInit);
        }
    }

    // ---- queues ----
    var pendingInit = [];
    var initTimer = null;
    function queueInit(el) {
        pendingInit.push(el);
        if (!initTimer) {
            // thoda ruk kar, taake page apni select2 (ajax waghera) pehle laga le
            initTimer = setTimeout(function () {
                initTimer = null;
                var list = pendingInit;
                pendingInit = [];
                list.forEach(safeInit);
            }, 20);
        }
    }

    var pendingRefresh = [];
    var refreshQueued = false;
    function queueRefresh(el) {
        if (pendingRefresh.indexOf(el) === -1) {
            pendingRefresh.push(el);
        }
        if (!refreshQueued) {
            refreshQueued = true;
            Promise.resolve().then(function () {
                refreshQueued = false;
                var list = pendingRefresh;
                pendingRefresh = [];
                list.forEach(function (s) {
                    try { refresh(s); } catch (e) { /* select2 hat chuka */ }
                });
            });
        }
    }

    // ---- select2 / $(..).trigger('change') => asli change event ----
    var origTrigger = $.fn.trigger;
    $.fn.trigger = function (event) {
        if (event !== 'change' || arguments.length > 1) {
            return origTrigger.apply(this, arguments);
        }
        var rest = [];
        this.each(function () {
            if (this.tagName === 'SELECT' && !this.__v2Dispatching) {
                this.__v2Dispatching = true;
                try {
                    this.dispatchEvent(new Event('change', { bubbles: true }));
                } finally {
                    this.__v2Dispatching = false;
                }
            } else {
                rest.push(this);
            }
        });
        if (rest.length) {
            origTrigger.call($(rest), event);
        }
        return this;
    };

    // ---- JS se value badle to display update ----
    function hookSetter(proto, prop, selectOf) {
        var d = Object.getOwnPropertyDescriptor(proto, prop);
        if (!d || !d.set || !d.configurable) {
            return;
        }
        Object.defineProperty(proto, prop, {
            configurable: true,
            enumerable: d.enumerable,
            get: d.get,
            set: function (value) {
                d.set.call(this, value);
                var s = selectOf(this);
                if (s && s.classList && s.classList.contains('select2-hidden-accessible')) {
                    queueRefresh(s);
                }
            }
        });
    }
    var selectSelf = function (s) { return s; };
    hookSetter(HTMLSelectElement.prototype, 'value', selectSelf);
    hookSetter(HTMLSelectElement.prototype, 'selectedIndex', selectSelf);
    hookSetter(HTMLOptionElement.prototype, 'selected', function (o) {
        var p = o.parentElement;
        return p && p.tagName === 'OPTGROUP' ? p.parentElement : p;
    });

    document.addEventListener('reset', function (e) {
        var form = e.target;
        setTimeout(function () {
            if (form && form.querySelectorAll) {
                Array.prototype.forEach.call(form.querySelectorAll('select.select2-hidden-accessible'), queueRefresh);
            }
        }, 0);
    }, true);

    // ---- DOM changes ----
    function onMutations(records) {
        records.forEach(function (r) {
            var t = r.target;
            if (r.type === 'attributes') {
                if (t.tagName === 'SELECT') {
                    if (instanceOf(t)) {
                        syncVisibility(t);
                    } else if (!t.classList.contains('select2-hidden-accessible') && eligible(t)) {
                        queueInit(t); // pehle display:none tha, ab dikha
                    }
                }
                return;
            }
            if (t.tagName === 'SELECT' || t.tagName === 'OPTGROUP') {
                var s = t.tagName === 'SELECT' ? t : t.parentElement;
                if (s && s.classList.contains('select2-hidden-accessible')) {
                    queueRefresh(s);
                }
                return;
            }
            Array.prototype.forEach.call(r.addedNodes, function (n) {
                if (n.nodeType !== 1) {
                    return;
                }
                if (n.tagName === 'SELECT') {
                    queueInit(n);
                } else if (n.firstElementChild) {
                    Array.prototype.forEach.call(n.querySelectorAll('select'), queueInit);
                }
            });
            Array.prototype.forEach.call(r.removedNodes, function (n) {
                if (n.nodeType !== 1 || n.tagName !== 'SELECT' || n.isConnected) {
                    return;
                }
                var inst = instanceOf(n);
                if (inst) {
                    try { inst.destroy(); } catch (e) { inst.$container.remove(); }
                }
            });
        });
    }

    // ---- Livewire ----
    function destroyAuto(root) {
        var list = root.tagName === 'SELECT' ? [root] : Array.prototype.slice.call(root.querySelectorAll('select.v2-select2-auto'));
        list.forEach(function (el) {
            if (!instanceOf(el) || insideWireIgnore(el)) {
                return;
            }
            try {
                $(el).off('.v2auto').select2('destroy');
            } catch (e) { /* ignore */ }
            el.classList.remove('v2-select2', 'v2-select2-auto', 'v2-select2-fit');
        });
    }

    function registerLivewire() {
        if (!window.Livewire || !window.Livewire.hook || registerLivewire.done) {
            return;
        }
        registerLivewire.done = true;
        window.Livewire.hook('morph', function (p) { destroyAuto(p.el); });
        window.Livewire.hook('morphed', function (p) { scanNow(p.el); });
    }
    registerLivewire();
    document.addEventListener('livewire:init', registerLivewire);

    function start() {
        scanNow(document.body);
    }

    window.v2Select2 = { init: scanNow, refresh: queueRefresh, destroy: destroyAuto };

    if (window.MutationObserver) {
        new MutationObserver(onMutations).observe(document.documentElement, {
            childList: true,
            subtree: true,
            attributes: true,
            attributeFilter: ['class', 'style', 'hidden']
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () { setTimeout(start, 0); });
    } else {
        setTimeout(start, 0);
    }
    window.addEventListener('load', function () { setTimeout(start, 0); });
})();
