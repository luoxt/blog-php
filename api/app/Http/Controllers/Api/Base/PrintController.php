<?php
namespace App\Http\Controllers\Api\Base;

use App\Http\Controllers\Api\Controller;
use Illuminate\Http\Request;

/**
 * 打印接口
 * @package App\Http\Controllers\Api\Organize
 */
class PrintController extends Controller
{
    /**
     * Create a new controller instance.
     * IndexController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $app = app();
        $app['user_info'] = [
            'user_id' => '',
            'org_id' => ''
        ];

        parent::__construct($request);
    }

    public function widget ()
    {


    }

    public function tmplate ()
    {

        //页头

        //页体

        //页尾

    }

    public function index()
    {
        //验证请求数据
        $params = $this->request->input();
        $rule = [];
$out=
<<<EOF
<!DOCTYPE html>
<html>
<head>
<meta name="generator" content="">
<title>打印测试</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script language="javascript" src="http://www.lodop.net/demolist/LodopFuncs.js"></script>
<object  id="LODOP_OB" classid="clsid:2105C259-1E0C-4534-8141-A753534CB4CA" width=0 height=0> 
       <embed id="LODOP_EM" type="application/x-print-lodop" width=0 height=0></embed>
</object>
<style>
    .main_div {
        width:210mm;
        margin:0 auto;
        text-align:center;
        font-size:14px;
        font-family:'宋体',
        margin-bottom:40px;
    }
    .main_div h2, h1 {
        width: 100%;
        float:left;
    }
    
    table.dataintable {
        margin: 10mm auto;
        margin-top: 10px;
        border-collapse: collapse;
        border: 1px solid #aaa;
        width: 90%;
    }
    
    table tr, table th{
        //line-height:30px;
        height:10mm;
        border-collapse: collapse;
        border: 1px solid #aaa;
    }
    table td{
        border-collapse: collapse;
        border: 1px solid #aaa;
    }
    table.dataintable tr:nth-child(odd) {
        background-color: #F5F5F5;
    }
    table.dataintable tr:nth-child(even) {
        background-color: #fff;
    }
    
    ul {
        list-style:none;
        float:left;
        margin:0
    }
    li {
        list-style:none;
        float:left;
        margin-right:20px;
        line-height:40px;
    }
    li label{
        float:left; 
        margin:0;
    }
    </style>
</head>

<body>

    <div class="main_div" id="main_div">
      <style>
        div{
            text-align:center
        }
         ul {
            list-style:none;
            float:left;
            margin:0
        }
        li {
            list-style:none;
            float:left;
            margin-right:20px;
            line-height:40px;
        }
        li label{
            float:left; 
            margin:0;
        }
        td{
            border:1px solid #000;
            height:30px;
            line-height:30px;
        }
      </style>
      <h2>达州田田圈农业服务有限公司<span style="font-size:13px">(第1/2页)</span></h2>
      <h1>销售出库单</h1>
      <ul>
        <li style="width:170px;"><label>往来单位：</label>深圳洛普信</li>
        <li style="width:300px;"><label>单据编号：</label>XK-6556-A-T-2017-11-13-0002</li>
        <li style="width:200px;"><label>付款方式：</label>□现金  □除销</li>
      </ul>
      <ul>
        <li style="width:170px;"><label>送货地址：</label>韶关</li>
        <li style="width:300px;"><label>分支机构：</label>达州田田圈农业服务有限公司</li>
        <li style="width:200px;"><label>录单日期：</label>2017-11-13</li>
      </ul>
     
      <table id="tables" border="1" width="90%" style="border:solid 1px black;border-collapse:collapse; text-align:center">
        <thead>
          <tr>
            <td widtd=5%>行号</td>
            <td width=15%>商品名称</td>
            <td width=10%>规格</td>
            <td width=5%>型号</td>
            <td width=10%>销售单位</td>
            <td width=5%>销售数量</td>
            <td width=10%>销售单价</td>
            <td width=10%>销售金额</td>
          </tr>
    
        </thead>
        <tbody>
          <tr>
            <td width=5%>1</td>
            <td width=15%>洛普信黑将军</td>
            <td width=10%>200ml*40</td>
            <td width=5%>水剂</td>
            <td width=10%>件</td>
            <td width=5%>1</td>
            <td width=10%>360</td>
            <td width=10%>360</td>
          </tr>
          <tr>
            <td width=5%>2</td>
            <td width=15%>洛普信黑将军</td>
            <td width=10%>200ml*40</td>
            <td width=5%>水剂</td>
            <td width=10%>件</td>
            <td width=5%>1</td>
            <td width=10%>360</td>
            <td width=10%>360</td>
          </tr>
          <tr>
            <td width=5%>3</td>
            <td width=15%>洛普信黑将军</td>
            <td width=10%>200ml*40</td>
            <td width=5%>水剂</td>
            <td width=10%>件</td>
            <td width=5%>1</td>
            <td width=10%>360</td>
            <td width=10%>360</td>
          </tr>
          <tr>
            <td width=5%>4</td>
            <td width=15%>洛普信黑将军</td>
            <td width=10%>200ml*40</td>
            <td width=5%>水剂</td>
            <td width=10%>件</td>
            <td width=5%>1</td>
            <td width=10%>360</td>
            <td width=10%>360</td>
          </tr>
          <tr>
            <td width=5%>5</td>
            <td width=15%>洛普信黑将军</td>
            <td width=10%>200ml*40</td>
            <td width=5%>水剂</td>
            <td width=10%>件</td>
            <td width=5%>1</td>
            <td width=10%>360</td>
            <td width=10%>360</td>
          </tr>

        </tbody>
        <tfoot>
          <tr>
          </tr>
        </tfoot>
      </table>
     
      <ul>
        <li style="width:250px;"><label>库管员：</label><span>张三丰</span></li>
        <li style="width:200px;"><label>经手人：</label></li>
        <li style="width:200px;"><label>开单人：</label>张志宏</li>
      </ul>
      <ul>
        <li style="width:300px;"><label>公司地址：</label>深圳市南山区海天二路222号</li>
        <li style="width:150px;"><label>联系电话：</label>0755898989</li>
        <li style="width:200px;"><label>客户签名：</label>_____________</li>
      </ul>
    </div>

    <!--endprint2-->
    <form class="pbutton" style="float:left;width:100%; text-align:center;margin-top:50px">
        <input style="width:100px; margin-left:5%" id="btnPrint" type="button" value="打印预览"
        onclick="prn1_preview()">
        <input style="width:100px; margin-left:5%" id="btnPrint" type="button" value="直接打印"
        onclick="prn1_print()">
        <input style="width:100px; margin-left:30%" id="btnPrint" type="button" value="选择打印机"
        onclick="prn1_printA()">
    </form>
</body>
<script language="javascript" type="text/javascript">  
 
    var LODOP; //声明为全局变量 
	function prn1_preview() {	
		LODOP=getLodop();  
		LODOP.PRINT_INIT("");
		LODOP.SET_PRINT_STYLE("FontSize",18);
		LODOP.SET_PRINT_STYLE("Bold",1);
		//LODOP.ADD_PRINT_TEXT(50,23,1260,139,"");
		LODOP.ADD_PRINT_HTM(0,0,850,600, document.getElementById("main_div").innerHTML);
		
		LODOP.ADD_PRINT_TABLE(0,0,850,600, document.getElementById("tables").innerHTML)
		LODOP.PREVIEW();	
	};
	function prn1_print() {		
		CreateOneFormPage();
		LODOP.PRINT();	
	};
	function prn1_printA() {		
		CreateOneFormPage();
		LODOP.PRINTA(); 	
	};	
	
</script>
</html>
EOF;

    echo $out;exit();

                
    }



}
