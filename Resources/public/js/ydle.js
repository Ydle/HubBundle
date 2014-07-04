(function($) {
    var ydleTemplates = new Array();
    ydleTemplates["roomtype-list"] = twig(
    {
        cache: false, 
        id: "roomtype-list", // id is optional, but useful for referencing the template later
        href: "/bundles/ydlehub/templates/Widgets/widget.settings.roomtype.html.twig"  
    });
    ydleTemplates["nodetype-list"] = twig(
    {
        cache: false, 
        id: "nodetype-list", // id is optional, but useful for referencing the template later
        href: "/bundles/ydlehub/templates/Widgets/widget.settings.nodetype.html.twig"  
    });

    $(document).ready(function() {
      
        if($(".ajax-loading").length){
            $(".ajax-loading").each(function(){
                $element = $(this);
                if($element.hasClass('form-loading')){
                } else {
                    loadElement($element);
                }
            });
        }
            
        if($('form.ajax-form').length){
            $('form.ajax-form').each(function(){
                manageAjaxForm($(this));
            }); 
        } 
    });
    
    /**
     * Load an element with ajax after display
     */
    function loadElement($element, $target, $forcePagination)
    {
        if($target == '' || undefined === $target){
            $target = $element.attr('data-endpoint');
        }
        if(!$element.parent().find('.overlay').length){
            $element.parent().append('<div class="overlay"></div>');
            $element.parent().append('<div class="loading-img"></div>');
        }
        if($forcePagination){
            $currentPage = $element.find('.ajax-pagitation').attr('data-page');
            if($target.lastIndexOf("page=") != -1){
            } else {
                if($target.lastIndexOf("?") != -1){
                    $target = $target + "&page="+$currentPage;
                } else {
                    $target = $target + "?page="+$currentPage;
                }
            }
        }
        $.ajax({
            url: $target,
            success: function(data){
                        $targetTemplate = $element.attr('data-template');
                        if(ydleTemplates[$targetTemplate]){
                            // render the template
                            var tpl = twig({ ref: $targetTemplate }).render(data);
                        } else {
                            tpl = data
                        }
                        // Display the rendered template
                        $element.html(tpl);
                        $element.parent().children('.overlay').remove();
                        $element.parent().children('.loading-img').remove();
                        $pagination = $element.find('ul.ajax-pagitation');
                        $actions = $element.find('a.ajax-action');
                        $formAjax = $element.find('form.ajax-form');
                        if($actions.length){
                            manageAjaxActions($actions, $element);
                        }
                        if($pagination.length){
                            manageAjaxPagination($pagination, $element);
                        }
                        if($formAjax.length){
                            manageAjaxForm($formAjax);
                        } 
                   }
       }); 
    }
    
    /**
     * Manage ajax actions in listing
     **/
    function manageAjaxActions($elementAction, $element)
    {
        $elementAction.click(function(e){
            confirmation = true;
            if($(this).attr('data-confirm') == "yes"){
                confirmation = confirm($(this).attr('data-confirmmessage'));
            }
            if(confirmation){
                $target = $(this).attr('href');
                $form = $(this).attr('data-form');
                var editMode = false;
                $actionType = "PUT";
                if($(this).attr('data-action').length){
                    if($(this).attr('data-action') == "delete"){ $actionType = "DELETE" ; }
                    if($(this).attr('data-action') == "edit"){ $actionType = "GET" ; editMode = true}
                }
                if(!editMode){
                    $element.parent().append('<div class="overlay"></div>');
                    $element.parent().append('<div class="loading-img"></div>');
                } else {     
                    $formElement = $('#'+$form+">form");               
                    $formElement.parent().append('<div class="overlay"></div>');
                    $formElement.parent().append('<div class="loading-img"></div>');
                }
                
                if(editMode){
                    $formElement = $('#'+$form+">form");
                    $.ajax({
                        type: $actionType,
                        url: $target,
                        success: function(data){
                            data = $.parseHTML(data);
                            $formElement.parent().children('.overlay').remove();
                            $formElement.parent().children('.loading-img').remove();
                            $formElement.replaceWith(data);
                            if($formElement.hasClass('ajax-form')){
                                $formToUpdate = $('#'+$form);
                                manageAjaxForm($formToUpdate);
                            }
                        }
                    })
                } else {
                    $.ajax({
                        type: $actionType,
                        url: $target,
                        success: function(data){
                           loadElement($element, '', true);
                        }
                    })
                }
            }
            e.stopPropagation();
            return false;
        });
    }
    
    function manageAjaxForm($formElement)
    {
        $formElement.ajaxForm({success: manageFormResponse, beforeSubmit: function(arr, $form, options){
                addLoader($form.parent());
        } });
    }
    
    function addLoader($element)
    {
        $element.append('<div class="overlay"></div>');
        $element.append('<div class="loading-img"></div>');
    }
    function removeLoader($element)
    {
        $element.children('.overlay').remove();
        $element.children('.loading-img').remove();
    }
    
    /**
     * Manage ajax pagination for listing
     **/
    function manageAjaxPagination($pagination, $element)
    {
        $pagination.each(function(){
            $elementPagination = $(this);
            $target = $element.attr('data-endpoint');
            $links = $elementPagination.find('a');
            $links.click(function(e){
                $tmp = $(this);
                $targetArg = $tmp.attr('href');
                if($targetArg != ''){
                    $newTarget = $target+$targetArg;
                    loadElement($element, $newTarget);
                }
                e.stopPropagation();
                return false;
            });
        });
    }
    
    
    /**
     * Manage action todo after any ajax form submissions
     */
    function manageFormResponse(responseText, statusText, xhr, $form){
        if(statusText == "success"){
            if($form.attr('data-reload')){
                $reloadElement = $('#'+$form.attr('data-reload'));
                loadElement($reloadElement);
            }
            if($form.attr('data-refresh')){
                $form.resetForm();
                $form.clearForm();
            }
            
            removeLoader($form.parent());
        }
    };
})(jQuery);
