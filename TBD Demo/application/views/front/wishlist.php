<div class="col-xs-12 grey_bg_header">
    <div class="col-xs-6">Wishlist</div>
    <div class="col-xs-3">Date</div>
    <div class="col-xs-3">&nbsp;</div>
</div>

<?php if (!empty($wishlists)) { ?>
    <?php
    foreach ($wishlists as $key => $wishlist) {
        if ($key % 2) {
            $class = "grey_bg_row";
        } else {
            $class = "white_bg_row";
        }
        ?>

        <div class="col-xs-12 <?php echo $class ?>">
            <div class="col-xs-6"><?php echo $wishlist['WishlistDescription'] ?> (<?php echo $wishlist['products_count'] ?>)</div>
            <div class="col-xs-4"><?php echo $wishlist['CreatedOn'] ?></div>
            <div class="col-xs-2">
                <a href="javascript:void(0)" data-id="<?php echo $wishlist['Id'] ?>" title="View" class="wishlist_detail"> <i class="fa fa-bars fa-2x"></i></a>
                <a href="javascript:void(0);" class="remove_wishlist" data-id="<?php echo $wishlist['Id'] ?>"><i class="fa fa-trash-o fa-2x"></i></a>
            </div>

        </div>

        <?php
    }
}
?>
