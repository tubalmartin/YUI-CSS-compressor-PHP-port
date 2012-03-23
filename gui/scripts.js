$(function(){

    var inputCss = $('#input-css')
      ,	outputCss = $('#output-css')
      ,	outputContainer = $('#output-container')
      ,	compressBtn = $('#compress-btn')
      ,	lessConsole = $('#less-error-message')

        /**
         * Prints LESS compilation errors
         */
      ,	lessError = function(e) {
            var content, errorline
              ,	template = '<li><label>{line}</label><pre class="{class}">{content}</pre></li>'
              , error = [];

            content = '<h3>'  + (e.message || 'There is an error in your .less file') +
                      '</h3>' + '<p>';

            errorline = function (e, i, classname) {
                if (e.extract[i]) {
                    error.push(template.replace(/\{line\}/, parseInt(e.line) + (i - 1))
                                       .replace(/\{class\}/, classname)
                                       .replace(/\{content\}/, e.extract[i]));
                }
            };

            if (e.stack) {
                content += '<br/>' + e.stack.split('\n').slice(1).join('<br/>');
            } else if (e.extract) {
                errorline(e, 0, '');
                errorline(e, 1, 'line');
                errorline(e, 2, '');
                content += 'on line ' + e.line + ', column ' + (e.column + 1) + ':</p>' +
                            '<ul>' + error.join('') + '</ul>';
            }

            lessConsole.html(content).slideDown('fast');
      }

        /**
         * Compresses user's CSS with the PHP port of the YUI compressor
         */
      , compress = function(formData) {
            $.post(window.location.href, formData, function(data, textStatus, jqXHR){
                // Hide LESS error console
                lessConsole.slideUp('fast');

                // Show & fill output
                outputContainer.slideDown('fast');
                outputCss.val(data);

                // Restore button state
                compressBtn.button('reset');
            });
      };



    /**
     * Controller
     */
    $('#options-form').on('submit', function(e){
        e && e.preventDefault();

        var data = {
            css: inputCss.val(),
            options: $(this).serialize()
        };

        // Change button state
        compressBtn.button('loading');

        // If LESS enabled, precompile CSS with LESS and then compress
        if (!!$('#enable-less:checked').val()) {
            new(less.Parser)().parse(data.css, function (e, tree) {
                if (e) {
                    lessError(e);
                    compressBtn.button('reset');
                } else {
                    data.css = tree.toCSS();
                    compress(data);
                }
            });
        } else {
            compress(data);
        }
    });

});