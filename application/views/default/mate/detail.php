<style>
.mdetail,.mcontent{
	width:530px;
}
.mtype{
	display:inline-block;
	background:red;
	width:50px;
	height:30px;
	line-height:30px;
	padding:0px 0px 0px 10px;
	margin-right:5px;
	color:#fff;
}
.mtype a{
	color:#fff;
	background:red;
}
.mvdetail{
	text-indent: 2em;
	min-height:100px;
	padding-bottom:20px;
	line-height:24px;
	padding-left:20px;
	padding-right:20px;
}

.works-manage-box {
	height: 55px;
	background-color: #3f5260;
}
.works-manage-download {
	width: 124px;
	height: 55px;
	line-height: 55px;
	text-align: center;
	background-color: #d01876;
	overflow: hidden;
	cursor: pointer;
}
.works-manage-download em {
	display: inline-block;
	font-style: normal;
	font-size: 16px;
	font-family: microsoft yahei;
	color: #fff;
	vertical-align: middle;
}
.mr5 {
	margin-right: 5px;
}
.boxHd {
	line-height: 18px;
}
.roundFont{
	bord-moz-border-radius: 15px;
	-webkit-border-radius: 15px;
	border-radius:15px;
	border:1px solid #2EC4F9;
	padding:1px 5px;
	color:#2EC4F9;
	margin-right:5px;
}
.attachment-item{
	padding:5px 0;
	color:#2ec4f9;
}
.attachment-item .btn{
	color:#2ec4f9;
	font-size:13px !important;
}
.attachment-item .download{margin-left:5px;}
.same-list .mlogo{
	background: none repeat scroll 0 0 #f5f8fa;
    border-color: #9a9a9a #eeeeee #eeeeee #9a9a9a;
    border-style: solid;
    border-width: 1px;
    margin:0 auto;
    height: 142px;
    padding: 10px;
}
.same-list .mlogo img{
    width:208px;
    height:122px;
}
.same-list .mname{
	padding:5px;
}
.other-list,.same-list {
	padding:10px;
}
.other-list{min-height:180px;}
.other-list .other-item{
	overflow:hidden;
	padding:5px 0;
	display:none;
}
.other-list .version-depict{
	color:#08c;
	max-width:145px;
}
.other-list .version-date{
	color:#e3137b;
}
.pages{
	overflow:hidden;
	padding:5px 10px;
}
</style>
<div id="wcontainer">
	<div class="crumb mb20 mt20">
		<div class="fl ico crumb-ico mr5"></div>当前位置：
	    <a href="javascript:void(0);" title="" hidefocus="true">素材详情</a>&nbsp;&gt;&nbsp;<a href="javascript:void(0);" title="" hidefocus="true"><?php echo $material['mname']?></a>
	</div>
	<div id="wPageLeft">
		<!-- 素材基本信息介绍 -->
		<div class="oneMaterail">
			<div class="mbasic">
				<div class="mlogo">
					<img src="<?php echo base_url($material['logo']);?>"/>
				</div>
				<div class="mdetail">
					<p class="mtitle"><span class="mtype"><a href="<?php echo base_url('material/lists/' . $material['cid']);?>" style="color:#fff"><?php echo $material['cname'];?></a></span><span class="bold"><?php echo $material['mname']?></span></p>
					<ul class="mtime">
						<li style="width:220px">上传时间：<?php echo date('Y-m-d H:i', $material['create_at']);?></li>
						<li style="width:90px">版本数：<?php echo $material['vernum']?></li>
					</ul>
					<div class="cl"></div>
					<ul class="minfo">
						<li style="width:220px">更新时间：<?php echo date('Y-m-d H:i', $material['update_at']);?></li>
						<li style="width:300px;overflow:hidden">当前版本：<?php echo $version['depict'];?></li>
					</ul>
					<div class="cl"></div>
					<div class="mcontent">
						<?php echo cut_str($version['nohtml'], 70);?>
					</div>
				</div>
				<div class="cl"></div>
			</div>
		</div>
		<!-- 详情介绍 -->
		<div class="wBoxLeft mt20">
			<div>
            	<div class="fl ico original-ico" style="margin:6px">&nbsp;</div>
            	<h2 class="fl font-yahei b16">素材说明</h2>
            	<div class="cl"></div>
            </div>
            <div class="mvdetail font-yahei font14">
            	<?php echo $version['content']; ?>
            </div>
		</div>
		<!-- 素材附件-->
		<div class="wBoxLeft mt20">
			<div>
            	<div class="fl ico media-ico" style="margin:8px">&nbsp;</div>
            	<h2 class="fl font-yahei b16">素材附件</h2>
            	<div class="cl"></div>
            </div>
            <div class="font-yahei font14" style="padding:0 20px;">
            <?php if( ! empty($version_attachment)) : ?>
            	<ul class="attachment-list">
            		<?php foreach($version_attachment as $attachment) : ?>
            		<li class="attachment-item">
            			<span><?php echo $attachment['sname'];?></span>
            			<a href="<?php echo base_url('file/download/attachment/' . $attachment['id']);?>" class="btn btn-default pull-right download" target="_blank">下载</a>
            			<?php if(in_array($attachment['pfix'], array('jpg', 'gif', 'png', 'txt'))) : ?>
            			<a href="<?php echo base_url('file/view/' . $attachment['id']);?>" class="btn btn-default pull-right" target="_blank">查看</a>
            			<?php endif;?>
            		</li>
            		<?php endforeach;?>
            	</ul>
            <?php else:?>
            	<div class="text-center" style="padding:10px 0">没有附件</div>
            <?php endif;?>
            </div>
		</div>
		<!-- 打包下载 -->
		<?php if( ! empty($version_attachment)) : ?>
		<div class="works-manage-box mt20">
            <a href="<?php echo base_url('file/download/version/' . $version['id']);?>" title="点击进入下载" class="fr hover-none works-manage-download" hidefocus="true" target="_blank"><em class="mr5">打包下载</em><span class="download-ico2 ico inline-block vertical-middle"></span></a>
        </div>
        <?php endif;?>
	</div>
	<div id="wPageRight">
		<?php if( ! empty($other_versions)): ?>
		<div class="wBoxRight">
			<div class="boxHd">
				<div class="fl down-rank-ico ico" style="margin:5px 5px 0px 10px"></div><div class="fl b16 font-yahei mt5">其他版本下载</div><div class="fr mt5 font12 roundFont">版本数:<?php echo $material['vernum'] - 1;?></div>
				<div class="cl"></div>
			</div>
			<div style="min-height: 200px">
				<ul class="other-list">
					<?php $i = 0?>
					<?php foreach($other_versions as $other_version) : $i++;?>
					<li class="other-item font14" data-page="<?php echo ceil($i/$per_page);?>">
						<a href="<?php echo base_url('material/detail/'.$material['id'].'/'.$other_version['id']);?>" class="version-depict pull-left text-overflow" title="<?php echo $other_version['depict']?>" ><?php echo $other_version['depict']?></a>
						<span class="version-date pull-right"><?php echo date('Y/m/d', $other_version['cat'])?></span>
					</li>
					<?php endforeach;?>
				</ul>
				<?php if($pages > 1) : ?>
				<div class="pages">
					<input type="hidden" id="current-page" value="1" autocomplete="off" />
					<input type="hidden" id="max-page" value="<?php echo $pages;?>" autocomplete="off" />
					<button type="button" id="other-prev" class=" pull-left btn btn-default">上一页</button>
					<button type="button" id="other-next" class="other-next pull-right btn btn-default">下一页</button>
				</div>
				<?php endif;?>
			</div>
		</div>
	    <?php endif;?>
		<div class="wBoxRight <?php if( ! empty($other_versions)) {echo ' mt20';}else{ echo ' mt10';}?>">
			<div class="boxHd">
				<div class="fl keywords-ico ico" style="margin:5px 5px 0px 10px"></div><h2 class="fl b16 font-yahei">同类型素材</h2>
				<div class="cl"></div>
			</div>
			<div style="min-height: 200px">
			<?php if( ! empty($same_materials)): $i = 0;?>
				<ul class="same-list">
					
					<?php foreach($same_materials as $same_material) : $i++?>
					<li class="same-item" >
					<?php if($material['cname'] == '图片') :?>
					 <div class="mlogo text-center"><a href="<?php echo base_url('material/detail/' . $same_material['id']);?>"  target="_blank"><img src="<?php echo base_url($same_material['logo']);?>" /></a></div>
					 <div class="mname text-center font14 text-overflow"><a href="<?php echo base_url('material/detail/' . $same_material['id']);?>"  target="_blank" title="<?php echo $same_material['mname']?>"><?php echo $same_material['mname']?></a></div>
					 <?php else : ?>
					 <div class="mname font14 text-overflow"> <span class="badge"><?php echo $i;?></span>&nbsp;<a href="<?php echo base_url('material/detail/' . $same_material['id']);?>"  target="_blank" title="<?php echo $same_material['mname']?>"><?php echo $same_material['mname']?></a></div>
					 <?php endif;?>
					</li>
					<?php endforeach;?>
				</ul>
			<?php else : ?>
				<div class="text-center" style="padding:10px 0">没有同类型素材</div>
			<?php endif;?>
			</div>
		</div>
	</div>
	<div class="cl"></div>
</div>
<script type="text/javascript">
	$(function(){
		$('.other-item[data-page="1"]').show();

		$('#other-prev').click(function(){
			var current_page = parseInt($('#current-page').val());
			if(current_page == 1){
				return false;
			}else{
				page = current_page - 1;
				$('#current-page').val(page);
				$('.other-item').hide();
				$('.other-item[data-page="' + page + '"]').show();
			}
		});

		$('#other-next').click(function(){
			var current_page = parseInt($('#current-page').val());
			var max_page = parseInt($('#max-page').val());
			if(current_page == max_page){
				return false;
			}else{
				page = current_page + 1;
				$('#current-page').val(page);
				$('.other-item').hide();
				$('.other-item[data-page="' + page + '"]').show();
			}
		});
	});
</script>