<?php
/**
 * 写作时自动定位当前地理位置
 * @package #南喵写作定位#
 * @author 南城猫
 * @version 3.5.8
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
    return "喵~  你好，主人";
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
      return "喵~  再见，且行且珍惜";
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
      'keytx',
      null,
      null,
      _t('腾讯地图Key')
    );
    $form->addInput($f);
    $f = new Typecho_Widget_Helper_Form_Element_Text(
      'keygd',
      null,
      null,
      _t('高德地图Key')
    );
    $form->addInput($f);
    $f = new Typecho_Widget_Helper_Form_Element_Text(
      'keybd',
      null,
      null,
      _t('百度地图Key')
    );
    $form->addInput($f);
    $f = new Typecho_Widget_Helper_Form_Element_Radio(
      'sdk',
      array(
      	'0' => _t('腾讯地图'),
      	'1' => _t('高德地图'),
      	'2' => _t('百度地图')
      	),
      '0',
      _t('选择定位服务（请自行测试并选出对你当地地区最准确的定位服务）'),
      null
    );
    $form->addInput($f);
    $f = new Typecho_Widget_Helper_Form_Element_Radio(
      'usetype',
      array(
      	'0' => _t('按钮'),
      	'1' => _t('自动定位(仅显示在内容区时执行)')
      	),
      '0',
      _t('使用方式'),
      null
    );
    $form->addInput($f);
    $f = new Typecho_Widget_Helper_Form_Element_Radio(
      'precision',
      array(
      	'1' => _t('尽可能精确(可能有误差)'),
      	'0' => _t('到市级')
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
	      '0' => _t('关闭全部位置显示(即关闭插件)')
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


    echo '<strong><a onclick="window.open(\'https://es.ip3x.com\')" style="padding:5px 5px 5px 0">官网获取新版本</a>';
    echo '<a onclick="window.open(\'https://github.com/CrazyBoyM/MyPosition\')" style="padding:5px">Github</a></strong><br>';
    echo '<a onclick="window.open(\'https://lbs.qq.com\')" style="padding:5px 5px 5px 0">腾讯地图Key申请</a>'; 
    echo '<a onclick="window.open(\'https://lbs.amap.com\')" style="padding:5px">高德地图Key申请</a>'; 
    echo '<a onclick="window.open(\'http://lbsyun.baidu.com\')" style="padding:5px">百度地图Key申请</a>'; 
	echo '<br><span><strong>注意1：</strong>请在使用南喵写作定位插件时完全关闭所有网络代理，并确保设备本身支持定位，否则可能得到错误地址</span>';
	echo '<br><span><strong>注意2：</strong>key请点击上方链接去相应网站申请，若不填写则默认免费使用开发者的Key(不保证稳定有效)</span>';
    }
  // 用户个人设置
  public static function personalConfig(Typecho_Widget_Helper_Form $form){}
	

  // 插件实现
  public static function tengxunPosition($key){
  	if(!$key) $key="TVMBZ-E5XK4-DUHUV-DPXXB-WWTF3-FOBYS";
  	echo "<script type=\"text/javascript\" src=\"https://apis.map.qq.com/tools/geolocation/min?key={$key}&referer=myapp\"></script>";?>
  	
  	<script>
		var geolocation = new qq.maps.Geolocation();
		var options = {timeout: 9000};
		function getCurLocation() {
		    geolocation.getLocation(showPosition, showErr, options);
		}
		function getAboutLocation() {
		    geolocation.getIpLocation(showPosition, showErr);
		}
		function showPosition(pos) {
		    posx=precision?pos.nation+pos.city+pos.addr:pos.nation+pos.city;
		    nopos=false;
		}
		function showErr() {
		    posx="#2 定位失败,请检查是否已填写正确有效的Key或网站是否启动https,也可能是因为没有启用设备的位置开关。";
		    nopos=false;
		}
		function showWatchPosition() {
		    geolocation.watchPosition(showPosition);
		}
		function showClearWatch() {
		    geolocation.clearWatch();
		}
		getCurLocation();
  	</script><?
  }
  public static function gaodePosition($key){
  	if(!$key) $key="f7c7624f72e680e1eecfc8cb4dd8b9ac";
  	?><script type="text/javascript" src="https://webapi.amap.com/maps?v=1.4.15&key=<?echo $key?>"></script>
  	<script>
	  AMap.plugin('AMap.Geolocation', function() {
		  var geolocation = new AMap.Geolocation({
		    // 是否使用高精度定位，默认：true
		    enableHighAccuracy: true,
		    // 设置定位超时时间，默认：无穷大
		    timeout: 10000,
		    // maximumAge: 0,           //定位结果缓存0毫秒，默认：0
        	//convert: false  ,//坐标偏移
        	//enableHighAccuracy: false,//高精度定位
        	//noIpLocate: 3 //禁用Ip定位
		  })
		
		  geolocation.getCurrentPosition()
		  AMap.event.addListener(geolocation, 'complete', onComplete)
		  AMap.event.addListener(geolocation, 'error', onError)
		
		  function onComplete (data) {
		    // data是具体的定位信息
		    //console.log(data);
		    posx=precision?data.formattedAddress:data.addressComponent.country+data.addressComponent.province+data.addressComponent.city;
		  }
		
		  function onError (data) {
		    // 定位出错
		    console.log(data);
		  }
		})
	  
  	</script><?
  }
  public static function baiduPosition($key){
  	if(!$key) $key="dn22N7StZC488N7N5SHff4Ts0SmniG1f";
  	echo "<div id=\"allmap\"></div><script type=\"text/javascript\" src=\"https://api.map.baidu.com/api?v=2.0&ak={$key}\"></script>";?>
  	<script>
		var geolocation = new BMap.Geolocation();
		geolocation.getCurrentPosition(function(e){
			if(this.getStatus() == BMAP_STATUS_SUCCESS){
				//console.log(e);
				var x = e.address;
				posx = precision?x.province+x.city+x.district+x.street+x.street_number:x.province+x.city;
			}
			else {
				console.log('failed'+this.getStatus());
			}        
		},{enableHighAccuracy: true})
  	</script>
  	<?
  }
  public static function getSdk(){
  	?><script>
  		var posx="#1 定位失败，很可能是网络原因，请刷新几次(请关闭代理，打开设备“位置开关”)";
		var nopos=true;
		//console.log("getSdk");
  	</script><?
  	$set=Typecho_Widget::widget('Widget_Options')->plugin('MyPosition');
  	switch ($set->sdk) {
			case '0':
				MyPosition_Plugin::tengxunPosition($set->keytx);
				//echo $set->keytx;
				break;
			case '1':
				MyPosition_Plugin::gaodePosition($set->keygd);
				break;
			case '2':
				MyPosition_Plugin::baiduPosition($set->keybd);
				break;
			default:
				break;
		}
  }
  public static function positionButton(){ 
  	//echo "<script>console.log(\"123\");</script>";
    $set=Typecho_Widget::widget('Widget_Options')->plugin('MyPosition');
    //echo "<script>console.log({$set->usetype});</script>";
    $home = Helper::options()->pluginUrl.'/MyPosition';
    if($set->type == 0) return;
	echo "<script>var precision = {$set->precision};</script>";
    	//usetype=0,按钮方式
    if($set->usetype == 0) {
    	MyPosition_Plugin::getSdk();
    	//echo "$$$$$";
	    ?>
	    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/font-awesome@4.7.0/css/font-awesome.min.css" integrity="sha256-eZrrJcwDc/3uDhsdt61sL2oOBY362qM3lon1gyExkL0=" crossorigin="anonymous"><?
	    echo "<script src=\"{$home}/js/addPosition.js\"></script>";
	    echo "<script src=\"{$home}/js/addStr.js\"></script>";
	    echo "<script src=\"{$home}/js/addButton.js\"></script>";
	} else {
		//usetype=1,自动插入方式
		MyPosition_Plugin::getSdk();
	    ?><script src="<?echo $home?>/js/addPosition.js"></script>
	    <script>
		    $(document).ready(function(){
		    	//getPosition();
		    	$("#text").val("自动进行定位中...");
		    	setTimeout(function() {
		    		$("#text").val("[pos]"+posx+"[/pos]");
		    	}, 2700);
		    });
	    </script><?	
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
    	if(typeof myposx != "undefined"){
		    var begin='<?php echo (Typecho_Widget::widget('Widget_Options')->plugin('MyPosition')->start == null)?'<div><span>位置 : ':Typecho_Widget::widget('Widget_Options')->plugin('MyPosition')->start?>';
		    var end='<?php echo (Typecho_Widget::widget('Widget_Options')->plugin('MyPosition')->end == null)?'</span></div>':Typecho_Widget::widget('Widget_Options')->plugin('MyPosition')->end?>';
		    myposx=begin+myposx+end;
		    $("pos").prop("outerHTML",myposx);
    	}
	});
    </script><?php
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