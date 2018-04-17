<div class="">
<input type="hidden" id="baseUrl" value="<?php echo base_url(); ?>">

		<div id="tabss" class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <li class="active"><a href="#general" data-toggle="tab">General</a></li>
              <li><a href="#productstab" data-toggle="tab">Products</a></li>
              <li class="userstabli"><a class="userstaba" href="#userstab" data-toggle="tab">Users</a></li>
              
            </ul>
            <div class="tab-content">
              <div class="tab-pane active" id="general">
					<div class="col-md-3 col-sm-6 col-xs-12 diffblock">
						<div class="info-box ">
							<div class="info-box-content"> <a href="javascript:void(0);" class="storeFormatCount blockanchorclass"> 
							<span class="info-box-text">Store Formats</span> 
							<span class="info-box-number"><?php echo $store_formats_count['store_format_count']; ?></span> 
							<span class="info-box-text subtext">Expand</span> </a>
							</div>
							<!-- /.info-box-content -->
							
						</div>
						<!-- /.info-box -->
					</div>     

						<div style="margin-top: -20px;" class="storeformatsdiv col-md-9 col-sm-6 col-xs-12">
						
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
								<span class="info-box-number" id="store_products_count"><?php //echo $store_products['store_products_count']; ?></span>
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

    <!-- /.col -->
	<div class="clearfix clear"></div>
	
</div>


<h1 class="report_header"></h1>

	<div class="box box-primary">
		<div class="box-body"> 
				 <div style="display:none;"  id="users_state_report" class="reports">
						<div class="col-md-6 userstatereportmap">
							<div id="usersbystate"></div>
						</div>
						<div class="col-md-5 map_listing">
							<div class="col-md-10">
								
								<div id="users_state_report_table">
									
										<?php if (@$state_users) :
										echo '<div class="demo-wrapper" style="width: 130%; !important">
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
						

				<div class="htmltochange">
					
					
					
				</div>
			

		</div>
	</div>	
	
	
<!--	
	<div id="charts" style="margin:auto;"></div>
  <div id="country" style="margin:auto;"></div>
  <div id="city" style="margin:auto;"></div>
  <script src='http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>
<script src='https://www.google.com/jsapi?autoload={"modules":[{"name":"visualization","version":"1"}]}'></script>
<script>
var defaultRefresh=86400;
      var charts=[
               
                    {title:"Overall users per day of week (0 is Monday, 6 is Sunday)",chartType:"ColumnChart",refreshInterval:defaultRefresh,dataSourceUrl:"https://polished-enigma-732.appspot.com/query?id=ahVzfnBvbGlzaGVkLWVuaWdtYS03MzJyFQsSCEFwaVF1ZXJ5GICAgICArpkKDA&format=data-table-response"}
                 
                 ];
      function generateCharts(charts){
    google.setOnLoadCallback(drawVisualization);

    function drawVisualization() {

    var parent = document.getElementById("charts");
          console.log(parent);
          for (var i in charts){
            var node = document.createElement("div");
              node.id=i;
            parent.appendChild(node);
              new google.visualization.ChartWrapper({
         "containerId": i,
         "dataSourceUrl": charts[i].dataSourceUrl,
         "refreshInterval": charts[i].refreshInterval,
         "chartType": charts[i].chartType,
         "options": {
            "showRowNumber" : true,
            "is3D": true,
            "title": charts[i].title
         }
       }).draw();
              
              
          
          
          }
    }
        

    }
     





$('document').ready(function(){
generateCharts(charts);

});
</script>
	
	-->
	
	
	  
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
        width: '600',
        height: '400',
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
<script src="<?php echo $this -> config -> item('admin_assets'); ?>js/pages/Chart.js"></script>
