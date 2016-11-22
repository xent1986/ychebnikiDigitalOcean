var similar_schet=0;
var watched_schet=0;

$(document).ready(function(){

 $('span.r').each(function() {
var link = $(this).attr('r');
var html = $(this).html();
var title = $(this).attr('t');
var parent = $(this).parent();
var a = $('<a></a>').attr('href', link).html(html);
a.attr('title',title);
parent.append(a);
$(this).remove();
});

var pos=$(window).scrollTop();
var isvis=false;

  $(window).scroll(function(){

   var height = $(window).height();

   pos = $(window).scrollTop();
   if ((pos!=0)&(!isvis))
   {
    isvis=true;
    $("#scrollUp").css('top',height-100);
    $("#scrollUp").show();
   }

   if (isvis)
   {
    if (parseInt($("#scrollUp").css('top'))!=height-100)
    $("#scrollUp").css('top',height-100);
   }

   if ((pos==0)&(isvis))
   {
    isvis=false;
    $("#scrollUp").hide();
   }

  }
  );

  $("#scrollUp").click(function(){
    $("html:not(:animated),body:not(:animated)").animate({ scrollTop: 0}, 500 );
  }
  );

   /*$("#edSearch").autocomplete(domain+"application/utils/searchautocomplete.php", {
        delay:10,
        minChars:2,
        matchSubset:1,
        autoFill:true,
        matchContains:1,
        cacheLength:10,
        selectFirst:true,
        maxItemsToShow:10,
  }).result(function(event,data,formatted){
   return 1;
   });*/

   var cntWatchedProds=0;
   $('.pw_item_img').each(function(){cntWatchedProds = cntWatchedProds+1;});


   var cntSimilarProds=0;
   $('.ps_item_img').each(function(){cntSimilarProds = cntSimilarProds+1;});

   if (cntWatchedProds<6) cntWatchedProds=6;
   if (cntSimilarProds<3) cntSimilarProds=3;

   $("#showShortDesc").click(function(){
    $("#descShort").show(); $("#descFull").hide();
   }
   );
   $("#showFullDesc").click(function(){
    $("#descShort").hide(); $("#descFull").show();
   }
   );

   $("#showShortEntrance").click(function(){
    $("#entShort").show(); $("#entFull").hide();
   }
   );
   $("#showFullEntrance").click(function(){
    $("#entShort").hide(); $("#entFull").show();
   }
   );

   $("#moveSimilarLeftBtn").click(function(){
    if (similar_schet<cntSimilarProds-3)
    {
     similar_schet = similar_schet+1;
     $("#psitems").animate({left:'-=130'},300,function(){});
    }
   }
   );

   $("#moveSimilarRightBtn").click(function(){
    if (similar_schet>0)
    {
     similar_schet = similar_schet-1;
     $("#psitems").animate({left:'+=130'},300,function(){});
    }

   }
   );

   $("#moveWatchedLeftBtn").click(function(){
    if (watched_schet<cntWatchedProds-6)
    {
     watched_schet = watched_schet+1;
     $("#pwitems").animate({left:'-=130'},300,function(){});
    }
   }
   );

   $("#moveWatchedRightBtn").click(function(){
    if (watched_schet>0)
    {
     watched_schet = watched_schet-1;
     $("#pwitems").animate({left:'+=130'},300,function(){});
    }
   }
   );

   $("#cntup").click(function(){
     change_buy_cnt(1);
   }
   );

   $("#cntdown").click(function(){
    change_buy_cnt(-1);
   }
   );

   $("#btApply").click(function(){
     yaCounter20755705.reachGoal('buyBtn');
//     buyClickLog($("#goodname").html(),$("#myitem").val());
     buyClickLog($("#myitem").val());
     buyGood($("#myitem").val());
     return false;
    }
    );

    if ($("#secret").length)
    {
     sendSecretValue();
    }

    $("#zApply").click(function(){
      if ($("#descript").val().length>0)
      {
       $("#zakaznikForm").submit();
      }
      else alert('Не заполнено описание заявки');
    }
    );

    $("#toolFavorite").click(function(){
      bookmark($(this));
    }
    );

    if ($(".pd_hold").length)
    {
     var isinhold = checkHoldStatus($("#myitem").val());
     var str = '<div class="pd_hold_img"></div><div class="pd_hold_empty">отложить</div><div class="clear"></div>';
     if (isinhold)
     {
      str = getAlreadyHoldStr();
     }
     $(".pd_hold").append(str);
     $(".pd_hold").show();
    }

    $(".pd_hold_empty").click(function(){
      addHoldItem($("#myitem").val());
      $(".pd_hold_empty").remove();
      $(".pd_hold_img").remove();
      $(".pd_hold").append(getAlreadyHoldStr());
      updateLeftTool();
    }
    );

    $(".holds_show").click(function(){
      getHoldsItems();
    }
    );

    if ($(".holdprodid").length>0)
    {
     checkHoldsInTable();
    }

    $('.h_check_all').click(function(){
      allHoldsCheckChange(true);
    }
    );

    $('.h_clear_all').click(function(){
      allHoldsCheckChange(false);
    }
    );

    $("#btDelChecked").click(function(){
      delChecked();
    }
    );

    $("#btBuyChecked").click(function(){
      buyChecked();
    }
    );

    $(".btnBuy").each(function(){
      $(this).show();
    }
    );

    init_VK();
//  replaceExpressPartner();
    }
  );

function init_VK()
{
 //
}

function buyFromCat(id,obj)
{
  yaCounter20755705.reachGoal('buyBtn');
  buyClickLog(id);
  var cart=$('iframe#myShopOnelineCartIframe');
  var cart_src = cart.attr('src');
  var prnt = obj.parent();
  prnt.html('<img src=\"/images/loading5.gif\" title=\"Помещаем в корзину\">');
  $.get(domain+"application/utils/check.php",{good:id},
  function(data)
  {
   if (data==0)
   {
    prnt.html('<div id="fail_order">Нет в наличии</div>');
    prnt.css('margin-top','5px');
    prnt.find("#fail_order").css('display','block');
   }
   if (data==1)
   {
     var obj = $('iframe#cartdetailframe');
     var form = obj.contents().find("#frameCartForm");
     var item = obj.contents().find("#myitem");
     prnt.html('<a id="make_order_link" title="Перейти в корзину" href='+domain+'cart>В корзине</a>');
     prnt.css('margin-top','5px');
     prnt.find("#make_order_link").css('display','block');
     item.val(id);
     form.submit();
     setTimeout(function(){$('iframe#myShopOnelineCartIframe').attr('src',cart_src)},500);
   }
  }
 );
}

function extractCartValue()
{
 var nocach = Math.ceil(Math.random()*1000000);
 var param = location.search.substring(1);
 $.get(domain+"products/getcart",{cache:nocach,param:param},
 function(data)
 {
   alert(data); 
   });
  
}

function buyGood(id)
{
  
  cart=$('iframe#myShopOnelineCartIframe');
     cart_src = cart.attr('src');
 $("#btApplyDiv").html('<img src=\"/images/loading5.gif\" title=\"Помещаем в корзину\">');
 $.get(domain+"application/utils/check.php",{good:id},
  function(data)
  {
   if (data==0)
   {
    $("#btApplyDiv").html('<div id="btApplyFail">Нет в наличии</div>');
   }
   if (data==1)
   {
     var obj = $('iframe#cartdetailframe');
     var form = obj.contents().find("#frameCartForm");
     var item = obj.contents().find("#myitem");
     var cnt = obj.contents().find("#itemcnt");
     var cnt2 = $("#itemcnt").val(); if (cnt2<0) cnt2=1;
     $("#btApplyDiv").html('<a href='+domain+'cart><div id="btApplyOk">В корзине</div></a>');
     item.val(id);
     cnt.val(cnt2);
     form.submit();
     setTimeout(function(){$('iframe#myShopOnelineCartIframe').attr('src',cart_src)},500);
   }
  }
 );

}

function replaceExpressPartner()
{
  var h1 = $(document).height();
  pos = $(".express_all").position();
  var h2 = pos.top;
  var h3 = $(".bottom").height()+5;
  var h = h1-h2-200-h3;
  if (h<0) h=h+290;
  var top = h.toString()+'px';
  $(".express_all").css('margin-top',top);
  $(".express_all a").css('font-size','10px');

}

function delChecked()
{
 $('input[type=checkbox]:checked').each(function(){
   holdsItemDel($(this));
 }
 );
}

function buyChecked(){

/* var id = obj.attr('id');
 id = id.substr(3,id.length);  */
  t=1000;
  cart=$('iframe#myShopOnelineCartIframe');
 cart_src = cart.attr('src');

 $('input[type=checkbox]:checked').each(function(){

 var id = $(this).attr('id');
 id = id.substr(3,id.length);
 var prnt = $(this).parent();
 var prnt2 = prnt.parent();
 prnt2.find("#buyForm").remove();
 prnt2.find(".holds_btn_buy").html('<img src=\"/images/loading5.gif\" title=\"Обрабатывается\">');

 setTimeout(function(){
 $.get(domain+"application/utils/check.php",{good:id},
  function(data)
  {
   if (data==0)
   {
    prnt2.find(".holds_btn_buy").html('<img src=\"/images/cancel.png\" title=\"Нет в наличии\"> Нет');
   }
   if (data==1)
   {
     var obj = $('iframe#cartframe');
     var form = obj.contents().find("#frameCartForm");
     var item = obj.contents().find("#myitem");
     prnt2.find(".holds_btn_buy").html('<img src=\"/images/clean.png\" title=\"Есть в наличии\"> В корзине');
     item.val(id);
     form.submit();
   }
  }
 );
 },t);
   t=t+1000;
 }
 );

 setTimeout(function(){$('iframe#myShopOnelineCartIframe').attr('src',cart_src)},t+1000);

}

function allHoldsCheckChange(flag)
{
 $('input[type=checkbox]').each(function(){
   if (flag!=this.checked)
   {
    rClass = (this.checked?'holds_tr_checked':'holds_tr');
    aClass = (this.checked?'holds_tr':'holds_tr_checked');
    this.checked=flag;
    prnt = $(this).parent();
    prnt2 = prnt.parent();
    prnt2.removeClass(rClass);
    prnt2.addClass(aClass);
   }
 }
 );
}

function holdsdelVisible(flag,obj)
{
 if (flag)
 {
  dd = obj.find('.holds_td_price');
  dd.append("<div class='holds_del' onclick='holdsTrDel($(this));'><img src='/images/holdsdel.png' class='holds_del_img left'><div class='holds_del_btn left'>Удалить</div><div class='clear'></div></div>");
 }
 if (!flag)
 {
  var dd = obj.find('.holds_del');
  dd.remove();
 }
}

function holdsTrDel(obj)
{
 // удаление каждой позиции. Находим checkbox и передаем управление общей функции
 var p = obj.parent();
 var p2 = p.parent();
 ch = p2.find('input[type=checkbox]');
 holdsItemDel(ch);
}

function holdsItemDel(obj)
{
//удаление элемента из куки и со страницы
 var id = obj.attr('id');
 id = id.substr(3,id.length);
 var holds = $.cookie("holdlist");
 var ar = [];
 if (holds)
  ar = holds.split(',');
 var start = $.inArray(id,ar);
 if (start!=-1)
 {
  ar.splice(start,1);
  var res = ar.join(',');
  $.cookie("holdlist",res,{expires:30,path:'/'});
  p = obj.parent();
  p2 = p.parent();
  p2.remove();
  updateLeftTool();
 }
 if (ar.length==0)
 {
  clearHoldsTable();
 }
}

function clearHoldsTable()
{
  $('.holds_table').remove();
  $('.holds').append("<div class='h_no_items'>Список отложенных товаров очищен</div>");
}



function change_checkbox(obj)
{
 var ch = obj.find(':checkbox');
 var isChecked = ch.attr('checked');
 isChecked = !isChecked;
 ch.attr('checked',isChecked);
 if (isChecked)
 {
  obj.removeClass("holds_tr");
  obj.addClass("holds_tr_checked");
 }
 if (!isChecked)
 {
  obj.removeClass("holds_tr_checked");
  obj.addClass("holds_tr");
 }

}

function change_me(obj)
{
var ch=obj.attr('checked');
obj.attr('checked',!ch);
}

function updateLeftTool()
{
 var holds = $.cookie("holdlist");
 var ar = [];
 if (holds)
  ar = holds.split(',');
 var cnt = ar.length;
 if (cnt>0)
 {
  var str = "Отложенные (";
  if ($(".holds_caption").length>0)
  {
   str = str+cnt+")";
   $(".holds_caption").html(str);
  }
  else{
   str="<div class='holds_all'><div><a href='"+domain+"holdlist' title='Перейти к отложенным товарам'><div class='holds_caption left'>Отложенные (1)</div></a><div class='clear'></div></div><div><div class='holds_show left' onclick='getHoldsItems()'>показать здесь</div><div class='clear'></div></div></div>";
   $(".left_column").append(str);
  }
  if ($(".hide_holds").length>0)
  {
   hideholds($(".hide_holds"));
  }
 }
 else $('.holds_all').remove();
}

function addHoldItemT(obj)
{
 var prnt = obj.parent();
 var prodid = prnt.find(".holdprodid").val();
 addHoldItem(prodid);
 prnt.find(".clear").remove();
 prnt.find(".pt_hold_empty").remove();
 prnt.append(getAlreadyHoldStrT());
 updateLeftTool();
}

function checkHoldsInTable()
{
 $(".holdprodid").each(function(){
   var prnt = $(this).parent();
   var isinhold = checkHoldStatus($(this).val());
   var str = "<div class='pt_hold_empty' onclick='addHoldItemT($(this));'>отложить</div>";
   if (isinhold)
   {
    str = "<div class='pt_hold_exist'><a href='"+domain+"holdlist'>в отложенных</a></div>";
   }
    //prnt.find(".clear").remove();
    prnt.append(str);
    //prnt.append("<div class='clear'></div>");
 }
 );
}

function hideholds()
{
 $(".h_item").each(function(){
   $(this).remove();
 }
 );
 $(".hide_holds").parent().remove();
 var prnt = $(".holds_show").parent();
 prnt.show();
}

function getHoldsItems()
{
 var holds = $.cookie("holdlist");
 if ($(".hide_holds").length==0)
 {
  $.get(domain+"/holdlist/getholds",{items:holds},
  function(data)
  {
   var prnt = $(".holds_show").parent();
   prnt.hide();
   $(".holds_all").append(data);
  }
 );
 }
}

function addHoldItem(id)
{
 var holds = $.cookie("holdlist");
 var ar = [];
 if (holds)
  ar = holds.split(',');
 ar.push(id);
 var res = ar.join(',');
 $.cookie("holdlist",res,{expires:30,path:'/'});

}

function checkHoldStatus(id)
{
 var res = false;
 var holds = $.cookie('holdlist');
 if (holds)
 {
  var ar = holds.split(',');
  if (jQuery.inArray(id,ar)!=-1)
  {
   res = true;
  }
 }
return res;
}

function getAlreadyHoldStr()
{
 str = '<div class="pd_hold_exist"><a href="'+domain+'holdlist" title="Отложенные товары">в отложенных</a></div>';
 return str;
}

function getAlreadyHoldStrT()
{
 str = '<div class="pt_hold_exist"><a href="'+domain+'holdlist" title="Отложенные товары">в отложенных</a></div>';
 return str;
}

function change_buy_cnt(cnt)
{
    var cnt2;
    
    cnt2 = parseInt($("#itemcnt").val());
    cnt2 = cnt2+cnt;
    if (cnt2==0) cnt2=1;
    $("#itemcnt").val(cnt2);
    $("#cntnum").html(cnt2);
}


function resizeme(obj,iw,ih)
{
 var w = obj.width();
 var h = obj.height();
 var k=1;
 if (w>=h)
  k = iw/w;
 if (w<h)
  k = ih/h;
 w = w*k;
 h = h*k;
 if (w>iw)
 {
  k = iw/w;
  w = w*k;
  h = h*k;
 }
 obj.width(w);
 obj.height(h);
 obj.show();
 return 1;
}

function checkGoodExist(id)
{
 $.get(domain+"application/utils/check.php",{good:id},
  function(data)
  {
   var flag;
   if (data==0) {$('#sAvail').html('<img src=\"/images/cancel.png\" title=\"Нет в наличии\">'); flag=true; $.get(domain+"products/changeproductstatus",{prodid:id,status:1});}
   if (data==1) {$('#sAvail').html('<img src=\"/images/clean.png\" title=\"Есть в наличии\">'); flag=false;}
   //$("#btApply").attr("disabled",flag);
  }
 );
}

function updateGoodPrice(id,oldprice,realid)
{
 obj = $('.pd_buy_price');
 obj.html('<img src="/images/loading5.gif">');
 changeLastPrice(id,oldprice,obj,realid);
}

function changeLastPrice(id,oldprice,obj,realid)
{
 $.get(domain+"products/getlastprice",{id:id,old:oldprice,real:realid},
 function(data)
 {
  if (data==0)
   price = oldprice;
  else
  {
   jsonObj = JSON.parse(data);
   price = parseFloat(jsonObj.cost);
   saleprice = parseFloat(jsonObj.sale_cost);
   salepercent = parseFloat(jsonObj.sale_percent);
   salelimit = parseFloat(jsonObj.sale_limit);
   delivery = jsonObj.time_text_a;
   if (saleprice!==0)
   {
    $('.pd_price').append('<div id="oldprice" class="pd_old_price">Прежняя цена: <strike>'+price.toFixed(2)+' руб.</strike></div>');
    if (salepercent!==0)
    {
     $('.pd_price').append('<div id="pdiscount" class="pd_discount">(скидка: '+salepercent+'% начиная с '+salelimit+' шт.)</div>');
    }
    obj.html(saleprice.toFixed(2)+' руб.');
   } else obj.html(price.toFixed(2)+' руб.');
   if (delivery!=='')
   {
    $('.pd_di_info').html(delivery);
    $('.pd_delivery_info').show().animate({opacity:'1'},2000,function(){});
   }


  }
 }
 );
}

function checkCart()
{
 ar = new Array();
 $('.product_item').each(function(){
   idobj = $(this).find('.linkid');
   id = idobj.val();
   ar.push(id+'-1');
 }
 );
 var str=ar.join(',');
 $.get(domain+"products/checkcart",{cart:str},
 function(data)
 {
  ar2 = new Array();
  ar2 = data.split(',');
  for (var key in ar2) {
   ar3 = ar2[key].split('-');
   price = parseFloat(ar3[1]);
   $("input[value="+ar3[0]+"]").each(function(){
     prnt = $(this).parent();
     prnt.find('.prod_price2').html(price.toFixed(2)+' руб.');  
   });
  
  }
 }
 );
}

function getCommentForm(id)
{
 $.get(domain+"comments/index",{id:id},
 function(data)
 {
  $("#commentsblock").append(data);
 }
 );
}

function getComments(id)
{
 $.get(domain+"comments/getcomments",{id:id},
 function(data)
  {
   $("#commentsblock").append(data);
  }
 )
}

function addComment(id)
{
 var flag=true;
 if ($("#uname").val()=='') flag=false;
 if ($("#ucomment").val()=='') flag=false;
 if (!flag) alert('Не заполнено одно из полей.');
 if (flag)
 {
  var user = $("#uname").val();
  var comment = $("#ucomment").val();
  $.post(domain+"comments/addcomment",{name:user,text:comment,id:id},
  function(data)
  {
   $("#ucomment").val('');
   if ($("#commentsul").length<=0)
   {
    $("#nocomments").remove();
    $("#commentsblock").append("<ul id='commentsul' class='comments_list'></ul>");
   }
   $("#commentsul").prepend(data);
  }
  );
 }
}

/*function buyClickLog(name,id)
{
  $.get(domain+"cart/addlog",{mode:'log',good:name,id:id},
   function(data)
   {
   // $("#buyForm").submit();
   return 1;
   }
  );
} */

function buyClickLog(id)
{
 href = window.location.href;
  $.get(domain+"cart/addlog",{mode:'log',id:id,href:href},
   function(data)
   {
   // $("#buyForm").submit();
   return 1;
   }
  );
}

function sendSecretValue()
{
 $.get(domain+"zakaznik/getsecret",{mode:'secret'},
 function(data)
 {
  $("#secret").val(data);
 }
 );
 return 1;
}


function getBrowserInfo() {
 var t,v = undefined;
 if (window.opera) t = 'Opera';
 else if (document.all) {
  t = 'IE';
  var nv = navigator.appVersion;
  var s = nv.indexOf('MSIE')+5;
  v = nv.substring(s,s+1);
 }
 else if (navigator.appName) t = 'Netscape';
 return {type:t,version:v};
}

function bookmark(a){
 var url = window.document.location;
 var title = window.document.title;
 var b = getBrowserInfo();
 if (b.type == 'IE' && 7 > b.version && b.version >= 4) window.external.AddFavorite(url,title);
 else if (b.type == 'Opera') {
  a.href = url;
  a.rel = "sidebar";
  a.title = url+','+title;
  return true;
 }
 else if (b.type == "Netscape") window.sidebar.addPanel(title,url,"");
 else alert("Нажмите CTRL-D, чтобы добавить страницу в закладки.");
 return false;
}

function getSportLandiaBanner()
{
 $.get(domain+"products/getbanner",{mycat:8},
  function(data)
  {
   if (data!='')
   {
    $("#my_sport_landia_banner").append(data);
	$("#my_sport_landia_banner").show();
   } else $("#my_sport_landia_banner").remove();
  }
 );
}
