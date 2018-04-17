<?php echo form_open_multipart('loyaltyorders/edit_post/' . $Id, array('id' => 'order_form', 'class' => 'form-horizontal','autocomplete'=>'off')); ?>
<div class="row">
    <div class="col-xs-10 col-xs-offset-1">
            <div class="form-group">
            <div class="row">
                <div class="col-md-12">                    
                        <table id="ordersdetails-table" class="table table-bordered table-hover table-striped dataTables">                            
                            <tbody>
                                <tr role="row">                                    
                                    <th>Order Number :</th>
                                    <td><?php echo "#".$OrderNumber ?> </td>
                                    <th>Order Date :</th>
                                    <td><?php echo $CreatedOn ?> </td>
                                </tr>                               
                                
                                <tr role="row">                                    
                                    <th>Customer Name  :</th>
                                    <td><?php echo $Name ?> </td>
                                    <th>Customer Email :</th>
                                    <td><?php echo $Email ?> </td>
                                </tr>
                                
                                <tr role="row">                                    
                                    <th>Customer Mobile  :</th>
                                    <td colspan="3"><?php echo $userMobile ?> </td>
                                </tr>
                                
                                <tr role="row">                                    
                                    <td colspan="4"><b>Billing Address : </b><br>  <?php echo $shippingAddress; ?> </td>                                    
                                </tr>
                            </tbody>
                        </table>                    
                </div>                
            </div>
        </div>
        
        
        <div class="form-group">
            <div class="row">
                <div class="col-md-12">
                    <label for="OrderNumber">Order Details :</label><br>
                        <table id="loyaltyordersdetails-table" class="table table-bordered table-hover table-striped dataTables">
                            <thead>
                                <tr>
                                    <th width="5%">ID</th>
                                    <th width="78%">Product Name</th>                                    
                                    <th width="17%">Loyalty Point</th>                                    
                                </tr>
                            </thead>
                            <tbody>
                                <?php $srNo = 1;
                                        foreach ($orderProducts as $orderProduct){ 
                                            
                                        $style = "";
                                        if ($orderProduct['ProductImage']) {
                                            $image_path = front_url() . LOYALTY_PRODUCT_IMAGE_PATH . "medium/" . $orderProduct['ProductImage'];
                                            $style = ($orderProduct['ProductImage'] != '' || file_exists('./' . LOYALTY_PRODUCT_IMAGE_PATH . "/medium/" . $orderProduct['ProductImage']) ) ? 'style="background-image: url(' . $image_path . '); background-size: 100px 100px; "' : '';
                                        }       
                                            
                                ?>
                                <tr class="odd" data-id="6" role="row">
                                    <td class="orderNumber"><?php echo $srNo; ?></td>
                                    <td>
                                        <a href="javascript:void(0)" data-href="<?php echo base_url()."loyaltyorders/showImage/".$orderProduct['LoyaltyProductId']; ?>" data-productname="<?php echo $orderProduct['BrandName']." - ".$orderProduct['LoyaltyTitle']; ?>" title="Show Image" class="showLoyaltyImage" style="color: #333;">                                           
                                             <?php echo $orderProduct['BrandName']." - ".$orderProduct['LoyaltyTitle']; ?>
                                        </a>                                        
                                    </td>                                   
                                    <td align="right"><?php echo $orderProduct['PointsUsed']; ?></td>                                                                
                                </tr>
                                <?php $srNo++; } //foreach ($orderProducts as $orderProduct) ?>
                                                                
                                <tr class="odd" data-id="6" role="row">                                    
                                    <td></td>
                                    <td align="right"><b>Total</b></td>
                                    <td align="right"><b><?php echo number_format($OrderTotal,0) ; ?></b></td>                                    
                                </tr>                                
                            </tbody>
                        </table>
                    
                </div>                
            </div>
        </div>
        
        
        <div class="form-group">
            <div class="row">
                <div class="col-md-6">
                    <label for="CategoryId" >Order Status <span>*</span></label>                   
                        <select class="form-control select-filter" id="OrderStatus" name="OrderStatus" onchange="showVoucherDetails(this.value)">
                            <option value="">Select Status</option>
                            <option value="0" <?php echo ( $OrderStatus == "Received" ) ? "selected" : ""; ?> >Received</option>
                            <option value="1" <?php echo ( $OrderStatus == "Cancelled" ) ? "selected" : ""; ?> >Cancelled</option>
                            <option value="2" <?php echo ( $OrderStatus == "Dispatched" ) ? "selected" : ""; ?> >Dispatched</option>
                        </select>
                    <div class="error" id="">
                        <?php echo form_error('OrderStatus'); ?>
                    </div>
                </div>
                
                <div class="col-md-6" id="voucherCodeArea">
                    <label for="voucherCode" >Voucher Code </label>                   
                    <input type="text" class="form-control" name="voucherCode" id="voucherCode" placeholder="Voucher Code" value="<?php echo $VoucherCode; ?>">
                    <div class="error">
                        <?php echo form_error('voucherCode'); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <div class="col-md-12">
                    <button type="button" id="submit_order_edit" class="btn btn-primary btn-xs block full-width m-b">Update</button>
                    <!--<a class="btn btn-danger block full-width m-b" href="<?php //echo site_url('/products');     ?>">Cancel</a>-->
                </div>
            </div>
        </div>
    </div>
    <!-- Add Modal -->    

</div>
</form>

<script>
    $(document).ready(function(){
        // Show product image 
        $('table#loyaltyordersdetails-table tbody').on('click','.showLoyaltyImage', function(e) {
            e.preventDefault();
            e.preventDefault();
            var elm = $(this);
            var parent = elm.parent().parent().parent();
            var url = elm.attr('data-href');
            var productName = elm.attr('data-productname');

            $.ajax({
                url: url,
                data: {},
                success: function(data){
                    createModal('show-product-image-modal', '<b>'+productName+'</b>', data,'wd-20');
                        $('#show-product-image-modal').find('.modal-dialog').css({
                                width:'350'
                        });
                }
            });	
	});
        
        // Hide/Show voucher Code Area        
        var orderStatus = $('#OrderStatus').val();        
        if(orderStatus == 2)
        {
            $('#voucherCodeArea').show();
        }else{
            $('#voucherCodeArea').hide();
        }
        
    });
    
    // Hide/Show voucher Code Area on condition 
    function showVoucherDetails(orderStatus)
    {
        if(orderStatus == 2)
            {
                $('#voucherCodeArea').show();
            }else{
                $('#voucherCodeArea').hide();
            }
    }
</script>