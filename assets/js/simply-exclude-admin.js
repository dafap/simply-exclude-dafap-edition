 (function($){
	 // Inject a simple modal into the admin footer once
	 function ensureModal() {
		 if ($('#simply-exclude-help-modal').length) return;
		 var modal = '\n            <div id="simply-exclude-help-modal" style="display:none;position:fixed;z-index:99999;left:0;top:0;right:0;bottom:0;background:rgba(0,0,0,0.5);">\n                <div style="max-width:600px;margin:6% auto;background:#fff;padding:20px;border-radius:4px;position:relative;">\n                    <button id="simply-exclude-help-close" style="position:absolute;right:10px;top:10px;">&times;</button>\n                    <div id="simply-exclude-help-content"></div>\n                </div>\n            </div>';
		 $('body').append(modal);
		 $('#simply-exclude-help-close').on('click', function(){ $('#simply-exclude-help-modal').hide(); });
		 $('#simply-exclude-help-modal').on('click', function(e){ if (e.target.id === 'simply-exclude-help-modal') $(this).hide(); });
	 }

	 $(document).ready(function(){
		 ensureModal();

		 $(document).on('click', '.simply-exclude-help', function(e){
			 e.preventDefault();
			 var text = $(this).data('help') || $(this).attr('data-help') || '';
			 $('#simply-exclude-help-content').text(text);
			 $('#simply-exclude-help-modal').show();
		 });
	 });

 })(jQuery);
