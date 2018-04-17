<!-- /.row -->

<div class="row welcome_screen  ">
     <div class="col-md-12 welcome_header">
        <p>The Following Categories will be added
            <br/>

            </p>
    </div>
    <div class="col-xs-10 col-xs-offset-1">
        <p>
            <input type="checkbox" value="" id="sel_all_cat_build" /> Select All Categories
        </p>
    </div>
    <div class="col-xs-10 col-xs-offset-1">
        <form id="auto_cat_form">
        <?php
        if($categories){
            $cnt = 0;
            foreach($categories as $category){
                if(($cnt == 0) || ($cnt % 4 == 0)){
                    echo '<div class="row">';
                }
                ?>
        <div class="col-xs-3">
            <input type="checkbox" class="store_main_cat" name="select_auto_category[]" value="<?php echo $category['Id'] ?>" /> <?php echo $category['CategoryName'] ?>
        </div>
        <?php
                if((($cnt != 0) || sizeof($categories) == ($cnt + 1)) && ((($cnt + 1) % 4) == 0 || sizeof($categories) == ($cnt + 1))){
                    echo '</div>';
                }
                $cnt++;
            }
        }
        ?>
        </form>
    </div>
    <div class="clearfix"></div>
    <br /><br />
    <div class="col-xs-10  col-xs-offset-1">
        <p><a href="javascript:void(0)" class="btn btn-primary btn-xs block full-width m-b" id="add_auto_category">Start Adding</a></p>
    </div>
</div>