<section class="content-header">
    <?php
    $LogoImage = $this->session->userdata('user_logo');
     if($LogoImage != '' ) {
    ?>
    <div class="header_logo" style="display: inline-block"><img style="max-height: 30px" src="<?php echo $LogoImage?>" border="0" /></div>
    <?php } ?>
    <h1 style="display: inline-block"> <?php echo $title ?>  </h1>
    <?php if (!empty($breadcrumbs[0])) { ?>
        <ol class="breadcrumb">
            <li>
                <i class="fa fa-dashboard"></i>  <a href="<?php echo base_url(); ?>">Dashboard</a>
            </li>

            <?php foreach ($breadcrumbs as $index => $breadcrumb) { ?>
                <li class="<?php echo ($index === count($breadcrumbs) - 1) ? 'active' : '' ?>">
                    <?php if ($index === count($breadcrumbs) - 1) { ?>
                        <strong><?php echo $breadcrumb['label'] ?></strong>
                    <?php } else { ?>
                        <a href="<?php echo $breadcrumb['url'] == '' ? 'javascript:void(0)' : site_url($breadcrumb['url']); ?>"><?php echo $breadcrumb['label'] ?></a>
                    <?php } ?>
                </li>
            <?php } ?>
        </ol>
    <?php } ?>
</section>