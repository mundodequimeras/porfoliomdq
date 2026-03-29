(function (blocks, element, components, blockEditor, data) {
    var el = element.createElement;
    var __ = wp.i18n.__;
    var InspectorControls = blockEditor.InspectorControls;
    var { PanelBody, SelectControl, RangeControl, TextControl, TextareaControl } = components;
    var { useSelect } = data;

    blocks.registerBlockType('mdq/portfolio', {
        title: __('Grilla de Proyectos MDQ', 'porfoliomdq'),
        icon: 'portfolio',
        category: 'layout',
        attributes: {
            limit: { type: 'number', default: -1 },
            category: { type: 'string', default: '' },
            language: { type: 'string', default: '' },
            title: { type: 'string', default: '' },
            subtitle: { type: 'string', default: '' }
        },

        edit: function (props) {
            var attributes = props.attributes;
            var setAttributes = props.setAttributes;

            // Fetch taxonomies
            var categories = useSelect(function (select) {
                return select('core').getEntityRecords('taxonomy', 'mdq_category', { per_page: -1 });
            }, []);

            var languages = useSelect(function (select) {
                return select('core').getEntityRecords('taxonomy', 'mdq_language', { per_page: -1 });
            }, []);

            var categoryOptions = [{ label: __('Todas', 'porfoliomdq'), value: '' }];
            if (categories) {
                categories.forEach(function (cat) {
                    categoryOptions.push({ label: cat.name, value: cat.slug });
                });
            }

            var languageOptions = [{ label: __('Todos', 'porfoliomdq'), value: '' }];
            if (languages) {
                languages.forEach(function (lang) {
                    languageOptions.push({ label: lang.name, value: lang.slug });
                });
            }

            return [
                el(InspectorControls, { key: 'inspector' },
                    el(PanelBody, { title: __('Contenido de la Cabecera', 'porfoliomdq') },
                        el(TextControl, {
                            label: __('Título de la Sección', 'porfoliomdq'),
                            value: attributes.title,
                            onChange: function (val) { setAttributes({ title: val }); },
                            placeholder: __('Ej: Mis Proyectos', 'porfoliomdq')
                        }),
                        el(TextareaControl, {
                            label: __('Subtítulo de la Sección', 'porfoliomdq'),
                            value: attributes.subtitle,
                            onChange: function (val) { setAttributes({ subtitle: val }); },
                            placeholder: __('Breve descripción...', 'porfoliomdq')
                        })
                    ),
                    el(PanelBody, { title: __('Ajustes de la Grilla', 'porfoliomdq') },
                        el(RangeControl, {
                            label: __('Límite de proyectos', 'porfoliomdq'),
                            value: attributes.limit,
                            onChange: function (val) { setAttributes({ limit: val }); },
                            min: -1,
                            max: 50
                        }),
                        el(SelectControl, {
                            label: __('Filtrar por Categoría', 'porfoliomdq'),
                            value: attributes.category,
                            options: categoryOptions,
                            onChange: function (val) { setAttributes({ category: val }); }
                        }),
                        el(SelectControl, {
                            label: __('Filtrar por Lenguaje', 'porfoliomdq'),
                            value: attributes.language,
                            options: languageOptions,
                            onChange: function (val) { setAttributes({ language: val }); }
                        })
                    )
                ),
                el('div', { className: 'mdq-block-preview', style: { background: '#f3f4f6', padding: '40px', textAlign: 'center', borderRadius: '12px', border: '2px dashed #d1d5db', color: '#4b5563' } },
                    el('i', { className: 'dashicons dashicons-portfolio', style: { fontSize: '40px', width: '40px', height: '40px', marginBottom: '10px' } }),
                    el('h2', { style: { margin: 0, fontSize: '18px', fontWeight: 'bold' } }, __('Porfolio MDQ - Vista Previa', 'porfoliomdq')),
                    el('p', { style: { fontSize: '13px', marginTop: '10px' } }, 
                        __('Mostrando proyectos filtrados.', 'porfoliomdq') + ' ' + 
                        (attributes.category ? __('Categoría: ', 'porfoliomdq') + attributes.category : '') + ' ' +
                        (attributes.language ? __('Lenguaje: ', 'porfoliomdq') + attributes.language : '')
                    )
                )
            ];
        },

        save: function () {
            return null; // Rendered via PHP
        }
    });
})(
    window.wp.blocks,
    window.wp.element,
    window.wp.components,
    window.wp.blockEditor,
    window.wp.data
);
