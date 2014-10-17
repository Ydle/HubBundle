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
    ydleTemplates["logs-list"] = twig(
    {
        cache: false, 
        id: "logs-list", // id is optional, but useful for referencing the template later
        href: "/bundles/ydlehub/templates/Widgets/widget.logs.html.twig"  
    });
    ydleTemplates["nodes-list"] = twig(
    {
        cache: false, 
        id: "nodes-list", // id is optional, but useful for referencing the template later
        href: "/bundles/ydlehub/templates/Widgets/widget.nodes.html.twig"  
    });
    ydleTemplates["rooms-list"] = twig(
    {
        cache: false,
        id: "rooms-list", // id is optional, but useful for referencing the template later
        href: "/bundles/ydlehub/templates/Widgets/widget.rooms.html.twig"
    });
    ydleTemplates["room-nodes-list"] = twig(
    {
        cache: false, 
        id: "room-nodes-list", // id is optional, but useful for referencing the template later
        href: "/bundles/ydlehub/templates/Widgets/widget.room-nodes.html.twig"  
    });

    $(document).ready(function() {
        
        $("<div id='tooltip'></div>").css({
                position: "absolute",
                display: "none",
                border: "1px solid #fdd",
                padding: "2px",
                "background-color": "#fee",
                opacity: 0.80
        }).appendTo("body");

      
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
        
        if($('a.ajax-button').length){
            $('a.ajax-button').each(function(){
                manageAjaxButton($(this));
            }); 
        }

        if($('.ajax-graph').length){
            $('.ajax-graph').each(function(){
                manageAjaxGraph($(this));
            });
        } 
    });
    
    function manageAjaxGraph($graph)
    {
        if(!$graph.parent().find('.overlay').length){
            $graph.parent().append('<div class="overlay"></div>');
            $graph.parent().append('<div class="loading-img"></div>');
        }
        $graphUrl = $graph.attr('data-endpoint');
        $graphFilter = $graph.attr('data-filter');
        $chartsPlaceholder = $('.graph-placeholder', $graph);
        $graph.parent().find('.graph-action').each(function(){
            $(this).click(function(){
                $(this).parent().find('.graph-action.disabled').removeClass('disabled');
                $(this).addClass('disabled');
                $graph.parent().append('<div class="overlay"></div>');
                $graph.parent().append('<div class="loading-img"></div>');
                newFilterVal = $(this).attr('data-value') ;
                $graph.attr('data-filter', newFilterVal);
                loadGraph($graphUrl, $chartsPlaceholder, newFilterVal);
            });
        });
        loadGraph($graphUrl, $chartsPlaceholder, $graphFilter);
  	
    }
    
    function loadGraph($graphUrl, $chartsPlaceholder, $graphFilter)
    {
        $graphUrl += "&filter="+$graphFilter;
        var options = {
            lines: {
                show: true
            },
            points: {
                show: false
            },
            xaxis: {
                mode: "time",
                timeformat: "%d/%m/%Y"
            },
            yaxis: [{position:'left', alignTicksWithAxis: 1}, {min: 0, position:'left', alignTicksWithAxis: 1}]
        };
        var data = [];
        $.ajax({
            type: 'GET',
            url: $graphUrl,
            success: function(series) {
		//data.push({label:'test', data: [[1412288200000, 1698],[1412289200000,1728]]});
		for (serie in series) {
			data.push(series[serie]);
		}
		$.plot($chartsPlaceholder, data, options);
                $chartsPlaceholder.bind("plothover", function (event, pos, item) {
                    if (item) {
                        var x = item.datapoint[0].toFixed(2),
                                y = item.datapoint[1].toFixed(2);
                        tmpDate = new Date();
                        tmpDate.setTime(x);
                        $("#tooltip").html(tmpDate.getDate()+'/'+(tmpDate.getMonth()+1)+'/'+tmpDate.getFullYear()+' : '+y)
                                .css({top: item.pageY+5, left: item.pageX+5})
                                .fadeIn(200);
                    } else {
                        $("#tooltip").hide();
                    }
		});
                removeLoader($chartsPlaceholder.parent().parent());
            }
        });  
    }
    
    function manageAjaxButton($button)
    {
        $button.click(function(){
            $action = $button.attr('data-action');
            $reloadElement = $("#"+$button.attr('data-reload'));
            $reloadUrl = $button.attr('data-endpoint');
            $url = $button.attr('href');
            
            $.ajax({
                type: $action,
                url: $url,
                success: function(data){
                    loadElement($reloadElement, $reloadUrl, true);
                }
            })
            return false;
        })
    }
    
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
        
    function cleanFieldName(fieldName, formName)
    {
        fieldName = fieldName.replace(formName, '');
        fieldName = fieldName.replace('[','');
        fieldName = fieldName.replace(']','');
        return fieldName;
    }
    
    function manageAjaxForm($formElement)
    {
        if($formElement.hasClass('filter-form')){ 
            $formElement.submit(function(e){
                $url = $formElement.attr('data-source');
                $formElems = $formElement.serializeArray();
                $url += '?';//+$formElement.serialize();
                for(tmpElmt in $formElems){
                    cleanName = cleanFieldName($formElems[tmpElmt].name, $formElement.attr('name'));
                    $url += cleanName+'='+$formElems[tmpElmt].value+'&';
                }
                $reloadElement = $('#'+$formElement.attr('data-reload'));
                loadElement($reloadElement, $url, false);
                e.stopPropagation();
                return false;
            });
        } else {
            $formElement.ajaxForm({
                //target: $formElement.parent(),
		//replaceTarget: true,
                success: manageFormResponse, 
                error: manageFormResponse, 
                beforeSubmit: function(arr, $form, options){
                    addLoader($form.parent());
            } });
        }
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
            loadElement($form.parent());
        }
        if(statusText == "error"){
	    $tmpFormId = $form.attr('id');
	    $form.replaceWith(responseText.responseText);
	    $form = $('#'+$tmpFormId);
            console.log($form);
            console.log($form.parent());
            removeLoader($form.parent());
            manageAjaxForm($form);
        }
    };
})(jQuery);
