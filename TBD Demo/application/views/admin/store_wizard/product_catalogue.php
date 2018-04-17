<!-- /.row -->

<div class="row welcome_screen  ">
     <div class="col-md-12 welcome_header">
        <p> <button type="button" class="btn btn-primary btn-xs block full-width m-b">STEP 2 </button> Select Product Catalogue
            <br/>

            </p>
    </div>
    <div class="col-md-12">
        <p> Hello <b><?php echo $this->session->userdata('user_full_name'); ?></b>
            <br />
        </p>
    </div>
    <div class="col-md-12">
        <p>Next, we need to build your Store Product Catalogue. Don’t worry, you do not have to capture 20,000 items manually, the platform does this for you. How amazing is that. This process may take a few a minutes as we need to add ALL the products to your Store</p>
        <p>Please note that once the Products are added, you will need to go in and edit your standard pricing. This is important, as the default price of an item will always been shown when an item is not on promotion. Should you forget to do this, our Backoffice Assistant will email you weekly to ensure that your prices are updated.</p>
    </div>
    <div class="col-md-12">
<!--        <p>
            Would you like to inherit the <b><?php echo $retailer?></b> Catalogue or create a <b>New Catalogue</b>?
        </p>-->
        <p>Please note that once the Products are added, you will need to go in and edit your standard pricing. This is important, as the default price of an item will always been shown when an item is not on promotion. Should you forget to do this, our Backoffice Assistant will email you weekly to ensure that your prices are updated.</p>
    </div>
    <div class="col-md-12">
<!--        <p>
             <br/>
           <a href="<?php //echo site_url('/storeproducts/product_catalogue_inherit'); ?>" class="btn btn-primary btn-xs block full-width m-b">Yes</a>
           <a href="<?php //echo base_url() ?>storeproducts/add/new" class="btn btn-primary btn-xs block full-width m-b">Create New</a>
        </p>-->
        <p><a href="<?php echo base_url() ?>storeproducts/add_auto" class="btn btn-primary btn-xs block full-width m-b">Build Catalogue</a></p>
    </div>
</div>