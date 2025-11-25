(function ($) {
    const SectionBuilder = {
        init() {
            this.$wrapper = $('.fs-section-builder-wrapper');
            if (!this.$wrapper.length) return;

            this.$canvas = $('#fs-section-canvas');
            this.$library = $('#fs-block-library');
            this.$settings = $('#fs-block-settings');
            this.$search = $('#fs-builder-search');
            this.$toggleJson = this.$wrapper.find('.fs-toggle-json');
            this.$applyJson = this.$wrapper.find('.fs-apply-json');
            this.$jsonArea = this.$wrapper.find('.fs-builder-json');
            this.$rawTextarea = $('#fs-section-blocks-raw');
            this.$hiddenInput = $('#fs-section-blocks');

            this.definitions = this.parseJSON(this.$wrapper.attr('data-block-definitions')) || {};
            this.state = {
                blocks: this.parseJSON(this.$hiddenInput.val()) || [],
                selectedId: null,
            };

            this.bindEvents();
            this.renderLibrary();
            this.renderCanvas();
            this.renderInspector();
            this.updateEmptyState();
        },

        parseJSON(value) {
            if (!value) return null;
            try {
                return JSON.parse(value);
            } catch (err) {
                return null;
            }
        },

        bindEvents() {
            const self = this;

            this.$library.on('click', '.fs-block-add', function (e) {
                e.preventDefault();
                self.addBlock($(this).data('block'));
            });

            this.$canvas.sortable({
                axis: 'y',
                handle: '.fs-canvas-item-handle',
                placeholder: 'fs-canvas-sortable-placeholder',
                update() {
                    const ids = [];
                    self.$canvas.find('.fs-canvas-item').each(function () {
                        ids.push($(this).data('blockId'));
                    });
                    const reordered = [];
                    ids.forEach((id) => {
                        const found = self.state.blocks.find((block) => block.id === id);
                        if (found) reordered.push(found);
                    });
                    self.state.blocks = reordered;
                    self.saveState();
                    self.renderCanvas();
                },
            });

            this.$canvas.on('click', '.fs-canvas-item', function (e) {
                if ($(e.target).closest('button').length) return;
                const id = $(this).data('blockId');
                self.selectBlock(id);
            });

            this.$canvas.on('click', '.fs-block-delete', function (e) {
                e.preventDefault();
                e.stopPropagation();
                const id = $(this).closest('.fs-canvas-item').data('blockId');
                if (confirm(fsSectionBuilder.strings.delete_confirm)) {
                    self.deleteBlock(id);
                }
            });

            this.$canvas.on('click', '.fs-block-duplicate', function (e) {
                e.preventDefault();
                e.stopPropagation();
                const id = $(this).closest('.fs-canvas-item').data('blockId');
                self.duplicateBlock(id);
            });

            this.$search.on('input', function () {
                self.filterLibrary($(this).val().toLowerCase());
            });

            this.$settings.on('input change', '.fs-field-input', function () {
                const fieldPath = $(this).data('path');
                const blockId = $(this).data('blockId');
                let value = $(this).val();

                if ($(this).attr('type') === 'number') {
                    value = $(this).val() === '' ? '' : parseFloat($(this).val());
                }

                if ($(this).attr('type') === 'checkbox') {
                    value = $(this).is(':checked');
                }

                self.updateField(blockId, fieldPath, value);
            });

            this.$settings.on('click', '.fs-image-select', function (e) {
                e.preventDefault();
                const blockId = $(this).data('blockId');
                const fieldPath = $(this).data('path');

                const frame = wp.media({
                    title: 'Select image',
                    multiple: false,
                });

                frame.on('select', () => {
                    const attachment = frame.state().get('selection').first().toJSON();
                    const value = { id: attachment.id, url: attachment.url };
                    self.updateField(blockId, fieldPath, value);
                });

                frame.open();
            });

            this.$settings.on('click', '.fs-image-remove', function (e) {
                e.preventDefault();
                const blockId = $(this).data('blockId');
                const fieldPath = $(this).data('path');
                self.updateField(blockId, fieldPath, null);
            });

            this.$settings.on('click', '.fs-repeater-add', function (e) {
                e.preventDefault();
                const blockId = $(this).data('blockId');
                const fieldPath = $(this).data('path');
                const defaultValue = $(this).data('default');
                self.addRepeaterItem(blockId, fieldPath, defaultValue);
            });

            this.$settings.on('click', '.fs-repeater-item-remove', function (e) {
                e.preventDefault();
                if (!confirm(fsSectionBuilder.strings.delete_repeater_confirm)) {
                    return;
                }
                const blockId = $(this).data('blockId');
                const fieldPath = $(this).data('path');
                self.removeRepeaterItem(blockId, fieldPath);
            });

            this.$toggleJson.on('click', () => {
                this.$jsonArea.toggleClass('fs-builder-json-hidden');
            });

            this.$rawTextarea.on('input', () => {
                this.$applyJson.prop('disabled', false);
            });

            this.$applyJson.on('click', () => {
                const parsed = this.parseJSON(this.$rawTextarea.val());
                if (!Array.isArray(parsed)) {
                    alert(fsSectionBuilder.strings.json_parse_error);
                    return;
                }
                this.state.blocks = parsed;
                this.state.selectedId = null;
                this.saveState();
                this.renderCanvas();
                this.renderInspector();
                this.updateEmptyState();
                this.$applyJson.prop('disabled', true);
            });
        },

        renderLibrary() {
            const cards = Object.keys(this.definitions).map((key) => {
                const block = this.definitions[key];
                return `<div class="fs-block-library-card" data-block-key="${key}">
                    <h4>${block.label}</h4>
                    <p>${block.description || ''}</p>
                    <button type="button" class="button button-secondary fs-block-add" data-block="${key}">
                        ${block.button_label || 'Add block'}
                    </button>
                </div>`;
            });
            this.$library.html(cards.join(''));
        },

        renderCanvas() {
            const items = this.state.blocks.map((block) => {
                const def = this.definitions[block.type] || {};
                const title = def.label || block.type;
                const summary = this.getBlockSummary(block);
                const selectedClass = block.id === this.state.selectedId ? 'is-selected' : '';
                return `<li class="fs-canvas-item ${selectedClass}" data-block-id="${block.id}">
                    <span class="dashicons dashicons-move fs-canvas-item-handle"></span>
                    <div class="fs-canvas-item-title">${title}</div>
                    <div class="fs-canvas-item-meta">${summary}</div>
                    <div class="fs-canvas-item-actions">
                        <button type="button" class="button-link fs-block-duplicate" title="Duplicate">
                            <span class="dashicons dashicons-admin-page"></span>
                        </button>
                        <button type="button" class="button-link fs-block-delete" title="Delete">
                            <span class="dashicons dashicons-trash"></span>
                        </button>
                    </div>
                </li>`;
            });
            this.$canvas.html(items.join(''));
            this.updateEmptyState();
        },

        renderInspector() {
            const block = this.state.blocks.find((item) => item.id === this.state.selectedId);
            if (!block) {
                this.$settings.html(`<p class="fs-settings-empty">${fsSectionBuilder.strings.empty_settings}</p>`);
                return;
            }

            const def = this.definitions[block.type];
            if (!def) {
                this.$settings.html('<p>' + __('Unknown block definition', 'fashion-store') + '</p>');
                return;
            }

            const fields = Object.keys(def.fields).map((fieldKey) => {
                const fieldDef = def.fields[fieldKey];
                const value = this.getFieldValue(block, fieldKey);
                return this.renderField(block, fieldKey, fieldDef, fieldKey, value);
            });

            this.$settings.html(fields.join(''));
            this.$settings.find('.fs-color-field').wpColorPicker({
                change: (event, ui) => {
                    $(event.target).val(ui.color.toString()).trigger('change');
                },
            });
        },

        getFieldValue(block, fieldKey) {
            if (!block.settings) block.settings = {};
            if (typeof block.settings[fieldKey] === 'undefined') {
                block.settings[fieldKey] = this.getDefaultValue(this.definitions[block.type].fields[fieldKey]);
            }
            return block.settings[fieldKey];
        },

        renderField(block, fieldKey, fieldDef, path, value) {
            const blockId = block.id;
            const label = fieldDef.label || fieldKey;

            switch (fieldDef.type) {
                case 'text':
                case 'link':
                case 'code':
                case 'textarea':
                case 'number':
                case 'select':
                case 'switch':
                case 'background':
                case 'image':
                case 'repeater':
                case 'taxonomy':
                case 'product_selector':
                    break;
                default:
                    fieldDef.type = 'text';
            }

            const inputAttr = `data-block-id="${blockId}" data-path="${path}" class="fs-field-input"`;

            let html = `<div class="fs-field-group fs-field-${fieldDef.type}">
                <label>${label}</label>`;

            switch (fieldDef.type) {
                case 'text':
                    html += `<input type="text" ${inputAttr} value="${value !== undefined ? this.escape(value) : ''}" />`;
                    break;
                case 'number':
                    const step = fieldDef.step ? `step="${fieldDef.step}"` : '';
                    const min = fieldDef.min ? `min="${fieldDef.min}"` : '';
                    const max = fieldDef.max ? `max="${fieldDef.max}"` : '';
                    html += `<input type="number" ${inputAttr} ${min} ${max} ${step} value="${value !== undefined ? value : ''}" />`;
                    break;
                case 'textarea':
                case 'code':
                    html += `<textarea ${inputAttr} rows="4">${value !== undefined ? this.escape(value) : ''}</textarea>`;
                    break;
                case 'select':
                    html += `<select ${inputAttr}>`;
                    $.each(fieldDef.choices || {}, (k, v) => {
                        const selected = value == k ? 'selected' : '';
                        html += `<option value="${k}" ${selected}>${v}</option>`;
                    });
                    html += '</select>';
                    break;
                case 'switch':
                    const checked = value ? 'checked' : '';
                    html += `<label class="fs-switch"><input type="checkbox" ${inputAttr} ${checked} /> ${fieldDef.switch_label || ''}</label>`;
                    break;
                case 'link':
                    const linkValue = value || { url: '', label: '', target: '_self' };
                    html += `<input type="text" ${inputAttr} data-path="${path}.label" value="${this.escape(linkValue.label || '')}" placeholder="Label" />
                        <input type="url" ${inputAttr} data-path="${path}.url" value="${this.escape(linkValue.url || '')}" placeholder="URL" />
                        <select ${inputAttr} data-path="${path}.target">
                            <option value="_self" ${linkValue.target === '_self' ? 'selected' : ''}>Same tab</option>
                            <option value="_blank" ${linkValue.target === '_blank' ? 'selected' : ''}>New tab</option>
                        </select>`;
                    break;
                case 'image':
                    const imagePreview = value && value.url ? `<div class="fs-image-preview"><img src="${value.url}" alt=""></div>` : '';
                    html += `<div class="fs-image-field">
                        <button type="button" class="button fs-image-select" data-block-id="${blockId}" data-path="${path}">${value ? 'Change image' : 'Select image'}</button>
                        ${value ? `<button type="button" class="button-link fs-image-remove" data-block-id="${blockId}" data-path="${path}">Remove</button>` : ''}
                        ${imagePreview}
                    </div>`;
                    break;
                case 'background':
                    const bg = value || {};
                    html += `<label>Background color</label>
                        <input type="text" class="fs-field-input fs-color-field" data-block-id="${blockId}" data-path="${path}.color" value="${this.escape(bg.color || '')}" />
                        <label>Background image</label>`;
                    const bgImage = bg.image && bg.image.url ? `<div class="fs-image-preview"><img src="${bg.image.url}" alt=""></div>` : '';
                    html += `<div class="fs-image-field">
                        <button type="button" class="button fs-image-select" data-block-id="${blockId}" data-path="${path}.image">${bg.image ? 'Change image' : 'Select image'}</button>
                        ${bg.image ? `<button type="button" class="button-link fs-image-remove" data-block-id="${blockId}" data-path="${path}.image">Remove</button>` : ''}
                        ${bgImage}
                    </div>
                    <label>Overlay (rgba or hex)</label>
                    <input type="text" class="fs-field-input" data-block-id="${blockId}" data-path="${path}.overlay" value="${this.escape(bg.overlay || '')}" />`;
                    break;
                case 'repeater':
                    html += this.renderRepeaterField(block, fieldKey, fieldDef, path, value);
                    break;
                case 'taxonomy':
                    const choices = fieldDef.choices || [];
                    const multiple = fieldDef.allow_multiple ? 'multiple' : '';
                    const currentValue = value || [];
                    html += `<select ${inputAttr} ${multiple}>`;
                    choices.forEach((choice) => {
                        const selected = Array.isArray(currentValue) ? currentValue.includes(choice.slug) : currentValue === choice.slug;
                        html += `<option value="${choice.slug}" ${selected ? 'selected' : ''}>${choice.name}</option>`;
                    });
                    html += '</select>';
                    break;
                case 'product_selector':
                    html += `<textarea ${inputAttr} rows="2" placeholder="Enter product IDs separated by comma">${this.escape(value || '')}</textarea>
                        <p class="description">Enter product IDs or SKUs separated by comma.</p>`;
                    break;
            }

            if (fieldDef.description) {
                html += `<p class="fs-field-description">${fieldDef.description}</p>`;
            }

            html += '</div>';
            return html;
        },

        renderRepeaterField(block, fieldKey, fieldDef, path, value) {
            const items = Array.isArray(value) ? value : [];
            const defaultItem = this.getDefaultValue(fieldDef);
            let html = `<div class="fs-repeater" data-field="${fieldKey}">
                <div class="fs-repeater-items">`;
            items.forEach((itemValue, index) => {
                const itemPath = `${path}.${index}`;
                html += `<div class="fs-repeater-item">
                    <div class="fs-repeater-item-actions">
                        <button type="button" class="button-link fs-repeater-item-remove" data-block-id="${block.id}" data-path="${itemPath}"><span class="dashicons dashicons-trash"></span></button>
                    </div>
                    ${Object.keys(fieldDef.fields).map((childKey) => {
                        const childDef = fieldDef.fields[childKey];
                        const childValue = typeof itemValue[childKey] !== 'undefined' ? itemValue[childKey] : this.getDefaultValue(childDef);
                        return this.renderField(block, childKey, childDef, `${itemPath}.${childKey}`, childValue);
                    }).join('')}
                </div>`;
            });
            html += `</div>
                <button type="button" class="button button-secondary fs-repeater-add" data-block-id="${block.id}" data-path="${path}" data-default='${JSON.stringify(defaultItem)}'>${fieldDef.add_button_label || 'Add item'}</button>
            </div>`;
            return html;
        },

        escape(value) {
            if (value === null || typeof value === 'undefined') return '';
            return $('<div>').text(value).html();
        },

        addBlock(type) {
            if (!this.definitions[type]) return;
            const id = 'block_' + Date.now() + '_' + Math.floor(Math.random() * 999);
            const block = {
                id,
                type,
                settings: this.getDefaultSettings(type),
            };
            this.state.blocks.push(block);
            this.state.selectedId = id;
            this.saveState();
            this.renderCanvas();
            this.renderInspector();
        },

        getDefaultSettings(type) {
            const def = this.definitions[type];
            const settings = {};
            if (!def) return settings;
            Object.keys(def.fields).forEach((key) => {
                settings[key] = this.getDefaultValue(def.fields[key]);
            });
            return settings;
        },

        getDefaultValue(field) {
            switch (field.type) {
                case 'text':
                case 'textarea':
                case 'code':
                case 'taxonomy':
                case 'product_selector':
                    return field.default || '';
                case 'number':
                    return typeof field.default !== 'undefined' ? field.default : '';
                case 'select':
                    if (typeof field.default !== 'undefined') return field.default;
                    const keys = Object.keys(field.choices || {});
                    return keys.length ? keys[0] : '';
                case 'switch':
                    return !!field.default;
                case 'link':
                    return field.default || { url: '', label: '', target: '_self' };
                case 'image':
                    return field.default || null;
                case 'background':
                    return field.default || { color: '', image: null, overlay: '' };
                case 'repeater':
                    const min = field.min || 0;
                    const items = [];
                    for (let i = 0; i < min; i++) {
                        items.push(this.getDefaultValue({ type: 'group', fields: field.fields }));
                    }
                    return items;
                case 'group':
                    const groupValues = {};
                    Object.keys(field.fields || {}).forEach((key) => {
                        groupValues[key] = this.getDefaultValue(field.fields[key]);
                    });
                    return groupValues;
                default:
                    return field.default || '';
            }
        },

        filterLibrary(query) {
            this.$library.find('.fs-block-library-card').each(function () {
                const text = $(this).text().toLowerCase();
                $(this).toggle(text.indexOf(query) !== -1);
            });
        },

        selectBlock(id) {
            this.state.selectedId = id;
            this.renderCanvas();
            this.renderInspector();
        },

        deleteBlock(id) {
            this.state.blocks = this.state.blocks.filter((block) => block.id !== id);
            if (this.state.selectedId === id) {
                this.state.selectedId = null;
            }
            this.saveState();
            this.renderCanvas();
            this.renderInspector();
        },

        duplicateBlock(id) {
            const block = this.state.blocks.find((item) => item.id === id);
            if (!block) return;
            const clone = JSON.parse(JSON.stringify(block));
            clone.id = 'block_' + Date.now() + '_' + Math.floor(Math.random() * 999);
            this.state.blocks.push(clone);
            this.saveState();
            this.renderCanvas();
        },

        updateField(blockId, path, value) {
            const block = this.state.blocks.find((item) => item.id === blockId);
            if (!block) return;
            if (!block.settings) block.settings = {};
            const segments = path.split('.');
            let target = block.settings;
            for (let i = 0; i < segments.length - 1; i++) {
                if (typeof target[segments[i]] === 'undefined') {
                    target[segments[i]] = {};
                }
                target = target[segments[i]];
            }
            target[segments[segments.length - 1]] = value;
            this.saveState();
        },

        addRepeaterItem(blockId, path, defaultValue) {
            const block = this.state.blocks.find((item) => item.id === blockId);
            if (!block) return;
            const segments = path.split('.');
            let target = block.settings;
            segments.forEach((segment, index) => {
                if (typeof target[segment] === 'undefined') {
                    target[segment] = index === segments.length - 1 ? [] : {};
                }
                target = target[segment];
            });
            if (!Array.isArray(target)) return;
            target.push(defaultValue || {});
            this.saveState();
            this.renderInspector();
        },

        removeRepeaterItem(blockId, path) {
            const block = this.state.blocks.find((item) => item.id === blockId);
            if (!block) return;
            const segments = path.split('.');
            const index = parseInt(segments.pop(), 10);
            let target = block.settings;
            segments.forEach((segment) => {
                target = target[segment];
            });
            if (Array.isArray(target)) {
                target.splice(index, 1);
                this.saveState();
                this.renderInspector();
            }
        },

        getBlockSummary(block) {
            const def = this.definitions[block.type];
            if (!def) return '';
            if (block.settings && block.settings.title) {
                return block.settings.title;
            }
            if (block.settings && block.settings.heading) {
                return block.settings.heading;
            }
            if (block.settings && block.settings.slides) {
                return block.settings.slides.length + ' slides';
            }
            return '';
        },

        updateEmptyState() {
            if (this.state.blocks.length) {
                this.$wrapper.find('.fs-canvas-empty-state').hide();
            } else {
                this.$wrapper.find('.fs-canvas-empty-state').show();
            }
        },

        saveState() {
            const json = JSON.stringify(this.state.blocks);
            this.$hiddenInput.val(json);
            this.$rawTextarea.val(json);
            this.$applyJson.prop('disabled', true);
        },
    };

    $(document).ready(function () {
        if (typeof fsSectionBuilder === 'undefined') return;
        SectionBuilder.init();
    });
})(jQuery);

