//When the page has loaded.
const taxname = $('.taxonomy-list input').map(function() {return this.id;}).get();



$(document).ready(function(){
    $.ajax({ url: "/",
        context: document.body,
        data: {
            action: 'loadall',
            taxname,
            // sortOrder,
            //pageNumber,
        },
        success: function(data){
        //    alert("done");
        }
    });
});
    
    //filter code for blogs
    var pageNumber = 1;
   
    jQuery('.filter-link').on('click', function(e) {
        e.preventDefault();
        var pageNumber = 1;
        jQuery(this).parent().parent().find('a').removeClass('activeFilter');
        jQuery(this).addClass('activeFilter');
        jQuery('#more_blog_posts').removeClass('activeLoadmore');
        editFilterInputs(jQuery('#filters-' + jQuery(this).data('type')), jQuery(this).data('id'));
        filterBlogs();

    });

    function editFilterInputs(inputField, value) {
        const currentFilters = inputField.val().split(',');
        const newFilter = value.toString();

        if (currentFilters.includes(newFilter)) {
            const i = currentFilters.indexOf(newFilter);
            currentFilters.splice(i, 1);
            inputField.val(currentFilters);
        } else {
            inputField.val(inputField.val() + ',' + newFilter);
        }
    }
    


    function filterBlogs() {
        
        if(jQuery('#more_blog_posts').hasClass('activeLoadmore')){
            pageNumber++;
        }
		else {
            pageNumber = 1;
        }
        var catIds = jQuery('.cat-list a.activeFilter').attr('data-id');
        
        var postype = jQuery('.projects-grid').attr('post-type');
        const taxType = jQuery('.cat-list a.activeFilter').attr('data-type');;
        //const catIds = jQuery('#filters-' + jQuery(this).data('type')).val().split(',');
       // const taxname = $('.taxonomy-list input').map(function() {return this.id;}).get();

        // var tagIds = jQuery('.tag-list li a.activeFilter').attr('data-id');
        // const tagIds = jQuery('#filters-tag').val().split(',');
        
        jQuery.ajax({
            type: 'POST',
            url: myAjax.ajaxurl,
            dataType: 'json',
            data: {
                action: 'acpt_filter_blogs',
                catIds,
                postype,
                taxType,
                taxname,
                // sortOrder,
                //pageNumber,
            },
            success: function(result) {
               
            if(jQuery('#more_blog_posts').hasClass('activeLoadmore')){
                jQuery(".projects-grid").append(result.html);
            } else{
                jQuery('.projects-grid').html(result.html);
            }
            if(result.total == pageNumber){
                jQuery("#more_blog_posts").hide();
            } else{
                jQuery("#more_blog_posts").show();
            }
            //jQuery('#result-count').html(res.total);
            },
            error: function(err) {
                console.log(err);
            }
        })
    }


    //load more 
    jQuery("#more_blog_posts").on("click",function(){ // When btn is pressed.
    $("#more_blog_posts").attr("disabled",true); // Disable the button, temp.
    jQuery(this).addClass('activeLoadmore');
    filterBlogs();
    jQuery(this).insertAfter('.projects-grid');
    });

    



    
 

