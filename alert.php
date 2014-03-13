<?php
//THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
//please send BTC at: 1A42umF893jp3wf94juRhGArFQrqkdxnnM
//PARAMETERS: if you want to have many alerts you can copy this script and run many copy together but don't make more than 600 request every 10 min
$bidpriceAlert = 100;// price that will activate the alert
$alertType = "more"; // if you want to activate it when price is more then bidprice write "more" if you want activate it when is less write "less"
$sec = "2"; //interval of time for refresh the page and check the price again (600 request / 600 seconds maximum)

$page = $_SERVER['PHP_SELF'];
header("Refresh: $sec; url=$page");
include 'bitstamp.php';
$bs = new Bitstamp('WRITE YOUR API KEY HIER', 'WRITE YOUR SECRET KEY HIER', 'WRITE YOUR CUSTOMER 6 DIGIT ID HIER');
$ticker = $bs->ticker();
//get the ticker info
$askprice=$ticker["ask"];
$bidprice= $ticker["bid"];
$high=$ticker["high"];
$low= $ticker["low"];
$volume=$ticker["volume"];
print_r("Ticker Ask : ");
print_r($askprice);
print_r(" | Ticker Bid : ");
print_r($bidprice);
print_r(" | delta HL: ");
print_r($high-$low);
print_r(" | delta Ask-Bid : ");
print_r($askprice-$bidprice);
print_r(" || ");
echo"<a href='https://blockchain.info/fr/address/1A42umF893jp3wf94juRhGArFQrqkdxnnM'>Thank you for donation</a>";
if($bidpriceAlert < $bidprice && $alertType == "more"){
?>
<embed src="sound/alertmore.wav" width="1" height="1" id="more" enablejavascript="true" autostart="true">
<?php
}
if($bidpriceAlert > $bidprice && $alertType == "less"){
?>
<embed src="sound/alertless.wav" width="1" height="1" id="less" enablejavascript="true" autostart="true">
<?php
}
?>
