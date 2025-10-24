(function($) {
  // Vérifie qu'on est bien sur la liste des articles (edit.php)
  const isPostListScreen = window.location.pathname.includes('/wp-admin/edit.php');

  if (!isPostListScreen) {
    console.log('simply-exclude: script ignoré sur cet écran');
    return;
  }

  function initSimplyExcludeQuickEdit() {
    const inlineEditPost = window.inlineEditPost;

    if (!inlineEditPost || typeof inlineEditPost.edit !== 'function') {
      console.warn('simply-exclude: inlineEditPost.edit non disponible');
      return;
    }

    const originalEdit = inlineEditPost.edit;

    inlineEditPost.edit = function(postId) {
      originalEdit.apply(this, arguments);

      let id = typeof postId === 'object' && typeof this.getId === 'function'
        ? parseInt(this.getId(postId), 10)
        : parseInt(postId, 10);

      if (isNaN(id) || id <= 0) return;

      const dataDiv = $('#simply_exclude_data_' + id);
      if (!dataDiv.length) return;

      let flags = [];
      try {
        const raw = dataDiv.attr('data-flags');
        const parsed = JSON.parse(raw);
        flags = Array.isArray(parsed) ? parsed : [];
      } catch (e) {
        console.warn('simply-exclude: JSON invalide pour post ' + id);
      }

      const container = $('.inline-edit-row');
      container.find('input[name^="simply_exclude["]').prop('checked', false);

      flags.forEach(function(flag) {
        const selector = 'input[name="simply_exclude[' + flag + ']"]';
        container.find(selector).prop('checked', true);
      });
    };

    console.log('simply-exclude: surcharge de Quick Edit initialisée');
  }

  // Attente active jusqu’à ce que inlineEditPost soit prêt
  $(document).ready(function() {
    const maxTries = 20;
    let tries = 0;

    const interval = setInterval(function() {
      if (window.inlineEditPost && typeof window.inlineEditPost.edit === 'function') {
        clearInterval(interval);
        initSimplyExcludeQuickEdit();
      } else if (++tries >= maxTries) {
        clearInterval(interval);
        console.warn('simply-exclude: inlineEditPost.edit toujours indisponible après attente');
      }
    }, 200);
  });
})(jQuery);
