(function($) {
    'use strict';
    
    let components = [];
    let currentEditingIndex = null;
    
    // Initialize
    $(document).ready(function() {
        loadComponents();
        initSortable();
        bindEvents();
    });
    
    // Load components from hidden input
    function loadComponents() {
        const data = $('#fs-page-components').val();
        if (data) {
            try {
                components = JSON.parse(data);
                if (!Array.isArray(components)) {
                    components = [];
                }
            } catch (e) {
                components = [];
            }
        } else {
            components = [];
        }
    }
    
    // Save components to hidden input
    function saveComponents() {
        $('#fs-page-components').val(JSON.stringify(components));
    }
    
    // Initialize sortable
    function initSortable() {
        $('#fs-components-list').sortable({
            handle: '.fs-drag-handle, .fs-pb-component-header',
            placeholder: 'fs-pb-component-placeholder',
            tolerance: 'pointer',
            axis: 'y',
            update: function() {
                // Reorder components array
                const newOrder = [];
                $('#fs-components-list .fs-pb-component').each(function() {
                    const index = $(this).data('index');
                    if (components[index]) {
                        newOrder.push(components[index]);
                    }
                });
                components = newOrder;
                renderComponents();
                saveComponents();
            }
        });
    }
    
    // Bind events
    function bindEvents() {
        // Add component
        $(document).on('click', '.fs-add-component', function() {
            const type = $(this).data('type');
            addComponent(type);
        });
        
        // Edit component
        $(document).on('click', '.fs-edit-component', function() {
            const $component = $(this).closest('.fs-pb-component');
            const index = $component.data('index');
            editComponent(index);
        });
        
        // Delete component
        $(document).on('click', '.fs-delete-component', function() {
            if (!confirm(fsPageBuilder.strings.confirm_delete)) {
                return;
            }
            const $component = $(this).closest('.fs-pb-component');
            const index = $component.data('index');
            deleteComponent(index);
        });
        
        // Save component
        $(document).on('click', '.fs-save-component', function() {
            const $component = $(this).closest('.fs-pb-component');
            const index = $component.data('index');
            saveComponent(index);
        });
        
        // Cancel edit
        $(document).on('click', '.fs-cancel-edit', function() {
            const $component = $(this).closest('.fs-pb-component');
            const index = $component.data('index');
            cancelEdit(index);
        });
        
        // Image upload
        $(document).on('click', '.fs-upload-image', function(e) {
            e.preventDefault();
            const $button = $(this);
            const $wrapper = $button.closest('.fs-image-upload');
            const $imageId = $wrapper.find('.fs-field-image-id');
            const $imageUrl = $wrapper.find('.fs-field-image-url');
            const $preview = $wrapper.find('.fs-image-preview');
            
            const frame = wp.media({
                title: 'Chọn hình ảnh',
                button: {
                    text: 'Chọn hình ảnh'
                },
                multiple: false
            });
            
            frame.on('select', function() {
                const attachment = frame.state().get('selection').first().toJSON();
                $imageId.val(attachment.id);
                $imageUrl.val(attachment.url);
                
                if ($preview.length) {
                    $preview.html('<img src="' + attachment.url + '" style="max-width:200px; margin-top:10px;">');
                } else {
                    $wrapper.append('<div class="fs-image-preview"><img src="' + attachment.url + '" style="max-width:200px; margin-top:10px;"></div>');
                }
            });
            
            frame.open();
        });
    }
    
    // Add new component
    function addComponent(type) {
        const newComponent = {
            type: type,
            title: '',
            image_id: '',
            image_url: '',
            link: '',
            description: '',
            content: '',
            html: '',
            alt: '',
            css_class: ''
        };
        
        components.push(newComponent);
        const index = components.length - 1;
        renderComponents();
        editComponent(index);
        saveComponents();
    }
    
    // Edit component
    function editComponent(index) {
        if (currentEditingIndex !== null) {
            cancelEdit(currentEditingIndex);
        }
        
        currentEditingIndex = index;
        const $component = $('#fs-components-list .fs-pb-component[data-index="' + index + '"]');
        $component.find('.fs-pb-component-content').slideDown();
        
        // Focus on first input
        setTimeout(function() {
            $component.find('.fs-pb-component-form input, .fs-pb-component-form textarea').first().focus();
        }, 300);
    }
    
    // Save component
    function saveComponent(index) {
        const $component = $('#fs-components-list .fs-pb-component[data-index="' + index + '"]');
        const $form = $component.find('.fs-pb-component-form');
        
        const component = {
            type: $component.data('type'),
            title: $form.find('.fs-field-title').val() || '',
            image_id: $form.find('.fs-field-image-id').val() || '',
            image_url: $form.find('.fs-field-image-url').val() || '',
            link: $form.find('.fs-field-link').val() || '',
            description: $form.find('.fs-field-description').val() || '',
            content: $form.find('.fs-field-content').val() || '',
            html: $form.find('.fs-field-html').val() || '',
            alt: $form.find('.fs-field-alt').val() || '',
            css_class: $form.find('.fs-field-css-class').val() || ''
        };
        
        components[index] = component;
        renderComponents();
        saveComponents();
        
        // Show success message
        showNotice('Component đã được lưu thành công!', 'success');
    }
    
    // Cancel edit
    function cancelEdit(index) {
        const $component = $('#fs-components-list .fs-pb-component[data-index="' + index + '"]');
        $component.find('.fs-pb-component-content').slideUp();
        currentEditingIndex = null;
    }
    
    // Delete component
    function deleteComponent(index) {
        components.splice(index, 1);
        renderComponents();
        saveComponents();
        showNotice('Component đã được xóa!', 'success');
    }
    
    // Render all components
    function renderComponents() {
        const $list = $('#fs-components-list');
        $list.empty();
        
        if (components.length === 0) {
            $list.html('<p class="fs-pb-empty">Chưa có component nào. Click vào các nút phía trên để thêm component.</p>');
            return;
        }
        
        components.forEach(function(component, index) {
            const $component = createComponentElement(component, index);
            $list.append($component);
        });
        
        // Reinitialize sortable
        initSortable();
    }
    
    // Create component element
    function createComponentElement(component, index) {
        const type = component.type || 'text';
        const title = component.title || 'Component ' + (index + 1);
        
        let html = '<div class="fs-pb-component" data-index="' + index + '" data-type="' + type + '">';
        html += '<div class="fs-pb-component-header">';
        html += '<span class="fs-pb-component-type">' + type.charAt(0).toUpperCase() + type.slice(1) + '</span>';
        html += '<span class="fs-pb-component-title">' + (title || 'Component ' + (index + 1)) + '</span>';
        html += '<div class="fs-pb-component-actions">';
        html += '<button type="button" class="button-link fs-edit-component" title="Chỉnh sửa"><span class="dashicons dashicons-edit"></span></button>';
        html += '<button type="button" class="button-link fs-delete-component" title="Xóa"><span class="dashicons dashicons-trash"></span></button>';
        html += '<span class="dashicons dashicons-menu fs-drag-handle" title="Kéo để sắp xếp"></span>';
        html += '</div>';
        html += '</div>';
        html += '<div class="fs-pb-component-content" style="display:none;">';
        html += getComponentForm(component, index);
        html += '</div>';
        html += '</div>';
        
        return $(html);
    }
    
    // Get component form HTML
    function getComponentForm(component, index) {
        const type = component.type || 'text';
        let html = '<div class="fs-pb-component-form">';
        
        switch(type) {
            case 'banner':
                html += '<p><label><strong>Tiêu đề:</strong></label>';
                html += '<input type="text" class="widefat fs-field-title" value="' + escapeHtml(component.title || '') + '" placeholder="Nhập tiêu đề banner"></p>';
                html += '<p><label><strong>Hình ảnh:</strong></label>';
                html += '<div class="fs-image-upload">';
                html += '<input type="hidden" class="fs-field-image-id" value="' + escapeHtml(component.image_id || '') + '">';
                html += '<input type="text" class="widefat fs-field-image-url" value="' + escapeHtml(component.image_url || '') + '" placeholder="URL hình ảnh">';
                html += '<button type="button" class="button fs-upload-image">Chọn hình ảnh</button>';
                if (component.image_url) {
                    html += '<div class="fs-image-preview"><img src="' + escapeHtml(component.image_url) + '" style="max-width:200px; margin-top:10px;"></div>';
                }
                html += '</div></p>';
                html += '<p><label><strong>Link (URL):</strong></label>';
                html += '<input type="url" class="widefat fs-field-link" value="' + escapeHtml(component.link || '') + '" placeholder="https://example.com"></p>';
                html += '<p><label><strong>Mô tả:</strong></label>';
                html += '<textarea class="widefat fs-field-description" rows="3" placeholder="Mô tả banner">' + escapeHtml(component.description || '') + '</textarea></p>';
                html += '<p><label><strong>CSS Class tùy chỉnh:</strong></label>';
                html += '<input type="text" class="widefat fs-field-css-class" value="' + escapeHtml(component.css_class || '') + '" placeholder="custom-banner-class"></p>';
                break;
                
            case 'image':
                html += '<p><label><strong>Tiêu đề:</strong></label>';
                html += '<input type="text" class="widefat fs-field-title" value="' + escapeHtml(component.title || '') + '" placeholder="Nhập tiêu đề"></p>';
                html += '<p><label><strong>Hình ảnh:</strong></label>';
                html += '<div class="fs-image-upload">';
                html += '<input type="hidden" class="fs-field-image-id" value="' + escapeHtml(component.image_id || '') + '">';
                html += '<input type="text" class="widefat fs-field-image-url" value="' + escapeHtml(component.image_url || '') + '" placeholder="URL hình ảnh">';
                html += '<button type="button" class="button fs-upload-image">Chọn hình ảnh</button>';
                if (component.image_url) {
                    html += '<div class="fs-image-preview"><img src="' + escapeHtml(component.image_url) + '" style="max-width:200px; margin-top:10px;"></div>';
                }
                html += '</div></p>';
                html += '<p><label><strong>Link (URL):</strong></label>';
                html += '<input type="url" class="widefat fs-field-link" value="' + escapeHtml(component.link || '') + '" placeholder="https://example.com"></p>';
                html += '<p><label><strong>Alt Text:</strong></label>';
                html += '<input type="text" class="widefat fs-field-alt" value="' + escapeHtml(component.alt || '') + '" placeholder="Mô tả hình ảnh"></p>';
                html += '<p><label><strong>CSS Class:</strong></label>';
                html += '<input type="text" class="widefat fs-field-css-class" value="' + escapeHtml(component.css_class || '') + '" placeholder="custom-image-class"></p>';
                break;
                
            case 'text':
                html += '<p><label><strong>Tiêu đề:</strong></label>';
                html += '<input type="text" class="widefat fs-field-title" value="' + escapeHtml(component.title || '') + '" placeholder="Nhập tiêu đề"></p>';
                html += '<p><label><strong>Nội dung:</strong></label>';
                // Use textarea for now, can be enhanced with TinyMCE
                html += '<textarea class="widefat fs-field-content" rows="10" placeholder="Nhập nội dung">' + escapeHtml(component.content || '') + '</textarea>';
                html += '<small>Bạn có thể sử dụng HTML trong nội dung</small></p>';
                html += '<p><label><strong>CSS Class:</strong></label>';
                html += '<input type="text" class="widefat fs-field-css-class" value="' + escapeHtml(component.css_class || '') + '" placeholder="custom-text-class"></p>';
                break;
                
            case 'html':
                html += '<p><label><strong>Tiêu đề:</strong></label>';
                html += '<input type="text" class="widefat fs-field-title" value="' + escapeHtml(component.title || '') + '" placeholder="Nhập tiêu đề"></p>';
                html += '<p><label><strong>HTML Code:</strong></label>';
                html += '<textarea class="widefat fs-field-html" rows="10" placeholder="Nhập HTML code tùy chỉnh">' + escapeHtml(component.html || '') + '</textarea>';
                html += '<small>Bạn có thể nhập HTML, CSS, JavaScript tùy chỉnh</small></p>';
                html += '<p><label><strong>CSS Class:</strong></label>';
                html += '<input type="text" class="widefat fs-field-css-class" value="' + escapeHtml(component.css_class || '') + '" placeholder="custom-html-class"></p>';
                break;
        }
        
        html += '<p class="fs-pb-form-actions">';
        html += '<button type="button" class="button button-primary fs-save-component">Lưu Component</button>';
        html += '<button type="button" class="button fs-cancel-edit">Hủy</button>';
        html += '</p>';
        html += '</div>';
        
        return html;
    }
    
    // Escape HTML
    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text ? text.replace(/[&<>"']/g, function(m) { return map[m]; }) : '';
    }
    
    // Show notice
    function showNotice(message, type) {
        type = type || 'info';
        const $notice = $('<div class="notice notice-' + type + ' is-dismissible"><p>' + message + '</p></div>');
        $('.fs-page-builder-wrapper').before($notice);
        
        setTimeout(function() {
            $notice.fadeOut(function() {
                $(this).remove();
            });
        }, 3000);
    }
    
})(jQuery);

