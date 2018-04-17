<div class="row">
    <div class="col-lg-12">
        <div class="table-responsive">
            <table id="content-table" class="table table-bordered table-hover table-striped dataTables">
                <thead>
                    <tr>
                        <th>Menu Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($content as $content): ?>
                        <tr data-id="<?php echo $content['Id'] ?>">
                            <td><?php echo $content['MenuName'] ?></td>
                            <td>
                                <?php if ( $content['is_present'] != '' ) { ?>
                                            <a class="edit" href="javascript:void(0)" data-href="content/edit/<?php echo $content['Id'] ?>" title="Edit" class="edit"><i class="fa fa-fw fa-med fa-edit"></i></a>
                                <?php } else { ?>
                                    <a class="add" href="content/add/<?php echo $content['Id'] ?>" title="Add"><i class="fa fa-fw fa-med fa-plus"></i></a>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>