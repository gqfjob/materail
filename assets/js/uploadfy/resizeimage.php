<?php
/***************************************/
/*功   能：利用PHP的GD库生成高质量的缩略图*/
/*运行环境：PHP5.01/GD2*/
/*类说明：可以选择是/否裁图。

如果裁图则生成的图的尺寸与您输入的一样。
原则：尽可能多保持原图完整

如果不裁图，则按照原图比例生成新图
原则：根据比例以输入的长或者宽为基准*/
/*参 数：$img:源图片地址
 $wid:新图的宽度
 $hei:新图的高度
 $c:是否裁图，1为是，0为否*/
/***************************************/
class resizeimage
{
    //图片类型
    var $type;
    //实际宽度
    var $width;
    //实际高度
    var $height;
    //改变后的宽度
    var $resize_width;
    //改变后的高度
    var $resize_height;
    //是否裁图
    var $cut;
    //源图象
    var $srcimg;
    //目标图象地址
    var $dstimg;
    //临时创建的图象
    var $im;
    //转换后的目标文件后缀
    var $targetDir;

    function resizeimage($img, $wid, $hei,$c,$t)
    {
        $this->srcimg = $img;
        $this->resize_width = $wid;
        $this->resize_height = $hei;
        $this->cut = $c;
        $this->targetDir = $t;
        //图片的类型
        $this->type = substr(strrchr($this->srcimg,"."),1);
        //初始化图象
        $this->initi_img();
        //目标图象地址
        $this -> dst_img($this->targetDir);
        //--
        $this->width = imagesx($this->im);
        $this->height = imagesy($this->im);
        //生成图象
        $this->newimg();
        ImageDestroy ($this->im);
    }
    function newimg()
    {
        //改变后的图象的比例
        $resize_ratio = ($this->resize_width)/($this->resize_height);
        //实际图象的比例
        $ratio = ($this->width)/($this->height);
        if(($this->cut)=="1")
        //裁图
        {
            if($ratio>=$resize_ratio)
            //高度优先
            {
                $newimg = imagecreatetruecolor($this->resize_width,$this->resize_height);
                imagecopyresampled($newimg, $this->im, 0, 0, 0, 0, $this->resize_width,$this->resize_height, (($this->height)*$resize_ratio), $this->height);
                ImageJpeg ($newimg,$this->dstimg);
            }
            if($ratio<$resize_ratio)
            //宽度优先
            {
                $newimg = imagecreatetruecolor($this->resize_width,$this->resize_height);
                imagecopyresampled($newimg, $this->im, 0, 0, 0, 0, $this->resize_width, $this->resize_height, $this->width, (($this->width)/$resize_ratio));
                ImageJpeg ($newimg,$this->dstimg);
            }
        }
        else
        //不裁图
        {
            if($ratio>=$resize_ratio)
            {
                $newimg = imagecreatetruecolor($this->resize_width,($this->resize_width)/$ratio);
                imagecopyresampled($newimg, $this->im, 0, 0, 0, 0, $this->resize_width, ($this->resize_width)/$ratio, $this->width, $this->height);
                ImageJpeg ($newimg,$this->dstimg);
            }
            if($ratio<$resize_ratio)
            {
                $newimg = imagecreatetruecolor(($this->resize_height)*$ratio,$this->resize_height);
                imagecopyresampled($newimg, $this->im, 0, 0, 0, 0, ($this->resize_height)*$ratio, $this->resize_height, $this->width, $this->height);
                ImageJpeg ($newimg,$this->dstimg);
            }
        }
    }
    //初始化图象
    function initi_img()
    {
        if($this->type=="jpg")
        {
            $this->im = imagecreatefromjpeg($this->srcimg);
        }
        if($this->type=="gif")
        {
            $this->im = imagecreatefromgif($this->srcimg);
        }
        if($this->type=="png")
        {
            $this->im = imagecreatefrompng($this->srcimg);
        }
    }
    //图象目标地址
    function dst_img($dir)
    {
        $full_length   = strlen($this->srcimg);
        $type_length   = strlen($this->type);
        $name_length   = $full_length-$type_length;
        $name       = substr($this->srcimg,0,$name_length-1);
        $this->dstimg =str_replace('big',$dir, $name).".".$this->type;
    }
}

function random_string($type = 'alnum', $len = 8)
{
    switch($type)
    {
        case 'basic'    : return mt_rand();
            break;
        case 'alnum'    :
        case 'numeric'  :
        case 'nozero'   :
        case 'alpha'    :

                switch ($type)
                {
                    case 'alpha'    :   $pool = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                        break;
                    case 'alnum'    :   $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                        break;
                    case 'numeric'  :   $pool = '0123456789';
                        break;
                    case 'nozero'   :   $pool = '123456789';
                        break;
                }

                $str = '';
                for ($i=0; $i < $len; $i++)
                {
                    $str .= substr($pool, mt_rand(0, strlen($pool) -1), 1);
                }
                return $str;
            break;
        case 'unique'   :
        case 'md5'      :

                    return md5(uniqid(mt_rand()));
            break;
    }
}
