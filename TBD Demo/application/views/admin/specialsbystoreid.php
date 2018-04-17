    <div class="container">     <div class="row">
         
            <div id="store_details" class="col-md-10">
               
                <div class="table-report">
				<?php if(isset($result_specials) &&  !empty($result_specials)){
					?>
					<table id="specialListing" class="table table-bordered dataTables dataTable no-footer">
                        <thead>
                            <tr role="row">
                                <th width="50%">Special</th>
                                <th>From </th>
								<th>To </th>
								<th>Count</th>
								
                            </tr>
                        </thead>
                        <tbody>
						<?php  echo $result_specials; ?>
                        </tbody>
                    </table>
					<?php 
				} else echo' No Data Available'; ?>
                    
                </div>
            </div>

        </div></div>

  