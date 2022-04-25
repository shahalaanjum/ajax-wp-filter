<?php
$link = get_the_permalink();
$img = get_field('featured_image');
$title = get_the_title();
    echo'
    <div class="item web col-sm-6 col-md-4 col-lg-4 mb-4">
        <a href="'.$link.'" class="item-wrap fancybox"> 
            
            <div class="work-info">
                <h3>'.$title.'</h3>
                <span>Meta</span>
            </div>
            <img src="'.$img.'"  width="400" height="auto">
            
        </a>
    </div>';
    ?>