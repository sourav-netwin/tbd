<!-- /.row -->
<div class="row search_filter_container">
    <div class="col-lg-12">
        <div class="col-xs-8 col-md-8 col-sm-8">
            <div class="form-group pull-left">
                <label for="category_search">SEARCH</label>
                <input type="text" class="form-control" name="category_search" id="category_search">
            </div>
            <select class="form-control select-filter search_product_select" id="parent_category_search" name="parent_category">
                <option value="">Select Category</option>
                <?php
                    if( !empty ($parent_category) ) :
                        foreach ($parent_category as $category)
                        {
                           echo "<option value='".$category['id']."'>".$category['name']."</option>";
                        }
                    endif;
                ?>
            </select>
        </div>
        <div class="col-xs-4 col-md-4 col-sm-4">
            <a class="btn btn-primary pull-right btn-xs" href="categories/add"> Add Category</a>
            <a class="btn btn-primary pull-right btn-xs" href="javascript:void(0);" id="expand_all"> Expand All</a>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div id="category_list">
            <ul class="nav menu">
                <?php
                    foreach ($category_arr as $key => $category)
                    {
                        foreach ($category as $c)
                        {
                            if( $key == 0 )
                            {
                ?>
                                <li class="item-<?php echo $c['id']; ?> deeper parent category main_category">
                                    <a class="" href="javascript:void(0);">
                                        <span data-toggle="collapse" href="#sub-item-<?php echo $c['id']; ?>" class="sign"><i class="fa fa-fw fa-plus-circle"></i></span>
                                        <span class="lbl"><?php echo $c['name'] ?></span>
                                        <span class="actions" data-id="<?php echo $c['id']; ?>">
                                            <i class="fa fa-fw fa-med fa-arrow-circle-up" title="Move Up"></i>
                                            <i class="fa fa-fw fa-med fa-arrow-circle-down" title="Move Down"></i>
                                            <i class="fa fa-fw fa-med fa-plus-circle add_to_category" title="Add to category"></i>
                                            <?php if ($c['active']) { ?>
                                                <i class="fa fa-fw fa-med fa-check active" data-status="0" title="Change status"></i>
                                            <?php } else { ?>
                                                <i class="fa fa-fw fa-med fa-close active" data-status="1" title="Change status"></i>
                                            <?php } ?>
                                            <i class="fa fa-fw fa-med fa-trash-o" title="Delete category"></i>
                                            <i class="fa fa-fw fa-med fa-edit" title="Edit category"></i>
                                        </span>
                                    </a>
                                    <?php
                                        if( !empty ( $category_arr[$c['id']] ) )
                                        {
                                    ?>
                                            <ul class="children nav-child unstyled small collapse" id="sub-item-<?php echo $c['id']; ?>">
                                    <?php
                                            foreach( $category_arr[$c['id']] as $subcategory )
                                            {
                                    ?>

                                                <li class="item-<?php echo $subcategory['id']; ?> deeper parent parent_category">
                                                    <a class="" href="javascript:void(0);">
                                                        <span data-toggle="collapse" href="#sub-item-<?php echo $subcategory['id']; ?>" class="sign"><i class="fa fa-fw fa-plus-circle"></i></span>
                                                        <span class="lbl"><?php echo $subcategory['name']; ?></span>
                                                        <span class="actions" data-id="<?php echo $subcategory['id']; ?>">
                                                            <i class="fa fa-fw fa-med fa-arrow-circle-up" title="Move Up"></i>
                                                            <i class="fa fa-fw fa-med fa-arrow-circle-down" title="Move Down"></i>
                                                            <i class="fa fa-fw fa-med fa-plus-circle add_to_category" title="Add to category"></i>
                                                            <?php if ($subcategory['active']) { ?>
                                                                <i class="fa fa-fw fa-med fa-check active" data-status="0" title="Change status"></i>
                                                            <?php } else { ?>
                                                                <i class="fa fa-fw fa-med fa-close active" data-status="1" title="Change status"></i>
                                                            <?php } ?>
                                                            <i class="fa fa-fw fa-med fa-trash-o" title="Delete category"></i>
                                                            <i class="fa fa-fw fa-med fa-edit" title="Edit category"></i>
                                                        </span>
                                                    </a>
                                                    <?php
                                                        if( !empty ( $category_arr[$subcategory['id']] ) )
                                                        {
                                                    ?>
                                                            <ul class="children nav-child unstyled small collapse" id="sub-item-<?php echo $subcategory['id']; ?>">
                                                    <?php
                                                            foreach( $category_arr[$subcategory['id']] as $inner_subcategory )
                                                            {
                                                    ?>
                                                                <li class="sub_category item-<?php echo $inner_subcategory['id']; ?>">
                                                                    <a class="" href="javascript:void(0);">
                                                                        <span class="sign" style="background-color: #00bfbf;"><i class="fa fa-fw fa-minus-circle"></i></span>
                                                                        <span class="lbl"><?php echo $inner_subcategory['name']; ?></span>
                                                                        <span class="actions" data-id="<?php echo $inner_subcategory['id']; ?>">
                                                                            <i class="fa fa-fw fa-med fa-arrow-circle-up" title="Move Up"></i>
                                                                            <i class="fa fa-fw fa-med fa-arrow-circle-down" title="Move Down"></i>
                                                                            <?php if ($inner_subcategory['active']) { ?>
                                                                                <i class="fa fa-fw fa-med fa-check active" data-status="0" title="Change status"></i>
                                                                            <?php } else { ?>
                                                                                <i class="fa fa-fw fa-med fa-close active" data-status="1" title="Change status"></i>
                                                                            <?php } ?>
                                                                            <i class="fa fa-fw fa-med fa-trash-o" title="Delete category"></i>
                                                                            <i class="fa fa-fw fa-med fa-edit" title="Edit category"></i>
                                                                        </span>
                                                                    </a>
                                                                </li>

                                                    <?php   }   ?>
                                                            </ul>
                                                <?php   }   ?>
                                                </li>

                                    <?php   }   ?>
                                            </ul>
                                     <?php
                                        }
                                    ?>
                                </li>
                <?php
                            }
                        }
                    }
                ?>
            </ul>
        </div>
    </div>
</div>

	<div class="modal fade" id="imageModal" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel"> <span id="form-action">Select Image 123</span></h4>
                </div>
                <div class="modal-body">

                        <div class="image-crop">
                            <img src="<?php echo $this->config->item('admin_assets'); ?>img/category.jpg">
                        </div>

                    <input type="hidden" name="aspect_ratio" id="aspect_ratio" value="any">    
                    <div><a id="crop-button" class="btn btn-primary btn-xs block full-width m-b">Select</a></div>
                    
                </div>
            </div>
        </div>
    </div>
	
	