function init_1()
{
$("p").hide();
alert("hidden");
}

function show_head()
{
var op=$("div.nav").css("opacity");
op=parseFloat(op);
if(op<1)
{op=op+0.1;}
$("div.login").css("opacity",op);
$("div.nav").css("opacity",op);
var TIMER=setTimeout(show_head,50);
}

function hide_head()
{
var op=$("div.nav").css("opacity");
op=parseFloat(op);
if(op>0.1)
{op=op-0.1;}
$("div.nav").css("opacity",op);
var TIMER=setTimeout(hide_head,50);
}
