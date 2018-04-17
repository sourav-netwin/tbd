<section class="one-section">
    <div class="container">
        <h2 class="underline">Set Location</h2>
        <?php echo form_open('', array('id' => 'set_location_form', 'class' => 'form-horizontal custom_form mtop-40')); ?>
        <div class="form-group">
            <label for="" class="col-xs-2 control-label">Select Location</label>
            <div class="col-xs-9">
                <div id="location-div" style="width: 600px; height: 400px">

                </div>
            </div>
        </div>
        <div class="form-group">
            <label for="" class="col-xs-2 control-label">Address</label>
            <div class="col-xs-10">
                <input class="form-control" id="us_address" name="us_address" style="width: 600px;" />
            </div>
        </div>
        <div class="form-group">
            <label for="" class="col-xs-2 control-label">Latitude & Longitude</label>
            <div class="col-xs-10" style="padding-right: 0px">
                <div style="width: 297px; display: inline;float: left; margin-right: 3px;">
                    <input class="form-control" id="us_latitude" name="us_latitude" style="width: 297px;" />
                </div>
                <div style="width: 299px; display: inline">
                    <input class="form-control" id="us_longitude" name="us_longitude" style="width: 299px;" /> 
                </div>


            </div>
        </div>
        <input type="hidden" id="us_radius_hd" name="us_radius_hd" />
        <div class="form-group">
            <label for="" class="col-xs-2 control-label">Location Range</label>
            <div class="col-xs-10" style="max-width: 600px">
                <p class="mr-bt-45">Please select a distance. So that we can show you the price suggestions with in that range.</p>
                <div id="slider"></div>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-6">
                <input type="submit" class="btn btn-login" value="Submit" data-loading-text="Submitting...">
            </div>
        </div>
        </form>
    </div>
</section>