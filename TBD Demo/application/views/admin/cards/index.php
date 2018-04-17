<!-- /.row -->
<div class="row search_filter_container">
    <div class="col-md-12 text-right">
        <a class="btn btn-primary btn-xs" href="<?php echo base_url() ?>cards/add"> Add Cards</a>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="table-responsive">
            <table id="cards-table" class="table table-bordered table-hover table-striped dataTables">
                <thead>
                    <tr>
                        <th class="no-sort">Title</th>
                        <!--<th class="no-sort">Text</th>-->
                        <th class="no-sort" width="12%">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        if($cards){ 
                            $index=1;
                            foreach ($cards as $card) { ?>
                        <tr data-id="<?php echo $card['Id']; ?>">
                            <td>
                                <!--
                                <img src="<?php echo $this->config->item('front_url').CARDS_IMAGE_PATH.$card['CardImage'] ?>" width="120px;">
                                -->
                                <?php echo $card['CardTitle']; ?>
                            </td>
                            
                            <td style="vertical-align: middle">
                                <span class="actions">
                                    <?php 
                                        
                                        if( $card['IsActive'] == 1)
                                            $html = '<a class="active" data-status="0" href="javascript:void(0);" title="Change status"><i class="fa fa-fw fa-med fa-check"></i></a>';
                                        else
                                            $html = '<a class="active" data-status="1" href="javascript:void(0);" title="Change status"><i class="fa fa-fw fa-med fa-close"></i></a>';

                                        echo $html;
                                    ?>
                                    <a href="javascript:void(0)" class="edit" title="Edit" data-href="<?php echo base_url() ?>cards/edit/<?php echo $card['Id']; ?>">
                                        <i class="fa fa-fw fa-med fa-edit"></i>
                                    </a>
                                    <a href="javascript:void(0);" class="delete" title="Delete">
                                        <i class="fa fa-fw fa-med fa-trash-o"></i>
                                    </a>
                                </span>
                            </td>
                        </tr>
                    <?php $index++; } 
                        } else {   
                    ?>
                        <tr><td colspan="2">No Cards Available. </td></tr>    
                    <?php } ?>  
                        
                </tbody>
            </table>
        </div>
    </div>
</div>