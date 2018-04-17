<nav class="navbar navbar-inverse yamm">
    <div class="container">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <?php if (!empty($all_categories)) { ?>
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    <?php
                    foreach ($all_categories as $key => $value) {
                        $cat_count = 0;
                        ?>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?php echo $key ?>
                                <!--<span class="caret"></span>-->
                            </a>
                            <ul class="dropdown-menu display_products">
                                <div class="yamm-content">
                                    <div class="row">



                                        <?php
                                        $close = 0;
                                        $cat_count = 0;
                                        foreach ($value as $k => $v) {
                                            $parent_category_details = explode("::", $k);


                                            if (($cat_count + count($v)) > 2 || $cat_count == 0) {
                                                if ($cat_count) {
                                                    echo "</ul>";
                                                }


                                                $cat_count = 0;
                                                ?>
                                                <ul class="col-sm-6 list-unstyled">
                                                <?php } ?>
                                                <li class="sub_category_header">
                                                    <?php $cat_count++; ?>
                                                    <p><strong><a href="<?php echo front_url() . 'productslist/index/parent/' . $this -> encrypt -> encode($parent_category_details[0]) ?>"><?php echo $parent_category_details[1] ?></a></strong></p>
                                                </li>
                                                <?php
                                                foreach ($v as $key1 => $val1) {
                                                    $cat_count++;
                                                    ?>
                                                    <li><a href="<?php echo front_url() . 'productslist/index/sub/' . $this -> encrypt -> encode($key1) ?>"><?php echo $val1; ?></a></li>
                                                <?php } ?>
                                                <?php
                                                if ($close == 1) {
                                                    $close = 0;
                                                    ?>

                                                <?php } ?>
                                            <?php } ?>
                                    </div>
                                </div>
                            </ul>
                        </li>
                    <?php } ?>
                </ul>
            </div><!-- /.navbar-collapse -->
        <?php } ?>
    </div><!-- /.container-fluid -->
</nav>
<section class="breadcrumb-front">
    <ol>
        <li><a href="<?php echo front_url() ?>">Home</a></li>
        <?php
        if (isset($breadcrumbs)) {
            $cnt = 1;
            foreach ($breadcrumbs as $breadcrumb) {
                if ($breadcrumb['name']) {
                    ?>
                    <li <?php echo ($cnt == sizeof($breadcrumbs)) ? 'class="active"' : '' ?>><a href="<?php echo $breadcrumb['url'] == '' ? 'javascript:void(0)' : $breadcrumb['url'] ?>"><?php echo $breadcrumb['name'] ?></a></li>
                    <?php
                    $cnt++;
                }
            }
        }
        ?>
    </ol> 
</section>