(function ($) {
    const PageSections = {
        init() {
            this.$wrapper = $('.fs-page-sections-wrapper');
            if (!this.$wrapper.length) return;

            this.availableSections = this.$wrapper.data('available') || [];
            this.$availableList = $('#fs-available-section-list');
            this.$selectedList = $('#fs-selected-section-list');
            this.$search = $('#fs-section-search');
            this.$hiddenInput = $('#fs-attached-sections');
            this.$emptyState = $('.fs-selected-empty');

            this.state = this.parseJSON(this.$hiddenInput.val()) || [];
            this.renderAvailable();
            this.renderSelected();
            this.bindEvents();
            this.toggleEmptyState();
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

            this.$availableList.on('click', '.fs-add-section', function (e) {
                e.preventDefault();
                const id = $(this).data('id');
                self.addSection(id);
            });

            this.$selectedList.sortable({
                axis: 'y',
                handle: '.fs-section-handle',
                update() {
                    const newOrder = [];
                    self.$selectedList.find('li').each(function () {
                        const itemId = $(this).data('id');
                        const existing = self.state.find((item) => item.id === itemId);
                        if (existing) {
                            newOrder.push(existing);
                        }
                    });
                    self.state = newOrder;
                    self.saveState();
                },
            });

            this.$selectedList.on('click', '.fs-remove-section', function (e) {
                e.preventDefault();
                const id = $(this).closest('li').data('id');
                self.removeSection(id);
            });

            this.$search.on('input', () => {
                const query = this.$search.val().toLowerCase();
                this.$availableList.find('li').each(function () {
                    const text = $(this).text().toLowerCase();
                    $(this).toggle(text.indexOf(query) !== -1);
                });
            });
        },

        renderAvailable() {
            const selectedIds = this.state.map((item) => item.id);
            const items = this.availableSections.map((section) => {
                const disabled = selectedIds.includes(section.id) ? 'disabled' : '';
                const group = section.group && section.group.length ? section.group.join(', ') : '';
                return `<li data-id="${section.id}">
                    <div>
                        <strong>${section.title}</strong>
                        <div class="fs-section-meta">${group}</div>
                    </div>
                    <button type="button" class="button button-secondary fs-add-section" data-id="${section.id}" ${disabled}>Add</button>
                </li>`;
            });
            this.$availableList.html(items.join(''));
        },

        renderSelected() {
            const items = this.state.map((section) => {
                const info = this.availableSections.find((item) => item.id === section.id);
                const title = info ? info.title : `Section #${section.id}`;
                return `<li class="fs-section-item" data-id="${section.id}">
                    <span class="dashicons dashicons-move fs-section-handle"></span>
                    <div>
                        <strong>${title}</strong>
                        <div class="fs-section-meta">${section.notes || ''}</div>
                    </div>
                    <button type="button" class="button-link fs-remove-section"><span class="dashicons dashicons-no"></span></button>
                </li>`;
            });
            this.$selectedList.html(items.join(''));
            this.renderAvailable();
            this.toggleEmptyState();
        },

        addSection(id) {
            id = parseInt(id, 10);
            if (this.state.find((item) => item.id === id)) return;
            this.state.push({
                id,
                visibility: 'all',
                notes: '',
            });
            this.saveState();
            this.renderSelected();
        },

        removeSection(id) {
            id = parseInt(id, 10);
            this.state = this.state.filter((item) => item.id !== id);
            this.saveState();
            this.renderSelected();
        },

        toggleEmptyState() {
            if (this.state.length) {
                this.$emptyState.hide();
            } else {
                this.$emptyState.show();
            }
        },

        saveState() {
            this.$hiddenInput.val(JSON.stringify(this.state));
        },
    };

    $(document).ready(function () {
        PageSections.init();
    });
})(jQuery);

