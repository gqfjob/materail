<?php
?>
<!-- Content starts -->
<div class="content">
	<?php echo $bg_left;?>
	<!-- Main bar -->
	<div class="mainbar">
		 <div class="page-head">
        	<h2 class="pull-left"><i class="icon-home"></i> 后台首页</h2>
        	<!-- Breadcrumb -->
	        <div class="bread-crumb pull-right">
	        	<a href="<?php echo base_url('admin/index'); ?>"><i class="icon-home"></i> 首页</a> 
	            <!-- Divider -->
	          	<span class="divider">/</span> 
	          	<a href="#" class="bread-current">控制台</a>
	        </div>
        	<div class="clearfix"></div>
         </div>
         
         <div class="container">
          <!-- Table -->
        	<div class="row">
            	<div class="col-md-12">
	                <div class="widget">
		            	<div class="widget-head">
		                	<div class="pull-left">数据统计:</div>
		                    <div class="clearfix"></div>
		                </div>
		                <div class="widget-content">
		                	<div class="padd">
			                    <div class="row">
	                            	<div class="col-lg-4">
	                            		<strong>素材数 : <a href="<?php echo base_url('admin/mgMaterial');?>"><?php echo $total_material;?></a></strong>
	                            	</div>
	                                <div class="col-lg-8">
	                                	<strong>下载量 : <?php echo $total_download;?></strong>
	                                </div>
	                            </div>
	                            <div class="row">
                                    <div class="col-lg-4">
                                    	<strong>用户数 : <a href="<?php echo base_url('admin/mgUser');?>"><?php echo $total_user;?></a></strong>
                                    </div>
                                    <div class="col-lg-8">
                                    	<strong>访问量 : <a href="<?php echo base_url('admin/mgVisitor');?>"><?php echo $total_visit;?></a></strong>
                                    </div>
                                </div>
		                    </div>
		                </div>
	                </div>
              </div>
            </div>
        </div>
	</div>
	<!-- Main bar end-->
	<div class="clearfix"></div>
</div>
<!-- Content ends -->