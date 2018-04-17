        <?php if (!empty($notifications)) { ?>
            <?php
            foreach ($notifications as $key => $notification) {
                if ($key % 2) {
                    $class = "grey_bg_row";
                } else {
                    $class = "white_bg_row";
                }
                ?>
                <div class="col-xs-12 <?php echo $class ?>">
                    <div class="col-xs-12"><?php echo $notification['Message'] ?></div>
                    <div class="col-xs-11"><a href="javascript:void(0)"><?php echo $notification['CreatedOn'] ?></a></div>
                    <div class="col-xs-1"><a href="javascript:void(0)" data-id="<?php echo $notification['Id'] ?>" class="delete_notification"><i class="fa fa-trash-o fa-2x"></i> </a></div>
                </div>

                <?php
            }
        }
        ?>