(function($){
    // Quand Quick Edit est ouvert (Quick Edit uses inline-edit-post), WordPress triggers 'inlineEditPost' event
    var inlineEditPost = window.inlineEditPost;

    // on sauvegarde le constructeur original
    var _edit = inlineEditPost.edit;

    inlineEditPost.edit = function( postId ) {
        // appel du constructeur original
        _edit.apply(this, arguments);

        // postId peut être un objet ou nombre selon l'appel
        var id = 0;
        if ( typeof(postId) === 'object' ) {
            id = parseInt(this.getId(postId));
        } else {
            id = parseInt(postId, 10);
        }

        if ( isNaN(id) || id <= 0 ) return;

        // trouver le container qui contient les flags JSON (renderColumn crée #simply_exclude_data_{id})
        var dataDiv = $('#simply_exclude_data_' + id);
        if ( ! dataDiv.length ) return;

        var flags = [];
        var raw = dataDiv.attr('data-flags');
        try {
            flags = JSON.parse(raw);
            if (!Array.isArray(flags)) flags = [];
        } catch (e) {
            flags = [];
        }

        // décocher toutes les cases dans l'inline edit pour ce nom (utilisant le même nom que la meta box)
        var container = $('.inline-edit-row');
        container.find('input[name^="simply_exclude["]') .prop('checked', false);

        // cocher celles qui correspondent (nom associatif: simply_exclude[key])
        flags.forEach(function(flag){
            var selector = 'input[name="simply_exclude[' + flag + ']"]';
            container.find(selector).prop('checked', true);
        });
    };

})(jQuery);
