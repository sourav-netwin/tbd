<input type="hidden" id="baseUrl" value="<?php echo base_url(); ?>">

		<div id="tabss" class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <li class="active"><a href="#general" data-toggle="tab">General</a></li>
              <li><a href="#productstab" data-toggle="tab">Products</a></li>
              <li class="userstabli"><a class="userstaba" href="#userstab" data-toggle="tab">Users</a></li>
              
            </ul>
            <div class="tab-content">
              <div class="tab-pane active" id="general">
					  
					<div class="col-md-6 col-sm-6 col-xs-12 diffblock">
						<div class="col-md-6 col-sm-6 col-xs-12 diffblock">
							<div class="info-box ">
								<div class="info-box-content"> <a href="javascript:void(0);" class="retailersCount blockanchorclass"> 
								<span class="info-box-text">Retailers</span> 
								<span class="info-box-number"><?php echo $retailers['retailer_count']; ?></span> 
								<span class="info-box-text subtext">Expand</span> </a>
								</div>
								<!-- /.info-box-content -->
								
							</div>
						<!-- /.info-box -->
						</div>
						<div class="col-md-6 col-sm-6 col-xs-12 diffblock">
							<div class="info-box ">
								<div class="info-box-content"> <a href="javascript:void(0);" class="usersCount blockanchorclass"> 
								<span class="info-box-text">Total Users</span> 
								<span class="info-box-number"><?php echo $users['users']; ?></span> 
								<span class="info-box-text subtext">Expand</span> </a>
								</div>
								<!-- /.info-box-content -->
								
							</div>
						<!-- /.info-box -->
						</div>
						<div class="clearfix"></div>
						<div class="col-md-12 col-sm-12 col-xs-12 diffblock signupusersmap"  style="">
						<div class="info-box ">
								<div class="info-box-content"><span class="info-box-text">Registered Users</span> 
							<div id="chartContainer" style="height: 73%; width: 100%;"></div>
						</div></div></div>
						</div>

					<div class="col-md-6 col-sm-6 col-xs-12 diffblock proviencelisting">
						
								<div class="demo-wrapper allprovience">
									<div id="stores_state_report" class="reports stores_state_report_table input-box-content">
									<?php if ($state_stores_count) : 
									$arr  = $state_stores_count;
									$sort = array();
									foreach($arr as $k=>$v) {
										$sort['Store_Count'][$v['Name'].'&&&&&'.$v['Id']] = $v['Store_Count'];
									}

									array_multisort($sort['Store_Count'], SORT_DESC, $arr);
									$state_stores_counts=$sort['Store_Count'];
									
									$html=''; ?>
									<?php foreach ($state_stores_counts as $k=>$v) : 
										$parts=explode('&&&&&',$k);
										$state_store_count['Name']=$parts[0];
										$state_store_count['Id']=$parts[1];
										$state_store_count['Store_Count']=$v;
										$devideBy=5;
										$Store_Count=$state_store_count['Store_Count'];
								
										$divWidthPercent=($Store_Count/$devideBy)+10;
										$html.='<a style="width:23% !important;" data-href="'.base_url().'home/getStoresListingByProvience/'.$state_store_count['Id'].'" href="javascript:void(0)" class="storeFormat provienceName" name="state_count" data-code="'.$state_store_count['Name'].'" data-name="'.$state_store_count['Name'].'">
										<b class="">'.$state_store_count['Name'].' </b></a>
										<div class="storecountwithbar" style="width:77% !important;">
										<span class="storecount">'.$state_store_count['Store_Count'].'</span>
											<div class="progress" style="width:'.$divWidthPercent.'%; max-width:90% !important;min-width:10% !important">
										  
											<div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100" style="width:100%">
										
										</div> </div></div>';
															endforeach; 
												echo $html;
															?>
									<?php endif; ?>
									</div></div>
					</div> 
					<div class="clearfix"></div>
					<div class="col-md-3 col-sm-6 col-xs-12 diffblock genralTabSecondRow">
							<div class="info-box ">
								<div class="info-box-content"> <a href="javascript:void(0);" class="activeStores blockanchorclass"> 
								<span class="info-box-text">Active Stores</span> 
								<span class="info-box-number"><?php echo count($activeStores); ?></span> 
								<span class="info-box-text subtext">Expand</span> </a>
								</div>
								<!-- /.info-box-content -->
								
							</div>
						<!-- /.info-box -->
						</div>
					<div class="col-md-3 col-sm-6 col-xs-12 diffblock genralTabSecondRow">
							<div class="info-box ">
								<div class="info-box-content"> <a href="javascript:void(0);" class="livePromos blockanchorclass"> 
								<span class="info-box-text">Live Promos</span> 
								<span class="info-box-number"><?php echo $liveSpecials['count']; ?></span> 
								<span class="info-box-text subtext">Expand</span> </a>
								</div>
								<!-- /.info-box-content -->
								
							</div>
						<!-- /.info-box -->
						</div>
					<div class="col-md-3 col-sm-6 col-xs-12 diffblock genralTabSecondRow">
							<div class="info-box ">
								<div class="info-box-content"> <a href="javascript:void(0);" class="itemsonspecial blockanchorclass"> 
								<span class="info-box-text">Items on Special</span> 
								<span class="info-box-number"><?php echo  $special_product['special_product_count']; ?></span> 
								<span class="info-box-text subtext">Expand</span> </a>
								</div>
								<!-- /.info-box-content -->
								
							</div>
						<!-- /.info-box -->
						</div>
						
					<div class="col-md-3 col-sm-6 col-xs-12 diffblock genralTabSecondRow">
							<div class="info-box ">
								<div class="info-box-content"> <a href="javascript:void(0);" class="newUsers blockanchorclass"> 
								<span class="info-box-text">New Users</span> 
								<span class="info-box-number"><?php echo count($newUsers); ?></span> 
								<span class="info-box-text subtext">Expand</span> </a>
								</div>
								<!-- /.info-box-content -->
								
							</div>
						<!-- /.info-box -->
						</div>
				</div>
              <!-- /.tab-pane -->
              <div class="tab-pane" id="productstab">
					<!-- /.col -->
					<!-- fix for small devices only -->
					<div class="col-md-3 col-sm-6 col-xs-12 diffblock storeproductsblock">
						<div class="info-box "> 
						<a class="productCatalogue blockanchorclass" href="javascript:void(0);">
							<div class="info-box-content">
								<span class="info-box-text">Store Products</span> 
								<span class="info-box-number" id="store_products_count"></span>
								<span class="info-box-text subtext">Expand</span>			
							</div>
							<!-- /.info-box-content -->
							</a>
						</div>
						<!-- /.info-box -->
						
					</div>
					<!-- /.col -->
					<div class="col-md-3 col-sm-6 col-xs-12 diffblock specialproductcountblock">
						<div class="info-box">
						
							<div class="info-box-content"><a href="javascript:void(0);" class="specialproductcount blockanchorclass">
							<span class="info-box-text">Products on Special</span>							
							<div id="specialproducts"  data-text="<?php echo $special_product['special_product_count']; ?>" data-percent="<?php echo $special_product['special_product_count']; ?>"></div>
							<span class="info-box-text subtext">Expand</span>
							</a>  </div> 
							<!-- /.info-box-content -->
						</div>
						<!-- /.info-box -->
					</div>  
					
					<div class="col-md-3 col-sm-6 col-xs-12 diffblock specialbrowseboxx">
						<div class="info-box">
						
							<div class="info-box-content ">
							<a href="javascript:void(0);" class="specialbrowsebox blockanchorclass">
								<span class="info-box-text">Promo Checkedin</span> 
								<span class="info-box-number" id="specialbrowse"></span>
								<span class="info-box-text subtext">Expand</span>			
							</div>
						</a>
							<!-- /.info-box-content -->
						</div>
						<!-- /.info-box -->
					</div> 
					<div class="col-md-3 col-sm-6 col-xs-12 diffblock specialbrowseboxx">
						<div class="info-box">
						
							<div class="info-box-content ">
							<a href="javascript:void(0);" class="mostvisitedproducts blockanchorclass">
								<span class="info-box-text">Most Visited</span> 
								<span class="info-box-number"><?php echo $mostvisited; ?></span> 
								<span class="info-box-text subtext">Expand</span>			
							</div>
						</a>
							<!-- /.info-box-content -->
						</div>
						<!-- /.info-box -->
					</div> 
					<!-- /.col -->
					<div class="trendingproductsdiv">
					<div class="col-md-12 trendingproductslabel"><label>TOP 20 TRENDING PRODUCTS</label></div>
					<div class="col-md-3 col-sm-6 col-xs-12 toptrendingproductsofspecialblock trendingproductblocktab">
						<div class="info-box"><div class="info-box-content">
						<a href="javascript:void(0);" class="toptrendingproductsofspecial"> <span class="info-box-icon"><i class="fa fa-fw fa-bars"></i></span>
							 <span class="info-box-text">Products on Special</span></a>  
							 </div>
							<!-- /.info-box-content -->
						</div>
						<!-- /.info-box -->
					</div>
					<!-- /.col -->
					
					<!-- /.col -->
					<div class="col-md-3 col-sm-6 col-xs-12 toptrendingproductsofviewsblock trendingproductblocktab">
						<div class="info-box">
						<div class="info-box-content">
						<a href="javascript:void(0);" class="toptrendingproductsofviews"> <span class="info-box-icon "><i class="fa fa-fw fa-bars"></i></span>
							<span class="info-box-text">Products By Views </span></a>  </div>
							<!-- /.info-box-content -->
						</div>
						<!-- /.info-box -->
					</div>
					<!-- /.col -->
					
					<!-- /.col -->
					<div class="col-md-3 col-sm-6 col-xs-12 toptrendingproductsofreviewblock trendingproductblocktab">
						<div class="info-box">
						<div class="info-box-content"> 
						<a href="javascript:void(0);" class="toptrendingproductsofreview"> <span class="info-box-icon "><i class="fa fa-fw fa-bars"></i></span>
							<span class="info-box-text">Products By Review </span></a>  </div>
							<!-- /.info-box-content -->
						</div>
						<!-- /.info-box -->
					</div>
					<!-- /.col -->
					
					<!-- /.col -->
					<div class="col-md-3 col-sm-6 col-xs-12 toptrendingproductsofshareblock trendingproductblocktab">
					<div class="info-box">
						<div class="info-box-content">
							<a href="javascript:void(0);" class="toptrendingproductsofshare"> <span class="info-box-icon "><i class="fa fa-fw fa-bars"></i></span>
								<span class="info-box-text">Products By Share </span></a>  </div>
							<!-- /.info-box-content -->
						</div>
						<!-- /.info-box -->
					</div>
					<!-- /.col -->
					</div>	
								
				</div>
              <!-- /.tab-pane -->   

		 <!-- /.tab-pane -->
			  <div class="tab-pane" id="userstab">
					
					<div class="col-md-3 col-sm-6 col-xs-12 diffblock usersbygeodiv">
						<div class="info-box"><div class="info-box-content ">
						<a href="javascript:void(0);" class="usersbygeo blockanchorclass"> 							
							<span class="info-box-text subtext">Users by Geo Location</span>
							<!-- <span class="info-box-number"><?php echo $checkedinusers['checkedinusers']; ?></span> 
							<span class="info-box-text subtext">Expand</span> -->
							</a>  </div>
							<!-- /.info-box-content -->
						</div>
						<!-- /.info-box -->
					</div>
					<!-- /.col -->

					<div class="col-md-3 col-sm-6 col-xs-12 diffblock signedusersdiv">
						<div class="info-box"><div class="info-box-content"><a href="javascript:void(0);" class="signedusers blockanchorclass"> 

							<span class="info-box-text subtext">Registered Users</span>

							<!-- <span class="info-box-number"><?php echo $signedusers['signedusers']; ?></span> 
							<span class="info-box-text subtext">Expand</span> -->
							</a>  </div>
							<!-- /.info-box-content -->
						</div>
						<!-- /.info-box -->
					</div>
					<!-- /.col -->   
					
					<div class="col-md-3 col-sm-6 col-xs-12 diffblock usersbygender toptrendingproductsofreviewblock">
						<div class="info-box"><div class="info-box-content"><a href="javascript:void(0);" class="usersbygender blockanchorclass"> 

							<span class="info-box-text subtext">Users By Gender</span>

							<!-- <span class="info-box-number"><?php echo $users['usersbygender']; ?></span> 
							<span class="info-box-text subtext">Expand</span> -->
							</a>  </div>
							<!-- /.info-box-content -->
						</div>
						<!-- /.info-box -->
					</div>
					<!-- /.col -->
					
					<div class="col-md-3 col-sm-6 col-xs-12 diffblock usersbyage toptrendingproductsofshareblock ">
						<div class="info-box"><div class="info-box-content"><a href="javascript:void(0);" class="usersbyage blockanchorclass"> 
							<span class="info-box-text subtext">Users By Age</span>
							</a>  </div>
							<!-- /.info-box-content -->
						</div>
						<!-- /.info-box -->
					</div>
					<!-- /.col -->
				
<!-- /.col -->
					<div class="trendingproductsdiv">
					<div class="col-md-12 trendingproductslabel"><label>Usage Activity</label></div>
					<div class="col-md-3 col-sm-6 col-xs-12 trendingproductblocktab">
						<div class="info-box">
						<div class="info-box-content">
						<a href="javascript:void(0);" class="timeofday"> <span class="info-box-icon "><i class="fa fa-fw fa-bars"></i></span>
							 <span class="info-box-text">Time of Day</span></a>  </div>
							<!-- /.info-box-content -->
						</div>
						<!-- /.info-box -->
					</div>
					<!-- /.col -->
					
					<!-- /.col -->
				<!--	<div class="col-md-3 col-sm-6 col-xs-12  trendingproductblocktab">
						<div class="info-box">
						<div class="info-box-content">
						<a href="javascript:void(0);" class="bylastweek"> <span class="info-box-icon "><i class="fa fa-fw fa-bars"></i></span>
							<span class="info-box-text">Last Week</span></a>  </div>
							
						</div>
						
					</div> -->
					<!-- /.col -->
					
					<!-- /.col -->
					<div class="col-md-3 col-sm-6 col-xs-12 trendingproductblocktab">
						<div class="info-box">
						<div class="info-box-content">
						<a href="javascript:void(0);" class="totalvisits"> <span class="info-box-icon "><i class="fa fa-fw fa-bars"></i></span>
							 <span class="info-box-text">Visits</span></a>  </div>
							<!-- /.info-box-content -->
						</div>
						<!-- /.info-box -->
					</div>
					
					<!-- /.col -->
					<div class="col-md-3 col-sm-6 col-xs-12 trendingproductblocktab">
						<div class="info-box">
						<div class="info-box-content">
						<a href="javascript:void(0);" class="usagetimespend"> <span class="info-box-icon "><i class="fa fa-fw fa-bars"></i></span>
							 <span class="info-box-text">Time Spend</span></a>  </div>
							
						</div>
						
					</div>
					</div>	

					
				</div>
			  <!-- /.tab-pane -->      
			  
            </div>
            <!-- /.tab-content -->
          </div>

   
	<div class="clearfix clear"></div>

<h1 class="report_header"></h1>

	<div class="box box-primary">
		<div class="box-body"> 
				 <div style="display:none;"  id="users_state_report" class="reports">
				 <div class="row">
						<div class="col-md-6 userstatereportmap">
							<div id="usersbystate"></div>
						</div>
						<div class="col-md-6 map_listing">
							<div class="">
								
								<div id="users_state_report_table">
									
										<?php if (@$state_users) :
										echo '<div class="demo-wrapper " style="">
												<div id="" class="reports ">
												  ';
										?>
											<?php foreach ($state_users as $user) :
											$devideBy=100;
											$userCount=$user['User_Count'];
											if(($userCount) <100)
												$userCount=(($userCount)*100);//echo $userCount.'-------------'.$devideBy;
											if(($userCount)>=10000)
												$devideBy=1000;
											if (($userCount)<=10000 && ($userCount)>=1000)
												$devideBy=100;
											if(($userCount) <=1000)
												$devideBy=10;
											
											$divWidthPercent=($userCount/$devideBy);
											
											echo '<a href="javascript:void(0)" class="">
											<b class="">'.$user['Name'].' </b></a>
											<div class="storecountwithbar">
											<span class="storecount">'.$user['User_Count'].'</span>
												<div class="progress" style="width:'.$divWidthPercent.'%">
											  
												<div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100" style="width:100%">
											
											</div> </div></div>';
											 endforeach; 
										echo '	
													</div>
												</div>
												';
										endif; ?>
									</table>                              
								</div>
							</div>
						</div>

					</div>
					</div>	
				
				<div class="htmltochange">
		

		</div>
	</div>	
		  
		  
		   </div>
                </div>
            </div>
        </div>
    </section>
</div>


<script src="<?php echo $this -> config -> item('admin_assets'); ?>js/pages/fusioncharts.js"></script>
<script src="<?php echo $this -> config -> item('admin_assets'); ?>js/pages/fusioncharts.maps.js"></script>
<script src="<?php echo $this -> config -> item('admin_assets'); ?>js/pages/fusioncharts.theme.fint.js"></script>
<script src="<?php echo $this -> config -> item('admin_assets'); ?>js/pages/fusioncharts.southafrica.js"></script>	




	  <script type="text/javascript">
        $(function(){            
			$("#specialproducts").percircle({
				progressBarColor: "#ED3237",
						});
        });
    </script>
        <script>
		
//USER PER STATE DATA
           var user_data = [
<?php if (@$state_users) : ?>
    <?php foreach ($state_users as $user) : ?>
                    {
                        "hc-key": "<?php echo $user['StateCode'] ?>",
                        "st_key": "<?php echo $user['Id'] ?>",
                        "value": <?php echo $user['User_Count'] ?>
                    },
    <?php endforeach; ?>
<?php endif; ?>
    ];

            //STORES PER STATE DATA
            var store_data = [

			<?php if (@$state_stores_count) : ?>
				<?php foreach ($state_stores_count as $state_store_count) : ?>
								{
									"key": "<?php echo $state_store_count['StateCode'] ?>",
									"value": <?php echo $state_store_count['Store_Count'] ?>
								},
				<?php endforeach; ?>
			<?php endif; ?>
    ];
	

FusionCharts.ready(function() {
    var salesByState = new FusionCharts({
        type: 'southafrica',
        renderAt: 'usersbystate',
        width: '100%',
        height: '385',
        dataFormat: 'json',
        dataSource: {
            "chart": {
                "caption": "",
                "subcaption": "",
                "entityFillHoverColor": "#cccccc",
                "numberScaleValue": "1,1000,1000",
                "numberScaleUnit": "",
                "numberPrefix": " ",
                "showLabels": "1",
                "theme": "fint"
            },
            "colorrange": {
                "minvalue": "0",
                "startlabel": "Low",
                "endlabel": "High",
                "code": "#e44a00",
                "gradient": "1",
                "color": [
                    {
                        "maxvalue": "56580",
                        "displayvalue": "Average",
                        "code": "#f8bd19"
                    },
                    {
                        "maxvalue": "100000",
                        "code": "#6baa01"
                    }
                ]
            },
            "data": [
			<?php
			$gtvalue=0;$ntvalue=0;
			if (@$state_users) { ?>
			<?php foreach ($state_users as $user) {
			if($user['Id']<10) $stateId='0'.$user['Id']; else $stateId=''.$user['Id'];
			?>
                {
                    "id": "<?php echo $stateId; ?>",
                    "value": "<?php echo $user['User_Count']; ?>"
					<?php if($stateId=='02'){ $ntvalue=1;
						?>
						,"displayValue": "KZN"
						<?php
					} else if($stateId=='06'){ $gtvalue=1;
						?>
						,"displayValue": "GP"
					<?php } ?>
                }  ,
			<?php }} 
			if($ntvalue==0){
				?>
				{
            "id": "02",
            "value": "",
            "showLabel": "1",
            "displayValue": "KZN"
        },
				<?php
			}
			if($gtvalue==0){
				?>
				{
            "id": "06",
            "value": "",
            "showLabel": "1",
            "displayValue": "GP"
        },
				<?php
			}
			?>				
               
            ]
        }
    }).render();

});

        </script>
		
