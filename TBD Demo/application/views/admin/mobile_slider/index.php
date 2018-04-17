<!-- /.row -->
<div class="row search_filter_container">
    <div class="col-md-12 text-right">
        <a class="btn btn-primary btn-xs" href="<?php echo base_url() ?>mobileslider/add"> Add Slider</a>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="table-responsive">
            <table id="slider-table" class="table table-bordered table-hover table-striped dataTables">
                <thead>
                    <tr>
                        <th class="no-sort">Slider</th>
                        <!--<th class="no-sort">Text</th>-->
                        <th class="no-sort" width="12%">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $index=1;
                            foreach ($sliders as $slider) { ?>
                        <tr data-id="<?php echo $slider['Id']; ?>">
                            <td>
                                <!--
                                <img src="<?php echo $this->config->item('front_url').MOBILE_SLIDER_IMAGE_PATH.$slider['Image'] ?>" width="120px;">
                                -->
                                Slider <?php echo $index; ?>
                            </td>
                            <!--
                            <td style="color:<?php echo $slider['Color'] ?>; vertical-align: middle; text-align: center"><span style="padding: 10px; background-color: <?php echo $slider['BgColor'] ?>"><?php echo $slider['Text'] ?></span></td>
                            -->
                            <td style="vertical-align: middle">
                                <span class="actions">
                                    <?php 
                                        
                                        if( $slider['IsActive'] == 1)
                                            $html = '<a class="active" data-status="0" href="#" title="Change status"><i class="fa fa-fw fa-med fa-check"></i></a>';
                                        else
                                            $html = '<a class="active" data-status="1" href="#" title="Change status"><i class="fa fa-fw fa-med fa-close"></i></a>';

                                        echo $html;
                                    ?>
                                    <a href="javascript:void(0)" class="edit" title="Edit" data-href="<?php echo base_url() ?>mobileslider/edit/<?php echo $slider['Id']; ?>">
                                        <i class="fa fa-fw fa-med fa-edit"></i>
                                    </a>
                                    <a href="#" class="delete" title="Delete">
                                        <i class="fa fa-fw fa-med fa-trash-o"></i>
                                    </a>
                                </span>
                            </td>
                        </tr>
                    <?php $index++; } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>