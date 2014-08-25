<?php
?>
<!-- Content starts -->
<div class="content">
	<?php echo $bg_left;?>
	<!-- Main bar -->
	<div class="mainbar">
		 <div class="page-head">
        	<h2 class="pull-left"><i class="icon-magic"></i> 系统设置</h2>
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
		                	<div class="pull-left">系统设置</div>
		                    <div class="clearfix"></div>
		                </div>
		                <div class="widget-content">
		                	<div class="padd">
			                	<form role="form" class="form-horizontal" method="post" action="<?php echo base_url('admin/set_site_config');?>">
			                		<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" autocomplete="off" />
				                    <div class="form-group">
		                            	<label class="col-lg-4 control-label">网站名称</label>
		                                <div class="col-lg-8">
		                                    <input type="text" name="title" autocomplete="off" placeholder="输入网站名称" class="form-control" value="<?php echo isset($site_config['SITE_TITLE']['svalue']) ? $site_config['SITE_TITLE']['svalue'] : '';?>">
		                                </div>
		                            </div>
		                            <div class="form-group">
	                                    <label class="col-lg-4 control-label">网站公告</label>
	                                    <div class="col-lg-8">
	                                      <textarea autocomplete="off" name="notice" placeholder="输入网站公告" rows="3" class="form-control"><?php echo isset($site_config['SITE_NOTICE']['svalue']) ? $site_config['SITE_NOTICE']['svalue'] : '';?></textarea>
	                                    </div>
	                                </div>
	                                <div class="form-group">
	                                  <label class="col-lg-4 control-label">发布</label>
	                                  <div class="col-lg-8">
	                                    <label class="checkbox-inline">
	                                      <input  autocomplete="off" name="is_notice" type="checkbox" value="option1" id="inlineCheckbox1" <?php echo (isset($site_config['IS_NOTICE']['svalue']) && $site_config['IS_NOTICE']['svalue']) ? 'checked="checked"' : '';?>>
	                                    </label>
	                                  </div>
	                                </div>
	                                 <div class="form-group">
	                                    <label class="col-lg-4 control-label"></label>
	                                    <div class="col-lg-8">
											<input id="set-system" type="submit" class="btn btn-primary" value="保存" />
										</div>
									</div>
	                            </form>
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