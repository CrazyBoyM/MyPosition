<?php
/**
 * 写作时自动定位当前地理位置
 * @package #南喵写作定位#
 * @author 南城猫
 * @version 3.0.2
 * @link http://es.ip3x.com
 * 支持学习，讽刺盗改
 */
class MyPosition_Plugin implements Typecho_Plugin_Interface
{
	/**
     * 激活插件方法,如果激活失败,直接抛出异常
     * 
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
  /* 启用插件方法 */
	public static function activate(){
    // 添加按钮
    Typecho_Plugin::factory('admin/write-post.php')->bottom = array('MyPosition_Plugin', 'positionButton');
    //Typecho_Plugin::factory('Widget_Contents_Page_Edit')->bottom = array('MyPosition_Plugin', 'positionButton');
    // 内容处理
    Typecho_Plugin::factory('Widget_Abstract_Contents')->contentEx = array('MyPosition_Plugin','showPosition');
    // 摘要处理
    Typecho_Plugin::factory('Widget_Abstract_Contents')->excerptEx = array('MyPosition_Plugin','hidePosition');
    // 头部插入
    Typecho_Plugin::factory('Widget_Archive')->header = array('MyPosition_Plugin', 'header');
    // 尾部插入
    Typecho_Plugin::factory('Widget_Archive')->footer = array('MyPosition_Plugin', 'footer');
    return "喵~插件安装成功";
	  }
	/**
     * 禁用插件方法,如果禁用失败,直接抛出异常
     *
     * @static
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
  public static function deactivate(){
      return "喵~插件卸载成功";
    }
    /**
     * 获取插件配置面板
     *
     * @access public
     * @param Typecho_Widget_Helper_Form $form 配置面板
     * @return void
     */
	public static function config(Typecho_Widget_Helper_Form $form){
    $f = new Typecho_Widget_Helper_Form_Element_Text(
      'apikey',
      null,
      null,
      _t('输入在腾讯地图官网获得的Key值')
    );
    $form->addInput($f);
    $f = new Typecho_Widget_Helper_Form_Element_Radio(
      'usetype',
      array(
      	'0' => _t('按钮'),
      	'1' => _t('自动定位')
      	),
      '0',
      _t('使用方式'),
      null
    );
    $form->addInput($f);
    $f = new Typecho_Widget_Helper_Form_Element_Radio(
      'precision',
      array(
      	'0' => _t('尽可能精确'),
      	'1' => _t('到市级')
      	),
      '0',
      _t('精度'),
      null
    );
    $form->addInput($f);
    $f = new Typecho_Widget_Helper_Form_Element_Radio(
      'type',
      array(
	      '1' => _t('显示在内容区'),
	      '2' => _t('显示在短代码的位置'),
	      '0' => _t('关闭全部位置显示')
    	),
      '1',
      _t('显示方式'),
      _t('选择位置显示的方式(如需短代码，请自行插如下内容到原生代码中：<strong><删pos/删></strong>,注意：需要jquery)')
    );
    $form->addInput($f);
    $f = new Typecho_Widget_Helper_Form_Element_Radio(
      'loadjq',
      array(
        '0' => _t('不加载'),
        '1' => _t('加载')
      ),
      '0',
      _t('加载Jquery'),
      _t('是否引入Jquery(3.4.1)，如引入后原有主题功能异常请关闭此项（一般情况下大部分主题自带jquery，无需开启此项）')
    );
    $form->addInput($f);
    $f = new Typecho_Widget_Helper_Form_Element_Text(
      'start',
      null,
      _t('<div class="typos"><span>位置 : '),
      _t('[pos]对应的解析')
    );
    $form->addInput($f);
    $f = new Typecho_Widget_Helper_Form_Element_Text(
      'end',
      null,
      _t('</span></div>'),
      _t('[/pos]对应的解析')
    );
    $form->addInput($f);
    $f = new Typecho_Widget_Helper_Form_Element_Textarea(
      'style',
      null,
      _t('.typos{
            font-size:15px;
            font-weight:356;
            font-family:"KaiTi"
          }
        '),
      _t('自定义style')
    );
    $form->addInput($f);


    echo '<a onclick="window.open(\'https://cn.bing.com/search?q=%E8%85%BE%E8%AE%AF%E5%9C%B0%E5%9B%BEkey%E7%94%B3%E8%AF%B7\')">申请腾讯地图Key的教程</a>';
    echo '<br><a onclick="window.open(\'https://es.ip3x.com\')">官网获取新版本</a>';
    echo '<a onclick="window.open(\'https://github.com/CrazyBoyM/MyPosition\')">   Github</a>';
    }
  // 用户个人设置
  public static function personalConfig(Typecho_Widget_Helper_Form $form){}

  // 插件实现
  public static function positionButton(){ 
    if(!Typecho_Widget::widget('Widget_Options')->plugin('MyPosition')->apikey) return;
    if(Typecho_Widget::widget('Widget_Options')->plugin('MyPosition')->type == 0) return;
    $home = Helper::options()->pluginUrl.'/MyPosition';?>
    <script>var precision =<?echo Typecho_Widget::widget('Widget_Options')->plugin('MyPosition')->precision?>;</script><?
    if(Typecho_Widget::widget('Widget_Options')->plugin('MyPosition')->usetype == 0) {
	?>
    <script type="text/javascript" src="https://apis.map.qq.com/tools/geolocation/min?key=<?php echo Typecho_Widget::widget('Widget_Options')->plugin('MyPosition')->apikey;?>&referer=myapp"></script>
    <script src="https://cdn.jsdelivr.net/npm/layui-layer@1.0.9/dist/layer.js" integrity="sha256-L4H29RJtGmgEBvVsRRTFCMq3gSUVE7vRxUIO1FWQ9gI=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/font-awesome@4.7.0/css/font-awesome.min.css" integrity="sha256-eZrrJcwDc/3uDhsdt61sL2oOBY362qM3lon1gyExkL0=" crossorigin="anonymous">
    <?php
    echo "<script src=\"{$home}/js/addPosition.js\"></script>";
    echo "<script src=\"{$home}/js/addStr.js\"></script>";
    echo "<script src=\"{$home}/js/addButton.js\"></script>";
	} else if(Typecho_Widget::widget('Widget_Options')->plugin('MyPosition')->type == 1){
		?>
    <script type="text/javascript" src="https://apis.map.qq.com/tools/geolocation/min?key=<?php echo Typecho_Widget::widget('Widget_Options')->plugin('MyPosition')->apikey;?>&referer=myapp"></script>
    <script src="<?echo $home?>/js/addPosition.js"></script>
    <script>
	    $(document).ready(function(){
	    	getPosition();
	    	$("#text").val("自动定位地理位置中...");
	    	setTimeout(function() {
	    		$("#text").val("[pos]"+posx+"[/pos]");
	    	}, 3000);
	    });
    </script>
    	<?php
	}
  }
  public static function showPosition($data,$widget,$last){
    $content = empty($last)?$data:$last;
    $begin=(Typecho_Widget::widget('Widget_Options')->plugin('MyPosition')->start == null)?'<div><span>位置 : ':Typecho_Widget::widget('Widget_Options')->plugin('MyPosition')->start;
    $end=(Typecho_Widget::widget('Widget_Options')->plugin('MyPosition')->end == null)?'</span></div>':Typecho_Widget::widget('Widget_Options')->plugin('MyPosition')->end;
    if (Typecho_Widget::widget('Widget_Options')->plugin('MyPosition')->type == 0) {
    	$content=preg_replace("#\[pos\](.*)\[/pos\]#","",$content);
    }else if ($widget instanceof Widget_Archive) {
    	if (Typecho_Widget::widget('Widget_Options')->plugin('MyPosition')->type == 1)
    		$content=str_replace(array('[pos]','[/pos]'),array($begin,$end),$content);
    	if (Typecho_Widget::widget('Widget_Options')->plugin('MyPosition')->type == 2)
    		$content=str_replace(array('[pos]','[/pos]'),array("<script>var myposx='","'</script>"),$content);
    }
    return $content;
  }
  public static function hidePosition($data,$widget,$last){
    $content = empty($last)?$data:$last;
    if ($widget instanceof Widget_Archive) {
    	$content=preg_replace("#\[pos\](.*)\[/pos\]#","",$content);
    }
    return $content;
  }
  public static function loadJquery(){
    echo '<script src="https://cdn.jsdelivr.net/npm/jquery@3.4.1/dist/jquery.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>';
  }
  public static function shortCode(){
  	$home = Helper::options()->pluginUrl.'/MyPosition';?>
    <script>
    $(document).ready(function(){
	    var begin='<?php echo (Typecho_Widget::widget('Widget_Options')->plugin('MyPosition')->start == null)?'<div><span>位置 : ':Typecho_Widget::widget('Widget_Options')->plugin('MyPosition')->start?>';
	    var end='<?php echo (Typecho_Widget::widget('Widget_Options')->plugin('MyPosition')->end == null)?'</span></div>':Typecho_Widget::widget('Widget_Options')->plugin('MyPosition')->end?>';
	    myposx=begin+myposx+end;
	    $("pos").prop("outerHTML",myposx);
	});
    </script>";<?php
  }
  public static function header(){
    if(Typecho_Widget::widget('Widget_Options')->plugin('MyPosition')->start != null){
      echo "<style>".Typecho_Widget::widget('Widget_Options')->plugin('MyPosition')->style."</style>";
    }
  }

  public static function footer(){
    if(Typecho_Widget::widget('Widget_Options')->plugin('MyPosition')->loadjq) MyPosition_Plugin::loadJquery();
    if(Typecho_Widget::widget('Widget_Options')->plugin('MyPosition')->type == 2) MyPosition_Plugin::shortCode();
  }
}
  ?>