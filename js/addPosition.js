var geolocation = new qq.maps.Geolocation();
var options = {timeout: 9000};
var position = 0;
var posx="#1 定位失败，很可能是网络原因(记得关闭代理)，请刷新几次(记得打开设备“位置开关”)";
var nopos=true;
function getCurLocation() {
    geolocation.getLocation(showPosition, showErr, options);
}
function getAboutLocation() {
    geolocation.getIpLocation(showPosition, showErr);
}
function showPosition(pos) {
    posx=!precision?pos.nation+pos.city+pos.addr:pos.nation+pos.city;
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
function getPosition(){
    getCurLocation();
}
function addPosition(){
    //while(nopos){
    console.log(posx);
    getPosition();
    //}
    layer.msg("自动获取当前地理位置中...");
    setTimeout(function(){
        addStr("[pos]"+posx+"[/pos]");
    },3000);
}
