function BxWallPost(oOptions) {    
    this._sActionsUrl = oOptions.sActionUrl;
    this._sObjName = oOptions.sObjName == undefined ? 'oWallPost' : oOptions.sObjName;
    this._iOwnerId = oOptions.iOwnerId == undefined ? 0 : parseInt(oOptions.iOwnerId);
    this._iGlobAllowHtml = 0;
    this._sAnimationEffect = oOptions.sAnimationEffect == undefined ? 'slide' : oOptions.sAnimationEffect;
    this._iAnimationSpeed = oOptions.iAnimationSpeed == undefined ? 'slow' : oOptions.iAnimationSpeed;
}

BxWallPost.prototype.changePostType = function(oElement) {    
    var $this = this;
    var sId = $(oElement).attr('id');
    var sType = sId.substr(sId.lastIndexOf('-') + 1, sId.length);
    
    var sSubType = '';
    if($(oElement).is('select'))
    	sSubType = $(oElement).val();

    var oLoading = $('#bx-wall-post-loading');
    if(oLoading)
    	oLoading.bx_loading();

    //--- Change Control ---//
    if($(oElement).is('a'))
    	$(oElement).parent().siblings('.active:visible').hide().siblings('.notActive:hidden').show().siblings('#' + sId + '-pas:visible').hide().siblings('#' + sId + '-act:hidden').show();

    //--- Change Content ---//
    var oContents = $(oElement).parents('.disignBoxFirst').find('.wall-ptype-cnt');
    if((sType == 'photo' || sType == 'sound' || sType == 'video')) {
        jQuery.post (
            $this._sActionsUrl + 'get_uploader/' + this._iOwnerId + '/' + sType + (sSubType && sSubType.length >0 ? '/' + sSubType : ''),
            {},
            function(sResult) {
            	if($.trim(sResult).length) {
            		oContents.filter(':visible').find('iframe[name=upload_file_frame]').remove();

            		var oContent = oContents.filter('.wall_' + sType);
            		if(oContent.is(':visible')) {
	            		oContent.bxwallanim('hide', this._sAnimationEffect, this._iAnimationSpeed, function() {
	            			$(this).html(sResult).addWebForms().bxwallanim('show', $this._sAnimationEffect, $this._iAnimationSpeed, function() {
	            				if(oLoading)
	            	            	oLoading.bx_loading();
	            			});
	            		});

	            		return;
            		}

            		oContent.html(sResult).addWebForms();
            		$this._animContent(oElement, sType);
            	}
            }
        );
    }
    else
        this._animContent(oElement, sType);
};

BxWallPost.prototype._animContent = function(oElement, sType) {
    var $this = this;
    var oLoading = $('#bx-wall-post-loading');

    $(oElement).parents('.disignBoxFirst').find('.wall-ptype-cnt:visible').bxwallanim('hide', this._sAnimationEffect, this._iAnimationSpeed, function() {
    	var sIdHide = $(this).attr('id');
    	var sTypeHide = sIdHide.substr(sIdHide.lastIndexOf('-') + 1, sIdHide.length);
    	if(sTypeHide == 'photo' || sTypeHide == 'sound' || sTypeHide == 'video')
    		$(this).html('');

        $(this).siblings('.wall-ptype-cnt').filter('.wall_' + sType).bxwallanim('show', $this._sAnimationEffect, $this._iAnimationSpeed, function() {
        	if(oLoading)
            	oLoading.bx_loading();
        });
    });
};

BxWallPost.prototype.postSubmit = function(oForm) {
	var oLoading = $('#bx-wall-post-loading');
	if(oLoading)
    	oLoading.bx_loading();

    return true;
};

BxWallPost.prototype._getPost = function(oElement, iPostId) {
    var $this = this;
    var oData = this._getDefaultData();
    oData['WallPostId'] = iPostId;

    // Hide Loading in Post block. 
    var oLoading = $('#bx-wall-post-loading');
    if(oLoading)
    	oLoading.bx_loading(false);

    // Show Loading in View block. 
    var oLoading = $('#bx-wall-view-loading');
    if(oLoading)
    	oLoading.bx_loading();

    jQuery.post (
        this._sActionsUrl + 'get_post/',
        oData,
        function(sResult) {
        	if(oLoading)
            	oLoading.bx_loading();

        	if($.trim(sResult).length) {
        		if(!$('.wall-view .wall-events div.wall-divider-today').is(':visible'))
                    $('.wall-view .wall-events div.wall-divider-today').show();

        		if(!$('.wall-view .wall-events div.wall-load-more').is(':visible'))
                    $('.wall-view .wall-events div.wall-load-more').show();

        		if($('.wall-view .wall-events .wall-empty').is(':visible'))
                    $('.wall-view .wall-events .wall-empty').hide();

                $('.wall-view .wall-events div.wall-divider-today').after($(sResult).hide()).next('.wall-event:hidden').bxwallanim('show', $this._sAnimationEffect, $this._iAnimationSpeed, function() {
                	$(this).find('a.bx-link').dolEmbedly();
                });
        	}
        }
    );
};

BxWallPost.prototype._getDefaultData = function () {
    return {WallOwnerId: this._iOwnerId};
};

BxWallPost.prototype._err = function (oElement, bShow, sMessage) {    
	if (bShow && !$(oElement).next('.wall-post-err').length)
        $(oElement).after(' <b class="wall-post-err">' + sMessage + '</b>');
    else if (!bShow && $(oElement).next('.wall-post-err').length)
        $(oElement).next('.wall-post-err').remove();    
};
