(function ($) {

    const highlightWords = function ($element, settings) {

        if (!settings.heading_text) return;

        let text = settings.heading_text + '';

        if (Array.isArray(settings.highlight_words)) {
            settings.highlight_words.forEach(high => {

                const word = (high.word || '').trim();
                if (!word) return;

                let innerStyles = [];
                let innerClasses = [];
                let outerStyles = [];

                // TEXT COLOR SOLID
                if (high.text_type === 'solid' && high.text_color) {
                    innerStyles.push(`color: ${high.text_color}`);
                }

                // TEXT COLOR GRADIENT
                if (high.text_type === 'gradient' &&
                    high.text_gradient_color_1 &&
                    high.text_gradient_color_2) {

                    innerClasses.push('heading-text-gradient');

                    const angle = high.gradient_angle || 0;
                    const stop1 = high.gradient_stop_1 || 0;
                    const stop2 = high.gradient_stop_2 || 100;

                    innerStyles.push(
                        `background: linear-gradient(${angle}deg, ${high.text_gradient_color_1} ${stop1}%, ${high.text_gradient_color_2} ${stop2}%)`
                    );
                }

                // BACKGROUND COLOR
                if (high.background_type === 'color' && high.background_color) {

                    outerStyles.push(`background-color: ${high.background_color}`);

                    const unit = (high.padding && high.padding.unit) || 'px';
                    const top = high.padding?.top || 2;
                    const right = high.padding?.right || 2;
                    const bottom = high.padding?.bottom || 2;
                    const left = high.padding?.left || 2;

                    outerStyles.push(
                        `padding: ${top}${unit} ${right}${unit} ${bottom}${unit} ${left}${unit}`
                    );
                    outerStyles.push('display: inline-block');

                    const radius = high.border_radius?.size || 2;
                    outerStyles.push(`border-radius: ${radius}px`);
                }

                // BACKGROUND GRADIENT
                if (high.background_type === 'gradient' &&
                    high.background_gradient_color_1 &&
                    high.background_gradient_color_2) {

                    const angle = high.background_gradient_angle || 0;
                    const stop1 = high.background_gradient_stop_1 || 0;
                    const stop2 = high.background_gradient_stop_2 || 100;

                    outerStyles.push(
                        `background: linear-gradient(${angle}deg, ${high.background_gradient_color_1} ${stop1}%, ${high.background_gradient_color_2} ${stop2}%)`
                    );

                    const unit = (high.padding && high.padding.unit) || 'px';
                    const top = high.padding?.top || 2;
                    const right = high.padding?.right || 2;
                    const bottom = high.padding?.bottom || 2;
                    const left = high.padding?.left || 2;

                    outerStyles.push(
                        `padding: ${top}${unit} ${right}${unit} ${bottom}${unit} ${left}${unit}`
                    );
                    outerStyles.push('display: inline-block');

                    const radius = high.border_radius?.size || 2;
                    outerStyles.push(`border-radius: ${radius}px`);
                }

                // BUILD FINAL HTML
                const innerClassAttr = innerClasses.length ? `class="${innerClasses.join(' ')}"` : '';
                const innerStyleAttr = innerStyles.length ? `style="${innerStyles.join(';')}"` : '';
                const outerStyleAttr = outerStyles.length ? `style="${outerStyles.join(';')}"` : '';

                const escaped = word.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');

                let regex;

                try {
                    regex = new RegExp(`(?<!\\p{L})(${escaped})(?!\\p{L})`, 'giu');
                } catch (e) {
                    regex = new RegExp(`\\b(${escaped})\\b`, 'gi');
                }

                const replacement =
                    `<span ${outerStyleAttr}><span ${innerClassAttr} ${innerStyleAttr}>$1</span></span>`;

                text = text.replace(regex, replacement);
            });
        }

        $element.find('.heading-style-widget-heading').html(text);
    };

    // Elementor Hook
    $(window).on('elementor/frontend/init', function () {

        elementor.hooks.addAction('panel/open_editor/widget', function (panel, model) {

            const type = model.get('widgetType');
            if (type !== 'heading-style-widget') return;

            // Listen for any control change
            model.on('change', function () {

                const settings = model.attributes.settings.attributes;

                const widget = elementorFrontend.documentsManager.documents[model.get('id')];
                if (!widget) return;

                const $wrapper = widget.$element;

                // Apply live preview
                highlightWords($wrapper, settings);

                // Update alignment
                if (settings.text_align) {
                    $wrapper.find('.heading-style-widget-heading')
                        .css('text-align', settings.text_align);
                }
            });
        });

    });

})(jQuery);
