// Coded by Fabian Beer
// Visit fabee.de
// And now have fun with the code!
$(".lqip").css({"background-image":"url("+lqipBgSmall+")"}),$("body").append('<img class="fully lqip-img" />'),$(".fully").attr("src",lqipBgOrginal),$("img").load(function(){$(".lqip").css({"background-image":"url("+lqipBgOrginal+")"}).addClass("noblur")});