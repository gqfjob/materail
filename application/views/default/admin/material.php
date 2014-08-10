<?php
?>
<!-- Content starts -->
<div class="content">
	<?php echo $bg_left;?>
	<!-- Main bar -->
	<div class="mainbar">
		<!-- Page heading -->
        <div class="page-head">
        	<h2 class="pull-left"><i class="icon-file-alt"></i> 素材管理</h2>
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
	      	 <form class="navbar-form navbar-right" role="form">
				  <div class="form-group">
				    <label class="sr-only" for="exampleInputEmail2">搜索素材</label>
				    <input type="email" class="form-control col-lg-9" id="exampleInputEmail2" placeholder="搜索素材名">
				  </div>
				  <div class="form-group">
				  <button type="submit" class="btn btn-default">查找</button>
		 	</form>
		 	
	  </div>
	  <div class="clearfix"></div>
      <!-- Page heading ends -->
		<div class="container">
          <!-- Table -->
        	<div class="row">
            	<div class="col-md-12">
	                <div class="widget">
		            	<div class="widget-head">
		                	<div class="pull-left">素材列表</div>
		                    <div class="clearfix"></div>
		                </div>
		                <div class="widget-content">
		                    <table class="table table-striped table-bordered table-hover">
		                    	<thead>
			                        <tr>
			                        	<th><input type="checkbox" name="material-select" value="" /></th>
			                            <th>素材名</th>
			                            <th>作者</th>
			                            <th>类型</th>
			                            <th>附件数</th>
			                            <th>版本数</th>
			                            <th>当前版本描述</th>
			                            <th>上传时间</th>
			                        </tr>
		                      	</thead>
		                      <tbody>
		                         <?php if(empty($materials)) : ?>
		                         <tr>
		                         	<td colspan="8">暂无素材</td>
		                         </tr>
		                         <?php else: ?>       
		                         <?php foreach($materials as $material) : ?>
		                         <tr>
		                         	<td><input type="checkbox" name="material-select" value="" /></td>
		                         	<td><?php echo $material['mname']; ?></td>
		                         	<td><?php echo isset($users[$material['uid']]['nickname']) ? $users[$material['uid']]['nickname'] : '';?></td>
		                         	<td><?php echo $material['cname']; ?></td>
		                         	<td></td>
		                         	<td><?php echo $material['vernum']?></td>
		                         	<td><?php echo isset($versions[$material['cversion']]['depict'])?></td>
		                         	<td><?php echo date('Y-m-d H:i:s', $material['create_at'])?></td>
		                         </tr>
		                         <?php endforeach;?>
		                         <?php endif;?>                                            
		                      </tbody>
		                    </table>
		
		                    <div class="widget-foot">
								<div class="pull-left" style="padding-top:10px">
									<button type="button" class="btn btn-primary">转为草稿</button>
									<button type="button" class="btn btn-success">发布</button>
									<button type="button" class="btn btn-danger">批量删除</button>
								</div>
		                      	
		                        <ul class="pagination pull-right">
		                          <li><a href="#">上一页</a></li>
		                          <li><a href="#">1</a></li>
		                          <li><a href="#">2</a></li>
		                          <li><a href="#">3</a></li>
		                          <li><a href="#">4</a></li>
		                          <li><a href="#">下一页</a></li>
		                        </ul>
		                      
		                      <div class="clearfix"></div> 
		
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